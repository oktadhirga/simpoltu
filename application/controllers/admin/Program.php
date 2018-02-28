<?php
defined('BASEPATH') OR exit('No direct script access allowed');



class Program extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('program_model','program');
		$this->load->model('pengaturan_model','pengaturan');
		$this->login_lib->cek_login();
		$this->login_lib->cek_bendahara();
	}

	public function index()
	{
		$pengaturan = $this->pengaturan->listing();
		$data = array('title' => 'Halaman program',
						  		'isi'  => 'admin/program/list_program',
									'foot' => 'admin/program/foot_program',
									'breadcrum1' => 'Data Master',
									'breadcrum2' => 'Program',
									'pengaturan' => $pengaturan,
								);

		$this->load->view('admin/layout/wrapper', $data);
	}

	public function ajax_list()
	{
		$list = $this->program->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		$pengaturan = $this->pengaturan->listing();
		$rek_pd = $pengaturan[2]->nilai_pengaturan;
		foreach ($list as $program) {
			$no++;
			$row = array();
			$row[] = $i;
			$row[] = $program->nama_program;
			$row[] = '<span class="rekening">'.$rek_pd.' </span>'.$program->rekening_program;
			$row[] = $program->tahun;

			//add html for action
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_program('."'".$program->id_program."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_program('."'".$program->id_program."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';

			$data[] = $row;
			$i++;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->program->count_all(),
						"recordsFiltered" => $this->program->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id_program)
	{
		$data = $this->program->get_by_id($id_program);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		$data = array(
				'nama_program' => $this->input->post('nama_program'),
				'rekening_program' => $this->input->post('rekening_program'),
				'tahun' => $this->input->post('tahun'),
			);
		$insert = $this->program->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
				'nama_program' => $this->input->post('nama_program'),
				'rekening_program' => $this->input->post('rekening_program'),
				'tahun' => $this->input->post('tahun'),
			);
		$this->program->update(array('id_program' => $this->input->post('id_program')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id_program)
	{
		$this->program->delete_by_id($id_program);
		echo json_encode(array("status" => TRUE));
	}


	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('nama_program') == '')
		{
			$data['inputerror'][] = 'nama_program';
			$data['error_string'][] = 'Nama program harus diisi';
			$data['status'] = FALSE;
		}

		// if($this->input->post('rekening_program') == '')
		// {
		// 	$data['inputerror'][] = 'rekening_program';
		// 	$data['error_string'][] = 'Nama program harus diisi';
		// 	$data['status'] = FALSE;
		// }
		//
		// if($this->input->post('tahun') == '')
		// {
		// 	$data['inputerror'][] = 'tahun';
		// 	$data['error_string'][] = 'Tahun harus diisi';
		// 	$data['status'] = FALSE;
		// }


		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}


}
