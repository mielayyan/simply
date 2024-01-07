<?php

require_once 'Inf_Controller.php';

class Home extends Inf_Controller
{

    function __construct()
    { 
        parent::__construct();
        $this->load->model('profile_model');
        $this->load->model('report_model');
    }

    function index() {
        $title = $this->lang->line('overview');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "dashboard";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('overview');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('overview');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $user_id = $this->LOG_USER_ID;
        $session_data = $this->session->userdata('inf_logged_in');
        $table_prefix = $session_data['table_prefix'];
        $prefix = str_replace('_', '', $table_prefix);
        $site_url = SITE_URL;

        //joining
        $total_payout = $this->home_model->getPayoutDetails('', '', $user_id);
        $total_payout = $this->DEFAULT_CURRENCY_VALUE * $total_payout;
        $total_payout = $this->niceNumberCommission($total_payout);
        $total_payout = $this->DEFAULT_SYMBOL_LEFT . $total_payout . $this->DEFAULT_SYMBOL_RIGHT;
        //ewallet
        $ewallet_status = $this->MODULE_STATUS['ewallet_status'];
        $store_id = "";
        if($this->MODULE_STATUS['opencart_status'] == "yes")
        {
            if (DEMO_STATUS == 'yes'){
                $store_id = "&id=".str_replace("_","",$this->db->dbprefix);
            }
        }
        $this->set('store_id', $store_id);

        $product_status = $this->MODULE_STATUS['product_status'];
        $this->set("product_status", $product_status);
        $total_amount = 0;
        $requested_amount = 0;
        $total_request = 0;
        $total_released = 0;
        $commission = 0;
        $donation = 0;
        $given_donation = 0;
        $recieved_commission = 0;
        $donation_type = '';
        if ($ewallet_status == 'yes') {
            $total_amount = $this->home_model->getGrandTotalEwallet($user_id);
            $total_amount = $this->DEFAULT_CURRENCY_VALUE * $total_amount;
            $total_amount = $this->niceNumberCommission($total_amount);
            $total_amount = $this->DEFAULT_SYMBOL_LEFT . $total_amount . $this->DEFAULT_SYMBOL_RIGHT;
            $requested_amount = $this->home_model->getTotalRequestAmount($user_id);
            $total_request = $this->home_model->getGrandTotalEwallet($user_id);
            $total_released = $this->home_model->getTotalReleasedAmount($user_id);
         
        }
        
        //income expense profit
        $this->load->model('income_details_model');
        $this->load->model('ewallet_model');
        $user_id = $this->LOG_USER_ID;
        $total_expense = $this->ewallet_model->getuserExpense($user_id);
        $total_income = $this->ewallet_model->getUserIncome($user_id);
        $this->set("amount", $total_income);
        $this->set("expense", $total_expense);

        //commission earned
        $total_commission = $this->home_model->getCommissionDetails($from_date = '', $to_date = '', $user_id);
        $this->set("total_commission", $total_commission);

        //payout pending
        $this->load->model('payout_model');
        $payout_pending = $this->payout_model->getRequestPendingAmount($user_id);
        $this->set("payout_pending", $payout_pending);

        // sales
          $sales = $this->home_model->getTotalSales($from_date = '', $to_date = '', $user_id);
          $this->set("sales", $sales);
        
        //for sales
        $total_sales = $this->home_model->getSalesCount('', '', $user_id);
        $total_sales = $this->niceNumber($total_sales);
        $today_sales = $this->home_model->getSalesCount(date('Y-m-d') . " 00:00:00", date('Y-m-d') . " 23:59:59", $user_id);
        $today_sales = $this->niceNumber($today_sales);

        //mail
        $read_mail = $this->home_model->getTotaMailCount('user', '', '', $read_status = '', 'all');
        $read_mail = $this->niceNumber($read_mail);
        $mail_today = $this->home_model->getAllTodayMessages('user');
        $mail_today = $this->niceNumber($mail_today);

        $package_validity_date = $this->validation_model->getUserProductValidity($user_id);
        $product_id = $this->validation_model->getProductId($user_id);
        if ($product_id === 0) {
            $this->validation_model->hideReactivationMenu();
        }
        $show_package_validity_date = "no";
        $today = date("Y-m-d H:i:s");
        $last_month = $package_validity_date;
        if ($product_id != 0 && $today < $last_month && $this->MODULE_STATUS['subscription_status'] == 'yes' && $this->MODULE_STATUS['product_status'] == 'yes') {
            $show_package_validity_date = "yes";
        }
        $package_validity_date = date("F j, Y, g:i a", strtotime($package_validity_date));
        $this->set("show_package_validity_date", $show_package_validity_date);
        $this->set("package_validity_date", $package_validity_date);
        $this->set("product_id", $product_id);

        //Social Media
        $social_media_info = $this->home_model->getSocialMediaInfo();
        //top 5 recruters
        $top_recruters = $this->home_model->getTopRecruters(7, $this->LOG_USER_ID);
        $j = 0;
        foreach ($top_recruters as $v) {
            $top_recruters[$j]['user_full_name'] = $v['user_detail_name']." ".$v['user_detail_second_name'];
            if (file_exists(IMG_DIR . "profile_picture/" . $top_recruters[$j]['profile_picture'])) {
                $top_recruters[$j]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/" . $top_recruters[$j]['profile_picture'];
            } else {
                $top_recruters[$j]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/nophoto.jpg";
            }
            $j++;
        }
        //top 5 Earners
        $top_earners = $this->home_model->getTopEarners(4, $this->LOG_USER_ID);

        $i = 0;
        foreach ($top_earners as $v) {
            $top_earners[$i]['user_full_name'] = $v['user_detail_name']." ".$v['user_detail_second_name'];
            $top_earners[$i]['balance_amount'] = $this->DEFAULT_SYMBOL_LEFT . number_format($v['balance_amount'] * $this->DEFAULT_CURRENCY_VALUE, 2) . $this->DEFAULT_SYMBOL_RIGHT;
            $top_earners[$i]['profile_picture'] = $this->validation_model->getProfilePicture($v['id']);
            if (file_exists(IMG_DIR . "profile_picture/" . $top_earners[$i]['profile_picture'])) {
                $top_earners[$i]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/" . $top_earners[$i]['profile_picture'];
            } else {
                $top_earners[$i]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/nophoto.jpg";
            }
            $i++;
        }
        //logged User details
        $user_details = $this->validation_model->getUserDetails($this->LOG_USER_ID, $this->LOG_USER_TYPE);
        $user_details['user_name'] = $this->LOG_USER_NAME;
        if($this->MODULE_STATUS['opencart_status'] == "yes" || $this->MODULE_STATUS['opencart_status_demo'] == 'yes') {
            $user_details['membership'] = $this->validation_model->getOpenCartProductNameFromUserID($this->LOG_USER_ID);
        } else {
            $user_details['membership'] = $this->validation_model->getProductNameFromUserID($this->LOG_USER_ID);
        }
      

        //Data For World Map
        $map_data = $this->home_model->getCountryMapdata($this->LOG_USER_ID);
        //Data For Progress bar
        $prgrsbar_data = $this->home_model->getPackageProgressData(4, $this->LOG_USER_ID);
        
        $rank_data = $this->home_model->getRankData(4, $this->LOG_USER_ID);

        if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
            $date = date('Y-m-d');
            $rs = $this->joining_model->getJoiningDetailsForBinaryLeftRight($this->LOG_USER_ID);
            $binary_tree = $this->home_model->individulaDetails($this->LOG_USER_ID, ['total_left_carry','total_right_carry']);
            $binary_tree_carry = [
                'total_left_carry' => $binary_tree->total_left_carry == 0 ? 0 : $binary_tree->total_left_carry,
                'total_right_carry' => $binary_tree->total_right_carry == 0 ? 0 : $binary_tree->total_right_carry,
            ];
            $this->set("binary_tree_carry", $binary_tree_carry);
            $daily_joining = $this->joining_model->getJoiningDetailsForBinaryLeftRight($this->LOG_USER_ID, $date . " 00:00:00", $date . " 23:59:59");
            $joining["joinings_data2"] = $rs['joining'];
            $joining["joinings_data4"] = $rs['joining_right'];
            $joining["joinings_data1"] = $daily_joining['joining'];
            $joining["joinings_data3"] = $daily_joining['joining_right'];
        } else {
            $start_date = date('Y-m-01') . " 00:00:00";
            $end_date = date("Y-m-d 23:59:59", strtotime("+1 month -1 day"));
            $week_end_date = date('Y-m-d') . " 23:59:59";
            $week_start_date = date('Y-m-d', strtotime('last sunday')) . " 00:00:00";

            $joining["joinings_data2"] = $this->home_model->totalJoiningUsers($this->LOG_USER_ID);
            $joining["joinings_data1"] = $this->home_model->todaysJoiningCount($this->LOG_USER_ID);
            $joining["joinings_data4"] = $this->joining_model->getJoiningCountPerMonth($start_date, $end_date, $this->LOG_USER_ID);
            $joining["joinings_data3"] = $this->tree_model->getDownlineUsersCount($this->LOG_USER_ID, 'father', $week_start_date, $week_end_date);
        }

