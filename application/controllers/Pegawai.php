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
        $data['total_requests'] = 0;
        $data['pending_requests'] = 0;
        $data['approved_requests'] = 0;
        $data['delivered_requests'] = 0;
        $data['unread_notifications'] = 0;
        
        $this->template->loadmodern('pegawai/dashboard-modern', $data);
    }
}
