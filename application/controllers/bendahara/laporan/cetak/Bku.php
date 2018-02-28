<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Bku extends CI_Controller {

  public $id_user = null;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('laporan_model','laporan');
		$this->load->model('kegiatan_model','kegiatan');
		$this->load->model('user_model','user');
		$this->id_user = $this->session->userdata('id_user');
	}

    public function _cetak_bku()
    {
      ob_start();
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
      $datePanjar = $this->laporan->search_panjar($dateFrom);
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


      $foot_left = '<br><br>Mengetahui, <br> Kuasa Pengguna Anggaran <br><br><br><br><strong><u>'.strtoupper($kegiatan->nama_kpa).'</u></strong><br>NIP. '.$kegiatan->nip_kpa;
      $foot_right = 'Trenggalek,................................ <br><br> Bendahara Pengeluaran Pembantu <br><br><br><br><strong><u>'.strtoupper($user->nama_user).'</u></strong><br>NIP. '.$user->nip_user;
    // 		TCPDF::writeHTMLCell 	($w, $h, $x, $y, $html = “, $border = 0, $ln = 0, $fill = false, $reseth = true, $align = “, $autopadding = true)

      $pdf->writeHTMLCell(100,40,10,'', $num, 0, 0 , 0, true, 'L', true);
      $pdf->Ln(30);
      $pdf->writeHTMLCell(55,40,20,'', $foot_left, 0, 0 , 0, true, 'C', true);
      $pdf->writeHTMLCell(55,40,120,'', $foot_right, 0, 0 , 0, true, 'C', true);
      $pdf->Output('BKU.pdf', 'I');

    }
}
