<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class ModDispatch extends CI_Model {

	public $NAMESPACE = "dispatch";
	private $TABLE = "dispatch",
		$FIELDS = array(
		"id" => "dispatch.id",
		"driver" => "dispatch.driver_id",
		"dispatch_date" => "dispatch.dispatch_date",
		"datetime" => "dispatch.datetime",
		"status" => "dispatch.status"
	);

	function __construct() {
		// Call the Model constructor
		parent::__construct();
	}

	function getAll($param) {
		$tablefield = "";
		$this->FIELDS["driver_name"] = "driver.name";

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
		$this->db->from("dispatch");
		$this->db->join("driver", 'driver.id = dispatch.driver_id', 'left');
		$this->db->order_by('datetime', 'DESC');

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

		if ($this->db->insert('dispatch', $data)) {
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

		if ($this->db->update('dispatch', $data)) {
			$result["success"] = true;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}

	function delete($param){
		$sql = "DELETE FROM `dispatch` WHERE `dispatch`.`id` = '".$param["id"]."'";
		$result = array();
		if($this->db->query($sql)){
			$result["success"] = true;
		}
		return $result;
	}

	function getAvailTransaction(){
		$sql = "SELECT DISTINCT `trx`.`id`, `trx`.`datetime`, `customer`.`name` FROM `transaction` trx
  					INNER JOIN `customer` ON `customer`.`customer_id` = `trx`.`customer_id` 
 					WHERE NOT EXISTS (
                        SELECT `dpd`.`transaction_id` FROM `dispatch_detail` dpd WHERE `dpd`.`transaction_id` = `trx`.`id`
                    ) AND `trx`.`store_id` = '2'";

		$query = $this->db->query($sql);
		return $query;
	}

}
