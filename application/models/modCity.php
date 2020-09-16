<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Modcity extends CI_Model {

	public $NAMESPACE = "city";
	private $TABLE = "city",
		$FIELDS = array(
		"id" => "city.id",
		"name" => "city.name"
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
		$this->db->from("city");
		$this->db->order_by('city.name', 'ASC');

		$query = $this->db->get();
		return $query;
	}

	function insert($param) {
		$result = array();
		$data = array();
		unset($this->FIELDS["id"]);

	


		foreach ($this->FIELDS as $alias => $field) {
			if (array_key_exists($alias, $param)) {					// if(array_key_exists("location_image", $param))
				if ($param[$alias] != "") {							//    if($param["location_image] != "")
					$data[$field] = $param[$alias];					//			$data[$field] = $param[$alias]
				}													//						
			}
		}

		if ($this->db->insert('city', $data)) {
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

		
		
		if(array_key_exists("id", $param)) {
			$id = $param["id"];
		} else {
			$id = $param["city_id"];
		}

		
		// if($param["location_image"] == ""){
		// 	unset($this->FIELDS["location_image"]);
		// }

		foreach ($this->FIELDS as $alias => $field) {
			if (array_key_exists($alias, $param)) {
				if ($param[$alias] != "") {		
					$data[$field] = $param[$alias];
				}
			}
			
		}

		$this->db->where($this->FIELDS['id'], $id);

		if ($this->db->update('city', $data)) {
			$result["success"] = true;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}


	function delete($id){
		$sql = "DELETE FROM `city` WHERE `city`.`city_id` = '$id'";
		$result = array();
		if($this->db->query($sql)){
			$result["success"] = true;
		}
		return $result;
	}


}
