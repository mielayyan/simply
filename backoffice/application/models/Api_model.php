<?php

class Api_model extends inf_model {

    public $mail;

    public function __construct() {
        parent::__construct();
        $this->load->model('validation_model');
        $this->load->model('Mail_model');
        $this->load->model('product_model');
        $ticketStatus = $this->MODULE_STATUS['ticket_system_status'] ?? '';
        if ($ticketStatus == 'yes') {
            $this->load->model('Ticket_system_model');
        }
    }
    
    public function getUserEwalletHistoryTableCount($user_id, $from_date, $to_date,$type){

        $details = array();
        if ($user_id != '') {
            $this->db->where('to_user_id', $user_id);
        }
        if ($from_date != '') {
            $this->db->where("date >=", $from_date);
        }
        if ($to_date != '') {
            $this->db->where("date <=", $to_date);
        }
        if ($type != '') {
            $this->db->where_in('amount_type',$type);
        }
        return $this->db->count_all_results('fund_transfer_details');
    }
    public function getEwalletHistoryForMobile($user_id, $page, $limit) {

        $from_user_amount_types = [
            'referral',
            'level_commission',
            'repurchase_level_commission',
            'upgrade_level_commission',
            'xup_commission',
            'xup_repurchase_level_commission',
            'xup_upgrade_level_commission',
            'matching_bonus',
            'matching_bonus_purchase',
            'matching_bonus_upgrade',
        ];

        $this->db->select('e.ewallet_type,e.amount,e.amount_type,e.type,e.date_added,e.transaction_id,e.transaction_note,e.transaction_fee,f.user_name as from_user');
        $this->db->from('ewallet_history as e');
        $this->db->join('ft_individual as f', 'e.from_id = f.id', 'left');
        $this->db->where('e.user_id', $user_id);
        $this->db->limit($limit, $page);
        $this->db->order_by('e.id');
        $res = $this->db->get();
        $i = 0;
        $data = array();
        if ($res->num_rows() > 0) {
            foreach ($res->result_array() as $row) {
                $row['amount'] = $this->convertToDefaultCurrency($row['amount']);
                $row["date_added"] = date("d-M-Y h:i A", strtotime($row["date_added"]));
                $data[$i] = $row;
                if ($row['ewallet_type'] == "fund_transfer") {
                    if ($row['amount_type'] == "user_credit") {
                        $data[$i]['description'] = lang('transfer_from') . " " . $row['from_user'] . " " . lang('transaction_id') . " :" . $row['transaction_id'];
                    } else if ($row['amount_type'] == "user_debit") {
                        $data[$i]['description'] = lang('fund_transfer_to') . " " . $row['from_user'] . " " . lang('transaction_id') . " :" . $row['transaction_id'];
                    } else if ($row['amount_type'] == "admin_credit") {
                        $data[$i]['description'] = lang('admin_credit') . " " . $row['from_user'] . " " . lang('transaction_id') . " :" . $row['transaction_id'];
                    } else if ($row['amount_type'] == "admin_debit") {
                        $data[$i]['description'] = lang('deducted_by') . " " . $row['from_user'] . " " . lang('transaction_id') . " :" . $row['transaction_id'];
                    } else {
                        $data[$i]['description'] = $row['amount_type'];
                    }
                } else if ($row['ewallet_type'] == "commission") {
                    if($row['amount_type'] == "donation") {
                        if($row['type'] == "debit") {
                            $data[$i]['description'] = lang('donation_debit') . $row['from_user'];
                        } else {
                            $data[$i]['description'] = lang('donation_credit') . $row['from_user'];
                        }
                    } else if($row['amount_type'] == 'board_commission' && $this->MODULE_STATUS['table_status'] == 'yes') {
                         $data[$i]['description'] = lang('table_commission');
                    } else {
                        if (in_array($row['amount_type'], $from_user_amount_types)) {
                            $data[$i]['description'] = lang($row['amount_type']) . lang('from') . $row['from_user'];
                        } else {
                            $data[$i]['description'] = lang($row['amount_type']);
                        }
                    }
                } else if ($row['ewallet_type'] == "ewallet_payment") {
                    if ($row['amount_type'] == "registration") {
                        $data[$i]['description'] = lang('deducted_for_registration_of') . " " . $row['from_user'];
                    } else if ($row['amount_type'] == "repurchase") {
                        $data[$i]['description'] = lang('deducted_for_repurchase_by') . " " . $row['from_user'];
                    } else if ($row['amount_type'] == "package_validity") {
                        $data[$i]['description'] = lang('deducted_for_membership_renewal_of') . " " . $row['from_user'];
                    } else {
                        $data[$i]['description'] = $row['amount_type'];
                    }
                } else if ($row['ewallet_type'] == "payout") {
                    if ($row['amount_type'] == "payout_request") {
                        $data[$i]['description'] = lang('deducted_for_payout_request');
                    } else if ($row['amount_type'] == "payout_release") {
                        $data[$i]['description'] = lang('payout_released_for_request');
                    } else if ($row['amount_type'] == "payout_delete") {
                        $data[$i]['description'] = lang('credited_for_payout_request_delete');
                    } else if ($row['amount_type'] == "payout_release_manual") {
                        $data[$i]['description'] = lang('payout_released_by_manual');
                    } else if ($row['amount_type'] == "withdrawal_cancel") {
                        $data[$i]['description'] = lang('credited_for_waiting_withdrawal_cancel');
                    } else {
                        $data[$i]['description'] = $row['amount_type'];
                    }
                } else if ($row['ewallet_type'] == "pin_purchase") {
                    if ($row['amount_type'] == "pin_purchase") {
                        $data[$i]['description'] = lang('deducted_for_pin_purchase');
                    } else if ($row['amount_type'] == "pin_purchase_delete") {
                        $data[$i]['description'] = lang('credited_for_pin_purchase_delete');
                    } else if($row['amount_type'] = "pin_purchase_refund") {
                        $data[$i]['description'] = lang('credited_for_pin_purchase_refund');
                    } else {
                        $data[$i]['description'] = $row['amount_type'];
                    }
                } else if($row['ewallet_type'] == "package_purchase") {
                    if($row['amount_type'] == "purchase_donation") {
                        $data[$i]['description'] = lang('purchase_donation') . lang('from') . $row['from_user'];
                    } else {
                        $data[$i]['description'] = $row['amount_type'];
                    }
                } else {
                    $data[$i]['description'] = $row['amount_type'];
                }

                $i++;
            }
        }
        return $data;
    }

    // public function getAllCountries() {
    //     $detail = array();
    //     $this->db->select('country_id as id,country_name as name,phone_code');
    //     $this->db->from('infinite_countries');
    //     $this->db->order_by('country_name', "ASC");
    //     $res = $this->db->get();
    //     foreach ($res->result_array() as $row) {
    //         $detail[] = $row;
    //     }
    //     return $detail;
    // }
    public function getAllCountries() {
        $detail = array();
        $this->db->select('country_id as value,country_name as title,country_name as code,phone_code');
        $this->db->from('infinite_countries');
        $this->db->order_by('country_name', "ASC");
        $res = $this->db->get();
        foreach ($res->result_array() as $row) {
            $detail[] = $row;
        }
        return $detail;
    }

