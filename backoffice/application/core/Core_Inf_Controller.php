<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Core_Inf_Controller extends CI_Controller
{
    public $POP_UP =null;
    public $IP_ADDR;                        //IP ADDRESS
    public $SERVER_TIME;                    //SERVER TIME
    public $table_prefix;                   //TABLE PREFIX
    public $MLM_PLAN;                       //MLM PLAN
    public $CURRENT_CTRL = null;            //CURRENT CONTROLLER CLASS
    public $CURRENT_MTD = null;             //CURRENT CONTROLLER METHOD
    public $BASE_URL;                       //BASE URL
    public $CURRENT_URL;                    //CURRENT URL
    public $CURRENT_URL_FULL;               //CURRENT URL WITH URL ARGUEMENTS
    public $REDIRECT_URL_FULL;               //CURRENT URL WITH URL ARGUEMENTS
    public $LEFT_MENU = null;               //BACKOFFICE LEFT MENU
    public $VIEW_DATA = [];        //DATA ARRAY FOR VIEW FILES
    public $ARR_SCRIPT = [];           //SCRPT ARRAY FOR VIEW FILES
    public $COMPANY_NAME;                   //COMAPNY NAME
    public $LANG_ARR = [];             //ARRAY OF ALL ACTIVE LANGUAGES
    public $LANG_ID;                        //CURRENT LANGUAGE ID
    public $LANG_NAME;                      //CURRENT LANGUAGE FOLDER NAME
    public $HEADER_LANG;                    //COMMON LANGUAGE ARRAY FOR HEADER TEXTS
    public $SESS_STATUS;                    //SESS STATUS
    public $LOG_USER_NAME = null;           //LOGGED USER NAME
    public $LOG_USER_ID = null;             //LOGGED USER ID
    public $LOG_USER_TYPE = null;           //LOGGED USER TYPE admin/distributer/employee
    public $ADMIN_USER_NAME = null;         //ADMIN USER NAME
    public $ADMIN_USER_ID = null;           //ADMIN USER ID
    public $CURRENCY_ARR = [];         //ARRAY OF ALL ACTIVE CURRENCIES
    public $DEFAULT_CURRENCY_VALUE;         //DEFAULT CURRENCY CONVERSION VALUE
    public $DEFAULT_CURRENCY_CODE;          //DEFAULT CURRENCY CODE
    public $DEFAULT_SYMBOL_LEFT = '';       //DEFAULT CURRENCY SYMBOL LEFT
    public $DEFAULT_SYMBOL_RIGHT = '';      //DEFAULT CURRENCY SYMBOL RIGHT
    public $ADMIN_THEME_FOLDER;             //ADMIN THEME FOLDER
    public $USER_THEME_FOLDER;              //USER THEME FOLDER
    public $FROM_MOBILE;                    //ACCESS FROM MOBILE
    public $MODULE_STATUS;                  //MODULE STATUS ARRAY
    public $SHUFFLE_STATUS;                 //SHUFFLE STATUS FOR BOARD PLAN
    public $LANG_STATUS;                    //MULTI LANGUAGE MODULE STATUS
    public $HELP_STATUS;                    //HELP LINK STATUS
    public $STATCOUNTER_STATUS;             //STAT COUNTER STATUS
    public $FOOTER_DEMO_STATUS;             //FOOTER DEMO TEXT STATUS
    public $CAPTCHA_STATUS;                 //CAPTCHA STATUS
    public $LIVECHAT_STATUS;                //LIVE CHAT STATUS
    public $COMMON_PAGES;                   //PAGES WITHOUT ADMIN/USER PREFIX IN URL
    public $NO_LOGIN_PAGES;                 //PAGES THAT DOESN'T NEED LOGGED IN SESSION
    public $NO_TRANSLATION_PAGES;           //PAGES THAT DOESN'T NEED TRANSLATION FILE
    public $NO_MODEL_CLASS_PAGES;           //PAGES THAT DOESN'T NEED MODEL CLASS
    public $CSRF_TOKEN_NAME;                //CSRF TOKEN NAME
    public $CSRF_TOKEN_VALUE;               //CSRF TOKEN VALUE
    public $MAINTENANCE_MODE;               //SITE MAINTENANCE MODE
    public $MAINTENANCE_DATA;               //SITE MAINTENANCE DATA
    public $BLOCK_LOGIN;
    public $BLOCK_REGISTER;
    public $BLOCK_ECOMMERCE;
    public $OPTIONAL_MODULE;
    public $ADDON_MODULES;              // LIST OF ADDON MODULES
    public $ADDON_PAGES;              // LIST OF ADDON PAGES
    public $PAGINATION_PER_PAGE;          //DEFAULT CURRENCY CODE
    public $PRECISION;          //DEFAULT CURRENCY CODE
    public $INF_TOKEN;
    public $PUBLIC_VARS = [];          //DEFAULT UPLOAD DIRECTORY
    public $USER_DATA;
    public $APP_CONFIG;

    function __construct()
    {
        parent::__construct();

        $this->APP_CONFIG = require dirname(FCPATH) . '/project_config.php';

        if (substr(uri_string(), 0, strlen('api/')) === 'api/') {
            return;
        }
        
        $this->load_default_model_classes();
        
        $this->initialize_public_variables();
        
        $exit_status = $this->check_demo_installed();
        if ($exit_status) {
            return false;
        }
        
        $this->set_session_time_out();
        
        
        
        $this->check_maintenance_mode();
        
        $this->set_public_url_values();
        
        
        $this->check_request_from_mobile();
        
        if (!$this->FROM_MOBILE) {
            $this->set_logged_user_data();
        }
        
        $this->set_module_status_array();
        
        if (!$this->FROM_MOBILE) { //new
            $this->load_default_language();
        }
        $this->load_default_currency();
        
        $this->load_theme_folder();
        
        $this->auto_load_model_class();
        
        if (!$this->FROM_MOBILE)
        $this->set_live_chat_code();
        
        $this->check_demo_blocked();
        
        if ($this->MODULE_STATUS) {
            if (($this->LOG_USER_TYPE == 'admin' || $this->LOG_USER_TYPE == 'employee') && $this->MODULE_STATUS['opencart_status_demo'] == "yes" && $this->MODULE_STATUS['opencart_status'] == "yes") {
                $this->set_order_notification();
            }
        }
        $this->check_url_permitted();

        if (DEMO_STATUS == 'yes') {
            $this->check_preset_demo_action();
        }
        $this->getValidationErrorMessages();

        if (DEMO_STATUS == 'yes' && $this->uri->uri_string == "register/multiRegistrationAPI") {
            $this->checkForDemoUsersApiRegistration();
        }
    }

    public function check_demo_installed()
    {
        $status = false;
        $this->load->model('login_model');
        if (DEMO_STATUS == 'yes') {
            if ($this->is_valid_demo_register_link()) {
                $status = true;
                if ($this->session->has_userdata('inf_logged_in')) {
                    foreach ($this->session->userdata as $key => $value) {
                        if (strpos($key, 'inf_') === 0) {
                            $this->session->unset_userdata($key);
                        }
                    }
                    redirect($this->uri->uri_string(), 'refresh');
                }
            } else {
                if ($this->session->has_userdata('inf_logged_in')) {
                    $user_data = $this->session->userdata('inf_logged_in');
                    $user_name = $user_data['user_name'];
                    $user_type = $user_data['user_type'];
                    if ($user_type == 'admin') {
                        $demo_id = $this->login_model->getDemoId($user_name);
                        if (!$this->login_model->isDemoInstalled($demo_id)) {
                            if ($this->uri->uri_string() == 'admin/home/get_notifications') {
                                echo json_encode([]);
                                exit();
                            }
                            $this->LOG_USER_TYPE = $user_type;
                            $this->LOG_USER_NAME = $user_name;
                            $this->LOG_USER_ID = 0;
                            if ($this->CURRENT_URL == 'login/logout') {
                                $this->lang->load('login', 'english');
                                $status = true;
                            } else {
                                redirect(DEMO_URL . '/register');
                            }
                        }
                    }
                }
            }
        }
        return $status;
    }

    public function is_valid_demo_register_link()
    {
        $valid = false;
        $total_segments = $this->uri->total_segments();
        if ($total_segments == 4 || $total_segments == 5) {
            $segments = $this->uri->segment_array();
            $i = 0;
            if ($total_segments == 5 && strlen($segments[1]) == 2) {
                $i = 1;
            }
            $route = $segments[$i + 1] . '/' . $segments[$i + 2] . '/' . $segments[$i + 3];
            $user_name = $segments[$i + 4];
            if ($route == 'login/index/admin') {
                $user_id = $this->login_model->getDemoId($user_name);
                if ($user_id) {
                    $demo_installed = $this->login_model->isDemoInstalled($user_id);
                    if (!$demo_installed) {
                        $valid = true;
                    }
                }
            }
        }
        return $valid;
    }

    function initialize_public_variables()
    {
        $this->SESS_STATUS = false;
        $this->FROM_MOBILE = false;
        $this->MODULE_STATUS = [];
        $this->MLM_PLAN = 'Binary';
        $this->LANG_ID = $this->configuration_model->getLangID($this->APP_CONFIG['default_language']);
        $this->LANG_NAME = $this->APP_CONFIG['default_language']['name'];
        $this->table_prefix = $this->APP_CONFIG['db_prefix'];
        $this->CURRENT_CTRL = $this->router->class;
        $this->CURRENT_MTD = $this->router->method;
        $this->CURRENT_URL_FULL = $this->CURRENT_CTRL . "/" . $this->CURRENT_MTD;
        $this->REDIRECT_URL_FULL = $this->CURRENT_CTRL . "/" . $this->CURRENT_MTD;
        $this->CURRENT_URL = $this->CURRENT_CTRL . "/" . $this->CURRENT_MTD;
        $this->IP_ADDR = $this->input->server('REMOTE_ADDR');
        $this->BASE_URL = base_url();
        $this->PUBLIC_URL = $this->BASE_URL . "public_html/";
        $this->PUBLIC_VARS = $this->APP_CONFIG['public_vars'];
        $this->DEFAULT_CURRENCY_VALUE = $this->APP_CONFIG['default_currency']['value'];
        $this->DEFAULT_CURRENCY_CODE = $this->APP_CONFIG['default_currency']['code'];
        $this->PAGINATION_PER_PAGE = $this->APP_CONFIG['pagination'];
        $this->DEFAULT_SYMBOL_LEFT = $this->APP_CONFIG['default_currency']['symbol_left'];
        $this->DEFAULT_SYMBOL_RIGHT = $this->APP_CONFIG['default_currency']['symbol_right'];
        $this->PRECISION = $this->APP_CONFIG['precision'];
        $this->ADMIN_THEME_FOLDER = 'default';
        $this->USER_THEME_FOLDER = 'default';
        $this->COMMON_PAGES = array("login", "register", "captcha", "time", "social_invites", "crm", "repurchase", "upgrade", "project", "signup", "lcp", "replica");
        $this->NO_LOGIN_PAGES = array("login", "captcha", "backup", "time", "cron", "register", "DemoFix", "test_mail", "oc_register", "social_invites", "magento_register", "project", "signup", "lcp", "replica");
        $this->NO_TRANSLATION_PAGES = array("captcha", "time", "DemoFix", "test_mail", "oc_register", "social_invites", "cron", "magento_register", "demo_action", "business");
        $this->NO_MODEL_CLASS_PAGES = array("time", "test_mail", "DemoFix", "ticket", "business");
        $this->CSRF_TOKEN_NAME = $this->security->get_csrf_token_name();
        $this->CSRF_TOKEN_VALUE = $this->security->get_csrf_hash();
        $this->MAINTENANCE_MODE = false;
        $this->MAINTENANCE_DATA = [];
        $this->BLOCK_LOGIN = false;
        $this->BLOCK_REGISTER = false;
        $this->BLOCK_ECOMMERCE = false;
        $this->OPTIONAL_MODULE = false;
        $this->ADDON_MODULES = array("epin", "sms", "ticket_system", "employee", "currency", "crm", "auto_responder", "maintenance");
        $this->ADDON_PAGES = array(
            "ewallet/ewallet_pin_purchase",
            "configuration/pin_config",
            "configuration/payment_view",
            "configuration/payment_gateway_configuration",
            "configuration/authorize_config",
            "configuration/paypal_config",
            "configuration/bitcoin_configuration",
            "configuration/sms_settings",
            "member/leads",
            "configuration/language_settings",
            "ticket/ticket_system"
        );
        $this->REPLICA_CONTROLLERS = ['home', 'about_us', 'contact', 'policy', 'terms'];
    }

    function set_session_time_out()
    {
        if ($this->CURRENT_CTRL != "time" && $this->CURRENT_MTD != 'get_notifications') {
            $this->session->set_userdata("inf_user_page_load_time", time());
        }
    }

    function load_default_model_classes()
    {
        $this->load->model('inf_model', '', true);
        $this->load->model('validation_model', '', true);
        $this->load->model('captcha_model', '', true);
        $this->load->model('currency_model', '', true);
        $this->load->model('country_state_model', '', true);
        $this->load->model('mail_model', '', true);
    }

    function set_public_url_values()
    {
        $this->CURRENT_URL = $this->CURRENT_CTRL . "/" . $this->CURRENT_MTD;
        $this->CURRENT_URL_FULL = "";
        $this->REDIRECT_URL_FULL = "";
        $uri_count = count($this->uri->segments);

        for ($i = 1; $i <= $uri_count; $i++) {
            $uri_segment = $this->uri->segments[$i];

            if ($uri_segment != 'en' && $uri_segment != 'es' && $uri_segment != 'ch' && $uri_segment != 'pt' && $uri_segment != 'de' && $uri_segment != 'po' && $uri_segment != 'tr' && $uri_segment != 'it' && $uri_segment != 'fr' && $uri_segment != 'ar' && $uri_segment != 'ru') {

                $this->CURRENT_URL_FULL .= $uri_segment;

                if ($i == 1) {
                    if ($uri_segment != "admin" && $uri_segment != "user") {
                        $this->REDIRECT_URL_FULL .= $uri_segment;
                    }
                } else {
                    $this->REDIRECT_URL_FULL .= $uri_segment;
                }

                if (($i + 1) <= count($this->uri->segments)) {
                    $this->CURRENT_URL_FULL .= "/";
                    $this->REDIRECT_URL_FULL .= "/";
                }
            }
        }
    }

    function update_session_status()
    {
        if (DEMO_STATUS == 'no' && in_array("register", $this->NO_LOGIN_PAGES)) {
            if ($this->checkSession()) {
                $this->SESS_STATUS = true;
            }
        } else {
            $this->SESS_STATUS = true;
        }
    }

    function check_request_from_mobile()
    {
        $post_array = [];
        if ($this->input->post()) {
            $post_array = $this->input->post(null, true);
        } else {
            if (isset($_GET["admin_username"]) && isset($_GET["user_name"]) && isset($_GET["from_mobile"])) {
                $post_array["admin_username"] = $_GET["admin_username"];
                $post_array["user_name"] = $_GET["user_name"];
                $post_array["from_mobile"] = $_GET["from_mobile"];
            }
        }
        $post_array = $this->validation_model->stripTagsPostArray($post_array);

        if (isset($post_array["from_mobile"]) && $post_array["from_mobile"]) {
            $this->FROM_MOBILE = true;
        }
    }

    function set_logged_user_data()
    {

        if ($this->checkSession()) {

            $logged_in_arr = $this->session->userdata('inf_logged_in');
            if($this->login_model->checkDeletedUser($logged_in_arr["user_name"], $logged_in_arr["table_prefix"])) {
                foreach ($this->session->userdata as $key => $value) {
                    if (strpos($key, 'inf_') === 0) {
                        $this->session->unset_userdata($key);
                    }
                }

                if ($this->MODULE_STATUS) {
                    
                    if ($this->MODULE_STATUS['opencart_status_demo'] == "yes" && $this->MODULE_STATUS['opencart_status'] == "yes") {
                        $this->session->unset_userdata('customer_id');
                        $this->unset_store_session_data();
                    }
                }
                
                $this->redirect('', 'login');
            }
            $this->LOG_USER_NAME = $logged_in_arr['user_name'];
            $this->LOG_USER_ID = $logged_in_arr['user_id'];
            $this->LOG_USER_TYPE = $logged_in_arr['user_type'];
            $this->ADMIN_USER_ID = $logged_in_arr['admin_user_id'];
            $this->ADMIN_USER_NAME = $logged_in_arr['admin_user_name'];
            $this->MLM_PLAN = $logged_in_arr['mlm_plan'];
            $this->table_prefix = $logged_in_arr['table_prefix'];

            // if($this->LOG_USER_TYPE =='admin' || $this->LOG_USER_TYPE =='user'){
                $user_details = $this->validation_model->getUserDetails($this->LOG_USER_ID, $this->LOG_USER_TYPE);
            // }else{
                // $user_details = $this->validation_model->getUserDetailsAgent($this->LOG_USER_ID, $this->LOG_USER_TYPE);
            // }

            $email = $user_details['user_detail_email'];
            $affiliates_count = $user_details['affiliates_count'];
            $status = $user_details['status'];
            $profile_pic = $user_details['user_photo'];
            $rank_status = $user_details['rank_status'];
            $rank = $user_details['rank'];
            $rank_name = $user_details['rank_name'];

            $this->set("email", $email);
            $this->set('profile_pic', $profile_pic);
            $this->set('rank_status', $rank_status);
            $this->set('rank', $rank);
            $this->set('rank_name', $rank_name);
            $this->set("affiliates_count", $affiliates_count);
            $this->set("status", $status);

            if (!$this->MAINTENANCE_MODE && $this->BLOCK_LOGIN && $this->LOG_USER_TYPE != 'admin') {
                foreach ($this->session->userdata as $key => $value) {
                    if (strpos($key, 'inf_') === 0) {
                        $this->session->unset_userdata($key);
                    }
                }
                if ($this->MODULE_STATUS) {
                    if ($this->MODULE_STATUS['opencart_status_demo'] == "yes" && $this->MODULE_STATUS['opencart_status'] == "yes") {
                        $this->session->unset_userdata('customer_id');
                        $this->unset_store_session_data();
                    }
                }
                $this->redirect('', 'login');
            }
        }
    }

    function load_default_language() {
        $this->LANG_ARR = $this->inf_model->getAllLanguages();
        $lang_arr_count = count($this->LANG_ARR);
        $uri_lang_code = $this->uri->segment(1);
        $lang_id_temp = $this->LANG_ID;
        $lang_name_temp = $this->LANG_NAME;
        $this->LANG_ID = "";
        $this->LANG_NAME = "";
        
        if (strlen($uri_lang_code) == 2) {
            $lang_active = false;
            for ($i = 0; $i < $lang_arr_count; $i++) {
                if ($uri_lang_code == $this->LANG_ARR[$i]['lang_code']) {
                    $lang_active = true;
                    $this->LANG_ID = $this->LANG_ARR[$i]['lang_id'];
                    $this->LANG_NAME = $this->LANG_ARR[$i]['lang_name_in_english'];
                    $this->inf_model->setDefaultLang($this->LANG_ID);
                    $this->session->set_userdata("inf_language", array("lang_id" => $this->LANG_ID, "lang_name_in_english" => $this->LANG_NAME));
                }
            }
        } else {
            if ($this->checkSession()) {
                $user_type = $this->LOG_USER_TYPE;
                if ($user_type == "employee") {
                    $user_id = $this->ADMIN_USER_ID;
                } else {
                    $user_id = $this->LOG_USER_ID;
                }
                if($user_type == 'Unapproved'){
                    $this->LANG_ID = $this->inf_model->getDefaultLangUnapprovedUser($user_id); 
                } else {
                    $this->LANG_ID = $this->MODULE_STATUS['lang_status'] == "yes" ? $this->inf_model->getDefaultLang($user_id) : $this->LANG_ID;
                }
                $this->LANG_NAME = $this->inf_model->getLanguageName($this->LANG_ID);
                $this->session->set_userdata("inf_language", array("lang_id" => $this->LANG_ID, "lang_name_in_english" => $this->LANG_NAME));
            }
        }

        if (!$this->LANG_ID) {
            if ($this->session->userdata("inf_language")) {
                $language_array = $this->session->userdata("inf_language");
                $this->LANG_ID = $language_array['lang_id'];
                $this->LANG_NAME = $language_array['lang_name_in_english'];
            } else {
                $default_language_array = $this->inf_model->getProjectDefaultLang();
                $this->LANG_ID = $default_language_array['lang_id'];
                $this->LANG_NAME = $default_language_array['lang_name_in_english'];
                $this->session->set_userdata("inf_language", array("lang_id" => $this->LANG_ID, "lang_name_in_english" => $this->LANG_NAME));
            }
        }

        $this->lang->load('common', $this->LANG_NAME);
        $this->lang->load('menu', $this->LANG_NAME);

        if (!in_array($this->CURRENT_CTRL, $this->NO_TRANSLATION_PAGES)) {
            if (in_array($this->CURRENT_CTRL, $this->COMMON_PAGES)) {
                $this->lang->load($this->CURRENT_CTRL, $this->LANG_NAME);
            } else {
                $this->lang->load($this->CURRENT_CTRL, $this->LANG_NAME);
            }
        }
        if(!$this->LANG_ID) {
            $this->LANG_ID = $lang_id_temp;
            $this->LANG_NAME = $lang_name_temp;
        }
        $this->lang->load('override', $this->LANG_NAME);
    }

    function load_default_currency()
    {
        $user_id = $this->LOG_USER_ID;
        if ($user_id) {
            $multy_currency_status = $this->currency_model->getMultyCurrencyStatus();
            $default_admin_currency = $this->currency_model->getProjectDefaultCurrencyDetails();
            if ($multy_currency_status) {
                if ($this->LOG_USER_TYPE == "employee") {
                    $currency_details = $this->currency_model->getUserDefaultCurrencyDetails($this->ADMIN_USER_ID);
                } elseif($this->LOG_USER_TYPE == "Unapproved"){                
                    $currency_details = $this->currency_model->getUserDefaultCurrencyDetailsForUnapproved($this->LOG_USER_ID);
                }else {
                    $currency_details = $this->currency_model->getUserDefaultCurrencyDetails($user_id);
                }
                if (!$currency_details) {
                    $currency_details = $default_admin_currency;
                }
                $this->DEFAULT_CURRENCY_VALUE = $currency_details['value'];
                $this->DEFAULT_CURRENCY_CODE = $currency_details['code'];
                $this->DEFAULT_SYMBOL_LEFT = $this->security->xss_clean($currency_details['symbol_left']);
                $this->DEFAULT_SYMBOL_RIGHT = $this->security->xss_clean($currency_details['symbol_right']);
                if ($this->DEFAULT_CURRENCY_CODE == 'BTC') {
                    $this->PRECISION = $this->PRECISION > 8 ? $this->PRECISION : 8;
                } else {
                    $this->PRECISION = $this->PRECISION;
                }
            } else {
                $this->DEFAULT_CURRENCY_CODE = $this->APP_CONFIG['default_currency']['code'];
                $this->DEFAULT_CURRENCY_VALUE = $this->APP_CONFIG['default_currency']['value'];
                $this->DEFAULT_SYMBOL_LEFT = $this->APP_CONFIG['default_currency']['symbol_left'];
                $this->DEFAULT_SYMBOL_RIGHT = $this->APP_CONFIG['default_currency']['symbol_right'];
            }
            $this->CURRENCY_ARR = $this->currency_model->getAllCurrency();
        }
    }

    function load_theme_folder()
    {
        if ($this->checkSession()) {

            $theme_folder = $this->inf_model->getThemeFolder('admin_theme_folder');
            $directories = glob(APPPATH . 'views/admin/layout/themes/*');
            foreach ($directories as $directory) {
                if ($theme_folder == basename($directory)) {
                    $this->ADMIN_THEME_FOLDER = $theme_folder;
                    break;
                }
            }

            $theme_folder = $this->inf_model->getThemeFolder('user_theme_folder');
            $directories = glob(APPPATH . 'views/user/layout/themes/*');
            foreach ($directories as $directory) {
                if ($theme_folder == basename($directory)) {
                    $this->USER_THEME_FOLDER = $theme_folder;
                    break;
                }
            }

            if (DEMO_STATUS == 'yes') {
                $is_preset_demo = $this->validation_model->isPresetDemo($this->ADMIN_USER_ID);
                if ($is_preset_demo) {
                    if ($this->session->has_userdata('inf_theme_folder')) {
                        $this->ADMIN_THEME_FOLDER = $this->USER_THEME_FOLDER = $this->session->userdata('inf_theme_folder');
                    } else {
                        $this->ADMIN_THEME_FOLDER = $this->USER_THEME_FOLDER = 'default';
                    }
                }
            }
        }
    }

    function set_module_status_array()
    {

        $set_module = true;
        if (DEMO_STATUS == "yes") {
            if (!$this->LOG_USER_ID) {
                $replica_session = $this->session->userdata('replica_user');
                if ($replica_session) {
                    $table_prefix = $replica_session['table_prefix'];
                    $prefix = $this->db->dbprefix;
                    $this->db->set_dbprefix($table_prefix);
                    $replica_status = $this->validation_model->getModuleStatusByKey('replicated_site_status');
                    if ($replica_status == 'no') {
                        $set_module = false;
                        $this->db->set_dbprefix($prefix);
                    }
                } else if ($this->CURRENT_CTRL == "register") {
                    if ($this->uri->uri_string == "register/user_register") {
                        $admin_username = $this->input->get("admin");
                    } else if ($this->session->has_userdata('admin_user_name')) {
                        $admin_username = $this->session->userdata('admin_user_name');
                    } else {
                        $admin_username = "";
                    }
                    $db_prefix = "";
                    if ($admin_username) {
                        $db_prefix = $this->validation_model->getTablePrefixFromAdminUserName($admin_username);
                    }
                    if ($db_prefix) {
                        $this->db->set_dbprefix($db_prefix . "_");
                        $this->table_prefix = $db_prefix;
                        $this->session->set_userdata('admin_user_name', $admin_username);
                    } else {
                        $set_module = false;
                    }
                } else if ($this->session->has_userdata('admin_user_name')) {
                    $admin_username = $this->session->userdata('admin_user_name');
                    $db_prefix = "";
                    if ($admin_username) {
                        $db_prefix = $this->validation_model->getTablePrefixFromAdminUserName($admin_username);
                    }
                    if ($db_prefix) {
                        $this->db->set_dbprefix($db_prefix . "_");
                        $this->table_prefix = $db_prefix;
                    } else
                        $set_module = false;
                } else {
                    $set_module = false;
                }
            }

            if ($this->router->class == 'oc_register') {
                $reg_post_array = $this->input->post(NULL, TRUE);
                $reg_post_array = $this->validation_model->stripTagsPostArray($reg_post_array);
                $table_prefix = $reg_post_array['table_prefix'];
                $prefix = $this->db->dbprefix;
                $this->db->set_dbprefix($table_prefix);
                $opencart_status = $this->validation_model->getModuleStatusByKey('opencart_status');
                if ($opencart_status == 'no') {
                    $set_module = false;
                    $this->db->set_dbprefix($prefix);
                } else {
                    $set_module = true;
                }
            }
        }
        if ($set_module) {

            $this->MODULE_STATUS = $this->inf_model->trackModule();
            $this->MLM_PLAN = $this->MODULE_STATUS['mlm_plan'];
            $this->load->model('register_model', '', true);

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
            if($this->uri->uri_string != "register/multiRegistrationAPI") {
                $this->NO_LOGIN_PAGES = array_diff($this->NO_LOGIN_PAGES, ['register']);
            }
            $this->set("LANG_STATUS", 'yes');
            $this->set("HELP_STATUS", 'yes');
            $this->set("STATCOUNTER_STATUS", 'yes');
            $this->set("FOOTER_DEMO_STATUS", 'yes');
            $this->set("CAPTCHA_STATUS", 'yes');
            $this->set("LIVECHAT_STATUS", 'yes');
        }
        $is_app = 1;
        if (isset($_COOKIE['is_app']) && $_COOKIE['is_app'] == 'true') {
            $is_app = 0;
        }
        $this->set("is_app", $is_app);
    }

    function auto_load_model_class()
    {
        if ($this->uri->uri_string == "register/multiRegistrationAPI") {
            return true;
        }
        if (!in_array($this->CURRENT_CTRL, $this->NO_MODEL_CLASS_PAGES)) {
            $controler_class_model = $this->CURRENT_CTRL . "_model";
            $this->load->model($controler_class_model, '', true);
        }
    }

    function set_live_chat_code()
    {
        $CHAT_CODE = '';
        if ($this->checkSession() && $this->MODULE_STATUS['live_chat_status'] == 'yes') {
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

    function load_langauge_scripts()
    {
        $this->set_array_scripts();
        $this->set_header_language();
    }

    function set_public_variables()
    {
        $this->POP_UP = $this->input->cookie('preset_demo_visiter',true);
        if($this->LOG_USER_TYPE == 'user') {
            $this->set('demo_pop_up_url', 'user/revamp/add_new_demo_visiter');
        } else {
            $this->set('demo_pop_up_url', 'admin/revamp/add_new_demo_visiter');
        }
        $this->set("POP_UP", $this->POP_UP);
        $this->set('HEADER_LANG', $this->HEADER_LANG);
        $this->set("DEMO_STATUS", DEMO_STATUS);
        $this->set("REPLICATION_URL", REPLICATION_URL);
        $this->set("IP_ADDR", $this->IP_ADDR);
        $this->set("BASE_URL", $this->BASE_URL);
        $this->set("MLM_PLAN", $this->MLM_PLAN);
        $this->set("SESS_STATUS", $this->SESS_STATUS);
        $this->set("CURRENT_CTRL", $this->CURRENT_CTRL);
        $this->set("CURRENT_MTD", $this->CURRENT_MTD);
        $this->set("CURRENT_URL", $this->CURRENT_URL);
        $this->set("CURRENT_URL_FULL", $this->CURRENT_URL_FULL);
        $this->set('LANG_ID', $this->LANG_ID);
        $this->set('LANG_NAME', $this->LANG_NAME);
        $this->set('LANG_ARR', $this->LANG_ARR);
        $this->set('LOG_USER_ID', $this->LOG_USER_ID);
        $this->set('LOG_USER_NAME', $this->LOG_USER_NAME);
        $this->set('LOG_USER_TYPE', $this->LOG_USER_TYPE);
        $this->set('ADMIN_USER_ID', $this->ADMIN_USER_ID);
        $this->set('ADMIN_USER_NAME', $this->ADMIN_USER_NAME);
        $this->set('COMPANY_NAME', $this->COMPANY_NAME);
        $this->set('MODULE_STATUS', $this->MODULE_STATUS);
        $this->set('LEFT_MENU', $this->LEFT_MENU);
        $this->set('PUBLIC_URL', $this->PUBLIC_URL);
        $this->set('PUBLIC_VARS', $this->PUBLIC_VARS);
        $this->set('DEFAULT_CURRENCY_VALUE', $this->DEFAULT_CURRENCY_VALUE);
        $this->set('DEFAULT_CURRENCY_CODE', $this->DEFAULT_CURRENCY_CODE);
        $this->set('DEFAULT_SYMBOL_LEFT', $this->security->xss_clean($this->DEFAULT_SYMBOL_LEFT . ' '));
        $this->set('DEFAULT_SYMBOL_RIGHT', $this->security->xss_clean($this->DEFAULT_SYMBOL_RIGHT));
        $this->set('CURRENCY_ARR', $this->security->xss_clean($this->CURRENCY_ARR));
        $this->set('SERVER_TIME', date("H:i:s"));
        $this->set('SERVER_DATE', date("l\, F jS\, Y "));
        $this->set('ADMIN_THEME_FOLDER', $this->ADMIN_THEME_FOLDER);
        $this->set('USER_THEME_FOLDER', $this->USER_THEME_FOLDER);
        $this->set('TABLE_PREFIX', $this->table_prefix);
        $this->set('CSRF_TOKEN_NAME', $this->CSRF_TOKEN_NAME);
        $this->set('CSRF_TOKEN_VALUE', $this->CSRF_TOKEN_VALUE);
        $this->set('MAINTENANCE_MODE', $this->MAINTENANCE_MODE);
        $this->set('MAINTENANCE_DATA', $this->MAINTENANCE_DATA);
        $this->set('BLOCK_LOGIN', $this->BLOCK_LOGIN);
        $this->set('BLOCK_REGISTER', $this->BLOCK_REGISTER);
        $this->set('BLOCK_ECOMMERCE', $this->BLOCK_ECOMMERCE);
        $this->set('OPTIONAL_MODULE', $this->OPTIONAL_MODULE);
        $this->set('PRECISION', $this->PRECISION);
        $this->set('INF_TOKEN', $this->INF_TOKEN);

        //inactiviy logout setting-->
        if ($this->session->has_userdata('inf_logged_in')) {
            $time = $this->validation_model->selectLogoutTime();
            $this->set('Logout_time', $time);
        }
        //

        if ((in_array($this->CURRENT_CTRL, $this->ADDON_MODULES) || in_array($this->CURRENT_URL, $this->ADDON_PAGES)) && $_SERVER['SERVER_NAME'] == 'infinitemlmsoftware.com' && DEMO_STATUS == 'yes') {
            $this->set('ADDON_MODULE', true);
        } else {
            $this->set('ADDON_MODULE', false);
        }
        if (in_array($this->CURRENT_CTRL, $this->COMMON_PAGES)) {
            $this->set('SHORT_URL', $this->CURRENT_URL);
        } else {
            if ($this->LOG_USER_TYPE == 'user') {
                $this->set('SHORT_URL', 'user/' . $this->CURRENT_URL);
            } else if($this->LOG_USER_TYPE == 'Unapproved'){
                $this->set('SHORT_URL', 'Unapproved/' . $this->CURRENT_URL);
            }else {
                $this->set('SHORT_URL', 'admin/' . $this->CURRENT_URL);
            }
        }
        if (DEMO_STATUS == 'yes') {
            $is_preset_demo = $this->validation_model->isPresetDemo($this->ADMIN_USER_ID);
            $this->set("is_preset_demo", $is_preset_demo);
        }
        $this->set('USER_DATA', $this->USER_DATA);

        $this->set('system_start_date', date("F d,Y", strtotime($this->inf_model->getSystemStartDate())));
        $daterangepicker_language = [
            'applyLabel' => lang('applyLabel'),
            'cancelLabel' => lang('cancelLabel'),
            'fromLabel' => lang('fromLabel'),
            'toLabel' => lang('toLabel'),
            'customRangeLabel' => lang('customRangeLabel')
        ];
        $daterangepicker_ranges_language = [
            'All' => lang('All'),
            'Today' => lang('Today'),
            'ThisWeek' => lang('ThisWeek'),
            'ThisMonth' => lang('ThisMonth'),
            'ThisYear' => lang('ThisYear')
        ];
        $data_table_language = [
            'sProcessing' => lang('sProcessing'),
            'sSearch' => lang('sSearch'),
            'sLengthMenu' => lang('sLengthMenu'),
            'sInfo' => lang('sInfo'),
            'sInfoEmpty' => lang('sInfoEmpty'),
            'sInfoFiltered' => lang('sInfoFiltered'),
            'sInfoPostFix' => lang('sInfoPostFix'),
            'sLoadingRecords' => lang('sLoadingRecords'),
            'sZeroRecords' => lang('sZeroRecords'),
            'sEmptyTable' => lang('sEmptyTable')
        ];
        $this->set('data_table_language', json_encode($data_table_language, JSON_UNESCAPED_UNICODE));
        $this->set('daterangepicker_language', json_encode($daterangepicker_language, JSON_UNESCAPED_UNICODE));
        $this->set('daterangepicker_ranges_language', json_encode($daterangepicker_ranges_language, JSON_UNESCAPED_UNICODE));
    }

    function set_header_mailbox()
    {
        $this->load->model('home_model', '', true);
        $user_type = $this->LOG_USER_TYPE;
        $user_id = $this->LOG_USER_ID;
        $mail_content_data = $this->home_model->getUnreadMessages($user_type, $user_id);
        $mail_content = array_slice($mail_content_data, 0, 3);
        $unread_mail_count = count($mail_content_data);
        $this->set("unread_mail", $unread_mail_count);
        $this->set("mail_content", $this->security->xss_clean($mail_content));
    }

    function set_demo_upgrade_status()
    {
        $session_data = $this->session->userdata('inf_logged_in');
        $table_prefix = $session_data['table_prefix'];
        $user_ref_id = str_replace("_", "", $table_prefix);
        $upgrade_cond = $this->inf_model->checkUpgrade($user_ref_id);
        $this->set('upgrade_cond', $upgrade_cond);
    }

    function set_flash_message()
    {
        $FLASH_ARR_MSG = $this->session->flashdata('MSG_ARR');
        if ($FLASH_ARR_MSG) {
            $this->set("MESSAGE_DETAILS", $FLASH_ARR_MSG["MESSAGE"]["DETAIL"]);
            $this->set("MESSAGE_TYPE", $FLASH_ARR_MSG["MESSAGE"]["TYPE"]);
            $this->set("MESSAGE_STATUS", $FLASH_ARR_MSG["MESSAGE"]["STATUS"]);
        } else {
            $this->set("MESSAGE_STATUS", false);
            $this->set("MESSAGE_DETAILS", false);
            $this->set("MESSAGE_TYPE", false);
        }
    }

    function set_array_scripts()
    {

        $this->VIEW_DATA['ARR_SCRIPT'] = $this->inf_model->getURLScripts($this->CURRENT_URL);
    }

    function set_header_language()
    {
        $this->VIEW_DATA['HEADER_LANG'] = $this->HEADER_LANG;
    }

    function set_site_information()
    {
        $this->load->model('validation_model', '', true);
        $site_info = $this->validation_model->getSiteInformation();

        if (!file_exists(IMG_DIR . 'logos/' . $site_info['logo'])) {
            $site_info['logo'] = 'logo_' . $this->ADMIN_THEME_FOLDER . '.png';
        }

        if (!file_exists(IMG_DIR . 'logos/' . $site_info['login_logo'])) {
            $site_info['login_logo'] = 'logo_login.png';
        }
        
        $this->COMPANY_NAME = $site_info['company_name'];
        $this->set("site_info", $site_info);
        $this->set('coming_from', $_SERVER['HTTP_REFERER'] ?? '');
    }

    function checkSession()
    {
        $flag = !empty($this->session->userdata('inf_logged_in')) ? true : false;
        if (!$flag && count($this->MODULE_STATUS) && DEMO_STATUS == 'no') {
            if (($this->MODULE_STATUS['opencart_status'] == 'yes' && $this->MODULE_STATUS['opencart_status_demo'] == 'yes') && DEMO_STATUS == 'no') {
                $oc_sess_data = $this->get_store_session_data();
                if (isset($oc_sess_data['customer_id'])) {
                    $inf_logged_in = $this->login_model->getInfLoggedInArrayFromCustomerId($oc_sess_data['customer_id']);
                    if(count($inf_logged_in)) {
                        $user_type = $inf_logged_in['user_type'];
                        if (($user_type == "admin") && BASE_URL == $this->PUBLIC_VARS['ADMIN_URL']) {
                            $this->session->set_userdata('inf_logged_in', $inf_logged_in);
                            redirect($this->uri->uri_string(), 'refresh');
                        }
                        if (($user_type == "user") && BASE_URL == $this->PUBLIC_VARS['USER_URL']) {
                            $this->session->set_userdata('inf_logged_in', $inf_logged_in);
                            redirect($this->uri->uri_string(), 'refresh');
                        }
                    }
                }
            }
        } elseif (DEMO_STATUS == 'yes' && !$flag) {
            $oc_sess_data = $this->get_store_session_data();
            if (isset($oc_sess_data['customer_id']) && isset($oc_sess_data['dbprefix'])) {
                $db_prefix = $oc_sess_data['dbprefix'];
                $opencart_status = $this->inf_model->getOpencartStatus($db_prefix);
                if ($opencart_status['opencart_status'] == 'yes' && $opencart_status['opencart_status_demo'] == 'yes') {
                    $inf_logged_in = $this->login_model->getInfLoggedInArrayFromCustomerId($oc_sess_data['customer_id'], $db_prefix);
                    if(count($inf_logged_in)) {
                        $this->session->set_userdata('inf_logged_in', $inf_logged_in);
                        $flag = true;
                        redirect($this->uri->uri_string(), 'refresh');
                    }
                }
            }
        }
        if ($flag) {
            $logged_in_arr = $this->session->userdata('inf_logged_in');
            $user_type = $logged_in_arr['user_type'];
            $user_id = $logged_in_arr['user_id'];
            if ($user_type == "employee") {
                $emp_status = $this->validation_model->getEmployeeStatus($user_id);
                if ($emp_status == "no") {
                    $flag = false;
                }
            }
        }
        return $flag;
    }

    function checkAdminLogged()
    {
        if ($this->checkSession()) {
            $user_type = $this->LOG_USER_TYPE;
            if ($user_type != "admin" && $user_type != "employee") {
                $this->redirect("", "../user/home");
            }
        } else {
            $base_url = base_url();
            $login_link = $base_url . "login";
            if ($this->CURRENT_URL != "cleanup/clean_up" && $this->inf_model->getURLID($this->CURRENT_URL)) {
                $this->session->set_userdata("redirect_url", $this->REDIRECT_URL_FULL);
            } else {
                $this->session->unset_userdata("redirect_url");
            }
            echo "You don't have permission to access this page. <a href='$login_link'>Login</a>";
            die();
        }
        return true;
    }

    function checkUserLogged()
    {

        if ($this->checkSession()) {
            $user_type = $this->LOG_USER_TYPE;
            if ($user_type != "user") {
                $this->redirect("", "../admin/home");
            }
        } else {
            $base_url = base_url();
            $login_link = $base_url . "login";
            if ($this->CURRENT_URL != "cleanup/clean_up" && $this->inf_model->getURLID($this->CURRENT_URL)) {
                $this->session->set_userdata("redirect_url", $this->REDIRECT_URL_FULL);
            } else {
                $this->session->unset_userdata("redirect_url");
            }
            echo "You don't have permission to access this page. <a href='$login_link'>Login</a>";
            die();
        }

        if (!$this->validation_model->isUserActive($this->LOG_USER_ID) || $this->LOG_USER_NAME != $this->validation_model->getUserName($this->LOG_USER_ID)) {
            $base_url = base_url();
            $login_link = $base_url . "login";
            if ($this->CURRENT_URL != "cleanup/clean_up" && $this->inf_model->getURLID($this->CURRENT_URL)) {
                $this->session->set_userdata("redirect_url", $this->REDIRECT_URL_FULL);
            } else {
                $this->session->unset_userdata("redirect_url");
            }
            $this->session->sess_destroy();
            echo "You don't have permission to access this page. <a href='$login_link'>Login</a>";
            die();
        }

        return true;
    }

    function checkLogged()
    {
        $base_url = base_url();
        $login_link = $base_url . "login";

        if (!$this->checkSession()) {
            if ($this->CURRENT_URL != "cleanup/clean_up" && $this->inf_model->getURLID($this->CURRENT_URL)) {
                $this->session->set_userdata("redirect_url", $this->REDIRECT_URL_FULL);
            } else {
                $this->session->unset_userdata("redirect_url");
            }
            die("You don't have permission to access this page. <a href='$login_link'>Login</a>");
        }
        return true;
    }

    public function check_replica_user()
    {
        $replica_session = $this->inf_model->getReplicaSessionFromFile();
        $flag = isset($replica_session['replica_user']) ? true : false;
        return $flag;
    }

    function check_menu_permitted()
    {
        if ($this->LOG_USER_TYPE == 'employee') {
            $user_id = $this->LOG_USER_ID;
            $assigned_menus = $this->inf_model->getAllAssignedMenus($user_id);
            if (isset($this->CURRENT_URL) && $this->CURRENT_URL != 'home/index') {
                $link_id = $this->inf_model->getURLID($this->CURRENT_URL);
                $menu_id = '';
                if ($link_id) {
                    if ($link_id == 145) {
                        $link_id = 87;
                    }
                   $module_status_arr = explode(",", $assigned_menus);
                   if (!in_array("10#4", $module_status_arr)) { 
                    $status = $this->inf_model->checkMenuPermitted($link_id, 'perm_emp', $menu_id, $assigned_menus);
                    if (!$status) {
                        $msg = "you don't have permission to access this page";
                        $this->redirect($msg, 'home', false);
                    }
                  }
                }
            }
        } elseif ($this->LOG_USER_TYPE == 'user') {
            $perm_type = "perm_dist";
            $status = false;

            if (isset($this->CURRENT_URL) && $this->CURRENT_URL != 'home/index') {
                $product_id = $this->validation_model->getProductId($this->LOG_USER_ID);
                $package_validity_date = $this->validation_model->getUserProductValidity($this->LOG_USER_ID);
                $today = date("Y-m-d H:i:s");
                if ($today > $package_validity_date && $product_id != 0 && $this->MODULE_STATUS['subscription_status'] == 'yes' && $this->MODULE_STATUS['product_status'] == 'yes') {
                    $assigned_menus = $this->inf_model->getPackageExpiredUserMenus();
                    $module_status_arr = explode(",", $assigned_menus);
                    $link_id = $this->inf_model->getURLID($this->CURRENT_URL);
                    if ($link_id != '#') {
                        $menu_id = $this->inf_model->getMenuID($link_id, $perm_type);
                        if (!$menu_id) {
                            $submenu_id = $this->inf_model->getSubMenuID($link_id, $perm_type);
                            $menu_id = $this->inf_model->getMainMenuIdFromSubLink($link_id);
                            $menu_check = $menu_id . "#" . $submenu_id;
                        } else {
                            $menu_check = 'm#' . $menu_id;
                        }
                        if (in_array($menu_check, $module_status_arr)) {
                            $status = true;
                        }
                    } else {
                        $status = true;
                    }
                    if (!$status) {
                        $msg = "you don't have permission to access this page";
                        $this->redirect($msg, 'home', false);
                    }
                }
            }
        }
    }

    function set($set_key, $set_value)
    {
        $this->VIEW_DATA[$set_key] = $set_value;
    }

    function setView($view_path = '')
    {

        $this->set_public_variables();

        if ($this->MAINTENANCE_MODE && $this->LOG_USER_TYPE != 'admin') {
            $this->smarty->view('maintenance/index.tpl', $this->VIEW_DATA);
        } else {
            if ($view_path) {
                $this->smarty->view($view_path . '.tpl', $this->VIEW_DATA);
            } else {
                if($this->LOG_USER_TYPE == 'Unapproved'){
                $sub_directory = 'Unapproved';
                }else{
                $sub_directory = 'user';
                }
                if ($this->LOG_USER_TYPE != 'user' && $this->LOG_USER_TYPE != 'Unapproved') {
                    $sub_directory = 'admin';
                }
                if ($this->FROM_MOBILE) {
                    $sub_directory = 'mobile';
                }
                if (in_array($this->CURRENT_CTRL, $this->COMMON_PAGES)) {
                    $this->smarty->view($this->CURRENT_CTRL . '/' . $this->CURRENT_MTD . '.tpl', $this->VIEW_DATA);
                } else {
                    $this->smarty->view("$sub_directory/" . $this->CURRENT_CTRL . '/' . $this->CURRENT_MTD . '.tpl', $this->VIEW_DATA);
                }
            }
        }
    }

    function redirect($msg, $page, $message_type = false, $MSG_ARR = array())
    {
        $MSG_ARR["MESSAGE"]["DETAIL"] = $msg;
        $MSG_ARR["MESSAGE"]["TYPE"] = $message_type;
        $MSG_ARR["MESSAGE"]["STATUS"] = true;
        $this->session->set_flashdata('MSG_ARR', $MSG_ARR);

        $path = base_url();

        $split_pages = explode("/", $page);
        $controller_name = $split_pages[0];
        $page = (count($split_pages) == 1 || $controller_name == 'login' || $controller_name == 'replica' || $controller_name == 'lcp') ? $page : preg_replace("/($controller_name)\//i", "", $page, 1);


        if (in_array($controller_name, $this->COMMON_PAGES)) {
            $path .= $page;
            redirect("$path", 'refresh');
            exit();
        } else {
            if ($this->checkSession()) {
                $inf_sess = $this->session->userdata('inf_logged_in');
                $user_type = $inf_sess['user_type'];
                if ($user_type == "admin" || $user_type == "employee") {
                    $path .= "admin/" . $page;
                } else {
                    $path .= "$user_type/" . $page;
                }
                redirect("$path", 'refresh');
                exit();
            } else {
                if (in_array($controller_name, $this->NO_LOGIN_PAGES)) {
                    $path .= $page;
                    redirect("$path", 'refresh');
                    exit();
                } else {
                    $path .= "login";
                    redirect("$path", 'refresh');
                    exit();
                }
            }
        }
    }

    function check_maintenance_mode()
    {
        if (DEMO_STATUS == 'no' || (DEMO_STATUS == 'yes' && $this->checkSession())) {
            if ($this->inf_model->checkMaintanenceMode() && $this->LOG_USER_TYPE != 'admin') {
                $this->MAINTENANCE_MODE = true;
                $this->set("title", $this->COMPANY_NAME);
            }
            $this->MAINTENANCE_DATA = $this->inf_model->getMaintanenceData();
            $this->BLOCK_LOGIN = $this->MAINTENANCE_DATA['block_login'];
            $this->BLOCK_REGISTER = $this->MAINTENANCE_DATA['block_register'];
            $this->BLOCK_ECOMMERCE = $this->MAINTENANCE_DATA['block_ecommerce'];
        }
    }

    function set_header_notification_box()
    {

        $this->load->model('payout_model', '', true);
        $notification_count = 0;
        $payout_count = 0;
        $pin_count = 0;
        $feedback_document_count = 0;
        if ($this->LOG_USER_TYPE == "admin") {
            $this->load->model('feedback_model', '', true);

            $payout_release_type = $this->MODULE_STATUS['payout_release_status'];
            if ($payout_release_type == "ewallet_request" || $payout_release_type == "both") {
                $payout_count = $this->payout_model->getUnreadPayoutRequestsCount();
            }

            if ($this->MODULE_STATUS['pin_status'] == "yes") {
                $this->load->model('epin_model', '', true);
                $pin_count = $this->epin_model->getAllPinRequestCount(2);
            }

            $feedback_document_count = $this->feedback_model->getAllUnreadFeedbackCount();
            //$feedback_document_count = count($feedback);

            $notification_count = $payout_count + $pin_count + $feedback_document_count;
        } elseif ($this->LOG_USER_TYPE == "user") {
            $this->load->model('document_model', '', true);
            $this->load->model('news_model', '', true);
            $two_days_back = date("Y-m-d H:i:s", strtotime("-2 days"));

            $payout_release_type = $this->MODULE_STATUS['payout_release_status'];
            if ($payout_release_type == "ewallet_request") {
                $payout_count = $this->payout_model->userPayoutRequestCount($this->LOG_USER_ID, "released", $two_days_back = '', 1);
            }

            if ($this->MODULE_STATUS['pin_status'] == "yes") {
                $this->load->model('epin_model', '', true);
                $pin_count = $this->epin_model->getUserPinRequestCount($this->LOG_USER_ID, 'no', 1);
            }

            $feedback_document_count = $this->document_model->getUnreadDocumentsCount($this->LOG_USER_ID);

            $news_count = $this->news_model->getUnreadNewsCount($this->LOG_USER_ID);
            $this->set("news_count", $news_count);

            $notification_count = $payout_count + $pin_count + $feedback_document_count + $news_count;
        }
        $this->set("payout_count", $payout_count);
        $this->set("pin_count", $pin_count);
        $this->set("feedback_count", $feedback_document_count);
        $this->set("notification_count", $notification_count);
    }

    function decode_session_data($session_data)
    {
        $return_data = [];
        $offset = 0;
        while ($offset < strlen($session_data)) {
            if (!strstr(substr($session_data, $offset), "|")) {
                return $return_data;
            }
            $pos = strpos($session_data, "|", $offset);
            $num = $pos - $offset;
            $varname = substr($session_data, $offset, $num);
            $offset += $num + 1;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $return_data;
    }

    function encode_session_data($session_data)
    {
        $temp = $_SESSION;
        $_SESSION = $session_data;
        $return_data = session_encode();
        $_SESSION = $temp;
        return $return_data;
    }

    function get_store_session_data()
    {
        $oc_sess_data = [];
        if (isset($_COOKIE['OCSESSID'])) {
            $oc_sess_file = dirname(dirname(dirname(__DIR__))) . '/store/system/storage/session/sess_' . $_COOKIE['OCSESSID'];
            if (is_file($oc_sess_file)) {
                $handle = fopen($oc_sess_file, 'r');
                flock($handle, LOCK_SH);
                $oc_sess_data = fread($handle, filesize($oc_sess_file));
                flock($handle, LOCK_UN);
                fclose($handle);
                // $oc_sess_data = $this->decode_session_data($oc_sess_data);
                if($oc_sess_data) {
                    $oc_sess_data = unserialize($oc_sess_data);
                } else {
                    $oc_sess_data = [];
                }
                // if (isset($oc_sess_data[$_COOKIE['default']])) {
                //     $oc_sess_data = $oc_sess_data[$_COOKIE['default']];
                // }
            }
        }
        return $oc_sess_data;
    }

    function unset_store_session_data()
    {
        $oc_sess_data = $this->get_store_session_data();
        if (isset($_COOKIE['OCSESSID'])) {
            if(isset($oc_sess_data['inf_logged_in']))
                unset($oc_sess_data['inf_logged_in']);
            if(isset($oc_sess_data['customer_id']))
                unset($oc_sess_data['customer_id']);
            if(isset($oc_sess_data['inf_module_status']))
                unset($oc_sess_data['inf_module_status']);
            if (isset($oc_sess_data['inf_reg_data']))
                unset($oc_sess_data['inf_reg_data']);
            $oc_sess_file = dirname(dirname(dirname(__DIR__))) . '/store/system/storage/session/sess_' . $_COOKIE['OCSESSID'];
            if (is_file($oc_sess_file)) {
                $handle = fopen($oc_sess_file, 'w');
                flock($handle, LOCK_EX);
                // $oc_sess_data = $this->encode_session_data($oc_sess_data);
                $oc_sess_data = serialize($oc_sess_data);
                fwrite($handle, $oc_sess_data);
                fflush($handle);
                flock($handle, LOCK_UN);
                fclose($handle);
            }
        }
    }

    public function write_store_session_data($oc_sess_data)
    {
        if(!isset($_COOKIE['OCSESSID'])) {
            return false;
        }
        $oc_sess_file = dirname(dirname(dirname(__DIR__))) . '/store/system/storage/session/sess_' . $_COOKIE['OCSESSID'];
        if (is_file($oc_sess_file)) {
            $handle = fopen($oc_sess_file, 'w');
            flock($handle, LOCK_EX);
            // $oc_sess_data = $this->encode_session_data($oc_sess_data);
            $oc_sess_data = serialize($oc_sess_data);
            fwrite($handle, $oc_sess_data);
            fflush($handle);
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    function check_demo_blocked()
    {
        $blocked = false;
        $current_url = $this->CURRENT_CTRL . '/' . $this->CURRENT_MTD;
        if (DEMO_STATUS == 'yes' && $this->checkSession()) {
            $account_status = $this->inf_model->getDemoActiveStatus();
            if ($account_status == 'blocked') {
                $blocked = true;
            }
        }
        if ($blocked && !in_array($current_url, array('login/index', 'login/logout'))) {
            $this->session->sess_destroy();
            echo "<script>alert('Your demo has been blocked.');</script>";
            echo "<script>document.location.href ='" . SITE_URL . "';</script>";
            exit();
        }
    }

    function check_action_allowed()
    {
        if (DEMO_STATUS == 'yes') {
            $is_preset_demo = $this->validation_model->isPresetDemo($this->ADMIN_USER_ID);
            if ($is_preset_demo) {
                $msg = '<strong>Warning!</strong> You can\'t perform this action in shared demos. You can check it from your own demo.' . "<a href='https://infinitemlmsoftware.com/register.php' target='_blank'>Click here</a> to register a custom demo.";
                return $msg;
            }
        }
    }

    public function check_preset_demo_action()
    {
        $data = [];
        $data['url'] = [
            'configuration/update_payment_config' => 'payment_view',
            'configuration/update_module' => 'set_module_status',
            'configuration/update_menu_permission' => 'menu_permission',
            'configuration/change_language_status' => 'language_settings',
            'maintenance/site_maintenance' => 'site_maintenance_mode',
            'currency/set_default_currency' => 'currency_management',
            'currency/delete' => 'currency_management',
            'multi_language/set_default_language' => 'language_settings',
        ];
        $data['url_post'] = [
            'tran_pass/forget_transaction_password' => '',
            'tran_pass/transaction_password_change' => '',
            'password/change_user_login_password' =>'',
            'member/activate_block_member_ajax' =>'',
            'profile/user_profile_upload' =>'',
            'home/update_theme_setting' => '',
            'profile/update_personal_info' => '',
            'profile/update_contact_info' => '',
            'profile/update_bank_info' => '',
            'profile/update_payment_details' => '',
            'profile/update_default_language' => '',
            'profile/update_profileimg_banner' => 'profile_view',
            'member/activate_block_member' => 'search_member',
            'password/post_change_password' => 'change_password',
            'password/post_change_user_password' => 'change_password',
            'tran_pass/change_passcode' => 'change_passcode',
            'configuration/pin_config' => 'pin_config',
            'configuration/delete_amount_details' => 'pin_config',
            'product/membership_package_action' => 'membership_package',
            'product/edit_membership_package' => 'membership_package',
            'product/add_membership_package' => 'membership_package',
            'product/repurchase_package_action' => 'repurchase_package',
            'product/edit_repurchase_package' => 'repurchase_package',
            'product/add_repurchase_package' => 'repurchase_package',
            'product/edit_repurchase_category' => 'repurchase_category',
            'product/repurchase_category_action' => 'repurchase_category',
            'product/add_repurchase_category' => 'repurchase_category',
            'configuration/general_setting' => 'general_setting',
            'configuration/commission_settings' => 'commission_settings',
            'configuration/profile_setting' => 'profile_setting',
            'configuration/custome_field' => 'custome_field',
            'configuration/user_dashboard' => 'user_dashboard',
            'configuration/update_compensations' => 'compensation_settings',
            'configuration/binary_bonus' => 'compensation_settings',
            'configuration/level_commissions' => 'compensation_settings',
            'configuration/rank_configuration' => 'rank_configuration',
            'configuration/add_new_rank' => 'rank_configuration',
            'configuration/referal_commissions' => 'compensation_settings',
            'configuration/matching_bonus' => 'compensation_settings',
            'configuration/pool_bonus' => 'compensation_settings',
            'configuration/fast_start_bonus' => 'compensation_settings',
            'configuration/performance_bonus' => 'compensation_settings',
            'configuration/sales_commission' => 'compensation_settings',
            'configuration/roi_commission' => 'compensation_settings',
            'configuration/payout_setting' => 'payout_setting',
            'configuration/kyc_configuration' => 'kyc_configuration',
            'configuration/paypal_config' => 'payment_view',
            'configuration/authorize_config' => 'payment_view',
            'configuration/blockchain_configuration' => 'payment_view',
            'configuration/bitgo_configuration' => 'payment_view',
            'configuration/payeer_configuration' => 'payment_view',
            'configuration/mail_settings' => 'mail_settings',
            'configuration/sms_settings' => 'sms_settings',
            'configuration/tooltip_settings' => 'tooltip_settings',
            'currency/currency_management_action' => 'currency_management',
            'currency/edit_currency' => 'currency_management',
            'configuration/save_api_key' => 'api_credentials',
            'configuration/site_information' => 'site_information',
            'configuration/content_management' => 'content_management',
            'configuration/update_mail_content' => 'content_management',
            'password/post_change_password' => 'change_password',
            'tran_pass/change_passcode' => 'change_passcode',
            'configuration/signup_settings' => 'signup_settings',
            'configuration/update_pending_signup_option' => 'signup_settings',
            'configuration/update_signup_field_config' => 'signup_settings',
            'configuration/plan_settings' => 'plan_settings',
            'configuration/stairstep_configuration' => 'stairstep_configuration',
            'configuration/donation_configuration' => 'donation_configuration',
            'configuration/treeIconConfig' => 'tooltip_settings',
            'configuration/password_policy' => 'password_policy',
        ];
        if (in_array($this->CURRENT_URL, array_keys($data['url']))) {
            $this->block_preset_demo_action($data['url'][$this->CURRENT_URL]);
        }
        if (in_array($this->CURRENT_URL, array_keys($data['url_post'])) && $this->input->method() == 'post') {
            $this->block_preset_demo_action($data['url_post'][$this->CURRENT_URL]);
        }
    }

    function block_preset_demo_action($redirect)
    {
        if (DEMO_STATUS == 'yes') {
            $is_preset_demo = $this->validation_model->isPresetDemo($this->ADMIN_USER_ID);
            if ($is_preset_demo) {
                $msg = '<strong>Warning!</strong> You can\'t perform this action in shared demos. You can check it from your own demo.' . "<a href='https://infinitemlmsoftware.com/register.php' target='_blank'>Click here</a> to register a custom demo.";
                if ($redirect) {
                    $this->redirect($msg, $redirect, false);
                } else {
                    exit();
                }
            }
        }
    }

    function set_order_notification()
    {
        $this->load->model('order_model', '', true);

        $orders = $this->order_model->getOrderNotification(0, 3);
        $unread_orders = $this->order_model->getOrderNotificationCount();
        $this->set("latest_orders", $unread_orders);
        $this->set("latest_order", $orders);
    }

    public function deny_permission()
    {
        $msg = lang('permission_denied');
        $this->redirect($msg, 'home/index', false);
    }

    public function check_url_permitted()
    {

        if ($this->checkSession()) {
            if ($this->LOG_USER_TYPE == "admin") {
                $perm_type = "perm_admin";
            } elseif ($this->LOG_USER_TYPE == "user") {
                $perm_type = "perm_dist";
            } elseif($this->LOG_USER_TYPE == "Unapproved") {
                $perm_type = "perm_dist";
            }else {
                $perm_type = "perm_emp";
            }
            $current_url = $this->CURRENT_URL;
            if ($current_url != "configuration/menu_permission") {
                $link_id = $this->inf_model->getURLID($current_url);
                if ($link_id) {
                    $url_perm = $this->inf_model->checkUrlPermitted($link_id, $perm_type);
                    if (!$url_perm && $current_url != "home/index") {
                        $msg = lang('permission_denied');
                        $this->redirect($msg, 'home/index', false);
                    }
                }
            }
        }
    }

    public function url_permission($module)
    {
        if ($this->MODULE_STATUS["$module"] == "no") {
            $msg = lang('permission_denied');
            $this->redirect($msg, 'home/index', false);
        }
    }

    public function setLoginLink()
    {
        $base_url = base_url();
        $login_link = $base_url . "login";
        if ($this->CURRENT_URL != "cleanup/clean_up" && $this->inf_model->getURLID($this->CURRENT_URL)) {
            $this->session->set_userdata("redirect_url", $this->REDIRECT_URL_FULL);
        } else {
            $this->session->unset_userdata("redirect_url");
        }
        echo "You don't have permission to access this page. <a href='$login_link'>Login</a>";
        exit();
    }

    public function redirectIfNot($user_type)
    {
        // $log_user_type = ($this->LOG_USER_TYPE == 'employee') ? 'admin' : $this->LOG_USER_TYPE;
        // dd($this->LOG_USER_TYPE);
        $log_user_type = ($this->LOG_USER_TYPE == 'employee') ? 'admin' : (($this->LOG_USER_TYPE == 'agent') ? 'admin' : $this->LOG_USER_TYPE);
        // dd($log_user_type);
        if ($user_type != $log_user_type) {
            $this->redirect('', "{$log_user_type}/home");
        }
    }

    public function clearUserSessionIfInvalid()
    {
        // $log_user_type = ($this->LOG_USER_TYPE == 'employee') ? 'admin' : $this->LOG_USER_TYPE;
        $log_user_type = ($this->LOG_USER_TYPE == 'employee') ? 'admin' : (($this->LOG_USER_TYPE == 'agent') ? 'admin' : $this->LOG_USER_TYPE);
        if($log_user_type == 'Unapproved'){
         //redirect($this->uri->uri_string(), 'refresh');

        }
        else if ($log_user_type != 'admin') {
            if (!$this->validation_model->isUserActive($this->LOG_USER_ID) || $this->LOG_USER_NAME != $this->validation_model->getUserName($this->LOG_USER_ID) || $this->validation_model->forceLogout($this->LOG_USER_ID)) {
                $this->session->sess_destroy();
                redirect($this->uri->uri_string(), 'refresh');
            }
        }
    }

    public function getValidationErrorMessages()
    {
        $array = $this->lang->load('validation', $this->getLanguageName(), true);
        $this->set('validations', $array);
    }

    public function getLanguageName() {
        $lang_name = $this->session->userdata('login_language')['lang_name_in_english'] ?? $this->LANG_NAME;
        if(!$lang_name) {
            $lang_name = $this->LANG_NAME;
        }
        return $lang_name;
    }

    
    /**
     * keep flash_data when request is XHR type 
     * for google authentication purpose 
     * @return void
    */
    protected function keep_flash_data() {
        if(isset(
            $_SERVER['HTTP_X_REQUESTED_WITH']) && 
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {    
            if($this->session->flashdata('auth_qr_code') && $this->session->flashdata('inf_pre_login')) {
                $this->session->keep_flashdata(['auth_qr_code', 'inf_pre_login']);   
            }
        }
    }

    public function checkForDemoUsersApiRegistration()
    {
        if ($this->uri->uri_string == "register/multiRegistrationAPI") {
            $reg_post_array = $this->input->get(NULL, TRUE);
            if (!count($reg_post_array) || DEMO_STATUS != "yes") {
                echo json_encode(['status' => false, 'message' => 'Invalid request']);
                exit();
            }
            $inf_token = $reg_post_array['inf_token'] ?? '';
            $demo_id = $reg_post_array['user_id'] ?? '';
            
            if ($inf_token != 'f6f7369316c4928fdceaaed397356f5b' || !$demo_id) {
                echo json_encode(['status' => false, 'message' => 'Invalid request']);
                exit();
            }
            $infinite_mlm_user_details = $this->inf_model->getInfiniteMlmUserDetails($demo_id);
            if (!count($infinite_mlm_user_details)) {
                echo json_encode(['status' => false, 'message' => 'Invalid request']);
                exit();
            }
            $this->inf_model->setDBPrefix($infinite_mlm_user_details['table_prefix'] . '_');
            $this->load->model('register_model');
        }
    }

    /**
     * [getAdminID get Admin ID]
     * @return [int] [eg: 14959]
     */
    public function getAdminID() {
        return $this->LOG_USER_TYPE == "employee" ? $this->validation_model->getAdminId() : $this->LOG_USER_ID;
    }
}
