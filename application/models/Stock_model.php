<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stock_model extends CI_Model
{
    private function get_admin_user_ids()
    {
        $rows = $this->db
            ->select('id_user')
            ->from('user')
            ->where('level', 1)
            ->where('is_active', 1)
            ->get()
            ->result_array();

        $admin_ids = [];
        foreach ($rows as $row) {
            $admin_ids[] = (int) $row['id_user'];
        }

        return $admin_ids;
    }

    private function notify_low_stock($item)
    {
        if (!$item) {
            return ['success' => true];
        }

        $available = (int) $item['available_qty'];
        $threshold = (int) $item['low_stock_threshold'];
        if ($available > $threshold) {
            return ['success' => true];
        }

        $this->load->model('Notification_model');
        $admin_ids = $this->get_admin_user_ids();
        $message = sprintf(
            'Stok %s tersisa %d (minimum %d).',
            $item['item_name'],
            $available,
            $threshold
        );

        return $this->Notification_model->create_for_users(
            $admin_ids,
            'Stok menipis',
            $message,
            'stock',
            $item['id_item']
        );
    }
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
        $this->db->trans_begin();

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

        if ($this->db->trans_status() === false) {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => $error['message'] ?? 'Gagal menyesuaikan stok.'
            ];
        }

        $latest = $this->get_by_id($id_item);
        $notify = $this->notify_low_stock($latest);
        if (!$notify['success']) {
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => $notify['message'] ?? 'Gagal membuat notifikasi stok menipis.'
            ];
        }

        $this->db->trans_commit();

        return [
            'success' => true,
            'message' => 'Stok berhasil disesuaikan.'
        ];
    }

    public function reserve_stock($item_id, $qty, $user_id, $reason)
    {
        $qty = (int) $qty;

        if ($qty <= 0) {
            return [
                'success' => false,
                'message' => 'Jumlah reservasi tidak valid.'
            ];
        }

        $this->db->trans_begin();

        $this->db->set('available_qty', "available_qty - {$qty}", false);
        $this->db->set('reserved_qty', "reserved_qty + {$qty}", false);
        $this->db->where('id_item', $item_id);
        $this->db->where('available_qty >=', $qty);
        $this->db->update('stock_item');

        if ($this->db->affected_rows() === 0) {
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => 'Stok tersedia tidak mencukupi.'
            ];
        }

        $this->db->insert('stock_movement', [
            'item_id' => $item_id,
            'movement_type' => 'reserve',
            'qty_delta' => $qty,
            'reason' => $reason,
            'user_id' => $user_id
        ]);

        if ($this->db->affected_rows() === 0) {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => $error['message'] ?? 'Gagal mencatat reservasi stok.'
            ];
        }

        if ($this->db->trans_status() === false) {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => $error['message'] ?? 'Gagal mencatat reservasi stok.'
            ];
        }

        $item = $this->get_by_id($item_id);
        $notify = $this->notify_low_stock($item);
        if (!$notify['success']) {
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => $notify['message'] ?? 'Gagal membuat notifikasi stok menipis.'
            ];
        }

        $this->db->trans_commit();

        return [
            'success' => true,
            'message' => 'Stok berhasil direservasi.'
        ];
    }

    public function deliver_stock($item_id, $qty, $user_id, $reason)
    {
        $qty = (int) $qty;

        if ($qty <= 0) {
            return [
                'success' => false,
                'message' => 'Jumlah pengiriman tidak valid.'
            ];
        }

        $this->db->trans_begin();

        $this->db->set('reserved_qty', "reserved_qty - {$qty}", false);
        $this->db->set('used_qty', "used_qty + {$qty}", false);
        $this->db->where('id_item', $item_id);
        $this->db->where('reserved_qty >=', $qty);
        $this->db->update('stock_item');

        if ($this->db->affected_rows() === 0) {
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => 'Stok reservasi tidak mencukupi untuk dikirim.'
            ];
        }

        $this->db->insert('stock_movement', [
            'item_id' => $item_id,
            'movement_type' => 'deliver',
            'qty_delta' => $qty,
            'reason' => $reason,
            'user_id' => $user_id
        ]);

        if ($this->db->affected_rows() === 0) {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => $error['message'] ?? 'Gagal mencatat pengiriman stok.'
            ];
        }

        if ($this->db->trans_status() === false) {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => $error['message'] ?? 'Gagal mencatat pengiriman stok.'
            ];
        }

        $item = $this->get_by_id($item_id);
        $notify = $this->notify_low_stock($item);
        if (!$notify['success']) {
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => $notify['message'] ?? 'Gagal membuat notifikasi stok menipis.'
            ];
        }

        $this->db->trans_commit();

        return [
            'success' => true,
            'message' => 'Stok berhasil dikirim.'
        ];
    }

    public function release_reserved_stock($item_id, $qty, $user_id, $reason)
    {
        $qty = (int) $qty;

        if ($qty <= 0) {
            return [
                'success' => false,
                'message' => 'Jumlah pembatalan tidak valid.'
            ];
        }

        $this->db->trans_begin();

        $this->db->set('reserved_qty', "reserved_qty - {$qty}", false);
        $this->db->set('available_qty', "available_qty + {$qty}", false);
        $this->db->where('id_item', $item_id);
        $this->db->where('reserved_qty >=', $qty);
        $this->db->update('stock_item');

        if ($this->db->affected_rows() === 0) {
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => 'Stok reservasi tidak mencukupi untuk dikembalikan.'
            ];
        }

        $this->db->insert('stock_movement', [
            'item_id' => $item_id,
            'movement_type' => 'cancel',
            'qty_delta' => $qty,
            'reason' => $reason,
            'user_id' => $user_id
        ]);

        if ($this->db->affected_rows() === 0) {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => $error['message'] ?? 'Gagal mencatat pembatalan reservasi.'
            ];
        }

        if ($this->db->trans_status() === false) {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => $error['message'] ?? 'Gagal mencatat pembatalan reservasi.'
            ];
        }

        $item = $this->get_by_id($item_id);
        $notify = $this->notify_low_stock($item);
        if (!$notify['success']) {
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => $notify['message'] ?? 'Gagal membuat notifikasi stok menipis.'
            ];
        }

        $this->db->trans_commit();

        return [
            'success' => true,
            'message' => 'Reservasi stok berhasil dibatalkan.'
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

        $this->db->trans_begin();

        $updated_count = 0;
        $movements = [];
        $stock_changes = [];

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

            $current_available = (int) $current['available_qty'];
            $new_available = $current_available + $qty_delta;

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

            $stock_changes[$id_item] = [
                'from' => $current_available,
                'to' => $new_available
            ];

            $updated_count++;
        }

        // Insert all movement logs in batch
        if (!empty($movements)) {
            $this->db->insert_batch('stock_movement', $movements);
        }

        if ($this->db->trans_status() === false) {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return [
                'success' => false,
                'updated' => 0,
                'message' => $error['message'] ?? 'Gagal melakukan restock.'
            ];
        }

        if (!empty($stock_changes)) {
            $updated_items = $this->db
                ->from('stock_item')
                ->where_in('id_item', array_keys($stock_changes))
                ->get()
                ->result_array();

            foreach ($updated_items as $item) {
                $item_id = (int) $item['id_item'];
                $change = $stock_changes[$item_id] ?? null;
                if (!$change) {
                    continue;
                }

                $threshold = (int) $item['low_stock_threshold'];
                $was_above = $change['from'] > $threshold;
                $is_low = $change['to'] <= $threshold;
                if ($was_above && $is_low) {
                    $notify = $this->notify_low_stock($item);
                    if (!$notify['success']) {
                        $this->db->trans_rollback();
                        return [
                            'success' => false,
                            'updated' => $updated_count,
                            'message' => $notify['message'] ?? 'Gagal membuat notifikasi stok menipis.'
                        ];
                    }
                }
            }
        }

        $this->db->trans_commit();

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

    /**
     * Get stock movement report with running balance
     * @param array $filters (date_start, date_end, item_id, category_id)
     * @return array
     */
    public function get_stock_movement_report(array $filters = [])
    {
        // Build base query with joins
        $this->db
            ->select('stock_movement.*, stock_item.item_name, stock_category.category_name, user.nama as user_name')
            ->from('stock_movement')
            ->join('stock_item', 'stock_item.id_item = stock_movement.item_id', 'left')
            ->join('stock_category', 'stock_category.id_category = stock_item.category_id', 'left')
            ->join('user', 'user.id_user = stock_movement.user_id', 'left');

        // Apply date range filter (inclusive)
        if (!empty($filters['date_start'])) {
            $this->db->where('stock_movement.created_at >=', $filters['date_start'] . ' 00:00:00');
        }
        if (!empty($filters['date_end'])) {
            $this->db->where('stock_movement.created_at <=', $filters['date_end'] . ' 23:59:59');
        }

        // Apply item filter
        if (!empty($filters['item_id'])) {
            $this->db->where('stock_movement.item_id', $filters['item_id']);
        }

        // Apply category filter
        if (!empty($filters['category_id'])) {
            $this->db->where('stock_item.category_id', $filters['category_id']);
        }

        // Get all movements ordered by date ASC for running balance calculation
        $movements = $this->db
            ->order_by('stock_movement.created_at', 'ASC')
            ->get()
            ->result_array();

        // Compute running balance
        return $this->compute_running_balance($movements);
    }

    /**
     * Get audit trail report (optimized for audit log view)
     * @param array $filters (date_start, date_end, item_id, category_id)
     * @return array
     */
    public function get_audit_trail_report(array $filters = [])
    {
        // Build base query with joins
        $this->db
            ->select('stock_movement.*, stock_item.item_name, stock_category.category_name, user.nama as user_name')
            ->from('stock_movement')
            ->join('stock_item', 'stock_item.id_item = stock_movement.item_id', 'left')
            ->join('stock_category', 'stock_category.id_category = stock_item.category_id', 'left')
            ->join('user', 'user.id_user = stock_movement.user_id', 'left');

        // Apply date range filter (inclusive)
        if (!empty($filters['date_start'])) {
            $this->db->where('stock_movement.created_at >=', $filters['date_start'] . ' 00:00:00');
        }
        if (!empty($filters['date_end'])) {
            $this->db->where('stock_movement.created_at <=', $filters['date_end'] . ' 23:59:59');
        }

        // Apply item filter
        if (!empty($filters['item_id'])) {
            $this->db->where('stock_movement.item_id', $filters['item_id']);
        }

        // Apply category filter
        if (!empty($filters['category_id'])) {
            $this->db->where('stock_item.category_id', $filters['category_id']);
        }

        // Return ordered by date DESC (newest first for audit trail view)
        return $this->db
            ->order_by('stock_movement.created_at', 'DESC')
            ->get()
            ->result_array();
    }

    /**
     * Compute running balance for stock movements
     * Movement type delta mapping:
     * - in: +qty (adds to available)
     * - out: -qty (subtracts from available)
     * - adjust: +qty (manual adjustment, can be positive or negative)
     * - reserve: -qty (moves from available to reserved)
     * - cancel: +qty (returns reserved to available)
     * - deliver: 0 (no change to available, moves from reserved to used)
     * @param array $movements
     * @return array
     */
    private function compute_running_balance(array $movements)
    {
        // Group movements by item to compute running balance per item
        $item_balances = [];

        foreach ($movements as &$movement) {
            $item_id = $movement['item_id'];

            // Initialize balance for this item if not exists
            if (!isset($item_balances[$item_id])) {
                $item_balances[$item_id] = 0;
            }

            // Apply delta based on movement type
            $delta = 0;
            $qty_delta = (int) $movement['qty_delta'];

            switch ($movement['movement_type']) {
                case 'in':
                    $delta = $qty_delta;
                    break;
                case 'out':
                    $delta = -$qty_delta;
                    break;
                case 'adjust':
                    $delta = $qty_delta; // Can be positive or negative
                    break;
                case 'reserve':
                    $delta = -$qty_delta;
                    break;
                case 'cancel':
                    $delta = $qty_delta;
                    break;
                case 'deliver':
                    $delta = 0; // No change to available qty
                    break;
                default:
                    $delta = 0;
            }

            // Update running balance
            $item_balances[$item_id] += $delta;
            $movement['running_balance'] = $item_balances[$item_id];
        }

        return $movements;
    }
}
