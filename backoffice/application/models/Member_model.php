<?php

class member_model extends inf_model {

    public function __construct() {
        $this->load->library('inf_phpmailer', NULL, 'phpmailer');

        $this->load->model('validation_model');
    }

    public function searchMembers($keyword, $page, $limit) {

        $this->load->model('country_state_model');
        $this->db->select("fi.*, ud.*");
        $this->db->from("ft_individual as fi");
        $this->db->join("user_details as ud", "ud.user_detail_refid = fi.id", "INNER");
        $where = array('fi.user_name' => $keyword, 'ud.user_detail_name' => $keyword, 'ud.user_detail_address' => $keyword, 'ud.user_detail_mobile' => $keyword);
        $this->db->group_start()
                ->or_like($where)
                ->or_like("CONCAT( ud.user_detail_name,' ',ud.user_detail_second_name)", $keyword)
                ->group_end();
        $this->db->order_by("fi.id");
        $this->db->limit($limit, $page);
        $query = $this->db->get();

        $cnt = $query->num_rows();
        $this->search_user = null;
        if ($cnt > 0) {
            $i = 0;

            foreach ($query->result_array() as $row) {

                $this->search_user["detail$i"]["user_id"] = $row['id'];
                $id_encode = $this->encryption->encrypt($row['user_name']);
                $id_encode = str_replace("/", "_", $id_encode);
                $encrypt_id = urlencode($id_encode);
                $this->search_user["detail$i"]["user_id_en"] = $encrypt_id;

                $this->search_user["detail$i"]["user_name"] = $row['user_name'];
                $this->search_user["detail$i"]["active"] = $row['active'];
                $this->search_user["detail$i"]["father_id"] = $row['father_id'];
                $this->search_user["detail$i"]["sponser_name"] = $this->validation_model->IdToUserName($row['sponsor_id']);
                if (!$this->search_user["detail$i"]["sponser_name"]) {
                    $this->search_user["detail$i"]["sponser_name"] = "NA";
                }
                $this->search_user["detail$i"]["user_detail_name"] = $row['user_detail_name'];
                $this->search_user["detail$i"]["user_detail_name"] .= " " . $row['user_detail_second_name'];
                if ($row['user_detail_address'] != "")
                    $this->search_user["detail$i"]["user_detail_address"] = $row['user_detail_address'];
                else
                    $this->search_user["detail$i"]["user_detail_address"] = "NA";
                if ($row['user_detail_mobile'])
                    $this->search_user["detail$i"]["user_detail_mobile"] = $row['user_detail_mobile'];
                else
                    $this->search_user["detail$i"]["user_detail_mobile"] = "NA";
                if ($row['user_detail_country'])
                    $this->search_user["detail$i"]["user_detail_country"] = $this->country_state_model->getCountryNameFromId($row['user_detail_country']);
                else
                    $this->search_user["detail$i"]["user_detail_country"] = "NA";
                $this->search_user["detail$i"]["date_of_joining"] = $row['date_of_joining'];
                $this->search_user["detail$i"]["rank"] = $this->validation_model->getRankName($row['user_rank_id']);
                $this->search_user["detail$i"]["rank_color"] = $this->validation_model->getRankColor($row['user_rank_id']);
                $i++;
            }
        }

        return $this->search_user;
    }
    public function find_memeber($keyword= "", $status = "yes", $limit, $offset) {
        $this->load->model('country_state_model');
       $this->db->select("fi.id, fi.user_name,fi.active,CONCAT(ud.user_detail_name, ' ', ud.user_detail_second_name) AS full_name, ud.user_detail_mobile,ud.user_photo, ud.user_detail_email, rd.rank_name, ft.user_name sponsor_name,ft.date_of_joining");
        $this->db->from("ft_individual as fi");
        $this->db->join("user_details as ud", "ud.user_detail_refid = fi.id", "INNER");
        $this->db->join('rank_details as rd','rd.rank_id=fi.user_rank_id', "LEFT");
        $this->db->join('ft_individual as ft','ft.id=fi.sponsor_id', "INNER");
        $where = array('fi.user_name' => $keyword,'ud.user_detail_name' => $keyword, 'ud.user_detail_mobile' => $keyword,'ud.user_detail_email' => $keyword,'rd.rank_name' =>$keyword,'ft.user_name' => $keyword);
        if($this->MODULE_STATUS['opencart_status'] == "yes") {
            $lang_id = $this->session->userdata('inf_language')['lang_id'] ?: $this->APP_CONFIG['default_language']['oc_lang_id'];
            $this->db->select('pd.name as product_name');
            $this->db->join('oc_product as pe','pe.package_id=fi.product_id', "INNER");
            $this->db->join('oc_product_description as pd', 'pd.product_id = pe.product_id', 'LEFT');
            $this->db->where('pd.language_id', $lang_id);
            $where['pd.name']=$keyword;
        } else {
            $this->db->select('pe.product_name');
            $this->db->join('package as pe','pe.prod_id=fi.product_id', "INNER");
            $where['pe.product_name']=$keyword;
        }
        if($keyword !='')
        {
        $this->db->group_start();
        $this->db->or_like($where);
        $this->db->group_end();
        }
        
        $this->db->where('fi.delete_status', "active");
        $this->db->where('fi.active', $status);
        $this->db->where('fi.user_type', 'user');
        $this->db->order_by('fi.id', 'DESC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();

        $cnt = $query->num_rows();
        return $query->result_array();
    }
    public function searchMemberCount($keyword = "", $status = "yes") {
        $this->db->select("fi.id, fi.user_name,fi.active,CONCAT(ud.user_detail_name, ' ', ud.user_detail_second_name) AS full_name, ud.user_detail_mobile, ud.user_detail_email, rd.rank_name, ft.user_name sponsor_name");
        $this->db->from("ft_individual as fi");
        $this->db->join("user_details as ud", "ud.user_detail_refid = fi.id", "INNER");
        $this->db->join('rank_details as rd','rd.rank_id=fi.user_rank_id', "LEFT");
        $this->db->join('ft_individual as ft','ft.id=fi.sponsor_id', "INNER");
        $where = array('fi.user_name' => $keyword,'ud.user_detail_name' => $keyword, 'ud.user_detail_mobile' => $keyword,'ud.user_detail_email' => $keyword,'rd.rank_name' =>$keyword,'ft.user_name' => $keyword);
        if($this->MODULE_STATUS['opencart_status'] == "yes") {
            $lang_id = $this->session->userdata('inf_language')['lang_id'] ?: 1;
            $this->db->select('pd.name as product_name');
            $this->db->join('oc_product as pe','pe.package_id=fi.product_id', "INNER");
            $this->db->join('oc_product_description as pd', 'pd.product_id = pe.product_id', 'INNER');
            $this->db->where('pd.language_id', $lang_id);
            $where['pd.name']=$keyword;
        } else if($this->MODULE_STATUS['product_status'] == "yes") {
            $this->db->select('pe.product_name');
            $this->db->join('package as pe','pe.prod_id=fi.product_id', "INNER");
            $where['pe.product_name']=$keyword;
        }
        if($keyword !='')
        {
        $this->db->group_start();
        $this->db->or_like($where);
        $this->db->group_end();
        }
        
        $this->db->where('fi.delete_status', "active");
        $this->db->where('fi.active', $status);
        $this->db->where('fi.user_type', 'user');
        $this->db->order_by('fi.id', 'DESC');
        
        $count = $this->db->count_all_results();

        return $count;
    }
    public function getCountMembers($keyword) {
        $this->db->select("fi.id, ud.user_detail_refid");
        $this->db->from("ft_individual as fi");
        $this->db->join("user_details as ud", "ud.user_detail_refid = fi.id", "INNER");
        $where = array('fi.user_name' => $keyword, 'ud.user_detail_name' => $keyword, 'ud.user_detail_address' => $keyword, 'ud.user_detail_mobile' => $keyword);
        $this->db->group_start()
                ->or_like($where)
                ->or_like("CONCAT( ud.user_detail_name,' ',ud.user_detail_second_name)", $keyword)
                ->group_end();
        $count = $this->db->count_all_results();

        return $count;
    }

    public function activateAccount($user_id, $type = 'auto') {
        $result = FALSE;
        $this->db->set('active', 'yes');
        $this->db->where('id', $user_id);
        $res = $this->db->update('ft_individual');
        if ($res) {
            $result = $this->usertActivationDeactivationHistory($user_id, $type, 'activated');
        }
        return $result;
    }

    public function usertActivationDeactivationHistory($user_id, $type, $status = '') {
        $this->db->set('user_id', $user_id);
        $this->db->set('type', $type);
        $this->db->set('status', $status);
        $result = $this->db->insert('user_activation_deactivation_history');
        return $result;
    }

    public function inactivateAccount($user_id, $type = 'auto') {
        $result = FALSE;
        $this->db->set('active', 'no');
        $this->db->where('id', $user_id);
        $res = $this->db->update('ft_individual');
        if ($res) {
            $result = $this->usertActivationDeactivationHistory($user_id, $type, 'deactivated');
        }
        return $result;
    }

    public function getLeadDetails($user_id = '', $keyword = '', $limit = '', $offset = '') {
        $this->db->select('l.id,f.user_name sponser_name,l.first_name,l.last_name,l.email_id email,l.skype_id,l.country,l.mobile_no phone,l.date,l.lead_status status,ud.user_detail_name,ud.user_detail_second_name');
        $this->db->from('crm_leads l');
        $this->db->join('ft_individual f', 'l.added_by = f.id');
        $this->db->join('user_details ud', 'l.added_by = ud.user_detail_refid');
        if ($keyword != '') {
            $where = array('l.first_name' => $keyword, 'l.last_name ' => $keyword, 'l.email_id' => $keyword, 'l.mobile_no' => $keyword, 'l.country' => $keyword, 'l.skype_id' => $keyword);
            $this->db->group_start()
                    ->or_like($where)
                    ->group_end();
        }
        if ($user_id) {
            $this->db->where('l.added_by', $user_id);
        }
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        $res = $this->db->get();
        return $res->result_array();
    }

    public function getLeadDetailsCount($user_id = '', $keyword = '') {
        $this->db->from('crm_leads l');
        if ($keyword != '') {
            $where = array('l.first_name' => $keyword, 'l.last_name ' => $keyword, 'l.email_id' => $keyword, 'l.mobile_no' => $keyword, 'l.country' => $keyword, 'l.skype_id' => $keyword);
            $this->db->or_group_start()
                    ->or_like($where)
                    ->group_end();
        }
        if ($user_id) {
            $this->db->where('l.added_by', $user_id);
        }
        return $this->db->count_all_results();
    }

    public function getLeadDetailsById($id) {
        $this->load->model('country_state_model');
        $leads = array();
        $this->db->select('*');
        $this->db->from('crm_leads');
        $this->db->where('id', $id);
        $this->db->limit(1);
        $res = $query = $this->db->get();
        foreach ($res->result_array() as $row) {
            $row['sponser_name'] = $this->validation_model->IdToUserName($row['added_by']);
            $row['country'] = $this->country_state_model->getCountryNameFromId($row['country']);
            $leads = $row;
        }

        return $leads;
    }

    public function addFollowup($det) {
        if ($det['admin_comment']) {
            $this->db->set('description', $det['admin_comment']);
            $this->db->set('lead_id', $det['lead_id']);
            $this->db->set('followup_entered_by', $this->LOG_USER_ID);
            $this->db->set('date', date('Y-m-d H:i:s'));
            return $this->db->insert('crm_followups');
        }
    }

    public function updateCRM($det) {
        $this->db->set('lead_status', $det['status']);
        $this->db->where('id', $det['lead_id']);
        return $this->db->update('crm_leads');
    }

    public function IdToUserName($user_id) {
        $user_name = NULL;
        $this->db->select('user_name');
        $this->db->from('ft_individual');
        $this->db->where('id', $user_id);
        $this->db->limit(1);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $user_name = $row->user_name;
        }
        return $user_name;
    }

