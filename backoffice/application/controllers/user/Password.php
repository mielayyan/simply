<?php

require_once 'Inf_Controller.php';

class Password extends Inf_Controller
{

    function __construct()
    {
        parent::__construct();
//        $this->url_permission('password/change_password');
    }

    function edit_password()
    {
        $title = lang('change_password_login');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $help_link = 'change-password';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('change_password_login');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('change_password_login');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        //Function start for change password
        $user_type = $this->LOG_USER_TYPE;
        $user_id = $this->LOG_USER_ID;
        $user_name = $this->LOG_USER_NAME;
        $this->set('user_type', $user_type);

        $preset_demo = 'no';
        // UNCOMMENT FOLLOWING LINES OF CODE WHEN UPLOADING TO infinitemlmsoftware.com
//        $table_prefix = substr($this->db->dbprefix, 0, -1);
//        if ((DEMO_STATUS == 'yes') && (($table_prefix == 5552) || ($table_prefix == 5553) || ($table_prefix == 5554) || ($table_prefix == 5555) || ($table_prefix == 5556))) {
//            $preset_demo = 'yes';
//        }

        $this->set('passwordPolicyJson', json_encode($this->validation_model->getPasswordPolicyArray()));
        $this->set('preset_demo', $preset_demo);
        $this->setView('user/password/change_password');
    }

