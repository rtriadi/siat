<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // check_already_login();
        $this->load->model('user_model');
        $this->load->library('session');
        $this->load->database();
    }

    public function login()
    {
        $this->load->library('form_validation');

        if ($this->input->post()) {
            $this->form_validation->set_rules('username', 'Username', 'required|trim');
            $this->form_validation->set_rules('password', 'Password', 'required');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('auth/login');
            }

            $username = $this->input->post('username', true);
            $password = $this->input->post('password');

            $user = $this->user_model->get_by_username($username);

            if (!$user) {
                $this->session->set_flashdata('error', 'Username atau Password salah!');
                redirect('auth/login');
            }

            if ((int) $user['is_active'] === 0) {
                $this->session->set_flashdata('error', 'Akun tidak aktif.');
                redirect('auth/login');
            }

            // Check if old SHA1 hash (40 characters) or new bcrypt hash
            if (strlen($user['password']) === 40) {
                // Old SHA1 system - verify and migrate
                if (sha1($password) === $user['password']) {
                    // Password correct - upgrade to bcrypt
                    $new_hash = password_hash($password, PASSWORD_DEFAULT);
                    $this->user_model->update_password($user['id_user'], $new_hash);
                    $this->session->set_userdata(array(
                        'id_user' => $user['id_user'],
                        'username' => $user['username'],
                        'nama' => $user['nama'],
                        'level' => $user['level'],
                        'login_year' => $this->input->post('login_year') ?: date('Y'),
                        'must_change_password' => $user['must_change_password'],
                    ));
                    $this->user_model->update_login_meta($user['id_user'], date('Y-m-d H:i:s'));
                    if ((int) $user['level'] === 2 && (int) $user['must_change_password'] === 1) {
                        $this->session->set_flashdata('warning', 'Password Anda masih default. Silakan ubah terlebih dahulu.');
                        redirect('auth/change_password');
                    }
                    $this->redirect_by_level($user['level']);
                } else {
                    // Password incorrect
                    $this->session->set_flashdata('error', 'Username atau Password salah!');
                    redirect('auth/login');
                }
            } else {
                // Already using bcrypt
                if (password_verify($password, $user['password'])) {
                    // Check if hash needs rehashing (cost/algorithm changed)
                    if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                        $new_hash = password_hash($password, PASSWORD_DEFAULT);
                        $this->user_model->update_password($user['id_user'], $new_hash);
                    }
                    $this->session->set_userdata(array(
                        'id_user' => $user['id_user'],
                        'username' => $user['username'],
                        'nama' => $user['nama'],
                        'level' => $user['level'],
                        'login_year' => $this->input->post('login_year') ?: date('Y'),
                        'must_change_password' => $user['must_change_password'],
                    ));
                    $this->user_model->update_login_meta($user['id_user'], date('Y-m-d H:i:s'));
                    if ((int) $user['level'] === 2 && (int) $user['must_change_password'] === 1) {
                        $this->session->set_flashdata('warning', 'Password Anda masih default. Silakan ubah melalui menu ubah password.');
                    }
                    $this->redirect_by_level($user['level']);
                } else {
                    // Password incorrect
                    $this->session->set_flashdata('error', 'Username atau Password salah!');
                    redirect('auth/login');
                }
            }
        }
        $this->load->view('login-modern');
    }

    public function change_password()
    {
        check_not_login();
        $this->load->library('form_validation');

        if ($this->input->post()) {
            $this->form_validation->set_rules('current_password', 'Password Saat Ini', 'required');
            $this->form_validation->set_rules('new_password', 'Password Baru', 'required|min_length[8]');
            $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required|matches[new_password]');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('auth/change_password');
            }

            $id_user = $this->session->userdata('id_user');
            $current_password = $this->input->post('current_password');
            $new_password = $this->input->post('new_password');

            $user = $this->user_model->get_by_id($id_user);
            if (!$user) {
                $this->session->set_flashdata('error', 'Akun tidak ditemukan.');
                redirect('auth/login');
            }

            if (!password_verify($current_password, $user['password'])) {
                $this->session->set_flashdata('error', 'Password saat ini tidak sesuai.');
                redirect('auth/change_password');
            }

            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $this->user_model->update_password($id_user, $new_hash);
            $this->user_model->set_must_change_password($id_user, 0);
            $this->session->set_userdata('must_change_password', 0);
            $this->session->set_flashdata('success', 'Password berhasil diperbarui.');
            redirect('dashboard');
        }

        $data['page'] = 'Ubah Password';
        $this->template->loadmodern('user/change_password-modern', $data);
    }

    public function logout()
    {
        // $this->session->unset_userdata('id_user');
        $this->session->sess_destroy();
        redirect('auth/login'); // Ganti 'auth/login' dengan halaman login
    }

    private function redirect_by_level($level)
    {
        if ((int) $level === 1) {
            redirect('dashboard');
        }

        if ((int) $level === 2) {
            redirect('pegawai');
        }

        redirect('auth/login');
    }
}
