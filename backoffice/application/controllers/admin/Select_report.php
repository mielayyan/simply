<?php

require_once 'Inf_Controller.php';

class Select_report extends Inf_Controller {

    function __construct() {
        parent::__construct();
    }

    function admin_profile_report() {

        $this->set("action_page", $this->CURRENT_URL);
        $title = lang('profile_reports');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('profile_reports');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('profile_reports');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $error_array = array();
        if ($this->session->userdata('inf_profile_report_view_error')) {
            $error_array = $this->session->userdata('inf_profile_report_view_error');
            $this->session->unset_userdata('inf_profile_report_view_error');
        }

        $error_array_count = array();
        if ($this->session->userdata('inf_profile_report_view_count_error')) {
            $error_array_count = $this->session->userdata('inf_profile_report_view_count_error');
            $this->session->unset_userdata('inf_profile_report_view_count_error');
        }

        $error_array_profile_count = array();
        if ($this->session->userdata('inf_profile_report_count_error')) {
            $error_array_profile_count = $this->session->userdata('inf_profile_report_count_error');

            $this->session->unset_userdata('inf_profile_report_count_error');
        }

        $this->set('error_array', $error_array);
        $this->set('error_count', count($error_array));

        $this->set('error_array_count', $error_array_count);
        $this->set('error_single_count', count($error_array_count));

        $this->set('error_array_profile_count', $error_array_profile_count);
        $this->set('error_profile_count', count($error_array_profile_count));

        $help_link = "member-profile-report";
        $this->set("help_link", $help_link);
        $this->setView();
    }

