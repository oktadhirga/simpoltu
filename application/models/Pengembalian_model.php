<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengembalian_model extends CI_Model {

	var $table = 'pengembalian';
	var $column_order = array('nama_kegiatan','no_bukti','no_pengembalian','tgl_pengembalian','nilai_pengembalian','ket_pengembalian'); //set column field database for datatable orderable
	var $column_search = array('no_pengembalian','tgl_pengembalian','nilai_pengembalian','ket_pengembalian'); //set column field database for datatable searchable just firstname , lastname , address are searchable
	var $order = array('id_pengembalian' => 'asc'); // default order

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->join('panjar', 'pengembalian.id_panjar = panjar.id_panjar', 'inner');
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

	public function get_by_id($id_pengembalian)
	{
		$this->db->select('id_pengembalian, pengembalian.id_panjar, no_pengembalian, DATE_FORMAT(tgl_pengembalian, "%d %m %Y") as tglpengembalian, nilai_pengembalian, ket_pengembalian, panjar.id_kegiatan, panjar.no_bukti');
		$this->db->from($this->table);
		$this->db->join('panjar','pengembalian.id_panjar = panjar.id_panjar', 'inner');
		$this->db->where('id_pengembalian', $id_pengembalian);
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data)
	{
		$this->db->set('id_pengembalian', 'REPLACE(UUID(),"-","")', FALSE);
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id_pengembalian)
	{
		$this->db->where('id_pengembalian', $id_pengembalian);
		$this->db->delete($this->table);
	}

	public function listing(){
		$query = $this->db->get($this->table);
		return $query->result();
	}

	public function get_by_user($id_user)
	{
		$this->db->from($this->table);
		$this->db->join('panjar','pengembalian.id_panjar = panjar.id_panjar', 'inner');
		$this->db->join('kegiatan', 'panjar.id_kegiatan = kegiatan.id_kegiatan', 'inner');
		$this->db->where('id_user',$id_user);
		$this->db->group_by('id_pengembalian');
		$query = $this->db->get();

		return $query->result();
	}

	public function get_detail($id_panjar)
	{
		$this->db->from($this->table);
		$this->db->join('panjar','pengembalian.id_panjar = panjar.id_panjar', 'inner');
		$this->db->where('panjar.id_panjar',$id_panjar);
		$query = $this->db->get();

		return $query->result();
	}

}
