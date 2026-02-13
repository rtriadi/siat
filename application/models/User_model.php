<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{

    public function get_by_username($username)
    {
        return $this->db
            ->select('id_user, username, password, nama, nip, unit, level, is_active, must_change_password')
            ->get_where('user', array('username' => $username))
            ->row_array();
    }

    public function get_by_id($id_user)
    {
        return $this->db->get_where('user', array('id_user' => $id_user))->row_array();
    }

    public function update_login_meta($id_user, $timestamp)
    {
        return $this->db->update(
            'user',
            array('last_login' => $timestamp),
            array('id_user' => $id_user)
        );
    }

    public function set_must_change_password($id_user, $flag)
    {
        return $this->db->update(
            'user',
            array('must_change_password' => (int) $flag),
            array('id_user' => $id_user)
        );
    }

    public function update_password($id_user, $new_password_hash)
    {
        return $this->db->update('user', 
            array('password' => $new_password_hash), 
            array('id_user' => $id_user)
        );
    }

    public function get_existing_nips($nips)
    {
        if (empty($nips)) {
            return [];
        }

        $result = $this->db
            ->select('nip, username')
            ->from('user')
            ->group_start()
                ->where_in('nip', $nips)
                ->or_where_in('username', $nips)
            ->group_end()
            ->get()
            ->result_array();

        $existing = [];
        foreach ($result as $row) {
            if (!empty($row['nip'])) {
                $existing[] = $row['nip'];
            }
            if (!empty($row['username'])) {
                $existing[] = $row['username'];
            }
        }

        return array_values(array_unique($existing));
    }

    public function insert_pegawai_batch($rows)
    {
        if (empty($rows)) {
            return [
                'success' => false,
                'inserted' => 0,
                'message' => 'Tidak ada data yang diimport.',
            ];
        }

        $payload = [];
        foreach ($rows as $row) {
            $nip = $row['nip'];
            $payload[] = [
                'username' => $nip,
                'password' => password_hash($nip, PASSWORD_DEFAULT),
                'nama' => $row['nama'],
                'nip' => $nip,
                'unit' => $row['unit'],
                'level' => 2,
                'must_change_password' => 1,
                'is_active' => 1,
            ];
        }

        $this->db->trans_start();
        $this->db->insert_batch('user', $payload);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $error = $this->db->error();
            return [
                'success' => false,
                'inserted' => 0,
                'message' => $error['message'] ?? 'Gagal menyimpan data.',
            ];
        }

        return [
            'success' => true,
            'inserted' => count($payload),
            'message' => 'OK',
        ];
    }

    public function get_all_users()
    {
        return $this->db
            ->select('user.*, user_role.nama_role')
            ->from('user')
            ->join('user_role', 'user_role.id_role = user.level', 'left')
            ->order_by('user.is_active', 'DESC')
            ->order_by('user.nama', 'ASC')
            ->get()
            ->result_array();
    }

    public function set_active($id_user, $is_active)
    {
        $user = $this->get_by_id($id_user);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Pengguna tidak ditemukan.'
            ];
        }

        if ($this->db->update('user', ['is_active' => (int) $is_active], ['id_user' => $id_user])) {
            return [
                'success' => true,
                'message' => 'Status pengguna berhasil diubah.'
            ];
        }

        return [
            'success' => false,
            'message' => 'Gagal mengubah status pengguna.'
        ];
    }

    public function create_user($data)
    {
        if (empty($data['username']) || empty($data['password']) || empty($data['nama'])) {
            return [
                'success' => false,
                'message' => 'Data tidak lengkap.'
            ];
        }

        $existing = $this->db
            ->where('username', $data['username'])
            ->or_where('nip', !empty($data['nip']) ? $data['nip'] : '')
            ->get('user')
            ->row_array();

        if ($existing) {
            if ($existing['username'] === $data['username']) {
                return [
                    'success' => false,
                    'message' => 'Username sudah digunakan.'
                ];
            }
            if (!empty($data['nip']) && $existing['nip'] === $data['nip']) {
                return [
                    'success' => false,
                    'message' => 'NIP sudah digunakan.'
                ];
            }
        }

        $payload = [
            'username' => $data['username'],
            'password' => $data['password'],
            'nama' => $data['nama'],
            'nip' => !empty($data['nip']) ? $data['nip'] : null,
            'unit' => !empty($data['unit']) ? $data['unit'] : null,
            'level' => isset($data['level']) ? (int) $data['level'] : 2,
            'is_active' => isset($data['is_active']) ? (int) $data['is_active'] : 1,
            'must_change_password' => 1,
            'created_at' => !empty($data['created_at']) ? $data['created_at'] : date('Y-m-d H:i:s')
        ];

        $this->db->trans_start();
        $this->db->insert('user', $payload);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $error = $this->db->error();
            return [
                'success' => false,
                'message' => $error['message'] ?? 'Gagal menyimpan data.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Pengguna berhasil ditambahkan.'
        ];
    }

    public function get_all_users_paginated($filters = [], $per_page = 10, $offset = 0)
    {
        $this->db
            ->select('user.*, user_role.nama_role')
            ->from('user')
            ->join('user_role', 'user_role.id_role = user.level', 'left');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('user.nama', $search);
            $this->db->or_like('user.username', $search);
            $this->db->or_like('user.nip', $search);
            $this->db->or_like('user.unit', $search);
            $this->db->group_end();
        }

        $this->db->order_by('user.is_active', 'DESC');
        $this->db->order_by('user.nama', 'ASC');
        
        $total = $this->db->count_all_results('', false);
        
        $result = $this->db->limit($per_page, $offset)->get()->result_array();
        
        return [
            'rows' => $result,
            'total' => $total
        ];
    }
}
