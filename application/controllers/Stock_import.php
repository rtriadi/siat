<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

class Stock_import extends CI_Controller
{
    private const IMPORT_SESSION_KEY = 'stock_import_rows';

    public function __construct()
    {
        parent::__construct();
        check_not_login();
        check_admin();
        $this->load->model('stock_model');
        $this->load->model('category_model');
        $this->load->library('session');

        $autoload = FCPATH . 'vendor/autoload.php';
        if (file_exists($autoload)) {
            require_once $autoload;
        }
    }

    public function index()
    {
        redirect('stock_import/import');
    }

    public function import()
    {
        $data['page'] = 'Import Stok';
        $data['categories'] = $this->category_model->get_all();
        $this->template->loadmodern('stock/import_form-modern', $data);
    }

    public function import_preview()
    {
        $purchase_date = $this->input->post('purchase_date');
        if (empty($purchase_date)) {
            $this->session->set_flashdata('error', 'Tanggal Pembelian wajib diisi.');
            redirect('stock_import/import');
        }
        $this->session->set_userdata('import_purchase_date', $purchase_date);

        $this->load->library('upload', $this->upload_config());

        if (!$this->upload->do_upload('import_file')) {
            $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
            redirect('stock_import/import');
        }

        $file_data = $this->upload->data();

        try {
            $extension = strtolower(ltrim($file_data['file_ext'], '.'));
            if ($extension === 'xls') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } else {
                $reader = new Xlsx();
            }

            $spreadsheet = $reader->load($file_data['full_path']);
        } catch (Exception $e) {
            @unlink($file_data['full_path']);
            $this->session->set_flashdata('error', 'Gagal membaca file Excel: ' . $e->getMessage());
            redirect('stock_import/import');
        }

