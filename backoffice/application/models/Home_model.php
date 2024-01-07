<?php

Class home_model extends inf_model {

    public function __construct() {

        $this->load->model('validation_model');
        $this->load->model('joining_class_model');
        $this->load->model('joining_model');
        $this->load->model('mail_model');
        if ($this->LOG_USER_ID && $this->MODULE_STATUS['pin_status'] == 'yes') {
            $this->load->model('epin_model');
        }
        $this->load->model('ewallet_model');
        $this->load->model('payout_model');
        $this->load->model('tree_model');
        $this->load->model('configuration_model');
    }
   
    public function todaysJoiningCount($user_id = '') {
        $date = date("Y-m-d");
        return $this->joining_class_model->todaysJoiningCount($date, $user_id);
    }

    public function totalJoiningUsers($user_id = '') {
        return $this->joining_model->totalJoiningUsers($user_id);
    }

    public function getAllReadMessages($type) {
        return $this->mail_model->getAllReadMessages($type);
    }

    public function getAllUnreadMessages($type) {
        return $this->mail_model->getAllUnreadMessages($type);
    }

    public function getAllMessagesToday($type) {
        return $this->mail_model->getAllMessagesToday($type);
    }

    public function getGrandTotalEwallet($user_id = '') {
        return $this->ewallet_model->getGrandTotalEwallet($user_id);
    }

    public function getTotalRequestAmount($user_id = '') {
        return $this->ewallet_model->getTotalRequestAmount($user_id);
    }

    public function getTotalReleasedAmount($user_id = '') {
        return $this->ewallet_model->getTotalReleasedAmount($user_id);
    }

    public function getJoiningDetailsperMonth($user_id = '') {
        return $this->joining_model->getJoiningDetailsperMonth($user_id);
    }
    public function getTotalCommission($user_id = '',$start_date = '',$end_date = '') {
        return $this->ewallet_model->getTotalCommission($user_id,$start_date,$end_date);
    }
    public function getTotalDonation($user_id = '',$start_date = '',$end_date = '') {
        return $this->ewallet_model->getTotalDonation($user_id,$start_date,$end_date);
    }

    public function getNotifications() {
        $notifications = array();

        $date = date("Y-m-d H:i:s", time() - 30);

        $this->db->select('id,user_id,done_by,ip,activity');
        $this->db->from('activity_history');
        $this->db->where('date >', $date);
        $this->db->where('notification_status', 0);
        $this->db->where('done_by_type !=', 'admin');
        $this->db->where('done_by !=', '');
        $this->db->order_by('date', 'DESC');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(5);
        $query = $this->db->get();

        foreach ($query->result_array() as $row) {

            $doneby_user_name = $this->validation_model->idToUserName($row['done_by']);
            $user_name = $this->validation_model->idToUserName($row['user_id']);
            $ip = $row['ip'];
            $activity = 'user_' . $row['activity'];
            $message = sprintf(lang($activity), $doneby_user_name, $ip);
            if ($message == '') {
                $message = "$doneby_user_name" . $this->lang->line('performed') . "'$activity'";
            }
            $row["message"] = $message;
            $notifications [] = $row;

            $this->db->set("notification_status", 1);
            $this->db->where("id", $row["id"]);
            $this->db->update("activity_history");
        }
        return $notifications;
    }

    public function getTopRecruters($count = 5, $sponsor_id = "") {
        $details = array();
        $sponsor_left = '';
        $sponsor_right = '';
        if ($sponsor_id) {
            // $sponsor_left_right = $this->validation_model->getUserLeftAndRight($sponsor_id, "sponsor");
            // $sponsor_left = $sponsor_left_right['left_sponsor'];
            // $sponsor_right = $sponsor_left_right['right_sponsor'];
        }
        $this->db->select("count(f2.sponsor_id) as count, f1.user_name,f1.id, u.user_detail_name, u.user_detail_second_name");
        $this->db->from("sponsor_treepath as t");
        $this->db->join("ft_individual as f1", "f1.id = t.descendant", "inner");
        $this->db->join("ft_individual as f2", "f2.sponsor_id = t.descendant", "inner");
        $this->db->join("user_details as u", "u.user_detail_refid = f1.id", "inner");
        $this->db->where("t.ancestor", $sponsor_id);
        $this->db->where("t.descendant !=", $sponsor_id);
        $this->db->group_by('f2.sponsor_id');
        //$this->db->distinct('f2.sponsor_id');
        $this->db->order_by('count', 'DESC');
        $this->db->limit($count);
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $value) {
           $details[$i] = $value;
           $details[$i]['profile_picture'] = $this->validation_model->getProfilePicture($value['id']);
            $i++;
        }
        return $details;
    }

    public function getTopRecruters_old($count = 5, $sponsor_id = "") {
        $details = array();
        $sponsor_left = '';
        $sponsor_right = '';
        if ($sponsor_id) {
            // $sponsor_left_right = $this->validation_model->getUserLeftAndRight($sponsor_id, "sponsor");
            // $sponsor_left = $sponsor_left_right['left_sponsor'];
            // $sponsor_right = $sponsor_left_right['right_sponsor'];
            $downlines = $this->validation_model->getDownlineUsers($sponsor_id, "sponsor");
            array_push($downlines, $sponsor_id);
            $this->db->where_in('sponsor_id', $downlines);
        }
        $this->db->select('count(f1.sponsor_id) as count,f2.user_name,u.user_photo as profile_picture');
        $this->db->from('ft_individual as f1');
        $this->db->join('tree_parser t', 't.ft_id = f1.id', 'LEFT');
        $this->db->where('f1.sponsor_id !=', 0);
        // $this->db->where('f1.sponsor_id !=', $sponsor_id);
        // $this->db->where("t.left_sponsor >", $sponsor_left);
        // $this->db->where("t.right_sponsor <", $sponsor_right);
        $this->db->join("ft_individual as f2", "f1.sponsor_id = f2.id");
        $this->db->join("user_details as u", "u.user_detail_refid = f2.id");

        $this->db->group_by('f1.sponsor_id');
        $this->db->order_by('count', 'DESC');
        $this->db->limit($count);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getTopEarners($count = 5, $sponsor_id = "") {
        $details = array();

        $this->db->select('sum(leg.amount_payable) as balance_amount,ft.user_name as user_name,ft.id, u.user_detail_name, u.user_detail_second_name');
        $this->db->from('ft_individual as ft');
        if ($sponsor_id) {
            $this->db->join('sponsor_treepath as str', 'str.descendant = ft.id');
        }
        $this->db->join("leg_amount as leg", "leg.user_id = ft.id");
        $this->db->join("user_details as u", "u.user_detail_refid = ft.id");
        $this->db->where('sponsor_id !=', 0);
        $this->db->where('amount_payable !=', 0);
        if ($sponsor_id) {
            $this->db->where('str.ancestor', $sponsor_id);
            $this->db->where('str.descendant !=', $sponsor_id);
        }
        $this->db->group_by('ft.id');
        $this->db->order_by('balance_amount', 'DESC');
        $this->db->limit($count);
        return $this->db->get()->result_array();
    }

    public function placementJoiningUsers($user_id = '') {
        return $this->joining_model->placementJoiningUsers($user_id);
    }

    /* Ajax function Starts */

    public function getMailCount($type, $start_date, $end_date, $read_status, $all = "notall") {
        $inf_sess = $this->session->userdata('inf_logged_in');
        $user_name = $inf_sess['user_name'];
        $id = $this->validation_model->userNameToID($user_name);
        $numrows = 0;
        if ($type == "admin") {
            $mail = 'mailtoadmin';
            $this->db->select('mailadid');
            $where = "mailadiddate between '$start_date' and '$end_date'";
        } else if ($type == "user") {
            $mail = 'mailtouser';
            $this->db->select('mailtousid');
            $this->db->where('mailtoususer', $id);
            $where = "mailtousdate between '$start_date' and '$end_date'";
        }
        $this->db->where('status', 'yes');
        if ($all != "all") {
            if ($read_status) {
                $this->db->where('read_msg', $read_status);
            }
            $this->db->where($where);
        }
        $this->db->from($mail);
        $numrows = $this->db->count_all_results(); // Number of rows returned from above query.
        return $numrows;
    }

    /* Ajax function  End */

    public function getSocialMediaInfo() {
        $this->db->select('fb_link,twitter_link,inst_link,gplus_link,fb_count,twitter_count,inst_count,gplus_count');
        $res = $this->db->get('site_information');
        return $res->row_array();
    }

    public function getSocialmediaFollowers() {
        return $this->configuration_model->getSocialMediaFollowersCount();
    }

    /* Ajax For Dynamic Box in Second row Begins */

    public function getSocialmediaLinks() {
        return $this->configuration_model->getSocialMediaLinks();
    }

    /* Ajax For Dynamic Box in Second row Ends */

    public function getCountToDoList($user_id = '') {

        $this->db->select('task_id,task,time,user_id');
        $this->db->where('user_id', $user_id);
        $this->db->from("to_do_list");
        $numrows = $this->db->count_all_results(); // Number of rows returned from above query.
        return $numrows;
    }

    public function getToDoList($user_id = '', $id = '', $emp_id = '') {
        $this->db->select("DATEDIFF(CURDATE(),time) as days");
        $this->db->select('task_id,task,time,status,user_id');
        if ($user_id != '') {
        $this->db->where('user_id', $user_id);
        }
        if ($id != '') {
            $this->db->where('task_id =', $id);
        }
        if ($emp_id != '') {
            $this->db->or_where('user_id', $emp_id);
        }
        //$this->db->where("DATEDIFF(NOW(), time) BETWEEN 30 AND 60");
        $this->db->from("to_do_list");

        $this->db->order_by("time", "asc");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function addToDoList($task, $task_time, $user_id) {

        $this->db->set('task', $task);
        $this->db->set('time', $task_time);
        $this->db->set('user_id', $user_id);
        $this->db->set('status', 'not_started');
        return $this->db->insert("to_do_list");
    }

    public function updateToDoList($task, $task_time, $user_id = '', $task_id) {

        $this->db->set('task', $task);
        $this->db->set('time', $task_time);
        if ($user_id != '') {
        $this->db->where('user_id', $user_id);
        }
        $this->db->where('task_id', $task_id);
        return $this->db->update("to_do_list");
    }

    public function deleteToDoList($user_id, $task_id) {

        $this->db->where('user_id =', $user_id);
        $this->db->where('task_id =', $task_id);
        return $query = $this->db->delete('to_do_list');
    }

    public function ChangeToDoStatus($user_id, $task_id, $status) {
        $this->db->set('status', $status);
        $this->db->where('user_id', $user_id);
        $this->db->where('task_id', $task_id);
        return $this->db->update("to_do_list");
    }

    public function getCountryMapdata($user_id = '') {
        $data = array();
        $this->db->select('c.country_code,COUNT(u.user_detail_id) as count');
        $this->db->from("infinite_countries as c");
        if ($user_id != "") {
            $join_condition = "c.country_id=u.user_detail_country and u.user_details_ref_user_id=$user_id";
        } else {
            $join_condition = "c.country_id=u.user_detail_country and u.user_detail_refid!= $this->ADMIN_USER_ID";
        }
        $this->db->join('user_details as u', $join_condition, 'left');
        $this->db->group_by('c.country_id');
        $result = $this->db->get();
        foreach ($result->result_array() as $row) {
            $data[strtoupper($row["country_code"])] = $row["count"];
        }
        return json_encode($data);
    }

    public function getLatestJoinees($user_type = '') {
        $data = array();
        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d', strtotime($from_date . '-7 days'));
        $this->db->select('ft.id,ft.user_name,active,ft.date_of_joining, user_detail_name, user_detail_second_name, reg.product_amount,reg.product_id,ft.product_id as prod_id');
        $this->db->from("ft_individual as ft");
        $this->db->join("user_details as ud", 'ud.user_detail_refid = ft.id');
        $this->db->join("infinite_user_registration_details as reg", 'reg.user_id = ft.id');
        $this->db->where('user_type !=', 'admin');
        $this->db->where('active !=', 'terminated');
        $this->db->where('active !=', 'no');
        $this->db->where('id !=', $this->LOG_USER_ID);
        $this->db->limit(10);
        if ($user_type == 'user') {
            $this->db->where('ft.sponsor_id', $this->LOG_USER_ID);
        }
        $this->db->order_by("date_of_joining", "desc");
        $query = $this->db->get();
        $result = $query->result_array();
        $cnt = $query->num_rows();
        if ($cnt > 0) {
            $i = 0;
            foreach ($result as $search_latest) {
                $data[$i]["id"] = $search_latest['id'];
                $data[$i]["user_name"] = $search_latest['user_name'];
                $data[$i]["active"] = $search_latest['active'];
                $data[$i]["date_of_joining"] = date("d M Y", strtotime($search_latest['date_of_joining']));
                $data[$i]["user_full_name"] = $search_latest['user_detail_name'] . ' ' . $search_latest['user_detail_second_name'];
                
                $data[$i]["profile_pic"] = $this->validation_model->getProfilePicture($search_latest['id']);
                
                // if($this->validation_model->getPckProdTypeNew($search_latest['product_id'])== "founder_pack"){
                //     $data[$i]["product_amount"]=$this->getProductAmountFromChild($search_latest['id']);
                // }else{
                
                    $data[$i]["product_amount"] =$this->getProductAmountAndPV($search_latest['prod_id']);
                //}

                $i++;
            }
        }

        return $data;

    }

    public function getPackageProgressData($limit = 4, $user_id = '') {
        $i = 0;
        $total = 0;
        $data = [];
        $MODULE_STATUS = $this->trackModule();
        if ($MODULE_STATUS['opencart_status'] == 'yes' && $MODULE_STATUS['opencart_status_demo'] == 'yes') {
            $this->db->select('COUNT(ft.id) as count,ft.product_id');
            $this->db->from('ft_individual as ft');
            $this->db->join('oc_product as oc', 'oc.package_id = ft.product_id');
            $this->db->where('oc.status !=', 0);
            $this->db->where('ft.product_id !=', '');
            if ($user_id)
                $this->db->where('ft.sponsor_id', $user_id);
            else
                $this->db->where('ft.id!=', $this->ADMIN_USER_ID);
            $this->db->group_by('ft.product_id');
            $this->db->order_by('count', 'DESC');
            $this->db->limit($limit);
            $query = $this->db->get();
        } else{
            $this->db->select('COUNT(ft.id) as count,ft.product_id');
            $this->db->from('ft_individual as ft');
            $this->db->join('package as pck', 'pck.prod_id = ft.product_id');
            $this->db->where('ft.product_id !=', '');
            if ($user_id)
                $this->db->where('ft.sponsor_id', $user_id);
            else
                $this->db->where('ft.id!=', $this->ADMIN_USER_ID);
            $this->db->group_by('ft.product_id');
            $this->db->order_by('count', 'DESC');
            $this->db->limit($limit);
            $query = $this->db->get();
        }

        foreach ($query->result_array() as $row) {
            $data[$i]['joining_count'] = $row['count'];
            $data[$i]['package_name'] = $this->getPackageNameFromPackageId($row['product_id'], $MODULE_STATUS);
            $total += $row['count'];
            $data[0]['perc'] = 100 / $total;
            $i = $i + 1;
        }
        if ($i < 6) {
            $default_list = $this->getDefaultProducts('yes', 'registration', $data,6-$i);
            foreach (range(0, 6) as $v) {
                if (isset($default_list[$v])) {
                    $data[] = ["package_name" => $default_list[$v]['product_name'], "joining_count" => 0, "perc" => 0];
                }
            }
        }
        return $data;
    }

    public function getPackageNameFromPackageId($package_id, $module_status) {
        if ($module_status['opencart_status'] == 'yes' && $module_status['opencart_status_demo'] == 'yes') {
            $this->db->select('model product_name');
            $this->db->from('oc_product');
            $this->db->where('package_id', $package_id);
        } else {
            $this->db->select('product_name');
            $this->db->from('package');
            $this->db->where('prod_id', $package_id);
            $this->db->where('type_of_package', "registration");
        }
        $query = $this->db->get();
        return $query->row_array()['product_name'];
    }

    public function getPackageCount($product_id, $user_id = '') {
        $i = 0;
        $count = 0;
        $this->db->select('COUNT(id) as count');
        $this->db->from('ft_individual');
        $this->db->where('product_id', $product_id);
        if ($user_id)
            $this->db->where('sponsor_id', $user_id);
        else
            $this->db->where('id!=', $this->ADMIN_USER_ID);
        $this->db->group_by('product_id');
        $this->db->order_by('count', 'DESC');
        $query = $this->db->get();
        if ($query->num_rows() > 0)
            $count = $query->result_array()[0]['count'];
        return $count;
    }

    public function getTotalPackageCount($user_id = '') {
        $i = 0;
        $total = 0;
        $this->db->select('COUNT(id) as count');
        $this->db->from('ft_individual');
        $this->db->where('product_id!=', '');
        if ($user_id)
            $this->db->where('sponsor_id', $user_id);
        else
            $this->db->where('id!=', $this->ADMIN_USER_ID);
        $this->db->group_by('product_id');
        $this->db->order_by('count', 'DESC');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $total += $row['count'];
            $i = $i + 1;
        }
        if ($total > 0)
            return (100 / $total);
        else
            return 0;
    }

    public function getTodyCntctMsg() {
        $date = date("Y-m-d");
        $this->db->select('*');
        $this->db->from('contacts');
        $this->db->where('owner_id', $this->LOG_USER_ID);
        $this->db->where('status', 'yes');
        $this->db->like('mailadiddate', $date);
        $cntct_mail_cnt = $this->db->count_all_results();
        return $cntct_mail_cnt;
    }

    public function getAllTodayMessages($type) {
        $mailcnt = $this->mail_model->getAllMessagesToday($type);
        //Get All Todays Contact Messages
        $contantcnt = $this->getTodyCntctMsg();
        $totalcnt = $mailcnt + $contantcnt;
        return $totalcnt;
    }

    public function getTotaMailCount($type, $start_date, $end_date, $read_status, $all = "notall") {
        $inf_sess =  $this->session->userdata('inf_logged_in');
        $user_name = $inf_sess['user_name'];
        $id = $this->validation_model->userNameToID($user_name);
        $numrows = 0;
        if ($type == "admin") {
            $mail = 'mailtoadmin';
            $this->db->select('mailadid');
            $where = "mailadiddate between '$start_date' and '$end_date'";
        } else if ($type == "user") {
            $mail = 'mailtouser';
            $this->db->select('mailtousid');
            $this->db->where('mailtoususer', $id);
            $where = "mailtousdate between '$start_date' and '$end_date'";
        }
        $this->db->where('status', 'yes');
        if ($all != "all") {
            if ($read_status) {
                $this->db->where('read_msg', $read_status);
            }
            $this->db->where($where);
        }
        $this->db->from($mail);
        $numrows = $this->db->count_all_results(); // Number of rows returned from above query.
        //Get contact mailcount
        $this->db->select('*');
        $this->db->from('contacts');
        $this->db->where('owner_id', $this->LOG_USER_ID);
        $this->db->where('status', 'yes');
        $this->db->order_by('mailadiddate', 'desc');
        $cntct_mail_cnt = $this->db->count_all_results();

        $total = $numrows + $cntct_mail_cnt;
        return $total;
    }

    public function getDefaultProducts($status = '', $type_of_package = '', $data,$limit = 4) {

        $product_details = array();
        $MODULE_STATUS = $this->trackModule();
        if ($MODULE_STATUS['opencart_status'] != "yes" || $MODULE_STATUS['opencart_status_demo'] != "yes") {
            $i = 0;
            $this->db->select('product_name,active,prod_id');
            $this->db->from('package');
            if ($status != '') {
                $this->db->where('active', $status);
            }
            if ($type_of_package != '') {
                $this->db->where('type_of_package', $type_of_package);
            }
            $this->db->limit($limit);
            if (!empty($data))
                $this->db->where_not_in('product_name', array_column($data, 'package_name'));
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                $product_details[] = $row;
            }
        } else {
            $i = 0;
            $this->db->select('model,product_id,package_type');
            $this->db->from("oc_product");
            $this->db->where('status',1);
            if ($type_of_package != '') {
                $this->db->where('package_type', $type_of_package);
            }
            $this->db->limit($limit);
            if (!empty($data))
                $this->db->where_not_in('model', array_column($data, 'package_name'));
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                $product_details[$i]['product_name'] = $row['model'];
                $product_details[$i]['prod_id'] = $row['product_id'];
                $product_details[$i]['type_of_package'] = $row['package_type'];
                $i = $i + 1;
            }
        }
        return $product_details;
    }

    /* Ajax Functon For Payout Tile Starts */

    public function getPayoutDetails($from_date = '', $to_date = '', $user_id = '') {

        $this->load->model('leg_class_model');
        $total_amount = 0;

        $this->db->select_sum('paid_amount');
        $this->db->from('amount_paid ');
        $this->db->where('paid_type', 'released');
        if ($from_date != '' AND $to_date != '') {
            $where = "paid_date BETWEEN '$from_date' AND '$to_date'";
            $this->db->where($where);
        }
        if ($user_id != '') {
            $this->db->where('paid_user_id', $user_id);
        }

        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $total_amount += $row['paid_amount'];
        }
        return $total_amount;
    }

    /* Ajax Functon For Payout Tile Ends */

    /* Ajax Functon For Commission Tile */
     public function getCommissionDetails($from_date = '', $to_date = '', $user_id = ''){
       
        $this->db->select_sum('amount_payable');
        $this->db->where('user_id', $user_id);
          if ($from_date && $to_date) {
            $where = "date_of_submission BETWEEN '$from_date' AND '$to_date'";
            $this->db->where($where);
         }

        $query = $this->db->get('leg_amount');
        $total_commission = $query->row_array()['amount_payable'] ?? 0;
        
        return $total_commission;
    }

