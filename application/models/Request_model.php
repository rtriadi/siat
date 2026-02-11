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
        return $this->db
            ->from('request_header')
            ->where('user_id', $user_id)
            ->order_by('created_at', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_all($filters = [])
    {
        $this->db->from('request_header');

        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }

        if (!empty($filters['user_id'])) {
            $this->db->where('user_id', $filters['user_id']);
        }

        return $this->db
            ->order_by('created_at', 'DESC')
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

        if ($this->db->trans_status() === false) {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return ['success' => false, 'message' => $error['message'] ?? 'Gagal mengirim permintaan.'];
        }

        $this->db->trans_commit();

        return ['success' => true, 'message' => 'Permintaan telah dikirim.'];
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
