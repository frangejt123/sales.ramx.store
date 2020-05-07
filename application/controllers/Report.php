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
			/*$dd_param = explode(" - ", $param["param"]);
			$from = date("Y-m-d", strtotime($dd_param[0]));
			$to = date("Y-m-d", strtotime($dd_param[1]));
			$condition = [
				"DELIVERY_DATE_FROM" => $from,		  
				"DELIVERY_DATE_TO" => $to
			];*/
			$condition = [
				"CONDITION" => "transaction.delivery_date='".$param["param"]."'"
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
		if(isset($_SESSION["username"])) {
			$param = $this->input->post(NULL, "true");
			$dd_param = explode(" - ", $param["param"]);
			$from = date("Y-m-d", strtotime($dd_param[0]));
			$to = date("Y-m-d", strtotime($dd_param[1]));
			
			$condition = [
				"DELIVERY_DATE_FROM" => $from,
				"DELIVERY_DATE_TO" => $to
			];
			
			$condition = [
				"DELIVERY_DATE_FROM" => $from,		  
				"DELIVERY_DATE_TO" => $to,
				"CONDITION" => "transaction.status!=3 AND transaction.delivery_date>='" . $from . "' AND transaction.delivery_date<='" .$to. "'"
			];
			
			$report_client = new Client("https://jasper.ribshack.info", "jasperadmin", "dsscRGC2019", "");
			$report = $report_client->reportService()->runReport("/Reports/RAMX/SalesByDeliveryDate", "pdf", null, null, $condition);

			$this->output
				->set_content_type('application/pdf')
				->set_output($report);
			
		}
	}

	public function so_list_by_delivery_date() {
		$input_param = [
			"delivery_date_from" 	=> $this->input->post("delivery_date_from", TRUE),
			"delivery_date_to" 		=> $this->input->post("delivery_date_to", TRUE),
			"txn_status" 			=> $this->input->post("txn_status", TRUE),
			"payment_method" 		=> $this->input->post("payment_method", TRUE),
			"txn_paid" 				=> $this->input->post("txn_paid", TRUE),
		];

		// Check required parameters
		if (is_null($input_param["delivery_date_from"]) || is_null($input_param["delivery_date_from"])) $this->output->set_status_header(400)->set_output("Bad Request");

		$report_param = [
			"DELIVERY_DATE_FROM" => $input_param["delivery_date_from"],
			"DELIVERY_DATE_TO" => $input_param["delivery_date_to"],
			"CONDITION" => "transaction.delivery_date>='" . $input_param["delivery_date_from"] . "' AND transaction.delivery_date<='" . $input_param["delivery_date_to"] . "'",
		];

		if (!is_null($input_param["txn_status"])) {
			$report_param["CONDITION"] .= " AND transaction.status=" . $input_param["txn_status"];
		}
		
		if (!is_null($input_param["txn_paid"])) {
			$report_param["CONDITION"] .= " AND transaction.paid=" . $input_param["txn_paid"];
		}

		if (!is_null($input_param["payment_method"])) {
			$report_param["CONDITION"] .= " AND transaction.payment_method=" . $input_param["payment_method"];
		}

		$report_client = new Client("https://jasper.ribshack.info", "jasperadmin", "dsscRGC2019", "");
		$report = $report_client->reportService()->runReport("/Reports/RAMX/SalesOrderListByDeliveryDate", "pdf", null, null, $report_param);

		$this->output
			->set_content_type('application/pdf')
			->set_output($report);
	}
}
