<?php

require_once 'Inf_Controller.php';

class Payout extends Inf_Controller
{

    function __construct()
    {
        parent::__construct();
    }
    public function index() {
        $title = lang('payout');
        $this->VIEW_DATA['title'] = $this->COMPANY_NAME . ' | ' . $title;

        $this->HEADER_LANG['page_top_header'] = $title;
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = $title;
        $this->HEADER_LANG['page_small_header'] = '';
        $this->set_public_variables();

        if ($this->input->get('tab') == "requests") {
            $this->payout_model->setPayoutViewed(1);
            $this->set_header_notification_box();
        }

        $user_id = $this->LOG_USER_ID;
        $total_amount_active_request=$this->payout_model->getTotalAmountPendingRequest('pending',$user_id);
        $total_amount_waiting_requests=$this->payout_model->getTotalAmountApproved($user_id);
        $total_amount_paid_request=$this->payout_model->getTotalAmountPaid($user_id);
        $total_amount_rejected_requests=$this->payout_model->getTotalAmountRejected('deleted',$user_id);
        // payout data 
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
        }
        //$possible_payout_amount = $possible_payout_amount>=0?:0;                                                                                  //
        if($config_details["payout_fee_mode"] == 'percentage') {
            $possible_payout_amount = $balance_amount * 100 / (100 + $config_details["payout_fee_amount"]);
        }
        $payout_fee_deduct = $balance_amount - $possible_payout_amount;

        if ($possible_payout_amount <= $maximum_payout_amount) {
            $available_max_payout = $possible_payout_amount;
        } else {
            $available_max_payout = $maximum_payout_amount;
        }
        $this->set('possible_payout_amount', $possible_payout_amount);
        $this->set('payout_fee_deduct', $payout_fee_deduct);
        $this->set('balance_amount', $balance_amount);
        $this->set('req_amount', $req_amount);
        $this->set('total_amount', $total_amount);
        $this->set('min_payout', $minimum_payout_amount);
        $this->set('max_payout', $maximum_payout_amount);
        $this->set('config_details', $config_details);
        $this->set('available_max_payout', $available_max_payout);
        $this->set('payout_method', $payout_method);

