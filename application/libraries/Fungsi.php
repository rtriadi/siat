<?php

class Fungsi
{
	protected $ci;

	function __construct()
	{
		$this->ci = &get_instance();
	}

	function user_login()
	{
		$id_user = $this->ci->session->userdata('id_user');
		if (!$id_user) {
			return null;
		}

		return $this->ci->db
			->get_where('user', array('id_user' => $id_user))
			->row();
	}
}
