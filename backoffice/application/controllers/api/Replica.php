<?php

require_once 'Inf_Controller.php';

class Replica extends Inf_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->set_session_time_out();
        $this->load->model('configuration_model', '', true);
        $this->load->model('register_model', '', true);
        $this->load->model('replica_model', '', true);
    }

    public function check_demo_id()
    {
        $uri_segment = trim($this->input->server('PATH_INFO')?:$this->input->server('SCRIPT_URL'), '/');
        $uri_segment  = (explode('/', $uri_segment));

        if (isset($uri_segment[1]) && strlen($uri_segment[0]) > 2 && !in_array($uri_segment[1], $this->REPLICA_CONTROLLERS)) {
            if(isset($uri_segment[2]) && strlen($uri_segment[1]) > 2) {
                $replica_user = $uri_segment[2];
                $replica_admin = $uri_segment[1];
                $admin_user_id = $this->inf_model->getDemoId($replica_admin);
            } else {
                $replica_user = $uri_segment[1];
                $admin_user_id = $this->inf_model->getDemoId($replica_user);
            }

            // $backoffice_session = $this->inf_model->getBackofficeSessionFromFile();
            // if (isset($backoffice_session['inf_logged_in']['admin_user_id'])) {
            //     $admin_user_id = $backoffice_session['inf_logged_in']['admin_user_id'];
            // }

            $is_valid_demo = $this->inf_model->isValidDemoUser($replica_user, $admin_user_id);
            if ($is_valid_demo['status']) {
                $table_prefix = $is_valid_demo['table_prefix'] . "_";
                if (!$this->inf_model->checkReplicaStatus($table_prefix)) {
                    $this->session->unset_userdata("replica_user");
                    echo "<script>alert('Replication is not enabled in your demo!!!');</script>";
                    echo "<script>document.location.href ='" . SITE_URL . "';</script>";
                } else {
                    $this->inf_model->setDBPrefix($table_prefix);
                    $account_status = $this->inf_model->getDemoActiveStatus();
                    if ($account_status == 'blocked') {
                        echo "<script>alert('Your demo has been blocked.');</script>";
                        echo "<script>document.location.href ='" . SITE_URL . "';</script>";
                    }
                    $replica_id = $this->validation_model->userNameToID($replica_user);
                    $replica_user_array = array("table_prefix" => $table_prefix, "user_id" => $replica_id, "user_name" => $replica_user);
                    $this->session->set_userdata("replica_user", $replica_user_array);
                }
            } else {
                echo "<script>alert('Demo User doesn\'t exist in our System!!!');</script>";
                echo "<script>document.location.href ='" . SITE_URL . "';</script>";
            }
        } elseif ($this->check_replica_user()) {
            $replica_user = $this->session->userdata("replica_user");
            $table_prefix = $replica_user['table_prefix'];
            $this->inf_model->setDBPrefix($table_prefix);
        } else {
            $this->session->unset_userdata("replica_user");
            echo "<script>alert('Invalid URL!!!');</script>";
            echo "<script>document.location.href ='" . SITE_URL . "';</script>";
        }
    }

    public function check_replica_id()
    {
        $uri_segment = trim($this->input->server('PATH_INFO'), '/');
        $uri_segment  = (explode('/', $uri_segment));

        // if (isset($uri_segment[1]) && strlen($uri_segment[0]) > 2 && !in_array($uri_segment[1], $this->REPLICA_CONTROLLERS)) {
        if ($this->get('replica_user')) {
            $replica_user = $this->get('replica_user');
            $is_valid_demo = $this->validation_model->userNameToId($replica_user);
            if ($is_valid_demo) {
                $replica_id = $this->validation_model->userNameToId($replica_user);
                $table_prefix = $this->db->dbprefix;
                if (!$this->inf_model->checkReplicaStatus($table_prefix)) {
                    $this->session->unset_userdata("replica_user");
                    // echo "<script>alert('Replication is not enabled in your demo!!!');</script>";
                    // echo "<script>document.location.href ='" . SITE_URL . "';</script>";
                    $this->set_error_response(403, 1057);
                } else {
                    $replica_user = $this->validation_model->idToUserName($replica_id);
                    $replica_user = array("table_prefix" => $table_prefix, "user_id" => $replica_id, "user_name" => $replica_user);
                    $this->session->set_userdata("replica_user", $replica_user);
                    $this->session->set_userdata("replica_ok", "1");
                }
            } else {
                $this->session->unset_userdata("replica_user");
                // echo "<script>alert('Demo User doesn\'t exist in our System!!!');</script>";
                // echo "<script>document.location.href ='" . SITE_URL . "';</script>";
                $this->set_error_response(403, 1043);
            }
        }else if ($this->post('replica_user')) {
            $replica_user = $this->post('replica_user');
            $is_valid_demo = $this->validation_model->userNameToId($replica_user);
            if ($is_valid_demo) {
                $replica_id = $this->validation_model->userNameToId($replica_user);
                $table_prefix = $this->db->dbprefix;
                if (!$this->inf_model->checkReplicaStatus($table_prefix)) {
                    $this->session->unset_userdata("replica_user");
                    // echo "<script>alert('Replication is not enabled in your demo!!!');</script>";
                    // echo "<script>document.location.href ='" . SITE_URL . "';</script>";
                    $this->set_error_response(403, 1057);
                } else {
                    $replica_user = $this->validation_model->idToUserName($replica_id);
                    $replica_user = array("table_prefix" => $table_prefix, "user_id" => $replica_id, "user_name" => $replica_user);
                    $this->session->set_userdata("replica_user", $replica_user);
                    $this->session->set_userdata("replica_ok", "1");
                }
            } else {
                $this->session->unset_userdata("replica_user");
                // echo "<script>alert('Demo User doesn\'t exist in our System!!!');</script>";
                // echo "<script>document.location.href ='" . SITE_URL . "';</script>";
                $this->set_error_response(403, 1043);
            }
        }else if ($this->check_replica_user()) {
            $replica_user = $this->session->userdata('replica_user');
            $table_prefix = $replica_user['table_prefix'];
        } else {
            $this->session->unset_userdata("replica_user");
            // echo "<script>alert('Invalid URL!!!');</script>";
            // echo "<script>document.location.href ='" . SITE_URL . "';</script>";
            $this->set_error_response(403, 1064);
        }
    }

    public function check_replica_user()
    {
        $flag = !empty($this->session->userdata("replica_user")) ? true : false;
        return $flag;
    }

    public function set_replicated_user_data()
    {
        $replica_user = $this->session->userdata('replica_user');
        // dd($replica_user);
        $replica_id = $replica_user['user_id'];
        $this->USER_DATA = $this->inf_model->getAllUserDetails($replica_id);
        if (!$this->USER_DATA) {
            echo "<script>alert('User doesn\'t exist in our System!!!');</script>";
            echo "<script>document.location.href ='" . SITE_URL . "';</script>";
        }
        if ($this->session->userdata("replica_message")) {
            $errorr_email = $this->session->userdata("replica_message");
            $this->set("email_message", $errorr_email['message']);
            $this->set("email_messagetype", $errorr_email['type']);
            $this->session->unset_userdata("replica_message");
        }
    }

    function set_session_time_out()
    {
        if ($this->CURRENT_CTRL != "time") {
            $this->session->set_userdata("replica_user_page_load_time", time());
        }
    }

    public function get_plan_details()
    {
        $replica_user = $this->session->userdata('replica_user');
        $replica_id = $replica_user['user_id'];
        $this->REPLICA_DATA = $this->inf_model->getAllUserDetails($replica_id);
        $this->MLM_PLAN = $this->REPLICA_DATA['mlm_plan'];
    }

    function check_maintenance_mode()
    {
        if (DEMO_STATUS == 'no' || (DEMO_STATUS == 'yes' && $this->check_replica_user())) {
            if ($this->inf_model->checkMaintanenceMode()) {
                $this->MAINTENANCE_MODE = TRUE;
                $this->set("title", $this->COMPANY_NAME);
            }
            $this->MAINTENANCE_DATA = $this->inf_model->getMaintanenceData();
            $this->BLOCK_LOGIN = $this->MAINTENANCE_DATA['block_login'];
            $this->BLOCK_REGISTER = $this->MAINTENANCE_DATA['block_register'];
            $this->BLOCK_ECOMMERCE = $this->MAINTENANCE_DATA['block_ecommerce'];
        }
    }

    function load_default_currency()
    {
        if ($this->check_replica_user()) {
            $this->load->model('currency_model');
            $replica_user = $this->session->userdata('replica_user');
            $user_id = $replica_user['user_id'];
            if ($user_id) {
                $multy_currency_status = $this->currency_model->getMultyCurrencyStatus();
                $default_admin_currency = $this->currency_model->getProjectDefaultCurrencyDetails();
                if ($multy_currency_status) {
                    $conversion_status = $this->currency_model->getConversionStatus();
                    if ($conversion_status == 'automatic') {
                        if ($default_admin_currency['last_modified'] < date("Y-m-d")) {
                            $this->currency_model->automaticCurrencyUpdate($default_admin_currency['code']);
                        }
                    }
                    $currency_details = $this->currency_model->getUserDefaultCurrencyDetails($user_id);
                    if (!$currency_details) {
                        $currency_details = $default_admin_currency;
                    }
                    $this->DEFAULT_CURRENCY_VALUE = $currency_details['value'];
                    $this->DEFAULT_CURRENCY_CODE = $currency_details['code'];
                    $this->DEFAULT_SYMBOL_LEFT = $currency_details['symbol_left'];
                    $this->DEFAULT_SYMBOL_RIGHT = $currency_details['symbol_right'];
                    if ($this->DEFAULT_CURRENCY_CODE == 'BTC') {
                        $this->PRECISION = $this->PRECISION > 8 ? $this->PRECISION : 8 ;
                    } else {
                        $this->PRECISION = $this->PRECISION;;
                    }
                } else {
                    $this->DEFAULT_CURRENCY_VALUE = 1;
                    $this->DEFAULT_CURRENCY_CODE = '';
                    $this->DEFAULT_SYMBOL_LEFT = '';
                    $this->DEFAULT_SYMBOL_RIGHT = '';
                }
                $this->CURRENCY_ARR = $this->currency_model->getAllCurrency();
            }
        }
    }

    function load_default_language()
    {

        $this->LANG_ARR = $this->inf_model->getAllLanguages();
        $lang_arr_count = count($this->LANG_ARR);
        $uri_lang_code = $this->uri->segment(1);
        if (strlen($uri_lang_code) == 2) {
            $lang_active = false;
            for ($i = 0; $i < $lang_arr_count; $i++) {
                if ($uri_lang_code == $this->LANG_ARR[$i]['lang_code']) {
                    $lang_active = true;
                    $this->LANG_ID = $this->LANG_ARR[$i]['lang_id'];
                    $this->LANG_NAME = $this->LANG_ARR[$i]['lang_name_in_english'];
                    if ($this->check_replica_user()) {
                        $replica_user = $this->session->userdata('replica_user');
                        $replica_id = $replica_user['user_id'];
                        $this->inf_model->setDefaultLang($this->LANG_ID, $replica_id);
                    }
                    $this->session->set_userdata("replica_language", array("lang_id" => $this->LANG_ID, "lang_name_in_english" => $this->LANG_NAME));
                }
            }
            if (!$lang_active) {
                $default_language_array = $this->inf_model->getProjectDefaultLang();
                $this->LANG_ID = $default_language_array['lang_id'] ?? $this->LANG_ID;
                $this->LANG_NAME = $default_language_array['lang_name_in_english'];
                $this->session->set_userdata("replica_language", array("lang_id" => $this->LANG_ID, "lang_name_in_english" => $this->LANG_NAME));
            }
        } else {
            if (empty($this->session->userdata('replica_language')) && $this->check_replica_user()) {
                $replica_user = $this->session->userdata('replica_user');
                $replica_id = $replica_user['user_id'];
                $this->LANG_ID = $this->inf_model->getDefaultLang($replica_id);
                $this->LANG_NAME = $this->inf_model->getLanguageName($this->LANG_ID);
                $this->session->set_userdata("replica_language", array("lang_id" => $this->LANG_ID, "lang_name_in_english" => $this->LANG_NAME));
            } else {
                if ($this->session->userdata("replica_language")) {
                    $language_array = $this->session->userdata("replica_language");
                    $this->LANG_ID = $language_array['lang_id'] ?: $this->LANG_ID;
                    $this->LANG_NAME = $language_array['lang_name_in_english'];
                } else {
                    $default_language_array = $this->inf_model->getProjectDefaultLang();
                    $this->LANG_ID = $default_language_array['lang_id'];
                    $this->LANG_NAME = $default_language_array['lang_name_in_english'];
                    $this->session->set_userdata("replica_language", array("lang_id" => $this->LANG_ID, "lang_name_in_english" => $this->LANG_NAME));
                }
            }
        }

        $this->lang->load('common', $this->LANG_NAME);

        // if (!in_array($this->CURRENT_CTRL, $this->NO_TRANSLATION_PAGES)) {
        //     $this->lang->load($this->CURRENT_CTRL, $this->LANG_NAME);
        // }
    }


    function set_module_status_array()
    {
        $set_module = false;
        if (DEMO_STATUS == "yes") {
            if ($this->check_replica_user()) {
                $set_module = TRUE;
            }
        } else {
            $set_module = TRUE;
        }


        if ($set_module) {

            $this->MODULE_STATUS = $this->inf_model->trackModule();

            if ($this->MODULE_STATUS['mlm_plan'] == "Board") {
                $this->SHUFFLE_STATUS = $this->MODULE_STATUS['shuffle_status'];
            }
            $this->set("LANG_STATUS", $this->MODULE_STATUS['lang_status']);
            $this->set("HELP_STATUS", $this->MODULE_STATUS['help_status']);
            $this->set("STATCOUNTER_STATUS", $this->MODULE_STATUS['statcounter_status']);
            $this->set("FOOTER_DEMO_STATUS", $this->MODULE_STATUS['footer_demo_status']);
            $this->set("CAPTCHA_STATUS", $this->MODULE_STATUS['captcha_status']);
            $this->set("LIVECHAT_STATUS", $this->MODULE_STATUS['live_chat_status']);
        } else {
            $this->set("LANG_STATUS", 'yes');
            $this->set("HELP_STATUS", 'yes');
            $this->set("STATCOUNTER_STATUS", 'yes');
            $this->set("FOOTER_DEMO_STATUS", 'yes');
            $this->set("CAPTCHA_STATUS", 'yes');
            $this->set("LIVECHAT_STATUS", 'yes');
        }
    }

    public function check_demo_installed()
    {
        return true;
    }

    public function set_public_url_values()
    {
        return true;
    }

    public function set_logged_user_data()
    {
        return true;
    }

    function set_live_chat_code()
    {
        $CHAT_CODE = '';
        if ($this->check_replica_user() && $this->MODULE_STATUS['live_chat_status'] == 'yes') {
            $CHAT_CODE = ' <!--Start of Tawk.to Script-->
                                <script type="text/javascript">
                                    var Tawk_API=Tawk_API||{ }, Tawk_LoadStart=new Date();
                                    (function(){
                                    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
                                    s1.async=true;
                                    s1.src="https://embed.tawk.to/5465a1c8eebdcbe3576a5f8f/default";
                                    s1.charset="UTF-8";
                                    s1.setAttribute("crossorigin","*");
                                    s0.parentNode.insertBefore(s1,s0);
                                    })();
                                </script>
                            <!--End of Tawk.to Script-->';
        }
        $this->set("CHAT_CODE", $CHAT_CODE);
    }

    function home_get() {
        $title          = lang('Home');
        $replica_user   = $this->input->get('replica_user');
        $replica_id     = $this->validation_model->userNameToID($replica_user);
        $banners        = $this->replica_model->selectBanner($replica_id);
        $banners        = $this->security->xss_clean($banners);
        $replica_content = $this->replica_model->GetReplicaContent($this->LANG_ID,$replica_id);
        $comapny_name   = $this->configuration_model->getSiteName();

        $regsitration_url = REPLICATION_REGISTER_URL ."/". "replica_register/".$replica_user;
        if($this->MODULE_STATUS['opencart_status'] == "yes") {
            $regsitration_url = STORE_URL . "?route=register/mlm&sp_name=" . $replica_user;
        }
       
        $data = [
            'title' => $this->COMPANY_NAME . " | $title",
            'regsitration_url' => $regsitration_url,
            'banners' => $banners,
            'content' => $replica_content,
            'user_details' => $this->replica_model->getUserDetails($replica_id),
            'company_name' => strtoupper($comapny_name),
            'OPTIONAL_MODULE' => true
        ];
        $this->set_success_response(200, $data);
    }

    function contact_post(){

        $contact_error = [];
        $contact_post_array = [];
        if ($this->session->userdata('replica_contact_error')) {
            $contact_post_array = $this->session->userdata('replica_contact_post_array');
            $contact_error = $this->session->userdata('replica_contact_error');
            $this->session->unset_userdata('replica_contact_post_array');
            $this->session->unset_userdata('replica_contact_error');
        }

        if ($this->post('submit')) {
            $this->lang->load('validation');

            $contact_details = $this->post();
            $contact_details = $this->validation_model->stripTagsPostArray($contact_details);
            $this->form_validation->set_data($contact_details);

            $this->form_validation->set_error_delimiters('<span class="text-danger" style="color:#a94442;">', '</span>');
            $this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[250]', [
                'required' => ucfirst(sprintf(lang('required'), lang('name'))),
                'max_length' => ucfirst(sprintf(lang('maxlength'), lang('name'), "250")),
            ]);
            $this->form_validation->set_rules('email', lang('email'), 'trim|required|max_length[250]|valid_email', [
                'required' => ucfirst(sprintf(lang('required'), lang('email'))),
                'max_length' => ucfirst(sprintf(lang('maxlength'), lang('email'), "250")), 
                'valid_email' => ucfirst(lang('valid_email'))
            ]);
            $this->form_validation->set_rules('address', lang('address'), 'trim|required|max_length[1000]', [
                'required' => ucfirst(sprintf(lang('required'), lang('address'))),
                'max_length' => ucfirst(sprintf(lang('maxlength'), lang('address'), "1000"))
            ]);
            $this->form_validation->set_rules('phone', lang('phone_field'), 'trim|required|regex_match[/^[\s0-9+()-]+$/]|max_length[50]', [
                'required' => ucfirst(sprintf(lang('required'), lang('phone_field'))),
                'regex_match' => ucfirst(lang('phone_number')),
                'max_length' => ucfirst(sprintf(lang('maxlength'), lang('phone_field'), 50))
            ]);
            $this->form_validation->set_rules('message', lang('message'), 'max_length[10000]', [
                'max_length' => ucfirst(sprintf(lang('maxlength'), lang('message'), "10000"))
            ]);

            $res_val = $this->form_validation->run();

            $contact_post_array = $this->post(NULL, TRUE);
            if ($res_val) {
                $replica_user = $this->session->userdata('replica_user');
                $replica_id = $replica_user['user_id'];
                $name = $this->post('name', TRUE);
                $email = $this->post('email', TRUE);
                $address = $this->post('address', TRUE);
                $phone = $this->post('phone', TRUE);
                $describe = $this->post('message', TRUE);

                $res = $this->replica_model->insertDetails($replica_id, $name, $email, $address, $phone, $describe);
                if ($res) {
                    $msg = $this->lang->line('will_contact_you_shortly');
                    // $this->redirect($msg, "replica/home", TRUE);
                    $data = ['message' => $msg];
                    $this->set_success_response(200, $data);
                } else {
                    $msg = $this->lang->line('error_occured_try_again_later');
                    // $this->redirect($msg, "replica/home", FALSE);
                    $this->set_error_response(406, 1030);
                }
            } else {
                $error = $this->form_validation->error_array();
                $this->session->set_userdata('replica_contact_error', $error);
                $this->session->set_userdata('replica_contact_post_array', $contact_post_array);
                $msg = $this->lang->line('please_check_the_fields');
                // $this->redirect($msg, "replica/home#contact", FALSE);
                $this->set_error_response(406, 1004);
            }

            $data = [
                'title' => $this->COMPANY_NAME . " | $title",
                'contact_post_array' => $contact_post_array,
                'contact_error' => $contact_error
            ];
            $this->set_success_response(200, $data);

        }
    }

    function load_top_banner_get()
    {
        $replica_user   = $this->input->get('replica_user');
        $replica_id     = $this->validation_model->userNameToID($replica_user);
        $banners        = $this->replica_model->selectBanner($replica_id);
        $banners        = $this->security->xss_clean($banners);

        $banners1       = SITE_URL. '/uploads/images/banners/' . $banners;
        $data = ['banner' => $banners1];
        $this->set_success_response(200, $data);
        
    }

    public function policy_get() {
        $title          = lang('Policy');
        $replica_user   = $this->input->get('replica_user');
        $replica_id     = $this->validation_model->userNameToID($replica_user);
        $replica_content = $this->replica_model->GetReplicaContent($this->LANG_ID,$replica_id);
        $data = [
            'title' => $this->COMPANY_NAME . ' | $title',
            'content' => $replica_content
        ];
        $this->set_success_response(200, $data);
    }

    function terms_get()
    {
        $title = lang('Terms');
        // $this->set("title", $this->COMPANY_NAME . " | $title");
        $replica_user = $this->session->userdata('replica_user');
        $replica_id = $replica_user['user_id'];
        $this->load_langauge_scripts();
        $this->load->model('home_model', '', TRUE);
        $this->load->model('replica_model', '', TRUE);
        $replica_content = $this->replica_model->GetReplicaContent($this->LANG_ID,$replica_id);

        // $this->set('content',$replica_content);
        $data = [
            'title' => $this->COMPANY_NAME . ' | $title',
            'content' => $replica_content,
        ];
        $this->set_success_response(200, $data);
    }

    /**
     * [change_replica_language]
     * @return [ajax]
     */
    public function change_replica_language_post() {
        $language_id = $this->post('language');
        $languages = $this->inf_model->getAllLanguages();
        $lang_arr_count = count($languages);
        for ($i = 0; $i < $lang_arr_count; $i++) {
            if ($language_id == $languages[$i]['lang_id']) {
                $this->session->set_userdata("replica_language", array("lang_id" => $languages[$i]['lang_id'], "lang_name_in_english" => $languages[$i]['lang_name_in_english']));
                break;
            }
        }
        $data = ['message' => 'yes'];
        $this->set_success_response(200, $data);
        // exit;
    }

    // function checkDemoAccess()
    // {
    //     if(DEMO_STATUS != 'yes') {
    //         return;
    //     }
    //     if(in_array($_SERVER['REMOTE_ADDR'], ['103.103.174.106', '117.200.76.39'])) {
    //         return;
    //     }
    //     $lead_details = [];
    //     $is_preset_demo = $this->validation_model->isPresetDemo($this->ADMIN_USER_ID);
    //     $this->load->model('revamp_model');
    //     if($is_preset_demo) {
    //         $visitor_id = get_cookie('demo_visitor_id', true);
    //         if(!$visitor_id) {
    //             $this->redirect(lang('unauth_access'), 'login', false);
    //         }
    //         $lead_details = $this->revamp_model->getLeadDetailsFromVisitorId($visitor_id);
    //         if(!$lead_details) {
    //             delete_cookie('demo_visitor_id');
    //             $this->redirect(lang('unauth_access'), 'login', false);
    //         }
    //     } else {
    //         $lead_details = $this->revamp_model->getLeadDetailsFromDemoId($this->ADMIN_USER_ID);
    //         if(!$lead_details) {
    //             $this->logout_execution();
    //             $msg = lang('unauth_access');
    //             $this->redirect($msg, 'login', false);
    //         }
    //     }

    //     if($lead_details['status'] == 'verified' && $lead_details['access_expiry'] > date("Y-m-d H:i:s")) {
    //         return;
    //     }

    //     if($lead_details['status'] == 'expired') {
    //         $this->logout_execution();
    //         $msg = ($is_preset_demo)?lang('your_access_time_limit_exceeded'):lang('your_demo_expired');
    //         $this->redirect($msg, 'login', false);
    //     }

    //     $this->redirect(lang('unauth_access'), 'login', false);
    // }
}

