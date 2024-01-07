<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once 'Inf_Controller.php';

class Login extends Inf_Controller {

    function __construct() {
        parent::__construct();
         $this->load->model('configuration_model');
    }

    function index($url_user_name = "") {
        $title = lang('login');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "login";
        $this->set("help_link", $help_link);

        $this->load_langauge_scripts();

        $is_logged_in = $this->checkSession();
        if ($is_logged_in) {
            $this->redirect("", 'home', true);
        }
        if ($this->session->userdata('inf_user_invalid_count')) {
            if ($this->session->userdata('inf_user_invalid_count') >= 3) {
                $this->CAPTCHA_STATUS = 'yes';
            }
        }

        $url_user_name_decode = urldecode($url_user_name);
        $url_user_name_decode = str_replace("_", "/", $url_user_name_decode);
        $user_user_name = $this->encryption->decrypt($url_user_name_decode);

        $isvalid = $this->login_model->isUsernameValid($user_user_name);
        if (!$isvalid) {
            $url_user_name = $user_user_name = '';
        }

        $this->set('url_user_name', $user_user_name);
        $this->set('CAPTCHA_STATUS', $this->CAPTCHA_STATUS);

        $login_language_id  = $this->session->userdata("inf_language") ? $this->session->userdata("inf_language")['lang_id'] : 1;
         $lang_arr = $this->configuration_model->getLanguages();

        if($this->session->userdata("inf_language")) {
            $this->lang->load('common', $this->session->userdata("inf_language")['lang_name_in_english']);
            $this->lang->load($this->CURRENT_CTRL, $this->session->userdata("inf_language")['lang_name_in_english']);
        }

        $this->set('selected_language_id', $login_language_id);
        $this->set('lang_arr', $lang_arr);

        $this->setView();
    }

    function verifylogin() {
        $path = '';
        $module_status = [];

        $u_user_name = $this->input->post('user_username', TRUE);
        $captcha_user = $this->session->userdata('inf_captcha_user');
        $module_status = $this->inf_model->trackModule();
        
        if ($this->session->userdata('inf_user_invalid_count')) {
            $invalid_count = $this->session->userdata('inf_user_invalid_count');
        } else {
            $invalid_count = 0;
        }

        $captcha_status = $this->session->userdata('inf_user_invalid_count');
        $user_name_encode = $this->encryption->encrypt($u_user_name);
        $user_name_encode = str_replace("/", "_", $user_name_encode);
        $user_name_encode = urlencode($user_name_encode);

        if (($this->MAINTENANCE_MODE || $this->BLOCK_LOGIN ) && ($u_user_name != $this->ADMIN_USER_NAME)) {
            $this->redirect(lang('you_can`t_login_system'), "login/index/$user_name_encode", false);
        }

        $this->load->model('register_model');

        $email_verification = $this->configuration_model->getEmailVerificationStatus();
        $is_ft_user = $this->register_model->isftUser($u_user_name);
        if($is_ft_user == 0){
            $is_valid = $this->register_model->isvalidusername($u_user_name);
            if($email_verification == 'yes' && $is_valid){   

               $user_email_verification_status = $this->register_model->getUserEmailVerificationStatus($u_user_name);

               if($user_email_verification_status == 'no'){

                $user_mail_id = $this->get_starred($this->register_model->getMailidOfUser($u_user_name));
                $msg = "Your E-mail id is not verified, Click Resend to your mail id : {$user_mail_id}";

                $msg  .= " <a href =" . BASE_URL. "/login/resend/?user_name={$u_user_name} style= 'color:blue'>Resend</a>";
                $this->redirect($msg , 'login' , FALSE);

               }
            }
        }
        
        if ($captcha_status >= 3 && $module_status['captcha_status'] == 'yes') {
            if ((empty($captcha_user) || trim(strtolower($_REQUEST['captcha_user'])) != $captcha_user)) {
                $captcha_message = $this->lang->line('invalid_captcha');
                $this->redirect("$captcha_message", "login/index/$user_name_encode", false);
            }
        }

        $this->form_validation->set_rules('user_username', lang('user_name'), 'trim|required|strip_tags|min_length[3]|max_length[30]|htmlentities|callback_check_charaters');
        $this->form_validation->set_rules('user_password', lang('password'), 'trim|required|strip_tags|min_length[5]|max_length[30]|callback_check_database');
        $login_res = $this->form_validation->run();
        $bit_status = $this->validation_model->checkBitcoinStatus();
        $auth_status = $this->validation_model->getAuthStatus();
        
        if ($login_res) {
            $user_name = $this->input->post('user_username', TRUE);
            $user_id = $this->validation_model->userNameToID($user_name);
            $this->validation_model->updateForceLogout($user_id, 0);
            // if ($bit_status == 'yes' && $this->configuration_model->getPaymentStatus('Payment Gateway')=='yes' && $auth_status == 'yes') {
            if ($auth_status == 'yes') {
                $user_name = $this->input->post('user_username', TRUE);
                $password = $this->input->post('user_password', TRUE);
                $login = $this->validation_model->loginForQr($user_name, $password, null);
                require_once dirname(FCPATH) . '/vendor/2fa/TwoFactorAuth.php';
                $tfa = new TwoFactorAuth($user_name, 6, 30);
                if ($this->session->userdata('last_logged_user')) {
                    if ($this->session->userdata('last_logged_user') != $user_name) {
                        $this->session->unset_userdata('show_qr_code');
                        $this->session->unset_userdata('auth_key');
                    }
                }
                if (!$this->session->userdata('show_qr_code')) {
                    $this->session->set_userdata('show_qr_code', 'show');
                }
                   
                $user_id = $this->validation_model->userNameToID($user_name);                
                $goc_key = $this->validation_model->getGocKey($user_id);
                if (!empty($goc_key)) {
                    $secret_key = $goc_key;
                } else {
                    $secret_key = $tfa->createSecret();
                }
                $qr_code_image = $tfa->getQRCodeImageAsDataUri($user_name, $secret_key);
                $this->session->set_userdata('auth_key', $secret_key);
                $this->session->set_flashdata('auth_qr_code', $qr_code_image);
                $this->session->set_flashdata('inf_pre_login', json_encode($login));
                $this->redirect('', "login/one_time_password", true);
            } else {
            $this->session->unset_userdata('inf_captcha_user');
            $this->session->unset_userdata('inf_user_invalid_count');
            if ($this->session->userdata("redirect_url")) {
                $redirect_url = $this->session->userdata("redirect_url");
                $this->session->unset_userdata("redirect_url");
                if (strcmp($redirect_url, "register/") >= 0) {
                    $this->redirect("", $redirect_url, true);
                } else {
                    $this->redirect("", $redirect_url, true);
                }
            } else {
                $this->redirect("", "home", true);
            }
        }
        } else {
            $invalid_count++;
            $this->session->set_userdata('inf_user_invalid_count', $invalid_count);
            $u_user_name = $this->input->post('user_username', TRUE);
             if (!$this->check_charaters($u_user_name))
                 $path = "login/index";
            $valid = $this->login_model->isUsernameValid($u_user_name);
            if ($valid) {
                $path = "login/index/$user_name_encode";
            }

            $msg = $this->lang->line('invalid_user_name_or_password');
            $this->redirect("$msg", "$path", false);
        }
    }

