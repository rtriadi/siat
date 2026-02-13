<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        check_not_login();
        $this->load->model('stock_model');
        $this->load->model('request_model');
        $this->load->model('category_model');
    }

    public function index()
    {
        $level = (int) $this->session->userdata('level');
        
        if ($level === 1) {
            $this->admin_dashboard();
        } else {
            $this->employee_dashboard();
        }
    }

    private function admin_dashboard()
    {
        $items = $this->stock_model->get_all();
        
        $total_items = count($items);
        $low_stock_items = 0;
        $total_stock = 0;
        $category_count = count($this->category_model->get_all());
        
        foreach ($items as $item) {
            $total_stock += (int) ($item['available_qty'] ?? 0);
            if (($item['available_qty'] ?? 0) <= ($item['low_stock_threshold'] ?? 0)) {
                $low_stock_items++;
            }
        }

        $pending_requests = $this->db
            ->where('status', 'pending')
            ->count_all_results('request_header');
            
        $delivered_today = $this->db
            ->where('status', 'delivered')
            ->where('DATE(delivered_at)', date('Y-m-d'))
            ->count_all_results('request_header');

        $this->db->select('
            rh.id_request,
            rh.request_no,
            rh.status,
            rh.created_at,
            rh.updated_at,
            u.nama as user_name,
            GROUP_CONCAT(DISTINCT si.item_name ORDER BY si.item_name SEPARATOR ", ") as item_names,
            SUM(ri.qty_requested) as total_qty
        ');
        $this->db->from('request_header rh');
        $this->db->join('user u', 'u.id_user = rh.user_id', 'left');
        $this->db->join('request_item ri', 'ri.request_id = rh.id_request', 'left');
        $this->db->join('stock_item si', 'si.id_item = ri.item_id', 'left');
        $this->db->group_by('rh.id_request');
        $this->db->order_by('rh.created_at', 'DESC');
        $this->db->limit(8);
        $recent_requests = $this->db->get()->result_array();

        $activities = [];
        $status_text = [
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'delivered' => 'Diterima'
        ];
        $status_class = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'delivered' => 'primary'
        ];
        $icon = [
            'pending' => 'clock',
            'approved' => 'check',
            'rejected' => 'times',
            'delivered' => 'truck'
        ];
        
        foreach ($recent_requests as $req) {
            $time = $this->format_time_ago($req['created_at']);
            
            $activities[] = [
                'type' => $status_class[$req['status']] ?? 'primary',
                'icon' => $icon[$req['status']] ?? 'circle',
                'message' => ($req['user_name'] ?? 'User') . ' - ' . ($req['item_names'] ?? 'Item') . ' (' . ($req['total_qty'] ?? 0) . ')',
                'time' => $time,
                'status' => $status_text[$req['status']] ?? $req['status']
            ];
        }

        $data = [
            'page' => 'Dashboard',
            'level' => 'admin',
            'total_items' => $total_items,
            'total_stock' => $total_stock,
            'category_count' => $category_count,
            'pending_requests' => $pending_requests,
            'low_stock_items' => $low_stock_items,
            'delivered_today' => $delivered_today,
            'recent_activities' => $activities,
            'recent_requests' => $recent_requests,
            'user_name' => $this->session->userdata('nama')
        ];
        
        $this->template->loadmodern('dashboard-modern', $data);
    }

    private function employee_dashboard()
    {
        $user_id = $this->session->userdata('id_user');
        
        $pending_count = $this->db
            ->where('user_id', $user_id)
            ->where('status', 'pending')
            ->count_all_results('request_header');
            
        $approved_count = $this->db
            ->where('user_id', $user_id)
            ->where('status', 'approved')
            ->count_all_results('request_header');
            
        $delivered_count = $this->db
            ->where('user_id', $user_id)
            ->where('status', 'delivered')
            ->count_all_results('request_header');

        $this->db->select('
            rh.id_request,
            rh.request_no,
            rh.status,
            rh.created_at,
            rh.updated_at,
            GROUP_CONCAT(DISTINCT si.item_name ORDER BY si.item_name SEPARATOR ", ") as item_names,
            SUM(ri.qty_requested) as total_qty
        ');
        $this->db->from('request_header rh');
        $this->db->join('request_item ri', 'ri.request_id = rh.id_request', 'left');
        $this->db->join('stock_item si', 'si.id_item = ri.item_id', 'left');
        $this->db->where('rh.user_id', $user_id);
        $this->db->group_by('rh.id_request');
        $this->db->order_by('rh.created_at', 'DESC');
        $this->db->limit(10);
        $all_my_requests = $this->db->get()->result_array();

        $data = [
            'page' => 'Dashboard',
            'level' => 'employee',
            'pending_count' => $pending_count,
            'approved_count' => $approved_count,
            'delivered_count' => $delivered_count,
            'all_my_requests' => $all_my_requests,
            'user_name' => $this->session->userdata('nama')
        ];
        
        $this->template->loadmodern('dashboard-modern', $data);
    }

    private function format_time_ago($datetime)
    {
        if (empty($datetime)) return '-';
        $time = strtotime($datetime);
        $diff = time() - $time;
        
        if ($diff < 60) return 'Baru saja';
        if ($diff < 3600) return floor($diff / 60) . ' menit lalu';
        if ($diff < 86400) return floor($diff / 3600) . ' jam lalu';
        if ($diff < 604800) return floor($diff / 86400) . ' hari lalu';
        return date('d M', $time);
    }
}
