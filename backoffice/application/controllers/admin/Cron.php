<?php

require_once 'Inf_Controller.php';

class cron extends Inf_Controller {

    function __construct() {
        parent::__construct();
        $this->cron_model = new cron_model();
    }

    function generate_backup() {
        $cron_id = $this->cron_model->insertCronHistory('backup');
        $status = $this->cron_model->backupDatabase();
        if ($status) {
            $this->cron_model->updateCronHistory($cron_id, "finished");
        } else {
            $this->cron_model->updateCronHistory($cron_id, "failed");
        }
    }

    public function autoresponder_mail() {//sending mail to pending and following LCP users depend on date
        $cron_id = $this->cron_model->insertIntoCronHistory('autoresponder_mail');
        if ($this->MODULE_STATUS['autoresponder_status'] == "yes" && DEMO_STATUS == "no") {
            $autorespond = $this->cron_model->sentAutoresponderMail();
        } else if (DEMO_STATUS == "yes") {
            $all_table_prifix = $this->cron_model->getAllTablePrifix();
            for ($i = 0; $i <= count($all_table_prifix); $i++) {
                $this->session->set_userdata('inf_table_prefix', $all_table_prifix[$i]);
                $autorespond = $this->cron_model->sentAutoresponderMail();
            }
        } else {
            echo "error";
            die();
        }
        if ($autorespond) {
            $this->cron_model->updateCronHistory('success', $cron_id);
            echo "mails are Successfully Delivered";
        } else {

            $this->cron_model->updateCronHistory('failed', $cron_id);
            echo "Unable To Send Mail";
        }
    }

    function auto_cache_clear() {

        $cron_id = $this->cron_model->insertCronHistory('clear_cache');
        $status = $this->cron_model->clearCache();

        if ($status) {
            $this->cron_model->updateCronHistory($cron_id, "finished");
        } else {
            $this->cron_model->updateCronHistory($cron_id, "failed");
        }
    }

    function daily_investment() {

        $flag = TRUE;
        $date = $date1 = date('Y-m-d');
        $date = strtotime($date);

        $roi_configuration = $this->validation_model->getConfig(['roi_period,roi_days_skip']);
        $roi_period = $roi_configuration['roi_period'];
        $roi_days = explode(',', $roi_configuration['roi_days_skip']);

        if ($roi_period == "yearly") {
            $from_date = date('Y-m-d', strtotime('this year January 1st')) . " 00:00:00";
            $to_date = date('Y-m-d', strtotime('this year December 31st')) . " 23:59:59";
        }
        elseif ($roi_period == "monthly") {
            $from_date = date('Y-m-d', strtotime('first day of this month')) . " 00:00:00";
            $to_date = date('Y-m-d', strtotime('last day of this month')) . " 23:59:59";
           
        } elseif ($roi_period == "weekly") {
            $week_arr = $this->getWeekDateRange('sunday');
            $from_date = $week_arr["start"] . " 00:00:00";
            $to_date = $week_arr["end"] . " 23:59:59";
        } elseif ($roi_period == "daily") {
            $from_date = date("Y-m-d") . " 00:00:00";
            $to_date = date("Y-m-d") . " 23:59:59";
            if (in_array(date("D", $date),$roi_days)) {
                $flag = FALSE;
            }
        }

        $result = $this->cron_model->isCronCalculated($from_date, $to_date, $roi_period, 'daily_investment');

        if ($result && $flag) {
            $cron_id = $this->cron_model->insertCronHistory('daily_investment');
            $status = $this->cron_model->calculateDailyInvestment();
            if ($status) {
                $this->cron_model->updateCronHistory($cron_id, "finished");
            } else {
                $this->cron_model->updateCronHistory($cron_id, "failed");
            }
        }
        $msg = lang("Cron executed successfully");
        $this->redirect($msg, 'configuration/cron_status', TRUE);
    }

    public function getWeekDateRange($start_day)
    {
        $start = strtotime("last $start_day");
        $start = date('w', $start) == date('w') ? $start + 7 * 86400 : $start;

        $end = strtotime(date("Y-m-d", $start) . " +6 days");

        $this_week_sd = date("Y-m-d", $start);
        $this_week_ed = date("Y-m-d", $end);

        return [
            'start' => $this_week_sd,
            'end' => $this_week_ed
        ];
    }

    function pool_bonus() {
        $date = date('Y-m-d H:i:s');
        $cron_id = $this->cron_model->insertCronHistory('pool_bonus');
        $status = $this->cron_model->calculatePoolBonus();
        if ($status) {
            $this->cron_model->updateCronHistory($cron_id, "finished");
            echo "finished";
        } else {
            $this->cron_model->updateCronHistory($cron_id, "failed");
            echo "failed";
        }
    }

