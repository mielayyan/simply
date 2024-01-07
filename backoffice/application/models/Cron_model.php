<?php

class cron_model extends inf_model {

    public function __construct() {
        $this->load->model('validation_model');
        $this->load->model('member_model');
        $this->load->library('inf_phpmailer', NULL, 'phpmailer');
    }

    public function getAutoMailSettings($today) {
        $i = 0;
        $mail_arr = array();
        $this->db->select('*')
                ->from('autoresponder_setting')
                ->where('date_to_send', $today);

        $qry = $this->db->get();

        foreach ($qry->result_array() as $row) {
            $mail_arr[$i]['subject'] = $row['subject'];
            $mail_arr[$i]['mail_content'] = $row['content'];
            $mail_arr[$i]['date_to_send'] = $row['date_to_send'];
            $i++;
        }

        return $mail_arr;
    }

    public function insertIntoCronHistory() {


        $data_arr = array('cron_name' => 'autoresponder',
            'cron_end_time' => date('Y-m-d H:i:s'),
            'cron_status' => 'started');

        $this->db->insert('cron_history', $data_arr);
        return $this->db->insert_id();
    }

    public function updateCronHistory($cron_id, $status) {
        $this->db->set("cron_status", $status);
        $this->db->set('cron_end_time', date("Y-m-d H:i:s"));
        $this->db->where('cron_id', $cron_id);
        $this->db->update('cron_history');
        return TRUE;
    }

    public function insertCronHistory($cron_name) {
        $this->db->set("cron_name", $cron_name);
        // $this->db->set('cron_date_time', date("Y-m-d H:i:s"));
        $this->db->set('cron_start_time', date("Y-m-d H:i:s"));
        $this->db->insert('cron_history');
        $cron_id = $this->db->insert_id();
        return $cron_id;
    }

    public function sentAutoresponderMail() {

        $this->load->model('auto_responder_model');
        $regr = array();
        $result = false;
        $visitor_details = $this->auto_responder_model->getVisitordetails();
        foreach ($visitor_details as $row) {
            $send_day = date('d');
            $mail_details = $this->getAutoMailSettings($send_day);
            // dd($mail_details);
            $count_mail_details = count($mail_details);
            for ($j = 0; $j <= $count_mail_details; $j++) {
                if(isset($mail_details[$j]['date_to_send'])){
                    if ($mail_details && $send_day == $mail_details[$j]['date_to_send']) {
                        $regr['sponser_phone_num'] = $this->validation_model->getUserPhoneNumber($row['user_id']);
                        $regr['username'] = $this->validation_model->IdToUserName($row['user_id']);
                        $regr['sponser_name'] = $this->validation_model->getFullName($row['user_id']);
                        $regr['sponser_email'] = $this->validation_model->getUserEmailId($row['user_id']);
                        $regr['user_name'] = $row['name'];
                        $regr['email'] = $row['email'];
                        $regr['first_name'] = $row['name'];
                        $regr['last_name'] = '';
                        $regr['mail_content'] = $mail_details[$j]['mail_content'];
                        $regr['subject'] = $mail_details[$j]['subject'];
                        $result = $this->mail_model->sendAllEmails($type = 'autoresponder', $regr);
                    }
                }
            }
        }
        return $result;
    }

    public function getAllTablePrifix() {
        $dbprefix = $this->db->dbprefix;
        $this->db->set_dbprefix('');
        $table_prifixs = array();
        $i = 0;
        $this->db->select('id');
        $this->db->where('account_status !=', 'deleted');
        $query = $this->db->get('infinite_mlm_user_detail');
        $this->db->set_dbprefix($dbprefix);
        foreach ($query->result_array() AS $rows) {
            $table_prefix = $rows['id'] . "_";
            $table_prifixs[$i] = $table_prefix;
            $i++;
        }
        return $table_prifixs;
    }

    public function clearCache() {
        $result = false;


        $MODULE_STATUS = $this->trackModule();

         //$folder_list[0] = BASEPATH . '../application/views/templates_c/';

        // $folder_list[1] = BASEPATH . '../application/logs/';

        // if ($MODULE_STATUS['replicated_site_status'] == 'yes' && $MODULE_STATUS['replicated_site_status_demo'] == 'yes') {
        //     $folder_list[] = BASEPATH . '../../replica/application/views/templates_c/';
        //     $folder_list[] = BASEPATH . '../../replica/application/logs/';
        // }

        // if ($MODULE_STATUS['lead_capture_status'] == 'yes' && $MODULE_STATUS['lead_capture_status_demo'] == 'yes') {
        //     $folder_list[] = BASEPATH . '../../LCP/application/logs/';
        //     $folder_list[] = BASEPATH . '../../LCP/application/views/templates_c/';
        // }

        // if ($MODULE_STATUS['opencart_status'] == 'yes' && $MODULE_STATUS['opencart_status_demo'] == 'yes') {
        //     $folder_list[] = BASEPATH . '../../store/vqmod/vqcache/';
        //     $folder_list[] = BASEPATH . '../../store/vqmod/';
        //     $folder_list[] = BASEPATH . '../../store/system/cache/';
        // }

        $folder_list[] = dirname(FCPATH)."/backoffice/application/sessions/";
        $folder_list[] = dirname(FCPATH)."/backoffice/application/views/templates_c/";
         if ($MODULE_STATUS['opencart_status'] == 'yes' && $MODULE_STATUS['opencart_status_demo'] == 'yes') {
        $folder_list[] = dirname(FCPATH)."/store/system/storage/cache/";
        $folder_list[] = dirname(FCPATH)."/store/system/storage/session/";
        }

        $dont_delete = array("index.html", "index.php", "pathReplaces.php", "vqmod.php", "install", "xml", "vqcache", "logs");

        $k = 0;
        $this->load->helper("file");
        $this->load->helper('directory');

        foreach ($folder_list AS $folder) {

            $file_list = directory_map($folder, 1);

            if (!empty($file_list)) {
                foreach ($file_list as $file) {
                    if (!in_array($file, $dont_delete)) {
                        $file_path = $folder . $file;
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                }
            }
        }
        return true;
    }

    public function calculateDailyInvestment() {
        $res = false;
        $roi_commission_status = $this->validation_model->getCompensationConfig('roi_commission_status');

        if($roi_commission_status == 'yes') {
            $all_users = $this->getAllActiveUsers();
            $amount_type = "daily_investment";
            $count = 0;
            $user_count = count($all_users);
            $res = true;
            if ($user_count > 0) {
                $config_details = $this->validation_model->getConfig(['tds', 'service_charge']);
                $tds_db = $config_details["tds"];
                $service_charge_db = $config_details["service_charge"];

                foreach ($all_users as $user) {
                    $user_poduct_details = array();
                    $user_id = $user['id'];
                    $product_id = $user['product_id'];
                    $user_poduct_details = $this->getUserProductDetails($user_id);
                    foreach ($user_poduct_details as $details) {
                        $poduct_details = array();
                        $poduct_details = $this->getProductDetails($details['prod_id']);
                        $roi = $poduct_details['roi'];
                        $days = $details['days'];

                        $prod_id = $details['prod_id'];
                        $products_id = $poduct_details['product_id'];

                        $product_value = $details['amount'];
                        $date = date('Y-m-d H:i:s');
                        $total_amount = ($product_value * $roi) / 100;

                        $tds = ($total_amount * $tds_db) / 100;
                        $service_charge = ($total_amount * $service_charge_db) / 100;
                        $amount_payable = $total_amount - ($tds + $service_charge);

                        $count = $this->getDaysCount($user_id, $products_id);

                        if ($days > $count) {
                            if ($total_amount > 0) {
                                $res = $this->insertInToLegAmount($user_id, $total_amount, $amount_payable, $tds, $service_charge, $date, 0, $amount_type, $user_id, $products_id, 0, $product_value, 0);
                            }
                        }
                    }
                }
            }
        }
        return $res;
    }

    public function getAllActiveUsers() {
        $detail = array();
        $this->db->select('id,product_id,date_of_joining');
        $this->db->from('ft_individual');
        $this->db->where('active', 'yes');
        $this->db->where('user_type !=', 'admin');
        $res = $this->db->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $detail[$i]['id'] = $row->id;
            $detail[$i]['product_id'] = $row->product_id;
            $detail[$i]['date_of_joining'] = $row->date_of_joining;
            $i++;
        }
        return $detail;
    }

