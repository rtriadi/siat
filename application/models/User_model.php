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
}
