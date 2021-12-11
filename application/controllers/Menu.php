<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_users');
		$this->load->model('m_menu');

		is_logged_in();
	}

	public function index()
	{
		$email = $this->session->userdata('email');

		$data['title'] = 'Menu Management';

		$data['user'] = $this->m_users->UserByEmail($email)->row(); //Kalau hasil nya cuma sebaris pakai row(row_array)

		$data['menu'] = $this->m_menu->getMenu(); //Kalau hasil nya bayanyak baris pakai result(result_array)

		$this->form_validation->set_rules('menu', 'Menu', 'required');

		if ($this->form_validation->run() == false) {
			$this->load->view('templates/header', $data);
			$this->load->view('templates/sidebar', $data);
			$this->load->view('templates/topbar', $data);
			$this->load->view('menu/index', $data);
			$this->load->view('templates/footer');
		} else {

			$data = [
				'menu' => $this->input->post('menu')
			];

			$this->db->insert('user_menu', $data);
			if ($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
				New menu added!</div>');
			}
			redirect('Menu');
		}
	}

	public function submenu()
	{
		$email = $this->session->userdata('email');

		$data['title'] = 'Submenu Management';

		$data['user'] = $this->m_users->UserByEmail($email)->row(); //Kalau hasil nya cuma sebaris pakai row(row_array)

		$data['submenu'] = $this->m_menu->getSubMenu(); //Kalau hasil nya bayanyak baris pakai result(result_array)

		$data['menu'] = $this->m_menu->getMenu();

		$this->form_validation->set_rules('title', 'Title', 'required');
		$this->form_validation->set_rules('menu_id', 'Menu', 'required');
		$this->form_validation->set_rules('url', 'URL', 'required');
		$this->form_validation->set_rules('icon', 'Icon', 'required');

		if ($this->form_validation->run() == false) {
			$this->load->view('templates/header', $data);
			$this->load->view('templates/sidebar', $data);
			$this->load->view('templates/topbar', $data);
			$this->load->view('menu/submenu', $data);
			$this->load->view('templates/footer');
		} else {
			$post = $this->input->post();
			$data = [
				'title' => $post['title'],
				'menu_id' => $post['menu_id'],
				'url' => $post['url'],
				'icon' => $post['icon'],
				'is_active' => $post['is_active']
			];

			$this->db->insert('user_sub_menu', $data);
			if ($this->db->affected_rows() > 0) {
				$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
				New submenu added!</div>');
			}
			redirect('Menu/submenu');
		}
	}
}
