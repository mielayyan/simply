<?php

class epin_model extends inf_model {
    public $OBJ_PIN;
    function __construct($product_status = '11') {
        parent::__construct();
        if (isset($this->uri->segments[1]) && $this->uri->segments[1] != 'mobile' && isset($this->inf_model->MODULE_STATUS['product_status'])) {
            $this->MODULE_STATUS = $this->inf_model->MODULE_STATUS;
            $product_status = $this->MODULE_STATUS['product_status'];
        }
        $this->load->model('misc_model');

        require_once 'Pin_model.php';

        $this->OBJ_PIN = new pin_model();

        if ($product_status == "yes") {
            $this->load->model('product_model');
        }
        $this->load->model('validation_model');
    }

    // Old Code
    /*public function generatePasscode($cnt, $status, $uploded_date, $pin_amount, $expiry_date, $pin_alloc_date, $purchase_status = '')
    {
        for ($i = 0; $i < $cnt; $i++) {
            $passcode = $this->misc_model->getRandStr(9, 9);

            $generated_user = $this->LOG_USER_ID;
            $user_type = $this->LOG_USER_TYPE;
            if ($user_type == 'employee') {
                $this->load->model('validation_model');
                $generated_user = $this->validation_model->getAdminId();
            }
            if ($this->LOG_USER_TYPE == 'admin' || $this->LOG_USER_TYPE == 'employee') {
                $allocated_user = "NULL";
            } else {
                $allocated_user = $generated_user;
            }
            $res = $this->OBJ_PIN->insertPasscode($passcode, $status, $uploded_date, $generated_user, $allocated_user, $pin_amount, $expiry_date, $pin_alloc_date, $purchase_status = '');
        }
        return $res;
    }*/

    public function pinSelector($page, $limit, $pages_selection, $keyword = "", $keyword1 = "")
    {
        $arr = array();

        switch ($pages_selection) {
            case 'generate':
                $arr['pin_numbers'] = $this->OBJ_PIN->getFreePins($page, $limit);
                break;
            case 'search':
                if ($keyword == "") {
                    $arr['pin_numbers'] = "";
                    $arr['numrows'] = "";
                }
                break;
            case 'active':
                $arr['pin_numbers'] = $this->OBJ_PIN->getActivePins($page, $limit);
                $arr['numrows'] = $this->OBJ_PIN->getAllActivePinspage();
                break;
            case 'inactive':
                $arr['pin_numbers'] = $this->OBJ_PIN->getInactivePins($page, $limit);
                $arr['numrows'] = $this->OBJ_PIN->getAllInactivePinspage();
                break;
            case 'defualt':
                $arr['pin_numbers'] = $this->OBJ_PIN->getActivePinsDefualt($page, $limit);
                $arr['numrows'] = $this->OBJ_PIN->getAllActivePinspageDefualt();
                break;
        }
        return $arr;
    }

    public function getUserFreePinCount()
    {
        $user_id = $this->LOG_USER_ID;
        $this->db->select("count(*) as cnt");
        $this->db->from("pin_numbers");
        $this->db->where("allocated_user_id", $user_id);
        $this->db->where_in("status", ['yes', 'no']);

        $search_my_active = $this->db->get();
        foreach ($search_my_active->result() as $row) {
            return $row->cnt;
        }
    }

    public function updateEPin($delete_id, $status)
    {
        return $this->OBJ_PIN->updatePasscode($delete_id, $status);
    }

    /*public function deleteEpin($delete_id, $action = '')
    {
        return $this->OBJ_PIN->deletePasscode($delete_id, $action);
    }*/

    public function deleteEpin($pin_id) {
        $this->db->set('status', 'delete');
        $this->db->where('pin_id', $pin_id);
        $res = $this->db->update('pin_numbers');
        if ($res) {
            
                //$this->load->model('ewallet_model');
                $pin_detail = $this->getPinInfo($pin_id);
                $agent_id=$pin_detail['generated_agent'];
                 if($agent_id){
                    //  $balamount = $this->validation_model->getAgentWalletBalance($agent_id);
                    //  $bal = round($balamount + $pin_detail['pin_balance_amount'], 8);
                    //  $update = $this->validation_model->updateAgentBalanceAmount($agent_id, $bal);
                    //  $this->validation_model->addAgentwalletHistory($agent_id,0 , $pin_detail['pin_id'], 'pin_purchase', $pin_detail['pin_balance_amount'], 'pin_purchase_delete', 'credit', 0,'',0,0);
                    $this->updateBalanceAmountDetailsTo($pin_detail['allocated_user_id'], $pin_detail['pin_balance_amount']);
                    $ewallet_id = $pin_id;
                    if($pin_detail['transaction_id']==''){
                        $pin_detail['transaction_id']=$pin_id;
                    }
                    $this->validation_model->addEwalletHistory($pin_detail['allocated_user_id'], 0, $ewallet_id, 'pin_purchase', $pin_detail['pin_balance_amount'], 'pin_purchase_delete', 'credit', $pin_detail['transaction_id']);
                 }elseif ($this->isPurchasedPin($pin_id) && $this->isNotExpiredPin($pin_id)) {
                    $this->updateBalanceAmountDetailsTo($pin_detail['allocated_user_id'], $pin_detail['pin_balance_amount']);
                    $ewallet_id = $pin_id;
                    $this->validation_model->addEwalletHistory($pin_detail['allocated_user_id'], 0, $ewallet_id, 'pin_purchase', $pin_detail['pin_balance_amount'], 'pin_purchase_delete', 'credit', $pin_detail['transaction_id']);
                }
                $this->load->model('cron_model');
                $this->cron_model->changePinRefundStatus($pin_id,'yes');
                
            }
        

        return $res;   
    }

    public function isPurchasedPin($pin_id) {
        $this->db->from('pin_numbers pn');
        $this->db->join('pin_purchases pp', 'pn.pin_numbers = pp.pin_numbers');
        $this->db->where('pn.pin_id', $pin_id);
        return $this->db->count_all_results();
    }

    public function isNotExpiredPin($pin_id) {
        $this->db->from('pin_numbers pn');
        $this->db->where('pn.pin_id', $pin_id);
        $this->db->where('DATE(pn.pin_expiry_date) >', 'DATE(NOW())', FALSE);
        return $this->db->count_all_results();
    }

    public function getPinInfo($pin_id) {
        $this->db->select('allocated_user_id,pin_balance_amount,transaction_id,generated_agent');
        $this->db->where('pin_id', $pin_id);
        $res = $this->db->get('pin_numbers');
        return $res->row_array();
    }

    public function updateBalanceAmountDetailsTo($to_user_id, $trans_amount) {
        $this->db->set('balance_amount', 'ROUND(balance_amount + ' . $trans_amount . ',8)', FALSE);
        $this->db->where('user_id', $to_user_id);
        $query = $this->db->update('user_balance_amount');
        return $query;
    }

