<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ModInventoryAdjustmentDetail extends CI_Model {

    public $NAMESPACE = "inventory_adjustment_detail";
    private $TABLE = "inventory_adjustment_detail",
            $FIELDS = array(
                "id" => "inventory_adjustment_detail.id",
                "inventory_adjustment_id" => "inventory_adjustment_detail.inventory_adjustment_id",
                "product_id" => "inventory_adjustment_detail.product_id",
                "quantity" => "inventory_adjustment_detail.quantity"
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
		$this->db->from($this->TABLE);
        $this->db->order_by('inventory_adjustment_detail.id', 'ASC');

        $query = $this->db->get();
        return $query;
	}
	
	function getDetailById($param) {
     
        $tablefield = "";

		$this->FIELDS["type"] = "inventory_adjustment.type";
		
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
		$this->db->from($this->TABLE);
		$this->db->join("inventory_adjustment", "inventory_adjustment.id = inventory_adjustment_detail.inventory_adjustment_id", "INNER");
        $this->db->order_by('inventory_adjustment_detail.id', 'ASC');

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

        if ($this->db->insert($this->TABLE, $data)) {
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

        if ($this->db->update($this->TABLE, $data)) {
            $result["success"] = true;
        } else {
            $result["success"] = false;
            $result["error_id"] = $this->db->_error_number();
            $result["message"] = $this->db->_error_message();
        }

        return $result;
    }

	function delete($param){
		$sql = "DELETE FROM `inventory_adjustment_detail` WHERE `inventory_adjustment_detail`.`id` = '".$param["id"]."'";
		$result = array();
		if($this->db->query($sql)){
			$result["success"] = true;
		}
		return $result;
	}

	function addInventory($product, $quantity) {
		$sql = "UPDATE `inventory` SET `qty`=qty+$quantity WHERE  `product_id`=$product";
		$result = array();
		if($this->db->query($sql)){
			$result["success"] = true;
		}
		return $result;
	}

	function lessInventory($product, $quantity) {
		$sql = "UPDATE `inventory` SET `qty`=qty-$quantity WHERE  `product_id`=$product";
		$result = array();
		if($this->db->query($sql)){
			$result["success"] = true;
		}
		return $result;
	}




}
