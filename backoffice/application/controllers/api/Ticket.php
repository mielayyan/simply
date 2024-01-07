<?php

require_once 'Inf_Controller.php';

class Ticket extends Inf_Controller {

    function __construct() {
        parent::__construct();
        $this->LOG_USER_ID = $this->rest->user_id;
        $this->LOG_USER_TYPE = 'user';
        $this->load->model('ticket_system_model');
    }
    
    public function filters_get() {
        $this->set_success_response(200, [
            'filters' => [
                'categories' => $this->ticket_system_model->getAllTicketCategory(),
                'statuses' => $this->ticket_system_model->getAllTicketStatus(),
                'priorities' => $this->ticket_system_model->getAllTicketPriority()
            ]
        ]);
    }
    
    public function tickets_get() {
        $this->load->helper('date');
        $data = [
            'tickets' => $this->ticket_system_model->getTickets($this->LOG_USER_ID, $this->get()),
            'faqs' => $this->ticket_system_model->getFAQDetails()
        ];
        $this->set_success_response(200, $data);
    }
    

    public function tickets_post() {
        $this->lang->load('validation');
        $this->form_validation->set_rules('subject', 'subject', 'trim|required|strip_tags');
        $this->form_validation->set_rules('priority', 'priority', 'trim|required');
        $this->form_validation->set_rules('category', 'category', 'trim|required');
        $this->form_validation->set_rules('message_to_admin', 'message_to_admin', 'trim|required');
        if(!$this->form_validation->run()) {
            return $this->set_error_response(422,1004);
        }
        $this->ticket_system_model->begin();
        $ticket = [
            'trackid' => $this->ticket_system_model->createTicketId(),
            'subject' => $this->post('subject'),
            'user_id' => $this->LOG_USER_ID,
            'message' => $this->post('message_to_admin'),
            'category' => $this->post('category'),
            'priority' => $this->post('priority'),
            'file_name' => ''
        ];
        $doc_file_name = "";
        $file_details = [];
        if(isset($_FILES['attachment']['name'])) {
            $upload_doc               = 'attachment';
            $config['file_name'] = "doc_". floor($this->LOG_USER_ID * rand(1000, 9999));
            $config['upload_path']   = IMG_DIR . 'ticket_system';
            $config['allowed_types'] = 'jpg|png|jpeg|JPG|gif';
            $config['max_size']      = '2048';
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if ($this->upload->do_upload($upload_doc)) {
                $this->validation_model->updateUploadCount($this->LOG_USER_ID);
                $ticket['file_name'] = $this->upload->data('file_name');
                $data     = array('upload_data' => $this->upload->data());
                $file_arr                       = $this->upload->data();
                $file_details['original_name']  = $file_arr['orig_name'];
                $file_details['saved_name']     = $file_arr['file_name'];
                $file_details['file_size']      = $file_arr['file_size'];
                $doc_file_name                  = $data['upload_data']['file_name'];
            }
            else{
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
        }

        $new_ticket = $this->ticket_system_model->createNewTicket($ticket);
        if ($new_ticket) {
            $this->ticket_system_model->incrementCategoryCount($ticket['category']);
        }
        $ticket_id = $this->ticket_system_model->getTicketId($ticket['trackid'], $this->LOG_USER_ID);
        if (isset($_FILES['attachment']['name'])) {
            $res1 = $this->ticket_system_model->insertIntoAttachment($ticket['trackid'], $file_details, $doc_file_name);
        }

        $reply_ticket = $this->ticket_system_model->replyTicket($ticket_id, $ticket['message'], $ticket['user_id'], $doc_file_name);

        if ($new_ticket && $ticket_id && $reply_ticket) {
            $data_array                 = array();
            $data_array['mail_subject'] = $ticket['subject'];
            $data_array['mail_body']    = $ticket['message'];
            $data                       = serialize($data_array);
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'Ticket created', $this->ADMIN_USER_ID, $data);

            $details = $this->ticket_system_model->insertToHistory($ticket['trackid'], $this->LOG_USER_ID, $this->LOG_USER_TYPE, "Ticket created");
            $this->ticket_system_model->commit();
            $this->set_success_response(200);
        } else {
            $this->ticket_system_model->rollback();
            $this->set_error_response(422, 1004);
        }
    }

