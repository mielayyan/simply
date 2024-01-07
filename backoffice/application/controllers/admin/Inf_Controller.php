<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Inf_Controller extends Core_Inf_Controller {

    function __construct() {

        parent::__construct();

        $is_logged_in = $this->checkSession();
        if ($is_logged_in) {
            $this->redirectIfNot('admin');

            $this->check_menu_permitted();

            $this->set_header_mailbox();
            
            $this->set_header_notification_box();
            
            $current_url_arr = explode("/", $this->CURRENT_URL);
            if($current_url_arr[0] == "ticket_system") {
                $this->LEFT_MENU = $this->inf_model->getTicketSystemLeftMenu($this->LOG_USER_TYPE, $this->CURRENT_URL);
            }
            else {
                if(!$this->input->is_ajax_request()) {
                    $this->LEFT_MENU = $this->inf_model->getLeftMenu($this->LOG_USER_ID, $this->LOG_USER_TYPE, $this->CURRENT_URL, $this->MODULE_STATUS['mlm_plan']);
                } else {
                    $this->LEFT_MENU = [];
                }
            }
        }
        else {
            if (!in_array($this->CURRENT_CTRL, $this->NO_LOGIN_PAGES)) {
                $this->setLoginLink();
            }
        }
        if($this->LOG_USER_TYPE=='agent'){
            $this->LEFT_MENU=$this->inf_model->getAgentMenu($this->CURRENT_URL);
        }
        $this->set_flash_message();

        $this->set_site_information();
    }

}
