<?php

require_once 'Inf_Controller.php';

class User_Account extends Inf_Controller {

    function __construct() {
        parent::__construct();
    }

    function user_summary_header($user_name = '') {
        $user_name = $user_name ?: $this->validation_model->getAdminUsername();
        $user_id = $this->validation_model->userNameToID($user_name);
        if ($user_id) {
            $full_name = $this->validation_model->getUserFullName($user_id);
            $user_image = $this->validation_model->getUserImage($user_id);
            $this->set('user_name', $user_name);
            $this->set('full_name', $full_name);
            $this->set('user_image', $user_image);
        }
        
        $this->load_langauge_scripts();
        
        $this->setView();
    }

}
