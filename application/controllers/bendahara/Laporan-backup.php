<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends CI_Controller {
	public $id_user = null;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('laporan_model','laporan');
		$this->load->model('program_model','program');
		$this->load->model('kegiatan_model','kegiatan');
		$this->load->model('user_model','user');
		$this->load->model('rekening_model','rekening');
		$this->load->model('rekening_max_model','rekening_max');
		$this->load->model('pengaturan_model','pengaturan');
		$this->id_user = $this->session->userdata('id_user');
		$this->login_lib->cek_login();
		$this->login_lib->cek_admin();
	}

	public function index()
	{
		$kegiatan = $this->kegiatan->get_by_user($this->id_user);

		$data = array('title' => 'Halaman Laporan',
						  		'isi'  => 'bendahara/cetak/list_cetak',
									'foot' => 'bendahara/cetak/foot_cetak',
									'breadcrum1' => 'Laporan',
									'breadcrum2' => '',
									'kegiatan' => $kegiatan
								);

		$this->load->view('bendahara/layout/wrapper', $data);
	}

	public function bku(){
			$jumlah_desimal ="0";
			$pemisah_desimal =",";
			$pemisah_ribuan =".";
			$this->table->set_heading(array('Tanggal', 'Keterangan', 'Rekening', 'Pemasukan', 'Pengeluaran' , 'Sisa'));
			$list_laporan = $this->panjar->listing();
			foreach ($list_laporan as $laporan) {
					$this->table->add_row(date('d-m-Y', strtotime($laporan->tgl_bukti)), $laporan->ket_panjar, '' , number_format($laporan->nilai_panjar, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan),'', number_format($laporan->nilai_panjar, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan));
					//add detail
					$list_detail = $this->panjar->get_detail($laporan->id_panjar);
					$list_pengembalian = $this->pengembalian->get_detail($laporan->id_panjar);
					$sisa = $laporan->nilai_panjar;
					foreach ($list_detail as $detail) {
						$sisa = $sisa - $detail->nilai_spj;
						$this->table->add_row(date('d-m-Y', strtotime($detail->tgl_spj)), $detail->uraian_rekening, $detail->kode_rekening , '' ,number_format($detail->nilai_spj, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan), number_format($sisa, $jumlah_desimal, $pemisah_desimal, $pemisah_ribuan));
					}
			}
			$template = array(
        									'table_open' => '<table id="tabel_bku" class="cell-border dt-center" border="1" cellpadding="2" style="text-align:center">',
											);
			$this->table->set_template($template);

			echo $this->table->generate();

	}
	public function cetak(){
		$this->_validate();
		$lap = $this->input->get('optLp');
		switch ($lap) {
			case 1:
				$this->_cetak_bku();
				break;
			case 2:
				$this->_cetak_bkt();
				break;
			case 3:
				$this->_cetak_pajak();
				break;
			case 4:
				$this->_cetak_lp();
				break;
			case 5:
				$this->_cetak_realisasi();
				break;
			default:
				# code...
				break;
		}
	}

	public function xls(){
		$this->_validate();
		$lap = $this->input->get('optLp');
		switch ($lap) {
			case 1:
				$this->_xls_bku();
				break;
			case 2:
				$this->_xls_bkt();
				break;
			case 3:
				$this->_xls_pajak();
				break;
			default:
				# code...
				break;
		}
	}


	private function _validate()
	{
		$status = TRUE;
		$pesan = '';

		$dateFrom = $this->input->get('tgA') == '' ? '' : date('Y-m-d', strtotime($this->input->get('tgA')));
		$dateTo = $this->input->get('tgB') == '' ? '' : date('Y-m-d', strtotime($this->input->get('tgB')));


		if($dateFrom == '' || $dateTo == '')
		{
			$status = FALSE;
			$pesan = '*) Tanggal harus diisi semua';
		}

		if($status === FALSE)
		{
			$this->session->set_flashdata('error_tgl', $pesan);
			redirect(base_url('bendahara/laporan'));
		}
	}


	private function _cetak_bku()
	{
		ob_start();
		$pengaturan = $this->pengaturan->listing();
		$user = $this->user->get_by_id($this->id_user);
		$i = 1;
		$saldo = $saldo_sebelum = $saldo_sebelumnya = 0;
		$id_kegiatan = $this->input->get('optKg');
		if ($id_kegiatan) {
			$kegiatan = $this->kegiatan->get_by_id($id_kegiatan);
		}
		$dateFrom = date('Y-m-d', strtotime($this->input->get('tgA')));
		$dateTo = date('Y-m-d', strtotime($this->input->get('tgB')));
		//cek dateFrom ? panjar
		$datePanjar = $this->laporan->search_panjar($dateFrom, $id_kegiatan);
		if ($datePanjar){
			$list_laporan = $this->laporan->listing($datePanjar->tgl_laporan);
		} else {
			$list_laporan = $this->laporan->list_all();
		}
		//header table
		$tbl = '<table border="1" cellpadding="2" width="100%">
							<tr align="center" style="font-weight:bold">
								<th style="width:5%">No</th>
								<th style="width:12%">Tanggal</th>
								<th style="width:35%">Keterangan</th>
								<th style="width:12%">Rekening</th>
								<th style="width:12%">Penerimaan</th>
								<th style="width:12%">Pengeluaran</th>
								<th style="width:12%">Saldo</th>
							</tr>';

		//hitung saldo sebelumnya
		foreach ($list_laporan as $lap) {
			if ($lap->id_kegiatan == $id_kegiatan ) {
				$dateLap = date('Y-m-d', strtotime($lap->tgl_laporan));
				if ($dateLap < $dateFrom) {
					$saldo_sebelum = $saldo_sebelum + $lap->penerimaan - $lap->pengeluaran;
					$saldo_sebelumnya = $this->money($saldo_sebelum);
				}
			}
		}
			$tbl .= '<tr><td></td><td></td><td>Saldo Sebelumnya</td><td></td><td></td><td></td><td align="center">'.$saldo_sebelumnya.'</td></tr>';

		//body table
		foreach ($list_laporan as $laporan) {
			if ($laporan->id_kegiatan == $id_kegiatan ) {
					$dateLap = date('Y-m-d', strtotime($laporan->tgl_laporan));
					if ($dateLap <= $dateTo) {
							$saldo = $saldo + $laporan->penerimaan - $laporan->pengeluaran;
					}
					//cek tgl_laporan
					if ($dateLap >= $dateFrom && $dateLap <= $dateTo) {
						$penerimaan = $laporan->penerimaan == 0 ? '': $this->money($laporan->penerimaan);
						$pengeluaran = $laporan->pengeluaran == 0 ? '': $this->money($laporan->pengeluaran);
						$kode_rekening = $laporan->kode_rekening;
						if ($kode_rekening != "") {
							$rekening = $this->rekening->get_by_id($laporan->kode_rekening);
							$kode_rekening = $rekening->kode_rekening;
						}

						$tbl .= '<tr><td align="center">'.$i.'</td><td align="center">'.$this->date_dmy($laporan->tgl_laporan).'</td><td>'.$laporan->ket_laporan.'</td><td align="center">'.$kode_rekening.'</td><td align="center">'.$penerimaan.'</td><td align="center">'.$pengeluaran.'</td><td align="center">'.$this->money($saldo).'</td></tr>';
						$i++;

					}
			}

		}

		if ($i == 1) {
			$tbl .= '<tr><td colspan="7" align="center"> Data Tidak Ditemukan, Cek Kembali Data Anda...!</td></tr>';
		}

		$tbl .='</table>';


		$this->load->library("Pdf");
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);

		// set margins
		$pdf->setPrintHeader(FALSE);
		$pdf->SetMargins(10, 10, 10, TRUE);
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
				text-transform : uppercase;
				font-size : 16px;
			}
		</style>';
		$judul .= '<span class="judul"><br>'.$pengaturan[0]->nilai_pengaturan.'</span><br>';
		$judul .= '<span class="judul-tengah">Buku Kas Umum</span><br>';
		$judul .= '<span class="judul">Bendahara Pengeluaran Pembantu</span><br><br>';
		$judul .= '<div>Kegiatan : '.$kegiatan->nama_kegiatan.'</div>';


		// Add a page
		$pdf->AddPage();
		$pdf->SetFont('helvetica', 'n', 10);
		$pdf->writeHTML($judul, true, false, true, false, '');
		$pdf->SetFont('helvetica', 'n', 9);
		//echo $html;
		$pdf->writeHTML($tbl, true, false, true, false, '');
		// set some text for example
		$num = "Kas di Bendahara Pengeluaran Rp ".$this->money($saldo)."<br>";
		$num .= "( ".ucwords(strtolower($this->konversi->eja($saldo)))." Rupiah ) <br> terdiri dari : <br>";
		$num .= '<table border="0" cellpadding="2" width="100%">
							<tr>
								<td style="width:10%">a.</td>
								<td style="width:60%">Tunai</td>
								<td style="width:30%">Rp...................</td>
							</tr>
							<tr>
								<td>b.</td>
								<td>Saldo Bank</td>
								<td>Rp...................</td>
							</tr>
							<tr>
								<td>c.</td>
								<td>Surat Berharga</td>
								<td>Rp...................</td>
							</tr>
							</table><br>';


		$foot_left = '<br><br>Mengetahui, <br> Kuasa Pengguna Anggaran <br><br><br><br><strong><u>'.$kegiatan->nama_kpa.'</u></strong><br>NIP. '.$kegiatan->nip_kpa;
		$foot_right = 'Trenggalek,................................ <br><br> Bendahara Pengeluaran Pembantu <br><br><br><br><strong><u>'.$user->nama_user.'</u></strong><br>NIP. '.$user->nip_user;
	// 		TCPDF::writeHTMLCell 	($w, $h, $x, $y, $html = “, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = “, $autopadding = true)

		$pdf->writeHTMLCell(100,40,10,'', $num, 0, 0 , 0, true, 'L', true);
		$pdf->Ln(30);
		$pdf->writeHTMLCell(55,40,20,'', $foot_left, 0, 0 , 0, true, 'C', true);
		$pdf->writeHTMLCell(55,40,120,'', $foot_right, 0, 0 , 0, true, 'C', true);
		$pdf->Output('BKU.pdf', 'I');

	}

	private function _cetak_bkt()
	{
		ob_start();
		$pengaturan = $this->pengaturan->listing();
		$user = $this->user->get_by_id($this->id_user);
		$i = 1;
		$saldo = $saldo_sebelum = $saldo_sebelumnya = 0;
		$id_kegiatan = $this->input->get('optKg');
		if ($id_kegiatan) {
			$kegiatan = $this->kegiatan->get_by_id($id_kegiatan);
		}
		$dateFrom = date('Y-m-d', strtotime($this->input->get('tgA')));
		$dateTo = date('Y-m-d', strtotime($this->input->get('tgB')));
		//cek dateFrom ? panjar
		$datePanjar = $this->laporan->search_panjar($dateFrom, $id_kegiatan);
		if ($datePanjar){
			$list_laporan = $this->laporan->listing_bkt($datePanjar->tgl_laporan);
		} else {
			$list_laporan = $this->laporan->list_all_bkt();
		}
		//header table
		$tbl = '<table border="1" cellpadding="2" width="100%">
							<tr align="center" style="font-weight:bold">
								<th style="width:5%">No</th>
								<th style="width:12%">Tanggal</th>
								<th style="width:35%">Keterangan</th>
								<th style="width:12%">Rekening</th>
								<th style="width:12%">Penerimaan</th>
								<th style="width:12%">Pengeluaran</th>
								<th style="width:12%">Saldo</th>
							</tr>';

		//hitung saldo sebelumnya
		foreach ($list_laporan as $lap) {
			if ($lap->id_kegiatan == $id_kegiatan ) {
				$dateLap = date('Y-m-d', strtotime($lap->tgl_laporan));
				if ($dateLap < $dateFrom) {
					$saldo_sebelum = $saldo_sebelum + $lap->penerimaan - $lap->pengeluaran;
					$saldo_sebelumnya = $this->money($saldo_sebelum);
				}
			}
		}
			$tbl .= '<tr><td></td><td></td><td>Saldo Sebelumnya</td><td></td><td></td><td></td><td align="center">'.$saldo_sebelumnya.'</td></tr>';

		//body table
		foreach ($list_laporan as $laporan) {
			if ($laporan->id_kegiatan == $id_kegiatan ) {
					$dateLap = date('Y-m-d', strtotime($laporan->tgl_laporan));
					if ($dateLap <= $dateTo) {
							$saldo = $saldo + $laporan->penerimaan - $laporan->pengeluaran;
					}
					//cek tgl_laporan
					if ($dateLap >= $dateFrom && $dateLap <= $dateTo) {
						$penerimaan = $laporan->penerimaan == 0 ? '': $this->money($laporan->penerimaan);
						$pengeluaran = $laporan->pengeluaran == 0 ? '': $this->money($laporan->pengeluaran);
						$kode_rekening = $laporan->kode_rekening;
						if ($kode_rekening != "") {
							$rekening = $this->rekening->get_by_id($laporan->kode_rekening);
							$kode_rekening = $rekening->kode_rekening;
						}

						$tbl .= '<tr><td align="center">'.$i.'</td><td align="center">'.$this->date_dmy($laporan->tgl_laporan).'</td><td>'.$laporan->ket_laporan.'</td><td align="center">'.$kode_rekening.'</td><td align="center">'.$penerimaan.'</td><td align="center">'.$pengeluaran.'</td><td align="center">'.$this->money($saldo).'</td></tr>';
						$i++;

					}
			}

		}

		if ($i == 1) {
			$tbl .= '<tr><td colspan="7" align="center"> Data Tidak Ditemukan, Cek Kembali Data Anda...!</td></tr>';
		}

		$tbl .='</table>';


		$this->load->library("Pdf");
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);

		// set margins
		$pdf->setPrintHeader(FALSE);
		$pdf->SetMargins(10, 10, 10, TRUE);
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
				text-transform : uppercase;
				font-size : 16px;
			}
		</style>';
		$judul .= '<span class="judul"><br>'.$pengaturan[0]->nilai_pengaturan.'</span><br>';
		$judul .= '<span class="judul-tengah">Buku Pembantu Kas Tunai</span><br>';
		$judul .= '<span class="judul">Bendahara Pengeluaran Pembantu</span><br><br>';
		$judul .= '<div>Kegiatan : '.$kegiatan->nama_kegiatan.'</div>';


		// Add a page
		$pdf->AddPage();
		$pdf->SetFont('helvetica', 'n', 10);
		$pdf->writeHTML($judul, true, false, true, false, '');
		$pdf->SetFont('helvetica', 'n', 9);
		//echo $html;
		$pdf->writeHTML($tbl, true, false, true, false, '');
		// KETERANGAN NOMINAL SALDO
		// $num = "Kas di Bendahara Pengeluaran Rp ".$this->money($saldo)."<br>";
		// $num .= "( ".ucwords(strtolower($this->konversi->eja($saldo)))." Rupiah ) <br> terdiri dari : <br>";
		// $num .= '<table border="0" cellpadding="2" width="100%">
		// 					<tr>
		// 						<td style="width:10%">a.</td>
		// 						<td style="width:60%">Tunai</td>
		// 						<td style="width:30%">Rp...................</td>
		// 					</tr>
		// 					<tr>
		// 						<td>b.</td>
		// 						<td>Saldo Bank</td>
		// 						<td>Rp...................</td>
		// 					</tr>
		// 					<tr>
		// 						<td>c.</td>
		// 						<td>Surat Berharga</td>
		// 						<td>Rp...................</td>
		// 					</tr>
		// 					</table><br>';


		$foot_left = '<br><br>Mengetahui, <br> Kuasa Pengguna Anggaran <br><br><br><br><strong><u>'.$kegiatan->nama_kpa.'</u></strong><br>NIP. '.$kegiatan->nip_kpa;
		$foot_right = 'Trenggalek,................................ <br><br> Bendahara Pengeluaran Pembantu <br><br><br><br><strong><u>'.$user->nama_user.'</u></strong><br>NIP. '.$user->nip_user;
	// 		TCPDF::writeHTMLCell 	($w, $h, $x, $y, $html = “, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = “, $autopadding = true)

		// $pdf->writeHTMLCell(100,40,10,'', $num, 0, 0 , 0, true, 'L', true);
		$pdf->Ln(10);
		$pdf->writeHTMLCell(55,40,20,'', $foot_left, 0, 0 , 0, true, 'C', true);
		$pdf->writeHTMLCell(55,40,120,'', $foot_right, 0, 0 , 0, true, 'C', true);
		$pdf->Output('BKU.pdf', 'I');

	}


	private function _cetak_pajak()
	{
		ob_start();
		$pengaturan = $this->pengaturan->listing();
		$user = $this->user->get_by_id($this->id_user);
		$i = 1;
		$saldo = 0;
		$saldo_sebelum = 0;
		$saldo_sebelumnya = 0;
		$id_kegiatan = $this->input->get('optKg');
		if ($id_kegiatan) {
			$kegiatan = $this->kegiatan->get_by_id($id_kegiatan);
		}
		$dateFrom = date('Y-m-d', strtotime($this->input->get('tgA')));
		$dateTo = date('Y-m-d', strtotime($this->input->get('tgB')));
		//cek dateFrom ? panjar
		$datePanjar = $this->laporan->search_panjar($dateFrom, $id_kegiatan);
		if ($datePanjar){
			$list_laporan = $this->laporan->list_pajak($datePanjar->tgl_laporan);
		} else {
			$list_laporan = $this->laporan->list_all_pajak();
		}
		//header table
		$tbl = '<table border="1" cellpadding="2" width="100%">
							<tr align="center" style="font-weight:bold">
								<th style="width:5%">No</th>
								<th style="width:12%">Tanggal</th>
								<th style="width:35%">Keterangan</th>
								<th style="width:12%">Rekening</th>
								<th style="width:12%">Penerimaan</th>
								<th style="width:12%">Pengeluaran</th>
								<th style="width:12%">Saldo</th>
							</tr>';

		//hitung saldo sebelumnya
		foreach ($list_laporan as $lap) {
			if ($lap->id_kegiatan == $id_kegiatan ) {
				$dateLap = date('Y-m-d', strtotime($lap->tgl_laporan));
				if ($dateLap < $dateFrom) {
					$saldo_sebelum = $saldo_sebelum + $lap->penerimaan - $lap->pengeluaran;
					$saldo_sebelumnya = $this->money($saldo_sebelum);
				}
			}
		}
			$tbl .= '<tr><td></td><td></td><td>Saldo Sebelumnya</td><td></td><td></td><td></td><td align="center">'.$saldo_sebelumnya.'</td></tr>';

		//body table
		foreach ($list_laporan as $laporan) {
			if ($laporan->id_kegiatan == $id_kegiatan ) {
					$saldo = $saldo + $laporan->penerimaan - $laporan->pengeluaran;
					//cek tgl_laporan
					$dateLap = date('Y-m-d', strtotime($laporan->tgl_laporan));
					if ($dateLap >= $dateFrom && $dateLap <= $dateTo) {

						$penerimaan = $laporan->penerimaan == 0 ? '': $this->money($laporan->penerimaan);
						$pengeluaran = $laporan->pengeluaran == 0 ? '': $this->money($laporan->pengeluaran);
						$tbl .= '<tr><td align="center">'.$i.'</td><td align="center">'.$this->date_dmy($laporan->tgl_laporan).'</td><td>'.$laporan->ket_laporan.'</td><td align="center">'.$laporan->kode_rekening.'</td><td align="center">'.$penerimaan.'</td><td align="center">'.$pengeluaran.'</td><td align="center">'.$this->money($saldo).'</td></tr>';
						$i++;

					}
			}

		}

		if ($i == 1) {
			$tbl .= '<tr><td colspan="7" align="center"> Data Tidak Ditemukan, Cek Kembali Data Anda...!</td></tr>';
		}

		$tbl .='</table>';


		$this->load->library("Pdf");
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);

		// set margins
		$pdf->setPrintHeader(FALSE);
		$pdf->SetMargins(10, 10, 10, TRUE);
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
				text-transform : uppercase;
				font-size : 16px;
			}
		</style>';
		$judul .= '<span class="judul"><br>'.$pengaturan[0]->nilai_pengaturan.'</span><br>';
		$judul .= '<span class="judul-tengah">Buku Pembantu Pajak</span><br>';
		$judul .= '<span class="judul">Bendahara Pengeluaran Pembantu</span><br><br>';
		$judul .= '<div>Kegiatan : '.$kegiatan->nama_kegiatan.'</div>';


		// Add a page
		$pdf->AddPage();
		$pdf->SetFont('helvetica', 'n', 10);
		$pdf->writeHTML($judul, true, false, true, false, '');
		$pdf->SetFont('helvetica', 'n', 9);
		//echo $html;
		$pdf->writeHTML($tbl, true, false, true, false, '');
		// set some text for example
		$foot_left = '<br><br>Mengetahui, <br> Kuasa Pengguna Anggaran <br><br><br><br><strong><u>'.$kegiatan->nama_kpa.'</u></strong><br>NIP. '.$kegiatan->nip_kpa;
		$foot_right = 'Trenggalek,................................ <br><br> Bendahara Pengeluaran Pembantu <br><br><br><br><strong><u>'.$user->nama_user.'</u></strong><br>NIP. '.$user->nip_user;