    public function deleteAllEPin($pin_status, $page, $limit)
    {
        if ($pin_status != 'Active') {
            $pin_status = 'Blocked';
        }
        $result = false;
        switch ($pin_status) {
            case 'Active':
                $result = $this->OBJ_PIN->deleteActivePins($page, $limit);
                break;
            case 'Blocked':
                $result = $this->OBJ_PIN->deleteInactivePins($page, $limit);
                break;
            default:
                $result = false;
        }
        return $result;
    }

    public function ifChecked($id, $pin_count, $pin_alloc_date, $status, $uploded_date, $admin_id, $allocate_id, $rem_count, $amount, $expiry_date)
    {

        for ($m = 0; $m < $pin_count; $m++) {
            $passcode = $this->misc_model->getRandStr(9, 9);
            $res = $this->OBJ_PIN->insertPasscode($passcode, $status, $uploded_date, $admin_id, $allocate_id, $amount, $expiry_date, $pin_alloc_date);
        }
        $res = $this->OBJ_PIN->updatePinRequest($id, $rem_count, $pin_count);
        return $res;
    }

    public function viewEpinRequest($pro_status, $limit = '', $page = '')
    {

        $pin_detail_arr = $this->getAllPinRequest($limit, $page);
        $arr_length = count($pin_detail_arr);
        for ($i = 0; $i < $arr_length; $i++) {

            $user_id = $pin_detail_arr["detail$i"]["user_id"];
            $pin_detail_arr["detail$i"]["user_name"] = $this->validation_model->IdToUserName($user_id);
        }
        return $pin_detail_arr;
    }

