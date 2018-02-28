<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('program_model','program');
	}

	public function index()
	{
		$this->login_lib->cek_in_login();
		//validation form
		$valid=$this->form_validation;
		$valid->set_rules('username','Username','required',
								array('required'		=> 'Username harus diisi'));
		$valid->set_rules('password','Password','required',
								array('required'		=> 'Password harus diisi'));

		$user=$this->input->post('username');
		$pass=$this->input->post('password');
		$tahun=$this->input->post('tahun_anggaran');

		if($valid->run()){
			$this->login_lib->login($user,$pass, $tahun);
		}

		$data = array('title'=> 'Login');
		$this->load->view('login_view',$data);
	}

	public function signout(){
		$this->login_lib->logout();
	}

	public function year(){
		$tahun = $this->program->get_year();
		echo json_encode($tahun);
	}
}
