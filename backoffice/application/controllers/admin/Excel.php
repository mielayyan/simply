<?php

require_once 'Inf_Controller.php';

class Excel extends Inf_Controller {

    function __construct() {
        parent::__construct();
    }

    function user_profiles_excel() {
        $this->session->userdata('inf_profile_type');
        if ($this->session->userdata('inf_profile_type') == "one_count") {
            $cnt = $this->session->userdata('inf_profile_count');
            $arr = $this->excel_model->getProfiles($cnt);
            $date = date("Y-m-d H:i:s");
            $this->excel_model->writeToExcel($arr, lang('profile_report') . " ($date)");
        } else if ($this->session->userdata('inf_profile_type') == "two_count") {
            $count_from = $this->session->userdata('inf_count_from');
            $count_to = $this->session->userdata('inf_count_to');
            $date = date("Y-m-d H:i:s");
            $arr = $this->excel_model->getProfilesFrom($count_from, $count_to);
            $this->excel_model->writeToExcel($arr, lang('profile_report') . " ($date)");
        }
    }

    function create_excel_joining_report_daily() {
        $date = date("Y-m-d H:i:s");
        if (!empty($this->session->userdata('inf_total_joining_daily'))) {
            $report_date = $this->session->userdata('inf_total_joining_daily');
            $excel_array = $this->excel_model->getJoiningReportDaily($report_date);
            $this->excel_model->writeToExcel($excel_array, lang('user_joining_report') . " ($date)");
        }
    }

    function create_excel_joining_report_weekly() {
        $date = date("Y-m-d H:i:s");
        $from_date = $this->input->get('from_date');
        $to_date = $this->input->get('to_date');
        if( $from_date!='')
        {
            $from_date = $from_date . " 00:00:00";
        }
        if($to_date!=''){ 
           $to_date = $to_date . " 23:59:59";
        }

        $excel_array = $this->excel_model->getJoiningReportWeekly($from_date, $to_date);
        $this->excel_model->writeToExcel($excel_array, lang('user_joining_report') . " ($date)");
    }
    //ewallet excel

    function create_excel_total_ewallet_transaction_report() {
        $date = date("Y-m-d H:i:s");
        $excel_array = $this->excel_model->getEwalletTransactionsReport();
        $this->excel_model->writeToExcel($excel_array, lang('ewallet_transaction_report') . " ($date)");
    }
    //balace
    function create_excel_total_ewallet_balance_report() {
        $date = date("Y-m-d H:i:s");
        $excel_array = $this->excel_model->EwalletBalanceReport();
        $this->excel_model->writeToExcel($excel_array, lang('ewallet_balace_report') . " ($date)");
    }
    //statement
    function create_excel_total_ewallet_statement_report() {
        $date = date("Y-m-d H:i:s");
        $excel_array = $this->excel_model->getEwalletStatementReport();
        $this->excel_model->writeToExcel($excel_array, lang('ewallet_statement_report') . " ($date)");
    }
//user earnings
     function create_excel_total_user_earnings_report() {
        $date = date("Y-m-d H:i:s");
        
        $excel_array = $this->excel_model->getEwalletEarningsReport();
        $this->excel_model->writeToExcel($excel_array, lang('user_earnings_report') . " ($date)");
    }




    function create_excel_total_payout_report() {
        $date = date("Y-m-d H:i:s");
        $excel_array = $this->excel_model->getTotalPayoutReport();
        $this->excel_model->writeToExcel($excel_array, lang('total_bonus_report') . " ($date)");
    }

    function create_excel_weekly_payout_report() {
        $date = date("Y-m-d H:i:s");
        $from_date = $this->input->get('from_date');
        $to_date   = $this->input->get('to_date');
        $user_name = $this->input->get('user_name');
        $user_id = $this->report_model->userNameToID($user_name);

        $excel_array = $this->excel_model->getTotalPayoutReport($from_date, $to_date,$user_id);
        $this->excel_model->writeToExcel($excel_array, lang('week_wise_bonus_report') . " ($date)");
    }

