<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ls_detail extends CI_Controller {
	public $id_user = null;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('kegiatan_model','kegiatan');
		$this->load->model('ls_model', 'ls');
		$this->load->model('ls_detail_model', 'ls_detail');
		$this->load->model('rekening_model', 'rekening');
		$this->id_user = $this->session->userdata('id_user');
		$this->login_lib->cek_login();
		$this->login_lib->cek_admin();
	}


	public function ajax_list($id_ls)
	{
		$jumlah_desimal ="0";
		$pemisah_desimal =",";
		$pemisah_ribuan =".";
		$list = $this->ls_detail->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		foreach ($list as $ls) {
			if ($ls->id_ls == $id_ls) {

						$no++;
						$row = array();
						$row[] = $ls->id_ls_detail;
						$row[] = $i;
						$row[] = $ls->kode_rekening;
						$row[] = $ls->uraian_rekening;
						$row[] = number_format($ls->nilai_ls_detail, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan);
						$row[] = $ls->ket_ls;

						$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_ls('."'".$ls->id_ls."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
										  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_ls('."'".$ls->id_ls."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';

						$data[] = $row;
						$i++;
			}
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->ls->count_all(),
						"recordsFiltered" => $this->ls->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function sum_ls($id_ls)
	{
		$data = $this->ls_detail->sum_ls($id_ls);
		echo json_encode($data->nilai_ls_detail);
	}


	public function ajax_edit($id_ls_detail)
	{
		$data = $this->ls_detail->get_by_id($id_ls_detail);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		$data = array(
				'id_ls' => $this->input->post('id_ls'),
				'nilai_ls_detail' => $this->input->post('nilai_ls_detail'),
				'tgl_ls_detail' => date('Y-m-d', strtotime($this->input->post('tgl_ls_detail'))),
				'ket_ls_detail' => $this->input->post('ket_ls_detail'),
				'id_rekening' => $this->input->post('option_rekening'),
			);
		$insert = $this->ls_detail->save($data);
		echo json_encode(array("status" => TRUE, "notif" => "LS berhasil ditambahkan"));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
			'nilai_ls_detail' => $this->input->post('nilai_ls_detail'),
			'tgl_ls_detail' => date('Y-m-d', strtotime($this->input->post('tgl_ls_detail'))),
			'ket_ls_detail' => $this->input->post('ket_ls_detail'),
			'id_rekening' => $this->input->post('option_rekening'),
			);
		$this->ls_detail->update(array('id_ls_detail' => $this->input->post('id_ls_detail')), $data);
		echo json_encode(array("status" => TRUE, "notif" => "LS berhasil diedit"));
	}

	public function ajax_delete($id_ls_detail)
	{
		$this->ls_detail->delete_by_id($id_ls_detail);
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
			$data['inputerror'][] = 'option_rekening';
			$data['error_string'][] = 'Rekening belanja harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('nilai_ls_detail') == '')
		{
			$data['inputerror'][] = 'nilai_ls_detail';
			$data['error_string'][] = 'Nilai LS harus diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	public function option_rekening(){
		$list = $this->rekening->listing();
		$data = '<option selected="selected" value="" disabled> - Pilih Rekening - </option>';
		foreach ($list as $rekening) {
			if ($rekening->parent != 0) {
				$data .= '<option value="'.$rekening->id_rekening.'">'.$rekening->kode_rekening.' - '.$rekening->uraian_rekening.'</option>';
			}
		}
		echo json_encode($data);
	}

	public function option_rekening_edit($id_rekening){
		$list = $this->rekening->listing();
		$data = '<option value="" disabled> - Pilih Kegiatan - </option>';
		foreach ($list as $rekening) {
			if ($rekening->parent != 0) {
				if ($id_rekening == $rekening->id_rekening) {
					$data .= '<option value="'.$rekening->id_rekening.'" selected="selected">'.$rekening->kode_rekening.' - '.$rekening->uraian_rekening.'</option>';
				} else {
					$data .= '<option value="'.$rekening->id_rekening.'">'.$rekening->kode_rekening.' - '.$rekening->uraian_rekening.'</option>';
				}
			}

		}
		echo json_encode($data);
	}

}
