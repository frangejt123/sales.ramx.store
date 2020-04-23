<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require '../kioskpos/escpos-php/vendor/autoload.php';

class Login extends CI_Controller {

	public function index()
	{
		session_start();
		if(isset($_SESSION["username"])) {
			$this->load->model('modTransaction', "", TRUE);
			$data["transaction"] = $this->modTransaction->getAll(null)->result_array();
			$data["lastid"] = $this->modTransaction->getLastTransactionID(null)->row_array();
			$this->load->view('orderlist', $data);
		}else{
			$this->load->view('login');
		}
	}

	public function auth(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modUser', "", TRUE);

		$param["password"] = md5($param["password"]);
		$res = $this->modUser->getAll($param)->row_array();
		$count = $this->modUser->getAll($param)->num_rows();
		if($count == 1) {
			session_start();
			$_SESSION["username"] = $param["username"];
			$_SESSION["id"] = $res["id"];
			$_SESSION["access_level"] = $res["access_level"];
		}

		echo $count;

	}

	function logout(){
		session_start();
		unset($_SESSION["username"]);
		session_destroy();
		echo "success";
	}
}
