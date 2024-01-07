<?php

class excel_model extends inf_model {

    private $obj_xml;
    private $symbol_left;
    private $symbol_right;

    public function __construct() {
        parent::__construct();
        $this->load->model('validation_model');
        require_once 'excel/class-excel-xml.inc.php';
        $this->obj_xml = new Excel_XML();
        $this->load->model('payout_model');
        $this->load->model('report_model');
        $this->load->model('ewallet_model');
       
        $this->load->model('select_report_model');

        $this->setUTFCurrencySymbol();
    }

    public function setUTFCurrencySymbol() {
        $this->symbol_left = $this->DEFAULT_SYMBOL_LEFT;
        $this->symbol_right = $this->DEFAULT_SYMBOL_RIGHT;
        if ($this->DEFAULT_SYMBOL_LEFT != '$') {
            $this->symbol_left = '';
        }
        if ($this->DEFAULT_SYMBOL_RIGHT != '$') {
            $this->symbol_right = '';
        }
    }

    public function writeToExcel($doc_arr, $file_name) {
        $this->obj_xml->addArray($doc_arr);
        $this->obj_xml->generateXML("$file_name");
    }

    public function getProfiles($cnt) {
        $excel_array = array();
        $details_arr = $this->report_model->profileReport($cnt);
        $detail_count = count($details_arr);
        $excel_array[1] = array(lang('member_name'),lang('sponsor'),lang('email'),lang('mobile_no'),lang('country'),lang('zipcode'),lang('enrollment_date'));
        for ($i = 2; $i <= $detail_count + 1; $i++) {
            $excel_array[$i][0] = "{$details_arr[$i - 2]["user_detail_name"]} {$details_arr[$i - 2]["user_detail_second_name"]} ({$details_arr[$i - 2]["uname"]})";
            $excel_array[$i][1] = $details_arr[$i - 2]['sponser_name'];
            $excel_array[$i][2] = $details_arr[$i - 2]["user_detail_email"];
            $excel_array[$i][3] = $details_arr[$i - 2]["user_detail_mobile"];
            $excel_array[$i][4] = $details_arr[$i - 2]["user_detail_country"];
            $excel_array[$i][5] = $details_arr[$i - 2]["user_detail_pin"];
            $excel_array[$i][6] = date('d M Y - h:i:s A', strtotime($details_arr[$i - 2]["join_date"]));
        }
        return $excel_array;
    }

    public function getProfilesFrom($count_from, $count_to) {
        $excel_array = array();
        $details_arr = $this->report_model->profileReportFromTo($count_to, $count_from);
        $detail_count = count($details_arr);
        $excel_array[1] = array(lang('member_name'), lang('sponsor'), lang('email'), lang('mobile_no'), lang('country'), lang('zipcode'), lang('enrollment_date'));
        for ($i = 2; $i <= $detail_count + 1; $i++) {
            $excel_array[$i][0] = "{$details_arr[$i - 2]["user_detail_name"]} {$details_arr[$i - 2]["user_detail_second_name"]} ({$details_arr[$i - 2]["uname"]})";
            $excel_array[$i][1] = $details_arr[$i - 2]['sponser_name'];
            $excel_array[$i][2] = $details_arr[$i - 2]["user_detail_email"];
            $excel_array[$i][3] = $details_arr[$i - 2]["user_detail_mobile"];
            $excel_array[$i][4] = $details_arr[$i - 2]["user_detail_country"];
            $excel_array[$i][5] = $details_arr[$i - 2]["user_detail_pin"];
            $excel_array[$i][6] = date('d M Y - h:i:s A', strtotime($details_arr[$i - 2]["join_date"]));
        }
        return $excel_array;
    }

    public function getJoiningReportDaily($date) {
        $joinings_arr = $this->report_model->getTodaysJoining($date);
        $count = count($joinings_arr);
        $excel_array[1] = array(lang('user_name'), lang('full_name'), lang('upline_name'), lang('sponsor_name'), lang('status'), lang('date_of_joining'));
        for ($i = 2; $i <= $count + 1; $i++) {
            $j = $i - 2;
            $excel_array[$i][0] = $joinings_arr["detail$j"]["user_name"];
            $excel_array[$i][1] = $joinings_arr["detail$j"]["user_full_name"];
            $excel_array[$i][2] = $joinings_arr["detail$j"]["father_user"];
            $excel_array[$i][3] = $joinings_arr["detail$j"]["sponsor_name"];
            if ($joinings_arr["detail$j"]['active'] == 'yes') {
                $excel_array[$i][4] = lang('active');
            } else {
                $excel_array[$i][4] = lang('blocked');
            }
            $excel_array[$i][5] = date('Y/m/d', strtotime($joinings_arr["detail$j"]["date_of_joining"]));
        }
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }

    public function getJoiningReportWeekly($from_date, $to_date) {
        $this->load->model('country_state_model');
        $showRegFee = $this->report_model->checkRegistrationFeeDisplay();
        $joinings_arr = $this->report_model->getWeeklyJoining($from_date, $to_date);
        $count = count($joinings_arr);
        $excel_array[1] = array();
        $excel_array[1][] = lang('member_name');
        $excel_array[1][] = lang('Country');
        $excel_array[1][] = lang('sponsor');
        $excel_array[1][] = lang('package');
        if($showRegFee == 'yes')
            $excel_array[1][] = lang('registration_fee');
        $excel_array[1][] = lang('payment_method');
        $excel_array[1][] = lang('date_of_joining');
        for ($i = 2; $i <= $count + 1; $i++) {
            $j = $i - 2;
            $excel_array[$i] = [];
            if($joinings_arr["detail$j"]['delete_status'] == 'active') {
                $excel_array[$i][] = "{$joinings_arr["detail$j"]["user_full_name"]} ({$joinings_arr["detail$j"]["user_name"]})";
            } else {
                $excel_array[$i][] = $joinings_arr["detail$j"]["user_name"];
            }
            $excel_array[$i][] = $joinings_arr["detail$j"]["country"];
            $excel_array[$i][] = $joinings_arr["detail$j"]["sponsor_name"];
            $package = $joinings_arr["detail$j"]["package_name"];
            if($package != lang('na'))
                $package .= "(".format_currency($joinings_arr["detail$j"]["package_amount"]).")";
            $excel_array[$i][] = $package;
            if($showRegFee == 'yes')
                $excel_array[$i][] = format_currency($joinings_arr["detail$j"]["reg_amount"]);
            $excel_array[$i][] = $joinings_arr["detail$j"]["paymode"];
            $excel_array[$i][] = date('d M Y - h:i:s A', strtotime($joinings_arr["detail$j"]["date_of_joining"]));
        }
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }

    public function getTotalPayoutReport($from_date = '', $to_date = '', $user_id = '') {
        $sumTotal = 0;
        $sumTDS = 0;
        $sumServiceCharge = 0;
        $sumPayable = 0;

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

        if ($from_date == '' && $to_date == '') {
            $total_payout_array = $this->report_model->getTotalPayout('', '', $user_id);
        } else {
            $total_payout_array = $this->report_model->getTotalPayout($from_date, $to_date,$user_id);
        }
        $count = count($total_payout_array);
        $excel_array[1] = array();
        $excel_array[1][] = lang('full_name');
        // $excel_array[1][] = lang('address');
        // $excel_array[1][] = lang('bank');
        // $excel_array[1][] = lang('account_no');
        $excel_array[1][] = lang('total_amount');
        if($showTDS == "yes")
            $excel_array[1][] = lang('tds');
        if($showServiceCharge == "yes")
            $excel_array[1][] = lang('service_charge');
        if($showAmountPayable == "yes")
            $excel_array[1][] = lang('amount_payable');
        for ($i = 2; $i <= $count + 1; $i++) {
            $j = $i - 2;
            $excel_array[$i] = [];
            $excel_array[$i][] = "{$total_payout_array["detail$j"]["full_name"]} ({$total_payout_array["detail$j"]["user_name"]})";
            // $excel_array[$i][] = $total_payout_array["detail$j"]["user_address"];
            // $excel_array[$i][] = $total_payout_array["detail$j"]["user_bank"];
            // $excel_array[$i][] = $total_payout_array["detail$j"]["acc_number"];
            $excel_array[$i][] = format_currency($total_payout_array["detail$j"]["total_amount"]);
            if($showTDS == "yes")
                $excel_array[$i][] = format_currency($total_payout_array["detail$j"]["tds"]);
            if($showServiceCharge == "yes")
                $excel_array[$i][] =  format_currency($total_payout_array["detail$j"]["service_charge"]) ;
            if($showAmountPayable == "yes")
                $excel_array[$i][] = format_currency($total_payout_array["detail$j"]["amount_payable"]);
            $sumTotal += $total_payout_array["detail$j"]["total_amount"];
            $sumTDS += $total_payout_array["detail$j"]["tds"];
            $sumServiceCharge += $total_payout_array["detail$j"]["service_charge"];
            $sumPayable += $total_payout_array["detail$j"]["amount_payable"];
        }
        $excel_array[$i+1] = [];
        $excel_array[$i+1][] = 'Total';
        $excel_array[$i+1][] = format_currency($sumTotal);
        if($showTDS == "yes")
            $excel_array[$i+1][] = format_currency($sumTDS);
        if($showServiceCharge == "yes")
            $excel_array[$i+1][] = format_currency($sumServiceCharge);
        if($showAmountPayable == "yes")
            $excel_array[$i+1][] = format_currency($sumPayable);
        $excel_array = $this->replaceNullFromArray($excel_array);

        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }

    public function getRankAchieversReport($from_date, $to_date, $ranks) {
        $ranked_users_array = $this->report_model->rankedUsers($ranks, $from_date, $to_date);
        $count = count($ranked_users_array);
        $excel_array = [];
        $excel_array[1] = array(lang('new_rank'), lang('member_name'), lang('rank_achieved_date'));
        for ($i = 2; $i <= $count + 1; $i++) {
            $excel_array[$i] = [];
            $excel_array[$i][] = $ranked_users_array[$i - 2]["rank_name"];
            $excel_array[$i][] = $ranked_users_array[$i - 2]["user_detail_name"] . "(".$ranked_users_array[$i - 2]["user_name"].")";
            $excel_array[$i][] = date("d M Y - h:i:s A", strtotime($ranked_users_array[$i - 2]["date"]));
        }
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }

    public function getCommissionReport($from_date, $to_date, $type, $user_id) {
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

        $sum1 = 0;
        $sum2 = 0;
        $sumTDS = 0;
        $sumServiceCharge = 0;
        $commission_details_array = $this->report_model->getCommisionDetails($type, $from_date, $to_date, $user_id);
        $count = count($commission_details_array);
        $excel_array[1] = array();
        if(!$user_id)
            $excel_array[1][] = lang('member_name');
        $excel_array[1][] = lang('amount_type');
        $excel_array[1][] = lang('total_amount');
        if($showTDS == "yes")
            $excel_array[1][] = lang('tds');
        if($showServiceCharge == "yes")
            $excel_array[1][] = lang('service_charge');
        if($showAmountPayable == "yes")
            $excel_array[1][] = lang('amount_payable');
        $excel_array[1][] = lang('date');
        for ($i = 2; $i <= $count + 1; $i++) {
            $excel_array[$i] = [];
            if(!$user_id) {
                if($commission_details_array[$i - 2]["delete_status"] == 'active') {
                    $excel_array[$i][] = "{$commission_details_array[$i - 2]["full_name"]} ({$commission_details_array[$i - 2]["user_name"]})";
                } else {
                    $excel_array[$i][] = $commission_details_array[$i - 2]["user_name"];
                }
            }
            $excel_array[$i][] = $commission_details_array[$i - 2]["view_amt"];
            $excel_array[$i][] = format_currency($commission_details_array[$i - 2]["total_amount"]);
            if($showTDS == "yes")
                $excel_array[$i][] = format_currency($commission_details_array[$i - 2]["tds"]);
            if($showServiceCharge == "yes")
                $excel_array[$i][] = format_currency($commission_details_array[$i - 2]["service_charge"]);
            if($showAmountPayable == "yes")
                $excel_array[$i][] =  format_currency($commission_details_array[$i - 2]["amount_payable"]);
            $excel_array[$i][] = date('d M Y - h:i:s A', strtotime($commission_details_array[$i - 2]["date"]));
            
            $sum1 = $sum1 + $commission_details_array[$i - 2]["total_amount"];
            $sum2 = $sum2 + $commission_details_array[$i - 2]["amount_payable"];
            $sumTDS += $commission_details_array[$i - 2]["tds"];
            $sumServiceCharge += $commission_details_array[$i - 2]["service_charge"];
        }
        $excel_array[$i+1] = [];
        if(!$user_id)
            $excel_array[$i+1][] = ' ';
        $excel_array[$i+1][] = lang('total');
        $excel_array[$i+1][] = format_currency($sum1);
        if($showTDS == "yes")
            $excel_array[$i+1][] = format_currency($sumTDS);
        if($showServiceCharge == "yes")
            $excel_array[$i+1][] = format_currency($sumServiceCharge);
        if($showAmountPayable == "yes")
            $excel_array[$i+1][] = format_currency($sum2);
        $excel_array[$i+1][] = ' ';
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }

    public function getEpinReport() {
        $epin_details_array = $this->report_model->getUsedPin();
        $count = count($epin_details_array);
        $excel_array[1] = array(lang('used_user'), lang('epin'), lang('pin_uploaded_date'), lang('pin_amount'), lang('pin_balance_amount'));
        for ($i = 2; $i <= $count + 1; $i++) {
            $excel_array[$i][0] = $epin_details_array[$i - 2]["used_user"];
            $excel_array[$i][1] = $epin_details_array[$i - 2]["pin_number"];
            $excel_array[$i][2] = $epin_details_array[$i - 2]["pin_alloc_date"];
            $excel_array[$i][3] = round($epin_details_array[$i - 2]["pin_amount"] * $this->DEFAULT_CURRENCY_VALUE, 8);
            $excel_array[$i][4] = round($epin_details_array[$i - 2]["pin_balance_amount"] * $this->DEFAULT_CURRENCY_VALUE, 8);
        }
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }

    public function getTopEarnersReport() {
        $top_earners_array = $this->select_report_model->getTopEarners();
        $count = count($top_earners_array);
        $excel_array[1] = array(lang('member_name'), lang('current_balance'), lang('total_earnings'));
        for ($i = 2; $i <= $count + 1; $i++) {
            $j = $i - 2;
            $excel_array[$i][0] = "{$top_earners_array["details$j"]["name"]} ({$top_earners_array["details$j"]["user_name"]})";
            $excel_array[$i][1] = format_currency($top_earners_array["details$j"]["current_balance"]);
            $excel_array[$i][2] = format_currency($top_earners_array["details$j"]["total_earnings"]);
        }
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }

    public function getProfileViewReport($user_name) {
        $profile_details_array = $this->report_model->getProfileDetails($user_name);
        $excel_array[1] = [lang('member_name'), lang('sponsor'), lang('email'), lang('mobile_no'), lang('country'), lang('zipcode'), lang('enrollment_date')];
        $excel_array[2] = [
                $profile_details_array["details"][0]['user_detail_name'] . "($user_name)",
                $profile_details_array["details"][0]['user_name'],
                $profile_details_array["details"][0]['user_detail_email'],
                $profile_details_array["details"][0]['user_detail_mobile'],
                $profile_details_array["details"][0]['user_detail_country'],
                $profile_details_array["details"][0]['user_detail_pin'],
                date("d M Y - h:i:s A", strtotime($profile_details_array["details"][0]['join_date']))
            ];
        
        /*$excel_array[1][0] = lang('member_name');
        $excel_array[1][1] = $profile_details_array["details"][0]['user_detail_name'] . "($user_name)";

        $excel_array[2][0] = lang('sponsor');
        $excel_array[2][1] = $profile_details_array["details"][0]['user_name'];

        $excel_array[3][0] = lang('email');
        $excel_array[3][1] = $profile_details_array["details"][0]['user_detail_email'];

        $excel_array[4][0] = lang('mobile_no');
        $excel_array[4][1] = $profile_details_array["details"][0]['user_detail_mobile'];

        $excel_array[5][0] = lang('country');
        $excel_array[5][1] = $profile_details_array["details"][0]['user_detail_country'];

        $excel_array[6][0] = lang('zipcode');
        $excel_array[6][1] = $profile_details_array["details"][0]['user_detail_pin'];

        $excel_array[7][0] = lang('enrollment_date');
        $excel_array[7][1] = date("d M Y - h:i:s A", strtotime($profile_details_array["details"][0]['join_date']));*/

        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }

