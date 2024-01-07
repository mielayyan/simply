<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthorizeNetPayment
 *
 * @author ioss
 */
class authorizeNetPayment_model extends inf_model  {
    public function __construct() {
        parent::__construct();
        $this->load->model('configuration_model');
    }
    public function authorizePay($api_login_id, $transaction_key, $amount, $fp_sequence, $fp_timestamp) {
        require_once 'anet_php_sdk/AuthorizeNet.php';
        $fingerprint = AuthorizeNetSIM_Form::getFingerprint($api_login_id, $transaction_key, $amount, $fp_sequence, $fp_timestamp);
        return $fingerprint;
    }
    public function insertAuthorizeNetPayment($response, $user_id = '0') {

        $date = date('Y-m-d H:i:s');

        if (!empty($user_id)) {
            $this->db->set('user_id', $user_id);
        }
        $this->db->set('first_name', $response['x_first_name']);
        $this->db->set('last_name', $response['x_last_name']);
        $this->db->set('company', $response['x_company']);
        $this->db->set('address', $response['x_address']);
        $this->db->set('city', $response['x_city']);
        $this->db->set('state', $response['x_state']);
        $this->db->set('zip', $response['x_zip']);
        $this->db->set('country', $response['x_country']);
        $this->db->set('phone', $response['x_phone']);
        $this->db->set('fax', $response['x_fax']);
        $this->db->set('email', $response['x_email']);
        $this->db->set('date', $date);
        $this->db->set('invoice_num', $response['x_invoice_num']);
        $this->db->set('description', $response['x_description']);
        $this->db->set('cust_id', $response['x_cust_id']);
        $this->db->set('ship_to_first_name', $response['x_ship_to_first_name']);
        $this->db->set('ship_to_last_name', $response['x_ship_to_last_name']);
        $this->db->set('ship_to_company', $response['x_ship_to_company']);
        $this->db->set('ship_to_address', $response['x_ship_to_address']);
        $this->db->set('ship_to_city', $response['x_ship_to_city']);
        $this->db->set('ship_to_state', $response['x_ship_to_state']);
        $this->db->set('ship_to_zip', $response['x_ship_to_zip']);
        $this->db->set('ship_to_country', $response['x_ship_to_country']);
        $this->db->set('amount', $response['x_amount']);
        $this->db->set('tax', $response['x_tax']);
        $this->db->set('duty', $response['x_duty']);
        $this->db->set('freight', $response['x_freight']);
        $this->db->set('auth_code', $response['x_auth_code']);
        $this->db->set('trans_id', $response['x_trans_id']);
        $this->db->set('method', $response['x_method']);
        $this->db->set('card_type', $response['x_card_type']);
        $this->db->set('account_number', $response['x_account_number']);
        $res = $this->db->insert('authorize_payment_details');
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function updateAuthorizeNetPayment($insert_id, $user_id, $pending_status = FALSE) {
        $pending_id = 'NULL';
        if ($pending_status) {
            $pending_id = $user_id;
            $user_id = 'NULL';
        }
        $this->db->set('user_id', $user_id, FALSE);
        $this->db->set('pending_id', $pending_id, FALSE);
        $this->db->where('id', $insert_id);
        $result = $this->db->update('authorize_payment_details');
        return $result;
    }

    public function getAuthorizeDetails() {
        return $this->configuration_model->getAuthorizeConfigDetails();
    }
}
