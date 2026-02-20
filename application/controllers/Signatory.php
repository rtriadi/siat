<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Signatory extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        check_not_login();
        check_admin();
        $this->load->model('Signatory_model');
    }

    public function index()
    {
        $data['page'] = 'Master Penandatangan';
        $data['roles'] = $this->Signatory_model->get_all_roles();
        $data['employees'] = $this->Signatory_model->get_active_employees();

        $this->template->loadmodern('master/signatory-modern', $data);
    }

    public function update()
    {
        $roles_input = $this->input->post('roles');

        if (is_array($roles_input)) {
            $success = true;
            $this->db->trans_start();

            foreach ($roles_input as $role_code => $user_id) {
                // if empty string, it means we unset the user_id (NULL)
                $user_id_val = empty($user_id) ? null : $user_id;

                if (!$this->Signatory_model->update_role_assignment($role_code, $user_id_val)) {
                    $success = false;
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE || !$success) {
                $this->session->set_flashdata('error', 'Gagal menyimpan perubahan penandatangan.');
            } else {
                $this->session->set_flashdata('success', 'Master Penandatangan berhasil diperbarui.');
            }
        }

        redirect('signatory');
    }
}
