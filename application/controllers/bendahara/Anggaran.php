<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Anggaran extends CI_Controller {
	public $id_user = null;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('kegiatan_model','kegiatan');
		$this->load->model('pengaturan_model', 'pengaturan');
		$this->load->model('rekening_model', 'rekening');
		$this->load->model('rekening_max_model', 'rekening_max');
		$this->id_user = $this->session->userdata('id_user');
		$this->login_lib->cek_login();
		$this->login_lib->cek_admin();
	}

	public function ajax_list($id_kegiatan)
	{
		$list = $this->rekening_max->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		foreach ($list as $anggaran) {
			if ($anggaran->id_kegiatan == $id_kegiatan) {

				$no++;
				$row = array();
				$row[] = $i;
				$row[] = $anggaran->kode_rekening;
				$row[] = $anggaran->uraian_rekening;
				$row[] = $this->money($anggaran->jumlah);
				$row[] = $anggaran->parent;


				//add html for action
				if ($anggaran->parent != 0) {
					$row[] = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_anggaran('."'".$anggaran->id_max."'".')"><i class="glyphicon glyphicon-trash"></i> Hapus</a>';
				} else {
					$row[] = '';
				}


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

	public function ajax_delete($id_max){

		//GET parent
		$list = $this->rekening_max->get_by_id($id_max);
		$parent = $this->rekening->get_parent($list->id_rekening);
		$this->rekening_max->delete_by_id($id_max);
		$total_anggaran = $this->rekening_max->sum_anggaran($parent->parent, $list->id_kegiatan);

		if ($total_anggaran->jumlah == 0) {
			$this->rekening_max->delete_by_2id($list->id_kegiatan, $parent->parent);
		} else {
			$data = array(
					'jumlah' => $total_anggaran->jumlah
				);
			$this->rekening_max->update(array('id_kegiatan' => $list->id_kegiatan, 'id_rekening' => $parent->parent), $data);
		}
		echo json_encode(array("status" => TRUE))
		;
	}

	private function money($money)
	{
		if ($money == 0 || $money == "") {
			return "-";
			} else {
			$jumlah_desimal ="0";
			$pemisah_desimal =",";
			$pemisah_ribuan =".";
			return number_format($money, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan);
		}
	}

}
