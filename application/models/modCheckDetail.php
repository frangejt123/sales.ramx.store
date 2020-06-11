<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class ModCheckDetail extends CI_Model {

	public $NAMESPACE = "check_detail";
	private $TABLE = "check_detail",
		$FIELDS = array(
		"id" => "check_detail.id",
		"payment_id" => "check_detail.payment_id",
		"bank" => "check_detail.bank",
		"accnt_num" => "check_detail.accnt_num",
		"accnt_name" => "check_detail.accnt_name",
		"check_num" => "check_detail.check_num",
		"amount" => "check_detail.amount"
	);

	function __construct() {
		// Call the Model constructor
		parent::__construct();
	}

	function getAll($param) {
		$tablefield = "";

		foreach ($this->FIELDS as $alias => $field) {
			if ($tablefield != "") {
				$tablefield .= ",";
			}
			//Construct table field selection
			$tablefield .= $field . " AS `" . $alias . "`";
			if($param)
				if (array_key_exists($alias, $param)) {
					$this->db->where($field, $param[$alias]);
				}
		}

		$this->db->select($tablefield);
		$this->db->from("check_detail");

		$query = $this->db->get();
		return $query;
	}

	function insert($param) {
		$result = array();
		$data = array();

		foreach ($this->FIELDS as $alias => $field) {
			if (array_key_exists($alias, $param)) {
				if ($param[$alias] != "") {
					$data[$field] = $param[$alias];
				}
			}
		}

		if ($this->db->insert('check_detail', $data)) {
			$result["id"] = $this->db->insert_id();
			$result["success"] = true;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}

	function update($param) {

		$result = array();
		$data = array();

		$payment_id = $param["payment_id"];

		foreach ($this->FIELDS as $alias => $field) {
			if (array_key_exists($alias, $param))
				$data[$field] = $param[$alias];
		}

		$this->db->where($this->FIELDS['payment_id'], $payment_id);

		if ($this->db->update('check_detail', $data)) {
			$result["success"] = true;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}

	function delete($param){
		$sql = "DELETE FROM `check_detail` WHERE `check_detail`.`payment_id` = '".$param["payment_id"]."'";
		$result = array();
		if($this->db->query($sql)){
			$result["success"] = true;
		}
		return $result;
	}

}
