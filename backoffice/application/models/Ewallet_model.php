<?php

class ewallet_model extends inf_model {

    public function __construct() {
        $this->load->model('validation_model');
        $this->load->model('misc_model');

        $this->load->library('inf_phpmailer', NULL, 'phpmailer');
    }

    public function userNameToID($user_name) {
        $user_id = $this->validation_model->userNameToID($user_name);
        return $user_id;
    }

    public function getAllEwalletAmounts() {
        $i = 0;
        $amount_detail = array();
        $this->db->select('id,amount');
        $this->db->from('pin_amount_details');
        $this->db->order_by("amount", "asc");
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $amount_detail["details$i"]["id"] = $row['id'];
            $amount_detail["details$i"]["amount"] = $row['amount'];
            $i++;
        }
        return $amount_detail;
    }

    public function getBalanceAmount($user_id) {
        $this->db->select('balance_amount');
        $this->db->from('user_balance_amount');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        foreach ($query->result() as $row)
            return $row->balance_amount;
    }

    public function getTransactionFee() {
        $this->db->select('trans_fee');
        $this->db->from('configuration');
        $query = $this->db->get();
        foreach ($query->result() as $row)
            return $row->trans_fee;
    }

    public function getUserPassword($user_id) {
        $this->db->select('tran_password');
        $this->db->from('tran_password');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        foreach ($query->result() as $row)
            return $row->tran_password;
    }

    public function insertBalAmountDetails($from_user_id, $to_user_id, $trans_amount, $amount_type = '', $transaction_concept = '', $trans_fee = '', $transaction_id = '') {
        $date = date('Y-m-d H:i:s');
        if($this->MODULE_STATUS['employee_status']=="yes")
        {
        $res = $this->validation_model->isemployee($from_user_id);
        $from_user_id = ($res)? $this->validation_model->getAdminId() : $from_user_id;
         }else{
            $res=0;
         }
        
        if ($amount_type != '') {
            $data = array(
                'from_user_id' => $from_user_id,
                'to_user_id' => $to_user_id,
                'amount' => $trans_amount,
                'date' => $date,
                'amount_type' => $amount_type,
                'transaction_concept' => $transaction_concept,
                'trans_fee' => $trans_fee,
                'transaction_id' => $transaction_id
                );
            $query = $this->db->insert('fund_transfer_details', $data);
            $ewallet_id = $this->db->insert_id();
            $this->validation_model->addEwalletHistory($to_user_id, $from_user_id, $ewallet_id, 'fund_transfer', $trans_amount, $amount_type, ($amount_type == 'admin_debit') ? 'debit' : 'credit', $transaction_id, $transaction_concept, $trans_fee);
        } else {
            $data = array(
                'from_user_id' => $from_user_id,
                'to_user_id' => $to_user_id,
                'amount' => $trans_amount,
                'date' => $date,
                'amount_type' => 'user_credit',
                'transaction_concept' => $transaction_concept,
                'trans_fee' => $trans_fee,
                'transaction_id' => $transaction_id
                );
            $query = $this->db->insert('fund_transfer_details', $data);
            $ewallet_id = $this->db->insert_id();
            $this->validation_model->addEwalletHistory($to_user_id, $from_user_id, $ewallet_id, 'fund_transfer', $trans_amount, 'user_credit', 'credit', $transaction_id, $transaction_concept, $trans_fee);
            $data = array(
                'from_user_id' => $to_user_id,
                'to_user_id' => $from_user_id,
                'amount' => $trans_amount,
                'date' => $date,
                'amount_type' => 'user_debit',
                'transaction_concept' => $transaction_concept,
                'trans_fee' => $trans_fee,
                'transaction_id' => $transaction_id
                );
            $query = $this->db->insert('fund_transfer_details', $data);
            $ewallet_id = $this->db->insert_id();
            $this->validation_model->addEwalletHistory($from_user_id, $to_user_id, $ewallet_id, 'fund_transfer', $trans_amount, 'user_debit', 'debit', $transaction_id, $transaction_concept, $trans_fee);
        }
    }

    public function updateBalanceAmountDetailsFrom($from_user_id, $trans_amount) {
        $this->db->set('balance_amount', 'ROUND(balance_amount - ' . $trans_amount . ',8)', FALSE);
        $this->db->where('user_id', $from_user_id);
        $query = $this->db->update('user_balance_amount');
        return $query;
    }

    public function updateBalanceAmountDetailsTo($to_user_id, $trans_amount) {

        $this->db->set('balance_amount', 'ROUND(balance_amount + ' . $trans_amount . ',8)', FALSE);
        $this->db->where('user_id', $to_user_id);
        $query = $this->db->update('user_balance_amount');

        return $query;
    }

    public function getEpinAmount($amount_id) {
        $amount = 0;
        $this->db->select('amount');
        $this->db->from('pin_amount_details');
        $this->db->where('id', $amount_id);
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $amount = $row['amount'];
        }
        return $amount;
    }

    public function updateBalanceAmount($user_id, $bal) {
        $bal = round($bal, 8);
        $data = array(
            'balance_amount' => $bal
            );
        $this->db->where('user_id', $user_id);
        $result = $this->db->update('user_balance_amount', $data);
        return $result;
    }

    public function getBalancePin($user_id) {
        if ($this->table_prefix == "") {
            $this->table_prefix = $_SESSION['table_prefix'];
        }
        $pin_numbers = $this->table_prefix . "pin_numbers";

        $dbprefix = $this->db->dbprefix;
        $this->db->set_dbprefix('');
        $this->db->where('allocated_user_id', $user_id);
        $this->db->where('status', 'yes');
        $count = $this->db->count_all_results($pin_numbers);
        $this->db->set_dbprefix($dbprefix);
        $balance = intval($count);
        return $balance;
    }

    public function addUserBalanceAmount($to_userid, $amount) {
        $this->db->set('balance_amount', 'ROUND(balance_amount + ' . $amount . ',8)', FALSE);
        $this->db->where('user_id', $to_userid);
        $query = $this->db->update('user_balance_amount');
        return $query;
    }

    public function deductUserBalanceAmount($to_userid, $amount) {
        $this->db->set('balance_amount', 'ROUND(balance_amount - ' . $amount . ',8)', FALSE);
        $this->db->where('user_id', $to_userid);
        $query = $this->db->update('user_balance_amount');
        return $query;
    }

    public function getUserEwalletDetails($user_id, $from_date, $to_date, $type, $page = '', $limit = '', $order_column = "date", $dir = "DESC") {
         if ($from_date) {
            $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
        }
        if ($to_date) {
            $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
        }
        $details = array();
        $this->db->select('from_user_id');
        $this->db->select('to_user_id');
        $this->db->select('from_user.user_name AS from_user_name');
        $this->db->select('to_user.user_name AS to_user_name');
        $this->db->select('amount');
        $this->db->select('trans_fee');
        $this->db->select('date');
        $this->db->select('transaction_id');
        $this->db->select('amount_type');
        $this->db->select('transaction_concept');
        $this->db->select('to_user_id');
        $this->db->from('fund_transfer_details');
        $this->db->join('ft_individual as from_user', 'from_user.id = fund_transfer_details.from_user_id');
        $this->db->join('ft_individual as to_user', 'to_user.id = fund_transfer_details.to_user_id');
        $this->db->limit($limit, $page);
        if ($user_id != '') {
            // $this->db->group_start();
            $this->db->where('to_user_id', $user_id);
            // $this->db->or_where('from_user_id', $user_id);
            // $this->db->group_end();
        }
        if ($from_date) {
            // $this->db->where("DATE_FORMAT(date,'%Y-%m-%d') >=", $from_date);
            $this->db->where("date >=", $from_date);
        }
        
        if ($to_date) {
            // $this->db->where("DATE_FORMAT(date,'%Y-%m-%d') <=", $to_date);
            $this->db->where("date <=", $to_date);
        }
        if (!empty($type)) {
            $this->db->where_in('amount_type', $type);
        }

        /*if (!empty($type) && count($type)==1 ) {
             foreach($type as $amount_type) {
            if($amount_type == 'debit') {
                $this->db->where_in('amount_type',"user_debit");
            }
            if($amount_type == "credit") {
               $this->db->where_in('amount_type', "user_credit");

            }
            }
        }*/
        $this->db->where_not_in('amount_type', ['admin_debit', 'admin_credit']);
        $this->db->order_by($order_column, $dir);
        // $this->db->group_by('transaction_id');
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $details[$i]['total_amount'] = $row['amount'];
            $details[$i]['date'] = $row['date'];
            $details[$i]['amount_type'] = $row['amount_type'];
            $details[$i]['trans_fee'] = $row['trans_fee'];
            $details[$i]['transaction_id'] = $row['transaction_id'];
            $details[$i]['transaction_note'] = $row['transaction_concept'];
            $details[$i]['user_name'] = $this->validation_model->IdToUserName($row['to_user_id']);
            $details[$i]['from_user_name'] = $row['from_user_name'];
            $details[$i]['to_user_name'] = $row['to_user_name'];
            $i++;
        }
        return $details;
    }

    public function isUserNameAvailable($user_name) {
        $res = $this->validation_model->isUserNameAvailable($user_name);
        return $res;
    }

    public function getAdminEmailId() {
        $this->db->select('id');
        $this->db->from('ft_individual');
        $this->db->where('user_type', 'admin');
        $res1 = $this->db->get();
        foreach ($res1->result() as $row1) {
            $user_id = $row1->id;
        }
        $this->db->select('user_detail_email');
        $this->db->from('user_details');
        $this->db->where('user_detail_refid', $user_id);
        $res2 = $this->db->get();
        foreach ($res2->result() as $row2) {
            return $row2->user_detail_email;
        }
    }

    public function getTransactionPasscode($user_id) {
        //$tran_passcodes = $this->table_prefix . 'tran_password';
        $this->db->select('tran_password');
        $this->db->from('tran_password');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $passcode = $row->tran_password;
        }
        return $passcode;
    }

    public function getGrandTotalEwallet($user_id = '') {

        $grand_total = 0;
        if ($user_id == "") {
            $this->db->select_sum('balance_amount');
            $this->db->from('user_balance_amount');
            $query = $this->db->get();
            foreach ($query->result() as $row) {
                $grand_total = $row->balance_amount;
            }
        } else {
            $this->db->select('balance_amount');
            $this->db->from('user_balance_amount');
            $this->db->where("user_id", $user_id);
            $query = $this->db->get();
            foreach ($query->result() as $row) {
                $grand_total = $row->balance_amount;
            }
        }
        return $grand_total;
    }

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

    public function getMaxPinCount() {

        $OBJ_PIN = new pin_model();
        $maxpincount = $OBJ_PIN->getMaxPinCount();
        return $maxpincount;
    }

    public function getAllActivePinspage($purchase_status = '') {

        $OBJ_PIN = new pin_model();
        $num = $OBJ_PIN->getAllActivePinspage($purchase_status);
        return $num;
    }

    public function checkUser($user_name) {
        $flag = false;
        $user_name = ($user_name);
        $user_id = $this->validation_model->userNameToID($user_name);
        if ($user_id) {
            $flag = 1;
        }
        return $flag;
    }

    public function getTotalRequestAmount($user_id = "") {
        $req_amount = 0;
        $this->db->select_sum('requested_amount');
        $this->db->where('status', 'pending');
        if ($user_id != "")
            $this->db->where('requested_user_id', $user_id);
        $query = $this->db->get('payout_release_requests');
        foreach ($query->result() as $row) {
            $req_amount = $row->requested_amount;
        }
        return $req_amount;
    }

    public function getTotalReleasedAmount($user_id = "") {
        $released_amount = 0;
        $this->db->select_sum('paid_amount');
        $this->db->where('paid_type', 'released');
        if ($user_id != "")
            $this->db->where('paid_user_id', $user_id);
        $query = $this->db->get('amount_paid');
        foreach ($query->result() as $row) {
            $released_amount = $row->paid_amount;
        }
        return $released_amount;
    }

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

    public function insertReleasedDetails($to_userid, $amount, $user_level, $transaction_id = '') {
        $date = date("Y/m/d");
        $paid_type = "admin_debit";
        $data = array(
            'paid_user_id' => $to_userid,
            'paid_amount' => $amount,
            'paid_date' => $date,
            'paid_type' => $paid_type,
            'transaction_id' => $transaction_id
            );
        $query = $this->db->insert('amount_paid', $data);
    }

    public function getUserLevel($to_userid) {
        $this->db->select('user_level');
        $this->db->from('ft_individual');
        $this->db->where('id', $to_userid);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $level = $row->user_level;
        }
        return $level;
    }

    public function getBusinessWalletDetails($from_date = '', $to_date = '')
    {
        if ($from_date) {
            $from_date .= ' 00:00:00';
        }
        if ($to_date) {
            $to_date .= ' 23:59:59';
        }

        $wallet_details = [];
        $this->db->select_sum('total_amount');
        $this->db->select_sum('reg_amount');
        $this->db->select_sum('product_amount');
        if ($from_date) {
            $this->db->where("reg_date >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("reg_date <=", $to_date);
        }
        $query = $this->db->get('infinite_user_registration_details');
     
        $registration_total_amount = $query->row_array()['total_amount'] ?? 0;
        $joining_fee = $query->row_array()['reg_amount'] ?? 0;
        $package_amount = $query->row_array()['product_amount'] ?? 0;
       
        $wallet_details['joining_fee'] = [
            'type' => 'income',
            'amount' => $joining_fee
        ];

        if ($this->MODULE_STATUS['product_status'] == 'yes') {
            $wallet_details['package_amount'] = [
                'type' => 'income',
                'amount' => $package_amount
            ];
        }
        
        if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
            unset($wallet_details['joining_fee']);
            
            $wallet_details['package_amount'] = [
                'type' => 'income',
                'amount' => $registration_total_amount
            ];
        }

        if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
            $this->db->select_sum('total');
            $this->db->where("order_status_id", 5);
            $this->db->where("order_type", 'purchase');
            if ($from_date) {
                $this->db->where("date_added >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date_added <=", $to_date);
            }
            $query = $this->db->get('oc_order');
            $purchase_amount = ($query->row_array()['total'] ?? 0) - $registration_total_amount;

            $wallet_details['repurchase'] = [
                'type' => 'income',
                'amount' => $purchase_amount
            ];
        } else {
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes') {
                $this->db->select_sum('total_amount');
                $this->db->where('order_status', 'confirmed');
                if ($from_date) {
                    $this->db->where("order_date >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("order_date <=", $to_date);
                }
                $query = $this->db->get('repurchase_order');
                $purchase_amount = $query->row_array()['total_amount'] ?? 0;

                $wallet_details['repurchase'] = [
                    'type' => 'income',
                    'amount' => $purchase_amount
                ];
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $this->db->select_sum("amount");
                if ($from_date) {
                    $this->db->where("date_added >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("date_added <=", $to_date);
                }
                $query = $this->db->get('upgrade_sales_order');
                $purchase_amount = $query->row_array()['amount'] ?? 0;

                $wallet_details['upgrade'] = [
                    'type' => 'income',
                    'amount' => $purchase_amount
                ];
            }
            if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                $this->db->select_sum('total_amount');
                if ($from_date) {
                    $this->db->where("date_submitted >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("date_submitted <=", $to_date);
                }
                $query = $this->db->get('package_validity_extend_history');
                $membership_reactivation_amount = $query->row_array()['total_amount'] ?? 0;

                $wallet_details['membership_reactivation'] = [
                    'type' => 'income',
                    'amount' => $membership_reactivation_amount
                ];
            }
        }

        $this->db->select_sum('trans_fee');
        $this->db->where('amount_type', 'user_credit');
        if ($from_date) {
            $this->db->where("date >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date <=", $to_date);
        }
        $query = $this->db->get('fund_transfer_details');
        $fund_transfer_fee = $query->row_array()['trans_fee'] ?? 0;
        $wallet_details['fund_transfer_fee'] = [
            'type' => 'income',
            'amount' => $fund_transfer_fee
        ];
        
        $enabled_bonus_list = $this->getEnabledBonusList();

        $this->db->select("SUM(tds + service_charge) total");
        $this->db->where_in('amount_type', $enabled_bonus_list);
        if ($from_date) {
            $this->db->where("date_of_submission >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_of_submission <=", $to_date);
        }
        $query = $this->db->get('leg_amount');
        $commission_charge = $query->row_array()['total'] ?? 0;
        $wallet_details['commission_charge'] = [
            'type' => 'income',
            'amount' => $commission_charge
        ];

        // payout fee
        $wherePayoutFee = ["payout_fee >" => 0];
        if($from_date)
            $wherePayoutFee["paid_date >"] = $from_date;
        if($to_date)
            $wherePayoutFee["paid_date <"] = $to_date;
        $arr = $this->db->select_sum("payout_fee")
                ->from("amount_paid")
                ->where($wherePayoutFee)
                ->get()->result_array();
        $payoutFee = 0;
        if($arr && count($arr)) {
            $payoutFee = $arr[0]["payout_fee"] * 1;
        }
        $wallet_details['payout_fee'] = [
            'type' => 'income',
            'amount' => $payoutFee
        ];
        // payout fee end

        foreach ($enabled_bonus_list as $bonus) {
            $wallet_details[$bonus] = [
                'type' => 'commission',
                'amount' => 0
            ];
        }
        
        $this->db->select('amount_type,SUM(amount_payable) total');
        $this->db->group_by('amount_type');
        $this->db->where_in('amount_type', $enabled_bonus_list);
        if ($from_date) {
            $this->db->where("date_of_submission >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_of_submission <=", $to_date);
        }
        $query = $this->db->get('leg_amount');
        foreach ($query->result_array() as $row) {
            $wallet_details[$row['amount_type']] = [
                'type' => 'commission',
                'amount' => $row['total'] ?? 0
            ];
        }
        
        foreach ($wallet_details as $k => $v) {
            if (in_array($k, ['purchase_donation'])) {
                $wallet_details['donation']['amount'] += $wallet_details[$k]['amount'];
                unset($wallet_details[$k]);
            }
            if (in_array($k, ['repurchase_level_commission', 'upgrade_level_commission'])) {
                $wallet_details['level_commission']['amount'] += $wallet_details[$k]['amount'];
                unset($wallet_details[$k]);
            }
            if (in_array($k, ['xup_repurchase_level_commission', 'xup_upgrade_level_commission'])) {
                $wallet_details['xup_commission']['amount'] += $wallet_details[$k]['amount'];
                unset($wallet_details[$k]);
            }
            if (in_array($k, ['repurchase_leg', 'upgrade_leg'])) {
                $wallet_details['leg']['amount'] += $wallet_details[$k]['amount'];
                unset($wallet_details[$k]);
            }
            if (in_array($k, ['matching_bonus_purchase', 'matching_bonus_upgrade'])) {
                $wallet_details['matching_bonus']['amount'] += $wallet_details[$k]['amount'];
                unset($wallet_details[$k]);
            }
        }

        $this->db->select_sum('paid_amount');
        $this->db->where('paid_type', 'released');
        $this->db->where('paid_status', 'yes');
        if ($from_date) {
            $this->db->where("paid_date >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("paid_date <=", $to_date);
        }
        $query = $this->db->get('amount_paid');
        $payout_approved_paid = $query->row_array()['paid_amount'] ?? 0;
        $wallet_details['payout_approved_paid'] = [
            'type' => 'payout',
            'amount' => $payout_approved_paid
        ];

        $this->db->select_sum('paid_amount');
        $this->db->where('paid_type', 'released');
        $this->db->where('paid_status !=', 'yes');
        $this->db->where('payment_method','bank');
        if ($from_date) {
            $this->db->where("paid_date >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("paid_date <=", $to_date);
        }
        $query = $this->db->get('amount_paid');
        $payout_approved_pending = $query->row_array()['paid_amount'] ?? 0;
        
        $this->db->select_sum('requested_amount_balance');
        $this->db->where('status', 'pending');
        if ($from_date) {
            $this->db->where("requested_date >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("requested_date <=", $to_date);
        }
        $query = $this->db->get('payout_release_requests');
        $payout_requests_pending = $query->row_array()['requested_amount_balance'] ?? 0;
        $wallet_details['payout_pending_amount'] = [
            'type' => 'payout_pending',
            'amount' => $payout_requests_pending + $payout_approved_pending
        ];

        return $wallet_details;
    }

    public function getBusinessWalletTotal()
    {
        $wallet_total = [
            'income' => 0,
            'commission' => 0,
            'payout' => 0,
            'payout_pending' => 0
        ];
        $this->db->select_sum('total_amount');
        $this->db->select_sum('reg_amount');
        $this->db->select_sum('product_amount');
        $query = $this->db->get('infinite_user_registration_details');
     
        $registration_total_amount = $query->row_array()['total_amount'] ?? 0;
        $joining_fee = $query->row_array()['reg_amount'] ?? 0;
        $package_amount = $query->row_array()['product_amount'] ?? 0;
        
        if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
            $wallet_total['income'] += $registration_total_amount;

            $this->db->select_sum('total');
            $this->db->where('order_status_id >', 0);
            $query = $this->db->get('oc_order');
            $purchase_amount = ($query->row_array()['total'] ?? 0) - $registration_total_amount;

            $wallet_total['income'] += $purchase_amount;
        } else {
            $wallet_total['income'] += $joining_fee;
            if ($this->MODULE_STATUS['product_status'] == 'yes') {
                $wallet_total['income'] += $package_amount;
            }

            if ($this->MODULE_STATUS['repurchase_status'] == 'yes') {
                $this->db->select_sum('total_amount');
                $this->db->where('order_status', 'confirmed');
                $query = $this->db->get('repurchase_order');
                $purchase_amount = $query->row_array()['total_amount'] ?? 0;

                $wallet_total['income'] += $purchase_amount;
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $this->db->select_sum('amount');
                $query = $this->db->get('upgrade_sales_order');
                $purchase_amount = $query->row_array()['amount'] ?? 0;

                $wallet_total['income'] += $purchase_amount;
            }
            if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                $this->db->select_sum('total_amount');
                $query = $this->db->get('package_validity_extend_history');
                $membership_reactivation_amount = $query->row_array()['total_amount'] ?? 0;

                $wallet_total['income'] += $membership_reactivation_amount;
            }
        }

        $this->db->select_sum('trans_fee');
        $this->db->where('amount_type', 'user_credit');
        $query = $this->db->get('fund_transfer_details');
        $fund_transfer_fee = $query->row_array()['trans_fee'] ?? 0;
        $wallet_total['income'] += $fund_transfer_fee;
        
        $enabled_bonus_list = $this->getEnabledBonusList();

        $this->db->select("SUM(tds + service_charge) total");
        $this->db->where_in('amount_type', $enabled_bonus_list);
        $query = $this->db->get('leg_amount');
        $commission_charge = $query->row_array()['total'] ?? 0;
        $wallet_total['income'] += $commission_charge;

        $this->db->select('SUM(amount_payable) total');
        $this->db->where_in('amount_type', $enabled_bonus_list);
        $query = $this->db->get('leg_amount');
        $commission_amount = $query->row_array()['total'] ?? 0;
        $wallet_total['commission'] += $commission_amount;
        
        $this->db->select_sum('paid_amount');
        $this->db->where('paid_type', 'released');
        $this->db->where('paid_status', 'yes');
        $query = $this->db->get('amount_paid');
        $payout_approved_paid = $query->row_array()['paid_amount'] ?? 0;
        $wallet_total['payout'] += $payout_approved_paid;

        $this->db->select_sum('paid_amount');
        $this->db->where('paid_type', 'released');
        $this->db->where('paid_status !=', 'yes');
        $this->db->where('payment_method','bank');
        $query = $this->db->get('amount_paid');
        $payout_approved_pending = $query->row_array()['paid_amount'] ?? 0;
        
        $this->db->select_sum('requested_amount_balance');
        $this->db->where('status', 'pending');
        $query = $this->db->get('payout_release_requests');
        $payout_requests_pending = $query->row_array()['requested_amount_balance'] ?? 0;
        $wallet_total['payout_pending'] += ($payout_requests_pending + $payout_approved_pending);

        //payout fee
        $arr = $this->db->select_sum("payout_fee")
                ->from("amount_paid")
                ->get()->result_array();
        if($arr && count($arr)) {
            $wallet_total['income'] += $arr[0]["payout_fee"] * 1;
        }
        //payout fee end
        
        return $wallet_total;
    }

    public function getReceivedBonusList()
    {
        $list = [];
        $this->db->select('amount_type');
        $this->db->group_by('amount_type');
        $query = $this->db->get('leg_amount');
        foreach ($query->result_array() as $row) {
            $list[] = $row['amount_type'];
        }
        return $list;
    }

    public function getEnabledBonusList()
    {
        $list = [];
        $level_commission_status = 'no';
        if (in_array($this->MLM_PLAN, ['Matrix', 'Unilevel', 'Donation']) || $this->MODULE_STATUS['sponsor_commission_status'] == 'yes') {
            $level_commission_status = 'yes';
        }
        $xup_commission_status = 'no';
        if ($this->MODULE_STATUS['xup_status'] == 'yes' && $level_commission_status == 'yes') {
            $xup_commission_status = 'yes';
            $level_commission_status = 'no';
        }
        if ($this->MODULE_STATUS['referal_status'] == 'yes') {
            $list[] = 'referral';
        }
        if ($this->MODULE_STATUS['rank_status'] == 'yes') {
            $list[] = 'rank_bonus';
        }
        if ($level_commission_status == 'yes') {
            $list[] = 'level_commission';
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes' || $this->MODULE_STATUS['opencart_status'] == 'yes') {
                $list[] = 'repurchase_level_commission';
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $list[] = 'upgrade_level_commission';
            }
        }
        if ($xup_commission_status == 'yes') {
            $list[] = 'xup_commission';
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes' || $this->MODULE_STATUS['opencart_status'] == 'yes') {
                $list[] = 'xup_repurchase_level_commission';
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $list[] = 'xup_upgrade_level_commission';
            }
        }
        if ($this->MLM_PLAN == 'Binary') {
            $list[] = 'leg';
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes' || $this->MODULE_STATUS['opencart_status'] == 'yes') {
                $list[] = 'repurchase_leg';
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $list[] = 'upgrade_leg';
            }
        }
        if ($this->MLM_PLAN == 'Stair_Step') {
            $list[] = 'stair_step';
            $list[] = 'override_bonus';
        }
        if ($this->MLM_PLAN == 'Board') {
            $list[] = 'board_commission';
        }
        if ($this->MODULE_STATUS['roi_status'] == 'yes' || $this->MODULE_STATUS['hyip_status'] == 'yes') {
            $list[] = 'daily_investment';
        }
        if ($this->MLM_PLAN == 'Donation') {
            $list[] = 'donation';
            $list[] = 'purchase_donation';
        }
        $matching_bonus_status = $this->validation_model->getCompensationConfig('matching_bonus');
        $pool_bonus_status = $this->validation_model->getCompensationConfig('pool_bonus');
        $fast_start_bonus_status = $this->validation_model->getCompensationConfig('fast_start_bonus');
        $performance_bonus_status = $this->validation_model->getCompensationConfig('performance_bonus');
        if ($matching_bonus_status == 'yes') {
            $list[] = 'matching_bonus';
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes' || $this->MODULE_STATUS['opencart_status'] == 'yes') {
                $list[] = 'matching_bonus_purchase';
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $list[] = 'matching_bonus_upgrade';
            }
        }
        if ($pool_bonus_status == 'yes') {
            $list[] = 'pool_bonus';
        }
        if ($fast_start_bonus_status == 'yes') {
            $list[] = 'fast_start_bonus';
        }
        if ($performance_bonus_status == 'yes') {
            $performance_bonus_types = $this->getPerformanceBonusTypes();
            foreach ($performance_bonus_types as $v) {
                $list[] = $v;
            }
        }
        $list[] ='rank_promo_bonus';
        $list[] = 'founder_bonus';
        $list[] = 'company_bonus';

        return $list;
    }

    public function getPerformanceBonusTypes()
    {
        $list = [];
        $this->db->select('bonus_name');
        $query = $this->db->get('performance_bonus');
        foreach ($query->result_array() as $row) {
            $list[] = $row['bonus_name'];
        }
        return $list;
    }
    
    public function getUniqueTransactionId() {
        $code = $this->getRandStr(9, 9);
        $this->db->set('transaction_id', $code);
        $this->db->insert('transaction_id');
        return $code;
    }

    public function getRandStr() {
        $key = "";
        $charset = "0123456789";
        $length = 10;
        for ($i = 0; $i < $length; $i++)
            $key .= $charset[(mt_rand(0, (strlen($charset) - 1)))];

        $randum_number = $key;
        $this->db->from('transaction_id');
        $this->db->where('transaction_id', $randum_number);
        $count = $this->db->count_all_results();
        if ($count > 0)
            $this->getRandStr();
        else
            return $key;
    }

    public function getEwalletHistoryCount($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->where('amount_type !=', 'payout_release');
        return $this->db->count_all_results('ewallet_history');
    }

    public function ewallet_history($user_id, $filter) {
        $this->db->select('e.ewallet_type,e.amount,e.amount_type,e.type,e.date_added,e.transaction_id,e.transaction_note,e.transaction_fee,e.pending_id,IF(e.pending_id IS NULL, f.user_name, p.user_name) as from_user,e.purchase_wallet', false);
        $this->db->from('ewallet_history as e');
        $this->db->join('ft_individual as f', 'e.from_id = f.id', 'left');
        $this->db->join('pending_registration as p', 'e.pending_id = p.id', 'left');
        $this->db->where('e.user_id', $user_id);
        $this->db->where('amount_type !=', 'payout_release');
        $this->db->order_by('e.id');
        $this->db->limit($filter['limit'], $filter['start']);
        // $this->db->limit($limit, $page);
        $res = $this->db->get();
        // dd($this->db->last_query());
        return $res->result_array();
    }

    public function getEwalletHistory($user_id, $page, $limit) {
        $this->db->select('e.ewallet_type,e.amount,e.amount_type,e.type,e.date_added,e.transaction_id,e.transaction_note,e.transaction_fee,e.pending_id,IF(e.pending_id IS NULL, f.user_name, p.user_name) as from_user,e.purchase_wallet', false);
        $this->db->from('ewallet_history as e');
        $this->db->join('ft_individual as f', 'e.from_id = f.id', 'left');
        $this->db->join('pending_registration as p', 'e.pending_id = p.id', 'left');
        $this->db->where('e.user_id', $user_id);
        $this->db->where('amount_type !=', 'payout_release');
        $this->db->limit($limit, $page);
        $this->db->order_by('e.id');
        $res = $this->db->get();
        return $res->result_array();
    }

    public function getUserEwalletStatement($user_id, $filter) {
        $this->db->select('e.ewallet_type,e.amount,e.amount_type,e.type,e.date_added,e.transaction_id,e.transaction_note,e.transaction_fee,e.pending_id,IF(e.pending_id IS NULL, f.user_name, p.user_name) as from_user,e.purchase_wallet,e.agent_id', false);
        $this->db->from('ewallet_history as e');
        $this->db->join('ft_individual as f', 'e.from_id = f.id', 'left');
        $this->db->join('pending_registration as p', 'e.pending_id = p.id', 'left');
        $this->db->where('e.user_id', $user_id);
        $this->db->where('amount_type !=', 'payout_release');
        // $this->db->order_by($filter['order'], $filter['direction']);
        $this->db->order_by('e.id');
        $this->db->limit($filter['limit'], $filter['start']);
        $res = $this->db->get();
        return $res->result_array();
    }

    public function getPreviousEwalletBalance($user_id, $page) {
        if(!$page) {
            return 0;
        }

        $this->db->select('*');
        $this->db->from('ewallet_history as e');
        $this->db->where('e.user_id', $user_id);
        $this->db->limit($page, 0);
        $ewallet_data = $this->db->get_compiled_select();

        $this->db->select("SUM(IF(f.type = 'credit', f.amount, 0)) as credit", FALSE);
        $this->db->select("SUM(IF(f.type = 'credit', f.purchase_wallet, 0)) as pwallet", FALSE);
        $this->db->select("SUM(IF(f.type = 'debit', f.amount, 0)) as debit", FALSE);
        $this->db->select("SUM(IF(f.type = 'debit', f.transaction_fee, 0)) as transaction_fee", FALSE);
        $this->db->from("($ewallet_data) as f", FALSE);
        $this->db->where('f.amount_type !=', 'payout_release');
        $res = $this->db->get();
        return ($res->row_array()['credit'] - $res->row_array()['debit'] - $res->row_array()['transaction_fee'] - $res->row_array()['pwallet']);
    }

    public function checkEwalletPassword($user_id, $password) {
        $flag = 'no';
        $this->db->select('tran_password');
        $this->db->where('user_id', $user_id);
        $this->db->limit(1);
        $query = $this->db->get('tran_password');
        $password_hash = $query->row_array()['tran_password'];
        $password_matched = password_verify($password, $password_hash);
        if ($password_hash && $password_matched) {
            $flag = 'yes';
        }
        return $flag;
    }

    public function ewalletPayment($ewallet_user_id, $user_id, $used_amount, $amount_type) {
        $date = date('Y-m-d H:i:s');
        $transaction_id = $this->getUniqueTransactionId();
        $this->db->set('used_user_id', $ewallet_user_id);
        $this->db->set('used_amount', $used_amount);
        $this->db->set('user_id', $user_id);
        $this->db->set('used_for', $amount_type);
        $this->db->set('date', $date);
        $this->db->set('transaction_id', $transaction_id);
        $res1 = $this->db->insert('ewallet_payment_details');

        $ewallet_id = $this->db->insert_id();
        $res2 = $this->validation_model->addEwalletHistory($ewallet_user_id, $user_id, $ewallet_id, 'ewallet_payment', $used_amount, $amount_type, 'debit', $transaction_id, '', 0);

        $res3 = $this->deductUserBalanceAmount($ewallet_user_id, $used_amount);

        return $res1 && $res2 && $res3;
    }

    public function validateEwalletDetails($ewallet_username, $ewallet_password, $payment_amount, $upgrade_username)
    {
        $status = "";
        $user_id = $this->validation_model->userNameToID($ewallet_username);
        if ($this->LOG_USER_TYPE == 'admin' || $this->LOG_USER_TYPE == 'employee') {
            $admin_username = $this->validation_model->getAdminUsername();
            if ($ewallet_username != $admin_username && $ewallet_username != $upgrade_username) {
                $status = "invalid";
                return $status;
            }
        } else if ($this->LOG_USER_TYPE == 'user') {
            if ($ewallet_username != $this->LOG_USER_NAME) {
                $status = "invalid";
                return $status;
            }
        }
        if ($user_id) {
            $user_password = $this->checkEwalletPassword($user_id, $ewallet_password);
            if ($user_password == 'yes') {
                $user_balance_amount = $this->getBalanceAmount($user_id);
                if ($user_balance_amount > 0 && $user_balance_amount >= $payment_amount) {
                    $status = "yes";
                }
                else {
                    $status = "low_balance";
                }
            }
            else {
                $status = "invalid";
            }
        }
        else {
            $status = "invalid";
        }
        return $status;
    }
    public function getEwalletHistoryForMobile($user_id, $page, $limit) {
        $data = array();
        $this->db->select('e.ewallet_type,e.amount,e.amount_type,e.type,e.date_added,e.transaction_id,e.transaction_note,e.transaction_fee,f.user_name as from_user', false);
        $this->db->from('ewallet_history as e');
        $this->db->join('ft_individual as f', 'e.from_id = f.id', 'left');
        $this->db->where('e.user_id', $user_id);
        $this->db->limit($limit, $page);
        $this->db->order_by('e.id');
        $res = $this->db->get();
        $i = 0;
        if($res->num_rows() > 0){
            foreach ($res->result_array() as $row) {
                $data[$i] = $row;
                if($row['ewallet_type'] == "fund_transfer"){
                    if($row['amount_type'] == "user_credit"){
                        $data[$i]['description'] = lang('transfer_from')." ".$row['from_user']." ".lang('transaction_id')." :".$row['transaction_id'];
                    }else if($row['amount_type'] == "user_debit"){
                       $data[$i]['description'] = lang('fund_transfer_to')." ".$row['from_user']." ".lang('transaction_id')." :".$row['transaction_id'];
                    }else if($row['amount_type'] == "admin_credit"){
                       $data[$i]['description'] = lang('admin_credit')." ".$row['from_user']." ".lang('transaction_id')." :".$row['transaction_id'];
                    }else if($row['amount_type'] == "admin_debit"){
                       $data[$i]['description'] = lang('deducted_by')." ".$row['from_user']." ".lang('transaction_id')." :".$row['transaction_id'];
                    }
                }else if ($row['ewallet_type'] == "commission"){
                    $data[$i]['description'] = lang($row['amount_type'])." from ".$row['from_user'];
                }else if ($row['ewallet_type'] == "ewallet_payment"){
                    if($row['amount_type'] == "registration"){
                        $data[$i]['description'] = lang('deducted_for_registration_of')." ".$row['from_user'];
                    }else if($row['amount_type'] == "repurchase"){
                       $data[$i]['description'] = lang('deducted_for_repurchase_by')." ".$row['from_user'];
                    }else if($row['amount_type'] == "package_validity"){
                       $data[$i]['description'] = lang('deducted_for_membership_renewal_of')." ".$row['from_user'];
                    }
                }else if ($row['ewallet_type'] == "payout"){
                    if($row['amount_type'] == "payout_request"){
                        $data[$i]['description'] = lang('deducted_for_payout_request');
                    }else if($row['amount_type'] == "payout_release"){
                       $data[$i]['description'] = lang('payout_released_for_request');
                    }else if($row['amount_type'] == "payout_delete"){
                       $data[$i]['description'] = lang('credited_for_payout_request_delete');
                    }else if($row['amount_type'] == "payout_release_manual"){
                       $data[$i]['description'] = lang('payout_released_by_manual');
                    }else if($row['amount_type'] == "withdrawal_cancel"){
                       $data[$i]['description'] = lang('credited_for_waiting_withdrawal_cancel');
                    }
                }else if ($row['ewallet_type'] == "pin_purchase"){
                    if($row['amount_type'] == "pin_purchase"){
                        $data[$i]['description'] = lang('deducted_for_pin_purchase');
                    }else if($row['amount_type'] == "pin_purchase_delete"){
                       $data[$i]['description'] = lang('credited_for_pin_purchase_delete');
                }
                }
                $i++;
            }
        }
        return $data;
    }
    public function getTotalCommission($user_id,$start_date,$end_date) {
        //print_r($start_date);die;
        $commission = 0;
        $this->db->select('*');
        $this->db->from('ewallet_history as e');
        if($user_id != "")
        $this->db->where('e.user_id', $user_id);
        if($start_date != '' && $end_date != ''){
            $where = "date_added between '$start_date' and '$end_date'";
            $this->db->where($where);
        }
        $ewallet_data = $this->db->get_compiled_select();

        $this->db->select("SUM(IF(f.type = 'credit' AND f.amount_type != 'donation', f.amount, 0)) as credit", FALSE);
        $this->db->select("SUM(IF(f.type = 'debit' AND f.amount_type != 'payout_release', f.amount, 0)) as debit", FALSE);
        $this->db->select("SUM(IF(f.type = 'debit' AND f.amount_type != 'payout_release', f.transaction_fee, 0)) as transaction_fee", FALSE);
        $this->db->from("($ewallet_data) as f", FALSE);
        $res = $this->db->get();

        return ($res->row_array()['credit'] - $res->row_array()['debit'] - $res->row_array()['transaction_fee']);
    }
    public function getTotalDonation($user_id,$start_date,$end_date) {

        $donation = 0;
        $this->db->select('*');
        $this->db->from('ewallet_history as e');
        if($user_id != "")
        $this->db->where('e.user_id', $user_id);
        if($start_date != "" && $end_date != ""){
            $where = "date_added between '$start_date' and '$end_date'";
            $this->db->where($where);
        }
        $ewallet_data = $this->db->get_compiled_select();

        $this->db->select("SUM(IF(f.type = 'credit' AND f.amount_type = 'donation'AND f.ewallet_type = 'commission', f.amount, 0)) as credit", FALSE);
        $this->db->select("SUM(IF(f.type = 'debit' AND f.amount_type = 'donation'AND f.ewallet_type = 'commission', f.transaction_fee, 0)) as transaction_fee", FALSE);
        $this->db->from("($ewallet_data) as f", FALSE);
        $res = $this->db->get();
        return ($res->row_array()['credit'] - $res->row_array()['transaction_fee']);
    }


    public function getTotalDailyInvestment($user_id='') {
        $amount = 0;
        $this->db->select('SUM(amount) - SUM(purchase_wallet) as total_amount', FALSE);
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
        return $amount;
    }

    public function getAllEwalletDetails($given_username = "", $recieved_username = "", $from_date = "", $to_date = "", $offset = 1, $row_count = 2)  {
        $this->db->select("td.id, td.amount, u.user_name AS from_username, u.delete_status as from_user_delete_status, u2.delete_status as to_user_delete_status, CONCAT(dt.user_detail_name, ' ' ,dt.user_detail_second_name) AS from_user_fullname, u2.user_name AS to_username, CONCAT(dt2.user_detail_name, ' ',dt2.user_detail_second_name) AS to_user_fullname, td.trans_fee, td.date, td.transaction_concept");
        $this->db->from('fund_transfer_details AS td');
        if ($from_date) {
            $this->db->where("DATE_FORMAT(td.date,'%Y-%m-%d') >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("DATE_FORMAT(td.date,'%Y-%m-%d') <=", $to_date);
        }
        if ($given_username) {
            $this->db->where('u.user_name', $given_username);
        }
        if ($recieved_username) {
            $this->db->where('u2.user_name', $recieved_username);
        }
        $this->db->join('ft_individual as u', 'u.id = td.from_user_id', 'left');
        $this->db->join('ft_individual as u2', 'u2.id = td.to_user_id', 'left');
        $this->db->join('user_details AS dt', 'dt.user_detail_refid = u.id', 'left');
        $this->db->join('user_details AS dt2', 'dt2.user_detail_refid = u2.id', 'left');
        $this->db->where_in('amount_type', ['user_credit', 'admin_credit']);
        $this->db->limit($row_count, $offset);
        $this->db->order_by('td.date',"desc");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getCountAllEwalletDetails($given_username = "", $recieved_username = "", $from_date = "", $to_date = "") {
        $this->db->select("count(td.id) AS count");
        $this->db->from('fund_transfer_details AS td');
        if ($from_date) {
            $this->db->where("DATE_FORMAT(td.date,'%Y-%m-%d') >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("DATE_FORMAT(td.date,'%Y-%m-%d') <=", $to_date);
        }
        if ($given_username) {
            $this->db->where('u.user_name', $given_username);
        }
        if ($recieved_username) {
            $this->db->where('u2.user_name', $recieved_username);
        }
        $this->db->join('ft_individual as u', 'u.id = td.from_user_id', 'left');
        $this->db->join('ft_individual as u2', 'u2.id = td.to_user_id', 'left');
        $this->db->join('user_details AS dt', 'dt.user_detail_refid = u.id', 'left');
        $this->db->join('user_details AS dt2', 'dt2.user_detail_refid = u2.id', 'left');
        $this->db->where_in('amount_type', ['user_credit', 'admin_credit']);
        $this->db->order_by('td.date',"desc");
        $query = $this->db->get();
        return $query->row('count');
    }

    public function getAllEwalletDetailsOld($from_user_id, $from_date, $to_date,$recieved_userid = '') {
        $details = array();
        $this->db->select('amount');
        $this->db->select('trans_fee');
        $this->db->select('date');
        $this->db->select('transaction_id');
        $this->db->select('amount_type');
        $this->db->select('transaction_concept');
        $this->db->select('to_user_id');
        $this->db->select('from_user_id');
        $this->db->from('fund_transfer_details');
        if ($from_user_id != '') {
            $this->db->where("CASE WHEN amount_type = 'admin_debit' THEN to_user_id = '$from_user_id' ELSE from_user_id = '$from_user_id' END");
            $this->db->where('amount_type !=','user_debit');
        }
        if ($recieved_userid != '') {
            $this->db->where("CASE WHEN amount_type = 'admin_debit' THEN from_user_id = '$recieved_userid' ELSE to_user_id = '$recieved_userid' END");
        }

        if ($from_date != '') {
            $this->db->where("date >=", $from_date);
            $this->db->where('amount_type !=','user_debit');
        }
        if ($to_date != '') {
            $this->db->where("date <=", $to_date);
            $this->db->where('amount_type !=','user_debit');
        }
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $details[$i]['total_amount'] = $row['amount'];
            $details[$i]['date'] = $row['date'];
            $details[$i]['amount_type'] = $row['amount_type'];
            $details[$i]['trans_fee'] = $row['trans_fee'];
            $details[$i]['transaction_id'] = $row['transaction_id'];
            $details[$i]['transaction_note'] = $row['transaction_concept'];
            if($row['amount_type'] == 'admin_debit'){
                $details[$i]['user_name'] = $this->validation_model->IdToUserName($row['to_user_id']);
                $details[$i]['from_user_name'] = $this->validation_model->IdToUserName($row['from_user_id']);
            }else{
                $details[$i]['user_name'] = $this->validation_model->IdToUserName($row['from_user_id']);
                $details[$i]['from_user_name'] = $this->validation_model->IdToUserName($row['to_user_id']);
            }

            $i++;
        }
        return $details;
    }

//Purchase wallet starts
    public function getPreviousPurchasewalletBalance($user_id, $page) {
        if(!$page) {
            return 0;
        }

        $this->db->select('*');
        $this->db->from('purchase_wallet_history as e');
        $this->db->where('e.user_id', $user_id);
        $this->db->order_by('e.id','desc');
        $this->db->limit($page, 0);
        $ewallet_data = $this->db->get_compiled_select();
        $this->db->select("SUM(IF(f.type = 'credit', f.purchase_wallet, 0)) as credit", FALSE);
        $this->db->select("SUM(IF(f.type = 'debit' AND f.amount_type != 'payout_release', f.purchase_wallet, 0)) as debit", FALSE);
        $this->db->from("($ewallet_data) as f", FALSE);
        $res = $this->db->get();
        
        return ($res->row_array()['credit'] - $res->row_array()['debit']);
    }
    public function getPurchasewalletHistoryCount($user_id) {
        $this->db->where('user_id', $user_id);
        return $this->db->count_all_results('purchase_wallet_history');
    }

    public function getPurchasewalletHistory($user_id, $page, $limit) {
        $this->db->select('e.purchase_wallet as amount,e.amount_type,e.type,e.date date_added,f.user_name as from_user', false);
        $this->db->from('purchase_wallet_history as e');
        $this->db->join('ft_individual as f', 'e.from_user_id = f.id', 'left');
        $this->db->where('e.user_id', $user_id);
        $this->db->limit($limit, $page);
        $this->db->order_by('e.id','desc');
        $res = $this->db->get();
        return $res->result_array();
    }

    public function purchase_wallet_history($user_id, $filter) {
        $this->db->select('e.purchase_wallet as amount,e.amount_type,e.type,e.date date_added,f.user_name as from_user', false);
        $this->db->from('purchase_wallet_history as e');
        $this->db->join('ft_individual as f', 'e.from_user_id = f.id', 'left');
        $this->db->where('e.user_id', $user_id);
        $this->db->order_by('e.id', 'DESC');
        $this->db->limit($filter['limit'], $filter['start']);
        $res = $this->db->get();
        return $res->result_array();
    }

    public function deductFromPurchaseWallet($user_id, $total_amount) {
        $this->db->set('purchase_wallet', 'ROUND(purchase_wallet -' . $total_amount . ',8)', false);
        $this->db->where('user_id', $user_id);
        $this->db->limit(1);
        $res = $this->db->update('user_balance_amount');
        return $res;
    }

    public function addFundToPurchaseWallet($user_id, $amount, $type)
    {
        $this->db->set('purchase_wallet', "purchase_wallet + $amount", FALSE);
        $this->db->where('user_id', $user_id);
        $res = $this->db->update('user_balance_amount');
        if ($res) {
            $this->db->set('user_id', $user_id);
            $this->db->set('amount', 0);
            $this->db->set('purchase_wallet', $amount);
            $this->db->set('amount_type', $type);
            $this->db->set('tds', 0);
            $this->db->set('type', 'credit');
            $res = $this->db->insert('purchase_wallet_history');
        }
        return $res;
    }
    public function getRequestPendingAmount($user_id) {
        $req_amount = 0;
        $this->db->select_sum('requested_amount_balance');
        $this->db->where('requested_user_id', $user_id);
        $this->db->where('status', 'pending');
        $this->db->from('payout_release_requests');
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            if($row->requested_amount_balance != ''){
                $req_amount = $row->requested_amount_balance;
            }else{
                $req_amount = 0;
            }
        }
        return $req_amount;
    }
    
    public function getAllTransactionCount($user_id, $cat_type, $category, $from_date, $to_date)
    {
        if ($from_date) {
            $from_date .= ' 00:00:00';
        }
        if ($to_date) {
            $to_date .= ' 23:59:59';
        }

        $count = 0;
        
        if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
            if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'package_amount'])) {
                if ($user_id) {
                    $this->db->where('user_id', $user_id);
                }
                if ($from_date) {
                    $this->db->where("reg_date >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("reg_date <=", $to_date);
                }
                $count += $this->db->count_all_results('infinite_user_registration_details');
            }

            if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'repurchase'])) {
                $this->db->from('oc_order as e');
                $this->db->join('ft_individual as f', 'e.customer_id = f.oc_customer_ref_id');
                $this->db->where('e.order_type', 'purchase');
                $this->db->where('e.order_status_id >', 0);
                if ($user_id) {
                    $this->db->where('f.id', $user_id);
                }
                if ($from_date) {
                    $this->db->where("e.date_added >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("e.date_added <=", $to_date);
                }
                $count += $this->db->count_all_results();
            }
        } else {
            if ($this->MODULE_STATUS['product_status'] == 'yes') {
                if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'package_amount'])) {
                    if ($user_id) {
                        $this->db->where('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("reg_date >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("reg_date <=", $to_date);
                    }
                    $count += $this->db->count_all_results('infinite_user_registration_details');
                }
                
                if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'joining_fee'])) {
                    if ($user_id) {
                        $this->db->where('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("reg_date >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("reg_date <=", $to_date);
                    }
                    $count += $this->db->count_all_results('infinite_user_registration_details');
                }
            }
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes') {
                if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'repurchase'])) {
                    $this->db->where('order_status', 'confirmed');
                    if ($user_id) {
                        $this->db->where('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("order_date >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("order_date <=", $to_date);
                    }
                    $count += $this->db->count_all_results('repurchase_order');
                }
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'upgrade'])) {
                    if ($user_id) {
                        $this->db->where('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("date_added >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("date_added <=", $to_date);
                    }
                    $count += $this->db->count_all_results('upgrade_sales_order');
                }
            }
            if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'membership_reactivation'])) {
                    if ($user_id) {
                        $this->db->where('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("date_submitted >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("date_submitted <=", $to_date);
                    }
                    $count += $this->db->count_all_results('package_validity_extend_history');
                }
            }
        }

        if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'fund_transfer_fee'])) {
            $this->db->where("trans_fee >", 0);
            $this->db->where('amount_type', 'user_credit');
            if ($user_id) {
                $this->db->where('from_user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("date >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date <=", $to_date);
            }
            $count += $this->db->count_all_results('fund_transfer_details');
        }

        if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'payout_fee'])) {
            $this->db->where('status', 'released');
            $this->db->where("payout_fee >", 0);
            if ($user_id) {
                $this->db->where('requested_user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("requested_date >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("requested_date <=", $to_date);
            }
            $count += $this->db->count_all_results('payout_release_requests');
        }
        
        $bonus_list_all = $this->getEnabledBonusList();
        
        if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'commission_charge'])) {
            $this->db->where_in('amount_type', $bonus_list_all);
            $this->db->where('tds + service_charge >', 0, false);
            if ($user_id) {
                $this->db->where('user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("date_of_submission >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date_of_submission <=", $to_date);
            }
            $count += $this->db->count_all_results('leg_amount');
        }

        if (in_array($cat_type, ['all', 'commission']) && in_array($category, array_merge(['all'], $bonus_list_all))) {
            if ($category == 'all') {
                $this->db->where_in('amount_type', $bonus_list_all);
            } else {
                if ($category == 'donation') {
                    $list = array_intersect(['donation', 'purchase_donation'], $bonus_list_all);
                    $this->db->where_in('amount_type', $list);
                } elseif ($category == 'level_commission') {
                    $list = array_intersect(['level_commission', 'repurchase_level_commission', 'upgrade_level_commission'], $bonus_list_all);
                    $this->db->where_in('amount_type', $list);
                } elseif ($category == 'xup_commission') {
                    $list = array_intersect(['xup_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission'], $bonus_list_all);
                    $this->db->where_in('amount_type', $list);
                } elseif ($category == 'leg') {
                    $list = array_intersect(['leg', 'repurchase_leg', 'upgrade_leg'], $bonus_list_all);
                    $this->db->where_in('amount_type', $list);
                } elseif ($category == 'matching_bonus') {
                    $list = array_intersect(['matching_bonus', 'matching_bonus_purchase', 'matching_bonus_upgrade'], $bonus_list_all);
                    $this->db->where_in('amount_type', $list);
                } else {
                    $this->db->where('amount_type', $category);
                }
            }
            if ($user_id) {
                $this->db->where('user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("date_of_submission >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date_of_submission <=", $to_date);
            }
            $count += $this->db->count_all_results('leg_amount');
        }

        if (in_array($cat_type, ['all', 'paid']) && in_array($category, ['all', 'payout_approved_paid'])) {
            $this->db->where('paid_type', 'released');
            $this->db->where('paid_status', 'yes');
            if ($user_id) {
                $this->db->where('paid_user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("paid_date >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("paid_date <=", $to_date);
            }
            $count += $this->db->count_all_results('amount_paid');
        }

        if (in_array($cat_type, ['all', 'pending']) && in_array($category, ['all', 'payout_approved_pending', 'payout_requests_pending'])) {
            if ($category != 'payout_requests_pending') {
                $this->db->where('paid_type', 'released');
                $this->db->where('paid_status !=', 'yes');
                $this->db->where('payment_method','bank');
                if ($user_id) {
                    $this->db->where('paid_user_id', $user_id);
                }
                if ($from_date) {
                    $this->db->where("paid_date >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("paid_date <=", $to_date);
                }
                $count += $this->db->count_all_results('amount_paid');
            }

            if ($category != 'payout_approved_pending') {
                $this->db->where('status', 'pending');
                if ($user_id) {
                    $this->db->where('requested_user_id', $user_id);
                }
                if ($from_date) {
                    $this->db->where("requested_date >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("requested_date <=", $to_date);
                }
                $count += $this->db->count_all_results('payout_release_requests');
            }
        }
        
        return $count;
    }

    public function getAllTransaction($user_id, $cat_type, $category, $from_date, $to_date, $page, $limit)
    {
        if ($from_date) {
            $from_date .= ' 00:00:00';
        }
        if ($to_date) {
            $to_date .= ' 23:59:59';
        }

        $query_union = [];
        
        if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
            if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'package_amount'])) {
                $this->db->select("total_amount amount,'package_amount' category,'income' cat_type,reg_date transaction_date,user_id", false);
                if ($user_id) {
                    $this->db->where('user_id', $user_id);
                }
                if ($from_date) {
                    $this->db->where("reg_date >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("reg_date <=", $to_date);
                }
                $query_union[] = $this->db->get_compiled_select('infinite_user_registration_details');
            }

            if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'repurchase'])) {
                $this->db->select("e.total amount,'repurchase' category,'income' cat_type,e.date_added transaction_date,f.id user_id", false);
                $this->db->from('oc_order as e');
                $this->db->join('ft_individual as f', 'e.customer_id = f.oc_customer_ref_id');
                $this->db->where('e.order_type', 'purchase');
                $this->db->where('e.order_status_id >', 0);
                if ($user_id) {
                    $this->db->where('f.id', $user_id);
                }
                if ($from_date) {
                    $this->db->where("e.date_added >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("e.date_added <=", $to_date);
                }
                $query_union[] = $this->db->get_compiled_select();
            }
        } else {
            if ($this->MODULE_STATUS['product_status'] == 'yes') {
                if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'package_amount'])) {
                    $this->db->select("product_amount amount,'package_amount' category,'income' cat_type,reg_date transaction_date,user_id", false);
                    if ($user_id) {
                        $this->db->where('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("reg_date >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("reg_date <=", $to_date);
                    }
                    $query_union[] = $this->db->get_compiled_select('infinite_user_registration_details');
                }
                
                if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'joining_fee'])) {
                    $this->db->select("reg_amount amount,'joining_fee' category,'income' cat_type,reg_date transaction_date,user_id", false);
                    if ($user_id) {
                        $this->db->where('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("reg_date >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("reg_date <=", $to_date);
                    }
                    $query_union[] = $this->db->get_compiled_select('infinite_user_registration_details');
                }
            }
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes') {
                if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'repurchase'])) {
                    $this->db->select("total_amount amount,'repurchase' category,'income' cat_type,order_date transaction_date,user_id", false);
                    $this->db->where('order_status', 'confirmed');
                    if ($user_id) {
                        $this->db->where('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("order_date >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("order_date <=", $to_date);
                    }
                    $query_union[] = $this->db->get_compiled_select('repurchase_order');
                }
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'upgrade'])) {
                    $this->db->select("amount,'upgrade' category,'income' cat_type,date_added transaction_date,user_id", false);
                    if ($user_id) {
                        $this->db->where('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("date_added >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("date_added <=", $to_date);
                    }
                    $query_union[] = $this->db->get_compiled_select('upgrade_sales_order');
                }
            }
            if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'membership_reactivation'])) {
                    $this->db->select("total_amount amount,'membership_reactivation' category,'income' cat_type,date_submitted transaction_date,user_id", false);
                    if ($user_id) {
                        $this->db->where('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("date_submitted >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("date_submitted <=", $to_date);
                    }
                    $query_union[] = $this->db->get_compiled_select('package_validity_extend_history');
                }
            }
        }

        if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'fund_transfer_fee'])) {
            $this->db->select("trans_fee amount,'fund_transfer_fee' category,'income' cat_type,date transaction_date,from_user_id user_id");
            $this->db->where('amount_type', 'user_credit');
            $this->db->where("trans_fee >", 0);
            if ($user_id) {
                $this->db->where('from_user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("date >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date <=", $to_date);
            }
            $query_union[] = $this->db->get_compiled_select('fund_transfer_details');
        }

        // payout fee
        if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'payout_fee'])) {
            $this->db->select("payout_fee amount,'payout_fee' category,'income' cat_type,paid_date transaction_date,paid_user_id user_id");
            $this->db->where("payout_fee >", 0);
            if ($user_id) {
                $this->db->where('paid_user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("paid_date >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("paid_date <=", $to_date);
            }
            $query_union[] = $this->db->get_compiled_select('amount_paid');
        }
        // payout fee end

        
        $bonus_list_all = $this->getEnabledBonusList();
        
        if (in_array($cat_type, ['all', 'income']) && in_array($category, ['all', 'commission_charge'])) {
            $this->db->select("(tds + service_charge) amount,'commission_charge' category,'income' cat_type,date_of_submission transaction_date,user_id");
            $this->db->where_in('amount_type', $bonus_list_all);
            $this->db->where('tds + service_charge >', 0, false);
            if ($user_id) {
                $this->db->where('user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("date_of_submission >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date_of_submission <=", $to_date);
            }
            $query_union[] = $this->db->get_compiled_select('leg_amount');
        }

        if (in_array($cat_type, ['all', 'commission']) && in_array($category, array_merge(['all'], $bonus_list_all))) {
            $this->db->select("total_amount amount", false);
            $this->db->select("CASE WHEN amount_type = 'purchase_donation' THEN 'donation' WHEN amount_type = 'repurchase_level_commission' THEN 'level_commission' WHEN amount_type = 'upgrade_level_commission' THEN 'level_commission' WHEN amount_type = 'xup_repurchase_level_commission' THEN 'xup_commission' WHEN amount_type = 'xup_upgrade_level_commission' THEN 'xup_commission' WHEN amount_type = 'repurchase_leg' THEN 'leg' WHEN amount_type = 'upgrade_leg' THEN 'leg' WHEN amount_type = 'matching_bonus_purchase' THEN 'matching_bonus' WHEN amount_type = 'matching_bonus_upgrade' THEN 'matching_bonus' ELSE amount_type END AS category");
            $this->db->select("'commission' cat_type,date_of_submission transaction_date,user_id", false);
            if ($category == 'all') {
                $this->db->where_in('amount_type', $bonus_list_all);
            } else {
                if ($category == 'donation') {
                    $list = array_intersect(['donation', 'purchase_donation'], $bonus_list_all);
                    $this->db->where_in('amount_type', $list);
                } elseif ($category == 'level_commission') {
                    $list = array_intersect(['level_commission', 'repurchase_level_commission', 'upgrade_level_commission'], $bonus_list_all);
                    $this->db->where_in('amount_type', $list);
                } elseif ($category == 'xup_commission') {
                    $list = array_intersect(['xup_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission'], $bonus_list_all);
                    $this->db->where_in('amount_type', $list);
                } elseif ($category == 'leg') {
                    $list = array_intersect(['leg', 'repurchase_leg', 'upgrade_leg'], $bonus_list_all);
                    $this->db->where_in('amount_type', $list);
                } elseif ($category == 'matching_bonus') {
                    $list = array_intersect(['matching_bonus', 'matching_bonus_purchase', 'matching_bonus_upgrade'], $bonus_list_all);
                    $this->db->where_in('amount_type', $list);
                } else {
                    $this->db->where('amount_type', $category);
                }
            }
            if ($user_id) {
                $this->db->where('user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("date_of_submission >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date_of_submission <=", $to_date);
            }
            $query_union[] = $this->db->get_compiled_select('leg_amount');
        }

        if (in_array($cat_type, ['all', 'paid']) && in_array($category, ['all', 'payout_approved_paid'])) {
            $this->db->select("paid_amount amount,'payout_approved_paid' category,'paid' cat_type,paid_date transaction_date,paid_user_id user_id", false);
            $this->db->where('paid_type', 'released');
            $this->db->where('paid_status', 'yes');
            if ($user_id) {
                $this->db->where('paid_user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("paid_date >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("paid_date <=", $to_date);
            }
            $query_union[] = $this->db->get_compiled_select('amount_paid');
        }

        if (in_array($cat_type, ['all', 'pending']) && in_array($category, ['all', 'payout_approved_pending', 'payout_requests_pending'])) {
            if ($category != 'payout_requests_pending') {
                $this->db->select("paid_amount amount,'payout_approved_pending' category,'pending' cat_type,paid_date transaction_date,paid_user_id user_id", false);
                $this->db->where('paid_type', 'released');
                $this->db->where('paid_status !=', 'yes');
                $this->db->where('payment_method','bank');
                if ($user_id) {
                    $this->db->where('paid_user_id', $user_id);
                }
                if ($from_date) {
                    $this->db->where("paid_date >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("paid_date <=", $to_date);
                }
                $query_union[] = $this->db->get_compiled_select('amount_paid');
            }

            if ($category != 'payout_approved_pending') {
                $this->db->select("requested_amount_balance amount,'payout_requests_pending' category,'pending' cat_type,requested_date transaction_date,requested_user_id user_id", false);
                $this->db->where('status', 'pending');
                if ($user_id) {
                    $this->db->where('requested_user_id', $user_id);
                }
                if ($from_date) {
                    $this->db->where("requested_date >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("requested_date <=", $to_date);
                }
                $query_union[] = $this->db->get_compiled_select('payout_release_requests');
            }
        }
        
        if ($query_union) {
            $query = implode(" UNION ALL ", $query_union);
            $dbprefix = $this->db->dbprefix;
            $res = $this->db->query("SELECT t.*,f.user_name,f.delete_status,u.user_detail_name,u.user_detail_second_name FROM ({$query}) t JOIN {$dbprefix}ft_individual f ON (t.user_id = f.id) LEFT JOIN {$dbprefix}user_details u ON (u.user_detail_refid = f.id) ORDER BY t.transaction_date DESC LIMIT {$page}, {$limit}");
            
            
            return $res->result_array();
        }
        return [];
    }
    
    public function getEnabledBusinessCategories()
    {
        $categories = [];
        
        if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
            $categories[] = 'package_amount';
            
            $categories[] = 'repurchase';
        } else {
            $categories[] = 'joining_fee';

            if ($this->MODULE_STATUS['product_status'] == 'yes') {
                $categories[] = 'package_amount';
            }
            
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes') {
                $categories[] = 'repurchase';
            }
            
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $categories[] = 'upgrade';
            }
            if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                $categories[] = 'membership_reactivation';
            }
        }

        $categories[] = 'fund_transfer_fee';

        $categories[] = 'commission_charge';

        $bonus_list = $this->getEnabledBonusList();
        $bonus_list = array_diff($bonus_list, ['purchase_donation', 'repurchase_level_commission', 'upgrade_level_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission', 'repurchase_leg', 'upgrade_leg','matching_bonus_purchase', 'matching_bonus_upgrade']);

        $categories = array_merge($categories, $bonus_list);
        
        $categories[] = 'payout_approved_paid';
        
        $categories[] = 'payout_approved_pending';

        $categories[] = 'payout_requests_pending';

        $categories[] = 'payout_fee';
        
        return $categories;
    }

    public function getEnabledCategories()
    {
        $list = [];
        $level_commission_status = 'no';
        if (in_array($this->MLM_PLAN, ['Matrix', 'Unilevel', 'Donation']) || $this->MODULE_STATUS['sponsor_commission_status'] == 'yes') {
            $level_commission_status = 'yes';
        }
        $xup_commission_status = 'no';
        if ($this->MODULE_STATUS['xup_status'] == 'yes' && $level_commission_status == 'yes') {
            $xup_commission_status = 'yes';
            $level_commission_status = 'no';
        }
        if ($this->MODULE_STATUS['referal_status'] == 'yes') {
            $list[] = 'referral';
        }
        if ($this->MODULE_STATUS['rank_status'] == 'yes') {
            $list[] = 'rank_bonus';
        }
        if ($level_commission_status == 'yes') {
            $list[] = 'level_commission';
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes' || $this->MODULE_STATUS['opencart_status'] == 'yes') {
                $list[] = 'repurchase_level_commission';
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $list[] = 'upgrade_level_commission';
            }
        }
        if ($xup_commission_status == 'yes') {
            $list[] = 'xup_commission';
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes' || $this->MODULE_STATUS['opencart_status'] == 'yes') {
                $list[] = 'xup_repurchase_level_commission';
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $list[] = 'xup_upgrade_level_commission';
            }
        }
        if ($this->MLM_PLAN == 'Binary') {
            $list[] = 'leg';
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes' || $this->MODULE_STATUS['opencart_status'] == 'yes') {
                $list[] = 'repurchase_leg';
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $list[] = 'upgrade_leg';
            }
        }
        if ($this->MLM_PLAN == 'Stair_Step') {
            $list[] = 'stair_step';
            $list[] = 'override_bonus';
        }
        if ($this->MLM_PLAN == 'Board') {
            $list[] = 'board_commission';
        }
        if ($this->MODULE_STATUS['roi_status'] == 'yes' || $this->MODULE_STATUS['hyip_status'] == 'yes') {
            $list[] = 'daily_investment';
        }
        if ($this->MLM_PLAN == 'Donation') {
            $list[] = 'donation';
        }
        $additional_bonus_status = $this->validation_model->getConfig(['matching_bonus', 'pool_bonus', 'fast_start_bonus', 'performance_bonus']);
        if ($additional_bonus_status['matching_bonus'] == 'yes') {
            $list[] = 'matching_bonus';
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes' || $this->MODULE_STATUS['opencart_status'] == 'yes') {
                $list[] = 'matching_bonus_purchase';
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $list[] = 'matching_bonus_upgrade';
            }
        }
        if ($additional_bonus_status['pool_bonus'] == 'yes') {
            $list[] = 'pool_bonus';
        }
        if ($additional_bonus_status['fast_start_bonus'] == 'yes') {
            $list[] = 'fast_start_bonus';
        }
        if ($additional_bonus_status['performance_bonus'] == 'yes') {
            $performance_bonus_types = $this->getPerformanceBonusTypes();
            foreach ($performance_bonus_types as $v) {
                $list[] = $v;
            }
        }

        $list[] = 'joining_fee';
        if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
            $list[] = 'repurchase';
        } else {
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes') {
                $list[] = 'repurchase';
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $list[] = 'upgrade';
            }
            if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                $list[] = 'membership_reactivation';
            }
        }
        if ($this->MODULE_STATUS['pin_status'] == 'yes') {
            $list[] = 'epin_generated';
            $list[] = 'pin_purchase';
            $list[] = 'pin_purchase_credit';
        }
        $list[] = 'admin_credit';
        $list[] = 'admin_debit';
        return $list;
    }

    public function getAllEwalletTransactionCount($user_id, $cat_type, $category, $from_date, $to_date)
    {
        if ($from_date) {
            $from_date .= ' 00:00:00';
        }
        if ($to_date) {
            $to_date .= ' 23:59:59';
        }

        $count = 0;

        $bonus_list_all = $this->getEnabledBonusList();

        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        if ($from_date) {
            $this->db->where("date_added >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_added <=", $to_date);
        }
        if (in_array($cat_type, ['credit', 'debit'])) {
            $this->db->where('type', $cat_type);
        }
        if ($category != 'all') {
            if (substr($category, 0, strlen('ewallet_payment')) === 'ewallet_payment') {
                $this->db->where("CONCAT(ewallet_type, '_', amount_type)='{$category}'");
            } elseif ($category == 'donation') {
                $this->db->where_in('amount_type', ['donation', 'purchase_donation']);
            } elseif ($category == 'level_commission') {
                $list = array_intersect(['level_commission', 'repurchase_level_commission', 'upgrade_level_commission'], $bonus_list_all);
                $this->db->where_in('amount_type', $list);
            } elseif ($category == 'xup_commission') {
                $list = array_intersect(['xup_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission'], $bonus_list_all);
                $this->db->where_in('amount_type', $list);
            } elseif ($category == 'leg') {
                $list = array_intersect(['leg', 'repurchase_leg', 'upgrade_leg'], $bonus_list_all);
                $this->db->where_in('amount_type', $list);
            } elseif ($category == 'matching_bonus') {
                $list = array_intersect(['matching_bonus', 'matching_bonus_purchase', 'matching_bonus_upgrade'], $bonus_list_all);
                $this->db->where_in('amount_type', $list);
            } elseif ($category == 'fund_transfer') {
                $this->db->where_in('amount_type', ['user_credit', 'user_debit']);
            } elseif ($category == 'payout_release_request') {
                $this->db->where_in('amount_type', ['payout_request']);
            } elseif ($category == 'payout_cancel') {
                $this->db->where_in('amount_type', ['payout_delete', 'payout_inactive', 'withdrawal_cancel']);
            } elseif ($category == 'pin_purchase_refund') {
                $this->db->where_in('amount_type', ['pin_purchase_delete', 'pin_purchase_refund']);
            } else {
                $this->db->where('amount_type', $category);
            }
        }
        
        $this->db->where('amount_type !=', 'payout_release');
        $count += $this->db->count_all_results('ewallet_history');
        
        if (in_array($cat_type, ['all', 'debit']) && in_array($category, ['all', 'fund_transfer_fee'])) {
            $this->db->where("trans_fee >", 0);
            $this->db->where('amount_type', 'user_credit');
            if ($user_id) {
                $this->db->where('from_user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("date >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date <=", $to_date);
            }
            $count += $this->db->count_all_results('fund_transfer_details');
        }

        return $count;
    }

    public function getAllEwalletTransaction($user_id, $cat_type, $category, $from_date, $to_date, $page, $limit)
    {
        if ($from_date) {
            $from_date .= ' 00:00:00';
        }
        if ($to_date) {
            $to_date .= ' 23:59:59';
        }

        $bonus_list_all = $this->getEnabledBonusList();
        
        $this->db->select("(amount-purchase_wallet) amount", false);
        $this->db->select("CASE WHEN amount_type = 'purchase_donation' THEN 'donation' WHEN amount_type = 'repurchase_level_commission' THEN 'level_commission' WHEN amount_type = 'upgrade_level_commission' THEN 'level_commission' WHEN amount_type = 'xup_repurchase_level_commission' THEN 'xup_commission' WHEN amount_type = 'xup_upgrade_level_commission' THEN 'xup_commission' WHEN amount_type = 'repurchase_leg' THEN 'leg' WHEN amount_type = 'upgrade_leg' THEN 'leg' WHEN amount_type = 'matching_bonus_purchase' THEN 'matching_bonus' WHEN amount_type = 'matching_bonus_upgrade' THEN 'matching_bonus' WHEN ewallet_type = 'ewallet_payment' THEN CONCAT(ewallet_type, '_', amount_type) WHEN (amount_type = 'user_credit' OR amount_type = 'user_debit') THEN 'fund_transfer' WHEN amount_type = 'payout_request' THEN 'payout_release_request' WHEN (amount_type = 'payout_delete' OR amount_type = 'payout_inactive' OR amount_type = 'withdrawal_cancel') THEN 'payout_cancel' WHEN (amount_type = 'pin_purchase_delete' OR amount_type = 'pin_purchase_refund') THEN 'pin_purchase_refund' ELSE amount_type END AS category");
        $this->db->select("type cat_type,date_added transaction_date,user_id");
        $this->db->from('ewallet_history');
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        if ($from_date) {
            $this->db->where("date_added >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_added <=", $to_date);
        }
        if (in_array($cat_type, ['credit', 'debit'])) {
            $this->db->where('type', $cat_type);
        }
        if ($category != 'all') {
            if (substr($category, 0, strlen('ewallet_payment')) === 'ewallet_payment') {
                $this->db->where("CONCAT(ewallet_type, '_', amount_type)='{$category}'");
            } elseif ($category == 'donation') {
                $this->db->where_in('amount_type', ['donation', 'purchase_donation']);
            } elseif ($category == 'level_commission') {
                $list = array_intersect(['level_commission', 'repurchase_level_commission', 'upgrade_level_commission'], $bonus_list_all);
                $this->db->where_in('amount_type', $list);
            } elseif ($category == 'xup_commission') {
                $list = array_intersect(['xup_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission'], $bonus_list_all);
                $this->db->where_in('amount_type', $list);
            } elseif ($category == 'leg') {
                $list = array_intersect(['leg', 'repurchase_leg', 'upgrade_leg'], $bonus_list_all);
                $this->db->where_in('amount_type', $list);
            } elseif ($category == 'matching_bonus') {
                $list = array_intersect(['matching_bonus', 'matching_bonus_purchase', 'matching_bonus_upgrade'], $bonus_list_all);
                $this->db->where_in('amount_type', $list);
            } elseif ($category == 'fund_transfer') {
                $this->db->where_in('amount_type', ['user_credit', 'user_debit']);
            } elseif ($category == 'payout_release_request') {
                $this->db->where_in('amount_type', ['payout_request']);
            } elseif ($category == 'payout_cancel') {
                $this->db->where_in('amount_type', ['payout_delete', 'payout_inactive', 'withdrawal_cancel']);
            } elseif ($category == 'pin_purchase_refund') {
                $this->db->where_in('amount_type', ['pin_purchase_delete', 'pin_purchase_refund']);
            } else {
                $this->db->where('amount_type', $category);
            }
        }
        $this->db->where('amount_type !=', 'payout_release');
        $quey_set[] = $this->db->get_compiled_select();

        if (in_array($cat_type, ['all', 'debit']) && in_array($category, ['all', 'fund_transfer_fee'])) {
            $this->db->select("trans_fee amount,'fund_transfer_fee' category,'debit' cat_type,date transaction_date,from_user_id user_id");
            $this->db->where('amount_type', 'user_credit');
            $this->db->where("trans_fee >", 0);
            if ($user_id) {
                $this->db->where('from_user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("date >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date <=", $to_date);
            }
            $quey_set[] = $this->db->get_compiled_select('fund_transfer_details');
        }

        // payout fee
        if (in_array($cat_type, ['all', 'debit']) && in_array($category, ['all', 'payout_fee'])) {
            $this->db->select("transaction_fee amount,'payout_fee' category,'debit' cat_type,date_added transaction_date,user_id");
            $this->db->where('amount_type', 'payout_request');
            $this->db->where("transaction_fee >", 0);
            if ($user_id) {
                $this->db->where('user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("date_added >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date_added <=", $to_date);
            }
            $quey_set[] = $this->db->get_compiled_select('ewallet_history');
        }
        // payout fee end

        $query = implode(" UNION ALL ", $quey_set);
        $dbprefix = $this->db->dbprefix;
        $res = $this->db->query("SELECT t.*,f.user_name, f.delete_status,u.user_detail_name,u.user_detail_second_name FROM ({$query}) t LEFT JOIN {$dbprefix}ft_individual f ON (t.user_id = f.id) LEFT JOIN {$dbprefix}user_details u ON (u.user_detail_refid = f.id) ORDER BY t.transaction_date DESC LIMIT {$page}, {$limit}");

        return $res->result_array();
    }

    public function getEnabledEwalletCategories()
    {
        $categories = [];

        if ($this->MODULE_STATUS['pin_status'] == 'yes') {
            $categories[] = 'pin_purchase';
            $categories[] = 'pin_purchase_refund';
        }
        
        $categories[] = 'admin_credit';
        
        $categories[] = 'admin_debit';
        
        $categories[] = 'fund_transfer';

        $categories[] = 'fund_transfer_fee';
        
        $categories[] = 'payout_release_request';

        $categories[] = 'payout_fee';
        
        $categories[] = 'payout_release_manual';
        
        $categories[] = 'payout_cancel';
        
        $this->load->model('register_model');
        $ewallet_status = $this->register_model->getPaymentStatus('E-wallet');
        if ($ewallet_status == 'yes') {
            $categories[] = 'ewallet_payment_registration';
            
            if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
                $categories[] = 'ewallet_payment_repurchase';
            } else {
                if ($this->MODULE_STATUS['repurchase_status'] == 'yes') {
                    $categories[] = 'ewallet_payment_repurchase';
                }
                if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                    $categories[] = 'ewallet_payment_upgrade';
                }
                if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                    $categories[] = 'ewallet_payment_package_validity';
                }
            }
        }
        
        $bonus_list = $this->getEnabledBonusList();
        $bonus_list = array_diff($bonus_list, ['purchase_donation', 'repurchase_level_commission', 'upgrade_level_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission', 'repurchase_leg', 'upgrade_leg','matching_bonus_purchase', 'matching_bonus_upgrade']);

        $categories = array_merge($categories, $bonus_list);
        
        return $categories;
    }

    public function getEnabledBonusCategories()
    {
        $categories = [];
        
        $bonus_list = $this->getEnabledBonusList();
        $bonus_list = array_diff($bonus_list, ['purchase_donation', 'repurchase_level_commission', 'upgrade_level_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission', 'repurchase_leg', 'upgrade_leg','matching_bonus_purchase', 'matching_bonus_upgrade']);

        $categories = array_merge($categories, $bonus_list);
        
        return $categories;
    }
    
    public function getUserEwalletDetailsCount($user_id, $from_date, $to_date, $type = []) {
        if ($from_date) {
            $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
        }
        if ($to_date) {
            $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
        }

        $details = array();
        if ($user_id != '') {
            $this->db->group_start();
            $this->db->where('to_user_id', $user_id);
            $this->db->group_end();
        }
        
        if ($from_date != '') {
            $this->db->where("date >=", $from_date);
        }
        if ($to_date != '') {
            $this->db->where("date <=", $to_date);
        }

        if(!empty($type)) {
            $this->db->where_in('amount_type', $type);
        }
        $this->db->where_not_in('amount_type', ['admin_debit', 'admin_credit']);
        $this->db->group_by('transaction_id');
        // $this->db->get('fund_transfer_details');
        // dump($this->db->last_query());
        return $this->db->count_all_results('fund_transfer_details');
    }

    public function getEwalletOutwardFundDetails($user_id, $category, $date, $page, $limit)
    {
        $current_day = date('Y-m-d');
        $this->db->select("amount");
        $this->db->select("CASE WHEN amount_type = 'purchase_donation' THEN 'donation' WHEN ewallet_type = 'ewallet_payment' THEN CONCAT(ewallet_type, '_', amount_type) WHEN amount_type = 'user_credit' OR amount_type = 'user_debit' THEN ewallet_type ELSE amount_type END AS amount_type");
        $this->db->select("type,date_added,user_id");
        $this->db->from('ewallet_history');
        $this->db->where('type', 'debit');
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }

        if ($category) {
            if (substr($category, 0, strlen('ewallet_payment')) === 'ewallet_payment') {
                $this->db->where("CONCAT(ewallet_type, '_', amount_type)='{$category}'");
            }
            elseif ($category == 'donation') {
                $this->db->where_in('amount_type', ['donation', 'purchase_donation']);
            }
            elseif ($category == 'fund_transfer') {
                $this->db->where('type', 'debit');
            }
            elseif ($category == 'payout_release_request') {
                $this->db->where_in('amount_type', ['payout_request', 'payout_release_manual']);
            }
            elseif ($category == 'payout_cancel') {
                $this->db->where_in('amount_type', ['payout_delete', 'payout_inactive', 'withdrawal_cancel']);
            }
            elseif ($category == 'pin_purchase_credit') {
                $this->db->where_in('amount_type', ['pin_purchase_delete', 'pin_purchase_refund']);
            }
            elseif (in_array($category, ['joining_fee', 'repurchase', 'upgrade', 'membership_reactivation'])) {
                $this->db->where("1=0");
            }
            else {
                $this->db->where('amount_type', $category);
            }
        }
        if ($date == 'month') {
            $this->db->where("MONTH(date_added)=MONTH('{$current_day}')");
            $this->db->where("YEAR(date_added)=YEAR('{$current_day}')");
        }
        $this->db->where('amount_type !=', 'payout_release');
        $quey_set[] = $this->db->get_compiled_select();

        $query = implode(" UNION ALL ", $quey_set);
        $dbprefix = $this->db->dbprefix;
        $res = $this->db->query("SELECT t.*,f.user_name FROM ({$query}) t LEFT JOIN {$dbprefix}ft_individual f ON (t.user_id = f.id) ORDER BY date_added LIMIT {$page}, {$limit}");

        return [
            'categories' => $this->getEnabledEwalletCategories(),
            'data' => $res->result_array()
        ];
    }

    public function getEwalletOutwardFundCount($user_id, $category, $date)
    {
        $current_day = date('Y-m-d');
        $count = 0;
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        $this->db->where('type', 'debit');
        if ($category) {
            if (substr($category, 0, strlen('ewallet_payment')) === 'ewallet_payment') {
                $this->db->where("CONCAT(ewallet_type, '_', amount_type)='{$category}'");
            } elseif ($category == 'donation') {
                $this->db->where_in('amount_type', ['donation', 'purchase_donation']);
            } elseif ($category == 'fund_transfer') {
                $this->db->where('type', 'debit');
            } elseif ($category == 'payout_release_request') {
                $this->db->where_in('amount_type', ['payout_request', 'payout_release_manual']);
            } elseif ($category == 'payout_cancel') {
                $this->db->where_in('amount_type', ['payout_delete', 'payout_inactive', 'withdrawal_cancel']);
            } elseif ($category == 'pin_purchase_credit') {
                $this->db->where_in('amount_type', ['pin_purchase_delete', 'pin_purchase_refund']);
            } else {
                $this->db->where('amount_type', $category);
            }
        }
        if ($date == 'month') {
            $this->db->where("MONTH(date_added)=MONTH('{$current_day}')");
            $this->db->where("YEAR(date_added)=YEAR('{$current_day}')");
        }
        if ($date == 'year') {
            $this->db->where("YEAR(date_added)=YEAR('{$current_day}')");
        }
        $this->db->where('amount_type !=', 'payout_release');
        $count += $this->db->count_all_results('ewallet_history');

        return $count;
    }

    public function getEwalletSummary($from_date, $to_date)
    {
        if ($from_date) {
            $from_date .= ' 00:00:00';
        }
        if ($to_date) {
            $to_date .= ' 23:59:59';
        }
        
        $wallet_details = [];

        if ($this->MODULE_STATUS['pin_status'] == 'yes') {
            $this->db->select_sum('pin_amount');
            $this->db->where('purchase_status', 'yes');
            if ($from_date) {
                $this->db->where("pin_uploded_date >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("pin_uploded_date <=", $to_date);
            }
            $query = $this->db->get('pin_numbers');
            $epin_purchase_amount = $query->row_array()['pin_amount'] ?? 0;

            $wallet_details['pin_purchase'] = [
                'type' => 'debit',
                'amount' => $epin_purchase_amount
            ];

            $this->db->select_sum('amount');
            $this->db->where_in('amount_type', ['pin_purchase_refund', 'pin_purchase_delete']);
            if ($from_date) {
                $this->db->where("date_added >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date_added <=", $to_date);
            }
            $query = $this->db->get('ewallet_history');
            $epin_refund_amount = $query->row_array()['amount'] ?? 0;

            $wallet_details['pin_purchase_refund'] = [
                'type' => 'credit',
                'amount' => $epin_refund_amount
            ];
        }

        $this->db->select_sum('amount');
        $this->db->where('amount_type', 'admin_credit');
        if ($from_date) {
            $this->db->where("date_added >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_added <=", $to_date);
        }
        $query = $this->db->get('ewallet_history');
        $admin_credit_amount = $query->row_array()['amount'] ?? 0;

        $wallet_details['admin_credit'] = [
            'type' => 'credit',
            'amount' => $admin_credit_amount
        ];

        $this->db->select_sum('amount');
        $this->db->where('amount_type', 'admin_debit');
        if ($from_date) {
            $this->db->where("date_added >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_added <=", $to_date);
        }
        $query = $this->db->get('ewallet_history');
        $admin_debit_amount = $query->row_array()['amount'] ?? 0;

        $wallet_details['admin_debit'] = [
            'type' => 'debit',
            'amount' => $admin_debit_amount
        ];

        $this->db->select_sum('amount');
        $this->db->where('amount_type', 'user_credit');
        if ($from_date) {
            $this->db->where("date_added >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_added <=", $to_date);
        }
        $query = $this->db->get('ewallet_history');
        $user_credit_amount = $query->row_array()['amount'] ?? 0;

        $wallet_details['fund_transfer1'] = [
            'type' => 'credit',
            'amount' => $user_credit_amount
        ];

        $this->db->select_sum('amount');
        $this->db->where('amount_type', 'user_debit');
        if ($from_date) {
            $this->db->where("date_added >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_added <=", $to_date);
        }
        $query = $this->db->get('ewallet_history');
        $user_debit_amount = $query->row_array()['amount'] ?? 0;

        $wallet_details['fund_transfer2'] = [
            'type' => 'debit',
            'amount' => $user_debit_amount
        ];

        $this->db->select_sum('trans_fee');
        $this->db->where('amount_type', 'user_credit');
        if ($from_date) {
            $this->db->where("date >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date <=", $to_date);
        }
        $query = $this->db->get('fund_transfer_details');
        $fund_transfer_fee_amount = $query->row_array()['trans_fee'] ?? 0;

        $wallet_details['fund_transfer_fee'] = [
            'type' => 'debit',
            'amount' => $fund_transfer_fee_amount
        ];

        $this->db->select_sum('amount');
        $this->db->where_in('amount_type', ['payout_request']);
        if ($from_date) {
            $this->db->where("date_added >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_added <=", $to_date);
        }
        $query = $this->db->get('ewallet_history');
        $payout_amount = $query->row_array()['amount'] ?? 0;

        $wallet_details['payout_release_request'] = [
            'type' => 'debit',
            'amount' => $payout_amount
        ];
        
        $this->db->select_sum('amount');
        $this->db->where_in('amount_type', ['payout_release_manual']);
        if ($from_date) {
            $this->db->where("date_added >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_added <=", $to_date);
        }
        $query = $this->db->get('ewallet_history');
        $payout_amount = $query->row_array()['amount'] ?? 0;

        $wallet_details['payout_release_manual'] = [
            'type' => 'debit',
            'amount' => $payout_amount
        ];

        $this->db->select_sum('amount');
        $this->db->where_in('amount_type', ['payout_delete', 'payout_inactive', 'withdrawal_cancel']);
        if ($from_date) {
            $this->db->where("date_added >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_added <=", $to_date);
        }
        $query = $this->db->get('ewallet_history');
        $payout_cancel_amount = $query->row_array()['amount'] ?? 0;

        $wallet_details['payout_cancel'] = [
            'type' => 'credit',
            'amount' => $payout_cancel_amount
        ];

        $this->load->model('register_model');
        $ewallet_status = $this->register_model->getPaymentStatus('E-wallet');
        if ($ewallet_status == 'yes') {
            $this->db->select_sum('amount');
            $this->db->where('amount_type', 'registration');
            $this->db->where('ewallet_type', 'ewallet_payment');
            if ($from_date) {
                $this->db->where("date_added >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date_added <=", $to_date);
            }
            $query = $this->db->get('ewallet_history');
            $ewallet_payment_registration_amount = $query->row_array()['amount'] ?? 0;

            $wallet_details['ewallet_payment_registration'] = [
                'type' => 'debit',
                'amount' => $ewallet_payment_registration_amount
            ];

            if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
                $this->db->select_sum('amount');
                $this->db->where('amount_type', 'repurchase');
                $this->db->where('ewallet_type', 'ewallet_payment');
                if ($from_date) {
                    $this->db->where("date_added >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("date_added <=", $to_date);
                }
                $query = $this->db->get('ewallet_history');
                $ewallet_payment_repurchase_amount = $query->row_array()['amount'] ?? 0;

                $wallet_details['ewallet_payment_repurchase'] = [
                    'type' => 'debit',
                    'amount' => $ewallet_payment_repurchase_amount
                ];
            } else {
                if ($this->MODULE_STATUS['repurchase_status'] == 'yes') {
                    $this->db->select_sum('amount');
                    $this->db->where('amount_type', 'repurchase');
                    $this->db->where('ewallet_type', 'ewallet_payment');
                    if ($from_date) {
                        $this->db->where("date_added >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("date_added <=", $to_date);
                    }
                    $query = $this->db->get('ewallet_history');
                    $ewallet_payment_repurchase_amount = $query->row_array()['amount'] ?? 0;

                    $wallet_details['ewallet_payment_repurchase'] = [
                        'type' => 'debit',
                        'amount' => $ewallet_payment_repurchase_amount
                    ];
                }
                if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                    $this->db->select_sum('amount');
                    $this->db->where('amount_type', 'upgrade');
                    $this->db->where('ewallet_type', 'ewallet_payment');
                    if ($from_date) {
                        $this->db->where("date_added >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("date_added <=", $to_date);
                    }
                    $query = $this->db->get('ewallet_history');
                    $ewallet_payment_upgrade_amount = $query->row_array()['amount'] ?? 0;

                    $wallet_details['ewallet_payment_upgrade'] = [
                        'type' => 'debit',
                        'amount' => $ewallet_payment_upgrade_amount
                    ];
                }
                if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                    $this->db->select_sum('amount');
                    $this->db->where('amount_type', 'package_validity');
                    $this->db->where('ewallet_type', 'ewallet_payment');
                    if ($from_date) {
                        $this->db->where("date_added >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("date_added <=", $to_date);
                    }
                    $query = $this->db->get('ewallet_history');
                    $ewallet_payment_package_validity_amount = $query->row_array()['amount'] ?? 0;

                    $wallet_details['ewallet_payment_package_validity'] = [
                        'type' => 'debit',
                        'amount' => $ewallet_payment_package_validity_amount
                    ];
                }
            }
        }

        $enabled_bonus_list = $this->getEnabledBonusList();
        foreach ($enabled_bonus_list as $bonus) {
            $wallet_details[$bonus] = [
                'type' => 'credit',
                'amount' => 0
            ];
        }
        $this->db->select('amount_type,SUM(amount_payable-purchase_wallet) total');
        $this->db->group_by('amount_type');
        $this->db->where_in('amount_type', $enabled_bonus_list);
        if ($from_date) {
            $this->db->where("date_of_submission >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_of_submission <=", $to_date);
        }
        $query = $this->db->get('leg_amount');
        foreach ($query->result_array() as $row) {
            $wallet_details[$row['amount_type']] = [
                'type' => 'credit',
                'amount' => $row['total'] ?? 0
            ];
        }

        // payout fee
        $this->db->select_sum('transaction_fee');
        $this->db->where_in('amount_type', ['payout_request']);
        if ($from_date) {
            $this->db->where("date_added >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_added <=", $to_date);
        }
        $query = $this->db->get('ewallet_history');
        $payout_cancel_amount = $query->row_array()['transaction_fee'] ?? 0;

        $wallet_details['payout_fee'] = [
            'type' => 'debit',
            'amount' => $payout_cancel_amount
        ];
        // payout fee end

        foreach ($wallet_details as $k => $v) {
            if (in_array($k, ['purchase_donation'])) {
                $wallet_details['donation']['amount'] += $wallet_details[$k]['amount'];
                unset($wallet_details[$k]);
            }
            if (in_array($k, ['repurchase_level_commission', 'upgrade_level_commission'])) {
                $wallet_details['level_commission']['amount'] += $wallet_details[$k]['amount'];
                unset($wallet_details[$k]);
            }
            if (in_array($k, ['xup_repurchase_level_commission', 'xup_upgrade_level_commission'])) {
                $wallet_details['xup_commission']['amount'] += $wallet_details[$k]['amount'];
                unset($wallet_details[$k]);
            }
            if (in_array($k, ['repurchase_leg', 'upgrade_leg'])) {
                $wallet_details['leg']['amount'] += $wallet_details[$k]['amount'];
                unset($wallet_details[$k]);
            }
            if (in_array($k, ['matching_bonus_purchase', 'matching_bonus_upgrade'])) {
                $wallet_details['matching_bonus']['amount'] += $wallet_details[$k]['amount'];
                unset($wallet_details[$k]);
            }
        }

        return $wallet_details;
    }

    public function getEwalletSummaryTotal()
    {
        $wallet_total = [
            'credit' => 0,
            'debit' => 0
        ];

        if ($this->MODULE_STATUS['pin_status'] == 'yes') {
            $this->db->select_sum('pin_amount');
            $this->db->where('purchase_status', 'yes');
            $query = $this->db->get('pin_numbers');
            $wallet_total['debit'] += $query->row_array()['pin_amount'] ?? 0;

            $this->db->select_sum('amount');
            $this->db->where_in('amount_type', ['pin_purchase_refund', 'pin_purchase_delete']);
            $query = $this->db->get('ewallet_history');
            $wallet_total['credit'] += $query->row_array()['amount'] ?? 0;
        }

        $this->db->select_sum('amount');
        $this->db->where('amount_type', 'admin_credit');
        $query = $this->db->get('ewallet_history');
        $wallet_total['credit'] += $query->row_array()['amount'] ?? 0;

        $this->db->select_sum('amount');
        $this->db->where('amount_type', 'admin_debit');
        $query = $this->db->get('ewallet_history');
        $wallet_total['debit'] += $query->row_array()['amount'] ?? 0;
        
        $this->db->select_sum('amount');
        $this->db->where('amount_type', 'user_credit');
        $query = $this->db->get('ewallet_history');
        $wallet_total['credit'] += $query->row_array()['amount'] ?? 0;

        $this->db->select_sum('amount');
        $this->db->where('amount_type', 'user_debit');
        $query = $this->db->get('ewallet_history');
        $wallet_total['debit'] += $query->row_array()['amount'] ?? 0;

        $this->db->select_sum('transaction_fee');
        $this->db->where('amount_type', 'payout_request');
        $query = $this->db->get('ewallet_history');
        $wallet_total['debit'] += $query->row_array()['transaction_fee'] ?? 0;

        $this->db->select_sum('trans_fee');
        $this->db->where('amount_type', 'user_credit');
        $query = $this->db->get('fund_transfer_details');
        $wallet_total['debit'] += $query->row_array()['trans_fee'] ?? 0;

        $this->db->select_sum('amount');
        $this->db->where_in('amount_type', ['payout_request', 'payout_release_manual']);
        $query = $this->db->get('ewallet_history');
        $wallet_total['debit'] += $query->row_array()['amount'] ?? 0;

        $this->db->select_sum('amount');
        $this->db->where_in('amount_type', ['payout_delete', 'payout_inactive', 'withdrawal_cancel']);
        $query = $this->db->get('ewallet_history');
        $wallet_total['credit'] += $query->row_array()['amount'] ?? 0;

        $this->load->model('register_model');
        $ewallet_status = $this->register_model->getPaymentStatus('E-wallet');
        if ($ewallet_status == 'yes') {
            $this->db->select_sum('amount');
            $this->db->where('amount_type', 'registration');
            $this->db->where('ewallet_type', 'ewallet_payment');
            $query = $this->db->get('ewallet_history');
            $wallet_total['debit'] += $query->row_array()['amount'] ?? 0;

            if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
                $this->db->select_sum('amount');
                $this->db->where('amount_type', 'repurchase');
                $this->db->where('ewallet_type', 'ewallet_payment');
                $query = $this->db->get('ewallet_history');
                $wallet_total['debit'] += $query->row_array()['amount'] ?? 0;
            } else {
                if ($this->MODULE_STATUS['repurchase_status'] == 'yes') {
                    $this->db->select_sum('amount');
                    $this->db->where('amount_type', 'repurchase');
                    $this->db->where('ewallet_type', 'ewallet_payment');
                    $query = $this->db->get('ewallet_history');
                    $wallet_total['debit'] += $query->row_array()['amount'] ?? 0;
                }
                if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                    $this->db->select_sum('amount');
                    $this->db->where('amount_type', 'upgrade');
                    $this->db->where('ewallet_type', 'ewallet_payment');
                    $query = $this->db->get('ewallet_history');
                    $wallet_total['debit'] += $query->row_array()['amount'] ?? 0;
                }
                if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                    $this->db->select_sum('amount');
                    $this->db->where('amount_type', 'package_validity');
                    $this->db->where('ewallet_type', 'ewallet_payment');
                    $query = $this->db->get('ewallet_history');
                    $wallet_total['debit'] += $query->row_array()['amount'] ?? 0;
                }
            }
        }

        $enabled_bonus_list = $this->getEnabledBonusList();
        $this->db->select('SUM(amount_payable-purchase_wallet) total');
        $this->db->where_in('amount_type', $enabled_bonus_list);
        $query = $this->db->get('leg_amount');
        $wallet_total['credit'] += $query->row_array()['total'] ?? 0;
        
        return $wallet_total;
    }

    public function getEwalletBalanceReport($user_id, $page = '', $limit = '')
    {
        $this->db->select("fi.user_name,CONCAT(ud.user_detail_name,' ',ud.user_detail_second_name) full_name,uba.balance_amount");
        $this->db->from("ft_individual as fi");
        $this->db->join("user_details as ud", "ud.user_detail_refid = fi.id");
        $this->db->join("user_balance_amount as uba", "uba.user_id = ud.user_detail_refid");
        if ($user_id) {
            $this->db->where('fi.id', $user_id);
        }
        $this->db->limit($limit, $page);
        $res = $this->db->get();
        return $res->result_array();
    }

    public function getEwalletBalanceReportCount($user_id)
    {
        if ($user_id) {
            $this->db->where('id', $user_id);
        }
        return $this->db->count_all_results('ft_individual');
    }
        public function ewalletPay($amount,$ewallet_user,$tp)
    {
        $ewallet_user_id = $this->validation_model->userNameToID($ewallet_user);
        $user_available = $this->validation_model->isUserAvailable($ewallet_user_id);
        if ($user_available) {
            $trans_pass_available = $this->register_model->checkEwalletPassword($ewallet_user_id, $tp);
            if ($trans_pass_available == 'yes') {
                $ewallet_balance_amount = $this->register_model->getBalanceAmount($ewallet_user_id);
                if ($ewallet_balance_amount >= $amount) {
                    return true;
                } else {
                    return 1014;
                }
            }else{
                return 1015;
            }
        } else {
            return 1011;
        }
    }
    public function getuserExpense($user_id)
    {
     $this->db->select_sum('amount');
     $this->db->where('user_id', $user_id);
     $this->db->where('type', 'debit');
     $query = $this->db->get('ewallet_history');
     $total_expense = $query->row_array()['amount'] ?? 0;
     return $total_expense;
    }
    public function getUserIncome($user_id)
    {
     $this->db->select_sum('amount');
     $this->db->where('user_id', $user_id);
     $this->db->where('type', 'credit');
     $query = $this->db->get('ewallet_history');
     $total_income = $query->row_array()['amount'] ?? 0;
     return $total_income;
    }


    public function getUserEearningsCount($user_id, $categories, $from_date, $to_date)
    {
        if ($from_date) {
            $from_date .= ' 00:00:00';
        }
        if ($to_date) {
            $to_date .= ' 23:59:59';
        }

        $count = 0;

        $bonus_list_all = $this->getEnabledBonusList();

        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        if ($from_date) {
            $this->db->where("date_of_submission >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_of_submission <=", $to_date);
        }
        if(!empty($categories)) {
            $this->db->group_start();
                if(in_array('donation', $categories)) {
                    $this->db->or_where_in('amount_type', ['donation', 'purchase_donation']);
                }
                
                if(in_array('level_commission', $categories)) {
                    $list1 = array_intersect(['level_commission', 'repurchase_level_commission', 'upgrade_level_commission'], $bonus_list_all);
                    $this->db->or_where_in('amount_type', $list1);
                }

                if(in_array('xup_commission', $categories)) {
                    $lis2 = array_intersect(['xup_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission'], $bonus_list_all);
                    $this->db->or_where_in('amount_type', $list2);
                }

                if(in_array('leg', $categories)) {
                    $list3 = array_intersect(['leg', 'repurchase_leg', 'upgrade_leg'], $bonus_list_all);
                    $this->db->or_where_in('amount_type', $list3);
                }

                if(in_array('matching_bonus', $categories)) {
                    $list4 = array_intersect(['matching_bonus', 'matching_bonus_purchase', 'matching_bonus_upgrade'], $bonus_list_all);
                    $this->db->or_where_in('amount_type', $list4);
                }

                if(count(array_diff($categories, $bonus_list_all)) === 0) {
                    $this->db->or_where_in('amount_type', $categories);
                }

            $this->db->group_end();
        }
        
        $count += $this->db->count_all_results('leg_amount');
        
        return $count;
    }

    public function getUserEarnings($user_id, $categories, $from_date, $to_date, $filter)
    {
        if ($from_date) {
            $from_date .= ' 00:00:00';
        }
        if ($to_date) {
            $to_date .= ' 23:59:59';
        }

        $bonus_list_all = $this->getEnabledBonusList();
        
        $this->db->select("CASE WHEN l.amount_type = 'purchase_donation' THEN 'donation' WHEN l.amount_type = 'repurchase_leg' THEN 'leg' WHEN l.amount_type = 'upgrade_leg' THEN 'leg' WHEN l.amount_type = 'matching_bonus_purchase' THEN 'matching_bonus' WHEN l.amount_type = 'matching_bonus_upgrade' THEN 'matching_bonus' ELSE l.amount_type END AS category");
        $this->db->select("l.total_amount, l.amount_payable, l.tds,l.service_charge,l.date_of_submission transaction_date,l.user_level,u.user_detail_name,u.user_detail_second_name,f.user_name");
        $this->db->from('leg_amount l');
        $this->db->join('user_details u', 'l.from_id=u.user_detail_refid', 'left');
        $this->db->join('ft_individual f', 'l.from_id=f.id', 'left');
        if ($user_id) {
            $this->db->where('l.user_id', $user_id);
        }
        if ($from_date) {
            $this->db->where("l.date_of_submission >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("l.date_of_submission <=", $to_date);
        }

        if(!empty($categories)) {
            $this->db->group_start();
                if(in_array('donation', $categories)) {
                    $this->db->or_where_in('amount_type', ['donation', 'purchase_donation']);
                }
                
                if(in_array('level_commission', $categories)) {
                    $list1 = array_intersect(['level_commission', 'repurchase_level_commission', 'upgrade_level_commission'], $bonus_list_all);
                    $this->db->or_where_in('amount_type', $list1);
                }

                if(in_array('xup_commission', $categories)) {
                    $lis2 = array_intersect(['xup_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission'], $bonus_list_all);
                    $this->db->or_where_in('amount_type', $list2);
                }

                if(in_array('leg', $categories)) {
                    $list3 = array_intersect(['leg', 'repurchase_leg', 'upgrade_leg'], $bonus_list_all);
                    $this->db->or_where_in('amount_type', $list3);
                }

                if(in_array('matching_bonus', $categories)) {
                    $list4 = array_intersect(['matching_bonus', 'matching_bonus_purchase', 'matching_bonus_upgrade'], $bonus_list_all);
                    $this->db->or_where_in('amount_type', $list4);
                }

                if(count(array_diff($categories, $bonus_list_all)) === 0) {
                    $this->db->or_where_in('amount_type', $categories);
                }

            $this->db->group_end();
        }

        // $this->db->order_by('date_of_submission', 'desc');
        $this->db->order_by($filter['order'], $filter['direction']);
        $this->db->limit($filter['limit'], $filter['start']);
        $quey = $this->db->get();
        return $quey->result_array();
    }

    public function getTotalCommissionEarned($user_id)
    {  
       $this->db->select('sum(amount_payable) sum');
       $this->db->from('leg_amount');
       $this->db->where('user_id',$user_id);
       $query=$this->db->get();
       $res=$query->row('sum');

       return $res;

    }
    public function getTotalEwalletBalanceOfAllUser()
    {
            $this->db->select('sum(balance_amount) sum');
            $this->db->from('user_balance_amount');
           
            $query = $this->db->get();
            $res=$query->row('sum');
            
            return $res;

    }

    //--- New Design ---//

    public function getEwalletCategories()
    {
        $categories = [];

        if ($this->MODULE_STATUS['pin_status'] == 'yes') {
            $categories = [...$categories, 'pin_purchase', 'pin_purchase_refund'];
        }
        
        $categories = [...$categories, 'admin_credit', 'admin_debit', 'fund_transfer', 'fund_transfer_fee', 'payout_request', 'payout_release_manual', 'payout_fee', 'payout_delete'];
        
        $this->load->model('register_model');
        $ewallet_status = $this->register_model->getPaymentStatus('E-wallet');
        if ($ewallet_status == 'yes') {
            $categories = [...$categories, 'registration'];
            if ($this->MODULE_STATUS['opencart_status'] == 'yes' || $this->MODULE_STATUS['repurchase_status'] == 'yes') {
                $categories = [...$categories, 'repurchase'];
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $categories = [...$categories, 'upgrade'];
            }
            if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                $categories = [...$categories, 'package_validity'];
            }
        }
        
        $bonus_categories = $this->getBonusCategories();
        
        $categories = [...$categories, ...$bonus_categories];
        
        return $categories;
    }

    public function getBonusCategories()
    {
        $bonus_list = $this->getEnabledBonuses();
        $bonus_list = array_diff($bonus_list, ['purchase_donation', 'repurchase_level_commission', 'upgrade_level_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission', 'repurchase_leg', 'upgrade_leg','matching_bonus_purchase', 'matching_bonus_upgrade']);

        return $bonus_list;
    }

    public function getEnabledBonuses()
    {
        $list = [];
        $level_commission_status = 'no';
        if (in_array($this->MLM_PLAN, ['Matrix', 'Unilevel', 'Donation']) || $this->MODULE_STATUS['sponsor_commission_status'] == 'yes') {
            $level_commission_status = 'yes';
        }
        $xup_commission_status = 'no';
        if ($this->MODULE_STATUS['xup_status'] == 'yes' && $level_commission_status == 'yes') {
            $xup_commission_status = 'yes';
            $level_commission_status = 'no';
        }
        if ($this->MODULE_STATUS['referal_status'] == 'yes') {
            $list[] = 'referral';
        }
        if ($this->MODULE_STATUS['rank_status'] == 'yes') {
            $list[] = 'rank_bonus';
        }
        if ($level_commission_status == 'yes') {
            $list[] = 'level_commission';
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes' || $this->MODULE_STATUS['opencart_status'] == 'yes') {
                $list[] = 'repurchase_level_commission';
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $list[] = 'upgrade_level_commission';
            }
        }
        if ($xup_commission_status == 'yes') {
            $list[] = 'xup_commission';
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes' || $this->MODULE_STATUS['opencart_status'] == 'yes') {
                $list[] = 'xup_repurchase_level_commission';
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $list[] = 'xup_upgrade_level_commission';
            }
        }
        if ($this->MLM_PLAN == 'Binary') {
            $list[] = 'leg';
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes' || $this->MODULE_STATUS['opencart_status'] == 'yes') {
                $list[] = 'repurchase_leg';
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $list[] = 'upgrade_leg';
            }
        }
        if ($this->MLM_PLAN == 'Stair_Step') {
            $list[] = 'stair_step';
            $list[] = 'override_bonus';
        }
        if ($this->MLM_PLAN == 'Board') {
            $list[] = 'board_commission';
        }
        if ($this->MODULE_STATUS['roi_status'] == 'yes' || $this->MODULE_STATUS['hyip_status'] == 'yes') {
            $list[] = 'daily_investment';
        }
        if ($this->MLM_PLAN == 'Donation') {
            $list[] = 'donation';
            $list[] = 'purchase_donation';
        }
        $matching_bonus_status = $this->validation_model->getCompensationConfig('matching_bonus');
        $pool_bonus_status = $this->validation_model->getCompensationConfig('pool_bonus');
        $fast_start_bonus_status = $this->validation_model->getCompensationConfig('fast_start_bonus');
        $performance_bonus_status = $this->validation_model->getCompensationConfig('performance_bonus');
        if ($matching_bonus_status == 'yes') {
            $list[] = 'matching_bonus';
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes' || $this->MODULE_STATUS['opencart_status'] == 'yes') {
                $list[] = 'matching_bonus_purchase';
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $list[] = 'matching_bonus_upgrade';
            }
        }
        if ($pool_bonus_status == 'yes') {
            $list[] = 'pool_bonus';
        }
        if ($fast_start_bonus_status == 'yes') {
            $list[] = 'fast_start_bonus';
        }
        if ($performance_bonus_status == 'yes') {
            $performance_bonus_types = $this->getPerformanceBonusTypes();
            foreach ($performance_bonus_types as $v) {
                $list[] = $v;
            }
        }
        $list[] = 'rank_promo_bonus';
        $list[] = 'company_bonus';
        $list[] = 'founder_bonus';

        return $list;
    }

    public function getEwalletOverview($from_date, $to_date)
    {
        if ($from_date) {
            $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
        }
        if ($to_date) {
            $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
        }

        $wallet_details = [];
        $amount_types = [];
        $credit_amount_types = [];
        $debit_amount_types = [];

        if ($this->MODULE_STATUS['pin_status'] == 'yes') {
            $amount_types = [...$amount_types, 'pin_purchase', 'pin_purchase_refund', 'pin_purchase_delete'];
            $credit_amount_types = [...$credit_amount_types, 'pin_purchase_refund', 'pin_purchase_delete'];
            $debit_amount_types = [...$debit_amount_types, 'pin_purchase'];
        }

        $amount_types = [...$amount_types, 'admin_credit', 'admin_debit', 'user_credit', 'user_debit', 'payout_request', 'payout_release_manual', 'payout_delete', 'payout_inactive', 'withdrawal_cancel'];
        $credit_amount_types = [...$credit_amount_types, 'admin_credit', 'user_credit', 'payout_delete', 'payout_inactive', 'withdrawal_cancel'];
        $debit_amount_types = [...$debit_amount_types, 'admin_debit', 'user_debit', 'payout_request', 'payout_release_manual'];
        
        $this->load->model('register_model');
        $ewallet_status = $this->register_model->getPaymentStatus('E-wallet');
        if ($ewallet_status == 'yes') {
            $amount_types = [...$amount_types, 'registration'];
            $debit_amount_types = [...$debit_amount_types, 'registration'];

            if ($this->MODULE_STATUS['opencart_status'] == 'yes' || $this->MODULE_STATUS['repurchase_status'] == 'yes') {
                $amount_types = [...$amount_types, 'repurchase'];
                $debit_amount_types = [...$debit_amount_types, 'repurchase'];
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $amount_types = [...$amount_types, 'upgrade'];
                $debit_amount_types = [...$debit_amount_types, 'upgrade'];
            }
            if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                $debit_amount_types = [...$debit_amount_types, 'package_validity'];
            }
        }

        $enabled_bonus_list = $this->getEnabledBonuses();

        $amount_types = [...$amount_types, ...$enabled_bonus_list];
        $credit_amount_types = [...$credit_amount_types, ...$enabled_bonus_list];
        $combined_types = $this->getCombinedAmountTypes();
        foreach ($amount_types as $type) {
            if (in_array($type, $credit_amount_types)) {
                $wallet_details[$type] = [
                    'type' => 'credit',
                    'amount' => 0
                ];
            } elseif (in_array($type, $debit_amount_types)) {
                $wallet_details[$type] = [
                    'type' => 'debit',
                    'amount' => 0
                ];
            }
            if (in_array($type, array_keys($combined_types))) {
                $wallet_details[$combined_types[$type]] = $wallet_details[$type];
                unset($wallet_details[$type]);
            }
        }
        
        $this->db->select("SUM(IF(ewallet_type = 'commission', amount-purchase_wallet, amount)) as total", false);
        $this->db->select('amount_type,type');
        $this->db->where_in('amount_type', $amount_types);
        if ($from_date) {
            $this->db->where("date_added >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_added <=", $to_date);
        }
        $this->db->group_by(['amount_type', 'type']);
        $query = $this->db->get('ewallet_history');
        foreach ($query->result_array() as $row) {
            if (in_array($row['amount_type'], array_keys($combined_types))) {
                $row['amount_type'] = $combined_types[$row['amount_type']];
                $wallet_details[$row['amount_type']]['type'] = $row['type'];
            }
            $wallet_details[$row['amount_type']]['amount'] += $row['total'];
        }
        
        $this->db->select_sum('transaction_fee');
        $this->db->select('amount_type');
        $this->db->where_in('amount_type', ['payout_request', 'user_credit']);
        $this->db->group_by('amount_type');
        $query = $this->db->get('ewallet_history');
        foreach ($query->result_array() as $row) {
            if ($row['amount_type'] == 'user_credit') {
                $wallet_details['fund_transfer_fee'] = [
                    'type' => 'debit',
                    'amount' => $row['transaction_fee']
                ];
            }
            if ($row['amount_type'] == 'payout_request') {
                $wallet_details['payout_fee'] = [
                    'type' => 'debit',
                    'amount' => $row['transaction_fee']
                ];
            }
        }
        
        return $wallet_details;
    }

    public function getCombinedAmountTypes()
    {
        $amount_types = [
            'pin_purchase_delete' => 'pin_purchase_refund',
            'payout_inactive' => 'payout_delete',
            'withdrawal_cancel' => 'payout_delete',
            'repurchase_level_commission' => 'level_commission',
            'upgrade_level_commission' => 'level_commission',
            'xup_repurchase_level_commission' => 'xup_commission',
            'xup_upgrade_level_commission' => 'xup_commission',
            'repurchase_leg' => 'leg',
            'upgrade_leg' => 'leg',
            'matching_bonus_purchase' => 'matching_bonus',
            'matching_bonus_upgrade' => 'matching_bonus',
            'purchase_donation' => 'donation',
        ];

        return $amount_types;
    }

    public function getEwalletOverviewTotal($user_id = "")
    {
        $wallet_total = [
            'credit' => 0,
            'debit' => 0
        ];

        $amount_types = [];

        if ($this->MODULE_STATUS['pin_status'] == 'yes') {
            $amount_types = [...$amount_types, 'pin_purchase', 'pin_purchase_refund', 'pin_purchase_delete'];
        }

        $amount_types = [...$amount_types, 'admin_credit', 'admin_debit', 'user_credit', 'user_debit', 'payout_request', 'payout_release_manual', 'payout_delete', 'payout_inactive', 'withdrawal_cancel'];
        
        $this->load->model('register_model');
        $ewallet_status = $this->register_model->getPaymentStatus('E-wallet');
        if ($ewallet_status == 'yes') {
            $amount_types = [...$amount_types, 'registration'];

            if ($this->MODULE_STATUS['opencart_status'] == 'yes' || $this->MODULE_STATUS['repurchase_status'] == 'yes') {
                $amount_types = [...$amount_types, 'repurchase'];
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $amount_types = [...$amount_types, 'upgrade'];
            }
            if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                $amount_types = [...$amount_types, 'package_validity'];
            }
        }

        $enabled_bonus_list = $this->getEnabledBonuses();

        $amount_types = [...$amount_types, ...$enabled_bonus_list];

        $this->db->select("SUM(IF(ewallet_type = 'commission', amount-purchase_wallet, amount)) as total", false);
        $this->db->select('type');
        $this->db->where_in('amount_type', $amount_types);
        if($user_id) {
            $this->db->where('user_id', $user_id);
        }
        $this->db->group_by('type');
        $query = $this->db->get('ewallet_history');
        foreach ($query->result_array() as $row) {
            if ($row['type'] == 'credit') {
                $wallet_total['credit'] = $row['total'];
            }
            if ($row['type'] == 'debit') {
                $wallet_total['debit'] = $row['total'];
            }
        }

        $this->db->select_sum('transaction_fee');
        $this->db->where_in('amount_type', ['payout_request', 'user_credit']);
        if($user_id) {
            $this->db->where('user_id', $user_id);
        }
        $query = $this->db->get('ewallet_history');
        $wallet_total['debit'] += $query->row_array()['transaction_fee'] ?? 0;
        
        return $wallet_total;
    }

    public function getEwalletTransactionsCount($user_id, $type, $category, $from_date, $to_date)
    {
        if ($from_date) {
            $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
        }
        if ($to_date) {
            $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
        }

        $split_types = $this->getSplitAmountTypes();

        if (!empty($user_id)) {
            $this->db->where_in('user_id', $user_id);
        }
        if ($from_date) {
            $this->db->where("date_added >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_added <=", $to_date);
        }
        if (!empty($type)) {
            $this->db->where_in('type', $type);
        }
        if (!empty($category)) {
            $amount_types = [];
            foreach ($category as $cat) {
                if (isset($split_types[$cat])) {
                    $amount_types = [...$amount_types, ...$split_types[$cat]];
                } else {
                    $amount_types = [...$amount_types, $cat];
                }
            }
            $this->db->where_in('amount_type', $amount_types);
        }
        $this->db->where('amount_type !=', 'payout_release');
        $count = $this->db->count_all_results('ewallet_history');

        if (empty($type)) {
            $type = ['credit', 'debit'];
        }
        if (in_array('debit', $type) && (empty($category) || in_array('fund_transfer_fee', $category))) {
            $this->db->where('amount_type', 'user_debit');
            $this->db->where("transaction_fee >", 0);
            if (!empty($user_id)) {
                $this->db->where_in('user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("date_added >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date_added <=", $to_date);
            }
            $count += $this->db->count_all_results('ewallet_history');
        }
        if (in_array('debit', $type) && (empty($category) || in_array('payout_fee', $category))) {
            $this->db->where_in('amount_type', ['payout_request', 'payout_release_manual']);
            $this->db->where("transaction_fee >", 0);
            if (!empty($user_id)) {
                $this->db->where_in('user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("date_added >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date_added <=", $to_date);
            }
            $count += $this->db->count_all_results('ewallet_history');
        }
        
       return $count;
    }

    public function getSplitAmountTypes()
    {
        $amount_types = [
            'pin_purchase_refund' => ['pin_purchase_refund', 'pin_purchase_delete'],
            'payout_delete' => ['payout_delete', 'payout_inactive', 'withdrawal_cancel'],
            'level_commission' => ['level_commission', 'repurchase_level_commission', 'upgrade_level_commission'],
            'xup_commission' => ['xup_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission'],
            'leg' => ['leg', 'repurchase_leg', 'upgrade_leg'],
            'matching_bonus' => ['matching_bonus', 'matching_bonus_purchase', 'matching_bonus_upgrade'],
            'donation' => ['donation', 'purchase_donation'],
            'fund_transfer' => ['user_credit', 'user_debit'],
        ];

        return $amount_types;
    }

    public function getEwalletTransactions($user_id, $type, $category, $from_date, $to_date, $filter)
    {
        if ($from_date) {
            $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
        }
        if ($to_date) {
            $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
        }

        $split_types = $this->getSplitAmountTypes();

        $this->db->select("IF(ewallet_type = 'commission', amount-purchase_wallet, amount) as amount", false);
        $this->db->select('amount_type');
        $this->db->select("type,date_added,user_id");
        $this->db->select('id');
        $this->db->from('ewallet_history');
        if (!empty($user_id)) {
            $this->db->where_in('user_id', $user_id);
        }
        if ($from_date) {
            $this->db->where("date_added >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_added <=", $to_date);
        }
        if (!empty($type)) {
            $this->db->where_in('type', $type);
        }
        if (!empty($category)) {
            $amount_types = [];
            foreach ($category as $cat) {
                if (isset($split_types[$cat])) {
                    $amount_types = [...$amount_types, ...$split_types[$cat]];
                } else {
                    $amount_types = [...$amount_types, $cat];
                }
            }
            $this->db->where_in('amount_type', $amount_types);
        }
        $this->db->where('amount_type !=', 'payout_release');
        $quey_list[] = $this->db->get_compiled_select();

        if (empty($type)) {
            $type = ['credit', 'debit'];
        }
        if (in_array('debit', $type) && (empty($category) || in_array('fund_transfer_fee', $category))) {
            $this->db->select("transaction_fee amount,'fund_transfer_fee' amount_type,type,date_added,user_id");
            $this->db->select('id');
            $this->db->where('amount_type', 'user_debit');
            $this->db->where("transaction_fee >", 0);
            if (!empty($user_id)) {
                $this->db->where_in('user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("date_added >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date_added <=", $to_date);
            }
            $quey_list[] = $this->db->get_compiled_select('ewallet_history');
        }
        if (in_array('debit', $type) && (empty($category) || in_array('payout_fee', $category))) {
            $this->db->select("transaction_fee amount,'payout_fee' amount_type,'debit' type,date_added,user_id");
            $this->db->select('id');
            $this->db->where_in('amount_type', ['payout_request', 'payout_release_manual']);
            $this->db->where("transaction_fee >", 0);
            if (!empty($user_id)) {
                $this->db->where_in('user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("date_added >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date_added <=", $to_date);
            }
            $quey_list[] = $this->db->get_compiled_select('ewallet_history');
        }
        
        $query = implode(" UNION ALL ", $quey_list);
        $dbprefix = $this->db->dbprefix;
        $res = $this->db->query("SELECT t.*,f.user_name, f.delete_status,CONCAT(u.user_detail_name,u.user_detail_second_name) full_name,u.user_photo FROM ({$query}) t LEFT JOIN {$dbprefix}ft_individual f ON (t.user_id = f.id) LEFT JOIN {$dbprefix}user_details u ON (u.user_detail_refid = f.id) ORDER BY {$filter['order']} {$filter['direction']}, amount_type, t.id LIMIT {$filter['start']}, {$filter['limit']}", false);

        return $res->result_array();
    }

public function getEwalletTransactionsnew($user_id, $type, $category, $from_date, $to_date)
    {
        if ($from_date) {
            $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
        }
        if ($to_date) {
            $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
        }

        $split_types = $this->getSplitAmountTypes();

        $this->db->select("IF(ewallet_type = 'commission', amount-purchase_wallet, amount) as amount", false);
        $this->db->select('amount_type');
        $this->db->select("type,date_added,user_id");
        $this->db->select('id');
        $this->db->from('ewallet_history');
        if (!empty($user_id)) {
            $this->db->where_in('user_id', $user_id);
        }
        if ($from_date) {
            $this->db->where("date_added >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_added <=", $to_date);
        }
        if (!empty($type)) {
            $this->db->where_in('type', $type);
        }
        if (!empty($category)) {
            $amount_types = [];
            foreach ($category as $cat) {
                if (isset($split_types[$cat])) {
                    $amount_types = [...$amount_types, ...$split_types[$cat]];
                } else {
                    $amount_types = [...$amount_types, $cat];
                }
            }
            $this->db->where_in('amount_type', $amount_types);
        }
        $this->db->where('amount_type !=', 'payout_release');
        $quey_list[] = $this->db->get_compiled_select();

        if (empty($type)) {
            $type = ['credit', 'debit'];
        }
        if (in_array('debit', $type) && (empty($category) || in_array('fund_transfer_fee', $category))) {
            $this->db->select("transaction_fee amount,'fund_transfer_fee' amount_type,type,date_added,user_id");
            $this->db->select('id');
            $this->db->where('amount_type', 'user_debit');
            $this->db->where("transaction_fee >", 0);
            if (!empty($user_id)) {
                $this->db->where_in('user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("date_added >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date_added <=", $to_date);
            }
            $quey_list[] = $this->db->get_compiled_select('ewallet_history');
        }
        if (in_array('debit', $type) && (empty($category) || in_array('payout_fee', $category))) {
            $this->db->select("transaction_fee amount,'payout_fee' amount_type,'debit' type,date_added,user_id");
            $this->db->select('id');
            $this->db->where_in('amount_type', ['payout_request', 'payout_release_manual']);
            $this->db->where("transaction_fee >", 0);
            if (!empty($user_id)) {
                $this->db->where_in('user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("date_added >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date_added <=", $to_date);
            }
            $quey_list[] = $this->db->get_compiled_select('ewallet_history');
        }
        
        $query = implode(" UNION ALL ", $quey_list);
        $dbprefix = $this->db->dbprefix;
        $res = $this->db->query("SELECT t.*,f.user_name, f.delete_status,CONCAT(u.user_detail_name,u.user_detail_second_name) full_name,u.user_photo FROM ({$query}) t LEFT JOIN {$dbprefix}ft_individual f ON (t.user_id = f.id) LEFT JOIN {$dbprefix}user_details u ON (u.user_detail_refid = f.id) ");



        return $res->result_array();
    }


  public function getEwalletBalanceCount($user_id)
    {
        if (!empty($user_id)) {
            $this->db->where_in('id', $user_id);
        }
        return $this->db->count_all_results('ft_individual');
    }

    public function getEwalletBalance($user_id, $filter)
    {
        $this->db->select("fi.user_name,CONCAT(ud.user_detail_name,' ',ud.user_detail_second_name) full_name,uba.balance_amount amount,ud.user_photo");
        $this->db->from("ft_individual as fi");
        $this->db->join("user_details as ud", "ud.user_detail_refid = fi.id");
        $this->db->join("user_balance_amount as uba", "uba.user_id = ud.user_detail_refid");
        if (!empty($user_id)) {
            $this->db->where_in('fi.id', $user_id);
        }
        $this->db->order_by($filter['order'], $filter['direction']);
        $this->db->limit($filter['limit'], $filter['start']);
        $res = $this->db->get();
        return $res->result_array();
    }

    public function getBusinessOverviewTotal()
    {
        $wallet_total = [
            'income' => 0,
            'bonus' => 0,
            'paid' => 0,
            'pending' => 0
        ];
        
        $this->db->select_sum('reg_amount');
        $this->db->select_sum('product_amount');
        $query = $this->db->get('infinite_user_registration_details');
     
        $joining_fee = $query->row_array()['reg_amount'] ?? 0;
        $package_amount = $query->row_array()['product_amount'] ?? 0;
        
        if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
            $order_types = ['register', 'purchase'];
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $order_types[] = 'upgrade';
            }
            if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                $order_types[] = 'renewal';
            }
            $this->db->select('SUM(op.total) total');
            $this->db->from('oc_order o');
            $this->db->join('oc_order_product op', 'o.order_id = op.order_id');
            $this->db->where('o.order_status_id', 5);
            $query = $this->db->get();
            $wallet_total['income'] += $query->row_array()['total'] ?? 0;
        } else {
            $wallet_total['income'] += $joining_fee;
            if ($this->MODULE_STATUS['product_status'] == 'yes') {
                $wallet_total['income'] += $package_amount;
            }
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes') {
                $this->db->select_sum('total_amount');
                $this->db->where('order_status', 'confirmed');
                $query = $this->db->get('repurchase_order');
                $purchase_amount = $query->row_array()['total_amount'] ?? 0;
                $wallet_total['income'] += $purchase_amount;
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $this->db->select_sum('amount');
                $query = $this->db->get('upgrade_sales_order');
                $purchase_amount = $query->row_array()['amount'] ?? 0;
                $wallet_total['income'] += $purchase_amount;
            }
            if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                $this->db->select_sum('total_amount');
                $query = $this->db->get('package_validity_extend_history');
                $membership_reactivation_amount = $query->row_array()['total_amount'] ?? 0;
                $wallet_total['income'] += $membership_reactivation_amount;
            }
        }

        $this->db->select_sum('trans_fee');
        $this->db->where('amount_type', 'user_credit');
        $query = $this->db->get('fund_transfer_details');
        $fund_transfer_fee = $query->row_array()['trans_fee'] ?? 0;
        $wallet_total['income'] += $fund_transfer_fee;

        $this->db->select_sum('payout_fee');
        $query = $this->db->get('amount_paid');
        $wallet_total['income'] += $query->row_array()['payout_fee'] ?? 0;
        
        $enabled_bonus_list = $this->getEnabledBonuses();

        $this->db->select("SUM(service_charge) total");
        $this->db->where_in('amount_type', $enabled_bonus_list);
        $query = $this->db->get('leg_amount');
        $commission_charge = $query->row_array()['total'] ?? 0;
        $wallet_total['income'] += $commission_charge;

        $this->db->select('SUM(amount_payable) total');
        $this->db->where_in('amount_type', $enabled_bonus_list);
        $query = $this->db->get('leg_amount');
        $commission_amount = $query->row_array()['total'] ?? 0;
        $wallet_total['bonus'] += $commission_amount;
        
        $this->db->select_sum('paid_amount');
        $this->db->where('paid_type', 'released');
        $this->db->where('paid_status', 'yes');
        $query = $this->db->get('amount_paid');
        $payout_approved_paid = $query->row_array()['paid_amount'] ?? 0;
        $wallet_total['paid'] += $payout_approved_paid;

        $this->db->select_sum('paid_amount');
        $this->db->where('paid_type', 'released');
        $this->db->where('paid_status !=', 'yes');
        $this->db->where('payment_method','bank');
        $query = $this->db->get('amount_paid');
        $payout_approved_pending = $query->row_array()['paid_amount'] ?? 0;
        
        $this->db->select_sum('requested_amount_balance');
        $this->db->where('status', 'pending');
        $query = $this->db->get('payout_release_requests');
        $payout_requests_pending = $query->row_array()['requested_amount_balance'] ?? 0;
        $wallet_total['pending'] += ($payout_requests_pending + $payout_approved_pending);
        
        return $wallet_total;
    }

    public function getBusinessOverview($from_date, $to_date)
    {
        if ($from_date) {
            $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
        }
        if ($to_date) {
            $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
        }

        $wallet_details = [];
        $this->db->select_sum('reg_amount');
        $this->db->select_sum('product_amount');
        if ($from_date) {
            $this->db->where("reg_date >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("reg_date <=", $to_date);
        }
        $query = $this->db->get('infinite_user_registration_details');
     
        $joining_fee = $query->row_array()['reg_amount'] ?? 0;
        $package_amount = $query->row_array()['product_amount'] ?? 0;
       
        $wallet_details['joining_fee'] = [
            'type' => 'income',
            'amount' => $joining_fee
        ];

        if ($this->MODULE_STATUS['product_status'] == 'yes') {
            $wallet_details['register'] = [
                'type' => 'income',
                'amount' => $package_amount
            ];
        }
        
        if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
            unset($wallet_details['joining_fee']);
            
            $order_types = ['register', 'purchase'];
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $order_types[] = 'upgrade';
            }
            if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                $order_types[] = 'renewal';
            }
            $this->db->select('SUM(op.total) total');
            $this->db->select('o.order_type');
            $this->db->from('oc_order o');
            $this->db->join('oc_order_product op', 'o.order_id = op.order_id');
            $this->db->where_in('o.order_type', $order_types);
            $this->db->where('o.order_status_id', 5);
            if ($from_date) {
                $this->db->where("o.date_added >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("o.date_added <=", $to_date);
            }
            $this->db->group_by('o.order_type');
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                $wallet_details[$row['order_type']] = [
                    'type' => 'income',
                    'amount' => $row['total']
                ];
            }
        } else {
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes') {
                $this->db->select_sum('total_amount');
                $this->db->where('order_status', 'confirmed');
                if ($from_date) {
                    $this->db->where("order_date >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("order_date <=", $to_date);
                }
                $query = $this->db->get('repurchase_order');
                $purchase_amount = $query->row_array()['total_amount'] ?? 0;

                $wallet_details['purchase'] = [
                    'type' => 'income',
                    'amount' => $purchase_amount
                ];
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $this->db->select_sum("amount");
                if ($from_date) {
                    $this->db->where("date_added >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("date_added <=", $to_date);
                }
                $query = $this->db->get('upgrade_sales_order');
                $purchase_amount = $query->row_array()['amount'] ?? 0;

                $wallet_details['upgrade'] = [
                    'type' => 'income',
                    'amount' => $purchase_amount
                ];
            }
            if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                $this->db->select_sum('total_amount');
                if ($from_date) {
                    $this->db->where("date_submitted >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("date_submitted <=", $to_date);
                }
                $query = $this->db->get('package_validity_extend_history');
                $membership_reactivation_amount = $query->row_array()['total_amount'] ?? 0;

                $wallet_details['renewal'] = [
                    'type' => 'income',
                    'amount' => $membership_reactivation_amount
                ];
            }
        }

        $this->db->select_sum('trans_fee');
        $this->db->where('amount_type', 'user_credit');
        if ($from_date) {
            $this->db->where("date >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date <=", $to_date);
        }
        $query = $this->db->get('fund_transfer_details');
        $fund_transfer_fee = $query->row_array()['trans_fee'] ?? 0;
        $wallet_details['fund_transfer_fee'] = [
            'type' => 'income',
            'amount' => $fund_transfer_fee
        ];

        $this->db->select_sum('payout_fee');
        if ($from_date) {
            $this->db->where("paid_date >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("paid_date <=", $to_date);
        }
        $query = $this->db->get('amount_paid');
        $payout_fee = $query->row_array()['payout_fee'] ?? 0;
        $wallet_details['payout_fee'] = [
            'type' => 'income',
            'amount' => $payout_fee
        ];
        
        $enabled_bonus_list = $this->getEnabledBonuses();
        $combined_types = $this->getCombinedAmountTypes();

        $this->db->select("SUM(service_charge) total");
        $this->db->where_in('amount_type', $enabled_bonus_list);
        if ($from_date) {
            $this->db->where("date_of_submission >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_of_submission <=", $to_date);
        }
        $query = $this->db->get('leg_amount');
        $commission_charge = $query->row_array()['total'] ?? 0;
        $wallet_details['commission_charge'] = [
            'type' => 'income',
            'amount' => $commission_charge
        ];
        
        foreach ($enabled_bonus_list as $bonus) {
            $wallet_details[$bonus] = [
                'type' => 'bonus',
                'amount' => 0
            ];
            if (in_array($bonus, array_keys($combined_types))) {
                $wallet_details[$combined_types[$bonus]] = $wallet_details[$bonus];
                unset($wallet_details[$bonus]);
            }
        }
        
        $this->db->select('amount_type,SUM(amount_payable) total');
        $this->db->group_by('amount_type');
        $this->db->where_in('amount_type', $enabled_bonus_list);
        if ($from_date) {
            $this->db->where("date_of_submission >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_of_submission <=", $to_date);
        }
        $query = $this->db->get('leg_amount');
        foreach ($query->result_array() as $row) {
            if (in_array($row['amount_type'], array_keys($combined_types))) {
                $row['amount_type'] = $combined_types[$row['amount_type']];
            }
            $wallet_details[$row['amount_type']]['amount'] += $row['total'];
        }
        
        $this->db->select_sum('paid_amount');
        $this->db->where('paid_type', 'released');
        $this->db->where('paid_status', 'yes');
        if ($from_date) {
            $this->db->where("paid_date >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("paid_date <=", $to_date);
        }
        $query = $this->db->get('amount_paid');
        $payout_approved_paid = $query->row_array()['paid_amount'] ?? 0;
        $wallet_details['paid'] = [
            'type' => 'paid',
            'amount' => $payout_approved_paid
        ];

        $this->db->select_sum('paid_amount');
        $this->db->where('paid_type', 'released');
        $this->db->where('paid_status !=', 'yes');
        $this->db->where('payment_method','bank');
        if ($from_date) {
            $this->db->where("paid_date >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("paid_date <=", $to_date);
        }
        $query = $this->db->get('amount_paid');
        $payout_approved_pending = $query->row_array()['paid_amount'] ?? 0;
        
        $this->db->select_sum('requested_amount_balance');
        $this->db->where('status', 'pending');
        if ($from_date) {
            $this->db->where("requested_date >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("requested_date <=", $to_date);
        }
        $query = $this->db->get('payout_release_requests');
        $payout_requests_pending = $query->row_array()['requested_amount_balance'] ?? 0;
        $wallet_details['pending'] = [
            'type' => 'pending',
            'amount' => $payout_requests_pending + $payout_approved_pending
        ];

        return $wallet_details;

    }

    public function getIncomeCategories()
    {
        $categories = [];
        
        if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
            $categories = [...$categories, 'register', 'purchase'];
        } else {
            $categories = [...$categories, 'joining_fee'];

            if ($this->MODULE_STATUS['product_status'] == 'yes') {
                $categories = [...$categories, 'register'];
            }
            
            if ($this->MODULE_STATUS['repurchase_status'] == 'yes') {
                $categories = [...$categories, 'purchase'];
            }
            
        }
        if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
            $categories = [...$categories, 'upgrade'];
        }
        if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
            $categories = [...$categories, 'renewal'];
        }

        $categories = [...$categories, 'fund_transfer_fee', 'commission_charge', 'payout_fee'];

        return $categories;
    }

    public function getBusinessCategories()
    {
        $categories = $this->getIncomeCategories();
        
        $bonus_categories = $this->getBonusCategories();
        
        $categories = [...$categories, ...$bonus_categories, 'paid', 'pending'];
        
        return $categories;
    }

    public function getBusinessTransactionsCount($user_id, $type, $category, $from_date, $to_date)
    {
        if ($from_date) {
            $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
        }
        if ($to_date) {
            $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
        }

        if (empty($type)) {
            $type = ['income', 'bonus', 'paid', 'pending'];
        }
        
        if (empty($category)) {
            $category = $this->getBusinessCategories();
        }

        $count = 0;

        $income_categories = $this->getIncomeCategories();
        
        $bonus_categories = $this->getBonusCategories();

        $bonus_categories_db = $this->getEnabledBonuses();

        if (in_array('income', $type) && !empty(array_intersect($category, $income_categories))) {
            if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
                $order_types = array_intersect(['register', 'purchase', 'upgrade', 'renewal'], $category);
                if (!empty($order_types)) {
                    $this->db->select('o.order_id');
                    $this->db->from('oc_order o');
                    $this->db->join('oc_order_product op', 'o.order_id = op.order_id');
                    $this->db->join('ft_individual f', 'f.oc_customer_ref_id = o.customer_id');
                    $this->db->where_in('o.order_type', $order_types);
                    $this->db->where('o.order_status_id', 5);
                    if (!empty($user_id)) {
                        $this->db->where_in('f.id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("o.date_added >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("o.date_added <=", $to_date);
                    }
                    $this->db->group_by('o.order_type');
                    $count += $this->db->count_all_results();
                }
            } else {
                if (in_array('joining_fee', $category)) {
                    if (!empty($user_id)) {
                        $this->db->where_in('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("reg_date >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("reg_date <=", $to_date);
                    }
                    $count += $this->db->count_all_results('infinite_user_registration_details');
                }
                if ($this->MODULE_STATUS['product_status'] == 'yes' && in_array('register', $category)) {
                    if (!empty($user_id)) {
                        $this->db->where_in('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("reg_date >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("reg_date <=", $to_date);
                    }
                    $count += $this->db->count_all_results('infinite_user_registration_details');
                }
                if ($this->MODULE_STATUS['repurchase_status'] == 'yes' && in_array('purchase', $category)) {
                    $this->db->where('order_status', 'confirmed');
                    if (!empty($user_id)) {
                        $this->db->where_in('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("order_date >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("order_date <=", $to_date);
                    }
                    $count += $this->db->count_all_results('repurchase_order');
                }
                if ($this->MODULE_STATUS['package_upgrade'] == 'yes' && in_array('upgrade', $category)) {
                    if (!empty($user_id)) {
                        $this->db->where_in('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("date_added >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("date_added <=", $to_date);
                    }
                    $count += $this->db->count_all_results('upgrade_sales_order');
                }
                if ($this->MODULE_STATUS['subscription_status'] == 'yes' && in_array('renewal', $category)) {
                    if (!empty($user_id)) {
                        $this->db->where_in('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("date_submitted >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("date_submitted <=", $to_date);
                    }
                    $count += $this->db->count_all_results('package_validity_extend_history');
                }
            }
            if (in_array('fund_transfer_fee', $category)) {
                $this->db->where('amount_type', 'user_credit');
                $this->db->where("trans_fee >", 0);
                if (!empty($user_id)) {
                    $this->db->where_in('from_user_id', $user_id);
                }
                if ($from_date) {
                    $this->db->where("date >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("date <=", $to_date);
                }
                $count += $this->db->count_all_results('fund_transfer_details');
            }
            if (in_array('payout_fee', $category)) {
                $this->db->where("payout_fee >", 0);
                if (!empty($user_id)) {
                    $this->db->where_in('paid_user_id', $user_id);
                }
                if ($from_date) {
                    $this->db->where("paid_date >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("paid_date <=", $to_date);
                }
                $count += $this->db->count_all_results('amount_paid');
            }
            if (in_array('commission_charge', $category)) {
                $this->db->where_in('amount_type', $bonus_categories_db);
                $this->db->where('service_charge >', 0);
                if (!empty($user_id)) {
                    $this->db->where_in('user_id', $user_id);
                }
                if ($from_date) {
                    $this->db->where("date_of_submission >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("date_of_submission <=", $to_date);
                }
                $count += $this->db->count_all_results('leg_amount');
            }
        }

        if (in_array('bonus', $type) && !empty(array_intersect($category, $bonus_categories))) {
            $split_types = $this->getSplitAmountTypes();
            $amount_types = [];
            foreach ($category as $cat) {
                if (isset($split_types[$cat])) {
                    $amount_types = [...$amount_types, ...$split_types[$cat]];
                } else {
                    $amount_types = [...$amount_types, $cat];
                }
            }
            $this->db->where_in('amount_type', $amount_types);
            if (!empty($user_id)) {
                $this->db->where_in('user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("date_of_submission >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date_of_submission <=", $to_date);
            }
            $count += $this->db->count_all_results('leg_amount');
        }

        if (in_array('paid', $type) && in_array('paid', $category)) {
            $this->db->where('paid_type', 'released');
            $this->db->where('paid_status', 'yes');
            if (!empty($user_id)) {
                $this->db->where_in('paid_user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("paid_date >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("paid_date <=", $to_date);
            }
            $count += $this->db->count_all_results('amount_paid');
        }

        if (in_array('pending', $type) && in_array('pending', $category)) {
            $this->db->where('paid_type', 'released');
            $this->db->where('paid_status !=', 'yes');
            $this->db->where('payment_method','bank');
            if (!empty($user_id)) {
                $this->db->where_in('paid_user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("paid_date >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("paid_date <=", $to_date);
            }
            $count += $this->db->count_all_results('amount_paid');

            $this->db->where('status', 'pending');
            if (!empty($user_id)) {
                $this->db->where_in('requested_user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("requested_date >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("requested_date <=", $to_date);
            }
            $count += $this->db->count_all_results('payout_release_requests');
        }
        
        return $count;
    }
    public function getBusinessTransactions($user_id, $type, $category, $from_date, $to_date, $filter)
    {
        if ($from_date) {
            $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
        }
        if ($to_date) {
            $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
        }

        if (empty($type)) {
            $type = ['income', 'bonus', 'paid', 'pending'];
        }
        
        if (empty($category)) {
            $category = $this->getBusinessCategories();
        }

        $query_list = [];

        $income_categories = $this->getIncomeCategories();
        
        $bonus_categories = $this->getBonusCategories();

        $bonus_categories_db = $this->getEnabledBonuses();

        if (in_array('income', $type) && !empty(array_intersect($category, $income_categories))) {
            if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
                $order_types = array_intersect(['register', 'purchase', 'upgrade', 'renewal'], $category);
                if (!empty($order_types)) {
                    $this->db->select('SUM(op.total) amount');
                    $this->db->select("o.order_type amount_type,'income' type,o.date_added,f.id user_id", false);
                    $this->db->from('oc_order o');
                    $this->db->join('oc_order_product op', 'o.order_id = op.order_id');
                    $this->db->join('ft_individual f', 'f.oc_customer_ref_id = o.customer_id');
                    $this->db->where_in('o.order_type', $order_types);
                    $this->db->where('o.order_status_id', 5);
                    if (!empty($user_id)) {
                        $this->db->where_in('f.id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("o.date_added >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("o.date_added <=", $to_date);
                    }
                    $this->db->group_by('o.order_type');
                    $query_list[] = $this->db->get_compiled_select();
                }
            } else {
                if (in_array('joining_fee', $category)) {
                    $this->db->select("reg_amount amount,'joining_fee' amount_type,'income' type,reg_date date_added,user_id", false);
                    if (!empty($user_id)) {
                        $this->db->where_in('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("reg_date >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("reg_date <=", $to_date);
                    }
                    $query_list[] = $this->db->get_compiled_select('infinite_user_registration_details');
                }
                if ($this->MODULE_STATUS['product_status'] == 'yes' && in_array('register', $category)) {
                    $this->db->select("product_amount amount,'register' amount_type,'income' type,reg_date date_added,user_id", false);
                    if (!empty($user_id)) {
                        $this->db->where_in('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("reg_date >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("reg_date <=", $to_date);
                    }
                    $query_list[] = $this->db->get_compiled_select('infinite_user_registration_details');
                }
                if ($this->MODULE_STATUS['repurchase_status'] == 'yes' && in_array('purchase', $category)) {
                    $this->db->select("total_amount amount,'purchase' amount_type,'income' type,order_date date_added,user_id", false);
                    $this->db->where('order_status', 'confirmed');
                    if (!empty($user_id)) {
                        $this->db->where_in('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("order_date >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("order_date <=", $to_date);
                    }
                    $query_list[] = $this->db->get_compiled_select('repurchase_order');
                }
                if ($this->MODULE_STATUS['package_upgrade'] == 'yes' && in_array('upgrade', $category)) {
                    $this->db->select("amount,'upgrade' amount_type,'income' type,date_added,user_id", false);
                    if (!empty($user_id)) {
                        $this->db->where_in('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("date_added >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("date_added <=", $to_date);
                    }
                    $query_list[] = $this->db->get_compiled_select('upgrade_sales_order');
                }
                if ($this->MODULE_STATUS['subscription_status'] == 'yes' && in_array('renewal', $category)) {
                    $this->db->select("total_amount amount,'renewal' amount_type,'income' type,date_submitted date_added,user_id", false);
                    if (!empty($user_id)) {
                        $this->db->where_in('user_id', $user_id);
                    }
                    if ($from_date) {
                        $this->db->where("date_submitted >=", $from_date);
                    }
                    if ($to_date) {
                        $this->db->where("date_submitted <=", $to_date);
                    }
                    $query_list[] = $this->db->get_compiled_select('package_validity_extend_history');
                }
            }
            if (in_array('fund_transfer_fee', $category)) {
                $this->db->select("trans_fee amount,'fund_transfer_fee' amount_type,'income' type,date date_added,from_user_id user_id");
                $this->db->where('amount_type', 'user_credit');
                $this->db->where("trans_fee >", 0);
                if (!empty($user_id)) {
                    $this->db->where_in('from_user_id', $user_id);
                }
                if ($from_date) {
                    $this->db->where("date >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("date <=", $to_date);
                }
                $query_list[] = $this->db->get_compiled_select('fund_transfer_details');
            }
            if (in_array('payout_fee', $category)) {
                $this->db->select("payout_fee amount,'payout_fee' amount_type,'income' type,paid_date date_added,paid_user_id user_id");
                $this->db->where("payout_fee >", 0);
                if (!empty($user_id)) {
                    $this->db->where_in('paid_user_id', $user_id);
                }
                if ($from_date) {
                    $this->db->where("paid_date >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("paid_date <=", $to_date);
                }
                $query_list[] = $this->db->get_compiled_select('amount_paid');
            }
            if (in_array('commission_charge', $category)) {
                $this->db->select("service_charge amount,'commission_charge' amount_type,'income' type,date_of_submission date_added,user_id");
                $this->db->where_in('amount_type', $bonus_categories_db);
                $this->db->where('service_charge >', 0);
                if (!empty($user_id)) {
                    $this->db->where_in('user_id', $user_id);
                }
                if ($from_date) {
                    $this->db->where("date_of_submission >=", $from_date);
                }
                if ($to_date) {
                    $this->db->where("date_of_submission <=", $to_date);
                }
                $query_list[] = $this->db->get_compiled_select('leg_amount');
            }
        }

        if (in_array('bonus', $type) && !empty(array_intersect($category, $bonus_categories))) {
            $split_types = $this->getSplitAmountTypes();
            $amount_types = [];
            foreach ($category as $cat) {
                if (isset($split_types[$cat])) {
                    $amount_types = [...$amount_types, ...$split_types[$cat]];
                } else {
                    $amount_types = [...$amount_types, $cat];
                }
            }
            $this->db->select("total_amount amount,amount_type,'bonus' type,date_of_submission date_added,user_id", false);
            $this->db->where_in('amount_type', $amount_types);
            if (!empty($user_id)) {
                $this->db->where_in('user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("date_of_submission >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("date_of_submission <=", $to_date);
            }
            $query_list[] = $this->db->get_compiled_select('leg_amount');
        }

        if (in_array('paid', $type) && in_array('paid', $category)) {
            $this->db->select("paid_amount amount,'payout_approved_paid' amount_type,'paid' type,paid_date date_added,paid_user_id user_id", false);
            $this->db->where('paid_type', 'released');
            $this->db->where('paid_status', 'yes');
            if (!empty($user_id)) {
                $this->db->where_in('paid_user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("paid_date >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("paid_date <=", $to_date);
            }
            $query_list[] = $this->db->get_compiled_select('amount_paid');
        }

        if (in_array('pending', $type) && in_array('pending', $category)) {
            $this->db->select("paid_amount amount,'payout_approved_pending' amount_type,'pending' type,paid_date date_added,paid_user_id user_id", false);
            $this->db->where('paid_type', 'released');
            $this->db->where('paid_status !=', 'yes');
            $this->db->where('payment_method','bank');
            if (!empty($user_id)) {
                $this->db->where_in('paid_user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("paid_date >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("paid_date <=", $to_date);
            }
            $query_list[] = $this->db->get_compiled_select('amount_paid');

            $this->db->select("requested_amount_balance amount,'payout_requests_pending' amount_type,'pending' type,requested_date date_added,requested_user_id user_id", false);
            $this->db->where('status', 'pending');
            if (!empty($user_id)) {
                $this->db->where_in('requested_user_id', $user_id);
            }
            if ($from_date) {
                $this->db->where("requested_date >=", $from_date);
            }
            if ($to_date) {
                $this->db->where("requested_date <=", $to_date);
            }
            $query_list[] = $this->db->get_compiled_select('payout_release_requests');
        }
        
        if (!empty($query_list)) {
            $query = implode(" UNION ALL ", $query_list);
            $dbprefix = $this->db->dbprefix;
            $res = $this->db->query("SELECT t.*,f.user_name,f.delete_status,CONCAT(u.user_detail_name,u.user_detail_second_name) full_name,u.user_photo FROM ({$query}) t LEFT JOIN {$dbprefix}ft_individual f ON (t.user_id = f.id) LEFT JOIN {$dbprefix}user_details u ON (u.user_detail_refid = f.id) ORDER BY {$filter['order']} {$filter['direction']}, amount_type LIMIT {$filter['start']}, {$filter['limit']}");

            // $res = $this->db->query("SELECT t.*,f.user_name,f.delete_status,CONCAT(u.user_detail_name,u.user_detail_second_name) full_name,u.user_photo FROM ({$query}) t LEFT JOIN {$dbprefix}ft_individual f ON (t.user_id = f.id) LEFT JOIN {$dbprefix}user_details u ON (u.user_detail_refid = f.id) ORDER BY {$filter['order']} {$filter['direction']}, amount_type, t.id LIMIT {$filter['start']}, {$filter['limit']}", false);
            
            
            return $res->result_array();
        }
        return [];
    }

    public function getUserEarnigsCount($user_id, $category = "all", $from_date = "", $to_date = "") {
        if ($from_date) {
            $from_date .= ' 00:00:00';
        }
        if ($to_date) {
            $to_date .= ' 23:59:59';
        }

        $bonus_list_all = $this->getEnabledBonusList();

        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        if ($from_date) {
            $this->db->where("date_of_submission >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_of_submission <=", $to_date);
        }
        if ($category != 'all') {
            if ($category == 'donation') {
                $this->db->where_in('amount_type', ['donation', 'purchase_donation']);
            } elseif ($category == 'level_commission') {
                $list = array_intersect(['level_commission', 'repurchase_level_commission', 'upgrade_level_commission'], $bonus_list_all);
                $this->db->where_in('amount_type', $list);
            } elseif ($category == 'xup_commission') {
                $list = array_intersect(['xup_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission'], $bonus_list_all);
                $this->db->where_in('amount_type', $list);
            } elseif ($category == 'leg') {
                $list = array_intersect(['leg', 'repurchase_leg', 'upgrade_leg'], $bonus_list_all);
                $this->db->where_in('amount_type', $list);
            } elseif ($category == 'matching_bonus') {
                $list = array_intersect(['matching_bonus', 'matching_bonus_purchase', 'matching_bonus_upgrade'], $bonus_list_all);
                $this->db->where_in('amount_type', $list);
            } else {
                $this->db->where('amount_type', $category);
            }
        }
        return $this->db->count_all_results('leg_amount');
    }

    public function getUserEarnigs($user_id, $category = "all", $from_date = "", $to_date = "", $filter = []) {
        if ($from_date) {
            $from_date .= ' 00:00:00';
        }
        if ($to_date) {
            $to_date .= ' 23:59:59';
        }

        $bonus_list_all = $this->getEnabledBonusList();
        
        $this->db->select("CASE WHEN l.amount_type = 'purchase_donation' THEN 'donation' WHEN l.amount_type = 'repurchase_leg' THEN 'leg' WHEN l.amount_type = 'upgrade_leg' THEN 'leg' WHEN l.amount_type = 'matching_bonus_purchase' THEN 'matching_bonus' WHEN l.amount_type = 'matching_bonus_upgrade' THEN 'matching_bonus' ELSE l.amount_type END AS category");
        $this->db->select("l.amount_payable amount,l.tds,l.service_charge,l.date_of_submission transaction_date,l.user_level,u.user_detail_name,u.user_detail_second_name,f.user_name");
        $this->db->from('leg_amount l');
        $this->db->join('user_details u', 'l.from_id=u.user_detail_refid', 'left');
        $this->db->join('ft_individual f', 'l.from_id=f.id', 'left');
        if ($user_id) {
            $this->db->where('l.user_id', $user_id);
        }
        if ($from_date) {
            $this->db->where("l.date_of_submission >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("l.date_of_submission <=", $to_date);
        }
        if ($category != 'all') {
            if ($category == 'donation') {
                $this->db->where_in('l.amount_type', ['donation', 'purchase_donation']);
            } elseif ($category == 'level_commission') {
                $list = array_intersect(['level_commission', 'repurchase_level_commission', 'upgrade_level_commission'], $bonus_list_all);
                $this->db->where_in('l.amount_type', $list);
            } elseif ($category == 'xup_commission') {
                $list = array_intersect(['xup_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission'], $bonus_list_all);
                $this->db->where_in('l.amount_type', $list);
            } elseif ($category == 'leg') {
                $list = array_intersect(['leg', 'repurchase_leg', 'upgrade_leg'], $bonus_list_all);
                $this->db->where_in('l.amount_type', $list);
            } elseif ($category == 'matching_bonus') {
                $list = array_intersect(['matching_bonus', 'matching_bonus_purchase', 'matching_bonus_upgrade'], $bonus_list_all);
                $this->db->where_in('l.amount_type', $list);
            } else {
                $this->db->where('l.amount_type', $category);
            }
        }
        $this->db->order_by($filter['order'], $filter['direction']);
        $this->db->limit($filter['limit'], $filter['start']);
        $query = $this->db->get(); 
        // dd($this->db->last_query());
        return $query->result_array();
    }

    /**
     * purchase wallet balance description
     * @return [array] [credit, debit]
     */
    public function purchase_wallet_balance() {
        $this->db->select('*');
        $this->db->from('purchase_wallet_history as e');
        $this->db->order_by('e.id','desc');
        $ewallet_data = $this->db->get_compiled_select();
        
        $this->db->select("SUM(IF(f.type = 'credit', f.purchase_wallet, 0)) as credit", FALSE);
        $this->db->select("SUM(IF(f.type = 'debit' AND f.amount_type != 'payout_release', f.purchase_wallet, 0)) as debit", FALSE);
        $this->db->from("($ewallet_data) as f", FALSE);
        $res = $this->db->get();
        return ($res->row_array()['credit'] - $res->row_array()['debit']);
    }

    /**
     * [get Total Commission Earned description]
     * @return [type]          [description]
     */
    public function total_commission_earned() {  
       return $this->db->select('sum(amount_payable) sum')
        ->get('leg_amount')
        ->row('sum');
    }
    public function getEwalletOverviewTotalAgent($user_id = "")
    {
        $wallet_total = [
            'credit' => 0,
            'debit' => 0
        ];

        $amount_types = [];

        if ($this->MODULE_STATUS['pin_status'] == 'yes') {
            $amount_types = [...$amount_types, 'pin_purchase', 'pin_purchase_refund', 'pin_purchase_delete'];
        }

        $amount_types = [...$amount_types, 'admin_credit', 'admin_debit', 'user_credit', 'user_debit', 'payout_request', 'payout_release_manual', 'payout_delete', 'payout_inactive', 'withdrawal_cancel'];
        
        $this->load->model('register_model');
        $ewallet_status = $this->register_model->getPaymentStatus('E-wallet');
        if ($ewallet_status == 'yes') {
            $amount_types = [...$amount_types, 'registration'];

            if ($this->MODULE_STATUS['opencart_status'] == 'yes' || $this->MODULE_STATUS['repurchase_status'] == 'yes') {
                $amount_types = [...$amount_types, 'repurchase'];
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                $amount_types = [...$amount_types, 'upgrade'];
            }
            if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                $amount_types = [...$amount_types, 'package_validity'];
            }
        }

        $enabled_bonus_list = $this->getEnabledBonuses();

        $amount_types = [...$amount_types, ...$enabled_bonus_list];
        $amount_types = [...$amount_types, 'agent_commission'];
        $this->db->select("SUM(IF(ewallet_type = 'commission', amount-purchase_wallet, amount)) as total", false);
        $this->db->select('type');
        $this->db->where_in('amount_type', $amount_types);
        if($user_id) {
            $this->db->where('user_id', $user_id);
        }
        $this->db->group_by('type');
        $query = $this->db->get('agent_wallet_history');
        foreach ($query->result_array() as $row) {
            if ($row['type'] == 'credit') {
                $wallet_total['credit'] = $row['total'];
            }
            if ($row['type'] == 'debit') {
                $wallet_total['debit'] = $row['total'];
            }
        }

        $this->db->select_sum('transaction_fee');
        $this->db->where_in('amount_type', ['payout_request', 'user_credit']);
        if($user_id) {
            $this->db->where('user_id', $user_id);
        }
        $query = $this->db->get('agent_wallet_history');
        $wallet_total['debit'] += $query->row_array()['transaction_fee'] ?? 0;
        
        return $wallet_total;
    }
    public function getEnabledBonusCategoriesAgent()
    {
        $categories = [];
        
        //$bonus_list = $this->getEnabledBonusList();
        //$bonus_list = array_diff($bonus_list, ['purchase_donation', 'repurchase_level_commission', 'upgrade_level_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission', 'repurchase_leg', 'upgrade_leg','matching_bonus_purchase', 'matching_bonus_upgrade']);

        //$categories = array_merge($categories, $bonus_list);
        $categories[]='agent_commission';
        return $categories;
    }
    public function getTotalCommissionEarnedAgent($user_id)
    {  
       $this->db->select('sum(amount) sum');
       $this->db->from('agent_wallet_history');
       $this->db->where('ewallet_type','commission');
       $this->db->where('type','credit');
       $this->db->where('user_id',$user_id);
       $query=$this->db->get();
       $res=$query->row('sum');

       return $res;

    }
    public function getAgentwalletHistoryCount($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->where('amount_type !=', 'payout_release');
        return $this->db->count_all_results('agent_wallet_history');
    }
    public function getUserAgentwalletStatement($user_id, $filter) {
        $this->db->select('e.ewallet_type,e.amount,e.amount_type,e.type,e.date_added,e.transaction_id,e.transaction_note,e.transaction_fee,e.pending_id,IF(e.pending_id IS NULL, f.user_name, p.agent_username) as from_user,e.purchase_wallet', false);
        $this->db->from('agent_wallet_history as e');
        $this->db->join('ft_individual as f', 'e.from_id = f.id', 'left');
        $this->db->join('agents as p', 'e.id = p.id', 'left');
        $this->db->where('e.user_id', $user_id);
        $this->db->where('amount_type !=', 'payout_release');
        // $this->db->order_by($filter['order'], $filter['direction']);
        $this->db->order_by('e.id');
        $this->db->limit($filter['limit'], $filter['start']);
        $res = $this->db->get();
        return $res->result_array();
    }
    public function getPreviousAgentwalletBalance($user_id, $page) {
        if(!$page) {
            return 0;
        }

        $this->db->select('*');
        $this->db->from('agent_wallet_history as e');
        $this->db->where('e.user_id', $user_id);
        $this->db->limit($page, 0);
        $ewallet_data = $this->db->get_compiled_select();

        $this->db->select("SUM(IF(f.type = 'credit', f.amount, 0)) as credit", FALSE);
        $this->db->select("SUM(IF(f.type = 'credit', f.purchase_wallet, 0)) as pwallet", FALSE);
        $this->db->select("SUM(IF(f.type = 'debit', f.amount, 0)) as debit", FALSE);
        $this->db->select("SUM(IF(f.type = 'debit', f.transaction_fee, 0)) as transaction_fee", FALSE);
        $this->db->from("($ewallet_data) as f", FALSE);
        $this->db->where('f.amount_type !=', 'payout_release');
        $res = $this->db->get();
        return ($res->row_array()['credit'] - $res->row_array()['debit'] - $res->row_array()['transaction_fee'] - $res->row_array()['pwallet']);
    }
    public function getUserAgentwalletDetailsCount($user_id, $from_date, $to_date, $type = []) {
        return 0;
        if ($from_date) {
            $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
        }
        if ($to_date) {
            $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
        }

        $details = array();
        if ($user_id != '') {
            $this->db->group_start();
            $this->db->where('to_user_id', $user_id);
            $this->db->group_end();
        }
        
        if ($from_date != '') {
            $this->db->where("date >=", $from_date);
        }
        if ($to_date != '') {
            $this->db->where("date <=", $to_date);
        }

        if(!empty($type)) {
            $this->db->where_in('amount_type', $type);
        }
        $this->db->where_not_in('amount_type', ['admin_debit', 'admin_credit']);
        $this->db->group_by('transaction_id');
        return $this->db->count_all_results('fund_transfer_details');
    }
    public function getUserAgentwalletDetails($user_id, $from_date, $to_date, $type, $page = '', $limit = '', $order_column = "date", $dir = "DESC") {
         if ($from_date) {
            $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
        }
        if ($to_date) {
            $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
        }
        $details = array();
        return $details;
        $this->db->select('from_user_id');
        $this->db->select('to_user_id');
        $this->db->select('from_user.user_name AS from_user_name');
        $this->db->select('to_user.user_name AS to_user_name');
        $this->db->select('amount');
        $this->db->select('trans_fee');
        $this->db->select('date');
        $this->db->select('transaction_id');
        $this->db->select('amount_type');
        $this->db->select('transaction_concept');
        $this->db->select('to_user_id');
        $this->db->from('fund_transfer_details');
        $this->db->join('ft_individual as from_user', 'from_user.id = fund_transfer_details.from_user_id');
        $this->db->join('ft_individual as to_user', 'to_user.id = fund_transfer_details.to_user_id');
        $this->db->limit($limit, $page);
        if ($user_id != '') {
            // $this->db->group_start();
            $this->db->where('to_user_id', $user_id);
            // $this->db->or_where('from_user_id', $user_id);
            // $this->db->group_end();
        }
        if ($from_date) {
            // $this->db->where("DATE_FORMAT(date,'%Y-%m-%d') >=", $from_date);
            $this->db->where("date >=", $from_date);
        }
        
        if ($to_date) {
            // $this->db->where("DATE_FORMAT(date,'%Y-%m-%d') <=", $to_date);
            $this->db->where("date <=", $to_date);
        }
        if (!empty($type)) {
            $this->db->where_in('amount_type', $type);
        }

        /*if (!empty($type) && count($type)==1 ) {
             foreach($type as $amount_type) {
            if($amount_type == 'debit') {
                $this->db->where_in('amount_type',"user_debit");
            }
            if($amount_type == "credit") {
               $this->db->where_in('amount_type', "user_credit");

            }
            }
        }*/
        $this->db->where_not_in('amount_type', ['admin_debit', 'admin_credit']);
        $this->db->order_by($order_column, $dir);
        // $this->db->group_by('transaction_id');
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $details[$i]['total_amount'] = $row['amount'];
            $details[$i]['date'] = $row['date'];
            $details[$i]['amount_type'] = $row['amount_type'];
            $details[$i]['trans_fee'] = $row['trans_fee'];
            $details[$i]['transaction_id'] = $row['transaction_id'];
            $details[$i]['transaction_note'] = $row['transaction_concept'];
            $details[$i]['user_name'] = $this->validation_model->IdToUserName($row['to_user_id']);
            $details[$i]['from_user_name'] = $row['from_user_name'];
            $details[$i]['to_user_name'] = $row['to_user_name'];
            $i++;
        }
        return $details;
    }
    public function getUserAgentEarningsCount($user_id, $categories, $from_date, $to_date)
    {
        if ($from_date) {
            $from_date .= ' 00:00:00';
        }
        if ($to_date) {
            $to_date .= ' 23:59:59';
        }

        $count = 0;

        $bonus_list_all = $this->getEnabledBonusList();

        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        if ($from_date) {
            $this->db->where("date_added >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("date_added <=", $to_date);
        }
        $this->db->where('ewallet_type','commission');
        $this->db->where('type','credit');
        if(!empty($categories)) {
            $this->db->group_start();
                if(in_array('donation', $categories)) {
                    $this->db->or_where_in('amount_type', ['donation', 'purchase_donation']);
                }
                
                if(in_array('level_commission', $categories)) {
                    $list1 = array_intersect(['level_commission', 'repurchase_level_commission', 'upgrade_level_commission'], $bonus_list_all);
                    $this->db->or_where_in('amount_type', $list1);
                }

                if(in_array('xup_commission', $categories)) {
                    $lis2 = array_intersect(['xup_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission'], $bonus_list_all);
                    $this->db->or_where_in('amount_type', $list2);
                }

                if(in_array('leg', $categories)) {
                    $list3 = array_intersect(['leg', 'repurchase_leg', 'upgrade_leg'], $bonus_list_all);
                    $this->db->or_where_in('amount_type', $list3);
                }

                if(in_array('matching_bonus', $categories)) {
                    $list4 = array_intersect(['matching_bonus', 'matching_bonus_purchase', 'matching_bonus_upgrade'], $bonus_list_all);
                    $this->db->or_where_in('amount_type', $list4);
                }

                if(count(array_diff($categories, $bonus_list_all)) === 0) {
                    $this->db->or_where_in('amount_type', $categories);
                }

            $this->db->group_end();
        }
        
        $count += $this->db->count_all_results('agent_wallet_history');
        
        return $count;
    }
    public function getAgentUserEarnings($user_id, $categories, $from_date, $to_date, $filter)
    {
        if ($from_date) {
            $from_date .= ' 00:00:00';
        }
        if ($to_date) {
            $to_date .= ' 23:59:59';
        }

        $bonus_list_all = $this->getEnabledBonusList();
        
        $this->db->select("CASE WHEN l.amount_type = 'purchase_donation' THEN 'donation' WHEN l.amount_type = 'repurchase_leg' THEN 'leg' WHEN l.amount_type = 'upgrade_leg' THEN 'leg' WHEN l.amount_type = 'matching_bonus_purchase' THEN 'matching_bonus' WHEN l.amount_type = 'matching_bonus_upgrade' THEN 'matching_bonus' ELSE l.amount_type END AS category");
        $this->db->select("l.amount as total_amount , l.amount as amount_payable, l.date_added transaction_date,u.user_detail_name,u.user_detail_second_name,f.user_name");
        $this->db->from('agent_wallet_history l');
        $this->db->where('l.ewallet_type','commission');
        $this->db->where('l.type','credit');
        $this->db->join('user_details u', 'l.from_id=u.user_detail_refid', 'left');
        $this->db->join('ft_individual f', 'l.from_id=f.id', 'left');
        if ($user_id) {
            $this->db->where('l.user_id', $user_id);
        }
        if ($from_date) {
            $this->db->where("l.date_added >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("l.date_added <=", $to_date);
        }

        if(!empty($categories)) {
            $this->db->group_start();
                if(in_array('donation', $categories)) {
                    $this->db->or_where_in('amount_type', ['donation', 'purchase_donation']);
                }
                
                if(in_array('level_commission', $categories)) {
                    $list1 = array_intersect(['level_commission', 'repurchase_level_commission', 'upgrade_level_commission'], $bonus_list_all);
                    $this->db->or_where_in('amount_type', $list1);
                }

                if(in_array('xup_commission', $categories)) {
                    $lis2 = array_intersect(['xup_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission'], $bonus_list_all);
                    $this->db->or_where_in('amount_type', $list2);
                }

                if(in_array('leg', $categories)) {
                    $list3 = array_intersect(['leg', 'repurchase_leg', 'upgrade_leg'], $bonus_list_all);
                    $this->db->or_where_in('amount_type', $list3);
                }

                if(in_array('matching_bonus', $categories)) {
                    $list4 = array_intersect(['matching_bonus', 'matching_bonus_purchase', 'matching_bonus_upgrade'], $bonus_list_all);
                    $this->db->or_where_in('amount_type', $list4);
                }

                if(count(array_diff($categories, $bonus_list_all)) === 0) {
                    $this->db->or_where_in('amount_type', $categories);
                }

            $this->db->group_end();
        }

        // $this->db->order_by('date_of_submission', 'desc');
        $this->db->order_by($filter['order'], $filter['direction']);
        $this->db->limit($filter['limit'], $filter['start']);
        $quey = $this->db->get();
        return $quey->result_array();
    }

    public function getEwalletBalancenew($user_id)
    {
        $this->db->select("fi.user_name,CONCAT(ud.user_detail_name,' ',ud.user_detail_second_name) full_name,uba.balance_amount amount,ud.user_photo");
        $this->db->from("ft_individual as fi");
        $this->db->join("user_details as ud", "ud.user_detail_refid = fi.id");
        $this->db->join("user_balance_amount as uba", "uba.user_id = ud.user_detail_refid");
        if (!empty($user_id)) {
            $this->db->where_in('fi.id', $user_id);
        }
        // $this->db->order_by($filter['order'], $filter['direction']);
        // $this->db->limit($filter['limit'], $filter['start']);
        $res = $this->db->get();
        return $res->result_array();
    }
    public function ewallet_history_new($user_id) {
        $this->db->select('e.ewallet_type,e.amount,e.amount_type,e.type,e.date_added,e.transaction_id,e.transaction_note,e.transaction_fee,e.pending_id,IF(e.pending_id IS NULL, f.user_name, p.user_name) as from_user,e.purchase_wallet', false);
        $this->db->from('ewallet_history as e');
        $this->db->join('ft_individual as f', 'e.from_id = f.id', 'left');
        $this->db->join('pending_registration as p', 'e.pending_id = p.id', 'left');
        $this->db->where('e.user_id', $user_id);
        $this->db->where('amount_type !=', 'payout_release');
        $this->db->order_by('e.id');
        // $this->db->limit($filter['limit'], $filter['start']);
        // $this->db->limit($limit, $page);
        $res = $this->db->get();
        // dd($this->db->last_query());
        return $res->result_array();
    }

    public function getPreviousEwalletBalancenew($user_id) {
        // if(!$page) {
        //     return 0;
        // }

        $this->db->select('*');
        $this->db->from('ewallet_history as e');
        $this->db->where('e.user_id', $user_id);
        //$this->db->limit($page, 0);
        $ewallet_data = $this->db->get_compiled_select();

        $this->db->select("SUM(IF(f.type = 'credit', f.amount, 0)) as credit", FALSE);
        $this->db->select("SUM(IF(f.type = 'credit', f.purchase_wallet, 0)) as pwallet", FALSE);
        $this->db->select("SUM(IF(f.type = 'debit', f.amount, 0)) as debit", FALSE);
        $this->db->select("SUM(IF(f.type = 'debit', f.transaction_fee, 0)) as transaction_fee", FALSE);
        $this->db->from("($ewallet_data) as f", FALSE);
        $this->db->where('f.amount_type !=', 'payout_release');
        $res = $this->db->get();
        return ($res->row_array()['credit'] - $res->row_array()['debit'] - $res->row_array()['transaction_fee'] - $res->row_array()['pwallet']);
    }

     public function getUserEarnigsnew($user_id, $category, $from_date = "", $to_date = "") {
        // if ($from_date) {
        //     $from_date .= ' 00:00:00';
        // }
        // if ($to_date) {
        //     $to_date .= ' 23:59:59';
        // }

        $bonus_list_all = $this->getEnabledBonusList();
        
        $this->db->select("CASE WHEN l.amount_type = 'purchase_donation' THEN 'donation' WHEN l.amount_type = 'repurchase_leg' THEN 'leg' WHEN l.amount_type = 'upgrade_leg' THEN 'leg' WHEN l.amount_type = 'matching_bonus_purchase' THEN 'matching_bonus' WHEN l.amount_type = 'matching_bonus_upgrade' THEN 'matching_bonus' ELSE l.amount_type END AS category");
        $this->db->select("l.amount_payable amount,l.tds,l.service_charge,l.date_of_submission transaction_date,l.user_level,u.user_detail_name,u.user_detail_second_name,f.user_name");
        $this->db->from('leg_amount l');
        $this->db->join('user_details u', 'l.from_id=u.user_detail_refid', 'left');
        $this->db->join('ft_individual f', 'l.from_id=f.id', 'left');
        if ($user_id) {
            $this->db->where('l.user_id', $user_id);
        }
        if ($from_date) {
            $this->db->where("l.date_of_submission >=", $from_date);
        }
        if ($to_date) {
            $this->db->where("l.date_of_submission <=", $to_date);
        }
        if ($category != 'all') {
            if ($category == 'donation') {
                $this->db->where_in('l.amount_type', ['donation', 'purchase_donation']);
            } elseif ($category == 'level_commission') {
                $list = array_intersect(['level_commission', 'repurchase_level_commission', 'upgrade_level_commission'], $bonus_list_all);
                $this->db->where_in('l.amount_type', $list);
            } elseif ($category == 'xup_commission') {
                $list = array_intersect(['xup_commission', 'xup_repurchase_level_commission', 'xup_upgrade_level_commission'], $bonus_list_all);
                $this->db->where_in('l.amount_type', $list);
            } elseif ($category == 'leg') {
                $list = array_intersect(['leg', 'repurchase_leg', 'upgrade_leg'], $bonus_list_all);
                $this->db->where_in('l.amount_type', $list);
            } elseif ($category == 'matching_bonus') {
                $list = array_intersect(['matching_bonus', 'matching_bonus_purchase', 'matching_bonus_upgrade'], $bonus_list_all);
                $this->db->where_in('l.amount_type', $list);
            } else {
                $this->db->where('l.amount_type', $category);
            }
        }
        // $this->db->order_by($filter['order'], $filter['direction']);
        // $this->db->limit($filter['limit'], $filter['start']);
        $query = $this->db->get(); 
        // dd($this->db->last_query());
        return $query->result_array();
    }

    public function getagentBalanceAmount($user_id) {
        $this->db->select('balance_amount');
        $this->db->from('agent_wallet');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        foreach ($query->result() as $row)
            return $row->balance_amount;
    }

    public function getUserloginPassword($user_id) {
        $this->db->select('agent_password');
        $this->db->from('agents');
        $this->db->where('id', $user_id);
        $query = $this->db->get();
        foreach ($query->result() as $row)
            return $row->agent_password;
    }

    public function updateagentBalanceAmountDetailsFrom($from_user_id, $trans_amount) {
        $this->db->set('balance_amount', 'ROUND(balance_amount - ' . $trans_amount . ',8)', FALSE);
        $this->db->where('user_id', $from_user_id);
        $query = $this->db->update('agent_wallet');
        return $query;
    }

    //fund transfer from agent to user/admin
    public function insertagentBalAmountDetails($from_user_id, $to_user_id, $trans_amount, $amount_type = '', $transaction_concept = '', $trans_fee = '', $transaction_id = '') {
        $date = date('Y-m-d H:i:s');
        
        if ($amount_type != '') {
            $data = array(
                'from_user_id' => $from_user_id,
                'to_user_id' => $to_user_id,
                'amount' => $trans_amount,
                'date' => $date,
                'amount_type' => $amount_type,
                'transaction_concept' => $transaction_concept,
                'trans_fee' => $trans_fee,
                'transaction_id' => $transaction_id
                );
            $query = $this->db->insert('fund_transfer_details', $data);
            $ewallet_id = $this->db->insert_id();
            
            $this->validation_model->addEwalletHistory($to_user_id, $from_user_id, $ewallet_id, 'fund_transfer', $trans_amount, $amount_type, ($amount_type == 'admin_debit') ? 'debit' : 'credit', $transaction_id, $transaction_concept, $trans_fee);
        } else {
            $data = array(
                'from_user_id' => $to_user_id,
                'to_user_id' => $from_user_id,
                'amount' => $trans_amount,
                'date' => $date,
                'amount_type' => 'debit',
                'transaction_concept' => $transaction_concept,
                'trans_fee' => $trans_fee,
                'transaction_id' => $transaction_id
                );
            $query = $this->db->insert('fund_transfer_details_agent', $data);
            $ewallet_id = $this->db->insert_id();
            

            $this->validation_model->addEwalletHistory($to_user_id, 0, $ewallet_id='', 'fund_transfer', $trans_amount, 'agent_fund_transfer_credit', 'credit', $transaction_id, $transaction_concept, $trans_fee,FALSE,$from_user_id);


            $this->validation_model->addAgentwalletHistory($to_user_id, $from_user_id,$ewallet_id, 'fund_transfer', $trans_amount, 'agent_fund_transfer_debit',
            'debit', $transaction_id, $transaction_concept, $trans_fee);
        }
    }
//fund transfer to agent

public function insertFundToAgentBalAmountDetails($from_user_id, $to_user_id, $trans_amount, $amount_type = '', $transaction_concept = '', $trans_fee = '', $transaction_id = '') {
    $date = date('Y-m-d H:i:s');
    
    if ($amount_type != '') {
        $data = array(
            'from_user_id' => $from_user_id,
            'to_user_id' => $to_user_id,
            'amount' => $trans_amount,
            'date' => $date,
            'amount_type' => $amount_type,
            'transaction_concept' => $transaction_concept,
            'trans_fee' => $trans_fee,
            'transaction_id' => $transaction_id
            );
        $query = $this->db->insert('fund_transfer_details_agent', $data);
        $ewallet_id = $this->db->insert_id();
        
        // $this->validation_model->addEwalletHistory($to_user_id, $from_user_id, $ewallet_id, 'fund_transfer', $trans_amount, $amount_type, ($amount_type == 'admin_debit') ? 'debit' : 'credit', $transaction_id, $transaction_concept, $trans_fee);
        $this->validation_model->addAgentwalletHistory($to_user_id, $from_user_id, $ewallet_id='', 'fund_transfer', $trans_amount, 'agent_credit', 'credit', $transaction_id, $transaction_concept, $trans_fee);

    } else {
        $data = array(
            'from_user_id' => $from_user_id,
            'to_user_id' => $to_user_id,
            'amount' => $trans_amount,
            'date' => $date,
            'amount_type' => 'agent_credit',
            'transaction_concept' => $transaction_concept,
            'trans_fee' => $trans_fee,
            'transaction_id' => $transaction_id
            );
            
        $query = $this->db->insert('fund_transfer_details_agent', $data);
        
        $ewallet_id = $this->db->insert_id();
        
        $this->validation_model->addAgentwalletHistory($to_user_id, $from_user_id, $ewallet_id='', 'fund_transfer', $trans_amount, 'agent_credit', 'credit', $transaction_id, $transaction_concept, $trans_fee);

        // $data = array(
        //     'from_user_id' => $from_user_id,
        //     'to_user_id' => $to_user_id,
        //     'amount' => $trans_amount,
        //     'date' => $date,
        //     'amount_type' => 'agent_debit',
        //     'transaction_concept' => $transaction_concept,
        //     'trans_fee' => $trans_fee,
        //     'transaction_id' => $transaction_id
        //     );
        // $query = $this->db->insert('fund_transfer_details_agent', $data);
        // $ewallet_id = $this->db->insert_id();

        $this->validation_model->addEwalletHistory($from_user_id, 0,$ewallet_id, 'fund_transfer', $trans_amount, 'agent_debit',
        'debit', $transaction_id, $transaction_concept, $trans_fee,FALSE,$to_user_id);
    }
}
public function getAgentList() {
    $this->db->select('agent_username');
    $this->db->from('agents');
    $query = $this->db->get()->result_array();
    return $query;
}
public function isAgentNameAvailable($user_name) {
    
    $this->db->where('agent_username', $user_name);
    $count = $this->db->count_all_results('agents');
    $flag = ($count > 0);
    return $flag;
}
    public function getUserAgentwalletDetailsNew($user_id, $from_date, $to_date, $type, $page = '', $limit = '', $order_column = "date", $dir = "DESC") {
         if ($from_date) {
            $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
        }
        if ($to_date) {
            $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
        }
        $details = array();
      
        $this->db->select('from_user_id,to_user_id,amount,trans_fee,date,transaction_id,amount_type,transaction_concept');
        $this->db->from('fund_transfer_details_agent');
        $this->db->limit($limit, $page);
        if ($user_id != '') {
            $this->db->group_start();
            $this->db->where('from_user_id', $user_id);
            $this->db->or_where('to_user_id', $user_id);
            $this->db->group_end();
        }
        if ($from_date) {
            // $this->db->where("DATE_FORMAT(date,'%Y-%m-%d') >=", $from_date);
            $this->db->where("date >=", $from_date);
        }
        
        if ($to_date) {
            // $this->db->where("DATE_FORMAT(date,'%Y-%m-%d') <=", $to_date);
            $this->db->where("date <=", $to_date);
        }
        if (!empty($type)) {
            $this->db->where_in('amount_type', $type);
        }

        /*if (!empty($type) && count($type)==1 ) {
             foreach($type as $amount_type) {
            if($amount_type == 'debit') {
                $this->db->where_in('amount_type',"user_debit");
            }
            if($amount_type == "credit") {
               $this->db->where_in('amount_type', "user_credit");

            }
            }
        }*/
        $this->db->where_in('amount_type', ['debit', 'credit']);
        $this->db->order_by($order_column, $dir);
        // $this->db->group_by('transaction_id');
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $details[$i]['total_amount'] = $row['amount'];
            $details[$i]['date'] = $row['date'];
            $details[$i]['amount_type'] = $row['amount_type'];
            $details[$i]['trans_fee'] = $row['trans_fee'];
            $details[$i]['transaction_id'] = $row['transaction_id'];
            $details[$i]['transaction_note'] = $row['transaction_concept'];
            if($row['amount_type']=='credit'){
                $details[$i]['from_user_name'] = $this->validation_model->IdToAgentUserName($row['from_user_id']);
                $details[$i]['to_user_name'] = $this->validation_model->idToUsername($row['to_user_id']);
            }else{
                $details[$i]['from_user_name'] = $this->validation_model->idToUsername($row['from_user_id']);
                $details[$i]['to_user_name'] = $this->validation_model->IdToAgentUserName($row['to_user_id']);
            }
            $details[$i]['user_name'] = $this->validation_model->IdToUserName($row['to_user_id']);
            
            $i++;
        }

        return $details;
    }
    public function getUserAgentwalletDetailsCountNew($user_id, $from_date, $to_date, $type = []) {
        if ($from_date) {
            $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
        }
        if ($to_date) {
            $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
        }

        $details = array();
        if ($user_id != '') {
            $this->db->group_start();
            $this->db->where('to_user_id', $user_id);
            $this->db->or_where('from_user_id', $user_id);
            $this->db->group_end();
        }
        
        if ($from_date != '') {
            $this->db->where("date >=", $from_date);
        }
        if ($to_date != '') {
            $this->db->where("date <=", $to_date);
        }

        if(!empty($type)) {
            $this->db->where_in('amount_type', $type);
        }
        $this->db->where_not_in('amount_type', ['admin_debit', 'admin_credit']);
        $this->db->group_by('transaction_id');
        return $this->db->count_all_results('fund_transfer_details_agent');
    }

}
