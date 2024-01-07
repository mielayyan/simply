<?php

define("IN_WALLET", true);
require "../vendor/autoload.php";
require_once 'Inf_Controller.php';
 require_once "../vendor/stripe/stripe-php/init.php";
// require_once "../vendor/stripe.php";


class Register extends Inf_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('configuration_model', '', true);
        $this->load->model('ewallet_model', '', true);
        $this->load->model('tree_model', '', true);
        $this->MLM_PLAN = $this->validation_model->getMLMPlan();
        $this->load->model('member_model', '', true);
    }

    function user_register($placement_id_encrypted = "", $position = "")
    {
        $signup_settings = $this->configuration_model->getGeneralSignupConfig();
        $get_leg_type = '';
        if ($signup_settings['registration_allowed'] == 'no' && $this->LOG_USER_TYPE != 'admin' && $this->LOG_USER_TYPE != 'employee') {
            $msg = lang('registration_not_allowed');
            $this->redirect($msg, 'home', false);
        }


        // Registration blocked for subscription expired user
        if ($this->LOG_USER_ID) {
            $subscription_status = $this->MODULE_STATUS['subscription_status'];
            if ($subscription_status == 'yes') {
                $subscription_config = $this->configuration_model->getSubscriptionConfig();
                $current_date = date('Y-m-d H:i:s');
                if (uri_string() == 'replica_register') {
                    $replica_session = $this->session->userdata('replica_user');
                    $user_package_validity = $this->validation_model->getUserProductValidity($replica_session['user_id']);
                    if ($user_package_validity < $current_date) {
                        $msg = lang('subscription_expired');
                        $this->redirect($msg, 'home/index', FALSE);
                    }
                } elseif ($subscription_status == 'yes' && $subscription_config['reg_status'] == 'yes' && $this->LOG_USER_TYPE != 'admin' && $this->LOG_USER_TYPE != 'employee') {

                    $user_package_validity = $this->validation_model->getUserProductValidity($this->LOG_USER_ID);
                    if ($user_package_validity < $current_date) {
                        $msg = lang('subscription_expired');
                        $this->redirect($msg, 'home/index', FALSE);
                    }
                }
            }
        }
        //end


        $sponsor_user_name = $this->LOG_USER_NAME;
        if (($this->session->has_userdata('admin_user_name')) && (DEMO_STATUS == "yes"))
            $sponsor_user_name = $this->session->userdata('admin_user_name');
        $user_id = $this->LOG_USER_ID;

        if ($this->LOG_USER_TYPE == 'employee') {
            $sponsor_user_name = $this->ADMIN_USER_NAME;
            $user_id = $this->ADMIN_USER_ID;
        }

        if (!empty($this->session->userdata('from_replica'))) {
            $this->session->unset_userdata('from_replica');
        }

        $replica = FALSE;
        if (uri_string() == 'replica_register') {
            $this->session->set_userdata('from_replica', 'yes');
            $replica_session = $this->session->userdata('replica_user');
            if ($replica_session) {
                $sponsor_user_name = $replica_session['user_name'];
                $user_id = $replica_session['user_id'];
                $replica = TRUE;
            }
            $replica_lang = $this->session->userdata('replica_language');
            if ($replica_lang) {
                $lang_name_in_english = $replica_lang['lang_name_in_english'];
                $this->lang->load($this->CURRENT_CTRL, $lang_name_in_english);
            }
            if ($this->session->userdata("inf_language")) {
                $this->lang->load('common', $this->session->userdata("inf_language")['lang_name_in_english']);
                $this->lang->load($this->CURRENT_CTRL, $this->session->userdata("inf_language")['lang_name_in_english']);
            }
        } else {
            // $this->session->unset_userdata("inf_language");
        }


        $reg_from_tree = 0;
        $placement_id = '';
        $placement_user_name = '';
        $placement_full_name = '';
        if ($placement_id_encrypted != '') {
            $reg_from_tree = 1;
            $placement_id = $this->validation_model->userNameToID($placement_id_encrypted);
            if (!$this->validation_model->idToUserName($placement_id)) {
                $this->redirect("Invalid Placement", "tree/genology_tree", false);
            } else {
                $placement_user_name = $this->validation_model->IdToUserName($placement_id);
                $placement_full_name = $this->validation_model->getFullName($placement_id);
                if (($this->MLM_PLAN == "Unilevel" || $this->MLM_PLAN == "Stair_Step") && $this->LOG_USER_ID == $this->ADMIN_USER_ID) {

                    $sponsor_user_name = $placement_user_name;
                    $user_id = $placement_id;
                }
            }
        } else {
            $placement_user_name = $this->validation_model->IdToUserName($user_id);
            $placement_full_name = $this->validation_model->getFullName($user_id);
        }

        $sponsor_full_name = $this->validation_model->getFullName($user_id);

        if ($this->MODULE_STATUS['opencart_status_demo'] == "yes" && $this->MODULE_STATUS['opencart_status'] == "yes") {

            $this->session->set_userdata("inf_placement_array", array("reg_from_tree" => $reg_from_tree, "sponsor_user_name" => $sponsor_user_name, "sponsor_full_name" => $sponsor_full_name, "placement_user_name" => $placement_user_name, "placement_full_name" => $placement_full_name, "position" => $position, "mlm_plan" => $this->MLM_PLAN));

            if (!empty($this->session->userdata('inf_reg_data'))) {
                $this->session->unset_userdata('inf_reg_data');
            }

            // setup inf_reg_data & db prefix in oc session start
            $oc_session_data = $this->get_store_session_data();
            $oc_session_data['inf_reg_data'] = [
                'sponsor_info' => [
                    'sponsor_user_name' => $sponsor_user_name,
                    'reg_from_tree' => ($reg_from_tree),
                    'placement_user_name' => $placement_user_name,
                    'placement_full_name' => $placement_full_name,
                    'position' => $position,
                ],
                'reg_mode' => 'unlogged'
            ];
            $table_prefix = str_replace("_", "", $this->table_prefix);
            if ($this->session->userdata('from_replica') == 'yes') {
                $oc_session_data['inf_reg_data']['reg_mode'] = 'replica';
                if (DEMO_STATUS == 'yes') {
                    $table_prefix = $this->session->userdata('replica_user')['table_prefix'];
                    $table_prefix = str_replace("_", "", $table_prefix);
                }
            } else {
                if ($this->LOG_USER_TYPE == "user") {
                    $oc_session_data['inf_reg_data']['reg_mode'] = 'user_backoffice';
                }
                if ($this->LOG_USER_TYPE == "admin" || $this->LOG_USER_TYPE == "employee") {
                    $oc_session_data['inf_reg_data']['reg_mode'] = 'admin_backoffice';
                }
            }
            $this->write_store_session_data($oc_session_data);
            // setup inf_reg_data & db prefix in oc session end
            
            $store_path = STORE_URL . "/index.php?route=register/mlm";
            if (DEMO_STATUS == "yes") {
                $store_path = STORE_URL . "/index.php?route=register/mlm&id=$table_prefix";
            }
            redirect($store_path);
        }

        $title = lang('new_user_signup');
        $this->set('title', $this->COMPANY_NAME . " | $title");

        $help_link = "register_downline";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('new_user_signup');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('new_user_signup');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $countries = $this->country_state_model->viewCountry();
        $states = '';
        $products = '';
        if ($this->MODULE_STATUS['product_status'] == "yes") {
            $products = $this->register_model->viewProducts();
        }

        if ($signup_settings['default_country']) {
            $countries = $this->country_state_model->viewCountry($signup_settings['default_country']);
            $states = $this->country_state_model->viewState($signup_settings['default_country']);
        }

        $reg_post_array = array();
        $reg_count = 0;
        $pin_count = 0;
        if ($this->session->userdata("inf_reg_post_array")) {
            $reg_post_array = $this->session->userdata("inf_reg_post_array");
            $reg_from_tree = $reg_post_array['reg_from_tree'];
            $pin_count = $reg_post_array['pin_count'];
            $sponsor_user_name = $reg_post_array['sponsor_user_name'];
            $placement_user_name = $reg_post_array['placement_user_name'];
            $placement_full_name = $reg_post_array['placement_full_name'];
            $reg_count = count($this->session->userdata("inf_reg_post_array"));
            $reg_post_array['country'] = $reg_post_array['country'] ?? 1;
            $reg_post_array['state'] = $reg_post_array['state'] ?? null;
            $countries = $this->country_state_model->viewCountry($reg_post_array['country']);
            $states = $this->country_state_model->viewState($reg_post_array['country'], $reg_post_array['state']);
            if ($this->MODULE_STATUS['product_status'] == "yes") {
                $products = $this->register_model->viewProducts($reg_post_array['product_id']);
            }
            $this->session->unset_userdata("inf_reg_post_array");
        } else
            if (DEMO_STATUS == "yes") {
            $reg_post_array = $this->register_model->getDefaultData();
            $reg_count = count($reg_post_array);
        }

        $is_product_added = "";
        if ($this->MODULE_STATUS['product_status'] == "yes") {
            $is_product_added = $this->register_model->isProductAdded();
        }

        $is_pin_added = "";
        if ($this->MODULE_STATUS['pin_status'] == "yes") {
            $is_pin_added = $this->register_model->isPinAdded();
        }

        if ($this->session->userdata('inf_error')) {
            $error = $this->session->userdata('inf_error');
            $this->set('error', $error);
            $this->session->unset_userdata('inf_error');
        }

        $payment_methods_tab = false;
        $payment_gateway_array = array();
        $payment_module_status_array = array();
        $registration_fee = $this->register_model->getRegisterAmount();
        $registration_fee1 = round($registration_fee * $this->DEFAULT_CURRENCY_VALUE, 8);

        if ($registration_fee || $this->MODULE_STATUS['product_status'] == 'yes') {
            $payment_methods_tab = true;
            $payment_gateway_array = $this->register_model->getPaymentGatewayStatus("registration");
            $payment_module_status_array = $this->register_model->getPaymentModuleStatus();

            $payment_gateway_using_reg_status = $this->register_model->getPaymentGatewayUsingRegistration('registration');
        } else {
            $payment_gateway_using_reg_status['gateway_name'] = "Free Joining";
        }

        $termsconditions = $this->register_model->getTermsConditions($this->LANG_ID);
        $username_config = $this->configuration_model->getUsernameConfig();
        $user_name_type = $username_config["type"];
        $contact_fields = $this->register_model->getContactInfoFields();
        $register_language_id  = $this->LANG_ID ? $this->LANG_ID : 1;
        $lang_arr = $this->configuration_model->getLanguages();
        $bank_details=$this->configuration_model->getBankInfo();
        $this->set('bank_details',$bank_details);
        $this->set('signup_settings', $signup_settings);
        $this->set('reg_from_tree', $reg_from_tree);
        $this->set('pin_count', $pin_count);
        $this->set('reg_post_array', $reg_post_array);
        $this->set('reg_count', $reg_count);
        $this->set("sponsor_user_name", $sponsor_user_name);
        $this->set("user_id", $user_id);
        $this->set('position', $position);
        $this->set("placement_full_name", $placement_full_name);
        $this->set("placement_user_name", $placement_user_name);
        $this->set('user_name_type', $user_name_type);
        $this->set('payment_methods_tab', $payment_methods_tab);
        $this->set('payment_gateway_array', $payment_gateway_array);
        $this->set('payment_module_status_array', $payment_module_status_array);
        $this->set("registration_fee", $registration_fee);
        $this->set("registration_fee1", $registration_fee1);
        $this->set('termsconditions', $this->security->xss_clean($termsconditions));
        $this->set("products", $this->security->xss_clean($products));
        $this->set("is_pin_added", $is_pin_added);
        $this->set('is_product_added', $is_product_added);
        $this->set('from_replica', $replica);
        $this->set('countries', $countries);
        $this->set("states", $states);
        $this->set('fields', $contact_fields);
        $this->set('selected_language_id', $register_language_id);
        $this->set('lang_arr', $lang_arr);
        $this->set('payment_gateway_using_reg_status', $payment_gateway_using_reg_status);
        $this->set('passwordPolicyJson', json_encode($this->validation_model->getPasswordPolicyArray()));

        $this->setView();
    }

    function register_submit()
    {
        if ($this->session->userdata("inf_language")) {
            $this->lang->load('common', $this->session->userdata("inf_language")['lang_name_in_english']);
            $this->lang->load($this->CURRENT_CTRL, $this->session->userdata("inf_language")['lang_name_in_english']);
        }
        
        // Change redirect URL if registration is from REPLICA 
        $by_replica = false;
        if (isset($this->session->userdata['from_replica']) && !empty($this->session->userdata('from_replica'))) {
            $redirect_url = "register/replica_register";
            $by_replica = true;
        } else {
            $redirect_url = "register/user_register";
        }
        
        if ($this->BLOCK_REGISTER && !in_array($this->LOG_USER_TYPE, ['admin', 'employee'])) {
            $this->redirect(lang('you_cant_access_page_due_to_block_status'), 'home', false);
        }

        $signup_settings = $this->configuration_model->getGeneralSignupConfig();
        if ($signup_settings['registration_allowed'] == 'no' && !in_array($this->LOG_USER_TYPE, ['admin', 'employee']) && !$by_replica) {
            $this->redirect(lang('registration_not_allowed'), 'home', false);
        }

        $regr = [];
        $reg_post_array              = $this->input->post(null, true);
        $payment_module_status_array = $this->register_model->getPaymentModuleStatus();
        $payment_gateway_array       = $this->register_model->getPaymentGatewayStatus("registration");

        if ($reg_post_array['active_tab'] == 'free_join_tab' && $payment_module_status_array['free_joining_type'] == 'no') {
            $this->redirect(lang('please_choose_a_payment_method'), $redirect_url, false);
        }

        if ($this->LOG_USER_TYPE == 'user' && !$by_replica && $reg_post_array['sponsor_user_name'] != $this->LOG_USER_NAME) {
            $this->redirect(lang('invalid_sponser_user_name'), $redirect_url, false);
        }

        $this->session->set_userdata('inf_reg_post_array', $reg_post_array);

        if ($this->input->post('active_tab') != 'bank_transfer') {
            $this->session->unset_userdata('file');
        }

        if ($this->validate_register_submit()) {
            $payment_status  = $is_pin_ok = $is_ewallet_ok = $is_paypal_ok = $is_authorize_ok = $is_blockchain_ok = $is_bitgo_ok = $is_bank_transfer_ok = $is_payeer_ok = $is_sofort_ok = $is_squareup_ok = $is_stripe_ok = false;
            $payment_type    = 'free_join';

            $module_status   = $this->MODULE_STATUS;
            $username_config = $this->configuration_model->getUsernameConfig();
            $reg_post_array  = $this->validation_model->stripTagsPostArray($reg_post_array);
            $reg_from_tree   = $reg_post_array['reg_from_tree'];
            $active_tab      = $reg_post_array['active_tab'];
            
            $regr = $reg_post_array;
            if ($this->MLM_PLAN == "Unilevel" || $this->MLM_PLAN == "Stair_Step") {
                $regr['placement_user_name'] = $reg_post_array["sponsor_user_name"];
            }

            $product_details = $this->getProductDetails($reg_post_array['product_id']);

            $regr['reg_amount']       = $this->register_model->getRegisterAmount();
            $regr['product_status']   = $this->MODULE_STATUS['product_status'];
            $regr['product_id']       = $reg_post_array['product_id'];
            $regr['product_name']     = $product_details['name'];
            $regr['product_pv']       = $product_details['name'];
            $regr['product_amount']   = $product_details['amount'];
            $regr['product_validity'] = $product_details['validity'];
            $regr['total_amount']     = (float)$regr['reg_amount'] + (float)$regr['product_amount'];
            $regr['user_name_type']   = $username_config["type"];
            $regr['joining_date']     = date('Y-m-d H:i:s');
            $regr['active_tab']       = $active_tab;
            $regr['reg_from_tree']    = $reg_from_tree;
            $regr['sponsor_id']       = $this->validation_model->userNameToID($regr['sponsor_user_name']);
            $regr['placement_id']     = $this->validation_model->userNameToID($regr['placement_user_name']);
            $support_board=$this->validation_model->getPackageSupportBasedOnProductId($regr['product_id'],'board');
            $downline_data='';
            if($support_board=='yes' && $regr['sponsor_id']){
                $downlines=$this->getBoardDownlines($regr['sponsor_user_name']);
                $support_board='no';
                if(!empty($downlines)){
                    $downline_count=count($downlines['data'])??0;
                    if($downline_count>2){
                        
                        if(!isset($regr['board_downline'])){
                            $this->redirect('Please choose user for gifting', $redirect_url, false);
                        }
                    }
                }
            }
            $registration_fee         = $this->register_model->getRegisterAmount();
            if ($this->MODULE_STATUS['product_status'] == 'yes' || $registration_fee > 0) {
                if ($active_tab == 'epin_tab') {
                    $payment_type = 'epin';
                    $pin_array = $this->getEpins($reg_post_array['product_id'], $regr['sponsor_id']);
                    $pin_count = count($pin_array) - 1;
                    $is_pin_ok = $pin_array['is_pin_ok'];
                    if (!$pin_array['is_pin_ok']) {
                        $this->redirect($this->lang->line('Invalid_Epins'), $redirect_url, false);
                    } 
                } elseif ($active_tab == 'ewallet_tab') {
                    $payment_type = 'ewallet';
                    $used_amount = $regr['total_amount'];
                    $ewallet_user = $reg_post_array['user_name_ewallet'];
                    $ewallet_trans_password = $reg_post_array['tran_pass_ewallet'];
                    $admin_username = $this->validation_model->getAdminUsername();
                    if ($ewallet_user != "") {
                        if ($this->LOG_USER_TYPE == 'admin' || $this->LOG_USER_TYPE == 'employee') {
                            if ($ewallet_user != $admin_username && $ewallet_user != $regr['sponsor_user_name']) {
                                $this->redirect($this->lang->line('invalid_user_name_ewallet_tab'), $redirect_url, false);
                            }
                        } else if ($this->LOG_USER_TYPE == 'user') {
                            if ($ewallet_user != $regr['sponsor_user_name'] || $ewallet_user != $this->LOG_USER_NAME) {
                                $this->redirect($this->lang->line('invalid_user_name_ewallet_tab'), $redirect_url, false);
                            }
                        }

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
                                        $this->redirect($this->lang->line('insuff_bal'), $redirect_url, false);
                                    }
                                } else {
                                    $this->redirect($this->lang->line('invalid_transaction_password_ewallet_tab'), $redirect_url, false);
                                }
                            } else {
                                $this->redirect($this->lang->line('invalid_transaction_password_ewallet_tab'), $redirect_url, false);
                            }
                        } else {
                            $this->redirect($this->lang->line('invalid_user_name_ewallet_tab'), $redirect_url, false);
                        }
                    } else {
                        $this->redirect($this->lang->line('invalid_user_name_ewallet_tab'), $redirect_url, false);
                    }
                } elseif (($active_tab == "paypal_tab")) {
                    if ($payment_gateway_array['paypal_status'] == "no") {
                        $this->redirect(lang('payment_method_not_available'), $redirect_url, false);
                    }
                    $payment_type = 'paypal';
                    $is_paypal_ok = true;
                } elseif (($active_tab == "authorize_tab")) {
                    if ($payment_gateway_array['authorize_status'] == "no") {
                        $this->redirect(lang('payment_method_not_available'), $redirect_url, false);
                    }
                    $payment_type = 'authorize.net';
                    $is_authorize_ok = true;
                } elseif (($active_tab == "blockchain_tab")) {
                    if ($payment_gateway_array['blockchain_status'] == "no") {
                        $this->redirect(lang('payment_method_not_available'), $redirect_url, false);
                    }
                    $payment_type = 'blockchain';
                    $is_blockchain_ok = true;
                } elseif (($active_tab == "bitgo_tab")) {
                    if ($payment_gateway_array['bitgo_status'] == "no") {
                        $this->redirect(lang('payment_method_not_available'), $redirect_url, false);
                    }
                    $payment_type = 'bitgo';
                    $is_bitgo_ok = true;
                } elseif (($active_tab == "bank_transfer")) {
                    $payment_type = 'bank_transfer';
                    $is_bank_transfer_ok = true;
                } elseif ($active_tab == "payeer_tab") {
                    if ($payment_gateway_array['payeer_status'] == "no") {
                        $this->redirect(lang('payment_method_not_available'), $redirect_url, false);
                    }
                    $payment_type = 'payeer';
                    $is_payeer_ok = true;
               } elseif ($active_tab == "stripe_tab") {
                    if ($payment_gateway_array['stripe'] == "no") {
                        $this->redirect(lang('payment_method_not_available'), $redirect_url, false);
                    }
                    $payment_type = 'stripe';
                    $is_stripe_ok = true;


                } elseif ($active_tab == "sofort_tab") {
                    if ($payment_gateway_array['sofort_status'] == "no") {
                        $this->redirect(lang('payment_method_not_available'), $redirect_url, false);
                    }
                    $payment_type = 'sofort';
                    $is_sofort_ok = true;
                } elseif ($active_tab == "squareup_tab") {
                    if ($payment_gateway_array['squareup_status'] == "no") {
                        $this->redirect(lang('payment_method_not_available'), $redirect_url, false);
                    }
                    $payment_type = 'squareup';
                    $is_squareup_ok = true;
                } elseif ($active_tab == "free_join_tab") {
                    $payment_type = 'free_join';
                    $is_free_join_ok = true;
                } else {
                    $this->redirect(lang('please_choose_a_payment_method'), $redirect_url, false);
                }

                $regr['payment_type'] = $payment_type;
                $pending_signup_status = $this->configuration_model->getPendingSignupStatus($payment_type);
                $email_verification = $this->configuration_model->getEmailVerificationStatus();

                if ($is_pin_ok) {
                    $this->register_model->begin();
                    $regr['by_using'] = 'pin';
                    $res = $this->register_model->UpdateUsedUserEpin($pin_array, $pin_count);
                    $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);
                    if ($status['status']) {
                        $pin_array['user_id'] = $status['id'];
                        $res = $this->register_model->UpdateUsedEpin($pin_array, $pin_count);
                        if ($res) {
                            $this->register_model->insertUsedPin($pin_array, $pin_count, $pending_signup_status, '', $email_verification);
                            $payment_status = true;
                        }
                    }
                } elseif ($is_ewallet_ok) {
                    $this->register_model->begin();

                    /*$regr['by_using'] = 'ewallet';
                    $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);
                    if ($status['status']) {
                        $user_id = $status['id'];
                        $used_user_id = $this->validation_model->userNameToID($ewallet_user);
                        $transaction_id = $this->ewallet_model->getUniqueTransactionId();
                        $res1 = $this->register_model->insertUsedEwallet($used_user_id, $user_id, $used_amount, $transaction_id, $pending_signup_status, 'registration', $email_verification);
                        if ($res1) {
                            $res2 = $this->register_model->deductFromBalanceAmount($used_user_id, $used_amount);
                            if ($res2) {
                                $payment_status = true;
                            }
                        }
                    }*/
                    $used_user_id = $this->validation_model->userNameToID($ewallet_user);
                    $transaction_id = $this->ewallet_model->getUniqueTransactionId();
                    $ewallet_id = $this->register_model->insertUsedEwallet($used_user_id, $used_user_id, $used_amount, $transaction_id, $pending_signup_status, 'registration', $email_verification);
                    if($ewallet_id) {
                        $res2 = $this->register_model->deductFromBalanceAmount($used_user_id, $used_amount);
                    }

                    if($ewallet_id && $res2) {
                        $regr['by_using'] = 'ewallet';
                        $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);
                        if($status['status']) {
                             //if (!$pending_signup_status && $email_verification != 'yes') {
                                $this->register_model->updateEwalletDetails($ewallet_id, $status['id'],$pending_signup_status,$email_verification);
                             //}
                            $payment_status = true;

                        }
                    }
                } elseif ($is_paypal_ok) {
                    $regr['by_using'] = 'paypal';
                    $this->session->set_userdata('inf_regr', $regr);
                    $msg = "";
                    if ($by_replica) {
                        $link  = 'register/replica_pay_now';
                    } else {
                        $link  = 'register/pay_now';
                    }
                    $this->redirect($msg, $link, false);
                } elseif ($is_authorize_ok) {
                    $regr['by_using'] = 'Authorize.Net';
                    $this->session->set_userdata('inf_regr', $regr);
                    $msg = "";
                    if ($by_replica) {
                        $link  = 'register/replica_authorizeNetPayment';
                    } else {
                        $link  = 'register/authorizeNetPayment';
                    }
                    $this->redirect($msg, $link, false);
                } elseif ($is_blockchain_ok) {
                    $regr['by_using'] = 'blockchain';
                    $this->session->set_userdata('inf_regr', $regr);
                    $msg = "";
                    if ($by_replica) {
                        $link  = 'register/replica_blockchain';
                    } else {
                        $link  = 'register/blockchain';
                    }
                    $this->redirect($msg, "register/blockchain", false);
                } elseif ($is_bitgo_ok) {
                    $regr['by_using'] = 'bitgo';
                    $regr['is_new'] = 'yes';
                    $this->session->set_userdata('inf_regr', $regr);
                    $msg = "";
                    if ($by_replica) {
                        $link  = 'register/replica_bitgo_gateway';
                    } else {
                        $link  = 'register/bitgo_gateway';
                    }
                    $this->redirect($msg, $link, false);
                } elseif ($is_payeer_ok) {
                    $regr['by_using'] = 'payeer';
                    $data = array(
                        'user_id' => $this->LOG_USER_ID,
                        'product_id' => $reg_post_array['product_id'],
                        'product_name' => $product_details['name'],
                        'product_amount' => $product_details['amount'],
                        'currency' => 'EUR',
                    );
                    $msg = "";
                    $this->session->set_userdata('payeer_data', $data);
                    if ($by_replica) {
                        $link  = 'register/replica_payeer';
                    } else {
                        $link  = 'register/payeer';
                    }
                    $this->redirect($msg, $link, false);
                } elseif ($is_sofort_ok) {
                    $regr['by_using'] = 'sofort';
                    $this->session->set_userdata('inf_regr', $regr);
                    $msg = "";
                    if ($by_replica) {
                        $link  = 'register/replica_sofort_payment';
                    } else {
                        $link  = 'register/sofort_payment';
                    }
                    $this->redirect($msg, $link, false);
                } elseif ($is_squareup_ok) {
                    $regr['by_using'] = 'squareup';
                    $this->session->set_userdata('inf_regr', $regr);
                    $msg = "";
                    if ($by_replica) {
                        $link  = 'register/replica_squareup_gateway';
                    } else {
                        $link  = 'register/squareup_gateway';
                    }
                    $this->redirect($msg, $link, false);
                } elseif ($is_bank_transfer_ok) {

                    $regr['by_using'] = 'bank_transfer';
                    $this->register_model->begin();

                    $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);
                    if ($status['status']) {
                        $payment_status = true;
                    }
                 } elseif ($is_stripe_ok) {
                    $regr['by_using'] = 'stripe';
                    $this->session->set_userdata('inf_regr', $regr);
                    $msg = "";
                    if ($by_replica) {
                        $link  = 'register/stripe_payment';
                    } else {
                        $link  = 'register/stripe_payment';
                    }
                    $this->redirect($msg, $link, false);                 
                } else {
                    $regr['by_using'] = 'free join';
                    $this->register_model->begin();
                    $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);
                    if ($status['status']) {
                        $payment_status = true;
                    }
                }
            } else {
                $regr['by_using'] = 'free join';
                $payment_type = 'free_join';
                $regr['payment_type'] = $payment_type;
                $pending_signup_status = false;
                $email_verification = $this->configuration_model->getEmailVerificationStatus();
                $this->register_model->begin();
                $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);

                if ($status['status']) {

                    $payment_status = true;
                }
            }
            $msg = '';
            if ($payment_status && $this->validation_model->isUserActive($regr['sponsor_id'])) {

                $user_name = $status['user_name'];
                $user_id = $status['user_id'];
                $transaction_password = ($pending_signup_status || ($email_verification == 'yes'))  ? '' : $status['transaction_password'];
                ////for referal commision of extra users for founder pack////

                // $referal_commission_status = $this->validation_model->getCompensationConfig(['referal_commission_status']);
                // if(isset($status['referal_commission_data'])){
                //     if ($referal_commission_status == "yes") {
                //         foreach ($status['referal_commission_data'] as $row) {
                //             $direct_commission = $this->calculation_model->calculateReferralCommission($row['sponsor_id'], $row['user_id'],$row['product_pv']);
                // // dd($direct_commission);
                //         }   
                //     }
                // }
                if ($this->MODULE_STATUS['product_status'] == "yes") {

                    $insert_into_sales = $this->register_model->insertIntoSalesOrder($user_id, $regr['product_id'], $payment_type, $pending_signup_status, $email_verification);
                }
                $wallet = array('0' => 'anm','1' =>'hajar','2'=>'panda','3'=>'raed','4'=>'agent' );
                foreach ($wallet as $key => $value) {
                    $this->register_model->addToCwallet($regr, $status['id'], $value,'registration',$regr['product_id']);
                }
                
                $this->register_model->commit();
                $activity_user_id = 0;
                if ($by_replica) {
                    $activity_user_id = $regr['sponsor_id'];
                } elseif ($this->LOG_USER_ID) {
                    $activity_user_id = $this->LOG_USER_ID;
                }
                $this->validation_model->insertUserActivity($activity_user_id, 'New user registered', $user_id);


                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'user_register', 'New user registered', $pending_signup_status);
                }
                $this->session->unset_userdata(['inf_regr', 'inf_reg_post_array', 'from_replica']);
                if ($pending_signup_status) {

                    $this->session->unset_userdata('file');

                    $msg = "<span><b>" . lang('registration_completed_successfully_pending') . "!</b> " . lang("User_Name") . ": {$user_name}";
                } elseif (($email_verification == 'yes') && ($pending_signup_status == 0)) {

                    $msg = "<span><b>" . lang('email_verification_required') . "!</b> " . lang("User_Name") . ": {$user_name}";
                } else {

                    $this->session->unset_userdata('file');
                    $msg = "<span><b>" . lang('registration_completed_successfully') . "!</b> " . lang("User_Name") . ": {$user_name} " . lang("transaction_password") . ": {$transaction_password}</span>";
                }

                if ($redirect_url == 'register/replica_register') {
                    $this->redirect($msg, "register/replica_preview/{$user_name}", true);
                } else {
                    $this->redirect($msg, "register/preview/{$user_name}", true);
                }
            } else {

                $this->register_model->rollback();
                if (isset($status['error'])) {
                    $msg = $status['error'];
                } else {
                    $msg = lang('registration_failed');
                }
                $this->session->unset_userdata(['inf_regr', 'inf_reg_post_array', 'file', 'from_replica']);
                $this->redirect($msg, $redirect_url, false);
            }
        } else {
            $this->session->unset_userdata(['inf_regr', 'inf_reg_post_array', 'file', 'from_replica']);
            $this->session->set_userdata('inf_error', $this->form_validation->error_array());
            return $this->user_register("", "");
        }
    }

    protected function getEpins($product_id, $sponsor_id) {
        $pin_details = [];
        for ($i = 1; $i <= $this->input->post('pin_count'); $i++) {
            if ($this->input->post('epin'.$i)) {
                $pin_number = $this->input->post('epin'.$i);
                $pin_details[$i]['pin'] = $pin_number;
                $pin_details[$i]['i'] = $i;
            }
        }
        return $this->register_model->checkAllEpins($pin_details, $product_id, $this->MODULE_STATUS['product_status'], $sponsor_id, true);
    }

    
    protected function getProductDetails($product_id) {
        $product_details = [
            'name'    => '',
            'pv'      => '',
            'amount'  => '',
            'validity' => ''
        ];
        if ($this->MODULE_STATUS['product_status'] == "yes") {
            $product = $this->product_model->getProductDetails($product_id, 'yes');
            $product_details['name'] = $product[0]['product_name'];
            $product_details['pv'] = $product[0]['pair_value'];
            $product_details['amount'] = $product[0]['product_value'];
            if ($this->MODULE_STATUS['subscription_status'] == "yes") {
                $product_details['validity'] = $this->product_model->getPackageValidityDate($product[0]['prod_id'], '', $this->MODULE_STATUS);
            }
        }
        return $product_details;
    }

    function validate_register_submit() {
        $this->lang->load('validation');
        $product_status = $this->MODULE_STATUS['product_status'];
        $username_config = $this->configuration_model->getUsernameConfig();
        $user_name_type = $username_config["type"];
        $active_tab = $this->input->post('active_tab', true);
        $reg_from_tree = $this->input->post('reg_from_tree', true);
        $pin_count = $this->input->post('pin_count', true);
        $contact_fields = $this->register_model->getSignUpAllFieldStatus();
        if ($reg_from_tree && !in_array($this->MLM_PLAN, ["Unilevel", "Stair_Step"])) {
            $this->form_validation->set_rules('placement_user_name', lang('placement_user_name'), 'required|callback_validate_username|trim');
        }
        
        $this->form_validation->set_rules('sponsor_user_name', lang('sponsor_user_name'), 'required|callback_validate_username|trim', [
            'required' => lang('required'),
            'validate_username' => lang('invalid'),
        ]);
        
        if ($this->MLM_PLAN == 'Binary') {
            $this->form_validation->set_rules('position', lang('position'), 'trim|required|in_list[L,R]|callback_vm_check_sponsor_leg_available', [
                'required' => lang('required'),
                'in_list' => lang('invalid')
            ]);
        }

        if ($product_status == "yes") {
            $this->form_validation->set_rules('product_id', lang('product'), 'trim|required|callback_valid_product[registration]', [
                'required' => lang('required'),
                'valid_product' => lang('invalid')
            ]);
        }

        $usernameRange = $this->validation_model->getUsernameRange();
        $minlength = $usernameRange['min'];
        $maxlength = $usernameRange['max'];
        if ($user_name_type == 'static') {
            $this->form_validation->set_rules('user_name_entry', lang('User_Name'), 'required|strtolower|regex_match[/^[a-zA-Z0-9_.-]+$/]|min_length[' . "$minlength" . ']|max_length[' . "$maxlength" . ']|callback_is_username_available', [
                'regex_match' => lang('alpha_numeric_some_special'),
                'required' => lang('required'),
                'min_length' => sprintf(lang('minlength'), lang('username'), "$minlength"),
                'max_length' => sprintf(lang('maxlength'), lang('username'), "$maxlength"),
                'is_username_available' => lang('username_not_available')
            ]);
        }

        $this->form_validation->set_rules('pswd', lang('password'), "{$this->validation_model->getPasswordPolicyValidationString()}", [
            'required' => lang('required'),
            'max_length' => sprintf(lang('maxlength'), lang('password'), "50")
        ]);
        $this->form_validation->set_rules('cpswd', lang('confirm_password'), 'trim|matches[pswd]', [
            'matches' => lang('password_mismatch')
        ]);
        
        if ($contact_fields['first_name'] == "yes") {
            $this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required|max_length[250]', [
                'required' => lang('required'),
                'max_length' => sprintf(lang('maxlength'), lang('first_name'), "250"),
            ]);
        }

        if ($contact_fields['last_name'] == "yes") {
            $this->form_validation->set_rules('last_name', lang('last_name'), 'trim|max_length[250]|callback_check_required[last_name]', [
                'check_required' => lang('required'),
                'max_length' => sprintf(lang('maxlength'), lang('last_name'), "250"),
            ]);
        }
        if ($contact_fields['date_of_birth'] == "yes") {
            $this->form_validation->set_rules('date_of_birth', lang('date_of_birth'), 'trim|callback_validate_age_year|callback_check_required[date_of_birth]', [
                'validate_age_year' => sprintf(lang('valid_age'), $this->configuration_model->getAgeLimitSetting()),
                'check_required' => lang('required')
            ]);
        }

        if ($contact_fields['email'] == "yes") {
            $this->form_validation->set_rules('email', lang('email'), 'trim|required|strtolower|max_length[250]|strtolower|valid_email', [
                'required' => lang('required'),
                'max_length' => sprintf(lang('maxlength'), lang('email'), "250"),
                'valid_email' => lang('valid_email'),
            ]);
        }

        if ($contact_fields['mobile'] == "yes") {
            $this->form_validation->set_rules('mobile', lang('mobile_no'), 'trim|required|regex_match[/^[\s0-9+()-]+$/]|max_length[50]', [
                'required' => lang('required'),
                'max_length' => sprintf(lang('maxlength'), lang('mobile_no'), "50"),
                'regex_match' => lang('phone_number'),
            ]);
        }
        
        if ($contact_fields['gender'] == "yes") {
            $this->form_validation->set_rules('gender', lang('gender'), 'trim|callback_check_required[gender]|in_list[M,F]', ['in_list' => lang('You_must_select_gender')]);
        }

        if ($contact_fields['adress_line1'] == "yes") {
            $this->form_validation->set_rules('adress_line1', lang('adress_line1'), 'trim|max_length[1000]|callback_check_required[adress_line1]', [
                'max_length' => sprintf(lang('maxlength'), lang('address_line1'), "1000"),
                'check_required' => lang('required')
            ]);
        }
        
        if ($contact_fields['adress_line2'] == "yes") {
            $this->form_validation->set_rules('adress_line2', lang('adress_line2'), 'trim|max_length[1000]|callback_check_required[adress_line2]', [
                'max_length' => sprintf(lang('maxlength'), lang('address_line2'), "1000"),
                'check_required' => lang('required')
            ]);
        }
        
        if ($contact_fields['country'] == "yes") {
            $this->form_validation->set_rules('country', lang('country'), 'trim|callback_check_required[country]', [
                'check_required' => lang('required')
            ]);
        }
        
        if ($contact_fields['state'] == "yes") {
            $this->form_validation->set_rules('state', lang('state'), 'trim|callback_check_required[state]', [
                'check_required' => lang('required')
            ]);
        }
        
        if ($contact_fields['city'] == "yes") {
            $this->form_validation->set_rules('city', lang('city'), 'trim|max_length[250]|callback_check_required[city]', [
                'max_length' => lang('string_max_length'),
                'check_required' => lang('required')
            ]);
        }
        
        if ($contact_fields['pin'] == "yes") {
            $this->form_validation->set_rules('pin', lang('pin'), 'trim|max_length[50]|callback_check_required[pin]', [
                'max_length' => sprintf(lang('maxlangth'), lang('pin'), "50"),
                'check_required' => lang('required'),
            ]);
        }
        
        if ($contact_fields['land_line'] == "yes") {
            $this->form_validation->set_rules('land_line', lang('land_line'), 'trim|regex_match[/^[\s0-9+()-]+$/]|max_length[50]|callback_check_required[land_line]', [
                'max_length' => sprintf(lang('maxlength'), lang('land_line'), "50"),
                'check_required' => lang('required'),
                'regex_match' => lang('phone_number')
            ]);
        }

        $this->form_validation->set_rules('agree', lang('terms_conditions'), 'trim|required', [
            'required' => lang('agree')
        ]);
        // $this->form_validation->set_message('check_required', lang('the_%s_field_must_be_exactly_10_digit'));
        if ($active_tab == 'epin_tab') {
            $temp_pin_array = "";
            $this->session->set_userdata("inf_temp_pin_array", $temp_pin_array);
            for ($i = 1; $i <= $pin_count; $i++) {
                if ($this->input->post("epin$i")) {
                    $this->form_validation->set_rules("epin$i", lang('epin') . $i, 'trim|required|callback_has_match', [
                        'required' => lang('required')
                    ]);
                }
            }
            $this->session->unset_userdata("inf_temp_pin_array");
        }
        if ($active_tab == 'ewallet_tab') {
            $this->form_validation->set_rules('user_name_ewallet', lang('ewallet_user_name'), 'trim|required', [
                'required' => lang('required')
            ]);
            $this->form_validation->set_rules('tran_pass_ewallet', lang('transaction_password'), 'trim|required', [
                'required' => lang('required')
            ]);
        }
        if ($active_tab == 'bank_transfer') {
            $this->form_validation->set_rules('active_tab', lang('upload_reciepts..'), 'required|callback_vm_check_file_uploaded');
        }
        if($this->input->post('product_id')==2){
            $this->form_validation->set_rules('downline_user_position', lang('downline_user_position'), 'trim|required|in_list[BOTH,L,R]', [
                'required' => lang('required'),
                'in_list' => lang('invalid')
            ]);
        }
        
        $validation_status = $this->form_validation->run_with_redirect('register/user_register');
        return $validation_status;
    }

    function vm_check_file_uploaded() {
        $this->form_validation->set_message('vm_check_file_uploaded', lang('upload_reciepts..'));
        return !empty($this->session->userdata('file'));
    }

    function vm_check_sponsor_leg_available() {
        $placement_user_name = $this->input->post('placement_user_name') ?? '';
        $checkLegAvailable = $this->checkSponsorLegAvailable($this->input->post('position'), $this->input->post('sponsor_user_name'), $placement_user_name);
        $this->form_validation->set_message('vm_check_sponsor_leg_available', lang('position_not_useable'));
        return $checkLegAvailable == 'no' ? FALSE : TRUE;
    }

    function valid_product($product_id, $type)
    {
        $res = $this->product_model->isActiveProduct($product_id, $type);
        $this->form_validation->set_message('valid_product', lang('you_must_select_product'));
        return ($res > 0);
    }

    function validate_username($username = '')
    {
        $crowd_stat = ($this->validation_model->getModuleStatusByKey('crowd_fund') == "yes") ? true : false;
        if ($username != '') {
            $flag = false;
            if ($this->validation_model->userNameToID($username)) {
                $flag = true;
                return $flag;
            }

            if ($crowd_stat) {
                $flag = $this->validation_model->isMemberAvailable($username);
            }
            return $flag;
        } else {
            // return false;
            $echo = 'no';
            $username = ($this->input->post('username', true));

            if ($id = $this->validation_model->userNameToID($username)) {
                if($this->validation_model->isUserActive($id)){
                $echo = "yes";
                }
            }

            if ($crowd_stat) {
                if ($this->validation_model->isMemberAvailable($username)) {
                    if($this->validation_model->isUserActive($id)){
                    $echo = "yes";
                    }
                }
            }
            echo $echo;
            exit();
        }
    }

    function getUsernameRange()
    {

        $usernameRange = $this->validation_model->getUsernameRange();

        echo json_encode($usernameRange);
        exit();
    }

    public function check_leg_availability()
    {
        $sponsor_leg = $this->input->post('sponsor_leg');
        $sponsor_user_name = $this->input->post('sponsor_user_name');
        $placement_user_name = $this->input->post('placement_user_name');
        echo $this->checkSponsorLegAvailable($sponsor_leg, $sponsor_user_name, $placement_user_name);
    }

    function checkSponsorLegAvailable($sponsor_leg, $sponsor_user_name,$placement_user_name = "") {
        $this->load->model('configuration_model');
        $this->load->model('tree_model');

        $sponsor_id = $this->validation_model->userNameToID($sponsor_user_name);
        if(!$sponsor_id) {
            return 'no';
        }
        $sponsor_user_type = $this->validation_model->getUserType($sponsor_id);

        $admin_locked_binary_leg = $this->configuration_model->getSignupBinaryLeg();
        if($admin_locked_binary_leg != 'any')
        {
            if($sponsor_user_type == "admin") {
                if($admin_locked_binary_leg != $sponsor_leg) {
                    return 'no';
                }
            } else {
                $admin_legs = $this->tree_model->getUserLeftRightNode($this->ADMIN_USER_ID);
                $admin_leg_id  = $admin_legs[$admin_locked_binary_leg];
                if(!$this->tree_model->checkAncestor($admin_leg_id, $sponsor_id)) {
                    return 'no';
                }
            }
            return 'yes';
        }

        if(in_array($this->LOG_USER_TYPE, ['admin','employee'])) {
            return 'yes';
        }

        $user_locked_binary_leg = $this->configuration_model->getUserWiseSignupBinaryLeg($sponsor_id);
        if($user_locked_binary_leg == "any") {
            return 'yes';
        }
        if($placement_user_name == $sponsor_user_name || !$placement_user_name) {
            if ($user_locked_binary_leg != $sponsor_leg) {
                return 'no';
            }
        } else {
            // from tree
            $placement_id = $this->validation_model->userNameToID($placement_user_name);
            $sponsor_legs = $this->tree_model->getUserLeftRightNode($sponsor_id);
            $sponsor_leg_id  = $sponsor_legs[$user_locked_binary_leg];
            if(!$this->tree_model->checkAncestor($sponsor_leg_id, $placement_id)) {
                return 'no';
            }
        }
        return 'yes';
    }

    function get_sponsor_full_name()
    {
        $username = ($this->input->post('sponsor_user_name', true));
        $user_id = $this->validation_model->userNameToID($username);
        $referral_name = $this->register_model->getReferralName($user_id);
        $crowd_stat = ($this->validation_model->getModuleStatusByKey('crowd_fund') == "yes") ? true : false;
        if ($crowd_stat && !$referral_name) {
            $user_id = $this->validation_model->memberUserNameToID($username);
            $referral_name = $this->validation_model->getReferralNameMember($user_id);
        }
        echo $referral_name;
        exit();
    }

    function get_total_registration_fee()
    {
        $product_id = $this->input->post('product_id', true);
        $product_amount = 0;
        if ($product_id) {
            $product_amount = $this->register_model->getProductAmount($product_id);
        }
        $registration_fee = $this->register_model->getRegisterAmount();

        $total_fee = $product_amount + $registration_fee;

        echo "$registration_fee==$product_amount==$total_fee";
        exit();
    }

    function checkPassAvailability()
    {

        if ($this->register_model->checkPassCode($this->input->post('prodcutpin'), $this->input->post('prodcutid'))) {
            echo "yes";
            exit();
        } else {
            echo "no";
            exit();
        }
    }

    function checkSponsorAvailability()
    {

        if ($this->register_model->checkSponser($this->input->post('sponser_name'), $this->input->post('user_id'))) {
            echo "yes";
            exit();
        } else {
            echo "no";
            exit();
        }
    }

    function get_states($country_id)
    {
        $state_select = '';

        $state_string = $this->country_state_model->viewState($country_id);
        if ($state_string != '') {
            $state_select .= "<option value =''>" . $this->lang->line('select_state') . "</option>";
            $state_select .= $state_string;
        } else {
            $state_select .= "<option value='0'>" . $this->lang->line('no_data_available') . "</option>";
        }
        $state_select .= '</select></div>';
        echo $state_select;
        exit();
    }

    function get_phone_code($country_id)
    {
        $country_telephone_code = $this->country_state_model->getCountryTelephoneCode($country_id);
        echo "+$country_telephone_code";
    }

    function preview($user_name = "")
    {
        $from_replica = FALSE;
        if ($this->uri->segment(1) == 'replica_preview') {
            $from_replica = TRUE;
            $replica_lang = $this->session->userdata('replica_language');
            if ($replica_lang) {
                $this->lang->load($this->CURRENT_CTRL, $replica_lang['lang_name_in_english']);
            }
            if ($this->session->userdata("inf_language")) {
                $this->lang->load('common', $this->session->userdata("inf_language")['lang_name_in_english']);
                $this->lang->load($this->CURRENT_CTRL, $this->session->userdata("inf_language")['lang_name_in_english']);
            }
        }

        $title = lang('letter_preview');
        $this->set('title', $this->COMPANY_NAME . " | $title");

        $help_link = "register_downline";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('letter_preview');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('letter_preview');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        /*$user_name = urldecode($user_name);
        $user_name = str_replace("_", "/", $user_name);
        $user_name = $this->encrypt->decode($user_name);*/
        $user_id = $this->validation_model->userNameToID($user_name);
        $is_pending_registration = $this->validation_model->isPendingUserRegistration($user_name);
        $email_verification = $this->configuration_model->getEmailVerificationStatus();
        if ($email_verification == 'yes' && !$is_pending_registration) {
            $msg = lang('email_verification_required') . ":{$user_name}";
            $this->redirect($msg, "home", false);
        }
        if (!$user_id && !$is_pending_registration) {
            $this->redirect("Invalid User Details.", "home", false);
        }

        if (DEMO_STATUS == 'no' && $this->check_replica_user()) {
            $replica_session = $this->inf_model->getReplicaSessionFromFile();
            $replica_user = $replica_session['replica_user'];
            $sponsor_user_name = $replica_user['user_name'];
            $this->set("sponsor_user_name", $sponsor_user_name);
        }

        $user_type = $this->LOG_USER_TYPE;
        if ($this->MODULE_STATUS['footer_demo_status'] == "yes") {
            $admin_user_name = $this->ADMIN_USER_NAME;
            $this->set("admin_user_name", $admin_user_name);
        }
        if ($user_type == "employee") {
            $user_type = 'admin';
        }

        $date = date('Y-m-d H:i:s');
        $lang_id = $this->LANG_ID;
        $letter_arr = $this->configuration_model->getLetterSetting($lang_id);
        $site_configuration = $this->validation_model->getSiteInformation();
        $product_status = $this->MODULE_STATUS['product_status'];
        $referal_status = $this->MODULE_STATUS['referal_status'];

        if ($is_pending_registration) {
            $user_registration_details = $this->register_model->getUserRegistrationDetailsForPreview(0, $user_name);
            $product_id = $user_registration_details['product_id'];
        } else {
            $user_registration_details = $this->register_model->getUserRegistrationDetailsForPreview($user_id);
            $father_id = $this->validation_model->getFatherId($user_id);
            $product_id = $this->validation_model->getProductId($user_id);
            $placement_user_name = $this->validation_model->IdToUserName($father_id);
            $this->set("placement_user_name", $placement_user_name);
        }

        $reg_amount = $this->register_model->getRegisterAmount();
        if ($product_status == "yes") {
            $product_details = $this->register_model->getProduct($product_id);
            $this->set("product_details", $product_details);
            $this->set("product_status", $product_status);
        }

        if ($referal_status == "yes") {
            $sponsor_id = $user_registration_details['sponsor_id'];
            $sponsorname = $this->validation_model->IdToUserName($sponsor_id);
            $this->set("sponsorname", $sponsorname);
            $this->set("referal_status", $referal_status);
        }

        // $user_name_encrypt = $this->encrypt->encode($user_name);
        // $user_name_encrypt_replace = str_replace("/", "_", $user_name_encrypt);
        // $user_name_encrypted = urlencode($user_name_encrypt_replace);

        $pdf_file_to_download = $user_name . ".pdf";

        $register_language_id  = $this->LANG_ID ? $this->LANG_ID : 1;
        $lang_arr = $this->configuration_model->getLanguages();
        $this->set("date", $date);
        $this->set("user_name", $user_name);
        $this->set("user_name_encrypted", $user_name);
        $this->set("user_type", $user_type);
        $this->set("letter_arr", $letter_arr);
        $this->set("site_configuration", $site_configuration);
        $this->set("reg_amount", $reg_amount);
        $this->set("product_status", $product_status);
        $this->set("referal_status", $referal_status);
        $this->set("user_registration_details", $this->security->xss_clean($user_registration_details));
        $this->set("pdf_file_to_download", $pdf_file_to_download);
        $this->set("is_pending_registration", $is_pending_registration);
        $this->set("from_replica", $from_replica);
        $this->set('selected_language_id', $register_language_id);
        $this->set('lang_arr', $lang_arr);
        $this->setView();
    }

    function checkBalanceAvailable()
    {
        $ewallet_user = $this->input->post('user_name', true);
        $balance = $this->input->post('balance', true);
        $user_name_ewallet = $this->validation_model->userNameToID($ewallet_user);
        $user_bal_amount = $this->register_model->getBalanceAmount($user_name_ewallet, $balance);
        echo $user_bal_amount;
    }

    function check_ewallet_balance()
    {
        $status = "no";
        $ewallet_user = $this->input->post('user_name', true);
        $ewallet_pass = $this->input->post('ewallet', true);
        $product_id = $this->input->post('product_id', true);
        $sponsor_user_name = $this->input->post('sponsor_username', true);
        $admin_username = $this->validation_model->getAdminUsername();
        if ($this->LOG_USER_TYPE == 'admin' || $this->LOG_USER_TYPE == 'employee') {
            if ($ewallet_user != $admin_username && $ewallet_user != $sponsor_user_name) {
                $status = "invalid";
                echo $status;
                exit();
            }
        }
        if ($this->LOG_USER_TYPE == 'user') {
            if ($ewallet_user != $sponsor_user_name && $ewallet_user != $this->LOG_USER_NAME) {
                $status = "invalid";
                echo $status;
                exit();
            }
        }
        $user_id = $this->validation_model->userNameToID($ewallet_user);
        if ($user_id) {
            $user_password = $this->register_model->checkEwalletPassword($user_id, $ewallet_pass);

            if ($user_password == 'yes') {
                $user_bal_amount = $this->register_model->getBalanceAmount($user_id);
                if ($user_bal_amount > 0) {
                    $reg_amount = $this->register_model->getRegisterAmount();
                    $product_amount = 0;
                    $product_status = $this->MODULE_STATUS['product_status'];
                    if ($product_status == "yes") {
                        $product_details = $this->register_model->getProduct($product_id);
                        $product_amount = $product_details["product_value"];
                    }
                    $total_amount = $reg_amount + $product_amount;

                    if ($user_bal_amount >= $total_amount) {
                        $status = "yes";
                    }
                }
            } else {
                $status = "invalid";
            }
        } else {
            $status = "invalid";
        }
        echo $status;
        exit();
    }

    function getRegisterAmount()
    {
        $res = $this->register_model->getRegisterAmount();
        echo $res;
    }

    /* form validation rule*
     *    Method is used to validate strings to allow alpha
     *    numeric spaces underscores and dashes ONLY.
     *    @param $str    String    The item to be validated.
     *    @return BOOLEAN   True if passed validation false if otherwise.
     */

    public function _alpha_space($str = '')
    {
        if (!$str) {
            return true;
        }
        $res = (bool)preg_match('/^[A-Z ]*$/i', $str);
        if (!$res) {
            $this->form_validation->set_message('_alpha_space', lang('only_alpha_space'));
        }
        return $res;
    }

    function has_match($post_epin)
    {
        $flag = false;
        $temp_pin_array = $this->session->userdata("inf_temp_pin_array");
        $split_arr = explode("==", $temp_pin_array);

        if (!in_array($post_epin, $split_arr)) {
            $temp_pin_array .= "==$post_epin";
            $this->session->set_userdata("inf_temp_pin_array", $temp_pin_array);
            $flag = true;
        }

        return $flag;
    }

    function _alpha_city_address($str_in = '')
    {
        if (!preg_match("/^([a-zA-Z0-9\s\.\,\-])*$/i", $str_in)) {
            $this->form_validation->set_message('_alpha_city_address', lang('city_field_characters'));
            return false;
        } else {
            return true;
        }
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

    public function validate_age($dob)
    {
        if (!$this->input->post('year') || $this->input->post('month') < 0 || !$this->input->post('day')) {
            return true;
        }
        $age_limit = $this->configuration_model->getAgeLimitSetting();
        if ($age_limit == 0) {
            return true;
        }
        $date1 = new DateTime($dob);
        $date1->add(new DateInterval("P{$age_limit}Y"));
        $date2 = new DateTime();
        if ($date1 <= $date2) {
            return true;
        } else {
            $this->form_validation->set_message('validate_age', sprintf(lang('You_should_be_atleast_n_years_old'), $age_limit));
            return false;
        }
    }

    public function check_required($field_value, $field_name)
    {
        $status = $this->register_model->getRequiredStatus($field_name);
        if ($status == 'yes') {
            if ($field_value == '') {
                $this->form_validation->set_message('check_required', sprintf(lang('the_n_field_is_required'), lang($field_name)));
                return false;
            } else
                return true;
        } else {
            return true;
        }
    }

    public function validate_age_year($dob)
    {
        if (!$this->input->post('year') || $this->input->post('month') < 0 || !$this->input->post('day')) {
            return true;
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

    public function ajax_is_username_available()
    {
        $user_name = $this->input->post('user_name', true);
        if (!$user_name) {
            echo 'no';
            exit();
        }
        $is_username_exists = $this->validation_model->isUsernameExists($user_name);
        if ($is_username_exists) {
            echo 'no';
            exit();
        } else {
            if($user_name != $this->input->post('old_user_name')) {
                $this->register_model->updateUserNameReceipt($user_name, $this->input->post('old_user_name'));
            }
            echo 'yes';
            exit();
        }
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

    //////////code for ADD ON////////////////////////////////////////////////////

    function pay_now()
    {
        require_once 'Paypal.php';
        $paypal = new Paypal;
        $regr = $this->session->userdata("inf_regr");

        $product_status = $regr["product_status"];
        $product_name = $regr["product_name"];
        $product_amount = round($regr["product_amount"]);
        $reg_amount = $regr["reg_amount"];

        $paypal_details = $this->configuration_model->getPaypalConfigDetails();
        //$paypal_currency_code = $paypal_details['currency'];
        $paypal_currency_code = "USD";
        $paypal_currency_left_symbol = "$";
        $paypal_currency_right_symbol = "";

        $default_currency_code = ($this->DEFAULT_CURRENCY_CODE != '') ? $this->DEFAULT_CURRENCY_CODE : "USD";
        $default_currency_left_symbol = ($this->DEFAULT_SYMBOL_LEFT != '') ? $this->DEFAULT_SYMBOL_LEFT : "$";
        $default_currency_right_symbol = ($this->DEFAULT_SYMBOL_RIGHT != '') ? $this->DEFAULT_SYMBOL_RIGHT : "";

        // $product_amount = round($product_amount * $this->DEFAULT_CURRENCY_VALUE, 8);
        // $reg_amount = round($reg_amount * $this->DEFAULT_CURRENCY_VALUE, 8);


        //        $usd_conevrsion_rate = $this->currency_model->getCurrencyConversionRate($default_currency_code, $paypal_currency_code);
        $usd_conevrsion_rate = 1;
        $product_amount = round($product_amount * $usd_conevrsion_rate, 8);
        $reg_amount = round($reg_amount * $usd_conevrsion_rate, 8);
        $total_amount = round($product_amount + $reg_amount, 8);

        $description = "New Membership to " . $this->COMPANY_NAME;
        $description .= "\nMembership Fee : $paypal_currency_left_symbol $reg_amount $paypal_currency_right_symbol";
        if ($product_status == "yes") {
            $description .= ", $product_name : $paypal_currency_left_symbol $product_amount $paypal_currency_right_symbol";
        }
        $base_url = base_url();
        $params = array(
            'amount' => $total_amount,
            'item' => "New Membership",
            'description' => $description,
            'currency' => $paypal_currency_code,
            'return_url' => $base_url . $paypal_details['return_url'],
            'cancel_url' => $base_url . $paypal_details['cancel_url']
        );
        $response = $paypal->initilize($params);
    }

    function payment_success()
    {
        require_once 'Paypal.php';
        $paypal = new Paypal;
        $pending_signup_status = $this->configuration_model->getPendingSignupStatus('paypal');
        $inf_reg = $this->session->userdata("inf_regr");
        $p_id = $inf_reg["product_id"];

        if (!empty($this->session->userdata('from_replica'))) {
            $redirect_url = "replica/replica_register";
        } else {
            $redirect_url = "register/user_register";
        }

        $product_amount = $this->register_model->getProductAmount($p_id);
        $register_amount = $this->register_model->getRegisterAmount();
        $product_amount = round($product_amount * $this->DEFAULT_CURRENCY_VALUE, 8);
        $register_amount = round($register_amount * $this->DEFAULT_CURRENCY_VALUE, 8);

        $paypal_currency_code = "USD";
        $paypal_currency_left_symbol = "$";
        $paypal_currency_right_symbol = "";

        $default_currency_code = ($this->DEFAULT_CURRENCY_CODE != '') ? $this->DEFAULT_CURRENCY_CODE : "USD";
        $default_currency_left_symbol = ($this->DEFAULT_SYMBOL_LEFT != '') ? $this->DEFAULT_SYMBOL_LEFT : "$";
        $default_currency_right_symbol = ($this->DEFAULT_SYMBOL_RIGHT != '') ? $this->DEFAULT_SYMBOL_RIGHT : "";

        //$usd_conevrsion_rate = $this->currency_model->getCurrencyConversionRate($default_currency_code, $paypal_currency_code);
        $usd_conevrsion_rate = 1;
        $product_amount = round($product_amount * $usd_conevrsion_rate, 8);
        $register_amount = round($register_amount * $usd_conevrsion_rate, 8);

        $total_amount = round($product_amount + $register_amount);

        $paypal_details = $this->configuration_model->getPaypalConfigDetails();
        $base_url = base_url();
        $params = array(
            'amount' => $total_amount,
            'currency' => $paypal_details['currency'],
            'return_url' => $base_url . $paypal_details['return_url'],
            'cancel_url' => $base_url . $paypal_details['cancel_url']
        );
        $response = $paypal->callback($params);
        if ($response->success()) {
            $paypal_output = $this->input->get();
            $regr = $this->session->userdata('inf_regr');
            $referral_id = $regr["sponsor_id"];
            $payment_details = array(
                'payment_method' => 'paypal',
                'token_id' => $paypal_output['token'],
                'currency' => $paypal_details['currency'],
                'amount' => $total_amount,
                'acceptance' => '',
                'payer_id' => $paypal_output['PayerID'],
                'user_id' => $referral_id,
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
            $module_status = $this->MODULE_STATUS;
            $regr['by_using'] = 'paypal';

            $email_verification = $this->configuration_model->getEmailVerificationStatus();
            $this->register_model->begin();
            $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);

            $msg = '';
            if ($status['status']) {
                $user_name = $status['user_name'];
                $user_id = $status['user_id'];
                $transaction_password = $pending_signup_status ? '' : $status['transaction_password'];

                $payment_method = "paypal";
                $product_status = $this->MODULE_STATUS['product_status'];
                $email_verification = $this->configuration_model->getEmailVerificationStatus();
                if ($product_status == "yes") {
                    $insert_into_sales = $this->register_model->insertIntoSalesOrder($user_id, $regr['product_id'], $payment_method, $pending_signup_status, $email_verification);
                }

                $this->register_model->commit();

                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'user_register', 'New User Registered', $pending_signup_status);
                }
                //
                $this->session->unset_userdata(['inf_regr', 'inf_reg_post_array', 'from_replica']);

                $id_encode = $this->encryption->encrypt($user_name);
                $id_encode = str_replace("/", "_", $id_encode);
                $user_name_encrypt = urlencode($id_encode);
                if ($pending_signup_status) {
                    $msg = "<span><b>" . lang('registration_completed_successfully_pending') . "!</b> " . lang("User_Name") . ": {$user_name}";
                } else {
                    $msg = "<span><b>" . lang('registration_completed_successfully') . "!</b> " . lang("User_Name") . ": {$user_name} " . lang("transaction_password") . ": {$transaction_password}</span>";
                }

                $this->redirect($msg, "register/preview/{$user_name}", true);
            } else {
                $this->register_model->rollback();
                $msg = lang('registration_failed');
                $this->redirect($msg, $redirect_url, false);
            }
        } else {
            $msg = 'Payment Failed';
            $this->redirect($msg, $redirect_url, false);
        }
    }

    function check_epin_validity()
    {
        //$input = file_get_contents('php://input');
        //$jsonData = json_decode($input, true);
        $product_id = $this->input->post('product_id');
        $pin_details = $this->input->post('pin_array');
        $product_status = $this->MODULE_STATUS["product_status"];
        $sponsor_name = $this->input->post('sponsor_name');
        $sponsor_id = $this->validation_model->userNameToID($sponsor_name);
        $flag = false;
        if ($sponsor_name != '') {
            if ($this->register_model->isUserAvailable($sponsor_name)) {
                $flag = true;
            }
        }
        if ($flag) {
            $pin_array = $this->register_model->checkAllEpins($pin_details, $product_id, $product_status, $sponsor_id);
            $value = json_encode($pin_array);
            echo $value;
            exit();
        }
    }

    function authorizeNetPayment()
    {
        if (!empty($this->session->userdata('replica_language'))) {
            $replica_lang = $this->session->userdata('replica_language');
            $this->lang->load($this->CURRENT_CTRL, $replica_lang['lang_name_in_english']);
        }

        $this->set("action_page", $this->CURRENT_URL);
        $title = lang('authorize_authentication');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('authorize_authentication');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('authorize_authentication');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $inf_regr = $this->session->userdata("inf_regr");
        $p_id = $inf_regr["product_id"];
        $product_amount = $this->register_model->getProductAmount($p_id);
        $register_amount = $this->register_model->getRegisterAmount();
        $total_amount = $product_amount + $register_amount;

        $this->load->model('authorizeNetPayment_model');
        $merchant_details = $this->authorizeNetPayment_model->getAuthorizeDetails();
        $api_login_id = $merchant_details['merchant_id'];
        $transaction_key = $merchant_details['transaction_key'];
        $fp_timestamp = time();
        $fp_sequence = "123" . time(); // Enter an invoice or other unique number.
        $fingerprint = $this->authorizeNetPayment_model->authorizePay($api_login_id, $transaction_key, $total_amount, $fp_sequence, $fp_timestamp);

        $this->set('user_type', $this->LOG_USER_TYPE);
        $this->set('api_login_id', $api_login_id);
        $this->set('transaction_key', $transaction_key);
        $this->set('amount', $total_amount);
        $this->set('fp_timestamp', $fp_timestamp);
        $this->set('fingerprint', $fingerprint);
        $this->set('fp_sequence', $fp_sequence);

        $this->setView();
    }
    
    function payment_done()
    {

        $pending_signup_status = $this->configuration_model->getPendingSignupStatus('authorize.net');

        $response = $this->input->post(null, true);
        $regr = $this->session->userdata('inf_regr');

        if (!empty($this->session->userdata('from_replica'))) {
            $redirect_url = "register/replica_register";
        } else {
            $redirect_url = "register/user_register";
        }

        $product_status = $this->MODULE_STATUS['product_status'];
        $module_status = $this->MODULE_STATUS;
        $this->load->model('authorizeNetPayment_model');
        $insert_id = $this->authorizeNetPayment_model->insertAuthorizeNetPayment($response);

        $this->register_model->begin();
        $email_verification = $this->configuration_model->getEmailVerificationStatus();
        $status = $this->register_model->ConfirmRegister($regr, $module_status, $pending_signup_status, $email_verification);

        if ($status['status']) {

            $user_name = $status['user_name'];
            $user_id = $status['user_id'];
            $transaction_password = $pending_signup_status ? '' : $status['transaction_password'];
            $payment_method = 'authorize.net';

            $this->authorizeNetPayment_model->updateAuthorizeNetPayment($insert_id, $user_id, $pending_signup_status);

            $email_verification = $this->configuration_model->getEmailVerificationStatus();
            if ($product_status == "yes") {
                $insert_into_sales = $this->register_model->insertIntoSalesOrder($user_id, $regr['product_id'], $payment_method, $pending_signup_status, $email_verification);
            }

            $this->register_model->commit();
            // Employee Activity History
            if ($this->LOG_USER_TYPE == 'employee') {
                $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'user_register', 'New User Registered', $pending_signup_status);
            }
            //

            $this->session->unset_userdata(['inf_regr', 'inf_reg_post_array', 'from_replica']);

            $id_encode = $this->encryption->encrypt($user_name);
            $id_encode = str_replace("/", "_", $id_encode);
            $user_name_encrypt = urlencode($id_encode);
            if ($pending_signup_status) {
                $msg = "<span><b>" . lang('registration_completed_successfully_pending') . "!</b> " . lang("User_Name") . ": {$user_name}";
            } else {
                $msg = "<span><b>" . lang('registration_completed_successfully') . "!</b> " . lang("User_Name") . ": {$user_name} " . lang("transaction_password") . ": {$transaction_password}</span>";
            }

            $this->redirect($msg, "register/preview/{$user_name}", true);
        } else {
            $this->register_model->rollback();
            $msg = lang('registration_failed');
            $this->redirect($msg, $redirect_url, false);
        }
    }

    /* Blockchain Payment Method Starts */

    function blockchain()
    {

        require_once 'Blockchain.php';
        $blockchain = new Blockchain;

        if (!empty($this->session->userdata('replica_language'))) {
            $replica_lang = $this->session->userdata('replica_language');
            $this->lang->load($this->CURRENT_CTRL, $replica_lang['lang_name_in_english']);
        }

        $title = lang('blockchain');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('blockchain');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('blockchain');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        if (!empty($this->session->userdata('from_replica'))) {
            $redirect_url = "register/replica_register";
        } else {
            $redirect_url = "register/user_register";
        }

        $base_url = base_url();
        $date = date("Y-m-d H:i:s");
        $invoice_id = time();
        $secret = $blockchain->getToken();
        if (empty($this->session->userdata("inf_regr"))) {
            $this->redirect("", $redirect_url, false);
        }
        $inf_reg = $this->session->userdata("inf_regr");
        $p_id = $inf_reg["product_id"];
        $product_amount = $this->register_model->getProductAmount($p_id);
        $register_amount = $this->register_model->getRegisterAmount();
        $total_amount = $product_amount + $register_amount;

        $currency = "USD";
        $blockchain_root = "https://blockchain.info/";
        // $price_in_btc = $total_amount;
        $price_in_btc = file_get_contents($blockchain_root . "tobtc?currency=$currency&value=" . $total_amount);
        // $blockchain_info['secret'];

        $new_address = false;
        if ($this->register_model->getUnpaidAddressCount() <= 19) {
            if ($address = ($this->register_model->getUnpaidAddress()) ?: false) {
            } else {
                if ($this->LOG_USER_TYPE == 'admin') {
                    $this->redirect(lang('you_have_reached_maximum_unpaid_address'), $redirect_url, false);
                } else {
                    $this->redirect(lang('payment_not_available_now'), $redirect_url, false);
                }
            }
        } else {
            $address = $blockchain->generateAddress();
            $new_address = true;
        }
        $qr_code = $blockchain->generateQr($address);
        if ($address) {
            if ($new_address) {
                $this->register_model->keepBitcoinAddress($address);
            } else {
                $this->register_model->updateAddressDate($address);
            }
            $regr = $this->session->userdata("inf_regr");
            $this->register_model->insertPaymentDetails($invoice_id, $address, $secret, $total_amount, $price_in_btc, $date, $regr, 'register');
        } else {
            $this->redirect("Something wrong", $redirect_url, false);
        }

        $this->set('address', $address);
        $this->set('qr_code', $qr_code);
        $this->set('amount', $total_amount);
        $this->set('amount_in_btc', $price_in_btc);
        $this->set('invoice_id', $invoice_id);
        $this->session->set_userdata('block_address', $address);
        $this->session->set_userdata('price_in_btc', $price_in_btc);
        $this->session->set_userdata('invoice_id', $invoice_id);


        $this->setView();
    }

    public function blockchain_payment_done()
    {
        require_once 'Blockchain.php';
        $blockchain = new Blockchain;

        if (!empty($this->session->userdata('from_replica'))) {
            $redirect_url = "register/replica_register";
        } else {
            $redirect_url = "register/user_register";
        }

        if ($this->session->userdata('block_address') && $this->session->userdata('price_in_btc')) {
            $block_address = $this->session->userdata('block_address');
            $paid_amount = $this->session->userdata('price_in_btc');
            $inf_reg = $this->session->userdata("inf_regr");
            $p_id = $inf_reg["product_id"];
            $product_amount = $this->register_model->getProductAmount($p_id);
            $register_amount = $this->register_model->getRegisterAmount();
            $total_amount = $product_amount + $register_amount;

            $res_arr = $blockchain->getResponse($block_address);
            $response_amount = 0;
            foreach ($res_arr['txs'] as $key => $value) {
                $count = count($value['out']);
                for ($i = 0; $i < $count; $i++) {
                    if ($value['out'][$i]['addr'] == $block_address) {
                        $amount = $value['out'][$i]['value'];
                        $response_amount = $amount / 100000000;
                    }
                }
            }
            $invoice_id = $this->session->userdata('invoice_id');
            $this->register_model->keepRowAddressReponse($block_address, $invoice_id, $res_arr, 'register');
            $this->register_model->updateBitcoinAddress($block_address, 'yes');

            if ($response_amount > 0.00000001 && (round($response_amount, 8) >= round($paid_amount, 8))) {

                $regr = $this->session->userdata('inf_regr');
                $referral_id = $regr["sponsor_id"];
                $module_status = $this->MODULE_STATUS;
                $pending_signup_status = $this->configuration_model->getPendingSignupStatus("blockchain");
                $regr['by_using'] = 'blockchain';

                $this->register_model->begin();
                $email_verification = $this->configuration_model->getEmailVerificationStatus();
                $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);

                $msg = '';
                if ($status['status']) {
                    $this->register_model->commit();
                    if ($pending_signup_status) {
                        $user = $status['user_name'];
                        $user_id = $status['user_id'];
                        $tran_code = "";
                        $pass = "";
                    } else {
                        $user = $status['user'];
                        $pass = $status['pwd'];
                        $tran_code = $status['tran'];
                    }

                    $product_status = $this->MODULE_STATUS['product_status'];
                    $payment_method = "blockchain";
                    //                    if ($product_status == "yes") {
                    //                        $user_id = $this->validation_model->userNameToID($user);
                    //                        $insert_into_sales = $this->register_model->insertIntoSalesOrder($user_id, $regr['product_id'], $payment_method,$pending_signup_status);
                    //                    }

                    $id_encode = $this->encryption->encrypt($user);
                    $id_encode = str_replace("/", "_", $id_encode);
                    $user1 = urlencode($id_encode);

                    $this->session->unset_userdata(['inf_regr', 'inf_reg_post_array', 'from_replica']);

                    $this->session->unset_userdata('block_address');
                    $this->session->unset_userdata('price_in_btc');
                    $this->session->unset_userdata('invoice_id');
                    $msg = lang('registration_completed_successfully');
                    $this->redirect("<span><b>$msg!</b>  Username : $user &nbsp;&nbsp; Password : $pass &nbsp; Transaction Password : $tran_code</span>", "register/preview/" . $user, true);
                    exit();
                } else {
                    $this->register_model->rollback();
                    $msg = lang('registration_failed');
                    $this->redirect($msg, $redirect_url, false);
                }
                //unset all session
            } else {
                $msg = "Invalid Operation !! " . lang('registration_failed');
                $this->redirect($msg, $redirect_url, false);
            }
        }
    }
    /* Blockchain Payment Method End */

    /* Bitgo Payment Method Starts */

    public function bitgo_gateway()
    {
        require_once 'Bitgo.php';
        $bitgo = new Bitgo;
        $error = '';
        $this->set("action_page", $this->CURRENT_URL);

        if (!empty($this->session->userdata('replica_language'))) {
            $replica_lang = $this->session->userdata('replica_language');
            $this->lang->load($this->CURRENT_CTRL, $replica_lang['lang_name_in_english']);
        }

        $title = lang('bitgo_gateway');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('bitgo_gateway');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('bitgo_gateway');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        if (!empty($this->session->userdata('from_replica'))) {
            $redirect_url = "register/replica_register";
        } else {
            $redirect_url = "register/user_register";
        }

        $regr = $this->session->userdata('inf_regr');
        $p_id = $regr['product_id'];
        $product_amount = $regr['product_amount'];
        $register_amount = $this->register_model->getRegisterAmount();

        $total_amount = $product_amount + $register_amount;
        $total_amount = $product_amount;
        if (!empty($this->session->userdata('bitcoin_session')) && $regr['is_new'] == "no") {
            $bitcoin_sess = $this->session->userdata('bitcoin_session');
            $pay_address = $bitcoin_sess['bitcoin_address'];
            $sendAmount = $bitcoin_sess['send_amount'];
        } else {
            try {
                $address = $bitgo->bitgo_gateway();
            } catch (Exception $e) {
                $msg = $e->getMessage();
                $this->redirect($msg, $redirect_url, false);
            }

            $btc_amount = $this->currency_model->currencyToBtc('USD', $total_amountcard_cvn);
            $sendAmount = $btc_amount['btc_amount'];
            $regr = $this->session->userdata('inf_regr');
            $p_id = $regr['product_id'];
            $user_id = $this->LOG_USER_ID;
            $pay_address = $address->address;
            $wallet_id = $address->wallet;
            $bitgo_hid = $this->register_model->insertIntoBitGoPaymentHistory($user_id, serialize($regr), $p_id, $btc_amount['btc_amount'], $pay_address, serialize($address), $wallet_id);

            $bitcoin_session = array(
                'bitcoin_address' => $pay_address,
                'send_amount' => $btc_amount['btc_amount'],
                'bitgo_hid' => $bitgo_hid,
                'wallet_id' => $wallet_id
            );
            $this->session->set_userdata('bitcoin_session', $bitcoin_session);
            $_SESSION['inf_regr']['is_new'] = "no";
        }

        $btc_amount = round($sendAmount, 8);
        $qr_code = $bitgo->generateBitcoinQrCode($pay_address, $btc_amount);

        $this->set('pay_address', $pay_address);
        $this->set('amount', $btc_amount);
        $this->set('qr_code', $qr_code);
        $this->set('error', $error);
        $this->setView();
    }

    public function ajax_bitgo_payment_verify()
    {
        require_once 'Bitgo.php';
        $bitgo = new Bitgo;
        if (!empty($this->session->userdata('bitcoin_session'))) {

            $rs_arr = array();
            $bitcoin_address_array = $this->session->userdata('bitcoin_session');
            $bitcoin_address = $bitcoin_address_array['bitcoin_address'];
            $btc_amount = $bitcoin_address_array['send_amount'];
            $bitgo_hid = $bitcoin_address_array['bitgo_hid'];
            $wallet_id = $bitcoin_address_array['wallet_id'];
            $bitcoin_status = $bitgo->checkBitcoinPaymentStatus($bitcoin_address, $btc_amount, $bitgo_hid, $wallet_id);

            $email_verification = $this->configuration_model->getEmailVerificationStatus();
            if ($bitcoin_status['status']) {
                if ($this->session->userdata('inf_regr')) {
                    $regr = $this->session->userdata('inf_regr');
                    $product_status = $this->MODULE_STATUS['product_status'];
                    $module_status = $this->MODULE_STATUS;
                    $pending_signup_status = $this->configuration_model->getPendingSignupStatus('bitgo');

                    $this->register_model->begin();
                    $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);

                    if ($status['status']) {
                        $this->register_model->commit();

                        if ($pending_signup_status) {
                            $user = $status['user_name'];
                            $user_id = $status['user_id'];
                            $tran_code = "";
                            $pass = "";
                        } else {
                            $user = $status['user'];
                            $pass = $status['pwd'];
                            $tran_code = $status['tran'];
                            $user_id = $this->validation_model->userNameToID($user);
                        }
                        $payment_method = 'bitgo';
                        if ($product_status == "yes") {
                            $this->register_model->insertIntoSalesOrder($user_id, $regr['product_id'], $payment_method, $pending_signup_status, $email_verification);
                        }
                        $id_encode = $this->encryption->encrypt($user);
                        $id_encode = str_replace("/", "_", $id_encode);
                        $user1 = urlencode($id_encode);

                        $bitgo_session = array(
                            'bitgo_resp_user' => $user,
                            'bitgo_resp_user1' => $user1,
                            'bitgo_resp_tran_code' => $tran_code,
                            'bitgo_resp_pass' => $pass,
                            'bitgo_pending_signup_status' => $pending_signup_status
                        );
                        $this->session->set_userdata('bitgo_session', $bitgo_session);
                        $rs_arr['status'] = $status['status'];
                    } else {
                        $this->register_model->rollback();
                        $rs_arr['status'] = $status['status'];
                    }
                    echo json_encode($rs_arr);
                }
            } else {
                $rs_arr['status'] = "Failed";
                echo json_encode($bitcoin_status);
            }
        } else {
            $rs_arr['status'] = "Failed";
            echo json_encode($rs_arr);
        }
    }

    function btc_confirm()
    {
        if (!empty($this->session->userdata('from_replica'))) {
            $redirect_url = "register/replica_register";
        } else {
            $redirect_url = "register/user_register";
        }

        if (!empty($this->session->userdata('bitgo_session'))) {
            $bitgo_address_array = $this->session->userdata('bitgo_session');
            $user = $bitgo_address_array['bitgo_resp_user'];
            $user1 = $bitgo_address_array['bitgo_resp_user1'];

            if ($bitgo_address_array['bitgo_pending_signup_status']) {
                $msg1 = "<span><b>" . lang('registration_completed_successfully_pending') . "!</b> " . lang("User_Name") . ": {$user}";
            } else {
                $tran_code = $bitgo_address_array['bitgo_resp_tran_code'];
                $pass = $bitgo_address_array['bitgo_resp_pass'];
                $msg = lang('registration_completed_successfully');
                $msg1 = "<span><b>$msg!</b>  " . lang("User_Name") . " : $user &nbsp;&nbsp; " . lang("password") . " : $pass &nbsp; " . lang('transaction_password') . " : $tran_code</span>";
            }
            $this->session->unset_userdata(['inf_regr', 'inf_reg_post_array', 'from_replica']);
            $this->session->unset_userdata('bitcoin_session');
            $this->session->unset_userdata('bitgo_session');

            $this->redirect($msg1, "register/preview/" . $user, true);
        } else {
            $msg = lang('registration_failed');
            $this->redirect($msg, $redirect_url, false);
        }
    }

    function upload_payment_reciept()
    {
        if ($this->input->is_ajax_request()) {
            $user_name = $this->input->post('user_name', true);
            $this->load->library('upload');
            $base_url = base_url();
            $response = array();
            $response['error'] = false;
            if (!isset($_FILES['file'])) {
                $response['error'] = true;
                $response['message'] = lang('select_payment_reciept');
                echo json_encode($response);
                exit();
            }
            if (!empty($_FILES['file'])) {
                $upload_config = $this->validation_model->getUploadConfig();
                $upload_count = $this->validation_model->getUploadCount($this->LOG_USER_ID);
                if ($upload_count >= $upload_config) {
                    $msg = lang('you_have_reached_max_upload_limit');
                    $response['error'] = true;
                    $response['message'] = $msg;
                    echo json_encode($response);
                    exit();
                }
            }
            if ($_FILES['file']['error'] != 4) {
                $this->session->set_userdata('file', 'file');
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
                } else {
                    $result = '';

                    $data = array('upload_data' => $this->upload->data());
                    $doc_file_name = $data['upload_data']['file_name'];
                    $result = $this->register_model->addReciept($user_name, $doc_file_name);
                    $this->validation_model->updateUploadCount($this->LOG_USER_ID);
                    if ($result) {
                        $data_array['file_name'] = $doc_file_name;
                        $data = serialize($data_array);

                        // Employee Activity History
                        if ($this->LOG_USER_TYPE == 'employee') {
                            $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'upload_material', 'Payment Receipt Uploaded');
                        }
                        $msg = lang('payment_receipt_ploaded_successfully');
                        $response['success'] = true;
                        $response['message'] = $msg;
                        $response['file_name'] = $doc_file_name;
                    } else {
                        $msg = lang('payment_receipt_upload_error');
                        $response['error'] = true;
                        $response['message'] = $msg;
                    }
                }
                echo json_encode($response);
                exit();
            }
        }
    }

    /* Bank Transfer Payment Method Ends */

    public function get_available_leg()
    {
        $this->load->model('tree_model');
        $user_name = $this->input->get('user_name');
        $user_id = $this->validation_model->userNameToID($user_name);
        $response = $this->tree_model->getAllowedBinaryLeg($user_id, $this->LOG_USER_TYPE, $this->LOG_USER_ID);
        echo $response;
        exit();
    }
    public function getTicketCount()
    {
        $new_ticket = 0;
        $this->load->model('ticket_system_model');
        $new_ticket = $this->ticket_system_model->getNewTickets();
        echo $new_ticket;
        exit();
    }

    public function reset_file_type()
    {
        $data = false;
        if (!empty($this->session->userdata('file'))) {
            $this->session->unset_userdata('file');
            $data = true;
        }
        echo $data;
        exit();
    }

    function validate_sponsorfullname()
    {
        $sponsorname = '';
        $username = $this->input->post('username');
        if ($username != '') {
            $sponsorname = $this->register_model->isSponsornameExist($username);
            echo $sponsorname;
            exit();
        } else {
            $flag = false;
            $user_name = ($this->input->post('sponsor_user_name', true));
            $sponsor_fullname = ($this->input->post('sponsor_full_name', true));
            $sponsorname = $this->register_model->isSponsornameExist($user_name);
            if ($sponsorname == $sponsor_fullname) {
                $flag = true;
            }
            return $flag;
        }
    }

    public function payeer()
    {
        if (!empty($this->session->userdata('replica_language'))) {
            $replica_lang = $this->session->userdata('replica_language');
            $this->lang->load($this->CURRENT_CTRL, $replica_lang['lang_name_in_english']);
        }

        $title = lang('payeer');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('payeer');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('payeer');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        if (!empty($this->session->userdata('from_replica'))) {
            $redirect_url = "register/replica_register";
        } else {
            $redirect_url = "register/user_register";
        }

        if ($this->session->userdata('payeer_data')) {
            $data = $this->session->userdata('payeer_data');
            $setting = $this->member_model->getPayeerSettings();
            $m_shop = $setting['merchant_id'];   //   merchant   ID
            $m_curr = $data['currency'];   //   invoice   currency
            $m_orderid = ''; //   invoice   number   in   the   merchant's   invoicing   system
            $m_amount = number_format($data['product_amount'], 2, '.', '');   //   invoice   amount   with   two   decimal   places following   a   period
            $m_desc = '';   //   invoice   description   encoded   using   a   base64 algorithm
            $m_key = $setting['merchant_key']; //   Forming   an   array   for   signature   generation
            $arHash = array($m_shop, $m_orderid, $m_amount, $m_curr, $m_desc); //   Forming   an   array   for   additional   parameters
            // $arParams   =   array('success_url'   =>   'https://dev.bizmo.world/backoffice/user/member/payeer_success',
            //                         'fail_url'   =>  'https://dev.bizmo.world/backoffice/user/member/payeer_failure',
            //                         'status_url'   =>   'https://dev.bizmo.world/backoffice/register/payeer_status',
            //                         //   Forming   an   array   for   additional   fields
            //                         'reference'   =>   array('var1'   =>   $data['product_id'],
            //                     ),
            //                     //'submerchant'   =>   'mail.com',
            //                 );
            // //   Forming   a   key   for   encryption
            // $key   =   md5($setting['encryption_key'].$m_orderid);//   Encrypting   additional   parameters
            // $m_params = urlencode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$key, json_encode($arParams), MCRYPT_MODE_ECB)));
            // //   Encrypting   additional   parameters   using   AES-256-CBC   (for   >=   PHP   7)
            // //
            // $m_params   =   urlencode(base64_encode(openssl_encrypt(json_encode($arParams),'AES-256-CBC',$key,OPENSSL_RAW_DATA)));
            // //   Adding   parameters   to   the   signature-formation   array
            // $arHash[]   =   $m_params;
            //  //   Adding   the   secret   key   to   the   signature-formation   array
            // $arHash[]   =   $m_key;
            // //   Forming   a   signature
            // $sign = strtoupper(hash('sha256', implode(':', $arHash)));
            if (isset($m_params)) {
                $arHash[] = $m_params;
            }
            // Adding the secret key
            $arHash[] = $m_key;
            // Forming a signature
            $sign = strtoupper(hash('sha256', implode(":", $arHash)));
            $new_package_name = $this->register_model->getProductName($data['product_id']);
            $comment = "Payment for the Product $new_package_name";
            $this->set('m_shop', $m_shop);
            $this->set('m_orderid', $m_orderid);
            $this->set('m_amount', $m_amount);
            $this->set('m_curr', $m_curr);
            $this->set('m_desc', $m_desc);
            $this->set('sign', $sign);
            $this->set('type', $comment);
            $this->setView('register/payeer');
        } else {
            $msg = "registration_failed";
            $this->session->unset_userdata('from_replica');
            $this->redirect($msg, $redirect_url, false);
        }
    }

    public function payeer_success()
    {
        $pending_signup_status = $this->configuration_model->getPendingSignupStatus('payeer');
        $inf_reg = $this->session->userdata("inf_regr");
        $p_id = $inf_reg["product_id"];

        if (!empty($this->session->userdata('from_replica'))) {
            $redirect_url = "register/replica_register";
        } else {
            $redirect_url = "register/user_register";
        }

        $product_amount = $this->register_model->getProductAmount($p_id);
        $register_amount = $this->register_model->getRegisterAmount();
        $product_amount = round($product_amount * $this->DEFAULT_CURRENCY_VALUE, 8);
        $register_amount = round($register_amount * $this->DEFAULT_CURRENCY_VALUE, 8);

        $payeer_currency_code = "EUR";
        $payeer_currency_left_symbol = "€";
        $payeer_currency_right_symbol = "";

        $default_currency_code = ($this->DEFAULT_CURRENCY_CODE != '') ? $this->DEFAULT_CURRENCY_CODE : "EUR";
        $default_currency_left_symbol = ($this->DEFAULT_SYMBOL_LEFT != '') ? $this->DEFAULT_SYMBOL_LEFT : "€";
        $default_currency_right_symbol = ($this->DEFAULT_SYMBOL_RIGHT != '') ? $this->DEFAULT_SYMBOL_RIGHT : "";

        $total_amount = round($product_amount + $register_amount);

        $payeer_details = $this->configuration_model->getPayeerConfigurationDetails();
        $base_url = base_url();
        $regr = $this->session->userdata('inf_regr');
        $referral_id = $regr["sponsor_id"];
        $payment_details = array(
            'user_id' => $this->LOG_USER_ID,
            'purpose' => 'Registration',
            'amount' => $product_amount,
            'product_id' => $p_id,
            'status' => 'success',
            'currency' => $default_currency_code,
            'invoice_number' => '',
            'date' => date('Y-m-d H:i:s')
        );
        $this->register_model->insertIntoPayeerOrderHistory($payment_details);
        $module_status = $this->MODULE_STATUS;
        $regr['by_using'] = 'payeer';
        $this->register_model->begin();
        $email_verification = $this->configuration_model->getEmailVerificationStatus();
        $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);
        $msg = '';
        if ($status['status']) {
            $user_name = $status['user_name'];
            $user_id = $status['user_id'];
            $transaction_password = $pending_signup_status ? '' : $status['transaction_password'];

            $payment_method = "payeer";
            $email_verification = $this->configuration_model->getEmailVerificationStatus();
            $product_status = $this->MODULE_STATUS['product_status'];
            if ($product_status == "yes") {
                $insert_into_sales = $this->register_model->insertIntoSalesOrder($user_id, $regr['product_id'], $payment_method, $pending_signup_status, $email_verification);
            }
            $this->register_model->commit();
            // Employee Activity History
            if ($this->LOG_USER_TYPE == 'employee') {
                $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'user_register', 'New User Registered', $pending_signup_status);
            }
            //
            $this->session->unset_userdata(['inf_regr', 'inf_reg_post_array', 'from_replica']);

            $id_encode = $this->encryption->encrypt($user_name);
            $id_encode = str_replace("/", "_", $id_encode);
            $user_name_encrypt = urlencode($id_encode);
            if ($pending_signup_status) {
                $msg = "<span><b>" . lang('registration_completed_successfully_pending') . "!</b> " . lang("User_Name") . ": {$user_name}";
            } else {
                $msg = "<span><b>" . lang('registration_completed_successfully') . "!</b> " . lang("User_Name") . ": {$user_name} " . lang("transaction_password") . ": {$transaction_password}</span>";
            }
            $this->redirect($msg, "register/preview/{$user_name}", true);
        } else {
            $this->register_model->rollback();
            $msg = lang('registration_failed');
            $this->redirect($msg, $redirect_url, false);
        }
    }

    public function payeer_failure()
    {
        $this->register_model->rollback();
        $msg = lang('registration_failed');

        if (!empty($this->session->userdata('from_replica'))) {
            $redirect_url = "register/replica_register";
        } else {
            $redirect_url = "register/user_register";
        }

        $this->redirect($msg, $redirect_url, false);
    }

    function sofort_payment()
    {
        if (!empty($this->session->userdata('replica_language'))) {
            $replica_lang = $this->session->userdata('replica_language');
            $this->lang->load($this->CURRENT_CTRL, $replica_lang['lang_name_in_english']);
        }

        $this->set("action_page", $this->CURRENT_URL);
        $title = lang('sofort');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('sofort');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('sofort');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        if ($this->session->userdata('inf_regr')) {

            $regr = $this->session->userdata('inf_regr');
            $p_id = $regr['product_id'];
            $product_amount = $regr['product_amount'];
            $register_amount = $this->register_model->getRegisterAmount();

            //          $eur_conevrsion_rate = $this->currency_model->getCurrencyConversionRate('USD', "EUR");
            $eur_conevrsion_rate = 0.87;
            $product_amount = round($product_amount * $eur_conevrsion_rate, 8);
            $register_amount = round($register_amount * $eur_conevrsion_rate, 8);

            $total_amount = $product_amount + $register_amount;

            $currency = 'EUR';
            $package_name = $this->register_model->getProductName($p_id);

            $comment = "Payment for the Product $package_name";
            $this->set('comment', $comment);
            $this->set('amount', $total_amount);
            $this->set('currency', $currency);
            $this->setView();
        }
    }

    public function sofort_response()
    {

        require_once 'SofortPay.php';
        $sofort = new SofortPay;

        if (!empty($this->session->userdata('from_replica'))) {
            $redirect_url = "register/replica_register";
        } else {
            $redirect_url = "register/user_register";
        }

        $this->load->model("payment_model");
        $input = array();
        $input = $this->input->post(null, true);

        $result = $sofort->sofortResponse($input);
        if (!$result['status']) {
            $result = $this->payment_model->insertInToSofortProcessDetails($this->session->userdata('inf_regr'), $result['msg'], $this->LOG_USER_ID);
            $msg = lang('registration_failed');
            $this->redirect($msg, $redirect_url, false);
        }
    }
    public function sofort_success()
    {

        $this->load->model("payment_model");
        if (!empty($this->session->userdata('from_replica'))) {
            $redirect_url = "register/replica_register";
        } else {
            $redirect_url = "register/user_register";
        }

        if ($this->session->userdata('inf_regr')) {
            $transaction_id = $this->session->userdata('transactionid');
            $pending_signup_status = $this->configuration_model->getPendingSignupStatus('sofort');
            $regr = $this->session->userdata('inf_regr');
            $module_status = $this->MODULE_STATUS;
            $product_status = $module_status['product_status'];
            $this->register_model->begin();
            $email_verification = $this->configuration_model->getEmailVerificationStatus();
            $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);
            if ($status['status']) {
                $this->register_model->commit();
                $user_name = $status['user_name'];
                $user_id = $status['user_id'];
                $transaction_password = $pending_signup_status ? '' : $status['transaction_password'];
                $payment_method = 'sofort';

                $p_id = $regr["product_id"];
                $product_amount = $this->register_model->getProductAmount($p_id);
                $register_amount = $this->register_model->getRegisterAmount();
                $total_amount = $product_amount + $register_amount;

                $payment_details = [
                    'user_id' => $user_id,
                    'type' => 'Registration',
                    's tatus' => 'success',
                    'total_amount' => $total_amount,
                    'transaction_id' => $transaction_id
                ];

                $result = $this->payment_model->insertIntoSofortPaymentHistory($payment_details);

                $email_verification = $this->configuration_model->getEmailVerificationStatus();
                if ($product_status == "yes") {
                    $insert_into_sales = $this->register_model->insertIntoSalesOrder($user_id, $regr['product_id'], $payment_method, $pending_signup_status, $email_verification);
                }

                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'user_register', 'New User Registered', $pending_signup_status);
                }
                //
                $this->session->unset_userdata(['inf_regr', 'inf_reg_post_array', 'from_replica']);
                $this->session->unset_userdata('transactionid');

                $id_encode = $this->encryption->encrypt($user_name);
                $id_encode = str_replace("/", "_", $id_encode);
                $user_name_encrypt = urlencode($id_encode);
                if ($pending_signup_status) {
                    $msg = "<span><b>" . lang('registration_completed_successfully_pending') . "!</b> " . lang("User_Name") . ": {$user_name}";
                } else {
                    $msg = "<span><b>" . lang('registration_completed_successfully') . "!</b> " . lang("User_Name") . ": {$user_name} " . lang("transaction_password") . ": {$transaction_password}</span>";
                }

                $this->redirect($msg, "register/preview/{$user_name}", true);
            } else {
                $this->register_model->rollback();
                $result = $this->payment_model->insertInToSofortProcessDetails($this->session->userdata('inf_regr'), "confirmRegister failed", $this->LOG_USER_ID);
                $msg = lang('registration_failed');
                $this->redirect($msg, $redirect_url, false);
            }
        } else {
            $error_data['user_name_entry'] = '';
            $result = $this->payment_model->insertInToSofortProcessDetails($error_data, "No Session Data", $this->LOG_USER_ID);
            $msg = lang('registration_failed');
            $this->redirect($msg, $redirect_url, false);
        }
    }

    public function squareup_gateway()
    {

        $title = lang('squareup_payment');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('squareup_payment');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('squareup_payment');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        if (!empty($this->session->userdata('from_replica'))) {
            $redirect_url = "register/replica_register";
        } else {
            $redirect_url = "register/user_register";
        }

        if (empty($this->session->userdata('inf_regr'))) {
            $msg = lang('you_cant_go_to_payment_page_directly_without_filling_all_registration_fields');
            $this->redirect($msg, $redirect_url, false);
        }

        $merchant_details = $this->configuration_model->getSquareUpConfigDetails();
        $application_id = $merchant_details['application_id'];
        $location_id = $merchant_details['location_id'];
        $inf_reg = $this->session->userdata("inf_regr");
        $product_id = $inf_reg["product_id"];
        $product_amount = $this->register_model->getProductAmount($product_id);
        $register_amount = $this->register_model->getRegisterAmount();
        $total_amount = round((floatval($product_amount + $register_amount) / $this->DEFAULT_CURRENCY_VALUE), 8);

        $total_amount = $total_amount * 100; //USD in Cents
        $this->session->set_userdata('total_amount', $total_amount);

        $this->set('application_id', $application_id);
        $this->set('location_id', $location_id);

        $this->setView();
    }

    public function squareup_payment()
    {

        require_once 'Squareup.php';
        $squareup = new SquareUp;
        $this->load->model('payment_model');

        if (!empty($this->session->userdata('from_replica'))) {
            $redirect_url = "register/replica_register";
        } else {
            $redirect_url = "register/user_register";
        }

        if (empty($this->session->userdata('inf_regr'))) {
            $msg = lang('you_cant_go_to_payment_page_directly_without_filling_all_registration_fields');
            $this->redirect($msg, $redirect_url, false);
        }

        $regr_data = $this->session->userdata('inf_regr');
        $total_amount = $this->session->userdata('total_amount');

        $merchant_details = $this->configuration_model->getSquareUpConfigDetails();
        $location_id = $merchant_details['location_id'];

        $nonce = $_POST['nonce'];
        if (is_null($nonce)) {
            $this->payment_model->insertSquareUpResponse($regr_data, "Invalid card data", $this->LOG_USER_ID);
            $this->session->unset_userdata(['inf_regr', 'inf_reg_post_array', 'from_replica']);
            $msg = lang('invalid_card_data');
            $this->redirect($msg, $redirect_url, false);
        }

        $request_body = array(
            "card_nonce" => $nonce,
            # This amount is in cents. It's also hard-coded for $1.00, which isn't very useful.
            "amount_money" => array(
                "amount" => $total_amount,
                "currency" => "USD"
            ),
            "idempotency_key" => uniqid()
        );
        $response = $squareup->squareResponse($request_body, $location_id);

        if ($response['status']) {
            $transaction_id = $response['transaction_id'];
            $pending_signup_status = $this->configuration_model->getPendingSignupStatus('squareup');
            $regr = $this->session->userdata('inf_regr');
            $module_status = $this->MODULE_STATUS;
            $product_status = $module_status['product_status'];
            $this->register_model->begin();
            $email_verification = $this->configuration_model->getEmailVerificationStatus();
            $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);
            if ($status['status']) {
                $this->register_model->commit();
                $user_name = $status['user_name'];
                $user_id = $status['user_id'];
                $insert_id = $this->payment_model->insertSquareUpPaymentDetails($user_id, $user_name, $request_body, 'register', $transaction_id, 'success');
                $transaction_password = $pending_signup_status ? '' : $status['transaction_password'];
                $payment_method = 'squareup';

                $p_id = $regr["product_id"];
                $product_amount = $this->register_model->getProductAmount($p_id);
                $register_amount = $this->register_model->getRegisterAmount();
                $total_amount = $product_amount + $register_amount;

                $email_verification = $this->configuration_model->getEmailVerificationStatus();
                if ($product_status == "yes") {
                    $insert_into_sales = $this->register_model->insertIntoSalesOrder($user_id, $regr['product_id'], $payment_method, $pending_signup_status, $email_verification);
                }
                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'user_register', 'New User Registered', $pending_signup_status);
                }

                $this->session->unset_userdata(['inf_regr', 'inf_reg_post_array', 'from_replica']);

                $id_encode = $this->encryption->encrypt($user_name);
                $id_encode = str_replace("/", "_", $id_encode);
                $user_name_encrypt = urlencode($id_encode);
                if ($pending_signup_status) {
                    $msg = "<span><b>" . lang('registration_completed_successfully_pending') . "!</b> " . lang("User_Name") . ": {$user_name}";
                } else {
                    $msg = "<span><b>" . lang('registration_completed_successfully') . "!</b> " . lang("User_Name") . ": {$user_name} " . lang("transaction_password") . ": {$transaction_password}</span>";
                }

                $this->redirect($msg, "register/preview/{$user_name}", true);
            } else {
                $this->payment_model->insertSquareUpResponse($this->session->userdata('inf_regr'), 'Confirm Register Failed', $this->LOG_USER_ID);
                $this->register_model->rollback();
                $msg = lang('registration_failed');
                $this->redirect($msg, $redirect_url, false);
            }
        } else {
            $this->payment_model->insertSquareUpResponse($this->session->userdata('inf_regr'), $response['msg'], $this->LOG_USER_ID);
            $this->session->unset_userdata(['inf_regr', 'inf_reg_post_array', 'from_replica']);
            $msg = $response['msg'];
            $this->redirect($msg, $redirect_url, false);
        }
    }

    public function change_default_language()
    {
        $language_id = $this->input->post('language');
        $languages = $this->inf_model->getAllLanguages();
        $lang_arr_count = count($languages);
        for ($i = 0; $i < $lang_arr_count; $i++) {
            if ($language_id == $languages[$i]['lang_id']) {
                $this->session->set_userdata("inf_language", array("lang_id" => $languages[$i]['lang_id'], "lang_name_in_english" => $languages[$i]['lang_name_in_english']));
                break;
            }
        }
        echo "yes";
    }
    public function get_product_amount()
    {
        $product_id = $this->input->post('product_id');
        if($product_id){
        $product_amount = $this->product_model->getProductAmountForReg($product_id);
        
        $pck_type = $this->validation_model->getPckProdType($product_id);
        $support_board=$this->validation_model->getPackageSupportBasedOnProductId($product_id,'board');
        $downline_count=0;
        $sponsor_name = $this->input->post('sponsor_user_name');
        $user_id=$this->validation_model->userNameToID($sponsor_name);
        $downline_data='';
        if($support_board=='yes' && $user_id){
            $support_board='no';
            $downlines=$this->getBoardDownlines($sponsor_name);
            if(!empty($downlines)){
                $downline_count=count($downlines['data'])??0;
                if($downline_count>2){
                    $support_board='yes';
                    foreach($downlines['data'] as $data){
                        $downline_data .= '<option value="' . $data['id'] . '" >' . $data['username'] . '</option>';
                    }
                }
            }
        }else{
        	$support_board='no';
        }
        $product_comb=$combo=array();
        // if($pck_type=='founder_pack'){
        //     $combo=$this->product_model->getProductCombo($product_id);
        //     $combo=json_decode($combo);
        //     $i=0;
        //     foreach($combo as $key=>$comb){
        //         $product_comb[$i]['count']=$comb;
        //         $product_comb[$i]['key']=$key;
        //         $product_comb[$i]['package_name']=$this->product_model->getProductNamePackageId($key);
        //         $i++;
        //     }
        // }
        
        echo json_encode(['product_amount' => round($product_amount, $this->PRECISION),
        'pck_type' => $pck_type,'combo'=>$product_comb,'downline_count'=>$downline_count,'board_status'=>$support_board,'downlines'=>$downline_data]);
        }
    }
    public function confirm_email()
    {

        if (!empty($this->uri->segment(3))) {

            $keyword = $this->uri->segment(3);
            $user_name = urldecode($keyword);
            $user_name = str_replace("_", "/", $user_name);
            $user_name = $this->encryption->decrypt($user_name);

            $registration_details = $this->register_model->getEmailRegistrationDetailsByUsername($user_name);

            $payment_method = $registration_details['payment_method'];
            $id = $registration_details['data']["pending_id"] = $registration_details['id'];

            $user_id = $this->validation_model->userNameToID($registration_details['data']['placement_user_name']);
            $this->register_model->begin();
            if ($this->validation_model->isLegAvailable($user_id, $registration_details['data']['position'], true)) {
                $registration_details['data']['reg_from_tree'] = true;
            } else {
                $registration_details['data']['reg_from_tree'] = false;
            }
            $registration_details['data']['user_name_type'] = 'static';
            $registration_details['data']['joining_date'] = date('Y-m-d H:i:s');
            $res = $this->register_model->confirmRegister($registration_details['data'], $this->MODULE_STATUS);
            if (isset($res['status']) && $res['status']) {
                $this->register_model->commit();
                $user_id = $res['user_id'];
                $this->register_model->updatePendingRegistration($id, $user_id, $user_name, $payment_method, $registration_details['data']);

                $result = true;
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'user_register', 'User Registered', $data = '');
                }
            } else {
                $this->register_model->rollback();
                $msg = lang('error_approve_registration') . " of $user_name";
                $this->redirect($msg, 'pending_registration', false);
            }

            //redirect url
            $redirect_url = SITE_URL . "/backoffice/login";
            if ($result) {
                $msg = lang('success_approve_registration');
                redirect($redirect_url);
            } else {
                $msg = lang('error_approve_registration');
                redirect($redirect_url);
            }
        }
    }

    public function multiRegistrationAPI()
    {
        $reg_post_array = $this->input->get(NULL, TRUE);
        $reg_post_array = $this->validation_model->stripTagsPostArray($reg_post_array);
        if(!count($reg_post_array) || DEMO_STATUS != "yes") {
            echo json_encode(['status' => false, 'message' => 'Invalid request']);
            exit();
        }
        $inf_token = $reg_post_array['inf_token'] ?? '';
        $demo_id = $reg_post_array['user_id'] ?? '';
        
        if($inf_token != 'f6f7369316c4928fdceaaed397356f5b' || !$demo_id) {
            echo json_encode(['status' => false, 'message' => 'Invalid request']);
            exit();
        }
        $this->load->model('api_register_model');
        $infinite_mlm_user_details = $this->api_register_model->getInfiniteMlmUserDetails($demo_id);
        if(!count($infinite_mlm_user_details)) {
            echo json_encode(['status' => false, 'message' => 'Invalid request']);
            exit();
        }
        $this->inf_model->setDBPrefix($infinite_mlm_user_details['table_prefix'] . '_');
        $this->load->model('cleanup_model');
        $this->MODULE_STATUS = $this->cleanup_model->trackModule();
        $this->cleanup_model->multi_user_registration(10);
        echo json_encode(['status' => true, 'message' => 'Success']);
        exit();
    }
    ///test////
    function test_mail(){
        $regr['email']='amil@teamioss.in';
        $regr['first_name'] ='test';
        $regr['last_name'] ='test';
        $regr['mail_content']="test content";
        $this->mail_model->sendAllEmails('invaite_mail', $regr);
    }
    function rank_check(){
        $this->load->model('rank_model');
        $this->rank_model->updateUplineRank(575);
    }
