<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kegiatan extends CI_Controller {
	public $id_user = null;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('kegiatan_model','kegiatan');
		$this->load->model('program_model', 'program');
		$this->load->model('user_model', 'user');
		$this->load->model('pengaturan_model', 'pengaturan');
		$this->load->model('rekening_model', 'rekening');
		$this->load->model('rekening_max_model', 'rekening_max');
		$this->id_user = $this->session->userdata('id_user');
		$this->tahun = $this->session->userdata('tahun');
		$this->login_lib->cek_login();
		$this->login_lib->cek_admin();
	}

	public function index()
	{
		$pengaturan = $this->pengaturan->listing();
		$data = array('title' => 'Halaman kegiatan',
						  		'isi'  => 'bendahara/kegiatan/list_kegiatan',
									'foot' => 'bendahara/kegiatan/foot_kegiatan',
									'breadcrum1' => 'Data Master',
									'breadcrum2' => 'Kegiatan',
									'pengaturan' => $pengaturan,
								);

		$this->load->view('bendahara/layout/wrapper', $data);
	}

	public function ajax_list()
	{
		$pengaturan = $this->pengaturan->listing();
		$list = $this->kegiatan->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		foreach ($list as $kegiatan) {
			if ($kegiatan->id_user == $this->id_user) {
				if ($kegiatan->tahun == $this->tahun) {



					$no++;
					$row = array();
					$row[] = $i;
					$row[] = $kegiatan->nama_kegiatan;
					$row[] = $kegiatan->nama_program;
					$row[] = $pengaturan[2]->nilai_pengaturan.''.$kegiatan->rekening_program.'.'.$kegiatan->rekening_kegiatan;
					$row[] = $kegiatan->nama_kpa;
					$row[] = $kegiatan->nama_pptk;


					//add html for action
					$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_kegiatan('."'".$kegiatan->id_kegiatan."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
										<a class="btn btn-sm btn-success" href="'.base_url('bendahara/kegiatan/anggaran/').$kegiatan->id_kegiatan.'" title="Edit Anggaran Belanja"><i class="glyphicon glyphicon-credit-card"></i> Angg. Belanja</a>';
					// $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_kegiatan('."'".$kegiatan->id_kegiatan."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
					// <a class="btn btn-sm btn-success" href="javascript:void(0)" title="Edit Rekening Belanja" onclick="edit_rekening('."'".$kegiatan->id_kegiatan."'".')"><i class="glyphicon glyphicon-credit-card"></i> Rek. Belanja</a>
					// <a class="btn btn-sm btn-success" href="'.base_url('bendahara/kegiatan/anggaran/').$kegiatan->id_kegiatan.'" title="Edit Anggaran Belanja"><i class="glyphicon glyphicon-credit-card"></i> Angg. Belanja</a>';

					$data[] = $row;
					$i++;
				}
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


	public function ajax_update()
	{
		$this->_validate();
		$data = array(
				'nama_kegiatan' => $this->input->post('nama_kegiatan'),
				'rekening_kegiatan' => $this->input->post('rekening_kegiatan'),
				'nama_kpa' => $this->input->post('nama_kpa'),
				'nip_kpa' => $this->input->post('nip_kpa'),
				'nama_pptk' => $this->input->post('nama_pptk'),
				'nip_pptk' => $this->input->post('nip_pptk'),
			);
		$this->kegiatan->update(array('id_kegiatan' => $this->input->post('id_kegiatan')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit_rekening($id_kegiatan)
	{
		$data = $this->kegiatan->get_by_id($id_kegiatan);
		echo json_encode($data);
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

		if($this->input->post('rekening_kegiatan') == '')
		{
			$data['inputerror'][] = 'rekening_kegiatan';
			$data['error_string'][] = 'Rekening kegiatan harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('nama_kpa') == '')
		{
			$data['inputerror'][] = 'nama_kpa';
			$data['error_string'][] = 'Nama KPA harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('nip_kpa') == '')
		{
			$data['inputerror'][] = 'nip_kpa';
			$data['error_string'][] = 'NIP KPA harus diisi';
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

	public function get_rekening_max($id_kegiatan, $id_user){
		$data = $this->rekening_max->get_by_2id($id_kegiatan, $id_user);
		if (!$data) {
				$data = array('jumlah' => 0, 'id_max' => null);
		}
		 echo json_encode($data);

	}

	public function ajax_max_add(){
		$id_kegiatan = $this->input->post('id_kegiatan');
		$id_rekening = $this->input->post('option_rekening');
		$data = array(
				'id_kegiatan' => $id_kegiatan,
				'id_rekening' => $id_rekening,
				'jumlah' => $this->input->post('jumlah_anggaran'),
			);
		$insert = $this->rekening_max->save($data);

		// GET PARENT
		$parent = $this->rekening->get_parent($id_rekening);
		$list = $this->rekening_max->get_by_2id($id_kegiatan, $parent->parent);
		$total_anggaran = $this->rekening_max->sum_anggaran($parent->parent, $id_kegiatan);
		if (!$list) {
			$data = array(
					'id_kegiatan' => $id_kegiatan,
					'id_rekening' => $parent->parent,
					'jumlah' => $total_anggaran->jumlah,
				);
			$this->rekening_max->save($data);
		} else {
			$data = array(
					'id_kegiatan' => $id_kegiatan,
					'id_rekening' => $parent->parent,
					'jumlah' => $total_anggaran->jumlah,
				);
			$this->rekening_max->update(array('id_max' => $list->id_max), $data);
		}

		echo json_encode(array("status" => TRUE, "notif" => "Data Berhasil Disimpan"));
	}

	public function ajax_max_update(){
		$id_kegiatan = $this->input->post('id_kegiatan');
		$id_rekening = $this->input->post('option_rekening');
		$data = array(
				'id_kegiatan' => $id_kegiatan,
				'id_rekening' => $id_rekening,
				'jumlah' => $this->input->post('jumlah_anggaran')
			);
		$this->rekening_max->update(array('id_max' => $this->input->post('id_max')), $data);

		// GET PARENT
		$parent = $this->rekening->get_parent($id_rekening);
		$list = $this->rekening_max->get_by_2id($id_kegiatan, $parent->parent);
		$total_anggaran = $this->rekening_max->sum_anggaran($parent->parent, $id_kegiatan);
		if (!$list) {
			$data = array(
					'id_kegiatan' => $id_kegiatan,
					'id_rekening' => $parent->parent,
					'jumlah' => $total_anggaran->jumlah,
				);
			$this->rekening_max->save($data);
		} else {
			$data = array(
					'id_kegiatan' => $id_kegiatan,
					'id_rekening' => $parent->parent,
					'jumlah' => $total_anggaran->jumlah,
				);
			$this->rekening_max->update(array('id_max' => $list->id_max), $data);
		}
		echo json_encode(array("status" => TRUE, "notif" => "Data Berhasil Disimpan"));
	}

	public function anggaran($id_kegiatan)
	{

		$list_kegiatan = $this->kegiatan->get_by_id($id_kegiatan);

		$data = array('title' => 'Halaman Anggaran Belanja',
									'isi'  => 'bendahara/anggaran/list_anggaran',
									'foot' => 'bendahara/anggaran/foot_anggaran',
									'breadcrum1' => 'Data Kegiatan',
									'breadcrum2' => 'Anggaran Belanja',
									'list_kegiatan' => $list_kegiatan
								);

		$this->load->view('bendahara/layout/wrapper', $data);
	}

}