    public function getUserProductDetails($id) {

        $this->db->select('*');
        $this->db->from('roi_order');
        $this->db->where('user_id', $id);
        $res = $this->db->get();
        return $res->result_array();
    }

    public function getDaysCount($user_id, $prod_id) {

        $amount_type = "daily_investment";
        $this->db->where('user_id', $user_id);
        $this->db->where('amount_type', $amount_type);
        $this->db->where('product_id', $prod_id);
        $this->db->from('leg_amount');
        $count = $this->db->count_all_results();
        return $count;
    }

    public function getProductDetails($product_id) {

        $this->db->select('*');
        $this->db->from('package');
        $this->db->where('prod_id', $product_id);
        $res = $this->db->get();
        return $res->row_array();
    }

    public function insertInToLegAmount($user_id, $total_amount, $amount_payable, $tds_amount, $service_charge, $date_of_sub, $level, $amount_type, $from_user = NULL, $product_id = 0, $product_pair_value = 0, $product_amount = 0, $oc_order_id = 0) {

        $result = false;

        if ($total_amount) {
            $date_of_sub = strtotime($date_of_sub);
            $date_of_sub += 1;
            $date_of_sub = date("Y-m-d H:i:s", $date_of_sub);

            $this->db->set('user_id', $user_id);
            $this->db->set('from_id', $from_user);
            $this->db->set('total_amount', round($total_amount, 8));
            $this->db->set('amount_payable', round($amount_payable, 8));
            $this->db->set('tds', round($tds_amount, 8));
            $this->db->set('service_charge', round($service_charge, 8));
            $this->db->set('date_of_submission', $date_of_sub);
            $this->db->set('user_level', $level);
            $this->db->set('amount_type', $amount_type);
            $this->db->set('product_id', $product_id);
            $this->db->set('pair_value', $product_pair_value);
            $this->db->set('product_value', $product_amount);

            $MODULE_STATUS = $this->trackModule();
            if ($MODULE_STATUS['opencart_status_demo'] == "yes" && $MODULE_STATUS['opencart_status'] == "yes") {
                $this->db->set('oc_order_id', $oc_order_id);
            }

            $result = $this->db->insert('leg_amount');

            if ($result) {
                $ewallet_id = $this->db->insert_id();
                $this->validation_model->addEwalletHistory($user_id, $from_user, $ewallet_id, 'commission', $amount_payable, $amount_type, 'credit');
                $this->updateBalanceAmount($user_id, $amount_payable);

                if (ANDROID_APP_STATUS == "yes") {

                    $subfolder = 'admin';
                    if ($this->LOG_USER_TYPE != 'admin' && $this->LOG_USER_TYPE != 'employee') {
                        $subfolder = 'user';
                    }
                    $this->lang->load('ewallet', $this->LANG_NAME . "/$subfolder");

                    if ($amount_type == "leg") {
                        $amount_type = lang('binary_commission');
                    } elseif ($amount_type == "level_commission") {
                        $amount_type = lang("level_commission");
                    } else if ($amount_type == "auto_board_1") {
                        $amount_type = lang('auto_board_1');
                    } else if ($amount_type == "board_fill_commission") {
                        $amount_type = lang("board_fill_commission");
                    } elseif ($amount_type == "rank_commission") {
                        $amount_type = lang('rank_commission');
                    } else if ($amount_type == "referral") {
                        $amount_type = lang('referal_commission');
                    }

                    $from_username = $this->validation_model->IdToUserName($from_user);
                    $data = array("message" => sprintf(lang('you_received_commission_from_user'), $amount_type, $amount_payable, $from_username));

                    $api_key = $this->validation_model->getUserApiKey($user_id);
                    $this->validation_model->sendGoogleCloudMessage($data, $api_key);
                }
            }
        }
        return $result;
    }

    public function updateBalanceAmount($user_id, $total_amount) {

        $this->db->set('balance_amount', 'ROUND(balance_amount +' . $total_amount . ',8)', FALSE);
        $this->db->where('user_id', $user_id);
        $this->db->limit(1);
        $res = $this->db->update('user_balance_amount');

        return $res;
    }

    public function getHolidayStatus($date) {
        $this->db->select('*');
        $this->db->from('public_holidays');
        $this->db->where('status', 1);
        $this->db->where('date', $date);
        $res = $this->db->get();

        foreach ($res->result() as $row) {
            return FALSE;
        }
        return TRUE;
    }

    /* Backup Function Starts */

    public function backupDatabase() {

        ini_set("memory_limit", "10000M");
        ini_set("max_execution_time", "20000");

        $this->load->dbutil();
        $this->load->helper('file');

        $datetime = date("Y-m-d-H-i-s");
        $backup_file_name = "{$this->db->dbprefix}ims_" . $datetime;
        $backup_file_ext = '.gz';
        $backup_file_dir = FCPATH . 'db_backup/dump/';
        $backup_file_url = base_url() . 'db_backup/dump/';
        $backup_file = $backup_file_dir . $backup_file_name . $backup_file_ext;

        if (file_exists($backup_file)) {
            return false;
        }

        $prefs = array(
            'tables' => array(),
            'ignore' => array(),
            'format' => 'gzip',
            'filename' => $backup_file_name,
            'foreign_key_checks' => FALSE,
        );
        $backup = $this->dbutil->backup($prefs);
        $res = write_file($backup_file, $backup);

        $this->deleteOldBackups(7, $backup_file_dir, array('gz'));

        // $this->sendBackupMail($backup_file_name . $backup_file_ext, $backup_file, $backup_file_url . $backup_file_name . $backup_file_ext);

        return $res;
    }

    public function deleteOldBackups($days_before, $file_path, $file_ext) {
        $days = $days_before;
        $path = $file_path;
        $filetypes_to_delete = $file_ext;
        $old_files_found = false;

        // Open the directory
        if ($handle = opendir($path)) {
            // Loop through the directory
            while (false !== ($file = readdir($handle))) {
                // Check the file we're doing is actually a file
                if (is_file($path . $file)) {
                    $file_info = pathinfo($path . $file);
                    if (isset($file_info['extension']) && in_array(strtolower($file_info['extension']), $filetypes_to_delete)) {
                        // Check if the file is older than X days old
                        if (filemtime($path . $file) < ( time() - ( $days * 24 * 60 * 60 ) )) {
                            echo "The file $file is older than $days days. <br>";
                            $old_files_found = true;
                            // Do the deletion
                            unlink($path . $file);
                        }
                    }
                }
            }
        }
        if ($old_files_found) {
            echo 'Old backup files deleted.';
        }
    }

    public function sendBackupMail($file_name, $path, $url) {

        $this->load->library('inf_phpmailer', NULL, 'phpmailer');
        $this->load->model('configuration_model');

        $time = date('Y-m-d H:i:s');
        $common_mail_settings = $this->configuration_model->getMailDetails();
        $mail_type = $common_mail_settings['reg_mail_type'];
        $smtp_data = array();
        if ($mail_type == "smtp") {
            $smtp_data = array(
                "SMTPAuth" => $common_mail_settings['smtp_authentication'],
                "SMTPSecure" => ($common_mail_settings['smtp_protocol'] == "none") ? "" : $common_mail_settings['smtp_protocol'],
                "Host" => $common_mail_settings['smtp_host'],
                "Port" => $common_mail_settings['smtp_port'],
                "Username" => $common_mail_settings['smtp_username'],
                "Password" => $common_mail_settings['smtp_password'],
                "Timeout" => $common_mail_settings['smtp_timeout']
            );
        }
        $mail_to = array("email" => 'path@teamioss.com', "name" => 'IOSS');
        $mail_from = array("email" => 'sijina@teamioss.com', "name" => 'IOSS');
        $mail_reply_to = $mail_from;
        $mail_subject = "Database backup - $time";
        $attachments = array($path);
        $mail_body = "Please find the attached file containing backup of your MLM Software.
                            <br/><br/>
                            File name : $file_name <br/>
                            <br/>
                            To Download the File
                            <a heref='" . $url . "'>Click Here</a>
                            <br/><br/>
                            Keep this file safe in order to restore the database if required.
                            Regards,
                            <br /><b>Team IOSS</b>
                            <br />https://www.ioss.in";
        $send_mail = $this->phpmailer->send_mail($mail_from, $mail_to, $mail_reply_to, $mail_subject, $mail_body, $mail_body, $mail_type, $smtp_data, $attachments);
        if ($send_mail['status']) {
            echo "Email send successfully.";
            $cmd = "rm $path";
            exec($cmd);
        } else {
            echo "Error sending email.";
        }
    }