    function create_excel_rank_achievers_report() {
        $date = date("Y-m-d H:i:s");
        
        $from_date = $this->input->get('from_date');
        $to_date   = $this->input->get('to_date');
        $rank_data     = $this->input->get("ranks");
        
        // parse_str($rank_data, $ranks);
        $ranks = ($rank_data)?explode(",", $rank_data):"";
        if($ranks && !count($ranks))
            $ranks = "";

        $excel_array = $this->excel_model->getRankAchieversReport($from_date, $to_date, $ranks);
            $this->excel_model->writeToExcel($excel_array, lang('rank_achievers_report') . " ($date)");
    }

    function create_excel_commission_report($user_name = '') {
        $user_id = $this->validation_model->userNameToID($user_name);
        if ($user_name && !$user_id) {
            $msg = lang('invalid_username');
            $this->redirect($msg, 'select_report/commission_report', FALSE);
        }
        $date = date("Y-m-d H:i:s");
        $from_date  = $this->input->get('from_date');
        $to_date = $this->input->get('to_date');
        if( $from_date!='')
        {
            $from_date = $from_date . " 00:00:00";
        }
        if($to_date!=''){ 
           $to_date = $to_date . " 23:59:59";
        }
        $type = $this->session->userdata("inf_commision_type");

        $excel_array = $this->excel_model->getCommissionReport($from_date, $to_date, $type, $user_id);
        $this->excel_model->writeToExcel($excel_array, lang('commission_report') . " ($date)");
        
    }


    




    function create_excel_epin_report() {
        $date = date("Y-m-d H:i:s");
        $excel_array = $this->excel_model->getEpinReport();
        $this->excel_model->writeToExcel($excel_array, lang('epin_report') . " ($date)");
    }

    function create_excel_top_earners_report() {
        $date = date("Y-m-d H:i:s");
        $excel_array = $this->excel_model->getTopEarnersReport();
        $this->excel_model->writeToExcel($excel_array, lang('top_earners_report') . " ($date)");
    }

    function create_excel_profile_view_report() {
        $date = date("Y-m-d H:i:s");
        if (!empty($this->session->userdata("inf_profile_report_view_user_name"))) {
            $user_name = $this->session->userdata("inf_profile_report_view_user_name");
            $excel_array = $this->excel_model->getProfileViewReport($user_name);
            $this->excel_model->writeToExcel($excel_array, lang('profile_report') . " ($date)");
    }
    }

    function create_excel_sales_report($product_type="") {
        $date = date("Y-m-d H:i:s");
        $from_date = $this->input->get('from_date');
        $to_date   = $this->input->get('to_date');
        $package   = $this->input->get('package');

        $excel_array = $this->excel_model->getSalesReport($from_date, $to_date,$product_type, $package);
            $this->excel_model->writeToExcel($excel_array, lang('sales_report') . " ($date)");
    }

    function create_excel_product_sales_report($product_type="") {
        $date = date("Y-m-d H:i:s");
        if (!empty($this->session->userdata("inf_product_sales_id"))) {
            $prod_id = $this->session->userdata("inf_product_sales_id");
            $excel_array = $this->excel_model->productSalesReport($prod_id,$product_type);
            $this->excel_model->writeToExcel($excel_array, lang('tran_product_wise_sales_report') . " ($date)");
        }
    }

    function create_excel_member_wise_payout_report() {
        $date = date("Y-m-d H:i:s");
        if (!empty($this->session->userdata("inf_user_name_payout"))) {
            $user_name = $this->session->userdata("inf_user_name_payout");
            $excel_array = $this->excel_model->getMemberPayoutReport($user_name);
            $this->excel_model->writeToExcel($excel_array, lang('member_wise_bonus_report') . " ($date)");
        }
    }

