<?php

require_once 'Inf_Controller.php';

class Lcp extends Inf_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('lcp_model');
        $this->load->model('validation_model');
        // $this->lang->load('common', $this->LANG_NAME);
    }

    public function addLcp_post()
    {
        $client_name = $this->input->post('user_name');

        $capture_details = $this->validation_model->stripTagsPostArray($this->input->post(null, true));
        $capture_details['user_id'] = $this->validation_model->userNameToID($client_name);

        if ($this->validate_lead()) {
            $res_crm = $this->lcp_model->InsertCrmLead($capture_details);
            if ($res_crm) {
                $msg = sprintf(lang('thanks_for_your_interest'), $client_name);
                $this->set_success_response(200, $msg);

            } else {
                $msg = $this->lang->line('Lead_not_Added');
                $this->set_error_response(401, 1003);
            }
        } else {
            $error = $this->form_validation->error_array();
            $msg = $this->lang->line('please_check_the_fields');
            $this->set_error_response(422, 1004);
        }
    }
    public function validate_lead() {
        $this->form_validation->set_rules('user_name', 'User Name', 'required', [
            'required' => sprintf(lang('required'), lang('user_name')),
            'max_length' => sprintf(lang('maxlength'), lang('user_name'), '250')
        ]);
        $this->form_validation->set_rules('first_name', 'First Name', 'required|max_length[250]', [
            'required' => sprintf(lang('required'), lang('first_name')),
            'max_length' => sprintf(lang('maxlength'), lang('first_name'), '250')
        ]);
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|max_length[250]', [
            'required' => sprintf(lang('required'), lang('last_name')),
            'max_length' => sprintf(lang('maxlength'), lang('last_name'), '250')
        ]);
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email', [
            'required' => sprintf(lang('required'), lang('email')),
            'email' => lang('valid_email')
        ]);
        $this->form_validation->set_rules('phone', lang('Your_Telephone_Cell_Number'), 'required|regex_match[/^[\s0-9+()-]+$/]', [
            'required' => sprintf(lang('required'), lang('Your_Telephone_Cell_Number')),
            'regex_match' => lang('phone_number')
        ]);
        $this->form_validation->set_rules('skype_id', lang('skype_id'), 'max_length[250]', [
            'max_length' => sprintf(lang('maxlength'), lang('skype_id'), '250')
        ]);
        $this->form_validation->set_rules('comment', lang('comment'), 'max_length[1000]', [
            'max_length' => sprintf(lang('maxlength'), lang('comment'), '1000')
        ]);
        $validation_status = $this->form_validation->run();
        return $validation_status;
    }
}

