<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Productmovement extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{	
		$this->load->view('productmovement');
	}

	public function generate(){
		$param = $this->input->post(NULL, "true");

		$this->load->model('modTransactionDetail', "", TRUE);
		$sales = $this->modTransactionDetail->generatepms($param)->result_array();

		echo json_encode($sales);
	}
}
