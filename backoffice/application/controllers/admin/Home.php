<?php

require_once 'Inf_Controller.php';

class Home extends Inf_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('login_model');
        $this->lang->load('ewallet_lang');
    }

    function index() {
                
        $this->lang->load('amount_type', $this->LANG_NAME);
        $this->lang->load('ewallet_lang');
       
        $this->set('from_login', false);
        if (DEMO_STATUS == 'yes' && $this->LOG_USER_TYPE == 'admin') {
            if ($this->session->flashdata('inf_from_login')) {
                $this->set('from_login', true);
            }
        }
        $title = lang('dashboard');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "dashboard";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('dashboard');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('dashboard');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();

        $user_id = $this->LOG_USER_ID;
        $session_data = $this->session->userdata('inf_logged_in');
        $table_prefix = $session_data['table_prefix'];
        $prefix = str_replace('_', '', $table_prefix);
        $site_url = SITE_URL;

        // Top widget
        $ewalletBalance = $this->ewallet_model->getTotalEwalletBalanceOfAllUser();
        $businessWalletDetails = $this->ewallet_model->getBusinessWalletDetails();
        $TotalIncome = 0;
        $bussinessBonus = 0;
        $bussinessPaid = 0;
        $bussinessPending = 0;
        foreach ($businessWalletDetails as $key => $value) {
            if($value['type'] == "income")
                $TotalIncome += $value["amount"];
            if($value['type'] == "commission")
                $bussinessBonus += $value["amount"];
            if($value['type'] == "payout")
                $bussinessPaid += $value["amount"];
            if($value['type'] == "payout_pending")
                $bussinessPending += $value["amount"];
        }
        $this->set("ewalletBalance", $ewalletBalance);
        $this->set("TotalIncome", $TotalIncome);
        $this->set("bussinessBonus", $bussinessBonus);
        $this->set("bussinessPaid", $bussinessPaid);
        $this->set("bussinessPending", $bussinessPending);
        // end :: Top widget

        // bar chart
        $barChartData = $this->home_model->getIncomeBonusBarChartData("month");
        $this->set("barChartData", json_encode($barChartData));
        // end :: bar chart

        // doughnut chart
        $doughnutLabelsArray = [lang("paid"), lang("approved"), lang("pending")];
        $doughnutDataArray = [];
        
        $doughnutDataArray[] = round($bussinessPaid * $this->DEFAULT_CURRENCY_VALUE, $this->PRECISION);
        $doughnutDataViewArray[] = format_currency($bussinessPaid);
        
        $approvedAmountTotal = $this->payout_model->getTotalAmountApproved();
        $doughnutDataArray[] = round($approvedAmountTotal * $this->DEFAULT_CURRENCY_VALUE, $this->PRECISION);
        $doughnutDataViewArray[] = format_currency($approvedAmountTotal);
        
        $pendingAmountTotal = $this->payout_model->getTotalAmountPendingRequest('pending');
        $doughnutDataArray[] = round($pendingAmountTotal * $this->DEFAULT_CURRENCY_VALUE, $this->PRECISION);
        $doughnutDataViewArray[] = format_currency($pendingAmountTotal);
        $this->set("doughnutLabelsArray", json_encode($doughnutLabelsArray));
        $this->set("doughnutDataArray", json_encode($doughnutDataArray));
        $this->set("doughnutDataViewArray", json_encode($doughnutDataViewArray));
        $this->set("piechart", $doughnutDataArray);
        // end :: doughnut chart

        // joininngs
        $admin_id = $this->LOG_USER_ID;
        if ($this->LOG_USER_TYPE == 'employee')
            $admin_id = $this->validation_model->getAdminId();

                    
        // $rs = $this->joining_model->getJoiningDetailsForBinaryLeftRight($admin_id);
        $rs = $this->home_model->getLeftRightPV($admin_id);
        $this->set("leftJoinings", $rs["total_left_carry"]);
        $this->set("rightJoinings", $rs["total_right_carry"]);

        $joiningLineGraphData = $this->home_model->getJoiningLineChartData($admin_id, "month");
        $this->set("joiningLineGraphData", json_encode($joiningLineGraphData));
        // end :: joininngs

        // new joins
        $latest_joinees = $this->home_model->getLatestJoinees();
        $j = 0;
        foreach ($latest_joinees as $v) {
            if (file_exists(IMG_DIR . "profile_picture/" . $latest_joinees[$j]['profile_pic'])) {
                $latest_joinees[$j]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/" . $latest_joinees[$j]['profile_pic'];
            } else {
                $latest_joinees[$j]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/nophoto.jpg";
            }
            $j++;
        }
        $this->set("latest_joinees", $latest_joinees);
        // end :: new joins
        

        $total_amount = 0;
        $requested_amount = 0;
        $released_amount = 0;
        $commission = 0;
        $donation = 0;
        $given_donation = 0;
        $recieved_commission = 0;
        $ewallet_status = $this->MODULE_STATUS['ewallet_status'];
        if ($ewallet_status == 'yes') {
            $requested_amount = $this->home_model->getTotalRequestAmount();
            $released_amount = $this->home_model->getTotalReleasedAmount();
            if ($this->MODULE_STATUS['mlm_plan'] == 'Donation') {
                $commission = $this->home_model->getTotalCommission();
                $commission = $this->DEFAULT_CURRENCY_VALUE * $commission;
                $commission = $this->niceNumberCommission($commission);

                $donation = $this->home_model->getTotalDonation();
                $donation = $this->DEFAULT_CURRENCY_VALUE * $donation;
                $recieved_commission = $donation;
                $donation = $this->niceNumber($donation);
                $this->load->model('donation_model');
                $given_commission = $this->donation_model->givenDonation();
                $recieved_commission *= $this->DEFAULT_CURRENCY_VALUE;
                $donation_level = $this->donation_model->getLevelName($this->donation_model->getCurrentLevel($user_id));
            }
        }
        //for sales
        $total_sales = $this->home_model->getSalesCount();
        $total_sales = $this->niceNumber($total_sales);
        // $total_sales = number_format($total_sales);
        $today_sales = $this->home_model->getSalesCount(date('Y-m-d') . " 00:00:00", date('Y-m-d') . " 23:59:59");
        $today_sales = $this->niceNumber($today_sales);

        //for mail
        $read_mail = $this->home_model->getTotaMailCount('admin', '', '', $read_status = '', 'all');
        $read_mail = $this->niceNumber($read_mail);
        // $read_mail = number_format($read_mail);
        $mail_today = $this->home_model->getAllTodayMessages('admin');
        $mail_today = number_format($mail_today);

        //Social Media        
        $social_media_info = $this->home_model->getSocialMediaInfo();

        //top 5 recruters
        if ($this->LOG_USER_TYPE == 'employee') {
            $admin_id = $this->validation_model->getAdminId();
            $top_recruters = $this->home_model->getTopRecruters(4, $admin_id);
            //top 5 Earners
            $top_earners = $this->home_model->getTopEarners(4);
        } else {
            $top_recruters = $this->home_model->getTopRecruters(4, $user_id);
            //top 5 Earners
            $top_earners = $this->home_model->getTopEarners(100);
        }
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
        //logged User details
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
        $user_details = $this->validation_model->getUserDetails($this->LOG_USER_ID, $this->LOG_USER_TYPE);
        $user_details['user_name'] = $this->LOG_USER_NAME;
        $user_details['membership'] = $this->validation_model->getProductNameFromUserID($this->LOG_USER_ID);

        //Data For World Map        
        $map_data = $this->home_model->getCountryMapdata();

        //Data For Progress bar
        $prgrsbar_data = $this->home_model->getPackageProgressData(6, '');
        if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
            if ($this->LOG_USER_TYPE == 'employee') {
                $admin_id = $this->validation_model->getAdminId();
                $date = date('Y-m-d');
                $rs = $this->joining_model->getJoiningDetailsForBinaryLeftRight($admin_id);
                $daily_joining = $this->joining_model->getJoiningDetailsForBinaryLeftRight($admin_id, $date . " 00:00:00", $date . " 23:59:59");
                $joining["joinings_data2"] = $rs['joining'];
                $joining["joinings_data4"] = $rs['joining_right'];
                $joining["joinings_data1"] = $daily_joining['joining'];
                $joining["joinings_data3"] = $daily_joining['joining_right'];
            } else {
                $date = date('Y-m-d');
                $rs = $this->joining_model->getJoiningDetailsForBinaryLeftRight($this->LOG_USER_ID);
                $daily_joining = $this->joining_model->getJoiningDetailsForBinaryLeftRight($this->LOG_USER_ID, $date . " 00:00:00", $date . " 23:59:59");
                $joining["joinings_data2"] = $rs['joining'];
                $joining["joinings_data4"] = $rs['joining_right'];
                $joining["joinings_data1"] = $daily_joining['joining'];
                $joining["joinings_data3"] = $daily_joining['joining_right'];
            }
        } else {
            $start_date = date('Y-m-01') . " 00:00:00";
            $end_date = date("Y-m-d 23:59:59", strtotime("+1 month -1 day"));
            $week_end_date = date('Y-m-d') . " 23:59:59";
            $week_start_date = date('Y-m-d', strtotime('last sunday')) . " 00:00:00";

            $joining["joinings_data2"] = $this->home_model->totalJoiningUsers();
            $joining["joinings_data1"] = $this->home_model->todaysJoiningCount();
            $joining["joinings_data4"] = $this->joining_model->getJoiningCountPerMonth($start_date, $end_date);
            if ($this->LOG_USER_TYPE == 'employee') {

                $joining["joinings_data3"] = $this->tree_model->getDownlineUsersCount($admin_id, 'father', $week_start_date, $week_end_date);
            } else {

                $joining["joinings_data3"] = $this->tree_model->getDownlineUsersCount($this->LOG_USER_ID, 'father', $week_start_date, $week_end_date);
            }
        }


        //////////////////////////////////////////////////////////////////////////////

        $latest_joinees = $this->home_model->getLatestJoinees();
        $j = 0;
        foreach ($latest_joinees as $v) {
            if (file_exists(IMG_DIR . "profile_picture/" . $latest_joinees[$j]['profile_pic'])) {
                $latest_joinees[$j]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/" . $latest_joinees[$j]['profile_pic'];
            } else {
                $latest_joinees[$j]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/nophoto.jpg";
            }
            $j++;
        }
        $rank_data = $this->home_model->getRankData(4);
        $incomeandCommission = $this->home_model->getincomeAndCommissionDetails();
        $this->lang->load('amount_type', $this->LANG_NAME);
        
        $this->set("incomeandCommission", $incomeandCommission);
        $this->set("rank_data", $rank_data);

        $latest_joinees = $this->home_model->getLatestJoinees();
        $j = 0;
        foreach ($latest_joinees as $v) {
            if (file_exists(IMG_DIR . "profile_picture/" . $latest_joinees[$j]['profile_pic'])) {
                $latest_joinees[$j]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/" . $latest_joinees[$j]['profile_pic'];
            } else {
                $latest_joinees[$j]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/nophoto.jpg";
            }
            $j++;
        }
        $pool_bonus=$this->home_model->getSumTopUserAmountPayable();
        $this->set("pool_bonus",$pool_bonus);
        $this->set("joining_data", $joining);
        $this->set("prgrsbar_data", $this->security->xss_clean($prgrsbar_data));
        $this->set("map_data", $map_data);
        $this->set("requested_amount", $requested_amount);
        $this->set("released_amount", $released_amount);
        $this->set("commission", $commission);
        $this->set("donation", $donation);

        $this->set("total_sales", $total_sales);
        $this->set("today_sales", $today_sales);

        $this->set("read_mail", $read_mail);
        $this->set("mail_today", $mail_today);

        $this->set("pinstatus", "NO");
        $this->set("top_recruters", $top_recruters);
        $this->set("top_earners", $top_earners);
        $this->set("user_details", $user_details);

        $this->set("user_id", $user_id);
        $this->set("table_prefix", $prefix);
        $this->set("site_url", $site_url);

        $this->set('social_media_info', $social_media_info);
        $this->set('total_joinings', $this->home_model->totalJoiningUsers());
        $this->set('today_joinings', $this->home_model->todaysJoiningCount());
        $this->set('rank_promo',$this->home_model->selectRankDetailsPromo());

        if ($this->MODULE_STATUS['mlm_plan'] == 'Donation') {
            $this->set('recieved_commission', $recieved_commission);
            $this->set('given_commission', $given_commission);
            $this->set('level_name', $donation_level);
        }
        
        if (DEMO_STATUS == 'yes') {
            $is_preset_demo = $this->validation_model->isPresetDemo($this->ADMIN_USER_ID);
            $this->set('is_preset_demo', $is_preset_demo);
        }
        
        $this->set('replica_lcp_user_name', $this->LOG_USER_TYPE == "employee" ? $this->ADMIN_USER_NAME : $this->LOG_USER_NAME);
        $current_pool=$next_pool=null;
        $cpool=null;
        $npool=null;
        $this->load->model('rank_model');
        $crank = $this->rank_model->currentRankName($this->LOG_USER_ID);
        $cpool=$this->rank_model->currentPool($this->LOG_USER_ID);
        if(!empty($cpool)){
                $current_pool = $this->rank_model->getCurrentPoolData($this->LOG_USER_ID);
                 $npool = $this->rank_model->NextPromoRank($cpool['id']);
        }else{
             $npool = $this->rank_model->NextPromoRank(0);
        }
       
        if($npool){
             $next_pool = $this->rank_model->getNextPoolData($npool['id'],$this->LOG_USER_ID);
        }
        $promo = $this->validation_model->getConfig(['promo_start_date', 'promo_end_date']);
        $promo_status='no';
        if(date('Y-m-d')>=$promo['promo_start_date'] && date('Y-m-d')<=$promo['promo_end_date']){
            $promo_status='yes';
        }
        $this->set("promo", $promo);
        $this->set("npool", $npool ?? "");
        $this->set("cpool", $cpool ?? "");
        $this->set("current_pool", $current_pool);
        $this->set("next_pool", $next_pool);
        $this->set('promo_status', $promo_status);
        $rank_pool=$this->home_model->getPoolDetails();
        $this->set('rank_pool',$rank_pool);
       // print_r($rank_pool);die;
        if ($this->LOG_USER_TYPE == 'employee') {
            $block_name = [
                'ewallet', 
                'sales',
                'payout', 
                'active_deposit', 
                'matured_deposit', 
                'mail',
                'replica', 
                'lcp', 
                'country_graph', 
                'joinings', 
                'to_do', 
                'top_earners', 
                'social_media', 
                'new_members', 
                'top_recruiters', //new
                'ewallet_balance',
                'total_income',
                'bonus',
                'payout_paid',
                'payout_pending',
                'chart_income_commission',
                'chart_payout',
                'new_members_panel',
                'promotional_tools',
                'team_performance',
                'income_and_commission',
            ];
            $this->load->model('employee_model');
            $permission = $this->employee_model->viewDashboardPermission($this->LOG_USER_NAME);
            $dashboard_menu = explode(",", $permission);
            
            $this->set('dashboard_menu', $dashboard_menu);
            
            $this->set('block_name', $block_name);
            

            $this->setView('admin/home/index_employee');
        } else {
             $this->setView();
        }
        
    }
   
    public function get_notifications() {

        $notifications = $this->home_model->getNotifications();

        echo json_encode($notifications);
        exit();
    }
    
    /* Ajax Functon For Payout Tile */

    function ajax_payout($range) {

        $total_amount = 0;
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
        $total_amount = $this->home_model->getPayoutDetails($start_date, $end_date);
        $total_amount = $total_amount * $this->DEFAULT_CURRENCY_VALUE;
        $total_amount = $this->niceNumberCommission($total_amount);
        // $total_amount = number_format($total_amount, 2);
        $total_amount = $this->DEFAULT_SYMBOL_LEFT . $total_amount . $this->DEFAULT_SYMBOL_RIGHT;
        echo $total_amount;
    }

    /* Ajax Functon For Sales Tile */

    function ajax_sales($range) {
        $total_sales = 0;
        $start_date = '';
        $end_date = '';

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
        }
        if ($range == 'all_sales') {
            $start_date = '';
            $end_date = '';
        }
        $total_sales = $this->home_model->getSalesCount($start_date, $end_date);
        $total_sales = $this->niceNumber($total_sales);
        // $total_sales = number_format($total_sales);
        echo $total_sales;
    }

    /* Ajax Functon For Mail Tile */

    function ajax_mail($range) {
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
        $count_mail_total = $this->home_model->getMailCount('admin', $start_date, $end_date, $read_status = '');
        $count_mail_unread = $this->home_model->getMailCount('admin', $start_date, $end_date, $read_status = 'no');

        if ($range == 'all_mail') {
            $count_mail_total = $this->home_model->getMailCount('admin', $start_date, $end_date, $read_status = '', 'all');
            $count_mail_unread = $this->home_model->getAllUnreadMessages('admin');
        }
        $count_mail_total = $this->niceNumber($count_mail_total);
        // $count_mail_total = number_format($count_mail_total);
        $count_mail_unread = $this->niceNumber($count_mail_unread);
        // $count_mail_unread = number_format($count_mail_unread);
        $result = array('mail_total' => $count_mail_total, 'mail_unread' => $count_mail_unread);
        echo json_encode($result);
    }

    /* Ajax Functon For Joining In Chart */

    function ajax_joinings_chart($range) {
        $this->load->model('joining_model');
        $this->load->model('tree_model');
        $rs = [];
        $type = 'father';
        $i = 0;
        if ($range == 'monthly_joining_graph') {
            $monthly_joining = $this->home_model->getJoiningDetailsperMonth();

            if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
                if ($this->LOG_USER_TYPE == 'employee') {
                    $admin_id = $this->validation_model->getAdminId();
                    $monthly_joining = $this->joining_model->getJoiningDetailsperMonthLeftRight($admin_id);
                } else {
                    $monthly_joining = $this->joining_model->getJoiningDetailsperMonthLeftRight($this->LOG_USER_ID);
                }
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
                    if ($this->LOG_USER_TYPE == 'employee') {
                        $admin_id = $this->validation_model->getAdminId();
                        $yearly_joining = $this->joining_model->getJoiningDetailsForBinaryLeftRight($admin_id, $start_date, $end_date);
                        $rs[$i]["y"] = $yearly_joining['joining'];
                        $rs[$i]["z"] = $yearly_joining['joining_right'];
                    } else {
                        $yearly_joining = $this->joining_model->getJoiningDetailsForBinaryLeftRight($this->LOG_USER_ID, $start_date, $end_date);
                        $rs[$i]["y"] = $yearly_joining['joining'];
                        $rs[$i]["z"] = $yearly_joining['joining_right'];
                    }
                } else {
                    $rs[$i]["y"] = $this->joining_model->getJoiningCountPerMonth($start_date, $end_date);
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
                    if ($this->LOG_USER_TYPE == 'employee') {
                        $admin_id = $this->validation_model->getAdminId();
                        $daily_joining = $this->joining_model->getJoiningDetailsForBinaryLeftRight($admin_id, $start_date . " 00:00:00", $start_date . " 23:59:59");
                    } else {
                        $daily_joining = $this->joining_model->getJoiningDetailsForBinaryLeftRight($this->LOG_USER_ID, $start_date . " 00:00:00", $start_date . " 23:59:59");
                    }
                    $rs[$i]["y"] = $daily_joining['joining'];
                    $rs[$i]["z"] = $daily_joining['joining_right'];
                } else {
                    $rs[$i]["y"] = $this->joining_model->getJoiningCountPerMonth($start_date . " 00:00:00", $start_date . " 23:59:59");
                }
                $start_date = date('Y-m-d', strtotime($start_date . ' +1 day'));
                $i++;
            }
        }
        echo json_encode($rs);
    }

    public function add_todo() {
        $title = lang('configure_todo');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "dashboard";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = $title;
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = $title;
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $todo_list = $this->home_model->getToDoList($this->LOG_USER_ID);

        $this->setView();
    }

    public function edit_todo() {
        $title = lang('configure_todo');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "dashboard";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = $title;
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = $title;
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();


        $id = $this->input->post('id', TRUE);
        $admin_id = $this->validation_model->getAdminId();
        $todo_list = $this->home_model->getToDoList('', $id, '');
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

    public function delete_todo() {
        $id = $this->input->post('tsk_id', TRUE);
        $res = $this->home_model->deleteToDoList($this->LOG_USER_ID, $id);
        if ($res) {
            $data_array['details'] = $res;
            $data = serialize($data_array);

            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Deleted Task in To-do list', $this->LOG_USER_ID, $data);

            if ($this->LOG_USER_TYPE == 'employee') {
                $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'Deleted Task in To-do list', 'Deleted Task in To-do list');
            }
            $this->redirect(lang('deleted_task_successfully'), 'home/index', TRUE);
        } else {
            $this->redirect(lang('failed_to_delete'), 'home/index', FALSE);
        }
    }

    public function change_todo() {
        $id = $this->input->post('id', TRUE);
        $status = $this->input->post('status', TRUE);
        $res = $this->home_model->ChangeToDoStatus($this->LOG_USER_ID, $id, $status);
        if ($res) {
            $data_array['details'] = $res;
            $data = serialize($data_array);
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Status Changed for Task in To-do list', $this->LOG_USER_ID, $data);
            if ($this->LOG_USER_TYPE == 'employee') {
                $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'Status Changed for Task in To-do list', 'Status Changed for Task in To-do list');
            }
        }
    }

    public function validate_todo_list() {
        $this->lang->load('validation');
        $this->form_validation->set_rules('task', lang("task"), 'trim|required|max_length[250]', [
            'required' => sprintf(lang('required'), lang('task')),
            'max_length' => sprintf(lang('maxlength'), lang('task'), "250")
        ]);
        $this->form_validation->set_rules('task_time', lang("time"), 'trim|required|valid_time|callback_validate_date', [
            'required' => sprintf(lang('required_select'), 'time'),
            'valid_time' => lang('valid_time'),
        ]);
        $this->form_validation->set_rules('task_date', lang("date"), 'trim|required|valid_date|callback_validate_date', [
            'required' => sprintf(lang('required_select'), lang('date')),
            'valid_date' => lang('valid_date'),
        ]);
        $validation_result = $this->form_validation->run();
        return $validation_result;
    }

    public function validate_addlist() {
        $json = array();
        if ($this->validate_todo_list()) {
            $post_arr = $this->validation_model->stripTagsPostArray($this->input->post(NULL, TRUE));
            $user_id = $this->LOG_USER_ID;

            $time = date("H:i:s", strtotime($post_arr['task_time']));
            $date = date('y-m-d', strtotime($post_arr['task_date']));
            $start_time = ($date . ' ' . $time);

            $res = $this->home_model->addToDoList($post_arr['task'], $start_time, $user_id);
            if ($res) {
                $json['success'] = lang("task_added_to_to_do_list_successfully");
                $data = serialize($json);
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, lang("task_added_to_to_do_list_successfully"), $this->LOG_USER_ID, $data);
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, lang("task_added_to_to_do_list_successfully"), lang("task_added_to_to_do_list_successfully"));
                }
            } else {
                $json['error']['warning'] = lang("an_error_occurred_followup_date_could_not_be_updated");
            }
        } else {
            $json['error'] = $this->form_validation->error_array();
        }
        echo json_encode($json);
        exit();
    }

    public function validate_edit_todo() {
        $json = array();
        if ($this->validate_todo_list()) {
            $post_arr = $this->validation_model->stripTagsPostArray($this->input->post(NULL, TRUE));
            $user_id = $this->LOG_USER_ID;
            $time = date("H:i:s", strtotime($post_arr['task_time']));
            $date = date('y-m-d', strtotime($post_arr['task_date']));
            $start_time = ($date . ' ' . $time);
            if ($this->LOG_USER_TYPE == 'admin') {
                $res = $this->home_model->updateToDoList($post_arr['task'], $start_time, '', $post_arr['task_id']);
            } else {
                $res = $this->home_model->updateToDoList($post_arr['task'], $start_time, $user_id, $post_arr['task_id']);
            }

            if ($res) {
                $json['success'] = lang("task_edited_to_to_do_list_successfully");
            } else {
                $json['error']['warning'] = lang("an_error_occurred_followup_date_could_not_be_updated");
            }
        } else {
            $json['error'] = $this->form_validation->error_array();
        }
        echo json_encode($json);
        exit();
    }

    public function validate_date() {
        $post_arr = $this->validation_model->stripTagsPostArray($this->input->post(NULL, TRUE));
        $date = strtotime($post_arr['task_date'] . ' ' . $post_arr['task_time']);
        $current_date = strtotime(date('Y-m-d H:i:s'));
        if ($date < $current_date) {
            $this->form_validation->set_message('validate_date', lang("task_date_cannot_be_less_than_the_current_date"));
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /* Package List - Progressbar Starts */

    public function package_list() {
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
        $base_url = base_url() . 'admin/home/package_list';
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
        $total_count = $this->home_model->getTotalPackageCount();
        $product_details = $this->product_model->getPackageListForProgressbar($package_type, "", $config['per_page'], $page);
        foreach ($product_details as $v) {
            $product_details[$i]['perc'] = ($total_count * (int) $this->home_model->getPackageCount($v['prod_id']));
            $i++;
        }

        
        $this->set('product_details', $this->security->xss_clean($product_details));
        
        $this->set('page', $page);
        $this->setView();
    }

    /* Package List - Progressbar Ends */

    function niceNumber($n) {
        // first strip any formatting;
        $n = (0 + str_replace(",", "", $n));

        // is this a number?
        if (!is_numeric($n))
            return false;

        // now filter it;
        if ($n > 1000000000000)
            return round(($n / 1000000000000), 2) . ' T';
        elseif ($n > 1000000000)
            return round(($n / 1000000000), 2) . ' B';
        elseif ($n > 1000000)
            return round(($n / 1000000), 2) . ' M';

        return number_format($n);
    }

    function niceNumberCommission($n) {
        // first strip any formatting;
        $n = (0 + str_replace(",", "", $n));

        // is this a number?
        if (!is_numeric($n))
            return false;

        // now filter it;
        if ($n > 1000000000000)
            return round(($n / 1000000000000), 2) . ' T';
        elseif ($n > 1000000000)
            return round(($n / 1000000000), 2) . ' B';
        elseif ($n > 1000000)
            return round(($n / 1000000), 2) . ' M';
    //    elseif ($n > 1000) return round(($n / 1000), 2) . ' K';

        return number_format($n, 2);
    }

    public function roi_details() {
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
        $base_url = base_url() . "admin/home/roi_details";
        $config['base_url'] = $base_url;
        $config['per_page'] = $this->PAGINATION_PER_PAGE;
        if ($this->uri->segment(4) != "") {
            $page = $this->uri->segment(4);
        } else {
            $page = 0;
        }
        $total_amount = $this->home_model->getHyipTotalLegamount();
        $roi_details = array();
        $roi_details = $this->home_model->getHyipTotalLegAmountDetails($page, $config['per_page']);
        $total_rows = $this->home_model->getReturnInvestmentDetailsCount();
        $this->set("roi_details", $this->security->xss_clean($roi_details));
        $config['total_rows'] = $total_rows;
        $this->set('details_count', $total_rows);
        $this->pagination->initialize($config);
        $this->set("total_amount", $total_amount);
        
        
        $this->set('page_id', $page);
        $this->setView();
    }

    public function active_deposit() {
        $title = lang('active_deposit');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $help_link = 'active_deposit';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('active_deposit');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('active_deposit');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        
        $config = $this->pagination->customize_style();
        $base_url = base_url() . 'admin/home/active_deposit';
        $config['base_url'] = $base_url;
        $config['per_page'] = $this->PAGINATION_PER_PAGE;

        $total_active_amount = $this->home_model->getActiveDeposit();
        $active_deposit = array();
        $details = $this->home_model->getActiveDepositDetails();
        $total_rows = count($details);
        
        $config['total_rows'] = $total_rows;
        $config['uri_segment'] = 4;
        
        $this->pagination->initialize($config);
        $page = 0;
        if ($this->uri->segment(4) != '') {
            $page = $this->uri->segment(4);
        }
        

        if (!$page){
            if($config['per_page'] > $total_rows)
                $limit = $total_rows;
            else {
                $limit = $config['per_page'];    
            }
        }  
        else {
            $page_end_limit = $page + $config['per_page'];
            $limit = $page_end_limit;
            if($limit > $total_rows) 
                $limit = $total_rows;
        }   

        for ($i = $page; $i < $limit; $i++) {
            $active_deposit[] = $details[$i];
        }
        
        $this->set("active_deposit", $active_deposit);
        $this->set("page", $page);
        
        $this->set('details_count', $total_rows);
        $this->set("total_amount", $total_active_amount);
        $this->setView();
    }

    public function matured_deposit() {

        $title = lang('matured_deposit');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $help_link = 'matured_deposit';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('matured_deposit');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('matured_deposit');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();


        $config = $this->pagination->customize_style();
        $base_url = base_url() . 'admin/home/matured_deposit';
        $config['base_url'] = $base_url;
        $config['per_page'] = $this->PAGINATION_PER_PAGE;

        $total_deposit = $this->home_model->getHyipTotalLegAmount();
        $total_active_deposit = $this->home_model->getActiveDeposit();
        $total_matured_deposit = $total_deposit - $total_active_deposit;

        $matured_deposit = array();
        $details = $this->home_model->getMaturedDepositDetails();
        $total_rows = count($details);

        $config['total_rows'] = $total_rows;
        $config['uri_segment'] = 4;

        $this->pagination->initialize($config);
        $page = 0;
        if ($this->uri->segment(4) != '') {
            $page = $this->uri->segment(4);
        }
        

        if (!$page){
            if($config['per_page'] > $total_rows)
                $limit = $total_rows;
            else {
                $limit = $config['per_page'];    
            }
        }  
        else {
            $page_end_limit = $page + $config['per_page'];
            $limit = $page_end_limit;
            if($limit > $total_rows) 
                $limit = $total_rows; 
        }
        for ($i = $page; $i < $limit; $i++) {
            $matured_deposit[] = $details[$i];
        }

        $this->set("matured_deposit", $matured_deposit);
        $this->set("page", $page);
        
        $this->set('details_count', $total_rows);
        $this->set("total_matured_deposit", $total_matured_deposit);
        $this->setView();
    }

    public function ajax_users_autolist() {
        if ($this->input->is_ajax_request()) {
            $keyword = $this->input->post('keyword', TRUE);
            $data = $this->home_model->getUsersByKeyword($keyword);
            echo json_encode($data);
            exit();
        }
    }

    public function ajax_except_admin_autolist() {
        if ($this->input->is_ajax_request()) {
            $keyword = $this->input->post('keyword', TRUE);
            $data = $this->home_model->getUsersByKeyword($keyword, 'admin');
            echo json_encode($data);
            exit();
        }
    }

    public function change_default_language() {
        if ($this->input->is_ajax_request()) {
            $this->load->model('multi_language_model');
            $language = $this->input->post('language', TRUE);
            $res = $this->multi_language_model->setUserDefaultLanguage($language, $this->LOG_USER_ID);
            if ($res) {
                echo 'yes';
            } else {
                echo 'no';
            }
            exit();
        }
    }

    public function update_theme_setting()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post(NULL, TRUE);

            //$theme_type=$this->input->post('theme_type');
            //print_r($data);die;
            
            
            $response = $this->validation_model->updateThemeSetting($data);
            echo $response;
            exit();
            
        }
    }
   // public function update_theme_user_setting()
   //  {
   //      if ($this->input->is_ajax_request()) {
   //          $data = $this->input->post(null, true);
   //          $response = $this->validation_model->updateThemeSetting($data);
   //          echo $response;
   //          exit();
   //      }
   //  }

    public function get_theme_details()
    {

     $theme_type=$this->input->post('theme_type');
     
     $theme=$this->validation_model->getThemeSettingsByAllUser($theme_type); 
     echo $theme;
      

    }


    public function get_user_dashbord_data() {
        $daterange = $this->input->get('filter') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));

        $income = 0;
        $bonus_value = 0;
        $payout_paid = 0;
        $business_data = $this->ewallet_model->getBusinessWalletDetails($from_date, $to_date);
        foreach ($business_data as $key => $value) {
            if($value['type'] == "income") {
                $income += $value['amount'];
            }
            if($value['type'] == "commission") {
                $bonus_value += $value['amount'];
            }
            if($value['type'] == "payout") {
                $payout_paid += $value["amount"];
            }
        }
        
        echo json_encode([
                'income' => thousands_currency_format($income),
                'bonus_value' => thousands_currency_format($bonus_value),
                'payout_paid' => thousands_currency_format($payout_paid)
            ]);
    }

    public function get_user_dashbord_graph_data() {
        if($this->input->get('graph') == "income_commission") {
            $barChartData = $this->home_model->getIncomeBonusBarChartData($this->input->get('filter'));
            echo json_encode(['bar_chart_data' => $barChartData]);
        } elseif($this->input->get('graph') == "joinings") {
            $admin_id = $this->LOG_USER_ID;
            if ($this->LOG_USER_TYPE == 'employee')
                $admin_id = $this->validation_model->getAdminId();
            $joiningLineGraphData = $this->home_model->getJoiningLineChartData($admin_id, $this->input->get('filter'));
            echo json_encode(['joining_graph_data' => $joiningLineGraphData]);
        }

    }

    public function user_search()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->inf_model->getUsersByKeyword($this->input->get('term', TRUE));
            echo json_encode($data);
            exit();
        }
    }

    public function ajax_countrywise_user_autolist($country_id='')
    {
        if ($this->input->is_ajax_request()) {
            $keyword = $this->input->post('keyword', TRUE);

            $data = $this->home_model->getUsersByKeywordByCountry($keyword,$country_id);
            echo json_encode($data);
            exit();
        }
    }

    public function ajax_agent_autolist() {
        if ($this->input->is_ajax_request()) {
            $keyword = $this->input->post('keyword', TRUE);
            $data = $this->home_model->getAgentsByKeyword($keyword);
            echo json_encode($data);
            exit();
        }
    }
     function agent_home() {
        $this->lang->load('amount_type', $this->LANG_NAME);
        $this->lang->load('ewallet_lang');
       
        $this->set('from_login', false);
        if (DEMO_STATUS == 'yes' && $this->LOG_USER_TYPE == 'admin') {
            if ($this->session->flashdata('inf_from_login')) {
                $this->set('from_login', true);
            }
        }
        $title = lang('dashboard');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "dashboard";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('dashboard');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('dashboard');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();

        $user_id = $this->LOG_USER_ID;
        $session_data = $this->session->userdata('inf_logged_in');
        $table_prefix = $session_data['table_prefix'];
        $prefix = str_replace('_', '', $table_prefix);
        $site_url = SITE_URL;

        // Top widget
        $ewalletBalance = $this->validation_model->getAgentWalletBalance($this->LOG_USER_ID);
        $TotalIncome = 0;
        $bussinessBonus = 0;
        $bussinessPaid = 0;
        $bussinessPending = 0;
        $TotalIncome=$this->ewallet_model->getTotalCommissionEarnedAgent($this->LOG_USER_ID);
        $this->set("ewalletBalance", $ewalletBalance);
        $this->set("TotalIncome", $TotalIncome);
        $this->set("bussinessBonus", $bussinessBonus);
        $this->set("bussinessPaid", $bussinessPaid);
        $this->set("bussinessPending", $bussinessPending);
        // end :: Top widget

        $this->setView();
        
    }
    public function ajax_agent_autolist_new() {
        if ($this->input->is_ajax_request()) {
            $keyword = $this->input->post('keyword', TRUE);
            $data = $this->home_model->getAgentsByKeyword($keyword);
            echo json_encode($data);
            exit();
        }
    }
    

}
