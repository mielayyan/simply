<?php

define("IN_WALLET", true);
require_once 'Inf_Controller.php';
require "../vendor/autoload.php";

use Blocktrail\SDK\BlocktrailSDK;

class member extends Inf_Controller
{

    public function leads()
    {

        $title = lang('lead');
        $this->set("title", $this->COMPANY_NAME . " | $title");
        $this->url_permission('lead_capture_status');

        $this->HEADER_LANG['page_top_header'] = lang('lead');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('lead');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $help_link = "Leads";


        $user_id = $this->LOG_USER_ID;
        $session_data = $this->session->userdata('inf_logged_in');
        $table_prefix = $session_data['table_prefix'];
        $prefix = str_replace('_', '', $table_prefix);
        $username = $this->member_model->IdToUserName($user_id);
        $key_word="";
         if ($this->input->get('search_lead')) {
            $key_word = ($this->input->get('keyword', true));
           
        }

        $count = $this->member_model->getLeadDetailsCount($user_id,$key_word);
        $page=$this->input->get('offset') ?: 0;
        
        $this->set("page", $page);

        $details = $this->member_model->getLeadDetails($user_id,$key_word,$this->PAGINATION_PER_PAGE,$page);

        $admin_name = $this->member_model->getadmin_name();
        $this->pagination->set_all('user/leads', $count);
        $this->set("id", $user_id);
        $this->set("prefix", $prefix);
        $this->set("help_link", $help_link);
        $this->set('key_word',$key_word);

        $this->set("admin_name", $admin_name);
        $this->set("details", $this->security->xss_clean($details));
        $this->set("tran_user_name", $username);
        $this->OPTIONAL_MODULE = true;
        $this->set('OPTIONAL_MODULE', $this->OPTIONAL_MODULE);

        $this->setView();
    }

    public function getleads($id = '')
    {
        if (!$id)
            $id = $this->input->post('id', true);
        $details = $this->member_model->getLeadDetailsById($id);
        $comment_admin = $this->member_model->getAdminComent($id);
        $pending_status = '';
        $following_status = '';
        $reg_status = '';
        $dec_status = '';
        $i = 1;
        $details = $this->security->xss_clean($details);
        $comment_admin = $this->security->xss_clean($comment_admin);
        if ($details['lead_status'] == 'Ongoing') {
            $following_status = 'selected';
        } elseif ($details['lead_status'] == 'Accepted') {
            $reg_status = 'selected';
        } elseif ($details['lead_status'] == 'Rejected') {
            $dec_status = 'selected';
        }
        if ($details) {
            if (!$details["first_name"])
                $details["first_name"] = 'NA';
            if (!$details["sponser_name"])
                $details["sponser_name"] = 'NA';
            if (!$details["email_id"])
                $details["email_id"] = 'NA';
            if (!$details["skype_id"])
                $details["skype_id"] = 'NA';
            if (!$details["mobile_no"])
                $details["mobile_no"] = 'NA';
            if (!$details["country"])
                $details["country"] = 'NA';
            if (!$details["date"])
                $details["date"] = 'NA';
            if (!$details["description"])
                $$details["description"] = 'NA';
        }
        $csrf_token_name = $this->CSRF_TOKEN_NAME;
        $csrf_token_value = $this->CSRF_TOKEN_VALUE;

        $this->set('details', $details);
        $this->set('comment_admin', $comment_admin);
        $this->set('following_status', $following_status);
        $this->set('reg_status', $reg_status);
        $this->set('dec_status', $dec_status);

        $this->setView();
    }

    public function edit_Lead_Capture()
    {
        $res1 = $res2 = false;
        if ($this->input->post('edit_lead')) {
            $det = $this->input->post(null, true);
            $det = $this->validation_model->stripTagsPostArray($det);
            $res1 = $this->member_model->addFollowup($det);
            $res2 = $this->member_model->updateCRM($det);
            $lead_details = $this->member_model->getLeadDetailsById($det['lead_id']);
            $lead_details['new_status'] = $det["status"];
            $lead_details['admin_comment'] = $det["admin_comment"];
            $lead_details['email'] = $lead_details["email_id"];

            if ($res1 && $res2) {
                $this->load->model('mail_model');
                $this->mail_model->sendAllEmails("lcp_reply", $lead_details);
                $this->redirect(lang('lead_capture_updated'), "member/leads", true);
            } else if ($res2) {
                $this->redirect(lang('lead_capture_updated'), "member/leads", true);
            } else {
                $this->redirect(lang('unable_to_update_lead_capture'), "member/leads", false);
            }
        }
    }

