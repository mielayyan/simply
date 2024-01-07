<?php

require_once 'Inf_Controller.php';

class Cleanup extends Inf_Controller {

    function __construct() {
        parent::__construct();
        ini_set('max_execution_time', '900');
    }

    function clean_up() {
        $title = $this->lang->line('clean_up');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        if(DEMO_STATUS == 'yes') {
            $is_preset_demo = $this->validation_model->isPresetDemo($this->ADMIN_USER_ID);
            if($is_preset_demo) {
                $msg = '<strong>Warning!</strong> Cleanup not allowed for Preset Demos.';
                $this->redirect($msg, "home/index", false);
            }
        }
        
        $this->load->model('cron_model');
        if(!$this->cron_model->backupDatabase()) {
            $msg = $this->lang->line('Clean_up_failed_try_again');
            $this->redirect($msg, "home/index", false);
        }

        $res = $this->cleanup_model->cleanup($this->MODULE_STATUS);

        if ($res) {
            $msg = $this->lang->line('Cleanup_done_successfully');
            $this->redirect($msg, "home/index", true);
        } else {
            $msg = $this->lang->line('Clean_up_failed_try_again');
            $this->redirect($msg, "home/index", false);
        }
    }

    public function reset_config()
    {
        $this->load->model('cron_model');
        if (!$this->cron_model->backupDatabase()) {
            $msg = $this->lang->line('config_reset_failed_try_again');
            $this->redirect($msg, "home/index", false);
        }

        $res = $this->cleanup_model->reset_config_tables($this->MODULE_STATUS);

        if ($res) {
            $msg = $this->lang->line('config_reset_done_successfully');
            $this->redirect($msg, "home/index", true);
        } else {
            $msg = $this->lang->line('config_reset_failed_try_again');
            $this->redirect($msg, "home/index", false);
        }
    }

    public function full_reset()
    {
        $this->load->model('cron_model');
        if (!$this->cron_model->backupDatabase()) {
            $msg = $this->lang->line('full_reset_failed_try_again');
            $this->redirect($msg, "home/index", false);
        }

        $res1 = $this->cleanup_model->cleanup($this->MODULE_STATUS);

        if (!$res1) {
            $msg = $this->lang->line('full_reset_failed_try_again');
            $this->redirect($msg, "home/index", false);
        }

        $res2 = $this->cleanup_model->reset_config_tables($this->MODULE_STATUS);

        if ($res2) {
            $msg = $this->lang->line('full_reset_done_successfully');
            $this->redirect($msg, "home/index", true);
        } else {
            $msg = $this->lang->line('full_reset_failed_try_again');
            $this->redirect($msg, "home/index", false);
        }
    }

    public function multi()
    {
        // die("commented");
        $this->cleanup_model->multi_user_registration(4);
        // $this->cleanup_model->opencart_migration();
    }

    public function infBackup()
    {
        $this->db->set_dbprefix('inf_');
        $this->load->model('cron_model');
        $this->cron_model->backupDatabase();
    }

    public function deleteCustomDemo($admin_user_name)
    {
        if(!in_array($admin_user_name, ['shajil', 'affarvk', 'ansueu', 'testioss'])) {
            echo 'no';
            die;
        }
        $this->cleanup_model->deleteCustomDemo('shajil');
        echo 'ok';
    }

    public function clearCustomDemos()
    {
        die('no');
        $demos = array_column(
            $this->db->query("select infinite_mlm_user_detail.user_name from infinite_mlm_user_detail left outer join inf_preset_demo_users on inf_preset_demo_users.user_name = infinite_mlm_user_detail.user_name")->result_array(),
            'user_name');
        foreach ($demos as $demo) {
            $this->cleanup_model->deleteCustomDemo($demo);
        }
        die('ok');
    }

}
