<?php

require_once 'Inf_Controller.php';

class Profile extends Inf_Controller {

    const SUBSCRIPTION_PAYMENT_TYPE = "subscription_renewal";
    const PT_FREE_PURCHASE          = "free_purchase" ;

    function __construct() {
        parent::__construct();
        $this->load->model('validation_model');
        $this->load->model('password_model');
        $this->load->model('Api_model');
        $this->load->model('profile_model');
        $this->load->model('configuration_model');
        $this->load->model('captcha_model');
        $this->LOG_USER_ID = $this->rest->user_id;
        $this->LOG_USER_TYPE = 'user';
    }

    function index_get()
    {
        $user_id = $this->rest->user_id;
        $data = $this->Api_model->getProfileData($user_id);
        if ($data) {
            $this->set_success_response(200, $data);
        } else {
            $this->set_error_response(401, 1003);
        }
    }

    public function subscription_details_get() {
        $data = $this->profile_model->getScriptionDetails($this->rest->user_id);
        if(empty($data)) {
            $this->set_error_response(401, 1003);
        }
        $this->set_success_response(200, [
            'id' => $data['product_id'],
            'validity' => $data['product_validity'],
            'price' => $data['product_value']
        ]);
    }

    function check_product_exists($product_id) {
        if(!$this->product_model->isActiveProduct($product_id, 'registration')) {
            $this->form_validation->set_message('check_product_exists', 'invalid product');
            return FALSE;
        }
        return TRUE;
    }

