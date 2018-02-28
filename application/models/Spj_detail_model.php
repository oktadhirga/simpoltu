<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Spj_detail_model extends CI_Model {

	var $table = 'spj_detail';
	var $column_order = array('id_spj_detail', null,'kode_rekening','uraian_rekening','nilai_spj', 'no_spj_detail', 'tgl_spj_detail', null); //set column field database for datatable orderable
	var $column_search = array('kode_rekening','uraian_rekening','nilai_spj', 'no_spj_detail', 'tgl_spj_detail'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('tgl_spj_detail' => 'desc'); // default order

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->join('spj', 'spj_detail.id_spj = spj.id_spj', 'inner');
		$this->db->join('rekening','spj_detail.id_rekening = rekening.id_rekening', 'inner');

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

	public function get_by_id($id_spj_detail)
	{
		$this->db->select('id_spj_detail, id_spj, spj_detail.id_rekening, kode_rekening, nilai_spj, DATE_FORMAT(tgl_spj_detail, "%d %m %Y") as tglspjdetail, no_spj_detail, ket_spj_detail');
		$this->db->from($this->table);
		$this->db->join('rekening','spj_detail.id_rekening = rekening.id_rekening', 'inner');
		$this->db->where('id_spj_detail',$id_spj_detail);
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data)
	{
		//set id column value as UUID
		$this->db->set('id_spj_detail', 'REPLACE(UUID(),"-","")', FALSE);
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id_spj_detail)
	{
		$this->db->where('id_spj_detail', $id_spj_detail);
		$this->db->delete($this->table);
	}

	public function listing($id_spj){
		$this->db->from($this->table);
		$this->db->join('spj', 'spj_detail.id_spj = spj.id_spj', 'inner');
		$this->db->join('rekening','spj_detail.id_rekening = rekening.id_rekening', 'inner');
		$this->db->where('spj.id_spj', $id_spj);
		$query = $this->db->get();
		return $query->result();
	}

	public function sum_spj($id_spj)
	{
		$this->db->select_sum('nilai_spj');
		$this->db->where('id_spj', $id_spj);
		$query = $this->db->get($this->table);
		return $query->row();
	}

	public function sum_spj_by_panjar($id_panjar)
	{
		$this->db->select_sum('nilai_spj');
		$this->db->from($this->table);
		$this->db->join('spj','spj_detail.id_spj = spj.id_spj', 'inner');
		$this->db->where('id_panjar', $id_panjar);
		$query = $this->db->get();
		return $query->row();
	}

	//ADD TITLE FOR PANJAR SAH
	public function get_panjar_sah($id_spj_detail){
		$this->db->select('spj.no_pengesahan, spj.tgl_pengesahan');
		$this->db->from($this->table);
		$this->db->join('spj','spj_detail.id_spj = spj.id_spj', 'inner');
		$this->db->where('spj_detail.id_spj_detail', $id_spj_detail);
		$query = $this->db->get();
		return $query->row();
	}
}
