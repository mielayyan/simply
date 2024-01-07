<?php

require_once 'Inf_Controller.php';

class Tools extends Inf_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("android/new/android_model");
        $this->load->model('Api_model');
        $this->load->model('news_model');
        $this->load->model('member_model');
        $this->LOG_USER_ID = $this->rest->user_id;
        $this->LOG_USER_TYPE = 'user';
    }

     public function all_news_get()
    {
        $data =[];
        $total_news = $this->news_model->getAllNewsCount();

        if ($this->news_model->getUnreadNewsCount($this->LOG_USER_ID) > 0) {
            $this->news_model->setNewsViewed($this->LOG_USER_ID);
            $this->set_header_notification_box();
        }
        $news_details = $this->news_model->getAllNews();
        $news =$this->security->xss_clean($news_details);
        $data['total_news_count'] = $total_news;
        foreach ($news as $item) {
            if (file_exists(IMG_DIR . "news/" .$item['news_image'])) {
                $news_image = SITE_URL. "/uploads/images/news/" . $item['news_image'];
            } else {
                $news_image = SITE_URL. "/uploads/images/news/default.png";
            }
            $data['news_data'][] = [
                'news_id'    =>  $item['news_id'],
                'news_title' =>  $item['news_title'],
                'news_desc'  =>  $item['news_desc'],
                'news_date'  =>  $item['news_date'],
                'news_image' =>  $news_image,
            ];
        }
        
        $this->set_success_response(200,$data);
    }

    public function view_news_get()
    {
        $data = [];
        $get_arr = $this->get();
        $news_id = $get_arr['news_id'];
        if (isset($news_id)) {
            $news=$this->news_model->getAllLatestNews($news_id);
            foreach ($news as $key => $value) {
               if (file_exists(IMG_DIR . "news/" . $news[$key]['news_image'])) {
                    $news[$key]['news_image'] = SITE_URL. "/uploads/images/news/" . $news[$key]['news_image'];
                } else {
                    $news[$key]['news_image'] = SITE_URL. "/uploads/images/news/default.png";
                }
            }
            $data['active_news'] = $news;
            // 
            $recent = $this->news_model->getRecentNews($news_id);
            foreach ($recent as $key => $value) {
               if (file_exists(IMG_DIR . "news/" . $recent[$key]['news_image'])) {
                    $recent[$key]['news_image'] = SITE_URL. "/uploads/images/news/" . $recent[$key]['news_image'];
                } else {
                    $recent[$key]['news_image'] = SITE_URL. "/uploads/images/news/default.png";
                }
            }
            $data['recent_news'] = $recent;
        }
        
        $this->set_success_response(200,$data);
    }
    
    public function faq_get()
    {
        $faq = $this->news_model->getBackFAQDetails();
        $data['faq'] = $faq;
        $this->set_success_response(200,$data);
    }
    
    public function download_product_get()
    {
        $this->load->model('document_model');
        if ($this->document_model->getUnreadDocumentsCount($this->LOG_USER_ID) > 0) {
            $this->document_model->setDocumentViewed($this->LOG_USER_ID);
            $this->set_header_notification_box();
        }
        $documents = $this->document_model->getAllDocuments( $this->document_model->getAllDocumentsCount(), 0);
        foreach ($documents as $key => $value) {
               if (file_exists(IMG_DIR . "document/" . $documents[$key]['doc_file_name'])) {
                    $documents[$key]['doc_file_name'] = IMG_DIR. "images/document/" . $documents[$key]['doc_file_name'];
                } else {
                    $documents[$key]['doc_file_name'] = IMG_DIR. "images/document/default.png";
                }
                $fileExt = pathinfo($documents[$key]['doc_file_name'], PATHINFO_EXTENSION);
                if(in_array($fileExt,['pdf', 'xlsx', 'word', 'ods', 'docx']))
                {
                    $documents[$key]['doc_icone'] = IMG_DIR. "images/document/document.svg";
                }
                else if(in_array($fileExt,['mp4' , 'avi' , 'flv' , 'mpg' , 'wmv' , '3gp' , 'rm']))
                {
                    $documents[$key]['doc_icone'] = IMG_DIR. "images/document/mov.svg";
                }
                else if(in_array($fileExt,['png', 'jpeg','svg', 'jpg']))
                {
                    $documents[$key]['doc_icone'] = IMG_DIR. "images/document/image.svg";
                }
            }
        $data['documen_data'] = $documents;
        $this->set_success_response(200,$data);
    }

    public function leads_get() {
        if ($this->MODULE_STATUS['lead_capture_status'] != 'yes') {
            $this->set_error_response(422,1057);
        }
        if ($this->MODULE_STATUS['lcp_type'] != 'lcp') {
            $this->set_error_response(422,1057);
        } 
        $admin_username = $this->validation_model->getAdminUsername();
        $user_name = $this->validation_model->IdToUserName($this->LOG_USER_ID);
        $this->load->model('member_model');
        $this->set_success_response(200,[
            'count' => $this->member_model->getLeadDetailsCount($this->LOG_USER_ID, $this->get('keyword')),
            'leads' => array_map(function ($lead) {
                $lead['comments'] = $this->member_model->getAdminComent($lead['id']);
                return $lead;
            }, $this->member_model->getLeadDetails($this->LOG_USER_ID, $this->get('keyword'), $this->PAGINATION_PER_PAGE, $this->get('start'))),
            'lead_url' => SITE_URL.'/lcp/'. $admin_username . '/'.$user_name
        ]);
    }
    
    public function leads_put($id) {
        $data = [
            'status'        => $this->put('status'),
            'admin_comment' => $this->put('comment'),
            'lead_id'       => $id,
        ];
        
        $res1 = $res2 = false;
        $res1 = $this->member_model->addFollowup($data);
        $res2 = $this->member_model->updateCRM($data);
        
        $lead_details = $this->member_model->getLeadDetailsById($id);
        $lead_details['new_status'] = $data["status"];
        $lead_details['admin_comment'] = $data["admin_comment"];
        $lead_details['email'] = $lead_details["email_id"];

        if ($res1 && $res2) {
            $this->load->model('mail_model');
            $this->mail_model->sendAllEmails("lcp_reply", $lead_details);
            $this->set_success_response(204);
        } else if ($res2) {
            $this->set_success_response(204);
        } else {
            $this->set_error_response(422, 1030);
        }
    }
    
    public function replica_banner_post() {
        if (!isset($_FILES['image'])) {
            $this->set_error_response(400, 1032);
        }
        
        $config['upload_path'] = IMG_DIR . 'banners';
        $config['allowed_types'] = 'png|jpeg|jpg';
        $config['max_size'] = '2000';
        $config['remove_spaces'] = true;
        $config['overwrite'] = FALSE;
        
        $this->load->library('upload', $config);
        
        if (!$this->upload->do_upload('image')) {
            $error_keys = $this->upload->list_error_keys();
            $err = reset($error_keys);
            if ($err[0] == 'upload_file_exceeds_limit' || $err[0] == 'upload_file_exceeds_form_limit') {
                $code = 1018;
            } else if ($err[0] == 'upload_invalid_filetype') {
                $code = 1017;
            } else {
                $code = 1024;
            }
            $this->set_error_response(422, $code);
        }
        $details = [];
        $banner_arr = ['upload_data' => $this->upload->data()];
        $res = $this->member_model->insertBannerforReplica($banner_arr['upload_data']['file_name'], $this->LOG_USER_ID);
         if ($res) {
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Top Banner updated for replication site', $this->LOG_USER_ID);
            $this->set_success_response(200, ['message' => 'top_banner_updated']);
        } else {
            $this->set_error_response(500, ['message' => 'error_on_updation']); 
        }
    }
    
    public function replica_banner_get() {
        if ($this->MODULE_STATUS['replicated_site_status'] != 'yes') {
            $this->set_error_response(422,1057);
        }
        $this->set_success_response(200, [
            'replica_banner' => SITE_URL .'/uploads/images/banners/' . $this->configuration_model->selectBanner($this->LOG_USER_ID)
        ]);
    }

    public function invites_emails_get() {
        $this->load->model('member_model');
        $invite_history_details = $this->member_model->getInviteHistory($this->LOG_USER_ID, 10 ,0);
        $invite_text = $this->member_model->getTextInvitesData(10, 0);
        if ($invite_text) {
            $invite_text[0]['subject'] = html_entity_decode($invite_text[0]['subject']);
            $invite_text[0]['content'] = html_entity_decode($invite_text[0]['content']);
            $invite_text[0]['replica'] = REPLICATION_URL.'/'.$this->validation_model->IdToUserName($this->LOG_USER_ID);
        }
        $social_invite_fb = $this->member_model->getSocialInviteData('social_fb', 10, 0);
        $social_invite_twitter = $this->member_model->getSocialInviteData('social_twitter', 10, 0);
        $social_invite_instagram = $this->member_model->getSocialInviteData('social_instagram', 10, 0);
        $this->set_success_response(200, [
            'social_emails' => $this->member_model->getSocialInviteData('social_email'),
            'banners'       => $this->member_model->getBanners(20, 0),
            'invite_history' => $invite_history_details,
            'text_invite' =>$invite_text,
            'social_invite' =>[
                'fb' => $social_invite_fb,
                'twitter' => $social_invite_twitter,
                'instagram' => $social_invite_instagram,

            ]
        ]);
    }
    public function invite_post(){
        $this->load->model('mail_model');
        $user_id = $this->LOG_USER_ID;
        $invite_details = $this->post(null, true);
        $invite_details = $this->validation_model->stripTagsPostArray($invite_details);
        $invite_details['message'] = $this->validation_model->stripTagTextArea($this->post('message'));
        $result = $this->member_model->sendInvites($invite_details, $user_id);
        $to_id = $invite_details['to_mail_id'];
        if ($result == 1) {
            $data_array = array();
            $data_array['invite_details'] = $invite_details;
            $data = serialize($data_array);
            $this->validation_model->insertUserActivity($user_id, 'invitation sent', $user_id, $data);
            $this->set_success_response(204);
        } else {
            $this->set_error_response(422,1060);
        }
    }
    
}

?>
