<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // check_already_login();
        $this->load->model('user_model');
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
                        'level' => $user['level'],
                        'must_change_password' => $user['must_change_password'],
                    ));
                    $this->user_model->update_login_meta($user['id_user'], date('Y-m-d H:i:s'));
                    redirect('dashboard');
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
                        'level' => $user['level'],
                        'must_change_password' => $user['must_change_password'],
                    ));
                    $this->user_model->update_login_meta($user['id_user'], date('Y-m-d H:i:s'));
                    redirect('dashboard');
                } else {
                    // Password incorrect
                    $this->session->set_flashdata('error', 'Username atau Password salah!');
                    redirect('auth/login');
                }
            }
        }
        $this->load->view('login');
    }

    public function logout()
    {
        // $this->session->unset_userdata('id_user');
        $this->session->sess_destroy();
        redirect('auth/login'); // Ganti 'auth/login' dengan halaman login
    }
}
