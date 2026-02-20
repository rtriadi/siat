<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Request extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        check_not_login();
        check_pegawai();
        $this->load->model('Request_model');
        $this->load->model('Stock_model');
        $this->load->library('session');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $user_id = $this->session->userdata('id_user');
        $data = [
            'page' => 'Permintaan ATK',
            'requests' => $this->Request_model->get_by_user($user_id),
            'is_closed' => $this->Stock_model->check_period_closed($this->session->userdata('login_year') ?? date('Y'))
        ];

        $this->template->loadmodern('request/index-modern', $data);
    }

    /**
     * Helper to verify if the period is closed
     */
    private function check_period_closed()
    {
        $year = $this->session->userdata('login_year') ?? date('Y');
        if ($this->Stock_model->check_period_closed($year)) {
            $this->session->set_flashdata('error', 'Akses ditolak. Periode tahun ' . $year . ' sudah ditutup, tidak dapat membuat permintaan baru.');
            redirect('request');
            exit;
        }
    }

    public function create()
    {
        $this->check_period_closed();
        $items = $this->Stock_model->get_all();
        $available_items = [];
        foreach ($items as $item) {
            if ((int) $item['available_qty'] > 0) {
                $available_items[] = $item;
            }
        }

        $data = [
            'page' => 'Buat Permintaan',
            'items' => $available_items,
            'old_qtys' => $this->session->flashdata('old_qtys') ?? [],
            'old_note' => $this->session->flashdata('old_note') ?? ''
        ];

        $this->template->loadmodern('request/form-modern', $data);
    }

    public function store()
    {
        $this->check_period_closed();
        $qtys = $this->input->post('qty_requested');
        if (!is_array($qtys)) {
            $qtys = [];
        }

        $items = [];
        foreach ($qtys as $item_id => $qty) {
            $qty = (int) $qty;
            if ($qty > 0) {
                $items[] = [
                    'item_id' => (int) $item_id,
                    'qty_requested' => $qty
                ];
            }
        }

        $note = $this->input->post('note', true);

        $this->form_validation->set_data([
            'items' => $items,
            'note' => $note
        ]);

        $this->form_validation->set_rules('items', 'Item', 'required|callback_validate_request_items');
        $this->form_validation->set_rules('note', 'Catatan', 'max_length[500]');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error', validation_errors());
            $this->session->set_flashdata('old_qtys', $qtys);
            $this->session->set_flashdata('old_note', $note);
            redirect('request/create');
        }

        $user_id = $this->session->userdata('id_user');
        $result = $this->Request_model->create_request($user_id, $items, $note);

        if ($result['success']) {
            $this->session->set_flashdata('success', $result['message']);
            redirect('request');
        }

        $this->session->set_flashdata('error', $result['message']);
        $this->session->set_flashdata('old_qtys', $qtys);
        $this->session->set_flashdata('old_note', $note);
        redirect('request/create');
    }

    public function detail($id_request)
    {
        $id_request = (int) $id_request;
        $request = $this->Request_model->get_detail($id_request);

        if (!$request) {
            $this->session->set_flashdata('error', 'Permintaan tidak ditemukan.');
            redirect('request');
        }

        $user_id = $this->session->userdata('id_user');
        if ((int) $request['user_id'] !== (int) $user_id) {
            $this->session->set_flashdata('error', 'Akses tidak diizinkan.');
            redirect('request');
        }

        $data = [
            'page' => 'Detail Permintaan',
            'request' => $request
        ];

        $this->template->loadmodern('request/detail-modern', $data);
    }

    public function cancel($id_request)
    {
        $id_request = (int) $id_request;
        $user_id = $this->session->userdata('id_user');
        $result = $this->Request_model->cancel_request($id_request, $user_id);

        if ($result['success']) {
            $this->session->set_flashdata('success', $result['message']);
        } else {
            $this->session->set_flashdata('error', $result['message']);
        }

        redirect('request');
    }

    public function validate_request_items($items)
    {
        if (empty($items) || !is_array($items)) {
            $this->form_validation->set_message('validate_request_items', 'Pilih item dan jumlah minimal 1.');
            return false;
        }

        foreach ($items as $item) {
            $item_id = isset($item['item_id']) ? (int) $item['item_id'] : 0;
            $qty = isset($item['qty_requested']) ? (int) $item['qty_requested'] : 0;

            if ($item_id <= 0 || $qty <= 0) {
                $this->form_validation->set_message('validate_request_items', 'Jumlah item harus lebih dari 0.');
                return false;
            }

            $stock_item = $this->Stock_model->get_by_id($item_id);
            if (!$stock_item) {
                $this->form_validation->set_message('validate_request_items', 'Item tidak ditemukan.');
                return false;
            }

            $available_qty = (int) $stock_item['available_qty'];
            if ($available_qty <= 0) {
                $this->form_validation->set_message(
                    'validate_request_items',
                    'Stok untuk ' . $stock_item['item_name'] . ' habis.'
                );
                return false;
            }

            if ($qty > $available_qty) {
                $this->form_validation->set_message(
                    'validate_request_items',
                    'Stok tersedia untuk ' . $stock_item['item_name'] . ' hanya ' . $available_qty . '.'
                );
                return false;
            }
        }

        return true;
    }
}
