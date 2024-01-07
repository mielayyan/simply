<?php

require_once 'Inf_Controller.php';

class Home extends Inf_Controller
{

    public $display_tree = "";

    public function __construct()
    {
        parent::__construct();
        $this->load->model("android/new/android_model");
        $this->load->model('Api_model');
        $this->load->model('home_model');
        $this->load->model('joining_model');
        $this->load->model('profile_model');
        $this->load->model('rank_model');
        $this->load->model('validation_model');
        $this->LOG_USER_ID = $this->rest->user_id;
        $this->LOG_USER_TYPE = 'user';
    }

    public function get_details()
    {
        $is_loggin = $this->LOG_USER_ID;
        if (!$is_loggin) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_login_details');
        } else {
            $json_response['status'] = true;
            $json_response['message'] = lang('logged_in');
            $user_name = $this->validation_model->IdToUserName($is_loggin);
            $json_response['password_md5'] = $this->validation_model->getUserPassword($user_name);
            $json_response['data'] = $this->Api_model->getUserDetails($this->LOG_USER_ID);
        }

        echo json_encode($json_response);
        exit();
    }

    public function get_module_status()
    {

        $is_loggin = $this->LOG_USER_ID;
        if (!$is_loggin) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_login_details');
        } else {
            $json_response['status'] = true;
            $json_response['message'] = lang('logged_in');
            if (count($this->MODULE_STATUS)) {
                $json_response['data'] = $this->MODULE_STATUS;
            }

            $json_response['data'] = $this->inf_model->trackModule();

            if ($this->MODULE_STATUS['ticket_system_status_demo'] == "yes" && $this->MODULE_STATUS['ticket_system_status'] == "yes") {
                $json_response['data']['ticket_system_mobile'] = "yes";
            } else {
                $json_response['data']['ticket_system_mobile'] = "no";
            }

            if ($this->MODULE_STATUS['repurchase_status_demo'] == "yes" && $this->MODULE_STATUS['repurchase_status'] == "yes") {
                $json_response['data']['repurchase_status_mobile'] = "yes";
            } else {
                $json_response['data']['repurchase_status_mobile'] = "no";
            }

            if ($this->MODULE_STATUS['pin_status_demo'] == "yes" && $this->MODULE_STATUS['pin_status'] == "yes") {
                $json_response['data']['pin_mobile'] = "yes";
            } else {
                $json_response['data']['pin_mobile'] = "no";
            }

            if ($this->MODULE_STATUS['opencart_status_demo'] == "yes" && $this->MODULE_STATUS['opencart_status'] == "yes") {
                $json_response['data']['opencart_mobile'] = "yes";
            } else {
                $json_response['data']['opencart_mobile'] = "no";
            }

            if ($this->MODULE_STATUS['lang_status_demo'] == "yes" && $this->MODULE_STATUS['lang_status'] == "yes") {
                $json_response['data']['lang_mobile'] = "yes";
            } else {
                $json_response['data']['lang_mobile'] = "no";
            }

            if ($this->MODULE_STATUS['multy_currency_status_demo'] == "yes" && $this->MODULE_STATUS['multy_currency_status'] == "yes") {
                $json_response['data']['multy_currency_mobile'] = "yes";
            } else {
                $json_response['data']['multy_currency_mobile'] = "no";
            }

            if ($this->MODULE_STATUS['lead_capture_status_demo'] == "yes" && $this->MODULE_STATUS['lead_capture_status'] == "yes") {
                $json_response['data']['lead_capture_mobile'] = "yes";
            } else {
                $json_response['data']['lead_capture_mobile'] = "no";
            }

            if ($this->MODULE_STATUS['replicated_site_status_demo'] == "yes" && $this->MODULE_STATUS['replicated_site_status'] == "yes") {
                $json_response['data']['replicated_site_mobile'] = "yes";
            } else {
                $json_response['data']['replicated_site_mobile'] = "no";
            }

            if ($this->MODULE_STATUS['package_upgrade_demo'] == "yes" && $this->MODULE_STATUS['package_upgrade'] == "yes") {
                $json_response['data']['package_upgrade_mobile'] = "yes";
            } else {
                $json_response['data']['package_upgrade_mobile'] = "no";
            }

            $username_config = $this->configuration_model->getUsernameConfig();
            $json_response['data']['user_name_type'] = $username_config["type"];

            $signup_settings = $this->configuration_model->getGeneralSignupConfig();

            $json_response['data']['registration_allowed'] = $signup_settings['registration_allowed'];
            $json_response['data']['sponsor_required'] = $signup_settings['sponsor_required'];
            $json_response['data']['bank_info_required'] = $signup_settings['bank_info_required'];

        }
        echo json_encode($json_response);
        exit();
    }
    // language change
    public function set_default_language_post()
    {
        $this->lang->load('multi_language', $this->LANG_NAME);
        if ($this->MODULE_STATUS['lang_status'] == 'yes') {
            $lang_id = $this->post('lang_id');
            //validation required for unwated laguage id
            if ($lang_id) {
                $user_id = $this->LOG_USER_ID;
                $this->load->model('multi_language_model');
                $res = $this->multi_language_model->setUserDefaultLanguage($lang_id, $user_id);
                if ($res) {
                    $data_array['lang_id'] = $lang_id;
                    $var = serialize($data_array);
                    $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'default language updated', $this->LOG_USER_ID, $var);
                    $msg = lang('default_language_updated');
                } else {
                    $this->set_error_response(200, 1030);
                }

                $data['response'] = $msg;
            } else {
                $data['response'] = $this->form_validation->error_array();
            }
            $this->set_success_response(200, $data);
        }
        $this->set_error_response(200, 1029);
    }

    //chenge currency
    public function set_default_currency_post(){
        if($this->MODULE_STATUS['multy_currency_status'] =='yes' ||$this->MODULE_STATUS['multy_currency_status_demo'] =='yes'){
            $currency_id = $this->post('currency');
            if($currency_id){
                $user_id = $this->LOG_USER_ID;
                $this->load->model('api_model');
                if($this->api_model->changeDefaultCurrency($user_id,$currency_id)){
                    $this->set_success_response(200);
                }else{
                    $this->set_error_response(422);
                }
            }else{
                    $this->set_error_response(422);
            }
        }
    }

    public function validate_language_details()
    {
        $this->form_validation->set_rules('lang_id', lang('language'), 'trim|required');

        $validation_status = $this->form_validation->run();
        return $validation_status;
    }

    public function dashboard_tile_post()
    {
        $this->lang->load('home', $this->LANG_NAME);
        $user_id = $this->LOG_USER_ID;

        switch ($this->post('range')) {
            case "monthly_payout":
                $start_date = date('Y-m-01', strtotime('this month')) . " 00:00:00";
                $end_date = date('Y-m-t', strtotime('this month')) . " 23:59:59";
                break;

            case "yearly_payout":
                $start_date = date('Y-01-01') . " 00:00:00";
                $end_date = date('Y-12-31') . " 23:59:59";
                break;

            case "weekly_payout":
                $tomorrow = date("Y-m-d", time() + 86400);
                $start_date = date('Y-m-d', strtotime('last Sunday', strtotime($tomorrow))) . " 00:00:00";
                $end_date = date('Y-m-d') . " 23:59:59";
                break;

            default:
                $start_date = '';
                $end_date = '';
                break;
        }

        $data = [];
        $dashboardConfig = $this->home_model->getUserDashboardConfig();
        if ($dashboardConfig["ewallet"] == 1) {
            $ewallet = $this->home_model->getGrandTotalEwallet($user_id);
            $data[] = [
                'amount' => $ewallet,
                'withcurrency' => format_currency($ewallet),
                'text' => 'ewallet',
                'title' => lang('e_wallet'),
                'to' => '/ewallet',
                'test' => lang('pagination_first_link'),
            ];

        }
        if ($dashboardConfig["commission_earned"] == 1) {
            $commision = $this->home_model->getCommissionDetails($start_date, $end_date, $user_id);
            $data[] = [
                'amount' => $commision,
                'withcurrency' => format_currency($commision),
                'text' => 'commision',
                'title' => lang('commision_earned'),
                'to' => '/ewallet',
            ];
        }
        if ($dashboardConfig["payout_released"] == 1) {
            $payout_released = $this->home_model->getPayoutDetails($start_date, $end_date, $user_id);
            $data[] = [
                'amount' => $payout_released,
                'withcurrency' => format_currency($payout_released),
                'text' => 'payoutRelease',
                'title' => lang('payout_released'),
                'to' => '/ewallet',
            ];
        }
        if ($dashboardConfig["payout_pending"] == 1) {
            $payout_pending = $this->payout_model->getRequestPendingAmount($user_id);
            $data[] = [
                'amount' => $payout_pending,
                'withcurrency' => format_currency($payout_pending),
                'text' => 'payoutPending',
                'title' => lang('payout_pending'),
                'to' => '/ewallet',
            ];
        }
        $this->set_success_response(200, $data);
    }

    public function user_dashboard_get()
    {
        $this->lang->load('home', $this->LANG_NAME);
        $user_id = $this->LOG_USER_ID;
        if (!$user_id) {
            $data['status'] = false;
            $data['message'] = lang('inv_user');
        } else {
            $user_details = $this->validation_model->getUserDetails($this->LOG_USER_ID, $this->LOG_USER_TYPE);
            $dashboardConfig = $this->home_model->getUserDashboardConfig();
            $current_rank = $this->rank_model->currentRankName($this->LOG_USER_ID);
            $individula_details = $this->home_model->individulaDetails($this->LOG_USER_ID, ['personal_pv', 'gpv']);
            $site_url = SITE_URL;
            $binary_tree = $this->home_model->individulaDetails($this->LOG_USER_ID, ['total_left_carry', 'total_right_carry']);

            // $product_status = $this->$MODULE_STATUS['product_status'];
            $pro_file_arr = $this->profile_model->getProfileDetails($this->LOG_USER_ID, $this->MODULE_STATUS['product_status']);

            //profile
            if (file_exists(IMG_DIR . "profile_picture/" . $pro_file_arr['details']['profile_photo'])) {
                $profile_picture_full = SITE_URL . "/uploads/images/profile_picture/" . $pro_file_arr['details']['profile_photo'];
            } else {
                $profile_picture_full = SITE_URL . "/uploads/images/profile_picture/nophoto.png";
            }
            if ($dashboardConfig["profile_membership_replica_lcp"] == 1) {

                $data['profile'] = [
                    'full_name' => $user_details['user_detail_name'] . " " . $user_details['user_detail_second_name'],
                    'user_name' => $this->validation_model->IdToUserName($this->LOG_USER_ID),
                    'user_photo' => $profile_picture_full,
                ];

            }
            //
            //
            if ($this->MODULE_STATUS['product_status'] == "yes" && $dashboardConfig["profile_membership_replica_lcp"] == 1) {
                if ($this->MODULE_STATUS['opencart_status'] == "yes" || $this->MODULE_STATUS['opencart_status_demo'] == 'yes') {
                    $membership = $this->validation_model->getOpenCartProductNameFromUserID($this->LOG_USER_ID);
                } else {
                    $membership = $this->validation_model->getProductNameFromUserID($this->LOG_USER_ID);
                }

                $data['profile']['membership_package'] = [
                    'title' => lang('current_package'),
                    'name' => $membership,
                ];

                $store_id = "";
                if ($this->MODULE_STATUS['package_upgrade'] == "yes") {
                    $data['profile']['membership_package']['upgrade_link'] = '';
                    if ($this->MODULE_STATUS['opencart_status'] == "yes") {
                        if (DEMO_STATUS == 'yes') {
                            $store_id = "&id=" . str_replace("_", "", $this->db->dbprefix);
                        }
                        $data['profile']['membership_package']['upgrade_link'] = SITE_URL . '/store/index.php?route=upgrade/upgrade' . $store_id;
                    }
                }
            }
            //
            //

            if ($this->MODULE_STATUS['product_status'] == "yes" && $dashboardConfig["profile_membership_replica_lcp"] == 1 && $this->MODULE_STATUS['subscription_status'] == 'yes') {
                $store_id = "";
                $data['profile']['membership_package']['product_validity'] = [
                    'title' => lang('membership_will_expire'),
                    'date' => $pro_file_arr['details']['product_validity'] ?? '',
                    'status' => true,
                ];
                if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                    $data['profile']['membership_package']['product_validity']['renewal_link'] = '';
                    if ($this->MODULE_STATUS['opencart_status'] == "yes") {
                        if (DEMO_STATUS == 'yes') {
                            $store_id = "&id=" . str_replace("_", "", $this->db->dbprefix);
                        }
                        $data['profile']['membership_package']['product_validity']['renewal_link'] = SITE_URL . '/store/index.php?route=renewal/renewal' . $store_id;
                    }
                }
            }

            // rank
            $rank_configuration = $this->configuration_model->getRankConfiguration();
            if ($this->MODULE_STATUS['rank_status'] == "yes" && $rank_configuration['joinee_package'] != 1 && $dashboardConfig["rank"] == 1 && $dashboardConfig["profile_membership_replica_lcp"] == 1) {
                $data['profile']['rank'] = [
                    'title' => lang('rank'),
                    'curent_rank' => $current_rank['rank_name'] ?? lang('na'),
                ];
            }
            //end of rank

            //extra data in dashboard

            // personal pv and group pv
            if ($dashboardConfig["pv"] == 1) {
                if ($this->IS_MOBILE) {
                    $data['extra_data']['sponsor'] = [
                        'head' => $pro_file_arr['details']['sponsor_name'],
                        'text' => 'sponsorName',
                        'title' => lang('sponsor_name'),
                    ];
                    $data['extra_data']['pv']['personal'] = [
                        'head' => (int) $individula_details->personal_pv,
                        'title' => lang('personal'),
                    ];
                    $data['extra_data']['pv']['group'] = [
                        'head' => (int) $individula_details->gpv,
                        'title' => lang('group'),
                    ];
                } else {
                    $data['extra_data'][] = [
                        'head' => $pro_file_arr['details']['sponsor_name'],
                        'text' => 'sponsorName',
                        'title' => lang('sponsor_name'),

                    ];
                    $data['extra_data'][] = [
                        'head' => (int) $individula_details->personal_pv,
                        'text' => 'personalPv',
                        'title' => lang('personal_pv'),
                    ];
                    $data['extra_data'][] = [
                        'head' => (int) $individula_details->gpv,
                        'text' => 'groupPV',
                        'title' => lang('group_pv'),
                    ];
                }
            }
            //end of personal pv and group pv

            // binary plane
            if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
                if ($this->IS_MOBILE) {
                    $data['extra_data']['carry']['leftCarry'] = [
                        'head' => (int) $binary_tree->total_left_carry,
                        'text' => 'leftCarry',
                        'title' => lang('total_left_carry'),
                    ];
                    $data['extra_data']['carry']['rightCarry'] = [
                        'head' => (int) $binary_tree->total_right_carry,
                        'text' => 'rightCarry',
                        'title' => lang('total_right_carry'),
                    ];
                } else {
                    $data['extra_data'][] = [
                        'head' => (int) $binary_tree->total_left_carry,
                        'text' => 'leftCarry',
                        'title' => lang('total_left_carry'),
                    ];
                    $data['extra_data'][] = [
                        'head' => (int) $binary_tree->total_right_carry,
                        'text' => 'rightCarry',
                        'title' => lang('total_right_carry'),
                    ];
                }
            }

            //end of binary plane

            //end of extra data in dashboard

            $admin = $this->validation_model->getAdminUsername();
            // replica link
            // $replica= [
            //     'status' =>$this->MODULE_STATUS['replicated_site_status']=="yes"?true:false,
            //     'title' =>lang('replica_link'),
            // ];
            if ($this->MODULE_STATUS['replicated_site_status'] == "yes" && $dashboardConfig["profile_membership_replica_lcp"] == 1) {
                if ($this->IS_MOBILE) {
                    $data['profile']['replica']['title'] = lang('replica_link');
                    if (DEMO_STATUS == 'yes') {

                        $replica_link = $site_url . "/replica/" . $admin . "/" . $this->validation_model->IdToUserName($user_id);

                    } else {

                        $replica_link = $site_url . "/replica/" . $this->validation_model->IdToUserName($user_id);
                    }
                    $data['profile']['replica']['copy_link'] = [
                        'icon' => 'files-o',
                        'link' => $replica_link,
                    ];
                    $data['profile']['replica']['shared_link'][] = [
                        'icon' => 'facebook',
                        'colour' => '#4267B2',
                        'link' => "https://www.facebook.com/sharer/sharer.php?u=" . $replica_link,
                    ];
                    $data['profile']['replica']['shared_link'][] = [
                        'icon' => 'twitter',
                        'colour' => '#1DA1F2',
                        'link' => "https://twitter.com/share?url=" . $replica_link,
                    ];
                    $data['profile']['replica']['shared_link'][] = [
                        'icon' => 'linkedin',
                        'colour' => '#0072b1',
                        'link' => "http://www.linkedin.com/shareArticle?url=" . $replica_link,
                    ];
                } else {
                    $data['profile']['replica_title'] = lang('replica_link');
                    if (DEMO_STATUS == 'yes') {

                        $replica_link = $site_url . "/replica/" . $admin . "/" . $this->validation_model->IdToUserName($user_id);

                    } else {

                        $replica_link = $site_url . "/replica/" . $this->validation_model->IdToUserName($user_id);
                    }
                    $data['profile']['replica'][] = [
                        'icon' => 'fa fa-files-o',
                        'link' => $replica_link,
                    ];
                    $data['profile']['replica'][] = [
                        'icon' => 'fa fa-facebook',
                        'link' => "https://www.facebook.com/sharer/sharer.php?u=" . $replica_link,
                    ];
                    $data['profile']['replica'][] = [
                        'icon' => 'fa fa-twitter',
                        'link' => "https://twitter.com/share?url=" . $replica_link,
                    ];
                    $data['profile']['replica'][] = [
                        'icon' => 'fa fa-linkedin',
                        'link' => "http://www.linkedin.com/shareArticle?url=" . $replica_link,
                    ];
                }

            }
            //end replica link

            // lead capture
            // $lead_capture= [
            //     'status' =>$this->MODULE_STATUS['lead_capture_status']=="yes"?true:false,
            //     'title' =>lang('replica_link'),
            // ];
            if ($this->MODULE_STATUS['lead_capture_status'] == "yes" && $dashboardConfig["profile_membership_replica_lcp"] == 1) {
                if ($this->IS_MOBILE) {
                    $data['profile']['lead_capture']['title'] = lang('lead_capture');
                    if (DEMO_STATUS == 'yes') {
                        $lead_capture = $site_url . "/lcp/" . $admin . "/" . $this->validation_model->IdToUserName($user_id);
                    } else {

                        $lead_capture = $site_url . "/lcp/" . $this->validation_model->IdToUserName($user_id);

                    }
                    $data['profile']['lead_capture']['copy_link'] = [
                        'icon' => 'files-o',
                        'link' => $lead_capture,
                    ];
                    $data['profile']['lead_capture']['shared_link'][] = [
                        'icon' => 'facebook',
                        'colour' => '#4267B2',
                        'link' => "https://www.facebook.com/sharer/sharer.php?u=" . $lead_capture,
                    ];
                    $data['profile']['lead_capture']['shared_link'][] = [
                        'icon' => 'twitter',
                        'colour' => '#1DA1F2',
                        'link' => "https://twitter.com/share?url=" . $lead_capture,
                    ];
                    $data['profile']['lead_capture']['shared_link'][] = [
                        'icon' => 'linkedin',
                        'colour' => '#0072b1',
                        'link' => "http://www.linkedin.com/shareArticle?url=" . $lead_capture,
                    ];
                } else {
                    $data['profile']['lead_capture_title'] = lang('lead_capture');
                    if (DEMO_STATUS == 'yes') {
                        $lead_capture = $site_url . "/lcp/" . $admin . "/" . $this->validation_model->IdToUserName($user_id);
                    } else {

                        $lead_capture = $site_url . "/lcp/" . $this->validation_model->IdToUserName($user_id);

                    }
                    $data['profile']['lead_capture'][] = [
                        'icon' => 'fa fa-files-o',
                        'link' => $lead_capture,
                    ];
                    $data['profile']['lead_capture'][] = [
                        'icon' => 'fa fa-facebook',
                        'link' => "https://www.facebook.com/sharer/sharer.php?u=" . $lead_capture,
                    ];
                    $data['profile']['lead_capture'][] = [
                        'icon' => 'fa fa-twitter',
                        'link' => "https://twitter.com/share?url=" . $lead_capture,
                    ];
                    $data['profile']['lead_capture'][] = [
                        'icon' => 'fa fa-linkedin',
                        'link' => "http://www.linkedin.com/shareArticle?url=" . $lead_capture,
                    ];
                }
            }
            //end of lead capture

            $this->set_success_response(200, $data);
        }
    }

    // listing of latest member in dashboard
    public function latest_members_get()
    {
        $this->lang->load('home', $this->LANG_NAME);
        $dashboardConfig = $this->home_model->getUserDashboardConfig();
        $latest_joinees = $this->home_model->getLatestJoinees('user');
        $j = 0;
        foreach ($latest_joinees as $v) {
            $latest_joinees[$j]['product_amount_with_currency'] = format_currency($latest_joinees[$j]['product_amount']);
            if (!file_exists(IMG_DIR . "profile_picture/" . $latest_joinees[$j]['profile_pic'])) {
                $latest_joinees[$j]['profile_pic'] = 'nophoto.png';
            }
            if ($this->IS_MOBILE) {
                $latest_joinees[$j]['profile_pic'] = SITE_URL . "/uploads/images/profile_picture/" . $latest_joinees[$j]['profile_pic'];
            } else {
                $latest_joinees[$j]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/" . $latest_joinees[$j]['profile_pic'];
            }
            $j++;
        }
        $data['new_members_status'] = $dashboardConfig["new_members"] == 1 ? true : false;
        if ($dashboardConfig["new_members"] == 1) {
            $data['new_members'] = $latest_joinees;
        }

        /**
         * Current Rank
         */

        if ($this->MODULE_STATUS['rank_status'] == "yes") {
            $crank = $this->rank_model->currentRankName($this->LOG_USER_ID);
            if (!empty($crank)) {
                $current_rank = $this->rank_model->getCurrentRankData($this->LOG_USER_ID);
                $rank_criteria = array_keys($current_rank['criteria'], 1);
                //
                $current_ranks = [];
                foreach ($rank_criteria as $criteria) {
                    if ($criteria != 'downline_package_count' && $criteria != 'downline_rank' && $criteria != "joinee_package") {
                        $criteria = $criteria == "downline_count" ? "downline_member_count" : $criteria;
                        if ($criteria == "referal_count") {
                            $text = "referralCount";
                            $title = lang('referal_count');
                        } else if ($criteria == "personal_pv") {
                            $text = "personalPv";
                            $title = lang('personal_pv');
                        } else if ($criteria == "group_pv") {
                            $text = "groupPV";
                            $title = lang('group_pv');
                        } else {
                            $text = "downlineMemberCount";
                            $title = lang('downline_member_count');
                        }
                        $current_rank[$criteria]['text'] = $text;
                        $current_rank[$criteria]['title'] = $title;
                        $current_ranks['criteria'][] = $current_rank[$criteria];

                    }

                }
                $current_ranks['criteria'][0]['percentage'] = round($current_ranks['criteria'][0]['percentage'], 1);
                $data['rank']['current'] = $current_ranks; //current rank criteria array
                $data['rank']['current']['name'] = $crank['rank_name']; // current rank name
                //
                $nrank = $this->rank_model->NextRankName($crank['rank_id']);
            } else {
                $current_rank = null;
                $nrank = $this->rank_model->NextRankName();
                $rank_criteria = null;
            }

            /**
             * Next Rank
             */

            if (!empty($nrank)) {
                $next_rank = $this->rank_model->getNextRankData($nrank['rank_id'], $this->LOG_USER_ID);
                $next_ranks = [];
                foreach ($next_rank as $key => $value) {
                    if ($key != 'downline_package_count' && $key != 'downline_rank' && $key != "joinee_package") {
                        if ($key == "referal_count") {
                            $text = "referralCount";
                            $title = lang('referal_count');
                        } else if ($key == "personal_pv") {
                            $text = "personalPv";
                            $title = lang('personal_pv');
                        } else if ($key == "group_pv") {
                            $text = "groupPV";
                            $title = lang('group_pv');
                        } else {
                            $text = "downlineMemberCount";
                            $title = lang('downline_member_count');
                        }
                        $key = ($key == "downline_count") ? "downline_member_count" : $key;
                        $next_rank[$key]['text'] = $text;
                        $next_rank[$key]['title'] = $title;
                        $next_ranks['criteria'][] = $next_rank[$key];

                    }
                }
                $next_ranks['criteria'][0]['percentage'] = round($next_ranks['criteria'][0]['percentage'], 1);
                $data['rank']['next'] = $next_ranks;
                $data['rank']['next']['name'] = $nrank['rank_name'];
            } else {
                $next_rank = null;
            }
        }

        /**
         * Earnings & Expenses
         */
        if ($dashboardConfig["earnings_nd_expenses"] == 1) {
            /**
             * Earnings
             */
            if ($dashboardConfig["earnings"] == 1) {
                $income = $this->home_model->getAllIncomeOrExpense($this->LOG_USER_ID, 'credit', 4);
                foreach ($income as $key => $value) {
                    $type = $value['amount_type'];
                    if ($type == "rank_bonus") {
                        $type = "rankcommission";
                    } elseif ($type == "level_commission") {
                        $type = "levelCommission";
                    } elseif ($type == 'leg') {
                        $type = "binaryCommission";
                    } elseif ($type == "referral") {
                        $type = "referralCommission";
                    }
                    $income[$key]['amount_type'] = $type;
                    $income[$key]['title'] = strtoupper(lang($type));
                    if ($this->IS_MOBILE) {
                        $income[$key] = [
                            'title' => strtoupper(lang($type)),
                            'colour' => '#27c24c',
                            'data' => format_currency($income[$key]['amount']),
                        ];
                    }
                }
                if ($this->IS_MOBILE) {
                    $data['earnings_nd_expenses'][] = [
                        "title" => lang('Earnings'),
                        "code" => "earnings",
                        "data" => $income,
                    ];
                } else {
                    $data['earnings_nd_expenses']['incomes'] = $income;
                }
            }
            /**
             * Expenses
             */
            if ($dashboardConfig["expenses"] == 1) {
                if ($this->IS_MOBILE) {
                    $expenses_array = $this->home_model->getAllIncomeOrExpense($this->LOG_USER_ID, 'debit', 4);
                    for ($i = 0; $i < count($expenses_array); $i++) {
                        $amount = format_currency($expenses_array[$i]['amount']);
                        $title = lang($expenses_array[$i]['amount_type']);
                        if ($this->IS_MOBILE) {
                            $expenses_array[$i] = [
                                'title' => $title,
                                'colour' => '#27c24c',
                                'data' => $amount,
                            ];
                        }
                    }
                    $data['earnings_nd_expenses'][] = [
                        "title" => lang('expenses'),
                        "code" => "expenses",
                        "data" => $expenses_array,
                    ];
                } else {
                    $data['earnings_nd_expenses']['expenses'] = $this->home_model->getAllIncomeOrExpense($this->LOG_USER_ID, 'debit', 4);
                }
            }
            /**
             * Payout
             */
            if ($dashboardConfig["payout_status"] == 1) {
                if ($this->IS_MOBILE) {
                    $data['earnings_nd_expenses'][] = [
                        "title" => lang('payout_status'),
                        "code" => "payout_status",
                        "data" => [
                            [
                                'title' => lang('requested'),
                                'colour' => '#000000',
                                'data' => format_currency($this->payout_model->getUserTotalPayoutRequests($this->LOG_USER_ID)),
                            ],
                            [
                                'title' => lang('approved'),
                                'colour' => '#7266ba',
                                'data' => format_currency($this->payout_model->getUserTotalPayouts($this->LOG_USER_ID, 'approved')),
                            ],
                            [
                                'title' => lang('paid'),
                                'colour' => '#27c24c',
                                'data' => format_currency($this->payout_model->getUserTotalPayouts($this->LOG_USER_ID, 'paid')),
                            ],
                            [
                                'title' => lang('rejected'),
                                'colour' => '#f05050',
                                'data' => format_currency($this->payout_model->getUserTotalPayouts($this->LOG_USER_ID, 'rejected')),
                            ],
                        ],
                    ];
                } else {
                    $data['earnings_nd_expenses']['payout_statuses'] = [
                        'requested' => (float) $this->payout_model->getUserTotalPayoutRequests($this->LOG_USER_ID),
                        'approved' => (float) $this->payout_model->getUserTotalPayouts($this->LOG_USER_ID, 'approved'),
                        'paid' => (float) $this->payout_model->getUserTotalPayouts($this->LOG_USER_ID, 'paid'),
                        'rejected' => (float) $this->payout_model->getUserTotalPayouts($this->LOG_USER_ID, 'rejected'),
                    ];
                }
            }
        }
        /**
         * End Of Earnings & Expenses
         */

        /**
         * Team Perfomance
         */
        if ($dashboardConfig["team_perfomance"] == 1) {
            /**
             * Top Rerned user
             */
            if ($dashboardConfig["top_earners"] == 1) {
                //top 5 Earners
                $top_earners = $this->home_model->getTopEarners(4, $this->LOG_USER_ID);
                $i = 0;
                $top_earners_mobile_view = [];
                foreach ($top_earners as $v) {
                    $profile_picture = $this->validation_model->getProfilePicture($v['id']);
                    if (!file_exists(IMG_DIR . "profile_picture/" . $profile_picture)) {
                        $profile_picture = "nophoto.png";
                    }
                    if ($this->IS_MOBILE) {
                        $top_earners_mobile_view[] = [
                            'profile_picture' => SITE_URL . "/uploads/images/profile_picture/" . $profile_picture,
                            'user_name' => $top_earners[$j]['user_name'],
                            'full_name' => $top_earners[$j]['user_detail_name'] . ' ' . $top_earners[$j]['user_detail_second_name'],
                            'data' => format_currency($top_earners[$i]['balance_amount']),
                        ];
                    } else {
                        $top_earners[$i]['profile_picture'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/" . $profile_picture;
                    }
                    $i++;
                }
                if ($this->IS_MOBILE) {
                    $data['team_perfomance'][] = [
                        'title' => lang('top_earners'),
                        'code' => "top_earners",
                        'data' => $top_earners_mobile_view,
                    ];
                } else {
                    $data['team_perfomance']['top_earners'] = $top_earners;
                }
            }
            /**
             * Top Rerned user
             */
            if ($dashboardConfig["top_recruiters"] == 1) {
                $top_recruters = $this->home_model->getTopRecruters(7, $this->LOG_USER_ID);
                $j = 0;
                $top_recruters_mobile_view = [];
                foreach ($top_recruters as $v) {
                    if (!file_exists(IMG_DIR . "profile_picture/" . $top_recruters[$j]['profile_picture'])) {
                        $top_recruters[$j]['profile_picture'] = "nophoto.png";
                    }
                    if ($this->IS_MOBILE) {
                        $top_recruters_mobile_view[] = [
                            'profile_picture' => SITE_URL . "/uploads/images/profile_picture/" . $top_recruters[$j]['profile_picture'],
                            'user_name' => $top_recruters[$j]['user_name'],
                            'full_name' => $top_recruters[$j]['user_detail_name'] . ' ' . $top_recruters[$j]['user_detail_second_name'],
                            'data' => $top_recruters[$j]['count'],
                        ];
                    } else {
                        $top_recruters[$j]['profile_picture'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/" . $top_recruters[$j]['profile_picture'];
                    }
                    $j++;
                }
                if ($this->IS_MOBILE) {
                    $data['team_perfomance'][] = [
                        'title' => lang('top_recruiters'),
                        'code' => "top_recruiters",
                        'data' => $top_recruters_mobile_view,
                    ];
                } else {
                    $data['team_perfomance']['top_recruters'] = $top_recruters;
                }
            }
            /**
             * package Overview
             */
            if ($this->MODULE_STATUS['product_status'] == "yes") {
                if ($dashboardConfig["package_overview"] == 1) {
                    //Data For Progress bar
                    $prgrsbar_data = $this->home_model->getPackageProgressData(4, $this->LOG_USER_ID);
                    if ($this->IS_MOBILE) {
                        $package_overview_mobile = [];
                        foreach ($prgrsbar_data as $dt) {
                            $package_overview_mobile[] = [
                                'text1' => $dt['package_name'],
                                'text2' => lang('you_have') . " {$dt['joining_count']} {$dt['package_name']} " . lang('package_purchases_in_your_team'),
                            ];
                        }
                        $data['team_perfomance'][] = [
                            'title' => lang('package_overview'),
                            'code' => "package_overview",
                            'data' => $package_overview_mobile,
                        ];
                    } else {
                        $data['team_perfomance']['package_overview'] = $prgrsbar_data;
                    }
                }
            }
            /**
             * Rank Overview
             */
            if ($this->MODULE_STATUS['rank_status'] == "yes") {
                if ($dashboardConfig["rank_overview"] == 1) {
                    $rank_data = $this->home_model->getRankData(4, $this->LOG_USER_ID);
                    if ($this->IS_MOBILE) {
                        $rank_data_mobile = [];
                        foreach ($rank_data as $dt) {
                            $rank_data_mobile[] = [
                                'text1' => $dt['rank_name'],
                                'text2' => lang('you_have') . " {$dt['count']} {$dt['rank_name']} " . lang('rank_in_your_team'),
                            ];
                        }
                        $data['team_perfomance'][] = [
                            "title" => lang('rank_overview'),
                            "code" => "rank_overview",
                            "data" => $rank_data_mobile,
                        ];
                    } else {
                        $data['team_perfomance']['rank_overview'] = $rank_data;
                    }
                }
            }
        }
        /**
         * End of Team Perfomance
         */

        $this->set_success_response(200, $data);

    }
    //end of  listing of latest member in dashboard

    //member joining graph

    public function latest_member_joining_get()
    {
        $this->lang->load('home', $this->LANG_NAME);
        $data = [];
        $labels = [];
        $leftJoinData = [];
        $rightJoinData = [];
        $dashboardConfig = $this->home_model->getUserDashboardConfig();
        // if ($dashboardConfig["member_joinings"] != 1) {
        //     $this->set_success_response(200, []);
        // }
        $range = $this->get('range') ?? 'monthly_joining_graph';
        $this->load->model('joining_model');
        $rs = [];
        $i = 0;
        $chartMode = "month";
        if ($range == 'yearly_joining_graph') {
            $chartMode = "year";
        } elseif ($range == 'daily_joining_graph') {
            $chartMode = "day";
        }

        $join_data = $this->home_model->getJoiningLineChartData($this->LOG_USER_ID, $chartMode);

        if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
            $data['rightJoinDataTitle'] = lang('right_join');
            $data['rightJoinDataColour'] = '#189ec8';
            $data['rightJoinData'] = $join_data['rightJoinArray'];
            $data['leftJoinDataTitle'] = lang('left_join');
            $data['leftJoinDataColour'] = '#7265ba';
            $data['leftJoinData'] = $join_data['leftJoinArray'];
        } else {
            $data['leftJoinDataTitle'] = lang('joinings');
            $data['leftJoinDataColour'] = '#7265ba';
            $data['leftJoinData'] = $join_data['joinArray'];
        }
        $data['labels'] = $join_data['labels'];
        $this->set_success_response(200, $data);
    }

    //end of member joining graph

    public function get_username_config()
    {

        $is_loggin = $this->LOG_USER_ID;
        if (!$is_loggin) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_login_details');
        } else {
            $json_response['status'] = true;
            $json_response['message'] = lang('logged_in');
            $json_response['data'] = $this->configuration_model->getUsernameConfig();
            $json_response['data']['user_name'] = $this->LOG_USER_NAME;
        }

        echo json_encode($json_response);
        exit();
    }

    public function get_user_income_details()
    {
        $user_id = $this->LOG_USER_ID;

        if (!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
        } else {
            $limit = $this->input->post('limit');

            if ($this->input->post('offset') != "") {
                $offset = $this->input->post('offset');
            } else {
                $offset = 0;
            }

            $json_response['status'] = true;
            $json_response['message'] = lang('success');
            $json_response['data'] = $this->Api_model->getIncome($user_id, $offset, $limit);
        }

        echo json_encode($json_response);
        exit();
    }

    public function get_user_released_income()
    {
        $user_id = $this->LOG_USER_ID;
        $json_response = array();

        if (!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
            echo json_encode($json_response);
            exit();
        } else {
            $this->load->model('payout_model');
            $limit = $this->input->post('limit');
            if ($this->input->post('offset') != "") {
                $offset = $this->input->post('offset');
            } else {
                $offset = 0;
            }

            $json_response['status'] = true;
            $json_response['message'] = lang('success');
            $json_response['available_balance'] = $this->payout_model->getUserBalanceAmount($user_id);
            $json_response['data'] = $this->Api_model->getIncomeStatement($user_id, $offset, $limit);
            echo json_encode($json_response);
            exit();
        }
    }
    public function get_user_ewallet_details()
    {
        $user_id = $this->LOG_USER_ID;

        if (!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
        } else {
            $this->load->model('ewallet_model');

            $limit = $this->input->post('limit');

            if ($this->input->post('offset') != "") {
                $page = $this->input->post('offset');
            } else {
                $page = 0;
            }

            $json_response['status'] = true;
            $json_response['message'] = lang('success');
            $json_response['bal_amount'] = $this->ewallet_model->getBalanceAmount($user_id);
            $json_response['data'] = $this->Api_model->getEwalletHistoryForMobile($user_id, $page, $limit);
        }
        echo json_encode($json_response);
        exit();
    }

    public function set_api_key()
    {
        $is_loggin = $this->LOG_USER_ID;
        $post_array = $this->input->post();
        $key = $post_array['key'];
        if (!$is_loggin) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
        } else {
            $json_response['status'] = true;
            $json_response['message'] = lang('key_updated');
            $json_response['data'] = $this->validation_model->getInsertIndividualApiKey($this->LOG_USER_ID, $key);
        }
        echo json_encode($json_response);
        exit();
    }

    public function get_state_from_country()
    {
        $this->load->model('country_state_model');
        $is_loggin = $this->LOG_USER_ID;
        $post_array = $this->input->post();
        $country_id = $post_array['country_id'];
        if (!$is_loggin) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_login_details');
        } else {
            $json_response['status'] = true;
            $json_response['message'] = lang('success');
            $json_response['data'] = $this->Api_model->getStatesFromCountry($country_id);
        }

        echo json_encode($json_response);
        exit();
    }

    public function get_user_leg_details()
    {
        $is_loggin = $this->LOG_USER_ID;
        $this->load->model('leg_count_model');
        $product_status = $this->MODULE_STATUS['product_status'];
        $this->leg_count_model->initialize($product_status);
        $user_id = $this->LOG_USER_ID;
        $user_type = $this->LOG_USER_TYPE;
        $arr = array();
        $numrows = $result_per_page = 0;
        if (!$is_loggin) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
        } else {
            $base_url = base_url() . 'user/leg_count/view_leg_count';
            $config = $this->pagination->customize_style();
            $config['base_url'] = $base_url;
            $config['per_page'] = $this->input->post('limit');
            // $config['per_page'] = $this->PAGINATION_PER_PAGE;
            if ($this->input->post('offset') != "") {
                $page = $this->input->post('offset');
            } else {
                $page = 0;
            }

            $json_response['status'] = true;
            $json_response['message'] = lang('success');
            $json_response['data'] = $this->leg_count_model->getUserLegDetails($user_id, $page, $config['per_page'], $user_type, '', $mobile = 1);
        }

        echo json_encode($json_response);
        exit();
    }
    public function change_default_currency()
    {
        $is_loggin = $this->LOG_USER_ID;
        $post_array = $this->input->post();
        $c_code = $post_array['c_code'];
        if (!$is_loggin) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_login_details');
        } else {
            if (!$post_array['c_code']) {
                $json_response['status'] = false;
                $json_response['message'] = lang('select_country');
            } else {
                $valid = $this->validation_model->checkCurrencyCode($c_code);
                if (count($valid) == 0) {
                    $json_response['status'] = false;
                    $json_response['message'] = lang('inv_currency');
                } else {
                    $c_id = $valid['id'];
                    $status = $this->validation_model->changeDefaultCurrency($is_loggin, $c_id);
                    $json_response['status'] = true;
                    $json_response['message'] = lang('currency_changed');
                    $json_response['data'] = $valid;
                }
            }
        }
        echo json_encode($json_response);
        exit();
    }

    public function get_dynamic_dashboard()
    {
        $user_id = $this->LOG_USER_ID;
        $post_array = $this->input->post();
        $type = $post_array['type'];
        $range = $post_array['range'];

        if (!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_login_details');
        } else {
            if ($type == 'sales') {
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

                $total_sales = $this->home_model->getSalesCount($start_date, $end_date, $user_id);
                $total_sales = $this->niceNumber($total_sales);

                $json_response['status'] = true;
                $json_response['message'] = lang('success');
                $json_response['data']['total'] = $total_sales;

            } else if ($type == 'payout') {
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
                $total_amount = $this->home_model->getPayoutDetails($start_date, $end_date, $user_id);
                $total_amount = $total_amount * $this->DEFAULT_CURRENCY_VALUE;
                $total_amount = $this->niceNumberCommission($total_amount);
                $total_amount = $this->DEFAULT_SYMBOL_LEFT . $total_amount . $this->DEFAULT_SYMBOL_RIGHT;

                $json_response['status'] = true;
                $json_response['message'] = lang('success');
                $json_response['data']['total'] = $total_amount;

            } else if ($type == 'mail') {

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

                $result = array('total' => $count_mail_total, 'mail_unread' => $count_mail_unread);
                $json_response['status'] = true;
                $json_response['message'] = lang('success');
                $json_response['data'] = $result;
            } else if ($type == 'ewallet') {

                $total_amount = $this->home_model->getGrandTotalEwallet($user_id);
                $total_amount = $this->DEFAULT_CURRENCY_VALUE * $total_amount;
                $total_amount = $this->niceNumberCommission($total_amount);
                $total_amount = $this->DEFAULT_SYMBOL_LEFT . $total_amount . $this->DEFAULT_SYMBOL_RIGHT;

                $json_response['status'] = true;
                $json_response['message'] = 'success';
                $json_response['data']['total'] = $total_amount;
            } else if ($type == 'joining_chart') {
                $this->load->model('joining_model');
                $rs = array();
                $i = 0;
                if ($range == 'monthly_joining_graph') {
                    $monthly_joining = $this->home_model->getJoiningDetailsperMonth($this->LOG_USER_ID);

                    if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
                        $monthly_joining = $this->joining_model->getJoiningDetailsperMonthLeftRight($this->LOG_USER_ID);
                    }
                    foreach ($monthly_joining as $value) {
                        $rs["cord"][$i]["x"] = $value['month'];
                        $rs["cord"][$i]["y"] = $value['joining'];
                        if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
                            $rs["cord"][$i]["z"] = $value['joining_right'];
                        }
                        $i++;
                    }
                }
                if ($range == 'yearly_joining_graph') {
                    while ($i <= 5) {
                        $j = 5 - $i;
                        $start_date = date('Y-01-01', strtotime("-$j year")) . " 00:00:00";
                        $end_date = date('Y-12-31', strtotime("-$j year")) . " 23:59:59";
                        $rs["cord"][$i]["x"] = intval($start_date);
                        if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
                            $yearly_joining = $this->joining_model->getJoiningDetailsForBinaryLeftRight($this->LOG_USER_ID, $start_date, $end_date);
                            $rs["cord"][$i]["y"] = $yearly_joining['joining'];
                            $rs["cord"][$i]["z"] = $yearly_joining['joining_right'];
                        } else {
                            $rs["cord"][$i]["y"] = $this->joining_model->getJoiningCountPerMonth($start_date, $end_date, $this->LOG_USER_ID);
                        }
                        $i++;
                    }
                }
                if ($range == 'daily_joining_graph') {
                    $start_date = date('Y-m-01');
                    $end_date = date('Y-m-t', strtotime('this month'));
                    while ($start_date <= $end_date) {
                        $rs["cord"][$i]["x"] = date('d', strtotime($start_date));

                        if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
                            $daily_joining = $this->joining_model->getJoiningDetailsForBinaryLeftRight($this->LOG_USER_ID, $start_date . " 00:00:00", $start_date . " 23:59:59");
                            $rs["cord"][$i]["y"] = $daily_joining['joining'];
                            $rs["cord"][$i]["z"] = $daily_joining['joining_right'];
                        } else {
                            $rs["cord"][$i]["y"] = $this->joining_model->getJoiningCountPerMonth($start_date . " 00:00:00", $start_date . " 23:59:59", $this->LOG_USER_ID);
                        }
                        $start_date = date('Y-m-d', strtotime($start_date . ' +1 day'));
                        $i++;
                    }
                }
                $json_response['status'] = true;
                $json_response['message'] = lang('success');
                $json_response['data'] = $rs;
            } else {
                $json_response['status'] = false;
                $json_response['message'] = lang('partial_content');
            }
        }
        echo json_encode($json_response);
        exit();
    }

    public function get_dynamic_dashboard_new()
    {
        $is_loggin = $this->LOG_USER_ID;

        if (!$is_loggin) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_login_details');
        } else {

            $json_response['status'] = true;
            $json_response['message'] = lang('success');

            //SALES STARTS
            $json_response['data']['sales']['today'] = $this->home_model->getSalesCount(date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59'));
            //MONTHLY
            $start_date = date('Y-m-01', strtotime('this month')) . " 00:00:00";
            $end_date = date('Y-m-t', strtotime('this month')) . " 23:59:59";
            $json_response['data']['sales']['monthly'] = $this->home_model->getSalesCount($start_date, $end_date);

            //YEARLY
            $start_date = date('Y-01-01') . " 00:00:00";
            $end_date = date('Y-12-31') . " 23:59:59";
            $json_response['data']['sales']['yearly'] = $this->home_model->getSalesCount($start_date, $end_date);

            //WEEKLY
            $start_date = date('Y-m-d', strtotime('last sunday')) . " 00:00:00";
            $end_date = date('Y-m-d') . " 23:59:59";
            $json_response['data']['sales']['weekly'] = $this->home_model->getSalesCount($start_date, $end_date);

            //ALL
            $start_date = '';
            $end_date = '';
            $json_response['data']['sales']['all'] = $this->home_model->getSalesCount($start_date, $end_date);
            //SALES ENDS

            //PAYOUT STARTS
            $today_amount = $this->home_model->getPayoutDetails(date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59'));
            $today_amount = $today_amount * $this->DEFAULT_CURRENCY_VALUE;

            $json_response['data']['payout']['today'] = $today_amount;

            //MONTHLY
            $start_date = date('Y-m-01', strtotime('this month')) . " 00:00:00";
            $end_date = date('Y-m-t', strtotime('this month')) . " 23:59:59";
            $total_amount = $this->home_model->getPayoutDetails($start_date, $end_date);
            $total_amount = $total_amount * $this->DEFAULT_CURRENCY_VALUE;

            $json_response['data']['payout']['monthly'] = $total_amount;

            //YEARLY
            $start_date = date('Y-01-01') . " 00:00:00";
            $end_date = date('Y-12-31') . " 23:59:59";
            $total_amount = $this->home_model->getPayoutDetails($start_date, $end_date);
            $total_amount = $total_amount * $this->DEFAULT_CURRENCY_VALUE;
            $json_response['data']['payout']['yearly'] = $total_amount;

            //WEEKLY
            $start_date = date('Y-m-d', strtotime('last sunday')) . " 00:00:00";
            $end_date = date('Y-m-d') . " 23:59:59";
            $total_amount = $this->home_model->getPayoutDetails($start_date, $end_date);
            $total_amount = $total_amount * $this->DEFAULT_CURRENCY_VALUE;
            $json_response['data']['payout']['weekly'] = $total_amount;

            //ALL
            $start_date = '';
            $end_date = '';
            $total_amount = $this->home_model->getPayoutDetails($start_date, $end_date);
            $total_amount = $total_amount * $this->DEFAULT_CURRENCY_VALUE;
            $json_response['data']['payout']['all'] = $total_amount;
            //PAYOUT ENDS

            //MAIL STARTS
            $json_response['data']['mail']['today'] = $this->home_model->getMailCount('user', date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59'), $read_status = '', 'all');
            //MONTHLY
            $start_date = date('Y-m-01', strtotime('this month')) . " 00:00:00";
            $end_date = date('Y-m-t', strtotime('this month')) . " 23:59:59";
            $json_response['data']['mail']['monthly']['total'] = $this->home_model->getMailCount('user', $start_date, $end_date, $read_status = '');
            $json_response['data']['mail']['monthly']['unread'] = $this->home_model->getMailCount('user', $start_date, $end_date, $read_status = 'no');

            //YEARLY
            $start_date = date('Y-01-01') . " 00:00:00";
            $end_date = date('Y-12-31') . " 23:59:59";
            $json_response['data']['mail']['yearly']['total'] = $this->home_model->getMailCount('user', $start_date, $end_date, $read_status = '');
            $json_response['data']['mail']['yearly']['unread'] = $this->home_model->getMailCount('user', $start_date, $end_date, $read_status = 'no');

            //WEEKLY
            $start_date = date('Y-m-d', strtotime('last sunday')) . " 00:00:00";
            $end_date = date('Y-m-d') . " 23:59:59";
            $json_response['data']['mail']['weekly']['total'] = $this->home_model->getMailCount('user', $start_date, $end_date, $read_status = '');
            $json_response['data']['mail']['weekly']['unread'] = $this->home_model->getMailCount('user', $start_date, $end_date, $read_status = 'no');

            //ALL
            $json_response['data']['mail']['all']['total'] = $this->home_model->getMailCount('user', $start_date, $end_date, $read_status = '', 'all');
            $json_response['data']['mail']['all']['unread'] = $this->home_model->getAllUnreadMessages('user');
            //MAIL ENDS

            //EWALLET STARTS
            $user_id = $this->LOG_USER_ID;

            $total_amount = $this->home_model->getGrandTotalEwallet($user_id);
            $total_amount = $total_amount * $this->DEFAULT_CURRENCY_VALUE;
            $json_response['data']['ewallet']['total'] = $total_amount;

            //MONTHLY
            $start_date = date('Y-m-01', strtotime('this month')) . " 00:00:00";
            $end_date = date('Y-m-t', strtotime('this month')) . " 23:59:59";
            $total_released = $this->Api_model->getReleasedAmount($user_id, $start_date, $end_date);
            $total_requested = $this->Api_model->getRequestedAmount($user_id, $start_date, $end_date);
            $total_released = $total_released * $this->DEFAULT_CURRENCY_VALUE;
            $total_requested = $total_requested * $this->DEFAULT_CURRENCY_VALUE;
            if (!$total_released) {
                $total_released = 0;
            }
            if (!$total_requested) {
                $total_requested = 0;
            }
            $json_response['data']['ewallet']['monthly']['requested'] = $total_requested;
            $json_response['data']['ewallet']['monthly']['released'] = $total_released;

            //YEARLY
            $start_date = date('Y-01-01') . " 00:00:00";
            $end_date = date('Y-12-31') . " 23:59:59";
            $total_released = $this->Api_model->getReleasedAmount($user_id, $start_date, $end_date);
            $total_requested = $this->Api_model->getRequestedAmount($user_id, $start_date, $end_date);
            $total_released = $total_released * $this->DEFAULT_CURRENCY_VALUE;
            $total_requested = $total_requested * $this->DEFAULT_CURRENCY_VALUE;
            if (!$total_released) {
                $total_released = 0;
            }
            if (!$total_requested) {
                $total_requested = 0;
            }
            $json_response['data']['ewallet']['yearly']['requested'] = $total_requested;
            $json_response['data']['ewallet']['yearly']['released'] = $total_released;

            //WEEKLY
            $start_date = date('Y-m-d', strtotime('last sunday')) . " 00:00:00";
            $end_date = date('Y-m-d') . " 23:59:59";
            $total_released = $this->Api_model->getReleasedAmount($user_id, $start_date, $end_date);
            $total_requested = $this->Api_model->getRequestedAmount($user_id, $start_date, $end_date);
            $total_released = $total_released * $this->DEFAULT_CURRENCY_VALUE;
            $total_requested = $total_requested * $this->DEFAULT_CURRENCY_VALUE;
            if (!$total_released) {
                $total_released = 0;
            }
            if (!$total_requested) {
                $total_requested = 0;
            }
            $json_response['data']['ewallet']['weekly']['requested'] = $total_requested;
            $json_response['data']['ewallet']['weekly']['released'] = $total_released;

            //ALL
            if ($this->MODULE_STATUS['ewallet_status'] == 'yes') {
                $total_requested = $this->home_model->getTotalRequestAmount($user_id);
                $total_released = $this->home_model->getTotalReleasedAmount($user_id);
            } else {
                $total_requested = 0;
                $total_released = 0;
            }
            if (!$total_released) {
                $total_released = 0;
            }
            if (!$total_requested) {
                $total_requested = 0;
            }
            $json_response['data']['ewallet']['all']['requested'] = $total_requested;
            $json_response['data']['ewallet']['all']['released'] = $total_released;
            //EWALLET ENDS

            //JOINING CHART STARTS
            $this->load->model('joining_model');

            //MONTHLY
            $rs = array();
            $monthly_joining = $this->home_model->getJoiningDetailsperMonth($this->LOG_USER_ID);

            if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
                $monthly_joining = $this->joining_model->getJoiningDetailsperMonthLeftRight($this->LOG_USER_ID);
            }
            $i = 0;
            foreach ($monthly_joining as $value) {
                $rs["cord"][$i]["x"] = $value['month'];
                $rs["cord"][$i]["y"] = $value['joining'];
                if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
                    $rs["cord"][$i]["z"] = $value['joining_right'];
                }
                $i++;
            }
            $json_response['data']['joining']['monthly'] = $rs;

            //YEARLY
            $rs = array();
            $i = 0;
            while ($i <= 5) {
                $j = 5 - $i;
                $start_date = date('Y-01-01', strtotime("-$j year")) . " 00:00:00";
                $end_date = date('Y-12-31', strtotime("-$j year")) . " 23:59:59";
                $rs["cord"][$i]["x"] = intval($start_date);
                if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
                    $yearly_joining = $this->joining_model->getJoiningDetailsForBinaryLeftRight($this->LOG_USER_ID, $start_date, $end_date);
                    $rs["cord"][$i]["y"] = $yearly_joining['joining'];
                    $rs["cord"][$i]["z"] = $yearly_joining['joining_right'];
                } else {
                    $rs["cord"][$i]["y"] = $this->joining_model->getJoiningCountPerMonth($start_date, $end_date, $this->LOG_USER_ID);
                }
                $i++;
            }
            $json_response['data']['joining']['yearly'] = $rs;

            //DAILY
            $rs = array();
            $i = 0;
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t', strtotime('this month'));
            while ($start_date <= $end_date) {
                $rs["cord"][$i]["x"] = date('d', strtotime($start_date));

                if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
                    $daily_joining = $this->joining_model->getJoiningDetailsForBinaryLeftRight($this->LOG_USER_ID, $start_date . " 00:00:00", $start_date . " 23:59:59");
                    $rs["cord"][$i]["y"] = $daily_joining['joining'];
                    $rs["cord"][$i]["z"] = $daily_joining['joining_right'];
                } else {
                    $rs["cord"][$i]["y"] = $this->joining_model->getJoiningCountPerMonth($start_date . " 00:00:00", $start_date . " 23:59:59", $this->LOG_USER_ID);
                }
                $start_date = date('Y-m-d', strtotime($start_date . ' +1 day'));
                $i++;
            }
            $json_response['data']['joining']['daily'] = $rs;
            //JOINING CHART ENDS
        }
        echo json_encode($json_response);
        exit();
    }

    public function get_current_currency_details()
    {
        $user_id = $this->LOG_USER_ID;

        if (!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
            echo json_encode($json_response);
            exit();
        } else {
            $default_currency_details = array(
                'SYMBOL_LEFT' => $this->DEFAULT_SYMBOL_LEFT,
                'SYMBOL_RIGHT' => $this->DEFAULT_SYMBOL_RIGHT,
                'CURRENCY_VALUE' => $this->DEFAULT_CURRENCY_VALUE,
            );
            $json_response['status'] = true;
            $json_response['message'] = lang('success');
            $json_response['current_currency_details'] = $default_currency_details;
            echo json_encode($json_response);
            exit();
        }
    }

    public function niceNumber($n)
    {
        // first strip any formatting;
        $n = (0 + str_replace(",", "", $n));

        // is this a number?
        if (!is_numeric($n)) {
            return false;
        }

        // now filter it;
        if ($n > 1000000000000) {
            return round(($n / 1000000000000), 2) . ' T';
        } elseif ($n > 1000000000) {
            return round(($n / 1000000000), 2) . ' B';
        } elseif ($n > 1000000) {
            return round(($n / 1000000), 2) . ' M';
        }

        return number_format($n);
    }

    public function niceNumberCommission($n)
    {
        // first strip any formatting;
        $n = (0 + str_replace(",", "", $n));

        // is this a number?
        if (!is_numeric($n)) {
            return false;
        }

        // now filter it;
        if ($n > 1000000000000) {
            return round(($n / 1000000000000), 2) . ' T';
        } elseif ($n > 1000000000) {
            return round(($n / 1000000000), 2) . ' B';
        } elseif ($n > 1000000) {
            return round(($n / 1000000), 2) . ' M';
        }

        // elseif ($n > 1000) return round(($n / 1000), 2) . ' K';

        return number_format($n, 2);
    }

    public function app_layout_get()
    {

        $site_info =$this->validation_model->getSiteInformation();
        $site_info = $this->validation_model->getSiteInformation();
        $site_info['login_logo']  = SITE_URL.'/uploads/images/logos/logo_login.png';
      
        $menuList = $this->Api_model->getSideMenuList($this->IS_MOBILE);
        if($this->MODULE_STATUS['opencart_status'] == 'yes'){
            foreach ($menuList as $key => $value) {
                if($value['title'] =='ecomStore'){
                    $menuList[$key]['url'] = $this->store_url();
                }else if($value['title'] =='register'){
                    $menuList[$key]['url'] = $this->register_url();
                }
            }
        }

        $languages = [];
        if ($this->MODULE_STATUS['lang_status'] == 'yes') {
            $languages = $this->Api_model->getAllLanuages($this->LOG_USER_ID);
        }
        $currencies = [];
        if ($this->MODULE_STATUS['multy_currency_status'] == 'yes') {
            $currencies = $this->Api_model->getAllCurrencies($this->LOG_USER_ID);
        }
        // dd($this->validation_model->IdToUserName($this->rest->user_id));
        $config = $this->configuration_model->getSettings();
        $data = [
            'lang_status' => ($this->MODULE_STATUS['lang_status'] == 'yes'),
            'languages' => $languages,
            'currency_status' => ($this->MODULE_STATUS['multy_currency_status'] == 'yes'),
            'currencies' => $currencies,
            'menu_list' => $menuList,
            'user_name' => $this->validation_model->getUserName($this->LOG_USER_ID),
            'company_info' => $site_info,
            'user_Image'    =>SITE_URL.'/uploads/images/profile_picture/'.$this->validation_model->getUserImage($this->LOG_USER_ID),
            'mlm_plan' => $this->MODULE_STATUS['mlm_plan'],
            'width'        => $config['width_ceiling'],
            'company_name' =>$site_info['company_name']
        ];
        if (!$this->IS_MOBILE) {
            $data['footer'] = 'to be done';
        } else {
            $data['user_name'] = $this->validation_model->getUserName($this->LOG_USER_ID);
        }
        // $data['notification_count'] = $this->notificationDetails_get();
        $this->set_success_response(200, $data);
    }

    public function dashboard_get()
    {
        $user_id = $this->LOG_USER_ID;
        $this->lang->load('home', $this->LANG_NAME);
        switch ($this->post('range')) {
            case "monthly_payout":
                $start_date = date('Y-m-01', strtotime('this month')) . " 00:00:00";
                $end_date = date('Y-m-t', strtotime('this month')) . " 23:59:59";
                break;

            case "yearly_payout":
                $start_date = date('Y-01-01') . " 00:00:00";
                $end_date = date('Y-12-31') . " 23:59:59";
                break;

            case "weekly_payout":
                $tomorrow = date("Y-m-d", time() + 86400);
                $start_date = date('Y-m-d', strtotime('last Sunday', strtotime($tomorrow))) . " 00:00:00";
                $end_date = date('Y-m-d') . " 23:59:59";
                break;

            default:
                $start_date = '';
                $end_date = '';
                break;
        }

        $tiles = [];
        $dashboardConfig = $this->home_model->getUserDashboardConfig();
        if($this->IS_MOBILE){
            $ewallet = $this->home_model->getGrandTotalEwallet($user_id);
            $tiles[] = [
                'amount' => $ewallet,
                'withcurrency' => thousands_currency_format($ewallet),
                'text' => 'ewallet',
                'title' => lang('e_wallet'),
                'to' => '/ewallet',
                'test' => lang('pagination_first_link'),
                'filter' => false,
            ];
        }
        else {
            if ($dashboardConfig["ewallet"] == 1) {
                $ewallet = $this->home_model->getGrandTotalEwallet($user_id);
                $tiles[] = [
                    'amount' => $ewallet,
                    'withcurrency' => format_currency($ewallet),
                    'text' => 'ewallet',
                    'title' => lang('e_wallet'),
                    'to' => '/ewallet',
                    'test' => lang('pagination_first_link'),
                    'filter' => false,
                ];
            }
        }
        if(!$this->IS_MOBILE){
            if ($dashboardConfig["commission_earned"] == 1) {
                $commision = $this->home_model->getCommissionDetails($start_date, $end_date, $user_id);
                $tiles[] = [
                    'amount' => $commision,
                    'withcurrency' => format_currency($commision),
                    'text' => 'commision',
                    'title' => lang('commision_earned'),
                    'to' => '/ewallet',
                    'filter' => true,
                ];
            }
        }
        if ($dashboardConfig["payout_released"] == 1) {
            $payout_released = $this->home_model->getPayoutDetails($start_date, $end_date, $user_id);
            $tiles[] = [
                'amount' => $payout_released,
                'withcurrency' => ($this->IS_MOBILE) ? thousands_currency_format($payout_released) : format_currency($payout_released),
                'text' => 'payoutRelease',
                'title' => lang('payout_released'),
                'to' => '/payout',
                'filter' => true,
            ];
        }
        if ($dashboardConfig["payout_pending"] == 1) {
            $payout_pending = $this->payout_model->getRequestPendingAmount($user_id);
            $tiles[] = [
                'amount' => $payout_pending,
                'withcurrency' => ($this->IS_MOBILE) ? thousands_currency_format($payout_pending) :format_currency($payout_pending, 2),
                'text' => 'payoutPending',
                'title' => lang('payout_pending'),
                'to' => '/payout',
                'filter' => false,
            ];
        }
        $data = ['tiles' => $tiles];

        $user_details = $this->validation_model->getUserDetails($this->LOG_USER_ID, $this->LOG_USER_TYPE);
        $placementDetails = $this->validation_model->getPlacementDetails($this->LOG_USER_ID);
        $current_rank = $this->rank_model->currentRankName($this->LOG_USER_ID);
        $individula_details = $this->home_model->individulaDetails($this->LOG_USER_ID, ['personal_pv', 'gpv']);
        $site_url = SITE_URL;
        $binary_tree = $this->home_model->individulaDetails($this->LOG_USER_ID, ['total_left_carry', 'total_right_carry']);

        // $product_status = $this->$MODULE_STATUS['product_status'];
        $pro_file_arr = $this->profile_model->getProfileDetails($this->LOG_USER_ID, $this->MODULE_STATUS['product_status']);

        //profile
        if (file_exists(IMG_DIR . "profile_picture/" . $pro_file_arr['details']['profile_photo'])) {
            $profile_picture_full = SITE_URL . "/uploads/images/profile_picture/" . $pro_file_arr['details']['profile_photo'];
        } else {
            $profile_picture_full = SITE_URL . "/uploads/images/profile_picture/nophoto.png";
        }
        if ($dashboardConfig["profile_membership_replica_lcp"] == 1) {
            $data['profile'] = [
                'full_name' => $user_details['user_detail_name'] . " " . $user_details['user_detail_second_name'],
                'user_name' => $this->validation_model->IdToUserName($this->LOG_USER_ID),
                'user_photo' => $profile_picture_full,
                'placement' => $placementDetails->user_name
            ];

        }
        if ($this->MODULE_STATUS['product_status'] == "yes" && $dashboardConfig["profile_membership_replica_lcp"] == 1) {

            if ($this->MODULE_STATUS['opencart_status'] == "yes") {
                $membership = $this->validation_model->getOpenCartProductNameFromUserID($this->LOG_USER_ID);
            } else {
                $membership = $this->validation_model->getProductNameFromUserID($this->LOG_USER_ID);
            }
            $data['profile']['membership_package'] = [
                'title' => lang('current_package'),
                'name' => $membership,
            ];

            $store_id = "";
            if ($this->MODULE_STATUS['package_upgrade'] == "yes") {
                $this->load->model('upgrade_model');
                $current_package_details = $this->upgrade_model->getMembershipPackageDetails($this->LOG_USER_ID);
                if($current_package_details){
                    $upgradable_package_list = $this->upgrade_model->getUpgradablePackageList($current_package_details);
                }
                if (isset($upgradable_package_list) && count($upgradable_package_list)>0) {
                    $data['profile']['membership_package']['upgrade_link'] = '';
                    if ($this->MODULE_STATUS['opencart_status'] == "yes") {
                        if (DEMO_STATUS == 'yes') {
                            $store_id = "&id=" . str_replace("_", "", $this->db->dbprefix);
                        }
                        $data['profile']['membership_package']['upgrade_link'] = STORE_URL . '/index.php?route=upgrade/upgrade' . $store_id;
                    }
                    else{
                        $data['profile']['membership_package']['upgrade_link'] = "";
                    }
                }
            }
        }

        if ($this->MODULE_STATUS['product_status'] == "yes" && $dashboardConfig["profile_membership_replica_lcp"] == 1 && $this->MODULE_STATUS['subscription_status'] == 'yes') {
            $store_id = "";
            $data['profile']['membership_package']['product_validity'] = [
                'title' => lang('membership_will_expire'),
                'date' => $pro_file_arr['details']['product_validity'] ?? '',
                'status' => true,
            ];
            if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                $data['profile']['membership_package']['product_validity']['renewal_link'] = '';
                if ($this->MODULE_STATUS['opencart_status'] == "yes") {
                    if (DEMO_STATUS == 'yes') {
                        $store_id = "&id=" . str_replace("_", "", $this->db->dbprefix);
                    }
                    $data['profile']['membership_package']['product_validity']['renewal_link'] = STORE_URL . 'index.php?route=renewal/renewal' . $store_id;
                }
            }
        }

        // rank
        if(!$this->IS_MOBILE){
            $rank_details = $this->configuration_model->getActiveRankDetails($current_rank['rank_id'] ?? '');
            $rank_configuration = $this->configuration_model->getRankConfiguration();
            if ($this->MODULE_STATUS['rank_status'] == "yes" && $rank_configuration['joinee_package'] != 1 && $dashboardConfig["profile_membership_replica_lcp"] == 1) {
                $data['profile']['rank'] = [
                    'title' => lang('rank'),
                    'curent_rank' => $current_rank['rank_name'] ?? lang('na'),
                    'color' => isset($rank_details[0]) ? $rank_details[0]['rank_color'] : "#ccc"
                ];
            }
        }
        //end of rank

        //extra data in dashboard
        //sponsor name
        if ($this->IS_MOBILE) {
            $data['extra_data']['sponsor'] = [
                    'head' => $pro_file_arr['details']['sponsor_name'],
                    'text' => 'sponsorName',
                    'title' => lang('sponsor_name'),
                ];
            }else{
                $data['sponser_details'][] = [
                    'head' => $pro_file_arr['details']['sponsor_name'],
                    'text' => 'sponsorName',
                    'title' => lang('sponsor_name'),

                ];
            }


        // personal pv and group pv
        if ($dashboardConfig["sponsor_pv_carry"] == 1) {
            if ($this->IS_MOBILE) {
                $data['extra_data']['pv']['personal'] = [
                    'head' => (int) $individula_details->personal_pv,
                    'title' => lang('personal'),
                ];
                $data['extra_data']['pv']['group'] = [
                    'head' => (int) $individula_details->gpv,
                    'title' => lang('group'),
                ];
            } else {
                $data['extra_data'][] = [
                    'head' => (int) $individula_details->personal_pv,
                    'text' => 'personalPv',
                    'title' => lang('personal_pv'),
                ];
                $data['extra_data'][] = [
                    'head' => (int) $individula_details->gpv,
                    'text' => 'groupPV',
                    'title' => lang('group_pv'),
                ];
            }
        }
        //end of personal pv and group pv

        // binary plane
        if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
            if ($this->IS_MOBILE) {
                $data['extra_data']['carry']['leftCarry'] = [
                    'head' => (int) $binary_tree->total_left_carry,
                    'text' => 'leftCarry',
                    'title' => lang('total_left_carry'),
                ];
                $data['extra_data']['carry']['rightCarry'] = [
                    'head' => (int) $binary_tree->total_right_carry,
                    'text' => 'rightCarry',
                    'title' => lang('total_right_carry'),
                ];
            } else {
                $data['extra_data'][] = [
                    'head' => (int) $binary_tree->total_left_carry,
                    'text' => 'leftCarry',
                    'title' => lang('total_left_carry'),
                ];
                $data['extra_data'][] = [
                    'head' => (int) $binary_tree->total_right_carry,
                    'text' => 'rightCarry',
                    'title' => lang('total_right_carry'),
                ];
            }
        }

        //end of binary plane

        //end of extra data in dashboard
        $admin = $this->validation_model->getAdminUsername();
        // replica link
        // $replica= [
        //     'status' =>$this->MODULE_STATUS['replicated_site_status']=="yes"?true:false,
        //     'title' =>lang('replica_link'),
        // ];

        if ($this->MODULE_STATUS['replicated_site_status'] == "yes" && $dashboardConfig["profile_membership_replica_lcp"] == 1) {
            if ($this->IS_MOBILE) {
                $data['profile']['replica']['title'] = lang('replica_link');
                if (DEMO_STATUS == 'yes') {

                    $replica_link = $site_url . "replica/" . $admin . "/" . $this->validation_model->IdToUserName($user_id);

                } else {

                    $replica_link = $site_url . "replica/" . $this->validation_model->IdToUserName($user_id);
                }
                $data['profile']['replica']['copy_link'] = [
                    'icon' => 'files-o',
                    'link' => $replica_link,
                ];
                $data['profile']['replica']['shared_link'][] = [
                    'icon' => 'facebook',
                    'colour' => '#1877F2',
                    'link' => $replica_link,
                ];
                $data['profile']['replica']['shared_link'][] = [
                    'icon' => 'twitter',
                    'colour' => '#1DA1F2',
                    'link' => $replica_link,
                ];
                $data['profile']['replica']['shared_link'][] = [
                    'icon' => 'linkedin',
                    'colour' => '#2867B2',
                    'link' => $replica_link,
                ];
                $data['profile']['replica']['shared_link'][] = [
                    'icon' => 'telegram',
                    'colour' => '#0088CC',
                    'link' => $replica_link,
                ];
                $data['profile']['replica']['shared_link'][] = [
                    'icon' => 'whatsapp',
                    'colour' => '#25D366',
                    'link' => $replica_link,
                ];
            } else {
                $data['profile']['replica_title'] = lang('replica_link');
                if (DEMO_STATUS == 'yes') {
                    $replica_link = REPLICATION_URL . $this->validation_model->IdToUserName($user_id)."/". $admin;

                } else {

                    $replica_link = REPLICATION_URL . $this->validation_model->IdToUserName($user_id);
                }
                $data['profile']['replica'][] = [
                    'icon' => 'fa fa-files-o',
                    'link' => $replica_link,
                ];
                $data['profile']['replica'][] = [
                    'icon' => 'fa fa-facebook',
                    'link' => "https://www.facebook.com/sharer/sharer.php?u=" . $replica_link,
                ];
                $data['profile']['replica'][] = [
                    'icon' => 'fa fa-twitter',
                    'link' => "https://twitter.com/share?url=" . $replica_link,
                ];
                $data['profile']['replica'][] = [
                    'icon' => 'fa fa-linkedin',
                    'link' => "http://www.linkedin.com/shareArticle?url=" . $replica_link,
                ];
            }

        }
        //end replica link

        if ($this->MODULE_STATUS['lead_capture_status'] == "yes" && $dashboardConfig["profile_membership_replica_lcp"] == 1) {
            if ($this->IS_MOBILE) {
                $data['profile']['lead_capture']['title'] = lang('lead_capture');
                if (DEMO_STATUS == 'yes') {
                    $lead_capture = $site_url . "/lcp/" . $admin . "/" . $this->validation_model->IdToUserName($user_id);
                } else {

                    $lead_capture = $site_url . "/lcp/" . $this->validation_model->IdToUserName($user_id);

                }
                $data['profile']['lead_capture']['copy_link'] = [
                    'icon' => 'files-o',
                    'link' => $lead_capture,
                ];
                $data['profile']['lead_capture']['shared_link'][] = [
                    'icon' => 'facebook',
                    'colour' => '#4267B2',
                    'link' => "https://www.facebook.com/sharer/sharer.php?u=" . $lead_capture,
                ];
                $data['profile']['lead_capture']['shared_link'][] = [
                    'icon' => 'twitter',
                    'colour' => '#1DA1F2',
                    'link' => "https://twitter.com/share?url=" . $lead_capture,
                ];
                $data['profile']['lead_capture']['shared_link'][] = [
                    'icon' => 'linkedin',
                    'colour' => '#0072b1',
                    'link' => "http://www.linkedin.com/shareArticle?url=" . $lead_capture,
                ];
            } else {
                $data['profile']['lead_capture_title'] = lang('lead_capture');
                if (DEMO_STATUS == 'yes') {
                    $lead_capture = $site_url . "/lcp/" . $admin . "/" . $this->validation_model->IdToUserName($user_id);
                } else {

                    $lead_capture = $site_url . "/lcp/" . $this->validation_model->IdToUserName($user_id);

                }
                $data['profile']['lead_capture'][] = [
                    'icon' => 'fa fa-files-o',
                    'link' => $lead_capture,
                ];
                $data['profile']['lead_capture'][] = [
                    'icon' => 'fa fa-facebook',
                    'link' => "https://www.facebook.com/sharer/sharer.php?u=" . $lead_capture,
                ];
                $data['profile']['lead_capture'][] = [
                    'icon' => 'fa fa-twitter',
                    'link' => "https://twitter.com/share?url=" . $lead_capture,
                ];
                $data['profile']['lead_capture'][] = [
                    'icon' => 'fa fa-linkedin',
                    'link' => "http://www.linkedin.com/shareArticle?url=" . $lead_capture,
                ];
            }
        }
        //end of lead capture

        $latest_joinees = $this->home_model->getLatestJoinees('user');
        $j = 0;
        foreach ($latest_joinees as $v) {
            $latest_joinees[$j]['product_amount_with_currency'] = format_currency($latest_joinees[$j]['product_amount']);
            if (!file_exists(IMG_DIR . "profile_picture/" . $latest_joinees[$j]['profile_pic'])) {
                $latest_joinees[$j]['profile_pic'] = 'nophoto.png';
            }
            if ($this->IS_MOBILE) {
                $latest_joinees[$j]['profile_pic'] = SITE_URL . "/uploads/images/profile_picture/" . $latest_joinees[$j]['profile_pic'];
            } else {
                $latest_joinees[$j]['profile_picture_full'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/" . $latest_joinees[$j]['profile_pic'];
            }
            $j++;
        }
        $data['new_members_status'] = $dashboardConfig["new_members"] == 1 ? true : false;
        if ($dashboardConfig["new_members"] == 1) {
            $data['new_members'] = $latest_joinees;
        }

        /**
         * Current Rank
         */

        if ($this->MODULE_STATUS['rank_status'] == "yes" &&$dashboardConfig["rank"] == 1) {
            $crank = $this->rank_model->currentRankName($this->LOG_USER_ID);
            if (!empty($crank)) {
                $current_rank = $this->rank_model->getCurrentRankData($this->LOG_USER_ID);
                $rank_criteria = array_keys($current_rank['criteria'], 1);
                //
                $current_ranks = [];
                if ($rank_criteria[0]!="joinee_package") {
                    foreach ($rank_criteria as $criteria) {
                        if ($criteria != 'downline_package_count' && $criteria != 'downline_rank' && $criteria != "joinee_package") {
                            $criteria = $criteria == "downline_count" ? "downline_member_count" : $criteria;
                            if ($criteria == "referal_count") {
                                $text = "referralCount";
                                $title = lang('referal_count');
                            } else if ($criteria == "personal_pv") {
                                $text = "personalPv";
                                $title = lang('personal_pv');
                            } else if ($criteria == "group_pv") {
                                $text = "groupPV";
                                $title = lang('group_pv');
                            } else {
                                $text = "downlineMemberCount";
                                $title = lang('downline_member_count');
                            }
                            $current_rank[$criteria]['text'] = $text;
                            $current_rank[$criteria]['title'] = $title;
                            $current_ranks['criteria'][] = $current_rank[$criteria];

                        }else if($criteria == 'downline_package_count'){
                            foreach ($current_rank[$criteria]  as $package_rank) {
                                $current_ranks['criteria'][] =[
                                    'text' => 'downline_package_count',
                                    'title' => lang('downline_package_count'),
                                    'subtile' => $package_rank['product_name'],
                                    'required' => $package_rank['required'],
                                    'achieved' => $package_rank['achieved'],
                                    'percentage' => $package_rank['percentage'],
                                ];
                            }
                        }else if($criteria == 'downline_rank'){
                            foreach ($current_rank[$criteria]  as $downline_rank) {
                                $current_ranks['criteria'][] =[
                                    'text' => 'downline_rank',
                                    'title' => lang('downline_rank'),
                                    'subtile' => $downline_rank['rank_name'],
                                    'required' => $downline_rank['required'],
                                    'achieved' => $downline_rank['achieved'],
                                    'percentage' => $downline_rank['percentage'],
                                ];
                            }
                        }

                    }
                    $current_ranks['criteria'][0]['percentage'] = isset($current_ranks['criteria']) ? round($current_ranks['criteria'][0]['percentage'], 1) : "";
                    $data['rank']['current'] = $current_ranks; //current rank criteria array
                    $data['rank']['current']['name'] = $crank['rank_name']; // current rank name
                    //
                    $nrank = $this->rank_model->NextRankName($crank['rank_id']);
                }
            } else {
                $data['rank']['current'] = [
                    'criteria' => [],
                    'name'  => lang('na')
                ];
                // $data['rank']['current']['name'] = lang('na');
                $current_rank = null;
                $nrank = $this->rank_model->NextRankName();
                $rank_criteria = null;
            }

            /**
             * Next Rank
             */
            if(!$this->IS_MOBILE){
                if (!empty($nrank)) {
                    $next_rank = $this->rank_model->getNextRankData($nrank['rank_id'], $this->LOG_USER_ID);
                    $next_ranks = [];
                    foreach ($next_rank as $key => $value) {
                        if ($key != 'downline_package_count' && $key != 'downline_rank' && $key != "joinee_package") {
                            if ($key == "referal_count") {
                                $text = "referralCount";
                                $title = lang('referal_count');
                            } else if ($key == "personal_pv") {
                                $text = "personalPv";
                                $title = lang('personal_pv');
                            } else if ($key == "group_pv") {
                                $text = "groupPV";
                                $title = lang('group_pv');
                            } else {
                                $text = "downlineMemberCount";
                                $title = lang('downline_member_count');
                            }
                            $key = ($key == "downline_count") ? "downline_member_count" : $key;
                            $next_rank[$key]['text'] = $text;
                            $next_rank[$key]['title'] = $title;
                            $next_ranks['criteria'][] = $next_rank[$key];
                        }else if($key == 'downline_package_count'){
                            foreach ($next_rank[$key]  as $package_rank) {
                                $next_ranks['criteria'][] =[
                                    'text' => 'downline_package_count',
                                    'title' => lang('downline_package_count'),
                                    'subtile' => $package_rank['product_name'],
                                    'required' => $package_rank['required'],
                                    'achieved' => $package_rank['achieved'],
                                    'percentage' => $package_rank['percentage'],
                                ];
                            }
                        }else if($key == 'downline_rank'){
                                foreach ($next_rank[$key]  as $downline_rank) {
                                    $next_ranks['criteria'][] =[
                                        'text' => 'downline_rank',
                                        'title' => lang('downline_rank'),
                                        'subtile' => $downline_rank['rank_name'],
                                        'required' => $downline_rank['required'],
                                        'achieved' => $downline_rank['achieved'],
                                        'percentage' => $downline_rank['percentage'],
                                    ];
                                }
                            }
                    }
                    $next_ranks['criteria'][0]['percentage'] = isset($next_ranks['criteria'][0]['percentage']) ? round($next_ranks['criteria'][0]['percentage'], 1) : 0;
                    $data['rank']['next'] = $next_ranks;
                    $data['rank']['next']['name'] = $nrank['rank_name'];
                } else {
                    $next_rank = null;
                }
            }
        }

        /**
         * Earnings & Expenses
         */
        if(!$this->IS_MOBILE){
            if ($dashboardConfig["earnings_expenses"] == 1) {
                /**
                 * Earnings
                 */
                if ($dashboardConfig["earnings"] == 1) {
                    $income = $this->home_model->getAllIncomeOrExpense($this->LOG_USER_ID, 'credit', 4);
                    foreach ($income as $key => $value) {
                        $income[$key]['amount']     = $income[$key]['amount'];
                        $type = $value['amount_type'];
                        if ($type == "rank_bonus") {
                            $type = "rankcommission";
                        } elseif ($type == "level_commission") {
                            $type = "levelCommission";
                        } elseif ($type == 'leg') {
                            $type = "binaryCommission";
                        } elseif ($type == "referral") {
                            $type = "referralCommission";
                        }
                        $income[$key]['amount_type'] = $type;
                        $income[$key]['title'] = strtoupper(lang($type));
                        if ($this->IS_MOBILE) {
                            $income[$key] = [
                                'title' => strtoupper(lang($type)),
                                'colour' => '#27c24c',
                                'data' => format_currency($income[$key]['amount']),
                            ];
                        }
                    }
                    if ($this->IS_MOBILE) {
                        $data['earnings_nd_expenses'][] = [
                            "title" => lang('Earnings'),
                            "code" => "earnings",
                            "data" => $income,
                        ];
                    } else {
                        $data['earnings_nd_expenses']['incomes'] = $income;
                    }
                }
                /**
                 * Expenses
                 */
                if ($dashboardConfig["expenses"] == 1) {
                    $expenses_array = $this->home_model->getAllIncomeOrExpense($this->LOG_USER_ID, 'debit', 4);
                    if ($this->IS_MOBILE) {
                        for ($i = 0; $i < count($expenses_array); $i++) {
                            $amount = format_currency($expenses_array[$i]['amount']);
                            $title = lang($expenses_array[$i]['amount_type']);
                            if ($this->IS_MOBILE) {
                                $expenses_array[$i] = [
                                    'title' => $title,
                                    'colour' => '#27c24c',
                                    'data' => $amount,
                                ];
                            }
                        }
                        $data['earnings_nd_expenses'][] = [
                            "title" => lang('expenses'),
                            "code" => "expenses",
                            "data" => $expenses_array,
                        ];
                    } else {
                        $expensesData = array_map( function($expense) {
                            $data = $expense;
                            $data['amount'] = $expense['amount'];
                            return $data;
                        }, $expenses_array); 
                        $data['earnings_nd_expenses']['expenses'] = $expensesData;
                    }
                }
                /**
                 * Payout
                 */
                if ($dashboardConfig["payout_status"] == 1) {
                    if ($this->IS_MOBILE) {
                        $data['earnings_nd_expenses'][] = [
                            "title" => lang('payout_status'),
                            "code" => "payout_status",
                            "data" => [
                                [
                                    'title' => lang('requested'),
                                    'colour' => '#000000',
                                    'data' => format_currency($this->payout_model->getUserTotalPayoutRequests($this->LOG_USER_ID)),
                                ],
                                [
                                    'title' => lang('approved'),
                                    'colour' => '#7266ba',
                                    'data' => format_currency($this->payout_model->getUserTotalPayouts($this->LOG_USER_ID, 'approved')),
                                ],
                                [
                                    'title' => lang('paid'),
                                    'colour' => '#27c24c',
                                    'data' => format_currency($this->payout_model->getUserTotalPayouts($this->LOG_USER_ID, 'paid')),
                                ],
                                [
                                    'title' => lang('rejected'),
                                    'colour' => '#f05050',
                                    'data' => format_currency($this->payout_model->getUserTotalPayouts($this->LOG_USER_ID, 'rejected')),
                                ],
                            ],
                        ];
                    } else {
                        $data['earnings_nd_expenses']['payout_statuses'] = [
                            'requested' => (float) $this->payout_model->getUserTotalPayoutRequests($this->LOG_USER_ID),
                            'approved' => (float) $this->payout_model->getUserTotalPayouts($this->LOG_USER_ID, 'approved'),
                            'paid' => (float) $this->payout_model->getUserTotalPayouts($this->LOG_USER_ID, 'paid'),
                            'rejected' => (float) $this->payout_model->getUserTotalPayouts($this->LOG_USER_ID, 'rejected'),
                        ];
                    }
                }
            }
        }
        /**
         * End Of Earnings & Expenses
         */

        /**
         * Team Perfomance
         */
        if(!$this->IS_MOBILE){
            if ($dashboardConfig["team_perfomance"] == 1) {
                /**
                 * Top Rerned user
                 */
                if ($dashboardConfig["top_earners"] == 1) {
                    //top 5 Earners
                    $top_earners = $this->home_model->getTopEarners(4, $this->LOG_USER_ID);
                    $i = 0;
                    $top_earners_mobile_view = [];
                    foreach ($top_earners as $v) {
                        $profile_picture = $this->validation_model->getProfilePicture($v['id']);
                        if (!file_exists(IMG_DIR . "profile_picture/" . $profile_picture)) {
                            $profile_picture = "nophoto.png";
                        }
                        if ($this->IS_MOBILE) {
                            $top_earners_mobile_view[] = [
                                'profile_picture' => SITE_URL . "/uploads/images/profile_picture/" . $profile_picture,
                                'user_name' => $top_earners[$i]['user_name'],
                                'full_name' => $top_earners[$i]['user_detail_name'] . ' ' . $top_earners[$i]['user_detail_second_name'],
                                'data' => format_currency($top_earners[$i]['balance_amount']),
                            ];
                        } else {
                            $top_earners[$i]['profile_picture'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/" . $profile_picture;
                        }
                        $i++;
                    }
                    if ($this->IS_MOBILE) {
                        $data['team_perfomance'][] = [
                            'title' => lang('top_earners'),
                            'code' => "top_earners",
                            'data' => $top_earners_mobile_view,
                        ];
                    } else {
                        $data['team_perfomance']['top_earners'] = $top_earners;
                    }
                }
                /**
                 * Top Rerned user
                 */
                if ($dashboardConfig["top_recruiters"] == 1) {
                    $top_recruters = $this->home_model->getTopRecruters(7, $this->LOG_USER_ID);
                    $j = 0;
                    $top_recruters_mobile_view = [];
                    foreach ($top_recruters as $v) {
                        if (!file_exists(IMG_DIR . "profile_picture/" . $top_recruters[$j]['profile_picture'])) {
                            $top_recruters[$j]['profile_picture'] = "nophoto.png";
                        }
                        if ($this->IS_MOBILE) {
                            $top_recruters_mobile_view[] = [
                                'profile_picture' => SITE_URL . "/uploads/images/profile_picture/" . $top_recruters[$j]['profile_picture'],
                                'user_name' => $top_recruters[$j]['user_name'],
                                'full_name' => $top_recruters[$j]['user_detail_name'] . ' ' . $top_recruters[$j]['user_detail_second_name'],
                                'data' => $top_recruters[$j]['count'],
                            ];
                        } else {
                            $top_recruters[$j]['profile_picture'] = dirname($this->BASE_URL) . "/uploads/images/profile_picture/" . $top_recruters[$j]['profile_picture'];
                        }
                        $j++;
                    }
                    if ($this->IS_MOBILE) {
                        $data['team_perfomance'][] = [
                            'title' => lang('top_recruiters'),
                            'code' => "top_recruiters",
                            'data' => $top_recruters_mobile_view,
                        ];
                    } else {
                        $data['team_perfomance']['top_recruters'] = $top_recruters;
                    }
                }
                /**
                 * package Overview
                 */
                if ($this->MODULE_STATUS['product_status'] == "yes") {
                    if ($dashboardConfig["package_overview"] == 1) {
                        //Data For Progress bar
                        $prgrsbar_data = $this->home_model->getPackageProgressData(4, $this->LOG_USER_ID);
                        if ($this->IS_MOBILE) {
                            $package_overview_mobile = [];
                            foreach ($prgrsbar_data as $dt) {
                                $package_overview_mobile[] = [
                                    'text1' => $dt['package_name'],
                                    'text2' => lang('you_have') . " {$dt['joining_count']} {$dt['package_name']} " . lang('package_purchases_in_your_team'),
                                ];
                            }
                            $data['team_perfomance'][] = [
                                'title' => lang('package_overview'),
                                'code' => "package_overview",
                                'data' => $package_overview_mobile,
                            ];
                        } else {
                            $data['team_perfomance']['package_overview'] = $prgrsbar_data;
                        }
                    }
                }
                /**
                 * Rank Overview
                 */
                if ($this->MODULE_STATUS['rank_status'] == "yes") {
                    if ($dashboardConfig["rank_overview"] == 1) {
                        $rank_data = $this->home_model->getRankData(4, $this->LOG_USER_ID);
                        if ($this->IS_MOBILE) {
                            $rank_data_mobile = [];
                            foreach ($rank_data as $dt) {
                                $rank_data_mobile[] = [
                                    'text1' => $dt['rank_name'],
                                    'text2' => lang('you_have') . " {$dt['count']} {$dt['rank_name']} " . lang('rank_in_your_team'),
                                ];
                            }
                            $data['team_perfomance'][] = [
                                "title" => lang('rank_overview'),
                                "code" => "rank_overview",
                                "data" => $rank_data_mobile,
                            ];
                        } else {
                            $data['team_perfomance']['rank_overview'] = $rank_data;
                        }
                    }
                }
            }
        }
        // $joining_graph_data = [];
        // if ($dashboardConfig["joinings_graph"] == 1) {
        //     $range = $this->get('range') ?? 'monthly_joining_graph';
        //     $this->load->model('joining_model');
        //     $rs = [];
        //     $i = 0;
        //     $chartMode = "month";
        //     if ($range == 'yearly_joining_graph') {
        //         $chartMode = "year";
        //     } elseif ($range == 'daily_joining_graph') {
        //         $chartMode = "day";
        //     }

        //     $join_data = $this->home_model->getJoiningLineChartData($this->LOG_USER_ID, $chartMode);

            // if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
            //     $joining_graph_data['rightJoinDataTitle'] = lang('right_join');
            //     $joining_graph_data['rightJoinDataColour'] = '#189ec8';
            //     $joining_graph_data['rightJoinData'] = $join_data['rightJoinArray'];
            //     $joining_graph_data['leftJoinDataTitle'] = lang('left_join');
            //     $joining_graph_data['leftJoinDataColour'] = '#7265ba';
            //     $joining_graph_data['leftJoinData'] = $join_data['leftJoinArray'];
            // } else {
            //     $joining_graph_data['leftJoinDataTitle'] = lang('joinings');
            //     $joining_graph_data['leftJoinDataColour'] = '#7265ba';
            //     $joining_graph_data['leftJoinData'] = $join_data['joinArray'];
            // }

        //     if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
        //             $joining_graph_data['data'][]=[
        //                 "label"                => lang('right_join'),
        //                 "code"                 => 'rightJoinings',
        //                 "color"                =>'#189ec8',
        //                 "backgroundColor"      => 'rgba(71, 172, 222,0.3)',
        //                 "data"                 => $join_data['rightJoinArray'],
        //             ];
        //             $joining_graph_data['data'][]=[
        //                 "label"                => lang('left_join'),
        //                 "code"                 => 'leftJoinings',
        //                 "color"                =>'#7265ba',
        //                 "backgroundColor"      => 'rgba(149, 139, 204,0.3)',
        //                 "data"                 => $join_data['leftJoinArray'],
        //             ];
        //     }
        //     else
        //     {
        //             $joining_graph_data['data'][]=[
        //                 "label"                => lang('joinings'),
        //                 "code"                 => 'joinings',
        //                 "color"                =>'#7265ba',
        //                 "backgroundColor"      => 'rgba(149, 139, 204,0.3)',
        //                 "data"                 => $join_data['joinArray'],
        //             ];
        //     }
        //     $joining_graph_data['labels'] = $join_data['labels'];
        //     $data['joining_graph_data'] = $joining_graph_data;
        // }

        
        // new joining graph configuration
        $joining_graph_data_new = [];
        if ($dashboardConfig["joinings_graph"] == 1) {
            $range = $this->get('range') ?? 'monthly_joining_graph';
            $this->load->model('joining_model');
            $rs = [];
            $i = 0;
            $chartMode = "month";
            if ($range == 'yearly_joining_graph') {
                $chartMode = "year";
            } elseif ($range == 'daily_joining_graph') {
                $chartMode = "day";
            }

            $join_data = $this->home_model->getJoiningLineChartData($this->LOG_USER_ID, $chartMode);
            if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
                for ($i=0; $i <= 11 ; $i++) { 
                    $joining_graph_data_new['chart'][$i] = [$join_data['labels'][$i], $join_data['leftJoinArray'][$i], $join_data['rightJoinArray'][$i]]; 
                }
                // $joining_graph_data_new['data'][]=[
                //     "label"                => [lang('left_join'),lang('right_join')],
                //     "code"                 => ['leftJoinings','rightJoinings'],
                //     "color"                =>['#7265ba','#189ec8'],
                //     "backgroundColor"      => ['rgba(149, 139, 204,0.3)','rgba(71, 172, 222,0.3)'],
                //     // "data"                 => $join_data['rightJoinArray']
                // ];
                $joining_graph_data_new['label']=[lang('left_join'),lang('right_join')];
                $joining_graph_data_new['code']=['leftJoinings','rightJoinings'];
                $joining_graph_data_new['color']=['#7265ba','#189ec8'];
                $joining_graph_data_new['background']=['rgba(149, 139, 204,0.3)','rgba(71, 172, 222,0.3)'];

            }
            else
            {
                for ($i=0; $i <= 11 ; $i++) { 
                    $joining_graph_data_new['chart'][$i] = [$join_data['labels'][$i], $join_data['joinArray'][$i]]; 
    
                }
                // $joining_graph_data_new['data'][]=[
                //     "label"                => [lang('joinings')],
                //     "code"                 => ['joinings'],
                //     "color"                =>['#7265ba'],
                //     "backgroundColor"      => ['rgba(149, 139, 204,0.3)'],
                //     // "data"                 => $join_data['joinArray'],
                // ];
                $joining_graph_data_new['label']=[lang('joinings')];
                $joining_graph_data_new['code']=['joinings'];
                $joining_graph_data_new['color']=['#7265ba'];
                $joining_graph_data_new['background']=['rgba(149, 139, 204,0.3)'];

            }
            // $joining_graph_data_new['labels'] = $join_data['labels'];
            $data['joining_graph_data_new'] = $joining_graph_data_new;
        }
        $this->set_success_response(200, $data);
    }

