<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Spj_detail extends CI_Controller {
	public $id_user = null;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('kegiatan_model','kegiatan');
		$this->load->model('rekening_model','rekening');
		$this->load->model('spj_model', 'spj');
		$this->load->model('spj_detail_model', 'spj_detail');
		$this->load->model('panjar_model', 'panjar');
		$this->load->model('pajak_model', 'pajak');
		$this->id_user = $this->session->userdata('id_user');
		$this->login_lib->cek_login();
		$this->login_lib->cek_admin();
	}

	public function index()
	{
		$data = array('title' => 'Halaman Detail SPJ',
						  		'isi'  => 'bendahara/spj_detail/list_spj_detail',
									'foot' => 'bendahara/spj_detail/foot_spj_detail',
									'breadcrum1' => 'Data Panjar',
									'breadcrum2' => 'SPJ Detail'
								);

		$this->load->view('bendahara/layout/wrapper', $data);
	}

	public function ajax_list($id_spj)
	{
		$jumlah_desimal ="0";
		$pemisah_desimal =",";
		$pemisah_ribuan =".";
		$list = $this->spj_detail->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		foreach ($list as $spj_detail) {
			if ($spj_detail->id_spj == $id_spj) {

						$list_pajak = $this->pajak->list_pajak($spj_detail->id_spj_detail);

						$no++;
						$row = array();
						$row[] = $spj_detail->id_spj_detail;
						$row[] = $i;
						$row[] = $spj_detail->kode_rekening;
						$row[] = $spj_detail->uraian_rekening;
						$row[] = number_format($spj_detail->nilai_spj, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan);
						$row[] = $spj_detail->no_spj_detail;
						$row[] = date('d-m-Y', strtotime($spj_detail->tgl_spj_detail));
						if ($list_pajak  == null) {
								$row[] = '-';
						} else {
								$row[] = $list_pajak->pajak;
						}
						$row[] =  '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Input Pajak" onclick="input_pajak('."'".$spj_detail->id_spj_detail."'".')"><i class="glyphicon glyphicon-scissors"></i> Pajak</a>';

						$data[] = $row;
						$i++;
			}
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->spj_detail->count_all(),
						"recordsFiltered" => $this->spj_detail->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id_spj_detail)
	{
		$data = $this->spj_detail->get_by_id($id_spj_detail);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		if($this->input->post('tgl_setor_pajak') == '')
		{
			$tgl_setor_pajak = NULL;
		} else {
			$tgl_setor_pajak = date('Y-m-d', strtotime($this->input->post('tgl_setor_pajak')));
		}
		$data = array(
				'id_spj'		=> $this->input->post('id_spj'),
				'id_rekening' => $this->input->post('option_rekening'),
				'nilai_spj' => $this->input->post('nilai_spj'),
				'no_spj_detail' => $this->input->post('no_spj_detail'),
				'tgl_spj_detail' => date('Y-m-d', strtotime($this->input->post('tgl_spj_detail'))),
				'ket_spj_detail' => $this->input->post('ket_spj_detail'),
			);
		$insert = $this->spj_detail->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		if($this->input->post('tgl_setor_pajak') == '')
		{
			$tgl_setor_pajak = NULL;
		} else {
			$tgl_setor_pajak = date('Y-m-d', strtotime($this->input->post('tgl_setor_pajak')));
		}


		$data = array(
				'id_rekening' => $this->input->post('option_rekening'),
				'nilai_spj' => $this->input->post('nilai_spj'),
				'no_spj_detail' => $this->input->post('no_spj_detail'),
				'tgl_spj_detail' => date('Y-m-d', strtotime($this->input->post('tgl_spj_detail'))),
				'ket_spj_detail' => $this->input->post('ket_spj_detail'),
			);
		$this->spj_detail->update(array('id_spj_detail' => $this->input->post('id_spj_detail')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id_spj_detail)
	{
		$this->spj_detail->delete_by_id($id_spj_detail);
		echo json_encode(array("status" => TRUE));
	}


	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('option_rekening') == '')
		{
			$data['inputerror'][] = 'rekening';
			$data['error_string'][] = 'Nomor Rekening harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('nilai_spj') == '')
		{
			$data['inputerror'][] = 'nilai_spj';
			$data['error_string'][] = 'Nilai harus diisi';
			$data['status'] = FALSE;
		}


		if($this->input->post('no_spj_detail') == '')
		{
			$data['inputerror'][] = 'no_spj_detail';
			$data['error_string'][] = 'No. Bukti harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('tgl_spj_detail') == '')
		{
			$data['inputerror'][] = 'tgl_spj_detail';
			$data['error_string'][] = 'Tanggal bukti harus diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	private function _validate_pajak()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('option_pajak') == '')
		{
			$data['inputerror'][] = 'pajak';
			$data['error_string'][] = 'Jenis Pajak Harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('nilai_pajak') == '')
		{
			$data['inputerror'][] = 'nilai_pajak';
			$data['error_string'][] = 'Nilai pajak harus diisi';
			$data['status'] = FALSE;
		}


		if($this->input->post('tgl_setor_pajak') == '')
		{
			$data['inputerror'][] = 'tgl_setor_pajak';
			$data['error_string'][] = 'Tanggal setor pajak harus diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}


	public function option_uraian_rekening(){
		$list = $this->rekening->listing();
		$data = '<option selected="selected" value="" disabled> - Pilih Rekening - </option>';
		foreach ($list as $rekening) {
			if ($rekening->parent != '0') {
				$data .= '<option value="'.$rekening->id_rekening.'">'.$rekening->kode_rekening.' - '.$rekening->uraian_rekening.'</option>';
			}
		}
		echo json_encode($data);
	}

	public function option_uraian_rekening_edit($id_rekening){
		$list = $this->rekening->listing();
		$data = '<option value="" disabled> - Pilih Kegiatan - </option>';
		foreach ($list as $rekening) {
			if ($rekening->parent != 0 ) {
				if ($id_rekening == $rekening->id_rekening) {
					$data .= '<option value="'.$rekening->id_rekening.'" selected="selected">'.$rekening->kode_rekening.' - '.$rekening->uraian_rekening.'</option>';
				} else {
					$data .= '<option value="'.$rekening->id_rekening.'">'.$rekening->kode_rekening.' - '.$rekening->uraian_rekening.'</option>';
				}
			}
		}
		echo json_encode($data);
	}

	public function option_rekening(){
		$list = $this->rekening->listing();
		$data = '<option selected="selected" value="" disabled> - Pilih Rekening - </option>';
		foreach ($list as $rekening) {
			if ($rekening->parent == '0') {
				$data .= '<option value="'.$rekening->id_rekening.'" class="parent">'.$rekening->kode_rekening.' - '.$rekening->uraian_rekening.'</option>';
			} else {
				$data .= '<option value="'.$rekening->id_rekening.'">'.$rekening->kode_rekening.' - '.$rekening->uraian_rekening.'</option>';
			}
		}
		echo json_encode($data);
	}

	public function option_rekening_edit($id_rekening){
		$list = $this->rekening->listing();
		$data = '<option value="" disabled> - Pilih Kegiatan - </option>';
		foreach ($list as $rekening) {
			if ($id_rekening == $rekening->id_rekening) {
				$data .= '<option value="'.$rekening->id_rekening.'" selected="selected">'.$rekening->kode_rekening.' - '.$rekening->uraian_rekening.'</option>';
			} else {
				$data .= '<option value="'.$rekening->id_rekening.'">'.$rekening->kode_rekening.' - '.$rekening->uraian_rekening.'</option>';
			}
		}
		echo json_encode($data);
	}

	// public function option_pajak_edit($id_spj_detail){
	// 	$pajak_db = $this->spj_detail->get_by_id($id_spj_detail);
	// 	$pajak = array("PPN", "PPh 21", "PPh 22", "PPh 23", "PPh Pasal 4 (2)", "Pajak Daerah");
	// 	$data = '<option value=""> - Pilih Pajak - </option>';
	// 	foreach ($pajak as $pajak) {
	// 		if ($pajak == $pajak_db->pajak) {
	// 			$data .= '<option value="'.$pajak.'" selected="selected">'.$pajak.'</option>';
	// 		} else {
	// 			$data .= '<option value="'.$pajak.'">'.$pajak.'</option>';
	// 		}
	// 	}
	// 	echo json_encode($data);
	// }

	public function sum_spj($id_spj)
	{
		$data = $this->spj_detail->sum_spj($id_spj);
		echo json_encode($data->nilai_spj);
	}

	public function sisa_spj($id_spj)
	{
		$spj = $this->spj->get_by_id($id_spj);
		$panjar = $this->panjar->get_by_id($spj->id_panjar);
		$data = $this->spj_detail->sum_spj($id_spj);
		$sisa = $panjar->nilai_panjar - $data->nilai_spj;
		echo json_encode($sisa);
	}

	public function pajak_list($id_spj_detail){
		$jumlah_desimal ="0";
		$pemisah_desimal =",";
		$pemisah_ribuan =".";
		$list = $this->pajak->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		foreach ($list as $pajak) {
			if ($pajak->id_spj_detail == $id_spj_detail) {

						$no++;
						$row = array();
						$row[] = $i;
						$row[] = $pajak->pajak;
						$row[] = number_format($pajak->nilai_pajak, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan);
						$row[] = date('d-m-Y', strtotime($pajak->tgl_setor_pajak));
						$row[] =  '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="hapus_pajak('."'".$pajak->id."'".')"><i class="glyphicon glyphicon-trash"></i> Hapus</a>';

						$data[] = $row;
						$i++;
			}
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->spj_detail->count_all(),
						"recordsFiltered" => $this->spj_detail->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function get_pajak(){
		$id_spj_detail = $this->input->post('id_spj_detail');
		$pajak = $this->input->post('pajak');
		$data = $this->pajak->get_by_2cat($id_spj_detail, $pajak);
		if (!$data) {
				$data = array('jumlah' => 0, 'id_spj_detail' => null);
		}
		 echo json_encode($data);
	}

	public function pajak_add()
	{
		$this->_validate_pajak();

		$tgl_setor_pajak = date('Y-m-d', strtotime($this->input->post('tgl_setor_pajak')));

		$data = array(
				'id_spj_detail'		=> $this->input->post('id_spj_detail'),
				'pajak' => $this->input->post('option_pajak'),
				'nilai_pajak' => $this->input->post('nilai_pajak'),
				'tgl_setor_pajak' => $tgl_setor_pajak,
			);
		$insert = $this->pajak->save($data);
		echo json_encode(array("status" => TRUE, "notif" => "<strong><h4>Info!</h4></strong>Pajak berhasil ditambahkan"));
	}

	public function pajak_update()
	{
		$this->_validate_pajak();

		$tgl_setor_pajak = date('Y-m-d', strtotime($this->input->post('tgl_setor_pajak')));

		$data = array(
				'pajak' => $this->input->post('option_pajak'),
				'nilai_pajak' => $this->input->post('nilai_pajak'),
				'tgl_setor_pajak' => $tgl_setor_pajak,
			);

		$this->pajak->update(array('id' => $this->input->post('id_pajak')), $data);
		echo json_encode(array("status" => TRUE, "notif" => "<strong><h4>Info!</h4></strong>Pajak berhasil diedit"));
	}

	public function pajak_delete($id)
	{
		$this->pajak->delete_by_id($id);
		echo json_encode(array("status" => TRUE));
	}

}
