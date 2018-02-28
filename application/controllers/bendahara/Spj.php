<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Spj extends CI_Controller {
	public $id_user = null;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('kegiatan_model','kegiatan');
		$this->load->model('spj_model', 'spj');
		$this->load->model('panjar_model', 'panjar');
		$this->load->model('spj_detail_model', 'spj_detail');
		$this->load->model('pengaturan_model', 'pengaturan');
		$this->id_user = $this->session->userdata('id_user');
		$this->login_lib->cek_login();
		$this->login_lib->cek_admin();
	}

	// public function index()
	// {
	// 	$data = array('title' => 'Halaman Pengajuan SPJ',
	// 					  		'isi'  => 'bendahara/spj/list_spj',
	// 								'foot' => 'bendahara/spj/foot_spj',
	// 								'breadcrum1' => 'Data Panjar',
	// 								'breadcrum2' => 'Pengajuan SPJ'
	// 							);
	//
	// 	$this->load->view('bendahara/layout/wrapper', $data);
	// }

	public function ajax_list($id_panjar)
	{
		$jumlah_desimal ="0";
		$pemisah_desimal =",";
		$pemisah_ribuan =".";
		$list = $this->spj->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		foreach ($list as $spj) {
			if ($spj->id_panjar == $id_panjar) {
						$tgl_pengesahan = date('d-m-Y', strtotime($spj->tgl_pengesahan));
						$no++;
						$row = array();
						$row[] = $spj->no_spj;
						$row[] = date('d-m-Y', strtotime($spj->tgl_spj));
						$row[] = $spj->ket_spj;

						//validation isVerified
						if ($spj->isVerified == 0) {
									$row[] = '<span class="badge bg-grey">Belum Disahkan</span>';
									$row[] = '<a class="btn btn-sm btn-info" href="'.base_url('bendahara/spj/detail/').$spj->id_spj.'" title="Detail"><i class="glyphicon glyphicon-search"></i> Detail</a>
														<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_spj('."'".$spj->id_spj."'".')"><i class="glyphicon glyphicon-pencil"></i> </a>
										  			<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_spj('."'".$spj->id_spj."'".')"><i class="glyphicon glyphicon-trash"></i> </a>';
						} else {

									//add html for action
									$row[] = '<span class="badge bg-green">Sudah Disahkan<br> '.$tgl_pengesahan.'<br>'.$spj->no_pengesahan.'</span>';
									$row[] = '<a class="btn btn-sm btn-info" href="'.base_url('bendahara/spj/detail/').$spj->id_spj.'" title="Detail"><i class="glyphicon glyphicon-pencil"></i> Detail</a>
														<a class="btn btn-sm btn-primary disabled" href="javascript:void(0)" title="Edit"><i class="glyphicon glyphicon-pencil"></i> </a>
										  			<a class="btn btn-sm btn-danger disabled" href="javascript:void(0)" title="Hapus"><i class="glyphicon glyphicon-trash"></i> </a>
														<a target="_blank" class="btn btn-sm btn-success" href="'.base_url('bendahara/spj/cetak/').$spj->id_spj.'" title="Cetak"><i class="glyphicon glyphicon-print"></i></a>';
						}
						$data[] = $row;
						$i++;
			}
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->spj->count_all(),
						"recordsFiltered" => $this->spj->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id_spj)
	{
		$data = $this->spj->get_by_id($id_spj);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		$data = array(
				'id_panjar' => $this->input->post('id_panjar'),
				'no_spj' => $this->input->post('no_spj'),
				'tgl_spj' => date('Y-m-d', strtotime($this->input->post('tgl_spj'))),
				'ket_spj' => $this->input->post('ket_spj'),
			);
		$insert = $this->spj->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate();
		$data = array(
					'no_spj' => $this->input->post('no_spj'),
					'tgl_spj' => date('Y-m-d', strtotime($this->input->post('tgl_spj'))),
					'ket_spj' => $this->input->post('ket_spj'),
			);
		$this->spj->update(array('id_spj' => $this->input->post('id_spj')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id_spj)
	{
		$this->spj->delete_by_id($id_spj);
		echo json_encode(array("status" => TRUE));
	}


	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;


		if($this->input->post('no_spj') == '')
		{
			$data['inputerror'][] = 'no_spj';
			$data['error_string'][] = 'Nomor SPJ harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('tgl_spj') == '')
		{
			$data['inputerror'][] = 'tgl_spj';
			$data['error_string'][] = 'Tanggal SPJ harus diisi';
			$data['status'] = FALSE;
		}


		if($this->input->post('ket_spj') == '')
		{
			$data['inputerror'][] = 'ket_spj';
			$data['error_string'][] = 'Keterangan SPJ harus diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	public function detail($id_spj)
	{
		$spj_detail= $this->spj_detail->listing($id_spj);
		$list_program = $this->spj->list_program($id_spj);

		$data = array('title' => 'Halaman Detail SPJ',
						  		'isi'  => 'bendahara/spj_detail/list_spj_detail',
									'foot' => 'bendahara/spj_detail/foot_spj_detail',
									'breadcrum1' => 'Data Panjar',
									'breadcrum2' => 'Detail SPJ',
									'spj_detail' => $spj_detail,
									'list_program' => $list_program
								);

		$this->load->view('bendahara/layout/wrapper', $data);
	}

	public function cek_status($id_spj){
		$status = $this->spj->cek_status($id_spj);
		echo json_encode($status);
	}

	public function cetak($id_spj){
			ob_start();
			$spj = $this->spj->get_by_id($id_spj);
			$panjar = $this->panjar->get_by_id($spj->id_panjar);
			$kegiatan = $this->kegiatan->get_by_id($panjar->id_kegiatan);
			// $user = $this->user->get_by_id($this->id_user);
			$pengaturan = $this->pengaturan->listing();
			$sum_spj = $this->spj_detail->sum_spj($id_spj);

			$this->load->library("Pdf");
			$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$pdf->SetCreator(PDF_CREATOR);

			// set margins
			$pdf->setPrintHeader(FALSE);
			$pdf->SetMargins(25, 10, 25, TRUE);
			//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
			$judul = '<style>
				.judul-tengah {
					text-align : center;
					font-weight : bold;
					text-decoration : underline:
					text-transform : uppercase;
					font-size : 14px;
				}
				.kotak {
					border: 1px solid black;
				}
			</style>';
			$judul .= '<span class="judul-tengah"><br><u>KWITANSI</u></span><br><br>';
			$judul .= '<table border="0" cellpadding="2" width="100%">
								<tr>
									<td style="width:20%">Sudah Terima Dari</td>
									<td style="width:5%">:</td>
									<td style="width:75%">PENGGUNA ANGGARAN '.strtoupper($pengaturan[1]->nilai_pengaturan).'</td>
								</tr>
								<tr>
									<td>Jumlah Uang</td>
									<td>:</td>
									<td style="width:30%"><strong>Rp  '.$this->money($sum_spj->nilai_spj).' </strong></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
									<td style="width:75%"><div><strong>'.ucwords(strtolower($this->konversi->eja($sum_spj->nilai_spj))).' Rupiah</strong></div></td>
								</tr>
								<tr>
									<td>Keperluan</td>
									<td>:</td>
									<td>Pertanggungjawaban Ganti Uang Persediaan (GU) Nihil kegiatan :  '.$kegiatan->nama_kegiatan.'</td>
								</tr>
								</table><br>';
				$pagebreak = '<br pagebreak = "true">';

			// Add a page
			$pdf->AddPage();
			$pdf->SetFont('helvetica', 'n', 10);
			$pdf->writeHTML($judul, true, false, true, false, '');
			$pdf->SetFont('helvetica', 'n', 9);

			$foot_left = '<br>Setuju dibayar, <br>Kuasa Pengguna Anggaran<br><br><br><br><strong><u>'.$kegiatan->nama_kpa.'</u></strong><br>NIP. '.$kegiatan->nip_kpa;
			$foot_right = '<br>Yang Menerima, <br>Bendahara Pengeluaran<br><br><br><br><strong><u>'.$pengaturan[3]->nilai_pengaturan.'</u></strong><br>NIP. '.$pengaturan[4]->nilai_pengaturan;
			$foot_center = 'Mengetahui, <br>Kuasa Pengguna Anggaran<br><br><br><br><strong><u>'.$pengaturan[7]->nilai_pengaturan.'</u></strong><br>NIP. '.$pengaturan[8]->nilai_pengaturan;
		// 		TCPDF::writeHTMLCell 	($w, $h, $x, $y, $html = “, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = “, $autopadding = true)

			$pdf->Ln(10);
			$pdf->writeHTMLCell(55,40,20,'', $foot_left, 0, 0 , 0, true, 'C', true);
			$pdf->writeHTMLCell(55,40,120,'', $foot_right, 0, 0 , 0, true, 'C', true);
			$pdf->Ln(35);
			$pdf->writeHTMLCell(55,40,75,'', $foot_center, 0, 0 , 0, true, 'C', true);
			$pdf->writeHTMLCell(170,160,20,10, '', 1, 0 , 0, true, 'C', true);
			$pdf->Output('Kwitansi_GU.pdf', 'I');


	}

	private function money($money)
	{
		if ($money == 0 || $money == "") {
			return "-";
			} else {
			$jumlah_desimal ="2";
			$pemisah_desimal =",";
			$pemisah_ribuan =".";
			return number_format($money, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan);
		}
	}

	private function just_money($money)
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
