<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class ModAuditTrail extends CI_Model {

	public $NAMESPACE = "user_audit_trails";
	private $TABLE = "user_audit_trails",
		$FIELDS = array(
		"id" => "user_audit_trails.id",
		"user_id" => "user_audit_trails.user_id",
		"user_type" => "user_audit_trails.user_type",
		"transaction_id" => "user_audit_trails.transaction_id",
		"event" => "user_audit_trails.event",
		"table_name" => "user_audit_trails.table_name",
		"old_values" => "user_audit_trails.old_values",
		"new_values" => "user_audit_trails.new_values",
		"created_at" => "user_audit_trails.created_at"
	);

	function __construct() {
		// Call the Model constructor
		parent::__construct();
	}

	function getAll($param) {
		$tablefield = "";
		$this->FIELDS["user_name"] = "user.name";

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
		$this->db->from("user_audit_trails");
		$this->db->join('user', 'user_audit_trails.user_id = user.id', 'left');
		$this->db->order_by('user_audit_trails.created_at', 'DESC');

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

		if ($this->db->insert('user_audit_trails', $data)) {
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

		$this->db->where("user_audit_trails.id", $id);

		if ($this->db->update('user_audit_trails', $data)) {
			$result["success"] = true;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}

	function delete($param){
		$sql = "DELETE FROM `user_audit_trails` WHERE `user_audit_trails`.`id` = '".$param["id"]."'";
		$result = array();
		if($this->db->query($sql)){
			$result["success"] = true;
		}
		return $result;
	}

	function updateInventory($param){
		$sql = "UPDATE `inventory` SET `qty` = `qty` + ".$param["qty"]." WHERE `user_audit_trails_id` = '".$param["id"]."'";
		$this->db->query($sql);
	}

	function availQty($id){
		$sql = "SELECT `inventory`.`qty`, `user_audit_trails`.`description` FROM `user_audit_trails` LEFT JOIN `inventory`
					ON `user_audit_trails`.`id` = `inventory`.`user_audit_trails_id` 
					WHERE `inventory`.`user_audit_trails_id` = '".$id."'";
		$result = $this->db->query($sql);
		return $result;
	}

}
