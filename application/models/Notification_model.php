<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notification_model extends CI_Model
{
    public function create_for_user($user_id, $title, $message, $type, $source_id = null)
    {
        $user_id = (int) $user_id;
        $title = trim((string) $title);
        $message = trim((string) $message);

        if ($user_id <= 0 || $title === '' || $message === '') {
            return [
                'success' => false,
                'message' => 'Data notifikasi tidak valid.'
            ];
        }

        if (!in_array($type, ['request', 'stock'], true)) {
            return [
                'success' => false,
                'message' => 'Tipe notifikasi tidak valid.'
            ];
        }

        $payload = [
            'user_id' => $user_id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'source_id' => $source_id !== null ? (int) $source_id : null,
            'is_read' => 0
        ];

        if ($this->db->insert('notification', $payload)) {
            return [
                'success' => true,
                'id_notification' => $this->db->insert_id()
            ];
        }

        $error = $this->db->error();
        return [
            'success' => false,
            'message' => $error['message'] ?? 'Gagal membuat notifikasi.'
        ];
    }

    public function create_for_users(array $user_ids, $title, $message, $type, $source_id = null)
    {
        $title = trim((string) $title);
        $message = trim((string) $message);

        if ($title === '' || $message === '') {
            return [
                'success' => false,
                'message' => 'Data notifikasi tidak valid.'
            ];
        }

        if (!in_array($type, ['request', 'stock'], true)) {
            return [
                'success' => false,
                'message' => 'Tipe notifikasi tidak valid.'
            ];
        }

        $unique_ids = [];
        foreach ($user_ids as $user_id) {
            $user_id = (int) $user_id;
            if ($user_id > 0) {
                $unique_ids[$user_id] = true;
            }
        }

        if (empty($unique_ids)) {
            return [
                'success' => true,
                'created' => 0
            ];
        }

        $rows = [];
        foreach (array_keys($unique_ids) as $user_id) {
            $rows[] = [
                'user_id' => $user_id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'source_id' => $source_id !== null ? (int) $source_id : null,
                'is_read' => 0
            ];
        }

        $result = $this->db->insert_batch('notification', $rows);
        if ($result) {
            return [
                'success' => true,
                'created' => $result
            ];
        }

        $error = $this->db->error();
        return [
            'success' => false,
            'message' => $error['message'] ?? 'Gagal membuat notifikasi.'
        ];
    }

    public function get_by_user($user_id, $limit = 50)
    {
        $user_id = (int) $user_id;
        $limit = (int) $limit;
        if ($limit <= 0) {
            $limit = 50;
        }

        return $this->db
            ->from('notification')
            ->where('user_id', $user_id)
            ->order_by('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->result_array();
    }

    public function mark_read($id_notification, $user_id)
    {
        $id_notification = (int) $id_notification;
        $user_id = (int) $user_id;

        if ($id_notification <= 0 || $user_id <= 0) {
            return [
                'success' => false,
                'message' => 'Data notifikasi tidak valid.'
            ];
        }

        $this->db->update(
            'notification',
            [
                'is_read' => 1,
                'read_at' => date('Y-m-d H:i:s')
            ],
            [
                'id_notification' => $id_notification,
                'user_id' => $user_id
            ]
        );

        if ($this->db->affected_rows() === 0) {
            return [
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Notifikasi ditandai dibaca.'
        ];
    }
}
