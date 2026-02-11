<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stock extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        check_not_login();
        check_admin();
        $this->load->model('stock_model');
        $this->load->model('category_model');
        $this->load->library('session');
        $this->load->library('form_validation');
    }

    /**
     * Stock list view with category grouping and low-stock alerts
     */
    public function index()
    {
        $items = $this->stock_model->get_all();
        
        // Group by category for display
        $grouped = [];
        foreach ($items as $item) {
            $category_name = $item['category_name'] ?? 'Tanpa Kategori';
            if (!isset($grouped[$category_name])) {
                $grouped[$category_name] = [];
            }
            $grouped[$category_name][] = $item;
        }
        
        $data = [
            'page' => 'Stock Management',
            'grouped_items' => $grouped
        ];
        
        $this->template->load('layout/template', 'stock/index', $data);
    }

    /**
     * Show create item form
     */
    public function create()
    {
        $data = [
            'page' => 'Tambah Item',
            'categories' => $this->category_model->get_all()
        ];
        
        $this->template->load('layout/template', 'stock/form', $data);
    }

    /**
     * Store new item with validation
     */
    public function store()
    {
        $this->form_validation->set_rules('category_id', 'Kategori', 'required|trim');
        $this->form_validation->set_rules('item_name', 'Nama Item', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('available_qty', 'Jumlah Awal', 'required|integer|greater_than_equal_to[0]');
        $this->form_validation->set_rules('low_stock_threshold', 'Batas Stok Minimum', 'required|integer|greater_than_equal_to[0]');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('stock/create');
        }

        $data = [
            'category_id' => $this->input->post('category_id'),
            'item_name' => $this->input->post('item_name'),
            'available_qty' => $this->input->post('available_qty'),
            'low_stock_threshold' => $this->input->post('low_stock_threshold')
        ];

        $result = $this->stock_model->create_item($data);

        if ($result['success']) {
            $this->session->set_flashdata('success', $result['message']);
            redirect('stock');
        } else {
            $this->session->set_flashdata('error', $result['message']);
            redirect('stock/create');
        }
    }

    /**
     * Show edit item form
     */
    public function edit($id_item)
    {
        $item = $this->stock_model->get_by_id($id_item);
        
        if (!$item) {
            $this->session->set_flashdata('error', 'Item tidak ditemukan.');
            redirect('stock');
        }
        
        $data = [
            'page' => 'Edit Item',
            'item' => $item,
            'categories' => $this->category_model->get_all()
        ];
        
        $this->template->load('layout/template', 'stock/form', $data);
    }

    /**
     * Update item details and adjust quantities if needed
     */
    public function update($id_item)
    {
        $this->form_validation->set_rules('category_id', 'Kategori', 'required|trim');
        $this->form_validation->set_rules('item_name', 'Nama Item', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('low_stock_threshold', 'Batas Stok Minimum', 'required|integer|greater_than_equal_to[0]');
        $this->form_validation->set_rules('qty_adjustment', 'Penyesuaian Stok', 'integer');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('stock/edit/' . $id_item);
        }

        // Update item details
        $item_data = [
            'category_id' => $this->input->post('category_id'),
            'item_name' => $this->input->post('item_name'),
            'low_stock_threshold' => $this->input->post('low_stock_threshold')
        ];

        $result = $this->stock_model->update_item($id_item, $item_data);

        if (!$result['success']) {
            $this->session->set_flashdata('error', $result['message']);
            redirect('stock/edit/' . $id_item);
        }

        // Handle stock quantity adjustment if provided
        $qty_adjustment = $this->input->post('qty_adjustment');
        if ($qty_adjustment !== null && $qty_adjustment !== '' && (int)$qty_adjustment !== 0) {
            $adjustment_reason = $this->input->post('adjustment_reason') ?? 'Manual adjustment';
            $user_id = $this->fungsi->user_login()->id_user;
            
            $adjustment_result = $this->stock_model->adjust_stock(
                $id_item,
                'adjust',
                (int)$qty_adjustment,
                $adjustment_reason,
                $user_id
            );

            if (!$adjustment_result['success']) {
                $this->session->set_flashdata('error', $adjustment_result['message']);
                redirect('stock/edit/' . $id_item);
            }
        }

        $this->session->set_flashdata('success', 'Item berhasil diubah.');
        redirect('stock');
    }
}
