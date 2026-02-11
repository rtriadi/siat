<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stock_model extends CI_Model
{
    /**
     * Get all items with category join
     * @param array $filters Optional filters (category_id, low_stock_only)
     * @return array
     */
    public function get_all($filters = [])
    {
        $this->db
            ->select('stock_item.*, stock_category.category_name')
            ->from('stock_item')
            ->join('stock_category', 'stock_category.id_category = stock_item.category_id', 'left');

        if (isset($filters['category_id'])) {
            $this->db->where('stock_item.category_id', $filters['category_id']);
        }

        if (isset($filters['low_stock_only']) && $filters['low_stock_only']) {
            $this->db->where('stock_item.available_qty <= stock_item.low_stock_threshold');
        }

        return $this->db
            ->order_by('stock_category.category_name', 'ASC')
            ->order_by('stock_item.item_name', 'ASC')
            ->get()
            ->result_array();
    }

    /**
     * Get item by ID
     * @param int $id_item
     * @return array|null
     */
    public function get_by_id($id_item)
    {
        return $this->db
            ->select('stock_item.*, stock_category.category_name')
            ->from('stock_item')
            ->join('stock_category', 'stock_category.id_category = stock_item.category_id', 'left')
            ->where('stock_item.id_item', $id_item)
            ->get()
            ->row_array();
    }

    /**
     * Create new stock item
     * @param array $data (category_id, item_name, available_qty, low_stock_threshold)
     * @return array ['success' => bool, 'id' => int|null, 'message' => string]
     */
    public function create_item($data)
    {
        // Validate required fields
        if (empty($data['category_id']) || empty($data['item_name'])) {
            return [
                'success' => false,
                'id' => null,
                'message' => 'Kategori dan nama item wajib diisi.'
            ];
        }

        // Treat identifiers as strings to prevent numeric coercion
        $payload = [
            'category_id' => (string)$data['category_id'],
            'item_name' => $data['item_name'],
            'available_qty' => isset($data['available_qty']) ? max(0, (int)$data['available_qty']) : 0,
            'reserved_qty' => 0,
            'used_qty' => 0,
            'low_stock_threshold' => isset($data['low_stock_threshold']) ? max(0, (int)$data['low_stock_threshold']) : 10
        ];

        if ($this->db->insert('stock_item', $payload)) {
            return [
                'success' => true,
                'id' => $this->db->insert_id(),
                'message' => 'Item berhasil dibuat.'
            ];
        }

        $error = $this->db->error();
        return [
            'success' => false,
            'id' => null,
            'message' => $error['message'] ?? 'Gagal membuat item.'
        ];
    }

    /**
     * Update item details (not stock quantities - use adjust_stock for that)
     * @param int $id_item
     * @param array $data (category_id, item_name, low_stock_threshold)
     * @return array ['success' => bool, 'message' => string]
     */
    public function update_item($id_item, $data)
    {
        $existing = $this->get_by_id($id_item);
        if (!$existing) {
            return [
                'success' => false,
                'message' => 'Item tidak ditemukan.'
            ];
        }

        $payload = [];
        if (isset($data['category_id'])) {
            $payload['category_id'] = (string)$data['category_id'];
        }
        if (isset($data['item_name'])) {
            $payload['item_name'] = $data['item_name'];
        }
        if (isset($data['low_stock_threshold'])) {
            $payload['low_stock_threshold'] = max(0, (int)$data['low_stock_threshold']);
        }

        if (empty($payload)) {
            return [
                'success' => false,
                'message' => 'Tidak ada data yang diubah.'
            ];
        }

        if ($this->db->update('stock_item', $payload, array('id_item' => $id_item))) {
            return [
                'success' => true,
                'message' => 'Item berhasil diubah.'
            ];
        }

        $error = $this->db->error();
        return [
            'success' => false,
            'message' => $error['message'] ?? 'Gagal mengubah item.'
        ];
    }

    /**
     * Adjust stock with transaction and movement logging
     * @param int $id_item
     * @param string $type 'in' (add to available), 'out' (subtract from available), 'adjust' (manual adjustment)
     * @param int $qty_delta Quantity change (positive or negative)
     * @param string $reason
     * @param int $user_id
     * @return array ['success' => bool, 'message' => string]
     */
    public function adjust_stock($id_item, $type, $qty_delta, $reason, $user_id)
    {
        // Validate type
        if (!in_array($type, ['in', 'out', 'adjust'])) {
            return [
                'success' => false,
                'message' => 'Tipe perubahan tidak valid.'
            ];
        }

        // Get current item
        $item = $this->get_by_id($id_item);
        if (!$item) {
            return [
                'success' => false,
                'message' => 'Item tidak ditemukan.'
            ];
        }

        // Calculate new available qty
        $new_available = $item['available_qty'] + $qty_delta;

        // Prevent negative stock
        if ($new_available < 0) {
            return [
                'success' => false,
                'message' => 'Stok tidak boleh negatif.'
            ];
        }

        // Begin transaction
        $this->db->trans_start();

        // Update stock
        $this->db->update(
            'stock_item',
            ['available_qty' => $new_available],
            ['id_item' => $id_item]
        );

        // Log movement
        $this->db->insert('stock_movement', [
            'item_id' => $id_item,
            'movement_type' => $type,
            'qty_delta' => $qty_delta,
            'reason' => $reason,
            'user_id' => $user_id
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $error = $this->db->error();
            return [
                'success' => false,
                'message' => $error['message'] ?? 'Gagal menyesuaikan stok.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Stok berhasil disesuaikan.'
        ];
    }

    /**
     * Bulk restock via batch update with transaction
     * @param array $items Array of ['id_item' => int, 'qty_delta' => int, 'reason' => string]
     * @param int $user_id
     * @return array ['success' => bool, 'updated' => int, 'message' => string]
     */
    public function restock_batch($items, $user_id)
    {
        if (empty($items)) {
            return [
                'success' => false,
                'updated' => 0,
                'message' => 'Tidak ada data yang diproses.'
            ];
        }

        $this->db->trans_start();

        $updated_count = 0;
        $movements = [];

        foreach ($items as $item_data) {
            $id_item = (int)$item_data['id_item'];
            $qty_delta = (int)$item_data['qty_delta'];
            $reason = isset($item_data['reason']) ? $item_data['reason'] : 'Bulk restock';

            // Get current item
            $current = $this->db
                ->select('available_qty')
                ->get_where('stock_item', ['id_item' => $id_item])
                ->row_array();

            if (!$current) {
                continue; // Skip non-existent items
            }

            $new_available = $current['available_qty'] + $qty_delta;

            // Prevent negative stock
            if ($new_available < 0) {
                continue; // Skip items that would go negative
            }

            // Update stock
            $this->db->update(
                'stock_item',
                ['available_qty' => $new_available],
                ['id_item' => $id_item]
            );

            // Prepare movement log entry
            $movements[] = [
                'item_id' => $id_item,
                'movement_type' => 'in',
                'qty_delta' => $qty_delta,
                'reason' => $reason,
                'user_id' => $user_id
            ];

            $updated_count++;
        }

        // Insert all movement logs in batch
        if (!empty($movements)) {
            $this->db->insert_batch('stock_movement', $movements);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $error = $this->db->error();
            return [
                'success' => false,
                'updated' => 0,
                'message' => $error['message'] ?? 'Gagal melakukan restock.'
            ];
        }

        return [
            'success' => true,
            'updated' => $updated_count,
            'message' => "Berhasil memperbarui {$updated_count} item."
        ];
    }

    /**
     * Get stock movement history for an item
     * @param int $id_item
     * @param int $limit
     * @return array
     */
    public function get_movements($id_item, $limit = 50)
    {
        return $this->db
            ->select('stock_movement.*, user.nama as user_name')
            ->from('stock_movement')
            ->join('user', 'user.id_user = stock_movement.user_id', 'left')
            ->where('stock_movement.item_id', $id_item)
            ->order_by('stock_movement.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->result_array();
    }
}
