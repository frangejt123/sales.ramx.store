<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once "../sales.ramx.store/vendor/autoload.php";
use Jaspersoft\Client\Client;

class Report extends CI_Controller {

	public function index(){
		session_start();
		if(isset($_SESSION["username"])) {
			$param = $this->input->post(NULL, "true");
			$condition = [
				"CONDITION" => "id=".$param["id"]
			];
			
			$report_client = new Client("https://jasper.ribshack.info", "jasperadmin", "dsscRGC2019", "");
			$report = $report_client->reportService()->runReport("/Reports/RAMX/SalesOrder", "pdf", null, null, $condition);
			
			$this->output
				->set_content_type('application/pdf')
				->set_output($report);
		}
	}

	public function item_summary(){
		session_start();
		if(isset($_SESSION["username"])) {
			$param = $this->input->post(NULL, "true");
			$dd_param = explode(" - ", $param["param"]);
			$from = date("Y-m-d", strtotime($dd_param[0]));
			$to = date("Y-m-d", strtotime($dd_param[1]));
			$condition = [
				"DELIVERY_DATE_FROM" => $from,		  
				"DELIVERY_DATE_TO" => $to
			];
			
			$report_client = new Client("https://jasper.ribshack.info", "jasperadmin", "dsscRGC2019", "");
			$report = $report_client->reportService()->runReport("/Reports/RAMX/ItemSummary", "pdf", null, null, $condition);

			$this->output
				->set_content_type('application/pdf')
				->set_output($report);
		}
	}
	
	public function item_summary_detail(){
		session_start();
		if(isset($_SESSION["username"])) {
			$param = $this->input->post(NULL, "true");
			
			$from = "";
			$to = "";
			if(isset($param["param"]) && $param["param"] != ""){
				$dd_param = explode(" - ", $param["param"]);
				$from = date("Y-m-d", strtotime($dd_param[0]));
				$to = date("Y-m-d", strtotime($dd_param[1]));
			}
			
			$condition = [
				"CONDITION" => "transaction.delivery_date>='" . $from . "' AND transaction.delivery_date<='" . $to . "'"
			];
			
			$report_client = new Client("https://jasper.ribshack.info", "jasperadmin", "dsscRGC2019", "");
			$report = $report_client->reportService()->runReport("/Reports/RAMX/ItemSummaryDetail", "pdf", null, null, $condition);

			$this->output
				->set_content_type('application/pdf')
				->set_output($report);
			
		}
	}
	
	public function payment_record(){
		session_start();
		if(isset($_SESSION["username"])) {
			$param = $this->input->post(NULL, "true");
			$condition = [
				"TRANSACTION_ID" => $param["param"]
			];
			
			$report_client = new Client("https://jasper.ribshack.info", "jasperadmin", "dsscRGC2019", "");
			$report = $report_client->reportService()->runReport("/Reports/RAMX/PaymentRecord", "pdf", null, null, $condition);

			$this->output
				->set_content_type('application/pdf')
				->set_output($report);
			
		}
	}
	
