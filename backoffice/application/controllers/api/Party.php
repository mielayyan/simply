<?php
require_once 'Inf_Controller.php';
/**
 * 
 */
class Party extends Inf_Controller
{
	
	function __construct()
	{
        parent::__construct();
        $this->load->model('party_setup_model');

	}
	public function create_setup_get(){
		$user_id = $this->rest->user_id;
		$host_arr = $this->party_setup_model->getAllHosts($user_id);
		$data = [
			'host' => $host_arr
		];
		$this->set_success_response(200,$data);
	}
	public function getAllCountry_get(){
		$data=[
			'country' =>$this->Api_model->getAllCountries(),
		];
		$this->set_success_response(200,$data);
	}
}
?>