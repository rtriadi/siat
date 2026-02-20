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
     * Get next item code (BRG-0001 to BRG-9999)
     * @return string
     */
    public function get_next_item_code()
    {
        $this->db->select('item_code');
        $this->db->from('stock_item');
        $this->db->like('item_code', 'BRG-', 'after');
        $this->db->order_by('item_code', 'DESC');
        $this->db->limit(1);
        
        $result = $this->db->get()->row_array();
        
        if ($result && !empty($result['item_code'])) {
            $last_num = (int) str_replace('BRG-', '', $result['item_code']);
            if ($last_num < 9999) {
                return 'BRG-' . str_pad($last_num + 1, 4, '0', STR_PAD_LEFT);
            }
        }
        
        return 'BRG-0001';
    }

    /**
     * Create new item with auto-generated item code
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

        $item_code = $this->get_next_item_code();

        // Treat identifiers as strings to prevent numeric coercion
        $payload = [
            'item_code' => $item_code,
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
                'message' => 'Item berhasil dibuat dengan kode ' . $item_code
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
     * Pastikan apakah tahun ini butuh rollover
     * Hanya butuh jika tahun login > tahun transaksi pertama di sistem
     */
    public function needs_rollover($year)
    {
        $this->db->select('YEAR(MIN(created_at)) as first_year');
        $first_tx = $this->db->get('stock_movement')->row_array();
        
        $first_year = !empty($first_tx['first_year']) ? (int)$first_tx['first_year'] : (int)date('Y');
        
        return $year > $first_year;
    }

    /**
     * Cek apakah rollover untuk tahun tsb sudah komplit
     */
    public function check_rollover_status($year)
    {
        if (!$this->needs_rollover($year)) {
            return true;
        }

        $status = $this->db->get_where('yearly_rollover', ['year' => $year])->row_array();
        return ($status && $status['status'] === 'completed');
    }

    /**
     * Cek apakah periode/tahun tertentu sudah ditutup (is_closed = 1)
     */
    public function check_period_closed($year)
    {
        $status = $this->db->get_where('yearly_rollover', ['year' => $year])->row_array();
        return ($status && isset($status['is_closed']) && $status['is_closed'] == 1);
    }

    /**
     * Tutup periode untuk tahun berjalan agar bisa ditarik ke tahun berikutnya
     */
    public function close_period($year, $admin_id)
    {
        if ($this->check_period_closed($year)) {
            return ['success' => false, 'message' => 'Periode tahun ' . $year . ' sudah ditutup sebelumnya.'];
        }

        // We check if the row exists
        $status = $this->db->get_where('yearly_rollover', ['year' => $year])->row_array();

        if ($status) {
            $this->db->where('year', $year)->update('yearly_rollover', [
                'is_closed' => 1,
                'closed_at' => date('Y-m-d H:i:s'),
                'closed_by' => $admin_id
            ]);
        } else {
            $this->db->insert('yearly_rollover', [
                'year' => $year,
                'status' => 'pending', // Pending because it hasn't pulled forward anything itself yet (if applicable)
                'is_closed' => 1,
                'closed_at' => date('Y-m-d H:i:s'),
                'closed_by' => $admin_id
            ]);
        }

        return ['success' => true, 'message' => 'Periode tahun ' . $year . ' berhasil ditutup. Data sisa siap ditarik ke tahun berikutnya.'];
    }

    /**
     * Eksekusi penarikan saldo sisa dari akhir tahun sebelumnya (dec 31)
     */
    public function process_yearly_rollover($year, $admin_id)
    {
        if ($this->check_rollover_status($year)) {
            return ['success' => false, 'message' => 'Tarik data sudah dilakukan untuk tahun ini.'];
        }

        $prev_year = $year - 1;
        
        // Pengecekan krusial: Pastikan tahun sebelumnya sudah ditutup (Tutup Periode)
        if (!$this->check_period_closed($prev_year)) {
            return ['success' => false, 'message' => 'Gagal menarik data. Anda harus melakukan "Tutup Periode" untuk tahun ' . $prev_year . ' terlebih dahulu.'];
        }

        // Use keadaan_barang logic to get exact ending stock of previous year
        $filters = [
            'year' => $prev_year
        ];
        $prev_stock = $this->get_keadaan_barang($filters);
        
        $this->db->trans_begin();

        $movements = [];
        $insertedCount = 0;

        foreach ($prev_stock as $item) {
            $ending_stock = (int)$item['ending_stock'];
            if ($ending_stock > 0) {
                // Determine item_id
                $db_item = $this->db->select('id_item')->get_where('stock_item', ['item_code' => $item['item_code']])->row_array();
                if (!$db_item) continue;
                
                $item_id = $db_item['id_item'];
                
                // Add initial stock movement for exactly Jan 1st 00:00:00 of the current year
                $movements[] = [
                    'item_id' => $item_id,
                    'movement_type' => 'adjust', // Using adjust for generic initial injection
                    'qty_delta' => $ending_stock,
                    'reason' => 'Saldo Awal Tahun ' . $year,
                    'user_id' => $admin_id,
                    'created_at' => $year . '-01-01 00:00:00'
                ];
                $insertedCount++;
            }
        }

        // Insert movements
        if (!empty($movements)) {
            $this->db->insert_batch('stock_movement', $movements);
        }

        // Record rollover status
        $this->db->replace('yearly_rollover', [
            'year' => $year,
            'status' => 'completed',
            'processed_at' => date('Y-m-d H:i:s'),
            'processed_by' => $admin_id
        ]);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'Gagal menarik data stok awal.'];
        }

        $this->db->trans_commit();

        return [
            'success' => true, 
            'message' => 'Berhasil menarik ' . $insertedCount . ' item sebagai saldo awal tahun ' . $year . '.'
        ];
    }

    public function get_all($filters = [])
    {
        $year = $this->session->userdata('login_year') ?? date('Y');

        $this->db
            ->select("
                stock_item.id_item, 
                stock_item.item_code, 
                stock_item.category_id, 
                stock_item.item_name, 
                stock_item.low_stock_threshold, 
                stock_category.category_name,
                COALESCE(SUM(CASE 
                    WHEN sm.movement_type IN ('in', 'adjust', 'cancel') THEN sm.qty_delta
                    WHEN sm.movement_type = 'reserve' THEN -sm.qty_delta
                    ELSE 0
                END), 0) as available_qty
            ", false)
            ->from('stock_item')
            ->join('stock_category', 'stock_category.id_category = stock_item.category_id', 'left')
            ->join('stock_movement sm', "sm.item_id = stock_item.id_item AND YEAR(sm.created_at) = '$year'", 'left');

        if (isset($filters['category_id'])) {
            $this->db->where('stock_item.category_id', $filters['category_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('stock_item.item_name', $search);
            $this->db->or_like('stock_category.category_name', $search);
            $this->db->group_end();
        }

        $this->db->group_by('stock_item.id_item, stock_item.item_code, stock_item.category_id, stock_item.item_name, stock_item.low_stock_threshold, stock_category.category_name');

        if (isset($filters['low_stock_only']) && $filters['low_stock_only']) {
            $this->db->having('available_qty <= stock_item.low_stock_threshold');
        }

        if (!empty($filters['order_by'])) {
            $this->db->order_by($filters['order_by']);
        } else {
            $this->db->order_by('stock_category.category_name', 'ASC');
            $this->db->order_by('stock_item.item_name', 'ASC');
        }

        return $this->db->get()->result_array();
    }

    public function get_all_paginated($filters = [], $per_page = 10, $offset = 0)
    {
        $year = $this->session->userdata('login_year') ?? date('Y');

        $this->db
            ->select("
                stock_item.id_item, 
                stock_item.item_code, 
                stock_item.category_id, 
                stock_item.item_name, 
                stock_item.low_stock_threshold, 
                stock_category.category_name,
                COALESCE(SUM(CASE 
                    WHEN sm.movement_type IN ('in', 'adjust', 'cancel') THEN sm.qty_delta
                    WHEN sm.movement_type = 'reserve' THEN -sm.qty_delta
                    ELSE 0
                END), 0) as available_qty
            ", false)
            ->from('stock_item')
            ->join('stock_category', 'stock_category.id_category = stock_item.category_id', 'left')
            ->join('stock_movement sm', "sm.item_id = stock_item.id_item AND YEAR(sm.created_at) = '$year'", 'left');

        if (isset($filters['category_id'])) {
            $this->db->where('stock_item.category_id', $filters['category_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('stock_item.item_name', $search);
            $this->db->or_like('stock_category.category_name', $search);
            $this->db->group_end();
        }

        $this->db->group_by('stock_item.id_item, stock_item.item_code, stock_item.category_id, stock_item.item_name, stock_item.low_stock_threshold, stock_category.category_name');

        if (isset($filters['low_stock_only']) && $filters['low_stock_only']) {
            $this->db->having('available_qty <= stock_item.low_stock_threshold');
        }

        // To get total rows accurately with GROUP BY and HAVING
        $count_query = clone $this->db;
        $total = $count_query->get()->num_rows();

        $this->db->order_by('stock_category.category_name', 'ASC');
        $this->db->order_by('stock_item.item_name', 'ASC');
        
        $result = $this->db->limit($per_page, $offset)->get()->result_array();
        
        return [
            'rows' => $result,
            'total' => $total
        ];
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
     * @param string|null $purchase_date
     * @return array ['success' => bool, 'message' => string]
     */
    public function adjust_stock($id_item, $type, $qty_delta, $reason, $user_id, $purchase_date = null)
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
        $movement_data = [
            'item_id' => $id_item,
            'movement_type' => $type,
            'qty_delta' => $qty_delta,
            'reason' => $reason,
            'user_id' => $user_id
        ];

        if ($purchase_date !== null && $type === 'in') {
            $movement_data['purchase_date'] = $purchase_date;
        }

        $this->db->insert('stock_movement', $movement_data);

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
        $year = $this->session->userdata('login_year') ?? date('Y');
        return $this->db
            ->select('stock_movement.*, user.nama as user_name')
            ->from('stock_movement')
            ->join('user', 'user.id_user = stock_movement.user_id', 'left')
            ->where('stock_movement.item_id', $id_item)
            ->where('YEAR(stock_movement.created_at)', $year)
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

        // Exclude internal virtual locks from the physical ledger report
        $this->db->where_not_in('stock_movement.movement_type', ['reserve', 'cancel']);

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

        $year = $this->session->userdata('login_year') ?? date('Y');
        $this->db->where('YEAR(stock_movement.created_at)', $year);

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
     * Get stock levels report with category filter
     * @param array $filters (category_id)
     * @return array
     */
    public function get_stock_levels_report(array $filters = [])
    {
        $this->db
            ->select('stock_item.id_item, stock_item.item_name, stock_item.available_qty, stock_item.reserved_qty, stock_item.used_qty, stock_item.low_stock_threshold, stock_category.category_name')
            ->from('stock_item')
            ->join('stock_category', 'stock_category.id_category = stock_item.category_id', 'left');

        // Apply category filter
        if (!empty($filters['category_id'])) {
            $this->db->where('stock_item.category_id', $filters['category_id']);
        }

        return $this->db
            ->order_by('stock_category.category_name', 'ASC')
            ->order_by('stock_item.item_name', 'ASC')
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
                case 'adjust':
                    $delta = $qty_delta;
                    break;
                case 'out':
                case 'deliver':
                    $delta = -$qty_delta;
                    break;
                case 'reserve':
                case 'cancel':
                    $delta = 0; // Excluded from physical ledger natively
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

    public function get_rkbp($filters)
    {
        $this->db->select('stock_item.*, stock_category.category_name');
        $this->db->from('stock_item');
        $this->db->join('stock_category', 'stock_category.id_category = stock_item.category_id', 'left');
        $this->db->order_by('stock_category.category_name', 'ASC');
        $this->db->order_by('stock_item.item_name', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_buku_bantu($filters, $type)
    {
        $this->db->select('
            stock_movement.*, 
            stock_item.item_name, 
            stock_item.unit, 
            request_header.request_no, 
            user.nama AS pegawai_nama,
            COALESCE(stock_movement.purchase_date, DATE(stock_movement.created_at)) AS display_date
        ');
        $this->db->from('stock_movement');
        $this->db->join('stock_item', 'stock_movement.item_id = stock_item.id_item', 'left');
        $this->db->join('request_header', 'request_header.request_no = SUBSTRING_INDEX(stock_movement.reason, "#", -1)', 'left');
        $this->db->join('user', 'user.id_user = request_header.user_id', 'left');
        
        if ($type === 'in') {
            $this->db->where_in('stock_movement.movement_type', ['in', 'adjust']);
        } elseif ($type === 'out') {
            $this->db->where_in('stock_movement.movement_type', ['out', 'deliver']);
        }

        // Filters use the coalesce date
        if (!empty($filters['date_start'])) $this->db->where('COALESCE(stock_movement.purchase_date, DATE(stock_movement.created_at)) >=', $filters['date_start']);
        if (!empty($filters['date_end'])) $this->db->where('COALESCE(stock_movement.purchase_date, DATE(stock_movement.created_at)) <=', $filters['date_end']);
        if (!empty($filters['month'])) $this->db->where('MONTH(COALESCE(stock_movement.purchase_date, stock_movement.created_at)) =', $filters['month']);
        if (!empty($filters['year'])) $this->db->where('YEAR(COALESCE(stock_movement.purchase_date, stock_movement.created_at)) =', $filters['year']);
        
        $this->db->order_by('display_date', 'ASC');
        $this->db->order_by('stock_movement.created_at', 'ASC'); // secondary sort
        return $this->db->get()->result_array();
    }

    public function get_laporan_biaya($filters)
    {
        $in = $this->get_buku_bantu($filters, 'in');
        $out = $this->get_buku_bantu($filters, 'out');
        
        // Dummy values since schema lacks pricing
        $total_in = count($in) * 15000; 
        $total_out = count($out) * 5000;

        return [
            ['uraian' => 'Saldo Awal', 'pemasukan' => 0, 'pengeluaran' => 0, 'total' => 0],
            ['uraian' => 'Penerimaan Biaya ATK Perkara', 'pemasukan' => $total_in, 'pengeluaran' => 0, 'total' => 0],
            ['uraian' => 'Pengeluaran Belanja ATK', 'pemasukan' => 0, 'pengeluaran' => $total_out, 'total' => 0],
            ['uraian' => 'SALDO', 'pemasukan' => $total_in, 'pengeluaran' => $total_out, 'total' => ($total_in > 0 ? $total_in - $total_out : -$total_out)]
        ];
    }

    public function get_keadaan_barang($filters)
    {
        // 1. Get current stock for all items
        $this->db->select('id_item, item_code, item_name, available_qty, reserved_qty, unit');
        $items = $this->db->get('stock_item')->result_array();

        // 2. Determine the period boundary
        $date_start = null;
        $date_end = null;
        
        if (!empty($filters['month']) && !empty($filters['year'])) {
            $date_start = $filters['year'] . '-' . str_pad($filters['month'], 2, '0', STR_PAD_LEFT) . '-01 00:00:00';
            $date_end = date('Y-m-t 23:59:59', strtotime($date_start));
        } elseif (!empty($filters['year'])) {
            $date_start = $filters['year'] . '-01-01 00:00:00';
            $date_end = $filters['year'] . '-12-31 23:59:59';
        } elseif (!empty($filters['date_start']) && !empty($filters['date_end'])) {
            $date_start = $filters['date_start'] . ' 00:00:00';
            $date_end = $filters['date_end'] . ' 23:59:59';
        }

        $processed = [];
        foreach ($items as $item) {
            $processed[$item['id_item']] = [
                'item_code' => $item['item_code'],
                'item_name' => $item['item_name'],
                'unit' => $item['unit'] ?: 'Pcs',
                'beginning_stock' => 0,
                'stock_in' => 0,
                'stock_out' => 0,
                'ending_stock' => 0,
                // Internal use for back-calculation: physical stock in warehouse
                'current_qty' => ((int)$item['available_qty'] + (int)$item['reserved_qty']),
                'future_in' => 0,
                'future_out' => 0
            ];
        }

        // 3. Get ALL movements to back-calculate beginning stock and get period movements
        $this->db->select('item_id, movement_type, qty_delta, created_at');
        $all_movements = $this->db->get('stock_movement')->result_array();

        foreach ($all_movements as $mov) {
            $id = $mov['item_id'];
            if (!isset($processed[$id])) continue;

            $m_date = $mov['created_at'];
            $qty = (int)$mov['qty_delta'];
            $type = $mov['movement_type'];

            // Movement within selected period
            if ($m_date >= $date_start && $m_date <= $date_end) {
                if (in_array($type, ['in', 'adjust'])) {
                    $processed[$id]['stock_in'] += $qty;
                } elseif (in_array($type, ['out', 'deliver'])) {
                    $processed[$id]['stock_out'] += $qty;
                }
            } 
            // Movement AFTER selected period (used to back-calculate)
            elseif ($m_date > $date_end) {
                if (in_array($type, ['in', 'adjust'])) {
                    $processed[$id]['future_in'] += $qty;
                } elseif (in_array($type, ['out', 'deliver'])) {
                    $processed[$id]['future_out'] += $qty;
                }
            }
        }

        // 4. Final calculation: 
        // ending_stock = beginning_stock + period_in - period_out
        // We know: current_qty = ending_stock + future_in - future_out
        // So: ending_stock = current_qty - future_in + future_out
        foreach ($processed as &$p) {
            $p['ending_stock'] = $p['current_qty'] - $p['future_in'] + $p['future_out'];
            $p['beginning_stock'] = $p['ending_stock'] - $p['stock_in'] + $p['stock_out'];
        }

        return array_values($processed);
    }
}
