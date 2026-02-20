<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Signatory_model extends CI_Model
{
    /**
     * Get all signatory roles along with the assigned user details
     */
    public function get_all_roles()
    {
        $this->db->select('sr.*, u.nama as user_name, u.nip as user_nip');
        $this->db->from('signatory_role sr');
        $this->db->join('user u', 'u.id_user = sr.user_id', 'left');
        $this->db->order_by('sr.id', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Get active employees (level 2) to populate the select dropdown
     */
    public function get_active_employees()
    {
        $this->db->select('id_user, nama, nip');
        $this->db->from('user');
        $this->db->where('level', 2);
        $this->db->where('is_active', 1);
        $this->db->order_by('nama', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Update the assigned user for a specific role code
     */
    public function update_role_assignment($role_code, $user_id)
    {
        $data = ['user_id' => $user_id ? $user_id : null];
        $this->db->where('role_code', $role_code);
        return $this->db->update('signatory_role', $data);
    }
}
