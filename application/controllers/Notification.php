<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notification extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        check_not_login();
        $level = (int) $this->session->userdata('level');
        if (!in_array($level, [1, 2], true)) {
            redirect('auth/login');
        }
        $this->load->model('Notification_model');
        $this->load->library('session');
    }

    public function index()
    {
        $user_id = (int) $this->session->userdata('id_user');
        if ($user_id <= 0) {
            $this->session->set_flashdata('error', 'Akun tidak valid.');
            redirect('dashboard');
        }

        $data = [
            'page' => 'Notifications',
            'notifications' => $this->Notification_model->get_by_user($user_id, 50)
        ];

        $this->template->loadmodern('notification/index-modern', $data);
    }

    public function mark_read($id)
    {
        $id_notification = (int) $id;
        $user_id = (int) $this->session->userdata('id_user');

        if ($user_id <= 0 || $id_notification <= 0) {
            $this->session->set_flashdata('error', 'Notifikasi tidak valid.');
            redirect('notification');
        }

        $result = $this->Notification_model->mark_read($id_notification, $user_id);
        if ($result['success']) {
            $this->session->set_flashdata('success', $result['message']);
        } else {
            $this->session->set_flashdata('error', $result['message']);
        }

        redirect('notification');
    }

    public function mark_all_read()
    {
        $user_id = (int) $this->session->userdata('id_user');

        if ($user_id <= 0) {
            $this->session->set_flashdata('error', 'Akun tidak valid.');
            redirect('notification');
        }

        $result = $this->Notification_model->mark_all_read($user_id);
        if ($result['success']) {
            $this->session->set_flashdata('success', $result['message']);
        } else {
            $this->session->set_flashdata('error', $result['message']);
        }

        redirect('notification');
    }
}