    function create_excel_activate_deactivate_report_view() {
        $date = date("Y-m-d H:i:s");
        $from_date = $this->input->get('from_date');
        $to_date = $this->input->get('to_date');
        if( $from_date!='')
        {
            $from_date = $from_date . " 00:00:00";
        }
        if($to_date!=''){ 
           $to_date = $to_date . " 23:59:59";
        }
        $excel_array = $this->excel_model->getActiveInactiveReport($from_date, $to_date);
        $this->excel_model->writeToExcel($excel_array, lang('activate_deactivate_report') . " ($date)");
    }

    function create_excel_activate_deactivate_report_view_daily() {
        $date = date("Y-m-d H:i:s");
        if (!empty($this->session->userdata('inf_date1'))) {
            $report_date = $this->session->userdata('inf_date1');
            $from_date = $report_date . " 00:00:00";
            $to_date = $report_date . " 23:59:59";
            $excel_array = $this->excel_model->getActiveInactiveReport($from_date, $to_date);
            $this->excel_model->writeToExcel($excel_array, lang('user_joining_report') . " ($date)");
        }
    }

    function create_excel_repurchase_report($user_name="") {
        //$date = date("Y-m-d H:i:s");
        $user_id = $this->validation_model->userNameToID($user_name);
        if ($user_name && !$user_id) {
            $msg = lang('invalid_username');
            $this->redirect($msg, 'select_report/repurchase_report', FALSE);
        }
        $date = date("Y-m-d H:i:s");
        $from_date  = $this->input->get('from_date');
        $to_date = $this->input->get('to_date');
            $excel_array = $this->excel_model->getRepurchaseReport($from_date, $to_date, $user_id);
            $this->excel_model->writeToExcel($excel_array, lang('repurchase_report') . " ($date)");
        //}
    }

    function create_excel_stairstep_report() {
        $date = date("Y-m-d H:i:s");
        if (!empty($this->session->userdata("inf_date1")) &&!empty($this->session->userdata("inf_date2"))) {

            $user_name = $this->session->userdata("inf_user_name");
            $leader_id = $this->report_model->userNameToID($user_name);

            $week_date1 = $this->session->userdata("inf_date1");
            $week_date2 = $this->session->userdata("inf_date2");

            $purcahse_details = $this->excel_model->getStairStepDetails($week_date1, $week_date2, $leader_id);
            $this->excel_model->writeToExcel($purcahse_details, lang('stairstep_report') . " ($date)");
        }
    }

    function create_excel_override_report() {
        $date = date("Y-m-d H:i:s");
        if (!empty($this->session->userdata("inf_date1")) &&!empty($this->session->userdata("inf_date2"))) {

            $user_name = $this->session->userdata("inf_user_name");
            $leader_id = $this->report_model->userNameToID($user_name);

            $week_date1 = $this->session->userdata("inf_date1");
            $week_date2 = $this->session->userdata("inf_date2");

            $purcahse_details = $this->excel_model->getOverRideDetails($week_date1, $week_date2, $leader_id);
            $this->excel_model->writeToExcel($purcahse_details, lang('override_report') . " ($date)");
        }
    }

    function create_excel_payout_released_report_daily() {
        $date = date("Y-m-d H:i:s");
        if (!empty($this->session->userdata('inf_released_report_daily'))) {
            $report_date = $this->session->userdata('inf_released_report_daily');
            $excel_array = $this->excel_model->getReleasedPayoutReport($report_date);
            $this->excel_model->writeToExcel($excel_array, lang('payout_released_report_daily') . " ($date)");
        }
    }
   function create_excel_payout_released_report_weekly() {
        $date = date("Y-m-d H:i:s");
        // if (!empty($this->session->userdata('inf_released_report_from_date')) && !empty($this->session->userdata('inf_released_report_to_date'))) {
            $from_date = $this->input->get('from_date');
            $to_date = $this->input->get('to_date');
            $excel_array = $this->excel_model->getReleasedPayoutReport($from_date, $to_date);
            $this->excel_model->writeToExcel($excel_array, lang('payout_released_report_weekly') . " ($date)");
        // }
    }