    function check_database($password) {
        $flag = false;

        $login_details = $this->input->post(NULL, TRUE);
        $login_details = $this->validation_model->stripTagsPostArray($login_details);

        $username = $login_details['user_username'];

        $login_result = $this->login_model->login($username, $password);
if (!$this->check_charaters($username)) {
                return $flag;
            }   
        if ($login_result) {        
            $bit_status = $this->validation_model->checkBitcoinStatus();
            $auth_status = $this->validation_model->getAuthStatus();
            $payment_gateway_status = $this->configuration_model->getPaymentStatus('Payment Gateway');
            if($auth_status == "no" && (($this->login_model->checkUserGoogleAuthStatus($username) == "no") || $login_result[0]->user_type == 'Unapproved')) {
                $this->login_model->setUserSessionDatas($login_result);
            }
            $flag = true;
        } else {
            $flag = false;
        }
        return $flag;
    }

    function login_employee($url_user_name = '') {
        $title = $this->lang->line('login');
        $this->set("title", $this->COMPANY_NAME . " | $title");
        $is_logged_in = $this->checkSession();
        // dd($is_logged_in);
        if ($is_logged_in) {
            $this->redirect("", 'home', true);
        }

        $this->load_langauge_scripts();

        $url_user_name_decode = urldecode($url_user_name);
        $url_user_name_decode = str_replace("_", "/", $url_user_name_decode);
        $user_user_name = $this->encryption->decrypt($url_user_name_decode);

        if (!$this->login_model->isValidEmployee($user_user_name)) {
            $user_user_name = '';
        }

        $this->set("employee_username", $user_user_name);

        $login_language_id  = $this->session->userdata("inf_language") ? $this->session->userdata("inf_language")['lang_id'] : 1;
         $lang_arr = $this->configuration_model->getLanguages();

        if($this->session->userdata("inf_language")) {
            $this->lang->load('common', $this->session->userdata("inf_language")['lang_name_in_english']);
            $this->lang->load($this->CURRENT_CTRL, $this->session->userdata("inf_language")['lang_name_in_english']);
        }

        $this->set('selected_language_id', $login_language_id);
        $this->set('lang_arr', $lang_arr);

        $this->setView();
    }

    function verify_employee_login() {
        $login_details = $this->input->post(NULL, TRUE);
        $login_details = $this->validation_model->stripTagsPostArray($login_details);

        $employee_username = trim($login_details['user_username']);

        $this->form_validation->set_rules('user_username', lang('user_name'), 'trim|required|strip_tags|min_length[3]|max_length[30]|htmlentities|callback_check_charaters');
        $this->form_validation->set_rules('user_password', lang('password'), 'trim|required|strip_tags|min_length[5]|max_length[30]|callback_check_database_employee');
        $login_res = $this->form_validation->run();
        $bit_status = $this->validation_model->checkBitcoinStatus();
        $auth_status = $this->validation_model->getAuthStatus();

        if ($login_res) {
            
            if ($auth_status == 'yes') {
                $user_name = $this->input->post('user_username', TRUE);
                $password = $this->input->post('user_password', TRUE);
                $login = $this->validation_model->loginForQr($user_name, $password, $login_type='employee');
                require_once dirname(FCPATH) . '/vendor/2fa/TwoFactorAuth.php';
                $tfa = new TwoFactorAuth($user_name, 6, 30);
                if ($this->session->userdata('last_logged_user')) {
                    if ($this->session->userdata('last_logged_user') != $user_name) {
                        $this->session->unset_userdata('show_qr_code');
                        $this->session->unset_userdata('auth_key');
                    }
                }
                if (!$this->session->userdata('show_qr_code')) {
                    $this->session->set_userdata('show_qr_code', 'show');
                }
                    
                $user_id = $this->validation_model->employeeUserNameToID($user_name);                
                $goc_key = $this->validation_model->getEmployeeGocKey($user_id);
                if (!empty($goc_key)) {
                    $secret_key = $goc_key;
                } else {
                    $secret_key = $tfa->createSecret();
                }
                $qr_code_image = $tfa->getQRCodeImageAsDataUri($user_name, $secret_key);
                $this->session->set_userdata('auth_key', $secret_key);
                $this->session->set_flashdata('auth_qr_code', $qr_code_image);
                $this->session->set_flashdata('inf_pre_login', json_encode($login));
                $this->redirect('', "login/one_time_password", true);
            } else {
                $this->load->model('employee_model');
                $user_id = $this->validation_model->employeeNameToID($this->input->post('user_username'));
                $redirect_page = $this->employee_model->getRedirectPageOnLogin($user_id);
                $this->validation_model->insertEmployeeActivity($user_id, $user_id, 'login', 'logged in');
                $this->redirect("", $redirect_page, true);
            }
        } else {
            $employee_username = urlencode(str_replace("/", "_", $employee_username));
           if (!$this->check_charaters($employee_username))
                $path = "login/login_employee";
            else          
            $path = "login/login_employee/$employee_username";
            $msg = $this->lang->line('invalid_user_name_or_password');
            $this->redirect("$msg", "$path", false);
        }
    }

    function check_database_employee($password) {
        $flag = false;

        $login_details = $this->input->post(NULL, TRUE);
        $login_details = $this->validation_model->stripTagsPostArray($login_details);

        $username = $login_details['user_username'];
	if (!$this->check_charaters($username)) {
                return $flag;
            }
        $login_result = $this->login_model->login_employee($username, $password);
        if ($login_result) {
            $bit_status = $this->validation_model->checkBitcoinStatus();
            $payment_gateway_status = $this->configuration_model->getPaymentStatus('Payment Gateway');
            $auth_status = $this->validation_model->getAuthStatus();
            if ($bit_status == 'no' || $payment_gateway_status != 'yes' || $auth_status == 'no') {
                $this->login_model->setUserSessionDatasEmployee($login_result);
            }
            $flag = true;
        } else {
            $flag = false;
        }
        return $flag;
    }