        //////////////////////////////////////////////////////////////////////////////

        $latest_joinees = $this->home_model->getLatestJoinees('user');
        $j = 0;
        foreach ($latest_joinees as $v) {
            if (file_exists(IMG_DIR . "profile_picture/" . $latest_joinees[$j]['profile_pic'])) {
                $latest_joinees[$j]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/" . $latest_joinees[$j]['profile_pic'];
            } else {
                $latest_joinees[$j]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/nophoto.jpg";
            }
            $j++;
        }

        $current_rank = null;
        $rank_criteria = null;
        $next_rank = null;
        $crank = null;
        $nrank = null;
        $current_pool=null;
        $cpool=null;
        $npool=null;
        $next_pool=null;
        if ($this->MODULE_STATUS['rank_status'] == 'yes') {
            $this->load->model('rank_model');
            $rank_achievement = $this->rank_model->getRankCriteria($this->LOG_USER_ID);
            $this->set("rank_achievement", $rank_achievement);
            /**
             * Current Rank
             */
            $crank = $this->rank_model->currentRankName($this->LOG_USER_ID);
            $cpool=$this->rank_model->currentPool($this->LOG_USER_ID);
            if(!empty($crank))  {
                $current_rank = $this->rank_model->getCurrentRankData($this->LOG_USER_ID);
                $rank_criteria = array_keys($current_rank['criteria'], 1);
                $nrank = $this->rank_model->NextRankName($crank['rank_id']);
               // $current_pool = $this->rank_model->getCurrentPoolData($this->LOG_USER_ID);
            } else {
                $nrank = $this->rank_model->NextRankName();
            }
            if(!empty($cpool)){
                $current_pool = $this->rank_model->getCurrentPoolData($this->LOG_USER_ID);
                $npool = $this->rank_model->NextPromoRank($cpool['id']);
            }else{
                $npool = $this->rank_model->NextPromoRank(0);
            }
            
            
            
            // rank configuration
            $rank_configuration = $this->configuration_model->getRankConfiguration();
            $this->set("rank_configuration", $rank_configuration);
            // end of rank configuration
            /**
             * Next Rank
             */
            if(!empty($nrank)) {
                $next_rank = $this->rank_model->getNextRankData($nrank['rank_id'],$this->LOG_USER_ID);
               
            }
            if(!empty($npool)){
                 $next_pool = $this->rank_model->getNextPoolData($npool['id'],$this->LOG_USER_ID);
            }

        }
        $promo = $this->validation_model->getConfig(['promo_start_date', 'promo_end_date']);
        $promo_status='no';
        if(date('Y-m-d')>=$promo['promo_start_date'] && date('Y-m-d')<=$promo['promo_end_date']){
            $promo_status='yes';
        }
        $this->set('promo_status', $promo_status);
        $this->set("promo", $promo);
        $this->set("nrank", $nrank['rank_name'] ?? "");
        $this->set("crank", $crank['rank_name'] ?? "");
        $this->set("rank_criteria", $rank_criteria);
        $this->set("current_rank", $current_rank);
        $this->set("next_rank", $next_rank);
        $this->set("npool", $npool ?? "");
        $this->set("cpool", $cpool ?? "");
        $this->set('rank_promo',$this->home_model->selectRankDetailsPromo());
        $this->set("current_pool", $current_pool);
        $this->set("next_pool", $next_pool);
        $this->set("travel_voucher", $this->home_model->getTravelVoucher($this->LOG_USER_ID));
        //package upgarde
            if($this->MODULE_STATUS['package_upgrade'] == "yes" && $this->MODULE_STATUS['opencart_status'] == "no") {
                $this->load->model('upgrade_model');
                $current_package_details = $this->upgrade_model->getMembershipPackageDetails($this->LOG_USER_ID);
                $upgradable_package_list = $this->upgrade_model->getUpgradablePackageList($current_package_details);
                $this->set('upgradable_package_list', $upgradable_package_list);
            }
        // end of package upgrade
        $profile_arr = $this->profile_model->getProfileDetails($this->LOG_USER_ID, $product_status);
        // dd($profile_arr);
        $profile_details = $profile_arr['details'];
        $product_name = '';
        $product_validity = '';
        $grace_validity = '';
        if ($product_status == 'yes') {
            $product_name = $profile_arr['product_name'];
            $product_validity = $profile_arr['product_validity'];
            $product_validity = $profile_arr['product_validity'];
        }

        $this->set("simply_url", $profile_details['simply_url']);
        $this->set("product_name", $product_name);
        $this->set("product_validity", $product_validity);
        $this->set("grace_validity", $grace_validity);
        //loginuser full name
        $user_details['full_name'] = $user_details['user_detail_name']." ".$user_details['user_detail_second_name'];
        //package
        if($this->MODULE_STATUS['package_upgrade'] == "yes" && !$this->MODULE_STATUS['opencart_status']) {
            $this->load->model('upgrade_model');
            $current_package_details = $this->upgrade_model->getMembershipPackageDetails($this->LOG_USER_ID);
            $upgradable_package_list = $this->upgrade_model->getUpgradablePackageList($current_package_details);
            $this->set('upgradable_package_list', $upgradable_package_list);
        }
        // rank color 
        $rank_color = "#7d899b";
        $user_rank_id = $this->validation_model->getUserRank($this->LOG_USER_ID);
        if($user_rank_id)
        {

            $rank_details = $this->configuration_model->getActiveRankDetails($user_rank_id);
            $rank_color =$rank_details[0]['rank_color'];
        
        }
        $this->set("rank_color",$rank_color);
        // end of rank color
        /** News */
        $news = $this->news_model->getAllNews(6, 0);
        $this->set('news', $news);
        // dd($user_details);
        // $this->set("joining_data", $joining);
        $this->set("prgrsbar_data", $this->security->xss_clean($prgrsbar_data));
        $this->set("rank_data", $rank_data);
        $this->set("map_data", $map_data);
       
