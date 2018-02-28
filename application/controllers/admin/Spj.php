<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Spj extends CI_Controller {
	public $id_user = null;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('kegiatan_model','kegiatan');
		$this->load->model('spj_model', 'spj');
		$this->load->model('panjar_model', 'panjar');
		$this->load->model('spj_detail_model', 'spj_detail');
		$this->login_lib->cek_login();
		$this->login_lib->cek_bendahara();
	}

	public function index()
	{
		$data = array('title' => 'Halaman Pengajuan SPJ',
						  		'isi'  => 'admin/spj/list_spj',
									'foot' => 'admin/spj/foot_spj',
									'breadcrum1' => 'Data Panjar',
									'breadcrum2' => 'Pengajuan SPJ'
								);

		$this->load->view('admin/layout/wrapper', $data);
	}

	public function ajax_list($id_panjar)
	{
		$jumlah_desimal ="0";
		$pemisah_desimal =",";
		$pemisah_ribuan =".";
		$list = $this->spj->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		foreach ($list as $spj) {
			if ($spj->id_panjar == $id_panjar) {

						$no++;
						$row = array();
						$row[] = $spj->nama_kegiatan;
						$row[] = $spj->no_bukti;
						$row[] = number_format($spj->nilai_panjar, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan);
						$row[] = $spj->no_spj;
						$row[] = date('d-m-Y', strtotime($spj->tgl_spj));
						$row[] = $spj->ket_spj;

						//validation isVerified
						if ($spj->isVerified == 0) {
									$row[] = '<a class="badge bg-grey" href="javascript:void(0)" title="Validasi" id="belum_sah" onclick="validasi_spj('."'".$spj->id_spj."'".')">Belum Disahkan</a>';
						} else {
									//add html for action
									$row[] = '<a class="badge bg-green" href="javascript:void(0)" title="Validasi" id="sudah_sah" onclick="validasi_spj('."'".$spj->id_spj."'".')">Sudah Disahkan</span>';
						}
						$data[] = $row;
						$i++;
			}
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->spj->count_all(),
						"recordsFiltered" => $this->spj->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id_spj)
	{
		$data = $this->spj->get_by_id($id_spj);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		$data = array(
				'id_panjar' => $this->input->post('option_panjar'),
				'no_spj' => $this->input->post('no_spj'),
				'tgl_spj' => date('Y-m-d', strtotime($this->input->post('tgl_spj'))),
				'ket_spj' => $this->input->post('ket_spj'),
			);
		$insert = $this->spj->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
					'id_panjar' => $this->input->post('option_panjar'),
					'no_spj' => $this->input->post('no_spj'),
					'tgl_spj' => date('Y-m-d', strtotime($this->input->post('tgl_spj'))),
					'ket_spj' => $this->input->post('ket_spj'),
			);
		$this->spj->update(array('id_spj' => $this->input->post('id_spj')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update_pengesahan()
	{
		$data = array(
					'no_pengesahan' => $this->input->post('no_pengesahan'),
					'tgl_pengesahan' => date('Y-m-d', strtotime($this->input->post('tgl_pengesahan'))),
					'isVerified' => $this->input->post('isVerified'),
			);
		$this->spj->update(array('id_spj' => $this->input->post('id_spj')), $data);
		echo json_encode(array("status" => TRUE, "notif" => "Status SPJ telah disimpan"));
	}

	public function ajax_delete($id_spj)
	{
		$this->spj->delete_by_id($id_spj);
		echo json_encode(array("status" => TRUE));
	}


	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('option_panjar') == '')
		{
			$data['inputerror'][] = 'option_panjar';
			$data['error_string'][] = 'Nama kegiatan harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('no_spj') == '')
		{
			$data['inputerror'][] = 'no_spj';
			$data['error_string'][] = 'Nomor SPJ harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('tgl_spj') == '')
		{
			$data['inputerror'][] = 'tgl_spj';
			$data['error_string'][] = 'Tanggal SPJ harus diisi';
			$data['status'] = FALSE;
		}


		if($this->input->post('ket_spj') == '')
		{
			$data['inputerror'][] = 'ket_spj';
			$data['error_string'][] = 'Keterangan SPJ harus diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	public function option_panjar(){
		$list = $this->panjar->get_by_user($this->id_user);
		$data = '<option value=""> - Pilih Kegiatan - </option>';
		foreach ($list as $panjar) {
			$data .= '<option value="'.$panjar->id_panjar.'">'.$panjar->nama_kegiatan.' - '.$panjar->no_bukti.'</option>';
		}
		echo json_encode($data);
	}

	public function option_panjar_edit($id_panjar){
		$list = $this->panjar->get_by_user($this->id_user);
		$data = '<option value=""> - Pilih Kegiatan - </option>';
		foreach ($list as $panjar) {
			if ($id_panjar== $panjar->id_panjar) {
				$data .= '<option value="'.$panjar->id_panjar.'" selected="selected">'.$panjar->nama_kegiatan.' - '.$panjar->no_bukti.'</option>';
			} else {
				$data .= '<option value="'.$panjar->id_panjar.'">'.$panjar->nama_kegiatan.' - '.$panjar->no_bukti.'</option>';
			}
		}
		echo json_encode($data);
	}


	public function listing($id_spj)
	{
		$spj = $this->spj->listing($id_spj);
		echo json_encode($spj);
	}


	public function detail($id_spj)
	{
		$spj_detail= $this->spj_detail->listing($id_spj);
		$list_program = $this->spj->list_program($id_spj);

		$data = array('title' => 'Halaman Detail SPJ',
						  		'isi'  => 'admin/spj_detail/list_spj_detail',
									'foot' => 'admin/spj_detail/foot_spj_detail',
									'breadcrum1' => 'Data Panjar',
									'breadcrum2' => 'Detail SPJ',
									'spj_detail' => $spj_detail,
									'list_program' => $list_program
								);

		$this->load->view('admin/layout/wrapper', $data);
	}
}