// 		TCPDF::writeHTMLCell 	($w, $h, $x, $y, $html = “, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = “, $autopadding = true)

		$pdf->Ln(10);
		$pdf->writeHTMLCell(55,40,20,'', $foot_left, 0, 0 , 0, true, 'C', true);
		$pdf->writeHTMLCell(55,40,120,'', $foot_right, 0, 0 , 0, true, 'C', true);
		$pdf->Output('B_Pajak.pdf', 'I');

	}


	private function _cetak_lp()
	{
		ob_start();
		$pengaturan = $this->pengaturan->listing();
		$user = $this->user->get_by_id($this->id_user);
		$i = 1;
		$saldo = 0;
		$saldo_sebelum = 0;
		$saldo_sebelumnya = 0;
		$id_kegiatan = $this->input->get('optKg');
		if ($id_kegiatan) {
			$kegiatan = $this->kegiatan->get_by_id($id_kegiatan);
		}
		$dateFrom = date('Y-m-d', strtotime($this->input->get('tgA')));
		$dateTo = date('Y-m-d', strtotime($this->input->get('tgB')));
		$yearFrom = date('Y', strtotime($this->input->get('tgA')));
		$monthFrom = date('n', strtotime($this->input->get('tgA')));
		$monthArray = array('noMonth', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

		//cari id_rekening di tabel rekening_max
		$this->rekening_max->get_by_id_kegiatan($id_kegiatan);

		//cari id_rekening di Bulan Itu
		$idBelanja = $this->laporan->list_id_belanja($dateFrom, $dateTo, $id_kegiatan);
		//Header tabel
		$tbl ='<style>
						td.uraian {text-align : left}
						table {font-size: 8pt}
					</style>';
		$tbl .= '<table border="1" cellpadding="2" width="100%">
							<tr align="center">
								<td style="width:6%" rowspan="2">Kode Rekening</td>
								<td style="width:12%" rowspan="2">Uraian</td>
								<td style="width:7%" rowspan="2">Jumlah Anggaran</td>
								<td style="width:20%" colspan="3">SPJ-LS Gaji</td>
								<td style="width:20%" colspan="3">SPJ-LS Barang Jasa</td>
								<td style="width:20%" colspan="3">SPJ-UP/GU/TU</td>
								<td style="width:8%" rowspan="2">Jumlah SPJ (LS+UP/GU/TU) s.d Bulan ini</td>
								<td style="width:7%" rowspan="2">Sisa Pagu Anggaran</td>
							</tr>
							<tr align="center">
								<td>s.d Bulan Lalu</td>
								<td>Bulan ini</td>
								<td>s.d Bulan ini</td>
								<td>s.d Bulan Lalu</td>
								<td>Bulan ini</td>
								<td>s.d Bulan ini</td>
								<td>s.d Bulan Lalu</td>
								<td>Bulan ini</td>
								<td>s.d Bulan ini</td>
							</tr>
							<tr align="center">
								<td>1</td>
								<td>2</td>
								<td>3</td>
								<td>4</td>
								<td>5</td>
								<td>6</td>
								<td>7</td>
								<td>8</td>
								<td>9=(7+8)</td>
								<td>10</td>
								<td>11</td>
								<td>12=(10+11)</td>
								<td>13=(6+9+12)</td>
								<td>14=(3-13)</td>
							</tr>';
		$jumLastMonthGaji = $jumThisMonthGaji = $jumTillMonthGaji = 0;
		$jumLastMonthLs = $jumThisMonthLs = $jumTillMonthLs = 0;
		$jumLastMonthGu = $jumThisMonthGu = $jumTillMonthGu = 0;
		$jumTotalSpj = $jumSisaPagu = $jumRekMax = 0;
		$rek_max = 0;
		foreach ($idBelanja as $idBelanja) {
			//get jumlah anggaran, kode dan uraian rekening
			$rekMax = $this->rekening_max->get_by_2id($id_kegiatan, $idBelanja->kode_rekening);
			$list_rekening = $this->rekening->get_by_id($idBelanja->kode_rekening);

			if ($rekMax) {
				$rek_max = $rekMax->jumlah;
			}

			//hitung belanja Gaji
			$lastMonthGaji = $this->laporan->last_month_gaji($dateFrom, $idBelanja->kode_rekening, $id_kegiatan);
			$thisMonthGaji = $this->laporan->this_month_gaji($dateFrom, $dateTo, $idBelanja->kode_rekening, $id_kegiatan);
			$tillMonthGaji = $lastMonthGaji->pengeluaran + $thisMonthGaji->pengeluaran;
			//hitung belanja LS
			$lastMonthLs = $this->laporan->last_month_ls($dateFrom, $idBelanja->kode_rekening, $id_kegiatan);
			$thisMonthLs = $this->laporan->this_month_ls($dateFrom, $dateTo, $idBelanja->kode_rekening, $id_kegiatan);
			$tillMonthLs = $lastMonthLs->pengeluaran + $thisMonthLs->pengeluaran;
			//hitung belanja GU
			$lastMonthGu = $this->laporan->last_month_gu($dateFrom, $idBelanja->kode_rekening, $id_kegiatan);
			$thisMonthGu = $this->laporan->this_month_gu($dateFrom, $dateTo, $idBelanja->kode_rekening, $id_kegiatan);
			$tillMonthGu = $lastMonthGu->pengeluaran + $thisMonthGu->pengeluaran;
			//Gaji + LS + GU
			$totalSpj = $tillMonthGaji + $tillMonthLs + $tillMonthGu;
			$sisaPagu = $rek_max - $totalSpj;

			$tbl .= '<tr align="right"><td class="uraian">'.$list_rekening->kode_rekening.'</td><td class="uraian">'.$list_rekening->uraian_rekening.'</td><td>'.$this->money($rek_max).'</td><td>'.$this->money($lastMonthGaji->pengeluaran).'</td><td>'.$this->money($thisMonthGaji->pengeluaran).'</td><td>'.$this->money($tillMonthGaji).'</td>';
			$tbl .= '<td>'.$this->money($lastMonthLs->pengeluaran).'</td><td>'.$this->money($thisMonthLs->pengeluaran).'</td><td>'.$this->money($tillMonthLs).'</td><td>'.$this->money($lastMonthGu->pengeluaran).'</td><td>'.$this->money($thisMonthGu->pengeluaran).'</td><td>'.$this->money($tillMonthGu).'</td>';
			$tbl .= '<td>'.$this->money($totalSpj).'</td><td>'.$this->money($sisaPagu).'</td></tr>';

			$jumRekMax += $rek_max;
			$jumLastMonthGaji += $lastMonthGaji->pengeluaran;
			$jumThisMonthGaji += $thisMonthGaji->pengeluaran;
			$jumTillMonthGaji += $tillMonthGaji;
			$jumLastMonthLs += $lastMonthLs->pengeluaran;
			$jumThisMonthLs += $thisMonthLs->pengeluaran;
			$jumTillMonthLs += $tillMonthLs;
			$jumLastMonthGu += $lastMonthGu->pengeluaran;
			$jumThisMonthGu += $thisMonthGu->pengeluaran;
			$jumTillMonthGu += $tillMonthGu;
			$jumTotalSpj += $totalSpj;
			$jumSisaPagu += $sisaPagu;

			// echo $rekMax->kode_rekening;
			// echo $rekMax->uraian_rekening;
			// echo $rekMax->jumlah;
			// echo $lastMonthGaji->pengeluaran;
			// echo $thisMonthGaji->pengeluaran;
			// echo $tillMonthGaji;
			// echo $lastMonthLs->pengeluaran;
			// echo $thisMonthLs->pengeluaran;
			// echo $tillMonthLs;
			// echo $lastMonthGu->pengeluaran;
			// echo $thisMonthGu->pengeluaran;
			// echo $tillMonthGu;
			// echo $totalSpj;
			// echo $sisaPagu;
			//
			// echo "<br>";
			// print_r($thisMonth);
		}
		$tbl .= '<tr align="right"><td class="uraian">JUMLAH</td><td></td><td>'.$this->money($jumRekMax).'</td><td>'.$this->money($jumLastMonthGaji).'</td><td>'.$this->money($jumThisMonthGaji).'</td><td>'.$this->money($jumTillMonthGaji).'</td>';
		$tbl .= '<td>'.$this->money($jumLastMonthLs).'</td><td>'.$this->money($jumThisMonthLs).'</td><td>'.$this->money($jumTillMonthLs).'</td>';
		$tbl .= '<td>'.$this->money($jumLastMonthGu).'</td><td>'.$this->money($jumThisMonthGu).'</td><td>'.$this->money($jumTillMonthGu).'</td>';
		$tbl .= '<td>'.$this->money($jumTotalSpj).'</td><td>'.$this->money($jumSisaPagu).'</td></tr>';
		$tbl .= '<tr><td rowspan="11"></td><td colspan="13">Penerimaan</td></tr>';

		//hitung uang sp2d / Panjar PENERIMAAN
		$lastMonthSp2dGaji = $this->laporan->last_month_sp2d_gaji($dateFrom, $id_kegiatan);
		$thisMonthSp2dGaji = $this->laporan->this_month_sp2d_gaji($dateFrom, $dateTo, $id_kegiatan);
		$tillMonthSp2dGaji = $lastMonthSp2dGaji->penerimaan + $thisMonthSp2dGaji->penerimaan;
		$lastMonthSp2d = $this->laporan->last_month_sp2d($dateFrom, $id_kegiatan);
		$thisMonthSp2d = $this->laporan->this_month_sp2d($dateFrom, $dateTo, $id_kegiatan);
		$tillMonthSp2d = $lastMonthSp2d->penerimaan + $thisMonthSp2d->penerimaan;
		$lastMonthPanjar = $this->laporan->last_month_panjar($dateFrom, $id_kegiatan);
		$thisMonthPanjar = $this->laporan->this_month_panjar($dateFrom, $dateTo, $id_kegiatan);
		$tillMonthPanjar = $lastMonthPanjar->penerimaan + $thisMonthPanjar->penerimaan;
		$jumPenerimaan = $tillMonthSp2dGaji + $tillMonthSp2d + $tillMonthPanjar;
		$tbl .= '<tr align="right"><td class="uraian" colspan="2">- SP2D / Uang Panjar</td><td>'.$this->money($lastMonthSp2dGaji->penerimaan).'</td><td>'.$this->money($thisMonthSp2dGaji->penerimaan).'</td><td>'.$this->money($tillMonthSp2dGaji).'</td>';
		$tbl .= '<td>'.$this->money($lastMonthSp2d->penerimaan).'</td><td>'.$this->money($thisMonthSp2d->penerimaan).'</td><td>'.$this->money($tillMonthSp2d).'</td>';
		$tbl .= '<td>'.$this->money($lastMonthPanjar->penerimaan).'</td><td>'.$this->money($thisMonthPanjar->penerimaan).'</td><td>'.$this->money($tillMonthPanjar).'</td><td>'.$this->money($jumPenerimaan).'</td><td></td></tr>';
		$tbl .= '<tr><td colspan="13">- Potongan Pajak</td></tr>';
		// <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
		//HITUNG PPN
		$lastMonthPpn = $this->laporan->last_month_ppn($dateFrom, $id_kegiatan);
		$thisMonthPpn = $this->laporan->this_month_ppn($dateFrom, $dateTo, $id_kegiatan);
		$tillMonthPpn = $lastMonthPpn->penerimaan + $thisMonthPpn->penerimaan;
		$tbl .= '<tr align="right"><td class="uraian" colspan="2">  a. PPN</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>'.$this->money($lastMonthPpn->penerimaan).'</td><td>'.$this->money($thisMonthPpn->penerimaan).'</td><td>'.$this->money($tillMonthPpn).'</td><td>'.$this->money($tillMonthPpn).'</td><td></td></tr>';

		//HITUNG PPh 21
		$lastMonthPph21 = $this->laporan->last_month_pph21($dateFrom, $id_kegiatan);
		$thisMonthPph21 = $this->laporan->this_month_pph21($dateFrom, $dateTo, $id_kegiatan);
		$tillMonthPph21 = $lastMonthPph21->penerimaan + $thisMonthPph21->penerimaan;
		$tbl .= '<tr align="right"><td class="uraian" colspan="2">  b. PPh 21</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>'.$this->money($lastMonthPph21->penerimaan).'</td><td>'.$this->money($thisMonthPph21->penerimaan).'</td><td>'.$this->money($tillMonthPph21).'</td><td>'.$this->money($tillMonthPph21).'</td><td></td></tr>';

		//HITUNG PPh 22
		$lastMonthPph22 = $this->laporan->last_month_pph22($dateFrom, $id_kegiatan);
		$thisMonthPph22 = $this->laporan->this_month_pph22($dateFrom, $dateTo, $id_kegiatan);
		$tillMonthPph22 = $lastMonthPph22->penerimaan + $thisMonthPph22->penerimaan;
		$tbl .= '<tr align="right"><td class="uraian" colspan="2">  c. PPh 22</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>'.$this->money($lastMonthPph22->penerimaan).'</td><td>'.$this->money($thisMonthPph22->penerimaan).'</td><td>'.$this->money($tillMonthPph22).'</td><td>'.$this->money($tillMonthPph22).'</td><td></td></tr>';

		//HITUNG PPh 23
		$lastMonthPph23 = $this->laporan->last_month_pph23($dateFrom, $id_kegiatan);
		$thisMonthPph23 = $this->laporan->this_month_pph23($dateFrom, $dateTo, $id_kegiatan);
		$tillMonthPph23 = $lastMonthPph23->penerimaan + $thisMonthPph23->penerimaan;
		$tbl .= '<tr align="right"><td class="uraian" colspan="2">  d. PPh 23</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>'.$this->money($lastMonthPph23->penerimaan).'</td><td>'.$this->money($thisMonthPph23->penerimaan).'</td><td>'.$this->money($tillMonthPph23).'</td><td>'.$this->money($tillMonthPph23).'</td><td></td></tr>';

		//HITUNG PPh Pasal 4 Ayat 2
		$lastMonthPph42 = $this->laporan->last_month_pph42($dateFrom, $id_kegiatan);
		$thisMonthPph42 = $this->laporan->this_month_pph42($dateFrom, $dateTo, $id_kegiatan);
		$tillMonthPph42 = $lastMonthPph42->penerimaan + $thisMonthPph42->penerimaan;
		$tbl .= '<tr align="right"><td class="uraian" colspan="2">  e. PPh Pasal 4 (2)</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>'.$this->money($lastMonthPph42->penerimaan).'</td><td>'.$this->money($thisMonthPph42->penerimaan).'</td><td>'.$this->money($tillMonthPph42).'</td><td>'.$this->money($tillMonthPph42).'</td><td></td></tr>';

		//HITUNG PAJAK DAERAH
		$lastMonthPajakDaerah = $this->laporan->last_month_pajak_daerah($dateFrom, $id_kegiatan);
		$thisMonthPajakDaerah = $this->laporan->this_month_pajak_daerah($dateFrom, $dateTo, $id_kegiatan);
		$tillMonthPajakDaerah = $lastMonthPajakDaerah->penerimaan + $thisMonthPajakDaerah->penerimaan;
		$tbl .= '<tr align="right"><td class="uraian" colspan="2">  f. Pajak Daerah</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>'.$this->money($lastMonthPajakDaerah->penerimaan).'</td><td>'.$this->money($thisMonthPajakDaerah->penerimaan).'</td><td>'.$this->money($tillMonthPajakDaerah).'</td><td>'.$this->money($tillMonthPajakDaerah).'</td><td></td></tr>';


		$tbl .= '<tr align="right"><td class="uraian" colspan="2">- Lain-lain</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>';

		//Hitung total Penerimaan Panjar
		$lastMonthPenerimaanPanjar = $lastMonthPanjar->penerimaan + $lastMonthPpn->penerimaan + $lastMonthPph21->penerimaan + $lastMonthPph22->penerimaan + $lastMonthPph23->penerimaan + $lastMonthPph42->penerimaan + $lastMonthPajakDaerah->penerimaan;
		$thisMonthPenerimaanPanjar = $thisMonthPanjar->penerimaan + $thisMonthPpn->penerimaan + $thisMonthPph21->penerimaan + $thisMonthPph22->penerimaan + $thisMonthPph23->penerimaan + $thisMonthPph42->penerimaan + $thisMonthPajakDaerah->penerimaan;
		$tillMonthPenerimaanPanjar = $tillMonthPanjar + $tillMonthPpn + $tillMonthPph21 + $tillMonthPph22 + $tillMonthPph23 + $tillMonthPph42 + $tillMonthPajakDaerah;
		$jumTotalPenerimaan = $tillMonthSp2dGaji + $tillMonthSp2d + $tillMonthPenerimaanPanjar;

		$tbl .= '<tr align="right"><td class="uraian" colspan="2">Jumlah Penerimaan</td><td>'.$this->money($lastMonthSp2dGaji->penerimaan).'</td><td>'.$this->money($thisMonthSp2dGaji->penerimaan).'</td><td>'.$this->money($tillMonthSp2dGaji).'</td>';
		$tbl .= '<td>'.$this->money($lastMonthSp2d->penerimaan).'</td><td>'.$this->money($thisMonthSp2d->penerimaan).'</td><td>'.$this->money($tillMonthSp2d).'</td>';
		$tbl .= '<td>'.$this->money($lastMonthPenerimaanPanjar).'</td><td>'.$this->money($thisMonthPenerimaanPanjar).'</td><td>'.$this->money($tillMonthPenerimaanPanjar).'</td><td>'.$this->money($jumTotalPenerimaan).'</td><td></td></tr>';
		$tbl .= '</table><br pagebreak = "true">';


		//INSERT TABLE PENGELUARAN
		$tblpengeluaran ='<style>
												td.uraian {text-align : left}
												table {font-size: 8pt}
											</style>';
		$tblpengeluaran .= '<table border="1" cellpadding="2" width="100%">
							<tr align="center">
								<td style="width:6%" rowspan="2">Kode Rekening</td>
								<td style="width:12%" rowspan="2">Uraian</td>
								<td style="width:7%" rowspan="2">Jumlah Anggaran</td>
								<td style="width:20%" colspan="3">SPJ-LS Gaji</td>
								<td style="width:20%" colspan="3">SPJ-LS Barang Jasa</td>
								<td style="width:20%" colspan="3">SPJ-UP/GU/TU</td>
								<td style="width:8%" rowspan="2">Jumlah SPJ (LS+UP/GU/TU) s.d Bulan ini</td>
								<td style="width:7%" rowspan="2">Sisa Pagu Anggaran</td>
							</tr>
							<tr align="center">
								<td>s.d Bulan Lalu</td>
								<td>Bulan ini</td>
								<td>s.d Bulan ini</td>
								<td>s.d Bulan Lalu</td>
								<td>Bulan ini</td>
								<td>s.d Bulan ini</td>
								<td>s.d Bulan Lalu</td>
								<td>Bulan ini</td>
								<td>s.d Bulan ini</td>
							</tr>
							<tr align="center">
								<td>1</td>
								<td>2</td>
								<td>3</td>
								<td>4</td>
								<td>5</td>
								<td>6</td>
								<td>7</td>
								<td>8</td>
								<td>9=(7+8)</td>
								<td>10</td>
								<td>11</td>
								<td>12=(10+11)</td>
								<td>13=(6+9+12)</td>
								<td>14=(3-13)</td>
							</tr>';
		$tblpengeluaran .= '<tr><td rowspan="11"></td><td colspan="13">Pengeluaran</td></tr>';
		//hitung SPJ
		$tblpengeluaran .= '<tr align="right"><td class="uraian" colspan="2">- SPJ(LS+UP/GU/TU)</td><td>'.$this->money($jumLastMonthGaji).'</td><td>'.$this->money($jumThisMonthGaji).'</td><td>'.$this->money($jumTillMonthGaji).'</td>';
		$tblpengeluaran .= '<td>'.$this->money($jumLastMonthLs).'</td><td>'.$this->money($jumThisMonthLs).'</td><td>'.$this->money($jumTillMonthLs).'</td>';
		$tblpengeluaran .= '<td>'.$this->money($jumLastMonthGu).'</td><td>'.$this->money($jumThisMonthGu).'</td><td>'.$this->money($jumTillMonthGu).'</td><td>'.$this->money($jumTotalSpj).'</td><td></td></tr>';
		$tblpengeluaran .= '<tr><td colspan="13">- Penyetoran Pajak</td></tr>';

		//HITUNG PPN
		$lastMonthPpnBayar = $this->laporan->last_month_ppn_bayar($dateFrom, $id_kegiatan);
		$thisMonthPpnBayar = $this->laporan->this_month_ppn_bayar($dateFrom, $dateTo, $id_kegiatan);
		$tillMonthPpnBayar = $lastMonthPpnBayar->pengeluaran + $thisMonthPpnBayar->pengeluaran;
		$tblpengeluaran .= '<tr align="right"><td class="uraian" colspan="2">  a. PPN</td><td></td><td></td><td></td><td></td><td></td><td></td><td>'.$this->money($lastMonthPpnBayar->pengeluaran).'</td><td>'.$this->money($thisMonthPpnBayar->pengeluaran).'</td><td>'.$this->money($tillMonthPpnBayar).'</td><td>'.$this->money($tillMonthPpnBayar).'</td><td></td></tr>';

		//HITUNG PPh 21
		$lastMonthPph21Bayar = $this->laporan->last_month_pph21_bayar($dateFrom, $id_kegiatan);
		$thisMonthPph21Bayar = $this->laporan->this_month_pph21_bayar($dateFrom, $dateTo, $id_kegiatan);
		$tillMonthPph21Bayar = $lastMonthPph21Bayar->pengeluaran + $thisMonthPph21Bayar->pengeluaran;
		$tblpengeluaran .= '<tr align="right"><td class="uraian" colspan="2">  b. PPh 21</td><td></td><td></td><td></td><td></td><td></td><td></td><td>'.$this->money($lastMonthPph21Bayar->pengeluaran).'</td><td>'.$this->money($thisMonthPph21Bayar->pengeluaran).'</td><td>'.$this->money($tillMonthPph21Bayar).'</td><td>'.$this->money($tillMonthPph21Bayar).'</td><td></td></tr>';

		//HITUNG PPh 22
		$lastMonthPph22Bayar = $this->laporan->last_month_pph22_bayar($dateFrom, $id_kegiatan);
		$thisMonthPph22Bayar = $this->laporan->this_month_pph22_bayar($dateFrom, $dateTo, $id_kegiatan);
		$tillMonthPph22Bayar = $lastMonthPph22Bayar->pengeluaran + $thisMonthPph22Bayar->pengeluaran;
		$tblpengeluaran .= '<tr align="right"><td class="uraian" colspan="2">  c. PPh 22</td><td></td><td></td><td></td><td></td><td></td><td></td><td>'.$this->money($lastMonthPph22Bayar->pengeluaran).'</td><td>'.$this->money($thisMonthPph22Bayar->pengeluaran).'</td><td>'.$this->money($tillMonthPph22Bayar).'</td><td>'.$this->money($tillMonthPph22Bayar).'</td><td></td></tr>';

		//HITUNG PPh 23
		$lastMonthPph23Bayar = $this->laporan->last_month_pph23_bayar($dateFrom, $id_kegiatan);
		$thisMonthPph23Bayar = $this->laporan->this_month_pph23_bayar($dateFrom, $dateTo, $id_kegiatan);
		$tillMonthPph23Bayar = $lastMonthPph23Bayar->pengeluaran + $thisMonthPph23Bayar->pengeluaran;
		$tblpengeluaran .= '<tr align="right"><td class="uraian" colspan="2">  d. PPh 23</td><td></td><td></td><td></td><td></td><td></td><td></td><td>'.$this->money($lastMonthPph23Bayar->pengeluaran).'</td><td>'.$this->money($thisMonthPph23Bayar->pengeluaran).'</td><td>'.$this->money($tillMonthPph23Bayar).'</td><td>'.$this->money($tillMonthPph23Bayar).'</td><td></td></tr>';

		//HITUNG PPh Pasal 4 Ayat 2
		$lastMonthPph42Bayar = $this->laporan->last_month_pph42_bayar($dateFrom, $id_kegiatan);
		$thisMonthPph42Bayar = $this->laporan->this_month_pph42_bayar($dateFrom, $dateTo, $id_kegiatan);
		$tillMonthPph42Bayar = $lastMonthPph42Bayar->pengeluaran + $thisMonthPph42Bayar->pengeluaran;
		$tblpengeluaran .= '<tr align="right"><td class="uraian" colspan="2">  e. PPh Pasal 4 (2)</td><td></td><td></td><td></td><td></td><td></td><td></td><td>'.$this->money($lastMonthPph42Bayar->pengeluaran).'</td><td>'.$this->money($thisMonthPph42Bayar->pengeluaran).'</td><td>'.$this->money($tillMonthPph42Bayar).'</td><td>'.$this->money($tillMonthPph42Bayar).'</td><td></td></tr>';

		//HITUNG PAJAK DAERAH
		$lastMonthPajakDaerahBayar = $this->laporan->last_month_pajak_daerah_bayar($dateFrom, $id_kegiatan);
		$thisMonthPajakDaerahBayar = $this->laporan->this_month_pajak_daerah_bayar($dateFrom, $dateTo, $id_kegiatan);
		$tillMonthPajakDaerahBayar = $lastMonthPajakDaerahBayar->pengeluaran + $thisMonthPajakDaerahBayar->pengeluaran;
		$tblpengeluaran .= '<tr align="right"><td class="uraian" colspan="2">  f. Pajak Daerah</td><td></td><td></td><td></td><td></td><td></td><td></td><td>'.$this->money($lastMonthPajakDaerahBayar->pengeluaran).'</td><td>'.$this->money($thisMonthPajakDaerahBayar->pengeluaran).'</td><td>'.$this->money($tillMonthPajakDaerahBayar).'</td><td>'.$this->money($tillMonthPajakDaerahBayar).'</td><td></td></tr>';

		//HITUNG PENGEMBALIAN
		$lastMonthPengembalian = $this->laporan->last_month_pengembalian($dateFrom, $id_kegiatan);
		$thisMonthPengembalian = $this->laporan->this_month_pengembalian($dateFrom, $dateTo, $id_kegiatan);
		$tillMonthPengembalian = $lastMonthPengembalian->pengeluaran + $thisMonthPengembalian->pengeluaran;
		$tblpengeluaran .= '<tr align="right"><td class="uraian" colspan="2">- Lain-lain</td><td></td><td></td><td></td><td></td><td></td><td></td><td>'.$this->money($lastMonthPengembalian->pengeluaran).'</td><td>'.$this->money($thisMonthPengembalian->pengeluaran).'</td><td>'.$this->money($tillMonthPengembalian).'</td><td>'.$this->money($tillMonthPengembalian).'</td><td></td></tr>';

		//HITUNG JUMLAH PENGELUARAN GU
		$lastMonthPengeluaranGu = $jumLastMonthGu + $lastMonthPpnBayar->pengeluaran + $lastMonthPph21Bayar->pengeluaran + $lastMonthPph22Bayar->pengeluaran + $lastMonthPph23Bayar->pengeluaran + $lastMonthPph42Bayar->pengeluaran + $lastMonthPajakDaerahBayar->pengeluaran + $lastMonthPengembalian->pengeluaran;
		$thisMonthPengeluaranGu = $jumThisMonthGu + $thisMonthPpnBayar->pengeluaran + $thisMonthPph21Bayar->pengeluaran + $thisMonthPph22Bayar->pengeluaran + $thisMonthPph23Bayar->pengeluaran + $thisMonthPph42Bayar->pengeluaran + $thisMonthPajakDaerahBayar->pengeluaran + $thisMonthPengembalian->pengeluaran;
		$tillMonthPengeluaranGu = $jumTillMonthGu + $tillMonthPpnBayar + $tillMonthPph21Bayar + $tillMonthPph22Bayar + $tillMonthPph23Bayar + $tillMonthPph42Bayar + $tillMonthPajakDaerahBayar + $tillMonthPengembalian;

		$jumTotalPengeluaran = $jumTillMonthGaji + $jumTillMonthLs + $tillMonthPengeluaranGu;
		$tblpengeluaran .= '<tr align="right"><td class="uraian" colspan="2">Jumlah Pengeluaran</td><td>'.$this->money($jumLastMonthGaji).'</td><td>'.$this->money($jumThisMonthGaji).'</td><td>'.$this->money($jumTillMonthGaji).'</td><td>'.$this->money($jumLastMonthLs).'</td><td>'.$this->money($jumThisMonthLs).'</td><td>'.$this->money($jumTillMonthLs).'</td>';
		$tblpengeluaran .= '<td>'.$this->money($lastMonthPengeluaranGu).'</td><td>'.$this->money($thisMonthPengeluaranGu).'</td><td>'.$this->money($tillMonthPengeluaranGu).'</td><td>'.$this->money($jumTotalPengeluaran).'</td><td></td></tr>';
		$tblpengeluaran .= '<tr align="right"><td style="text-align:center" colspan="12">Selisih</td><td>'.$this->money($jumTotalPenerimaan-$jumTotalPengeluaran).'</td><td></td></tr>';
		$tblpengeluaran .= '</table>';

		$this->load->library("Pdf");
		$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);

		// set margins
		$pdf->setPrintHeader(FALSE);
		$pdf->SetMargins(10, 10, 10, TRUE);
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
				text-transform : uppercase;
				font-size : 16px;
			}
		</style>';
		$judul .= '<span class="judul"><br>'.$pengaturan[0]->nilai_pengaturan.'</span><br>';
		$judul .= '<span class="judul">Laporan Pertanggungjawaban Bendahara Pengeluaran Pembantu</span><br>';

		$subjudul = '<table width="100%">';
		$subjudul .= '<tr><td width="25%">SKPD</td><td width="5%">:</td><td width="70%">'.$pengaturan[1]->nilai_pengaturan.'</td></tr>';
		$subjudul .= '<tr><td>Kuasa Pengguna Anggaran</td><td>:</td><td>'.ucwords($kegiatan->nama_kpa).'</td></tr>';
		$subjudul .= '<tr><td>Bendahara Pengeluaran Pembantu</td><td>:</td><td>'.ucwords($user->nama_user).'</td></tr>';
		$subjudul .= '<tr><td>Tahun Anggaran</td><td>:</td><td>'.$yearFrom.'</td></tr>';
		$subjudul .= '<tr><td>Bulan</td><td>:</td><td>'.$monthArray[$monthFrom].'</td></tr>';
		$subjudul .= '<tr><td>Kegiatan</td><td>:</td><td>'.$kegiatan->nama_kegiatan.'</td></tr>';
		$subjudul .= '</table>';


		// Add a page
		$pdf->AddPage();
		$pdf->SetFont('helvetica', 'n', 10);
		$pdf->writeHTML($judul, true, false, true, false, '');
		$pdf->SetFont('helvetica', 'n', 9);
		$pdf->writeHTML($subjudul, true, false, true, false, '');
		//echo $html;
		$pdf->writeHTML($tbl, true, false, true, false, '');
		$pdf->writeHTML($tblpengeluaran, true, false, true, false, '');
		// set some text for example
		$foot_left = '<br><br>Mengetahui, <br> Kuasa Pengguna Anggaran <br><br><br><br><strong><u>'.$kegiatan->nama_kpa.'</u></strong><br>NIP. '.$kegiatan->nip_kpa;
		$foot_right = 'Trenggalek,................................ <br><br> Bendahara Pengeluaran Pembantu <br><br><br><br><strong><u>'.$user->nama_user.'</u></strong><br>NIP. '.$user->nip_user;
