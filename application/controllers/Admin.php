<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_users');

		is_logged_in();
	}

	public function index()
	{
		$data['title'] = 'Dashboard';
		$email = $this->session->userdata('email');
		// $data['user'] = $this->m_users->UserByEmail($email)->row(); ==> bisa juga pake variabel seperti ini.
		$data['user'] = $this->m_users->UserByEmail($email)->row();

		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar', $data);
		$this->load->view('templates/topbar', $data);
		$this->load->view('admin/index', $data);
		$this->load->view('templates/footer');
	}

	public function role()
	{
		$email = $this->session->userdata('email');

		$data['title'] = 'Role';

		$data['user'] = $this->m_users->UserByEmail($email)->row();

		$data['role'] = $this->db->get('user_role')->result();

		$this->db->where('id !=', 1);
		$data['menu'] = $this->db->get('user_menu')->result();

		$this->form_validation->set_rules('role', 'Role', 'required');

		if ($this->form_validation->run() == false) {
			$this->load->view('templates/header', $data);
			$this->load->view('templates/sidebar', $data);
			$this->load->view('templates/topbar', $data);
			$this->load->view('admin/role', $data);
			$this->load->view('templates/footer');
		} else {
			$data = [
				'role' => $this->input->post('role')
			];

			$this->db->insert('user_role', $data);
			if ($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
				New role added!</div>');
			}
			redirect('Admin/role');
		}
	}

	public function changeaccess()
	{
		$menu_id = $this->input->post('menuId');
		$role_id = $this->input->post('roleId');

		$data = [
			'role_id' => $role_id,
			'menu_id' => $menu_id
		];

		$result = $this->db->get_where('user_access_menu', $data);

		if ($result->num_rows() < 1) {
			$this->db->insert('user_access_menu', $data);
		} else {
			$this->db->delete('user_access_menu', $data);
		}
	}
}
