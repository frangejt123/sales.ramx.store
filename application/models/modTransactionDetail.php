<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ModTransactionDetail extends CI_Model {

    public $NAMESPACE = "transaction_detail";
    private $TABLE = "transaction_detail",
            $FIELDS = array(
                "id" => "transaction_detail.id",
                "transaction_id" => "transaction_detail.transaction_id",
                "product_id" => "transaction_detail.product_id",
                "quantity" => "transaction_detail.quantity",
                "total_price" => "transaction_detail.total_price"
    );

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    function getAll($param) {
        $this->FIELDS["description"] = "product.description";
		$this->FIELDS["price"] = "product.price";
		$this->FIELDS["uom"] = "product.uom";
		$this->FIELDS["prod_img"] = "product.prod_img";
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
        $this->db->from("transaction_detail");
        $this->db->join('product', 'product.id = transaction_detail.product_id');
        $this->db->order_by('transaction_detail.product_id', 'ASC');

        $query = $this->db->get();
        return $query;
    }

    function insert($param) {
        $result = array();
        $data = array();
		unset($param["price"]);

        foreach ($this->FIELDS as $alias => $field) {
            if (array_key_exists($alias, $param)) {
                if ($param[$alias] != "") {
                    $data[$field] = $param[$alias];
                }
            }
        }

        if ($this->db->insert('transaction_detail', $data)) {
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
		unset($param["price"]);

        foreach ($this->FIELDS as $alias => $field) {
            if (array_key_exists($alias, $param))
                $data[$field] = $param[$alias];
        }

        $this->db->where($this->FIELDS['id'], $id);

        if ($this->db->update('transaction_detail', $data)) {
            $result["success"] = true;
        } else {
            $result["success"] = false;
            $result["error_id"] = $this->db->_error_number();
            $result["message"] = $this->db->_error_message();
        }

        return $result;
    }

	function delete($param){
		$sql = "DELETE FROM `transaction_detail` WHERE `transaction_detail`.`id` = '".$param["id"]."'";
		$result = array();
		if($this->db->query($sql)){
			$result["success"] = true;
		}
		return $result;
	}

    function addQty($param) {
        $sql = "UPDATE transaction_detail SET serve = (serve + 1) WHERE id = '".$param."'";
        $this->db->query($sql);
     }

    function minusQty($param) {
        $sql = "UPDATE transaction_detail SET serve = (serve - 1) WHERE id = '".$param."'";
        $this->db->query($sql);
    }

    function generatepms($param){
        $datefrom = date("Y-m-d", strtotime($param["datefrom"]))." 00:00:00";
        $dateto = date("Y-m-d", strtotime($param["dateto"]))." 23:59:59";
        $sql = "SELECT SUM(`quantity`) as qty, description from
                     `transaction_detail` INNER JOIN `product` ON `product`.`id` = `transaction_detail`.`product_id`
                     INNER JOIN `transaction` ON `transaction`.`id` = `transaction_detail`.`transaction_id`
                     WHERE `transaction`.`datetime` >= '".$datefrom."' AND `transaction`.`datetime` <= '".$dateto."'
                     GROUP BY `transaction_detail`.`product_id` ORDER BY `product`.`description`";
        $res = $this->db->query($sql);

        return $res;
    }

}
