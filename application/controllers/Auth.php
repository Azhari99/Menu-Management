<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->library('email');
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

			//token
			$token = base64_encode(random_bytes(32));

			$this->m_users->save();
			$this->m_users->saveUserToken($token);
			$this->_sendEmail($token, 'verify');

			if ($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
				Congratulation! your account has been created. Please active your account</div>');
			}
			redirect('Auth');
		}
	}

	private function _sendEmail($token, $type)
	{
		$email = $this->input->post('email');
		$config = [
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://smtp.googlemail.com',
			'smtp_user' => 'ff.developersolution@gmail.com',
			'smtp_pass' => '1234567890azhari',
			'smtp_port' => 465,
			'mailtype' => 'html',
			'charset' => 'utf-8',
			'newline' => "\r\n"
		];

		$this->email->initialize($config);

		$this->email->from('ff.developersolution@gmail.com', 'FF Developer Solution');
		$this->email->to($email);

		if ($type == 'verify') {
			$this->email->subject('Account Verification');
			$this->email->message('Click this link to verify your account : 
				<a href="' . base_url() . 'Auth/verify?email=' . $email . '&token=' . urlencode($token) . '">Activate</a>');
		} else if ($type == 'forgot') {
			$this->email->subject('Reset Password');
			$this->email->message('Click this link to reset your password : 
				<a href="' . base_url() . 'Auth/resetPassword?email=' . $email . '&token=' . urlencode($token) . '">Reset Password</a>');
		}

		if ($this->email->send()) {
			return true;
		} else {
			echo $this->email->print_debugger();
			die;
		}
	}

	public function verify()
	{
		$email = $this->input->get('email');
		$token = $this->input->get('token');

		$user = $this->m_users->UserByEmail($email)->row();

		if ($user) {
			$user_token = $this->db->get_where('user_token', ['token' => $token])->row();
			if ($user_token) {
				if (time() - $user_token->date_created < 86400) { //token akan expired jika lebih dr 24 jam

					//update is_active table user where email
					$this->db->set('is_active', 1);
					$this->db->where('email', $email);
					$this->db->update('user');

					//delete user token by email, karena sudah tidak dibutuhkan.
					$this->db->delete('user_token', ['email' => $email]);

					$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
					' . $email . ' has been activated! Please login</div>');
					redirect('Auth');
				} else {

					//delete user dan user token
					$this->db->delete('user', ['email' => $email]);
					$this->db->delete('user_token', ['email' => $email]);

					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
					Account activation failed! Token expired.</div>');
					redirect('Auth');
				}
			} else {
				$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
				Account activation failed! Token invalid.</div>');
				redirect('Auth');
			}
		} else {
			$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
			Account activation failed! Wrong email.</div>');
			redirect('Auth');
		}
	}

	public function resetPassword()
	{
		$email = $this->input->get('email');
		$token = $this->input->get('token');

		$user = $this->m_users->UserByEmail($email)->row();

		if ($user) {
			$user_token = $this->db->get_where('user_token', ['token' => $token])->row();
			if ($user_token) {
				if (time() - $user_token->date_created < 86400) { //token akan expired jika lebih dr 24 jam

					$this->session->set_userdata('reset_email', $email);
					$this->changePassword();

					//delete user token by email, karena sudah tidak dibutuhkan.
					/* $this->db->delete('user_token', ['email' => $email]);

					$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
					' . $email . ' has been change password! Please login</div>');
					redirect('Auth'); */
				} else {

					//delete user dan user token
					$this->db->delete('user', ['email' => $email]);
					$this->db->delete('user_token', ['email' => $email]);

					$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
					Reset password failed! Token expired.</div>');
					redirect('Auth');
				}
			} else {
				$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
				Reset password failed! Token invalid.</div>');
				redirect('Auth');
			}
		} else {
			$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
			Reset password failed! Wrong email.</div>');
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

	public function forgotPassword()
	{
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');

		if ($this->form_validation->run() == false) {
			$data['title'] = 'Forgot Password';
			$this->load->view('templates/auth_header', $data);
			$this->load->view('auth/forgot-password');
			$this->load->view('templates/auth_footer');
		} else {
			$email = $this->input->post('email');

			$user = $this->db->get_where('user', ['email' => $email, 'is_active' => 1])->row();

			if ($user) {
				$token = base64_encode(random_bytes(32));
				$this->m_users->saveUserToken($token);
				$this->_sendEmail($token, 'forgot');

				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
				Please check your email to reset your password.</div>');
				redirect('Auth/forgotPassword');
			} else {
				$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
				email is not registered or activated.</div>');
				redirect('Auth/forgotPassword');
			}
		}
	}

	public function changePassword()
	{
		if (!$this->session->userdata('reset_email')) {
			redirect('Auth');
		}

		$data['title'] = 'Change Password';
		$email = $this->session->userdata('reset_email');
		$data['user'] = $this->m_users->UserByEmail($email)->row();

		$this->form_validation->set_rules('password1', 'New Password', 'required|trim|min_length[3]|matches[password2]', [
			'matches' => 'Password not match!',
			'min_length' => 'Password too short!'
		]);
		$this->form_validation->set_rules('password2', 'Repeat Password', 'required|trim|min_length[3]|matches[password1]', [
			'matches' => 'Password not match!',
			'min_length' => 'Password too short!'
		]);

		if ($this->form_validation->run() == false) {
			$this->load->view('templates/auth_header', $data);
			$this->load->view('auth/change-password', $data);
			$this->load->view('templates/auth_footer');
		} else {
			$newpassword = $this->input->post('password1');

			$password_hash = password_hash($newpassword, PASSWORD_DEFAULT);

			$this->db->set('password', $password_hash);
			$this->db->where('email', $email);
			$this->db->update('user');

			//delete user token by email, karena sudah tidak dibutuhkan.
			$this->db->delete('user_token', ['email' => $email]);

			//hapus session reset email
			$this->session->unset_userdata('reset_email');

			if ($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
						Password has been changed! Please login</div>');
			}
			redirect('Auth');
		}
	}
}
