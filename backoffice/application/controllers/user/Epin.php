<?php

require_once 'Inf_Controller.php';

class Epin extends Inf_Controller
{

    function __construct() {
        parent::__construct();
        $this->url_permission('pin_status');
        $this->lang->load('epin', $this->LANG_NAME);
    }

    public function epin_search() {
        if ($this->input->is_ajax_request()) {
            $data = $this->epin_model->getEpinsByKeywordNew($this->input->get('term', TRUE),$this->LOG_USER_ID);
            echo json_encode($data);
            exit();
        }
    }
    // new design 
    function summary_total()
    {
        $epin_requests =  $this->epin_model->getAllEpinRequestsCountNew($this->LOG_USER_ID);
        $active_epins = $this->epin_model->activeEpins($this->LOG_USER_ID);
        $total['active_epins_count'] =  $active_epins->count;
        $total['active_epins'] =  thousands_currency_format($active_epins->amount);
        $total['epin_requests_total_amount'] = $epin_requests;
        echo json_encode($total);
         exit();   
    }
    
    /**
     * [index epin]
     * @return [type] [view]
     */
    function index() {
        $title = lang('new_epin');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);
        $help_link = 'e-pin-management';
        $this->set('help_link', $help_link);
        $this->HEADER_LANG['page_top_header'] = lang('my_e_pin');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('my_e_pin');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();
        
        // Epin balance
        $balamount = $this->epin_model->getBalanceAmount($this->LOG_USER_ID);
        
        // FILTER DATA
        $amount_details = $this->epin_model->getAllEwalletAmounts();
        $user_name      = $this->LOG_USER_NAME;//$this->input->get('user_name');
        $epin           = $this->input->get('epin');
        $amount         = $this->input->get('amount');
        $status         = $this->input->get('status') ?: "active";
        /*
            VALIDATION
         */
        /* Invalid Username validation */
        if(!empty($user_name)) {
            $user_id = $this->validation_model->userNameToID($user_name);
            if(!$user_id) {
                $msg = lang('invalid_username');
                $this->redirect($msg, 'user/my_epin'.get_previous_url_query_string()  , false);
            }
        }

        /* Invalid Epin Validation */
        if(!empty($epin)) {
            if(!$this->epin_model->isEpinExist($epin)) {
                $epin = "";
                $msg = lang('invalid_epin');
                $this->redirect($msg, 'user/my_epin'.get_previous_url_query_string()  , false);   
            }
        }
        // loged user epin 
        $epins = $this->epin_model->getUserEpinList($this->LOG_USER_ID);
        $this->set('epin_details', $epins);
        // DATA
        $epins = $this->epin_model->getEpins($user_name, $epin, $amount, $status, $this->PAGINATION_PER_PAGE, $this->input->get('offset'));
        foreach($epins as $key => $item) {
            if($status == "used_expired") {
                if($item['pin_balance_amount'] <= 0) {
                    $epins[$key]['status_name'] = "used";
                } elseif( $item['pin_expiry_date'] < date('Y-m-d') ) {
                    $epins[$key]['status_name'] = "expired";
                }
            } else {
                $epins[$key]['status_name'] = $status;
            }
        }
        $count = $this->epin_model->getEpinsCount($user_name, $epin, $amount, $status);
        $this->pagination->set_all('user/my_epin', $count);
      
