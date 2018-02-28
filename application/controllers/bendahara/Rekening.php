<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekening extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('rekening_model','rekening');
		$this->login_lib->cek_login();
		$this->login_lib->cek_admin();
	}

	public function index()
	{
		$data = array('title' => 'Halaman Rekening Belanja',
						  		'isi'  => 'bendahara/rekening/list_rekening',
									'foot' => 'bendahara/rekening/foot_rekening',
									'breadcrum1' => 'Data Master',
									'breadcrum2' => 'Rekening Belanja'
								);

		$this->load->view('bendahara/layout/wrapper', $data);
	}

	public function ajax_list()
	{
		$list = $this->rekening->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		foreach ($list as $rekening) {
			$no++;
			$row = array();
			$row[] = $i;
			$row[] = $rekening->kode_rekening;
			$row[] = $rekening->uraian_rekening;


			//add html for action
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_rekening('."'".$rekening->id_rekening."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_rekening('."'".$rekening->id_rekening."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';

			$data[] = $row;
			$i++;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->rekening->count_all(),
						"recordsFiltered" => $this->rekening->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id_rekening)
	{
		$data = $this->rekening->get_by_id($id_rekening);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		$data = array(
				'parent'	=> $this->input->post('option_jenis_belanja'),
				'kode_rekening' => $this->input->post('kode_rekening'),
				'uraian_rekening' => $this->input->post('uraian_rekening'),
			);
		$insert = $this->rekening->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
				'parent'	=> $this->input->post('option_jenis_belanja'),
				'kode_rekening' => $this->input->post('kode_rekening'),
				'uraian_rekening' => $this->input->post('uraian_rekening'),
			);
		$this->rekening->update(array('id_rekening' => $this->input->post('id_rekening')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id_rekening)
	{
		$this->rekening->delete_by_id($id_rekening);
		echo json_encode(array("status" => TRUE));
	}


	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('kode_rekening') == '')
		{
			$data['inputerror'][] = 'kode_rekening';
			$data['error_string'][] = 'Kode rekening harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('uraian_rekening') == '')
		{
			$data['inputerror'][] = 'uraian_rekening';
			$data['error_string'][] = 'Uraian rekening harus diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	public function option_rekening(){
		$list = $this->kegiatan->get_by_user($this->id_user);
		$data = '<option value=""> - Pilih Kegiatan - </option>';
		foreach ($list as $kegiatan) {
			$data .= '<option value="'.$kegiatan->id_kegiatan.'">'.$kegiatan->nama_kegiatan.'</option>';
		}
		echo json_encode($data);
	}

	public function option_jenis_belanja(){
		$list = $this->rekening->get_jenis();
		$data = '<option value="0"> - Pilih Jenis Belanja - </option>';
		foreach ($list as $belanja) {
			$data .= '<option value="'.$belanja->id_rekening.'">'.$belanja->kode_rekening.' - '.$belanja->uraian_rekening.'</option>';
		}
		echo json_encode($data);
	}

	public function sum_option_jenis_belanja(){
		$list = $this->rekening->get_jenis();
		$data = count($list);
		echo json_encode($data);
	}

	public function option_jenis_belanja_edit($id_rekening){
		$list = $this->rekening->get_by_id($id_rekening);
		$jenis = $this->rekening->get_jenis();
		$data = '<option value="0"> - Pilih Jenis Belanja - </option>';
		foreach ($jenis as $belanja) {
			if ($list->parent == $belanja->id_rekening) {
				$data .= '<option value="'.$belanja->id_rekening.'" selected = "selected">'.$belanja->kode_rekening.' - '.$belanja->uraian_rekening.'</option>';
			} else {
				$data .= '<option value="'.$belanja->id_rekening.'">'.$belanja->kode_rekening.' - '.$belanja->uraian_rekening.'</option>';
			}
		}
		echo json_encode($data);
	}

	public function option_jenis_belanja_edit_in_panjar($id_rekening){
		$jenis = $this->rekening->get_jenis();
		$data = '<option value="0"> - Pilih Jenis Belanja - </option>';
		foreach ($jenis as $belanja) {
			if ($id_rekening == $belanja->id_rekening) {
				$data .= '<option value="'.$belanja->id_rekening.'" selected = "selected">'.$belanja->kode_rekening.' - '.$belanja->uraian_rekening.'</option>';
			} else {
				$data .= '<option value="'.$belanja->id_rekening.'">'.$belanja->kode_rekening.' - '.$belanja->uraian_rekening.'</option>';
			}
		}
		echo json_encode($data);
	}


	public function option_kegiatan_edit($id_kegiatan){
		$list = $this->kegiatan->get_by_user($this->id_user);
		$data = '<option value=""> - Pilih Kegiatan - </option>';
		foreach ($list as $kegiatan) {
			if ($id_kegiatan== $kegiatan->id_kegiatan) {
				$data .= '<option value="'.$kegiatan->id_kegiatan.'" selected="selected">'.$kegiatan->nama_kegiatan.'</option>';
			} else {
				$data .= '<option value="'.$kegiatan->id_kegiatan.'">'.$kegiatan->nama_kegiatan.'</option>';
			}
		}
		echo json_encode($data);
	}

}
