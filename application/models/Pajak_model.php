<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pajak_model extends CI_Model {

	var $table = 'pajak_spj_detail';
	var $column_order = array(null,'pajak','nilai_pajak','tgl_setor_pajak',null); //set column field database for datatable orderable
	var $column_search = array('pajak','nilai_pajak','tgl_setor_pajak'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('id' => 'asc'); // default order

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$this->db->select('*');
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


	public function get_by_id($id_panjar_rinci)
	{
		$this->db->from($this->table);
		$this->db->where('id_panjar_rinci',$id_panjar_rinci);
		$query = $this->db->get();

		return $query->row();
	}

	public function get_by_id_spj_detail($id_spj_detail)
	{
		$this->db->from($this->table);
		//$this->db->join('rekening', 'rekening.id_rekening = panjar_rinci.id_rekening', 'inner');
		$this->db->where('id_spj_detail', $id_spj_detail);
		$query = $this->db->get();

		return $query->result();
	}
	public function get_by_2cat($id_spj_detail, $pajak)
	{
		$this->db->select('id, id_spj_detail, pajak, nilai_pajak,  DATE_FORMAT(tgl_setor_pajak, "%d %m %Y") as tgl_setor_pajak');
		$this->db->from($this->table);
		$this->db->where(array('id_spj_detail' => $id_spj_detail, 'pajak' => $pajak ));
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data)
	{
		$this->db->set('id', 'REPLACE(UUID(),"-","")', FALSE);
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('id', $id);
		$this->db->delete($this->table);
	}

	public function list_pajak($id_spj_detail){
		$this->db->select('GROUP_CONCAT(pajak) as pajak');
		$this->db->where('id_spj_detail', $id_spj_detail);
		$query = $this->db->get($this->table);
		return $query->row();
	}

	public function listing(){
		$query = $this->db->get($this->table);
		return $query->result();
	}


}
