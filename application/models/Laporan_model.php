<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_model extends CI_Model {

	var $table = 'laporan';

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}


	// public function get_by_id($id_pajak)
	// {
	// 	$this->db->select('id_pajak, id_spj_detail, jenis_pajak, nilai_pajak, DATE_FORMAT(tgl_terima_pajak, "%d %m %Y") as tgl_terima_pajak, DATE_FORMAT(tgl_setor_pajak, "%d %m %Y") as tgl_setor_pajak');
	// 	$this->db->from($this->table);
	// 	$this->db->where('id_pajak',$id_pajak);
	// 	$query = $this->db->get();
	//
	// 	return $query->row();
	// }


	public function ceksaldosebelum($id_kegiatan,$dateFrom){
		$this->db->select('SUM(penerimaan) as total');
		$this->db->from($this->table);
		$this->db->where(array('tgl_laporan <' => $dateFrom, 'tgl_laporan >=' => substr($dateFrom,0,4).'-01-01','jenis_laporan !=' => 'panjar ','id_kegiatan LIKE '  => '%'.$id_kegiatan.'%'));
		$totalpenerimaan=$this->db->get()->row()->total;
		
		$this->db->select('SUM(pengeluaran) as total');
		$this->db->from($this->table);
		$this->db->where(array('tgl_laporan <' => $dateFrom, 'tgl_laporan >=' => substr($dateFrom,0,4).'-01-01','jenis_laporan !=' => 'panjar ','id_kegiatan LIKE '  => '%'.$id_kegiatan.'%'));
		$totalpengeluaran=$this->db->get()->row()->total;
				
		return $totalpenerimaan-$totalpengeluaran;

	}
	
	public function listing($dateFrom,$dateTo){
		$this->db->from($this->table);
		//$this->db->where(array('tgl_laporan >=' => $datePanjar, 'jenis_laporan !=' => 'panjar '));
		$this->db->where(array('tgl_laporan >=' => $dateFrom,'tgl_laporan <=' => $dateTo, 'jenis_laporan !=' => 'panjar '));
		$this->db->order_by('tgl_laporan', 'ASC');
		$this->db->order_by('tgl_a', 'ASC');
		$this->db->order_by('id_laporan', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	
	/*
	public function listing($datePanjar){
		$this->db->from($this->table);
		//$this->db->where(array('tgl_laporan >=' => $datePanjar, 'jenis_laporan !=' => 'panjar '));
		$this->db->where(array('tgl_laporan >=' => $datePanjar, 'jenis_laporan !=' => 'panjar '));
		$this->db->order_by('tgl_laporan', 'ASC');
		$this->db->order_by('tgl_a', 'ASC');
		$this->db->order_by('id_laporan', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}
    */
	public function list_all(){
		$this->db->from($this->table);
		$this->db->where(array('jenis_laporan !=' => 'panjar'));
		$this->db->order_by('tgl_laporan', 'ASC');
		$this->db->order_by('tgl_a', 'ASC');
		$this->db->order_by('id_laporan', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	
	public function ceksaldosebelum_bkt($id_kegiatan,$dateFrom){
		$this->db->select('SUM(penerimaan) as total');
		$this->db->from($this->table);
		$this->db->where(array('tgl_a <' => $dateFrom,'tgl_a >=' => substr($dateFrom,0,4).'-01-01', 'id_kegiatan LIKE '  => '%'.$id_kegiatan.'%'));
		$totalpenerimaan=$this->db->get()->row()->total;
		
		$this->db->select('SUM(pengeluaran) as total');
		$this->db->from($this->table);
		$this->db->where(array('tgl_a <' => $dateFrom, 'tgl_a >=' => substr($dateFrom,0,4).'-01-01','id_kegiatan LIKE '  => '%'.$id_kegiatan.'%'));
		$totalpengeluaran=$this->db->get()->row()->total;
				
		return $totalpenerimaan-$totalpengeluaran;

	}
	
	
	public function listing_bkt($dateFrom,$dateTo){
		$this->db->from($this->table);
		//$this->db->where(array('tgl_laporan >=' => $datePanjar));
		$this->db->where(array('tgl_a >=' => $dateFrom,'tgl_a <=' => $dateTo));
		//$not_jenis_laporan = array('tunai', 'panjar_sah');
		//$this->db->where_not_in('jenis_laporan', $not_jenis_laporan);
		$this->db->order_by('tgl_a', 'ASC');
		//$this->db->order_by('tgl_laporan', 'ASC');
		$this->db->order_by('link', 'ASC');
		$this->db->order_by('jenis_laporan', 'DESC');
		$this->db->order_by('id_laporan', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function list_all_bkt(){
		$this->db->from($this->table);
		//$not_jenis_laporan = array('tunai', 'panjar_sah');
		//$this->db->where_not_in($not_jenis_laporan);
		$this->db->order_by('tgl_a', 'ASC');
		//$this->db->order_by('tgl_laporan', 'ASC');
		$this->db->order_by('link', 'ASC');
		$this->db->order_by('jenis_laporan', 'DESC');
		$this->db->order_by('id_laporan', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function search_panjar($dateFrom, $id_kegiatan){
		$this->db->select('tgl_laporan');
		$this->db->from($this->table);
		$this->db->where(array('jenis_laporan' => 'ajupanjar', 'tgl_laporan <=' => $dateFrom, "id_kegiatan" => $id_kegiatan));
		$this->db->order_by('tgl_laporan', 'DESC');
		$query = $this->db->get();
		return $query->row();
	}


	public function list_tunai(){
		$this->db->from($this->table);
		$this->db->where(array('jenis_laporan' => 'tunai'));
		$this->db->order_by('tgl_laporan', 'ASC');
		$this->db->order_by('id_laporan', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function list_pajak($datePanjar){
		$this->db->from($this->table);
		$this->db->where(array('tgl_laporan >=' => $datePanjar, 'jenis_laporan' => 'pajak'));
		$this->db->order_by('tgl_laporan', 'ASC');
		$this->db->order_by('id_laporan', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function list_all_pajak(){
		$this->db->from($this->table);
		$this->db->where(array('jenis_laporan' => 'pajak'));
		$this->db->order_by('tgl_laporan', 'ASC');
		$this->db->order_by('id_laporan', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}


	public function list_id_belanja($dateFrom, $dateTo, $id_kegiatan){
		$this->db->select('kode_rekening');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "kode_rekening !=" => "", "penerimaan" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->order_by('kode_rekening', 'ASC');
		$this->db->group_by("kode_rekening");
		$query = $this->db->get();
		return $query->result();
	}

	// FOR REALISASI
	public function list_id_belanja_all($dateFrom, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select('kode_rekening');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan <" => $dateFrom, "kode_rekening !=" => "", "penerimaan" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->order_by('kode_rekening', 'ASC');
		$this->db->group_by("kode_rekening");
		$query = $this->db->get();
		return $query->result();
	}

	public function last_month_gu($dateFrom, $id_rekening, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("kode_rekening" => $id_rekening, "penerimaan" => 0, "YEAR(tgl_laporan)" => $yearFrom, "tgl_laporan <" => $dateFrom, "jenis_laporan" => "panjar_sah", "id_kegiatan" => $id_kegiatan ));
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_gu($dateFrom, $dateTo, $id_rekening, $id_kegiatan){
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "kode_rekening" => $id_rekening, "penerimaan" => 0, "jenis_laporan" => "panjar_sah", "id_kegiatan" => $id_kegiatan));
		$query = $this->db->get();
		return $query->row();
	}

	public function last_month_ls($dateFrom, $id_rekening, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("kode_rekening" => $id_rekening, "penerimaan" => 0, "YEAR(tgl_laporan)" => $yearFrom, "tgl_laporan <" => $dateFrom, "jenis_laporan" => "tunai", "id_kegiatan" => $id_kegiatan ));
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_ls($dateFrom, $dateTo, $id_rekening, $id_kegiatan){
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "kode_rekening" => $id_rekening, "penerimaan" => 0, "jenis_laporan" => "tunai", "id_kegiatan" => $id_kegiatan ));
		$query = $this->db->get();
		return $query->row();
	}

	public function last_month_gaji($dateFrom, $id_rekening, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("kode_rekening" => $id_rekening, "penerimaan" => 0, "YEAR(tgl_laporan)" => $yearFrom, "tgl_laporan <" => $dateFrom, "jenis_laporan" => "gaji", "id_kegiatan" => $id_kegiatan ));
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_gaji($dateFrom, $dateTo, $id_rekening, $id_kegiatan){
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "kode_rekening" => $id_rekening, "penerimaan" => 0, "jenis_laporan" => "gaji", "id_kegiatan" => $id_kegiatan ));
		$query = $this->db->get();
		return $query->row();
	}


	public function last_month_sp2d_gaji($dateFrom, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('penerimaan');
		$this->db->from($this->table);
		$this->db->where(array("pengeluaran" => 0, "tgl_laporan <" => $dateFrom, "YEAR(tgl_laporan)" => $yearFrom, "jenis_laporan" => "gaji", "id_kegiatan" => $id_kegiatan ));
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_sp2d_gaji($dateFrom, $dateTo, $id_kegiatan){
		$this->db->select_sum('penerimaan');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "pengeluaran" => 0, "jenis_laporan" => "gaji", "id_kegiatan" => $id_kegiatan ));
		$query = $this->db->get();
		return $query->row();
	}

	public function last_month_sp2d($dateFrom, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('penerimaan');
		$this->db->from($this->table);
		$this->db->where(array("pengeluaran" => 0, "tgl_laporan <" => $dateFrom, "YEAR(tgl_laporan)" => $yearFrom, "jenis_laporan" => "tunai", "id_kegiatan" => $id_kegiatan ));
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_sp2d($dateFrom, $dateTo, $id_kegiatan){
		$this->db->select_sum('penerimaan');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "pengeluaran" => 0, "jenis_laporan" => "tunai", "id_kegiatan" => $id_kegiatan ));
		$query = $this->db->get();
		return $query->row();
	}

	public function last_month_panjar($dateFrom, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('penerimaan');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan <" => $dateFrom, "YEAR(tgl_laporan)" => $yearFrom, "jenis_laporan" => "ajupanjar", "id_kegiatan" => $id_kegiatan ));
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_panjar($dateFrom, $dateTo, $id_kegiatan){
		$this->db->select_sum('penerimaan');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "jenis_laporan" => "ajupanjar", "id_kegiatan" => $id_kegiatan ));
		$query = $this->db->get();
		return $query->row();
	}

	//PENERIMAAN PAJAK

	public function last_month_ppn($dateFrom, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('penerimaan');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan <" => $dateFrom, "YEAR(tgl_laporan)" => $yearFrom, "pengeluaran" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPN', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_ppn($dateFrom, $dateTo, $id_kegiatan){
		$this->db->select_sum('penerimaan');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "pengeluaran" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPN', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function last_month_pph21($dateFrom, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('penerimaan');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan <" => $dateFrom, "YEAR(tgl_laporan)" => $yearFrom, "pengeluaran" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPh 21', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_pph21($dateFrom, $dateTo, $id_kegiatan){
		$this->db->select_sum('penerimaan');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "pengeluaran" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPh 21', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function last_month_pph22($dateFrom, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('penerimaan');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan <" => $dateFrom, "YEAR(tgl_laporan)" => $yearFrom, "pengeluaran" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPh 22', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_pph22($dateFrom, $dateTo, $id_kegiatan){
		$this->db->select_sum('penerimaan');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "pengeluaran" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPh 22', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function last_month_pph23($dateFrom, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('penerimaan');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan <" => $dateFrom, "YEAR(tgl_laporan)" => $yearFrom, "pengeluaran" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPh 23', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_pph23($dateFrom, $dateTo, $id_kegiatan){
		$this->db->select_sum('penerimaan');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "pengeluaran" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPh 23', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function last_month_pph42($dateFrom, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('penerimaan');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan <" => $dateFrom, "YEAR(tgl_laporan)" => $yearFrom, "pengeluaran" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPh Pasal 4 (2)', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_pph42($dateFrom, $dateTo, $id_kegiatan){
		$this->db->select_sum('penerimaan');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "pengeluaran" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPh Pasal 4 (2)', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function last_month_pajak_daerah($dateFrom, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('penerimaan');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan <" => $dateFrom, "YEAR(tgl_laporan)" => $yearFrom, "pengeluaran" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'Pajak Daerah', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_pajak_daerah($dateFrom, $dateTo, $id_kegiatan){
		$this->db->select_sum('penerimaan');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "pengeluaran" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'Pajak Daerah', 'both');
		$query = $this->db->get();
		return $query->row();
	}


	//PENGELUARAN PAJAK
	public function last_month_ppn_bayar($dateFrom, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan <" => $dateFrom, "YEAR(tgl_laporan)" => $yearFrom, "penerimaan" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPN', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_ppn_bayar($dateFrom, $dateTo, $id_kegiatan){
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "penerimaan" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPN', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function last_month_pph21_bayar($dateFrom, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan <" => $dateFrom, "YEAR(tgl_laporan)" => $yearFrom, "penerimaan" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPh 21', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_pph21_bayar($dateFrom, $dateTo, $id_kegiatan){
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "penerimaan" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPh 21', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function last_month_pph22_bayar($dateFrom, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan <" => $dateFrom, "YEAR(tgl_laporan)" => $yearFrom, "penerimaan" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPh 22', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_pph22_bayar($dateFrom, $dateTo, $id_kegiatan){
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "penerimaan" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPh 22', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function last_month_pph23_bayar($dateFrom, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan <" => $dateFrom, "YEAR(tgl_laporan)" => $yearFrom, "penerimaan" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPh 23', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_pph23_bayar($dateFrom, $dateTo, $id_kegiatan){
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "penerimaan" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPh 23', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function last_month_pph42_bayar($dateFrom, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan <" => $dateFrom, "YEAR(tgl_laporan)" => $yearFrom, "penerimaan" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPh Pasal 4 (2)', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_pph42_bayar($dateFrom, $dateTo, $id_kegiatan){
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "penerimaan" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'PPh Pasal 4 (2)', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function last_month_pajak_daerah_bayar($dateFrom, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan <" => $dateFrom, "YEAR(tgl_laporan)" => $yearFrom, "penerimaan" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'Pajak Daerah', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_pajak_daerah_bayar($dateFrom, $dateTo, $id_kegiatan){
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "penerimaan" => 0, "id_kegiatan" => $id_kegiatan ));
		$this->db->like('ket_laporan', 'Pajak Daerah', 'both');
		$query = $this->db->get();
		return $query->row();
	}

	public function last_month_pengembalian($dateFrom, $id_kegiatan){
		$yearFrom = date('Y', strtotime($dateFrom));
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan <" => $dateFrom, "YEAR(tgl_laporan)" => $yearFrom, "jenis_laporan" => "panjar_pengembalian", "id_kegiatan" => $id_kegiatan ));
		$query = $this->db->get();
		return $query->row();
	}

	public function this_month_pengembalian($dateFrom, $dateTo, $id_kegiatan){
		$this->db->select_sum('pengeluaran');
		$this->db->from($this->table);
		$this->db->where(array("tgl_laporan >=" => $dateFrom, "tgl_laporan <=" => $dateTo, "jenis_laporan" => "panjar_pengembalian", "id_kegiatan" => $id_kegiatan ));
		$query = $this->db->get();
		return $query->row();
	}


}
