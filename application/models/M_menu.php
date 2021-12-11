<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_menu extends CI_Model
{
	private $_table = 'user_menu';

	public function getMenu()
	{
		return $this->db->get($this->_table)->result();
	}
	/* Perbedaan result dengan row 
		result = hasil query dengan banyak baris
		row = hasil query hanya 1 baris
	*/
	public function getSubMenu()
	{
		$querySubMenu = "SELECT * FROM user_sub_menu sm
				JOIN user_menu m ON m.id = sm.menu_id
				ORDER BY m.id ASC";
		return $this->db->query($querySubMenu)->result();
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