        //balance amount loged user
        $this->set('balamount', $balamount);
        // DATA TO VIEW
        $this->set('amount_details', $amount_details);
        $this->set('user_name', $user_name);
        $this->set('epin', $epin);
        $this->set('amount', $amount);
        $this->set('epins', $epins);
        $this->set('status', $status);
        $this->set('active_epins', $this->epin_model->activeEpins($this->LOG_USER_ID));
        $this->set('epin_requests_count', $this->epin_model->getAllEpinRequestsCountNew($this->LOG_USER_ID));
        $this->set('amounts', $this->epin_model->getAllEpinAmounts());
        $this->setView('newui/user/epin/index');   
    }


        /**
     * [epin_purchase_validation description]
     * @return [type] [description]
     */
    public function epin_purchase_validation() {
        $this->lang->load('validation', $this->LANG_NAME);
        $this->form_validation->set_rules('passcode', lang('transaction_password'), 'trim|required|max_length[100]',[
           "required"=>lang('required'),
           "max_length"=>sprintf(lang('maxlength'),lang('transaction_password'),"100")
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
     * [epin_transfer_report]
     * @return [JSON] [datatable]
     */
    public function epin_transfer_report() {
        $columns = array( 
            0 => 'ft.user_name',
            2 => 'pin.pin_amount',
            3 => 'eth.date',
        );
        
        $filter = [
          'limit' => $this->input->get('length'),
          'start' => intval($this->input->get("start")),
          'order' => $columns[$this->input->get('order')[0]['column']],
          'direction' => $this->input->get('order')[0]['dir']
        ];
        $from_date = $this->input->get('start_date');
        $to_date = $this->input->get('end_date');

        $count            = $this->epin_model->getEpinTransferListCount($this->LOG_USER_ID, $from_date, $to_date);
        $transfer_details = $this->epin_model->getEpinTransferList($this->LOG_USER_ID, $filter, $from_date, $to_date);
        $data = [];
        foreach($transfer_details as $item) {
            $member_name = $item->from_user_id == $this->LOG_USER_ID ? $item->member_name . " ( ". $item->user_name . " )" : $item->member_name2 . " ( ". $item->user_name2 . " )";
            $data[] = [
                'member_name'      => $member_name,
                'epin'             => $item->pin_number,
                'transferred_date' => date("F j, Y, g:i a",strtotime($item->date)),
                'type'             => $item->from_user_id == $this->LOG_USER_ID ? lang('transferred') : lang('recieved'),
                'amount'           =>  "<span class='badge bg-amount'>".
                    format_currency($item->pin_amount).
                "</span>"
            ];
        }
        
        echo json_encode([
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data
        ]); die;
    }

    /**
     * [epin_purchase_post description]
     * @return [type] [description]
     */
    public function epin_purchase_post() {
        if ($this->input->post()) {
            $validated = $this->epin_purchase_validation();
            if ($validated['status']) {
                $post_arr = $this->input->post(null, true);
                $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
                $pin_count = $post_arr['pin_count'];
                $amount_id = $post_arr['amount'];
                $tran_pass = $post_arr['passcode'];
                $total_epin = $this->epin_model->getEpinsCount();
                // $epin_max_count = $this->epin_model->getMaxPinCount();
                //check the epin active count
                // if (($total_epin + $pin_count) > $epin_max_count) {
                //     echo json_encode([
                //         'status'     => 'failed',
                //         'error_type' => 'unknown',
                //         'message'    => lang('error_epin_count')
                //     ]);exit();
                // }
                $user_id = $this->LOG_USER_ID;
                $user_name = $this->validation_model->IdToUserName($user_id);
                $pass = $this->ewallet_model->getUserPassword($user_id);
                if (!password_verify($tran_pass, $pass)) {
                    echo json_encode([
                        'status'     => 'failed',
                        'error_type' => 'unknown',
                        'message'    => lang('invalid_transaction_password')
                    ]); die;
                }
                $kyc_status = $this->MODULE_STATUS['kyc_status'];
                if ($kyc_status == 'yes') {
                     $kyc_upload = $this->validation_model->checkKycUpload($user_id);
                     if ($kyc_upload != 'yes') {
                       echo json_encode([
                            'status'     => 'failed',
                            'error_type' => 'unknown',
                            'message'    => lang('KYC not verified')
                        ]); die;
                      }
                }
                if ($user_id != $this->ADMIN_USER_ID) {
                    //$balamount = $this->ewallet_model->getBalanceAmount($user_id);
                    $balamount = $this->epin_model->getBalanceAmount($user_id);
                    if ($pin_count > 0 && $amount_id != '' && is_numeric($pin_count)) {
                        // $amount = $this->ewallet_model->getEpinAmount($amount_id);
                        $amount = $this->epin_model->getEpinAmount($amount_id);
                        $tot_avb_amt = $amount * $pin_count;
                        if ($tot_avb_amt <= $balamount) {
                            $uploded_date = date('Y-m-d H:i:s');
                            // $expiry_date = date('Y-m-d', strtotime('+6 months', strtotime($uploded_date)));
                            $expiry_date    = $post_arr['expiry_date'];
                            $purchase_status = 'yes';
                            $status = 'yes';
                            $res = false;

                            // $max_active_pincount = $this->ewallet_model->getMaxPinCount();
                            $max_active_pincount = $this->epin_model->getMaxPinCount();
                            //$current_active_pin_count = $this->ewallet_model->getAllActivePinspage($purchase_status);
                            $current_active_pin_count = $this->epin_model->getAllActivePinspage($purchase_status);
                            
                                    $balance_count = $max_active_pincount - $current_active_pin_count;
                                    

                                        //$this->ewallet_model->begin();
                                        $this->epin_model->begin();
                                        //$transaction_id = $this->ewallet_model->getUniqueTransactionId();
                                        $transaction_id = $this->epin_model->getUniqueTransactionId();
                                        //$res = $this->ewallet_model->generatePasscode($pin_count, $status, $uploded_date, $amount, $expiry_date, $purchase_status, $amount_id, $user_id, $this->ADMIN_USER_ID, $transaction_id);
                                        $res = $this->epin_model->generatePasscode($pin_count, $status, $uploded_date, $amount, $expiry_date, $purchase_status, $amount_id, $user_id, $this->ADMIN_USER_ID, $transaction_id);
                                        if ($res) {
                                            $bal = round($balamount - $tot_avb_amt, 8);
                                            $update = $this->ewallet_model->updateBalanceAmount($user_id, $bal);
                                            if ($update) {
                                                $this->ewallet_model->commit();
                                                $data = serialize($post_arr);
                                                $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'epin purchased using ewallet', $this->LOG_USER_ID, $data);

                                                // Employee Activity History
                                                if ($this->LOG_USER_TYPE == 'employee') {
                                                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'purchase_epin_by_ewallet', 'EPIN Purchased Using E-Wallet By ' . $user_name);
                                                }
                                                //
                                                echo json_encode([
                                                    'status'     => 'success',
                                                    'message'    => lang('epin_purchased_successfully')
                                                ]);exit();
                                                
                                            } else {
                                                $this->ewallet_model->rollback();
                                                echo json_encode([
                                                    'status'     => 'failed',
                                                    'error_type' => 'unknown',
                                                    'message'    => lang('error_on_epin_purchase')
                                                ]); exit();
                                            }
                                        } else {
                                            $this->ewallet_model->rollback();
                                            echo json_encode([
                                                'status'     => 'failed',
                                                'error_type' => 'unknown',
                                                'message'    => lang('error_on_epin_purchase')
                                            ]); exit();
                                        }
                                    
                                
                        } else {
                            echo json_encode([
                                'status'     => 'failed',
                                'error_type' => 'unknown',
                                'message'    => lang('no_sufficient_balance_amount')
                            ]); exit();
                        }
            } 
            else {
                echo json_encode([
                    'status'     => 'failed',
                    'error_type' => 'unknown',
                    'message'    => lang('error_on_purchasing_epin_please_try_again')
                ]); exit();
            }
        }
    
    }
    else
    {
        echo json_encode($validated);
        exit();
    }
    }
    }
    /**
     * [create epin ajax]
     * @return [json]
     */
    public function reuest_epin_post() {
        $validated = $this->reuest_epin_validation();
        if ($validated['status']) {
            $request_date = date('Y-m-d H:i:s');
            $post_arr = $this->input->post(null, true);
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
            $user = $this->LOG_USER_ID;
            $user_name = $this->validation_model->IdToUserName($user);
            $total_epin = $this->epin_model->getEpinsCount();
            // $epin_max_count = $this->epin_model->getMaxPinCount();
            //check the epin active count
            // if (($total_epin + $post_arr['epin_count']) > $epin_max_count) {
            //     echo json_encode([
            //         'status'     => 'failed',
            //         'error_type' => 'unknown',
            //         'message'    => lang('error_epin_count')
            //     ]);exit();
            // }
            $kyc_status = $this->MODULE_STATUS['kyc_status'];
            if ($kyc_status == 'yes') {
                 $kyc_upload = $this->validation_model->checkKycUpload($user);
                 if ($kyc_upload != 'yes') {
                   echo json_encode([
                        'status'     => 'failed',
                        'error_type' => 'unknown',
                        'message'    => lang('KYC not verified')
                    ]); die;
                  }
            }
            $res = $this->epin_model->insertPinRequest($user, $post_arr['epin_count'], $request_date, $post_arr['expiry_date'], $post_arr['amount']);
            if ($res) {
                
                $data = serialize($post_arr);
                $loggin_id = $this->LOG_USER_ID;
                $admin_id = $this->ADMIN_USER_ID;
                $this->validation_model->insertUserActivity($loggin_id, 'epin requested', $admin_id);
                
                echo json_encode([
                    'status'  => 'success',
                    'message' => lang('pin_request_send_successfully')
                ]); exit();
            
            } else {
                echo json_encode([
                    'status'     => 'failed',
                    'error_type' => 'unknown',
                    'message'    => lang('error_on_pin_request')
                ]);exit();
            } 
        } else {
            echo json_encode($validated);
            exit();
        }
    }

    /**
     * [create_epin_validation description]
     * @return [type] [description]
     */
    protected function reuest_epin_validation() {
        $this->lang->load('validation', $this->LANG_NAME);
        $this->form_validation->set_rules('amount', lang('amount'), 'trim|required|greater_than[0]', [
            'required' => sprintf(lang('required'), 'amount'),
            'greater_than' => lang('greater_zero')
        ]);
        $this->form_validation->set_rules('epin_count', lang('epin_count'), 'trim|required|integer|greater_than[0]', [
            'required' => sprintf(lang('required'), lang('epin_count')),
            'integer' => lang('digits_only'),
            'greater_than' => lang('greater_zero')
        ]);
        $this->form_validation->set_rules('expiry_date', lang('date'), 'trim|required|valid_date|date_less_than_current_date', [
            'required' => sprintf(lang('required'), lang('date')),
            'valid_date' => lang('select_valid_date'),
            'date_less_than_current_date' => lang('valid_date')
        ]);
        
        if (!$this->form_validation->run()) {
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
     * [epin_transfer_post description]
     * @return [type] [description]
     */
    public function epin_transfer_post() {
        $this->load->model('mail_model');
        // dd('here');
        $validated=[];
        $validated = $this->epin_transfer_validation();
        if ($validated['status']) {

            $post_arr = $this->input->post(null, true);
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
            $user = strip_tags($this->validation_model->userNameToID($post_arr['to_user_name']));
            $from_user = $this->LOG_USER_ID;
            $verified_epin = $this->epin_model->CheckEpinBelongsTouser($from_user,$post_arr['epin']);
            if ($verified_epin) {

                $res = $this->epin_model->epinAllocation($user, $post_arr['epin']);
            } else {
                 echo json_encode([
                    'status'     => 'success',
                    'message'    => lang('error_please_try_again')
                ]);die;
            }

            if ($res) {
                error_reporting(E_ALL);
ini_set('display_errors', '1');
                $data = serialize($post_arr);
                $this->validation_model->insertUserActivity($from_user, 'Epin transferred', $user, $data);
                // Epin Transfer History
                $this->epin_model->insertEpinTransferHistory($from_user,'Epin transferred', $user, $from_user, $post_arr['epin'], $data);
                
                $mail = array();
                $user_id = $this->LOG_USER_ID;
                $mail['full_name'] = $this->validation_model->getUserFullName($user_id);
                $mail['email'] = $this->validation_model->getUserEmailId($user_id);
                $mail['first_name'] = $this->validation_model->getUserData($user_id, "user_detail_name");
                $mail['last_name'] = $this->validation_model->getUserData($user_id, "user_detail_second_name");
                $mail['to_user_name'] = $post_arr['to_user_name'];
                
                $this->mail_model->sendAllEmails('epin_transfer',$mail);
                
                $mail = array();
                $user_id = $user;
                $mail['full_name'] = $this->validation_model->getUserFullName($user_id);
                $mail['email'] = $this->validation_model->getUserEmailId($user_id);
                $mail['first_name'] = $this->validation_model->getUserData($user_id, "user_detail_name");
                $mail['last_name'] = $this->validation_model->getUserData($user_id, "user_detail_second_name");
                $mail['to_user_name'] = $post_arr['to_user_name'];
                $this->mail_model->sendAllEmails('epin_received',$mail);
                
                echo json_encode([
                    'status'     => 'success',
                    'message'    => lang('epin_transferred_successfully')
                ]);die;
            
            } else {
                echo json_encode([
                    'status'     => 'failed',
                    'error_type' => 'unknown',
                    'message'    => lang('error_please_try_again')
                ]);die;
            }
        } else {
            echo json_encode($validated);
            exit();
        }
    }

      /**
     * [epin_transfer_validation description]
     * @return [type] [description]
     */
    public function epin_transfer_validation() {
        $this->lang->load('validation', $this->LANG_NAME);
        $this->form_validation->set_rules('epin', lang('epin'), 'trim|required|callback_valid_epin',[
            "requuired" => lang('required'),
            "valid_epin" => sprintf(lang('invalid'),lang('epin'))
        ]);
        
        $this->form_validation->set_rules('to_user_name', lang('to_user_name'), 'trim|required|callback_valid_user|callback_check_match[' . $this->input->post('from_user_name') . ']',[
            "required"=>lang('required'),
            "valid_user"=>sprintf(lang('invalid'),lang('to_user_name'))
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
     /**
     * [epin_requests ajax datatable requests]
     * @return [json] [datatable json]
     */
    public function epin_requests() {
        $columns = array( 
            0 => 'req_date',
            1 => 'pin_expiry_date',
            2 =>'req_pin_count',
            3 => 'req_rec_pin_count',
            4 => 'pin_amount'
            
        );
        
        $filter = [
          'limit' => $this->input->get('length'),
          'start' => intval($this->input->get("start")),
          'order' => $columns[$this->input->get('order')[0]['column']],
          'direction' => $this->input->get('order')[0]['dir']
        ];
        
        $user_ids = $this->LOG_USER_ID;
        $count = $this->epin_model->getAllEpinRequestsCountNew($user_ids);
        $epin_requests = $this->epin_model->getAllEpinRequestsNew($filter, $user_ids, $this->PAGINATION_PER_PAGE, $this->input->get('offset'));
        
        $data = [];
        foreach($epin_requests as $request) {
            $profile_image = profile_image_path($request['user_photo']);
            $data[] = [
                'requested_pin_count' => $request['req_pin_count'],
                'pin_count' =>  $request['req_rec_pin_count'],
                'amount' => format_currency($request['pin_amount']),
                'requested_date' => date("F j, Y",strtotime($request['req_date'])),
                'expiry_date' => date("F j, Y",strtotime($request['pin_expiry_date']))
            ];
        }
        
        echo json_encode([
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data
        ]); die;
    }
    function request_epin() {
        $title = lang('request_e_pin');
        $this->set("title", $this->COMPANY_NAME . " | $title ");

        $help_link = "request-pin";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('request_e_pin');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('request_e_pin');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $pro_status = $this->MODULE_STATUS['product_status'];
        $amount_details = $this->epin_model->getAllEwalletAmounts();
        $this->set("amount_details", $amount_details);

        $success = lang('pin_request_send_successfully');
        $error = lang('error_on_pin_request');
        if ($this->input->post('reqpasscode') && $this->validate_request_epin()) {
            $request_date = date('Y-m-d H:i:s');
            $post_arr = $this->input->post(NULL, TRUE);
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
            $cnt = $post_arr['count'];
            $pin_amount = $post_arr['amount1'];
            // $expiry_date = date('Y-m-d', strtotime('+6 months'));  //pin valid for 6 months
            $expiry_date = $post_arr['date'];
            $req_user = $this->LOG_USER_ID;
            $res = $this->epin_model->insertPinRequest($req_user, $cnt, $request_date, $expiry_date, $pin_amount);
            if ($res) {
                $loggin_id = $this->LOG_USER_ID;
                $admin_id = $this->ADMIN_USER_ID;
                $this->validation_model->insertUserActivity($loggin_id, 'epin requested', $admin_id);
                $this->redirect($success, "epin/request_epin", TRUE);
            } else {
                $this->redirect($error, "epin/request_epin", FALSE);
            }
        }
        if ($pro_status == "yes") {
            $produc_details = $this->epin_model->getAllProducts('yes');
            $this->set("produc_details", $produc_details);
        }
        $this->set("pro_status", $pro_status);

        $this->setView();
    }

    function validate_request_epin()
    {
        $this->form_validation->set_rules('amount1', lang('amount'), 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('count', lang('count'), 'trim|required|integer|greater_than[0]|max_length[5]');
        $this->form_validation->set_rules('date', lang('date'), 'trim|required|valid_date|date_less_than_current_date', [
            'required' => sprintf(lang('required'), lang('date')),
            'valid_date' => lang('select_valid_date'),
            'date_less_than_current_date' => lang('valid_date')
        ]);

        $validate_form = $this->form_validation->run();
        return $validate_form;
    }
     public function epin_list() {
        $columns = array( 
            0 => '',
            1 =>'user_detail_name', 
            2 =>'pin_numbers',
            3 => 'pin_amount',
            4 => 'pin_balance_amount',
            5 => 'status',
            6 => 'pin_expiry_date'
        );
        
        $filter = [
          'limit' => $this->input->get('length'),
          'start' => intval($this->input->get("start")),
          'order' => $columns[$this->input->get('order')[0]['column']],
          'direction' => $this->input->get('order')[0]['dir']
        ];
        
        $user_names = $this->LOG_USER_NAME;
        $epins = $this->input->get('epins') ?: [];
        $amounts = $this->input->get('amounts') ?: [];
        $status = $this->input->get('status') ?: "active";
        $user_ids = $this->validation_model->userNamesToIDs($user_names);
        $data = []; 
        $count = $this->epin_model->countEpinList($user_ids, $epins, $amounts, $status);
        $epins = $this->epin_model->epinList($filter, $user_ids, $epins, $amounts, $status);

        foreach($epins as $epin) {
                
            $status_name = "";
            $refund = lang('na');
            if($epin['status'] == "yes" && ($epin['pin_expiry_date'] >= date('Y-m-d')) && ($epin['pin_balance_amount'] > 0)  ) {
                // if($epin['purchase_status'] == "yes")
                // {
                    $refund = lang('refund');
                //}
                
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
            $profile_image = profile_image_path($epin['user_photo']);

            $data[] = [
                
                'epin_id' => $epin['pin_id'],
                'full_name' => $epin['full_name'],
                'user_name' => $epin['user_name'],
                'profile_image' => $profile_image,
                'pin_number' => $epin['pin_numbers'],
                'amount' => format_currency($epin['pin_amount']),
                'balance_amount' => format_currency($epin['pin_balance_amount']),
                'status' => lang($status_name),
                'expiry_date' => date("F j, Y",strtotime($epin['pin_expiry_date'])),
                'refund' => $refund,
            ];
        }
        
        echo json_encode([
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data
        ]); die;
    }
    public function my_epin($page = "", $limit = ""){

        // HEADER DATA
        $title = lang('new_epin');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);
        $help_link = 'e-pin-management';
        $this->set('help_link', $help_link);
        $this->HEADER_LANG['page_top_header'] = lang('my_e_pin');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('my_e_pin');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();

        // FILTER DATA
        $amount_details = $this->epin_model->getAllEwalletAmounts();
        $user_name      = $this->LOG_USER_NAME;//$this->input->get('user_name');
        $epin           = $this->input->get('epin');
        $amount         = $this->input->get('amount');
        $status         = $this->input->get('status') ?: "active";
        /*
            VALIDATION
         */
        /* Invalid Username validation */
        if(!empty($user_name)) {
            $user_id = $this->validation_model->userNameToID($user_name);
            if(!$user_id) {
                $msg = lang('invalid_username');
                $this->redirect($msg, 'user/my_epin'.get_previous_url_query_string()  , false);
            }
        }

        /* Invalid Epin Validation */
        if(!empty($epin)) {
            if(!$this->epin_model->isEpinExist($epin)) {
                $epin = "";
                $msg = lang('invalid_epin');
                $this->redirect($msg, 'user/my_epin'.get_previous_url_query_string()  , false);   
            }
        }
        // DATA
        $epins = $this->epin_model->getEpins($user_name, $epin, $amount, $status, $this->PAGINATION_PER_PAGE, $this->input->get('offset'));
        foreach($epins as $key => $item) {
            if($status == "used_expired") {
                if($item['pin_balance_amount'] <= 0) {
                    $epins[$key]['status_name'] = "used";
                } elseif( $item['pin_expiry_date'] < date('Y-m-d') ) {
                    $epins[$key]['status_name'] = "expired";
                }
            } else {
                $epins[$key]['status_name'] = $status;
            }
        }
        $count = $this->epin_model->getEpinsCount($user_name, $epin, $amount, $status);
        $this->pagination->set_all('user/my_epin', $count);
      
        
        // DATA TO VIEW
        $this->set('amount_details', $amount_details);
        $this->set('user_name', $user_name);
        $this->set('epin', $epin);
        $this->set('amount', $amount);
        $this->set('epins', $epins);
        $this->set('status', $status);
        $this->setView();
        return false;

    }

    function epin_transfer()
    {
        $title = lang('epin_transfer');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $help_link = 'allocate-pin-to-user';
        $this->set('help_link', $help_link);
        $login_id = $this->LOG_USER_ID;

        $this->HEADER_LANG['page_top_header'] = lang('epin_transfer');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('epin_transfer');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $pin_details = $this->epin_model->getEpinList($login_id);
        if ($this->input->post('transfer') && $this->validate_epin_transfer()) {
            $post_arr = $this->input->post(NULL, TRUE);
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
            $user = strip_tags($this->validation_model->userNameToID($post_arr['user_name']));

            $verified_epin = $this->epin_model->CheckEpinBelongsTouser($login_id, $post_arr['epin']);
            if ($verified_epin) {
                $res = $this->epin_model->epinAllocation($user, $post_arr['epin']);
            } else {
                $res = FALSE;
            }

            if ($res) {
                $user_type = $this->LOG_USER_TYPE;
                if ($user_type == 'employee') {
                    $login_id = $this->validation_model->getAdminId();
                }
                $data = serialize($post_arr);
                $this->validation_model->insertUserActivity($login_id, 'Epin transferred', $user, $data);

                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $user, 'transfer_epin', 'EPIN transferred');
                }
                //
                // Epin Transfer History
                $this->epin_model->insertEpinTransferHistory($login_id, 'Epin transferred', $user, $login_id, $post_arr['epin'], $data);

                $msg = lang('epin_transferred_successfully');
                $this->redirect($msg, 'epin/epin_transfer', TRUE);
            } else {
                $msg = lang('error_on_epin_allocation');
                $this->redirect($msg, 'epin/epin_transfer', FALSE);
            }
        }

        $config = $this->pagination->customize_style();
        $base_url = base_url() . "user/epin/epin_transfer";
        $config['base_url'] = $base_url;
        $config['per_page'] = $this->PAGINATION_PER_PAGE;
        $config['uri_segment'] = 4;
        $config['num_links'] = 5;
        $page = 0;
        if ($this->uri->segment(4) != "") {
            $page = $this->uri->segment(4);
        }

        $pin_detailss = $this->epin_model->pinSelector($page, $config['per_page'], "defualt");
        $pin_count = $pin_detailss["numrows"];
        $config['total_rows'] = $pin_count;

        if (($this->MODULE_STATUS['pin_status'] == "yes") && ($this->epin_model->getUserPinRequestCount($this->LOG_USER_ID, 'no', 1) > 0)) {
            $this->epin_model->setEpinViewed(0); //status 1 for admin read
            $this->set_header_notification_box();
        }
        $this->set('start_id', $page);
        $this->pagination->initialize($config);
        $page_footer = $this->pagination->create_links();
        $pin_numbers = $pin_detailss["pin_numbers"];

        $this->set("pin_numbers", $pin_numbers);
        $this->set("page_footer", $page_footer);
        $this->set('epin_details', $pin_details);
        $this->setView();
    }

    function validate_epin_transfer()
    {
        $this->form_validation->set_rules('epin', lang('epin'), 'trim|required');
        $this->form_validation->set_rules('user_name', lang('user_name'), 'trim|required|callback_valid_user|callback_check_match');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function valid_user($user_name)
    {
        $user_id = $this->validation_model->userNameToID($user_name);
        if (!$user_id) {
            $this->form_validation->set_message('valid_user', lang('invalid_username'));
            return FALSE;
        }
        return TRUE;
    }

    function check_match($user_name)
    {
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

    function refund_epin($action = '', $delete_id = '')
    {
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
                echo json_encode([
                    'status'     => 'success',
                    'message'    => lang('epin_refunded_successfully')
                ]);exit();
                $msg = lang('error_on_refunding_epin');
                $this->redirect($msg, 'epin/my_epin', TRUE);
            } else {
                $msg = lang('error_on_refunding_epin');
                $this->redirect($msg, 'epin/my_epin', FALSE);
            }
        }
    }
    function valid_epin($epin)
    {
        if ($epin == 'default') {
            $this->form_validation->set_message('valid_epin', lang('select_epin'));
            return false;
        }
        return true;
    }
     public function validate_username() {
        if($this->valid_user($this->input->post('username'))) {
            echo "yes";
            return true;
        }
        echo "no";
        return false;

    }

}
