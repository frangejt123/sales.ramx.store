<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dispatch extends CI_Controller {

	public function index(){
		session_start();
		if(isset($_SESSION["username"])) {
			$this->load->model('modDispatch', "", TRUE);
			$param = $this->input->post(NULL, "true");
			$data["dispatchlist"] = $this->modDispatch->getAll(null)->result_array();
			$this->load->view('dispatchlist', $data);
		}else{
			$this->load->view('login');
		}
	}

	public function detail($id){
		$param["id"] = base64_decode($id);
		$this->load->model('modDriver', "", TRUE);
		$this->load->model('modDispatch', "", TRUE);
		$this->load->model('modDispatchDetail', "", TRUE);
		$this->load->model('modTransactionDetail', "", TRUE);

		$detailparam["dispatch_id"] = $param["id"];
		$dispatch = $this->modDispatch->getAll($param)->row_array();
		$dispatchdetail = $this->modDispatchDetail->getAll($detailparam)->result_array();

		$transactiondetailarr = array();
		foreach($dispatchdetail as $ind => $row){
			$trxparam["transaction_id"] = $row["transaction_id"];
			$transactiondetail = $this->modTransactionDetail->getAll($trxparam)->result_array();
			$transactiondetailarr[$row["transaction_id"]] = $transactiondetail;
		}

		$trx_id = $this->modDispatch->getAvailTransaction(null)->result_array();

		$data["trx"] = $trx_id;
		$data["dispatch"] = $dispatch;
		$data["dispatchdetail"] = $dispatchdetail;
		$data["transactiondetail"] = $transactiondetailarr;

		$drivers = $this->modDriver->getAll(null)->result_array();
		$data["driverlist"] = $drivers;

		session_start();

		if(isset($_SESSION["username"])) {
			$data["store_id"] = $_SESSION["store_id"];
			$this->load->view('dispatchdetail', $data);
		}else{
			$this->load->view('login');
		}
	}

	public function updatestatus(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modDriver', "", TRUE);
		$this->load->model('modDispatch', "", TRUE);
		$this->load->model('modDispatchDetail', "", TRUE);
		$this->load->model('modTransaction', "", TRUE);

		$dparam["dispatch_id"] = $param["id"];
		$dispatchdetail = $this->modDispatchDetail->getAll($dparam)->result_array();
		$p["id"] = $param["id"];
		$dispatch = $this->modDispatch->getAll($p)->row_array();
		$trxupdateErr = 0;
		foreach($dispatchdetail as $ind => $row){
			$trxparam["id"] = $row["transaction_id"];
			$trxparam["status"] = $param["status"];
			$trxparam["driver_id"] = $dispatch["driver"];
			$trxres = $this->modTransaction->update($trxparam);
			if(!$trxres["success"])
				$trxupdateErr++;
		}

		$result = array();
		if($trxupdateErr == 0){
			if($param["status"] == "4"){
				$driverparam["id"] = $dispatch["driver"];
				$driverparam["status"] = "1";//avail
				$this->modDriver->update($driverparam);
				$param["status"] = "2";
			}

			$result = $this->modDispatch->update($param);
		}else{
			$result["success"] = false;
		}

		echo json_encode($result);
	}

	public function newdispatch(){
		$this->load->model('modDriver', "", TRUE);
		$this->load->model('modDispatch', "", TRUE);

		$trx_id = $this->modDispatch->getAvailTransaction(null)->result_array();
		$drivers = $this->modDriver->getAll(null)->result_array();

		$data["trx"] = $trx_id;
		$data["driverlist"] = $drivers;

		session_start();
		if(isset($_SESSION["username"])) {
			$data["store_id"] = $_SESSION["store_id"];
			$this->load->view('dispatchForm', $data);
		}else{
			$this->load->view('login');
		}
	}

	public function save(){
		date_default_timezone_set("Asia/Manila");
		session_start();

		$this->load->model('modDispatch', "", TRUE);
		$this->load->model('modDispatchDetail', "", TRUE);

		$param = $this->input->post(NULL, "true");
		$result = array();
		$param["dispatch"]["datetime"] = date("Y-m-d H:i:s");
		$newid = "";

		if($param["dispatch"]["type"] == "new"){
			$param["dispatch"]["status"] = "0"; //pending
			$result = $this->modDispatch->insert($param["dispatch"]);
			$newid = $result["id"];
		}else{
			$result = $this->modDispatch->update($param["dispatch"]);
			$newid = $param["dispatch"]["id"];
		}

		if($result["success"]){
			if(isset($param["detail"])){
				foreach($param["detail"] as $ind => $row){
					if($row["detailtype"] == "new"){//insert
						$row["dispatch_id"] = $newid;
						$this->modDispatchDetail->insert($row);
					}else{
						$deleteparam["id"] = $row["id"];
						$this->modDispatchDetail->delete($row);
					}
				}
			}
		}

		echo json_encode($result);
	}

	public function update(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modDriver', "", TRUE);

		$param["name"] = ucwords($param["name"]);
		$result = array();
		$result = $this->modDriver->update($param);

		echo json_encode($result);
	}

	public function delete(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modDriver', "", TRUE);
		$result = $this->modDriver->delete($param);

		echo json_encode($result);
	}
}
