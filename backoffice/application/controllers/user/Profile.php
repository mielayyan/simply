<?php

require_once 'Inf_Controller.php';

class Profile extends Inf_Controller
{

    function __construct()
    {
        parent::__construct();
    }
    //get user photo
    public function getUserPhoto()
    {
   $response = array();
   $res = $this->profile_model->getUserPhoto($this->LOG_USER_ID);
   if($res)
   {
    $response['success'] = true;
    $response['photo'] =SITE_URL.'/uploads/images/profile_picture/'.$res;
    $response['background'] ='../../../uploads/images/profile_picture/'.$res;
   }
   echo json_encode($response);
   exit();
    }
    //update user profile using filepond
    public function user_profile_upload() {
        $user_id = $this->LOG_USER_ID;
        $random_number = floor($user_id * rand(1000, 9999));
         $config['file_name'] = "pro_" . $random_number;
        $config['upload_path'] = IMG_DIR . 'profile_picture/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = '2048';
        $config['remove_spaces'] = true;
        $config['overwrite'] = false;
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('file')) {
            $msg = $this->upload->display_errors();
            http_response_code(400);
            echo json_encode([
                'status' => false,
                'message' => $this->upload->display_errors(),
            ]);exit;
        } else {
            $image_arr = array('upload_data' => $this->upload->data());
            $new_file_name = $image_arr['upload_data']['file_name'];
            $image = $image_arr['upload_data'];
            if ($image['file_name']) {
                $data['photo'] = '../uploads/images/profile_picture/' . $image['file_name'];
                $data['raw'] = $image['raw_name'];
                $data['ext'] = $image['file_ext'];
            }
            $res1 = $this->profile_model->changeProfilePicture($user_id, $new_file_name);
             if($res1) {
                    //insert configuration_change_history
                    $settings = $this->configuration_model->getSettings();
                    if ($settings['profile_updation_history']) {
                        $history = "Updated Profile Picture to :" . $new_file_name;
                            $this->configuration_model->insertConfigChangeHistory('profile updation', $history);
                            $history = "";
                        }
                    }              
                http_response_code(200);
                echo json_encode([
                    'status' => true,
                    'message' => lang('profile updated successfully'),
                ]);die;    
            }
    }

    
    function profile_view($value='')
    {

        $this->HEADER_LANG['page_top_header'] = lang('profile');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('profile');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();
        $this->lang->load('validation',$this->LANG_NAME);
        $this->lang->load('tran_pass',$this->LANG_NAME);
        $this->lang->load('password',$this->LANG_NAME);
        $help_link = 'profile-management';
        $this->set('help_link', $help_link);

        
        
        if($value !=''){
            $user_id = $value;
            $u_name = $this->validation_model->IdToUserName($user_id);
        }else{
            $user_id = $this->LOG_USER_ID;
            $u_name = $this->LOG_USER_NAME;
        }
        $this->lang->load('home', $this->LANG_NAME);
        $title = lang('profile');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $this->load->model('configuration_model');
        $this->load->model('payout_model');
        $bank_info_status = $this->configuration_model->getBankInfoStatus();
        $age_limit = $this->configuration_model->getAgeLimitSetting();

        $current_update_section = '';
        $error_flag = '';
        $product_validity = '';
        $store_id = '';
        if($this->MODULE_STATUS['opencart_status'] == "yes")
        {
            if (DEMO_STATUS == 'yes'){
            $store_id = "&id=".str_replace("_","",$this->db->dbprefix);
            }
        }
        $this->set('store_id', $store_id);

        $product_status = $this->MODULE_STATUS['product_status'];
        $pin_status = $this->MODULE_STATUS['pin_status'];

        $profile_arr = $this->profile_model->getProfileDetails($user_id, $product_status);
        $profile_arr['details']['year'] = date("Y", strtotime($profile_arr["details"]["dob"]));
        $profile_arr['details']['month'] = date("m", strtotime($profile_arr["details"]["dob"]));
        $profile_arr['details']['day'] = date("d", strtotime($profile_arr["details"]["dob"]));


        // defualt currency value
        if($this->MODULE_STATUS['multy_currency_status'] == 'yes') {
            if ($profile_arr['details']['default_currency']=='NA') {
                $this->load->model('currency_model');
                $default_admin_currency = $this->currency_model->getProjectDefaultCurrencyDetails();
                $defualt_currency = $this->currency_model->getUserDefaultCurrencyDetails($user_id);
                $profile_arr['details']['default_currency'] = $default_admin_currency['id'];
            }
        }
        // end defult currency  value


        $profile_details = $profile_arr['details'];

        // dd($profile_details);
        $country_id = $profile_details['country_id'];
        $state_id = $profile_details['state_id'];
        $countries = $this->country_state_model->viewCountry($country_id);
        $country_name=$this->country_state_model->getCountryNameFromId($country_id);
        $state_name=$this->country_state_model->getStateNameFromId($state_id);
        
        $states = $this->country_state_model->viewState($country_id, $state_id);
        if ($profile_details['state'] == 'NA') {
            $states = "<option value='0' selected>" . lang('no_state_selected') . "</option>";
        }

        if ($country_id != '') {
            $mob_code = $this->country_state_model->getCountryTelephoneCode($country_id);
            $mobile_code = "+" . $mob_code;
        } else {
            $mobile_code = "";
        }

        $product_name = '';
        if ($product_status == 'yes') {
            $product_name = $profile_arr['product_name'];
            $product_validity = $profile_arr['product_validity'];
        }
        // kyc status

        if($this->MODULE_STATUS['kyc_status'])
        {
            $kyc_status = $this->validation_model->checkKycUpload($user_id);
        }
        $this->set('kyc_status', $kyc_status);

        // end of kyc


        // rank 
        $rank_criteria = null;
        if ($this->MODULE_STATUS['rank_status'] == 'yes') {
            $this->load->model('rank_model');
            $crank = $this->rank_model->currentRankName($this->LOG_USER_ID);
            if(!empty($crank))  {
                $current_rank = $this->rank_model->getCurrentRankData($this->LOG_USER_ID);
                $rank_criteria = array_keys($current_rank['criteria'], 1);
            } else {
                $current_rank = null;
                $rank_criteria = null;
            }
        }

        $this->set('passwordPolicyJson', json_encode($this->validation_model->getPasswordPolicyArray()));
        $this->set("rank_criteria", $rank_criteria);
        // end of rank 


        // rank configuration
            $rank_configuration = $this->configuration_model->getRankConfiguration();
            $this->set("rank_configuration", $rank_configuration);
        // end of rank configuration


        //package upgarde
            if($this->MODULE_STATUS['package_upgrade'] == "yes" && $this->MODULE_STATUS['opencart_status'] == "no") {
                $this->load->model('upgrade_model');
                $current_package_details = $this->upgrade_model->getMembershipPackageDetails($this->LOG_USER_ID);
                $upgradable_package_list = $this->upgrade_model->getUpgradablePackageList($current_package_details);
                $this->set('upgradable_package_list', $upgradable_package_list);
            }
        // end of package upgrade

        //binary model 
        if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
        $binary_tree = $this->home_model->individulaDetails($this->LOG_USER_ID, ['total_left_carry','total_right_carry']);
        $binary_tree_carry = [
                'total_left_carry' => $binary_tree->total_left_carry == 0 ? 0 : $binary_tree->total_left_carry,
                'total_right_carry' => $binary_tree->total_right_carry == 0 ? 0 : $binary_tree->total_right_carry,
            ];
        } else {
            $binary_tree_carry = [
                'total_left_carry' => 0,
                'total_right_carry' => 0,
            ];
        }
        //end binary model 


        //leg_settings 
        if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
        $get_leg_type = $this->tree_model->get_leg_type($user_id);
        $get_leg_settings = $this->tree_model->get_bnary_leg_setng();
        $this->set('get_leg_type', $get_leg_type);
        $this->set('get_leg_settings', $get_leg_settings);
        }
        //end os leg settings
 
        
        // 
        $individula_details = $this->home_model->individulaDetails($this->LOG_USER_ID, ['personal_pv', 'gpv']);
            $profile_extra_data = [
                'personal_pv' => $individula_details->personal_pv == 0 ? 0 : $individula_details->personal_pv,
                'group_pv' => $individula_details->gpv == 0 ? 0 : $individula_details->gpv,
            ];
        $this->set('profile_extra_data', $profile_extra_data);


        $payment_gateway = $this->profile_model->getActivePaymentGateway();
        $payment_method  = $this->payout_model->gatewayList();
        $dynamic_fields = $this->register_model->getContactInfoFields();
        $custom_details = $this->configuration_model->getCustomFields($user_id);
        // dd($custom_details);
        $rank_color = "#7d899b";
        $user_rank_id = $this->validation_model->getUserRank($this->LOG_USER_ID);
        if($user_rank_id)
        {
            $rank_details = $this->configuration_model->getActiveRankDetails($user_rank_id);
            if ($rank_details) {
                $rank_color =$rank_details[0]['rank_color'];
            }
        }
        $this->set("rank_color",$rank_color);
        $this->set("binary_tree_carry", $binary_tree_carry);
        $this->set('custom_details', $custom_details);
        $this->set('gateway_list', $payment_method);
        $this->set('payment_gateway', $payment_gateway);
        $this->set('bank_info_status', $bank_info_status);
        $this->set('age_limit', $age_limit);
        $full_name = $profile_details["name"]." ".$profile_details["user_detail_second_name"];
        $this->set('full_name', $full_name);
        $this->set('u_name', $u_name);
        $this->set('date', $u_name);
        $this->set('countries', $countries);
        $this->set('country_name', $country_name);
        $this->set('state_name', $state_name);
        $this->set('states', $states);
        $this->set('mobile_code', $mobile_code);
        $this->set('product_name', $this->security->xss_clean($product_name));
        $this->set('product_status', $product_status);
        $this->set('product_validity', $product_validity);
        $this->set('pin_status', $pin_status);
        $this->set('profile_details', $this->security->xss_clean($profile_details));
        $this->set('current_update_section', $current_update_section);
        $this->set('error_flag', $error_flag);
        $this->set('dynamic_fields', $dynamic_fields);
        $this->set('user_country_id', $country_id);
        $this->set('user_state_id', $state_id);
        $this->setView();
    }

    function get_states($country_id)
    {

        $state_string = $this->country_state_model->viewState($country_id);
        $state = '<select name="state" id="state" tabindex="4" class="form-control">';
        if ($state_string != '') {
            $state .= "<option value='0'>" . lang('select_state_menu') . "</option>" . $state_string;
        } else {
            $state .= "<option value='0'>" . lang('no_data_available') . "</option>";
        }
        $state .= "</select>";
        echo $state;
        exit();
    }

    function business_volume()
    {

        $title = lang('business_volume');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $this->HEADER_LANG['page_top_header'] = lang('business_volume');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('business_volume');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $help_link = 'business-volume';
        $this->set('help_link', $help_link);

        $user_id = $this->LOG_USER_ID;

        $base_url = base_url() . 'user/profile/business_volume';
        $config = $this->pagination->customize_style();
        $config['base_url'] = $base_url;
        $config['per_page'] = $this->PAGINATION_PER_PAGE;
        if ($this->uri->segment(4) != "")
            $page = $this->uri->segment(4);
        else
            $page = 0;

        $config['total_rows'] = $this->profile_model->getTotalBusinessVolumeCount($user_id);

        $volume_details = $this->profile_model->getBusinessVolumeDetails($config['per_page'], $page, $user_id);

        $this->pagination->initialize($config);
        

        $this->set('details', $volume_details);
        
        $this->set('page_id', $page);
        $this->set('user_id', $user_id);

        $this->setView();
    }

    public function update_contact_info()
    {
        if ($this->input->is_ajax_request()) {
            $response = array();
            $response['error'] = false;
            $post_arr = $this->input->post(null, true);
            $user_id = $this->LOG_USER_ID;
             $kyc_status = $this->validation_model->checkKycUpload($user_id);
            if (isset($post_arr['country'])&&$post_arr['country'] != '0') {
                $countries = $this->country_state_model->getCountries($post_arr['country']);
            }
            if (isset($post_arr['state'])&&$post_arr['state'] != '0') {
                $states = $this->country_state_model->getStates($post_arr['country'], $post_arr['state']);
            }
            
            if ($this->validate_contact_info($kyc_status)) {
                $opencart_status = $this->MODULE_STATUS['opencart_status'] == "yes" && $this->MODULE_STATUS['opencart_status_demo'] == "yes";
                $res = $this->profile_model->updateContactInfo($user_id, $post_arr, $opencart_status,$kyc_status);
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
                    //
                    $response['success'] = true;
                    $response['message'] = lang('contact_info_update_success');
                } else {
                    $response['error'] = true;
                    $response['message'] = lang('contact_info_update_error');
                }
            } else {
                $response['error'] = true;
                $response['message'] = lang('errors_check');
                foreach ($post_arr as $key => $value) {
                    $response['form_error'][$key] = form_error($key);
                }
            }
            echo json_encode($response);
            exit();
        }
    }

    public function validate_contact_info($kyc_status) {
        $this->lang->load('validation', $this->LANG_NAME);    
        $this->lang->load('register', $this->LANG_NAME);    
        $this->form_validation->set_rules('address', lang('adress_line1'), 'trim|callback_check_required[adress_line1]|max_length[1000]', [
                'max_length' => sprintf(lang('maxlength'), lang('address_line1'), "1000")
            ]
        );
        $this->form_validation->set_rules('address2', lang('adress_line2'), 'trim|callback_check_required[adress_line2]|max_length[1000]', [
            'max_length' => sprintf(lang('maxlength'), lang('address_line2'), "1000")
        ]);

        $this->form_validation->set_rules('pincode', lang('pin'), 'trim|max_length[50]|callback_check_required[pin]', [
                'max_length' => sprintf(lang('maxlangth'), lang('pincode'), "50"),
        ]);
        if($kyc_status=='no'){
            $this->form_validation->set_rules('country', lang('country'), 'trim|callback_check_required[country]');
            $this->form_validation->set_rules('state', lang('state'), 'trim|callback_check_required[state]');
        }
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

    public function update_bank_info()
    {
        if ($this->input->is_ajax_request()) {
            $response = array();
            $response['error'] = false;
            $post_arr = $this->input->post(null, true);
            $user_id = $this->LOG_USER_ID;
            if ($this->validate_bank_info()) {
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
                    //
                    $response['success'] = true;
                    $response['message'] = lang('bank_info_update_success');
                } else {
                    $response['error'] = true;
                    $response['message'] = lang('bank_info_update_error');
                }
            } else {
                $response['error'] = true;
                $response['message'] = lang('errors_check');
                foreach ($post_arr as $key => $value) {
                    $response['form_error'][$key] = form_error($key);
                }
            }
            echo json_encode($response);
            exit();
        }
    }

    public function validate_bank_info()
    {
        $this->form_validation->set_rules('bank_name', lang('bank_name'), 'trim|min_length[3]|max_length[32]|callback__alpha_space');
        $this->form_validation->set_rules('branch_name', lang('branch_name'), 'trim|min_length[3]|max_length[32]|callback__alpha_space');
        $this->form_validation->set_rules('account_holder', lang('acct_holder_name'), 'trim|min_length[3]|max_length[32]|callback__alpha_space');
        $this->form_validation->set_rules('account_no', lang('account_no'), 'trim|min_length[3]|max_length[32]|alpha_numeric');
        $this->form_validation->set_rules('ifsc', lang('ifsc'), 'trim|min_length[3]|max_length[32]|alpha_numeric');
        $this->form_validation->set_rules('pan', lang('pan_no'), 'trim|min_length[3]|max_length[32]|alpha_numeric');

        $validation_status = $this->form_validation->run();
        return $validation_status;
    }

    public function update_social_info()
    {
        if ($this->input->is_ajax_request()) {
            $response = array();
            $response['error'] = false;
            $post_arr = $this->input->post(null, true);
            $user_id = $this->LOG_USER_ID;
            if ($this->validate_social_profile()) {
                $res = $this->profile_model->updateSocialProfile($user_id, $post_arr);
                if ($res) {
                    //insert configuration_change_history
                    $settings = $this->configuration_model->getSettings();
                    if ($settings['profile_updation_history']) {
                        $history = "Updated Social Info as Facebook :" . $post_arr['facebook'] . ", ";
                        $history .= "Twitter :" . $post_arr['twitter'];

                        $this->configuration_model->insertConfigChangeHistory('profile updation', $history);
                        $history = "";
                    }
                    //
                    $response['success'] = true;
                    $response['message'] = lang('social_profile_update_success');
                } else {
                    $response['error'] = true;
                    $response['message'] = lang('social_profile_update_error');
                }
            } else {
                $response['error'] = true;
                $response['message'] = lang('errors_check');
                foreach ($post_arr as $key => $value) {
                    $response['form_error'][$key] = form_error($key);
                }
            }
            echo json_encode($response);
            exit();
        }
    }

    public function validate_social_profile()
    {
        $this->form_validation->set_rules('facebook', lang('facebook'), 'trim|valid_url');
        $this->form_validation->set_rules('twitter', lang('twitter'), 'trim|valid_url');

        $validation_status = $this->form_validation->run();
        return $validation_status;
    }

    public function _alpha_space($str = '')
    {
        if (!$str) {
            return true;
        }
        $res = (bool)preg_match('/^[A-Z ]*$/i', $str);
        if (!$res) {
            $this->form_validation->set_message('_alpha_space', lang('only_alpha_space'));
        }
        return $res;
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
// update settings(language,currency,leg locking,google authentication)
    public function update_default_settings()
    {
        if ($this->input->is_ajax_request()) {
            $response = array();
            $response['error'] = false;
            $post_arr = $this->input->post(null, true);
            $user_id = $this->LOG_USER_ID;
            if (!$user_id) {
                $response['error'] = true;
                $response['message'] = lang('invalid_user_name');
                echo json_encode($response);
                exit();
            }
            if($this->MODULE_STATUS['lang_status'] == 'yes'){
            $this->load->model('multi_language_model');
            $res1 = $this->multi_language_model->setUserDefaultLanguage($post_arr['language'], $user_id);
            }
            if($this->MODULE_STATUS['multy_currency_status'] == 'yes'){

            $res2 = $this->currency_model->updateUserCurrency($post_arr['currency'], $user_id);
            
            }
            if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') 
            {

            $res3 = $this->tree_model->updateLeg($user_id, $post_arr['binary_leg']);

            }
            if($this->MODULE_STATUS['google_auth_status'] == 'yes')
            {

            $res4 = $this->profile_model->updateauthentication($user_id, $post_arr['google_auth_status']);
            }
            $response['success'] = true;
            $response['message'] = lang('settings_details_update_success');
            

            echo json_encode($response);
            exit();
        
    }
}



    /*Ajax Function For Payment Details Updation Begins*/
    public function update_payment_details()
    {
        if ($this->input->is_ajax_request()) {
            $response = array();
            $response['error'] = false;
            $post_arr = $this->input->post(null, true);
            $user_id = $this->LOG_USER_ID;
            if (!$user_id) {
                $response['error'] = true;
                $response['message'] = lang('invalid_user_name');
                echo json_encode($response);
                exit();
            }
            if ($this->validate_payment_details()) {
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
                    $response['success'] = true;
                    $response['message'] = lang('payment_details_update_success');
                } else {
                    $response['error'] = true;
                    $response['message'] = lang('payment_details_update_error');
                }
            } else {
                $response['error'] = true;
                $response['message'] = lang('errors_check');
                foreach ($post_arr as $key => $value) {
                    $response['form_error'][$key] = form_error($key);
                }
            }
            echo json_encode($response);
            exit();
        }
    }

    public function validate_payment_details()
    {
        $this->form_validation->set_rules('paypal_account', lang('paypal_account'), 'trim|valid_email');
        $this->form_validation->set_rules('blockchain_account', lang('blockchain_account'), 'trim|alpha_numeric');
        $this->form_validation->set_rules('bitgo_account', lang('bitgo_account'), 'trim|alpha_numeric');
        $this->form_validation->set_rules('payment_method', lang('payment_method'), 'trim|required');

        $validation_status = $this->form_validation->run();
        return $validation_status;
    }

    /*Ajax Function For Payment Details Updation Ends*/

    function validate_username($ref_user = '')
    {
        if ($ref_user != '') {
            $flag = false;
            if ($this->profile_model->isUserNameAvailable($ref_user)) {
                $flag = TRUE;
            }
            return $flag;
        } else {
            $echo = 'no';
            $username = ($this->input->post('username', TRUE));

            if ($this->profile_model->isUserNameAvailable($username)) {
                $echo = "yes";
            }
            echo $echo;
            exit();
        }
    }

    public function update_default_language()
    {
        if ($this->MODULE_STATUS['lang_status'] == 'yes' && $this->input->is_ajax_request()) {
            $this->load->model('multi_language_model');
            $response = array();
            $response['error'] = false;
            $post_arr = $this->input->post(null, true);
            $user_id = $this->LOG_USER_ID;
            if (!$user_id) {
                $response['error'] = true;
                $response['message'] = lang('invalid_user_name');
                echo json_encode($response);
                exit();
            }
            if ($this->validate_language_details()) {
                $res = $this->multi_language_model->setUserDefaultLanguage($post_arr['language'], $user_id);
                if ($res) {
                    /* $language_name = $this->inf_model->getLanguageName($post_arr['language']);
                    //insert configuration_change_history
                    $settings = $this->configuration_model->getSettings();
                    if ($settings['profile_updation_history']) {
                        $history = "Updated preferred language as {$language_name}";
                        $this->configuration_model->insertConfigChangeHistory('profile updation', $history);
                    }
                    // */
                    $response['success'] = true;
                    $response['message'] = lang('language_details_update_success');
                } else {
                    $response['error'] = true;
                    $response['message'] = lang('language_details_update_error');
                }
            } else {
                $response['error'] = true;
                $response['message'] = lang('errors_check');
                foreach ($post_arr as $key => $value) {
                    $response['form_error'][$key] = form_error($key);
                }
            }
            echo json_encode($response);
            exit();
        }
    }

    public function validate_language_details()
    {
        $this->form_validation->set_rules('language', lang('language'), 'trim|required');

        $validation_status = $this->form_validation->run();
        return $validation_status;
    }

    public function update_personal_info()
    {
        if ($this->input->is_ajax_request()) {
            $response = array();
            $response['error'] = false;
            $post_arr = $this->input->post(null, true);
            // dd($post_arr['dob']);
            $user_id = $this->LOG_USER_ID;
             $kyc_status = $this->validation_model->checkKycUpload($user_id);
            if(isset($post_arr['dob'])&&$post_arr['dob']<=date('y-m-d') || $kyc_status=='yes')
            {
            
            if ($this->validate_personal_info($kyc_status)) {
                $opencart_status = $this->MODULE_STATUS['opencart_status'] == "yes" && $this->MODULE_STATUS['opencart_status_demo'] == "yes";
                $res = $this->profile_model->updatePersonalInfo($user_id, $post_arr, $opencart_status,$kyc_status);
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
                    $response['success'] = true;
                    $response['message'] = lang('personal_info_update_success');
                } else {
                    $response['error'] = true;
                    $response['message'] = lang('personal_info_update_error');
                }
            } else {
                $response['error'] = true;
                $response['message'] = lang('errors_check');
                foreach ($post_arr as $key => $value) {
                    $response['form_error'][$key] = form_error($key);
                }
            }
            }
            else
            {
                $response['error'] = true;
                $response['message'] = lang('errors_check');
            }
            echo json_encode($response);
            exit();
        }
    }

    /**
     * [validate_personal_info description]
     * @return [type] [description]
     */
    public function validate_personal_info($kyc_status) {
        $this->lang->load('validation', $this->LANG_NAME);
        $this->lang->load('register', $this->LANG_NAME);
        if($kyc_status=='no'){
        $this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required|max_length[250]', [
                'max_length' => sprintf(lang('maxlength'), lang('first_name'), "250")
            ],
        );

        $this->form_validation->set_rules('last_name', lang('last_name'), 'trim|max_length[250]|callback_check_required[last_name]', [
            'max_length' => sprintf(lang('maxlength'), lang('last_name'), "250"),
        ]);
        $this->form_validation->set_rules('dob', lang('date_of_birth'), 'trim|callback_check_required[date_of_birth]|callback_validate_age_year', [
                'validate_age_year' => sprintf(lang('valid_age'), $this->configuration_model->getAgeLimitSetting()),
            ]);
        }
        $this->form_validation->set_rules('gender', lang('gender'), 'trim|callback_check_required[gender]|in_list[M,F]', ['in_list' => lang('You_must_select_gender')]);

        

        return $this->form_validation->run();
    }

    public function check_required($field_value, $field_name) {
        if ($this->profile_model->getRequiredStatus($field_name) == 'yes') {
            if ($field_value == '') {
                $this->form_validation->set_message('check_required', sprintf(lang('the_n_field_is_required'), lang($field_name)));
                return false;
            } else {
                return true;
            }
        } 
        return true;
    }

    public function validate_age_year($dob)
    {
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


    public function upload_kyc()
    {

        $title          = lang('kyc_details');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('kyc_details');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('kyc_details');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $this->load->model('configuration_model');
        $this->url_permission('kyc_status');

        $user_id    = $this->LOG_USER_ID;
        $user_name  = $this->LOG_USER_NAME;
        $id_proof   = $this->profile_model->getMyKycDoc($user_id);
        $kyc_catg   = $this->configuration_model->getKycDocCategory();

        if ($this->input->post('upload_kyc')) {
            $catg       = $this->input->post('category');
            $file_count = count($_FILES['id_proof']['tmp_name']);
            $exist      = $this->profile_model->checkKycDocs($user_id, $catg);
            if($catg=="")
            {
                $msg = lang('please select category');
                    $this->redirect($msg, "profile/kyc_upload", false);
            }

            if ($exist) {
                $msg = lang('id_already_exist');
                $this->redirect($msg, "profile/kyc_upload", false);
            }

            if ($file_count > 0) {
                $upload_config = $this->validation_model->getUploadConfig();
                $upload_count  = $this->validation_model->getUploadCount($this->LOG_USER_ID);
                if ($upload_count >= $upload_config) {
                    $msg = lang('you_have_reached_max_upload_limit');
                    $this->redirect($msg, "profile/kyc_upload", false);
                }
            } else {
                $msg = lang('select_kyc');
                $this->redirect($msg, "profile/kyc_upload", FALSE);
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

                $_FILES['id_proof']['name']     = $files['id_proof']['name'][$i];
                $_FILES['id_proof']['type']     = $files['id_proof']['type'][$i];
                $_FILES['id_proof']['tmp_name'] = $files['id_proof']['tmp_name'][$i];
                $_FILES['id_proof']['error']    = $files['id_proof']['error'][$i];
                $_FILES['id_proof']['size']     = $files['id_proof']['size'][$i];

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
                $msg = lang('error_on_upload_id') . ". $error";
                $this->redirect($msg, "profile/kyc_upload", FALSE);
            }
            if (count($insert_array)) {
                $this->profile_model->InsertIdentityProof($insert_array, $user_id, $catg);
            }
            $msg = lang('id_upload_success');
            $this->redirect($msg, "profile/kyc_upload", TRUE);
        }

        if ($this->input->post('delete')) {
            $id     = $this->input->post('delete');
            $result = $this->profile_model->deletetKyc($id, $user_id);
            if ($result) {
                $msg = lang('deleted_success');
                $this->redirect($msg, "profile/kyc_upload", true);
            }
            $msg = lang('something_wrong');
            $this->redirect($msg, "profile/kyc_upload", false);
        }

        $help_link = "kyc";
        $this->set("kyc_catg", $kyc_catg);
        $this->set("help_link", $help_link);
        $this->set("identity_proof", $id_proof);
        $this->setView('user/profile/kyc_upload');
    }

   
    public function update_custom_field()
    {
        if ($this->input->is_ajax_request()) {
            $response = array();
            $response['error'] = false;
            $post_arr = $this->input->post(null, true);
            $user_id = $this->LOG_USER_ID;

            if (!$user_id) {
                $response['error'] = true;
                $response['message'] = lang('invalid_user_name');
                echo json_encode($response);
                exit();
            }
            if ($this->validate_custom_details()) {
                $res = $this->profile_model->setCustomDetails($post_arr, $user_id);
                if ($res) {
                    $response['success'] = true;
                    $response['message'] = lang('custom_details_update_success');
                } else {
                    $response['error'] = true;
                    $response['message'] = lang('custom_details_update_error');
                }
            } else {
                $response['error'] = true;
                $response['message'] = lang('errors_check');
                foreach ($post_arr as $key => $value) {
                    $response['form_error'][$key] = form_error($key);
                }
            }
            echo json_encode($response);
            exit();
        }
    }

    public function validate_custom_details()
    {
        $details = $this->configuration_model->getRequiredCustomFields();
        $count = count($details);
        foreach($details as $det) {
            $this->form_validation->set_rules("{$det['field_name']}", '', 'trim|required|max_length[50]',
                        array('required' => 'This field is required')
                );
        }
        if($count > 0) {
            $validation_status = $this->form_validation->run();
            return $validation_status;
        } else {
            return true;
        }
    }
}
