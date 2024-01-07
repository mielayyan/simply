<?php

require_once 'Inf_Controller.php';

class Epin extends Inf_Controller
{

    function __construct() {
        parent::__construct();
        $this->url_permission('pin_status');
        $this->set_public_variables();
    }
    
    /**
     * [index epin menu with tiles and and tabs(Epin List and Epin Requests)]
     * @return [view] [newui/admin/epin/index.tpl]
     */
    public function index() {
        $this->set('title', $this->COMPANY_NAME . ' | ' . lang('epin'));
        $this->load_langauge_scripts();

        if ($this->input->get('tab') == "requests") {
            $this->epin_model->setEpinViewed(1);
            $this->set_header_notification_box();
        }

        $active_user_name = $this->validation_model->isUsernameExists($this->input->get('user_name')) ? $this->input->get('user_name') : '';

        $this->set('active_epins', $this->epin_model->activeEpins());
        $this->set('epin_requests_count', $this->epin_model->getAllEpinRequestsCountNew());
        $this->set('amounts', $this->epin_model->getAllEpinAmounts());
        $this->set('active_user_name', $active_user_name);
        $this->setView('newui/admin/epin/index');
    }

    /**
     * [epin_list datatable json request for all epin list]
     * @return [type] [description]
     */
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
        
        $user_names = $this->input->get('user_names');
        $epins = $this->input->get('epins') ?: [];
        $amounts = $this->input->get('amounts') ?: [];
        $status = $this->input->get('status') ?: "active";
        $user_ids = $this->validation_model->userNamesToIDs($user_names);
        $data = []; 
        $count = $this->epin_model->countEpinList($user_ids, $epins, $amounts, $status);
        $epins = $this->epin_model->epinList($filter, $user_ids, $epins, $amounts, $status);