    function auto_ship() {
        if ($this->MODULE_STATUS['auto_ship_status'] == "yes") {
            $cron_id = $this->cron_model->insertCronHistory('auto_ship');
            $status = $this->cron_model->autoShipReactivation();
            if ($status) {
                $this->cron_model->updateCronHistory($cron_id, "finished");
                echo "finished";
            } else {
                $this->cron_model->updateCronHistory($cron_id, "failed");
                echo "failed";
            }
        } else {
            echo "Autoship module is disabled";
        }
    }

    function binary_commission() {

        $result = FALSE;
        $binary_config = $this->configuration_model->getBinaryBonusConfig();
        $calculation_period = $binary_config['calculation_period'];

        if ($calculation_period == "yearly") {
            $from_date = date('Y-m-d', strtotime('this year January 1st')) . " 00:00:00";
            $to_date = date('Y-m-d', strtotime('this year December 31st')) . " 23:59:59";
        }
        elseif ($calculation_period == "monthly") {
            $from_date = date('Y-m-d', strtotime('first day of this month')) . " 00:00:00";
            $to_date = date('Y-m-d', strtotime('last day of this month')) . " 23:59:59";
        } elseif ($calculation_period == "weekly") {
            $week_arr = $this->getWeekDateRange('sunday');
            $from_date = $week_arr["start"] . " 00:00:00";
            $to_date = $week_arr["end"] . " 23:59:59";
        } elseif ($calculation_period == "daily") {
            $from_date = date("Y-m-d") . " 00:00:00";
            $to_date = date("Y-m-d") . " 23:59:59";
        }

        if($calculation_period != "instant") { 
            $result = $this->cron_model->isCronCalculated($from_date, $to_date, $calculation_period, 'binary_commission');
        }

        if($result) { 
            $cron_id = $this->cron_model->insertCronHistory('binary_commission');
            $status =  $this->cron_model->calculateBinaryCommission();
            if ($status) {
                $this->cron_model->updateCronHistory($cron_id, "finished");
                echo "finished";
            } else {
                $this->cron_model->updateCronHistory($cron_id, "failed");
                echo "failed";
            }

        } else {
            echo "Cron Can't set";
        }
    }
    function rank_expiry() {

        $result = FALSE;
        $rank_configuration = $this->configuration_model->getRankConfiguration();
            $rank_period = $rank_configuration['rank_expiry'];

        if ($rank_period == "yearly") {
            $from_date = date('Y-m-d', strtotime('this year January 1st')) . " 00:00:00";
            $to_date = date('Y-m-d', strtotime('this year December 31st')) . " 23:59:59";
        }
        elseif ($rank_period == "monthly") {
            $from_date = date('Y-m-d', strtotime('first day of this month')) . " 00:00:00";
            $to_date = date('Y-m-d', strtotime('last day of this month')) . " 23:59:59";
        } elseif ($rank_period == "weekly") {
            $week_arr = $this->getWeekDateRange('sunday');
            $from_date = $week_arr["start"] . " 00:00:00";
            $to_date = $week_arr["end"] . " 23:59:59";
        } elseif ($rank_period == "daily") {
            $from_date = date("Y-m-d") . " 00:00:00";
            $to_date = date("Y-m-d") . " 23:59:59";
        }

        if($rank_period != "instant") { 
            $result = $this->cron_model->isCronCalculated($from_date, $to_date, $rank_period, 'rank_bonus_expiry');
        }

        if($result) { 
            $cron_id = $this->cron_model->insertCronHistory('rank_bonus_expiry');
            $status = $this->cron_model->calculateRank();
            if ($status) {
                $this->cron_model->updateCronHistory($cron_id, "finished");
                echo "finished";
            } else {
                $this->cron_model->updateCronHistory($cron_id, "failed");
                echo "failed";
            }

        } else {
            echo "Cron Can't set";
        }
    }

    public function DistributeAgentWallet($value='')
    {
        $cron_id = $this->cron_model->insertCronHistory('agentwallet_distribution');
        $this->cron_model->DistributeAgentWallet();
        $this->cron_model->updateCronHistory($cron_id, "finished");
        echo "finished";
    }
    
