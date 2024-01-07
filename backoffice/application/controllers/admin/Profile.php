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
        $user_id = $this->validation_model->userNameToID($this->input->post('user_name'));
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
                    
            }
            http_response_code(200);
            echo json_encode([
                'status' => true,
                'message' => lang('profile updated successfully'),
            ]);die;
    }
    function profile_view($value='')
    {
        
        $this->HEADER_LANG['page_top_header'] = lang('profile_management');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('profile_management');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $this->lang->load('validation',$this->LANG_NAME);
        $this->lang->load('tran_pass',$this->LANG_NAME);
        $this->lang->load('password',$this->LANG_NAME);
        $this->lang->load('member',$this->LANG_NAME);
        $this->lang->load('user_account',$this->LANG_NAME);
        $help_link = 'profile-management';
        $this->set('help_link', $help_link);
        if($value !=''){
            $user_id = $value;
            $user_name = $this->validation_model->IdToUserName($user_id);
        }else{
            $user_name = $this->LOG_USER_NAME;
             $user_id = $this->LOG_USER_ID;
        }
        // $user_name = $this->LOG_USER_NAME;
        // $user_id = $this->LOG_USER_ID;
        $this->lang->load('home', $this->LANG_NAME);
        $user_type = $this->validation_model->getUserType($user_id);
        if ($this->LOG_USER_TYPE == 'employee') {
            $user_name = $this->ADMIN_USER_NAME;
            $user_id = $this->ADMIN_USER_ID;
        }
        if ($this->input->get('user_name')) {
            $user_name = $this->input->get('user_name') ?: '';
            $user_id = $this->validation_model->userNameToID($user_name);
            if (empty($user_id)) {
                $msg = lang('invalid_username');
                $this->redirect($msg, 'profile_view', false);
            }
            else
            {
                $user_type = $this->validation_model->getUserType($user_id);
            }
        }

        $this->load->model('configuration_model');
        $this->load->model('payout_model');
        $bank_info_status = $this->configuration_model->getBankInfoStatus();
        $age_limit = $this->configuration_model->getAgeLimitSetting();
        
        $title = lang('profile_management');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

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

        $country_id = $profile_details['country_id'];
        $state_id = $profile_details['state_id'];
        $countries = $this->country_state_model->viewCountry($country_id);
        $states = $this->country_state_model->viewState($country_id, $state_id);
        $states = "<option value=''>" . lang('no_state_selected') . "</option>" . $states;

        if ($country_id != '') {
            $mob_code = $this->country_state_model->getCountryTelephoneCode($country_id);
            $mobile_code = "+" . $mob_code;
        } else {
            $mobile_code = "";
        }

        $product_name = '';
        $product_validity = '';
        $store_id = '';
        if($this->MODULE_STATUS['opencart_status'] == "yes")
        {
            if (DEMO_STATUS == 'yes'){
            $store_id = "&id=".str_replace("_","",$this->db->dbprefix);
            }
        }
        $this->set('store_id', $store_id);
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
        

        // update PV
            $this->load->model('select_report_model');

            $current_rank = $this->validation_model->getUserRank($user_id);
            $rank_detail = [];
            if ($current_rank != '') {
                $rank_detail = $this->select_report_model->selectRankDetails($current_rank);
            } else {
                $rank_detail['rank_name'] = "NA";
            }
            $member_personal_pv = $this->validation_model->getPersnlPv($user_id);
            $member_group_pv = $this->validation_model->getGrpPv($user_id);

            $this->set("rank_detail", $rank_detail);
            $this->set("member_personal_pv", $member_personal_pv);
            $this->set("member_group_pv", $member_group_pv);
        // end of update pv


        // end of kyc
        $payment_gateway = $this->profile_model->getActivePaymentGateway();
        $custom_details = $this->configuration_model->getCustomFields($user_id);
        $payment_method = $this->payout_model->gatewayList();
        $dynamic_fields = $this->register_model->getContactInfoFields();
        //$profile=$this->input->get('profile');
        // rank configuration
        $rank_configuration = $this->configuration_model->getRankConfiguration();
        $this->set("rank_configuration", $rank_configuration);
        // end of rank configuration
        // 
        $individula_details = $this->home_model->individulaDetails($user_id, ['personal_pv', 'gpv']);
            $profile_extra_data = [
                'personal_pv' => $individula_details->personal_pv == 0 ? 0 : $individula_details->personal_pv,
                'group_pv' => $individula_details->gpv == 0 ? 0 : $individula_details->gpv,
            ];
        $this->set('profile_extra_data', $profile_extra_data);

         $this->set('passwordPolicyJson', json_encode($this->validation_model->getPasswordPolicyArray()));
         //leg_settings 
        $user_active = $this->validation_model->isUserActive($user_id);
        $this->set("user_active", $user_active);
        if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
        $get_leg_type = $this->tree_model->get_leg_type($user_id);
        $get_leg_settings = $this->tree_model->get_bnary_leg_setng();
        $this->set('get_leg_type', $get_leg_type);
        $this->set('get_leg_settings', $get_leg_settings);
        }
        //end os leg settings
        //binary model 
        if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
        $binary_tree = $this->home_model->individulaDetails($user_id, ['total_left_carry','total_right_carry']);
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
        $this->set("binary_tree_carry", $binary_tree_carry);
        // rank color 
        $rank_color = "#7d899b";
        $user_rank_id = $this->validation_model->getUserRank($user_id);
        if($user_rank_id)
        {
            $rank_details = $this->configuration_model->getActiveRankDetails($user_rank_id);
            if ($rank_details) {
                $rank_color =$rank_details[0]['rank_color'];
            }
        }
        $this->set("rank_color",$rank_color);
        // end of rak color 
        //package upgarde
            if($this->MODULE_STATUS['package_upgrade'] == "yes" && $this->MODULE_STATUS['opencart_status'] == "no") {
                $this->load->model('upgrade_model');
                $current_package_details = $this->upgrade_model->getMembershipPackageDetails($user_id);
                $upgradable_package_list = $this->upgrade_model->getUpgradablePackageList($current_package_details);
                $this->set('upgradable_package_list', $upgradable_package_list);
            }
        // end of package upgrade

        $country_id = $profile_details['country_id'];
        $state_id = $profile_details['state_id'];
        $countries = $this->country_state_model->viewCountry($country_id);
        $states = $this->country_state_model->viewState($country_id, $state_id);
        $full_name = $profile_details["name"]." ".$profile_details["user_detail_second_name"];
        $this->set('full_name', $full_name);
        $this->set('user_type', $user_type);
        $this->set('custom_details', $custom_details);
        $this->set('gateway_list', $payment_method);
        $this->set('payment_gateway', $payment_gateway);
        $this->set('bank_info_status', $bank_info_status);
        $this->set('age_limit', $age_limit);
        $this->set('user_name', $user_name);
        $this->set('countries', $countries);
        $this->set('user_country_id', $country_id);
        $this->set('user_state_id', $state_id);
        $this->set('mobile_code', $mobile_code);
        $this->set('product_validity', $product_validity);
        $this->set('states', $states);
        $this->set('product_name', $this->security->xss_clean($product_name));
        $this->set('product_status', $product_status);
        $this->set('product_validity', $product_validity);
        $this->set('pin_status', $pin_status);
        $this->set('profile_details', $this->security->xss_clean($profile_details));
        $this->set('dynamic_fields', $dynamic_fields);

        $this->setView();
    }

    function validate_user_account()
    {
        $this->form_validation->set_rules('user_name', lang('user_name'), 'trim|required|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function val_user_name($user_name, $k)
    {
        $user_id = $this->validation_model->userNameToID($user_name);
        if ($user_id && $k)
            return true;
        else if (!$user_id && !$k)
            return true;
        else
            return false;
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
    // update settings(language,currency,leg locking,google authentication)
    public function update_default_settings()
    {
        if ($this->input->is_ajax_request()) {
            $response = array();
            $response['error'] = false;
            $post_arr = $this->input->post(null, true);
            $user_id = $this->validation_model->userNameToID($this->input->post('profile_user'));
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
            $user_type = $this->validation_model->getUserType($user_id);
            if($user_type == 'user')
            {
                if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') 
                {

                $res3 = $this->tree_model->updateLeg($user_id, $post_arr['binary_leg']);

                }
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
    
    function business_volume() {
        // HEADER
        $title = lang('business_volume');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);
        $this->HEADER_LANG['page_top_header'] = lang('business_volume');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('business_volume');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();

        // DATA
        $user_name = $this->input->get('user_name') ?: $this->LOG_USER_NAME;
        $user_id = $this->validation_model->userNameToID($user_name);
        if (!$user_id) {
            $msg = lang('invalid_user');
            $this->redirect($msg, 'profile/business_volume', false);
            return false;
        }
        $volume_details = $this->profile_model->getBusinessVolumeDetails($this->PAGINATION_PER_PAGE, $this->input->get('offset'), $user_id);
        
        // Pagination
        $count = $this->profile_model->getTotalBusinessVolumeCount($user_id);
        $this->pagination->set_all('admin/business_volume', $count);

        // SET DATA TO VIEW
        $this->set('user_name', $user_name);
        $this->set('details', $volume_details);
        $this->setView();
    }

    public function ajax_is_username_available()
    {
        $user_name = $this->input->post('user_name', true);
        if (!$user_name) {
            echo 'no';
            exit();
        }
        $is_username_exists = $this->validation_model->isUsernameExists($user_name);
        if ($is_username_exists) {
            echo 'no';
            exit();
        } else {
            echo 'yes';
            exit();
        }
    }
    public function update_personal_info()
    {
        if ($this->input->is_ajax_request()) {
            $response = array();
            $response['error'] = false;
            $post_arr = $this->input->post(null, true);
            // $this->confirmOtp();
            $user_id = $this->validation_model->userNameToID($this->input->post('profile_user'));
            if (!$user_id) {
                $response['error'] = true;
                $response['message'] = lang('invalid_user_name');
                echo json_encode($response);
                exit();
            }
            if ($this->validate_personal_info()) {
                $opencart_status = $this->MODULE_STATUS['opencart_status'] == "yes" && $this->MODULE_STATUS['opencart_status_demo'] == "yes";
                $res = $this->profile_model->updatePersonalInfo($user_id, $post_arr, $opencart_status);
                if ($res) {
                    //insert configuration_change_history
                    $settings = $this->configuration_model->getSettings();
                    if ($settings['profile_updation_history']) {
                        $history = "Updated " . $post_arr['profile_user'] . "'s " . "Personal info as First Name :" . $post_arr['first_name'] . ", ";
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
            echo json_encode($response);
            exit();
        }
    }

    /**
     * [validate_personal_info description]
     * @return [bool] 
     */
    public function validate_personal_info() {
        $this->lang->load('validation', $this->LANG_NAME);
        $this->lang->load('register', $this->LANG_NAME);
        $this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required|max_length[250]', [
                'max_length' => sprintf(lang('maxlength'), lang('first_name'), "250")
            ],
        );

        $this->form_validation->set_rules('last_name', lang('last_name'), 'trim|max_length[250]|callback_check_required[last_name]', [
            'max_length' => sprintf(lang('maxlength'), lang('last_name'), "250"),
        ]);

        $this->form_validation->set_rules('gender', lang('gender'), 'trim|callback_check_required[gender]|in_list[M,F]', ['in_list' => lang('You_must_select_gender')]);

        $this->form_validation->set_rules('dob', lang('date_of_birth'), 'trim|callback_check_required[date_of_birth]|callback_validate_age_year', [
                'validate_age_year' => sprintf(lang('valid_age'), $this->configuration_model->getAgeLimitSetting()),
            ]);

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

    public function update_contact_info() {
        if ($this->input->is_ajax_request()) {
            $response = array();
            $response['error'] = false;
            $post_arr = $this->input->post(null, true);
            // $this->confirmOtp();
            $user_id = $this->validation_model->userNameToID($this->input->post('profile_user'));
            if (!$user_id) {
                $response['error'] = true;
                $response['message'] = lang('invalid_user_name');
                echo json_encode($response);
                exit();
            }
            if ($this->validate_contact_info()) {
                $opencart_status = $this->MODULE_STATUS['opencart_status'] == "yes" && $this->MODULE_STATUS['opencart_status_demo'] == "yes";
                $res = $this->profile_model->updateContactInfo($user_id, $post_arr, $opencart_status);
                if ($post_arr['country'] != '0') {
                    $countries = $this->country_state_model->getCountries($post_arr['country']);
                }
                if ($post_arr['state'] != '0') {
                    $states = $this->country_state_model->getStates($post_arr['country'], $post_arr['state']);
                }
                if ($res) {
                    //insert configuration_change_history
                    $settings = $this->configuration_model->getSettings();
                    if ($settings['profile_updation_history']) {
                        $history = "Updated " . $post_arr['profile_user'] . "'s " . " Contact Info as Address Line 1:" . $post_arr['address'] . ", ";
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

    public function validate_contact_info() {
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

    public function update_bank_info() {
        if ($this->input->is_ajax_request()) {
            $response = array();
            $response['error'] = false;
            $post_arr = $this->input->post(null, true);
            // $this->confirmOtp();
            $user_id = $this->validation_model->userNameToID($this->input->post('profile_user'));
            if (!$user_id) {
                $response['error'] = true;
                $response['message'] = lang('invalid_user_name');
                echo json_encode($response);
                exit();
            }
            if ($this->validate_bank_info()) {
                $res = $this->profile_model->updateBankInfo($user_id, $post_arr);
                if ($res) {
                    //insert configuration_change_history
                    $settings = $this->configuration_model->getSettings();
                    if ($settings['profile_updation_history']) {
                        $history = "Updated " . $post_arr['profile_user'] . "'s " . " Bank Info as Account no. :" . $post_arr['account_no'] . ", ";
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

    /**
     * [validate_bank_info description]
     * @return [type] [description]
     */
    public function validate_bank_info() {
        $this->form_validation->set_rules('bank_name', lang('bank_name'), 'trim|max_length[250]');
        $this->form_validation->set_rules('branch_name', lang('bank_branch'), 'trim|max_length[250]');
        $this->form_validation->set_rules('account_holder', lang('acct_holder_name'), 'trim|max_length[250]');
        $this->form_validation->set_rules('account_no', lang('account_no'), 'trim|max_length[250]');
        $this->form_validation->set_rules('ifsc', lang('ifsc'), 'trim|max_length[250]');
        $this->form_validation->set_rules('pan', lang('pan_no'), 'trim|max_length[250]');
        return $this->form_validation->run();
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

    public function validate_age($dob)
    {
        if (!$this->input->post('year') || $this->input->post('month') < 0 || !$this->input->post('day')) {
            return true;
        }
        $age_limit = $this->configuration_model->getAgeLimitSetting();
        if ($age_limit == 0) {
            return true;
        }
        $date1 = new DateTime($dob);
        $date1->add(new DateInterval("P{$age_limit}Y"));
        $date2 = new DateTime();
        if ($date1 <= $date2) {
            return true;
        } else {
            $this->form_validation->set_message('validate_age', sprintf(lang('You_should_be_atleast_n_years_old'), $age_limit));
            return false;
        }
    }

    /**
     * [validate_age_year description]
     * @param  [type] $dob [description]
     * @return [type]      [description]
     */
    public function validate_age_year($dob) {
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

    /*Ajax Function For Payment Details Updation Begins*/
    public function update_payment_details()
    {
        if ($this->input->is_ajax_request()) {
            $response = array();
            $response['error'] = false;
            $post_arr = $this->input->post(null, true);
            // $this->confirmOtp();
            $user_id = $this->validation_model->userNameToID($this->input->post('profile_user'));
            $user_name = $this->input->post('profile_user', true);
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
                        $history = "Updated " . $post_arr['profile_user'] . "'s Payment details as";
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

        $validation_status = $this->form_validation->run();
        return $validation_status;
    }

    /*Ajax Function For Payment Details Updation Ends*/

    public function validate_language_details()
    {
        $this->form_validation->set_rules('language', lang('language'), 'trim|required');

        $validation_status = $this->form_validation->run();
        return $validation_status;
    }
    public function checkAdmin()
    {
        $post_user = $this->input->post('user_name');
        if ($post_user && $post_user == $this->validation_model->getAdminUsername()) {
            echo "yes";
            exit;
        }
        echo "no";
        exit;
    }
    public function profileOtpModal()
    {
        $status = false;
        $otp = rand(pow(10, 4), pow(10, 5) - 1);
        if ($otp) {
            if (!empty($this->session->userdata('profile_otp')))
                $this->session->unset_userdata('profile_otp');
            $type = lang('profile_update');
            $this->mail_model->sendOtpMail($otp, $this->validation_model->getUserEmailId($this->validation_model->getAdminId()), $type);
            $this->session->set_userdata('profile_otp', $otp);
            echo $status = true;
            exit;
        } else {
            echo $status;
            exit;
        }
    }
    

    //KYC Approval starts
    public function kyc() {
        $title = lang('kyc_details');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('kyc_details');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('kyc_details');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $this->load->model('configuration_model');
        $this->url_permission('kyc_status');
        $type = '';
        
        $u_id = '';
        $show_table = 'no';
        $uname = $this->input->get('user') ?:'';
        $status = $this->input->get('status')?:'Pending';
        $u_id = $this->validation_model->userNameToID($uname);
        if ($uname && !$u_id) {
            $msg = lang('invalid_user');
            $this->redirect($msg, "profile/kyc", false);
        }
        $show_table = 'yes';
        $kyc_list = $this->profile_model->getPendingKyc($u_id, $type, $status);
        $kyc_catg = $this->configuration_model->getKycDocCategory();

        $this->set("type", $type);
        $this->set("show_table", $show_table);
        $this->set("uname", $uname);
        $this->set("status", $status);
        $this->set("kyc_catg", $kyc_catg);
        $this->set("kyc_list", $kyc_list);
        $this->setView();
    }

    public function ajaxVerify()
    {
        $user_name = $this->input->post('user_name');
        $type = $this->input->post('type');
        $u_id = $this->validation_model->userNameToID($user_name);
        if ($user_name) {
            $status = $this->profile_model->verifyKyc($u_id, $type);
            if ($status) {
                echo 'yes';
                exit();
            }
        }
        echo 'no';
        exit();
    }

    public function ajaxReject()
    {
        $user_name = $this->input->post('user_name');
        $type = $this->input->post('type');
        $reason = $this->input->post('reason');
        $u_id = $this->validation_model->userNameToID($user_name);
        if ($u_id != '' && $reason != '') {
            $status = $this->profile_model->rejectKyc($u_id, $type, $this->security->xss_clean($reason));
            if ($status) {
                echo 'yes';
                exit();
            }
        }
        echo 'no';
        exit();
    }
    //KYC Approval ends

    

    
    
    
    
    public function update_custom_field()
    {
        if ($this->input->is_ajax_request()) {
            $response = array();
            $response['error'] = false;
            $post_arr = $this->input->post(null, true);
            $user_id = $this->validation_model->userNameToID($this->input->post('profile_user'));
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
    function change_username() {
        $title = lang('change_username');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $help_link = 'change-username';
        $this->set('help_link', $help_link);
        $this->lang->load('activate');
        
        $this->HEADER_LANG['page_top_header'] = lang('change_username');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('change_username');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        if ($this->input->post('change_username')) {
            if($this->validate_change_username())
            {
            $post_arr = $this->input->post(NULL, TRUE);
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
            $user_name = $post_arr['user_name'];
            $user_id = $this->validation_model->userNameToID($user_name);
            $new_user_name = $post_arr['new_username'];

            $admin_id = $this->validation_model->getAdminId();

            if ($user_id != $admin_id) {
                if(DEMO_STATUS == 'yes') {
                    $preset_user = $this->validation_model->getPresetUser($admin_id);
                    if($preset_user == $user_name) {
                        $msg = 'You can\'t change preset username';
                        $this->redirect($msg, "profile/change_username", false);
                    }
                }
                $res = $this->profile_model->changeUsername($user_id, $user_name, $new_user_name);
                if (!$res) {
                    $msg = lang('username_cannot_be_changed');
                    $this->redirect($msg, 'profile/change_username', FALSE);
                } else {
                    $data = serialize($post_arr);
                    $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'username changed', $user_id, $data);
                    
                    // Employee Activity History
                    if ($this->LOG_USER_TYPE == 'employee') {
                        $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $user_id, 'change_username', 'Username Changed');
                    }
                    //
                    
                    $msg = lang('username_changed_successfully');
                    $this->redirect($msg, 'profile/change_username', TRUE);
                }
            } else {
                $msg = "You can't change admin username.";
                $this->redirect($msg, 'profile/change_username', FALSE);
            }
        }
        else
        {
            $this->setView();
        }
    }

        $this->setView();
    }
    function getUsernameRange()
    {

        $usernameRange = $this->validation_model->getUsernameRange();
        dd($usernameRange);
        echo json_encode($usernameRange);
        exit();
    }
    function validate_change_username() {
        $usernameRange = $this->validation_model->getUsernameRange();

        $this->form_validation->set_rules('new_username', lang('user_name'), 'trim|required|alpha_numeric|min_length['.$usernameRange['min'].']|max_length['.$usernameRange['max'].']|callback_is_username_available');
        $this->form_validation->set_rules('user_name', lang('user_name'), 'required|callback_validate_username|trim');
        $this->form_validation->set_message('val_user_name', $this->lang->line('invalid_username_or_new_username_exists'));
        $this->form_validation->set_message('is_username_available', $this->lang->line('user_name_not_available'));
        $this->form_validation->set_message('validate_username', $this->lang->line('incorrect_username'));

        $val = $this->form_validation->run();
        if (!$val) {
            return false;
        } else
        return true;
    }
    public function is_username_available($user_name)
    {
        if (!$user_name) {
            return FALSE;
        }
        $is_username_exists = $this->validation_model->isUsernameExists($user_name);
        if ($is_username_exists) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    function validate_username($ref_user = '') {
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
}