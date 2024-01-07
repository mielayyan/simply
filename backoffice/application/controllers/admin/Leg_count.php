<?php

require_once 'Inf_Controller.php';

class Leg_Count extends Inf_Controller {

    function __construct() {
        parent::__construct();
    }

    function view_leg_count($page_id='') {

        // Header date
        $title = lang('leg_count');
        $this->set('title', $this->COMPANY_NAME . ' |' . $title);
        $help_link = 'commission-details';
        $this->set('help_link', $help_link);
        $this->HEADER_LANG['page_top_header'] = lang('binary_details');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('binary_details');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();

        // Data
        $is_valid_username = false;        
        $user_name = $this->input->get('user_name') ?: '';
        $is_valid_username = $this->validation_model->isUserNameAvailable($user_name);

        if ($user_name != "" && !$is_valid_username) {
            $msg = lang('Username_not_Exists');
            $this->redirect($msg, 'view_leg_count', false);
            return false;
        }

        $user_id = $this->validation_model->userNameToID($user_name);
        $user_type = "";
        if ($user_name) {
            $users = $this->leg_count_model->getUserIdFromUserName($user_name);
            $user_id = $users['user_id'];
            $user_type = $users['user_type'];
        }
        $product_status = $this->MODULE_STATUS['product_status'];
        $this->leg_count_model->initialize($product_status);
        $user_leg_detail = $this->leg_count_model->getUserLegDetails($user_id, $this->input->get('offset'), $this->PAGINATION_PER_PAGE, $user_type);

        // Pagination
        if($user_leg_detail){

        $count = $this->leg_count_model->getCountUserLegDetails($user_id, $user_type);
    }else{
        $count=0;
    }

        $this->pagination->set_all('admin/view_leg_count', $count);
        

        // Data to view
        $this->set('user_name', $user_name);
        $this->set('user_leg_detail', $user_leg_detail);
        $this->setView();
    }

    function validate_view_leg_count() {
        $this->form_validation->set_rules('user_name', lang('user_name'), 'trim|required|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

}
