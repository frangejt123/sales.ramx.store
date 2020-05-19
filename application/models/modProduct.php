<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ModProduct extends CI_Model {

    public $NAMESPACE = "product";
    private $TABLE = "product",
            $FIELDS = array(
                "id" => "product.id",
                "description" => "product.description",
                "price" => "product.price",
				"uom" => "product.uom",
				"category_id" => "product.category_id",
				"phase_out" => "product.phase_out",
				"store_id" => "product.store_id"
    );

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    function getAll($param) {
        $tablefield = "";
		$this->FIELDS["avail_qty"] = "inventory.qty";
		$this->FIELDS["category"] = "product_category.name";

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
        $this->db->from("product");
		$this->db->join('inventory', 'product.id = inventory.product_id', 'left');
		$this->db->join('product_category', 'product.category_id = product_category.id', 'left');
        $this->db->order_by('product.description', 'ASC');

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

		if ($this->db->insert('product', $data)) {
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

		$this->db->where("product.id", $id);

		if ($this->db->update('product', $data)) {
			$result["success"] = true;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}

	function delete($param){
    	$sql = "DELETE FROM `product` WHERE `product`.`id` = '".$param["id"]."'";
		$result = array();
    	if($this->db->query($sql)){
			$result["success"] = true;
		}
		return $result;
	}

	function updateInventory($param){
    	$sql = "UPDATE `inventory` SET `qty` = `qty` + ".$param["qty"]." WHERE `product_id` = '".$param["id"]."'";
		$this->db->query($sql);
	}

	function availQty($id){
		$sql = "SELECT `inventory`.`qty`, `product`.`description` FROM `product` LEFT JOIN `inventory`
					ON `product`.`id` = `inventory`.`product_id` 
					WHERE `inventory`.`product_id` = '".$id."'";
		$result = $this->db->query($sql);
		return $result;
	}

	function getname($id){
		$sql = "SELECT `product`.`description` FROM `product` WHERE `product`.`id` = '".$id."'";
		$result = $this->db->query($sql);
		return $result;
	}

}
