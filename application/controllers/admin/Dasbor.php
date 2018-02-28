<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dasbor extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->login_lib->cek_login();
		$this->login_lib->cek_bendahara();
	}

	public function index()
	{
		$data = array('title' => 'Halaman Dashboard Admin',
						  'isi'  => 'admin/list');

		$this->load->view('/admin/layout/wrapper', $data);
	}
}
