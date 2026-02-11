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

    public function import()
    {
        $data['page'] = 'Import Stok';
        $this->template->load('layout/template', 'stock/import_form', $data);
    }

    public function import_preview()
    {
        $this->load->library('upload', $this->upload_config());

        if (! $this->upload->do_upload('import_file')) {
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

        // Build lookup maps for validation
        $category_map = $this->build_category_map();
        $item_map = $this->build_item_map();

        $preview_rows = [];
        $errors = [];
        $row_number = 0;

        foreach ($rows as $index => $row) {
            if ($index === 1) {
                continue; // Skip header row
            }

            $row_number = $index;
            $item_id = trim((string) ($row['A'] ?? ''));
            $category_name = trim((string) ($row['B'] ?? ''));
            $item_name = trim((string) ($row['C'] ?? ''));
            $qty = trim((string) ($row['D'] ?? ''));
            $note = trim((string) ($row['E'] ?? ''));

            // Skip completely empty rows
            if ($item_id === '' && $category_name === '' && $item_name === '' && $qty === '') {
                continue;
            }

            $row_errors = [];

            // Validate quantity
            if ($qty === '') {
                $row_errors[] = 'Qty wajib diisi.';
            } elseif (!is_numeric($qty) || (int)$qty <= 0) {
                $row_errors[] = 'Qty harus angka positif.';
            }

            // Match item: prefer ID, fallback to category+name
            $matched_item_id = null;

            if ($item_id !== '') {
                // Try to match by ID
                if (isset($item_map['by_id'][$item_id])) {
                    $matched_item_id = $item_map['by_id'][$item_id]['id_item'];
                } else {
                    $row_errors[] = 'Item ID tidak ditemukan.';
                }
            } else {
                // Fallback: match by category+name
                if ($category_name === '') {
                    $row_errors[] = 'Kategori wajib diisi jika Item ID kosong.';
                } elseif ($item_name === '') {
                    $row_errors[] = 'Nama Item wajib diisi jika Item ID kosong.';
                } else {
                    // Find category
                    $category_id = null;
                    foreach ($category_map as $cat) {
                        if (strcasecmp($cat['category_name'], $category_name) === 0) {
                            $category_id = $cat['id_category'];
                            break;
                        }
                    }

                    if ($category_id === null) {
                        $row_errors[] = 'Kategori tidak ditemukan.';
                    } else {
                        // Find item by category+name
                        $lookup_key = $category_id . '|' . strtolower($item_name);
                        if (isset($item_map['by_category_name'][$lookup_key])) {
                            $matched_item_id = $item_map['by_category_name'][$lookup_key]['id_item'];
                        } else {
                            $row_errors[] = 'Item tidak ditemukan dalam kategori tersebut.';
                        }
                    }
                }
            }

            $preview_rows[] = [
                'row' => $row_number,
                'item_id' => $item_id,
                'category' => $category_name,
                'item_name' => $item_name,
                'qty' => $qty,
                'note' => $note,
                'matched_item_id' => $matched_item_id,
                'errors' => $row_errors,
            ];
        }

        // Collect valid rows for session storage
        $valid_rows = [];
        foreach ($preview_rows as $preview_row) {
            if (!empty($preview_row['errors'])) {
                $errors[] = 'Baris ' . $preview_row['row'] . ': ' . implode(' ', $preview_row['errors']);
                continue;
            }

            $valid_rows[] = [
                'id_item' => $preview_row['matched_item_id'],
                'qty_delta' => (int)$preview_row['qty'],
                'reason' => !empty($preview_row['note']) ? $preview_row['note'] : 'Import restock',
            ];
        }

        if (empty($preview_rows)) {
            $this->session->set_flashdata('error', 'Tidak ada data yang bisa diproses.');
            redirect('stock_import/import');
        }

        $this->session->set_userdata(self::IMPORT_SESSION_KEY, $valid_rows);

        $data = [
            'page' => 'Preview Import Stok',
            'rows' => $preview_rows,
            'errors' => $errors,
            'valid_count' => count($valid_rows),
        ];

        $this->template->load('layout/template', 'stock/import_preview', $data);
    }

    public function import_commit()
    {
        $rows = $this->session->userdata(self::IMPORT_SESSION_KEY);

        if (empty($rows)) {
            $this->session->set_flashdata('error', 'Tidak ada data import yang tersimpan.');
            redirect('stock_import/import');
        }

        $user_id = $this->session->userdata('id_user');
        $result = $this->stock_model->restock_batch($rows, $user_id);

        if (!$result['success']) {
            $this->session->set_flashdata('error', 'Gagal import: ' . $result['message']);
            redirect('stock_import/import');
        }

        $this->session->unset_userdata(self::IMPORT_SESSION_KEY);
        $this->session->set_flashdata('success', 'Import berhasil. Total: ' . $result['updated'] . ' item.');
        redirect('stock_import/import');
    }

    public function download_template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Restock');

        $sheet->setCellValue('A1', 'Item ID');
        $sheet->setCellValue('B1', 'Category');
        $sheet->setCellValue('C1', 'Item Name');
        $sheet->setCellValue('D1', 'Qty');
        $sheet->setCellValue('E1', 'Note');

        $writer = new XlsxWriter($spreadsheet);

        $filename = 'template_import_restock.xlsx';

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
            'max_size' => 2048,
            'encrypt_name' => true,
        ];
    }

    private function build_category_map(): array
    {
        return $this->category_model->get_all();
    }

    private function build_item_map(): array
    {
        $items = $this->stock_model->get_all();
        $map = [
            'by_id' => [],
            'by_category_name' => [],
        ];

        foreach ($items as $item) {
            // Map by ID
            $map['by_id'][(string)$item['id_item']] = $item;

            // Map by category_id + item_name (case-insensitive)
            $key = $item['category_id'] . '|' . strtolower($item['item_name']);
            $map['by_category_name'][$key] = $item;
        }

        return $map;
    }
}