    function create_excel_payout_pending_report() {
        $date = date("Y-m-d H:i:s");
        // if (!empty($this->session->userdata('inf_pending_report_from_date')) && !empty($this->session->userdata('inf_pending_report_to_date'))) {
            $from_date = $this->input->get('from_date');
            $to_date = $this->input->get('to_date');
            $excel_array = $this->excel_model->getPendingPayoutReport($from_date, $to_date);
            $this->excel_model->writeToExcel($excel_array, lang('payout_pending_report') . " ($date)");
        //}
    }

    function create_excel_epin_transfer_report() {
        $date = date("Y-m-d H:i:s");
        
        $from_date = $this->input->get('from_date');
        $to_date   = $this->input->get('to_date');
        $user_name = $this->input->get('from_user');
        $to_suer   = $this->input->get('to_user');

        $user_id    = $this->report_model->userNameToID($user_name);
        $to_user_id = $this->report_model->userNameToID($to_suer);

        $excel_array = $this->excel_model->getEpinTransferDetails($from_date, $to_date, $user_id,$to_user_id);
        $this->excel_model->writeToExcel($excel_array, lang('epin_transfer_report') . " ($date)");
    }
    //CREATE CSV FILE
    function create_csv_joining_report_weekly(){
        // dd('hhh');
        $date = date("Y-m-d H:i:s");
        $from_date = $this->input->get('from_date');
        $to_date = $this->input->get('to_date');

        $csv_array = $this->excel_model->getJoiningReportWeekly($from_date, $to_date);
        $this->create_csv($csv_array, lang('user_joining_report_nw'));

    }

    function create_csv_joining_report_daily(){
         $date = date("Y-m-d H:i:s");
        if (!empty($this->session->userdata('inf_total_joining_daily'))) {
            $report_date = $this->session->userdata('inf_total_joining_daily');

            $csv_array = $this->excel_model->getJoiningReportDaily($report_date);
            $this->create_csv($csv_array,lang('user_joining_report_nw') );
        }
    }
    function create_csv_total_payout_report() {
        $date = date("Y-m-d");
        $csv_array = $this->excel_model->getTotalPayoutReport();
        $this->create_csv($csv_array,lang('total_bonus_report'));

    }

    function create_csv_weekly_payout_report() {
        $name = lang('week_wise_bonus_report');

        $date = date("Y-m-d H:i:s");
        $from_date = $this->input->get('from_date');
        $to_date   = $this->input->get('to_date');
        $user_name = $this->input->get('user_name');
        $user_id = $this->report_model->userNameToID($user_name);

        $csv_array = $this->excel_model->getTotalPayoutReport($from_date, $to_date, $user_id);
        $this->create_csv($csv_array, $name);
    }

    function create_csv_top_earners_report() {
        $date = date("Y-m-d");
        $csv_array = $this->excel_model->getTopEarnersReport();
        $this->create_csv($csv_array,lang('top_earners_report_nw') );
    }

    function create_csv_member_wise_payout_report() {
        $date = date("Y-m-d H:i:s");
        if (!empty($this->session->userdata("inf_user_name_payout"))) {
            $user_name = $this->session->userdata("inf_user_name_payout");
            $csv_array = $this->excel_model->getMemberPayoutReport($user_name);
            $this->create_csv($csv_array, lang('member_wise_bonus_report') );
        }
    }

    function create_csv_sales_report($product_type="") {
        $date = date("Y-m-d H:i:s");
        $from_date = $this->input->get('from_date');
        $to_date   = $this->input->get('to_date');
        $package   = $this->input->get('package');

        $csv_array = $this->excel_model->getSalesReport($from_date, $to_date,$product_type,$package);
            $this->create_csv($csv_array, lang('sales_report_nw'));
    }