        foreach($epins as $epin) {
                
            $status_name = "";
            $extend='';
            if($epin['status'] == "yes" && ($epin['pin_expiry_date'] >= date('Y-m-d')) && ($epin['pin_balance_amount'] > 0)) {
                $status_name = "active";
            }
            
            if($epin['status'] == "no" && $epin['pin_balance_amount'] > 0) {
                $status_name = "blocked";
            }elseif ($epin['status'] == "no" && $epin['pin_balance_amount'] == 0) {
                $status_name = "used";
            }
            
            if($epin['status'] == "yes" && (($epin['pin_balance_amount'] <= 0) || $epin['pin_expiry_date'] < date('Y-m-d'))) {
                $status_name = "expired";
                $extend='<form action="'.SITE_URL .'/backoffice/admin/epin/extend_pin" method="get"><input type="number" min=0 name="extend_days" id="extend_days" placeholder="days" value="10" style="width: 50px;padding: 5px;"><input type="hidden"name="id" id="epin_id" placeholder="days" value="'.$epin['pin_id'].'"><button type="submit" class="btn btn-danger">Extend days</button></form>';
            }
            
            if($epin['status'] == "delete") {
                $status_name = "deleted";
            }
            $profile_image = ($epin['user_photo'])?profile_image_path($epin['user_photo']):profile_image_path('default.jpg');
            $full_name = ($epin['full_name'])? $epin['full_name']: lang('all_members');
            $user_name = ($epin['user_name'])? $epin['user_name']: '';
            $data[] = [
                'epin_id' => $epin['pin_id'],
                'full_name' => $full_name,
                'user_name' => $user_name,
                'profile_image' => $profile_image,
                'pin_number' => $epin['pin_numbers'],
                'amount' => format_currency($epin['pin_amount']),
                'balance_amount' => format_currency($epin['pin_balance_amount']),
                'status' => lang($status_name),
                'expiry_date' => date("F j, Y",strtotime($epin['pin_expiry_date'])),
                'extend' =>$extend
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
     * [epin_requests ajax datatable requests]
     * @return [json] [datatable json]
     */
    public function epin_requests() {
        $columns = array( 
            0 => 'req_id',
            1 =>'user_detail_name', 
            2 =>'req_pin_count',
            3 => 'req_rec_pin_count',
            4 => 'pin_amount',
            5 => 'req_date',
            6 => 'pin_expiry_date'
        );
        
        $filter = [
          'limit' => $this->input->get('length'),
          'start' => intval($this->input->get("start")),
          'order' => $columns[$this->input->get('order')[0]['column']],
          'direction' => $this->input->get('order')[0]['dir']
        ];
        
        $user_names = $this->input->get('user_names');
        $user_ids = $this->validation_model->userNamesToIDs($user_names);
        $count = $this->epin_model->getAllEpinRequestsCountNew($user_ids);
        $epin_requests = $this->epin_model->getAllEpinRequestsNew($filter, $user_ids, $this->PAGINATION_PER_PAGE, $this->input->get('offset'));
        
        $data = [];
        foreach($epin_requests as $request) {
            $profile_image = profile_image_path($request['user_photo']);
            $data[] = [
                'epin_id' => $request['req_id'],
                'full_name' => $request['full_name'],
                'user_name' => $request['user_name'],
                'profile_image' => $profile_image,
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

    /**
     * [epin_search ajax requests]
     * @return [json] [json epins]
     */
    public function epin_search() {
        if ($this->input->is_ajax_request()) {
            $data = $this->epin_model->getEpinsByKeywordNew($this->input->get('term', TRUE));
            echo json_encode($data);
            exit();
        }
    }


    // Actions
    /**
     * [create epin ajax]
     * @return [json]
     */
    public function create_epin_post() {
        $validated = $this->create_epin_validation();
        if ($validated['status']) {
            $post_arr = $this->input->post(null, true);
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
            $user = '';
            if($post_arr['user_name']) {
                $user = strip_tags($this->validation_model->userNameToID($post_arr['user_name']));
            }
            $kyc_status = $this->MODULE_STATUS['kyc_status'];
            // if ($kyc_status == 'yes') {
            //      $kyc_upload = $this->validation_model->checkKycUpload($user);
            //      if ($kyc_upload != 'yes') {
            //       echo json_encode([
            //             'status'     => 'failed',
            //             'error_type' => 'unknown',
            //             'message'    => lang('KYC not verified')
            //         ]); die();
            //       }
            // }
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

            $res = $this->epin_model->generateEpinNew($post_arr['user_name'], $post_arr['amount'], $post_arr['epin_count'], $post_arr['expiry_date']);
            if ($res) {
                $login_id = $this->LOG_USER_ID;
                $user_type = $this->LOG_USER_TYPE;
                if ($user_type == 'employee') {
                    $login_id = $this->validation_model->getAdminId();
                }
                $data = serialize($post_arr);
                $this->validation_model->insertUserActivity($login_id, 'epin allocated', $user, $data);
                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $user, 'allocate_epin', 'EPIN Allocated');
                }
                if($this->LOG_USER_TYPE == 'agent'){
                    $this->validation_model->insertAgentActivity($this->LOG_USER_ID, $user, 'allocate_epin', 'EPIN Allocated');
                }
                echo json_encode([
                    'status'  => 'success',
                    'message' => lang('epin_allocated_successfully')
                ]); exit();
            
            } else {
                echo json_encode([
                    'status'     => 'failed',
                    'error_type' => 'unknown',
                    'message'    => lang('error_on_epin_allocation')
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
    protected function create_epin_validation() {
        $this->lang->load('validation', $this->LANG_NAME);
        $this->form_validation->set_rules('user_name', lang('user_name'), 'trim|callback_valid_user', [
            'required' => sprintf(lang('required'), lang('user_name')),
            'valid_user' => lang('invalid_username')
        ]);
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
     * [user_epin_list description]
     * @return [type] [description]
     */
    public function user_epin_list() {
        $this->lang->load('validation');
        $this->form_validation->set_rules('from_user_name', lang('from_user_name'), 'trim|required|callback_valid_user',[
            "required"=>lang('required'),
            'valid_user'=>sprintf(lang('invalid'),lang('from_user_name'))
        ]);

        if (!$this->form_validation->run()) {
            echo json_encode([
                'status' => false,
                'validation_error' => $this->form_validation->error_array(),
                'message' => lang('errors_check')
            ]); die;        
        }
        
        $epins = $this->epin_model->getUserEpinList($this->validation_model->userNameToID($this->input->post('from_user_name')));
        if(!empty($epins)) {
                echo json_encode([
                'status'     => 'success',
                'message'    => '',
                'data'       => $epins, 
            ]);die;    
        } else {
            echo json_encode([
                'status' => false,
                'message' => lang('this_user_has_not_found_any_epins'),
                'validation_error' => [
                    'from_user_name'   => lang('this_user_has_not_found_any_epins')
                ],
            ]); die;    
        }
    }

    /**
     * [epin_transfer_post description]
     * @return [type] [description]
     */
    public function epin_transfer_post() {
        // dd('here');
        $validated=[];
        $validated = $this->epin_transfer_validation();
        if ($validated['status']) {
            $post_arr = $this->input->post(null, true);
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
            $user = strip_tags($this->validation_model->userNameToID($post_arr['to_user_name']));
            $from_user = strip_tags($this->validation_model->userNameToID($post_arr['from_user_name']));
            if ($this->epin_model->epinAllocationNew($user, $post_arr['epin'])) {
                $login_id = $this->LOG_USER_ID;
                if ($this->LOG_USER_TYPE == 'employee') {
                    $login_id = $this->validation_model->getAdminId();
                }
                $data = serialize($post_arr);
                $this->validation_model->insertUserActivity($user, 'Epin transferred to ' . $post_arr['to_user_name'], $this->LOG_USER_ID, $data);

                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'transfer_epin', 'EPIN transferred to ' . $post_arr['to_user_name']);
                }
                // Epin Transfer History
                $this->epin_model->insertEpinTransferHistory($login_id, 'Epin transferred', $user, $from_user, $post_arr['epin'], $data);
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
     * [allocate_epin description]
     * @return [type] [description]
     */
    public function allocate_epin() {
        if(!empty($this->input->post('epins'))) {
            foreach($this->input->post('epins') as $epin) {
                if($epin['count'] <= 0) {
                    echo json_encode([
                       'status'     => 'failed',
                       'error_type' => 'unknown',
                       'message'    => lang('you_must_enter_a_positive_value')
                    ]);die;
                    break;
                }
            
                $total_epin = $this->epin_model->getEpinsCount();
                // $epin_max_count = $this->epin_model->getMaxPinCount();
                //check the epin active count
                // if (($total_epin) > $epin_max_count) {
                //     echo json_encode([
                //         'status'     => 'failed',
                //         'error_type' => 'unknown',
                //         'message'    => lang('error_epin_count')
                //     ]);exit();
                // }
                $epin_request = $this->epin_model->getEpinRequestAllocatedUserId($epin['pin_id']);
                if($epin_request->req_rec_pin_count < $epin['count']) {
                    echo json_encode([
                       'status'     => 'failed',
                       'error_type' => 'unknown',
                       'message'    => lang('epin_count_should_less_req_count')
                    ]); die;
                    break;   
                }
            }

            $pin_numbers = [];
            $pin_requests = [];
            foreach($this->input->post('epins') as $key => $epin) {
                $epin_request = $this->epin_model->getEpinRequestAllocatedUserId($epin['pin_id']);
                for($i = 0; $i < $epin['count']; $i++) {
                    $pin_numbers[] = [
                        'pin_numbers'        => $this->misc_model->getRandStr(9, 9),
                        'pin_alloc_date'     => date('Y-m-d H:i:s'),
                        'status'             => 'yes',
                        'used_user'          => '',
                        'pin_uploded_date'   => date('Y-m-d H:i:s'),
                        'generated_user_id'  => $this->getAdminID(),
                        'allocated_user_id'  => $epin_request->req_user_id,
                        'pin_expiry_date'    => $epin_request->pin_expiry_date,
                        'pin_amount'         => $epin_request->pin_amount,
                        'pin_balance_amount' => $epin_request->pin_amount
                     ];
                }
                if($epin['count'] == $epin_request->req_pin_count) {
                    $pin_requests[] = [
                        'req_id' => $epin['pin_id'],
                        'status' => 'no'
                    ];
                } else {
                    $pin_requests[] = [
                        'req_id'        => $epin['pin_id'],
                        'req_pin_count' => $epin_request->req_pin_count - $epin['count'],
                        'req_rec_pin_count'     => $epin_request->req_pin_count - $epin['count'],
                    ];
                }
            }

            if($this->epin_model->allocateEpinRequests($pin_numbers, $pin_requests)) {
                $data = serialize($this->input->post());
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'epin requests granted', $this->LOG_USER_ID, $data);
                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'grant_epin_request', 'EPIN Request Granted');
                }
                echo json_encode([
                   'status'     => 'success',
                   'error_type' => 'unknown',
                   'message'    => lang('epin_allocated_successfully')
                ]); die;
            }
            echo json_encode([
               'status'     => 'failed',
               'error_type' => 'unknown',
               'message'    => lang('error_on_epin_allocation')
            ]); die;
        }
        echo json_encode([
           'status' => 'failed',
           'error_type' => 'validation',
           'message' => lang('please_select_checkbox')
        ]); die;
    }

    /**
     * [delete_epin requests]
     * @return [json] []
     */
    public function delete_epin_requests() {
        if ($this->epin_model->deleteAllRequestedEpin($this->input->post('epins'), "remark deleted")) {
            echo json_encode([
               'status' => 'success',
               'error_type' => 'validation',
               'message' => lang('requested_epin_deleted_sucessfully')
            ]); die;
        } else {
            echo json_encode([
               'status' => 'failed',
               'error_type' => 'validation',
               'message' => lang('error_on_requested_epin_deletion')
            ]); die;
        }
    }
 
    // new design 
    function summary_total()
    {
        $epin_requests =  $this->epin_model->epinRequests();
        $active_epins = $this->epin_model->activeEpins();
        $total['active_epins_count'] =  $active_epins->count;
        $total['active_epins'] =  thousands_currency_format($active_epins->amount);
        $total['epin_requests_total_amount'] = (isset($epin_requests->count) ? $epin_requests->count : 0) *  (isset($epin_requests->pin_amount) ? $epin_requests->pin_amount : 0);
        echo json_encode($total);
         exit();   
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
        
        $this->form_validation->set_rules('from_user_name', lang('from_user_name'), 'trim|required|callback_valid_user',[
            "required"=>lang('required'),
            'valid_user'=>sprintf(lang('invalid'),lang('from_user_name'))
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
     * [epin_purchase_validation description]
     * @return [type] [description]
     */
    public function epin_purchase_validation() {
        $this->lang->load('validation', $this->LANG_NAME);
        $this->form_validation->set_rules('user_name', lang('user_name'), 'trim|required|callback_valid_user', [
            'required' => lang('required'),
            'valid_user' => lang('invalid_username')
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
     * [epin_purchase_post description]
     * @return [type] [description]
     */
    public function epin_purchase_post() {
        if ($this->input->post()) {
            $validated = $this->epin_purchase_validation();
            if ($validated['status']) {
                $post_arr = $this->input->post(null, true);
                $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
                $user_name = $post_arr['user_name'];
                $pin_count = $post_arr['pin_count'];
                $amount_id = $post_arr['amount'];

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
                $user_id = $this->validation_model->userNameToId($user_name);
                
                if ($user_id == $this->ADMIN_USER_ID) {
                    echo json_encode([
                        'status'     => 'failed',
                        'error_type' => 'unknown',
                        'message'    => lang('you_cant_use_admin_account')
                    ]); die;
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
                                    if ($current_active_pin_count < $max_active_pincount) {
                                        $balance_count = $max_active_pincount - $current_active_pin_count;
                                        if ($pin_count <= $balance_count) {
    
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
                                                'message'    => sprintf(lang('only_few_epins_can_be_generated'), $balance_count)
                                            ]); exit();
                                        }
                                    } else {
                                        echo json_encode([
                                            'status'     => 'failed',
                                            'error_type' => 'unknown',
                                            'message'    => lang('already').$current_active_pin_count.lang('epin_present')
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
     * [delete_epin description]
     * @return [type] [description]
     */
    public function delete_epin() {
        if(!empty($this->input->post('epins'))) {
            $result = false;
            foreach($this->input->post('epins') as $epin) {
                $result = $this->epin_model->deleteEPin($epin);
                if ($result) {
                    $data_array['delete_id'] = $epin;
                    $data = serialize($data_array);
                    $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'epin deleted', $this->LOG_USER_ID, $data);
                    if ($this->LOG_USER_TYPE == 'employee') {
                        $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'epin deleted', 'EPIN Deleted');
                    }
                }
            }
            if($result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => lang('epin_deleted_successfully') 
                ]); die;
            }
            echo json_encode([
                'status' => 'error',
                'error_type' => 'unknown',
                'message' => lang('error_on_deleting_epin') 
            ]); die;
            
        } 
        echo json_encode([
           'status' => 'failed',
           'error_type' => 'validation',
           'message' => lang('please_select_checkbox')
        ]); 
    }

    public function block_epin() {
        if(!empty($this->input->post('epins'))) {
            if ($this->input->post('status') == 'blocked') {
                $result = $this->epin_model->activateEpins($this->input->post('epins'));
            }else{
                $result = $this->epin_model->deactivateEPins($this->input->post('epins'));
            }
            if($result) {
                $data = serialize(['epin_ids' => $this->input->post('epins')]);
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'epin deactivated', $this->LOG_USER_ID, $data);
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'epin deactivated', 'EPIN Deleted');
                }
                if ($this->input->post('status') == 'blocked') {
                    echo json_encode([
                        'status' => 'success',
                        'message' => lang('epin_activated_successfully') 
                    ]); die;
                }else{
                    echo json_encode([
                        'status' => 'success',
                        'message' => lang('epin_deactivated_successfully') 
                    ]); die;
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'error_type' => 'unknown',
                    'message' => lang('error_on_delactivating_epin') 
                ]); die;
            }
        } else {
            echo json_encode([
               'status' => 'failed',
               'error_type' => 'validation',
               'message' => lang('please_select_checkbox')
            ]); 
        }
    }

    function search_pin()
    {
        $pin_amount = '';
        if ($this->session->has_userdata('epin_amount') && $this->session->userdata('epin_search_type')) {
            $pin_amount = $this->session->userdata('epin_amount');
        }
        if ($this->uri->segment(4) != "") {
            $page = $this->uri->segment(4);
        } else {
            $page = 0;
        }
        $this->redirect("", "epin/search_epin/$page/?key=$pin_amount", false);
    }

    function view_epin_request() {
        // HEADER DATA
        $title = lang('epin_request');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);
        $help_link = 'view-pin-request';
        $this->set('help_link', $help_link);
        $this->HEADER_LANG['page_top_header'] = lang('epin_request');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('epin_request');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();

        // FILTER
        $user_name = $this->input->get('user_name') ?: '';
        $user_id = $this->validation_model->userNameToID($user_name);
        if ($user_name != "" && empty($user_id)) {
            $msg = lang('invalid_username');
            $this->redirect($msg, 'admin/view_epin_request', false);
        }

        // DATA
        $epin_requests = $this->epin_model->getAllEpinRequests($user_name, $this->PAGINATION_PER_PAGE, $this->input->get('offset'));


        // Pagination
        $count = $this->epin_model->getAllEpinRequestsCount($user_name);
        $this->pagination->set_all('admin/view_epin_request', $count);
        
        // SET DATA TO VIEW
        $this->set('user_name', $user_name);
        $this->set('epin_requests', $epin_requests);
        $this->setView();
    }

    function allocate_pin_user() {
        $title = lang('epin_allocation');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $help_link = 'allocate-pin-to-user';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('epin_allocation');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('epin_allocation');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();
        $amount_details = $this->epin_model->getAllEwalletAmounts();
        if ($this->input->post('insert') && $this->validate_allocate_pin_user()) {
            // $epin_list_count = $this->epin_model->getEpinsCount();
            // $epin_max_count = $this->epin_model->getMaxPinCount();
            // if ($epin_list_count > $epin_max_count) {
            //     $msg = lang('error_epin_count');
            //     $this->redirect($msg, 'epin/allocate_pin_user', false);
            // }
            $post_arr = $this->input->post(null, true);
            $date = $post_arr['date'];
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
            $user = strip_tags($this->validation_model->userNameToID($post_arr['user_name']));
            $res = $this->epin_model->generateEpin($post_arr['user_name'], $post_arr['amount1'], $post_arr['count'], $post_arr['date']);
            if ($res) {
                $login_id = $this->LOG_USER_ID;
                $user_type = $this->LOG_USER_TYPE;
                if ($user_type == 'employee') {
                    $login_id = $this->validation_model->getAdminId();
                }
                $data = serialize($post_arr);
                $this->validation_model->insertUserActivity($login_id, 'epin allocated', $user, $data);
                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $user, 'allocate_epin', 'EPIN Allocated');
                }
                $msg = lang('epin_allocated_successfully');
                $this->redirect($msg, 'epin/allocate_pin_user', true);
            } else {
                $msg = lang('error_on_epin_allocation');
                $this->redirect($msg, 'epin/allocate_pin_user', false);
            }
        }
        $this->set('amount_details', $amount_details);
        $this->setView();
    }

    function valid_user($user_name)
    {
        if(!$user_name) {
            return true;
        }
        $user_id = $this->validation_model->userNameToID($user_name);
        if (!$user_id) {
            $this->form_validation->set_message('valid_user', lang('invalid_username'));
            return false;
        }
        return true;
    }

    function validate_allocate_pin_user() {
        $this->lang->load('validation');
        $this->form_validation->set_rules('user_name', lang('user_name'), 'trim|required|callback_valid_user', [
            'required' => sprintf(lang('required'), lang('user_name')),
            'valid_user' => lang('invalid_username')
        ]);
        $this->form_validation->set_rules('amount1', lang('amount'), 'trim|required|greater_than[0]', [
            'required' => sprintf(lang('required'), 'amount'),
            'greater_than' => lang('greater_zero')
        ]);
        $this->form_validation->set_rules('count', lang('epin_count'), 'trim|required|integer|greater_than[0]', [
            'required' => sprintf(lang('required'), lang('epin_count')),
            'integer' => lang('digits_only'),
            'greater_than' => lang('greater_zero')
        ]);
        $this->form_validation->set_rules('date', lang('date'), 'trim|required|valid_date|date_less_than_current_date', [
            'required' => sprintf(lang('required'), lang('date')),
            'valid_date' => lang('select_valid_date'),
            'date_less_than_current_date' => lang('valid_date')
        ]);
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function validate_view_pin_user()
    {
        if (!$this->input->post('user_name')) {
            $msg = lang('you_must_enter_user_name');
            $this->redirect($msg, 'epin/view_pin_user', false);
        } else {
            $this->form_validation->set_rules('user_name', lang('user_name'), 'trim|required|strip_tags');
            $validate_form = $this->form_validation->run();
            return $validate_form;
        }
    }

    function delete($delete_id = '')
    {

        $result = $this->epin_model->deleteEPin($delete_id);
        if ($result) {
            $data_array['delete_id'] = $delete_id;
            $data = serialize($data_array);
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'epin deleted', $this->LOG_USER_ID, $data);
            if ($this->LOG_USER_TYPE == 'employee') {
                $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'epin deleted', 'EPIN Deleted');
            }
            $msg = lang('epin_deleted_successfully');
            $this->redirect($msg, 'profile/view_pin_user', true);
        } else {
            $msg = lang('error_on_deleting_epin');
            $this->redirect($msg, 'profile/view_pin_user', false);
        }
    }

    function validate_generate_epin()
    {

        if ($this->input->post('addpasscode')) {
            $tab1 = 'active';
            $tab2 = '';
            $this->session->set_userdata('inf_epin_tab_active_arr', array('tab1' => $tab1, 'tab2' => $tab2));
            $this->form_validation->set_rules('amount1', lang('amount'), 'trim|required|greater_than[0]');
            $this->form_validation->set_rules('count', lang('count'), 'trim|required|integer|greater_than[0]');
            $this->form_validation->set_rules('date', lang('date'), 'required');
            $val = $this->form_validation->run();
            if ($val) {
                $exp = $this->input->post('date', true);
                if ($exp < date("Y-m-d")) {
                    $msg1 = lang('old_date');
                    $this->redirect($msg1, 'epin/add_new_epin', false);
                }
                $cnt = $this->input->post('count', true);
                $max_pincount = $this->epin_model->getMaxPinCount();
                $rec = $this->epin_model->getAllActivePinspage();
                if ($rec < $max_pincount) {
                    $errorcount = $max_pincount - $rec;
                    if ($cnt <= $errorcount) {
                        return true;
                    } else {
                        $msg1 = lang('you_are_permitted_to_add');
                        $msg2 = lang('epin_only');
                        $this->redirect($msg1 . ' ' . $errorcount . ' ' . $msg2, 'epin/add_new_epin', false);
                    }
                } else {
                    $msg1 = lang('already');
                    $msg2 = lang('epin_present');
                    $this->redirect($msg1 . ' ' . $rec . ' ' . $msg2, 'epin/add_new_epin', false);
                }
            } else {
                $error = $this->form_validation->error_array();
                if (isset($error['amount1'])) {
                    $this->redirect($error['amount1'], 'epin/add_new_epin', false);
                } elseif (isset($error['count'])) {
                    $this->redirect($error['count'], 'epin/add_new_epin', false);
                } elseif (isset($error['date'])) {
                    $this->redirect($error['date'], 'epin/add_new_epin', false);
                }
            }
        }
    }

    function validate_search_epin()
    {
        if ($this->input->post('search_pin')) {
            $this->form_validation->set_rules('keyword', lang('epin'), 'trim|required');
            $val = $this->form_validation->run();
            if ($val) {
                return true;
            } else {
                $this->session->unset_userdata('epin_search_type');
                $error = $this->form_validation->error_array();
                if (isset($error['keyword'])) {
                    $this->redirect($error['keyword'], 'epin/search_epin');
                }
            }
        }
    }

    function validate_search_pin_amount()
    {
        if ($this->input->post('search_pin_pro')) {
            $this->form_validation->set_rules('amount', lang('amount'), 'trim|required');
            $val = $this->form_validation->run();
            if ($val) {
                return true;
            } else {
                $this->session->unset_userdata('epin_search_type');
                $error = $this->form_validation->error_array();
                if (isset($error['amount'])) {
                    $this->redirect($error['amount'], 'epin/search_epin');
                }
            }
        }
    }

    function active_epin($action = '', $delete_id = '')
    {

        if ($action == 'block') {
            $result = $this->epin_model->updateEPin($delete_id, 'no');
            if ($result) {
                $data_array['delete_id'] = $delete_id;
                $data = serialize($data_array);
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'epin deactivated', $this->LOG_USER_ID, $data);

                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'deactivate_epin', 'EPIN Deactivated');
                }
                //

                $msg = lang('epin_deactivated_successfully');
                $this->redirect($msg, 'epin/epin_management', true);
            } else {
                $msg = lang('error_on_updating_epin');
                $this->redirect($msg, 'epin/epin_management', false);
            }
        }
    }



    function delete_epin_old($action = '', $delete_id = '')
    {

        if ($action == 'delete') {
            $result = $this->epin_model->deleteEPin($delete_id);
            if ($result) {
                $data_array['delete_id'] = $delete_id;
                $data = serialize($data_array);
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'epin deleted', $this->LOG_USER_ID, $data);

                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'delete_epin', 'EPIN Deleted');
                }
                //

                $msg = lang('epin_deleted_successfully');
                $this->redirect($msg, 'epin/epin_management', true);
            } else {
                $msg = lang('error_on_deleting_epin');
                $this->redirect($msg, 'epin/epin_management', false);
            }
        }
    }

