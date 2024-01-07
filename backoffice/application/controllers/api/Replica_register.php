<?php

require_once 'Inf_Controller.php';

class replica_register extends Inf_Controller
{

    public function __construct()
    {
        parent::__construct();
        // $this->LOG_USER_ID = $this->rest->user_id;
        // $this->LOG_USER_TYPE = 'user';
        $this->load->model('register_model');
        $this->load->model('configuration_model');
        $this->load->model('tree_model');
        $this->load->model('ewallet_model');
        $this->load->model('validation_model');

        $this->lang->load('register_lang');
    }

    public function getUserDetails_get()
    {
        $username   = $this->input->get('username');
        $userId     = $this->validation_model->userNameToID($username);

        $userData = $this->validation_model->getUserFullName($userId);
        dd($userData);
    }

    function register_users_post()
    {
        $array = array();
        if ($this->input->post()) {
            $reg_post_array = $this->input->post(NULL, TRUE);
            $reg_post_array = $this->validation_model->stripTagsPostArray($reg_post_array);

            $table_prefix = $reg_post_array['table_prefix'];
            $admin_id = $reg_post_array['user_id'];
            $count = $this->api_register_model->validateTablePrefix($admin_id, $table_prefix);
            if ($count < 1) {
                $array["status"] = "invalid table prefix";
                $this->response($array, 200);
            }
            $this->table_prefix = $reg_post_array['table_prefix'] . '_';

            $this->inf_model->setDBPrefix($this->table_prefix);
            $this->set_module_status_array();

            $this->MLM_PLAN = $this->MODULE_STATUS['mlm_plan'];
            $this->LOG_USER_ID = $admin_id;
            $this->LOG_USER_TYPE = 'admin';
            $this->ADMIN_USER_NAME = $this->validation_model->getAdminUsername();

            $this->load->model('register_model');

            $reg_post_array["pswd"] = $reg_post_array["cpswd"] = '123456';
            $reg_post_array["first_name"] = 'Infinite';
            $reg_post_array["last_name"] = 'MLM';
            $reg_post_array["gender"] = 'M';
            $reg_post_array["date_of_birth"] = '2000-01-01 00:00:00';
            $reg_post_array["address"] = 'Your Address 1';
            $reg_post_array["address_line2"] = 'Your Address 2';
            $reg_post_array["pin"] = '1234';
            $reg_post_array["state"] = '1';
            $reg_post_array["city"] = 'Your City';
            $reg_post_array["land_line"] = '';
            $reg_post_array["mobile"] = '123456789';
            $reg_post_array["bank_name"] = '';
            $reg_post_array["bank_branch"] = '';
            $reg_post_array["bank_acc_no"] = '';
            $reg_post_array["ifsc"] = '';
            $reg_post_array["pan_no"] = '';
            $reg_post_array["sponsor_user_name"] = $this->ADMIN_USER_NAME;
            $settings = $this->validation_model->getSettings();
            $reg_post_array["depth"] = $settings['depth_ceiling'];
            $reg_post_array["width"] = $settings['width_ceiling'];
            $reg_post_array['product_id'] = 0;
            $product_id = 0;

            $payment_status = false;
            $payment_type = 'free_join';

            $module_status = $this->MODULE_STATUS;
            $product_status = $this->MODULE_STATUS['product_status'];
            $username_config = $this->configuration_model->getUsernameConfig();

            if ($this->MLM_PLAN == 'Matrix' || $this->MLM_PLAN == 'Board' || $this->MLM_PLAN == 'Table') {
                $user_count = 0;
                for ($pow = 1; $pow <= $reg_post_array['depth']; $pow++) {
                    $user_count += pow($reg_post_array['width'], $pow);
                }
            } else {
                $user_count = '25';
            }

            $j = 0;
            $regr = $reg_post_array;
            $registered_array = array();
            for ($i = 0; $i < $user_count; $i++) {

                $reg_from_tree = FALSE;

                $regr['position'] = "";
                $regr['user_name_entry'] = 'mlm_user' . (($i > 0) ? $i : '');
                $regr["email"] = $regr['user_name_entry'] . '@email.com';

                if ($this->MLM_PLAN == "Binary") {
                    $registered_array[$i]['user_name'] = $regr['user_name_entry'];
                    if ($i % 2 == 0) {
                        $regr['position'] = 'L';
                    } else {
                        $regr['position'] = 'R';
                    }

                    if ($i <= 1) {
                        $regr['sponsor_user_name'] = $regr['placement_user_name'] = $reg_post_array["sponsor_user_name"];
                    } else {
                        $j = $i - 2;
                        $j = (($j % 2) == 0) ? $j : ($j - 1);
                        $j = $j / 2;
                        $regr['sponsor_user_name'] = $regr['placement_user_name'] = $registered_array[$j]['user_name'];
                    }
                } elseif ($this->MLM_PLAN == "Unilevel" || $this->MLM_PLAN == "Party") {
                    $registered_array[$i]['user_name'] = $regr['user_name_entry'];
                    if ($i != 0 && ($i % 5) == 0) {
                        $reg_post_array["sponsor_user_name"] = $registered_array[$j]['user_name'];
                        $j++;
                    }
                    $regr['placement_user_name'] = $regr['sponsor_user_name'] = $reg_post_array["sponsor_user_name"];
                } else {
                    $regr['placement_user_name'] = $regr['sponsor_user_name'] = $reg_post_array['sponsor_user_name'];
                }

                $regr['reg_amount'] = $this->register_model->getRegisterAmount();

                $product_name = 'NA';
                $product_pv = '0';
                $product_amount = '0';
                $product_validity = 0;
                if ($product_status == "yes") {
                    $reg_post_array['product_id'] = 1;
                    $product_id = 1;
                    $this->load->model('product_model');
                    $product_details = $this->product_model->getProductDetails($product_id, 'yes');
                    $product_name = $product_details[0]['product_name'];
                    $product_pv = $product_details[0]['pair_value'];
                    $product_amount = $product_details[0]['product_value'];
                    if ($this->MODULE_STATUS['subscription_status'] == "yes") {
                        $product_validity = $this->product_model->calculateProductValidity($product_details[0]['subscription_period']);
                    }
                }
                $regr['product_validity'] = $product_validity;
                $regr['product_status'] = $product_status;
                $regr['product_id'] = $product_id;
                $regr['product_name'] = $product_name;
                $regr['product_pv'] = $product_pv;
                $regr['product_amount'] = $product_amount;
                $regr['total_amount'] = $regr['reg_amount'] + $regr['product_amount'];


                $regr['country_name'] = $this->country_state_model->getCountryNameFromID($reg_post_array['country']);
                $regr['state_name'] = $this->country_state_model->getStateNameFromId('1');

                $regr['user_name_type'] = $username_config["type"];
                $regr['joining_date'] = date('Y-m-d H:i:s');
                $regr['reg_from_tree'] = $reg_from_tree;

                $regr['sponsor_id'] = $this->validation_model->userNameToID($regr['sponsor_user_name']);
                $regr['placement_id'] = $this->validation_model->userNameToID($regr['placement_user_name']);
                $regr['product_name'] = $this->register_model->getProductName($regr['product_id']);

                $regr['payment_type'] = $payment_type;
                $regr['by_using'] = 'free join';
                $this->register_model->begin();
                $status = $this->register_model->confirmRegister($regr, $module_status);
                if ($status['status']) {
                    $payment_status = true;
                }

                if ($payment_status) {
                    $user = $status['user'];
                    $pass = $status['pwd'];
                    $encr_id = $status['id'];
                    $tran_code = $status['tran'];
                    $user_id = $this->validation_model->userNameToID($user);
                    if ($product_status == "yes") {
                        $insert_into_sales = $this->register_model->insertIntoSalesOrder($user_id, $regr['product_id'], $payment_type);
                    }
                    $this->register_model->commit();
                    $array["status"] = "success";
                } else {
                    $this->register_model->rollback();
                    $array["status"] = "error";
                }
            }
        } else {
            $array["status"] = "error";
        }
        $this->response($array, 200);
    }

    function set_module_status_array()
    {

        $this->MODULE_STATUS = $this->inf_model->trackModule();

        if ($this->MODULE_STATUS['mlm_plan'] == "Board") {
            $this->SHUFFLE_STATUS = $this->MODULE_STATUS['shuffle_status'];
        }

        if (DEMO_STATUS == 'no' && $this->MODULE_STATUS['replicated_site_status'] == "yes" && $this->MODULE_STATUS['replicated_site_status_demo'] == "yes") {
            $this->NO_LOGIN_PAGES[] = "register";
            $this->load->model('register_model', '', TRUE);
        }
    }