// new graph filter get function
    public function graphFilter_get()
    {
        $joining_graph_data = [];
        $range = $this->get('range') ?? 'month';
        $this->load->model('joining_model');

        $join_data = $this->home_model->getJoiningLineChartData($this->LOG_USER_ID, $range);
        if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
            if($range != 'year') {
                for ($i=0; $i <= 11 ; $i++) { 
                    $joining_graph_data_new['chart'][$i] = [$join_data['labels'][$i], $join_data['leftJoinArray'][$i], $join_data['rightJoinArray'][$i]]; 
    
                }
            }
            for ($i=0; $i <= 5 ; $i++) { 
                $joining_graph_data_new['chart'][$i] = [$join_data['labels'][$i], $join_data['leftJoinArray'][$i], $join_data['rightJoinArray'][$i]]; 

            }
            // $joining_graph_data_new['data'][]=[
            //     "label"                => [lang('left_join'),lang('right_join')],
            //     "code"                 => ['leftJoinings','rightJoinings'],
            //     "color"                =>['#7265ba','#189ec8'],
            //     "backgroundColor"      => ['rgba(149, 139, 204,0.3)','rgba(71, 172, 222,0.3)'],
            //     // "data"                 => $join_data['rightJoinArray']
            // ];
            $joining_graph_data_new['label']=[lang('left_join'),lang('right_join')];
            $joining_graph_data_new['code']=['leftJoinings','rightJoinings'];
            $joining_graph_data_new['color']=['#7265ba','#189ec8'];
            $joining_graph_data_new['background']=['rgba(149, 139, 204,0.3)','rgba(71, 172, 222,0.3)'];
        }
        else
        {
            // dd($join_data);
            if($range != 'year') {
                for ($i=0; $i <= 11 ; $i++) { 
                    $joining_graph_data_new['chart'][$i] = [$join_data['labels'][$i], $join_data['joinArray'][$i]]; 
    
                }
            }
            for ($i=0; $i <= 5 ; $i++) { 
                $joining_graph_data_new['chart'][$i] = [$join_data['labels'][$i], $join_data['joinArray'][$i]]; 

            }
            
            // dd($joining_graph_data_new);
            // $joining_graph_data_new['data'][]=[
            //     "label"                => [lang('joinings')],
            //     "code"                 => ['joinings'],
            //     "color"                =>['#7265ba'],
            //     "backgroundColor"      => ['rgba(149, 139, 204,0.3)'],
            //     // "data"                 => $join_data['joinArray'],
            // ];
            $joining_graph_data_new['label']=[lang('joinings')];
            $joining_graph_data_new['code']=['joinings'];
            $joining_graph_data_new['color']=['#7265ba'];
            $joining_graph_data_new['background']=['rgba(149, 139, 204,0.3)'];
        }
        // $joining_graph_data['labels'] = $join_data['labels'];
        $data['joining_graph_data_new'] = $joining_graph_data_new;
        $this->set_success_response(200, $data);
    }


    // //graph filter get method api
    // public function graphFilter_get()
    // {
    //     $joining_graph_data = [];
    //     $range = $this->get('range') ?? 'month';
    //     $this->load->model('joining_model');

    //     $join_data = $this->home_model->getJoiningLineChartData($this->LOG_USER_ID, $range);
    //     if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
    //                 $joining_graph_data['data'][]=[
    //                     "label"                => lang('right_join'),
    //                     "code"                 => 'rightJoinings',
    //                     "color"                =>'#189ec8',
    //                     "backgroundColor"      => 'rgba(71, 172, 222,0.3)',
    //                     "data"                 => $join_data['rightJoinArray'],
    //                 ];
    //                 $joining_graph_data['data'][]=[
    //                     "label"                => lang('left_join'),
    //                     "code"                 => 'leftJoinings',
    //                     "color"                =>'#7265ba',
    //                     "backgroundColor"      => 'rgba(149, 139, 204,0.3)',
    //                     "data"                 => $join_data['leftJoinArray'],
    //                 ];
    //         }
    //         else
    //         {
    //                 $joining_graph_data['data'][]=[
    //                     "label"                => lang('joinings'),
    //                     "code"                 => 'joinings',
    //                     "color"                =>'#7265ba',
    //                     "backgroundColor"      => 'rgba(149, 139, 204,0.3)',
    //                     "data"                 => $join_data['joinArray'],
    //                 ];
    //         }
    //         $joining_graph_data['labels'] = $join_data['labels'];
    //         $data['joining_graph_data'] = $joining_graph_data;
    //         $this->set_success_response(200, $data);
    // }

    public function tileFilter_get()
    {
        $this->lang->load('home', $this->LANG_NAME);
        $user_id = $this->LOG_USER_ID;

        switch ($this->get('range')) {
            case "thisMonth":
                $start_date = date('Y-m-01', strtotime('this month')) . " 00:00:00";
                $end_date = date('Y-m-t', strtotime('this month')) . " 23:59:59";
                break;

            case "thisYear":
                $start_date = date('Y-01-01') . " 00:00:00";
                $end_date = date('Y-12-31') . " 23:59:59";
                break;

            case "thisWeek":
                $tomorrow = date("Y-m-d", time() + 86400);
                $start_date = date('Y-m-d', strtotime('last Sunday', strtotime($tomorrow))) . " 00:00:00";
                $end_date = date('Y-m-d') . " 23:59:59";
                break;

            default:
                $start_date = '';
                $end_date = '';
                break;
        }
        $tiles = [];
        if ($this->get('type') == "commision") {
            $commision = $this->home_model->getCommissionDetails($start_date, $end_date, $user_id);
            $tiles = [
                'amount' => $commision,
                'withcurrency' => format_currency($commision),
                'text' => 'commision',
                'title' => lang('commision_earned'),
                'to' => '/ewallet',
                'filter' => true,
            ];
        }
        if ($this->get('type') == "payoutRelease") {
            $payout_released = $this->home_model->getPayoutDetails($start_date, $end_date, $user_id);
            $tiles = [
                'amount' => $payout_released,
                'withcurrency' => format_currency($payout_released),
                'text' => 'payoutRelease',
                'title' => lang('payout_released'),
                'to' => '/ewallet',
                'filter' => true,
            ];
        }
        $data = $tiles;
       
        $this->set_success_response(200, $data);
    }

    public function notifications_get()
    {
        $limit = (int)$this->get('limit');
        if(!$limit) {
            $limit = 0;
        }
        $user_id = $this->LOG_USER_ID;
        $mail_data = $this->home_model->getUnreadMessages($this->LOG_USER_TYPE, $user_id);
        foreach ($mail_data as $key => $value) {
            $mail_data[$key]['image'] = SITE_URL.'/uploads/images/profile_picture/'.$value['image'];
        }
        if($limit > 0) {
            $mail_content = array_slice($mail_data, 0, $limit);
        }
        $mail_count = count($mail_data);
        $mail_details = compact('mail_data', 'mail_count');

        $this->load->model('document_model', '', true);
        $this->load->model('news_model', '', true);
        $this->load->model('payout_model', '', true);
        $this->load->model('epin_model', '', true);

        $payout_release_type = $this->MODULE_STATUS['payout_release_status'];
        $payout_count = 0;
        if ($payout_release_type == "ewallet_request") {
            $payout_count = (int)$this->payout_model->userPayoutRequestCount($user_id, "released", date("Y-m-d H:i:s", strtotime("-2 days")), 1);
        }
        $pin_count = 0;
        if ($this->MODULE_STATUS['pin_status'] == "yes") {
            $this->load->model('epin_model', '', true);
            $pin_count = (int)$this->epin_model->getUserPinRequestCount($user_id, 'no', 1);
        }
        $document_count = (int)$this->document_model->getUnreadDocumentsCount($user_id);
        $news_count = (int)$this->news_model->getUnreadNewsCount($user_id);

        $notification_count = $payout_count + $pin_count + $document_count + $news_count;
        $notification_details = compact('payout_count', 'pin_count', 'document_count', 'news_count', 'notification_count');

        $this->set_success_response(200, [
            'mail_details' => $mail_details,
            'notification_details' => $notification_details
        ]);
    }

    public function store_url_get()
    {
        $token = $this->Api_model->get_store_url($this->LOG_USER_ID);
        if(!$token){
            $token = $this->Api_model->create_oc_token($this->LOG_USER_ID,$this->rest->key);
        }else{
            $token = $token['token'];
        }
        $table_prefix = str_replace("_", "", $this->db->dbprefix);
        $url = SITE_URL . "/store/index.php?route=common/home&key=$token&id=$table_prefix";
        $this->set_success_response(200, ['url' => $url]);
    }
    function store_url(){
        $token = $this->Api_model->get_store_url($this->LOG_USER_ID);
        if(!$token){
            $token = $this->Api_model->create_oc_token($this->LOG_USER_ID,$this->rest->key);
        }else{
            $token = $token['token'];
        }
        $table_prefix = str_replace("_", "", $this->db->dbprefix);
        $url = STORE_URL . "index.php?route=common/home&key=$token&id=$table_prefix";
        return $url;
    }
    function register_url(){
        $token = $this->Api_model->get_store_url($this->LOG_USER_ID);
        if(!$token){
            $token = $this->Api_model->create_oc_token($this->LOG_USER_ID,$this->rest->key);
        }else{
            $token = $token['token'];
        }
        $table_prefix = str_replace("_", "", $this->db->dbprefix);
        $url = STORE_URL . "index.php?route=register/mlm&key=$token&id=$table_prefix";
        return $url;   
    }
    public function notificationDetails_get()
    {
        $user_id = $this->LOG_USER_ID;
        

        $this->load->model('document_model', '', true);
        $this->load->model('news_model', '', true);
        $this->load->model('payout_model', '', true);
        $this->load->model('epin_model', '', true);

        $payout_release_type = $this->MODULE_STATUS['payout_release_status'];
        $payoutData = [];
        if ($payout_release_type == "ewallet_request") {
            $payoutData = $this->payout_model->userPayoutRequest($user_id, "released", date("Y-m-d H:i:s", strtotime("-10 days")), 1);
        }
        // $pin_count = 0;
        // if ($this->MODULE_STATUS['pin_status'] == "yes") {
        //     $this->load->model('epin_model', '', true);
        //     $pin_count = (int)$this->epin_model->getUserPinRequestCount($user_id, 'no', 1);
        // }
        $documents  = $this->document_model->getUnreadDocuments($user_id, date("Y-m-d H:i:s", strtotime("-10 days")));
        $news       = $this->news_model->getNewsDetails($user_id, date("Y-m-d H:i:s", strtotime("-10 days")));

        $notificationDocuments  = [];
        $notificationNews       = [];
        $notificationPayout     = [];
        foreach ($payoutData as $key => $value) {
            $notificationPayout[$key]['title']   = "Payout Released";
            $notificationPayout[$key]['subject'] = "Your payout of ".format_currency($value->requested_amount)." has been released successfully."; 
            $notificationPayout[$key]['date']    = $value->updated_date; 
            $notificationPayout[$key]['sender']  = ""; 
        }

        foreach ($documents as $key => $value) {
            $notificationDocuments[$key]['title']   = "New file uploaded- ".$value->file_title;
            $notificationDocuments[$key]['subject'] = $value->doc_desc; 
            $notificationDocuments[$key]['date']    = $value->uploaded_date; 
            $notificationDocuments[$key]['sender']  = ""; 
        }
        foreach ($news as $key => $value) {
            $notificationNews[$key]['title']   = $value->news_title;
            $notificationNews[$key]['subject'] = $value->news_desc; 
            $notificationNews[$key]['date']    = $value->news_date; 
            $notificationNews[$key]['sender']  = ""; 
        }

        $notificationCount = count($notificationNews) + count($notificationDocuments) + count($notificationPayout);
        // $notification_count = $payout_count + $pin_count + $document_count + $news_count;
        $notification_details = compact('notificationDocuments', 'notificationNews', 'notificationPayout', 'notificationCount');

        $this->set_success_response(200, $notification_details);
    }

    public function chckeDemo_get(){
        
        if(DEMO_STATUS == "yes"){
            $loggin_id      = $this->LOG_USER_ID;
            $admin_id       =  $this->validation_model->getAdminId();
            $is_preset_demo = $this->validation_model->isPresetDemo($admin_id);

            $data['is_preset_demo'] = ($is_preset_demo) ? "yes" : "no";
        } else {
            $data['is_preset_demo'] = "no";
        }
        $this->set_success_response(200, $data);

    }


}