// 		TCPDF::writeHTMLCell 	($w, $h, $x, $y, $html = “, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = “, $autopadding = true)

		$pdf->Ln(10);
		$pdf->writeHTMLCell(55,40,30,'', $foot_left, 0, 0 , 0, true, 'C', true);
		$pdf->writeHTMLCell(55,40,200,'', $foot_right, 0, 0 , 0, true, 'C', true);
		$pdf->Output('Lap_jawab.pdf', 'I');
	}

	private function _cetak_realisasi()
	{
		ob_start();
		$pengaturan = $this->pengaturan->listing();
		$user = $this->user->get_by_id($this->id_user);
		$i = 1;
		$id_kegiatan = $this->input->get('optKg');
		if ($id_kegiatan) {
			$kegiatan = $this->kegiatan->get_by_id($id_kegiatan);
			$program = $this->program->get_by_id($kegiatan->id_program);
		}

		$dateFrom = date('Y-m-d', strtotime($this->input->get('tgA')));
		$yearFrom = date('Y', strtotime($this->input->get('tgA')));
		$monthFrom = date('n', strtotime($this->input->get('tgA')));
		$monthArray = array('noMonth', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
		$dayFrom = date('d', strtotime($this->input->get('tgA')));
		$tanggal = $dayFrom.' '.$monthArray[$monthFrom].' '.$yearFrom;
		//cari id_rekening sebelumnya
		$idBelanja = $this->laporan->list_id_belanja_all($dateFrom, $id_kegiatan);
		//Header tabel
		$tbl ='<style>
						table {font-size : 8pt}
						.center {text-align : center}
						.right {text-align : right}
					</style>';
		$tbl .= '<table border="1" cellpadding="2" width="100%">
							<tr align="center">
								<td style="width:5%" rowspan="2">No.</td>
								<td style="width:14%" rowspan="2">KODE REKENING</td>
								<td style="width:23%" colspan="2">PAGU ANGGARAN KEGIATAN</td>
								<td style="width:23%" rowspan="2">URAIAN</td>
								<td style="width:23%" colspan="2">REALISASI KEGIATAN (SP2D)</td>
								<td style="width:12%" rowspan="2">SISA PAGU ANGGARAN</td>
							</tr>
							<tr align="center">
								<td>UP/GU/TU</td>
								<td>LS</td>
								<td>UP/GU/TU</td>
								<td>LS</td>
							</tr>';

		$jumRekMax = $jumLastMonthLs = $jumLastMonthGu = $jumSisaPagu = 0;
		$rek_max = 0;
		foreach ($idBelanja as $idBelanja) {
			//get jumlah anggaran, kode dan uraian rekening
			$list_rekening = $this->rekening->get_by_id($idBelanja->kode_rekening);
			$rekMax = $this->rekening_max->get_by_2id($id_kegiatan, $idBelanja->kode_rekening);
			if ($rekMax) {
					$rek_max = $rekMax->jumlah;
			}

			//hitung belanja LS
			$lastMonthLs = $this->laporan->last_month_ls($dateFrom, $idBelanja->kode_rekening, $id_kegiatan);
			//hitung belanja GU
			$lastMonthGu = $this->laporan->last_month_gu($dateFrom, $idBelanja->kode_rekening, $id_kegiatan);
			//Gaji + LS + GU
			$totalSpj = $lastMonthLs->pengeluaran + $lastMonthGu->pengeluaran;
			$sisaPagu = $rek_max - $totalSpj;

			$tbl .= '<tr><td class="center">'.$i.'</td><td>'.$list_rekening->kode_rekening.'</td><td class="right">'.$this->money($rek_max).'</td><td class="right"></td><td>'.$list_rekening->uraian_rekening.'</td><td class="right">'.$this->money($lastMonthGu->pengeluaran).'</td><td class="right">'.$this->money($lastMonthLs->pengeluaran).'</td><td class="right">'.$this->money($sisaPagu).'</td></tr>';

			$jumRekMax += $rek_max;
			$jumLastMonthLs += $lastMonthLs->pengeluaran;
			$jumLastMonthGu += $lastMonthGu->pengeluaran;
			$jumSisaPagu += $sisaPagu;
			$i++;
		}
		$tbl .= '<tr><td></td><td></td><td class="right">'.$this->money($jumRekMax).'</td><td class="right">-</td><td></td><td class="right">'.$this->money($jumLastMonthGu).'</td><td class="right">'.$this->money($jumLastMonthLs).'</td><td class="right">'.$this->money($jumSisaPagu).'</td></tr>';
		$tbl .= '</table>';

		$this->load->library("Pdf");
		$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);

		// set margins
		$pdf->setPrintHeader(FALSE);
		$pdf->SetMargins(10, 10, 10, TRUE);
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
				text-transform : uppercase;
				font-size : 16px;
			}
		</style>';
		$judul .= '<span class="judul"><br>'.$pengaturan[0]->nilai_pengaturan.'</span><br>';
		$judul .= '<span class="judul">'.$pengaturan[1]->nilai_pengaturan.'</span><br>';
		$judul .= '<span style="text-align:center">'.$pengaturan[5]->nilai_pengaturan.'</span><br>';
		$judul .= '<span style="text-align:center">'.$pengaturan[6]->nilai_pengaturan.'</span><br><br>';

		$judul .= '<div class="judul"><strong>LAPORAN REALISASI PER KEGIATAN</strong></div><br><br>';

		$subjudul = '<table width="100%">';
		$subjudul .= '<tr><td width="20%">SKPD</td><td width="3%">:</td><td width="82%">'.$pengaturan[1]->nilai_pengaturan.'</td></tr>';
		$subjudul .= '<tr><td>Nama Program</td><td>:</td><td>'.ucwords($program->nama_program).'</td></tr>';
		$subjudul .= '<tr><td>Nama Kegiatan</td><td>:</td><td>'.ucwords($kegiatan->nama_kegiatan).'</td></tr>';
		$subjudul .= '<tr><td>Nama PPTK</td><td>:</td><td>'.$kegiatan->nama_pptk.'</td></tr>';
		$subjudul .= '</table>';


		// Add a page
		$pdf->AddPage();
		$pdf->SetFont('helvetica', 'n', 8);
		// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
		$imageloc = base_url('assets/dist/img/logo_trenggalek_bw.png');
		$pdf->Image($imageloc, 20, 12, 18, 21, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
		$pdf->writeHTML($judul, true, false, true, false, '');
		$pdf->SetFont('helvetica', 'n', 8);
		$pdf->writeHTML($subjudul, true, false, true, false, '');
		//echo $html;
		$pdf->writeHTML($tbl, false, false, true, false, '');
		$pdf->writeHTMLCell('',60,'','', '', 1, 0 , 0, true, 'C', true);
		// set some text for example
		$foot_left = '<br><br><br> Kuasa Pengguna Anggaran <br><br><br><br><strong><u>'.$kegiatan->nama_kpa.'</u></strong><br>NIP. '.$kegiatan->nip_kpa;
		$foot_center = '<br><br><br>Pejabat Pelaksana Teknis Kegiatan<br><br><br><br><strong><u>'.$kegiatan->nama_pptk.'</u></strong><br>NIP. '.$kegiatan->nip_pptk;
		$foot_right = 'Trenggalek, '.$tanggal.' <br><br> Bendahara Pengeluaran Pembantu <br><br><br><br><strong><u>'.$user->nama_user.'</u></strong><br>NIP. '.$user->nip_user;
	// 		TCPDF::writeHTMLCell 	($w, $h, $x, $y, $html = “, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = “, $autopadding = true)

		$pdf->Ln(10);
		$pdf->writeHTMLCell(55,40,10,'', $foot_left, 0, 0 , 0, true, 'C', true);
		$pdf->writeHTMLCell(55,40,75,'', $foot_center, 0, 0 , 0, true, 'C', true);
		$pdf->writeHTMLCell(55,40,140,'', $foot_right, 0, 0 , 0, true, 'C', true);
		$pdf->Output('Realisasi.pdf', 'I');
	}





	////XLS//////
	private function _xls_bku()
	{
		$user = $this->user->get_by_id($this->id_user);
		$i = 1;
		$saldo = 0;
		$saldo_sebelum = 0;
		$saldo_sebelumnya = 0;
		$id_kegiatan = $this->input->get('optKg');
		if ($id_kegiatan) {
			$kegiatan = $this->kegiatan->get_by_id($id_kegiatan);
		}
		$dateFrom = date('Y-m-d', strtotime($this->input->get('tgA')));
		$dateTo = date('Y-m-d', strtotime($this->input->get('tgB')));
		//cek dateFrom ? panjar
		$datePanjar = $this->laporan->search_panjar($dateFrom, $id_kegiatan);
		if ($datePanjar){
			$list_laporan = $this->laporan->listing($datePanjar->tgl_laporan, $id_kegiatan);
		} else {
			$list_laporan = $this->laporan->list_all($id_kegiatan);
		}
		//header xls
		//activate worksheet number 1
		$this->excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->excel->getActiveSheet()->setTitle('bku');
		//set cell A1 content with some text
		$this->excel->getActiveSheet()->setCellValue('A1', 'PEMERINTAH KABUPATEN TRENGGALEK');
		$this->excel->getActiveSheet()->setCellValue('A2', 'BUKU KAS UMUM');
		$this->excel->getActiveSheet()->setCellValue('A3', 'BENDAHARA PENGELUARAN PEMBANTU');
		$this->excel->getActiveSheet()->setCellValue('A5', 'Kegiatan :'.$kegiatan->nama_kegiatan);
		//change the font size
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
		$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(14);
		$this->excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(12);
		$this->excel->getActiveSheet()->getStyle('A5')->getFont()->setSize(12);
		//make the font become bold
		$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
		$this->excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
		//set aligment to center for that merged cell (A1 to D1)
		$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);


		$rowCount = 6;
		$i = 1;
			// Iterate through each result from the SQL query in turn
			// We fetch each database result row into $row in turn

					foreach ($list_laporan as $laporan) {
						if ($laporan->id_kegiatan == $id_kegiatan ) {
								$saldo = $saldo + $laporan->penerimaan - $laporan->pengeluaran;
								$kode_rekening = $laporan->kode_rekening;
								if ($kode_rekening != "") {
									$rekening = $this->rekening->get_by_id($laporan->kode_rekening);
									$kode_rekening = $rekening->kode_rekening;
								}
								//cek tgl_laporan
								$dateLap = date('Y-m-d', strtotime($laporan->tgl_laporan));
								if ($dateLap >= $dateFrom && $dateLap <= $dateTo) {

									$this->excel->getActiveSheet()->SetCellValue('A'.$rowCount, $i);
									$this->excel->getActiveSheet()->SetCellValue('B'.$rowCount, $this->date_dmy($laporan->tgl_laporan));
									$this->excel->getActiveSheet()->SetCellValue('C'.$rowCount, $laporan->ket_laporan);
									$this->excel->getActiveSheet()->SetCellValue('D'.$rowCount, $kode_rekening);
									$this->excel->getActiveSheet()->SetCellValue('E'.$rowCount, $laporan->penerimaan);
									$this->excel->getActiveSheet()->SetCellValue('F'.$rowCount, $laporan->pengeluaran);
									$this->excel->getActiveSheet()->SetCellValue('G'.$rowCount, $saldo);
									$rowCount++;
									//$tbl .= '<tr><td align="center">'.$i.'</td><td align="center">'.$this->date_dmy($laporan->tgl_laporan).'</td><td>'.$laporan->ket_laporan.'</td><td align="center">'.$laporan->kode_rekening.'</td><td align="center">'.$penerimaan.'</td><td align="center">'.$pengeluaran.'</td><td align="center">'.$this->money($saldo).'</td></tr>';
									$i++;
								}
						}

					}

		$filename='BKU.xls'; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache

		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');

	}

	private function _xls_bkt()
	{
		ob_start();
		$user = $this->user->get_by_id($this->id_user);
		$i = 1;
		$saldo = 0;
		$id_kegiatan = $this->input->get('optKg');
		if ($id_kegiatan) {
			$kegiatan = $this->kegiatan->get_by_id($id_kegiatan);
		}
		$dateFrom = date('Y-m-d', strtotime($this->input->get('tgA')));
		$dateTo = date('Y-m-d', strtotime($this->input->get('tgB')));
		$list_laporan = $this->laporan->list_tunai($id_kegiatan);

		//header table
		$tbl = '<table border="1" cellpadding="2" width="100%">
							<tr align="center" style="font-weight:bold">
								<th style="width:5%">No</th>
								<th style="width:12%">Tanggal</th>
								<th style="width:35%">Keterangan</th>
								<th style="width:12%">Rekening</th>
								<th style="width:12%">Penerimaan</th>
								<th style="width:12%">Pengeluaran</th>
								<th style="width:12%">Saldo</th>
							</tr>';

		//body table
		foreach ($list_laporan as $laporan) {
			if ($laporan->id_kegiatan == $id_kegiatan ) {
					$saldo = $saldo + $laporan->penerimaan - $laporan->pengeluaran;
					//cek tgl_laporan
					$dateLap = date('Y-m-d', strtotime($laporan->tgl_laporan));
					if ($dateLap >= $dateFrom && $dateLap <= $dateTo) {

						$penerimaan = $laporan->penerimaan == 0 ? '': $this->money($laporan->penerimaan);
						$pengeluaran = $laporan->pengeluaran == 0 ? '': $this->money($laporan->pengeluaran);
						$tbl .= '<tr><td align="center">'.$i.'</td><td align="center">'.$this->date_dmy($laporan->tgl_laporan).'</td><td>'.$laporan->ket_laporan.'</td><td align="center">'.$laporan->kode_rekening.'</td><td align="center">'.$penerimaan.'</td><td align="center">'.$pengeluaran.'</td><td align="center">'.$this->money($saldo).'</td></tr>';
						$i++;

					}
			}

		}

		if ($i == 1) {
			$tbl .= '<tr><td colspan="7" align="center"> Data Tidak Ditemukan, Cek Kembali Data Anda...!</td></tr>';
		}

		$tbl .='</table>';


		$this->load->library("Pdf");
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);

		// set margins
		$pdf->setPrintHeader(FALSE);
		$pdf->SetMargins(10, 10, 10, TRUE);
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
				text-transform : uppercase;
				font-size : 16px;
			}
		</style>';
		$judul .= '<span class="judul"><br>Pemerintah Kabupaten Trenggalek</span><br>';
		$judul .= '<span class="judul-tengah">Buku Pembantu Kas Tunai</span><br>';
		$judul .= '<span class="judul">Bendahara Pengeluaran Pembantu</span><br><br>';
		$judul .= '<div>Kegiatan : '.$kegiatan->nama_kegiatan.'</div>';


		// Add a page
		$pdf->AddPage();
		$pdf->SetFont('helvetica', 'n', 10);
		$pdf->writeHTML($judul, true, false, true, false, '');
		$pdf->SetFont('helvetica', 'n', 9);
		//echo $html;
		$pdf->writeHTML($tbl, true, false, true, false, '');
		// set some text for example
		$foot_left = '<br><br>Mengetahui, <br> Kuasa Pengguna Anggaran <br><br><br><br><strong><u>'.$kegiatan->nama_kpa.'</u></strong><br>NIP. '.$kegiatan->nip_kpa;
		$foot_right = 'Trenggalek,................................ <br><br> Bendahara Pengeluaran Pembantu <br><br><br><br><strong><u>'.$user->nama_user.'</u></strong><br>NIP. '.$user->nip_user;