        $this->set("total_payout", $total_payout);
        // $this->set("todays_payout", $todays_payout);
        // $this->set("placement_joining", $placement_joinig);
        // $this->set("todays_placement_joining", $todays_placement_joining);
        // $this->set("donation_type", $donation_type);

        $this->set("total_amount", $total_amount);
        // $this->set("requested_amount", $requested_amount);
        // $this->set("total_request", $total_request);
        // $this->set("total_released", $total_released);
        // $this->set("commission", $commission);
        // $this->set("donation", $donation);

        // $this->set("total_sales", $total_sales);
        // $this->set("today_sales", $today_sales);

        /*$this->set("read_mail", $read_mail);
        $this->set("mail_today", $mail_today);*/

        
        /*$this->set("pinstatus", "NO");
        $this->set("user_id", $user_id);
        $this->set("table_prefix", $prefix);*/
        //$board_status=$this->validation_model->getBoardRegisterStatus($this->LOG_USER_ID);
	$board_status='yes';
        if($board_status=='no'){
            $sponsor_id=$this->validation_model->getSponsorId($this->LOG_USER_ID);
            $sponsor_username=$this->validation_model->idToUsername($sponsor_id);
            $board_downlines=$this->getBoardDownlines($sponsor_username);
            $down_count=0;
            if(isset($board_downlines['data'])){
                $down_count=count($board_downlines['data']);
                //dd($board_downlines['data']);
                $this->set('down_board',$board_downlines['data']);
            }
            $this->set('down_count',$down_count);
        }
        $this->set('board_status',$board_status);
        $check_package_support_board=$this->validation_model->getPackageSupport($this->LOG_USER_ID,'board');
        $this->set('check_package_support_board',$check_package_support_board);
        $check_package_support_services=$this->validation_model->getPackageSupport($this->LOG_USER_ID,'services');
        $this->set('check_package_support_services',$check_package_support_services);
        $check_package_support_tourism=$this->validation_model->getPackageSupport($this->LOG_USER_ID,'tourism');
        $this->set('check_package_support_tourism',$check_package_support_tourism);
        !$this->MODULE_STATUS['opencart_status']? $this->set("current_package_details",$current_package_details):null;
        $this->set("site_url", $site_url);
        $this->set("top_recruters", $top_recruters);
        $this->set("top_earners", $top_earners);
        $this->set("user_details", $user_details);

        $this->set('social_media_info', $social_media_info);
        $this->set("latest_joinees", $latest_joinees);
        /**
         * @author Treesa
         * new dashboard tiles
         * */
        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');
        $month = date('m');
        $today_steps = $this->home_model->getCountOfSteps('leg', $from_date, $to_date,'', $this->LOG_USER_ID,'today');
        $this->set("today_steps", $today_steps);

        $monthly_steps = $this->home_model->getCountOfSteps('leg','', '',$month, $this->LOG_USER_ID,'month');
        $this->set("monthly_steps", $monthly_steps);
        $flushoutlimit = $this->validation_model->getFlushoutLimit($this->LOG_USER_ID);
        $todays_binary_count=$this->home_model->getTodaysBinaryCount($this->LOG_USER_ID);
        $cumulativecount=0;
        if($todays_binary_count)
            $cumulativecount  = intval($todays_binary_count/$flushoutlimit);
        //if($cumulativecount <= $flushoutlimit){
            $this->set("cumulativecount", $cumulativecount);
        // }else{
        //     $this->set("cumulativecount",$flushoutlimit);
        // }
        
        
        $rwallet_steps = $this->home_model->getRwalletSteps($this->LOG_USER_ID);
        $this->set("rwallet_steps", $rwallet_steps);

        $user_status = $this->validation_model->getUserStatus($this->LOG_USER_ID);
        $this->set("user_status", $user_status);

        $kyc_status = $this->validation_model->checkKycUpload($this->LOG_USER_ID);
        $this->set("kyc_status", $kyc_status);
        $founderspool_bonus = $this->home_model->getFoundersPoolBonus($this->LOG_USER_ID);
        $pool_shares=$this->home_model->getPoolShares();
        $this->set("founderspool_bonus", $founderspool_bonus);
        $this->set("pool_shares", $pool_shares);
    //dashboard tiles end////////////////////////////////////////////////////////////////////////////////////
       

        if (DEMO_STATUS == 'yes') {
            $is_preset_demo = $this->validation_model->isPresetDemo($this->ADMIN_USER_ID);
            $this->set('is_preset_demo', $is_preset_demo);
        }

        /**
         * Profile Section
         * @var [type]
         */
        $product_status = $this->MODULE_STATUS['product_status'];
        $pro_file_arr = $this->profile_model->getProfileDetails($this->LOG_USER_ID, $product_status);
        $this->set('user_product_name', $pro_file_arr['product_name'] ?? '');
        $this->set('user_validity', $pro_file_arr['details']['product_validity'] ?? '');
        $this->set('profile_photo', $pro_file_arr['details']['profile_photo']);

        $incomes = $this->home_model->getAllIncomeOrExpense($this->LOG_USER_ID, 'credit', 4);
        $expenses = $this->home_model->getAllIncomeOrExpense($this->LOG_USER_ID, 'debit', 4);
        $payout_statuses = [
            'requested' => $this->payout_model->getUserTotalPayoutRequests($this->LOG_USER_ID, 'pending'),
            'approved' => $this->payout_model->getUserTotalPayouts($this->LOG_USER_ID, 'approved'),
            'paid' => $this->payout_model->getUserTotalPayouts($this->LOG_USER_ID, 'paid'),
            'rejected' => $this->payout_model->getUserTotalPayoutRequests($this->LOG_USER_ID, 'deleted')
        ];
        $this->set('incomes', $incomes);
        $this->set('expenses', $expenses);
        $this->set('payouts', $payout_statuses);
        $individula_details = $this->home_model->individulaDetails($this->LOG_USER_ID, ['personal_pv', 'gpv']);
            $profile_extra_data = [
                'placement_user_name' => $pro_file_arr['details']['father_name'],
                'sponsor_name' => $pro_file_arr['details']['sponsor_name'],
                'personal_pv' => $individula_details->personal_pv == 0 ? 0 : $individula_details->personal_pv,
                'group_pv' => $individula_details->gpv == 0 ? 0 : $individula_details->gpv,
            ];
            $this->set('profile_extra_data', $profile_extra_data);

