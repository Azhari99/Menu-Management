<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_users');

		is_logged_in();
	}

	public function index()
	{
		$data['title'] = 'My Profile';
		$email = $this->session->userdata('email');
		// $data['user'] = $this->m_users->UserByEmail($email)->row(); ==> bisa juga pake variabel seperti ini.
		$data['user'] = $this->m_users->UserByEmail($email)->row();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar', $data);
		$this->load->view('templates/topbar', $data);
		$this->load->view('user/index', $data);
		$this->load->view('templates/footer');
	}

	public function edit()
	{
		$data['title'] = 'Edit Profile';
		$email = $this->session->userdata('email');
		// $data['user'] = $this->m_users->UserByEmail($email)->row(); ==> bisa juga pake variabel seperti ini.
		$data['user'] = $this->m_users->UserByEmail($email)->row();

		$this->form_validation->set_rules('name', 'Full name', 'required|trim');

		if ($this->form_validation->run() == false) {
			$this->load->view('templates/header', $data);
			$this->load->view('templates/sidebar', $data);
			$this->load->view('templates/topbar', $data);
			$this->load->view('user/edit', $data);
			$this->load->view('templates/footer');
		} else {

			$name = $this->input->post('name');
			$email = $this->input->post('email');

			// Cek jika ada gambar yang diupload
			$upload_image = $_FILES['image']['name'];

			if ($upload_image) {
				$config['allowed_types'] = 'gif|jpg|png';
				//$config['max_size']     = '5120';
				$config['upload_path'] = './assets/img/profile/';

				$this->load->library('upload', $config);

				if ($this->upload->do_upload('image')) {
					$new_image = $this->upload->data('file_name');
					$old_image = $data['user']->image;

					if ($old_image != 'default.png') {
						unlink(FCPATH . 'assets/img/profile/' . $old_image);
					}

					$this->db->set('image', $new_image);
				} else {
					echo $this->upload->display_errors();
				}
			}

			$this->db->set('name', $name);
			$this->db->where('email', $email);
			$this->db->update('user');
			if ($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
				Your profile has been updated!</div>');
			}
			redirect('User');
		}
	}

	public function changepassword()
	{
		$data['title'] = 'Change Password';
		$email = $this->session->userdata('email');
		// $data['user'] = $this->m_users->UserByEmail($email)->row(); ==> bisa juga pake variabel seperti ini.
		$data['user'] = $this->m_users->UserByEmail($email)->row();

		$this->form_validation->set_rules('oldpassword', 'Old Password', 'required|trim');
		$this->form_validation->set_rules('password1', 'New Password', 'required|trim|min_length[3]|matches[password2]', [
			'matches' => 'Password not match!',
			'min_length' => 'Password too short!'
		]);
		$this->form_validation->set_rules('password2', 'Password Confirmation', 'required|trim|matches[password1]');

		if ($this->form_validation->run() == false) {
			$this->load->view('templates/header', $data);
			$this->load->view('templates/sidebar', $data);
			$this->load->view('templates/topbar', $data);
			$this->load->view('user/changepassword', $data);
			$this->load->view('templates/footer');
		}
	}
}
