<?php

use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'libraries/REST_API_Controller.php';
require APPPATH . 'libraries/API_Format.php';
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET,PUT, OPTIONS");
header("Access-Control-Allow-Headers: *");
class Inf_Controller extends Core_Inf_Controller
{

    public $DATE_FORMAT = 'd-m-Y';
    public $TIME_FORMAT = 'd-m-Y h:i a';
    public $MODULE_STATUS;
    public $NATIVE_APPS = false;
    public $IP_ADDR;
    public $IS_MOBILE = false;
    public $MLM_PLAN = '';
    protected $ERROR_CODES = [
        401 => 'Unauthorized',
        403 => 'Forbidden',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        429 => 'Too Many Requests',
        1001 => 'Invalid API Key',
        1002 => 'Invalid Access Token',
        1003 => 'Invalid Credentials / Invalid Username or Password',
        1004 => 'Incorrect Input Format / Validation Error',
        1005 => 'Login Blocked',
        1006 => 'Registration Blocked',
        1007 => 'Invalid Sponsor Username',
        1008 => 'Position Not Usable / Position Already Filled',
        1009 => 'Invalid Placement',
        1010 => 'Username Not Available / Username Already Exists',
        1011 => 'Invalid Username / Username Not Found',
        1012 => 'Product Not Available',
        1013 => 'Incorrect Date Format',
        1014 => 'Insufficient E-wallet Balance',
        1015 => 'Incorrect Transaction Password',
        1016 => 'Invalid E-pin Code',
        1017 => 'File Type Not Supported',
        1018 => 'File Size Exceeded',
        1019 => 'KYC Not Verified',
        1020 => 'Unsupported Protocol',
        1021 => 'Incorrect Password',
        1022 => 'Email Address Does Not Match Your Account',
        1023 => 'Invalid Address',
        1024 => 'Error While Uploading File',
        1025 => 'Insufficient Amount',
        1026 => 'Cart Is Empty',
        1027 => 'Requested payout amount is too low',
        1028 => 'Requested payout amount is too high',
        1029 => 'Multilanguage not enabled',
        1030 => 'Error occured! Please try again',
        1031 => 'Payout request sending failed',
        1032 => 'File not found',
        1033 => 'Invalid Leg Position',
        1034 => 'Registration Not Allowed',
        1035 => 'Invalid Sponsor Username',
        1036 => 'Invalid payment Method',
        1037 => 'E-mail Verification Required',
        1038 => 'Too Many Upload Limit',
        1039 => 'Invalid Transaction Details',
        1040 => 'Invalid Captcha',
        1041 => 'ID Already Exists',
        1042 => 'Invalid Admin Username',
        1043 => 'Invalid User',
        1044 => 'E-pin purchase failed',
        1045 => 'E-pin request failed',
        1046 => 'E-pin transfer failed',
        1047 => 'Mail seinding failed',
        1048 => 'Mail deletion failed',
        1048 => 'Invalid mail',
        1049 => 'Invalid payment type',
        1050 => 'Invalid Product',
        1051 => 'Invalid Id',
        1052 => 'Must Be Alteast one',
        1053 => 'Must Be An Integer',
        1054 => 'Repurchase failed',
        1055 => 'Duplicate E-pin',
        1056 => 'Invalid Board',
        1057 => 'Permission Denied',
        1058 => 'No Sufficient Referrals',
        1059 => 'Donation Sending Failed',
        1060 => 'Unable To Send Invitation',
        1061 => 'Invalid Lead',
        1062 => 'Email already exists',
        1063 => 'Failed To Update lead',
        1064 => 'Invalid OTP',
        1065 => 'Visitor ID not found',
        1066 => 'Lead details not found',
        1067 => 'Invalid request',
        1068 => 'OTP expired',
        1069 => 'OTP verification failed',
        1070 => 'This operation is not possible in demo',
        1071 => 'Admin username not valid.',

    ];

    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    function __construct()
    {

        parent::__construct();
        $_SESSION['inf_table_prefix'] = '';

        
        $this->checkFromMobile();
        
        $this->checkNativeApps();
        
        $this->load_default_model_classes();
        
        $this->check_api_key_credentials();
        
        $this->__resTraitConstruct();
        
        $this->methods["{$this->router->method}_{$this->input->method()}"]['limit'] = $this->config->item('rest_max_limit');
        
        $this->load_app_language();
        
        $this->load_default_currency();
        $this->initialize_public_variables();
    }

