<?php

require_once 'Inf_Controller.php';

class Upgrade extends Inf_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model(['payment_model', 'upgrade_model', 'home_model', 'validation_model']);
        $this->LOG_USER_ID = $this->rest->user_id;
        $this->LOG_USER_TYPE = 'user';
    }

    function check_product_exists($product_id) {
        if(!$this->product_model->isActiveProduct($product_id, 'registration')) {
            $this->form_validation->set_message('check_product_exists', 'invalid product');
            return FALSE;
        }
        return TRUE;
    }

    function check_payment_exists($payment_type) {
        switch($payment_type) {
            case "freejoin":
                $payment_method = "Free Joining";
            break;
            case "banktransfer":
                $payment_method = "Bank transfer";
            break;
            case "ewallet":
                $payment_method = "E-wallet";
            break;
            case "epin":
                $payment_method = "E-pin";
            break;
            default:
                $payment_method = "";
            break;
        }
        if($this->payment_model->getGatewayStatus($payment_method, 'upgradation') != "yes") {
            $this->form_validation->set_message('check_payment_exists', 'invalid payment');
            return FALSE;
        }
        return TRUE;
    }

    function check_package_upgradable($product_id) {
    	$package_id = $this->product_model->getProductPackageId($product_id, $this->MODULE_STATUS, 'registration');
    	$current_package_id = $this->validation_model->getProductId($this->LOG_USER_ID);
    	if($this->upgrade_model->isUpgradablePackage($current_package_id, $package_id)) {
    		return true;
    	}
    	$this->form_validation->set_message('check_package_upgradable', 'invalid product');
        return FALSE;
    }


    public function upgrade_post() {
        $this->form_validation->set_data($this->post());
        $this->form_validation->set_rules('product_id', 'product_id', 'required|callback_check_product_exists|callback_check_package_upgradable');
        $this->form_validation->set_rules('payment_method', 'payment_method', 'required|callback_check_payment_exists');
        if(!$this->form_validation->run()) {
            $this->set_error_response(422, 1004);
        }
        
        $package_id = $this->product_model->getProductPackageId($this->post('product_id'), $this->MODULE_STATUS, 'registration');
        $current_package_id = $this->validation_model->getProductId($this->LOG_USER_ID);
        $current_package_amount = $this->product_model->getCartProduct($current_package_id);
        $package_amount = $this->product_model->getCartProduct($package_id);
        $payment_amount = $package_amount - $current_package_amount;

        $this->inf_model->begin();
        $payment_res = false;
        switch($this->post('payment_method')) {
            case "freejoin":
                $payment_type = 'free_upgrade';
             	$payment_res = true;
                $payment_status = "confirmed"; 
            break;

            case "banktransfer":

            	$user_name = $this->validation_model->IdToUserName($this->LOG_USER_ID);
                $payment_receipt = $this->payment_model->getReceipt($user_name, 'package_upgrade');
                $payment_type = 'bank_transfer';
                $purchase['by_using'] = 'bank_transfer';

                $pending_array =  serialize([
                    'user_id' => $this->LOG_USER_ID,
                    'current_package_id' => $current_package_id,
                    'product_id' => $this->post('product_id'),
                    'package_id' => $package_id,
                    'payment_amount' => $payment_amount,
                    'payment_type' => $payment_type,
                    'done_by' => $this->LOG_USER_ID,
                    'module_status' => $this->MODULE_STATUS,
                    'receipt' => $payment_receipt,
                    'post_data'=> $this->post(),
                ]);

                $payment_res = true;
                $payment_status = "pending"; 
            break;

            case "ewallet":
                $payment_type = 'ewallet';
                $ewallet_user = $this->post('user_name_ewallet');
                $ewallet_trans_password = $this->post('tran_pass_ewallet');
                $product_id = $this->post('product_id');
                $this->load->service('Ewallet_payment_service');
                $validated = $this->ewallet_payment_service->validate_payment($ewallet_user, $ewallet_trans_password, $product_id, 'package_upgrade');
                if($validated['status']) {
                    $purchase['by_using'] = 'ewallet';
                    $payment_res = $this->ewallet_payment_service->run_payment($ewallet_user, $ewallet_trans_password, $product_id, 'package_upgrade', 'upgrade');
                    $payment_status = "confirmed";
                } else {
                    $this->set_error_response(422,$validated['code']);
                }
            break;

            case "epin":
                $payment_type = 'epin';
                $pin_count = $this->post('pin_array');
                $pin_details = [];
                for ($i = 1; $i <= $pin_count; $i++) {
                    if ($this->post("epin$i")) {
                        $pin_number = $this->post("epin$i");
                        $pin_details[$i]['pin'] = $pin_number;
                        $pin_details[$i]['i'] = $i;
                    }
                }
                $pin_array = $this->payment_model->checkAllEpins($pin_details, $this->post('product_id'), $this->MODULE_STATUS['product_status'], $this->LOG_USER_ID, 'package_upgrade', true);
                $is_pin_ok = $pin_array["is_pin_ok"];

                if(!$is_pin_ok) {
                    $this->set_error_response(422, 1049);
                }
                $this->payment_model->begin();
                $purchase['by_using'] = 'pin';
                $res = $this->payment_model->UpdateUsedUserEpin($pin_array, $pin_count);
                if ($res) {
                    $pin_array['user_id'] = $this->LOG_USER_ID;
                    $payment_res = $this->payment_model->insertUsedPin($pin_array, $pin_count, false, 'package_upgrade');
                    $payment_status = 'confirmed';
                    $this->payment_model->commit();
                }
            break;
            case "paypal": 
                $payment_gateway_array = $this->register_model->getPaymentGatewayStatus("upgradation");
                if($payment_gateway_array['paypal_status'] == 'no'){
                    $this->set_error_response(401, 1009);
                }
                $payment_details = array(
                    'payment_method' => 'paypal',
                    'token_id' => $this->post('paypal_token'),
                    'currency' => $this->post('currency'),
                    'amount' => $payment_amount,
                    'acceptance' => '',
                    'payer_id' => $this->post('PayerID'),
                    'user_id' => $this->LOG_USER_ID,
                    'status' => '',
                    'card_number' => '',
                    'ED' => '',
                    'card_holder_name' => '',
                    'submit_date' => date("Y-m-d H:i:s"),
                    'pay_id' => '',
                    'error_status' => '',
                    'brand' => ''
                );
                $this->register_model->insertintoPaymentDetails($payment_details);
                $payment_type = "paypal";
                $payment_res = true;
                $is_paypal_ok = true;
            break;
            default:
                $this->set_error_response(422, 1049);
            break;
        }
        $user_id = $this->LOG_USER_ID;
        $product_id = $this->post('product_id');
        $module_status = $this->MODULE_STATUS;
        $upgrade_res = false;
        if ($payment_res == true && $payment_status == "confirmed") {
            $upgrade_res = $this->upgrade_model->upgradeMembershipPackage($user_id, $current_package_id, $product_id, $package_id, $payment_amount, $payment_type, $this->LOG_USER_ID, $module_status, $payment_status);
            $package_name = $this->home_model->getPackageNameFromPackageId($package_id, $module_status);
            $data = serialize($this->post());
            $login_id = $this->LOG_USER_ID;
            
            $user_name = $this->validation_model->getUserName($login_id);
            $this->validation_model->insertUserActivity($login_id, $user_name . '`s package upgraded to ' . $package_name . ' through ' . lang($payment_type), $login_id, $data);
        } else if($payment_res == true && $payment_status == "pending") {
            $login_id = $this->LOG_USER_ID;
            $package_name = $this->home_model->getPackageNameFromPackageId($package_id, $module_status);
            $data = serialize($this->post());
            $upgrade_res = $this->upgrade_model->upgradeMembershipPackagePending($user_id, $current_package_id, $product_id, $package_id, $payment_amount, $payment_type, $this->LOG_USER_ID, $module_status, $payment_status, $this->session->userdata('inf_payment_receipt'));
             $user_name = $this->validation_model->getUserName($user_id);
                $this->validation_model->insertUserActivity($login_id, $user_name . '`requested a package upgraded. ' . $package_name . ' through ' . lang($payment_type), $user_id, $data);
        }
        if ($upgrade_res == true && $payment_type != "bank_transfer") {
            $this->inf_model->commit();
            $this->set_success_response(200, ['message' => 'package_upgrade_success']);
        } else if($upgrade_res == true && $payment_type == "bank_transfer") {
            $this->inf_model->commit();
            $this->set_success_response(200, ['message' => 'admin_approval_required']);
        } else {
            $this->inf_model->rollback();
            $this->set_error_response(422, 1030);
        }
    }
}

        
