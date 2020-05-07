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
}
