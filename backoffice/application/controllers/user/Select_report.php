<?php

require_once 'Inf_Controller.php';

class Select_report extends Inf_Controller {

    function __construct() {
        parent::__construct();
    }

    public function ajax_users_auto($user_name) {
        $letters = preg_replace("/[^a-z0-9 ]/si", "", $user_name);
        $str = $this->select_report_model->selectUser($letters);

        echo $str;
    }

    /*     * ****************************code by albert************************** */

    function bank_statement_report() {
        $user_type = $this->LOG_USER_TYPE;
        $this->set('user_type', "$user_type");
        $this->set('username', "User Name");
        $title = $this->lang->line('bank_statement_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->ARR_SCRIPT[0]["name"] = "ajax.js";
        $this->ARR_SCRIPT[0]["type"] = "js";
        
        $this->ARR_SCRIPT[1]["name"] = "ajax-dynamic-list.js";
        $this->ARR_SCRIPT[1]["type"] = "js";
        
        $this->ARR_SCRIPT[2]["name"] = "autoComplete.css";
        $this->ARR_SCRIPT[2]["type"] = "css";
        
        $this->ARR_SCRIPT[3]["name"] = "validate_profile.js";
        $this->ARR_SCRIPT[3]["type"] = "js";
        
        $this->load_langauge_scripts();
        $this->setView();
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

    function repurchase_report_new() {
        $title = lang('repurchase_report');
        $this->set("title", $this->COMPANY_NAME . " | $title ");

        $this->HEADER_LANG['page_top_header'] = lang('repurchase_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('repurchase_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
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
        
        function rank_performance_report() {
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
    
        $full_name = $user_details['user_detail_name'];

        $this->load->model('rank_model');
        $rank_achievement = $this->rank_model->getRankCriteria($user_id);
        
        $report_name = lang('rank_details');

        $this->set('report_name', $report_name);
        $help_link = "Top-earners";
        $this->set("report_date", '');
        $this->set("help_link", $help_link);
        $this->set("rank_achievement", $rank_achievement);
        $this->set("user_name", $user_name);
        $this->set("full_name", $full_name);

        $this->setView();
    }
    
        
    
    function get_rank_performance_detail($user_id){
            
            
            $details = array();
            $rank_details = array();
            $referal_count = 0;
            $current_rank = 0;
            $next_ran = 0;
            $next_rank = '';
            $referal_count = $this->validation_model->getReferalCount($user_id);
           
            $current_rank = $this->validation_model->getUserRank($user_id);            
            $personal_pv = $this->validation_model->getPersnlPv($user_id);
            
            if($current_rank!=''){  
                $rank_details = $this->select_report_model->selectRankDetails($current_rank); 
                $ref_count = $rank_details['referal_count'];
            }else{
                $ref_count = 0;
            }
            
            if(!$personal_pv){            
                $personal_pv =0;
            }
            
            $group_pv = $this->validation_model->getGrpPv($user_id);
            if(!$group_pv){
            $group_pv= 0;            
            }

            $next_rank = $this->select_report_model->getNextRank($ref_count);        
                
            if($current_rank != 0){
            $rank_details = $this->select_report_model->selectRankDetails($current_rank);                 
            $current_rank = $rank_details['rank_name']; 
            }else {
            $current_rank = 'NA';
            }
            
            if($next_rank){
            if($next_rank[0]->referal_count > $referal_count) {   
            $balance_referal_count = $next_rank[0]->referal_count - $referal_count;
            }else{
            $balance_referal_count = 0;    
            }
            if($next_rank[0]->personal_pv > $personal_pv) {
            $balance_personal_pv = $next_rank[0]->personal_pv - $personal_pv;
            }else {
            $balance_personal_pv = 0;  
            }
            if($next_rank[0]->gpv > $group_pv) {
            $balance_gpv = $next_rank[0]->gpv - $group_pv;
            }else {
            $balance_gpv = 0;  
            }            
            
            $next_ran = $next_rank[0]->rank_name;
            $next_referal_count = $next_rank[0]->referal_count;
            $next_pers_pv = $next_rank[0]->personal_pv;
            $next_grp_pv = $next_rank[0]->gpv;

            }else {
            $balance_referal_count = 'NA';
            $next_ran = 'NA';
            $next_referal_count = 'NA';
            $next_pers_pv = 'NA';
            $next_grp_pv = 'NA';
            $balance_gpv = 'NA';
            $balance_personal_pv = 'NA';            
            }
            $details ['referal_count'] = $referal_count;
            $details ['personal_pv'] = $personal_pv;
            $details ['group_pv'] = $group_pv;
            $details ['current_rank'] = $current_rank;
            $details ['next_rank'] = $next_ran;
            $details ['balance_referal_count'] = $balance_referal_count;
            $details ['balance_pers_pv'] = $balance_personal_pv;
            $details ['balance_grp_pv'] = $balance_gpv;
            $details ['next_referal_count'] = $next_referal_count;
            $details ['next_pers_pv'] = $next_pers_pv;
            $details ['next_grp_pv'] = $next_grp_pv;

            return $details;
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
        $this->lang->load('configuration_lang',$this->LANG_NAME);
        $this->setView();
    }
    
     function pv_report()
    {
        // dd('hre');
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

        
        $user_id = $this->LOG_USER_ID;
        if (empty($user_id)) {
            $msg = lang('invalid_username');
            $this->redirect($msg, 'pv_report', false);
        }

        $this->set('from_report', $this->input->get('from_report'));
        
        $page = $this->input->get('offset') ?: 0;
        $count = $this->select_report_model->getPVHistoryCount($user_id,$from_date,$to_date);
        $pv_details = $this->select_report_model->getPVHistory($user_id, $page, $this->PAGINATION_PER_PAGE,$from_date, $to_date);
        $this->pagination->set_all('user/select_report/pv_report', $count);
        
        $group_pv = $this->validation_model->getGrpPv($user_id);
        $personal_pv = $this->validation_model->getPersnlPv($user_id);

        $this->set('gpv', $group_pv);
        $this->set('pv', $personal_pv);
        
        $this->set('page_id', $page);
        $this->set('pv_details', $pv_details);

        $this->lang->load('amount_type', $this->LANG_NAME);

        $this->setView();
    }
    

}