// 		TCPDF::writeHTMLCell 	($w, $h, $x, $y, $html = “, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = “, $autopadding = true)

		$pdf->Ln(10);
		$pdf->writeHTMLCell(55,40,20,'', $foot_left, 0, 0 , 0, true, 'C', true);
		$pdf->writeHTMLCell(55,40,120,'', $foot_right, 0, 0 , 0, true, 'C', true);
		$pdf->Output('BKT.pdf', 'I');
	}

	private function _xls_pajak()
	{
		ob_start();
		$user = $this->user->get_by_id($this->id_user);
		$i = 1;
		$saldo = 0;
		$saldo_sebelum = 0;
		$saldo_sebelumnya = 0;
		$id_kegiatan = $this->input->get('optKg');
		if ($id_kegiatan) {
			$kegiatan = $this->kegiatan->get_by_id($id_kegiatan);
		}
		$dateFrom = date('Y-m-d', strtotime($this->input->get('tgA')));
		$dateTo = date('Y-m-d', strtotime($this->input->get('tgB')));
		//cek dateFrom ? panjar
		$datePanjar = $this->laporan->search_panjar($dateFrom, $id_kegiatan);
		if ($datePanjar){
			$list_laporan = $this->laporan->list_pajak($datePanjar->tgl_laporan, $id_kegiatan);
		} else {
			$list_laporan = $this->laporan->list_all_pajak($id_kegiatan);
		}
		//header table
		$tbl = '<table border="1" cellpadding="2" width="100%">
							<tr align="center" style="font-weight:bold">
								<th style="width:5%">No</th>
								<th style="width:12%">Tanggal</th>
								<th style="width:35%">Keterangan</th>
								<th style="width:12%">Rekening</th>
								<th style="width:12%">Penerimaan</th>
								<th style="width:12%">Pengeluaran</th>
								<th style="width:12%">Saldo</th>
							</tr>';

		//hitung saldo sebelumnya
		foreach ($list_laporan as $lap) {
			if ($lap->id_kegiatan == $id_kegiatan ) {
				$dateLap = date('Y-m-d', strtotime($lap->tgl_laporan));
				if ($dateLap < $dateFrom) {
					$saldo_sebelum = $saldo_sebelum + $lap->penerimaan - $lap->pengeluaran;
					$saldo_sebelumnya = $this->money($saldo_sebelum);
				}
			}
		}
			$tbl .= '<tr><td></td><td></td><td>Saldo Sebelumnya</td><td></td><td></td><td></td><td align="center">'.$saldo_sebelumnya.'</td></tr>';

		//body table
		foreach ($list_laporan as $laporan) {
			if ($laporan->id_kegiatan == $id_kegiatan ) {
					$saldo = $saldo + $laporan->penerimaan - $laporan->pengeluaran;
					//cek tgl_laporan
					$dateLap = date('Y-m-d', strtotime($laporan->tgl_laporan));
					if ($dateLap >= $dateFrom && $dateLap <= $dateTo) {

						$penerimaan = $laporan->penerimaan == 0 ? '': $this->money($laporan->penerimaan);
						$pengeluaran = $laporan->pengeluaran == 0 ? '': $this->money($laporan->pengeluaran);
						$tbl .= '<tr><td align="center">'.$i.'</td><td align="center">'.$this->date_dmy($laporan->tgl_laporan).'</td><td>'.$laporan->ket_laporan.'</td><td align="center">'.$laporan->kode_rekening.'</td><td align="center">'.$penerimaan.'</td><td align="center">'.$pengeluaran.'</td><td align="center">'.$this->money($saldo).'</td></tr>';
						$i++;

					}
			}

		}

		if ($i == 1) {
			$tbl .= '<tr><td colspan="7" align="center"> Data Tidak Ditemukan, Cek Kembali Data Anda...!</td></tr>';
		}

		$tbl .='</table>';


		$this->load->library("Pdf");
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);

		// set margins
		$pdf->setPrintHeader(FALSE);
		$pdf->SetMargins(10, 10, 10, TRUE);
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
				text-transform : uppercase;
				font-size : 16px;
			}
		</style>';
		$judul .= '<span class="judul"><br>Pemerintah Kabupaten Trenggalek</span><br>';
		$judul .= '<span class="judul-tengah">Buku Pembantu Pajak</span><br>';
		$judul .= '<span class="judul">Bendahara Pengeluaran Pembantu</span><br><br>';
		$judul .= '<div>Kegiatan : '.$kegiatan->nama_kegiatan.'</div>';


		// Add a page
		$pdf->AddPage();
		$pdf->SetFont('helvetica', 'n', 10);
		$pdf->writeHTML($judul, true, false, true, false, '');
		$pdf->SetFont('helvetica', 'n', 9);
		//echo $html;
		$pdf->writeHTML($tbl, true, false, true, false, '');
		// set some text for example
		$foot_left = '<br><br>Mengetahui, <br> Kuasa Pengguna Anggaran <br><br><br><br><strong><u>'.$kegiatan->nama_kpa.'</u></strong><br>NIP. '.$kegiatan->nip_kpa;
		$foot_right = 'Trenggalek,................................ <br><br> Bendahara Pengeluaran Pembantu <br><br><br><br><strong><u>'.$user->nama_user.'</u></strong><br>NIP. '.$user->nip_user;
// 		TCPDF::writeHTMLCell 	($w, $h, $x, $y, $html = “, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = “, $autopadding = true)

		$pdf->Ln(10);
		$pdf->writeHTMLCell(55,40,20,'', $foot_left, 0, 0 , 0, true, 'C', true);
		$pdf->writeHTMLCell(55,40,120,'', $foot_right, 0, 0 , 0, true, 'C', true);
		$pdf->Output('B_Pajak.pdf', 'I');

	}

		private function date_dmy($date)
		{
			 return date('d-m-Y', strtotime($date));
		}

		private function date_ymd($date)
		{
			return date('Y-m-d', strtotime($date));
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