    function inactive_epin($action = '', $delete_id = '')
    {

        if ($action == 'activate') {
            $result = $this->epin_model->updateEPin($delete_id, 'yes');
            if ($result) {
                $data_array['delete_id'] = $delete_id;
                $data = serialize($data_array);
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'epin activated', $this->LOG_USER_ID, $data);

                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'activate_epin', 'EPIN Activated');
                }
                //

                $msg = lang('epin_activated_successfully');
                $this->redirect($msg, 'epin/epin_management', true);
            } else {
                $msg = lang('error_on_updating_epin');
                $this->redirect($msg, 'epin/epin_management', false);
            }
        }
    }

    function delete_all_epin($action = '', $pin_status = 'active', $page = '')
    {
        if ($action == 'delete') {
            $limit = $this->PAGINATION_PER_PAGE;
            if ($page == '') {
                $page = 0;
            }
            $result = $this->epin_model->deleteAllEPin($pin_status, $page, $limit);
            if ($result) {
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'All EPIN Deleted', $this->LOG_USER_ID, $data = '');
                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'delete_all_epin', 'All EPIN Deleted');
                }
                //

                $msg = lang('epin_deleted_successfully');
                $this->redirect($msg, 'epin/epin_management', true);
            } else {
                $msg = lang('error_on_deleting_epin');
                $this->redirect($msg, 'epin/epin_management', false);
            }
        }
    }

    public function add_new_epin()
    {
        $title = lang('add_new_epin');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $help_link = 'e-pin-management';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('add_new_epin');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('add_new_epin');
        $this->HEADER_LANG['page_small_header'] = '';

        $link_id = $this->inf_model->getURLID('epin/epin_management');
        if ($link_id) {
            $url_perm = $this->inf_model->checkUrlPermitted($link_id, 'perm_admin');
            if (!$url_perm) {
                $msg = lang('permission_denied');
                $this->redirect($msg, 'home/index', false);
            }
        }

        if ($this->input->post('addpasscode') && $this->validate_generate_epin()) {
            $add_post_array = $this->input->post(null, true);
            $add_post_array = $this->validation_model->stripTagsPostArray($add_post_array);

            $uploded_date = date('Y-m-d H:i:s');
            $pin_alloc_date = date('Y-m-d H:i:s');
            $status = 'yes';
            $cnt = $add_post_array['count'];
            $pin_amount = $add_post_array['amount1'];
            $expiry_date = $add_post_array['date'];
            $res = $this->epin_model->generatePasscode($cnt, $status, $uploded_date, $pin_amount, $expiry_date, $pin_alloc_date);
            if ($res) {
                $login_id = $this->LOG_USER_ID;
                $user_type = $this->LOG_USER_TYPE;
                if ($user_type == 'employee') {
                    $login_id = $this->validation_model->getAdminId();
                }
                $data = serialize($add_post_array);
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'epin added', $this->LOG_USER_ID, $data);

                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'add_epin', 'EPIN Added');
                }
                //

                $msg = lang('epin_added_successfully');
                $this->redirect($msg, 'epin/epin_management', true);
            } else {
                $msg = lang('error_on_adding_epin');
                $this->redirect($msg, 'epin/epin_management', false);
            }
        }
        $amount_details = $this->epin_model->getAllEwalletAmounts();
        $total_pin = $this->epin_model->getUnallocatedPinCount();
        $this->load_langauge_scripts();
        $this->set('un_allocated_pin', $total_pin);
        $this->set('amount_details', $amount_details);
        $this->setView();
    }

    function epin_transfer()
    {
        $title = lang('epin_transfer');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);

        $help_link = 'allocate-pin-to-user';
        $this->set('help_link', $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('epin_transfer');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('epin_transfer');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        if ($this->input->post('allocate') && $this->validate_epin_transfer()) {
            $post_arr = $this->input->post(null, true);
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
            $user = strip_tags($this->validation_model->userNameToID($post_arr['user_name']));
            $from_user = strip_tags($this->validation_model->userNameToID($post_arr['from_user_name']));
            $res = $this->epin_model->epinAllocation($user, $post_arr['epin']);
            if ($res) {
                $login_id = $this->LOG_USER_ID;
                $user_type = $this->LOG_USER_TYPE;
                if ($user_type == 'employee') {
                    $login_id = $this->validation_model->getAdminId();
                }
                $data = serialize($post_arr);
                $this->validation_model->insertUserActivity($user, 'Epin transferred to ' . $post_arr['user_name'], $this->LOG_USER_ID, $data);

                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'transfer_epin', 'EPIN transferred to ' . $post_arr['user_name']);
                }
                //
                // Epin Transfer History
                $this->epin_model->insertEpinTransferHistory($login_id, 'Epin transferred', $user, $from_user, $post_arr['epin'], $data);
                $msg = lang('epin_transferred_successfully');
                $this->redirect($msg, 'epin/epin_transfer', true);
            } else {
                $msg = lang('error_please_try_again');
                $this->redirect($msg, 'epin/epin_transfer', false);
            }
        }
        $this->setView();
    }

    function validate_epin_transfer()
    {
        $this->lang->load('validation');
        $this->form_validation->set_rules('epin', lang('epin'), 'trim|required|callback_valid_epin',[
       "requuired"=>lang('required'),
       "valid_epin"=>sprintf(lang('invalid'),lang('epin'))
        ]);
        $this->form_validation->set_rules('from_user_name', lang('from_user_name'), 'trim|required|callback_valid_user',[
         "required"=>lang('required'),
         'valid_user'=>sprintf(lang('invalid'),lang('from_user_name'))
        ]);
        $this->form_validation->set_rules('user_name', lang('user_name'), 'trim|required|callback_valid_user|callback_check_match[' . $this->input->post('from_user_name') . ']',[
          "required"=>lang('required'),
          "valid_user"=>sprintf(lang('invalid'),lang('user_name'))

        ]);
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function check_match($to_user_name, $from_user_name)
    {
        if ($from_user_name == $to_user_name) {
            $this->form_validation->set_message('check_match', lang('can_not_transfer_to_same_user'));
            return false;
        }
        return true;
    }

    function valid_epin($epin)
    {
        if ($epin == 'default') {
            $this->form_validation->set_message('valid_epin', lang('select_epin'));
            return false;
        }
        return true;
    }

    public function epin_dynamic_list($username = '')
    {
        $result_html = "<option value='default'>" . lang('select_epin') . "</option>";
        if ($this->valid_user($username)) {
            $result_arr = $this->epin_model->getEpinList($this->validation_model->userNameToID($username));
            foreach ($result_arr as $result) {
                $result_html .= '<option value=' . $result["pin_id"] . '>' . $result["pin_numbers"];
            }
        }
        echo json_encode($result_html);
    }

    function validate_allocate_user()
    {
        $this->form_validation->set_rules('user_name', lang('user_name'), 'trim|required|callback_valid_user');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function refund_epin($action = '', $delete_id = '')
    {
        $result = '';
        if ($action == 'refund') {
            if ($this->session->userdata('epin_search_type')) {
                $this->session->unset_userdata('epin_search_type');
            }
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
                //
                $msg = lang('epin_refunded_successfully');
                $this->redirect($msg, 'epin/search_epin', true);
            } else {
                $msg = lang('error_on_refunding_epin');
                $this->redirect($msg, 'epin/search_epin', false);
            }
        }
    }

    public function ajax_epin_autolist()
    {
        if ($this->input->is_ajax_request()) {
            $keyword = $this->input->post('keyword', true);
            $data = $this->epin_model->getEPinsByKeyword($keyword);
            echo json_encode($data);
            exit();
        }
    }

    public function validate_username() {
        if($this->valid_user($this->input->post('username'))) {
            echo "yes";
            return true;
        }
        echo "no";
        return false;

    }


    public function allocate_delete_epin_request() {
        // dd($_POST);
        if (empty($this->input->post('request_id'))) {
            $msg = lang('please_select_checkbox');
            $this->redirect($msg, 'epin/view_epin_request'.get_previous_url_query_string(), false); 
        } else if($this->input->post('action') == "allocate") {
            $pin_post_array = $this->input->post(null, true);
            $pin_post_array = $this->validation_model->stripTagsPostArray($pin_post_array);
            $admin_id = $this->LOG_USER_ID;
            $user_type = $this->LOG_USER_TYPE;
            if ($user_type == 'employee') {
                $admin_id = $this->validation_model->getAdminId();
            }
            $uploded_date = date('Y-m-d H:i:s');
            $pin_alloc_date = date('Y-m-d H:i:s');
            $status = 'yes';
            $res = true;
            $flag1 = true;
            $flag2 = true;
            foreach($pin_post_array['request_id'] as $request) {
                if($pin_post_array['count'][$request] < 0) {
                    $flag1 = false;
                }
                if($pin_post_array['count'][$request] == "") {
                    $flag2 = false;
                }
            }
            if (!$flag1) {
                $msg = lang('you_must_enter_a_positive_value');
                $this->redirect($msg, 'epin/view_epin_request'.get_previous_url_query_string(), false);
            }
            if (!$flag2) {
                $msg = lang('count_field_is_required');
                $this->redirect($msg, 'epin/view_epin_request'.get_previous_url_query_string(), false);
            }
            $this->epin_model->begin();
            foreach($pin_post_array['request_id'] as $key => $request) {
                $id = $request;
                $pin_count = $pin_post_array['count'][$request];
                $allocate_id = $pin_post_array['allocate_user'][$request];
                $rem_count = $pin_post_array['remaining_epin_count'][$request];
                $expiry_date = $pin_post_array['epin_expiry_date'][$request];
                $amount = $pin_post_array['epin_amount'][$request];
                if ($pin_count <= $rem_count) {
                    $res = $this->epin_model->ifChecked($id, $pin_count, $pin_alloc_date, $status, $uploded_date, $admin_id, $allocate_id, $rem_count, $amount, $expiry_date);
                    if (!$res) {
                        $res = false;
                        $msg = lang('error_on_epin_allocation');
                        break;
                    }
                } else {
                    $res = false;
                    $msg = lang('epin_count_should_less_req_count');
                    break;
                }
            }
            if ($res) {
                $this->epin_model->commit();
                $data = serialize($pin_post_array);
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'epin requests granted', $this->LOG_USER_ID, $data);

                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'grant_epin_request', 'EPIN Request Granted');
                }
                $msg = lang('epin_allocated_successfully');
                $this->redirect($msg, 'epin/view_epin_request'.get_previous_url_query_string(), true);
            } else {
                $this->epin_model->rollback();
                $this->redirect($msg, 'epin/view_epin_request'.get_previous_url_query_string(), false);
            }
        } else if($this->input->post('action') == "delete") {
            $pin_post_array = $this->input->post(null, true);
            $pin_post_array = $this->validation_model->stripTagsPostArray($pin_post_array);
            $delete_id =$pin_post_array['request_id'];
            $result = $this->epin_model->deleteAllRequestedEpin($delete_id, "remark deleted");
            if ($result) {
                $msg = lang('requested_epin_deleted_sucessfully');
                $this->redirect($msg, 'epin/view_epin_request'.get_previous_url_query_string(), true);
            } else {
                $msg = lang('error_on_requested_epin_deletion');
                $this->redirect($msg, 'epin/view_epin_request'.get_previous_url_query_string(), false);
            }
        }

    }
   public function agent_epin() {
        $this->set('title', $this->COMPANY_NAME . ' | ' . lang('epin'));
        $this->load_langauge_scripts();

        if ($this->input->get('tab') == "requests") {
            $this->epin_model->setEpinViewed(1);
            $this->set_header_notification_box();
        }

        $active_user_name = $this->validation_model->isUsernameExists($this->input->get('user_name')) ? $this->input->get('user_name') : '';

        $this->set('active_epins', $this->epin_model->activeEpins());
        $this->set('epin_requests_count', $this->epin_model->getAllEpinRequestsCountNew());
        $this->set('amounts', $this->epin_model->getAllEpinAmounts());
        $this->set('active_user_name', $active_user_name);
        $this->set('wallet_balance', $this->validation_model->getAgentWalletBalance($this->LOG_USER_ID));
        $this->setView('newui/admin/epin/agent_epin');
    }
    public function create_epin_post_agent() {
        $validated = $this->create_epin_validation();
        if ($validated['status']) {
            $post_arr = $this->input->post(null, true);
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
            $user = '';
            if($post_arr['user_name']) {
                $user = strip_tags($this->validation_model->userNameToID($post_arr['user_name']));
            }
            $kyc_status = $this->MODULE_STATUS['kyc_status'];
            // if ($kyc_status == 'yes') {
            //      $kyc_upload = $this->validation_model->checkKycUpload($user);
            //      if ($kyc_upload != 'yes') {
            //       echo json_encode([
            //             'status'     => 'failed',
            //             'error_type' => 'unknown',
            //             'message'    => lang('KYC not verified')
            //         ]); die();
            //       }
            // }
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
            $agent_id=$this->LOG_USER_ID;
            $balamount = $this->validation_model->getAgentWalletBalance($agent_id);
            // $amount = $this->ewallet_model->getEpinAmount($amount_id);
            $amount = $post_arr['amount'];
            $pin_count=$post_arr['epin_count'];
            $tot_avb_amt = $amount * $pin_count;
            if ($tot_avb_amt <= $balamount) {
                $res = $this->epin_model->generateEpinNew($post_arr['user_name'], $post_arr['amount'], $post_arr['epin_count'], $post_arr['expiry_date']);
                if ($res) {
                    $bal = round($balamount - $tot_avb_amt, 8);
                    $update = $this->validation_model->updateAgentBalanceAmount($agent_id, $bal);
                    $login_id = $this->LOG_USER_ID;
                    $user_type = $this->LOG_USER_TYPE;
                    if ($user_type == 'employee') {
                        $login_id = $this->validation_model->getAdminId();
                    }
                    $data = serialize($post_arr);
                    $this->validation_model->insertUserActivity($login_id, 'epin allocated', $user, $data);
                    // Employee Activity History
                    if ($this->LOG_USER_TYPE == 'employee') {
                        $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $user, 'allocate_epin', 'EPIN Allocated');
                    }
                    if($this->LOG_USER_TYPE == 'agent'){
                        $this->validation_model->insertAgentActivity($this->LOG_USER_ID, $user, 'allocate_epin', 'EPIN Allocated');
                    }
                    echo json_encode([
                        'status'  => 'success',
                        'message' => lang('epin_allocated_successfully')
                    ]); exit();
                
                } else {
                    echo json_encode([
                        'status'     => 'failed',
                        'error_type' => 'unknown',
                        'message'    => lang('error_on_epin_allocation')
                    ]);exit();
                } 
                   
            }else {
                    echo json_encode([
                        'status'     => 'failed',
                        'error_type' => 'unknown',
                        'message'    => lang('no_sufficient_balance_amount')
                    ]); exit();
                } 
        } else {
            echo json_encode($validated);
            exit();
        }
        
    }
    public function allocate_epin_agent() {
        if(!empty($this->input->post('epins'))) {
            foreach($this->input->post('epins') as $epin) {
                if($epin['count'] <= 0) {
                    echo json_encode([
                       'status'     => 'failed',
                       'error_type' => 'unknown',
                       'message'    => lang('you_must_enter_a_positive_value')
                    ]);die;
                    break;
                }
            
                $total_epin = $this->epin_model->getEpinsCount();
                // $epin_max_count = $this->epin_model->getMaxPinCount();
                //check the epin active count
                // if (($total_epin) > $epin_max_count) {
                //     echo json_encode([
                //         'status'     => 'failed',
                //         'error_type' => 'unknown',
                //         'message'    => lang('error_epin_count')
                //     ]);exit();
                // }
                $epin_request = $this->epin_model->getEpinRequestAllocatedUserId($epin['pin_id']);
                if($epin_request->req_rec_pin_count < $epin['count']) {
                    echo json_encode([
                       'status'     => 'failed',
                       'error_type' => 'unknown',
                       'message'    => lang('epin_count_should_less_req_count')
                    ]); die;
                    break;   
                }
            }

            $pin_numbers = [];
            $pin_requests = [];
            $pin_amount=0;
            foreach($this->input->post('epins') as $key => $epin) {
                $epin_request = $this->epin_model->getEpinRequestAllocatedUserId($epin['pin_id']);
                for($i = 0; $i < $epin['count']; $i++) {
                    $pin_numbers[] = [
                        'pin_numbers'        => $this->misc_model->getRandStr(9, 9),
                        'pin_alloc_date'     => date('Y-m-d H:i:s'),
                        'status'             => 'yes',
                        'used_user'          => '',
                        'pin_uploded_date'   => date('Y-m-d H:i:s'),
                        'generated_user_id'  => $this->validation_model->getAdminId(),
                        'allocated_user_id'  => $epin_request->req_user_id,
                        'pin_expiry_date'    => $epin_request->pin_expiry_date,
                        'pin_amount'         => $epin_request->pin_amount,
                        'pin_balance_amount' => $epin_request->pin_amount,
                        'user_type'          => $this->LOG_USER_TYPE,
                        'generated_agent'    => $this->LOG_USER_ID,
                     ];
                     $pin_amount+=$epin_request->pin_amount;
                }
                if($epin['count'] == $epin_request->req_pin_count) {
                    $pin_requests[] = [
                        'req_id' => $epin['pin_id'],
                        'status' => 'no'
                    ];
                } else {
                    $pin_requests[] = [
                        'req_id'        => $epin['pin_id'],
                        'req_pin_count' => $epin_request->req_pin_count - $epin['count'],
                        'req_rec_pin_count'     => $epin_request->req_pin_count - $epin['count'],
                    ];
                }
            }
            $balamount = $this->validation_model->getAgentWalletBalance($this->LOG_USER_ID);

            if ($pin_amount <= $balamount) {
                if($this->epin_model->allocateEpinRequests($pin_numbers, $pin_requests)) {
                    $bal = round($balamount - $pin_amount, 8);
                    $update = $this->validation_model->updateAgentBalanceAmount($this->LOG_USER_ID, $bal);
                    $data = serialize($this->input->post());
                    $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'epin requests granted', $this->LOG_USER_ID, $data);
                    // Employee Activity History
                    if ($this->LOG_USER_TYPE == 'employee') {
                        $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'grant_epin_request', 'EPIN Request Granted');
                    }
                    if($this->LOG_USER_TYPE == 'agent'){
                        $this->validation_model->insertAgentActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'grant_epin_request', 'EPIN Request Granted');
                    }
                    echo json_encode([
                       'status'     => 'success',
                       'error_type' => 'unknown',
                       'message'    => lang('epin_allocated_successfully')
                    ]); die;
                }
                echo json_encode([
                   'status'     => 'failed',
                   'error_type' => 'unknown',
                   'message'    => lang('error_on_epin_allocation')
                ]); die;
            }else{
                 echo json_encode([
                    'status'     => 'failed',
                    'error_type' => 'unknown',
                    'message'    => lang('no_sufficient_balance_amount')
                ]); die;
            }
        }
        echo json_encode([
           'status' => 'failed',
           'error_type' => 'validation',
           'message' => lang('please_select_checkbox')
        ]); die;
    }
    function extend_pin(){
        $id=$this->input->get('id');
        $days=$this->input->get('extend_days');
        if($id && $days>0){
            $result = $this->epin_model->extendPinValidity($id,$days);
            if ($result) {
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Epin Extended', $this->LOG_USER_ID, $data = '');
                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'epin_extend', 'EPIN Extended');
                }
                //

                $msg = lang('epin_extended');
                $this->redirect($msg, 'epin/index', true);
            } else {
                $msg = lang('failed_to_extend_validity');
                $this->redirect($msg, 'epin/index', false);
            }
        }
        $this->redirect('', 'epin/index', false);
    }
}
