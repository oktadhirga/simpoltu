<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ls extends CI_Controller {
	public $id_user = null;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('kegiatan_model','kegiatan');
		$this->load->model('ls_model', 'ls');
		$this->load->model('ls_detail_model', 'ls_detail');
		$this->id_user = $this->session->userdata('id_user');
		$this->login_lib->cek_login();
		$this->login_lib->cek_admin();
	}

	public function index()
	{
		$data = array('title' => 'Halaman SPJ LS',
						  		'isi'  => 'bendahara/ls/list_ls',
									'foot' => 'bendahara/ls/foot_ls',
									'breadcrum1' => 'Data LS',
									'breadcrum2' => ''
								);

		$this->load->view('bendahara/layout/wrapper', $data);
	}

	public function ajax_list()
	{
		$jumlah_desimal ="0";
		$pemisah_desimal =",";
		$pemisah_ribuan =".";
		$list = $this->ls->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		foreach ($list as $ls) {
			if ($ls->id_user == $this->id_user) {

						$no++;
						$row = array();
						$row[] = $i;
						$row[] = $ls->nama_kegiatan;
						$row[] = $ls->no_ls;
						$row[] = date('d-m-Y', strtotime($ls->tgl_ls));
						$row[] = number_format($ls->nilai_ls, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan);
						$row[] = $ls->ket_ls;

						$row[] = '<a class="btn btn-sm btn-info" href="'.base_url('bendahara/ls/detail/').$ls->id_ls.'" title="Detail"><i class="glyphicon glyphicon-search"></i> Detail</a>
											<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_ls('."'".$ls->id_ls."'".')"><i class="glyphicon glyphicon-pencil"></i> </a>
										  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_ls('."'".$ls->id_ls."'".')"><i class="glyphicon glyphicon-trash"></i> </a>';

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

	public function ajax_edit($id_ls)
	{
		$data = $this->ls->get_by_id($id_ls);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		$data = array(
				'id_kegiatan' => $this->input->post('option_kegiatan'),
				'no_ls' => $this->input->post('no_ls'),
				'tgl_ls' => date('Y-m-d', strtotime($this->input->post('tgl_ls'))),
				'nilai_ls' => $this->input->post('nilai_ls'),
				'ket_ls' => $this->input->post('ket_ls'),
			);
		$insert = $this->ls->save($data);
		echo json_encode(array("status" => TRUE, "notif" => "LS berhasil ditambahkan"));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
				'id_kegiatan' => $this->input->post('option_kegiatan'),
				'no_ls' => $this->input->post('no_ls'),
				'tgl_ls' => date('Y-m-d', strtotime($this->input->post('tgl_ls'))),
				'nilai_ls' => $this->input->post('nilai_ls'),
				'ket_ls' => $this->input->post('ket_ls'),
			);
		$this->ls->update(array('id_ls' => $this->input->post('id_ls')), $data);
		echo json_encode(array("status" => TRUE, "notif" => "LS berhasil diedit"));
	}

	public function ajax_delete($id_ls)
	{
		$this->ls->delete_by_id($id_ls);
		echo json_encode(array("status" => TRUE));
	}


	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('option_kegiatan') == '')
		{
			$data['inputerror'][] = 'option_kegiatan';
			$data['error_string'][] = 'Nama kegiatan harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('no_ls') == '')
		{
			$data['inputerror'][] = 'no_ls';
			$data['error_string'][] = 'Nomor LS harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('tgl_ls') == '')
		{
			$data['inputerror'][] = 'tgl_ls';
			$data['error_string'][] = 'Tanggal LS harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('nilai_ls') == '')
		{
			$data['inputerror'][] = 'nilai_ls';
			$data['error_string'][] = 'Nilai LS harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('ket_ls') == '')
		{
			$data['inputerror'][] = 'ket_ls';
			$data['error_string'][] = 'Keterangan LS harus diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	public function option_kegiatan(){
		$list = $this->kegiatan->get_by_user($this->id_user);
		$data = '<option value=""> - Pilih Kegiatan - </option>';
		foreach ($list as $kegiatan) {
			$data .= '<option value="'.$kegiatan['id_kegiatan'].'">'.$kegiatan['nama_kegiatan'].'</option>';
		}
		echo json_encode($data);
	}

	public function option_kegiatan_edit($id_kegiatan){
		$list = $this->kegiatan->get_by_user($this->id_user);
		$data = '<option value=""> - Pilih Kegiatan - </option>';
		foreach ($list as $kegiatan) {
			if ($id_kegiatan== $kegiatan['id_kegiatan']) {
				$data .= '<option value="'.$kegiatan['id_kegiatan'].'" selected="selected">'.$kegiatan['nama_kegiatan'].'</option>';
			} else {
				$data .= '<option value="'.$kegiatan['id_kegiatan'].'">'.$kegiatan['nama_kegiatan'].'</option>';
			}
		}
		echo json_encode($data);
	}


	//LS - DETAIL
	public function detail($id_ls)
	{
		$ls = $this->ls->get_by_id($id_ls);
		$kegiatan = $this->kegiatan->get_by_id($ls->id_kegiatan);

		$data = array('title' => 'Halaman Detail LS',
						  		'isi'  => 'bendahara/ls_detail/list_ls_detail',
									'foot' => 'bendahara/ls_detail/foot_ls_detail',
									'breadcrum1' => 'Data LS',
									'breadcrum2' => 'Detail LS',
									'ls' => $ls,
									'kegiatan' => $kegiatan
								);

		$this->load->view('bendahara/layout/wrapper', $data);
	}


}
