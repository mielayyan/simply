<?php

require_once 'Inf_Controller.php';

class Signup extends Inf_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('register_model');
    }

    function index_post()
    {
        $post_array = $this->post();
        $post_array = $this->validation_model->stripTagsPostArray($post_array);
        $user_id = $this->rest->user_id;
        $username = $this->validation_model->IdToUserName($user_id);
        $user_type = $this->validation_model->getUserType($user_id);
        $module_status = $this->MODULE_STATUS;
        $product_status = $module_status['product_status'];

        $signup_settings = $this->configuration_model->getGeneralSignupConfig();
        if ($signup_settings['registration_allowed'] == 'no') {
            $this->set_error_response(401, 1006);
        }

        if ($this->validate_register_submit()) {
            if($post_array['placement_username'] != '') {
                $placement_name = $post_array['placement_username'];
            } else {
                $placement_name = $post_array['sponsor_username'];
            }
            if (strtolower($post_array['sponsor_username']) != strtolower($username)) {
                $this->set_error_response(401, 1007);
            }
            
            if ($module_status['mlm_plan'] == 'Binary') {
                $this->load->model('tree_model');
                $placement_id = $this->validation_model->userNameToID($placement_name);
                $binary_leg_allowed = $this->tree_model->getAllowedBinaryLeg($placement_id, $user_type, $user_id);
                if ($binary_leg_allowed != 'any' && $binary_leg_allowed != $post_array['position']) {
                    $this->set_error_response(401, 1008);
                }
            }

            $payment_status = false;
            $is_free_join_ok = false;
            $is_pin_ok = false;
            $is_ewallet_ok = false;

            $username_config = $this->configuration_model->getUsernameConfig();
            $reg_post_array = $this->validation_model->stripTagsPostArray($post_array);
            $regr = $reg_post_array;

            if ($module_status['mlm_plan'] == "Unilevel" || $module_status['mlm_plan'] == "Stair_Step") {
                $regr['placement_user_name'] = $placement_name;
            }
            $regr['reg_amount'] = $this->register_model->getRegisterAmount();

            $product_id = 0;
            $product_name = 'NA';
            $product_pv = '0';
            $product_amount = '0';
            $product_validity = "";

            if ($product_status == 'yes') {
                $product_id = $reg_post_array['product_id'];
                $this->load->model('product_model');
                $product_details = $this->product_model->getProductDetails($product_id, 'yes');
                $product_name = $product_details[0]['product_name'];
                $product_pv = $product_details[0]['pair_value'];
                $product_amount = $product_details[0]['product_value'];
                if ($module_status['subscription_status'] == "yes") {
                    $product_validity = $this->product_model->calculateProductValidity($product_details[0]['subscription_period']);
                }
            }

            $regr['product_status'] = $product_status;
            $regr['product_id'] = $product_id;
            $regr['product_name'] = $product_name;
            $regr['product_pv'] = $product_pv;
            $regr['product_amount'] = $product_amount;
            $regr['product_validity'] = $product_validity;
            $regr['total_amount'] = $regr['reg_amount'] + $regr['product_amount'];
            $regr['user_name_type'] = $username_config['type'];
            $regr['joining_date'] = date('Y-m-d H:i:s');
            $regr['reg_from_tree'] = 0;
            $regr['sponsor_id'] = $this->validation_model->userNameToID($regr['sponsor_username']);
            $regr['product_name'] = $this->register_model->getProductName($product_id);
            
            $regr['position'] = isset($reg_post_array['position']) ? $reg_post_array['position'] : '' ;
            $regr['user_name_entry'] = $reg_post_array['username'];
            $regr['pswd'] = $reg_post_array['password'];
            $regr['first_name'] = $reg_post_array['firstname'];
            $regr['last_name'] = isset($reg_post_array['lastname']) ? $reg_post_array['lastname'] : '' ;
            $regr['land_line'] = isset($reg_post_array['landline']) ? $reg_post_array['landline'] : '' ;
            $regr['city'] = isset($reg_post_array['city']) ? $reg_post_array['city'] : '' ;
            $regr['adress_line2'] = isset($reg_post_array['adress_line2']) ? $reg_post_array['adress_line2'] : '' ;
            $regr['adress_line1'] = isset($reg_post_array['adress_line1']) ? $reg_post_array['adress_line1'] : '' ;
            $regr['gender'] = isset($reg_post_array['gender']) ? $reg_post_array['gender'] : '' ;

            $payment_type = $reg_post_array['payment_method'];

            if ($payment_type == 'epin') {
                $pin_count = $reg_post_array['pin_count'];
                $pin_details = [];
                for ($i = 1; $i <= $pin_count; $i++) {
                    if ($reg_post_array["epin$i"]) {
                        $pin_number = $reg_post_array["epin$i"];
                        $pin_details[$i]['pin'] = $pin_number;
                    }
                }
                $pin_array = $this->register_model->checkAllEpins($pin_details, $product_id, $product_status, $regr['sponsor_id'], true);

                $is_pin_ok = $pin_array["is_pin_ok"];
                if (!$is_pin_ok) {
                    $this->set_error_response(401, 1016);
                }
            }  elseif ($payment_type == 'ewallet') {
                $used_amount = $regr['total_amount'];
                $ewallet_user = $username;
                $ewallet_trans_password = $reg_post_array['tran_pass_ewallet'];

                $user_available = $this->register_model->isUserAvailable($ewallet_user);
                if ($user_available) {
                    if ($ewallet_trans_password != "") {
                        $ewallet_user_id = $this->validation_model->userNameToID($ewallet_user);
                        $trans_pass_available = $this->register_model->checkEwalletPassword($ewallet_user_id, $ewallet_trans_password);
                        if ($trans_pass_available == 'yes') {
                            $ewallet_balance_amount = $this->register_model->getBalanceAmount($ewallet_user_id);
                            if ($ewallet_balance_amount >= $used_amount) {
                                $is_ewallet_ok = true;
                            } else {
                                $this->set_error_response(401, 1014);
                            }
                        } else {
                            $this->set_error_response(401, 1015);
                        }
                    } else {
                        $this->set_error_response(401, 1015);
                    }
                } else {
                    $this->set_error_response(401, 1011);
                }

            } else if($payment_type == 'free_join') {
                $is_free_join_ok = true;
            } else {
                $this->set_error_response(500);
            }

            $regr['payment_type'] = $payment_type;
            unset($regr['user_name']);

            $pending_signup_status = $this->configuration_model->getPendingSignupStatus($payment_type);

            if ($is_pin_ok) {
                $this->register_model->begin();
                $regr['by_using'] = 'pin';
                $res = $this->register_model->UpdateUsedUserEpin($pin_array, $pin_count);
                $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status);
                if ($status['status']) {
                    $pin_array['user_id'] = $status['id'];
                    $res = $this->register_model->UpdateUsedEpin($pin_array, $pin_count);
                    if ($res) {
                        $this->register_model->insertUsedPin($pin_array, $pin_count, $pending_signup_status);
                        $payment_status = true;
                    }
                }
            } elseif ($is_ewallet_ok) {
                $this->register_model->begin();
                $regr['by_using'] = 'ewallet';
                $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status);
                if ($status['status']) {
                    $user_id = $status['id'];
                    $used_user_id = $this->validation_model->userNameToID($ewallet_user);
                    $transaction_id = $this->ewallet_model->getUniqueTransactionId();
                    $res1 = $this->register_model->insertUsedEwallet($used_user_id, $user_id, $used_amount, $transaction_id, $pending_signup_status);
                    if ($res1) {
                        $res2 = $this->register_model->deductFromBalanceAmount($used_user_id, $used_amount);
                        if ($res2) {
                            $payment_status = true;
                        }
                    }
                }
            } else {
                $regr['by_using'] = 'free join';
                $this->register_model->begin();
                $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status);
                if ($status['status']) {
                    $payment_status = true;
                }
            }

            if ($payment_status) {
                $user_name = $status['user_name'];
                $user_id = $status['user_id'];

                if ($product_status == "yes") {
                    $this->register_model->insertIntoSalesOrder($user_id, $regr['product_id'], $payment_type, $pending_signup_status);
                }

                $this->register_model->commit();
                $this->validation_model->insertUserActivity($this->rest->user_id, 'New user registered', $user_id);
                $response = ['username' => $user_name];
                $this->set_success_response(201, $response);
            } else {
                $this->register_model->rollback();
                $this->set_error_response(500);
            }

        }
        else {
            $this->set_error_response(422, 1004);
        }
    }

    function validate_register_submit()
    {
        $product_status = $this->MODULE_STATUS['product_status'];
        $username_config = $this->configuration_model->getUsernameConfig();
        $user_name_type = $username_config['type'];

        $this->form_validation->set_rules('sponsor_username', lang('sponsor_user_name'), 'trim|required|callback_validate_username');
        $this->form_validation->set_rules('placement_username', lang('placement_username'), 'trim|required|callback_validate_username');

        if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
            $this->form_validation->set_rules('position', lang('position'), 'trim|required|in_list[L,R]', ['in_list' => '%s must be valid']);
        }
        if ($product_status == 'yes') {
            $this->form_validation->set_rules('product_id', lang('product'), 'trim|required|callback_valid_product[registration]');
        }
        $this->form_validation->set_rules('firstname', lang('first_name'), 'trim|required|min_length[3]|max_length[32]|callback__alpha_space');
        $this->form_validation->set_rules('lastname', lang('last_name'), 'trim|min_length[3]|max_length[32]|callback__alpha_space');
        $this->form_validation->set_rules('date_of_birth', lang('date_of_birth'), 'trim|required|callback_validate_age_year');
        $this->form_validation->set_rules('email', lang('email'), 'trim|required|valid_email');
        $this->form_validation->set_rules('mobile', lang('mobile_no'), 'trim|required|is_natural|min_length[5]|max_length[10]');
        if ($user_name_type == 'static') {
            $this->form_validation->set_rules('username', lang('user_name'), 'trim|required|alpha_numeric|min_length[6]|max_length[12]|callback_is_username_available');
        }
        $this->form_validation->set_rules('password', lang('password'), 'trim|required|min_length[6]|max_length[32]|callback__alpha_password|matches[password_confirmation]');
        $this->form_validation->set_rules('password_confirmation', lang('confirm_password'), 'trim|required|min_length[6]|max_length[32]|callback__alpha_password');
        $this->form_validation->set_rules('agree_terms', lang('terms_conditions'), 'trim|required|in_list[true]', ['in_list' => '%s must be valid']);
        $this->form_validation->set_rules('payment_method', lang('payment_method'), 'trim|required|in_list[epin,ewallet,free_join]', ['in_list' => '%s must be valid']);

        $this->form_validation->set_message('validate_username', lang('%s_is_not_available'));
        $this->form_validation->set_message('is_username_available', lang('the_username_is_not_available'));

        $validation_status = $this->form_validation->run();
        return $validation_status;
    }

    function validate_username($ref_user = '')
    {
        $flag = false;
        if ($this->register_model->isUserAvailable($ref_user)) {
            $flag = true;
        }
        return $flag;
    }

    public function is_username_available($user_name)
    {
        if (!$user_name) {
            return false;
        }
        $is_username_exists = $this->validation_model->isUsernameExists($user_name);
        if ($is_username_exists) {
            return false;
        } else {
            return true;
        }
    }

    function valid_product($product_id, $type)
    {
        $res = $this->product_model->isActiveProduct($product_id, $type);
        $this->form_validation->set_message('valid_product', "You must select a valid product");
        return ($res > 0);
    }

    function _alpha_password($str_in = '')
    {
        if (!preg_match("/^[0-9a-zA-Z\s\r\n@!#\$\^%&*()+=\-\[\]\\\';,\.\/\{\}\|\":<>\?\_\`\~]+$/i", $str_in)) {
            $this->form_validation->set_message('_alpha_password', lang('password_characters_allowed'));
            return false;
        } else {
            return true;
        }
    }

    public function _alpha_space($str = '')
    {
        if (!$str) {
            return true;
        }
        $res = (bool)preg_match('/^[A-Z ]*$/i', $str);
        if (!$res) {
            $this->form_validation->set_message('_alpha_space', lang('form_validation_alpha_space'));
        }
        return $res;
    }

    public function validate_age_year($dob)
    {
        if(!$this->validate_date($dob)) {
            $this->form_validation->set_message('validate_age_year', "You must enter a valid date");
            return false;
        }

        $age_limit = $this->configuration_model->getAgeLimitSetting();
        if ($age_limit == 0) {
            return true;
        }
        $year = date('Y', strtotime($dob));
        $current_year = date('Y');
        if (($current_year - $year) >= $age_limit) {
            return true;
        } else {
            $this->form_validation->set_message('validate_age_year', sprintf(lang('You_should_be_atleast_n_years_old'), $age_limit));
            return false;
        }
    }

    function validate_date($date) {
        if (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts)) {
            // check whether the date is valid or not
            if (checkdate($parts[2], $parts[3], $parts[1])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    function fee_get()
    {
        $this->set_success_response(200, ['fee'=>$this->validation_model->getConfig('reg_amount')]);
    }
}
