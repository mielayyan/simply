<?php

ini_set('max_execution_time', '0');

class Migrate extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("DemoFixModel");
	}

	public function index()
	{
        $this->db->query("SET FOREIGN_KEY_CHECKS=0;");
		$dbprefix = $this->db->dbprefix;
        // dd($dbprefix);
        $this->load->dbforge();
        $this->db->set_dbprefix('');
        $demo_list = $this->getDemoList();
        array_push($demo_list, array('id' => 'inf', 'table_prefix' => 'inf'));
        foreach ($demo_list as $demo) {
            $new_dbprefix = "{$demo['table_prefix']}_";
            $this->db->set_dbprefix($new_dbprefix);
            if(!$this->db->table_exists("module_status")) {
                continue;
            }
            $this->load->library("migration", "", "migration_{$demo['id']}");
            if ($this->{"migration_{$demo['id']}"}->current() === false) {
            	show_error($this->{"migration_{$demo['id']}"}->error_string());
            	die();
        	}
        }
        $this->db->set_dbprefix($dbprefix);
        $this->db->query("SET FOREIGN_KEY_CHECKS=1;");
	}

	public function list()
	{
		echo "migrations : <br><pre>";
        print_r($this->migration->find_migrations());
	}

    public function getDemoList()
    {
        $this->db->select('u.id,u.user_name,u.table_prefix');
        $this->db->from('infinite_mlm_user_detail u');
        $this->db->where('u.account_status !=', 'deleted');
        $this->db->where_not_in('u.user_name', ['user102']);
        $res = $this->db->get()->result_array();
        return $res;
    }
	
}