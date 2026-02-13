<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Category extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        check_not_login();
        check_admin();
        $this->load->model('category_model');
        $this->load->library('session');
        $this->load->library('form_validation');
    }

    /**
     * Category list view
     */
    public function index()
    {
        $search = $this->input->get('search', true);
        $page = (int) $this->input->get('page', true);
        $per_page = (int) $this->input->get('per_page', true);
        
        if ($per_page < 1) $per_page = 10;
        if ($page < 1) $page = 1;
        
        $total_rows = $this->category_model->count_all($search);
        $total_pages = ceil($total_rows / $per_page);
        
        if ($page > $total_pages && $total_pages > 0) {
            $page = $total_pages;
        }
        
        $offset = ($page - 1) * $per_page;
        
        $categories = $this->category_model->get_paginated($per_page, $offset, $search);
        
        $data = [
            'page' => 'Kelola Kategori',
            'categories' => $categories,
            'search' => $search ?? '',
            'current_page' => $page,
            'total_rows' => $total_rows,
            'total_pages' => $total_pages,
            'per_page' => $per_page,
            'start_row' => $total_rows > 0 ? $offset + 1 : 0,
            'end_row' => min($offset + $per_page, $total_rows)
        ];
        
        $this->template->loadmodern('category/index-modern', $data);
    }

    /**
     * Show create category form
     */
    public function create()
    {
        $data = [
            'page' => 'Tambah Kategori'
        ];
        
        $this->template->loadmodern('category/form-modern', $data);
    }

    /**
     * Store new category
     */
    public function store()
    {
        $this->form_validation->set_rules('category_name', 'Nama Kategori', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('description', 'Deskripsi', 'trim|max_length[500]');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('category/create');
        }

        $data = [
            'category_name' => trim($this->input->post('category_name', true)),
            'description' => trim($this->input->post('description', true))
        ];

        $result = $this->category_model->create($data);

        if ($result['success']) {
            $this->session->set_flashdata('success', $result['message']);
            redirect('category');
        } else {
            $this->session->set_flashdata('error', $result['message']);
            redirect('category/create');
        }
    }

    /**
     * Show edit category form
     */
    public function edit($id_category)
    {
        $category = $this->category_model->get_by_id($id_category);
        
        if (!$category) {
            $this->session->set_flashdata('error', 'Kategori tidak ditemukan.');
            redirect('category');
        }
        
        $data = [
            'page' => 'Edit Kategori',
            'category' => $category
        ];
        
        $this->template->loadmodern('category/form-modern', $data);
    }

    /**
     * Update category
     */
    public function update($id_category)
    {
        $this->form_validation->set_rules('category_name', 'Nama Kategori', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('description', 'Deskripsi', 'trim|max_length[500]');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('category/edit/' . $id_category);
        }

        $data = [
            'category_name' => trim($this->input->post('category_name', true)),
            'description' => trim($this->input->post('description', true))
        ];

        $result = $this->category_model->update($id_category, $data);

        if ($result['success']) {
            $this->session->set_flashdata('success', $result['message']);
            redirect('category');
        } else {
            $this->session->set_flashdata('error', $result['message']);
            redirect('category/edit/' . $id_category);
        }
    }

    /**
     * Delete category (POST method)
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            show_error('Method not allowed', 405);
            return;
        }

        $id_category = $this->input->post('id_category');
        
        if (!$id_category) {
            $this->session->set_flashdata('error', 'ID kategori tidak valid.');
            redirect('category');
            return;
        }

        $result = $this->category_model->delete($id_category);

        if ($result['success']) {
            $this->session->set_flashdata('success', $result['message']);
        } else {
            $this->session->set_flashdata('error', $result['message']);
        }
        
        redirect('category');
    }
}
