<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class ModCustomer extends CI_Model {

	public $NAMESPACE = "customer";
	private $TABLE = "customer",
		$FIELDS = array(
		"id" => "customer.customer_id",
		"name" => "customer.name",
		"contact_number" => "customer.contact_number",
		"delivery_address" => "customer.delivery_address"
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
		$this->db->from("customer");
		$this->db->order_by('customer.name', 'ASC');

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

		if ($this->db->insert('customer', $data)) {
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

		$id = $param["customer_id"];

		foreach ($this->FIELDS as $alias => $field) {
			if (array_key_exists($alias, $param))
				$data[$field] = $param[$alias];
		}

		$this->db->where($this->FIELDS['id'], $id);

		if ($this->db->update('customer', $data)) {
			$result["success"] = true;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}


}
