<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kegiatan extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('kegiatan_model','kegiatan');
		$this->load->model('program_model', 'program');
		$this->load->model('user_model', 'user');
		$this->load->model('pengaturan_model', 'pengaturan');
		$this->login_lib->cek_login();
		$this->login_lib->cek_bendahara();
	}

	// public function index()
	// {
	// 	$pengaturan = $this->pengaturan->listing();
	// 	$data = array('title' => 'Halaman kegiatan',
	// 					  		'isi'  => 'admin/kegiatan/list_kegiatan',
	// 								'foot' => 'admin/kegiatan/foot_kegiatan',
	// 								'breadcrum1' => 'Data Master',
	// 								'breadcrum2' => 'Kegiatan',
	// 								'pengaturan' => $pengaturan,
	// 							);
	//
	// 	$this->load->view('admin/layout/wrapper', $data);
	// }

	public function ajax_list($id_program)
	{
		$pengaturan = $this->pengaturan->listing();
		$list = $this->kegiatan->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		foreach ($list as $kegiatan) {
			if ($kegiatan->id_program == $id_program) {

					$row = array();
					$row[] = $i;
					$row[] = $kegiatan->nama_kegiatan;
					$row[] = $pengaturan[2]->nilai_pengaturan.''.$kegiatan->rekening_program.' . '.$kegiatan->rekening_kegiatan;
					$row[] = $kegiatan->nama_kpa;
					$row[] = $kegiatan->nama_pptk;
					$row[] = $kegiatan->nama_user;


					//add html for action
					$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_kegiatan('."'".$kegiatan->id_kegiatan."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
						  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_kegiatan('."'".$kegiatan->id_kegiatan."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';

					$data[] = $row;
					$i++;
			}
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->kegiatan->count_all(),
						"recordsFiltered" => $this->kegiatan->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id_kegiatan)
	{
		$data = $this->kegiatan->get_by_id($id_kegiatan);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		$data = array(
				'nama_kegiatan' => $this->input->post('nama_kegiatan'),
				'rekening_kegiatan' => $this->input->post('rekening_kegiatan'),
				'id_program' => $this->input->post('id_program'),
				'id_user' => $this->input->post('option_user'),
				'nama_kpa' => $this->input->post('nama_kpa'),
				'nip_kpa' => $this->input->post('nip_kpa'),
				'nama_pptk' => $this->input->post('nama_pptk'),
				'nip_pptk' => $this->input->post('nip_pptk'),
			);
		$insert = $this->kegiatan->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
				'nama_kegiatan' => $this->input->post('nama_kegiatan'),
				'rekening_kegiatan' => $this->input->post('rekening_kegiatan'),
				'id_user' => $this->input->post('option_user'),
				'nama_kpa' => $this->input->post('nama_kpa'),
				'nip_kpa' => $this->input->post('nip_kpa'),
				'nama_pptk' => $this->input->post('nama_pptk'),
				'nip_pptk' => $this->input->post('nip_pptk'),
			);
		$this->kegiatan->update(array('id_kegiatan' => $this->input->post('id_kegiatan')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id_kegiatan)
	{
		$this->kegiatan->delete_by_id($id_kegiatan);
		echo json_encode(array("status" => TRUE));
	}


	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('nama_kegiatan') == '')
		{
			$data['inputerror'][] = 'nama_kegiatan';
			$data['error_string'][] = 'Nama kegiatan harus diisi';
			$data['status'] = FALSE;
		}


		if($this->input->post('option_user') == '')
		{
			$data['inputerror'][] = 'option_user';
			$data['error_string'][] = 'Bendahara harus diisi';
			$data['status'] = FALSE;
		}


		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	public function option_program(){
		$list = $this->program->listing();
		$data = '<option value=""> - Pilih Program - </option>';
		foreach ($list as $program) {
			$data .= '<option value="'.$program->id_program.'">'.$program->nama_program.'</option>';
		}
		echo json_encode($data);
	}

	public function option_program_edit($id_program){
		$list = $this->program->listing();
		$data = '<option value=""> - Pilih Program - </option>';
		foreach ($list as $program) {
			if ($id_program== $program->id_program) {
				$data .= '<option value="'.$program->id_program.'" selected="selected">'.$program->nama_program.'</option>';
			} else {
				$data .= '<option value="'.$program->id_program.'">'.$program->nama_program.'</option>';
			}
		}
		echo json_encode($data);
	}

	public function option_user(){
	  $list = $this->user->listing();
	  $data = '<option value=""> - Pilih Bendahara - </option>';
	  foreach ($list as $user) {
	    $data .= '<option value="'.$user->id_user.'">'.$user->nama_user.'</option>';
	  }
	  echo json_encode($data);
	}

	public function option_user_edit($id_user){
	  $list = $this->user->listing();
	  $data = '<option value=""> - Pilih Bendahara - </option>';
	  foreach ($list as $user) {
	    if ($id_user== $user->id_user) {
	      $data .= '<option value="'.$user->id_user.'" selected="selected">'.$user->nama_user.'</option>';
	    } else {
	      $data .= '<option value="'.$user->id_user.'">'.$user->nama_user.'</option>';
	    }
	  }
	  echo json_encode($data);
	}

}