    function create_csv_product_sales_report($product_type="") {
        $date = date("Y-m-d");
        if (!empty($this->session->userdata("inf_product_sales_id"))) {
            $prod_id = $this->session->userdata("inf_product_sales_id");
            $csv_array = $this->excel_model->productSalesReport($prod_id,$product_type);
            $this->create_csv($csv_array, lang('tran_product_wise_sales_report_nw'));
        }
    }

    function create_csv_rank_achievers_report() {
        $date = date("Y-m-d H:i:s");

        $from_date = $this->input->get('from_date');
        $to_date   = $this->input->get('to_date');
        $rank_data     = $this->input->get("ranks");

        // parse_str($rank_data, $ranks);
        $ranks = ($rank_data)?explode(",", $rank_data):"";
        if($ranks && !count($ranks))
            $ranks = "";

         $csv_array = $this->excel_model->getRankAchieversReport($from_date, $to_date, $ranks);
         $this->create_csv($csv_array, lang('rank_achievers_report_nw') );
    }

    function create_csv_profile_view_report() {
        $date = date("Y-m-d H:i:s");
        if (!empty($this->session->userdata("inf_profile_report_view_user_name"))) {
            $user_name = $this->session->userdata("inf_profile_report_view_user_name");
            $csv_array = $this->excel_model->getProfileViewReport($user_name);
            $this->create_csv($csv_array,lang('profile_report_nw'));

        }
    }

    function user_profiles_csv() {
        $this->session->userdata('inf_profile_type');
        if ($this->session->userdata('inf_profile_type') == "one_count") {
            $cnt = $this->session->userdata('inf_profile_count');
            $arr = $this->excel_model->getProfiles($cnt);
            $date = date("Y-m-d H:i:s");
            $this->create_csv($arr,lang('profile_report_nw'));

        } else if ($this->session->userdata('inf_profile_type') == "two_count") {
            $count_from = $this->session->userdata('inf_count_from');
            $count_to = $this->session->userdata('inf_count_to');
            $date = date("Y-m-d H:i:s");
            $arr = $this->excel_model->getProfilesFrom($count_from, $count_to);
            $this->create_csv($arr,lang('profile_report_nw'));

        }
    }


    function create_csv_epin_report() {
        $date = date("Y-m-d H:i:s");
        $csv_array = $this->excel_model->getEpinReport();
        $this->create_csv($csv_array,lang('epin_report_nw'));
    }

    function create_csv_commission_report($user_name = '') {
        $user_id = $this->validation_model->userNameToID($user_name);
        if ($user_name && !$user_id) {
            $msg = lang('invalid_username');
            $this->redirect($msg, 'select_report/commission_report', FALSE);
        }
        $date = date("Y-m-d H:i:s");
        $from_date  = $this->input->get('from_date');
        $to_date = $this->input->get('to_date');
        if( $from_date!='')
        {
            $from_date = $from_date . " 00:00:00";
        }
        if($to_date!=''){ 
           $to_date = $to_date . " 23:59:59";
        }
        $type = $this->session->userdata("inf_commision_type");
        
        $csv_array = $this->excel_model->getCommissionReport($from_date, $to_date, $type, $user_id);
            $this->create_csv($csv_array,lang('commission_report_nw') );
    }

    function create_csv_repurchase_report($user_name="") {
        //$date = date("Y-m-d H:i:s");
         $user_id = $this->validation_model->userNameToID($user_name);
        if ($user_name && !$user_id) {
            $msg = lang('invalid_username');
            $this->redirect($msg, 'select_report/commission_report', FALSE);
        }
        $date = date("Y-m-d H:i:s");
        $from_date  = $this->input->get('from_date');
        $to_date = $this->input->get('to_date');
            $csv_array = $this->excel_model->getRepurchaseReport($from_date, $to_date, $user_id);
            $this->create_csv($csv_array,lang('repurchase_report_nw'));

        //}
    }


