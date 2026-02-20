<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

class Reports extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        check_not_login();
        check_admin();
        $this->load->model('Request_model');
        $this->load->library('session');

        $autoload = FCPATH . 'vendor/autoload.php';
        if (file_exists($autoload)) {
            require_once $autoload;
        }
    }

    public function request_history()
    {
        $filters = [
            'date_start' => $this->input->get('date_start'),
            'date_end' => $this->input->get('date_end'),
            'user_id' => $this->input->get('user_id'),
            'status' => $this->input->get('status')
        ];

        $filters = array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });

        $rows = $this->Request_model->get_request_history_report($filters);

        $pegawai_list = $this->db
            ->select('id_user, nama, nip')
            ->from('user')
            ->where('level', 2)
            ->where('is_active', 1)
            ->order_by('nama', 'ASC')
            ->get()
            ->result_array();

        $data = [
            'page' => 'Request History',
            'rows' => $rows,
            'pegawai_list' => $pegawai_list,
            'filters' => $filters
        ];

        $this->template->loadmodern('reports/request_history-modern', $data);
    }

    public function export_request_history()
    {
        $filters = [
            'date_start' => $this->input->get('date_start'),
            'date_end' => $this->input->get('date_end'),
            'user_id' => $this->input->get('user_id'),
            'status' => $this->input->get('status')
        ];

        $filters = array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });

        $rows = $this->Request_model->get_request_history_report($filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Request History');

        $headers = [
            'No. Request',
            'Tanggal',
            'Pegawai',
            'NIP',
            'Jabatan',
            'Status',
            'Item',
            'Qty Diminta',
            'Qty Disetujui',
            'Qty Dikirim',
            'Catatan'
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValueByColumnAndRow($col + 1, 1, $header);
        }

        $row_num = 2;
        foreach ($rows as $row) {
            $sheet->setCellValueByColumnAndRow(1, $row_num, $row['request_no']);
            $sheet->setCellValueByColumnAndRow(2, $row_num, $row['created_at']);
            $sheet->setCellValueByColumnAndRow(3, $row_num, $row['nama'] ?? '-');
            $sheet->setCellValueByColumnAndRow(4, $row_num, $row['nip'] ?? '-');
            $sheet->setCellValueByColumnAndRow(5, $row_num, $row['jabatan'] ?? '-');
            $sheet->setCellValueByColumnAndRow(6, $row_num, $this->translate_status($row['status']));
            $sheet->setCellValueByColumnAndRow(7, $row_num, $row['item_name'] ?? '-');
            $sheet->setCellValueByColumnAndRow(8, $row_num, $row['qty_requested']);
            $sheet->setCellValueByColumnAndRow(9, $row_num, $row['qty_approved']);
            $sheet->setCellValueByColumnAndRow(10, $row_num, $row['qty_delivered']);
            $sheet->setCellValueByColumnAndRow(11, $row_num, $row['item_note'] ?? '');
            $row_num++;
        }

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'request_history_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new XlsxWriter($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function stock_movement()
    {
        $this->load->model('Stock_model');
        $this->load->model('Category_model');

        $filters = [
            'date_start' => $this->input->get('date_start'),
            'date_end' => $this->input->get('date_end'),
            'item_id' => $this->input->get('item_id'),
            'category_id' => $this->input->get('category_id')
        ];

        $filters = array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });

        $rows = $this->Stock_model->get_stock_movement_report($filters);

        // Get filter data for dropdowns
        $items = $this->Stock_model->get_all();
        $categories = $this->Category_model->get_all();

        $data = [
            'page' => 'Stock Movement',
            'rows' => $rows,
            'items' => $items,
            'categories' => $categories,
            'filters' => $filters
        ];

        $this->template->loadmodern('reports/stock_movement-modern', $data);
    }

    public function audit_trail()
    {
        $this->load->model('Stock_model');
        $this->load->model('Category_model');

        $filters = [
            'date_start' => $this->input->get('date_start'),
            'date_end' => $this->input->get('date_end'),
            'item_id' => $this->input->get('item_id'),
            'category_id' => $this->input->get('category_id')
        ];

        $filters = array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });

        $rows = $this->Stock_model->get_audit_trail_report($filters);

        // Get filter data for dropdowns
        $items = $this->Stock_model->get_all();
        $categories = $this->Category_model->get_all();

        $data = [
            'page' => 'Audit Trail',
            'rows' => $rows,
            'items' => $items,
            'categories' => $categories,
            'filters' => $filters
        ];

        $this->template->loadmodern('reports/audit_trail-modern', $data);
    }

    public function export_stock_movement()
    {
        $this->load->model('Stock_model');

        $filters = [
            'date_start' => $this->input->get('date_start'),
            'date_end' => $this->input->get('date_end'),
            'item_id' => $this->input->get('item_id'),
            'category_id' => $this->input->get('category_id')
        ];

        $filters = array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });

        $rows = $this->Stock_model->get_stock_movement_report($filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Stock Movement');

        $headers = [
            'Tanggal',
            'Item',
            'Kategori',
            'Tipe',
            'Qty',
            'Reason',
            'User',
            'Running Balance'
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValueByColumnAndRow($col + 1, 1, $header);
        }

        $row_num = 2;
        foreach ($rows as $row) {
            $sheet->setCellValueByColumnAndRow(1, $row_num, $row['created_at']);
            $sheet->setCellValueByColumnAndRow(2, $row_num, $row['item_name'] ?? '-');
            $sheet->setCellValueByColumnAndRow(3, $row_num, $row['category_name'] ?? '-');
            $sheet->setCellValueByColumnAndRow(4, $row_num, $this->translate_movement_type($row['movement_type']));
            $sheet->setCellValueByColumnAndRow(5, $row_num, $row['qty_delta']);
            $sheet->setCellValueByColumnAndRow(6, $row_num, $row['reason'] ?? '');
            $sheet->setCellValueByColumnAndRow(7, $row_num, $row['user_name'] ?? '-');
            $sheet->setCellValueByColumnAndRow(8, $row_num, $row['running_balance'] ?? 0);
            $row_num++;
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'stock_movement_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new XlsxWriter($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function stock_levels()
    {
        $this->load->model('Stock_model');
        $this->load->model('Category_model');

        $filters = [
            'category_id' => $this->input->get('category_id')
        ];

        $filters = array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });

        $rows = $this->Stock_model->get_stock_levels_report($filters);
        $categories = $this->Category_model->get_all();

        $data = [
            'page' => 'Stock Levels',
            'rows' => $rows,
            'categories' => $categories,
            'filters' => $filters
        ];

        $this->template->loadmodern('reports/stock_levels-modern', $data);
    }

    public function export_stock_levels()
    {
        $this->load->model('Stock_model');

        $filters = [
            'category_id' => $this->input->get('category_id')
        ];

        $filters = array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });

        $rows = $this->Stock_model->get_stock_levels_report($filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Stock Levels');

        $headers = [
            'Kategori',
            'Item',
            'Tersedia',
            'Direservasi',
            'Digunakan',
            'Batas Stok Minimum'
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValueByColumnAndRow($col + 1, 1, $header);
        }

        $row_num = 2;
        foreach ($rows as $row) {
            $sheet->setCellValueByColumnAndRow(1, $row_num, $row['category_name'] ?? '-');
            $sheet->setCellValueByColumnAndRow(2, $row_num, $row['item_name'] ?? '-');
            $sheet->setCellValueByColumnAndRow(3, $row_num, $row['available_qty'] ?? 0);
            $sheet->setCellValueByColumnAndRow(4, $row_num, $row['reserved_qty'] ?? 0);
            $sheet->setCellValueByColumnAndRow(5, $row_num, $row['used_qty'] ?? 0);
            $sheet->setCellValueByColumnAndRow(6, $row_num, $row['low_stock_threshold'] ?? 0);
            $row_num++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'stock_levels_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new XlsxWriter($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function translate_status($status)
    {
        $map = [
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'delivered' => 'Dikirim',
            'cancelled' => 'Dibatalkan'
        ];
        return $map[$status] ?? $status;
    }

    private function translate_movement_type($type)
    {
        $map = [
            'in' => 'Masuk',
            'out' => 'Keluar',
            'adjust' => 'Penyesuaian',
            'reserve' => 'Reservasi',
            'cancel' => 'Batal Reservasi',
            'deliver' => 'Pengiriman'
        ];
        return $map[$type] ?? $type;
    }
}