    function total_joining_report() {

        $title = lang('joining_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('joining_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('joining_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $error_array = array();
        if ($this->session->userdata('inf_total_joining_daily_error')) {
            $error_array = $this->session->userdata('inf_total_joining_daily_error');
            $this->session->unset_userdata('inf_total_joining_daily_error');
        }
        $error_array_weekely = array();
        if ($this->session->userdata('inf_total_joining_weekly_error')) {
            $error_array_weekely = $this->session->userdata('inf_total_joining_weekly_error');
            $this->session->unset_userdata('inf_total_joining_weekly_error');
        }

        $this->set('error_array', $error_array);
        $this->set('error_count', count($error_array));

        $this->set('error_array_weekly', $error_array_weekely);
        $this->set('error_count_weekly', count($error_array_weekely));

        $help_link = "joining-report";
        $this->set("help_link", $help_link);

        $this->setView();
    }

    function total_payout_report() {

        $title = lang('total_bonus_report');
        $this->set("title", $this->COMPANY_NAME . " | $title ");

        $this->HEADER_LANG['page_top_header'] = lang('total_bonus_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('total_bonus_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $error_array = array();
        if ($this->session->userdata('inf_weekly_payout_report_error')) {
            $error_array = $this->session->userdata('inf_weekly_payout_report_error');
            $this->session->unset_userdata('inf_weekly_payout_report_error');
        }

        $error_array_user = array();
        if ($this->session->userdata('inf_member_payout_report_error')) {
            $error_array_user = $this->session->userdata('inf_member_payout_report_error');
            $this->session->unset_userdata('inf_member_payout_report_error');
        }

        $this->set('error_array', $error_array);
        $this->set('error_count', count($error_array));

        $this->set('error_array_user', $error_array_user);
        $this->set('error_count_user', count($error_array_user));

        $help_link = "payout-report";
        $this->set("help_link", $help_link);

        $this->setView();
    }

    function payout_release_report() {

        $title = lang('payout_reports');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('payout_reports');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('payout_reports');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $error_array = array();
        if ($this->session->userdata('inf_payout_released_report_daily_error')) {
            $error_array = $this->session->userdata('inf_payout_released_report_daily_error');
            $this->session->unset_userdata('inf_payout_released_report_daily_error');
        }
        
        $this->set('error_array', $error_array);
        $this->set('error_count', count($error_array));

        $help_link = "payout-release-report";
        $this->set("help_link", $help_link);

        $this->setView();
    }
    
    function rank_achievers_report() {

        $title = lang('rank_achieve_report');
        $this->set("title", $this->COMPANY_NAME . " | $title ");

        $help_link = "rank-achievers-report";
        $this->set("help_link", $help_link);
        $this->url_permission('rank_status');

        $this->HEADER_LANG['page_top_header'] = lang('rank_achieve_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('rank_achieve_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $error_array = array();
        if ($this->session->userdata('inf_rank_achievers_report_error')) {
            $error_array = $this->session->userdata('inf_rank_achievers_report_error');
            $this->session->unset_userdata('inf_rank_achievers_report_error');
        }

        $rank_arr = array();
        $rank_arr = $this->select_report_model->getAllRank();

        $this->set("rank_arr", $rank_arr);
        $this->set('error_array', $error_array);
        $this->set('error_count', count($error_array));

        $this->setView();
    }

    function commission_report() {

        $title = lang('commission_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('commission_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('commission_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $error_array = array();
        if ($this->session->userdata('inf_commission_report_error')) {
            $error_array = $this->session->userdata('inf_commission_report_error');
            $this->session->unset_userdata('inf_commission_report_error');
        }

        $this->load->model('ewallet_model');
        $commission_types = $this->ewallet_model->getEnabledBonusList();
        $received_commission_types = $this->ewallet_model->getReceivedBonusList();
        $commission_types = array_unique(array_merge($commission_types, $received_commission_types));
        $count_commission = count($commission_types);

        $this->set('error_array', $error_array);
        $this->set('error_count', count($error_array));

        $help_link = "commission_report";
        $this->set("help_link", $help_link);
        $this->set("commission_types", $commission_types);
        $this->set("count_commission", $count_commission);
        $this->set("MLM_PLAN", $this->MLM_PLAN);

        $this->lang->load('amount_type', $this->LANG_NAME);

        $this->setView();
    }

    function epin_report() {

        $title = lang('epin_transfer_report');
        $this->set("title", $this->COMPANY_NAME . " | $title ");
        $this->url_permission('pin_status');

        $this->HEADER_LANG['page_top_header'] = lang('epin_transfer_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('epin_transfer_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $help_link = "payout-report";
        $this->set("help_link", $help_link);
        $this->setView();
    }

    function top_earners_report() {

        $title = lang('top_earners');
        $this->set("title", $this->COMPANY_NAME . " | $title ");

        $this->HEADER_LANG['page_top_header'] = lang('top_earners');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('top_earners');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        
        // $base_url = base_url() . 'admin/select_report/top_earners_report';
        // $config = $this->pagination->customize_style();
        // $config['base_url'] = $base_url;
        // $config['per_page'] = $this->PAGINATION_PER_PAGE;

        // if ($this->uri->segment(4) != "") {
        //     $page = $this->uri->segment(4);
        // } else {
        //     $page = 0;
        // }
        
        //$count = $this->select_report_model->getTopEarnersCount();
        //$config['total_rows'] = $count;
        //$this->pagination->initialize($config);
        
            
        $top_earners = $this->select_report_model->getTopEarners($this->input->get('offset'), 
            $this->PAGINATION_PER_PAGE);
        $count= $this->select_report_model->getCountTopEarners();
        $this->pagination->set_all('admin/top_earners_report',$count);
        //$help_link = "Top-earners";
       // $this->set("help_link", $help_link);
        $this->set("top_earners", $top_earners);
        
        //$this->set('page_id', $page);

        $this->setView();
    }

    function ajax_users_auto($user_name = "") {
       // $letters = preg_replace("/[^a-z0-9 ]/si", "", $user_name);
        $user_detail = $this->select_report_model->selectUser($user_name);
        echo $user_detail;
    }

    function ajax_epin_auto($user_name = "") {
        $letters = preg_replace("/[^a-z0-9 ]/si", "", $user_name);
        $str = $this->select_report_model->selectEpin($letters);
        echo $str;
    }

    function repurchase_report() {
        $title = lang('repurchase_report');
        $this->set("title", $this->COMPANY_NAME . " | $title ");

        $this->HEADER_LANG['page_top_header'] = lang('repurchase_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('repurchase_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $error_array = array();
        if ($this->session->userdata('inf_repurchase_report_view_error')) {
            $error_array = $this->session->userdata('inf_repurchase_report_view_error');
            $this->session->unset_userdata('inf_repurchase_report_view_error');
        }
        $this->set('error_array', $error_array);
        $this->set('error_count', count($error_array));

        $help_link = "Purchase-report";
        $this->set("help_link", $help_link);

        $this->setView();
    }

    function stair_step_report() {
        
        $title = lang('stair_step_report');
        $this->set("title", $this->COMPANY_NAME . " | $title ");

        $this->HEADER_LANG['page_top_header'] = lang('stair_step_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('stair_step_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $error_array = array();
        if ($this->session->userdata('inf_stair_step_report_view_error')) {
            $error_array = $this->session->userdata('inf_stair_step_report_view_error');
            $this->session->unset_userdata('inf_stair_step_report_view_error');
        }
        $this->set('error_array', $error_array);
        $this->set('error_count', count($error_array));

        $help_link = "stair-step-report";
        $this->set("help_link", $help_link);

        $this->setView();
    }
    
    function override_report() {
        $title = lang('override_report');
        $this->set("title", $this->COMPANY_NAME . " | $title ");

        $this->HEADER_LANG['page_top_header'] = lang('override_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('override_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $error_array = array();
        if ($this->session->userdata('inf_override_report_view_error')) {
            $error_array = $this->session->userdata('inf_override_report_view_error');
            $this->session->unset_userdata('inf_override_report_view_error');
        }
        $this->set('error_array', $error_array);
        $this->set('error_count', count($error_array));

        $help_link = "override_report";
        $this->set("help_link", $help_link);

        $this->setView();
    }
//config change report    
    function config_changes_report() {
        $title = lang('config_history');
        $this->set("title", $this->COMPANY_NAME . " | $title ");

        $this->HEADER_LANG['page_top_header'] = lang('settings_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('settings_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $help_link = "configuration-history-report";
        $this->set("help_link", $help_link);
        $this->setView();
    }
            
    function rank_performance_report() {
        $full_name = '';
        $date = date("Y-m-d");
        $this->set("date", $date);
        
        $title = lang('rank_performance_report');
        $this->set("title", $this->COMPANY_NAME . " | $title ");
        $this->url_permission('rank_status');

        $this->HEADER_LANG['page_top_header'] = "Rank Performance Report";
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = "Rank Performance Report";
        $this->HEADER_LANG['page_small_header'] = '';
        $user_name = $this->LOG_USER_NAME;
        $user_id = $this->LOG_USER_ID;        
        $this->load_langauge_scripts();
        
        $base_url = base_url() . 'user/select_report/rank_performance_report';

        if ($this->uri->segment(4) != "") {
            $page = $this->uri->segment(4);
        } else {
            $page = 0;
        }
        $user_details = $this->validation_model->getAllUserDetails($user_id);
        if(isset($user_details['user_detail_name'])){
            $full_name = $user_details['user_detail_name'];
        }
         
        $this->set('report_name', "");
        $help_link = "Top-earners";
        $this->set("report_date", '');
        $this->set("help_link", $help_link);

        $this->set("user_name", $user_name);
        $this->set("full_name", $full_name);

        $this->setView();
    }
    
     function roi_report() {
        $this->url_permission('roi_status');
        
        $title = lang('roi_report');
        $this->set("title", $this->COMPANY_NAME . " | $title ");

        $this->HEADER_LANG['page_top_header'] = lang('roi_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('roi_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $error_array = array();
        if ($this->session->userdata('inf_roi_report_view_error')) {
            $error_array = $this->session->userdata('inf_roi_report_view_error');
            $this->session->unset_userdata('inf_roi_report_view_error');
        }
        $this->set('error_array', $error_array);
        $this->set('error_count', count($error_array));

        $help_link = "ROI-report";
        $this->set("help_link", $help_link);

        $this->setView();
    }

    public function package_upgrade_report()
    {

         $title = lang('package_upgrade_report');
        $this->set("title", $this->COMPANY_NAME . " | $title ");

        $this->HEADER_LANG['page_top_header'] = lang('package_upgrade_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('package_upgrade_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $search_url="admin/package_upgrade_report";
        
        
        
        $package_names=$this->select_report_model->getPackageName();

        $this->set('package_names',$package_names);
        $this->set('search_url',$search_url);
        $this->setView();

    }
    function pv_report()
    {
        $dateRangeString = "";
        $title = lang('pv_details');
        $this->set('title', $this->COMPANY_NAME . ' |' . $title);

        $help_link = 'pv_details';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('pv_details');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('pv_details');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));
      if (($from_date != '') && ($to_date != '')) {
                if ($from_date > $to_date) {
                    $msg = lang('To-Date should be greater than From-Date');
                    $this->redirect($msg, 'pv_report', FALSE);
                }
        }   

        $user_name = $this->input->get('user_name') ?: $this->validation_model->getAdminUsername();
        $user_id = $this->validation_model->userNameToID($user_name);
        if (empty($user_id)) {
            $msg = lang('invalid_username');
            $this->redirect($msg, 'pv_report', false);
        }

        $this->set('from_report', $this->input->get('from_report'));
        
        $page = $this->input->get('offset') ?: 0;
        $count = $this->select_report_model->getPVHistoryCount($user_id,$from_date,$to_date);
        $pv_details = $this->select_report_model->getPVHistory($user_id, $page, $this->PAGINATION_PER_PAGE,$from_date, $to_date);

        $this->pagination->set_all('admin/select_report/pv_report', $count);
        $group_pv = $this->validation_model->getGrpPv($user_id);
        $personal_pv = $this->validation_model->getPersnlPv($user_id);

        $this->set('gpv', $group_pv);
        $this->set('pv', $personal_pv);
        
        $this->set('page_id', $page);
        $this->set('user_name', $user_name);
        $this->set('pv_details', $pv_details);

        $this->lang->load('amount_type', $this->LANG_NAME);

        $this->setView();
    }
    function subscription_report()
    {
        $title = lang('subscription_report');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $help_link = 'subscription_report';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('subscription_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('subscription_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        
        $user_name = $this->input->get('user_name') ?: $this->validation_model->getAdminUsername();
        $user_id = $this->validation_model->userNameToID($user_name);
        if (!$user_id) {
            $msg = lang('invalid_username');
            $this->redirect($msg, 'subscription_report', false);
        }
        $error_array = array();
        if ($this->session->userdata('inf_subscription_report_view_error')) {
            $error_array = $this->session->userdata('inf_profile_report_view_error');
            $this->session->unset_userdata('inf_profile_report_view_error');
        }

        $error_array_count = array();
        if ($this->session->userdata('inf_subscription_report_view_count_error')) {
            $error_array_count = $this->session->userdata('inf_profile_report_view_count_error');
            $this->session->unset_userdata('inf_profile_report_view_count_error');
        }

        $error_array_profile_count = array();
        if ($this->session->userdata('inf_subscription_report_count_error')) {
            $error_array_profile_count = $this->session->userdata('inf_profile_report_count_error');

            $this->session->unset_userdata('inf_profile_report_count_error');
        }

        $this->set('error_array', $error_array);
        $this->set('error_count', count($error_array));

        $this->set('error_array_count', $error_array_count);
        $this->set('error_single_count', count($error_array_count));

        $this->set('error_array_profile_count', $error_array_profile_count);
        $this->set('error_profile_count', count($error_array_profile_count));

        $this->setView();
    }

    function agent_credit_report()
    {
        $title = lang('agent_wallet_report');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $help_link = 'agent_wallet_report';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('agent_wallet_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('agent_wallet_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        
        $agent_user_name = $this->input->get('agent_user_name') ?: $this->validation_model->getAdminUsername();
        $user_id = $this->validation_model->userNameToID($agent_user_name);
        if (!$user_id) {
            $msg = lang('invalid_username');
            $this->redirect($msg, 'agent_wallet_report', false);
        }

        $this->set('user_name', $agent_user_name);

        $this->setView();
    }


}