        $this->set('total_amount_active_request', $total_amount_active_request);
        $this->set('total_amount_waiting_requests', $total_amount_waiting_requests);
        $this->set('total_amount_paid_request', $total_amount_paid_request);
        $this->set('total_amount_rejected_requests', $total_amount_rejected_requests);
        $this->setView("newui/user/payout/index");
    }
    function summary_total()
    {
        $user_id = $this->LOG_USER_ID;
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
        }
        //$possible_payout_amount = $possible_payout_amount>=0?:0;                                                                                  //
        if($config_details["payout_fee_mode"] == 'percentage') {
            $possible_payout_amount = $balance_amount * 100 / (100 + $config_details["payout_fee_amount"]);
        }
        $payout_fee_deduct = $balance_amount - $possible_payout_amount;

        if ($possible_payout_amount <= $maximum_payout_amount) {
            $available_max_payout = $possible_payout_amount ;
        } else {
            $available_max_payout = $maximum_payout_amount;
        }

        // payout data 
        $total['minimum_payout_amount'] = $minimum_payout_amount;
        $total['maximum_payout_amount'] = $maximum_payout_amount;
        $total['balance_amount'] = format_currency($balance_amount);
        $total['req_amount'] = format_currency($req_amount);
        $total['total_amount'] = format_currency($total_amount);
        $total['payout_method'] = $payout_method;
        $total['possible_payout_amount'] = $possible_payout_amount;
        $total['available_max_payout']    = $available_max_payout;
        //

        $total['total_amount_active_request']    = thousands_currency_format($this->payout_model->getTotalAmountPendingRequest('pending',$user_id));
        $total['total_amount_waiting_requests']  = thousands_currency_format($this->payout_model->getTotalAmountApproved($user_id));
        $total['total_amount_paid_request']      = thousands_currency_format($this->payout_model->getTotalAmountPaid($user_id));
        $total['total_amount_rejected_requests'] = thousands_currency_format($this->payout_model->getTotalAmountRejected('deleted',$user_id));
        
        echo json_encode($total);
        exit();   
    }
    public function payout_requests_delete_action() {
        $post_arr = $this->input->post(null, true);
         if(!empty($post_arr['payouts'])) {
            foreach($this->input->post('payouts') as $request) {

                $user_id = $this->payout_model->getPayoutRequestUserId($request);
                $user_name = $this->validation_model->IdToUserName($user_id);
                // dd($user_name);
                if (!$user_id) {
                    echo json_encode([
                        'status' => 'failed',
                        'error_type' => 'unknown',
                        'message' =>  lang('invalid_user_name')
                    ]); die;
                }
                $res = $this->payout_model->deletePayoutRequest($request, $user_id,'cancelled');
                if ($res) {
                    $this->validation_model->insertUserActivity($this->LOG_USER_ID, lang('payout_request_deleted'), $this->LOG_USER_ID);
                    if ($this->LOG_USER_TYPE == 'employee') {
                        $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $user_id, 'delete_payout_request', 'Payout Request Deleted');
                    }
                } else {
                    echo json_encode([
                        'status' => 'failed',
                        'error_type' => 'unknown',
                        'message' =>  lang('Error_on_deleting_Payout_Request')
                    ]); die;
                }
            }
            echo json_encode([
                'status' => 'success',
                'message' => lang('withdrawal_canceled_successfully')
            ]); die;
        } else {
             echo json_encode([
                'status' => 'failed',
                'error_type' => 'unknown',
                'message' =>  lang('please_select_payout')
            ]); die;
        }
    }
      public function payout_status_approved_paid_list() {
        $columns = [
            // 0 => '',
            0 => 'paid_date',
            1 => 'paid_amount',
            2 => 'payment_method',
        ];
        
        $filter = [
          'limit' => $this->input->get('length'),
          'start' => intval($this->input->get("start")),
          'order' => isset($columns[$this->input->get('order')[0]['column']]) ? $columns[$this->input->get('order')[0]['column']] : '',
          'direction' => $this->input->get('order')[0]['dir']
        ];
        
        $user_ids = $this->LOG_USER_ID;
        $count = $this->payout_model->getReleasedWithdrawalCountNew($user_ids, 'approved_paid');
        $paid_requests = $this->payout_model->getReleasedWithdrawalDetailsNew($user_ids, 'approved_paid', $filter);
        
        $data = [];
        $total_amount = 0;
        foreach($paid_requests as $key => $item) {
            $total_amount += $item['paid_amount'];
            $data[] = [
                'paid_date'  => date("F j, Y, g:i a",strtotime($item['paid_date'])),
                'amount'     => format_currency($item['paid_amount']),
                'payout_method' => lang($item['payment_method'])
            ];
        }
        if($this->input->get('total_row') && !empty($paid_requests)) {
            $data[] = [
                'paid_date'      => '<span class="text-lg">'.lang('total').'</span>',
                'amount'         => format_currency($total_amount),
                'payout_method'  => ''
            ];
        }
        
        echo json_encode([
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data
        ]); die;
    }
    public function payout_status_approved_pending_list() {
        $columns = [
            0 => 'paid_date',
            1 => 'paid_amount',
            2 => 'payment_method',
        ];
        
        $filter = [
          'limit' => $this->input->get('length'),
          'start' => intval($this->input->get("start")),
          'order' => isset($columns[$this->input->get('order')[0]['column']]) ? $columns[$this->input->get('order')[0]['column']] : '',
          'direction' => $this->input->get('order')[0]['dir']
        ];
        
        $user_ids = $this->LOG_USER_ID;
        
        $count = $this->payout_model->getReleasedWithdrawalCountNew($user_ids, 'approved_pending');
        $waiting_requests = $this->payout_model->getReleasedWithdrawalDetailsNew($user_ids, 'approved_pending', $filter);
        
        $data = [];
        $total_amount = 0;
        foreach($waiting_requests as $key => $item) {
            $total_amount += $item['paid_amount'];
            $data[] = [
                "approved_date" => date("F j, Y, g:i a",strtotime($item['paid_date'])),
                'amount'        => format_currency($item['paid_amount']),
                'payout_method' => lang($item['payment_method'])
            ];
        }

        if($this->input->get('total_row') && !empty($waiting_requests)) {
            $data[] = [
                'approved_date'      => '<span class="text-lg">'.lang('total').'</span>',
                'amount'         => format_currency($total_amount),
                'payout_method'  => ''
            ];
        }
        
        echo json_encode([
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data
        ]); die;
    }
    public function payout_status_pending_list() {
        $columns = [
            0 => 'requested_amount_balance',
            1 => '',
            2 => 'requested_date',
            3 => '',
        ];
        
        $filter = [
          'limit' => $this->input->get('length'),
          'start' => intval($this->input->get("start")),
          'order' => isset($columns[$this->input->get('order')[0]['column']]) ? $columns[$this->input->get('order')[0]['column']] : '',
          'direction' => $this->input->get('order')[0]['dir']
        ];
        
        $user_ids = $this->LOG_USER_ID;
        
        $count = $this->payout_model->getPayoutWithdrawalCountNew($user_ids, 'pending');
        $pending_requests = $this->payout_model->getPayoutWithdrawalDetailsNew($user_ids, 'pending', $filter);
        $data = [];
        $total_amount = 0;
        foreach($pending_requests as $key => $item) {
            $total_amount += $item['payout_amount'];
            $data[] = [
                'request_id' =>$item['req_id'],
                'payout_amount' => format_currency($item['payout_amount']),
                'ewallet_balance' => format_currency($item['balance_amount']),
                "requested_date" => date("F j, Y, g:i a",strtotime($item['requested_date']))
            ];
        }
        if($this->input->get('total_row') &&  !empty($pending_requests)) {
            $data[] = [
                'request_id'      => '<span class="text-lg">'.lang('total').'</span>',
                "requested_date"  => '',
                'payout_amount'   => format_currency($total_amount),
                'ewallet_balance' => ''
            ];   
        }
        
        echo json_encode([
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data
        ]); die;
        
    }
    public function payout_status_rejected_list() {
        $columns = [
            0 => 'balance_amount',
            1 => '',
            2 => 'user_name',
            3 => 'payout_type',
        ];
        
        $filter = [
          'limit' => $this->input->get('length'),
          'start' => intval($this->input->get("start")),
          'order' => isset($columns[$this->input->get('order')[0]['column']]) ? $columns[$this->input->get('order')[0]['column']] : '',
          'direction' => $this->input->get('order')[0]['dir']
        ];
        
        $user_ids = $this->LOG_USER_ID;
        
        $count = $this->payout_model->getPayoutWithdrawalCountNew($user_ids, 'deleted');
        $rejected_requests = $this->payout_model->getPayoutWithdrawalDetailsNew($user_ids, 'deleted', $filter);
    
        $data = [];
        $total_amount = 0;
        foreach($rejected_requests as $key => $item) {
            $total_amount += $item['payout_amount'];
            $data[] = [
                'rejected_date' => date("F j, Y, g:i a",strtotime($item['updated_date'])),
                'amount'     => format_currency($item['payout_amount']),
                'requested_date' => date("F j, Y, g:i a",strtotime($item['requested_date']))
            ];
        }

        if($this->input->get('total_row') && !empty($rejected_requests)) {
            $data[] = [
                'rejected_date' => '<span class="text-lg">'.lang('total').'</span>',
                'amount'     => format_currency($total_amount),
                'requested_date' => ''
            ];   
        }
        
        echo json_encode([
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data
        ]); die;
    }
    //edited for cancel waiting withrawal
    function payout_release_request($action = '', $withdrawed_amount = '')
    {

        $payout_type = $this->configuration_model->getPayOutTypes();
        if ($payout_type == 'from_ewallet') {
            $msg = lang('you_dont_have_permission_to_access_this_page');
            $this->redirect($msg, 'home', FALSE);
        }

        $this->url_permission('ewallet_status');
        $title = lang('Request_Payout_Release');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $help_link = 'payout-release-request';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('Request_Payout_Release');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('Request_Payout_Release');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $this->load->model('ewallet_model');

        $user_id = $this->LOG_USER_ID;
        
        // Payout request is disabled for subscription expired user
        $subscription_status = $this->MODULE_STATUS['subscription_status'];
        $current_date = date('Y-m-d H:i:s');
        if($subscription_status == 'yes') {
            $subscription_config = $this->configuration_model->getSubscriptionConfig();
            if ($subscription_config['payout_status'] == 'yes') {
                $user_package_validity = $this->validation_model->getUserProductValidity($user_id);
                if($user_package_validity < $current_date){
                    $msg = lang('subscription_expired');
                    $this->redirect($msg, 'home/index', FALSE);   
                }
            }
        }
        //Subscrption payout request end

        $minimum_payout_amount = $this->payout_model->getMinimumPayoutAmount();
        $maximum_payout_amount = $this->payout_model->getMaximumPayoutAmount();
        $balance_amount = $this->payout_model->getUserBalanceAmount($user_id);
        $req_amount = $this->payout_model->getRequestPendingAmount($user_id);
        $total_amount = $this->ewallet_model->getTotalReleasedAmount($user_id);
        $config_details = $this->configuration_model->getSettings();
        $payout_method = $this->payout_model->getUserPayoutType($user_id);
        $possible_payout_amount = $balance_amount - $config_details["payout_fee_amount"];
        //$possible_payout_amount = $possible_payout_amount>=0?:0;                                                                                  //
        if($config_details["payout_fee_mode"] == 'percentage') {
            $possible_payout_amount = $balance_amount * 100 / (100 + $config_details["payout_fee_amount"]);
        }
        $payout_fee_deduct = $balance_amount - $possible_payout_amount;

        if ($possible_payout_amount <= $maximum_payout_amount) {
            $available_max_payout = $possible_payout_amount;
        } else {
            $available_max_payout = $maximum_payout_amount;
        }

        if ($action == 'cancel') {

            $res = $this->payout_model->deletePayoutWithdrawed($user_id);
            if ($res) {
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, lang('cancelled_waiting_withdrawal'), $this->LOG_USER_ID);
                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $user_id, 'cancelled_waiting_withdrawal', 'Waiting Withdrawal Cancel');
                }
                //

                $msg = lang('withdrawal_canceled_successfully');
                $this->redirect($msg, 'payout/payout_release_request', TRUE);
            } else {
                $this->redirect('', 'payout/payout_release_request');
            }
        }
        //ends
        if ($this->MODULE_STATUS['payout_release_status'] == 'ewallet_request') {
            $this->payout_model->setPayoutViewed(0);
            $this->set_header_notification_box();
        }

        $this->set('possible_payout_amount', $possible_payout_amount);
        $this->set('payout_fee_deduct', $payout_fee_deduct);
        $this->set('balance_amount', $balance_amount);
        $this->set('req_amount', $req_amount);
        $this->set('total_amount', $total_amount);
        $this->set('min_payout', $minimum_payout_amount);
        $this->set('max_payout', $maximum_payout_amount);
        $this->set('config_details', $config_details);
        $this->set('available_max_payout', $available_max_payout);
        $this->set('payout_method', $payout_method);

        $this->setView();
    }

    function validate_transation_password()
    {
        $this->form_validation->set_rules('payout_amount', lang('payout_amount'), 'trim|required|numeric');
        $this->form_validation->set_rules('transation_password', lang('transaction_password'), 'required');
        $res_val = $this->form_validation->run();
        return $res_val;
    }

    function my_income()
    {

        $title = $this->lang->line('income');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);
        $help_link = 'income-statement';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('income');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('income');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $user_name = $this->LOG_USER_NAME;
        $this->set('user_name', $user_name);

        $base_url = base_url() . "user/payout/my_income";
        $config = $this->pagination->customize_style();
        $config['base_url'] = $base_url;
        $config['per_page'] = $this->PAGINATION_PER_PAGE;
        $total_rows = $this->payout_model->getIncomeStatementCount($this->LOG_USER_ID);
        $config['total_rows'] = $total_rows;
        $config["uri_segment"] = 4;
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        
        
        $this->set("page", $page);

        $binary = $this->payout_model->getIncomeStatement($this->LOG_USER_ID, $page, $config['per_page']);
        $this->set('binary', $binary);

        $this->setView();
    }
     public function validate_payout_request()
    {   $this->lang->load('validation');
        $this->form_validation->set_rules('payout_amount', 'lang:amount', 'trim|required|greater_than[0]',[
            "required"=>lang('required'),
            "greater_than"=>lang('field_greater_than_zero'),
        ]);
        $this->form_validation->set_rules('transation_password', 'lang:transaction_password', 'trim|required|min_length[6]|max_length[100]',[
            "required"=>lang('required'),
            "min_length"=>sprintf(lang('minlength'),lang('transaction_password'),"6"),
            "max_length"=>sprintf(lang('max_length'),lang('transaction_password'),"100")
        ]);
        $validate_form = $this->form_validation->run_with_redirect('payout/payout_release_request');
        return $validate_form;
    }
    public function validate_payout_request_ajax()
    {    
        $this->lang->load('validation', $this->LANG_NAME);
        $this->form_validation->set_rules('payout_amount', 'lang:amount', 'trim|required|greater_than[0]',[
            "required"=>lang('required'),
            "greater_than"=>lang('field_greater_than_zero'),
        ]);
        $this->form_validation->set_rules('transaction_password', 'lang:transaction_password', 'trim|required|min_length[6]|max_length[100]',[
            "required"=>lang('required'),
            "min_length"=>sprintf(lang('minlength'),lang('transaction_password'),"6"),
            "max_length"=>sprintf(lang('max_length'),lang('transaction_password'),"100")
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
    function post_payout_release_request()
    {
        if ($this->input->post('payout_request_submit') && $this->validate_payout_request()) {
            $user_id = $this->LOG_USER_ID;

            $kyc_status = $this->MODULE_STATUS['kyc_status'];
            if ($kyc_status == 'yes') {
                $kyc_upload = $this->validation_model->checkKycUpload($user_id);
                if ($kyc_upload != 'yes') {
                    $msg = lang('kyc_not_uploaded');
                    $this->redirect("$msg", 'payout/payout_release_request', FALSE);
                }
            }

            $minimum_payout_amount = $this->payout_model->getMinimumPayoutAmount();
            $maximum_payout_amount = $this->payout_model->getMaximumPayoutAmount();
            $balance_amount = $this->payout_model->getUserBalanceAmount($user_id);
            $req_amount = $this->payout_model->getRequestPendingAmount($user_id);
            $total_amount = $this->payout_model->getReleasedPayoutTotal($user_id);
            $min_payout = $this->configuration_model->getMinPayout();
            $transation_password = (strip_tags($this->input->post('transation_password', TRUE)));
            $password_flag = $this->payout_model->checkTransactionPassword($user_id, $transation_password);
            if ($password_flag) {
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
                        $msg = $this->lang->line('payout_request_sent_successfully');
                        $this->redirect("$msg", 'payout/payout_release_request', TRUE);
                    } else {
                        $msg = $this->lang->line('payout_request_sending_failed');
                        $this->redirect("$msg", 'payout/payout_release_request', FALSE);
                    }
                } else if ($deductAmount > $balance_amount) {
                    $msg = $this->lang->line('insufficient_balance');
                    $this->redirect($msg, 'payout/payout_release_request', FALSE);
                } else if ($payout_amount <= $minimum_payout_amount) {
                    $default_currency_left_symbol = ($this->DEFAULT_SYMBOL_LEFT != '') ? $this->DEFAULT_SYMBOL_LEFT : "$";
                    $minimum_payout_amount = round(($minimum_payout_amount) * $this->DEFAULT_CURRENCY_VALUE, 8);
                    $msg = $this->lang->line('minimum_amount') . $default_currency_left_symbol . round($minimum_payout_amount);
                    $this->redirect($msg, 'payout/payout_release_request', FALSE);
                } else {
                    // dd("payout_amount=".$payout_amount." "."balance_amount".$balance_amount);
                    $default_currency_left_symbol = ($this->DEFAULT_SYMBOL_LEFT != '') ? $this->DEFAULT_SYMBOL_LEFT : "$";
                    $maximum_payout_amount = round(($maximum_payout_amount) * $this->DEFAULT_CURRENCY_VALUE, 8);
                    $msg = $this->lang->line('maximum_amount') . $default_currency_left_symbol . round($maximum_payout_amount);
                    $this->redirect($msg, 'payout/payout_release_request', FALSE);
                }
            } else {
                $msg = $this->lang->line('invalid_transaction_password');
                $this->redirect($msg, 'payout/payout_release_request', FALSE);
            }
        }
    }
    function post_payout_release_request_ajax()
    {
        $validated = $this->validate_payout_request_ajax();
        if ($validated['status']) {
            $user_id = $this->LOG_USER_ID;

            $kyc_status = $this->MODULE_STATUS['kyc_status'];
            if ($kyc_status == 'yes') {
                $kyc_upload = $this->validation_model->checkKycUpload($user_id);
                if ($kyc_upload != 'yes') {
                    echo json_encode([
                    'status'     => false,
                    'error_type' => 'unknown',
                    'message'    => lang('kyc_not_uploaded')
                    ]);die;
                }
            }

            $minimum_payout_amount = $this->payout_model->getMinimumPayoutAmount();
            $maximum_payout_amount = $this->payout_model->getMaximumPayoutAmount();
            $balance_amount = $this->payout_model->getUserBalanceAmount($user_id);
            $req_amount = $this->payout_model->getRequestPendingAmount($user_id);
            $total_amount = $this->payout_model->getReleasedPayoutTotal($user_id);
            $min_payout = $this->configuration_model->getMinPayout();
            $transation_password = (strip_tags($this->input->post('transaction_password', TRUE)));
            $password_flag = $this->payout_model->checkTransactionPassword($user_id, $transation_password);
            if ($password_flag) {
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
                         echo json_encode([
                        'status'  => 'success',
                        'message' => lang('payout_request_sent_successfully')
                         ]); die;
                    } else {
                        echo json_encode([
                        'status'     => false,
                        'error_type' => 'unknown',
                        'message'    => lang('payout_request_sending_failed')
                    ]);die;
                    }
                } else if ($deductAmount > $balance_amount) {
                    echo json_encode([
                        'status'     => false,
                        'error_type' => 'unknown',
                        'message'    => lang('insufficient_balance')
                    ]);die;
                } else if ($payout_amount <= $minimum_payout_amount) {
                    $default_currency_left_symbol = ($this->DEFAULT_SYMBOL_LEFT != '') ? $this->DEFAULT_SYMBOL_LEFT : "$";
                    $minimum_payout_amount = round(($minimum_payout_amount) * $this->DEFAULT_CURRENCY_VALUE, 8);
                    $msg = $this->lang->line('minimum_amount') . $default_currency_left_symbol . round($minimum_payout_amount);
                    echo json_encode([
                        'status'     => false,
                        'error_type' => 'unknown',
                        'message'    => $msg
                    ]);die;
                } else {
                    // dd("payout_amount=".$payout_amount." "."balance_amount".$balance_amount);
                    $default_currency_left_symbol = ($this->DEFAULT_SYMBOL_LEFT != '') ? $this->DEFAULT_SYMBOL_LEFT : "$";
                    $maximum_payout_amount = round(($maximum_payout_amount) * $this->DEFAULT_CURRENCY_VALUE, 8);
                    $msg = $this->lang->line('maximum_amount') . $default_currency_left_symbol . round($maximum_payout_amount);
                     echo json_encode([
                        'status'     => false,
                        'error_type' => 'unknown',
                        'message'    => $msg
                    ]);die;
                }
            } else {
                 echo json_encode([
                        'status'     => false,
                        'error_type' => 'unknown',
                        'message'    => lang('invalid_transaction_password')
                    ]);die;
            }
        }
        else {
            echo json_encode($validated);
            die;
        }
    }

    public function my_withdrawal_request($tab = 'tab1') {

        $tab1 = $tab2 = $tab3 = $tab4 = '';
        switch ($tab) {
            case 'tab1':
                $tab1 = ' checked';
                break;
            case 'tab2':
                $tab2 = ' checked';
                break;
            case 'tab3':
                $tab3 = ' checked';
                break;
            case 'tab4':
                $tab4 = ' checked';
                break;
            default:
                $tab1 = ' checked';
        }
        $title = lang('my_withdrawal_status');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('my_withdrawal_status');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('my_withdrawal_status');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $help_link = "my_withdrawal_status";
        $this->set("help_link", $help_link);


        $user_id = $this->LOG_USER_ID;

        $pagination1 = new Core_Inf_Pagination();
        $base_url1 = base_url() . "user/payout/my_withdrawal_request/tab1";
        $config1 = $pagination1->customize_style();
        $config1['base_url'] = $base_url1;
        $config1['per_page'] = $this->PAGINATION_PER_PAGE;
        $total_rows1 =  $this->payout_model->getPayoutWithdrawalCount($user_id, 'pending');
        $config1['total_rows'] = $total_rows1;
        $config1["uri_segment"] = 5;
        $pagination1->initialize($config1);
        if ($tab == 'tab1') {
            $page1 = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        } else {
            $page1 = 0;
        }
        $result_per_page1 = $pagination1->create_links();
        $this->set("result_per_page1", $result_per_page1);
        $this->set("page1", $page1);

        $pagination2 = new Core_Inf_Pagination();
        $base_url2 = base_url() . "user/payout/my_withdrawal_request/tab2/tab2";
        $config2 = $pagination2->customize_style();
        $config2['base_url'] = $base_url2;
        $config2['per_page'] = $this->PAGINATION_PER_PAGE;
        $total_rows2 = $this->payout_model->getReleasedWithdrawalCount($user_id, 'approved_pending');
        $config2['total_rows'] = $total_rows2;
        $config2["uri_segment"] = 6;
        $pagination2->initialize($config2);
        if ($tab == 'tab2') {
            $page2 = ($this->uri->segment(6)) ? $this->uri->segment(6) : 0;
        } else {
            $page2 = 0;
        }
        $result_per_page2 = $pagination2->create_links();
        $this->set("result_per_page2", $result_per_page2);
        $this->set("page2", $page2);

        $pagination3 = new Core_Inf_Pagination();
        $base_url3 = base_url() . "user/payout/my_withdrawal_request/tab3/tab3/tab3";
        $config3 = $pagination3->customize_style();
        $config3['base_url'] = $base_url3;
        $config3['per_page'] = $this->PAGINATION_PER_PAGE;
        $total_rows3 = $this->payout_model->getReleasedWithdrawalCount($user_id, 'approved_paid');
        $config3['total_rows'] = $total_rows3;
        $config3["uri_segment"] = 7;
        $pagination3->initialize($config3);
        if ($tab == 'tab3') {
            $page3 = ($this->uri->segment(7)) ? $this->uri->segment(7) : 0;
        } else {
            $page3 = 0;
        }
        $result_per_page3 = $pagination3->create_links();
        $this->set("result_per_page3", $result_per_page3);
        $this->set("page3", $page3);

        $pagination4 = new Core_Inf_Pagination();
        $base_url4 = base_url() . "user/payout/my_withdrawal_request/tab4/tab4/tab4/tab4";
        $config4 = $pagination4->customize_style();
        $config4['base_url'] = $base_url4;
        $config4['per_page'] = $this->PAGINATION_PER_PAGE;
        $total_rows4 =  $this->payout_model->getPayoutWithdrawalCount($user_id, 'deleted');
        $config4['total_rows'] = $total_rows4;
        $config4["uri_segment"] = 8;
        $pagination4->initialize($config4);
        if ($tab == 'tab4') {
            $page4 = ($this->uri->segment(8)) ? $this->uri->segment(8) : 0;
        } else {
            $page4 = 0;
        }
        $result_per_page4 = $pagination4->create_links();
        $this->set("result_per_page4", $result_per_page4);
        $this->set("page4", $page4);

        $active_requests = $this->payout_model->getPayoutWithdrawalDetails($user_id, 'pending', $config1['per_page'], $page1);
        $waiting_requests =   $this->payout_model->getReleasedWithdrawalDetails($user_id, 'approved_pending', $config2['per_page'], $page2);
        $paid_requests = $this->payout_model->getReleasedWithdrawalDetails($user_id, 'approved_paid', $config3['per_page'], $page3);
        $rejected_requests = $this->payout_model->getPayoutWithdrawalDetails($user_id, 'deleted', $config4['per_page'], $page4);

        $total_amount_active_request=$this->payout_model->getTotalAmountPendingRequest('pending',$user_id);
        $total_amount_waiting_requests=$this->payout_model->getTotalAmountApproved($user_id);
        $total_amount_paid_request=$this->payout_model->getTotalAmountPaid($user_id);
        $total_amount_rejected_requests=$this->payout_model->getTotalAmountRejected('deleted',$user_id);

        $this->set("base_url", $this->BASE_URL);

        $this->set("active_requests", $this->security->xss_clean($active_requests));
        $this->set("waiting_requests", $this->security->xss_clean($waiting_requests));
        $this->set("paid_requests", $this->security->xss_clean($paid_requests));
        $this->set("rejected_requests", $this->security->xss_clean($rejected_requests));
        $this->set('total_amount_active_request',$total_amount_active_request);
        $this->set('total_amount_waiting_requests',$total_amount_waiting_requests);
        $this->set('total_amount_paid_request',$total_amount_paid_request);
        $this->set('total_amount_rejected_requests',$total_amount_rejected_requests);

        $this->set('tab1', $tab1);
        $this->set('tab2', $tab2);
        $this->set('tab3', $tab3);
        $this->set('tab4', $tab4);

        $this->setView();
    }
    public function payout_released(){

        $title = lang('payout_released');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = 'payout_released';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('payout_released');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('payout_released');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $base_url = base_url() . 'user/payout/payout_released';
        $config = $this->pagination->customize_style();
        $config['base_url'] = $base_url;
        $config['per_page'] = $this->PAGINATION_PER_PAGE;
        $total_rows = $this->payout_model->getIncomeStatementCount($this->LOG_USER_ID);
        $config['total_rows'] = $total_rows;
        $config["uri_segment"] = 4;
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        
        
        $this->set("page", $page);

        $binary = $this->payout_model->getIncomeStatement($this->LOG_USER_ID, $page, $config['per_page']);
        
        
        
          
        $total_payout_released = $this->payout_model->getUserTotalPayouts($this->LOG_USER_ID);
        
        
        $this->set('total_payout_released',$total_payout_released);
        $this->set('count', $total_rows);
        $this->set('binary', $binary);

        $this->setView();
        }
        
    public function payout_invoice($paid_id)
    {
         $title = lang('Invoice');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = $title;
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = $title;
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

         $user_id=$this->payout_model->getPayoutReleasedUseridFromPaidID($paid_id);
          $user_details=$this->payout_model->getUserDeatilsForInvoice($user_id);
          $payout_details=$this->payout_model->getPayoutDetailsFromAmountPaid($paid_id);
          $invoice_number="PR000".$paid_id;
          $this->set('invoice_number',$invoice_number);
          $this->set('user_details',$user_details);
          $this->set('payout_details',$payout_details);

        $this->setView();

    }
}