/* Ajax Functon For Commission Tile ends */
    /* Ajax Functon For Sales Tile Ends */

    public function getSalesCount($from_date = '', $to_date = '', $user_id = '') {

        $total_sales = 0;
        $MODULE_STATUS = $this->trackModule();
        if ($MODULE_STATUS['repurchase_status'] == 'yes') {
            $this->db->select('COUNT(*) as count');
            $this->db->from('repurchase_order');
            $this->db->where('order_status', "confirmed");
            if ($from_date && $to_date) {
                $this->db->where('order_date >=', $from_date);
                $this->db->where('order_date <=', $to_date);
            }
            if ($user_id != '') {
                $this->db->where('user_id', $user_id);
            }
            $query = $this->db->get();
            $total_sales = $query->result_array()[0]['count'];
        }
        if ($MODULE_STATUS['product_status'] == 'yes' && $MODULE_STATUS['opencart_status'] != 'yes' && $MODULE_STATUS['opencart_status_demo'] != 'yes') {
            $this->db->select('COUNT(*) as count');
            $this->db->from('sales_order');
                $this->db->where('pending_id', NULL);
            if ($from_date && $to_date) {
                $this->db->where('date_submission >=', $from_date);
                $this->db->where('date_submission <=', $to_date);
            }
            if ($user_id != '') {
                $this->db->where('user_id', $user_id);
            }
            $query = $this->db->get();
            $total_sales_register = $query->result_array()[0]['count'];
            $total_sales = $total_sales + $total_sales_register;
        }

        if ($MODULE_STATUS['opencart_status'] == "yes" || $MODULE_STATUS['opencart_status_demo'] == "yes") {
            if ($user_id != '') {
                $customer_id = $this->validation_model->getOcCustomerId($user_id);
            }
            $this->db->select('COUNT(*) as count');
            $this->db->from('oc_order as o');
            $this->db->join("oc_order_history as oh", "o.order_id=oh.order_id ", "INNER");
            $this->db->where("oh.order_status_id >=", 1);
            if ($from_date && $to_date) {
                $this->db->where('o.date_added >=', $from_date);
                $this->db->where('o.date_added <=', $to_date);
            }
            if ($user_id != '') {
                $this->db->where('o.customer_id', $customer_id);
            }
            $query = $this->db->get();
            $total_sales_order = $query->result_array()[0]['count'];
            $total_sales = $total_sales + $total_sales_order;
        }
        return $total_sales;
    }

 public function getTotalSales($from_date = '', $to_date = '', $user_id = '') {

        $total_sales = 0;
        $MODULE_STATUS = $this->trackModule();
        if ($MODULE_STATUS['repurchase_status'] == 'yes') {
            $this->db->select_sum('total_amount');
            $this->db->from('repurchase_order');
            $this->db->where('order_status', "confirmed");
             $this->db->where('user_id', $user_id);
            if ($from_date && $to_date) {
                $this->db->where('order_date >=', $from_date);
                $this->db->where('order_date <=', $to_date);
            }
           
            $query = $this->db->get();
            $total_sales = $query->row_array()['total_amount'] ?? 0;
           
        }
        if ($MODULE_STATUS['product_status'] == 'yes' && $MODULE_STATUS['opencart_status'] != 'yes' && $MODULE_STATUS['opencart_status_demo'] != 'yes') {
            $this->db->select_sum('amount');
            $this->db->where('pending_id', NULL);
            $this->db->where('user_id', $user_id);
            if ($from_date && $to_date) {
                $this->db->where('date_submission >=', $from_date);
                $this->db->where('date_submission <=', $to_date);
            }
           
            $query = $this->db->get('sales_order');
            $total_sales_register = $query->row_array()['amount'] ?? 0;
            $total_sales = $total_sales + $total_sales_register;

        }

        if ($MODULE_STATUS['opencart_status'] == "yes" || $MODULE_STATUS['opencart_status_demo'] == "yes") {
            if ($user_id != '') {
                $customer_id = $this->validation_model->getOcCustomerId($user_id);
            }
            $this->db->select_sum('total');
            $this->db->where('o.customer_id', $customer_id);
            if ($from_date && $to_date) {
                $this->db->where('o.date_added >=', $from_date);
                $this->db->where('o.date_added <=', $to_date);
            }
    
            $query = $this->db->get('oc_order o');
            $total_sales_order = $query->row_array()['amount'] ?? 0;
            $total_sales = $total_sales + $total_sales_order;

        }
        return $total_sales;
    }

    /* Ajax Functon For Sales Tile Ends */

    public function getUnreadMessages($type, $user_id) {
        $result = array();
        $result1 = array();
        if ($type == "admin" || $type == "employee") {
            $tbl = 'mailtoadmin';
            $this->db->select('*');
            $where1 = array('status' => 'yes', 'read_msg' => 'no');
            $where2 = array('status' => 'no', 'deleted_by != ' => $user_id,'read_msg' => 'no','deleted_by !=' => 'both');
            $this->db->group_start()
                ->where($where1)
                ->or_group_start()
                ->where($where2)
                ->group_end()
                ->group_end();
            // $this->db->where('status', 'yes');
            // $this->db->where('read_msg', 'no');
            $this->db->order_by("mailadiddate", "desc");
            $this->db->from($tbl);
            $query = $this->db->get();
            $i = 0;
            foreach ($query->result_array() as $rows) {
                $result[$i] = $rows;
                $result[$i]['username'] = $this->validation_model->IdToUserName($rows['mailaduser']);
                $mail_userid = $this->validation_model->userNameToID($result[$i]['username']);
                $result[$i]['image'] = $this->validation_model->getProfilePicture($mail_userid);
                $result[$i]['mailadiddate'] = date("F j, g:i", strtotime($rows['mailadiddate']));
                $i++;
            }

            $this->db->select('*');
            $this->db->from('contacts');
            $this->db->where('owner_id', $user_id);
            $this->db->where('status', 'yes');
            $this->db->where('read_msg', 'no');
            $this->db->order_by('mailadiddate', 'desc');
            $query = $this->db->get();
            $i = 0;
            foreach ($query->result_array() as $rows) {
                $result1[$i] = $rows;
                $result1[$i]['username'] = $rows['contact_name'];
                $result1[$i]['image'] = 'nophoto.jpg';
                $result1[$i]['mailadsubject'] = $rows['contact_info'];
                $result1[$i]['mailadiddate'] = date("F j, g:i", strtotime($rows['mailadiddate']));
                $i++;
            }
            $res = array_merge($result, $result1);
            return $res;
        } else {

            $tbl = 'mailtouser';
            $this->db->select('*');
            // $this->db->where('status', 'yes');
            // $this->db->where('read_msg', 'no');
            $where1 = array('status' => 'yes','mailtoususer' => $user_id ,'read_msg' => 'no');
            $where2 = array('status' => 'no', 'deleted_by != ' => $user_id,'mailtoususer' => $user_id,'read_msg' => 'no','deleted_by !=' => 'both');
            $this->db->group_start()
                ->where($where1)
                ->or_group_start()
                ->where($where2)
                ->group_end()
                ->group_end();
            $this->db->order_by("mailtousdate", "desc");
            $this->db->from($tbl);
            $query = $this->db->get();
            $i = 0;
            foreach ($query->result_array() as $rows) {

                $result[$i]["mailaduser"] = $rows["mailtoususer"];
                $result[$i]["mailadsubject"] = $rows["mailtoussub"];
                $result[$i]["mailadiddate"] = date("F j, g:i", strtotime($rows['mailtousdate']));

                if ($rows['mailfromuser'] != 'admin') {
                    $result[$i]['username'] = $this->validation_model->idToUserName($rows['mailfromuser']);
                } else {
                    $result[$i]['username'] = $this->ADMIN_USER_NAME;
                }

                $mail_userid = $this->validation_model->userNameToID($result[$i]['username']);
                $result[$i]['image'] = $this->validation_model->getProfilePicture($mail_userid);
                $i++;
            }
            $this->db->select('*');
            $this->db->from('contacts');
            $this->db->where('owner_id', $user_id);
            $this->db->where('status', 'yes');
            $this->db->where('read_msg', 'no');
            $this->db->order_by('mailadiddate', 'desc');
            $query = $this->db->get();
            $i = 0;
            foreach ($query->result_array() as $rows) {
                $result1[$i] = $rows;
                $result1[$i]['username'] = $rows['contact_name'];
                $result1[$i]['image'] = 'nophoto.jpg';
                $result1[$i]['mailadsubject'] = $rows['contact_info'];
                $result1[$i]['mailadiddate'] = date("F j, g:i", strtotime($rows['mailadiddate']));
                $i++;
            }
            $res = array_merge($result, $result1);
            return $res;
        }
    }

    public function getCountLegamount($user_id,$prod_id) {
        $product_id =  $this->getProductDetails($prod_id);
        $cntct_mail_cnt = 0;
        $this->db->select('*');
        $this->db->from('leg_amount');
        $this->db->where('user_id', $user_id);
        $this->db->where('product_id', $product_id);
        $this->db->where('amount_type', 'daily_investment');

        $cntct_mail_cnt = $this->db->count_all_results();
        return $cntct_mail_cnt;
    }

    public function getProdName($prod_id) {
        $product_name = '';
        $this->db->select('product_name');
        $this->db->from('package');
        $this->db->where('prod_id', $prod_id);
        $res = $this->db->get();
        foreach ($res->result() as $row) {
            $product_name = $row->product_name;
        }
        return $product_name;
    }

    public function getProductDetails($prod_id) {
        $this->db->select('product_id');
        $this->db->from('package');
        $this->db->where('prod_id', $prod_id);
        $res = $this->db->get();
        foreach ($res->result() as $row) {
            $product_id = $row->product_id;
        }
        return $product_id;
    }

    public function getReturnInvestmentDetailsCount($user_id = '') {
        $this->db->from('leg_amount');
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        $this->db->where("amount_type", 'daily_investment');
        return $this->db->count_all_results();

    }

    public function getHyipTotalLegAmount($user_id ='') {
        return $this->ewallet_model->getTotalDailyInvestment($user_id);
     }

    public function getHyipTotalLegAmountDetails($page='', $limit='',$user_id='') {
        $array = array();
        $tot_amount = 0;
        $this->db->select('amount_payable,user_id,from_id,product_id,date_of_submission');
        $this->db->from('leg_amount');
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        $this->db->where("amount_type", 'daily_investment');
        $this->db->limit($limit, $page);
        $query = $this->db->get();
        $i = 1;
        foreach ($query->result_array() as $row) {
            $array[$i]["amount_payable"] = $row["amount_payable"];
            if ($row["from_id"]) {
                $array[$i]["from_id"] = $this->validation_model->IdToUserName($row["from_id"]);
            } else {
                $array[$i]["from_id"] = "NA";
            }
            $tot_amount+=$array[$i]["amount_payable"];
            $array[$i]['tot_amount'] = $tot_amount;
            $prod_id   = $this->getPackageId($row['product_id']);
            $prod_name = $this->getProdName($prod_id);
            $array[$i]['package'] = $prod_name;
            $array[$i]['date_of_submission'] = $row["date_of_submission"];
            $i++;
        }
        return $array;
    }

    public function getPackageId($product_id) {
        $this->db->select('prod_id');
        $this->db->where('product_id', $product_id);
        $query = $this->db->get('package');
        return $query->row_array()['prod_id'];
    }

    public function getActiveDeposit($user_id ='') {
        $this->db->select('ro.user_id,ro.prod_id,ro.days,leg.amount_payable,leg.user_id,leg.product_id,leg.date_of_submission');
        $this->db->from('roi_order AS ro');
        $this->db->join('package as pck', 'pck.prod_id = ro.prod_id');
        $this->db->join('leg_amount as leg', 'leg.user_id = ro.user_id and leg.product_id = pck.product_id');
        $this->db->where('leg.amount_type', 'daily_investment');
        $this->db->where('ro.pending_status', 0);
        if ($user_id) {
            $this->db->where('ro.user_id', $user_id);
        }
        $query = $this->db->get();
        $i = 0;
        $j = 0;
        $tot_amount = 0;
        foreach ($query->result_array() as $row) {
            $count_leg_amount            = $this->getCountLegamount($row['user_id'],$row['prod_id']);
            $expiry                      = $row['days'] - $count_leg_amount;
            if($expiry > 0){
            $tot_amount+=$row["amount_payable"];
            }
            $i++;
        }

        if($this->MODULE_STATUS['purchase_wallet'] == 'yes' && $tot_amount > 0) {
            $amount = 0;
            $this->db->select('SUM(purchase_wallet) as total_amount', FALSE);
            $this->db->where('amount_type', 'daily_investment');
            $this->db->where('type', 'credit');
            $this->db->where('ewallet_type', 'commission');
            if ($user_id) {
                $this->db->where('user_id', $user_id);
            }
            $query = $this->db->get('ewallet_history');
            foreach ($query->result() as $row) {
                $amount = $row->total_amount;
            }
            $tot_amount-=$amount;
        }

        return $tot_amount;
     }

    public function getActiveDepositDetails($user_id ='') {
        $array = array();
        $this->db->select('amount_payable,user_id,from_id,product_id,date_of_submission');
        $this->db->from('leg_amount as leg');
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        $this->db->where('amount_type', 'daily_investment');
        $query = $this->db->get();
        $i = 0;
        $j = 0;
        $tot_amount = 0;
        foreach ($query->result_array() as $row) {
            $prod_id                     = $this->getPackageId($row['product_id']);
            $count_leg_amount            = $this->getCountLegamount($row['user_id'],$prod_id);
            $days                        =  $this->getRoiDays($row['user_id'],$prod_id);
            $expiry                      = $days - $count_leg_amount;
            if($expiry > 0){
            $array[$j]["amount_payable"] = $row["amount_payable"];
            if ($row["from_id"]) {
                $array[$j]["from_id"] = $this->validation_model->IdToUserName($row["from_id"]);
            } else {
                $array[$j]["from_id"] = "NA";
            }
            $tot_amount+=$array[$j]["amount_payable"];
            $array[$j]['tot_amount'] = $tot_amount;
            $prod_id   = $this->getPackageId($row['product_id']);
            $prod_name = $this->getProdName($prod_id);
            $array[$j]['package'] = $prod_name;
            $array[$j]['date_of_submission'] = $row["date_of_submission"];
            $array[$j]['user_id'] = $row['user_id'];
            $j++;
            }
            $i++;
        }
        return $array;
     }

    public function getMaturedDepositDetails($user_id ='') {
        $array = array();
        $this->db->select('amount_payable,user_id,from_id,product_id,date_of_submission');
        $this->db->from('leg_amount as leg');
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        $this->db->where('amount_type', 'daily_investment');
        $query = $this->db->get();
        $i = 0;
        $j = 0;
        $tot_amount = 0;
        foreach ($query->result_array() as $row) {
            $prod_id                     = $this->getPackageId($row['product_id']);
            $count_leg_amount            = $this->getCountLegamount($row['user_id'],$prod_id);
            $days                        =  $this->getRoiDays($row['user_id'],$prod_id);
            $expiry                      = $days - $count_leg_amount;
            if($expiry == 0){
            $array[$j]["amount_payable"] = $row["amount_payable"];
            if ($row["from_id"]) {
                $array[$j]["from_id"] = $this->validation_model->IdToUserName($row["from_id"]);
            } else {
                $array[$j]["from_id"] = "NA";
            }
            $tot_amount+=$array[$j]["amount_payable"];
            $array[$j]['tot_amount'] = $tot_amount;
            $prod_id   = $this->getPackageId($row['product_id']);
            $prod_name = $this->getProdName($prod_id);
            $array[$j]['package'] = $prod_name;
            $array[$j]['date_of_submission'] = $row["date_of_submission"];
            $j++;
            }
            $i++;
        }
        return $array;
    }

    public function getRoiDays($user_id,$prod_id) {
        $this->db->select('days');
        $this->db->from('roi_order');
        $this->db->where('user_id', $user_id);
        $this->db->where('prod_id', $prod_id);
        $res = $this->db->get();
        foreach ($res->result() as $row) {
            $days = $row->days;
        }
        return $days;
    }

    public function getUsersByKeyword($keyword, $type = '') {
        $this->db->select('user_name');
        $this->db->like('user_name', $keyword, 'after');
        if($type != ''){
            $this->db->where('user_type !=', $type);
        }
        $this->db->where('delete_status', "active");
        $this->db->order_by('id');
        $this->db->limit(500);
        $query = $this->db->get('ft_individual');
        $response = [];
        foreach ($query->result_array() as $row) {
            $response[] = $row['user_name'];
        }
        return $response;
    }

    public function getDownlineUsersByKeyword($log_user_id, $keyword) {
        $this->load->model('tree_model');
        $left_right = $this->tree_model->getUserLeftRightNode($log_user_id);
        $this->db->select('f.user_name');
        $this->db->from('ft_individual f');
        $this->db->join('tree_parser t', 't.ft_id = f.id', 'LEFT');
        $this->db->like('f.user_name', $keyword, 'after');
        $this->db->where("t.left_father >=", $left_right['left']);
        $this->db->where("t.left_father <=", $left_right['right']);
        $this->db->order_by('t.left_father');
        $this->db->order_by('f.id');
        $this->db->limit(500);
        $query = $this->db->get();
        $response = [];
        foreach ($query->result_array() as $row) {
            $response[] = $row['user_name'];
        }
        return $response;
    }
    public function getRankData($limit = 4, $user_id = '')
     {     
       $ranks = [];
       $this->db->select('rank_name, rank_id');
       $this->db->from('rank_details');
       $this->db->order_by('rank_id', 'DESC');
       $ranks = $this->db->get()->result();
       foreach($ranks as $rank) {
           $this->db->select("COUNT(ft.id) as count");
           $this->db->from('ft_individual as ft');
           $this->db->where([
                //'ft.sponsor_id' => $user_id,
                'ft.user_rank_id' => $rank->rank_id,
            ]);
            if($user_id != ''){
            $this->db->where('ft.sponsor_id',$user_id);
            }
            $this->db->order_by('ft.user_rank_id', 'DESC');
           $rank->count  = $this->db->get()->row('count');
       }
       $ranks_array = [];
       foreach($ranks as $key => $rank) {
        $ranks_array[$key]['rank_name'] = $rank->rank_name;
        $ranks_array[$key]['count'] = $rank->count;

       }
       $data = array_column($ranks_array, 'count');
       array_multisort($data, SORT_DESC, $ranks_array);
       $ranks_array = array_slice($ranks_array, 0, 4);
      return $ranks_array;  
    }

    public function getAllIncomeOrExpense($user_id, $debit_credit, $limit = 0)
    {
        $this->db->select("SUM(amount) AS amount, CASE WHEN amount_type = 'purchase_donation' THEN 'donation' WHEN ewallet_type = 'ewallet_payment' THEN CONCAT(ewallet_type, '_', amount_type) WHEN amount_type = 'user_credit' OR amount_type = 'user_debit' THEN ewallet_type ELSE amount_type END AS amount_type, user_id");
        $this->db->from('ewallet_history');
        $this->db->where('user_id', $user_id);
        $this->db->where('type', $debit_credit);
        $this->db->group_by('amount_type');
        $this->db->order_by('amount', 'desc');
        $this->db->limit($limit);
        $quey_set[] = $this->db->get_compiled_select();
        $query = implode(" UNION ALL ", $quey_set);
        $dbprefix = $this->db->dbprefix;
        $res = $this->db->query("SELECT t.* FROM ({$query}) t LEFT JOIN {$dbprefix}ft_individual f ON (t.user_id = f.id)");
        return $res->result_array();
    }

    public function userPayoutRequests($user_id) {
        return $this->db->select('r.payment_method, r.requested_date, r.requested_amount, r.status')
                ->from('payout_release_requests As r')
                ->where('r.requested_user_id', $user_id)
                ->get()->result_array();
    }

    public function individulaDetails($user_id, $select_data) {
        $query = $this->db->select($select_data)
        ->from('ft_individual')
        ->where('id', $user_id)
        ->get();
        return $query->row();
    }

    public function getIncomeBonusBarChartData($type = "month")
    {
        $wallet_details = [];
        $labels = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $incomeArray = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $bonusArray = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $from_date = date("Y-01-01 00:00:00");
        if ($type == "month" && date("m") != 12) {
            $labelsPre = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            $labels = [];
            $curMonth = date("m") * 1;
            $curMonthIndex = $curMonth - 1;

            for ($i = ($curMonthIndex + 1); $i < count($labelsPre); $i++) {
                $labels[] = $labelsPre[$i] . " " . (date("Y") - 1);
            }
            for ($i = 0; $i <=  $curMonthIndex; $i++) {
                $labels[] = $labelsPre[$i] . " " . date("Y");
            }
            $nextMonthFirstDay = date("Y-" . ($curMonth + 1) . "-01");
            $from_date = date("Y-m-01", strtotime("-1 year", strtotime($nextMonthFirstDay)));
        }
        if($type == "year") {
            $incomeArray = [0, 0, 0, 0, 0, 0];
            $bonusArray = [0, 0, 0, 0, 0, 0];
            $curYear = date("Y") * 1;
            $labels = [];
            $i = $curYear - 5; // 6 - 1
            while($i <= $curYear) {
                $labels[] = $i;
                $i++;
            }
            $from_date = date("Y-01-01 00:00:00", strtotime("-5 year"));
        }

        if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
            $select = "SUM(total) as total";
            if ($type == "month") {
                if (date("m") == 12) {
                    $select = "$select, (MONTH(date_added) - 1) as labelid";
                } else {
                    $select = "$select, (12 - TIMESTAMPDIFF(MONTH, DATE_FORMAT(date_added, '%Y/%m/01'), '" . date("Y/m/01") . "') - 1) as labelid";
                }
            }
            if ($type == "year") {
                $select = "$select , (YEAR(date_added) - " . $labels[0] . ") as labelid";
            }
            $this->db->select($select);
            $this->db->where("order_status_id >", 0);
            $this->db->where("date_added >=", $from_date);
            $this->db->group_by("labelid");
            $dbResultArray = $this->db->get('oc_order')->result_array();

            foreach ($dbResultArray as $value) {
                $incomeArray[$value["labelid"]] += $value['total'];
            }
        } else {
            $select = "SUM(total_amount) as total_amount, SUM(reg_amount) as reg_amount, SUM(product_amount) as product_amount";
            if ($type == "month") {
                if (date("m") == 12) {
                    $select = "$select, (MONTH(reg_date) - 1) as labelid";
                } else {
                    $select = "$select, (12 - TIMESTAMPDIFF(MONTH, DATE_FORMAT(reg_date, '%Y/%m/01'), '" . date("Y/m/01") . "') - 1) as labelid";
                }
            }
            if ($type == "year") {
                $select = $select . " , (YEAR(reg_date) - " . $labels[0] . ") as labelid";
            }
            $this->db->select($select);
            $this->db->where("reg_date >=", $from_date);
            $this->db->group_by("labelid");
            $dbResultArray = $this->db->get('infinite_user_registration_details')->result_array();
            
            foreach ($dbResultArray as $value) {
                $incomeArray[$value["labelid"]] += $value['reg_amount'];
                if ($this->MODULE_STATUS['product_status'] == 'yes') {
                    $incomeArray[$value["labelid"]] += $value['product_amount'];
                }
            }

            if ($this->MODULE_STATUS['repurchase_status'] == 'yes') {
                $select = "SUM(total_amount) as total_amount";
                if ($type == "month") {
                    if (date("m") == 12) {
                        $select = "$select, (MONTH(order_date) - 1) as labelid";
                    } else {
                        $select = "$select, (12 - TIMESTAMPDIFF(MONTH, DATE_FORMAT(order_date, '%Y/%m/01'), '" . date("Y/m/01") . "') - 1) as labelid";
                    }
                }
                if ($type == "year") {
                    $select = "$select , (YEAR(order_date) - " . $labels[0] . ") as labelid";
                }
                $this->db->select($select);
                $this->db->where('order_status', 'confirmed');
                $this->db->where("order_date >=", $from_date);
                $this->db->group_by("labelid");
                $dbResultArray = $this->db->get('repurchase_order')->result_array();

                foreach ($dbResultArray as $value) {
                    $incomeArray[$value["labelid"]] += $value['total_amount'];
                }
            }

            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $select = "SUM(amount) as amount";
                if ($type == "month") {
                    if ($type == "month") {
                        if (date("m") == 12) {
                            $select = "$select, (MONTH(date_added) - 1) as labelid";
                        } else {
                            $select = "$select, (12 - TIMESTAMPDIFF(MONTH, DATE_FORMAT(date_added, '%Y/%m/01'), '" . date("Y/m/01") . "') - 1) as labelid";
                        }
                    }
                }
                if ($type == "year") {
                    $select = "$select , (YEAR(date_added) - " . $labels[0] . ") as labelid";
                }
                $this->db->select($select);
                $this->db->where("date_added >=", $from_date);
                $this->db->group_by("labelid");
                $dbResultArray = $this->db->get('upgrade_sales_order')->result_array();

                foreach ($dbResultArray as $value) {
                    $incomeArray[$value["labelid"]] += $value['amount'];
                }
            }
            if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                $select = "SUM(total_amount) as total_amount";
                if ($type == "month") {
                    if (date("m") == 12) {
                        $select = "$select, (MONTH(date_submitted) - 1) as labelid";
                    } else {
                        $select = "$select, (12 - TIMESTAMPDIFF(MONTH, DATE_FORMAT(date_submitted, '%Y/%m/01'), '" . date("Y/m/01") . "') - 1) as labelid";
                    }
                }
                if ($type == "year") {
                    $select = "$select , (YEAR(date_submitted) - " . $labels[0] . ") as labelid";
                }
                $this->db->select($select);
                $this->db->group_by("labelid");
                $this->db->where("date_submitted >=", $from_date);
                $dbResultArray = $this->db->get('package_validity_extend_history')->result_array();

                foreach ($dbResultArray as $value) {
                    $incomeArray[$value["labelid"]] += $value['total_amount'];
                }
            }
        }


        $select = "SUM(trans_fee) as trans_fee";
        if ($type == "month") {
            if (date("m") == 12) {
                $select = "$select, (MONTH(date) - 1) as labelid";
            } else {
                $select = "$select, (12 - TIMESTAMPDIFF(MONTH, DATE_FORMAT(date, '%Y/%m/01'), '" . date("Y/m/01") . "') - 1) as labelid";
            }
        }
        if ($type == "year") {
            $select = "$select , (YEAR(date) - " . $labels[0] . ") as labelid";
        }
        $this->db->select($select);
        $this->db->where('amount_type', 'user_credit');
        $this->db->group_by("labelid");
        $this->db->where("date >=", $from_date);
        $dbResultArray = $this->db->get('fund_transfer_details')->result_array();
        
        foreach ($dbResultArray as $value) {
            $incomeArray[$value["labelid"]] += $value['trans_fee'];
        }

        $enabled_bonus_list = $this->ewallet_model->getEnabledBonusList();

        $select = "SUM(tds + service_charge) as total";
        if ($type == "month") {
            if (date("m") == 12) {
                $select = "$select, (MONTH(date_of_submission) - 1) as labelid";
            } else {
                $select = "$select, (12 - TIMESTAMPDIFF(MONTH, DATE_FORMAT(date_of_submission, '%Y/%m/01'), '" . date("Y/m/01") . "') - 1) as labelid";
            }
        }
        if ($type == "year") {
            $select = "$select , (YEAR(date_of_submission) - " . $labels[0] . ") as labelid";
        }
        $this->db->select($select);
        $this->db->where_in('amount_type', $enabled_bonus_list);
        $this->db->group_by("labelid");
        $this->db->where("date_of_submission >=", $from_date);
        $dbResultArray = $this->db->get('leg_amount')->result_array();

        foreach ($dbResultArray as $value) {
            $incomeArray[$value["labelid"]] += $value['total'];
        }

        $select = "SUM(payout_fee) as payout_fee";
        if ($type == "month") {
            if (date("m") == 12) {
                $select = "$select, (MONTH(paid_date) - 1) as labelid";
            } else {
                $select = "$select, (12 - TIMESTAMPDIFF(MONTH, DATE_FORMAT(paid_date, '%Y/%m/01'), '" . date("Y/m/01") . "') - 1) as labelid";
            }
        }
        if ($type == "year") {
            $select = "$select , (YEAR(paid_date) - " . $labels[0] . ") as labelid";
        }
        $this->db->select($select);
        $this->db->where("payout_fee >", 0);
        $this->db->group_by("labelid");
        $this->db->where("paid_date >=", $from_date);
        $dbResultArray = $this->db->get("amount_paid")->result_array();
        
        foreach ($dbResultArray as $value) {
            $incomeArray[$value["labelid"]] += $value['payout_fee'];
        }
        
        $select = "SUM(amount_payable) as total";
        if ($type == "month") {
            if (date("m") == 12) {
                $select = "$select, (MONTH(date_of_submission) - 1) as labelid";
            } else {
                $select = "$select, (12 - TIMESTAMPDIFF(MONTH, DATE_FORMAT(date_of_submission, '%Y/%m/01'), '" . date("Y/m/01") . "') - 1) as labelid";
            }
        }
        if ($type == "year") {
            $select = "$select , (YEAR(date_of_submission) - " . $labels[0] . ") as labelid";
        }
        $this->db->select($select);
        $this->db->where_in('amount_type', $enabled_bonus_list);
        $this->db->group_by("labelid");
        $this->db->where("date_of_submission >=", $from_date);
        $dbResultArray = $this->db->get('leg_amount')->result_array();
        
        foreach ($dbResultArray as $value) {
            $bonusArray[$value["labelid"]] += $value['total'];
        }

        $incomeStringArray = [];
        $bonusStringArray = [];
        for($i = 0;$i < count($incomeArray); $i++) {
            $incomeStringArray[$i] = format_currency($incomeArray[$i]);
            $bonusStringArray[$i] = format_currency($bonusArray[$i]);
            $incomeArray[$i] = round($incomeArray[$i] * $this->DEFAULT_CURRENCY_VALUE, $this->PRECISION);
            $bonusArray[$i] = round($bonusArray[$i] * $this->DEFAULT_CURRENCY_VALUE, $this->PRECISION);
        }
        return compact("labels", "incomeArray", "bonusArray", "incomeStringArray", "bonusStringArray");
    }
    public function getincomeAndCommissionDetails()
    {
        $this->load->model('ewallet_model');
        $details = $this->ewallet_model->getBusinessWalletDetails();
        $income = array();
        $commission = array();
        $incomeAndComm = array();
        $i = 0;
        //top 4 income
        foreach ($details as $key => $value) {
            if($value['type'] == 'income'){
            $income[$i]['type'] = $key;
            $income[$i]['amount'] = $value['amount'];
            
            $i++;
            } 
            
        }
        
        usort($income, function($a, $b) {
            if($a['amount']==$b['amount']) return 0;
            return $a['amount'] < $b['amount']?1:-1;
        });

        $income = array_slice($income,0,4);


        // Top 4 commission 
        $i = 0;
        foreach ($details as $key => $value) {
            if($value['type'] == 'commission'){
            $commission[$i]['type'] = $key;
            $commission[$i]['amount'] = $value['amount'];
            
            $i++;
            } 
            
        }
        
        usort($commission, function($a, $b) {
            if($a['amount']==$b['amount']) return 0;
            return $a['amount'] < $b['amount']?1:-1;
        });

         $commission = array_slice($commission,0,4);

         $incomeAndComm['income'] = $income;
         $incomeAndComm['commission'] = $commission;

         return $incomeAndComm;

    }

    public function getJoiningLineChartData($user_id, $chartMode = "month")
    {
        $labels = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $joinArray = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $from_date = date("Y-01-01 00:00:00");
        if($chartMode == "month" && date("m") != 12) {
            $labelsPre = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            $labels = [];
            $curMonth = date("m") * 1;
            $curMonthIndex = $curMonth - 1;

            for($i = ($curMonthIndex + 1);$i < count($labelsPre);$i++) {
                $labels[] = $labelsPre[$i] . " " . (date("Y") - 1);
            }
            for ($i = 0; $i <=  $curMonthIndex; $i++) {
                $labels[] = $labelsPre[$i] . " " . date("Y");
            }
            $nextMonthFirstDay = date("Y-" . ($curMonth + 1) . "-01");
            $from_date = date("Y-m-01", strtotime("-1 year", strtotime($nextMonthFirstDay)));
        }
        if ($chartMode == "year") {
            $joinArray = [0, 0, 0, 0, 0, 0];
            $curYear = date("Y") * 1;
            $labels = [];
            $i = $curYear - 5; // 6 - 1
            while ($i <= $curYear) {
                $labels[] = $i;
                $i++;
            }
            $from_date = date("Y-01-01 00:00:00", strtotime("-5 year"));
        }
        if ($chartMode == "day") {
            $labels = [];
            $joinArray = [];
            $from_date = date("Y-m-d", strtotime("-9 days"));
            $dt = $from_date;
            $dt_end = date("Y-m-d");
            while($dt <= $dt_end) {
                if(date("Y-m", strtotime($from_date)) == date("Y-m", strtotime($dt_end))) {
                    $labels[] = date("M d", strtotime($dt));
                } elseif (date("Y", strtotime($from_date)) == date("Y", strtotime($dt_end))) {
                    $labels[] = date("M d", strtotime($dt));
                } else {
                    $labels[] = date("Y-m-d", strtotime($dt));
                }
                
                $joinArray[] = 0;
                $dt = date("Y-m-d", strtotime("+1 day", strtotime($dt)));
            }
        }

        if($this->MODULE_STATUS["mlm_plan"] == "Binary") {
            $leftJoinArray = $joinArray;
            $rightJoinArray = $joinArray;
            $left_user_id = $this->joining_model->getLeftNodeId($user_id);
            $right_user_id = $this->joining_model->geRighttNodeId($user_id);
            $select = "COUNT(f.id) as joining_count";
            if ($chartMode == "year") {
                $select = "$select, (YEAR(date_of_joining) - " . $labels[0] . ") as labelid";
            }
            if ($chartMode == "month") {
                if (date("m") == 12) {
                    $select = "$select, (MONTH(date_of_joining) - 1) as labelid";
                } else {
                    $select = "$select, (12 - TIMESTAMPDIFF(MONTH, DATE_FORMAT(f.date_of_joining, '%Y/%m/01'), '".date("Y/m/01")."') - 1) as labelid";
                }
            }
            if ($chartMode == "day") {
                $select = "$select, (9 - DATEDIFF('" . date("Y/m/d") . "', DATE_FORMAT(f.date_of_joining, '%Y/%m/%d'))) as labelid";
            }

            if($left_user_id) {
                $this->db->select($select);
                $this->db->from('ft_individual f');
                $this->db->join('treepath t', 't.descendant = f.id', 'LEFT');
                $this->db->where("t.ancestor", $left_user_id);
                $this->db->where("f.date_of_joining >=", $from_date);
                $this->db->group_by("labelid");
                $array1 = $this->db->get()->result_array();
                foreach ($array1 as $value1) {
                    $leftJoinArray[$value1['labelid']] += $value1["joining_count"];
                }
            }
            
            if($right_user_id) {
                // $arr2 = $this->validation_model->getUserLeftAndRight($right_user_id, "father");
                $this->db->select($select);
                $this->db->from('ft_individual f');
                $this->db->join('treepath t', 't.descendant = f.id', 'LEFT');
                $this->db->where("t.ancestor", $right_user_id);
                $this->db->where("f.date_of_joining >=", $from_date);
                $this->db->group_by("labelid");
                $array2 = $this->db->get()->result_array();
                foreach ($array2 as $value2) {
                    $rightJoinArray[$value2['labelid']] += $value2["joining_count"];
                }
            }
            return compact("labels", "leftJoinArray", "rightJoinArray");
        }

        $select = "COUNT(f.id) as joining_count";
        if ($chartMode == "year") {
            $select = "$select, (YEAR(date_of_joining) - " . $labels[0] . ") as labelid";
        }
        if ($chartMode == "month") {
            if (date("m") == 12) {
                $select = "$select, (MONTH(date_of_joining) - 1) as labelid";
            } else {
                $select = "$select, (12 - TIMESTAMPDIFF(MONTH, DATE_FORMAT(f.date_of_joining, '%Y/%m/01'), '" . date("Y/m/01") . "') - 1) as labelid";
            }
        }
        if ($chartMode == "day") {
            $select = "$select, (9 - DATEDIFF('" . date("Y/m/d") . "', DATE_FORMAT(f.date_of_joining, '%Y/%m/%d'))) as labelid";
        }
        if($user_id != $this->validation_model->getAdminId()) {
            // $arr = $this->validation_model->getUserLeftAndRight($user_id, "father");
            $this->db->select($select);
            $this->db->from('ft_individual f');
            $this->db->join('treepath t', 't.descendant = f.id', 'LEFT');
            $this->db->where("t.ancestor", $user_id);
        } else {
            //$arr = $this->validation_model->getUserLeftAndRight($user_id, "father");
            $this->db->select($select);
            $this->db->from('ft_individual f');
        }
        $this->db->where("active !=", "server");
        $this->db->where("date_of_joining >=", $from_date);
        $this->db->group_by("labelid");
        $array = $this->db->get()->result_array();
        foreach ($array as $value) {
            $joinArray[$value['labelid']] += $value["joining_count"];
        }
        return compact("labels", "joinArray");
    }

    public function getUserDashboardConfig()
    {
        $array =  $this->db->select("item, status")
                        ->get("user_dashboard_items")
                        ->result_array();
        $details = [];
        foreach ($array as $row) {
            $details[$row["item"]] = $row["status"];
        }
        return $details;
    }

    public function getLeftRightPV($user_id)
    {
        return $this->db->select('total_left_carry, total_right_carry')
                    ->where('id', $user_id)
                    ->limit(1)
                    ->get('ft_individual')
                    ->row_array();
    }


    public function getUsersByKeywordByCountry($keyword,$country_id='') {
        $this->db->select('ft_individual.user_name');
        $this->db->like('ft_individual.user_name', $keyword, 'after');
        $this->db->join("user_details as u", "u.user_detail_refid = ft_individual.id", "inner");
        $this->db->where('ft_individual.delete_status', "active");
        $this->db->where('u.user_detail_country', $country_id);
        $this->db->order_by('ft_individual.id');
        $this->db->limit(500);
        $query = $this->db->get('ft_individual');
        $response = [];
        foreach ($query->result_array() as $row) {
            $response[] = $row['user_name'];
        }
        return $response;
    }

    public function getCountOfSteps($type, $from_date = '', $to_date = '',$month='',$user_id = '',$category=''){
     $date = date("Y-m-d H:i:s");
        
        $i = 0;
        $details = array();
        $count = 0;
        $this->db->select("sum(l.total_leg) AS count");
        $this->db->from('leg_amount as l');
        $this->db->join('ft_individual as f', 'f.id=l.user_id', 'INNER');
        if ($type != '') {
            $this->db->where_in('amount_type', $type);
        }
        if($category =='today'){
            if ($from_date != '') {
                $this->db->where('date(l.date_of_submission) >=',$from_date);
            }
            if ($to_date != '') {
                $this->db->where('date(l.date_of_submission) <=',$to_date);
            }
        }
        if($category == 'month'){
            $this->db->where('month(l.date_of_submission) >=',$month);
        }
        if ($user_id != '') {
            $this->db->where("user_id", $user_id);
        } else {
           // $this->db->group_by('user_id');
        }
        //$this->db->group_by('amount_type');
            
        $query = $this->db->get(); 
        //return $this->db->affected_rows();
        return $query->row_array()['count']??0;

    }

    public function getRwalletSteps($user_id = ''){
     $date = date("Y-m-d H:i:s");
        
        $rwallet_total = 0;
        if ($user_id == "") {
            $this->db->select_sum('amount');
            $this->db->from('rwallet_history');
            $query = $this->db->get();
            foreach ($query->result() as $row) {
                $rwallet_total = $row->amount;
            }
        } else {
            $this->db->select('amount');
            $this->db->from('rwallet_history');
            $this->db->where("user_id", $user_id);
            $query = $this->db->get();
            foreach ($query->result() as $row) {
                $rwallet_total = $row->amount;
            }
        }

        return $rwallet_total;

    }

    public function getAgentsByKeyword($keyword, $type = '') {
        $this->db->select('CONCAT(agent_firstname," ",agent_secondname) as fullname');
        $this->db->select('agent_username');
        $this->db->like('agent_username', $keyword, 'after');
        $this->db->or_like('agent_firstname', $keyword, 'after');
        $this->db->or_like('agent_secondname', $keyword, 'after');
        if($type != ''){
            $this->db->where('user_type !=', $type);
        }
        $this->db->where('status', "yes");
        $this->db->order_by('id');
        $this->db->limit(500);
        $query = $this->db->get('agents');
        $response = [];
        foreach ($query->result_array() as $row) {
            $response[] = $row['agent_username'];
        }
        return $response;
    }
    public function getTodaysBinaryCount($user_id){
        $sum=0;
        $this->db->select_sum('total_leg');
        $this->db->where('user_id',$user_id);
        $this->db->where('amount_type','leg');
        $this->db->like('date_of_submission',date('Y-m-d'));
        $res=$this->db->get('leg_amount');
        return $res->row_array()['total_leg'] ?? 0;
    }
    public function getProductAmountFromChild($user_id){
        $amount=0;
        $this->db->select('product_amount');
        $this->db->where('sponsor_id',$user_id);
        $this->db->where('placement_id',$user_id);
        $res=$this->db->get('infinite_user_registration_details');
        return $res->row_array()['product_amount'] ?? 0;
    }
    public function getFoundersPoolBonus($user_id){
        // $this->db->select_sum('amount_payable');
        // $this->db->where('amount_type','founder_bonus');
        // $this->db->where('user_id',$user_id);
        // $res=$this->db->get('leg_amount');
        // return $res->row_array()['amount_payable']??0;
        $top_sum_amount_payable = $this->getSumTopUserAmountPayable();
        $pool_amount = ($top_sum_amount_payable>0)?$top_sum_amount_payable*(40/100):0;
        return $pool_amount;
    }
    function getPoolShares(){
        $top_sum_amount_payable = $this->getSumTopUserAmountPayable();
        $pool_amount = ($top_sum_amount_payable>0)?$top_sum_amount_payable*(40/100):0;
        $this->load->model('cron_model');
        $highest_ranks = $this->getRanksForPoolBonus();
        $pool['Gold']=0;
        $pool['Ruby']=0;
        $pool['Diamond']=0;
        $pool['Legend']=0;
        if($pool_amount>0){
            foreach ($highest_ranks as $ranks) {
                    $pool[$ranks['rank_name']] = $pool_amount * ($ranks['pool_bonus_perc'] / 100);
            }
        }
        return $pool;
    }
    function getSumTopUserAmountPayable(){
        $date=date('Y-m');
        $this->db->select_sum('amount_payable');
        $this->db->where('amount_type','leg');
        $this->db->like('date_of_submission',$date);
        $this->db->where_in('user_id',array(32,33,34));
        $res=$this->db->get('leg_amount');
        return $res->row_array()['amount_payable']??0;
    }
    public function getRanksForPoolBonus() {
        $rank_ids = [];
        $level = 1;
        $this->db->select('rank_id,rank_name,pool_bonus_perc');
        $this->db->order_by('rank_id', 'DESC');
        $this->db->where('pool_status','yes');
        $query = $this->db->get('rank_details');
        foreach ($query->result_array() as $row) {
            $rank_ids[]=$row;
        }
        return $rank_ids;
    }
    public function getTravelVoucher($user_id){
        $this->db->select_sum('voucher');
        $this->db->where('user_id',$user_id);
        $res=$this->db->get('voucher_history');
        return $res->row_array()['voucher']??0;
    }
    public function getMaxOutDays($user_id){
        $this->db->where('user_id',$user_id);
        return $this->db->count_all_results('binary_max_history');
    }
    public function selectRankDetailsPromo()
    {
        $obj_arr=array();
        $this->db->where('status','active');
        $query = $this->db->get('rank_promo');
        $i=0;
        foreach ($query->result_array() as $row) {
            $obj_arr[$i]['rank_name'] = $row['rank_name'];
            $obj_arr[$i]['group_pv'] = $row['group_pv'];
            $obj_arr[$i]['direct'] = $row['direct'];
            $obj_arr[$i]['bonus'] = $row['bonus'];
            $obj_arr[$i]['voucher'] = $row['voucher'];
            $obj_arr[$i]['group_pv_percent'] = $row['group_pv_percent'];
            $i++;
        }
        return $obj_arr;
    }
    public function getPoolDetails(){
        $this->load->model('cron_model');
        //$company_income = $this->cron_model->getPoolIncome();
        $top_sum_amount_payable = $this->getSumTopUserAmountPayable();

        $company_income = $top_sum_amount_payable*(40/100);
        //$company_amount = $top_sum_amount_payable*(20/100);
        $founder_amount = $top_sum_amount_payable*(60/100);
        $ranks=$this->getAllRank();
        $details=array();
        $details[0]['rank_name']='Founder';
        $details[0]['amount']=$founder_amount;
        $i=1;
        foreach ($ranks as $rank) {
            $details[$i]=$rank;
            $pool_perc = $rank['pool_bonus_perc'];
            $pool_amount=round($company_income*$pool_perc/100,2);
            $details[$i]['amount']=$pool_amount;
            $i++;
        }
        
        return $details;
    }
    function getAllRank(){
        $this->db->select('pool_bonus_perc,rank_name');
        $this->db->where('pool_status','yes');
        $this->db->where('rank_status','active');
        $res=$this->db->get('rank_details');
        return $res->result_array();
    }
    // function getSumTopUserAmountPayable(){
    //     $last_cron_run=$this->getLastCronRundate();
    //     //  $start_date='2022-01-01';
    //     // $end_date='2022-02-28';
    //     $this->db->select_sum('amount_payable');
    //     $this->db->where('amount_type','leg');
    //     $this->db->where('date_of_submission>=',$last_cron_run);
    //     //$this->db->where("date_of_submission BETWEEN '{$start_date}' AND '{$end_date}'");
    //     $this->db->where_in('user_id',array(32,33,34));
    //     $res=$this->db->get('leg_amount');
    //     return $res->row_array()['amount_payable']??0;
    // }
    function getLastCronRundate(){
        $this->db->select('cron_start_time');
        $this->db->where('cron_name','monthly_profit_distribution');
        $this->db->order_by('id','desc');
        $this->db->limit(1);
        $res=$this->db->get('cron_history');
        return $res->row_array()['cron_start_time'];
    }
    public function getProductAmountAndPV($product_id) {
        $pair_value = 0;
        $product_value = 0;
        $MODULE_STATUS = $this->trackModule();

        if ($MODULE_STATUS['opencart_status'] == "no" || $MODULE_STATUS['opencart_status_demo'] == "no") {
            $this->db->select('pair_value');
            $this->db->select('product_value');
            $this->db->where('prod_id', $product_id);
            $query = $this->db->get('package');
            foreach ($query->result() as $row) {
                $pair_value = $row->pair_value;
                $product_value = $row->product_value;
            }
        } else {
            $this->db->select('pair_value,price AS product_value');
            $this->db->where('package_id', $product_id);
            $query = $this->db->get('oc_product');
            foreach ($query->result() as $row) {
                $pair_value = $row->pair_value;
                $product_value = $row->product_value;
            }
        }

        $amount['pair_value'] = $pair_value;
        $amount['product_value'] = $product_value;
        return $product_value;
    }
    
}
