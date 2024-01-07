<?php

require_once 'Inf_Controller.php';

class Auto_responder extends Inf_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('profile_model');
        $this->load->model('configuration_model');
        $this->url_permission('autoresponder_status');
    }

    function auto_responder_settings($action = '', $id = '') {
        
        //if($id!="")
       //{
        // $id = urldecode($id);
        // $id = str_replace('_', '/', $id);
        // $id = $this->encryption->decrypt($id);
        //}
        $title = lang('auto_responder_settings');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('auto_responder_settings');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('auto_responder_settings');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $help_link = "AutoResponder";
        $this->set("help_link", $help_link);

        $mail_details = $this->auto_responder_model->getAutoResponderData();
        $count = count($mail_details);
        $sub = '';
        $mail_cond = '';
        $send_date = '';
        $base_url = base_url() . "admin/auto_responder/auto_responder_settings";
        $config = $this->pagination->customize_style();
        $config['base_url'] = $base_url;
        $config['per_page'] = $this->PAGINATION_PER_PAGE;
        $total_rows = $this->auto_responder_model->getAutoCount();
        $config['total_rows'] = $total_rows;
        $config["uri_segment"] = 4;
        $this->pagination->initialize($config);
        if($action == 'delete' || $action == 'edit') {
            $page = 0;
        }
        else {
            $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        }
        
        
        $this->set("page", $page);
        
        $mail_data = $this->auto_responder_model->getAuto($config['per_page'], $page);
     
        $this->set("mail_data", $mail_data);
        $this->set('mail_details', $mail_details);
        $this->set('count', $count);

        if ($this->input->post('update')) {
            $this->lang->load('validation');
            $this->form_validation->set_rules('mail_content', lang('mail_content'), 'required',[
               'required'=>lang('required'),
            ]);
            $this->form_validation->set_rules('subject',  lang('subject'), 'required',[
              'required'=>lang('required'),
            ]);
            $this->form_validation->set_rules('date_to_send',  lang('date_to_send'), 'required',[
              'required'=>lang('required'),
            ]);

            $val = $this->form_validation->run();

            if ($val) {
                $settings_post_array = $this->input->post(NULL, TRUE);
                $settings_post_array = $this->validation_model->stripTagsPostArray($settings_post_array);
                $settings_post_array['mail_content'] = $this->validation_model->stripTagTextArea($this->input->post('mail_content'));
                $mail_setting['mail_content'] = $settings_post_array['mail_content'];
                $mail_setting['subject'] = $settings_post_array['subject'];
                $mail_setting['date_to_send'] = $settings_post_array['date_to_send'];
                $mail_setting['mail_number'] = $settings_post_array['mail_number'];               
                $res = $this->auto_responder_model->insertIntoAutoResponder($mail_setting);
                if ($res) {
                    $data = serialize($mail_setting);
                    $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'auto responder settings updated', $this->LOG_USER_ID, $data);
                    
                    // Employee Activity History
                    if ($this->LOG_USER_TYPE == 'employee') {
                        $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'update_auto_responder_setting', 'Auto Responder Settings Updated');
                    }
                    //
                    
                    if ($mail_setting['mail_number'] != "NA") {
                        $msg = lang('auto_responder_details_updated');
                    } else {
                        $msg = 'Auto responder details inserted sucessfully';
                    }
                    $this->redirect($msg, "auto_responder/auto_responder_details", true);
                } else {
                    if ($mail_setting['mail_number'] != "NA") {
                        $msg = lang('unable_to_update_auto_responder_details');
                    } else {
                        $msg = 'Unable to insert autoresponder details.';
                    }
                    $this->redirect($msg, "auto_responder/auto_responder_settings", false);
                }
            }
            else {
                $settings_post_array = $this->input->post(NULL, TRUE);
                $settings_post_array = $this->validation_model->stripTagsPostArray($settings_post_array);
                $settings_post_array['mail_content'] = $this->validation_model->stripTagTextArea($this->input->post('mail_content'));
                $sub = $settings_post_array['subject'];
                $mail_cond = $settings_post_array['mail_content'];                
                $send_date =  $settings_post_array['date_to_send'];
            }
        }
        $this->set('edit', 'false');
        if ($action == 'edit') {
            $edit_id = $id;
            $current_date = $this->auto_responder_model->getCurrentmailDate($edit_id);
            $mail_details = $this->auto_responder_model->getAutoResponderData($edit_id);
            $this->set('current_date', $current_date);
            $this->set('mail_details', $mail_details);
            $this->set('edit', 'true');
        }
        if ($action == 'delete') {
            $delete_id = $id;
            $mail_details = $this->auto_responder_model->DeleteAutoResponderData($delete_id);
            if ($mail_details) {
                $data_array['mail_id'] = $delete_id;
                $data = serialize($data_array);
                $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'auto responder deleted', $this->LOG_USER_ID, $data);
                
                // Employee Activity History
                if ($this->LOG_USER_TYPE == 'employee') {
                    $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'delete_auto_responder', 'Auto Responder Deleted');
                }
                //
                
                $msg = lang('details_deleted');
                $this->redirect($msg, "auto_responder/auto_responder_details", TRUE);
            } else {
                $msg = lang('details_not_deleted');
                $this->redirect($msg, "auto_responder/auto_responder_details", FALSE);
            }
        }
        $this->set('send_date', $send_date);
        $this->set('sub', $sub);
        $this->set('mail_cond', $mail_cond);
        $this->setView();
    }
    
    function auto_responder_details($action = '', $id = '') {

        if($id!="")
        {
        $id = urldecode($id);
        $id = str_replace('_', '/', $id);
        $id = $this->encryption->decrypt($id);
        }
        $title = lang('auto_responder_settings');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('auto_responder_settings');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('auto_responder_settings');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $help_link = "AutoResponder";
        $this->set("help_link", $help_link);

        $mail_details = $this->auto_responder_model->getAutoResponderData();
        $count = count($mail_details);
        
        $base_url = base_url() . "admin/auto_responder/auto_responder_details";
        $config = $this->pagination->customize_style();
        $config['base_url'] = $base_url;
        $config['per_page'] = $this->PAGINATION_PER_PAGE;
        $total_rows = $this->auto_responder_model->getAutoCount();
        $config['total_rows'] = $total_rows;
        $config["uri_segment"] = 4;
        $this->pagination->initialize($config);
        if($action == 'delete' || $action == 'edit') {
            $page = 0;
        }
        else {
            $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        }
        
        
        $this->set("page", $page);
        
        $mail_data = $this->auto_responder_model->getAuto($config['per_page'], $page);

        $this->set("mail_data", $mail_data);
        $this->set('mail_details', $mail_details);
        $this->set('count', $count);

        if ($this->input->post('update')) {
            $this->form_validation->set_rules('mail_content', lang('mail_content'), 'required');
            $this->form_validation->set_rules('subject',  lang('subject'), 'required');
            $this->form_validation->set_rules('date_to_send',  lang('date_to_send'), 'required');

            $val = $this->form_validation->run();

            if ($val) {
                $settings_post_array = $this->input->post(NULL, TRUE);
                $settings_post_array = $this->validation_model->stripTagsPostArray($settings_post_array);
                $settings_post_array['mail_content'] = $this->validation_model->stripTagTextArea($this->input->post('mail_content'));
                $mail_setting['mail_content'] = $settings_post_array['mail_content'];
                $mail_setting['subject'] = $settings_post_array['subject'];
                $mail_setting['date_to_send'] = $settings_post_array['date_to_send'];
                $mail_setting['mail_number'] = $settings_post_array['mail_number'];
                $res = $this->auto_responder_model->insertIntoAutoResponder($mail_setting);
                if ($res) {
                    $data = serialize($mail_setting);
                    $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'auto responder settings updated', $this->LOG_USER_ID, $data);
                    
                    // Employee Activity History
                    if ($this->LOG_USER_TYPE == 'employee') {
                        $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'update_auto_responder_setting', 'Auto Responder Settings Updated');
                    }
                    //
                    
                    if ($mail_setting['mail_number'] != "NA") {
                        $msg = lang('auto_responder_details_updated');
                    } else {
                        $msg = 'Auto responder Details Inserted Sucessfully';
                    }
                    $this->redirect($msg, "auto_responder/auto_responder_settings", true);
                } else {
                    if ($mail_setting['mail_number'] != "NA") {
                        $msg = lang('unable_to_update_auto_responder_details');
                    } else {
                        $msg = 'Unable to insert autoresponder details.';
                    }
                    $this->redirect($msg, "auto_responder/auto_responder_settings", false);
                }
            }
        }

        $this->setView();
    }
    
    function read_mail($id = '') { 
        
        if($id!="")
        {
        $id = urldecode($id);
        $id = str_replace('_', '/', $id);
        $id = $this->encryption->decrypt($id);
        } else {
                               
            $this->redirect('', "auto_responder/auto_responder_details", false);
        }
        $title = lang('auto_responder_settings');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('auto_responder_settings');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('auto_responder_settings');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $help_link = "AutoResponder";
        $this->set("help_link", $help_link);
        
        $mail_details = $this->auto_responder_model->getAutoResponderData($id);
       
//        print_r($mail_details);die;
        $this->set('current_date', $mail_details['date_to_send']);
        $this->set('sub', $mail_details['subject']);
        $this->set('mail_details', $mail_details['content']);
        $this->setView();
    }

}