    public function invites($tab = 'tab1')
    {
        if($this->MODULE_STATUS['promotion_status'] != "yes") {
            $this->redirect(lang('permission_denied'), 'home/index', false);
        }
        
        $tab1 = $tab2 = $tab3 = $tab4 = $tab5 = $tab7 = $tab8 = '';
        switch ($tab) {
            case 'tab1':
                $tab1 = ' active';
                break;
            case 'tab2':
                $tab2 = ' active';
                break;
            case 'tab3':
                $tab3 = ' active';
                break;
            case 'tab4':
                $tab4 = ' active';
                break;
            case 'tab5':
                $tab5 = ' active';
                break;
            case 'tab7':
                $tab7 = ' active';
                break;
            case 'tab8':
                $tab8 = ' active';
                break;
            default:
                $tab1 = ' active';
        }

        $title = lang('invites');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('invites');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('invites');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $help_link = "Leads";
        $this->set("help_link", $help_link);


        $user_id = $this->LOG_USER_ID;

        $pagination1 = new Core_Inf_Pagination();
        $base_url1 = base_url() . "user/member/invites";
        $config1 = $pagination1->customize_style();
        $config1['base_url'] = $base_url1;
        $config1['per_page'] = 10;
        $total_rows1 = $this->member_model->getSocialInviteDataCount('social_email');
        $config1['total_rows'] = $total_rows1;
        $config1["uri_segment"] = 4;
        $pagination1->initialize($config1);
        if ($tab == 'tab1') {
            $page1 = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        } else {
            $page1 = 0;
        }
        $result_per_page1 = $pagination1->create_links();
        $this->set("result_per_page1", $result_per_page1);
        $this->set("page1", $page1);

        $pagination2 = new Core_Inf_Pagination();
        $base_url2 = base_url() . "user/member/invites/tab2";
        $config2 = $pagination2->customize_style();
        $config2['base_url'] = $base_url2;
        $config2['per_page'] = 10;
        $total_rows2 = $this->member_model->getInviteHistoryCount($user_id);
        $config2['total_rows'] = $total_rows2;
        $config2["uri_segment"] = 5;
        $pagination2->initialize($config2);
        if ($tab == 'tab2') {
            $page2 = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        } else {
            $page2 = 0;
        }
        $result_per_page2 = $pagination2->create_links();
        $this->set("result_per_page2", $result_per_page2);
        $this->set("page2", $page2);

        $pagination3 = new Core_Inf_Pagination();
        $base_url3 = base_url() . "user/member/invites/tab4/tab4";
        $config3 = $pagination3->customize_style();
        $config3['base_url'] = $base_url3;
        $config3['per_page'] = 10;
        $total_rows3 = $this->member_model->getBannersCount();
        $config3['total_rows'] = $total_rows3;
        $config3["uri_segment"] = 6;
        $pagination3->initialize($config3);
        if ($tab == 'tab4') {
            $page3 = ($this->uri->segment(6)) ? $this->uri->segment(6) : 0;
        } else {
            $page3 = 0;
        }
        $result_per_page3 = $pagination3->create_links();
        $this->set("result_per_page3", $result_per_page3);
        $this->set("page3", $page3);

        $pagination4 = new Core_Inf_Pagination();
        $base_url4 = base_url() . "user/member/invites/tab5/tab5/tab5";
        $config4 = $pagination4->customize_style();
        $config4['base_url'] = $base_url4;
        $config4['per_page'] = 10;
        $total_rows4 = $this->member_model->getTextInvitesDataCount();
        $config4['total_rows'] = $total_rows4;
        $config4["uri_segment"] = 7;
        $pagination4->initialize($config4);
        if ($tab == 'tab5') {
            $page4 = ($this->uri->segment(7)) ? $this->uri->segment(7) : 0;
        } else {
            $page4 = 0;
        }
        $result_per_page4 = $pagination4->create_links();
        $this->set("result_per_page4", $result_per_page4);
        $this->set("page4", $page4);

        $pagination5 = new Core_Inf_Pagination();
        $base_url5 = base_url() . "user/member/invites/tab3/tab3/tab3/tab3";
        $config5 = $pagination5->customize_style();
        $config5['base_url'] = $base_url5;
        $config5['per_page'] = 10;
        $total_rows5 = $this->member_model->getSocialInviteDataCount('social_fb');
        $config5['total_rows'] = $total_rows5;
        $config5["uri_segment"] = 8;
        $pagination5->initialize($config5);
        if ($tab == 'tab3') {
            $page5 = ($this->uri->segment(8)) ? $this->uri->segment(8) : 0;
        } else {
            $page5 = 0;
        }
        $result_per_page5 = $pagination5->create_links();
        $this->set("result_per_page5", $result_per_page5);
        $this->set("page5", $page5);

        $pagination7 = new Core_Inf_Pagination();
        $base_url7 = base_url() . "user/member/invites/tab7/tab7/tab7/tab7/tab7/tab7";
        $config7 = $pagination7->customize_style();
        $config7['base_url'] = $base_url7;
        $config7['per_page'] = 10;
        $total_rows7 = $this->member_model->getSocialInviteDataCount('social_twitter');
        $config7['total_rows'] = $total_rows7;
        $config7["uri_segment"] = 10;
        $pagination7->initialize($config7);
        if ($tab == 'tab7') {
            $page7 = ($this->uri->segment(10)) ? $this->uri->segment(10) : 0;
        } else {
            $page7 = 0;
        }
        $result_per_page7 = $pagination7->create_links();
        $this->set("result_per_page7", $result_per_page7);
        $this->set("page7", $page7);

        $pagination8 = new Core_Inf_Pagination();
        $base_url8 = base_url() . "user/member/invites/tab8/tab8/tab8/tab8/tab8/tab8/tab8";
        $config8 = $pagination8->customize_style();
        $config8['base_url'] = $base_url8;
        $config8['per_page'] = 10;
        $total_rows8 = $this->member_model->getSocialInviteDataCount('social_instagram');
        $config8['total_rows'] = $total_rows8;
        $config8["uri_segment"] = 11;
        $pagination8->initialize($config8);
        if ($tab == 'tab8') {
            $page8 = ($this->uri->segment(11)) ? $this->uri->segment(11) : 0;
        } else {
            $page8 = 0;
        }
        $result_per_page8 = $pagination8->create_links();
        $this->set("result_per_page8", $result_per_page8);
        $this->set("page8", $page8);

        $invite_history_details = $this->member_model->getInviteHistory($user_id, $config2['per_page'], $page2);
        $invite_text = $this->member_model->getTextInvitesData($config4['per_page'], $page4);
        if ($invite_text) {
            $invite_text[0]['subject'] = html_entity_decode($invite_text[0]['subject']);
            $invite_text[0]['content'] = html_entity_decode($invite_text[0]['content']);
        }

        $social_invite_email = $this->member_model->getSocialInviteData('social_email', $config1['per_page'], $page1);
        $social_invite_fb = $this->member_model->getSocialInviteData('social_fb', $config5['per_page'], $page5);
        $social_invite_twitter = $this->member_model->getSocialInviteData('social_twitter', $config7['per_page'], $page7);
        $social_invite_instagram = $this->member_model->getSocialInviteData('social_instagram', $config8['per_page'], $page8);

        $banners = $this->member_model->getBanners($config3['per_page'], $page3);
        $this->set("banners", $this->security->xss_clean($banners));
        $this->set("base_url", $this->BASE_URL);

        $this->set("social_invite_email", $this->security->xss_clean($social_invite_email));
        $this->set("social_invite_fb", $this->security->xss_clean($social_invite_fb));
        $this->set("social_invite_twitter", $this->security->xss_clean($social_invite_twitter));
        $this->set("social_invite_instagram", $this->security->xss_clean($social_invite_instagram));
        $this->set("invite_text", $this->security->xss_clean($invite_text));
        $this->set("invite_history_details", $this->security->xss_clean($invite_history_details));

        if ($this->input->post('invite') && $this->validate_invite()) {
            //$invite_details = array();
            $invite_details = $this->input->post(null, true);
            $invite_details = $this->validation_model->stripTagsPostArray($invite_details);
            $invite_details['message'] = $this->validation_model->stripTagTextArea($this->input->post('message'));
            $result = $this->member_model->sendInvites($invite_details, $user_id);
            $to_id = $invite_details['to_mail_id'];
            if ($result == 1) {
                $data_array = array();
                $data_array['invite_details'] = $invite_details;
                $data = serialize($data_array);
                $this->validation_model->insertUserActivity($user_id, 'invitation sent', $user_id, $data);
                $msg = lang('invitation_send');
                $this->redirect($msg, "member/invites", true);
            } else {
                $msg = lang('unable_to_send_invitation');
                $this->redirect($msg, "member/invites", false);
            }
        }

        $this->set('tab1', $tab1);
        $this->set('tab2', $tab2);
        $this->set('tab3', $tab3);
        $this->set('tab4', $tab4);
        $this->set('tab5', $tab5);
        $this->set('tab7', $tab7);
        $this->set('tab8', $tab8);

        $this->setView();
    }

