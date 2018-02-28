<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_lib {

	// SET SUPER GLOBAL
	var $CI = NULL;
	public function __construct() {
		$this->CI =& get_instance();
	}

	// Login
	public function login($username, $password, $tahun) {
		// Query untuk pencocokan data
		$query = $this->CI->db->get_where('user', array(
										'username' => $username,
										'password' => $password
										));

		// Jika ada hasilnya
		if($query->num_rows() == 1) {
			$row 	= $this->CI->db->get_where('user', array('username' => $username));
			$user 	= $row->row();
			$id_user 	= $user->id_user;
			$nama_user	= $user->nama_user;
			$akses_level	= $user->akses_level;
			$nip_user = $user->nip_user;
			$pic_user = $user->pic_user;
			// $_SESSION['username'] = $username;
			// $this->CI->session->set_userdata('username', $username);
			$this->CI->session->set_userdata('akses_level', $akses_level);
			$this->CI->session->set_userdata('nama_user', $nama_user);
			// $this->CI->session->set_userdata('nip_user', $nip_user);
			$this->CI->session->set_userdata('id_login', uniqid(rand()));
			$this->CI->session->set_userdata('id_user', $id_user);

			// $this->CI->session->set_userdata('pic_user', $pic_user);
			// akses_level admin
      if ($akses_level == 'admin') {
          redirect(base_url('admin/program'));
      } else if ($akses_level == 'bendahara' || $akses_level == 'bendahara_gaji') {
					$this->CI->session->set_userdata('tahun', $tahun);
			    redirect(base_url('bendahara/dasbor'));
      } else {
					$this->CI->session->sess_destroy();
          $this->CI->session->set_flashdata('sukses','Maaf, anda gagal login');
          redirect(base_url());
      }

      //jika tidak ada hasilnya
		} else {
			$this->CI->session->set_flashdata('sukses','Oopss.. Username/password salah');
			redirect(base_url());
		}
		return false;
	}

	// Cek login
	public function cek_login() {
		$username = $this->CI->session->userdata('username');
		$akses_level = $this->CI->session->userdata('akses_level');
		if ($username == '' && $akses_level == '') {
			$this->CI->session->set_flashdata('sukses','Oops...silakan login dulu');
			redirect(base_url());
		}
	}

	public function cek_admin()
	{
		if ($this->CI->session->userdata('akses_level') == 'admin') {
				redirect(base_url('admin/program'));
		}
	}


	public function cek_bendahara()
	{
		if ($this->CI->session->userdata('akses_level') == 'bendahara') {
				redirect(base_url('bendahara/dasbor'));
		}
	}

	// Cek in login
	public function cek_in_login() {
		$akses_level = $this->CI->session->userdata('akses_level');
		if ($akses_level == 'admin') {
				redirect(base_url('admin/program'));
		} else if ($akses_level == 'bendahara') {
				redirect(base_url('bendahara/dasbor'));
		}
	}

	// Logout
	public function logout() {
		$this->CI->session->sess_destroy();
		$this->CI->session->set_flashdata('sukses','Terimakasih, Anda berhasil sign out');
		redirect(base_url());
	}

}
