<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Guide extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        check_not_login();
    }

    public function index()
    {
        $data['page'] = 'Panduan Aplikasi';
        $this->template->loadmodern('guide/index-modern', $data);
    }
}
