<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bidang extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('bidang_model','bidang');
		$this->login_lib->cek_login();
		$this->login_lib->cek_bendahara();
	}

	public function index()
	{
		$data = array('title' => 'Halaman Bidang',
						  		'isi'  => 'admin/bidang/list_bidang',
									'foot' => 'admin/bidang/foot_bidang',
									'breadcrum1' => 'Data Master',
									'breadcrum2' => 'Bidang'
								);

		$this->load->view('admin/layout/wrapper', $data);
	}

	public function ajax_list()
	{
		$list = $this->bidang->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		foreach ($list as $bidang) {
			$no++;
			$row = array();
			$row[] = $i;
			$row[] = $bidang->nama_bidang;
			$row[] = $bidang->akronim;
			$row[] = $bidang->kepala_bidang;


			//add html for action
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_bidang('."'".$bidang->id_bidang."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_bidang('."'".$bidang->id_bidang."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';

			$data[] = $row;
			$i++;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->bidang->count_all(),
						"recordsFiltered" => $this->bidang->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id_bidang)
	{
		$data = $this->bidang->get_by_id($id_bidang);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		$data = array(
				'nama_bidang' => $this->input->post('nama_bidang'),
				'akronim' => $this->input->post('akronim'),
				'kepala_bidang' => $this->input->post('kepala_bidang'),
			);
		$insert = $this->bidang->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
				'nama_bidang' => $this->input->post('nama_bidang'),
				'akronim' => $this->input->post('akronim'),
				'kepala_bidang' => $this->input->post('kepala_bidang'),
			);
		$this->bidang->update(array('id_bidang' => $this->input->post('id_bidang')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id_bidang)
	{
		$this->bidang->delete_by_id($id_bidang);
		echo json_encode(array("status" => TRUE));
	}


	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('nama_bidang') == '')
		{
			$data['inputerror'][] = 'nama_bidang';
			$data['error_string'][] = 'Nama bidang harus diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

}
