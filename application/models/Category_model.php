<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Category_model extends CI_Model
{
    /**
     * Get all categories with optional ordering
     * @param string $order_by Column to order by (default: category_name)
     * @return array
     */
    public function get_all($order_by = 'category_name')
    {
        return $this->db
            ->order_by($order_by, 'ASC')
            ->get('stock_category')
            ->result_array();
    }

    /**
     * Get categories with pagination and search
     * @param int $limit
     * @param int $offset
     * @param string $search
     * @return array
     */
    public function get_paginated($limit, $offset = 0, $search = '')
    {
        $this->db->from('stock_category');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('category_name', $search);
            $this->db->or_like('description', $search);
            $this->db->group_end();
        }
        
        $this->db->order_by('category_name', 'ASC');
        $this->db->limit($limit, $offset);
        
        return $this->db->get()->result_array();
    }

    /**
     * Count total categories with optional search
     * @param string $search
     * @return int
     */
    public function count_all($search = '')
    {
        $this->db->from('stock_category');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('category_name', $search);
            $this->db->or_like('description', $search);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }

    /**
     * Get category by ID
     * @param int $id_category
     * @return array|null
     */
    public function get_by_id($id_category)
    {
        return $this->db
            ->get_where('stock_category', array('id_category' => $id_category))
            ->row_array();
    }

    /**
     * Create new category
     * @param array $data (category_name, description)
     * @return array ['success' => bool, 'id' => int|null, 'message' => string]
     */
    public function create($data)
    {
        // Validate required field
        if (empty($data['category_name'])) {
            return [
                'success' => false,
                'id' => null,
                'message' => 'Nama kategori wajib diisi.'
            ];
        }

        $payload = [
            'category_name' => $data['category_name'],
            'description' => isset($data['description']) ? $data['description'] : null
        ];

        if ($this->db->insert('stock_category', $payload)) {
            return [
                'success' => true,
                'id' => $this->db->insert_id(),
                'message' => 'Kategori berhasil dibuat.'
            ];
        }

        $error = $this->db->error();
        return [
            'success' => false,
            'id' => null,
            'message' => $error['message'] ?? 'Gagal membuat kategori.'
        ];
    }

    /**
     * Update existing category
     * @param int $id_category
     * @param array $data (category_name, description)
     * @return array ['success' => bool, 'message' => string]
     */
    public function update($id_category, $data)
    {
        // Check if category exists
        $existing = $this->get_by_id($id_category);
        if (!$existing) {
            return [
                'success' => false,
                'message' => 'Kategori tidak ditemukan.'
            ];
        }

        $payload = [];
        if (isset($data['category_name'])) {
            $payload['category_name'] = $data['category_name'];
        }
        if (isset($data['description'])) {
            $payload['description'] = $data['description'];
        }

        if (empty($payload)) {
            return [
                'success' => false,
                'message' => 'Tidak ada data yang diubah.'
            ];
        }

        if ($this->db->update('stock_category', $payload, array('id_category' => $id_category))) {
            return [
                'success' => true,
                'message' => 'Kategori berhasil diubah.'
            ];
        }

        $error = $this->db->error();
        return [
            'success' => false,
            'message' => $error['message'] ?? 'Gagal mengubah kategori.'
        ];
    }

    /**
     * Delete category (only if no items reference it)
     * @param int $id_category
     * @return array ['success' => bool, 'message' => string]
     */
    public function delete($id_category)
    {
        // Check if category exists
        $existing = $this->get_by_id($id_category);
        if (!$existing) {
            return [
                'success' => false,
                'message' => 'Kategori tidak ditemukan.'
            ];
        }

        // Check if any items reference this category
        $item_count = $this->db
            ->where('category_id', $id_category)
            ->count_all_results('stock_item');

        if ($item_count > 0) {
            return [
                'success' => false,
                'message' => "Tidak dapat menghapus kategori. Masih ada {$item_count} item yang menggunakan kategori ini."
            ];
        }

        if ($this->db->delete('stock_category', array('id_category' => $id_category))) {
            return [
                'success' => true,
                'message' => 'Kategori berhasil dihapus.'
            ];
        }

        $error = $this->db->error();
        return [
            'success' => false,
            'message' => $error['message'] ?? 'Gagal menghapus kategori.'
        ];
    }
}
