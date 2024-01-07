<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Inf_Controller extends Core_Inf_Controller {

    function __construct() {

        parent::__construct();

        $is_logged_in = $this->checkSession();
        if ($is_logged_in) {
            $this->redirectIfNot('user');

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
    }

}