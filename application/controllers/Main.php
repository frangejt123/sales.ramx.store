<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require '../sales.ramx.store/escpos-php/vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class Main extends CI_Controller {

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

	public function pos(){
		$this->load->model('modProduct', "", TRUE);
		$this->load->model('modCustomer', "", TRUE);

		$data["product"] = $this->modProduct->getAll(null)->result_array();
		$customer = $this->modCustomer->getAll(null)->result_array();
		$customerarray = array();
		$nameopt = array();
		foreach($customer as $ind => $row){
			$customerarray[$row["id"]] = array();
			$customerarray[$row["id"]]["name"] = $row["name"];
			$customerarray[$row["id"]]["contact_number"] = $row["contact_number"];
			$customerarray[$row["id"]]["delivery_address"] = $row["delivery_address"];
			json_encode($nameopt[$row["id"]] = $row["name"]);
		}

		$data["customerdetail"] = json_encode($customerarray);
		$data["namelist"] = json_encode($nameopt);

		session_start();
		if(isset($_SESSION["username"])) {
			$this->load->view('main', $data);
		}else{
			$this->load->view('login');
		}
	}

	public function orderdetail($orderid){
		$orderid = base64_decode($orderid);
		$this->load->model('modTransaction', "", TRUE);
		$this->load->model('modTransactionDetail', "", TRUE);
		$param["id"] = $orderid;
		$detailparam["transaction_id"] = $orderid;
		$transaction = $this->modTransaction->getAll($param)->row_array();
		$transactiondetail = $this->modTransactionDetail->getAll($detailparam)->result_array();

		$data["transaction"] = $transaction;
		$data["transactiondetail"] = $transactiondetail;
		session_start();
		if(isset($_SESSION["username"])) {
			$this->load->view('orderdetail', $data);
		}else{
			$this->load->view('login');
		}
	}

	public function neworder(){
		set_time_limit ( 120	);
		$this->load->model('modTransaction', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$lastid = $param["lastid"];
		while(true){
			$newlastid = $this->modTransaction->getLastTransactionID(null)->row_array();
			if($newlastid["id"] != $lastid){
				$data["transaction"]["orders"] = $this->modTransaction->getNewTrasaction($lastid)->result_array();
				$data["transaction"]["lastid"] = $newlastid["id"];
				echo json_encode($data["transaction"]);
				break;
			}else{
				sleep(1);
				continue;
			}
		}
	}

	public function checkchanges(){
		set_time_limit ( 120	);
		$this->load->model('modTransaction', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$id = $param["id"];
		while(true){
			$data = $this->modTransaction->haschanges($id)->row_array();
			if($data["haschanges"] == 1 || $data["status"] == 3){
				echo json_encode($data);
				break;
			}else{
				sleep(1);
				continue;
			}
		}
	}

	public function updateChanges(){
		$this->load->model('modTransaction', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$id = $param["id"];
		$this->modTransaction->updateChanges($id);
	}

	public function settle(){
		date_default_timezone_set ( "Asia/Manila");
		session_start();
		$this->load->model('modTransaction', "", TRUE);
		$this->load->model('modTransactionDetail', "", TRUE);
		$this->load->model('modCustomer', "", TRUE);

		$param = $this->input->post(NULL, "true");
		$param["trans"]["datetime"] = date("Y-m-d H:i:s");

		if(isset($param["newcustomer"])){
			$customerres = $this->modCustomer->insert($param["newcustomer"]);
			$param["trans"]["customer_id"] = $customerres["id"];
		}else{
			$customerres = $this->modCustomer->update($param["customerdetail"]);
		}

		$param["trans"]["user_id"] = $_SESSION["id"];
		$param["trans"]["delivery_date"] = date("Y-m-d", strtotime($param["trans"]["delivery_date"]));
		if($param["trans"]["haschanges"] == 1){
			$param["trans"]["id"] = $param["trans"]["transaction_id"];
			$result = $this->modTransaction->update($param["trans"]);
		}else{
			unset($param["trans"]["transaction_id"]);
			$result = $this->modTransaction->insert($param["trans"]);
		}

		foreach($param["detail"] as $ind => $row){
			$row["transaction_id"] = $result["id"];
			if($row["status"] == "new")
				$this->modTransactionDetail->insert($row);
			else if($row["status"] == "edited")
				$this->modTransactionDetail->update($row);
			else if($row["status"] == "deleted")
				$this->modTransactionDetail->delete($row);
		}

		echo json_encode($result);
	}

	public function printtest(){
		$this->load->model('modTransaction', "", TRUE);
		$this->load->model('modTransactionDetail', "", TRUE);
		$lasttrans = $this->modTransaction->getLastTransactionID(null)->row_array();

		$detailparam["transaction_id"] = $lasttrans["id"];
		//$details["id"] = $lasttrans["id"];
		// $details["table_number"] = $lasttrans["table_number"];
		// $details["transaction_time"] = $lasttrans["datetime"];
		$details = $this->modTransactionDetail->getAll($detailparam)->result_array();

		$connector = new NetworkPrintConnector("192.168.1.108", 9100);
		$printer = new Printer($connector);
		/* Initialize */
		$printer -> initialize();

		$printer->setFont(Printer::FONT_A);
		$printer -> setTextSize(2, 1);
		/* Text */
		$printer -> text("TABLE NUMBER : #".$lasttrans["table_number"]."\n\n");
		$printer -> text(date("m/d/Y H:i:s", strtotime($lasttrans["datetime"]))."\n");
		$printer -> text("\n\n");

		foreach($details as $ind => $row){
			$printer -> setTextSize(2, 1);
			$printer -> text($row["quantity"]);
			$printer -> setTextSize(2, 1);
			$printer -> text(" ".$row["description"]."\n\n");
		}

		$printer -> text("\n\n");
		$printer->setJustification(Printer::JUSTIFY_CENTER);

		$printer -> setTextSize(1, 1);
		$printer -> text("TRANSACTION #".$lasttrans["id"]."\n");
		$printer -> text("RIBSHACK GRILL CORPORATION\n");

		$printer -> cut();
		$printer -> close();
	}

	public function ut($id){
		$this->load->model('modProduct', "", TRUE);
		$this->load->model('modCustomer', "", TRUE);

		$this->load->model('modTransaction', "", TRUE);
		$this->load->model('modTransactionDetail', "", TRUE);

		$data["product"] = $this->modProduct->getAll(null)->result_array();
		$customer = $this->modCustomer->getAll(null)->result_array();

		$id = base64_decode($id);

		$customerarray = array();
		$nameopt = array();
		foreach($customer as $ind => $row){
			$customerarray[$row["id"]] = array();
			$customerarray[$row["id"]]["name"] = $row["name"];
			$customerarray[$row["id"]]["contact_number"] = $row["contact_number"];
			$customerarray[$row["id"]]["delivery_address"] = $row["delivery_address"];
			json_encode($nameopt[$row["id"]] = $row["name"]);
		}

		$paramtrans["id"] = $id;
		$paramtransdetail["transaction_id"] = $id;
		$data["transaction"] = $this->modTransaction->getAll($paramtrans)->row_array();
		$data["transactiondetail"] = $this->modTransactionDetail->getAll($paramtransdetail)->result_array();

		$data["customerdetail"] = json_encode($customerarray);
		$data["namelist"] = json_encode($nameopt);

		session_start();
		if(isset($_SESSION["username"])) {
			$this->load->view('main', $data);
		}else{
			$this->load->view('login');
		}
	}

	public function gettransactionslip(){
		$this->load->model('modTransaction', "", TRUE);
		$this->load->model('modTransactionDetail', "", TRUE);
		$param["complete"] = "0";
		$transaction = $this->modTransaction->getAll($param)->result_array();

		foreach($transaction as $ind => $row){
		    $transaction[$ind]["datetime"] = date("H:i:s", strtotime($transaction[$ind]["datetime"]));
			$detailparam["transaction_id"] = $row["id"];
			$transaction[$ind]["details"] = $this->modTransactionDetail->getAll($detailparam)->result_array();
		}

		echo json_encode($transaction);
	}

	public function voidorder(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modTransaction', "", TRUE);
		if($param["void_reason"] == "2"){
			$param["void_reason"] = $param["other"];
		}else{
			$reasons = array("Customer Cancel Order", "Wrong Input");
			$param["void_reason"] = $reasons[$param["void_reason"]];
		}
		$param["status"] = "3";
		$res = $this->modTransaction->update($param);

		echo json_encode($res);
	}

	public function updateorder(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modTransaction', "", TRUE);
		$res = $this->modTransaction->update($param);

		echo json_encode($res);
	}

	public function minusQty($id){
		$this->load->model('modTransactionDetail', "", TRUE);
		$this->modTransactionDetail->minusQty($id);
	}

	public function addQty($id){
		$this->load->model('modTransactionDetail', "", TRUE);
		$this->modTransactionDetail->addQty($id);
	}

	public function completetrans($id){
		$this->load->model('modTransaction', "", TRUE);
		$this->modTransaction->complete($id);
	}

	public function productlist(){
		$this->load->model('modProduct', "", TRUE);
		$product = $this->modProduct->getAll(null)->result_array();

		echo json_encode($product);
	}

	public function saveproductchanges(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modProduct', "", TRUE);

		$error = 0;
		foreach($param["data"] as $ind => $row){
			if($row["status"] == "new"){
				$res = $this->modProduct->insert($row);
				if(!$res)
					$error++;
			}
			if($row["status"] == "edit"){
				$res = $this->modProduct->update($row);
				if(!$res)
					$error++;
			}
			if($row["status"] == "delete"){
				$res = $this->modProduct->delete($row);
				if(!$res)
					$error++;
			}
		}

		if($error == 0)
			echo "success";
		else
			echo "failed";

		//$res = $this->modTransaction->update($param);

		//echo json_encode($res);

	}
}