    /* Backup Function Ends */

    public function calculatePoolBonusOld() {
        $date = date('Y-m-d H:i:s');
        $config_details = $this->validation_model->getConfig(['tds', 'service_charge']);
        $tds_db = $config_details["tds"];
        $service_charge_db = $config_details["service_charge"];
        $pool_bonus_status = $this->validation_model->getCompensationConfig('pool_bonus');
        $rank_status = $this->MODULE_STATUS['rank_status'];
        if ($pool_bonus_status == 'yes' && $rank_status == 'yes') {
            $pool_bonus_percent = $this->validation_model->getConfig('pool_bonus_percent');
            if ($pool_bonus_percent > 0) {
                $start_date = date('Y-m-d 00:00:00', strtotime("-6 month"));
                $end_date = date('Y-m-d 23:59:59', strtotime("-1 day"));
                $company_income = $this->getCompanyIncome($start_date, $end_date);
                if ($company_income > 0) {
                    $pool_amount = $company_income * ($pool_bonus_percent / 100);
                    $pool_bonus_config = $this->configuration_model->getPoolBonusConfig();
                    $pool_bonus_levels = count($pool_bonus_config);
                    $highest_ranks = $this->getHighestRanksForPoolBonus($pool_bonus_levels);
                    foreach ($highest_ranks as $level => $rank_id) {
                        $users = $this->getUsersByRankId($rank_id);
                        $user_count = count($users);
                        if ($user_count > 0) {
                            $level_percent = $pool_bonus_config[$level];
                            $level_amount = $pool_amount * ($level_percent / 100);
                            $distributed_amount = $level_amount / $user_count;
                            $tds = ($distributed_amount * $tds_db) / 100;
                            $service_charge = ($distributed_amount * $service_charge_db) / 100;
                            $amount_payable = $distributed_amount - ($tds + $service_charge);
                            foreach ($users as $u) {
                                $this->insertInToLegAmount($u['id'], $distributed_amount, $amount_payable, $tds, $service_charge, $date, $level, 'pool_bonus');
                            }
                        }
                    }
                }
            }
        }
        return TRUE;
    }

    public function getCompanyIncome($start_date, $end_date ,$criteria = null) {
        $company_income = 0;
        if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
            $this->db->select('order_id');
            $this->db->where('status', 'pending');
            $query1 = $this->db->get('oc_temp_registration');
            $pending_orders = [];
            foreach ($query1->result_array() as $row) {
                $pending_orders[] = $row['order_id'];
            }

            if($criteria){
                $this->db->select_sum('o.pair_value * o.quantity as total');
                $this->db->from('oc_order as o');
                $this->db->join('oc_order_product op','o.order_id = op.order_id');
                $this->db->where("o.date_added BETWEEN '{$start_date}' AND '{$end_date}'");
                if (count($pending_orders)) {
                    $this->db->where_not_in('o.order_id', $pending_orders);
                }
                $query2 = $this->db->get();
            }else{
                $this->db->select_sum('total');
                $this->db->where("date_added BETWEEN '{$start_date}' AND '{$end_date}'");
                if (count($pending_orders)) {
                    $this->db->where_not_in('order_id', $pending_orders);
                }
                $query2 = $this->db->get('oc_order');
            }
            $company_income += $query2->row_array()['total'];
        } else {
            if($criteria){
                $this->db->select_sum('product_pv as total_amount');
            }else{
                $this->db->select_sum('total_amount');
            }
            $this->db->where("reg_date BETWEEN '{$start_date}' AND '{$end_date}'");
            $query1 = $this->db->get('infinite_user_registration_details');
            $company_income += $query1->row_array()['total_amount'];

            if ($this->MODULE_STATUS['repurchase_status'] == 'yes') {
                if($criteria){
                    $this->db->select_sum('total_pv as total_amount');
                }else{
                    $this->db->select_sum('total_amount');
                }
                $this->db->where("order_date BETWEEN '{$start_date}' AND '{$end_date}'");
                $this->db->where('order_status', 'confirmed');
                $query2 = $this->db->get('repurchase_order');
                $company_income += $query2->row_array()['total_amount'];
            }
            if ($this->MODULE_STATUS['subscription_status'] == 'yes') {
                if($criteria){
                    $this->db->select_sum('product_pv as total_amount');
                }else{
                    $this->db->select_sum('total_amount');
                }
                $this->db->where("date_submitted BETWEEN '{$start_date}' AND '{$end_date}'");
                $query3 = $this->db->get('package_validity_extend_history');
                $company_income += $query3->row_array()['total_amount'];
            }
            if ($this->MODULE_STATUS['package_upgrade'] == 'yes') {
                if($criteria){
                    $this->db->select_sum('total_pv as amount');
                }else{
                    $this->db->select_sum('amount');
                }
                $this->db->where("date_added BETWEEN '{$start_date}' AND '{$end_date}'");
                $query4 = $this->db->get('upgrade_sales_order');
                $company_income += $query4->row_array()['amount'];
            }
            $company_income += $this->db->select_sum('trans_fee')->where('amount_type', 'user_credit')
                ->get('fund_transfer_details')->row_array()['trans_fee'] ?? 0;

            $company_income += $this->db->select_sum('payout_fee')->get('amount_paid')->row_array()['payout_fee'] ?? 0;

            $this->load->model('ewallet_model');
            $enabled_bonus_list = $this->ewallet_model->getEnabledBonuses();
            if($enabled_bonus_list && count($enabled_bonus_list)) {
                $company_income += $this->db->select_sum("service_charge")->where_in('amount_type', $enabled_bonus_list)->get('leg_amount')->row_array()['service_charge'] ?? 0;
            }
        }

