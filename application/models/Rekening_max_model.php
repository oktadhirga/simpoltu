<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekening_max_model extends CI_Model {

	var $table = 'rekening_max';
	var $column_order = array(null,'kode_rekening','uraian_rekening','jumlah',null); //set column field database for datatable orderable
	var $column_search = array('kode_rekening','uraian_rekening'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('kode_rekening' => 'asc'); // default order


	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->join('rekening', 'rekening_max.id_rekening = rekening.id_rekening', 'inner');

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


	public function get_by_id_kegiatan($id_kegiatan)
	{
		$this->db->from($this->table);
		$this->db->join('rekening', 'rekening_max.id_rekening = rekening.id_rekening', 'inner');
		$this->db->where(array('rekening_max.id_kegiatan' => $id_kegiatan, 'rekening.parent !=' => 0));
		$this->db->order_by('rekening.kode_rekening', 'asc');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_by_2id($id_kegiatan, $id_rekening)
	{
		$this->db->from($this->table);
		$this->db->join('rekening', 'rekening_max.id_rekening = rekening.id_rekening', 'inner');
		$this->db->where(array('id_kegiatan' => $id_kegiatan, 'rekening_max.id_rekening' => $id_rekening));
		$query = $this->db->get();

		return $query->row();
	}

	public function get_by_id($id_max)
	{
		$this->db->from($this->table);
		$this->db->where('id_max',$id_max);
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

	public function delete_by_id($id_max)
	{
		$this->db->where('id_max', $id_max);
		$this->db->delete($this->table);
	}

	public function delete_by_2id($id_kegiatan, $id_rekening)
	{
		$this->db->where(array('id_kegiatan' => $id_kegiatan, 'id_rekening' => $id_rekening));
		$this->db->delete($this->table);
	}

	public function listing(){
		$query = $this->db->get($this->table);
		return $query->result();
	}

	public function sum_anggaran($parent, $id_kegiatan){
		$this->db->select_sum('jumlah');
		$this->db->from($this->table);
		$this->db->join('rekening', 'rekening_max.id_rekening = rekening.id_rekening', 'inner' );
		$this->db->where(array('rekening_max.id_kegiatan' => $id_kegiatan, 'rekening.parent' => $parent));
		$query = $this->db->get();
		return $query->row();
	}


}
