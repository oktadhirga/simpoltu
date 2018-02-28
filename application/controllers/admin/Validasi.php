<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Validasi extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('kegiatan_model','kegiatan');
		$this->load->model('program_model','program');
		$this->load->model('validasi_model', 'validasi');
		$this->login_lib->cek_login();
		$this->login_lib->cek_bendahara();
	}

	public function index()
	{
		$data = array('title' => 'Halaman Pengesahan Panjar',
						  		'isi'  => 'admin/validasi/list_validasi',
									'foot' => 'admin/validasi/foot_validasi',
									'breadcrum1' => 'Data Panjar',
									'breadcrum2' => 'Validasi Panjar'
								);

		$this->load->view('admin/layout/wrapper', $data);
	}

	public function ajax_list()
	{
		$jumlah_desimal ="0";
		$pemisah_desimal =",";
		$pemisah_ribuan =".";
		$list = $this->validasi->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		foreach ($list as $panjar) {


						$no++;
						$row = array();
						$row[] = $i;
						$row[] = $panjar->nama_kegiatan;
						$row[] = $panjar->no_bukti;
						$row[] = date('d-m-Y', strtotime($panjar->tgl_bukti));
						$row[] = number_format($panjar->nilai_panjar, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan);
						$row[] = $panjar->ket_panjar;
						$row[] = $panjar->nama_user;
						$row[] = $panjar->isVerified;
						$row[] = '<a class="btn btn-sm btn-success" href="'.base_url('admin/validasi/spj/').$panjar->id_panjar.'" title="spj" ><i class="glyphicon glyphicon-usd"></i> SPJ</a>
											<a class="btn btn-sm btn-info" href="'.base_url('admin/validasi/pengembalian/').$panjar->id_panjar.'" title="pengembalian" ><i class="glyphicon glyphicon-refresh"></i> </a>
											<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_panjar('."'".$panjar->id_panjar."'".')"><i class="glyphicon glyphicon-pencil"></i> </a>
										  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_panjar('."'".$panjar->id_panjar."'".')"><i class="glyphicon glyphicon-trash"></i> </a>';

						$data[] = $row;
						$i++;

		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->validasi->count_all(),
						"recordsFiltered" => $this->validasi->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_list_filter($id_program)
	{
		$jumlah_desimal ="0";
		$pemisah_desimal =",";
		$pemisah_ribuan =".";
		$list = $this->validasi->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		foreach ($list as $panjar) {
			if ($panjar->id_program == $id_program) {

						$no++;
						$row = array();
						$row[] = $i;
						$row[] = $panjar->nama_kegiatan;
						$row[] = $panjar->no_bukti;
						$row[] = date('d-m-Y', strtotime($panjar->tgl_bukti));
						$row[] = number_format($panjar->nilai_panjar, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan);
						$row[] = $panjar->ket_panjar;
						$row[] = $panjar->nama_user;
						$row[] = $panjar->isVerified;
						$row[] = '<a class="btn btn-sm btn-success" href="'.base_url('admin/validasi/spj/').$panjar->id_panjar.'" title="spj" ><i class="glyphicon glyphicon-usd"></i> SPJ</a>
											<a class="btn btn-sm btn-info" href="'.base_url('admin/validasi/pengembalian/').$panjar->id_panjar.'" title="pengembalian" ><i class="glyphicon glyphicon-refresh"></i> </a>
											<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_panjar('."'".$panjar->id_panjar."'".')"><i class="glyphicon glyphicon-pencil"></i> </a>
										  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_panjar('."'".$panjar->id_panjar."'".')"><i class="glyphicon glyphicon-trash"></i> </a>';

						$data[] = $row;
						$i++;

			}

		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->validasi->count_all(),
						"recordsFiltered" => $this->validasi->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}


	public function ajax_edit($id_panjar)
	{
		$data = $this->validasi->get_by_id($id_panjar);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		$data = array(
				'id_kegiatan' => $this->input->post('option_kegiatan'),
				'no_bukti' => $this->input->post('no_bukti'),
				'tgl_bukti' => date('Y-m-d', strtotime($this->input->post('tgl_bukti'))),
				'nilai_panjar' => $this->input->post('nilai_panjar'),
				'ket_panjar' => $this->input->post('ket_panjar'),
			);
		$insert = $this->validasi->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
				'id_kegiatan' => $this->input->post('option_kegiatan'),
				'no_bukti' => $this->input->post('no_bukti'),
				'tgl_bukti' => date('Y-m-d', strtotime($this->input->post('tgl_bukti'))),
				'nilai_panjar' => $this->input->post('nilai_panjar'),
				'ket_panjar' => $this->input->post('ket_panjar'),
			);
		$this->validasi->update(array('id_panjar' => $this->input->post('id_panjar')), $data);
		echo json_encode(array("status" => TRUE));
	}


	public function ajax_delete($id_panjar)
	{
		$this->validasi->delete_by_id($id_panjar);
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

		if($this->input->post('no_bukti') == '')
		{
			$data['inputerror'][] = 'no_bukti';
			$data['error_string'][] = 'Nomor bukti harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('tgl_bukti') == '')
		{
			$data['inputerror'][] = 'tgl_bukti';
			$data['error_string'][] = 'Tanggal Bukti harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('nilai_panjar') == '')
		{
			$data['inputerror'][] = 'nilai_panjar';
			$data['error_string'][] = 'Nilai panjar harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('ket_panjar') == '')
		{
			$data['inputerror'][] = 'ket_panjar';
			$data['error_string'][] = 'Keterangan panjar harus diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	public function option_kegiatan(){
		$list = $this->kegiatan->all_list();
		$data = '<option value=""> - Pilih Kegiatan - </option>';
		foreach ($list as $kegiatan) {
			$data .= '<option value="'.$kegiatan->id_kegiatan.'">'.$kegiatan->nama_kegiatan.'</option>';
		}
		echo json_encode($data);
	}

	public function option_kegiatan_edit($id_kegiatan){
		$list = $this->kegiatan->all_list();
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

	public function option_program(){
		$list = $this->program->listing();
		$data = '<option value="-1"> - Semua Program - </option>';
		foreach ($list as $program) {
			$data .= '<option value="'.$program->id_program.'">'.$program->nama_program.'</option>';
		}
		echo json_encode($data);
	}

	public function list_program($id_panjar){
		$list_program = $this->validasi->list_program($id_panjar);
		echo json_encode($list_program);
	}

	public function spj($id_panjar){
		$data = array('title' => 'Halaman Detail SPJ',
									'isi'  => 'admin/spjadmin/list_spjadmin',
									'foot' => 'admin/spjadmin/foot_spjadmin',
									'breadcrum1' => 'Data Panjar',
									'breadcrum2' => 'Detail SPJ',
									'id_panjar' => $id_panjar
								);

		$this->load->view('admin/layout/wrapper', $data);
	}

	public function pengembalian($id_panjar){

		$data = array('title' => 'Halaman Pengembalian',
									'isi'  => 'admin/pengembalian/list_pengembalian',
									'foot' => 'admin/pengembalian/foot_pengembalian',
									'breadcrum1' => 'Data Panjar',
									'breadcrum2' => 'Pengembalian',
									'id_panjar' => $id_panjar
								);

		$this->load->view('admin/layout/wrapper', $data);
	}

}