    function preset_register_users_post()
    {
        $array = array();
        $gender_arr = ['M', 'F'];
        $country_arr = [223, 138, 30, 193, 81, 99, 176, 13];
        $product_arr = [1, 2, 3, 4];

        if ($this->input->post()) {
            $reg_post_array = $this->input->post(NULL, TRUE);
            $reg_post_array = $this->validation_model->stripTagsPostArray($reg_post_array);

            $table_prefix = $reg_post_array['table_prefix'];
            $count = $this->api_register_model->validateTablePrefix($table_prefix);
            if ($count < 1) {
                $array["status"] = "invalid table prefix";
                $this->response($array, 200);
            }
            $this->table_prefix = $reg_post_array['table_prefix'] . '_';

            $this->inf_model->setDBPrefix($this->table_prefix);
            $this->set_module_status_array();

            $this->MLM_PLAN = $this->MODULE_STATUS['mlm_plan'];
            $this->LOG_USER_ID = $table_prefix;
            $this->LOG_USER_TYPE = 'admin';
            $this->ADMIN_USER_NAME = $this->validation_model->getAdminUsername();

            $reg_post_array["pswd"] = $reg_post_array["cpswd"] = '123456';
            $reg_post_array["first_name"] = 'Infinite';
            $reg_post_array["last_name"] = 'MLM';
            $reg_post_array["date_of_birth"] = '2000-01-01 00:00:00';
            $reg_post_array["address"] = 'Your Address 1';
            $reg_post_array["address_line2"] = 'Your Address 2';
            $reg_post_array["pin"] = '1234';
            $reg_post_array["state"] = '1';
            $reg_post_array["city"] = 'Your City';
            $reg_post_array["land_line"] = '';
            $reg_post_array["mobile"] = '9876543210';
            $reg_post_array["bank_name"] = '';
            $reg_post_array["bank_branch"] = '';
            $reg_post_array["bank_acc_no"] = '';
            $reg_post_array["ifsc"] = '';
            $reg_post_array["pan_no"] = '';
            $reg_post_array["sponsor_user_name"] = $reg_post_array["sponsor_user_name"][array_rand($reg_post_array["sponsor_user_name"])];
            $settings = $this->validation_model->getSettings();
            $reg_post_array["depth"] = $settings['depth_ceiling'];
            $reg_post_array["width"] = $settings['width_ceiling'];
            $reg_post_array['product_id'] = 0;
            $product_id = 0;

            $payment_status = false;
            $payment_type = 'free_join';

            $module_status = $this->MODULE_STATUS;
            $product_status = $this->MODULE_STATUS['product_status'];
            $username_config = $this->configuration_model->getUsernameConfig();

            if ($this->MLM_PLAN == 'Matrix' || $this->MLM_PLAN == 'Board' || $this->MLM_PLAN == 'Table') {
                $user_count = 0;
                for ($pow = 1; $pow <= $reg_post_array['depth']; $pow++) {
                    $user_count += pow($reg_post_array['width'], $pow);
                }
            } else {
                $user_count = '25';
            }

            $j = 0;
            $regr = $reg_post_array;
            $registered_array = array();
            for ($i = 0; $i < $user_count; $i++) {

                $reg_from_tree = FALSE;

                $reg_post_array['country'] = $regr['country'] = $country_arr[array_rand($country_arr)];

                $regr['position'] = "";
                $regr['user_name_entry'] = 'mlm_user' . (($i > 0) ? $i : '');
                $regr["email"] = $regr['user_name_entry'] . '@email.com';

                if ($this->MLM_PLAN == "Binary") {
                    $registered_array[$i]['user_name'] = $regr['user_name_entry'];
                    if ($i % 2 == 0) {
                        $regr['position'] = 'L';
                    } else {
                        $regr['position'] = 'R';
                    }

                    if ($i <= 1) {
                        $regr['sponsor_user_name'] = $regr['placement_user_name'] = $reg_post_array["sponsor_user_name"];
                    } else {
                        $j = $i - 2;
                        $j = (($j % 2) == 0) ? $j : ($j - 1);
                        $j = $j / 2;
                        $regr['sponsor_user_name'] = $regr['placement_user_name'] = $registered_array[$j]['user_name'];
                    }
                } elseif ($this->MLM_PLAN == "Unilevel" || $this->MLM_PLAN == "Party") {
                    $registered_array[$i]['user_name'] = $regr['user_name_entry'];
                    if ($i != 0 && ($i % 5) == 0) {
                        $reg_post_array["sponsor_user_name"] = $registered_array[$j]['user_name'];
                        $j++;
                    }
                    $regr['placement_user_name'] = $regr['sponsor_user_name'] = $reg_post_array["sponsor_user_name"];
                } else {
                    $regr['placement_user_name'] = $regr['sponsor_user_name'] = $reg_post_array['sponsor_user_name'];
                }

                $regr['reg_amount'] = $this->register_model->getRegisterAmount();

                $product_name = 'NA';
                $product_pv = '0';
                $product_amount = '0';
                $product_validity = 0;
                if ($product_status == "yes") {
                    $reg_post_array['product_id'] = $product_arr[array_rand($product_arr)];
                    $product_id = $product_arr[array_rand($product_arr)];
                    $this->load->model('product_model');
                    $product_details = $this->product_model->getProductDetails($product_id, 'yes');
                    $product_name = $product_details[0]['product_name'];
                    $product_pv = $product_details[0]['pair_value'];
                    $product_amount = $product_details[0]['product_value'];
                    if ($this->MODULE_STATUS['subscription_status'] == "yes") {
                        $product_validity = $this->product_model->calculateProductValidity($product_details[0]['subscription_period']);
                    }
                }
                $regr['product_validity'] = $product_validity;
                $regr['product_status'] = $product_status;
                $regr['product_id'] = $product_id;
                $regr['product_name'] = $product_name;
                $regr['product_pv'] = $product_pv;
                $regr['product_amount'] = $product_amount;
                $regr['total_amount'] = $regr['reg_amount'] + $regr['product_amount'];

                $reg_post_array['gender'] = $regr['gender'] = $gender_arr[array_rand($gender_arr)];

                $regr['country_name'] = $this->country_state_model->getCountryNameFromID($reg_post_array['country']);
                $regr['state_name'] = $this->country_state_model->getStateNameFromId('1');

                $regr['user_name_type'] = $username_config["type"];
                $regr['joining_date'] = date('Y-m-d H:i:s');
                $regr['reg_from_tree'] = $reg_from_tree;

                $regr['sponsor_id'] = $this->validation_model->userNameToID($regr['sponsor_user_name']);
                $regr['placement_id'] = $this->validation_model->userNameToID($regr['placement_user_name']);
                $regr['product_name'] = $this->register_model->getProductName($regr['product_id']);

                $regr['payment_type'] = $payment_type;
                $regr['by_using'] = 'free join';
                $this->register_model->begin();
                $status = $this->register_model->confirmRegister($regr, $module_status);
                if ($status['status']) {
                    $payment_status = true;
                }

                if ($payment_status) {
                    $user = $status['user'];
                    $pass = $status['pwd'];
                    $encr_id = $status['id'];
                    $tran_code = $status['tran'];
                    $user_id = $this->validation_model->userNameToID($user);
                    if ($product_status == "yes") {
                        $insert_into_sales = $this->register_model->insertIntoSalesOrder($user_id, $regr['product_id'], $payment_type);
                    }
                    $this->register_model->commit();
                    $array["status"] = "success";
                } else {
                    $this->register_model->rollback();
                    $array["status"] = "error";
                }
            }
        } else {
            $array["status"] = "error";
        }
        $this->response($array, 200);
    }
    function register_get()
    {
        if (!$this->configuration_model->isRegistrationAllowed()) {
            $this->set_error_response(422, 1057);
        }
        $this->lang->load('register_lang');
        $this->load->model('validation_model');
        $this->load->model('register_model');
        $this->load->model('product_model');
        //reegitser from tree
        $placement_user_name = '';
        $sponsor_name        = $this->get('username');
        $sponsor_id          = $this->validation_model->userNameToID($sponsor_name);
        $placement_position = $this->get('position') ?? '';
        //sponsor default field
        $sponsorField = [
            [
                'code'      => 'sponsorUserName',
                'title'     => lang('sponsor_user_name'),
                'required'  => true,
                'value'     => $this->validation_model->IdToUserName($sponsor_id),
                'isEditable' => false,
                'type'      => 'text',
                'field_name' => 'sponsor_user_name'
            ],
            [
                'code'      => 'sponsorFullName',
                'title'     => lang('sponsor_full_name'),
                'required'  => false,
                'value'     => $this->register_model->getReferralName($sponsor_id),
                'isEditable' => false,
                'type'      => 'text',
                'field_name' => 'sponsor_full_name'
            ]
        ];
        if ($placement_user_name) {
            if ($this->validate_username($placement_user_name)) {
                $sponsor_id = $this->validation_model->userNameToID($placement_user_name);
                $sponsorField[] = [
                    'code'      => 'placementUserName',
                    'title'     => lang('placement_user_name'),
                    'required'  => true,
                    'value'     => $placement_user_name,
                    'isEditable' => false,
                    'type'      => 'text',
                    'field_name' => 'placement_user_name'
                ];
                $sponsorField[] = [
                    'code'      => 'placementFullName',
                    'title'     => lang('placement_full_name'),
                    'required'  => true,
                    'value'     => $this->validation_model->getUserFullName($sponsor_id),
                    'isEditable' => false,
                    'type'      => 'text',
                    'field_name' => 'placement_full_name'
                ];
            } else {
                $this->set_error_response(422, 1009);
            }
        }
        //check the plan is binary
        if ($this->MODULE_STATUS['mlm_plan'] == 'Binary') {
            $position_options = [];
            if (!in_array($placement_position, ['L', 'R']) && $placement_position) {
                $this->set_error_response(422, 1033);
            }
            // $placement_position = 'L';
            if ($placement_position) {
                $position_options = [
                    [
                        'title' => $placement_position == 'L' ? lang('left_leg') : lang('right_leg'),
                        'code' => $placement_position == 'L' ? 'leftLeg' : 'rightLeg',
                        'value' => $placement_position == 'L' ? 'L' : 'R'
                    ]
                ];
            } else {
                $position_options = [
                    ['title' => lang('left_leg'), 'code' => 'leftLeg', 'value' => 'L'],
                    ['title' => lang('right_leg'), 'code' => 'rightLeg', 'value' => 'R'],
                ];
            }
            $placement_position = $placement_position ? $placement_position : 'L';
            $sponsorField[] =
                [
                    'code'      => 'position',
                    'title'    => lang('position'),
                    'value'    => $placement_position ?? 'L',
                    'type'     => 'select',
                    'field_name' => 'position',
                    'required' => true,
                    'options'  => $position_options
                ];
        }
        if ($this->MODULE_STATUS['product_status'] == 'yes') {
            $type_of_package = "registration";
            $product_array = $this->product_model->getAllProducts('yes', $type_of_package);
            $options = [];
            // dd($product_array);
            foreach ($product_array as $product) {
                $options[] = [
                    'code'  => $product['product_name'],
                    'value' => $product['product_id'],
                    'productValue' => $product['product_value'],
                    'title' => $product['product_name'] . " (" . format_currency($product['product_value']) . ")"
                ];
            }
            $sponsorField[] =
                [
                    'code'      => 'product',
                    'title'    => lang('product'),
                    'value'    => '',
                    'type'     => 'select',
                    'required' => true,
                    'field_name' => 'product_id',
                    'options'  => $options
                ];
        }
        $data['sponsor'] = [
            'title' => [
                'code'  => $this->MODULE_STATUS['product_status'] == 'yes' ? 'sponsorAndPackage' : 'sponsor',
                'title' => lang('sponsor_and_package_information')
            ],
            'fields' => $sponsorField
        ];


        //contact information 
        $contact_fields = $this->register_model->getContactInfoFields();
        $signup_settings = $this->configuration_model->getGeneralSignupConfig();
        $fields = [];
        $selectFields = ['country', 'gender', 'state', 'date_of_birth'];
        foreach ($contact_fields as $value) {
            // dd($value);
            if (in_array($value['key_name'], $selectFields)) {
                switch ($value['key_name']) {
                    case 'gender':
                        $fields[] = [
                            'title'     => lang('gender'),
                            'code'      => 'gender',
                            'value'     => '',
                            'type'      => 'select',
                            'field_name' => 'gender',
                            'required'  => $value['required'] == 'yes' ? true : false,
                            'options'   => [
                                ['title' => lang('male'), 'code' => 'male', 'value' => 'M'],
                                ['title' => lang('female'), 'code' => 'female', 'value' => 'F']
                            ]
                        ];
                        break;
                    case 'country':
                        $fields[] = [
                            'title' => lang('country'),
                            'code'  => 'country',
                            'value' => $signup_settings['default_country'],
                            'type'  => 'select',
                            'field_name' => 'country',
                            'required'  => $value['required'] == 'yes' ? true : false,
                            'options'   => $this->Api_model->getAllCountries()
                        ];
                        break;
                    case 'state':
                        $fields[] = [
                            'title' => lang('state'),
                            'code'  => 'state',
                            'field_name' => 'state',
                            'value' => NULL,
                            'type'  => 'select',
                            'required'  => $value['required'] == 'yes' ? true : false,
                            'options'   => $this->Api_model->getStatesFromCountry($signup_settings['default_country']) ?? []
                        ];

                        break;
                    case 'date_of_birth':
                        $fields[] = [
                            'title' => lang('date_of_birth'),
                            'code'  => 'dateOfBirth',
                            'value' => '1990-01-01',
                            'field_name' => 'date_of_birth',
                            'type'  => 'date',
                            'required' => $value['required'] == 'yes' ? true : false,
                            'validation' => [
                                'agelimit' => $signup_settings['age_limit']
                            ]
                        ];
                        break;
                    default:
                        # code...
                        break;
                }
            } else {
                $code = $value['key_name'];
                $type = "text";
                $defaultvalue = '';
                switch ($value['key_name']) {
                    case 'first_name':
                        $code = 'firstName';
                        $defaultvalue = "First Name";
                        break;
                    case 'last_name':
                        $code  = 'lastName';
                        break;
                    case 'mobile':
                        $type = ($this->IS_MOBILE) ? "number" : "tel";
                        $defaultvalue = '00918080809090';
                        break;
                    case 'email':
                        $defaultvalue = 'yourmail@email.com';
                        break;
                    case 'adress_line1':
                        $code = 'addressLine1';
                        break;
                    case 'adress_line2':
                        $code = 'addressLine2';
                        break;
                    case 'pin':
                        $code = 'pin';
                        break;
                    case 'land_line':
                        $code = 'landLine';
                        break;
                    default:
                        # code...
                        break;
                }
                $fields[] = [
                    'code'      => $code,
                    'title'     => lang($value['key_name']),
                    'required'  => $value['required'] == 'yes' ? true : false,
                    'isEditable' => true,
                    'type'      => $type,
                    'value'     => $defaultvalue,
                    'field_name' => $value['key_name'],
                ];
            }
        }
        $data['contactInfo'] = [
            'title' => [
                'code'  => 'contactInformation',
                'title' => lang('contact_info')
            ],
            'fields' => $fields
        ];

        //Login Information 
        $username_config = $this->configuration_model->getUsernameConfig();
        $user_name_type = $username_config["type"];
        $LoginFields = [];
        $usernameRange = $this->validation_model->getUsernameRange();
        if ($user_name_type != 'dynamic') {

            $LoginFields[] =
                [
                    'code'      => 'userName',
                    'title'     => lang('User_Name'),
                    'required'  => true,
                    'isEditable' => true,
                    'type'      => 'text',
                    'field_name' => 'user_name_entry',
                    'validation' => [
                        'min_length' => $usernameRange['min'],
                        'max_length' => $usernameRange['max']
                    ]
                ];
        }
        $passwordPolicy = $this->validation_model->getPasswordPolicyArray();
        foreach ($passwordPolicy as $key => $value) {
            if ($passwordPolicy[$key] == 0 && $key != 'disableHelper') {
                unset($passwordPolicy[$key]);
            }
        }
        $termsconditions = $this->register_model->getTermsConditions($this->LANG_ID);
        $LoginFields[] =
            [
                'code'      => 'password',
                'title'     => lang('password'),
                'required'  => true,
                'isEditable' => true,
                'type'      => 'password',
                'validation' => $passwordPolicy,
                'field_name' => 'pswd',
            ];
        $LoginFields[] =
            [
                'code'      => 'confirmPassword',
                'title'     => lang('confirm_password'),
                'required'  => true,
                'isEditable' => true,
                'type'      => 'password',
                'field_name' => 'cpswd',
            ];
        $LoginFields[] =
            [
                'code'      => 'agree_terms',
                'title'     => lang('I_ACCEPT_TERMS_AND_CONDITIONS'),
                'required'  => true,
                'isEditable' => true,
                'type'      => 'checkbox',
                'value'     => false,
                'content'   => $this->security->xss_clean($termsconditions),
                'field_name' => 'agree_terms',
            ];
        $data['loginInformation'] = [
            'title' => [
                'code'  => 'loginInformation',
                'title' => lang('login_information')
            ],
            'fields' => $LoginFields
        ];

        //payment method
        //check the payment methods tabs
        $PaymentMethodsStatus = false;
        $registration_fee = $this->register_model->getRegisterAmount();
        $registration_fee1 = round($registration_fee * $this->DEFAULT_CURRENCY_VALUE, 8);
        $PaymentMethods = [];
        if ($registration_fee || $this->MODULE_STATUS['product_status'] == 'yes') {
            $PaymentMethodsStatus = true;
            $payment_gateway_array = $this->register_model->getPaymentGatewayStatus("registration");
            foreach ($payment_gateway_array as $key => $value) {
                if ($value == 'yes') {
                    $icon = '';
                    switch ($key) {
                        case 'paypal_status':
                            $icon = "fa fa-paypal";
                            break;
                        case 'authorize_status':
                            $icon = "fa fa-lock";
                            break;
                        case 'bitcoin_status':
                            $icon = "fa fa-btc";
                            break;
                        case 'blockchain_status':
                            $icon = "fa fa-asterisk";
                            break;
                        case 'bitgo_status':
                            $icon = "fa fa-btc";
                            break;
                        case 'payeer_status':
                            $icon = "fa fa-product-hunt";
                            break;
                        case 'sofort_status':
                            $icon = "fa fa-euro";
                            break;
                        case 'squareup_status':
                            $icon = "fa fa-square";
                            break;
                        case 'epin_status':
                            $icon = "fa fa-window-restore";
                            break;
                        case 'ewallet_status':
                            $icon = "fa fa-archive";
                            break;
                        case 'banktransfer_status':
                            $icon = "fa fa-bank";
                            break;
                        case 'freejoin_status':
                            $icon = "fa fa-cog";
                            break;
                        default:
                            # code...
                            break;
                    }
                    $title = lang($key);
                    $code = substr($key, 0, -7);
                    if ($this->IS_MOBILE) {
                        $title = lang($code);
                    }
                    if ($this->IS_MOBILE && in_array($code, ['paypal', 'authorize', 'blockchain', 'bitgo', 'payeer', 'sofort', 'squareup'])) {
                        continue;
                    }
                    $PaymentMethods[] = [
                        'code' => $code,
                        'value' => true,
                        'title' => $title,
                        'icon'  => $this->IS_MOBILE ? str_replace("fa fa-", "", $icon) : $icon
                    ];
                }
            }
            $data['paymentMethods'] = [
                'title' => [
                    'code'  => 'paymentType',
                    'title' => lang('reg_type')
                ],
                'fields' => $PaymentMethods,
                'registrationFee' => $registration_fee
            ];
        }
        if ($this->IS_MOBILE) {
            $data = array_values($data);
            $data = [
                'list' => $data,
                'PaymentMethodsStatus' => $PaymentMethodsStatus
            ];
        } else {
            $data['PaymentMethodsStatus'] = $PaymentMethodsStatus;
        }
        $this->set_success_response(200, $data);
    }
    //validate sponsor username
    function validate_username_post()
    {
        $this->load->model('register_model');
        $username = $this->post('username');
        $flag = false;
        $sponsorFullName = '';
        if ($this->validation_model->userNameToID($username)) {
            $user_id = $this->validation_model->userNameToID($username);
            $sponsorFullName = $this->register_model->getReferralName($user_id);
            $flag = true;
            $data = [
                'valid' => $flag,
                'sponsorFullName'   => $sponsorFullName
            ];
            $this->set_success_response(200, $data);
        } else {
            $this->set_error_response(422, 1007);
        }
    }

