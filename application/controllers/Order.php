<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Order extends CI_Controller
{
	public function index() {
		$this->load->view('order/welcome');
	}

	public function track() {
		$this->load->view('order/track');
	}



	public function new() {
		$store_id = 1;

		$this->load->model('modProduct', "", TRUE);
		$this->load->model('modCustomer', "", TRUE);
		$this->load->model('modCategory', "", TRUE);
		$param = $this->input->post(NULL, "true");
		

		$product_param["phase_out"] = "0";
		$product_param["store_id"] = $store_id;

		$data["category"] = $this->modCategory->getAll($product_param)->result_array();
		$data["product"] = $this->modProduct->getAll($product_param)->result_array();

		$data["store_id"] = $store_id;

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
		

		$this->load->view('order/new_order', $data);
	}

	public function settle() {
		date_default_timezone_set("Asia/Manila");
	
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
		$moparray = ["Cash on Delivery", "Bank Transfer - BPI", "GCash", "Bank Transfer - Metrobank", "Check"];

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
		} else {
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
			$newvalues["sales_agent"] = 'CUSTOMER_ENCODED';
			$updatenewvalue = $newvalues;

			$audit_trails["user_id"] = $transactions["customer_id"];
			$audit_trails["user_type"] = 'Customer';
			$audit_trails["event"] = "update";
			$audit_trails["transaction_id"] = $param["trans"]["id"];
			$audit_trails["table_name"] = json_encode(array("transaction","transaction_detail"));
			$audit_trails["old_values"] = json_encode(array("transaction"=>$oldvalues, "transaction_detail"=>$olddetails));
			$audit_trails["created_at"] = date("Y-m-d H:i:s");

			$result = $this->modTransaction->update($param["trans"]);
		}else{
			unset($param["trans"]["transaction_id"]);
			$param["trans"]["user_id"] = null;
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
			$newvalues["sales_agent"] = 'CUSTOMER ENCODED';

			$newdetails = array();
			if(isset($param["detail"])) {
				foreach ($param["detail"] as $ind => $row) {
					$name = $this->modProduct->getname($row["product_id"])->row_array();
					$newdetails[$ind]["name"] = $name["description"];
					$newdetails[$ind]["quantity"] = $row["quantity"];
					$newdetails[$ind]["total_price"] = $row["price"];
				}
			}

			$audit_trails["user_id"] = $param["trans"]["customer_id"];
			$audit_trails["user_type"] = 'Customer';
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

	public function tracking() {
		$param = $this->input->get(NULL, "true");
		$this->load->model('modTransaction', "", TRUE);
		$result = [];

		$search = $this->modTransaction->getAll(array('name' => $param["customerName"], "order_number" => $param["transId"]))->result_array();
		
		if(count($search) > 0) {
			
			$trans = $search[0];
			
			if($trans["status"] == 0) {
				if($trans["printed"] == 1) {
					$result = [
						"success" => true,
						"message" => 'Your order has been packed and is being handed over to our delivery team.',
						"status" => 200
					];
		
					
				} else {
					$result = [
						"success" => true,
						"message" => 'Order is still pending for verification',
						"status" => 200
					];
		
				}
			} else if($trans["status"] == 1) {
				$result = [
					"success" => true,
					"message" => 'Your order is ready for delivery.',
					"status" => 200
				];
			} else if( $trans["status"] == 4) {
				$result = [
					"success" => true,
					"message" => 'Your order has been succesfuly delivered.',
					"status" => 200
				];
			}  else if( $trans["status"] == 2) {
				$result = [
					"success" => true,
					"message" => 'Your order is now completed. Thank you for purchasing at RAMX-X Meatshop and see you on your next purchase!',
					"status" => 200
				];
			} else if( $trans["status"] == 3) {
				$result = [
					"success" => true,
					"message" => 'Your order has been voided',
					"status" => 200
				];
			} 

		} else {
			$result = [
				"success" => false,
				"message" => 'Order not found. Check your Transaction number and try again.',
				"status" => 404
			];

		
		}


		echo json_encode($result);
	}

	public function success($id) {
		$this->load->model('modTransaction', "", TRUE);
		$this->load->model('modTransactionDetail', "", TRUE);

		$id = base64_decode($id);
		$paramtrans["id"] = $id;
		$data["transaction"] = $this->modTransaction->getAll($paramtrans)->row_array();
		$data["payment_method"] = $this->modTransaction->PAYMENT_METHOD;
		if(empty($data["transaction"])) {
			redirect('/order');
		} else {

			$data["detail"] = $this->modTransactionDetail->getAll(array("transaction_id" => $id))->result_array();

			$this->load->view("order/success", $data);
		}


		
	}
}
