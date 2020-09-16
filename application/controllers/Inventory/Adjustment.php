<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adjustment extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	function __construct() {
        parent::__construct();
		session_start();
		if (isset($_SESSION["username"])) {
			if ($_SESSION['access_level'] == 2) {
				redirect('/order');
			}
		}
    }



	public function index()
	{
		$this->load->model('modInventoryAdjustment', "", TRUE);

		$data = [
			"adjustments" => []
		];


		if(isset($_SESSION["username"])) {
			$this->view('inventory_adjustment/index', $data);
		}else{
			$this->load->view('login');
		}

	}

	public function list() {
		
		$this->load->model('modInventoryAdjustment', "", TRUE);

		
        $column = ["id", "date", "type", "status"];

		$param = $this->input->post(NULL, "true");

		$draw = $param['draw'];
		
		$totalRecords = $this->modInventoryAdjustment->getAll(null)->num_rows();

        if(isset($param["order"])) {
			$param["order_by"] = $param["order"][0]["column"];
			$param["sort_order"] = $param["order"][0]["dir"];
		}


		$data  = $this->modInventoryAdjustment->getAll($param)->result_array();
		
		unset($param["start"]);
		$totalDisplayRecords =  $this->modInventoryAdjustment->getAll($param)->num_rows();
	
        

		$response = array(
			"draw" => intval($draw),
			"recordsTotal" => $totalRecords,
			"recordsFiltered" => $totalDisplayRecords,
			"data" => $data
		);

		//print_r($data);
	 	echo json_encode($response);

	}

	public function new() {
		$this->load->model('modProduct', "", TRUE);

		if(isset($_SESSION["username"])) {
			$data = array(
				"product" => $this->modProduct->getAll(null)->result_array()
			);
			$this->view('inventory_adjustment/detail', $data);
		}
		else{
			$this->load->view('login');
		}
	}

	private function view($page, $data) {
			$this->load->view("layouts/header");
			$this->load->view($page, $data);
			$this->load->view("layouts/js");
			$this->load->view("inventory_adjustment/js");
			$this->load->view("layouts/footer");
	}

	public function save() {
		$param = $this->input->post(NULL, "true");
		$this->load->model('modInventoryAdjustment', "", TRUE);
		$this->load->model('modInventoryAdjustmentDetail', "", TRUE);

		// $this->db->transStart();

		$param["adjustment"]["prepared_by"] = $_SESSION["id"];
		$state = $param["adjustment"]["_state"];
		if($state == "new") {
			$adjustment =  $this->modInventoryAdjustment->insert($param["adjustment"]);
		} else if ($state == "edited") {
			$adjustment =  $this->modInventoryAdjustment->update($param["adjustment"]);
		}
		$result = [
			"adjustment" => $adjustment
		];
		
		$details = [];
		if(array_key_exists("details", $param)) {

			if($adjustment["success"]) {
				//insert details;
			
				foreach($param["details"] as $i => $detail) {
					$detail["inventory_adjustment_id"] = $adjustment["id"];
					if($detail["_state"] == "new") {
						$details[$i] = $this->modInventoryAdjustmentDetail->insert($detail);
						$details[$i]["tmp_id"] = $detail["tmp_id"]; 
					} else if($detail["_state"] == "edited") {
						$details[$i] = $this->modInventoryAdjustmentDetail->update($detail);
						$details[$i]["tmp_id"] = $detail["tmp_id"]; 
					} else if($detail["_state"] == "deleted") {
						$details[$i] = $this->modInventoryAdjustmentDetail->delete($detail);
						$details[$i]["tmp_id"] = $detail["tmp_id"]; 
					}
				}
				$result["details"] = $details;			
			}
		}
		// $this->db->transComplete();
		
		

		echo json_encode($result);

	}

	public function detail($id) {
		$this->load->model('modProduct', "", TRUE);
		$this->load->model('modInventoryAdjustment', "", TRUE);
		$this->load->model('modInventoryAdjustmentDetail', "", TRUE);
		$this->load->model('modUser', "", TRUE);
		$data = [
			"product" => $this->modProduct->getAll(null)->result_array(),
			"adjustment" => $this->modInventoryAdjustment->getAll(["id" => $id])->row_array(),
			"details" => $this->modInventoryAdjustmentDetail->getAll(["inventory_adjustment_id" => $id])->result_array()
		];

		if($data["adjustment"]["prepared_by"]) {
			$prep_by  = $this->modUser->getAll(["id" => $data["adjustment"]["prepared_by"]])->row_array();
			$data["prep_by"] = $prep_by;
		}

		if($data["adjustment"]["approved_by"]) {
			$app_by  = $this->modUser->getAll(["id" => $data["adjustment"]["approved_by"]])->row_array();
			$data["app_by"] = $app_by;
		}

		if(isset($_SESSION["username"])) {
			$this->view('inventory_adjustment/detail', $data);
		}
		else{
			$this->load->view('login');
		}
	}

	public function approve() {
	
		$this->load->model('modInventoryAdjustment', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$user = $_SESSION["id"];
		$id = $param["id"];

		$result["adjustment"] = $this->modInventoryAdjustment->approve($id,  $user);
		$result["adjustment"]["approved_by_name"] = $_SESSION["username"]; 

		$this->setProductQty($id);
		echo json_encode($result);
		
	}

	public function delete() {
		$this->load->model('modInventoryAdjustment', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$id = $param["id"];

		$result["adjustment"] = $this->modInventoryAdjustment->delete($id);
		
		echo json_encode($result);
	}
	
	public function setProductQty($id) {
		$this->load->model('modInventoryAdjustmentDetail', "", TRUE);

		$details = $this->modInventoryAdjustmentDetail->getDetailById(["inventory_adjustment_id" => $id])->result_array();


		foreach($details as $i => $detail) {

			if( $detail["type"] == 2) {
				$detail["quantity"] = $detail["quantity"] * -1;
			}

			$this->modInventoryAdjustmentDetail->updateInventory($detail["product_id"], $detail["quantity"]);
		}
		
	}
}
