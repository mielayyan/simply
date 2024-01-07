<?php

require 'Inf_Controller.php';

class Demo extends Inf_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('login_model');
        $this->load->model('password_model');
    }

    public function demo_credentials_get()
    {
        $login_details = $this->validation_model->getRandomDemoUserCredentials();
        if (!count($login_details)) {
            $this->set_success_response(200, []);
        }
        $api_key = $this->configuration_model->getAdminApiKey($login_details['admin_username']);
        if (!$api_key)
            $this->set_success_response(200, []);
        // $login_details['api_key'] = $api_key;
        $this->set_success_response(200, $login_details);
    }

    public function api_key_get()
    {
        $this->load->model('configuration_model');
        $adminUserName = $this->get("admin");
        if (!$adminUserName)
            $adminUserName = "";
        $api_key = $this->configuration_model->getAdminApiKey($adminUserName);
        if (!$api_key)
            $this->set_error_response(405);

        $this->set_success_response(200, compact("api_key"));
    }

    public function admin_username_get()
    {
        $this->load->model('configuration_model');
        $email = $this->get("email");
        $admin_user_name = $this->validation_model->getAdminUserNameFromEmail($email);
        if (!$admin_user_name)
            $this->set_success_response(200, []);
        $this->set_success_response(200, compact("admin_user_name"));
    }
}