    public function getadmin_name() {
        return $this->validation_model->getAdminUsername();
    }

    public function sendInvites($invite_details, $user_id) {
        $flag = 0;
        $myArray = explode(',', $invite_details['to_mail_id']);
        foreach ($myArray as $row) {
            $result = $this->sendInviteMails($invite_details['subject'], $invite_details['message'], $row);
            if ($result) {
                $flag = 1;
                $date = date('Y-m-d H:i:s');
                $this->db->set('user_id', $user_id);
                $this->db->set('mail_id', $row);
                $this->db->set('subject', $invite_details['subject']);
                $this->db->set('message', $invite_details['message']);
                $this->db->set('date', $date);
                $this->db->insert('invite_history');
            }
        }
        return $flag;
    }

    public function sendInviteMails($subject, $message, $email) {
        $regr = array();
        $mailBodyDetails = '<table border="1" width="100%" align="center">            
                             <tr>
                               <td><b>Name: </b>' . $subject . '</td>
                             </tr>
                             <tr>
                               <td><b>Membership ID #: </b>USA' . $message . '</td>
                             </tr>
                            </table>';
//        $result = $this->sendMail($mailBodyDetails, $subject, $email);
        $regr['mail_content'] = $mailBodyDetails;
        $regr['email'] = $email;
        $regr['first_name'] = '';
        $regr['last_name'] = '';

        $result = $this->mail_model->sendAllEmails('invaite_mail', $regr, array());
        return $result;
    }