        if($this->MODULE_STATUS['replicated_site_status'] == "no" && $this->MODULE_STATUS['lead_capture_status'] == "no") {
            $individula_details = $this->home_model->individulaDetails($this->LOG_USER_ID, ['personal_pv', 'gpv']);
            $profile_extra_data = [
                'placement_user_name' => $pro_file_arr['details']['father_name'],
                'sponsor_name' => $pro_file_arr['details']['sponsor_name'],
                'personal_pv' => $individula_details->personal_pv == 0 ? 0 : $individula_details->personal_pv,
                'group_pv' => $individula_details->gpv == 0 ? 0 : $individula_details->gpv,
            ];
            $this->set('profile_extra_data', $profile_extra_data);
        }
        // $this->lang->load('amount_type');

        if ($this->MODULE_STATUS['mlm_plan'] == 'Donation') {
            $donation_type = $this->validation_model->getColoumnFromTable("configuration", "donation_type");
            $this->load->model('donation_model');
            $donation_level = $this->donation_model->getLevelName($this->donation_model->getCurrentLevel($user_id));
            $this->set('donation_level', $donation_level);
            $this->set('donation_type', $donation_type);
        }
        $this->set("dashboardConfig", $this->home_model->getUserDashboardConfig());
        $this->set('max_out_count',$this->home_model->getMaxOutDays($user_id));
        $this->setView();
    }
    public function news()
    {
        $this->load->model('news_model');
        $news_arr = $this->news_model->getLatestNews();
        $this->set("news_list", $this->security->xss_clean($news_arr));
        $this->setView();
    }


    /* Ajax Functon For Payout Tile */
    function ajax_payout($range)
    {
        $total_amount = 0;
        $user_id = $this->LOG_USER_ID;
        if ($range == 'monthly_payout') {
            $start_date = date('Y-m-01', strtotime('this month')) . " 00:00:00";
            $end_date = date('Y-m-t', strtotime('this month')) . " 23:59:59";
        }
        if ($range == 'yearly_payout') {
            $start_date = date('Y-01-01') . " 00:00:00";
            $end_date = date('Y-12-31') . " 23:59:59";
        }
        if ($range == 'weekly_payout') {
            $tomorrow = date("Y-m-d", time() + 86400);
            $start_date = date('Y-m-d', strtotime('last Sunday', strtotime($tomorrow))) . " 00:00:00";
            $end_date = date('Y-m-d') . " 23:59:59";
        }
        if ($range == 'all_payout') {
            $start_date = '';
            $end_date = '';
        }
        $total_amount = $this->home_model->getPayoutDetails($start_date, $end_date, $user_id);
        $total_amount = $total_amount * $this->DEFAULT_CURRENCY_VALUE;
        $total_amount = $this->niceNumberCommission($total_amount);
        $total_amount = $this->DEFAULT_SYMBOL_LEFT . $total_amount . $this->DEFAULT_SYMBOL_RIGHT;
        echo $total_amount;
    }

 /* Ajax Functon For Commission  Tile*/

 function ajax_commission($range)
    {
  
        $total_amount = 0;
        $user_id = $this->LOG_USER_ID;

        if ($range == 'monthly_commission') {
            $start_date = date('Y-m-01', strtotime('this month')) . " 00:00:00";
            $end_date = date('Y-m-t', strtotime('this month')) . " 23:59:59";
        }
        if ($range == 'yearly_commission') {
            $start_date = date('Y-01-01') . " 00:00:00";
            $end_date = date('Y-12-31') . " 23:59:59";
        }
        if ($range == 'weekly_commission') {
            $tomorrow = date("Y-m-d", time() + 86400);
            $start_date = date('Y-m-d', strtotime('last Sunday', strtotime($tomorrow))) . " 00:00:00";
            $end_date = date('Y-m-d') . " 23:59:59";
        }
        if ($range == 'all_commission') {
            $start_date = '';
            $end_date = '';
        }
        $total_commission = $this->home_model->getCommissionDetails($start_date, $end_date, $user_id);

        // $total_commission = $total_commission * $this->DEFAULT_CURRENCY_VALUE;
        // $total_commission = $this->niceNumberCommission($total_commission);
        $total_commission = thousands_currency_format($total_commission);
        // $total_commission = $this->DEFAULT_SYMBOL_LEFT . $total_commission . $this->DEFAULT_SYMBOL_RIGHT;

        echo $total_commission;
    }


    /* Ajax Functon For Sales  Tile*/

    function ajax_sales($range)
    {
        $total_sales = 0;
        $user_id = $this->LOG_USER_ID;

        if ($range == 'monthly_sales') {
            $start_date = date('Y-m-01', strtotime('this month')) . " 00:00:00";
            $end_date = date('Y-m-t', strtotime('this month')) . " 23:59:59";
        }
        if ($range == 'yearly_sales') {
            $start_date = date('Y-01-01') . " 00:00:00";
            $end_date = date('Y-12-31') . " 23:59:59";
        }
        if ($range == 'weekly_sales') {
            $tomorrow = date("Y-m-d", time() + 86400);
            $start_date = date('Y-m-d', strtotime('last Sunday', strtotime($tomorrow))) . " 00:00:00";
            $end_date = date('Y-m-d') . " 23:59:59";
            //echo 'start:- '.$start_date.' enddate: '.$end_date;  exit;
        }
        if ($range == 'all_sales') {
            $start_date = '';
            $end_date = '';
        }

        $total_sales = $this->home_model->getTotalSales($start_date, $end_date, $user_id);
        $total_sales = $total_sales * $this->DEFAULT_CURRENCY_VALUE;
        $total_sales = $this->niceNumber($total_sales);
         $total_sales = $this->DEFAULT_SYMBOL_LEFT . $total_sales . $this->DEFAULT_SYMBOL_RIGHT;
        echo $total_sales;
    }

    /* Ajax Functon For Mail Tile*/

    function ajax_mail($range)
    {
        $count_mail_read = 0;
        $count_mail_unread = 0;
        $start_date = '';
        $end_date = '';
        if ($range == 'monthly_mail') {
            $start_date = date('Y-m-01', strtotime('this month')) . " 00:00:00";
            $end_date = date('Y-m-t', strtotime('this month')) . " 23:59:59";
        }
        if ($range == 'yearly_mail') {
            $start_date = date('Y-01-01') . " 00:00:00";
            $end_date = date('Y-12-31') . " 23:59:59";
        }
        if ($range == 'weekly_mail') {
            $tomorrow = date("Y-m-d", time() + 86400);
            $start_date = date('Y-m-d', strtotime('last Sunday', strtotime($tomorrow))) . " 00:00:00";
            $end_date = date('Y-m-d') . " 23:59:59";
        }
        $count_mail_total = $this->home_model->getMailCount('user', $start_date, $end_date, $read_status = '');
        $count_mail_unread = $this->home_model->getMailCount('user', $start_date, $end_date, $read_status = 'no');

        if ($range == 'all_mail') {
            $count_mail_total = $this->home_model->getMailCount('user', $start_date, $end_date, $read_status = '', 'all');
            $count_mail_unread = $this->home_model->getAllUnreadMessages('user');
        }
        $count_mail_total = $this->niceNumber($count_mail_total);
        $count_mail_unread = $this->niceNumber($count_mail_unread);
        $result = array('mail_total' => $count_mail_total, 'mail_unread' => $count_mail_unread);
        echo json_encode($result);
    }


    /* Ajax Functon For Joining In Chart */
    function ajax_user_points_and_carrys() {
        $points['left'] = $this->joining_model->getUserPointsAndCarrys($this->LOG_USER_ID, 'left_leg');
        $points['right'] = $this->joining_model->getUserPointsAndCarrys($this->LOG_USER_ID, 'right_leg');
        $points['left_carry'] = $this->joining_model->getUserPointsAndCarrys($this->LOG_USER_ID, 'left_carry');
        $points['right_carry'] = $this->joining_model->getUserPointsAndCarrys($this->LOG_USER_ID, 'right_carry');
        $points['active_members']= $this->joining_model->getJoiningDetailsForBinaryLeftRight($this->LOG_USER_ID);
        echo json_encode($points);
    }
    function ajax_joinings_chart($range)
    {
        $this->load->model('joining_model');
        $rs = [];
        $i = 0;
        if ($range == 'monthly_joining_graph') {
            $monthly_joining = $this->home_model->getJoiningDetailsperMonth($this->LOG_USER_ID);
            if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
                $monthly_joining = $this->joining_model->getJoiningDetailsperMonthLeftRight($this->LOG_USER_ID);
            }
            foreach ($monthly_joining as $value) {
                $rs[$i]["x"] = $i;
                $rs[$i]["x_label"] = $value['month'];
                $rs[$i]["y"] = $value['joining'];
                if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
                    $rs[$i]["z"] = $value['joining_right'];
                }
                $i++;
            }
        }
        if ($range == 'yearly_joining_graph') {
            while ($i <= 5) {
                $j = 5 - $i;
                $start_date = date('Y-01-01', strtotime("-$j year")) . " 00:00:00";
                $end_date = date('Y-12-31', strtotime("-$j year")) . " 23:59:59";
                $rs[$i]["x"] = $i;
                $rs[$i]["x_label"] = intval($start_date);
                if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
                    $yearly_joining = $this->joining_model->getJoiningDetailsForBinaryLeftRight($this->LOG_USER_ID, $start_date, $end_date);
                    $rs[$i]["y"] = $yearly_joining['joining'];
                    $rs[$i]["z"] = $yearly_joining['joining_right'];
                } else {
                    $rs[$i]["y"] = $this->joining_model->getJoiningCountPerMonth($start_date, $end_date, $this->LOG_USER_ID);
                }
                $i++;
            }
            
        }
        if ($range == 'daily_joining_graph') {
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t', strtotime('this month'));
            while ($start_date <= $end_date) {
                $rs[$i]["x"] = $i;
                $rs[$i]["x_label"] = date('d', strtotime($start_date));

                if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
                    $daily_joining = $this->joining_model->getJoiningDetailsForBinaryLeftRight($this->LOG_USER_ID, $start_date . " 00:00:00", $start_date . " 23:59:59");
                    $rs[$i]["y"] = $daily_joining['joining'];
                    $rs[$i]["z"] = $daily_joining['joining_right'];
                } else {
                    $rs[$i]["y"] = $this->joining_model->getJoiningCountPerMonth($start_date . " 00:00:00", $start_date . " 23:59:59", $this->LOG_USER_ID);
                }
                $start_date = date('Y-m-d', strtotime($start_date . ' +1 day'));
                $i++;
            }
            
        }
        echo json_encode($rs);
    }

    public function add_todo()
    {
        $title = lang('configure_todo');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "dashboard";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = $title;
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = $title;
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $this->setView();
    }
    public function edit_todo()
    {
        $title = lang('configure_todo');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "dashboard";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = $title;
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = $title;
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();


        $id = $this->input->post('id', true);
        $todo_list = $this->home_model->getToDoList($this->LOG_USER_ID, $id);

        $date = date('Y-m-d', strtotime($todo_list[0]['time']));
        $time = date('g:i a', strtotime($todo_list[0]['time']));
        $task = $todo_list[0]['task'];

        $this->set('date', $date);
        $this->set('task_id', $id);
        $this->set('time', $time);
        $this->set('task', $task);
        $this->set('todo_list', $todo_list);
        $this->setView();
    }

    public function delete_todo()
    {
        $id = $this->input->post('tsk_id', true);
        $res = $this->home_model->deleteToDoList($this->LOG_USER_ID, $id);
        if ($res) {
            $data_array['details'] = $res;
            $data = serialize($data_array);
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Deleted Task in To-do list', $this->LOG_USER_ID, $data);
            $this->redirect(lang('deleted_task_successfully'), 'home/index', true);
        } else {
            $this->redirect(lang('failed_to_delete'), 'home/index', false);
        }
    }

    public function change_todo()
    {
        $id = $this->input->post('id', true);
        $status = $this->input->post('status', true);
        $res = $this->home_model->ChangeToDoStatus($this->LOG_USER_ID, $id, $status);
        if ($res) {
            $data_array['details'] = $res;
            $data = serialize($data_array);
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Status Changed for Task in To-do list', $this->LOG_USER_ID, $data);
        }
    }

    public function validate_todo_list()
    {
        $this->form_validation->set_rules('task', lang("task"), 'trim|required');
        $this->form_validation->set_rules('task_time', lang("time"), 'trim|required|valid_time');
        $this->form_validation->set_rules('task_date', lang("date"), 'trim|required|valid_date|callback_validate_date');
        $validation_result = $this->form_validation->run();
        return $validation_result;
    }

    public function validate_addlist()
    {
        $json = array();
        if ($this->validate_todo_list()) {
            $post_arr = $this->validation_model->stripTagsPostArray($this->input->post(null, true));
            $user_id = $this->LOG_USER_ID;

            $time = date("H:i:s", strtotime($post_arr['task_time']));
            $date = date('y-m-d', strtotime($post_arr['task_date']));
            $start_time = ($date . ' ' . $time);

            $res = $this->home_model->addToDoList($post_arr['task'], $start_time, $user_id);

            if ($res) {
                $json['success'] = lang("task_added_to_to_do_list_successfully");
                $data = serialize($json);
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, lang("task_added_to_to_do_list_successfully"), $this->LOG_USER_ID, $data);
            } else {
                $json['error']['warning'] = lang("an_error_occurred");
            }
        } else {
            $json['error'] = $this->form_validation->error_array();
        }

        echo json_encode($json);
        exit();
    }
    public function validate_edit_todo()
    {
        $json = array();
        if ($this->validate_todo_list()) {
            $post_arr = $this->validation_model->stripTagsPostArray($this->input->post(null, true));
            $user_id = $this->LOG_USER_ID;

            $time = date("H:i:s", strtotime($post_arr['task_time']));
            $date = date('y-m-d', strtotime($post_arr['task_date']));
            $start_time = ($date . ' ' . $time);

            $res = $this->home_model->updateToDoList($post_arr['task'], $start_time, $user_id, $post_arr['task_id']);

            if ($res) {
                $json['success'] = lang("task_edited_to_to_do_list_successfully");
            } else {
                $json['error']['warning'] = lang("an_error_occurred");
            }
        } else {
            $json['error'] = $this->form_validation->error_array();
        }

        echo json_encode($json);
        exit();
    }

    public function validate_date()
    {
        $post_arr = $this->validation_model->stripTagsPostArray($this->input->post(null, true));
        $date = strtotime($post_arr['task_date'] . ' ' . $post_arr['task_time']);
        $current_date = strtotime(date('Y-m-d H:i:s'));
        if ($date < $current_date) {
            $this->form_validation->set_message('validate_date', lang("task_date_cannot_be_less_than_the_current_date"));
            return false;
        } else {
            return true;
        }
    }

 /*Package List - Progressbar Starts*/
    public function package_list()
    {
        $title = lang('package_list');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $help_link = 'package_list';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('package_list');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('package_list');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $this->load->model("product_model");

        $package_type = 'registration';
        $pro_status = 'yes';

        $config = $this->pagination->customize_style();
        $base_url = base_url() . 'user/home/package_list';
        $config['base_url'] = $base_url;
        $config['per_page'] = $this->PAGINATION_PER_PAGE;
        $tot_rows = $this->product_model->getPackageCountForProgressbar($package_type, $pro_status);

        $config['total_rows'] = $tot_rows;
        $config['uri_segment'] = 4;

        $this->pagination->initialize($config);
        $page = 0;
        if ($this->uri->segment(4) != '') {
            $page = $this->uri->segment(4);
        }

        $i = 0;
        $total_count = $this->home_model->getTotalPackageCount($this->LOG_USER_ID);
        $product_details = $this->product_model->getPackageListForProgressbar($package_type, "", $config['per_page'], $page);
        foreach ($product_details as $v) {
            $product_details[$i]['perc'] = ($total_count * (int)$this->home_model->getPackageCount($v['prod_id'], $this->LOG_USER_ID));
            $i++;
        }

        
        $this->set('product_details', $this->security->xss_clean($product_details));
        
        $this->set('page', $page);
        $this->setView();
    }
 /*Package List - Progressbar Ends*/
    function niceNumber($n)
    {
        // first strip any formatting;
        $n = (0 + str_replace(",", "", $n));

        // is this a number?
        if (!is_numeric($n)) return false;

        // now filter it;
        if ($n > 1000000000000) return round(($n / 1000000000000), 2) . ' T';
        elseif ($n > 1000000000) return round(($n / 1000000000), 2) . ' B';
        elseif ($n > 1000000) return round(($n / 1000000), 2) . ' M';

        return number_format($n);
    }
    function niceNumberCommission($n)
    {
        // first strip any formatting;
        $n = (0 + str_replace(",", "", $n));

        // is this a number?
        if (!is_numeric($n)) return false;

        // now filter it;
        if ($n > 1000000000000) return round(($n / 1000000000000), 2) . ' T';
        elseif ($n > 1000000000) return round(($n / 1000000000), 2) . ' B';
        elseif ($n > 1000000) return round(($n / 1000000), 2) . ' M';
    //  elseif ($n > 1000) return round(($n / 1000), 2) . ' K';

        return number_format($n, 2);
    }

    public function roi_details()
    {
        $help_link = 'roi';
        $this->set('help_link', $help_link);

        if ($this->MLM_PLAN == 'Unilevel' && $this->MODULE_STATUS['hyip_status'] == 'yes') {
            $title = lang('roi');
        } else {
            $title = lang('total_deposit');
        }
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $this->HEADER_LANG['page_top_header'] = lang('roi');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('roi');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $config = $this->pagination->customize_style();
        $base_url = base_url() . "user/home/roi_details";
        $config['base_url'] = $base_url;
        $config['per_page'] = $this->PAGINATION_PER_PAGE;
        if ($this->uri->segment(4) != "") {
            $page = $this->uri->segment(4);
        } else {
            $page = 0;
        }

        $user_id = $this->LOG_USER_ID;

        $total_amount = $this->home_model->getHyipTotalLegamount($user_id);
        $roi_details = array();
        $roi_details = $this->home_model->getHyipTotalLegAmountDetails($page, $config['per_page'], $user_id);
        $total_rows = $this->home_model->getReturnInvestmentDetailsCount($user_id);
        $this->set("roi_details", $this->security->xss_clean($roi_details));
        $config['total_rows'] = $total_rows;
        $this->set('details_count', $total_rows);
        $this->pagination->initialize($config);
        $this->set("total_amount", $total_amount);
        
        
        $this->set('page_id', $page);
        $this->setView();
    }

    public function active_deposit()
    {
        $title = lang('active_deposit');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $help_link = 'active_deposit';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('active_deposit');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('active_deposit');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $user_id = $this->LOG_USER_ID;

        $total_active_amount = $this->home_model->getActiveDeposit($user_id);
        $active_deposit = array();
        $active_deposit = $this->home_model->getActiveDepositDetails($user_id);
        $total_rows = count($active_deposit);
        $this->set("active_deposit", $active_deposit);
        $this->set('details_count', $total_rows);
        $this->set("total_amount", $total_active_amount);

        $this->setView();
    }

    public function matured_deposit()
    {

        $title = lang('matured_deposit');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $help_link = 'matured_deposit';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('matured_deposit');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('matured_deposit');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $user_id = $this->LOG_USER_ID;

        $total_deposit = $this->home_model->getHyipTotalLegAmount($user_id);
        $total_active_deposit = $this->home_model->getActiveDeposit($user_id);
        $total_matured_deposit = $total_deposit - $total_active_deposit;
        $matured_deposit = array();
        $matured_deposit = $this->home_model->getMaturedDepositDetails($user_id);
        $total_rows = count($matured_deposit);

        $this->set("matured_deposit", $matured_deposit);
        $this->set('details_count', $total_rows);
        $this->set("total_matured_deposit", $total_matured_deposit);
        $this->setView();
    }

    public function change_default_language() {
        if ($this->input->is_ajax_request()) {
            $this->load->model('multi_language_model');
            $language = $this->input->post('language', TRUE);
            $res = $this->multi_language_model->setUserDefaultLanguage($language, $this->LOG_USER_ID);
            if ($res) {
                echo 'yes';
            }
            else {
                echo 'no';
            }
            exit();
        }
    }

    public function ajax_user_downline_autolist() {
        if ($this->input->is_ajax_request()) {
            $keyword = $this->input->post('keyword', TRUE);
            $data = $this->home_model->getDownlineUsersByKeyword($this->LOG_USER_ID, $keyword);
            echo json_encode($data);
            exit();
        }
    }

    public function update_theme_setting()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post(null, true);
            
            
            $response = $this->validation_model->updateThemeSetting($data);
            echo $response;
            exit();
            
        }
    }

     public function get_theme_details()
    {

     $theme_type=$this->input->post('theme_type');
     
     $theme=$this->validation_model->getThemeSettingsByAllUser($theme_type); 
     echo $theme;
      

    }
    function dashboard_new() {
        $title = $this->lang->line('overview');
        $this->VIEW_DATA['title'] = $title;

        $help_link = "dashboard";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('overview');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('overview');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->set_public_variables();

        $user_id = $this->LOG_USER_ID;
        $session_data = $this->session->userdata('inf_logged_in');
        $table_prefix = $session_data['table_prefix'];
        $prefix = str_replace('_', '', $table_prefix);
        $site_url = SITE_URL;
        $total_payout = $this->home_model->getPayoutDetails('', '', $user_id);
        $total_payout = $this->DEFAULT_CURRENCY_VALUE * $total_payout;
        $total_payout = $this->niceNumberCommission($total_payout);
        $total_payout = $this->DEFAULT_SYMBOL_LEFT . $total_payout . $this->DEFAULT_SYMBOL_RIGHT;
        $ewallet_status = $this->MODULE_STATUS['ewallet_status'];

        $total_amount = 0;
        $requested_amount = 0;
        $total_request = 0;
        $total_released = 0;
        $commission = 0;
        $donation = 0;
        $given_donation = 0;
        $recieved_commission = 0;
        $donation_type = '';
        if ($ewallet_status == 'yes') {
            $total_amount = $this->home_model->getGrandTotalEwallet($user_id);
            $total_amount = $this->DEFAULT_CURRENCY_VALUE * $total_amount;
            $total_amount = $this->niceNumberCommission($total_amount);
            $total_amount = $this->DEFAULT_SYMBOL_LEFT . $total_amount . $this->DEFAULT_SYMBOL_RIGHT;
            $requested_amount = $this->home_model->getTotalRequestAmount($user_id);
            $total_request = $this->home_model->getGrandTotalEwallet($user_id);
            $total_released = $this->home_model->getTotalReleasedAmount($user_id);
         
        }
        
        //income expense profit
        $this->load->model('income_details_model');
        $this->load->model('ewallet_model');
        $user_id = $this->LOG_USER_ID;
        $total_expense = $this->ewallet_model->getuserExpense($user_id);
        $total_income = $this->ewallet_model->getUserIncome($user_id);
        $this->set("amount", $total_income);
        $this->set("expense", $total_expense);

        //commission earned
        $total_commission = $this->home_model->getCommissionDetails($from_date = '', $to_date = '', $user_id);
        $this->set("total_commission", $total_commission);

        //payout pending
        $this->load->model('payout_model');
        $payout_pending = $this->payout_model->getRequestPendingAmount($user_id);
        $this->set("payout_pending", $payout_pending);

        // sales
          $sales = $this->home_model->getTotalSales($from_date = '', $to_date = '', $user_id);
          $this->set("sales", $sales);
        
        //for sales
        $total_sales = $this->home_model->getSalesCount('', '', $user_id);
        $total_sales = $this->niceNumber($total_sales);
        $today_sales = $this->home_model->getSalesCount(date('Y-m-d') . " 00:00:00", date('Y-m-d') . " 23:59:59", $user_id);
        $today_sales = $this->niceNumber($today_sales);

        //mail
        $read_mail = $this->home_model->getTotaMailCount('user', '', '', $read_status = '', 'all');
        $read_mail = $this->niceNumber($read_mail);
        $mail_today = $this->home_model->getAllTodayMessages('user');
        $mail_today = $this->niceNumber($mail_today);

        $package_validity_date = $this->validation_model->getUserProductValidity($user_id);
        $product_id = $this->validation_model->getProductId($user_id);
        if ($product_id === 0) {
            $this->validation_model->hideReactivationMenu();
        }
        $show_package_validity_date = "no";
        $today = date("Y-m-d H:i:s");
        $last_month = $package_validity_date;
        if ($product_id != 0 && $today < $last_month && $this->MODULE_STATUS['subscription_status'] == 'yes' && $this->MODULE_STATUS['product_status'] == 'yes') {
            $show_package_validity_date = "yes";
        }
        $package_validity_date = date("F j, Y, g:i a", strtotime($package_validity_date));
        $this->set("show_package_validity_date", $show_package_validity_date);
        $this->set("package_validity_date", $package_validity_date);
        $this->set("product_id", $product_id);

        //Social Media
        $social_media_info = $this->home_model->getSocialMediaInfo();
        //top 5 recruters
        $top_recruters = $this->home_model->getTopRecruters(7, $this->LOG_USER_ID);
        $j = 0;
        foreach ($top_recruters as $v) {
            if (file_exists(IMG_DIR . "profile_picture/" . $top_recruters[$j]['profile_picture'])) {
                $top_recruters[$j]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/" . $top_recruters[$j]['profile_picture'];
            } else {
                $top_recruters[$j]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/nophoto.jpg";
            }
            $j++;
        }
        //top 5 Earners
        $top_earners = $this->home_model->getTopEarners(4, $this->LOG_USER_ID);

        $i = 0;
        foreach ($top_earners as $v) {
            $top_earners[$i]['balance_amount'] = $this->DEFAULT_SYMBOL_LEFT . number_format($v['balance_amount'] * $this->DEFAULT_CURRENCY_VALUE, 2) . $this->DEFAULT_SYMBOL_RIGHT;
            $top_earners[$i]['profile_picture'] = $this->validation_model->getProfilePicture($v['id']);
            if (file_exists(IMG_DIR . "profile_picture/" . $top_earners[$i]['profile_picture'])) {
                $top_earners[$i]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/" . $top_earners[$i]['profile_picture'];
            } else {
                $top_earners[$i]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/nophoto.jpg";
            }
            $i++;
        }
        //logged User details
        $user_details = $this->validation_model->getUserDetails($this->LOG_USER_ID, $this->LOG_USER_TYPE);
        $user_details['user_name'] = $this->LOG_USER_NAME;
        if($this->MODULE_STATUS['opencart_status'] == "yes" || $this->MODULE_STATUS['opencart_status_demo'] == 'yes') {
            $user_details['membership'] = $this->validation_model->getOpenCartProductNameFromUserID($this->LOG_USER_ID);
        } else {
            $user_details['membership'] = $this->validation_model->getProductNameFromUserID($this->LOG_USER_ID);
        }
      

        // $this->load->model('news_model');
        // $news_arr = $this->news_model->getLatestNews();
        // $this->set("news_arr", count($news_arr));
        

        //Data For World Map
        $map_data = $this->home_model->getCountryMapdata($this->LOG_USER_ID);
        //Data For Progress bar
        $prgrsbar_data = $this->home_model->getPackageProgressData(4, $this->LOG_USER_ID);
        
        $rank_data = $this->home_model->getRankData(4, $this->LOG_USER_ID);

        if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
            $date = date('Y-m-d');
            $rs = $this->joining_model->getJoiningDetailsForBinaryLeftRight($this->LOG_USER_ID);
            $daily_joining = $this->joining_model->getJoiningDetailsForBinaryLeftRight($this->LOG_USER_ID, $date . " 00:00:00", $date . " 23:59:59");
            $joining["joinings_data2"] = $rs['joining'];
            $joining["joinings_data4"] = $rs['joining_right'];
            $joining["joinings_data1"] = $daily_joining['joining'];
            $joining["joinings_data3"] = $daily_joining['joining_right'];
        } else {
            $start_date = date('Y-m-01') . " 00:00:00";
            $end_date = date("Y-m-d 23:59:59", strtotime("+1 month -1 day"));
            $week_end_date = date('Y-m-d') . " 23:59:59";
            $week_start_date = date('Y-m-d', strtotime('last sunday')) . " 00:00:00";

            $joining["joinings_data2"] = $this->home_model->totalJoiningUsers($this->LOG_USER_ID);
            $joining["joinings_data1"] = $this->home_model->todaysJoiningCount($this->LOG_USER_ID);
            $joining["joinings_data4"] = $this->joining_model->getJoiningCountPerMonth($start_date, $end_date, $this->LOG_USER_ID);
            $joining["joinings_data3"] = $this->tree_model->getDownlineUsersCount($this->LOG_USER_ID, 'father', $week_start_date, $week_end_date);
        }

        //////////////////////////////////////////////////////////////////////////////

        $latest_joinees = $this->home_model->getLatestJoinees('user');
        $j = 0;
        foreach ($latest_joinees as $v) {
            if (file_exists(IMG_DIR . "profile_picture/" . $latest_joinees[$j]['profile_pic'])) {
                $latest_joinees[$j]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/" . $latest_joinees[$j]['profile_pic'];
            } else {
                $latest_joinees[$j]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/nophoto.jpg";
            }
            $j++;
        }

        $current_rank = null;
        $rank_criteria = null;
        $next_rank = null;
        $crank = null;
        $nrank = null;
        if ($this->MODULE_STATUS['rank_status'] == 'yes') {
            $this->load->model('rank_model');
            $rank_achievement = $this->rank_model->getRankCriteria($this->LOG_USER_ID);
            $this->set("rank_achievement", $rank_achievement);
            /**
             * Current Rank
             */
            $crank = $this->rank_model->currentRankName($this->LOG_USER_ID);
            if(!empty($crank))  {
                $current_rank = $this->rank_model->getCurrentRankData($this->LOG_USER_ID);
                $rank_criteria = array_keys($current_rank['criteria'], 1);
                $nrank = $this->rank_model->NextRankName($crank['rank_id']);
            } else {
                $nrank = $this->rank_model->NextRankName();
            }
            

            /**
             * Next Rank
             */
            if(!empty($nrank)) {
                $next_rank = $this->rank_model->getNextRankData($nrank['rank_id'],$this->LOG_USER_ID);
            } else {
                $next_rank = null;
            }
            
            $this->set("nrank", $nrank['rank_name'] ?? "");
            $this->set("crank", $crank['rank_name'] ?? "");
            $this->set("rank_criteria", $rank_criteria);
            $this->set("current_rank", $current_rank);
            $this->set("next_rank", $next_rank);
           
        }
       $user_details['full_name'] = $user_details['user_detail_name']." ".$user_details['user_detail_second_name']; 
        if($this->MODULE_STATUS['package_upgrade'] == "yes") {
            $this->load->model('upgrade_model');
            $current_package_details = $this->upgrade_model->getMembershipPackageDetails($this->LOG_USER_ID);
        }
        /** News */
        $news = $this->news_model->getAllNews(3, 0);
        $this->set('news', $news);

        $this->set("prgrsbar_data", $this->security->xss_clean($prgrsbar_data));
        $this->set("rank_data", $rank_data);
        $this->set("map_data", $map_data);
       
        $this->set("total_payout", $total_payout);

        $this->set("total_amount", $total_amount);
        $this->set("site_url", $site_url);
        $this->set("top_recruters", $top_recruters);
        $this->set("top_earners", $top_earners);
        $this->set("user_details", $user_details);

        $this->set('social_media_info', $social_media_info);
        $this->set("latest_joinees", $latest_joinees);

       

        if (DEMO_STATUS == 'yes') {
            $is_preset_demo = $this->validation_model->isPresetDemo($this->ADMIN_USER_ID);
            $this->set('is_preset_demo', $is_preset_demo);
        }
        
        $product_status = $this->MODULE_STATUS['product_status'];
        $pro_file_arr = $this->profile_model->getProfileDetails($this->LOG_USER_ID, $product_status);
        $this->set('user_validity', $pro_file_arr['details']['product_validity']);
        $this->set('profile_photo', $pro_file_arr['details']['profile_photo']);
        if($this->MODULE_STATUS['package_upgrade'] == "yes") {
            $this->load->model('upgrade_model');
            $current_package_details = $this->upgrade_model->getMembershipPackageDetails($this->LOG_USER_ID);
            $upgradable_package_list = $this->upgrade_model->getUpgradablePackageList($current_package_details);
            $this->set('upgradable_package_list', $upgradable_package_list);
        }
        $incomes = $this->home_model->getAllIncomeOrExpense($this->LOG_USER_ID, 'credit', 4);
        $expenses = $this->home_model->getAllIncomeOrExpense($this->LOG_USER_ID, 'debit', 4);
        $payout_statuses = [
            'requested' => $this->payout_model->getUserTotalPayoutRequests($this->LOG_USER_ID, 'pending'),
            'approved' => $this->payout_model->getUserTotalPayouts($this->LOG_USER_ID, 'approved'),
            'paid' => $this->payout_model->getUserTotalPayouts($this->LOG_USER_ID, 'paid'),
            'rejected' => $this->payout_model->getUserTotalPayoutRequests($this->LOG_USER_ID, 'deleted')
        ];
        $this->set('incomes', $incomes);
        $this->set('expenses', $expenses);
        $this->set('payouts', $payout_statuses);


        if($this->MODULE_STATUS['replicated_site_status'] == "no" && $this->MODULE_STATUS['lead_capture_status'] == "no") {
            $individula_details = $this->home_model->individulaDetails($this->LOG_USER_ID, ['personal_pv', 'gpv']);
            $profile_extra_data = [
                'placement_user_name' => $pro_file_arr['details']['father_name'],
                'sponsor_name' => $pro_file_arr['details']['sponsor_name'],
                'personal_pv' => $individula_details->personal_pv == 0 ? 0 : $individula_details->personal_pv,
                'group_pv' => $individula_details->gpv == 0 ? 0 : $individula_details->gpv,
            ];
            $this->set('profile_extra_data', $profile_extra_data);
        }
        // $this->lang->load('amount_type');

        if ($this->MODULE_STATUS['mlm_plan'] == 'Donation') {
            $donation_type = $this->validation_model->getColoumnFromTable("configuration", "donation_type");
            $this->load->model('donation_model');
            $donation_level = $this->donation_model->getLevelName($this->donation_model->getCurrentLevel($user_id));
            $this->set('donation_level', $donation_level);
            $this->set('donation_type', $donation_type);
        }
        $this->set("dashboardConfig", $this->home_model->getUserDashboardConfig());
        
        // $this->setView();
        $this->smarty->view("newui/user/home/dashboard_new.tpl", $this->VIEW_DATA);
    }
    public function ajax_agent_autolist() {
        if ($this->input->is_ajax_request()) {
            $keyword = $this->input->post('keyword', TRUE);
            $data = $this->home_model->getAgentsByKeyword($keyword);
            echo json_encode($data);
            exit();
        }
    }
    public function getBoardDownlines($sponsorUsername){
        
        $url = BOARD_URL.'/user/getdownlines';
        $apiKey = BOARD_API_KEY;
        $data = array(
            'api_key' => $apiKey,
            'sponsor_username' => $sponsorUsername,
        );

        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ),
        );

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            // Handle error
           
        } else {
            // Process the result
            return json_decode($result,true);
        }
        
    }


}
