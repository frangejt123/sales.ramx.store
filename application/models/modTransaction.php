<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ModTransaction extends CI_Model {

    public $NAMESPACE = "transaction";
    private $TABLE = "transaction",
            $FIELDS = array(
                "id" => "transaction.id",
                "datetime" => "transaction.datetime",
				"total" => "transaction.total",
				"customer_id" => "transaction.customer_id",
				"user_id" => "transaction.user_id",
				"delivery_address" => "transaction.delivery_address",
				"delivery_date" => "transaction.delivery_date",
				"payment_method" => "transaction.payment_method",
				"payment_confirmation_detail" => "transaction.payment_confirmation_detail",
				"remarks" => "transaction.remarks",
                "status" => "transaction.status",
                "void_reason" => "transaction.void_reason",
				"haschanges" => "transaction.haschanges",
				"location_image" => "transaction.location_image"
    );

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    function getAll($param) {
        $tablefield = "";
		$this->FIELDS["name"] = "customer.name";
		$this->FIELDS["facebook_name"] = "customer.facebook_name";
		$this->FIELDS["cust_location_image"] = "customer.location_image";
		$this->FIELDS["contact_number"] = "customer.contact_number";

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
        $this->db->from("transaction");
		$this->db->join("customer", 'customer.customer_id = transaction.customer_id');
		$this->db->order_by('transaction.delivery_date', 'DESC');
		$this->db->order_by('transaction.status', 'ASC');

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

        if ($this->db->insert('transaction', $data)) {
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

    function getLastTransactionID($param){
        $sql = "SELECT * from transaction ORDER BY id DESC LIMIT 1";
        return $this->db->query($sql);
    }

    function complete($param) {
        $sql = "UPDATE transaction SET complete = 1 WHERE id = '".$param."'";
        $this->db->query($sql);
    }

    function generatesales($param){
        $datefrom = date("Y-m-d", strtotime($param["datefrom"]))." 00:00:00";
        $dateto = date("Y-m-d", strtotime($param["dateto"]))." 23:59:59";
        $sql = "SELECT SUM(`total`) as total from transaction WHERE `datetime` >= '".$datefrom."' AND `datetime` <= '".$dateto."'";
        $res = $this->db->query($sql);

        return $res;
    }

    function getNewTrasaction($id){
		$sql = "SELECT transaction.*, 
					DATE_FORMAT(transaction.datetime, '%m/%d/%Y %H:%i:%s') as datetime, 
					DATE_FORMAT(transaction.delivery_date, '%m/%d/%Y') as delivery_date,
					customer.name from transaction 
					INNER JOIN customer ON customer.customer_id = transaction.customer_id
					WHERE transaction.id > ".$id."
					ORDER BY transaction.status ASC, 
					transaction.datetime DESC";
		return $this->db->query($sql);
	}

	function haschanges($id){
		$sql = "SELECT transaction.haschanges, transaction.status, transaction.void_reason FROM transaction WHERE transaction.id = ".$id;
		return $this->db->query($sql);
	}

	function updatechanges($id){
		$sql = "UPDATE transaction SET transaction.haschanges = '0' WHERE transaction.id = ".$id;
		return $this->db->query($sql);
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

		if ($this->db->update('transaction', $data)) {
			$result["success"] = true;
			$result["id"] = $id;
		} else {
			$result["success"] = false;
			$result["error_id"] = $this->db->_error_number();
			$result["message"] = $this->db->_error_message();
		}

		return $result;
	}
}