    public function getStatesFromCountry($country_id) {
        $state_array = array();
        $this->db->select('state_id as value, state_name as title,state_name as code');
        $this->db->where('country_id', $country_id);
        $this->db->order_by('state_name', "ASC");
        $this->db->from('infinite_states');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $state_array[] = $row;
        }
        if(empty($state_array)) {
            $state_array[] = array(
            );
        }
        return $state_array;
    }

    public function checkCurrencyCode($c_code) {
        $detail = array();
        $this->db->from('currency_details');
        $this->db->select('id');
        $this->db->where('code', $c_code);
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $detail['id'] = $row['id'];
        }
        return $detail;
    }

    public function changeDefaultCurrency($user_id, $c_id) {
        $this->db->set('default_currency', $c_id);
        $this->db->where('id', $user_id);
        $result = $this->db->update('ft_individual');
        return $result;
    }

    public function getAllCurrency() {
        $where = [
            'status' => 'enabled',
            'delete_status' => 'yes'
        ];
        return $this->db->select('id, code as c_code,value as c_value,symbol_left as c_symbol')
            ->where($where)
            ->from('currency_details')->get()->result_array();
    }

    public function getUserDefaultCurrency($user_id) {
        $this->db->select('c.*');
        $this->db->from('ft_individual as f');
        $this->db->join('currency_details c', 'f.default_currency = c.id');
        $this->db->where('f.id', $user_id);
        $this->db->limit(1);
        $array = $this->db->get()->result_array();
        if(count($array))
            return $array;
        return [];
    }

    public function getTicketData($user_id, $limit, $offset, $ticket_id = '', $resolved_status = '', $status = 0, $category = 0, $priority = 0) {
        $ticket_arr = array();
        $this->db->select('*');
        $this->db->where('user_id', $user_id);
        if ($resolved_status != '') {
            $this->db->where('status', $resolved_status);
        }
        if ($ticket_id != '') {
            $this->db->where('trackid', $ticket_id);
            ;
        }
        if ($status != 0)
            $this->db->where('status', $status);
        if ($priority != 0)
            $this->db->where('priority', $priority);
        if ($category != 0)
            $this->db->where('category', $category);
        $this->db->from('ticket_tickets');
        $this->db->order_by('lastchange', 'desc');

        if ($limit != 0)
            $this->db->limit($limit, $offset);
        $res = $this->db->get();
        $i = 0;
        foreach ($res->result_array() as $row) {
            $ticket_arr[$i]['id'] = $row['id'];
            $ticket_arr[$i]['read'] = 1;
            $this->db->select('read');
            $this->db->where('replyto', $row['id']);
            $this->db->from('ticket_replies');
            $this->db->limit(1);
            $res1 = $this->db->get();
            foreach ($res1->result_array() as $row1) {
                if ($row1['read'] == 0)
                    $ticket_arr[$i]['read'] = $row1['read'];
            }
            $ticket_arr[$i]['ticket_id'] = $row['trackid'];
            $ticket_arr[$i]['status'] = $this->Ticket_system_model->getStatus($row['status']);
            $ticket_arr[$i]['status_no'] = $row['status'];
            $ticket_arr[$i]['created_date'] = $row['dt'];
            $ticket_arr[$i]['updated_date'] = $row['lastchange'];
            $ticket_arr[$i]['subject'] = $row['subject'];
            $ticket_arr[$i]['attachments'] = $row['attachments'];
            $ticket_arr[$i]['user'] = $this->validation_model->IdToUserName($row['user_id']);
            if ($row['last_replier_type'] != "employee")
                $ticket_arr[$i]['lastreplier'] = $this->validation_model->IdToUserName($row['lastreplier']);
            else {
                $ticket_arr[$i]['lastreplier'] = $this->validation_model->EmployeeIdToUserName($row['lastreplier']);
            }
            $ticket_arr[$i]['category'] = $this->Ticket_system_model->getCategory($row['category']);
            $ticket_arr[$i]['priority'] = $row['priority'];
            $ticket_arr[$i]['priority_name'] = $this->Ticket_system_model->getPriority($row['priority']);
            $ticket_arr[$i]['name'] = $row['name'];
            $i++;
        }
        return $ticket_arr;
    }

    function createNewTicket($ticket) {
        $admin_id = $this->validation_model->getAdminId();
        $admin_name = $this->validation_model->getAdminUsername($admin_id);
        $user_name = $this->LOG_USER_NAME;
        global $hesk_settings, $hesklang, $hesk_db_link;
        $data_ticket = array(
            'trackid' => $ticket['trackid'],
            'name' => $user_name,
            'email' => '',
            'user_id' => $ticket['user_id'],
            'category' => $ticket['category'],
            'priority' => $ticket['priority'],
            'assignee_id' => $admin_id,
            'assignee_name' => $admin_name,
            'assignee_read_ticket' => "no",
            'subject' => $ticket['subject'],
            'message' => $ticket['message'],
            'dt' => date("Y-m-d H:i:s"),
            'lastchange' => date("Y-m-d H:i:s"),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'language' => '',
            'status' => '1',
            'owner' => '0',
            'attachments' => $ticket['file_name'],
            'merged' => 'ss',
            'history' => 'ss'
        );

        $res = $this->db->insert('ticket_tickets', $data_ticket);
        return $res;
    }

    function get_ticket_details($user_id, $ticket_id) {
        $array = $this->getTicketData($user_id, 0, 0, $ticket_id);
        if (isset($array[0]))
            return $array[0];
        return array();
    }

    public function getAllReply($ticket_id, $limit, $offset) {
        $details = array();

        $this->db->select('*');
        $this->db->order_by('id', 'asc');
        $this->db->from('ticket_replies');
        $this->db->where('replyto', $ticket_id);
        $this->db->limit($limit, $offset);
        $res = $this->db->get();
        $i = 0;
        foreach ($res->result_array() as $row) {

            $details[$i]['message'] = html_entity_decode($row['message']);
            $details[$i]['reply_user_type'] = $row['reply_user_type'];
            if ($row['reply_user_type'] != 'employee') {

                $details[$i]['profile_pic'] = $this->validation_model->getProfilePicture($row['user_id']);
            } else {
                $details[$i]['profile_pic'] = 'nophoto.jpg';
            }
            $details[$i]['date'] = $row['dt'];
            $details[$i]['attachments'] = $row['attachments'];
            $details[$i]['user'] = $this->validation_model->IdToUserName($row['user_id']);
            $details[$i]['user_id'] = $row['user_id'];
            $i++;
        }
        return $details;
    }

    public function getUserPassword($username) {
        $password = NULL;
        $this->db->select('password');
        $this->db->from('ft_individual');
        $this->db->where('user_name', $username);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $password = $row->password;
        }
        return $password;
    }

    public function replyTicket($ticket_table_id, $message, $user_id, $file_name, $replier_type = '') {
        $data_ticket = array(
            'replyto' => $ticket_table_id,
            'user_id' => $user_id,
            'reply_user_type' => $replier_type,
            'message' => $message,
            'attachments' => $file_name,
            'dt' => date('Y-m-d H:i:s'),
            'read' => '1');
        $res = $this->db->insert('ticket_replies', $data_ticket);
        if ($res) {
            $this->db->set('lastreplier', $user_id);
            if ($replier_type != "")
                $this->db->set('last_replier_type', $replier_type);
            $this->db->set('lastchange', date('Y-m-d H:i:s'));
            $this->db->where('id', $ticket_table_id);
            $res = $this->db->update('ticket_tickets');
        }
        return $res;
    }

    public function get_id_from_ticket_track_id($track_id) {
        $this->db->select('id');
        $this->db->from('ticket_tickets');
        $this->db->where('trackid', $track_id);
        $res = $this->db->get();
        foreach ($res->result_array() as $row) {
            return $row['id'];
        }
        return 0;
    }

    public function validate_temp_user_mobile($mobile) {
        $return = FALSE;
        $this->db->where('user_detail_mobile', $mobile);
        $count = $this->db->count_all_results('user_details');
        if (!$count) {
            $date = date('Y-m-d H:i:s');
            $this->db->where('expire_date >=', $date);
            $this->db->where('status', 'pending');
            $this->db->where('user_detail_mobile', $mobile);
            $res = $this->db->count_all_results('temp_registeration');
            if (!$res)
                $return = TRUE;
        }
        return $return;
    }

    public function confirmTempUserRegister($reg_arr) {
        $this->load->model('register_model');
        $this->load->model('misc_model');
        $temp_regr_id = 0;
        $first_name = $reg_arr['first_name'];
        $last_name = $reg_arr['last_name'];
        $mobile = $reg_arr['mobile'];
        $email = $reg_arr['email'];
        $sponser_id = $reg_arr['sponser_id'];
        $join_date = date('Y-m-d H:i:s');
        $this->begin();
// add 3 days to date
        $expire_date = Date('Y-m-d 23:59:59', strtotime("+3 days"));
        do {
            $sponser_code = $this->misc_model->getRandStr(9, 9);
        } while (!$this->register_model->checkSponserCode($sponser_code));
        $reg_arr['sponser_code'] = $sponser_code;
        $this->db->set("user_details_ref_user_id", $sponser_id);
        $this->db->set("sponser_code", $sponser_code);
        $this->db->set("user_detail_name", $first_name);
        $this->db->set("user_detail_second_name", $last_name);
        $this->db->set("user_detail_mobile", $mobile);
        $this->db->set("user_detail_email", $email);
        $this->db->set("expire_date", $expire_date);
        $this->db->set("join_date", $join_date);
        $this->db->set("status", "pending");
        $query = $this->db->insert("temp_registeration");

        $temp_regr_id = $this->db->insert_id();
        if ($query) {
            $res = $this->register_model->generateSponserCode($reg_arr);
            if ($res) {
                $this->commit();
            } else {
                $this->rollBack();
                return FALSE;
            }
        } else {
            $this->rollBack();
            return FALSE;
        }
        return $temp_regr_id;
    }

    public function getTempUserSponserCodeDetails($temp_regr_id) {
        $sponser_code_details = array();

        $this->db->select('tr.*,sc.reg_user_id');
        $this->db->from('temp_registeration as tr');
        $this->db->where('tr.id', $temp_regr_id);
        $this->db->join('sponser_codes as sc', 'tr.sponser_code = sc.sponser_code', 'LEFT');
        $this->db->order_by("tr.join_date", "desc");
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $sponser_code_details['id'] = $row['id'];
            $sponser_code_details['sponser_code'] = $row['sponser_code'];
            $sponser_code_details['first_name'] = $row['user_detail_name'];
            $sponser_code_details['second_name'] = $row['user_detail_second_name'];
            $sponser_code_details['mobile_no'] = $row['user_detail_mobile'];
            $sponser_code_details['email'] = $row['user_detail_email'];
            $sponser_code_details['status'] = $row['status'];
            $sponser_code_details['join_date'] = $row['join_date'];
            $sponser_code_details['expire_date'] = $row['expire_date'];
            $sponser_code_details['reg_user_name'] = "";
            if ($row['reg_user_id'])
                $sponser_code_details['reg_user_name'] = $this->validation_model->IdToUserName($row['reg_user_id']);
        }
        return $sponser_code_details;
    }

    function getCommisionDetails($type, $from_date, $to_date, $user_id, $limit, $offset) {
        $i = 0;
        $details = array();
        $this->db->select('user_id,from_id,amount_type,date_of_submission');
        $this->db->select("SUM(total_amount) as total_amount");
        $this->db->select("SUM(amount_payable) as amount_payable");
        $this->db->from('leg_amount');
        if ($type) {
            $this->db->where_in('amount_type', $type);
        }
        if($from_date)
            $this->db->where("date_of_submission >=", $from_date);
        if($to_date)
            $this->db->where("date_of_submission <=", $to_date);
        if ($user_id) {
            $this->db->where("user_id", $user_id);
        }
        $this->db->limit($limit, $offset);
        $this->db->group_by('amount_type');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $view_amt = $this->validation_model->getViewAmountType($row['amount_type']);
            $details[] = [
                "user_name" => $this->validation_model->IdToUserName($row['user_id']),
                'full_name'=> $this->validation_model->getFullName($row['user_id']),
                'amount_type'=> $row['amount_type'],
                'view_amt'=> $view_amt,
                'date' => $row['date_of_submission'],
                'total_amount' => $this->convertToDefaultCurrency($row['total_amount']),
                'amount_payable' => $this->convertToDefaultCurrency($row['amount_payable'])
            ];
            
        }
        return $details;
    }

    function get_ewallet_balance($user_id) {
        $this->db->select('balance_amount');
        $this->db->where('user_id', $user_id);
        $res = $this->db->get('user_balance_amount');
        foreach ($res->result_array() as $row) {
            return round($row['balance_amount'], 2);
        }
        return 0;
    }

    public function checkEmail($user_id, $e_mail) {
        $mail_db = '';
        $flag = FALSE;
        if ($user_id != "" && $e_mail != "") {
            $this->db->select("user_detail_email");
            $this->db->from("user_details");
            $this->db->where("user_detail_refid", $user_id);
            $query = $this->db->get();
            foreach ($query->result() as $row) {
                $mail_db = $row->user_detail_email;
            }

            if ($e_mail == $mail_db) {
                $flag = TRUE;
            }
        }
        return $flag;
    }

    public function sendEmail($user_id, $tranpass) {

        $send_details = array();
        $send_details['user_id'] = $user_id;
        $type = 'send_tranpass';
        $email = $this->validation_model->getUserEmailId($user_id);
        $send_details['full_name'] = $this->validation_model->getUserFullName($user_id);
        $send_details['email'] = $email;
        $send_details['tranpass'] = $tranpass;
        $send_details['first_name'] = $this->validation_model->getUserData($user_id, "user_detail_name");
        $send_details['last_name'] = $this->validation_model->getUserData($user_id, "user_detail_second_name");
        return $this->mail_model->sendAllEmails($type, $send_details);
    }

    function get_money_wallet_balance($user_id) {
        $this->db->select('money_wallet');
        $this->db->where('user_id', $user_id);
        $res = $this->db->get('user_balance_amount');
        foreach ($res->result_array() as $row) {
            return round($row['money_wallet'], 2);
        }
        return 0;
    }

    public function getMoneyWalletHistoryForMobile($user_id, $page, $limit) {
        $this->db->select('e.ewallet_type,e.amount,e.amount_type,e.type,e.date_added,e.transaction_id,e.transaction_note,e.transaction_fee,f.user_name as from_user');
        $this->db->from('money_wallet_ewallet_history as e');
        $this->db->join('ft_individual as f', 'e.from_id = f.id', 'left');
        $this->db->where('e.user_id', $user_id);
        $this->db->limit($limit, $page);
        $this->db->order_by('e.id');
        $res = $this->db->get();
        $i = 0;
        $data = array();
        if ($res->num_rows() > 0) {
            foreach ($res->result_array() as $row) {
                $row['amount'] = $this->convertToDefaultCurrency($row['amount']);
                $row["date_added"] = date("d-M-Y h:i A", strtotime($row["date_added"]));
                $data[$i] = $row;
                if ($row['ewallet_type'] == "fund_transfer") {
                    if ($row['amount_type'] == "user_credit") {
                        $data[$i]['description'] = lang('transfer_from') . " " . $row['from_user'] . " " . lang('transaction_id') . " :" . $row['transaction_id'];
                    } else if ($row['amount_type'] == "user_debit") {
                        $data[$i]['description'] = lang('fund_transfer_to') . " " . $row['from_user'] . " " . lang('transaction_id') . " :" . $row['transaction_id'];
                    } else if ($row['amount_type'] == "admin_credit") {
                        $data[$i]['description'] = lang('admin_credit') . " " . $row['from_user'] . " " . lang('transaction_id') . " :" . $row['transaction_id'];
                    } else if ($row['amount_type'] == "admin_debit") {
                        $data[$i]['description'] = lang('deducted_by') . " " . $row['from_user'] . " " . lang('transaction_id') . " :" . $row['transaction_id'];
                    }
                } else if ($row['ewallet_type'] == "commission") {
                    $data[$i]['description'] = lang($row['amount_type']) . " from " . $row['from_user'];
                } else if ($row['ewallet_type'] == "ewallet_payment") {
                    if ($row['amount_type'] == "registration") {
                        $data[$i]['description'] = lang('deducted_for_registration_of') . " " . $row['from_user'];
                    } else if ($row['amount_type'] == "repurchase") {
                        $data[$i]['description'] = lang('deducted_for_repurchase_by') . " " . $row['from_user'];
                    } else if ($row['amount_type'] == "package_validity") {
                        $data[$i]['description'] = lang('deducted_for_membership_renewal_of') . " " . $row['from_user'];
                    }
                } else if ($row['ewallet_type'] == "payout") {
                    if ($row['amount_type'] == "payout_request") {
                        $data[$i]['description'] = lang('deducted_for_payout_request');
                    } else if ($row['amount_type'] == "payout_release") {
                        $data[$i]['description'] = lang('payout_released_for_request');
                    } else if ($row['amount_type'] == "payout_delete") {
                        $data[$i]['description'] = lang('credited_for_payout_request_delete');
                    } else if ($row['amount_type'] == "payout_release_manual") {
                        $data[$i]['description'] = lang('payout_released_by_manual');
                    } else if ($row['amount_type'] == "withdrawal_cancel") {
                        $data[$i]['description'] = lang('credited_for_waiting_withdrawal_cancel');
                    }
                } else if ($row['ewallet_type'] == "pin_purchase") {
                    if ($row['amount_type'] == "pin_purchase") {
                        $data[$i]['description'] = lang('deducted_for_pin_purchase');
                    } else if ($row['amount_type'] == "pin_purchase_delete") {
                        $data[$i]['description'] = lang('credited_for_pin_purchase_delete');
                    }
                }
                $i++;
            }
        }
        return $data;
    }

    public function getProductDetails1($product_id = '', $offset = 0, $limit = 0, $status = 'yes') {
        $product_details = array();

        $MODULE_STATUS = $this->trackModule();

        if ($MODULE_STATUS['opencart_status'] != "yes" || $MODULE_STATUS['opencart_status_demo'] != "yes") {
            $this->db->select('*');
            if ($product_id != '') {
                $this->db->where('product_id', $product_id);
            }
            if ($status != '') {
                $this->db->where('active', $status);
            }
            if ($limit != 0)
                $this->db->limit($limit, $offset);

            $query = $this->db->get('package');

            foreach ($query->result_array() as $row) {
                $product_details[] = $row;
            }
        } else {
            // $where = '';
            // if ($limit != 0)
            //     $this->db->limit($limit, $offset);

            // $query = $this->db->query('SELECT product_id,model AS product_name,pair_value,price AS product_value FROM ' . $this->db->ocprefix . 'product' . $where);
            $this->db->select('product_id,model AS product_name,pair_value,price AS product_value');
            $this->db->from('oc_product');
            if ($product_id != '') {
                $this->db->where('product_id', $product_id);
            }
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                $product_details[] = $row;
            }
        }

        return $product_details;
    }

    public function getRepurchaseReport($date1, $date2, $user_id, $limit, $offset) {
        $details = array();
        $start_date = $date1 . " 00:00:00";
        $to_date = $date2 . " 23:59:59";
        $this->db->select('*');
        $this->db->from('repurchase_order');
        $this->db->where('order_status', 'confirmed');
        $this->db->where('order_date >=', $start_date);
        $this->db->where('order_date <=', $to_date);
        $this->db->where("user_id", $user_id);
        $this->db->limit($limit, $offset);

        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {

            // $details["$i"]["id"] = $row['orde_id'];
            $details["$i"]["user_name"] = $this->validation_model->IdToUserName($row['user_id']);
            $details["$i"]["full_name"] = $this->validation_model->getUserFullName($row['user_id']);

            $details["$i"]["invoice_no"] = $row['invoice_no'];
            $details["$i"]["order_date"] = $row['order_date'];
            $details["$i"]["amount"] = $row['total_amount'];
            $details["$i"]["payment_method"] = lang($row['payment_method']);
            $details["$i"]["encrypt_order_id"] = $this->validation_model->encrypt($row['order_id']);
            $i++;
        }
        return $details;
    }

    public function getTokensReport($from_date, $to_date, $user_id, $offset, $limit) {
        $date1 = $from_date . " 00:00:00";
        $date2 = $to_date . " 23:59:59";
        $this->db->select("*");
        $this->db->from("user_token_history");
        $this->db->where("added_date >= ", $date1);
        $this->db->where("added_date <= ", $date2);
        $this->db->where("user_id", $user_id);
        $this->db->limit($limit, $offset);

        $result = $this->db->get();

        $token_details = array();
        $i = 0;

        foreach ($result->result_array() as $row) {
            $row['payment_method'] = lang($row['payment_method']);
            $token_details[$i] = $row;
            $token_details[$i]['user_name'] = $this->validation_model->IdToUserName($row['user_id']);
            $i++;
        }
        return $token_details;
    }

    public function getUserPurchaseAddress($user_id) {
        $this->db->select('id, pin, town, mobile, default_address, id address_id,name,address,pin zip_code,town city,mobile phone');
        $this->db->from('repurchase_address');
        $this->db->where('user_id', $user_id);
        $res = $this->db->get();
        return $res->result_array();
    }

    public function insert_repurchase_address($address) {
        $res1 = $res2 = false;
        $this->begin();

        $this->db->set('default_address', '0');
        $this->db->set('user_id', $address['user_id']);
        $res1 = $this->db->update('repurchase_address');

        $this->db->set('user_id', $address['user_id']);
        $this->db->set('name', $address['full_name']);
        $this->db->set('address', $address['address']);
        $this->db->set('pin', $address['pin_no']);
        $this->db->set('town', $address['city']);
        $this->db->set('mobile', $address['phone']);
        $this->db->set('default_address', '1');
        $this->db->insert('repurchase_address');
        $res2 = $this->db->insert_id();

        if ($res1 && $res2) {
            $this->commit();
            return $res2;
        } else {
            $this->rollBack();
            return false;
        }
    }

    public function delete_address($address_id,$user_id) {
        $this->db->set('delete_status', 'no');
        $this->db->where('id', $address_id);
        if($user_id)
            $this->db->where('user_id', $user_id);
        $this->db->update('repurchase_address');
        return ($this->db->affected_rows() > 0 ? true : false);
    }

    public function getUserDetails($user_id) {
        $this->load->model('home_model');
        $this->load->model('validation_model');
        $details = array();

        $session_data = $this->session->userdata('inf_logged_in');
        $table_prefix = $session_data['table_prefix'];

        $this->load->model('home_model');
        $this->load->model('epin_model');
        $this->load->model('country_state_model');

        $details['user_id'] = $user_id;
        $details['table_prifix'] = $table_prefix;
        $details['email'] = $this->validation_model->getUserEmailId($user_id);
        //sales
        $details['total_sales'] = $this->home_model->getSalesCount('', '', $user_id);
        $details['today_sales'] = $this->home_model->getSalesCount(date('Y-m-d') . " 00:00:00", date('Y-m-d') . " 23:59:59", $user_id);
        //JOINING DETAILS
        $details['total_joining'] = $this->home_model->totalJoiningUsers($user_id);
        $details['today_joining'] = $this->home_model->todaysJoiningCount($user_id);
        $details['balance_amount'] = $this->validation_model->getUserBalanceAmount($user_id);

        //AMOUNT DETAILS
        $details['total_amount'] = $this->home_model->getGrandTotalEwallet($user_id);
        $details['requested_amount'] = $this->home_model->getTotalRequestAmount($user_id);
        $details['total_request'] = $this->home_model->getGrandTotalEwallet($user_id);
        $details['total_released'] = $this->home_model->getTotalReleasedAmount($user_id);
        if ($details['total_released'] == '') {
            $details['total_released'] = 0;
        }
        if ($details['requested_amount'] == '') {
            $details['requested_amount'] = 0;
        }
        $details['today_released'] = $this->home_model->getPayoutDetails(date('Y-m-d') . " 00:00:00", date('Y-m-d') . " 23:59:59", $user_id);
        if ($details['today_released'] == '') {
            $details['today_released'] = 0;
        }
        //epin
        $details['total_pin'] = 0; //$this->epin_model->getAllPinCount($user_id);
        $details['used_pin'] = 0; //$this->epin_model->getUsedPinCount($user_id);
        $details['requested_pin'] = 0; //$this->epin_model->getRequestedPinCount($user_id);
        //mail
        $details['read_mail'] = $this->home_model->getAllReadMessages('user');
        $details['unread_mail'] = $this->home_model->getAllUnreadMessages('user');
        $details['mail_today'] = $this->home_model->getAllMessagesToday('user');
        $details['mail_total'] = $this->getAllMailCount($user_id);
        //user photo
        $details['photo_name'] = $this->userPhoto($user_id);
        $details['country'] = $this->country_state_model->getAllCountries();
        $details['full_name'] = $this->validation_model->getUserFullName($user_id);
        $currency_status = $this->validation_model->getModuleStatusByKey('multy_currency_status');
        if ($currency_status == 'no') {
            $details['selected_currency'] = array('c_code' => 'USD','c_value' => 1,'c_symbol' => '$','title' => "Dollar");
            $details['currency_details'] = [];
        } else {
            $details['selected_currency'] = $this->validation_model->getUserDefaultCurrency($user_id);
            $details['currency_details'] = $this->getAllCurrency();
        }

        $details['user_rank'] = $this->getUserRank($user_id);
        $details['version_code'] = '2';

        $details['admin_fullname'] = $this->Api_model->getAdminFullname();

        return $details;
    }

    public function userPhoto($user_id) {
        $user_photo = 'nophoto.jpg';
        $this->db->select('user_photo');
        $this->db->from('user_details');
        $this->db->where('user_detail_refid', $user_id);
        $this->db->limit(1);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $user_photo = $row->user_photo;
        }
        return $user_photo;
    }

    function get_package_cost($product_id) {
        $this->db->select('product_value');
        $this->db->where('product_id', $product_id);
        $this->db->from('package');
        $res = $this->db->get();
        foreach ($res->result_array() as $row) {
            return $row['product_value'];
        }
        return 0;
    }

    function getPackagePriceFromPackageId($prod_id) {
        $this->db->select('product_value');
        $this->db->where('prod_id', $prod_id);
        $this->db->from('package');
        $res = $this->db->get();
        foreach ($res->result_array() as $row) {
            return $row['product_value'];
        }
        return 0;
    }

    function getAllRepurchaseProducts($offset, $limit) {
        $this->db->select("*");
        $this->db->from("package");
        $this->db->where('type_of_package', "repurchase");
        $this->db->where('active', "yes");
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getAllMailCount($user_id) {
        $this->db->where('mailtoususer', $user_id);
        $this->db->where('status', 'yes');
        return $this->db->count_all_results('mailtouser');
    }

    public function checkMailexist($mail_id) {
        $this->db->from('mailtouser');
        $this->db->where('mailtousid', $mail_id);
        $this->db->where('status', 'yes');
        $count = $this->db->count_all_results();
        $this->db->from('mailtoadmin');
        $this->db->where('mailadid', $mail_id);
        $this->db->where('status', 'yes');
        $count = $count + $this->db->count_all_results();
        return $count;
    }

    public function deleteAdminMessage($msg_id) {
        $data = array(
            'status' => 'no'
        );
        $this->db->where('mailadid', $msg_id);
        $res = $this->db->update('mailtoadmin', $data);
        return $res;
    }

    public function deleteUserMessage($msg_id) {
        $data = array(
            'status' => 'no'
        );
        $this->db->where('mailtousid', $msg_id);
        $res = $this->db->update('mailtouser', $data);
        return $res;
    }

    function getUserAll() {
        $this->db->select('id, user_name');
        $this->db->from('ft_individual');
        $this->db->where('active !=', 'server');
        $this->db->where('user_type !=', 'admin');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getIncome($id, $page='', $limit='') {

        $return_array = array();

        $level_based_amount_type = [
            'level_commission',
            'repurchase_level_commission',
            'upgrade_level_commission',
            'xup_commission',
            'xup_repurchase_level_commission',
            'xup_upgrade_level_commission',
            'matching_bonus',
            'matching_bonus_purchase',
            'matching_bonus_upgrade',
        ];

        $from_user_amount_types = [
            'referral',
            'level_commission',
            'repurchase_level_commission',
            'upgrade_level_commission',
            'xup_commission',
            'xup_repurchase_level_commission',
            'xup_upgrade_level_commission',
            'matching_bonus',
            'matching_bonus_purchase',
            'matching_bonus_upgrade',
        ];

        $array = array();
        $tot_amount = 0;
        $this->db->select('amount_type,amount_payable,user_id,user_level,from_id');
        $this->db->from('leg_amount');
        $this->db->where('user_id', $id);
        $this->db->limit($limit, $page);
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $view_amt_type = $this->validation_model->getViewAmountType($row["amount_type"]);
            $array[$i]["amount_type"] = $view_amt_type;
            $array[$i]["amount_payable"] = $this->convertToDefaultCurrency($row["amount_payable"]);
            if ($row["from_id"] && in_array($row["amount_type"], $from_user_amount_types)) {
                $array[$i]["from_id"] = $this->validation_model->getUserName($row["from_id"]);
            } else {
                $array[$i]["from_id"] = "NA";
            }

            if (in_array($row["amount_type"], $level_based_amount_type)) {
                $array[$i]["user_level"] = $row["user_level"];
            } else {
                $array[$i]["user_level"] = "NA";
            }

            $tot_amount += $array[$i]["amount_payable"];
            $array[$i]['tot_amount'] = $tot_amount;
            $i++;
        }

        $return_array['income_details'] = $array;
        $return_array['available_balance'] = $tot_amount;
        return $return_array;
    }

    public function getCountEmail($email, $id = '') {
        $this->db->from('user_details');
        if ($id != '') {
            $this->db->where('user_detail_refid !=', $id);
        }
        $this->db->where('user_detail_email', $email);
        $count = $this->db->count_all_results();
        return $count;
    }

    public function getCountryIDFromName($country_name) {
        $country_id = 0;
        $this->db->select('country_id');
        $this->db->from('infinite_countries');
        $this->db->where('country_name', $country_name);
        $res = $this->db->get();
        foreach ($res->result() as $row) {
            $country_id = $row->country_id;
        }
        return $country_id;
    }

    public function getStateIdFromName($state_name) {
        $state_id = 0;
        if ($state_name == 'NA') {
            return 0;
        }
        $this->db->select("state_id");
        $this->db->from("infinite_states");
        $this->db->where('state_name', $state_name);
        $qr = $this->db->get();
        foreach ($qr->result() as $row) {
            $state_id = $row->state_id;
        }
        return $state_id;
    }

    public function getReferalDetails($user_id, $table_prefix = NULL) {

        $this->load->model('country_state_model');
        $arr = array();
        if ($user_id != NULL) {

            $this->db->select('user_detail_refid');
            $this->db->select('user_detail_name');
            $this->db->select('join_date');
            $this->db->select('user_detail_email');
            $this->db->select('user_detail_country');
            $this->db->from('user_details');
            $this->db->where('user_details_ref_user_id', $user_id);
            $query = $this->db->get();

            $i = 0;
            foreach ($query->result_array() as $row) {
                $user_id = $row['user_detail_refid'];
                $arr[$i]['user_name'] = $this->validation_model->IdToUserName($user_id);
                $arr[$i]['name'] = $row['user_detail_name'];
                $arr[$i]['join_date'] = $row['join_date'];
                $arr[$i]['email'] = $row['user_detail_email'];
                $arr[$i]['country'] = $this->country_state_model->getCountryNameFromId($row['user_detail_country']);
                $i++;
            }

            for ($j = 0; $j < count($arr); $j++) {
                if ($arr[$j]['email'] == NULL)
                    $arr[$j]['email'] = 'NA';
                if ($arr[$j]['country'] == NULL)
                    $arr[$j]['country'] = 'NA';
            }
            return $arr;
        }
    }

    public function getProductData($type = 'registration') {

        $this->load->model('product_model');

        $product_details = [];
        $product_details = $this->product_model->getAllProducts('yes','registration');

        return $product_details;
    }

    public function updateUserProfileImage($user_id, $imagename) {
        $this->db->set('user_photo', $imagename);
        $this->db->where('user_detail_refid', $user_id);
        $result = $this->db->update('user_details');
        return $result;
    }

    public function getUserDetailsNames($user_id) {
        $details = array();
        $this->db->select('user_detail_name,user_detail_second_name');
        $this->db->from('user_details');
        $this->db->where('user_detail_refid', $user_id);
        $this->db->limit(1);
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $details['first_name'] = $row['user_detail_name'];
            $details['last_name'] = $row['user_detail_second_name'];
        }
        return $details;
    }

    public function sendAllEmails($type = 'notification', $regr = array(), $attachments = array(), $user_id, $email) {

        $attachments = array(BASEPATH . "../public_html/images/logos/logo.png");
        $this->load->library('inf_phpmailer', NULL, 'phpmailer');

        $this->load->model('login_model');
        $keyword = $this->login_model->getKeyWord($user_id);
        $site_info = $this->validation_model->getSiteInformation();
        $common_mail_settings = $this->configuration_model->getMailDetails();
        $reset_password = $this->resetUserPassword($user_id, $keyword);
        //$mail_type = $common_mail_settings['reg_mail_type']; //normal/smtp
        $mail_type = 'normal'; //normal/smtp
        $smtp_data = array();
        if ($mail_type == "smtp") {
            $smtp_data = array(
                "SMTPAuth" => $common_mail_settings['smtp_authentication'],
                "SMTPSecure" => ($common_mail_settings['smtp_protocol'] == "none") ? "" : $common_mail_settings['smtp_protocol'],
                "Host" => $common_mail_settings['smtp_host'],
                "Port" => $common_mail_settings['smtp_port'],
                "Username" => $common_mail_settings['smtp_username'],
                "Password" => $common_mail_settings['smtp_password'],
                "Timeout" => $common_mail_settings['smtp_timeout'],
                    //"SMTPDebug" => 3 //uncomment this line to check for any errors
            );
        }
        $mail_to = array("email" => $email, "name" => $regr['first_name'] . " " . $regr['last_name']);
        $mail_from = array("email" => $site_info['email'], "name" => $site_info['company_name']);
        $mail_reply_to = $mail_from;
        $mail_subject = "Notification";

        $mailBodyHeaderDetails = $this->getHeaderDetails($site_info);
        $mail_altbody = html_entity_decode('password_recovery');
        $mailBodyDetails = '<body>
<table border="0" width="800" height="700" align="center">
<tr>
<td    colspan="4"valign="top" ><br><br><br>
<br>
<font size="3" face="Trebuchet MS">
Dear  Customer,</b><br>
     <p syte="pading-left:20px;"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Your password sucessfully changed.Your new password is ' . $keyword . '.Please use this password to login your account:<p>

  <br><br><br>
  </td>
</tr>
</font>
</table>
</body>';
        $mail_subject = 'Password Recovery';
        $mailBodyFooterDetails = $this->getFooterDetails($site_info);
        $mail_body = $mailBodyHeaderDetails . $mailBodyDetails . $mailBodyFooterDetails . "</br></br></br></br></br>";


        $send_mail = $this->phpmailer->send_mail($mail_from, $mail_to, $mail_reply_to, $mail_subject, $mail_body, $mail_altbody, $mail_type, $smtp_data, $attachments);

        if (!$send_mail['status'] || !$reset_password) {
            $data["message"] = "Error: " . $send_mail['ErrorInfo'];
        } else {
            $data["message"] = "Message sent correctly!";
        }


        return $send_mail;
    }

    public function getHeaderDetails($site_info) {
        $current_date = date('M d,Y H:i:s');
        $company_address = $site_info['company_address'];
        $company_name = $site_info['company_name'];
        $site_logo = $site_info['logo'];

        $mailBodyHeaderDetails = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>' . $company_name . '</title>
    </head>
    <body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;">

        <div style="width: 680px;">

            <div style="padding: 20px; border:solid 5px #ccc; ">

                <div style="width:100%; left:0px; float:left;">

                    <a href=" " title="' . $company_name . '">
                        <img src="' . $this->BASE_URL . 'public_html/images/logos/' . $site_logo . '" alt="' . $company_name . '" style="margin-bottom:-6px; border: none; margin-right:-100px; margin-bottom:5px;float:left;" />
                    </a>
                    <br><br></br></br><br><br>
                    <span style="color:rgb(225, 0, 0);float:left;text-align:left;position:relative;">
                        <b style=" word-break: break-all;">' . $company_name . "</br>" . $company_address . '</b>
                        <br><font color="blue">' . $current_date . '</font>
                    </span>
                </div>
                <hr>
                <table style="margin-bottom: 20px;">
                    <tbody>
                        <tr>
                            <td style="font-size: 12px;text-align: left; padding: 7px;">';
        return $mailBodyHeaderDetails;
    }

    public function getFooterDetails($site_info) {
        $company_name = $site_info['company_name'];
        $company_mail = $site_info['email'];
        $company_phone = $site_info['phone'];
        $mailBodyFooterDetails = '</td>
                            </tr>
                        </tbody>
                    </table>
                    </br><b>Sincerely</b></br></br>Admin</b></br>.
                    <hr>
                    <p style="margin-top: 0px; margin-bottom: 20px; font-size:small;">
               Please do not reply to this email. This mailbox is not monitored and you will not receive a response. For all other    questions please contact our member support department by email <a href="mailto:' . $company_mail . '">' . $company_mail . '</a    >     or by phone at ' . $company_phone . '.</br></br></p>

                    <p style="margin-top: 0px; margin-bottom: 20px; text-align : center;">Copyright &copy; ' . date("Y") . '&nbsp;<a href="' . $this->BASE_URL . '">' . $company_name . '</a> &nbsp;All Rights Reserved.
                    </p>
                </div>
            </div>
        </body>
    </html>';

        return $mailBodyFooterDetails;
    }

    public function resetUserPassword($user_id, $keyword) {
        $password = password_hash($keyword, PASSWORD_DEFAULT);
        $user_type = $this->LOG_USER_TYPE;
        $this->db->set("password", $password);
        $this->db->where("id", $user_id);
        $result_1 = $this->db->update("ft_individual");
        if ($user_type == 'admin' && DEMO_STATUS == 'yes') {
            $this->db->set("pswd", $password);
            $this->db->where("id", $user_id);
            $res = $this->db->update("infinite_mlm_user_detail");
            // $res = $this->db->query("update infinite_mlm_user_detail SET pswd ='$password' WHERE id='$user_id'");
        }

        $this->db->set("reset_status", 'yes');
        $this->db->where("keyword", $keyword);
        $result_2 = $this->db->update("password_reset_table");
        if ($result_1 && $result_2) {
            return 1;
        } else {
            return 0;
        }
    }

    public function checkSponsorExist($user_id) {
        $this->db->from('ft_individual');
        $this->db->where('id', $user_id);
        $count = $this->db->count_all_results();
        return $count;
    }

    public function get_language_status() {
        $lang_arr = array();

        $this->db->select('lang_status,lang_status_demo');
        $query1 = $this->db->get('module_status');
        $multi_lang_status = $query1->row_array();

        return ($multi_lang_status['lang_status'] == 'yes' && $multi_lang_status['lang_status_demo'] == 'yes');
    }

    public function getProfileDetails($user_id, $product_status = '') {
        $module_status = $this->trackModule();
        $this->db->select('*');
        $this->db->from('user_details AS u');
        $this->db->join('ft_individual AS f', 'u.user_detail_refid = f.id', 'INNER');
        $this->db->where('user_detail_refid', $user_id);
        $result = $this->db->get();
        $result_array = $result->result_array();

        $profile_details = $this->OrganiseUserDetails($result_array);
        $profile_arr['details'] = $profile_details['detail1'];
        $profile_arr['sponser'] = $this->validation_model->getSponserIdName($user_id);
        if ($this->MODULE_STATUS['product_status'] == "yes") {
            $profile_arr['product_name'] = $this->product_model->getPackageNameFromPackageId($profile_arr['details']['product_id'], $module_status, 'registration');
            $profile_arr['product_validity'] = $profile_details['detail1']['product_validity'];
        }
        return $profile_arr;
    }

    public function OrganiseUserDetails($result_array) {
        $this->load->model('country_state_model');
        $user_detail = array();

        $i = 1;
        foreach ($result_array as $row) {
            $user_detail["detail$i"]["id"] = $row["user_detail_refid"];
            $user_detail["detail$i"]["name"] = $row["user_detail_name"];
            $user_detail["detail$i"]["second_name"] = $row["user_detail_second_name"];
            $user_detail["detail$i"]["address"] = $row["user_detail_address"];
            $user_detail["detail$i"]["position"] = $row["position"];
            $user_detail["detail$i"]["country_id"] = $row["user_detail_country"];
            $user_detail["detail$i"]["state_id"] = $row["user_detail_state"];
            $user_detail["detail$i"]["pincode"] = $row["user_detail_pin"];
            $user_detail["detail$i"]["mobile"] = $row["user_detail_mobile"];
            $user_detail["detail$i"]["land"] = $row["user_detail_land"];
            $user_detail["detail$i"]["user_detail_second_name"] = $row["user_detail_second_name"];
            $user_detail["detail$i"]["user_detail_address2"] = $row["user_detail_address2"];
            $user_detail["detail$i"]["user_detail_city"] = $row["user_detail_city"];
            $user_detail["detail$i"]["email"] = $row["user_detail_email"];
            $user_detail["detail$i"]["dob"] = $row["user_detail_dob"];
            $user_detail["detail$i"]["gender"] = $row["user_detail_gender"];
            $user_detail["detail$i"]["acnumber"] = $row["user_detail_acnumber"];
            $user_detail["detail$i"]["ifsc"] = $row["user_detail_ifsc"];
            $user_detail["detail$i"]["nbank"] = $row["user_detail_nbank"];
            $user_detail["detail$i"]["user_detail_nacct_holder"] = $row["user_detail_nacct_holder"];
            $user_detail["detail$i"]["nbranch"] = $row["user_detail_nbranch"];
            $user_detail["detail$i"]["pan"] = $row["user_detail_pan"];
            $user_detail["detail$i"]["level"] = $row["user_level"];
            $user_detail["detail$i"]["date"] = $row["join_date"];
            $user_detail["detail$i"]["referral"] = $row["sponsor_id"];
            $user_detail["detail$i"]["acnumber"] = $row["user_detail_acnumber"];
            $user_detail["detail$i"]["ifsc"] = $row["user_detail_ifsc"];
            $user_detail["detail$i"]["nbank"] = $row["user_detail_nbank"];
            $user_detail["detail$i"]["user_detail_nacct_holder"] = $row["user_detail_nacct_holder"];
            $user_detail["detail$i"]["nbranch"] = $row["user_detail_nbranch"];
            $user_detail["detail$i"]["pan"] = $row["user_detail_pan"];

            $user_detail["detail$i"]["blocktrail_account"] = $this->encryptDecrypt($row['bitcoin_address'],"decryption");
            $user_detail["detail$i"]["paypal_account"] = $this->encryptDecrypt($row['user_detail_paypal'],"decryption");
            $user_detail["detail$i"]["blockchain_account"] = $this->encryptDecrypt($row['user_detail_blockchain_wallet_id'],"decryption");
            $user_detail["detail$i"]["bitgo_account"] = $this->encryptDecrypt($row['user_detail_bitgo_wallet_id'],"decryption");
            $user_detail["detail$i"]["payout_type"] = $row['payout_type'];

            $user_detail["detail$i"]["facebook"] = $row["user_detail_facebook"];
            $user_detail["detail$i"]["twitter"] = $row["user_detail_twitter"];
            $user_detail["detail$i"]["product_id"] = $row["product_id"];
            $user_detail["detail$i"]["product_validity"] = $row["product_validity"];
            $user_detail["detail$i"]["country"] = $this->country_state_model->getCountryNameFromId($row['user_detail_country']);
            $user_detail["detail$i"]["state"] = $this->country_state_model->getStateNameFromId($row["user_detail_state"]);
            $user_detail["detail$i"]["father_name"] = $this->validation_model->getFullName($row["father_id"]);
            $user_detail["detail$i"]["sponsor_name"] = $this->validation_model->getFullName($row["sponsor_id"]);

            $file_name = $this->getUserPhoto($row["user_detail_refid"]);
            if (!file_exists(IMG_DIR . 'profile_picture/' . $file_name)) {
                $file_name = 'nophoto.jpg';
            }
            $user_detail["detail$i"]["profile_photo"] = $file_name;
            $user_detail["detail$i"]["bank_info_required"] = $row["bank_info_required"];
            $user_detail["detail$i"]["payout_type"] = $row["payout_type"];

            $i++;
        }

        return $this->replaceNullFromArray($user_detail, "NA");
    }

    public function encryptDecrypt($value, $type){
        $key = $this->config->item('encryption_key');
        $rs = null;
        if(!empty($value)){
        if($type == "encryption"){
            $rs = $this->encryption->encrypt($value);
        }
        if($type == "decryption"){
            $rs = $this->encryption->encrypt($value);
        }
        }
        return $rs;
    }

    public function getUserPhoto($user_id) {
        $this->db->select('user_photo');
        $this->db->from('user_details');
        $this->db->where('user_detail_refid', $user_id);
        $res = $this->db->get();
        foreach ($res->result() as $row) {
            return $row->user_photo;
        }
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

    public function getReleasedAmount($user_id = "", $start_date, $end_date) {
        $released_amount = 0;
        $this->db->select_sum('paid_amount');
        $this->db->where('paid_type', 'released');
        $where = "paid_date between '$start_date' and '$end_date'";
        $this->db->where($where);
        if ($user_id != "")
            $this->db->where('paid_user_id', $user_id);
        $query = $this->db->get('amount_paid');
        foreach ($query->result() as $row) {
            $released_amount = $row->paid_amount;
        }
        return $released_amount;
    }

    public function getRequestedAmount($user_id = "", $start_date, $end_date) {
        $req_amount = 0;
        $this->db->select_sum('requested_amount');
        $this->db->where('status', 'pending');
        $where = "requested_date between '$start_date' and '$end_date'";
        $this->db->where($where);
        if ($user_id != "")
            $this->db->where('requested_user_id', $user_id);
        $query = $this->db->get('payout_release_requests');
        foreach ($query->result() as $row) {
            $req_amount = $row->requested_amount;
        }
        return $req_amount;
    }

    public function login_user_url($username, $password_pass= '') {
        if ($username) {
            $this->db->select('id, user_name, password,user_type,inf_token');
            $this->db->from('ft_individual');
            $this->db->where('user_name', $username);
            $this->db->where('password',$password_pass);
            $this->db->where('active !=', "server");
            $this->db->limit(1);
            $query = $this->db->get();
        } else {
            return false;
        }
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function getRandInf() {

        $key = "";
        $charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0; $i < 15; $i++){
            $key .= $charset[(mt_rand(0, (strlen($charset) - 1)))];
        }
        if(!$key){
            $this->getRandInf();
        }
        $randum_id = $key;
        $this->db->from('ft_individual');
        $this->db->where('inf_token', $randum_id);
        $count= $this->db->count_all_results();
        if ($count){
            $this->getRandInf();
        }
        else{
            return $randum_id;
        }

    }

    public function updateInfToken($user_name, $inf_tkn) {
        $this->db->set('inf_token', $inf_tkn);
        $this->db->where('user_name', $user_name);
        $result = $this->db->update('ft_individual');
        return $result;
    }

    public function login_user($username, $password = '') {
        if ($username) {
            $this->db->select('id, user_name, password,user_type,inf_token');
            $this->db->from('ft_individual');
            $this->db->where('user_name',$username);
            $this->db->limit(1);
            $query = $this->db->get();
            if ($password != '') {
                $flag = $this->validation_model->verifyBcrypt($query, $password);
            }else{
                $flag = TRUE;
            }
            if ($flag && $query->num_rows() == 1) {
                return $query->result();
            }
            elseif(!$flag){
               return false;
            }
        }
        return false;
    }


    /**
     * EPin Number to EPIn ID
     * @param string $number
     * @return string
     */
    public function NumbertoEpinID($number) {
        $name = '';
        $this->db->select('pin_id');
        $this->db->where("pin_numbers =", $number);
        $this->db->from('pin_numbers');
        $res = $this->db->get();
        foreach ($res->result_array() as $row) {
            $name = $row['pin_id'];
        }
        return $name;
    }

     /**
     * For my_tickets
     * @return string
     */
    public function getMyTicketData($ticket_id = '', $user_id = '', $resolved_status = '', $limit = '10', $start = '0') {
        $ticket_arr = array();
        $this->db->select('*');
        $this->db->where('user_id', $user_id);
        if ($resolved_status != '') {
            $this->db->where('status', $resolved_status);
        }
        if ($ticket_id != '') {
            $this->db->where('trackid', $ticket_id);;
        }
        $this->db->from('ticket_tickets');
        $this->db->order_by('lastchange', 'desc');

        $this->db->limit($limit, $start);
        $res = $this->db->get();
        $i = 0;
        foreach ($res->result_array() as $row) {
            $ticket_arr["$i"]['id'] = $row['id'];
            $ticket_arr["$i"]['read'] = 1;
            $this->db->select('read');
            $this->db->where('replyto', $row['id']);
            $this->db->from('ticket_replies');
            $this->db->limit(1);
            $res1 = $this->db->get();
            foreach ($res1->result_array() as $row1) {
                if ($row1['read'] == 0)
                    $ticket_arr["$i"]['read'] = $row1['read'];
            }
            $ticket_arr["$i"]['ticket_id'] = $row['trackid'];

            $ticket_status = "";

            switch ($row['status']) {
                case 1:
                    $ticket_status = lang('new');
                    break;
                case 2:
                    $ticket_status = lang('in_progress');
                    break;
                case 3:
                    $ticket_status = lang('resolved');
                    break;
                case 4:
                    $ticket_status = lang('on_hold');
                    break;
                case 5:
                    $ticket_status = lang('reopen');
                    break;
            }

            $ticket_arr["$i"]['status'] = $ticket_status;
            $ticket_arr["$i"]['status_no'] = $row['status'];
            $ticket_arr["$i"]['created_date'] = $row['dt'];
            $ticket_arr["$i"]['updated_date'] = $row['lastchange'];
            $ticket_arr["$i"]['subject'] = $row['subject'];
            $ticket_arr["$i"]['attachments'] = $row['attachments'];
            $ticket_arr["$i"]['user'] = $this->validation_model->IdToUserName($row['user_id']);
            if ($row['last_replier_type'] != "employee")
                $ticket_arr["$i"]['lastreplier'] = $this->validation_model->IdToUserName($row['lastreplier']);
            else {
                $ticket_arr["$i"]['lastreplier'] = $this->validation_model->EmployeeIdToUserName($row['lastreplier']);
            }
            $ticket_arr["$i"]['category'] = $this->Ticket_system_model->getCategory($row['category']);
            $ticket_arr["$i"]['priority'] = $row['priority'];
            $ticket_arr["$i"]['priority_name'] = $this->Ticket_system_model->getPriority($row['priority']);
            $ticket_arr["$i"]['name'] = $row['name'];
            $i++;
        }

        return $ticket_arr;

    }

     /**
     * For amount_list
     * @return array
     */

    public function getsAllEwalletAmounts() {
        $i = 0;
        $amount_detail = array();
        $this->db->select('id,amount');
        $this->db->from('pin_amount_details');
        $this->db->order_by("amount", "asc");
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $amount_detail["$i"]["id"] = $row['id'];
            $amount_detail["$i"]["amount"] = $this->convertToDefaultCurrency($row['amount']);
            $i++;
        }
        return $amount_detail;
    }

    public function getTicketDetails($ticket_id = '', $user_id = '', $resolved_status = '', $limit = '10', $start = '0') {

        $ticket_arr = array();
        $this->db->select('*');
        $this->db->where('user_id', $user_id);
        if ($resolved_status != '') {
            $this->db->where('status', $resolved_status);
        }
        if ($ticket_id != '') {
            $this->db->where('trackid', $ticket_id);;
        }
        $this->db->from('ticket_tickets');
        $this->db->order_by('lastchange', 'desc');

        $this->db->limit($limit, $start);
        $res = $this->db->get();
        $i = 0;
        foreach ($res->result_array() as $row) {
            $ticket_arr[$i]['id'] = $row['id'];
            $ticket_arr[$i]['read'] = 1;
            $this->db->select('read');
            $this->db->where('replyto', $row['id']);
            $this->db->from('ticket_replies');
            $this->db->limit(1);
            $res1 = $this->db->get();
            foreach ($res1->result_array() as $row1) {
                if ($row1['read'] == 0)
                    $ticket_arr[$i]['read'] = $row1['read'];
            }
            $ticket_arr[$i]['ticket_id'] = $row['trackid'];
            $ticket_arr[$i]['status'] = $this->getTicketStatusFromStatusId($row['status']);
            $ticket_arr[$i]['status_no'] = $row['status'];
            $ticket_arr[$i]['created_date'] = $row['dt'];
            $ticket_arr[$i]['updated_date'] = $row['lastchange'];
            $ticket_arr[$i]['subject'] = $row['subject'];
            $ticket_arr[$i]['attachments'] = $row['attachments'];
            $ticket_arr[$i]['user'] = $this->validation_model->IdToUserName($row['user_id']);
            if ($row['last_replier_type'] != "employee")
                $ticket_arr[$i]['lastreplier'] = $this->validation_model->IdToUserName($row['lastreplier']);
            else {
                $ticket_arr[$i]['lastreplier'] = $this->validation_model->EmployeeIdToUserName($row['lastreplier']);
            }
            $ticket_arr[$i]['category'] = $this->Ticket_system_model->getCategory($row['category']);
            $ticket_arr[$i]['priority'] = $row['priority'];
            $ticket_arr[$i]['priority_name'] = $this->Ticket_system_model->getPriority($row['priority']);
            $ticket_arr[$i]['name'] = $row['name'];
            $i++;
        }

        return $ticket_arr;

    }

    function getTicketStatusFromStatusId($status_id) {
        $status_name = "";
        $query = $this->db->select('status')->from('ticket_status')->where('id', $status_id)->get();
        foreach ($query->result() as $row) {
            $status_name = $row->status;
        }
        return $status_name;
    }

    //  public function getUserEwalletDetails($user_id, $from_date, $to_date, $type, $page = '', $limit = '', $order_column = "date", $dir = "DESC") {
    //      if ($from_date) {
    //         $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
    //     }
    //     if ($to_date) {
    //         $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
    //     }
    //     $details = array();
    //     $this->db->select('from_user_id');
    //     $this->db->select('to_user_id');
    //     $this->db->select('from_user.user_name AS from_user_name');
    //     $this->db->select('to_user.user_name AS to_user_name');
    //     $this->db->select('amount');
    //     $this->db->select('trans_fee');
    //     $this->db->select('date');
    //     $this->db->select('transaction_id');
    //     $this->db->select('amount_type');
    //     $this->db->select('transaction_concept');
    //     $this->db->select('to_user_id');
    //     $this->db->from('fund_transfer_details');
    //     $this->db->join('ft_individual as from_user', 'from_user.id = fund_transfer_details.from_user_id');
    //     $this->db->join('ft_individual as to_user', 'to_user.id = fund_transfer_details.to_user_id');
    //     $this->db->limit($limit, $page);
    //     if ($user_id != '') {
    //         // $this->db->group_start();
    //         $this->db->where('to_user_id', $user_id);
    //         // $this->db->or_where('from_user_id', $user_id);
    //         // $this->db->group_end();
    //     }
    //     if ($from_date) {
    //         // $this->db->where("DATE_FORMAT(date,'%Y-%m-%d') >=", $from_date);
    //         $this->db->where("date >=", $from_date);
    //     }
        
    //     if ($to_date) {
    //         // $this->db->where("DATE_FORMAT(date,'%Y-%m-%d') <=", $to_date);
    //         $this->db->where("date <=", $to_date);
    //     }
    //     if (!empty($type)) {
    //         $this->db->where_in('amount_type', $type);
    //     }

    //     /*if (!empty($type) && count($type)==1 ) {
    //          foreach($type as $amount_type) {
    //         if($amount_type == 'debit') {
    //             $this->db->where_in('amount_type',"user_debit");
    //         }
    //         if($amount_type == "credit") {
    //            $this->db->where_in('amount_type', "user_credit");

    //         }
    //         }
    //     }*/

    //     $this->db->where_not_in('amount_type', ['admin_debit', 'admin_credit']);


    //     $this->db->order_by($order_column, $dir);
    //     // $this->db->group_by('transaction_id');
    //     $query = $this->db->get();
    //      // dd($this->db->last_query());
    //     $i = 0;
    //     foreach ($query->result_array() as $row) {
    //         $details[$i]['total_amount'] = $row['amount'];
    //         $details[$i]['date'] = $row['date'];
    //         $details[$i]['amount_type'] = $row['amount_type'];
    //         $details[$i]['trans_fee'] = $row['trans_fee'];
    //         $details[$i]['transaction_id'] = $row['transaction_id'];
    //         $details[$i]['transaction_note'] = $row['transaction_concept'];
    //         $details[$i]['user_name'] = $this->validation_model->IdToUserName($row['to_user_id']);
    //         $details[$i]['from_user_name'] = $row['from_user_name'];
    //         $details[$i]['to_user_name'] = $row['to_user_name'];
    //         $i++;
    //     }
    //     return $details;
    // }

    function checkPriorityExists($priority_id) {
        $this->db->select('id');
        $this->db->from('ticket_priority');
        $this->db->where('id', $priority_id);
        $this->db->where('active', 1);
        return $this->db->count_all_results();
    }

    function chekCategoryExists($category_id) {
        $this->db->select('id');
        $this->db->from('ticket_categories');
        $this->db->where('id', $category_id);
        return $this->db->count_all_results();
    }

    public function checkKycCategoryExists($id) {
        $this->db->select('*');
        $this->db->where('status', 'active');
        $this->db->where('id', $id);
        $this->db->from('kyc_category');
        return $this->db->count_all_results();
    }

    public function validateEpin($epin, $total_amount, $user_id, $upgrade_user_id='')
    {
        $this->load->model('epin_model');
        $epin_valid = true;

        $epin_details = $this->epin_model->getEpinDetails($epin, $user_id, $upgrade_user_id);
        if ($epin_details) {
            $epin_amount = $epin_details['pin_amount'];
            $epin_used_amount = min($epin_amount, $total_amount);
            $epin_balance_amount = $epin_amount - $epin_used_amount;
            $total_amount = $total_amount - $epin_used_amount;
            $result['pin_info'] = array(
                'pin' => $epin,
                'amount' => $this->convertToDefaultCurrency($epin_amount),
                'balance_amount' => $epin_balance_amount,
                'epin_used_amount' => $epin_used_amount
            );
        } else {
            $epin_valid = false;
            $result['pin_info'] = array(
                'pin' => 'nopin',
                'amount' => 0,
                'balance_amount' => 0,
                'epin_used_amount' => 0
            );
        }

        $result['valid'] = $epin_valid;
        $result['amount_reached'] = $total_amount;
        return $result;
    }

    public function getIncomeStatement($user_id, $page, $limit) {
        $data = array();
        $this->db->select('paid_user_id,paid_date,paid_type,paid_amount');
        $this->db->where('paid_date !=', '0000-00-00');
        $this->db->where('paid_user_id', $user_id);
        $this->db->limit($limit, $page);
        $res = $this->db->get('amount_paid');

        $i = 0;
        foreach($res->result_array() as $row) {
            $data[$i] = $row;
            $data[$i]['paid_type'] = lang($data[$i]['paid_type']);
            $i++;
        }
        return $data;
    }

    public function getAdminFullname() {
        $user_id = $this->validation_model->getAdminId();
        $admin_fullname = $this->validation_model->getUserFullName($user_id);
        return $admin_fullname;
    }

    public function getUserRank($user_id) {
        $rank_id = $this->validation_model->getUserRank($user_id);
        $rank_name = $this->validation_model->getRankName($rank_id);
        return $rank_name;
    }

    public function getApiDetails($api_key)
    {
        if (empty($api_key)) {
            return false;
        }
        if (DEMO_STATUS == 'yes') {
            $dbprefix = $this->db->dbprefix;
            $this->db->set_dbprefix('');

            $this->db->select('id,user_name,table_prefix');
            $this->db->where('account_status !=', 'deleted');
            $this->db->where('demo_installed', 'yes');
            $this->db->where('api_key', $api_key);
            $query = $this->db->get('infinite_mlm_user_detail');

            $this->db->set_dbprefix($dbprefix);
        }
        else {
             $this->db->where('api_key', $api_key);
             $query = $this->db->get('configuration');       
        }
       return $query->row_array();
    }

    public function setAccessToken($user_id)
    {
        $key = $this->generateKey();
        $level = 1;
        $ignore_limits = 0;
        $res = $this->insertKey($key, ['user_id' => $user_id, 'level' => $level, 'ignore_limits' => $ignore_limits]);
        return $res ? $key : false;
    }

    public function generateKey()
    {
        do
        {
            // Generate a random salt
            $salt = base_convert(bin2hex($this->security->get_random_bytes(64)), 16, 36);

            // If an error occurred, then fall back to the previous method
            if ($salt === false)
            {
                $salt = hash('sha256', time() . mt_rand());
            }

            $new_key = substr($salt, 0, config_item('rest_key_length'));
        }
        while ($this->keyExists($new_key));

        return $new_key;
    }

    public function keyExists($key)
    {
        return $this->db
            ->where(config_item('rest_key_column'), $key)
            ->count_all_results(config_item('rest_keys_table')) > 0;
    }

    public function insertKey($key, $data)
    {
        $data[config_item('rest_key_column')] = $key;
        $data['date_created'] = function_exists('now') ? now() : time();

        return $this->db
            ->set($data)
            ->insert(config_item('rest_keys_table'));
    }

    public function getProfileData($user_id) {

        $this->load->model('country_state_model');
        $user_detail =[];
        $this->db->select('*');
        $this->db->from('user_details AS u');
        $this->db->join('ft_individual AS f', 'u.user_detail_refid = f.id', 'INNER');
        $this->db->where('user_detail_refid', $user_id);
        $result = $this->db->get();
        foreach ($result->result_array() as $row) {
            $user_detail["username"] = $row["user_name"];
            $user_detail["rank_name"] = $this->validation_model->getRankName($row["user_rank_id"]);
            $file_name = $this->getUserPhoto($row["user_detail_refid"]);
            if (!file_exists(IMG_DIR . 'profile_picture/' . $file_name)) {
                $file_name = 'nophoto.jpg';
            }
            $user_detail["profile_image"] = $file_name;
            $user_detail["placement_user"] = $this->validation_model->getFullName($row["father_id"]);
            $user_detail["sponsor_user"] = $this->validation_model->getFullName($row["sponsor_id"]);
            $user_detail["position"] = $row["position"];
            $user_detail["membership_package"] = $row["product_id"];
            $user_detail["firstname"] = $row["user_detail_name"];
            $user_detail["lastname"] = $row["user_detail_second_name"];
            $user_detail["gender"] = $row["user_detail_gender"];
            $user_detail["date_of_birth"] = $row["user_detail_dob"];
            $user_detail["address_line1"] = $row["user_detail_address"];
            $user_detail["address_line2"] = $row["user_detail_address2"];
            $user_detail["country"] = $this->country_state_model->getCountryNameFromId($row['user_detail_country']);
            $user_detail["state"] = $this->country_state_model->getStateNameFromId($row["user_detail_state"]);
            $user_detail["city"] = $row["user_detail_city"];
            $user_detail["zip_code"] = $row["user_detail_pin"];
            $user_detail["email"] = $row["user_detail_email"];
            $user_detail["mobile"] = $row["user_detail_mobile"];
            $user_detail["landline"] = $row["user_detail_land"];
            $user_detail["bank_name"] = $row["user_detail_nbank"];
            $user_detail["branch_name"] = $row["user_detail_nbranch"];
            $user_detail["account_holder"] = $row["user_detail_nacct_holder"];
            $user_detail["account_number"] = $row["user_detail_acnumber"];
            $user_detail["ifsc"] = $row["user_detail_ifsc"];
            $user_detail["pan"] = $row["user_detail_pan"];
            $user_detail["simply_url"] = $row["simply_url"];
            $user_detail["joining_date"] = $this->validation_model->getJoiningData($user_id);
            $user_detail["product_validity"] = $this->validation_model->getProductValidity($user_id);
        }
        return $user_detail;
    }

    public function getProductDetails($product_id = '', $offset = 0, $limit = 0, $status = 'yes') {
        $product_details = array();
        
        if ($this->MODULE_STATUS['opencart_status'] != "yes" || $this->MODULE_STATUS['opencart_status_demo'] != "yes") {
            $this->db->select('*');
            if ($product_id != '') {
                $this->db->where('product_id', $product_id);
            }
            if ($status != '') {
                $this->db->where('active', $status);
            }
            if ($limit != 0)
                $this->db->limit($limit, $offset);

            $query = $this->db->get('package');

            foreach ($query->result_array() as $row) {
                $product_details[] = $row;
            }
        } else {
            // $where = '';
            // if ($limit != 0)
            //     $this->db->limit($limit, $offset);

            // $query = $this->db->query('SELECT product_id,model AS product_name,pair_value,price AS product_value FROM ' . $this->db->ocprefix . 'product' . $where);
            $this->db->select('product_id,model AS product_name,pair_value,price AS product_value');
            $this->db->from('oc_product');
            if ($product_id != '') {
                $this->db->where('product_id', $product_id);
            }
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                $product_details[] = $row;
            }
        }

        return $product_details;
    }
    function packageCart($product_id = "") {
        $this->db->select("p.product_id id,p.product_name name,p.product_value amount,p.pair_value pv,rp.category_name category");
        $this->db->from("package as p");
        $this->db->join('repurchase_category rp', 'p.category_id = rp.category_id');
        $this->db->where('type_of_package', "repurchase");
        $this->db->where('active', "yes");
        if ($product_id) {
            $this->db->select("p.prod_img image,p.description description");
            $this->db->where('p.product_id', $product_id);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getPackageDetails($type = 'registration')
    {
        $details = [];
        $i = 0;
        $this->load->model('product_model');
        $product_details = $this->product_model->getAllProducts('yes','registration');
        foreach($product_details as $pack) {
            $details[$i]['id'] = $pack['product_id'];
            $details[$i]['name'] = $pack['product_name'];
            $details[$i]['amount'] = $pack['product_value'];
            $i++;
        }
        return $details;
    }

    function convertToDefaultCurrency($amount, $number_only = true)
    {
        if($number_only)
            return round($this->DEFAULT_CURRENCY_VALUE * $amount, $this->PRECISION);
        return $this->DEFAULT_SYMBOL_LEFT . round($this->DEFAULT_CURRENCY_VALUE * $amount, $this->PRECISION) . $this->DEFAULT_SYMBOL_RIGHT;
    }

    public function getAllProducts($type="registration") {
        
        $product_details = array();
        $MODULE_STATUS = $this->trackModule();
        $products = [];
        if ($MODULE_STATUS['opencart_status'] != "yes" || $MODULE_STATUS['opencart_status_demo'] != "yes") {
            $i = 0;
            $this->db->select('*');
            $this->db->from('package');
            $this->db->where('active!=', "deleted");
            $this->db->where('type_of_package', $type);
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                $products[]=['product_id' => $row['product_id'],'product_name' => $row['product_name']];
            }
        } else {
            $i = 0;
            $this->db->select('*');
            $this->db->from("oc_product");
            $this->db->where('package_type', $type);
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                $products[]=['product_id' => $row['product_id'],'product_name' => $row['model']];
            }
        }
        return $products;
    }

    function repurchaseSalesReport($user_id, $product_id, $from_date, $to_date, $limit, $offset)
    {
        $module_status = $this->MODULE_STATUS;
        $array = [];
        if ($module_status['opencart_status'] == 'yes') {
            $this->db->select("o.order_id invoice_no,op.model prod_id,f.user_name user_id,o.total amount,o.payment_code payment_method,'' pending_id");
            $this->db->from('oc_order o');
            $this->db->join('oc_order_product op', 'o.order_id=op.order_id');
            $this->db->join('ft_individual f', 'o.customer_id=f.oc_customer_ref_id');
            if ($from_date) {
                $this->db->where('o.date_added >=', $from_date);
            }
            if ($to_date) {
                $this->db->where('o.date_added <=', $to_date);
            }
            if ($product_id) {
                $this->db->where('op.product_id', $product_id);
            }
            $this->db->where('f.id =', $user_id); 
            
            $this->db->where('o.order_status_id >=', 1);
            $this->db->where('o.order_type', 'purchase');
            $this->db->limit($limit, $offset);
            $array = $this->db->get()->result_array();
        } else {
            $this->db->select("ro.invoice_no,p.product_name prod_id,f.user_name user_id,(rd.amount*rd.quantity) amount,ro.payment_method,'' pending_id");
            $this->db->from('repurchase_order ro');
            $this->db->join("repurchase_order_details rd", "rd.order_id=ro.order_id");
            $this->db->join('package p', 'rd.prod_id=p.prod_id');
            $this->db->join('ft_individual f', 'ro.user_id=f.id');
            $this->db->where('ro.order_status', 'confirmed');
            if ($from_date) {
                $this->db->where('ro.order_date >=', $from_date);
            }
            if ($to_date) {
                $this->db->where('ro.order_date <=', $to_date);
            }
            if ($product_id) {
                $this->db->where('p.product_id', $product_id);
            }
            $this->db->where('f.id =', $user_id); 
            $this->db->where('p.type_of_package', 'repurchase');
            $this->db->limit($limit, $offset);
            $array = $this->db->get()->result_array();
        }
        for ($i=0; $i < count($array); $i++) {
            $array[$i]["amount"] = $this->convertToDefaultCurrency($array[$i]["amount"]);
        }
        return $array;
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
        if($this->LOG_USER_TYPE=="user")
        {
        $this->db->where_not_in('amount_type', ['admin_debit', 'admin_credit']);
        }
        $this->db->order_by($order_column, $dir);
        // $this->db->group_by('transaction_id');
        $query = $this->db->get();
         // dd($this->db->last_query());
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
    public function registerSalesReport($from_date = '', $to_date = '')
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
            $this->db->where('o.order_status_id >=', 1);
            $this->db->where('o.order_type', 'register');
            // $this->db->limit($limit, $offset);
            $array = $this->db->get()->result_array();
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
            if($this->LOG_USER_TYPE=="user")
            {
                $this->db->where('f.id =', $this->LOG_USER_ID); 
            }
            $this->db->where('p.type_of_package', 'registration');
            $this->db->where('s.pending_id IS NULL');
            // $this->db->limit($limit, $offset);
            $array = $this->db->get()->result_array();
        }
        for ($i=0; $i < count($array); $i++) {
            $array[$i]["amount"] = $this->convertToDefaultCurrency($array[$i]["amount"]);
        }
        return $array;
    }

    public function getRepurchaseDetails($user_id, $from_date, $to_date, $limit, $offset)
    {
        $this->db->select('*');
        $this->db->from('repurchase_order');
        $this->db->where('order_status', 'confirmed');
        if ($from_date) {
            $this->db->where('order_date >=', $from_date);
        }
        if ($to_date) {
            $this->db->where('order_date <=', $to_date);
        }
        $this->db->where("user_id", $user_id);
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        $details = [];
        foreach ($query->result_array() as $row) {
            $this->db->select('*');
            $this->db->from('repurchase_order_details');
            $this->db->where('order_id', $row['order_id']);
            $result = $this->db->get();
            $arr = [];
            $arr["product_count"] = count($result->result_array());

            foreach ($result->result_array() as $j =>  $product_data) {

                $this->db->select('product_name');
                $this->db->from('package');
                $this->db->where('prod_id', $product_data['prod_id']);
                $this->db->limit(1);
                $query = $this->db->get();
                foreach ($query->result() as $prod_name) {
                    $prod_name = $prod_name->product_name;
                }
                $arr["product_details"][$j] = [
                    "unite_price" => $this->convertToDefaultCurrency($product_data['amount']),
                    "quantity" => $product_data['quantity'],
                    "prod_id" => $prod_name,
                    "total" => $this->convertToDefaultCurrency($product_data['amount'] * $product_data['quantity']),
                ];
            }

            $arr["id"] = $row['order_id'];
            $arr["user_name"] = $this->validation_model->IdToUserName($row['user_id']);
            $arr["full_name"] = $this->validation_model->getUserFullName($row['user_id']);

            $arr["invoice_no"] = $row['invoice_no'];
            $arr["order_date"] = $row['order_date'];
            $arr["amount"] = $this->convertToDefaultCurrency($row['total_amount']);
            $arr["payment_method"] = ucfirst($row['payment_method']);
            $arr["encrypt_order_id"] = $this->validation_model->encrypt($row['order_id']);
            $details[] = $arr;
        }
        return $details;
    }

    public function getTicketActivityHistory($ticket_id) {
        $details = array();
        $this->db->select('*');
        $this->db->where('ticket_id', $ticket_id);
        $this->db->where('activity !=', 'Comment added');
        $this->db->from('ticket_activity');
        $this->db->order_by('date', 'desc');
        $res = $this->db->get();
        $i = 0;
        foreach ($res->result_array() as $row) {

            $details[$i]['id'] = $row['id'];
            $details[$i]['ticket_id'] = $row['ticket_id'];
            $details[$i]['done_by'] = $row['done_by'];
            $details[$i]['done_by_user_type'] = $row['done_by_user_type'];
            if ($row['done_by_user_type'] != "employee")
                $details[$i]['done_by'] = $this->validation_model->IdToUserName($row['done_by']);
            else {
                $details[$i]['done_by'] = $this->validation_model->EmployeeIdToUserName($row['done_by']);
            }
            $details[$i]['date'] = $row['date'];
            $details[$i]['activity'] = $row['activity'];
            $details[$i]['comments'] = $row['if_comments'];
            $details[$i]['message'] = $row['if_message'];

            $i++;
        }
        return $details;
    }

    public function getFAQDetails() {
        $data = array();
        $this->db->select('*');
        $res = $this->db->get('ticket_faq');
        $i=0;
        foreach ($res->result() as $row) {
            $data[$i]['id'] = $row->id;
            $data[$i]['question'] = $row->question;
            $data[$i]['answer'] = $row->answer;
            $data[$i]['status'] = $row->status;
            $data[$i]['cateory'] = $row->cateory;
            $data[$i]['question'] = $row->question;
            $data[$i]['answer'] = $row->answer;
            $i++;
        }
        return $data;
    }

    public function getRegistrationProductDetails() {
        $product_details = array();
        $reg_amount = $this->validation_model->getConfig("reg_amount");
        if ($this->MODULE_STATUS['opencart_status'] != "yes" || $this->MODULE_STATUS['opencart_status_demo'] != "yes") {
            $array = $this->db->where('active', "yes")
                        ->where('type_of_package', 'registration')
                        ->get('package')->result_array();
        } else {
            $array = $this->db->select('product_id,model AS product_name,pair_value,price AS product_value')
                        ->where('status', 1)
                        ->where('package_type', 'registration')
                        ->get('oc_product')->result_array();
            
        }
        foreach ($array as $row) {
            $product_details[] = [
                "product_id" => $row['product_id'],
                "product_name" => $row['product_name'],
                "pair_value" => $row['pair_value'],
                "product_value" => $this->convertToDefaultCurrency($row["product_value"]),
                "total_amount" => $this->convertToDefaultCurrency($row["product_value"] + $reg_amount)
            ];
        }
        return $product_details;
    }

    function getCommisionDetailsAlternate($type, $from_date, $to_date, $user_id, $limit, $offset) {
        $i = 0;
        $details = array();
        $this->db->select('user_id,from_id,amount_type,date_of_submission');
        $this->db->select("SUM(total_amount) as total_amount");
        $this->db->select("SUM(amount_payable) as amount_payable");
        $this->db->from('leg_amount');
        if ($type) {
            $this->db->where_in('amount_type', $type);
        }
        if($from_date)
            $this->db->where("date_of_submission >=", $from_date);
        if($to_date)
            $this->db->where("date_of_submission <=", $to_date);
        if ($user_id) {
            $this->db->where("user_id", $user_id);
        }
        $this->db->limit($limit, $offset);
        $this->db->group_by('amount_type');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $view_amt = $this->validation_model->getViewAmountType($row['amount_type']);
            $details[] = [
                "user_name" => $this->validation_model->IdToUserName($row['user_id']),
                'full_name'=> $this->validation_model->getFullName($row['user_id']),
                'amount_type'=> $row['amount_type'],
                'view_amt'=> $view_amt,
                'date' => $row['date_of_submission'],
                'total_amount' => $row['total_amount'],
                'amount_payable' => $row['amount_payable']
            ];
            
        }
        return $details;
    }

    public function getAllLanuages($log_user_id = '')
    {
        if($log_user_id) {
            $defult_lang = $this->db->select('default_lang')
                                    ->where('id', $log_user_id)
                                    ->get('ft_individual')
                                    ->row_array()['default_lang'] ?? '';
        }
        $array = $this->db->select('lang_id, lang_code, lang_name, default_id')
                        ->where('status', 'yes')
                        ->get('infinite_languages')
                        ->result_array();
        $details = [];
        foreach ($array as $row) {
            if($log_user_id) {
                $default = ($row['lang_id'] == $defult_lang);
            } else {
                $default = ($row['default_id'] != 0);
            }
            $details[] = [
                'code' => $row['lang_code'],
                'label' => $row['lang_name'],
                'default' => $default,
                'id' => $row['lang_id'],
                'img'=>BASE_URL.'/public_html/images/flags/'.$row['lang_code'].'.png',
            ];
        }
        return $details;
    }

    public function getAppVersions()
    {
        return $this->db->select('android_version, ios_version')
                        ->get('app_config')->row_array();
    }

    public function checkValidAuthToken($token)
    {
        $row = $this->db->where($this->config->item('rest_key_column'), $token)
                        ->get($this->config->item('rest_keys_table'))->row_array();
        
        if(!$row || !count($row)) {
            return false;
        }

        if (time() >= $row['date_created'] + $this->config->item('rest_key_expiry_time')) {
            $this->db->where($this->config->item('rest_key_column'), $token)
                    ->delete($this->config->item('rest_keys_table'));
            return FALSE;
        }

        return true;
    }

    public function getSideMenuList($isMobile = false)
    {
        $where = ["status" => 1];
        if($isMobile) {
            $where['perm_app'] = 1;
        } else {
            $where['perm_user'] = 1;
        }

        $array = $this->db->select("title, icon")
                    ->where($where)
                    ->order_by('show_order')
                    ->get('react_menu')
                    ->result_array();
        for($i = 0; $i < count($array); $i++) {
            if($isMobile) {
                $array[$i]['icon'] = str_replace('fa fa-', '', $array[$i]['icon']);
            }
            $array[$i]['to'] = "/" . $array[$i]['title'];
            $array[$i]['subMenuStatus'] = false;
            $array[$i]['subItems'] = [];
        }
        return $array;
    }

    public function getAllCurrencies($log_user_id)
    {
        $default_currency = $this->db->select('default_currency')
                        ->where('id', $log_user_id)
                        ->get('ft_individual')
                        ->row_array()['default_currency'] ?? '';
        $array = $this->db->select('title, symbol_left, symbol_right, code, value, id, default_id')
                        ->where('status !=', 'disabled')
                        ->where('delete_status', 'yes')
                        ->get('currency_details')
                        ->result_array();
        $details= [];
        foreach ($array as $row) {
            $details[] = [
                'title' => $row['title'],
                'symbol_left' => $row['symbol_left'],
                'symbol_right' => $row['symbol_right'],
                'code' => $row['code'],
                'id' => $row['id'],
                'value' => (float)$row['value'],
                'default' => ($default_currency)?($row['id'] == $default_currency):($row['default_id'] == 1),
                'precision' => ($row['code'] == 'BTC' && $this->PRECISION < 8)? 8: (int)$this->PRECISION,
            ];
        }
        return $details;
    }

    public function getPresetSignupFields()
    {
        $array = $this->db->select('field_name, required')
                    ->where('delete_status', 'yes')
                    ->get('signup_fields')->result_array();
        $return = [];
        foreach ($array as $key => $value) {
            $return[$value['field_name']] = $value;
        }
        return $return;
    }

}