    function validate_change_password()
    {

        $this->lang->load('validation');
        $user_id     = $this->LOG_USER_ID;
        $post_arr    = $this->input->post(null, true);
        $post_arr    = $this->validation_model->stripTagsPostArray($post_arr);
        $current_pwd = $post_arr['current_pwd_admin'];
        $new_pwd     = $post_arr['new_pwd_admin'];
        $cf_pwd      = $post_arr['confirm_pwd_admin'];
        $val         = $this->password_model->validatePswd($new_pwd);
        $dbpassword  = $this->password_model->selectPassword($user_id);

        if (!$current_pwd) {
            $msg = lang('you_must_enter_your_current_password');
            $this->redirect($msg, 'password/change_password', false);
        } elseif (!$new_pwd) {
            $msg = lang('you_must_enter_new_password');
            $this->redirect($msg, 'password/change_password', false);
        } elseif (!$val) {
            $msg = lang('special_chars_not_allowed');
            $this->redirect($msg, 'password/change_password', false);
        } elseif (!password_verify($current_pwd, $dbpassword) || strlen($new_pwd) < 6) {
            $msg = lang('your_current_password_is_incorrect_or_new_password_is_too_short');
            $this->redirect($msg, 'password/change_password', false);
        } elseif (strcmp($new_pwd, $cf_pwd) != 0) {
            $msg = lang('password_mismatch');
            $this->redirect($msg, 'password/change_password', false);
        } else
            return true;
        $this->form_validation->set_rules('current_pwd_admin',lang('current_passwd'),"required|min_length[6]|max_length[100]",[
       "required"=>lang('required'),
       "min_length"=>sprintf(lang('minlength'),lang('current_passwd'),"6"),
       "max_length"=>sprintf(lang('maxlength'),lang('current_passwd'),"100"),
      ]);
       $this->form_validation->set_rules('new_pwd_admin',lang('new_passwd'),"{$this->validation_model->getPasswordPolicyValidationString()}",[
          "required"=>lang('required'),
       "max_length"=>sprintf(lang('maxlength'),lang('new_passwd'),"50"),
       ]);
        $this->form_validation->set_rules('confirm_pwd_admin',lang('cpasswd'),"required|matches[new_pwd_admin]|min_length[6]|max_length[100]",[
         "required"=>lang('required'),
         "matches"=>lang('password_mismatch'),
       "min_length"=>sprintf(lang('minlength'),lang('new_passwd'),"6"),
       "max_length"=>sprintf(lang('maxlength'),lang('new_passwd'),"100"),

        ]);
        return $this->form_validation->run_with_redirect("user/password/change_password");


    }
    //new password change for user 
    function change_user_login_password()
    {
        $user_type = $this->LOG_USER_TYPE;
        $user_id = $this->LOG_USER_ID;
        $user_name = $this->LOG_USER_NAME;
        if ($validated = $this->validate_user_change_password()) {

            $this->lang->load('validation');
            $post_arr = $this->input->post(null, true);
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
            $new_pwd  = $post_arr['new_pwd_user'];
            $current_pwd = $post_arr['current_pwd_user'];
            $cf_pwd      = $post_arr['confirm_pwd_user'];
            $val         = $this->password_model->validatePswd($new_pwd);
            $dbpassword  = $this->password_model->selectPassword($user_id);
            if (!$current_pwd) {
                 echo json_encode([
                        'status'     => 'failed',
                        'error_type' => 'unknown',
                        'message'    => lang('you_must_enter_your_current_password'),
                    ]); die;
            } elseif (!$new_pwd) {
                 echo json_encode([
                        'status'     => 'failed',
                        'error_type' => 'unknown',
                        'message'    => lang('you_must_enter_new_password'),
                    ]); die;
            } elseif (!$val) {
                echo json_encode([
                        'status'     => 'failed',
                        'error_type' => 'unknown',
                        'message'    => lang('special_chars_not_allowed'),
                    ]); die;
            } elseif (!password_verify($current_pwd, $dbpassword) ) {
                echo json_encode([
                        'status'     => 'failed',
                        'error_type' => 'unknown',
                        'message'    => lang('your_current_password_is_incorrect_or_new_password_is_too_short'),
                    ]); die;
            } elseif (strcmp($new_pwd, $cf_pwd) != 0) {
                echo json_encode([
                        'status'     => 'failed',
                        'error_type' => 'unknown',
                        'message'    => lang('password_mismatch'),
                    ]); die;
            }
            $new_pwd_md5 = password_hash($new_pwd, PASSWORD_DEFAULT);
            $admin_id = $this->validation_model->getAdminId();
            if (DEMO_STATUS == 'yes') {
                $preset_user = $this->validation_model->getPresetUser($admin_id);
                if ($preset_user == $user_name) {
                    $msg = 'You can\'t change preset user password';
                    echo json_encode([
                        'status'     => 'failed',
                        'error_type' => 'unknown',
                        'message'    => $msg,
                    ]); die;
                }
            }
            $update = $this->password_model->updatePassword($new_pwd_md5, $user_id, $user_type);
            if ($update) {

                if ($this->MODULE_STATUS['opencart_status_demo'] == "yes" && $this->MODULE_STATUS['opencart_status'] == "yes") {
                    $customer_id = $this->validation_model->getOcCustomerId($user_id);
                    $this->password_model->updateStorePassword($new_pwd, $customer_id);
                }
                $send_details = array();
                $type = 'change_password';
                $send_details['full_name'] = $this->validation_model->getUserFullName($user_id);
                $send_details['new_password'] = $new_pwd;
                $send_details['email'] = $this->validation_model->getUserEmailId($user_id);
                $send_details['first_name'] = $this->validation_model->getUserData($user_id, "user_detail_name");
                $send_details['last_name'] = $this->validation_model->getUserData($user_id, "user_detail_second_name");
                $result = $this->mail_model->sendAllEmails($type, $send_details);
                if($this->MODULE_STATUS["sms_status"] == "yes") {
                    $this->load->model("sms_model");
                    $mobile = $this->validation_model->getUserPhoneNumber($user_id);
                    $variableArray = [
                        "fullname" => $send_details['full_name'],
                        "company_name" => $this->COMPANY_NAME,
                        "new_password" => $new_pwd
                    ];
                    $this->sms_model->createAndSendSMS($this->LANG_ID, $type, $mobile, $variableArray);
                }
                $this->validation_model->insertUserActivity($user_id, 'password changed', $user_id);
                // $msg = lang('password_updated_successfully');
                echo json_encode([
                    'status'     => 'success',
                    'message'    => lang('password_updated_successfully')
                ]);exit();
            } else {
                echo json_encode([
                    'status'     => 'failed',
                    'error_type' => 'unknown',
                    'message'    => lang('error_on_password_updation')
                ]); exit();
            }


        }
        else {
                echo json_encode([
                    'status'     => 'failed',
                    'error_type' => 'unknown',
                    'message'    => lang('error_on_password_updation')
                ]); exit();
            }
    }
    function validate_user_change_password()
    {

        $this->lang->load('validation');
        
        $this->form_validation->set_rules('current_pwd_user',lang('current_passwd'),"required|min_length[6]|max_length[100]",[
       "required"=>lang('required'),
       "min_length"=>sprintf(lang('minlength'),lang('current_passwd'),"6"),
       "max_length"=>sprintf(lang('maxlength'),lang('current_passwd'),"100"),
      ]);
       $this->form_validation->set_rules('new_pwd_user',lang('new_passwd'),"{$this->validation_model->getPasswordPolicyValidationString()}",[
          "required"=>lang('required'),
       "max_length"=>sprintf(lang('maxlength'),lang('new_passwd'),"50"),
       ]);
        $this->form_validation->set_rules('confirm_pwd_user',lang('cpasswd'),"required|matches[new_pwd_admin]|min_length[6]|max_length[100]",[
         "required"=>lang('required'),
         "matches"=>lang('password_mismatch'),
       "min_length"=>sprintf(lang('minlength'),lang('new_passwd'),"6"),
       "max_length"=>sprintf(lang('maxlength'),lang('new_passwd'),"100"),

        ]);
        $status = $this->form_validation->run();
        if (!$status) {
            return [
                'status' => false,
                'validation_error' => $this->form_validation->error_array(),
                'message' => lang('errors_check')
            ];
        }
        return [
            'status' => true
        ];


    }
    //end of new password change 


