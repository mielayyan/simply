<?php

class report_model extends inf_model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('validation_model');
        $this->load->model('home_model');
    }

    public function getProfileDetails($user_name)
    {

        $user_id = $this->validation_model->userNameToID($user_name);
        $user_type = $this->validation_model->getUserType($user_id);

        $this->db->select("u.*,f1.user_name");
        $this->db->from('user_details as u');
        if ($user_type == 'admin') {
            $this->db->join("ft_individual AS f1", "u.user_detail_refid=f1.id", 'INNER');
        } else {
            $this->db->join("ft_individual", "u.user_detail_refid=ft_individual.id", 'INNER');
            $this->db->join("ft_individual as f1", "f1.id=ft_individual.sponsor_id", 'LEFT');
        }
        $this->db->where("user_detail_refid", $user_id);
        $query = $this->db->get();
        $profile_arr['details'] = $this->getUserDetailsArray($query);
        return $profile_arr;
    }

    public function getMemberPayout($user_mob_name)
    {
        $member_payout_details = array();
        $this->load->model('leg_class_model');
        $user_id = $this->validation_model->userNameToID($user_mob_name);

        $this->db->select_sum('total_leg', 'total_leg');
        $this->db->select_sum('total_amount', 'total_amount');
        $this->db->select_sum('amount_payable', 'amount_payable');
        $this->db->select_sum('tds', 'tds');
        $this->db->select_sum('service_charge', 'service_charge');
        $this->db->select('user_id');
        $this->db->from('leg_amount');
        $this->db->where('user_id', $user_id);
        $this->db->group_by('user_id');
        $this->db->select('user.user_detail_acnumber,user.user_detail_nbank,user.user_detail_nbranch,user.user_detail_pan,user.user_detail_address');
        $this->db->join('user_details as user', 'user.user_detail_refid=leg_amount.user_id', 'INNER');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $member_payout_details["user_id"] = $row['user_id'];
            $member_payout_details["user_name"] = $this->validation_model->IdToUserName($row['user_id']);
            $member_payout_details["user_id"] = $row['user_id'];
            $member_payout_details["full_name"] = $this->validation_model->getFullName($row['user_id']);
            $member_payout_details["left_leg"] = $this->leg_class_model->getLeftLegCount($row['user_id']);
            $member_payout_details["right_leg"] = $this->leg_class_model->getRightLegCount($row['user_id']);
            $member_payout_details["total_leg"] = $row['total_leg'];
            $member_payout_details["total_amount"] = $row['total_amount'];
            $member_payout_details["amount_payable"] = round($row['amount_payable'], $this->PRECISION);
            $member_payout_details["tds"] = round($row['tds'], $this->PRECISION);
            $member_payout_details["service_charge"] = round($row['service_charge'], $this->PRECISION);
            $member_payout_details["user_pan"] = $row['user_detail_pan'];
            if ($row['user_detail_acnumber'])
                $member_payout_details["acc_number"] = $row['user_detail_acnumber'];
            else
                $member_payout_details["acc_number"] = 'NA';
            if ($row['user_detail_nbank'])
                $member_payout_details["user_bank"] = $row['user_detail_nbank'];
            else
                $member_payout_details["user_bank"] = 'NA';

            if ($row['user_detail_address'])
                $member_payout_details["user_address"] = $row['user_detail_address'];
            else
                $member_payout_details["user_address"] = 'NA';
        }
        return $member_payout_details;
    }

    public function getUserDetails($user_id)
    {
        $this->load->model('country_state_model');
        $this->db->select('*');
        $this->db->from('user_details');
        $this->db->where('user_detail_refid', $user_id);
        $query = $this->db->get();
        $num = $query->num_rows();
        $i = 1;
        foreach ($query->result_array() as $row) {
            $user_detail["detail$i"]["id"] = $row["user_detail_refid"];
            $user_detail["detail$i"]["name"] = $row["user_detail_name"];
            $user_detail["detail$i"]["address"] = $row["user_detail_address"];
            $user_detail["detail$i"]["country"] = $this->country_state_model->getCountryNameFromId($row["user_detail_country"]);
            $user_detail["detail$i"]["state"] = $row["user_detail_state"];
            if ($row["user_detail_pin"] != '0' || $row["user_detail_pin"] != 'NA')
                $user_detail["detail$i"]["pincode"] = $row["user_detail_pin"];
            else
                $user_detail["detail$i"]["pincode"] = 'NA';
            $user_detail["detail$i"]["mobile"] = $row["user_detail_mobile"];
            $user_detail["detail$i"]["land"] = $row["user_detail_land"];
            $user_detail["detail$i"]["email"] = $row["user_detail_email"];
            $user_detail["detail$i"]["dob"] = $row["user_detail_dob"];
            $user_detail["detail$i"]["gender"] = $row["user_detail_gender"];
            $user_detail["detail$i"]["acnumber"] = $row["user_detail_acnumber"];
            $user_detail["detail$i"]["ifsc"] = $row["user_detail_ifsc"];
            $user_detail["detail$i"]["nbank"] = $row["user_detail_nbank"];
            $user_detail["detail$i"]["nbranch"] = $row["user_detail_nbranch"];
            $user_detail["detail$i"]["pan"] = $row["user_detail_pan"];
            $user_detail["detail$i"]["level"] = $row["user_level"];
            $user_detail["detail$i"]["date"] = $row["join_date"];
            $user_detail["detail$i"]["referral"] = $row["user_details_ref_user_id"];
            $i++;
        }
        $user_detail = $this->replaceNullFromArray($user_detail, "NA");
        return $user_detail;
    }

    public function userNameToID($user_name)
    {
        $user_id = $this->validation_model->userNameToID($user_name);
        return $user_id;
    }

    public function getUserDetailsArray($qr)
    {
        $this->load->model('country_state_model');
        foreach ($qr->result_array() as $row) {

            $user_detail[] = $row;
        }
        $user_detail[0]['user_detail_country'] = $this->country_state_model->getCountryNameFromId($user_detail[0]['user_detail_country']);
        $user_detail[0]['user_detail_state'] = $this->country_state_model->getStateNameFromId($user_detail[0]['user_detail_state']);
        $user_detail = $this->replaceNullFromArray($user_detail, "NA", false);
        return $user_detail;
    }

    public function replaceNullFromArray($user_detail, $replace = '', $last_name_replace = true)
    {
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
                if ($user_detail["$fild"]["$key_field"] == "" || $user_detail["$fild"]["$key_field"] == "0") {
                    if ($key_field != 'user_detail_second_name') {
                        $user_detail["$fild"]["$key_field"] = $replace;
                    } else {
                        if ($last_name_replace) {
                            $array["$fild"]["$key_field"] = $replace;
                        }
                    }
                }
            }
        }
        return $user_detail;
    }

    public function getTodaysJoining($today)
    {
        require_once 'Joining_class_model.php';
        $obj_join = new joining_class_model();
        $obj_join->todaysJoining($today);
        $arr = $obj_join->today_join;
        return $arr;
    }
    

    public function getWeeklyJoining($from = '', $to = '',$offset = '', $row_count = '')
    {
        $this->load->model('joining_class_model');
        $date = date("Y-m-d H:i:s");

        if (!isset($to) || trim($to) === '') {
            $to = $date;
        }
        $arr = $this->getJoiningReport($from, $to,$offset, $row_count);
        // $arr = $this->joining_class_model->weeklyJoining($from, $to);
        return $arr;
    }

    public function getJoiningReport($from = '', $to = '', $page = '', $limit = '')
    {
        $this->load->model('joining_class_model');
        $this->db->select('ft.id,ft.user_name,ft.father_id,ft.date_of_joining,ft.first_pair,ft.delete_status, reg.product_id as reg_prod_id, reg.product_amount as reg_prod_amount, reg.reg_amount');
        $this->db->from("ft_individual as ft");
        $this->db->join("infinite_user_registration_details as reg", "reg.user_id=ft.id", "LEFT");
        $this->db->not_like('ft.active', 'terminated', 'after');
        if($from) {
            $this->db->where('ft.date_of_joining >=', $from);
        }
        if($to) {
            $this->db->where('ft.date_of_joining <=', $to);
        }
        $this->db->order_by("ft.date_of_joining", "asc");
        if ($limit) {
            $this->db->limit($limit, $page);
        }        
        $query = $this->db->get();
        $this->weekly_join = [];
        $cnt = $query->num_rows();
        if ($cnt > 0) {
            $i = 0;
            foreach ($query->result_array() as $search_active) {

                $this->weekly_join["detail$i"]["id"] = $search_active['id'];
                $this->weekly_join["detail$i"]["country"] = $this->validation_model->getUserCountryName($search_active['id']);
                $this->weekly_join["detail$i"]["user_name"] = $search_active['user_name'];
                $this->weekly_join["detail$i"]["delete_status"] = $search_active['delete_status'];
                // $this->weekly_join["detail$i"]["active"] = $search_active['active'];
                $this->weekly_join["detail$i"]["father_id"] = $search_active['father_id'];
                $this->weekly_join["detail$i"]["date_of_joining"] = $search_active['date_of_joining'];
                $this->weekly_join["detail$i"]["first_pair"] = $search_active['first_pair'];
                $this->weekly_join["detail$i"]["user_full_name"]=$this->joining_class_model->userFullName($search_active['id']);
                $this->weekly_join["detail$i"]["sponsor_name"]=$this->joining_class_model->getSponsorId($search_active['user_name']);
                if(!$this->weekly_join["detail$i"]["sponsor_name"])
                    $this->weekly_join["detail$i"]["sponsor_name"] = lang('na');
                $this->weekly_join["detail$i"]["father_user"]=$this->joining_class_model->getUserName($search_active['father_id']);
                if(!$this->weekly_join["detail$i"]["father_user"])
                    $this->weekly_join["detail$i"]["father_user"] = lang('na');
                $this->weekly_join["detail$i"]["paymode"]=lang('na');
                $this->weekly_join["detail$i"]["package_name"]=lang('na');
                if($search_active['reg_prod_id']) {
                    $this->weekly_join["detail$i"]["paymode"]=$this->getUserRegistrationPurchasePaymode($search_active['id']);
                    if(!$this->weekly_join["detail$i"]["paymode"])
                        $this->weekly_join["detail$i"]["paymode"] = lang('na');
                    $this->weekly_join["detail$i"]["package_name"]=$this->getPackageName($search_active['reg_prod_id']);
                    if(!$this->weekly_join["detail$i"]["package_name"])
                        $this->weekly_join["detail$i"]["package_name"] = lang('na');
                }
                $this->weekly_join["detail$i"]["package_amount"]=$search_active['reg_prod_amount'];
                $this->weekly_join["detail$i"]["reg_amount"]=$search_active['reg_amount'];
                $i++;
            }
        }
        return $this->weekly_join;
    }
    
    function getUserRegistrationPurchasePaymode($user_id) {
        if($this->MODULE_STATUS['opencart_status'] == "yes") {
            $customer_id = $this->validation_model->getOcCustomerId($user_id);
            $where = [
                "order_type" => "register",
                "customer_id" => $customer_id
                ];
            $array = $this->db->select("payment_code")->where($where)->limit(1)->get("oc_order")->result_array();
            if(count($array))
                return lang($array[0]['payment_code']);
        } else {
            $array = $this->db->select("payment_method")->where("user_id", $user_id)->limit(1)->get("sales_order")->result_array();
            if(count($array))
                return lang($array[0]['payment_method']);
        }
        return "";
    }
    
    function getPackageName($prod_id) {
        if($this->MODULE_STATUS['opencart_status'] == "yes") {
            $array = $this->db->select("model")->where("product_id", $prod_id)->limit(1)->get("oc_product")->result_array();
            if(count($array)) {
                return $array[0]['model'];
            }
        } else {
            $array = $this->db->select("product_name")->where("product_id", $prod_id)->limit(1)->get("package")->result_array();
            if(count($array)) {
                return $array[0]['product_name'];
            }
        }
        return '';
    }

    public function getTotalPayout($from_date = "", $to_date = "", $user_id = '',$offset = '', $row_count = '')
    {
        $this->load->model('payout_class_model');
        $date = date("Y-m-d H:i:s");

        if (!isset($to_date) || trim($to_date) === '') {
            $to_date = $date;
        }

        return $this->payout_class_model->getTotalPayout($from_date, $to_date,$user_id,$offset, $row_count);
    }

    public function getPayoutPendingDetails($from_date = '', $to_date = '', $offset = 0,$limit = '')
    {
        $date = date("Y-m-d H:i:s");

        if (!isset($to_date) || trim($to_date) === '') {
            $to_date = $date;
        }
        $payout_settings = $this->getPayoutSettings();

        $payout_type = $payout_settings['payout_type'];
        $min_payout = $payout_settings['min_payout'];
        $max_payout = $payout_settings['max_payout'];

        $details = array();
        if ($payout_type == "ewallet_request" || $payout_type == "both") {
            $this->db->select('f.user_name paid_user_id,f.delete_status,p.requested_amount paid_amount,requested_date as paid_date,p.status,p.payout_fee');
            $this->db->select("CONCAT(u.user_detail_name,' ',u.user_detail_second_name) full_name");
            $this->db->from('payout_release_requests p');
            $this->db->join('ft_individual f', 'p.requested_user_id=f.id');
            $this->db->join('user_details u', 'p.requested_user_id=u.user_detail_refid');
            if($from_date)
                $this->db->where("p.requested_date >=", $from_date);
            if($to_date)
                $this->db->where("p.requested_date <=", $to_date);
            $this->db->where("p.status", 'pending');
            $this->db->where("f.delete_status", 'active');
            if($this->LOG_USER_TYPE=="user")
            {
            $this->db->where('f.id =', $this->LOG_USER_ID); 
            }
            //if($limit != '' && $offset != '')
            $query = $this->db->get();
            $details1 = $query->result_array();
        }
        if ($payout_type == "from_ewallet" || $payout_type == "both") {
            $this->db->select('f.user_name paid_user_id,NOW() as paid_date,f.delete_status');
            $this->db->select("'pending' AS status");
            $this->db->select("IF(balance_amount > {$max_payout}, '{$max_payout}', balance_amount) paid_amount");
            $this->db->select("CONCAT(u.user_detail_name,' ',u.user_detail_second_name) full_name");
            $this->db->from('user_balance_amount b');
            $this->db->join('ft_individual f', 'b.user_id=f.id');
            $this->db->join('user_details u', 'b.user_id=u.user_detail_refid');
            $this->db->where("b.balance_amount >=", $min_payout);
            $this->db->where('f.user_type !=', 'admin');
            $this->db->where("f.delete_status", 'active');
            if($from_date)
                $this->db->where("NOW() >=", $from_date);
            if($to_date)
                $this->db->where("NOW() <=", $to_date);
            if($this->LOG_USER_TYPE=="user")
            {
            $this->db->where('f.id =', $this->LOG_USER_ID); 
            }
            //if($limit != '' && $offset != '')
            $query = $this->db->get();
            $details2 = $query->result_array();
            foreach ($details2 as $key => $value) {
                $details2[$key]['payout_fee'] = 0;
            }
        }
        // dd($details2);

        if ($payout_type == "both") {
            $details = array_merge($details1, $details2);
        } else {
            if ($payout_type == "ewallet_request") {
                $details = $details1;
            }
            if ($payout_type == "from_ewallet") {
                $details = $details2;
            }
        }
        return $details;
    }

    public function getReleasedPayoutDetails($from_date = '', $to_date = '', $offset = 0, $limit = 10)
    {

        if ($to_date) {
            $from_date = $from_date . " 00:00:00";
            $to_date = $to_date . " 23:59:59";
        } else {
            $date = date("Y-m-d H:i:s");
            $to_date = $date;
            $from_date = $from_date . " 00:00:00";
        }
        $details = array();
        $this->db->select('a.paid_id, a.paid_user_id, a.paid_amount, a.paid_date, a.paid_type, a.paid_status, a.payout_fee, f.user_name, f.delete_status');
        $this->db->from('amount_paid as a');
        $this->db->join("ft_individual AS f", 'f.id=a.paid_user_id', 'INNER');
        $this->db->where('f.active', 'yes');
        $this->db->where('a.paid_status', 'yes');

        if ($from_date && $to_date) {
            $this->db->where("paid_date >=", $from_date);
            $this->db->where("paid_date <=", $to_date);
        }
        if($this->LOG_USER_TYPE=="user") {
            $this->db->where('f.id =', $this->LOG_USER_ID); 
        }
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $details["detail$i"]["paid_id"]=$row['paid_id'];
            // $details["detail$i"]["paid_user_id"] = $this->validation_model->IdToUserName($row['paid_user_id']);
            $details["detail$i"]["paid_user_id"] = $row['user_name'];
            $details["detail$i"]["full_name"] = $this->validation_model->getUserFullName($row['paid_user_id']);
            $details["detail$i"]["paid_amount"] = $row['paid_amount'];
            $details["detail$i"]["paid_date"] = date('Y-m-d H:i:s', strtotime($row['paid_date']));
            $details["detail$i"]["paid_type"] = $row['paid_type'];
            //mark as paid
            $details["detail$i"]["paid_status"] = $row['paid_status'];
            $details["detail$i"]["payout_fee"] = $row['payout_fee'];
            $details["detail$i"]["paid_user_name"] = $row['user_name'];
            $details["detail$i"]["delete_status"] = $row['delete_status'];
            //
            $i++;
        }

        return $details;
    }

    public function profileReport($cnt)
    {
        $this->load->model('country_state_model');
        $user_detail = array();
        $this->db->select("user_name");
        $this->db->from("ft_individual");
        $this->db->where("active", "yes");
        $this->db->limit($cnt);
        $query = $this->db->get();

        foreach ($query->result() as $row) {
            $u_name = $row->user_name;
            $user_id = $this->validation_model->userNameToID($u_name);

            $this->db->select('u.*');
            $this->db->from("user_details AS u");
            $this->db->join("ft_individual", "u.user_detail_refid=ft_individual.id", 'INNER');
            $this->db->where("user_detail_refid", $user_id);
            $query = $this->db->get();

            foreach ($query->result_array() as $rows) {
                $sponser_data = $this->validation_model->getSponserIdUserName($user_id);
                $rows['sponser_name'] = $sponser_data['name'];
                $rows['sponser_id'] = $sponser_data['id'];
                if($rows['user_detail_country']) {
                    $rows['user_detail_country'] = $this->country_state_model->getCountryNameFromId($rows['user_detail_country']);
                }
                $rows['uname'] = $row->user_name;
                $user_detail[] = $rows;
            }
            $user_detail = $this->replaceNullFromArray($user_detail, "NA", false);
        }

        return $user_detail;
    }

    public function profileReportFromTo($count_to, $count_from)
    {
        $this->load->model("country_state_model");
        $user_detail = array();
        $this->db->select("user_name");
        $this->db->from("ft_individual");
        $this->db->where("active", "yes");
        $this->db->limit($count_to, $count_from - 1);
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result() as $row) {
            $u_name = $row->user_name;
            $user_id = $this->validation_model->userNameToID($u_name);

            $this->db->select("u.*");
            $this->db->from("user_details AS u");
            $this->db->join("ft_individual", "u.user_detail_refid=ft_individual.id", "INNER");
            $this->db->where("user_detail_refid", $user_id);
            $query = $this->db->get();

            foreach ($query->result_array() as $rows) {
                $sponser_data = $this->validation_model->getSponserIdUserName($user_id);
                $rows['sponser_name'] = $sponser_data['name'];
                $rows['sponser_id'] = $sponser_data['id'];
                $rows['uname'] = $row->user_name;
                if($rows['user_detail_country']) {
                    $rows['user_detail_country'] = $this->country_state_model->getCountryNameFromId($rows['user_detail_country']);
                }
                $user_detail[] = $rows;
            }

            $user_detail = $this->replaceNullFromArray($user_detail, "NA");
        }

        return $user_detail;
    }

    public function getPayoutType()
    {
        $payout_type = "from_ewallet";

        $this->db->select("payout_release");
        $this->db->from("configuration");
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $payout_type = $row->payout_release;
        }
        return $payout_type;
    }

    public function getPayoutSettings()
    {
        $payout_type = "from_ewallet";
        $min_payout = 1;
        $max_payout = 100;
        $this->db->select("payout_release,min_payout,max_payout");
        $this->db->from("configuration");
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $payout_type = $row->payout_release;
            $min_payout = $row->min_payout;
            $max_payout = $row->max_payout;
        }
        return array("payout_type" => $payout_type, "min_payout" => $min_payout, "max_payout" => $max_payout);
    }

    function rankedUsers($ranks, $from_date = '', $to_date = '',$offset='',$limit='')
    {
        $date = date("Y-m-d H:i:s");

        if (!isset($to_date) || trim($to_date) === '') {
            $to_date = $date;
        }

        $i = 0;
        $rank_details = array();
        if ($ranks != '') {
            // foreach ($ranks as $rank) {
                $this->db->select('user_id,new_rank,date');
                $this->db->from('rank_history');
                $this->db->where_in("new_rank", $ranks);
                if ($from_date != '') {
                    $this->db->where("date >=", $from_date);
                }
                if ($to_date != '') {
                    $this->db->where("date <=", $to_date);
                }
                if($limit != 0) {
                    $this->db->limit($limit,$offset);    
                }
                $query = $this->db->get();
                foreach ($query->result() as $row) {
                    $rank_details[$i]["rank_name"] = $this->getRank($row->new_rank);
                    $rank_details[$i]["user_name"] = $this->validation_model->IdToUserName($row->user_id);
                    $rank_details[$i]["user_detail_name"] = $this->userFullName($row->user_id);
                    $rank_details[$i]["date"] = $row->date;
                    $i++;
                }
            // }
        } else {
            $this->db->select('user_id,new_rank,date');
            $this->db->from('rank_history');
            if ($from_date != '') {
                $this->db->where("date >=", $from_date);
            }
            if ($to_date != '') {
                $this->db->where("date <=", $to_date);
            }
            if($limit != 0) {
                $this->db->limit($limit,$offset);    
            }

            $query = $this->db->get();
            foreach ($query->result() as $row) {
                $rank_details[$i]["rank_name"] = $this->getRank($row->new_rank);
                $rank_details[$i]["user_name"] = $this->validation_model->IdToUserName($row->user_id);
                $rank_details[$i]["user_detail_name"] = $this->userFullName($row->user_id);
                $rank_details[$i]["date"] = $row->date;
                $i++;
            }
        }
        return $rank_details;
    }

    function getRank($rank_id)
    {

        $rank_name = '';
        $this->db->select('rank_name');
        $this->db->from('rank_details');
        $this->db->where('rank_id', "$rank_id");
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $rank_name = $row->rank_name;
        }
        return $rank_name;
    }

    function userFullName($user_id)
    {
        $user_full_name = '';
        $this->db->select('user_detail_name,user_detail_second_name');
        $this->db->from('user_details');
        $this->db->where('user_detail_refid', "$user_id");
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $user_full_name = $row->user_detail_name;
            $user_full_name .= " " . $row->user_detail_second_name;
        }
        return $user_full_name;
    }

    public function SalesReport($from_date = '', $to_date = '', $package = '', $limit = '', $offset = '')
    {
        $module_status = $this->MODULE_STATUS;
        $date = date("Y-m-d H:i:s");

        if (!isset($to_date) || trim($to_date) === '') {
            $to_date = $date;
        }

        if ($module_status['opencart_status'] == 'yes') {
            $this->db->select("o.order_id invoice_no,op.model prod_id,f.user_name user_id,o.total amount,o.payment_code payment_method,'' pending_id");
            $this->db->from('oc_order o');
            $this->db->join('oc_order_product op', 'o.order_id=op.order_id');
            $this->db->join('ft_individual f', 'o.customer_id=f.oc_customer_ref_id');
            if ($from_date != '') {
                $this->db->where('o.date_added >=', $from_date);
            }
            if ($to_date != '') {
                $this->db->where('o.date_added <=', $to_date);
            }
            if($this->LOG_USER_TYPE=="user")
            {
                $this->db->where('f.id =', $this->LOG_USER_ID); 
            }
            if($package != ''){
                $this->db->where('op.product_id', $package);
            }
            $this->db->where('o.order_status_id >=', 1);
            $this->db->where('o.order_type', 'register');
            if($limit != '' && $offset != '')
                $this->db->limit($limit, $offset);
            $query = $this->db->get();
            return $query->result_array();
        } else {
            $this->db->select("s.invoice_no,p.product_name prod_id,f.user_name user_id,s.payment_method,s.pending_id,ur.total_amount as amount");
            $this->db->from('sales_order s');
            $this->db->join('package p', 's.prod_id=p.prod_id');
            $this->db->join('ft_individual f', 's.user_id=f.id');
            $this->db->join('infinite_user_registration_details ur', 'f.id=ur.user_id');
            if ($from_date != '') {
                $this->db->where('s.date_submission >=', $from_date);
            }
            if ($to_date != '') {
                $this->db->where('s.date_submission <=', $to_date);
            }
            if($this->LOG_USER_TYPE=="user") {
                $this->db->where('f.id =', $this->LOG_USER_ID); 
            }
            if($package != '') {
                $this->db->where('p.product_id', $package);
            }
            $this->db->where('p.type_of_package', 'registration');
            $this->db->where('s.pending_id IS NULL');
            if($limit != '' && $offset != '')
                $this->db->limit($limit, $offset);
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function IdToUserNamePendingRegistration($id)
    {
        $this->db->select('user_name');
        $this->db->where('id', $id);
        $this->db->where('status', 'pending');
        $query = $this->db->get('pending_registration');
        return $query->row_array()['user_name'];
    }

    function productSalesReport($product_id)
    {
        $module_status = $this->MODULE_STATUS;

        if ($module_status['opencart_status'] == 'yes') {
            $this->db->select("o.order_id invoice_no,op.model prod_id,f.user_name user_id,op.total amount,o.payment_code payment_method,'' pending_id");
            $this->db->from('oc_order o');
            $this->db->join('oc_order_product op', 'o.order_id=op.order_id');
            $this->db->join('ft_individual f', 'o.customer_id=f.oc_customer_ref_id');
            if ($product_id == 'all') { } else {
                $this->db->where('op.product_id', $product_id);
            }
            if($this->LOG_USER_TYPE=="user")
            {
                $this->db->where('f.id =', $this->LOG_USER_ID); 
            }
            $this->db->where('o.order_status_id >=', 1);
            $this->db->where('o.order_type', 'register');
            $query = $this->db->get();
            return $query->result_array();
        } else {
            $this->db->select("s.invoice_no,p.product_name prod_id,f.user_name user_id,s.amount,s.payment_method,s.pending_id");
            $this->db->from('sales_order s');
            $this->db->join('package p', 's.prod_id=p.prod_id');
            $this->db->join('ft_individual f', 's.user_id=f.id');
            if ($product_id == 'all') { } else {
                $this->db->where('p.product_id', $product_id);
            }
            if($this->LOG_USER_TYPE=="user")
            {
                $this->db->where('f.id =', $this->LOG_USER_ID); 
            }
            $this->db->where('p.type_of_package', 'registration');
            $this->db->where('s.pending_id IS NULL');
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    function productRepurchaseSalesReport($product_id, $from_date = "", $to_date = "", $limit = '', $offset = '')
    {
        $module_status = $this->MODULE_STATUS;
        $date = date("Y-m-d H:i:s");
        if (!isset($to_date) || trim($to_date) === '') {
            $to_date = $date;
        }

        if ($module_status['opencart_status'] == 'yes') {
            $this->db->select("o.order_id invoice_no,op.model prod_id,f.user_name user_id,o.total amount,o.payment_code payment_method,'' pending_id");
            $this->db->from('oc_order o');
            $this->db->join('oc_order_product op', 'o.order_id=op.order_id');
            $this->db->join('ft_individual f', 'o.customer_id=f.oc_customer_ref_id');
            if ($product_id == 'all') {
                if ($from_date != '') {
                    $this->db->where('o.date_added >=', $from_date);
                }
                if ($to_date != '') {
                    $this->db->where('o.date_added <=', $to_date);
                }
            } else if($product_id != '') {
                $this->db->where('op.product_id', $product_id);
            }
            if($this->LOG_USER_TYPE=="user")
            {
                $this->db->where('f.id =', $this->LOG_USER_ID); 
            }
            $this->db->where('o.order_status_id >=', 1);
            $this->db->where('o.order_type', 'purchase');
            if($limit != '' && $offset != '')
                $this->db->limit($limit,$offset);
            $query = $this->db->get();

            return $query->result_array();
        } else {
            $this->db->select("ro.invoice_no,p.product_name prod_id,f.user_name user_id,(rd.amount*rd.quantity) amount,ro.payment_method,'' pending_id");
            $this->db->from('repurchase_order ro');
            $this->db->join("repurchase_order_details rd", "rd.order_id=ro.order_id");
            $this->db->join('package p', 'rd.prod_id=p.prod_id');
            $this->db->join('ft_individual f', 'ro.user_id=f.id');
            $this->db->where('ro.order_status', 'confirmed');
            if ($product_id == 'all') {
                if ($from_date != '') {
                    $this->db->where('ro.order_date >=', $from_date);
                }
                if ($to_date != '') {
                    $this->db->where('ro.order_date <=', $to_date);
                }
            } else if($product_id != ''){
                $this->db->where('p.product_id', $product_id);
            }
            if($this->LOG_USER_TYPE=="user")
            {
                $this->db->where('f.id =', $this->LOG_USER_ID); 
            }
            $this->db->where('p.type_of_package', 'repurchase');
            if($limit != '' && $offset != '')
                $this->db->limit($limit,$offset);
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    function getCommisionDetails($type, $from_date = '', $to_date = '', $user_id = '', $limit = '', $offset = '')
    {
        $date = date("Y-m-d H:i:s");
        
        $i = 0;
        $details = array();
        $count = 0;
        if($type)
            $count = count($type);
        $this->db->select('user_id,from_id,amount_type,tds,service_charge,date_of_submission,f.delete_status, f.user_name');
        $this->db->select("total_amount,amount_payable");
        $this->db->from('leg_amount as l');
        $this->db->join('ft_individual as f', 'f.id=l.user_id', 'INNER');
        if ($type != '') {
            $this->db->where_in('amount_type', $type);
        }
        if ($from_date != '') {
            $this->db->where("date_of_submission >=", $from_date);
        }
        if ($to_date != '') {
            $this->db->where("date_of_submission <=", $to_date);
        }
        if ($user_id != '') {
            $this->db->where("user_id", $user_id);
        } 
        else {
           // $this->db->group_by('user_id');
        }
        //$this->db->group_by('amount_type');
       // if($limit != '' && $offset != '')
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        $num_rows = $query->num_rows();
        if ($num_rows > 0) {
            foreach ($query->result_array() as $row) {
                $details[$i]['user_name'] = $row['user_name'];
                $details[$i]['full_name'] = $this->validation_model->getFullName($row['user_id']);
                $view_amt = $this->validation_model->getViewAmountType($row['amount_type']);
                $details[$i]['amount_type'] = $row['amount_type'];
                $details[$i]['tds'] = $row['tds'];
                $details[$i]['service_charge'] = $row['service_charge'];
                $details[$i]['view_amt'] = $view_amt;
                $details[$i]['date'] = $row['date_of_submission'];
                $details[$i]['total_amount'] = $row['total_amount'];
                $details[$i]['amount_payable'] = $row['amount_payable'];
                $details[$i]['delete_status'] = $row['delete_status'];
                $i = $i + 1;
            }
        }


        return $details;
    }

    function getUsedPin()
    {
        $i = 0;
        $detail = array();
        $this->db->select('*');
        $this->db->from('pin_used');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {

            $detail[$i]["id"] = $row['pin_id'];
            $detail[$i]["pin_number"] = $row['pin_number'];
            $detail[$i]["used_user"] = $this->validation_model->IdToUserName($row['used_user']);
            $detail[$i]["pending_id"] = $row['pending_id'];
            if ($row['pending_id']) {
                $detail[$i]["used_user"] = $this->IdToUserNamePendingRegistration($row['pending_id']);
            }
            $detail[$i]["pin_alloc_date"] = $row['pin_alloc_date'];
            $detail[$i]["pin_amount"] = $row['pin_amount'];
            $detail[$i]['pin_balance_amount'] = $row['pin_balance_amount'];
            $i++;
        }
        return $detail;
    }

    public function getAciveDeactiveUserDetails($start_date, $to_date,$offset = 1, $row_count = 2)
    {
        $details = array();
        $this->db->select('h.*, f.user_name');
        $this->db->from('user_activation_deactivation_history as h');
        $this->db->join("ft_individual as f", "f.id=h.user_id", "INNER");
        $this->db->where('f.delete_status', 'active');
        if($start_date != ''){
            $this->db->where('time >=', $start_date);
        }
        if($to_date != ''){
            $this->db->where('time <=', $to_date);
        }
        // $this->db->limit($row_count, $offset);
        $query = $this->db->get();
        // dd($this->db->last_query());
        $i = 0;
        foreach ($query->result_array() as $row) {
            $details["$i"]["user_name"] = $row['user_name'];
            $details["$i"]["full_name"] = $this->validation_model->getUserFullName($row['user_id']);
            $details["$i"]["status"] = ucfirst($row['status']);
            $date = strtotime($row['time']);
            $details["$i"]["date"] = date("d M Y - h:i:s A", $date);
            $i++;
        }
        return $details;
    }

    public function getDailyActivateDeactivateReport($date)
    {
        $details = array();
        $start_date = $date . " 00:00:00";
        $to_date = $date . " 23:59:59";
        $this->db->select('*');
        $this->db->from('user_activation_deactivation_history');
        $this->db->where('time >=', $start_date);
        $this->db->where('time <=', $to_date);
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {

            $details["$i"]["user_name"] = $this->validation_model->IdToUserName($row['user_id']);
            $details["$i"]["full_name"] = $this->validation_model->getUserFullName($row['user_id']);
            $details["$i"]["status"] = ucfirst($row['status']);
            $date = strtotime($row['time']);
            $details["$i"]["date"] = date("Y-m-d", $date);
            $i++;
        }
        return $details;
    }

    public function getRepurchaseDetails($week_date1 = '', $week_date2 = '', $user_id = "", $limit = '', $offset= '')
    {
        $details = array();

        $product_details = array();
        $date = date("Y-m-d H:i:s");

        if (!isset($week_date2) || trim($week_date2) === '') {
            $to_date = $date;
        } else {
            $to_date = $week_date2 . " 23:59:59";
        }
        if ($week_date1 != '') {
            $start_date = $week_date1 . " 00:00:00";
        } else {
            $start_date = '';
        }
        $this->db->select('r.*, f.delete_status, f.user_name');
        $this->db->from('repurchase_order as r');
        $this->db->join('ft_individual as f', 'f.id=r.user_id', 'INNER');
        $this->db->where('order_status', 'confirmed');
        if ($start_date != '') {
            $this->db->where('order_date >=', $start_date);
        }
        if ($to_date != '') {
            $this->db->where('order_date <=', $to_date);
        }
        if ($user_id) {
            $this->db->where("user_id", $user_id);
        }
        if($limit != '' && $offset != '')
            $this->db->limit($limit, $offset);
        $query = $this->db->get();
        $i = 0;
        $j = 0;
        foreach ($query->result_array() as $row) {
            $j = 0;
            $this->db->select('*');
            $this->db->from('repurchase_order_details');
            $this->db->where('order_id', $row['order_id']);
            $result = $this->db->get();

            $details["$i"]["product_count"] = count($result->result_array());

            foreach ($result->result_array() as  $product_data) {

                $this->db->select('product_name');
                $this->db->from('package');
                $this->db->where('prod_id', $product_data['prod_id']);
                $this->db->limit(1);
                $query = $this->db->get();
                foreach ($query->result() as $prod_name) {
                    $prod_name = $prod_name->product_name;
                }
                $details["$i"]["product_details"]["$j"]["unite_price"] = $product_data['amount'];
                $details["$i"]["product_details"]["$j"]["quantity"] = $product_data['quantity'];
                $details["$i"]["product_details"]["$j"]["prod_id"] = $prod_name;
                $details["$i"]["product_details"]["$j"]["total"] = ($product_data['amount'] * $product_data['quantity']);
                $j++;
            }

            $details["$i"]["id"] = $row['order_id'];
            $details["$i"]["user_name"] = $row['user_name'];
            $details["$i"]["full_name"] = $this->validation_model->getUserFullName($row['user_id']);

            $details["$i"]["invoice_no"] = $row['invoice_no'];
            $details["$i"]["order_date"] = $row['order_date'];
            $details["$i"]["amount"] = $row['total_amount'];
            $details["$i"]["delete_status"] = $row['delete_status'];
            $details["$i"]["payment_method"] = ucfirst($row['payment_method']);
            $details["$i"]["encrypt_order_id"] = $this->validation_model->encrypt($row['order_id']);
            $i++;
        }

        return $details;
    }

    public function getRpurchaseInvoiceDetails($invoice_order_id)
    {
        $details = array();

        $this->db->select('*');
        $this->db->from('repurchase_order_details');
        $this->db->where("order_id", $invoice_order_id);
        $query = $this->db->get();

        $i = 0;
        foreach ($query->result_array() as $row) {

            $details["$i"]["invoice_no"] = $this->getRepuchaseInvoiceNo($invoice_order_id);
            $details["$i"]["product_name"] = $this->validation_model->getPrdocutName($row['product_id']);
            $details["$i"]["quantity"] = $row['quantity'];
            $details["$i"]["amount"] = $row['amount'];
            $i++;
        }
        return $details;
    }

    public function getRepuchaseInvoiceNo($order_id)
    {

        $invoice_no = 0;
        $this->db->select('invoice_no');
        $this->db->from('repurchase_order');
        $this->db->where("order_id", $order_id);
        $query = $this->db->get();
        foreach ($query->result() as $value) {
            $invoice_no = $value->invoice_no;
        }
        return $invoice_no;
    }

    public function getStairStepDetails($week_date1, $week_date2, $leader_id = "")
    {
        $details = array();
        $start_date = $week_date1 . " 00:00:00";
        $to_date = $week_date2 . " 23:59:59";
        $this->db->select('*');
        $this->db->from('leg_amount');
        if($week_date1 != '')
        $this->db->where('date_of_submission >=', $start_date);
        if($week_date2 != '')
        $this->db->where('date_of_submission <=', $to_date);
        $this->db->where("amount_type", 'stair_step');

        if ($leader_id) {
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
        return $details;
    }

    public function getOverRideDetails($week_date1, $week_date2, $leader_id = "")
    {
        $details = array();
        $start_date = $week_date1 . " 00:00:00";
        $to_date = $week_date2 . " 23:59:59";
        $this->db->select('*');
        $this->db->from('leg_amount');
        $this->db->where('date_of_submission >=', $start_date);
        $this->db->where('date_of_submission <=', $to_date);
        $this->db->where("amount_type", 'override_bonus');

        if ($leader_id) {
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
        return $details;
    }

    //config change report
    function getConfigChanges($from_date, $to_date, $ip_add = '')
    {
        $i = 0;
        $detail = array();
        $this->db->select('*');
        $this->db->from('configuration_change_history');
        if ($from_date != '') {
            $this->db->where('date >=', $from_date);
        }
        if ($to_date != '') {
            $this->db->where('date <=', $to_date);
        }
        if ($ip_add != '') {
            $this->db->where('ip =', $ip_add);
        }
        $this->db->order_by('date', 'desc');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {

            $detail[$i]["id"] = $row['id'];
            $detail[$i]["activity"] = $row['activity'];
            $detail[$i]["done_by"] = $row['done_by'];
            if ($row['done_by_type'] == 'admin' || $row['done_by_type'] == 'user') {
                $detail[$i]["user_name"] = $this->validation_model->getUserName($row['done_by']);
            } else {
                $detail[$i]["user_name"] = $this->validation_model->EmployeeIdToUserName($row['done_by']);
            }
            $detail[$i]["desc"] = $row['description'];
            $detail[$i]["date"] = date('Y-m-d h:i:sa', strtotime($row['date']));
            $detail[$i]["ip"] = $row['ip'];


            $i++;
        }
        return $detail;
    }

    public function getEpinTransferDetails($week_date1 = '', $week_date2 = '', $user_id = "", $to_user_id = "",$offset='',$limit='')
    {
        $this->load->model('Epin_model');
        $details = array();

        $date = date("Y-m-d H:i:s");

        if (!isset($week_date2) || trim($week_date2) === '') {
            $to_date = $date;
        } else {
            $to_date = $week_date2 . " 23:59:59";
        }
        $start_date = $week_date1 . " 00:00:00";
        $this->db->select('e.user_id, e.from_user_id, e.epin_id, e.date, f1.user_name as from_user_name, f2.user_name as to_user_name, f1.delete_status as from_user_delete_status, f2.delete_status as to_user_delete_status');
        $this->db->from('epin_transfer_history as e');
        $this->db->join('ft_individual as f1', 'f1.id=e.from_user_id', 'INNER');
        $this->db->join('ft_individual as f2', 'f2.id=e.user_id', 'INNER');
        if($start_date) {
            $this->db->where('e.date >=', $start_date);
        }
        if($to_date) {
            $this->db->where('e.date <=', $to_date);
        }
        if ($user_id) {
            $this->db->where("from_user_id", $user_id);
        }
        if ($to_user_id) {
            $this->db->where("user_id", $to_user_id);
        }
        if($limit!="" && $offset!="")
        { 
            $this->db->limit($limit,$offset);
        }
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {

            // $details["$i"]["id"] = $row['orde_id'];
            $details["$i"]["to_user_name"] = $row['to_user_name'];
            $details["$i"]["to_full_name"] = $this->validation_model->getUserFullName($row['user_id']);
            $details["$i"]["from_user_name"] = $row['from_user_name'];
            $details["$i"]["from_full_name"] = $this->validation_model->getUserFullName($row['from_user_id']);
            $details["$i"]["epin"] = $this->Epin_model->EpinIdtoName($row['epin_id']);

            $details["$i"]["transfer_date"] = $row['date'];
            $details["$i"]["from_user_delete_status"] = $row['from_user_delete_status'];
            $details["$i"]["to_user_delete_status"] = $row['to_user_delete_status'];
            $i++;
        }
        return $details;
    }

    public function getEpinTransferDetailsForUser($week_date1 = '', $week_date2 = '', $user_id, $to_user_id = "", $page = 0, $limit = '')
    {
        $this->load->model('Epin_model');
        $details = array();
        $date = date("Y-m-d H:i:s");

        if (!isset($week_date2) || trim($week_date2) === '') {
            $to_date = $date;
        } else {
            $to_date = $week_date2 . " 23:59:59";
        }
        if ($week_date1 != '') {
            $start_date = $week_date1 . " 00:00:00";
        } else {
            $start_date = '';
        }
        $this->db->select('et.id,et.user_id,et.from_user_id,et.epin_id,et.date,u.user_detail_name,u.user_detail_second_name');
        $this->db->from('epin_transfer_history et');
        $this->db->join("user_details AS u", "et.from_user_id=u.user_detail_refid", 'INNER');

        if ($to_user_id) {
            $this->db->group_start();
            $this->db->where("from_user_id", $user_id);
            $this->db->where("user_id", $to_user_id);
            $this->db->or_where("from_user_id", $to_user_id);
            $this->db->where("user_id", $user_id);
            $this->db->group_end();
        } else {
            $this->db->group_start();
            $this->db->where("from_user_id", $user_id);
            $this->db->or_where("user_id", $user_id);
            $this->db->group_end();
        }
        if ($start_date != '') {
            $this->db->where('et.date >=', $start_date);
        }
        if ($to_date != '') {
            $this->db->where('et.date <=', $to_date);
        }
        if ($limit) {
            $this->db->limit($limit, $page);
        }
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            if ($row['user_id'] != $user_id) {
                $details["$i"]["user_full_name"] = $row['user_detail_name'] . " " . $row['user_detail_second_name'];
                $details["$i"]["user_name"] = $this->validation_model->IdToUserName($row['user_id']);
                $details["$i"]["type"] = lang('send');
            }
            if ($row['from_user_id'] != $user_id) {
                $details["$i"]["user_full_name"] = $row['user_detail_name'] . " " . $row['user_detail_second_name'];
                $details["$i"]["user_name"] = $this->validation_model->IdToUserName($row['from_user_id']);
                $details["$i"]["type"] = lang('received');
            }
            $details["$i"]["epin"] = $this->Epin_model->EpinIdtoName($row['epin_id']);

            $details["$i"]["transfer_date"] = $row['date'];
            $i++;
        }
        return $details;
    }

    public function getroiDetails($week_date1 = '', $week_date2 = '', $user_id = "")
    {
        $array = array();
        $tot_amount = 0;
        $date = date("Y-m-d H:i:s");

        if (!isset($week_date2) || trim($week_date2) === '') {
            $to_date = $date;
        } else {
            $to_date = $week_date2 . " 23:59:59";
        }
        if ($week_date1 != '') {
            $start_date = $week_date1 . " 00:00:00";
        } else {
            $start_date = '';
        }
        $this->db->select('amount_type,amount_payable,user_id,user_level,from_id,date_of_submission,product_id');
        $this->db->from('leg_amount');
        $this->db->where("amount_type", 'daily_investment');
        if ($start_date != '') {
            $this->db->where('date_of_submission >=', $start_date);
        }
        if ($to_date != '') {
            $this->db->where('date_of_submission <=', $to_date);
        }
        if ($user_id) {
            $this->db->where("user_id", $user_id);
        }

        $query = $this->db->get();
        $i = 1;
        foreach ($query->result_array() as $row) {
            $view_amt_type = $this->validation_model->getViewAmountType($row["amount_type"]);
            $array["det$i"]["amount_type"] = $view_amt_type;
            $array["det$i"]["amount_type_db"] = $row["amount_type"];
            $array["det$i"]["amount_payable"] = $row["amount_payable"];
            if ($row["from_id"]) {
                $array["det$i"]["from_id"] = $this->validation_model->IdToUserName($row["from_id"]);
            } else {
                $array["det$i"]["from_id"] = "NA";
            }

            $array["det$i"]["user_level"] = $row["user_level"];
            $tot_amount += $array["det$i"]["amount_payable"];
            $array["det$i"]['tot_amount'] = $tot_amount;
            $array["det$i"]['date_of_submission'] = $row["date_of_submission"];
            $prod_id = $this->home_model->getPackageId($row['product_id']);
            $prod_name = $this->home_model->getProdName($prod_id);
            $array["det$i"]['package'] = $prod_name;
            $i++;
        }
        return $array;
    }

    //
    public function getGdprDetails($user_name)
    {

        $user_id = $this->validation_model->userNameToID($user_name);
        $user_type = $this->validation_model->getUserType($user_id);

        $this->db->select("u.*,f1.user_name");
        $this->db->from("user_details AS u");
        if ($user_type == 'admin') {
            $this->db->join("ft_individual AS f1", "u.user_detail_refid=f1.id", 'INNER');
        } else {
            $this->db->join("ft_individual", "u.user_detail_refid=ft_individual.id", 'INNER');
            $this->db->join("ft_individual as f1", "f1.id=ft_individual.father_id", 'LEFT');
        }
        $this->db->where("user_detail_refid", $user_id);
        $query = $this->db->get();
        $profile_arr = $this->getGdprUserDetailsArray($query);
        return $profile_arr;
    }

    public function getGdprUserDetailsArray($qr)
    {
        $this->load->model('country_state_model');
        $user_detail = [];
        foreach ($qr->result_array() as $row) {

            $user_detail['about']['full_name'] = (!empty($row['user_detail_second_name'])) ? $row['user_detail_name'] . $row['user_detail_second_name'] : $row['user_detail_name'];
            $user_detail['about']['user_name'] = $row['user_name'];
            $user_detail['about']['address'] = (!empty($row['user_detail_address2'])) ? $row['user_detail_address'] . $row['user_detail_address2'] : $row['user_detail_address'];
            if (!empty($row['user_detail_pin']))
                $user_detail['about']['pincode'] = $row['user_detail_pin'];
            $user_detail['about']['country'] = $this->country_state_model->getCountryNameFromId($row['user_detail_country']);
            if (!empty($row['user_detail_state']))
                $user_detail['about']['state'] = $this->country_state_model->getStateNameFromId($row['user_detail_state']);
            $user_detail['about']['mobile_no'] = $row['user_detail_mobile'];
            if (!empty($row['user_detail_land']))
                $user_detail['about']['land_line_no'] = $this->country_state_model->getStateNameFromId($row['user_detail_land']);
            $user_detail['about']['email'] = $row['user_detail_email'];
            $user_detail['about']['date_of_birth'] = $row['user_detail_dob'];
            $user_detail['about']['gender'] = ($row['user_detail_gender'] == 'M') ? "male" : "female";
            $user_detail['about']['date_of_joining'] = date('Y-m-d', strtotime($row['join_date']));
            $user_detail['about']['referal_count'] = $this->validation_model->getReferalCount($this->LOG_USER_ID);
        }
        $user_detail['commission'] = $this->getCommisionDetails('', '', '', $this->LOG_USER_ID);
        return $user_detail;
    }

    public function getRevenueReportDetails($module_status, $date)
    {

        $amount_credit = 0;
        $amount_debit = 0;

        // registration amount
        $this->db->select_sum('total_amount');
        $this->db->like('reg_date', $date);
        $amount = $this->db->get('infinite_user_registration_details');
        $amount_credit += $amount->row('total_amount');

        // repurchase amount
        if ($module_status['repurchase_status'] == 'yes') {
            $this->db->select_sum('total_amount');
            $this->db->like('order_date', $date);
            $this->db->where('order_status', 'confirmed');
            $amount = $this->db->get('repurchase_order');
            $amount_credit += $amount->row('total_amount');
        }

        // upgrade package
        if ($module_status['package_upgrade'] == 'yes') {
            $this->db->select_sum('payment_amount');
            $this->db->like('date_added', $date);
            $amount = $this->db->get('package_upgrade_history');
            $amount_credit += $amount->row('payment_amount');
        }

        //package reactivation
        if ($module_status['subscription_status'] == 'yes') {
            $this->db->select_sum('total_amount');
            $this->db->like('date_submitted', $date);
            $amount = $this->db->get('package_validity_extend_history');
            $amount_credit += $amount->row('total_amount');
        }

        //epin
        if ($module_status['pin_status'] == 'yes') {

            $this->db->select_sum('pin_amount');
            $this->db->like('pin_uploded_date', $date);
            $amount = $this->db->get('pin_numbers');
            $amount_credit += $amount->row('pin_amount');
        }

        // transfer funds
        $this->db->select_sum('amount');
        $where = "from_user_id= $this->LOG_USER_ID AND amount_type='admin_debit' AND date LIKE '%$date%'";
        $where2 = "to_user_id= $this->LOG_USER_ID AND amount_type='user_credit' AND date LIKE '%$date%'";
        $this->db->where($where);
        $this->db->or_where($where2);
        $amount = $this->db->get('fund_transfer_details');
        $amount_credit += $amount->row('amount');

        // commissions

        $this->db->select_sum('total_amount');
        $this->db->like('date_of_submission', $date);
        $amount = $this->db->get('leg_amount');
        $amount_debit += $amount->row('total_amount');

        //transfer funds
        $this->db->select_sum('amount');
        $this->db->like('date', $date);
        $this->db->where('from_user_id', $this->LOG_USER_ID);
        $this->db->where_in('amount_type', array("admin_credit", "user_credit"));
        $amount = $this->db->get('fund_transfer_details');
        $amount_debit += $amount->row('amount');

        //payout released
        $this->db->select_sum('requested_amount');
        $this->db->like('updated_date', $date);
        $this->db->where('status', 'released');
        $amount = $this->db->get('payout_release_requests');
        $amount_debit += $amount->row('requested_amount');


        //other expenses
        $this->db->select('*');
        $this->db->like('date', $date);
        $query = $this->db->get('other_expenses');
        $total_other_exp = 0;
        foreach ($query->result_array() as $row) {
            $total_other_exp += round($row['amount'], 2);
        }

        $revenue_details['other_expenses'] = $query->result_array();
        $revenue_details['total_other_exp'] = round($total_other_exp, 2);
        $revenue_details['amount_credit'] = round($amount_credit, 2);
        $revenue_details['amount_debit'] = round($amount_debit, 2);
        $revenue_details['profit'] = $revenue_details['amount_credit'] - ($revenue_details['total_other_exp'] + $revenue_details['amount_debit']);
        $revenue_details['nodata'] = "no";
        if ($amount_credit == 0 && $amount_debit == 0 && $total_other_exp == 0) {
            $revenue_details['nodata'] = "yes";
        }


        return $revenue_details;
    }

    public function addNewExpense($post_arr)
    {
        $this->db->set("amount", $post_arr['amount']);
        $this->db->set("description", $post_arr['description']);
        $res = $this->db->insert('other_expenses');
        return $res;
    }

    public function gatewayList()
    {
        $available = array(5, 6, 7);
        $this->db->select('gateway_name');
        $this->db->where('payout_status', "yes");
        $this->db->where_in('id', $available);
        $this->db->order_by('payout_sort_order', "ASC");
        $this->db->from('payment_gateway_config');
        $res = $this->db->get();
        return $res->result_array();
    }

    public function getTranasactionErrorDetails($from_date, $to_date)
    {
        $details = array();
        $i = 0;

        $this->db->select('to_user_id as user_id,error_reason as message,amount_payable as payout_release_amount,bitcoin_address,date,payment_type');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $this->db->from('bitcoin_payout_release_error_report');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $details[$i] = $row;
            $details[$i++]['user_name'] = $this->validation_model->idToUserName($row['user_id']);
        }

        $this->db->select('user_id,message,payout_release_amount,bitcoin_address,date');
        $this->db->from('blockchain_payout_release_history');
        $this->db->where('status', '0');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $details[$i] = $row;
            $details[$i]['payment_type'] = lang('Blockchain');
            $details[$i++]['user_name'] = $this->validation_model->idToUserName($row['user_id']);
        }
        return $details;
    }

    public function getDailyReleasedPayoutDetails($date)
    {

        $details = array();
        $start_date = $date . " 00:00:00";
        $to_date = $date . " 23:59:59";
        $this->db->select('*');
        $this->db->from('amount_paid as a');
        $this->db->join("ft_individual AS f", 'f.id=a.paid_user_id', 'INNER');
        $this->db->where('f.active', 'yes');
        $this->db->where('a.paid_type', 'released');
        $this->db->where('paid_date >=', $start_date);
        $this->db->where('paid_date <=', $to_date);
        if($this->LOG_USER_TYPE=="user")
            {
                $this->db->where('f.id =', $this->LOG_USER_ID); 
            }
        $query = $this->db->get();

        $i = 0;
        foreach ($query->result_array() as $row) {

            $details["detail$i"]["paid_id"] =$row['paid_id'];
            $details["detail$i"]["paid_user_id"] = $this->validation_model->IdToUserName($row['paid_user_id']);
            $details["detail$i"]["full_name"] = $this->validation_model->getUserFullName($row['paid_user_id']);
            $details["detail$i"]["paid_amount"] = $row['paid_amount'];
            $details["detail$i"]["paid_date"] = date('Y-m-d', strtotime($row['paid_date']));
            $details["detail$i"]["paid_type"] = $row['paid_type'];
            //mark as paid
            $details["detail$i"]["paid_status"] = $row['paid_status'];
            //
            $i++;
        }
        return $details;
    }
    public function getDonorReleasedPayoutDetails($from_date = '', $to_date = '')
    {

        if ($to_date) {
            $from_date = $from_date . " 00:00:00";
            $to_date = $to_date . " 23:59:59";
        } else {
            $date = date("Y-m-d H:i:s");
            $to_date = $date;
            $from_date = $from_date . " 00:00:00";
        }
        $details = array();
        $this->db->select('*');
        $this->db->from('amount_paid as a');
        $this->db->join("ft_member AS f", 'f.id=a.paid_user_id', 'INNER');
        $this->db->where('f.active', 'yes');
        $this->db->where('a.paid_type', 'released');

        if ($from_date && $to_date) {
            $this->db->where("paid_date >=", $from_date);
            $this->db->where("paid_date <=", $to_date);
        }

        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {

            $details["detail$i"]["paid_user_id"] = $this->validation_model->memberIdToUserName($row['paid_user_id']);
            $details["detail$i"]["full_name"] = $this->validation_model->getMemberFullName($row['paid_user_id']);
            $details["detail$i"]["paid_amount"] = $row['paid_amount'];
            $details["detail$i"]["paid_date"] = date('Y-m-d', strtotime($row['paid_date']));
            $details["detail$i"]["paid_type"] = $row['paid_type'];
            //mark as paid
            $details["detail$i"]["paid_status"] = $row['paid_status'];
            //

            $i++;
        }

        return $details;
    }
    public function getDonorPayoutPendingDetails($from_date = '', $to_date = '')
    {
        $date = date("Y-m-d H:i:s");

        if (!isset($to_date) || trim($to_date) === '') {
            $to_date = $date;
        }
        if ($from_date)
            $from_date = $from_date . " 00:00:00";
        $to_date = $to_date . " 23:59:59";
        $payout_settings = $this->getPayoutSettings();

        $payout_type = $payout_settings['payout_type'];
        $min_payout = $payout_settings['min_payout'];
        $max_payout = $payout_settings['max_payout'];

        $details = array();
        $this->db->select('*');
        $this->db->from('member_payout_release_requests');
        $this->db->where("requested_date >=", $from_date);
        $this->db->where("requested_date <=", $to_date);
        $this->db->where("status", 'pending');
        $query = $this->db->get();

        $i = 0;
        foreach ($query->result_array() as $row) {
            $details["detail$i"]["paid_user_id"] = $this->validation_model->memberIdToUserName($row['requested_user_id']);
            $details["detail$i"]["full_name"] = $this->validation_model->getMemberFullName($row['requested_user_id']);
            $details["detail$i"]["paid_amount"] = $row['requested_amount'];
            $details["detail$i"]["paid_date"] = date('Y-m-d', strtotime($row['requested_date']));
            $details["detail$i"]["status"] = $row['status'];
            $i++;
        }

        return $details;
    }
    function getUserTypeWiseCommisionDetails($user_id = '', $type)
    {
        $i = 0;
        $details = array();
        $this->db->select('*');
        $where1 = '(user_id = "' . $user_id . '" or donor_id = "' . $user_id . '")';
        $where2 = '(user_id_type = "' . $type . '" or donor_id_type = "' . $type . '")';
        $this->db->where($where1);
        $this->db->where($where2);
        $this->db->from('crowd_donation_history');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $details[$i]['user_name'] = $this->getUserName($row['user_id'], $row['user_id_type']);
            $details[$i]['full_name'] = $this->getUserFullName($row['user_id'], $row['user_id_type']);
            $details[$i]['from_user'] = $this->getUserName($row['donor_id'], $row['donor_id_type']);
            $details[$i]['project_name'] = $this->getProjectNamefromId($row['project_id']);
            $details[$i]['type'] = $row['type'];
            $details[$i]['date'] = $row['date'];
            $details[$i]['total_amount'] = $row['amount'];
            $details[$i]['amount_payable'] = $row['amount'];
            if ($row['user_id'] == $user_id)
                $details[$i]['amount_type'] = 'credit';
            else
                $details[$i]['amount_type'] = 'debit';
            $i++;
        }
        return $details;
    }

    function getUserTypeWiseReleasedCommisionDetails($user_id = '')
    {

        $i = 0;
        $details = array();
        $this->db->select('*');
        $this->db->where('requested_user_id', $user_id);
        $this->db->where('status', 'released');
        $this->db->from('member_payout_release_requests');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $details[$i]['user_name'] = $this->getUserName($row['requested_user_id'], 'member');
            $details[$i]['full_name'] = $this->getUserFullName($row['requested_user_id'], 'member');
            $details[$i]['total_amount'] = $row['requested_amount'];
            $details[$i]['date'] = $row['updated_date'];
            $i++;
        }
        $this->db->select('*');
        $this->db->where('paid_user_id', $user_id);
        $this->db->where('paid_type', 'released');
        $this->db->from('amount_paid');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $details[$i]['user_name'] = $this->getUserName($row['paid_user_id'], 'member');
            $details[$i]['full_name'] = $this->getUserFullName($row['paid_user_id'], 'member');
            $details[$i]['total_amount'] = $row['paid_amount'];
            $details[$i]['date'] = $row['paid_date'];
            $i++;
        }

        return $details;
    }

    public function getUserName($user_id, $type)
    {
        $user_name = '';
        if ($type == 'member') {
            $user_name = $this->validation_model->memberIdToUserName($user_id);
        } else {
            $user_name = $this->validation_model->IdToUserName($user_id);
        }
        return $user_name;
    }

    public function getUserFullName($user_id, $type)
    {
        $user_name = '';
        if ($type == 'member') {
            $user_name = $this->validation_model->getMemberFullName($user_id);
        } else {
            $user_name = $this->validation_model->getUserFullName($user_id);
        }
        return $user_name;
    }
    public function getProjectNamefromId($project_id)
    {
        $project_name = '';
        $this->db->select('project_name');
        $this->db->where('project_id', $project_id);
        $this->db->where('lang_id', $this->LANG_ID);
        $query = $this->db->get('project_information');
        foreach ($query->result() as $row) {
            $project_name = $row->project_name;
        }
        return $project_name;
    }
    function getMembeReleasedCommisionDetails($user_id = '')
    {

        $i = 0;
        $details = array();
        $this->db->select('*');
        $this->db->where('requested_user_id', $user_id);
        $this->db->where('status', 'released');
        $this->db->from('member_payout_release_requests');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $user_name = $this->validation_model->memberIdToUserName($row['requested_user_id']);
            $details[$i]['user_name'] = $user_name;
            $details[$i]['full_name'] = $this->validation_model->getMemberFullName($row['requested_user_id']);
            $details[$i]['total_amount'] = $row['requested_amount'];
            $details[$i]['date'] = $row['updated_date'];
            $i++;
        }
        return $details;
    }
    function getUserDonationDetails($user_id)
    {

        $i = 0;
        $details = array();
        $this->db->select('*');
        $this->db->from('crowd_donation_history');
        $where1 = '(user_id = "' . $user_id . '" or donor_id = "' . $user_id . '")';
        $this->db->where($where1);
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            if ($user_id == $row['donor_id']) {
                $details[$i]['project_name'] = $this->getProjectNamefromId($row['project_id']);
                $details[$i]['giv_amount'] = $row['amount'];
                $details[$i]['To_user'] = $this->getUserName($row['user_id'], $row['user_id_type']);
                $details[$i]['rec_amount'] = 0;
                $details[$i]['from_user'] = 'NA';
                $details[$i]['type'] = $row['type'];
                $details[$i]['date'] = $row['date'];
            } else if ($user_id == $row['user_id']) {
                $details[$i]['project_name'] = $this->getProjectNamefromId($row['project_id']);
                $details[$i]['giv_amount'] = 0;
                $details[$i]['To_user'] = 'NA';
                $details[$i]['rec_amount'] = $row['amount'];
                if (($row['donor_id'] != 0) && ($row['type'] != 'open_donation')) {
                    $details[$i]['from_user'] = $this->getUserName($row['donor_id'], $row['donor_id_type']);
                } else {
                    $details[$i]['from_user'] = 'NA';
                }
                $details[$i]['type'] = $row['type'];
                $details[$i]['date'] = $row['date'];
            }
            $i++;
        }
        return $details;
    }

    public function getGrandTotalTDS()
    {
        $array = $this->db->select_sum("tds")->get("leg_amount")->result_array();
        if(count($array))
            return $array[0]['tds'];
        return 0;
    }

    public function getGrandTotalServiceCharge()
    {
        $array = $this->db->select_sum("service_charge")->get("leg_amount")->result_array();
        if(count($array))
            return $array[0]['service_charge'];
        return 0;
    }
    
    function checkRegistrationFeeDisplay() {
        $curRegFee = $this->validation_model->getConfig('reg_amount') * 1;
        $totalRegFee = 0;
        $array = $this->db->select_sum("reg_amount")->get("infinite_user_registration_details")->result_array();
        if(count($array))
            $totalRegFee = $array[0]['reg_amount'] * 1;
        if($curRegFee <= 0 && $totalRegFee <= 0) {
            return "no";
        }
        return "yes";
    }
    
    function checkPayoutFeeDisplay() {
        $payoutFee = $this->validation_model->getConfig('payout_fee_amount') * 1;
        $totalPayoutFee = 0;
        $array = $this->db->select_sum("payout_fee")->get("payout_release_requests")->result_array();
        if(count($array))
            $totalPayoutFee = $array[0]['payout_fee'] * 1;
        if($payoutFee <= 0 && $totalPayoutFee <= 0) {
            return "no";
        }
        return "yes";
    }
    
    function getSubscriptionReport($userId = "", $fromDate = "", $toDate = "", $offset = "", $limit = "") {
        $fromDate = ($fromDate)?date("Y-m-d 00:00:00",strtotime($fromDate)):"";
        $toDate = ($toDate)?date("Y-m-d 00:00:00",strtotime($toDate)):"";
        $this->db->select("user_id, invoice_id, package_id, payment_type_used, date_submitted, total_amount, renewal_status");
        if($fromDate)
            $this->db->where("date_submitted >=", $fromDate);
        if($toDate)
            $this->db->where("date_submitted <=", $toDate);
        if($userId){
            $this->db->where("user_id", $userId);
        }
        if($limit!= "" && $offset!= "")
        {$this->db->limit($limit,$offset);}
        $array = $this->db->get("package_validity_extend_history")->result_array();
        for($i = 0; $i < count($array); $i++) {
            if($this->MODULE_STATUS['product_status'] == "yes") {
                $array[$i]["package_name"] = $this->getPrdocutNameFromProdId($array[$i]["package_id"]);
                $array[$i]["package_amount"] = $this->getPrdocutPriceFromProdId($array[$i]["package_id"]);
            } else {
                $array[$i]["package_name"] = "";
                $array[$i]["package_amount"] = "";
            }
            $array[$i]["username"] = $this->validation_model->getUserName($array[$i]["user_id"]);
            $array[$i]["user_full_name"] = $this->validation_model->getUserFullName($array[$i]["user_id"]);
            if($array[$i]["payment_type_used"] == "free join") {
                $array[$i]["payment_type_used"] = "free_subscription";
            }
            if($array[$i]["payment_type_used"] == "pin") {
                $array[$i]["payment_type_used"] = "epin";
            }
            if($array[$i]["payment_type_used"] == "pp_express") {
                $array[$i]["payment_type_used"] = "Paypal";
            }
        }
        return $array;
    }
    
    function getPrdocutNameFromProdId($prodId) {
        $prod_name = "";
        if ($this->MODULE_STATUS['opencart_status'] != "yes") {
            $this->db->select('product_name');
            $this->db->from('package');
            $this->db->where('prod_id', $prodId);
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                $prod_name = $row['product_name'];
            }
        } else {
            $this->db->select('model AS product_name');
            $this->db->where('package_id', $prodId);
            $query = $this->db->get('oc_product');
            foreach ($query->result_array() as $row) {
                $prod_name = $row['product_name'];
            }
        }
        return $prod_name;
    }
    
    function getPrdocutPriceFromProdId($prodId) {
        $product_value = "";
        $MODULE_STATUS = $this->trackModule();
        if ($MODULE_STATUS['opencart_status'] != "yes" || $MODULE_STATUS['opencart_status_demo'] != "yes") {
            $this->db->select('product_value');
            $this->db->from('package');
            $this->db->where('prod_id', $prodId);
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                $product_value = $row['product_value'];
            }
        } else {
            $this->db->select('price');
            $this->db->where('package_id', $prodId);
            $query = $this->db->get('oc_product');
            foreach ($query->result_array() as $row) {
                $product_value = $row['price'];
            }
        }
        return $product_value;
    }
    public function getCountAciveDeactiveUserDetails($start_date, $to_date){
      
        //$this->db->select('h.id');
        $this->db->from('user_activation_deactivation_history as h');
        $this->db->join("ft_individual as f", "f.id=h.user_id", "INNER");
        $this->db->where('f.delete_status', 'active');
        if($start_date != ''){
            $this->db->where('time >=', $start_date);
        }
        if($to_date != ''){
            $this->db->where('time <=', $to_date);
        }
        
        $query = $this->db->get();
        return $this->db->affected_rows();

    }

    public function getCountWeeklyJoining($from, $to){

    $this->load->model('joining_class_model');
        $this->load->model('joining_class_model');
        $date = date("Y-m-d H:i:s");

        if (!isset($to) || trim($to) === '') {
            $to = $date;
        }
       // $this->db->select('count(ft.id) AS count');
        $this->db->from("ft_individual as ft");
        $this->db->join("infinite_user_registration_details as reg", "reg.user_id=ft.id", "LEFT");
        $this->db->not_like('ft.active', 'terminated', 'after');
        if($from) {
            $this->db->where('ft.date_of_joining >=', $from);
        }
        if($to) {
            $this->db->where('ft.date_of_joining <=', $to);
        }
        $this->db->order_by("ft.date_of_joining", "asc");
               
        $query = $this->db->get();
        return $this->db->affected_rows();
    }
    public function getCountCommisionDetails($type, $from_date = '', $to_date = '', $user_id = ''){
     $date = date("Y-m-d H:i:s");
        
        $i = 0;
        $details = array();
        $count = 0;
        if($type)
            $count = count($type);
        
        //$this->db->select("count(l.total_amount) AS count");
        $this->db->from('leg_amount as l');
        $this->db->join('ft_individual as f', 'f.id=l.user_id', 'INNER');
        if ($type != '') {
            $this->db->where_in('amount_type', $type);
        }
        if ($from_date != '') {
            $this->db->where("date_of_submission >=", $from_date);
        }
        if ($to_date != '') {
            $this->db->where("date_of_submission <=", $to_date);
        }
        if ($user_id != '') {
            $this->db->where("user_id", $user_id);
        } else {
           // $this->db->group_by('user_id');
        }
        //$this->db->group_by('amount_type');
        
            
        $query = $this->db->get(); 
        return $this->db->affected_rows();

    }
    public function getCountTotalPayout($from_date, $to_date, $user_id){
     $this->load->model('leg_class_model');
        if ($from_date == '' AND $to_date == '') {
        
            if($user_id != ''){
            $this->db->where('user_id', $user_id);
            }
            $this->db->from('leg_amount ');
            $this->db->join('ft_individual', 'leg_amount.user_id=ft_individual.id', 'INNER');
            $this->db->where('ft_individual.active', 'yes');
            $this->db->group_by('leg_amount.user_id');
        } else {


           
           // $this->db->select('leg_amount.user_id');
            if($user_id != ''){
            $this->db->where('user_id', $user_id);
            }
            $this->db->from('leg_amount ');
            $this->db->join('ft_individual', 'leg_amount.user_id=ft_individual.id', 'INNER');
            $this->db->where('ft_individual.active', 'yes');
            $where = "leg_amount.date_of_submission BETWEEN '$from_date' AND '$to_date'";
            $this->db->where($where);
            $this->db->group_by('leg_amount.user_id');
        }

        
        $this->db->join('user_details as user', 'user.user_detail_refid=leg_amount.user_id', 'INNER');
        $query = $this->db->get();
        return $this->db->affected_rows();

    }
    public function getCountReleasedPayoutDetails($from_date, $to_date){
    
         if ($to_date) {
            $from_date = $from_date . " 00:00:00";
            $to_date = $to_date . " 23:59:59";
        } else {
            $date = date("Y-m-d H:i:s");
            $to_date = $date;
            $from_date = $from_date . " 00:00:00";
        }
        
       // $this->db->select('count(a.paid_id) as count');
        $this->db->from('amount_paid as a');
        $this->db->join("ft_individual AS f", 'f.id=a.paid_user_id', 'INNER');
        $this->db->where('f.active', 'yes');
        $this->db->where('a.paid_status', 'yes');

        if ($from_date && $to_date) {
            $this->db->where("paid_date >=", $from_date);
            $this->db->where("paid_date <=", $to_date);
        }
        if($this->LOG_USER_TYPE=="user") {
            $this->db->where('f.id =', $this->LOG_USER_ID); 
        }

        $query = $this->db->get();
        return $this->db->affected_rows();


    }
    public function getCountPayoutPendingDetails($from_date, $to_date){
      $date = date("Y-m-d H:i:s");

        if (!isset($to_date) || trim($to_date) === '') {
            $to_date = $date;
        }
        if ($from_date) {
            $from_date = $from_date . " 00:00:00";
        }
        $to_date = $to_date . " 23:59:59";
        $payout_settings = $this->getPayoutSettings();

        $payout_type = $payout_settings['payout_type'];
        $min_payout = $payout_settings['min_payout'];
        $max_payout = $payout_settings['max_payout'];

        $details = [];
        if ($payout_type == "ewallet_request" || $payout_type == "both") {
            
            $this->db->from('payout_release_requests p');
            $this->db->join('ft_individual f', 'p.requested_user_id=f.id');
            $this->db->join('user_details u', 'p.requested_user_id=u.user_detail_refid');
            if($from_date)
                $this->db->where("p.requested_date >=", $from_date);
            if($to_date)
                $this->db->where("p.requested_date <=", $to_date);
            $this->db->where("p.status", 'pending');
            $this->db->where("f.delete_status", 'active');
            if($this->LOG_USER_TYPE=="user")
            {
            $this->db->where('f.id =', $this->LOG_USER_ID); 
            }
            $query = $this->db->get(); 
            return $this->db->affected_rows();
        }
            if ($payout_type == "from_ewallet" || $payout_type == "both") {
            
            $this->db->from('user_balance_amount b');
            $this->db->join('ft_individual f', 'b.user_id=f.id');
            $this->db->join('user_details u', 'b.user_id=u.user_detail_refid');
            $this->db->where("b.balance_amount >=", $min_payout);
            $this->db->where('f.user_type !=', 'admin');
            $this->db->where("f.delete_status", 'active');
            if($this->LOG_USER_TYPE=="user")
            {
            $this->db->where('f.id =', $this->LOG_USER_ID); 
            }
            $query = $this->db->get();
            return $this->db->affected_rows();
        }
    }
        public function countOfrankedUsers($ranks, $from_date, $to_date){
         
        $date = date("Y-m-d H:i:s");

        if (!isset($to_date) || trim($to_date) === '') {
            $to_date = $date;
        }

        if ($ranks != '') {
            
                $this->db->from('rank_history');
                $this->db->where_in("new_rank", $ranks);
                if ($from_date != '') {
                    $this->db->where("date >=", $from_date);
                }
                if ($to_date != '') {
                    $this->db->where("date <=", $to_date);
                }
                $query = $this->db->get();
                return $this->db->affected_rows();
            
            
        } else {
            
            $this->db->from('rank_history');
            if ($from_date != '') {
                $this->db->where("date >=", $from_date);
            }
            if ($to_date != '') {
                $this->db->where("date <=", $to_date);
            }
            $query = $this->db->get();
            return $this->db->affected_rows();
        }

        }
        public function getCountEpinTransferDetails($week_date1 = '', $week_date2 = '', $user_id = "", $to_user_id = ""){
         
        $this->load->model('Epin_model');
        

        $date = date("Y-m-d H:i:s");

        if (!isset($week_date2) || trim($week_date2) === '') {
            $to_date = $date;
        } else {
            $to_date = $week_date2 . " 23:59:59";
        }
        $start_date = $week_date1 . " 00:00:00";
        $this->db->from('epin_transfer_history as e');
        $this->db->join('ft_individual as f1', 'f1.id=e.from_user_id', 'INNER');
        $this->db->join('ft_individual as f2', 'f2.id=e.user_id', 'INNER');
        if($start_date) {
            $this->db->where('e.date >=', $start_date);
        }
        if($to_date) {
            $this->db->where('e.date <=', $to_date);
        }
        if ($user_id) {
            $this->db->where("from_user_id", $user_id);
        }
        if ($to_user_id) {
            $this->db->where("user_id", $to_user_id);
        }
        $query = $this->db->get();
        return $this->db->affected_rows();

        }
        public function getCountSubscriptionReport($userId = "", $fromDate = "", $toDate = ""){
        
        $fromDate = ($fromDate)?date("Y-m-d 00:00:00",strtotime($fromDate)):"";
        $toDate = ($toDate)?date("Y-m-d 00:00:00",strtotime($toDate)):"";
        $this->db->from("package_validity_extend_history");
        if($fromDate){
            $this->db->where("date_submitted >=", $fromDate);
        }

        if($toDate){
            $this->db->where("date_submitted <=", $toDate);
        }

        if($userId){
            $this->db->where("user_id", $userId);
        }
        $array = $this->db->get();
        return $this->db->affected_rows();

        }
        public function getCountEpinTransferDetailsForUser($week_date1 = '', $week_date2 = '', $user_id, $to_user_id = ""){
          
        $this->load->model('Epin_model');
        $details = array();
        $date = date("Y-m-d H:i:s");

        if (!isset($week_date2) || trim($week_date2) === '') {
            $to_date = $date;
        } else {
            $to_date = $week_date2 . " 23:59:59";
        }
        if ($week_date1 != '') {
            $start_date = $week_date1 . " 00:00:00";
        } else {
            $start_date = '';
        }
        
        $this->db->from('epin_transfer_history et');
        $this->db->join("user_details AS u", "et.from_user_id=u.user_detail_refid", 'INNER');

        if ($to_user_id) {
            $this->db->group_start();
            $this->db->where("from_user_id", $user_id);
            $this->db->where("user_id", $to_user_id);
            $this->db->or_where("from_user_id", $to_user_id);
            $this->db->where("user_id", $user_id);
            $this->db->group_end();
        } else {
            $this->db->group_start();
            $this->db->where("from_user_id", $user_id);
            $this->db->or_where("user_id", $user_id);
            $this->db->group_end();
        }
        if ($start_date != '') {
            $this->db->where('et.date >=', $start_date);
        }
        if ($to_date != '') {
            $this->db->where('et.date <=', $to_date);
        }
        $query = $this->db->get();
        return $this->db->affected_rows();

        }

    public function getRepurchaseDetailsCount($start_date = "", $to_date = "", $user_id = "") {
        $this->db->select('r.order_id');
        $this->db->from('repurchase_order as r');
        $this->db->where('order_status', 'confirmed');
        if ($start_date != '') {
            $start_date = date('Y-m-d 00:00:00', strtotime($start_date));
            $this->db->where('order_date >=', $start_date);
        }
        if ($to_date != '') {
            $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
            $this->db->where('order_date <=', $to_date);
        }
        if ($user_id) {
            $this->db->where("user_id", $user_id);
        }
        return $this->db->count_all_results();
    }

    public function getRepurchaseDetailsNew($start_date, $to_date, $user_id, $filter = []) {
        $this->db->select('r.invoice_no, r.total_amount, r.payment_method, r.order_date');
        $this->db->from('repurchase_order as r');
        $this->db->where('order_status', 'confirmed');
        if ($start_date != '') {
            $start_date = date('Y-m-d 00:00:00', strtotime($start_date));
            $this->db->where('order_date >=', $start_date);
        }
        if ($to_date != '') {
            $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
            $this->db->where('order_date <=', $to_date);
        }
        if ($user_id) {
            $this->db->where("user_id", $user_id);
        }
        return $this->db->get()->result();
    }

    public function getWalletReport($wallet_type = '',$agent_id='', $page = '', $limit = ''){
        $user_details =[];
        $this->db->select('cwallet_history.*,user_details.user_detail_name,user_details.user_detail_second_name,infinite_countries.country_name as country,user_details.agent_id');
        // $this->db->where('id', $pending_id);
        // $this->db->join('user_details','user_details.user_detail_refid = cwallet_history.user_id','left');
        $this->db->join('cwallet_history','user_details.user_detail_refid = cwallet_history.user_id','left');
        $this->db->join('infinite_countries','infinite_countries.country_id = user_details.user_detail_country','left');
        if($wallet_type != ''){
            $this->db->where('cwallet_history.wallet_type', $wallet_type);
        }
        if($agent_id != ''){
            $this->db->where('user_details.agent_id', $agent_id);
        }
        if ($limit) {
            $this->db->limit($limit, $page);
        }
        $query = $this->db->get('user_details');
        // dd($this->db->last_query($query));
        $i = 0;
        foreach ($query->result_array() as $row) {
            $user_details[$i]['user_name'] = $this->validation_model->IdToUserName($row['user_id']);
            $user_details[$i]['from_name'] = $this->validation_model->IdToUserName($row['from_id']);
            $user_details[$i]['agent_username'] = $this->validation_model->IdToAgentUserName($row['agent_id']);
            $user_details[$i]['full_name'] = $row['user_detail_name']." ".$row['user_detail_second_name'];
            $user_details[$i]['wallet_type'] = $row['wallet_type'];
            $user_details[$i]['wallet_amount'] = $row['wallet_amount'];
            $user_details[$i]['amount'] = $row['amount'];
            $user_details[$i]['country'] = $row['country'];
            $user_details[$i]['date_added'] = $row['date_added'];
            
            $i++;
        }
        return $user_details;

    }

    public function getCountWalletReport($wallet_type = "",$agent_id='') {
        $this->db->select('cwallet_history.*');
        // $this->db->join('user_details','user_details.user_detail_refid = cwallet_history.user_id','left');
        $this->db->join('cwallet_history','user_details.user_detail_refid = cwallet_history.user_id','left');
        if($wallet_type != ''){
            $this->db->where('wallet_type', $wallet_type);
        }
        if($agent_id != ''){
            $this->db->where('user_details.agent_id', $agent_id);
        }
        
        $query = $this->db->get('user_details');
        return $this->db->affected_rows();
    }

    
    
    public function getProfitDistributionReport($from_date = "", $to_date = "", $offset = '', $limit = '')
    {

        $date = date("Y-m-d H:i:s");

        if ($to_date) {
            $from_date = $from_date . " 00:00:00";
            $to_date = $to_date . " 23:59:59";
        } 
        
        $this->db->select('*');
        $this->db->from('profit_distribution');
        if ($from_date && $to_date) {
            $this->db->where("date >=", $from_date);
            $this->db->where("date <=", $to_date);
        }
        if($limit!= "" && $offset!= "")
        {$this->db->limit($limit,$offset);}
        $query = $this->db->get();
        return $query->result_array();
        
    }
    
    
    public function getCountProfitDistributionReport($from_date, $to_date){
        $this->db->select('*');
        $this->db->from('profit_distribution');
        if ($from_date && $to_date) {
            $this->db->where("date >=", $from_date);
            $this->db->where("date <=", $to_date);
        }            
        $query = $this->db->get();
        return $this->db->affected_rows();

    }
    
    public function getPoolDistributionReport($from_date = "", $to_date = "", $offset = '', $limit = '')
    {

        $date = date("Y-m-d H:i:s");

        if ($to_date) {
            $from_date = $from_date . " 00:00:00";
            $to_date = $to_date . " 23:59:59";
        } 
        
        $this->db->select('*');
        $this->db->from('pool_distribution');
        if ($from_date && $to_date) {
            $this->db->where("date >=", $from_date);
            $this->db->where("date <=", $to_date);
        }
        if($limit!= "" && $offset!= "")
        {$this->db->limit($limit,$offset);}
        $query = $this->db->get();
        return $query->result_array();
        
    }
    
    
    public function getCountPoolDistributionReport($from_date, $to_date){
        $this->db->select('*');
        $this->db->from('pool_distribution');
        if ($from_date && $to_date) {
            $this->db->where("date >=", $from_date);
            $this->db->where("date <=", $to_date);
        }            
        $query = $this->db->get();
        return $this->db->affected_rows();

    }
    public function getVoucherReport($user_id=0,$offset,$limit){
        $this->db->select('*');
        if($user_id)
            $this->db->where('user_id',$user_id);
        if($limit!= "")
            $this->db->limit($limit,$offset);
        $res=$this->db->get('voucher_history');
        $i=0;
        $details=array();
        foreach($res->result_array() as $row){
            $details[$i]=$row;
            $details[$i]['user_name']=$this->validation_model->idToUsername($row['user_id']);
            $details[$i]['rank_name']=$this->validation_model->getRankName($row['rank']);
            $i++;
        }
        return $details;
    }
    public function getCountVoucherReport($user_id=0){
        if($user_id)
            $this->db->where('user_id',$user_id);
        return $this->db->count_all_results('voucher_history');
    }
     public function getGroup_pv_details($time_period = 'month',$user_id = '',$from_date = '',$to_date = '' , $limit='' , $offset = ''){
        if($time_period == 'all')
        $this->db->select('*');
        else{
            $this->db->select('user_id');
            $this->db->select_sum('group_pv','group_pv');
            $this->db->group_by('user_id');
            if($time_period == 'today'){
                $this->db->where('date', date('d-m-Y'));
            }
            if($time_period == 'month'){
                $this->db->where('MONTH(date)', date('m'));
                $this->db->where('YEAR(date)', date('Y'));
            }
            if($time_period == 'year'){
                $this->db->where('YEAR(date)', date('Y'));
            }
            if($time_period == 'custom'){
                if ($from_date && $to_date) {
                    $this->db->where("date >=", $from_date);
                    $this->db->where("date <=", $to_date);
                }
            }
        }
        if($user_id != '')
        $this->db->where('user_id',$user_id);
        if($limit!= "")
        $this->db->limit($limit,$offset);
        $this->db->where('group_pv!=',0);
        $this->db->from('pv_history_details');
        $res = $this->db->get();
        $details=array();
        $i = 0;
        if($time_period == 'all'){
            foreach($res->result_array() as $row){
                $details[$i]=$row;
                $details[$i]['user_name']=$this->validation_model->idToUsername($row['user_id']);
                $details[$i]['from_user_name']=$this->validation_model->idToUsername($row['from_id']);
                $details[$i]['date_range']='';
                $i++;
            }
        }
  
        else{
            if($time_period == 'today'){
                foreach($res->result_array() as $row){
                    $details[$i]['group_pv']=$row['group_pv'];
                    $details[$i]['user_name']=$this->validation_model->idToUsername($row['user_id']);
                    $details[$i]['from_user_name']='NA';
                    $details[$i]['date']='';
                    $details[$i]['date_range']='Today';
                    $i++;
                }
            }
            if($time_period == 'month'){
                foreach($res->result_array() as $row){
                    $details[$i]['group_pv']=$row['group_pv'];
                    $details[$i]['user_name']=$this->validation_model->idToUsername($row['user_id']);
                    $details[$i]['from_user_name']='NA';
                    $details[$i]['date']='';
                    $details[$i]['date_range']='Current month';
                    $i++;
                }
            }
            if($time_period == 'year'){
                foreach($res->result_array() as $row){
                    $details[$i]['group_pv']=$row['group_pv'];
                    $details[$i]['user_name']=$this->validation_model->idToUsername($row['user_id']);
                    $details[$i]['from_user_name']='NA';
                    $details[$i]['date']='';
                    $details[$i]['date_range']='Current Year';
                    $i++;
                }
            }
            if($time_period == 'custom'){
                foreach($res->result_array() as $row){
                    $details[$i]['group_pv']=$row['group_pv'];
                    $details[$i]['user_name']=$this->validation_model->idToUsername($row['user_id']);
                    $details[$i]['from_user_name']='NA';
                    $details[$i]['date']='';
                    $details[$i]['date_range']=$from_date . ' to ' . $to_date;
                    $i++;
                }
            }
        }

        return $details;
    }
    public function getCountGroup_pv_details($time_period = 'month',$user_id = '',$from_date = '',$to_date = ''){
        if($time_period == 'all')
        $this->db->select('*');
        else{
            $this->db->select('user_id');
            $this->db->select_sum('group_pv','group_pv');
            $this->db->group_by('user_id');
            if($time_period == 'month'){
                $this->db->where('MONTH(date)', date('m'));
                $this->db->where('YEAR(date)', date('Y'));
            }
            if($time_period == 'year'){
                $this->db->where('YEAR(date)', date('Y'));
            }
            if($time_period == 'today'){
                $this->db->where('date', date('d-m-Y'));
            }
            if($time_period == 'custom'){
                if ($from_date && $to_date) {
                    $this->db->where("date >=", $from_date);
                    $this->db->where("date <=", $to_date);
                }
            }
        }
        if($user_id != '')
        $this->db->where('user_id',$user_id);

        $this->db->from('pv_history_details');
            return $this->db->count_all_results();
    }
}

