<?php

require_once 'Inf_Controller.php';

class Payment extends Inf_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('validation_model');
        $this->load->model('password_model');
        $this->load->model('Api_model');
        $this->load->model('profile_model');
        $this->load->model('configuration_model');
        $this->load->model('captcha_model');
        $this->load->model('payment_model');
        $this->load->model('register_model');
        $this->load->model('repurchase_model');
        $this->load->library('internal_cart', ['user_id' => $this->rest->user_id], 'cart');
        $this->LOG_USER_ID = $this->rest->user_id;
        $this->LOG_USER_TYPE = 'user'; 
    }


    public function payment_methods_get() {
        $type = $this->get('type') ?: 'registration';
        $payment_gateway_array = $this->payment_model->getPaymentGatewayStatus($type);
        $PaymentMethods = [];
        foreach ($payment_gateway_array as $key => $value) {
            if ($value=='yes') {
                $icon='';
                switch ($key) {
                    case 'paypal_status':
                        $icon="fa fa-paypal";
                        break;
                    case 'authorize_status':
                        $icon="fa fa-lock";
                        break;
                    case 'bitcoin_status':
                        $icon="fa fa-btc";
                        break;
                    case 'blockchain_status':
                        $icon="fa fa-asterisk";
                        break;
                    case 'bitgo_status':
                        $icon="fa fa-btc";
                        break;
                    case 'payeer_status':
                        $icon="fa fa-product-hunt";
                        break;
                    case 'sofort_status':
                        $icon="fa fa-euro";
                        break;
                    case 'squareup_status':
                        $icon="fa fa-square";
                        break;
                    case 'epin_status':
                        $icon="fa fa-window-restore";
                        break;
                    case 'ewallet_status':
                        $icon="fa fa-archive";
                        break;
                    case 'banktransfer_status':
                        $icon="fa fa-bank";
                        break;
                    case 'freejoin_status':
                        $icon="fa fa-cog";
                        break;
                    default:
                        # code...
                        break;
                }
                $title = lang($key);
                $code = substr($key, 0, -7);
                if ($this->IS_MOBILE) {
                    $title = lang($code); 
                }
                if($this->IS_MOBILE && in_array($code, ['paypal', 'authorize', 'blockchain', 'bitgo', 'payeer', 'sofort', 'squareup'])) {
                    continue;
                }
                if($type === 'repurchase' &&$code=='freejoin'){
                    $code = 'free_purchase';
                }
                $PaymentMethods[]=[
                    'code' => $code,
                    'value' => true,
                    'title' => $title,
                    'icon'  => $this->IS_MOBILE ? str_replace("fa fa-", "", $icon) : $icon
                ];
            }
        }
        if($type == 'repurchase' && $this->MODULE_STATUS['purchase_wallet']=='yes'){
            $title = lang('purchase_wallet');
            if ($this->IS_MOBILE) {
                $title = lang('purchase_wallet'); 
            }
            $PaymentMethods[]=[
                'code'  => 'purchase_wallet',
                'value' => true,
                'title' => $title,
                'icon'  => $this->IS_MOBILE ?  'shopping-basket':'fa fa-shopping-basket' 
            ];
        }
        $this->set_success_response(200, $PaymentMethods);
    }

    function index_get()
    {
        $user_id = $this->rest->user_id;
        $data = $this->Api_model->getProfileData($user_id);
        if ($data) {
            $this->set_success_response(200, $data);
        } else {
            $this->set_error_response(401, 1003);
        }
    }

    public function subscription_details_get() {
        $data = $this->profile_model->getScriptionDetails($this->rest->user_id);
        if(empty($data)) {
            $this->set_error_response(401, 1003);
        }
        $this->set_success_response(200, [
            'id' => $data['product_id'],
            'validity' => $data['product_validity'],
            'price' => $data['product_value']
        ]);
    }


    //upload payment reciept
    function upload_payment_reciept_post(){
        $this->load->library('upload');
        if (!isset($_FILES['file'])) {
            $this->set_error_response(422,1032);
        }
        $user_name = $this->post('user_name', true) ? $this->post('user_name', true) : $this->validation_model->IdToUserName($this->LOG_USER_ID);
        if(!$user_name || $this->validation_model->isUsernameExists($this->post('user_name'))) {
                $this->set_error_response(422,1043);
        }

        $user_id = $this->validation_model->userNameToID($user_name);
        if (!empty($_FILES['file']) && isset($_FILES['file'])) {
            $upload_config = $this->validation_model->getUploadConfig();
            $upload_count = $this->validation_model->getUploadCount($user_id);
            if ($upload_count >= $upload_config) {
                $this->set_error_response(422,1038);
            }
        }
        if ($_FILES['file']['error'] != 4) {
            $random_number = floor(2048 * rand(1000, 9999));
            $config['file_name'] = "bank_" . $random_number;
            $config['upload_path'] = IMG_DIR . 'reciepts';
            $config['allowed_types'] = 'jpg|png|jpeg';
            $config['max_size'] = '2048';
            $config['max_width'] = '3000';
            $config['max_height'] = '3000';
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('file')) {
                $msg = $this->upload->display_errors();
                $msg = strip_tags($msg);
                $response['error'] = true;
                $response['message'] = $msg;
                $this->set_error_response(422,1024);
            } else {
                $result = '';
                $data = array('upload_data' => $this->upload->data());
                $doc_file_name = $data['upload_data']['file_name'];
                $result = $this->payment_model->storeBTReceipt($user_name, $doc_file_name, $this->post('type'));
                $this->validation_model->updateUploadCount($user_id);
                if ($result) {
                    $data_array['file_name'] = $doc_file_name;
                    $data = serialize($data_array);

                    // Employee Activity History
                    if ($this->LOG_USER_TYPE == 'employee') {
                        $this->validation_model->insertEmployeeActivity($user_id, $user_id, 'upload_material', 'Payment Receipt Uploaded');
                    }
                    $msg = lang('payment_receipt_ploaded_successfully');
                    $response['success'] = true;
                    $response['message'] = $msg;
                    $response['file_name'] = $doc_file_name;
                    $this->set_success_response(200,$response);
                } else {
                    $this->set_error_response(422,1024);
                }
            }
        }else{
            $this->set_error_response(422,1024);
        }

    }

    /**
     * [check_ewallet_balance_post description]
     * @return [type] [description]
     */
    function check_ewallet_balance_post(){
        $ewallet_user = $this->post('user_name', true);
        $ewallet_pass = $this->post('ewallet', true);
        $product_id = $this->post('product_id', true);
        $type = $this->post('payment_type', true);

        $user_name = $this->validation_model->IdToUserName($this->LOG_USER_ID);
        
        //logged type is user
        if ($this->LOG_USER_TYPE == 'user') {
            if ($ewallet_user != $user_name) {
                $this->set_error_response(422,1039);
            }
        }

        if(!$type) {
            $this->set_error_response(422,1049);
        }

        $user_id = $this->validation_model->userNameToID($ewallet_user);
        if ($user_id) {
            if ($this->payment_model->checkEwalletPassword($user_id, $ewallet_pass)) {
                $user_bal_amount = $this->payment_model->getBalanceAmount($user_id);
                if ($user_bal_amount > 0) {
                    $total_amount = $this->getTotalPaymentAmount($type, $product_id);
                    if ($user_bal_amount >= $total_amount) {
                        $this->set_success_response(200);
                    }else{
                        $this->set_error_response(422,1014);
                    }
                }else{
                    $this->set_error_response(422,1014);
                }
            } else {
                $this->set_error_response(422,1039);
            }
        } else {
            $this->set_error_response(422,1039);
        }
    }

    function check_epin_validity_post() {
        $user_id = $this->rest->user_id;
        $product_id = $this->post('product_id');
        $pin_details = $this->post('pin_array');
        $product_status = $this->MODULE_STATUS["product_status"];
        $sponsor_id = $this->LOG_USER_ID;
        $payment_type = $this->post('payment_type');
        $user_name = $this->validation_model->getUserName($sponsor_id);
        $flag = false;
        if ($user_name != '') {
            if ($this->payment_model->isUserAvailable($user_name)) {
                $flag = true;
            }
        }
        if ($flag) {
            if($payment_type == 'repurchase'){
                $purchase_amount = $this->cart->total();
                $pin_array = $this->repurchase_model->validateAllEpins($pin_details, $purchase_amount, $user_id);
                unset($pin_array['valid']);
            }else{
                $pin_array = $this->payment_model->checkAllEpins($pin_details, $product_id, $product_status, $sponsor_id, $payment_type);
            }
            if ($this->IS_MOBILE) {
                $total_amount = 0;
                foreach ($pin_array as $value) {
                    $total_amount += $value['epin_used_amount'];
                }
                $pin_array[count($pin_array)-1]['total_amount'] = $total_amount;
            }
            $this->set_success_response(200,$pin_array);
        }else{
            $this->set_error_response(422);
        }
    }

    private function getTotalPaymentAmount($type, $product_id='') {
        $total_amount = 0;
        switch($type) {
            case "registration":
                $reg_amount = $this->register_model->getRegisterAmount();
                $product_amount = 0;
                if ($this->MODULE_STATUS['product_status'] == "yes") {
                    if(!$product_id) {
                        $this->set_error_response(422,1012);
                    }
                    $product_details = $this->register_model->getProduct($product_id);
                    $product_amount = $product_details["product_value"];
                }
                $total_amount = $reg_amount + $product_amount;
            break;
            
            case "subscription_renewal":
                $product_details = $this->payment_model->getProduct($product_id);
                $total_amount = $product_details["product_value"] ?: 0;
            break;
            case 'repurchase':
                $total_amount = $this->cart->total();
                break;
            case "package_upgrade":
                $package_id = $this->product_model->getProductPackageId($this->post('product_id'), $this->MODULE_STATUS, 'registration');
                $current_package_id = $this->validation_model->getProductId($this->LOG_USER_ID);
                $current_package_amount = $this->product_model->getCartProduct($current_package_id);
                $package_amount = $this->product_model->getCartProduct($package_id);
                $total_amount = $package_amount - $current_package_amount;
            break;

            default: 
                $this->set_error_response(422,1049);
            break;
        }
        return $total_amount;
    }
    //check the purchase wallet availability
    public function check_purchase_wallet_balance_post(){
        $ewallet_user = $this->post('user_name');
        $ewallet_pass = $this->post('ewallet');
        $total_amount = $this->post('repruchase_amount');
        $user_name = $this->validation_model->IdToUserName($this->LOG_USER_ID);
        if ($ewallet_user != $user_name) {
            $this->set_error_response(422,1039);
        }
        $user_password = $this->register_model->checkEwalletPassword($this->LOG_USER_ID, $ewallet_pass);
        if ($user_password == 'yes') {
            $user_bal_amount = $this->validation_model->getPurchaseWalletAmount($this->LOG_USER_ID);
            if ($user_bal_amount > 0) {
                if ($user_bal_amount >= $total_amount) {
                    $this->set_success_response(204);
                }else{
                    $this->set_error_response(422,1025);
                }
            }else{
                $this->set_error_response(422,1025);
            }
        } else {
            $this->set_error_response(422,1039);
        }
    }

}

        
