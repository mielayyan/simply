<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Inf_Controller extends Core_Inf_Controller {

    function __construct() {

        parent::__construct();

        $exit_status = $this->check_demo_installed();
        if ($exit_status) {
            return false;
        }

        $is_logged_in = $this->checkSession();
        if ($is_logged_in) {
            $this->clearUserSessionIfInvalid();

            $this->check_menu_permitted();

            $this->set_header_mailbox();
            
            $this->set_header_notification_box();

            $this->LEFT_MENU = $this->inf_model->getLeftMenu($this->LOG_USER_ID, $this->LOG_USER_TYPE, $this->CURRENT_URL, $this->MODULE_STATUS['mlm_plan']);
        }
        else {
            if (!in_array($this->CURRENT_CTRL, $this->NO_LOGIN_PAGES)) {
                $this->setLoginLink();
            }
        }

        $this->set_flash_message();

        $this->set_site_information();

        /**
         * keep google authentication flash data
         */
        if((DEMO_STATUS == "no" && $this->MODULE_STATUS['google_auth_status'] == "yes") || DEMO_STATUS == "yes")  {
            $this->keep_flash_data();
        }
    }
}