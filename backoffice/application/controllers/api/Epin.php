<?php

/*
 * Epin Management
 * (c) Infinte Open Source Solutions LLP
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */
require_once 'Inf_Controller.php';

/**
 * EPIN Management of BackOffice
 */
class Epin extends Inf_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("android/new/android_model");
        $this->load->model("Api_model");
        $this->load->model("epin_model");
        $this->load->model('payout_model');
        $this->LOG_USER_ID = $this->rest->user_id;
        $this->LOG_USER_TYPE = 'user';
        $this->LOG_USER_NAME = $this->validation_model->IdToUserName($this->LOG_USER_ID);
    }

    /**
     *  EPIN Tile
      * @api
     */
    public function epin_tile_get()
    {
        $this->lang->load('epin', $this->LANG_NAME);
        //START OF  TILE DATA
        $active_epins = $this->epin_model->activeEpins($this->LOG_USER_ID);// active_epins
        
        $epin_requests_count = $this->epin_model->getAllEpinRequestsCountNew($this->LOG_USER_ID);//epin_requests_count

        // END OF TILE DATA

        $data['epin_tile'][] = [
                'amount' => $active_epins->count ?? 0,
                'text' => $this->IS_MOBILE ? lang('active_epin_count') : 'active_epin',
                'icon'=> SITE_URL . "/uploads/images/logos/Paid-w.png",
                'bg_color'=>"#5bc554"

        ];
        $data['epin_tile'][] = [
                'amount' => $this->IS_MOBILE ? format_currency($active_epins->amount) : $active_epins->amount,
                'text' => $this->IS_MOBILE ? lang('active_epinsss') : 'epin_balance',
                'icon'=>SITE_URL . "/uploads/images/logos/E-Wallet-w.png",
                'bg_color'=>"#44badc"
        ];
        $data['epin_tile'][] = [
            'amount' => $epin_requests_count ?? 0,
            'text' => $this->IS_MOBILE ? lang('epin_requests') : 'pending_epin',
            'icon'=>SITE_URL . "/uploads/images/logos/Pending-w.png",
            'bg_color'=>"#ffe690"
        ];
        $this->set_success_response(200, $data);
    }
    /**
     *  End of EPIN Tile
      * @api
     */
    
    public function epin_search_get() {
            $data = $this->epin_model->getEpinsByKeywordNew($this->input->get('term', TRUE),$this->LOG_USER_ID);
            $this->set_success_response(200, $data);
    }
    
    /**
     *  EPIN List
      * @api
    */
    public function epin_list_get()
    {
        // Epin balance
        $balamount = $this->epin_model->getBalanceAmount($this->LOG_USER_ID);

        // FILTER DATA
        $amount_details = $this->epin_model->getAllEpinAmounts();//epin amound
        $epins = $this->epin_model->getUserEpinList($this->LOG_USER_ID);// loged user epin 
        // END OF FILTER
        $columns = array( 
            0 => '',
            1 =>'user_detail_name', 
            2 =>'pin_numbers',
            3 => 'pin_amount',
            4 => 'pin_balance_amount',
            5 => 'status',
            'expiry_date'=> 'pin_expiry_date'
        );
        
        $feild = $this->input->get(null, true);
        $order = $feild['order']??'';
        $direction = $feild['direction']??'asc';
        $filter = [
            'limit' => $feild['length']??10,
            'start' => $feild['start']??0,
            'order' => $order_columns[$order]??null,
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];
        
        $user_names = $this->LOG_USER_NAME;
        $epins = $this->input->get('epins') ?: [];
        $amounts = $this->input->get('amounts') ?: [];

        $epins = array_filter($epins, fn($value) => !is_null($value) && $value !== '');
        $amounts = array_filter($amounts, fn($value) => !is_null($value) && $value !== '');

        $status = $this->input->get('status') ?: "active";
        $user_ids = $this->validation_model->userNamesToIDs($user_names);
        $data = []; 
        $count = $this->epin_model->countEpinList($user_ids, $epins, $amounts, $status);
        $epins = $this->epin_model->epinList($filter, $user_ids, $epins, $amounts, $status);

        $data['count'] = $count;
        $data['table_data'] = [];
        // dd($epins);
        foreach($epins as $epin) {
            $status_name = "";
            $refund = lang('na');
            if($epin['status'] == "yes" && ($epin['pin_expiry_date'] >= date('Y-m-d')) && ($epin['pin_balance_amount'] > 0)  ) {
                if($epin['purchase_status'] == "yes")
                {
                    $refund = lang('refund');
                }
                
                $status_name = "active";
            }
            if($epin['status'] == "no" && $epin['pin_balance_amount'] > 0) {
                $status_name = "blocked";
            }elseif ($epin['status'] == "no" && $epin['pin_balance_amount'] == 0) {
                $status_name = "used";
            }
            
            if($epin['status'] == "yes" && (($epin['pin_balance_amount'] <= 0) || $epin['pin_expiry_date'] < date('Y-m-d'))) {
                $status_name = "expired";
            }
            
            if($epin['status'] == "delete") {
                $status_name = "deleted";
            }
            $data['table_data'][] = [
                'epin_id' => $epin['pin_id'],
                'pin_number' => $epin['pin_numbers'],
                'amount' => ($epin['pin_amount']),
                'balance_amount' => ($epin['pin_balance_amount']),
                'status' => lang($status_name),
                'expiry_date' => date("F j, Y",strtotime($epin['pin_expiry_date'])),
                'refund' => $refund,
            ];
            $data['amounts'] = $amount_details;
            $data['epins'] = $epins;
        }
        $this->set_success_response(200, $data);
    }
    /**
     * End of EPIN List
      * @api
    */


    /**
    *Pending epin request
    */
    public function epin_pending_requests_get() {
        $columns = array( 
            'request_date' => 'req_date',
            'expiry_date'  => 'pin_expiry_date',
            2              =>'req_pin_count',
            3              => 'req_rec_pin_count',
            'pin_amount'   => 'pin_amount'
        );
        
        $feild = $this->input->get(null, true);
        $order = $feild['order']??'';
        $direction = $feild['direction']??'asc';
        $filter = [
            'limit' => $feild['length']??10,
            'start' => $feild['start']??0,
            'order' => $order_columns[$order]??null,
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];
        
        $user_ids = $this->LOG_USER_ID;
        $count = $this->epin_model->getAllEpinRequestsCountNew($user_ids);
        $epin_requests = $this->epin_model->getAllEpinRequestsNew($filter, $user_ids, $this->PAGINATION_PER_PAGE, $this->input->get('offset'));
        
        $data = [];
        // $data['table_data'] = [];
        $data['count'] = $count;
        foreach($epin_requests as $request) {
            $data['table_data'][] = [
                'requested_pin_count' => $request['req_pin_count'],
                'pin_count' =>  $request['req_rec_pin_count'],
                'amount' => $this->IS_MOBILE ? format_currency($request['pin_amount']) : $request['pin_amount'],
                'requested_date' => date("F j, Y",strtotime($request['req_date'])),
                'expiry_date' => date("F j, Y",strtotime($request['pin_expiry_date']))
            ];
        }
         $this->set_success_response(200, $data);
        
    }

    /**
    *Epin Transfer History
    */
    public function epin_transfer_history_get() {
        $columns = array( 
            0 => 'ft.user_name',
            2 => 'pin.pin_amount',
            3 => 'eth.date',
        );
        
        $feild = $this->input->get(null, true);
        $order = $feild['order']??'';
        $direction = $feild['direction']??'asc';
        $filter = [
            'limit' => $feild['length']??10,
            'start' => $feild['start']??0,
            'order' => $order_columns[$order]??null,
            'direction' => in_array($direction, ['asc', 'desc']) ? $direction : 'asc',
        ];
        
        $from_date = $this->input->get('start_date');
        $to_date = $this->input->get('end_date');
        $count  = $this->epin_model->getEpinTransferListCount($this->LOG_USER_ID, $from_date, $to_date);
        $transfer_details = $this->epin_model->getEpinTransferList($this->LOG_USER_ID, $filter, $from_date, $to_date);
        $data = [];
        $data['count'] = $count;
        foreach($transfer_details as $item) {
            
            $member_name = $item->from_user_id == $this->LOG_USER_ID ? $item->member_name . " ( ". $item->user_name . " )" : $item->member_name2 . " ( ". $item->user_name2 . " )";
            
            $data['table_data'][] = [
                'member_name'      => $member_name,
                'epin'             => $item->pin_number,
                'transferred_date' => date("F j, Y, g:i a",strtotime($item->date)),
                'type'             => $item->from_user_id == $this->LOG_USER_ID ? lang('transferred') : lang('received'),
                'amount'           => $this->IS_MOBILE ? format_currency($item->pin_amount) : $item->pin_amount 
            ];
        }
         $this->set_success_response(200, $data);
        
    }

    /**
    **Epin Transfer History
    */
    
    function valid_epin($epin)
    {
        if ($epin == 'default') {
            $this->form_validation->set_message('valid_epin', lang('select_epin'));
            return false;
        }
        return true;
    }

    /**
     * [ end of epin_transfer]
     * @return
     */

    /**
     * Request EPIN
     * @api
     */
    public function request_epin() {
        $request_date = date('Y-m-d H:i:s');
        if ($this->validate_request_epin()) {
            $post_arr = $this->input->post(NULL, TRUE);
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
            $cnt = $post_arr['count'];
            $pin_amount = $post_arr['amount'];
            $expiry_date = date('Y-m-d', strtotime('+6 months'));  //pin valid for 6 months
            $req_user = $this->LOG_USER_ID;
            $res = $this->epin_model->insertPinRequest($req_user, $cnt, $request_date, $expiry_date, $pin_amount);
            if ($res) {
                $loggin_id = $this->LOG_USER_ID;
                $admin_id = $this->ADMIN_USER_ID;
                $this->validation_model->insertUserActivity($loggin_id, 'epin requested', $admin_id);
                $json_response['status'] = TRUE;
                $json_response['message'] = lang('pin_request_send_successfully');
            } else {
                $json_response['status'] = FALSE;
                $json_response['message'] = lang('error_on_pin_request');
            }
        } else {
            $json_response['status'] = FALSE;
            $json_response['message'] = strip_tags($this->form_validation->error_string());
        }
        $json_response['data'] = [];
        //http_response_code(200);
        echo json_encode($json_response);
        exit();
    }

    /**
     * Validate request EPIN
     * @return boolean
     */
    private function validate_request_epin() {
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('count', lang('count'), 'trim|required|integer|greater_than[0]|max_length[5]');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    /**
     * My EPIN
     * @api
     */
    public function get_my_epin() {
        $limit = $this->input->post('limit', TRUE);
        $offset = $this->input->post('offset', TRUE);
        $pin_details = $this->epin_model->pinSelector($offset, $limit, "generate");
        $msg = empty($pin_details) ? lang('no_epin_found') : lang('my_e_pin');
        $json_response['status'] = TRUE;
        $json_response['message'] = $msg;
        $json_response['data'] = array_values($pin_details['pin_numbers']);
        http_response_code(200);
        echo json_encode($json_response);
        exit();
    }

    /**
     * EWallet EPin Purchase
     * @api
     */
    public function ewallet_pin_purchase() {
        $this->load->model('ewallet_model');
        $user_id = $this->LOG_USER_ID;
        $balamount = $this->ewallet_model->getBalanceAmount($user_id);
        $amount_details = $this->ewallet_model->getAllEwalletAmounts();
        $msg = '';
        if ($this->validate_ewallet_pin_purchase()) {
            $pin_post_array = $this->input->post(NULL, TRUE);
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
                        $expiry_date = date('Y-m-d', strtotime('+6 months', strtotime($uploded_date)));
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
                            $json_response['status'] = FALSE;
                            $json_response['message'] = $msg1 . $rec . $msg2;
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
                                $json_response['status'] = TRUE;
                                $json_response['message'] = lang('epin_purchased_successfully');
                            } else {
                                $this->ewallet_model->rollback();
                                $msg = lang('error_on_epin_purchase');
                                $json_response['status'] = FALSE;
                                $json_response['message'] = lang('error_on_epin_purchase');
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
                            $json_response['status'] = FALSE;
                            $json_response['message'] = lang('no_epin_found_please_contact_administrator');
                        }
                    } else {
                        $json_response['status'] = FALSE;
                        $json_response['message'] = lang('no_sufficient_balance_amount');
                    }
                } else {
                    $json_response['status'] = FALSE;
                    $json_response['message'] = lang('invalid_transaction_password');
                }
            } else {
                $msg = lang('error_on_purchasing_epin_please_try_again');
                $this->redirect($msg, 'ewallet/ewallet_pin_purchase', FALSE);
            }
        } else {
            $json_response['status'] = FALSE;
            $json_response['message'] = strip_tags($this->form_validation->error_string());
        }
        http_response_code(200);
        $json_response['data'] = [];
        echo json_encode($json_response);
        exit();
    }

    /**
     * Validation For EWallet EPin Purchase
     * @return boolean
     */
    private function validate_ewallet_pin_purchase() {
        $this->form_validation->set_rules('passcode', lang('transaction_password'), 'trim|required');
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('pin_count', lang('epin_count'), 'trim|required|integer|greater_than[0]');

        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function epin_transfer_report_view() {

        $this->load->model('report_model');

        $user_id = $this->LOG_USER_ID;

        if (!($this->input->post('week_date1')) && !($this->input->post('week_date2')) && !($this->input->post('to_user_name'))) {
            $json_response['status'] = false;
            $json_response['message'] = lang('please_select_date');
            $json_response['data'] = array();
            echo json_encode($json_response);
            exit();
        }

        $to_user_name = strip_tags($this->input->post('to_user_name', TRUE));
        if (!empty($to_user_name)) {
            if (!($this->report_model->userNameToID($to_user_name))) {
                $json_response['status'] = false;
                $json_response['message'] = lang('invalid_user_name');
                $json_response['data'] = array();
                echo json_encode($json_response);
                exit();
            }
        }

        if ($this->input->post('week_date1') && $this->input->post('week_date2')) {
            if ($this->input->post('week_date1') > $this->input->post('week_date2')) {
                $json_response['status'] = false;
                $json_response['message'] = lang('date_greater');
                $json_response['data'] = array();
                echo json_encode($json_response);
                exit();
            }
        }

        $user_name = $this->validation_model->IdToUserName($user_id);

        $week_date1 = strip_tags($this->input->post('week_date1'));

        $week_date2 = strip_tags($this->input->post('week_date2'));

        $report_date = date("Y-m-d H:i:s");
        if ($week_date1) {
            if ($week_date2) {
                $report_date = lang('from') . "\t" . $week_date1 . "\t" . lang('to') . "\t" . $week_date2;
            } else {
                $report_date = $week_date1;
            }
        } else if ($week_date2) {
            $report_date = $week_date2;
        }

        $to_user_id = $this->report_model->userNameToID($to_user_name);

        $limit = $this->input->post('limit');
        if ($this->input->post('offset'))
            $page = $this->input->post('offset');
        else
            $page = 0;

        $transfer_details = $this->report_model->getEpinTransferDetailsForUser($week_date1, $week_date2, $user_id, $to_user_id, $page, $limit);

        $json_response['status'] = true;
        $json_response['message'] = lang('transfer_details');
        $json_response['data'] = $transfer_details;
        echo json_encode($json_response);
        exit();
    }

    /**
     * Listamount_list
     * @api
     */
       public function amount_list() {
             $this->load->model('ewallet_model');
             $amount_details = $this->Api_model->getsAllEwalletAmounts();
              $json_response['status'] = true;
              $json_response['message'] = lang('success');
             $json_response['data'] =  $amount_details;

        echo json_encode($json_response);
        exit();

    }
     /**
     *BalanceAmount
     * @api
     */
       public function balance_amount() {
        $this->load->model('ewallet_model');
        $user_id = $this->LOG_USER_ID;
        $balamount = $this->ewallet_model->getBalanceAmount($user_id);
         $json_response['status'] = true;
         $json_response['message'] = lang('success');
        $json_response['data'] =  $balamount;

        echo json_encode($json_response);
        exit();
    }
    public function payment_post() {
        $this->load->model("ewallet_model");
        $this->load->model("repurchase_model");
        $this->load->model("register_model");
        $user_id = $this->rest->user_id;
        $post_arr = $this->post();
        $this->form_validation->set_data($post_arr);

        if ($this->validatePurchasePayment()) {
            $epin_details = array_filter($post_arr['epin_code'], 'strlen');
            $pin_array = $this->epin_model->validateAllEpins($epin_details, $post_arr['payment_amount'], $user_id);
            $is_pin_duplicate = (count(array_column($pin_array, 'pin')) != count(array_unique(array_column($pin_array, 'pin'))));
            // if ($is_pin_duplicate) {
            //     $msg = $this->lang->line('duplicate_epin');
            //     $this->redirect($msg, "upgrade/package_upgrade", false);
            // }
            if ($pin_array['valid']) {
                if ($pin_array['amount_reached'] > 0) {
                    $this->set_error_response(422, 1025);
                } else {
                    if ($this->epin_model->epinPayment($pin_array, $user_id)) {
                        $this->set_success_response(204);
                    } else {
                        $this->set_error_response(500);
                    }
                }
            } else {
                $this->set_error_response(422, 1016);
            }
        } else {
            $this->set_error_response(422, 1004);
        }
    }
    public function validatePurchasePayment()
    {
        $this->form_validation->set_rules('payment_amount', lang('payment_amount'), 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('epin_code[]', lang('epin_code'), 'trim|required|callback_duplicate_epin');

        $this->form_validation->set_message('duplicate_epin', lang('duplicate_epin'));
        $validation_status = $this->form_validation->run();
        return $validation_status;
    }
    function duplicate_epin() {
        return count(array_map('strtolower', $this->post('epin_code'))) != count(array_unique(array_map('strtolower', $this->post('epin_code')))) ? false : true;
    }

    public function epin_amounts_get() {
        $balamount = $this->epin_model->getBalanceAmount($this->LOG_USER_ID);
        $amount_details = $this->epin_model->getAllEpinAmounts(); // amounts list
        if($this->IS_MOBILE) {
            $balamount = format_currency($balamount);
        }
        $amounts = [];
        foreach ($amount_details as $detail) {
            if($this->IS_MOBILE) {
                $amounts[] = ['id' => $detail['id'], 'value' => format_currency($detail['amount'])];
            } else {
                $amounts[] = ['id' => $detail['id'], 'value' => $detail['amount']];
            }
        }
        $this->set_success_response(200, [
             'amount' => $amounts,
             'balance' =>$balamount,
        ]);
    }

    /* Refund E-pin */

    public function epin_refund_post(){
        
        $delete_id = $this->post('delete_id');
        $action = $this->post('action');
        $result = '';
        if ($action == 'refund') {
            $is_active_pin = $this->epin_model->isActivePin($delete_id);
            if ($is_active_pin > 0) {
                $result = $this->epin_model->deleteEPin($delete_id, $action);
            }
            if ($result) {
                $data_array['refund_id'] = $delete_id;
                $data = serialize($data_array);
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'epin refunded', $this->LOG_USER_ID, $data);

                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'refund_epin', 'EPIN Refunded');
                }
                $this->set_success_response(200);
            } else {
                $this->set_error_response(422, 1004);
                $this->redirect($msg, 'epin/my_epin', FALSE);
            }
        }
    }

    public function epin_purchase_post() {
        $user_id = $this->rest->user_id;
        $this->lang->load('ewallet',$this->LANG_NAME);
        $this->load->model('ewallet_model');
        
        $this->form_validation->set_data($this->post());
        if(!$this->epin_purchase_validation()) {
            $this->set_error_response(422, 1004);
        }
        
        $pin_count = $this->post('pin_count');
        $amount_id = $this->post('amount');
        $tran_pass = $this->post('passcode');
        $expiry_date = date('Y-m-d', strtotime($this->post('expiry_date')));
        
        $total_epin = $this->epin_model->getEpinsCount();
        $user_id = $this->LOG_USER_ID;
        $user_name = $this->validation_model->IdToUserName($user_id);
        $pass = $this->ewallet_model->getUserPassword($user_id);
        if (!password_verify($tran_pass, $pass)) {
            $this->set_error_response(422, 1015);
        }
        
        $balamount = $this->epin_model->getBalanceAmount($user_id);
        
        $amount = $this->epin_model->getEpinAmount($amount_id);
        $tot_avb_amt = $amount * $pin_count;
        
        if ($tot_avb_amt > $balamount) {
            $this->set_error_response(422,1014);
        }
        
        $uploded_date = date('Y-m-d H:i:s');
        $purchase_status = 'yes';
        $status = 'yes';
        
        // $max_active_pincount = $this->epin_model->getMaxPinCount();
        // $current_active_pin_count = $this->epin_model->getAllActivePinspage($purchase_status);
        // $balance_count = $max_active_pincount - $current_active_pin_count;
        
        $this->epin_model->begin();
        $transaction_id = $this->epin_model->getUniqueTransactionId();
        $res = $this->epin_model->generatePasscode($pin_count, $status, $uploded_date, $amount, $expiry_date, $purchase_status, $amount_id, $user_id, $this->ADMIN_USER_ID, $transaction_id);
        
        if (!$res) { // the purchase operation failed
            $this->ewallet_model->rollback();
            $this->set_error_response(422, 1044);
        }
        
        $bal = round($balamount - $tot_avb_amt, 8);
        $update = $this->ewallet_model->updateBalanceAmount($user_id, $bal);
        
        if (!$update) { // updation of balance failed
            $this->ewallet_model->rollback();
            $this->set_error_response(422, 1044);
        }
        
        $this->ewallet_model->commit();
        
        $data = serialize($this->post());
        $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'epin purchased using ewallet', $this->LOG_USER_ID, $data);
        
        $this->set_success_response(200);
    }
    public function check_transaction_password($password) {
        return $this->payout_model->checkTransactionPassword($this->LOG_USER_ID, $password);
    }
    public function epin_purchase_validation() {
        $this->lang->load('validation', $this->LANG_NAME);
        $this->form_validation->set_rules('passcode', lang('transaction_password'), 'trim|required|callback_check_transaction_password',[
           "required"=>lang('required')
        ]);
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required|greater_than[0]', [
            'required' => lang('required'),
            'greater_than' => lang('greater_zero')
        ]);
        $this->form_validation->set_rules('pin_count', lang('epin_count'), 'trim|required|integer|greater_than[0]', [
            'required' => lang('required'),
            'greater_than' => lang('greater_zero')
        ]);

        $this->form_validation->set_rules('expiry_date', lang('date'), 'trim|required|valid_date|date_less_than_current_date', [
            'required' => sprintf(lang('required'), lang('date')),
            'valid_date' => lang('select_valid_date'),
            'date_less_than_current_date' => lang('valid_date')
        ]);
        return $this->form_validation->run();
    }

    /**
     * [requset epin]
     */
    public function request_epin_post() {
        $user_id = $this->rest->user_id;
        $this->lang->load('ewallet',$this->LANG_NAME);

        $this->form_validation->set_data($this->post());
        if(!$this->reuest_epin_validation()) {
            $this->set_error_response(422, 1004);
        }

        $epin_count = $this->post('pin_count');
        $amount = $this->epin_model->getEpinAmount($this->post('amount'));
        $expiry_date = date('Y-m-d H:i:s', strtotime($this->post('expiry_date')));
        $request_date = date('Y-m-d H:i:s');

        $total_epin = $this->epin_model->getEpinsCount();
        $res = $this->epin_model->insertPinRequest($this->LOG_USER_ID, $epin_count, $request_date, $expiry_date, $amount);
        if (!$res) {
            $this->set_error_response(422, 1045);
        }

        $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'epin requested', $this->ADMIN_USER_ID, serialize($this->post()));

         $this->set_success_response(200);
    }

    // validate epin request
    protected function reuest_epin_validation() {
        $this->lang->load('validation', $this->LANG_NAME);
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required|greater_than[0]', [
            'required' => sprintf(lang('required'), 'amount'),
            'greater_than' => lang('greater_zero')
        ]);
        $this->form_validation->set_rules('pin_count', lang('epin_count'), 'trim|required|integer|greater_than[0]', [
            'required' => sprintf(lang('required'), lang('epin_count')),
            'integer' => lang('digits_only'),
            'greater_than' => lang('greater_zero')
        ]);
        $this->form_validation->set_rules('expiry_date', lang('date'), 'trim|required|valid_date|date_less_than_current_date', [
            'required' => sprintf(lang('required'), lang('date')),
            'valid_date' => lang('select_valid_date'),
            'date_less_than_current_date' => lang('valid_date')
        ]);
        return $this->form_validation->run();
    }
    /**
    *end of epin request
    */

    /**
     * EPIN Transfer
     * @api
     */
    public function epin_transfer_post() {
        $this->form_validation->set_data($this->post());
        if (!$this->validate_epin_transfer()) {
            $this->set_error_response(422, 1004);
        }
        $post_arr = $this->validation_model->stripTagsPostArray($this->post());
        if(!$this->Api_model->checkEpinBelongsToUser($this->LOG_USER_ID, $post_arr['epin'])) {
            $this->set_error_response(422, 1016);
        }
        $post_arr['epin'] = $this->Api_model->NumbertoEpinID($post_arr['epin']);
        
        $to_user = $this->validation_model->userNameToID($post_arr['transfer_user']);
        if(!$to_user) {
            $this->set_error_response(422, 1011);
        }
        $res = $this->epin_model->epinAllocation($to_user, $post_arr['epin']);
        if (!$res) {
            $this->set_error_response(422, 1046);
        }

        $data = serialize($post_arr);
        $this->epin_model->insertEpinTransferHistory($this->LOG_USER_ID, 'Epin transferred', $to_user, $this->LOG_USER_ID, $post_arr['epin'], $data);
        $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Epin transferred', $to_user, $data);
        
        $this->set_success_response(200);
    }

    function validate_epin_transfer() {
        $this->lang->load('validation', $this->LANG_NAME);
        $this->form_validation->set_rules('epin', lang('epin'), 'trim|required');
        $this->form_validation->set_rules('transfer_user', lang('user_name'), 'trim|required|callback_valid_user|callback_check_match');
        return $this->form_validation->run();
    }

    /**
     * User Validity
     * @param string $user_name
     * @return boolean
     */
    function valid_user($user_name) {
        $user_id = $this->validation_model->userNameToID($user_name);
        if (!$user_id) {
            $this->form_validation->set_message('valid_user', lang('invalid_username'));
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Checking User Match
     * @param string $user_name
     * @return boolean
     */
    function check_match($user_name) {
        if ($this->validation_model->userNameToID($user_name) == $this->LOG_USER_ID) {
            $this->form_validation->set_message('check_match', lang('can_not_transfer_to_same_user'));
            return FALSE;
        }
        if ($this->validation_model->userNameToID($user_name) == $this->validation_model->getAdminId()) {
            $this->form_validation->set_message('check_match', lang('can_not_transfer_to_admin'));
            return FALSE;
        }
        return TRUE;
    }

    /**
     * E-pin raw list
     * @api
     */
    public function epin_numbers_get() {
        $pin_details = $this->epin_model->getEpinList($this->LOG_USER_ID);
        $epin_numbers = [];
        foreach ($pin_details as $detail) {
            $epin_numbers[] = ['id' => $detail['pin_numbers'], 'value' => $detail['pin_numbers']];
        }
        $this->set_success_response(200, [
            'epin_numbers' => $epin_numbers
        ]);
    }

}
?>
