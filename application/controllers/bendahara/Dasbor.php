<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dasbor extends CI_Controller {

		public $id_user = null;
		public $akses_level = null;
		public $username = null;
		public $nama_user = null;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('program_model','program');
		$this->load->model('kegiatan_model','kegiatan');
		$this->load->model('panjar_model', 'panjar');
		$this->load->model('ls_model', 'ls');
		$this->load->model('spj_model', 'spj');
		$this->load->model('pengembalian_model', 'pengembalian');
		$this->id_user = $this->session->userdata('id_user');
		$this->akses_level = $this->session->userdata('akses_level');
		$this->username = $this->session->userdata('username');
		$this->nama_user = $this->session->userdata('nama_user');
		$this->login_lib->cek_login();
		$this->login_lib->cek_admin();
	}


	public function index()
	{
		$program = $this->program->get_by_user($this->id_user);
		$count_program = count($program);

		$kegiatan = $this->kegiatan->get_by_user($this->id_user);
		$count_kegiatan = count($kegiatan);

		$panjar = $this->panjar->get_by_user($this->id_user);
		$count_panjar = count($panjar);

		$spj = $this->spj->get_by_user($this->id_user);
		$count_spj = count($spj);

		$pengembalian = $this->pengembalian->get_by_user($this->id_user);
		$count_pengembalian = count($pengembalian);

		$ls = $this->ls->get_by_user($this->id_user);
		$count_ls = count($ls);

		$data = array('title' => 'Summary',
								  'isi'  => 'bendahara/list',
									'foot' => 'bendahara/foot',
									'breadcrum1' => '',
									'breadcrum2' => '',
									'kegiatan' => $kegiatan,
									'count_kegiatan' => $count_kegiatan,
									'program' => $program,
									'count_program' => $count_program,
									'panjar' => $panjar,
									'count_panjar' => $count_panjar,
									'count_spj' => $count_spj,
									'count_pengembalian' => $count_pengembalian,
									'count_ls' => $count_ls,
										);
		$this->load->view('bendahara/layout/wrapper', $data);
	}

	public function list_program()
	{
		$program = $this->program->get_by_user($this->id_user);
		$count = count($data);

		$output = array(
										'data' => $data,
										'count' => $count
									);
		echo json_encode($output);
	}

}