	public function sales_by_delivery(){
		session_start();

		if (!isset($_SESSION["username"])) $this->output->set_status_header(401)->set_output("Unauthorize Access!");
		
		$param = $this->input->post(NULL, "true");
		
		$from = "";
		$to = "";
		if(isset($param["param"]) && $param["param"] != ""){
			$dd_param = explode(" - ", $param["param"]);
			$from = date("Y-m-d", strtotime($dd_param[0]));
			$to = date("Y-m-d", strtotime($dd_param[1]));
		}
		
		$status = isset($param["param_status"]) ? $param["param_status"] : "";
		$mop = isset($param["param_mop"]) ? $param["param_mop"] : "";
		$paid = isset($param["param_paid"]) ? $param["param_paid"] : "";

		$input_param = [
			"delivery_date_from" 	=> $from, // Required Parameter
			"delivery_date_to" 		=> $to, // Required Parameter
			"txn_status" 			=> $status,
			"payment_method" 		=> $mop,
			"txn_paid" 				=> $paid
		];
		
		// Check required parameters
		if (is_null($input_param["delivery_date_from"]) || is_null($input_param["delivery_date_from"])) $this->output->set_status_header(400)->set_output("Bad Request");

		$report_param = [
			"DELIVERY_DATE_FROM" => $input_param["delivery_date_from"],
			"DELIVERY_DATE_TO" => $input_param["delivery_date_to"],
			"CONDITION" => "transaction.status!=3 AND transaction.delivery_date>='" . $input_param["delivery_date_from"] . "' AND transaction.delivery_date<='" . $input_param["delivery_date_to"] . "'",
		];
		
		if ($input_param["txn_status"] != "") {
			$report_param["CONDITION"] .= " AND transaction.status=" . $input_param["txn_status"];
		}
		
		if ($input_param["txn_paid"] != "") {
			$report_param["CONDITION"] .= " AND transaction.paid=" . $input_param["txn_paid"];
		}
		
		if ($input_param["payment_method"] != "") {
			$report_param["CONDITION"] .= " AND transaction.payment_method IN (".$input_param["payment_method"].")";
		}
		
		
		$report_client = new Client("https://jasper.ribshack.info", "jasperadmin", "dsscRGC2019", "");
		$report = $report_client->reportService()->runReport("/Reports/RAMX/SalesByDeliveryDate", "pdf", null, null, $report_param);

		$this->output
			->set_content_type('application/pdf')
			->set_output($report);
			
	}

	public function so_list_by_delivery_date() {
		session_start();

		if (!isset($_SESSION["username"])) $this->output->set_status_header(401)->set_output("Unauthorize Access!");
		
		$param = $this->input->post(NULL, "true");
		
		$from = "";
		$to = "";
		if(isset($param["param"]) && $param["param"] != ""){
			$dd_param = explode(" - ", $param["param"]);
			$from = date("Y-m-d", strtotime($dd_param[0]));
			$to = date("Y-m-d", strtotime($dd_param[1]));
		}
		
		$status = isset($param["param_status"]) ? $param["param_status"] : "";
		$mop = isset($param["param_mop"]) ? $param["param_mop"] : "";
		$paid = isset($param["param_paid"]) ? $param["param_paid"] : "";

		$input_param = [
			"delivery_date_from" 	=> $from, // Required Parameter
			"delivery_date_to" 		=> $to, // Required Parameter
			"txn_status" 			=> $status,
			"payment_method" 		=> $mop,
			"txn_paid" 				=> $paid
		];

		// Check required parameters
		if (is_null($input_param["delivery_date_from"]) || is_null($input_param["delivery_date_from"])) $this->output->set_status_header(400)->set_output("Bad Request");

		$report_param = [
			"DELIVERY_DATE_FROM" => $input_param["delivery_date_from"],
			"DELIVERY_DATE_TO" => $input_param["delivery_date_to"],
			"CONDITION" => "transaction.delivery_date>='" . $input_param["delivery_date_from"] . "' AND transaction.delivery_date<='" . $input_param["delivery_date_to"] . "'",
		];

		if ($input_param["txn_status"] != "") {
			$report_param["CONDITION"] .= " AND transaction.status=" . $input_param["txn_status"];
		}
		
		if ($input_param["txn_paid"] != "") {
			$report_param["CONDITION"] .= " AND transaction.paid=" . $input_param["txn_paid"];
		}
		
		if ($input_param["payment_method"] != "") {
			$report_param["CONDITION"] .= " AND transaction.payment_method IN (".$input_param["payment_method"].")";
		}

		$report_client = new Client("https://jasper.ribshack.info", "jasperadmin", "dsscRGC2019", "");
		$report = $report_client->reportService()->runReport("/Reports/RAMX/SalesOrderListByDeliveryDate", "pdf", null, null, $report_param);

		$this->output
			->set_content_type('application/pdf')
			->set_output($report);
	}

