<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class ModPayment extends CI_Model {

	public $NAMESPACE = "payment";
	private $TABLE = "payment",
		$FIELDS = array(
		"id" => "payment.id",
		"transaction_id" => "payment.transaction_id",
		"payment_method" => "payment.payment_method",
		"payment_confirmation_detail" => "payment.payment_confirmation_detail",
		"amount" => "payment.amount",
		"payment_date" => "payment.payment_date"
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
		$this->db->from("payment");
		$this->db->order_by('payment.id', 'DESC');

		$query = $this->db->get();
		return $query;
	}

	function insert($param) {
		$result = array();
		$data = array();

		unset($this->FIELDS["id"]);

		foreach ($this->FIELDS as $alias => $field) {
			if (array_key_exists($alias, $param)) {
				if ($param[$alias] != "") {
					$data[$field] = $param[$alias];
				}
			}
		}

		if ($this->db->insert('payment', $data)) {
			//$result_row = $this->db->query("SELECT LAST_INSERT_ID() AS `id`")->result_object();
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
		unset($param["status"]);
		$id = $param["id"];

		foreach ($this->FIELDS as $alias => $field) {
			if (array_key_exists($alias, $param))
				$data[$field] = $param[$alias];
		}

		$this->db->where("payment.id", $id);

		if ($this->db->update('payment', $data)) {
			$result["success"] = true;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}

	function delete($param){
		$sql = "DELETE FROM `payment` WHERE `payment`.`id` = '".$param["id"]."'";
		$result = array();
		if($this->db->query($sql)){
			$result["success"] = true;
		}
		return $result;
	}

	function updateInventory($param){
		$sql = "UPDATE `inventory` SET `qty` = `qty` + ".$param["qty"]." WHERE `payment_id` = '".$param["id"]."'";
		$this->db->query($sql);
	}

	function getrecent($param){
		$sql = "SELECT `payment`.`payment_method` FROM `payment` WHERE `payment`.`transaction_id` = '".$param["transaction_id"]."' ORDER BY `payment`.`payment_date` DESC LIMIT 1";
		$this->db->query($sql);
	}

	function availQty($id){
		$sql = "SELECT `inventory`.`qty`, `payment`.`description` FROM `payment` LEFT JOIN `inventory`
					ON `payment`.`id` = `inventory`.`payment_id` 
					WHERE `inventory`.`payment_id` = '".$id."'";
		$result = $this->db->query($sql);
		return $result;
	}

}