    public function getInviteHistory($user_id, $limit, $offset) {
        $this->db->select('mail_id,subject,message,date');
        $this->db->from('invite_history');
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        $this->db->limit($limit, $offset);
        $res = $this->db->get();
        return $res->result_array();
    }

    public function getInviteHistoryCount($user_id) {
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        return $this->db->count_all_results('invite_history');
    }

    public function insertTextInvites($details) {
        $date = date('Y-m-d');
        $this->db->set('subject', $details['subject']);
        $this->db->set('content', $details['mail_content']);
        $this->db->set('uploaded_date', $date);
        $this->db->set('type', 'text');
        $res = $this->db->insert('invites_configuration');

        return $res;
    }

    public function getTextInvitesData($limit, $offset) {
        $this->db->select('id,subject,content,uploaded_date');
        $this->db->from('invites_configuration');
        $this->db->where('type', 'text');
        $this->db->order_by('id');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getTextInvitesDataCount() {
        $this->db->where('type', 'text');
        return $this->db->count_all_results('invites_configuration');
    }

    public function getAdminComent($lead_id) {
        $i = 0;
        $mail_details = array();
        $this->db->select('*');
        $this->db->from('crm_followups');
        $this->db->where('lead_id', $lead_id);
        $this->db->order_by('date', 'desc');
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $leads["detail$i"]['id'] = $row->description;
            $i++;
        }
        return $query->result_array();
    }