    function check_payment_exists($payment_type) {
        switch($payment_type) {
            case "freejoin":
                $payment_method = "Free Joining";
            break;
            case "banktransfer":
                $payment_method = "Bank transfer";
            break;
            case "ewallet":
                $payment_method = "E-wallet";
            break;
            case "epin":
                $payment_method = "E-pin";
            break;
            default:
                $payment_method = "";
            break;
        }
        if($this->payment_model->getGatewayStatus($payment_method, 'membership_renewal') != "yes") {
            $this->form_validation->set_message('check_payment_exists', 'invalid payment');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * [subscription renewal Post]
     * @return [json] [description]
     */
    public function subscription_post() {

        // Validation
        $this->load->model('payment_model');
        $this->load->model('repurchase_model');
        $this->form_validation->set_data($this->post());
        $this->form_validation->set_rules('product_id', 'product_id', 'required|callback_check_product_exists');
        $this->form_validation->set_rules('payment_method', 'payment_method', 'required|callback_check_payment_exists');
        if(!$this->form_validation->run()) {
            $this->set_error_response(422, 1004);
        }
        $payment_status = false;
        $user_id = $this->rest->user_id;
        $subscription_config = $this->configuration_model->getSubscriptionConfig();
        $purchase['user_id'] = $this->rest->user_id;

        if($this->MODULE_STATUS['subscription_status'] == 'yes' && $subscription_config['based_on'] == 'amount_based') {
            $purchase['total_amount'] = $subscription_config['fixed_amount'];     
        } else {
            $product = $this->product_model->getProductDetails($this->post('product_id'));
            $purchase['total_amount'] = $product[0]['product_value'];
        }
        $payment_method = "";
        $payment_type = "";
        $product_details = $this->product_model->getPackageDetails($this->post('product_id'));
        $package_details[0]['id'] = $product_details['prod_id'];
        $user_name = $this->validation_model->IdToUserName($this->LOG_USER_ID);
        $this->repurchase_model->begin();
        switch($this->post('payment_method')) {
            case "freejoin":
                $payment_type = "free_purchase";
                $payment_method = "Free Joining";
                $purchase['by_using'] = 'free join';
                $payment_status = true;
            break;

            case "banktransfer":
                $payment_type = 'bank_transfer';
                $purchase['by_using'] = 'bank_transfer';
                $ewallet_user = $package_validity_upgrade['user_name_ewallet'];
                $payment_receipt = $this->payment_model->getReceipt($user_name, Profile::SUBSCRIPTION_PAYMENT_TYPE);
                $payment_status = true;
            break;

            case "ewallet":
                $payment_type = 'ewallet';
                $ewallet_user = $this->post('user_name_ewallet');
                $ewallet_trans_password = $this->post('tran_pass_ewallet');
                $product_id = $this->post('product_id');
                $this->load->service('Ewallet_payment_service');
                $validated = $this->ewallet_payment_service->validate_payment($ewallet_user, $ewallet_trans_password, $product_id, 'subscription_renewal');
                if($validated['status']) {
                    $purchase['by_using'] = 'ewallet';
                    $payment_status = $this->ewallet_payment_service->run_payment($ewallet_user, $ewallet_trans_password, $product_id, 'subscription_renewal', 'package_validity');
                } else {
                    $this->set_error_response(422,$validated['code']);
                }
            break;

            case "epin":
                $payment_type = 'epin';
                $pin_count = $this->post('pin_array');
                $pin_details = [];
                for ($i = 1; $i <= $pin_count; $i++) {
                    if ($this->post("epin$i")) {
                        $pin_number = $this->post("epin$i");
                        $pin_details[$i]['pin'] = $pin_number;
                        $pin_details[$i]['i'] = $i;
                    }
                }
                $pin_array = $this->payment_model->checkAllEpins($pin_details, $this->post('product_id'), $this->MODULE_STATUS['product_status'], $this->LOG_USER_ID, 'subscription_renewal', true);
                $is_pin_ok = $pin_array["is_pin_ok"];

                if(!$is_pin_ok) {
                    $this->set_error_response(422, 1049);
                }
                $this->payment_model->begin();
                $purchase['by_using'] = 'pin';
                $res = $this->payment_model->UpdateUsedUserEpin($pin_array, $pin_count);
                if ($res) {
                    $pin_array['user_id'] = $user_id;
                    $this->payment_model->insertUsedPin($pin_array, $pin_count, false, 'package_validity');
                    $payment_status = true;
                    $this->payment_model->commit();
                }
            break;
            case 'paypal' :
                $payment_gateway_array = $this->register_model->getPaymentGatewayStatus("membership_renewal");
                    if($payment_gateway_array['paypal_status'] == 'no'){
                        $this->set_error_response(401, 1009);
                    }
                    $payment_details = array(
                    'payment_method' => 'paypal',
                    'token_id' => $this->post('paypal_token'),
                    'currency' => $this->post('currency'),
                    'amount' => $payment_amount,
                    'acceptance' => '',
                    'payer_id' => $this->post('PayerID'),
                    'user_id' => $this->LOG_USER_ID,
                    'status' => '',
                    'card_number' => '',
                    'ED' => '',
                    'card_holder_name' => '',
                    'submit_date' => date("Y-m-d H:i:s"),
                    'pay_id' => '',
                    'error_status' => '',
                    'brand' => ''
                );
                $this->register_model->insertintoPaymentDetails($payment_details);
                    $payment_type = "paypal";
                    $payment_status = true; 
                break;
            default:
                $this->set_error_response(422, 1049);
            break;
        }
        if($payment_status) {
            $invoice_no = $this->member_model->packageValidityUpgrade($package_details, $purchase);
            $data = serialize($purchase);
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Membership reactivation through ' . lang($purchase['by_using']), $this->LOG_USER_ID, $data);

            if ($invoice_no) {
                $this->repurchase_model->commit();
                $enc_order_id = $this->validation_model->encrypt($invoice_no);
                $this->set_success_response(200);
            } else {
                $this->repurchase_model->rollback();
                $this->set_error_response(422, 1030);
            }
        } 
        $this->set_error_response(422, 1030);
    }

    public function personal_put() {

        $user_id = $this->rest->user_id;      
        $post_arr = $this->put();
        $this->form_validation->set_data($post_arr);

        if ($this->validatePersonalInfo()) {
            $post_arr['first_name'] = $post_arr['firstname'];
            $post_arr['last_name'] = $post_arr['lastname'] ?? '';
            $post_arr['dob'] = $post_arr['date_of_birth'];
            $res = $this->profile_model->updatePersonalInfo($user_id, $post_arr, '');
            if ($res) {
                $this->set_success_response(204);
            } else {
                $this->set_error_response(500); 
            }
        } else {
            $this->set_error_response(422, 1004);
        }
    }

    // public function validatePersonalInfo()
    // {
    //     $this->form_validation->set_rules('firstname', lang('first_name'), 'trim|required|min_length[3]|max_length[32]|callback__alpha_space');
    //     $this->form_validation->set_rules('lastname', lang('last_name'), 'trim|min_length[3]|max_length[32]|callback__alpha_space');
    //     $this->form_validation->set_rules('gender', lang('gender'), 'trim|required|in_list[M,F]', ['in_list' => lang('You_must_select_gender')]);        
    //     $this->form_validation->set_rules('date_of_birth', lang('date_of_birth'), 'callback_validate_age_year');

    //     $validation_status = $this->form_validation->run();
    //     return $validation_status;
    // }

    public function contact_put() {

        $user_id = $this->rest->user_id;      
        $post_arr = $this->put();

        $this->form_validation->set_data($post_arr);
        if ($this->validateContactInfo()['status']) {
            $post_arr['address'] = $post_arr['address_line1'];
            $post_arr['address2'] = isset($post_arr['address_line2']) ? $post_arr['address_line2'] : '' ;
            $post_arr['pincode'] = isset($post_arr['zip_code']) ? $post_arr['zip_code'] : '' ;
            $post_arr['land_line'] = isset($post_arr['landline']) ? $post_arr['landline'] : '' ;
            $res = $this->profile_model->updateContactInfo($user_id, $post_arr, '');
            if ($res) {
                $this->set_success_response(204);
            } else {
                $this->set_error_response(500); 
            }
        } else {
            $this->set_error_response(422, 1004);
        }
    }

    public function bank_put() {

        $user_id = $this->rest->user_id;      
        $post_arr = $this->put();

        $this->form_validation->set_data($post_arr);
        if ($this->validateBankInfo()) {
            $post_arr['bank_name'] = isset($post_arr['bank_name']) ? $post_arr['bank_name'] : '';
            $post_arr['branch_name'] = isset($post_arr['branch_name']) ? $post_arr['branch_name'] : '';
            $post_arr['account_holder'] = isset($post_arr['account_holder']) ? $post_arr['account_holder'] : '';
            $post_arr['account_no'] = isset($post_arr['account_number']) ? $post_arr['account_number'] : '';
            $post_arr['ifsc'] = isset($post_arr['ifsc']) ? $post_arr['ifsc'] : '';
            $post_arr['pan'] = isset($post_arr['pan']) ? $post_arr['pan'] : '';
            $res = $this->profile_model->updateBankInfo($user_id, $post_arr);
            if ($res) {
                $this->set_success_response(204);
            } else {
                $this->set_error_response(500); 
            }
        } else {
            $this->set_error_response(422, 1004);
        }
    }

    // public function validateBankInfo()
    // {
    //     $this->form_validation->set_rules('bank_name', lang('bank_name'), 'trim|min_length[3]|max_length[32]|callback__alpha_space');
    //     $this->form_validation->set_rules('branch_name', lang('bank_branch'), 'trim|min_length[3]|max_length[32]|callback__alpha_space');
    //     $this->form_validation->set_rules('account_holder', lang('acct_holder_name'), 'trim|min_length[3]|max_length[32]|callback__alpha_space');
    //     $this->form_validation->set_rules('account_number', lang('account_no'), 'trim|min_length[3]|max_length[32]|alpha_numeric');
    //     $this->form_validation->set_rules('ifsc', lang('ifsc'), 'trim|min_length[3]|max_length[32]|alpha_numeric');
    //     $this->form_validation->set_rules('pan', lang('pan_no'), 'trim|min_length[3]|max_length[32]|alpha_numeric');

    //     $validation_status = $this->form_validation->run();
    //     return $validation_status;
    // }

    public function _alpha_space($str = '')
    {
        if (!$str) {
            return true;
        }
        $res = (bool)preg_match('/^[A-Z ]*$/i', $str);
        if (!$res) {
            $this->form_validation->set_message('_alpha_space', lang('form_validation_alpha_space'));
        }
        return $res;
    }

    public function validate_age_year($dob)
    {
        if(!$this->validate_date($dob)) {
            $this->form_validation->set_message('validate_age_year', lang('form_validation_validate_age_year'));
            return false;
        }
        
        $age_limit = $this->configuration_model->getAgeLimitSetting();
        if ($age_limit == 0) {
            return true;
        }
        $year = date('Y', strtotime($dob));
        $current_year = date('Y');
        if (($current_year - $year) >= $age_limit) {
            return true;
        } else {
            $this->form_validation->set_message('validate_age_year', sprintf(lang('You_should_be_atleast_n_years_old'), $age_limit));
            return false;
        }
    }
    
    function validate_date($date) {
        if (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts)) {
            // check whether the date is valid or not
            if (checkdate($parts[2], $parts[3], $parts[1])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function _alpha_city_address($str_in = '')
    {
        if (!preg_match("/^([a-zA-Z0-9\s\.\,\-])*$/i", $str_in)) {
            $this->form_validation->set_message('_alpha_city_address', lang('city_field_characters'));
            return false;
        } else {
            return true;
        }
    }

    public function image_post() {

        $user_id = $this->rest->user_id;
        $response = [];
        if (!isset($_FILES['image'])) {
            $this->set_error_response(400, 1032);
        }
        if (!empty($_FILES['image'])) { 
            $upload_config = $this->validation_model->getUploadConfig();
            $upload_count = $this->validation_model->getUploadCount($user_id);
            if ($upload_count >= $upload_config) {
                $this->set_error_response(400, 1038);
            }
        }
        
        if ($_FILES['image']['error'] != 4) {
            $random_number = floor($user_id * rand(1000, 9999));
            $config['file_name'] = "pro_" . $random_number;
            $config['upload_path'] = IMG_DIR . 'profile_picture/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = '2048';
            $config['remove_spaces'] = true;
            $config['overwrite'] = false;
            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('image')) {
                $error_keys = $this->upload->list_error_keys();
                $err = reset($error_keys);
                if ($err[0] == 'upload_file_exceeds_limit' || $err[0] == 'upload_file_exceeds_form_limit') {
                    $code = 1018;
                } else if ($err[0] == 'upload_invalid_filetype') {
                    $code = 1017;
                } else {
                    $code = 1024;
                }
                $this->set_error_response(422, $code);
            } else {
                $image_arr = array('upload_data' => $this->upload->data());
                $new_file_name = $image_arr['upload_data']['file_name'];

                $res = $this->profile_model->changeProfilePicture($user_id, $new_file_name);
                $this->validation_model->updateUploadCount($user_id);
                if ($res) {
                    $this->set_success_response(204);
                } else {
                    $this->set_error_response(500); 
                }
            }
        } else {
            $this->set_error_response(422, 1032);
        }
    }

    public function image_delete_get(){
        $profile_picture = $this->validation_model->getProfilePicture($this->LOG_USER_ID);
        $path = IMG_DIR . 'profile_picture/'.$profile_picture;
        if($profile_picture != 'nophoto.png') {
            if (unlink($path)) {
                $user_id = $this->rest->user_id;
                // dd($user_id);
                $profile_picture = "nophoto.png";
                $res = $this->profile_model->changeProfilePicture($user_id, $profile_picture);
                // echo 'success'; 
                $this->set_success_response(200); 
            } else {
                // echo 'fail';
                $this->set_error_response(500);
            }    
        }else {
            // echo 'Cannot delete Default profile pic ';
            $this->set_error_response(404);
        }
    }

    public function profile_pic_get() {
        $profile_picture = $this->validation_model->getProfilePicture($this->LOG_USER_ID);
        if (!file_exists(IMG_DIR . "profile_picture/" . $profile_picture)) {
            $profile_picture = "nophoto.png";
        }

        $name = IMG_DIR . "profile_picture/" . $profile_picture;
        $fp = fopen($name, 'rb');

        header('Access-Control-Allow-Origin: *');
        header("Content-Type: image/jpeg");
        header("Content-Length: " . filesize($name));
        fpassthru($fp);
        $this->response('', 200);
        exit;

        /*$this->set_success_response(200,  SITE_URL."/uploads/images/profile_picture/" . $profile_picture);*/
    }

    public function view_get() {
        $this->lang->load('profile', $this->LANG_NAME);
        $this->lang->load('home', $this->LANG_NAME);
        $this->load->model('home_model');
        $this->load->model('country_state_model');
        $this->load->model('rank_model');
        $settings_details   = [];
        $user_details       = $this->validation_model->getUserDetails($this->LOG_USER_ID, $this->LOG_USER_TYPE);
        $current_rank       = $this->rank_model->currentRankName($this->LOG_USER_ID);
        $individual_details = $this->home_model->individulaDetails($this->LOG_USER_ID, ['personal_pv', 'gpv']);
        $binary_tree        = $this->home_model->individulaDetails($this->LOG_USER_ID, ['total_left_carry','total_right_carry']);
        $pro_file_arr       = $this->profile_model->getProfileDetails($this->LOG_USER_ID, $this->MODULE_STATUS['product_status']);
        $methods_array      = [];
        $country_telephone_code = $this->country_state_model->getCountryTelephoneCode($user_details['user_detail_country']);

        //profile
        if (file_exists(IMG_DIR . "profile_picture/" . $pro_file_arr['details']['profile_photo'])) {
            $profile_picture_full = SITE_URL. "/uploads/images/profile_picture/" . $pro_file_arr['details']['profile_photo'];
        } else {
            $profile_picture_full = SITE_URL. "/uploads/images/profile_picture/nophoto.png";
        }
        $passwordPolicy = $this->validation_model->getPasswordPolicyArray();
        foreach ($passwordPolicy as $key => $value) {
            if($key == 'disableHelper'){
                $passwordPolicy[$key] = $passwordPolicy[$key]==0? true:false;   
            }
            if($passwordPolicy[$key]==0 && $key!='disableHelper'){
                unset($passwordPolicy[$key]);
            }
        }
        // $$passwordPolicy['disableHelper'] = $$passwordPolicy['disableHelper'] == 1?false:true;
        $data['profile'] = [
            'full_name' => $user_details['user_detail_name']." ".$user_details['user_detail_second_name'],
            'user_name' => $this->validation_model->IdToUserName($this->LOG_USER_ID),
            'user_photo' => $profile_picture_full,
            'email' => $user_details['user_detail_email']?$user_details['user_detail_email']:NULL,
            'password_policy' => $passwordPolicy
        ];
        if($this->MODULE_STATUS['kyc_status'] == 'yes') {
            $data['profile']['kyc_status'] = ($user_details['kyc_status'] == 'yes');
        }

        if ($this->MODULE_STATUS['product_status'] == "yes") {

            if ($this->MODULE_STATUS['opencart_status'] == "yes") {
                $membership = $this->validation_model->getOpenCartProductNameFromUserID($this->LOG_USER_ID);
            } else {
                $membership = $this->validation_model->getProductNameFromUserID($this->LOG_USER_ID);
            }
            
            $data['profile']['membership_package'] = [
                'title' => lang('current_package'),
                'name' => $membership,
            ];
            
            $store_id = "";
            if ($this->MODULE_STATUS['package_upgrade'] == "yes") {
                $this->load->model('upgrade_model');
                $current_package_details = $this->upgrade_model->getMembershipPackageDetails($this->LOG_USER_ID);
                if($current_package_details){
                    $upgradable_package_list = $this->upgrade_model->getUpgradablePackageList($current_package_details);
                }
                if (isset($upgradable_package_list) && count($upgradable_package_list)>0) {
                    $data['profile']['membership_package']['upgrade_link'] = '';
                    if ($this->MODULE_STATUS['opencart_status'] == "yes") {
                        if (DEMO_STATUS == 'yes') {
                            $store_id = "&id=" . str_replace("_", "", $this->db->dbprefix);
                        }
                        $data['profile']['membership_package']['upgrade_link'] = SITE_URL . '/store/index.php?route=upgrade/upgrade' . $store_id;
                    }
                    else{
                        $data['profile']['membership_package']['upgrade_link'] = "";
                    }
                }
            }
        }

        if ($this->MODULE_STATUS['product_status'] == "yes" && $this->MODULE_STATUS['subscription_status'] == 'yes') {
            $store_id = "";
            $data['profile']['membership_package']['product_validity'] = [
                'title' => lang('membership_will_expire'),
                'date' => $pro_file_arr['details']['product_validity'] ?? '',
                'status' => true,
            ];
            if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                $data['profile']['membership_package']['product_validity']['renewal_link'] = '';
                if ($this->MODULE_STATUS['opencart_status'] == "yes") {
                    if (DEMO_STATUS == 'yes') {
                        $store_id = "&id=" . str_replace("_", "", $this->db->dbprefix);
                    }
                    $data['profile']['membership_package']['product_validity']['renewal_link'] = SITE_URL . '/store/index.php?route=renewal/renewal' . $store_id;
                }
            }
        }

        // rank 
        $rank_configuration = $this->configuration_model->getRankConfiguration();
        $rank_details = $this->configuration_model->getActiveRankDetails($current_rank['rank_id'] ?? '');
        if($this->MODULE_STATUS['rank_status'] == "yes" &&$rank_configuration['joinee_package'] != 1)
        {
            $data['profile']['rank'] = [
                'title' =>lang('rank'),
                'curent_rank' =>$current_rank['rank_name'] ?? lang('na'),
                'rank_color' => isset($rank_details[0]) ? $rank_details[0]['rank_color'] : ""
            ];
        }
        //end of rank 
        $data['extra_data'] = [];
        //placement and sponsor data
        $placement_data = [
            'sponsor' => [
                'title' => lang('sponsor'),
                'text' => 'sponsor',
                'head' => $pro_file_arr['details']['sponsor_name']
            ],
            'placement' => [
                'title' => lang('placement'),
                'text' => 'placement',
                'head' => $pro_file_arr['details']['father_name']
            ]
        ];
        //end placement and sponsor data

        // personal pv and group pv
        $pv = [
            'personal' => [
                 'head' => $individual_details->personal_pv ?: 0,
                 'text' => 'personalPv',
                 'title' => lang('personal'),
                ],
            'group' => [
                 'head' => $individual_details->gpv ?: 0,
                 'text' => 'groupPV',
                 'title' => lang('group'),
                ]
        ];
        //end of personal pv and group pv
        // binary plan special
        $carry=[];
        if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
            $placement_data['position'] = [
                'title' => lang('position'),
                'text' => 'position',
                'head' => ($pro_file_arr['details']['position'] == 'L')?
                            (($this->IS_MOBILE)?lang('left'):'left'):
                            (($this->IS_MOBILE)?lang('right'):'right'),
            ];
            $carry =[
                'leftCarry' => [
                     'head' => $binary_tree->total_left_carry,
                     'text' => 'leftCarry',
                     'title' => lang('total_left_carry'),
                ],
                'rightCarry' => [
                     'head' => $binary_tree->total_right_carry,
                     'text' => 'rightCarry',
                     'title' => lang('total_right_carry'),
                ]
            ];
        }
        // end binary plan special
        if($this->IS_MOBILE) {
            if(isset($pv)) {
                $placement_data['pv'] = $pv;
            }
            if(isset($carry)) {
                $placement_data['carry'] = $carry;
            }
            $data['extra_data'] = $placement_data;
        } else {
            $data['extra_data'] = [
                'placement' => array_values($placement_data),
                'pv' => [...array_values($pv), ...array_values($carry ?? [])]
            ];
        }


        // editable fields in profile page
        $preset_fields = $this->Api_model->getPresetSignupFields();
        $user_profile_data =$pro_file_arr['details'];
        $personal_details_fields = [];
        if(isset($preset_fields['first_name'])) {
            $personal_details_fields[] = [
                'title' => lang('first_name'),
                'code' => 'firstName',
                'value' => ($user_details['user_detail_name'])?$user_details['user_detail_name']:NULL,
                'type' => 'text',
                'required' => ($preset_fields['first_name']['required'] == 'yes')
            ];
        }
        if(isset($preset_fields['last_name'])) {
            $personal_details_fields[] = [
                'title' => lang('last_name'),
                'code' => 'lastName',
                'value' => ($user_details['user_detail_second_name'])?$user_details['user_detail_second_name']:NULL,
                'type' => 'text',
                'required' => ($preset_fields['last_name']['required'] == 'yes')
            ];
        }
        if(isset($preset_fields['gender'])) {
            $personal_details_fields[] = [
                'title' => lang('gender'),
                'code' => 'gender',
                'value' => ($user_details['user_detail_gender'])?$user_details['user_detail_gender']:NULL,
                'type' => 'select',
                'required' => ($preset_fields['gender']['required'] == 'yes'),
                'options' => [
                    ['title' => lang('male'), 'code' => 'male', 'value' => 'M'],
                    ['title' => lang('female'), 'code' => 'female', 'value' => 'F']
                ]
            ];
        }
        if(isset($preset_fields['date_of_birth'])) {
            $personal_details_fields[] = [
                'title' => lang('date_of_birth'),
                'code' => 'dateOfBirth',
                'value' => ($user_details['user_detail_dob'])?$user_details['user_detail_dob']:NULL,
                'type' => 'date',
                'required' => ($preset_fields['date_of_birth']['required'] == 'yes')
            ];
        }
        $personal_details = [
            'title' => lang('personal_details'),
            'code' => 'personalDetails',
            'fields' => $personal_details_fields
        ];
        // contact details
        $contact_details_fields = [];
        if(isset($preset_fields['adress_line1'])) {
            $contact_details_fields[] = [
                'title' => lang('adress_line1'),
                'code' => 'addressLine1',
                'value' => ($user_details['user_detail_address'])?$user_details['user_detail_address']:NULL,
                'type' => 'text',
                'required' => ($preset_fields['adress_line1']['required'] == 'yes'),
                'field_name' => 'address_line1',
            ];
        }
        if(isset($preset_fields['adress_line2'])) {
            $contact_details_fields[] = [
                'title' => lang('adress_line2'),
                'code' => 'addressLine2',
                'value' => ($user_details['user_detail_address2'])?$user_details['user_detail_address2']:NULL,
                'type' => 'text',
                'required' => ($preset_fields['adress_line2']['required'] == 'yes'),
                'field_name' => 'address_line2',
            ];
        }
        if(isset($preset_fields['country'])) {
            $contact_details_fields[] = [
                'title' => lang('country'),
                'code' => 'country',
                'value' => ($user_details['user_detail_country'])?$user_details['user_detail_country']:NULL,
                'type' => 'select',
                'required' => ($preset_fields['country']['required'] == 'yes'),
                'options' => $this->Api_model->getAllCountries(),
                'field_name' => 'country',
            ];
        }
        if(isset($preset_fields['state'])) {
            $this->load->model('country_state_model');
            $contact_details_fields[] = [
                'title' => lang('state'),
                'code' => 'state',
                'value' => ($user_details['user_detail_state'])?$user_details['user_detail_state']:NULL,
                'type' => 'select',
                'required' => ($preset_fields['state']['required'] == 'yes'),
                'options' => ($user_details['user_detail_country'])?$this->Api_model->getStatesFromCountry($user_details['user_detail_country']):[],
                'field_name' => 'state',
            ];
        }
        if(isset($preset_fields['city'])) {
            $contact_details_fields[] = [
                'title'    => lang('city'),
                'code'     => 'city',
                'value'    => ($user_details['user_detail_city'])?$user_details['user_detail_city']:NULL,
                'type'     => 'text',
                'required' => ($preset_fields['city']['required'] == 'yes'),
                'field_name' => 'city',
            ];
        }
        if(isset($preset_fields['pin'])) {
            $contact_details_fields[] = [
                'title'    => lang('zip_code'),
                'code'     => 'zipCode',
                'value'    => ($user_profile_data['pincode'])?$user_profile_data['pincode']:NULL,
                'type'     => 'text',
                'required' => ($preset_fields['pin']['required'] == 'yes'),
                'field_name' => 'pin',
            ];
        }
        if(isset($preset_fields['email'])) {
            $contact_details_fields[] = [
                'title'    => lang('email'),
                'code'     => 'email',
                'value'    => ($user_profile_data['email'])?$user_profile_data['email']:NULL,
                'type'     => 'text',
                'required' => ($preset_fields['email']['required'] == 'yes'),
                'field_name' => 'email',
            ];
        }
        if(isset($preset_fields['mobile'])) {
            $contact_details_fields[] = [
                'title'    => lang('mob_no_10_digit'),
                'code'     => 'mobile',
                'value'    => ($user_profile_data['mobile'])?$user_profile_data['mobile']:NULL,
                'type'     => 'text',
                'required' => ($preset_fields['mobile']['required'] == 'yes'),
                'field_name' => 'mobile',
                'country_code' => $country_telephone_code
            ];
        }
        if(isset($preset_fields['land_line'])) {
            $contact_details_fields[] = [
                'title'    => lang('land_line_no'),
                'code'     => 'landLine',
                'value'    => ($user_profile_data['land'])?$user_profile_data['land']:NULL,
                'type'     => 'text',
                'required' => ($preset_fields['land_line']['required'] == 'yes'),
                'field_name' => 'land_line',
            ];
        }
        $contact_details = [
            'title'  => lang('contact_details'),
            'code'   => 'contactDetails',
            'fields' => $contact_details_fields
        ];
        //
        //Bank Details 
        $bank_details_fields = [];
       
        $bank_details_fields[] = [
            'title'    => lang('bank_name'),
            'code'     => 'bankName',
            'value'    => ($user_details['user_detail_nbank'])?$user_details['user_detail_nbank']:NULL,
            'type'     => 'text',
            'required' => false,
        ];
        $bank_details_fields[] = [
            'title'    => lang('branch_name'),
            'code'     => 'branchName',
            'value'    => ($user_details['user_detail_nbranch'])?$user_details['user_detail_nbranch']:NULL,
            'type'     => 'text',
            'required' => false,
        ];
        $bank_details_fields[] = [
            'title'    => lang('account_holder'),
            'code'     => 'accountHolder',
            'value'    => ($user_details['user_detail_nacct_holder'])?$user_details['user_detail_nacct_holder']:NULL,
            'type'     => 'text',
            'required' => false,
        ];
        $bank_details_fields[] = [
            'title'    => lang('account_no'),
            'code'     => 'accountNo',
            'value'    => ($user_details['user_detail_acnumber'])?$user_details['user_detail_acnumber']:NULL,
            'type'     => 'text',
            'required' => false,
        ];
        $bank_details_fields[] = [
            'title'    => lang('ifsc'),
            'code'     => 'ifsc',
            'value'    => ($user_details['user_detail_ifsc'])?$user_details['user_detail_ifsc']:NULL,
            'type'     => 'text',
            'required' => false,
        ];
         $bank_details_fields[] = [
            'title'    => lang('pan'),
            'code'     => 'pan',
            'value'    => ($user_details['user_detail_pan'])?$user_details['user_detail_pan']:NULL,
            'type'     => 'text',
            'required' => false,
        ];
        $bank_details = [
            'title'  => lang('bank_details'),
            'code'   => 'bankDetails',
            'fields' => $bank_details_fields
        ];
        // end of bank details

        //Payment Details
        $payment_gateway = $this->profile_model->getActivePaymentGateway();
        $payment_method  = $this->payout_model->gatewayList();
        foreach($payment_gateway as $gateway )
        {
            
            switch ($gateway['gateway_name']) {
            case "Paypal":
                $title =lang('paypal_account');
                $code  = 'paypalAccount';
                $value = $user_profile_data["paypal_account"]?$user_profile_data["paypal_account"]:NULL;
                break;

            case "Bitcoin":
                $title =lang('blocktrail');
                $code  = 'blocktrailAccount';
                $value = $user_profile_data["blocktrail_account"]?$user_profile_data["blocktrail_account"]:NULL;
                break;

            case "Blockchain":
                $title =lang('blockchain_wallet_address');
                $code  = 'blockchainAccount';
                $value = $user_profile_data["blockchain_account"]?$user_profile_data["blockchain_account"]:NULL;
                break;

            default:
                $title =lang('bitgo');
                $code  = 'bitgoAccount';
                $value = $user_profile_data["bitgo_account"]?$user_profile_data["bitgo_account"]:NULL;
                break;
        }
            $payment_details_fields [] = [
                'title'    => $title,
                'code'     => $code,
                'value'    => $value,
                'type'     => 'text',
                'required' => false,
            ];
        }
        foreach ($payment_method as $method) {
            switch ($method['gateway_name']) {
            case "Bitcoin":
                $title =lang('blocktrail');
                $code  = $method['gateway_name'];
                $value = $method['gateway_name'];
                break;
            default:
                $title = $method['gateway_name'];
                $code  = $method['gateway_name'];
                $value = $method['gateway_name'];
                break;
            }
            $methods = [
                    "value"   =>  $value,
                    "title"   =>  $title,
                    "code"    =>  $code,

            ];
            $methods_array[]=$methods;

        }
        
        $payment_details_fields [] = [
                'title'    => lang('payment_method'),
                'code'     => 'paymentMethod',
                'value'    => $user_profile_data['payout_type'],
                'type'     => 'select',
                'required' => false,
                'options'  => $methods_array
        ];

        $payment_details = [
            'title'  => lang('payment_gateway'),
            'code'   => 'paymentDetails',
            'fields' => $payment_details_fields
        ];
        //End of Payment Details
        
        //Settings Details 
        $setting_details_fields = [];
        if($this->MODULE_STATUS['lang_status'] == 'yes' && !$this->IS_MOBILE )
        {
            $this->load->model('multi_language_model');
            $languages = $this->Api_model->getAllLanuages();
            $lang_array = [];
            foreach ($languages as $language) {
                $language = [
                    "value"   =>  $language['code'],
                    "title"   =>  $language['label'],
                    "code"    =>  $language['label'],
                    "lang_id" =>  $language['id']

                ];
                $lang_array[]=$language;
               
            }

            $setting_details_fields[] = [
            'title'    => lang('language'),
            'code'     => 'language',
            'value'    => $this->multi_language_model->languageIdtoCode($user_profile_data['lang_id'])??NULL,
            'type'     => 'select',
            'required' => true,
            'options'  => $lang_array
            ];
        }
        if($this->MODULE_STATUS['mlm_plan'] == 'Binary')
        {
            $get_leg_type = $this->tree_model->get_leg_type($this->LOG_USER_ID);
            $setting_details_fields[] = [
            'title'    => lang('binary_leg_settings'),
            'code'     => 'binaryLegSettings',
            'value'    => $get_leg_type,
            'type'     => 'select',
            'required' => true,
            'options'  =>[
                         ['title' => lang('none'), 'code' => 'none', 'value' => 'any'],
                         ['title' => lang('left_leg'), 'code' => 'leftLeg', 'value' => 'left'],
                         ['title' => lang('right_leg'), 'code' => 'rightLeg', 'value' => 'right'],
                         ['title' => lang('weak_leg'), 'code' => 'weakLeg', 'value' => 'weak_leg']
                         ]
            ];
            

        }
        if($this->MODULE_STATUS['multy_currency_status'] == 'yes')
        {
            if(!$this->IS_MOBILE){
                $currencys = $this->Api_model->getAllCurrencies($this->LOG_USER_ID);
                $currency_array = [];
                foreach ($currencys as $currency) {
                    $currency = [
                        "value"         =>  $currency['id'],
                        "title"         =>  $currency['symbol_left']." ".$currency['title'],
                        "code"          =>  $currency['symbol_left']." ".$currency['title'],
                        "currency_code" =>  $currency['code'],
                        "precision"     =>  $currency['precision'],
                        "symbol_left"   =>  $currency['symbol_left'],
                        "currency_value"=>  $currency['value']

                    ];
                    $currency_array[]=$currency;
                   
                }
                $setting_details_fields[] = [
                'title'    => lang('currency'),
                'code'     => 'currency',
                'value'    => ($user_profile_data['default_currency'])?$user_profile_data['default_currency']:NULL,
                'type'     => 'select',
                'required' => true,
                'options'  => $currency_array
                ];
            }
        }
        // 
        if($this->MODULE_STATUS['google_auth_status'] == 'yes')
        {
            $setting_details_fields[] = [
            'title'    => lang('google_auth_status'),
            'code'     => 'googleAuthStatus',
            'value'    => $user_profile_data['google_auth_status'],
            'type'     => 'select',
            'required' => true,
            'options'  =>[
                         ['title' => lang('enabled'), 'code' => 'enabled', 'value' => 'yes'],
                         ['title' => lang('disabled'), 'code' => 'disabled', 'value' => 'no'],
                         ]
            ];
        }
        if($this->MODULE_STATUS['lang_status'] == 'yes' || $this->MODULE_STATUS['mlm_plan'] == 'Binary' || $this->MODULE_STATUS['multy_currency_status'] == 'yes' || $this->MODULE_STATUS['google_auth_status'] == 'yes')
        {

            $settings_details = [
                'title'  => lang('settings'),
                'code'   => 'settingstDetails',
                'fields' => $setting_details_fields
            ];

        }
        
        // End of Settings

         // 
        $data['edit_fields']     = compact('personal_details', 'contact_details','bank_details','payment_details','settings_details');
        if($this->IS_MOBILE) {
            $data['edit_fields'] = array_values($data['edit_fields']);
        }

        // end editable fields in profile page

        $this->set_success_response(200, $data);
    }



    public function countryChange_get()
    {
        $this->load->model('country_state_model');
            $data = $this->Api_model->getStatesFromCountry($this->get('country_id'))
            ;
        $this->set_success_response(200, $data);
    }

    // update personal details
    public function personalDetails_put()
    {
        $user_id = $this->rest->user_id;      
        $post_arr = $this->put();
        $this->form_validation->set_data($post_arr);
        $validated = $this->validatePersonalInfo();
        if($validated['status']) {
            $post_arr['first_name'] = $post_arr['firstName'];
            $post_arr['last_name'] = $post_arr['lastName'] ?? '';
            $post_arr['dob'] = $post_arr['dateOfBirth'];
            $post_arr['gender'] = $post_arr['gender'];
            $opencart_status = $this->MODULE_STATUS['opencart_status'] == "yes" && $this->MODULE_STATUS['opencart_status_demo'] == "yes";
            $res = $this->profile_model->updatePersonalInfo($user_id, $post_arr, $opencart_status);
            if ($res) {
                    //insert configuration_change_history
                    $settings = $this->configuration_model->getSettings();
                    if ($settings['profile_updation_history']) {
                        $history = "Updated Personal info as First Name :" . $post_arr['first_name'] . ", ";
                        $history .= "Last Name :" . $post_arr['last_name'] . ", ";
                        $history .= "Gender :" . $post_arr['gender'] . ", ";
                        $history .= "D.O.B :" . $post_arr['dob'];

                        $this->configuration_model->insertConfigChangeHistory('profile updation', $history);
                        $history = "";
                    }
                    //
                    $data = [
                        'status'  => true,
                        'message' => lang('personal_info_update_success')
                    ];
                    $this->set_success_response(200, $data);
            } 
            else {
                $this->set_error_response(500);
            }
        }
        else 
        {
            $this->set_error_response(422, 1004);
        }
        
    }
    // validate personal details feild
    public function validatePersonalInfo()
    {
        $this->form_validation->set_rules('firstName', lang('first_name'), 'trim|required|min_length[3]|max_length[32]|callback__alpha_space');
        $this->form_validation->set_rules('lastName', lang('last_name'), 'trim|callback_check_required[last_name]|min_length[1]|max_length[32]|callback__alpha_space');
        $this->form_validation->set_rules('gender', lang('gender'), 'trim|required|in_list[M,F]', ['in_list' => lang('You_must_select_gender')]);        
        $this->form_validation->set_rules('dateOfBirth', lang('date_of_birth'), 'callback_validate_age_year');
        if (!$this->form_validation->run()) {
            return [
                'status' => false,
                'error_type' => 'validation_error',
                'validation_error' => $this->form_validation->error_array(),
            ];
        }
        return [
            'status' => true
        ];
    }
    // End of validate personal details feild
    // end of personal Deatils

    // contact details
    public function contactDetails_put()
    {
        $user_id = $this->rest->user_id;      
        $post_arr = $this->put();
        $this->form_validation->set_data($post_arr);
        $validated = $this->validate_contact_info();
        if($validated) {
            if($this->IS_MOBILE){
                $post_arr['address'] = $post_arr['addressLine1'];
                $post_arr['address2'] = isset($post_arr['addressLine2']) ? $post_arr['addressLine2'] : '' ;
            } else {
                $post_arr['address'] = $post_arr['address_line1'];
                $post_arr['address2'] = isset($post_arr['address_line2']) ? $post_arr['address_line2'] : '' ;
            }

            $post_arr['pincode'] = isset($post_arr['zipCode']) ? $post_arr['zipCode'] : '' ;
            $post_arr['land_line'] = isset($post_arr['landLine']) ? $post_arr['landLine'] : '' ;
            $opencart_status = $this->MODULE_STATUS['opencart_status'] == "yes" && $this->MODULE_STATUS['opencart_status_demo'] == "yes";
            $res = $this->profile_model->updateContactInfo($user_id, $post_arr, $opencart_status);
                if ($res) {
                    //insert configuration_change_history
                    $settings = $this->configuration_model->getSettings();
                    if ($settings['profile_updation_history']) {
                        $history = "Updated Contact Info as Address Line 1:" . $post_arr['address'] . ", ";
                        $history .= "Address Line 2 :" . $post_arr['address2'] . ", ";
                        if ($post_arr['country'] == '0') {
                            $history .= "Country : " . ", ";
                        } else {
                            $history .= "Country :" . $countries['0']['country_name'] . ", ";
                        }
                        if ($post_arr['state'] == '0') {
                            $history .= "State : " . ", ";
                        } else {
                            $history .= "State :" . $states['0']['state_name'] . ", ";
                        }
                        $history .= "City :" . $post_arr['city'] . ", ";
                        $history .= "Mobile :" . $post_arr['mobile'] . ", ";
                        $history .= "LandLine :" . $post_arr['land_line'] . ", ";
                        $history .= "Email :" . $post_arr['email'] . ", ";
                        $history .= "Pincode :" . $post_arr['pincode'];

                        $this->configuration_model->insertConfigChangeHistory('profile updation', $history);
                        $history = "";
                    
                    } 
                    $data = [
                        'status'  => true,
                        'message' => lang('contact_info_update_success')
                    ];
                    $this->set_success_response(200, $data);
                }
                else {
                    $this->set_error_response(500);
                }

        
        } else {
            $this->set_error_response(422, 1004);
        }
    
    }
    
    public function validate_contact_info() {
        $this->lang->load('validation', $this->LANG_NAME);    
        $this->lang->load('register', $this->LANG_NAME);    
        $this->form_validation->set_rules('address_line1', 'adress_line1', 'trim|callback_check_required[adress_line1]|max_length[1000]', [
                'max_length' => sprintf(lang('maxlength'), lang('address_line1'), "1000")
            ]
        );
        $this->form_validation->set_rules('address_line2', lang('adress_line2'), 'trim|callback_check_required[adress_line2]|max_length[1000]', [
            'max_length' => sprintf(lang('maxlength'), lang('address_line2'), "1000")
        ]);

        $this->form_validation->set_rules('pincode', lang('pin'), 'trim|max_length[50]|callback_check_required[pin]', [
                'max_length' => sprintf(lang('maxlangth'), lang('pincode'), "50"),
        ]);
       
        $this->form_validation->set_rules('country', lang('country'), 'trim|callback_check_required[country]');
        $this->form_validation->set_rules('state', lang('state'), 'trim|callback_check_required[state]');
        $this->form_validation->set_rules('city', lang('city'), 'trim|max_length[250]|callback_check_required[city]', [
                'max_length' => lang('string_max_length'),
        ]);
        $this->form_validation->set_rules('email', lang('email'), 'trim|required|max_length[250]|valid_email', [
                'max_length' => sprintf(lang('maxlength'), lang('email'), "250"),
                'valid_email' => lang('valid_email'),
        ]);
        $this->form_validation->set_rules('mobile', lang('mobile_no'), 'trim|required|regex_match[/^[\s0-9+()-]+$/]|max_length[50]', [
                'max_length' => sprintf(lang('maxlength'), lang('mobile_no'), "50"),
                'regex_match' => lang('phone_number'),
        ]);
        $this->form_validation->set_rules('land_line', lang('mobile'), 'trim|regex_match[/^[\s0-9+()-]+$/]|max_length[50]|callback_check_required[land_line]', [
                'max_length' => sprintf(lang('maxlength'), lang('land_line'), "50"),
                'regex_match' => lang('phone_number')
        ]);
        return $this->form_validation->run();
    }
    
     public function check_required($field_value, $field_name) {
        if ($this->profile_model->getRequiredStatus($field_name) == 'yes') {
            if ($field_value == '') {
                // $this->form_validation->set_message('check_required', sprintf(lang('the_n_field_is_required'), lang($field_name)));
                return false;
            } else {
                return true;
            }
        } 
        return true;
    }
    
    public function validateContactInfo()
    {
        $this->form_validation->set_rules('addressLine1', 'adress_line1', 'trim|required|min_length[3]|max_length[32]');
        $this->form_validation->set_rules('addressLine2', lang('adress_line2'), 'trim|min_length[3]|max_length[32]');
        $this->form_validation->set_rules('country', lang('country'), 'trim|required|is_natural');
        $this->form_validation->set_rules('state', lang('state'), 'trim|is_natural');
        $this->form_validation->set_rules('city', lang('city'), 'trim|required|min_length[3]|max_length[32]|callback__alpha_city_address');
        $this->form_validation->set_rules('zipCode', lang('zip_code'), 'trim|min_length[3]|max_length[10]|is_natural');
        $this->form_validation->set_rules('email', lang('email'), 'trim|required|valid_email');
        $this->form_validation->set_rules('mobile', lang('mobile_no'), 'trim|required|is_natural|min_length[5]|max_length[10]');
        $this->form_validation->set_rules('landLine', lang('land_line_no'), 'trim|is_natural|min_length[5]|max_length[10]');

        if (!$this->form_validation->run()) {
            return [
                'status' => false,
                'error_type' => 'validation_error',
                'validation_error' => $this->form_validation->error_array(),
            ];
        }
        return [
            'status' => true
        ];
    }
    public function validateContactInfoNew()
    {
        $this->form_validation->set_rules('addressLine1', lang('adress_line1'), 'required|min_length[3]|max_length[32]');
        $this->form_validation->set_rules('addressLine2', lang('adress_line2'), 'trim|min_length[3]|max_length[32]');
        $this->form_validation->set_rules('country', lang('country'), 'trim|required|is_natural');
        $this->form_validation->set_rules('state', lang('state'), 'trim|is_natural');
        $this->form_validation->set_rules('city', lang('city'), 'trim|required|min_length[3]|max_length[32]|callback__alpha_city_address');
        $this->form_validation->set_rules('zipCode', lang('zip_code'), 'trim|min_length[3]|max_length[10]|is_natural');
        $this->form_validation->set_rules('email', lang('email'), 'trim|required|valid_email');
        $this->form_validation->set_rules('mobile', lang('mobile_no'), 'trim|required|is_natural|min_length[5]|max_length[10]');
        $this->form_validation->set_rules('landLine', lang('land_line_no'), 'trim|is_natural|min_length[5]|max_length[10]');
        return $this->form_validation->run();
        /*if (!$this->form_validation->run()) {
            return [
                'status' => false,
                'error_type' => 'validation_error',
                'validation_error' => $this->form_validation->error_array(),
            ];
        }
        return [
            'status' => true
        ];*/
    }
    // end of validation
    // end of contact details

   // Bank Details
    public function bankDetails_put()
    {
        $user_id = $this->rest->user_id;      
        $post_arr = $this->put();
        $this->form_validation->set_data($post_arr);
        $validated = $this->validateBankInfo();
        if($validated['status']) {
            $post_arr['bank_name'] = isset($post_arr['bankName']) ? $post_arr['bankName'] : '';
            $post_arr['branch_name'] = isset($post_arr['branchName']) ? $post_arr['branchName'] : '';
            $post_arr['account_holder'] = isset($post_arr['accountHolder']) ? $post_arr['accountHolder'] : '';
            $post_arr['account_no'] = isset($post_arr['accountNo']) ? $post_arr['accountNo'] : '';
            $post_arr['ifsc'] = isset($post_arr['ifsc']) ? $post_arr['ifsc'] : '';
            $post_arr['pan'] = isset($post_arr['pan']) ? $post_arr['pan'] : '';
            $res = $this->profile_model->updateBankInfo($user_id, $post_arr);
            if ($res) {
                //insert configuration_change_history
                    $settings = $this->configuration_model->getSettings();
                    if ($settings['profile_updation_history']) {
                        $history = "Updated Bank Info as Account no. :" . $post_arr['account_no'] . ", ";
                        $history .= "IFSC :" . $post_arr['ifsc'] . ", ";
                        $history .= "Bank Name :" . $post_arr['bank_name'] . ", ";
                        $history .= "Account Holder :" . $post_arr['account_holder'] . ", ";
                        $history .= "Branch Name :" . $post_arr['branch_name'] . ", ";
                        $history .= "PAN :" . $post_arr['pan'];

                        $this->configuration_model->insertConfigChangeHistory('profile updation', $history);
                        $history = "";
                    }
                $data = [
                    'status'  => true,
                    'message' => lang('bank_info_update_success')
                ];
                 $this->set_success_response(200, $data);
            } 
            else {
                $this->set_error_response(500);
            }

        }
        else 
        {
            $this->set_error_response(422, 1004);
        }
    }
    // validation start
        public function validateBankInfo()
        {
            $this->form_validation->set_rules('bankName', lang('bank_name'), 'trim|min_length[3]|max_length[32]|callback__alpha_space');
            $this->form_validation->set_rules('branchName', lang('bank_branch'), 'trim|min_length[3]|max_length[32]|callback__alpha_space');
            $this->form_validation->set_rules('accountHolder', lang('acct_holder_name'), 'trim|min_length[3]|max_length[32]|callback__alpha_space');
            $this->form_validation->set_rules('accountNo', lang('account_no'), 'trim|min_length[3]|max_length[32]|alpha_numeric');
            $this->form_validation->set_rules('ifsc', lang('ifsc'), 'trim|min_length[3]|max_length[32]|alpha_numeric');
            $this->form_validation->set_rules('pan', lang('pan_no'), 'trim|min_length[3]|max_length[32]|alpha_numeric');

            if (!$this->form_validation->run()) {
            return [
                'status' => false,
                'error_type' => 'validation_error',
                'validation_error' => $this->form_validation->error_array(),
            ];
            }
            return [
                'status' => true
            ];
        }
    // end of validation

   // End of Bank Details
    
    //Payment Details
    public function paymentMethod_put()
    {
        $user_id = $this->rest->user_id;      
        $post_arr = $this->put();
        $post_arr['paypal_account'] = isset($post_arr['paypalAccount']) ? $post_arr['paypalAccount'] : '';
        $post_arr['blockchain_account'] = isset($post_arr['blockchainAccount']) ? $post_arr['blockchainAccount'] : '';
        $post_arr['bitgo_account'] = isset($post_arr['bitgoAccount']) ? $post_arr['bitgoAccount'] : '';
        $post_arr['blocktrail_account'] = isset($post_arr['blocktrailAccount']) ? $post_arr['blocktrailAccount'] : '';
        $post_arr['payment_method'] = isset($post_arr['paymentMethod']) ? $post_arr['paymentMethod'] : '';        
        $this->form_validation->set_data($post_arr);
        $validated = $this->validatePaymentInfo();
        if($validated['status']) {
            $res = $this->profile_model->updatePaymentDetails($user_id, $post_arr);
            if ($res) {
                    //insert configuration_change_history
                    $settings = $this->configuration_model->getSettings();
                    if ($settings['profile_updation_history']) {
                        $history = "Updated Payment details as";
                        if (isset($post_arr['paypal_account'])) {
                            if ($post_arr['paypal_account'] == '') {
                                $history .= " Paypal Account: NA,";
                            } else {
                                $history .= " Paypal Account: " . $post_arr['paypal_account'] . ",";
                            }
                        }
                        if (isset($post_arr['blockchain_account'])) {
                            if ($post_arr['blockchain_account'] == '') {
                                $history .= " Blockchain Address: NA,";
                            } else {
                                $history .= " Blockchain Address: " . $post_arr['blockchain_account'] . ", ";
                            }
                        }
                        if (isset($post_arr['bitgo_account'])) {
                            if ($post_arr['bitgo_account'] == '') {
                                $history .= " BitGo Address: NA,";
                            } else {
                                $history .= " BitGo Address: " . $post_arr['bitgo_account'] . ",";
                            }
                        }
                        if (isset($post_arr['blocktrail_account'])) {
                            if ($post_arr['blocktrail_account'] == '') {
                                $history .= " Blocktrail Address: NA";
                            } else {
                                $history .= " Blocktrail Address: " . $post_arr['blocktrail_account'];
                            }
                        }
                        if (isset($post_arr['payment_method'])) {
                            $history .= " Payment Method: " . $post_arr['payment_method'];
                        }

                        $history = rtrim($history, ", ");

                        $this->configuration_model->insertConfigChangeHistory('profile updation', $history);
                        $history = "";
                    }
                    //
                    $data = [
                    'status'  => true,
                    'message' => lang('payment_details_update_success')
                    ];
                     $this->set_success_response(200, $data);
                } else {
                    $this->set_error_response(500);
                }  
        }
        else
        {
            $this->set_error_response(422, 1004);
        }
    }
    public function paymentDetails_put()
    {
        $user_id = $this->rest->user_id;      
        $post_arr = $this->put();
        $post_arr['paypal_account'] = isset($post_arr['paypalAccount']) ? $post_arr['paypalAccount'] : '';
        $post_arr['blockchain_account'] = isset($post_arr['blockchainAccount']) ? $post_arr['blockchainAccount'] : '';
        $post_arr['bitgo_account'] = isset($post_arr['bitgoAccount']) ? $post_arr['bitgoAccount'] : '';
        $post_arr['blocktrail_account'] = isset($post_arr['blocktrailAccount']) ? $post_arr['blocktrailAccount'] : '';
        $post_arr['payment_method'] = isset($post_arr['paymentMethod']) ? $post_arr['paymentMethod'] : '';        
        $this->form_validation->set_data($post_arr);
        $validated = $this->validatePaymentInfo();
        if($validated['status']) {
            $res = $this->profile_model->updatePaymentDetails($user_id, $post_arr);
            if ($res) {
                    //insert configuration_change_history
                    $settings = $this->configuration_model->getSettings();
                    if ($settings['profile_updation_history']) {
                        $history = "Updated Payment details as";
                        if (isset($post_arr['paypal_account'])) {
                            if ($post_arr['paypal_account'] == '') {
                                $history .= " Paypal Account: NA,";
                            } else {
                                $history .= " Paypal Account: " . $post_arr['paypal_account'] . ",";
                            }
                        }
                        if (isset($post_arr['blockchain_account'])) {
                            if ($post_arr['blockchain_account'] == '') {
                                $history .= " Blockchain Address: NA,";
                            } else {
                                $history .= " Blockchain Address: " . $post_arr['blockchain_account'] . ", ";
                            }
                        }
                        if (isset($post_arr['bitgo_account'])) {
                            if ($post_arr['bitgo_account'] == '') {
                                $history .= " BitGo Address: NA,";
                            } else {
                                $history .= " BitGo Address: " . $post_arr['bitgo_account'] . ",";
                            }
                        }
                        if (isset($post_arr['blocktrail_account'])) {
                            if ($post_arr['blocktrail_account'] == '') {
                                $history .= " Blocktrail Address: NA";
                            } else {
                                $history .= " Blocktrail Address: " . $post_arr['blocktrail_account'];
                            }
                        }
                        if (isset($post_arr['payment_method'])) {
                            $history .= " Payment Method: " . $post_arr['payment_method'];
                        }

                        $history = rtrim($history, ", ");

                        $this->configuration_model->insertConfigChangeHistory('profile updation', $history);
                        $history = "";
                    }
                    //
                    $data = [
                    'status'  => true,
                    'message' => lang('payment_details_update_success')
                    ];
                     $this->set_success_response(200, $data);
                } else {
                    $this->set_error_response(500);
                }  
        }
        else
        {
            $this->set_error_response(422, 1004);
        }
    }
    // validation
    public function validatePaymentInfo()
    {
        $this->form_validation->set_rules('paypalAccount', lang('paypal_account'), 'trim|valid_email');
        $this->form_validation->set_rules('blockchainAccount', lang('blockchain_account'), 'trim|alpha_numeric');
        $this->form_validation->set_rules('bitgoAccount', lang('bitgo_account'), 'trim|alpha_numeric');
        $this->form_validation->set_rules('blocktrailAccount', lang('blocktrail_account'), 'trim|alpha_numeric');
        $this->form_validation->set_rules('paymentMethod', lang('payment_method'), 'trim|required');
        if (!$this->form_validation->run()) {
            return [
                'status' => false,
                'error_type' => 'validation_error',
                'validation_error' => $this->form_validation->error_array(),
            ];
        }
        return [
            'status' => true
        ];
    }
    // end of validation
    // end of Payment Details 


    // settingstDetails
    public function settingstDetails_put()
    {
        $user_id = $this->rest->user_id;      
        $post_arr = $this->put();
        $post_arr['paypal_account'] = isset($post_arr['paypalAccount']) ? $post_arr['paypalAccount'] : ''; 
        $this->form_validation->set_data($post_arr);
        $validated = $this->validateSettingsInfo();
        if($validated['status']) {
            if($this->MODULE_STATUS['lang_status'] == 'yes' && !$this->IS_MOBILE){
                $this->load->model('multi_language_model');
                $lang_id = $this->multi_language_model->languageCodetiId($post_arr['language']);
                $res1 = $this->multi_language_model->setUserDefaultLanguage($lang_id, $user_id);
            }
            if($this->MODULE_STATUS['multy_currency_status'] == 'yes' && !$this->IS_MOBILE){

                $res2 = $this->currency_model->updateUserCurrency($post_arr['currency'], $user_id);
            
            }
            if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') 
            {
                $this->load->model('tree_model');
                $res3 = $this->tree_model->updateLeg($user_id, $post_arr['binaryLegSettings']);

            }
            if($this->MODULE_STATUS['google_auth_status'] == 'yes')
            {
                $res4 = $this->profile_model->updateauthentication($user_id, $post_arr['googleAuthStatus']);
            }
            $data = [
                'status'  => true,
                'message' => lang('settings_details_update_success')
            ];
            $this->set_success_response(200, $data);
        }
        else
        {
            $this->set_error_response(422, 1004);
        }
       
    }
    // validation
    public function validateSettingsInfo()
    {
        if($this->MODULE_STATUS['lang_status'] == 'yes' && !$this->IS_MOBILE){
            $this->form_validation->set_rules('language', lang('language'), 'trim|required');
        }
        if($this->MODULE_STATUS['multy_currency_status'] == 'yes' && !$this->IS_MOBILE){
            $this->form_validation->set_rules('currency', lang('currency'), 'trim|required');
        }
        if($this->MODULE_STATUS['mlm_plan'] == 'Binary'){
            $this->form_validation->set_rules('binaryLegSettings', lang('binary_leg_settings'), 'trim|required');
        }
        // $this->form_validation->set_rules('googleAuthStatus', lang('google_auth_status'), 'trim|required');
        if (!$this->form_validation->run()) {
            return [
                'status' => false,
                'error_type' => 'validation_error',
                'validation_error' => $this->form_validation->error_array(),
            ];
        }
        return [
            'status' => true
        ];
    }
    
    public function forget_transaction_password_get() {
        $this->captcha_model->CreateImageApi($this->rest->user_id, 'user');
    }
    
    public function forget_transaction_password_post() {
        $post_arr = $this->validation_model->stripTagsPostArray($this->post());
        $this->form_validation->set_data($post_arr);
        $this->form_validation->set_rules('captcha', 'captcha', 'required|callback_captcha_exists');
        if(!$this->form_validation->run()) {
            return $this->set_error_response(422, 1004);
        }
        
        $e_mail = $this->validation_model->getUserEmailId($this->rest->user_id);
        $check_result = $this->validation_model->checkEmail($this->rest->user_id, $e_mail);
        if (!$check_result) {
            $this->set_error_response(500);
        }
        $this->load->model('tran_pass_model');
        $this->tran_pass_model->sendEmail($this->rest->user_id, $e_mail);
        $msg = $this->lang->line('your_request_has_been_accepted_we_will_send_you_confirmation_mail_please_follow_that_instruction');
        $data = 
        $this->set_success_response(200, ['message' => lang('your_request_has_been_accepted_we_will_send_you_confirmation_mail_please_follow_that_instruction')]);
    }
    
    public function captcha_exists($captcha) {
        if (!$this->captcha_model->userCaptchaExists($this->rest->user_id, $captcha)) {
            $this->form_validation->set_message('captcha', lang('invalid_captcha'));
            return false;
        } else {
            return true;
        }
    }
    
    // end of validation
    // end of settingstDetails
    //change password
    public function password_put()
    {
        if(DEMO_STATUS == 'yes'){
            $this->set_error_response(400,1070); 
        }
        $user_id = $this->rest->user_id;      
        $post_arr = $this->validation_model->stripTagsPostArray($this->put());
        $this->form_validation->set_data($post_arr);
        if ($this->validatePasswordInfo()) {
            $dbpassword  = $this->password_model->selectPassword($user_id);
            $current_pwd = $post_arr['current_password'];
            if (!password_verify($current_pwd, $dbpassword)) {
                $this->set_error_response(401, 1021);
            }
            $new_pwd_user = $post_arr['new_password'];
            $new_pwd_user_md5   = password_hash($new_pwd_user, PASSWORD_DEFAULT);
            $res = $this->password_model->updatePassword($new_pwd_user_md5, $user_id);
            if ($res) {
                $data = ['message' => 'SuccessfullyUpdated'];
                $this->set_success_response(200,$data);
            } else {
                $this->set_error_response(500); 
            }
        } else {
            $this->set_error_response(422, 1004);
        }

    }
    
    public function transaction_password_put()
    {
        $user_id = $this->rest->user_id;      
        $post_arr = $this->validation_model->stripTagsPostArray($this->put());
        $this->form_validation->set_data($post_arr);
        if ($this->validatePasswordInfo()) {
            $dbpassword  = $this->password_model->selectTransactionPassword($user_id);
            $current_pwd = $post_arr['current_password'];
            if (!password_verify($current_pwd, $dbpassword)) {
                $this->set_error_response(401, 1021);
            }
            $new_pwd_user = $post_arr['new_password'];
            $new_pwd_user_md5   = password_hash($new_pwd_user, PASSWORD_DEFAULT);
            $res = $this->password_model->updateTransactionPassword($new_pwd_user_md5, $user_id);
            if ($res) {
                $data = ['message' => 'SuccessfullyUpdated'];
                $this->set_success_response(200,$data);
            } else {
                $this->set_error_response(500); 
            }
        } else {
            $this->set_error_response(422, 1004);
        }

    }
    
    public function change_password_post() {
        $user_id = $this->rest->user_id;      
        $post_arr = $this->validation_model->stripTagsPostArray($this->post());
        $this->form_validation->set_data($post_arr);
        if ($this->validatePasswordInfo()) {
            $dbpassword  = $this->password_model->selectPassword($user_id);
            //dd($post_arr);
            $current_pwd = $post_arr['current_password'];
            if (!password_verify($current_pwd, $dbpassword)) {
                $this->set_error_response(401, 1021);
            }
            $new_pwd_user = $post_arr['new_password'];
            $new_pwd_user_md5   = password_hash($new_pwd_user, PASSWORD_DEFAULT);
            $res = $this->password_model->updatePassword($new_pwd_user_md5, $user_id);
            if ($res) {
                $data = ['message' => 'SuccessfullyUpdated'];
                $this->set_success_response(200,$data);
            } else {
                $this->set_error_response(500); 
            }
        } else {
            $this->set_error_response(422, 1004);
        }

    }


    public function validatePasswordInfo()
    {
        $this->form_validation->set_rules('current_password', lang('current_password'), 'trim|required|min_length[6]|max_length[32]|callback__alpha_password');
        $this->form_validation->set_rules('new_password', lang('new_password'), 'trim|required|max_length[32]|min_length[6]|callback__alpha_password');
        $this->form_validation->set_rules('password_confirmation', lang('password_confirmation'), 'trim|required|min_length[6]|max_length[32]|callback__alpha_password|matches[new_password]');
        return $this->form_validation->run();
    }

    function _alpha_password($str_in = '')
    {
        if (!preg_match("/^[0-9a-zA-Z\s\r\n@!#\$\^%&*()+=\-\[\]\\\';,\.\/\{\}\|\":<>\?\_\`\~]+$/i", $str_in)) {
            $this->form_validation->set_message('_alpha_password', lang('password_characters_allowed'));
            return false;
        } else {
            return true;
        }
    }

    //key list with category
    function kyc_uploads_get(){
        $user_id    = $this->LOG_USER_ID;
        $this->load->model('configuration_model');
        $this->load->model('profile_model');
        $kyc_catg   = $this->configuration_model->getKycDocCategory();
        $data = [];
        $data['category'] = $kyc_catg;
        $id_proof   = $this->profile_model->getMyKycDoc($user_id);
        foreach ($id_proof as $key => $value) {
            foreach ($value['file_name'] as  $file_index =>$file_name) {
                $id_proof[$key]['file_name'][$file_index] = SITE_URL.'/uploads/images/document/kyc/'.$file_name;
            }
            unset($id_proof[$key]['user_id']);
        }
        $data['id'] = $id_proof;
        $this->set_success_response(200,$data);
    }


    //kyc uploads
    function kyc_file_uploads_post(){
        $user_id    = $this->LOG_USER_ID;
        $user_name = $this->validation_model->IdToUserName($this->LOG_USER_ID);
        //chekc the value
        if($this->post()){
            //category
            $catg       = $this->post('category');
            //file count
            if($this->IS_MOBILE) {
                $file_count = $this->post('count');
            }else{
                $file_count = count($_FILES['id_proof']['tmp_name']);
            }
            $exist = $this->profile_model->checkKycDocs($user_id, $catg);
            //check if proof is exists
            if ($exist) {
                $this->set_error_response(422,1041);
            }

            //validation check the category
            if($catg==""){
                $this->set_error_response(422,1003);
            }

            //
            if ($file_count > 0) {
                $upload_config = $this->validation_model->getUploadConfig();
                $upload_count  = $this->validation_model->getUploadCount($this->LOG_USER_ID);
                if ($upload_count >= $upload_config) {
                    $this->set_error_response(422,1038);
                }
            } else {
                $this->set_error_response(422,1003);
            }

            $success_count  = 0;
            $insert_array   = [];
            $upload_path    = IMG_DIR . "/document/kyc/";
            $config = array(
                'upload_path'   => "$upload_path",
                'allowed_types' => 'pdf|jpeg|jpg|png',
                'max_size'      => '5120000',
            );
            $this->load->library('upload', $config);
            $files = $_FILES;
            for ($i = 0; $i < $file_count; $i++) {
                if ($this->IS_MOBILE) {
                    $_FILES['id_proof']['name']     = $files['id_proof_'.$i]['name'];
                    $_FILES['id_proof']['type']     = $files['id_proof_'.$i]['type'];
                    $_FILES['id_proof']['tmp_name'] = $files['id_proof_'.$i]['tmp_name'];
                    $_FILES['id_proof']['error']    = $files['id_proof_'.$i]['error'];
                    $_FILES['id_proof']['size']     = $files['id_proof_'.$i]['size'];
                }else{  
                    $_FILES['id_proof']['name']     = $files['id_proof']['name'][$i];
                    $_FILES['id_proof']['type']     = $files['id_proof']['type'][$i];
                    $_FILES['id_proof']['tmp_name'] = $files['id_proof']['tmp_name'][$i];
                    $_FILES['id_proof']['error']    = $files['id_proof']['error'][$i];
                    $_FILES['id_proof']['size']     = $files['id_proof']['size'][$i];
                }

                $ext        = pathinfo($_FILES['id_proof']['name'], PATHINFO_EXTENSION);
                $config = array(
                    'upload_path' => "$upload_path",
                    'allowed_types' => 'pdf|jpeg|jpg|png',
                    'max_size' => '5120000',
                    'file_name' => $user_name . "_" . time() . $i . '.' . $ext,
                );

                $this->upload->initialize($config);

                if ($this->upload->do_upload('id_proof')) {
                    $data           = array('upload_data' => $this->upload->data());
                    $insert_array[] = $data['upload_data']['file_name'];
                    $success_count++;
                } else {
                    $error = $this->upload->display_errors();
                    $error = preg_replace('/<[^>]*>/', ' ', $error);
                }
            }
            if ($file_count != $success_count) {
                foreach ($insert_array as $value) {
                    if (file_exists($upload_path . $value)) {
                        unlink($upload_path . $value);
                    }
                }
                $this->set_error_response(422,1024);
            }
            if (count($insert_array)) {
                $this->profile_model->InsertIdentityProof($insert_array, $user_id, $catg);
            }
            $this->set_success_response(200);
        }else{
            $this->set_error_response(422,1003);
        }
    }

    //delete kyc file
    function remove_kyc_post(){
        if ($this->post()) {
            $user_id    = $this->LOG_USER_ID;
            $id     = $this->post('id');
            $result = $this->profile_model->deletetKyc($id, $user_id);
            if ($result) {
                $this->set_success_response(200);
            }
            $this->set_error_response(422,1003);
        }else{
            $this->set_error_response(422,1003);
        }

    }
    
    public function upgrade_package_get() {
        $this->load->model('upgrade_model');
        
        if($this->MODULE_STATUS['opencart_status'] == 'yes'){
         
          $current_package_details = $this->upgrade_model->getMembershipOpencartPackageDetails($this->LOG_USER_ID);
          $upgradable_package_list = $this->upgrade_model->getOpencartUpgradablePackageList($current_package_details);
        } else {
          $current_package_details = $this->upgrade_model->getMembershipPackageDetails($this->LOG_USER_ID);
          $upgradable_package_list = $this->upgrade_model->getUpgradablePackageList($current_package_details);
        }
        
        $this->set_success_response(200, [
            'current_package_details' => [
                'procuct_id'   => $current_package_details['product_id'],
                'product_name' => $current_package_details['product_name'],
                'price'        => $current_package_details['price'],
                'package_id'   => $current_package_details['package_id'],
                'pv' => $current_package_details['pair_value'],
            ],
            'upgrade_list'     => $upgradable_package_list
        ]);
    }
}

        
