<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_users extends CI_Model
{
	private $_table = 'user';

	public function list()
	{
		return $this->db->get($this->_table)->result();
	}
	/* Perbedaan result dengan row 
		result = hasil query dengan banyak baris
		row = hasil query hanya 1 baris
	*/
	public function detail($id)
	{
		return $this->db->get_where($this->_table, array('id' => $id));
	}

	public function UserByEmail($email)
	{
		return $this->db->get_where($this->_table, array('email' => $email)); //di controller nya pakai ->row lagi
		//return $this->db->get_where($this->_table, array('email' => $email))->row(); di controller nya ga perlu pakai ->row lagi
	}

	public function save()
	{
		$post = $this->input->post();
		$this->name = $post['name'];
		$this->email = $post['email'];
		$this->image = 'default.jpg';
		$this->password = password_hash($post['password1'], PASSWORD_DEFAULT);
		$this->role_id = 2;
		$this->is_active = 1;
		$this->date_create = time();
		// $data = [
		// 	'name' => htmlspecialchars($this->input->post('name', true)),
		// 	'email' => htmlspecialchars($this->input->post('email', true)),
		// 	'image' => 'default.jpg',
		// 	'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
		// 	'role_id' => 2,
		// 	'is_active' => 1,
		// 	'date_create' => time()

		// ];
		$this->db->insert($this->_table, $this);
	}

	public function update()
	{
	}

	public function delete($id)
	{
		return $this->db->delete($this->_table, array('id' => $id));
	}


	public function updatePass($post)
	{
	}
}
