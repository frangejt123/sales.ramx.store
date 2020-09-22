<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller {

	public function index(){
		session_start();
		if(isset($_SESSION["username"])) {
			
			if ($_SESSION['access_level'] == 2) {
				redirect('/order');
			}
				

			$param = $this->input->post(NULL, "true");
			$this->load->model('modProduct', "", TRUE);
			$this->load->model('modCategory', "", TRUE);
			$param["store_id"] = $_SESSION["store_id"];

			$data["product"] = $this->modProduct->getAll($param)->result_array();
			$data["category"] = $this->modCategory->getAll($param)->result_array();
			$data["store_id"] = $_SESSION["store_id"];

			$this->load->view('product', $data);
		}else{
			$this->load->view('login');
		}
	}

	public function addProduct(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modProduct', "", TRUE);


		if(isset($param["prod_img"])) {
			$image = $param["prod_img"];
			$imgname = strtolower(str_replace(" ", "", $param	["description"]));
	
			if($image != "") {
				list($type, $image) = explode(';', $image);
				list(, $image) = explode(',', $image);
				$image = base64_decode($image);
	
				$filepath = "assets/prod_img/".$imgname.".jpeg";
	
				file_put_contents($filepath, $image);
	
				$param["prod_img"] = $imgname.".jpeg";
			}
		}
	


		$result = $this->modProduct->insert($param);

		echo json_encode($result);
	}

	public function update(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modProduct', "", TRUE);

		if(isset($param["prod_img"])) {
			$image = $param["prod_img"];
			$imgname = strtolower(str_replace(" ", "", $param	["description"]));
	
			if($image != "") {
				list($type, $image) = explode(';', $image);
				list(, $image) = explode(',', $image);
				$image = base64_decode($image);
	
				$filepath = "assets/prod_img/".$imgname.".jpeg";
	
				file_put_contents($filepath, $image);
	
				$param["prod_img"] = $imgname.".jpeg";
			}
		}
		
		
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