    public function getSalesReport($from_date, $to_date,$product_type , $package = '') {
        $sum = 0;
        if($product_type=="repurchase")
            $sales_report_array = $this->report_model->productRepurchaseSalesReport($package,$from_date, $to_date);
        else    
            $sales_report_array = $this->report_model->salesReport($from_date, $to_date,$package);
        
        $count = count($sales_report_array);
        $excel_array[1] = array(lang('invoice_no'), lang('prod_name'), lang('user_name'), lang('payment_method'), lang('amount'));
        for ($i = 2; $i <= $count + 1; $i++) {
            $excel_array[$i][0] = $sales_report_array[$i - 2]["invoice_no"];
            $excel_array[$i][1] = $sales_report_array[$i - 2]["prod_id"];
            $excel_array[$i][2] = $sales_report_array[$i - 2]["user_id"];
            $excel_array[$i][3] = lang($sales_report_array[$i - 2]["payment_method"]);
            $excel_array[$i][4] = round($sales_report_array[$i - 2]["amount"] * $this->DEFAULT_CURRENCY_VALUE, 8);
            $sum = $sum + round($sales_report_array[$i - 2]["amount"] * $this->DEFAULT_CURRENCY_VALUE, 8);
        }
        $excel_array[$i+1][0] = ' ';
        $excel_array[$i+1][1] = ' ';
        $excel_array[$i+1][2] = ' ';
        $excel_array[$i+1][3] = 'Total';
        $excel_array[$i+1][4] = $sum;
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }

    public function productSalesReport($prod_id,$product_type="register") {
        $sum = 0;
        if($product_type == "repurchase")
            $sales_report_array = $this->report_model->productRepurchaseSalesReport($prod_id);
        else
            $sales_report_array = $this->report_model->productSalesReport($prod_id);
        $count = count($sales_report_array);
        $excel_array[1] = array(lang('invoice_no'), lang('prod_name'), lang('user_name'), lang('payment_method'), lang('amount'));
        for ($i = 2; $i <= $count + 1; $i++) {
            $excel_array[$i][0] = $sales_report_array[$i - 2]["invoice_no"];
            $excel_array[$i][1] = $sales_report_array[$i - 2]["prod_id"];
            $excel_array[$i][2] = $sales_report_array[$i - 2]["user_id"];
            $excel_array[$i][3] = lang($sales_report_array[$i - 2]["payment_method"]);
            $excel_array[$i][4] = round($sales_report_array[$i - 2]["amount"] * $this->DEFAULT_CURRENCY_VALUE, 8) ;
            $sum = $sum + round($sales_report_array[$i - 2]["amount"] * $this->DEFAULT_CURRENCY_VALUE, 8);
        }
        $excel_array[$i+1][0] = ' ';
        $excel_array[$i+1][1] = ' ';
        $excel_array[$i+1][2] = ' ';
        $excel_array[$i+1][3] = 'Total';
        $excel_array[$i+1][4] = $sum;
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }

    public function getMemberPayoutReport($user_name) {
        $member_payout_array = $this->report_model->getMemberPayout($user_name);
        $excel_array[1][0] = lang('user_name');
        $excel_array[1][1] = $member_payout_array['user_name'];

        $excel_array[2][0] = lang('user_full_name');
        $excel_array[2][1] = $member_payout_array['full_name'];

        $excel_array[3][0] = lang('address');
        $excel_array[3][1] = $member_payout_array['user_address'];

        $excel_array[4][0] = lang('bank');
        $excel_array[4][1] = $member_payout_array['user_bank'];

        $excel_array[5][0] = lang('account_no');
        $excel_array[5][1] = $member_payout_array['acc_number'];

        $excel_array[6][0] = lang('total_amount');
        $excel_array[6][1] = round($member_payout_array['total_amount'] * $this->DEFAULT_CURRENCY_VALUE, 8);
        $excel_array[7][0] = lang('tds');
        $excel_array[7][1] = round($member_payout_array['tds'] * $this->DEFAULT_CURRENCY_VALUE, 8);

        $excel_array[8][0] = lang('service_charge');
        $excel_array[8][1] = round($member_payout_array['service_charge'] * $this->DEFAULT_CURRENCY_VALUE, 8);

        $excel_array[9][0] = lang('amount_payable');
        $excel_array[9][1] = round($member_payout_array['amount_payable'] * $this->DEFAULT_CURRENCY_VALUE, 8);

        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }

    public function replaceNullFromArray($user_detail, $replace = '') {
        if ($replace == '') {
            $replace = "NA";
        }

        $len = count($user_detail);
        $key_up_arr = array_keys($user_detail);
        for ($i = 1; $i <= $len; $i++) {
            $k = $i - 1;
            $fild = $key_up_arr[$k];
            $arr_key = array_keys($user_detail["$fild"]);
            $key_len = count($arr_key);
            for ($j = 0; $j < $key_len; $j++) {
                $key_field = $arr_key[$j];
                if ($user_detail["$fild"]["$key_field"] == "") {
                    $user_detail["$fild"]["$key_field"] = $replace;
                }
            }
        }
        return $user_detail;
    }

    public function getActiveInactiveReport($from_date, $to_date) {
        $active_deactive_arr = $this->report_model->getAciveDeactiveUserDetails($from_date, $to_date);
        $count = count($active_deactive_arr);
        $excel_array[1] = array(lang('member_name'), lang('status'), lang('active_deactive_date'));
        for ($i = 2; $i <= $count + 1; $i++) {
            $j = $i - 2;
            $excel_array[$i][0] = "{$active_deactive_arr["$j"]["full_name"]} ({$active_deactive_arr["$j"]["user_name"]})";
            $excel_array[$i][1] = $active_deactive_arr["$j"]["status"];
            $excel_array[$i][2] = $active_deactive_arr["$j"]["date"];
        }
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }
    public function getRepurchaseReport($from_date, $to_date, $user_id="") {
        $total_amount = 0;
        $repurchase_arr = $this->report_model->getRepurchaseDetails($from_date, $to_date, $user_id);
        $count = count($repurchase_arr); 
        $excel_array[1] = array();
        $excel_array[1][] = lang('invoice_no');
        if(!$user_id)
            $excel_array[1][] = lang('member_name');
        $excel_array[1][] = lang('total_amount');
        $excel_array[1][] = lang('payment_method');
        $excel_array[1][] = lang('purchase_date');
        for ($i = 2; $i <= $count + 1; $i++) {
            $j = $i - 2;
            $excel_array[$i] = [];
            $excel_array[$i][] = $repurchase_arr["$j"]["invoice_no"];
            if(!$user_id) {
                if($repurchase_arr["$j"]["delete_status"] == "active")  {
                    $excel_array[$i][] = $repurchase_arr["$j"]["full_name"]."(".$repurchase_arr["$j"]["user_name"].")";
                } else {
                    $excel_array[$i][] = $repurchase_arr["$j"]["user_name"];
                }

            }
            $excel_array[$i][] = format_currency($repurchase_arr["$j"]["amount"]);
            $excel_array[$i][] = lang($repurchase_arr["$j"]["payment_method"]);
            $excel_array[$i][] = date("d M Y - h:i:s A", strtotime($repurchase_arr["$j"]["order_date"]));
            $total_amount += $repurchase_arr["$j"]["amount"];
        }
        $j = $i - 2;
        $excel_array[$i] = [];
        // if($user_id)
        //     $excel_array[$i][] = ' ';
        $excel_array[$i][] = ' ';
        $excel_array[$i][] = lang('total_amount');
        $excel_array[$i][] = format_currency($total_amount);
        $excel_array[$i][] = ' ';
        $excel_array[$i][] = ' ';
        
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }

