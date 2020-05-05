<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//require '../sales.ramx.store/escpos-php/vendor/autoload.php';
//use Mike42\Escpos\Printer;
//use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class Main extends CI_Controller {

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

	public function pos(){
		$this->load->model('modProduct', "", TRUE);
		$this->load->model('modCustomer', "", TRUE);
		$this->load->model('modCategory', "", TRUE);

		$data["category"] = $this->modCategory->getAll(null)->result_array();
		$data["product"] = $this->modProduct->getAll(null)->result_array();
		$customer = $this->modCustomer->getAll(null)->result_array();
		$customerarray = array();
		$nameopt = array();
		foreach($customer as $ind => $row){
			$customerarray[$row["id"]] = array();
			$customerarray[$row["id"]]["name"] = $row["name"];
			$customerarray[$row["id"]]["fb_name"] = $row["facebook_name"];
			$customerarray[$row["id"]]["contact_number"] = $row["contact_number"];
			$customerarray[$row["id"]]["delivery_address"] = $row["delivery_address"];
			$customerarray[$row["id"]]["cust_location_image"] = $row["location_image"];
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
		$this->load->model('modPayment', "", TRUE);
		$this->load->model('modAuditTrail', "", TRUE);
		$param["id"] = $orderid;
		$detailparam["transaction_id"] = $orderid;
		$transaction = $this->modTransaction->getAll($param)->row_array();
		$transactiondetail = $this->modTransactionDetail->getAll($detailparam)->result_array();
		$paymentparam["transaction_id"] = $orderid;
		$payment = $this->modPayment->getAll($paymentparam)->result_array();
		$audittrail = $this->modAuditTrail->getAll($detailparam)->result_array();

		$data["transaction"] = $transaction;
		$data["transactiondetail"] = $transactiondetail;
		$data["paymenthistory"] = $payment;
		$data["orderhistory"] = $audittrail;
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
				foreach($data["transaction"]["orders"] as $ind => $row){
					$transdate = date("mdY", strtotime($row["datetime"]));
					$formatID = $transdate.'-'.sprintf("%04s", $row["id"]);
					$data["transaction"]["orders"][$ind]["formatid"] = $formatID;
					$data["transaction"]["orders"][$ind]["datetime"] = date("m/d/Y H:i:s", strtotime($row["datetime"]));
				}
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
		date_default_timezone_set("Asia/Manila");
		session_start();
		$this->load->model('modTransaction', "", TRUE);
		$this->load->model('modTransactionDetail', "", TRUE);
		$this->load->model('modCustomer', "", TRUE);
		$this->load->model('modProduct', "", TRUE);
		$this->load->model('modAuditTrail', "", TRUE);

		$param = $this->input->post(NULL, "true");

		/* UPDATE INVENTORY */
		$noavailqty = false;
		$productnoavailqty = array();
		if (isset($param["inventorydata"]))
			foreach ($param["inventorydata"] as $ind => $row) {
					$prodqty = $this->modProduct->availQty($row["id"])->row_array();
					if (($prodqty["qty"] + $row["qty"]) < 0) {
						$noavailqty = true;
						array_push($productnoavailqty, $prodqty["description"]);
					} else {
						$this->modProduct->updateInventory($row);
					}
			}

		/* UPDATE INVENTORY */
		if($noavailqty){
			$result["success"] = false;
			$result["error"] = "Some products have no available quantity.";
			$result["product"] = $productnoavailqty;
			echo json_encode($result);
			return;
		}

		$param["trans"]["datetime"] = date("Y-m-d H:i:s");

		$image = $param["locationimg"];
		$param["customerdetail"]["location_image"] = "";
		$imgname = strtolower(str_replace(" ", "", $param["trans"]["customer_name"]));

		if($image != "") {
			list($type, $image) = explode(';', $image);
			list(, $image) = explode(',', $image);
			$image = base64_decode($image);

			$filepath = "assets/location_image/".$imgname.".jpeg";
			$param["customerdetail"]["location_image"] = $imgname.".jpeg";

			file_put_contents($filepath, $image);
		}

		$param["trans"]["delivery_date"] = date("Y-m-d", strtotime($param["trans"]["delivery_date"]));
		$param["trans"]["location_image"] = str_replace("data:image/jpeg;base64,", "", $image);
		$moparray = ["Cash on Delivery", "Bank Transfer - BPI", "GCash", "Bank Transfer - Metrobank"];

		$updatenewvalue = array();
		$detailparam["transaction_id"] = $param["trans"]["transaction_id"];

		$contact_number = "";
		if(isset($param["newcustomer"])){
			if($image == "")
				$param["newcustomer"]["location_image"] = "";
			else{
				$param["newcustomer"]["location_image"] = $imgname.".jpeg";
			}
			$param["newcustomer"]["facebook_name"] = $param["trans"]["facebook_name"];
			$customerres = $this->modCustomer->insert($param["newcustomer"]);
			$param["trans"]["customer_id"] = $customerres["id"];
			$contact_number = $param["newcustomer"]["contact_number"];
		}else{
			$param["customerdetail"]["facebook_name"] = $param["trans"]["facebook_name"];
			$contact_number = $param["customerdetail"]["contact_number"];
			$customerres = $this->modCustomer->update($param["customerdetail"]);
		}

		if($param["trans"]["haschanges"] == 1){
			$param["trans"]["id"] = $param["trans"]["transaction_id"];
			$param["trans"]["date_revised"] = date("Y-m-d H:i:s");

			$audit_trails = array();
			$p["id"] = $param["trans"]["id"];
			$transactions = $this->modTransaction->getAll($p)->row_array();
			$param["trans"]["printed"] = 3;//updated
			if($transactions["printed"] == "1")
				$param["trans"]["printed"] = 2;//revised

			unset($transactions["location_image"]);

			$oldvalues = array();
			$oldvalues["total"] = $transactions["total"];
			$oldvalues["name"] = $transactions["name"];
			$oldvalues["delivery_address"] = $transactions["delivery_address"];
			$oldvalues["delivery_date"] = $transactions["delivery_date"];
			$oldvalues["payment_method"] = $moparray[$transactions["payment_method"]];
			$oldvalues["payment_confirmation_detail"] = $transactions["payment_confirmation_detail"];
			$oldvalues["remarks"] = $transactions["remarks"];
			$oldvalues["facebook_name"] = $transactions["facebook_name"];
			$oldvalues["contact_number"] = $transactions["contact_number"];
			$oldvalues["sales_agent"] = $transactions["sales_agent"];

			$transactionsdetails = $this->modTransactionDetail->getAll($detailparam)->result_array();

			$olddetails = array();
			foreach ($transactionsdetails as $ind => $row) {
				$name = $this->modProduct->getname($row["product_id"])->row_array();
				$olddetails[$ind]["name"] = $name["description"];
				$olddetails[$ind]["quantity"] = $row["quantity"];
				$olddetails[$ind]["total_price"] = $row["total_price"];
			}

			$newvalues = array();
			$newvalues["total"] = $param["trans"]["total"];
			$newvalues["name"] = $param["trans"]["customer_name"];
			$newvalues["delivery_address"] = $param["trans"]["delivery_address"];
			$newvalues["delivery_date"] = $param["trans"]["delivery_date"];
			$newvalues["payment_method"] = $moparray[$param["trans"]["payment_method"]];
			$newvalues["payment_confirmation_detail"] = $param["trans"]["payment_confirmation_detail"];
			$newvalues["remarks"] = $param["trans"]["remarks"];
			$newvalues["facebook_name"] = $param["trans"]["facebook_name"];
			$newvalues["contact_number"] = $contact_number;
			$newvalues["sales_agent"] = $_SESSION["name"];
			$updatenewvalue = $newvalues;

			$audit_trails["user_id"] = $_SESSION["id"];
			$audit_trails["event"] = "update";
			$audit_trails["transaction_id"] = $param["trans"]["id"];
			$audit_trails["table_name"] = json_encode(array("transaction","transaction_detail"));
			$audit_trails["old_values"] = json_encode(array("transaction"=>$oldvalues, "transaction_detail"=>$olddetails));
			$audit_trails["created_at"] = date("Y-m-d H:i:s");

			$result = $this->modTransaction->update($param["trans"]);
		}else{
			unset($param["trans"]["transaction_id"]);
			$param["trans"]["user_id"] = $_SESSION["id"];
			$result = $this->modTransaction->insert($param["trans"]);

			$newvalues = array();
			$newvalues["total"] = $param["trans"]["total"];
			$newvalues["name"] = $param["trans"]["customer_name"];
			$newvalues["delivery_address"] = $param["trans"]["delivery_address"];
			$newvalues["delivery_date"] = $param["trans"]["delivery_date"];
			$newvalues["payment_method"] = $moparray[$param["trans"]["payment_method"]];
			$newvalues["payment_confirmation_detail"] = $param["trans"]["payment_confirmation_detail"];
			$newvalues["remarks"] = $param["trans"]["remarks"];
			$newvalues["facebook_name"] = $param["trans"]["facebook_name"];
			$newvalues["contact_number"] = $contact_number;
			$newvalues["sales_agent"] = $_SESSION["name"];

			$newdetails = array();
			if(isset($param["detail"])) {
				foreach ($param["detail"] as $ind => $row) {
					$name = $this->modProduct->getname($row["product_id"])->row_array();
					$newdetails[$ind]["name"] = $name["description"];
					$newdetails[$ind]["quantity"] = $row["quantity"];
					$newdetails[$ind]["total_price"] = $row["price"];
				}
			}

			$audit_trails["user_id"] = $_SESSION["id"];
			$audit_trails["event"] = "insert";
			$audit_trails["transaction_id"] = $result["id"];
			$audit_trails["table_name"] = json_encode(array("transaction","transaction_detail"));
			$audit_trails["new_values"] = json_encode(array("transaction"=>$newvalues, "transaction_detail"=>$newdetails));
			$audit_trails["created_at"] = date("Y-m-d H:i:s");
		}

		if(isset($param["detail"])) {
			foreach ($param["detail"] as $ind => $row) {
				$row["transaction_id"] = $result["id"];
				$row["total_price"] = $row["quantity"] * $row["price"];
				if ($row["status"] == "new")
					$this->modTransactionDetail->insert($row);
				else if ($row["status"] == "edited")
					$this->modTransactionDetail->update($row);
				else if ($row["status"] == "deleted")
					$this->modTransactionDetail->delete($row);
			}
		}

		if($param["trans"]["haschanges"] == 1){
			$newtransactionsdetails = $this->modTransactionDetail->getAll($detailparam)->result_array();
			$newdetails = array();
			foreach ($newtransactionsdetails as $ind => $row) {
				$name = $this->modProduct->getname($row["product_id"])->row_array();
				$newdetails[$ind]["name"] = $name["description"];
				$newdetails[$ind]["quantity"] = $row["quantity"];
				$newdetails[$ind]["total_price"] = $row["total_price"];
			}

			$audit_trails["new_values"] = json_encode(array("transaction"=>$updatenewvalue, "transaction_detail"=>$newdetails));
		}

		$this->modAuditTrail->insert($audit_trails);

		$result["transaction_id"] = date("mdY")."-".sprintf("%04s", $result["id"]);
		echo json_encode($result);
	}

	public function ut($id){
		$this->load->model('modProduct', "", TRUE);
		$this->load->model('modCustomer', "", TRUE);

		$this->load->model('modTransaction', "", TRUE);
		$this->load->model('modTransactionDetail', "", TRUE);
		$this->load->model('modCategory', "", TRUE);

		$data["category"] = $this->modCategory->getAll(null)->result_array();
		$data["product"] = $this->modProduct->getAll(null)->result_array();
		$customer = $this->modCustomer->getAll(null)->result_array();

		$id = base64_decode($id);

		$customerarray = array();
		$nameopt = array();
		foreach($customer as $ind => $row){
			$customerarray[$row["id"]] = array();
			$customerarray[$row["id"]]["name"] = $row["name"];
			$customerarray[$row["id"]]["fb_name"] = $row["facebook_name"];
			$customerarray[$row["id"]]["contact_number"] = $row["contact_number"];
			$customerarray[$row["id"]]["delivery_address"] = $row["delivery_address"];
			$customerarray[$row["id"]]["cust_location_image"] = $row["location_image"];
			json_encode($nameopt[$row["id"]] = $row["name"]);
		}

		$paramtrans["id"] = $id;
		$paramtransdetail["transaction_id"] = $id;
		$data["transaction"] = $this->modTransaction->getAll($paramtrans)->row_array();
		$data["transactiondetail"] = $this->modTransactionDetail->getAll($paramtransdetail)->result_array();

		$data["customerdetail"] = json_encode($customerarray);
		$data["namelist"] = json_encode($nameopt);
		$data["update"] = true;

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
		session_start();
		$param = $this->input->post(NULL, "true");
		$this->load->model('modTransaction', "", TRUE);
		$this->load->model('modTransactionDetail', "", TRUE);
		$this->load->model('modProduct', "", TRUE);
		if($param["void_reason"] == "2"){
			$param["void_reason"] = $param["other"];
		}else{
			$reasons = array("Customer Cancel Order", "Wrong Input");
			$param["void_reason"] = $reasons[$param["void_reason"]];
		}
		$param["status"] = "3";
		$param["void_user"] = $_SESSION["name"];
		$res = $this->modTransaction->update($param);

		//update inventory
		$p["transaction_id"] = $param["id"];
		$transdetail = $this->modTransactionDetail->getAll($p)->result_array();
		foreach ($transdetail as $ind => $row) {
			$r["id"] = $row["product_id"];
			$r["qty"] = $row["quantity"];
			$this->modProduct->updateInventory($r);
		}


		echo json_encode($res);
	}

	public function updateorder(){
		date_default_timezone_set("Asia/Manila");
		$param = $this->input->post(NULL, "true");
		$this->load->model('modTransaction', "", TRUE);

		if(isset($param["print_date"]))
			$param["date_printed"] = date("Y-m-d H:i:s");

		$res = $this->modTransaction->update($param);

		echo json_encode($res);
	}

	public function insertpayment(){
		$param = $this->input->post(NULL, "true");

		$this->load->model('modPayment', "", TRUE);
		$this->load->model('modTransaction', "", TRUE);
		$param["payment_date"] = date("Y-m-d");
		$res = $this->modPayment->insert($param);
		$transparam["id"] = $param["transaction_id"];
		$transparam["payment_method"] = $param["payment_method"];
		$transparam["payment_confirmation_detail"] = $param["payment_confirmation_detail"];
		if($param["amount"] >= $param["balance"]){
			$transparam["paid"] = "1";
		}
		$this->modTransaction->update($transparam);


		echo json_encode($res);
	}

	public function deletepayment(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modPayment', "", TRUE);
		$this->load->model('modTransaction', "", TRUE);
		$res = $this->modPayment->delete($param);
		$paidparam["id"] = $param["transaction_id"];
		$paidparam["paid"] = "0";
		$this->modTransaction->update($paidparam);

		echo json_encode($res);
	}

	public function updatepayment(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modPayment', "", TRUE);
		$this->load->model('modTransaction', "", TRUE);
		$res = $this->modPayment->update($param);
		$paidparam["id"] = $param["transaction_id"];
		$transparam["transaction_id"]  = $param["transaction_id"];
		if($param["newbalance"] == 0)
			$paidparam["paid"] = "1";
		else
			$paidparam["paid"] = "0";

		$recentpayment = $this->modPayment->getAll($transparam)->row_array();
		$paidparam["payment_method"] = $recentpayment["payment_method"];
		$this->modTransaction->update($paidparam);
		$res["payment_method"] = $recentpayment["payment_method"];

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

	/*public function printtest(){
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
		// Initialize
		$printer -> initialize();

		$printer->setFont(Printer::FONT_A);
		$printer -> setTextSize(2, 1);
		//Text
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
	}*/

}
