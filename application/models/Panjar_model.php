<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Panjar_model extends CI_Model {

	var $table = 'panjar';
	var $column_order = array(null,'nama_kegiatan','no_bukti','tgl_bukti','nilai_panjar','ket_panjar' ,null); //set column field database for datatable orderable
	var $column_search = array('nama_kegiatan','no_bukti','tgl_bukti','ket_panjar'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('panjar.tgl_bukti' => 'desc'); // default order

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$this->db->from($this->table);
		$this->db->join('kegiatan', 'panjar.id_kegiatan = kegiatan.id_kegiatan', 'inner');
		$this->db->join('program', 'kegiatan.id_program = program.id_program', 'inner');


		$i = 0;

		foreach ($this->column_search as $item) // loop column
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{

				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}

		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		}
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	public function get_by_id($id_panjar)
	{
		$this->db->select('id_panjar, id_kegiatan, no_bukti, DATE_FORMAT(tgl_bukti, "%d-%m-%Y") as tglbukti, nilai_panjar, ket_panjar');
		$this->db->from($this->table);
		$this->db->where('id_panjar',$id_panjar);
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data)
	{
		//set id column value as UUID
		// $this->db->set('id_panjar', 'REPLACE(UUID(),"-","")', FALSE);
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id_panjar)
	{
		$this->db->where('id_panjar', $id_panjar);
		$this->db->delete($this->table);
	}

	public function listing(){
		$query = $this->db->get($this->table);
		return $query->result();
	}

	public function listing_with_kegiatan(){
		$this->db->from($this->table);
		$this->db->join('kegiatan', 'panjar.id_kegiatan = kegiatan.id_kegiatan', 'inner');
		$query = $this->db->get();
		return $query->result();
	}

	public function listing_detail(){
		$this->db->from($this->table);
		$this->db->join('kegiatan', 'panjar.id_kegiatan = kegiatan.id_kegiatan', 'inner');
		$this->db->join('spj','panjar.id_panjar = spj.id_panjar', 'inner');
		$this->db->join('spj_detail','spj.id_spj = spj_detail.id_spj', 'inner');
		$this->db->join('rekening', 'rekening.id_rekening = spj_detail.id_rekening', 'inner');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_by_user($id_user)
	{
		$this->db->from($this->table);
		$this->db->join('kegiatan', 'panjar.id_kegiatan = kegiatan.id_kegiatan', 'inner');
		$this->db->join('program', 'kegiatan.id_program = program.id_program', 'inner');
		$this->db->where('id_user',$id_user);
		$this->db->where('tahun', $this->session->userdata('tahun'));
		$query = $this->db->get();

		return $query->result();
	}

	public function get_detail($id_panjar)
	{
		$this->db->from($this->table);
		$this->db->join('spj','panjar.id_panjar = spj.id_panjar', 'inner');
		$this->db->join('spj_detail','spj.id_spj = spj_detail.id_spj', 'inner');
		$this->db->join('rekening', 'rekening.id_rekening = spj_detail.id_rekening', 'inner');
		$this->db->where('panjar.id_panjar',$id_panjar);
		$query = $this->db->get();

		return $query->result();
	}

	public function list_panjar_rinci_sebelumnya($id_kegiatan, $id_rekening, $tgl_bukti){
		$year_tgl_bukti = date('Y', strtotime($tgl_bukti));
		$this->db->select('panjar.id_panjar, panjar.id_kegiatan, panjar.no_bukti, panjar.tgl_bukti, panjar.nilai_panjar, panjar.ket_panjar, panjar_rinci.id_rekening, SUM(panjar_rinci.jumlah) as sum_jumlah_rinci');
		$this->db->from($this->table);
		$this->db->join('panjar_rinci','panjar.id_panjar = panjar_rinci.id_panjar', 'inner');
		$this->db->where(array('panjar.id_kegiatan' => $id_kegiatan, 'panjar_rinci.id_rekening' => $id_rekening, 'panjar.tgl_bukti <' => $tgl_bukti, 'YEAR(panjar.tgl_bukti)' => $year_tgl_bukti));
		$this->db->group_by(array('panjar_rinci.id_rekening', 'panjar.id_kegiatan'));
		$query = $this->db->get();

		return $query->row();
	}

	public function list_panjar_rinci_now($id_panjar, $id_rekening){
		$this->db->select('panjar.id_panjar, panjar.id_kegiatan, panjar.no_bukti, panjar.tgl_bukti, panjar.nilai_panjar, panjar.ket_panjar, panjar_rinci.id_rekening, SUM(panjar_rinci.jumlah) as sum_jumlah_rinci');
		$this->db->from($this->table);
		$this->db->join('panjar_rinci','panjar.id_panjar = panjar_rinci.id_panjar', 'inner');
		$this->db->where(array('panjar.id_panjar' => $id_panjar, 'panjar_rinci.id_rekening' => $id_rekening));
		$this->db->group_by(array('panjar_rinci.id_rekening', 'panjar.id_kegiatan'));
		$query = $this->db->get();

		return $query->row();
	}

}