    public function getStairStepDetails($week_date1, $week_date2, $leader_id="") {
        $details = array();
        $start_date = $week_date1 . " 00:00:00";
        $to_date = $week_date2 . " 23:59:59";
        $this->db->select('*');
        $this->db->from('leg_amount');
        $this->db->where('date_of_submission >=', $start_date);
        $this->db->where('date_of_submission <=', $to_date);
        $this->db->where("amount_type", 'stair_step');

        if($leader_id){
            $this->db->where("user_id", $leader_id);
        }

        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $details["$i"]["user_name"] = $this->validation_model->IdToUserName($row['user_id']);
            $details["$i"]["full_name"] = $this->validation_model->getUserFullName($row['user_id']);
 
            $details["$i"]["date_of_submission"] = $row['date_of_submission'];
            $details["$i"]["amount"] = $row['amount_payable'];
            $details["$i"]["paid_step"] = $row['user_level'];
            $details["$i"]["personal_volume"] = $row['pair_value'];
            $i++;
        }

        $count = count($details);
        $excel_array[1] = array(lang('user_name'), lang('full_name'), lang('date_submission'), lang('paid_step'), lang('personal_volume'), lang('amount'));
        for ($i = 2; $i <= $count + 1; $i++) {
            $excel_array[$i][0] = $details[$i - 2]["user_name"];
            $excel_array[$i][1] = $details[$i - 2]["full_name"];
            $excel_array[$i][2] = $details[$i - 2]["date_of_submission"];
            $excel_array[$i][3] = $details[$i - 2]["paid_step"];
            $excel_array[$i][4] = $details[$i - 2]["personal_volume"]; 
            $excel_array[$i][5] = $details[$i - 2]["amount"];
        }
        $excel_array = $this->replaceNullFromArray($excel_array);

