<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
	private $_table = 'user';

	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model('m_users');
	}

	public function index()
	{
		if ($this->session->userdata('email')) {
			if ($this->session->userdata('role_id') == 1) {
				redirect('Admin');
			} else if ($this->session->userdata('role_id') == 2) {
				redirect('User');
			}
		}

		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required|trim');


		if ($this->form_validation->run() == false) {
			$data['title'] = 'Login Page';
			$this->load->view('templates/auth_header', $data);
			$this->load->view('auth/login');
			$this->load->view('templates/auth_footer');
		} else {
			$this->_checkLogin();
		}
	}

	private function _checkLogin()
	{
		$post = $this->input->post();
		$email = $post['email'];
		$password = $post['password'];

		// $sql = $this->db->get_where($this->_table, ['email' => $post['email']]); -----> select nya bisa seperti ini juga

		/* $this->db->where('email = ', $email); 
		$sql = $this->db->get($this->_table)->row(); ==> bisa query langsung */

		$sql = $this->m_users->UserByEmail($email)->row(); /*  ==> bisa query nya pake model */
		if ($sql) {
			//cek jika user active
			if ($sql->is_active == 1) {
				// cek password
				$isPasswordTrue = password_verify($password, $sql->password);
				if ($isPasswordTrue) {
					$params = [
						'name'	=> $sql->name,
						'email'	=> $sql->email,
						'role_id'	=> $sql->role_id
					];
					$this->session->set_userdata($params);

					if ($sql->role_id == 1) {
						redirect('Admin');
					} else if ($sql->role_id == 2) {
						redirect('User');
					}
				} else {
					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
					Password wrong!</div>');
					redirect('Auth');
				}
			} else {
				$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
				This email has not been activated!</div>');
				redirect('Auth');
			}
		}
		$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
		Email is not regitered!</div>');
		redirect('Auth');
	}

	// private function updateLastLogin($user_id)
	// {
	// 	$sql = "UPDATE {$this->_table} SET lastlogin = now() WHERE id = {$user_id}";
	// 	$this->db->query($sql);
	// }

	public function registration()
	{
		if ($this->session->userdata('email')) {
			if ($this->session->userdata('role_id') == 1) {
				redirect('Admin');
			} else if ($this->session->userdata('role_id') == 2) {
				redirect('User');
			}
		}

		$this->form_validation->set_rules('name', 'Name', 'required|trim');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]', [
			'is_unique' => 'This email already registered!'
		]);
		$this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', [
			'matches' => 'Password not match!',
			'min_length' => 'Password too short!'
		]);
		$this->form_validation->set_rules('password2', 'Password Confirmation', 'required|trim|matches[password1]');

		if ($this->form_validation->run() == false) {
			$data['title'] = 'User Registration';
			$this->load->view('templates/auth_header', $data);
			$this->load->view('auth/registration');
			$this->load->view('templates/auth_footer');
		} else {
			// $data = [
			// 	'name' => htmlspecialchars($this->input->post('name', true)),
			// 	'email' => htmlspecialchars($this->input->post('email', true)),
			// 	'image' => 'default.jpg',
			// 	'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
			// 	'role_id' => 2,
			// 	'is_active' => 1,
			// 	'date_create' => time()

			// ];
			$this->m_users->save();
			if ($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
				Congratulation! your account has been created. Please Actived.</div>');
			}
			redirect('Auth');
		}
	}

	public function logout()
	{
		$this->session->unset_userdata('name');
		$this->session->unset_userdata('email');
		$this->session->unset_userdata('role_id');
		$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
		You have been logged out!</div>');
		redirect('Auth');
	}

	public function blocked()
	{
		$data['title'] = 'Access Forbidden';
		$this->load->view('templates/header', $data);
		$this->load->view('auth/blocked');
		$this->load->view('templates/auth_footer');
	}
}