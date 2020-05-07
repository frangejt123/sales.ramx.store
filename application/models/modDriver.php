<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class ModDriver extends CI_Model {

	public $NAMESPACE = "driver";
	private $TABLE = "driver",
		$FIELDS = array(
		"id" => "driver.id",
		"name" => "driver.name",
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
		$this->db->from("driver");
		$this->db->order_by('name', 'ASC');

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

		if ($this->db->insert('driver', $data)) {
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

		if ($this->db->update('driver', $data)) {
			$result["success"] = true;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}

	function delete($param){
		$sql = "DELETE FROM `driver` WHERE `driver`.`id` = '".$param["id"]."'";
		$result = array();
		if($this->db->query($sql)){
			$result["success"] = true;
		}
		return $result;
	}

}
