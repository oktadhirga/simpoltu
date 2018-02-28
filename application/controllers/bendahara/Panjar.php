<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Panjar extends CI_Controller {
	public $id_user = null;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('kegiatan_model','kegiatan');
		$this->load->model('panjar_model', 'panjar');
		$this->load->model('panjar_rinci_model', 'panjar_rinci');
		$this->load->model('spj_model', 'spj');
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
		$data = array('title' => 'Halaman Pengajuan Panjar',
						  		'isi'  => 'bendahara/panjar/list_panjar',
									'foot' => 'bendahara/panjar/foot_panjar',
									'breadcrum1' => 'Data Panjar',
									'breadcrum2' => 'Pengajuan Panjar'
								);

		$this->load->view('bendahara/layout/wrapper', $data);
	}

	public function ajax_list()
	{
		$jumlah_desimal ="0";
		$pemisah_desimal =",";
		$pemisah_ribuan =".";
		$list = $this->panjar->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		foreach ($list as $panjar) {
			if ($panjar->id_user == $this->id_user) {
				if ($panjar->tahun == $this->tahun) {


						//cek in spj
						$status = $this->spj->get_status($panjar->id_panjar);

						$no++;
						$row = array();
						$row[] = $i;
						$row[] = $panjar->nama_kegiatan;
						$row[] = $panjar->no_bukti;
						$row[] = date('d-m-Y', strtotime($panjar->tgl_bukti));
						$row[] = number_format($panjar->nilai_panjar, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan);
						$row[] = $panjar->ket_panjar;
						if ($status) {
							$row[] = $status->isVerified;
						} else {
							$row[] = 0;
						}

						$row[] = '<a class="btn btn-sm btn-success" href="'.base_url('bendahara/panjar/spj/').$panjar->id_panjar.'" title="spj" ><i class="glyphicon glyphicon-usd"></i> SPJ</a>
											<a class="btn btn-sm btn-info" href="'.base_url('bendahara/panjar/pengembalian/').$panjar->id_panjar.'" title="Pengembalian" ><i class="glyphicon glyphicon-repeat"></i> </a>
											<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_panjar('."'".$panjar->id_panjar."'".')"><i class="glyphicon glyphicon-pencil"></i></a>
										  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_panjar('."'".$panjar->id_panjar."'".')"><i class="glyphicon glyphicon-trash"></i></a>
											<a target="_blank" class="btn btn-sm btn-default" href="'.base_url('bendahara/panjar/cetak/').$panjar->id_panjar.'" title="Cetak" ><i class="glyphicon glyphicon-print"></i> </a>';

						$data[] = $row;
						$i++;
					}
			}
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->panjar->count_all(),
						"recordsFiltered" => $this->panjar->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id_panjar)
	{
		$data = $this->panjar->get_by_id($id_panjar);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate();
		$id_panjar = str_shuffle(uniqid());
		$data = array(
				'id_panjar' => $id_panjar,
				'id_kegiatan' => $this->input->post('option_kegiatan'),
				'no_bukti' => $this->input->post('no_bukti'),
				'tgl_bukti' => date('Y-m-d', strtotime($this->input->post('tgl_bukti'))),
				'nilai_panjar' => $this->input->post('nilai_panjar'),
				'ket_panjar' => $this->input->post('ket_panjar'),
			);
		$insert = $this->panjar->save($data);

		if ($this->input->post('option_jenis_belanja')) {
			$option_jenis_belanja = $this->input->post('option_jenis_belanja', true);
			$jum_jenis_belanja =  $this->input->post('jum_jenis_belanja', true);
			$action_jenis_belanja =  $this->input->post('action_jenis_belanja', true);

			foreach ($option_jenis_belanja as $i => $option) {
				if ($option != 0 || $jum_jenis_belanja[$i] != 0 || $action_jenis_belanja[$i] != '' ) {

					$data_rinci = array(
														'id_panjar' => $id_panjar,
														'id_rekening' => $option,
														'jumlah' => $jum_jenis_belanja[$i],
														'pelaksanaan' => $action_jenis_belanja[$i],
												);

					$this->db->insert('panjar_rinci', $data_rinci);
				}
			}
		}

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
		$this->panjar->update(array('id_panjar' => $this->input->post('id_panjar')), $data);



		if ($this->input->post('option_jenis_belanja')) {
			$this->panjar_rinci->delete_by_id_panjar($this->input->post('id_panjar'));
			$option_jenis_belanja = $this->input->post('option_jenis_belanja', true);
			$jum_jenis_belanja =  $this->input->post('jum_jenis_belanja', true);
			$action_jenis_belanja =  $this->input->post('action_jenis_belanja', true);

			foreach ($option_jenis_belanja as $i => $option) {
				if ($option != 0 || $jum_jenis_belanja[$i] != 0 || $action_jenis_belanja[$i] != '') {

					$data_rinci = array(
														'id_panjar' => $this->input->post('id_panjar'),
														'id_rekening' => $option,
														'jumlah' => $jum_jenis_belanja[$i],
														'pelaksanaan' =>$action_jenis_belanja[$i],
												);

					$this->db->insert('panjar_rinci', $data_rinci);
				}
			}
		}


		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id_panjar)
	{
		$this->panjar->delete_by_id($id_panjar);
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

	public function spj($id_panjar){
		$panjar = $this->panjar->get_by_id($id_panjar);
		$kegiatan = $this->kegiatan->get_by_id($panjar->id_kegiatan);

		$data = array('title' => 'Halaman SPJ',
									'isi'  => 'bendahara/spj/list_spj',
									'foot' => 'bendahara/spj/foot_spj',
									'breadcrum1' => 'Data Panjar',
									'breadcrum2' => 'SPJ',
									'id_panjar' => $id_panjar,
									'nama_kegiatan' => $kegiatan->nama_kegiatan,
									'no_bukti' => $panjar->no_bukti
								);

		$this->load->view('bendahara/layout/wrapper', $data);
	}

	public function pengembalian($id_panjar){
		$panjar = $this->panjar->get_by_id($id_panjar);
		$kegiatan = $this->kegiatan->get_by_id($panjar->id_kegiatan);

		$data = array('title' => 'Halaman Pengembalian',
									'isi'  => 'bendahara/pengembalian/list_pengembalian',
									'foot' => 'bendahara/pengembalian/foot_pengembalian',
									'breadcrum1' => 'Data Panjar',
									'breadcrum2' => 'Pengembalian',
									'id_panjar' => $id_panjar,
									'nama_kegiatan' => $kegiatan->nama_kegiatan,
									'no_bukti' => $panjar->no_bukti
								);

		$this->load->view('bendahara/layout/wrapper', $data);
	}

	public function get_rinci_panjar($id_panjar, $i){
		$this->db->from('panjar_rinci');
		$this->db->where('id_panjar', $id_panjar);
		$this->db->order_by('id_rekening', 'ASC');
		$query = $this->db->get();
		$rinci = $query->row($i);
		echo json_encode($rinci);
	}

	public function count_rinci_panjar($id_panjar){
		$this->db->from('panjar_rinci');
		$this->db->where('id_panjar', $id_panjar);
		$query = $this->db->get();
		$count = count($query->result());
		echo json_encode($count);
	}

	public function cetak($id_panjar){
		ob_start();
		$panjar = $this->panjar->get_by_id($id_panjar);
		$kegiatan = $this->kegiatan->get_by_id($panjar->id_kegiatan);
		$user = $this->user->get_by_id($this->id_user);
		$pengaturan = $this->pengaturan->listing();

		$tgl_bukti = date('Y-m-d', strtotime($panjar->tglbukti));
		$year = date('Y', strtotime($tgl_bukti));
		$month = date('n', strtotime($tgl_bukti));
		$day = date('d', strtotime($tgl_bukti));
		$monthArray = array('noMonth', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
		$tanggal = $day.' '.$monthArray[$month].' '.$year;

		$this->load->library("Pdf");
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);

		// set margins
		$pdf->setPrintHeader(FALSE);
		$pdf->SetMargins(25, 10, 25, TRUE);
		//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$judul = '<style>
			.judul {
				text-align : center;
				font-weight : bold;
				text-transform : uppercase;
				font-size : 10px;
			}
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
		$judul .= '<span class="judul-tengah"><br>KWITANSI</span><br><br>';
		$judul .= '<table border="0" cellpadding="2" width="100%">
							<tr>
								<td style="width:20%">Sudah Terima Dari</td>
								<td style="width:5%">:</td>
								<td style="width:75%">Bendahara Pengeluaran '.$pengaturan[1]->nilai_pengaturan.'</td>
							</tr>
							<tr>
								<td>Jumlah Uang</td>
								<td>:</td>
								<td class = "kotak" style="width:30%"> Rp  '.$this->money($panjar->nilai_panjar).'</td>
							</tr>
							<tr>
								<td></td>
								<td></td>
								<td style="width:75%"><div>Terbilang <strong>'.strtoupper($this->konversi->eja($panjar->nilai_panjar)).' RUPIAH</strong></div></td>
							</tr>
							<tr>
								<td>Keperluan</td>
								<td>:</td>
								<td>Uang panjar kegiatan :  '.$kegiatan->nama_kegiatan.'</td>
							</tr>
							</table><br>';
			$pagebreak = '<br pagebreak = "true">';

		// Add a page
		$pdf->AddPage();
		$pdf->SetFont('helvetica', 'n', 10);
		$pdf->writeHTML($judul, true, false, true, false, '');
		$pdf->SetFont('helvetica', 'n', 9);

		$foot_left = '<br><br>Dibayar Lunas, <br>Bendahara Pengeluaran <br><br><br><br><strong><u>'.$pengaturan[3]->nilai_pengaturan.'</u></strong><br>NIP. '.$pengaturan[4]->nilai_pengaturan;
		$foot_right = 'Trenggalek, '.$tanggal.' <br>Yang Menerima, <br> Bend. Pengeluaran Pembantu <br><br><br><br><strong><u>'.$user->nama_user.'</u></strong><br>NIP. '.$user->nip_user;
	// 		TCPDF::writeHTMLCell 	($w, $h, $x, $y, $html = “, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = “, $autopadding = true)

	  $foot_left2 = '<br><br>Mengetahui, <br>Kuasa Pengguna Anggaran <br><br><br><br><strong><u>'.$kegiatan->nama_kpa.'</u></strong><br>NIP. '.$kegiatan->nip_kpa;
		$foot_right2 = '<br><br><br>P P T K<br><br><br><br><strong><u>'.$kegiatan->nama_pptk.'</u></strong><br>NIP. '.$kegiatan->nip_pptk;

		$pdf->Ln(10);
		$pdf->writeHTMLCell(55,40,20,'', $foot_left, 0, 0 , 0, true, 'C', true);
		$pdf->writeHTMLCell(55,40,120,'', $foot_right, 0, 0 , 0, true, 'C', true);
		$pdf->Ln(35);
		$pdf->writeHTMLCell(55,40,20,'', $foot_left2, 0, 0 , 0, true, 'C', true);
		$pdf->writeHTMLCell(55,40,120,'', $foot_right2, 0, 0 , 0, true, 'C', true);
		$pdf->writeHTMLCell(170,160,20,10, '', 1, 0 , 0, true, 'C', true);
		$pdf->writeHTML($pagebreak, true, false, true, false, '');


		//PAGE 2
		$judul2 = '<style>
			.judul {
				text-align : center;
				font-weight : bold;
				text-transform : uppercase;
				font-size : 12px;
				margin-left: 200px;
			}
			.judul-tengah {
				text-align : center;
				font-weight : bold;
				text-transform : uppercase;
				font-size : 14px;
			}
		</style>';
		// $pdf->SetMargins(40, 10, 25, TRUE);
		$judul2 .= '<span class="judul"><br>'.$pengaturan[0]->nilai_pengaturan.'</span><br>';
		$judul2 .= '<span class="judul-tengah">'.$pengaturan[1]->nilai_pengaturan.'</span><br>';
		$judul2 .= '<span style="text-align:center; font-size: 10px">'.$pengaturan[5]->nilai_pengaturan.'</span><br>';
		$judul2 .= '<span class="judul">'.$pengaturan[6]->nilai_pengaturan.'</span><br><br>';
		$judul2 .= '<div class="judul"><u>NOTA PERMINTAAN PEMBAYARAN UANG PANJAR</u></div>';
		$judul2 .= '<span style="text-align:center">Nomor : '.$panjar->no_bukti.'</span><br><br><br>';
		$tabel2 = '<style>
									table {border:1px solid #000}
									td,th {border:none}
									td.center {text-align:center}
									.right {text-align:right}
							</style>';
		$tabel2 .= '<table frame="box" rules="none" cellpadding="2" width="100%">
								<tr>
									<td>Kepada <br>BENDAHARA PENGELUARAN <br>'.$pengaturan[1]->nilai_pengaturan.'<br>supaya membayar Uang Panjar kepada</td>
								</tr></table>';
		$tabel2 .= '<table><tr><td></td></tr>
								<tr>
									<td style="width:5%">1.</td>
									<td style="width:35%">Bendahara Pengeluaran Pembantu</td>
									<td style="width:5%">:</td>
									<td style="width:55%">'.$user->nama_user.'</td>
								</tr>';
		$tabel2 .= '<tr>
									<td>2.</td>
									<td>Kegiatan</td>
									<td>:</td>
									<td>'.$kegiatan->nama_kegiatan.'</td>
								</tr>';
		$tabel2 .= '<tr>
									<td>3.</td>
									<td>Jumlah Pembayaran yang Diminta</td>
									<td>:</td>
									<td>Rp '.$this->money($panjar->nilai_panjar).'</td>
								</tr>';
		$tabel2 .= '<tr>
									<td></td>
									<td></td>
									<td></td>
									<td><strong>'.strtoupper($this->konversi->eja($panjar->nilai_panjar)).' RUPIAH</strong></td>
								</tr>';
		$tabel2 .= '<tr><td></td></tr></table>';
		$tabel2 .= '<table width="100%" border="1"><tr><td align="center">Pembebanan pada kode rekening</td></tr></table>';
		$tabel2 .= '<table width="100%" border="1">
									<tr>
										<td style="width:6%" class="center">No. Urut</td>
										<td style="width:10%" class="center">Kode Rekening</td>
										<td style="width:20%" class="center">Uraian</td>
										<td style="width:16%" class="center">Anggaran</td>
										<td style="width:16%" class="center">Akm. Pencairan Sebelumnya</td>
										<td style="width:16%" class="center">Pengajuan saat ini</td>
										<td style="width:16%" class="center">Sisa</td>
									</tr>';
		$tabel2 .= '<tr>
										<td class="center">1</td>
										<td class="center">2</td>
										<td class="center">3</td>
										<td class="center">4</td>
										<td class="center">5</td>
										<td class="center">6</td>
										<td class="center">7 = 4-(5+6)</td>
									</tr>';


		$jenis_belanja = $this->rekening->get_jenis();
		$i = 1;
		$totalJumlahAnggaran = $totalSumBefore = $totalSumNow = $totalSisa = 0;
		foreach ($jenis_belanja as $jenis_belanja) {
			 	$sisa = 0;
				$listBefore = $this->panjar->list_panjar_rinci_sebelumnya($kegiatan->id_kegiatan, $jenis_belanja->id_rekening, $tgl_bukti);
				if ($listBefore) {
					$sumBefore = $listBefore->sum_jumlah_rinci;
				} else {
					$sumBefore = 0;
				}
				$belanja = $this->rekening->get_by_id($jenis_belanja->id_rekening);
				$anggaran = $this->rekening_max->get_by_2id($kegiatan->id_kegiatan, $jenis_belanja->id_rekening);
				if ($anggaran) {
					$jumlahAnggaran = $anggaran->jumlah;
				} else {
					$jumlahAnggaran = 0;
				}
				$listNow = $this->panjar->list_panjar_rinci_now($panjar->id_panjar, $jenis_belanja->id_rekening);
				if ($listNow) {
					$sumNow = $listNow->sum_jumlah_rinci;
				} else {
					$sumNow = 0;
				}
				$sisa = $jumlahAnggaran - ($sumBefore + $sumNow);
				$tabel2 .= '<tr>
												<td class="center">'.$i.'</td>
												<td class="center">'.$belanja->kode_rekening.'</td>
												<td class="">'.$belanja->uraian_rekening.'</td>
												<td class="right">'.$this->just_money($jumlahAnggaran).'</td>
												<td class="right">'.$this->just_money($sumBefore).'</td>
												<td class="right">'.$this->just_money($sumNow).'</td>
												<td class="right">'.$this->just_money($sisa).'</td>
											</tr>';
				$i++;
				$totalJumlahAnggaran += $jumlahAnggaran;
				$totalSumBefore += $sumBefore;
				$totalSumNow += $sumNow;
				$totalSisa += $sisa;
		}
		$tabel2 .= '<tr>
										<td class="center" colspan="3">JUMLAH</td>
										<td class="right">'.$this->just_money($totalJumlahAnggaran).'</td>
										<td class="right">'.$this->just_money($totalSumBefore).'</td>
										<td class="right">'.$this->just_money($totalSumNow).'</td>
										<td class="right">'.$this->just_money($totalSisa).'</td>
									</tr>';
		$tabel2 .= '</table>';

		$foot_left2 = '<br><br><br>Pejabat Pelaksana Teknis Kegiatan <br><br><br><br><strong><u>'.$kegiatan->nama_pptk.'</u></strong><br>NIP. '.$kegiatan->nip_pptk;
		$foot_right2 = 'Trenggalek, '.$tanggal.' <br><br> Bendahara Pengeluaran Pembantu <br><br><br><br><strong><u>'.$user->nama_user.'</u></strong><br>NIP. '.$user->nip_user;
		$foot_center2 = 'Mengetahui, <br>Kuasa Pengguna Anggaran<br><br><br><br><strong><u>'.$kegiatan->nama_kpa.'</u></strong><br>NIP. '.$kegiatan->nip_kpa;
		$doc = 'C.9a';
		// set color for text
		$pdf->SetTextColor(255, 255, 255);
		$pdf->writeHTMLCell('','',170,12, $doc, 0, 0 , 1, true, 'C', true);
		$pdf->Ln(1);
		$pdf->SetTextColor(0, 0, 0);
		// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
		$imageloc = base_url('assets/dist/img/logo_trenggalek_bw.png');
		$pdf->Image($imageloc, 25, 17, 18, 21, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
		$pdf->writeHTML($judul2, true, false, true, false, '');
		$pdf->writeHTML($tabel2, true, false, true, false, '');
	// 		TCPDF::writeHTMLCell 	($w, $h, $x, $y, $html = “, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = “, $autopadding = true)

		$pdf->Ln(10);
		$pdf->writeHTMLCell(55,40,25,'', $foot_left2, 0, 0 , 0, true, 'C', true);
		$pdf->writeHTMLCell(55,40,120,'', $foot_right2, 0, 0 , 0, true, 'C', true);
		$pdf->Ln(40);
		$pdf->writeHTMLCell(55,40,75,'', $foot_center2, 0, 0 , 0, true, 'C', true);
		$pdf->writeHTML($pagebreak, true, false, true, false, '');


		//PAGE 3
		$judul3 = '<style>
			.judul {
				text-align : center;
				font-weight : bold;
				text-transform : uppercase;
				font-size : 12px;
				margin-left: 200px;
			}
			.judul-tengah {
				text-align : center;
				font-weight : bold;
				text-transform : uppercase;
				font-size : 14px;
			}
		</style>';
		// $pdf->SetMargins(40, 10, 25, TRUE);
		$judul3 .= '<span class="judul"><br>'.$pengaturan[0]->nilai_pengaturan.'</span><br>';
		$judul3 .= '<span class="judul-tengah">'.$pengaturan[1]->nilai_pengaturan.'</span><br>';
		$judul3 .= '<span style="text-align:center; font-size: 10px">'.$pengaturan[5]->nilai_pengaturan.'</span><br>';
		$judul3 .= '<span class="judul">'.$pengaturan[6]->nilai_pengaturan.'</span><br><br><br>';
		$judul3 .= '<div class="judul"><u>SURAT PERNYATAAN PERMINTAAN UANG PANJAR</u></div><br><br>';
		// $judul3 .= '<span style="text-align:center">Nomor : '.$panjar->no_bukti.'</span><br><br><br>';
		$tabel3 = '<style>
								p {
										line-height : 150%;
										text-align : justify;
									}
							</style>';
		$tabel3 .= '<table width="100%" border="0">
								<tr>
									<td><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sehubungan dengan Surat Pengajuan Permintaan Uang Panjar Nomor : '.$panjar->no_bukti.' tanggal '.$tanggal.' yang kami ajukan sebesar : Rp '.$this->just_money($panjar->nilai_panjar).'
									  <strong> ('.$this->konversi->eja($panjar->nilai_panjar).' RUPIAH)</strong><br>
										Untuk keperluan kegiatan : '.$kegiatan->nama_kegiatan.'<br>
										tahun anggaran '.$year.', dengan ini menyatakan dengan sebenarnya bahwa : </p>
									</td>
								</tr>';
		$tabel3 .= '<tr>
									<td width="3%">1.</td>
									<td width="95%"><p>Jumlah Uang Panjar tersebut di atas akan dipergunakan untuk keperluan guna membiayai Kegiatan yang akan kami laksanakan sesuai DPK-SKPD </p></td>
								</tr>';
		$tabel3 .= '<tr>
									<td>2.</td>
									<td><p>Jumlah Uang Panjar tersebut tidak akan digunakan untuk membiayai pengeluaran-pengeluaran yang menurut ketentuan yang berlaku harus dilakukan dengan Pembayaran Langsung (LS)</p></td>
								</tr>';
		$tabel3 .= '<tr>
									<td width="100%"><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Demikian Surat Pernyataan ini dibuat untuk melengkapi persyaratan pengajuan Uang Panjar kegiatan kami.</p></td>
								</tr>';
		$tabel3 .= '</table>';
		$foot_right3 = 'Trenggalek, '.$tanggal.' <br><br><strong>KUASA PENGGUNA ANGGARAN</strong> <br><br><br><br><strong><u>'.$kegiatan->nama_kpa.'</u></strong><br>NIP. '.$kegiatan->nip_kpa;

		$doc = 'C.9b';
		// set color for text
		$pdf->SetTextColor(255, 255, 255);
		$pdf->writeHTMLCell('','',170,12, $doc, 0, 0 , 1, true, 'C', true);
		$pdf->Ln(1);
		$pdf->SetTextColor(0, 0, 0);
		// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
		$pdf->Image($imageloc, 25, 17, 18, 21, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
		$pdf->writeHTML($judul3, true, false, true, false, '');
		$pdf->SetFont('helvetica', 'n', 10);
		$pdf->writeHTML($tabel3, true, false, true, false, '');
		$pdf->Ln(10);
		$pdf->writeHTMLCell(65,40,120,'', $foot_right3, 0, 0 , 0, true, 'C', true);
		$pdf->writeHTML($pagebreak, true, false, true, false, '');


		//PAGE 4
		$judul4 = '<style>
			.judul {
				text-align : center;
				font-weight : bold;
				text-transform : uppercase;
				font-size : 12px;
				margin-left: 200px;
			}
			.judul-tengah {
				text-align : center;
				font-weight : bold;
				text-transform : uppercase;
				font-size : 14px;
			}
		</style>';
		$judul4 .= '<div class="judul">RENCANA PENGGUNAAN UANG PANJAR DAN<br>WAKTU PELAKSANAAN KEGIATAN</div><br>';
		$judul4 .= '<div>Kegiatan : '.$kegiatan->nama_kegiatan.'</div><br><br>';

		$tabel4 = '<style>
								.center{text-align:center}
								.right{text-align:right}
								tr.noBorder td {border-right : 1 solid black}
							</style>';
		$tabel4 .= '<table width="100%" border="1" cellpadding="2">
							<tr>
								<td class="center" width="5%" rowspan="2">No.</td>
								<td class="center" width="65%" colspan="3">Rencana Penggunaan Dana</td>
								<td class="center" width="30%" rowspan="2">Rencana Waktu Pelaksanaan Kegiatan</td>
							</tr>';
		$tabel4 .= '<tr>
								<td class="center" width="21%">Kode Rekening</td>
								<td class="center" width="28%">Uraian</td>
								<td class="center" width="16%">Jumlah</td>
							</tr>';
		$tabel4 .= '<tr>
								<td class="center" width="5%">1</td>
								<td class="center" width="21%">2</td>
								<td class="center" width="28%">3</td>
								<td class="center" width="16%">4</td>
								<td class="center" width="30%">5</td>
							</tr>';


		$panjar_rinci = $this->panjar_rinci->get_by_id_panjar($panjar->id_panjar);
		$i = 1;
		foreach ($panjar_rinci as $list_panjar_rinci) {
			$tabel4 .= '<tr class="noBorder">
									<td class="center">'.$i.'</td>
									<td class="">'.$pengaturan[2]->nilai_pengaturan.''.$list_panjar_rinci->kode_rekening.'</td>
									<td class="">'.$list_panjar_rinci->uraian_rekening.'</td>
									<td class="right">'.$this->just_money($list_panjar_rinci->jumlah).'</td>
									<td class="">'.$list_panjar_rinci->pelaksanaan.'</td>
								</tr>';
			$i++;
		}

		$tabel4 .= '<tr class="noBorder">
								<td class="center"></td>
								<td class=""></td>
								<td class=""></td>
								<td class="right"></td>
								<td class=""></td>
							</tr>';
		$tabel4 .= '</table>';
		$foot_left4 = '<br><br><br>Pejabat Pelaksana Teknis Kegiatan <br><br><br><br><strong><u>'.$kegiatan->nama_pptk.'</u></strong><br>NIP. '.$kegiatan->nip_pptk;
		$foot_right4 = 'Trenggalek, '.$tanggal.' <br><br>Bendahara Pengeluaran Pembantu<br><br><br><br><strong><u>'.$user->nama_user.'</u></strong><br>NIP. '.$user->nip_user;


		$doc = 'C.9c';
		// set color for text
		$pdf->SetTextColor(255, 255, 255);
		$pdf->writeHTMLCell('','',170,12, $doc, 0, 0 , 1, true, 'C', true);
		$pdf->Ln(5);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->writeHTML($judul4, true, false, true, false, '');
		$pdf->writeHTML($tabel4, true, false, true, false, '');
		$pdf->Ln(10);
		$pdf->writeHTMLCell(55,40,25,'', $foot_left2, 0, 0 , 0, true, 'C', true);
		$pdf->writeHTMLCell(55,40,120,'', $foot_right2, 0, 0 , 0, true, 'C', true);

		$pdf->Output('Nota_Panjar'.$panjar->no_bukti.'.pdf', 'I');

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