    function logout() {
        $user_name_encode = '';
        $user_type = '';

        if ($this->checkSession()) {
            $user_name = $this->LOG_USER_NAME;
            $user_id = $this->LOG_USER_ID;
            $user_type = $this->LOG_USER_TYPE;

            $user_name_encode = $this->encryption->encrypt($user_name);
            $user_name_encode = str_replace("/", "_", $user_name_encode);
            $user_name_encode = urlencode($user_name_encode);
            if ($this->LOG_USER_TYPE == 'employee') {
                $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'logout', 'logged out');
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'logged out', $this->LOG_USER_ID, $data = '', $user_type);
            } else {
                if ($user_id) {
                    $this->validation_model->insertUserActivity($user_id, 'Logout', $user_id, $data = '', $user_type);
                }
            }
        }
        foreach ($this->session->userdata as $key => $value) {
            if (strpos($key, 'inf_') === 0) {
                $this->session->unset_userdata($key);
            }
        }

        if ($this->MODULE_STATUS['opencart_status_demo'] == "yes" && $this->MODULE_STATUS['opencart_status'] == "yes") {
            $this->session->unset_userdata('customer_id');
            $this->unset_store_session_data();
        }

        $path = "login";
        if ($user_type == 'employee') {
            $path = "login/login_employee/$user_name_encode";
        } else {
            $path = "login/index/$user_name_encode";
        }

        $msg = $this->lang->line('successfully_logged_out');
        $this->redirect("$msg", $path, true);
    }

    function auto_logout() {
        $user_name_encode = '';
        $user_type = '';

        if ($this->checkSession()) {
            $user_name = $this->LOG_USER_NAME;
            $user_id = $this->LOG_USER_ID;
            $user_type = $this->LOG_USER_TYPE;

            $user_name_encode = $this->encryption->encrypt($user_name);
            $user_name_encode = str_replace("/", "_", $user_name_encode);
            $user_name_encode = urlencode($user_name_encode);
        }
        foreach ($this->session->userdata as $key => $value) {
            if (strpos($key, 'inf_') === 0) {
                $this->session->unset_userdata($key);
            }
        }

        if ($this->MODULE_STATUS['opencart_status_demo'] == "yes" && $this->MODULE_STATUS['opencart_status'] == "yes") {
            $this->session->unset_userdata('customer_id');
            $this->unset_store_session_data();
        }
        if($user_type == 'employee'){
             $path = "login/login_employee";
        }
        else {
            $path = "login";
            if ($user_name_encode) {
                $path .= "/lock_screen/$user_name_encode";
            }
        }

        $msg = $this->lang->line('successfully_logged_out');
        $this->redirect("$msg", $path, true);
    }

    function lock_screen($url_user_name = "") {
        $title = lang('auto_login');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "login";
        $this->set("help_link", $help_link);

        $is_logged_in = $this->checkSession();
        if ($is_logged_in) {
            $this->redirect("", 'home', true);
        }

        $this->load_langauge_scripts();

        $url_user_name_decode = urldecode($url_user_name);
        $url_user_name_decode = str_replace("_", "/", $url_user_name_decode);
        $user_user_name = $this->encryption->decrypt($url_user_name_decode);

        $user_photo = 'nophoto.jpg';
        $isvalid = $this->login_model->isUsernameValid($user_user_name);
        if (!$isvalid) {
            $this->redirect('', 'login', false);
        } else {
            $user_id = $this->validation_model->userNameToID($url_user_name);
            $user_photo = $this->validation_model->getProfilePicture($user_id);
        }

        $this->set('user_user_name', $user_user_name);
        $this->set('user_photo', $user_photo);
        $this->setView();
    }

    function validate_lock_screen() {

        if ($this->input->post('user_type') == 'employee') {
            $this->form_validation->set_rules('user_password', lang('password'), 'trim|required|strip_tags|min_length[6]|max_length[30]|callback_check_database_employee');
        } else {
            $this->form_validation->set_rules('user_password', lang('password'), 'trim|required|strip_tags|min_length[6]|max_length[30]|callback_check_database');
        }
        $login_res = $this->form_validation->run();
        if ($login_res) {
            $this->redirect("", "home", true);
        } else {
            $login_details = $this->input->post(NULL, TRUE);
            $login_details = $this->validation_model->stripTagsPostArray($login_details);
            $user_name = trim($login_details['user_username']);

            $user_name_encode = $this->encryption->encrypt($user_name);
            $user_name_encode = str_replace("/", "_", $user_name_encode);
            $user_name_encode = urlencode($user_name_encode);

            $path = "login/lock_screen/$user_name_encode";
            $msg = $this->lang->line('invalid_password');
            $this->redirect($msg, "$path", false);
        }
    }

    function forgot_password() {
        if ($this->checkSession()) {
            $this->redirect("", 'home', true);
        }
        $this->set("title", $this->COMPANY_NAME . " | " . lang('forgot_password'));

        $this->load_langauge_scripts();

        if ($this->input->post("forgot_password_submit") && $this->validate_forgot_password()) {
            $user_name = $this->input->post("user_name", TRUE);
            $captcha = $this->session->userdata('inf_captcha');
            if ((empty($captcha) || trim(strtolower($_REQUEST['captcha'])) != $captcha)) {
                $captcha_message = lang("invalid_captcha");
                $this->redirect("$captcha_message", "login/forgot_password", false);
            } $user_id = $this->validation_model->userNameToID($user_name);
            $e_mail = $this->input->post("e_mail", TRUE);

            $check_result = $this->validation_model->checkEmail($user_id, $e_mail);
            if ($check_result) {
                $this->validation_model->sendEmail($user_id, $e_mail);

                $msg = $this->lang->line('your_request_has_been_accepted_we_will_send_you_confirmation_mail_please_follow_that_instruction');
                $this->redirect("$msg", "login", TRUE);
            } else {
                $msg = $this->lang->line('invalid_username_or_email');
                $this->redirect("$msg", "login/forgot_password", FALSE);
            }
        }

        $login_language_id  = $this->session->userdata("inf_language") ? $this->session->userdata("inf_language")['lang_id'] : 1;
         $lang_arr = $this->configuration_model->getLanguages();

        if($this->session->userdata("inf_language")) {
            $this->lang->load('common', $this->session->userdata("inf_language")['lang_name_in_english']);
            $this->lang->load($this->CURRENT_CTRL, $this->session->userdata("inf_language")['lang_name_in_english']);
        }

        $this->set('selected_language_id', $login_language_id);
        $this->set('lang_arr', $lang_arr);

        $this->setView();
    }

    function validate_forgot_password() {
        $this->form_validation->set_rules('user_name', lang('user_name'), 'trim|required|strip_tags');
        $this->form_validation->set_rules('e_mail', lang('email'), 'trim|required|strip_tags|valid_email');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function reset_password($resetkey = "") {
        if ($this->checkSession()) {
            $this->redirect("", 'home', true);
        }
        $this->set("title", $this->COMPANY_NAME . " | Reset Password");

        $this->load_langauge_scripts();
        $resetkey_original = $resetkey;
        $resetkey = str_replace(["~", ".", "-"], ["/", "+", "="], $resetkey);
        $resetkey = $this->encryption->decrypt($resetkey);

        if ($this->input->post("reset_password_submit") && $this->validate_reset_password()) {
            $user_name = $this->input->post("user_name", TRUE);
            $key = $this->input->post("key", TRUE);
            $captcha = $this->session->userdata('inf_captcha');
            if ((empty($captcha) || trim(strtolower($_REQUEST['captcha'])) != $captcha)) {
                $captcha_message = $this->lang->line('invalid_captcha');
                $this->redirect("$captcha_message", "login/reset_password/$resetkey_original", false);
            }
            $user_id = $this->validation_model->userNameToID($user_name);

            $pass_word = $this->input->post("pass", TRUE);
            $confirm_pass = $this->input->post("confirm_pass", TRUE);
            if ($pass_word == $confirm_pass) {
                $res = $this->validation_model->updatePasswordOut($user_id, $pass_word, $key);
                if ($res) {
                    $msg = $this->lang->line('password_updated_successfully');
                    $this->redirect("$msg", "login", true);
                } else {
                    $msg = $this->lang->line('error_on_reset_password');
                    $this->redirect("$msg", "login", FALSE);
                }
            }
        }
        else {
            $user_name = NULL;
            $id = NULL;
            if ($resetkey != "") {
                $user_arr = $this->validation_model->getUserDetailFromKey($resetkey);
                $id = $user_arr[0];
                if ($id == "") {
                    $msg = $this->lang->line('invalid_url');
                    $this->redirect("$msg", "login", FALSE);
                }
                $user_name = $user_arr[1];
            } else {
                $msg = $this->lang->line('invalid_url');
                $this->redirect("$msg", "login", FALSE);
            }
        }
        $this->set("user_id", $id);
        $this->set("key", $resetkey_original);
        $this->set("user_name", $user_name);

        $login_language_id  = $this->session->userdata("inf_language") ? $this->session->userdata("inf_language")['lang_id'] : 1;
         $lang_arr = $this->configuration_model->getLanguages();

        if($this->session->userdata("inf_language")) {
            $this->lang->load('common', $this->session->userdata("inf_language")['lang_name_in_english']);
            $this->lang->load($this->CURRENT_CTRL, $this->session->userdata("inf_language")['lang_name_in_english']);
        }

        $this->set('selected_language_id', $login_language_id);
        $this->set('lang_arr', $lang_arr);

        $this->setView();
    }

    function validate_reset_password() {
        $valid = '';
        $path = '';

        $login_details = $this->input->post(NULL, TRUE);
        $login_details = $this->validation_model->stripTagsPostArray($login_details);

        if(isset($login_details['admin_username'])  && isset($login_details['user_username'])){
            $admin_name = trim($login_details['admin_username']);
            $u_user_name = trim($login_details['user_username']);
        }

        $this->form_validation->set_rules('pass', lang('password'), 'trim|required|strip_tags|min_length[6]');
        $this->form_validation->set_rules('confirm_pass', lang('confirm_password'), 'trim|required|strip_tags|matches[pass]');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }
    
     function check_charaters($user_name) {
        if (preg_match('/[^.-a-z0-9 _]+/i', $user_name)) {
            return false;
        } else {
            return true;
        }
    }

    public function get_user_type()
    {
        if($this->input->is_ajax_request()) {
            header("Content-Type: text/plain");
            $user_type = null;
            if($this->session->has_userdata('inf_logged_in')) {
                $user_type = $this->session->userdata('inf_logged_in')['user_type'];
                if ($user_type == 'employee') {
                    $user_type = 'admin';
                }
            }
            echo $user_type;
            exit();
        }
    }
    
    public function one_time_password() {
        if ($this->session->flashdata('auth_qr_code') && $this->session->userdata('auth_key') && $this->session->flashdata('inf_pre_login')) {
            $this->session->keep_flashdata('inf_pre_login');
            $login_details = $this->session->flashdata('inf_pre_login');
            $this->session->set_userdata('login_details', $login_details);
        } else { 
            $this->redirect('An error occured. Please try again!', "login/index", false);
        }

        $title = lang('enter_otp');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "login";
        $this->set("help_link", $help_link);
        $goc_status = 'not-verified';

        $this->set('qr_code', $this->session->flashdata('auth_qr_code'));
        $this->set('show_qr_code', $this->session->userdata('show_qr_code'));
        $this->load_langauge_scripts();
        $login_details = json_decode($this->session->flashdata('inf_pre_login'));
        $user_id = $login_details[0]->id;
        $goc_key = $this->validation_model->getGocKey($user_id);
        if(!empty($goc_key)){
            $goc_status = 'verified';            
        }
        $secret_key = $this->session->userdata('auth_key');
        $this->set("secret_key", $secret_key);
        $this->set("goc_status", $goc_status);

        $login_language_id  = $this->session->userdata("inf_language") ? $this->session->userdata("inf_language")['lang_id'] : 1;
         $lang_arr = $this->configuration_model->getLanguages();

        if($this->session->userdata("inf_language")) {
            $this->lang->load('common', $this->session->userdata("inf_language")['lang_name_in_english']);
            $this->lang->load($this->CURRENT_CTRL, $this->session->userdata("inf_language")['lang_name_in_english']);
        }

        $this->set('selected_language_id', $login_language_id);
        $this->set('lang_arr', $lang_arr);

        $this->setView();
    }

    public function verify_one_time_password() {
        $is_logged_in = $this->checkSession();
        if ($is_logged_in) {      
            $this->redirect("", 'home', true);
        }
        if ($this->session->userdata('auth_key') && $this->session->userdata('login_details')) {
            
        } else {
            $this->redirect('An error occured. Please try again!', "login/index", false);
        }

        if ($this->input->post('verify')) {
            $login_details = json_decode($this->session->userdata('login_details'));
            $user_id = $login_details[0]->id;
            $one_time_password = $this->input->post('one_time_password', TRUE);

            require_once dirname(FCPATH) . '/vendor/2fa/TwoFactorAuth.php';
            $tfa = new TwoFactorAuth($login_details[0]->user_name, 6, 30);
            $result = $tfa->verifyCode($this->session->userdata('auth_key'), $one_time_password);
            if ($result === true) {
                $this->validation_model->setGocKey($user_id, $this->session->userdata('auth_key'));
                $this->session->set_userdata('show_qr_code', 'hidden');
                $this->login_model->setUserSessionDatas($login_details);
                $user_name = $login_details[0]->user_name;
                $this->session->set_userdata('last_logged_user', $user_name);
                $password = $login_details[0]->password;
                $this->session->set_userdata('password', $password);
                $this->session->unset_userdata('inf_captcha_user');
                $this->session->unset_userdata('inf_user_invalid_count');
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'login', 'logged in');
                }
                if ($this->session->userdata("redirect_url")) {
                    $redirect_url = $this->session->userdata("redirect_url");
                    $this->session->unset_userdata("redirect_url");
                    if (strcmp($redirect_url, "register/") >= 0) {
                        $this->redirect("", $redirect_url, true);
                    } else {
                        $this->redirect("", $redirect_url, true);
                    }
                } else {
                    $this->redirect("", "home", true);
                }
                $this->redirect("", 'home', true);
            } else {
                $msg = 'Invalid OTP. Please try again!';
                $this->redirect($msg, "login/index", false);
            }
        } else {
            $msg = 'An error occured. Please try again!';
            $this->redirect($msg, "login/index", false);
        }
    }
    
        public function backup_authentication() {

        if ($this->session->userdata('auth_key') && $this->session->userdata('login_details')) {
            
        } else {
            $this->redirect('An error occured. Please try again!', "login/index", false);
        }

        $title = lang('enter_authentication_key');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "login";
        $this->set("help_link", $help_link);
        $goc_status = 'not-verified';

        $this->set('qr_code', $this->session->flashdata('auth_qr_code'));
        $this->set('show_qr_code', $this->session->userdata('show_qr_code'));
        $this->load_langauge_scripts();

        $login_details = json_decode($this->session->userdata('login_details'));
        $user_id = $login_details[0]->id;
        $goc_key = $this->validation_model->getGocKey($user_id);
        if(!empty($goc_key)){
            $goc_status = 'verified';            
        }

        $secret_key = $this->session->userdata('auth_key');

        $this->set("secret_key", $secret_key);
        $this->set("goc_status", $goc_status);

        $login_language_id  = $this->session->userdata("inf_language") ? $this->session->userdata("inf_language")['lang_id'] : 1;
         $lang_arr = $this->configuration_model->getLanguages();

        if($this->session->userdata("inf_language")) {
            $this->lang->load('common', $this->session->userdata("inf_language")['lang_name_in_english']);
            $this->lang->load($this->CURRENT_CTRL, $this->session->userdata("inf_language")['lang_name_in_english']);
        }

        $this->set('selected_language_id', $login_language_id);
        $this->set('lang_arr', $lang_arr);

        $this->setView();
    }

    public function verify_backup_key() {

        $is_logged_in = $this->checkSession();
        if ($is_logged_in) {
            $this->redirect("", 'home', true);
        }

        if ($this->input->post('verify')) {
            $login_details = $this->session->userdata('login_details');
            $user_id = $login_details[0]->id;
            $auth_key = $this->input->post('one_time_password', TRUE);
            $secret_key = $this->session->userdata('auth_key');
            if ($secret_key == $auth_key) {
                $this->session->set_userdata('show_qr_code', 'hidden');
                $this->login_model->setUserSessionDatas($login_details);
                $user_name = $login_details[0]->user_name;
                $this->session->set_userdata('last_logged_user', $user_name);
                $password = $login_details[0]->password;
                $this->session->set_userdata('password', $password);
                $this->session->unset_userdata('inf_captcha_user');
                $this->session->unset_userdata('inf_user_invalid_count');
                if ($this->session->userdata("redirect_url")) {
                    $redirect_url = $this->session->userdata("redirect_url");
                    $this->session->unset_userdata("redirect_url");
                    if (strcmp($redirect_url, "register/") >= 0) {
                        $this->redirect("", $redirect_url, true);
                    } else {
                        $this->redirect("", $redirect_url, true);
                    }
                } else {
                    $this->redirect("", "home", true);
                }
                $this->redirect("", 'home', true);
            } else {
                $msg = 'Invalid OTP. Please try again!';
                $this->redirect($msg, "login/index", false);
            }
        } else {
            $msg = 'An error occured. Please try again!';
            $this->redirect($msg, "login/index", false);
        }
    }

    function reset_tran_password($resetkey = "") {
        $this->load->model('validation_model');
        $this->set("title", $this->COMPANY_NAME . " | Reset Transaction Password");
        $this->load->model('tran_pass_model');  

        $this->load_langauge_scripts();
        $resetkey_original = $resetkey;
        $admin_user_name = $this->uri->segments[4];
        $resetkey = str_replace(["~", ".", "-"], ["/", "+", "="], $resetkey);
        $resetkey = $this->encryption->decrypt($resetkey);

        if ($this->input->post("reset_password_submit") && $this->validate_reset_transaction_password()) {
            $user_name = $this->input->post("user_name", TRUE);
            $captcha = $this->session->userdata('inf_captcha');
            if ((empty($captcha) || trim(strtolower($_REQUEST['captcha'])) != $captcha)) {
                $captcha_message = $this->lang->line('invalid_captcha');
                $this->redirect("$captcha_message", "login/reset_tran_password/$resetkey_original/$admin_user_name", false);
            }
            $prefix = str_replace('_', '', $this->db->dbprefix);
            $user_id = $this->validation_model->UserNameToIdWitoutLogin($user_name,$prefix);
            $pass_word = $this->input->post("pass", TRUE);
            $confirm_pass = $this->input->post("confirm_pass", TRUE);
            if ($pass_word == $confirm_pass) {
                $res = $this->tran_pass_model->updatePasswordOut($user_id, $pass_word, $resetkey,$prefix);
                if ($res) {
                    $msg = $this->lang->line('tran_password_updated_successfully');
                    if($this->LOG_USER_ID) {
                        $this->redirect("$msg", "home", true);
                    }
                    $this->redirect("$msg", "login", true);
                } else {
                    $msg = $this->lang->line('error_on_reset_tran_password');
                    $this->redirect("$msg", "login/reset_tran_password/$resetkey_original/$admin_user_name", FALSE);
                }
            }
        }
        else {
            $user_name = NULL;
            $id = NULL;
            if ($resetkey != "") {
                $prefix = str_replace('_', '', $this->db->dbprefix);
                $user_arr = $this->tran_pass_model->getUserDetailFromKey($resetkey,$prefix);
                $id = $user_arr[0];
                if ($id == "") {
                    $msg = $this->lang->line('invalid_url');
                    $this->redirect("$msg", "login", FALSE);
                }
                $user_name = $user_arr[1];
            } else {
                $msg = $this->lang->line('invalid_url');
                $this->redirect("$msg", "login", FALSE);
            }
        }
        $this->set("user_id", $id);
        $this->set("key", $resetkey_original);
        $this->set("user_name", $user_name);
        $this->SESS_STATUS = FALSE;

        $login_language_id  = $this->session->userdata("inf_language") ? $this->session->userdata("inf_language")['lang_id'] : 1;
         $lang_arr = $this->configuration_model->getLanguages();

        if($this->session->userdata("inf_language")) {
            $this->lang->load('common', $this->session->userdata("inf_language")['lang_name_in_english']);
            $this->lang->load($this->CURRENT_CTRL, $this->session->userdata("inf_language")['lang_name_in_english']);
        }

        $this->set('selected_language_id', $login_language_id);
        $this->set('lang_arr', $lang_arr);

        $this->setView();
    }
    public function validate_reset_transaction_password() {
        $valid = '';
        $path = '';

        $login_details = $this->input->post(NULL, TRUE);
        $login_details = $this->validation_model->stripTagsPostArray($login_details);
        if(isset($login_details['admin_username'])  && isset($login_details['user_username'])){
        $admin_name = trim($login_details['admin_username']);
        $u_user_name = trim($login_details['user_username']);
        }

        $this->form_validation->set_rules('pass', lang('password'), 'trim|required|strip_tags|min_length[8]|max_length[32]');
        $this->form_validation->set_rules('confirm_pass', lang('confirm_password'), 'trim|required|strip_tags|matches[pass]|min_length[8]|max_length[32]');
        $this->form_validation->set_rules('captcha', lang('captcha'), 'required');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }
    public function Reset_google_authentication()
    {
        $this->set("title", $this->COMPANY_NAME . " | " . lang('Reset google Authentication'));

        $this->load_langauge_scripts();
        
        if(($this->input->post('google_auth_reset_submit')) && ($this->validate_Reset_google_authentication()))
        {
         $user_name = $this->input->post('user_name');
         $user_id = $this->validation_model->userNameToID($user_name);
         $e_mail = $this->input->post('e_mail');
         $check_result = $this->validation_model->checkEmail($user_id,$e_mail);
         if($check_result)
          {
          $this->validation_model->sendEmailforRestGoogleAuth($user_id, $e_mail);
          $msg = $this->lang->line('your_request_has_been_accepted_we_will_send_you_confirmation_mail_please_follow_that_instruction');
          $this->redirect("$msg", "login", true);    
          }
          else
          {
          $msg = $this->lang->line('invalid_username_or_email');
          $this->redirect("$msg", "login", false);    
          }    
        }    

        $login_language_id  = $this->session->userdata("inf_language") ? $this->session->userdata("inf_language")['lang_id'] : 1;
         $lang_arr = $this->configuration_model->getLanguages();

        if($this->session->userdata("inf_language")) {
            $this->lang->load('common', $this->session->userdata("inf_language")['lang_name_in_english']);
            $this->lang->load($this->CURRENT_CTRL, $this->session->userdata("inf_language")['lang_name_in_english']);
        }

        $this->set('selected_language_id', $login_language_id);
        $this->set('lang_arr', $lang_arr);
        
        $this->setView();
    }
    public function one_tme_password_generate($resetkey = "", $random_key = "")
    {
        $curret_date = date('d-m-Y');
        $orginal_reset = $resetkey;
        $orginal_random = $random_key;
        $resetkey = str_replace(["~", ".", "-"], ["/", "+", "="], $resetkey);
        $resetkey = $this->encryption->decrypt($resetkey);
        $random_key = str_replace(["~", ".", "-"], ["/", "+", "="], $random_key);
        $random_key = $this->encryption->decrypt($random_key);
        $check_key = $this->validation_model->checkkeyAvailability($resetkey,$random_key);
        if($check_key == 'yes')
        {
         $msg = 'Invalid url';
         $this->redirect($msg, "login/index", false);
          
        }
        else {
           
        }
        
        $user_name = $this->validation_model->IdToUserName($resetkey);
                require_once dirname(FCPATH) . '/vendor/2fa/TwoFactorAuth.php';
                $tfa = new TwoFactorAuth($user_name, 6, 30);
                if ($this->session->userdata('last_logged_user')) {
                    if ($this->session->userdata('last_logged_user') != $user_name) {
                        $this->session->unset_userdata('show_qr_code');
                        $this->session->unset_userdata('auth_key');
                    }
                }
                if (!$this->session->userdata('show_qr_code')) {
                    $this->session->set_userdata('show_qr_code', 'show');
                }    
        
        
            $secret_key = $tfa->createSecret();    
            $qr_code_image = $tfa->getQRCodeImageAsDataUri($user_name, $secret_key);
            $this->session->set_userdata('auth_key', $secret_key);
            $this->session->set_userdata('user_name', $user_name);
            $this->session->set_flashdata('auth_qr_code', $qr_code_image);

        $title = lang('enter_otp');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "login";
        $this->set("help_link", $help_link);
        $goc_status = 'not-verified';

        $this->set('qr_code', $this->session->flashdata('auth_qr_code'));
        $this->set('show_qr_code', $this->session->userdata('show_qr_code'));
        $this->load_langauge_scripts();
        $user_id = $this->validation_model->userNameToID($this->session->userdata('user_name'));
        $secret_key = $this->session->userdata('auth_key');
        $this->set("secret_key", $secret_key);
        $this->set("resetkey",$orginal_reset);
        $this->set("random_key",$orginal_random);
        $this->set("goc_status", $goc_status);

        $login_language_id  = $this->session->userdata("inf_language") ? $this->session->userdata("inf_language")['lang_id'] : 1;
         $lang_arr = $this->configuration_model->getLanguages();

        if($this->session->userdata("inf_language")) {
            $this->lang->load('common', $this->session->userdata("inf_language")['lang_name_in_english']);
            $this->lang->load($this->CURRENT_CTRL, $this->session->userdata("inf_language")['lang_name_in_english']);
        }

        $this->set('selected_language_id', $login_language_id);
        $this->set('lang_arr', $lang_arr);

        $this->setView();
    }
    public function reset_google_auth_key()
    {
    
        if($this->input->post('verify'))
        {
            $secret_key = $this->session->userdata('auth_key');
            $user_id = $this->validation_model->userNameToID($this->session->userdata('user_name'));
            $result = $this->validation_model->updateGocKey($user_id , $secret_key);
            if($result)
            {
            $resetkey = $this->input->post('reset');
            $resetkey = str_replace(["~", ".", "-"], ["/", "+", "="], $resetkey);
            $resetkey = $this->encryption->decrypt($resetkey);
            
            $random_key = $this->input->post('random');
            $random_key = str_replace(["~", ".", "-"], ["/", "+", "="], $random_key);
            $random_key = $this->encryption->decrypt($random_key);
            
            $result = $this->login_model->updateGoogleAuthkeyStatus($resetkey,$random_key);     
            $msg = 'Google Authentication reseted';
            $this->redirect($msg, "login/index", true);   
            }
            else
            {
            $msg = 'An error occured. Please try again!';
            $this->redirect($msg, "login/index", false);
            }
            
        }
    }
     public function validate_Reset_google_authentication() {
        $this->form_validation->set_rules('user_name', lang('user_name'), 'trim|required|strip_tags');
        $this->form_validation->set_rules('e_mail', lang('email'), 'trim|required|strip_tags|valid_email');
        $validate_form = $this->form_validation->run();
        return $validate_form; 
    }

    /**
     * [valid_email description]
     * @return [type] [description]
     */
    function valid_user_email($request_type = "ajax") {
        if($this->input->post('e_mail') == $this->validation_model->getUserEmailId($this->validation_model->userNameToID($this->input->post('user_name')))) {
            if($request_type == "ajax") {
                echo "yes";
            }
            return true;
        }
        if($request_type == "ajax") {
            echo "no";
        }
        return false;
    }

    public function change_default_language() {
        $language_id = $this->input->post('language');
        $languages = $this->inf_model->getAllLanguages();
        $lang_arr_count = count($languages);
        for ($i = 0; $i < $lang_arr_count; $i++) {
            if ($language_id == $languages[$i]['lang_id']) {
                $this->session->set_userdata("inf_language", array("lang_id" => $languages[$i]['lang_id'], "lang_name_in_english" => $languages[$i]['lang_name_in_english']));
                break;
            }
        }
        echo "yes";
    }
    public function confirm_email(){

       if(!empty($this->uri->segment(3))){  
        
        $keyword = $this->uri->segment(3);

        $user_name =  $keyword;  

        
        $this->load->model('register_model');
        $registration_details = $this->register_model->getEmailRegistrationDetailsByUsername($user_name);


        $reg_query = http_build_query($registration_details);
        
        $date_link_generated = $registration_details['data']['joining_date']; 
        $user_mail = $registration_details['data']['email'];
        
        $user_mail = $this->get_starred($user_mail);

        $expire_date = date('Y-m-d H:i:s', strtotime($date_link_generated . ' + 1 day'));


        $curret_date = date('Y-m-d H:i:s');

        if($curret_date > $expire_date){
        
        $msg = "Your Link has been Expired, Click Resend to your {$user_mail}";

        $msg  .= " <a href =" . BASE_URL. "/login/resend/?user_name={$user_name} style= 'color:blue'>Resend</a>";
        $this->redirect($msg , 'login' , TRUE);

        }

        $payment_method = $registration_details['payment_method'];
        $id = $registration_details['data']["pending_id"] = $registration_details['id'];
            
        $user_id=$this->validation_model->userNameToID($registration_details['data']['placement_user_name']);
            $this->register_model->begin();
        if($this->validation_model->isLegAvailable($user_id, $registration_details['data']['position'], true))
        {
            $registration_details['data']['reg_from_tree'] = true;
        }
        else
        {
            $registration_details['data']['reg_from_tree'] = false;
        }
        $registration_details['data']['user_name_type'] = 'static';
        $registration_details['data']['joining_date'] = date('Y-m-d H:i:s');

        $res = $this->register_model->confirmRegister($registration_details['data'], $this->MODULE_STATUS);
        if (isset($res['status']) && $res['status']) {
                $this->register_model->commit();
                $user_id = $res['user_id'];
                $this->register_model->updateEmailVerificationStatus($user_name);  
                $this->register_model->updatePendingRegistration($id, $user_id, $user_name, $payment_method, $registration_details['data']);
                if($payment_method=='ewallet'){
                    $ewallet_id=$this->register_model->getEwalletIdfromPending($id);
                    if($ewallet_id)
                        $this->register_model->updateEwalletDetails($ewallet_id, $user_id);
                }
                $result = true;
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'user_register', 'User Registered', $data = '');
                }
        } else {
                $this->register_model->rollback();
                $msg = lang('error_approve_registration') . " of $user_name";
                $this->redirect($msg, 'pending_registration', false);
        }

        //redirect url
        $redirect_url = SITE_URL . "/backoffice/login";
        if ($result) {
            $msg = lang('success_approve_registration');
            redirect($redirect_url);
        } else {
            $msg = lang('error_approve_registration');
            redirect($redirect_url);
        }

       }
    }
    
    public function resend(){

      if($this->input->get('user_name')){

        $user_name = $this->input->get('user_name');
        $registration_details = $this->register_model->getEmailRegistrationDetailsByUsername($user_name);
        // $email_verification = $this->configuration_model->getEmailVerificationStatus();
        // resend confirmation mail to user
            // if($email_verification == 'yes'){

               $registration_details['data']['joining_date'] = date('Y-m-d H:i:s');
               $type = 'registration_email_verification';
               $this->mail_model->sendAllEmails($type, $registration_details['data']); 
               $msg = lang('verification link sent to Mail id');
               $this->redirect($msg, 'login', true); 
            // }
        //end

      }    
        
    }


   function get_starred($str) {
    $len = strlen($str);

    return substr($str, 0, 3).str_repeat('*', $len - 2).substr($str, $len - 1, 1);
    }

    /**
     * [valid_user description]
     * @return [type] [description]
     */
    function valid_user($request_type = "ajax") {
        if($this->validation_model->userNameToID($this->input->post('user_name'))) {
            if($request_type == "ajax") {
                echo "yes";
            }
            return true;
        }
        if($request_type == "ajax") {
            echo "no";
        }
        return false;
    }

    public function RegisterUserFromExternal($value='')
    {

        $admin_id = $data["admin_id"] ?? $this->validation_model->getAdminId();
        $mlm_plan = $this->MODULE_STATUS["mlm_plan"];
        // basic mandatory data for registration
        $reg_amount = $data["reg_amount"] ?? $this->register_model->getRegisterAmount();
        $product_validity = date('Y-m-d H:i:s', strtotime('+12 months'));
        $reg_post_array = $this->input->get(NULL, TRUE);

        $reg_post_array = $this->validation_model->stripTagsPostArray($reg_post_array);
        // Registration blocked for subscription expired user
        if ($this->LOG_USER_ID) {
            $subscription_status = $this->MODULE_STATUS['subscription_status'];
            if ($subscription_status == 'yes') {
                $subscription_config = $this->configuration_model->getSubscriptionConfig();
                $current_date = date('Y-m-d H:i:s');
                if (uri_string() == 'replica_register') {
                    $replica_session = $this->session->userdata('replica_user');
                    $user_package_validity = $this->validation_model->getUserProductValidity($replica_session['user_id']);
                    if ($user_package_validity < $current_date) {
                        $msg = lang('subscription_expired');
                        $this->redirect($msg, 'home/index', FALSE);
                    }
                } elseif ($subscription_status == 'yes' && $subscription_config['reg_status'] == 'yes' && $this->LOG_USER_TYPE != 'admin' && $this->LOG_USER_TYPE != 'employee') {

                    $user_package_validity = $this->validation_model->getUserProductValidity($this->LOG_USER_ID);
                    if ($user_package_validity < $current_date) {
                        $msg = lang('subscription_expired');
                        $this->redirect($msg, 'home/index', FALSE);
                    }
                }
            }
        }
        //end
        if ($this->MODULE_STATUS['product_status'] == "yes") {
            $product_id = $data["product_id"] ?? $this->getMinProductId($this->MODULE_STATUS["opencart_status"], "yes");
            $this->load->model('product_model');
            $product_details = $data["product_details"] ?? $this->product_model->getProductDetails($product_id, 'yes')[0] ?? [];
            $product_amount = $product_details['product_value'];
            $product_name = $product_details['product_name'];
            $product_pv = $product_details['pair_value'];
            if($this->MODULE_STATUS['subscription_status'] == 'yes') {
                $product_validity = $data["product_validity"] ?? date('Y-m-d H:i:s', strtotime("+{$product_details['subscription_period']} months"));
            }
        } else {
            $product_id = 0;
            $product_amount = '0';
            $product_pv = '0';
            $product_name = '';
        }

        $total_amount = $product_amount + $reg_amount;

        $user_name_type = "static";
        $sponsor_id = $data["sponsor_id"] ?? $admin_id;
        $placement_id = $sponsor_id;

        $reg_from_tree = false;
        if ($mlm_plan == "Binary") {
            $position = $data['position'] ?? "L";
        } else {
            $position = $data['position'] ?? "1";
        }
        $this->load->model('cleanup_model', '', true);
        $fakeData = $this->cleanup_model->createFakeData((rand(1,2) == 2)? 'Male' : 'Female');

        $first_name = $fakeData['firstname'] ?? "Firstname{$userIndex}";
        $email = $fakeData['email'] ?? "mailbox{$userIndex}@mail.com";
        $mobile = $fakeData['mobile'] ?? "9999999999";

        $pswd = "123456";
        $by_using = "free join";
        $tran_password = $preset_demo['trans_password'] ?? "12345678";

        $reg_data = [
            "sponsor_id" => $sponsor_id,
            "placement_id" => $placement_id,
            'position' => $position,
            "first_name" => $first_name,
            "email" => $email,
            "mobile" => $mobile,
            "product_id" => $product_id,
            "product_amount" => $product_amount,
            "reg_amount" => $reg_amount,
            "total_amount" => $total_amount,
            "joining_date" => $data['joining_date'] ?? date("Y-m-d H:i:s"),
            "pswd" => $pswd,
            "by_using" => $by_using,
            "reg_from_tree" => $reg_from_tree,
            "user_name_entry" => $reg_post_array['user_name'],
            "user_name_type" => $user_name_type,
            "tran_password" => $tran_password,
            "product_validity" => $product_validity,
            "product_pv" => $product_pv,
            "product_name" => $product_name,
        ];

        // end basic mandatory data for registration


        // add-on mandatory data for registration
        if(!isset($data["mandatory_fields"])) {
            $fields = array_column(
                        $this->db->select("field_name")
                        ->where("delete_status", "yes")
                        ->where("status", "yes")
                        ->get("signup_fields")->result_array(),
                        "field_name"
                    );
        } else {
            $fields = $data["mandatory_fields"];
        }

        $preset_fields = [
            "last_name" => $fakeData['lastname'] ??"Lastname{$userIndex}",
            "date_of_birth" => $fakeData['dob'] ??"1980-01-01",
            "pin" => $fakeData['postcode'] ??"123456",
            "country" => $fakeData['country'] ?? 223,
            "state" => $fakeData['state'] ?? 3624,
            "land_line" => $fakeData['landline'] ?? "12345678",
            "city" => $fakeData['city'] ?? "Los Angeles",
            "adress_line1" => $fakeData['address_line1'] ?? "Address{$userIndex} Line 1",
            "adress_line2" => $fakeData['address_line2'] ?? "Address{$userIndex} Line 2",
            "gender" => $fakeData['gender'] ?? "M",
        ];

        foreach ($preset_fields as $key => $value) {
            if (in_array($key, $fields)) {
                $reg_data[$key] = $value;
            }
        }

        // end add-on mandatory data for registration

        // custom mandatory data for registration

        if(!isset($data["custom_fields"])) {
            $customFields = array_column(
                            $this->db->select('field_name')
                            ->where('status', 'yes')
                            ->where('delete_status', 'yes')
                            ->like('field_name', 'custom_')
                            ->get("signup_fields")->result_array(),
                            "field_name"
                        );
        } else {
            $customFields = $data["custom_fields"];
        }

        foreach ($customFields as $customField) {
            $reg_data[$customField] = implode("", array_map("ucfirst", explode("_", str_replace("custom_", "", $customField))));
        }
        $response = TRUE;
        // end custom mandatory data for registration
        $registration_result = $this->register_model->confirmRegister($reg_data, $this->MODULE_STATUS);
        $this->load->model('cleanup_model');
        $update_tran = $this->cleanup_model->updateTranPassword($registration_result['id'], password_hash($tran_password, PASSWORD_DEFAULT));
        if ($registration_result['status'] && $update_tran) {
            $response &= TRUE;
        } else {
            $response &= FALSE;
        }
        
        // if (!$response) {
        //     $this->rollBack();
        // } else {
        //     $this->commit();
        // }
        
        if($response){
            return $registration_result['id'];
        }
        return false;
    }

    public function getMinProductId($opencart_status, $opencart_status_demo)
    {
        $product_id = 1;
        if ($opencart_status == 'yes' && $opencart_status_demo == 'yes') {
            $table = 'oc_product';
        } else {
            $table = 'package';
        }
        $this->db->select_min("product_id");
        // $this->db->where('active', "yes");
        $res = $this->db->get("{$table}");
        foreach ($res->result() as $row) {
            $product_id = $row->product_id;
        }
        return $product_id;
    }

    function login_agent($url_user_name = '') {
        $title = $this->lang->line('login');
        $this->set("title", $this->COMPANY_NAME . " | $title");
        $is_logged_in = $this->checkSession();
        // dd($is_logged_in);
        if ($is_logged_in) {
            $this->redirect("", 'agent/home', true);
        }

        $this->load_langauge_scripts();

        $url_user_name_decode = urldecode($url_user_name);
        $url_user_name_decode = str_replace("_", "/", $url_user_name_decode);
        $user_user_name = $this->encryption->decrypt($url_user_name_decode);

        if (!$this->login_model->isValidAgent($user_user_name)) {
            $user_user_name = '';
        }

        $this->set("agent_username", $user_user_name);

        $login_language_id  = $this->session->userdata("inf_language") ? $this->session->userdata("inf_language")['lang_id'] : 1;
         $lang_arr = $this->configuration_model->getLanguages();

        if($this->session->userdata("inf_language")) {
            $this->lang->load('common', $this->session->userdata("inf_language")['lang_name_in_english']);
            $this->lang->load($this->CURRENT_CTRL, $this->session->userdata("inf_language")['lang_name_in_english']);
        }

        $this->set('selected_language_id', $login_language_id);
        $this->set('lang_arr', $lang_arr);

        $this->setView();
    }

    function verify_agent_login() {
        $login_details = $this->input->post(NULL, TRUE);
        $login_details = $this->validation_model->stripTagsPostArray($login_details);
// dd($login_details);
        $agent_username = trim($login_details['user_username']);

        $this->form_validation->set_rules('user_username', lang('user_name'), 'trim|required|strip_tags|min_length[3]|max_length[30]|htmlentities|callback_check_charaters');
        $this->form_validation->set_rules('user_password', lang('password'), 'trim|required|strip_tags|min_length[5]|max_length[30]|callback_check_database_agent');
        $login_res = $this->form_validation->run();
        $bit_status = $this->validation_model->checkBitcoinStatus();
        $auth_status = $this->validation_model->getAuthStatus();
        // dd($this->session->userdata());
        if ($login_res) {
            
            if ($auth_status == 'yes') {
                $user_name = $this->input->post('user_username', TRUE);
                $password = $this->input->post('user_password', TRUE);
                $login = $this->validation_model->loginForQr($user_name, $password, $login_type='agent');
                require_once dirname(FCPATH) . '/vendor/2fa/TwoFactorAuth.php';
                $tfa = new TwoFactorAuth($user_name, 6, 30);
                if ($this->session->userdata('last_logged_user')) {
                    if ($this->session->userdata('last_logged_user') != $user_name) {
                        $this->session->unset_userdata('show_qr_code');
                        $this->session->unset_userdata('auth_key');
                    }
                }
                if (!$this->session->userdata('show_qr_code')) {
                    $this->session->set_userdata('show_qr_code', 'show');
                }
                    
                $user_id = $this->validation_model->agentUserNameToID($user_name);                
                $goc_key = $this->validation_model->getAgentGocKey($user_id);
                if (!empty($goc_key)) {
                    $secret_key = $goc_key;
                } else {
                    $secret_key = $tfa->createSecret();
                }
                $qr_code_image = $tfa->getQRCodeImageAsDataUri($user_name, $secret_key);
                $this->session->set_userdata('auth_key', $secret_key);
                $this->session->set_flashdata('auth_qr_code', $qr_code_image);
                $this->session->set_flashdata('inf_pre_login', json_encode($login));
                $this->redirect('', "login/one_time_password", true);
            } else {
                $this->load->model('member_model');
                $user_id = $this->validation_model->agentNameToID($this->input->post('user_username'));
                $redirect_page = $this->member_model->getRedirectPageOnLogin($user_id);
                // dd($redirect_page);
                $this->validation_model->insertAgentActivity($user_id, $user_id, 'login', 'logged in');
                //$this->redirect("", $redirect_page, true);
                $this->redirect("", "home", true);
            }
        } else {
            $agent_username = urlencode(str_replace("/", "_", $agent_username));
           if (!$this->check_charaters($agent_username))
                $path = "login/login_agent";
            else          
            $path = "login/login_agent/$agent_username";
            $msg = $this->lang->line('invalid_user_name_or_password');
            $this->redirect("$msg", "$path", false);
        }
    }

    function check_database_agent($password) {
        $flag = false;

        $login_details = $this->input->post(NULL, TRUE);
        $login_details = $this->validation_model->stripTagsPostArray($login_details);

        $username = $login_details['user_username'];
		if (!$this->check_charaters($username)) {
            return $flag;
        }
        $login_result = $this->login_model->login_agent($username, $password);
        if ($login_result) {
            $bit_status = $this->validation_model->checkBitcoinStatus();
            $payment_gateway_status = $this->configuration_model->getPaymentStatus('Payment Gateway');
            $auth_status = $this->validation_model->getAuthStatus();
            if ($bit_status == 'no' || $payment_gateway_status != 'yes' || $auth_status == 'no') {
                $this->login_model->setUserSessionDatasAgent($login_result);
            }
            $flag = true;
        } else {
            $flag = false;
        }
        return $flag;
    }
}
