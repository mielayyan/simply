<?php

class payout_model extends inf_model {

    function __construct() {

        parent::__construct();

        $this->load->model('payout_class_model');
        $this->load->model('validation_model');
        $this->load->model('register_model');
        $this->load->model('settings_model');
    }

    public function payoutWeeklyTotal($limit, $page, $from, $to, $user_id = '') {
        $row1 = array();
        if ($user_id == '') {
            $this->db->select_sum('leg_amount.total_leg', 'total_leg');
            $this->db->select_sum('total_amount', 'total_amount');
            $this->db->select_sum('amount_payable', 'amount_payable');
            $this->db->select_sum('tds', 'tds');
            $this->db->select_sum('service_charge', 'service_charge');
            $this->db->select_sum('leg_amount_carry', 'leg_amount_carry');
            $this->db->select('user_id');
            $this->db->from('leg_amount');
            $this->db->join('ft_individual AS ft', 'ft.id = leg_amount.user_id', 'INNER');
            $this->db->where("date_of_submission >=", $from);
            $this->db->where("date_of_submission <=", $to);
            $this->db->where('ft.active', 'yes');
            $this->db->group_by('user_id');
            $this->db->limit($limit, $page);

            $query = $this->db->get();
        } else {
            $this->db->select_sum('leg_amount.total_leg', 'total_leg');
            $this->db->select_sum('total_amount', 'total_amount');
            $this->db->select_sum('amount_payable', 'amount_payable');
            $this->db->select_sum('tds', 'tds');
            $this->db->select_sum('service_charge', 'service_charge');
            $this->db->select_sum('leg_amount_carry', 'leg_amount_carry');
            $this->db->select('user_id');
            $this->db->from('leg_amount');
            $this->db->join('ft_individual AS ft', 'ft.id = leg_amount.user_id', 'INNER');
            $this->db->where("date_of_submission >=", $from);
            $this->db->where("date_of_submission <=", $to);
            $this->db->where('user_id', $user_id);
            $this->db->where('ft.active', 'yes');
            $this->db->group_by('user_id');
            $this->db->limit($limit, $page);
            $query = $this->db->get();
        }
        $i = 0;
        $row1 = array();
        foreach ($query->result_array() as $row) {
            $row1[$i]['user_id'] = $row['user_id'];
            $row1[$i]['total_leg'] = $row['total_leg'];
            $row1[$i]['total_amount'] = round($row['total_amount'], 8);
            $row1[$i]['leg_amount_carry'] = $row['leg_amount_carry'];
            $row1[$i]['user_name'] = $this->validation_model->IdToUserName($row['user_id']);
            $row1[$i]['full_name'] = $this->validation_model->getUserFullName($row['user_id']);
            $row1[$i]['amount_payable'] = round($row['amount_payable'], 8);
            $row1[$i]['tds'] = round($row['tds'], 8);
            $row1[$i]['service_charge'] = round($row['service_charge'], 8);
            $i++;
        }
        return $row1;
    }

    public function getIncomeStatement($user_id, $page, $limit) {
        $this->db->select('paid_id,paid_user_id,paid_date,paid_type,paid_amount,paid_status');
        $this->db->where('paid_status','yes');
        $this->db->where('paid_user_id', $user_id);
        $this->db->limit($limit, $page);
        $res = $this->db->get('amount_paid');
        return $res->result_array();
    }
    
    public function getIncomeStatementCount($user_id) {
        $this->db->where('paid_date !=', '0000-00-00');
        $this->db->where('paid_user_id', $user_id);
        return $this->db->count_all_results('amount_paid');
    }

    public function getPayoutUserDetails($previous_pyout_date, $date_sub) {

        $payout_type = $this->getPayoutTypes();
        if ($payout_type == 'daily') {
            $this->db->select('a.user_name');
            $this->db->select('b.user_id ');
            $this->db->select_sum('total_amount');
            $this->db->select_sum('amount_payable');
            $this->db->select('b.amount_type ');
            $this->db->select('c.user_detail_name');
            $this->db->select('c.user_detail_address');
            $this->db->select('c.user_detail_mobile');
            $this->db->select('c.user_detail_nbank');
            $this->db->select('c.user_detail_nbranch');
            $this->db->select('c.user_detail_acnumber');
            $this->db->select(' c.user_detail_ifsc');
            $this->db->from('ft_individual AS a');
            $this->db->join('leg_amount AS b', 'a.id = b.user_id', 'inner');
            $this->db->join('user_details AS c', 'a.id = c.user_detail_refid', 'inner');
            $this->db->like('date_of_submission', $date_sub, 'after');
            $this->db->where('active', 'yes');
            $this->db->group_by('a . id');
            $query = $this->db->get();
        } else {
            $this->db->select('a.user_name');
            $this->db->select('b.user_id ');
            $this->db->select_sum('total_amount');
            $this->db->select_sum('amount_payable');
            $this->db->select('b.amount_type ');
            $this->db->select('c.user_detail_name');
            $this->db->select('c.user_detail_address');
            $this->db->select('c.user_detail_mobile');
            $this->db->select('c.user_detail_nbank');
            $this->db->select('c.user_detail_nbranch');
            $this->db->select('c.user_detail_acnumber');
            $this->db->select(' c.user_detail_ifsc');
            $this->db->from('ft_individual AS a');
            $this->db->join('leg_amount AS b', 'a.id = b.user_id', 'inner');
            $this->db->join('user_details AS c', 'a.id = c.user_detail_refid', 'inner');
            $this->db->where('released_date', $date_sub);
            $this->db->where('active', 'yes');
            $this->db->group_by('a . id');
            $query = $this->db->get();
        }

        $release = array();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $release[$i]['name'] = $row['user_name'];
            $release[$i]['uid'] = $row['user_id'];
            $release[$i]['total_amount'] = $row['total_amount'];
            $release[$i]['amount_payable'] = $row['amount_payable'];
            $release[$i]['type'] = $row['amount_type'];
            $release[$i]['user_name'] = $row['user_detail_name'];
            $release[$i]['address'] = $row['user_detail_address'];
            $release[$i]['mobile'] = $row['user_detail_mobile'];
            $release[$i]['bank'] = $row['user_detail_nbank'];
            $release[$i]['branch'] = $row['user_detail_nbranch'];
            $release[$i]['acc'] = $row['user_detail_acnumber'];
            $release[$i]['ifsc'] = $row['user_detail_ifsc'];
            $i++;
        }

