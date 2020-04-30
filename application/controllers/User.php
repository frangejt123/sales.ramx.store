<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

	public function index(){
		session_start();
		if(isset($_SESSION["username"])) {
			$this->load->model('modUser', "", TRUE);
			$param = $this->input->post(NULL, "true");
			$data["userlist"] = $this->modUser->getAll(null)->result_array();
			$this->load->view('userlist', $data);
		}else{
			$this->load->view('login');
		}
	}

	public function adduser(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modUser', "", TRUE);
		$param["password"] = md5($param["password"]);
		$param["name"] = ucwords($param["name"]);
		$result = array();
		$data["username"] = $param["username"];
		$usernameexist = $this->modUser->getAll($data)->num_rows();
		if($usernameexist > 0){
			$result["error_msg"] = "Username already exist.";
			$result["success"] = false;
		}else{
			$result = $this->modUser->insert($param);
		}

		echo json_encode($result);
	}

	public function update(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modUser', "", TRUE);

		if(isset($param["password"]))
			$param["password"] = md5($param["password"]);

		$param["name"] = ucwords($param["name"]);
		$result = array();
		$data["username"] = $param["username"];
		$userdetail = $this->modUser->getAll($data)->result_array();
		$usernameexist = 0;
		foreach($userdetail as $ind => $row){
			if($row["id"] != $param["id"]){
				$usernameexist++;
			}
		}

		if($usernameexist > 0){
			$result["error_msg"] = "Username already exist.";
			$result["success"] = false;
		}else{
			$result = $this->modUser->update($param);
		}

		echo json_encode($result);
	}

	public function delete(){
		$param = $this->input->post(NULL, "true");
		$this->load->model('modUser', "", TRUE);
		$result = $this->modUser->delete($param);

		echo json_encode($result);
	}
}
