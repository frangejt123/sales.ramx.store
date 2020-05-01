<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller {

	public function index(){
		session_start();
		if(isset($_SESSION["username"])) {
			$param = $this->input->post(NULL, "true");
			$this->load->model('modProduct', "", TRUE);
			$this->load->model('modCategory', "", TRUE);
			$data["product"] = $this->modProduct->getAll(null)->result_array();
			$data["category"] = $this->modCategory->getAll(null)->result_array();
			$this->load->view('product', $data);
		}else{
			$this->load->view('login');
		}
	}

	public function addProduct(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modProduct', "", TRUE);
		$result = $this->modProduct->insert($param);

		echo json_encode($result);
	}

	public function update(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modProduct', "", TRUE);
		$result = $this->modProduct->update($param);

		echo json_encode($result);
	}

	public function delete(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modProduct', "", TRUE);
		$result = $this->modProduct->delete($param);

		echo json_encode($result);
	}
}