        return $company_income;
    }

    /* public function getHighestRanksForPoolBonus($pool_bonus_levels) {
        $rank_ids = [];
        $level = 1;
        $this->db->select('rank_id');
        $this->db->where('rank_status', 'active');
        $this->db->order_by('rank_id', 'DESC');
        $this->db->limit($pool_bonus_levels);
        $query = $this->db->get('rank_details');
        foreach ($query->result_array() as $row) {
            $rank_ids[$level] = $row['rank_id'];
            $level++;
        }
        return $rank_ids;
    } */

    public function getHighestRanksForPoolBonus($pool_bonus_levels) {
        $rank_ids = [];
        $level = 1;
        $this->db->select('user_rank_id');
        $this->db->group_by('user_rank_id');
        $this->db->order_by('user_rank_id', 'DESC');
        $this->db->limit($pool_bonus_levels);
        $query = $this->db->get('ft_individual');
        foreach ($query->result_array() as $row) {
            if (!empty($row['user_rank_id'])) {
                $rank_ids[$level] = $row['user_rank_id'];
                $level++;
            }
        }
        return $rank_ids;
    }

    public function getUsersByRankId($rank_id) {
        $this->db->select('id');
        $this->db->where('user_rank_id', $rank_id);
        $this->db->where('active', 'yes');
        $query = $this->db->get('ft_individual');
        return $query->result_array();
    }

    public function autoShipReactivation() {

        $flag = false;
        $this->load->model('member_model');
        $this->load->model('product_model');
        $this->load->model('repurchase_model');


        $expired_users = $this->getPackageExpiredUsers($this->ADMIN_USER_ID, '');
        foreach ($expired_users as $row) {
            $purchase['user_id'] = $ewallet_user_id = $row["user_id"];
            $purchase['total_amount'] = $total_amount = $this->product_model->getProduct($row['product_id']);
            $package_details[0]['id'] = $row['product_id'];
            $user_available = $this->validation_model->isUserAvailable($ewallet_user_id);
            if ($user_available) {
                $ewallet_balance_amount = $this->register_model->getBalanceAmount($ewallet_user_id);
                if ($ewallet_balance_amount >= $total_amount) {
                    $this->repurchase_model->begin();
                    $purchase['by_using'] = 'ewallet';
                    $transaction_id = $this->repurchase_model->getUniqueTransactionId();
                    $res1 = $this->register_model->insertUsedEwallet($ewallet_user_id, $ewallet_user_id, $total_amount, $transaction_id, false, "package_validity", "cron");
                    if ($res1) {
                        $res2 = $this->register_model->deductFromBalanceAmount($ewallet_user_id, $total_amount);
                        if ($res2) {
                            $invoice_no = $this->member_model->packageValidityUpgrade($package_details, $purchase, FALSE, "cron");
                            $data = serialize($purchase);
                            $this->validation_model->insertUserActivity('', 'Membership Reactivation of ' . $row['user_name'] . ' through ' . lang('ewallet'), $ewallet_user_id, $data);
                            if ($invoice_no) {
                                $this->repurchase_model->commit();
                                $flag = true;
                            } else {
                                $this->repurchase_model->rollback();
                            }
                        }
                    }
                }
            }
        }
        return $flag;
    }

    public function getPackageExpiredUsers($admin_id, $user_id, $page = '', $limit = '') {
        $user_details = array();
        $today = date("Y-m-d h:i:s");

        $this->db->select('id,user_name,product_validity,sponsor_id,ft.active,p.active');
        $this->db->select('ft.product_id');
        $this->db->from('ft_individual ft');
        $this->db->join("package as p", "p.prod_id = ft.product_id", "LEFT");
        $this->db->where("product_validity <", $today);
        $this->db->where('id !=', $admin_id);
        $this->db->where('ft.product_id !=', '');
        $this->db->where('p.active !=', 'deleted');
        if ($user_id) {
            $this->db->where('id', $user_id);
        }
        if ($limit) {
            $this->db->limit($limit, $page);
        }

        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $user_details[$i]['user_id'] = $row['id'];

            $id_encode = $this->encryption->encrypt($row['user_name']);
            $id_encode = str_replace("/", "_", $id_encode);
            $encrypt_id = urlencode($id_encode);

            $user_details[$i]['user_name'] = $row['user_name'];
            $user_details[$i]['product_validity'] = $row['product_validity'];
            $user_details[$i]['sponsor_name'] = $this->validation_model->IdToUserName($row['sponsor_id']);
            $user_details[$i]['encrypt_id'] = $encrypt_id;
            $user_details[$i]['product_id'] = $row['product_id'];
            $user_details[$i]['user_img'] = $this->validation_model->getUserImage($row['id']);
            $i++;
        }
        return $user_details;
    }
    public function calculatePoolBonus() {
        $date = date('Y-m-d H:i:s');
        $config_details = $this->validation_model->getConfig(['tds', 'service_charge']);
        $tds_db = $config_details["tds"];
        $service_charge_db = $config_details["service_charge"];
        $pool_bonus_status = $this->validation_model->getCompensationConfig('pool_bonus');
        $rank_status = $this->MODULE_STATUS['rank_status'];
        if ($pool_bonus_status == 'yes' && $rank_status == 'yes') {
            $pool_bonus_percent = $this->validation_model->getConfig('pool_bonus_percent');
            if ($pool_bonus_percent > 0) {
                $pool_period = $this->validation_model->getConfig('pool_bonus_period');
                $pool_criteria = $this->validation_model->getConfig('pool_bonus_criteria');
                if($pool_period == 'monthly'){
                    $start_date = date('Y-m-d 00:00:00', strtotime("-1 month"));
                }elseif($pool_period == 'quarterly'){
                    $start_date = date('Y-m-d 00:00:00', strtotime("-3 month"));
                }elseif($pool_period == 'half_yearly'){ 
                    $start_date = date('Y-m-d 00:00:00', strtotime("-6 month"));
                }elseif($pool_period == 'yearly'){
                    $start_date = date('Y-m-d 00:00:00', strtotime("-12 month"));
                }
                $end_date = date('Y-m-d 23:59:59', strtotime("-1 day"));
                // if ($pool_criteria == 'sales'){
                //     $company_income = $this->getCompanyIncome($start_date, $end_date);
                // }else{
                //     $company_income = $this->getCompanyIncome($start_date, $end_date,true);
                // }
                $company_income = $this->getPoolIncome();

                if ($company_income > 0) {
                    //$pool_amount = $company_income * ($pool_bonus_percent / 100);
                    $pool_amount = $company_income;
                    $highest_ranks = $this->getRanksForPoolBonus();
                    foreach ($highest_ranks as $level => $rank_id) {
                        $users = $this->getUsersByRankId($rank_id);
                        $pool_perc = $this->getPoolByRankId($rank_id);
                        $user_count = count($users);
                        if ($user_count > 0 && $pool_perc) {
                            $level_amount = $pool_amount * ($pool_perc / 100);
                            $distributed_amount = $level_amount / $user_count;
                            $tds = ($distributed_amount * $tds_db) / 100;
                            $service_charge = ($distributed_amount * $service_charge_db) / 100;
                            $amount_payable = $distributed_amount - ($tds + $service_charge);
                            foreach ($users as $u) {
                                $this->insertInToLegAmount($u['id'], $distributed_amount, $amount_payable, $tds, $service_charge, $date, $level, 'pool_bonus');
                            }
                        }
                    }
                }
                $this->db->set('date',date('Y-m-d h:i:s'));
                $this->db->set('status','yes');
                $this->db->where('status','no');
                $this->db->update('pool_income');
            }
        }
        return TRUE;
    }
    public function getRanksForPoolBonus() {
        $rank_ids = [];
        $level = 1;
        $this->db->select('user_rank_id');
        $this->db->group_by('user_rank_id');
        $this->db->order_by('user_rank_id', 'DESC');
        $query = $this->db->get('ft_individual');
        foreach ($query->result_array() as $row) {
            if (!empty($row['user_rank_id'])) {
                $rank_ids[$level] = $row['user_rank_id'];
                $level++;
            }
        }
        return $rank_ids;
    }
    public function getPoolByRankId($rank_id) {
        $pool = 0;
        $this->db->select('pool_bonus_perc');
        $this->db->where('pool_status','yes');
        $this->db->where('rank_id',$rank_id);
        $query = $this->db->get('rank_details');
        foreach ($query->result_array() as $row) {
            if (!empty($row['pool_bonus_perc'])) {
                $pool = $row['pool_bonus_perc'];
            }
        }
        return $pool;
    }

    public function calculateRank() {

        $this->load->model('rank_model');
        $rank_status = $this->MODULE_STATUS['rank_status'];
        if ($rank_status == 'yes') {
            $all_users = $this->getAllUsersDetails();
            $user_count = count($all_users);
            if ($user_count > 0) {
                $rank_configuration = $this->configuration_model->getRankConfiguration();
                $rank_commission_status = $this->validation_model->getCompensationConfig(['rank_commission_status']);
                foreach ($all_users as $user) {
                    $user_id = $user['id'];
                    $personal_pv = $user['personal_pv'];
                    $group_pv = $user['gpv'];
                    $old_rank = $user['user_rank_id'];
                    $referal_count  = $this->validation_model->getReferalCount($user_id);
                    if($rank_configuration['joinee_package']) {
                        $new_rank = $this->rank_model->checkNewRank(0, 0, 0, $user_id, $old_rank);
                    } else {
                        $new_rank = $this->rank_model->checkNewRank($referal_count, $personal_pv, $group_pv, $user_id, $old_rank);
                    }
                    if ($new_rank != $old_rank) {
                        $this->rank_model->updateUserRank($user_id, $new_rank);
                        if($rank_commission_status == 'yes') {
                            $this->rank_model->rankBonus($new_rank, $user_id, $user_id);
                        }
                    }
                }
            }
        return TRUE;
        }
    }

    public function getAllUsersDetails() {
        $detail = array();
        $this->db->select('id,personal_pv,gpv,user_rank_id,promo_rank');
        $this->db->from('ft_individual');
        $this->db->order_by('id', 'DESC');
        $res = $this->db->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $detail[$i]['id'] = $row->id;
            $detail[$i]['personal_pv'] = $row->personal_pv;
            $detail[$i]['gpv'] = $row->gpv;
            $detail[$i]['user_rank_id'] = $row->user_rank_id;
            $detail[$i]['promo_rank'] = $row->promo_rank;
            $i++;
        }
        return $detail;
    }

    function isCronCalculated($start_date, $end_date, $period, $type) {
        $status = TRUE;
        $this->db->select('cron_start_time');
        $this->db->where('cron_name', $type);
        if($period = 'daily') {
            $this->db->where("cron_start_time >=",$start_date);
            $this->db->where("cron_start_time <=",$end_date);
        } else {
            $this->db->where("cron_start_time BETWEEN '{$start_date}' AND '{$end_date}'");
        }
        $this->db->order_by('cron_id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('cron_history');
        if ($query->num_rows() > 0) {
            $status = FAlSE;
        }
        return $status;
    }

    public function calculateBinaryCommission() {

        $this->load->model('configuration_model');
        $this->load->model('binary_model');

        $binary_config = $this->configuration_model->getBinaryBonusConfig();

        if ($binary_config['calculation_period'] == "monthly") {
            if ($binary_config['calculation_period'] == 'instant') {
                $from_date = date('Y-m-1');
                $to_date = date('Y-m-t');
            } else {
                $from_date = date('Y-m-d', strtotime('first day of last month'));
                $to_date = date('Y-m-d', strtotime('last day of last month'));
            }
        } elseif ($binary_config['calculation_period'] == "weekly") {
            $week_arr = $this->getWeekDateRange('sunday');
            $from_date = $week_arr["start"];
            $to_date = $week_arr["end"];
            if ($binary_config['calculation_period'] != 'instant') {
                $from_date = date("Y-m-d", strtotime("$from_date - 7 days"));
                $to_date = date("Y-m-d", strtotime("$to_date - 7 days"));
            }
        } elseif ($binary_config['calculation_period'] == "daily") {
            $from_date = date("Y-m-d");
            $to_date = date("Y-m-d");
            if ($binary_config['calculation_period'] != 'instant') {
                $from_date = date('Y-m-d', strtotime("last day"));
                $to_date = date('Y-m-d', strtotime("last day"));
            }
        } else {
            $from_date = "";
            $to_date = "";
        }
        if ($from_date) {
            $from_date = date('Y-m-d 00:00:00', strtotime("$from_date"));
        }
        if ($to_date) {
            $to_date = date('Y-m-d 23:59:59', strtotime("$to_date"));
        }

        $binary_users = $this->getAllUsersForBinary();
        $user_count = count($binary_users);

        if ($user_count > 0) {
            $this->binary_model->setBinaryCommission($binary_users, $binary_config, "leg", "leg");
        }   
        return TRUE;
    }

    public function getAllUsersForBinary() {
        $detail = array();
        $this->db->select('id,father_id,total_left_carry,total_right_carry,product_id,active');
        $this->db->from('ft_individual');
        $res = $this->db->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $detail[$i]['id'] = $row->id;
            $detail[$i]['left_carry'] = $row->total_left_carry;
            $detail[$i]['right_carry'] = $row->total_right_carry;
            $detail[$i]['product_id'] = $row->product_id;
            $detail[$i]['active'] = $row->active;
            $i++;
        }
        return $detail;
    }

    public function getWeekDateRange($start_day)
    {
        $start = strtotime("last $start_day");
        $start = date('w', $start) == date('w') ? $start + 7 * 86400 : $start;

        $end = strtotime(date("Y-m-d", $start) . " +6 days");

        $this_week_sd = date("Y-m-d", $start);
        $this_week_ed = date("Y-m-d", $end);

        return [
            'start' => $this_week_sd,
            'end' => $this_week_ed
        ];
    }

    public function DistributeAgentWallet($value='')
    {
        $date = date('Y-m-d H:i:s');
        // $config_details = $this->validation_model->getConfig(['tds', 'service_charge']);
        // $tds_db = $config_details["tds"];
        // $service_charge_db = $config_details["service_charge"];
        $get_agents = $this->member_model->getAgentDetails();
        foreach ($get_agents as $u) {
            $amount_details = $this->GetAgentWalletAmount($u['user_id']);

            // dd($amount_details);
            // $this->insertInToLegAmount($u['user_id'], $distributed_amount, $amount_payable, $tds, $service_charge, $date, $level, 'pool_bonus');
            $tds = 0;
            $service_charge = 0;
            foreach ($amount_details as $details) {

                $poduct_details = $this->getProductDetails($details['product_id']);
                $products_id = $poduct_details['product_id'];
                $product_value = $details['amount']; 
                $res = $this->insertInToLegAmount($u['user_id'], $details['wallet_amount'], $details['wallet_amount'], $tds, $service_charge, $date, 0, 'agent_wallet', $details['from_id'], $products_id, 0, $product_value, 0);

            }
        }
        return TRUE;
    }
    
    public function GetAgentWalletAmount($user_id='')
    {

        $details=[];
        $this->db->select('user_id,from_id,wallet_amount,product_id,amount');
        $this->db->join('ft_individual ft','ft.id = cwallet_history.user_id');
        $this->db->where('user_id', $user_id);
        // $this->db->where('user_id', $user_id);
        $this->db->where('wallet_type', 'agent');
        $query = $this->db->get('cwallet_history');
        $i=0;
        foreach ($query->result_array() as $row) {
            // dd($row);
            if (!empty($row)) {
                $details[$i]['user_id'] = $row['user_id'];
                $details[$i]['from_id'] = $row['from_id'];
                $details[$i]['product_id'] = $row['product_id'];
                $details[$i]['amount'] = $row['amount'];
                $details[$i]['wallet_amount'] = $row['wallet_amount'];
                $i++;
            }
        }
        return $details;

    }

    /**
     * Add reserved wallet amount and distribute remaining amount to downlines
     * 29-06-2021
     * Renshif
     */
    public function calculateReserveAndDistributionAmount() {
        $this->load->model('configuration_model');
        $this->load->model('product_model');
        $this->load->model('rwallet_model');
        $this->load->model('dwallet_model');
        $this->load->model('binary_model');

        $days_count = date('t', strtotime("previous month"));
        $from_date = date('Y-m-d', strtotime("first day of this month")) . " 00:00:00";
        $to_date = date('Y-m-d', strtotime("last day of this month")) . " 23:59:59";

        $binary_config = $this->configuration_model->getBinaryBonusConfig();
        $ceiling_limit = $binary_config['flush_out_limit'] * $days_count;
        $pair_commission_type = $binary_config['commission_type'];

        $binary_users = $this->getAllUsersForBinary();
        foreach ($binary_users as $user) {
            $user_id = $user['id'];
            $product_id = $user['product_id'];
            $pair_price = $this->product_model->getPackagePairPrice($product_id, $this->MODULE_STATUS, 'registration');
            $active = $user['active'];
            if ($active == 'yes') {
                $commission_earned = $this->getEarnedBinaryCommission($user_id, $from_date, $to_date);
                $user_balances = $this->getWalletBalances($user_id);
                if ($user_balances['rwallet_balance'] > 0) {
                    if ($pair_commission_type == 'percentage') {
                        $ceiling_amount = $ceiling_limit * ($pair_price / 100);
                    } else {
                        $ceiling_amount = $ceiling_limit * $pair_price;
                    }

                    if ($commission_earned <= $ceiling_amount) {
                        $rwallet_amount = $ceiling_amount - $commission_earned;
                        if ($rwallet_amount > $user_balances['rwallet_balance']) {
                            $rwallet_amount = $user_balances['rwallet_balance'];
                        }

                        $this->rwallet_model->deductFromRwallet($user_id, $rwallet_amount, 'reserve_debit');

                        if ($commission_earned == $ceiling_amount) {
                            $this->addToSummary($user_id);
                        }
                    }
                }
                
                $pending_pair = $this->getUserPendingPair($user_id);
                if ($pending_pair > 0) {
                    if ($pending_pair > $ceiling_limit) {
                        $reserve_pair = $ceiling_limit;
                        $distribution_pair = $pending_pair - $ceiling_limit;
                    } else {
                        $reserve_pair = $pending_pair;
                        $distribution_pair = 0;
                    }

                    if ($pair_commission_type == 'percentage') {
                        $reserve_amount = $reserve_pair * ($pair_price / 100);
                        $distribution_amount = $distribution_pair * ($pair_price / 100);
                    } else {
                        $reserve_amount = $reserve_pair * $pair_price;
                        $distribution_amount = $distribution_pair * $pair_price;
                    }

                    $this->rwallet_model->insertInToRwallet($user_id, $reserve_amount, 'binary_reserve');
                    $this->dwallet_model->calculateDistribution($user_id, $distribution_amount, $from_date, $to_date, $binary_config);

                    $remaining_pair = $pending_pair - ($reserve_pair + $distribution_pair);
                    $this->updateUserPairFlushed($user_id, $remaining_pair);
                }
            }
        }
        return TRUE;
    }

    /**
     * Get pending pairs to match of a user
     * 29-06-2021
     * Renshif
     */
    public function getUserPendingPair($user_id) {
        $this->db->select('pair_flushed_carry_forward');
        $this->db->where('id', $user_id);
        return $this->db->get('leg_details')->row()->pair_flushed_carry_forward;
    }

    /**
     * Get already credited binary commission in previous month
     * 01-07-2021
     * Renshif
     */
    public function getEarnedBinaryCommission($user_id, $from_date, $to_date) {
        $this->db->select_sum('total_amount');
        $this->db->from('leg_amount');
        $this->db->where('user_id', $user_id);
        $this->db->where('amount_type', 'leg');
        $this->db->where('date_of_submission >=', $from_date);
        $this->db->where('date_of_submission <=', $to_date);
        $total_amount = $this->db->get()->row()->total_amount;

        return $total_amount;
    }

    /**
     * Get reserve wallet balance
     * 01-07-2021
     * Renshif
     */
    public function getWalletBalances($user_id) {
        $balance_array = [];

        $this->db->where('user_id', $user_id);
        $query = $this->db->get('user_balance_amount');

        foreach ($query->result_array() as $row) {
            $balance_array['rwallet_balance'] = $row['reserve_wallet'];
            $balance_array['dwallet_balance'] = $row['distribution_wallet'];
        }

        return $balance_array;
    }
    
    
    
    public function distributeMonthlyProfit(){
        //$top_sum_amount_payable = $this->getSumTopMonthAmountPayable();
        $top_sum_amount_payable = $this->getSumTopUserAmountPayable();
        $pool_amount = $top_sum_amount_payable*(40/100);
        //$company_amount = $top_sum_amount_payable*(20/100);
        $founder_amount = $top_sum_amount_payable*(60/100);
        $company_amount=0;
        //$this->distributeToCompany($company_amount);
        $this->distributeToFounder($founder_amount);
        $this->distributeToPool($pool_amount);
        $this->resetMonthlyAmountPayable();
        
        $founder_count = $this->founderCount();
        
        $this->insertProfitDistributionEntry($top_sum_amount_payable,$pool_amount,$company_amount,$founder_count);
        
        return TRUE;
    }
    
    function insertProfitDistributionEntry($top_sum_amount_payable,$pool_amount,$company_amount,$founder_count) {
            $this->db->set('total_profit',$top_sum_amount_payable);
            $this->db->set('pool',$pool_amount);
            $this->db->set('founder',$pool_amount);
            $this->db->set('company',$company_amount);
            $this->db->set('founder_count',$founder_count);
            $this->db->insert('profit_distribution');
    }
    
    
    
    function getSumTopMonthAmountPayable(){
        $this->db->select_sum('monthly_amount_payable');
        $this->db->from('summary_info');
        $this->db->order_by('user_id','ASC');
        $this->db->limit(3);
        $query = $this->db->get();
        $monthly_amount_payable = 0;
        foreach($query->result() as $row){
            $monthly_amount_payable = $row->monthly_amount_payable;
        }
        
        return $monthly_amount_payable;
    }
    
    function distributeToPool($amount) {
        $this->db->where('status','no');
        $count = $this->db->count_all_results('pool_income');
        
        if($count) {
            $this->db->set('income','income + '.$amount,FALSE);
            $this->db->where('status','no');
            $this->db->update('pool_income');
        } else {
            $this->db->set('income',$amount);
            $this->db->insert('pool_income');
        }
    }
    
    function getPoolIncome(){
        $income = 0;
        $this->db->select('income');
        $this->db->from('pool_income');
        $this->db->where('status','no');
        $query = $this->db->get();
        foreach($query->result() as $row) {
            $income = $row->income;
        }
        return $income;
    }
    
    function distributeToCompany($amount){
        $this->db->select('id');
        $this->db->from('ft_individual');
        $this->db->where('active','yes');
        $this->db->order_by('id','ASC');
        $this->db->limit(3);
        $query = $this->db->get();
        $users = $query->result_array();
        $distributed_amount = $amount/3;
        
        $date = date('Y-m-d H:i:s');
        $config_details = $this->validation_model->getConfig(['tds', 'service_charge']);
        $tds_db = $config_details["tds"];
        $service_charge_db = $config_details["service_charge"];
        
        foreach($users as $u){
            $tds = ($distributed_amount * $tds_db) / 100;
            $service_charge = ($distributed_amount * $service_charge_db) / 100;
            $amount_payable = $distributed_amount - ($tds + $service_charge);
            $this->insertInToLegAmount($u['id'], $distributed_amount, $amount_payable, $tds, $service_charge, $date, 0, 'company_bonus');
        }
        
        
    }
    
    
    function distributeToFounder($amount){
        $founder_packs=$this->getFounderPacks();
        $this->db->where_in('product_id',$founder_packs);
        $this->db->where('active','yes');
        $count = $this->db->count_all_results('ft_individual');

        $this->db->select('id');
        $this->db->from('ft_individual');
        $this->db->where('active','yes');
        $this->db->where_in('product_id',$founder_packs);
        $query = $this->db->get();
        $users = $query->result_array();
        $distributed_amount = $amount/$count;
        
        $date = date('Y-m-d H:i:s');
        $config_details = $this->validation_model->getConfig(['tds', 'service_charge']);
        $tds_db = $config_details["tds"];
        $service_charge_db = $config_details["service_charge"];
        
        foreach($users as $u){
            $tds = ($distributed_amount * $tds_db) / 100;
            $service_charge = ($distributed_amount * $service_charge_db) / 100;
            $amount_payable = $distributed_amount - ($tds + $service_charge);
            $this->insertInToLegAmount($u['id'], $distributed_amount, $amount_payable, $tds, $service_charge, $date, 0, 'founder_bonus');
        }
        
        
    }
    
    
    function founderCount() {
        $founder_packs=$this->getFounderPacks();
        //$this->db->where('product_id','pck2');
        $this->db->where_in('product_id',$founder_packs);
        $this->db->where('active','yes');
        $count = $this->db->count_all_results('ft_individual');
        return $count;
    }
    
    function resetMonthlyAmountPayable(){
        $this->db->set('monthly_amount_payable',0);
        $this->db->update('summary_info');
    }

    public function addToSummary($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->set('binary_max', 'binary_max + 1', FALSE);
        $this->db->update('summary_info');
        return;
    }
    public function calculatePendingPair(){
        $this->load->model('configuration_model');
        $this->load->model('calculation_model');
        $this->load->model('binary_model');
        $this->load->model('product_model');
        $this->load->model('rwallet_model');
        $this->load->model('dwallet_model');
        $this->load->model('binary_model');
        $binary_config = $this->configuration_model->getBinaryBonusConfig();
        $ceiling_limit = $binary_config['flush_out_limit'];
        $pair_commission_type = $binary_config['commission_type'];
        $product_status = $this->MODULE_STATUS['product_status'];
        $config_details = $this->validation_model->getConfig(['tds', 'service_charge', 'start_date']);
        $tds_db = $config_details["tds"];
        $matching_bonus_status = $this->validation_model->getCompensationConfig('matching_bonus');
        if ($matching_bonus_status == 'yes') {
            $matching_bonus_config = $this->settings_model->getMatchingBonusConfig();
        }
        $service_charge_db = $config_details["service_charge"];
        $binary_users = $this->getAllUsersForBinary();
        $amount_type='leg';
        $action='register';
         if ($binary_config['flush_out'] == 'yes') {
            if ($binary_config['calculation_period'] == 'instant') {
                $pair_ceiling_type = $binary_config['flush_out_period'];
            } else {
                $pair_ceiling_type = $binary_config['calculation_period'];
            }
        } elseif ($binary_config['calculation_period'] != 'instant') {
            $pair_ceiling_type = $binary_config['calculation_period'];
        }

        if ($pair_ceiling_type == "monthly") {
            if ($binary_config['calculation_period'] == 'instant') {
                $from_date = date('Y-m-1');
                $to_date = date('Y-m-t');
            } else {
                $from_date = date('Y-m-d', strtotime('first day of last month'));
                $to_date = date('Y-m-d', strtotime('last day of last month'));
            }
        } elseif ($pair_ceiling_type == "weekly") {
            $week_arr = $this->getWeekDateRange($week_start);
            $from_date = $week_arr["start"];
            $to_date = $week_arr["end"];
            if ($binary_config['calculation_period'] != 'instant') {
                $from_date = date("Y-m-d", strtotime("$from_date - 7 days"));
                $to_date = date("Y-m-d", strtotime("$to_date - 7 days"));
            }
        } elseif ($pair_ceiling_type == "daily") {
            $from_date = date("Y-m-d");
            $to_date = date("Y-m-d");
            if ($binary_config['calculation_period'] != 'instant') {
                $from_date = date('Y-m-d', strtotime("last day"));
                $to_date = date('Y-m-d', strtotime("last day"));
            }
        } else {
            $from_date = "";
            $to_date = "";
        }
        if ($from_date) {
            $from_date = date('Y-m-d 00:00:00', strtotime("$from_date"));
        }
        if ($to_date) {
            $to_date = date('Y-m-d 23:59:59', strtotime("$to_date"));
        }
        foreach ($binary_users as $user) {
            $user_id = $user['id'];
            $pending_pair = $this->getUserPendingPair($user_id);
            
            $product_id = $user['product_id'];
            $pair_price = $this->product_model->getPackagePairPrice($product_id, $this->MODULE_STATUS, 'registration');
            $active = $user['active'];
            if ($active == 'yes') {
                if ($pending_pair > 0) {
                    if ($pending_pair > $ceiling_limit) {
                        //$reserve_pair = $ceiling_limit;
                        $distribution_pair = $ceiling_limit;
                    } else {
                        //$reserve_pair = $pending_pair;
                        $distribution_pair = $pending_pair;
                    }
                    $week_total = $this->binary_model->getWeekTotal($user_id, $from_date, $to_date);
                    $week_added_total = $week_total + $distribution_pair;
                    if ($week_added_total > $ceiling_limit) {
                        if ($week_total >= $ceiling_limit) {
                            //$pair_flushed = $distribution_pair;
                            $distribution_pair = 0;
                        } else {
                            $pair_flushed = $distribution_pair;
                            $distribution_pair = $ceiling_limit - $week_total;
                            //$pair_flushed -= $distribution_pair;
                        }
                    }
        
                    if ($week_added_total == $ceiling_limit) {
                        $this->binary_model->addToSummary($user_id,$from_date,$to_date);
                    }
                    $pending_carry=$this->getMinCarryExistInLeg($user_id);
                    if($pending_carry<$distribution_pair){
                        $distribution_pair=$pending_carry;
                    }
                    if($distribution_pair>0 && $this->checkCarryExistInLeg($user_id,$distribution_pair)){
                        if ($pair_commission_type == 'percentage') {
                            //$reserve_amount = $reserve_pair * ($pair_price / 100);
                            $distribution_amount = $distribution_pair * ($pair_price / 100);
                        } else {
                            //$reserve_amount = $reserve_pair * $pair_price;
                            $distribution_amount = $distribution_pair * $pair_price;
                        }
                        
            
                       // $this->rwallet_model->insertInToRwallet($user_id, $reserve_amount, 'binary_reserve');
                       // $this->dwallet_model->calculateDistribution($user_id, $distribution_amount, $from_date, $to_date, $binary_config);
                       
                       $total_amount=$distribution_amount;
    
                        $tds_amount = ($total_amount * $tds_db) / 100;
    
                        $service_charge = ($total_amount * $service_charge_db) / 100;
    
                        $amount_payable = $total_amount - ($tds_amount + $service_charge);
    
                        $date_of_sub = date("Y-m-d H:i:s");
    
                        if ($product_status == 'yes') {
                            $res = $this->calculation_model->insertInToLegAmount($user_id, $total_amount, $amount_payable, $tds_amount, $service_charge, $date_of_sub, 0, $amount_type, 0, $product_id, 0, 0, 0, 0, 0, $distribution_pair);
                        } else {
                            $res = $this->calculation_model->insertInToLegAmount($user_id, $total_amount, $amount_payable, $tds_amount, $service_charge, $date_of_sub, 0, $amount_type, 0, 0, 0, 0, 0, 0, 0, $distribution_pair);
                        }
                        $from_date=date('Y-m-d 00:00:00');
                        $to_date =date('Y-m-d 23:59:59');
                        $this->binary_model->insertInToBinaryBonusHistory($user_id, $distribution_pair, 0, 0, $binary_config['calculation_period'], $from_date, $to_date);
    
                        if ($res && $matching_bonus_status == 'yes' && $amount_payable > 0) {
                            $this->calculation_model->calculateMatchingBonus($action, $user_id, $amount_payable, $date_of_sub, $tds_db, $service_charge_db, $matching_bonus_config);
                        }
                        if($distribution_pair)
                        $this->binary_model->updateFTIndividualForPairCron($user_id, $distribution_pair, $distribution_pair, $distribution_pair);
                       
                    
            
                        $remaining_pair = $pending_pair-$distribution_pair;
                        $this->updateUserPairFlushed($user_id, $remaining_pair);
                    }
                }
            }
        }
    return TRUE;
    }
    public function updateUserPairFlushed($user_id, $pair_flushed) {
        $this->db->set('pair_flushed_carry_forward', $pair_flushed);
        $this->db->where('id', $user_id);
        $result = $this->db->update('leg_details');
    }
    function checkCarryExistInLeg($user_id,$carry){
        $this->db->where('id',$user_id);
        $this->db->where('total_left_carry>=',$carry);
        $this->db->where('total_right_carry>=',$carry);
        return $this->db->count_all_results('leg_details');
    }
    function getMinCarryExistInLeg($user_id){
        $min_carry=0;
        $this->db->select('total_left_carry,total_right_carry');
        $this->db->where('id',$user_id);
        $this->db->limit(1);
        $res=$this->db->get('leg_details');
        foreach($res->result_array() as $row){
            $min_carry=min($row['total_left_carry'],$row['total_right_carry']);
        }
        return $min_carry;
    }
    
    function getUserswithoutURL(){
        $this->db->select('user_name,id');
        $this->db->from('ft_individual f');
        $this->db->join('user_details u','u.user_detail_refid = f.id','INNER');
        $this->db->group_start();
        $this->db->where('u.simply_url','');
        $this->db->or_where('u.simply_url',NULL);
        $this->db->group_end();
        $query = $this->db->get();
        return $query->result_array();
        
    }

    function updateSimplyURL($id,$url) {
        $this->db->set('simply_url',$url);
        $this->db->where('user_detail_refid',$id);
        $this->db->update('user_details');
    }
    function getSumTopUserAmountPayable(){
        $date=date('Y-m', strtotime('previous month'));
        $start_date='2021-10-01';
        $end_date='2021-12-31';
        $this->db->select_sum('amount_payable');
        $this->db->where('amount_type','leg');
        //$this->db->like('date_of_submission',$date);
        $this->db->where("date_of_submission BETWEEN '{$start_date}' AND '{$end_date}'");
        $this->db->where_in('user_id',array(32,33,34));
        $res=$this->db->get('leg_amount');
        return $res->row_array()['amount_payable']??0;
    }
    function calculateRankPromo(){
        $promo_ranks=$this->getAllActivePromo();
        $promo_users=array();
        foreach($promo_ranks as $rank){
            $this->getPromoQualifiedUsers($rank['id'],$rank['group_pv'],$rank['direct'],$rank['bonus'],$rank['voucher'],$rank['group_pv_percent']);
        }
        return TRUE;
    }
    function getAllActivePromo(){
        $this->db->select('*');
        $this->db->where('status','active');
        $this->db->order_by('id','desc');
        $res=$this->db->get('rank_promo');
        return $res->result_array();
    }
    function getPromoQualifiedUsers($rank_id,$group_pv,$direct,$bonus,$voucher,$gpv_percent){
        $date = date('Y-m-d H:i:s');
        $config_details = $this->validation_model->getConfig(['tds', 'service_charge']);
        $tds_db = $config_details["tds"];
        $service_charge_db = $config_details["service_charge"];
        $this->db->select('id,promo_rank');
        $this->db->where('promo_rank',$rank_id);
        //$this->db->where('promo_status','no');
        $this->db->where('gpv>=',$group_pv);
        $res=$this->db->get('ft_individual');
        foreach($res->result_array() as $row){
            if($this->checkSponsorCount($row['id'])>=$direct && $this->checkGroupPV($row['id'],$gpv_percent,$group_pv)>=$group_pv){
                $tds = ($bonus * $tds_db) / 100;
                $service_charge = ($bonus * $service_charge_db) / 100;
                $amount_payable = $bonus - ($tds + $service_charge);
                $this->calculation_model->insertInToLegAmount($row['id'], $bonus, $amount_payable, $tds, $service_charge, $date, 0, 'rank_promo_bonus');
                //$this->updatePromoStatus($row['id']);
                
                if($voucher){
                    $this->insertVoucherHistory($row['id'],$voucher,$rank_id);
                }
            }
        }
        return TRUE;
    }
    function checkSponsorCount($user_id){
        $this->db->where('sponsor_id',$user_id);
        return $this->db->count_all_results('ft_individual');
    }
    function insertVoucherHistory($user_id,$voucher,$rank_id){
        $this->db->set('user_id',$user_id);
        $this->db->set('voucher',$voucher);
        $this->db->set('rank',$rank_id);
        $this->db->set('date',date('Y-m-d H:i:s'));
        return $this->db->insert('voucher_history');
    }
    function updatePromoStatus($user_id){
        $this->db->set('promo_status','yes');
        $this->db->where('id',$user_id);
        return $this->db->update('ft_individual');
    }
    function checkGroupPV($user_id,$gpv_percent,$group_pv){
        $max_per_leg=($group_pv*$gpv_percent)/100;
        $allowed_gpv=0;
        $this->db->select('personal_pv,gpv');
        $this->db->where('sponsor_id',$user_id);
        $res=$this->db->get('ft_individual');
        foreach($res->result_array() as $row){
            $total_gpv=$row['personal_pv']+$row['gpv'];
            $gpv=($total_gpv>$max_per_leg)?$max_per_leg:$total_gpv;
            $allowed_gpv+=$gpv;
        }
        return $allowed_gpv;
    }
    public function calculateRankPromoNew() {

        $this->load->model('rank_model');
        $rank_status = $this->MODULE_STATUS['rank_status'];
        if ($rank_status == 'yes') {
            $all_users = $this->getAllUsersDetails();
            $user_count = count($all_users);
            if ($user_count > 0) {
                $rank_configuration = $this->getAllActivePromo();
                $rank_commission_status = $this->validation_model->getCompensationConfig(['rank_commission_status']);
                foreach ($all_users as $user) {
                    $user_id = $user['id'];
                    $personal_pv = $user['personal_pv'];
                    $group_pv = $user['gpv'];
                    $old_rank = $user['promo_rank'];
                    $referal_count  = $this->validation_model->getReferalCount($user_id);
                    $new_rank = $this->rank_model->checkNewPromoRank($referal_count, $personal_pv, $group_pv, $user_id, $old_rank);
                    if ($new_rank != $old_rank) {
                        $this->rank_model->updateUserPromoRank($user_id, $new_rank);
                        //if($rank_commission_status == 'yes') {
                            $this->rank_model->rankPromoBonus($new_rank, $user_id, $user_id);
                        //}
                    }
                }
            }
        return TRUE;
        }
    }
    function getAllRankedUsers(){
        $this->db->select('id,user_rank_id');
        $this->db->where('user_rank_id is NOT NULL', NULL, FALSE);
        $res=$this->db->get('ft_individual');
        return $res->result_array();
    }
    function updatePromoRankFromRankHistory($user_id,$rank_id){
        $this->db->select('new_rank');
        $this->db->where('user_id',$user_id);
        $this->db->where('date<=','2021-12-10 00:00:00');
        $this->db->order_by('date','desc');
        $this->db->limit(1);
        $res=$this->db->get('rank_history');
        $new_rank=$res->row_array()['new_rank']??$rank_id;
        if($new_rank>5){
            $new_rank=5;
        }
        $this->db->set('promo_rank',$new_rank);
        $this->db->where('id',$user_id);
        return $this->db->update('ft_individual');
    }
    function refundEpin(){
        $epins=$this->getAllExpiredEpins();
        $this->load->model('ewallet_model');
        foreach($epins as $epin){
            if($epin['purchase_status']=='yes'){
                $this->changePinRefundStatus($epin['pin_id'],'processing');
                $user_id=$epin['allocated_user_id'];
                $agent_id=$epin['generated_agent'];
                $amount=$epin['pin_balance_amount'];
                // if($agent_id){
                //     $balamount = $this->validation_model->getAgentWalletBalance($agent_id);
                //     $bal = round($balamount + $amount, 8);
                //     $update = $this->validation_model->updateAgentBalanceAmount($agent_id, $bal);
                //     $this->validation_model->addAgentwalletHistory($agent_id,0 , $epin['pin_id'], 'pin_purchase', $amount, 'pin_purchase_delete', 'credit', 0,'',0,0);
                // }else{
                    
                        $up_date2 = $this->ewallet_model->updateBalanceAmountDetailsTo($user_id, round($amount, 8));
                        $this->validation_model->addEwalletHistory($user_id, 0, $epin['pin_id'], 'pin_purchase', $amount, 'pin_purchase_delete', 'credit');
                   // }
                //}
                $this->changePinRefundStatus($epin['pin_id'],'yes');
            }
        }
        return TRUE;
    }
    function getAllExpiredEpins(){
        $this->db->select('pin_id,generated_user_id,pin_balance_amount,allocated_user_id,generated_agent,purchase_status');
        $this->db->where('refund_status','no');
        $this->db->where('status','yes');
        $this->db->where('user_type!=','admin');
        $this->db->where('pin_balance_amount>',0);
        $this->db->where('DATE(pin_expiry_date) <', 'DATE(NOW())', FALSE);
        $res=$this->db->get('pin_numbers');
        return $res->result_array();
    }
    function changePinRefundStatus($pin_id,$status){
        $this->db->set('refund_status',$status);
        $this->db->where('pin_id',$pin_id);
        return $this->db->update('pin_numbers');
    }
    function getFounderPacks(){
        $arr=[];
        $this->db->select('prod_id');
        $this->db->where('pck_type','founder_pack');
        $rs=$this->db->get('package');
        foreach($rs->result_array() as $row){
            $arr[]=$row['prod_id'];
        }
        return $arr;
    }



}
