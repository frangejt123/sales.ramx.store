<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function index(){
		session_start();
		if(isset($_SESSION["username"])) {
			$this->load->model('modTransaction', "", TRUE);
			$param["sort_delivery_date"] = true;
			$param["no_image"] = true;
			$new_transaction = $this->modTransaction->getAll($param)->result_array();
			$new_param["old_transaction"] = true;
			$old_transaction = $this->modTransaction->getAll($new_param)->result_array();
			$data["transaction"] = array_merge($new_transaction, $old_transaction);

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
			$_SESSION["name"] = $res["name"];
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