    public function ticket_time_line_get($ticket_id) {

        $ticket_activity_history = $this->ticket_system_model->getTicketActivityHistory($ticket_id);

        foreach ($ticket_activity_history as $key => $part) {
            $sort[$key] = strtotime($part['date']);
        }
        
        if (!empty($ticket_activity_history))
            array_multisort($sort, SORT_DESC, $ticket_activity_history);
        $this->set_success_response(200, $ticket_activity_history);
    }

    public function ticket_details_get($ticket_id) {
        $data['details'] = $this->ticket_system_model->getTicketData($ticket_id, $this->LOG_USER_ID);
        $data['ticket_replies'] = $this->ticket_system_model->getAllReply($data['details']['id']);
        foreach($data['ticket_replies'] as $key => $reply) {
            $data['ticket_replies'][$key]['profile_pic'] = SITE_URL.'/uploads/images/profile_picture/'.$reply['profile_pic'];
            $data['ticket_replies'][$key]['attachments'] = SITE_URL.'/uploads/images/ticket_system/'.$reply['attachments'];
        }

        $this->ticket_system_model->readTicket($data['details']['id']);
        $this->set_success_response(200, $data);
    }

    public function save_ticket_post() {
        $this->lang->load('validation');
        $this->form_validation->set_rules('message', 'message', 'trim|required|strip_tags');
        
        if(!$this->form_validation->run()) {
            return $this->set_error_response(422,1004);
        }

        $message = $this->validation_model->textAreaLineBreaker($this->post('message'));
        $ticket_arr = $this->ticket_system_model->getTicketId($this->post('ticket_id'), $this->LOG_USER_ID);
        
        $doc_file_name = '';
        if(isset($_FILES['attachment']['name']) && !empty($_FILES['attachment']['name'])) {
            $upload_config = $this->validation_model->getUploadConfig();
            $upload_count  = $this->validation_model->getUploadCount($this->LOG_USER_ID);
            if ($upload_count >= $upload_config) {
                $this->set_error_response(422, 1038);
            }

            $random_number            = floor($this->LOG_USER_ID * rand(1000, 9999));
            $config1['file_name']     = "doc_" . $random_number;
            $config1['upload_path']   = IMG_DIR . 'ticket_system';
            $config1['allowed_types'] = 'jpg|png|jpeg|JPG|gif';
            $config1['max_size']      = '2048';

            $this->load->library('upload', $config1);
            $this->upload->initialize($config1);

            if ($this->upload->do_upload('attachment')) {
                $data       = array('upload_data' => $this->upload->data());
                $file_arr   = $this->upload->data();
                $file_details['original_name']  = $file_arr['orig_name'];
                $file_details['saved_name']     = $file_arr['file_name'];
                $file_details['file_size']      = $file_arr['file_size'];
                $doc_file_name                  = $data['upload_data']['file_name'];
                $msg = "Uploaded and ";

                $res1 = $this->ticket_system_model->insertIntoAttachment($this->post('ticket_id'), $file_details, $doc_file_name);
                $this->validation_model->updateUploadCount($this->LOG_USER_ID);
            } else {
                $this->set_error_response(422, strip_tags($this->upload->display_errors()));
            }
        }

        $res = $this->ticket_system_model->replyTicket($ticket_arr, $message, $this->LOG_USER_ID, $doc_file_name);

         if ($res) {
            $reply_to_user  = $this->validation_model->getAdminUsername();
            $user_type      = $this->LOG_USER_TYPE;
            $activity       = 'Reply sent to ' . $reply_to_user;
            $data_array['reply_post_array'] = [
                'ticket_id' => $this->post('ticket_id'),
                'row_id'    => $this->post('row_id'),
                'message'   => $message
            ];
            
            $details        = $this->ticket_system_model->insertToHistory($this->post('ticket_id'), $this->LOG_USER_ID, $this->LOG_USER_TYPE, $activity,'', $message);  
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'ticket reply sent', $this->ADMIN_USER_ID, serialize($data_array));           
            $this->set_success_response(200);
        }
        $this->set_error_response(422, 1004);
    }
}
?>
