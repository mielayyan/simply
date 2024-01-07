<?php
require_once 'Inf_Controller.php';
	/**
	 * 
	 */
	class donation extends Inf_Controller
	{
		
		function __construct()
		{
			parent::__construct();
       	 	$this->load->model('validation_model');
       	 	$this->load->model('donation_model');
       	 	$this->LOG_USER_ID = $this->rest->user_id;
        	$this->LOG_USER_TYPE = 'user';
		}

		//get the donation view get 
		public function donation_view_get(){
			$donation_type = $this->validation_model->getColoumnFromTable("configuration","donation_type");
			if($donation_type == "automatic" || $this->MODULE_STATUS['mlm_plan'] != 'Donation'){
	            $this->set_error_response(422, 1057);
	        }
	        $next_level         = '';
	        $next_level_index   = '';
	        $to_user            = '';
	        $min_amount         = 0;
	        $eligible           = false;
	        $font_color         = "#ec564a";
	        $admin              = false;
	        $user_id            = $this->LOG_USER_ID;
	        $current_level      = $this->donation_model->getCurrentLevel($user_id);
	        $rate_table         = $this->donation_model->getDonationAmountTotal($user_id);
	        if ($current_level <= 3) {
	            $next_level_index = $rate_table['index' . ($current_level)];
	            $next_level       = $rate_table['level' . ($current_level)];
	            $min_ref          = $this->donation_model->getReferalCount($next_level_index);
	            $min_amount       = $this->donation_model->getDonationAmount($next_level_index);
	            $referal_count    = $this->validation_model->getReferalCount($user_id);
	            $balance          = $this->validation_model->getUserBalanceAmount($user_id);
	            if($balance<$min_amount){
	            	$this->set_error_response(422, 1025);
	            } elseif($referal_count<$min_ref){
	            	$this->set_error_response(422, 1058);
	            } else {                
	                $user_list   = $this->get_level_user($next_level_index, $user_id);
	                $to_user     = $this->validation_model->IDTouserName($user_list['to_user']);
	                if($user_list['exact_user'] != ''){
	                    $admin = true;
	                }
	            }
	        } else {
	        	$data = [
	        		'top_level' => true  
	        	];
	        	$this->set_success_response(200,$data);
	        }
	        $data = [
	        	'current_level' => $current_level,
	        	'to_user' => $to_user,
	        	'amount' =>$min_amount,
	        	'next_level' =>$next_level,
	        	'top_level' => false
	        ];
	        $this->set_success_response(200,$data);
		}
		function get_level_user($level, $user_id) {
	        $ret['exact_user'] = '';
	        $ret['to_user'] = $this->validation_model->getAdminId();
	        $up_array       = $this->donation_model->getAllUplineId($user_id, 0, 0);
	        
	        if (isset($up_array['detail' . $level]['id'])) {
	            $id    = $up_array['detail' . $level]['id'];
	            $status =  $this->donation_model->checkUserLevelStatus($id, $level);
	            if ($status) {
	                $ret['to_user'] = $up_array['detail' . $level]['id'];
	            } else {
	                $ret['exact_user'] = $up_array['detail' . $level]['id'];
	            }
	        }
	        return $ret;
	    }
	     public function donate_fund_post(){
	     	$to_user_name = $this->post('to_user');
	     	if(!$to_user_name){
	     		$this->set_error_response(422,1051);
	     	}
	     	$to_user_id = $this->validation_model->userNameToID($to_user_name);
	     	$user_id            = $this->LOG_USER_ID;
	        $current_level      = $this->donation_model->getCurrentLevel($user_id);
	        $rate_table         = $this->donation_model->getDonationAmountTotal($user_id);
	        if ($current_level <= 3) {
	            $next_level_index = $rate_table['index' . ($current_level)];
	            $next_level       = $rate_table['level' . ($current_level)];
	            $min_ref          = $this->donation_model->getReferalCount($next_level_index);
	            $min_amount       = $this->donation_model->getDonationAmount($next_level_index);
	            $referal_count    = $this->validation_model->getReferalCount($user_id);
	            $balance          = $this->validation_model->getUserBalanceAmount($user_id);
	            if($balance<$min_amount){
	            	$this->set_error_response(422, 1025);
	            } elseif($referal_count<$min_ref){
	            	$this->set_error_response(422, 1058);
	            } else {                
	                $user_list   = $this->get_level_user($next_level_index, $user_id);
	                $to_user     = $this->validation_model->IDTouserName($user_list['to_user']);
	            }
	            $payment        = lang('normal');
	            $transaction_id = $this->ewallet_model->getUniqueTransactionId();
	            $res            = $this->donation_model->insertDonationtransferDetails($user_id, $user_list['to_user'], $min_amount, $payment, $next_level_index, $transaction_id, $user_list['exact_user']);
	            if ($res) {
	                $this->set_success_response(204);
	            } else {
	            	$this->set_error_response(422,1059);
	            }
	        } else {
	        	$this->set_error_response(422, 1059);
	        } 
	     }
		//recieve donation report
		public function recieve_donation_report_get(){
			if($this->MODULE_STATUS['mlm_plan'] != 'Donation'){
				$this->set_error_response(422,1057);
			}
			$user_id = $this->LOG_USER_ID;
			$total_payout = $this->donation_model->getRecieveDonationReport($user_id);
			$count = count($total_payout);
			$data = [
				'data' => $total_payout,
				'total_count' =>$count
			];
			$this->set_success_response(200,$data);
		} 
		public function sent_donation_report_get(){
			if($this->MODULE_STATUS['mlm_plan'] != 'Donation'){
				$this->set_error_response(422,1057);
			}
			$user_id = $this->LOG_USER_ID;
			$total_payout = $this->donation_model->getSentDonationReport($user_id);
			$count = count($total_payout);
			$data = [
				'data' => $total_payout,
				'total_count' =>$count
			];
			$this->set_success_response(200,$data);
		}
	}

?>