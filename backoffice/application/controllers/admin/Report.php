<?php

require_once 'Inf_Controller.php';

class Report extends Inf_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('select_report_model', '', TRUE);
        $this->load->model('activity_history_model');
        $this->lang->load('amount_type');
        $this->lang->load('select_report_lang');
    }

    function profile_report_view() {
        //FILTER DATA 
        $user_name = $this->input->get('user_name');
        
        //VALIDATION
        $user_id = $this->report_model->userNameToID($user_name);
        if ($user_id) {
            $this->session->set_userdata("inf_profile_report_view_user_name", $user_name);
        } else {
            $msg = lang('Invalid_Username');
            $this->redirect($msg, "select_report/admin_profile_report", false);
        }

        $userFullName = $this->validation_model->getUserFullName($user_id);
        $this->set('filterString', lang('member_name') . " : $userFullName($user_name)");

        $title = lang('report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('profile_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('profile_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $date = date("Y-m-d");
        $this->set("date", $date);

        //  DATA
        $profile_arr = $this->report_model->getProfileDetails($user_name);
        //print_r($profile_arr);die;
        $this->set("details", $this->security->xss_clean($profile_arr['details']));
        $this->set("user_name", $user_name);


        $help_link = "report";
        $this->set("help_link", $help_link);
        $this->set("report_date", '');
        $this->setView();
        
    }

    function validate_profile_report_view() {

        $this->lang->load('validation');
        $this->form_validation->set_rules('user_name', lang('user_name'), 'trim|required|strip_tags',[
         "required"=>lang('required'),
        ]);
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function profile_report() {
        $title = lang('profile_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);
        $this->HEADER_LANG['page_top_header'] = lang('profile_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('profile_report');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();

        //FILTER DATA
        $count = $this->input->get('count');


        //VALIDATION
        if ($count > 0) {
                $this->session->set_userdata('inf_profile_count', $count);
                $this->session->set_userdata("inf_profile_type", "one_count");
        } else {
                $msg = lang('invalid_entry');
                $this->redirect($msg, "select_report/admin_profile_report", FALSE);
        } 


        //DATA
        $profile_arr = $this->report_model->profileReport($count);

        $count = count($profile_arr);
        $this->set("profile_arr", $this->security->xss_clean($profile_arr));
        $this->set("count", $count);
        $help_link = "profile_report";
        $this->set("report_date", '');
        $this->set("help_link", $help_link);
        $this->setView();

    }

    function validate_profile_report_single_count() {

        $this->lang->load('validation');
        $this->form_validation->set_rules('count', lang('count'), 'trim|required|greater_than[0]|numeric|strip_tags',[
         "required"=>lang('required'),
         "numeric"=>lang('digits'),
        ]);
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function profile_report_multiple_count() {
        //FILTER DATA
        $count_from = $this->input->get('count_from');
        $count_to   = $this->input->get('count_to');

        //VALIDATION
        if ($count_from > 0 && $count_to > 0) {
            $this->session->set_userdata("inf_profile_type", "two_count");
            $this->session->set_userdata('inf_count_from', $count_from);
            $this->session->set_userdata('inf_count_to', $count_to);
        } else {
            $msg = lang('invalid_entry');
            $this->redirect($msg, "select_report/admin_profile_report", FALSE);
        }
        // $count_to = $count_from + $count_to - 1;
        $filterString = lang('showing') . "($count_from - $count_to)";
        $this->set('filterString', $filterString);

        $title = lang('profile_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);
        $this->HEADER_LANG['page_top_header'] = lang('profile_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('profile_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        //DATA
        $profile_arr = $this->report_model->profileReportFromTo($count_to, $count_from);
        $this->set("profile_arr", $this->security->xss_clean($profile_arr));
        
        $count = count($profile_arr);
        $this->set("count", $count);

        $this->set("count_from", $count_from);

        $help_link = "report";
        $this->set("report_date", '');
        $this->set("help_link", $help_link);
        $this->setView();
    }

    function validate_profile_report() {
        $this->lang->load('validation');
        $this->form_validation->set_rules('count_from', lang('count_from'), 'trim|required|greater_than[0]|numeric|strip_tags',[
         "required"=>lang('required'),
         "numeric"=>lang('digits')
        ]);
        $this->form_validation->set_rules('count_to', lang('count_to'), 'trim|required|greater_than[0]|numeric|strip_tags',[
          "required"=>lang('required'),
          "numeric"=>lang('digits')

        ]);
        $validate_form = $this->form_validation->run();

        return $validate_form;
    }

    function total_joining_daily() {

        $title = lang('user_joining_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $report_date = '';
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('user_joining_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('user_joining_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $this->report_header();

        if ($this->input->post('dailydate') && $this->validate_total_joining_daily()) {
            $today = (strip_tags($this->input->post('date', TRUE)));
            $this->session->set_userdata("inf_total_joining_daily", $today);
            $report_date = $today;
        } else {
            $error_array = $this->form_validation->error_array();
            $this->session->set_userdata('inf_total_joining_daily_error', $error_array);
            redirect('admin/select_report/total_joining_report');
        }
        if (!empty($this->session->userdata("inf_total_joining_daily"))) {
            $today = $this->session->userdata("inf_total_joining_daily");
            $report_date = $today;
            $todays_join = $this->report_model->getTodaysJoining($today);

            $count = count($todays_join);
            $this->set("count", $count);
            $this->set("todays_join", $todays_join);
        } else {
            $error_array = $this->form_validation->error_array();
            $this->session->set_userdata('inf_profile_report_view_error', $error_array);
            redirect('admin/select_report/total_joining_report');
        }
        $help_link = "downlaod_document";
        $this->set("help_link", $help_link);
        $this->set("report_date", $report_date);
        $this->setView();
    }

    function validate_total_joining_daily() {
        $this->form_validation->set_rules('date', lang('date'), 'trim|required|strip_tags',[
         "required"=>sprintf(lang('required_select'),lang('date'))
        ]);

        $validate_form = $this->form_validation->run();

        return $validate_form;
    }

    function total_joining_weekly() {
        //FILTER DATA
        // dd('hre');
        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));

        $dateRangeString = "";
        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $fromDate = ($from_date)? date('d M Y', strtotime($from_date)): lang('NA');
            $toDate = ($to_date)? date('d M Y', strtotime($to_date)): lang('NA');
            $dateRangeString .= lang('daterange') . " : ($fromDate - $toDate)";
        }
        $this->set('dateRangeString', $dateRangeString);


        $title = lang('user_joining_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('user_joining_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('user_joining_report');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->report_header();

        $this->load_langauge_scripts();

        $date = date("Y-m-d");
        $this->set("date", $date);


        if($from_date != '') {
            $from_date = $from_date . " ". "00:00:00";
        } if($to_date != '') { 
            $to_date = $to_date . " ". "23:59:59";
        }

        $date = date("Y-m-d");
        $report_date = '';
        $this->set("date", $date);

        //DATA
        $this->load->model('country_state_model');
        $week_join = $this->report_model->getWeeklyJoining($from_date, $to_date,$this->input->get('offset'), 
            $this->PAGINATION_PER_PAGE);
        
        $count1= $this->report_model->getCountWeeklyJoining($from_date, $to_date);
        $this->pagination->set_all('admin/total_joining_weekly', $count1);

        $count = count($week_join);
        $this->set("count", $count);
        $this->set("week_join", $week_join);
        
        $help_link = "downlaod_document";
        $this->set("help_link", $help_link);
        $this->set("showRegFee", $this->report_model->checkRegistrationFeeDisplay());
        $this->set("report_date", $report_date);
        $this->set("from_date", ($from_date)? date('Y-m-d', strtotime($from_date)): '');
        $this->set("to_date",($to_date)? date('Y-m-d', strtotime($to_date)): '');
        $this->set("daterange",$daterange);
        $this->setView();
    }

    function validate_total_joining_weekly() {

        $this->lang->load('validation');
        $this->form_validation->set_rules('week_date1', lang('start_date'), 'trim|required|strip_tags',[
         "required"=>sprintf(lang('required_select'),lang('start_date'))
        ]);
        $this->form_validation->set_rules('week_date2', lang('end_date'), 'trim|required|strip_tags',[
         "required"=>sprintf(lang('required_select'),lang('end_date')),
        ]);

        $validate_form = $this->form_validation->run();

        return $validate_form;
    }

    function total_payout_report_view() {

        $title = lang('total_bonus_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('total_bonus_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('total_bonus_report');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();

        $this->report_header();


        $date = date("Y-m-d");
        $this->set("date", $date);

        $total_payout = $this->report_model->getTotalPayout();
        $count = count($total_payout);
        $this->set("count", $count);
        $this->set("total_payout", $this->security->xss_clean($total_payout));

        $help_link = "report";
        $this->set("report_date", '');
        $this->set("help_link", $help_link);

        $this->setview();
    }

    function member_payout_report() {

        $title = lang('member_wise_bonus_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('member_wise_bonus_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('member_wise_bonus_report');
        $this->HEADER_LANG['page_small_header'] = '';


        $this->load_langauge_scripts();

        $this->report_header();

        $date = date("Y-m-d");
        $this->set("date", $date);
        $is_valid_username = false;

        if ($this->input->post('user_submit') && $this->validate_member_payout_report()) {
            $user_mob_name = ($this->input->post('user_name', TRUE));
            $this->session->set_userdata("is_valid_user_name", $user_mob_name);
            $user_id = $this->validation_model->userNameToID($user_mob_name);
            $is_valid_username = $this->validation_model->isUserAvailable($user_id);
            if (!$is_valid_username) {
                $msg = lang('invalid_user_name');
                $this->redirect($msg, 'select_report/total_payout_report', FALSE);
            }
            $this->session->set_userdata("inf_user_name_payout", $user_mob_name);
        } else {
            $error_array_user = $this->form_validation->error_array();
            $this->session->set_userdata('inf_member_payout_report_error', $error_array_user);
            redirect('admin/select_report/total_payout_report');
        }
        if (!empty($this->session->userdata("inf_user_name_payout"))) {
            $user_mob_name = $this->session->userdata("inf_user_name_payout");
            $member_payout = $this->report_model->getMemberPayout($user_mob_name);
            $count = count($member_payout);
            $this->set("count", $count);
            $this->set("member_payout", $this->security->xss_clean($member_payout));
        } else {
            $error_array = $this->form_validation->error_array();
            $this->session->set_userdata('inf_profile_report_view_error', $error_array);
            redirect('admin/select_report/total_payout_report');
        }

        $help_link = "report";
        $this->set("report_date", '');
        $this->set("help_link", $help_link);
        $this->setView();
    }

    function validate_member_payout_report() {
        $this->form_validation->set_rules('user_name', lang('user_name'), 'trim|required|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function weekly_payout_report() {
        $filterUserString = "";
        $dateRangeString = "";

        $title = lang('total_bonus_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('total_bonus_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('total_bonus_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $this->report_header();

        $date = date("Y-m-d");
        $report_date = '';
        $this->set("date", $date);
        $user_id = '';
        
        //FILTER DATA
        
        $user_name = $this->input->get('user_name');
        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));
      

        //VALIDATION
        if($this->input->get('user_name')){
            $user_id = $this->report_model->userNameToID($user_name);
            $is_valid_username = $this->validation_model->isUserAvailable($user_id);
            if (!$is_valid_username) {
                    $msg = lang('invalid_user_name');
                    $this->redirect($msg, 'select_report/total_payout_report', FALSE);
            }
            $filterUserString = lang('member_name') . ": {$this->validation_model->getUserFullName($user_id)} ($user_name)";
        }

        if ($from_date != '' && $to_date != '') {
                $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
        } else if ($from_date != '') {
                $report_date = lang('from') . "\t" . $from_date;
        } else if ($to_date != '') {
                $report_date = lang('to') . "\t" . $to_date;
        } else {
                $report_date = '';
        }

        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $fromDate = ($from_date)? date('d M Y', strtotime($from_date)): lang('NA');
            $toDate = ($to_date)? date('d M Y', strtotime($to_date)): lang('NA');
            $dateRangeString .= lang('daterange') . " : ($fromDate - $toDate)";
        }

        if ($from_date)
                $from_date = $from_date . " 00:00:00";
        if ($to_date)
                $to_date = $to_date . " 23:59:59";


         if (($from_date != '') && ($to_date != '')) {
                if ($from_date > $to_date) {
                    $msg = lang('To-Date should be greater than From-Date');
                    $this->redirect($msg, 'select_report/total_payout_report', FALSE);
                }
        }   
        
        $showTDS = "yes";
        $showServiceCharge = "yes";
        $showAmountPayable = "yes";
        if(($this->validation_model->getConfig('tds') <= 0) && ($this->report_model->getGrandTotalTDS() <= 0)) {
            $showTDS = "no";
        }
        if(($this->validation_model->getConfig('service_charge') <= 0) && ($this->report_model->getGrandTotalServiceCharge() <= 0)) {
            $showServiceCharge = "no";
        }
        if($showServiceCharge == "no" && $showTDS == "no")
            $showAmountPayable = "no";
        $this->set("showTDS", $showTDS);
        $this->set("showServiceCharge", $showServiceCharge);
        $this->set("showAmountPayable", $showAmountPayable);
        
        //DATA 
        $total_amount = 0;
        $total_tds = 0;
        $total_service_charge = 0;
        $total_amount_payable = 0;
        $weekly_payout = $this->report_model->getTotalPayout($from_date, $to_date, $user_id,$this->input->get('offset'),$this->PAGINATION_PER_PAGE);
        foreach ($weekly_payout as $row) {
            $total_amount += $row['total_amount'];
            $total_tds += $row['tds'];
            $total_service_charge += $row['service_charge'];
            $total_amount_payable += $row['amount_payable'];
        }
        // dump('here');
        $count1 = $this->report_model->getCountTotalPayout($from_date, $to_date, $user_id);
        // dd($count1);
        $this->pagination->set_all('admin/weekly_payout_report',$count1);
        $count = count($weekly_payout);
        $this->set("count", $count);
        $this->set("weekly_payout", $this->security->xss_clean($weekly_payout)); 
        $this->set("total_amount", $total_amount);
        $this->set("total_tds", $total_tds);
        $this->set("total_service_charge", $total_service_charge);
        $this->set("total_amount_payable", $total_amount_payable);
    
        $this->set('dateRangeString', $dateRangeString);
        $this->set('filterUserString', $filterUserString);
        
        $help_link = "report";
        $this->set("from_date", ($from_date)? date('Y-m-d', strtotime($from_date)): '');
        $this->set("to_date",($to_date)? date('Y-m-d', strtotime($to_date)): '');
        $this->set("daterange",$daterange);
        $this->set('user_name', $user_name);
        $this->set("report_date", $report_date);
        $this->set("help_link", $help_link);
        $this->setView(); 
    }

    function validate_weekly_payout_report() {
        $this->form_validation->set_rules('week_date1', lang('start_date'), 'trim|strip_tags');
        $this->form_validation->set_rules('week_date2', lang('end_date'), 'trim|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function payout_released_report_daily() {

        $title = lang('payout_release_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('payout_release_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('payout_release_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();


        $date = date("Y-m-d");
        $from_date = '';
        $this->set("date", $date);

        if (($this->input->post('payout_released')) && $this->validate_payout_released_report_daily()) {

            $from_date = (strip_tags($this->input->post('week_date1', TRUE)));
            $this->session->set_userdata("inf_released_report_daily", $from_date);
            $ewallt_req_details = $this->report_model->getDailyReleasedPayoutDetails($from_date);
            $count = count($ewallt_req_details);
            $this->set("binary_details", $ewallt_req_details);
            $this->set("count", $count);
        } else {
            $error_array = $this->form_validation->error_array();
            $this->session->set_userdata('inf_payout_released_report_daily_error', $error_array);
            redirect('admin/select_report/payout_release_report');
        }

        $help_link = "downlaod_document";
        $this->set("help_link", $help_link);
        $this->set('report_date', $from_date);
        $this->setView();
    }

    function validate_payout_released_report_daily() {
        $this->form_validation->set_rules('week_date1', lang('date'), 'trim|required|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function payout_released_report_weekly() {

        $title = lang('payout_release_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('payout_release_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('payout_release_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();


        $date = date("Y-m-d");
        $report_date = '';
        $this->set("date", $date);

        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));

        $dateRangeString = "";
        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $fromDate = ($from_date)? date('d M Y', strtotime($from_date)): lang('NA');
            $toDate = ($to_date)? date('d M Y', strtotime($to_date)): lang('NA');
            $dateRangeString .= lang('daterange') . " : ($fromDate - $toDate)";
        }

        if($from_date != ''){
        $from_date = $from_date . " ". "00:00:00";
        }if($to_date != ''){ 
        $to_date = $to_date . " ". "23:59:59";
        }
        

          if (($from_date != '') && ($to_date != '')) {
            if (($this->input->get('from_date')) > ($this->input->get('to_date'))) {
                $msg = lang('To-Date should be greater than From-Date');
                $this->redirect($msg, 'select_report/payout_release_report', FALSE);
            }
        }

        if ($from_date != '' && $to_date != '') {
                $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
            } else if ($from_date != '') {
                $report_date = lang('from') . "\t" . $from_date;
            } else if ($to_date != '') {
                $report_date = lang('to') . "\t" . $to_date;
            } else {
                $report_date = '';
        }




        $ewallt_req_details = $this->report_model->getReleasedPayoutDetails($from_date, $to_date,$this->input->get('offset'), 
            $this->PAGINATION_PER_PAGE);
         $count1 = $this->report_model->getCountReleasedPayoutDetails($from_date, $to_date); 
         $this->pagination->set_all('admin/payout_released_report_weekly',$count1);   
        $totalPayout = 0;
        $totalPayoutFee = 0;
        foreach($ewallt_req_details as $row) {
            $totalPayout += $row['paid_amount'];
            $totalPayoutFee += $row['payout_fee'];    
        }
        $count = count($ewallt_req_details);

        // if (($this->input->post('payout_released')) && $this->validate_payout_released_report_weekly()) {
        //     if (!($this->input->post('from_date_weekly')) && !($this->input->post('to_date_weekly'))) {
        //         $msg = lang('You_must_select_a_date');
        //         $this->redirect($msg, 'select_report/payout_release_report', FALSE);
        //     }
        //     if (($this->input->post('from_date_weekly') != '') && ($this->input->post('to_date_weekly') != '')) {
        //         if (($this->input->post('from_date_weekly')) > ($this->input->post('to_date_weekly'))) {
        //             $msg = lang('To-Date should be greater than From-Date');
        //             $this->redirect($msg, 'select_report/payout_release_report', FALSE);
        //         }
        //     }

        //     $from_date = (strip_tags($this->input->post('from_date_weekly', TRUE)));
        //     $to_date = (strip_tags($this->input->post('to_date_weekly', TRUE)));
        //     $this->session->set_userdata("inf_released_report_from_date", $from_date);
        //     $this->session->set_userdata("inf_released_report_to_date", $to_date);
        //     $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
        //     $ewallt_req_details = $this->report_model->getReleasedPayoutDetails($from_date, $to_date);
        //     $count = count($ewallt_req_details);
        //     $this->set("binary_details", $ewallt_req_details);
        //     $this->set("count", $count);
        // } else {
        //     $error_array = $this->form_validation->error_array();
        //     $this->session->set_userdata('inf_payout_released_report_daily_error', $error_array);
        //     redirect('admin/select_report/payout_release_report');
        // }
        
        $this->set("payoutFeeDisplay", $this->report_model->checkPayoutFeeDisplay());
        $this->set("dateRangeString", $dateRangeString);
        $this->set("binary_details", $ewallt_req_details);
        $this->set("totalPayout", $totalPayout);
        $this->set("totalPayoutFee", $totalPayoutFee);
        $this->set("count", $count);
        $this->set("report_date", $report_date);
        $this->set("from_date", $from_date);
        $this->set("to_date", $to_date);
        $this->setView();
    }

    function validate_payout_released_report_weekly() {
        $this->form_validation->set_rules('from_date_weekly', lang('date'), 'trim|strip_tags');
        $this->form_validation->set_rules('to_date_weekly', lang('date'), 'trim|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function payout_pending_report_weekly() {
        $title = lang('payout_pending_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('payout_pending_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('payout_pending_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $date = date("Y-m-d");
        $report_date = '';
        $this->set("date", $date);

        $this->load_langauge_scripts();

        $this->report_header();


        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));

        $dateRangeString = "";
        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $fromDate = ($from_date)? date('d M Y', strtotime($from_date)): lang('NA');
            $toDate = ($to_date)? date('d M Y', strtotime($to_date)): lang('NA');
            $dateRangeString .= lang('daterange') . " : ($fromDate - $toDate)";
        }

        if($from_date != ''){
        $from_date = $from_date . " ". "00:00:00";
        }if($to_date != ''){ 
        $to_date = $to_date . " ". "23:59:59";
        }
        

          if (($from_date != '') && ($to_date != '')) {
            if (($this->input->get('from_date')) > ($this->input->get('to_date'))) {
                $msg = lang('To-Date should be greater than From-Date');
                $this->redirect($msg, 'select_report/payout_release_report', FALSE);
            }
        }

         if ($from_date != '' && $to_date != '') {
                $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
            } else if ($from_date != '') {
                $report_date = lang('from') . "\t" . $from_date;
            } else if ($to_date != '') {
                $report_date = lang('to') . "\t" . $to_date;
            } else {
                $report_date = '';
        }



        $ewallt_req_details = $this->report_model->getPayoutPendingDetails($from_date, $to_date,$this->input->get('offset'), 
            $this->PAGINATION_PER_PAGE);
        $totalPayout = 0;
        $totalPayoutFee = 0;
        foreach($ewallt_req_details as $row) {
            $totalPayout += $row['paid_amount'];
            $totalPayoutFee += $row['payout_fee'] ?? 0;
        }
        foreach($ewallt_req_details as $key=>$value) {
            $ewallt_req_details[$key]['payout_fee'] = $value['payout_fee'] ?? 0;
        }
        
        $count1 = $this->report_model->getCountPayoutPendingDetails($from_date, $to_date);
        $this->pagination->set_all('admin/payout_pending_report_weekly',$count1);
        $count = count($ewallt_req_details);



        $this->set("payoutFeeDisplay", $this->report_model->checkPayoutFeeDisplay());
        $this->set("binary_details", $ewallt_req_details);
        $this->set("dateRangeString", $dateRangeString);
        $this->set("totalPayout", $totalPayout);
        $this->set("totalPayoutFee", $totalPayoutFee);
        $this->set("count", $count);
        $this->set("report_name", lang('payout_pending_report'));
        $this->set("report_date", $report_date);
        $this->set("from_date", $from_date);
        $this->set("to_date", $to_date);
        $this->setview('admin/report/payout_pending_report_weekly');
    }

    function validate_payout_pending_report_weekly() {

        $this->form_validation->set_rules('from_date_pending', lang('date'), 'trim|strip_tags');
        $this->form_validation->set_rules('to_date_pending', lang('date'), 'trim|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function sales_report_view() {

        $title = lang('report');
        $this->set("title", $this->COMPANY_NAME . " | $title");
        $this->url_permission('product_status');

        $this->HEADER_LANG['page_top_header'] = lang('sales_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('sales_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $this->set("date_submission", lang('date_submission'));

        $report_name = lang('sales_report');
        $this->set('report_name', "$report_name");


        $date = date("Y-m-d");
        $report_date = '';
        $product_type = '';
        $this->set("date", $date);

        //FILTER DATA
        
        $product_id   = $this->input->get('product_id');
        $package_id   = $this->input->get('prod');
        $daterange    = $this->input->get('daterange') ?: 'all';
        $product_type = $this->input->get('product_id');
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));

        if($from_date != ''){
        $from_date = $from_date . " ". "00:00:00";
        }if($to_date != ''){ 
        $to_date = $to_date . " ". "23:59:59";
        }
        
        //VALIDATION

        if (($from_date != '') && ($to_date != '')) {
                if ($from_date > $to_date) {
                    $msg = lang('To-Date should be greater than From-Date');
                    $this->redirect($msg, 'select_report/sales_report', FALSE);
                }
        }

        if ($from_date != '' && $to_date != '') {
                $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
            } else if ($from_date != '') {
                $report_date = lang('from') . "\t" . $from_date;
            } else if ($to_date != '') {
                $report_date = lang('to') . "\t" . $to_date;
            } else {
                $report_date = '';
        }

        
        // DATA

        if ($this->input->get('product_id') == "repurchase") {
                $report_arr = $this->report_model->productRepurchaseSalesReport($package_id, $from_date, $to_date);
            } else {
                $report_arr = $this->report_model->salesReport($from_date, $to_date, $package_id);
        }

        $count = count($report_arr);
        $this->set('report_arr', $this->security->xss_clean($report_arr));
        $this->set('count', $count);
        $this->set('from_date', ($from_date)? date('Y-m-d', strtotime($from_date)): '');
        
        $this->set('to_date', ($to_date)? date('Y-m-d', strtotime($to_date)): '');
        $this->set('package_id', $package_id);
        $help_link = "report";
        $this->set("product_type", $product_type);
        $this->set("help_link", $help_link);
        $this->set("report_date", $report_date);
        $this->setView();
    }

    function validate_sales_report_view() {

        $this->form_validation->set_rules('week_date1', lang('date'), 'trim|strip_tags');
        $this->form_validation->set_rules('week_date2', lang('date'), 'trim|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function product_sales_report() {

        $title = lang('report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('sales_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('sales_report');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();


        $this->set("date_submission", lang('date_submission'));
        $this->set("payment_method", lang('Payment_method'));
        $report_name = lang('sales_report');
        $this->set('report_name', "$report_name");


        $date = date("Y-m-d");
        $this->set("date", $date);
        $product_type = "register";

        if (($this->input->post('user_submit')) && $this->validate_product_sales_report()) {
            $prod_id = (strip_tags($this->input->post('product_id', TRUE)));
            $this->session->set_userdata("inf_product_sales_id", $prod_id);
        } elseif (($this->input->post('user_submit_repurchase')) && $this->validate_product_sales_report()) {
            $prod_id = (strip_tags($this->input->post('product_id', TRUE)));
            $this->session->set_userdata("inf_product_sales_id", $prod_id);
            $product_type = "repurchase";
        } else {
            $error_array_sales = $this->form_validation->error_array();
            $this->session->set_userdata('inf_product_sales_report_error', $error_array_sales);
            redirect('admin/select_report/sales_report');
        }
        ///////////////////////////////////
        if (!empty($this->session->userdata("inf_product_sales_id"))) {
            $prod_id = $this->session->userdata("inf_product_sales_id");
            if ($product_type == "repurchase")
                $sales_report_arr = $this->report_model->productRepurchaseSalesReport($prod_id);
            else
                $sales_report_arr = $this->report_model->productSalesReport($prod_id);
            $count = count($sales_report_arr);
            $this->set('sales_report_arr', $this->security->xss_clean($sales_report_arr));
            $this->set('count', $count);
        } else {
            $error_array = $this->form_validation->error_array();
            $this->session->set_userdata('inf_profile_report_view_error', $error_array);
            redirect('admin/select_report/sales_report');
        }
        $help_link = "report";
        $this->set("report_date", '');
        $this->set("product_type", $product_type);
        $this->set("help_link", $help_link);
        $this->setView();
    }

    function validate_product_sales_report() {

        $this->form_validation->set_rules("product_id", lang('product'), 'trim|required|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function report_header() {

        $this->set("tran_Welcome_to", $this->lang->line('Welcome_to'));
        $this->set("tran_O", $this->lang->line('O'));
        $this->set("tran_I", $this->lang->line('I'));
        $this->set("tran_Floor", $this->lang->line('Floor'));
        $this->set("tran_em", $this->lang->line('em'));
        $this->set("tran_addr", $this->lang->line('addr'));
        $this->set("tran_comp", $this->lang->line('comp'));
        $this->set("tran_ph", $this->lang->line('ph'));
        $this->set("tran_nfinite", $this->lang->line('nfinite'));
        $this->set("tran_pen", $this->lang->line('pen'));
        $this->set("tran_ource", $this->lang->line('ource'));
        $this->set("tran_olutions", $this->lang->line('olutions'));
        $this->set("tran_S", $this->lang->line('S'));
        $this->set("tran_Date", $this->lang->line('Date'));
        $this->set("tran_email", $this->lang->line('email'));
        $this->set("tran_address", $this->lang->line('address'));
        $this->set("tran_phone", $this->lang->line('phone'));
        $this->set("tran_click_here_print", $this->lang->line('click_here_print'));
    }

    function rank_achievers_report_view() {

        $title = lang('report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('rank_achieve_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('rank_achieve_report');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();

        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));
        
        $dateRangeString = "";
        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $fromDate = ($from_date)? date('d M Y', strtotime($from_date)): lang('NA');
            $toDate = ($to_date)? date('d M Y', strtotime($to_date)): lang('NA');
            $dateRangeString .= lang('daterange') . " : ($fromDate - $toDate)";
        }

        $report_name = $this->lang->line('rank_achieve_report');
        $this->set('report_name', "$report_name");

        $date = date("Y-m-d");
        $report_date = '';
        $this->set("date", $date);
        $ranks = array();
        
        //FILTER DATA
        $ranks     = $this->input->get('ranks', TRUE);
        $rankFilterString = "";
        if($ranks && count($ranks)) {
            $rankFilterString = lang('rank') . " : ";
            foreach($ranks as $rk) {
                $rankFilterString .= $this->validation_model->getRankName($rk) . ", ";
            }
            $rankFilterString = substr($rankFilterString, 0, -2);
        }
        
        
        //VALIDATION 

        // if($ranks == ''){
             
        //      $msg = lang('Please Select Atleast One Criteria.');
        //      $this->redirect($msg, 'select_report/rank_achievers_report', FALSE);
        // }
        if (($from_date != '') && ($to_date != '')) {
            if ($from_date > $to_date) {
                $msg = lang('To-Date should be greater than From-Date');
                $this->redirect($msg, 'select_report/rank_achievers_report', FALSE);
            }
        }

        if ($from_date != '' && $to_date != '') {
                $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
            } else if ($from_date != '') {
                $report_date = lang('from') . "\t" . $from_date;
            } else if ($to_date != '') {
                $report_date = lang('to') . "\t" . $to_date;
            } else {
                $report_date = '';
            }
            if ($from_date != '') {
                $from_date = $from_date . " 00:00:00";
            } else {
                $from_date = '';
            }
            if ($to_date != '') {
                $to_date = $to_date . " 23:59:59";
            } else {
                $to_date = '';
            }


        //DATA
        
        $ranked_user_details = array();
        $ranked_user_details = $this->report_model->rankedUsers($ranks, $from_date, $to_date,$this->input->get('offset'), 
            $this->PAGINATION_PER_PAGE);
        $count1= $this->report_model->countOfrankedUsers($ranks, $from_date, $to_date); 
        $this ->pagination->set_all('admin/rank_achievers_report_view',$count1);   
        $count = count($ranked_user_details);
        $this->set('report_arr', $ranked_user_details);
        $this->set('count', $count);    
        
        
        $rank_data = ($ranks)?implode(",", $ranks):"";
        
        $help_link = "report";
        
        $rank_arr = $this->select_report_model->getAllRank();
        $error_array = array();
        if ($this->session->userdata('inf_rank_achievers_report_error')) {
            $error_array = $this->session->userdata('inf_rank_achievers_report_error');
            $this->session->unset_userdata('inf_rank_achievers_report_error');
        }

        $this->set('dateRangeString', $dateRangeString);
        $this->set('rankFilterString', $rankFilterString);
        $this->set('rank_data', $rank_data);
        $this->set('daterange', $daterange);
        $this->set('from_date', ($from_date)? date('Y-m-d', strtotime($from_date)): '');
        $this->set('to_date', ($to_date)? date('Y-m-d', strtotime($to_date)): '');
        $this->set("report_date", $report_date);
        $this->set("help_link", $help_link);

        $this->set("rank_arr", $rank_arr);
        $this->set('error_array', $error_array);
        $this->set('error_count', count($error_array));
        $this->setView();
    }

    function validate_rank_achievers_report_view() {

        $this->form_validation->set_rules('week_date1', lang('start_date'), 'trim|strip_tags');
        $this->form_validation->set_rules('week_date2', lang('end_date'), 'trim|strip_tags');
        $this->form_validation->set_rules('ranks[]', lang('rank'), 'trim|strip_tags');
        $validate_form = $this->form_validation->run();

        return $validate_form;
    }

    function commission_report_view() {

        $filterUserString = "";
        $dateRangeString = "";
        $typeFilterString = "";

        $title = lang('commission_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('commission_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('commission_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        
        $date = date("Y-m-d");
        $report_date = '';
        $this->set("date", $date);
        $date1 = date('Y-m-d:H:i:s');
        $user_id = "";
        $user_name = "";

        
        //FILTER DATA
        $type       = $this->input->get('amount_type');
        $user_name  = $this->input->get('user_name');
        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));

        $dateRangeString = "";
        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $fromDate = ($from_date)? date('d M Y', strtotime($from_date)): lang('NA');
            $toDate = ($to_date)? date('d M Y', strtotime($to_date)): lang('NA');
            $dateRangeString .= lang('daterange') . " : ($fromDate - $toDate)";
        }


        if($from_date != ''){
        $from_date = $from_date . " ". "00:00:00";
        }if($to_date != ''){ 
        $to_date = $to_date . " ". "23:59:59";
        }
        

        //validation
        if (($from_date != '') && ($to_date != '')) {
            if (($this->input->get('from_date')) > ($this->input->get('to_date'))) {
                $msg = lang('To-Date should be greater than From-Date');
                $this->redirect($msg, 'select_report/commission_report', FALSE);
            }
        }

        if ($this->input->get("amount_type") != '') {
                $i = 0;
                foreach ($type as $t) {
                    if ($type[$i] == 'table_fill_commission') {
                        if ($this->MODULE_STATUS['table_status'] == "yes" || $this->MODULE_STATUS['mlm_plan'] == "Board") {
                            $type[$i] = 'board_commission';
                        }
                    }
                    $typeFilterString .= lang($type[$i]) . ", ";
                    $i++;
                }
            } else {
                $type = '';
        }
        if($typeFilterString != "")
            $typeFilterString = lang('amount_type') . " : " . substr($typeFilterString, 0, -2);

        $this->session->set_userdata('inf_commision_type', $type);
        if ($this->input->get("user_name")) {
                $user_id = $this->report_model->userNameToID($user_name);
                if (!$user_id) {
                    $msg = lang('invalid_username');
                    $this->redirect($msg, 'select_report/commission_report', FALSE);
                }
                $filterUserString = lang('member_name') . ": {$this->validation_model->getUserFullName($user_id)} ($user_name)";
        }
        $this->set('user_name', $user_name);

        if ($from_date != '' && $to_date != '') {
                $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
            } else if ($from_date != '') {
                $report_date = lang('from') . "\t" . $from_date;
            } else if ($to_date != '') {
                $report_date = lang('to') . "\t" . $to_date;
            } else {
                $report_date = '';
        }


        //DATA

        $details = $this->report_model->getCommisionDetails($type, $from_date, $to_date, $user_id,$this->PAGINATION_PER_PAGE,$this->input->get('offset'));
        $count1=$this->report_model->getCountCommisionDetails($type, $from_date, $to_date, $user_id);
        $this->pagination->set_all('admin/commission_report_view',$count1);
        
            
        $count = count($details);

        $total_amount = 0;
        $total_amount_payable = 0;
        $total_tds = 0;
        $total_service_charge = 0;
        foreach($details as $detail) {
            $total_amount += $detail['total_amount'];
            $total_amount_payable += $detail['amount_payable'];
            $total_tds += $detail['tds'];
            $total_service_charge += $detail['service_charge'];
        }

        $showTDS = "yes";
        $showServiceCharge = "yes";
        $showAmountPayable = "yes";
        if(($this->validation_model->getConfig('tds') <= 0) && ($this->report_model->getGrandTotalTDS() <= 0)) {
            $showTDS = "no";
        }
        if(($this->validation_model->getConfig('service_charge') <= 0) && ($this->report_model->getGrandTotalServiceCharge() <= 0)) {
            $showServiceCharge = "no";
        }
        if($showServiceCharge == "no" && $showTDS == "no")
            $showAmountPayable = "no";

        $commission_types = $this->ewallet_model->getEnabledBonusList();
        $received_commission_types = $this->ewallet_model->getReceivedBonusList();
        $commission_types = array_unique(array_merge($commission_types, $received_commission_types));
        $count_commission = count($commission_types);
        $this->set('commission_types', $commission_types);
        $this->set("showTDS", $showTDS);
        $this->set("showServiceCharge", $showServiceCharge);
        $this->set("showAmountPayable", $showAmountPayable);

        $this->set("filterUserString", $filterUserString);
        $this->set("dateRangeString", $dateRangeString);
        $this->set("typeFilterString", $typeFilterString);

        $this->set('total_amount', number_format((float)$total_amount, 2, '.', ''));
        $this->set('total_amount_payable', number_format((float)$total_amount_payable, 2, '.', ''));
        $this->set('total_tds', number_format((float)$total_tds, 2, '.', ''));
        $this->set('total_service_charge', number_format((float)$total_service_charge, 2, '.', ''));
        $this->set('details', $details);
        $this->set('count', $count);
        $this->set('date1', $date1);
        $this->set('type', $type);
        $help_link = "report";
        $this->set("help_link", $help_link);
        $this->set("report_date", $report_date);
        $this->set("from_date", ($from_date)? date('Y-m-d', strtotime($from_date)): '');
        $this->set("to_date",($to_date)? date('Y-m-d', strtotime($to_date)): '');
        $this->set("daterange",$daterange);
        $this->setView();

    }

    function validate_commission_report_view() {

        $this->form_validation->set_rules('user_name', lang('user_name'), 'trim|strip_tags');
        $this->form_validation->set_rules('from_date', lang('from_date'), 'trim|strip_tags');
        $this->form_validation->set_rules('to_date', lang('to_date'), 'trim|strip_tags');
        $this->form_validation->set_rules('package_combo[]', lang('package_combo'), 'trim|strip_tags');
        $validate_form = $this->form_validation->run();

        return $validate_form;
    }

    function epin_report_view() {

        $title = lang('epin_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");
        $this->url_permission('pin_status');

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('epin_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('epin_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();


        $date = date("Y-m-d");
        $this->set("date", $date);
        $pin_details = $this->report_model->getUsedPin();
        $count = count($pin_details);
        $this->set("count", $count);
        $this->set("pin_details", $pin_details);
        $help_link = "report";
        $this->set("report_date", '');
        $this->set("help_link", $help_link);
        $this->setview();
    }

    function activate_deactivate_report() {
        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));

        $dateRangeString = "";
        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $fromDate = ($from_date)? date('d M Y', strtotime($from_date)): lang('NA');
            $toDate = ($to_date)? date('d M Y', strtotime($to_date)): lang('NA');
            $dateRangeString .= lang('daterange') . " : ($fromDate - $toDate)";
        }
        $this->set('dateRangeString', $dateRangeString);

        $title = lang('activate_deactivate_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('activate_deactivate_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('activate_deactivate_report');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->report_header();

        $this->load_langauge_scripts();


        $date = date("Y-m-d");
        $this->set("date", $date);


        if($from_date != ''){
        $from_date = $from_date . " ". "00:00:00";
        }if($to_date != ''){ 
        $to_date = $to_date . " ". "23:59:59";
        }
        $report_date = '';
        if ($from_date != '' && $to_date != '') {
            $report_date = lang('from') . "\t" . date("d-m-Y", strtotime($from_date)) . "\t" . lang('to') . "\t" . date("d-m-Y", strtotime($to_date));
        } else if ($from_date != '') {
            $report_date = lang('from') . "\t" . date("d-m-Y", strtotime($from_date));
        } else if ($to_date != '') {
            $report_date = lang('to') . "\t" . date("d-m-Y", strtotime($to_date));
        } else {
            $report_date = '';
        }

        $activate_deactive = $this->report_model->getAciveDeactiveUserDetails($from_date, $to_date,$this->input->get('offset'), $this->PAGINATION_PER_PAGE);
            //print_r($this->PAGINATION_PER_PAGE);die;
        $count1 = $this->report_model->getCountAciveDeactiveUserDetails($from_date, $to_date); 
        $this->pagination->set_all('admin/activate_deactivate_report_view', $count1);   
        $count = count($activate_deactive);
        $this->set("count", $count);
        $this->set("activate_deactive", $activate_deactive);
        $help_link = "downlaod_document";
        $this->set("help_link", $help_link);
        $this->set("report_date", $report_date);
        $this->set("daterange",$daterange);
        $this->set("from_date",$from_date);
        $this->set("to_date",$to_date);
        $this->setView('admin/report/activate_deactivate_report_view');
        // $this->setView('admin/select_report/activate_deactivate_report');
    }

    function activate_deactivate_report_view() {
        //FILTER DATA
        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));

        $dateRangeString = "";
        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $fromDate = ($from_date)? date('d M Y', strtotime($from_date)): lang('NA');
            $toDate = ($to_date)? date('d M Y', strtotime($to_date)): lang('NA');
            $dateRangeString .= lang('daterange') . " : ($fromDate - $toDate)";
        }
        $this->set('dateRangeString', $dateRangeString);

        $title = lang('activate_deactivate_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('activate_deactivate_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('activate_deactivate_report');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->report_header();

        $this->load_langauge_scripts();


        $date = date("Y-m-d");
        $this->set("date", $date);


        if($from_date != ''){
        $from_date = $from_date . " ". "00:00:00";
        }if($to_date != ''){ 
        $to_date = $to_date . " ". "23:59:59";
        }
        $report_date = '';
        if ($from_date != '' && $to_date != '') {
            $report_date = lang('from') . "\t" . date("d-m-Y", strtotime($from_date)) . "\t" . lang('to') . "\t" . date("d-m-Y", strtotime($to_date));
        } else if ($from_date != '') {
            $report_date = lang('from') . "\t" . date("d-m-Y", strtotime($from_date));
        } else if ($to_date != '') {
            $report_date = lang('to') . "\t" . date("d-m-Y", strtotime($to_date));
        } else {
            $report_date = '';
        }

        $activate_deactive = $this->report_model->getAciveDeactiveUserDetails($from_date, $to_date,$this->input->get('offset'), $this->PAGINATION_PER_PAGE);
            //print_r($this->PAGINATION_PER_PAGE);die;
        $count1 = $this->report_model->getCountAciveDeactiveUserDetails($from_date, $to_date); 
        $this->pagination->set_all('admin/activate_deactivate_report_view', $count1);   
        $count = count($activate_deactive);
        $this->set("count", $count);
        $this->set("activate_deactive", $activate_deactive);
        $help_link = "downlaod_document";
        $this->set("help_link", $help_link);
        $this->set("report_date", $report_date);
        $this->set("from_date", ($from_date)? date('Y-m-d', strtotime($from_date)): '');
        $this->set("to_date",($to_date)? date('Y-m-d', strtotime($to_date)): '');
        $this->set("daterange",$daterange);
        $this->setView();
    }

    function validate_activate_deactivate_report_view() {

        $this->form_validation->set_rules('week_date1', lang('start_date'), 'trim|required|strip_tags');
        $this->form_validation->set_rules('week_date2', lang('end_date'), 'trim|required|strip_tags');

        $validate_form = $this->form_validation->run();

        return $validate_form;
    }

    function activate_deactivate_daily() {

        $title = lang('activate_deactivate_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('activate_deactivate_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('activate_deactivate_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $this->report_header();

        if ($this->input->post('dailydate') && $this->validate_active_inactive_daily()) {

            $today = (strip_tags($this->input->post('date', TRUE)));
            $this->session->set_userdata("inf_date1", $today);
        } else {
            $error_array = $this->form_validation->error_array();
            $this->session->set_userdata('inf_total_active_deactive_error', $error_array);
            redirect('admin/select_report/activate_deactivate_report');
        }
        $report_date = '';
        if (!empty($this->session->userdata("inf_date1"))) {
            $report_date = "$today";
            $today = $this->session->userdata("inf_date1");
            $todays_active_deactive = $this->report_model->getDailyActivateDeactivateReport($today);
            $count = count($todays_active_deactive);
            $this->set("count", $count);
            $this->set("todays_active_deactive", $todays_active_deactive);
        } else {
            $error_array = $this->form_validation->error_array();
            $this->session->set_userdata('inf_profile_report_view_error', $error_array);
            redirect('admin/select_report/activate_deactivate_report');
        }
        $help_link = "downlaod_document";
        $this->set("help_link", $help_link);
        $this->set("report_date", $report_date);
        $this->setView();
    }

    function validate_active_inactive_daily() {
        $this->form_validation->set_rules('date', lang('date'), 'trim|required|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function repurchase_report_view() {

        $title = lang('repurchase_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('repurchase_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('repurchase_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $this->report_header();

        $report_name = '';
        $this->set('report_name', "$report_name");


        $date = date("Y-m-d");
        $report_date = '';
        $this->set("date", $date);
        $date1 = date('Y-m-d:H:i:s');
        $user_id = "";
        $user_name = "";


        $user_name  = $this->input->get('user_name');
        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));
        
        $dateRangeString = "";
        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $fromDate = ($from_date)? date('d M Y', strtotime($from_date)): lang('NA');
            $toDate = ($to_date)? date('d M Y', strtotime($to_date)): lang('NA');
            $dateRangeString .= lang('daterange') . " : ($fromDate - $toDate)";
        }

        if($from_date != ''){
        $from_date = $from_date . " ". "00:00:00";
        }if($to_date != ''){ 
        $to_date = $to_date . " ". "23:59:59";
        }
        

          if (($from_date != '') && ($to_date != '')) {
            if (($this->input->get('from_date')) > ($this->input->get('to_date'))) {
                $msg = lang('To-Date should be greater than From-Date');
                $this->redirect($msg, 'select_report/repurchase_report', FALSE);
            }
        }

        $userFilter = "";
        if ($this->input->get("user_name")) {
                $user_id = $this->report_model->userNameToID($user_name);
                if (!$user_id) {
                    $msg = lang('invalid_username');
                    $this->redirect($msg, 'select_report/repurchase_report', FALSE);
                }
                $userFilter = lang('member_name') . " : " . $this->validation_model->getUserFullName($user_id) . "(" . $user_name . ")";       
        }
        $this->set('user_name', $user_name);


           if ($from_date != '' && $to_date != '') {
                $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
            } else if ($from_date != '') {
                $report_date = lang('from') . "\t" . $from_date;
            } else if ($to_date != '') {
                $report_date = lang('to') . "\t" . $to_date;
            } else {
                $report_date = '';
        }


        $purcahse_details = $this->report_model->getRepurchaseDetails($from_date, $to_date, $user_id);
            $count = count($purcahse_details);
        $this->set("dateRangeString", $dateRangeString);
        $this->set("userFilter", $userFilter);
        $this->set("count", $count);
        $this->set("purcahse_details", $purcahse_details);
        $help_link = "repurcahse_report_view";
        $this->set("help_link", $help_link);
        $this->set("report_date", $report_date);
        $this->set("daterange", $daterange);
        $this->set("from_date", ($from_date)? date('Y-m-d', strtotime($from_date)): '');
        $this->set("to_date", ($to_date)? date('Y-m-d', strtotime($to_date)): '');
        $this->setView();
    }

    function validate_repurcahse() {
        $this->lang->load("validation");
        $this->form_validation->set_rules('week_date1', lang('from_date'), 'trim|strip_tags|required',[
           "required"=>sprintf(lang('required_select'),lang('from_date')),
        ]);
        $this->form_validation->set_rules('week_date2', lang('to_date'), 'trim|strip_tags|required',[
             "required"=>sprintf(lang('required_select'),lang('to_date'))
        ]);
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function product_invoice($enc_invoice_order_id) {

        $title = lang('invoice_details');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('invoice_details');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('invoice_details');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $this->report_header();
        $report_date = date("Y-m-d");

        if (!$enc_invoice_order_id) {
            redirect('admin/select_report/repurchase_report');
        } else {
            $invoice_order_id = $this->validation_model->decrypt($enc_invoice_order_id);

            if (!$invoice_order_id) {
                $this->redirect(lang('invalid_invoice_id'), "select_report/repurchase_report", FALSE);
            }
            $invoice_details = $this->report_model->getRpurchaseInvoiceDetails($invoice_order_id);
            $this->set("count", count($invoice_details));
            $this->set("invoice_details", $invoice_details);
        }

        $help_link = "invoice_details";
        $this->set("help_link", $help_link);
        $this->set("report_date", $report_date);

        $this->setView();
    }

    function stair_step_report_view() {

        $title = lang('stair_step_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('stair_step_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('stair_step_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $this->report_header();

        //FILTER DATA

        $user_name = $this->input->get('user_name');
        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));

        //VALIDATION

        if ($this->input->get("user_name")) {
                $user_id = $this->report_model->userNameToID($user_name);
                if (!$user_id) {
                    $msg = lang('invalid_username');
                    $this->redirect($msg, 'select_report/repurchase_report', FALSE);
                }
        }

      

        //DATA

        $leader_id = $this->report_model->userNameToID($user_name);
        $report_date = '';
        $purcahse_details = $this->report_model->getStairStepDetails($from_date, $to_date, $leader_id);
        $count = count($purcahse_details);
        $this->set("count", $count);
        $this->set("purcahse_details", $purcahse_details);


        
        $help_link = "stair_step_view";
        $this->set("help_link", $help_link);
        $this->set("report_date", $report_date);
        $this->setView();
    }

    function override_report_view() {

        $title = lang('override_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('override_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('override_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $this->report_header();

        if ($this->input->post('submit') && $this->validate_repurcahse()) {

            $week_date1 = strip_tags($this->input->post('week_date1'));
            $this->session->set_userdata("inf_date1", $week_date1);

            $week_date2 = strip_tags($this->input->post('week_date2'));
            $this->session->set_userdata("inf_date2", $week_date2);

            $user_name = strip_tags($this->input->post('user_name'));
            $this->session->set_userdata("inf_user_name", $user_name);
        } else {
            $error_array = $this->form_validation->error_array();
            $this->session->set_userdata('inf_override_report_view_error', $error_array);
            redirect('admin/select_report/override_report');
        }
        $report_date = '';
        if (!empty($this->session->userdata("inf_date1")) && !empty($this->session->userdata("inf_date2"))) {
            $report_date = date("Y-m-d H:i:s");

            $user_name = $this->session->userdata("inf_user_name");
            $leader_id = $this->report_model->userNameToID($user_name);

            $week_date1 = $this->session->userdata("inf_date1");
            $week_date2 = $this->session->userdata("inf_date2");

            $purcahse_details = $this->report_model->getOverRideDetails($week_date1, $week_date2, $leader_id);
            $count = count($purcahse_details);
            $this->set("count", $count);
            $this->set("purcahse_details", $purcahse_details);
        } else {
            $error_array = $this->form_validation->error_array();
            $this->session->set_userdata('inf_override_report_view_error', $error_array);
            redirect('admin/select_report/override_report_view');
        }

        $help_link = "override_report_view";
        $this->set("help_link", $help_link);
        $this->set("report_date", $report_date);
        $this->setView();
    }

    //config change report
    function config_changes_report_view() {

        $title = lang('settings_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('settings_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('settings_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $form_submit = FALSE;

        $help_link = "report";
        $this->set("report_date", '');
        $this->set("help_link", $help_link);

        if ($this->input->post('submit_date')) {

            $date_post_array = $this->input->post(NULL, TRUE);
            $date_post_array = $this->validation_model->stripTagsPostArray($date_post_array);

            if (($date_post_array['from_date'] == '') && ( $date_post_array['to_date'] == '')) {
                $msg = lang('Please Select Atleast One Criteria.');
                $this->redirect($msg, 'select_report/config_changes_report', FALSE);
            }
            if ($date_post_array['from_date'] != '' && $date_post_array['to_date'] != '') {
                if (strtotime($date_post_array['from_date']) > strtotime($date_post_array['to_date'])) {
                    $msg = lang('To-Date should be greater than From-Date');
                    $this->redirect($msg, 'select_report/config_changes_report', FALSE);
                }
            }
            $form_submit = TRUE;
            if ($date_post_array['from_date'] != '') {
                $from_date = $date_post_array['from_date'] . " 00:00:00";
            } else {
                $from_date = '';
            }
            if ($date_post_array['to_date'] != '') {
                $to_date = $date_post_array['to_date'] . " 23:59:59";
            } else {
                $to_date = '';
            }
            $ip_address = $date_post_array['ip_address'];


            $this->session->set_userdata('from_date', $from_date);
            $this->session->set_userdata('to_date', $to_date);
            $this->session->set_userdata('ip_address', $ip_address);
            $config_details = $this->report_model->getConfigChanges($from_date, $to_date, $ip_address);
            $count = count($config_details);
            $this->set("count", $count);
            $this->set("from_date", $from_date);
            $this->set("to_date", $to_date);
            $this->set("config_details", $this->security->xss_clean($config_details));
        } else {
            redirect('admin/select_report/config_changes_report');
        }

        $this->setview();
    }

    //
    //E-pin Transfer History Report
    function epin_transfer_report_view() {

        $title = lang('epin_transfer_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('epin_transfer_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('epin_transfer_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $user_id = null;

        //FILTER DATA

        $user_name    = $this->input->get('user_name');
        $to_user_name = $this->input->get('to_user_name');

        $daterange = $this->input->get('daterange') ?: 'all';
        $dateRangeString = "";
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));
        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $fromDate = ($from_date)? date('d M Y', strtotime($from_date)): lang('NA');
            $toDate = ($to_date)? date('d M Y', strtotime($to_date)): lang('NA');
            $dateRangeString .= lang('daterange') . " : ($fromDate - $toDate)";
        }
        $this->set('dateRangeString', $dateRangeString);

  
        $date = date("Y-m-d");
        $this->set("date", $date);


        if($from_date != ''){
        $from_date = $from_date . " ". "00:00:00";
        }if($to_date != ''){ 
        $to_date = $to_date . " ". "23:59:59";
        }

        //VALIDATION
        
        $fromUserString = "";
        $toUserString = "";

        if (!empty($user_name)) {
            $fromUserId = $this->report_model->userNameToID($user_name);
            if (!($fromUserId)) {
                $msg = lang('invalid_user_name');
                $this->redirect($msg, "select_report/epin_report", FALSE);
            }
            $fromUserString = lang('from') . " : " . $this->validation_model->getUserFullName($fromUserId) . "($user_name)";
        }

        if (!empty($to_user_name)) {
            $toUserId = $this->report_model->userNameToID($to_user_name);
            if (!($toUserId)) {
                $msg = lang('invalid_user_name');
                $this->redirect($msg, "select_report/epin_report", FALSE);
            }
            $toUserString = lang('to') . " : " . $this->validation_model->getUserFullName($toUserId) . "($user_name)";
        }


        $report_date = date("Y-m-d H:i:s");
        if ($from_date) {
            if ($to_date) {
                $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
            } else {
                $report_date = $to_date;
            }
        } elseif ($to_date) {
            $report_date = $to_date;
        }
        
        if (($from_date != '') && ($to_date != '')) {
                if ($from_date > $to_date) {
                    $msg = lang('To-Date should be greater than From-Date');
                    $this->redirect($msg, 'select_report/epin_report', FALSE);
                }
        }

        //DATA

        $user_id = $this->report_model->userNameToID($user_name);
        $to_user_id = $this->report_model->userNameToID($to_user_name);
        $transfer_details = $this->report_model->getEpinTransferDetails($from_date, $to_date, $user_id, $to_user_id,$this->input->get('offset'), 
            $this->PAGINATION_PER_PAGE);
        $count1 = $this->report_model->getCountEpinTransferDetails($from_date, $to_date, $user_id, $to_user_id); 
        $this->pagination->set_all('admin/epin_transfer_report_view',$count1);   
        $count = count($transfer_details);
        $this->set("count", $count);
        $this->set("transfer_details", $transfer_details);

        $this->set("fromUserString", $fromUserString);
        $this->set("toUserString", $toUserString);
        $date = date("Y-m-d");
        $this->set("date", $date);
        $this->set("user_name", $user_name);
        $this->set("to_user_name",$to_user_name);
        $this->set("daterange", $daterange);
        $this->set("from_date", ($from_date)? date('Y-m-d', strtotime($from_date)): '');
        $this->set("to_date", ($to_date)? date('Y-m-d', strtotime($to_date)): '');
        $this->set("report_date", $report_date);
        $help_link = "report";
        $this->set("help_link", $help_link);
        $this->setview();
    }

    function rank_performance_report() {

        $title = lang('rank_performance_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = "Rank Performance Report";
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = "Rank Performance Report";
        $this->HEADER_LANG['page_small_header'] = '';


        $this->load_langauge_scripts();

        $this->report_header();

        $date = date("Y-m-d");
        $this->set("date", $date);
        $is_valid_username = false;
        $user_mob_name="";
         $full_name="";
         $rank_achievement=array();

            $user_mob_name = ($this->input->get('user_name', TRUE)) ?: $this->LOG_USER_NAME;

            //$this->session->set_userdata("is_valid_user_name", $user_mob_name);
            $user_id = $this->validation_model->userNameToID($user_mob_name);
            $is_valid_username = $this->validation_model->isUserAvailable($user_id);

            if (!$is_valid_username) {
                $msg = lang('invalid_user_name');
                $this->redirect($msg, 'select_report/rank_performance_report', FALSE);
            }

            //$this->session->set_userdata("inf_user_name_payout", $user_mob_name);
            $user_details = $this->validation_model->getAllUserDetails($user_id);

            $full_name = $user_details['user_detail_name']. ' ' . $user_details['user_detail_second_name'];

            $this->load->model('rank_model');
            $rank_achievement = $this->rank_model->getRankCriteria($user_id);

        $report_name = lang('rank_details');

        $this->set('report_name', $report_name);
        $help_link = "Top-earners";
        $this->set("report_date", '');
        $help_link = "report";
        $this->set("rank_achievement", $rank_achievement);
        $this->set("user_name", $user_mob_name);
        $this->set("full_name", $full_name);
        $this->set("help_link", $help_link);
        $this->setView();
    }

    function validate_performance_report() {
        $this->form_validation->set_rules('user_name', lang('user_name'), 'trim|required|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function get_rank_performance_detail($user_id) {

        $details = array();
        $rank_details = array();
        $referal_count = 0;
        $current_rank = 0;
        $next_ran = 0;
        $next_rank = '';
        $referal_count = $this->validation_model->getReferalCount($user_id);

        $current_rank = $this->validation_model->getUserRank($user_id);
        $personal_pv = $this->validation_model->getPersnlPv($user_id);

        if ($current_rank != '') {
            $rank_details = $this->select_report_model->selectRankDetails($current_rank);
            $ref_count = $rank_details['referal_count'];
        } else {
            $ref_count = 0;
        }

        if (!$personal_pv) {
            $personal_pv = 0;
        }

        $group_pv = $this->validation_model->getGrpPv($user_id);
        if (!$group_pv) {
            $group_pv = 0;
        }

        $next_rank = $this->select_report_model->getNextRank($ref_count);

        if ($current_rank != 0) {
            $rank_details = $this->select_report_model->selectRankDetails($current_rank);
            $current_rank = $rank_details['rank_name'];
        } else {
            $current_rank = 'NA';
        }

        if ($next_rank) {
            if ($next_rank[0]->referal_count > $referal_count) {
                $balance_referal_count = $next_rank[0]->referal_count - $referal_count;
            } else {
                $balance_referal_count = 0;
            }
            if ($next_rank[0]->personal_pv > $personal_pv) {
                $balance_personal_pv = $next_rank[0]->personal_pv - $personal_pv;
            } else {
                $balance_personal_pv = 0;
            }
            if ($next_rank[0]->gpv > $group_pv) {
                $balance_gpv = $next_rank[0]->gpv - $group_pv;
            } else {
                $balance_gpv = 0;
            }

            $next_ran = $next_rank[0]->rank_name;
            $next_referal_count = $next_rank[0]->referal_count;
            $next_pers_pv = $next_rank[0]->personal_pv;
            $next_grp_pv = $next_rank[0]->gpv;
        } else {
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

    function roi_report_view() {

        $title = lang('roi_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('roi_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('roi_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $date = date("Y-m-d");
        $report_date = '';
        $this->set("date", $date);
        $date1 = date('Y-m-d:H:i:s');
        $user_id = "";
        $user_name = "";

        //FILTER DATA
        $user_name  = $this->input->get('user_name');
        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));

        $date = date("Y-m-d");
        $this->set("date", $date);


        if($from_date != ''){
        $from_date = $from_date . " ". "00:00:00";
        }if($to_date != ''){ 
        $to_date = $to_date . " ". "23:59:59";
        }

        
        //VALIDATION
        if (($from_date != '') && ($to_date != '')) {
                if ($from_date > $to_date) {
                    $msg = lang('to_date_should_be_greater_than_or_equal_to_from_date');
                    $this->redirect($msg, 'select_report/roi_report', FALSE);
                }
        } 
        if ($this->input->get("user_name")) {
                $user_id = $this->report_model->userNameToID($user_name);
                if (!$user_id) {
                    $msg = lang('invalid_username');
                    $this->redirect($msg, 'select_report/roi_report', FALSE);
                }
        }
        $this->set('user_name', $user_name);
        
        if ($from_date != '' && $to_date != '') {
                $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
            } else if ($from_date != '') {
                $report_date = lang('from') . "\t" . $from_date;
            } else if ($to_date != '') {
                $report_date = lang('to') . "\t" . $to_date;
            } else {
                $report_date = '';
        }


        //DATA
        $roi_details = $this->report_model->getroiDetails($from_date, $to_date, $user_id);
        
        $count = count($roi_details);

        $this->report_header();

        $this->set('roi_details', $this->security->xss_clean($roi_details));
        $this->set('count', $count);
        $this->set('date1', $date1);
        $this->set('from_date', ($from_date)? date('Y-m-d', strtotime($from_date)): '');
        $this->set('to_date', ($to_date)? date('Y-m-d', strtotime($to_date)): '');
        $help_link = "report";
        $this->set("help_link", $help_link);
        $this->set("report_date", $report_date);
        $this->setView();
    }

    function validate_roi() {
        $this->form_validation->set_rules('week_date1', lang('from_date'), 'trim|strip_tags');
        $this->form_validation->set_rules('week_date2', lang('to_date'), 'trim|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function validate_expense() {
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required|xss_clean|numeric|strip_tags');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required|xss_clean|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function validate_date() {
        $this->form_validation->set_rules('weekdate', lang('month'), 'trim|required|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function validate_transaction_errors() {
        $this->form_validation->set_rules('week_date1', lang('from_date'), 'trim|required|strip_tags');
        $this->form_validation->set_rules('week_date2', lang('to_date'), 'trim|required|strip_tags');

        $validate_form = $this->form_validation->run();

        return $validate_form;
    }
    function getAllProducts(){
        $this->lang->load('select_report_lang');
        $package_type = $this->input->post('package_type');
        $this->load->model('select_report_model');
        if($package_type == 'repurchase'){
         
         $products = $this->select_report_model->getAllProducts('repurchase');
        
        }else{
         $products = $this->select_report_model->getAllProducts();

        }
        echo json_encode($products);exit(); 

    }
    
    public function package_upgrade_report_view()
    {
        $title = lang('package_upgrade_report');
        $this->set("title", $this->COMPANY_NAME . " | $title ");

        $this->HEADER_LANG['page_top_header'] = lang('package_upgrade_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('package_upgrade_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $report_date = date("Y-m-d");
        //$report_date = '';
        //$this->set("date", $date);

        $user_name=$this->input->get('user_name');
        $user_id=$this->validation_model->userNameToID($user_name);
       
        $userFilter = "";
        $packageString = "";
        if((!$user_id)&& $user_name!="") {
            $msg=lang("invalid_username");
            $this->redirect($msg,'select_report/package_upgrade_report',false);
        } 
        if($user_id) {
           $userFilter = lang('member_name') . " : " . $this->validation_model->getUserFullName($user_id) . "(" . $user_name . ")";
        }
        
        $product_id1=$this->input->get('package_name');
        $product_id=$this->select_report_model->getProdIDFromProductName($product_id1);
        if($product_id) {
            $packageString = lang("package") . " : " . $this->report_model->getPackageName($product_id1);
        }

        $package_details=$this->select_report_model->getPackageUpgradeDetails($user_id,$product_id,$this->input->get('offset'),$this->PAGINATION_PER_PAGE);
        $count1=$this->select_report_model->getCountPackageUpgradeDetails($user_id,$product_id);
        $this->pagination->set_all('admin/package_upgrade_report_view',$count1);
        $total_amount = 0;
        for($i = 0;$i < count($package_details);$i++) {
           $package_details[$i]["full_name"] = $this->validation_model->getUserFullName($package_details[$i]["user_id"]);
           $total_amount += $package_details[$i]["payment_amount"];
        }

        $package_names=$this->select_report_model->getPackageName();

        $this->set('package_names',$package_names);

        $this->set('package_details',$package_details);
        $this->set('total_amount',$total_amount);
        $this->set('userFilter',$userFilter);
        $this->set('packageString',$packageString);
        $this->set('user_name',$user_name);
        $this->set('product_id',$product_id);
        $this->set('product_id1',$product_id1);
        $this->set("report_date", $report_date);

        $this->setView();

    }
    
    public function subscription_report_view()
    {
        $title = lang('subscription_report');
        $this->set("title", $this->COMPANY_NAME . " | $title ");

        $this->HEADER_LANG['page_top_header'] = $title;
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = $title;
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        
        $filterUserString = "";
        $userName=$this->input->get('user_name');
        $userId = "";
        if($userName) {
            $userId=$this->validation_model->userNameToID($userName);
            if(!$userId) {
                $msg=lang("invalid_username");
                $this->redirect($msg,'select_report/subscription_report',false);
            }
            $userFullName = $this->validation_model->getUserFullName($userId);
            $filterUserString = "$userFullName($userName)";
        }
        
        $details = $this->report_model->getSubscriptionReport($userId,'','',$this->input->get('offset'),$this->PAGINATION_PER_PAGE);
        $count1 = $this->report_model->getCountSubscriptionReport($userId,'','');

        $user_name = $this->input->get('user_name') ?: $this->validation_model->getAdminUsername();
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
        $this->pagination->set_all('admin/report/subscription_report_view',$count1);
        $this->set("showPackage", $this->MODULE_STATUS["product_status"]);
        $this->set("details", $details);
        $this->set("count", count($details));
        $this->set("filterUserString", $filterUserString);
        $this->set("userName", $userName);
        $this->set("report_date", '');
        $this->setView();
    }

    public function payout_report() {
        $title = lang('payout_release_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('payout_release_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('payout_release_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        // dd($this->input->get('status') == "pending");
        if($this->input->get('status') == "pending") {
            return $this->payout_pending_report_weekly();
        }


        $date = date("Y-m-d");
        $report_date = '';
        $this->set("date", $date);

        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));

        $dateRangeString = "";
        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $fromDate = ($from_date)? date('d M Y', strtotime($from_date)): lang('NA');
            $toDate = ($to_date)? date('d M Y', strtotime($to_date)): lang('NA');
            $dateRangeString .= lang('daterange') . " : ($fromDate - $toDate)";
        }

        if($from_date != ''){
        $from_date = $from_date . " ". "00:00:00";
        }if($to_date != ''){ 
        $to_date = $to_date . " ". "23:59:59";
        }
        

          if (($from_date != '') && ($to_date != '')) {
            if (($this->input->get('from_date')) > ($this->input->get('to_date'))) {
                $msg = lang('To-Date should be greater than From-Date');
                $this->redirect($msg, 'select_report/payout_release_report', FALSE);
            }
        }

        if ($from_date != '' && $to_date != '') {
                $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
            } else if ($from_date != '') {
                $report_date = lang('from') . "\t" . $from_date;
            } else if ($to_date != '') {
                $report_date = lang('to') . "\t" . $to_date;
            } else {
                $report_date = '';
        }




        $ewallt_req_details = $this->report_model->getReleasedPayoutDetails($from_date, $to_date,$this->input->get('offset'), 
            $this->PAGINATION_PER_PAGE);
         $count1 = $this->report_model->getCountReleasedPayoutDetails($from_date, $to_date); 
         $this->pagination->set_all('admin/payout_released_report_weekly',$count1);   
        $totalPayout = 0;
        $totalPayoutFee = 0;
        foreach($ewallt_req_details as $row) {
            $totalPayout += $row['paid_amount'];
            $totalPayoutFee += $row['payout_fee'];    
        }
        $count = count($ewallt_req_details);
        $this->set("payoutFeeDisplay", $this->report_model->checkPayoutFeeDisplay());
        $this->set("dateRangeString", $dateRangeString);
        $this->set("binary_details", $ewallt_req_details);
        $this->set("totalPayout", $totalPayout);
        $this->set("totalPayoutFee", $totalPayoutFee);
        $this->set("count", $count);
        $this->set("report_date", $report_date);
        $this->set("daterange", $daterange);
        $this->set("from_date", ($from_date)? date('Y-m-d', strtotime($from_date)): '');
        $this->set("to_date", ($to_date)? date('Y-m-d', strtotime($to_date)): '');
        $this->setView('admin/report/payout_released_report_weekly');
    }

    public function agent_credit_report_view($value='')
    {
        // dd($value);
        $title = lang('agent_credit_report_view');
        $this->set("title", $this->COMPANY_NAME . " | $title ");

        $this->HEADER_LANG['page_top_header'] = $title;
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = $title;
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        
        $filterUserString = "";
        $userName=$this->input->get('agent_user_name');
        $arr = explode("(", $userName, 2);
        $agent_username = $arr[0];
        // dd($agent_username);
       $agent_id=$this->validation_model->AgentUserNameToID($agent_username);
        if($userName) {
            $agent_id=$this->validation_model->AgentUserNameToID($agent_username);
            if(!$agent_id) {
                $msg=lang("invalid_agent");
                $this->redirect($msg,'select_report/agent_credit_report_view',false);
            }
            // $userFullName = $this->validation_model->getUserFullName($userId);
            // $filterUserString = "$userFullName($userName)";
        }
        $details = $this->report_model->getWalletReport('agent',$agent_id,$this->input->get('offset'),$this->PAGINATION_PER_PAGE);
        // dd($details);
        $count1 = $this->report_model->getCountWalletReport('agent',$agent_id);
        $user_name = $this->input->get('user_name') ?: $this->validation_model->getAdminUsername();
        $error_array = array();
        

        $this->set('error_array', $error_array);
        $this->set('error_count', count($error_array));

        // $this->set('error_array_count', $error_array_count);
        // $this->set('error_single_count', count($error_array_count));

        $this->pagination->set_all('admin/report/agent_credit_report_view',$count1);
        $this->set("showPackage", $this->MODULE_STATUS["product_status"]);
        $this->set("details", $details);
        $this->set("count", count($details));
        $this->set("filterUserString", $filterUserString);
        $this->set("user_name", $userName);
        $this->set("report_date", '');
        $this->setView();
    }
    
    
    function profit_distribution_report() {
        $dateRangeString = "";

        $title = lang('profit_distribution_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('profit_distribution_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('profit_distribution_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $this->report_header();

        $date = date("Y-m-d");
        $report_date = '';
        $this->set("date", $date);
        $user_id = '';
        
        //FILTER DATA
        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));
      
        if ($from_date != '' && $to_date != '') {
                $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
        } else if ($from_date != '') {
                $report_date = lang('from') . "\t" . $from_date;
        } else if ($to_date != '') {
                $report_date = lang('to') . "\t" . $to_date;
        } else {
                $report_date = '';
        }

        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $fromDate = ($from_date)? date('d M Y', strtotime($from_date)): lang('NA');
            $toDate = ($to_date)? date('d M Y', strtotime($to_date)): lang('NA');
            $dateRangeString .= lang('daterange') . " : ($fromDate - $toDate)";
        }

        if ($from_date)
                $from_date = $from_date . " 00:00:00";
        if ($to_date)
                $to_date = $to_date . " 23:59:59";


         if (($from_date != '') && ($to_date != '')) {
                if ($from_date > $to_date) {
                    $msg = lang('To-Date should be greater than From-Date');
                    $this->redirect($msg, 'admin/profit_distribution_report', FALSE);
                }
        }   
        

        $weekly_payout = $this->report_model->getProfitDistributionReport($from_date, $to_date,$this->input->get('offset'),$this->PAGINATION_PER_PAGE);
        // dump('here');
        $count1 = $this->report_model->getCountProfitDistributionReport($from_date, $to_date, $user_id);
        // dd($count1);
        $this->pagination->set_all('admin/profit_distribution_report',$count1);
        $count = count($weekly_payout);
        $this->set("count", $count);
        $this->set("weekly_payout", $this->security->xss_clean($weekly_payout)); 
    
        $this->set('dateRangeString', $dateRangeString);

        $help_link = "report";
        $this->set("from_date", ($from_date)? date('Y-m-d', strtotime($from_date)): '');
        $this->set("to_date",($to_date)? date('Y-m-d', strtotime($to_date)): '');
        $this->set("daterange",$daterange);
        $this->set("report_date", $report_date);
        $this->set("help_link", $help_link);
        $this->setView(); 
    }
    
    
    function pool_distribution_report() {
        $dateRangeString = "";

        $title = lang('pool_distribution_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('pool_distribution_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('pool_distribution_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $this->report_header();

        $date = date("Y-m-d");
        $report_date = '';
        $this->set("date", $date);
        $user_id = '';
        
        //FILTER DATA
        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));
      
        if ($from_date != '' && $to_date != '') {
                $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
        } else if ($from_date != '') {
                $report_date = lang('from') . "\t" . $from_date;
        } else if ($to_date != '') {
                $report_date = lang('to') . "\t" . $to_date;
        } else {
                $report_date = '';
        }

        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $fromDate = ($from_date)? date('d M Y', strtotime($from_date)): lang('NA');
            $toDate = ($to_date)? date('d M Y', strtotime($to_date)): lang('NA');
            $dateRangeString .= lang('daterange') . " : ($fromDate - $toDate)";
        }

        if ($from_date)
                $from_date = $from_date . " 00:00:00";
        if ($to_date)
                $to_date = $to_date . " 23:59:59";


         if (($from_date != '') && ($to_date != '')) {
                if ($from_date > $to_date) {
                    $msg = lang('To-Date should be greater than From-Date');
                    $this->redirect($msg, 'admin/pool_distribution_report', FALSE);
                }
        }   
        

        $weekly_payout = $this->report_model->getPoolDistributionReport($from_date, $to_date,$this->input->get('offset'),$this->PAGINATION_PER_PAGE);
        // dump('here');
        $count1 = $this->report_model->getCountPoolDistributionReport($from_date, $to_date, $user_id);
        // dd($count1);
        $this->pagination->set_all('admin/pool_distribution_report',$count1);
        $count = count($weekly_payout);
        $this->set("count", $count);
        $this->set("weekly_payout", $this->security->xss_clean($weekly_payout)); 
    
        $this->set('dateRangeString', $dateRangeString);

        $help_link = "report";
        $this->set("from_date", ($from_date)? date('Y-m-d', strtotime($from_date)): '');
        $this->set("to_date",($to_date)? date('Y-m-d', strtotime($to_date)): '');
        $this->set("daterange",$daterange);
        $this->set("report_date", $report_date);
        $this->set("help_link", $help_link);
        $this->setView(); 
    }
    function voucher_report(){
        $title = lang('voucher_report');
        $this->set("title", $this->COMPANY_NAME . " | $title ");

        $this->HEADER_LANG['page_top_header'] = $title;
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = $title;
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        
        $filterUserString = "";
        $userName=$this->input->get('user_name');
        $userId = "";
        if($userName) {
            $userId=$this->validation_model->userNameToID($userName);
            if(!$userId) {
                $msg=lang("invalid_username");
                $this->redirect($msg,'report/voucher_report',false);
            }
            $userFullName = $this->validation_model->getUserFullName($userId);
            $filterUserString = "$userFullName($userName)";
        }
        
        $details = $this->report_model->getVoucherReport($userId,$this->input->get('offset'),$this->PAGINATION_PER_PAGE);
        $count1 = $this->report_model->getCountVoucherReport($userId);
        $user_name = $this->input->get('user_name') ?: $this->validation_model->getAdminUsername();
        $error_array = array();
        if ($this->session->userdata('inf_voucher_report_view_error')) {
            $error_array = $this->session->userdata('inf_voucher_report_view_error');
            $this->session->unset_userdata('inf_voucher_report_view_error');
        }

        $error_array_count = array();
        if ($this->session->userdata('inf_voucher_report_view_count_error')) {
            $error_array_count = $this->session->userdata('inf_voucher_report_view_count_error');
            $this->session->unset_userdata('inf_voucher_report_view_count_error');
        }

        $error_array_profile_count = array();
        if ($this->session->userdata('inf_voucher_report_count_error')) {
            $error_array_profile_count = $this->session->userdata('inf_voucher_report_count_error');

            $this->session->unset_userdata('inf_voucher_report_count_error');
        }

        $this->set('error_array', $error_array);
        $this->set('error_count', count($error_array));

        $this->set('error_array_count', $error_array_count);
        $this->set('error_single_count', count($error_array_count));

        $this->set('error_array_profile_count', $error_array_profile_count);
        $this->set('error_profile_count', count($error_array_profile_count));
        $this->pagination->set_all('admin/report/voucher_report',$count1);
        $this->set("showPackage", $this->MODULE_STATUS["product_status"]);
        $this->set("details", $details);
        $this->set("count", count($details));
        $this->set("filterUserString", $filterUserString);
        $this->set("userName", $userName);
        $this->set("report_date", '');
        $this->setView();
    }
    function group_pv_report() {

        $filterUserString = "";
        $dateRangeString = "";

        $title = lang('group_pv_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('group_pv_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('group_pv_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        
        $date = date("Y-m-d");
        $report_date = '';
        $this->set("date", $date);
        $date1 = date('Y-m-d:H:i:s');
        $user_id = "";
        $user_name = "";

        
        //FILTER DATA
        $user_name  = $this->input->get('user_name');
        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));

        $dateRangeString = "";
        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $fromDate = ($from_date)? date('d M Y', strtotime($from_date)): lang('NA');
            $toDate = ($to_date)? date('d M Y', strtotime($to_date)): lang('NA');
            $dateRangeString .= lang('daterange') . " : ($fromDate - $toDate)";
        }


        if($from_date != ''){
        $from_date = $from_date . " ". "00:00:00";
        }if($to_date != ''){ 
        $to_date = $to_date . " ". "23:59:59";
        }
        

        //validation
        if (($from_date != '') && ($to_date != '')) {
            if (($this->input->get('from_date')) > ($this->input->get('to_date'))) {
                $msg = lang('To-Date should be greater than From-Date');
                $this->redirect($msg, 'admin/report/group_pv_report', FALSE);
            }
        }



        if ($this->input->get("user_name")) {
                $user_id = $this->report_model->userNameToID($user_name);
                if (!$user_id) {
                    $msg = lang('invalid_username');
                    $this->redirect($msg, 'admin/report/group_pv_report', FALSE);
                }
                $filterUserString = lang('member_name') . ": {$this->validation_model->getUserFullName($user_id)} ($user_name)";
        }
        $this->set('user_name', $user_name);

        if ($from_date != '' && $to_date != '') {
                $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
            } else if ($from_date != '') {
                $report_date = lang('from') . "\t" . $from_date;
            } else if ($to_date != '') {
                $report_date = lang('to') . "\t" . $to_date;
            } else {
                $report_date = '';
        }


        //DATA

        $details = $this->report_model->getGroup_pv_details($daterange, $user_id,$from_date, $to_date, $this->PAGINATION_PER_PAGE,$this->input->get('offset'));
        $count1=$this->report_model->getCountGroup_pv_details($daterange, $user_id,$from_date, $to_date);
        $this->pagination->set_all('admin/report/group_pv_report',$count1);
        
            
        $count = count($details);
        $this->set("filterUserString", $filterUserString);
        $this->set("dateRangeString", $dateRangeString);

        $this->set('details', $details);
        $this->set('count', $count);
        $this->set('date1', $date1);
        $help_link = "report";
        $this->set("help_link", $help_link);
        $this->set("report_date", $report_date);
        $this->set("from_date", ($from_date)? date('Y-m-d', strtotime($from_date)): '');
        $this->set("to_date",($to_date)? date('Y-m-d', strtotime($to_date)): '');
        $this->set("daterange",$daterange);
        $this->setView();

    }

}
