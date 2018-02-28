<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Panjar_rinci_model extends CI_Model {

	var $table = 'panjar_rinci';

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_by_id($id_panjar_rinci)
	{
		$this->db->from($this->table);
		$this->db->where('id_panjar_rinci',$id_panjar_rinci);
		$query = $this->db->get();

		return $query->row();
	}

	public function get_by_id_panjar($id_panjar)
	{
		$this->db->from($this->table);
		$this->db->join('rekening', 'rekening.id_rekening = panjar_rinci.id_rekening', 'inner');
		$this->db->where('panjar_rinci.id_panjar', $id_panjar);
		$query = $this->db->get();

		return $query->result();
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

	public function delete_by_id_panjar($id_panjar)
	{
		$this->db->where('id_panjar', $id_panjar);
		$this->db->delete($this->table);
	}

	public function listing(){
		$query = $this->db->get($this->table);
		return $query->result();
	}



}
