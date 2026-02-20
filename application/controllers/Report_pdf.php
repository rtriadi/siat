<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Report_pdf extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        check_not_login();
        check_admin();
        $this->load->model('Stock_model');
        $this->load->model('Signatory_model');

        $autoload = FCPATH . 'vendor/autoload.php';
        if (file_exists($autoload)) {
            require_once $autoload;
        }
    }

    public function index()
    {
        $data['page'] = 'Cetak Laporan';
        
        $report_type = $this->input->get('report_type');
        $period_type = $this->input->get('period_type');
        $date_start = $this->input->get('date_start');
        $date_end = $this->input->get('date_end');
        $month = $this->input->get('month');
        $year = $this->input->get('year');

        $data['report_type'] = $report_type;
        $data['period_type'] = $period_type ?: 'monthly';
        $data['date_start'] = $date_start;
        $data['date_end'] = $date_end;
        $data['month'] = $month ?: date('m');
        $data['year'] = $year ?: date('Y');

        $data['preview_data'] = null;
        $data['signatories'] = $this->Signatory_model->get_all_roles();

        if ($report_type) {
            $filters = $this->build_filters($data);
            $data['preview_data'] = $this->get_report_data($report_type, $filters);
        }

        $this->template->loadmodern('reports/report_pdf-modern', $data);
    }

    private function build_filters($data)
    {
        $filters = [];
        if ($data['period_type'] == 'range') {
            $filters['date_start'] = $data['date_start'];
            $filters['date_end'] = $data['date_end'];
        } elseif ($data['period_type'] == 'monthly') {
            $filters['month'] = $data['month'];
            $filters['year'] = $data['year'];
        } elseif ($data['period_type'] == 'yearly') {
            $filters['year'] = $data['year'];
        }
        return $filters;
    }

    private function get_report_data($report_type, $filters)
    {
        switch ($report_type) {
            case 'laporan_biaya':
                return $this->Stock_model->get_laporan_biaya($filters);
            case 'rkbp':
                return $this->Stock_model->get_rkbp($filters);
            case 'buku_bantu_penerimaan':
                return $this->Stock_model->get_buku_bantu($filters, 'in');
            case 'buku_bantu_pengeluaran':
                return $this->Stock_model->get_buku_bantu($filters, 'out');
            case 'keadaan_barang':
                return $this->Stock_model->get_keadaan_barang($filters);
            default:
                return [];
        }
    }

    public function download()
    {
        $report_type = $this->input->get('report_type');
        $data['report_type'] = $report_type;
        $data['period_type'] = $this->input->get('period_type') ?: 'monthly';
        $data['date_start'] = $this->input->get('date_start');
        $data['date_end'] = $this->input->get('date_end');
        $data['month'] = $this->input->get('month') ?: date('m');
        $data['year'] = $this->input->get('year') ?: date('Y');
        $data['signatories'] = $this->Signatory_model->get_all_roles();

        $filters = $this->build_filters($data);
        $data['report_data'] = $this->get_report_data($report_type, $filters);

        // Determine paper orientation for print CSS
        $is_landscape = in_array($report_type, ['buku_bantu_penerimaan', 'buku_bantu_pengeluaran', 'keadaan_barang']);
        $data['print_orientation'] = $is_landscape ? 'landscape' : 'portrait';

        // Render report HTML content
        $report_html = $this->load->view('reports/pdf/' . $report_type, $data, true);

        $data['report_html'] = $report_html;

        // Output a standalone print-ready HTML page
        $this->load->view('reports/print_wrapper', $data);
    }
}