    public function check_api_key_credentials()
    {
        if (strtolower($this->router->class) == "demo") {
            return TRUE;
        }
        $api_key = $this->input->get_request_header('api-key');
        if ($this->uri->segment(2) == 'common' && $this->uri->segment(1) == 'api' && DEMO_STATUS == 'yes' && $api_key == 'a201cb4c-0fa2-e8bae44e-aee1-3bfceb9a5fc5') {
            $api_details = ['id' => 0,'user_name' => 'admin','table_prefix' => 'inf'];
        } else {
            $api_details = $this->Api_model->getApiDetails($api_key);
        }
        if ($api_details) {
            if (DEMO_STATUS == 'yes') {
                $this->db->set_dbprefix($api_details['table_prefix'] . '_');
            }
            $this->MODULE_STATUS = $this->inf_model->trackModule();
            $this->MLM_PLAN = $this->MODULE_STATUS['mlm_plan'];
            $this->IP_ADDR = $this->input->ip_address();
            $this->PUBLIC_URL = base_url() . "public_html/";
        } else {
            $this->set_error_response(422, 1001, false);
        }
    }
    public function set_error_response_withMsg($status_code, $error_code = null, $default = true, $msg){
        if (empty($error_code)) {
            $error_code = $status_code;
        }
        $description = lang($error_code);
        if ($description == $error_code)
            $description = $this->ERROR_CODES[$error_code] ?? null;
        $response = [
            'status' => false,
            'error' => [
                'code' => $error_code,
                'description' => $msg,
                // 'description' => $this->ERROR_CODES[$error_code] ?? null,
            ]
        ];
        if ($error_code == 1004) {
            if($this->IS_MOBILE) {
                $fields = [];
                $error_array = $this->form_validation->error_array();
                foreach ($error_array as $key => $value) {
                    if(strpos($key, '_err') === false) {
                        $fields[] = ['code' => $key, 'error' => $value];
                    }
                }
                $response['error']['fields'] = $fields;
            } else {
                $response['error']['fields'] = $this->form_validation->error_array();
            }
        }
        if ($default) {
            $this->response($response, $status_code);
        } else {
            http_response_code($status_code);
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }

    public function set_error_response($status_code, $error_code = null, $default = true)
    {
        if (empty($error_code)) {
            $error_code = $status_code;
        }
        $description = lang($error_code);
        if ($description == $error_code)
            $description = $this->ERROR_CODES[$error_code] ?? null;
        $response = [
            'status' => false,
            'error' => [
                'code' => $error_code,
                'description' => $description,
                // 'description' => $this->ERROR_CODES[$error_code] ?? null,
            ]
        ];
        if ($error_code == 1004) {
            if($this->IS_MOBILE) {
                $fields = [];
                $error_array = $this->form_validation->error_array();
                foreach ($error_array as $key => $value) {
                    if(strpos($key, '_err') === false) {
                        $fields[] = ['code' => $key, 'error' => $value];
                    }
                }
                $response['error']['fields'] = $fields;
            } else {
                $response['error']['fields'] = $this->form_validation->error_array();
            }
        }
        if ($default) {
            $this->response($response, $status_code);
        } else {
            http_response_code($status_code);
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }

    public function set_success_response($status_code, $data = '')
    {
        $response = [
            'status' => true
        ];
        if ($data || $data >= 0) {
            $response['data'] = $data;
        }
        $this->response($response, $status_code);
    }

    function load_default_model_classes()
    {
        $this->load->model('inf_model', '', true);
        $this->load->model('validation_model', '', true);
        $this->load->model('Api_model');
    }

    function load_app_language()
    {
        $user_id = $this->rest->user_id ?? "";
        $lang_code = $this->input->get_request_header('Accept-Language');
        $this->LANG_ID = 1;
        $this->LANG_NAME = $this->APP_CONFIG['default_language']['name'];
        if(!$this->Api_model->get_language_status()) {
            return true;
        }
        if($lang_code && $this->IS_MOBILE) {
            $this->LANG_NAME = $this->validation_model->get_language_name($lang_code);
        } elseif ($user_id) {
            $lang_id = $this->inf_model->getDefaultLang($user_id);
            $this->LANG_NAME = $this->inf_model->getLanguageName($lang_id);
        }
        // $this->lang->load("mobile", $this->LANG_NAME . "/api");
    }
    
    function load_default_currency()
    {
        $user_id = $this->rest->user_id ?? "";
        $this->DEFAULT_CURRENCY_CODE = $this->APP_CONFIG['default_currency']['code'];
        $this->DEFAULT_CURRENCY_VALUE = $this->APP_CONFIG['default_currency']['value'];
        $this->DEFAULT_SYMBOL_LEFT = $this->APP_CONFIG['default_currency']['symbol_left'];
        $this->DEFAULT_SYMBOL_RIGHT = $this->APP_CONFIG['default_currency']['symbol_right'];
        $this->PRECISION = $this->APP_CONFIG['precision'];
        if($this->MODULE_STATUS['multy_currency_status'] != 'yes') {
            return true;
        }
        $this->load->model('currency_model');
        $currency_code = $this->input->get_request_header('Accept-Currency');
        $currency_details = [];
        if($currency_code) {
            $currency_details = $this->currency_model->getCurrencyDetailsByCode($currency_code);
        } elseif($user_id) {
            $currency_details = $this->currency_model->getUserDefaultCurrencyDetails($user_id);
        }
        if (!$currency_details || !count($currency_details)) {
            $currency_details = $this->currency_model->getProjectDefaultCurrencyDetails();
        }
        if (!$currency_details || !count($currency_details)) {
            return false;
        }

        $this->DEFAULT_CURRENCY_VALUE = $currency_details['value'];
        $this->DEFAULT_CURRENCY_CODE = $currency_details['code'];
        $this->DEFAULT_SYMBOL_LEFT = $this->security->xss_clean($currency_details['symbol_left']);
        $this->DEFAULT_SYMBOL_RIGHT = $this->security->xss_clean($currency_details['symbol_right']);
        $this->PRECISION = $this->APP_CONFIG['precision'];
        if ($this->DEFAULT_CURRENCY_CODE == 'BTC') {
            $this->PRECISION = $this->PRECISION > 8 ? $this->PRECISION : 8;
        } else {
            $this->PRECISION = $this->PRECISION;
        }
    }

    function checkNativeApps()
    {
        $native = $this->input->get_post("native");
        if ($native) {
            $this->NATIVE_APPS = true;
            $this->IS_MOBILE = true;
        }
    }

    function checkFromMobile()
    {
        $isMobile = $this->input->get_request_header('isMobile');
        if ($isMobile) {
            $this->IS_MOBILE = true;
        }
    }
    function initialize_public_variables(){
        $this->PAGINATION_PER_PAGE = $this->APP_CONFIG['pagination'];
    }

    public function check_demo_access_restriction()
    {
        if (DEMO_STATUS != 'yes') {
            return true;
        }
        if(!$this->LOG_USER_ID) {
            return true;
        }
        $this->load->model('revamp_model');
        if($this->validation_model->isPresetDemo($this->ADMIN_USER_ID)) {
            if(!$this->revamp_model->check_demo_access_ip_restriction_enabled()) {
                return true;
            }
            $ip_details = $this->revamp_model->getDemoAccessIpDetails($_SERVER['REMOTE_ADDR']);
            if(!$ip_details) {
                $this->set_error_response(401);
            }
            if(($ip_details['type'] != 'internal') && (date('Y-m-d H:i:s') > $ip_details['exp_date'])) {
                $this->revamp_model->setIpExpired($_SERVER['REMOTE_ADDR']);
                $this->set_error_response(401);
            }
        } else {
            if(!$this->revamp_model->check_preset_demo_access_otp_enabled()) {
                return true;
            }
            if(!$this->revamp_model->check_otp_verified_demo($this->ADMIN_USER_ID)) {
                $this->set_error_response(401);
            }
        }
    }
}
