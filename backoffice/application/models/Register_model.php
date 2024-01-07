<?php

class register_model extends inf_model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('validation_model');
        $this->load->model('product_model');
        $this->load->model('configuration_model');
        $this->load->model('registersubmit_model');
        $this->load->model('mail_model');

        if (!$this->LOG_USER_ID) {
            $this->MLM_PLAN = ucfirst($this->validation_model->getMLMPlan());
        }
        if ($this->MLM_PLAN == 'Hyip' || $this->MLM_PLAN == 'X-Up') {
            $this->load->model('Unilevel_model', 'plan_model');
        } else {
            $this->load->model($this->MLM_PLAN . '_model', 'plan_model');
        }
    }

    public function confirmRegister($regr, $module_status, $pending_signup_status = false, $email_verifcation_status = false)
    {   
        if ($pending_signup_status || $email_verifcation_status == 'yes') {

            if (!$pending_signup_status) {

                $res = $this->addPendingRegistration($regr['payment_type'], $regr, 'email_verified');
                $regr['user_name_entry'] = $res['user_name'];
                $type = 'registration_email_verification';
                $this->mail_model->sendAllEmails($type, $regr);
                return $res;
            } else if ($regr['by_using'] != 'opencart') {

                $res = $this->addPendingRegistration($regr['payment_type'], $regr);
                return $res;
            }
        }

        $msg = ['user' => '', 'pwd' => '', 'id' => '', 'status' => false, 'tran' => ''];

        $sponsor_id = $regr['sponsor_id'];
        $reg_from_tree = $regr['reg_from_tree'];
        $mlm_plan = $module_status['mlm_plan'];
        if (!in_array($mlm_plan, ['Binary', 'Matrix'])) {
            $reg_from_tree = false;
        }
        $regr['product_status'] = $module_status['product_status'];
        $position = $regr['position'];

        //USER PLACEMENT SECTION STARTS//
        $placement_details = $this->plan_model->getPlacementAndPosition($sponsor_id, $position, $reg_from_tree);
        if ($placement_details) {
            $regr['placement_id'] = $placement_details['id'];
            $regr['position'] = $placement_details['position'];
        } else {
            if (!$reg_from_tree) {
                $msg['error'] = "Unexpected error occured. Please conatct Admin";
                return $msg;
            }
        }
        //USER PLACEMENT SECTION ENDS//

        if ($regr['user_name_type'] == 'dynamic') {
            $regr['username'] = $this->registersubmit_model->getUsername();
        } else {
            $regr['username'] = $regr['user_name_entry'];
        }

        if ($this->validation_model->isUserNameAvailable($regr['username'])) {
            if ($regr['by_using'] === "opencart") {
                $msg['error'] = "Username Not Available";
            } else {
                $msg['error'] = $this->lang->line('user_name_not_available');
            }
            return $msg;
        }
        if (!$this->validation_model->isLegAvailable($regr['placement_id'], $regr['position'], true)) {
            if ($regr['by_using'] === "opencart") {
                $msg['error'] = "Leg Not Available";
            } else {
                $msg['error'] = $this->lang->line('user_already_registered');
            }
            return $msg;
        }
        if ($module_status['product_status'] == 'yes') {
            if (!$this->product_model->isProductAvailable($regr['product_id'], 'registration')) {
                if ($regr['by_using'] === "opencart") {
                    $msg['error'] = "Product Not Available";
                } else {
                    $msg['error'] = $this->lang->line('product_not_available');
                }
                return $msg;
            }
        }

        if ($reg_from_tree) {
            $this->load->model('tree_model');
            $placement_id = $regr['placement_id'];
            $res = $this->tree_model->IsPlacementUnderSponsor($placement_id, $sponsor_id);
            if (!$res) {
                $msg['error'] = lang('invalid_placement');
                return $msg;
            }
        }

        $regr['package_id'] = $this->product_model->getProductPackageId($regr['product_id'], $module_status, 'registration');

        $stsflag = true;
        try {
            $reg_status = $this->registersubmit_model->registerUser($regr, $module_status);

            $db_error = $this->db->error();
            if (!empty($db_error) && isset($db_error['code']) && $db_error['code'] != 0) {
                throw new Exception('Database error! Error Code [' . $db_error['code'] . '] Error: ' . $db_error['message']);
            }
        } catch (\Exception $e) {
            $stsflag = false;
            $this->confirmRegister($regr, $module_status, $pending_signup_status, $email_verifcation_status);
        }
        
        if($stsflag == false) {
            $msg['status'] = false;
        }

        if (isset($reg_status['status']) && $reg_status['status']) {
            $user_id = $regr['userid'] = $reg_status['user_id'];
            $regr['tran_password'] = $reg_status['transaction_password'];

            $msg['user_name'] = $msg['user'] = $regr['username'];
            $msg['password'] = $msg['pwd'] = $regr['pswd'];
            $msg['user_id'] = $msg['id'] = $user_id;
            $msg['user_id_encrypt'] = $msg['encr_id'] = $this->getEncrypt($user_id);
            $msg['transaction_password'] = $msg['tran'] = $regr['tran_password'];
            $msg['status'] = true;


            //TREE PATH INFORMATION

            // $this->insertLegPositionInformation($user_id, $regr['placement_id']);
            $this->storeTreePathInformation($user_id, $regr);
            //TREE PATH INFORMATION END
            // PLAN SPECIFIC FUNCTION
            $this->plan_model->addBySpecificPlan($user_id, $regr['sponsor_id']);

            // PLAN SPECIFIC FUNCTION ENDS

            $rank_status = $module_status['rank_status'];
            $product_status = $module_status['product_status'];
            $referal_status = $module_status['referal_status'];
            $basic_demo_status = $module_status['basic_demo_status'];
            $balance_amount = 0;

            $product_id = 0;
            $product_amount = $regr['reg_amount'];
            $product_pv = $regr['reg_amount']; //if there is no product, level commissions are based on the registration fee
            if ($product_status == "yes") {
                $product_id = $regr['product_id'];
                $product_details = $this->product_model->getProductAmountAndPV($product_id);
                $product_pv = $product_details['pair_value'];
                $product_amount = $product_details['product_value'];
                $regr['product_name'] = $product_details['product_value'];
                $regr['product_pv'] = $product_pv;
            }

            $oc_order_id = $regr['order_id'] ?? 0;

            if ($mlm_plan == 'Matrix') {
                $upline_id = $regr['sponsor_id'];
            } else {
                $upline_id = $regr['placement_id'];
            }
            $data = [
                'username' => $regr['username'],
                'sponsor_id' => $regr['sponsor_id'],
                'product_amount' => $regr['product_amount']
            ];

            $action = 'register';
            //CALCULATION SECTION STARTS//

            $this->plan_model->runCalculation($action, $user_id, $product_id, $product_pv, $product_amount, $oc_order_id, $upline_id, 0, $position, $data);

            //CALCULATION SECTION ENDS//

            $this->load->model('calculation_model');

            // Fast start bonus
            $this->calculation_model->calculateFastStartBonus($regr['sponsor_id']);

            // Rank commission
            if ($rank_status == 'yes') {

                $this->load->model('rank_model');
                $obj_arr = $this->configuration_model->getRankConfiguration();

                if ($obj_arr['default_rank_id'] != 0) {
                    $this->rank_model->updateDefaultRank($user_id, $obj_arr['default_rank_id']);
                }

                $this->rank_model->updateUplineRank($user_id);
            }

            // Referral commission
            $referal_commission_status = $this->validation_model->getCompensationConfig(['referal_commission_status']);
           
            if ($referal_commission_status == "yes") {
                $this->calculation_model->calculateReferralCommission($regr['sponsor_id'], $user_id, $product_pv);
            }
            $type = 'registration';
            $this->mail_model->sendAllEmails($type, $regr);

            // send sms
            if ($this->MODULE_STATUS["sms_status"] == "yes" && DEMO_STATUS == "no") {
                $this->load->model("sms_model");
                $mobile = $this->validation_model->getUserPhoneNumber($user_id);
                $variableArray = [
                    "fullname" => $this->validation_model->getUserFullName($user_id),
                    "company_name" => $this->COMPANY_NAME,
                    "link" => SITE_URL
                ];
                $langId = $this->validation_model->getUserDefaultLanguage($user_id);
                $this->sms_model->createAndSendSMS($langId, 'registration', $mobile, $variableArray);
            }
            // end::send sms

            if (ANDROID_APP_STATUS == 'yes') {
                $data = array("message" => lang('registration_completed_successfully'));
                $api_key = $this->getIndividualApiId($regr['sponsor_id']);
                $this->validation_model->sendGoogleCloudMessage($data, $api_key);
            }
        }
        
        $pck_typee = $this->validation_model->getPckProdType($regr['product_id']);
        
        if($pck_typee == "founder_pack"){
        
            if($msg['status']){
                
                if(!isset($regr['secondary_reg1'])){
                    
                    $regr1 = [];
                    $regr1 = $regr;
                    $regr1['product_id'] = 1;  
                    $regr1['sponsor_id'] = $this->validation_model->userNameToID($regr['user_name_entry']);
                    
                    $product_details = $this->getProductDetails($regr1['product_id']);
                    $product_details2 = $this->product_model->getProductAmountAndPV($regr1['product_id']);
                    
                    
                    $regr1['registration_fee'] = $this->getRegisterAmount();
                    $regr1['product_amount'] = $product_details['amount'];
                    $regr1['total_reg_amount'] = $this->getRegisterAmount();
                    $regr1['total_reg_amount1'] = $this->getRegisterAmount();
                    $regr1['sponsor_user_name'] = $regr['user_name_entry'];
                    $regr1['sponsor_full_name'] = $this->validation_model->getUserFullName($regr1['sponsor_id']);
                    $regr1['placement_user_name'] = $regr['user_name_entry'];
                    $regr1['placement_full_name'] = $this->validation_model->getUserFullName($regr1['sponsor_id']);
                    $regr1['position'] = 'L'; 
                    if($regr['downline_user_position']=="R"){
                        $regr1['position'] = 'R';
                    }
                    
                    $regr1['user_name_entry'] = $regr['user_name_child1'];
                    $regr1['pswd'] = $regr['pswd_child1']; 
                    $regr1['cpswd'] = $regr['cpswd_child1']; 
                    $regr1['reg_amount'] = $this->getRegisterAmount();
                    $regr1['product_name'] = $product_details2['product_value']; 
                    $regr1['product_pv'] = $product_details2['pair_value']; 
                    $regr1['total_amount'] = (float)$regr1['reg_amount'] + (float)$regr1['product_amount'];
                    $regr1['placement_id'] = $regr1['sponsor_id'];
                    $regr1['username'] = $regr['user_name_child1'];
                    $regr1['package_id'] = $this->product_model->getProductPackageId($regr1['product_id'], $module_status, 'registration');
                    $regr1['secondary_reg'] = TRUE;
                    
                    
                    $msg1 = $this->confirmRegister($regr1, $module_status, $pending_signup_status, $email_verifcation_status);
		     //$msg1['test']=$regr1;
		    // return $msg1;

                    if($msg1['status']){
                        $user_id1 = $regr1['userid'] = $msg1['user_id'];
                        if ($product_status == "yes") {
                            
                            $insert_into_sales = $this->insertIntoSalesOrder($user_id1, $regr1['product_id'], $regr1['by_using'], $pending_signup_status, $email_verifcation_status);
                            
                            $product_id = $regr1['product_id'];
                            $product_details = $this->product_model->getProductAmountAndPV($product_id);
                            $product_pv1 = $product_details['pair_value'];
                            $product_amount = $product_details['product_value'];
                            $regr1['product_name'] = $product_details['product_value'];
                            $regr1['product_pv'] = $product_pv1;
                        }
                         //Referral commission
                        // $referal_commission_status = $this->validation_model->getCompensationConfig(['referal_commission_status']);
                        
                       
                        // if ($referal_commission_status == "yes") {

                        //     $direct_commission1=$this->calculation_model->calculateReferralCommission($regr1['sponsor_id'], $user_id1, $product_pv1);
                        // }
                        $regr2 = [];
                        $regr2 = $regr;
                        $regr2['product_id'] = 1; 
                        $regr2['sponsor_id'] = $this->validation_model->userNameToID($regr['user_name_entry']);
                        $regr2['position'] = 'R';
                        if($regr['downline_user_position']=="L"){
                            $regr2['position'] = 'L';
                            // $regr2['sponsor_id']=$this->validation_model->userNameToID($regr['user_name_child1']);
                            $regr2['placement_id']=$this->validation_model->userNameToID($regr['user_name_child1']);
                        }elseif($regr['downline_user_position']=="R"){
                            $regr2['position'] = 'R';
                            // $regr2['sponsor_id']=$this->validation_model->userNameToID($regr['user_name_child1']);
                            $regr2['placement_id']=$this->validation_model->userNameToID($regr['user_name_child1']);
                        }
                        $product_details = $this->getProductDetails($regr2['product_id']);
                        $product_details2 = $this->product_model->getProductAmountAndPV($regr2['product_id']);
                        
                        
                        $regr2['registration_fee'] = $this->getRegisterAmount();
                        $regr2['product_amount'] = $product_details['amount'];
                        $regr2['total_reg_amount'] = $this->getRegisterAmount();
                        $regr2['total_reg_amount1'] = $this->getRegisterAmount();
                        $regr2['sponsor_user_name'] = $regr['user_name_entry'];
                        $regr2['sponsor_full_name'] = $this->validation_model->getUserFullName($regr2['sponsor_id']);
                        $regr2['placement_user_name'] = $regr['user_name_child1'];
                        $regr2['placement_full_name'] = $this->validation_model->getUserFullName($regr2['placement_id']);

                        $regr2['user_name_entry'] = $regr['user_name_child2'];
                        $regr2['pswd'] = $regr['pswd_child2']; 
                        $regr2['cpswd'] = $regr['cpswd_child2']; 
                        $regr2['reg_amount'] = $this->getRegisterAmount();
                        $regr2['product_name'] = $product_details2['product_value']; 
                        $regr2['product_pv'] = $product_details2['pair_value']; 
                        $regr2['total_amount'] = (float)$regr2['reg_amount'] + (float)$regr2['product_amount'];
                        // $regr2['placement_id'] = $regr2['sponsor_id'];
                        $regr2['username'] = $regr2['user_name_entry'];
                        $regr2['package_id'] = $this->product_model->getProductPackageId($regr2['product_id'], $module_status, 'registration');
                        $regr2['secondary_reg'] = TRUE;

                        $msg2=$this->confirmRegister($regr2, $module_status, $pending_signup_status, $email_verifcation_status);
                        if($msg2['status']){

                            $user_id2 = $regr2['userid'] = $msg2['user_id'];
                            if ($product_status == "yes") {
                                
                                $insert_into_sales = $this->insertIntoSalesOrder($user_id2, $regr2['product_id'], $regr2['by_using'], $pending_signup_status, $email_verifcation_status);
                                
                                $product_id = $regr2['product_id'];
                                $product_details = $this->product_model->getProductAmountAndPV($product_id);
                                $product_pv2 = $product_details['pair_value'];
                                $product_amount = $product_details['product_value'];
                                $regr2['product_name'] = $product_details['product_value'];
                                $regr2['product_pv'] = $product_pv2;
                            }
                            //  //Referral commission
                            // $referal_commission_status = $this->validation_model->getCompensationConfig(['referal_commission_status']);
                           
                            // if ($referal_commission_status == "yes") {
                            //     $direct_commission2 = $this->calculation_model->calculateReferralCommission($regr2['sponsor_id'], $user_id2,$product_pv2);
                            // }
                            $msg['status1'] = TRUE;
                        }else{
                        $msg['status2'] = $msg2;
                        $msg['status'] = false;
                    }
                    
                    }else{
                    	$msg['status1'] = $msg1;
                        $msg['status'] = false;
                    }
                    if($msg['status']){
                    $referral_commission_data = array('0' => array('user_id' => $user_id1,'product_pv' => $product_pv1 , 'sponsor_id' => $regr1['sponsor_id']), '1' => array('user_id' => $user_id2,'product_pv' => $product_pv2 , 'sponsor_id' => $regr2['sponsor_id']));
                    $msg['referal_commission_data'] = $referral_commission_data;
                    // $referal_commission_status = $this->validation_model->getCompensationConfig(['referal_commission_status']);
                    
                    // if ($referal_commission_status == "yes") {
                    //     foreach ($referral_commission_data as $row) {
                    //         $direct_commission_ = $this->calculation_model->calculateReferralCommission($row['sponsor_id'], $row['user_id'],$row['product_pv']);
                    //     }   
                    // }
                    }
                
                }
                
            }
        

        
        
        
        }
        
        
            
        
        
        

        return $msg;
    }

    public function addPendingRegistration($payment_method, $details, $email_status = '')
    {
        $response = [];

        if ($details['user_name_type'] == 'dynamic') {
            $details['user_name_entry'] = $this->registersubmit_model->getUsername();
            if ($payment_method == 'bank_transfer') {

                $this->db->select_max('id');
                $this->db->from('payment_receipt');
                $query = $this->db->get();
                foreach ($query->result() as $row) {
                    $max_id = $row->id;
                }
                $this->db->set('user_name', $details['user_name_entry']);
                $this->db->where('id', $max_id);
                $result = $this->db->update('payment_receipt');
            }
        }

        $this->db->set('user_name', $details['user_name_entry']);
        $this->db->set('payment_method', $payment_method);
        if ($email_status == 'email_verified') {
            $this->db->set('status', 'email');
        }
        $this->db->set('data', json_encode($details));
        $response['status'] = $this->db->insert('pending_registration');
        $response['user_id'] = $response['id'] = $this->db->insert_id();
        $response['user_name'] = $details['user_name_entry'];

        return $response;
    }

    public function getUserRegistrationDetails($user_id)
    {
        $registration_details = array();
        $get_data = array(
            "user_id" => $user_id
        );
        $this->db->limit(1);
        $result = $this->db->get_where('infinite_user_registration_details', $get_data);
        foreach ($result->result_array() as $row) {
            $registration_details = $row;
        }
        return $registration_details;
    }

    public function viewProducts($product_id = '', $status = 'yes')
    {
        $type_of_package = "registration";
        $product_array = $this->product_model->getAllProducts($status, $type_of_package);
        $lang_product = $this->lang->line('select_product');
        $products = '<option value="">' . $lang_product . '</option>';
        for ($i = 0; $i < count($product_array); $i++) {
            $id = $product_array[$i]['product_id'];
            $product_name = $product_array[$i]['product_name'];
            $product_value = $product_array[$i]['product_value'];
            $options = "$product_name ( " . $this->DEFAULT_SYMBOL_LEFT . round($product_value * $this->DEFAULT_CURRENCY_VALUE, $this->PRECISION) . $this->DEFAULT_SYMBOL_RIGHT . " )";
            $selected = '';
            if ($id == $product_id) {
                $selected = 'selected';
            }
            $products .= '<option value="' . $id . '"' . $selected . ' >' . $options . '</option>';
        }
        return $products;
    }

    public function isProductAdded()
    {

        $flag = 'no';

        $this->db->select('COUNT(*) AS cnt');
        $this->db->from('package');
        $query = $this->db->get();

        foreach ($query->result() as $row) {
            $count = $row->cnt;
        }

        if ($count > 0)
            $flag = 'yes';

        return $flag;
    }

    public function isPinAdded()
    {
        $flag = 'no';

        $this->db->select('COUNT(*) AS cnt');
        $this->db->from('pin_numbers');
        $query = $this->db->get();

        foreach ($query->result() as $row) {
            $count = $row->cnt;
        }

        if ($count > 0)
            $flag = 'yes';

        return $flag;
    }

    public function checkPassCode($prodcutpin, $prodcutid = "")
    {
        $prodcutpin = ($prodcutpin);
        if ($this->product_model->isProductPinAvailable($prodcutid, $prodcutpin))
            return $this->product_model->isPasscodeAvailable($prodcutpin);
    }

    public function checkSponser($sponser_full_name, $user_id)
    {
        $flag = false;
        $sponser_full_name = ($sponser_full_name);
        $sponser_user_name = ($user_id);

        $sponser_user_id = $this->validation_model->userNameToID($sponser_user_name);
        $sponser_full_name = $this->validation_model->getUserFullName($sponser_user_id);

        if ($sponser_user_id > 0) {

            $this->db->select('COUNT(*) AS cnt');
            $this->db->from('user_details');
            $this->db->where('user_detail_refid', $sponser_user_id);
            $this->db->where('user_detail_name', $sponser_full_name);
            $query = $this->db->get();
            foreach ($query->result() as $row) {
                $count = $row->cnt;
            }
            if ($count > 0) {
                $flag = true;
            }
        }
        return $flag;
    }

    public function checkLeg($sponserleg, $sponser_user_name)
    {
        $this->load->model('tree_model');
        $sponserid = $this->validation_model->userNameToID($sponser_user_name);
        if (!$sponserid) {
            return false;
        }
        return $this->validation_model->isLegAvailable($sponserid, $sponserleg);
    }

    public function checkUser($user_name)
    {
        $flag = true;
        if ($user_name == "") {
            $flag = false;
            return $flag;
        }

        $this->db->select('COUNT(*) AS cnt');
        $this->db->from('ft_individual');
        $this->db->where('user_name', $user_name);

        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $count = $row->cnt;
        }
        if ($count > 0) {
            $flag = false;
        }
        return $flag;
    }

    function getEncrypt($string)
    {
        $key = "EASY1055MLM!@#$";
        $result = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result .= $char;
        }
        return base64_encode($result);
    }

    public function isUserAvailable($user_name)
    {
        $this->db->select("COUNT(id) as count");
        $this->db->from("ft_individual");
        $this->db->where('user_name', $user_name);
        $this->db->where('active', 'yes');
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $count = $row->count;
        }

        return $count;
    }

    public function getTermsConditions($lang_id = '')
    {
        $terms_con = "";
        $this->db->select('terms_conditions');
        $this->db->from('terms_conditions');
        if ($lang_id != '')
            $this->db->where('lang_ref_id', $lang_id);
        $query = $this->db->get();
        $terms_con = $query->row('terms_conditions'); //->terms_conditions;
        return stripslashes($terms_con);
    }

    public function getUserDetails($uid)
    {
        $user_details = array();

        $this->db->select('*');
        $this->db->from('user_details');
        $this->db->where('user_detail_refid', $uid);
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $user_details = $row;
        }
        return $this->replaceNullFromArray($user_details, 'NA');
    }

    public function replaceNullFromArray($user_detail, $replace = '')
    {

        if ($replace == '') {
            $replace = "NA";
        }

        $len = count($user_detail);
        $key_up_arr = array_keys($user_detail);

        for ($i = 0; $i < $len; $i++) {

            $key_field = $key_up_arr[$i];
            if ($user_detail["$key_field"] == "") {
                $user_detail["$key_field"] = $replace;
            }
        }
        return $user_detail;
    }

    public function getProduct($product_id)
    {
        $MODULE_STATUS = $this->trackModule();
        if ($MODULE_STATUS['opencart_status'] != "yes" || $MODULE_STATUS['opencart_status_demo'] != "yes") {
            $this->db->select('*');
            $this->db->from('package');
            $this->db->where('product_id', $product_id);
            $query = $this->db->get();
        } else {
            $this->db->select("product_id,model as product_name,'yes' as active,date_added as date_of_insertion,package_id as prod_id,price as product_value,pair_value,0 as bv_value,0 as product_qty", false);
            $this->db->from("oc_product");
            $this->db->where('product_id', $product_id);
            $query = $this->db->get();
        }
        return $query->row_array();
    }

    public function getReferralName($user_id)
    {
        $user_detail_name = null;
        $this->db->select('user_detail_name,user_detail_second_name');
        $this->db->from('user_details');
        $this->db->where('user_detail_refid', $user_id);
        $query = $this->db->get();

        foreach ($query->result() as $row) {
            $user_detail_name = $row->user_detail_name;
            if ($row->user_detail_second_name != "NA")
                $user_detail_name .= " " . $row->user_detail_second_name;
        }
        return $user_detail_name;
    }

    public function checkMailStatus()
    {
        $status = null;
        $this->db->select('from_name');
        $this->db->select('reg_mail_status');
        $this->db->from('mail_settings');
        $this->db->where('id', 1);
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $status = $row;
        }
        return $status;
    }

    public function insertIntoSalesOrder($user_id, $product_id, $payment_method = "", $pending_status = false, $email_verification = 'no')
    {
        $date = date('Y-m-d H:i:s');
        $last_inserted_id = $this->getMaxSalesOrderId();
        $invoice_no = 1000 + $last_inserted_id;
        $product_details = $this->getProduct($product_id);
        $amount = $product_details['product_value'];
        $product_pv = $this->product_model->getProductPV($product_id);
        $pending_id = 'NULL';
        if ($pending_status || $email_verification == 'yes') {
            $pending_id = $user_id;
            $user_id = 'NULL';
        }

        $this->db->set('invoice_no', $invoice_no);
        $this->db->set('prod_id', $product_details['prod_id']);
        $this->db->set('user_id', $user_id, false);
        $this->db->set('amount', round($amount, 2));
        $this->db->set('product_pv', $product_pv);
        $this->db->set('date_submission', $date);
        $this->db->set('payment_method', $payment_method);
        $this->db->set('pending_id', $pending_id, false);
        $res = $this->db->insert('sales_order');

        if ($this->MODULE_STATUS['roi_status'] == 'yes' && ($this->MODULE_STATUS['opencart_status'] == "no" || $this->MODULE_STATUS['opencart_status_demo'] == "no")) {
            $this->db->set('prod_id', $product_details['prod_id']);
            $this->db->set('user_id', $user_id);
            $this->db->set('amount', round($amount, 2));
            $this->db->set('date_submission', $date);
            $this->db->set('payment_method', $payment_method);
            $this->db->set('pending_status', $pending_id);
            $this->db->set('roi', $product_details['roi']);
            $this->db->set('days', $product_details['days']);
            $res1 = $this->db->insert('roi_order');
        }
        return $res;
    }

    public function checkEPinValidity($epin, $sponsor_id)
    {
        $epin_arr = array();
        $session_data = $this->session->userdata('inf_logged_in');
        //$user_id = $session_data['user_id'];
        $admin_userid = $this->validation_model->getAdminId();

        $date = date('Y-m-d');
        $this->db->select('pin_numbers,pin_balance_amount');
        $this->db->from('pin_numbers');
        //$this->db->where('pin_numbers', $epin);
        $this->db->where("pin_numbers LIKE BINARY '$epin'", NULL, true);
        // $this->db->group_start();
        // $this->db->where('allocated_user_id', NULL);
        // $this->db->or_where('allocated_user_id', $sponsor_id);
        // if ($this->LOG_USER_TYPE == 'admin' || $this->LOG_USER_TYPE == 'employee') {
        //     // $this->db->or_where('allocated_user_id', $user_id); ??
        //     $this->db->or_where('allocated_user_id', $admin_userid);
        // } else {
        //     $this->db->or_where('allocated_user_id', $user_id);
        // }
        // $this->db->group_end();

        $this->db->where('pin_amount >', 0);
        $this->db->where('status', 'yes');
        $this->db->where('pin_expiry_date >=', $date);
        $res = $this->db->get();
        foreach ($res->result_array() as $row) {
            $epin_arr['pin_numbers'] = $row['pin_numbers'];
            $epin_arr['pin_amount'] = $row['pin_balance_amount'];
        }
        return $epin_arr;
    }

    public function UpdateUsedEpin($pin_det, $pin_count)
    {
        $user_id = $pin_det['user_id'];

        for ($i = 1; $i <= $pin_count; $i++) {
            $pin_no = $pin_det["$i"]['pin'];
            $pin_balnce = $pin_det["$i"]['balance_amount'];
            if ($pin_balnce == 0) {
                $this->db->set('status', "no");
            }
            $pin_balnce = round($pin_balnce / $this->DEFAULT_CURRENCY_VALUE, 2);
            $this->db->set('used_user', $user_id);
            $this->db->set('pin_balance_amount', round($pin_balnce, 2));
            $this->db->where('pin_numbers', $pin_no);
            $this->db->where('status', "yes");
            $result = $this->db->update('pin_numbers');
        }
        return $result;
    }

    public function UpdateUsedUserEpin($pin_det, $pin_count)
    {
        for ($i = 1; $i <= $pin_count; $i++) {
            $pin_no = $pin_det["$i"]['pin'];
            $pin_balnce = $pin_det["$i"]['balance_amount'];
            if ($pin_balnce == 0) {
                $this->db->set('status', "no");
            }
            $pin_balnce = round($pin_balnce / $this->DEFAULT_CURRENCY_VALUE, 2);
            $this->db->set('pin_balance_amount', round($pin_balnce, 2));
            $this->db->where('pin_numbers', $pin_no);
            $this->db->where('status', "yes");
            $result = $this->db->update('pin_numbers');
        }
        return $result;
    }

    public function insertUsedPin($epin_det, $pin_count, $pending_status = false, $type = 'register', $email_verification = 'no')
    {
        $user_id = $epin_det['user_id'];
        $date = date('Y-m-d H:m:s');
        $pending_id = 'NULL';
        if ($pending_status || $email_verification == 'yes') {
            $pending_id = $user_id;
            $user_id = 'NULL';
        }

        for ($i = 1; $i <= $pin_count; $i++) {
            $pin_no = $epin_det["$i"]['pin'];
            $pin_balnce = $epin_det["$i"]['balance_amount'];
            $pin_amount = $epin_det["$i"]['amount'];
            $status = "yes";
            if ($pin_balnce == 0) {
                $status = "no";
            }
            $this->db->set('status', $status);
            $this->db->set('pin_number', $pin_no);
            $this->db->set('used_user', $user_id, false);
            $this->db->set('pin_alloc_date', $date);
            $this->db->set('pending_id', $pending_id, false);
            $this->db->set('pin_amount', round($pin_amount, 2));
            $this->db->set('pin_balance_amount', round($pin_balnce, 2));
            $res = $this->db->insert('pin_used');
        }
        return $res;
    }

    public function getProductAmount($product_id)
    {
        $product_details = $this->product_model->getProductAmountAndPV($product_id);
        $product_amount = $product_details['product_value'];
        return $product_amount;
    }

    public function getBalanceAmount($user_id, $balance = '')
    {
        $user_balance = 0;
        $this->db->select('balance_amount');
        $this->db->select('user_id');
        $this->db->where('user_id', $user_id);
        if ($balance != '') {
            $this->db->where('balance_amount >', $balance);
        }
        $this->db->from('user_balance_amount');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $user_balance = $row['balance_amount'];
        }
        return $user_balance;
    }

    public function checkEwalletPassword($user_id, $password)
    {
        $flag = 'no';
        $this->db->select('tran_password');
        $this->db->where('user_id', $user_id);
        $this->db->limit(1);
        $query = $this->db->get('tran_password');
        $password_hash = $query->row_array()['tran_password'];
        $password_matched = password_verify($password, $password_hash);
        if ($password_hash && $password_matched) {
            $flag = 'yes';
        }
        return $flag;
    }

    public function insertUsedEwallet($user_ref_id, $user_id, $used_amount, $transaction_id, $pending_status = false, $amount_type = "registration", $email_verification = "no")
    {

        $date = date('Y-m-d H:i:s');
        $pending_id = 'NULL';
        if ($pending_status || $email_verification == 'yes') {
            $pending_id = 'NULL'; 
            $user_id = 'NULL';
        }
        $this->db->set('used_user_id', $user_ref_id);
        $this->db->set('used_amount', round($used_amount, 2));
        $this->db->set('user_id', $user_id, false);
        $this->db->set('used_for', $amount_type);
        $this->db->set('date', $date);
        $this->db->set('pending_id', $pending_id, false);
        $this->db->set('transaction_id', $transaction_id);
        $res = $this->db->insert('ewallet_payment_details');

        $ewallet_id = $this->db->insert_id();
        $this->validation_model->addEwalletHistory($user_ref_id, $user_id, $ewallet_id, 'ewallet_payment', $used_amount, $amount_type, 'debit', $transaction_id, '', 0, $pending_id);

        return $ewallet_id;
    }

    public function updateEwalletDetails($ewallet_id, $user_id,$pending_status=false,$email_verification="no") {
        $pending_id='NULL';
        if ($pending_status || $email_verification == 'yes') {
            $pending_id = $user_id;
            $user_id = 'NULL';
            //$this->db->set('user_id', $user_id)
            $this->db->set('pending_id', $pending_id, false)
            ->where('id', $ewallet_id)
            ->update('ewallet_payment_details');
        }else{
            $this->db->set('user_id', $user_id)
                ->where('id', $ewallet_id)
                ->update('ewallet_payment_details');
    
            $this->db->set('from_id', $user_id)
                ->where('ewallet_id', $ewallet_id)
                ->update('ewallet_history');
        }
    }

    public function updateUsedEwallet($ewallet_user, $ewallet_bal, $up_bal = '')
    {
        if ($up_bal == '') {
            $user_id = $this->validation_model->userNameToID($ewallet_user);
        } else {
            $user_id = $ewallet_user;
        }
        $this->db->set('balance_amount', round($ewallet_bal, 8));
        $this->db->where('user_id', $user_id);
        $res = $this->db->update('user_balance_amount');
        return $res;
    }

    public function getPaymentGatewayStatus($page = '') {
        if ($page != '') {
            $details['paypal_status'] = $this->getGatewayStatus('Paypal', $page);
            $details['creditcard_status'] = $this->getGatewayStatus('Creditcard', $page);
            $details['authorize_status'] = $this->getGatewayStatus('Authorize.net', $page);
            $details['bitcoin_status'] = $this->getGatewayStatus('Bitcoin', $page);
            $details['blockchain_status'] = $this->getGatewayStatus('Blockchain', $page);
            $details['bitgo_status'] = $this->getGatewayStatus('bitgo', $page);
            $details['payeer_status'] = $this->getGatewayStatus('Payeer', $page);
            $details['sofort_status'] = $this->getGatewayStatus('Sofort', $page);
            $details['squareup_status'] = $this->getGatewayStatus('SquareUp', $page);
            $details['epin_status'] = $this->getGatewayStatus('E-pin', $page);
            $details['ewallet_status'] = $this->getGatewayStatus('E-wallet', $page);
            $details['banktransfer_status'] = $this->getGatewayStatus('Bank Transfer', $page);
            $details['freejoin_status'] = $this->getGatewayStatus('Free Joining', $page);
            $details['stripe'] = $this->getGatewayStatus('Stripe', $page);
        } else {
            $details['paypal_status'] = $this->getGatewayStatus('Paypal');
            $details['creditcard_status'] = $this->getGatewayStatus('Creditcard');
            $details['authorize_status'] = $this->getGatewayStatus('Authorize.net');
            $details['bitcoin_status'] = $this->getGatewayStatus('Bitcoin');
            $details['blockchain_status'] = $this->getGatewayStatus('Blockchain');
            $details['bitgo_status'] = $this->getGatewayStatus('bitgo');
            $details['payeer_status'] = $this->getGatewayStatus('Payeer');
            $details['sofort_status'] = $this->getGatewayStatus('Sofort');
            $details['squareup_status'] = $this->getGatewayStatus('SquareUp');
            $details['epin_status'] = $this->getGatewayStatus('E-pin');
            $details['ewallet_status'] = $this->getGatewayStatus('E-wallet');
            $details['banktransfer_status'] = $this->getGatewayStatus('Bank Transfer');
            $details['freejoin_status'] = $this->getGatewayStatus('Free Joining');
        }
        return $details;
    }

    public function getGatewayStatus($gateway, $page = '') {
        $status = "no";
        $this->db->select('status');
        $this->db->like('gateway_name', $gateway);
        $this->db->from('payment_gateway_config');
        if ($page != '')
            $this->db->where($page, 1);
        $this->db->limit(1);
        $query = $this->db->get();

        foreach ($query->result() as $row) {
            $status = $row->status;
        }
        return $status;
    }

    public function getPaymentStatus($type) {
        return $this->db->select('status')
                ->from('payment_gateway_config')
                ->like('gateway_name', $type)
                ->limit(1)
                ->get()
                ->row('status');
    }

    public function getPaymentModuleStatus()
    {

        $details = array();
        $details['gateway_type'] = $this->getPaymentStatus('Payment Gateway');
        $details['epin_type'] = $this->getPaymentStatus('E-pin');
        $details['free_joining_type'] = $this->getPaymentStatus('Free Joining');
        $details['ewallet_type'] = $this->getPaymentStatus('E-wallet');
        $details['bank_transfer'] = $this->getPaymentStatus('Bank Transfer');
        return $details;
    }

    public function insertintoPaymentDetails($payment_details)
    {

        $data = array(
            'type' => $payment_details['payment_method'],
            'user_id' => $payment_details['user_id'],
            'acceptance' => $payment_details['acceptance'],
            'payer_id' => $payment_details['payer_id'],
            'order_id' => $payment_details['token_id'],
            'amount' => $payment_details['amount'],
            'currency' => $payment_details['currency'],
            'status' => $payment_details['status'],
            'card_number' => $payment_details['card_number'],
            'ED' => $payment_details['ED'],
            'card_holder_name' => $payment_details['card_holder_name'],
            'date_of_submission' => $payment_details['submit_date'],
            'pay_id' => $payment_details['pay_id'],
            'error_status' => $payment_details['error_status'],
            'brand' => $payment_details['brand']
        );
        $res = $this->db->insert('payment_registration_details', $data);
        return $res;
    }

    public function getRegisterAmount() {
        return $this->db->select('reg_amount')
            ->from('configuration')
            ->get()
            ->row('reg_amount');
    }

    public function getProductName($product_id)
    {
        return $this->product_model->getPrdocutName($product_id);
    }

    // public function generateOrderid($name, $type) {
    //     $order_id = null;
    //     $date = date('Y-m-d H:i:s');
    //     $this->db->set('firstname', $name);
    //     $this->db->set('status', $type);
    //     $this->db->set('date_added', $date);
    //     $res = $this->db->insert('epdq_payment_order');
    //     $order_id = $this->db->insert_id();
    //     return $order_id;
    // }
    public function getWidthCieling()
    {

        $obj_arr = $this->getSettings();
        $width_cieling = $obj_arr["width_ceiling"];
        return $width_cieling;
    }

    public function getSettings()
    {
        $obj_arr = array();
        $this->db->select("*");
        $this->db->from("configuration");
        $res = $this->db->get();
        foreach ($res->result_array() as $row) {
            $obj_arr["id"] = $row['id'];
            $obj_arr["tds"] = $row['tds'];
            $obj_arr["pair_price"] = $row['pair_price'];
            $obj_arr["pair_ceiling"] = $row['pair_ceiling'];
            $obj_arr["service_charge"] = $row['service_charge'];
            $obj_arr["product_point_value"] = $row['product_point_value'];
            $obj_arr["pair_value"] = $row['pair_value'];
            $obj_arr["startDate"] = $row['start_date'];
            $obj_arr["endDate"] = $row['end_date'];
            $obj_arr["sms_status"] = $row['sms_status'];
            $obj_arr["payout_release"] = $row['payout_release'];
            $obj_arr["referal_amount"] = $row['referal_amount'];
            $obj_arr["level_commission_type"] = $row['level_commission_type'];
            $obj_arr["pair_commission_type"] = $row['pair_commission_type'];
            $obj_arr["depth_ceiling"] = $row['depth_ceiling'];
            $obj_arr["width_ceiling"] = $row['width_ceiling'];
        }


        return $obj_arr;
    }

    public function getProdAndJoiningDetails($user_id)
    {

        $details = array();
        $this->db->select('*');
        $this->db->where('id', $user_id);
        $query = $this->db->get('ft_individual');

        foreach ($query->result() as $row) {

            $details['product_id'] = $row->product_id;
            $details['date_of_joining'] = date('Y-m-d', strtotime($row->date_of_joining));
        }

        return $details;
    }

    public function getTotalPurchase($user_name, $from_date = '', $to_date = '')
    {

        $amount = 0;
        $this->db->select('order_id');
        $this->db->where('sponsor', $user_name);
        if ($from_date != '') {
            $from_date = $from_date . " " . "00:00:00";
            $to_date = $to_date . ' ' . "23:59:59";
            $this->db->where("date_added >=", $from_date);
            $this->db->where("date_added <=", $to_date);
        } else {
            $this->db->like('date_added', date('Y-m'), 'after');
        }

        $query = $this->db->get('order');

        foreach ($query->result() as $row) {

            $order_id = $row->order_id;
            $amount += $this->getAmount($order_id);
        }

        return $amount;
    }

    public function getDownlineDetailsAll($id)
    {
        $arr1[] = $id;
        unset($this->referals);
        $this->referals = array();
        $arr = $this->getReferralCount($arr1, $i = 0);
        return $arr;
    }

    public function getReferralCount($user_id_arr, $i)
    {
        $temp_user_id_arr = array();
        $qr = $this->createQuerys($user_id_arr);
        $res = $this->selectData($qr, "Error On Selecting 157894512345");
        while ($row = mysql_fetch_array($res)) {
            $this->referals[$i] = $row['id'];
            $temp_user_id_arr[] = $row['id'];
            $i++;
        }
        if (count($temp_user_id_arr) > 0) {
            $this->getReferralCount($temp_user_id_arr, $i);
        }
        return $this->referals;
    }

    public function createQuerys($user_id_arr)
    {

        if ($this->table_prefix == "") {
            $_SESSION['table_prefix'] = '57_';
            $this->table_prefix = $_SESSION['table_prefix'];
        }
        $ft_individual = $this->table_prefix . "ft_individual";
        $arr_len = count($user_id_arr);
        if ($arr_len == 1)
            $where_qr = " father_id = '$user_id_arr[0]'";
        else {
            $where_qr = " father_id = '$user_id_arr[0]'";
            for ($i = 1; $i < $arr_len; $i++) {
                $user_id = $user_id_arr[$i];
                $where_qr .= " OR father_id = '$user_id'";
            }
        }


        //  if (count($this->referals) == 0)
        $qr = "Select id from $ft_individual where ($where_qr)";


        return $qr;
    }

    public function getClosedPartyId($user_id, $from_date = '', $to_date = '')
    {

        $i = 0;
        $details = array();
        $this->db->select('*');
        $this->db->where('added_by', $user_id);
        if ($from_date != '') {
            $from_date = $from_date . " " . "00:00:00";
            $to_date = $to_date . ' ' . "23:59:59";
            $this->db->where("from_date >=", $from_date);
            $this->db->where("from_date <=", $to_date);
        } else {
            $this->db->like('from_date', date('Y-m'), 'after');
        }

        $this->db->where('status', 'closed');
        $query = $this->db->get('party');

        foreach ($query->result() as $row) {

            $details[$i] = $row->id;
            $i++;
        }
        return $details;
    }

    public function totalProductAmountGetFromParty($party_id, $from_date = '', $to_date = '')
    {

        $amount = 0;
        $this->db->select_sum('total_amount');
        $this->db->where('party_id', $party_id);

        if ($from_date != '') {
            $from_date = $from_date . " " . "00:00:00";
            $to_date = $to_date . ' ' . "23:59:59";
            $this->db->where("date >=", $from_date);
            $this->db->where("date <=", $to_date);
        } else {
            $this->db->like('date', date('Y-m'), 'after');
        }

        $query = $this->db->get('party_guest_orders');

        foreach ($query->result() as $row) {

            $amount = $row->total_amount;
        }

        return $amount;
    }

    public function deductFromBalanceAmount($user_id, $total_amount)
    {
        $this->db->set('balance_amount', 'ROUND(balance_amount -' . $total_amount . ',8)', false);
        $this->db->where('user_id', $user_id);
        $this->db->limit(1);
        $res = $this->db->update('user_balance_amount');
        return $res;
    }

    public function checkAllEpins($pin_details, $product_id, $product_status = "no", $sponsor_id, $return_status = false)
    {
        $is_pin_ok = false;
        $pin_array = array();
        $reg_amount = $this->getRegisterAmount();
        $product_amount = 0;
        if ($product_status == "yes" && $product_id != "") {
            $product_details = $this->product_model->getProductDetails($product_id, 'yes');
            $product_amount = $product_details[0]['product_value'];
        }

        $total_reg_amount = $product_amount + $reg_amount;
        $total_reg_balance = $total_reg_amount;
        $arr_length = count($pin_details);

        if ($arr_length) {
            for ($i = 0; $i <= $arr_length; $i++) {
                if (isset($pin_details[$i])) {
                    $epin_value = ($pin_details[$i]['pin']);
                    $epin_details = $this->checkEPinValidity($epin_value, $sponsor_id);
                    if ($epin_details) {
                        $epin_amount = $epin_details['pin_amount'];
                        $epin_balance_amount = $epin_details['pin_amount'];
                        $epin_used_amount = $epin_details['pin_amount'];
                        if ($total_reg_balance) {
                            if ($epin_amount == $total_reg_balance) {
                                $epin_balance_amount = 0;
                                $total_reg_balance = 0;
                            } else {
                                if ($epin_amount > $total_reg_balance) {
                                    $epin_balance_amount = $epin_amount - $total_reg_balance;
                                    $epin_used_amount = $total_reg_balance;
                                    $total_reg_balance = 0;
                                } else {
                                    $epin_balance_amount = 0;
                                    $reg_balance = $total_reg_balance - $epin_amount;
                                    $total_reg_balance = ($reg_balance >= 0) ? $reg_balance : 0;
                                }
                            }
                            if ($total_reg_balance == 0) {
                                $is_pin_ok = true;
                            }
                        } else {
                            $epin_used_amount = 0;
                        }
                        $pin_array["$i"]['pin'] = $epin_details['pin_numbers'];
                        $pin_array["$i"]['amount'] = round($epin_amount * $this->DEFAULT_CURRENCY_VALUE, $this->PRECISION);
                        $pin_array["$i"]['balance_amount'] = round($epin_balance_amount * $this->DEFAULT_CURRENCY_VALUE, $this->PRECISION);
                        $pin_array["$i"]['reg_balance_amount'] = round($total_reg_balance * $this->DEFAULT_CURRENCY_VALUE, $this->PRECISION);
                        $pin_array["$i"]['epin_used_amount'] = round($epin_used_amount * $this->DEFAULT_CURRENCY_VALUE, $this->PRECISION);
                        $pin_array["$i"]['i'] = $pin_details[$i]['i'];
                        $pin_array["$i"]['product_amount'] = round($product_amount * $this->DEFAULT_CURRENCY_VALUE, $this->PRECISION);
                    } else {
                        $pin_array["$i"]['pin'] = 'nopin';
                        $pin_array["$i"]['amount'] = '0';
                        $pin_array["$i"]['balance_amount'] = '0';
                        $pin_array["$i"]['reg_balance_amount'] = '0';
                        $pin_array["$i"]['epin_used_amount'] = '0';
                        $pin_array["$i"]['i'] = '1';
                        $pin_array["$i"]['product_amount'] = round($product_amount * $this->DEFAULT_CURRENCY_VALUE, $this->PRECISION);
                    }
                }
            }
        } else {
            $pin_array["0"]['pin'] = 'nopin';
            $pin_array["0"]['amount'] = '0';
            $pin_array["0"]['balance_amount'] = '0';
            $pin_array["0"]['reg_balance_amount'] = '0';
            $pin_array["0"]['epin_used_amount'] = '0';
            $pin_array["0"]['product_amount'] = round($product_amount * $this->DEFAULT_CURRENCY_VALUE, $this->PRECISION);;
        }
        if ($return_status) {
            $pin_array['is_pin_ok'] = $is_pin_ok;
        }
        return $pin_array;
    }

    public function getUserSponsorTreeLevel($user_id, $from_id, $level = 0)
    {
        $this->db->select('sponsor_id');
        $this->db->where('id', $from_id);
        $this->db->limit(1);
        $query = $this->db->get('ft_individual');

        foreach ($query->result() as $row) {
            $father_id = $row->sponsor_id;
            $level++;
            if ($father_id && $father_id < $user_id) {
                $level = $this->getUserSponsorTreeLevel($user_id, $father_id, $level);
            }
        }

        return $level;
    }

    public function getMaxSalesOrderId()
    {
        $max_id = 0;
        $this->db->select_max('id');
        $this->db->from('sales_order');
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $max_id = $row->id;
        }
        return $max_id;
    }

    public function getDefaultData()
    {
        $default_user_data = array();
        $default_user_data['user_name_entry'] = "DEMO" . $this->getDynamicUserName();
        $default_user_data['position'] = "L";
        $default_user_data['product_id'] = "1";
        $default_user_data['first_name'] = "Your First Name";
        $default_user_data['last_name'] = "";
        $default_user_data['year'] = 1992;
        $default_user_data['month'] = 5;
        $default_user_data['day'] = 25;
        $default_user_data['date_of_birth'] = $default_user_data['year'] . '-' . $default_user_data['month'] . '-' . $default_user_data['day'];
        $default_user_data['active_tab'] = "free_join_tab";
        $default_user_data['free_joining_status'] = "yes";
        $default_user_data['epin_type'] = "no";
        $default_user_data['ewallet_type'] = "no";
        $default_user_data['gateway_type'] = "no";
        $default_user_data['paypal_status'] = "no";
        $default_user_data['authorize_status'] = "no";
        $default_user_data['email'] = "youremail@email.com";
        $default_user_data['mobile'] = 9999999999;
        $default_user_data['mobile_code'] = "+91";

        $default_user_data['gender'] = "M";
        $default_user_data['address'] = "Your Address";
        $default_user_data['country'] = "India";
        $default_user_data['country_id'] = 99;
        $default_user_data['state'] = "Kerala";
        $default_user_data['state_id'] = 1490;
        $default_user_data['city'] = "City";
        $default_user_data['land_line'] = "";
        return $default_user_data;
    }

    function getDynamicUserName()
    {
        $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $username = '';
        for ($i = 0; $i < 3; $i++)
            $username .= $chars[(mt_rand(0, (strlen($chars) - 1)))];
        return $username;
    }

    public function addToCustomerTables($user_id, $regr)
    {
        $dbprefix = $this->db->dbprefix;
        $this->db->set_dbprefix('');

        $first_name = $regr['first_name'];
        $last_name = $regr['last_name'];
        $email = $regr['email'];
        $phone = $regr['mobile'];
        $password = $regr['pswd'];
        $address1 = $regr['address'];
        $address2 = $regr['address_line2'];
        $city = $regr['city'];
        $pin = $regr['pin'];
        $join_date = $regr['joining_date'];
        $ip = $_SERVER['REMOTE_ADDR'];

        $customer = $this->db->dbprefix . "oc_customer";
        $this->db->set('customer_group_id', 1);
        $this->db->set('store_id', 0);
        $this->db->set('language_id', 1);
        $this->db->set('firstname', $first_name);
        $this->db->set('lastname', $last_name);
        $this->db->set('email', $email);
        $this->db->set('telephone', $phone);
        $this->db->set('fax', '');
        $this->db->set('password', $password);
        $this->db->set('salt', '');
        $this->db->set('cart', null);
        $this->db->set('wishlist', null);
        $this->db->set('newsletter', 0);
        $this->db->set('address_id', 1);
        $this->db->set('custom_field', '');
        $this->db->set('ip', $ip);
        $this->db->set('status', 1);
        $this->db->set('approved', 1);
        $this->db->set('safe', 0);
        $this->db->set('token', '');
        $this->db->set('code', '');
        $this->db->set('date_added', $join_date);
        $this->db->insert($customer);

        $customer_id = $this->db->insert_id();

        $address = $this->db->dbprefix . "oc_address";
        $this->db->set('customer_id', $customer_id);
        $this->db->set('firstname', $first_name);
        $this->db->set('lastname', $last_name);
        $this->db->set('company', '');
        $this->db->set('address_1', $address1);
        $this->db->set('address_2', $address2);
        $this->db->set('city', $city);
        $this->db->set('postcode', $pin);
        $this->db->set('country_id', 0);
        $this->db->set('zone_id', 0);
        $this->db->set('custom_field', '');
        $this->db->insert($address);

        $address_id = $this->db->insert_id();

        $this->db->set('address_id', $address_id);
        $this->db->where('customer_id', $customer_id);
        $this->db->update($customer);

        $ft_individual = $this->db->dbprefix . 'ft_individual';
        $this->db->set('oc_customer_ref_id', $customer_id);
        $this->db->where('id', $user_id);
        $this->db->update($ft_individual);

        $this->db->set_dbprefix($dbprefix);
    }

    function getIndividualApiId($user_id)
    {
        $this->db->select("api_key");
        $this->db->from("ft_individual");
        $this->db->where("id", $user_id);
        $query = $this->db->get();
        return $query->row()->api_key;
    }

    function getUserStatus($user_id)
    {
        $status = null;
        $this->db->select("active");
        $this->db->from("ft_individual");
        $this->db->where("id", $user_id);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $status = $row->active;
        }
        return $status;
    }

    public function getBitcoinSettings()
    {
        $settings = array();
        $query = $this->db->get('bitcoin_configuration');
        if ($query->num_rows() > 0) {
            $settings = $query->result_array()[0];
        }
        return $settings;
    }

    public function bitcoinHistory($user_id, $data, $purpose = '')
    {
        $date = date('Y-m-d H:i:s');
        $this->db->set('user_id', $user_id);
        $this->db->set('data', json_encode($data));
        $this->db->set('purpose', $purpose);
        $this->db->set('status', 'no');
        $this->db->set('date', $date);
        $result = $this->db->insert('bitcoin_history');
        return $this->db->insert_id();
    }

    public function insertInToBitcoinPaymentProcessDetails($regr_data, $reason, $registrer)
    {
        $this->db->set('registrer', $registrer);
        $this->db->set('user_name', $regr_data['user_name_entry']);
        $this->db->set('regr_data', json_encode($regr_data));
        $this->db->set('reason', $reason);
        $this->db->set('date', date('Y-m-d H:i:s'));
        $result = $this->db->insert('bitcoin_payment_process_details');
        return $result;
    }

    public function insertInToBitcoinPaymentDetails($bitcoin_id, $user_id, $purpose, $amount, $current_bitcoin_value, $paid_amount, $response_amount, $bitcoin_address, $transaction, $return_address, $pending_status = false)
    {
        $key = $this->config->item('encryption_key');
        $bitcoin_address_enc = $bitcoin_address;
        $pending_id = 'NULL';
        if ($pending_status) {
            $pending_id = $user_id;
            $user_id = 'NULL';
        }
        $this->db->set('bitcoin_history_id', $bitcoin_id);
        $this->db->set('user_id', $user_id, false);
        $this->db->set('purpose', $purpose);
        $this->db->set('amount', $amount);
        $this->db->set('bitcoin_rate', $current_bitcoin_value);
        $this->db->set('bitcoin_amount_to_be_paid', $paid_amount);
        $this->db->set('paid_bitcoin_amount', $response_amount);
        $this->db->set('bitcoin_address', $bitcoin_address_enc);
        $this->db->set('transaction', $transaction);
        $this->db->set('return_address', $return_address);
        $this->db->set('pending_id', $pending_id, false);
        $this->db->set('date', date('Y-m-d H:i:s'));
        $result = $this->db->insert('bitcoin_payment_details');
        return $result;
    }

    public function updateBitcoinHistory($user_id, $id, $status)
    {
        $this->db->set('user_id', $user_id);
        $this->db->set('status', $status);
        $this->db->where('id', $id);
        $result = $this->db->update('bitcoin_history');
        return $result;
    }

    public function getUserRegistrationDetailsForPreview($user_id, $user_name = '')
    {
        if ($user_id) {
            $this->db->select('sponsor_id,user_name,first_name,last_name,address,address_line2,state_name,country_name,mobile,email,reg_date,reg_amount,product_name,product_amount');
            $this->db->where('user_id', $user_id);
            $query = $this->db->get('infinite_user_registration_details');
            $details = $query->row_array();
        } else {
            $this->db->select('data');
            $this->db->where('user_name', $user_name);
            $this->db->where('status', 'pending');
            $query = $this->db->get('pending_registration');
            $details = json_decode($query->row_array()['data'], true);
            $details['user_name'] = $details['user_name_entry'];
            $details['reg_date'] = $details['joining_date'];
            $details['last_name'] = $details['last_name'] ?? null;
        }
        return $details;
    }

    public function getPendingRegistrations($page, $limit)
    {
        $this->load->model('country_state_model');
        $this->load->model('product_model');

        $this->db->select('id,user_name,data,status,payment_method,date_added');
        $this->db->where('status', 'pending');
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit, $page);
        $query = $this->db->get('pending_registration');
        $details = $query->result_array();
        foreach ($details as $k => $v) {
            if ($v['payment_method'] == 'bank_transfer') {
                $details[$k]['reciept'] = $this->getPaymentReciept($v['user_name']);
            }

            // $unserialized_data = unserialize($v['data']);
            $unserialized_data = json_decode($v['data'], true);
            unset($details[$k]['data']);
            $details[$k] = array_merge($details[$k], $unserialized_data);
            $details[$k]['sponsor_user_name'] = $this->validation_model->IdToUserName($unserialized_data['sponsor_id']);
            $details[$k]['sponsor_full_name'] = $this->validation_model->getUserFullName($unserialized_data['sponsor_id']);
            $details[$k]['package_name'] = $this->product_model->getPrdocutName($unserialized_data['product_id']);
            $details[$k]['last_name'] = $details[$k]['last_name'] ?? null;
            $details[$k]['first_name'] = $details[$k]['first_name'] ?? '';
        }
        return $details;
    }

    public function getPaymentReciept($username)
    {
        $reciept = '';
        $this->db->select('reciept_name');
        $this->db->where('user_name', $username);
        $this->db->where('type', 'register');
        $this->db->limit(1);
        $query = $this->db->get('payment_receipt');
        foreach ($query->result_array() as $row) {
            $reciept = $row['reciept_name'];
        }

        return $reciept;
    }

    public function getPendingRegistrationsCount()
    {
        $this->db->where('status', 'pending');
        return $this->db->count_all_results('pending_registration');
    }

    public function getPendingRegistrationDetailsByUsername($user_name)
    {
        $this->db->select('id,user_name,data,payment_method');
        $this->db->where('status', 'pending');
        $this->db->where('user_name', $user_name);
        $query = $this->db->get('pending_registration');
        $details = $query->row_array();
        $unserialized_data = json_decode($details['data'], true);
        unset($details['data']);
        $details['data'] = $unserialized_data;
        return $details;
    }

    public function updatePendingRegistration($id, $user_id, $user_name, $payment_method, $data)
    {
        $MODULE_STATUS = $this->trackModule();
        $res = true;
        switch ($payment_method) {
            case 'ewallet':
                $this->db->set('user_id', $user_id);
                $this->db->set('pending_id', 'NULL', false);
                $this->db->where('pending_id', $id);
                $this->db->where('user_id IS NULL');
                $res1 = $this->db->update('ewallet_payment_details');
                $this->db->set('from_id', $user_id);
                $this->db->set('pending_id', 'NULL', false);
                $this->db->where('ewallet_type', 'ewallet_payment');
                $this->db->where('from_id IS NULL');
                $this->db->where('pending_id', $id);
                $res2 = $this->db->update('ewallet_history');
                $res = $res1 && $res2;
                break;
            case 'epin':
                $this->db->set('pending_id', 'NULL', false);
                $this->db->set('used_user', $user_id);
                $this->db->where('pending_id', $id);
                $this->db->where('used_user IS NULL');
                $res = $this->db->update('pin_used');
                break;
            case 'authorize.net':
                $this->db->set('user_id', $user_id);
                $this->db->set('pending_id', 'NULL', false);
                $this->db->where('pending_id', $id);
                $this->db->where('user_id IS NULL');
                $res = $this->db->update('authorize_payment_details');
                break;
            case 'paypal':
                break;
            case 'bitcoin':
                $this->db->set('user_id', $user_id);
                $this->db->set('pending_id', 'NULL', false);
                $this->db->where('pending_id', $id);
                $this->db->where('user_id IS NULL');
                $res = $this->db->update('bitcoin_payment_details');
                break;
            case 'free_join':
                break;
            default:
                break;
        }
        $this->db->set('user_id', $user_id);
        $this->db->set('pending_id', 'NULL', false);
        $this->db->where('pending_id', $id);
        $this->db->where('user_id IS NULL');
        $res2 = $this->db->update('sales_order');

        if ($this->MODULE_STATUS['roi_status'] == 'yes' && ($this->MODULE_STATUS['opencart_status'] == "no" || $this->MODULE_STATUS['opencart_status_demo'] == "no")) {
            $this->db->set('user_id', $user_id);
            $this->db->set('pending_status', 0);
            $this->db->where('pending_status', $id);
            $this->db->where('user_id', 0);
            $this->db->update('roi_order');
        }

        if ($this->LOG_USER_TYPE == 'employee') {
            $this->db->set('user_id', $user_id);
            $this->db->set('pending_status', 0);
            $this->db->where('user_id', $id);
            $this->db->where('pending_status', 1);
            $res3 = $this->db->update('employee_activity');
        } else {
            $res3 = true;
        }
        $this->db->set('updated_id', $user_id);
        $this->db->set('status', 'approved');
        $this->db->set('date_modified', date('Y-m-d H:i:s'));
        $this->db->where('id', $id);
        $this->db->where('status', 'pending');
        $res4 = $this->db->update('pending_registration');

        return $res && $res2 && $res3 && $res4;
    }


    /*Blockchain Payment Method Starts*/
    public function getBlockchainInfo()
    {
        $query = $this->db->select('*')
            ->from('blockchain_config')
            ->get();
        foreach ($query->result_array() as $row) {
            $row['my_xpub'] = $this->encryption->decrypt($row['my_xpub']);
            $row['my_api_key'] = $this->encryption->decrypt($row['my_api_key']);
            $row['main_password'] = $this->encryption->decrypt($row['main_password']);
            $row['second_password'] = $this->encryption->decrypt($row['second_password']);
            return $row;
        }
    }

    public function getUnpaidAddressCount()
    {
        $count = $this->db->select('bitcoin_address')
            ->from('bitcoin_addresses')
            ->where('paid_status', 'no')
            ->count_all_results();
        return $count;
    }

    public function getUnpaidAddress()
    {
        $address = "";
        $query = $this->db->select('bitcoin_address')
            ->from('bitcoin_addresses')
            ->where('paid_status', 'no')
            ->where("TIMESTAMPDIFF(MINUTE,date,NOW()) > 30")
            ->order_by('id')
            ->limit(1)
            ->get();
        foreach ($query->result_array() as $row) {
            $address = $row['bitcoin_address'];
        }
        return $address;
    }

    public function getAvailableAddress()
    {
        $address = "";
        $query = $this->db->select('bitcoin_address')
            ->from('bitcoin_addresses')
            ->where('paid_status', 'no')
            ->where('current_status', 'no')
            ->order_by('date', 'desc')
            ->limit(1)
            ->get();
        foreach ($query->result_array() as $row) {
            $address = $row['bitcoin_address'];
        }
        return $address;
    }

    public function keepBitcoinAddress($address)
    {
        return $this->db->set('bitcoin_address', $address)
            ->set('date', date('Y-m-d H:i:s'))
            ->insert('bitcoin_addresses');
    }
    public function insertPaymentDetails($invoice_id, $address, $secret, $total_amount, $price_in_btc, $date, $regr, $used_for = "")
    {

        $this->db->set('invoice_id', $invoice_id)
            ->set('payment_address', $address)
            ->set('product_id', $regr['product_id'])
            ->set('secret', $secret)
            ->set('amount_to_pay', $total_amount)
            ->set('total_btc', $price_in_btc)
            ->set('date_added', $date)
            ->set('post_data', json_encode($regr))
            ->set('used_for', $used_for)
            ->insert('blockchain_history');

        return $this->db->insert_id();
    }

    public function updateCallbackError($invoice_id, $error)
    {
        $this->db->set('call_back_error', $error)
            ->where('invoice_id', $invoice_id)
            ->update('blockchain_history');
    }
    public function getTransaction($invoice_id, $transaction_hash)
    {
        $count = $this->db->select('*')
            ->from('blockchain_history')
            ->where('invoice_id', $invoice_id)
            ->where('transaction_hash', $transaction_hash)
            ->count_all_results();
        return $count;
    }

    public function updateTransaction($invoice_id, $transaction_hash, $confirmations)
    {
        $this->db->set('confirmations', $confirmations)
            ->where('invoice_id', $invoice_id)
            ->where('transaction_hash', $transaction_hash)
            ->update('blockchain_history');
    }

    public function addTransaction($invoice_id, $transaction_hash, $value, $confirmations, $response)
    {
        $this->db->set('confirmations', $confirmations)
            ->set('value', $value)
            ->set('transaction_hash', $transaction_hash)
            ->set('json_response', $response)
            ->where('invoice_id', $invoice_id)
            ->update('blockchain_history');
    }

    public function keepRowAddressReponse($address, $invoice_id, $response, $used_for)
    {
        $this->db->set('address', $address)
            ->set('invoice_id', $invoice_id)
            ->set('txn_hash', $response['hash160'])
            ->set('response', json_encode($response))
            ->set('date', date("Y-m-d H:i:s"))
            ->set('used_for', $used_for)
            ->insert('rawaddr_response');
    }

    public function updateBitcoinAddress($address, $status = "yes")
    {
        $this->db->set('paid_status', $status)
            ->where('bitcoin_address', $address)
            ->update('bitcoin_addresses');
    }

    public function getPaymentInfo($invoice_id)
    {
        $query = $this->db->select('*')
            ->from('blockchain_history')
            ->where('invoice_id', $invoice_id)
            ->get();
        foreach ($query->result_array() as $row) {
            return $row;
        }
    }
    /*Blockchain Payment Method Ends*/


    /*Bitgo Payment Method Starts*/
    public function getBitgoStatus()
    {
        $query = $this->db->select('mode')->where('gateway_name', 'bitgo')->get('payment_gateway_config');
        foreach ($query->result() as $row) {
            return $row->mode;
        }
    }

    public function getBitgoConfiguration($mode)
    {
        $query = $this->db->select()->where('mode', $mode)->get('bitgo_configuration')->result_array();

        return $query;
    }

    public function insertIntoBitGoPaymentHistory($user_id, $regr, $p_id, $send_amount, $pay_address, $address_result, $wallet_id, $type = 'backoffice_registration')
    {
        $data = array(
            'user_id' => $user_id,
            'regr' => $regr,
            'product_id' => $p_id,
            'send_amount' => $send_amount,
            'pay_address' => $pay_address,
            'address_result' => $address_result,
            'date' => date('Y-m-d H:i:s'),
            'type' => $type,
            'wallet_id' => $wallet_id
        );
        $this->db->insert('bitgo_payment_history', $data);
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }

    public function upateRecievedResult($h_id, $result_arry, $recieved_amount, $bitcoin_payment)
    {
        $this->db->set('recieved_result', $result_arry);
        $this->db->set('recieved_amount', $recieved_amount);
        $this->db->set('bitcoin_payment', $bitcoin_payment);
        $this->db->where('id', $h_id);
        $this->db->update('bitgo_payment_history');
    }

    public function upateBitGoStatus($h_id, $status)
    {
        $this->db->set('status', $status);
        $this->db->where('id', $h_id);
        $this->db->update('bitgo_payment_history');
    }
    /*Bitgo Payment Method Ends*/

    //    Bank Transfer Payment Method

    public function addReciept($user_name, $doc_file_name)
    {
        $date = date('Y-m-d H:i:s');
        $query = $this->db->select('*')->where('user_name', $user_name)->get('payment_receipt');
        $row_count = $query->num_rows();
        if ($row_count > 0) {
            $this->db->set('uploaded_date', $date);
            $this->db->set('reciept_name', $doc_file_name);
            $this->db->where('user_name', $user_name);
            $result = $this->db->update('payment_receipt');
        } else {
            $this->db->set('user_name', $user_name);
            $this->db->set('uploaded_date', $date);
            $this->db->set('reciept_name', $doc_file_name);
            $result = $this->db->insert('payment_receipt');
        }
        return $result;
    }

    public function isSponsornameExist($user_name)
    {
        $sponsorname = '';
        $user_id = $this->validation_model->userNameToID($user_name);
        if ($user_id != '') {
            $sponsorname = $this->register_model->getReferralName($user_id);
            $sponsorname = trim($sponsorname);
        }
        return $sponsorname;
    }
    public function updateAddressDate($address)
    {
        return $this->db->set('date', date('Y-m-d H:i:s'))
            ->where('bitcoin_address', $address)
            ->update('bitcoin_addresses');
    }

    public function insertIntoPayeerOrderHistory($payment_details)
    {
        $this->db->set('user_id', $payment_details['user_id']);
        $this->db->set('purpose', $payment_details['purpose']);
        $this->db->set('amount', $payment_details['amount']);
        $this->db->set('product_id', $payment_details['product_id']);
        $this->db->set('status', $payment_details['status']);
        $this->db->set('currency', $payment_details['currency']);
        $this->db->set('invoice_number', $payment_details['invoice_number']);
        $this->db->set('date', $payment_details['date']);
        $res = $this->db->insert('payeer_order_history');
        return $res;
    }

    public function getContactInfoFields()
    {
        $query = $this->db->select('*')
            ->from('signup_fields')
            ->where('status', 'yes')
            ->where('delete_status', 'yes')
            ->order_by('sort_order')
            ->get();
        $detail =  $query->result_array();

        foreach ($detail as $k => $v) {
            $field_name = $key_name = $v['field_name'];
            if (strpos($v['field_name'], 'custom_') !== false) {
                $field_name = $this->getCustomFieldDetails($v['field_name']);
            }
            $details[$k]['field_name'] = $field_name;
            $details[$k]['key_name'] = $key_name;
            $details[$k]['status'] = $v['status'];
            $details[$k]['required'] = $v['required'];
            $details[$k]['sort_order'] = $v['sort_order'];
        }
        return $details;
    }

    public function getRequiredStatus($field_name)
    {
        $query = $this->db->select('required')
            ->from('signup_fields')
            ->where('status', 'yes')
            ->where('field_name', $field_name)
            ->get();
        return $query->result_array()[0]['required'];
    }

    public function getSignUpAllFieldStatus()
    {

        $details = array();
        $details['first_name'] = $this->getSignUpFieldStatus('first_name');
        $details['last_name'] = $this->getSignUpFieldStatus('last_name');
        $details['mobile'] = $this->getSignUpFieldStatus('mobile');
        $details['email'] = $this->getSignUpFieldStatus('email');
        $details['date_of_birth'] = $this->getSignUpFieldStatus('date_of_birth');
        $details['gender'] = $this->getSignUpFieldStatus('gender');
        $details['adress_line1'] = $this->getSignUpFieldStatus('adress_line1');
        $details['adress_line2'] = $this->getSignUpFieldStatus('adress_line2');
        $details['country'] = $this->getSignUpFieldStatus('country');
        $details['state'] = $this->getSignUpFieldStatus('state');
        $details['city'] = $this->getSignUpFieldStatus('city');
        $details['pin'] = $this->getSignUpFieldStatus('pin');
        $details['land_line'] = $this->getSignUpFieldStatus('land_line');
        return $details;
    }

    public function getSignUpFieldStatus($type)
    {
        $status = '';
        $this->db->select('status');
        $this->db->where('field_name', $type);
        $this->db->from('signup_fields');
        $this->db->limit(1);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $status = $row->status;
        }
        return $status;
    }
    public function getApprovedDetailsOfRegistration()
    {
        $this->db->select('status');
        $this->db->from('pending_registration');
        $query = $this->db->get();
        $res = $query->result_array();
        $details = [];
        foreach ($res as  $key => $row) {
            $details[$key]['status'] = $row['status'];
        }
        return $details;
    }

    public function getSignupFields()
    {
        return $this->db->get('signup_fields')->result_array();
    }

    public function rejectPendingRegistration($user_name, $reject)
    {
        if ($user_name != "" && isset($reject)) {
            $this->db->set('status', 'rejected');
            $this->db->set('date_modified', date('Y-m-d H:i:s'));
            $this->db->where_in('user_name', $user_name);
            $this->db->where('status', 'pending');
            $res4 = $this->db->update('pending_registration');
        }
    }
    public function getEmailRegistrationDetailsByUsername($user_name)
    {

        $pending_array = array('pending', 'email');
        $this->db->select('id,user_name,data,payment_method');
        $this->db->where_in('status', $pending_array);
        $this->db->where('user_name', $user_name);
        $query = $this->db->get('pending_registration');
        $details = $query->row_array();
        $unserialized_data = json_decode($details['data'], true);
        unset($details['data']);
        $details['data'] = $unserialized_data;
        return $details;
    }

    public function getPaymentGatewayUsingRegistration($payment_category)
    {
        $this->db->select('gateway_name');
        $this->db->from('payment_gateway_config');
        $this->db->where('status', "yes");
        $this->db->where($payment_category, 1);
        if ($this->LOG_USER_TYPE != 'admin') {
            $this->db->where('admin_only !=', 1);
        }

        $this->db->order_by('sort_order', "asc");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function updateEmailVerificationStatus($user_name)
    {
        $this->db->set('email_verification_status', 'yes');
        $this->db->where('user_name', $user_name);
        $res = $this->db->update('pending_registration');
        return $res;
    }
    public function getUserEmailVerificationStatus($user_name)
    {

        $this->db->select('email_verification_status');
        $this->db->where('user_name', $user_name);
        $this->db->where('status', 'email');
        $res = $this->db->get('pending_registration');

        if ($res->num_rows() > 0) {

            return $res->result_array()[0]['email_verification_status'];
        } else {

            return 'yes';
        }
    }
    public function getMailidOfUser($user_name)
    {

        $mail_id = '';
        $this->db->select('data');
        $this->db->where('user_name', $user_name);
        $res = $this->db->get('pending_registration');

        $data = $res->result_array();

        if ($res->num_rows() > 0) {

            $data = (array)(json_decode($data[0]['data']));

            $mail_id = $data['email'];
        }
        return $mail_id;
    }
    public function isftUser($user_name)
    {

        $this->db->where('user_name', $user_name);
        $res = $this->db->count_all_results('ft_individual');

        return $res;
    }
    public function isvalidusername($user_name)
    {
        $this->db->where('user_name', $user_name);
        $res = $this->db->count_all_results('pending_registration');

        if ($res > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function getEmailRegistrationDetailsByUsernameinStore($user_name)
    {

        $pending_array = array('pending', 'email');
        $this->db->select('id,user_name,reg_data');
        $this->db->where_in('status', $pending_array);
        $this->db->where('user_name', $user_name);
        $query = $this->db->get('oc_temp_registration');

        $details = $query->row_array();
        $unserialized_data = unserialize(($details['reg_data']));
        $details['data'] = $unserialized_data['reg_data'];
        return $details;
    }
    public function getCustomFieldDetails($key)
    {

        $lang = '';
        if ($this->MODULE_STATUS['lang_status'] == 'yes') {
            $lang_code = $this->validation_model->get_language_code($this->LANG_NAME);
        }
        $this->db->select('field_name');
        $this->db->from('custom_fields');
        $this->db->where('key', $key);
        if ($this->MODULE_STATUS['lang_status'] == 'yes') {
            $this->db->where('lang', $lang_code);
        } else {
            $this->db->where('lang', 'en');
        }
        $this->db->limit(1);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $lang = $row->field_name;
        }
        return $lang;
    }
    public function storeTreePathInformation($new_user_id, $regr = [])
    {
        if(!$new_user_id) {
            return false;
        }
        $res = true;
        $treepath_ancestors = $this->getallUplines($regr['placement_id']);
        $treepath_rows = [['ancestor' => $new_user_id, 'descendant' => $new_user_id]];
        foreach ($treepath_ancestors as $treepath_ancestor) {
            if (!$treepath_ancestor) {
                continue;
            }
            $treepath_rows[] = [
                'ancestor' => $treepath_ancestor,
                'descendant' => $new_user_id
            ];
        }
        if(count($treepath_rows)) {
            $res = $this->db->insert_batch('treepath', $treepath_rows);
        }
        if(!$res) {
            return false;
        }
        $sponsor_treepath_ancestors = $this->getallSponsorWiseUplines($regr['sponsor_id']);
        $sponsor_treepath_rows= [['ancestor' => $new_user_id, 'descendant' => $new_user_id]];
        foreach ($sponsor_treepath_ancestors as $sponsor_treepath_ancestor) {
            if (!$sponsor_treepath_ancestor) {
                continue;
            }
            $sponsor_treepath_rows[] =[
                'ancestor' => $sponsor_treepath_ancestor,
                'descendant' => $new_user_id
            ];
        }
        if(count($sponsor_treepath_rows)) {
            $res = $this->db->insert_batch('sponsor_treepath', $sponsor_treepath_rows);
        }
        return $res;
    }
    public function getallUplines($placement_id)
    {
        $array = $this->db->select('ancestor')
                        ->where('descendant', $placement_id)
                        ->get('treepath')->result_array();
        return array_column($array, 'ancestor');
    }
    public function getallSponsorWiseUplines($sponsor_id)
    {
        $array = $this->db->select('ancestor')
                    ->where('descendant', $sponsor_id)
                    ->get('sponsor_treepath')->result_array();
        return array_column($array, 'ancestor');
    }
    public function insertLegPositionInformation($user_id, $father_id)
    {
        $childCount = $this->validation_model->getTotalChildcount($father_id);
        // update Leg Position
        $this->db->set('leg_position', ($childCount));
        $this->db->where('id', $user_id);
        $res = $this->db->update('ft_individual');
        return $res;
    }

    /**
     * [updateUserNameReceipt description]
     * @param  [type] $new_user_name [description]
     * @param  [type] $old_user_name [description]
     * @return [type]                [description]
     */
    public function updateUserNameReceipt($new_user_name, $old_user_name) {
        $this->db->set('user_name', $new_user_name)
            ->where('user_name', $old_user_name)
            ->update('payment_receipt');
        return $this->db->affected_rows() >= 1;
    }

    public function addToCwallet($regr='',$from_id='',$cwallet_type='',$cwallet_method='',$product_id=0)
    {
        $agent_id = $this->validation_model->getAgentIdByCountry($regr['country']);
        $agent_status = $this->validation_model->CheckAgentStatus($agent_id);
        $pck_typee = $this->validation_model->getPckProdType($product_id);
        

        // dd($agent_id);
        if($cwallet_type =='anm'){
            $wallet_id =1;
        }else if($cwallet_type == 'panda'){
            $wallet_id =2;
        }else if($cwallet_type == 'hajar'){
            $wallet_id =3;
        }else if($cwallet_type == 'raed'){
            $wallet_id =4;
        }else{
            if($agent_status == 'yes'){
                $wallet_id =5;
            }else{
                $wallet_id = '';
            }
        }
        //set wallet id null for giving commission for admin if the country agent is deactivated
        if($wallet_id !=''){
            $wallet_amount=$this->validation_model->get_Wallet_Amount($wallet_id);
            if($pck_typee == "founder_pack"){
                $wallet_amount=$wallet_amount*3;
            }
            $date=date("Y-m-d H:i:s");
            $user_id = $this->validation_model->userNameToID($regr['sponsor_user_name']);
            $this->db->set('user_id', $user_id);
            $this->db->set('from_id', $from_id);
            $this->db->set('wallet_type', $cwallet_type);
            $this->db->set('wallet_amount', $wallet_amount);
            $this->db->set('amount', $regr['total_amount']);
            // $this->db->set('agent_id', $agent_id);
            $this->db->set('date_added', $date);
    
            $result = $this->db->insert('cwallet_history');
    
            if ($result) {
                $cwallet_id = $this->db->insert_id();
            }
        }
        if($cwallet_type == 'agent' && $wallet_id !=''){
            $this->validation_model->updateAgentWallet($agent_id,$wallet_amount);
            $this->validation_model->addAgentwalletHistory($agent_id, $from_id, 0, 'commission', $wallet_amount, 'agent_commission', 'credit',0,'',0,0);
        }
        
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
    function getEwalletIdfromPending($id){
        $this->db->select('id');
        $this->db->where('pending_id',$id);
        $res=$this->db->get('ewallet_payment_details');
        return $res->row_array()['id'] ?? 0;
    }
public function confirmStripeRegistration($payment_history,$session_id)
    {
        /* Stripe User Registration Starts */

        $data = unserialize($payment_history->data); 

        $module_status = $this->MODULE_STATUS;
        $pending_signup_status = $this->configuration_model->getPendingSignupStatus($data['payment_type']);

        $this->register_model->begin();
        $status = $this->register_model->confirmRegister($data, $module_status, $pending_signup_status);

        if ($status['status']) {
            $user_name = $status['user_name'];
            $user_id = $status['user_id'];
            $transaction_password = $pending_signup_status ? '' : $status['transaction_password'];

            if ($module_status['product_status'] == "yes") {
                $insert_into_sales = $this->register_model->insertIntoSalesOrder($user_id, $data['product_id'], $data['payment_type'], $pending_signup_status);
            }
            $wallet = array('0' => 'anm','1' =>'hajar','2'=>'panda','3'=>'raed','4'=>'agent' );
            foreach ($wallet as $key => $value) {
                $this->register_model->addToCwallet($data, $user_id, $value,'registration',$data['product_id']);
            }
            $this->load->model('payment_model');
            $this->payment_model->updateStripePaymentHistory($session_id, [
                'action' => 'approved',
            ]);

            $this->register_model->commit();

            $logged_userid = $payment_history->done_by;

            if($logged_userid)
            {
                $this->validation_model->insertUserActivity($logged_userid, 'New user registered', $user_id);
            }
            

        }

        /* Stripe User Registration Ends */
    }
    
    
}
