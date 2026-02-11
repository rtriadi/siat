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
        $data['page'] = 'Dashboard';
        $this->template->load('layout/template', 'pegawai/dashboard', $data);
    }
}
