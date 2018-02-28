<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengaturan extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('pengaturan_model','pengaturan');
		$this->login_lib->cek_login();
		$this->login_lib->cek_bendahara();
	}

	public function index()
	{
		$pengaturan = $this->pengaturan->listing();
		$data = array('title' => 'Pengaturan Umum',
						  		'isi'  => 'admin/pengaturan/list_pengaturan',
									'foot' => 'admin/pengaturan/foot_pengaturan',
									'pengaturan' => $pengaturan,
									'breadcrum1' => 'Pengaturan Umum',
									'breadcrum2' => ''
								);

		$this->load->view('admin/layout/wrapper', $data);
	}


	public function ajax_update()
	{
		// $this->_validate();
		// $data = array(
		// 		'nama_pengaturan' => $this->input->post('nama_pengaturan'),
		// 		'akronim' => $this->input->post('akronim'),
		// 		'kepala_pengaturan' => $this->input->post('kepala_pengaturan'),
		// 	);

		$this->pengaturan->update(array('nama_pengaturan' => 'unit_instansi'), array('nilai_pengaturan' => $this->input->post('instansi')));
		$this->pengaturan->update(array('nama_pengaturan' => 'perangkat_daerah'), array('nilai_pengaturan' => $this->input->post('unit_kerja')));
		$this->pengaturan->update(array('nama_pengaturan' => 'rekening_pd'), array('nilai_pengaturan' => $this->input->post('rekening_uk')));
		$this->pengaturan->update(array('nama_pengaturan' => 'bendahara_pengeluaran'), array('nilai_pengaturan' => $this->input->post('bendahara_pengeluaran')));
		$this->pengaturan->update(array('nama_pengaturan' => 'nip_bp'), array('nilai_pengaturan' => $this->input->post('nip_bp')));
		$this->pengaturan->update(array('nama_pengaturan' => 'alamat_pd'), array('nilai_pengaturan' => $this->input->post('alamat_unit_kerja')));
		$this->pengaturan->update(array('nama_pengaturan' => 'kota_kdpos'), array('nilai_pengaturan' => $this->input->post('kota_kdpos')));
		$this->pengaturan->update(array('nama_pengaturan' => 'nama_pa'), array('nilai_pengaturan' => $this->input->post('pengguna_anggaran')));
		$this->pengaturan->update(array('nama_pengaturan' => 'nip_pa'), array('nilai_pengaturan' => $this->input->post('nip_pa')));
		echo json_encode(array("status" => TRUE, "notif" => "Pengaturan berhasil diperbarui"));
	}



	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('nama_pengaturan') == '')
		{
			$data['inputerror'][] = 'nama_pengaturan';
			$data['error_string'][] = 'Nama pengaturan harus diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

}
