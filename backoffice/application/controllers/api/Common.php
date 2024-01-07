<?php

require_once 'Inf_Controller.php';

class Common extends Inf_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('api_model');
        $this->load->model('validation_model');
    }

    public function app_info_get()
    {
        $site_info = $this->validation_model->getSiteInformation();
        $site_info['login_logo']  = SITE_URL.'/uploads/images/logos/logo_login.png';
    	$data = [
    		'lang_status' => ($this->MODULE_STATUS['lang_status'] == 'yes'),
            'currency_status' => ($this->MODULE_STATUS['multy_currency_status'] == 'yes'),
    	];

    	$data['languages'] = [];
    	if($this->MODULE_STATUS['lang_status'] == 'yes') {
    		$data['languages'] = $this->api_model->getAllLanuages();
    	}
        $data['currencies'] = [];
        if ($this->MODULE_STATUS['multy_currency_status'] == 'yes') {
            $data['currencies'] = $this->Api_model->getAllCurrencies($this->LOG_USER_ID);
        }
    	if($this->IS_MOBILE) {
    		$app_versions = $this->api_model->getAppVersions();

    		$data['android_version'] = $app_versions['android_version'];
    		$data['ios_version'] = $app_versions['ios_version'];
    	}
        $data['company_info'] = $site_info;
    	$this->set_success_response(200, $data);
    }

    public function check_token_post()
    {
    	$token = $this->post('token');
    	if(!$token) {
    		$this->set_error_response(422, 1002);
    	}

    	if($this->api_model->checkValidAuthToken($token)) {
			$this->set_success_response(200, []);
    	}

		$this->set_error_response(422, 1002);
    }

	public function api_key_get()
	{
        if(DEMO_STATUS == 'yes') {
			$admin_user_name = $this->get('admin_user_name');
			if(!$admin_user_name) {
				$this->set_error_response(422, 1042);
			}
			$apiKey = $this->Api_model->getApiKeyFromAdminUserName($admin_user_name);
			if(!$apiKey) {
				$this->set_error_response(422, 1042);
			}
			$this->set_success_response(200, [
				'key' => $apiKey->api_key
			]);
		}
        else{
            $apiKey = $this->api_model->getApiKeyHere();
            if(!$apiKey) {
                $this->set_error_response(422, 1042);
            }
            $this->set_success_response(200, [
                'key' => $apiKey['api_key']
            ]);
        }
		$this->set_error_response(403, 403);

	}
    public function getAllCountry_get(){
        $data=[
            'country' =>$this->Api_model->getAllCountries(),
        ];
        $this->set_success_response(200,$data);
    }
    //check the username in forgot password
    public function check_username_post(){
        if($this->validation_model->userNameToID($this->post('username'))) {
            $this->set_success_response(204);
        }
        $this->set_error_response(422,1011);
    }

    //validate the user email address
    public function validate_email_post(){
        if($this->post('e_mail') == $this->validation_model->getUserEmailId($this->validation_model->userNameToID($this->post('user_name')))) {
            $this->set_success_response(204);
        }
        $this->set_error_response(422,1011);
    }
    public function add_new_demo_visitor_post()
    {
        $this->form_validation->set_rules('name', 'Name', 'required|max_length[200]');
        $this->form_validation->set_rules('email', 'E-mail', 'required|max_length[200]');
        $this->form_validation->set_rules('mobile', 'Mobile', 'required|numeric|max_length[15]');
        $this->form_validation->set_rules('country', 'Country', 'required');
        if(!$this->form_validation->run()) {
            $this->set_error_response(422, 1004);
        }

        extract($this->validation_model->stripTagsPostArray($this->input->post()));
        $this->load->model('revamp_model');
        if(!$this->revamp_model->checkVisitorEmailReuseStatus($email)) {
            $this->set_error_response(422,1011);
        }
       
        $visitor_id = $this->revamp_model->addPresetDemoVisitor($name, $email, $mobile, $country);
        if(!$visitor_id) {
            $this->set_error_response(422,1011);
        }
        $data = ['message' => lang('otp_sent'), 'visitor_id' => $visitor_id];
        $this->set_success_response(200, $data);

    }
    public function verifyOtp_post()
    {
        $this->load->model('revamp_model');
        $demo_visitor_id = $this->input->post('visitor_id');
        if($demo_visitor_id && $demo_visitor_id != ""){
            $lead_details    = $this->revamp_model->getLeadDetailsFromVisitorId($demo_visitor_id);
        } else {
            $this->set_error_response(422, 1065);
        }
        if(!$lead_details) {
            $this->set_error_response(422, 1066);
        }
        if($lead_details['status'] != 'pending' || $lead_details['access_expiry'] <= date("Y-m-d H:i:s")) {
            $this->set_error_response(422, 1067);
        }
        $otp_expiry = $lead_details['otp_expiry'];
        if(!$this->input->post('demo_otp')) {
            $this->set_error_response(422, 1064);
        }
        if($otp_expiry < date('Y-m-d H:i:s')) {
            $this->set_error_response(422, 1068);
        }

        $email_otp = $this->input->post('demo_otp');

        $otp_check = $this->revamp_model->verifyLeadOTP($lead_details['id'], $email_otp, null);
        if($otp_check) {
            $this->set_success_response(200, ['message' => lang('otp_verified_successfully')]);
        }
        $this->set_error_response(422, 1069);
    }
    public function resendOTP_post()
    {
        $demo_visitor_id = $this->input->post('visitor_id');
        $this->load->model('revamp_model');

        if($demo_visitor_id && $demo_visitor_id != ""){
            $lead_details    = $this->revamp_model->getLeadDetailsFromVisitorId($demo_visitor_id);
        } else {
            $this->set_error_response(422, 1065);
        }
        $res = $this->revamp_model->sendLeadOTP($lead_details['id']);
        $this->set_success_response(200, ['message' => lang('otp_resent_successfully')]);

    }
    public function user_data_post()
    {
        $user_id=$this->input->post('user_id')??0;
        $limit=$this->input->post('limit')??0;
        $offset=$this->input->post('offset')??0;
        $data=$this->validation_model->getAllUserData($limit,$offset,$user_id);
        $data=json_encode($data);
        echo $data;
        exit();
    }
   
}
