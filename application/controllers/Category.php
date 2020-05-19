<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends CI_Controller {

	public function index(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modCategory', "", TRUE);

		$category = $this->modCategory->getAll($param)->result_array();
		echo json_encode($category);
	}

	public function savechanges(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modCategory', "", TRUE);

		$error = 0;
		foreach($param["data"] as $ind => $row){
			if($row["status"] == "new"){
				$res = $this->modCategory->insert($row);
				if(!$res)
					$error++;
			}
			if($row["status"] == "edit"){
				$res = $this->modCategory->update($row);
				if(!$res)
					$error++;
			}
			if($row["status"] == "delete"){
				$res = $this->modCategory->delete($row);
				if(!$res)
					$error++;
			}
		}

		if($error == 0)
			echo "success";
		else
			echo "failed";

		//$res = $this->modTransaction->update($param);
		//echo json_encode($res);

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
