<?php

require_once 'Inf_Controller.php';
require "../vendor/autoload.php";

define("IN_WALLET", true);

class Payout extends Inf_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('profile_model');
        $this->load->model('mail_model');
        $this->load->model('validation_model');
        $this->load->model('payout_optional_model');
        $this->config->set_item('csrf_exclude_uris', 'payout/bitgo_trans_approved');
    }

    public function summary_total()
    {
        $total['total_amount_active_request'] = thousands_currency_format($this->payout_model->getTotalAmountPendingRequest('pending'));
        $total['total_amount_waiting_requests'] = thousands_currency_format($this->payout_model->getTotalAmountApproved());
        $total['total_amount_paid_request'] = thousands_currency_format($this->payout_model->getTotalAmountPaid());
        $total['total_amount_rejected_requests'] = thousands_currency_format($this->payout_model->getTotalAmountRejected('deleted'));

        echo json_encode($total);
        exit();
    }
    public function index()
    {
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

        $total_amount_active_request = $this->payout_model->getTotalAmountPendingRequest('pending');
        $total_amount_waiting_requests = $this->payout_model->getTotalAmountApproved();
        $total_amount_paid_request = $this->payout_model->getTotalAmountPaid();
        $total_amount_rejected_requests = $this->payout_model->getTotalAmountRejected('deleted');

        $payout_release = $this->configuration_model->getPayOutTypes();
        $payment_method = $this->payout_model->gatewayList();
        $payment_type = $this->input->get('payment_method') ?: 'Bank Transfer';

        if ($payout_release == 'from_ewallet') {
            $payout_type = 'admin';
        } else if ($payout_release == 'ewallet_request') {
            $payout_type = 'user';
        } else if ($payout_release == 'both') {
            $payout_type = 'admin';
        }

        $active_user_name = $this->validation_model->isUsernameExists($this->input->get('user_name')) ? $this->input->get('user_name') : '';
        $this->set('active_user_name', $active_user_name);

        $this->set('total_amount_active_request', $total_amount_active_request);
        $this->set('total_amount_waiting_requests', $total_amount_waiting_requests);
        $this->set('total_amount_paid_request', $total_amount_paid_request);
        $this->set('total_amount_rejected_requests', $total_amount_rejected_requests);
        $this->set('payout_type', $payout_type);
        $this->set('payment_method', $payment_method);
        $this->set('payment_type', $payment_type);
        $this->set('payout_release', $payout_release);
        $this->setView("newui/admin/payout/index");
    }

    public function payout_release_json()
    {
        $columns = [
            0 => '',
            1 => 'ud.user_detail_name',
            2 => '',
            3 => 'user_name',
            4 => 'payout_type',
            5 => 'balance_amount',
        ];

        $index = $this->input->get('order')[0]['column'] ?? -1;
        $filter = [
            'limit' => $this->input->get('length'),
            'start' => intval($this->input->get("start")),
            'order' => $columns[$index] ?? '',
            'direction' => $this->input->get('order')[0]['dir'] ?? '',
        ];

        $payout_release = $this->payout_model->getPayOutTypes();
        if ($payout_release == 'from_ewallet') {
            $payout_type = 'admin';
        } else if ($payout_release == 'ewallet_request') {
            $payout_type = 'user';
        } else if ($payout_release == 'both') {
            $payout_type = 'admin';
        }

        $user_names = $this->input->get('user_names');
        $user_ids = $this->payout_model->userNamesToIDs($user_names);
        // $payment_type   = $this->input->get('payment_method') ?: ['bank'];
        $payment_type = $this->input->get('payment_method') ?: ['Bank Transfer'];
        $payout_type = $this->input->get('payout_release_type') ?: $payout_type;
        $kyc_status = $this->input->get('kyc_status') ?: 'active';
        if ($payout_type == "admin") {
            $count = $this->payout_model->getPayoutDetailsCount('from_ewallet', '', '', $payment_type, $user_names, $kyc_status);
            $payout_requests = $this->payout_model->getPayoutDetailsNew('from_ewallet', '', '', $payment_type, $user_names, $filter, $kyc_status);
        } else {
            $count = $this->payout_model->getPayoutDetailsCount('ewallet_request', '', '', $payment_type, $user_names, $kyc_status);
            $payout_requests = $this->payout_model->getPayoutDetailsNew('ewallet_request', '', '', $payment_type, $user_names, $filter, $kyc_status);
        }

        $data = [];
        foreach ($payout_requests as $request) {
            $data[] = [
                'request_id' => $request['req_id'],
                'full_name' => $request['full_name'],
                'user_name' => $request['user_name'],
                'profile_image' => profile_image_path($request['user_photo']),
                'payout_request_type' => $payout_type,
                'payout_amount' => round($request['payout_amount'] * $this->DEFAULT_CURRENCY_VALUE, $this->PRECISION),
                "payout_method" => ucfirst($request['payout_type']),
                "payout_type" => $payout_type == "admin" ? lang('manual') : lang('user_request'),
                "ewallet_balance" => $request['balance_amount'],
            ];
        }

        echo json_encode([
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data,
        ]);die;
    }

    public function payout_requests()
    {
        $columns = [
            0 => '',
            1 => 'user_detail_name',
            2 => 'requested_amount_balance',
            3 => 'user_name',
            4 => 'payout_type',
            5 => 'requested_date',
        ];

        $filter = [
            'limit' => $this->input->get('length'),
            'start' => intval($this->input->get("start")),
            'order' => isset($columns[$this->input->get('order')[0]['column']]) ? $columns[$this->input->get('order')[0]['column']] : '',
            'direction' => $this->input->get('order')[0]['dir'] ?: 'ASC',
        ];

        $payout_release = $this->payout_model->getPayOutTypes();
        if ($payout_release == 'from_ewallet') {
            $payout_type = 'admin';
        } else if ($payout_release == 'ewallet_request') {
            $payout_type = 'user';
        } else if ($payout_release == 'both') {
            $payout_type = 'admin';
        }

        $user_names = $this->input->get('user_names');
        $user_ids = $this->payout_model->userNamesToIDs($user_names);
        $payment_type = $this->input->get('payment_method') ?: ['Bank Transfer'];

        $count = $this->payout_model->getPayoutDetailsCount('ewallet_request', '', '', $payment_type, $user_names);
        $payout_requests = $this->payout_model->getPayoutDetailsNew('ewallet_request', '', '', $payment_type, $user_names, $filter);
        $data = [];
        foreach ($payout_requests as $request) {
            $data[] = [
                'checkbox' => "
                    <div class='checkbox'>
                        <label class='i-checks'>
                            <input type='checkbox' name='request_id[]' class='payout-checkbox payout-requests-release-single' value='" . $request['req_id'] . "'>
                            <i></i>
                        </label>
                    </div>",
                'member_name' => "
                    <div class='d-flex'>
                        <img src='" . SITE_URL . "/uploads/images/profile_picture/" . $request['user_photo'] . "' alt='img' class='ht-30 wd-30 mr-2'>
                        <div class='margin-wallet-img'>
                            <h5>" . $request['full_name'] . "</h5>
                            <span class='sub-text'>" . $request['user_name'] . "</span>
                        </div>
                    </div>",
                'payout_amount' => "<span class='badge bg-amount'>" . format_currency($request['payout_amount']) . "</span>
                    <input type='hidden' class='payout_amount' value='" . round($request['payout_amount'] * $this->DEFAULT_CURRENCY_VALUE, $this->PRECISION) . "'> ",
                "payout_method" => ucfirst($request['payout_type']),
                "payout_type" => lang('user_request'),
                "requested_date" => date("F j, Y, g:i a", strtotime($request['requested_date'])),
            ];
        }

        echo json_encode([
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data,
        ]);die;
    }

    public function payout_status_pending_list()
    {
        $columns = [
            0 => 'user_detail_name',
            1 => 'requested_amount_balance',
            2 => '',
            3 => 'requested_date',
        ];

        $filter = [
            'limit' => $this->input->get('length'),
            'start' => intval($this->input->get("start")),
            'order' => isset($columns[$this->input->get('order')[0]['column']]) ? $columns[$this->input->get('order')[0]['column']] : '',
            'direction' => $this->input->get('order')[0]['dir'],
        ];

        $user_names = $this->input->get('user_names');
        $user_ids = $this->payout_model->userNamesToIDs($user_names);

        $count = $this->payout_model->getPayoutWithdrawalCountNew($user_ids, 'pending');
        $pending_requests = $this->payout_model->getPayoutWithdrawalDetailsNew($user_ids, 'pending', $filter);
        $data = [];
        foreach ($pending_requests as $key => $item) {
            $data[] = [
                'user_photo' => profile_image_path($item['user_photo']),
                'full_name' => $item['full_name'],
                'user_name' => $item['user_name'],
                'payout_amount' => format_currency($item['payout_amount']),
                'ewallet_balance' => format_currency($item['balance_amount']),
                "requested_date" => date("F j, Y, g:i a", strtotime($item['requested_date'])),
            ];
        }

        echo json_encode([
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data,
        ]);die;

    }

    public function payout_status_approved_pending_list()
    {
        $columns = [
            0 => 'user_detail_name',
            1 => 'paid_amount',
            2 => 'payment_method',
            3 => 'paid_date',
        ];

        $filter = [
            'limit' => $this->input->get('length'),
            'start' => intval($this->input->get("start")),
            'order' => isset($columns[$this->input->get('order')[0]['column']]) ? $columns[$this->input->get('order')[0]['column']] : '',
            'direction' => $this->input->get('order')[0]['dir'],
        ];

        $user_names = $this->input->get('user_names');
        $user_ids = $this->payout_model->userNamesToIDs($user_names);

        $count = $this->payout_model->getReleasedWithdrawalCountNew($user_ids, 'approved_pending');
        $waiting_requests = $this->payout_model->getReleasedWithdrawalDetailsNew($user_ids, 'approved_pending', $filter);

        $data = [];
        foreach ($waiting_requests as $key => $item) {
            $data[] = [
                'user_name' => $item['user_name'],
                'full_name' => $item['user_detail_name'],
                'user_photo' => profile_image_path($item['user_photo']),
                'amount' => format_currency($item['paid_amount']),
                'payout_method' => lang($item['payment_method']),
                "approved_date" => date("F j, Y, g:i a", strtotime($item['paid_date'])),
            ];
        }

        echo json_encode([
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data,
        ]);die;
    }

    public function payout_status_approved_paid_list() {
        $columns = [
            0 => 'user_detail_name',
            1 => 'paid_amount',
            2 => 'payment_method',
            3 => 'paid_date',
        ];

        $filter = [
            'limit' => $this->input->get('length'),
            'start' => intval($this->input->get("start")),
            'order' => isset($columns[$this->input->get('order')[0]['column']]) ? $columns[$this->input->get('order')[0]['column']] : '',
            'direction' => $this->input->get('order')[0]['dir'],
        ];

        $user_names = $this->input->get('user_names');
        $user_ids = $this->payout_model->userNamesToIDs($user_names);

        // dd('here');
        $count = $this->payout_model->getReleasedWithdrawalCountNew($user_ids, 'approved_paid');
        $paid_requests = $this->payout_model->getReleasedWithdrawalDetailsNew($user_ids, 'approved_paid', $filter);

        $data = [];
        foreach ($paid_requests as $key => $item) {
            $data[] = [
                // 'slno' => $filter['start'] + $key + 1 ,
                'user_photo' => profile_image_path($item['user_photo']),
                'full_name' => $item['user_detail_name'],
                'user_name' => $item['user_name'],
                'invoice_no' => $item['paid_id'],
                'amount' => format_currency($item['paid_amount']),
                'paid_date' => date("F j, Y, g:i a", strtotime($item['paid_date'])),
                'payout_method' => lang($item['payment_method']),
            ];
        }

        echo json_encode([
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data,
        ]);die;
    }

    public function payout_status_rejected_list()
    {
        $columns = [
            0 => 'user_detail_name',
            1 => '',
            2 => 'user_name',
            3 => 'payout_type',
            4 => 'balance_amount',
        ];

        $filter = [
            'limit' => $this->input->get('length'),
            'start' => intval($this->input->get("start")),
            'order' => isset($columns[$this->input->get('order')[0]['column']]) ? $columns[$this->input->get('order')[0]['column']] : '',
            'direction' => $this->input->get('order')[0]['dir'],
        ];

        $user_names = $this->input->get('user_names');
        $user_ids = $this->payout_model->userNamesToIDs($user_names);

        $count = $this->payout_model->getPayoutWithdrawalCountNew($user_ids, 'deleted');
        $rejected_requests = $this->payout_model->getPayoutWithdrawalDetailsNew($user_ids, 'deleted', $filter);

        $data = [];
        foreach ($rejected_requests as $key => $item) {
            $data[] = [
                'user_photo' => profile_image_path($item['user_photo']),
                'full_name' => $item['user_detail_name'],
                'user_name' => $item['user_name'],
                'amount' => format_currency($item['payout_amount']),
                'requested_date' => date("F j, Y, g:i a", strtotime($item['requested_date'])),
                'rejected_date' => date("F j, Y, g:i a", strtotime($item['updated_date'])),
            ];
        }

        echo json_encode([
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data,
        ]);die;
    }

    public function process_payment_list()
    {
        $columns = [
            0 => 'paid_id',
            1 => 'user_detail_name',
            2 => 'paid_amount',
            3 => 'paid_date',
        ];

        $filter = [
            'limit' => $this->input->get('length'),
            'start' => intval($this->input->get("start")),
            'order' => isset($columns[$this->input->get('order')[0]['column']]) ? $columns[$this->input->get('order')[0]['column']] : '',
            'direction' => $this->input->get('order')[0]['dir'],
        ];

        $user_names = $this->input->get('user_names');
        $from_date = $to_date = "";

        $count = $this->payout_model->getPayoutCountNew($user_names, $from_date, $to_date);
        $payout_details = $this->payout_model->getReleasedPayoutNew($user_names, $from_date, $to_date, $filter);
        $data = [];
        foreach ($payout_details as $item) {
            $data[] = [
                'paid_id' => $item['paid_id'],
                'user_photo' => profile_image_path($item['user_photo']),
                'full_name' => $item['full_name'],
                'user_name' => $item['user_name'],
                'paid_amount' => format_currency($item['paid_amount']),
                "approved_date" => date("F j, Y, g:i a", strtotime($item['paid_date'])),
            ];
        }

        echo json_encode([
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data,
        ]);die;
    }

    public function payout_release_action()
    {
        $min_max_payout_amount = $this->payout_model->getMinimumMaximunPayoutAmount();
        if ($this->input->post()) {
            $post_arr = $this->input->post(null, true);
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
            $payout_release_type = $this->MODULE_STATUS['payout_release_status'];
            $kyc_status = $this->MODULE_STATUS['kyc_status'];
            $result = false;
            $j = 0;
            $payout_release_arr = array();
            $payout_release = array();
            $paypal_arr = array();
            $base_call = '';
            $total_amount = 0;
            $type = 'payout_release';
            if (!empty($post_arr['payouts'])) {
                foreach ($this->input->post('payouts') as $request) {
                    if ($this->input->post('payout_type') == "user") {
                        $user_id = $this->payout_model->getPayoutRequestUserId($request['user_name']);
                        $user_name = $this->validation_model->IdToUserName($user_id);

                    } elseif ($this->input->post('payout_type') == "admin") {
                        $user_id = $this->validation_model->userNameToID($request['user_name']);
                        $user_name = $request['user_name'];

                    }
                    $user_id = $this->validation_model->userNameToID($user_name);
                    if (!$user_id) {
                        echo json_encode([
                            'status' => 'failed',
                            'error_type' => 'unknown',
                            'message' => lang('invalid_user_name'),
                        ]);die;
                    }
                    $post_arr['payment_method'] = $this->payout_model->getUserPayoutPayMethod($user_id);
                    if(!$post_arr['payment_method']) {
                        echo json_encode([
                            'status' => 'failed',
                            'error_type' => 'unknown',
                            'message' => lang('Payout_Release_Failed'),
                        ]);die;
                    }

                    if ($kyc_status == 'yes') {
                        $kyc_upload = $this->validation_model->checkKycUpload($user_id);
                        if ($kyc_upload != 'yes') {
                            echo json_encode([
                                'status' => 'failed',
                                'error_type' => 'unknown',
                                'message' => lang('kyc_not_uploaded_for') . $user_name,
                            ]);die;
                        }
                    }

                    $payout_release_amount = 0;
                    $payout_fee = 0;
                    $userWalletDeduct = 0;
                    if ($this->input->post('payout_type') == "user") {
                        $payout_release_details = $this->payout_model->getPayoutRequestById($request['user_name']);
                        $payout_release_amount = $payout_release_details["requested_amount_balance"];
                        $payout_fee = $payout_release_details["payout_fee"];
                    } elseif ($this->input->post('payout_type') == "admin") {
                        $payout_release_amount = round((floatval($request['amount']) / $this->DEFAULT_CURRENCY_VALUE), 8);
                        $payout_fee = $this->payout_model->calculatePayoutFee($payout_release_amount);
                        $userWalletDeduct = $payout_release_amount + $payout_fee;
                    }

                    if ($payout_release_amount > $min_max_payout_amount['max_payout'] || $payout_release_amount < $min_max_payout_amount['min_payout']) {
                        echo json_encode([
                            'status' => 'failed',
                            'error_type' => 'unknown',
                            'message' => lang('You_cant_release_this_amount_for') . ' ' . $user_name,
                        ]);die;
                    }
                    if ($this->input->post('payout_type') == "admin") {
                        $request_id = $user_id;
                        $release_req_type = "from_ewallet";
                        $balance_amount = $this->payout_model->getUserBalanceAmount($user_id);
                        if ($userWalletDeduct > $balance_amount) {
                            echo json_encode([
                                'status' => 'failed',
                                'error_type' => 'unknown',
                                'message' => lang('Payout_Release_Failed') . " \n" . lang('Low_balance'),
                            ]);die;
                        }
                    }
                    if ($this->input->post('payout_type') == "user") {
                        $release_req_type = "ewallet_request";
                        $payout_request_balance_amount = $this->payout_model->getPayoutRequestById($request);
                        if ($payout_release_amount > $payout_request_balance_amount) {
                            echo json_encode([
                                'status' => 'failed',
                                'error_type' => 'unknown',
                                'message' => lang('You_cant_release_this_amount_for') . ' ' . $user_name,
                            ]);die;
                        }
                    }

                    $all_bitcoin_rates = $this->get_rate_for_one_bitcoin();
                    $system_bitcoin_rates = $all_bitcoin_rates['USD'];
                    $recent_bitcoin_rate = $system_bitcoin_rates['last'];

                    if ($post_arr['payment_method'] == "Bank Transfer" && $release_req_type == "from_ewallet") {
                        $res = $this->payout_model->updateUserBalanceAmount($user_id, $userWalletDeduct);
                    }
                    /* Payment Gateway Payout Begin */
                    elseif ($post_arr['payment_method'] == "Blockchain") {

                        $blockchain_details = $this->payout_optional_model->getBlockchainDetails();
                        $btc_transaction_fee = $blockchain_details['fee'] / 100000000;
                        $system_currency = $this->DEFAULT_CURRENCY_CODE;
                        $admin_wallet_balance = $this->get_admin_wallet_balance($blockchain_details);
                        $admin_btc_balance = $admin_wallet_balance['balance'] / 100000000;
                        $this->BlockchainPayout($user_id, $payout_release_amount, $request, $recent_bitcoin_rate, $blockchain_details, $btc_transaction_fee, $system_currency, $payout_release_type, $release_req_type, $admin_btc_balance);

                    } elseif ($post_arr['payment_method'] == "Bitgo") {
                        $btc_send_amount = round($payout_release_amount / $recent_bitcoin_rate, 8);
                        $user_bitgo_wallet_address = $this->payout_optional_model->getUserBitgoWalletAddress($user_id);
                        if ($user_bitgo_wallet_address == '' || $user_bitgo_wallet_address == 'NA') {
                            echo json_encode([
                                'status' => 'failed',
                                'error_type' => 'unknown',
                                'message' => lang('invalid_bitgo_wallet_address'),
                            ]);die;
                        }

                        // $requested_date = $post_arr['requested_date' . $i];
                        $request_details = $this->payout_model->getRequestPayoutDetails($request);
                        $requested_date = $request_details['requested_date'];
                        $total_amount = $total_amount + $btc_send_amount;
                        $bitgo_arr[$j]['amount'] = $btc_send_amount;
                        $bitgo_arr[$j]['amount_payable'] = $payout_release_amount;
                        $bitgo_arr[$j]['user_id'] = $user_id;
                        $bitgo_arr[$j]['payout_release_amount'] = $payout_release_amount;
                        $bitgo_arr[$j]['payout_release_type'] = $release_req_type;
                        $bitgo_arr[$j]['requested_date'] = $requested_date;
                        $bitgo_arr[$j]['req_id'] = $request;
                        $bitgo_arr[$j]['address'] = $user_bitgo_wallet_address;

                        $payout_release['recipients'][$j]['address'] = $user_bitgo_wallet_address;
                        $payout_release['recipients'][$j]['amount'] = $btc_send_amount * 100000000;
                        $j = $j + 1;
                    } elseif ($post_arr['payment_method'] == "Paypal") {
                        $user_email = $this->payout_optional_model->getUserPayPalEmail($user_id);
                        if ($user_email == "NA" || $user_email == "NULL" || $user_email == "") {
                            echo json_encode([
                                'status' => 'failed',
                                'error_type' => 'unknown',
                                'message' => "Pay email of $user_name not provided",
                            ]);die;
                        } else {
                            $id = urlencode(2040);
                            if ($this->input->post('payout_type') == "user") {
                                $request_details = $this->payout_model->getRequestPayoutDetails($request);
                                $requested_date = $request_details['requested_date'];
                            } else {
                                $request_details = [];
                                $requested_date = '';
                            }
                            $paypal_arr[$j]['user_id'] = $user_id;
                            array_push($payout_release_arr, $user_name);
                            array_push($payout_release_arr, $user_email);
                            array_push($payout_release_arr, $payout_release_amount);
                            $base_call .= "&L_EMAIL$j=" . urlencode($user_email) .
                            "&L_AMT$j=" . urlencode($payout_release_amount) .
                            "&L_UNIQUEID$j=" . urlencode($id) .
                            "&L_NOTE$j=" . urlencode('Payout Realease') .
                            "&EMAILSUBJECT$j=" . urlencode('Payout Realease') .
                            "&RECEIVERTYPE$j=" . urlencode('Payout Realease') .
                                "&CURRENCYCODE=" . 'USD';

                            $total_amount = $total_amount + $payout_release_amount;
                            $payout_release[$j]['user_id'] = $user_id;
                            $payout_release[$j]['payout_release_amount'] = $payout_release_amount;
                            $payout_release[$j]['payout_release_type'] = $release_req_type;
                            if ($this->input->post('payout_type') == "user") {
                                $payout_release[$j]['requested_date'] = $requested_date;
                                $payout_release[$j]['req_id'] = $request;
                            } else {
                                $payout_release[$j]['requested_date'] = '';
                                $payout_release[$j]['req_id'] = '';
                            }
                            $j = $j + 1;
                        }
                    }

                    /* Payment Gateway Payout Ends */
                    if ($post_arr['payment_method'] != "Paypal" && $post_arr['payment_method'] != "Bitgo") {
                        $result = $this->payout_model->updatePayoutReleaseRequest($request['user_name'], $user_id, $payout_release_amount, $release_req_type, $post_arr['payment_method']);
                        //if ($check_status == 'yes') {
                        $this->sendMailtouser($user_id, $type);
                        // $this->sendPayoutSMStoUser($user_id, $payout_release_amount);
                        //}
                    }
                }
            } else {
                echo json_encode([
                    'status' => 'failed',
                    'error_type' => 'unknown',
                    'message' => lang('please_select_payout'),
                ]);die;
            }

            /* Paypal Payout Transfer Begin */
            if ($post_arr['payment_method'] == "Paypal") {
                $result = $this->PayPalPayout($payout_release, $total_amount, $base_call, $paypal_arr, $payout_release_arr, $type, $release_req_type);
            }
            /* Paypal Payout Transfer Ends */

            /* Bitgo Payout Continue */
            if ($post_arr['payment_method'] == "Bitgo") {
                $this->BitgoPayout($user_id, $payout_release, $total_amount, $bitgo_arr, $btc_transaction_fee, $request_id, $payout_release_type, $type, $release_req_type, $post_arr['payment_method']);
            }
            /* Bitgo Payout Ends */
            if ($result) {
                $login_id = $this->LOG_USER_ID;
                $this->validation_model->insertUserActivity($user_id, 'release payout', $login_id);

                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'payout_release', 'Payout Released');
                }
                echo json_encode([
                    'status' => 'success',
                    'message' => lang('Payout_Released_Successfully'),
                ]);die;
            } else {
                echo json_encode([
                    'status' => 'failed',
                    'error_type' => 'unknown',
                    'message' => lang('Payout_Release_Failed'),
                ]);die;
            }
        }
    }

    public function payout_requests_release_action()
    {
        $min_max_payout_amount = $this->payout_model->getMinimumMaximunPayoutAmount();
        if ($this->input->post()) {
            $post_arr = $this->input->post(null, true);
            $post_arr = $this->validation_model->stripTagsPostArray($post_arr);
            $otp_stat = $this->getOtpStat(true);
            if ($otp_stat) {
                $otp = $post_arr['otp'] ?? false;
                if ($otp) {
                    if (!empty($this->session->userdata('payout_otp'))) {

                        if ($otp == $this->session->userdata('payout_otp')) {
                            $this->session->unset_userdata('payout_otp');
                        } else {
                            echo json_encode([
                                'status' => 'failed',
                                'error_type' => 'unknown',
                                'message' => lang('invalid_otp'),
                            ]);die;
                        }
                    } else {
                        echo json_encode([
                            'status' => 'failed',
                            'error_type' => 'unknown',
                            'message' => lang('otp_expired'),
                        ]);die;
                    }
                } else {
                    echo json_encode([
                        'status' => 'failed',
                        'error_type' => 'unknown',
                        'message' => lang('otp_required'),
                    ]);die;
                }
            }
            $payout_release_type = $this->MODULE_STATUS['payout_release_status'];
            $kyc_status = $this->MODULE_STATUS['kyc_status'];
            $result = false;
            $j = 0;
            $payout_release_arr = array();
            $payout_release = array();
            $paypal_arr = array();
            $base_call = '';
            $total_amount = 0;
            if ($post_arr['payment_method'] == "Blockchain" && $this->validate_payment_method()) {
                $blockchain_details = $this->payout_optional_model->getBlockchainDetails();
                $blockchain_details['main_password'] = null;
                $blockchain_details['second_password'] = null;
                $blockchain_details['main_password'] = $this->input->post('main_password', true);
                $blockchain_details['second_password'] = $this->input->post('second_password', true);

                if (empty($blockchain_details)) {
                    echo json_encode([
                        'status' => 'failed',
                        'error_type' => 'unknown',
                        'message' => lang('please_fill_up_your_blockchain_details'),
                    ]);die;
                }

                $system_currency = $this->DEFAULT_CURRENCY_CODE;
                $all_bitcoin_rates = $this->get_rate_for_one_bitcoin();
                $system_bitcoin_rates = $all_bitcoin_rates['USD'];

                $recent_bitcoin_rate = $system_bitcoin_rates['last'];
                $btc_transaction_fee = $blockchain_details['fee'] / 100000000;
                $admin_wallet_balance = $this->get_admin_wallet_balance($blockchain_details);

                if (!$admin_wallet_balance) {
                    echo json_encode([
                        'status' => 'failed',
                        'error_type' => 'unknown',
                        'message' => lang('please_start_blockchain_wallet_service'),
                    ]);die;

                    /* To Start Blockchain Wallet Service :
                 *  Open terminal
                 *  blockchain-wallet-service start --port 3000
                 *                      */
                }
                if (isset($admin_wallet_balance['error']) && $admin_wallet_balance['error'] == "Not found") {
                    echo json_encode([
                        'status' => 'failed',
                        'error_type' => 'unknown',
                        'message' => lang('invalid_blockchain_wallet_config'),
                    ]);die;
                }
                $admin_btc_balance = $admin_wallet_balance['balance'] / 100000000;
            }

            // Bitgo
            if ($post_arr['payment_method'] == "Bitgo" && $this->validate_payment_method()) {
                $bitgo_details = $this->payout_optional_model->getBitgoDetails();
                $bitgo_details['wallet_id'] = null;
                $bitgo_details['passphrase'] = null;
                $bitgo_details['wallet_id'] = $this->input->post('wallet_id', true);
                $bitgo_details['passphrase'] = $this->input->post('passphrase', true);
                if (empty($bitgo_details)) {
                    echo json_encode([
                        'status' => 'failed',
                        'error_type' => 'unknown',
                        'message' => lang('please_fill_up_your_bitgo_details'),
                    ]);die;
                }

                $service_status = $this->bitgo_service_status($bitgo_details);
                if (!$service_status) {
                    echo json_encode([
                        'status' => 'failed',
                        'error_type' => 'unknown',
                        'message' => lang('please_start_bitgo_wallet_service'),
                    ]);die;
                    /* To Start BitGo Wallet Service :
                 *  Open terminal
                 *  cd BitGoJS
                 *  cd bin
                 *  ./bitgo-express --debug --port 3080 --env test --bind localhost
                 *                      */
                }

                $system_currency = $this->DEFAULT_CURRENCY_CODE;
                $all_bitcoin_rates = $this->get_rate_for_one_bitcoin();
                $system_bitcoin_rates = $all_bitcoin_rates['USD'];
                $bitgo_details['fee'] = 10;
                $recent_bitcoin_rate = $system_bitcoin_rates['last'];
                $btc_transaction_fee = $bitgo_details['fee'] / 100000000;
            }

            $type = 'payout_release';
            //$check_status = $this->mail_model->checkMailStatus($type);

            if (!empty($post_arr['payouts'])) {
                foreach ($this->input->post('payouts') as $request) {
                    if ($this->input->post('payout_type') == "user") {
                        $user_id = $this->payout_model->getPayoutRequestUserId($request['user_name']);
                        $user_name = $this->validation_model->IdToUserName($user_id);

                    } elseif ($this->input->post('payout_type') == "admin") {
                        $user_id = $this->validation_model->userNameToID($request['user_name']);
                        $user_name = $request['user_name'];

                    }
                    $user_id = $this->validation_model->userNameToID($user_name);
                    if (!$user_id) {
                        echo json_encode([
                            'status' => 'failed',
                            'error_type' => 'unknown',
                            'message' => lang('invalid_user_name'),
                        ]);die;
                    }
                    $request_details = $this->payout_model->getRequestPayoutDetails($request['user_name']);
                    $post_arr['payment_method'] = $request_details['payment_method'] ?? '';
                    if(!$post_arr['payment_method'] || $request_details['status'] != 'pending') {
                        echo json_encode([
                            'status' => 'failed',
                            'error_type' => 'unknown',
                            'message' => lang('Payout_Release_Failed'),
                        ]);die;
                    }

                    if ($kyc_status == 'yes') {
                        $kyc_upload = $this->validation_model->checkKycUpload($user_id);
                        if ($kyc_upload != 'yes') {
                            echo json_encode([
                                'status' => 'failed',
                                'error_type' => 'unknown',
                                'message' => lang('kyc_not_uploaded_for') . $user_name,
                            ]);die;
                        }
                    }

                    $payout_release_amount = 0;
                    $payout_fee = 0;
                    $userWalletDeduct = 0;
                    //dd('here');
                    if ($this->input->post('payout_type') == "user") {
                        $payout_release_details = $this->payout_model->getPayoutRequestById($request['user_name']);
                        $payout_release_amount = $payout_release_details["requested_amount_balance"];
                        $payout_fee = $payout_release_details["payout_fee"];
                    } elseif ($this->input->post('payout_type') == "admin") {
                        $payout_release_amount = round((floatval($request['amount']) / $this->DEFAULT_CURRENCY_VALUE), 8);
                        $payout_fee = $this->payout_model->calculatePayoutFee($payout_release_amount);
                        $userWalletDeduct = $payout_release_amount + $payout_fee;
                    }

                    if ($payout_release_amount > $min_max_payout_amount['max_payout'] || $payout_release_amount < $min_max_payout_amount['min_payout']) {
                        echo json_encode([
                            'status' => 'failed',
                            'error_type' => 'unknown',
                            'message' => lang('You_cant_release_this_amount_for') . ' ' . $user_name,
                        ]);die;
                    }
                    if ($this->input->post('payout_type') == "admin") {
                        $request_id = $user_id;
                        $release_req_type = "from_ewallet";
                        $balance_amount = $this->payout_model->getUserBalanceAmount($user_id);
                        if ($userWalletDeduct > $balance_amount) {
                            echo json_encode([
                                'status' => 'failed',
                                'error_type' => 'unknown',
                                'message' => lang('Payout_Release_Failed') . " \n" . lang('Low_balance'),
                            ]);die;
                        }
                    }
                    if ($this->input->post('payout_type') == "user") {
                        $release_req_type = "ewallet_request";
                        //dump('here');
                        $payout_request_balance_amount = $this->payout_model->getPayoutRequestById($request['user_name']);
                        //dd('here2');
                        if ($payout_release_amount > $payout_request_balance_amount) {
                            echo json_encode([
                                'status' => 'failed',
                                'error_type' => 'unknown',
                                'message' => lang('You_cant_release_this_amount_for') . ' ' . $user_name,
                            ]);die;
                        }
                    }
                    if ($post_arr['payment_method'] == "Bank Transfer" && $release_req_type == "from_ewallet") {
                        $res = $this->payout_model->updateUserBalanceAmount($user_id, $userWalletDeduct);
                    }
                    /* Payment Gateway Payout Begin */
                    elseif ($post_arr['payment_method'] == "Blockchain") {
                        $this->BlockchainPayout($user_id, $payout_release_amount, $request, $recent_bitcoin_rate, $blockchain_details, $btc_transaction_fee, $system_currency, $payout_release_type, $release_req_type, $admin_btc_balance);
                    } elseif ($post_arr['payment_method'] == "Bitgo") {
                        $btc_send_amount = round($payout_release_amount / $recent_bitcoin_rate, 8);
                        $user_bitgo_wallet_address = $this->payout_optional_model->getUserBitgoWalletAddress($user_id);
                        if ($user_bitgo_wallet_address == '' || $user_bitgo_wallet_address == 'NA') {
                            $msg = lang('invalid_bitgo_wallet_address');
                            $this->redirect($msg, 'payout/payout_release' . get_previous_url_query_string(), false);
                        }

                        // $requested_date = $post_arr['requested_date' . $i];
                        $request_details = $this->payout_model->getRequestPayoutDetails($request);
                        $requested_date = $request_details['requested_date'];
                        $total_amount = $total_amount + $btc_send_amount;
                        $bitgo_arr[$j]['amount'] = $btc_send_amount;
                        $bitgo_arr[$j]['amount_payable'] = $payout_release_amount;
                        $bitgo_arr[$j]['user_id'] = $user_id;
                        $bitgo_arr[$j]['payout_release_amount'] = $payout_release_amount;
                        $bitgo_arr[$j]['payout_release_type'] = $release_req_type;
                        $bitgo_arr[$j]['requested_date'] = $requested_date;
                        $bitgo_arr[$j]['req_id'] = $request;
                        $bitgo_arr[$j]['address'] = $user_bitgo_wallet_address;

                        $payout_release['recipients'][$j]['address'] = $user_bitgo_wallet_address;
                        $payout_release['recipients'][$j]['amount'] = $btc_send_amount * 100000000;
                        $j = $j + 1;
                    } elseif ($post_arr['payment_method'] == "Paypal") {
                        $user_email = $this->payout_optional_model->getUserPayPalEmail($user_id);
                        if ($user_email == "NA" || $user_email == "NULL" || $user_email == "") {
                            echo json_encode([
                                'status' => 'failed',
                                'error_type' => 'unknown',
                                'message' => "Pay email of $user_name not provided",
                            ]);die;
                        } else {
                            $id = urlencode(2040);
                            $requested_date = $request_details['requested_date'];
                            $paypal_arr[$j]['user_id'] = $user_id;
                            array_push($payout_release_arr, $user_name);
                            array_push($payout_release_arr, $user_email);
                            array_push($payout_release_arr, $payout_release_amount);
                            $base_call .= "&L_EMAIL$j=" . urlencode($user_email) .
                            "&L_AMT$j=" . urlencode($payout_release_amount) .
                            "&L_UNIQUEID$j=" . urlencode($id) .
                            "&L_NOTE$j=" . urlencode('Payout Realease') .
                            "&EMAILSUBJECT$j=" . urlencode('Payout Realease') .
                            "&RECEIVERTYPE$j=" . urlencode('Payout Realease') .
                                "&CURRENCYCODE=" . 'USD';

                            $total_amount = $total_amount + $payout_release_amount;
                            $payout_release[$j]['user_id'] = $user_id;
                            $payout_release[$j]['payout_release_amount'] = $payout_release_amount;
                            $payout_release[$j]['payout_release_type'] = $release_req_type;
                            $payout_release[$j]['requested_date'] = $requested_date;
                            $payout_release[$j]['req_id'] = $request['user_name'];
                            $j = $j + 1;
                        }
                    }

                    /* Payment Gateway Payout Ends */
                    if ($post_arr['payment_method'] != "Paypal" && $post_arr['payment_method'] != "Bitgo") {
                        $result = $this->payout_model->updatePayoutReleaseRequest($request['user_name'], $user_id, $payout_release_amount, $release_req_type, $post_arr['payment_method']);
                        //if ($check_status == 'yes') {
                        $this->sendMailtouser($user_id, $type);
                        // $this->sendPayoutSMStoUser($user_id, $payout_release_amount);
                        //}
                    }
                }
            } else {
                echo json_encode([
                    'status' => 'failed',
                    'error_type' => 'unknown',
                    'message' => lang('please_select_payout'),
                ]);die;
            }

            /* Paypal Payout Transfer Begin */
            if ($post_arr['payment_method'] == "Paypal") {
                $result = $this->PayPalPayout($payout_release, $total_amount, $base_call, $paypal_arr, $payout_release_arr, $type, $release_req_type);
            }
            /* Paypal Payout Transfer Ends */

            /* Bitgo Payout Continue */
            if ($post_arr['payment_method'] == "Bitgo") {
                $this->BitgoPayout($user_id, $payout_release, $total_amount, $bitgo_arr, $btc_transaction_fee, $request_id, $payout_release_type, $type, $release_req_type, $post_arr['payment_method']);
            }
            /* Bitgo Payout Ends */
            if ($result) {
                $login_id = $this->LOG_USER_ID;
                $this->validation_model->insertUserActivity($user_id, 'release payout', $login_id);

                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'payout_release', 'Payout Released');
                }
                echo json_encode([
                    'status' => 'success',
                    'message' => lang('Payout_Released_Successfully'),
                ]);die;
            } else {
                echo json_encode([
                    'status' => 'failed',
                    'error_type' => 'unknown',
                    'message' => lang('Payout_Release_Failed'),
                ]);die;
            }
        }
    }

    public function process_payout_action()
    {
        if ($this->input->post('payouts')) {
            $result = false;
            $post_arr = $this->input->post('payouts');
            foreach ($post_arr as $paid_id) {
                $paid_details = $this->payout_model->getPaidPayoutDetails($paid_id);
                $result = $this->payout_model->updateBankTransactionStatus($paid_id, $paid_details['paid_user_id'], $paid_details['paid_amount']);
                if ($result) {
                    $data_array['paid_id'] = $paid_id;
                    $data_array['paid_amount'] = $paid_details['paid_amount'];
                    $data_array['user_name'] = $paid_details['user_name'];
                    $data_array['user_id'] = $paid_details['paid_user_id'];
                    $data = serialize($data_array);
                    $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Payout marked as paid ', $this->LOG_USER_ID, $data);

                    // Employee Activity History
                    if ($this->LOG_USER_TYPE == 'employee') {
                        $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'Payout marked as paid for ' . $data_array['user_name'] . ' and amount is ' . $data_array['paid_amount'], 'Payout Marked as paid');
                    }
                }
            }
            if ($result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => lang('payout_marked_as_paid'),
                ]);die;
            } else {
                echo json_encode([
                    'status' => 'failed',
                    'error_type' => 'unknown',
                    'message' => lang('payout_marking_failed'),
                ]);die;
            }
        }
    }

    public function payout_requests_delete_action()
    {
        $post_arr = $this->input->post(null, true);
        if (!empty($post_arr['payouts'])) {
            foreach ($this->input->post('payouts') as $request) {
                $user_id = $this->payout_model->getPayoutRequestUserId($request);
                $user_name = $this->validation_model->IdToUserName($user_id);
                // dd($user_name);
                if (!$user_id) {
                    echo json_encode([
                        'status' => 'failed',
                        'error_type' => 'unknown',
                        'message' => lang('invalid_user_name'),
                    ]);die;
                }
                $res = $this->payout_model->deletePayoutRequest($request, $user_id);
                if ($res) {
                    $this->validation_model->insertUserActivity($this->LOG_USER_ID, lang('payout_request_deleted'), $this->LOG_USER_ID);
                    if ($this->LOG_USER_TYPE == 'employee') {
                        $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $user_id, 'delete_payout_request', 'Payout Request Deleted');
                    }
                } else {
                    echo json_encode([
                        'status' => 'failed',
                        'error_type' => 'unknown',
                        'message' => lang('Error_on_deleting_Payout_Request'),
                    ]);die;
                }
            }
            echo json_encode([
                'status' => 'success',
                'message' => lang('Payout_Request_Deleted_Successfully'),
            ]);die;
        } else {
            echo json_encode([
                'status' => 'failed',
                'error_type' => 'unknown',
                'message' => lang('please_select_payout'),
            ]);die;
        }
    }

    // Old code start here 11/09/20
    public function payout_release()
    {
        $this->payout_model->deActivateExpiredPayoutRequests();
        //Set Header Data
        $title = $this->lang->line('payout_release');
        $help_link = 'release-payout';
        $this->set("title", $this->COMPANY_NAME . " | " . $title);
        $this->set('help_link', $help_link);
        $this->HEADER_LANG['page_top_header'] = lang('payout_release');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('payout_release');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();

        $payout_release = $this->configuration_model->getPayOutTypes();

        if ($payout_release == 'from_ewallet') {

            $payout_type = 'admin';

        } else if ($payout_release == 'ewallet_request') {

            $payout_type = 'user';

        } else if ($payout_release == 'both') {
            $payout_type = 'admin';
        }

        // Data
        $payment_method = $this->payout_model->gatewayList();
        $payment_type = $this->input->get('payment_method') ?: 'Bank Transfer';
        $payout_type = $this->input->get('payout_type') ?: $payout_type;
        $user_name = $this->input->get('user_name') ?: '';
        $user_id = $this->validation_model->userNameToID($user_name);

        if ($user_name != "" && empty($user_id)) {
            $msg = lang('invalid_username');
            $this->redirect($msg, 'admin/payout_release' . get_previous_url_query_string(), false);
        }

        if ($payout_type == "admin") {
            $payout_requests = $this->payout_model->getPayoutDetails('from_ewallet', '', '', $payment_type, $user_name);
        } else {
            $payout_requests = $this->payout_model->getPayoutDetails('ewallet_request', '', '', $payment_type, $user_name);
        }

        // Pagination
        $count = count($payout_requests);
        $this->pagination->set_all('admin/payout_release', $count);

        $start = $this->input->get('offset') ?: 0;
        $payout_requests = array_slice($payout_requests, $start, $this->PAGINATION_PER_PAGE);

        // Data to View
        $this->set('payout_release', $payout_release);
        $this->set('user_name', $user_name);
        $this->set('gateway_list', $payment_method);
        $this->set('payment_type', $payment_type);
        $this->set('payout_type', $payout_type);
        $this->set('payout_requests', $payout_requests);
        $this->setView();
    }

    /**
     * [release_or_delete_payout_requests description]
     * @return [type] [description]
     */
    public function release_or_delete_payout_requests()
    {

        if ($this->input->post('action') == "release_payout") {

            $this->post_payout_release();
        } elseif ($this->input->post('action') == "delete_payout") {
            $this->delete_payout_requests();
        }
    }

    public function delete_payout_requests()
    {
        $post_arr = $this->input->post(null, true);
        if (!empty($post_arr['payout_request_id'])) {
            foreach ($this->input->post('payout_request_id') as $request) {
                $user_id = $this->payout_model->getPayoutRequestUserId($request);
                $user_name = $this->validation_model->IdToUserName($user_id);
                if (!$user_id) {
                    $msg = lang('invalid_user_name');
                    $this->redirect($msg, 'payout/payout_release' . get_previous_url_query_string(), false);
                }
                $res = $this->payout_model->deletePayoutRequest($request, $user_id);
                if ($res) {
                    $this->validation_model->insertUserActivity($this->LOG_USER_ID, lang('payout_request_deleted'), $this->LOG_USER_ID);
                    if ($this->LOG_USER_TYPE == 'employee') {
                        $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $user_id, 'delete_payout_request', 'Payout Request Deleted');
                    }
                } else {
                    $msg = lang('Error_on_deleting_Payout_Request');
                    $this->redirect($msg, 'payout/payout_release' . get_previous_url_query_string(), false);
                }
            }
            $msg = lang('Payout_Request_Deleted_Successfully');
            $this->redirect($msg, 'payout/payout_release' . get_previous_url_query_string(), true);
        } else {
            $msg = lang('please_select_payout');
            $this->redirect($msg, 'payout/payout_release' . get_previous_url_query_string(), false);
        }
    }

    public function validate_transation_password()
    {
        $this->form_validation->set_rules('payout_amount', lang('payout_amount'), 'trim|required|numeric');
        $this->form_validation->set_rules('transation_password', lang('transation_password'), 'required');
        $res_val = $this->form_validation->run();
        return $res_val;
    }

    public function user_details($user_name = '')
    {
        $user_id = $this->validation_model->userNameToID($user_name);
        if (!$user_id) {
            exit();
        }
        $user_details = $this->payout_model->getUserDetails($user_id);
        $user_details = $this->security->xss_clean($user_details);

        $this->set('user_details', $user_details);

        $this->setView();
    }

    //mark as paid
    public function mark_paid()
    {
        // HEADER DATA
        $title = $this->lang->line('process_payment');
        $this->set('title', $this->COMPANY_NAME . ' | ' . $title);
        $this->HEADER_LANG['page_top_header'] = lang('process_payment');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('process_payment');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();

        // FILTER
        $date_range = $this->input->get('daterange') ?: 'all';
        $user_name = $this->input->get('user_name') ?: '';
        $from_date = $to_date = "";
        list($from_date, $to_date) = get_daterange($date_range, $this->input->get('from_date'), $this->input->get('to_date'));

        $user_id = $this->validation_model->userNameToID($user_name);
        if ($user_name != "" && empty($user_id)) {
            $msg = lang('invalid_username');
            $this->redirect($msg, 'admin/payout_release' . get_previous_url_query_string(), false);
        }

        // DATA
        $payout_details = $this->payout_model->getReleasedPayout($user_name, $from_date, $to_date, $this->PAGINATION_PER_PAGE, $this->input->get('offset'));

        // PAGINATION
        $count = $this->payout_model->getPayoutCount($user_name, $from_date, $to_date);
        $this->pagination->set_all('admin/mark_paid', $count);

        // SET DATA TO VIEW
        $this->set('daterange', $date_range);
        $this->set('from_date', $from_date);
        $this->set('to_date', $to_date);
        $this->set('user_name', $user_name);
        $this->set('payout_details', $payout_details);
        $this->setView();
    }

    public function confirm_mark_paid()
    {
        if ($this->input->post('payout_paid_id')) {
            $result = false;
            $post_arr = $this->input->post('payout_paid_id');
            foreach ($post_arr as $paid_id) {
                $paid_details = $this->payout_model->getPaidPayoutDetails($paid_id);
                $result = $this->payout_model->updateBankTransactionStatus($paid_id, $paid_details['paid_user_id'], $paid_details['paid_amount']);
                if ($result) {
                    $data_array['paid_id'] = $paid_id;
                    $data_array['paid_amount'] = $paid_details['paid_amount'];
                    $data_array['user_name'] = $paid_details['user_name'];
                    $data_array['user_id'] = $paid_details['paid_user_id'];
                    $data = serialize($data_array);
                    $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Payout marked as paid ', $this->LOG_USER_ID, $data);

                    // Employee Activity History
                    if ($this->LOG_USER_TYPE == 'employee') {
                        $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'Payout marked as paid for ' . $data_array['user_name'] . ' and amount is ' . $data_array['paid_amount'], 'Payout Marked as paid');
                    }
                }
            }
            if ($result) {
                $msg = lang('payout_marked_as_paid');
                $this->redirect($msg, 'payout/mark_paid' . get_previous_url_query_string(), true);
            } else {
                $msg = lang('payout_marking_failed');
                $this->redirect($msg, 'payout/mark_paid' . get_previous_url_query_string(), false);
            }
        }
    }

    public function validate_date_field()
    {
        $this->form_validation->set_rules('start_date', lang('start_date'), 'trim|required|strip_tags');
        $this->form_validation->set_rules('end_date', lang('end_date'), 'trim|required|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    //mark as paid ends

    /* Blockchain Payout Related Functions Begin */
    public function get_rate_for_one_bitcoin()
    {
        $url = "https://blockchain.info/ticker";
        $data = $this->curl($url);
        return $data;
    }

    public function get_admin_wallet_balance($blockchain_details)
    {
        $guid = $blockchain_details['my_api_key'];
        $main_password = $blockchain_details['main_password'];
        $url = "http://localhost:3000/merchant/$guid/balance?password=$main_password";
        $data = $this->curl($url);
        return $data;
    }

    public function send_bitcoin($blockchain_details, $btc_send_amount, $address)
    {

        // $btc_send_amount=0.5;
        $amount = $btc_send_amount * 100000000;
        $guid = $blockchain_details['my_api_key'];
        $main_password = $blockchain_details['main_password'];
        $second_password = $blockchain_details['second_password'];
        $fee = $blockchain_details['fee'];
        $url = "http://localhost:3000/merchant/$guid/payment?password=$main_password&second_password=$second_password&to=$address&amount=$amount&from=0&fee=$fee";
        $data = $this->curl($url);
        return $data;
    }

    public function curl($url)
    {
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'bereal');
        $result = curl_exec($curl_handle);
        curl_close($curl_handle);
        $info = json_decode($result, true);
        return $info;
    }

    public function encrypt_decrypt()
    {
        $data = "rishad";
        $encrypted_data = $this->encryption->encrypt($data);
        echo "Encrypted Data : " . $encrypted_data;
        $decrypted_data = $this->encryption->decrypt($encrypted_data);
        echo "</br>Decrypted Data : " . $decrypted_data;
        die();
    }

    public function test_blockchain()
    {
        $blockchain_details = $this->payout_optional_model->getBlockchainDetails();
        $data = $this->get_admin_wallet_balance($blockchain_details);
        print_r($data);
        die();
    }

    /* Blockchain Payout Related Functions Ends */

    /* Bitgo Payout Related Functions Begins */
    /*
    Run these commands :
    git clone https://github.com/BitGo/BitGoJS
    cd BitGoJS
    npm install
    sudo service apache2 restart
     */

    public function send_bitgo_bitcoin($btc_send_amount = 0, $address)
    {

        $bitgo_details = $this->payout_optional_model->getBitgoDetails();
        $wallet_id = $bitgo_details['wallet_id'];
        $token = $bitgo_details['token'];
        $pasphrase = $bitgo_details['passphrase'];

        /* $token = "v2x43f95c1c82db98d40fc72dfa01f93e2fa4abb0cd40596d417e0e0df2821582eb";
         * Test Tocken with transfer amount greater than zero
         *          */
        //test data{
        $btc_send_amount = 00010000;
        $address = "2N6dmW86GD2yeCGVU6Na1AS18daDgh7bBuR";
        //}

        $data['address'] = $address;
        $data['amount'] = $btc_send_amount;
        $data['walletPassphrase'] = $pasphrase;
        $field_string = json_encode($data);

        $ch = curl_init();
        $url = "http://localhost:3080/api/v1/wallet/$wallet_id/sendcoins";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $headers = array();
        $headers[] = "Authorization: Bearer $token";
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        $info = json_decode($result);
        curl_close($ch);

        return $result;
    }

    public function send_bitgo_mass_payout($recipients_arr = "")
    {

        $bitgo_details = $this->payout_optional_model->getBitgoDetails();
        $wallet_id = $bitgo_details['wallet_id'];
        $token = $bitgo_details['token'];
        $pasphrase = $bitgo_details['passphrase'];

        $recipients_arr['walletPassphrase'] = $pasphrase;
        $field_string = json_encode($recipients_arr);

        $ch = curl_init();
        $url = "http://localhost:3080/api/v1/wallet/$wallet_id/sendmany";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $headers = array();
        $headers[] = "Authorization: Bearer $token";
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        $info = json_decode($result, true);
        curl_close($ch);

        return $info;
    }

    public function bitgo_wallet_balance()
    {

        $data = $this->payout_optional_model->getBitgoDetails();
        $wallet_id = $data['wallet_id'];
        $token = $data['token'];
        if ($data['mode'] == 'test') {
            $url = "https://test.bitgo.com/api/v1/wallet/$wallet_id";
        } else {
            $url = "https://bitgo.com/api/v1/wallet/$wallet_id";
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $headers = array();
        $headers[] = "Authorization: Bearer $token";
        $headers[] = "Content-Type: application/json";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        $info = json_decode($result, true);
        curl_close($ch);
        $balance = $info['balance'] / 100000000;
        return $balance;
    }

    public function bitgo_service_status()
    {
        /*

         * After Install Bitgo-express
         * Open terminal
         * Locate : BitGoJS/bin
         * Run : "./bitgo-express --debug --port 3080 --env test --bind localhost"
         *
         *  Do not Close terminal
         */

        $ch = curl_init();
        $url = "http://localhost:3080/api/v1/ping";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $info = json_decode($result, true);
        curl_close($ch);
        if ($info['status'] == "service is ok!") {
            return true;
        } else {
            return false;
        }

    }

    public function bitgo_trans_approved()
    {
        /*

         * Login your bitgo account & Choose your Wallet.
         * Goto Settings->Developer options->Add Webhook
         * Create One with URL "https://yourdomain.com/{path}/bitgo_trans_approved"
         * Choose "Transaction" as event.
         *
         */
        //test data{
        $response = '{
  "type": "transaction",
  "walletId": "2MwLxgWaAGmMT9asT4nAdeewWzPEz3Sn5Eg",
  "hash": "fd60b6a7da6c56ddc7e55b94bf76f0df7ea34d6fbc07a232f82e2964b94a925e"
}';
        //}

        $decode_res = array();
        $decode_res = json_decode($response, true);
        $data = $this->payout_optional_model->updateTransactionStatus('', '', '', $decode_res['hash']);
    }

    /* Bitgo Payout Related Functions End */

    public function blocktrail_amount_conversion()
    {

        $bitcoin_price = 1;
        $currency = $this->DEFAULT_CURRENCY_CODE;
        if ($currency != "BTC") {
            $url = 'https://bitpay.com/api/rates/' . $currency;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15"));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 500);
            $result = curl_exec($ch);
            curl_close($ch);
            $info = json_decode($result, true);
            $bitcoin_price = $info['rate'];
        }
        return $bitcoin_price;
    }

    /* Blocktrail Related Function Ends */
    /* Optimization Starts */

    public function sendMailtouser($user_id, $type)
    {

        $send_details = array();
        $email = $this->validation_model->getUserEmailId($user_id);
        $send_details['full_name'] = $this->validation_model->getUserFullName($user_id);
        $send_details['email'] = $email;
        $send_details['first_name'] = $this->validation_model->getUserData($user_id, "user_detail_name");
        $send_details['last_name'] = $this->validation_model->getUserData($user_id, "user_detail_second_name");

        $this->mail_model->sendAllEmails($type, $send_details, [], $user_id);
        return true;
    }

    public function BlocktrailPayout($user_id, $payout_release_amount, $payout_release_type, $request_id, $bitcoin_price, $wallet, $release_req_type, $method)
    {
        $sendAmount = round($payout_release_amount / $bitcoin_price, 8);
        $bitcoin_address = $this->payout_model->getBitcoinAddress($user_id);
        if ($bitcoin_address == '' || $bitcoin_address == 'NA') {
            $msg = lang('invalid_bitcoin_address');
            $this->redirect($msg, 'payout/payout_release', false);
        }
        $data['user_id'] = $user_id;
        $data['amount_payable'] = $payout_release_amount;
        $data['from_user'] = $this->ADMIN_USER_ID;
        $data['bitcoin_address'] = $bitcoin_address;
        $data['bitcoin_price'] = $bitcoin_price;
        $data['bitcoin_amount'] = $sendAmount;
        $amount_payable = $payout_release_amount;
        $bitcoin_history_id = $this->register_model->bitcoinHistory($user_id, $data, 'payout_release');
        $txHash = $this->blocktrail_send($bitcoin_history_id, $data, $wallet);
        if ($txHash) {
            $return_address = $bitcoin_address;
            $update_status = $this->payout_model->updatePayoutReleaseRequest($user_id, $request_id, $payout_release_amount, $payout_release_type, $method, $txHash);
            $result = $this->register_model->insertInToBitcoinPaymentDetails($bitcoin_history_id, $user_id, 'payout_released', $payout_release_amount, $bitcoin_price, $sendAmount, $sendAmount, $bitcoin_address, $txHash, $return_address);
            if ($release_req_type == "from_ewallet") {
                $result1 = $this->payout_model->updateUserBalanceAmount($user_id, $payout_release_amount);
            }

            $this->payout_optional_model->updateTransactionStatus($request_id, $user_id, $payout_release_amount, $txHash);
            if ($result) {
                $result = $this->register_model->updateBitcoinHistory($user_id, $bitcoin_history_id, 'yes');
                return true;
            }
        }
        return false;
    }

    public function BlockchainPayout($user_id, $payout_release_amount, $request_id, $recent_bitcoin_rate, $blockchain_details, $btc_transaction_fee, $system_currency, $payout_release_type, $release_req_type, $admin_btc_balance)
    {
        $btc_send_amount = round($payout_release_amount / $recent_bitcoin_rate, 8);
        $user_bitcoin_address = $this->payout_optional_model->getUserBitcoinAddress($user_id);
        if ($user_bitcoin_address == '' || $user_bitcoin_address == 'NA') {
            $msg = lang('invalid_bitcoin_address');
            $this->redirect($msg, 'payout/payout_release' . get_previous_url_query_string(), false);
        }
        $btc_send_response = $this->send_bitcoin($blockchain_details, $btc_send_amount, $user_bitcoin_address);
        if (isset($btc_send_response['success']) && $btc_send_response['success']) {
            $result = true;
            $this->payout_model->commit();
            $message = $btc_send_response['message'];
            $tx_hash = $btc_send_response['tx_hash'];
            $btc_admin_debit = $btc_send_amount + $btc_transaction_fee;
            $admin_btc_balance = $admin_btc_balance - $btc_admin_debit;
            $status = $this->payout_optional_model->insertIntoBitcoinPayoytReleaseHistory($btc_send_response['success'], $message, $user_id, $admin_btc_balance, $payout_release_amount, $system_currency, $recent_bitcoin_rate, $btc_send_amount, $user_bitcoin_address, $btc_send_response, $btc_transaction_fee, $btc_admin_debit, $tx_hash, $payout_release_type);

            if ($release_req_type == "from_ewallet") {
                $result1 = $this->payout_model->updateUserBalanceAmount($user_id, $payout_release_amount);
            }

            $this->payout_optional_model->updateTransactionStatus($request_id, $user_id, $payout_release_amount);
        } else {
            $this->payout_model->rollback();
            $message = $btc_send_response['error'];
            $status = $this->payout_optional_model->insertIntoBitcoinPayoytReleaseHistory(0, $message, $user_id, $admin_btc_balance, $payout_release_amount, $system_currency, $recent_bitcoin_rate, $btc_send_amount, $user_bitcoin_address, $btc_send_response, $btc_transaction_fee, '', '', $payout_release_type);
            $this->redirect($message, 'payout/payout_release' . get_previous_url_query_string(), false);
        }
    }

    public function BitgoPayout($user_id, $payout_release, $total_amount, $bitgo_arr, $btc_transaction_fee, $request_id, $payout_release_type, $type, $release_req_type, $method)
    {

        $count_pay_release = count($payout_release);
        if ($count_pay_release) {
            $bitgo_btc_send_response = $this->send_bitgo_mass_payout($payout_release);

            if (isset($bitgo_btc_send_response['status']) && $bitgo_btc_send_response['status'] == "accepted") {
                $result = true;
                $this->payout_model->commit();
                $count_bitgo_release = 0;
                $admin_btc_balance = $this->bitgo_wallet_balance();
                if ($admin_btc_balance < $total_amount) {
                    $message = lang('not_enough_balance_in_bitgo_wallet');
                    $this->redirect($message, 'payout/payout_release', false);
                }
                while ($count_bitgo_release < count($bitgo_arr)) {
                    $btc_admin_debit = $bitgo_arr[$count_bitgo_release]['amount'] + $btc_transaction_fee;
                    $admin_btc_balance = $admin_btc_balance - $btc_admin_debit;
                    $status = $this->payout_optional_model->insertIntoBitGoPayoutHistory($bitgo_btc_send_response, $bitgo_arr[$count_bitgo_release], $admin_btc_balance, $btc_transaction_fee, $btc_admin_debit);

                    if ($release_req_type == "from_ewallet") {
                        $result1 = $this->payout_model->updateUserBalanceAmount($bitgo_arr[$count_bitgo_release]['user_id'], $bitgo_arr[$count_bitgo_release]['payout_release_amount']);
                    }

                    $result = $this->payout_model->updatePayoutReleaseRequest($request_id, $bitgo_arr[$count_bitgo_release]['user_id'], $bitgo_arr[$count_bitgo_release]['payout_release_amount'], $release_req_type, $method, $bitgo_btc_send_response['hash']);
                    $count_bitgo_release++;
                }
                $cnt = count($bitgo_arr);
                if ($cnt) {
                    for ($i = 0; $i < $cnt; $i++) {
                        $this->sendMailtouser($bitgo_arr[$i]['user_id'], $type);
                        $this->sendPayoutSMStoUser($bitgo_arr[$i]['user_id'], $total_amount);
                    }
                }
            } elseif (isset($bitgo_btc_send_response['error'])) {
                $this->payout_model->rollback();
                $data = array(
                    'bitcoin_history_id' => 0,
                    'from_user' => $this->ADMIN_USER_ID,
                    'to_user_id' => $user_id,
                    // 'amount_payable' => $payout_release[0]['amount_payable'],
                    'error_reason' => $bitgo_btc_send_response['message'],
                    'date' => date('Y-m-d h:i:s'),
                    'bitcoin_amount' => $payout_release['recipients'][0]['amount'],
                    'bitcoin_address' => $payout_release['recipients'][0]['address'],
                    'payment_type' => "Bitgo",
                );
                $this->db->insert('bitcoin_payout_release_error_report', $data);

                $message = lang('error_occurred') . $bitgo_btc_send_response['message'];
                $this->redirect($message, 'payout/payout_release' . get_previous_url_query_string(), false);
            }
        }
    }

    public function PayPalPayout($payout_release, $total_amount, $base_call, $paypal_arr, $payout_release_arr, $type)
    {
        $count_pay_release = count($payout_release);

        if ($count_pay_release) {
            $paypal_balance = $this->payout_optional_model->PayPalBalance();

            if ($paypal_balance < $total_amount) {
                $this->redirect(lang('not_enough_balance_in_paypal'), "payout/payout_release" . get_previous_url_query_string(), false);
            } else {
                $result = $this->payout_optional_model->massPay($base_call, $payout_release, $total_amount);
                if ($result) {
                    $cnt = count($paypal_arr);
                    if ($cnt) {
                        for ($i = 0; $i < $cnt; $i++) {
                            $this->sendMailtouser($paypal_arr[$i]['user_id'], $type);
                            $this->sendPayoutSMStoUser($paypal_arr[$i]['user_id'], $total_amount);
                        }
                    }
                    $this->payout_optional_model->massPayoutHistory($payout_release_arr);
                    return true;
                } else {
                    $this->redirect(lang('Payout_Release_Failed'), "payout/payout_release" . get_previous_url_query_string(), false);
                }
            }
        }
    }

    /* Optimization Ends */

    public function validate_payment_method()
    {
        $res_val = false;
        $msg = "";
        $payment = '';
        if ($this->input->post('payment_method') == "Blockchain") {
            $payment = "Blockchain";
            $this->form_validation->set_rules('main_password', lang('main_password'), 'trim|required');
            $this->form_validation->set_rules('second_password', lang('second_password'), 'trim|required');
            $msg = lang('please_fill_up_your_blockchain_details');
        }
        if ($this->input->post('payment_method') == "Bitgo") {
            $payment = "Bitgo";
            $this->form_validation->set_rules('wallet_id', lang('wallet_id'), 'trim|required');
            $this->form_validation->set_rules('passphrase', lang('passphrase'), 'trim|required');
            $msg = lang('please_fill_up_your_bitgo_details');
        }
        if ($this->input->post('payment_method') == "Bitcoin") {
            $payment = "Bitcoin";
            $this->form_validation->set_rules('wallet_name', lang('wallet_name'), 'trim|required');
            $this->form_validation->set_rules('wallet_password', lang('wallet_password'), 'trim|required');
            $msg = lang('please_fill_up_your_blocktrail_details');
        }
        $res_val = $this->form_validation->run();

        if ($res_val) {
            return $res_val;
        } else {
            $this->redirect($msg, "payout/payout_release" . get_previous_url_query_string(), false);
        }
    }

    public function payoutOtpModal()
    {
        $status = false;
        $otp = rand(pow(10, 4), pow(10, 5) - 1);
        if ($otp) {
            if (!empty($this->session->userdata('payout_otp'))) {
                $this->session->unset_userdata('payout_otp');
            }

            $type = lang('payout_release');
            $this->mail_model->sendOtpMail($otp, $this->validation_model->getUserEmailId($this->validation_model->getAdminId()), $type);
            $this->session->set_userdata('payout_otp', $otp);
            echo $status = true;
            exit;
        } else {
            echo $status;
            exit;
        }
    }

    public function my_withdrawal_request($tab = 'tab1')
    {
        $this->payout_model->deActivateExpiredPayoutRequests();
        // Set Header Data
        $title = lang('my_withdrawal_status');
        $this->set("title", $this->COMPANY_NAME . " | $title");
        $this->HEADER_LANG['page_top_header'] = lang('my_withdrawal_status');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('my_withdrawal_status');
        $this->HEADER_LANG['page_small_header'] = '';
        $help_link = "my_withdrawal_status";
        $this->set("help_link", $help_link);
        $this->load_langauge_scripts();

        // Set Tab Data
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
                $tab1 = 'checked';
        }
        $user_name = $this->input->get('user_name') ?: '';
        $user_id = $this->validation_model->userNameToID($user_name);
        if (!empty($user_name) && empty($user_id)) {
            $msg = lang('invalid_username');
            $this->redirect($msg, 'my_withdrawal_request', false);
        }

        $offset = $this->input->get('offset');

        // Tab1 Pagination
        $total_rows1 = $this->payout_model->getPayoutWithdrawalCount($user_id, 'pending');
        $_GET['offset'] = $tab == 'tab1' ? $offset : 0;
        $page1 = $tab == 'tab1' ? $this->input->get('offset1') : 0;
        $this->pagination->set_all('admin/my_withdrawal_request/tab1', $total_rows1);
        $result_per_page1 = $this->pagination->create_links();
        $_GET['offset'] = $offset;
        $this->set("result_per_page1", $result_per_page1);
        $this->set("page1", $page1);

        // Tab2 Pagination
        $total_rows2 = $this->payout_model->getReleasedWithdrawalCount($user_id, 'approved_pending');
        $_GET['offset'] = $tab == 'tab2' ? $offset : 0;
        $page2 = $tab == 'tab2' ? $offset : 0;
        $this->pagination->set_all('admin/my_withdrawal_request/tab2', $total_rows2);
        $result_per_page2 = $this->pagination->create_links();
        $_GET['offset'] = $offset;
        $this->set("result_per_page2", $result_per_page2);
        $this->set("page2", $page2);

        // Tab3 Pagination
        $total_rows3 = $this->payout_model->getReleasedWithdrawalCount($user_id, 'approved_paid');
        $_GET['offset'] = $tab == 'tab3' ? $offset : 0;
        $page3 = $tab == 'tab3' ? $offset : 0;
        $this->pagination->set_all('admin/my_withdrawal_request/tab3', $total_rows3);
        $result_per_page3 = $this->pagination->create_links();
        $_GET['offset'] = $offset;
        $this->set("result_per_page3", $result_per_page3);
        $this->set("page3", $page2);

        // Tab4 Pagination
        $total_rows4 = $this->payout_model->getPayoutWithdrawalCount($user_id, 'deleted');
        $_GET['offset'] = $tab == 'tab4' ? $offset : 0;
        $page4 = $tab == 'tab4' ? $offset : 0;
        $this->pagination->set_all('admin/my_withdrawal_request/tab4', $total_rows4);
        $result_per_page4 = $this->pagination->create_links();
        $_GET['offset'] = $offset;
        $this->set("result_per_page4", $result_per_page4);
        $this->set("page4", $page4);

        // Table Data
        $active_requests = $this->payout_model->getPayoutWithdrawalDetails($user_id, 'pending', $this->PAGINATION_PER_PAGE, $page1);
        $waiting_requests = $this->payout_model->getReleasedWithdrawalDetails($user_id, 'approved_pending', $this->PAGINATION_PER_PAGE, $page2);
        $paid_requests = $this->payout_model->getReleasedWithdrawalDetails($user_id, 'approved_paid', $this->PAGINATION_PER_PAGE, $page3);
        $rejected_requests = $this->payout_model->getPayoutWithdrawalDetails($user_id, 'deleted', $this->PAGINATION_PER_PAGE, $page4);

        $total_amount_active_request = $this->payout_model->getTotalAmountPendingRequest('pending');
        $total_amount_waiting_requests = $this->payout_model->getTotalAmountApproved();
        $total_amount_paid_request = $this->payout_model->getTotalAmountPaid();
        $total_amount_rejected_requests = $this->payout_model->getTotalAmountRejected('deleted');
        // Data to view
        $this->set("base_url", $this->BASE_URL);
        $this->set("active_requests", $this->security->xss_clean($active_requests));
        $this->set("waiting_requests", $this->security->xss_clean($waiting_requests));
        $this->set("paid_requests", $this->security->xss_clean($paid_requests));
        $this->set("rejected_requests", $this->security->xss_clean($rejected_requests));
        $this->set('total_amount_active_request', $total_amount_active_request);
        $this->set('total_amount_waiting_requests', $total_amount_waiting_requests);
        $this->set('total_amount_paid_request', $total_amount_paid_request);
        $this->set('total_amount_rejected_requests', $total_amount_rejected_requests);
        // Active Tab to view
        $this->set('tab1', $tab1);
        $this->set('tab2', $tab2);
        $this->set('tab3', $tab3);
        $this->set('tab4', $tab4);

        $this->set('user_name', $user_name);

        $this->setView();
    }

    public function search_member_withdrawal()
    {
        if ($this->input->post('search_member_submit')) {
            $user_name = $this->input->post('user_name', true);
            $user_id = $this->validation_model->userNameToID($user_name);
            if ($user_id) {
                $this->session->set_flashdata('username', $user_name);
                $this->redirect('', 'payout/my_withdrawal_request', true);
            } else {
                $msg = lang('invalid_username');
                $this->redirect($msg, 'payout/my_withdrawal_request', false);
            }
        }
    }
    public function getOtpStat($flag = false)
    {
        if ($flag) {
            return ($this->validation_model->getModuleStatusByKey('otp_modal') == "yes") ? true : false;
        } else {
            echo $this->validation_model->getModuleStatusByKey('otp_modal');
            exit();
        }
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

        $user_id = $this->payout_model->getPayoutReleasedUseridFromPaidID($paid_id);
        $user_details = $this->payout_model->getUserDeatilsForInvoice($user_id);
        $payout_details = $this->payout_model->getPayoutDetailsFromAmountPaid($paid_id);
        $invoice_number = "PR000" . $paid_id;
        $this->set('invoice_number', $invoice_number);
        $this->set('user_details', $user_details);
        $this->set('payout_details', $payout_details);

        $this->setView();

    }

    public function sendPayoutSMStoUser($user_id, $payout_release_amount)
    {
        if ($this->MODULE_STATUS["sms_status"] == "yes") {
            $this->load->model("sms_model");
            $mobile = $this->validation_model->getUserPhoneNumber($user_id);
            $variableArray = [
                "fullname" => $this->validation_model->getUserFullName($user_id),
                "company_name" => $this->COMPANY_NAME,
                "amount" => $payout_release_amount,
            ];
            $langId = $this->validation_model->getUserDefaultLanguage($user_id);
            $type = "payout_release";

            $this->sms_model->createAndSendSMS($langId, $type, $mobile, $variableArray);
        }
        return true;
    }

}
