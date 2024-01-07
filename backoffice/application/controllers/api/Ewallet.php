<?php

require_once 'Inf_Controller.php';

class Ewallet extends Inf_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('ewallet_model');
        $this->LOG_USER_ID = $this->rest->user_id;
        $this->LOG_USER_TYPE = 'user';
        $this->LOG_USER_NAME = $this->validation_model->IdToUserName($this->LOG_USER_ID);
    }

    function balance_get()
    {
        $user_id = $this->rest->user_id;
        $res = $this->Api_model->get_ewallet_balance($user_id);
        $this->set_success_response(200, $res);

    }

    // react api 
    // tile
    public function ewallet_tile_get()
    {
        $balamount = $this->ewallet_model->getBalanceAmount($this->LOG_USER_ID);
        $total = $this->ewallet_model->getEwalletOverviewTotal($this->LOG_USER_ID);
        $debit =  ($total['credit']-$balamount) > 0? $total['credit']-$balamount : 0 ;
        $transactionFee = $this->ewallet_model->getTransactionFee();
        $data['transactionFee'] = $transactionFee;


        if($this->IS_MOBILE)
        {
            $data['transactionFee'] = convert_currency($transactionFee);
        }

        $data['repurchase_status']  = $this->MODULE_STATUS['repurchase_status'];
        $data['purchase_wallet']    = $this->MODULE_STATUS['purchase_wallet'];

                
        $data['transactionFeewithCurrency'] = format_currency($this->ewallet_model->getTransactionFee());
        $data['ewallet_tile'][] = [
                'amount' => $total['credit'],
                'text' => 'credited',
                'amountWithCurrency'    => thousands_currency_format($total['credit']),
                'icon'=>SITE_URL . "/uploads/images/logos/income-w.png",
                'bg_color'=>"#8777DE"

        ];
        $data['ewallet_tile'][] = [
                'amount' => $debit,
                'text' => 'debited',
                'amountWithCurrency'    => thousands_currency_format($debit),
                'icon'=>SITE_URL . "/uploads/images/logos/Bonus-w.png",
                'bg_color'=>"#38A5A9"
        ];
        $data['ewallet_tile'][] = [
                'amount' => $balamount,
                'text' => 'ewalletBalance',
                'amountWithCurrency'    => thousands_currency_format($balamount),
                'icon'=>SITE_URL . "/uploads/images/logos/E-Wallet-w.png",
                'bg_color'=>"#5B9CCE"

        ];
        $data['ewallet_tile'][] = [
                'amount' => $this->validation_model->getPurchaseWalletAmount($this->LOG_USER_ID),
                'text' => 'purchaseWallet',
                'amountWithCurrency'    => thousands_currency_format($this->validation_model->getPurchaseWalletAmount($this->LOG_USER_ID)),
                'icon'=>SITE_URL . "/uploads/images/logos/income-w.png",
                'bg_color'=>"#6176C1"

        ];
        $data['ewallet_tile'][] = [
                'amount' =>  $this->ewallet_model->getTotalCommissionEarned($this->LOG_USER_ID),
                'text' => 'commissionEarned',
                'amountWithCurrency'    => thousands_currency_format($this->ewallet_model->getTotalCommissionEarned($this->LOG_USER_ID)),
                'icon'=>SITE_URL . "/uploads/images/logos/income-w.png",
                'bg_color'=>"#E0937A"

        ];
        $data['balance'] = $balamount;
        $data['balanceWithCurrency'] = convert_currency($balamount);
        $this->set_success_response(200, $data);
    }
    //end of tile


    //ewallet statement
    public function ewallet_statement_table_get()
    {
        $order_columns = [
            2 => 'amount',
            3 => 'date_added',
        ];
        $feild = $this->input->get(null, true);
        $order = $feild['order']??'';
        // dd($order);
        $direction = $feild['direction']??'asc';
        $filter = [
            'limit' => $feild['length']??10,
            'start' => $feild['start']??0,
            'order' => $order_columns[$order]??null,
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];
        $page       = $filter['start'];
        $statement  = $this->ewallet_model->getUserEwalletStatement($this->LOG_USER_ID, $filter);
        $count      = $this->ewallet_model->getEwalletHistoryCount($this->LOG_USER_ID);
        $previous_ewallet_balance = $this->ewallet_model->getPreviousEwalletBalance($this->LOG_USER_ID, $page);
        $balance = $previous_ewallet_balance;
        $debit   = 0;
        $credit = 0;

        $data = [];
        $data['count'] =$count;
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
                    $amount = "( ".lang('transfer_fee') ." - ".format_currency($item['transaction_fee']).")";
                } else if($item['ewallet_type'] == "payout") {
                    $amount = "( " .lang('payout_fee') ." - " .format_currency($item['transaction_fee']).")";

                }
            }
            $data['table_data'][] = [
                'index' => $key + $filter['start'] + 1,
                'description' => $this->getDescription($item, $amount),
                'amount' => $this->IS_MOBILE ? format_currency(($item['amount']-$item['purchase_wallet'] + $item['transaction_fee'])) : format_currency_without_comma(($item['amount']-$item['purchase_wallet'] + $item['transaction_fee'])),
                'type'   => $item['type'],
                'transaction_date' => date("F j, Y, g:i a",strtotime($item['date_added'])),
                'balance' => $this->IS_MOBILE ? format_currency($balance) : format_currency_without_comma($balance),
            ];
        }
        $this->set_success_response(200, $data);
    } 


    public function getDescription($item, $transaction_fee) 
    {
        $this->lang->load('ewallet', $this->LANG_NAME);
        $this->lang->load('common', $this->LANG_NAME);
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
            }
        } elseif($item['ewallet_type'] == "commission") {
            if($item['amount_type'] == "donation") {
                if ($item['type'] == "debit") {
                    $description = lang('donation_debit'). ' ' .$item['from_user'] ;
                } else {
                    $description = lang('donation_credit'). ' '.$item['from_user'] ;
                }
            } elseif($item['amount_type'] == 'board_commission' && $this->MODULE_STATUS['table_status'] == 'yes') {
                $description = lang('table_commission') ;
            } else {
                if (in_array($item['amount_type'], $from_user_amount_types)) {
                    $description = lang($item['amount_type']).' '.lang('from') . ' ' . $item['from_user'] ;
                } else {
                    $description = lang($item['amount_type']) ;
                }
            }
        } elseif ($item['ewallet_type'] == "ewallet_payment") {
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
    //end of ewallet statement 



 


    //ewallet history
    public function ewallet_history_table_get()
    {
         $order_columns = [
            // 0 => 'amount_type',
            // 1 => 'amount',
            'transaction_fee' => 'trans_fee',
            // 4 => 'amount_type',
            'date' => 'date'
        ];

        $feild = $this->input->get(null, true);
        $order = $feild['order']??'';
        // dd($order);
        $direction = $feild['direction']??'asc';
        $filter = [
            'limit' => $feild['length']??10,
            'start' => $feild['start']??0,
            'order' => $order_columns[$order]??null,
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];
         // dd($filter);
        
        $from_date = $this->input->get('start_date');
        $to_date = $this->input->get('end_date');
        $type = $this->input->get('type', true)??['user_debit','user_credit'];
        if(empty($type))
        {
          $type = ['user_debit','user_credit'];
        }
        //dd($type);
        $count = $this->Api_model->getUserEwalletHistoryTableCount($this->LOG_USER_ID, $from_date, $to_date, $type);
        $fund_transfer_details = $this->Api_model->getUserEwalletDetails($this->LOG_USER_ID, $from_date, $to_date, $type, $filter['start'], $filter['limit'], $filter['order'], $filter['direction']);
        $data = [];
        $data['count'] = $count;
        $data['table_data']=[];

        foreach($fund_transfer_details as $key => $item) {
            $total_amount = $item['total_amount'];
            $trans_fee = $item['trans_fee'];
            if ($this->IS_MOBILE) {
                $total_amount = format_currency($total_amount);
                $trans_fee = format_currency($trans_fee);
            }
            $data['table_data'][] = [
                'description' => $this->getDescriptionTransaction($item),
                'transaction_id' => $item['transaction_id'],
                'amount' => $this->IS_MOBILE ? format_currency($item['total_amount']) : format_currency_without_symbol($item['total_amount']),
                'transaction_fee' =>  ($item['amount_type'] == "user_debit") ? 
                                    (($this->IS_MOBILE) ? format_currency($item['trans_fee']) : format_currency_without_symbol($item['trans_fee'])) : 'NA',
                'transfer_type'  => $item['amount_type'] == "user_debit" ? lang('debit') : lang('credit'),
                'type'           => $item['amount_type'] == "user_debit" ? 'debit' : 'credit',
                'date' => date("F j, Y, g:i a",strtotime($item['date'])),
            ];
        }
        $this->set_success_response(200, $data);
    } 

    private function getDescriptionTransaction($item) {
        $this->lang->load('common', $this->LANG_NAME);
        if($item['amount_type'] == 'user_credit'){
            if($this->LOG_USER_NAME == $item['from_user_name']) {
                return lang('fund_transfer_to') .' '. $item['to_user_name'];
            } else if($this->LOG_USER_NAME == $item['to_user_name']) {
                return lang('transfer_from'). ' '. $item['from_user_name'];
            }

        } else {
            if($this->LOG_USER_NAME == $item['from_user_name']) {
                return lang('fund_transfer_to') .' '. $item['to_user_name'];
            } else if($this->LOG_USER_NAME == $item['to_user_name']) {
                return lang('transfer_to'). ' '. $item['from_user_name'];
            }
        }
        return "";

    }
    //end of ewallet history 




    // Purchase Wallet
    public function purchase_wallet_table_get()
    {
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

        $ewallet_details = $this->ewallet_model->getPurchasewalletHistory($this->LOG_USER_ID, $filter['start'], $filter['limit']);
        $previous_ewallet_balance = $this->ewallet_model->getPreviousPurchasewalletBalance($this->LOG_USER_ID, $filter['start']);
        $balance = $previous_ewallet_balance;
        $count = $this->ewallet_model->getPurchasewalletHistoryCount($this->LOG_USER_ID);
        $data = [];
        $data['count'] = $count;
        $data['table_data'] = [];
        foreach($ewallet_details as $key => $item) {
            if ($item['type'] == 'debit') {
                $balance = $balance - $item['amount'];
                // $debit = $debit + $item['amount'];
            }
            if ($item['type'] == 'credit') {
                $balance = $balance + $item['amount'];
                // $credit = $credit + $item['amount'];
            }

            $debit  = format_currency($item['amount']);
            $credit = format_currency($item['amount']);
            $data['table_data'][] = [
                'description' => $this->getPurchaseWalletDescription($item),
                'amount'      => $this->IS_MOBILE ? format_currency($item['amount']) : $item['amount'] ,
                'type'        => $item['type'],
                'debit'       => $debit,
                'credit'      => $credit,
                'date'        => date("F j, Y, g:i a",strtotime($item['date_added'])),
                'balance'     => $item['amount'] ? $balance : $balance,
            ];
        }
        $this->set_success_response(200, $data);
    } 
    public function getPurchaseWalletDescription($item) {
        $this->lang->load('common', $this->LANG_NAME);
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
        } elseif ($item['amount_type'] == 'board_commission' && $this->MODULE_STATUS['table_status'] == 'yes') {
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
    //end of Purchase Wallet 


    // user earning

    public function user_earnings_table_get() {
        $this->lang->load('common', $this->LANG_NAME);
        $order_columns = [
            // 1 => 'amount_payable',
            'transaction_date' => 'date_of_submission'
         ];

        $feild = $this->input->get(null, true);
        $order = $feild['order']??'transaction_date';
        // dd($order);
        $direction = $feild['direction']??'asc';
        $filter = [
            'limit' => $feild['length']??10,
            'start' => $feild['start']??0,
            'order' => $order_columns[$order]??null,
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];
        // dd($filter);
        $categories = $this->input->get('categories') ?: [];
        // dd($categories);
        $from_date = $this->input->get('start_date');
        $to_date = $this->input->get('end_date');

        $page = $this->input->get('offset');

        $count = $this->ewallet_model->getUserEearningsCount($this->LOG_USER_ID, $categories, $from_date, $to_date);
        $all_transaction = $this->ewallet_model->getUserEarnings($this->LOG_USER_ID, $categories, $from_date, $to_date, $filter);
        $data = [];
        $data['count'] = $count;
        $data['table_data'] = [];
        foreach ($this->ewallet_model->getEnabledBonusCategories() as  $cat) {
            $data['category'][] = [
                'key' => lang($cat),
                'value' => $cat
            ];
        }
        $total_amount = 0;
        $total_tax    = 0;
        $total_service_charge = 0;
        $total_amount_payable = 0;
        foreach($all_transaction as $key => $item) {
            $total_amount += $item['total_amount'];
            $total_tax += $item['tds'];
            $total_service_charge += $item['service_charge'];
            $total_amount_payable += $item['amount_payable'];
            // dd($item['category']);
            $data['table_data'][] = [
                'category'         => lang($item['category']),
                'total_amount'     => $this->IS_MOBILE ? format_currency($item['total_amount']): format_currency_without_symbol($item['total_amount']),
                'tax'              => $this->IS_MOBILE ? format_currency($item['tds']): format_currency_without_symbol($item['tds']),
                'service_charge'   => $this->IS_MOBILE ? format_currency($item['service_charge']): format_currency_without_symbol($item['service_charge']),
                'amount_payable'   => $this->IS_MOBILE ? format_currency($item['amount_payable']) : format_currency_without_symbol($item['amount_payable']),
                'transaction_date' => date("F j, Y, g:i a",strtotime($item['transaction_date']))
            ];
        }

        
        $this->set_success_response(200, $data);
    }

    // end of user earning 


    // end of react api
    

    public function password_put()
    {
        $this->lang->load('ewallet', $this->LANG_NAME);
        $this->lang->load('common', $this->LANG_NAME);
        $user_id = $this->rest->user_id;
        $post_arr = $this->validation_model->stripTagsPostArray($this->put());
        $username = $this->validation_model->IdToUserName($user_id);

        $this->form_validation->set_data($post_arr);
        if ($this->validatePasswordInfo()) {
            $this->load->model('tran_pass_model');
            $this->load->model('mail_model');
            $passcode = $this->tran_pass_model->getUserPasscode($user_id);
            $old_passcode = $post_arr['current_password'];
            if (!password_verify($old_passcode, $passcode)) {
                $this->set_error_response(401, 1015);
            }
            $new_passcode = $post_arr['new_password'];
            $res = $this->tran_pass_model->updatePasscode($user_id, $new_passcode, $passcode);
            if ($res) {
                $this->tran_pass_model->sentTransactionPasscode($user_id, $new_passcode, $username);
                $this->set_success_response(204);
            } else {
                $this->set_error_response(500);
            }
        } else {
            $this->set_error_response(422, 1004);
        }
    }

    public function validatePasswordInfo()
    {
        $this->form_validation->set_rules('current_password', lang('current_password'), 'trim|required|strip_tags|min_length[8]|alpha_numeric|callback__verifyCurrent');
        $this->form_validation->set_rules('new_password', lang('new_password'), 'trim|required|strip_tags|min_length[8]|alpha_numeric');
        $this->form_validation->set_rules('password_confirmation', lang('password_confirmation'), 'trim|required|strip_tags|matches[new_password]');
        return $this->form_validation->run();
    }
    public function _verifyCurrent($password)
    {
        $user_id = $this->rest->user_id;   
        $this->load->model('tran_pass_model');

        $passcode = $this->tran_pass_model->getUserPasscode($user_id);
        if (!password_verify($password, $passcode)) {
            $this->form_validation->set_message('_verifyCurrent', lang('current_password_not_correct'));

            return false;
        }
        return true;
    }

    public function password_forget_post()
    {
        $this->load->model('tran_pass_model');
        $this->load->model('mail_model');
        $user_id = $this->rest->user_id;
        $e_mail = $this->validation_model->getUserEmailId($user_id);
        $res = $this->tran_pass_model->sendEmail($user_id, $e_mail);
        if ($res) {
            $this->set_success_response(204);
        } else {
            $this->set_error_response(500);
        }
    }
    public function payment_post()
    {
        $this->MLM_PLAN = $this->MODULE_STATUS['mlm_plan'];
        $this->load->model("ewallet_model");
        $this->load->model("repurchase_model");
        $this->load->model("register_model");
        $user_id = $this->rest->user_id;
        $post_arr = $this->post();
        $this->form_validation->set_data($post_arr);

        if ($this->validatePurchasePayment()) {
            $res = $this->ewallet_model->ewalletPay($post_arr['payment_amount'], $post_arr['ewallet_username'], $post_arr['transaction_password']);
            if (!is_numeric($res)) {
                $used_user_id = $this->validation_model->userNameToID($post_arr['ewallet_username']);
                $transaction_id = $this->repurchase_model->getUniqueTransactionId();
                $this->register_model->begin();
                $res1 = $this->register_model->insertUsedEwallet($used_user_id, $user_id, $post_arr['payment_amount'], $transaction_id, false, $post_arr['purpose']);
                if ($res1) {
                    $res2 = $this->register_model->deductFromBalanceAmount($used_user_id, $post_arr['payment_amount']);
                    if ($res2) {
                        $this->register_model->commit();
                        $this->set_success_response(204);
                    } else {
                        $this->register_model->rollback();
                        $this->set_error_response(500);
                    }
                } else {
                    $this->register_model->rollback();
                    $this->set_error_response(500);
                }
            } else {

                $this->set_error_response(422, $res);
            }
        } else {
            $this->set_error_response(422, 1004);
        }
    }
    public function validatePurchasePayment()
    {
        $this->form_validation->set_rules('payment_amount', lang('payment_amount'), 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('purpose', lang('purpose'), 'trim|required');
        $this->form_validation->set_rules('ewallet_username', lang('ewallet_username'), 'trim|required');
        $this->form_validation->set_rules('transaction_password', lang('transaction_password'), 'trim|required');

        $validation_status = $this->form_validation->run();
        return $validation_status;
    }

    function ewallet_statement_get()
    {
        $this->load->model("ewallet_model");
        $get_arr = $this->get();
        $this->form_validation->set_data($get_arr);
        $rules = [
            [
                'field' => 'offset',
                'label' => lang("offset"),
                'rules' => 'trim|required|greater_than_equal_to[0]'
            ],
            [
                'field' => 'limit',
                'label' => lang("limit"),
                'rules' => 'trim|required|greater_than_equal_to[0]|less_than_equal_to[1000]'
            ],
        ];
        $this->form_validation->set_rules($rules);
        if (!$this->form_validation->run()) {
            $this->set_error_response(422, 1004);
        }

        $offset = $this->get("offset");
        $limit = $this->get("limit");

        $data = $this->ewallet_model->getEwalletHistory($this->LOG_USER_ID, $offset, $limit);
        $previousEwalletBalance = $this->ewallet_model->getPreviousEwalletBalance($this->LOG_USER_ID, $offset);
        $ewalletBalance = $this->ewallet_model->getBalanceAmount($this->LOG_USER_ID);

        $previousEwalletBalance = format_currency($previousEwalletBalance);
        $ewalletBalance = format_currency($ewalletBalance);

        if (!$this->NATIVE_APPS)
            $this->set_success_response(200, $data);
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
        $balance = 0;
        $debit = 0;
        $credit = 0;
        $details = [];
        foreach ($data as $k => $v) {
            $description = "";
            $amount = "";
            $amount = format_currency($v['amount']);
            $amountColour = "#808080";
            if ($v['type'] == 'debit' && $v['amount_type'] != 'payout_release') {
                $balance = $balance - $v['amount'] - $v['transaction_fee'];
                $debit = $debit + $v['amount'];
                $amount = "-" . format_currency($v['amount']);
                $amountColour = "#ff8080";
            }
            if ($v['type'] == 'credit') {
                $balance = $balance + $v['amount'] - $v['purchase_wallet'];
                $credit = $credit + $v['amount'] - $v['purchase_wallet'];
                $amount = "+" . format_currency($v['amount']);
                $amountColour = "#80ff80";
            }
            if ($v['ewallet_type'] == "fund_transfer") {
                if ($v['amount_type'] == "user_credit") {
                    $description = lang('transfer_from') . " " . $v['from_user'];
                } elseif ($v['amount_type'] == "user_debit") {
                    $description = lang('fund_transfer_to') . " " . $v['from_user'];
                } elseif ($v['amount_type'] == "admin_credit") {
                    $description = lang('credited_by') . " " . $v['from_user'];
                } elseif ($v['amount_type'] == "admin_debit") {
                    $description = lang('deducted_by') . " " . $v['from_user'];
                }
            } elseif ($v['ewallet_type'] == "commission") {
                if ($v['amount_type'] == "donation") {
                    if ($v['type'] == "debit") {
                        $description = lang('donation_debit') . " " . $v['from_user'];
                    } else {
                        $description = lang('donation_credit') . " " . $v['from_user'];
                    }
                } elseif ($v['amount_type'] == 'board_commission' && $this->MODULE_STATUS['table_status'] == 'yes') {
                    $description = lang('table_commission');
                } else {
                    if (in_array($v['amount_type'], $from_user_amount_types))
                        $description = lang($v['amount_type']) . " " . lang('from') . " " . $v['from_user'];
                    else
                        $description = lang($v['amount_type']);
                }
            } elseif ($v['ewallet_type'] == "ewallet_payment") {
                if ($v['amount_type'] == "registration") {
                    $description = lang('deducted_for_registration_of') . " " . $v['from_user'];
                } elseif ($v['amount_type'] == "repurchase") {
                    $description = lang('deducted_for_repurchase_by') . " " . $v['from_user'];
                } elseif ($v['amount_type'] == "package_validity") {
                    $description = lang('deducted_for_membership_renewal_of') . " " . $v['from_user'];
                } elseif ($v['amount_type'] == "upgrade") {
                    $description = lang('deducted_for_upgrade_of') . " " . $v['from_user'];
                }
            } elseif ($v['ewallet_type'] == "payout") {

                if ($v['amount_type'] == "payout_request") {
                    $description = lang('deducted_for_payout_request');
                } elseif ($v['amount_type'] == "payout_release") {
                    $description = lang('payout_released_for_request');
                } elseif ($v['amount_type'] == "payout_delete") {
                    $description = lang('credited_for_payout_request_delete');
                } elseif ($v['amount_type'] == "payout_release_manual") {
                    $description = lang('payout_released_by_manual');
                } elseif ($v['amount_type'] == "withdrawal_cancel") {
                    $description = lang('credited_for_waiting_withdrawal_cancel');
                }
            } elseif ($v['ewallet_type'] == "pin_purchase") {
                if ($v['amount_type'] == "pin_purchase") {
                    $description = lang('deducted_for_pin_purchase');
                } elseif ($v['amount_type'] == "pin_purchase_refund") {
                    $description = lang('credited_for_pin_purchase_refund');
                } elseif ($v['amount_type'] == "pin_purchase_delete") {
                    $description = lang('credited_for_pin_purchase_delete');
                }
            } elseif ($v['ewallet_type'] == "package_purchase") {
                if ($v['amount_type'] == "purchase_donation") {
                    $description = lang('purchase_donation') . " " . lang('from') . " " . $v['from_user'];
                }
            }

            if ($v['pending_id']) {
                $description .= " (" . lang('') . ")";
            }

            $dateTime = date($this->TIME_FORMAT, strtotime($v['date_added']));

            $row = compact("amount", "amountColour", "description", "dateTime");
            if (in_array($v['ewallet_type'], array('fund_transfer', 'payout')) && ($v['transaction_fee'] > 0) && ($v['type'] == 'debit')) {
                $row["transFee"] = "-" . format_currency($v['transaction_fee']);
                $row["transFeeName"] = lang("payout_fee");
                if ($v['ewallet_type'] == "fund_transfer")
                    $row["transFeeName"] = lang("fund_transfer_fee");
            }
            $details[] = $row;
        }

        $summaryArray = [];
        $summaryArray[] = [
            "title" => lang("ewallet_balance"),
            "title_colour" => "#000000",
            "value" => format_currency($balance),
            "value_colour" => "#000000",
        ];
        $this->set_success_response(200, compact("details", "summaryArray"));
    }

    //fund transfer api
    function fund_transfer_post()
    {
        if ($this->post()) {
            // $validated = $this->fund_transfer_validation();
            if ($this->fund_transfer_validation()) {
                $transfer_post_array = $this->post(null, true);
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
                            $data = [
                                "status" => true
                            ];
                            $this->set_success_response(200,$data);
                        } else {
                            $this->ewallet_model->rollback();
                            $data = [
                                "status" => false,
                                "message" => 'error_on_fund_transfer'
                            ];
                            $this->set_error_response(422,1030);
                        }
                    } else {
                            $this->set_error_response(422,1015);
                    }
                } else {
                        $this->set_error_response(422,1014);
                }
            } else {
                $this->set_error_response(422,1004);
                // echo json_encode($validated);
                exit();
            }
        }
    }

    protected function fund_transfer_validation()
    {
        $this->lang->load('validation',$this->LANG_NAME);
        
        $this->form_validation->set_rules('to_user_name', 'lang:user_name', 'trim|required|max_length[50]|user_exists|differs[user_name]', [
                "required"    => lang('required'),
                "max_length"  => sprintf(lang("maxlength"), lang('user_name'), "50"),
                "user_exists" => lang('username_not_available'),
                'differs'     => lang('username_not_to_be_same_as_to_username'),
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
        
        // $this->form_validation->set_rules('transaction_note', 'lang:transaction_note', 'trim|required|max_length[1000]',[
        //         "required" => lang('required'),
        //         "max_length" => sprintf(lang("maxlength"), lang("transaction_note"), "1000")
        //     ]
        // );

        $this->form_validation->set_rules('pswd', lang('transaction_password'), 'trim|required|max_length[100]|callback_check_transaction_password', [
                'required' => lang('required'),
                "max_length"  => sprintf(lang("maxlength"), lang('transaction_password'), "100"),
                'check_transaction_password' => lang('invalid_transaction_password')
            ]
        );

        return $this->form_validation->run();
        // if (!$status) {
        //     return [
        //         'status' => false,
        //         'validation_error' => $this->form_validation->error_array(),
        //         'message' => lang('errors_check')
        //     ];
        // }
        // return [
        //     'status' => true
        // ];
    }
    function check_transaction_password($password) {
        $from_user_id = $this->LOG_USER_ID;
        $pass = $this->ewallet_model->getUserPassword($from_user_id);
        $msg = lang('invalid_transaction_password');
        if(!password_verify($password, $pass)) {
            // $this->set_error_response(422,1015);
            // $MSG_ARR["MESSAGE"]["DETAIL"] = $msg;
            // $MSG_ARR["MESSAGE"]["TYPE"] = false;
            // $MSG_ARR["MESSAGE"]["STATUS"] = false;
            // $this->session->set_flashdata('MSG_ARR', $MSG_ARR);
            return false;
        }
        return true;
    }
    
    public function balance_check($amount1) {
        $balance_amount = $this->ewallet_model->getBalanceAmount($this->LOG_USER_ID);
        if(($amount1+$this->input->post('tran_fee')) <= $balance_amount) {
            return true;            
        }
        return false;
    }

    public function earnings_export_data_get() {
        $this->lang->load('common', $this->LANG_NAME);
        $categories = $this->input->get('categories') ?? [];
        $from_date = $this->input->get('start_date');
        $to_date = $this->input->get('end_date');
        $this->load->model('excel_model');
        $date = date("Y-m-d H:i:s");
        $excel_array = $this->excel_model->getUserEearnings($this->LOG_USER_ID, $categories ,$from_date, $to_date);
        $data = [];
        $total_amount = 0;
        $total_tax    = 0;
        $total_service_charge = 0;
        $total_amount_payable = 0;
        // dd($excel_array); 
        foreach($excel_array as $key => $item) {
            $total_amount += $item['total_amount'];
            $total_tax += $item['tds'];
            $total_service_charge += $item['service_charge'];
            $total_amount_payable += $item['amount_payable'];
            // dd(lang($item['category']));
            $data[$key] = [
                'category' => lang($item['category']),
                'total_amount' => format_currency($item['total_amount']),
                'tax' => format_currency($item['tds']),
                'service_charge' => format_currency($item['service_charge']),
                'amount_payable' => format_currency($item['amount_payable']),
                'transaction_date' => date("d M Y - h:i:s A", strtotime($item['transaction_date']))
            ];
        }
        $data[] = [
            'category' => lang('Total'),
            'total_amount' => format_currency($total_amount),
            'tax'              => format_currency($total_tax),
            'service_charge'   => format_currency($total_service_charge),
            'amount_payable'   => format_currency($total_amount_payable),
            'transaction_date' => ''
        ];

        $this->set_success_response(200, $data);
    }
}

