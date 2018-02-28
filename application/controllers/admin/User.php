<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model','user');
		$this->login_lib->cek_login();
		$this->login_lib->cek_bendahara();
	}

	public function index()
	{
		$data = array('title' => 'Halaman User',
						  		'isi'  => 'admin/user/list_user',
									'foot' => 'admin/user/foot_user',
									'breadcrum1' => 'Data Master',
									'breadcrum2' => 'User'
								);

		$this->load->view('admin/layout/wrapper', $data);
	}

	public function ajax_list()
	{
		$list = $this->user->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		foreach ($list as $user) {
			$no++;
			$row = array();
			$row[] = $i;
			$row[] = $user->nama_user;
			$row[] = $user->username;
			$row[] = $user->akses_level;


			//add html for action
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_user('."'".$user->id_user."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_user('."'".$user->id_user."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';

			$data[] = $row;
			$i++;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->user->count_all(),
						"recordsFiltered" => $this->user->count_filtered(),
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}

	public function ajax_edit($id_user)
	{
		$data = $this->user->get_by_id($id_user);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$this->_validate_add();
		$data = array(
				'nama_user' => $this->input->post('nama_user'),
				'nip_user' => $this->input->post('nip_user'),
				'username' => $this->input->post('username'),
				'password' => $this->input->post('password'),
				'akses_level' => $this->input->post('akses_level'),
			);
		$insert = $this->user->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update()
	{
		$this->_validate_update();
		$data = array(
				'nama_user' => $this->input->post('nama_user'),
				'nip_user' => $this->input->post('nip_user'),
				'username' => $this->input->post('username'),
				'password' => $this->input->post('password'),
				'akses_level' => $this->input->post('akses_level'),
			);
		$this->user->update(array('id_user' => $this->input->post('id_user')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_delete($id_user)
	{
		$this->user->delete_by_id($id_user);
		echo json_encode(array("status" => TRUE));
	}


	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;


		if($this->input->post('nama_user') == '')
		{
			$data['inputerror'][] = 'nama_user';
			$data['error_string'][] = 'Nama user harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('nip_user') == '')
		{
			$data['inputerror'][] = 'nip_user';
			$data['error_string'][] = 'NIP user harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('username') == '')
		{
			$data['inputerror'][] = 'username';
			$data['error_string'][] = 'Username harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('password') == '')
		{
			$data['inputerror'][] = 'password';
			$data['error_string'][] = 'Password user harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('akses_level') == '')
		{
			$data['inputerror'][] = 'akses_level';
			$data['error_string'][] = 'Akses level user harus diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	private function _validate_add()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;


		if($this->input->post('nama_user') == '')
		{
			$data['inputerror'][] = 'nama_user';
			$data['error_string'][] = 'Nama user harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('nip_user') == '')
		{
			$data['inputerror'][] = 'nip_user';
			$data['error_string'][] = 'NIP user harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('username') == '')
		{
			$data['inputerror'][] = 'username';
			$data['error_string'][] = 'Username harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('password') == '')
		{
			$data['inputerror'][] = 'password';
			$data['error_string'][] = 'Password user harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('akses_level') == '')
		{
			$data['inputerror'][] = 'akses_level';
			$data['error_string'][] = 'Akses level user harus diisi';
			$data['status'] = FALSE;
		}

		$this->form_validation->set_rules('username','User Name', 'is_unique[user.username]');
		if ($this->form_validation->run() == FALSE)
		{
			$data['inputerror'][] = 'username';
			$data['error_string'][] = 'Username sudah terdaftar';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

	private function _validate_update()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;


		if($this->input->post('nama_user') == '')
		{
			$data['inputerror'][] = 'nama_user';
			$data['error_string'][] = 'Nama user harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('nip_user') == '')
		{
			$data['inputerror'][] = 'nip_user';
			$data['error_string'][] = 'NIP user harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('username') == '')
		{
			$data['inputerror'][] = 'username';
			$data['error_string'][] = 'Username harus diisi';
			$data['status'] = FALSE;
		}

		$id_user = $this->input->post('id_user');
		$username = $this->input->post('username');
		$list = $this->user->get_by_id($id_user);
		if ($list->username !== $username ) {
			$this->form_validation->set_rules('username','User Name', 'is_unique[user.username]');
			if ($this->form_validation->run() == FALSE)
			{
				$data['inputerror'][] = 'username';
				$data['error_string'][] = 'Username sudah terdaftar';
				$data['status'] = FALSE;
			}
		}

		if($this->input->post('password') == '')
		{
			$data['inputerror'][] = 'password';
			$data['error_string'][] = 'Password user harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('akses_level') == '')
		{
			$data['inputerror'][] = 'akses_level';
			$data['error_string'][] = 'Akses level user harus diisi';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}

	}


}
