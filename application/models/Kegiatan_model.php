<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kegiatan_model extends CI_Model {

	var $table = 'kegiatan';
	var $column_order = array(null,'nama_kegiatan','nama_program','rekening_kegiatan','nama_kpa','nama_pptk','nama_user',null); //set column field database for datatable orderable
	var $column_search = array('nama_kegiatan','nama_program','nama_kpa','nama_user', 'nama_pptk'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('program.rekening_program' => 'asc', 'rekening_kegiatan' => 'asc'); // default order

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->join('program', 'program.id_program = kegiatan.id_program', 'inner');
    $this->db->join('user', 'user.id_user = kegiatan.id_user', 'inner');
    $this->db->like('akses_level', 'bendahara', 'after');

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

	public function get_by_id($id_kegiatan)
	{
		$this->db->from($this->table);
		$this->db->where('id_kegiatan',$id_kegiatan);
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data)
	{
		$this->db->set('id_kegiatan', 'REPLACE(UUID(),"-","")', FALSE);
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id_kegiatan)
	{
		$this->db->where('id_kegiatan', $id_kegiatan);
		$this->db->delete($this->table);
	}

	public function all_list()
	{
		$this->db->from($this->table);
		$query = $this->db->get();

		return $query->result();
	}

	public function get_by_user($id_user)
	{
		$this->db->from($this->table);
		$this->db->join('program', 'kegiatan.id_program = program.id_program', 'inner');
		$this->db->where('id_user',$id_user);
		$this->db->where('tahun', $this->session->userdata('tahun'));
		$this->db->group_by('id_kegiatan');
		$query = $this->db->get();
		return $query->result_array();
	}


}
