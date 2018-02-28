<?php
defined('BASEPATH') OR exit('No direct script access allowed');



class Program extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('program_model','program');
		$this->login_lib->cek_login();
		$this->login_lib->cek_admin();
	}


	public function ajax_edit($id_program)
	{
		$data = $this->program->get_by_id($id_program);
		echo json_encode($data);
	}

}