	public function sales_by_payment_method () {
		session_start();

		if (!isset($_SESSION["username"])) $this->output->set_status_header(401)->set_output("Unauthorize Access!");
		$param = $this->input->post(NULL, "true");
		
		$from = "";
		$to = "";
		if(isset($param["param"]) && $param["param"] != ""){
			$dd_param = explode(" - ", $param["param"]);
			$from = date("Y-m-d", strtotime($dd_param[0]));
			$to = date("Y-m-d", strtotime($dd_param[1]));
		}
		
		$trx_from = "";
		$trx_to = "";
		if(isset($param["param_trxdate"]) && $param["param_trxdate"] != ""){
			$trx_param = explode(" - ", $param["param_trxdate"]);
			$trx_from = date("Y-m-d", strtotime($trx_param[0]));
			$trx_to = date("Y-m-d", strtotime($trx_param[1]));
		}
		
		$status = isset($param["param_status"]) ? $param["param_status"] : "";
		$mop = isset($param["param_mop"]) ? $param["param_mop"] : "";
		$driver = isset($param["param_driver"]) ? $param["param_driver"] : "";

		$input_param = [
			"delivery_date_from" 	=> $from,
			"delivery_date_to" 		=> $to,
			"txn_date_from" 		=> $trx_from,
			"txn_date_to" 			=> $trx_to,
			"txn_status" 			=> $status,
			"payment_method" 		=> $mop,
			"driver"				=> $driver
		];

		$report_param = [
			"CONDITION" => "transaction.status!=3 AND transaction.paid=1"
		];
		
		if (($input_param["delivery_date_from"] != "") && ($input_param["delivery_date_to"] != "")) {
			$report_param["CONDITION"] .= " AND transaction.delivery_date>='" . $input_param["delivery_date_from"] . "' AND transaction.delivery_date<='" . $input_param["delivery_date_to"] . "'";
		}

		if (($input_param["txn_date_from"] != "") && ($input_param["txn_date_to"] != "")) {
			$report_param["CONDITION"] .= " AND transaction.datetime>='" . $input_param["txn_date_from"] . "' AND transaction.datetime<='" . $input_param["txn_date_to"] . "'";
		}

		if ($input_param["txn_status"] != "") {
			$report_param["CONDITION"] .= " AND transaction.status=" . $input_param["txn_status"];
		}
		
		if ($input_param["payment_method"] != "") {
			$report_param["CONDITION"] .= " AND transaction.payment_method IN (".$input_param["payment_method"].")";
		}

		if (!is_null($input_param["driver"]) && $input_param["driver"] != "") {
			$report_param["CONDITION"] .= " AND driver.id=\"" . $input_param["driver"] . "\"";
		}
		
		//print_r($report_param);
	
		$report_client = new Client("https://jasper.ribshack.info", "jasperadmin", "dsscRGC2019", "");
		$report = $report_client->reportService()->runReport("/Reports/RAMX/SalesByPaymentMethod", "pdf", null, null, $report_param);

		$this->output
			->set_content_type('application/pdf')
			->set_output($report);
	}

