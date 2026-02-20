<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Request_model extends CI_Model
{
    public function create_request($user_id, $items, $note)
    {
        if (empty($items)) {
            return [
                'success' => false,
                'request_id' => null,
                'message' => 'Item permintaan belum diisi.'
            ];
        }

        $normalized_items = [];
        foreach ($items as $item) {
            $item_id = isset($item['item_id']) ? (int) $item['item_id'] : 0;
            $qty_requested = isset($item['qty_requested']) ? (int) $item['qty_requested'] : 0;
            if ($item_id <= 0 || $qty_requested <= 0) {
                return [
                    'success' => false,
                    'request_id' => null,
                    'message' => 'Item permintaan tidak valid.'
                ];
            }

            $normalized_items[] = [
                'item_id' => $item_id,
                'qty_requested' => $qty_requested,
                'note' => isset($item['note']) ? $item['note'] : null
            ];
        }

        $request_no = $this->generate_request_no();

        $this->db->trans_begin();

        $this->load->model('Notification_model');

        $this->db->insert('request_header', [
            'request_no' => $request_no,
            'user_id' => $user_id,
            'status' => 'pending',
            'notes' => $note
        ]);

        $request_id = $this->db->insert_id();

        foreach ($normalized_items as $item) {
            $this->db->insert('request_item', [
                'request_id' => $request_id,
                'item_id' => $item['item_id'],
                'qty_requested' => $item['qty_requested'],
                'qty_approved' => 0,
                'qty_delivered' => 0,
                'note' => $item['note']
            ]);
        }

        $admin_rows = $this->db
            ->select('id_user')
            ->from('user')
            ->where('level', 1)
            ->where('is_active', 1)
            ->get()
            ->result_array();

        $admin_ids = [];
        foreach ($admin_rows as $admin) {
            $admin_ids[] = (int) $admin['id_user'];
        }

        $user = $this->db->select('nama')->get_where('user', ['id_user' => $user_id])->row_array();
        $user_name = $user['nama'] ?? 'Unknown';
        $item_count = count($normalized_items);

        $notify_admins = $this->Notification_model->create_for_users(
            $admin_ids,
            'Permintaan baru',
            'Permintaan baru dari ' . $user_name . ' (' . $item_count . ' item) - No: ' . $request_no,
            'request',
            $request_id
        );

        if (!$notify_admins['success']) {
            $this->db->trans_rollback();
            return [
                'success' => false,
                'request_id' => null,
                'message' => $notify_admins['message'] ?? 'Gagal membuat notifikasi permintaan.'
            ];
        }

        if ($this->db->trans_status() === false) {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return [
                'success' => false,
                'request_id' => null,
                'message' => $error['message'] ?? 'Gagal membuat permintaan.'
            ];
        }

        $this->db->trans_commit();

        return [
            'success' => true,
            'request_id' => $request_id,
            'message' => 'Permintaan berhasil dibuat.'
        ];
    }

    public function get_by_user($user_id)
    {
        $year = $this->session->userdata('login_year') ?? date('Y');
        return $this->db
            ->from('request_header')
            ->where('user_id', $user_id)
            ->where('YEAR(created_at)', $year)
            ->order_by('created_at', 'DESC')
            ->get()
            ->result_array();
    }

    public function count_active_requests()
    {
        $year = $this->session->userdata('login_year') ?? date('Y');
        return $this->db
            ->where_in('status', ['pending', 'approved'])
            ->where('YEAR(created_at)', $year)
            ->from('request_header')
            ->count_all_results();
    }

    public function get_all($filters = [])
    {
        $year = $this->session->userdata('login_year') ?? date('Y');
        $this->db->from('request_header');
        $this->db->join('user', 'user.id_user = request_header.user_id', 'left');
        $this->db->where('YEAR(request_header.created_at)', $year);

        if (!empty($filters['status'])) {
            $this->db->where('request_header.status', $filters['status']);
        }

        if (!empty($filters['user_id'])) {
            $this->db->where('request_header.user_id', $filters['user_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('request_header.request_no', $search);
            $this->db->or_like('user.nama', $search);
            $this->db->or_like('user.nip', $search);
            $this->db->group_end();
        }

        return $this->db
            ->order_by('request_header.created_at', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_detail($request_id)
    {
        $header = $this->db
            ->get_where('request_header', ['id_request' => $request_id])
            ->row_array();

        if (!$header) {
            return null;
        }

        $items = $this->db
            ->select('request_item.*, stock_item.item_name')
            ->from('request_item')
            ->join('stock_item', 'stock_item.id_item = request_item.item_id', 'left')
            ->where('request_id', $request_id)
            ->get()
            ->result_array();

        $header['items'] = $items;
        return $header;
    }

    public function approve_request($request_id, $approved_items, $note, $admin_id)
    {
        $header = $this->db->get_where('request_header', ['id_request' => $request_id])->row_array();
        if (!$header) {
            return ['success' => false, 'message' => 'Permintaan tidak ditemukan.'];
        }

        if ($header['status'] !== 'pending') {
            return ['success' => false, 'message' => 'Status permintaan tidak dapat disetujui.'];
        }

        if (empty($approved_items)) {
            return ['success' => false, 'message' => 'Item persetujuan belum diisi.'];
        }

        $request_items = $this->db
            ->get_where('request_item', ['request_id' => $request_id])
            ->result_array();

        if (empty($request_items)) {
            return ['success' => false, 'message' => 'Item permintaan tidak ditemukan.'];
        }

        $requested_map = [];
        foreach ($request_items as $item) {
            $requested_map[(int) $item['item_id']] = (int) $item['qty_requested'];
        }

        foreach ($approved_items as $item_id => $qty) {
            if (!isset($requested_map[(int) $item_id])) {
                return ['success' => false, 'message' => 'Item persetujuan tidak sesuai permintaan.'];
            }
        }

        $this->load->model('Stock_model');
        $this->load->model('Notification_model');

        $this->db->trans_begin();

        foreach ($request_items as $item) {
            $item_id = (int) $item['item_id'];
            $requested_qty = (int) $item['qty_requested'];
            $qty = isset($approved_items[$item_id]) ? (int) $approved_items[$item_id] : 0;
            if ($qty < 0 || $qty > $requested_qty) {
                $this->db->trans_rollback();
                return ['success' => false, 'message' => 'Jumlah persetujuan tidak valid.'];
            }

            $this->db->update(
                'request_item',
                ['qty_approved' => $qty],
                ['request_id' => $request_id, 'item_id' => (int) $item_id]
            );

            if ($qty > 0) {
                $result = $this->Stock_model->reserve_stock(
                    (int) $item_id,
                    $qty,
                    $admin_id,
                    'Reservasi permintaan #' . $header['request_no']
                );

                if (!$result['success']) {
                    $this->db->trans_rollback();
                    return $result;
                }
            }
        }

        $this->db->update(
            'request_header',
            [
                'status' => 'approved',
                'notes' => $note,
                'approved_by' => $admin_id,
                'approved_at' => date('Y-m-d H:i:s')
            ],
            ['id_request' => $request_id]
        );

        $notify_user = $this->Notification_model->create_for_user(
            (int) $header['user_id'],
            'Permintaan disetujui',
            'Permintaan #' . $header['request_no'] . ' telah disetujui.',
            'request',
            $request_id
        );

        if (!$notify_user['success']) {
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => $notify_user['message'] ?? 'Gagal membuat notifikasi persetujuan.'
            ];
        }

        if ($this->db->trans_status() === false) {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return ['success' => false, 'message' => $error['message'] ?? 'Gagal menyetujui permintaan.'];
        }

        $this->db->trans_commit();

        return ['success' => true, 'message' => 'Permintaan disetujui.'];
    }

    public function reject_request($request_id, $note, $admin_id)
    {
        $header = $this->db->get_where('request_header', ['id_request' => $request_id])->row_array();
        if (!$header) {
            return ['success' => false, 'message' => 'Permintaan tidak ditemukan.'];
        }

        if ($header['status'] !== 'pending') {
            return ['success' => false, 'message' => 'Status permintaan tidak dapat ditolak.'];
        }

        $this->load->model('Notification_model');

        $this->db->trans_begin();

        $this->db->update(
            'request_header',
            [
                'status' => 'rejected',
                'notes' => $note,
                'rejected_by' => $admin_id,
                'rejected_at' => date('Y-m-d H:i:s')
            ],
            ['id_request' => $request_id]
        );

        $notify_user = $this->Notification_model->create_for_user(
            (int) $header['user_id'],
            'Permintaan ditolak',
            'Permintaan #' . $header['request_no'] . ' ditolak.',
            'request',
            $request_id
        );

        if (!$notify_user['success']) {
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => $notify_user['message'] ?? 'Gagal membuat notifikasi penolakan.'
            ];
        }

        if ($this->db->trans_status() === false) {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return ['success' => false, 'message' => $error['message'] ?? 'Gagal menolak permintaan.'];
        }

        $this->db->trans_commit();

        return ['success' => true, 'message' => 'Permintaan ditolak.'];
    }

    public function cancel_request($request_id, $pegawai_id)
    {
        $header = $this->db->get_where('request_header', ['id_request' => $request_id])->row_array();
        if (!$header) {
            return ['success' => false, 'message' => 'Permintaan tidak ditemukan.'];
        }

        if ($header['status'] !== 'pending') {
            return ['success' => false, 'message' => 'Permintaan tidak dapat dibatalkan.'];
        }

        if ((int) $header['user_id'] !== (int) $pegawai_id) {
            return ['success' => false, 'message' => 'Akses tidak diizinkan.'];
        }

        $this->db->trans_begin();

        $this->db->update(
            'request_header',
            [
                'status' => 'cancelled',
                'cancelled_by' => $pegawai_id,
                'cancelled_at' => date('Y-m-d H:i:s')
            ],
            ['id_request' => $request_id]
        );

        if ($this->db->trans_status() === false) {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return ['success' => false, 'message' => $error['message'] ?? 'Gagal membatalkan permintaan.'];
        }

        $this->db->trans_commit();

        return ['success' => true, 'message' => 'Permintaan dibatalkan.'];
    }

    public function deliver_request($request_id, $delivered_items, $admin_id)
    {
        $header = $this->db->get_where('request_header', ['id_request' => $request_id])->row_array();
        if (!$header) {
            return ['success' => false, 'message' => 'Permintaan tidak ditemukan.'];
        }

        if ($header['status'] !== 'approved') {
            return ['success' => false, 'message' => 'Status permintaan tidak dapat dikirim.'];
        }

        $items = $this->db
            ->get_where('request_item', ['request_id' => $request_id])
            ->result_array();

        if (empty($items)) {
            return ['success' => false, 'message' => 'Item permintaan tidak ditemukan.'];
        }

        $this->load->model('Stock_model');
        $this->load->model('Notification_model');

        $this->db->trans_begin();

        foreach ($items as $item) {
            $item_id = (int) $item['item_id'];
            $approved_qty = (int) $item['qty_approved'];
            $delivered_qty = isset($delivered_items[$item_id]) ? (int) $delivered_items[$item_id] : 0;

            if ($delivered_qty < 0 || $delivered_qty > $approved_qty) {
                $this->db->trans_rollback();
                return ['success' => false, 'message' => 'Jumlah pengiriman tidak valid.'];
            }

            $this->db->update(
                'request_item',
                ['qty_delivered' => $delivered_qty],
                ['id_request_item' => $item['id_request_item']]
            );

            if ($delivered_qty > 0) {
                $result = $this->Stock_model->deliver_stock(
                    $item_id,
                    $delivered_qty,
                    $admin_id,
                    'Pengiriman permintaan #' . $header['request_no']
                );

                if (!$result['success']) {
                    $this->db->trans_rollback();
                    return $result;
                }
            }

            $remaining = $approved_qty - $delivered_qty;
            if ($remaining > 0) {
                $result = $this->Stock_model->release_reserved_stock(
                    $item_id,
                    $remaining,
                    $admin_id,
                    'Sisa permintaan #' . $header['request_no']
                );

                if (!$result['success']) {
                    $this->db->trans_rollback();
                    return $result;
                }
            }
        }

        $this->db->update(
            'request_header',
            [
                'status' => 'delivered',
                'delivered_by' => $admin_id,
                'delivered_at' => date('Y-m-d H:i:s')
            ],
            ['id_request' => $request_id]
        );

        $notify_user = $this->Notification_model->create_for_user(
            (int) $header['user_id'],
            'Permintaan dikirim',
            'Permintaan #' . $header['request_no'] . ' telah dikirim.',
            'request',
            $request_id
        );

        if (!$notify_user['success']) {
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => $notify_user['message'] ?? 'Gagal membuat notifikasi pengiriman.'
            ];
        }

        if ($this->db->trans_status() === false) {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return ['success' => false, 'message' => $error['message'] ?? 'Gagal mengirim permintaan.'];
        }

        $this->db->trans_commit();

        return ['success' => true, 'message' => 'Permintaan telah dikirim.'];
    }

    public function get_request_history_report(array $filters = [])
    {
        $this->db
            ->select([
                'request_header.id_request',
                'request_header.request_no',
                'request_header.status',
                'request_header.created_at',
                'request_header.approved_at',
                'request_header.delivered_at',
                'request_item.id_request_item',
                'request_item.qty_requested',
                'request_item.qty_approved',
                'request_item.qty_delivered',
                'request_item.note as item_note',
                'stock_item.item_name',
                'user.id_user',
                'user.nama',
                'user.nip',
                'user.jabatan'
            ])
            ->from('request_header')
            ->join('request_item', 'request_item.request_id = request_header.id_request', 'inner')
            ->join('stock_item', 'stock_item.id_item = request_item.item_id', 'left')
            ->join('user', 'user.id_user = request_header.user_id', 'left');

        $year = $this->session->userdata('login_year') ?? date('Y');
        $this->db->where('YEAR(request_header.created_at)', $year);

        if (!empty($filters['date_start'])) {
            $date_start = date('Y-m-d 00:00:00', strtotime($filters['date_start']));
            $this->db->where('request_header.created_at >=', $date_start);
        }

        if (!empty($filters['date_end'])) {
            $date_end = date('Y-m-d 23:59:59', strtotime($filters['date_end']));
            $this->db->where('request_header.created_at <=', $date_end);
        }

        if (!empty($filters['status'])) {
            $this->db->where('request_header.status', $filters['status']);
        }

        if (!empty($filters['user_id'])) {
            $this->db->where('request_header.user_id', (int) $filters['user_id']);
        }

        $this->db
            ->order_by('request_header.created_at', 'DESC')
            ->order_by('request_item.id_request_item', 'ASC');

        return $this->db->get()->result_array();
    }

    private function generate_request_no()
    {
        $prefix = 'REQ-' . date('Ymd');
        $count = $this->db
            ->like('request_no', $prefix, 'after')
            ->from('request_header')
            ->count_all_results();

        $sequence = str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
        return $prefix . '-' . $sequence;
    }
}
