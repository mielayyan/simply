<?php

require_once 'Inf_Controller.php';

class Password extends Inf_Controller
{

    function __construct()
    {
        parent::__construct();
    }
    //new password change for user 
    function change_user_login_password()
    {
        // $user_type = $this->LOG_USER_TYPE;
        // $user_id = $this->LOG_USER_ID;
        // $user_name = $this->LOG_USER_NAME;
        $user_name = $this->input->post('user_name');
        $user_id = $this->validation_model->userNameToID($user_name);
        $user_type = $this->validation_model->getUserType($user_id);
        if ($validated = $this->validate_user_change_password()) {

            $this->lang->load('validation');
            $post_arr = $this->input->post(null, true);
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
            $new_pwd  = $post_arr['new_pwd_user'];
            $current_pwd = $post_arr['current_pwd_user'];
            $cf_pwd      = $post_arr['confirm_pwd_user'];
            $val         = $this->password_model->validatePswd($new_pwd);
            $dbpassword  = $this->password_model->selectPassword($user_id);
            if($user_type == 'admin')
            {
                if (!$current_pwd) {
                     echo json_encode([
                            'status'     => 'failed',
                            'error_type' => 'unknown',
                            'message'    => lang('you_must_enter_your_current_password'),
                        ]); die;
                } 
            }
            if (!$new_pwd) {
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
            }
            if($user_type == 'admin')
            {
                if (!password_verify($current_pwd, $dbpassword) ) {
                    echo json_encode([
                            'status'     => 'failed',
                            'error_type' => 'unknown',
                            'message'    => lang('your_current_password_is_incorrect_or_new_password_is_too_short'),
                        ]); die;
                }
            }if (strcmp($new_pwd, $cf_pwd) != 0) {
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
        $user_id = $this->validation_model->userNameToID($this->input->post('user_name'));
        $user_type = $this->validation_model->getUserType($user_id);
        if($user_type == 'admin')
        {
           $this->form_validation->set_rules('current_pwd_user',lang('current_passwd'),"required|min_length[6]|max_length[100]",[
           "required"=>lang('required'),
           "min_length"=>sprintf(lang('minlength'),lang('current_passwd'),"6"),
           "max_length"=>sprintf(lang('maxlength'),lang('current_passwd'),"100"),
          ]);
        }
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
    function change_password()
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

        $user_type = $this->LOG_USER_TYPE;
        $user_id = $this->LOG_USER_ID;
        $user_name = $this->LOG_USER_NAME;
        if ($user_type == 'employee') {
            $user_id = $this->validation_model->getAdminId();
            $tab2 = ' active';
            $tab1 = '';
        } else {
            $tab1 = ' active';
            $tab2 = '';
        }
        $table_prefix = $this->password_model->table_prefix;
        $user_ref_id = str_replace('_', '', $table_prefix);
        $this->set('user_type', $user_type);
        $msg = '';

        $preset_demo = 'no';

        if ($this->session->userdata('inf_pass_tab_active_arr')) {
            $tab_arr = $this->session->userdata('inf_pass_tab_active_arr');
            $tab1 = $tab_arr['tab1'];
            $tab2 = $tab_arr['tab2'];
            $this->session->unset_userdata('inf_pass_tab_active_arr');
        }

        $this->set('preset_demo', $preset_demo);
        $this->set('user_type', $user_type);
        $this->set('tab1', $tab1);
        $this->set('tab2', $tab2);
        $this->set('passwordPolicyJson', json_encode($this->validation_model->getPasswordPolicyArray()));
        $this->setView();
    }

    function validate_change_password_change_pass_admin()
    {    
        $this->lang->load('validation');

        $tab1 = ' active';
        $tab2 = '';
        $this->session->set_userdata('inf_pass_tab_active_arr', array('tab1' => $tab1, 'tab2' => $tab2));

        $user_id     = $this->LOG_USER_ID;
        $post_arr    = $this->validation_model->stripTagsPostArray($this->input->post());
        $current_pwd = $post_arr['current_pwd_admin'];
        $new_pwd     = $post_arr['new_pwd_admin'];
        $cf_pwd      = $post_arr['confirm_pwd_admin'];
        $val         = $this->password_model->validatePswd($new_pwd);
        $dbpassword  = $this->password_model->selectPassword($user_id);

        // if (!$current_pwd) {
        //     $msg = lang('you_must_enter_your_current_password');
        //     $this->redirect($msg, 'password/change_password', false);
        // } elseif (!$new_pwd) {
        //     $msg = lang('you_must_enter_new_password');
        //     $this->redirect($msg, 'password/change_password', false);
        // } elseif (!$val) {
        //     $msg = lang('special_chars_not_allowed');
        //     $this->redirect($msg, 'password/change_password', false);
        // } elseif (!password_verify($current_pwd, $dbpassword) || strlen($new_pwd) < 6) {
        //     $msg = lang('your_current_password_is_incorrect_or_new_password_is_too_short');
        //     $this->redirect($msg, 'password/change_password', false);
        // } elseif (strcmp($new_pwd, $cf_pwd) != 0) {
        //     $msg = lang('password_mismatch');
        //     $this->redirect($msg, 'password/change_password', false);
        // } else
        //     return true;
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
      $validate_form=$this->form_validation->run_with_redirect("admin/password/change_password");
      return $validate_form;

    }

    function validate_change_password_change_pass_common()
    {

        $this->lang->load('validation');
        $tab1 = '';
        $tab2 = ' active';
        $this->session->set_userdata('inf_pass_tab_active_arr', array('tab1' => $tab1, 'tab2' => $tab2));

        $post_arr     = $this->validation_model->stripTagsPostArray($this->input->post());
        $name_user    = $post_arr['user_name_common'];
        $id_user      = $this->validation_model->userNameToID($name_user);
        $new_pwd_user = $post_arr['new_pwd_common'];
        $cf_pwd_user  = $post_arr['confirm_pwd_common'];
        $val          = $this->password_model->validatePswd($new_pwd_user);
        $admin_id     = $this->validation_model->getAdminId();

        // if (!$name_user) {
        //     $msg = lang('You_must_enter_user_name');
        //     $this->redirect($msg, 'password/change_password', false);
        // } elseif (!$this->password_model->isUserNameAvailable($name_user)) {
        //     $msg = lang('invalid_user_name');
        //     $this->redirect($msg, 'password/change_password', false);
        // } elseif (!$new_pwd_user || !$cf_pwd_user) {
        //     $msg = lang('you_must_enter_new_password');
        //     $this->redirect($msg, 'password/change_password', false);
        // } elseif (!$val) {
        //     $msg = lang('special_chars_not_allowed');
        //     $this->redirect($msg, 'password/change_password', false);
        // } elseif (strlen($new_pwd_user) < 6) {
        //     $msg = lang('New_password_is_too_short');
        //     $this->redirect($msg, 'password/change_password', false);
        // } elseif (strcmp($new_pwd_user, $cf_pwd_user) != 0) {
        //     $msg = lang('password_mismatch');
        //     $this->redirect($msg, 'password/change_password', false);
        // } elseif ($admin_id == $id_user) {
        //     $msg = lang('You_cant_change_admin_password');
        //     $this->redirect($msg, 'password/change_password', false);
        // } else
        //     return true;

      $this->form_validation->set_rules('user_name_common',lang('user_name'),"required|max_length[100]|callback_valid_user",[
       "required"=>lang('required'),
       "max_length"=>sprintf(lang('maxlength'),lang('current_passwd'),"100"),
       "valid_user"=>lang('invalid_username')
      ]);
       $this->form_validation->set_rules('new_pwd_common',lang('new_passwd'),"{$this->validation_model->getPasswordPolicyValidationString()}",[
          "required"=>lang('required'),
       "max_length"=>sprintf(lang('maxlength'),lang('new_passwd'),"50"),
       ]);
        $this->form_validation->set_rules('confirm_pwd_common',lang('cpasswd'),"required|matches[new_pwd_common]|min_length[6]|max_length[100]",[
         "required"=>lang('required'),
         "matches"=>lang('password_mismatch'),
       "min_length"=>sprintf(lang('minlength'),lang('new_passwd'),"6"),
       "max_length"=>sprintf(lang('maxlength'),lang('new_passwd'),"100"),

        ]);
      $form_validation= $this->form_validation->run_with_redirect("admin/password/change_password");
      return $form_validation;

    }

    function validate_username()
    {
        $username = ($this->input->post('username', true));
        if ($username != '') {
            $valid = 'no';
            if ($this->password_model->isUserNameAvailable($username)) {
                $valid = 'yes';
            }
            echo $valid;
            exit();
        }
    }
    function post_change_password()
    {
        $user_type = $this->LOG_USER_TYPE;
        $user_id = $this->LOG_USER_ID;
        $user_name = $this->LOG_USER_NAME;
        if ($user_type == 'employee') {
            $user_id = $this->validation_model->getAdminId();
            $tab2 = ' active';
            $tab1 = '';
        } else {
            $tab1 = ' active';
            $tab2 = '';
        }
        $table_prefix = $this->password_model->table_prefix;
        $user_ref_id = str_replace('_', '', $table_prefix);
        $preset_demo = 'no';
        ///admin password......
        if ($this->input->post('change_pass_button_admin')&& $this->validate_change_password_change_pass_admin())  { 
            

           // )
//            if($preset_demo == 'yes') {
//                $msg = lang('this_option_is_not_available_in_preset_demos');
//                $this->redirect($msg, 'password/change_password', FALSE);
//            }
            $admin_passwd=$this->validation_model->getAdminPassword();
            
            
             $current_passwd=$this->input->post('current_pwd_admin');
             $current_passwd_decode=$current_passwd;
             
             if(!password_verify($current_passwd_decode,$admin_passwd))
             {
                $msg="your current password is incorrect";
                $this->redirect($msg, "password/change_password", false);
             }
            $post_arr = $this->input->post(null, true);
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
            $new_pwd = $post_arr['new_pwd_admin'];
            $new_pwd_md5 = password_hash($new_pwd, PASSWORD_DEFAULT);
            if (DEMO_STATUS == 'yes') {
                $is_preset_demo = $this->validation_model->isPresetDemo($user_id);
                if ($is_preset_demo) {
                    $msg = 'You cannot change preset admin password';
                    $this->redirect($msg, "password/change_password", false);
                }
            }
            $update = $this->password_model->updatePassword($new_pwd_md5, $user_id, $user_type, $user_ref_id);
            if ($update) {

                if ($this->MODULE_STATUS['opencart_status_demo'] == "yes" && $this->MODULE_STATUS['opencart_status'] == "yes") {
                    $customer_id = $this->validation_model->getOcCustomerId($user_id);
                    $this->password_model->updateStorePassword($new_pwd, $customer_id);
                }
                $send_details = array();
                $type = 'change_password';
                $email = $this->validation_model->getUserEmailId($user_id);
                $send_details['full_name'] = $this->validation_model->getUserFullName($user_id);
                $send_details['new_password'] = $new_pwd;
                $send_details['email'] = $email;
                $send_details['first_name'] = $this->validation_model->getUserData($user_id, "user_detail_name");
                $send_details['last_name'] = $this->validation_model->getUserData($user_id, "user_detail_second_name");

                $result = $this->mail_model->sendAllEmails($type, $send_details);
                // send sms
                if($this->MODULE_STATUS["sms_status"] == "yes") {
                    $this->load->model("sms_model");
                    $mobile = $this->validation_model->getUserPhoneNumber($user_id);
                    $variableArray = [
                        "fullname" => $send_details['first_name'] . " " . $send_details['last_name'],
                        "company_name" => $this->COMPANY_NAME,
                        "new_password" => $new_pwd
                    ];
                    $this->sms_model->createAndSendSMS($this->LANG_ID, $type, $mobile, $variableArray);
                }
                // end::send sms

                $data = serialize($send_details);
                $this->validation_model->insertUserActivity($user_id, 'password changed', $user_id, $data);

                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $user_id, 'change_password', 'Password Changed');
                }
                //

                $msg = lang('password_updated_successfully');
                $this->redirect($msg, 'password/change_password', true);
            } else {
                $msg = lang('error_on_password_updation');
                $this->redirect($msg, 'password/change_password', false);
            }
        }
        //admin passwod ends
    }
    function post_change_user_password()
    {
        $user_type = $this->LOG_USER_TYPE;
        $user_id = $this->LOG_USER_ID;
        $user_name = $this->LOG_USER_NAME;
        if ($user_type == 'employee') {
            $user_id = $this->validation_model->getAdminId();
            $tab2 = ' active';
            $tab1 = '';
        } else {
            $tab1 = ' active';
            $tab2 = '';
        }
        $table_prefix = $this->password_model->table_prefix;
        $user_ref_id = str_replace('_', '', $table_prefix);
        $preset_demo = 'no';
        //user password in admin
        if ($this->input->post('change_pass_button_common') && $this->validate_change_password_change_pass_common()) {
            $post_arr = $this->input->post(null, true);
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);

            $name_user          = $post_arr['user_name_common'];
            $id_user            = $this->validation_model->userNameToID($name_user);
            $new_pwd_user       = $post_arr['new_pwd_common'];
            $new_pwd_user_md5   = password_hash($new_pwd_user, PASSWORD_DEFAULT);

            if ($preset_demo == 'yes' && (($name_user == 'INF750391') || ($name_user == 'INF823741') || ($name_user == 'INF792691') || ($name_user == 'INF793566') || ($name_user == 'INF867749'))) {
                $msg = lang('this_option_is_not_available_for_preset_users');
                $this->redirect($msg, 'password/change_password', false);
            }
            if (DEMO_STATUS == 'yes') {
                $preset_user = $this->validation_model->getPresetUser($user_id);
                if ($preset_user == $name_user) {
                    $msg = 'You can\'t change preset user password';
                    $this->redirect($msg, "password/change_password", false);
                }
            }
            $update = $this->password_model->updatePassword($new_pwd_user_md5, $id_user);
            if ($update) {
                if ($this->MODULE_STATUS['opencart_status_demo'] == "yes" && $this->MODULE_STATUS['opencart_status'] == "yes") {
                    $customer_id = $this->validation_model->getOcCustomerId($id_user);
                    $this->password_model->updateStorePassword($new_pwd_user, $customer_id);
                }
                $this->validation_model->updateForceLogout($id_user, 1);
                $send_details = array();
                $type = 'change_password';
                $email = $this->validation_model->getUserEmailId($id_user);
                $send_details['full_name'] = $this->validation_model->getUserFullName($id_user);
                $send_details['new_password'] = $new_pwd_user;
                $send_details['email'] = $email;
                $send_details['first_name'] = $this->validation_model->getUserData($user_id, "user_detail_name");
                $send_details['last_name'] = $this->validation_model->getUserData($user_id, "user_detail_second_name");

                $result = $this->mail_model->sendAllEmails($type, $send_details);

                // send sms
                if($this->MODULE_STATUS["sms_status"] == "yes") {
                    $this->load->model("sms_model");
                    $mobile = $this->validation_model->getUserPhoneNumber($id_user);
                    $variableArray = [
                        "fullname" => $send_details['full_name'],
                        "company_name" => $this->COMPANY_NAME,
                        "new_password" => $new_pwd_user
                    ];
                    $langId = $this->validation_model->getUserDefaultLanguage($id_user);
                    $this->sms_model->createAndSendSMS($langId, $type, $mobile, $variableArray);
                }
                // end::send sms

                $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'password change', $this->LOG_USER_ID);

                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'change_password', 'Password Changed');
                }
                //

                $msg = lang('password_updated_successfully');
                $this->redirect($msg, 'password/change_password', true);
            } else {
                $msg = lang('error_on_password_updation');
                $this->redirect($msg, 'password/change_password', false);
            }
            //user password in admin end
        }
    }
    function getUsersList($user_name = "")
    {
       // $letters = preg_replace("/[^a-z0-9 ]/si", "", $user_name);
        $user_detail = $this->password_model->getUsersList($user_name);
        echo $user_detail;
    }
    function valid_user($user_name)
    {
        $user_id = $this->validation_model->userNameToID($user_name);
        if (!$user_id) {
            $this->form_validation->set_message('valid_user', lang('invalid_username'));
            return false;
        }
        return true;
    }
}
