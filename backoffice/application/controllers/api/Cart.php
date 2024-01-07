<?php

require_once 'Inf_Controller.php';

class Cart extends Inf_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('repurchase_model');
        $this->load->model('report_model');
        // $this->load->library('internal_cart', ['user_id' => $this->rest->user_id]);
        $this->load->library('internal_cart', ['user_id' => $this->rest->user_id], 'cart');
    }
    public function products_post()
    {
        if ($this->validate_product()) {
            $data = $this->input->post(null, true);
            $details = $this->Api_model->packageCart($data['product_id']);
            if (!empty($details)) {
                $details = array_intersect_key($details[0], array_flip(['id', 'name', 'amount']));
                $details['qty'] = $data['quantity'];
                $details['price'] = $details['amount'];
                unset($details['amount']);
                if ($this->internal_cart->insert($details)) {
                    $this->set_success_response(204);
                } else {
                    $this->set_error_response(500);
                }
            } else {
                $this->set_error_response(422, 1012);
            }
        } else {
            $this->set_error_response(422, 1004);
        }
    }
    public function validate_product()
    {
        $this->form_validation->set_rules('product_id', lang('product_id'), 'trim|required');
        $this->form_validation->set_rules('quantity', lang('quantity'), 'trim|required|is_natural_no_zero');

        $validation_status = $this->form_validation->run();
        return $validation_status;
    }
    public function products_delete($all = null)
    {
        if($all){
            if($all = 'all'){
                $this->internal_cart->destroy();
                $this->set_success_response(204);
            }else{
                $this->set_error_response(405);
            }
        }else{
            $data = $this->delete();
            $this->form_validation->set_data($data);
            if ($this->validate_product_delete()) {
                $row_id = $this->internal_cart->get_id($data['product_id']);
                if ($row_id) {
                    if ($this->internal_cart->remove($row_id)) {
                        $this->set_success_response(204);
                    } else {
                        $this->set_error_response(500);
                    }
                } else {
                    $this->set_error_response(422, 1012);
                }
            } else {
                $this->set_error_response(422, 1004);
            }
        }
    }
    public function validate_product_delete()
    {
        $this->form_validation->set_rules('product_id', lang('product_id'), 'trim|required');
        $validation_status = $this->form_validation->run();
        return $validation_status;
    }
    public function shipping_address_delete()
    {
        $this->query('address_id') ? (!empty($this->Api_model->delete_address($this->query('address_id'),$this->rest->user_id)) ? $this->set_success_response(204): $this->set_error_response(422,1023)) : $this->set_error_response(400);
    }
    public function shipping_address_get()
    {
        $this->set_success_response(200, ['address_list' => $this->Api_model->getUserPurchaseAddress($this->rest->user_id)]);
    }
    public function shipping_address_post($defualt = null)
    {

        if (isset($defualt) && $defualt == 'defualt' && $this->post('address_id')) {
            $this->repurchase_model->updateDefualtAddress($this->rest->user_id, $this->post('address_id')) ? $this->set_success_response(204) : $this->set_error_response(422, 1023);
        } else {
            if ($this->validate_checkout_address()) {
                $address = $this->input->post(null, true);
                $address['full_name'] = $address['name'];
                $address['pin_no'] = $address['zip_code'];
                unset($address['name'],$address['zip_code']);
                $address['user_id'] = $this->rest->user_id;
                if ($this->Api_model->insert_repurchase_address($address)) {
                    $this->set_success_response(204);
                }else{
                    $this->set_success_response(500);
                }
            } else {
                $this->set_error_response(422, 1004);
            }
        }
    }
    public function validate_checkout_address()
    {
        $this->form_validation->set_rules('name', lang('name'), 'trim|required|min_length[3]|max_length[32]|callback__alpha_space');
        $this->form_validation->set_rules('address', lang('address'), 'trim|required|min_length[3]|max_length[32]');
        $this->form_validation->set_rules('zip_code', lang('zip_code'), 'trim|required|min_length[3]|max_length[10]|is_natural');
        $this->form_validation->set_rules('city', lang('city'), 'trim|required|min_length[3]|max_length[32]|callback__alpha_city_address');
        $this->form_validation->set_rules('phone', lang('phone_number'), 'trim|required|is_natural|min_length[5]|max_length[10]');

        $validation_status = $this->form_validation->run();
        return $validation_status;
    }
    function _alpha_space($str = '')
    {
        if (!$str) {
            return true;
        }
        $res = (bool)preg_match('/^[A-Z ]*$/i', $str);
        if (!$res) {
            $this->lang->load('register');
            $this->form_validation->set_message('_alpha_space', lang('only_alpha_space'));
        }
        return $res;
    }

    function _alpha_city_address($str_in = '')
    {
        if (!preg_match("/^([a-zA-Z0-9\s\.\,\-])*$/i", $str_in)) {
            $this->lang->load('register');
            $this->form_validation->set_message('_alpha_city_address', lang('city_field_characters'));
            return false;
        } else {
            return true;
        }
    }

    function checkout_post() {

        $user_id = $this->rest->user_id;
        $post_arr = $this->validation_model->stripTagsPostArray($this->post());
        $this->form_validation->set_data($post_arr);

        if($this->validate_order_details()) {
            $cart_products = $this->internal_cart->contents();
            $purchase['total_amount'] = $this->internal_cart->total();

            $purchase['user_id'] = $user_id;
            $purchase['order_address_id'] = $this->validation_model->getUserPurchaseDefaultAddressId($user_id);

            if(!$purchase['order_address_id']) {
                $this->set_error_response(422, 1023);
            }

            if (!empty($cart_products)) {
                if($post_arr['payment_method'] == 'free_join') {
                    $purchase['payment_type'] = $post_arr['payment_method'];
                    $purchase['status'] = 'confirmed';

                    $this->repurchase_model->begin();
                    $insert_into_sales = $this->insertIntoRepuchase($cart_products, $purchase);
                    $this->repurchase_model->updateUserPv($cart_products, $purchase, $this->MODULE_STATUS);

                    if (!empty($insert_into_sales)) {
                        if ($this->MODULE_STATUS['rank_status'] == "yes") {
                            $this->load->model('rank_model');
                            $this->rank_model->updateUplineRank($purchase['user_id']);
                        }
                        $this->repurchase_model->commit();
                        $this->internal_cart->destroy();
                        $this->set_success_response(204);
                    } else {
                        $this->repurchase_model->rollback();
                        $this->set_error_response(500);
                    }
                }
            } else {
                $this->set_error_response(422, 1026);
            }
        } else {
            $this->set_error_response(422, 1004);
        }

    }

    public function validate_order_details()
    {
        $this->form_validation->set_rules('payment_method', lang('payment_method'), 'trim|required|in_list[free_join]', ['in_list' => '%s not allowed']);
        return $this->form_validation->run();
    }

    public function insertIntoRepuchase($cart_products, $purchase_details)
    {
        $this->load->model('product_model');
        $orders_details = $this->repurchase_model->insertIntoRepuchaseOrder($purchase_details);
        $order_status = $purchase_details['status'];
        $orders_id = $orders_details['order_id'];
        if ($orders_id) {
            $total_pv = 0;
            foreach ($cart_products as $key => $value) {
                $value['pv'] = $this->product_model->getProductPV($value['id']);
                $total_pv += $value['pv'];
                $order_details = $this->repurchase_model->insertRepurchaseOrderDetails($value, $orders_id, $order_status);
                if (!$order_details) {
                    return false;
                }
            }
            $this->repurchase_model->updateRepurchaseOrderPV($orders_id, $total_pv);
        }
        return $orders_details;
    }

    public function products_get()
    {
        $this->load->model('product_model');
        $user_id = $this->rest->user_id;
        $product_details = $this->internal_cart->contents();
        if($product_details) {
            $data = [];
            $i = 0;
            foreach ($product_details as $key => $value) {
                $data[$i] = [
                    'product_id' => $this->validation_model->getProductidFromProdID($value['id']),
                    'product_name' => $this->product_model->getPackageNameFromPackageId($value['id'], $this->MODULE_STATUS, 'repurchase'),
                    'product_amount' =>  $this->Api_model->getPackagePriceFromPackageId($value['id']),
                    'quantity' => $value['qty'],
                ];
                $i++;
            }
        $response = ['cart_items' => $data];
        $this->set_success_response(200, $response);
        } else {
            $this->set_error_response(422, 1026);
        }

    }
    //cart checkout New
    public function repurchase_submit_post(){
        $user_id = $this->rest->user_id;
        $user_name = $this->validation_model->IdToUserName($user_id);
        $repurchase_post = $this->validation_model->stripTagsPostArray($this->post());
        $this->form_validation->set_data($repurchase_post);
        $cart_products = $this->cart->contents();
        $purchase['total_amount'] = $this->cart->total();
        $payment_gateway_array = $this->register_model->getPaymentGatewayStatus("repurchase");
        $is_pin_ok = false;
        $is_ewallet_ok = false;
        $is_pwallet_ok = false;
        $is_paypal_ok = false;
        $is_authorize_ok = false;
        $is_blocktrail_ok = false;
        $is_blockchain_ok = false;
        $is_bitgo_ok = false;
        $is_payeer_ok = false;
        $is_sofort_ok = false;
        $is_squareup_ok = false;
        $is_bank_transfer_ok = false;

        $purchase['user_id'] = $user_id;
        $purchase['order_address_id'] = $this->validation_model->getUserPurchaseDefaultAddressId($user_id);
        if (!$purchase['order_address_id']) {
            $this->set_error_response(422,1023);
        }
        //check the cart 
        if (!empty($cart_products)) {
            if(!isset($repurchase_post['payment_method'])){
                $this->set_error_response(422,1049);
            }
            //purchase wallet
            if ($repurchase_post['payment_method'] == "purchase_wallet") {
                if($this->MODULE_STATUS['purchase_wallet']==='no'){
                    $this->set_error_response(422,1036);       
                }
                $payment_type = 'purchase wallet';
                $used_amount = $purchase['total_amount'];
                $pwallet_user = $repurchase_post['user_name'];
                $pwallet_trans_password = $repurchase_post['password'];
                if ($pwallet_user != "") {
                    if ($pwallet_user != $user_name) {
                        $this->set_error_response(422,1039);
                    }
                }else{
                    $this->set_error_response(422,1039);
                }
                $pwallet_user_id = $user_id;
                if ($pwallet_trans_password != "") {
                    $trans_pass_available = $this->register_model->checkEwalletPassword($pwallet_user_id, $pwallet_trans_password);
                    if ($trans_pass_available == 'yes') {
                        $pwallet_balance_amount = $this->validation_model->getPurchaseWalletAmount($pwallet_user_id);
                        if ($pwallet_balance_amount >= $used_amount) {
                            $is_pwallet_ok = true;
                        } else {
                            $this->set_error_response(422,1025);
                        }
                    } else {
                        $this->set_error_response(422,1039);
                    }
                }else{
                    $this->set_error_response(422,1039);
                }
            }elseif ($repurchase_post['payment_method'] == "free_purchase") {
                if ($payment_gateway_array['freejoin_status']== 'no') {
                    $this->set_error_response(422,1049);
                }
                $payment_type = 'free_purchase';
                $is_free_join_ok = true;
            }elseif ($repurchase_post['payment_method'] == "epin") {
                if ($payment_gateway_array['epin_status']== 'no') {
                    $this->set_error_response(422,1049);
                }
                $payment_type = 'epin';
                $pin_details = $repurchase_post['epin'];
                $i = 1;
                $pin_data = [];
                $pin_count = count($repurchase_post['epin']);
                foreach ($pin_details as $v) {
                    $pin_data[$i]['pin'] = $v;
                    $pin_data[$i]['pin_amount'] = 0;
                    $i++;
                }
                $pin_array = $this->repurchase_model->validateAllEpins($pin_data, $purchase['total_amount'], $user_id);
                $is_pin_ok = !(in_array('nopin', array_column($pin_array, 'pin')));
                if (!$is_pin_ok) {
                    $this->set_error_response(422,1016);
                }
                $is_pin_duplicate = (count(array_column($pin_array, 'pin')) != count(array_unique(array_column($pin_array, 'pin'))));
                if ($is_pin_duplicate) {
                    $this->set_error_response(422,1055);
                }
                if(!$pin_array['valid']){
                    $this->set_error_response(422,1025);
                }
            }elseif ($repurchase_post['payment_method'] == "ewallet") {
                if ($payment_gateway_array['ewallet_status']== 'no') {
                    $this->set_error_response(422,1049);
                }
                $payment_type = 'ewallet';
                $used_amount = $purchase['total_amount'];
                $ewallet_user = isset($repurchase_post['user_name'])?$repurchase_post['user_name']:'';
                $ewallet_trans_password = isset($repurchase_post['password'])?$repurchase_post['password']:'';
                if ($ewallet_user != "") {
                    if ($ewallet_user != $user_name) {
                        $this->set_error_response(422,1039);
                    }
                    if ($ewallet_trans_password != "") {
                        $trans_pass_available = $this->register_model->checkEwalletPassword($user_id, $ewallet_trans_password);
                        if ($trans_pass_available == 'yes') {   
                            $ewallet_balance_amount = $this->register_model->getBalanceAmount($user_id);
                            if ($ewallet_balance_amount >= $used_amount) {
                                $is_ewallet_ok = true;
                            } else {
                                $this->set_error_response(422,1014);
                            }
                        } else {
                            $this->set_error_response(422,1015);
                        }
                    } else {
                        $this->set_error_response(422,1015);
                    }
                }else{
                    $this->set_error_response(422,1039);
                }
            }elseif ($repurchase_post['payment_method'] == "banktransfer") {
                if ($payment_gateway_array['banktransfer_status']== 'no') {
                    $this->set_error_response(422,1049);
                }
                $bank_transfer_status = $this->configuration_model->getPaymentStatus('Bank Transfer');
                if ($bank_transfer_status == "no") {
                    $this->set_error_response(422,1036);
                }
                $payment_type = 'bank_transfer';
                $is_bank_transfer_ok = true;
            }
            else {
                $this->set_error_response(422,1036);
            }
            $purchase['payment_type'] = $payment_type;
            $pending_status = false;

            if($payment_type == 'bank_transfer') {
                $purchase['status'] = 'pending';
            } else {
                $purchase['status'] = 'confirmed';
            }
            if ($is_pin_ok) {
                $this->repurchase_model->begin();
                $purchase['by_using'] = 'pin';

                $pin_array['user_id'] = $purchase['user_id'];
                $res = $this->register_model->UpdateUsedEpin($pin_array, $pin_count, 'repurchase');
                if ($res) {
                    $this->register_model->insertUsedPin($pin_array, $pin_count, false, 'repurchase');
                    $payment_status = true;
                }
            } elseif ($is_ewallet_ok) {
                $this->repurchase_model->begin();
                $purchase['by_using'] = 'ewallet';
                $user_id = $purchase['user_id'];
                $used_user_id = $this->validation_model->userNameToID($ewallet_user);
                $transaction_id = $this->repurchase_model->getUniqueTransactionId();
                $res1 = $this->register_model->insertUsedEwallet($used_user_id, $user_id, $used_amount, $transaction_id, false, "repurchase");
                if ($res1) {
                    $res2 = $this->register_model->deductFromBalanceAmount($used_user_id, $used_amount);
                    if ($res2) {
                        $payment_status = true;
                    }
                }
            } elseif ($is_pwallet_ok) {
                $this->repurchase_model->begin();
                $purchase['by_using'] = 'purchase wallet';
                $user_id = $purchase['user_id'];
                $used_user_id = $this->validation_model->userNameToID($pwallet_user);
                $transaction_id = $this->repurchase_model->getUniqueTransactionId();
                $res1 = $this->validation_model->insertPurchasewalletHistory($used_user_id, $user_id, $used_amount, $used_amount, "repurchase", 'debit', 0, $transaction_id);
                if ($res1) {
                    $res2 = $this->ewallet_model->deductFromPurchaseWallet($used_user_id, $used_amount);
                    if ($res2) {
                        $payment_status = true;
                    }
                }
            } elseif ($is_paypal_ok) {
                $purchase['by_using'] = 'paypal';
                $this->session->set_userdata('inf_repurchase', $purchase);
                $msg = "";
                $this->payNow($cart_products, $purchase);
            } elseif ($is_authorize_ok) {
                $purchase['by_using'] = 'Authorize.Net';
                $this->session->set_userdata('inf_repurchase', $purchase);
                $msg = "";
                $this->redirect($msg, "repurchase/authorizeRepurchase", false);
            } elseif ($is_blocktrail_ok) {
                $purchase['by_using'] = 'Blocktrail';
                $this->session->set_userdata('inf_repurchase', $purchase);
                $msg = "";
                $this->redirect($msg, "repurchase/blocktrailRepurchase", false);
            } elseif ($is_blockchain_ok) {
                $purchase['by_using'] = 'Blockchain';
                $this->session->set_userdata('inf_repurchase', $purchase);
                $msg = "";
                $this->redirect($msg, "repurchase/blockchainRepurchase", false);
            } elseif ($is_bitgo_ok) {
                $purchase['by_using'] = 'Bitgo';
                $this->session->set_userdata('inf_repurchase', $purchase);
                $this->session->set_userdata('is_new', 'yes');
                $msg = "";
                $this->redirect($msg, "repurchase/bitgoRepurchase", false);
            } elseif ($is_sofort_ok) {
                $purchase['by_using'] = 'sofort';
                $this->session->set_userdata('inf_repurchase', $purchase);
                $msg = "";
                $this->redirect($msg, "repurchase/sofort_repurchase", false);
            } elseif ($is_payeer_ok) {
                $purchase['by_using'] = 'payeer';
                $this->session->set_userdata('inf_repurchase', $purchase);
                $msg = "";
                $this->redirect($msg, "repurchase/payeer_repurchase", false);
            } elseif ($is_squareup_ok) {
                $purchase['by_using'] = 'squareup';
                $this->session->set_userdata('inf_repurchase', $purchase);
                $msg = "";
                $this->redirect($msg, "repurchase/squareupRepurchase", false);
            } elseif ($is_bank_transfer_ok) {
                $purchase['by_using'] = 'bank_transfer';
                $this->repurchase_model->begin();
                $payment_status = true;
                $pending_status = true;
            } else {
                $purchase['by_using'] = 'free join';
                $this->repurchase_model->begin();
                $payment_status = true;
            }

            if ($payment_status) {
                $insert_into_sales = $this->insertIntoRepuchase($cart_products, $purchase);
                 if(!$pending_status) {
                    $this->repurchase_model->updateUserPv($cart_products, $purchase, $this->MODULE_STATUS);
                }
                if (!empty($insert_into_sales)) {
                    $module_status = $this->MODULE_STATUS;
                    $rank_status = $module_status['rank_status'];
                    if ($rank_status == "yes" && !$pending_status) {
                        $this->load->model('rank_model');
                        $this->rank_model->updateUplineRank($purchase['user_id']);
                    }
                    $this->repurchase_model->commit();
                    $this->cart->destroy();
                    $this->session->unset_userdata('inf_repurchase');
                    $this->session->unset_userdata('repurchase_post_array');
                    $this->session->unset_userdata('inf_repurchase_post_array');
                    $enc_order_id = $this->validation_model->encrypt($insert_into_sales['order_id']);
                    $res_data = [
                        'invoice_no' => $insert_into_sales['invoice_no'],
                        'enc_order_id' => $enc_order_id
                    ];
                    if ($pending_status) {
                        $res_data['pending'] = true;
                    } else{
                        $res_data['pending'] = false;
                    }
                    $this->set_success_response(200,$res_data);
                }else{
                    $this->repurchase_model->rollback();
                    $this->set_error_response(422,1054);
                }
            }else{
                $this->repurchase_model->rollback();
                $this->set_error_response(422,1054);
            }
        }else{
            $this->set_error_response(422,1026);
        }
    }
    //get the invoice
    public function purchase_invoice_get(){
        if($this->MODULE_STATUS['repurchase_status'] != 'yes' || $this->MODULE_STATUS['product_status'] != 'yes') {
            $this->set_error_response(422,1057);
        }
        $user_id = $this->rest->user_id;
        $enc_invoice_order_id = $this->get('id')?:'';
        $invoice_details = array();
        $site_info = $this->validation_model->getSiteInformation();
        $site_info['login_logo']  = SITE_URL.'/uploads/images/logos/logo_login.png';
        $invoice_order_id = $this->validation_model->decrypt($enc_invoice_order_id);
        if (!$invoice_order_id) {
            $this->set_error_response(422,1051);
        } else {
            $data = $this->repurchase_model->getRpurchaseInvoiceDetails($invoice_order_id);
            if($data['invoice_no']){
                $data['companyInfo'] = $site_info;
                $this->set_success_response(200,$data);
            }else{
                $this->set_error_response(422,1051);
            }
        }
    }


    //purchase report 
    public function purchaseReport_get(){
        if($this->MODULE_STATUS['repurchase_status'] != 'yes' || $this->MODULE_STATUS['product_status'] != 'yes') {
            $this->set_error_response(422,1057);
        }
        $user_id  = $this->rest->user_id;
        $columns = [
            0 => 'invoice_no',
            1 => 'total_amount',
            2 => 'payment_method',
            3 => 'order_date',
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
        $from_date = $this->get('start_date');
        $to_date   = $this->get('end_date');
        $count = $this->report_model->getRepurchaseDetailsCount($from_date, $to_date, $user_id);
        $purcahse_details = $this->report_model->getRepurchaseDetailsNew($from_date, $to_date, $user_id,$filter);
        $details = [];
        $total_amount = 0;
        foreach($purcahse_details as $key => $item) {
            $total_amount += $item->total_amount;
            $details[] = [
                "invoice_no"     => $item->invoice_no,
                'amount_withCurrency'   => format_currency($item->total_amount),
                'amount'       => $item->total_amount,
                'payment_method' => lang($item->payment_method),
                'purchase_date'  => date("F j, Y, g:i a",strtotime($item->order_date)),
            ];
        }
        $data = [
            'total_row' =>intval($count),
            'data'  =>$details
        ];
        $this->set_success_response(200,$data);
    }
}
