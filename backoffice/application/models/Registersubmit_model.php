<?php

Class registersubmit_model extends inf_model {

    public $obj_module;

    public function __construct() {
        parent::__construct();
        $this->load->model('validation_model');
        $this->load->model('configuration_model');
    }

    public function registerUser($regr, $module_status) {
        $response = [];
        $flag = FALSE;
        $leg_detail_flag = TRUE;
        $purchase_address_flag = TRUE;
        $user_id = $this->addFtDetails($regr, $module_status);
        $regr['simply_url']='';
        if($this->checkSimplyUrlAllowed($regr['product_id'])){
            $regr['simply_url'] = $this->register_api($regr['user_name_entry'],$regr['username']);
        }
        if($this->validation_model->getPackageSupportBasedOnProductId($regr['product_id'],'board')){
            $this->boardRegister($regr,$user_id);
        }
        if ($user_id) {
            $response['user_id'] = $user_id;
            $this->addToCustomDetails($regr, $user_id);
            $user_detail_flag = $this->addUserDetails($regr, $user_id);
            if ($user_detail_flag) {
                $balance_amount_flag = $this->insertBalanceAmount($user_id);
                if ($balance_amount_flag) {
                    $transaction_password = (DEMO_STATUS == 'yes') ? '12345678' : $this->getRandTransPasscode(8);
                    $response['transaction_password'] = $transaction_password;
                    $tran_password_flag = $this->savePassCodes($user_id, $transaction_password);
                    if ($tran_password_flag) {

                        $reg_detail_flag = $this->addUserRegistrationDetails($regr, $user_id);
                        if ($reg_detail_flag) {
                            if ($module_status['repurchase_status'] == 'yes') {
                                $purchase_address_flag = true;
                            }
                            if ($purchase_address_flag) {
                                $flag = TRUE;
                            }
                        }
                    }
                }
            }
            $this->addToSummary($user_id,$regr['sponsor_id']);
        }

        $response['status'] = $flag;
        return $response;
    }
    
    public function addToSummary($user_id,$sponsor_id) {
        $this->db->set('user_id',$user_id);
        $this->db->insert('summary_info');
        
        $this->db->where('user_id',$sponsor_id);
        $this->db->set('directs', 'directs + 1', FALSE);
        $this->db->update('summary_info');
        return;
    }

    public function updateTreeNodes($mlm_plan, $father_id, $sponsor_id, $position) {
        $placement_nodes = $this->getUserLeftRightNode($father_id);
        $child_exists = ($placement_nodes['right'] - $placement_nodes['left']) > 1;
        $left_sponsor = $this->getUserRightNode($sponsor_id, 'sponsor_tree');
        $right_sponsor = $left_sponsor + 1;

        $this->db->set('right_sponsor', 'right_sponsor + 2', FALSE);
        $this->db->where('right_sponsor >= ', $left_sponsor);
        $this->db->update('tree_parser');

        $this->db->set('left_sponsor', 'left_sponsor + 2', FALSE);
        $this->db->where('left_sponsor > ', $left_sponsor);
        $this->db->update('tree_parser');

        if ($mlm_plan == 'Binary' && $position == 'L' && $child_exists) {
            $left_father = $placement_nodes['left'] + 1;

            $this->db->set('right_father', 'right_father + 2', FALSE);
            $this->db->where('right_father >', $placement_nodes['left']);
            $this->db->update('tree_parser');

            $this->db->set('left_father', 'left_father + 2', FALSE);
            $this->db->where('left_father > ', $placement_nodes['left']);
            $this->db->update('tree_parser');
        }
        else {
            $left_father = $placement_nodes['right'];

            $this->db->set('right_father', 'right_father + 2', FALSE);
            $this->db->where('right_father >= ', $placement_nodes['right']);
            $this->db->update('tree_parser');

            $this->db->set('left_father', 'left_father + 2', FALSE);
            $this->db->where('left_father > ', $placement_nodes['right']);
            $this->db->update('tree_parser');
        }

        $right_father = $left_father + 1;

        return [
            'left_father' => $left_father,
            'right_father' => $right_father,
            'left_sponsor' => $left_sponsor,
            'right_sponsor' => $right_sponsor,
        ];
    }

    public function addFtDetails($regr, $module_status) {

        //$user_tree_nodes = $this->updateTreeNodes($module_status['mlm_plan'], $regr['placement_id'], $regr['sponsor_id'], $regr['position']);
        $leg_position = $regr['position'];
        if($this->MLM_PLAN == "Binary") {
            $leg_position = ($regr['position'] == 'R') ? 2 : 1;
        }
        $customer_id = $regr['customer_id'] ?? 0;
        $package_id = $regr['package_id'] ?? '';
        $package_validity = $regr['product_validity'] ?? '';
        $join_date = $regr['joining_date'] ?? date('Y-m-d H:i:s');
        $tree_order_id = $this->getMaxOrderID() + 1;
        $father_level = $this->validation_model->getUserTreeLevel($regr['placement_id'], 'tree') + 1;
        $sponsor_level = $this->validation_model->getUserTreeLevel($regr['sponsor_id'], 'sponsor_tree') + 1;

        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE; //disable debugging for queries

        $this->db->set('oc_customer_ref_id', $customer_id);
        $this->db->set('order_id', $tree_order_id);
        $this->db->set('user_type', 'user');
        $this->db->set('user_name', $regr['username']);
        $this->db->set('password', password_hash($regr['pswd'], PASSWORD_DEFAULT));
        $this->db->set('active', 'yes');
        $this->db->set('position', $regr['position']);
        $this->db->set('leg_position', $leg_position);
        $this->db->set('father_id', $regr['placement_id']);
        $this->db->set('sponsor_id', $regr['sponsor_id']);
        $this->db->set('product_id', $package_id);
        if (!empty($package_validity)) {
            $this->db->set('product_validity', $package_validity);
        }
        $this->db->set('date_of_joining', $join_date);
        $this->db->set('user_level', $father_level);
        $this->db->set('sponsor_level', $sponsor_level);
        $this->db->set('register_by_using', $regr['by_using']);
        $query = $this->db->insert('ft_individual');
        $user_id = $this->db->insert_id();
        $this->db->db_debug = $db_debug;
        return $user_id;
    }

    public function addUserDetails($regr, $user_id) {

        if (isset($regr['bitcoin_address'])) {
            $key = $this->config->item('encryption_key');
            $bitcoin_address_enc = $this->encryption->encrypt($regr['bitcoin_address']);
        }
        else {
            $bitcoin_address_enc = '';
        }
        $bank_info_required = $this->configuration_model->getBankInfoStatus();
        $agent_id = $this->validation_model->getAgentIdByCountry($regr['country']);
        $data = [
            'user_detail_refid' => $user_id,
            'user_details_ref_user_id' => $regr['sponsor_id'],
            'user_detail_name' => $regr['first_name'],
            'user_detail_second_name' => $regr['last_name'] ?? '',
            'user_detail_dob' => $regr['date_of_birth']??null,
            'user_detail_email' => $regr['email'],
            'user_detail_mobile' => $regr['mobile'],
            'bitcoin_address' => $bitcoin_address_enc,
            'join_date' => $regr['joining_date'],
            'bank_info_required' => $bank_info_required,
            
            'user_detail_pin' => $regr['pin']??'NA',
            'user_detail_country' => $regr['country']??null,
            'user_detail_state' => $regr['state']??null,
            'payout_type'      => 'Bank Transfer',
            'agent_id' =>   $agent_id, 
            'simply_url' => $regr['simply_url'],
        ];

        $from_opencart = false;
        if ($this->MODULE_STATUS['opencart_status'] == 'yes' && $this->MODULE_STATUS['opencart_status_demo'] == 'yes') {
        $from_opencart = true;
        }

        $contact_fields = $this->register_model->getSignUpAllFieldStatus();

        if ($contact_fields['land_line'] == "yes") {
            $data['user_detail_land'] = $regr['land_line'] ?? '';
        }
        if ($contact_fields['city'] == "yes" || $from_opencart) {
            $data['user_detail_city'] = $regr['city'] ?? '';
        }
        if ($contact_fields['adress_line2'] == "yes" || $from_opencart) {
            if($from_opencart)
                $regr['adress_line2'] =  $regr['address_line2'] ?? '';
            $data['user_detail_address2'] = $regr['adress_line2'] ?? '';
        }
        if ($contact_fields['adress_line1'] == "yes" || $from_opencart) {
            if($from_opencart)
                $regr['adress_line1'] =  $regr['address'] ?? '';
            $data['user_detail_address'] = $regr['adress_line1'] ?? '';
        }
        if ($contact_fields['gender'] == "yes" || $from_opencart) {
            $data['user_detail_gender'] = $regr['gender'] ?? '';
        }
        if (empty($data['user_detail_state'])) {
            unset($data['user_detail_state']);
        }
        $result = $this->db->insert('user_details', $data);
        return $result;
    }

    public function addUserRegistrationDetails($regr, $user_id) {
      
        if(empty($regr['reg_amount'])) {
            $regr['reg_amount'] = 0;
        }
        if(empty($regr['total_amount'])) {
            $regr['total_amount'] = 0;
        }
        $contact_fields = $this->register_model->getSignUpAllFieldStatus();
        $insert_data = [
            "user_id" => $user_id,
            "user_name" => $regr['username'],
            "sponsor_id" => $regr['sponsor_id'],
            "placement_id" => $regr['placement_id'],
            "position" => $regr['position'],
            "first_name" => $regr['first_name'],
            "last_name" => $regr['last_name']??null,
            "email" => $regr['email'],
            "mobile" => $regr['mobile'],
            "product_id" => $regr['product_id'],
            "product_name" => $regr['product_name'],
            "product_pv" => $regr['product_pv'],
            "product_amount" => $regr['product_amount'],
            "reg_amount" => $regr['reg_amount'],
            "total_amount" => $regr['total_amount'],
            "reg_date" => $regr['joining_date']
        ];
        if (empty($regr['state'])) {
            if (isset($insert_data['state_id'])) {
                unset($insert_data['state_id']);
            }
        }
        $result = $this->db->insert('infinite_user_registration_details', $insert_data);
        return $result;
    }

    public function insertBalanceAmount($user_id) {
        $this->db->set('balance_amount', '0');
        $this->db->set('user_id', $user_id);
        $result = $this->db->insert('user_balance_amount');
        return $result;
    }

    public function savePassCodes($user_id, $tran_code) {
        $secure_code = password_hash($tran_code, PASSWORD_DEFAULT);
        $this->db->set('user_id', $user_id);
        $this->db->set('tran_password', $secure_code);
        $res = $this->db->insert('tran_password');
        return $res;
    }

    public function getUserRightNode($user_id, $tree_type = 'tree') {
        if($tree_type == 'tree') {
            $this->db->select('right_father right');

        } elseif($tree_type == 'sponsor_tree') {
            $this->db->select('right_sponsor right');
        }
        $this->db->where('ft_id', $user_id);
        $query = $this->db->get('tree_parser');
        return $query->row_array()['right'];
    }

    public function getUserLeftRightNode($user_id, $tree_type = 'tree') {
        if($tree_type == 'tree') {
            $this->db->select('left_father left,right_father right');

        } elseif($tree_type == 'sponsor_tree') {
            $this->db->select('left_sponsor left,right_sponsor right');
        }
        $this->db->where('ft_id', $user_id);
        $query = $this->db->get('tree_parser');
        return $query->row_array();
    }

    public function updateTreeLeftRightNode($value, $tree_type = 'tree') {
        if($tree_type == 'tree') {
            $this->db->set('right_father', 'right_father + 2', FALSE);
            $this->db->where('right_father >= ', $value);
            $this->db->update('tree_parser');

            $this->db->set('left_father', 'left_father + 2', FALSE);
            $this->db->where('left_father > ', $value);
            $this->db->update('tree_parser');

        } elseif($tree_type == 'sponsor_tree') {
            $this->db->set('right_sponsor', 'right_sponsor + 2', FALSE);
            $this->db->where('right_sponsor >= ', $value);
            $this->db->update('tree_parser');

            $this->db->set('left_sponsor', 'left_sponsor + 2', FALSE);
            $this->db->where('left_sponsor > ', $value);
            $this->db->update('tree_parser');
        }
    }

    public function str_rand($minlength, $maxlength, $useupper = true, $usenumbers = true) {
        $key = '';
        $charset = '';
        if ($useupper)
            $charset .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($usenumbers)
            $charset .= '0123456789';
        if ($minlength > $maxlength)
            $length = mt_rand($maxlength, $minlength);
        else
            $length = mt_rand($minlength, $maxlength);
        for ($i = 0; $i < $length; $i++)
            $key .= $charset[(mt_rand(0, (strlen($charset) - 1)))];
        return $key;
    }

    public function insertUserDetails($regr) {
        $flag = false;
        if(isset($regr['bitcoin_address'])){
            $key = $this->config->item('encryption_key');
            $bitcoin_address_enc = $this->encryption->encrypt($regr['bitcoin_address']);
        }else{
            $bitcoin_address_enc = '';
        }
        $bank_info_required = $this->configuration_model->getBankInfoStatus();

        $data = array(
            'user_detail_refid' => $regr['userid'],
            'user_details_ref_user_id' => $regr['sponsor_id'],
            'user_detail_name' => $regr['first_name'],
            'user_detail_second_name' => $regr['last_name'],
            'user_detail_gender' => $regr['gender'],
            'user_detail_dob' => $regr['date_of_birth'],
            'user_detail_address' => $regr['address'],
            'user_detail_address2' => $regr['address_line2'],
            'user_detail_pin' => $regr['pin'],
            'user_detail_country' => $regr['country'],
            'user_detail_state' => ($regr['state']!='')?$regr['state']:0,
            'user_detail_city' => $regr['city'],
            'user_detail_email' => $regr['email'],
            'user_detail_land' => $regr['land_line'],
            'user_detail_mobile' => $regr['mobile'],
            'user_detail_nbank' => $regr['bank_name'],
            'user_detail_nacct_holder' => isset($regr['acct_holder_name']) ? $regr['acct_holder_name'] : '',
            'user_detail_nbranch' => $regr['bank_branch'],
            'user_detail_acnumber' => $regr['bank_acc_no'],
            'user_detail_ifsc' => $regr['ifsc'],
            'user_detail_pan' => $regr['pan_no'],
            'bitcoin_address' => $bitcoin_address_enc,
            'join_date' => $regr['joining_date'],
            'bank_info_required' => $bank_info_required
            );
        if (empty($regr['state'])) {
            if (isset($data['user_detail_state'])) {
                unset($data['user_detail_state']);
            }
        }
        $res = $this->db->insert('user_details', $data);
        if ($res) {
            if($regr['userid'] != '') {
                $user_id = $regr['userid'];
            } else {
                $user_id = $regr['sponsor_id'];
            }
            if($regr['sponsor_id'] == $this->LOG_USER_ID){
                $done_by = $this->LOG_USER_ID;
            }
            else {
                $done_by = $regr['sponsor_id'];
            }
            $serialized_data = serialize($data);
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'new user registered', $this->LOG_USER_ID, $serialized_data);
            $flag = true;
        }
        return $flag;
    }

    public function updateUserDetails($regr, $uid) {
        $flag = false;
        $this->db->where('user_detail_refid', $uid);
        $reg_update = array('user_detail_name' => $regr['full_name'],
            'user_detail_second_name' => $regr['second_name'],
            'user_detail_address' => $regr['address'],
            'user_detail_address2' => $regr['address_line2'],
            'user_detail_country' => $regr['country'],
            'user_detail_state' => $regr['state'],
            'user_detail_city' => $regr['city'],
            'user_detail_mobile' => $regr['mobile'],
            'user_detail_land' => $regr['land_line'],
            'user_detail_email' => $regr['email'],
            'user_detail_pin' => $regr['pin'],
            'user_detail_acnumber' => $regr['bank_acc_no'],
            'user_detail_ifsc' => $regr['ifsc'],
            'user_detail_nbank' => $regr['bank_name'],
            'user_detail_nacct_holder' => $regr['user_detail_nacct_holder'],
            'user_detail_nbranch' => $regr['bank_branch'],
            'user_detail_pan' => $regr['pan_no'],
            'user_detail_dob' => $regr['date_of_birth'],
            'user_detail_gender' => $regr['gender'],
            'user_detail_facebook' => $regr['facebook'],
            'user_detail_twitter' => $regr['twitter'],
            );

        $reg_res = $this->db->update('user_details', $reg_update);
        if ($reg_res) {
            $flag = true;
        }
        return $flag;
    }

    public function getMaxOrderID() {
        $this->db->select_max('order_id', 'order_id');
        $query = $this->db->get('ft_individual');
        $max_order_id = $query->row_array()['order_id'];
        return $max_order_id;
    }

    public function getUsername() {
        $config = $this->configuration_model->getUsernameConfig();
        $u_name = $this->getRandId($config);
        return $u_name;
    }

    public function getUsernameConfig() {
        $query = $this->db->get('username_config');
        foreach ($query->result_array() as $row) {
            $config["length"] = $row["length"];
            $config["prefix_status"] = $row["prefix_status"];
            $config["prefix"] = $row["prefix"];
        }
        return $config;
    }

    public function getRandId($config) {

        $key = "";
        $charset = "0123456789";
        for ($i = 0; $i < $config['length']; $i++){
            $key .= $charset[(mt_rand(0, (strlen($charset) - 1)))];
        }
        if(!$key){
            $this->getRandId($config);
        }
        $randum_id = $key;
        if ($config["prefix_status"] == "yes") {
            $prefix = $config["prefix"];
            $randum_id = $prefix . $randum_id;
        }
        $this->db->from('ft_individual');
        $this->db->where('user_name', $randum_id);
        $count= $this->db->count_all_results();
        if ($count){
            $this->getRandId($config);
        }
        else{
            return $randum_id;
        }

    }

    public function getRandTransPasscode($length) {
        $key = '';
        $charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0; $i < $length; $i++)
            $key .= $charset[(mt_rand(0, (strlen($charset) - 1)))];
        $randum_id = $key;

        $this->db->select('*');
        $this->db->from('tran_password');
        $this->db->where('tran_password', $randum_id);
        $qr = $this->db->get();
        $count = $qr->num_rows();
        if (!$count)
            return $key;
        else
            $this->getRandTransPasscode($length);
    }

    public function insertUserActivity($login_id, $activity, $done_by) {

        $date = date('Y-m-d H:i:s');
        $ip_adress = $_SERVER['REMOTE_ADDR'];
        //Code to convert Ipv6 address to Ipv4
        if (!filter_var($ip_adress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
            $ip_adress = hexdec(substr($ip_adress, 0, 2)). "." . hexdec(substr($ip_adress, 2, 2)). "." . hexdec(substr($ip_adress, 5, 2)). "." . hexdec(substr($ip_adress, 7, 2));
         }
        $this->db->set('user_id', $login_id);
        $this->db->set('activity', $activity);
        $this->db->set('done_by', $done_by);
        $this->db->set('ip', $ip_adress);
        $this->db->set('date', $date);
        $result = $this->db->insert('activity_history');
        return $result;
    }

    public function getNewPositionOfUser($user_id) {
        $this->db->select_max('position', 'new_position');
        $this->db->from('ft_individual');
        $this->db->where('id', $user_id);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $new_position = $row->new_position;
        }

        return $new_position;
    }

    public function isUserLevelFull($father_id, $width_ceiling) {

        $this->db->select("COUNT(*) AS cnt");
        $this->db->from("ft_individual");
        $this->db->where('father_id', $father_id);
        $qr = $this->db->get();

        foreach ($qr->result() as $row) {
            $cnt = $row->cnt;
        }
        $current_users = $cnt;
        if ($current_users >= $width_ceiling) {
            $flag = true;
        } else {
            $flag = false;
        }

        return $flag;
    }

    public function insertRePurchaseAddress($regr, $user_id) {
        $data = [
            'user_id' => $user_id,
            'name' => $regr['first_name'],
            'mobile' => $regr['mobile'],
            'default_address' => '1'
        ];
        $res = $this->db->insert('repurchase_address', $data);
        return $res;
    }
    public function addToCustomDetails($regr, $user_id) {
        $res = FALSE;
        $query = $this->db->select('field_name')
                        ->from('signup_fields')
                        ->where('status', 'yes')
                        ->where('delete_status','yes')
                        ->like('field_name', 'custom_')
                        ->get();
        $detail =  $query->result_array();
        if ($query->num_rows() > 0) {
            foreach ($detail as $k => $v) {
                if (array_key_exists("{$v['field_name']}", $regr)) {
                    $val = $regr["{$v['field_name']}"];
                    $values[]= ['user_id' => $user_id, 'field_name' => $v['field_name'], 'field_value' => $val];
                }
            }
            $res = $this->db->insert_batch('custom_field_details', $values);
        }
        return $res;
    }
    
    
    public function register_api($username,$userid) {
        
        //return 'simply_url';
        
        $url = "https://backendapp.simply37.com/api/user/register?app_key=f9r%21G$2T?7k%25u3@M4x&username=$username&user_id=$userid";
        $data = array(array("app_key" => 'f9r!G$2T?7k%u3@M4x'),array('username' => $username), array('user_id' => $userid) );
        
        $postdata = json_encode($data);
        
        $ch = curl_init($url); 
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', "Postman-Token: 2cd8c863-7461-4c16-95dd-768e77b99572", "cache-control: no-cache"));
        $result = curl_exec($ch);
        
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
	$result = json_decode($result,TRUE);
	if(isset($result['status'])) {
        if($result['status']=='ok') {
            return $result['data'];
        } else {
            return '';
	}
	} else {
		return '';
	}
    }
    public function checkSimplyUrlAllowed($product_id){
        $this->db->select('simply_url_status');
        $this->db->where('product_id',$product_id);
        $res=$this->db->get('package');
        return ($res->row_array()['simply_url_status']=='yes')?TRUE:FALSE;
    }
    public function boardRegister($regr,$user_id){
        $url = BOARD_URL.'/user/register';
        $data = array(
            'api_key' => BOARD_API_KEY,
            'sponsor_username' => $regr['sponsor_user_name'],
            'name' => $regr['first_name']." ".$regr['last_name'],
            'firstname' => $regr['first_name'],
            'lastname' => $regr['last_name'],
            'username' => $regr['user_name_entry'],
            'email' =>$regr['email'],
            'country_code' => $this->country_state_model->getCountryCodeFromId($regr['country']),
            'mobile' => $regr['mobile'],
            'password' => password_hash($regr['pswd'], PASSWORD_DEFAULT),
            'downline_user_id' => $regr['board_downline']??'',
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
        $responseArray = json_decode($result, true);
        $this->db->set('user_id',$user_id);
        $this->db->set('data',serialize($responseArray));
        $this->db->insert('board_api_log');
        if ($result === FALSE) {
            // Handle error
           // echo 'Error occurred while making the request.';
            $msg = $this->lang->line('registration_failed');
            $this->redirect($msg, "home/index", false);
        } else {
            // Process the result
            //echo $result;
           
            // Check if decoding was successful
            if ($responseArray !== null) {
                // Access the relevant data
                $status = $responseArray['original']['status'];
                if($status==200){
                    $this->db->set('board_register_status','yes');
                    $this->db->where('id',$user_id);
                    $this->db->update('ft_individual');
                    
                }
                
            }
        }
        return TRUE;
    }
}
