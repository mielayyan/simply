<?php

require_once 'Inf_Controller.php';

class My_report extends Inf_Controller {

    function __construct() {
        parent::__construct();
    }

    function unilevel_history() {
        $title = lang('unilevel_history');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "unilevel_history";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('unilevel_history');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('unilevel_history');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $log_username = ($this->LOG_USER_TYPE == 'employee') ? $this->ADMIN_USER_NAME : $this->LOG_USER_NAME;
        $user_name = $this->input->get('user_name') ?: $log_username;
        $user_id = $this->validation_model->userNameToID($user_name);
        if ($user_id == 0) {
            $msg = lang('invalid_username');
            $this->redirect($msg, 'unilevel_history', false);
        }
        $level_value = (int) $this->input->get('level') ?: 'all';

        $this->set('from_tree', $this->input->get('from_tree'));
        
        $page = $this->input->get('offset') ?: 0;

        $level_arr = $this->my_report_model->getMaxLevelSponsor($user_id);
        if ($level_value != 'all') {
            $binary_level = $level_value;
            $level_value = $this->validation_model->getUserTreeLevel($user_id, 'sponsor_tree') + $level_value;
            $level_arr_rs = $this->my_report_model->getTotalDownlineUsersUnilevel($user_id, $level_value);
            $binary = $this->my_report_model->getDownlineDetailsUnilevel($user_id, $this->PAGINATION_PER_PAGE, $page, $level_value);
        } else {
            $level_arr_rs = $this->my_report_model->getTotalDownlineUsersUnilevel($user_id);
            $binary = $this->my_report_model->getDownlineDetailsUnilevel($user_id, $this->PAGINATION_PER_PAGE, $page);
            $binary_level = 'all';
        }

        $total_downline_count = $this->my_report_model->getTotalDownlineUsersCount($this->ADMIN_USER_ID);
        $this->set('total_downline_count', $total_downline_count);
        
        $total_levels = $this->my_report_model->getMaxLevelSponsor($this->ADMIN_USER_ID);
        $this->set('total_levels', $total_levels);

        $this->pagination->set_all('admin/unilevel_history', $level_arr_rs);
        
        
        $this->set("level_arr", $binary);
        $this->set('level', $level_value);

        $this->set("level_arr", $level_arr);
        $this->set('start', $page);
        $this->set("username", $user_name);
        
        $this->set('binary_level', $binary_level);
        $this->set("binary", $binary);

        $this->setView();
    }

    function binary_history() {
        $title = lang('downline_list');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "downline_list";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('downline_list');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('downline_list');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $log_username = ($this->LOG_USER_TYPE == 'employee') ? $this->ADMIN_USER_NAME : $this->LOG_USER_NAME;
        $user_name = $this->input->get('user_name') ?: $log_username;
        $user_id = $this->validation_model->userNameToID($user_name);
        if ($user_id == 0) {
            $msg = lang('invalid_username');
            $this->redirect($msg, 'binary_history', false);
        }
        $level_value = (int) $this->input->get('level') ?: 'all';
        
        $page = $this->input->get('offset') ?: 0;

        $this->set('from_tree', $this->input->get('from_tree'));

        $level_arr = $this->my_report_model->getMaxLevelUser($user_id, 'left_father', 'right_father');
        if ($level_value != 'all') {
            $binary_level = $level_value;
            $level_value = $this->validation_model->getUserLevel($user_id) + $level_value;
            $level_arr_rs = $this->my_report_model->getTotalDownlineUsersBinary($user_id, $level_value);
            $binary = $this->my_report_model->getDownlineDetailsBinary($user_id, $this->PAGINATION_PER_PAGE, $page, $level_value);
        } else {
            $level_arr_rs = $this->my_report_model->getTotalDownlineUsersBinary($user_id);
            $binary = $this->my_report_model->getDownlineDetailsBinary($user_id, $this->PAGINATION_PER_PAGE, $page);
            $binary_level = 'all';
        }

        $this->pagination->set_all('admin/binary_history', $level_arr_rs);

        $total_downline_count = $this->my_report_model->getTotalDownlineUsersCount($this->ADMIN_USER_ID);
        $this->set('total_downline_count', $total_downline_count);
        $total_levels = $this->my_report_model->getMaxLevelUser($this->ADMIN_USER_ID);
        $this->set('total_levels', $total_levels);
        
        $this->set("level_arr", $binary);
        $this->set('level', $level_value);

        $this->set("level_arr", $level_arr);
        $this->set('start', $page);
        $this->set("username", $user_name);
        
        $this->set('binary_level', $binary_level);
        $this->set("binary", $binary);

        $this->setView();
    }
    

}
