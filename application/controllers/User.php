<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

class User extends CI_Controller
{
    private const IMPORT_SESSION_KEY = 'pegawai_import_rows';

    public function __construct()
    {
        parent::__construct();
        check_not_login();
        check_admin();
        $this->load->model('user_model');
        $this->load->library('session');

        $autoload = FCPATH . 'vendor/autoload.php';
        if (file_exists($autoload)) {
            require_once $autoload;
        }
    }

    public function import()
    {
        $data['page'] = 'Import Pegawai';
        $this->template->load('layout/template', 'user/import_form', $data);
    }

    public function import_preview()
    {
        $this->load->library('upload', $this->upload_config());

        if (! $this->upload->do_upload('import_file')) {
            $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
            redirect('user/import');
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
            redirect('user/import');
        }

        @unlink($file_data['full_path']);

        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(null, true, true, true);

        $preview_rows = [];
        $errors = [];
        $nips = [];
        $row_number = 0;

        foreach ($rows as $index => $row) {
            if ($index === 1) {
                continue;
            }

            $row_number = $index;
            $nip = trim((string) ($row['A'] ?? ''));
            $nama = trim((string) ($row['B'] ?? ''));
            $unit = trim((string) ($row['C'] ?? ''));

            if ($nip === '' && $nama === '' && $unit === '') {
                continue;
            }

            $row_errors = [];

            if ($nip === '') {
                $row_errors[] = 'NIP wajib diisi.';
            }
            if ($nama === '') {
                $row_errors[] = 'Nama wajib diisi.';
            }
            if ($unit === '') {
                $row_errors[] = 'Unit wajib diisi.';
            }

            if ($nip !== '') {
                if (in_array($nip, $nips, true)) {
                    $row_errors[] = 'NIP duplikat di file.';
                } else {
                    $nips[] = $nip;
                }
            }

            $preview_rows[] = [
                'row' => $row_number,
                'nip' => $nip,
                'nama' => $nama,
                'unit' => $unit,
                'errors' => $row_errors,
            ];
        }

        if (! empty($nips)) {
            $existing = $this->user_model->get_existing_nips($nips);
            $existing_map = array_flip($existing);
            foreach ($preview_rows as &$preview_row) {
                if ($preview_row['nip'] !== '' && isset($existing_map[$preview_row['nip']])) {
                    $preview_row['errors'][] = 'NIP sudah terdaftar.';
                }
            }
            unset($preview_row);
        }

        $valid_rows = [];
        foreach ($preview_rows as $preview_row) {
            if (! empty($preview_row['errors'])) {
                $errors[] = 'Baris ' . $preview_row['row'] . ': ' . implode(' ', $preview_row['errors']);
                continue;
            }

            $valid_rows[] = [
                'nip' => $preview_row['nip'],
                'nama' => $preview_row['nama'],
                'unit' => $preview_row['unit'],
            ];
        }

        if (empty($preview_rows)) {
            $this->session->set_flashdata('error', 'Tidak ada data yang bisa diproses.');
            redirect('user/import');
        }

        $this->session->set_userdata(self::IMPORT_SESSION_KEY, $valid_rows);

        $data = [
            'page' => 'Preview Import Pegawai',
            'rows' => $preview_rows,
            'errors' => $errors,
            'valid_count' => count($valid_rows),
        ];

        $this->template->load('layout/template', 'user/import_preview', $data);
    }

    public function import_commit()
    {
        $rows = $this->session->userdata(self::IMPORT_SESSION_KEY);

        if (empty($rows)) {
            $this->session->set_flashdata('error', 'Tidak ada data import yang tersimpan.');
            redirect('user/import');
        }

        $result = $this->user_model->insert_pegawai_batch($rows);

        if (! $result['success']) {
            $this->session->set_flashdata('error', 'Gagal import: ' . $result['message']);
            redirect('user/import');
        }

        $this->session->unset_userdata(self::IMPORT_SESSION_KEY);
        $this->session->set_flashdata('success', 'Import berhasil. Total: ' . $result['inserted'] . ' pegawai.');
        redirect('user/import');
    }

    public function download_template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Pegawai');

        $sheet->setCellValue('A1', 'NIP');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Unit');

        $writer = new XlsxWriter($spreadsheet);

        $filename = 'template_import_pegawai.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    private function upload_config(): array
    {
        $upload_path = FCPATH . 'uploads/';
        if (! is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        return [
            'upload_path' => $upload_path,
            'allowed_types' => 'xlsx|xls',
            'max_size' => 2048,
            'encrypt_name' => true,
        ];
    }
}
