<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once 'Inf_Controller.php';

class Time extends Inf_Controller {

    function __construct() {
        parent::__construct();
    }

    function check_time_out() {
        $status = "";
        if ($this->session->userdata("inf_user_page_load_time")) {
            $current_time = time();
            $page_load_time = $this->session->userdata("inf_user_page_load_time");
            //inactiviy logout setting
            $time = $this->configuration_model->selectLogoutTime();

            if ($current_time - $page_load_time >= $time) { //time in seconds
                $status = "expired";
            }
            //
        } else {
            $status = "expired";
        }
        echo $status;
        exit();
    }

    function userRegister($user_name, $type = 3) {
        $string = "[sponsor_required] => yes
            [age_limit] => 18
            [mlm_plan] => Board
            [prim_mlm_plan] => Binary
            [path] => http://localhost/WC/mac/backoffice/
            [lang_id] => 1
            [path_temp] => http://localhost/WC/mac/backoffice/public_html/
            [path_root] => http://localhost/WC/mac/backoffice/
            [reg_from_tree] => 0
            [username_type] => static
            [pin_count] => 0
            [epin_count] => 0
            [ewallet_bal] => 0
            [ewallet_cheking_type] => register
            [registration_fee] => 50
            [product_amount] => 1200.00000000
            [total_reg_amount] => 50
            [total_reg_amount1] => 50
            [product_status] => yes
            [date_of_birth] => 1981-05-07
            [default_country] => 209
            [sponsor_user_name] => client1
            [sponsor_full_name] => client
            [placement_user_name] => client1
            [placement_full_name] => client 
            [position] => L
            [product_id] => 3
            [first_name] => clientr
            [last_name] => 
            [email] => asdfasdf@sdfasdf.asdf
            [mobile] => 123123123
            [user_name_entry] => asdfasf
            [pswd] => 123456
            [cpswd] => 123456
            [agree] => on
            [active_tab] => free_join_tab
            [free_join_status] => yes
            [submit] => Finish
            [reg_amount] => 50
            [product_name] => Chromium
            [product_pv] => 960
            [product_validity] => 2021-04-24 10:39:43
            [total_amount] => 1250
            [user_name_type] => static
            [joining_date] => 2020-10-10 10:39:43
            [sponsor_id] => 26232
            [placement_id] => 26232
            [payment_type] => free_join
            [by_using] => free join";
        $explode_array = explode('
            ', $string);

        $rger = [];
        foreach ($explode_array as $val) {
            $explod = explode('=>', $val);
            $explod[0] = ltrim($explod[0], '[');
            $explod[0] = trim($explod[0], '] ');
            $rger[$explod[0]] = !empty($explod[1]) ? trim($explod[1]) : '';
        }

        $sponsor_id = $this->validation_model->userNameToID('admin');

        for ($i = 1; $i <= 6; $i++) {
            $position = ($i % 2 == 0) ? 'R' : 'L';

            $rger['sponsor_user_name'] = $rger['placement_user_name'] = 'admin';
            $rger['sponsor_id'] = $sponsor_id;
            $rger['placement_id'] = $sponsor_id;
            $rger['position'] = $position;
            $rger['user_name_entry'] = $user_name . $i;

            $this->MODULE_STATUS = $module_status = $this->login_model->trackModule();
            $email_verification = $this->configuration_model->getEmailVerificationStatus();
            $status = $this->register_model->confirmRegister($rger, $module_status, '', $email_verification);
            echo $rger['user_name_entry'], '<br>';
        }
    }

}
