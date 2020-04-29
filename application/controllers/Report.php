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
			$condition = [
				"CONDITION" => "transaction.id=".$param["id"]
			];

			$report_client = new Client("https://jasper.ribshack.info", "jasperadmin", "dsscRGC2019", "");
			$report = $report_client->reportService()->runReport("/Reports/RAMX/ItemSummary", "pdf", null, null, $condition);

			$this->output
				->set_content_type('application/pdf')
				->set_output($report);
		}
	}
}