        return $release;
    }

    public function getPayoutReleasePercentages($user_id = '') {

        $payout_details = array();


        $released_payouts = $this->getReleasedPayoutCount($user_id);
        $pending_payouts = $this->getPendingPayoutCount($user_id);
        $total_payouts = $pending_payouts + $released_payouts;
        if ($total_payouts > 0) {
            $released_payouts_percentage = ($released_payouts / $total_payouts) * 100;
            $pending_payouts_percentage = ($pending_payouts / $total_payouts) * 100;
        } else {
            $released_payouts_percentage = 100;
            $pending_payouts_percentage = 0;
        }

        $payout_details['released'] = $released_payouts_percentage;
        $payout_details['pending'] = $pending_payouts_percentage;

        return $payout_details;
    }

    public function getReleasedPayoutCount($user_id = '') {

        $count = 0;
        if ($user_id == '') {
            $this->db->select('*');
            $this->db->from('leg_amount');
            $count = $this->db->count_all_results();
        } else {
            $this->db->select('*');
            $this->db->from('leg_amount');
            $this->db->where('user_id', $user_id);
            $count = $this->db->count_all_results();
        }
        return $count;
    }

    public function getPendingPayoutCount($user_id = '') {
        $count = 0;
        if ($user_id == '') {
            $this->db->select('*');
            $this->db->from('leg_amount');
            $count = $this->db->count_all_results();
        } else {
            $this->db->select('*');
            $this->db->from('leg_amount');
            $this->db->where('user_id', $user_id);
            $count = $this->db->count_all_results();
        }
        return $count;
    }

    public function getPayoutDetails($payout_release_type, $amount = '', $read_status='',$payment_type='', $user_name = '') {
        $payout_details = array();
        if ($amount == '') {
            $amount = $this->getMinimumPayoutAmount();
        }
        $current_date = date('Y-m-d H:i:s');
        if ($payout_release_type == 'ewallet_request') {
            $req_validity = $this->getPayoutRequestValidity();
            $this->db->select('pr.req_id,pr.requested_user_id,pr.requested_date,pr.requested_amount_balance,ft.user_name,ud.user_detail_name,ud.user_detail_second_name,ud.payout_type,pr.payment_method');
            $this->db->from('payout_release_requests AS pr');
            $this->db->join('ft_individual AS ft', 'ft.id = pr.requested_user_id', 'INNER');
            $this->db->join('user_details AS ud', 'ud.user_detail_refid = ft.id', 'INNER');
            $this->db->where('ft.active', 'yes');
            $this->db->where('ft.user_type !=', 'admin');
            if($read_status)
                $this->db->where('pr.read_status', $read_status);
            $this->db->where('pr.requested_amount >=', $amount);
            $this->db->where('pr.status', "pending");
            if($payment_type)
                $this->db->where('pr.payment_method', $payment_type);
            if($user_name) 
                $this->db->where('ft.user_name', $user_name);
            $this->db->order_by('pr.requested_date', 'DESC');
            $query = $this->db->get();
            $i = 0;
            foreach ($query->result_array() as $row) {
                $requested_date = $row['requested_date'];
                $req_id = $row['req_id'];
                $requested_user_id = $row['requested_user_id'];
                $diff = abs(strtotime($requested_date) - strtotime($current_date));
                $days = floor(($diff) / (60 * 60 * 24));
                $balance_amount = $this->getUserBalanceAmount($row['requested_user_id']);
                $requested_amount = $row['requested_amount_balance'];
                
                $payout_details[$i]['req_id'] = $row['req_id'];
                $payout_details[$i]['user_id'] = $requested_user_id;
                $payout_details[$i]['user_name'] = $row['user_name'];
                $payout_details[$i]['full_name'] = $row['user_detail_name'] . " " . ($row['user_detail_second_name'] ? $row['user_detail_second_name'] : "");
                $payout_details[$i]['user_detail_name'] = $row['user_detail_name'];
                $payout_details[$i]['balance_amount'] = $balance_amount;
                $payout_details[$i]['payout_amount'] = $requested_amount;
                $payout_details[$i]['requested_date'] = $row['requested_date']; 
                $payout_details[$i]['payout_type'] = ($row['payment_method']== 'Bitcoin')? "Blocktrail" : $row['payment_method']; 
                $i++;
            }
        } else {
            $this->db->select('usr.user_id,usr.balance_amount,ft.user_name,ud.user_detail_name,ud.user_detail_second_name,ud.payout_type');
            $this->db->from('user_balance_amount AS usr');
            $this->db->join('ft_individual AS ft', 'ft.id = usr.user_id', 'INNER');
            $this->db->join('user_details AS ud', 'ud.user_detail_refid = usr.user_id', 'INNER');
            $this->db->where('ft.active', 'yes');
            $this->db->where('ft.user_type !=', 'admin');
            $this->db->where('usr.balance_amount >=', $amount);
            $this->db->where('payout_type', $payment_type);
            if($user_name) 
                $this->db->where('ft.user_name', $user_name);
            $this->db->order_by('usr.balance_amount', 'DESC');
            $query = $this->db->get();
            $i = 0;
            foreach ($query->result_array() as $row) {
                $payout_details[$i]['req_id'] = $row['user_name'];
                $payout_details[$i]['user_id'] = $row['user_id'];
                $payout_details[$i]['user_name'] = $row['user_name'];
                $payout_details[$i]['full_name'] = $row['user_detail_name'] . " " . ($row['user_detail_second_name'] ? $row['user_detail_second_name'] : "");
                $payout_details[$i]['user_detail_name'] = $row['user_detail_name'];
                $payout_details[$i]['balance_amount'] = $row['balance_amount'];
                $payout_details[$i]['payout_amount'] = $amount;
                $payout_details[$i]['requested_date'] = $current_date;
                $payout_details[$i]['payout_type'] = ($row['payout_type']== 'Bitcoin')? "Blocktrail" : $row['payout_type']; 
                $i++;
            }
        }
        return $payout_details;
    }

    public function getMinimumPayoutAmount() {
        $amount = 0;
        $this->db->select('min_payout');
        $this->db->from('configuration');
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $amount = $row->min_payout;
        }
        return $amount;
    }

    public function getMaximumPayoutAmount() {
        $amount = 0;
        $this->db->select('max_payout');
        $this->db->from('configuration');
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $amount = $row->max_payout;
        }
        return $amount;
    }

    public function checkTransactionPassword($user_id, $transation_password) {
        $this->db->select('tran_password');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('tran_password');
        $password_hash = $query->row_array()['tran_password'];
        $password_matched = password_verify($transation_password, $password_hash);
        if ($password_hash && $password_matched) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getPayoutRequestValidity() {
        $request_validity = 0;
        $this->db->select('payout_request_validity');
        $this->db->from('configuration');
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $request_validity = $row->payout_request_validity;
        }
        return $request_validity;
    }

    public function getUserBalanceAmount($user_id) {
        $user_balance = 0;
        $this->db->select('balance_amount');
        $this->db->from('user_balance_amount');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();

        foreach ($query->result() as $row) {
            $user_balance = round($row->balance_amount, 8);
        }
        return $user_balance;
    }

    public function updateUserBalanceAmount($user_id, $payout_release_amount) {
        $res = 0;
        $balance_amount = $this->getUserBalanceAmount($user_id);
        if ($balance_amount >= $payout_release_amount && $payout_release_amount > 0) {
            $this->db->set('balance_amount', 'ROUND(balance_amount - ' . $payout_release_amount . ',8)', FALSE);
            $this->db->where('user_id', $user_id);
            $res = $this->db->update('user_balance_amount');
        }

        return $res;
    }

    public function insertPayoutReleaseRequest($user_id, $payout_amount, $request_date, $status = 'pending') {
        $this->load->model('configuration_model');

        $configuration = $this->configuration_model->getSettings();
        $payout_fee = $configuration['payout_fee_amount'];
        if($configuration['payout_fee_mode'] == 'percentage')
            $payout_fee = $payout_amount * $configuration['payout_fee_amount'] / 100;

        $payout_method = $this->validation_model->getUserData($user_id, "payout_type");
        $data = array(
            'requested_user_id' => $user_id,
            'requested_amount' => $payout_amount,
            'requested_amount_balance' => $payout_amount,
            'requested_date' => $request_date,
            'payout_fee' => $payout_fee,
            'status' => $status,
            'payment_method'=>$payout_method,
            'updated_date' => $request_date,
            );
        
        $res = $this->db->insert('payout_release_requests', $data);
        
        $ewallet_id = $this->db->insert_id();
        // $this->validation_model->addEwalletHistory($user_id, 0, $ewallet_id, 'payout', $payout_amount, 'payout_request', 'debit', $ewallet_id);
        $this->validation_model->addEwalletHistory($user_id, 0, $ewallet_id, 'payout', $payout_amount, 'payout_request', 'debit', $ewallet_id, '', $payout_fee);
        
        return $res;
    }

    public function getReleasedPayoutTotal($user_id) {
        $total_amount = '';
        if ($release_payout_status = 'released') { 
            $this->db->select_sum('requested_amount');
            $this->db->where('requested_user_id', $user_id);
            $this->db->where('status', 'released');
            $this->db->from('payout_release_requests');
            $query = $this->db->get();
            foreach ($query->result() as $row) {
                $total_amount = $row->requested_amount;
            }
            $this->db->select_sum('requested_amount');
            $this->db->select_sum('requested_amount_balance');
            $this->db->where('requested_user_id', $user_id);
            $this->db->where_in('status', array('deleted','pending'));
            $this->db->from('payout_release_requests');
            $query = $this->db->get();
            foreach ($query->result() as $row) {
                $total_amount1 = $row->requested_amount;
                $total_amount2 = $row->requested_amount_balance;
                $final_amount = $total_amount1 - $total_amount2;
            }
        }
        return ($total_amount + $final_amount);
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

    public function deletePayoutRequest($del_id, $user_id, $status = 'deleted') {
        $date = date('Y-m-d H:i:s');
        $this->db->set('status', $status);
        $this->db->set('updated_date', $date);
        $this->db->where('req_id', $del_id);
        $this->db->where('requested_user_id', $user_id);
        $res = $this->db->update('payout_release_requests');
        if ($res) {
            $refundAmount = $this->getPayoutRefundAmount($del_id, $user_id);

            // $data = array(
            //     'paid_user_id' => $user_id,
            //     'paid_amount' => $requested_amount,
            //     'paid_date' => $date,
            //     'paid_type' => $status . '_payout_release',
            //     'transaction_id' => 0
            //     );
            // $result = $this->db->insert('amount_paid', $data);
            
            $ewallet_id = $del_id;
            $this->validation_model->addEwalletHistory($user_id, 0, $ewallet_id, 'payout', $refundAmount, ($status == 'deleted') ? 'payout_delete' : 'payout_inactive', 'credit', $del_id);

            if ($refundAmount) {
                $this->addUserBalanceAmount($user_id, $refundAmount);
            }
        }
        return $res;
    }

    public function getPayoutRefundAmount($del_id, $user_id) {
        $refundAmount = 0;
        $this->db->select('requested_amount_balance, payout_fee');
        $this->db->where('req_id', $del_id);
        $this->db->where('requested_user_id', $user_id);
        $query = $this->db->get('payout_release_requests');
        foreach ($query->result_array()AS $row) {
            $refundAmount = $row["requested_amount_balance"] + $row['payout_fee'];
        }
        return $refundAmount;
    }

    public function getPayoutRequestAmount($del_id, $user_id) {
        $requested_amount = 0;
        $this->db->select('requested_amount_balance');
        $this->db->where('req_id', $del_id);
        $this->db->where('requested_user_id', $user_id);
        $query = $this->db->get('payout_release_requests');
        foreach ($query->result_array()AS $row) {
            $requested_amount = $row["requested_amount_balance"];
        }
        return $requested_amount;
    }

    public function addUserBalanceAmount($user_id, $amount) {
        $res = 0;
        $balance_amount = $this->getUserBalanceAmount($user_id);
        if ($amount > 0) {
            $this->db->set('balance_amount', 'ROUND(balance_amount + ' . $amount . ',8)', FALSE);
            $this->db->where('user_id', $user_id);
            $res = $this->db->update('user_balance_amount');
        }
        return $res;
    }

    public function getUserDetails($user_id) {
        $this->load->model('country_state_model');
        $this->db->select('*');
        $this->db->where('user_detail_refid', $user_id);
        $this->db->from('user_details');
        $res = $this->db->get();
        $i = 0;
        foreach ($res->result_array() as $row) {
            $result[$i]['name'] = $row['user_detail_name'] . " " . ($row['user_detail_second_name'] ? $row['user_detail_second_name'] : "");
            $result[$i]['address'] = $row['user_detail_address'];
            $result[$i]['pin'] = $row['user_detail_pin'];
            $result[$i]['email'] = $row['user_detail_email'];
            $result[$i]['user_name'] = $this->validation_model->IdToUserName($user_id);
            $result[$i]['mobile'] = $row['user_detail_mobile'];
            $result[$i]['country'] = $this->country_state_model->getCountryNameFromId($row['user_detail_country']);
            $result[$i]['dob'] = $row['user_detail_dob'];
            if ($row['user_detail_gender'] == 'M')
                $result[$i]['gender'] = 'Male';
            else
                $result[$i]['gender'] = 'Female';
            $result[$i]['pan'] = $row['user_detail_pan'];
            $result[$i]['acc'] = $row['user_detail_acnumber'];
            $result[$i]['bank'] = $row['user_detail_nbank'];
            $result[$i]['branch'] = $row['user_detail_nbranch'];
            $i++;
        }
        return $result;
    }

    public function updatePayoutReleaseRequest($request_id, $user_id, $payout_release_amount, $payout_release_type, $release_method, $hash="") {
        $result = false;
        if ($payout_release_amount > 0) {
            $update_request = false;
            $payoutRequestDetails = $this->getPayoutRequestById($request_id);
            if ($payout_release_type == 'ewallet_request') {
                if (count($payoutRequestDetails)) {
                    $this->db->set('status', "IF((requested_amount_balance - {$payout_release_amount}) <= 0, 'released', status)", FALSE);
                    $this->db->set('updated_date', date("Y-m-d H:i:s"));
                    $this->db->set('requested_amount_balance', 'ROUND(requested_amount_balance + ' . -$payout_release_amount . ',8)', FALSE);
                    $this->db->set('payment_method',$release_method);
                    $this->db->where('requested_user_id', $user_id);
                    $this->db->where('req_id', $request_id);
                    $this->db->where('status', 'pending');
                    $update_request = $this->db->update('payout_release_requests');
                }
            } else {
                $update_request = true;
            }
            if ($update_request) {
                $date = date('Y-m-d H:i:s');
                $data = array(
                    'paid_user_id' => $user_id,
                    'paid_amount' => $payout_release_amount,
                    'paid_date' => $date,
                    'transaction_id' => $hash,
                    'paid_type' => 'released',
                    'paid_status' => ($release_method == 'Bank Transfer')?'no':'yes',
                    'payment_method'=>$release_method
                    );
                $data['payout_fee'] = $this->calculatePayoutFee($payout_release_amount);
                if ($payout_release_type == 'ewallet_request') {
                    $data['request_id'] = $request_id;
                    if(count($payoutRequestDetails))
                        $data['payout_fee'] = $payoutRequestDetails["payout_fee"];
                }
                $result = $this->db->insert('amount_paid', $data);
                
                if ($payout_release_type == 'ewallet_request') {
                    $ewallet_id = $request_id;
                    $release_type = 'payout_release';
                    $transaction_id = $request_id;
                } else {
                    $ewallet_id = $this->db->insert_id();
                    $release_type = 'payout_release_manual';
                    $transaction_id = '';
                }
                
                $this->validation_model->addEwalletHistory($user_id, 0, $ewallet_id, 'payout', $payout_release_amount, $release_type, 'debit', $transaction_id, '', $data['payout_fee']);
            }
        }
        return $result;
    }

    public function isPayoutRequestPending($request_id, $user_id) {
        $this->db->where('requested_user_id', $user_id);
        $this->db->where('req_id', $request_id);
        $this->db->where('status', 'pending');
        $count = $this->db->count_all_results('payout_release_requests');
        return $count;
    }

    public function getMinimumMaximunPayoutAmount() {
        $details = array();
        $this->db->select('min_payout,max_payout');
        $this->db->from('configuration');
        $this->db->where('id', 1);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $details['min_payout'] = $row->min_payout;
            $details['max_payout'] = $row->max_payout;
        }
        return $details;
    }

    public function getBitcoinAddress($user_id) {
        $bitcoin_address = '';
        $this->db->select('bitcoin_address');
        $this->db->from('user_details');
        $this->db->where('user_detail_refid', $user_id);
        $query = $this->db->get();
        if (!empty($query->row('bitcoin_address'))) {
            $encoded_addr = $query->row('bitcoin_address');
            $key = $this->config->item('encryption_key');
            $bitcoin_address = $encoded_addr;  
        }
        return $bitcoin_address;
    }
    
    public function setPayoutViewed($to_read_status){  
        $this->db->set('read_status',$to_read_status++);
        $this->db->where('read_status',$to_read_status);
        $this->db->update('payout_release_requests');
        return;
    }
     //mark as paid 
    public function getReleasedPayout($user_name = "", $from, $to , $limit = '',$page='') {
        $this->db->select('paid_id, paid_amount, paid_date, ft.user_name, CONCAT(u.user_detail_name," ",u.user_detail_second_name) full_name');
        $this->db->from('amount_paid');
        $this->db->join('ft_individual AS ft', 'ft.id = amount_paid.paid_user_id', 'LEFT');
        $this->db->join('user_details AS u', 'u.user_detail_refid = ft.id', 'LEFT');
        $this->db->where(['paid_type' => 'released', 'paid_status !=' => "yes", "payment_method" => 'Bank Transfer']);

        if($user_name != '') {
            $this->db->where('ft.user_name', $user_name);
        }
         
        if($from != '') {
            $this->db->where('DATE_FORMAT(paid_date,"%Y-%m-%d")  >=', $from);
        } 
        if($to != '') {
            $this->db->where('DATE_FORMAT(paid_date,"%Y-%m-%d")  <=', $to);
        }
       
        $this->db->order_by('paid_date', 'DESC');
        $this->db->limit($limit, $page);
        $query = $this->db->get();
        return $query->result_array();

    }
    
    public function updateBankTransactionStatus($request_id,$user_id, $paid_amount)
    {
        $this->db->set('paid_status','yes');
        $this->db->set('paid_date', date('Y-m-d H:i:s'));
        $this->db->where('paid_id  =', $request_id);
        $this->db->where('paid_type  =', 'released');
        $this->db->where('paid_status  !=', 'yes');
        $this->db->where('paid_amount  =', $paid_amount);
        $this->db->where('paid_user_id', $user_id);
       
        return $this->db->update("amount_paid") ;
    }
    
    public function getPayoutCount($user_name = "", $from = "", $to = "") {
        $this->db->from("amount_paid");
        $this->db->where('paid_type  =', 'released');
        $this->db->where('paid_status  !=', 'yes');
        $this->db->where(['paid_type' => 'released', 'paid_status !=' => "yes", "payment_method" => 'Bank Transfer']);
        if($user_name != "") {
            $this->db->join('ft_individual as ft', 'ft.id = amount_paid.paid_user_id', 'LEFT');
            $this->db->where('ft.user_name', $user_name);
        }
        if($from != '' && $to != '') {
            $this->db->where('paid_date  >=', $from);
            $this->db->where('paid_date  <=', $to);
        } else if($from != '') {
            $this->db->where('paid_date  >=', $from);
        } else if($to != '') {
            $this->db->where('paid_date  <=', $to);
        }

        $count = $this->db->count_all_results();
        return $count;
    }
    //mark as paid ends
   
    //cancel waiting withrawal
    public function deletePayoutWithdrawed($user_id)
    {
        $result = false;
        $update_request = "";
        
                $this->db->select('req_id,requested_amount,requested_amount_balance,requested_user_id,status, payout_fee');
               // $this->db->select_sum('requested_amount_balance',  'amount_cancelled');
                $this->db->where('requested_user_id', $user_id);
                $this->db->where('status', 'pending');
                $details = $this->db->from('payout_release_requests');
                $query = $this->db->get();
                $count = $this->db->count_all_results();
                
                $amount_cancelled = 0;
                $payout_fee = 0;
                if($count > 0)
                {
                    foreach ($query->result_array() as $row)
                    {
                        
                    $amount_released = $row['requested_amount'] - $row['requested_amount_balance']; 
                      
                    $amount_cancelled +=  $row['requested_amount_balance'];
                    $payout_fee +=  $row['payout_fee'];
                    if(($row['requested_amount'] -($amount_released + $row['requested_amount_balance'])) == 0 )
                        {
                            $this->db->set('status',  'cancelled');
                         
                            $this->db->set('updated_date', date("Y-m-d H:i:s"));
                            $this->db->set('requested_amount', 'ROUND('.$row['requested_amount'].' +'  . -$row['requested_amount_balance'] . ',8)', FALSE);
                            $this->db->set('requested_amount_balance', '0' );
                            $this->db->where('req_id', $row['req_id'] );
                            $this->db->where('requested_amount',$row['requested_amount']);
                            $this->db->where('status', 'pending');
                            $update_request = $this->db->update('payout_release_requests');
                             $ewallet_id =  $row['req_id'];
                             
                            $this->db->set('balance_amount', 'ROUND(balance_amount  +' . ($row['requested_amount_balance'] + $row['payout_fee']) . ',8)', FALSE);
                            $this->db->where('user_id', $user_id);
                            $res1 = $this->db->update('user_balance_amount');
                        }
                    }
                    if($amount_cancelled || $payout_fee)
                        $this->validation_model->addEwalletHistory($user_id, 0, $ewallet_id, 'payout',($amount_cancelled + $payout_fee), 'withdrawal_cancel', 'credit', $ewallet_id);
                }
        
        else {
            $update_request = "";
        }
        
        return $update_request;
    }
         //ends 
    
    public function gatewayList() {
        return $this->db->select('gateway_name')
            ->from('payment_gateway_config')
            ->where('payout_status', "yes")
            ->order_by('payout_sort_order', "ASC")
            ->get()
            ->result_array();
    }

    public function userPayoutRequestCount($user_id, $status="released", $date="", $read_status='') {
        $this->db->where('requested_user_id', $user_id);
        $this->db->where('status', $status);
        if($date){
            $this->db->where('updated_date >=', $date);
        }
        if($read_status){
            $this->db->where('read_status', $read_status);
        }
        $count = $this->db->count_all_results('payout_release_requests');
        return $count;
    }
    
    public function getUserPayoutType($user_id){  
        
        $type = NULL;
        $this->db->select('payout_type');
        $this->db->from('user_details');
        $this->db->where('user_detail_refid', $user_id);
        $this->db->limit(1);
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $type = $row['payout_type'];
        }
        return $type;
    }

    public function getPayoutWithdrawalDetails($user_id ='', $status, $limit, $page) {
        $payout_details = array();
        $current_date = date('Y-m-d H:i:s');
            $req_validity = $this->getPayoutRequestValidity();
            $this->db->select('pr.req_id,pr.requested_user_id,pr.requested_date,pr.updated_date,pr.requested_amount_balance,pr.payment_method,ft.user_name,ud.user_detail_name,ud.user_detail_second_name, ft.delete_status as user_delete_status');
            $this->db->from('payout_release_requests AS pr');
            $this->db->join('ft_individual AS ft', 'ft.id = pr.requested_user_id', 'INNER');
            $this->db->join('user_details AS ud', 'ud.user_detail_refid = ft.id', 'LEFT');
            if($user_id!= NULL){
            $this->db->where('pr.requested_user_id', $user_id);
            }
            $this->db->where('ft.active', 'yes');
            $this->db->where('pr.status', $status);
            $this->db->limit($limit, $page);
            $this->db->order_by('pr.requested_date', 'DESC');
            $query = $this->db->get();
            $i = 0;
            foreach ($query->result_array() as $row) {
                $requested_date = $row['requested_date'];
                $req_id = $row['req_id'];
                $requested_user_id = $row['requested_user_id'];
                $diff = abs(strtotime($requested_date) - strtotime($current_date));
                $days = floor(($diff) / (60 * 60 * 24));
                $balance_amount = $this->getUserBalanceAmount($row['requested_user_id']);
                $requested_amount = $row['requested_amount_balance'];
                
                $payout_details[$i]['req_id'] = $row['req_id'];
                $payout_details[$i]['user_id'] = $requested_user_id;
                $payout_details[$i]['user_name'] = $row['user_name'];
                $payout_details[$i]['full_name'] = $row['user_detail_name'] . " " . ($row['user_detail_second_name'] ? $row['user_detail_second_name'] : "");
                $payout_details[$i]['user_detail_name'] = $row['user_detail_name'];
                $payout_details[$i]['balance_amount'] = $balance_amount;
                $payout_details[$i]['payout_amount'] = $requested_amount;
                $payout_details[$i]['requested_date'] = $row['requested_date']; 
                $payout_details[$i]['payout_type'] = ($row['payment_method']== 'Bitcoin')? "Blocktrail" : $row['payment_method']; 
                $payout_details[$i]['updated_date'] = $row['updated_date']; 
                $payout_details[$i]['user_delete_status'] = $row['user_delete_status']; 
                $i++;
            }
        return $payout_details;
    }



    public function getReleasedWithdrawalDetails($user_id = '',$paid_status, $limit, $page) {
        $income_arr = array();
        $this->db->select('ap.paid_date,ap.paid_amount,ap.payment_method,ft.user_name,ft.delete_status,ud.user_detail_name');
        if($user_id!= NULL){
        $this->db->where('paid_user_id', $user_id);
        }
        $this->db->where('paid_type  =', 'released');
        if($paid_status == 'approved_pending'){
            $this->db->where('paid_status  !=', 'yes');
            $this->db->where('ap.payment_method =','Bank Transfer');
        }elseif ($paid_status == 'approved_paid') {
            $this->db->where("CASE WHEN ap.payment_method = 'Bank Transfer' THEN paid_status = 'yes' ELSE paid_status != 'yes' END");   
        }
        $this->db->limit($limit, $page);
        $this->db->from("amount_paid AS ap");
        $this->db->join('ft_individual AS ft', 'ft.id = ap.paid_user_id', 'INNER');
        $this->db->join('user_details AS ud', 'ud.user_detail_refid = ft.id', 'LEFT');
        $this->db->order_by('ap.paid_date', 'DESC');
        $query = $this->db->get();

        $i = 0;
        
        foreach ($query->result_array() as $row) {
           
            $income_arr[$i]["paid_date"] = $row['paid_date'];
            $income_arr[$i]["user_name"] = $row['user_name'];
            $income_arr[$i]["user_detail_name"] = $row['user_detail_name'];
            $income_arr[$i]["paid_amount"] = $row['paid_amount'];
            $income_arr[$i]["payment_method"] = $row['payment_method'];
            $income_arr[$i]["user_delete_status"] = $row['delete_status'];
            $i++;
        }
        return $income_arr;
    } 
    
    public function getPayoutWithdrawalCount($user_id = '', $status) {
        if($user_id!= NULL){
                $this->db->where('pr.requested_user_id', $user_id);
        }
        $this->db->where('ft.active', 'yes');
        $this->db->where('pr.status', $status);
        $this->db->from('payout_release_requests AS pr');
        $this->db->join('ft_individual AS ft', 'ft.id = pr.requested_user_id', 'INNER');
        $this->db->join('user_details AS ud', 'ud.user_detail_refid = ft.id', 'INNER');
        return $this->db->count_all_results();
    }
    
    public function getReleasedWithdrawalCount($user_id = '',$paid_status) {
        
        if($user_id!= NULL){
        $this->db->where('paid_user_id', $user_id);
        }
        $this->db->where('paid_type  =', 'released');
        if($paid_status == 'approved_pending'){
            $this->db->where('paid_status  !=', 'yes');
            $this->db->where('ap.payment_method =','Bank Transfer');
        }elseif ($paid_status == 'approved_paid') {
            $this->db->where("CASE WHEN ap.payment_method = 'Bank Transfer' THEN paid_status = 'yes' END");   
        }
        $this->db->from("amount_paid AS ap");
        $this->db->join('ft_individual AS ft', 'ft.id = ap.paid_user_id', 'INNER');
        return $this->db->count_all_results();
        
    } 

    /**
     * User Dashboard
     * @param  [type] $user_id [description]
     * @return [type]          [description]
     */
    public function getUserTotalPayoutRequests($user_id, $status = 'pending') {
        $query = $this->db->select("SUM(requested_amount) AS amount")
                         ->from('payout_release_requests')
                         ->where('requested_user_id', $user_id)
                         ->where('status', $status)
                         ->get();
        return $query->row('amount');
    }
    public function getUserTotalPayouts($user_id, $paid_status = "") {
        $this->db->select('SUM(paid_amount) AS amount');
        $this->db->where('paid_user_id', $user_id);
        switch($paid_status) {
            case 'approved':
                $this->db->where('paid_type', 'released');
                $this->db->where('paid_status !=', 'yes');
            break;
            case 'paid':
                $this->db->where('paid_type', 'released');
                $this->db->where('paid_status', 'yes');
            break;
            case 'rejected':
                $this->db->where('paid_type', 'deleted_payout_release');
            break;
        }
        $this->db->from("amount_paid");
        $query = $this->db->get();
        return $query->row('amount');
    }
    /**/
     public function getUserLanguage($user_id) {
        $this->db->select('default_lang');
        $this->db->where('id',$user_id);
        $this->db->from('ft_individual');
        $lang_id = $this->db->get()->row('default_lang');
        return $lang_id;
    }

    public function getTotalAmountPendingRequest($status, $user_id = '')
    {
     $this->db->select('sum(requested_amount_balance) sum');
     $this->db->from('payout_release_requests');
     if($user_id != ''){
     $this->db->where('requested_user_id', $user_id);
     }
     $this->db->where('status',$status);
     $query=$this->db->get();
     $res=$query->row('sum');
     return $res;
    }
    public function getTotalAmountApproved($user_id = '')
    {       

            $this->db->select('sum(paid_amount) sum');
            $this->db->from('amount_paid');
            $this->db->where('paid_type  =', 'released');
            if($user_id != ''){
            $this->db->where('paid_user_id', $user_id);
            }
            $this->db->where('paid_status  !=', 'yes');
            $this->db->where('payment_method =','Bank Transfer');
            $query=$this->db->get();
            $res=$query->row('sum');
            
            return $res;

    }
    public function getTotalAmountPaid($user_id = '')
    {


       $this->db->select('sum(paid_amount) sum');
       $this->db->from('amount_paid'); 
       $this->db->where('paid_type  =', 'released');
       if($user_id != ''){
            $this->db->where('paid_user_id', $user_id);
        }
       $this->db->where("CASE WHEN payment_method = 'Bank Transfer' THEN paid_status = 'yes' ELSE paid_status != 'yes' END"); 
       $query=$this->db->get();
       $res=$query->row('sum');
       
       return $res;
     }
     public function getTotalAmountRejected($status, $user_id = '')
     {
        $this->db->select('sum(requested_amount_balance) sum');
     $this->db->from('payout_release_requests');
     $this->db->where('status',$status);
     if($user_id != ''){
     $this->db->where('requested_user_id', $user_id);
     }
     $query=$this->db->get();
     $res=$query->row('sum');
     
     return $res;
     }

    /**
     * [getPayoutRequestUserId userid from payout release requests]
     * @param  [any] $request_id [req_id from payout_release_request table]
     * @return [any]  requested_user_id
     */
    public function getPayoutRequestUserId($request_id) {
        return $this->db->select('requested_user_id')
            ->where('req_id', $request_id)
            ->get('payout_release_requests')
            ->row('requested_user_id');
    }

    /**
     * [getUnreadPayoutRequestsCount description]
     * @return [type] [description]
     */
    public function getUnreadPayoutRequestsCount() {
        $this->db->where(['read_status' => 2, 'status' => 'pending']);
        return $this->db->count_all_results('payout_release_requests');
    }

    public function getRequestPayoutDetails($request_id) {
        return $this->db->where('req_id', $request_id)->get('payout_release_requests')->row_array();
    }


    public function getPaidPayoutDetails($paid_id) {
        return $this->db
            ->select('paid_amount, paid_user_id, user.user_name')
            ->where('paid_id', $paid_id)
            ->join('ft_individual as user', 'user.id = amount_paid.paid_user_id', 'LEFT')
            ->get('amount_paid')
            ->row_array();
    }

    public function getPayoutRequestById($request)
    {
        $array = $this->db->where('req_id', $request)
                ->get("payout_release_requests")->result_array();
        if(count($array))
            return $array[0];
        return [];
    }

    public function calculatePayoutFee($payoutAmount) {
        $this->load->model('configuration_model');
        $configuration = $this->configuration_model->getSettings();
        $payoutFee = $configuration["payout_fee_amount"];
        if($configuration["payout_fee_mode"] == "percentage")
            $payoutFee = $payoutAmount * $configuration["payout_fee_amount"] / 100;
        return $payoutFee;
    }

    public function getPayoutReleasedUseridFromPaidID($paid_id){

        $this->db->select('paid_user_id');
        $this->db->from('amount_paid');
        $this->db->where('paid_id',$paid_id);
        $query=$this->db->get();

        return $query->result_array()[0]['paid_user_id'];
        }

        public function getUserDeatilsForInvoice($user_id)
        {
            $this->db->select('user_detail_name,user_detail_second_name,user_detail_address,user_detail_mobile,user_detail_city,user_detail_pin');
            $this->db->from('user_details');
            $this->db->where('user_detail_refid',$user_id);
            $query=$this->db->get();
            return $query->result_array();
        }

        public function getPayoutDetailsFromAmountPaid($paid_id)
        {
            $this->db->select('paid_amount,paid_date');
            $this->db->from('amount_paid');
            $this->db->where('paid_id',$paid_id);
            $query=$this->db->get();
            return $query->result_array();
        }

        public function deActivateExpiredPayoutRequests()
        {
            $validaity_days = $this->getPayoutRequestValidity() - 1;
            $req_date_min = date("Y-m-d 00:00:00", strtotime("-$validaity_days days"));

            $req_array = $this->db->select("req_id, requested_user_id")
                    ->where("status", "pending")
                    ->where("requested_amount_balance >", 0)
                    ->where("requested_date <", $req_date_min)
                    ->get("payout_release_requests")->result_array();

            foreach ($req_array as $req) {
                if(!$this->deletePayoutRequest($req['req_id'], $req['requested_user_id'], 'inactive')) {
                    return false;
                }
            }
            return true;
        }

    //New model methods 
    public function getPayoutTypes() {
        return $this->db->select('payout_release')
            ->get('configuration')
            ->row('payout_release');
    }

    public function getPayoutDetailsCount($payout_release_type, $amount = '', $read_status='',$payment_types=[], $user_names = [], $kyc_status = "active") {
        if ($amount == '') {
            $amount = $this->getMinimumPayoutAmount();
        }
        $current_date = date('Y-m-d H:i:s');
        if ($payout_release_type == 'ewallet_request') {
            $req_validity = $this->getPayoutRequestValidity();
            $this->db->select('pr.req_id,pr.requested_user_id,pr.requested_date,pr.requested_amount_balance,ft.user_name,ud.user_detail_name,ud.user_photo, ud.user_detail_second_name,ud.payout_type,pr.payment_method');
            $this->db->from('payout_release_requests AS pr');
            $this->db->join('ft_individual AS ft', 'ft.id = pr.requested_user_id', 'INNER');
            $this->db->join('user_details AS ud', 'ud.user_detail_refid = ft.id', 'INNER');
            $this->db->where('ft.active', 'yes');
            $this->db->where('ft.user_type !=', 'admin');
            if($this->MODULE_STATUS['kyc_status'] == "yes") {
                $this->db->where('ud.kyc_status', $kyc_status == "active" ? "yes" : "no");
            }
            if($read_status)
                $this->db->where('pr.read_status', $read_status);
            $this->db->where('pr.requested_amount >=', $amount);
            $this->db->where('pr.status', "pending");
            if(!empty($payment_types))
                $this->db->where_in('pr.payment_method', $payment_types);
            if(!empty($user_name)) 
                $this->db->where_in('ft.user_name', $user_names);
            return $this->db->count_all_results();
        } else {
            $this->db->select('usr.user_id,usr.balance_amount,ft.user_name,ud.user_detail_name,ud.user_detail_second_name,ud.payout_type, ud.user_photo');
            $this->db->from('user_balance_amount AS usr');
            $this->db->join('ft_individual AS ft', 'ft.id = usr.user_id', 'INNER');
            $this->db->join('user_details AS ud', 'ud.user_detail_refid = usr.user_id', 'INNER');
            $this->db->where('ft.active', 'yes');
            $this->db->where('ft.user_type !=', 'admin');
            $this->db->where('usr.balance_amount >=', $amount);
            if($this->MODULE_STATUS['kyc_status'] == "yes") {
                $this->db->where('ud.kyc_status', $kyc_status == "active" ? "yes" : "no");
            }
            if(!empty($payment_type)) {
                $this->db->where_in('payout_type', $payment_type);    
            }
            if(!empty($user_names)) {
                $this->db->where_in('ft.user_name', $user_names);
            } 
            return $this->db->count_all_results();
        }
    }

    public function getPayoutDetailsNew($payout_release_type, $amount = '', $read_status='',$payment_types=[], $user_names = [], $filter, $kyc_status = "active") {
        $payout_details = array();
        if ($amount == '') {
            $amount = $this->getMinimumPayoutAmount();
        }
        $current_date = date('Y-m-d H:i:s');
        if ($payout_release_type == 'ewallet_request') {
            $req_validity = $this->getPayoutRequestValidity();
            $this->db->select('pr.req_id,pr.requested_user_id,pr.requested_date,pr.requested_amount_balance,ft.user_name,ud.user_detail_name,ud.user_photo, ud.user_detail_second_name,ud.payout_type,ud.kyc_status,pr.payment_method');
            $this->db->from('payout_release_requests AS pr');
            $this->db->join('ft_individual AS ft', 'ft.id = pr.requested_user_id', 'INNER');
            $this->db->join('user_details AS ud', 'ud.user_detail_refid = ft.id', 'INNER');
            $this->db->where('ft.active', 'yes');
            $this->db->where('ft.user_type !=', 'admin');
            if($this->MODULE_STATUS['kyc_status'] == "yes") { 
                $this->db->where('ud.kyc_status', $kyc_status == "active" ? "yes" : "no");
            }
            if($read_status)
                $this->db->where('pr.read_status', $read_status);
            $this->db->where('pr.requested_amount >=', $amount);
            $this->db->where('pr.status', "pending");
            if(!empty($payment_types))
                $this->db->where_in('pr.payment_method', $payment_types);
            if(!empty($user_names)) 
                $this->db->where_in('ft.user_name', $user_names);
            if($filter['order'] != "balance_amount") {
                $this->db->order_by($filter['order'], $filter['direction']);
            }
            $this->db->limit($filter['limit'], $filter['start']);
            $query = $this->db->get();
            $i = 0;
            foreach ($query->result_array() as $row) {
                $requested_date = $row['requested_date'];
                $req_id = $row['req_id'];
                $requested_user_id = $row['requested_user_id'];
                $diff = abs(strtotime($requested_date) - strtotime($current_date));
                $days = floor(($diff) / (60 * 60 * 24));
                $balance_amount = $this->getUserBalanceAmount($row['requested_user_id']);
                $requested_amount = $row['requested_amount_balance'];
                
                $payout_details[$i]['req_id'] = $row['req_id'];
                $payout_details[$i]['user_id'] = $requested_user_id;
                $payout_details[$i]['user_name'] = $row['user_name'];
                $payout_details[$i]['full_name'] = $row['user_detail_name'] . " " . ($row['user_detail_second_name'] ? $row['user_detail_second_name'] : "");
                $payout_details[$i]['user_detail_name'] = $row['user_detail_name'];
                $payout_details[$i]['user_photo'] = $row['user_photo'];
                $payout_details[$i]['balance_amount'] = $balance_amount;
                $payout_details[$i]['payout_amount'] = $requested_amount;
                $payout_details[$i]['requested_date'] = $row['requested_date']; 
                $payout_details[$i]['payout_type'] = ($row['payment_method']== 'Bitcoin')? "Blocktrail" : $row['payment_method']; 
                $payout_details[$i]['kyc_status'] = $row['kyc_status'];
                $i++;
            }
        } else {
            $this->db->select('usr.user_id,usr.balance_amount,ft.user_name,ud.user_detail_name,ud.user_detail_second_name,ud.payout_type, ud.user_photo, ud.kyc_status');
            $this->db->from('user_balance_amount AS usr');
            $this->db->join('ft_individual AS ft', 'ft.id = usr.user_id', 'INNER');
            $this->db->join('user_details AS ud', 'ud.user_detail_refid = usr.user_id', 'INNER');
            $this->db->where('ft.active', 'yes');
            $this->db->where('ft.user_type !=', 'admin');
            $this->db->where('usr.balance_amount >=', $amount);
            if($this->MODULE_STATUS['kyc_status'] == "yes") { 
                $this->db->where('ud.kyc_status', $kyc_status == "active" ? "yes" : "no");
            }
            if(!empty($payment_types)) {
                $this->db->where_in('payout_type', $payment_types);    
            }
            if(!empty($user_names)) {
                $this->db->where_in('ft.user_name', $user_names);
            } 
            $this->db->order_by($filter['order'], $filter['direction']);
            $this->db->limit($filter['limit'], $filter['start']);
            $query = $this->db->get();
            $i = 0;
            foreach ($query->result_array() as $row) {
                $payout_details[$i]['req_id'] = $row['user_name'];
                $payout_details[$i]['user_photo'] = $row['user_photo'];
                $payout_details[$i]['user_id'] = $row['user_id'];
                $payout_details[$i]['user_name'] = $row['user_name'];
                $payout_details[$i]['full_name'] = $row['user_detail_name'] . " " . ($row['user_detail_second_name'] ? $row['user_detail_second_name'] : "");
                $payout_details[$i]['user_detail_name'] = $row['user_detail_name'];
                $payout_details[$i]['balance_amount'] = $row['balance_amount'];
                $payout_details[$i]['payout_amount'] = $amount;
                $payout_details[$i]['requested_date'] = $current_date;
                $payout_details[$i]['payout_type'] = ($row['payout_type']== 'Bitcoin')? "Blocktrail" : $row['payout_type'];
                $payout_details[$i]['kyc_status'] = $row['kyc_status'];
                $i++;
            }
        }
        return $payout_details;
    }

    public function userNamesToIDs($user_names, $delete_status = "active") {
        $user_ids = [];
        if(empty($user_names)) {
           return $user_ids; 
        }
        $this->db->select('id');
        $this->db->from('ft_individual');
        $this->db->where_in('user_name', $user_names);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
           $user_ids[] = $row->id;
        }
        return $user_ids;
    }

    public function getPayoutWithdrawalCountNew($user_ids = [], $status) {
        if(!empty($user_ids)){
            $this->db->where_in('pr.requested_user_id', $user_ids);
        }
        $this->db->where('ft.active', 'yes');
        $this->db->where('pr.status', $status);
        $this->db->from('payout_release_requests AS pr');
        $this->db->join('ft_individual AS ft', 'ft.id = pr.requested_user_id', 'INNER');
        $this->db->join('user_details AS ud', 'ud.user_detail_refid = ft.id', 'INNER');
        return $this->db->count_all_results();
    }

    public function getPayoutWithdrawalDetailsNew($user_ids = [], $status, $filter) {
        $payout_details = array();
        $current_date = date('Y-m-d H:i:s');
        $req_validity = $this->getPayoutRequestValidity();
        $this->db->select('pr.req_id,pr.requested_user_id,pr.requested_date,pr.updated_date,pr.requested_amount_balance,pr.payment_method,ft.user_name,ud.user_detail_name,ud.user_detail_second_name, ft.delete_status as user_delete_status, ud.user_photo');
        $this->db->from('payout_release_requests AS pr');
        $this->db->join('ft_individual AS ft', 'ft.id = pr.requested_user_id', 'INNER');
        $this->db->join('user_details AS ud', 'ud.user_detail_refid = ft.id', 'LEFT');
        if(!empty($user_ids)){
            $this->db->where_in('pr.requested_user_id', $user_ids);
        }
        $this->db->where('ft.active', 'yes');
        $this->db->where('pr.status', $status);
        $this->db->order_by($filter['order'], $filter['direction']);
        $this->db->limit($filter['limit'], $filter['start']);
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $requested_date = $row['requested_date'];
            $req_id = $row['req_id'];
            $requested_user_id = $row['requested_user_id'];
            $diff = abs(strtotime($requested_date) - strtotime($current_date));
            $days = floor(($diff) / (60 * 60 * 24));
            $balance_amount = $this->getUserBalanceAmount($row['requested_user_id']);
            $requested_amount = $row['requested_amount_balance'];
            
            $payout_details[$i]['req_id'] = $row['req_id'];
            $payout_details[$i]['user_id'] = $requested_user_id;
            $payout_details[$i]['user_name'] = $row['user_name'];
            $payout_details[$i]['full_name'] = $row['user_detail_name'] . " " . ($row['user_detail_second_name'] ? $row['user_detail_second_name'] : "");
            $payout_details[$i]['user_detail_name'] = $row['user_detail_name'];
            $payout_details[$i]['balance_amount'] = $balance_amount;
            $payout_details[$i]['payout_amount'] = $requested_amount;
            $payout_details[$i]['requested_date'] = $row['requested_date']; 
            $payout_details[$i]['payout_type'] = ($row['payment_method']== 'Bitcoin')? "Blocktrail" : $row['payment_method']; 
            $payout_details[$i]['updated_date'] = $row['updated_date']; 
            $payout_details[$i]['user_delete_status'] = $row['user_delete_status']; 
            $payout_details[$i]['user_photo'] = $row['user_photo']; 
            $i++;
        }
        return $payout_details;
    }

    public function getReleasedWithdrawalCountNew($user_ids = [], $paid_status) {    
        $this->db->from("amount_paid AS ap");
        $this->db->join('ft_individual AS ft', 'ft.id = ap.paid_user_id', 'INNER');
        if(!empty($user_ids)) {
            $this->db->where_in('paid_user_id', $user_ids);
        }
        $this->db->where('paid_type  =', 'released');
        if($paid_status == 'approved_pending'){
            $this->db->where('paid_status  !=', 'yes');
        } elseif ($paid_status == 'approved_paid') {
            $this->db->where('paid_status', 'yes');
        }

        return $this->db->count_all_results();
    } 

    public function getReleasedWithdrawalDetailsNew($user_ids = [], $paid_status, $filter) {
        $income_arr = array();
        $this->db->select('ap.paid_id, ap.paid_date,ap.paid_amount,ap.payment_method,ft.user_name,ft.delete_status,ud.user_detail_name, ud.user_photo');
        if(!empty($user_ids)) {
            $this->db->where_in('paid_user_id', $user_ids);
        }
        $this->db->where('paid_type  =', 'released');
        if($paid_status == 'approved_pending'){
            $this->db->where('paid_status  !=', 'yes');
            $this->db->where('ap.payment_method =','Bank Transfer');
        }elseif ($paid_status == 'approved_paid') {
            $this->db->where("IF (ap.payment_method = 'Bank Transfer', paid_status = 'yes', 1)");
        }
        $this->db->from("amount_paid AS ap");
        $this->db->join('ft_individual AS ft', 'ft.id = ap.paid_user_id', 'INNER');
        $this->db->join('user_details AS ud', 'ud.user_detail_refid = ft.id', 'LEFT');
        $this->db->order_by($filter['order'], $filter['direction']);
        $this->db->limit($filter['limit'], $filter['start']);
        $query = $this->db->get();

        $i = 0;
        
        foreach ($query->result_array() as $row) {
           
            $income_arr[$i]["paid_id"] = $row['paid_id'];
            $income_arr[$i]["paid_date"] = $row['paid_date'];
            $income_arr[$i]["user_name"] = $row['user_name'];
            $income_arr[$i]["user_detail_name"] = $row['user_detail_name'];
            $income_arr[$i]["paid_amount"] = $row['paid_amount'];
            $income_arr[$i]["payment_method"] = $row['payment_method'];
            $income_arr[$i]["user_delete_status"] = $row['delete_status'];
            $income_arr[$i]["user_photo"] = $row['user_photo'];
            $i++;
        }
        return $income_arr;
    }

    public function getPayoutCountNew($user_names = [], $from = "", $to = "") {
        $this->db->from("amount_paid");
        $this->db->where('paid_type  =', 'released');
        $this->db->where('paid_status  !=', 'yes');
        $this->db->where(['paid_type' => 'released', 'paid_status !=' => "yes", "payment_method" => 'Bank Transfer']);
        if(!empty($user_names)) {
            $this->db->join('ft_individual as ft', 'ft.id = amount_paid.paid_user_id', 'LEFT');
            $this->db->where_in('ft.user_name', $user_names);
        }
        if($from != '' && $to != '') {
            $this->db->where('paid_date  >=', $from);
            $this->db->where('paid_date  <=', $to);
        } else if($from != '') {
            $this->db->where('paid_date  >=', $from);
        } else if($to != '') {
            $this->db->where('paid_date  <=', $to);
        }

        $count = $this->db->count_all_results();
        return $count;
    }

    public function getReleasedPayoutNew($user_names = [], $from, $to , $filter) {
        $this->db->select('paid_id, paid_amount, paid_date, ft.user_name, CONCAT(u.user_detail_name," ",u.user_detail_second_name) full_name, u.user_photo');
        $this->db->from('amount_paid');
        $this->db->join('ft_individual AS ft', 'ft.id = amount_paid.paid_user_id', 'LEFT');
        $this->db->join('user_details AS u', 'u.user_detail_refid = ft.id', 'LEFT');
        $this->db->where(['paid_type' => 'released', 'paid_status !=' => "yes", "payment_method" => 'Bank Transfer']);

        if(!empty($user_names)) {
            $this->db->where_in('ft.user_name', $user_names);
        }
         
        if($from != '') {
            $this->db->where('DATE_FORMAT(paid_date,"%Y-%m-%d")  >=', $from);
        } 
        if($to != '') {
            $this->db->where('DATE_FORMAT(paid_date,"%Y-%m-%d")  <=', $to);
        }
       
        $this->db->order_by($filter['order'], $filter['direction']);
        $this->db->limit($filter['limit'], $filter['start']);
        
        $query = $this->db->get();
        return $query->result_array();

    }

    public function getUserPayoutPayMethod($user_id)
    {
        return $this->db->select('payout_type')
                    ->where('user_detail_refid', $user_id)
                    ->get('user_details')->row('payout_type') ?: null;
    }
}
