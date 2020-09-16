<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function index(){
		session_start();
		if(isset($_SESSION["username"])) {
			
			if ($_SESSION['access_level'] == 2) {
				redirect('/order');
			}

			$this->load->model('modTransaction', "", TRUE);
			$this->load->model('modDriver', "", TRUE);
			$this->load->model('modCity', "", TRUE);

//			$param["sort_delivery_date"] = true;
//			$param["no_image"] = true;
//			$new_transaction = $this->modTransaction->getAll($param)->result_array();
//			$new_param["old_transaction"] = true;
//			$old_transaction = $this->modTransaction->getAll($new_param)->result_array();
//			$data["transaction"] = array_merge($new_transaction, $old_transaction);

			$transaction = $this->modTransaction->getAll(NULL)->result_array();
			$data["city"] = $this->modCity->getAll(null)->result_array();
			$data["lastid"] = $this->modTransaction->getLastTransactionID(null)->row_array();
			$drivers = $this->modDriver->getAll(null)->result_array();
			$data["driverlist"] = $drivers;

			$order = array();
			foreach($transaction as $ind => $row){
				$transdate = date("mdY", strtotime($row["datetime"]));
				json_encode($order[$row["id"]] = $transdate.'-'.sprintf("%04s", $row["id"]));
			}

			$data["orderids"] = json_encode($order);

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
			$_SESSION["store_id"] = "1";
			$_SESSION["access_level"] = $res["access_level"];
		}

		echo $count;
	}

	function logout(){
		session_start();
		unset($_SESSION["username"]);
		unset($_SESSION["store_id"]);
		session_destroy();
		echo "success";
	}
}