        @unlink($file_data['full_path']);

        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, true);

        if (empty($rows)) {
            $this->session->set_flashdata('error', 'File Excel kosong.');
            redirect('stock_import/import');
        }

        return $this->process_import($rows);
    }

    private function process_import($rows)
    {
        $categories = $this->category_model->get_all();
        $category_map = [];
        foreach ($categories as $cat) {
            $category_map[strtolower($cat['category_name'])] = $cat['id_category'];
        }

        $items = $this->stock_model->get_all();
        $item_map = [];
        foreach ($items as $item) {
            $key = $item['category_id'] . '|' . strtolower($item['item_name']);
            $item_map[$key] = $item;
        }

        $preview_rows = [];
        $errors = [];

        foreach ($rows as $index => $row) {
            if ($index === 1) {
                continue;
            }

            $category_name = trim((string) ($row['A'] ?? ''));
            $item_name = trim((string) ($row['B'] ?? ''));
            $qty = trim((string) ($row['C'] ?? ''));
            $satuan = trim((string) ($row['D'] ?? ''));
            $min_stock = trim((string) ($row['E'] ?? ''));

            $row_str = implode(' ', array_map('strval', $row));
            if (trim($row_str) === '') {
                continue;
            }

            $row_errors = [];

            if ($category_name === '') {
                $row_errors[] = 'Kategori wajib diisi.';
            } else {
                $category_id = $category_map[strtolower($category_name)] ?? null;
                if ($category_id === null) {
                    $row_errors[] = 'Kategori tidak ditemukan.';
                }
            }

            if ($item_name === '') {
                $row_errors[] = 'Nama barang wajib diisi.';
            }

            if ($qty === '') {
                $row_errors[] = 'Jumlah wajib diisi.';
            } elseif (!is_numeric($qty) || (int)$qty < 0) {
                $row_errors[] = 'Jumlah harus angka positif.';
            }

            $category_id = $category_map[strtolower($category_name)] ?? null;
            $item_key = $category_id . '|' . strtolower($item_name);
            $existing_item = $item_map[$item_key] ?? null;
            $action = $existing_item ? 'update' : 'create';

            $preview_rows[] = [
                'row' => $index,
                'category_name' => $category_name,
                'item_name' => $item_name,
                'qty' => (int) $qty,
                'satuan' => !empty($satuan) ? $satuan : 'Pcs',
                'min_stock' => !empty($min_stock) ? (int) $min_stock : 10,
                'category_id' => $category_id,
                'existing_item' => $existing_item,
                'action' => $action,
                'errors' => $row_errors
            ];
        }

        if (empty($preview_rows)) {
            $this->session->set_flashdata('error', 'Tidak ada data yang bisa diproses.');
            redirect('stock_import/import');
        }

        $valid_count = 0;
        $create_count = 0;
        $update_count = 0;
        foreach ($preview_rows as $r) {
            if (empty($r['errors'])) {
                $valid_count++;
                if ($r['action'] === 'create') {
                    $create_count++;
                } else {
                    $update_count++;
                }
            }
        }

        $this->session->set_userdata(self::IMPORT_SESSION_KEY, $preview_rows);

        $data = [
            'page' => 'Preview Import',
            'rows' => $preview_rows,
            'errors' => $errors,
            'valid_count' => $valid_count,
            'create_count' => $create_count,
            'update_count' => $update_count
        ];

        $this->template->loadmodern('stock/import_preview-modern', $data);
    }

    public function import_commit()
    {
        $rows = $this->session->userdata(self::IMPORT_SESSION_KEY);
        $purchase_date = $this->session->userdata('import_purchase_date');

        if (empty($rows)) {
            $this->session->set_flashdata('error', 'Tidak ada data import yang tersimpan.');
            redirect('stock_import/import');
        }

        $success_count = 0;
        $error_count = 0;
        $created_count = 0;
        $updated_count = 0;
        $error_messages = [];

        foreach ($rows as $row) {
            if (!empty($row['errors'])) {
                $error_count++;
                continue;
            }

            if ($row['action'] === 'create') {
                $result = $this->stock_model->create_item([
                    'category_id' => $row['category_id'],
                    'item_name' => $row['item_name'],
                    'available_qty' => 0,
                    'low_stock_threshold' => $row['min_stock']
                ]);

                if ($result['success']) {
                    $user_id = $this->session->userdata('id_user');
                    $this->stock_model->adjust_stock($result['id'], 'in', $row['qty'], 'Import stok awal', $user_id, $purchase_date);

                    $success_count++;
                    $created_count++;
                } else {
                    $error_count++;
                    $error_messages[] = $row['item_name'] . ': ' . $result['message'];
                }
            } else {
                $item = $row['existing_item'];
                $user_id = $this->session->userdata('id_user');
                
                $result = $this->stock_model->adjust_stock($item['id_item'], 'in', $row['qty'], 'Import stok', $user_id, $purchase_date);

                if ($result['success']) {
                    $success_count++;
                    $updated_count++;
                } else {
                    $error_count++;
                    $error_messages[] = $row['item_name'] . ': ' . $result['message'];
                }
            }
        }

        $this->session->unset_userdata(self::IMPORT_SESSION_KEY);
        $this->session->unset_userdata('import_purchase_date');
        
        $msg = "Import selesai! ";
        if ($created_count > 0) {
            $msg .= "$created_count barang baru dibuat. ";
        }
        if ($updated_count > 0) {
            $msg .= "$updated_count stok diperbarui. ";
        }
        $this->session->set_flashdata('success', $msg);
        
        if ($error_count > 0) {
            $this->session->set_flashdata('error', "$error_count gagal. " . implode(', ', array_slice($error_messages, 0, 3)));
        }
        
        redirect('stock_import/import');
    }

    public function download_template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');

        $categories = $this->category_model->get_all();
        $cat_list = [];
        foreach ($categories as $c) {
            $cat_list[] = $c['category_name'];
        }
        $cat_reference = implode(', ', $cat_list);

        $sheet->setCellValue('A1', 'Kategori');
        $sheet->setCellValue('B1', 'Nama Barang');
        $sheet->setCellValue('C1', 'Jumlah');
        $sheet->setCellValue('D1', 'Satuan');
        $sheet->setCellValue('E1', 'Stok Minimum');

        $sheet->setCellValue('A2', $cat_reference);
        $sheet->setCellValue('B2', 'Kertas A4');
        $sheet->setCellValue('C2', '100');
        $sheet->setCellValue('D2', 'Pcs');
        $sheet->setCellValue('E2', '10');

        $writer = new XlsxWriter($spreadsheet);

        $filename = 'template_import_stok.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    private function upload_config(): array
    {
        $upload_path = FCPATH . 'uploads/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        return [
            'upload_path' => $upload_path,
            'allowed_types' => 'xlsx|xls',
            'max_size' => 5120,
            'encrypt_name' => true,
        ];
    }

    public function import_items()
    {
        redirect('stock_import/import');
    }

    public function import_items_preview()
    {
        redirect('stock_import/import');
    }

    public function import_items_commit()
    {
        redirect('stock_import/import');
    }

    public function download_items_template()
    {
        redirect('stock_import/download_template');
    }
}