function stripe_payment(){
        $title = lang('stripe_payment');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('stripe_payment');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('stripe_payment');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        if (!empty($this->session->userdata('from_replica'))) {
            $redirect_url = "register/replica_register";
        } else {
            $redirect_url = "register/user_register";
        }

        if (empty($this->session->userdata('inf_regr'))) {
            $msg = lang('you_cant_go_to_payment_page_directly_without_filling_all_registration_fields');
            $this->redirect($msg, $redirect_url, false);
        }

        $regr_data = $this->session->userdata('inf_regr');
        
        $total_amount = $regr_data['total_amount'];
        $total_amount=round($total_amount*1.055,2);
        $this->load->model('configuration_model');
        $stripe_config = $this->configuration_model->getStripeConfigDetails();
        $currency = $this->DEFAULT_CURRENCY_CODE;
        
        
        \Stripe\Stripe::setApiKey($stripe_config['secret_key']);
    
        $line_items = [];
        $reg_item = [
            'price_data' => [
                'product_data' => [
                    'name' => 'Amount to Pay',
                ],
                'unit_amount' => $total_amount * 100,
                'currency' =>  $currency,
            ],
            'quantity' => 1,
            ];
            
        array_push($line_items, $reg_item);

        $base_url = base_url();

        $session = \Stripe\Checkout\Session::create([
          'payment_method_types' => ['card'],
          'line_items' => $line_items,  
          'mode' => 'payment',
          'success_url' => base_url($stripe_config['return_url']),
          'cancel_url' => base_url($stripe_config['cancel_url'])
        ]);
        
        $user_id = $this->LOG_USER_ID;
        $session_id = $session->id;

        $this->load->model('payment_model');
        $this->payment_model->addStripePaymentHistory([
            'done_by' => $user_id,
            'user_name' => $regr_data['user_name_entry']??'',
            'session_id' => $session_id,
            'currency' => $currency,
            'total_amount' => $total_amount,
            'data' => serialize($regr_data),
            'date'          => date('Y-m-d H:i:s'),
            'type'          => 'registration',
            'status'        => 'pending',
            'action'        => 'pending',
        ]);
        
        $this->set('checkout_session_id', $session_id);
        $this->set('stripe_api_key', $stripe_config['public_key']);
        
        $this->setView();
    }
    public function stripe_success()
    {
        $this->load->model('payment_model');

        $this->payment_model->updateStripePaymentHistory($_REQUEST['session_id'], [
            'status' => 'success'
        ]);

        $user_id = $this->LOG_USER_ID;
        
        $msg = lang('registration_started_success');
        $this->redirect($msg, "home", true);
    }
    public function stripe_cancel()
    {
        $this->load->model('payment_model');
        $this->payment_model->updateStripePaymentHistory($_REQUEST['session_id'], [
            'session_id' => $_REQUEST['session_id'],
            'status' => 'cancelled'
        ]);
        
        $msg = lang('registration_started_failed');
        $this->redirect($msg, "home", false);
    }

     public function stripe_webhook()
    {

        $this->load->model('configuration_model');
        $this->load->model('payment_model');
        // $this->load->model('upgrade_model');

        $stripe_config = $this->configuration_model->getStripeConfigDetails();
        \Stripe\Stripe::setApiKey($stripe_config['secret_key']);
        
        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = $stripe_config['webhook_key'];

        $payload = @file_get_contents('php://input');

        $this->db->insert('stripe_webhook', [
            'data' => $payload,
            'date' => date('Y-m-d H:i:s')
        ]);

        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;
        
        try {
          $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $endpoint_secret
          );
        } catch(\UnexpectedValueException $e) {
          // Invalid payload
          http_response_code(400);
          exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
          // Invalid signature
          http_response_code(400);
          exit();
        }

        $session = $event->data->object;
        $payLoadArr = json_decode($payload, true);
        $response = $payLoadArr['data']['object'];
        $payment_history = $this->payment_model->getStripePaymentHistory($session->id, [ 'data', 'type','done_by']);
        // error_log("stripe http_response code reached", 0);
        if ($payLoadArr['type'] == 'checkout.session.completed' &&  $response['payment_status'] == 'paid') {
          
            if($payment_history->type == 'registration') {
                $this->register_model->confirmStripeRegistration($payment_history,$session->id);
            }
             
          
        }   
        http_response_code(200);

    }
    public function stripe_test(){
        $session_id='cs_live_a1NpZcYB1oxEwbmjhHI6NEbp1O5AY2p2IcAgG9CWAuU59sAbtLUeBQLYHq';
        $this->load->model('payment_model');
        $payment_history = $this->payment_model->getStripePaymentHistory($session_id, [ 'data', 'type','done_by']);
        //  log_message('error', 'stripe here');
     
       
        if($payment_history->type == 'registration') {
            //  log_message('error', 'stripe here');
            $this->register_model->confirmStripeRegistration($payment_history,$session_id);
        }
             
    }
    public function send_mail_otp(){
        $this->load->model('mail_model');
        $regr = array();
        $regr['email'] = $this->input->post('email');
        $regr['first_name'] = $this->input->post('first_name');
        $regr['last_name'] = '';
        $regr['otp'] = 123456;
        if (!filter_var($regr['email'], FILTER_VALIDATE_EMAIL)) {
          $emailErr = "Invalid email format";
          echo "no";
          exit();
        }

        $this->session->set_userdata('mail_otp',$regr['otp']);
        $this->session->set_userdata('email_address',$regr['email']);
        $result = $this->mail_model->sendAllEmails('send_mail_otp',$regr);
        echo "yes";
        exit();
        
    }

    public function check_otp(){
        // if($this->LOG_USER_TYPE=="admin"){
        //     echo "yes";
        //     exit();
        // }

        $otp = $this->input->post('otp');
        $email = $this->input->post('email');
        if(!$otp && !$email){
            echo "no";
            exit();
        }
        $check_otp = $this->session->userdata('mail_otp');
        $check_email = $this->session->userdata('email_address');

        if(($otp==$check_otp)&&($check_email==$email)) {
            $this->session->set_userdata('mail_otp_status',TRUE);
            echo "yes";
        } else {
            $this->session->set_userdata('mail_otp_status',FALSE);
            echo "no";
        }    
    }
    public function stripe_webhook_test()
    {

        $this->load->model('configuration_model');
        $this->load->model('payment_model');
        // $this->load->model('upgrade_model');

        $stripe_config = $this->configuration_model->getStripeConfigDetails();
        \Stripe\Stripe::setApiKey($stripe_config['secret_key']);
        
        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = $stripe_config['webhook_key'];

        $payload = @file_get_contents('php://input');

        $this->db->insert('stripe_webhook', [
            'data' => $payload,
            'date' => date('Y-m-d H:i:s')
        ]);

        // $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        // $event = null;
        
        // try {
        //   $event = \Stripe\Webhook::constructEvent(
        //     $payload, $sig_header, $endpoint_secret
        //   );
        // } catch(\UnexpectedValueException $e) {
        //   // Invalid payload
        //   http_response_code(400);
        //   exit();
        // } catch(\Stripe\Exception\SignatureVerificationException $e) {
        //   // Invalid signature
        //   http_response_code(400);
        //   exit();
        // }

        //$session = $event->data->object;
        $payLoadArr = json_decode($payload, true);
        $response = $payLoadArr['data']['object'];

        $payment_history = $this->payment_model->getStripePaymentHistory($response['id'], [ 'data', 'type','done_by']);
        // error_log("stripe http_response code reached", 0);
        if ($payLoadArr['type'] == 'checkout.session.completed' &&  $response['payment_status'] == 'paid') {
          
            if($payment_history->type == 'registration') {
                $this->register_model->confirmStripeRegistration($payment_history,$response['id']);
            }
             
          
        }   
        http_response_code(200);

    }
    public function test_otp(){
        $regr = array();
        $regr['email'] = 'amil@teamioss.in';
        $regr['first_name'] = 'amil';
        $regr['last_name'] = '';
        $regr['otp'] = mt_rand(100000, 999999);
        if (!filter_var($regr['email'], FILTER_VALIDATE_EMAIL)) {
          $emailErr = "Invalid email format";
          echo "no";
          exit();
        }

        $result = $this->mail_model->sendAllEmails('send_mail_otp',$regr);
        dd($result);
    }
       public function board_register(){
        $reg_post_array  = $this->input->post(null, true);
        $board_status=$this->validation_model->getBoardRegisterStatus($this->LOG_USER_ID);
        if($board_status=='no'){
            $sponsor_id=$this->validation_model->getSponsorId($this->LOG_USER_ID);
            $sponsor_username=$this->validation_model->idToUsername($sponsor_id);
            $board_downlines=$this->getBoardDownlines($sponsor_username);
            $down_count=0;
            if(isset($board_downlines['data'])){
                $down_count=count($board_downlines['data']);
            }
            $downline_id=null;
            if($down_count>1 && !isset($reg_post_array['gift'])){
                $msg = $this->lang->line('gift_id_is_required');
                $this->redirect($msg, "home/index", false);
            }else{
                if($down_count>1){
                    $downline_id=$reg_post_array['gift'];
                }
                $user_details=$this->validation_model->getAllUserData(0,0,$this->LOG_USER_ID);
                $url = BOARD_URL.'/user/register';
                $data = array(
                    'api_key' => BOARD_API_KEY,
                    'sponsor_username' => $sponsor_username,
                    'name' => $user_details[0]['first_name']." ".$user_details[0]['last_name'],
                    'firstname' => $user_details[0]['first_name'],
                    'lastname' => $user_details[0]['last_name'],
                    'username' => $user_details[0]['user_name'],
                    'email' =>$this->validation_model->getUserEmailId($this->LOG_USER_ID),
                    'country_code' => $this->country_state_model->getCountryCodeFromId($this->validation_model->getUserCountryId($this->LOG_USER_ID)),
                    'mobile' => $this->validation_model->getUserPhoneNumber($this->LOG_USER_ID),
                    'password' => $user_details[0]['password'],
                    'downline_user_id' => $downline_id,
                );

                $options = array(
                    'http' => array(
                        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method' => 'POST',
                        'content' => http_build_query($data),
                    ),
                );

                $context = stream_context_create($options);
                $result = file_get_contents($url, false, $context);

                if ($result === FALSE) {
                    // Handle error
                   // echo 'Error occurred while making the request.';
                    $msg = $this->lang->line('registration_failed');
                    $this->redirect($msg, "home/index", false);
                } else {
                    // Process the result
                    //echo $result;
                    $responseArray = json_decode($result, true);
                    $this->db->set('user_id',$this->LOG_USER_ID);
                    $this->db->set('data',serialize($responseArray));
                    $this->db->insert('board_api_log');
                    // Check if decoding was successful
                    if ($responseArray !== null) {
                        // Access the relevant data
                        $status = $responseArray['original']['status'];
                        if($status==200){
                            $this->db->set('board_register_status','yes');
                            $this->db->where('id',$this->LOG_USER_ID);
                            $this->db->update('ft_individual');
                            $msg = $this->lang->line('registration_completed');
                            $this->redirect($msg, "home/index", true);
                        }
                        $msg = $this->lang->line('registration_failed');
                        $this->redirect($msg, "home/index", false);
                        
                    }
                }
            }
        }
    }
    public function getBoardDownlines($sponsorUsername){
        
        $url = BOARD_URL.'/user/getdownlines';
        $apiKey = BOARD_API_KEY;
        $data = array(
            'api_key' => $apiKey,
            'sponsor_username' => $sponsorUsername,
        );

        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ),
        );

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            // Handle error
           
        } else {
            // Process the result
            return json_decode($result,true);
        }
        
    }
    

}