    /** 
     * Check availablity
     */
    public function check_leg_availability_get()
    {
        $placement_id = $this->validation_model->userNameToID($this->get('placement_user_name'));
        if (!$placement_id) {
            return $this->set_error_response(422, 1043);
        }
        if (!in_array($this->get('position'), ['L', 'R'])) {
            return $this->set_error_response(422, 1033);
        }
        if ($this->register_model->checkPositionUsed($placement_id, $this->get('position'))) {
            return $this->set_error_response(422, 1033);
        }
        $this->set_success_response(200, ['valid' => true]);
    }

    //check_leg availability
    function check_leg_availability_post()
    {
        $sponsor_leg = $this->post('sponsor_leg');
        $sponsor_user_name = $this->post('sponsor_user_name');
        $placement_user_name = $this->post('placement_user_name');
        if ($this->check_leg($sponsor_leg, $sponsor_user_name, $placement_user_name)) {
            $data = [
                'valid' => true
            ];
            $this->set_success_response(200, $data);
        } else {
            $this->set_error_response(422, 1033);
        }
    }

    //chekck the valida username
    function check_username_post()
    {
        $username = $this->post('userName');
        $is_username_exists = $this->validation_model->isUsernameExists($username);
        if ($is_username_exists) {
            //invalid username
            $this->set_error_response(422, 1010);
        } else {
            //username valid
            $data = [
                'valid' => true
            ];
            $this->set_success_response(200, $data);
        }
    }

