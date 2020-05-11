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
			$dd_param = explode(" - ", $param["param"]);
			$from = date("Y-m-d", strtotime($dd_param[0]));
			$to = date("Y-m-d", strtotime($dd_param[1]));
			
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
		$dd_param = explode(" - ", $param["param"]);
		$from = date("Y-m-d", strtotime($dd_param[0]));
		$to = date("Y-m-d", strtotime($dd_param[1]));
		
		$status = $param["param_status"];
		$mop = $param["param_mop"];
		$paid = $param["param_paid"];

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
		$dd_param = explode(" - ", $param["param"]);
		$from = date("Y-m-d", strtotime($dd_param[0]));
		$to = date("Y-m-d", strtotime($dd_param[1]));
		
		$status = $param["param_status"];
		$mop = $param["param_mop"];
		$paid = $param["param_paid"];

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
		
		$dd_param = explode(" - ", $param["param"]);
		$from = date("Y-m-d", strtotime($dd_param[0]));
		$to = date("Y-m-d", strtotime($dd_param[1]));
		
		$trx_from = "";
		$trx_to = "";
		if($param["param_trxdate"] != ""){
			$trx_param = explode(" - ", $param["param_trxdate"]);
			$trx_from = date("Y-m-d", strtotime($trx_param[0]));
			$trx_to = date("Y-m-d", strtotime($trx_param[1]));
		}
		
		$status = $param["param_status"];
		$mop = $param["param_mop"];

		$input_param = [
			"delivery_date_from" 	=> $from,
			"delivery_date_to" 		=> $to,
			"txn_date_from" 		=> $trx_from,
			"txn_date_to" 			=> $trx_to,
			"txn_status" 			=> $status,
			"payment_method" 		=> $mop
		];

		$report_param = [
			"CONDITION" => "transaction.status!=3 AND transaction.paid=1"
		];

		if (($input_param["txn_date_from"] != "") && ($input_param["txn_date_to"])) {
			$report_param["CONDITION"] .= " AND transaction.datetime>='" . $input_param["txn_date_from"] . "' AND transaction.datetime<='" . $input_param["txn_date_to"] . "'";
		}

		if ($input_param["txn_status"] != "") {
			$report_param["CONDITION"] .= " AND transaction.status=" . $input_param["txn_status"];
		}
		
		if ($input_param["payment_method"] != "") {
			$report_param["CONDITION"] .= " AND transaction.payment_method IN (".$input_param["payment_method"].")";
		}

		$report_client = new Client("https://jasper.ribshack.info", "jasperadmin", "dsscRGC2019", "");
		$report = $report_client->reportService()->runReport("/Reports/RAMX/SalesByPaymentMethod", "pdf", null, null, $report_param);

		$this->output
			->set_content_type('application/pdf')
			->set_output($report);
		
	}
}
