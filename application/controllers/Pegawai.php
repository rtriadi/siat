<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pegawai extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        check_not_login();
        check_pegawai();
    }

    public function index()
    {
        if ((int) $this->session->userdata('must_change_password') === 1) {
            $this->session->set_flashdata('warning', 'Password Anda masih default. Silakan ubah terlebih dahulu.');
            redirect('auth/change_password');
        }

        $data['page'] = 'Dashboard';
        $this->template->load('layout/template', 'pegawai/dashboard', $data);
    }
}