    //register submit
    function register_submit_post()
    {
        $user_id = $this->LOG_USER_ID;
        $username = $this->validation_model->IdToUserName($user_id);
        $user_type = $this->validation_model->getUserType($user_id);
        //chekc the registration is blocked
        if ($this->BLOCK_REGISTER && !in_array($this->LOG_USER_TYPE, ['admin', 'employee'])) {
            $this->set_error_response(422, 1006);
        }
        //check the registration allow
        $signup_settings = $this->configuration_model->getGeneralSignupConfig();
        if ($signup_settings['registration_allowed'] == 'no' && !in_array($this->LOG_USER_TYPE, ['admin', 'employee']) && !$by_replica) {
            $this->set_error_response(422, 1034);
        }

        $regr = [];
        $reg_post_array              = $this->post(null, true);
        $reg_post_array['position']  = $reg_post_array['position'] ?? "";
        $reg_post_array['product_id']= $reg_post_array['product_id'] ?? 0;
        $payment_gateway_array       = $this->register_model->getPaymentGatewayStatus("registration");
        $this->form_validation->set_data($reg_post_array);
        //validate sponsor username
        $sponsorUserName = $this->validation_model->IdToUserName($this->LOG_USER_ID);
        
        if ($this->LOG_USER_TYPE == 'user' && $reg_post_array['sponsor_user_name'] != $sponsorUserName) {
            $this->set_error_response(422, 1035);
        }
        if ($this->validate_register_submit()) {
            $payment_status  = $is_pin_ok = $is_ewallet_ok = $is_paypal_ok = $is_authorize_ok = $is_blockchain_ok = $is_bitgo_ok = $is_bank_transfer_ok = $is_payeer_ok = $is_sofort_ok = $is_squareup_ok = false;
            //check the free join payment method
            if ($reg_post_array['payment_method'] == 'freejoin' && $payment_gateway_array['freejoin_status'] == 'no') {
                $this->set_error_response(422, 1036);
            }
            $module_status   = $this->MODULE_STATUS;
            $username_config = $this->configuration_model->getUsernameConfig();
            $reg_post_array  = $this->validation_model->stripTagsPostArray($reg_post_array);
            $active_tab      = $reg_post_array['payment_method'];
            $regr = $reg_post_array;
            if ($this->MLM_PLAN == "Unilevel" || $this->MLM_PLAN == "Stair_Step") {
                $regr['placement_user_name'] = $reg_post_array["sponsor_user_name"];
            }
            $regr['reg_amount']       = $this->register_model->getRegisterAmount();
            $regr['product_status']   = $this->MODULE_STATUS['product_status'];
            $regr['total_amount']     = (float)$regr['reg_amount'];
            $regr['product_id']       = $regr['product_id'] ?? 0;
            $regr['product_name']     = "";
            $regr['product_pv']       = "";
            $regr['product_amount']   = "";
            $regr['product_validity'] = "";

            if($this->MODULE_STATUS['product_status'] == "yes"){
                $product_details = $this->getProductDetails($reg_post_array['product_id']);
                $regr['product_name']     = $product_details['name'];
                $regr['product_pv']       = $product_details['pv'];
                $regr['product_amount']   = $product_details['amount'];
                $regr['product_validity'] = $product_details['validity'];
                $regr['total_amount']     = (float)$regr['reg_amount'] + (float)$regr['product_amount'];
            }
            $regr['joining_date']     = date('Y-m-d H:i:s');
            $regr['active_tab']       = $active_tab;
            $regr['user_name_type']   = $username_config["type"];
            $regr['reg_from_tree']    = $reg_post_array['reg_from_tree'] ?? false;
            $placement_user_name      = $regr['placement_user_name'] ?? '';
            $regr['placement_user_name'] = $placement_user_name;
            $regr['sponsor_id']       = $this->validation_model->userNameToID($regr['sponsor_user_name']);
            $regr['placement_id']     = $this->validation_model->userNameToID($regr['placement_user_name']);
            
            //check the payment method 
            if ($this->MODULE_STATUS['product_status'] == 'yes' || $regr['reg_amount'] > 0) {
                $payment_type = "free_join";
                if ($active_tab == 'epin') {
                    $payment_type = 'epin';
                    $pin_count = $reg_post_array['pin_array'];
                    $pin_details = [];
                    for ($i = 1; $i <= $pin_count; $i++) {
                        if ($reg_post_array["epin$i"]) {
                            $pin_number = $reg_post_array["epin$i"];
                            $pin_details[$i]['pin'] = $pin_number;
                            $pin_details[$i]['i'] = $i;
                        }
                    }
                    $pin_array = $this->register_model->checkAllEpins($pin_details, $reg_post_array['product_id'], $this->MODULE_STATUS['product_status'], $regr['sponsor_id'], true);

                    $is_pin_ok = $pin_array["is_pin_ok"];
                    if (!$is_pin_ok) {
                        $this->set_error_response(401, 1016);
                    }
                    // } 
                } elseif ($active_tab == "freejoin") {
                    $payment_type = 'free_join';
                    $is_free_join_ok = true;
                } elseif ($active_tab == "banktransfer") {
                    $payment_type = 'bank_transfer';
                    $is_bank_transfer_ok = true;
                } elseif ($active_tab == 'ewallet') {
                    $payment_type = "ewallet";
                    $used_amount = $regr['total_amount'];
                    $ewallet_user = $username;
                    $ewallet_trans_password = $reg_post_array['tran_pass_ewallet'];

                    $user_available = $this->register_model->isUserAvailable($ewallet_user);
                    if ($user_available) {
                        if ($ewallet_trans_password != "") {
                            $ewallet_user_id = $this->validation_model->userNameToID($ewallet_user);
                            $trans_pass_available = $this->register_model->checkEwalletPassword($ewallet_user_id, $ewallet_trans_password);
                            if ($trans_pass_available == 'yes') {
                                $ewallet_balance_amount = $this->register_model->getBalanceAmount($ewallet_user_id);
                                if ($ewallet_balance_amount >= $used_amount) {
                                    $is_ewallet_ok = true;
                                } else {
                                    $this->set_error_response(401, 1014);
                                }
                            } else {
                                $this->set_error_response(401, 1015);
                            }
                        } else {
                            $this->set_error_response(401, 1015);
                        }
                    } else {
                        $this->set_error_response(401, 1011);
                    }
                } else if ($active_tab == 'paypal') {
                    $payment_gateway_array = $this->register_model->getPaymentGatewayStatus("registration");
                    if ($payment_gateway_array['paypal_status'] == 'no') {
                        $this->set_error_response(401, 1009);
                    }
                    $payment_type = "paypal";
                    $is_paypal_ok = true;
                } else {
                    $this->set_error_response(422, 1036);
                }
                $regr['payment_type'] = $payment_type;
                $regr['payment_method'] = $payment_type;
                $pending_signup_status = $this->configuration_model->getPendingSignupStatus($payment_type);
                $email_verification = $this->configuration_model->getEmailVerificationStatus();
                if ($is_pin_ok) {
                    $this->register_model->begin();
                    $regr['by_using'] = 'pin';
                    $res = $this->register_model->UpdateUsedUserEpin($pin_array, $pin_count);
                    $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);
                    if ($status['status']) {
                        $pin_array['user_id'] = $status['id'];
                        $res = $this->register_model->UpdateUsedEpin($pin_array, $pin_count);
                        if ($res) {
                            $this->register_model->insertUsedPin($pin_array, $pin_count, $pending_signup_status, '', $email_verification);
                            $payment_status = true;
                        }
                    }
                } elseif ($is_ewallet_ok) {
                    $this->register_model->begin();

                    /*$regr['by_using'] = 'ewallet';
                    $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);
                    if ($status['status']) {
                        $user_id = $status['id'];
                        $used_user_id = $this->validation_model->userNameToID($ewallet_user);
                        $transaction_id = $this->ewallet_model->getUniqueTransactionId();
                        $res1 = $this->register_model->insertUsedEwallet($used_user_id, $user_id, $used_amount, $transaction_id, $pending_signup_status, 'registration', $email_verification);
                        if ($res1) {
                            $res2 = $this->register_model->deductFromBalanceAmount($used_user_id, $used_amount);
                            if ($res2) {
                                $payment_status = true;
                            }
                        }
                    }*/
                    $used_user_id = $this->validation_model->userNameToID($ewallet_user);
                    $transaction_id = $this->ewallet_model->getUniqueTransactionId();
                    $ewallet_id = $this->register_model->insertUsedEwallet($used_user_id, $used_user_id, $used_amount, $transaction_id, $pending_signup_status, 'registration', $email_verification);
                    if ($ewallet_id) {
                        $res2 = $this->register_model->deductFromBalanceAmount($used_user_id, $used_amount);
                    }

                    if ($ewallet_id && $res2) {
                        $regr['by_using'] = 'ewallet';
                        $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);
                        if ($status['status']) {
                            $this->register_model->updateEwalletDetails($ewallet_id, $status['id']);
                            $payment_status = true;
                        }
                    }
                } elseif ($is_paypal_ok) {
                    $pending_signup_status = $this->configuration_model->getPendingSignupStatus('paypal');
                    $total_amount = $regr['total_amount'];
                    $referral_id = $regr["sponsor_id"];
                    $payment_details = array(
                        'payment_method' => 'paypal',
                        'token_id' => $reg_post_array['paypal_token'],
                        'currency' => $reg_post_array['currency'],
                        'amount' => $total_amount,
                        'acceptance' => '',
                        'payer_id' => $reg_post_array['PayerID'],
                        'user_id' => $referral_id,
                        'status' => '',
                        'card_number' => '',
                        'ED' => '',
                        'card_holder_name' => '',
                        'submit_date' => date("Y-m-d H:i:s"),
                        'pay_id' => '',
                        'error_status' => '',
                        'brand' => ''
                    );
                    $this->register_model->insertintoPaymentDetails($payment_details);
                    $module_status = $this->MODULE_STATUS;
                    $regr['by_using'] = 'paypal';
                    $email_verification = $this->configuration_model->getEmailVerificationStatus();
                    $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);
                    $msg = '';
                    if ($status['status']) {
                        $payment_status = true;
                        // $user_name = $status['user_name'];
                        // $user_id = $status['user_id'];
                        // $transaction_password = $pending_signup_status ? '' : $status['transaction_password'];

                        // $payment_method = "paypal";
                        // $product_status = $this->MODULE_STATUS['product_status'];
                        // $email_verification = $this->configuration_model->getEmailVerificationStatus();
                        // if ($product_status == "yes") {
                        //     $insert_into_sales = $this->register_model->insertIntoSalesOrder($user_id, $regr['product_id'], $payment_method, $pending_signup_status, $email_verification);
                        // }
                        // $this->register_model->commit();
                        // $id_encode = $this->encryption->encrypt($user_name);
                        // $id_encode = str_replace("/", "_", $id_encode);
                        // $user_name_encrypt = urlencode($id_encode);
                    }
                } elseif ($is_authorize_ok) {
                    $regr['by_using'] = 'Authorize.Net';
                    $this->session->set_userdata('inf_regr', $regr);
                    $msg = "";
                    if ($by_replica) {
                        $link  = 'register/replica_authorizeNetPayment';
                    } else {
                        $link  = 'register/authorizeNetPayment';
                    }
                    $this->redirect($msg, $link, false);
                } elseif ($is_blockchain_ok) {
                    $regr['by_using'] = 'blockchain';
                    $this->session->set_userdata('inf_regr', $regr);
                    $msg = "";
                    if ($by_replica) {
                        $link  = 'register/replica_blockchain';
                    } else {
                        $link  = 'register/blockchain';
                    }
                    $this->redirect($msg, "register/blockchain", false);
                } elseif ($is_bitgo_ok) {
                    $regr['by_using'] = 'bitgo';
                    $regr['is_new'] = 'yes';
                    $this->session->set_userdata('inf_regr', $regr);
                    $msg = "";
                    if ($by_replica) {
                        $link  = 'register/replica_bitgo_gateway';
                    } else {
                        $link  = 'register/bitgo_gateway';
                    }
                    $this->redirect($msg, $link, false);
                } elseif ($is_payeer_ok) {
                    $regr['by_using'] = 'payeer';
                    $data = array(
                        'user_id' => $this->LOG_USER_ID,
                        'product_id' => $reg_post_array['product_id'],
                        'product_name' => $product_details['name'],
                        'product_amount' => $product_details['amount'],
                        'currency' => 'EUR',
                    );
                    $msg = "";
                    $this->session->set_userdata('payeer_data', $data);
                    if ($by_replica) {
                        $link  = 'register/replica_payeer';
                    } else {
                        $link  = 'register/payeer';
                    }
                    $this->redirect($msg, $link, false);
                } elseif ($is_sofort_ok) {
                    $regr['by_using'] = 'sofort';
                    $this->session->set_userdata('inf_regr', $regr);
                    $msg = "";
                    if ($by_replica) {
                        $link  = 'register/replica_sofort_payment';
                    } else {
                        $link  = 'register/sofort_payment';
                    }
                    $this->redirect($msg, $link, false);
                } elseif ($is_squareup_ok) {
                    $regr['by_using'] = 'squareup';
                    $this->session->set_userdata('inf_regr', $regr);
                    $msg = "";
                    if ($by_replica) {
                        $link  = 'register/replica_squareup_gateway';
                    } else {
                        $link  = 'register/squareup_gateway';
                    }
                    $this->redirect($msg, $link, false);
                } elseif ($is_bank_transfer_ok) {

                    $regr['by_using'] = 'bank_transfer';
                    $this->register_model->begin();

                    $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);
                    if ($status['status']) {
                        $payment_status = true;
                    }
                } else {
                    $regr['by_using'] = 'free join';
                    $this->register_model->begin();
                    $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);
                    if ($status['status']) {
                        $payment_status = true;
                    }
                }
            } else {
                $regr['by_using'] = 'free join';
                $payment_type = 'free_join';
                $regr['payment_type'] = $payment_type;
                $pending_signup_status = false;
                $email_verification = $this->configuration_model->getEmailVerificationStatus();
                $this->register_model->begin();
                $status = $this->register_model->confirmRegister($regr, $module_status, $pending_signup_status, $email_verification);

                if ($status['status']) {

                    $payment_status = true;
                }
            }
            
            $msg = '';
            if ($payment_status && $this->validation_model->isUserActive($regr['sponsor_id'])) 
            {
                $user_name = $status['user_name'];
                $user_id = $status['user_id'];
                $transaction_password = ($pending_signup_status || ($email_verification == 'yes'))  ? '' : $status['transaction_password'];

                if ($this->MODULE_STATUS['product_status'] == "yes") {

                    $insert_into_sales = $this->register_model->insertIntoSalesOrder($user_id, $regr['product_id'], $payment_type, $pending_signup_status, $email_verification);
                }
                $this->register_model->commit();
                $activity_user_id = $this->LOG_USER_ID;
                $this->validation_model->insertUserActivity($activity_user_id, 'New user registered', $user_id);
                if ($pending_signup_status) {
                    $data = [
                        'message' => lang('registration_completed_successfully_pending') . '!' . lang("User_Name") . ": {$user_name}",
                        'code'    => 'registration_completed_successfully_pending',
                        'userName' => $user_name
                    ];
                    $this->set_success_response(200, $data);
                } elseif (($email_verification == 'yes') && ($pending_signup_status == 0)) {
                    $data = [
                        'message' => lang('email_verification_required') . '!' . lang("User_Name") . ": {$user_name}",
                        'code'    => 'email_verification_required',
                        'userName' => $user_name
                    ];
                    $this->set_success_response(200, $data);
                } else {
                    $data = [
                        'message' => lang('email_verification_required') . '!' . lang("User_Name") . ": {$user_name}",
                        'code'    => 'registration_completed_successfully',
                        'transaction_password' => $transaction_password,
                        'userName' => $user_name
                    ];
                    $this->set_success_response(200, $data);
                }
            }
        } else {
            // dd(validation_errors());
            $this->set_error_response(422, 1004);
        }
    }

    //validate the register 
    function validate_register_submit()
    {
        $this->lang->load('validation');
        $product_status = $this->MODULE_STATUS['product_status'];
        $username_config = $this->configuration_model->getUsernameConfig();
        $user_name_type = $username_config["type"];
        $contact_fields = $this->register_model->getSignUpAllFieldStatus();
        // dd($this->post());
        //sponsor username
        $this->form_validation->set_rules('sponsor_user_name', lang('sponsor_user_name'), 'required|callback_validate_username|trim', [
            'required' => lang('required'),
            'validate_username' => lang('invalid'),
        ]);
        //check the plan
        if ($this->MLM_PLAN == 'Binary') {
            //position
            $this->form_validation->set_rules('position', lang('position'), 'trim|required|in_list[L,R]|callback_vm_check_sponsor_leg_available', [
                'required' => lang('required'),
                'in_list' => lang('invalid')
            ]);
        }

        //package status
        if ($product_status == "yes") {
            $this->form_validation->set_rules('product_id', lang('product'), 'trim|required|callback_valid_product[registration]', [
                'required' => lang('required'),
                'valid_product' => lang('invalid')
            ]);
        }

        //username
        $usernameRange = $this->validation_model->getUsernameRange();
        $minlength = $usernameRange['min'];
        $maxlength = $usernameRange['max'];
        if ($user_name_type == 'static') {
            $this->form_validation->set_rules('user_name_entry', lang('User_Name'), 'required|regex_match[/^[a-zA-Z0-9_.-]+$/]|min_length[' . "$minlength" . ']|max_length[' . "$maxlength" . ']|callback_is_username_available', [
                'regex_match' => lang('alpha_numeric_some_special'),
                'required' => lang('required'),
                'min_length' => sprintf(lang('minlength'), lang('username'), "$minlength"),
                'max_length' => sprintf(lang('maxlength'), lang('username'), "$maxlength"),
                'is_username_available' => lang('username_not_available')
            ]);
        }

        //password
        $this->form_validation->set_rules('pswd', lang('password'), "{$this->validation_model->getPasswordPolicyValidationString()}", [
            'required' => lang('required'),
            'max_length' => sprintf(lang('maxlength'), lang('password'), "50")
        ]);
        //confirm password
        $this->form_validation->set_rules('cpswd', lang('confirm_password'), 'trim|matches[pswd]', [
            'matches' => lang('password_mismatch')
        ]);
        //first name
        if ($contact_fields['first_name'] == "yes") {
            $this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required|max_length[250]', [
                'required' => lang('required'),
                'max_length' => sprintf(lang('maxlength'), lang('first_name'), "250"),
            ]);
        }
        //lastname
        if ($contact_fields['last_name'] == "yes") {
            $this->form_validation->set_rules('last_name', lang('last_name'), 'trim|max_length[250]|callback_check_required[last_name]', [
                'check_required' => lang('required'),
                'max_length' => sprintf(lang('maxlength'), lang('last_name'), "250"),
            ]);
        }
        //date of birth
        if ($contact_fields['date_of_birth'] == "yes") {
            $this->form_validation->set_rules('date_of_birth', lang('date_of_birth'), 'trim|callback_validate_age_year|callback_check_required[date_of_birth]', [
                'validate_age_year' => sprintf(lang('valid_age'), $this->configuration_model->getAgeLimitSetting()),
                'check_required' => lang('required')
            ]);
        }
        //email
        if ($contact_fields['email'] == "yes") {
            $this->form_validation->set_rules('email', lang('email'), 'trim|required|max_length[250]|valid_email', [
                'required' => lang('required'),
                'max_length' => sprintf(lang('maxlength'), lang('email'), "250"),
                'valid_email' => lang('valid_email'),
            ]);
        }
        //mobile
        if ($contact_fields['mobile'] == "yes") {
            $this->form_validation->set_rules('mobile', lang('mobile_no'), 'trim|required|regex_match[/^[\s0-9+()-]+$/]|max_length[50]', [
                'required' => lang('required'),
                'max_length' => sprintf(lang('maxlength'), lang('mobile_no'), "50"),
                'regex_match' => lang('phone_number'),
            ]);
        }
        //gender
        if ($contact_fields['gender'] == "yes") {
            $this->form_validation->set_rules('gender', lang('gender'), 'trim|callback_check_required[gender]|in_list[M,F]', ['in_list' => lang('You_must_select_gender')]);
        }
        //address line 1
        if ($contact_fields['adress_line1'] == "yes") {
            $this->form_validation->set_rules('adress_line1', lang('adress_line1'), 'trim|max_length[1000]|callback_check_required[adress_line1]', [
                'max_length' => sprintf(lang('maxlength'), lang('address_line1'), "1000"),
                'check_required' => lang('required')
            ]);
        }
        //addressline 2
        if ($contact_fields['adress_line2'] == "yes") {
            $this->form_validation->set_rules('adress_line2', lang('adress_line2'), 'trim|max_length[1000]|callback_check_required[adress_line2]', [
                'max_length' => sprintf(lang('maxlength'), lang('address_line2'), "1000"),
                'check_required' => lang('required')
            ]);
        }
        //country
        if ($contact_fields['country'] == "yes") {
            $this->form_validation->set_rules('country', lang('country'), 'trim|callback_check_required[country]', [
                'check_required' => lang('required')
            ]);
        }
        //state
        if ($contact_fields['state'] == "yes") {
            $this->form_validation->set_rules('state', lang('state'), 'trim|callback_check_required[state]', [
                'check_required' => lang('required')
            ]);
        }
        //city
        if ($contact_fields['city'] == "yes") {
            $this->form_validation->set_rules('city', lang('city'), 'trim|max_length[250]|callback_check_required[city]', [
                'max_length' => lang('string_max_length'),
                'check_required' => lang('required')
            ]);
        }
        //zip code 
        if ($contact_fields['pin'] == "yes") {
            $this->form_validation->set_rules('pin', lang('pin'), 'trim|max_length[50]|callback_check_required[pin]', [
                'max_length' => sprintf(lang('maxlangth'), lang('pin'), "50"),
                'check_required' => lang('required'),
            ]);
        }
        //landline
        if ($contact_fields['land_line'] == "yes") {
            $this->form_validation->set_rules('landLine', lang('land_line'), 'trim|regex_match[/^[\s0-9+()-]+$/]|max_length[50]|callback_check_required[land_line]', [
                'max_length' => sprintf(lang('maxlength'), lang('land_line'), "50"),
                'check_required' => lang('required'),
                'regex_match' => lang('phone_number')
            ]);
        }
        $this->form_validation->set_rules('agree_terms', lang('terms_conditions'), 'trim|required', [
            'required' => lang('agree')
        ]);

        return $this->form_validation->run();
    }

    //validate user name
    function validate_username($username = '')
    {
        if ($this->validation_model->userNameToID($username)) {
            return true;
        } else {
            return false;
        }
    }

    //check the leg available
    function vm_check_sponsor_leg_available()
    {
        $placement_user_name = $this->post('placement_user_name') ?? '';
        $this->form_validation->set_message('vm_check_sponsor_leg_available', lang('position_not_useable'));
        $sponsor_leg = $this->post('position');
        $sponsor_user_name = $this->post('sponsor_user_name');
        $placement_user_name = $this->post('placement_user_name');
        return $this->check_leg($sponsor_leg, $sponsor_user_name, $placement_user_name);
    }


    //check the leg available private function 
    function check_leg($sponsor_leg, $sponsor_user_name, $placement_user_name = '')
    {
        $sponsor_id = $this->validation_model->userNameToID($sponsor_user_name);
        if (!$sponsor_id) {
            return fasle;
        }
        $sponsor_user_type = $this->validation_model->getUserType($sponsor_id);
        $admin_locked_binary_leg = $this->configuration_model->getSignupBinaryLeg();
        if ($admin_locked_binary_leg != 'any') {
            if ($sponsor_user_type == "admin") {
                if ($admin_locked_binary_leg != $sponsor_leg) {
                    return false;
                }
            } else {
                $admin_legs = $this->tree_model->getUserLeftRightNode($this->ADMIN_USER_ID);
                $admin_leg_id  = $admin_legs[$admin_locked_binary_leg];
                if (!$this->tree_model->checkAncestor($admin_leg_id, $sponsor_id)) {
                    return false;
                }
            }
            return true;
        }
        if (in_array($this->LOG_USER_TYPE, ['admin', 'employee'])) {
            return false;
        }

        $user_locked_binary_leg = $this->configuration_model->getUserWiseSignupBinaryLeg($sponsor_id);
        if ($user_locked_binary_leg == "any") {
            return true;
        }
        if ($placement_user_name == $sponsor_user_name || !$placement_user_name) {
            if ($user_locked_binary_leg != $sponsor_leg) {
                return false;
            }
        } else {
            // from tree
            $placement_id = $this->validation_model->userNameToID($placement_user_name);
            $sponsor_legs = $this->tree_model->getUserLeftRightNode($sponsor_id);
            $sponsor_leg_id  = $sponsor_legs[$user_locked_binary_leg];
            if (!$this->tree_model->checkAncestor($sponsor_leg_id, $placement_id)) {
                return false;
            }
        }
        return true;
    }


    //validate the product
    function valid_product($product_id, $type)
    {
        $res = $this->product_model->isActiveProduct($product_id, $type);
        $this->form_validation->set_message('valid_product', lang('you_must_select_product'));
        return ($res > 0);
    }

    //is username is available
    function is_username_available($user_name)
    {
        if (!$user_name) {
            return false;
        }
        $is_username_exists = $this->validation_model->isUsernameExists($user_name);
        if ($is_username_exists) {
            return false;
        } else {
            return true;
        }
    }

    //check the required value
    public function check_required($field_value, $field_name)
    {
        $status = $this->register_model->getRequiredStatus($field_name);
        if ($status == 'yes') {
            if ($field_value == '') {
                $this->form_validation->set_message('check_required', sprintf(lang('the_n_field_is_required'), lang($field_name)));
                return false;
            } else
                return true;
        } else {
            return true;
        }
    }

    //validate the age
    function validate_age_year($dob)
    {
        $age_limit = $this->configuration_model->getAgeLimitSetting();
        if ($age_limit == 0) {
            return true;
        }
        $year = date('Y', strtotime($dob));
        $current_year = date('Y');
        if (($current_year - $year) >= $age_limit) {
            return true;
        } else {
            $this->form_validation->set_message('validate_age_year', sprintf(lang('You_should_be_atleast_n_years_old'), $age_limit));
            return false;
        }
    }
    //get the product details
    protected function getProductDetails($product_id)
    {
        $product_details = [
            'name'    => '',
            'pv'      => '',
            'amount'  => '',
            'validity' => ''
        ];
        if ($this->MODULE_STATUS['product_status'] == "yes") {
            $product = $this->product_model->getProductDetails($product_id, 'yes');
            $product_details['name'] = $product[0]['product_name'];
            $product_details['pv'] = $product[0]['pair_value'];
            $product_details['amount'] = $product[0]['product_value'];
            if ($this->MODULE_STATUS['subscription_status'] == "yes") {
                $product_details['validity'] = $this->product_model->getPackageValidityDate($product[0]['prod_id'], '', $this->MODULE_STATUS);
            }
        }
        return $product_details;
    }

    //registration preview 
    function registration_preview_get()
    {
        $feild = $this->input->get(null, true);
        $user_name = $feild['username'] ?? '';
        if ($user_name) {
            $user_info = [];
            $user_id = $this->validation_model->userNameToID($user_name);
            $is_pending_registration = $this->validation_model->isPendingUserRegistration($user_name);
            $email_verification = $this->configuration_model->getEmailVerificationStatus();
            if ($email_verification == 'yes' && !$is_pending_registration) {
                $this->set_error_response(422, 1037);
            }
            if (!$user_id && !$is_pending_registration) {
                $this->set_error_response(422, 1011);
            }
            $user_info[] = [
                'code'  => 'userName',
                'title' => lang('User_Name'),
                'value' => $user_name
            ];

            // if (DEMO_STATUS == 'no' && $this->check_replica_user()) {
            //     $replica_session = $this->inf_model->getReplicaSessionFromFile();
            //     $replica_user = $replica_session['replica_user'];
            //     $sponsor_user_name = $replica_user['user_name'];
            //     $this->set("sponsor_user_name", $sponsor_user_name);
            // }
            $user_type = $this->LOG_USER_TYPE;
            if ($user_type == "employee") {
                $user_type = 'admin';
            }
            $date = date('Y-m-d H:i:s');
            $lang_id = $this->LANG_ID;
            $letter_arr = $this->configuration_model->getLetterSetting($lang_id);
            $site_configuration = $this->validation_model->getSiteInformation();
            $product_status = $this->MODULE_STATUS['product_status'];
            $referal_status = $this->MODULE_STATUS['referal_status'];
            if ($is_pending_registration) {
                $user_registration_details = $this->register_model->getUserRegistrationDetailsForPreview(0, $user_name);
                $product_id = $user_registration_details['product_id'];
            } else {
                $user_registration_details = $this->register_model->getUserRegistrationDetailsForPreview($user_id);
                $father_id = $this->validation_model->getFatherId($user_id);
                $product_id = $this->validation_model->getProductId($user_id);
                $placement_user_name = $this->validation_model->IdToUserName($father_id);
            }
            $sponsor_id = $user_registration_details['sponsor_id'];
            $sponsorname = $this->validation_model->IdToUserName($sponsor_id);
            $user_info[] = [
                'code'  => 'fullName',
                'title' => lang('fullname'),
                'value' => $user_registration_details['first_name'] . ' ' . $user_registration_details['last_name']
            ];
            $user_info[] = [
                'code'  => 'sponsor',
                'title' => lang('sponsor'),
                'value' => $sponsorname
            ];
            $reg_amount = $this->register_model->getRegisterAmount();
            $user_info[] = [
                'code'  => 'registrationAmount',
                'title' => lang('registration_amount'),
                'value' => format_currency($reg_amount),
                'amount' => $reg_amount
            ];
            $total_amount = $reg_amount;
            if ($product_status == "yes") {
                $product_details = $this->register_model->getProduct($product_id);
                $this->set("product_details", $product_details);
                $this->set("product_status", $product_status);
                $user_info[] = [
                    'code'  => 'package',
                    'title' => lang('package'),
                    'value' => $user_registration_details['product_name']
                ];
                $user_info[] = [
                    'code'  => 'packageAmount',
                    'title' => lang('package_amount'),
                    'value' => format_currency($user_registration_details['product_amount']),
                    'amount' => $user_registration_details['product_amount']
                ];
                if ($total_amount > 0) {
                    $total_amount = $total_amount + $user_registration_details['product_amount'];
                } else {
                    $total_amount = $user_registration_details['product_amount'];
                }
            }
            $user_info[] = [
                'code'  => 'totalAmount',
                'title' => lang('total_amount'),
                'value' => format_currency($total_amount),
                'amount' => $total_amount
            ];
            $site_configuration = $this->validation_model->getSiteInformation();
            $data = [
                'user_info' => $user_info,
                'letter'    => [
                    'content' => $letter_arr['main_matter'],
                    'date'  => $date,
                    'companyName' => $site_configuration['company_name']
                ]
            ];
            $this->set_success_response(200, $data);
        }
    }

    //get the epin
    protected function getEpins($product_id, $sponsor_id)
    {
        $pin_details = [];
        for ($i = 1; $i <= $this->post('pin_count'); $i++) {
            if ($this->post('epin' . $i)) {
                $pin_number = $this->input->post('epin' . $i);
                $pin_details[$i]['pin'] = $pin_number;
                $pin_details[$i]['i'] = $i;
            }
        }
        return $this->register_model->checkAllEpins($pin_details, $reg_post_array['product_id'], $this->MODULE_STATUS['product_status'], $regr['sponsor_id'], true);
    }
    //upload payment reciept
    function upload_payment_reciept_post()
    {
        $this->load->library('upload');
        if (!isset($_FILES['file'])) {
            $this->set_error_response(422, 1032);
        }
        if (!empty($_FILES['file']) && isset($_FILES['file'])) {
            $upload_config = $this->validation_model->getUploadConfig();
            $upload_count = $this->validation_model->getUploadCount($this->LOG_USER_ID);
            if ($upload_count >= $upload_config) {
                $this->set_error_response(422, 1038);
            }
        }
        if ($_FILES['file']['error'] != 4) {
            $random_number = floor(2048 * rand(1000, 9999));
            $config['file_name'] = "bank_" . $random_number;
            $config['upload_path'] = IMG_DIR . 'reciepts';
            $config['allowed_types'] = 'jpg|png|jpeg';
            $config['max_size'] = '2048';
            $config['max_width'] = '3000';
            $config['max_height'] = '3000';
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('file')) {
                $msg = $this->upload->display_errors();
                $msg = strip_tags($msg);
                $response['error'] = true;
                $response['message'] = $msg;
                $this->set_error_response(422, 1024);
            } else {
                $user_name = $this->post('user_name', true) ?: $this->validation_model->IdToUserName($this->LOG_USER_ID);
                $result = '';
                $data = array('upload_data' => $this->upload->data());
                $doc_file_name = $data['upload_data']['file_name'];
                $result = $this->register_model->addReciept($user_name, $doc_file_name);
                $this->validation_model->updateUploadCount($this->LOG_USER_ID);
                if ($result) {
                    $data_array['file_name'] = $doc_file_name;
                    $data = serialize($data_array);

                    // Employee Activity History
                    if ($this->LOG_USER_TYPE == 'employee') {
                        $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'upload_material', 'Payment Receipt Uploaded');
                    }
                    $msg = lang('payment_receipt_ploaded_successfully');
                    $response['success'] = true;
                    $response['message'] = $msg;
                    $response['file_name'] = $doc_file_name;
                    $this->set_success_response(200, $response);
                } else {
                    $this->set_error_response(422, 1024);
                }
            }
        } else {
            $this->set_error_response(422, 1024);
        }
    }

    //get the accound details 
    function accound_details_get()
    {
        $bank_details = $this->configuration_model->getBankInfo();
        $this->set_success_response(200, $bank_details);
    }

    //remove the imgaes
    function remove_reciept_post()
    {
        if ($this->post('user_name')) {
            $receipt_path = $this->register_model->getReceipt($this->post('user_name'));
            @unlink(IMG_DIR . 'reciepts/' . $receipt_path);
            $this->set_success_response(201);
        }
        $this->set_success_response(200);
    }
    function check_epin_validity_post()
    {
        $product_id = $this->post('product_id');
        $pin_details = $this->post('pin_array');
        $product_status = $this->MODULE_STATUS["product_status"];
        $sponsor_id = $this->LOG_USER_ID;
        $sponsor_name = $this->validation_model->getUserName($sponsor_id);
        $flag = false;
        if ($sponsor_name != '') {
            if ($this->register_model->isUserAvailable($sponsor_name)) {
                $flag = true;
            }
        }
        if ($flag) {
            $pin_array = $this->register_model->checkAllEpins($pin_details, $product_id, $product_status, $sponsor_id);
            $total_amount = 0;
            foreach ($pin_array as $value) {
                $total_amount += $value['epin_used_amount'];
            }
            if ($this->IS_MOBILE) {
                $pin_array[count($pin_array) - 1]['total_amount'] = $total_amount;
            }
            $this->set_success_response(200, $pin_array);
        } else {
            $this->set_error_response(422);
        }
    }

    //check the ewallet balance 
    function check_ewallet_balance_post()
    {
        $ewallet_user = $this->post('user_name', true);
        $ewallet_pass = $this->post('ewallet', true);
        $product_id = $this->post('product_id', true);
        $user_name = $this->validation_model->IdToUserName($this->LOG_USER_ID);
        //logged type is user
        if ($this->LOG_USER_TYPE == 'user') {
            if ($ewallet_user != $user_name) {
                $this->set_error_response(422, 1039);
            }
        }

        //log user is admin
        // if ($this->LOG_USER_TYPE == 'admin' || $this->LOG_USER_TYPE == 'employee') {
        //     if ($ewallet_user != $admin_username && $ewallet_user != $sponsor_user_name) {
        //         $status = "invalid";
        //         echo $status;
        //         exit();
        //     }
        // }

        $user_id = $this->validation_model->userNameToID($ewallet_user);
        if ($user_id) {
            $user_password = $this->register_model->checkEwalletPassword($user_id, $ewallet_pass);
            if ($user_password == 'yes') {
                $user_bal_amount = $this->register_model->getBalanceAmount($user_id);
                if ($user_bal_amount > 0) {
                    $reg_amount = $this->register_model->getRegisterAmount();
                    $product_amount = 0;
                    $product_status = $this->MODULE_STATUS['product_status'];
                    if ($product_status == "yes") {
                        $product_details = $this->register_model->getProduct($product_id);
                        $product_amount = $product_details["product_value"];
                    }
                    $total_amount = $reg_amount + $product_amount;

                    if ($user_bal_amount >= $total_amount) {
                        $this->set_success_response(200);
                    } else {
                        $this->set_error_response(422, 1014);
                    }
                } else {
                    $this->set_error_response(422, 1014);
                }
            } else {
                $this->set_error_response(422, 1039);
            }
        } else {
            $this->set_error_response(422, 1039);
        }
    }
    // upgrade payment
    function upgrade_payment_post()
    {
        $this->lang->load('upgrade_lang');
        $this->load->model('upgrade_model');

        $user_id = $this->rest->user_id;
        $product_id = $this->post('product_id');
        $new_package_price = 0;
        $current_package_price = 0;

        $package_info = $this->upgrade_model->getPackageDetails($product_id);
        $current_package_details = $this->upgrade_model->getMembershipPackageDetails($user_id);
        $package_details_pending = $this->upgrade_model->getMembershipPackageDetailspending($user_id);
        if (isset($package_info['price'])) {
            $new_package_price = $package_info['price'];
        }
        if (isset($current_package_details['price'])) {
            $current_package_price = $current_package_details['price'];
        }
        $package_info['amount_to_pay'] = format_currency($new_package_price - $current_package_price);
        $package_info['price'] = format_currency($new_package_price);
        $product_details[] = array(
            'title' => lang('package_name'),
            'value' => $package_info['product_name'],
        );
        $product_details[] = array(
            'title' => lang('package_price'),
            'value' => format_currency($new_package_price)
        );
        $product_details[] = array(
            'title' => lang('package_pv'),
            'value' => $package_info['pair_value']
        );
        $product_details[] = array(
            'title' => lang('amount_to_pay'),
            'value' => $package_info['amount_to_pay']
        );

        $data['upgrade_package_details'] = [
            'title' => [
                'code'  => 'upgrade_package_details',
                'title' => lang('upgrade_package_details')
            ],
            'package' => $product_details
        ];

        $this->set_success_response(200, $data);
    }
}