    public function getAllPinRequestCount($read_status = '')
    {
        $this->db->select('count(*) as cnt');
        $this->db->from("pin_request");
        $this->db->where("status", "yes");
        if ($read_status)
            $this->db->where('read_status', $read_status);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            return $row->cnt;
        }
    }

    public function setEpinViewed($to_read_status)
    {
        $this->db->set('read_status', $to_read_status++);
        $this->db->where('read_status', $to_read_status);
        $this->db->update('pin_request');
        return;
    }
    public function insertPinRequest($req_user, $cnt, $request_date, $expiry_date, $pin_amount)
    {


        return $res = $this->OBJ_PIN->insertPinRequest($req_user, $cnt, $request_date, $expiry_date, $pin_amount);
    }

    public function getAllProducts($status)
    {
        return $this->product_model->getAllProducts('yes');
    }

    public function generateEpin($user_name, $amount, $count, $expiry_date)
    {
        $user_id = $this->userNameToId($user_name);
        $user = $this->session->userdata("inf_logged_in");
        $user_type = $user["user_type"];
        $gen_user_id = $user["user_id"];
        if ($user_type == 'employee') {
            $this->load->model('validation_model');
            $gen_user_id = $this->validation_model->getAdminId();
        }
        $status = "yes";
        $uploded_date = date('Y-m-d h:m:s');
        $pin_alloc_date = date('Y-m-d h:m:s');
        if ($user_name != "" && $count != "") {
            for ($i = 0; $i < $count; $i++) {
                $passcode = $this->misc_model->getRandStr(9, 9);
                $res = $this->insertPasscode($passcode, $status, $uploded_date, $gen_user_id, $user_id, $amount, $expiry_date, $pin_alloc_date);
            }
            return $res;
        }
    }

    public function userNameToId($user_name)
    {

        $this->db->select("id");
        $this->db->from("ft_individual");
        $this->db->where("user_name", $user_name);
        $result = $this->db->get();

        foreach ($result->result() as $row) {

            return $row->id;
        }
    }

    public function getProductId($product_id)
    {

        $this->db->select("prod_id");
        $this->db->from("product");
        $this->db->where("product_id", $product_id);
        $result = $this->db->get();


        foreach ($result->result() as $row) {
            return $row->prod_id;
        }
    }

    public function insertPasscode($passcode, $status, $pin_uploded_date, $generated_user, $allocate_id, $amount, $expiry_date, $pin_alloc_date = "",$user_type)
    {
        $generated_agent = $generated_user;
        if($user_type == 'agent'){
            $generated_user = NULL;
            $generated_agent = $generated_agent;
        }else{
            $generated_agent = NULL;
        }
        
        $array = array('pin_numbers' => $passcode, 'pin_alloc_date' => $pin_alloc_date, 'status' => $status, 'pin_uploded_date' => $pin_uploded_date, 'generated_user_id' => $generated_user, 'allocated_user_id' => $allocate_id, 'pin_amount' => $amount, 'pin_expiry_date' => $expiry_date, 'pin_balance_amount' => $amount,'user_type' => $user_type,'generated_agent' => $generated_agent);
        // dd($array);
        $this->db->set($array);
        $res = $this->db->insert('pin_numbers');
        
        if($generated_agent){
             $ewallet_id = $this->db->insert_id();
            $this->validation_model->addAgentwalletHistory($generated_agent,$allocate_id , $ewallet_id, 'pin_purchase', $amount, 'pin_purchase', 'debit', 0,'',0,0);
        }
        // dd($this->db->last_query($res));
        return $res;
    }

    /*public function getAllActivePinspage()
    {
        $num = $this->OBJ_PIN->getAllActivePinspage();
        return $num;
    }*/

    /*public function getMaxPinCount()
    {
        $maxpincount = $this->OBJ_PIN->getMaxPinCount();
        return $maxpincount;
    }*/

    public function getPinDetailsForUser11($user_name, $limit, $page)
    {
        $arr = array();
        if ($user_name != "") {
            $user_id = $this->userNameToId($user_name);

            $this->db->select("*");
            $this->db->from("pin_numbers");
            $this->db->where("allocated_user_id", $user_id);
            $this->db->where("status", 'yes');
            $this->db->limit($limit, $page);
            $result = $this->db->get();
            $i = 0;
            foreach ($result->result_array() as $row) {

                $arr[$i]["pin_numbers"] = $row['pin_numbers'];
                $arr[$i]["pin_uploded_date"] = $row['pin_uploded_date'];
                $arr[$i]["id"] = $row['pin_id'];
                $arr[$i]["expiry_date"] = $row['pin_expiry_date'];
                $arr[$i]["amount"] = $row['pin_amount'];
                $arr[$i]["pin_balance_amount"] = $row['pin_balance_amount'];
                $i++;
            }


            return $arr;
        }
    }

    public function getPinDetailsForUser11Count($user_name)
    {
        $user_id = $this->userNameToId($user_name);
        $this->db->select('count(*) as cnt');
        $this->db->from('pin_numbers');
        $this->db->where("allocated_user_id", $user_id);
        $this->db->where("status", 'yes');
        $result = $this->db->get();
        foreach ($result->result() as $row) {
            return $row->cnt;
        }
    }

    public function getUnallocatedPinCount()
    {
        $user_id = $this->LOG_USER_ID;
        $user_type = $this->LOG_USER_TYPE;
        if ($user_type == 'employee') {
            $this->load->model('validation_model');
            $user_id = $this->validation_model->getAdminId();
        }
        $date = date("Y-m-d");
        $this->db->select("COUNT(*) AS count");
        $this->db->from("pin_numbers");
        $this->db->where("allocated_user_id", "NA");
        $this->db->where("generated_user_id", $user_id);
        $this->db->where("status", "yes");
        $this->db->where('pin_expiry_date >=', $date);
        $this->db->where('pin_balance_amount >', 0);
        $this->db->like("status", "yes");
        $qr = $this->db->get();
        foreach ($qr->result() as $row) {
            return $row->count;
        }
    }

    public function getPinDetails($pin_number, $check_status = '')
    {

        $details = array();
        $i = 0;
        $this->db->select('f.user_name allocated_user_name,p.pin_numbers pin_number,p.status,p.allocated_user_id,p.pin_uploded_date pin_uploaded_date,p.pin_expiry_date,p.pin_amount,p.pin_balance_amount,p.used_user,p.pin_id,p.purchase_status');
        $this->db->from('pin_numbers p');
        $this->db->join('ft_individual f', 'f.id = p.allocated_user_id', 'left');
        $this->db->where('p.pin_numbers', $pin_number);
        if ($check_status != '') {
            $this->db->where('p.status !=', 'delete');
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getPinSearch($amount = '', $check_status = '', $page, $limit)
    {
        $this->db->select('f.user_name allocated_user_id,p.used_user,p.status,p.pin_numbers pin_number,p.pin_uploded_date pin_uploaded_date,p.pin_expiry_date,p.pin_balance_amount,p.pin_amount,p.pin_id,p.purchase_status');
        $this->db->from('pin_numbers p');
        $this->db->join('ft_individual f', 'f.id = p.allocated_user_id', 'left');
        if ($amount != '')
            $this->db->where('p.pin_amount', $amount);
        if ($check_status != '') {
            $this->db->where('p.status !=', 'delete');
        }
        $this->db->limit($limit, $page);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getPinSearchCount($amount = '', $check_status = '')
    {
        if ($amount != '')
            $this->db->where('pin_amount', $amount);
        if ($check_status != '') {
            $this->db->where('status !=', 'delete');
        }
        return $this->db->count_all_results('pin_numbers');
    }

    public function getAllEwalletAmounts()
    {
        $i = 0;
        $amount_detail = array();
        $this->db->select('id');
        $this->db->select('amount');
        $this->db->from('pin_amount_details');
        $this->db->order_by("amount", "asc");
        $res = $this->db->get();
        foreach ($res->result_array() as $row) {
            $amount_detail["details$i"]["id"] = $row['id'];
            $amount_detail["details$i"]["amount"] = $row['amount'];
            $i++;
        }
        return $amount_detail;
    }

    public function addPinAmount($amount)
    {
        $this->db->set('amount', $amount);
        $res = $this->db->insert('pin_amount_details');
        return $res;
    }

    public function deletePinAmount($id)
    {
       
       
        $this->db->where('id', $id);
        $res = $this->db->delete('pin_amount_details');
        return $res;
        
       
    }

    public function check_pin_amount($amount)
    {
        $flag = false;
        $this->db->select('id');
        $this->db->from('pin_amount_details');
        $this->db->where('amount', $amount);
        $this->db->limit(1);
        $res = $this->db->get();
        $amount_avilable = $res->num_rows();
        if ($amount_avilable > 0) {
            $flag = true;
        }
        return $flag;
    }

    public function getUserPinRequestCount($user_id, $status = "no", $read_status = '')
    {
        $this->db->select('count(*) as cnt');
        $this->db->from("pin_request");
        $this->db->where("req_user_id", $user_id);
        $this->db->where("status", $status);
        if ($read_status) {
            $this->db->where("read_status", $read_status);
        }
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            return $row->cnt;
        }
    }
    
    public function deleteAllRequestedEpin($requested_epins = [], $remark = "deleted ") {
        $this->db->set('status', 'deleted');
        $this->db->set('remark', $remark);
        $this->db->where_in("req_id", $requested_epins);
        $result = $this->db->update("pin_request");
        return $result;
    }
    //insert configuration_change_history
    public function getPinbyId($id)
    {
        $amount = '';
        $this->db->select('amount');

        $this->db->where("id =", $id);
        $this->db->from('pin_amount_details');
        $res = $this->db->get();
        foreach ($res->result_array() as $row) {
            $amount = $row['amount'];
        }
        return $amount;
    }
    //

    public function epinAllocation($user_id, $epin_id)
    {
        $this->db->set('allocated_user_id', $user_id);
        $this->db->where('pin_id', $epin_id);
        return $this->db->update('pin_numbers');
    }

    public function insertEpinTransferHistory($login_id, $activity, $user_id, $from_user_id, $epin_id, $data = '', $user_type = '')
    {
        $ip_adress = $this->IP_ADDR;
        //Code to convert Ipv6 address to Ipv4
        // if (!filter_var($ip_adress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
        //     $ip_adress = hexdec(substr($ip_adress, 0, 2)) . "." . hexdec(substr($ip_adress, 2, 2)) . "." . hexdec(substr($ip_adress, 5, 2)) . "." . hexdec(substr($ip_adress, 7, 2));
        // }
        $this->db->set('done_by', $login_id);
        if ($user_type != '') {
            $this->db->set('done_by_type', $user_type);
        } else {
            $this->db->set('done_by_type', $this->LOG_USER_TYPE);
        }
        $this->db->set('ip', $ip_adress);
        $this->db->set('user_id', $user_id);
        $this->db->set('from_user_id', $from_user_id);
        $this->db->set('epin_id', $epin_id);
        $this->db->set('activity', $activity);
        $this->db->set('date', date("Y-m-d H:i:s"));
        $this->db->set('data', $data);
        $result = $this->db->insert('epin_transfer_history');

        return $result;
    }

    public function getUserEpinList($user_id) {
        return $this->db->select("pin_id,pin_numbers")
            ->from("pin_numbers")
            ->where("status", "yes")
            ->where("allocated_user_id", $user_id)
            ->where('pin_expiry_date >=', date("Y-m-d"))
            ->where('pin_balance_amount >', 0)
            ->order_by("pin_id", "DESC")
            ->get()
            ->result_array();
    }

    public function getEpinList($user_id)
    {
        $i = 0;
        $date = date("Y-m-d");
        $epin_detail = array();

        $this->db->select("pin_id,pin_numbers");
        $this->db->from("pin_numbers");
        $this->db->where("status", "yes");
        $this->db->where("allocated_user_id", $user_id);
        $this->db->where('pin_expiry_date >=', $date);
        $this->db->where('pin_balance_amount >', 0);
        $this->db->where('pin_balance_amount = pin_amount');
        $this->db->order_by("pin_id", "DESC");
        $query = $this->db->get();

        foreach ($query->result_array() as $row) {
            $epin_detail["details$i"]["pin_id"] = $row['pin_id'];
            $epin_detail["details$i"]["pin_numbers"] = $row['pin_numbers'];
            $i++;
        }
        return $epin_detail;
    }
    public function EpinIdtoName($id)
    {
        $name = '';
        $this->db->select('pin_numbers');
        $this->db->where("pin_id =", $id);
        $this->db->from('pin_numbers');
        $res = $this->db->get();
        foreach ($res->result_array() as $row) {
            $name = $row['pin_numbers'];
        }
        return $name;
    }

    public function validateAllEpins($epin_details, $total_amount, $user_id, $upgrade_user_id = '')
    {
        $epin_valid = true;
        $epin_array = [];
        $i = 0;
        foreach ($epin_details as $v) {
            $epin_array[$i]['pin'] = $v;
            $epin_array[$i]['pin_amount'] = 0;
            $i++;
        }
        $result = [];
        foreach ($epin_array as $key => $value) {
            $epin = $value['pin'];
            $epin_details = $this->getEpinDetails($epin, $user_id, $upgrade_user_id);
            if ($epin_details) {
                $epin_amount = $epin_details['pin_amount'];
                $epin_used_amount = min($epin_amount, $total_amount);
                $epin_balance_amount = $epin_amount - $epin_used_amount;
                $total_amount = $total_amount - $epin_used_amount;
                $result[$key] = array(
                    'pin' => $epin,
                    'amount' => $epin_amount,
                    'balance_amount' => $epin_balance_amount,
                    'reg_balance_amount' => $total_amount,
                    'epin_used_amount' => $epin_used_amount
                );
            } else {
                $epin_valid = false;
                $result[$key] = array(
                    'pin' => 'nopin',
                    'amount' => 0,
                    'balance_amount' => 0,
                    'reg_balance_amount' => 0,
                    'epin_used_amount' => 0
                );
            }
        }
        $result['valid'] = $epin_valid;
        $result['amount_reached'] = $total_amount;
        return $result;
    }

    public function getEpinDetails($epin, $user_id, $upgrade_user_id = '')
    {
        $date = date('Y-m-d');
        $admin_userid = $this->validation_model->getAdminId();
        $this->db->select('pin_numbers,pin_balance_amount pin_amount,allocated_user_id');
        //$this->db->where('pin_numbers', $epin);
        $this->db->where("pin_numbers LIKE BINARY '$epin'", NULL, true);
        $this->db->group_start();
        $this->db->where('allocated_user_id', NULL);
        if ($this->LOG_USER_TYPE == 'admin' || $this->LOG_USER_TYPE == 'employee') {
            if ($upgrade_user_id != '') {
                // $whr = '(allocated_user_id=' . $user_id . '  or allocated_user_id=' . $admin_userid . '  or allocated_user_id=' . $upgrade_user_id . ' or allocated_user_id="NA" )';
                // $this->db->or_where('allocated_user_id', $user_id);
                $this->db->or_where('allocated_user_id', $admin_userid);
                $this->db->or_where('allocated_user_id', $upgrade_user_id);
            } else {
                // $whr = '(allocated_user_id=' . $user_id . ' or allocated_user_id="NA" )';
                $this->db->or_where('allocated_user_id', $user_id);
            }
        } else {
            // $whr = '(allocated_user_id=' . $user_id . ')';
            $this->db->or_where('allocated_user_id', $user_id);
        }
        $this->db->group_end();
        $this->db->where('pin_amount >', 0);
        $this->db->where('status', 'yes');
        $this->db->where('pin_expiry_date >=', $date);
        $this->db->limit(1);
        $query = $this->db->get('pin_numbers');
        $res = $query->row_array();
        if ($res) {
            return $res;
        } else {
            return false;
        }
    }

    public function epinPayment($pin_array, $user_id)
    {
        if (isset($pin_array['valid'])) {
            unset($pin_array['valid']);
        }
        if (isset($pin_array['amount_reached'])) {
            unset($pin_array['amount_reached']);
        }
        $date = date('Y-m-d H:i:s');
        foreach ($pin_array as $key => $value) {
            $pin_no = $value['pin'];
            $pin_balance = $value['balance_amount'];
            $pin_amount = $value['amount'];
            $status = 'yes';
            if ($pin_balance == 0) {
                $status = 'no';
            }
            $pin_balance = round($pin_balance, 8);
            $this->db->set('used_user', $user_id);
            $this->db->set('status', $status);
            $this->db->set('pin_balance_amount', round($pin_balance, 8));
            $this->db->where('pin_numbers', $pin_no);
            $this->db->where('status', 'yes');
            $res1 = $this->db->update('pin_numbers');

            $this->db->set('status', $status);
            $this->db->set('pin_number', $pin_no);
            $this->db->set('used_user', $user_id);
            $this->db->set('pin_alloc_date', $date);
            $this->db->set('pin_amount', round($pin_amount, 8));
            $this->db->set('pin_balance_amount', round($pin_balance, 8));
            $res2 = $this->db->insert('pin_used');
            if (!$res1 || !$res2) {
                return false;
            }
        }
        return true;
    }

    public function allocateEPinToUser($pin_id, $user_id)
    {
        $this->db->set('allocated_user_id', $user_id);
        $this->db->where('pin_id', $pin_id);
        $res = $this->db->update('pin_numbers');
        return $res;
    }

    public function getAllPinRequest($limit = '', $page = '')
    {
        $pin_detail_arr = array();
        $this->db->select("*");
        $this->db->from("pin_request");
        $this->db->where("status", "yes");
        $this->db->limit($limit, $page);
        $qr_pin_req = $this->db->get();

        $cnt = $qr_pin_req->num_rows();
        if ($cnt > 0) {
            $i = 0;
            foreach ($qr_pin_req->result_array() as $row_search) {
                $pin_detail_arr["detail$i"]["req_id"] = $row_search['req_id'];
                $pin_detail_arr["detail$i"]["user_id"] = $row_search['req_user_id'];
                $pin_detail_arr["detail$i"]["full_name"] = $this->validation_model->getUserFullName($row_search['req_user_id']);
                $pin_detail_arr["detail$i"]["phone_number"] = $this->validation_model->getUserPhoneNumber($row_search['req_user_id']);
                $pin_detail_arr["detail$i"]["pin_count"] = $row_search['req_pin_count'];
                $pin_detail_arr["detail$i"]["rem_count"] = $row_search['req_rec_pin_count'];
                $pin_detail_arr["detail$i"]["req_date"] = $row_search['req_date'];
                $pin_detail_arr["detail$i"]["expiry_date"] = $row_search['pin_expiry_date'];
                $pin_detail_arr["detail$i"]["amount"] = $row_search['pin_amount'];
                $i++;
            }
        }
        return $pin_detail_arr;
    }

    public function getEPinsByKeyword($letters)
    {
        $this->db->select('pin_numbers');
        $this->db->where('status !=', 'delete');
        $this->db->like('pin_numbers', $letters, 'after');
        $this->db->order_by('pin_id');
        $this->db->limit(500);
        $query = $this->db->get('pin_numbers');
        $response = [];
        foreach ($query->result_array() as $row) {
            $response[] = $row['pin_numbers'];
        }
        return $response;
    }

    public function isActivePin($pin_number)
    {

        $this->db->where('pin_id', $pin_number);
        $this->db->where('status', 'yes');
        //$this->db->where('purchase_status', 'yes');
        return $this->db->count_all_results('pin_numbers');
    }

    public function CheckEpinBelongsTouser( $user_id, $pin_id)
    {
        $flag = FALSE;
        $this->db->where('allocated_user_id', $user_id);
        $this->db->where('pin_id', $pin_id);
        $count = $this->db->count_all_results('pin_numbers');
        if ($count) {
            $flag = TRUE;
        }
        return $flag;
    }

    public function isEpinExist($epin) {
        $this->db->select('pin_id')->where('pin_numbers', $epin)->get('pin_numbers')->row();
        return $this->db->affected_rows() == 1 ? true : false;
    }

    
    public function getEpins($user_name = '', $epin = "", $amount = "", $status = "active", $limit = 20, $offset = 0) {
        $this->db->select('pin_id, pin_numbers, status,purchase_status,pin_expiry_date, pin_amount, pin_balance_amount, ft.user_name, CONCAT(user.user_detail_name, " ", user.user_detail_second_name) AS full_name');
        $this->db->join('ft_individual AS ft', 'ft.id = pin_numbers.allocated_user_id');
        $this->db->join('user_details AS user', 'user.user_detail_refid = ft.id');
        if($user_name != '') {
            $this->db->where('ft.user_name', $user_name);
        }
        if($epin != "") {
            $this->db->where('pin_numbers', $epin);
        }
        if($amount != "") {
            $this->db->where('pin_amount', $amount);
        }
        $this->db->where("status !=", "delete");
        switch($status) {
            case "active":
                $this->db->where("status", "yes");
                $this->db->where('pin_expiry_date >=', date('Y-m-d'));
                $this->db->where('pin_balance_amount >', 0);
            break;
            case "blocked":
                $this->db->where("status", "no");
            break;
            case "used_expired":
                $this->db->group_start();   
                    $this->db->where('pin_balance_amount <=', 0);
                    $this->db->or_where('pin_expiry_date <', date('Y-m-d'));
                $this->db->group_end();
            break;
            case "deleted":
                $this->db->where("status", "delete");
            break;
        }
        $this->db->order_by("pin_expiry_date");
        $this->db->limit($limit, $offset);
        $query = $this->db->get('pin_numbers');
        return $query->result_array();
    }

    public function getEpinsCount($user_name = '', $epin = "", $amount = "", $status = "active") {
        $this->db->select('count(pin_id) AS count');
        $this->db->join('ft_individual AS ft', 'ft.id = pin_numbers.allocated_user_id');
        if($user_name != '') {
            $this->db->where('ft.user_name', $user_name);
        }
        if($epin != "") {
            $this->db->where('pin_numbers', $epin);
        }
        if($amount != "") {
            $this->db->where('pin_amount', $amount);
        }
        $this->db->where("status !=", "delete");
        switch($status) {
            case "active":
                $this->db->where("status", "yes");
                $this->db->where('pin_expiry_date >=', date('Y-m-d'));
                $this->db->where('pin_balance_amount >', 0);
            break;
            case "blocked":
                $this->db->where("status", "no");
            break;
            case "used_expired":
                 $this->db->group_start();   
                    $this->db->where('pin_balance_amount <=', 0);
                    $this->db->or_where('pin_expiry_date <', date('Y-m-d'));
                $this->db->group_end();
            break;
            case "deleted":
                $this->db->where("status", "delete");
            break;
        }
        return $this->db->get('pin_numbers')->row('count');
    }

    public function deactivateEPins($pin_ids = []) {
        $this->db->set('status', 'no');
        $this->db->where_in('pin_id', $pin_ids);
        $res = $this->db->update('pin_numbers');
        return $res;
    }

    public function activateEpins($pin_ids = []) {
        $this->db->set('status', 'yes');
        $this->db->where_in('pin_id', $pin_ids);
        $res = $this->db->update('pin_numbers');
        return $res;   
    }

    public function getAllEpinRequests($user_name = "", $limit, $offset) {
        $this->db->select('req_id, req_user_id, req_pin_count, req_rec_pin_count, req_date, pin_expiry_date, pin_amount, ft.user_name, CONCAT(user.user_detail_name, " ", user.user_detail_second_name) AS full_name');
        $this->db->from('pin_request');
        $this->db->join('ft_individual as ft', 'ft.id = pin_request.req_user_id');
        $this->db->join('user_details as user', 'user.user_detail_refid = ft.id');
        $this->db->where('status', "yes");
        if($user_name != "") {
            $this->db->where('ft.user_name', $user_name);
        }
        $this->db->order_by('pin_expiry_date');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getAllEpinRequestsCount($user_name = "") {
        $this->db->select('COUNT(req_id) total');
        $this->db->from('pin_request');
        $this->db->join('ft_individual as ft', 'ft.id = pin_request.req_user_id');
        $this->db->join('user_details as user', 'user.user_detail_refid = ft.id');
        $this->db->where('status', "yes");
        if($user_name != "") {
            $this->db->where('ft.user_name', $user_name);
        }
        $query = $this->db->get();
        return $query->row('total');
    }

   public function deletePinAmountDetails($delete,$id)
    {
       
       if(isset($delete)&& $id!="")
       {
        $this->db->where_in('id', $id);
        $res = $this->db->delete('pin_amount_details');
        return $res;
       } 
       
    }

    // New ui functions
    /**
     * [getEpinRequestAllocatedUserId description]
     * @param  [type] $request_id [description]
     * @return [type]             [description]
     */
    public function getEpinRequestAllocatedUserId($request_id) {
        return $this->db->from('pin_request')
            ->where('req_id', $request_id)
            ->get()
            ->row();
    }

    /**
     * [allocateEpinRequests description]
     * @param  [type] $pin_numbers  [description]
     * @param  [type] $pin_requests [description]
     * @return [type]               [description]
     */
    public function allocateEpinRequests($pin_numbers, $pin_requests) {
        $this->db->trans_begin();
        $this->db->insert_batch('pin_numbers', $pin_numbers);
        $this->db->update_batch('pin_request', $pin_requests, 'req_id');
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        } else {
            $this->db->trans_commit();
            return TRUE;
        }
    }

    /**
     * [activeEpins description]
     * @return [type] [description]
     */
    public function activeEpins($user_id="") {
         $this->db->select('count(pin_id) AS count, sum(pin_balance_amount) AS amount');
            $this->db->from('pin_numbers');
            if($this->LOG_USER_TYPE =='agent'){
                // $this->db->join('user_details as user', 'user.user_detail_refid = pin_numbers.allocated_user_id');
                // $this->db->join('cwallet_history as cw', 'cw.from_id = user.user_detail_refid');
                // $this->db->where('user.agent_id', $this->LOG_USER_ID);
                // $this->db->where('cw.wallet_type', 'agent');
                $this->db->where('generated_agent',$this->LOG_USER_ID);
            }else{
                if($user_id)
                {
                    $this->db->where("allocated_user_id", $user_id);
                }
            }
            $this->db->where("status", "yes");
            $this->db->where('pin_expiry_date >=', date('Y-m-d'));
            $this->db->where('pin_balance_amount >', 0);
            return $this->db->get()->row();
    }

    /**
     * [getAllEpinRequestsCountNew description]
     * @param  array  $user_ids [description]
     * @return [type]           [description]
     */
    public function getAllEpinRequestsCountNew($user_ids = []) {
        $country_ids = $this->validation_model->getAgentCountryID($this->LOG_USER_ID);
        
        $this->db->select('COUNT(req_id) total')
            ->from('pin_request')
            ->join('ft_individual as ft', 'ft.id = pin_request.req_user_id')
            ->join('user_details as user', 'user.user_detail_refid = ft.id')
            ->where('status', "yes");
        if($this->LOG_USER_TYPE =='agent'){
            $this->db->where_in('user.user_detail_country', $country_ids);
            $this->db->where_in('user.agent_id', $this->LOG_USER_ID);
        }
        if(!empty($user_ids)) {
            $this->db->where_in('ft.id', $user_ids);
        }
        return $this->db->get()->row('total');
    }

    public function getAllEpinAmounts() {
        return $this->db->select('id, amount')
            ->from('pin_amount_details')
            ->order_by("amount", "asc")
            ->get()
            ->result_array();
    }

    /**
     * [countEpinList description]
     * @param  [type] $user_ids [description]
     * @param  [type] $epins    [description]
     * @param  [type] $amounts  [description]
     * @param  [type] $status   [description]
     * @return [type]           [description]
     */
    public function countEpinList($user_ids, $epins, $amounts, $status) {

        $country_ids = $this->validation_model->getAgentCountryID($this->LOG_USER_ID);
        // dd($country_id);
       
        // foreach ($country_ids as $key=>$value) {
            $this->db->select('pin_id, pin_numbers, status,purchase_status,pin_expiry_date, pin_amount, pin_balance_amount, ft.user_name, CONCAT(user.user_detail_name, " ", user.user_detail_second_name) AS full_name, user_photo');
            $this->db->from('pin_numbers');
            $this->db->join('ft_individual AS ft', 'ft.id = pin_numbers.allocated_user_id', 'left');
            $this->db->join('user_details AS user', 'user.user_detail_refid = ft.id');
            if(!empty($user_ids)) {
                $this->db->where_in('ft.id', $user_ids);
            }
            if($this->LOG_USER_TYPE =='agent'){
                // $this->db->where_in('user.user_detail_country', $country_ids);
                // $this->db->where_in('user.agent_id', $this->LOG_USER_ID);
                $this->db->where_in('pin_numbers.generated_agent', $this->LOG_USER_ID);
            }
            if(!empty($epins)) {
                $this->db->where_in('pin_numbers', $epins);
            }
            if(!empty($amounts)) {
                $this->db->where_in('pin_amount', $amounts);
            }
            switch($status) {
                case "active":
                    $this->db->where("status", "yes");
                    $this->db->where('pin_expiry_date >=', date('Y-m-d'));
                    $this->db->where('pin_balance_amount >', 0);
                break;
                case "blocked":
                    $this->db->where("status", "no");
                    $this->db->where('pin_balance_amount >', 0);
                break;
                case "used_expired":
                    $this->db->where('status !=', 'delete');
                    $this->db->group_start();   
                        $this->db->where('pin_balance_amount <=', 0);
                        //$this->db->or_where('pin_expiry_date <', date('Y-m-d'));
                    $this->db->group_end();
                break;
                 case "expired":
                    $this->db->where("status", "yes");
                    $this->db->where('pin_expiry_date <', date('Y-m-d'));
                    $this->db->where('pin_balance_amount >', 0);
                break;
                case "deleted":
                    $this->db->where("status", "delete");
                break;
            }
        // }
        return $this->db->count_all_results();
    }

    /**
     * [epinList description]
     * @param  [type] $filter   [description]
     * @param  [type] $user_ids [description]
     * @param  [type] $epins    [description]
     * @param  [type] $amounts  [description]
     * @param  [type] $status   [description]
     * @return [type]           [description]
     */
    public function epinList($filter,$user_ids, $epins, $amounts, $status) {

        $country_ids = $this->validation_model->getAgentCountryID($this->LOG_USER_ID);
        $this->db->select('pin_id, pin_numbers, status,purchase_status,pin_expiry_date, pin_amount, pin_balance_amount, ft.user_name, CONCAT(user.user_detail_name, " ", user.user_detail_second_name) AS full_name, user_photo');
        $this->db->join('ft_individual AS ft', 'ft.id = pin_numbers.allocated_user_id', 'left');
        $this->db->join('user_details AS user', 'user.user_detail_refid = ft.id', 'left');
        if(!empty($user_ids)) {
            $this->db->where_in('ft.id', $user_ids);
        }
        if(!empty($epins)) {
            $this->db->where_in('pin_numbers', $epins);
        }
        if(!empty($amounts)) {
            $this->db->where_in('pin_amount', $amounts);
        }

        if($this->LOG_USER_TYPE =='agent'){
            // $this->db->where_in('user.user_detail_country', $country_ids);
            // $this->db->where_in('user.agent_id', $this->LOG_USER_ID);
            $this->db->where_in('pin_numbers.generated_agent', $this->LOG_USER_ID);
        }
        //$this->db->where("status !=", "delete");
        switch($status) {
            case "active":
                $this->db->where("status", "yes");
                $this->db->where('pin_expiry_date >=', date('Y-m-d'));
                $this->db->where('pin_balance_amount >', 0);
            break;
            case "blocked":
                $this->db->where("status", "no");
                $this->db->where('pin_balance_amount >', 0);
            break;
            case "used_expired":
                $this->db->where('status !=', 'delete');
                $this->db->group_start();   
                    $this->db->where('pin_balance_amount <=', 0);
                    //$this->db->or_where('pin_expiry_date <', date('Y-m-d'));
                $this->db->group_end();
            break;
            case "expired":
                $this->db->where("status", "yes");
                $this->db->where('pin_expiry_date <', date('Y-m-d'));
                $this->db->where('pin_balance_amount >', 0);
            break;
            case "deleted":
                $this->db->where("status", "delete");
            break;
        }
        $this->db->order_by($filter['order'], $filter['direction']);
        $this->db->limit($filter['limit'], $filter['start']);
        $query = $this->db->get('pin_numbers');
        return $query->result_array();
    }

    /**
     * [getAllEpinRequests description]
     * @param  [type] $filter   [description]
     * @param  array  $user_ids [description]
     * @return [type]           [description]
     */
    public function getAllEpinRequestsNew($filter, $user_ids = []) {

        $country_ids = $this->validation_model->getAgentCountryID($this->LOG_USER_ID);

        $this->db->select('req_id, req_user_id, req_pin_count, req_rec_pin_count, req_date, pin_expiry_date, pin_amount, ft.user_name, CONCAT(user.user_detail_name, " ", user.user_detail_second_name) AS full_name, user.user_photo')
            ->from('pin_request')
            ->join('ft_individual as ft', 'ft.id = pin_request.req_user_id')
            ->join('user_details as user', 'user.user_detail_refid = ft.id')
            ->where('status', "yes");
            
        if($this->LOG_USER_TYPE =='agent'){
            $this->db->where_in('user.user_detail_country', $country_ids);
            $this->db->where_in('user.agent_id', $this->LOG_USER_ID);
        }
        if(!empty($user_ids)) {
            $this->db->where_in('ft.id', $user_ids);
        }
        $this->db->order_by($filter['order'], $filter['direction']);
        $this->db->limit($filter['limit'], $filter['start']);
        return $this->db->get()->result_array();
    }

    /**
     * [getEpinsByKeywordNew description]
     * @param  [type] $keyword [description]
     * @return [type]          [description]
     */
    public function getEpinsByKeywordNew($keyword,$user_id="") {
        $this->db->select('pin_numbers');
            if($user_id!="")
            {
               $this->db->where('allocated_user_id',$user_id); 
            }
            $this->db->like('pin_numbers', $keyword, 'after');
            $this->db->order_by('pin_id');
            $this->db->limit(500);
            
        $query = $this->db->get('pin_numbers');
        $response = [];
        foreach ($query->result_array() as $row) {
            $response[] = [
                'id' => $row['pin_numbers'],
                'text' => $row['pin_numbers']
            ];
        }
        return $response;
    }

    /**
     * [generateEpin description]
     * @param  [type] $user_name   [description]
     * @param  [type] $amount      [description]
     * @param  [type] $count       [description]
     * @param  [type] $expiry_date [description]
     * @return [type]              [description]
     */
    public function generateEpinNew($user_name, $amount, $count, $expiry_date) {
        $user_id = ($user_name) ? $this->userNameToId($user_name): NULL;
        $user = $this->session->userdata("inf_logged_in");
        $user_type = $user["user_type"];
        $gen_user_id = $user["user_id"];
        if ($user_type == 'employee') {
            $this->load->model('validation_model');
            $gen_user_id = $this->validation_model->getAdminId();
        }
        $status = "yes";
        $uploded_date = date('Y-m-d h:m:s');
        $pin_alloc_date = date('Y-m-d h:m:s');
        if ($count) {
            for ($i = 0; $i < $count; $i++) {
                $passcode = $this->misc_model->getRandStr(9, 9);
                $res = $this->insertPasscode($passcode, $status, $uploded_date, $gen_user_id, $user_id, $amount, $expiry_date, $pin_alloc_date,$user_type);
            }
            return $res;
        }
    }

    /**
     * [getEpinList description]
     * @param  [type] $user_id [description]
     * @return [type]          [description]
     */
    public function getEpinListNew($user_id) {
        return $this->db->select("pin_id,pin_numbers")
            ->from("pin_numbers")
            ->where("status", "yes")
            ->where("allocated_user_id", $user_id)
            ->where('pin_expiry_date >=', date("Y-m-d"))
            ->where('pin_balance_amount >', 0)
            ->where('pin_balance_amount = pin_amount')
            ->order_by("pin_id", "DESC")
            ->get()
            ->result();
    }

    /**
     * [epinAllocation description]
     * @param  [type] $user_id [description]
     * @param  [type] $epin_id [description]
     * @return [type]          [description]
     */
    public function epinAllocationNew($user_id, $epin_id) {
        $this->db->set('allocated_user_id', $user_id);
        $this->db->where('pin_id', $epin_id);
        return $this->db->update('pin_numbers');
    }

    /**
     * [epinRequests description]
     * @return [type] [description]
     */
    public function epinRequests() {
        return $this->db->select('req_pin_count AS count, pin_amount')
            ->from('pin_request')
            ->where('status', 'yes')
            ->get()
            ->row();
    }

    /**
     * [getBalanceAmount description]
     * @param  [type] $user_id [description]
     * @return [type]          [description]
     */
    public function getBalanceAmount($user_id) {
        return $this->db->select('balance_amount')
            ->from('user_balance_amount')
            ->where('user_id', $user_id)
            ->get()
            ->row('balance_amount');
    }

    /**
     * [getEpinAmount description]
     * @param  [type] $amount_id [description]
     * @return [type]            [description]
     */
    public function getEpinAmount($amount_id) {
        return $this->db->select('amount')
            ->from('pin_amount_details')
            ->where('id', $amount_id)
            ->get()
            ->row('amount');
    }

    /**
     * [getMaxPinCount description]
     * @return [type] [description]
     */
    public function getMaxPinCount() {
        return $this->db->select('pin_maxcount')
            ->get('pin_config')
            ->row('pin_maxcount');
    }

    /**
     * [getAllActivePinspage description]
     * @param  string $purchase_status [description]
     * @return [type]                  [description]
     */
    public function getAllActivePinspage($purchase_status = '') {
        $date = date("Y-m-d");
        $this->db->select("*");
        $this->db->from("pin_numbers");
        $this->db->where('status', 'yes');
        $this->db->where('pin_expiry_date >=', $date);
        $this->db->where('pin_balance_amount >', 0);
        if ($purchase_status != '') {
            $this->db->where('purchase_status', 'yes');
        }
        return  $this->db->count_all_results();
    }

    /**
     * [getUniqueTransactionId description]
     * @return [type] [description]
     */
    public function getUniqueTransactionId() {
        $code = $this->misc_model->getRandStr(9, 9);
        $this->db->set('transaction_id', $code);
        $this->db->insert('transaction_id');
        return $code;
    }

    /**
     * [generatePasscode description]
     * @param  [type] $cnt             [description]
     * @param  [type] $status          [description]
     * @param  [type] $uploded_date    [description]
     * @param  [type] $amount          [description]
     * @param  [type] $expiry_date     [description]
     * @param  [type] $purchase_status [description]
     * @param  [type] $amount_id       [description]
     * @param  string $user_id         [description]
     * @param  string $gen_user_id     [description]
     * @param  string $transaction_id  [description]
     * @return [type]                  [description]
     */
    public function generatePasscode($cnt, $status, $uploded_date, $amount, $expiry_date, $purchase_status, $amount_id, $user_id = '', $gen_user_id = '', $transaction_id = '') {
        $res = false;
        for ($i = 0; $i < $cnt; $i++) {
            $passcode = $this->misc_model->getRandStr(9, 9);
            if ($user_id == '') {
                $allocated_user = 'NA';
            } else {
                $allocated_user = $user_id;
            }
            $res = $this->insertPurchases($passcode, $status, $uploded_date, $gen_user_id, $allocated_user, $amount, $expiry_date, $purchase_status, $amount_id, $transaction_id);
        }
        return $res;
    }
    
    /**
     * [insertPurchases description]
     * @param  [type] $passcode         [description]
     * @param  [type] $status           [description]
     * @param  [type] $pin_uploded_date [description]
     * @param  [type] $generated_user   [description]
     * @param  [type] $allocate_id      [description]
     * @param  [type] $pin_amount       [description]
     * @param  [type] $expiry_date      [description]
     * @param  [type] $purchase_status  [description]
     * @param  [type] $amount_id        [description]
     * @param  [type] $transaction_id   [description]
     * @return [type]                   [description]
     */
    public function insertPurchases($passcode, $status, $pin_uploded_date, $generated_user, $allocate_id, $pin_amount, $expiry_date, $purchase_status, $amount_id, $transaction_id) {

        $pin_alloc_date = $pin_uploded_date;
        $used_user = "";

        $array = array(
            'pin_numbers' => $passcode,
            'pin_alloc_date' => $pin_alloc_date,
            'status' => $status,
            'pin_uploded_date' => $pin_uploded_date,
            'generated_user_id' => $generated_user,
            'allocated_user_id' => $allocate_id,
            'pin_expiry_date' => $expiry_date,
            'pin_amount' => $pin_amount,
            'pin_balance_amount' => $pin_amount,
            'purchase_status' => $purchase_status,
            'transaction_id' => $transaction_id
            );

        $this->db->set($array);
        $res = $this->db->insert('pin_purchases');


        $this->db->set($array);
        $res = $this->db->insert('pin_numbers');

        $ewallet_id = $this->db->insert_id();
        $this->validation_model->addEwalletHistory($allocate_id, $generated_user, $ewallet_id, 'pin_purchase', $pin_amount, 'pin_purchase', 'debit', $transaction_id);

        return $res;
    }

    public function getEpinTransferListCount($user_id = "", $from_date, $to_date) {
        if ($from_date) {
            $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
        }
        if ($to_date) {
            $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
        }

        $this->db->select('eth.id');
        $this->db->from('epin_transfer_history AS eth');
        if($from_date != '' OR $to_date != '') {
            if ($from_date != '') {
                $this->db->where("eth.date >=", $from_date);
            }
            if ($to_date != '') {
                $this->db->where("eth.date <=", $to_date);
            }
        }
        if($user_id) {
            $this->db->group_start();
            $this->db->where('eth.user_id', $user_id);
            $this->db->or_where('eth.from_user_id', $user_id);
            $this->db->group_end();
        }
        return $this->db->count_all_results();
    }

    public function getEpinTransferList($user_id = "", $filter = [], $from_date, $to_date) {
        if ($from_date) {
            $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
        }
        if ($to_date) {
            $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
        }

        $this->db->select('CONCAT(user.user_detail_name, " ", user.user_detail_second_name) AS member_name, ft.user_name, CONCAT(user2.user_detail_name, " ", user2.user_detail_second_name) AS member_name2, ft2.user_name AS user_name2, eth.date, eth.user_id, eth.from_user_id, pin.pin_numbers AS pin_number, pin.pin_amount');
        $this->db->from('epin_transfer_history AS eth');
        $this->db->join('user_details AS user', 'user.user_detail_refid = eth.user_id', 'INNER');
        $this->db->join('user_details AS user2', 'user2.user_detail_refid = eth.from_user_id', 'INNER');
        $this->db->join('pin_numbers AS pin', 'eth.epin_id = pin.pin_id', 'INNER');
        $this->db->join('ft_individual as ft', 'ft.id = eth.user_id', 'INNER');
        $this->db->join('ft_individual as ft2', 'ft2.id = eth.from_user_id', 'INNER');
        if ($from_date != '') {
            $this->db->where("eth.date >=", $from_date);
        }
        if ($to_date != '') {
            $this->db->where("eth.date <=", $to_date);
        }
        if($user_id) {
            $this->db->group_start();
                $this->db->where('eth.user_id', $user_id);
                $this->db->or_where('eth.from_user_id', $user_id);
            $this->db->group_end();
        }
        $this->db->order_by($filter['order'], $filter['direction']);
        $this->db->limit($filter['limit'], $filter['start']);
        $query = $this->db->get();
        // dd($this->db->last_query());
        return $query->result();
        
        $query = $this->db->get();


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

    /**
     * [getAllEpinRequestsCount description]
     * @param  array  $user_ids [description]
     * @return [type]           [description]
     */
    /*public function getAllEpinRequestsCountNew($user_ids = []) {
        $this->db->select('COUNT(req_id) total')
            ->from('pin_request')
            ->join('ft_individual as ft', 'ft.id = pin_request.req_user_id')
            ->join('user_details as user', 'user.user_detail_refid = ft.id')
            ->where('status', "yes");
        if(!empty($user_ids)) {
            $this->db->where_in('ft.id', $user_ids);
        }
        return $this->db->get()->row('total');
    }*/
    public function getAgentBalanceAmount($user_id) {
        return $this->db->select('balance_amount')
            ->from('user_balance_amount')
            ->where('user_id', $user_id)
            ->get()
            ->row('balance_amount');
    }
    public function extendPinValidity($id,$days){
        $this->db->set('pin_expiry_date',date('Y-m-d',strtotime("+$days days")));
        $this->db->where('pin_id',$id);
        return $this->db->update('pin_numbers');
    }
}
