<?php

class cleanup_model extends inf_model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('register_model');
        $this->load->model('registersubmit_model');
    }

    public function cleanup($demo_user_reg = true)
    {
        $this->db->query("SET FOREIGN_KEY_CHECKS=0;");

        $this->load->model('registersubmit_model');
        $dbprefix = $this->db->dbprefix;
        $ocprefix = $this->db->ocprefix;
        $MODULE_STATUS = $this->trackModule();
        $mlm_plan = $MODULE_STATUS["mlm_plan"];

        // admin data backup
        $admin_id = $this->validation_model->getAdminId();
        $admin_password = $this->validation_model->getAdminPassword();
        $admin_user_details = $this->getUserDetails($admin_id);
        $admin_user_name = $this->validation_model->getAdminUsername();
        
        if ($this->db->table_exists("repurchase_address")) {
            $admin_repurchase_address = $this->getRepurchaseAddressOnUserId($admin_id);
        }
        if($MODULE_STATUS["opencart_status"] == "yes") {
            $admin_customer_id = $this->validation_model->getOcCustomerId($admin_id);
            $admin_customer_details = $this->db->get_where("oc_customer", ["customer_id" => $admin_customer_id])->row_array();
            $admin_customer_details['email'] = $admin_user_details['user_detail_email'];
            $admin_customer_address = $this->db->where("customer_id", $admin_customer_id)
                            ->order_by("address_id", "ASC")
                            ->limit(1)
                            ->get("oc_address")
                            ->row_array();
        }
        $current_date_time = date("Y-m-d H:i:s");
        //
        $cleanupTableArray = $this->getCleanupTables();
        $clean_tables = $cleanupTableArray['clean_tables'];
        $trunkate_status = false;
        if (substr($this->db->version(), 0, 1) == "8") {
            $trunkate_status = true;
        }
        $this->begin();
        $cleanup_status = true;
        foreach ($clean_tables as $table) {
            if ($this->db->table_exists($table)) {
                if (in_array($table, ["replica_banner", "replica_content"])) {
                    $cleanup_status &= $this->db->where("user_id !=", NULL)->delete($table);
                    continue;
                }

                // add custom codes prior
                if ($trunkate_status) {
                    $cleanup_status &= $this->db->query("TRUNCATE `{$dbprefix}{$table}`");
                } else {
                    $cleanup_status &= $this->db->query("DELETE FROM `{$dbprefix}{$table}` WHERE 1");
                }
            }
        }
        // add admin user
        if ($this->db->table_exists('ft_individual')) {
            $this->setTableAutoIncrement($dbprefix, 'ft_individual', $admin_id);

            $package_id = $this->getMinPackageId($MODULE_STATUS);
            $ft_details = [
                'id' => $admin_id,
                'position' => '',
                'user_type' => "admin",
                'user_name' => $admin_user_name,
                'password' => $admin_password,
                'active' => 'yes',
                'date_of_joining' => $current_date_time,
                'product_id' => $package_id ? $package_id : ''
            ];
            $cleanup_status &= $this->db->insert('ft_individual', $ft_details);
        }
        if ($this->db->table_exists('summary_info')) {
            $summary_info_details = [
                'user_id' => $admin_id,
            ];
            $cleanup_status &= $this->db->insert('summary_info', $summary_info_details);
        }
        if ($this->db->table_exists('user_details')) {
            $admin_user_details['join_date'] = $current_date_time;
            $this->db->insert('user_details', $admin_user_details);
            $this->db->set('user_banner', 'banner-tchnoly.jpg');
            $this->db->set('user_photo ', 'nophoto.jpg');
            $cleanup_status &= $this->db->update('user_details');
        }
        if ($this->db->table_exists('treepath')) {
            $cleanup_status &= $this->db->insert('treepath', [
                "ancestor" => $admin_id,
                "descendant" => $admin_id,
            ]);
        }
        if ($this->db->table_exists('sponsor_treepath')) {
            $cleanup_status &= $this->db->insert('sponsor_treepath', [
                "ancestor" => $admin_id,
                "descendant" => $admin_id,
            ]);
        }
        if ($this->db->table_exists('user_balance_amount')) {
            $user_balance_details = [
                'user_id' => $admin_id,
                'balance_amount' => '0',
                'purchase_wallet' => '0'
            ];
            $cleanup_status &= $this->db->insert('user_balance_amount', $user_balance_details);
        }
        if ($this->db->table_exists('tran_password')) {
            $tran_password_details = [
                'user_id' => $admin_id,
                'tran_password' => password_hash('12345678', PASSWORD_DEFAULT)
            ];
            $cleanup_status &= $this->db->insert('tran_password', $tran_password_details);
        }
        if ($this->db->table_exists("repurchase_address")) {
            if (count($admin_repurchase_address)) {
                $cleanup_status &= $this->db->insert_batch("repurchase_address", $admin_repurchase_address);
            }
        }
        if ($mlm_plan == "Binary") {
            if ($this->db->table_exists('leg_details')) {
                $cleanup_status &= $this->db->insert('leg_details', [
                    'id' => $admin_id
                ]);
            }
        }
        if ($mlm_plan == "Board") {
            if ($this->db->table_exists('auto_board_1')) {
                $this->setTableAutoIncrement($dbprefix, 'auto_board_1', $admin_id);

                $auto_board_det = [
                    "user_ref_id" => $admin_id,
                    "user_name" => "STARTER$admin_user_name",
                    'position' => '',
                    "active" => 'yes',
                    "father_id" => '0',
                    "date_of_joining" => $current_date_time,
                    "user_level" => '0'
                ];
                $cleanup_status &= $this->db->insert('auto_board_1', $auto_board_det);
            }

            if ($this->db->table_exists('auto_board_2')) {
                $this->setTableAutoIncrement($dbprefix, 'auto_board_2', $admin_id);

                $auto_board_det = [
                    "user_ref_id" => $admin_id,
                    "user_name" => "VIP$admin_user_name",
                    'position' => '',
                    "active" => 'yes',
                    "father_id" => '0',
                    "date_of_joining" => $current_date_time,
                    "user_level" => '0'
                ];
                $this->db->insert('auto_board_2', $auto_board_det);
            }

            if ($this->db->table_exists('board_view')) {
                $board_view_det = [
                    "board_top_id" => $admin_id,
                    "board_table_name" => '1',
                    "board_no" => '1',
                    "board_view_status" => 'yes',
                    "board_split_status" => 'no',
                    "date_of_join" => $current_date_time
                ];
                $this->db->insert('board_view', $board_view_det);

                $board_view_det = [
                    "board_top_id" => $admin_id,
                    "board_table_name" => '2',
                    "board_no" => '1',
                    "board_view_status" => 'yes',
                    "board_split_status" => 'no',
                    "date_of_join" => $current_date_time
                ];
                $this->db->insert('board_view', $board_view_det);
            }

            if ($this->db->table_exists('board_user_detail')) {
                $board_user_details = [
                    "board_table_name" => '1',
                    "user_id" => $admin_id,
                    "board_serial_no" => '1',
                    "date_of_join" => $current_date_time
                ];
                $this->db->insert('board_user_detail', $board_user_details);

                $board_user_details = [
                    "board_table_name" => '2',
                    "user_id" => $admin_id,
                    "board_serial_no" => '1',
                    "date_of_join" => $current_date_time
                ];
                $this->db->insert('board_user_detail', $board_user_details);
            }
        }
        if ($mlm_plan == 'Stair_Step') {
            if ($this->db->table_exists('stair_step')) {
                $step_id = $this->validation_model->getStairStepMaxId();
                $this->db->set('step_id', $step_id);
                $this->db->set('breakaway_status', "no");
                $this->db->set('leader_id', 0);
                $this->db->set('user_id', $admin_id);
                $cleanup_status &= $this->db->insert('stair_step');
            }

            if ($this->db->table_exists('user_pv_details')) {
                $this->db->set('total_pv', '0');
                $this->db->set('user_id', $admin_id);
                $cleanup_status &= $this->db->insert('user_pv_details');
            }
        }
        if($MODULE_STATUS["opencart_status"] == "yes") {
            $cleanup_status &= $this->db->set("oc_customer_ref_id", $admin_customer_id)
                        ->where("id", $admin_id)
                        ->update("ft_individual");
            if($admin_customer_details && count($admin_customer_details)) {
                $cleanup_status &= $this->db->insert("oc_customer", $admin_customer_details);
            }
            if($admin_customer_address && count($admin_customer_address)) {
                $cleanup_status &= $this->db->insert("oc_address", $admin_customer_address);
            }
        }
        
        // add admin user end

        $this->db->query("SET FOREIGN_KEY_CHECKS=1;");

        if (!$cleanup_status) {
            $this->rollBack();
            return false;
        } else {
            $this->commit();
        }

        if (DEMO_STATUS == "yes" && $demo_user_reg) {
            if($MODULE_STATUS["opencart_status"] == "yes") {
                $cleanup_status = $this->registerOcDemoUser();
            } else {
                $cleanup_status = $this->registerDemoUser();
            }
        }
        return ($cleanup_status);
    }

    public function getCleanupTables()
    {
        $MODULE_STATUS = $this->trackModule();
        $mlm_plan = $MODULE_STATUS["mlm_plan"];
        // tables to be cleaned up, no residual data, admin details will be added to ft
        $clean_tables = [
            "access_keys",
            "access_limits",
            "activity_history",
            "amount_paid",
            "authorize_payment_details",
            "binary_bonus_history",
            "bitcoin_addresses",
            "bitcoin_history",
            "bitcoin_payment_details",
            "bitcoin_payment_process_details",
            "bitcoin_payout_release_error_report",
            "bitgo_payment_history",
            "bitgo_payout_release_history",
            "blockchain_history",
            "blockchain_payout_release_history",
            "board_user_detail",
            "board_view",
            "business_volume",
            "cart",
            "configuration_change_history",
            "contacts",
            "crm_followups",
            "crm_leads",
            "cron_history",
            "donation_history",
            "donation_transfer_details",
            "employee_activity",
            "employee_details",
            "epin_transfer_history",
            "ewallet_history",
            "ewallet_payment_details",
            "feedback",
            "forget_request",
            "ft_individual",
            "fund_transfer_details",
            "googleAuth_reset_table",
            "infinite_user_registration_details",
            "invite_history",
            "kyc_docs",
            "leg_amount",
            "leg_details",
            "login_employee",
            "login_user",
            "mailtoadmin",
            "mailtouser",
            "mailtouser_cumulativ",
            "mail_from_lead",
            "mail_from_lead_cumulative",
            "manual_pv_update_history",
            "mass_payout_history",
            "mlm_curl_history",
            "package_upgrade_history",
            "package_validity_extend_history",
            "party",
            "party_guest",
            "party_guest_invited",
            "party_guest_orders",
            "party_host",
            "password_reset_table",
            "payeer_api_history",
            "payeer_order_history",
            "payment_receipt",
            "payment_registration_details",
            "payout_release_requests",
            "pending_registration",
            "pin_numbers",
            "pin_purchases",
            "pin_request",
            "pin_used",
            "placement_change_history",
            "purchase_wallet_history",
            "pv_history_details",
            "rank_history",
            "rawaddr_response",
            "replica_banner",
            "replica_content",
            "repurchase_address",
            "repurchase_order",
            "repurchase_order_details",
            "roi_order",
            "sales_order",
            "sofort_payment_history",
            "sofort_payment_response",
            "sponsor_change_history",
            "sponsor_treepath",
            "squareup_payment_history",
            "squareup_payment_response",
            "stair_step",
            "stair_step_history",
            "ticket_activity",
            "ticket_attachments",
            "ticket_comments",
            "ticket_replies",
            "ticket_tickets",
            "transaction_id",
            "tran_password",
            "tran_password_reset_table",
            "treepath",
            "upgrade_pendings",
            "upgrade_sales_order",
            "user_activation_deactivation_history",
            "user_balance_amount",
            "user_deletion_history",
            "user_details",
            "user_pv_details",
            "user_upgrade_history",
            "sms_history",
            "repurchase_address",
            "dwallet_history",
            "rwallet_history",
            "cwallet_history",
            "summary_info",
            'profit_distribution',
            'pool_distribution',
            'binary_max_history',
            'agents',
            'agent_activity',
            'agent_wallet_admin',
            'agent_wallet',
            'agent_wallet_history',
        ];
        if ($mlm_plan == 'Party') {
        }
        if ($mlm_plan == 'Binary') {
        }
        if ($mlm_plan == 'Board') {
            $board_count = $this->validation_model->getConfig("width_ceiling");
            $board_table_list = [];
            $i = $board_count;
            while ($i >= 1) {
                array_push($board_table_list, "auto_board_$i");
                $i--;
            }
            $clean_tables = array_merge($clean_tables, $board_table_list);
        }
        if ($mlm_plan == 'Stair_Step') {
            //
        }
        if ($mlm_plan == 'Donation') {
        }
        if ($MODULE_STATUS['opencart_status'] == 'yes') {
            $store_table_list = [
                "oc_address",
                "oc_cart",
                "oc_coupon", 
                "oc_coupon_category", 
                "oc_coupon_history", 
                "oc_coupon_product", 
                "oc_customer",
                "oc_customer_activity",
                "oc_customer_affiliate",
                "oc_customer_approval",
                "oc_customer_history",
                "oc_customer_ip",
                "oc_customer_login",
                "oc_customer_online",
                "oc_customer_reward",
                "oc_customer_search",
                "oc_customer_transaction",
                "oc_customer_wishlist",
                "oc_order",
                "oc_order_history",
                "oc_order_option",
                "oc_order_product",
                "oc_order_recurring",
                "oc_order_shipment",
                "oc_order_total",
                "oc_order_voucher",
                "oc_paypal_order",
                "oc_paypal_order_transaction",
                "oc_reg_order_activation_history", 
                "oc_return", 
                "oc_return_history", 
                "oc_review", 
                "oc_session",
                "oc_temp_registration",
                "oc_temp_repurchase",
                "oc_voucher",
                "oc_voucher_history",
            ];
            $clean_tables = array_merge($clean_tables, $store_table_list);
        }
        //
        // tables to be reset to inf_version
        $inf_tables = [];
        //
        return compact("clean_tables", "inf_tables");
    }
    
    function getUserDetails($id)
    {
        $user_details = array();
        $this->db->select("*");
        $this->db->from('user_details');
        $this->db->where('user_detail_refid', $id);
        $res = $this->db->get();
        foreach ($res->result_array() as $row) {
            $user_details = $row;
        }
        return $user_details;
    }

    public function getADMINOCDetails()
    {
        $password_det = array();
        $this->db->from("customer");
        $this->db->where("customer_id", '1');
        $this->db->limit(1);
        $res = $this->db->get();

        foreach ($res->result_array() as $row) {
            $password_det = $row;
        }
        return $password_det;
    }

    public function getADMINOCAddressDetails()
    {
        $password_det = array();
        $this->db->from("address");
        $this->db->where("customer_id", '1');
        $this->db->limit(1);
        $res = $this->db->get();

        foreach ($res->result_array() as $row) {
            $password_det = $row;
        }
        return $password_det;
    }

    public function getStoreUserDetails()
    {
        $user_det = array();
        $this->db->select("*");
        $this->db->from("user");
        $this->db->where('username', 'admin');
        $this->db->or_where('username', 'store_admin');
        $res = $this->db->get();

        foreach ($res->result_array() as $row) {
            $user_det[] = $row;
        }
        return $user_det;
    }

    public function updateFTAdminCustomerRefID($customer_id, $user_name)
    {
        $this->db->set("oc_customer_ref_id", $customer_id);
        $this->db->where("user_name", $user_name);
        $this->db->update("ft_individual");
    }

    public function getMinProductId($opencart_status, $opencart_status_demo)
    {
        $product_id = 1;
        if ($opencart_status == 'yes' && $opencart_status_demo == 'yes') {
            $table = 'oc_product';
        } else {
            $table = 'package';
        }
        $this->db->select_min("product_id");
        // $this->db->where('active', "yes");
        $res = $this->db->get("{$table}");
        foreach ($res->result() as $row) {
            $product_id = $row->product_id;
        }
        return $product_id;
    }

    public function getMinPackageId($module_status)
    {
        if ($module_status['opencart_status'] == 'yes' && $module_status['opencart_status_demo'] == 'yes') {
            $this->db->select('package_id');
            $this->db->where('status', 1);
            $this->db->where('package_type', 'registration');
            $this->db->order_by('product_id');
            $this->db->limit(1);
            $query = $this->db->get('oc_product');
            return $query->row_array()['package_id'] ?? "";
        } else {
            $this->db->select('prod_id');
            $this->db->where('active', 'yes');
            $this->db->where('type_of_package', 'registration');
            $this->db->order_by('product_id');
            $this->db->limit(1);
            $query = $this->db->get('package');
            return $query->row_array()['prod_id'] ?? "";
        }
    }

    public function updateTranPassword($user_id, $trans_password)
    {
        $this->db->set("tran_password", $trans_password);
        $this->db->where("user_id", $user_id);
        return $this->db->update("tran_password");
    }

    public function updateFTAdminCurrentLevel($admin_id)
    {
        $this->db->set("current_level", 4);
        $this->db->where("id", $admin_id);
        $this->db->update("ft_individual");
    }

    public function cleanupTable($dbprefix, $table_name)
    {
        if ($this->db->table_exists($table_name)) {
            $this->db->empty_table($table_name);
            $this->setTableAutoIncrement($dbprefix, $table_name);
        }
    }

    public function setTableAutoIncrement($dbprefix, $table_name, $auto_increment = 1)
    {
        if(!$auto_increment) {
            $auto_increment = 1;
        }
        $this->db->query("ALTER TABLE {$dbprefix}{$table_name} AUTO_INCREMENT {$auto_increment}");
    }
    
    public function getRepurchaseAddressOnUserId($user_id)
    {
        return $this->db->select("user_id, name, address, pin, town, mobile, default_address, delete_status")
        ->where("user_id", $user_id)
        ->get("repurchase_address")->result_array();
    }
    
    public function registerDemoUser($userIndex = "", $data = []) // no-opencart demos only, $userIndex meant to handle multi user registration
    {
        $admin_id = $data["admin_id"] ?? $this->validation_model->getAdminId();
        $MODULE_STATUS = $data["module_status"] ?? $this->trackModule();
        $mlm_plan = $MODULE_STATUS["mlm_plan"];

        if(!$userIndex) {
            $temp_prefix = $this->db->dbprefix;
            $this->db->set_dbprefix('');
            $preset_demo = $this->db->get_where('inf_preset_demo_users', ['admin_id' => $admin_id])->row_array();
            $this->db->set_dbprefix($temp_prefix);
            if (!$preset_demo || !count($preset_demo)) {
                return true;
            }
        }

        $this->begin();
        $response = TRUE;
        $user_name_entry = ($userIndex)?"member{$userIndex}":$preset_demo['user_name'];
        if($this->db->where("user_name", $user_name_entry)->count_all_results("ft_individual") > 0) {
            $this->rollBack();
            return false;
        }

        // basic mandatory data for registration
        $reg_amount = $data["reg_amount"] ?? $this->register_model->getRegisterAmount();
        $product_validity = date('Y-m-d H:i:s', strtotime('+12 months'));

        if ($MODULE_STATUS['product_status'] == "yes") {
            $product_id = $data["product_id"] ?? $this->getMinProductId($MODULE_STATUS["opencart_status"], "yes");
            $this->load->model('product_model');
            $product_details = $data["product_details"] ?? $this->product_model->getProductDetails($product_id, 'yes')[0] ?? [];
            $product_amount = $product_details['product_value'];
            $product_name = $product_details['product_name'];
            $product_pv = $product_details['pair_value'];
            if($MODULE_STATUS['subscription_status'] == 'yes') {
                $product_validity = $data["product_validity"] ?? date('Y-m-d H:i:s', strtotime("+{$product_details['subscription_period']} months"));
            }
        } else {
            $product_id = 0;
            $product_amount = '0';
            $product_pv = '0';
            $product_name = '';
        }

        $total_amount = $product_amount + $reg_amount;

        $user_name_type = "static";
        $sponsor_id = $data["sponsor_id"] ?? $admin_id;
        $placement_id = $sponsor_id;

        $reg_from_tree = false;
        if ($mlm_plan == "Binary") {
            $position = $data['position'] ?? "L";
        } else {
            $position = $data['position'] ?? "1";
        }

        $fakeData = $this->createFakeData((rand(1,2) == 2)? 'Male' : 'Female');

        $first_name = $fakeData['firstname'] ?? "Firstname{$userIndex}";
        $email = $fakeData['email'] ?? "mailbox{$userIndex}@mail.com";
        $mobile = $fakeData['mobile'] ?? "9999999999";

        $pswd = "123456";
        $by_using = "free join";
        $tran_password = $preset_demo['trans_password'] ?? "12345678";

        $reg_data = [
            "sponsor_id" => $sponsor_id,
            "placement_id" => $placement_id,
            'position' => $position,
            "first_name" => $first_name,
            "email" => $email,
            "mobile" => $mobile,
            "product_id" => $product_id,
            "product_amount" => $product_amount,
            "reg_amount" => $reg_amount,
            "total_amount" => $total_amount,
            "joining_date" => $data['joining_date'] ?? date("Y-m-d H:i:s"),
            "pswd" => $pswd,
            "by_using" => $by_using,
            "reg_from_tree" => $reg_from_tree,
            "user_name_entry" => $user_name_entry,
            "user_name_type" => $user_name_type,
            "tran_password" => $tran_password,
            "product_validity" => $product_validity,
            "product_pv" => $product_pv,
            "product_name" => $product_name,
        ];

        // end basic mandatory data for registration


        // add-on mandatory data for registration
        if(!isset($data["mandatory_fields"])) {
            $fields = array_column(
                        $this->db->select("field_name")
                        ->where("delete_status", "yes")
                        ->where("status", "yes")
                        ->get("signup_fields")->result_array(),
                        "field_name"
                    );
        } else {
            $fields = $data["mandatory_fields"];
        }

        $preset_fields = [
            "last_name" => $fakeData['lastname'] ??"Lastname{$userIndex}",
            "date_of_birth" => $fakeData['dob'] ??"1980-01-01",
            "pin" => $fakeData['postcode'] ??"123456",
            "country" => $fakeData['country'] ?? 223,
            "state" => $fakeData['state'] ?? 3624,
            "land_line" => $fakeData['landline'] ?? "12345678",
            "city" => $fakeData['city'] ?? "Los Angeles",
            "adress_line1" => $fakeData['address_line1'] ?? "Address{$userIndex} Line 1",
            "adress_line2" => $fakeData['address_line2'] ?? "Address{$userIndex} Line 2",
            "gender" => $fakeData['gender'] ?? "M",
        ];

        foreach ($preset_fields as $key => $value) {
            if (in_array($key, $fields)) {
                $reg_data[$key] = $value;
            }
        }

        // end add-on mandatory data for registration

        // custom mandatory data for registration

        if(!isset($data["custom_fields"])) {
            $customFields = array_column(
                            $this->db->select('field_name')
                            ->where('status', 'yes')
                            ->where('delete_status', 'yes')
                            ->like('field_name', 'custom_')
                            ->get("signup_fields")->result_array(),
                            "field_name"
                        );
        } else {
            $customFields = $data["custom_fields"];
        }

        foreach ($customFields as $customField) {
            $reg_data[$customField] = implode("", array_map("ucfirst", explode("_", str_replace("custom_", "", $customField))));
        }

        // end custom mandatory data for registration
        $registration_result = $this->register_model->confirmRegister($reg_data, $MODULE_STATUS);
        $update_tran = $this->updateTranPassword($registration_result['id'], password_hash($tran_password, PASSWORD_DEFAULT));
        if ($registration_result['status'] && $update_tran) {
            $response &= TRUE;
        } else {
            $response &= FALSE;
        }
        
        if (!$response) {
            $this->rollBack();
        } else {
            $this->commit();
        }
        
        if($response){
            return $registration_result['id'];
        }
        return false;
    }

    public function reset_config_tables()
    {
        // prerequisite
        $MODULE_STATUS = $this->trackModule();
        $mlm_plan = $MODULE_STATUS["mlm_plan"];
        $trunkate_status = true;
        if (substr($this->db->version(), 0, 1) == "8") {
            $trunkate_status = true;
        }
        $dbprefix = $this->db->dbprefix;
        $ocprefix = $this->db->ocprefix;
        $reset_status = true;
        $this->db->query("SET FOREIGN_KEY_CHECKS=0;");
        $this->begin();

        // end prerequisite
        // reset inf refered tables

        $config_tables_inf = [ // config tables which values to be copied from inf prefix
            "configuration",
            "authorize_config",
            "bitcoin_configuration",
            "common_mail_settings",
            "letter_config",
            "mail_settings",
            "package",
            "payment_gateway_config",
            "paypal_config",
            "rank_details",
            "terms_conditions",
            "username_config",
            "pending_signup_config",
            "signup_settings",
            "common_settings",
            "blockchain_config",
            "bitgo_configuration",
            "pin_amount_details",
            "pin_config",
            "sms_config",
            "currency_details",
            "site_maintenance",
            "ticket_categories",
            "ticket_priority",
            "ticket_status",
            "replica_banner",
            "infinite_languages",
            "party_product",
            "party_product_description",
            "stair_step_config",
            "donation_rate",
            "upload_categorys",
            "tooltip_config",
            "performance_bonus",
            "fast_start_bonus",
            "pool_bonus",
            "matching_bonus",
            "sofort_configuration",
            "repurchase_category",
            "theme_setting",
            "rank_configuration",
            "signup_fields",
            "squareup_config",
            "binary_bonus_config",
            "replica_content",
            "payeer_settings",
            "subscription_config",
            "sms_types",
            "sms_contents",
            "password_policy",
            "user_dashboard_items",
            "site_information",
        ];

        foreach ($config_tables_inf as $table) {
            if ($this->db->table_exists($table)) {
                if ($trunkate_status) {
                    $reset_status &= $this->db->query("TRUNCATE `{$dbprefix}{$table}`");
                } else {
                    $reset_status &= $this->db->query("DELETE FROM `{$dbprefix}{$table}` WHERE 1");
                }
                
                $this->db->set_dbprefix("inf_");
                $table_data = $this->db->get($table)->result_array();
                if(($table == 'configuration') && ($mlm_plan != 'Donation')) {
                    unset($table_data[0]["donation_type"]);
                }
                $this->db->set_dbprefix($dbprefix);

                if (count($table_data)) {
                    $reset_status &= ($this->db->insert_batch($table, $table_data) > 0);
                }
            }
        }

        // end reset inf refered tables
        // reset changable tables

        if ($MODULE_STATUS['opencart_status'] == 'yes') {
            $reset_status &= $this->db->update("configuration", [
                "reg_amount" => 0
            ]);
        }

        $configuration = $this->db->get("configuration")->row_array();

        $level_comm_tables = ["level_commision", "matching_level_commision", "sales_level_commision"];
        $level_comm_table_rows = [];
        $level_no = 1;
        $level_percentage = $configuration['depth_ceiling'];
        while ($level_no <= $configuration['depth_ceiling']) {
            $level_comm_table_rows[] = compact("level_no", "level_percentage");
            $level_percentage--;
            $level_no++;
        }
        foreach ($level_comm_tables as $tbl) {
            if ($trunkate_status) {
                $reset_status &= $this->db->query("TRUNCATE `{$dbprefix}{$tbl}`");
            } else {
                $reset_status &= $this->db->query("DELETE FROM `{$dbprefix}{$tbl}` WHERE 1");
            }
            $reset_status &= ($this->db->insert_batch($tbl, $level_comm_table_rows) > 0);
        }

        if ($mlm_plan == 'Donation') {
            for ($i = 1; $i <= $configuration['depth_ceiling']; $i++) {
                $reset_status &= $this->db->query("
                        update {$dbprefix}level_commision 
                        SET donation_1 = `{$dbprefix}level_commision`.`id` + 1,
                        donation_2 = `{$dbprefix}level_commision`.`id` + 2,
                        donation_3 = `{$dbprefix}level_commision`.`id` + 3,
                        donation_4 = `{$dbprefix}level_commision`.`id` + 4"
                    );
            }
        }

        $arr_rank = $this->db->select("rank_id,rank_name")->get("rank_details")->result_array();
        $commission = $configuration['depth_ceiling'];
        $sales_rank_commission_rows = [];
        for ($level = 1; $level <= $configuration['depth_ceiling']; $level++) {
            foreach ($arr_rank as $rank) {
                $sales_rank_commission_rows[] = [
                    "level" => $level, "rank_id" => $rank['rank_id'], "sales" => $commission
                ];
            }
            $commission = $configuration['depth_ceiling'] - $level;
        }
        if ($trunkate_status) {
            $reset_status &= $this->db->query("TRUNCATE `{$dbprefix}sales_rank_commissions`");
        } else {
            $reset_status &= $this->db->query("DELETE FROM `{$dbprefix}sales_rank_commissions` WHERE 1");
        }
        $reset_status &= ($this->db->insert_batch("sales_rank_commissions", $sales_rank_commission_rows) > 0);

        if ($mlm_plan == 'Board' || $mlm_plan == 'Table') {
            if ($this->db->table_exists("board_configuration")) {
                $this->db->update("board_configuration", [
                    "board_width" => $configuration['width_ceiling'],
                    "board_depth" => $configuration['depth_ceiling']
                ]);
            }
        }

        if ($MODULE_STATUS["opencart_status"] == "yes") {
            $arr_pck = [];
        } else {
            $arr_pck = $this->db->select("product_name,prod_id")
                ->where("type_of_package", "registration")
                ->where("active", "yes")
                ->order_by("product_id", "ASC")
                ->get("package")->result_array();
        }
        $level_commission_reg_pck_rows = [];
        for ($level = 1; $level <= $configuration['depth_ceiling']; $level++) {
            foreach ($arr_pck as $pack) {
                $level_commission_reg_pck_rows[] = [
                    "level" => $level,
                    "pck_id" => $pack['prod_id'],
                    "cmsn_reg_pck" => $commission,
                    "cmsn_member_pck" => $commission
                ];
                $commission = $configuration['depth_ceiling'] - $level;
            }
        }
        if ($trunkate_status) {
            $reset_status &= $this->db->query("TRUNCATE `{$dbprefix}level_commission_reg_pck`");
        } else {
            $reset_status &= $this->db->query("DELETE FROM `{$dbprefix}level_commission_reg_pck` WHERE 1");
        }
        if(count($level_commission_reg_pck_rows)) {
            $reset_status &= ($this->db->insert_batch("level_commission_reg_pck", $level_commission_reg_pck_rows) > 0);
        }

        $matching_commissions_rows = [];
        $commission = $configuration['depth_ceiling'];
        for ($level = 1; $level <= $configuration['depth_ceiling']; $level++) {
            foreach ($arr_pck as $pack) {
                $matching_commissions_rows[] = [
                    "level" => $level,
                    "pck_id" => $pack['prod_id'],
                    "cmsn_member_pck" => $commission
                ];
            }
        }
        if ($trunkate_status) {
            $reset_status &= $this->db->query("TRUNCATE `{$dbprefix}matching_commissions`");
        } else {
            $reset_status &= $this->db->query("DELETE FROM `{$dbprefix}matching_commissions` WHERE 1");
        }
        if(count($matching_commissions_rows)) {
            $reset_status &= ($this->db->insert_batch("matching_commissions", $matching_commissions_rows) > 0);
        }

        $sales_commissions_rows = [];
        $commission = $configuration['depth_ceiling'];
        for ($level = 1; $level <= $configuration['depth_ceiling']; $level++) {
            foreach ($arr_pck as $pack) {
                $sales_commissions_rows[] = [
                    "level" => $level,
                    "pck_id" => $pack['prod_id'],
                    "sales" => $commission
                ];
            }
            $commission = $configuration['depth_ceiling'] - $level;
        }
        if ($trunkate_status) {
            $reset_status &= $this->db->query("TRUNCATE `{$dbprefix}sales_commissions`");
        } else {
            $reset_status &= $this->db->query("DELETE FROM `{$dbprefix}sales_commissions` WHERE 1");
        }
        if(count($sales_commissions_rows)) {
            $reset_status &= ($this->db->insert_batch("sales_commissions", $sales_commissions_rows) > 0);
        }

        $rank_commission_status = $MODULE_STATUS["rank_status"];
        $roi_commission_status = $MODULE_STATUS["roi_status"];
        $matching_bonus = $configuration["matching_bonus"];
        $pool_bonus = $configuration["matching_bonus"];
        $fast_start_bonus = $configuration["fast_start_bonus"];
        $performance_bonus = $configuration["performance_bonus"];
        $sales_commission = $MODULE_STATUS["repurchase_status"];

        $reset_status &= $this->db->update(
            "compensations",
            compact(
                "rank_commission_status",
                "roi_commission_status",
                "matching_bonus",
                "pool_bonus",
                "fast_start_bonus",
                "performance_bonus",
                "sales_commission"
            )
        );

        // end reset changable tables

        $this->db->query("SET FOREIGN_KEY_CHECKS=1;");

        if (!$reset_status) {
            $this->rollBack();
        } else {
            $this->commit();
        }
        return $reset_status;
    }

    public function registerOcDemoUser($userIndex = "", $data = []) // opencart demos only, $userIndex meant to handle multi user registration
    {
        $admin_id = $data["admin_id"] ?? $this->validation_model->getAdminId();
        $MODULE_STATUS = $data["module_status"] ?? $this->trackModule();
        $mlm_plan = $MODULE_STATUS["mlm_plan"];
        $preset_demo = [];

        if(!$userIndex) {
            $temp_prefix = $this->db->dbprefix;
            $this->db->set_dbprefix('');
            $preset_demo = $this->db->get_where('inf_preset_demo_users', ['admin_id' => $admin_id])->row_array();
            $this->db->set_dbprefix($temp_prefix);

            if (!$preset_demo || !count($preset_demo)) {
                return TRUE;
            }
        }

        $user_name = ($userIndex)? "member{$userIndex}" : $preset_demo['user_name'];
        if($this->db->where("user_name", $user_name)->count_all_results("ft_individual") > 0) {
            return false;
        }
        $reg_from_tree = false;
        $sponsor_id = $data["sponsor_id"] ?? $admin_id;
        if($mlm_plan == "Binary") {
            $position = $data["position"] ?? "L";
        } else {
            $position = $data["position"] ?? 1;
        }

        $fakeData = $this->createFakeData((rand(1,2) == 2)? 'Male' : 'Female');

        $first_name = $fakeData['firstname'] ?? "Firstname{$userIndex}";
        $last_name = $fakeData['lastname'] ?? "Lastname{$userIndex}";
        $email = $fakeData['email'] ?? "mailbox{$userIndex}@mail.com";
        if($this->db->where("email", $email)->count_all_results("oc_customer") > 0) {
            return false;
        }
        $mobile = $fakeData['mobile'] ?? "9999999999";
        $telephone = $fakeData['telephone'] ?? "1234567890";
        $adress_line1 = $fakeData['address_line1'] ?? "Address{$userIndex} Line 1";
        $adress_line2 = $fakeData['address_line2'] ?? "Address{$userIndex} Line 2";
        $date_of_birth = $fakeData['dob'] ?? "1980-01-01";
        $pin = $fakeData['postcode'] ?? "123456";
        $country = $fakeData['country'] ?? 223;
        $country_name = $fakeData['country_name'] ?? "United States";
        $state = $fakeData['state'] ?? 3624;
        $state_name = $fakeData['state_name'] ?? "California";
        $land_line = $fakeData['landline'] ?? "12345678";
        $city = $fakeData['city'] ?? "Los Angeles";
        $payment_method = "Cash On Delivery";
        $payment_code = "cod";
        $shipping_method = "Free Shipping";
        $shipping_code = "free.free";
        $gender = $fakeData['gender'] ?? "M";
        $password = "123456";
        $tran_password = $preset_demo['trans_password'] ?? "12345678";
        $product_id = $data["product_id"] ?? $this->getMinProductId($MODULE_STATUS["opencart_status"], "yes");

        
        // call to opencart api order isertion
        $fields = compact("first_name","last_name","email","mobile","adress_line1","adress_line2","date_of_birth","pin","country","country_name","state","state_name","land_line","city","product_id","payment_method","payment_code","shipping_method","shipping_code");
        $url = STORE_URL . "/index.php?route=api/mlm/insertDemoOrder&id=" . str_replace("_", "", $this->db->dbprefix);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($fields, "", "&"),
            CURLOPT_HTTPHEADER => ["content-type: application/x-www-form-urlencoded"]
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        // dd($response);
        $response_array = json_decode($response, true);
        if(!$response_array["status"]) {
            return false;
        }

        $order_info = $response_array["data"];
        // end call to opencart api order isertion

        $registered_user_id = 0;
        $pending_id = 0;

        $reg_data = [
            "by_using" => 'opencart',
            "reg_from_tree" => $reg_from_tree,
            "sponsor_id" => $sponsor_id,
            "placement_id" => $sponsor_id,
            'position' => $position,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "email" => $email,
            "mobile" => $mobile,
            "land_line" => $land_line,
            "date_of_birth" => $date_of_birth,
            "gender" => $gender,
            "address" => $adress_line1,
            "address_line2" => $adress_line2,
            "city" => $city,
            "pin" => $pin,
            "state" => $state,
            "state_name" => $state_name,
            "country" => $country,
            "country_name" => $country_name,
            "user_name_type" => "static",
            "user_name_entry" => $user_name,
            "pswd" => $password,
            "cpswd" => $password,
            "mobile_code" => '',
            "bank_name" => '',
            "bank_branch" => '',
            "bank_acc_no" => '',
            "ifsc" => '',
            "pan_no" => '',
            "joining_date" => $data['joining_date'] ?? date('Y-m-d H:i:s'),
        ];

        $reg_data['reg_amount'] = $order_info['total'];
        $reg_data["order_id"] = $order_info["order_id"];
        $reg_data['product_id'] = $order_info["products"][0]["product_id"];

        $reg_data['registration_fee'] = 0;
        $product_details = $data["product_details"] ?? $this->product_model->getProductDetails($reg_data['product_id'], 'yes')[0] ?? [];
        
        $product_name = $product_details['product_name'];
        $product_pv = $product_details['pair_value'];
        $product_amount = $product_details['product_value'];

        $reg_data['product_name'] = $product_name;
        $reg_data['product_pv'] = $product_pv;
        $reg_data['product_amount'] = $product_amount;
        $reg_data['total_amount'] = $product_amount;
        $reg_data['reg_amount'] = 0;
        $reg_data['payment_type'] = 'opencart';

        if($MODULE_STATUS['subscription_status'] == 'yes') {
            $reg_data['product_validity'] = $data["product_validity"] ?? date('Y-m-d H:i:s', strtotime("+{$product_details['subscription_period']} months"));
        } else {
            $reg_data['product_validity'] = date('Y-m-d H:i:s', strtotime('+12 months'));
        }

        // add-on mandatory data for registration
        if(!isset($data["mandatory_fields"])) {
            $fields = array_column(
                        $this->db->select("field_name")
                        ->where("delete_status", "yes")
                        ->where("status", "yes")
                        ->get("signup_fields")->result_array(),
                        "field_name"
                    );
        } else {
            $fields = $data["mandatory_fields"];
        }

        $preset_fields = [
            "last_name" => $last_name,
            "date_of_birth" => "1980-01-01",
            "pin" => "123456",
            "country" => 223,
            "state" => 3624,
            "land_line" => "12345678",
            "city" => "Los Angeles",
            "adress_line1" => "Address{$userIndex} Line 1",
            "adress_line2" => "Address{$userIndex} Line 2",
            "gender" => "M",
        ];

        foreach ($preset_fields as $key => $value) {
            if (in_array($key, $fields)) {
                $reg_data[$key] = $value;
            }
        }

        // end add-on mandatory data for registration

        // custom mandatory data for registration

        if(!isset($data["custom_fields"])) {
            $customFields = array_column(
                            $this->db->select('field_name')
                            ->where('status', 'yes')
                            ->where('delete_status', 'yes')
                            ->like('field_name', 'custom_')
                            ->get("signup_fields")->result_array(),
                            "field_name"
                        );
        } else {
            $customFields = $data["custom_fields"];
        }

        foreach ($customFields as $customField) {
            $reg_data[$customField] = implode("", array_map("ucfirst", explode("_", str_replace("custom_", "", $customField))));
        }

        // end custom mandatory data for registration
        $registration_result = $this->register_model->confirmRegister($reg_data, $MODULE_STATUS);

        if(!isset($registration_result['status']) || !$registration_result['status']) {
            return false;
        }

        $customer_data = [
            "firstname" => $first_name,
            "lastname" => $last_name,
            "email" => $email,
            "telephone" => $telephone,
            "password" => $password,
            "address_1" => $adress_line1,
            "address_2" => $adress_line2,
            "city" => $city,
            "postcode" => $pin,
            "country_id" => $country,
            "zone_id" => $state,
            "order_id" => $order_info["order_id"],
        ];

        // curl for customer registration

        $url = STORE_URL . "/index.php?route=api/mlm/insertDemoCustomer&id=" . str_replace("_", "", $this->db->dbprefix);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($customer_data, "", "&"),
            CURLOPT_HTTPHEADER => ["content-type: application/x-www-form-urlencoded"]
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        $response_array = json_decode($response, true);
        if(!$response_array["status"]) {
            return false;
        }
        $customer_id = $response_array["data"]["customer_id"];
        
        // end curl for customer registration
        $this->db->where("user_name", $user_name)->update("ft_individual", [
            "oc_customer_ref_id" => $customer_id
        ]);

        $this->db->where("customer_id", $customer_id)->update("oc_customer", [
            "customer_type" => "mlm"
        ]);

        return $registration_result["id"];
    }

    public function multi_user_registration($count)
    {
        $count = $count * 1;
        if(!$count) {
            return false;
        }
        $MODULE_STATUS = $this->trackModule();
        
        $array1 = $this->db->select("field_name")
            ->from("signup_fields")
            ->where("delete_status", "yes")
            ->where("status", "yes")
            ->get()->result_array();
        $mandatory_fields = array_column($array1, "field_name");

        $array2 = $this->db->select('field_name')
            ->from('signup_fields')
            ->where('status', 'yes')
            ->where('delete_status', 'yes')
            ->like('field_name', 'custom_')
            ->get()->result_array();
        $customFields = array_column($array2, "field_name");
        if($MODULE_STATUS['product_status'] == "yes") {
            $products = $this->product_model->getAllProducts("yes", "registration");
        }

        $sponsor_ids_where = ["delete_status" => "active", "active" => "yes"];
        $sponsor_ids = array_column(
            $this->db->select("id")->where($sponsor_ids_where)->get("ft_individual")->result_array(),
            "id"
        );


        $data = [
            "admin_id" => $this->validation_model->getAdminId(),
            "module_status" => $MODULE_STATUS,
            "reg_amount" => $this->register_model->getRegisterAmount(),
            "custom_fields" => $customFields,
            "mandatory_fields" => $mandatory_fields,
            "joining_date" => date("Y-m-d H:i:s"),
        ];
        switch($MODULE_STATUS['mlm_plan']) {
            case "Binary": 
                $width = 2;
            break;
            case "Matrix":
                $width = $this->validation_model->getConfig("width_ceiling");
            break;
            default:
                $width = -1;
        }
        for ($i = 1; $i <= $count; $i++) {
            if($MODULE_STATUS['product_status'] == "yes") {
                $product_details = $products[rand(0, count($products) - 1)];
                $data["product_details"] = $product_details;
                $data["product_id"] = $product_details["product_id"];
                $data["subscription_period"] = date('Y-m-d H:i:s', strtotime("+12 months"));
                if($MODULE_STATUS['subscription_status'] == 'yes') {
                    $data["subscription_period"] = date('Y-m-d H:i:s', strtotime("+{$product_details['subscription_period']} months"));
                }
            }
            if($width <= 0) {
                $sponsor_id = $sponsor_ids[rand(0, count($sponsor_ids) - 1)];
                $position = $i % 5 + 1;
            }  elseif($MODULE_STATUS['mlm_plan'] == "Binary") {
                $sponsor_id = $sponsor_ids[ceil($i/$width) - 1];
                $position = $i % $width + 1;
                if($position <= 1) {
                    $position = "L";
                } else {
                    $position = "R";
                }
            } else {
                $sponsor_id = $sponsor_ids[ceil($i/$width) - 1];
                $position = $i % $width + 1;
            }
            $data["sponsor_id"] = $sponsor_id;
            $data['position'] = $position;
            if($MODULE_STATUS["opencart_status"] != "yes") {
                $user_id = $this->registerDemoUser($i, $data);
            } else {
                $user_id = $this->registerOcDemoUser($i, $data);
            }

            if(!$user_id || $user_id < 1) {
                $count++;
            } else {
                $sponsor_ids[] = $user_id;
            }
        }
        return true;
    }

    public function opencart_migration($old_oc_tables_db_prefix = '')
    {
        // need sql root user ; oc tables are transferred from $new_oc_tables_db_prefix to $old_oc_tables_db_prefix
        
        $new_oc_tables_db_prefix = 'inf';
        $old_oc_tables_db_prefix = str_replace('_', '', $old_oc_tables_db_prefix);
        $cur_db_prefix = $this->db->dbprefix;
        $this->db->set_dbprefix($old_oc_tables_db_prefix . '_');
        // $this->cleanup(false);
        $this->db->set_dbprefix($cur_db_prefix);

        $dbname = $this->db->database;
        $query1 = $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name LIKE '{$old_oc_tables_db_prefix}_oc_%'");
        $old_oc_tables = array_column($query1->result_array(), 'TABLE_NAME');
        if(!count($old_oc_tables)) {
            $old_oc_tables = array_column($query1->result_array(), 'table_name');
        }

        $drop_table_query = "DROP TABLE IF EXISTS ".implode(',', $old_oc_tables).";";
        $query2 = $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name LIKE '{$new_oc_tables_db_prefix}_oc_%'");

        $new_oc_tables = array_column($query2->result_array(), 'TABLE_NAME');
        if(!count($new_oc_tables)) {
            $new_oc_tables = array_column($query2->result_array(), 'table_name');
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS=0;");
        if(count($old_oc_tables)) {
            $this->db->query($drop_table_query);
        }
        foreach ($new_oc_tables as $table) {
            $new_table = str_replace($new_oc_tables_db_prefix . '_', $old_oc_tables_db_prefix . '_', $table);
            $this->db->query("DROP TABLE IF EXISTS {$new_table}");
            $this->db->query("CREATE TABLE {$new_table} LIKE {$table}");
            $this->db->query("INSERT INTO {$new_table} SELECT * FROM {$table}");
        }
        $this->db->query("SET FOREIGN_KEY_CHECKS=1;");
    }

    public function createFakeData($gender = 'Male') // Male/Female
    {
        $faker = Faker\Factory::create();
        $firstname = $faker->{"firstname$gender"};
        $lastname = $faker->lastname;
        $email = strtolower($firstname) . '.' . strtolower($lastname) . '@mail.com';
        $mobile = '9999999999';
        $landline = '8888888888';
        $dob = $faker->date('Y-m-d', '2002-01-01');
        $city = "$faker->city";
        $address_line1 = "$faker->secondaryAddress, $faker->streetAddress";
        $address_line2 = "$faker->streetName $faker->streetSuffix, $city";
        $state_name = $faker->state;
        $postcode = explode('-', $faker->postcode)[0];
        $country = 223;
        $country_name = 'United States';
        $state = $this->db->select('state_id')->where('country_id', '223')->like('state_name', $state_name)->get('infinite_states')->result_array()[0]['state_id'] ?? 3624;
        $gender = substr($gender, 0, 1);

        $array = compact('firstname', 'lastname', 'gender', 'email', 'mobile', 'dob', 'address_line1', 'address_line2', 'city', 'state', 'state_name', 'postcode', 'country', 'country_name');
        return $array;
    }

    public function deleteCustomDemo($admin_user_name)
    {
        $dbprefix = $this->db->query("SELECT `table_prefix` FROM `infinite_mlm_user_detail` WHERE `user_name` = ".$this->db->escape($admin_user_name)." AND `account_status` != 'deleted' LIMIT 1")->result_array()[0]['table_prefix'] ?? '';
        if(!$dbprefix) {
            return ;
        }

        $dbname = $this->db->database;
        $query1 = $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = '$dbname' AND table_name LIKE '{$dbprefix}%'");
        $delete_tables = array_column($query1->result_array(), 'TABLE_NAME');
        if(!count($delete_tables)) {
            $delete_tables = array_column($query1->result_array(), 'table_name');
        }
        $this->db->query("SET FOREIGN_KEY_CHECKS=0;");
        if(count($delete_tables)) {
            $drop_table_query = "DROP TABLE IF EXISTS ".implode(',', $delete_tables).";";
            $this->db->query($drop_table_query);
        }
        $this->db->query("SET FOREIGN_KEY_CHECKS=1;");

        $this->db->query("UPDATE `infinite_mlm_user_detail` SET `account_status` = 'deleted' WHERE `user_name` = ".$this->db->escape($admin_user_name));
    }
}
