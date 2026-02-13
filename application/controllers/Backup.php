<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Backup extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        check_not_login();
        check_admin();
    }

    public function index()
    {
        $data = [
            'page' => 'Backup Database',
            'backups' => $this->get_backup_files()
        ];
        
        $this->template->loadmodern('backup/index-modern', $data);
    }

    public function export()
    {
        $this->load->dbutil();
        
        $prefs = array(
            'tables'      => array(),
            'ignore'      => array(),
            'format'      => 'sql',
            'filename'    => 'siat_backup_' . date('Y-m-d_H-i-s') . '.sql',
            'add_drop'    => TRUE,
            'add_insert'  => TRUE,
            'newline'    => "\n",
            'foreign_key_checks' => TRUE,
            'completions' => TRUE
        );
        
        $backup = $this->dbutil->backup($prefs);
        
        $this->load->helper('file');
        $backup_path = FCPATH . 'backups/';
        
        if (!is_dir($backup_path)) {
            mkdir($backup_path, 0755, true);
        }
        
        $filename = 'siat_backup_' . date('Y-m-d_H-i-s') . '.sql';
        write_file($backup_path . $filename, $backup);
        
        $this->load->helper('download');
        force_download($filename, $backup);
        
        $this->session->set_flashdata('success', 'Database berhasil di-export.');
        redirect('backup');
    }

    public function download($filename)
    {
        check_not_login();
        check_admin();
        
        $filepath = FCPATH . 'backups/' . $filename;
        
        if (!file_exists($filepath)) {
            $this->session->set_flashdata('error', 'File tidak ditemukan.');
            redirect('backup');
        }
        
        $this->load->helper('download');
        force_download($filepath, NULL);
    }

    public function delete($filename)
    {
        check_not_login();
        check_admin();
        
        $filepath = FCPATH . 'backups/' . $filename;
        
        if (file_exists($filepath)) {
            unlink($filepath);
            $this->session->set_flashdata('success', 'Backup file berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'File tidak ditemukan.');
        }
        
        redirect('backup');
    }

    private function get_backup_files()
    {
        $backup_path = FCPATH . 'backups/';
        $files = [];
        
        if (is_dir($backup_path)) {
            $dir = scandir($backup_path);
            foreach ($dir as $file) {
                if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $files[] = [
                        'name' => $file,
                        'size' => filesize($backup_path . $file),
                        'date' => filemtime($backup_path . $file)
                    ];
                }
            }
        }
        
        usort($files, function($a, $b) {
            return $b['date'] - $a['date'];
        });
        
        return $files;
    }
}
