<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Order extends CI_Controller
{
	public function index() {

		 session_start();
		 if(isset($_SESSION['customer_id'])) {
			$this->load->view('order/welcome');
		 } else {
			redirect('/order/login');
		 }
			
		
	}

	public function track() {
		$this->load->view('order/track');
	}



	public function new($id = false) {

		session_start();
		if (isset($_SESSION["customer_id"])) {

	
	
			$store_id = 1;
			$user_id = $_SESSION["id"];

			$this->load->model('modProduct', "", TRUE);
			$this->load->model('modCustomer', "", TRUE);
			$this->load->model('modCategory', "", TRUE);
			$param = $this->input->post(NULL, "true");
			

			$product_param["phase_out"] = "0";
			$product_param["store_id"] = $store_id;

			$data["category"] = $this->modCategory->getAll($product_param)->result_array();
			$data["product"] = $this->modProduct->getAll($product_param)->result_array();

			$data["store_id"] = $store_id;

			$customer = $this->modCustomer->getAll(["user_id" => $user_id])->row_array();
	
			$data["customer"] = $customer;
			$data["username"] = $_SESSION['username'];

			if($id) {
				$this->load->model('modTransaction', "", TRUE);
				$this->load->model('modTransactionDetail', "", TRUE);

				$id = base64_decode($id);

				$data["transaction"] = $this->modTransaction->getAll(["id" => $id])->row_array();
				$data["transactiondetail"] = $this->modTransactionDetail->getAll(["transaction_id" => $id])->result_array();

				$data["update"] = true;
			}


			$this->load->view('order/new_order', $data);
		} else {
			redirect('/order/login');
		}

	}

	public function settle() {

		$param = $this->input->post(NULL, "true");
		session_start();
		if(count($_POST) == 0) {
			redirect('/order');
		}

		date_default_timezone_set("Asia/Manila");
	
		$this->load->model('modTransaction', "", TRUE);
		$this->load->model('modTransactionDetail', "", TRUE);
		$this->load->model('modCustomer', "", TRUE);
		$this->load->model('modProduct', "", TRUE);
		$this->load->model('modAuditTrail', "", TRUE);



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
		$param["trans"]["customer_id"] = $_SESSION["customer_id"];
		$param["trans"]["user_id"] = $_SESSION["id"];
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
				log_message('error', json_encode($row));
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
		
		
		$this->output->set_status_header(201);
		echo json_encode($result);
	}

	public function tracking() {
		$param = $this->input->get(NULL, "true");
		$this->load->model('modTransaction', "", TRUE);
		$result = [];

		$search = $this->modTransaction->getAll(array('name' => $param["customerName"], "order_number" => $param["transId"]))->result_array();
		
        if (count($search) > 0) {
            $trans = $search[0];
            
            $result = $this->getStatus($trans);
        } else {
			$result = [
				"success" => false,
				"message" => 'Order not found. Check your Transaction number and try again.',
				"status" => 404
			];
		}
		echo json_encode($result);
	}



	public function detail($id) {
		session_start();
		if(!isset($_SESSION['customer_id'])) {
			$this->load->view("order/login");
		} else {
            $this->load->model('modTransaction', "", true);
            $this->load->model('modTransactionDetail', "", true);

            $id = base64_decode($id);
            $paramtrans["id"] = $id;
            $data["transaction"] = $this->modTransaction->getAll($paramtrans)->row_array();
            $data["payment_method"] = $this->modTransaction->PAYMENT_METHOD;
            $data["username"] = $_SESSION["username"];

    
            if (is_null($data["transaction"])) {
                redirect('/order');
            } else {
                $data["tracking"] = $this->getStatus($data["transaction"], true);
                $data["detail"] = $this->modTransactionDetail->getAll(array("transaction_id" => $id))->result_array();

                $this->load->view("order/detail", $data);
            }
        }
	}

	public function login() {
		if(!isset($_SESSION['customer_id'])) {
			$this->load->view("order/login");
		} else {
			redirect('/order');
		}
	}

	public function auth(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modUser', "", TRUE);
		$this->load->model('modCustomer', '', TRUE);
		$param["password"] = md5($param["password"]);
		$param["access_level"] = 2;
		$res = $this->modUser->getAll($param)->row_array();
		
		if(!empty($res)) {
			$customer = $this->modCustomer->getAll(["user_id" => $res["id"]])->row_array();
			
			session_start();
			$_SESSION["username"] = $param["username"];
			$_SESSION["name"] = $res["name"];
			$_SESSION["id"] = $res["id"];
			$_SESSION["store_id"] = "1";
			$_SESSION["access_level"] = $res["access_level"];
			$_SESSION["customer_id"] = $customer["id"];
			unset($res["password"]);
		} else {
			$res["success"] = false;
			$this->output->set_status_header(401);

		}

		echo json_encode($res);
	}

	public function logout(){
		session_start();
		unset($_SESSION["username"]);
		unset($_SESSION["customer_id"]);
		session_destroy();
		$this->output->set_content_type('application/json')
					->set_output(json_encode(["loggedOut" => true]));
	}

	private function getStatus($trans, $withHistory = false) {
		
		$tracking_history = [];
		$has_result = false;

		$pending = [
			"success" => true,
			"message" => 'Order is still pending for verification',
			"status" => 'Pending',
			"date" => date('m/d/Y H:i', strtotime($trans["datetime"])),
			"color" => "#28a745"
		];

		$process = [
			"success" => true,
			"message" => 'Your order has been packed and is being handed over to our delivery team.',
			"status" => 'Process',
			"date" => is_null($trans["date_printed"]) ? null : date('m/d/Y H:i', strtotime($trans["date_printed"])),
			"color" => '#ff9800'
		];

		$delivered = [
			"success" => true,
			"message" => 'Your order has been succesfuly delivered.',
			"status" => 'Delivered',
			'date' => is_null($trans["date_delivered"]) ? null : date('m/d/Y H:i', strtotime($trans["date_delivered"])),
			"color" => "#2196f3"
		];

		$completed = [
			"success" => true,
			"message" => 'Your order is now completed. Thank you for purchasing at RAMX-X Meatshop!',
			"status" => 'Completed',
			"date" => null,
			"color" => "#3f51b5"
		];

		$to_receive =  [
			"success" => true,
			"message" => 'Your order is ready for delivery.',
			"status" => 'To Receive',
			"date" => null,
			"color" => "#00bcd4"
		];

		$cancelled =  [
			"success" => true,
			"message" => 'Your order has been voided',
			"status" => 'Cancelled',
			"date" => null,
			"color" => "#f44336"
		];


		if($trans["status"] == 0) {
			if($trans["printed"] == 1) {
				$tracking_history =  [
					"pending" => $pending,
					"process" => $process
				];
				$result = $process;					
			} else {
				$result = $pending;
				$tracking_history =  [
					"pending" => $pending
				];
			}
		} else if($trans["status"] == 1) {
			$tracking_history =  [
				"pending" => $pending,
				"process" => $process,
				"to_receive" => $to_receive
			];
				$result = $to_receive;
		} else if( $trans["status"] == 4) {
			$tracking_history =  [
				"pending" => $pending,
				"process" => $process,
				"to_receive" => $to_receive,
				"delivered" => $delivered
			];
			$result = $delivered;
		}  else if( $trans["status"] == 2) {
			$tracking_history =  [
				"pending" => $pending,
				"process" => $process,
				"to_receive" => $to_receive,
				"delivered" => $delivered,
				"completed" => $completed
			];
			$result = $completed;
		} else if( $trans["status"] == 3) {
			$tracking_history =  [
				"pending" => $pending,
				"cancelled" => $cancelled
			];
			$result = $cancelled;
		} 

		if(!$withHistory) {
			return $result;
		} else {
			return [
				"history" => $tracking_history,
				"status" => $result
			];
		}

	
	}

	public function purchases() {
		session_start();
		if(isset($_SESSION['customer_id'])) {
		
		   	$this->load->model('modTransaction', "", TRUE);
			$this->load->model('modTransactionDetail', "", TRUE);

		   	$param = [
				"customer_id" => $_SESSION["customer_id"],
				"no_image" => true,
				"columnname" => 'id',
				"columnsortorder" => 'DESC',
				"length" => 5,
				"start" => 0
		   ];
		   $transaction = $this->modTransaction->getAll($param)->result_array();
		   $purchases = [];
		   foreach($transaction as $row) {
				$row["tracking"] = $this->getStatus($row);
				$detail =  $this->modTransactionDetail->getAll(["transaction_id" => $row["id"]]);
				$row["detail"] = $detail->result_array();
				$row["detail_count"] = $detail->num_rows();
				$purchases[] = $row;
		   }
		   $payment_method = $this->modTransaction->PAYMENT_METHOD;

		   $this->load->view('order/purchases', ["purchases" => $purchases, "payment_method" => $payment_method, "username" => $_SESSION['username']]);
		} else {
		   redirect('/order/login');
		}

	}

	public function transaction() {
		session_start();
        if (isset($_SESSION['customer_id'])) {
			$param = $this->input->get(NULL, "true");
			$this->load->model('modTransaction', "", TRUE);
			$this->load->model('modTransactionDetail', "", TRUE);

			$getParam = [
				"customer_id" => $_SESSION["customer_id"],
				"no_image" => true,
				"columnname" => 'id',
				"columnsortorder" => 'DESC',
				"length" => $param["length"],
				"start" => $param["start"]
		   ];
			if($param["type"] == 'all') {
			   
			} else if($param["type"] == 'topay') {
				$getParam["paid"] = 0;
				$getParam["void_user"] = "";
			} else if($param["type"] == 'toreceive') {
				$getParam["status"] = 1;
			} else if($param["type"] == "completed") {
				$getParam["status"] = 2;
			}  else if($param["type"] == "cancelled") {
				$getParam["status"] = 3;
			} else if($param["type"] == "delivered") {
				$getParam["status"] = 4;
			}  else if($param["type"] == "packing") {
				$getParam["status"] = 0;
				$getParam["printed"] = 1;
			}


			$transaction = $this->modTransaction->getAll($getParam)->result_array();
			   $purchases = [];
			   foreach($transaction as $row) {
					$row["tracking"] = $this->getStatus($row);
					$detail =  $this->modTransactionDetail->getAll(["transaction_id" => $row["id"]]);
					$row["detail"] = $detail->result_array();
					$row["detail_count"] = $detail->num_rows();
					$purchases[] = $row;
			   }

			$this->output->set_content_type('application/json')
			->set_output(json_encode($purchases));


        } else {
			redirect('/order/login');
		}

	}

	public function profile(){
		session_start();
		if (isset($_SESSION['customer_id'])) {  
			$this->load->model('modCustomer', '', TRUE);
			$this->load->model('modUser', '', TRUE);
			$this->load->model('modCity', '', TRUE);

			$data['customer'] = $this->modCustomer->getAll(["id" => $_SESSION['customer_id']])->row_array();
			$data['user'] = $this->modUser->getAll(["id" => $data['customer']["user_id"]])->row_array();
			$data['username'] = $_SESSION['username'];
			$data['city'] = $this->modCity->getAll(null)->result_array();
			
			if($data["user"]) {
				unset($data["user"]["password"]);
			}

			$this->load->view('order/profile', $data);
		} else {
			redirect('/order/login');
		}

	}

	public function save_profile() {
		session_start();
		if (isset($_SESSION['customer_id'])) {  

			$post = $this->input->post(NULL, TRUE);

			$this->load->model('modCustomer', '', TRUE);
			$this->load->model('modUser', '', TRUE);
		
			$result = [];
			$name = '';
			if(isset($post["customer"])) {
				$name = $post['customer']['name'];
				$result['customer'] = 	$this->modCustomer->update($post['customer']);
			}

			if(isset($post["user"])) {

				if(array_key_exists('password', $post['user'])) {
					$post['user']['password'] = md5($post['user']['password'] );
				}

				if($name != '') {
					$post['user']['name'] = $name;
				}

				$result['user'] = $this->modUser->update($post['user']);
			}

			echo json_encode($result);
		} else {
			redirect('/order/login');
		}
	}

	
}
