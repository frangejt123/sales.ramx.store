<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {

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
    }

	public function index()
	{
		$this->load->model('modCustomer', "", TRUE);


		$data = array(
			"customers" => $this->modCustomer->getAll(null)->result_array()
		);

		if(isset($_SESSION["username"])) {
			$this->view('customer/index', $data);
		}else{
			$this->load->view('login');
		}
	}


	private function view($page, $data) {
			$this->load->view("layouts/header");
			$this->load->view($page, $data);
			$this->load->view("layouts/js");
			$this->load->view("customer/js");
			$this->load->view("layouts/footer");
	}

	public function save() {
		$param = $this->input->post(NULL, "true");
		$this->load->model('modCustomer', "", TRUE);

		$image = $param["location_img"];
		$imgname = strtolower(str_replace(" ", "", $param	["name"]));

		if($image != "") {
			list($type, $image) = explode(';', $image);
			list(, $image) = explode(',', $image);
			$image = base64_decode($image);

			$filepath = "assets/location_image/".$imgname.".jpeg";

			file_put_contents($filepath, $image);
		}

		if(!array_key_exists("id", $param)) {
			$customer =  $this->modCustomer->insert($param);
		} else  {
			$customer =  $this->modCustomer->update($param);
		}
	
	
		// $this->db->transComplete();

		echo json_encode($customer);

	}

	public function detail($id) {
		$this->load->model('modCustomer', "", TRUE);
		$this->load->model('modTransaction', "", TRUE);

		$data = array(
			"customer" => $this->modCustomer->getAll(["id" => $id])->row_array(),
			"transactions" => $this->modTransaction->getAllNoImage(array("customer_id" => $id))->result_array(),
			"status" => $this->modTransaction->STATUS,
			"payment_method" => $this->modTransaction->PAYMENT_METHOD
		);

		

		if(isset($_SESSION["username"])) {
			$this->view('customer/detail', $data);
		}
		else{
			$this->load->view('login');
		}
	}


	public function delete() {
		$this->load->model('modCustomer', "", TRUE);
		$param = $this->input->post(NULL, "true");
		$id = $param["id"];

		$result = $this->modCustomer->delete($id);
		
		echo json_encode($result);
	}
	

}
