<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Validasi_model extends CI_Model {

	var $table = 'panjar';
	var $column_order = array(null,'nama_kegiatan','no_bukti','tgl_bukti','nilai_panjar','ket_panjar','nama_user', 'isVerified', null); //set column field database for datatable orderable
	var $column_search = array('nama_kegiatan','no_bukti','tgl_bukti','ket_panjar','nama_user'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('tgl_bukti' => 'desc'); // default order

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->join('kegiatan', 'panjar.id_kegiatan = kegiatan.id_kegiatan', 'inner');
		$this->db->join('program', 'kegiatan.id_program = program.id_program', 'inner');
		$this->db->join('user', 'kegiatan.id_user = user.id_user', 'inner');
		$this->db->join('spj', 'panjar.id_panjar = spj.id_panjar', 'inner');

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
		$this->db->select('id_panjar, id_kegiatan, no_bukti, DATE_FORMAT(tgl_bukti, "%d %m %Y") as tglbukti, nilai_panjar, ket_panjar');
		$this->db->from($this->table);
		$this->db->where('id_panjar',$id_panjar);
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

	public function delete_by_id($id_panjar)
	{
		$this->db->where('id_panjar', $id_panjar);
		$this->db->delete($this->table);
	}

	public function listing(){
		$query = $this->db->get($this->table);
		return $query->result();
	}

	public function list_program($id_panjar){
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->join('kegiatan', 'panjar.id_kegiatan = kegiatan.id_kegiatan', 'inner');
		$this->db->join('program', 'kegiatan.id_program = program.id_program', 'inner');
		$this->db->join('user', 'kegiatan.id_user = user.id_user', 'inner');
		$this->db->join('spj', 'panjar.id_panjar = spj.id_panjar', 'inner');
		$this->db->where('panjar.id_panjar', $id_panjar);
		$query = $this->db->get();
		return $query->result();
	}

}