    function create_csv_payout_released_report_daily() {
        $date = date("Y-m-d H:i:s");
        if (!empty($this->session->userdata('inf_released_report_daily'))) {
            $report_date = $this->session->userdata('inf_released_report_daily');
            $csv_array = $this->excel_model->getReleasedPayoutReport($report_date);
            $this->create_csv($csv_array,lang('payout_released_report_daily_nw'));
        }
    }

    function create_csv_payout_released_report_weekly() {
        $date = date("Y-m-d H:i:s");
        // if (!empty($this->session->userdata('inf_released_report_from_date')) && !empty($this->session->userdata('inf_released_report_to_date'))) {
            $from_date = $this->input->get('from_date');
            $to_date = $this->input->get('to_date');
            $csv_array = $this->excel_model->getReleasedPayoutReport($from_date, $to_date);
            $this->create_csv($csv_array,lang('payout_released_report_weekly') );
        // }
    }

    function create_csv_payout_pending_report() {
        $date = date("Y-m-d H:i:s");
        // if (!empty($this->session->userdata('inf_pending_report_from_date')) && !empty($this->session->userdata('inf_pending_report_to_date'))) {
            $from_date = $this->input->get('from_date');
            $to_date = $this->input->get('to_date');
            $csv_array = $this->excel_model->getPendingPayoutReport($from_date, $to_date);
            $this->create_csv($csv_array, lang('payout_pending_report_nw'));
        //}
    }

    function create_csv($csv_array,$file_name= '') {

            $header = "Content-Disposition: attachment; filename=$file_name.csv";
            header("Content-type: application/csv");
            header($header);
            header("Pragma: no-cache");
            header("Expires: 0");

            $handle = fopen('php://output', 'w');
            foreach ( $csv_array as $line) {

            fputcsv($handle,$line );
            }
            fclose($handle);
    }

    function create_csv_epin_transfer_report() {
        $date = date("Y-m-d H:i:s");

        $from_date = $this->input->get('from_date');
        $to_date   = $this->input->get('to_date');
        $user_name = $this->input->get('from_user');
        $to_suer   = $this->input->get('to_user');

        $user_id    = $this->report_model->userNameToID($user_name);
        $to_user_id = $this->report_model->userNameToID($to_suer);

        $csv_array = $this->excel_model->getEpinTransferDetails($from_date, $to_date, $user_id,$to_user_id);
        $this->create_csv($csv_array,lang('epin_transfer_report'));
    }

    function create_config_changes_report() {
        $date = date("Y-m-d H:i:s");
        if (!empty($this->session->userdata("from_date")) &&!empty($this->session->userdata("to_date")))     {
            $from_date = $this->session->userdata("from_date");
            $to_date = $this->session->userdata("to_date");

            $excel_array = $this->excel_model->getConfigChangesReport($from_date,$to_date);


            $this->excel_model->writeToExcel($excel_array, lang('config_change_report') . " ($date)");

        }
    }

    function create_csv_config_changes_report() {
        if (!empty($this->session->userdata("from_date")) &&!empty($this->session->userdata("to_date"))) {
            $from_date = $this->session->userdata("from_date");
            $to_date = $this->session->userdata("to_date");
            $csv_array = $this->excel_model->getConfigChangesReport($from_date,$to_date);

            $this->create_csv($csv_array,lang('config_change_report'));
        }
    }

    function create_excel_roi_report() {
        $date = date("Y-m-d H:i:s");

        $from_date = $this->input->get('from_date');
        $to_date   = $this->input->get('to_date');
        $user_name = $this->input->get('user_name');

        $user_id    = $this->report_model->userNameToID($user_name);
        if($user_id == 0){
           $user_id = '';
        }
        
        $excel_array = $this->excel_model->getroiReport($from_date, $to_date, $user_id);
            $this->excel_model->writeToExcel($excel_array, lang('roi_report') . " ($date)");

    }

