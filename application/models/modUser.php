<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class ModUser extends CI_Model {

	public $NAMESPACE = "user";
	private $TABLE = "user",
		$FIELDS = array(
		"id" => "user.id",
		"username" => "user.username",
		"password" => "user.password",
		"access_level" => "user.access_level"
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
		$this->db->from("user");

		$query = $this->db->get();
		return $query;
	}

}
