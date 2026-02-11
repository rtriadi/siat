<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        check_not_login();
        check_admin();
    }

    public function index()
    {
        $data['page'] = 'Dashboard';
        $this->template->load('layout/template', 'dashboard', $data);
    }
}
