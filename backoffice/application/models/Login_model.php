<?php

class login_model extends inf_model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function login($username, $password)
    {

        if ($username && $password) {
            $this->db->select('id, user_name, password,user_type');
            $this->db->from('ft_individual');
            $this->db->where('user_name', $username);
            #START_USER_TYPE#
            #END_USER_TYPE#
            $this->db->where('active', "yes");
            $this->db->limit(1);
            $query = $this->db->get();

            if (($query->num_rows()) != 1) {

                $module_status = $this->validation_model->getModuleStatus();

                $login_unapproved_status = $this->validation_model->getLoginUnapprovedStatus();

                if ($module_status['opencart_status'] == 'yes' && $module_status['opencart_status_demo'] == 'yes' && $login_unapproved_status == 'yes') {
                    $this->db->select('*');
                    $this->db->from('oc_temp_registration');
                    $this->db->where('status', 'pending');
                    $this->db->where('user_name', $username);
                    $this->db->limit(1);
                    $query1 = $this->db->get();
                    if ($query1->num_rows() > 0) {
                        $flag_new = true;
                        $flag = false;

                        $db_pass = (unserialize(($query1->result()[0]->reg_data)))['reg_data']['pswd'];
                        $query1->result()[0]->password = $db_pass;
                        $query1->result()[0]->user_type = 'Unapproved'; //$login_type;
                        //$query1->result()[0]->admin_user_name = $admin_username;

                    } else {
                        $flag_new = false;
                    }
                } else if ($login_unapproved_status == 'yes') {
                    $this->db->select('*');
                    $this->db->from('pending_registration');
                    $this->db->where('status', 'pending');
                    $this->db->where('user_name', $username);
                    $this->db->limit(1);
                    $query1 = $this->db->get();
                    if ($query1->num_rows() > 0) {
                        $flag = false;
                        $flag_new = true;
                        $query1->result()[0]->password = (json_decode($query1->result()[0]->data))->pswd;
                        $query1->result()[0]->user_type = 'Unapproved'; //$login_type;
                        //$query1->result()[0]->admin_user_name = $admin_username;

                    } else {
                        $flag_new = false;
                    }
                } else {

                    $flag_new = false;
                    $flag = false;
                }
            }
        } else {
            return false;
        }

        if (($query->num_rows()) != 1) {

            if ($flag_new) {

                $flag_new = $this->validation_model->verifyBcryptForUnapprovedUer($query1, $password);
            } else {

                $flag = $this->validation_model->verifyBcrypt($query, $password);
            }
        } else {

            $flag = $this->validation_model->verifyBcrypt($query, $password);
        }
        if ($flag) {
            $login_id = $this->validation_model->userNameToID($username);
            // if ($login_id == 0) {
            //     $login_id = $this->validation_model->employeeNameToID($username);
            // }
            $user_type = $this->validation_model->getUserType($login_id);
            $this->validation_model->insertUserActivity($login_id, 'login', $login_id, $data = '', $user_type);

            if ($user_type == "employee") {
                $login_id = $this->validation_model->employeeNameToID($username);
                // $this->validation_model->insertEmployeeActivity($login_id, $login_id, 'login', 'logged in');
            }
            return $query->result();
        } else if ((isset($flag_new)) && ($flag_new)) {

            //$this->session->set_userdata('inf_table_prefix', $table_prefix);
            return $query1->result(); // this session is for login un approved user
        }
    }

    public function setUserSessionDatas($login_result)
    {
        $sess_array = array();
        $table_prefix = $this->db->dbprefix;
        $admin_username = $this->validation_model->getAdminUsername();
        $admin_userid = $this->validation_model->userNameToID($admin_username);
        foreach ($login_result as $row) {
            $sess_array = array(
                'user_id' => $row->id,
                'user_name' => $row->user_name,
                'user_type' => $row->user_type,
                'admin_user_name' => $admin_username,
                'admin_user_id' => $admin_userid,
                'table_prefix' => $table_prefix,
                'is_logged_in' => true
            );
        }

        $this->inf_model->trackModule();
        $sess_array['mlm_plan'] = $this->inf_model->MODULE_STATUS['mlm_plan'];
        $this->session->set_userdata('inf_logged_in', $sess_array);
    }

    public function isValidEmployee($user_name)
    {
        $flag = FALSE;

        $this->db->where('user_name', $user_name);
        $this->db->where('emp_status !=', 'terminated');
        $this->db->from('login_employee');
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            $flag = TRUE;
        }
        return $flag;
    }

    public function login_employee($username, $password)
    {

        $username = ($username);
        $password = ($password);

        $this->db->select('*');
        $this->db->from('login_employee');
        $this->db->where('user_name', $username);
        $this->db->where('addedby', "code");
        $this->db->where('emp_status', "yes");
        $this->db->limit(1);
        $query = $this->db->get();

        $flag = $this->validation_model->verifyBcrypt($query, $password);

        if ($flag) {
            $login_id = $this->validation_model->userNameToID($username);
            $user_type = $this->validation_model->getUserType($login_id);
            $this->validation_model->insertUserActivity($login_id, 'login', $login_id, $data = '', $user_type);
            return $query->result();
        } else {
            return false;
        }
    }

    public function setUserSessionDatasEmployee($login_result)
    {

        $sess_array = array();
        $module_status = "";
        $admin_username = $this->validation_model->getAdminUsername();
        $admin_userid = $this->validation_model->userNameToID($admin_username);
        foreach ($login_result as $row) {
            $sess_array = array(
                'user_id' => $row->user_id,
                'user_name' => $row->user_name,
                'user_type' => $row->user_type,
                'admin_user_name' => $admin_username,
                'admin_user_id' => $admin_userid,
                'table_prefix' => $this->db->dbprefix,
                'is_logged_in' => true
            );
            $module_status = $row->module_status;
        }
        $this->session->set_userdata('inf_module_status', $module_status);

        $this->inf_model->trackModule();
        $sess_array['mlm_plan'] = $this->inf_model->MODULE_STATUS['mlm_plan'];
        $this->session->set_userdata('inf_logged_in', $sess_array);
    }

    public function isUsernameValid($user_name)
    {
        $this->db->select('id');
        $this->db->from('ft_individual');
        $this->db->where('user_name', $user_name);
        #START_USER_TYPE#
        #END_USER_TYPE#
        $count = $this->db->count_all_results();
        $flag = ($count > 0);
        return $flag;
    }

    public function getUserId($user_name, $table_prefix)
    {
        $dbprefix = $this->db->dbprefix;
        $this->db->set_dbprefix($table_prefix . '_');
        $id = '';
        $this->db->select('id');
        $this->db->from('ft_individual');
        $this->db->where('user_name', $user_name);
        $query = $this->db->get();
        $this->db->set_dbprefix($dbprefix);
        foreach ($query->result() as $row) {
            $id = $row->id;
        }
        return $id;
    }

    public function getUserPhoto($table_prefix, $user_id)
    {
        $dbprefix = $this->db->dbprefix;
        $this->db->set_dbprefix($table_prefix . '_');
        $file_name = NULL;
        $this->db->select('user_photo');
        $this->db->from('user_details');
        $this->db->where('user_detail_refid', $user_id);
        $query = $this->db->get();
        $this->db->set_dbprefix($dbprefix);

        foreach ($query->result() as $row) {
            $file_name = $row->user_photo;
            if (!file_exists(IMG_DIR . 'profile_picture/' . $file_name)) {
                $file_name = 'nophoto.jpg';
            }
        }
        return $file_name;
    }

    public function getKeyWord($user_id)
    {
        $row = NULL;

        do {
            $keyword = rand(1000000000, 9999999999);
        } while ($this->keywordAvailable($keyword));

        $this->db->set('keyword', $keyword);
        $this->db->set('user_id', $user_id);
        $result = $this->db->insert("password_reset_table");

        if ($result) {
            return $keyword;
        }
    }

    public function keywordAvailable($keyword)
    {
        $flag = FALSE;
        $this->db->select('COUNT(*) AS count');
        $this->db->from('password_reset_table');
        $this->db->where('keyword', $keyword);
        $this->db->where('reset_status', 'no');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $cnt = $row['count'];
            if ($cnt > 0) {
                $flag = TRUE;
            }
            return $flag;
        }
    }

    public function getAuthKeyWord($user_id)
    {
        $row = NULL;
        do {
            $keyword = rand(1000000000, 9999999999);
        } while ($this->keywordAvailable($keyword));

        $this->db->set('keyword', $keyword);
        $this->db->set('user_id', $user_id);
        $result = $this->db->insert("googleAuth_reset_table");
        if ($result) {
            return $keyword;
        }
    }
    public function updateGoogleAuthkeyStatus($user_id, $keyword)
    {
        $this->db->set('reset_status', 'yes');
        $this->db->where('keyword', $keyword);
        $this->db->where('user_id', $user_id);
        $result = $this->db->update("googleAuth_reset_table");
        return $result;
    }

    public function verifyLoginCredentials($username, $password)
    {
        $this->db->select('id,password');
        $this->db->where('user_name', $username);
        $this->db->where('active', 'yes');
        $query = $this->db->get('ft_individual');
        $password_hash = $query->row_array()['password'] ?? '';
        $user_id = $query->row_array()['id'] ?? '';
        return password_verify($password, $password_hash) ? $user_id : false;
    }

    public function checkUserGoogleAuthStatus($username)
    {
        return $this->db->select('google_auth_status')
            ->where('user_name', $username)
            ->get('ft_individual')->row('google_auth_status');
    }

    public function getKeyWordFromDB($user_id)
    {
        $array = $this->db->select("keyword")
            ->where('user_id', $user_id)
            ->where('reset_status', 'no')
            ->get("password_reset_table")
            ->result_array();
        if (count($array))
            return $array[0]["keyword"];
        return "";
    }

    public function getInfLoggedInArrayFromCustomerId($customer_id, $table_prefix = "")
    {
        if ($table_prefix) {
            $table_prefix_pre = $this->db->dbprefix;
            $this->db->set_dbprefix("{$table_prefix}_");
        }

        $adminId = $this->validation_model->getAdminId();
        $adminUsername = $this->validation_model->getAdminUsername();

        $array = $this->db->select("id, user_type, user_name")
            ->where("oc_customer_ref_id", $customer_id)
            ->get("ft_individual")->result_array();
        if (!count($array))
            return [];

        $moduleStatus = $this->validation_model->getModuleStatus();

        $sess_array = array(
            'user_id' => $array[0]["id"],
            'user_name' => $array[0]["user_name"],
            'user_type' => $array[0]["user_type"],
            'admin_user_name' => $adminUsername,
            'admin_user_id' => $adminId,
            'table_prefix' => $this->db->dbprefix,
            'is_logged_in' => true,
            'mlm_plan' => $moduleStatus["mlm_plan"]
        );

        return $sess_array;
    }

    public function checkDeletedUser($username, $dbprefix = "")
    {
        if ($dbprefix) {
            $cur_dbprefix = $this->db->dbprefix;
            $this->db->set_dbprefix($dbprefix);
        }
        $count = $this->db->where("user_name", $username)
            ->where("delete_status", "deleted")
            ->where("user_type !=", "admin")
            ->count_all_results("ft_individual");

        if ($dbprefix) {
            $this->db->set_dbprefix($cur_dbprefix);
        }

        return ($count > 0);
    }

    public function isValidAgent($user_name)
    {
        $flag = FALSE;

        $this->db->where('agent_username', $user_name);
        $this->db->where('status !=', 'no');
        $this->db->from('agents');
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            $flag = TRUE;
        }
        return $flag;
    }

    public function setUserSessionDatasAgent($login_result)
    {

        $sess_array = array();
        $module_status = "";
        $admin_username = $this->validation_model->getAdminUsername();
        $admin_userid = $this->validation_model->userNameToID($admin_username);
        foreach ($login_result as $row) {
            $sess_array = array(
                'user_id' => $row->id,
                'user_name' => $row->agent_username,
                'user_type' => 'agent',
                // 'user_type' => $row->user_type,
                'admin_user_name' => $admin_username,
                'admin_user_id' => $admin_userid,
                'table_prefix' => $this->db->dbprefix,
                'is_logged_in' => true
            );
            $module_status = $row->module_status;
        }
        $this->session->set_userdata('inf_module_status', $module_status);

        $this->inf_model->trackModule();
        $sess_array['mlm_plan'] = $this->inf_model->MODULE_STATUS['mlm_plan'];
        $this->session->set_userdata('inf_logged_in', $sess_array);
    }

    public function login_agent($username, $password)
    {

        $username = ($username);
        $password = ($password);

        $this->db->select('*');
        $this->db->from('agents');
        $this->db->where('agent_username', $username);
        // $this->db->where('addedby', "code");
        $this->db->where('status', "yes");
        $this->db->limit(1);
        $query = $this->db->get();
        $flag = $this->validation_model->verifyAgentBcrypt($query, $password);

        if ($flag) {
            $login_id = $this->validation_model->userNameToID($username);
            $user_type = $this->validation_model->getUserType($login_id);
            $this->validation_model->insertUserActivity($login_id, 'login', $login_id, $data = '', $user_type);
            return $query->result();
        } else {
            return false;
        }
    }
}