    function create_csv_roi_report() {
        $date = date("Y-m-d H:i:s");

        $from_date = $this->input->get('from_date');
        $to_date   = $this->input->get('to_date');
        $user_name = $this->input->get('user_name');

        $user_id    = $this->report_model->userNameToID($user_name);
        if($user_id == 0){
           $user_id = '';
        }

        $csv_array = $this->excel_model->getroiReport($from_date, $to_date, $user_id);
            $this->create_csv($csv_array,lang('roi_report_nw'));
    }
    function personalDtaExport() {
        $date = date("Y-m-d H:i:s");
            $csv_array = $this->excel_model->getPersonalDta($this->LOG_USER_NAME);
            $this->create_csv($csv_array,lang('personal_data_report'));
    }
    function exelPersonalDtaExport() {
        $date = date("Y-m-d H:i:s");
        $excel_array = $this->excel_model->getPersonalDta($this->LOG_USER_NAME);
        $this->excel_model->writeToExcel($excel_array, lang('personal_data_report') . " ($date)");
    }

    public function create_excel_package_upgrade_report($user_name="")
    {   $date = date("Y-m-d H:i:s");
        $user_id=$this->report_model->userNameToID($user_name);
        //$product_name=$this->input->get('product_name');
        $product_id1=$this->input->get('product_id1');
        $product_id=$this->select_report_model->getProdIDFromProductName($product_id1);
         $excel_array = $this->excel_model->getPackageUpgradeReport($user_id, $product_id);
         $this->excel_model->writeToExcel($excel_array, lang('package_upgrade_report') . " ($date)");

    }
  public function create_csv_package_upgrade_report($user_name="")
  {
    
        $date = date("Y-m-d H:i:s");
        $user_id=$this->report_model->userNameToID($user_name);
        $product_id1=$this->input->get('product_id1');
        // $product_name=$this->input->get('product_name');
         $product_id=$this->select_report_model->getProdIDFromProductName($product_id1);

        $csv_array = $this->excel_model->getPackageUpgradeReport($user_id,$product_id);
            $this->create_csv($csv_array,lang('package_upgrade_report'));

  }

    function create_csv_activate_deactivate_report_view() {
        $date = date("Y-m-d H:i:s");
        $from_date = $this->input->get('from_date');
        $to_date = $this->input->get('to_date');
        if( $from_date!='')
        {
            $from_date = $from_date . " 00:00:00";
        }
        if($to_date!=''){ 
           $to_date = $to_date . " 23:59:59";
        }
        $excel_array = $this->excel_model->getActiveInactiveReport($from_date, $to_date);
        $this->create_csv($excel_array, lang('activate_deactivate_report') . " ($date)");
    }

    public function create_excel_rank_performance_report()
    {
        $user_name = $this->input->get('username');
        if($user_name) {
            $user_id = $this->validation_model->userNameToID($user_name);
            if($user_id) {
                $excel_array = $this->excel_model->formRankPerfomanceArray($user_id, $user_name);
                foreach ($excel_array as $key=>$result ){
                    unset($excel_array[0]);
                }
                $this->excel_model->writeToExcel($excel_array, lang('rank_performance_report') . " ($user_name)");
            }
        }
    }

    public function create_csv_rank_performance_report()
    {
        $user_name = $this->input->get('username');
        if($user_name) {
            $user_id = $this->validation_model->userNameToID($user_name);
            if($user_id) {
                $excel_array = $this->excel_model->formRankPerfomanceArray($user_id, $user_name);
                $this->create_csv($excel_array, lang('rank_performance_report') . " ($user_name)");
            }
        }
    }

