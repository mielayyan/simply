<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Mail_gun_model
 *
 * @author ioss
 */
class Mail_gun_model extends inf_model
{

    /**
     * Mailgun Configuration array
     *
     * @var array
     */

    public function __construct()
    {
        parent::__construct();
        $this->load->model('configuration_model');
        $config = $this->mailgunconfig();
        $this->load->library('inf_mailgun',$config);
    }
    public function sendEmail($mailBodyDetails, $email, $subject, $cc = '')
    {
        if ($_SERVER['HTTP_HOST'] != 'infinitemlmsoftware.com') {
            return true;
        }

        $mail_params = array(
            'name' => "Infinite MLM Software",
            'email' => $email,
            'subject' => $subject,
            'cc' => $cc,
            'text' => '',
            'html' => $mailBodyDetails
        );
        try {
            $response = $this->inf_mailgun->send($mail_params);
        } catch (Exception $e) {
            $response = false;
        }
        return $response;
    }
    
    public function mailgunconfig() {
        $mail_details = $this->configuration_model->getMailGunConfig();
        $config = [
            'from_name' => $mail_details['from_name'],
            'from' => $mail_details['from_email'],
            'reply_to' => $mail_details['reply_to'],
            'domain' => $mail_details['domain'],
            'api_key' => $mail_details['api_key'],
        ];
        return $config;
    }
}