        return $excel_array;
    }

    public function getOverRideDetails($week_date1, $week_date2, $leader_id="") {
        $details = array();
        $start_date = $week_date1 . " 00:00:00";
        $to_date = $week_date2 . " 23:59:59";
        $this->db->select('*');
        $this->db->from('leg_amount');
        $this->db->where('date_of_submission >=', $start_date);
        $this->db->where('date_of_submission <=', $to_date);
        $this->db->where("amount_type", 'override_bonus');

        if($leader_id){
            $this->db->where("user_id", $leader_id);
        }

        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $details["$i"]["user_name"] = $this->validation_model->IdToUserName($row['user_id']);
            $details["$i"]["full_name"] = $this->validation_model->getUserFullName($row['user_id']);
 
            $details["$i"]["date_of_submission"] = $row['date_of_submission'];
            $details["$i"]["amount"] = $row['amount_payable'];
            $details["$i"]["paid_step"] = $row['user_level'];
            $details["$i"]["personal_volume"] = $row['pair_value'];
            $i++;
        }
        $count = count($details);
        $excel_array[1] = array(lang('user_name'), lang('full_name'), lang('date_submission'), lang('paid_step'), lang('personal_volume'), lang('amount'));
        for ($i = 2; $i <= $count + 1; $i++) {
            $excel_array[$i][0] = $details[$i - 2]["user_name"];
            $excel_array[$i][1] = $details[$i - 2]["full_name"];
            $excel_array[$i][2] = $details[$i - 2]["date_of_submission"];
            $excel_array[$i][3] = $details[$i - 2]["paid_step"];
            $excel_array[$i][4] = $details[$i - 2]["personal_volume"]; 
            $excel_array[$i][5] = $details[$i - 2]["amount"];
        }
        $excel_array = $this->replaceNullFromArray($excel_array);

        return $excel_array;
    }

    //Added
    public function getReleasedPayoutReport($from_date= '', $to_date= '') {
        $released_payout_arr = $this->report_model->getReleasedPayoutDetails($from_date,$to_date);
        $count = count($released_payout_arr);        
        $excel_array = array();
        $payoutFeeDisplay = $this->report_model->checkPayoutFeeDisplay();
        $excel_array[1] = [];
        $excel_array[1][] = lang('invoice_no');
        if($this->LOG_USER_TYPE!="user")
            $excel_array[1][] = lang('member_name');
        $excel_array[1][] = lang('total_account');
        if($payoutFeeDisplay == "yes")
            $excel_array[1][] = lang('payout_fee');
        $excel_array[1][] = lang('date');
        $excel_array[1][] = lang('status');
        $totalPayout = 0;
        $totalPayoutFee = 0;
        for ($i = 2; $i <= $count + 1; $i++) {
            $j = $i - 2;
            $excel_array[$i] = [];
            $excel_array[$i][] = "PR000" . $released_payout_arr["detail$j"]["paid_id"];
            if($this->LOG_USER_TYPE!="user") {
                if($released_payout_arr["detail$j"]["delete_status"] == "active") {
                    $excel_array[$i][] = $released_payout_arr["detail$j"]["full_name"]."(".$released_payout_arr["detail$j"]["paid_user_name"].")";
                } else {
                    $excel_array[$i][] = $released_payout_arr["detail$j"]["paid_user_name"];
                }
            }
            $excel_array[$i][] = format_currency($released_payout_arr["detail$j"]["paid_amount"]);
            if($payoutFeeDisplay == "yes")
                $excel_array[$i][] = format_currency($released_payout_arr["detail$j"]["payout_fee"]);
            $excel_array[$i][] = date("d M Y - h:i:s A", strtotime($released_payout_arr["detail$j"]["paid_date"]));  
            $excel_array[$i][] = ($released_payout_arr["detail$j"]["paid_status"] == 'yes')?lang('paid'):lang('not_paid');
            $totalPayout += $released_payout_arr["detail$j"]["paid_amount"];
            $totalPayoutFee += $released_payout_arr["detail$j"]["payout_fee"];
        }
        $excel_array[$i] = [];
        if($this->LOG_USER_TYPE!="user")
            $excel_array[$i][] = " ";
        $excel_array[$i][] = lang("total_amount");
        $excel_array[$i][] = format_currency($totalPayout);
        if($payoutFeeDisplay == "yes")
            $excel_array[$i][] = format_currency($totalPayoutFee);
        $excel_array[$i][] = " ";
        $excel_array[$i][] = " ";
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }
    
    public function getPendingPayoutReport($from_date= '', $to_date= '') {
        $released_payout_arr = $this->report_model->getPayoutPendingDetails($from_date,$to_date); 
        $count = count($released_payout_arr);
        $payoutFeeDisplay = $this->report_model->checkPayoutFeeDisplay();
        // dd($released_payout_arr);
        $excel_array[1] = [];
        if($this->LOG_USER_TYPE!="user")
            $excel_array[1][] = lang('member_name');
        $excel_array[1][] = lang('total_account');
        if($payoutFeeDisplay == "yes")
            $excel_array[1][] = lang('payout_fee');
        $excel_array[1][] = lang('date');

        $totalPayout = 0;
        $totalPayoutFee = 0;
        for ($i = 2; $i <= $count + 1; $i++) {
            $j = $i - 2;
            if($this->LOG_USER_TYPE!="user") {
                if($released_payout_arr["$j"]["delete_status"] == "active") {
                    $excel_array[$i][] = $released_payout_arr["$j"]["full_name"]."(".$released_payout_arr["$j"]["paid_user_id"].")";
                } else {
                    $excel_array[$i][] = $released_payout_arr["$j"]["paid_user_id"];
                }
            }
            $excel_array[$i][] = format_currency($released_payout_arr["$j"]["paid_amount"]);
            if($payoutFeeDisplay == "yes")
                $excel_array[$i][] = format_currency($released_payout_arr["$j"]["payout_fee"]);
            $excel_array[$i][] = date("d M Y - h:i:s A", strtotime($released_payout_arr["$j"]["paid_date"]));
            $totalPayout += $released_payout_arr["$j"]["paid_amount"];
            $totalPayoutFee += $released_payout_arr["$j"]["payout_fee"] ?? 0;
        }
        $excel_array[$i] = [];
        if($this->LOG_USER_TYPE!="user")
            $excel_array[$i][] = lang("total_amount");
        $excel_array[$i][] = format_currency($totalPayout);
        if($payoutFeeDisplay == "yes")
            $excel_array[$i][] = format_currency($totalPayoutFee);
        if($this->LOG_USER_TYPE!="user") {
            $excel_array[$i][] = " ";
        } else {
            $excel_array[$i][] = lang("total_amount");
        }
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }
    
    public function getEpinTransferDetails($from_date, $to_date, $user_id="", $to_user_id = "") {
        $total_amount = 0;
        $transfer_arr = $this->report_model->getEpinTransferDetails($from_date, $to_date, $user_id,$to_user_id);
        $count = count($transfer_arr); 
        $excel_array[1] = array( lang('from_user'), lang('to_user'), lang('epin'), lang('transfer_date'));
        for ($i = 2; $i <= $count + 1; $i++) {
            $j = $i - 2;
            if($transfer_arr["$j"]["from_user_delete_status"] == "active") {
                $excel_array[$i][0] = $transfer_arr["$j"]["from_full_name"]."(".$transfer_arr["$j"]["from_user_name"].")";
            } else {
                $excel_array[$i][0] = $transfer_arr["$j"]["from_user_name"];
            }

            if($transfer_arr["$j"]["to_user_delete_status"] == "active") {
                $excel_array[$i][1] = $transfer_arr["$j"]["to_full_name"]."(".$transfer_arr["$j"]["to_user_name"].")";
            } else {
                $excel_array[$i][1] = $transfer_arr["$j"]["to_user_name"];
            }
            $excel_array[$i][2] = $transfer_arr["$j"]["epin"];
            $excel_array[$i][3] = date("d M Y - h:i:s A", strtotime($transfer_arr["$j"]["transfer_date"]));
        }
        
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }
    
    public function getEpinTransferDetailsForUser($from_date, $to_date, $user_id="") {
        $total_amount = 0;
        $transfer_arr = $this->report_model->getEpinTransferDetailsForUser($from_date, $to_date, $user_id);
        $count = count($transfer_arr); 
        $excel_array[1] = array( lang('member_name'), lang('epin'), lang('transfer_date'),lang('send')."/". lang('received'));
        for ($i = 2; $i <= $count + 1; $i++) {
            $j = $i - 2;
            $excel_array[$i][0] = $transfer_arr["$j"]["user_full_name"]."(".$transfer_arr["$j"]["user_name"].")";
            $excel_array[$i][1] = $transfer_arr["$j"]["epin"];
            $excel_array[$i][2] = date("d M Y - h:i:s A", strtotime($transfer_arr["$j"]["transfer_date"]));
            $excel_array[$i][3] = $transfer_arr["$j"]["type"];
        }
        
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }
     
    function getConfigChangesReport($from_date,$to_date) {
        $i = 0;
        $detail = array();
        $config_details = $this->report_model->getConfigChanges($from_date,$to_date);
        $count = count($config_details);
     
        $excel_array[1] = array(lang('sl_no'), lang('updated_by'), lang('activity'), lang('description'), lang('date'), lang('ip'));
        for ($i = 2; $i <= $count + 1; $i++) {
      
            $excel_array[$i][0] = $i - 1;
            $excel_array[$i][1] = $config_details[$i - 2]["user_name"];
            $excel_array[$i][2] = $config_details[$i - 2]["activity"];
            $excel_array[$i][3] = $config_details[$i - 2]["desc"];
            $excel_array[$i][4] = $config_details[$i - 2]["date"];
            $excel_array[$i][5] = $config_details[$i - 2]["ip"];
            
            }
            
       $excel_array = $this->replaceNullFromArray($excel_array);
       return $excel_array;
    }
    
    public function getroiReport($from_date, $to_date, $user_id="") {
        $total_amount = 0;
        $roi_arr = $this->report_model->getroiDetails($from_date, $to_date, $user_id);

        $count = count($roi_arr); 
        $excel_array[1] = array(lang('username'), lang('package'), lang('date_of_submission'), lang('total_amount'));
        for ($i = 3; $i <= $count + 2; $i++) {
            $j = $i - 2;
            $excel_array[$i][0] = $roi_arr["det$j"]["from_id"];
            $excel_array[$i][1] = $roi_arr["det$j"]["package"];
            $date_of_submission = $roi_arr["det$j"]["date_of_submission"];
            $excel_array[$i][2] = date('Y/m/d', strtotime($date_of_submission));
            $excel_array[$i][3] = $roi_arr["det$j"]["amount_payable"];
            $total_amount += $roi_arr["det$j"]["amount_payable"];
        }
        $j = $i - 2;
        $excel_array[$i][0] = ' ';
        $excel_array[$i][1] = ' ';
        $excel_array[$i][2] = 'Total';
        $excel_array[$i][3] = $total_amount;
        
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }
    public function getPersonalDta($user_name) {
        $total_amount = 0;
        $this->load->model('activity_history_model');
        $gdpr_arr = $this->report_model->getGdprDetails($user_name);
        $activity_details = $this->activity_history_model->getActivityHistory('','','','',$user_name);
        $excel_array[1] = array(lang('about'));
        $i = 3;
        foreach ($gdpr_arr['about'] as $key => $value) {
            $excel_array[$i][0] = lang($key);
            $excel_array[$i][1] = $value;
            $i++;
        }
        $excel_array[$i++] = array();
        $count = count($gdpr_arr['commission']);
        if($count){
            $excel_array[$i++] = array(lang('commission_details'));
            $excel_array[$i++] = array(lang('amount_type'), lang('total_amount'));
            for ($k = 1,$j=0; $k <= $count; $k++,$j++) {
               /*if($gdpr_arr['commission'][$j]['amount_type'] == "board_commission"){
                    if ($this->MODULE_STATUS['table_status'] == 'yes' && $this->MODULE_STATUS['mlm_plan'] == 'Board')
                        $gdpr_arr['commission'][$j]['amount_type'] = 'table_commission';
                        
               }*/
               $excel_array[$i][0] = lang($gdpr_arr['commission'][$j]['amount_type']);
               $excel_array[$i][1] = round($gdpr_arr['commission'][$j]['total_amount']*$this->DEFAULT_CURRENCY_VALUE,8);
               $total_amount += $gdpr_arr['commission'][$j]['total_amount'];
               $i++;
            }
            $excel_array[$i][0] = lang('total');
            $excel_array[$i][1] = round($total_amount*$this->DEFAULT_CURRENCY_VALUE,8);
            $i++;
        }
        $excel_array[$i++] = array();
        $excel_array[$i++] = array(lang('activities'));
        $count = count($activity_details);
        $excel_array[$i++] = array(lang('date'), lang('ip_address'), lang('activity'));
        for ($k = 1,$j=0; $k <= $count; $k++,$j++) {
           $excel_array[$i][0] = date('Y-m-d', strtotime($activity_details[$j]["date"]));
           $excel_array[$i][1] = $activity_details[$j]["ip"];
           $excel_array[$i][2] = $activity_details[$j]["activity"];
           $i++;
        }
        return $excel_array;
    }

    public function getPackageUpgradeReport($user_id,$product_id)
    {

         $i = 0;
        $detail = array();
        $config_details = $this->select_report_model->getPackageUpgradeDetails($user_id,$product_id);
        $count = count($config_details);
     
        $excel_array[1] = array();
        $excel_array[1][] = lang('member_name');
        $excel_array[1][] = lang('old_package');
        $excel_array[1][] = lang('upgraded_package');
        $excel_array[1][] = lang('amount');
        $excel_array[1][] = lang('payment_method');
        $excel_array[1][] = lang('upgraded_date');
        
        $total_amount = 0;
        for ($i = 2; $i <= $count + 1; $i++) {
            if($config_details[$i - 2]["delete_status"] == "active") {
                $excel_array[$i][] = $config_details[$i - 2]["full_name"] ."(".$config_details[$i - 2]["user_name"].")";
            } else {
                $excel_array[$i][] = $config_details[$i - 2]["user_name"];
            }
            $excel_array[$i][] = $config_details[$i - 2]["current_package"];
            $excel_array[$i][] = $config_details[$i - 2]["new_package"];
            $excel_array[$i][] = format_currency($config_details[$i - 2]["payment_amount"]);
            if($config_details[$i - 2]["payment_type"] == 'free_upgrade' &&  $config_details[$i - 2]["payment_amount"]== 0 ) {
               $excel_array[$i][]=lang('manualy_by_admin');
            }
            elseif($config_details[$i - 2]["payment_type"] == 'free_upgrade' &&  $config_details[$i - 2]["payment_amount"]!=0) {
                $excel_array[$i][]=lang('free_upgrade');
            }
            else{
            $excel_array[$i][] = lang($config_details[$i - 2]["payment_type"]);
            }
            $excel_array[$i][] = date("d M Y - h:i:s A", strtotime($config_details[$i - 2]["date_added"]));
            $total_amount += $config_details[$i - 2]["payment_amount"];
        }
        
        $excel_array[$i][] = " ";
        $excel_array[$i][] = " ";
        $excel_array[$i][] = lang('total');
        $excel_array[$i][] = format_currency($total_amount);
        $excel_array[$i][] = " ";
        $excel_array[$i][] = " ";
            
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }

    public function formRankPerfomanceArray($user_id, $user_name)
    {
        $i = 0;
        $detail = array();
        $this->load->model('rank_model');
        $this->lang->load('report');
        $user_details = $this->validation_model->getAllUserDetails($user_id);
        $full_name = $user_details['user_detail_name']. ' ' . $user_details['user_detail_second_name'];
        $rank_achievement = $this->rank_model->getRankCriteria($user_id);
        $data = [
            'criteria'     => [
                'referal_count'          => $rank_achievement['criteria']['referal_count'],
                'joinee_package'         => $rank_achievement['criteria']['joinee_package'],
                'personal_pv'            => $rank_achievement['criteria']['personal_pv'],
                'group_pv'               => $rank_achievement['criteria']['group_pv'],
                'downline_count'         => $rank_achievement['criteria']['downline_count'],
                'downline_package_count' => $rank_achievement['criteria']['downline_package_count'],
                'downline_rank'          => $rank_achievement['criteria']['downline_rank'],
            ],
            'current_rank' => [
                'rank_id'                => isset($rank_achievement['current_rank']['rank_id']) ? $rank_achievement['current_rank']['rank_id'] : lang('na'),
                'rank_name'              => isset($rank_achievement['current_rank']['rank_name']) ? $rank_achievement['current_rank']['rank_name']: lang('na'),
                'referal_count'          => isset($rank_achievement['current_rank']['referal_count']) ? $rank_achievement['current_rank']['referal_count'] : lang('na'),
                'personal_pv'            => isset($rank_achievement['current_rank']['personal_pv']) ? $rank_achievement['current_rank']['personal_pv'] : 0,
                'group_pv'               => isset($rank_achievement['current_rank']['group_pv']) ? $rank_achievement['current_rank']['group_pv'] : 0,
                'downline_count'         => isset($rank_achievement['current_rank']['downline_count']) ? $rank_achievement['current_rank']['downline_count'] : 0,
                'downline_package_count' => isset($rank_achievement['current_rank']['downline_package_count']) ? $rank_achievement['current_rank']['downline_package_count'] : 0,
                'package_name'           => isset($rank_achievement['current_rank']['package_name']) ? $rank_achievement['current_rank']['package_name'] : lang('na')
            ],
            'next_rank' => [
                'rank_name'              => isset($rank_achievement['next_rank']['rank_name']) ? $rank_achievement['next_rank']['rank_name']: lang('na'),
                'referal_count'          => isset($rank_achievement['next_rank']['referal_count']) ? $rank_achievement['next_rank']['referal_count']: 0,
                'personal_pv'            => isset($rank_achievement['next_rank']['personal_pv']) ? $rank_achievement['next_rank']['personal_pv'] : 0,
                'group_pv'               => isset($rank_achievement['next_rank']['group_pv']) ? $rank_achievement['next_rank']['group_pv'] : 0,
                'downline_count'         => isset($rank_achievement['next_rank']['downline_count']) ? $rank_achievement['next_rank']['downline_count'] : 0,
                'referal_count'          => isset($rank_achievement['next_rank']['referal_count']) ? $rank_achievement['next_rank']['referal_count'] : 0
            ]
        ];
        
        $excel_array = [];
        $excel_array[1] = [
            lang('member_name'),
            $full_name . " " . "(" . $user_name . ")"
        ];
        $excel_array[] = [
            lang('current_rank'),
            $data["current_rank"]["rank_name"]
        ];
        
        $excel_array[] = [
            lang('next_rank'),
            $data["next_rank"]["rank_name"]
        ];
        
        $excel_array[] = [
            lang('current_referal_count'),
            $data["current_rank"]["referal_count"]
        ];

        $excel_array[] = [
            lang('referral_count_for')." ". $data["next_rank"]["rank_name"],
            $data["next_rank"]["referal_count"]
        ];

        $excel_array[] = [
            lang('needed_referral_count'),
            $data["criteria"]["referal_count"]
        ];
        $excel_array[] = [
            lang('current_personal_pv'),
            $data["current_rank"]["personal_pv"]
        ];
        $excel_array[] = [
            lang('personal_pv_for')." ".$data["next_rank"]["rank_name"],
            $data["next_rank"]["personal_pv"]
        ];
        $excel_array[] = [
            lang('needed_personal_pv'),
            $data["next_rank"]["personal_pv"] ? (($data["next_rank"]["personal_pv"]-$data["current_rank"]["personal_pv"])>0 ? ($data["next_rank"]["personal_pv"]-$data["current_rank"]["personal_pv"]) :0) :0
        ];
        $excel_array[] = [
            lang('current_group_pv'),
            $data["current_rank"]["group_pv"]
        ];
        $excel_array[] = [
            lang('gpv_for')." ".$data["next_rank"]["rank_name"],
            $data["next_rank"]["group_pv"]
        ];
        $excel_array[] = [
            lang('needed_group_pv'),
            $data["next_rank"]["group_pv"] ?
            (($data["next_rank"]["group_pv"]-$data["current_rank"]["group_pv"])>0 ? ($data["next_rank"]["group_pv"]-$data["current_rank"]["group_pv"]) :0) :0
        ];
        $excel_array[] = [
            lang('current_downline_count'),
            $data["current_rank"]["downline_count"]
        ];
        $excel_array[] = [
            lang('downline_count_for')." ".$data["next_rank"]["rank_name"],
            $data["next_rank"]["downline_count"]
        ];
        $excel_array[] = [
            lang('needed_downline_count'),
            $data["next_rank"]["downline_count"] ?
            (($data["next_rank"]["downline_count"]-$data["current_rank"]["downline_count"])>0 ? ($data["next_rank"]["downline_count"]-$data["current_rank"]["downline_count"]) :0) :0
        ];

        // if($rank_achievement["next_rank"] && count($rank_achievement["next_rank"])) {
        //     $excel_array[] = [
        //         lang('referral_count_for') . "  " . $rank_achievement["next_rank"]["rank_name"],
        //         $rank_achievement["next_rank"]["referal_count"] * 1
        //     ];
        //     $excel_array[] = [
        //         lang('needed_referral_count'),
        //         $rank_achievement["next_rank"]["referal_count"] - $rank_achievement["current_rank"]["referal_count"]
        //     ];
        // }
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }
    
    function getSubscriptionReportArray($userId) {
        $details = $this->report_model->getSubscriptionReport($userId);
        $i = 0;
        $count = count($details);
     
        $excel_array[1] = array();
        $excel_array[1][] = lang('member_name');
        if($this->MODULE_STATUS["product_status"] == "yes")
            $excel_array[1][] = lang('package');
        $excel_array[1][] = lang('subscription_amount');
        $excel_array[1][] = lang('payment_method');
        $excel_array[1][] = lang('subscription_date');
        
        $total_amount = 0;
        for ($i = 2; $i <= $count + 1; $i++) {
      
            $excel_array[$i] = [];
            $excel_array[$i][] = $details[$i - 2]["user_full_name"] ."(".$details[$i - 2]["username"].")";
            if($this->MODULE_STATUS["product_status"] == "yes")
                $excel_array[$i][] = $details[$i - 2]["package_name"] . "(".format_currency($details[$i - 2]["package_amount"]).")";
            $excel_array[$i][] = format_currency($details[$i - 2]["total_amount"]);
            $excel_array[$i][] = lang($details[$i - 2]["payment_type_used"]);
            $excel_array[$i][] = date("d M Y - h:i:s A", strtotime($details[$i - 2]["date_submitted"]));
            $total_amount += $details[$i - 2]["total_amount"];
        }
        
        if($this->MODULE_STATUS["product_status"] == "yes")
            $excel_array[$i][] = " ";
        $excel_array[$i][] = lang('total');
        $excel_array[$i][] = format_currency($total_amount);
        $excel_array[$i][] = " ";
        $excel_array[$i][] = " ";
            
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }


    public function getAgentCreditReportArray($userId='')
    {
        $details = $this->report_model->getWalletReport('agent',$userId);
        $i = 0;
        $count = count($details);
       

        $excel_array[1] = array();
        $excel_array[1][] = lang('full_name');
        $excel_array[1][] = lang('from_name');
        $excel_array[1][] = lang('agent_username');
        $excel_array[1][] = lang('country');
        $excel_array[1][] = lang('wallet_amount');
        $excel_array[1][] = lang('date_of_joining');
        
        $total_amount = 0;
        for ($i = 2; $i <= $count + 1; $i++) {
      
            $excel_array[$i] = [];
            $excel_array[$i][] = $details[$i - 2]["full_name"];
            $excel_array[$i][] = $details[$i - 2]["from_name"];
            $excel_array[$i][] = $details[$i - 2]["agent_username"];
            $excel_array[$i][] = $details[$i - 2]["country"];
            $excel_array[$i][] = format_currency($details[$i - 2]["wallet_amount"]);
            $excel_array[$i][] = date("d M Y - h:i:s A", strtotime($details[$i - 2]["date_added"]));
            $total_amount += $details[$i - 2]["wallet_amount"];
        }
        
        if($this->MODULE_STATUS["product_status"] == "yes")
            $excel_array[$i][] = " ";
        $excel_array[$i][] = " ";
        $excel_array[$i][] = " ";
        $excel_array[$i][] = lang('total');
        $excel_array[$i][] = format_currency($total_amount);
        $excel_array[$i][] = " ";
        $excel_array[$i][] = " ";
            
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }
    public function getVoucherReportArray($user_id){
        $details = $this->report_model->getVoucherReport($user_id,0,0);
        $i = 0;
        $count = count($details);
     
        $excel_array[1] = array();
        $excel_array[1][] = lang('member_name');
        $excel_array[1][] = lang('Voucher Amount');
        $excel_array[1][] = lang('rank');
        $excel_array[1][] = lang('date');
        
        $total_amount = 0;
        for ($i = 2; $i <= $count + 1; $i++) {
      
            $excel_array[$i] = [];
            $excel_array[$i][] = $details[$i - 2]["user_name"];
            $excel_array[$i][] = format_currency($details[$i - 2]["voucher"]);
            $excel_array[$i][] = lang($details[$i - 2]["rank_name"]);
            $excel_array[$i][] = date("d M Y - h:i:s A", strtotime($details[$i - 2]["date"]));
            $total_amount += $details[$i - 2]["voucher"];
        }
        
        $excel_array[$i][] = lang('total');
        $excel_array[$i][] = format_currency($total_amount);
        $excel_array[$i][] = " ";
        $excel_array[$i][] = " ";
            
        $excel_array = $this->replaceNullFromArray($excel_array);
        return $excel_array;
    }



