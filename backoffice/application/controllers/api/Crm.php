<?php

require_once 'Inf_Controller.php';

class Crm extends Inf_Controller
{

    public $display_tree = "";

    public function __construct()
    {
        parent::__construct();
        $this->load->model("android/new/android_model");
        $this->load->model('crm_model');
        $this->LOG_USER_ID = $this->rest->user_id;
        $this->LOG_USER_TYPE = 'user';
    }
    public function crmTile_get()
    {
        $data = [];
        $data=[
        "ongoing_leads_count_today"   => $this->crm_model->getLeadsCount('Ongoing', 'today'),
        "total_ongoing_leads_count"   => $this->crm_model->getLeadsCount('Ongoing', 'total'),
        "total_accepted_leads_count"  => $this->crm_model->getLeadsCount('Accepted', 'total'),
        "total_rejected_leads_count"  => $this->crm_model->getLeadsCount('Rejected', 'total')
        ];
        $this->set_success_response(200, $data);
    }
    public function followUp_get()
    {
        $data = [];
        $data=[
        "followupsmissed"   => $this->crm_model->getTodaysFollowups('missed'),
        "followupstoday"    => $this->crm_model->getTodaysFollowups('today'),
        "followuprecent"    =>$this->crm_model->getTodaysFollowups('recent')
        ];
        $this->set_success_response(200, $data);
        
    }
    public function viewLeads_get()
    {
        $search_arr = array(
            'user_name' => '',
            'followup_date_from' => '',
            'followup_date_to' => '',
            'followup_added_date_from' => '',
            'followup_added_date_to' => '',
            'interest_status' => 'All',
            'lead_status' => 'All',
            'country' => '',
            'status_date_from' => '',
            'status_date_to' => '',
            'assignee' => ''
        );
        $new_lead = '';
        if ($this->input->get()) {
            $search_arr = $this->validation_model->stripTagsPostArray($this->input->get(null, true));
            if(isset($search_arr['formError']))
            {
                unset($search_arr['formError']);
            }
        }
        $leads = $this->crm_model->searchLeads($search_arr,0,10,$new_lead);
        $data=[
            "leads" =>$leads
        ];
        $this->set_success_response(200, $data);
    }
    public function addNextFollowup_post()
    {
        $this->form_validation->set_data($this->post());
        $this->form_validation->set_rules('followup_date', lang("next_followup_date"), 'trim|required|callback_validate_date');
        $this->form_validation->set_message('validate_date', lang("next_followup_date_should_be_greater_or_equal_to_current_date"));
        $validation_result = $this->form_validation->run();

        if (!$validation_result) {
             $this->set_error_response(422, 1004);
        } else {
            $post_arr = $this->validation_model->stripTagsPostArray($this->post(null, true));
            $new_followup_date = $post_arr['followup_date'];
            $id = $post_arr['id'];
            $res = $this->crm_model->updateFollowupDate($new_followup_date, $id);
            if ($res) {
                $this->set_success_response(204);
            } else {
                $this->set_error_response(422, 1030);
            }
        }
        $this->set_success_response(204); 
    }
    public function addLeads_post()
    {
       $lead_details = array(
            'first_name' => '',
            'last_name' => '',
            'added_by' => '',
            'email_id' => '',
            'skype_id' => '',
            'mobile_no' => '',
            'country' => '',
            'description' => '',
            'interest_status' => '',
            'followup_date' => '',
            'lead_status' => '',
            'director' => ''
        );
         $this->form_validation->set_data($this->post());
         $post_arr = $this->validation_model->stripTagsPostArray($this->post(null, true));
            if ($this->validate_lead()) {
                if ($post_arr['email_id'] != '') {
                    $email_id = $post_arr['email_id'];
                    $existing_user = $this->crm_model->getUserIdByEmail($email_id);
                    if (count($existing_user) > 0) {
                        $enc_id = $this->encryption->encrypt($existing_user[0]['id']);
                        $enc_id = str_replace("/", "_", $enc_id);
                        $enc_id = urlencode($enc_id);
                        $this->set_error_response(422,1062);
                    }
                }
                $res = $this->crm_model->addLead($post_arr);
                if ($res) {
                    $data_array['details'] = $lead_details;
                    $data = serialize($data_array);

                    $this->validation_model->insertUserActivity($this->LOG_USER_ID, lang("lead_added"), $this->LOG_USER_ID, $data);
                    if ($this->LOG_USER_TYPE == 'employee') {
                        $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'lead_added', 'Lead Added');
                    }
                     $this->set_success_response(204);
                } else {
                    $this->set_error_response(422, 1030);
                }
            }
            else
            {
                $this->set_error_response(422, 1004);
            }
    }

    public function editLeads_put($id='')
    {   
        $post_arr = $this->validation_model->stripTagsPostArray($this->put(null, true));
        if($id){
          $post_arr['id']=$id; 
        }
        $this->form_validation->set_data($post_arr);
        if ($this->validate_lead($type='put')) {
            $res = $this->crm_model->updateLead($post_arr);
            if ($res) {
                $data_array['details'] = $res;
                $data = serialize($data_array);
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, lang('edit_lead_added'), $this->LOG_USER_ID, $data);
                // $this->redirect(lang('lead_updated_successfully'), 'lead/view_lead', true);
            } else {
                // $this->redirect(lang('failed_to_update_lead_details'), 'lead/view_lead', false);
                $this->set_error_response(422,1063);
            }
        }else{
            $this->set_error_response(422,1004);
        }
        $this->set_success_response(204);
    }
    public function addFollowup_post()
    {

        $followup_details = $this->post();
        $selected_lead = $this->crm_model->getLeadDetails($followup_details['id']);
        if (count($selected_lead) >= 1 && $followup_details['id'] != '') {
            $followup_details['user_name'] = $selected_lead[0]['lead_id'] . '-' . $selected_lead[0]['first_name'] . ' ' . $selected_lead[0]['last_name'];

            $followup_details['first_name'] = $selected_lead[0]['first_name'];
            $followup_details['last_name'] = $selected_lead[0]['last_name'];
        }
        $this->form_validation->set_data($this->post());
        $this->form_validation->set_rules('lead_name', lang('lead'), 'trim|required|strip_tags');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required', array('required' => lang('You_must_enter_description')));
        $this->form_validation->set_rules('followup_date', lang('next_follow-up_date'), 'required|callback_validate_date');

        $this->form_validation->set_message('validate_date', lang("followup_date_cannot_be_less_than_the_current_date"));
        $this->form_validation->set_message('lead_validation', lang('invalid_lead'));

        $this->form_validation->set_message('lead_validation', lang('invalid_lead'));
        $validation_status = $this->form_validation->run();
        if (!$validation_status) {
            $this->set_error_response(422, 1004);
        } else {
            $upload_status = true;
            $res = false;
            $post_arr = $this->validation_model->stripTagsPostArray($this->post(null, true));

            $followup_details = $post_arr;
            $followup_details['lead'] =$selected_lead[0]['lead_id'];
            $followup_details['file_name'] = '';
            if (isset($_FILES['upload_doc']['name'])) {
                $old_file_name = explode('.', $_FILES['upload_doc']['name']);
                $count = count($old_file_name);
                $extension = $old_file_name[$count - 1];
                $new_file_name = $this->crm_model->generateFileName($extension);
                $_FILES['upload_doc']['name'] = $new_file_name;

                $config['upload_path'] = IMG_DIR . 'document/';
                $config['allowed_types'] = 'pdf|ppt|xls|xlsx|doc|docx|txt|png|jpg|jpeg';
                $config['max_size'] = '51200';
                $config['max_width'] = '3000';
                $config['max_height'] = '3000';
                $config['overwrite'] = false;
                $this->load->library('upload', $config);
                if ($this->upload->do_upload('upload_doc')) {
                    $data = array('upload_data' => $this->upload->data());
                    $doc_file_name = $data['upload_data']['file_name'];
                    $followup_details['file_name'] = $_FILES['upload_doc']['name'];
                    $res = $this->crm_model->insertFollowup($followup_details);
                } else {
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
            } else {
                $res = $this->crm_model->insertFollowup($followup_details);
            }
            if ($res && $upload_status) {
                $this->set_success_response(204);
            } else
            if ($upload_status) {
                $this->set_error_response(422, 1030);
            }
        }
        
    }
     public function graph_get() {

        $user_id = '';
        //edited view lead
        if ($this->LOG_USER_TYPE != 'admin') {
            $user_id = $this->LOG_USER_ID;
        }
        //end
        $joining_details_per_month = $this->crm_model->getJoiningDetailsperMonth($user_id);
        $joining_details_per_day = $this->crm_model->getJoiningDetailsperDay($user_id);
        $ongoing_leads_month = [];
        $accepted_leads_month = [];
        $rejected_leads_month = [];
        $ongoing_leads_day = [];
        $accepted_leads_day = [];
        $rejected_leads_day = [];
        $leads_day_ticks = [];
        foreach ($joining_details_per_month as $key => $value) {
            $ongoing_leads_month[] = $value['ongoing'];
            $accepted_leads_month[] = $value['accepted'];
            $rejected_leads_month[] = $value['rejected'];
        }
        foreach ($joining_details_per_day as $key => $value) {
            $ongoing_leads_day[]    = [
                                        'label' => $key, 
                                        'value' => $value['ongoing']
                                        ];
            $accepted_leads_day[]   = [
                                        'label' => $key, 
                                        'value' => $value['accepted']
                                        ];
            $rejected_leads_day[]   = [
                                        'label' => $key, 
                                        'value' => $value['rejected']
                                        ];
            $leads_day_ticks[]      = $key;
        }
        $data=[
        "ongoing_leads_month"   => json_encode($ongoing_leads_month),
        "accepted_leads_month"  => json_encode($accepted_leads_month),
        "rejected_leads_month"  => json_encode($rejected_leads_month),
        "ongoing_leads_day"     => $ongoing_leads_day,
        "accepted_leads_day"    => $accepted_leads_day,
        "rejected_leads_day"    => $rejected_leads_day,
        "leads_day_ticks"       => $leads_day_ticks
        ];
        $this->set_success_response(200, $data);
    }
    public function validate_lead($type='post') {

        $this->lang->load('validation');
        $this->form_validation->set_rules('first_name', lang('first_name'), 'trim|strip_tags|required|callback_alpha_space|max_length[250]',[
            "required"=>lang('required'),
            "alpha_space"=>lang('alpha_space_only'),
            "max_length"=>sprintf(lang('maxlength'),lang('first_name'),"250")
        ]);
        $this->form_validation->set_rules('last_name', lang('last_name'), 'trim|strip_tags|callback_alpha_space|max_length[250]',[
            "alpha_space"=>lang('alpha_space_only'),
            "max_length"=>sprintf(lang('maxlength'),lang('last_name'),"250")
        ]);
        $this->form_validation->set_rules('email_id', lang('email_id'), 'trim|strip_tags|valid_email',[
          "valid_email"=>lang('valid_email'),
        ]);
        $this->form_validation->set_rules('skype_id', lang('skype_id'), 'trim|strip_tags');
        $this->form_validation->set_rules('country', lang('country'), 'trim');
        $this->form_validation->set_rules('description', lang('description'), 'trim|required|max_length[1000]',[
         "required"=>lang('required'),
         "max_length"=>sprintf(lang('maxlength'),lang('description'),"250")

        ]);
        if($type=='post'){
        $this->form_validation->set_rules('followup_date', lang('next_follow-up_date'), 'required|callback_validate_date',[
              "required"=>sprintf(lang('required_select'),lang("next_follow-up_date")),
              "validate_date"=>lang('valid_date')
            
        ]);
        }
        $this->form_validation->set_rules('lead_status', lang('lead_status'), 'required');
        $this->form_validation->set_message('validate_date', lang("next_followup_date_should_be_greater_or_equal_to_current_date"));
        $this->form_validation->set_message('alpha_space', lang("the_%s_field_may_only_contain_alphabetic_characters_and_spaces"));

        $validation_status = $this->form_validation->run();
        return $validation_status;
    }
    function alpha_space($str_in = '') {
        if ($str_in == '') {
            return true;
        }
        if (!preg_match("/^([a-zA-Z ])+$/i", $str_in)) {
            return false;
        } else {
            return true;
        }
    }
    public function get_notifications() {

        $notifications = $this->crm_model->getNotifications();

        echo json_encode($notifications);
        exit();
    }

    public function lead_validation() {
        $followup_arr = $this->validation_model->stripTagsPostArray($this->input->post(null, true));

        $lead_arr = explode('-', $followup_arr['lead_name']);
        $lead_id = $lead_arr[0];
        $lead_details = $this->crm_model->getLeadsFromKey($lead_id);
        if (count($lead_details) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function validate_date($followup_date) {
        // dd($followup_date);
        // $lead_arr = $this->validation_model->stripTagsPostArray($this->post(null, true));
        $date = strtotime($followup_date);
        $current_date = strtotime(date('Y-m-d'));
        if ($date < $current_date) {
            return false;
        } else {
            return true;
        }
    }

    //get the crm timeline
    public function time_line_get(){
        $lead_id = $this->input->get('id');
        $display = false;
        $lead_details = array(array());
        $followup_history = array();
        $quotations = array();
        $requirements = array();
        $lead_completeness = 0;
        $color = "green";
        if($lead_id){
            $lead_details = $this->crm_model->getLeadDetails($lead_id);
            if (count($lead_details) < 1 || $lead_id == '') {
                $this->set_error_response(422,1061);
            }
            $date = new DateTime($lead_details[0]['date']);
            $lead_details[0]['month_year'] = $date->format('Y') . " " . $date->format('M');
            $followup_history = $this->crm_model->getFollowupHistory($lead_id);
            $i = 0;
            foreach ($followup_history as $followup) {

                $followup = $this->crm_model->stripSlashArray($followup);
                $followup['add_by'] =  $this->validation_model->IdToUserName($followup['followup_entered_by']);
                $date = new DateTime($followup['date']);
                $followup['month_year'] = $date->format('Y') . " " . $date->format('M');
                $followup['month_year_date'] = $date->format('d'). " " . $date->format('M'). " " . $date->format('Y');
                $followup_date = new DateTime($followup['date']);
                $followup['next_followup'] = $followup_date->format('d'). " " . $followup_date->format('M'). " " . $followup_date->format('Y');
                if($followup['file_name'])
                {
                    $followup['file_name'] = SITE_URL. "/uploads/images/document/".$followup['file_name'];
                }
                $followup_history[$i] = $followup;
                $i++;
            }

            $lead_completeness = $lead_details[0]['percent'];
            $color = $lead_details[0]['color'];
            $display = true;
        }else{
            $this->set_error_response(422,1061);
        }
        $data = [
            'display' => $display,
            'lead_details' => $lead_details[0],
            'followup_history' => $followup_history,
            'lead_completeness' =>$lead_completeness,
            'color' => $color
        ];
        $this->set_success_response(200,$data);
    }

    
}