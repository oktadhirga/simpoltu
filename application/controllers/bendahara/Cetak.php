<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cetak extends CI_Controller {
	public $id_user = null;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('table');
		$this->load->model('kegiatan_model','kegiatan');
		$this->load->model('rekening_model','rekening');
		$this->load->model('spj_model', 'spj');
		$this->load->model('spj_detail_model', 'spj_detail');
		$this->load->model('panjar_model', 'panjar');
		$this->id_user = $this->session->userdata('id_user');
		$this->login_lib->cek_login();
		$this->login_lib->cek_admin();
	}

	public function index()
	{
		$data = array('title' => 'Halaman Cetak',
						  		'isi'  => 'bendahara/cetak/list_cetak',
									'foot' => 'bendahara/cetak/foot_cetak',
									'breadcrum1' => 'Data Panjar',
									'breadcrum2' => 'SPJ Detail'
								);

		$this->load->view('bendahara/layout/wrapper', $data);
	}

	public function bku2(){
			$jumlah_desimal ="0";
			$pemisah_desimal =",";
			$pemisah_ribuan =".";
			$this->table->set_heading(array('Tanggal', 'Keterangan', 'Rekening', 'Pemasukan', 'Pengeluaran' , 'Sisa'));
			$list_header = $this->panjar->listing();
			foreach ($list_header as $header) {
					$this->table->add_row(date('d-m-Y', strtotime($header->tgl_bukti)), $header->ket_panjar, '' , number_format($header->nilai_panjar, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan),'', number_format($header->nilai_panjar, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan));
					//add detail
					$list_detail = $this->panjar->get_detail($header->id_panjar);
					$sisa = $header->nilai_panjar;
					foreach ($list_detail as $detail) {
						$sisa = $sisa - $detail->nilai_spj;
						$this->table->add_row(date('d-m-Y', strtotime($detail->tgl_spj)), $detail->uraian_rekening, $detail->kode_rekening , '' ,number_format($detail->nilai_spj, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan), number_format($sisa, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan));
					}
			}
			$template = array(
        									'table_open' => '<table id="tabel_bku" class="cell-border dt-center">'
											);
			$this->table->set_template($template);
			echo $this->table->generate();
	}


	public function bku(){
			$data = array();
			$list_header = $this->panjar->listing();
			foreach ($list_header as $header) {
				$row = array();
				$row[] = $header->tgl_bukti;
				$row[] = $header->nilai_panjar;
				$row[] = '';
				$row[] = $header->nilai_panjar;
					//add detail
					$list_detail = $this->panjar->get_detail($header->id_panjar);
					$sisa = $header->nilai_panjar;
					foreach ($list_detail as $detail) {
						$sisa = $sisa - $detail->nilai_spj;
						$row[] = $detail->tgl_spj;
						$row[] = '';
						$row[] = $detail->nilai_spj;
						$row[] = $sisa;
					}
			}
			$data[] = $row;
			echo json_encode($data);
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

						$no++;
						$row = array();
						$row[] = $spj_detail->id_spj_detail;
						$row[] = $i;
						$row[] = $spj_detail->kode_rekening;
						$row[] = $spj_detail->uraian_rekening;
						$row[] = number_format($spj_detail->nilai_spj, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan);
						$row[] = $spj_detail->no_spj_detail;
						$row[] = date('d-m-Y', strtotime($spj_detail->tgl_spj_detail));

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
		$data = array(
				'id_spj'		=> $this->input->post('id_spj'),
				'id_rekening' => $this->input->post('option_rekening'),
				'nilai_spj' => $this->input->post('nilai_spj'),
				'no_spj_detail' => $this->input->post('no_spj_detail'),
				'tgl_spj_detail' => date('Y-m-d', strtotime($this->input->post('tgl_spj_detail'))),
			);
		$insert = $this->spj_detail->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
				'id_rekening' => $this->input->post('option_rekening'),
				'nilai_spj' => $this->input->post('nilai_spj'),
				'no_spj_detail' => $this->input->post('no_spj_detail'),
				'tgl_spj_detail' => date('Y-m-d', strtotime($this->input->post('tgl_spj_detail'))),
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
			$data['inputerror'][] = 'option_rekening';
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

	public function option_rekening(){
		$list = $this->rekening->listing();
		$data = '<option selected="selected" value="" disabled> - Pilih Rekening - </option>';
		foreach ($list as $rekening) {
			$data .= '<option value="'.$rekening->id_rekening.'">'.$rekening->kode_rekening.' - '.$rekening->uraian_rekening.'</option>';
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

}