    /**
     * SET CRONJOB FOR THIS FUNCTION MONTHLY
     * Add pending pair amount to reserve wallet
     * 28-06-2021
     * Renshif E
     */
    public function add_amount_to_reserve_wallet() {
        $from_date = date('Y-m-d', strtotime('first day of this month')) . " 00:00:00";
        $to_date = date('Y-m-d', strtotime('last day of this month')) . " 23:59:59";

        $result = $this->cron_model->isCronCalculated($from_date, $to_date, 'monthly', 'reserve_amount');
        if ($result) {
            $cron_id = $this->cron_model->insertCronHistory('reserve_amount');
            $status = $this->cron_model->calculateReserveAndDistributionAmount();
            if ($status) {
                $this->cron_model->updateCronHistory($cron_id, "finished");
                echo "finished";
            } else {
                $this->cron_model->updateCronHistory($cron_id, "failed");
                echo "failed";
            }
        } else {
            echo "Cron Can't set";
        }
    }

    function monthly_profit_distribution(){
        $cron_id = $this->cron_model->insertCronHistory('monthly_profit_distribution');
        $status = $this->cron_model->distributeMonthlyProfit();
        if ($status) {
            $this->cron_model->updateCronHistory($cron_id, "finished");
            echo "finished";
        } else {
            $this->cron_model->updateCronHistory($cron_id, "failed");
            echo "failed";
        }
    }
    public function binary_pending_pair() {
        $from_date = date('Y-m-d 00:00:00');
        $to_date = date('Y-m-d 23:59:59');

        $result = $this->cron_model->isCronCalculated($from_date, $to_date, 'daily', 'pending_pair');
        if ($result) {
            $cron_id = $this->cron_model->insertCronHistory('pending_pair');
            $status = $this->cron_model->calculatePendingPair();
            if ($status) {
                $this->cron_model->updateCronHistory($cron_id, "finished");
                echo "finished";
            } else {
                $this->cron_model->updateCronHistory($cron_id, "failed");
                echo "failed";
            }
        } else {
            echo "Cron Can't set";
        }
    }
    
    function old_users_api(){
        $users = $this->cron_model->getUserswithoutURL();
        foreach($users as $u) {
            $username = $u['user_name'];
            $userid   = $u['id'];
            $url = "https://app.simply37.com/api/user/register?app_key=f9r%21G$2T?7k%25u3@M4x&username=$username&user_id=$userid";
            $data = array(array("app_key" => 'f9r!G$2T?7k%u3@M4x'),array('username' => $username), array('user_id' => $userid) );
            
            $postdata = json_encode($data);
            
            $ch = curl_init($url); 
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', "Postman-Token: 2cd8c863-7461-4c16-95dd-768e77b99572", "cache-control: no-cache"));
            $result = curl_exec($ch);
            
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $result = json_decode($result,TRUE);
            if($result['status']=='ok') {
                $url =  $result['data'];
            } else {
                $url =  '';
            }
            $this->cron_model->updateSimplyURL($u['id'],$url);
        }
        
    }
    function rank_promo(){
        $promo = $this->validation_model->getConfig(['promo_start_date', 'promo_end_date']);
        if(date('Y-m-d')>=$promo['promo_start_date'] && date('Y-m-d')<=$promo['promo_end_date']){
            $cron_id = $this->cron_model->insertCronHistory('rank_promo');
            $status = $this->cron_model->calculateRankPromoNew();
            if ($status) {
                $this->cron_model->updateCronHistory($cron_id, "finished");
                echo "finished";
            } else {
                $this->cron_model->updateCronHistory($cron_id, "failed");
                echo "failed";
            }
        }else{
             $cron_id = $this->cron_model->insertCronHistory('rank_promo');
            die('here');
        }
    }
    function updateUserRankPromo(){
        $users=$this->cron_model->getAllRankedUsers();
        foreach($users as $user){
            $this->cron_model->updatePromoRankFromRankHistory($user['id'],$user['user_rank_id']);
        }
        echo "finished";
    }
    function refund_epin(){
        $cron_id = $this->cron_model->insertCronHistory('refund_epin');
        $status = $this->cron_model->refundEpin();
        if ($status) {
            $this->cron_model->updateCronHistory($cron_id, "finished");
            echo "finished";
        } else {
            $this->cron_model->updateCronHistory($cron_id, "failed");
            echo "failed";
        }
    }
    function test(){
         $count=$this->cron_model->getSumTopUserAmountPayable();
        
         echo $count;die;
    }
    function revert_commission(){
        $this->load->model('register_model');
        $this->db->select('*');
        // $this->db->where('id>=',12615);
        // $this->db->where('id<=',13116);
        $this->db->where('id','5943');
        $res=$this->db->get('leg_amount');
        foreach($res->result_array() as $row){
            $amount=$row['amount_payable'];
            $user_id=$row['user_id'];
            $res2 = $this->register_model->deductFromBalanceAmount($user_id, $amount);
            $this->db->where('ewallet_id',$row['id']);
            $this->db->delete('ewallet_history');
            $this->db->where('id',$row['id']);
            $this->db->delete('leg_amount');
        }
        echo "finsihed";
    }

}