function getEwalletTransactionsReport() {
         $i = 0;
        $transactions = array();
         $transactions = $this->ewallet_model->getEwalletTransactionsnew(array(), array(), array(), "", "");
         // dd($transactions);
        $count = count($transactions);

         foreach($transactions as $tr) {
            $data[] = [
                'full_name' => $tr['full_name'],
                'user_name' => $tr['user_name'],
                'amount_type' => lang($tr['amount_type']),
                'type' => $tr['type'],
                'amount' => format_currency($tr['amount']),
                'date_added' => date("F j, Y, g:i a",strtotime($tr['date_added'])),
            ];
        }
      // dd($data);

        $excel_array[1] = array();
        $excel_array[1][] = lang('member_name');
        $excel_array[1][] = lang('category');
        $excel_array[1][] = lang('amount');
        $excel_array[1][] = lang('Type');
        $excel_array[1][] = lang('transaction_date');
        for ($i = 2; $i <= $count + 1; $i++) {
      
           
            $excel_array[$i][1] = $data[$i - 2]["user_name"];
            $excel_array[$i][2] = $data[$i - 2]["amount_type"];
            $excel_array[$i][3] = $data[$i - 2]["amount"];
            $excel_array[$i][3] = $data[$i - 2]["type"];
            $excel_array[$i][4] = $data[$i - 2]["date_added"];
       
            
            }
            
       $excel_array = $this->replaceNullFromArray($excel_array);
       return $excel_array;
    }
    function EwalletBalanceReport() {
         $i = 0;
        $balance = array();
         $balance = $this->ewallet_model->getEwalletBalancenew(array());
    
        $count = count($balance);
        $excel_array[1] = array();
        $excel_array[1][] = lang('member_name');
        $excel_array[1][] = lang('ewallet_balance');
        for ($i = 2; $i <= $count + 1; $i++) {
      
           $excel_array[$i][] = $balance[$i - 2]["full_name"] ."(".$balance[$i - 2]["user_name"].")";
            // $excel_array[$i][1] = $balance[$i - 2]["user_name"];
            $excel_array[$i][2] = $balance[$i - 2]["amount"];
       
            
            }
            
       $excel_array = $this->replaceNullFromArray($excel_array);
       return $excel_array;
    }

    function getEwalletStatementReport() {
         $i = 0;
        $statement = array();
            $user_name = $this->session->userdata("inf_ewallet_user_name");
            $user_id = $this->validation_model->usernameToId($user_name);
            //$count = $this->ewallet_model->getEwalletHistoryCount($user_id);

        $ewallet_statement = $this->ewallet_model->ewallet_history_new($user_id
        );


        $debit = $credit = 0;
        $data = [];
        //$balance = $this->ewallet_model->getPreviousEwalletBalancenew($user_id);
        $balance=0;
        $count = count($ewallet_statement);

          $from_user_amount_types = [
            'referral',
            'level_commission',
            'repurchase_level_commission',
            'upgrade_level_commission',
            'xup_commission',
            'xup_repurchase_level_commission',
            'xup_upgrade_level_commission',
            'sales_commission',
        ];
        $i=0;
        foreach($ewallet_statement as $item) {
            $description = "";
            if ($item['type'] == 'debit' && $item['amount_type'] != 'payout_release') {
                $balance = $balance - $item['amount'] - $item['transaction_fee'];
                $debit = $debit + $item['amount'];
            }
            if ($item['type'] == 'credit') {
                $balance = $balance + $item['amount'] - $item['purchase_wallet'];
                $credit = $credit + $item['amount'] - $item['purchase_wallet'];
            }

            if ($item['ewallet_type'] == "fund_transfer") {
                if ($item['amount_type'] == "user_credit") {
                    $description = lang('transfer_from') . ' ' . $item['from_user'];
                } elseif ($item['amount_type'] == "user_debit") {
                    $description = lang('fund_transfer_to') . ' ' . $item['from_user'];
                } elseif ($item['amount_type'] == "admin_credit") {
                    $description = lang('credited_by') . ' ' . $item['from_user'];
                } elseif ($item['amount_type'] == "admin_debit") {
                    $description = lang('deducted_by') . ' ' . $item['from_user'];
                }
            } elseif ($item['ewallet_type'] == "commission") {
                if ($item['amount_type'] == "donation") {
                    if ($item['type'] == "debit") {
                        $description = lang('donation_debit') . ' ' . $item['from_user'];
                    } else {
                        $description = lang('donation_credit') . ' ' . $item['from_user'];
                    }
                } elseif ($item['amount_type'] == 'board_commission' && $MODULE_STATUS['table_status'] == 'yes') {
                    $description = lang('table_commission');
                } else {
                    if (in_array($item['amount_type'], $from_user_amount_types)) {
                        $description = lang($item['amount_type']) . ' ' . lang('from') . ' ' . $item['from_user'];
                    } else {
                        $description = lang($item['amount_type']);
                    }
                }
            } elseif ($item['ewallet_type'] == "ewallet_payment") {
                if ($item['amount_type'] == "registration") {
                    $description = lang('deducted_for_registration_of') . ' ' . $item['from_user'];
                } elseif ($item['amount_type'] == "repurchase") {
                    $description = lang('deducted_for_repurchase_by') . ' ' . $item['from_user'];
                } elseif ($item['amount_type'] == "package_validity") {
                    $description = lang('deducted_for_membership_renewal_of') . ' '.  $item['from_user'];
                } elseif ($item['amount_type'] == "upgrade") {
                    $description = lang('deducted_for_upgrade_of') .' '. $item['from_user'];
                }
            } elseif ($item['ewallet_type'] == "payout") {
                if ($item['amount_type'] == "payout_request") {
                    $description = lang('deducted_for_payout_request');
                } elseif ($item['amount_type'] == "payout_release") {
                    $description = lang('payout_released_for_request');
                } elseif ($item['amount_type'] == "payout_delete") {
                    $description = lang('credited_for_payout_request_delete');
                } elseif ($item['amount_type'] == "payout_release_manual") {
                    $description = lang('payout_released_by_manual');
                } elseif ($item['amount_type'] == "withdrawal_cancel") {
                    $description = lang('credited_for_waiting_withdrawal_cancel');
                }
            } elseif ($item['ewallet_type'] == "pin_purchase") {
                if ($item['amount_type'] == "pin_purchase") {
                    $description = lang('deducted_for_pin_purchase');
                } elseif ($item['amount_type'] == "pin_purchase_refund") {
                    $description = lang('credited_for_pin_purchase_refund');
                } elseif ($item['amount_type'] == "pin_purchase_delete") {
                    $description = lang('credited_for_pin_purchase_delete');
                }
            } elseif ($item['ewallet_type'] == "package_purchase") {
                if ($item['amount_type'] == "purchase_donation") {
                    $description = lang('purchase_donation') . ' ' . lang('from') . ' ' . $item['from_user'];
                }
            }

            if ($item['pending_id']) {
                $description .= '<span>' .lang('pending') . '</span>';
            }
            if (in_array($item['ewallet_type'], array('fund_transfer', 'payout')) && $item['transaction_fee'] > 0 && $item['type'] == 'debit') {
                $description .= '('.lang('transaction_fee').')';
            }
            $ewallet_statement[$i]['description']=$description;
            $ewallet_statement[$i]['balance']=$balance;
            
            $i++;
        }

      // dd($data);
        $excel_array[1] = array();
        $excel_array[1][] = lang('description');
        $excel_array[1][] = lang('amount');
         $excel_array[1][] = lang('Type');
        $excel_array[1][] = lang('balance');
        $excel_array[1][] = lang('transaction_date');
        for ($i = 2; $i <= $count + 1; $i++) {
      
           
            $excel_array[$i][] = $ewallet_statement[$i - 2]["description"];
            $excel_array[$i][] = $ewallet_statement[$i - 2]["amount"];
            $excel_array[$i][] = $ewallet_statement[$i - 2]["type"];
            $excel_array[$i][] = $ewallet_statement[$i - 2]["balance"];
            $excel_array[$i][] = $ewallet_statement[$i - 2]["date_added"];
       
            
            }
            
       $excel_array = $this->replaceNullFromArray($excel_array);
       return $excel_array;
    }  

   function  getEwalletEarningsReport() {

    $i = 0;
        $user_earnigs = array();
        $user_name = $this->session->userdata('inf_ewallete_earnings_user_name');
        $user_id = $this->validation_model->usernameToId($user_name);
        $user_earnigs = $this->ewallet_model->getUserEarnigsnew($user_id, "all", "","");
        // dd($user_earnigs);
         
        $count = count($user_earnigs);
        $excel_array[1] = array();
        $excel_array[1][] = lang('category');
        $excel_array[1][] = lang('amount');
        $excel_array[1][] = lang('transaction_date');


         $data = [];
        foreach($user_earnigs as $item) {
            if($item['category'] == 'board_commission' && $MLM_PLAN == 'Board' && $MODULE_STATUS['table_status'] == 'yes') {
                $item['category'] = "table_commission";
            }

            if($item['category'] == 'level_commission' || $item['category'] == 'repurchase_level_commission' || $item['category'] =='upgrade_level_commission' || $item['category'] =='xup_commission' || $item['category'] =='xup_repurchase_level_commission' || $item['category'] =='xup_upgrade_level_commission' || $item['category'] =='matching_bonus' || $item['category'] =='matching_bonus_purchase' || $item['category'] == 'matching_bonus_upgrade' || $item['category'] =='sales_commission') {

                $item['category'] = lang($item['category']) . ' '. lang('received_from') . ' ' . $item['user_name'] . ' ' .lang('from_level') . ' '. $item['user_level'];
            } elseif($item['category'] == "referral") {
                $item['category'] = lang($item['category']). ' ' .lang('received_from') . ' ' . $item['user_name'];
            }

            $data[] = [
                'category'         => lang($item['category']),
                'amount'           => format_currency($item['amount']),
                'transaction_date' => date("F j, Y, g:i a",strtotime($item['transaction_date']))
            ];
        }
        

        for ($i = 2; $i <= $count + 1; $i++) {
      
           $excel_array[$i][] = $data[$i - 2]["category"];
            $excel_array[$i][1] = $data[$i - 2]["amount"];
            $excel_array[$i][2] = $data[$i - 2]["transaction_date"];
       
            
            }
            
       $excel_array = $this->replaceNullFromArray($excel_array);
       return $excel_array;

   }
  function getCustomWalletReport() {
    $i = 0;
   $transactions = array();
    $transactions = $this->member_model->getCwalletDetails('','',array(),'','');
   $count = count($transactions);

    foreach($transactions as $tr) {
       $data[] = [
           'full_name' => $tr['full_name'],
           'user_name' => $tr['user_name'],
           'amount_type' => lang($tr['amount_type']),
           'type' => $tr['type'],
           'amount' => format_currency($tr['amount']),
           'date_added' => date("F j, Y, g:i a",strtotime($tr['date_added'])),
       ];
   }
 // dd($data);

   $excel_array[1] = array();
   $excel_array[1][] = lang('member_name');
   $excel_array[1][] = lang('category');
   $excel_array[1][] = lang('amount');
   $excel_array[1][] = lang('Type');
   $excel_array[1][] = lang('transaction_date');
   for ($i = 2; $i <= $count + 1; $i++) {
 
      
       $excel_array[$i][1] = $data[$i - 2]["user_name"];
       $excel_array[$i][2] = $data[$i - 2]["amount_type"];
       $excel_array[$i][3] = $data[$i - 2]["amount"];
       $excel_array[$i][3] = $data[$i - 2]["type"];
       $excel_array[$i][4] = $data[$i - 2]["date_added"];
  
       
       }
       
  $excel_array = $this->replaceNullFromArray($excel_array);
  return $excel_array;
}
function getGroupPV($user_id = '', $date_range = '', $from_date = '', $to_date = '') {
    $i = 0;
   $group_pv_details = array();
    $group_pv_details = $this->report_model->getGroup_pv_details($date_range,$user_id,$from_date,$to_date);
   $count = count($group_pv_details);

    foreach($group_pv_details as $gpv) {
       $data[] = [
           'user_name' => $gpv['user_name'],
           'group_pv' => $gpv['group_pv'],
           'from_user_name' => $gpv['from_user_name'],
           'date' => date("F j, Y, g:i a",strtotime($gpv['date'])),
           'date_range' => $gpv['date_range'],
       ];
   }
 // dd($data);

   $excel_array[1] = array();
   $excel_array[1][] = lang('user_name');
   $excel_array[1][] = lang('group_pv');
   $excel_array[1][] = lang('from_user_name');
   $excel_array[1][] = lang('date');
   for ($i = 2; $i <= $count + 1; $i++) { 
       $excel_array[$i][1] = $data[$i - 2]["user_name"];
       $excel_array[$i][2] = $data[$i - 2]["group_pv"];
       $excel_array[$i][3] = $data[$i - 2]["from_user_name"];
       if($date_range == 'all')
       $excel_array[$i][4] = $data[$i - 2]["date"];
       else
       $excel_array[$i][4] = $data[$i - 2]["date_range"];
       
       }
  $excel_array = $this->replaceNullFromArray($excel_array);
  return $excel_array;
}
}
