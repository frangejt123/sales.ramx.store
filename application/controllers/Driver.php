<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Driver extends CI_Controller {

	public function index(){
		session_start();
		if(isset($_SESSION["username"])) {
		
				if ($_SESSION['access_level'] == 2) {
					redirect('/order');
				}
		
			$this->load->model('modDriver', "", TRUE);
			$param = $this->input->post(NULL, "true");
			$data["driverlist"] = $this->modDriver->getAll(null)->result_array();
			$this->load->view('driverlist', $data);
		}else{
			$this->load->view('login');
		}
	}

	public function adddriver(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modDriver', "", TRUE);
		$param["name"] = ucwords($param["name"]);
		$result = array();
		$result = $this->modDriver->insert($param);

		echo json_encode($result);
	}

	public function update(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modDriver', "", TRUE);

		$param["name"] = ucwords($param["name"]);
		$result = array();
		$result = $this->modDriver->update($param);

		echo json_encode($result);
	}

	public function delete(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modDriver', "", TRUE);
		$result = $this->modDriver->delete($param);

		echo json_encode($result);
	}
}