    public function validate_invite()
    {
        $this->form_validation->set_rules('to_mail_id', lang('email'), 'required');
        $this->form_validation->set_rules('subject', lang('subject'), 'required');
        $this->form_validation->set_rules('message', lang('message'), 'required');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    public function get_message($text_id)
    {

        $result = $this->member_model->getTextInvitesDataById($text_id);
        echo $result['content'];
    }

    public function get_subject($text_id)
    {

        $result = $this->member_model->getTextInvitesDataById($text_id);
        echo $result['subject'];
    }

    public function upgrade_package_validity($url_username = "") {

        //$this->url_permission('product_validity');
        $product_id = $this->validation_model->getProductId($this->LOG_USER_ID);
        if (!$product_id) {
            $msg = lang('invalid_package');
            $this->redirect($msg, 'home', false);
        }
        $this->load->model('product_model');
        $this->load->model('register_model');

        $title = lang('subscription_renewal');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('subscription_renewal');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('subscription_renewal');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $help_link = "package-validity";
        $this->set("help_link", $help_link);

        $user_id = $this->LOG_USER_ID;

        $this->set('user_id', $user_id);
        $module_status = $this->MODULE_STATUS;
        $package_id = $this->product_model->getProdId($product_id, $module_status, 'registration');
        //$product_status = $this->product_model->isProductAvailable($package_id);

        if($module_status['opencart_status'] == 'yes') {
  
         $package_id = $this->product_model->getProductIdFromPackageid($product_id);
         $product_status = $this->product_model->isProductAvailable($package_id);
         $expired_users = $this->member_model->getPackageExpiredUsersOpenCart($this->ADMIN_USER_ID, $user_id);

        }else{
         
         $product_status = $this->product_model->isProductAvailable($package_id);
         $expired_users = $this->member_model->getPackageExpiredUsers($this->ADMIN_USER_ID, $user_id);

        }
        
        if ($expired_users) {
            $expired_users = $expired_users[0];
        }
        $this->set("expired_users", $expired_users);
        $this->set("product_status", !$product_status);

        $pin_count = 0;
        if ($this->session->userdata("inf_package_validity_upgrade_post_array")) {
            $validity_post_array = $this->session->userdata("inf_package_validity_upgrade_post_array");
            $pin_count = $validity_post_array['pin_count'];
            $this->session->unset_userdata("inf_package_validity_upgrade_post_array");
        }
        $this->set('pin_count', $pin_count);

        // $product_amount = 0;
        // if ($expired_users) {
        //     $product_amount = $this->product_model->getProduct($expired_users['product_id']);
        // }
        $subscription_status = $this->MODULE_STATUS['subscription_status'];
        $subscription_config = $this->configuration_model->getSubscriptionConfig();
        if($subscription_status == 'yes' && $subscription_config['based_on'] == 'amount_based'){

        $product_amount = $subscription_config['fixed_amount'];     
        
        }else{
         
          if($module_status['opencart_status'] == 'yes') {
  
           $package_id = $this->product_model->getProductIdFromPackageid($expired_users['product_id']);
           $product_amount = $this->product_model->getProduct($package_id);
        
          }else{
            // $product_amount = $this->product_model->getProduct($expired_users['product_id']);  
            $product_amount = $this->product_model->getProductSubscriptionAmount($expired_users['product_id']);
          }         
        }
 
        $this->set('product_amount', $product_amount);

        $payment_methods_tab = false;
        $payment_gateway_array = array();
        $payment_module_status_array = array();

        if ($this->MODULE_STATUS['product_status'] == 'yes') {
            $payment_methods_tab = true;
            $payment_gateway_array = $this->register_model->getPaymentGatewayStatus("membership_renewal");
            $payment_module_status_array = $this->register_model->getPaymentModuleStatus();
            $payment_gateway_using_membership_status=$this->register_model->getPaymentGatewayUsingRegistration('membership_renewal');
        }
        $bank_details=$this->configuration_model->getBankInfo();
        $this->set('bank_details',$bank_details);
        $this->set('payment_methods_tab', $payment_methods_tab);
        $this->set('payment_gateway_array', $payment_gateway_array);
        $this->set('payment_module_status_array', $payment_module_status_array);
        $this->set('mlm_plan', $this->MLM_PLAN);
        $this->set('username_type', $this->LOG_USER_TYPE);
        $this->set('payment_gateway_using_membership_status',$payment_gateway_using_membership_status);

        $this->setView();
    }

    function package_validity_submit() {
        $this->load->model('repurchase_model');
        $package_validity_upgrade = $this->input->post(null, true);

        $module_status = $this->MODULE_STATUS;
        //        $payment_status = false;
        $is_free_join_ok = false;
        $is_pin_ok = false;
        $is_ewallet_ok = false;
        $is_paypal_ok = false;
        $is_authorize_ok = false;
        $is_blockchain_ok = false;
        $is_bitgo_ok = false;
        $is_bitcoin_ok = false;
        $is_sofort_ok = false;
        $is_payeer_ok = false;
        $is_squareup_ok = false;
        $is_bank_transfer_ok = false;


        $payment_gateway_array = $this->register_model->getPaymentGatewayStatus("membership_renewal");

        if($module_status['opencart_status'] == 'yes') {

         $expired_users = $this->member_model->getPackageExpiredUsersOpenCart($this->ADMIN_USER_ID, $package_validity_upgrade['user_id']);   

        }else{

         $expired_users = $this->member_model->getPackageExpiredUsers($this->ADMIN_USER_ID, $package_validity_upgrade['user_id']);
        }    

        if (empty($expired_users)) {
            $msg = $this->lang->line('Invalid_Epins');
            $this->redirect($msg, "member/upgrade_package_validity", false);
        }
        $expired_users = $expired_users[0];

        $subscription_status = $this->MODULE_STATUS['subscription_status'];
        $subscription_config = $this->configuration_model->getSubscriptionConfig();

        if($subscription_status == 'yes' && $subscription_config['based_on'] == 'amount_based'){

        $purchase['total_amount'] = $subscription_config['fixed_amount'];     
        
        }else{

            if($module_status['opencart_status'] == 'yes') {
  
            $package_id = $this->product_model->getProductIdFromPackageid($expired_users['product_id']);
            $purchase['total_amount'] = $this->product_model->getProduct($package_id);
        
            }else{
            
            // $purchase['total_amount'] = $this->product_model->getProduct($expired_users['product_id']);
            $purchase['total_amount'] = $this->product_model->getProductSubscriptionAmount($expired_users['product_id']); 
            
            }

        }
 

        $this->set('product_amount', $purchase['total_amount']);

       $product_id = $this->validation_model->getProductId($package_validity_upgrade['user_id']);

        if($module_status['opencart_status'] == 'yes') {
         
         $product_id = $this->product_model->getProductIdFromPackageid($product_id);
         //$package_id = $this->product_model->getProdId($product_id, $module_status, 'registration');


        }else{
         
         $package_id = $this->product_model->getProdId($product_id, $module_status, 'registration');
        
        }
        $product_status = $this->product_model->isProductAvailable($package_id);
        if (!$product_status) {
            $msg = $this->lang->line('your_product_currently_not_available');
            $this->redirect($msg, "member/upgrade_package_validity", false);
        }

        $purchase['user_id'] = $this->LOG_USER_ID;

        $package_details[0]['id'] = $expired_users['product_id'];
        $is_user_available = $this->validation_model->isUserAvailable($purchase['user_id']);
        if (!$is_user_available) {
            $msg = lang('invalid_user');
            $this->redirect($msg, 'member/upgrade_package_validity', false);
        }

        if ($package_validity_upgrade['active_tab'] == "epin_tab") {
            $payment_type = 'epin';
            $pin_count = count($package_validity_upgrade['epin']);
            $pin_details = $package_validity_upgrade['epin'];
            $pin_data = [];
            $i = 1;
            foreach ($pin_details as $v) {
                $pin_data[$i]['pin'] = $v;
                $pin_data[$i]['pin_amount'] = 0;
                $i++;
            }

            $pin_array = $this->repurchase_model->validateAllEpins($pin_data, $purchase['total_amount'], $this->LOG_USER_ID);


            $is_pin_ok = !(in_array('nopin', array_column($pin_array, 'pin')));
            if (!$is_pin_ok) {
                $msg = $this->lang->line('Invalid Epins');
                $this->redirect($msg, "member/upgrade_package_validity", false);
            }
            $is_pin_duplicate = (count(array_column($pin_array, 'pin')) != count(array_unique(array_column($pin_array, 'pin'))));
            if ($is_pin_duplicate) {
                $msg = $this->lang->line('duplicate_epin');
                $this->redirect($msg, "member/upgrade_package_validity", false);
            }
        } elseif ($package_validity_upgrade['active_tab'] == "ewallet_tab") {

            $payment_type = 'ewallet';
            $used_amount = $purchase['total_amount'];
            $ewallet_user = $package_validity_upgrade['user_name_ewallet'];
            $ewallet_trans_password = $package_validity_upgrade['tran_pass_ewallet'];
            if ($ewallet_user != "") {
                if ($this->LOG_USER_TYPE == 'user') {
                    if ($ewallet_user != $this->LOG_USER_NAME) {
                        $msg = $this->lang->line('invalid_user_name_ewallet_tab');
                        $this->redirect($msg, "member/upgrade_package_validity", false);
                    }
                }
                $ewallet_user_id = $this->validation_model->userNameToID($ewallet_user);

                $user_available = $this->validation_model->isUserAvailable($ewallet_user_id);
                if ($user_available) {
                    if ($ewallet_trans_password != "") {
                        $ewallet_user_id = $this->validation_model->userNameToID($ewallet_user);
                        $trans_pass_available = $this->register_model->checkEwalletPassword($ewallet_user_id, $ewallet_trans_password);
                        if ($trans_pass_available == 'yes') {

                            $ewallet_balance_amount = $this->register_model->getBalanceAmount($ewallet_user_id);
                            if ($ewallet_balance_amount >= $used_amount) {
                                $is_ewallet_ok = true;
                            } else {
                                $msg = $this->lang->line('insuff_bal');
                                $this->redirect($msg, "member/upgrade_package_validity", false);
                            }
                        } else {
                            $msg = $this->lang->line('invalid_transaction_password_ewallet_tab');
                            $this->redirect($msg, "member/upgrade_package_validity", false);
                        }
                    } else {
                        $msg = $this->lang->line('invalid_transaction_password_ewallet_tab');
                        $this->redirect($msg, "member/upgrade_package_validity", false);
                    }
                } else {
                    $msg = $this->lang->line('invalid_user_name_ewallet_tab');
                    $this->redirect($msg, "member/upgrade_package_validity", false);
                }
            } else {
                $msg = $this->lang->line('invalid_user_name_ewallet_tab');
                $this->redirect($msg, "member/upgrade_package_validity", false);
            }
        } elseif (($package_validity_upgrade['active_tab'] == "paypal_tab")) {
            if ($payment_gateway_array['paypal_status'] == "no") {
                $msg = lang('payment_method_not_available');
                $this->redirect($msg, "member/upgrade_package_validity", false);
            }
            $payment_type = 'paypal';
            $is_paypal_ok = true;
        } elseif (($package_validity_upgrade['active_tab'] == "authorize_tab")) {
            if ($payment_gateway_array['authorize_status'] == "no") {
                $msg = lang('payment_method_not_available');
                $this->redirect($msg, "member/upgrade_package_validity", false);
            }
            $payment_type = 'Athurize.Net';
            $is_authorize_ok = true;
        } elseif (($package_validity_upgrade['active_tab'] == "free_join_tab")) {
            $free_payment_status = $this->register_model->getPaymentStatus('Free Joining');
            if($free_payment_status == 'no'){
               $msg = lang('payment_method_not_available');
               $this->redirect($msg, "member/upgrade_package_validity", false);
            }
            $payment_type = 'free_purchase';
            $is_free_join_ok = true;
        } else if (($package_validity_upgrade['active_tab'] == "blockchain_tab")) {
            if ($payment_gateway_array['blockchain_status'] == "no") {
                $msg = lang('payment_method_not_available');
                $this->redirect($msg, "member/upgrade_package_validity", false);
            }
            $payment_type = 'blockchain';
            $is_blockchain_ok = true;
        } elseif (($package_validity_upgrade['active_tab'] == "bitgo_tab")) {
            if ($payment_gateway_array['bitgo_status'] == "no") {
                $msg = lang('payment_method_not_available');
                $this->redirect($msg, "member/upgrade_package_validity", false);
            }
            $payment_type = 'bitgo';
            $is_bitgo_ok = true;
        } else if (($package_validity_upgrade['active_tab'] == "bitcoin_tab")) {
            if ($payment_gateway_array['bitcoin_status'] == "no") {
                $msg = lang('payment_method_not_available');
                $this->redirect($msg, "member/upgrade_package_validity", false);
            }
            $payment_type = 'bitcoin';
            $is_bitcoin_ok = true;
        } elseif (($package_validity_upgrade['active_tab'] == "sofort_tab")) {
            if ($payment_gateway_array['sofort_status'] == "no") {
                $msg = lang('payment_method_not_available');
                $this->redirect($msg, "member/upgrade_package_validity", false);
            }
            $payment_type = 'sofort';
            $is_sofort_ok = true;
        } elseif (($package_validity_upgrade['active_tab'] == "payeer_tab")) {
            if ($payment_gateway_array['payeer_status'] == "no") {
                $msg = lang('payment_method_not_available');
                $this->redirect($msg, "member/upgrade_package_validity", false);
            }
            $payment_type = 'payeer';
            $is_payeer_ok = true;
        } elseif (($package_validity_upgrade['active_tab'] == "squareup_tab")) {
            if ($payment_gateway_array['squareup_status'] == "no") {
                $msg = lang('payment_method_not_available');
                $this->redirect($msg, "member/upgrade_package_validity", false);
            }
            $payment_type = 'squareup';
            $is_squareup_ok = true;
        } elseif (($package_validity_upgrade['active_tab'] == "bank_transfer")) {
                $payment_type = 'bank_transfer';
                $is_bank_transfer_ok = true;
        }

        $purchase['payment_type'] = $payment_type;

        if ($is_pin_ok) {
            $this->repurchase_model->begin();
            $purchase['by_using'] = 'pin';

            $pin_array['user_id'] = $purchase['user_id'];
            $res = $this->register_model->UpdateUsedEpin($pin_array, $pin_count, 'repurchase');
            if ($res) {
                $this->register_model->insertUsedPin($pin_array, $pin_count, false, 'package_validity');
                $payment_status = true;
            }
        } elseif ($is_ewallet_ok) {
            $this->repurchase_model->begin();
            $purchase['by_using'] = 'ewallet';
            $used_user_id = $this->validation_model->userNameToID($ewallet_user);
            $transaction_id = $this->repurchase_model->getUniqueTransactionId();
            $res1 = $this->register_model->insertUsedEwallet($used_user_id, $purchase['user_id'], $used_amount, $transaction_id, false, "package_validity");
            if ($res1) {
                $res2 = $this->register_model->deductFromBalanceAmount($used_user_id, $used_amount);
                if ($res2) {
                    $payment_status = true;
                }
            }
        } elseif ($is_paypal_ok) {
            $purchase['by_using'] = 'paypal';
            $this->session->set_userdata('inf_package_validity', $purchase);
            $this->session->set_userdata('package_details', $package_details);
            $msg = "";
            //            $this->payNow($package_details, $purchase);
            $this->redirect($msg, "/member/payNow/", false);
        } elseif ($is_authorize_ok) {
            $purchase['by_using'] = 'Authorize.Net';
            $this->session->set_userdata('inf_package_validity', $purchase);
            $msg = "";
            $this->redirect($msg, "/member/authorizeNetPayment/", false);
        } elseif ($is_free_join_ok) {
            $purchase['by_using'] = 'free join';
            $this->repurchase_model->begin();
            $payment_status = true;
        } elseif ($is_bank_transfer_ok) {
            $purchase['by_using'] = 'bank_transfer';
            
            $payment_receipt = $this->session->userdata('inf_payment_receipt');

            $pending_renewal = $this->member_model->InsertIntopendingRenewal($package_details, $purchase,$payment_receipt);

            $msg = lang('admin_approval_required');
            $this->redirect($msg, "/member/upgrade_package_validity/", TRUE);

        } elseif ($is_blockchain_ok) {
            $purchase['by_using'] = 'blockchain';
            $this->session->set_userdata('inf_package_validity', $purchase);
            $msg = "";
            $this->redirect($msg, "/member/blockchain", false);
        } elseif ($is_bitgo_ok) {
            $purchase['by_using'] = 'bitgo';
            $purchase['is_new'] = 'yes';
            $this->session->set_userdata('inf_package_validity', $purchase);
            $msg = "";
            $this->redirect($msg, "/member/bitgo_gateway/", false);
        } elseif ($is_sofort_ok) {
            $purchase['by_using'] = 'sofort';
            $this->session->set_userdata('inf_package_validity', $purchase);
            $msg = "";
            $this->redirect($msg, "/member/sofort_payment/", false);
        } elseif ($is_payeer_ok) {
            $purchase['by_using'] = 'payeer';
            $this->session->set_userdata('inf_package_validity', $purchase);
            $msg = "";
            $data = array(
                'user_id' => $package_validity_upgrade['user_id'],
                'product_id' => $expired_users['product_id'],
                'product_name' => $this->register_model->getProductName($expired_users['product_id']),
                'product_amount' => $purchase['total_amount'],
                'currency' => 'EUR',
            );
            $this->session->set_userdata('payeer_data', $data);
            $this->redirect($msg, "/member/payeer_payment/", false);
        } elseif ($is_squareup_ok) {
            $purchase['by_using'] = 'squareup';
            $this->session->set_userdata('inf_package_validity', $purchase);
            $msg = "";
            $this->redirect($msg, "/member/squareup_payment/", false);
        } 
        
        if ($payment_status) {

            $invoice_no = $this->member_model->packageValidityUpgrade($package_details, $purchase);
            $data = serialize($purchase);
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Membership reactivation through ' . lang($purchase['by_using']), $this->LOG_USER_ID, $data);

            if ($this->MLM_PLAN == "Stair_Step") {
                $this->repurchase_model->updateUserPv($package_details, $purchase);
            }

            if ($invoice_no) {
                $this->repurchase_model->commit();
                $this->session->unset_userdata('package_validity_upgrade_array');
                $this->session->unset_userdata('inf_package_validity_upgrade_array');
                $msg = lang('package_successfully_updated');
                $enc_order_id = $this->validation_model->encrypt($invoice_no);
                $this->redirect("<span><b>$msg </b> :  $invoice_no </span>", "member/upgrade_package_validity", true);
            } else {
                $this->repurchase_model->rollback();
                $msg = lang('package_updation_error');
                $this->redirect($msg, 'member/upgrade_package_validity', false);
            }
        } else {
            $this->repurchase_model->rollback();
            $msg = lang('payment_type_dosnot_selected');
            $this->redirect($msg, 'member/upgrade_package_validity', false);
        }
    }

    function payNow()
    {
        require(dirname(__FILE__) . '/../Paypal.php');
        $paypal = new Paypal;

        $this->load->model('repurchase_model');
        $cart_products = $this->session->userdata('package_details');

        $purchase_details = $this->session->userdata('inf_package_validity');
        $paypal_details = $this->configuration_model->getPaypalConfigDetails();


        $paypal_currency_code = "USD";
        $paypal_currency_left_symbol = "$";
        $paypal_currency_right_symbol = "";


        $default_currency_code = ($this->DEFAULT_CURRENCY_CODE != '') ? $this->DEFAULT_CURRENCY_CODE : "USD";

        $default_currency_left_symbol = ($this->DEFAULT_SYMBOL_LEFT != '') ? $this->DEFAULT_SYMBOL_LEFT : "$";
       
        $default_currency_right_symbol = ($this->DEFAULT_SYMBOL_RIGHT != '') ? $this->DEFAULT_SYMBOL_RIGHT : "";

        //        $usd_conevrsion_rate = $this->currency_model->getCurrencyConversionRate($default_currency_code, $paypal_currency_code);
        $usd_conevrsion_rate = 1;
        $total_amount = round($purchase_details['total_amount'] * $usd_conevrsion_rate, 8);
        $this->session->set_userdata('cart_products', $cart_products);


        // $this->load->library('merchant');
        // $this->merchant->load('paypal_express');

        $description = "Package Validity Upgrade " . $this->COMPANY_NAME;
        $description .= "\nPackage Amount : $paypal_currency_left_symbol $total_amount $paypal_currency_right_symbol";
        $product_status = $this->MODULE_STATUS['product_status'];

        $base_url = base_url();
        $params = array(
            'amount' => $total_amount,
            'item' => "Package Repuchase",
            'description' => $description,
            'currency' => $paypal_currency_code,
            'return_url' => $base_url . $this->LOG_USER_TYPE . "/" . $paypal_details['package_validity_return_url'],
            'cancel_url' => $base_url . $this->LOG_USER_TYPE . "/" . $paypal_details['package_validity_cancel_url']
        );
        $response = $paypal->initilize($params);
    }

    function payment_success()
    {
        require(dirname(__FILE__) . '/../Paypal.php');
        $paypal = new Paypal;
        $this->load->model('repurchase_model');

        $paypal_currency_code = "USD";
        $paypal_currency_left_symbol = "$";
        $paypal_currency_right_symbol = "";
        $default_currency_code = ($this->DEFAULT_CURRENCY_CODE != '') ? $this->DEFAULT_CURRENCY_CODE : "USD";
        $default_currency_left_symbol = ($this->DEFAULT_SYMBOL_LEFT != '') ? $this->DEFAULT_SYMBOL_LEFT : "$";
        $default_currency_right_symbol = ($this->DEFAULT_SYMBOL_RIGHT != '') ? $this->DEFAULT_SYMBOL_RIGHT : "";

        $usd_conevrsion_rate = $this->currency_model->getCurrencyConversionRate($default_currency_code, $paypal_currency_code);
        $purchase = $this->session->userdata('inf_package_validity');
        $total_amount = round($purchase['total_amount'] * $usd_conevrsion_rate, 8);

        $paypal_details = $this->configuration_model->getPaypalConfigDetails();

        $base_url = base_url();
        $params = array(
            'amount' => $total_amount,
            'currency' => $paypal_details['currency'],
            'return_url' => $base_url . $this->LOG_USER_TYPE . "/" . $paypal_details['package_validity_return_url'],
            'cancel_url' => $base_url . $this->LOG_USER_TYPE . "/" . $paypal_details['package_validity_cancel_url']
        );

        $response = $paypal->callback($params);

        if ($response->success()) {
            $paypal_output = $this->input->get();

            $user_id = $purchase["user_id"];
            $payment_details = array(
                'payment_method' => 'paypal',
                'token_id' => $paypal_output['token'],
                'currency' => $paypal_details['currency'],
                'amount' => $total_amount,
                'acceptance' => '',
                'payer_id' => $paypal_output['PayerID'],
                'user_id' => $user_id,
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
            $purchase['by_using'] = 'paypal';
            $this->repurchase_model->begin();

            $expired_users = $this->member_model->getPackageExpiredUsers($this->ADMIN_USER_ID, $purchase['user_id']);
            $package_details[0]['id'] = $expired_users[0]['product_id'];
            $invoice_no = $this->member_model->packageValidityUpgrade($package_details, $purchase);
            $data = serialize($purchase);
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Membership reactivation through ' . lang($purchase['by_using']), $this->LOG_USER_ID, $data);

            if ($this->MLM_PLAN == "Stair_Step") {
                $this->repurchase_model->updateUserPv($package_details, $purchase);
            }

            if ($invoice_no) {
                $this->repurchase_model->commit();
                $this->session->unset_userdata('package_validity_upgrade_array');
                $this->session->unset_userdata('inf_package_validity_upgrade_array');
                $msg = lang('package_successfully_updated');
                $enc_order_id = $this->validation_model->encrypt($invoice_no);
                $this->redirect("<span><b>$msg </b> :  $invoice_no </span>", "member/upgrade_package_validity", true);
            } else {
                $this->repurchase_model->rollback();
                $msg = lang('package_updation_error');
                $this->redirect($msg, 'member/upgrade_package_validity', false);
            }
        } else {
            $msg = 'Payment Failed';
            $this->redirect($msg, 'member/upgrade_package_validity', false);
        }
    }

    function authorizeNetPayment()
    {

        $this->load->model('repurchase_model');
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
        $purchase_details = $this->session->userdata('inf_package_validity');
        $total_amount = $purchase_details['total_amount'];

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

    public function edit_invite_wallpost()
    {
        if($this->MODULE_STATUS['promotion_status'] != "yes") {
            $this->redirect(lang('permission_denied'), 'home/index', false);
        }
        if ($this->input->post('invite_text_id')) {
            $title = 'Social Invite';
            $this->set("title", $this->COMPANY_NAME . " | $title");

            $this->HEADER_LANG['page_top_header'] = 'Email Invite';
            $this->HEADER_LANG['page_top_small_header'] = '';
            $this->HEADER_LANG['page_header'] = 'Email Invite';
            $this->HEADER_LANG['page_small_header'] = '';

            $this->load_langauge_scripts();

            $help_link = 'text invite';
            $this->set("help_link", $help_link);

            $edit_id = $this->input->post('invite_text_id', true);
            $type = $this->input->post('type', true);
            $mail_details = $this->member_model->getSocialInvitesDataById($edit_id, $type);
            $this->set('mail_details', $mail_details);
            if ($this->input->post('update')) {
                $update_post_array = $this->input->post(null, true);
                $update_post_array = $this->validation_model->stripTagsPostArray($update_post_array);
                $update_post_array['mail_content'] = $this->validation_model->stripTagTextArea($this->input->post('mail_content'));
                if ($this->validate_invite_text()) {
                    $mail_content['mail_content'] = $update_post_array['mail_content'];
                    $mail_content['subject'] = $update_post_array['subject'];
                    $mail_content['id'] = $update_post_array['invite_text_id'];
                    $res = $this->member_model->editTextInvites($mail_content);
                    if ($res) {
                        $data = serialize($update_post_array);
                        $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Social invite edited', $this->LOG_USER_ID, $data);

                        // Employee Activity History
                        if ($this->LOG_USER_TYPE == 'employee') {
                            $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'update_social_invite', 'Social Invite Updated');
                        }
                        //

                        $msg = lang('social_invite_updated');
                        $this->redirect($msg, "member/invite_wallpost_config", true);
                    } else {
                        $msg = lang('social_invite_not_updated');
                        $this->redirect($msg, "member/invite_wallpost_config", false);
                    }
                }
            }

            $this->setView();
        } else {
            $this->redirect('', "member/invites", true);
        }
    }
    
    /* Blockchain Starts */

    public function blockchain()
    {
        require(dirname(__FILE__) . '/../Blockchain.php');
        $blockchain = new Blockchain;
        $title = lang('pay_bitcoin');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('pay_bitcoin');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('pay_bitcoin');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $base_url = base_url();
        $date = date("Y-m-d H:i:s");
        if (empty($this->session->userdata("inf_package_validity"))) {
            $this->redirect("", 'member/upgrade_package_validity', false);
        }
        $product_id = $this->validation_model->getProductId($this->LOG_USER_ID);
        $purchase_details = $this->session->userdata("inf_package_validity");
        $total_amount = round((floatval($purchase_details['total_amount']) / $this->DEFAULT_CURRENCY_VALUE), 8);
        $invoice_id = time();
        $secret = $blockchain->getToken();

        $currency = "USD";
        $blockchain_root = "https://blockchain.info/";
        $price_in_btc = file_get_contents($blockchain_root . "tobtc?currency=$currency&value=" . $total_amount);

        $new_address = false;
        if ($this->register_model->getUnpaidAddressCount() >= 19) {
            if ($address = ($this->register_model->getUnpaidAddress()) ?: false) { } else {
                if ($this->LOG_USER_TYPE == 'admin') {
                    $this->redirect(lang('you_have_reached_maximum_unpaid_address'), 'member/upgrade_package_validity', false);
                } else {
                    $this->redirect(lang('payment_not_available_now'), 'member/upgrade_package_validity', false);
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
            $regr = $this->session->userdata("inf_package_validity");
            $regr['product_id'] = $product_id;
            $this->register_model->insertPaymentDetails($invoice_id, $address, $secret, $total_amount, $price_in_btc, $date, $regr, 'upgrade_package_validity');
        } else {
            $this->redirect("Something wrong", 'member/upgrade_package_validity', false);
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
        require(dirname(__FILE__) . '/../Blockchain.php');
        $blockchain = new Blockchain;
        if ($this->session->userdata('block_address') && $this->session->userdata('price_in_btc')) {
            $block_address = $this->session->userdata('block_address');
            $paid_amount = $this->session->userdata('price_in_btc');

            $purchase_details = $this->session->userdata("inf_package_validity");
            $total_amount = round((floatval($purchase_details['total_amount']) / $this->DEFAULT_CURRENCY_VALUE), 8);

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

            $purchase_details['user_name_entry'] = $this->member_model->IdToUserName($this->LOG_USER_ID);
            $purchase_details['block_address'] = $block_address;
            $purchase_details['paid_amount'] = $paid_amount;
            $purchase_details['response'] = $response;
            $this->register_model->keepRowAddressReponse($block_address, $invoice_id, $res_arr, 'Upgrade Package');
            $this->register_model->updateBitcoinAddress($block_address, 'yes');

            if ($response_amount > 0.00000001 && (round($response_amount, 8) >= round($paid_amount, 8))) {

                $purchase_details['by_using'] = 'blockchain';

                $expired_users = $this->member_model->getPackageExpiredUsers($this->ADMIN_USER_ID, $purchase_details['user_id']);
                $package_details[0]['id'] = $expired_users[0]['product_id'];
                $invoice_no = $this->member_model->packageValidityUpgrade($package_details, $purchase_details);
                $data = serialize($purchase_details);
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Membership reactivation through ' . lang($purchase['by_using']), $this->LOG_USER_ID, $data);

                $this->session->unset_userdata('inf_package_validity');
                $this->session->unset_userdata('block_address');
                $this->session->unset_userdata('price_in_btc');
                $this->session->unset_userdata('invoice_id');
                $msg = lang('package_upgradation_success');
                $this->redirect($msg, "member/upgrade_package_validity", true);
                exit();
                //unset all session
            } else {
                $msg = lang('package_upgradation_failed');
                $this->redirect($msg, 'member/upgrade_package_validity', false);
            }
        }
    }

    /* Blockchain Ends */

    /*BitGo Starts*/

    public function bitgo_gateway()
    {
        require(dirname(__FILE__) . '/../Bitgo.php');
        $bitgo = new Bitgo;

        $error = '';
        $this->set("action_page", $this->CURRENT_URL);
        $title = lang('bitgo_gateway');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $help_link = "";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('bitgo_gateway');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('bitgo_gateway');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $purchase_details = $this->session->userdata('inf_package_validity');
        $total_amount = round((floatval($purchase_details['total_amount']) / $this->DEFAULT_CURRENCY_VALUE), 8);

        $this->load->model("currency_model");
        $is_usd_default = $this->currency_model->isUSDDefault();
        if (!$is_usd_default) {
            $usd_details = $this->currency_model->getCurrencyDetailsById(1);
            $total_amount = $total_amount * $usd_details['value'];
        }

        if (!empty($this->session->userdata('bitcoin_session')) && $purchase_details['is_new'] == "no") {
            $btc_sess = $this->session->userdata('bitcoin_session');
            $pay_address = $btc_sess['bitcoin_address'];
            $sendAmount = $btc_sess['send_amount'];
        } else {

            try {
                $address = $bitgo->bitgo_gateway();
            } catch (Exception $e) {
                $msg = lang("initializing_wallet_failed_because") . ' ' . $e->getMessage();
                $this->redirect($msg, 'member/upgrade_package_validity', false);
            }
            $btc_amount = $this->currency_model->currencyToBtc('USD', $total_amount);
            $sendAmount = $btc_amount['btc_amount'];
            $user_id = $this->LOG_USER_ID;
            $p_id = $this->validation_model->getProductId($user_id);
            $pay_address = $address->address;
            $wallet_id = $address->wallet;
            $bitgo_hid = $this->register_model->insertIntoBitGoPaymentHistory($user_id, serialize($purchase_details), $p_id, $btc_amount['btc_amount'], $pay_address, serialize($address), $wallet_id);

            $bitcoin_session = array(
                'bitcoin_address' => $pay_address,
                'send_amount' => $btc_amount['btc_amount'],
                'bitgo_hid' => $bitgo_hid,
                'wallet_id' => $wallet_id
            );
            $this->session->set_userdata('bitcoin_session', $bitcoin_session);
            $_SESSION['purchase_details']['is_new'] = "no";
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
        require(dirname(__FILE__) . '/../Bitgo.php');
        $bitgo = new Bitgo;
        if (!empty($this->session->userdata('bitcoin_session'))) {

            $rs_arr = array();
            $bitcoin_address_array = $this->session->userdata('bitcoin_session');
            $bitcoin_address = $bitcoin_address_array['bitcoin_address'];
            $btc_amount = $bitcoin_address_array['send_amount'];
            $bitgo_hid = $bitcoin_address_array['bitgo_hid'];
            $wallet_id = $bitcoin_address_array['wallet_id'];
            $bitcoin_status = $bitgo->checkBitcoinPaymentStatus($bitcoin_address, $btc_amount, $bitgo_hid, $wallet_id);

            if ($bitcoin_status['status']) {
                if ($this->session->userdata('inf_package_validity')) {
                    $purchase_details = $this->session->userdata('inf_package_validity');
                    $purchase_details['by_using'] = 'BitGo';
                    $expired_users = $this->member_model->getPackageExpiredUsers($this->ADMIN_USER_ID, $purchase_details['user_id']);
                    $package_details[0]['id'] = $expired_users[0]['product_id'];
                    $invoice_no = $this->member_model->packageValidityUpgrade($package_details, $purchase_details);
                    $data = serialize($purchase_details);
                    $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Membership reactivation through ' . lang($purchase_details['by_using']), $this->LOG_USER_ID, $data);
                    $rs_arr['status'] = $bitcoin_status['status'];
                    echo json_encode($rs_arr);
                }
                //  echo json_encode($bitcoin_status);
            } else {
                $rs_arr['status'] = "Failed";
                echo json_encode($bitcoin_status);
            }
        } else {
            $rs_arr['status'] = "Failed";
            // $rs_arr['error'] = $bitcoin_status['msg'];
            echo json_encode($rs_arr);
            // $error = $bitcoin_status['msg'];
            //   $this->redirect(lang('current_session_expired'), 'register/user_register', false);
        }
    }

    function btc_confirm()
    {
        if (!empty($this->session->userdata('inf_package_validity'))) {
            $this->session->unset_userdata('inf_package_validity');
            $msg = lang('package_upgradation_success');
            $this->redirect($msg, "member/upgrade_package_validity", true);
        } else {
            $msg = lang('package_upgradation_failed');
            $this->redirect($msg, "member/upgrade_package_validity", false);
        }
    }
    /*BitGo Ends*/
    function check_menu_promotion()
    {

        $status = $this->member_model->getStatus(34);
        if ($status == "no") {
            $msg = lang('permission_denied');
            $this->redirect($msg, 'home/index', false);
        }
    }

    function getSocialInviteData()
    {
        $id = ($this->input->post('id', true));
        $details = $this->member_model->getSocialInvitesById($id);
        $value = json_encode($details);
        echo $value;
        exit();
    }

    function check_epin_validity()
    {
        $this->load->model('repurchase_model');
        $pin_details = $this->input->post('pin_array', true);
        $upgrade_user_name = $this->input->post('upgrade_user_name', true);
        $upgrade_user_id = $this->validation_model->userNameToID($upgrade_user_name);
        $pin_data = [];
        $i = 0;
        foreach ($pin_details as $v) {
            $pin_data[$i]['pin'] = $v;
            $pin_data[$i]['pin_amount'] = 0;
            $i++;
        }
        $total_amount = $this->input->post('repurchase_amount', true);
        $pin_array = $this->repurchase_model->validateAllEpins($pin_data, $total_amount, $this->LOG_USER_ID, $upgrade_user_id);
        $value = json_encode($pin_array);
        echo $value;
        exit();
    }

    function check_ewallet_balance()
    {
        $this->load->model('register_model');
        $status = "no";
        $ewallet_user = $this->input->post('user_name', true);
        $ewallet_pass = $this->input->post('ewallet', true);
        $total_amount = $this->input->post('repruchase_amount', true);
        $upgrade_username = $this->input->post('upgrade_username', true);
        if ($this->LOG_USER_TYPE == 'admin' || $this->LOG_USER_TYPE == 'employee') {
            $admin_username = $this->validation_model->getAdminUsername();
            if ($ewallet_user != $admin_username && $ewallet_user != $upgrade_username) {
                $status = "invalid";
                echo $status;
                exit();
            }
        }
        if ($this->LOG_USER_TYPE == 'user') {
            if ($ewallet_user != $this->LOG_USER_NAME) {
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

    function sofort_payment()
    {
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

        if ($this->session->userdata('inf_package_validity')) {
            $purchase_details = $this->session->userdata('inf_package_validity');
            $currency = 'EUR';
            $eur_conevrsion_rate = 0.87;
            $total_amount = round($purchase_details['total_amount'] * $eur_conevrsion_rate, 8);
            $user_name = $this->validation_model->getUserName($purchase_details['user_id']);
            $comment = 'Membership Reactivation of ' . $user_name . ' through ' . lang($purchase_details['by_using']);

            $this->set('comment', $comment);
            $this->set('amount', $total_amount);
            $this->set('currency', $currency);
            $this->setView();
        }
    }

    public function sofort_response()
    {

        require(dirname(__FILE__) . '/../SofortPay.php');
        $sofort = new SofortPay;

        $this->load->model("payment_model");
        $input = array();
        $input = $this->input->post(null, true);

        $result = $sofort->sofortResponse($input);
        if (!$result['status']) {
            $result = $this->payment_model->insertInToSofortProcessDetails($this->session->userdata('inf_package_validity'), $result['msg'], $this->LOG_USER_ID);
            $this->session->unset_userdata('inf_package_validity');
            $msg = lang('package_upgradation_failed');
            $this->redirect($msg, 'member/upgrade_package_validity', FALSE);
        }
    }

    public function sofort_success()
    {

        $this->load->model("payment_model");
        if ($this->session->userdata('inf_package_validity')) {

            $transaction_id = $this->session->userdata('transactionid');
            $purchase_details = $this->session->userdata('inf_package_validity');

            $payment_details = [
                'user_id' => $purchase_details['user_id'],
                'type' => 'Membership Reactivation',
                'status' => 'success',
                'total_amount' => $purchase_details['total_amount'],
                'transaction_id' => $transaction_id
            ];

            $result = $this->payment_model->insertIntoSofortPaymentHistory($payment_details);
            $purchase_details['by_using'] = 'sofort';
            $expired_users = $this->member_model->getPackageExpiredUsers($this->ADMIN_USER_ID, $purchase_details['user_id']);
            $package_details[0]['id'] = $expired_users[0]['product_id'];
            $invoice_no = $this->member_model->packageValidityUpgrade($package_details, $purchase_details);
            $data = serialize($purchase_details);
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Membership Reactivation of through ' . lang($purchase_details['by_using']), $this->LOG_USER_ID, $data);

            $this->session->unset_userdata('inf_package_validity');
            $msg = lang('package_successfully_updated');
            $this->redirect($msg, "member/upgrade_package_validity", TRUE);
            exit();
        } else {
            $msg = lang('package_upgradation_failed');
            $this->redirect($msg, 'member/upgrade_package_validity', FALSE);
        }
    }

    public function payeer_payment()
    {
        $title = lang('payeer');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('payeer');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('payeer');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        if ($this->session->userdata('payeer_data')) {
            $data = $this->session->userdata('payeer_data');
            $setting = $this->member_model->getPayeerSettings();
            $m_shop = $setting['merchant_id'];   //   merchant   ID
            $m_curr = 'EUR';   //   invoice   currency
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
            $this->setView();
        } else {
            $msg = lang('package_upgrade_error');
            $this->redirect($msg, 'upgrade/package_upgrade', false);
        }
    }

    public function payeer_success()
    {

        $this->load->model("payment_model");
        $session_data = $this->session->userdata('payeer_data');
        $module_status = $this->MODULE_STATUS;
        $payment_type = 'payeer';
        $total_amount = $session_data['payment_amount'];
        $user_id = $session_data['user_id'];
        $package_id = $session_data['package_id'];
        $product_id = $session_data['product_id'];
        $current_package_id = $this->validation_model->getProductId($user_id);
        $payment_details = array(
            'user_id' => $user_id,
            'purpose' => 'Package Upgrade',
            'amount' => $total_amount,
            'product_id' => $product_id,
            'status' => 'success',
            'currency' => 'EUR',
            'invoice_number' => '',
            'date' => date('Y-m-d H:i:s')
        );
        $this->payment_model->insertIntoPayeerOrderHistory($payment_details);
        $payeer_details = $this->configuration_model->getPayeerConfigurationDetails();
        $purchase_details = $this->session->userdata('inf_package_validity');
        if ($this->session->userdata('payeer_payment')) {

            $purchase_details['by_using'] = 'payeer';
            $expired_users = $this->member_model->getPackageExpiredUsers($this->ADMIN_USER_ID, $purchase_details['user_id']);
            $package_details[0]['id'] = $expired_users[0]['product_id'];
            $invoice_no = $this->member_model->packageValidityUpgrade($package_details, $purchase_details);
            $data = serialize($purchase_details);
            $login_id = $this->LOG_USER_ID;
            if ($this->LOG_USER_TYPE == 'admin') {
                $user_name = $this->validation_model->getUserName($purchase_details['user_id']);
                $this->validation_model->insertUserActivity($login_id, 'Membership Reactivation of ' . $user_name . ' through ' . lang($purchase_details['by_using']), $purchase_details['user_id'], $data);
            } else {
                $user_name = $this->validation_model->getUserName($login_id);
                $this->validation_model->insertUserActivity($login_id, 'Membership Reactivation of' . $user_name . ' through ' . lang($purchase_details['by_using']), $login_id, $data);
            }
            $this->session->unset_userdata('inf_package_validity');
            $msg = lang('package_upgradation_success');
            $this->redirect($msg, "member/upgrade_package_validity", TRUE);
            exit();
        } else {
            $this->inf_model->rollback();
            $msg = lang('package_upgrade_error');
            $this->redirect($msg, 'member/upgrade_package_validity', false);
        }
    }

    public function payeer_failure()
    {
        $this->register_model->rollback();
        $msg = lang('payeer_payment_error');
        $this->redirect($msg, 'member/upgrade_package_validity', false);
    }

    public function squareup_payment()
    {

        $title = lang('squareup_payment');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('squareup_payment');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('squareup_payment');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        if (empty($this->session->userdata('inf_package_validity'))) {
            $msg = lang('package_upgradation_failed');
            $this->redirect($msg, 'member/package_validity', FALSE);
        }

        $merchant_details = $this->configuration_model->getSquareUpConfigDetails();
        $application_id = $merchant_details['application_id'];
        $location_id = $merchant_details['location_id'];

        $purchase_details = $this->session->userdata('inf_package_validity');
        $payment_amount = $purchase_details['total_amount'];

        $total_amount = $payment_amount * 100; //USD in Cents
        $this->session->set_userdata('total_amount', $total_amount);

        $this->set('application_id', $application_id);
        $this->set('location_id', $location_id);

        $this->setView();
    }

    public function squareup_success()
    {

        require(dirname(__FILE__) . '/../Squareup.php');
        $squareup = new SquareUp;
        $this->load->model('payment_model');

        if (empty($this->session->userdata('inf_package_validity'))) {
            $msg = lang('package_upgradation_failed');
            $this->redirect($msg, 'member/package_validity', FALSE);
        }

        $purchase_details = $this->session->userdata('inf_package_validity');
        $total_amount = $this->session->userdata('total_amount');

        $merchant_details = $this->configuration_model->getSquareUpConfigDetails();
        $location_id = $merchant_details['location_id'];

        $nonce = $_POST['nonce'];
        if (is_null($nonce)) {
            $this->payment_model->insertSquareUpResponse($purchase_details, "Invalid card data", $this->LOG_USER_ID);
            $msg = lang('invalid_card_data');
            $this->redirect($msg, 'member/upgrade_package_validity', FALSE);
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
            $user_id = $purchase_details['user_id'];
            $user_name = $this->validation_model->IdToUserName($user_id);

            $insert_id = $this->payment_model->insertSquareUpPaymentDetails($user_id, $user_name, $request_body, 'Member Reactivation', $transaction_id, 'success');

            $purchase_details['by_using'] = 'squareup';
            $expired_users = $this->member_model->getPackageExpiredUsers($this->ADMIN_USER_ID, $purchase_details['user_id']);
            $package_details[0]['id'] = $expired_users[0]['product_id'];
            $invoice_no = $this->member_model->packageValidityUpgrade($package_details, $purchase_details);
            $data = serialize($purchase_details);
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Membership Reactivation of through ' . lang($purchase_details['by_using']), $this->LOG_USER_ID, $data);

            $this->session->unset_userdata('inf_package_validity');
            $msg = lang('package_successfully_updated');
            $this->redirect($msg, "member/upgrade_package_validity", true);
            exit();
        } else {
            $this->payment_model->insertSquareUpResponse($this->session->userdata('inf_package_validity'), $response['msg'], $this->LOG_USER_ID);
            $this->session->unset_userdata('inf_package_validity');
            $msg = lang('package_upgradation_failed');
            $this->redirect($msg, 'member/upgrade_package_validity', FALSE);
        }
    }
}
