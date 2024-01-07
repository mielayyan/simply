<?php

require_once 'Inf_Controller.php';

class Payout extends Inf_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('payout_model');
        $this->LOG_USER_ID = $this->rest->user_id;
        $this->LOG_USER_TYPE = 'user';
        $this->LOG_USER_NAME = $this->validation_model->IdToUserName($this->LOG_USER_ID);
        if ($this->MODULE_STATUS['mlm_plan'] == 'Hyip' || $this->MODULE_STATUS['mlm_plan'] == 'X-Up') {
        $this->load->model('Unilevel_model', 'plan_model');
        }
        else {
            $this->load->model($this->MODULE_STATUS['mlm_plan'] . '_model', 'plan_model');
        }

        $this->LOG_USER_ID = $this->rest->user_id;
        $this->LOG_USER_TYPE = 'user';
        $this->LOG_USER_NAME = $this->validation_model->IdToUserName($this->LOG_USER_ID);
    }

    public function payout_tiles_get() {
        $pending_amount = $this->payout_model->getTotalAmountPendingRequest('pending', $this->LOG_USER_ID) ?? 0;
        $data['payout_tile'][] = [
                'text' => 'pending',
                'amount' => $pending_amount,
                'amountWithCurrency'    => thousands_currency_format($pending_amount),
                'icon'=>SITE_URL . "/public_html/images/newui/pending.png",
                'bg_color'=>"#ffe690"

        ];
        $approved_amount = $this->payout_model->getTotalAmountApproved($this->LOG_USER_ID) ?? 0;
        $data['payout_tile'][] = [
                'amount' => $approved_amount,
                'text' => 'approved',
                'amountWithCurrency'    => thousands_currency_format($approved_amount),
                'icon'=>SITE_URL . "/public_html//images/newui/Approved.png",
                'bg_color'=>"#44badc"

        ];
        $paid_amount = $this->payout_model->getTotalAmountPaid($this->LOG_USER_ID) ?? 0;
        $data['payout_tile'][] = [
                'amount' => $paid_amount,
                'text' => 'paid',
                'amountWithCurrency'    => thousands_currency_format($paid_amount),
                'icon'=>SITE_URL . "/public_html//images/newui/paid.png",
                'bg_color'=>"#5bc554"

        ];
        $rejected_amount = $this->payout_model->getTotalAmountRejected('deleted',$this->LOG_USER_ID) ?? 0;
        $data['payout_tile'][] = [
                'amount' => $rejected_amount,
                'text' => 'rejected',
                'amountWithCurrency'    => thousands_currency_format($rejected_amount),
                'icon'=>SITE_URL . "/public_html//images/newui/Rejected.png",
                'bg_color'=>"#e92222cc"

        ];
        $config_details = $this->configuration_model->getSettings();
        $data['button_show'] =true;
        if($config_details['payout_release'] == 'from_ewallet'){
            $data['button_show'] =false;
        }

        $this->set_success_response(200, $data);
    }

    public function approved_pending_list_get() {
        $columns = [
            'approved_date' => 'paid_date',
            1 => 'paid_amount',
            2 => 'payment_method',
        ];
        $feild = $this->input->get(null, true);
        $order = $feild['order']??'';
        $direction = $feild['direction']??'asc';
        $filter = [
            'limit' => $feild['length']??10,
            'start' => $feild['start']??0,
            'order' => $order_columns[$order]??null,
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];

        // $order = $this->input->get('order', true)[0]['column'] ?? 0;
        // $filter = [
        //   'limit' => $this->input->get('length'),
        //   'start' => intval($this->input->get("start")),
        //   // 'order' => null != $this->input->get('order') && isset($this->input->get('order')[0]['column']) && isset($columns[$this->input->get('order')[0]['column']]) 
        //   //       ? $columns[$this->input->get('order')[0]['column']] 
        //   //       : '',
        //   'order' => $columns[$order] ?? $columns[0],
        //   'direction' => null != $this->input->get('order') && isset($this->input->get('order')[0]['dir'])  ? $this->input->get('order')[0]['dir'] : 'ASC'
        // ];
        
        
        $count = $this->payout_model->getReleasedWithdrawalCountNew($this->LOG_USER_ID, 'approved_pending');
        $waiting_requests = $this->payout_model->getReleasedWithdrawalDetailsNew($this->LOG_USER_ID, 'approved_pending', $filter);
        
        $data = [];
        $data['count'] = $count;
        $data['table_data'] = [];
        foreach($waiting_requests as $key => $item) {
            if($this->IS_MOBILE)
            {
                $paid_amount = format_currency($item['paid_amount']); 
            }
            else
            {
                $paid_amount = $item['paid_amount'];
            }
            $data['table_data'][] = [
                'amount'     => $paid_amount,
                'payout_method' => lang($item['payment_method']),
                "approved_date" => date("F j, Y, g:i a",strtotime($item['paid_date']))
            ];
        }
        
        
        $this->set_success_response(200, $data);
    }

    public function pending_list_get() {
        $columns = [
            'requested_amount' => 'requested_amount_balance',
            1 => '',
            'requested_date' => 'requested_date',
            3 => '',
        ];
        
        $feild = $this->input->get(null, true);
        $order = $feild['order']??'';
        $direction = $feild['direction']??'asc';
        $filter = [
            'limit' => $feild['length']??10,
            'start' => $feild['start']??0,
            'order' => $order_columns[$order]??null,
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];
        
        
        $count = $this->payout_model->getPayoutWithdrawalCountNew($this->LOG_USER_ID, 'pending');
        $pending_requests = $this->payout_model->getPayoutWithdrawalDetailsNew($this->LOG_USER_ID, 'pending', $filter);
        $data = [];
        $data['count'] = $count;
        $data['table_data'] = [];
        foreach($pending_requests as $key => $item) {
                $payout_amount = $item['payout_amount'];
                $balanceAmount = $item['balance_amount'];
            if($this->IS_MOBILE)
            {
                $payout_amount = format_currency($item['payout_amount']);
                $balanceAmount = format_currency($item['balance_amount']);
            }
            $data['table_data'][] = [
                'request_id' =>$item['req_id'],
                'payout_amount' => $payout_amount,
                'ewallet_balance' => $balanceAmount,
                "requested_date" => date("F j, Y, g:i a",strtotime($item['requested_date']))
            ];
        }
        
        $this->set_success_response(200, $data);
        
    }

    public function approved_paid_list_get() {
        $columns = [
            'paid_date' => 'paid_date',
            'paid_amount' => 'paid_amount',
            2 => 'payment_method',
        ];
        
        $feild = $this->input->get(null, true);
        $order = $feild['order']??'';
        $direction = $feild['direction']??'asc';
        $filter = [
            'limit' => $feild['length']??10,
            'start' => $feild['start']??0,
            'order' => $order_columns[$order]??null,
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];
        
        
        $count = $this->payout_model->getReleasedWithdrawalCountNew($this->LOG_USER_ID, 'approved_paid');
        $paid_requests = $this->payout_model->getReleasedWithdrawalDetailsNew($this->LOG_USER_ID, 'approved_paid', $filter);
        
        $data = [];
        $data['table_data'] = [];
        $data['count'] = $count;

        foreach($paid_requests as $key => $item) {
            if($this->IS_MOBILE)
            {
                $paid_amount = format_currency($item['paid_amount']); 
            }
            else
            {
                $paid_amount = $item['paid_amount'];
            }
            $data['table_data'][] = [
                'amount'     => $paid_amount,
                'paid_date'  => date("F j, Y, g:i a",strtotime($item['paid_date'])),
                'payout_method' => lang($item['payment_method'])
            ];
        }
        
        
        $this->set_success_response(200, $data);
    }

    public function rejected_list_get() {
        $columns = [
            'balance_amount' => 'balance_amount',
            1 => '',
            2 => 'user_name',
            3 => 'payout_type',
        ];
        
        $feild = $this->input->get(null, true);
        $order = $feild['order']??'';
        $direction = $feild['direction']??'asc';
        $filter = [
            'limit' => $feild['length']??10,
            'start' => $feild['start']??0,
            'order' => $order_columns[$order]??null,
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];
        
        
        $count = $this->payout_model->getPayoutWithdrawalCountNew($this->LOG_USER_ID, 'deleted');
        $rejected_requests = $this->payout_model->getPayoutWithdrawalDetailsNew($this->LOG_USER_ID, 'deleted', $filter);
    
        $data = [];
        $data['table_data'] = [];
        $data['count'] = $count;
        
        foreach($rejected_requests as $key => $item) {
            if($this->IS_MOBILE)
            {
                $payout_amount = format_currency($item['payout_amount']); 
            }
            else
            {
                $payout_amount = $item['payout_amount'];
            }
            $data['table_data'][] = [
                'amount'     => $payout_amount,
                'requested_date' => date("F j, Y, g:i a",strtotime($item['requested_date'])),
                'rejected_date' => date("F j, Y, g:i a",strtotime($item['updated_date']))
            ];
        }
         
        $this->set_success_response(200, $data);
    }

    
    //api for payout request form and payout request submition
    public function payout_request_post()
    {
        
        $this->load->model('ewallet_model');
        $user_id = $this->rest->user_id;
        $this->lang->load('payout',$this->LANG_NAME);
        $post_arr = $this->post(null, true);
        //if submited payout request form
        if((isset($post_arr['withdraw']))) {
            $this->form_validation->set_data($this->post());
            $validated = $this->validate_payout_request();
            if ($validated['status']) {
                $kyc_status = $this->MODULE_STATUS['kyc_status'];
                if ($kyc_status == "no"  || $kyc_status == 'yes') {
                    $this->lang->load('common',$this->LANG_NAME);
                    $kyc_upload = $this->validation_model->checkKycUpload($user_id);
                    if ($kyc_status == 'yes' AND $kyc_upload != 'yes') {
                        $this->set_error_response(422, 1019);
                    } else {
                        $minimum_payout_amount  = $this->payout_model->getMinimumPayoutAmount();
                        $maximum_payout_amount  = $this->payout_model->getMaximumPayoutAmount();
                        $balance_amount         = $this->payout_model->getUserBalanceAmount($user_id);
                        $req_amount             = $this->payout_model->getRequestPendingAmount($user_id);
                        $total_amount           = $this->payout_model->getReleasedPayoutTotal($user_id);
                        $transation_password    = (strip_tags($this->post('transaction_password', TRUE)));
                        $password_flag          = $this->payout_model->checkTransactionPassword($user_id, $transation_password);
                        if ($password_flag) {
                            $payout_amount = round(($this->post('payout_amount', TRUE)) / $this->DEFAULT_CURRENCY_VALUE, 8);
                            $config_details = $this->configuration_model->getSettings();
                            $deductAmount = $payout_amount + $config_details['payout_fee_amount'];
                            if($config_details['payout_fee_mode'] == 'percentage') {
                                $deductAmount = $payout_amount + ($payout_amount * $config_details['payout_fee_amount'] / 100);
                            }

                            $request_date = date('Y-m-d H:i:s');
                            $balance_amount = $this->payout_model->getUserBalanceAmount($user_id);
                            if ($balance_amount >= $deductAmount && $payout_amount >= $minimum_payout_amount && $payout_amount <= $maximum_payout_amount) 
                            {
                                $res = $this->payout_model->insertPayoutReleaseRequest($user_id, $payout_amount, $request_date, 'pending');
                                if ($res) {
                                    $this->payout_model->updateUserBalanceAmount($user_id, $deductAmount);
                                    $data_array = array();
                                    $data_array['tran_pass'] = $transation_password;
                                    $data_array['payout_amount'] = $payout_amount;
                                    $data_array['balance_amount'] = $balance_amount;
                                    $data = serialize($data_array);
                                    $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Payout request sent ', $this->LOG_USER_ID, $data);

                                    $mail_arr['payout_amount'] = $payout_amount;
                                    $mail_arr['username'] = $this->LOG_USER_NAME;
                                    $mail_arr['email'] = $this->validation_model->getUserEmailId($this->LOG_USER_ID);
                                    $mail_arr['first_name'] = '';
                                    $mail_arr['last_name'] = '';
                                    $this->mail_model->sendAllEmails('payout_request', $mail_arr);
                                    if($this->MODULE_STATUS["sms_status"] == "yes") {
                                        $this->load->model("sms_model");
                                        $mobile = $this->validation_model->getUserPhoneNumber($this->LOG_USER_ID);
                                        $variableArray = [
                                            "fullname" => $this->validation_model->getUserFullName($this->LOG_USER_ID),
                                            "company_name" => $this->COMPANY_NAME,
                                            "admin_user_name" => $this->ADMIN_USER_NAME,
                                            "username" => $this->LOG_USER_NAME,
                                            "payout_amount" => format_currency($payout_amount)
                                        ];
                                        $langId = $this->validation_model->getUserDefaultLanguage($this->LOG_USER_ID);
                                        $type = "payout_request";

                                        $this->sms_model->createAndSendSMS($langId, $type, $mobile, $variableArray);
                                    }
                                    $message = lang('payout_request_sent_successfully');
                                    $this->set_success_response(200,$message);
                                } else {
                                    $this->set_error_response(422, 1014);
                                }
                            } else if ($deductAmount > $balance_amount) {
                                $default_currency_left_symbol = ($this->DEFAULT_SYMBOL_LEFT != '') ? $this->DEFAULT_SYMBOL_LEFT : "$";
                                $msg = $this->lang->line('maximum_amount') . $default_currency_left_symbol . round($maximum_payout_amount);
                                $this->set_error_response_withMsg(422, 1028, true, $msg);
                            } else if ($payout_amount <= $minimum_payout_amount) {
                                $default_currency_left_symbol = ($this->DEFAULT_SYMBOL_LEFT != '') ? $this->DEFAULT_SYMBOL_LEFT : "$";
                                $minimum_payout_amount = round(($minimum_payout_amount) * $this->DEFAULT_CURRENCY_VALUE, 8);
                                $msg = $this->lang->line('minimum_amount') . $default_currency_left_symbol . round($minimum_payout_amount);
                                $this->set_error_response_withMsg(422, 1027,true, $msg);
                            } else {
                                // dd("payout_amount=".$payout_amount." "."balance_amount".$balance_amount);
                                $default_currency_left_symbol = ($this->DEFAULT_SYMBOL_LEFT != '') ? $this->DEFAULT_SYMBOL_LEFT : "$";
                                $maximum_payout_amount = round(($maximum_payout_amount) * $this->DEFAULT_CURRENCY_VALUE, 8);
                                $msg = $this->lang->line('maximum_amount') . $default_currency_left_symbol . round($maximum_payout_amount);
                                //  $data = [
                                //     'status'     => false,
                                //     'error_type' => 'validation_error',
                                //     'validation_error' =>[
                                //         'payout_amount'=>$msg
                                //     ],
                                // ];
                                 $this->set_error_response(422, 1028);
                            }
                        } else {
                            $this->set_error_response(401, 1015);
                        }
                            }
                }
                $this->set_error_response(422, 1019);
            } else {
                $this->set_error_response(422, 1004);
            }

        }
        // end of payout request submition 

        //  payout request details
        else
        {
            $minimum_payout_amount = $this->payout_model->getMinimumPayoutAmount();
            $maximum_payout_amount = $this->payout_model->getMaximumPayoutAmount();
            $balance_amount = $this->payout_model->getUserBalanceAmount($user_id);
            $req_amount = $this->payout_model->getRequestPendingAmount($user_id);
            $total_amount = $this->ewallet_model->getTotalReleasedAmount($user_id);
            $config_details = $this->configuration_model->getSettings();
            $payout_method = $this->payout_model->getUserPayoutType($user_id);
            $possible_payout_amount = $balance_amount - $config_details["payout_fee_amount"];
            if($possible_payout_amount <= 0) {
                $possible_payout_amount = 0;
            }                                                                                //
            if($config_details["payout_fee_mode"] == 'percentage') {
                $possible_payout_amount = $balance_amount * 100 / (100 + $config_details["payout_fee_amount"]);
            }
            $payout_fee_deduct = $balance_amount - $possible_payout_amount;

            if ($possible_payout_amount <= $maximum_payout_amount) {
                $available_max_payout = $possible_payout_amount;
            } else {
                $available_max_payout = $maximum_payout_amount;
            }
            // payout fee 
            if($config_details['payout_fee_mode'] == 'percentage')
            {
                $payout_fee_amount = $config_details['payout_fee_amount'].'%'. lang('of').lang('withdraw_amount') ;
            }
            else
            {
                $payout_fee_amount = $config_details['payout_fee_amount'];
            }
            $available_max_payout_amount =$available_max_payout;
            if($this->IS_MOBILE) {
                $balance_amount = format_currency($balance_amount);
                $payout_fee_amount = format_currency($payout_fee_amount);
                $payout_fee_amount = format_currency($balance_amount);
                $req_amount = format_currency($req_amount);
                $total_amount = format_currency($total_amount);
                $minimum_payout_amount = format_currency($minimum_payout_amount);
                $maximum_payout_amount = format_currency($maximum_payout_amount);
                $available_max_payout_amount = format_currency($available_max_payout);
                $payout_fee_amount = format_currency($payout_fee_amount);

            }
                    // 
                    $data['amount'] = [
                        'balance' =>$balance_amount,
                        'payout_fee' => $payout_fee_amount,
                        'fee'        => $config_details['payout_fee_amount'],
                        'type'       => $config_details['payout_fee_mode'],
                        'available_max_payout' => convert_currency($available_max_payout)
                    ];
                    // particulars ewallet
                    $data['particulars'][] = [
                        'key' => lang('ewallet_balance'),
                        'amount' =>$balance_amount,
                    ];
                    // particulars ewallet_amount_already_in_payout_process
                    $data['particulars'][] = [
                        'key' => lang('ewallet_amount_already_in_payout_process'),
                        'amount' =>$req_amount,
                    ];
                    // particulars total_paid_amount
                    $data['particulars'][] = [
                        'key' => lang('total_paid_amount'),
                        'amount' =>$total_amount,
                    ];
                    // particulars preffered_payout_method
                    if($payout_method = 'bank')
                    {
                        $payout_method = 'Bank';
                    }
                    elseif($payout_method == "Bitcoin")
                    {
                        $payout_method = 'Blocktrail';            
                    }
                    $data['particulars'][] = [
                        'key' => lang('preffered_payout_method'),
                        'amount' =>$payout_method,
                    ];
                    // particulars minimum_withdrawal_amount
                    $data['particulars'][] = [
                        'key' => lang('minimum_withdrawal_amount'),
                        'amount' =>$minimum_payout_amount,
                    ];
                    // particulars maximum_withdrawal_amount
                    $data['particulars'][] = [
                        'key' => lang('maximum_withdrawal_amount'),
                        'amount' =>$maximum_payout_amount,
                    ];
                    // particulars available_maximum_withdrawal_amount
                    $data['particulars'][] = [
                        'key' => lang('available_maximum_withdrawal_amount'),
                        'amount' =>$available_max_payout_amount,
                    ];
                    // particulars payout_request_validity
                    $data['particulars'][] = [
                        'key' => lang('payout_request_validity').lang('days'),
                        'amount' =>$config_details['payout_request_validity']
                    ];
                    // particulars payout_fee
                    
                    $data['particulars'][] = [
                        'key' => lang('payout_fee'),
                        'amount' =>$payout_fee_amount
                    ];
        }
        // end of payout request details
        $this->set_success_response(200, $data);

    }
    // end of payout request 

    public function validate_payout_request()
    {    
        $this->lang->load('validation', $this->LANG_NAME);
        $this->form_validation->set_rules('payout_amount', 'lang:amount', 'trim|required|greater_than[0]',[
            "required"=>lang('required'),
            "greater_than"=>lang('field_greater_than_zero'),
        ]);
        $this->form_validation->set_rules('transaction_password', 'lang:transaction_password', 'trim|required|min_length[6]|max_length[100]|callback_check_transaction_password',[
            "required"=>lang('required'),
            "min_length"=>sprintf(lang('minlength'),lang('transaction_password'),"6"),
            "max_length"=>sprintf(lang('max_length'),lang('transaction_password'),"100")
        ]);
        $status = $this->form_validation->run();
        if (!$status) {
            return [
                'status' => false,
                'error_type' => 'validation_error',
                'validation_error' => $this->form_validation->error_array(),
            ];
        }
        return [
            'status' => true
        ];
    }
    //payout refound 
    public function payout_request_cancelation_post()
    {
        $user_id = $this->rest->user_id;
        $this->lang->load('payout',$this->LANG_NAME);
        $post_arr = $this->input->post(null, true);
         if(!empty($post_arr['payouts'])) {
            foreach($this->input->post('payouts') as $request) {

                $user_id = $this->payout_model->getPayoutRequestUserId($request);
                $user_name = $this->validation_model->IdToUserName($user_id);
                if (!$user_id) {
                    $data = [
                        'status' => false,
                        'error_type' => 'unknown',
                        'message' =>  lang('invalid_user_name')
                    ];
                }
                $res = $this->payout_model->deletePayoutRequest($request, $user_id,'cancelled');
                if ($res) {
                    $this->validation_model->insertUserActivity($this->LOG_USER_ID, lang('payout_request_deleted'), $this->LOG_USER_ID);
                    if ($this->LOG_USER_TYPE == 'employee') {
                        $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $user_id, 'delete_payout_request', 'Payout Request Deleted');
                    }
                } else {
                   $data = [
                        'status' => false,
                        'error_type' => 'unknown',
                        'message' =>  lang('Error_on_deleting_Payout_Request')
                    ];
                }
            }
            $data = [
                'status' => true,
                'message' => lang('withdrawal_canceled_successfully')
            ];
        } else {
             $data = [
                'status' => false,
                'error_type' => 'unknown',
                'message' =>  lang('please_select_payout')
            ];
        }
        $this->set_success_response(200, $data);
    }
    //end  of payout refound 

    public function payout_release_request_post() {
        $kyc_status = $this->MODULE_STATUS['kyc_status'];
        
        if (($this->MODULE_STATUS['kyc_status'] == 'yes') AND ($this->validation_model->checkKycUpload($this->LOG_USER_ID) != 'yes')) {
                return $this->set_error_response(422, 1019, [
                    'status'     => false,
                    'error_type' => 'unknown',
                    'message'    => lang('kyc_not_uploaded')
                ]
            );
        }


        $validated = $this->validate_payout_request_ajax();
        if(!$validated['status']) {
            return $this->set_error_response(422, 1004, $validated);
        }

        $user_id = $this->LOG_USER_ID;
        $minimum_payout_amount = $this->payout_model->getMinimumPayoutAmount();
        $maximum_payout_amount = $this->payout_model->getMaximumPayoutAmount();
        $balance_amount = $this->payout_model->getUserBalanceAmount($user_id);
        $req_amount = $this->payout_model->getRequestPendingAmount($user_id);
        $total_amount = $this->payout_model->getReleasedPayoutTotal($user_id);
        $min_payout = $this->configuration_model->getMinPayout();
        $transation_password = (strip_tags($this->input->post('transaction_password', TRUE)));
            
        $payout_amount = round(($this->input->post('payout_amount', TRUE)) / $this->DEFAULT_CURRENCY_VALUE, 8);
        $config_details = $this->configuration_model->getSettings();
        $deductAmount = $payout_amount + $config_details['payout_fee_amount'];
        if($config_details['payout_fee_mode'] == 'percentage') {
            $deductAmount = $payout_amount + ($payout_amount * $config_details['payout_fee_amount'] / 100);
        }

        $request_date = date('Y-m-d H:i:s');
        $balance_amount = $this->payout_model->getUserBalanceAmount($user_id);
        if ($balance_amount >= $deductAmount && $payout_amount >= $minimum_payout_amount && $payout_amount <= $maximum_payout_amount) {
            $res = $this->payout_model->insertPayoutReleaseRequest($user_id, $payout_amount, $request_date, 'pending');
            if(!$res) {
                return $this->set_error_response(400, 1031);
            }

            $this->payout_model->updateUserBalanceAmount($user_id, $deductAmount);
           
            $data_array = array();
            $data_array['tran_pass'] = $transation_password;
            $data_array['payout_amount'] = $payout_amount;
            $data_array['balance_amount'] = $balance_amount;
            $data = serialize($data_array);
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Payout request sent ', $this->LOG_USER_ID, $data);

            $mail_arr['payout_amount'] = $payout_amount;
            $mail_arr['username'] = $this->LOG_USER_NAME;
            $mail_arr['email'] = $this->validation_model->getUserEmailId($this->ADMIN_USER_ID);
            $mail_arr['first_name'] = '';
            $mail_arr['last_name'] = '';
            $this->mail_model->sendAllEmails('payout_request', $mail_arr);
            
            if($this->MODULE_STATUS["sms_status"] == "yes") {
                $this->load->model("sms_model");
                $mobile = $this->validation_model->getUserPhoneNumber($this->ADMIN_USER_ID);
                $variableArray = [
                    "fullname" => $this->validation_model->getUserFullName($this->ADMIN_USER_ID),
                    "company_name" => $this->COMPANY_NAME,
                    "admin_user_name" => $this->ADMIN_USER_NAME,
                    "username" => $this->LOG_USER_NAME,
                    "payout_amount" => format_currency($payout_amount)
                ];
                $langId = $this->validation_model->getUserDefaultLanguage($this->ADMIN_USER_ID);
                $type = "payout_request";
                $this->sms_model->createAndSendSMS($langId, $type, $mobile, $variableArray);
            }
            
            return $this->set_success_response(200, [
                'status'  => 'success',
                'message' => lang('payout_request_sent_successfully')
            ]);

        } else if ($deductAmount > $balance_amount) {
            return $this->set_error_response(400, 1014);
        } else if ($payout_amount <= $minimum_payout_amount) {
            $default_currency_left_symbol = ($this->DEFAULT_SYMBOL_LEFT != '') ? $this->DEFAULT_SYMBOL_LEFT : "$";
            $minimum_payout_amount = round(($minimum_payout_amount) * $this->DEFAULT_CURRENCY_VALUE, 8);
            $msg = $this->lang->line('minimum_amount') . $default_currency_left_symbol . round($minimum_payout_amount);
            return $this->set_error_response(400, 1027);
        } else {
            $default_currency_left_symbol = ($this->DEFAULT_SYMBOL_LEFT != '') ? $this->DEFAULT_SYMBOL_LEFT : "$";
            $maximum_payout_amount = round(($maximum_payout_amount) * $this->DEFAULT_CURRENCY_VALUE, 8);
            $msg = $this->lang->line('maximum_amount') . $default_currency_left_symbol . round($maximum_payout_amount);
            return $this->set_error_response(400, 1028);
        }
    }


    private function validate_payout_request_ajax()
    {    
        $this->lang->load('validation', $this->LANG_NAME);
        $this->form_validation->set_rules('payout_amount', 'lang:amount', 'trim|required|greater_than[0]',[
            "required"=>lang('required'),
            "greater_than"=>lang('field_greater_than_zero'),
        ]);
        $this->form_validation->set_rules('transaction_password', 'lang:transaction_password', 'trim|required|min_length[6]|max_length[100]|callback_check_transaction_password',[
            "required"=>lang('required'),
            "min_length"=>sprintf(lang('minlength'),lang('transaction_password'),"6"),
            "max_length"=>sprintf(lang('max_length'),lang('transaction_password'),"100"),
            'check_transaction_password' => lang('invalid_transaction_password')
        ]);
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

    public function check_transaction_password($password) {
        return $this->payout_model->checkTransactionPassword($this->LOG_USER_ID, $password);
    }

    // 

    public function payout_details_get()
    {
        $this->load->model('ewallet_model');
        $user_id = $this->rest->user_id;
        $minimum_payout_amount = $this->payout_model->getMinimumPayoutAmount() * 1;
        $maximum_payout_amount = $this->payout_model->getMaximumPayoutAmount() * 1;
        $balance_amount = $this->payout_model->getUserBalanceAmount($user_id) * 1;
        $req_amount = $this->payout_model->getRequestPendingAmount($user_id) * 1;
        $total_amount = $this->ewallet_model->getTotalReleasedAmount($user_id) * 1;
        $config_details = $this->configuration_model->getSettings();
        $payout_method = $this->payout_model->getUserPayoutType($user_id);
        $payoutFee = format_currency($config_details["payout_fee_amount"]) * 1;
        $possible_payout_amount = $balance_amount - $config_details["payout_fee_amount"] * 1;
        if($config_details["payout_fee_mode"] == 'percentage') {
            $possible_payout_amount = $balance_amount * 100 / (100 + $config_details["payout_fee_amount"]);
            $payoutFee = $config_details["payout_fee_amount"] . "%";
        }
        $payout_fee_deduct = $balance_amount - $possible_payout_amount;

        if ($possible_payout_amount <= $maximum_payout_amount) {
            $available_max_payout = $possible_payout_amount;
        } else {
            $available_max_payout = $maximum_payout_amount;
        }

        $objData = compact("minimum_payout_amount", "maximum_payout_amount", "balance_amount", "req_amount", "total_amount", "payout_method", "possible_payout_amount", "available_max_payout");
        $objData["payout_fee_mode"] = $config_details["payout_fee_mode"];
        $objData["payout_fee_amount"] = $config_details["payout_fee_amount"] * 1;

        $recyclerData = [];
        $recyclerData[] = ["title" => lang('ewallet_balance'), "value" => format_currency($balance_amount)];
        $recyclerData[] = ["title" => lang('payout_req_amount'), "value" => format_currency($req_amount)];
        $recyclerData[] = ["title" => lang('released_amount'), "value" => format_currency($total_amount)];
        $recyclerData[] = ["title" => lang('payout_method'), "value" => $payout_method];
        $recyclerData[] = ["title" => lang('minimum_payout_amount'), "value" => format_currency($minimum_payout_amount)];
        $recyclerData[] = ["title" => lang('maximum_payout_amount'), "value" => format_currency($maximum_payout_amount)];
        $recyclerData[] = ["title" => lang('available_max_payout'), "value" => format_currency($available_max_payout)];
        $recyclerData[] = ["title" => lang('payout_request_validity'), "value" => $config_details['payout_request_validity']];
        $recyclerData[] = ["title" => lang('payout_fee'), "value" => $payoutFee];


        $this->set_success_response(200, compact("objData", "recyclerData"));
    }

    public function request_payout_post()
    {
        $this->load->model('ewallet_model');
        $this->load->model('payout_model');
        $this->load->model('mail_model');

        $user_id = $this->rest->user_id;
        $post_arr = $this->validation_model->stripTagsPostArray($this->post());
        $this->form_validation->set_data($post_arr);
        
        $this->form_validation->set_rules("payout_amount", lang("payout_amount"), "trim|required|greater_than[0]");
        $this->form_validation->set_rules("transation_password", lang("transation_password"), "trim|required|min_length[6]|max_length[100]");
        
        if(!$this->form_validation->run()) {
            $this->set_error_response(422, 1004);
        }
        $amount_requested = $this->post('payout_amount');
        $transation_password = $this->post('transation_password');

        $password_flag = $this->payout_model->checkTransactionPassword($user_id, $transation_password);
        if(!$password_flag) {
            $this->set_error_response(401, 1015);
        }

        $minimum_payout_amount = $this->payout_model->getMinimumPayoutAmount() * 1;
        $maximum_payout_amount = $this->payout_model->getMaximumPayoutAmount() * 1;
        $balance_amount = $this->payout_model->getUserBalanceAmount($user_id) * 1;
        $config_details = $this->configuration_model->getSettings();
        $possible_payout_amount = $balance_amount - $config_details["payout_fee_amount"] * 1;
        if($config_details["payout_fee_mode"] == 'percentage') {
            $possible_payout_amount = $balance_amount * 100 / (100 + $config_details["payout_fee_amount"]);
        }

        if ($possible_payout_amount <= $maximum_payout_amount) {
            $available_max_payout = $possible_payout_amount;
        } else {
            $available_max_payout = $maximum_payout_amount;
        }

        if($amount_requested < $minimum_payout_amount)
            $this->set_error_response(422, 1027);
        if($amount_requested > $available_max_payout)
            $this->set_error_response(422, 1028);

        $kyc_status = $this->MODULE_STATUS['kyc_status'];
        if ($kyc_status == 'yes') {
            $kyc_upload = $this->validation_model->checkKycUpload($user_id);
            if ($kyc_upload != 'yes')
                $this->set_error_response(422, 1019);
        }

        $payout_amount = round($amount_requested / $this->DEFAULT_CURRENCY_VALUE, 8);
        $config_details = $this->configuration_model->getSettings();
        $deductAmount = $payout_amount + $config_details['payout_fee_amount'];
        if ($config_details['payout_fee_mode'] == 'percentage')
            $deductAmount = $payout_amount + ($payout_amount * $config_details['payout_fee_amount'] / 100);

        $request_date = date('Y-m-d H:i:s');
        $balance_amount = $this->payout_model->getUserBalanceAmount($user_id);

        if ($balance_amount < $deductAmount)
            $this->set_error_response(422, 1014);

        $res = $this->payout_model->insertPayoutReleaseRequest($user_id, $payout_amount, $request_date, 'pending');
        if ($res) {
            $this->payout_model->updateUserBalanceAmount($user_id, $deductAmount);
            $data_array = array();
            $data_array['tran_pass'] = $transation_password;
            $data_array['payout_amount'] = $payout_amount;
            $data_array['balance_amount'] = $balance_amount;
            $data = serialize($data_array);
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Payout request sent ', $this->LOG_USER_ID, $data);

            $mail_arr['payout_amount'] = $payout_amount;
            $mail_arr['username'] = $this->LOG_USER_NAME;
            $mail_arr['email'] = $this->validation_model->getUserEmailId($this->ADMIN_USER_ID);
            $mail_arr['first_name'] = '';
            $mail_arr['last_name'] = '';
            $this->mail_model->sendAllEmails('payout_request', $mail_arr);
            $this->set_success_response(204);
        } else {
            $this->set_error_response(500);
        }
    }

    public function cancel_payout_request_post()
    {
        $this->load->model('ewallet_model');
        $this->load->model('payout_model');
        $this->load->model('mail_model');

        $user_id = $this->rest->user_id;
        $req_amount = $this->payout_model->getRequestPendingAmount($user_id) * 1;
        if($req_amount <= 0) {
            $this->set_error_response(406);
        }
        $res = $this->payout_model->deletePayoutWithdrawed($user_id);
        if ($res) {
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, lang('cancelled_waiting_withdrawal'), $this->LOG_USER_ID);
            $this->set_success_response(204);
        } else {
            $this->set_error_response(500);
        }
    }

}
