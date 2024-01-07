<?php

require_once 'Inf_Controller.php';

class Member extends Inf_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("android/new/android_model");
        $this->load->model('validation_model');
        $this->load->model('Api_model');
        $this->load->model('Ticket_system_model');
        $this->LOG_USER_ID = $this->rest->user_id;
        $this->LOG_USER_TYPE = 'user';
    }

    function json_response($status, $message, $data = array()) {
        $return = array(
            'status' => $status,
            'message' => $message,
            'data' => $data);
        echo json_encode($return);
        die;
    }

    function change_password_post() {
// add three extra fields current_password,new_password and confirm_password
        $post_array = $this->input->post(NULL, TRUE);
        $post_array = $this->validation_model->stripTagsPostArray($post_array);
        $this->load->model('password_model');
        $user_id = $this->LOG_USER_ID;
        $user_type = $this->LOG_USER_TYPE;
        $new_pwd_md5 = password_hash($post_array['new_password'], PASSWORD_DEFAULT);
        if (!$user_id) {
            $user_details['status'] = false;
            $user_details['message'] = lang('inv_login_details');
        } else {
            $dbpassword = $this->password_model->selectPassword($user_id);
            $val = $this->password_model->validatePswd($post_array['new_password']);
            if (!$val) {
                $user_details['status'] = false;
                $user_details['message'] = lang('special_char_no');
            } else if (!$post_array['new_password']) {
                $user_details['status'] = false;
                $user_details['message'] = lang('must_new_pas');
            } else if (!$post_array['old_password']) {
                $user_details['status'] = false;
                $user_details['message'] = lang('must_cur_pas');
            } elseif (!password_verify($post_array['old_password'], $dbpassword) || strlen($post_array['new_password']) < 6) {
                $user_details['status'] = false;
                $user_details['message'] = lang('error_dialog_paswd');
            } else {
                $update = $this->password_model->updatePassword($new_pwd_md5, $user_id, $user_type);
                if ($update) {
                    $send_details = array();
                    $type = 'change_password';
                    $email = $this->validation_model->getUserEmailId($user_id);
                    $send_details['full_name'] = $this->validation_model->getUserFullName($user_id);
                    $send_details['new_password'] = $post_array['new_password'];
                    $send_details['email'] = $email;
                    $send_details['first_name'] = $this->validation_model->getUserData($user_id, "user_detail_name");
                    $send_details['last_name'] = $this->validation_model->getUserData($user_id, "user_detail_second_name");
                    $result = $this->mail_model->sendAllEmails($type, $send_details);
                    $this->validation_model->insertUserActivity($user_id, 'password changed mob', $user_id);
                    $user_details['status'] = true;
                    $user_details['password_md5'] = $new_pwd_md5;
                    $user_details['message'] = lang('passwd_changed');
                } else {
                    $user_details['status'] = false;
                    $user_details['message'] = lang('error');
                }
            }
        }
        echo json_encode($user_details);
        exit();
    }

    function get_profile_data() {
        $user_id = $this->LOG_USER_ID;
        $this->load->model('profile_model');
        if (!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('invalid_user');
        } else {
            $user_name = $this->LOG_USER_NAME;
            $product_status = $this->MODULE_STATUS['product_status'];
            $lang_id = $this->LANG_ID;
            $json_response['status'] = true;
            $json_response['message'] = lang('logged_in');
            $json_response['data'] = $this->Api_model->getProfileDetails($user_id, $product_status, $user_name, $lang_id);
        }

        echo json_encode($json_response);
        exit();
    }
    
    public function get_gateways_list() {
        $user_id = $this->LOG_USER_ID;
        if (!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('invalid_user');
        } else {
            $this->load->model('payout_model');
            $payment_method  = $this->payout_model->gatewayList();
            $json_response['status'] = true;
            $json_response['message'] = "success";
            $json_response['data'] = $payment_method;
        }
        echo json_encode($json_response);
        exit();
    }

    function get_refferal_details() {
        $user_id = $this->LOG_USER_ID;
        $this->load->model('configuration_model');
        $numrows = $result_per_page = 0;
        if (!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('invalid_user');
        } else {
            $base_url = base_url() . 'user/payout/my_income';
            $config = $this->pagination->customize_style();
            $config['base_url'] = $base_url;
            $config['per_page'] = $this->input->post('limit');
            if ($this->input->post('offset') != "")
                $page = $this->input->post('offset');
            else
                $page = 0;
            $json_response['status'] = true;
            $json_response['message'] = lang('logged_in');
            $json_response['data'] = $this->Api_model->getReferalDetails($user_id, $page, $config['per_page']);
            //$json_response['data'] = $this->configuration_model->getReferalDetails($user_id,$limit='', $offset='');
            echo json_encode($json_response);
            exit();
        }
    }

    function get_product() {
        $user_id = $this->LOG_USER_ID;
        $this->load->model('configuration_model');
        if (!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('invalid_user');
        } else {
            $json_response['status'] = true;
            $json_response['message'] = lang('logged_in');
            $json_response['data'] = $this->Api_model->getProductDetails();
            echo json_encode($json_response);
            exit();
        }
    }
    
    public function uploadImage() {
        $user_id  = $this->LOG_USER_ID;
        
        $this->load->model('profile_model');
        $this->load->model('configuration_model');
        
        $response = array();

        if (!isset($_FILES['image'])) {
            $response['status'] = false;
            $response['message'] = lang('profile_pic_not_selected');
            echo json_encode($response);
            exit();
        }

        if (!empty($_FILES['image'])) {
            $upload_config = $this->validation_model->getUploadConfig();
            $upload_count = $this->validation_model->getUploadCount($user_id);
            if ($upload_count >= $upload_config) {
                $msg = lang('you_have_reached_the_max_upload_limit');
                $response['status'] = false;
                $response['message'] = $msg;
                echo json_encode($response);
                exit();
            }
        }
        
        if ($_FILES['image']['error'] != 4) {
            $random_number = floor($user_id * rand(1000, 9999));
            $config['file_name'] = "pro_" . $random_number;
            $config['upload_path'] = IMG_DIR . 'profile_picture/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = '2048';
            $config['remove_spaces'] = true;
            $config['overwrite'] = false;

            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('image')) {
                $msg = $this->upload->display_errors();
                $response['status'] = false;
                $response['message'] = $msg;
                echo json_encode($response);
                exit();
            } else {
                $image_arr = array('upload_data' => $this->upload->data());
                $new_file_name = $image_arr['upload_data']['file_name'];
                $image = $image_arr['upload_data'];

                if ($image['file_name']) {
                    $data['photo'] = '../uploads/images/profile_picture/' . $image['file_name'];
                    $data['raw'] = $image['raw_name'];
                    $data['ext'] = $image['file_ext'];
                }
                $res = $this->profile_model->changeProfilePicture($user_id, $new_file_name);
                $this->validation_model->updateUploadCount($user_id);
                if ($res) {
                    $msg = lang('profile_pic_updated_successfully');
                    $response['status'] = true;
                    $response['message'] = $msg;
                    echo json_encode($response);
                    exit();
                } else {
                    $msg = lang('profile_pic_updation_failed');
                    $response['status'] = false;
                    $response['message'] = $msg;
                    echo json_encode($response);
                    exit();
                }
            }
        } else {
            $response['status'] = false;
            $response['message'] = lang('profile_pic_not_selected');
            echo json_encode($response);
            exit();
        }
    }

    function change_transaction_password() {
// add three extra fields current_password,new_password and confirm_password
        $post_array = $this->input->post(NULL, TRUE);
        $post_array = $this->validation_model->stripTagsPostArray($post_array);
        $this->load->model('tran_pass_model');
        $user_id = $this->LOG_USER_ID;
        $user_type = $this->LOG_USER_TYPE;
        $user_name = $this->LOG_USER_NAME;
        $new_passcode = $post_array['new_tran_password'];
        $old_passcode = $post_array['old_tran_password'];
        if (!$user_id) {
            $user_details['status'] = false;
            $user_details['message'] = lang('inv_login_details');
        } else {
            $passcode = $this->tran_pass_model->getUserPasscode($user_id);
            if (!$post_array['new_tran_password']) {
                $user_details['status'] = false;
                $user_details['message'] = lang('enter_new_tranpas');
            } else if (!$post_array['old_tran_password']) {
                $user_details['status'] = false;
                $user_details['message'] = lang('enter_cur_transpas');
            } else if (strlen($post_array['new_tran_password']) < 8) {
                $user_details['status'] = false;
                $user_details['message'] = lang('trans_pass_short');
            } else if (!password_verify($old_passcode, $passcode)) {
                $user_details['status'] = false;
                $user_details['message'] = lang('trans_pass_wrong');
            } else {
                $update = $this->tran_pass_model->updatePasscode($user_id, $new_passcode, $passcode);
                if ($update) {
                    $send_details = array();
                    $type = 'send_tranpass';
                    $email = $this->validation_model->getUserEmailId($user_id);
                    $send_details['full_name'] = $this->validation_model->getUserFullName($user_id);
                    $send_details['tranpass'] = $post_array['new_tran_password'];
                    $send_details['email'] = $email;
                    $send_details['first_name'] = $this->validation_model->getUserData($user_id, "user_detail_name");
                    $send_details['last_name'] = $this->validation_model->getUserData($user_id, "user_detail_second_name");
                    $result = $this->mail_model->sendAllEmails($type, $send_details);
                    $this->validation_model->insertUserActivity($user_id, 'transaction password changed mob', $user_id);
                    $user_details['status'] = true;
                    $user_details['message'] = lang('trans_pas_change');
                } else {
                    $user_details['status'] = false;
                    $user_details['message'] = lang('err_trans_pas_chg');
                }
            }
        }
        echo json_encode($user_details);
        exit();
    }

    function get_fund_transfer_detail() {
// add three extra fields current_password,new_password and confirm_password
        $this->load->model('ewallet_model');
        $user_id = $this->LOG_USER_ID;
        $balamount = $this->ewallet_model->getBalanceAmount($user_id);
        $trans_fee = $this->ewallet_model->getTransactionFee();
        if (!$user_id) {
            $user_details['status'] = false;
            $user_details['message'] = lang('inv_login_details');
        } else {
            $user_details['status'] = true;
            $user_details['message'] = lang('success');
            $user_details['bal_amount'] = $balamount;
            $user_details['trans_fee'] = $trans_fee;
        }
        echo json_encode($user_details);
        exit();
    }

    function fund_transfer() {
        $user_details = array();
        $post_array = $this->input->post(NULL, TRUE);
        $post_array = $this->validation_model->stripTagsPostArray($post_array);
        $this->load->model('ewallet_model');
        $user_type = $this->LOG_USER_TYPE;
        $user_name = $this->LOG_USER_NAME;
        $userid = $this->LOG_USER_ID;

        $to_user = $post_array['to_username'];
        $to_userid = $this->ewallet_model->userNameToID($to_user);
        $user_exists = $this->ewallet_model->isUserNameAvailable($to_user);
        $balamount = $this->ewallet_model->getBalanceAmount($userid);
        $trans_fee = $this->ewallet_model->getTransactionFee();
        $pass = $this->ewallet_model->getUserPassword($userid);
        $tran_pass = $post_array['tran_pswd'];
        $to_user_name = $post_array['to_username'];
        $to_user_id = $this->ewallet_model->userNameToID($to_user_name);
        $trans_amt = $post_array['amount'];
        $trans_amt = round($trans_amt / $this->DEFAULT_CURRENCY_VALUE, 8);
        $transaction_concept = $this->validation_model->textAreaLineBreaker($post_array['transaction_note']);
        $total_req_amount = $post_array['tot_req_amount'];
        if (!$userid) {
            $user_details['status'] = false;
            $user_details['message'] = lang('inv_login_details');
        } else if (!$post_array['to_username']) {
            $user_details['status'] = false;
            $user_details['message'] = lang('enter_to_username');
        } else if (!$post_array['amount']) {
            $user_details['status'] = false;
            $user_details['message'] = lang('enter_amount');
        } else if (!$post_array['transaction_note']) {
            $user_details['status'] = false;
            $user_details['message'] = lang('please_enter_transaction_concept');
        } else if (!$post_array['tran_pswd']) {
            $user_details['status'] = false;
            $user_details['message'] = lang('you_must_enter_transaction_password');
        } else if (!$user_exists) {
            $user_details['status'] = false;
            $user_details['message'] = lang('invalid_user');
        } else if ($userid == $to_userid) {
            $user_details['status'] = false;
            $user_details['message'] = lang('invalid_user');
        } else {
            if ($total_req_amount <= $balamount) {
                if (password_verify($tran_pass, $pass)) {
                    $this->ewallet_model->begin();
                    $transaction_id = $this->ewallet_model->getUniqueTransactionId();
                    $up_date1 = $this->ewallet_model->updateBalanceAmountDetailsFrom($userid, round($total_req_amount, 8));
                    $up_date2 = $this->ewallet_model->updateBalanceAmountDetailsTo($to_user_id, round($trans_amt, 8));
                    $this->ewallet_model->insertBalAmountDetails($userid, $to_user_id, round($trans_amt, 8), $amount_type = '', $transaction_concept, $trans_fee, $transaction_id);
                    if ($up_date1 && $up_date2) {
                        $this->ewallet_model->commit();
                        $login_id = $this->LOG_USER_ID;
                        $data_array = array();
                        $data_array['transfer_post_array'] = $post_array;
                        $data = serialize($data_array);
                        $this->validation_model->insertUserActivity($login_id, 'fund transferred mob', $to_user_id, $data);
                        $user_details['status'] = true;
                        $user_details['message'] = lang('fund_transfered_successfully');
                    } else {
                        $this->ewallet_model->rollback();
                        $user_details['status'] = false;
                        $user_details['message'] = lang('error_on_fund_transfer');
                    }
                } else {
                    $user_details['status'] = false;
                    $user_details['message'] = lang('invalid_transaction_password');
                }
            } else {
                $user_details['status'] = false;
                $user_details['message'] = lang('you_dont_have_enough_balance');
            }
        }
        echo json_encode($user_details);
        exit();
    }

    function get_payout_release_details() {

        $this->load->model('payout_model');
        $this->load->model('ewallet_model');
        $user_id = $this->LOG_USER_ID;
 
        if (!$user_id) {
            $user_details['status'] = false;
            $user_details['message'] = lang('inv_login_details');
        } else {
            $user_details['status'] = true;
            $user_details['message'] = lang('success');
            $balance_amount = $this->payout_model->getUserBalanceAmount($user_id);
            $req_amount = $this->payout_model->getRequestPendingAmount($user_id);
            $total_amount = $this->ewallet_model->getTotalReleasedAmount($user_id);
            $payout_method = $this->payout_model->getUserPayoutType($user_id);
            $minimum_payout_amount = $this->payout_model->getMinimumPayoutAmount();
            $maximum_payout_amount = $this->payout_model->getMaximumPayoutAmount();
            $config_details = $this->configuration_model->getSettings();

            if($balance_amount <= $maximum_payout_amount) {
                $available_max_payout = $balance_amount;
            } else {
                $available_max_payout = $maximum_payout_amount;
            }

            $user_details['status'] = true;
            $user_details['message'] = lang('success');
            $user_details['balance_amount'] = $balance_amount;//Ewallet Balance 
            $user_details['req_amount'] = $req_amount;//Ewallet Amount Already in Payout Process
            $user_details['total_amount'] = $total_amount ? $total_amount : 0;//Total Paid Amount
            $user_details['payout_method'] = $payout_method;//Preffered Payout Method
            $user_details['minimum_payout_amount'] = $minimum_payout_amount;//Minimum Withdrawal Amount
            $user_details['maximum_payout_amount'] = $maximum_payout_amount;//Maximum Withdrawal Amount
            $user_details['available_max_payout'] = $available_max_payout;//Available Maximum Withdrawal Amount
            $user_details['payout_req_validity'] = $config_details['payout_request_validity'];//Payout Request Validity (Days)
        }
        echo json_encode($user_details);
        exit();
    }

    public function payout_release_request() {
        $user_details = array();
        $post_array = $this->input->post(NULL, TRUE);
        $post_array = $this->validation_model->stripTagsPostArray($post_array);
        $this->load->model('payout_model');
        $this->load->model('mail_model');
        $user_id = $this->LOG_USER_ID;
        $minimum_payout_amount = $this->payout_model->getMinimumPayoutAmount();
        $maximum_payout_amount = $this->payout_model->getMaximumPayoutAmount();
        $balance_amount = $this->payout_model->getUserBalanceAmount($user_id);
        $req_amount = $this->payout_model->getRequestPendingAmount($user_id);
        $total_amount = $this->payout_model->getReleasedPayoutTotal($user_id);
        $min_payout = $this->configuration_model->getMinPayout();
        $transation_password = (strip_tags($this->input->post('tran_pswd', TRUE)));
        $password_flag = $this->payout_model->checkTransactionPassword($user_id, $transation_password);
        if (!$user_id) {
            $user_details['status'] = false;
            $user_details['message'] = lang('inv_login_details');
        } else if (!$post_array['payout_amount']) {
            $user_details['status'] = false;
            $user_details['message'] = lang('enter_amount');
        } else if (!$post_array['tran_pswd']) {
            $user_details['status'] = false;
            $user_details['message'] = lang('you_must_enter_transaction_password');
        } else {
            if ($password_flag) {
                $payout_amount = round(($this->input->post('payout_amount', TRUE)) / $this->DEFAULT_CURRENCY_VALUE, 8);
                $request_date = date('Y-m-d H:i:s');
                $balance_amount = $this->payout_model->getUserBalanceAmount($user_id);
                if ($balance_amount >= $payout_amount && $payout_amount >= $minimum_payout_amount && $payout_amount <= $maximum_payout_amount) {
                    $res = $this->payout_model->insertPayoutReleaseRequest($user_id, $payout_amount, $request_date, 'pending');
                    if ($res) {
                        $this->payout_model->updateUserBalanceAmount($user_id, $payout_amount);
                        $data_array = array();
                        $data_array['tran_pass'] = $transation_password;
                        $data_array['payout_amount'] = $payout_amount;
                        $data_array['balance_amount'] = $balance_amount;
                        $data = serialize($data_array);
                        $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'payout request sent ', $this->LOG_USER_ID, $data);

                        $mail_arr['payout_amount'] = $payout_amount;
                        $mail_arr['username'] = $this->LOG_USER_NAME;
                        $mail_arr['email'] = $this->validation_model->getUserEmailId($this->ADMIN_USER_ID);
                        $mail_arr['first_name'] = '';
                        $mail_arr['last_name'] = '';
                        // $this->mail_model->sendAllEmails('payout_request', $mail_arr);
                        $user_details['status'] = true;
                        $user_details['message'] = lang('payout_req_sent');
                    } else {
                        $user_details['status'] = false;
                        $user_details['message'] = lang('payout_req_sent_err');
                    }
                } else if ($payout_amount > $balance_amount) {
                    $user_details['status'] = false;
                    $user_details['message'] = lang('you_dont_have_enough_balance');
                } else if ($payout_amount <= $minimum_payout_amount) {
                    $default_currency_left_symbol = ( $this->DEFAULT_SYMBOL_LEFT != '') ? $this->DEFAULT_SYMBOL_LEFT : "$";
                    $user_details['status'] = false;
                    $user_details['message'] = lang('payout_amount_low');
                } else {
                    $default_currency_left_symbol = ( $this->DEFAULT_SYMBOL_LEFT != '') ? $this->DEFAULT_SYMBOL_LEFT : "$";
                    $user_details['status'] = false;
                    $user_details['message'] = lang('payout_amount_high');
                }
            } else {
                $user_details['status'] = false;
                $user_details['message'] = lang('invalid_transaction_password');
            }
        }
        echo json_encode($user_details);
        exit();
    }

    public function get_repurchase_report() {
        $user_id = $this->LOG_USER_ID;
        $offset = $this->security->xss_clean(trim($this->input->post('offset')));
        $limit = $this->security->xss_clean(trim($this->input->post('limit')));
        $date1 = $this->security->xss_clean(trim($this->input->post('from_date')));
        $date2 = $this->security->xss_clean(trim($this->input->post('to_date')));
        if ($offset == '' || $offset == NULL || $limit == '' || $limit == NULL || $date1 == '' || $date1 == NULL || $date2 == '' || $date2 == NULL) {
            die(json_encode(array('status' => FALSE, 'message' => lang('partial_content'))));
        }
        $repurchase_details = $this->Api_model->getRepurchaseReport($date1, $date2, $user_id, $limit, $offset);
        die(json_encode(array('status' => TRUE, 'message' => lang('details_fetched'), 'data' => $repurchase_details)));
    }

    function get_rank_performance_detail() {
        $user_id = $this->LOG_USER_ID;
        if(!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_login_details');
            echo json_encode($json_response);
            exit();
        } else {
            $this->load->model('select_report_model');
            $details = array();
            $rank_details = array();
            $referal_count = 0;
            $current_rank = 0;
            $next_ran = 0;
            $next_rank = '';
            $referal_count = $this->validation_model->getReferalCount($user_id);
           
            $current_rank = $this->validation_model->getUserRank($user_id);            
            $personal_pv = $this->validation_model->getPersnlPv($user_id);
            
            if($current_rank!=''){  
                $rank_details = $this->select_report_model->selectRankDetails($current_rank); 
                $ref_count = $rank_details['referal_count'];
            }else{
                $ref_count = 0;
            }
            
            if(!$personal_pv){            
                $personal_pv =0;
            }
            
            $group_pv = $this->validation_model->getGrpPv($user_id);
            if(!$group_pv){
            $group_pv= 0;            
            }

            $next_rank = $this->select_report_model->getNextRank($ref_count);        
                
            if($current_rank != 0){
            $rank_details = $this->select_report_model->selectRankDetails($current_rank);                 
            $current_rank = $rank_details['rank_name']; 
            }else {
            $current_rank = 'NA';
            }
            
            if($next_rank){
            if($next_rank[0]->referal_count > $referal_count) {   
            $balance_referal_count = $next_rank[0]->referal_count - $referal_count;
            }else{
            $balance_referal_count = 0;    
            }
            if($next_rank[0]->personal_pv > $personal_pv) {
            $balance_personal_pv = $next_rank[0]->personal_pv - $personal_pv;
            }else {
            $balance_personal_pv = 0;  
            }
            if($next_rank[0]->gpv > $group_pv) {
            $balance_gpv = $next_rank[0]->gpv - $group_pv;
            }else {
            $balance_gpv = 0;  
            }            
            
            $next_ran = $next_rank[0]->rank_name;
            $next_referal_count = $next_rank[0]->referal_count;
            $next_pers_pv = $next_rank[0]->personal_pv;
            $next_grp_pv = $next_rank[0]->gpv;

            }else {
            $balance_referal_count = 'NA';
            $next_ran = 'NA';
            $next_referal_count = 'NA';
            $next_pers_pv = 'NA';
            $next_grp_pv = 'NA';
            $balance_gpv = 'NA';
            $balance_personal_pv = 'NA';            
            }
            $details ['referal_count'] = $referal_count;
            $details ['personal_pv'] = $personal_pv;
            $details ['group_pv'] = $group_pv;
            $details ['current_rank'] = $current_rank;
            $details ['next_rank'] = $next_ran;
            $details ['balance_referal_count'] = $balance_referal_count;
            $details ['balance_pers_pv'] = $balance_personal_pv;
            $details ['balance_grp_pv'] = $balance_gpv;
            $details ['next_referal_count'] = $next_referal_count;
            $details ['next_pers_pv'] = $next_pers_pv;
            $details ['next_grp_pv'] = $next_grp_pv;
            
            $json_response['status'] = true;
            $json_response['message'] = lang('details_fetched');
            $json_response['data'] = $details;
            echo json_encode($json_response);
            exit();
        }
    }

    public function enter_new_repurchase_address() {
        $user_id = $this->LOG_USER_ID;
        $name = $this->security->xss_clean(trim($this->input->post('name')));
        $address = $this->security->xss_clean(trim($this->input->post('address')));
        $pin = $this->security->xss_clean(trim($this->input->post('pin')));
        $town = $this->security->xss_clean(trim($this->input->post('town')));
        $mobile = $this->security->xss_clean(trim($this->input->post('mobile')));
        $make_default = $this->security->xss_clean(trim($this->input->post('make_default')));
        if ($name == '' || $name == NULL || $address == '' || $address == NULL || $pin == '' || $pin == NULL || $town == '' || $town == NULL || $mobile == '' || $mobile == NULL)
            $this->json_response(FALSE, lang('partial_content'));
        if ($this->Api_model->insert_repurchase_address($user_id, $name, $address, $pin, $town, $mobile, $make_default))
            $this->json_response(TRUE, lang('addrs_add'), $this->Api_model->getUserPurchaseAddress($user_id));
        $this->json_response(FALSE, lang('failed'));
    }

    public function delete_address() {
        $user_id = $this->LOG_USER_ID;
        $address_id = $this->security->xss_clean(trim($this->input->post('address')));
        if ($address_id == '' || $address_id == NULL)
            $this->json_response(FALSE, lang('partial_content'));
        if ($this->Api_model->delete_address($user_id, $address_id))
            $this->json_response(TRUE, lang('addrs_del'), $this->Api_model->getUserPurchaseAddress($user_id));
        $this->json_response(FALSE, lang('failed'));
    }

    public function get_checkout_details() {
        $this->load->model('repurchase_model');
        $this->load->model('Register_model');
        $user_id = $this->LOG_USER_ID;
        $data['user_addresses'] = $this->Api_model->getUserPurchaseAddress($user_id);
        $this->json_response(TRUE, lang('details_fetched'), $data);
    }

    public function get_package_details() {
        $is_loggin = $this->LOG_USER_ID;
               
        if (!$is_loggin) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_login_details');
            echo json_encode($json_response);
            exit();
        } else {
            $offset = $this->security->xss_clean(trim($this->input->post('offset')));
            $limit = $this->security->xss_clean(trim($this->input->post('limit')));
            if ($offset == '' || $offset == NULL || $limit == '' || $limit == NULL) {
                $this->json_response(FALSE, lang('partial_content'));
            }
                
            $package_details = $this->Api_model->getAllRepurchaseProducts($offset, $limit);
            $this->json_response(TRUE, lang('details_fetched'), $package_details);
        }     
    }

    function repurchase() {
        $user_id = $this->LOG_USER_ID;
        $this->load->model('repurchase_model');
        $this->load->model('register_model');
        $count = $this->security->xss_clean(trim($this->input->post('count')));
        $mode = $this->security->xss_clean(trim($this->input->post('paymode')));
        $address_id = $this->security->xss_clean(trim($this->input->post('address_id')));
        
        if ($count == 0 || $address_id == '' || $address_id == NULL) {
            $this->json_response(FALSE, lang('partial_content'));
        }
        $product_array = array();
        $total_amount = 0;
        for ($i = 0; $i < $count; $i++) {
            $product['id'] = $this->security->xss_clean(trim($this->input->post("product_$i")));
            $product['qty'] = $this->security->xss_clean(trim($this->input->post("quantity_$i")));
            $product['price'] = $this->Api_model->get_package_cost($product['id']);
            $total_amount = $total_amount + $product['price'] * $product['qty'];
            array_push($product_array, $product);
        }
        $purchase['payment_type'] = 'free_purchase';
        $purchase['total_amount'] = $total_amount;
        $purchase['user_id'] = $user_id;
        $purchase['order_address_id'] = $address_id;
        if ($mode != 'free') {
            $used_amount = $purchase['total_amount'];
            $ewallet_user = $this->security->xss_clean(trim($this->input->post('ewallet_user')));
            $ewallet_trans_password = $this->security->xss_clean(trim($this->input->post('transaction_password')));
            if ($ewallet_user == '' || $ewallet_user == NULL || $ewallet_trans_password == '' || $ewallet_trans_password == NULL)
                $this->json_response(FALSE, lang('partial_content'));

            if (strtolower($ewallet_user) != strtolower($this->LOG_USER_NAME)) {
                $this->json_response(FALSE, lang('partial_content'));
            }
            $ewallet_user_id = $this->validation_model->userNameToID($ewallet_user);

            $user_available = $this->validation_model->isUserAvailable($ewallet_user_id);
            if (!$user_available)
                $this->json_response(FALSE, lang('inv_user'));
            $trans_pass_available = $this->register_model->checkEwalletPassword($ewallet_user_id, $ewallet_trans_password);
            if ($trans_pass_available != 'yes')
                $this->json_response(FALSE, lang('invalid_transaction_password'));
            $ewallet_balance_amount = $this->register_model->getBalanceAmount($ewallet_user_id);
            if ($ewallet_balance_amount < $used_amount)
                $this->json_response(FALSE, lang('low_bal'));
            $is_ewallet_ok = true;
            $purchase['by_using'] = 'ewallet';
            $user_id = $purchase['user_id'];
            $used_user_id = $this->validation_model->userNameToID($ewallet_user);
            $transaction_id = $this->repurchase_model->getUniqueTransactionId();
            $res1 = $this->register_model->insertUsedEwallet($used_user_id, $user_id, $used_amount,$transaction_id,FALSE, "repurchase");
            $res2 = $this->register_model->deductFromBalanceAmount($used_user_id, $used_amount);
            if (!($res1 && $res2))
                $this->json_response(FALSE, lang('error'));
            $purchase['payment_type'] = 'ewallet';
        }
        if (count($product_array) == 0) {
            $this->json_response(FALSE, lang('partial_content'));
        }
        $orders_details = $this->repurchase_model->insertIntoRepuchaseOrder($purchase);
        $orders_id = $orders_details['order_id'];
        if ($orders_id) {
            foreach ($product_array as $value) {
                $value['status'] = "confirmed";
                $this->repurchase_model->insertRepurchaseOrderDetails($value, $orders_id);
            }

            $this->repurchase_model->updateUserPv($product_array, $purchase, $this->MODULE_STATUS);
            if (!empty($orders_details)) {

                $module_status = $this->MODULE_STATUS;
                $rank_status = $module_status['rank_status'];
                if ($rank_status == "yes") {
                    $this->load->model('rank_model');
                    $this->rank_model->updateUplineRank($purchase['user_id']);
                }

                $this->repurchase_model->commit();
                $enc_order_id = $this->validation_model->encrypt($orders_details['order_id']);
            }

            $this->json_response(TRUE, lang('success'), $orders_details);
        }
        $this->json_response(FALSE, lang('failed'));
    }

    function check_transaction_password() {
        $this->load->model('register_model');
        $ewallet_user_id = $this->LOG_USER_ID;
        $ewallet_user = $this->security->xss_clean(trim($this->input->post('ewallet_user')));
        if(strcasecmp($ewallet_user,$this->LOG_USER_NAME))
            $this->json_response(FALSE, lang('inv_user'));
        $ewallet_trans_password = $this->security->xss_clean(trim($this->input->post('transaction_password')));
        $trans_pass_available = $this->register_model->checkEwalletPassword($ewallet_user_id, $ewallet_trans_password);
        if ($trans_pass_available != 'yes')
            $this->json_response(FALSE, lang('invalid_transaction_password'));
        $this->json_response(TRUE, lang('success'));
    }
    
    function order_history(){
        $is_loggin = $this->LOG_USER_ID;
        $this->load->model('order_model');
        $arr = array();
        $numrows = $result_per_page = 0;
        if (!$is_loggin) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
        } else {
            $base_url = base_url() . 'user/order/order_history';
            $config = $this->pagination->customize_style();
            $config['base_url'] = $base_url;
            $config['per_page'] = $this->input->post('limit');
            if ($this->input->post('offset') != "")
                $page = $this->input->post('offset');
            else
                $page = 0;
            $customer_id = $this->validation_model->getOcCustomerId($this->LOG_USER_ID);
            $json_response['status'] = true;
            $json_response['message'] = lang('success');
            $json_response['data'] = $this->order_model->getOrderDetails($page, $config['per_page'], $customer_id);
        }

        echo json_encode($json_response);
        exit();
    }
    
    // transaction password  send mail
    public function send_transaction_password_post() {
        $this->form_validation->set_rules('captcha', lang('captcha'), 'required');
        $validate_form = $this->form_validation->run();
        if($validate_form){
                $captcha = $this->session->userdata('inf_captcha');
                if ((empty($captcha) || trim(strtolower($_REQUEST['captcha'])) != $captcha)) {
                    $this->set_error_response(422, 1040);
                }
            $this->load->model('tran_pass_model');
            $user_id = $this->LOG_USER_ID;
            if (!$user_id) {
                $this->set_error_response(422, 1011);
            } else {
                $e_mail = $this->validation_model->getUserEmailId($user_id);
                $check_result = $this->validation_model->checkEmail($user_id, $e_mail);
                if ($check_result) {
                    $this->tran_pass_model->sendEmail($user_id, $e_mail);
                    $json_response['message'] = lang('your_request_has_been_accepted_we_will_send_you_confirmation_mail_please_follow_that_instruction');
                    $this->set_success_response(200, $json_response);
                } else {
                    $this->set_error_response(422, 1011);
                }
            }
        }
        else
        {
            $this->set_error_response(422, 1004);
        }
    }
    // end of transaction password  send mail
    
    function my_tickets() {
      
        $user_id = $this->LOG_USER_ID;
        $arr = array();
        $numrows = $result_per_page = 0;
        if (!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
        } else {  
            $base_url = base_url() . 'user/ticket/my_tickets';
            $config = $this->pagination->customize_style();
            $config['base_url'] = $base_url;
            $config['per_page'] = $this->input->post('limit');
            if ($this->input->post('offset') != "")
                $page = $this->input->post('offset');
            else
                $page = 0;

            $json_response['status'] = true;
            $json_response['message'] = lang('success');
            $my_ticket =  $this->Api_model->getMyTicketData('', $user_id, "",$config["per_page"]);
            $json_response['data'] = $my_ticket;

        }

        echo json_encode($json_response);
        exit();

    }
    
    function view_ticket() {

        $is_loggin = $this->LOG_USER_ID;
        $ticket_id = $this->security->xss_clean(trim($this->input->post('ticket_id')));
       
        $ticket_reply = [];
        if (!$is_loggin) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
        } else {
            if($ticket_id == NULL){
                $json_response['status'] = false;
                $json_response['message'] = lang('partial_content');  
            }else{

                $data = $this->Api_model->getMyTicketData($ticket_id, $this->LOG_USER_ID);
                if($data){
                // if($data['0']['status'] == 0){  
                //  $data['0']['status']=   lang('new');            
                //  } else if($data['0']['status'] == 4){
                //  $data['0']['status']=   lang('in_progress');
                //  } else if($data['0']['status'] == 1) {
                //   $data['0']['status']=   lang('waiting_reply');
                //  } else if($data['0']['status'] == 2) {
                //   $data['0']['status']=   lang('replied');
                //  } else if($data['0']['status'] == 5) {
                //  $data['0']['status']=   lang('on_hold');
                //  }else {
                //   $data['0']['status']=   lang('resolved');
                //  }
                $ticket_reply = $this->Ticket_system_model->getAllReply($data['0']['id']);
                $json_response['status'] = true;
                $json_response['message'] = lang('success');
                $json_response['data'] =  $data;
                $json_response['reply'] =  $ticket_reply;
                }else{
                $json_response['status'] = false;
                $json_response['message'] = "Please enter a valid ticket ID";  
                }                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
        }
        echo json_encode($json_response);
        exit();
        }
    }
    
    function get_create_ticket_data() {
        $is_loggin = $this->LOG_USER_ID;
       
        if (!$is_loggin) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
            echo json_encode($json_response);
            exit();
        } else {           
            $priority = $this->Ticket_system_model->getAllTicketPrioritys();
            $category = $this->Ticket_system_model->getTicketCategorie();
            $status = $this->Ticket_system_model->getTicketStatusApi();
            $json_response['priority'] = $priority;
            $json_response['category'] = $category;
            $json_response['status_data'] = $status;
            $json_response['status'] = true;
            echo json_encode($json_response);          
        }
    }
    
    function create_ticket() {
        
        $is_loggin = $this->LOG_USER_ID;
       
        if (!$is_loggin) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
        } else {
            $ticket['trackid'] = $this->Ticket_system_model->createTicketId();

            $user_id = $this->LOG_USER_ID;

            $subject = $this->security->xss_clean(trim($this->input->post('subject')));
            $message= $this->security->xss_clean(trim($this->input->post('message')));
            $category = $this->security->xss_clean(trim($this->input->post('category_id')));
            $priority= $this->security->xss_clean(trim($this->input->post('priority_id')));

            if ($subject == "") {
                $json_response['status'] = false;
                $json_response['message'] = "You must enter a subject";
                echo json_encode($json_response);
                exit(); 
            } else if($message == "") {
                $json_response['status'] = false;
                $json_response['message'] = "You must enter a message";
                echo json_encode($json_response);
                exit(); 
            } else if($category == "") {
                $json_response['status'] = false;
                $json_response['message'] = "You must select a category";
                echo json_encode($json_response);
                exit(); 
            } else if($priority == "") {
                $json_response['status'] = false;
                $json_response['message'] = "You must select a priority";
                echo json_encode($json_response);
                exit(); 
            } else if(!$this->Api_model->chekCategoryExists($category)) {
                $json_response['status'] = false;
                $json_response['message'] = "You must select a valid category";
                echo json_encode($json_response);
                exit(); 
            } else if(!$this->Api_model->checkPriorityExists($priority)) {
                $json_response['status'] = false;
                $json_response['message'] = "You must select a valid priority";
                echo json_encode($json_response);
                exit(); 
            }
            
            $document1 = isset($_FILES['image']['name']) ? $_FILES['image']['name'] : "";

            $file_details['saved_name'] = "";
            $doc_file_name = "";
            
            if($document1) {
                $status = false;
                $upload_config = $this->validation_model->getUploadConfig();
                $upload_count = $this->validation_model->getUploadCount($this->LOG_USER_ID);
                if ($upload_count >= $upload_config) {
                    $msg = lang('you_have_reached_max_upload_limit');
                    $json_response = array(
                        'status' => false,
                        'data' => array(),
                        'message' => $msg
                    );
                    echo json_encode($json_response);
                    exit();
                }
                $upload_doc = 'image';
                $random_number = floor($this->LOG_USER_ID * rand(1000, 9999));
                $config1['file_name'] = "doc_" . $random_number;
                $config1['upload_path'] = IMG_DIR . 'ticket_system';
                $config1['allowed_types'] = 'jpg|png|jpeg|JPG|gif';
                $config1['max_size'] = '2048';
                $config['max_width'] = '1024';
                $config['max_height'] = '1024';
                
                $this->load->library('upload', $config1);
                $this->upload->initialize($config1);
                if ($this->upload->do_upload($upload_doc)) {
                    $status = true;
                    $file_arr = $this->upload->data();
                    $file_details['original_name'] = $file_arr['orig_name'];
                    $file_details['saved_name'] = $file_arr['file_name'];
                    $file_details['file_size'] = $file_arr['file_size'];
                    $doc_file_name = $file_arr['file_name'];
                    $this->validation_model->updateUploadCount($this->LOG_USER_ID);                       
                } else {
                    $msg = $this->upload->display_errors();
                    $json_response = array(
                        'status' => false,
                        'data' => array(),
                        'message' => $msg
                    );
                    echo json_encode($json_response);
                    exit();
                }   
            } else {
                $status= true;
            }
            if ($status) { 
                $ticket['file_name'] = $doc_file_name;
                $ticket['subject'] = $subject;
                $ticket['user_id'] = $user_id;
                $ticket['message'] = $message;
                $ticket['category'] = $category;
                $ticket['priority'] = $priority;
                $new_ticket = $this->Ticket_system_model->createNewTicket($ticket);

                if($new_ticket){
                    $details = $this->Ticket_system_model->getMyTicketData($ticket['trackid'], $this->LOG_USER_ID);
                }

                if ($document1) {
                    $this->Ticket_system_model->insertIntoAttachment($ticket['trackid'], $file_details, $doc_file_name);
                } else {
                    $doc_file_name = "";
                }
                
                $reply_ticket = $this->Ticket_system_model->replyTicket($details, $ticket['message'], $ticket['user_id'], $doc_file_name);
                
                if ($new_ticket && $details && $reply_ticket ) {

                    $data_array = array();
                    $data_array['mail_subject'] = $subject;
                    $data_array['mail_body'] = $message;
                    $data = serialize($data_array);
                    $this->validation_model->insertUserActivity($user_id, 'ticket created', $this->ADMIN_USER_ID, $data);
                    $details = $this->Ticket_system_model->insertToHistory($ticket['trackid'], $this->LOG_USER_ID, $this->LOG_USER_TYPE, "ticket created");
                }   

                $msg = lang('ticket_created') . ": " . $ticket['trackid'];
                $json_response['message'] = $msg;
                $json_response['status'] = true;
            } else {
                $json_response['status'] = false;
                $json_response['message'] = lang('error');
            }       
        }
        echo json_encode($json_response);
        exit();
    }
    
    
    function serach_ticket(){
        $is_loggin = $this->LOG_USER_ID;
          
        if (!$is_loggin) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
        } else {
         
            $category_id = $this->security->xss_clean(trim($this->input->post('category_id')));
            $priority_id = $this->security->xss_clean(trim($this->input->post('priority_id')));
            $status_id= $this->security->xss_clean(trim($this->input->post('status_id')));
            
            if ($category_id == "" && $priority_id == "" && $status_id == "") {
                $json_response['message'] = lang('please_select_search_criteria');
                $json_response['searched_tickets']=[''];
                echo json_encode($json_response);
                exit();

            }   
            $logged_id= $this->LOG_USER_ID;

            $searched_tickets = $this->Ticket_system_model->getApiSearchedTicketData($logged_id, $status_id, $category_id, $priority_id);
            $json_response['searched_tickets'] =  $searched_tickets;
            $json_response['message'] = lang('success');
            $json_response['status'] = true;
            echo json_encode($json_response);

        }
    }
    
    function serach_data(){
        
        $is_loggin = $this->LOG_USER_ID;
          
        if (!$is_loggin) {
          
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
        } else {
            $priority = $this->Ticket_system_model->getAllTicketPrioritys();
            $category = $this->Ticket_system_model->getTicketCategorie();
            $status = $this->Ticket_system_model->getTicketStatusApi();
            $json_response['priority'] = $priority;
            $json_response['category'] = $category;
            $json_response['status_data'] = $status;
            $json_response['status'] = true;
            echo json_encode($json_response);
        }
    }
        
    function resolved_ticket(){
        $this->load->model('api_model');
        
        $is_loggin = $this->LOG_USER_ID;
          
        if (!$is_loggin) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
        } else {
            $resolved_status = $this->Ticket_system_model->getTicketStatusIdBasedOnStatus("Resolved");
            $resolved_tickets = $this->api_model->getTicketDetails('', $this->LOG_USER_ID, $resolved_status);
            if($resolved_tickets) {
                $json_response['resolved_tickets'] =  $resolved_tickets;
                $json_response['status'] = true;
                $json_response['message'] = lang('success');
                echo json_encode($json_response);
            } else {
                $json_response['status'] = false;
                $json_response['message'] = lang('No_Resolved_Tickets_Found');  
                echo json_encode($json_response);
            }
       
        }
    }
    
    public function get_spec_package_details() {//To get details of package to upgrade
        $user_id = $this->LOG_USER_ID;
        
        if (!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
            echo json_encode($json_response);
            exit();
        } else {
            $this->load->model('upgrade_model');
            $product_id = $this->security->xss_clean(trim($this->input->post('product_id'))); 
            if(!is_numeric($product_id)) {
                $json_response['status'] = false;
                $json_response['message'] = "Invalid product";
                echo json_encode($json_response);
                exit();
            }
            $package_details = $this->upgrade_model->getPackageDetails($product_id); 
            if(empty($package_details)) {
                $json_response['status'] = false;
                $json_response['message'] = "Invalid product";
                echo json_encode($json_response);
                exit();
            }
 
            $this->load->model('product_model');           
            $package_id = $this->product_model->getProductPackageId($product_id, $this->MODULE_STATUS, 'registration');           
            $current_package_id = $this->validation_model->getProductId($user_id);
            $current_package_amount = $this->product_model->getProduct($current_package_id);
            $package_amount = $this->product_model->getProduct($package_id);
            $payment_amount = $package_amount - $current_package_amount;
            $package_details['amount_payable'] = $payment_amount;
          
            $json_response['status'] = true;
            $json_response['message'] = lang('success');
            $json_response['packge_details'] = $package_details;
            echo json_encode($json_response);
            exit();
        }        
    }
    
    public function get_pkg_upgrade_details() {
        $user_id = $this->LOG_USER_ID;
        
        if (!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
            echo json_encode($json_response);
            exit();
        } else {
            $this->load->model('upgrade_model');
            $current_package_details = $this->upgrade_model->getMembershipPackageDetails($user_id);  
            $upgradable_package_list = $this->upgrade_model->getUpgradablePackageList($current_package_details);
                       
            $json_response['status'] = true;
            $json_response['message'] = lang('success');
            $json_response['data']['current_package_details'] = $current_package_details;
            $json_response['data']['upgradable_package_list'] = $upgradable_package_list;
            echo json_encode($json_response);
            exit();
        }        
    }
    
    public function submit_package_upgrade() {
        $this->load->model('upgrade_model');
        $this->load->model('home_model');
        $user_id = $this->LOG_USER_ID;
        if(!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
            echo json_encode($json_response);
            exit();
        } else {
            $module_status = $this->MODULE_STATUS;

            $post_data = $this->input->post(null, true);
            $post_data = $this->validation_model->stripTagsPostArray($post_data);

            $product_id = $post_data['product_id'];
            
            if(!is_numeric($product_id)) {
                $json_response['status'] = false;
                $json_response['message'] = "Invalid product";
                echo json_encode($json_response);
                exit();
            }
            
            $package_id = $this->product_model->getProductPackageId($product_id, $module_status, 'registration');
            if (!$package_id) {
                $json_response['status'] = false;
                $json_response['message'] = "Invalid product";
                echo json_encode($json_response);
                exit();
            }
            
            $current_package_id = $this->validation_model->getProductId($user_id);
            $is_upgradable_package = $this->upgrade_model->isUpgradablePackage($current_package_id, $package_id);
            if (!$is_upgradable_package) {
                $json_response['status'] = false;
                $json_response['message'] = "You don't have any higher packages to upgrade";
                echo json_encode($json_response);
                exit();
            }
            $current_package_amount = $this->product_model->getProduct($current_package_id);
            $package_amount = $this->product_model->getProduct($package_id);
            $payment_amount = $package_amount - $current_package_amount;
            
            $payment_status = $this->upgrade_model->getPaymentStatus($this->MODULE_STATUS);
            $payment_type = $post_data['payment_method'];
            
            $this->inf_model->begin();

            $payment_res = false;
            
            if($payment_type == "ewallet" && $payment_status['ewallet']) {
                $ewallet_username = $logged_username = $this->LOG_USER_NAME;
                $transaction_password = $post_data['transaction_password'];
                
                $ewallet_user_id = $this->validation_model->userNameToID($ewallet_username);
                $ewallet_status = $this->ewallet_model->validateEwalletDetails($ewallet_username, $transaction_password, $payment_amount, $logged_username);
                
                if ($ewallet_status == 'invalid' || $ewallet_status == '') {
                    $json_response['status'] = false;
                    $json_response['message'] = lang('invalid_ewallet_details');
                    echo json_encode($json_response);
                    exit();
                } elseif ($ewallet_status == 'low_balance') {
                    $json_response['status'] = false;
                    $json_response['message'] = lang('low_ewallet_balance');
                    echo json_encode($json_response);
                    exit();
                } elseif ($ewallet_status != 'yes') {
                    $json_response['status'] = false;
                    $json_response['message'] = lang('invalid_ewallet_details');
                    echo json_encode($json_response);
                    exit();
                } 
                $payment_res = $this->ewallet_model->ewalletPayment($ewallet_user_id, $user_id, $payment_amount, 'upgrade');
            } else if($payment_type == "epin" && $payment_status['epin']) {
                $upgrade_user_id = $user_id;
                
                $epin_count = $post_data['epin_count'];
                
                for($i = 0; $i < $epin_count; $i++) {
                    if(isset($post_data["epin$i"])) {
                        $epin_details[] = $post_data["epin$i"];
                    } else {
                        $json_response['status'] = false;
                        $json_response['message'] = "Input data error";
                        echo json_encode($json_response);
                        exit();
                    }                    
                }
                
                $epin_details = array_unique(array_map('strtoupper', array_filter($epin_details, 'strlen')));
                $pin_array = $this->epin_model->validateAllEpins($epin_details, $payment_amount, $user_id, $upgrade_user_id);
                if ($pin_array['valid']) {
                    if ($pin_array['amount_reached'] > 0) {
                        $json_response['status'] = false;
                        $json_response['message'] = lang('low_epin_amount');
                        echo json_encode($json_response);
                        exit();
                    }
                } else {
                    $json_response['status'] = false;
                    $json_response['message'] = lang('invalid_epin_details');
                    echo json_encode($json_response);
                    exit();
                }
                $payment_res = $this->epin_model->epinPayment($pin_array, $user_id);
            } else if ($payment_type == "free_upgrade" && $payment_status['free_upgrade']) {
                $payment_res = true;
            } else {
                $json_response['status'] = false;
                $json_response['message'] = "You must select a valid payment method";
                echo json_encode($json_response);
                exit();
            }
            
            $upgrade_res = false;
            
            if ($payment_res) {
                $upgrade_res = $this->upgrade_model->upgradeMembershipPackage($user_id, $current_package_id, $product_id, $package_id, $payment_amount, $payment_type, $user_id, $module_status);
                $package_name = $this->home_model->getPackageNameFromPackageId($package_id, $module_status);
                $data = serialize($post_data);                
                $user_name = $this->validation_model->getUserName($user_id);
                $this->validation_model->insertUserActivity($user_id, $user_name . '`s package upgraded to ' . $package_name . ' through ' . lang($payment_type), $user_id, $data);
            }

            if ($upgrade_res) {
                $this->inf_model->commit();
                $json_response['status'] = true;
                $json_response['message'] = "Package upgraded successfully";
                echo json_encode($json_response);
                exit();
            } else {
                $this->inf_model->rollback();
                $json_response['status'] = false;
                $json_response['message'] = "Package upgrade failed";
                echo json_encode($json_response);
                exit();
            }
        }
    }
    
    public function check_epin_payment()
    {
        $user_id = $this->LOG_USER_ID;
        if(!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
            echo json_encode($json_response);
            exit();
        } else {
            $epin = $this->input->post('epin', true);
            $total_amount = $this->input->post('payment_amount', true);
            $upgrade_user_id = $user_id;
            $pin_info = $this->Api_model->validateEpin($epin, $total_amount, $user_id, $upgrade_user_id);
            
            if(!$pin_info['valid']) {
                $json_response['status'] = false;
                $json_response['message'] = lang('invalid_e_pin');
                echo json_encode($json_response);
                exit();
            }
            
            $json_response['status'] = true;
            $json_response['message'] = "success";
            $json_response['data'] = $pin_info;
            echo json_encode($json_response);
            exit();
        }
        
    }
    
    public function get_kyc_categories() {
        $user_id = $this->LOG_USER_ID;
        if(!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
            echo json_encode($json_response);
            exit();
        } else {
            $kyc_catg = $this->configuration_model->getKycDocCategory();
            $json_response['status'] = true;
            $json_response['message'] = "success";
            $json_response['data'] = $kyc_catg;
            echo json_encode($json_response);
            exit();
        }
        
    }
    
    public function upload_kyc() {
        $this->load->model('profile_model');
        $user_id = $this->LOG_USER_ID;
        if(!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
            echo json_encode($json_response);
            exit();
        }
        
        $catg = $this->input->post('category', true);      

        if(!$this->Api_model->checkKycCategoryExists($catg)) {
            $json_response['status'] = false;
            $json_response['message'] = "Invalid category";
            echo json_encode($json_response);
            exit();
        }

       if ($this->profile_model->checkKycDocs($user_id, $catg)) {
            $json_response['status'] = false;
            $json_response['message'] = "This type of KYC already exists";
            echo json_encode($json_response);
            exit();
        }
 /*
        $file_count = $this->input->post('kyc_count', true); 

        if ($file_count > 0) {
            if($file_count > 2) {
                $json_response['status'] = false;
                $json_response['message'] = "You can upload maximum two KYC documents at a time";
                echo json_encode($json_response);
                exit();
            }
                        
            $upload_config = $this->validation_model->getUploadConfig();
            $upload_count  = $this->validation_model->getUploadCount($user_id);
            if ($upload_count >= $upload_config) {
                $json_response['status'] = false;
                $json_response['message'] = "You have reached the maximum upload limit";
                echo json_encode($json_response);
                exit();
            }
        } else {
            $json_response['status'] = false;
            $json_response['message'] = "You haven't selected any documents";
            echo json_encode($json_response);
            exit();
        }
 
        $user_name = $this->validation_model->getUserName($user_id);
        
        $error = "";
        $success_count  = 0;
        $insert_array   = [];
        $upload_path = IMG_DIR . "/document/kyc/";

        for($i = 0; $i < $file_count; $i++) {
            if(!isset($_FILES["id_proof$i"]['name'])) {
                break;
            }

            $ext = pathinfo($_FILES["id_proof$i"]['name'], PATHINFO_EXTENSION);            
            $config1['file_name'] = $user_name . "_" . time() . $i . '.' . $ext;
            $config1['upload_path'] = $upload_path;
            $config1['allowed_types'] = 'pdf|jpeg|jpg|png';
            $config1['max_size'] = '5120000';

            $this->load->library('upload', $config1);
            $this->upload->initialize($config1);
            if ($this->upload->do_upload("id_proof$i")) {               
                $file_arr = $this->upload->data();
                $insert_array[] = $file_arr['file_name'];   
                $success_count++;
            } else {
                $error = $this->upload->display_errors();
                break;
            } 
        }

        if ($file_count != $success_count) {
            foreach ($insert_array as $value) {
                if (file_exists($upload_path . $value)) {
                    unlink($upload_path . $value);                        
                }
            }
            $json_response['status'] = false;
            $json_response['message'] = "Error on KYC upload " . ". $error";
            echo json_encode($json_response);
            exit();
        }
        if (count($insert_array)) {
            $this->profile_model->InsertIdentityProof($insert_array, $user_id, $catg);
            $json_response['status'] = true;
            $json_response['message'] = "KYC uploaded successfully";
            echo json_encode($json_response);
            exit();
        }*/
    }
    
    public function get_uploaded_kycs() {
        $user_id = $this->LOG_USER_ID;
        if(!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
            echo json_encode($json_response);
            exit();
        } else {
            $this->load->model('profile_model');
            $id_proof = $this->profile_model->getMyKycDoc($user_id);
            $json_response['status'] = true;
            $json_response['message'] = "success";
            $json_response['data'] = $id_proof;
            echo json_encode($json_response);
            exit();
        }
    }
    
    public function delete_kyc() {
        $user_id = $this->LOG_USER_ID;
        if(!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = lang('inv_user');
            echo json_encode($json_response);
            exit();
        } else {
            $this->load->model('profile_model');
            $id = $this->input->post('kyc_id'); 
            $result = $this->profile_model->deletetKyc($id, $user_id);
            if($result) {
                $json_response['status'] = true;
                $json_response['message'] = "Deleted successfully";
                echo json_encode($json_response);
                exit();
            }
            
            $json_response['status'] = false;
            $json_response['message'] = "Deletion failed";
            echo json_encode($json_response);
            exit();
        }
        
    }
}
?>
