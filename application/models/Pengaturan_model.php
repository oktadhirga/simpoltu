<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengaturan_model extends CI_Model {

	var $table = 'pengaturan';

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_by_id($id_bidang)
	{
		$this->db->from($this->table);
		$this->db->where('id_bidang',$id_bidang);
		$query = $this->db->get();

		return $query->row();
	}

	public function save($data)
	{
		//set id column value as UUID
		//$this->db->set('id_bidang', 'REPLACE(UUID(),"-","")', FALSE);
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id_bidang)
	{
		$this->db->where('id_bidang', $id_bidang);
		$this->db->delete($this->table);
	}

	public function listing(){
		$query = $this->db->get($this->table);
		return $query->result();
	}


}
