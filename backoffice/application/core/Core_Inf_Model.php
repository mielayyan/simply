<?php

class Core_Inf_Model extends CI_Model
{

    public $ARR_SCRIPT;

    function __construct()
    {
        $table_prefix = '';
        $session_data = $this->session->userdata('inf_logged_in');
        if (isset($this->uri->segments[1]) && $this->uri->segments[1] != 'mobile' && $session_data) {
            $table_prefix = $session_data['table_prefix'];
        }

        if (isset($session_data['table_prefix'])) {
            $table_prefix = $session_data['table_prefix'];
        }

        if (isset($_GET['table_prefix'])) {
            $table_prefix = $_GET['table_prefix'];
            if ($this->isValidDemoID($table_prefix)) {
                $this->table_prefix = $table_prefix . "_";
                $this->session->set_userdata('inf_table_prefix', $this->table_prefix);
            }
        }

        if (isset($this->session->userdata['inf_table_prefix'])) {
            $table_prefix = $this->session->userdata['inf_table_prefix'];
        }

        if ($table_prefix != '') {
            $this->setDBPrefix($table_prefix);
            $this->table_prefix = $table_prefix;
        }
    }

    public function setDBPrefix($table_prefix)
    {
        if ($table_prefix != '') {
            $this->db->set_dbprefix($table_prefix);
            $this->db->set_ocprefix($table_prefix . "oc_");
        }
    }

    public function isValidDemoID($demo_id)
    {
        $flag = FALSE;
        $count = 0;

        $query = $this->db->query("SELECT COUNT(*) AS `numrows` FROM (`infinite_mlm_user_detail`) WHERE `id` = '$demo_id' AND `account_status` != 'deleted'");
        foreach ($query->result() as $row) {
            $count = $row->numrows;
        }
        if ($count) {
            $flag = TRUE;
        }
        return $flag;
    }

    public function begin()
    {
        $this->db->trans_start();
    }

    public function commit()
    {
        $this->db->trans_commit();
    }

    public function rollBack()
    {
        $this->db->trans_rollback();
    }

    public function startTransaction()
    {
        $this->db->trans_start();
    }

    public function finishTransaction()
    {
        $this->db->trans_complete();
    }

    public function isTransactionSuccess()
    {
        return $this->db->trans_status() === TRUE;
    }
}
