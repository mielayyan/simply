<?php

require_once 'Inf_Controller.php';

class Mail extends Inf_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("android/new/android_model");
        $this->load->model(["Api_model", 'mail_model']);
        $this->LOG_USER_ID = $this->rest->user_id;
        $this->LOG_USER_TYPE = 'user';
    }

    function compose_email() {
        // add two extra fields message and subject
        $post_array = $this->input->post(NULL, TRUE);
        $post_array = $this->validation_model->stripTagsPostArray($post_array);
        $post_array['message'] = $this->validation_model->stripTagTextArea($post_array['message']);

        $user_id = $this->LOG_USER_ID;
        if (!$user_id) {
            $user_details['status'] = false;
            $user_details['message'] = 'Invalid Login details';
        } else {
            $subject = $post_array['subject'];
            $message = $post_array['message'];
            $message = addslashes($message);
            $dt = date('Y-m-d H:i:s');
            $res = $this->mail_model->sendMesageToAdmin($user_id, $post_array['message'], $post_array['subject'], $dt);
            $msg = '';
            if ($res) {
                $data_array = array();
                $data_array['mail_subject'] = $post_array['subject'];
                $data_array['mail_body'] = $post_array['subject'];
                $data = serialize($data_array);
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'mail sent mobile', $this->ADMIN_USER_ID, $data);
            }

            $user_details['status'] = true;
            $user_details['message'] = 'Message Send Sucessfully';
        }
        echo json_encode($user_details);
        exit();
    }

    function get_all_emails() {
        $user_id = $this->LOG_USER_ID;
        $this->load->model('income_details_model');
        if (!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = 'Invalid user';
        } else {
            $json_response['status'] = true;
            $json_response['message'] = 'Success';
            $data1 = $this->mail_model->getUserMessages($user_id, $page = '', $config['per_page'] = '');
            $data2 = $this->mail_model->getUserContactMessages($user_id, $page = '', $config['per_page'] = '');
            $json_response['data'] = array_merge($data1, $data2);
        }
        echo json_encode($json_response);
        exit();
    }

    function delete_email() {
        //add delete_id as extra field
        $user_id = $this->LOG_USER_ID;
        $post_array = $this->input->post(NULL, TRUE);
        $post_array = $this->validation_model->stripTagsPostArray($post_array);
        $msg_id = $post_array['delete_id'];
        $msg_type = $post_array['mail_type']; // to_admin, to_user
        if (!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = 'Invalid user';
        } else {

            $check_mail_exist = 1;
            if (!$check_mail_exist) {
                $json_response['status'] = false;
                $json_response['message'] = 'Invalid Message Id';
            } else {
                if ($msg_type == 'to_admin') {
                    $res = $this->mail_model->updateAdminMessage($msg_id);
                } else {
                    $res = $this->mail_model->updateUserMessage($msg_id);
                }
                if ($res) {
                    $data_array = array();
                    $data_array['msg_id'] = $msg_id;
                    $data_array['msg_type'] = 'mail_deleted_mob';
                    $data = serialize($data_array);
                    $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'mail read status changed', $this->LOG_USER_ID, $data);
                    $json_response['status'] = true;
                    $json_response['message'] = 'Mail read status changed ';
                } else {
                    $json_response['status'] = false;
                    $json_response['message'] = 'Failed';
                }
            }
        }
        echo json_encode($json_response);
        exit();
    }

    function change_mail_read_status() {
        //add delete_id as extra field
        $user_id = $this->LOG_USER_ID;
        $post_array = $this->input->post(NULL, TRUE);
        $post_array = $this->validation_model->stripTagsPostArray($post_array);
        $msg_id = $post_array['mail_id'];
        if (!$user_id) {
            $json_response['status'] = false;
            $json_response['message'] = 'Invalid user';
        } else {

            $check_mail_exist = $this->android_model->checkMailexist($msg_id);
            if (!$check_mail_exist) {
                $json_response['status'] = false;
                $json_response['message'] = 'Invalid Message Id';
            } else {
                $res = $this->mail_model->updateUserOneMessage($msg_id);
                if ($res) {
                    $data_array = array();
                    $data_array['msg_id'] = $msg_id;
                    $data_array['msg_type'] = 'mail_read_staus_changed_mob';
                    $data = serialize($data_array);
                    $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'mail read status changed mob', $this->LOG_USER_ID, $data);
                    $json_response['status'] = true;
                    $json_response['message'] = 'Mail read status change Success';
                } else {
                    $json_response['status'] = false;
                    $json_response['message'] = 'Failed';
                }
            }
        }
        echo json_encode($json_response);
        exit();
    }

    function get_sent_mails() {
        $user_id = $this->LOG_USER_ID;
        $mail = $this->mail_model->getUserMessagesSent($user_id, 1);
        echo json_encode(array('status' => TRUE, 'message' => lang('details_fetched'), 'data' => $mail));
    }

    function compose_email_by_type() {
        // add two extra fields message and subject
        $this->load->model('mail_model');
        $post_array = $this->input->post(NULL, TRUE);
        $post_array = $this->validation_model->stripTagsPostArray($post_array);
        $post_array['message'] = $this->validation_model->stripTagTextArea($post_array['message']);
        $subject = $post_array['subject'];
        $message = $post_array['message'];
        $message = addslashes($message);
        $dt = date('Y-m-d H:i:s');
        $type = $post_array['type'];
        $user_name = $post_array['user_name'];
        $user_id = $this->validation_model->userNameToID($user_name);
        if ($type == "admin") {
            $res = $this->mail_model->sendMesageToAdmin($user_id, $post_array['message'], $post_array['subject'], $dt);
            $msg = '';
            if ($res) {
                $data_array = array();
                $data_array['mail_subject'] = $post_array['subject'];
                $data_array['mail_body'] = $post_array['subject'];
                $data = serialize($data_array);
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'mail sent mobile', $this->ADMIN_USER_ID, $data);
                $user_details['status'] = true;
                $user_details['message'] = lang('success');
            } else {
                $user_details['status'] = false;
                $user_details['message'] = lang('err_send_msg');
            }
        } elseif ($type == "my_team") {
            $user_downlines = $this->mail_model->getUserDownlinesAll($user_id);
            if (count($user_downlines)) {

                $this->mail_model->sendMessageToDownlinesCumulative($subject, $user_id, 'team', $message, 'team');
                $res = $this->mail_model->sendMessageToAllDownlines($message, $user_id, $user_downlines, $subject);
                $msg = "";
                if ($res) {
                    $login_id = $user_id;
                    $data_array = array();
                    $data_array['mail_subject'] = $subject;
                    $data_array['mail_body'] = $message;
                    $data = serialize($data_array);
                    $this->validation_model->insertUserActivity($login_id, 'mail sent', $this->ADMIN_USER_ID, $data);
                    $user_details['status'] = true;
                    $user_details['message'] = lang('success');
                } else {
                    $user_details['status'] = false;
                    $user_details['message'] = lang('err_send_msg');
                }
            }
        } elseif ($type == "individual") {
            if (!isset($post_array['user_id'])) {
                $to_user_id = "";
            } else {
                $to_user_id = $post_array['user_id'];
            }
            if ($to_user_id != '') {
                $username = $this->validation_model->idToUserName($to_user_id);
                if ($username) {
                    $this->mail_model->sendMessageToDownlines($subject, $user_id, $to_user_id, $message);
                    $this->mail_model->sendMessageToDownlinesCumulative($subject, $user_id, $to_user_id, $message, 'individual');
                    $msg = "";
                    $res = true;
                    if ($res) {
                        $login_id = $this->LOG_USER_ID;
                        $data_array = array();
                        $data_array['mail_subject'] = $subject;
                        $data_array['mail_body'] = $message;
                        $data = serialize($data_array);
                        $this->validation_model->insertUserActivity($login_id, 'mail to downline sent', $this->ADMIN_USER_ID, $data);
                        $user_details['status'] = true;
                        $user_details['message'] = lang('success');
                    } else {
                        $user_details['status'] = false;
                        $user_details['message'] = lang('err_send_msg');
                    }
                } else {
                    $user_details['status'] = false;
                    $user_details['message'] = lang('invalid_user');
                }
            } else {
                $user_details['status'] = false;
                $user_details['message'] = lang('you_must_select_user');
            }
        }
        echo json_encode($user_details);
        exit();
    }

    public function getUserDownlinesAll() {
        $user_id = $this->LOG_USER_ID;
        $data1 = $this->Mail_model->getUserDownlinesAll($user_id);
        if ($data1 == NULL || $data1 == '')
            $data1 = [];
        $data = $this->array_flatten($data1);
        echo json_encode(array('status' => TRUE, 'message' => lang('success'), 'data' => $data));
    }

    public function array_flatten($array, $output = array()) { 
        if (!is_array($array)) { 
            return FALSE; 
        } 
        foreach ($array as $inner_array) {
            $output = array_merge($output, $inner_array);
        } 
        return $output; 
    }

    public function inbox_mail_list_get()
    {
        if ($this->MODULE_STATUS['mailbox_status'] != 'yes') {
            $this->set_error_response(422,1057);
        }
        $get_arr = $this->get();
        $this->form_validation->set_data($get_arr);
        $rules = [
            [
                'field' => 'offset',
                'label' => lang("offset"),
                'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
            ],
            [
                'field' => 'limit',
                'label' => lang("limit"),
                'rules' => 'trim|required|numeric|greater_than_equal_to[0]|less_than_equal_to[1000]'
            ],
        ];
        $this->form_validation->set_rules($rules);
        if (!$this->form_validation->run()) {
            $this->set_error_response(422, 1004);
        }

        $this->load->model('mail_model');
        $data1 = $this->mail_model->getUserMessages($this->LOG_USER_ID, $get_arr['offset'], $get_arr['limit']);
        $data2 = [];
        $data2 = $this->mail_model->getUserContactMessages($this->LOG_USER_ID, $get_arr['offset'], $get_arr['limit']);
        // uncommetd : mail box working for react
        $mail = array_merge($data1, $data2);
        $unread_count = 0;
        for ($i = 0; $i < count($mail); $i++) {
            if($mail[$i]['type'] == 'contact') {
                $id = $mail[$i]['mailtousid'];
                $mail[$i]['mail_enc_thread'] = $mail[$i]['mailtousid'];
            } else {
                $id = $mail[$i]['thread'];
                $mail[$i]['mail_enc_thread'] = $mail[$i]['thread'];
            }
            $mail[$i]['mail_enc_id'] = $id;
            $mail[$i]['mail_enc_type'] = $mail[$i]['type'];
            if($mail[$i]['read_msg'] != 'yes') {
                $unread_count++;
            }
        }
        $mail_count = $this->mail_model->getCountUserMessages($this->LOG_USER_ID) + $this->mail_model->getCountUserContactMessages($this->LOG_USER_ID);
        $this->set_success_response(200, [
            'mail_list' => $mail,
            'unread_count' => $unread_count,
            'mail_count' => $mail_count
        ]);
    }
    
    public function sent_mail_list_get()
    {
        $get_arr = $this->get();
        $this->form_validation->set_data($get_arr);
        $rules = [
            [
                'field' => 'offset',
                'label' => lang("offset"),
                'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
            ],
            [
                'field' => 'limit',
                'label' => lang("limit"),
                'rules' => 'trim|required|numeric|greater_than_equal_to[0]|less_than_equal_to[1000]'
            ],
        ];
        $this->form_validation->set_rules($rules);
        if (!$this->form_validation->run()) {
            $this->set_error_response(422, 1004);
        }

        $this->load->model('mail_model');
        $mail = $this->mail_model->getUserMessagesSent($this->LOG_USER_ID, $get_arr['offset'], $get_arr['limit']);
        for ($i = 0; $i < count($mail); $i++) {
            if($mail[$i]['type'] == 'contact' || $mail[$i]['type'] == 'ext_mail_user') {
                $id = $mail[$i]['mailtousid'];
            } else {
                $id = $mail[$i]['thread'];
            }
            $mail[$i]['mail_enc_id'] = $id;
            $mail[$i]['mail_enc_type'] = $mail[$i]['type'];
        }
        $mail_count = $this->mail_model->getCountUserMessagesSent($this->LOG_USER_ID);
        $this->set_success_response(200, [
            'mail_list' => $mail, 
            'mail_count' => $mail_count
        ]);
    }

    public function sent_mail_get()
    {
        $get_arr = $this->get();
        $this->form_validation->set_data($get_arr);
        $rules = [
            [
                'field' => 'id',
                'label' => lang("id"),
                'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
            ],
            [
                'field' => 'type',
                'label' => lang("type"),
                'rules' => 'trim|required|in_list[to_user,to_admin,ext_mail_user,user]'
            ],
        ];
        $this->form_validation->set_rules($rules);
        if (!$this->form_validation->run()) {
            $this->set_error_response(422, 1004);
        }
        $this->load->model('mail_model');
        $mail_details = $this->mail_model->getUserSentMailDetails($get_arr['id'], $get_arr['type'], $this->LOG_USER_ID);
        for ($i = 0; $i < count($mail_details); $i++) {
            if ($get_arr['type'] == "to_admin") {
                $mail_details[$i]['msg'] = htmlspecialchars_decode($mail_details[$i]['mailadidmsg']);
            } else {
                $mail_details[$i]['msg'] = htmlspecialchars_decode($mail_details[$i]['mailtousmsg']);
            }
        }
        $this->set_success_response(200, $mail_details);
    }

    public function inbox_mail_get()
    {
        if ($this->MODULE_STATUS['mailbox_status'] != 'yes') {
            $this->set_error_response(422,1057);
        }
        $get_arr = $this->get();
        $this->form_validation->set_data($get_arr);
        $rules = [
            [
                'field' => 'msg_id',
                'label' => lang("msg_id"),
                'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
            ],
            [
                'field' => 'msg_type',
                'label' => lang("msg_type"),
                'rules' => 'trim|required|in_list[user,contact]'
            ],
            [
                'field' => 'thread',
                'label' => lang("thread"),
                'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
            ]
        ];
        $this->form_validation->set_rules($rules);
        if (!$this->form_validation->run()) {
            $this->set_error_response(422, 1004);
        }
        $msg_id = $this->get('msg_id');
        $msg_type = $this->get('msg_type');
        $thread = $this->get('thread');
        if ($msg_type == 'user') {
            $result = $this->mail_model->updateMsgStatus($msg_id, $thread);
            
        }
        if ($msg_type == 'contact') {
            $result = $this->mail_model->updateContactMsgStatus($msg_id);
        }
        $mail_details = $this->mail_model->getUserMailDetails($msg_id, $msg_type, $thread);
        // dd($mail_details);
        for ($i = 0; $i < count($mail_details); $i++) {
            if ($msg_type == 'contact') {
                $mail_details['msg'] = htmlspecialchars_decode($mail_details['contact_info']);
            } else {
                $mail_details[$i]['msg'] = htmlspecialchars_decode($mail_details[$i]['message']);
            }
            if(isset($mail_details[$i])){
                if($mail_details[$i]['from'] == $this->LOG_USER_ID) {
                    $mail_details[$i]['is_sent_mail'] = true;
                } else {
                    $mail_details[$i]['is_sent_mail'] = false;
                }
            }
        }
        if ($msg_type == 'contact') {
            $mail_details['date'] = date('d M,Y g:i A', strtotime($mail_details['mailadiddate']));
        }

        $this->set_success_response(200, $mail_details);
    }

    public function mail_compose_data_get()
    {
        $this->load->model('mail_model');
        
        $this->set_success_response(200, [
            'sender_email' => $this->validation_model->getUserEmailId($this->LOG_USER_ID),
            'downline_users' => $this->Api_model->getDownlinesList($this->LOG_USER_ID),
            'admin_username' => $this->mail_model->getAdminUsername(),
        ]);
    }

    public function mail_compose_post()
    {
        $this->form_validation->set_data($this->post());
        // add two extra fields message and subject
        $this->form_validation->set_rules('subject', lang('subject'), 'trim|required|strip_tags',["required"=>lang('required')]);
        $this->form_validation->set_rules('message', lang('message'), 'trim|required',["required"=>lang('required')]);
        $this->form_validation->set_rules('type', lang('msg_type'), 'trim|required|in_list[admin,myTeam,individual,externalMail]',["required"=>lang('required')]);
        if($this->post('type') == 'individual') {
            $this->form_validation->set_rules('user', lang('user'), 'trim|required',["required"=>lang('required')]);
        }
        if (!$this->form_validation->run()) {
            $this->set_error_response(422, 1004);
        }

        $this->load->model('mail_model');
        $post_array = $this->input->post(NULL, TRUE);
        $post_array = $this->validation_model->stripTagsPostArray($this->post());
        $post_array['message'] = $this->validation_model->stripTagTextArea($post_array['message']);
        $subject = $post_array['subject'];
        $message = $post_array['message'];
        $message = addslashes($message);
        $dt = date('Y-m-d H:i:s');
        $type = $post_array['type'];
        $user_id = $this->LOG_USER_ID;
        $user_name = $this->validation_model->getUserName($user_id);
        $data = serialize(['mail_subject' => $subject, 'mail_body' => $message]);
        if ($type == "admin") {
            $res = $this->mail_model->sendMesageToAdmin($user_id, $message, $subject, $dt);
            $msg = '';
            if (!$res) {
                $this->set_error_response(422, 1047);
            }
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'mail sent mobile', $this->ADMIN_USER_ID, $data);
        } elseif ($type == "myTeam") {
            $user_downlines = $this->mail_model->getUserDownlinesAll($user_id);
            if (count($user_downlines)) {
                $this->mail_model->sendMessageToDownlinesCumulative($subject, $user_id, 'team', $message, 'team');
                $res = $this->mail_model->sendMessageToAllDownlines($message, $user_id, $user_downlines, $subject);
                $msg = "";
                if (!$res) {
                    $this->set_error_response(422, 1047);
                }
                $this->validation_model->insertUserActivity($user_id, 'mail sent', $this->ADMIN_USER_ID, $data);
            }
        } elseif ($type == "individual") {
            $username = $post_array['user'];
            $to_user_id = $this->validation_model->userNameToID($username);
            if(!$to_user_id) {
                $this->set_error_response(422, 1011);
            }
            $res = $this->mail_model->sendMessageToDownlines($subject, $user_id, $to_user_id, $message);
            if (!$res) {
                $this->set_error_response(422, 1047);
            }
            $res = $this->mail_model->sendMessageToDownlinesCumulative($subject, $user_id, $to_user_id, $message, 'individual');
            if (!$res) {
                $this->set_error_response(422, 1047);
            }
            $this->validation_model->insertUserActivity($user_id, 'mail to downline sent', $this->ADMIN_USER_ID, $data);            
        }elseif ($type== 'externalMail') {
            if($post_array['ext_mail_from'] != $this->validation_model->getUserEmailId($this->LOG_USER_ID)){
                $this->set_error_response(422,1047);
            }
            $send_details = array();
            $send_details['user_id'] = $this->LOG_USER_ID;
            $type = 'external_mail';
            $email = $post_array['ext_mail_from'];
            $to_mail = $post_array['ext_mail_to'];
            $send_details['full_name'] = $this->validation_model->getUserFullName($this->LOG_USER_ID);
            $send_details['fullname'] = $this->validation_model->getUserFullName($this->LOG_USER_ID);
            $send_details['email'] = $to_mail;
            $send_details['first_name'] = $this->validation_model->getUserData($this->LOG_USER_ID, "user_detail_name");
            $send_details['last_name'] = $this->validation_model->getUserData($this->LOG_USER_ID, "user_detail_second_name");
            $send_details['email_from'] = $email;
            $send_details['content'] = $message;
            $send_details['subject'] = $subject;
            $res = $this->mail_model->sendAllEmails($type, $send_details);
            $res1 = $this->mail_model->sendMessageToUserCumulative($to_mail, $subject, $message, $dt, 'ext_mail_user');
            $msg = "";
            if ($res) {
                $login_id = $this->LOG_USER_ID;
                $data_array = array();
                $data_array['mail_subject'] = $subject;
                $data_array['mail_body'] = $message;
                $data = serialize($data_array);
                $this->validation_model->insertUserActivity($login_id, 'mail sent', $this->ADMIN_USER_ID, $data);
                $this->set_success_response(200);
            } else {
                $this->set_error_response(422,1047);
            }
        }
        $this->set_success_response(200);
    }

    function mail_delete_post() {
        // parse_str(file_get_contents('php://input'), $post_array);
        $post_array = $this->post();
        $this->form_validation->set_data($post_array);
        $this->form_validation->set_rules('delete_id', lang('id'), 'trim|required|numeric|greater_than_equal_to[0]',["required"=>lang('required')]);
        $this->form_validation->set_rules('mail_type', lang('msg_type'), 'trim|required|in_list[user,contact,to_admin,ext_mail_user]',["required"=>lang('required')]);
        if (!$this->form_validation->run()) {
            $this->set_error_response(422, 1004);
        }

        $this->load->model('mail_model');
        $post_array = $this->validation_model->stripTagsPostArray($post_array);
        $msg_id = $post_array['delete_id'];
        $msg_type = $post_array['mail_type'];

        if ($msg_type == 'user') {
            $res1 = $this->mail_model->updateAdminMessage($msg_id, true);
            $res2 = $this->mail_model->updateUserMessage($msg_id, true);
            $res = ($res1 || $res2);
        }
        if ($msg_type == 'contact') {
            $res = $this->mail_model->updateuserContactMessage($msg_id);
        }
        if($msg_type == 'to_admin'){
            $res = $this->mail_model->updateUserMessageSent($msg_id, $msg_type);
        }
        if ($msg_type == 'ext_mail_user') {
            $res = $this->mail_model->updateAdminSentMessage($msg_id);
        } else {
            $res = $this->mail_model->updateUserMessageSent($msg_id, $msg_type);
        }
        if (!$res) {
            $this->set_error_response(422, 1048);
        }

        $data = serialize(['msg_id' => $msg_id, 'msg_type' => 'mail_deleted_mob']);
        $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'mail read status changed', $this->LOG_USER_ID, $data);
        $this->set_success_response(204);
    }

    public function mail_reply_data_get()
    {
        if($this->MODULE_STATUS['mailbox_status'] != 'yes') {
            $this->set_error_response(422,1057);
        }
        $this->form_validation->set_data($this->get());
        $this->form_validation->set_rules('mail_id', lang('id'), 'trim|required|numeric|greater_than_equal_to[0]',["required"=>lang('required')]);
        if (!$this->form_validation->run()) {
            $this->set_error_response(422, 1004);
        }
        $mail_id = $this->get('mail_id');
        $mail_details = $this->mail_model->getUserOneMessage($mail_id, $this->LOG_USER_ID);
        $mail_details = $mail_details->result_array();

        if (!count($mail_details) || $mail_details[0]['mailtoususer'] != $this->LOG_USER_ID) {
            $this->set_error_response(422, 1048);
        }

        if ($mail_details[0]['mailfromuser'] == 'admin') {
            $admin_id = $this->validation_model->getAdminId();
            $reply_to_user = $this->validation_model->idToUserName($admin_id);
        } else {
            $reply_to_user = $this->validation_model->idToUserName($mail_details[0]['mailfromuser']);
        }
        $thread = $mail_details[0]['thread'];
        $reply_msg = $mail_details[0]['mailtoussub'];
        if (preg_match('/([\w\-]+\:[\w\-]+)/', $reply_msg)) {
            $string = explode(":", $reply_msg);
            $reply_msg = $string[1];
        }
        $reply_msg = str_replace('%20', ' ', $reply_msg);
        $reply_msg = trim($reply_msg);
        $this->set_success_response(200, [
            "reply_to_user" => $reply_to_user,
            "reply_msg" => $reply_msg,
        ]);
    }

    public function mail_reply_post()
    {
        $this->form_validation->set_data($this->post());
        $this->form_validation->set_rules('mail_id', lang('id'), 'trim|required|numeric|greater_than_equal_to[0]',["required"=>lang('required')]);
        $this->form_validation->set_rules('message', lang('message'), 'trim|required',["required"=>lang('required')]);
        $this->form_validation->set_rules('subject', lang('subject'), 'trim|required',["required"=>lang('required')]);
        if (!$this->form_validation->run()) {
            $this->set_error_response(422, 1004);
        }
        
        $mail_id = $this->post('mail_id');
        $message = $this->validation_model->stripTagTextArea($this->post('message'));
        $subject = $this->validation_model->stripTagTextArea($this->post('subject'));
        $message = htmlentities($message);
        
        $mail_details = $this->mail_model->getUserOneMessage($mail_id, $this->LOG_USER_ID);
        $mail_details = $mail_details->result_array();
        if (!count($mail_details) || $mail_details[0]['mailtoususer'] != $this->LOG_USER_ID) {
            $this->set_error_response(422, 1048);
        }
        $thread = $mail_details[0]['thread'];
        $to_user_name = $this->validation_model->idToUserName($mail_details[0]['mailfromuser']);
        $dt = date('Y-m-d H:i:s');
        if ($to_user_name == $this->mail_model->getAdminUsername()) {
            $res = $this->mail_model->sendMesageToAdmin($this->LOG_USER_ID, $message, $subject, $dt, '', $thread);
        } else {
            $to_user_id = $this->mail_model->userNameToId($to_user_name);
            $res = $this->mail_model->sendMessageToUser($to_user_id, $subject, $message, $dt, $this->LOG_USER_ID, $thread);
        }
        if (!$res) {
            $this->set_error_response(422, 1047);
        }
        $this->set_success_response(200);
    }

}

?>
