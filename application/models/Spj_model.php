<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Spj_model extends CI_Model {

	var $table = 'spj';
	var $column_order = array('id_spj','no_bukti','nilai_panjar','no_spj','tgl_spj','ket_spj',null); //set column field database for datatable orderable
	var $column_search = array('no_spj','tgl_spj','ket_spj'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('id_spj' => 'asc'); // default order

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->join('panjar', 'spj.id_panjar = panjar.id_panjar', 'inner');
		$this->db->join('kegiatan', 'panjar.id_kegiatan = kegiatan.id_kegiatan', 'inner');


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

	public function get_by_id($id_spj)
	{
		$this->db->select('id_spj, id_panjar, no_spj, DATE_FORMAT(tgl_spj, "%d %m %Y") as tglspj, ket_spj, no_pengesahan,DATE_FORMAT(tgl_pengesahan, "%d %m %Y") as tgl_pengesahan, isVerified');
		$this->db->from($this->table);
		$this->db->where('id_spj',$id_spj);
		$query = $this->db->get();

		return $query->row();
	}


	//cek_status by id_spj
	public function cek_status($id_spj){
		$this->db->select('isVerified');
		$this->db->from($this->table);
		$this->db->where('id_spj', $id_spj);
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row();
	}

	//get_row by id_panjar
	public function get_status($id_panjar){
		$this->db->select('isVerified');
		$this->db->from($this->table);
		$this->db->where('id_panjar', $id_panjar);
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->row();
	}

	public function save($data)
	{
		$this->db->set('id_spj', 'REPLACE(UUID(),"-","")', FALSE);
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id_spj)
	{
		$this->db->where('id_spj', $id_spj);
		$this->db->delete($this->table);
	}

	public function listing(){
		$query = $this->db->get($this->table);
		return $query->result();
	}

	public function list_program($id_spj){
		$this->db->from($this->table);
		$this->db->join('panjar','spj.id_panjar = panjar.id_panjar', 'inner');
		$this->db->join('kegiatan', 'panjar.id_kegiatan = kegiatan.id_kegiatan', 'inner');
		$this->db->join('program', 'kegiatan.id_program = program.id_program');
		$this->db->where('spj.id_spj', $id_spj);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_by_user($id_user)
	{
		$this->db->from($this->table);
		$this->db->join('panjar','spj.id_panjar = panjar.id_panjar','inner');
		$this->db->join('kegiatan', 'panjar.id_kegiatan = kegiatan.id_kegiatan', 'inner');
		$this->db->where('id_user',$id_user);
		$this->db->group_by('id_spj');
		$query = $this->db->get();

		return $query->result();
	}


}
