<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class ModDispatchDetail extends CI_Model {

	public $NAMESPACE = "dispatch_detail";
	private $TABLE = "dispatch_detail",
		$FIELDS = array(
		"id" => "dispatch_detail.id",
		"dispatch_id" => "dispatch_detail.dispatch_id",
		"transaction_id" => "dispatch_detail.transaction_id",
	);

	function __construct() {
		// Call the Model constructor
		parent::__construct();
	}

	function getAll($param) {
		$tablefield = "";
		$this->FIELDS["customer_name"] = "customer.name";
		$this->FIELDS["transaction_date"] = "transaction.datetime";

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
		$this->db->from("dispatch_detail");
		$this->db->join("transaction", 'transaction.id = dispatch_detail.transaction_id', 'inner');
		$this->db->join("customer", 'transaction.customer_id = customer.customer_id', 'inner');
		$this->db->order_by('transaction_id', 'ASC');

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

		if ($this->db->insert('dispatch_detail', $data)) {
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

		$id = $param["id"];

		foreach ($this->FIELDS as $alias => $field) {
			if (array_key_exists($alias, $param))
				$data[$field] = $param[$alias];
		}

		$this->db->where($this->FIELDS['id'], $id);

		if ($this->db->update('dispatch_detail', $data)) {
			$result["success"] = true;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}

	function delete($param){
		$sql = "DELETE FROM `dispatch_detail` WHERE `dispatch_detail`.`id` = '".$param["id"]."'";
		$result = array();
		if($this->db->query($sql)){
			$result["success"] = true;
		}
		return $result;
	}

}
