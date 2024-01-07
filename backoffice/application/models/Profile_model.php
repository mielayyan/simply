<?php

class profile_model extends inf_model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('registersubmit_model');
        $this->load->model('validation_model');
        $this->load->model('member_model');
    }

    public function getProfileDetails($user_id, $product_status = '')
    {
        $module_status = $this->trackModule();
        $this->db->select('*,p.grace_period_month');
        $this->db->from('user_details AS u');
        $this->db->join('ft_individual AS f', 'u.user_detail_refid = f.id', 'INNER');
        $this->db->join('package AS p', 'p.prod_id = f.product_id', 'INNER');
        $this->db->where('user_detail_refid', $user_id);
        $result = $this->db->get();
        $result_array = $result->result_array();


        $profile_details = $this->getUserDetails($result_array);

        $profile_arr['details'] = $profile_details['detail1'];
        $profile_arr['sponser'] = $this->validation_model->getSponserIdName($user_id);

        if ($product_status == "yes" || $module_status['opencart_status'] == 'yes') {
            $profile_arr['product_name'] = $this->product_model->getPackageNameFromPackageId($profile_arr['details']['product_id'], $module_status, 'registration');
            $profile_arr['product_name']=$profile_arr['details']['product_name'];

            $profile_arr['product_validity'] = $profile_details['detail1']['product_validity'];
            // dd($profile_arr['product_validity']);
            // $profile_arr['grace_validity'] = $profile_details['detail1']['product_validity']+$profile_details['detail1']['grace_period'];
        }
        return $profile_arr;
    }

    public function getUserDetails($result_array)
    {

        $this->load->model('country_state_model');
        $user_detail = array();

        $i = 1;
        foreach ($result_array as $row) {
            $user_detail["detail$i"]["id"] = $row["user_detail_refid"];
            $user_detail["detail$i"]["name"] = $row["user_detail_name"];
            $user_detail["detail$i"]["second_name"] = $row["user_detail_second_name"];
            $user_detail["detail$i"]["address"] = $row["user_detail_address"];
            $user_detail["detail$i"]["position"] = $row["position"];
            $user_detail["detail$i"]["country_id"] = $row["user_detail_country"];
            $user_detail["detail$i"]["state_id"] = $row["user_detail_state"];
            $user_detail["detail$i"]["pincode"] = $row["user_detail_pin"];
            $user_detail["detail$i"]["mobile"] = $row["user_detail_mobile"];
            $user_detail["detail$i"]["land"] = $row["user_detail_land"];
            $user_detail["detail$i"]["user_detail_second_name"] = $row["user_detail_second_name"];
            $user_detail["detail$i"]["user_detail_address2"] = $row["user_detail_address2"];
            $user_detail["detail$i"]["user_detail_city"] = $row["user_detail_city"];
            $user_detail["detail$i"]["email"] = $row["user_detail_email"];
            $user_detail["detail$i"]["dob"] = $row["user_detail_dob"];
            $user_detail["detail$i"]["gender"] = $row["user_detail_gender"];
            $user_detail["detail$i"]["acnumber"] = $row["user_detail_acnumber"];
            $user_detail["detail$i"]["ifsc"] = $row["user_detail_ifsc"];
            $user_detail["detail$i"]["nbank"] = $row["user_detail_nbank"];
            $user_detail["detail$i"]["user_detail_nacct_holder"] = $row["user_detail_nacct_holder"];
            $user_detail["detail$i"]["nbranch"] = $row["user_detail_nbranch"];
            $user_detail["detail$i"]["pan"] = $row["user_detail_pan"];
            $user_detail["detail$i"]["level"] = $row["user_level"];
            $user_detail["detail$i"]["date"] = $row["join_date"];
            $user_detail["detail$i"]["referral"] = $row["sponsor_id"];
            $user_detail["detail$i"]["acnumber"] = $row["user_detail_acnumber"];
            $user_detail["detail$i"]["ifsc"] = $row["user_detail_ifsc"];
            $user_detail["detail$i"]["nbank"] = $row["user_detail_nbank"];
            $user_detail["detail$i"]["user_detail_nacct_holder"] = $row["user_detail_nacct_holder"];
            $user_detail["detail$i"]["nbranch"] = $row["user_detail_nbranch"];
            $user_detail["detail$i"]["pan"] = $row["user_detail_pan"];

            $user_detail["detail$i"]["blocktrail_account"] = $this->encryption->decrypt($row['bitcoin_address']);
            $user_detail["detail$i"]["paypal_account"] = $this->encryption->decrypt($row['user_detail_paypal']);
            $user_detail["detail$i"]["blockchain_account"] = $this->encryption->decrypt($row['user_detail_blockchain_wallet_id']);
            $user_detail["detail$i"]["bitgo_account"] = $this->encryption->decrypt($row['user_detail_bitgo_wallet_id']);

            $user_detail["detail$i"]["facebook"] = $row["user_detail_facebook"];
            $user_detail["detail$i"]["twitter"] = $row["user_detail_twitter"];
            $user_detail["detail$i"]["product_id"] = $row["product_id"];
            $user_detail["detail$i"]["product_name"] = $row["product_name"];
            $user_detail["detail$i"]["product_validity"] = $row["product_validity"];
            $user_detail["detail$i"]["grace_period"] = $row["grace_period_month"];
            $user_detail["detail$i"]["country"] = $this->country_state_model->getCountryNameFromId($row['user_detail_country']);
            $user_detail["detail$i"]["state"] = $this->country_state_model->getStateNameFromId($row["user_detail_state"]);
            $user_detail["detail$i"]["father_name"] = $this->validation_model->getFullName($row["father_id"]);
            $user_detail["detail$i"]["sponsor_name"] = $this->validation_model->getFullName($row["sponsor_id"]);
            $user_detail["detail$i"]["default_currency"] = $row["default_currency"];
            $user_detail["detail$i"]["google_auth_status"] = $row["google_auth_status"];
            $user_detail["detail$i"]["simply_url"] = $row["simply_url"];

            $file_name = $this->getUserPhoto($row["user_detail_refid"]);
            if (!file_exists(IMG_DIR . 'profile_picture/' . $file_name)) {
                $file_name = 'nophoto.jpg';
            }
            $banner_name = $row['user_banner'];
            if (!file_exists(IMG_DIR . 'banners/' . $row['user_banner'])) {
                $banner_name = 'banner-tchnoly.jpg';
            }
            $user_detail["detail$i"]["profile_photo"] = $file_name;
            $user_detail["detail$i"]["banner_name"] = $banner_name;
            $user_detail["detail$i"]["bank_info_required"] = $row["bank_info_required"];
            $user_detail["detail$i"]["lang_id"] = $row["default_lang"];
            $user_detail["detail$i"]["lang_name"] = $this->inf_model->getLanguageName($row['default_lang']);
            $user_detail["detail$i"]["payout_type"] = $row["payout_type"];
            $user_detail["detail$i"]["rank_name"] = $this->validation_model->getRankName($row['user_rank_id']);

            $i++;
        }

        return $this->replaceNullFromArray($user_detail, "NA");
    }
    public function updateauthentication($user_id , $google_auth_status)
    {
        $this->db->set('google_auth_status',$google_auth_status);
        $this->db->where('id', $user_id);
        $res = $this->db->update('ft_individual');
        return $res;
    }
    public function replaceNullFromArray($user_detail, $replace = '')
    {
        if ($replace == '') {
            $replace = "NA";
        }
        $len = count($user_detail);
        $key_up_arr = array_keys($user_detail);
        for ($i = 1; $i <= $len; $i++) {
            $k = $i - 1;
            $fild = $key_up_arr[$k];
            $arr_key = array_keys($user_detail["$fild"]);
            $key_len = count($arr_key);
            for ($j = 0; $j < $key_len; $j++) {
                $key_field = $arr_key[$j];
                if ($user_detail["$fild"]["$key_field"] == "") {
                    $user_detail["$fild"]["$key_field"] = $replace;
                }
            }
        }
        return $user_detail;
    }

    public function getUserPhoto($user_id)
    {
        $this->db->select('user_photo');
        $this->db->from('user_details');
        $this->db->where('user_detail_refid', $user_id);
        $res = $this->db->get();
        foreach ($res->result() as $row) {
            return $row->user_photo;
        }
    }

    public function changeProfilePicture($user_id, $file_name)
    {
        $arr = array(
            'user_photo' => $file_name
        );
        $this->db->where('user_detail_refid', $user_id);
        $res = $this->db->update('user_details', $arr);
        return $res;
    }

    public function getAllBoards()
    {
        $board_array = array();
        $res = $this->db->select("board_id")->get("board_configuration");
        foreach ($res->result() as $row) {
            $board_array[] = $row->board_id;
        }
        return $board_array;
    }

    public function isUserNameAvailable($user_name)
    {
        $res = $this->validation_model->isUserNameAvailable($user_name);
        return $res;
    }

    public function isUserAvailable($user_name)
    {
        $this->db->select("COUNT(id) as count");
        $this->db->from("ft_individual");
        $this->db->where('user_name', $user_name);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $count = $row->count;
        }

        return $count;
    }

    public function getBusinessVolumeDetails($limit = '', $page = '', $user_id = '')
    {
        $details = array();
        $this->db->select('bv.*, CONCAT_WS(" ", ud.user_detail_name, ud.user_detail_second_name) AS full_name');
        $this->db->from('business_volume AS bv');
        $this->db->join('ft_individual AS i', 'i.id = bv.user_id', 'left');
        $this->db->join('user_details AS ud', 'ud.user_detail_refid = i.id', 'left');
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        if ($limit) {
            $this->db->limit($limit, $page);
        }
        $this->db->order_by('date_of_submission', 'DESC');
        $query = $this->db->get();
        $i = 0;
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $details[$i]['user_name'] = $this->validation_model->IdToUserName($row->user_id);
                $details[$i]['full_name'] = $row->full_name;
                $details[$i]['from_name'] = $this->validation_model->IdToUserName($row->from_id);
                $details[$i]['left_leg'] = $row->left_leg;
                $details[$i]['left_leg_carry'] = $row->left_carry;
                $details[$i]['right_leg'] = $row->right_leg;
                $details[$i]['right_leg_carry'] = $row->right_carry;
                $details[$i]['amount_type'] = $row->amount_type;
                $details[$i]['date'] = $row->date_of_submission;
                $details[$i]['action'] = $row->action;
                $i++;
            }
        }
        return $details;
    }

    public function getTotalBusinessVolumeCount($user_id = '')
    {
        $count = 0;
        $this->db->select('COUNT(*) AS cnt');
        $this->db->from('business_volume');
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        $query = $this->db->get();

        foreach ($query->result() as $row) {
            $count = ($row->cnt > 0) ? $row->cnt : 0;
        }
        return $count;
    }

    public function updatePersonalInfo($user_id, $data, $opencart_status,$kyc_status='no')
    {
        if($kyc_status=='no'){
            $this->db->set('user_detail_name', $data['first_name']);
            $this->db->set('user_detail_second_name', $data['last_name']);
            $this->db->set('user_detail_dob', $data['dob']);
        }
        $this->db->set('user_detail_gender', $data['gender']);
        
        $this->db->where('user_detail_refid', $user_id);
        $res1 = $this->db->update('user_details');

        if ($opencart_status && $kyc_status=='no') {
            $oc_customer_id = $this->validation_model->getOcCustomerId($user_id);

            $this->db->set('firstname', $data['first_name']);
            $this->db->set('lastname', $data['last_name']);
            $this->db->where('customer_id', $oc_customer_id);
            $res2 = $this->db->update('oc_customer');

            $this->db->set('firstname', $data['first_name']);
            $this->db->set('lastname', $data['last_name']);
            $this->db->where('customer_id', $oc_customer_id);
            $res3 = $this->db->update('oc_address');

            return $res1 && $res2 && $res3;
        }

        return $res1;
    }

    public function updateContactInfo($user_id, $data, $opencart_status,$kyc_status='no')
    {
        $this->db->set('user_detail_address', $data['address']);
        $this->db->set('user_detail_address2', $data['address2']);
        if($kyc_status=='no'){
            $this->db->set('user_detail_country', $data['country']);
            if (!empty($data['state'])) {
                $this->db->set('user_detail_state', $data['state']);
            }
        }
        $this->db->set('user_detail_city', $data['city']);
        $this->db->set('user_detail_mobile', $data['mobile']);
        $this->db->set('user_detail_land', $data['land_line']);
        $this->db->set('user_detail_email', $data['email']);
        $this->db->set('user_detail_pin', $data['pincode']);
        $this->db->where('user_detail_refid', $user_id);
        $res1 = $this->db->update('user_details');

        if ($opencart_status) {
            $oc_customer_id = $this->validation_model->getOcCustomerId($user_id);

            $this->db->set('email', $data['email']);
            $this->db->set('telephone', $data['mobile']);
            $this->db->where('customer_id', $oc_customer_id);
            $res2 = $this->db->update('oc_customer');

            $this->db->set('address_1', $data['address']);
            $this->db->set('address_2', $data['address2']);
            $this->db->set('city', $data['city']);
            $this->db->set('postcode', $data['pincode']);
            $this->db->set('country_id', $data['country']);
            $this->db->set('zone_id', $data['state']);
            $this->db->where('customer_id', $oc_customer_id);
            $res3 = $this->db->update('oc_address');

            return $res1 && $res2 && $res3;
        }

        return $res1;
    }

    public function updateBankInfo($user_id, $data)
    {
        $this->db->set('user_detail_acnumber', $data['account_no']);
        $this->db->set('user_detail_ifsc', $data['ifsc']);
        $this->db->set('user_detail_nbank', trim($data['bank_name']));
        $this->db->set('user_detail_nacct_holder', trim($data['account_holder']));
        $this->db->set('user_detail_nbranch', trim($data['branch_name']));
        $this->db->set('user_detail_pan', $data['pan']);
        $this->db->where('user_detail_refid', $user_id);
        $this->db->where('bank_info_required', 'yes');
        $res1 = $this->db->update('user_details');

        return $res1;
    }

    public function updateSocialProfile($user_id, $data)
    {
        $this->db->set('user_detail_facebook', $data['facebook']);
        $this->db->set('user_detail_twitter', $data['twitter']);
        $this->db->where('user_detail_refid', $user_id);
        $res1 = $this->db->update('user_details');

        return $res1;
    }

    public function updatePaymentDetails($user_id, $data)
    {
        if (isset($data['paypal_account'])) {
            $paypal_account = $this->encryption->encrypt($data['paypal_account']);
            $this->db->set('user_detail_paypal', $paypal_account);
        }
        if (isset($data['blockchain_account'])) {
            $blockchain_account = $this->encryption->encrypt($data['blockchain_account']);
            $this->db->set('user_detail_blockchain_wallet_id', $blockchain_account);
        }
        if (isset($data['bitgo_account'])) {
            $bitgo_account = $this->encryption->encrypt($data['bitgo_account']);
            $this->db->set('user_detail_bitgo_wallet_id', $bitgo_account);
        }
        if (isset($data['blocktrail_account'])) {
            $blocktrail_account = $this->encryption->encrypt($data['blocktrail_account']);
            $this->db->set('bitcoin_address', $blocktrail_account);
        }
        if (isset($data['payment_method'])) {
            $this->db->set('payout_type', $data['payment_method']);
        }
        $this->db->where('user_detail_refid', $user_id);
        $res1 = $this->db->update('user_details');

        return $res1;
    }

    public function getActivePaymentGateway()
    {
        $details = array();
        $this->db->select('gateway_name,payout_status');
        $this->db->from('payment_gateway_config');
        $this->db->where('gateway_name !=', 'Bank Transfer');
        $this->db->where('payout_status', 'yes');
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result() as $row) {
            $details[$i]['gateway_name'] = $row->gateway_name;
            $i++;
        }
        return $details;
    }


    //GDPR Functions Starts
    public function addForgetRequest($user_id)
    {
        $this->db->set('user_id', $user_id);
        $this->db->set('status', 'yes');
        $res = $this->db->insert('forget_request');
        return $res;
    }

    public function checkForgetRequest($user_id = '')
    {
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        $this->db->where('status', 'yes');
        $count = $this->db->count_all_results('forget_request');
        return $count;
    }

    public function getForgetRequests()
    {
        $this->db->select('fr.*,f.user_name');
        $this->db->from('forget_request as fr');
        $this->db->join('ft_individual AS f', 'fr.user_id = f.id', 'INNER');
        $this->db->where('fr.status', 'yes');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function approveForgetRequest($id, $user_id)
    {
        $res = false;
        $result = $this->backup_user_details($user_id);
        if ($result) {
            $this->db->where('user_id', $user_id);
            // $this->db->where('id', $id);
            $this->db->set('status', 'forget');
            $res = $this->db->update('forget_request');
        }
        return $res;
    }

    public function backup_user_details($user_id)
    {
        $this->db->select('*');
        $this->db->from('ft_individual');
        $this->db->where('id', $user_id);
        $query = $this->db->get();
        $ft_result = base64_encode(json_encode($query->result_array()));

        $this->db->select('*');
        $this->db->from('user_details');
        $this->db->where('user_detail_refid', $user_id);
        $query1 = $this->db->get();
        $user_result = base64_encode(json_encode($query1->result_array()));

        $this->db->set('ft_details', $ft_result);
        $this->db->set('user_details', $user_result);
        $res = $this->db->insert('user_forget_history');

        return $res;
    }

    public function rejectForgetRequest($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->set('status', 'rejected');
        $res = $this->db->update('forget_request');
        return $res;
    }
    //GDPR Functions Ends

    //KYC Upload Functions Starts
    public function getMyKycDoc($user_id)
    {
        $details = [];
        $this->db->select('k.*,c.category');
        $this->db->from('kyc_docs as k');
        $this->db->join('kyc_category AS c', 'k.type = c.id', 'INNER');
        $this->db->where('user_id', $user_id);
        $this->db->where('k.status !=', 'deleted');
        $query = $this->db->get();

        $i = 0;
        foreach ($query->result_array() as $row) {
            $details[$i] = $row;
            $details[$i]['file_name'] = unserialize($row['file_name']);
            switch ($row['status']) {
                case "pending":
                    $details[$i]['font_class'] = 'warning';
                    break;
                case "rejected":
                    $details[$i]['font_class'] = 'danger';
                    break;
                default:
                    $details[$i]['font_class'] = 'success';
            }
            $i++;
        }
        return $details;
    }

    public function InsertIdentityProof($insert_array, $user_id, $category)
    {
        $ins = serialize($insert_array);
        $this->db->set('file_name', $ins);
        $this->db->set('status', 'pending');
        $this->db->set('type', $category);
        $this->db->set('user_id', $user_id);
        $result = $this->db->insert('kyc_docs');
        return $result;
    }

    public function checkKycDocs($user_id, $category = '')
    {
        $this->db->select("COUNT(id) as count");
        $this->db->from('kyc_docs');
        $this->db->where('user_id', $user_id);
        if ($category) {
            $this->db->where('type', $category);
        }
        $this->db->group_start()
            ->where('status', 'approved')
            ->or_where('status', 'pending')
            ->group_end();
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $count = $row->count;
        }
        if ($count > 0) {
            return true;
        }
        return false;
    }

    public function checkKycDocsExist($user_id)
    {
        $this->db->select("COUNT(id) as count");
        $this->db->where('user_id', $user_id);
        $this->db->where('status', 'approved');
        $query = $this->db->get('kyc_docs');
        $count = $this->db->count_all_results();
          if ($count > 0) {
                    return false;
                }
       

    }

    //KYC Upload Functions Ends

    //KYC Verify Functions Starts
    public function getPendingKyc($user_id = '', $type = '', $status = '')
    {
        $i = 0;
        $details = array();
        $this->db->select('kyc.*,ft.user_name,ft.active,ud.user_photo, ud.user_detail_name, ud.user_detail_second_name,c.category');
        $this->db->from('kyc_docs as kyc');
        $this->db->join('ft_individual as ft', 'ft.id=kyc.user_id', 'inner');
        $this->db->join('user_details as ud', 'ft.id=ud.user_detail_refid', 'inner');
        $this->db->join('kyc_category AS c', 'kyc.type = c.id', 'INNER');
        if ($user_id) {
            $this->db->where('kyc.user_id', $user_id);
        }
        if ($status != 'any') {
            $this->db->where('kyc.status', $status);
        }
        if ($type) {
            $this->db->where('kyc.type', $type);
        }
        $this->db->where('kyc.status !=', 'deleted');
        $this->db->order_by('kyc.date', 'asc');
        $query = $this->db->get();

        foreach ($query->result_array() as $row) {
            $details[$i]['user_name']   = $row['user_name'];
            $details[$i]['category']    = $row['category'];
            $details[$i]['type']        = $row['type'];
            $details[$i]['active']      = $row['active'];
            $details[$i]['user_photo']  = $row['user_photo'];
            $details[$i]['status']      = $row['status'];
            $details[$i]['reason']      = $row['reason'];
            $details[$i]['file_name']   = unserialize($row['file_name']);
            $details[$i]['full_name']   = $row['user_detail_name'] . " " . $row['user_detail_second_name'];
            switch ($row['status']) {
                case "pending":
                    $details[$i]['font_class'] = 'warning';
                    break;
                case "rejected":
                    $details[$i]['font_class'] = 'danger';
                    break;
                default:
                    $details[$i]['font_class'] = 'success';
            }
            $i++;
        }

        return $details;
    }

    public function verifyKyc($user_id, $type)
    {
        $this->db->set('status', 'approved');
        $this->db->set('reason', '');
        $this->db->where('user_id', $user_id);
        $this->db->where('type', $type);
        $this->db->where('status', 'pending');
        $result = $this->db->update('kyc_docs');

        if ($result) {
            $this->db->set('kyc_status', 'yes');
            $this->db->where('user_detail_refid', $user_id);
            $this->db->update('user_details');
        }
        return $result;
    }

    public function rejectKyc($user_id, $type, $reason)
    {
        $this->db->set('status', 'rejected');
        $this->db->set('reason', $reason);
        $this->db->where('user_id', $user_id);
        $this->db->where('status', 'pending');
        $this->db->where('type', $type);
        $result = $this->db->update('kyc_docs');
        return $result;
    }

    public function deletetKyc($id, $user_id)
    {
        $this->db->set('status', 'deleted');
        $this->db->where('id', $id);
        $result = $this->db->update('kyc_docs');
        if ($result) {
            $exist  = $this->checkKycDocsExist($user_id);
            if (!$exist) {
                $this->db->set('kyc_status', 'no');
                $this->db->where('user_detail_refid', $user_id);
                $this->db->update('user_details');
            }
        }
        return $result;
    }
    //KYC Verify Functions Ends
    public function changeBannerImage($user_id, $file_name)
    {
        $arr = array(
            'user_banner' => $file_name
        );
        $this->db->where('user_detail_refid', $user_id);
        $res = $this->db->update('user_details', $arr);
        return $res;
    }
    public function getBannerPic($user_id)
    {

        $file_name = $this->getUserPhoto($user_id);
        if (!file_exists(IMG_DIR . 'profile_picture/' . $file_name)) {
            $file_name = 'na';
        }
        $this->db->select('user_banner');
        $this->db->where('user_detail_refid', $user_id);
        $banner_name = $this->db->get('user_details')->row()->user_banner;
        if (!file_exists(IMG_DIR . 'banners/' . $banner_name)) {
            $banner_name = 'banner-tchnoly.jpg';
        }
        return [
            'user_banner' => $banner_name,
            'user_image' => $file_name,
        ];
    }
    public function getCustomFields()
    {
        $this->db->select('field_name');
        $this->db->from('signup_fields');
        $this->db->where('delete_status','yes');
        $this->db->like('field_name', 'custom_');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getUserCustomDetails($user_id)
    {   
        $details = [];
        $this->db->select('field_name,field_value');
        $this->db->from('custom_field_details');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        $i = 0;
        foreach($query->result_array() as $row) {
            $details["{$row['field_name']}"] = $row['field_value'];
            $i++;
        }
        return  $details;
    }
    public function setCustomDetails($data, $user_id)
    {
        $details = $this->getCustomFields();
        $user_details = $this->getUserCustomDetails($user_id);

        foreach($details as $det) {
            if (array_key_exists($det['field_name'], $user_details)) {  
                if(isset($data["{$det['field_name']}"])){
                $this->db->set('field_value', $data["{$det['field_name']}"]);
                $this->db->where('user_id', $user_id);
                $this->db->where('field_name', $det['field_name']);
                $this->db->update('custom_field_details');
               }
            } else {
                if(isset($data["{$det['field_name']}"])){
                $this->db->set('user_id', $user_id);
                $this->db->set('field_name', $det['field_name']);
                $this->db->set('field_value', $data["{$det['field_name']}"]);
                $this->db->insert('custom_field_details');
                }
            }
        }
       return TRUE;
    }
    public function changeUsername($user_id, $user_name, $new_user_name) {
        $flag = false;

        $user_type = $this->validation_model->getUserType($user_id);

        $this->begin();
        if ($user_type != "admin") {
            $this->db->set('user_name', $new_user_name);
            $this->db->where('id', $user_id);
            $result = $this->db->update('ft_individual');

            if ($result) {
                if ($this->MLM_PLAN == 'Board') {
                    $update_borad_username = $this->updateBoardUserName($user_id, $user_name, $new_user_name);
                } else {
                    $update_borad_username = true;
                }
                if ($update_borad_username) {
                    $this->addChangeUsernameHistory($user_id, $user_name, $new_user_name);
                    $flag = true;
                }
            }
        }

        if ($flag) {
            $this->commit();
        } else {
            $this->rollBack();
        }

        return $flag;
    }

    public function addChangeUsernameHistory($user_id, $user_name, $new_user_name) {
        $data = array(
            'user_id' => $user_id,
            'old_username' => $user_name,
            'new_username' => $new_user_name,
            'modified_date' => date('y-m-d H:i:s')
            );
        $res = $this->db->insert('username_change_history', $data);
        return $res;
    }

    /**
     * [getRequiredStatus description]
     * @param  [type] $field_name [description]
     * @return [type] [col val]
     */
    public function getRequiredStatus($field_name) {
        return $this->db->select('required')
            ->where('status', 'yes')
            ->where('field_name', $field_name)
            ->get('signup_fields')
            ->row('required');
    }
}
