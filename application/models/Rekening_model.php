<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekening_model extends CI_Model {

	var $table = 'rekening';
	var $column_order = array(null,'kode_rekening','uraian_rekening',null); //set column field database for datatable orderable
	var $column_search = array('kode_rekening','uraian_rekening'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('kode_rekening' => 'asc'); // default order

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{

		$this->db->from($this->table);

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

	public function get_by_id($id_rekening)
	{
		$this->db->from($this->table);
		$this->db->where('id_rekening',$id_rekening);
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id_rekening)
	{
		$this->db->where('id_rekening', $id_rekening);
		$this->db->delete($this->table);
	}

	public function listing(){
		$this->db->from($this->table);
		$this->db->order_by('kode_rekening', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_jenis(){

		$this->db->from($this->table);
		$this->db->where('parent', 0);
		$this->db->order_by('kode_rekening', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	public function get_parent($id_rekening){
		$this->db->select('parent');
		$this->db->from($this->table);
		$this->db->where('id_rekening', $id_rekening);
		$query = $this->db->get();
		return $query->row();
	}



}