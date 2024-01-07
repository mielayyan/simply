<?php

require_once 'Inf_Controller.php';

class Auth extends Inf_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('login_model');
        $this->load->model('password_model');
        $this->load->model('captcha_model');
        $this->lang->load('common', $this->LANG_NAME);
    }

    function access_post() {
        $username = $this->post('username');
        $password = $this->post('password');
        $res = $this->login_model->verifyLoginCredentials($username, $password);
        if ($res) {
            $access_token = $this->Api_model->setAccessToken($res);
            $data = ['access_token' => $access_token];
            if(!$this->IS_MOBILE) {
                $admin = $this->validation_model->getAdminUsername();
                $this->login_model->login($username, $password, 'user', $admin);
                $data['sess_id'] = $_COOKIE['ci_session'] ?? null;
            }
            $this->set_success_response(200, $data);
        } else {
            $this->set_error_response(401, 1003);
        }
    }

    public function password_put()
    {
        $user_id = $this->rest->user_id;      
        $post_arr = $this->validation_model->stripTagsPostArray($this->put());
        // if(DEMO_STATUS == 'yes'){
        //     $this->set_error_response(400,1070); 
        // }
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

    public function validatePasswordInfo()
    {
        $this->form_validation->set_rules('current_password', lang('current_password'), 'trim|required|min_length[6]|max_length[32]|callback__alpha_password');
        $this->form_validation->set_rules('new_password', lang('new_password'), 'trim|required|max_length[32]|min_length[6]|callback__alpha_password');
        $this->form_validation->set_rules('password_confirmation', lang('password_confirmation'), 'trim|required|min_length[6]|max_length[32]|callback__alpha_password|matches[new_password]');
        return $this->form_validation->run();
    }
    public function verifyCurrent($password)
    {
        $user_id = $this->rest->user_id;   
        $dbpassword  = $this->password_model->selectPassword($user_id);
        if (!password_verify($password, $dbpassword)) {
            return false;
        }
        return true;
    }

    function _alpha_password($str_in = '')
    {
        if (!preg_match("/^[0-9a-zA-Z\s\r\n@!#\$\^%&*()+=\-\[\]\\\';,\.\/\{\}\|\":<>\?\_\`\~]+$/i", $str_in)) {
            $this->form_validation->set_message('_alpha_password', lang('password_characters_not_allowed'));
            return false;
        } else {
            if(!$this->verifyCurrent($str_in)){
                $this->form_validation->set_message('_alpha_password', lang('current_password_not_correct'));
                return false;
            } 
            return true;
        }
    }

    public function password_forget_post()
    {     
        $post_arr = $this->validation_model->stripTagsPostArray($this->post());
        $this->form_validation->set_data($post_arr);

        $user_id = $this->validation_model->userNameToID($post_arr['username']);

        $this->form_validation->set_rules('captcha', 'captcha', 'required|callback_captcha_exists['.$user_id.']');
        if(!$this->form_validation->run()) {
            return $this->set_error_response(422, 1004);
        }

        if ($this->validate_forget_password()) {
            $user_id = $this->validation_model->userNameToID($post_arr['username']);
            $is_valid_email = $this->Api_model->checkEmail($user_id, $post_arr['email']);
            if(!$is_valid_email || !filter_var($post_arr['email'], FILTER_VALIDATE_EMAIL)) {
                $this->set_error_response(422, 1022);
            }
           
            $send_details = [
                'user_id'    => $user_id,
                'email'      => $this->validation_model->getUserEmailId($user_id),
                'full_name'  => $this->validation_model->getUserFullName($user_id),
                'first_name' => $this->validation_model->getUserData($user_id, "user_detail_name"),
                'last_name'  => $this->validation_model->getUserData($user_id, "user_detail_second_name")
            ];

            $this->load->model('mail_model');
            
            $res = $this->mail_model->sendAllEmails('forgot_password', $send_details);
            if(!is_bool($res) && isset($res['status'])) {
                $res = $res['status'];
            }
            
            if ($res) {
                $data = ['message' => 'SuccessfullyUpdated'];
                $this->set_success_response(200,['message' => 'forgot password mail sended successfully']);
            } else {
                $this->set_error_response(500); 
            }
        } else {
            $this->set_error_response(422, 1004);
        } 
    }

    public function captcha_exists($captcha,$user_id) {
        // dd($user_id);
        if (!$this->captcha_model->userCaptchaExists($user_id, $captcha)) {
            $this->form_validation->set_message('captcha', lang('invalid_captcha'));
            return false;
        } else {
            return true;
        }
    }

    public function forget_password_get() {
        $user_id = $this->validation_model->userNameToID($username = $this->get('username'));
        // $this->captcha_model->CreateImageApi($this->rest->user_id, 'user', 'forget_password');
        if($user_id){
          return $this->captcha_model->CreateImageApi($user_id, 'user', 'forget_password');  
        }else{
            $this->set_error_response(422, 1011);
        }
        
    }

    public function validate_forget_password() {
        $this->form_validation->set_rules('username', lang('user_name'), 'trim|required|user_exists|strip_tags|callback_is_user_active');
        $this->form_validation->set_rules('email', lang('email'), 'trim|required|strip_tags|valid_email');
        return $this->form_validation->run();
    }

    function is_user_active($username)
    {   
        $user_id = $this->validation_model->userNameToID($username);
        $res = $this->validation_model->isUserActive($user_id);
        $this->form_validation->set_message('is_user_active', "user_not_active");
        return ($res > 0);
    }
    public function logout_get()
    {
        $admin_username = '';
        $user_name = '';
        $user_type = '';

        if ($this->MODULE_STATUS) {
            if ($this->MODULE_STATUS['opencart_status_demo'] == "yes" && $this->MODULE_STATUS['opencart_status'] == "yes") {
                $sessions = FCPATH.'../store/system/storage/session/';
                foreach(glob($sessions.'sess_*') as $file) {
                    $data = $this->readFile($file);
                }
            }
        }


        // $this->set_success_response(200, $data);
    }
    private function readFile($file) {
        $data = [];
        if (is_file($file) && filesize($file)) {
            $handle = fopen($file, 'r');
            flock($handle, LOCK_SH);
            $data = fread($handle, filesize($file));
            flock($handle, LOCK_UN);
            fclose($handle);
            $data = unserialize($data);
            $loginFt_id = $this->validation_model->getOcCustomerId($this->rest->user_id);
            if($data['customer_id'] == $loginFt_id){
                unset($data['customer_id']);
                unset($data['sponsor_info']);
                unset($data['inf_reg_data']);
                unset($data['dbprefix']);
            }
        }
        $newSession = serialize($data);
        $this->writeFile($file, $newSession);
        return true;

    }
    private function writeFile($file, $data)
    {
        if (is_file($file)) {
            $handle = fopen($file, 'w');
            flock($handle, LOCK_EX);
            fwrite($handle, $data);
            fflush($handle);
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }



    public function checkCptcha_get()
    {
        $username   = $this->input->get('username');
        $this->captcha_model->CreateImageApiNouser();

        $this->set_success_response(200, $username);
    }
     /**
     * [valid_user description]
     * @return [type] [description]
     */
    public function valid_user_post() {
        // dd('fdjb');
        if($this->validation_model->userNameToID($this->post('user_name'))) {
            // if($request_type == "ajax") {
            //     echo "yes";
            // }
            // return true;
            // dd('ghfdsg');
            $data = ['message' => 'UsernameExist'];
            $this->set_success_response(200,$data);
        }
        // dd('bvjcbvb');
        // if($request_type == "ajax") {
        //     echo "no";
        // }
        // return false;
        $data = ['message' => 'InvalidUsername'];
        // echo "404";die;
        $this->set_error_response(401, 1043);
        // $this->set_error_response(1043,$data);
    }

    /**
     * [valid_email description]
     * @return [type] [description]
     */
    public function valid_user_email_post() {
        if($this->post('e_mail') == $this->validation_model->getUserEmailId($this->validation_model->userNameToID($this->post('user_name')))) {
            // if($request_type == "ajax") {
            //     echo "yes";
            // }
            // return true;
            $data = ['message' => 'UsernameExist'];
            $this->set_success_response(200,$data);
        }
        $data = ['message' => 'InvalidMail'];
        $this->set_error_response(401,1048);
    }

    public function reset_tran_password_get() {
        $this->load->model('tran_pass_model');

        $resetkey = $this->get('resetkey');
        $resetkey_original = $resetkey;
        // $admin_user_name = $this->uri->segments[4];
        $resetkey = str_replace(["~", ".", "-"], ["/", "+", "="], $resetkey);
        $resetkey = $this->encryption->decrypt($resetkey);

        $user_name = NULL;
        $id = NULL;
        if ($resetkey != "") {
            $prefix = str_replace('_', '', $this->db->dbprefix);
            $user_arr = $this->tran_pass_model->getUserDetailFromKey($resetkey,$prefix);
            $id = $user_arr[0];
            if ($id == "") {
                // $msg = $this->lang->line('invalid_url');
                // $this->redirect("$msg", "login", FALSE);
                $data = ['message' => 'invalid_url'];
                $this->set_error_response(401,1064);
            }
            $user_name = $user_arr[1];
            $data = ['message' => 'UsernameExist', 'user_name' => $user_name, 'key' => $resetkey, 'user_id' => $id];
            $this->set_success_response(200,$data);
        } else {
            // $msg = $this->lang->line('invalid_url');
            // $this->redirect("$msg", "login", FALSE);
            $data = ['message' => 'invalid_url'];
            $this->set_error_response(401,1064);
        }
    }

    public function tran_password_reset_post() {
        $this->load->model('tran_pass_model');
        $key = $this->post("key", TRUE);
        // $resetkey = $this->post('resetkey');
        // $resetkey_original = $resetkey;
        // // $admin_user_name = $this->uri->segments[4];
        // $resetkey = str_replace(["~", ".", "-"], ["/", "+", "="], $resetkey);
        // $resetkey = $this->encryption->decrypt($resetkey);
        $resetkey = $key;

        if ($this->post("reset_password_submit") && $this->validate_reset_transaction_password()) {
            $user_name = $this->post("user_name", TRUE);
            // $captcha = $this->session->userdata('inf_captcha');
            $captcha = $this->post('captcha');
            $prefix = str_replace('_', '', $this->db->dbprefix);
            $user_id = $this->validation_model->UserNameToIdWitoutLogin($user_name,$prefix);
            // dd($this->captcha_exists($captcha,$user_id));
            if ((empty($captcha)) || !($this->captcha_exists($captcha,$user_id))) {
                // $captcha_message = $this->lang->line('invalid_captcha');
                // $this->redirect("$captcha_message", "login/reset_tran_password/$resetkey_original/$admin_user_name", false);
                $data = ['message' => 'invalid_captcha'];
                $this->set_error_response(401,1040);
            }
            
            $pass_word = $this->post("pass", TRUE);
            $confirm_pass = $this->post("confirm_pass", TRUE);
            if ($pass_word == $confirm_pass) {
                $res = $this->tran_pass_model->updatePasswordOut($user_id, $pass_word, $resetkey,$prefix);
                if ($res) {
                    $data = ['message' => 'tran_password_updated_successfully'];
                    $this->set_success_response(200,$data);
                } else {
                    // $msg = $this->lang->line('error_on_reset_tran_password');
                    // $this->redirect("$msg", "login/reset_tran_password/$resetkey_original/$admin_user_name", FALSE);
                    $data = ['message' => 'error_on_reset_tran_password'];
                    $this->set_error_response(401,1030);
                }
            }
            $this->set_error_response(401,1015);
        }
        $this->set_error_response(401,1004);
    }

    public function validate_reset_transaction_password() {
        $valid = '';
        $path = '';

        $login_details = $this->post(NULL, TRUE);
        $login_details = $this->validation_model->stripTagsPostArray($login_details);
        $this->form_validation->set_data($login_details);
        // if(isset($login_details['admin_username'])  && isset($login_details['user_username'])){
        // if(isset($login_details['user_username'])){
        // // $admin_name = trim($login_details['admin_username']);
        // $u_user_name = trim($login_details['user_username']);
        // }

        $this->form_validation->set_rules('pass', lang('password'), 'trim|required|strip_tags|min_length[8]|max_length[32]');
        $this->form_validation->set_rules('confirm_pass', lang('confirm_password'), 'trim|required|strip_tags|matches[pass]|min_length[8]|max_length[32]');
        // $this->form_validation->set_rules('captcha', lang('captcha'), 'required');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function reset_password_get() {
        $resetkey = $this->get('resetkey');
        $resetkey_original = $resetkey;
        $resetkey = str_replace(["~", ".", "-"], ["/", "+", "="], $resetkey);
        $resetkey = $this->encryption->decrypt($resetkey);

        $user_name = NULL;
        $id = NULL;
        if ($resetkey != "") {
            $user_arr = $this->validation_model->getUserDetailFromKey($resetkey);
            $id = $user_arr[0];
            if ($id == "") {
                $msg = $this->lang->line('invalid_url');
                // $this->redirect("$msg", "login", FALSE);
                $this->set_error_response(401,1064);
            }
            $user_name = $user_arr[1];
            $data = ['message' => 'UsernameExist', 'user_name' => $user_name, 'key' => $resetkey_original, 'user_id' => $id];
            $this->set_success_response(200,$data);
        } else {
            $msg = $this->lang->line('invalid_url');
            // $this->redirect("$msg", "login", FALSE);
            $this->set_error_response(401,1064);
        }

    }

    public function password_reset_post() {
        $this->load->model('tran_pass_model');
        $login_details = $this->post();
        $login_details = $this->validation_model->stripTagsPostArray($login_details);
        $this->form_validation->set_data($login_details);
        
        if ($this->post("reset_password_submit") && $this->validate_reset_password()) {
            $user_name = $this->post("user_name", TRUE);
            $key = $this->post("key", TRUE);
            // $captcha = $this->session->userdata('inf_captcha');
            $captcha = $this->post('captcha');
            $user_id = $this->validation_model->userNameToID($user_name);

            if ((empty($captcha) || !($this->captcha_exists($captcha,$user_id)))) {
                // $captcha_message = $this->lang->line('invalid_captcha');
                // $this->redirect("$captcha_message", "login/reset_tran_password/$resetkey_original/$admin_user_name", false);
                $data = ['message' => 'invalid_captcha'];
                $this->set_error_response(401,1040);
            }
            //  dd('$this->captcha_exists($captcha,$user_id)');
            // $prefix = str_replace('_', '', $this->db->dbprefix);
            // $user_id = $this->validation_model->UserNameToIdWitoutLogin($user_name,$prefix);
            $pass_word = $this->post("pass", TRUE);
            $confirm_pass = $this->post("confirm_pass", TRUE);
            if ($pass_word == $confirm_pass) {
                $res = $this->validation_model->updatePasswordOut($user_id, $pass_word, $key);
                if ($res) {
                    $data = ['message' => 'password_updated_successfully'];
                    $this->set_success_response(200,$data);
                } else {
                    // $msg = $this->lang->line('error_on_reset_tran_password');
                    // $this->redirect("$msg", "login/reset_tran_password/$resetkey_original/$admin_user_name", FALSE);
                    $data = ['message' => 'error_on_reset_password'];
                    $this->set_error_response(401,1030);
                }
            }
            $this->set_error_response(401,1021);
        }
        $this->set_error_response(401,1004);
    }

    function validate_reset_password() {
        $valid = '';
        $path = '';
        $this->form_validation->set_rules('pass', lang('password'), 'trim|required|strip_tags|min_length[6]');
        $this->form_validation->set_rules('confirm_pass', lang('confirm_password'), 'trim|required|strip_tags|matches[pass]');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

}