    function post_change_password()
    {
        $user_type = $this->LOG_USER_TYPE;
        $user_id = $this->LOG_USER_ID;
        $user_name = $this->LOG_USER_NAME;
        if ($this->input->post('change_pass_button_admin') && $this->validate_change_password()) {
            // UNCOMMENT FOLLOWING 3 LINES OF CODE WHEN UPLOADING TO infinitemlmsoftware.com
//            if ($preset_demo == 'yes' && (($user_name == 'INF750391') || ($user_name == 'INF823741') || ($user_name == 'INF792691') || ($user_name == 'INF793566') || ($user_name == 'INF867749'))) {
//                $msg = lang('this_option_is_not_available_for_preset_users');
//                $this->redirect($msg, 'tran_pass/change_passcode', FALSE);
//            }
            $post_arr = $this->input->post(null, true);
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
            $new_pwd  = $post_arr['new_pwd_admin'];
            $new_pwd_md5 = password_hash($new_pwd, PASSWORD_DEFAULT);
            $admin_id = $this->validation_model->getAdminId();
            if (DEMO_STATUS == 'yes') {
                $preset_user = $this->validation_model->getPresetUser($admin_id);
                if ($preset_user == $user_name) {
                    $msg = 'You can\'t change preset user password';
                    $this->redirect($msg, "password/change_password", false);
                }
            }
            $update = $this->password_model->updatePassword($new_pwd_md5, $user_id, $user_type);
            if ($update) {

                if ($this->MODULE_STATUS['opencart_status_demo'] == "yes" && $this->MODULE_STATUS['opencart_status'] == "yes") {
                    $customer_id = $this->validation_model->getOcCustomerId($user_id);
                    $this->password_model->updateStorePassword($new_pwd, $customer_id);
                }
                $send_details = array();
                $type = 'change_password';
                $send_details['full_name'] = $this->validation_model->getUserFullName($user_id);
                $send_details['new_password'] = $new_pwd;
                $send_details['email'] = $this->validation_model->getUserEmailId($user_id);
                $send_details['first_name'] = $this->validation_model->getUserData($user_id, "user_detail_name");
                $send_details['last_name'] = $this->validation_model->getUserData($user_id, "user_detail_second_name");
                $result = $this->mail_model->sendAllEmails($type, $send_details);
                if($this->MODULE_STATUS["sms_status"] == "yes") {
                    $this->load->model("sms_model");
                    $mobile = $this->validation_model->getUserPhoneNumber($user_id);
                    $variableArray = [
                        "fullname" => $send_details['full_name'],
                        "company_name" => $this->COMPANY_NAME,
                        "new_password" => $new_pwd
                    ];
                    $this->sms_model->createAndSendSMS($this->LANG_ID, $type, $mobile, $variableArray);
                }
                $this->validation_model->insertUserActivity($user_id, 'password changed', $user_id);
                $msg = lang('password_updated_successfully');
                $this->redirect($msg, 'user/profile_view', true);
            } else {
                $msg = lang('error_on_password_updation');
                $this->redirect($msg, 'password/change_password', false);
            }
        }
    }

}
