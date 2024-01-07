<?php

require_once 'Inf_Controller.php';


class Home extends Inf_Controller
{

    function __construct()
    { 
        parent::__construct();
        $this->load->model('profile_model');
        $this->load->model('register_model');
        //$this->lang->load('profile_lang');
        //$this->lang->load('configuration_lang');
    }

    function index() {

        $title = $this->lang->line('dashboard');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "dashboard";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('dashboard');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('dashboard');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $module_status = $this->validation_model->getModuleStatus();
        if($module_status['opencart_status'] == 'no' && $module_status['opencart_status_demo'] == 'no'){
         
         $user_name = $this->LOG_USER_NAME;
         $user_details = $this->register_model->getEmailRegistrationDetailsByUsername($user_name);
         
        }else{
         
          $user_name = $this->LOG_USER_NAME;
          $user_details = $this->register_model->getEmailRegistrationDetailsByUsernameinStore($user_name);
       
        }
        $this->set('user_details', $user_details);
        $this->setView();
    }
    public function change_default_language() {
        if ($this->input->is_ajax_request()) {
            $this->load->model('multi_language_model');
            $language = $this->input->post('language', TRUE);
            $res = $this->multi_language_model->setUserDefaultLanguageForUnapprovedUser($language, $this->LOG_USER_ID);
            if ($res) {
                echo 'yes';
            }
            else {
                echo 'no';
            }
            exit();
        }
    }



}
