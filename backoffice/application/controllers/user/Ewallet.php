<?php

require_once 'Inf_Controller.php';

class Ewallet extends Inf_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->url_permission('ewallet_status');
        $this->load->model('epin_model');
    }

    public function index() {

        $title = lang('ewallet');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);
        $help_link = 'e-pin-management';
        $this->set('help_link', $help_link);
        $this->HEADER_LANG['page_top_header'] = lang('my_e_pin');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('my_e_pin');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();
        $balamount = $this->ewallet_model->getBalanceAmount($this->LOG_USER_ID);
        $total = $this->ewallet_model->getEwalletOverviewTotal($this->LOG_USER_ID);
        $debit =  ($total['credit']-$balamount) > 0? $total['credit']-$balamount : 0 ;
        $this->set('debit',$debit);
        $this->set('balamount', $balamount);
        $this->set('trans_fee', $this->ewallet_model->getTransactionFee());
        $this->set('user_earnings_categories', $this->ewallet_model->getEnabledBonusCategories());
        $this->set('total', $total);
        $this->set('commission_earned', $this->ewallet_model->getTotalCommissionEarned($this->LOG_USER_ID));
        $this->set('purchase_wallet', $this->validation_model->getPurchaseWalletAmount($this->LOG_USER_ID));
        $this->setView('newui/user/ewallet/index');
    }

    public function statement() {
        $order_columns = [
            2 => 'amount',
            3 => 'date_added',
        ];

        $order = $this->input->get('order', true)[0]['column'] ?? 3;
        $direction = $this->input->get('order', true)[0]['dir'] ?? 'asc';
        $filter = [
            'limit' => (int)$this->input->get('length', true),
            'start' => (int)$this->input->get('start', true),
            'order' => $order_columns[$order] ?? $order_columns[3],
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];

        $page = $filter['start'];
        $count     = $this->ewallet_model->getEwalletHistoryCount($this->LOG_USER_ID);
        $statement = $this->ewallet_model->getUserEwalletStatement($this->LOG_USER_ID, $filter);

        $previous_ewallet_balance = $this->ewallet_model->getPreviousEwalletBalance($this->LOG_USER_ID, $page);
        $balance = $previous_ewallet_balance;
        $debit   = 0;
        $credit = 0;

        $data = [];

        foreach($statement as $key => $item) {
            $amount = "";
            $amount_class = '';
            if($item['type'] == "debit" && $item['amount_type'] != "payout_release") {
                $balance = $balance - $item['amount'] - $item['transaction_fee'];
                $debit   = $debit + $item['amount'];
                $amount_class = 'text-danger-dker';
            }
            
            if($item['type'] == 'credit') {
                $balance = $balance + $item['amount'] - $item['purchase_wallet'];
                $credit = $credit + $item['amount'] - $item['purchase_wallet'];
                $amount_class = 'text-success-dker';
            }

            if (in_array($item['ewallet_type'], array('fund_transfer', 'payout')) && $item['transaction_fee'] > 0 && $item['type'] == 'debit') {
                if($item['ewallet_type'] == "fund_transfer") {
                    $amount = "( ".lang('transfer_fee') ." - 
                            <i class='text-danger-dker'>
                                ".format_currency($item['transaction_fee'])."
                            </i>
                        )
                    ";
                } else if($item['ewallet_type'] == "payout") {
                    $amount = "( " .lang('payout_fee') ." - 
                            <i class='text-danger-dker'>
                                ".format_currency($item['transaction_fee'])."
                            </i>
                        )
                    ";

                }
            }
          
            $data[] = [
                'index' => $key + $filter['start'] + 1,
                'description' => $this->getDescription($item, $amount),
                'amount' => format_currency($item['amount']-$item['purchase_wallet'] + $item['transaction_fee']),
                'type'   => $item['type'],
                'transaction_date' => date("F j, Y, g:i a",strtotime($item['date_added'])),
                'balance' => format_currency($balance),
            ];
        }

        
        echo json_encode([
            "draw" => intval($this->input->get("draw")),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data
        ]);
        exit();
    }

    public function getDescription($item, $transaction_fee) {
        $description = "";
        $debit   = 0;
        $credit = 0;
        $from_user_amount_types = [
            'referral',
            'level_commission',
            'repurchase_level_commission',
            'upgrade_level_commission',
            'xup_commission',
            'xup_repurchase_level_commission',
            'xup_upgrade_level_commission',
            'sales_commission',
            'getDescription',
            'agent_fund_transfer_credit'
        ];

        if($item['ewallet_type'] == "fund_transfer") {
            if($item['amount_type'] == "user_credit") {
                $description = lang('transfer_from').' '.$item['from_user'];
            } elseif($item['amount_type'] == "user_debit") {
                $description = lang('fund_transfer_to'). ' ' .$item['from_user'];
            } elseif($item['amount_type'] == "admin_credit") {
                $description = lang('credited_by') .' '. $item['from_user'];
            } elseif($item['amount_type'] == "admin_debit") {
                $description = lang('deducted_by').' '.$item['from_user'];
            } elseif($item['amount_type'] == "agent_fund_transfer_credit"){
                $description = lang('credited_by').' '.'agent';
            }elseif ($item['amount_type'] =='agent_debit') {
             $description = lang('fund_transfer_to_agent') ." - ".$this->validation_model->IdToAgentUserName($item['agent_id']);
             }
        } elseif($item['ewallet_type'] == "commission") {
            if($item['amount_type'] == "donation") {
                if ($item['type'] == "debit") {
                    $description = lang('donation_debit'). ' ' .$item['from_user'] ;
                } else {
                    $description = lang('donation_credit'). ' '.$item['from_user'] ;
                }
            } elseif($item['amount_type'] == 'board_commission' && $MODULE_STATUS['table_status'] == 'yes') {
                $description = lang('table_commission') ;
            } else {
                if (in_array($item['amount_type'], $from_user_amount_types)) {
                    $description = lang($item['amount_type']).' '.lang('from') . ' ' . $item['from_user'] ;
                } else {
                    $description = lang($item['amount_type']) ;
                }
            }
        } 
        
        elseif ($item['ewallet_type'] == "ewallet_payment") {
            if ($item['amount_type'] == "registration") {
                $description = lang('deducted_for_registration_of'). ' ' . $item['from_user'];
            } elseif ($item['amount_type'] == "repurchase") {
                $description = lang('deducted_for_repurchase_by').' '.$item['from_user'];
            } elseif ($item['amount_type'] == "package_validity") {
                $description = lang('deducted_for_membership_renewal_of'). ' ' . $item['from_user'];
            } elseif ($item['amount_type'] == "upgrade") {
                $description = lang('deducted_for_upgrade_of'). ' '. $item['from_user'];
            }
        } elseif($item['ewallet_type'] == "payout") {
            if ($item['amount_type'] == "payout_request") {
                $description = lang('deducted_for_payout_request');
            } elseif($item['amount_type'] == "payout_inactive") {
                $description = lang('payout_inactive');
            } elseif($item['amount_type'] == "payout_release") {
                $description = lang('payout_released_for_request');
            } elseif($item['amount_type'] == "payout_delete") {
                $description = lang('credited_for_payout_request_delete');
            } elseif($item['amount_type'] == "payout_release_manual") {
                $description = lang('payout_released_by_manual');
            } elseif($item['amount_type'] == "withdrawal_cancel") {
                $description = lang('credited_for_waiting_withdrawal_cancel');
            }
        } elseif($item['ewallet_type'] == "pin_purchase") {
            if ($item['amount_type'] == "pin_purchase") {
                $description = lang('deducted_for_pin_purchase');
            } elseif ($item['amount_type'] == "pin_purchase_refund") {
                $description = lang('credited_for_pin_purchase_refund');
            } elseif ($item['amount_type'] == "pin_purchase_delete") {
                $description = lang('credited_for_pin_purchase_delete');
            }
        } elseif ($item['ewallet_type'] == "package_purchase") {
            if ($item['amount_type'] == "purchase_donation") {
                $description = lang('purchase_donation'). ' ' .lang('from') .' ' .$item['from_user'];
            }
        }
        return $description. ' '. $transaction_fee;
    }

    public function transfer_history() {
        $order_columns = [
            0 => 'amount_type',
            1 => 'amount',
            3 => 'trans_fee',
            4 => 'amount_type',
            5 => 'date'
        ];

        $order = $this->input->get('order', true)[0]['column'] ?? 3;
        $direction = $this->input->get('order', true)[0]['dir'] ?? 'asc';
        $filter = [
            'limit' => (int)$this->input->get('length', true),
            'start' => (int)$this->input->get('start', true),
            'order' => $order_columns[$order] ?? $order_columns[3],
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];

        
        $from_date = $this->input->get('start_date');
        $to_date = $this->input->get('end_date');
        $type = $this->input->get('type', true);
        
        $count = $this->ewallet_model->getUserEwalletDetailsCount($this->LOG_USER_ID, $from_date, $to_date, $type);
        $fund_transfer_details = $this->ewallet_model->getUserEwalletDetails($this->LOG_USER_ID, $from_date, $to_date, $type, $filter['start'], $filter['limit'], $filter['order'], $filter['direction']);
        $data = [];
        foreach($fund_transfer_details as $key => $item) {
            $data[] = [
                'description' => $this->getDescriptionTransaction($item),
                'transaction_id' => $item['transaction_id'],
                'amount' => format_currency($item['total_amount']),
                'transaction_fee' =>  ($item['amount_type'] == "user_debit") ? format_currency($item['trans_fee']) : 'NA',
                'transfer_type'  => $item['amount_type'] == "user_debit" ? lang('debit') : lang('credit'),
                'type'           => $item['amount_type'] == "user_debit" ? 'debit' : 'credit',
                'date' => date("F j, Y, g:i a",strtotime($item['date'])),
            ];
        }
        
        echo json_encode([
            "draw" => intval($this->input->get("draw")),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data
        ]);
        exit();
    }

    private function getDescriptionTransaction($item) {
        if($item['amount_type'] == 'user_debit') {
            return lang('fund_transfer_to') .' '. $item['from_user_name'];
        } else if($item['amount_type'] == 'user_credit') {
            return lang('transfer_from'). ' '. $item['from_user_name'];
        }
        return "";

    }

    public function purchase_wallet_table() {
        $order_columns = [
            1 => 'transaction_id',
            2 => 'amount',
            3 => 'trans_fee',
            4 => 'amount_type',
            5 => 'date'
        ];

        $order = $this->input->get('order', true)[0]['column'] ?? 3;
        $direction = $this->input->get('order', true)[0]['dir'] ?? 'asc';
        $filter = [
            'limit' => (int)$this->input->get('length', true),
            'start' => (int)$this->input->get('start', true),
            'order' => $order_columns[$order] ?? $order_columns[3],
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];

        $count = $this->ewallet_model->getPurchasewalletHistoryCount($this->LOG_USER_ID);
        $ewallet_details = $this->ewallet_model->getPurchasewalletHistory($this->LOG_USER_ID, $filter['start'], $filter['limit']);
        $previous_ewallet_balance = $this->ewallet_model->getPreviousPurchasewalletBalance($this->LOG_USER_ID, $filter['start']);
        $balance = $previous_ewallet_balance;
        
        $data = [];
        foreach($ewallet_details as $key => $item) {
            if ($item['type'] == 'debit') {
                $balance = $balance - $item['amount'];
                // $debit = $debit + $item['amount'];
            }
            if ($item['type'] == 'credit') {
                $balance = $balance + $item['amount'];
                // $credit = $credit + $item['amount'];
            }

            $debit  = $item['type'] == "debit" ? "<font color='#f16164'>".format_currency($item['amount'])."</font>" : 'NA';
            $credit = $item['type'] == "credit" ? "<font color='#00581E'>".format_currency($item['amount'])."</font>" : 'NA';
            $data[] = [
                'description' => $this->getPurchaseWalletDescription($item),
                'amount'      => format_currency($item['amount']),
                'type'        => $item['type'],
                'debit'       => $debit,
                'credit'      => $credit,
                'date'        => date("F j, Y, g:i a",strtotime($item['date_added'])),
                'balance'     => format_currency($balance),
            ];
        }

        
        echo json_encode([
            "draw" => intval($this->input->get("draw")),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data
        ]);
        exit();
    }

    public function user_earnings_table() {
        $order_columns = [
            1 => 'amount_payable',
            2 => 'date_of_submission'
         ];

        $order = $this->input->get('order', true)[0]['column'] ?? 3;
        $direction = $this->input->get('order', true)[0]['dir'] ?? 'asc';
        $filter = [
            'limit' => (int)$this->input->get('length', true),
            'start' => (int)$this->input->get('start', true),
            'order' => $order_columns[$order] ?? $order_columns[1],
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];

        $categories = $this->input->get('categories') ?: [];
        $from_date = $this->input->get('start_date');
        $to_date = $this->input->get('end_date');

        $page = $this->input->get('offset');

        $count = $this->ewallet_model->getUserEearningsCount($this->LOG_USER_ID, $categories, $from_date, $to_date);
        $all_transaction = $this->ewallet_model->getUserEarnings($this->LOG_USER_ID, $categories, $from_date, $to_date, $filter);
        $data = [];
        $total_amount = 0;
        $total_tax    = 0;
        $total_service_charge = 0;
        $total_amount_payable = 0;
        foreach($all_transaction as $key => $item) {
            $total_amount += $item['total_amount'];
            $total_tax += $item['tds'];
            $total_service_charge += $item['service_charge'];
            $total_amount_payable += $item['amount_payable'];

            $data[] = [
                'category'         => lang($item['category']),
                'total_amount'     => $item['total_amount'],
                'tax'              => $item['tds'],
                'service_charge'   => $item['service_charge'],
                'amount_payable'   => $item['amount_payable'],
                'transaction_date' => date("F j, Y, g:i a",strtotime($item['transaction_date']))
            ];
        }
        if($this->input->get('total_row')) {
            $data[] = [
                'category' => '<span class="text-lg">'.lang('Total').'</span>',
                'total_amount' => $total_amount,
                'tax'              => $total_tax,
                'service_charge'   => $total_service_charge,
                'amount_payable'   => $total_amount_payable,
                'transaction_date' => ''
            ];
        }

        echo json_encode([
            "draw" => intval($this->input->get("draw")),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data
        ]);
        exit();
    }

    public function getPurchaseWalletDescription($item) {
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
            'sales_commission',
        ];
        if ($item['amount_type'] == "donation") {
            if ($item['type'] == "debit") {
                $description = lang('donation_debit') .' '. $item['from_user'];
            } else {
                $description = lang('donation_credit') . ' ' . $item['from_user'];
            }
        } elseif ($item['amount_type'] == 'board_commission' && $MODULE_STATUS['table_status'] == 'yes') {
            $description = lang('table_commission');
        } elseif ($item['amount_type'] == "repurchase") {
            $description = lang('deducted_for_repurchase_by') . ' ' . $item['from_user'];
        } elseif ($item['amount_type'] == "purchase_donation") {
            $description = lang('purchase_donation') . ' ' . lang('from') . ' ' .  $item['from_user'];
        } elseif (in_array($item['amount_type'], $from_user_amount_types)) {
            $description = lang($item['amount_type']) . ' ' . lang('from') . ' ' . $item['from_user'];
        } else {
            $description = lang($item['amount_type']);
        }

        return $description;
    }

    function fund_transfer() {
        $this->set('action_page', $this->CURRENT_URL);
        $title = lang('fund_transfer');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $help_link = 'fund-transfer';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('fund_transfer');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('fund_transfer');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $userid = $this->LOG_USER_ID;
        $balamount = $this->ewallet_model->getBalanceAmount($userid);
        $trans_fee = $this->ewallet_model->getTransactionFee();

        $this->set('trans_fee', $trans_fee);
        $pass = $this->ewallet_model->getUserPassword($userid);

        $msg = '';
        $this->set('transaction_note', '');
        $this->set('amount', '');
        $this->set('to_user', '');
        $this->set('bal_amount', '');
        $this->set('from_user', '');
        $this->set('total_req_amount', 0);
        $this->set("step1", '');
        $this->set("step2", ' none');

        if ((!$this->input->post('dotransfer')) && $this->input->post('transfer')) {
            $this->post_fund_transfer();
        }
        $response['error'] = false;
        if ($this->input->post('dotransfer')) {
            $response['error'] = false;
            $post_arr = $this->input->post(null, true);
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);

            if (array_key_exists('to_user_name', $post_arr)) {
                $touser = $post_arr['to_user_name'];
            }
            if (array_key_exists('amount1', $post_arr)) {
                $trans_amt = round($post_arr['amount1'] / $this->DEFAULT_CURRENCY_VALUE, 8);
                $total_req_amount = $trans_amt + $trans_fee;
            }
            $to_userid = $this->ewallet_model->userNameToID($touser);

            $transaction_note = $this->validation_model->textAreaLineBreaker($post_arr['tran_concept']);

            $response['error'] = false;
            $data = [
                "transaction_note" => $transaction_note,
                "bal_amount" => $balamount,
                "to_user" => $touser,
                "amount" => $trans_amt,
                "total_req_amount" => $total_req_amount,
            ];
            $response['data'] = $data;

            echo json_encode($response);
            exit();
        }
        $request_amount = $this->ewallet_model->getRequestPendingAmount($userid);

        $this->set('request_amount', round($request_amount, 8));
        $this->set('balamount', round($balamount, 8));
        $this->set('pass', $pass);
        $this->setView();
    }

    public function validate_transfer() {
        $this->lang->load('validation');
        $user_name = $this->LOG_USER_NAME;
        $this->form_validation->set_rules('to_user_name', 'lang:user_name', "trim|required|max_length[50]|not_equals[{$user_name}]|user_exists|differs[user_name]", [
                'required' => lang('required'),
                'max_length' => sprintf(lang('maxlength'), lang('user_name'), "50"),
                'differs' => lang('invalid_user_selection'), 
                'not_equals' => lang('invalid_user_selection')
            ]
        );
        $this->form_validation->set_rules('amount1', 'lang:amount', 'trim|required|greater_than[0]|max_length[10]', [
            'required' => lang('required'),
            'max_length' => sprintf(lang("max_digits"), lang('amount'), "10"),
            'greater_than' => lang('greater_zero'),
        ]);
        $this->form_validation->set_rules('tran_concept', 'lang:transaction_note', 'trim|required', [
            'required' => lang('required'),
        ]);
        $this->form_validation->set_rules('pswd', 'lang:transaction_password', 'trim|required|max_length[100]|callback_check_transaction_password', [
            'required' => lang('required'),
            'max_length' => sprintf(lang('maxlength'), lang('transaction_password'), "50"),
            'check_transaction_password' => lang('invalid_transaction_password')
        ]);
        $validate_form = $this->form_validation->run_with_redirect('ewallet/fund_transfer');
        return $validate_form;
    }

     /**
     * [check_transaction_password validate transaction password]
     * @param  [type] $password [description]
     * @return [type]           [description]
     */
    function check_transaction_password($password) {
        $from_user_id = $this->LOG_USER_ID;
        $pass = $this->ewallet_model->getUserPassword($from_user_id);
        $msg = lang('invalid_transaction_password');
        if(!password_verify($password, $pass)) {
            $MSG_ARR["MESSAGE"]["DETAIL"] = $msg;
            $MSG_ARR["MESSAGE"]["TYPE"] = false;
            $MSG_ARR["MESSAGE"]["STATUS"] = false;
            $this->session->set_flashdata('MSG_ARR', $MSG_ARR);
            return false;
        }
        return true;
    }

    public function validate_transfer1()
    {
        $userid = $this->LOG_USER_ID;

        $to_user = $this->input->post('to_user_name', true);
        $to_userid = $this->ewallet_model->userNameToID($to_user);
        $user_exists = $this->ewallet_model->isUserNameAvailable($to_user);
        $bal_amount = $this->ewallet_model->getBalanceAmount($userid);

        if (!$this->input->post('to_user_name')) {
            $msg = lang('Please_type_To_User_name');
            $this->redirect($msg, 'ewallet/fund_transfer', false);
        }

        if ($user_exists && $userid != $to_userid) {

            if (!$this->input->post('amount1')) {
                $msg = lang('Please_type_Amount');
                $this->redirect($msg, 'ewallet/fund_transfer', false);
            }

            if (!$this->input->post('tran_concept')) {
                $msg = lang('Please_type_transaction_note');
                $this->redirect($msg, 'ewallet/fund_transfer', false);
            }

            if (!$this->input->post('pswd')) {
                $msg = lang('Please_type_transaction_password');
                $this->redirect($msg, 'ewallet/fund_transfer', false);
            }
            if (!is_numeric($this->input->post('tot_req_amount'))) {
                $msg = lang('invalid_amount_please_try_again');
                $this->redirect($msg, 'ewallet/fund_transfer', false);
            }
            if ($this->input->post('tot_req_amount') < 0 || $this->input->post('tot_req_amount') > $bal_amount) {
                $msg = lang('invalid_amount_please_try_again');
                $this->redirect($msg, 'ewallet/fund_transfer', false);
            }
        } else {
            $msg = lang('invalid_user_selection');
            $this->redirect($msg, 'ewallet/fund_transfer', false);
        }
        return true;
    }

    function getLegAmount($user_name)
    {
        $this->AJAX_STATUS = true;
        $user = $this->ewallet_model->userNameToID($user_name);
        $bal_amount = $this->ewallet_model->getBalanceAmount($user);
        echo '<td>&nbsp;&nbsp;&nbsp;&nbsp;<b>Balance Amount:</b></td><td><input type="text" name="bal"  id="bal" readonly="true" value=' . $bal_amount . ' ></td>';
    }

    function getBalance_EPin()
    {
        $this->AJAX_STATUS = true;
        $user = $this->URL['user'];
        $bal_epin = $this->Ewallet->getBalancePin($user);
        $pwd1 = $this->Ewallet->getUserPassword($user);
        echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;<b>Balance E-pin Count</b></td><td><input type='text' name='balance'  readonly='true' id='balance' value=" . $bal_epin . " ></td><input type='hidden' id='u_pwd' name='u_pwd' value=" . $pwd1 . "  /></td>";
    }

    function getPassWordInmd($pswdm)
    {
        $this->AJAX_STATUS = true;
        $mdpsw = md5($pswdm);
        echo '<td><input type="hidden" id="md_psd" name="md_psd" value=' . $mdpsw . '  /></td>';
    }

    function ewallet_pin_purchase()
    {

        $title = lang('e_pin_purchase');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $help_link = 'pin-purchase';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('e_pin_purchase');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('e_pin_purchase');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $user_id = $this->LOG_USER_ID;
        $balamount = $this->ewallet_model->getBalanceAmount($user_id);
        $amount_details = $this->ewallet_model->getAllEwalletAmounts();
        $msg = '';

        if ($this->input->post('transfer') && $this->validate_ewallet_pin_purchase()) {

            $pin_post_array = $this->input->post(null, true);
            $pin_post_array = $this->validation_model->stripTagsPostArray($pin_post_array);

            $pin_count = $pin_post_array['pin_count'];
            $amount_id = $pin_post_array['amount'];

            if ($pin_count > 0 && $amount_id != '' && is_numeric($pin_count)) {
                $tran_pass = $pin_post_array['passcode'];
                $dbpass = $this->ewallet_model->getTransactionPasscode($user_id);
                if (password_verify($tran_pass, $dbpass)) {
                    $amount = $this->ewallet_model->getEpinAmount($amount_id);
                    $tot_avb_amt = $amount * $pin_count;
                    if ($tot_avb_amt <= $balamount) {
                        $uploded_date = date('Y-m-d H:i:s');
                        // $expiry_date = date('Y-m-d', strtotime('+6 months', strtotime($uploded_date)));
                        $expiry_date = $pin_post_array['expiry_date'];
                        $purchase_status = 'yes';
                        $status = 'yes';
                        $this->ewallet_model->begin();

                        $max_pincount = $this->ewallet_model->getMaxPinCount();
                        $rec = $this->ewallet_model->getAllActivePinspage($purchase_status);
                        if ($rec < $max_pincount) {
                            $errorcount = $max_pincount - $rec;
                            if ($pin_count <= $errorcount) {
                                $transaction_id = $this->ewallet_model->getUniqueTransactionId();
                                $res = $this->ewallet_model->generatePasscode($pin_count, $status, $uploded_date, $amount, $expiry_date, $purchase_status, $amount_id, $user_id, $user_id, $transaction_id);
                            }
                        } else {
                            $msg1 = lang('already');
                            $msg2 = lang('epin_present');
                            $this->redirect($msg1 . $rec . $msg2, 'epin/generate_epin', false);
                        }

                        if ($res) {

                            $bal = round($balamount - $tot_avb_amt, 8);
                            $update = $this->ewallet_model->updateBalanceAmount($user_id, $bal);
                            if ($res && $update) {
                                $this->ewallet_model->commit();
                                $loggin_id = $this->LOG_USER_ID;
                                $data_array = array();
                                $data_array['pin_post_array'] = $pin_post_array;
                                $data = serialize($data_array);
                                $this->validation_model->insertUserActivity($loggin_id, 'epin purchased', $loggin_id, $data);

                                $msg = lang('epin_purchased_successfully');
                                $this->redirect($msg, 'ewallet/ewallet_pin_purchase', true);
                            } else {
                                $this->ewallet_model->rollback();
                                $msg = lang('error_on_epin_purchase');
                                $this->redirect($msg, 'ewallet/ewallet_pin_purchase', false);
                            }
                        } else {
                            $this->ewallet_model->rollback();
                            $mail = $this->ewallet_model->getAdminEmailId();
                            $mailBodyDetails = '<html>
							<head>
							<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
							</head>
							<body >
							<table id="Table_01" width="600"   border="0" cellpadding="0" cellspacing="0">
							<tr><td><br />Dear Admin,<br /></td></tr>
							<tr><td>There is no active E-pin for the product in your company. Please generate new E-pin.</td></tr>
							<tr><td>Thanks,<br />World Class Reward</td></tr>
							</table>
							</body></html>';
                            $res = $this->validation_model->sendEmail($mailBodyDetails, $user_id, '');
                            $msg = lang('no_epin_found_please_contact_administrator');
                            $this->redirect($msg, 'ewallet/ewallet_pin_purchase', false);
                        }
                    } else {
                        $msg = lang('no_sufficient_balance_amount');
                        $this->redirect($msg, 'ewallet/ewallet_pin_purchase', false);
                    }
                } else {
                    $msg = lang('invalid_transaction_password');
                    $this->redirect($msg, 'ewallet/ewallet_pin_purchase', false);
                }
            } else {
                $msg = lang('error_on_purchasing_epin_please_try_again');
                $this->redirect($msg, 'ewallet/ewallet_pin_purchase', false);
            }
        }

        $this->set('balamount', $balamount);
        $this->set('amount_details', $amount_details);
        $this->setView();
    }

    public function validate_ewallet_pin_purchase()
    {    
        $this->lang->load('validation', $this->LANG_NAME);
        $this->form_validation->set_rules('passcode', lang('transaction_password'), 'trim|required|max_length[100]',[
           "required"=>lang('required'),
           "max_length"=>sprintf(lang('maxlength'),lang('transaction_password'),"100")
        ]);
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required|greater_than[0]|numeric',[
         "required"=>lang('required'),
         "greater_than"=>sprintf(lang('field_greater_than_zero'),lang('amount')),
         'numeric'=>lang('digits')
        ]);
        $this->form_validation->set_rules('pin_count', lang('epin_count'), 'trim|required|integer|greater_than[0]',[
           "required"=>lang('required'),
           "integer"=>lang('digits'),
           "greater_than"=>sprintf(lang('field_greater_than_zero'),lang('epin_count'))

        ]);

        $this->form_validation->set_rules('expiry_date', lang('expiry_date'), 'trim|required|valid_date|date_less_than_current_date', [
            'required' => sprintf(lang('required'), lang('date')),
            'valid_date' => lang('select_valid_date'),
            'date_less_than_current_date' => lang('valid_date')
        ]);

        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function my_transfer_details() {
        // HEADER DATA
        $title = $this->lang->line('transfer_details_ewallet');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);
        $help_link = 'my-transfer';
        $this->set('help_link', $help_link);
        $this->HEADER_LANG['page_top_header'] = lang('transfer_details_ewallet');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('transfer_details_ewallet');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();
        
        // FILTER
        $daterange = $this->input->get('daterange') ?: 'all';
        $from_date = $to_date = "";
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));
        // DATA
        $fund_transfer_details = $this->ewallet_model->getUserEwalletDetails($this->LOG_USER_ID, $from_date, $to_date, $this->input->get('offset'), $this->PAGINATION_PER_PAGE);
        
        // PAGINATION
        $count = $this->ewallet_model->getUserEwalletDetailsCount($this->LOG_USER_ID, $from_date, $to_date);
        $this->pagination->set_all('user/my_transfer_details', $count);

        // VIEW
        $this->set('fund_transfer_details', $fund_transfer_details);
        $this->set('daterange', $daterange);
        $this->setView();
        return false;

        $weekdate = (strip_tags($this->input->post('weekdate', true)));
        $daily = (strip_tags($this->input->post('daily', true)));
        $this->set('weekdate', $weekdate);
        $this->set('daily', $daily);

        $weekly_session = 0;
        $daily_session = 0;
        $result_per_page = $this->PAGINATION_PER_PAGE;


        if (($this->input->post('weekdate')) || ($this->session->userdata('inf_my_transfer_details_weekly'))) {


            $this->session->unset_userdata('inf_my_transfer_details_daily');

            $user_name = $this->LOG_USER_NAME;
            $user_id = $this->ewallet_model->userNameToID($user_name);

            $weekly_session = 1;
            if($this->input->post('weekdate') && $this->validate_my_transfer_details_weekdate()){

             $post_arr = $this->input->post(NULL, TRUE);
             $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
             $from_date = $post_arr['week_date1'];
              $to_date = $post_arr['week_date2'];
               if ($from_date != '') {
                $from_date = $from_date . ' 00:00:00';
               } else {
                $from_date = '';
               }
               if ($to_date != '') {
                $to_date = $to_date . ' 23:59:59';
               } else {
                $to_date = '';
               }
            }
            else{
             $from_date = $this->session->userdata('inf_my_transfer_details_from_data');
             $to_date = $this->session->userdata('inf_my_transfer_details_to_data');

            }

            $this->session->set_userdata('inf_my_transfer_details_weekly',$user_name);
            $this->session->set_userdata('inf_my_transfer_details_from_data', $from_date);
            $this->session->set_userdata('inf_my_transfer_details_to_data', $to_date);

            $count = $this->ewallet_model->getUserEwalletDetailsCount($user_id, $from_date, $to_date);

            $base_url = base_url() . 'user/ewallet/my_transfer_details/';
            $config = $this->pagination->customize_style();
            $config['base_url'] = $base_url;
             //$config['use_page_numbers'] = TRUE;
            $config['per_page'] = $this->PAGINATION_PER_PAGE;
            $page = ($this->uri->segment(4) != "") ? $this->uri->segment(4) : 0;
            $config['total_rows'] = $count;
            $this->pagination->initialize($config);
            

            $details = $this->ewallet_model->getUserEwalletDetails($user_id, $from_date, $to_date,$page,$config['per_page']);
            $this->set('details', $this->security->xss_clean($details));
            $this->set('user_name', $user_name);
            $details_count = count($details);
            $this->set('details_count', $details_count);
            $this->set('result_per_page',$result_per_page);
            $this->set('page',$page);
        }
        if (($this->input->post('daily')) || ($this->session->userdata('inf_my_transfer_details_daily'))) {

            $this->session->unset_userdata('inf_my_transfer_details_weekly');
            $user_name = $this->LOG_USER_NAME;
            $user_id = $this->ewallet_model->userNameToID($user_name);

            $daily_session = 1;

            if($this->input->post('daily') && $this->validate_my_transfer_details_daily()){

             $post_arr = $this->input->post(NULL, TRUE);
             $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
             $from_date = $post_arr['week_date3'] . ' 00:00:00';
             $to_date = $post_arr['week_date3'] . ' 23:59:59';
            }
            else{
             $from_date = $this->session->userdata('inf_my_transfer_details_from_data');
             $to_date = $this->session->userdata('inf_my_transfer_details_to_data');

            }

            $this->session->set_userdata('inf_my_transfer_details_daily',$user_name);
            $this->session->set_userdata('inf_my_transfer_details_from_data', $from_date);
            $this->session->set_userdata('inf_my_transfer_details_to_data', $to_date);
            $count = $this->ewallet_model->getUserEwalletDetailsCount($user_id, $from_date, $to_date);
            $base_url = base_url() . 'user/ewallet/my_transfer_details/';
            $config = $this->pagination->customize_style();
            $config['base_url'] = $base_url;
             //$config['use_page_numbers'] = TRUE;
            $config['per_page'] = $this->PAGINATION_PER_PAGE;
            $page = ($this->uri->segment(4) != "") ? $this->uri->segment(4) : 0;
            $config['total_rows'] = $count;
            $this->pagination->initialize($config);
            

            $details = $this->ewallet_model->getUserEwalletDetails($user_id, $from_date, $to_date,$page,$config['per_page']);
            $this->set('details', $this->security->xss_clean($details));
            $this->set('user_name', $user_name);
            $details_count = count($details);
            $this->set('details_count', $details_count);
            $this->set('result_per_page',$result_per_page);
            $this->set('page',$page);
        }
        $this->set('daily_session',$daily_session);
        $this->set('weekly_session',$weekly_session);
        $this->setView();
    }

    public function validate_my_transfer_details_weekdate()
    {
        $post_arr = $this->validation_model->stripTagsPostArray($this->input->post());

        //        if (!$post_arr['week_date1']) {
        //            $msg = lang('please_select_from_date');
        //            $this->redirect($msg, 'ewallet/my_transfer_details', FALSE);
        //        }
        //        if (!$post_arr['week_date2']) {
        //            $msg = lang('please_select_to_date');
        //            $this->redirect($msg, 'ewallet/my_transfer_details', FALSE);
        //        }

        if (!$post_arr['week_date1'] && !$post_arr['week_date2']) {
            $msg = lang('Please select atleast one criteria.');
            $this->redirect($msg, 'ewallet/my_transfer_details', false);
        }
        if ($post_arr['week_date1'] > $post_arr['week_date2']) {
            $msg = lang('to_date_should_greater_than_or_equal_to_from_date');
            $this->redirect($msg, 'ewallet/my_transfer_details', false);
        }
        return true;
    }

    public function validate_my_transfer_details_daily()
    {
        $post_arr = $this->validation_model->stripTagsPostArray($this->input->post());

        if (!$post_arr['week_date3']) {
            $msg = lang('please_select_date');
            $this->redirect($msg, 'ewallet/my_transfer_details', false);
        }

        return true;
    }

    public function user_availability()
    {
        if ($this->ewallet_model->checkUser((strip_tags($this->input->post('user_name', true))))) {
            echo "yes";
            exit();
        } else {
            echo "no";
            exit();
        }
    }

    function post_fund_transfer() {
        if ($this->input->post('transfer') && $this->validate_transfer()) {
            $transfer_post_array = $this->input->post(null, true);
            $transfer_post_array = $this->validation_model->stripTagsPostArray($transfer_post_array);
            $userid = $this->LOG_USER_ID;
            $balamount = $this->ewallet_model->getBalanceAmount($userid);
            $trans_fee = $this->ewallet_model->getTransactionFee();
            $pass = $this->ewallet_model->getUserPassword($userid);
            $tran_pass = $transfer_post_array['pswd'];
            $to_user_name = $transfer_post_array['to_user_name'];
            $to_user_id = $this->ewallet_model->userNameToID($to_user_name);
            $trans_amt = $transfer_post_array['amount1'];
            $trans_amt = round($trans_amt / $this->DEFAULT_CURRENCY_VALUE, 8);
            $transaction_concept = $this->validation_model->textAreaLineBreaker($transfer_post_array['tran_concept']);
            $total_req_amount = $trans_amt + $trans_fee;
            if ($total_req_amount <= $balamount) {
                if (password_verify($tran_pass, $pass)) {
                    $this->ewallet_model->begin();
                    $transaction_id = $this->ewallet_model->getUniqueTransactionId();
                    $up_date1 = $this->ewallet_model->updateBalanceAmountDetailsFrom($userid, round($total_req_amount, 8));
                    $up_date2 = $this->ewallet_model->updateBalanceAmountDetailsTo($to_user_id, round($trans_amt, 8));
                    $this->ewallet_model->insertBalAmountDetails($userid, $to_user_id, round($trans_amt, 8), $amount_type = '', $transaction_concept, $trans_fee, $transaction_id);
                    if ($up_date1 && $up_date2) {
                        $this->ewallet_model->commit();
                        $login_id = $this->LOG_USER_ID;
                        $data_array = array();
                        $data_array['transfer_post_array'] = $transfer_post_array;
                        $data = serialize($data_array);
                        $this->validation_model->insertUserActivity($login_id, 'fund transferred', $to_user_id, $data);
                        $msg = lang('fund_transfered_successfully');
                        $this->redirect($msg, 'ewallet/fund_transfer', true);
                    } else {
                        $this->ewallet_model->rollback();
                        $msg = lang('error_on_fund_transfer');
                        $this->redirect($msg, 'ewallet/fund_transfer', false);
                    }
                } else {
                    $msg = lang('invalid_transaction_password');
                    $this->redirect($msg, 'ewallet/fund_transfer', false);
                }
            } else {
                $msg = lang('low_balance_please_try_again');
                $this->redirect($msg, 'ewallet/fund_transfer', false);
            }
        }
    }

    function validate_username($ref_user = '')
    {
        if ($ref_user != '') {
            $flag = false;
            if ($this->validation_model->isUserNameAvailable($ref_user)) {
                $flag = true;
            }
            return $flag;
        } else {
            $echo = 'no';
            $username = ($this->input->post('username', true));

            if ($this->validation_model->isUserNameAvailable($username)) {
                $echo = "yes";
            }
            echo $echo;
            exit();
        }
    }

    public function add_purchase_wallet_amount()
    {
        $this->url_permission('purchase_wallet');
        if ($this->input->post('add_fund')) {
            $this->form_validation->set_rules('amount', 'lang:amount', "trim|required|greater_than[0]");
            $validate_form = $this->form_validation->run_with_redirect('ewallet/purchase_wallet');
            if ($validate_form) {
                $payment_amount = $this->input->post('amount', TRUE);
                $this->load->model('payment_model');
                if ($this->payment_model->isPaypalEnabled()) {
                    $session_data = array(
                        'user_id' => $this->LOG_USER_ID,
                        'payment_amount' => $payment_amount
                    );
                    $this->session->set_userdata('purchase_wallet_payment', $session_data);
                    require_once dirname(__DIR__) . '/Paypal.php';
                    $paypal = new Paypal;
                    $paypal_currency_code = "USD";
                    $paypal_currency_left_symbol = "$";
                    $paypal_currency_right_symbol = "";
                    $payment_amount = round($payment_amount / $this->DEFAULT_CURRENCY_VALUE, 8);
                    $usd_conevrsion_rate = 1;
                    $payment_amount = round($payment_amount / $usd_conevrsion_rate, 8);
                    $description = "Fund deposit to purchase wallet - " . $this->COMPANY_NAME;
                    $description .= "\nAmount : $paypal_currency_left_symbol $payment_amount $paypal_currency_right_symbol";
                    $params = array(
                        'amount' => $payment_amount,
                        'item' => "Fund deposit to purchase wallet",
                        'description' => $description,
                        'currency' => $paypal_currency_code,
                        'return_url' => BASE_URL . "/user/ewallet/paypal_purchase_wallet_success",
                        'cancel_url' => BASE_URL . "/user/ewallet/purchase_wallet"
                    );

                    $response = $paypal->initilize($params);
                    if (!$response->success()) {
                        $this->redirect(lang('paypal_payment_error'), 'ewallet/purchase_wallet', FALSE);
                    }
                } else {
                    $this->redirect(lang('paypal_disabled'), 'ewallet/purchase_wallet', FALSE);
                }
            }
        }
    }

    public function paypal_purchase_wallet_success()
    {
        require_once dirname(__DIR__) . '/Paypal.php';
        $paypal = new Paypal;
        $payment_data = $this->session->userdata('purchase_wallet_payment');
        $payment_amount = $payment_data['payment_amount'];
        $paypal_currency_code = "USD";
        $paypal_currency_left_symbol = "$";
        $paypal_currency_right_symbol = "";
        $payment_amount = $ewallet_amount = round($payment_amount / $this->DEFAULT_CURRENCY_VALUE, 8);
        $usd_conevrsion_rate = 1;
        $payment_amount = round($payment_amount / $usd_conevrsion_rate, 8);
        $params = array(
            'amount' => $payment_amount,
            'currency' => $paypal_currency_code,
            'return_url' => BASE_URL . "/user/ewallet/paypal_purchase_wallet_success",
            'cancel_url' => BASE_URL . "/user/ewallet/purchase_wallet"
        );
        $response = $paypal->callback($params);
        if ($response->success()) {
            $payment_res = TRUE;
            $this->load->model('register_model');
            $paypal_output = $this->input->get();
            $payment_details = [
                'payment_method' => 'paypal',
                'token_id' => $paypal_output['token'],
                'currency' => $paypal_currency_code,
                'amount' => $payment_amount,
                'acceptance' => '',
                'payer_id' => $paypal_output['PayerID'],
                'user_id' => $this->LOG_USER_ID,
                'status' => '',
                'card_number' => '',
                'ED' => '',
                'card_holder_name' => '',
                'submit_date' => date("Y-m-d H:i:s"),
                'pay_id' => '',
                'error_status' => '',
                'brand' => ''
            ];
            $this->register_model->insertintoPaymentDetails($payment_details);
        } else {
            $payment_res = FALSE;
        }

        $this->session->unset_userdata('purchase_wallet_payment');
        if ($payment_res) {
            $this->inf_model->begin();
            $res = $this->ewallet_model->addFundToPurchaseWallet($this->LOG_USER_ID, $ewallet_amount, 'fund_deposit_paypal');
            if ($res) {
                $this->inf_model->commit();
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, sprintf(lang('purchase_wallet_fund_deposit_paypal'), $this->DEFAULT_SYMBOL_LEFT . $ewallet_amount . $this->DEFAULT_SYMBOL_RIGHT), $this->LOG_USER_ID, serialize([]));
                $this->redirect(lang('add_fund_success'), 'ewallet/purchase_wallet', true);
            } else {
                $this->inf_model->rollback();
                $this->redirect(lang('add_fund_error'), 'ewallet/purchase_wallet', false);
            }
        } else {
            $this->redirect(lang('paypal_payment_error'), 'ewallet/purchase_wallet', false);
        }
    }

    function fund_transfer_post()
    {
        if ($this->input->post()) {
            $validated = $this->fund_transfer_validation();
            if ($validated['status']) {
                $transfer_post_array = $this->input->post(null, true);
                $trans_fee = $this->ewallet_model->getTransactionFee();
                $tran_pswd = $transfer_post_array['pswd'];
                // $from_user = $transfer_post_array['user_name'];
                $from_user_id = $this->LOG_USER_ID;
                // $to_user_name = $transfer_post_array['to_user_name'];
                $to_user_id = $this->ewallet_model->userNameToID($transfer_post_array['to_user_name']);
                $trans_amount = $transfer_post_array['amount'];
                $trans_amount = round($trans_amount / $this->DEFAULT_CURRENCY_VALUE, 8);
                $transaction_concept = $this->validation_model->textAreaLineBreaker($transfer_post_array['transaction_note']);
                $total_req_amount = $trans_amount + $trans_fee;
                $pass = $this->ewallet_model->getUserPassword($from_user_id);
                $balamount = $this->ewallet_model->getBalanceAmount($from_user_id);
                if ($total_req_amount <= $balamount) {
                    if (password_verify($tran_pswd, $pass)) {
                        $this->ewallet_model->begin();
                        $transaction_id = $this->ewallet_model->getUniqueTransactionId();
                        $up_date1 = $this->ewallet_model->updateBalanceAmountDetailsFrom($from_user_id, round($total_req_amount, 8));
                        $up_date2 = $this->ewallet_model->updateBalanceAmountDetailsTo($to_user_id, round($trans_amount, 8));
                        // $amount_type = $from_user_id == $this->LOG_USER_ID ? 'user_debit' : 'user_credit';
                        // $amount_type = 'fund_transfer';
                        $this->ewallet_model->insertBalAmountDetails($from_user_id, $to_user_id, round($trans_amount, 8), '', $transaction_concept, $trans_fee, $transaction_id);
                        if ($up_date1 && $up_date2) {
                            $this->ewallet_model->commit();
                            $data = serialize($transfer_post_array);
                            $this->validation_model->insertUserActivity($from_user_id, 'fund transferred', $this->LOG_USER_ID, $data);

                            // Employee Activity History
                            if ($this->LOG_USER_TYPE == 'employee') {
                                $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'transfer_fund', 'Fund Transferred');
                            }
                            echo json_encode([
                                'status' => true,
                                'message' => lang('fund_transfered_successfully')
                            ]);
                            exit();
                        } else {
                            $this->ewallet_model->rollback();
                            echo json_encode([
                                'status' => false,
                                'message' => lang('error_on_fund_transfer')
                            ]);
                            exit();
                        }
                    } else {
                        echo json_encode([
                            'status' => false,
                            'message' => lang('invalid_transaction_password')
                        ]);
                        exit();
                    }
                } else {
                    echo json_encode([
                        'status' => false,
                        'message' => lang('low_balance_please_try_again')
                    ]);
                    exit();
                }
            } else {
                echo json_encode($validated);
                exit();
            }
        }
    }

    protected function fund_transfer_validation()
    {
        $this->lang->load('validation');
        $this->form_validation->set_rules('to_user_name', 'lang:user_name', 'trim|required|max_length[50]|user_exists|callback_differs_user_name', [
                "required"    => lang('required'),
                "max_length"  => sprintf(lang("maxlength"), lang('user_name'), "50"),
                "user_exists" => lang('username_not_available'),
                'differs_user_name'     => lang('invalid_username'),
            ]
        );

        $this->form_validation->set_rules('amount', 'lang:amount', 'trim|required|numeric|greater_than_equal_to[0]|max_length[10]|callback_balance_check',[
                "required"              => lang('required'),
                "numeric"               => lang('digits'),
                "greater_than_equal_to" => lang('greater_zero'),
                "max_length"            => sprintf(lang("max_digits"), lang('amount'), "10"),
                "balance_check"         => lang('insufficient_balance')
            ]
        );
        
        $this->form_validation->set_rules('transaction_note', 'lang:transaction_note', 'trim|required|max_length[1000]',[
                "required" => lang('required'),
                "max_length" => sprintf(lang("maxlength"), lang("transaction_note"), "1000")
            ]
        );

        $this->form_validation->set_rules('pswd', lang('transaction_password'), 'trim|required|max_length[100]|callback_check_transaction_password', [
                'required' => lang('required'),
                "max_length"  => sprintf(lang("maxlength"), lang('transaction_password'), "100"),
                'check_transaction_password' => lang('invalid_transaction_password')
            ]
        );

        $status = $this->form_validation->run();
        if (!$status) {
            return [
                'status' => false,
                'validation_error' => $this->form_validation->error_array(),
                'message' => lang('errors_check')
            ];
        }
        return [
            'status' => true
        ];
    }

     /**
     * [balance_check check balnce to fund transfer]
     * @param  [type] $amount1 [description]
     * @return [type]          [description]
     */
    public function balance_check($amount1) {
        $balance_amount = $this->ewallet_model->getBalanceAmount($this->LOG_USER_ID);
        if(($amount1+$this->input->post('tran_fee')) <= $balance_amount) {
            return true;            
        }
        return false;
    }

    public function differs_user_name($user_name) {
        if($this->LOG_USER_NAME == $user_name) {
            return false;
        }
        return true;
    }

    public function summary_total() {
        $total = $this->ewallet_model->getEwalletOverviewTotal($this->LOG_USER_ID);
        $balamount = $this->ewallet_model->getBalanceAmount($this->LOG_USER_ID);
        $agent_list = $this->ewallet_model->getAgentList();
        $data = [
            'credited'          => thousands_currency_format($total['credit']),
            'debited'           => thousands_currency_format($total['debit']),
            'balance'           => thousands_currency_format($balamount),
            'purchase_wallet'   => thousands_currency_format($this->validation_model->getPurchaseWalletAmount($this->LOG_USER_ID)),
            'commission_earned' => thousands_currency_format($this->ewallet_model->getTotalCommissionEarned($this->LOG_USER_ID)),
            'balamount'         => convert_currency($this->ewallet_model->getBalanceAmount($this->LOG_USER_ID)),
            'trans_fee'         => convert_currency($this->ewallet_model->getTransactionFee()),
            'agnet_name'        =>$this->ewallet_model->getAgentList()
        ];
        echo json_encode($data);
        exit();   
    }
    //fund transfer to agent 
    function fund_transfer_to_agent_post()
    {
        if ($this->input->post()) {
            //dd($this->input->post());
            $validated = $this->fund_transfer_to_agent_validation();
            if ($validated['status']) {
                $transfer_post_array = $this->input->post(null, true);

                $trans_fee = $this->ewallet_model->getTransactionFee();
                $tran_pswd = $transfer_post_array['pswd'];
                // $from_user = $transfer_post_array['user_name'];
                $from_user_id = $this->LOG_USER_ID;
                // $to_user_name = $transfer_post_array['to_user_name'];
                $to_agent_id = $this->validation_model->agentUserNameToID($transfer_post_array['to_agent_name']);
                $trans_amount = $transfer_post_array['amount'];
                $trans_amount = round($trans_amount / $this->DEFAULT_CURRENCY_VALUE, 8);
                $transaction_concept = $this->validation_model->textAreaLineBreaker($transfer_post_array['transaction_note']);
                $total_req_amount = $trans_amount + $trans_fee;
                $pass = $this->ewallet_model->getUserPassword($from_user_id);
                $balamount = $this->ewallet_model->getBalanceAmount($from_user_id);
                if ($total_req_amount <= $balamount) {
                    if (password_verify($tran_pswd, $pass)) {
                        $this->ewallet_model->begin();
                        $transaction_id = $this->ewallet_model->getUniqueTransactionId();
                        $up_date1 = $this->ewallet_model->updateBalanceAmountDetailsFrom($from_user_id, round($total_req_amount, 8));
                        $up_date2 = $this->validation_model->updateAgentWallet($to_agent_id, round($trans_amount, 8));
                        //dd($up_date2);
                        // $amount_type = $from_user_id == $this->LOG_USER_ID ? 'user_debit' : 'user_credit';
                        // $amount_type = 'fund_transfer';
                        $this->ewallet_model->insertFundToAgentBalAmountDetails($from_user_id, $to_agent_id, round($trans_amount, 8), '', $transaction_concept, $trans_fee, $transaction_id);
                        if ($up_date1 && $up_date2) {
                            $this->ewallet_model->commit();
                            $data = serialize($transfer_post_array);
                            $this->validation_model->insertUserActivity($from_user_id, 'fund transferred', $this->LOG_USER_ID, $data);

                            // Employee Activity History
                            if ($this->LOG_USER_TYPE == 'employee') {
                                $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'transfer_fund', 'Fund Transferred');
                            }
                            echo json_encode([
                                'status' => true,
                                'message' => lang('fund_transfered_successfully')
                            ]);
                            exit();
                        } else {
                            $this->ewallet_model->rollback();
                            echo json_encode([
                                'status' => false,
                                'message' => lang('error_on_fund_transfer')
                            ]);
                            exit();
                        }
                    } else {
                        echo json_encode([
                            'status' => false,
                            'message' => lang('invalid_transaction_password')
                        ]);
                        exit();
                    }
                } else {
                    echo json_encode([
                        'status' => false,
                        'message' => lang('low_balance_please_try_again')
                    ]);
                    exit();
                }
            } else {
                echo json_encode($validated);
                exit();
            }
        }
    }
    //validation
    protected function fund_transfer_to_agent_validation()
    {
        $this->lang->load('validation');
        $this->form_validation->set_rules('to_agent_name', 'lang:user_name', 'trim|required|max_length[50]|callback_differs_user_name', [
                "required"    => lang('required'),
                "max_length"  => sprintf(lang("maxlength"), lang('user_name'), "50"),
                // "user_exists" => lang('username_not_available'),
                'differs_user_name'     => lang('invalid_username'),
            ]
        );

        $this->form_validation->set_rules('amount', 'lang:amount', 'trim|required|numeric|greater_than_equal_to[0]|max_length[10]|callback_balance_check',[
                "required"              => lang('required'),
                "numeric"               => lang('digits'),
                "greater_than_equal_to" => lang('greater_zero'),
                "max_length"            => sprintf(lang("max_digits"), lang('amount'), "10"),
                "balance_check"         => lang('insufficient_balance')
            ]
        );
        
        $this->form_validation->set_rules('transaction_note', 'lang:transaction_note', 'trim|required|max_length[1000]',[
                "required" => lang('required'),
                "max_length" => sprintf(lang("maxlength"), lang("transaction_note"), "1000")
            ]
        );

        $this->form_validation->set_rules('pswd', lang('transaction_password'), 'trim|required|max_length[100]|callback_check_transaction_password', [
                'required' => lang('required'),
                "max_length"  => sprintf(lang("maxlength"), lang('transaction_password'), "100"),
                'check_transaction_password' => lang('invalid_transaction_password')
            ]
        );

        $status = $this->form_validation->run();
        if (!$status) {
            return [
                'status' => false,
                'validation_error' => $this->form_validation->error_array(),
                'message' => lang('errors_check')
            ];
        }
        return [
            'status' => true
        ];
    }


}