	public function payment_summary_by_method () {
		session_start();

		if (!isset($_SESSION["username"])) $this->output->set_status_header(401)->set_output("Unauthorize Access!");

		$input_param = [
			"delivery_date_from" 	=> null,
			"delivery_date_to" 		=> null,
			"txn_date_from" 		=> null,
			"txn_date_to" 			=> null,
			"payment_date_from" 	=> null,
			"payment_date_from" 	=> null,
			"payment_method"		=> null
		];

		$param = $this->input->post(NULL, "true");
		
		if(isset($param["param"]) && $param["param"] != "") {
			$daterange = explode(" - ", $param["param"]);

			$input_param["delivery_date_from"] = DateTime::createFromFormat("m/d/Y", $daterange[0]);
			$input_param["delivery_date_to"] = DateTime::createFromFormat("m/d/Y", $daterange[1]);
		}
		

		if(isset($param["param_trxdate"]) && $param["param_trxdate"] != "") {
			$daterange = explode(" - ", $param["param_trxdate"]);

			$input_param["txn_date_from"] = DateTime::createFromFormat("m/d/Y", $daterange[0]);
			$input_param["txn_date_to"] = DateTime::createFromFormat("m/d/Y", $daterange[1]);
		}

		if(isset($param["param_paymentdate"]) && $param["param_paymentdate"] != "") {
			$daterange = explode(" - ", $param["param_paymentdate"]);

			$input_param["payment_date_from"] = DateTime::createFromFormat("m/d/Y", $daterange[0]);
			$input_param["payment_date_to"] = DateTime::createFromFormat("m/d/Y", $daterange[1]);
		}
		
		$input_param["payment_method"] = isset($param["param_mop"]) && $param["param_mop"] != "" ? $param["param_mop"] : null;
		
		
		$report_param = [
			"CONDITION" => "transaction.status!=3 AND transaction.paid=1",
			"REPORT_PAYLOAD" => ""
		];
		
		
		if (!is_null($input_param["delivery_date_from"]) && !is_null($input_param["delivery_date_to"])) {
			$date_from = DateTime::createFromFormat("Y-m-d", $input_param["delivery_date_from"]);
			$date_to = DateTime::createFromFormat("Y-m-d", $input_param["delivery_date_to"]);

			$report_param["CONDITION"] .= " AND transaction.delivery_date>='" . $date_from->format("Y-m-d") . "' AND transaction.delivery_date<='" . $date_to->format("Y-m-d") . "'";

			$report_param["REPORT_PAYLOAD"] .= "DELIVERY DATE[" . $date_from->format("m/d/Y") . " - " . $date_to->format("m/d/Y") . "]; ";
		}

		if (!is_null($input_param["txn_date_from"]) && !is_null($input_param["txn_date_to"])) {
			$date_from = DateTime::createFromFormat("Y-m-d", $input_param["txn_date_from"]);
			$date_to = DateTime::createFromFormat("Y-m-d", $input_param["txn_date_to"]);

			$report_param["CONDITION"] .= " AND transaction.datetime>='" . $date_from->format("Y-m-d") . "' AND transaction.datetime<='" . $date_to->format("Y-m-d") . "'";

			$report_param["REPORT_PAYLOAD"] .= "TXN DATE[" . $date_from->format("m/d/Y") . " - " . $date_to->format("m/d/Y") . "]; ";
		}

		if (!is_null($input_param["payment_date_from"]) && !is_null($input_param["payment_date_to"])) {
			$date_from = DateTime::createFromFormat("Y-m-d", $input_param["payment_date_from"]);
			$date_to = DateTime::createFromFormat("Y-m-d", $input_param["payment_date_to"]);

			$report_param["CONDITION"] .= " AND payment.payment_date>='" . $date_from->format("Y-m-d") . "' AND payment.payment_date<='" . $date_to->format("Y-m-d") . "'";

			$report_param["REPORT_PAYLOAD"] .= "PAYMENT DATE[" . $date_from->format("m/d/Y") . " - " . $date_to->format("m/d/Y") . "]; ";
		}
		
		if (!is_null($input_param["payment_method"])) {
			$report_param["CONDITION"] .= " AND payment.payment_method=" . $input_param["payment_method"];

			$report_param["REPORT_PAYLOAD"] .= "PAYMENT METHOD=" . $input_param["payment_method"];
		}
		
		print_r($report_param);
	
		/*$report_client = new Client("https://jasper.ribshack.info", "jasperadmin", "dsscRGC2019", "");
		$report = $report_client->reportService()->runReport("/Reports/RAMX/PaymentSummaryByMethod", "pdf", null, null, $report_param);

		$this->output
			->set_content_type('application/pdf')
			->set_output($report);*/
	}
}