    public function getTextInvitesDataById($id) {
        $mail_details = array();
        $this->db->select('*');
        $this->db->from('invites_configuration');
        $this->db->where('type', 'text');
        $this->db->where('id', $id);
        $this->db->limit(1);
        $this->db->order_by('id');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $mail_details = $row;
        }
        return $mail_details;
    }

    public function editTextInvites($details) {
        $this->db->set('subject', $details['subject']);
        $this->db->set('content', $details['mail_content']);
        $this->db->where('id', $details['id']);
        $res = $this->db->update('invites_configuration');

        return $res;
    }

    public function deleteInviteText($id) {
        $this->db->where('id', $id);
        return $this->db->delete('invites_configuration');
    }

    public function insertsocialInvites($details, $type) {
        $this->db->set('subject', $details['subject']);
        $this->db->set('content', $details['message']);
        $this->db->set('type', $type);
        $res = $this->db->insert('invites_configuration');

        return $res;
    }

    public function getSocialInviteDataCount($type) {
        $this->db->where('type', $type);
        return $this->db->count_all_results('invites_configuration');
    }

    public function getSocialInviteData($type, $limit, $offset) {
        $social_details = array();
        $this->db->select('*');
        $this->db->from('invites_configuration');
        $this->db->where('type', $type);
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $social_details[$i]['id'] = $row['id'];
            $social_details[$i]['type'] = $row['type'];
            $subject = stripslashes($row['subject']);
            $content = stripslashes($row['content']);
            $subject1 = trim($subject);
            $content1 = trim($content);
            $subject2 = str_replace("\n", '', $subject1);
            $content2 = str_replace("\n", '', $content1);
            $social_details[$i]['subject'] = html_entity_decode($subject2);
            $social_details[$i]['content'] = html_entity_decode($content2);
            $i++;
        }
        return $social_details;
    }

    public function insertBanner($file_name, $target_url, $name) {
        $date = date('Y-m-d');
        $this->db->set('subject', $name);
        $this->db->set('target_url', $target_url);
        $this->db->set('content', $file_name);
        $this->db->set('type', 'banner');
        $this->db->set('uploaded_date', $date);
        return $res = $this->db->insert('invites_configuration');
    }

    public function getBanners($limit, $offset) {
        $this->db->select('id,subject,content,target_url,uploaded_date');
        $this->db->from('invites_configuration');
        $this->db->where('type', 'banner');
        $this->db->order_by('id');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getBannersCount() {
        $this->db->where('type', 'banner');
        return $this->db->count_all_results('invites_configuration');
    }

    public function deleteBanner($id) {
        $this->db->where('id', $id);
        return $this->db->delete('invites_configuration');
    }

    public function getPackageExpiredUsers($admin_id, $user_id, $page = '', $limit = '') {
        $user_details = array();
        $today = date("Y-m-d 23:59:59");

        $this->db->select("id,user_name,product_validity,sponsor_id,ft.active, ft.personal_pv,p.active,p.product_name, ft.product_id, CONCAT(ud.user_detail_name, ' ', ud.user_detail_second_name) AS full_name, ud.user_detail_email AS email");
        $this->db->from('ft_individual ft');
        $this->db->join("user_details as ud", "ud.user_detail_refid = ft.id", "LEFT");
        $this->db->join("package as p", "p.prod_id = ft.product_id", "LEFT");
        $this->db->where('id !=', $admin_id);
        $this->db->where('ft.product_id !=', '');
        $this->db->where('p.active !=', 'deleted');
        if ($user_id) {
            $this->db->where('id', $user_id);
        }
        if ($limit) {
            $this->db->limit($limit, $page);
        }
        $this->db->order_by('product_validity',"ASC");
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $user_details[$i]['user_id'] = $row['id'];

            $id_encode = $this->encryption->encrypt($row['user_name']);
            $id_encode = str_replace("/", "_", $id_encode);
            $encrypt_id = urlencode($id_encode);

            $user_details[$i]['user_name'] = $row['user_name'];
            $user_details[$i]['full_name'] = $row['full_name'];
            $user_details[$i]['email'] = $row['email'];
            $user_details[$i]['personal_pv'] = $row['personal_pv'];
            $user_details[$i]['product_validity'] = $row['product_validity'];
            $user_details[$i]['sponsor_name'] = $this->validation_model->IdToUserName($row['sponsor_id']);
            $user_details[$i]['encrypt_id'] = $encrypt_id;
            $user_details[$i]['product_id'] = $row['product_id'];
            $user_details[$i]['product_name'] = $row['product_name'];
            $user_details[$i]['user_img'] = $this->validation_model->getUserImage($row['id']);
            $i++;
        }
        return $user_details;
    }

    public function getPackageExpiredUsersCount($admin_id, $user_id = '') {
        $count = 0;
        $today = date("Y-m-d H:i:s");

        $this->db->select('id,p.active');
        $this->db->from('ft_individual ft');
        $this->db->join("package as p", "p.prod_id = ft.product_id", "LEFT");
        //   $this->db->where('product_validity <', $today);
        $this->db->where('id !=', $admin_id);
        $this->db->where('p.active !=', 'deleted');
        if ($user_id) {
            $this->db->where('id', $user_id);
        }

        $query = $this->db->get();
        $count = $query->num_rows();
        return $count;
    }

    public function packageValidityUpgrade($package_details, $purchase, $by_upgrade = FALSE, $pay_type = "manual") {
        $this->load->model('product_model');
        $today = date("Y-m-d H:i:s");
        $result = FALSE;

        $last_inserted_id = $this->getMaxPackageValidityOrderId();
        $invoice_no = 1000 + $last_inserted_id;
        $invoice_no = "VLDPCK" . $invoice_no;

        if ($by_upgrade) {
            $result = TRUE;
        } else {
            $product_pv = $this->product_model->getProductPvByPackageId($package_details[0]['id']);
            $this->db->set('user_id', $purchase['user_id']);
            $this->db->set('invoice_id', $invoice_no);
            $this->db->set('package_id', $package_details[0]['id']);
            $this->db->set('payment_type_used', $purchase['by_using']);
            $this->db->set('total_amount', $purchase['total_amount']);
            $this->db->set('product_pv', $product_pv);
            $this->db->set('date_submitted', $today);
            $this->db->set('pay_type', $pay_type);
            if(isset($purchase['order_id']) && $purchase['order_id']) {
                $this->db->set('renewal_details', $purchase['order_id']);
            }
            $result = $this->db->insert('package_validity_extend_history');
        }

        if ($result) {
            $result = $invoice_no;
            // Referral commission
            $renewal_type = 'renewal';
            $user_id             = $purchase['user_id'];
            $sponsor_id          = $this->validation_model->getSponsorId($user_id);
            $subscription_amount = $this->product_model->getProductSubscriptionAmount($package_details[0]['id']);

            $referal_commission_status = $this->validation_model->getCompensationConfig(['referal_commission_status']);
          
            if ($referal_commission_status == "yes") {
                $this->calculation_model->calculateReferralCommission($sponsor_id, $user_id, $subscription_amount,$renewal_type);
            }
           //agent_share
           
            $user_countryid = $this->validation_model->getUserCountryId($user_id);
            $agent_id       = $this->validation_model->getAgentIdByCountry($user_countryid);
            $agent_status = $this->validation_model->CheckAgentStatus($agent_id);
            if($agent_status == 'yes'){
                $wallet_id =5;
            }
            if($wallet_id !=''){
                $wallet_amount  =$this->validation_model->get_Wallet_Amount($wallet_id);
                $this->validation_model->updateAgentWallet($agent_id,$wallet_amount);
                $this->validation_model->addAgentwalletHistory($agent_id, $user_id, 0, 'commission', $wallet_amount, 'commission', 'credit',0,'',0,0);
            }
           //
            $validity_date = $this->getValidityDate($purchase['user_id']);
            if ($validity_date < $today) {
                $expiry_date = $this->product_model->getPackageValidityDate($package_details[0]['id'], '' , $this->MODULE_STATUS);
            } else {
                $expiry_date = $this->product_model->getPackageValidityDate($package_details[0]['id'], $validity_date,$this->MODULE_STATUS);
            }
            $this->db->set("product_validity", $expiry_date);
            $this->db->where('id', $purchase['user_id']);
            $update_ft = $this->db->update('ft_individual');
            if (!$update_ft) {
                return FALSE;
            }
        }

        return $result;
    }

    public function getMaxPackageValidityOrderId() {
        $max_id = 0;
        $this->db->select_max('id');
        $this->db->from('package_validity_extend_history');
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $max_id = $row->id;
        }
        return $max_id;
    }

    public function getValidityDate($user_id) {
        $validity_date = 0;
        $this->db->select('product_validity');
        $this->db->from('ft_individual');
        $this->db->where('id', $user_id);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $validity_date = $row->product_validity;
        }
        return $validity_date;
    }

    public function getSocialInvitesDataById($id, $type) {
        $mail_details = array();
        $this->db->select('*');
        $this->db->from('invites_configuration');
        $this->db->where('type', $type);
        $this->db->where('id', $id);
        $this->db->limit(1);
        $this->db->order_by('id');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $mail_details = $row;
        }
        return $mail_details;
    }

    //Replication site home page 
    public function insertBannerforReplica( $file_name, $user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->from("replica_banner");
        $count = $this->db->count_all_results();
        $this->db->set('banner', $file_name);
        $this->db->set('user_id', $user_id);
        
        if( $count != 0 && $user_id){        
            $this->db->where('user_id', $user_id);
            return $res = $this->db->update('replica_banner');
        }  else {
            return $res = $this->db->insert('replica_banner');
        }
    }

    //Replication site home page ends
    public function deleteReplicaBanner($id) {
        $this->db->where('id', $id);
        return $this->db->delete('replica_banners');
    }

    public function getStatus($id) {
        $status = "no";
        $this->db->select("status");
        $this->db->from("infinite_mlm_menu");
        $this->db->where('id', $id);
        $qr = $this->db->get();
        foreach ($qr->result() as $row) {
            $status = $row->status;
        }
        return $status;
    }

    public function upgradePackageDetails($user_id, $current_package_id, $new_package_id, $payment_type, $done_by, $module_status) {

        $this->load->model('upgrade_model');

        $this->db->set('product_id', $new_package_id);
        $this->db->where('id', $user_id);
        $res1 = $this->db->update('ft_individual');

        $data = [
            'user_id' => $user_id,
            'current_package_id' => $current_package_id,
            'new_package_id' => $new_package_id,
            'payment_type' => $payment_type,
            'done_by' => $done_by
        ];
        $res2 = $this->db->insert('package_upgrade_history', $data);

        $data2 = [
            'user_id' => $user_id,
            'package_id' => $new_package_id,
            'payment_method' => $payment_type
        ];
        $res3 = $this->db->insert('upgrade_sales_order', $data2);

        $res4 = TRUE;

        if ($module_status['roi_status'] == "yes") {
            $product_details = $this->upgrade_model->getProduct($new_package_id);
            $roi = $product_details['roi'];
            $days = $product_details['days'];
            $pack_amount = $product_details['product_value'];
            $data5 = [
                'user_id' => $user_id,
                'prod_id' => $new_package_id,
                'amount' => $pack_amount,
                'payment_method' => $payment_type,
                'roi' => $roi,
                'days' => $days
            ];

            $res4 = $this->db->insert('roi_order', $data5);
        }

        if ($module_status['subscription_status'] == 'yes') {
            $package_details = [];
            $package_details[0]['id'] = $new_package_id;
            $purchase = [];
            $purchase['by_using'] = $payment_type;
            $purchase['user_id'] = $user_id;
            $purchase['total_amount'] = 0;
            $res4 = $this->packageValidityUpgrade($package_details, $purchase);
        }
        return $res1 && $res2 && $res3 && $res4;
    }

    public function getSocialInvitesTypeById($id) {

        $type = NULL;
        $this->db->select('*');
        $this->db->from('invites_configuration');
        $this->db->where('id', $id);
        $this->db->limit(1);
        $this->db->order_by('id');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $type = $row['type'];
        }
        return $type;
    }

    public function getSocialInvitesById($id) {

        $mail_details = array();
        $this->db->select('*');
        $this->db->from('invites_configuration');
        $this->db->where('id', $id);
        $this->db->limit(1);
        $this->db->order_by('id');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $mail_details = $row;
        }
        return $mail_details;
    }

    public function insertManualPVUpdateHistory($user_id, $new_pv, $total_pv, $old_pv, $action) {
        $date = date('Y-m-d H:i:s');
        $this->db->set('user_id', $user_id);
        $this->db->set('pv_added', $new_pv);
        $this->db->set('new_pv', $total_pv);
        $this->db->set('old_pv', $old_pv);
        $this->db->set('type', $action);
        $this->db->set('date', $date);
        $res = $this->db->insert('manual_pv_update_history');
        return $res;
    }

    public function getProductPV($product_id) {
        $pair_value = 0;
        $this->db->select("pair_value");
        $this->db->from("package");
        $this->db->where("product_id", $product_id);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $pair_value = $row->pair_value;
        }
        return $pair_value;
    }

    public function getPayeerSettings($type = '') {
        $details = array();
        $this->db->select('*');
        $this->db->from('payeer_settings');
        if ($type)
            $this->db->where('account', $type);
        $this->db->order_by('id', 'DESC');
        $result = $this->db->get();
        foreach ($result->result() as $row) {
            $details['merchant_id'] = $row->merchant_id;
            $details['merchant_key'] = $row->merchant_key;
            $details['encryption_key'] = $row->encryption_key;
        }
        return $details;
    }
    public function getMemberDetails($page = '', $limit = ''){
      $this->db->select('fi.*, ud.*');
      $this->db->from("ft_individual as fi");
      $this->db->join("user_details as ud", "ud.user_detail_refid = fi.id", "INNER");
      $this->db->order_by('fi.date_of_joining' , 'desc');
      $this->db->limit($limit, $page);
      $result = $this->db->get();
      
      return $result->result_array();
    }
    public function getMemberDetailsPOfUser($user_id){
      $this->db->select('fi.*, ud.*');
      $this->db->from("ft_individual as fi");
      $this->db->join("user_details as ud", "ud.user_detail_refid = fi.id", "INNER");
      $this->db->where('fi.id',$user_id);
      $result = $this->db->get();
      
      return $result->result_array();
    }

    public function searchMemberDetails($user_name = "", $status = "yes", $limit, $offset) {
        $this->db->select("fi.id, fi.user_name,fi.active,CONCAT(ud.user_detail_name, ' ', ud.user_detail_second_name) AS full_name, ud.user_detail_mobile, ud.user_detail_email, rd.rank_name, ft.user_name sponsor_name");
        $this->db->from("ft_individual as fi");
        $this->db->join("user_details as ud", "ud.user_detail_refid = fi.id", "LEFT");
        $this->db->join('rank_details as rd','rd.rank_id=fi.user_rank_id', "LEFT");
        $this->db->join('ft_individual as ft','ft.id=fi.sponsor_id', "LEFT");
        if($this->MODULE_STATUS['opencart_status'] == "yes") {
            $lang_id = $this->session->userdata('inf_language')['lang_id'] ?: 1;
            $this->db->select('pd.name as product_name');
            $this->db->join('oc_product as pe','pe.package_id=fi.product_id', "LEFT");
            $this->db->join('oc_product_description as pd', 'pd.product_id = pe.product_id', 'LEFT');
            $this->db->where('pd.language_id', $lang_id);
        } else {
            $this->db->select('pe.product_name');
            $this->db->join('package as pe','pe.prod_id=fi.product_id', "LEFT");
        }
        $this->db->where('fi.user_type', 'user');
        $this->db->where('fi.active', $status);
        $this->db->where('fi.delete_status', "active");
        if($user_name != "") {
            $this->db->where('fi.user_name', $user_name);
        }
        $this->db->order_by('fi.id', 'DESC');
        $this->db->limit($limit, $offset);
        $query=$this->db->get();
        return $query->result_array();
    }

    public function searchMemberDetailsCount($user_name = "", $status = "yes") {
        $this->db->select("COUNT(fi.id) AS total");
        $this->db->from("ft_individual as fi");
        $this->db->join("user_details as ud", "ud.user_detail_refid = fi.id", "LEFT");
        $this->db->join('rank_details as rd','rd.rank_id=fi.user_rank_id', "LEFT");
        $this->db->join('ft_individual as ft','ft.id=fi.sponsor_id', "LEFT");
        $this->db->join('package as pe','pe.prod_id=fi.product_id', "LEFT");
        $this->db->where('fi.user_type', 'user');
        $this->db->where('fi.active', $status);
        if($user_name != "") {
            $this->db->where('fi.user_name', $user_name);
        }
        $query=$this->db->get();
        return $query->row('total');
    }

    public function blockMembers($list) {
        $this->db->set('active','no');
        $this->db->where_in('id',$list);
        $this->db->UPDATE('ft_individual');
        return $this->db->affected_rows() >= 1 ? true : false;
    }   

    public function activateMembers($list) {
        $this->db->set('active','yes');
        $this->db->where_in('id',$list);
        $this->db->UPDATE('ft_individual');
        return $this->db->affected_rows() >= 1 ? true : false;
    }
 public function getPackageExpiredUsersNew($admin_id, $user_id, $page = '', $limit = '')
    {

        $this->db->select('ft.id,ft.user_name,p.subscription_value,ft.product_validity,ft.product_id,ft.active,p.product_name,p.active,p.product_value,ud.user_detail_name,ud.user_detail_second_name');
        $this->db->from('ft_individual ft');
        $this->db->join("user_details as ud", "ud.user_detail_refid = ft.id", "INNER");
        $this->db->join("package as p", "p.prod_id = ft.product_id", "LEFT");
        $this->db->where('ft.id !=', $admin_id);
        $this->db->where('ft.product_id !=', '');
        $this->db->where('p.active !=', 'deleted');
        if ($user_id) {
            $this->db->where('ft.id', $user_id);
        }
        $this->db->order_by('ft.product_validity',"ASC");
        if ($limit) {
            $this->db->limit($limit, $page);
        }
        
        $query = $this->db->get();

        $this->load->model('configuration_model');
        $subscription_config = $this->configuration_model->getSubscriptionConfig();
        $details = $query->result_array();

        if($subscription_config['based_on'] == 'amount_based'){

           foreach ($query->result_array() as $key => $value) {
             
              $value['product_value'] = $subscription_config['fixed_amount'];

              $details[$key] =  $value;  
             
           }

        }
        return $details;
    

}
   public function InsertIntopendingRenewal($package_details, $purchase,$receipt = '',$pay_type = "manual"){

    $renewal_details = array();
    $renewal_details['package_details'] = $package_details;
    $renewal_details['purchase'] = $purchase;
    $user_id = $purchase['user_id'];
    
    $renewal_details = serialize($renewal_details);

    $last_inserted_id = $this->getMaxPackageValidityOrderId();
    $invoice_no = 1000 + $last_inserted_id;
    $invoice_no = "VLDPCK" . $invoice_no;

    $product_pv = $this->product_model->getProductPvByPackageId($package_details[0]['id']);
    $this->db->set('user_id', $purchase['user_id']);
    $this->db->set('invoice_id', $invoice_no);
    $this->db->set('package_id', $package_details[0]['id']);
    $this->db->set('payment_type_used', $purchase['by_using']);
    $this->db->set('total_amount', $purchase['total_amount']);
    $this->db->set('product_pv', $product_pv);
    $this->db->set('date_submitted', date('Y-m-d H:i:s'));
    $this->db->set('pay_type', $pay_type);
    $this->db->set('renewal_details', $renewal_details);
    $this->db->set('renewal_status', 'pending');
    $this->db->set('receipt', $receipt);
    $res = $this->db->insert('package_validity_extend_history');

    return $res;

   }
   public function getPendingSubscriptionCount(){

    $this->db->where('renewal_status', 'pending');
        return $this->db->count_all_results('package_validity_extend_history');

   }

   public function getPendingSubscriptions($page, $limit)
    {
        $this->load->model('country_state_model');
        $this->load->model('product_model');

        $this->db->select('*');
        $this->db->where('renewal_status', 'pending');
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit, $page);
        $query = $this->db->get('package_validity_extend_history');
        $details = array();
        
        foreach ($query->result_array() as $k => $v) {

            $details[$k]['pending_id'] = $v['id'];
            $details[$k]['reciept'] = $v['receipt'];
            $details[$k]['user_name'] = $this->validation_model->IdToUserName($v['user_id']);
            $details[$k]['invoice_id'] = $v['invoice_id'];
            $details[$k]['payment_method'] = $v['payment_type_used'];
            $details[$k]['total_amount'] = $v['total_amount']; 
            
        }
        return $details;
    }
    public function getRenewalDetails($pending_id){

        $this->db->select('renewal_details');
        $this->db->where('id', $pending_id);
        $this->db->where('renewal_status', 'pending');
        $this->db->limit(1);
        $query = $this->db->get('package_validity_extend_history');

        return $query->result_array()[0]['renewal_details'];

    }
    public function updateSubscriptionPendingStatus($pending_id){

        $this->db->set('renewal_status', 'approved');
        $this->db->where('id', $pending_id);
        $res = $this->db->update('package_validity_extend_history');
        return $res;

    }

    public function insertDefaultBannerforReplica($file_name) {
        $this->db->where('user_id', NULL);
        $this->db->from("replica_banner");
        $this->db->set('banner', $file_name);
        
        return $res = $this->db->update('replica_banner');
 
    }
    public function getDefaultBanner(){

        $this->db->select('banner');
        $this->db->where('user_id', NULL);
        $res = $this->db->get('replica_banner');

        return $res->result_array()[0]['banner'];
    }
    
    /**
     * [getpendingUpgrdes description]
     * @param  [type] $page          [description]
     * @param  [type] $limit         [description]
     * @param  [type] $module_status [description]
     * @return [type]                [description]
     */
    public function getpendingUpgrdes($page, $limit, $module_status){
        $pendings = $this->db->select('pn.*, ft.user_name')
            ->from('package_upgrade_history as pn')
            ->join('ft_individual as ft', 'ft.id = pn.user_id', 'left')
            ->where('status', 'pending')
            ->limit($limit, $page)
            ->get()
            ->result_array();
        foreach($pendings as $key => $pending) {
            if($module_status['opencart_status'] == 'yes') {
                $pendings[$key]['current_package'] = $this->validation_model->getOpencartProductNameFromUserID($pending['user_id']); 
                $pendings[$key]['new_package']     = $this->product_model->getOpencartPackageNameNew($pending['new_package_id'], 'registration');
            } else {
                $pendings[$key]['current_package'] = $this->validation_model->getProductNameFromUserID($pending['user_id']);
                $pendings[$key]['new_package']     = $this->product_model->getPackageNameNew($pending['new_package_id'], 'registration');
            }
        }
        return $pendings;

    }

    /**
     * [getPendingPackageUpgradeDetails get status = pending where id param]
     * @param  [int] $id [id]
     * @return [array]     [row of the table]
     */
    public function getPendingPackageUpgradeDetails($id) {
        return $this->db->select('pn.*, ft.user_name')
            ->from('package_upgrade_history as pn')
            ->join('ft_individual as ft', 'ft.id = pn.user_id', 'left')
            ->where('pn.id', $id)
            ->where('status', 'pending')
            ->get()
            ->row_array();
    }

    public function countgetpendingUpgrdes(){
        return $this->db->from('package_upgrade_history')
            ->where('status', 'pending')
            ->count_all_results();
    }

    public function getPendingDetailsById($id){

        $this->db->select('upgrade_data');
        $this->db->where('id', $id);
        $res = $this->db->get("upgrade_pendings");
        return $res->result_array()[0]['upgrade_data'];

    }

    public function updatePendingUpdate($pending_id){
        $this->db->where('id', $pending_id);
        $this->db->set("status", 'approved');
        $res = $this->db->update('upgrade_pendings');
        return $res;
    }
    public function getPackageExpiredUsersOpenCart($admin_id, $user_id, $page = '', $limit = '') {
        $user_details = array();
        $today = date("Y-m-d 23:59:59");

        $this->db->select("id,user_name,product_validity,sponsor_id,ft.active, ft.personal_pv, ft.product_id, p.model AS product_name, CONCAT(ud.user_detail_name, ' ', ud.user_detail_second_name) AS full_name, ud.user_detail_email AS email");
        $this->db->from('ft_individual ft');
        $this->db->join("user_details as ud", "ud.user_detail_refid = ft.id", "LEFT");
        $this->db->join("oc_product as p", "p.package_id = ft.product_id", "LEFT");
//        if($user_id ==''){
//        $this->db->where("product_validity <", $today);
//        }
        $this->db->where('id !=', $admin_id);
        $this->db->where('ft.product_id !=', '');
        //$this->db->where('p.active !=', 'deleted');
        if ($user_id) {
            $this->db->where('id', $user_id);
        }
        if ($limit) {
            $this->db->limit($limit, $page);
        }
        $this->db->order_by('product_validity',"ASC");
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $user_details[$i]['user_id'] = $row['id'];

            $id_encode = $this->encryption->encrypt($row['user_name']);
            $id_encode = str_replace("/", "_", $id_encode);
            $encrypt_id = urlencode($id_encode);

            $user_details[$i]['user_name'] = $row['user_name'];
            $user_details[$i]['full_name'] = $row['full_name'];
            $user_details[$i]['email'] = $row['email'];
            $user_details[$i]['product_validity'] = $row['product_validity'];
            $user_details[$i]['personal_pv'] = $row['personal_pv'];
            $user_details[$i]['sponsor_name'] = $this->validation_model->IdToUserName($row['sponsor_id']);
            $user_details[$i]['encrypt_id'] = $encrypt_id;
            $user_details[$i]['product_id'] = $row['product_id'];
            $user_details[$i]['product_name'] = $row['product_name'];
            $user_details[$i]['user_img'] = $this->validation_model->getUserImage($row['id']);
            $i++;
        }
        return $user_details;
    }
    public function getPackageExpiredUsersNewOpencart($admin_id, $user_id, $page = '', $limit = '')
    {

        $this->db->select('ft.id,ft.user_name,ft.product_validity,ft.product_id,ft.active,p.model product_name,p.price product_value,ud.user_detail_name,ud.user_detail_second_name');
        $this->db->from('ft_individual ft');
        $this->db->join("user_details as ud", "ud.user_detail_refid = ft.id", "INNER");
        $this->db->join("oc_product as p", "p.package_id = ft.product_id", "LEFT");
        $this->db->where('ft.id !=', $admin_id);
        $this->db->where('ft.product_id !=', '');
        //$this->db->where('p.active !=', 'deleted');
        if ($user_id) {
            $this->db->where('ft.id', $user_id);
        }
        $this->db->order_by('ft.product_validity',"ASC");
        if ($limit) {
            $this->db->limit($limit, $page);
        }
        
        $query = $this->db->get();

        $this->load->model('configuration_model');
        $subscription_config = $this->configuration_model->getSubscriptionConfig();
        $details = $query->result_array();

        if($subscription_config['based_on'] == 'amount_based'){

           foreach ($query->result_array() as $key => $value) {
             
              $value['product_value'] = $subscription_config['fixed_amount'];

              $details[$key] =  $value;  
             
           }

        }
        return $details;
    

   }

   public function getCwalletDetails($page = '', $limit = '', $type = '',$from_date = '',$to_date = '',$user_id = ''){
        $user_details = [];
        $this->db->select('*');
        // $this->db->where('id', $pending_id);
        // $this->db->where('renewal_status', 'pending');
        if ($limit) {
            $this->db->limit($limit, $page);
        }
          if ($type != '') {
            $this->db->where_in('wallet_type', $type);
        }
        if ($from_date != '') {
            $this->db->where("date_added >=", $from_date);
        }
        if ($to_date != '') {
            $this->db->where("date_added <=", $to_date);
        }
        if ($user_id != '') {
            $this->db->where("user_id", $user_id);
        } 
        $query = $this->db->get('cwallet_history');
        $i = 0;
        foreach ($query->result_array() as $row) {
// dd($row['user_id']);
            $user_details[$i]['user_name'] = $this->validation_model->IdToUserName($row['user_id']);
            $user_details[$i]['from_name'] = $this->validation_model->IdToUserName($row['from_id']);
            $user_details[$i]['wallet_type'] = $row['wallet_type'];
            $user_details[$i]['wallet_amount'] = $row['wallet_amount'];
            $user_details[$i]['amount'] = $row['amount'];
            $user_details[$i]['date_added'] = $row['date_added'];
            
            $i++;
        }
        return $user_details;

    }

    public function getCwalletDetailsCount(){
        $this->db->from('cwallet_history');
        $count = $this->db->count_all_results();
        return $count;
    }

    public function getCwalletOverviewTotal($user_id = "")
    {
        $wallet_total = [
            'credit_anb' => 0,
            'credit_panda' => 0,
            'credit_hajar' => 0,
            'credit_raed' => 0,
            'credit_agent' => 0,
        ];

        $amount_types = [];
        $wallet = array('0' => 'anm','1' =>'hajar','2'=>'panda','3'=>'raed','4'=>'agent' );
        // foreach ($wallet as $key => $value) {
        //     $this->register_model->addToCwallet($regr, $status['id'], $value,'registration');
        // }
        $this->db->select("SUM(wallet_amount) as total", false);
        $this->db->select('wallet_type');
        // if($user_id) {
        //     $this->db->where('user_id', $user_id);
        // }
        $this->db->group_by('wallet_type');
        $query = $this->db->get('cwallet_history')->result_array();
        foreach ($query as $row) {
            // dd($row);
            if(isset($row['total'])){
                if ($row['wallet_type'] == 'anm') {
                    $wallet_total['credit_anm'] = $row['total'];
                }
                if ($row['wallet_type'] == 'hajar') {
                    $wallet_total['credit_hajar'] = $row['total'];
                }
                if ($row['wallet_type'] == 'panda') {
                    $wallet_total['credit_panda'] = $row['total'];
                }
                if ($row['wallet_type'] == 'raed') {
                    $wallet_total['credit_raed'] = $row['total'];
                }
                if ($row['wallet_type'] == 'agent') {
                    $wallet_total['credit_agent'] = $row['total'];
                }
            }else{
                $wallet_total['credit_anm'] = 0;
                $wallet_total['credit_hajar'] = 0;
                $wallet_total['credit_panda'] = 0;
                $wallet_total['credit_raed'] = 0;
                $wallet_total['credit_agent'] = 0;
            }
        }

        
        
        return $wallet_total;
    }

    public function AssignAgent($value='',$edit_id='')
    {
       
            $password = password_hash($value['agent_password'], PASSWORD_DEFAULT);
            $this->db->set('agent_firstname', $value['agent_firstname']);
            $this->db->set('agent_secondname', $value['agent_secondname']);
            $this->db->set('agent_email', $value['agent_email']);
            $this->db->set('agent_mobile', $value['agent_mobile']);
            $this->db->set('agent_username', $value['agent_username']);
            $this->db->set('agent_password', $password);
            if(isset($value['agent_country'])){
                $this->db->set('country_id', json_encode($value['agent_country']));
            }
            $this->db->set('status', 'yes');
            $this->db->set('date_created', date('Y-m-d H:i:s'));
            $result = $this->db->insert('agents');
            $agent_id= $this->db->insert_id();
            $this->db->set('user_id',$agent_id);
            $this->db->insert('agent_wallet');
            return $result;
    }

    public function getAgentDetails($page ='' ,$limit = '')
    {
        $this->db->select('*');
        // $this->db->where('id', $pending_id);
        // $this->db->where('renewal_status', 'pending');
        if ($limit) {
            $this->db->limit($limit, $page);
        }
        $query = $this->db->get('agents');
        $i = 0;
        // dd($query->result_array());
        foreach ($query->result_array() as $row) {
// dd($row['user_id']);
            $user_details[$i]['user_id'] = $row['id'];
            $user_details[$i]['user_name'] = $row['agent_username'];
            $user_details[$i]['full_name'] = $this->validation_model->getAgentFullName($row['id']);
            $user_details[$i]['wallet_total'] = $this->getAgentWalletTotal($row['id']);
            $user_details[$i]['country'] = $this->validation_model->getCountry($row['country_id']);
            $user_details[$i]['date_added'] = $row['date_created'];
            $user_details[$i]['status'] = $row['status'];
            
            $i++;
        }
        return $user_details;
    }
    
    public function getRedirectPageOnLogin($agent_id)
    {
        $redirect_page = 'epin';
        $this->db->select('module_status');
        $this->db->where('id', $agent_id);
        $query = $this->db->get('agents');
        $res = $query->row_array()['module_status'];
        $res = explode(',', $res);
        $res = array_diff($res, array('m#24,m#1'));
        if (count($res) === 1 && $res[0] == 'm#32') {
            $redirect_page = 'ticket_system/admin_home_page';
        }
        else {
            if (count($res) >= 1) {
                $res = array_map(function ($v) {
                    return substr($v, 0, strpos($v, '#'));
                }, $res);
                $res = array_unique($res);
                if (count($res) === 1 && $res[0] == '44') {
                    $redirect_page = 'crm/index';
                }
            }
        }
        return $redirect_page;
    }

    public function selectAgentDetails($edit_id)
    {
        $this->db->where('id', $edit_id);
        $query = $this->db->get('agents');
        foreach ($query->result_array() as $row) {
            $obj_arr['id'] = $row['id'];
            $obj_arr['agent_firstname'] = $row['agent_firstname'];
            $obj_arr['agent_secondname'] = $row['agent_secondname'];
            $obj_arr['agent_username'] = $row['agent_username'];
            $obj_arr['agent_country'] = $this->validation_model->getCountry($row['country_id']);
            $obj_arr['agent_country_id']= json_decode($row['country_id']);
            $obj_arr['agent_mobile'] = $row['agent_mobile'];
            $obj_arr['agent_email'] = $row['agent_email'];
            $obj_arr['agent_password'] = password_hash($row['agent_password'], PASSWORD_DEFAULT);
            $obj_arr['agent_wallet'] = $this->getAgentWalletTotal($row['id']);
            
        }
 
        return $obj_arr;
    }

    public function UpdateAssignAgent($value='',$edit_id='')
    {
        //$password = password_hash($value['agent_password'], PASSWORD_DEFAULT);
        $this->db->set('agent_firstname', $value['agent_firstname']);
        $this->db->set('agent_secondname', $value['agent_secondname']);
        $this->db->set('agent_email', $value['agent_email']);
        $this->db->set('agent_mobile', $value['agent_mobile']);
       // $this->db->set('agent_password', $password);
        if(isset($value['agent_country'])){

            $this->db->set('country_id', json_encode($value['agent_country']));
        }
        $this->db->set('status', 'yes');
        $this->db->set('date_created', date('Y-m-d H:i:s'));
        $this->db->where('id', $edit_id);
        $result = $this->db->update('agents');
        return $result;
    }

    public function inactivate_agent($agent_id)
    {
        $this->db->set('status', 'no');
        $this->db->where('id', $agent_id);
        $query = $this->db->update('agents');
        return $query;
    }

    public function activate_agent($agent_id)
    {
        $this->db->set('status', 'yes');
        $this->db->where('id', $agent_id);
        $query = $this->db->update('agents');
        return $query;
    }

    public function UpdateUserCountryDetails($value='')
    {
        dd($value);
    }

    public function getAgentWalletTotal($agent_id='')
    {
        $this->db->select("SUM(wallet_amount) as wallet_total");
        $this->db->join('user_details u','u.user_detail_refid=cwallet_history.from_id','left');
        $this->db->where('wallet_type', 'agent');
        $this->db->where('u.agent_id', $agent_id);
        $query = $this->db->get('cwallet_history')->row_array();
        if($query['wallet_total'] ==''){
            return 0;
        }else{
            return $query['wallet_total'];
        }
        
    }
    
    public function getTotalAdminWalletCount($agent_id='')
    {
        $this->db->from('agent_wallet_admin');
        $this->db->where('from_agent_id', $agent_id);
        $query =  $this->db->count_all_results();
        return $query;
        
    }
    
    public function addToAdminWalletFromAgent($agent_id = '',$data ='',$process='')
    {
        $count = $this->getTotalAdminWalletCount($agent_id);
        if($process == 'deactivate'){
            if($count != '' && $count >= 1){
                $this->db->set('amount', $data['agent_wallet']);
                $this->db->where('from_agent_id', $agent_id);
                $result = $this->db->update('agent_wallet_admin');  
            }else{
                 
                $this->db->set('from_agent_id', $agent_id);
                $this->db->set('amount', $data['agent_wallet']);
                $this->db->set('user_id', 32);
                $result = $this->db->insert('agent_wallet_admin'); 
            }
        }
        if($process == 'activate'){
            $this->db->set('amount', 0);
            $this->db->where('from_agent_id', $agent_id);
            $result = $this->db->update('agent_wallet_admin');
        }
        
        return $result;
    }

    public function getAgentDetailsCount($value='')
    {
        $this->db->select("*");
        $this->db->from("agents");
        $count = $this->db->count_all_results();
        return $count;
    }
    public function updateAgentPassword($value,$edit_id){
        $password = password_hash($value['agent_password'], PASSWORD_DEFAULT);
        $this->db->set('agent_password', $password);
        $this->db->where('id', $edit_id);
        return $this->db->update('agents');
    }
      public function getCwalletTypeList(){

        $this->db->select('wallet_type');
        $this->db->distinct();
        $query = $this->db->get('cwallet_history');
        $i = 0;
        foreach ($query->result_array() as $row) {
            $wallet_type[] = $row['wallet_type'];            
        }
        return $wallet_type;

    }

    public function userNameToID($user_name = ''){
        $user_id = $this->validation_model->userNameToID($user_name);
        return $user_id;
    }
}

