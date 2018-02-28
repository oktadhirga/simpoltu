<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengembalian extends CI_Controller {
	public $id_user = null;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('kegiatan_model','kegiatan');
		$this->load->model('pengembalian_model', 'pengembalian');
		$this->load->model('panjar_model', 'panjar');
		$this->load->model('spj_model', 'spj');
		$this->load->model('spj_detail_model', 'spj_detail');
		$this->id_user = $this->session->userdata('id_user');
		$this->login_lib->cek_login();
		$this->login_lib->cek_admin();
	}

	public function index()
	{
		$data = array('title' => 'Halaman Pengembalian Panjar',
						  		'isi'  => 'bendahara/pengembalian/list_pengembalian',
									'foot' => 'bendahara/pengembalian/foot_pengembalian',
									'breadcrum1' => 'Data Panjar',
									'breadcrum2' => 'Pengembalian Panjar'
								);

		$this->load->view('bendahara/layout/wrapper', $data);
	}

	public function ajax_list($id_panjar)
	{
		$jumlah_desimal ="0";
		$pemisah_desimal =",";
		$pemisah_ribuan =".";
		$list = $this->pengembalian->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		foreach ($list as $pengembalian) {
			if ($pengembalian->id_user == $this->id_user && $pengembalian->id_panjar == $id_panjar) {

						$no++;
						$row = array();
						$row[] = $pengembalian->no_pengembalian;
						$row[] = date('d-m-Y', strtotime($pengembalian->tgl_pengembalian));
						$row[] = number_format($pengembalian->nilai_pengembalian, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan);
						$row[] = $pengembalian->ket_pengembalian;

						$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_pengembalian('."'".$pengembalian->id_pengembalian."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
										  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_pengembalian('."'".$pengembalian->id_pengembalian."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';

						$data[] = $row;
						$i++;
			}
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->pengembalian->count_all(),
						"recordsFiltered" => $this->pengembalian->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id_pengembalian)
	{
		$data = $this->pengembalian->get_by_id($id_pengembalian);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		$data = array(
				'id_panjar' => $this->input->post('id_panjar'),
				'no_pengembalian' => $this->input->post('no_pengembalian'),
				'tgl_pengembalian' => date('Y-m-d', strtotime($this->input->post('tgl_pengembalian'))),
				'nilai_pengembalian' => $this->input->post('nilai_pengembalian'),
				'ket_pengembalian' => $this->input->post('ket_pengembalian'),
			);
		$insert = $this->pengembalian->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
				'no_pengembalian' => $this->input->post('no_pengembalian'),
				'tgl_pengembalian' => date('Y-m-d', strtotime($this->input->post('tgl_pengembalian'))),
				'nilai_pengembalian' => $this->input->post('nilai_pengembalian'),
				'ket_pengembalian' => $this->input->post('ket_pengembalian'),
			);
		$this->pengembalian->update(array('id_pengembalian' => $this->input->post('id_pengembalian')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id_pengembalian)
	{
		$this->pengembalian->delete_by_id($id_pengembalian);
		echo json_encode(array("status" => TRUE));
	}


	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;


		if($this->input->post('no_pengembalian') == '')
		{
			$data['inputerror'][] = 'no_pengembalian';
			$data['error_string'][] = 'Nomor pengembalian harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('tgl_pengembalian') == '')
		{
			$data['inputerror'][] = 'tgl_pengembalian';
			$data['error_string'][] = 'Tanggal Bukti harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('nilai_pengembalian') == '')
		{
			$data['inputerror'][] = 'nilai_pengembalian';
			$data['error_string'][] = 'Nilai pengembalian harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('ket_pengembalian') == '')
		{
			$data['inputerror'][] = 'ket_pengembalian';
			$data['error_string'][] = 'Keterangan pengembalian harus diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	public function option_kegiatan(){
		$list = $this->panjar->get_by_user($this->id_user);
		$data = '<option value=""> - Pilih Kegiatan - </option>';
		foreach ($list as $kegiatan) {
			$data .= '<option value="'.$kegiatan->id_panjar.'">'.$kegiatan->nama_kegiatan.' - '.$kegiatan->no_bukti.'</option>';
		}
		echo json_encode($data);
	}

	public function option_kegiatan_edit($id_kegiatan){
		$list = $this->panjar->get_by_user($this->id_user);
		$data = '<option value=""> - Pilih Kegiatan - </option>';
		foreach ($list as $kegiatan) {
			if ($id_kegiatan== $kegiatan->id_kegiatan) {
				$data .= '<option value="'.$kegiatan->id_panjar.'" selected="selected">'.$kegiatan->nama_kegiatan.' - '.$kegiatan->no_bukti.'</option>';
			} else {
				$data .= '<option value="'.$kegiatan->id_panjar.'">'.$kegiatan->nama_kegiatan.' - '.$kegiatan->no_bukti.'</option>';
			}
		}
		echo json_encode($data);
	}

	public function hitung_sisa($id_panjar){
			$panjar = $this->panjar->get_by_id($id_panjar);
			$spj_detail = $this->spj_detail->sum_spj_by_panjar($id_panjar);
			$sisa = $panjar->nilai_panjar - $spj_detail->nilai_spj;
			echo json_encode($sisa);
	}

}
