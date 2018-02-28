<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profil extends CI_Controller {
	var $image;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model','user');
		$this->login_lib->cek_login();
		$this->login_lib->cek_admin();
	}


	public function ajax_edit($id_user)
	{
		$data = $this->user->get_by_id($id_user);
		echo json_encode($data);
	}

	public function ajax_dashboard($id_user)
	{
		$data = $this->user->get_by_idd($id_user);
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
		$this->do_upload();
		$data = array(
				'nama_user' => $this->input->post('nama_user'),
				'nip_user' => $this->input->post('nip_user'),
				'username' => $this->input->post('username'),
				'password' => $this->input->post('password'),
				'pic_user' => $this->image,
			);

			if($this->do_upload()){
				$data = $this->array_push_assoc($data, 'pic_user', $this->input->post('id_user'));
			}

		$this->user->update(array('id_user' => $this->input->post('id_user')), $data);
		$pesan = array("status" => TRUE, "notif" => "<h3>Sukses, profil berhasil diubah!</h3>
		<p>Silahkan login kembali untuk melihat perubahan.</p>");
		echo json_encode($pesan);

	}

	public function ajax_delete($id_user)
	{
		$this->user->delete_by_id($id_user);
		echo json_encode(array("status" => TRUE));
	}

	public function delete_profil($id_user){
		$data = array(
				'pic_user' => '',
			);
		$this->user->update(array('id_user' => $id_user), $data);
		echo json_encode(array("status" => TRUE));
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

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}

	}

	public function array_push_assoc($array, $key, $value){
			$array[$key] = $value;
			return $array;
		}

	public function do_upload()
	{
		$id_user = $this->input->post('id_user');


		if (isset($_FILES['pic_user']['name']) && !empty($_FILES['pic_user']['name'])) {
			$config['upload_path']          = './assets/profile/';
			$config['allowed_types']        = 'gif|jpg|png';
			$config['overwrite']           	= TRUE;
			$config['file_name']						= $id_user;

			$this->load->library('upload', $config);

			if ( ! $this->upload->do_upload('pic_user'))
			{
				$this->image ='';
			}
			else
			{
				$this->image = $this->upload->data('file_name');
			}
		}
	}

	public function cek_image(){
			$file = $_FILES['pic_user'];
			/* Allowed file extension */
			$allowedExtensions = array("gif", "jpeg", "jpg", "png", "svg");
			$fileExtension = explode(".", $file["name"]);
			/* Contains file extension */
			$extension = end($fileExtension);
			/* Allowed Image types */
			$types = array('image/gif', 'image/png', 'image/x-png', 'image/pjpeg', 'image/jpg', 'image/jpeg','image/svg+xml');
			if(in_array(strtolower($file['type']), $types)
			    // Checking for valid image type
			    && in_array(strtolower($extension), $allowedExtensions)
			    // Checking for valid file extension
			    && !$file["error"] > 0)
			    // Checking for errors if any
			    {
						echo json_encode(array("status" => TRUE));

			} else {
			    echo json_encode(array("status" => FALSE, "html" => "Support only png/jpg images"));
			}
	}

}
