<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Request_admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        check_not_login();
        check_admin();
        $this->load->model('Request_model');
        $this->load->library('session');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $status = $this->input->get('status');
        $filters = [];
        if (!empty($status) && $status !== 'all') {
            $filters['status'] = $status;
        }

        $requests = $this->Request_model->get_all($filters);

        $user_map = [];
        if (!empty($requests)) {
            $user_ids = array_unique(array_map(static function ($row) {
                return (int) $row['user_id'];
            }, $requests));

            if (!empty($user_ids)) {
                $users = $this->db
                    ->select('id_user, nama, nip')
                    ->from('user')
                    ->where_in('id_user', $user_ids)
                    ->get()
                    ->result_array();

                foreach ($users as $user) {
                    $user_map[(int) $user['id_user']] = $user;
                }
            }
        }

        $data = [
            'page' => 'Request Management',
            'requests' => $requests,
            'user_map' => $user_map,
            'selected_status' => $status ?: 'all'
        ];

        $this->template->load('layout/template', 'request_admin/index', $data);
    }

    public function detail($id)
    {
        $request_id = (int) $id;
        $detail = $this->Request_model->get_detail($request_id);

        if (!$detail) {
            $this->session->set_flashdata('error', 'Permintaan tidak ditemukan.');
            redirect('request_admin');
        }

        $pegawai = $this->db
            ->get_where('user', ['id_user' => $detail['user_id']])
            ->row_array();

        $data = [
            'page' => 'Detail Permintaan',
            'request' => $detail,
            'pegawai' => $pegawai
        ];

        $this->template->load('layout/template', 'request_admin/detail', $data);
    }

    public function approve($id)
    {
        $request_id = (int) $id;
        $detail = $this->Request_model->get_detail($request_id);

        if (!$detail) {
            $this->session->set_flashdata('error', 'Permintaan tidak ditemukan.');
            redirect('request_admin');
        }

        if ($detail['status'] !== 'pending') {
            $this->session->set_flashdata('error', 'Permintaan tidak dapat disetujui.');
            redirect('request_admin/detail/' . $request_id);
        }

        if ($this->input->method() === 'post') {
            $approved_items = [];
            $input_items = $this->input->post('qty_approved');
            $errors = [];

            if (!is_array($input_items)) {
                $errors[] = 'Jumlah persetujuan belum diisi.';
            } else {
                foreach ($detail['items'] as $item) {
                    $item_id = (int) $item['item_id'];
                    $requested = (int) $item['qty_requested'];
                    $qty = isset($input_items[$item_id]) ? (int) $input_items[$item_id] : 0;

                    if ($qty < 0 || $qty > $requested) {
                        $errors[] = 'Jumlah persetujuan tidak valid untuk item ' . $item['item_name'] . '.';
                        break;
                    }

                    $approved_items[$item_id] = $qty;
                }
            }

            if (!empty($errors)) {
                $this->session->set_flashdata('error', implode('<br>', $errors));
                redirect('request_admin/approve/' . $request_id);
            }

            $note = $this->input->post('note', true);
            $admin_id = $this->fungsi->user_login()->id_user;

            $result = $this->Request_model->approve_request($request_id, $approved_items, $note, $admin_id);

            if ($result['success']) {
                $this->session->set_flashdata('success', $result['message']);
                redirect('request_admin/detail/' . $request_id);
            }

            $this->session->set_flashdata('error', $result['message']);
            redirect('request_admin/approve/' . $request_id);
        }

        $data = [
            'page' => 'Approve Permintaan',
            'request' => $detail
        ];

        $this->template->load('layout/template', 'request_admin/approve_form', $data);
    }

    public function reject($id)
    {
        if ($this->input->method() !== 'post') {
            redirect('request_admin/detail/' . (int) $id);
        }

        $this->form_validation->set_rules('note', 'Alasan', 'required|trim');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('request_admin/detail/' . (int) $id);
        }

        $request_id = (int) $id;
        $note = $this->input->post('note', true);
        $admin_id = $this->fungsi->user_login()->id_user;

        $result = $this->Request_model->reject_request($request_id, $note, $admin_id);

        if ($result['success']) {
            $this->session->set_flashdata('success', $result['message']);
        } else {
            $this->session->set_flashdata('error', $result['message']);
        }

        redirect('request_admin/detail/' . $request_id);
    }

    public function deliver($id)
    {
        $request_id = (int) $id;
        $detail = $this->Request_model->get_detail($request_id);

        if (!$detail) {
            $this->session->set_flashdata('error', 'Permintaan tidak ditemukan.');
            redirect('request_admin');
        }

        if ($detail['status'] !== 'approved') {
            $this->session->set_flashdata('error', 'Permintaan tidak dapat dikirim.');
            redirect('request_admin/detail/' . $request_id);
        }

        if ($this->input->method() === 'post') {
            $delivered_items = [];
            $input_items = $this->input->post('qty_delivered');
            $errors = [];

            if (!is_array($input_items)) {
                $errors[] = 'Jumlah pengiriman belum diisi.';
            } else {
                foreach ($detail['items'] as $item) {
                    $item_id = (int) $item['item_id'];
                    $approved = (int) $item['qty_approved'];
                    $qty = isset($input_items[$item_id]) ? (int) $input_items[$item_id] : 0;

                    if ($qty < 0 || $qty > $approved) {
                        $errors[] = 'Jumlah pengiriman tidak valid untuk item ' . $item['item_name'] . '.';
                        break;
                    }

                    $delivered_items[$item_id] = $qty;
                }
            }

            if (!empty($errors)) {
                $this->session->set_flashdata('error', implode('<br>', $errors));
                redirect('request_admin/deliver/' . $request_id);
            }

            $admin_id = $this->fungsi->user_login()->id_user;
            $result = $this->Request_model->deliver_request($request_id, $delivered_items, $admin_id);

            if ($result['success']) {
                $this->session->set_flashdata('success', $result['message']);
                redirect('request_admin/detail/' . $request_id);
            }

            $this->session->set_flashdata('error', $result['message']);
            redirect('request_admin/deliver/' . $request_id);
        }

        $data = [
            'page' => 'Deliver Permintaan',
            'request' => $detail
        ];

        $this->template->load('layout/template', 'request_admin/deliver_form', $data);
    }
}