    public function create_csv_subscription_report()
    {
        $user_name = $this->input->get('username');
        $user_id = "";
        if($user_name) {
            $user_id = $this->validation_model->userNameToID($user_name);
        }
        $excel_array = $this->excel_model->getSubscriptionReportArray($user_id);
        $this->create_csv($excel_array, lang('subscription_report'));
    }

    public function create_excel_subscription_report()
    {
        $user_name = $this->input->get('username');
        $user_id = "";
        if($user_name) {
            $user_id = $this->validation_model->userNameToID($user_name);
        }
        $excel_array = $this->excel_model->getSubscriptionReportArray($user_id);
        $this->excel_model->writeToExcel($excel_array, lang('subscription_report'));
    }

    public function create_excel_agent_view_report()
    {

        $user_name = $this->input->get('agent_username');
        $user_id = "";
        if($user_name) {
            $user_id = $this->validation_model->agentUserNameToID($user_name);
        }
        $excel_array = $this->excel_model->getAgentCreditReportArray($user_id);
        $this->excel_model->writeToExcel($excel_array, lang('agent_credit_report'));
    }

    public function create_csv_agent_view_report()
    {

        $user_name = $this->input->get('agent_username');
        $user_id = "";
        if($user_name) {
            $user_id = $this->validation_model->agentUserNameToID($user_name);
        }
        $excel_array = $this->excel_model->getAgentCreditReportArray($user_id);
        $this->create_csv($excel_array, lang('agent_credit_report'));
    }
    public function create_csv_voucher_report()
    {
        $user_name = $this->input->get('username');
        $user_id = "";
        if($user_name) {
            $user_id = $this->validation_model->userNameToID($user_name);
        }
        $excel_array = $this->excel_model->getVoucherReportArray($user_id);
        $this->create_csv($excel_array, lang('Voucher Report'));
    }

    public function create_excel_voucher_report()
    {
        $user_name = $this->input->get('username');
        $user_id = "";
        if($user_name) {
            $user_id = $this->validation_model->userNameToID($user_name);
        }
        $excel_array = $this->excel_model->getVoucherReportArray($user_id);
        $this->excel_model->writeToExcel($excel_array, lang('Voucher Report'));
    }
    function create_excel_custom_wallet_report() {
        $date = date("Y-m-d H:i:s");
        $excel_array = $this->excel_model->getCustomWalletReport();
        $this->excel_model->writeToExcel($excel_array, lang('ewallet_transaction_report') . " ($date)");
    }
    function create_excel_group_pv_report() {
        $date = date("Y-m-d H:i:s");
        $user_name = $this->input->get('username');
        $user_id = "";
        if($user_name) {
            $user_id = $this->validation_model->userNameToID($user_name);
        }
        $date_range = $this->input->get('date_range');
        $from_date = $this->input->get('from_date');
        $to_date = $this->input->get('to_date');
        if( $from_date!='')
        {
            $from_date = $from_date . " 00:00:00";
        }
        if($to_date!=''){ 
           $to_date = $to_date . " 23:59:59";
        }

        $excel_array = $this->excel_model->getGroupPV($user_id , $date_range, $from_date, $to_date);
        $this->excel_model->writeToExcel($excel_array, lang('group_pv_report') . " ($date)");
    }
    function create_csv_group_pv_report() {
        $date = date("Y-m-d H:i:s");
        $user_name = $this->input->get('username');
        $user_id = "";
        if($user_name) {
            $user_id = $this->validation_model->userNameToID($user_name);
        }
        $date_range = $this->input->get('date_range');
        $from_date = $this->input->get('from_date');
        $to_date = $this->input->get('to_date');
        if( $from_date!='')
        {
            $from_date = $from_date . " 00:00:00";
        }
        if($to_date!=''){ 
           $to_date = $to_date . " 23:59:59";
        }

        $excel_array = $this->excel_model->getGroupPV($user_id , $date_range, $from_date, $to_date);
        $this->create_csv($excel_array, lang('group_pv_report') . " ($date)");
    } 
}
