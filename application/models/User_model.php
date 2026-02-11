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
}
