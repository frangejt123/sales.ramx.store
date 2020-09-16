<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ModInventoryAdjustment extends CI_Model {

    public $NAMESPACE = "inventory_adjustment";
    private $TABLE = "inventory_adjustment",
            $FIELDS = array(
			   "id" => "inventory_adjustment.id",
			   "date" => "inventory_adjustment.`date`",
			   "type" => "inventory_adjustment.type",
			   "status" => "inventory_adjustment.status",
			   "remarks" => "inventory_adjustment.remarks",
			   "created_at" => "inventory_adjustment.created_at",
			   "updated_at" => "inventory_adjustment.updated_at",
			   "prepared_by" => "inventory_adjustment.prepared_by",
			   "approved_by" => "inventory_adjustment.approved_by"
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
         
			
			if ($param) {
                if (array_key_exists($alias, $param)) {
                    $this->db->where($field, $param[$alias]);
                } else if(array_key_exists('search', $param) && !empty($param["search"]["value"])) {
					$this->db->or_like($field, $param["search"]["value"]);
				}
            }
        }

        $this->db->select($tablefield);
        $this->db->from($this->TABLE);
		
		if(isset($param["order_by"]) && $param["order_by"] != ""){
			$this->db->order_by($param["order_by"], $param['sort_order']);
		}else{
			$this->db->order_by('id', 'DESC');
		}

		if(isset($param["start"]))
			$this->db->limit( $param["length"], $param["start"]);

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

  

    function approve($id, $approved_by) {
		$sql = "UPDATE inventory_adjustment SET status = 2, approved_by = '$approved_by'  WHERE id = '$id'";
		$result = [];

       if($this->db->query($sql)) {
		$result["success"] = true;
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
			$result["id"] = $id;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}

	
	function delete($id){
		$sql = "DELETE FROM `inventory_adjustment` WHERE `inventory_adjustment`.`id` = '$id'";
		$result = array();
		if($this->db->query($sql)){
			$result["success"] = true;
		}
		return $result;
	}
}
