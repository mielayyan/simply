<?php
require_once 'Inf_Controller.php';
require_once dirname(FCPATH) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Excel_register extends Inf_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('configuration_model', '', true);
        $this->load->model('register_model');
    }
    function excel_register()
    {

        $title = lang('excel_user_signup');
        $this->set('title', $this->COMPANY_NAME . " | $title");

        $help_link = "register_downline";
        $this->set("help_link", $help_link);

        $this->HEADER_LANG['page_top_header'] = lang('excel_user_signup');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('excel_user_signup');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $signup_settings = $this->configuration_model->getGeneralSignupConfig();
        
        if ($signup_settings['registration_allowed'] == 'no' && $this->LOG_USER_TYPE != 'admin' && $this->LOG_USER_TYPE != 'employee') {
            $msg = lang('registration_not_allowed');
            $this->redirect($msg, 'home', false);
        }
        
        if ($this->input->post('excel_reg')) {

            if (!isset($_FILES['register_doc'])) {
                $msg = lang('select_excel_file');
                $this->redirect($msg, "excel_register", false);
            }
            $upload_config = $this->validation_model->getUploadConfig();
            $upload_count = $this->validation_model->getUploadCount($this->LOG_USER_ID);
            if ($upload_count >= $upload_config) {
                $msg = lang('you_have_reached_max_upload_limit');
                $this->redirect($msg, "excel_register", false);
            }
            $upload_path = IMG_DIR . "/document/excel_reg/";
            $config = array(
                'upload_path' => "$upload_path",
                'allowed_types' => 'xls|xlsx',
                'max_size' => '2048',
            );

            $this->load->library('upload', $config);
            $msg = "";

            if (!$this->upload->do_upload('register_doc')) {
                $error = array('error' => $this->upload->display_errors());
                $error = $this->validation_model->stripTagsPostArray($error);
                $error = $this->validation_model->escapeStringPostArray($error);
                if ($error['error'] == 'The file you are attempting to upload is larger than the permitted size.' || $error['error'] == 'The uploaded file exceeds the maximum allowed size in your PHP configuration file.') {
                    $msg = lang('max_size_2MB');
                    $this->redirect($msg, "excel_register", false);
                } else if ($error['error'] == 'The filetype you are attempting to upload is not allowed.') {
                    $msg = lang('filetype_not_allowed');
                    $this->redirect($msg, "excel_register", false);
                } else if ($error['error'] == 'Invalid file name.') {
                    $msg = lang('invalid_file_name');
                    $this->redirect($msg, "excel_register", false);
                } else if ($error['error'] == 'You did not select a file to upload.') {
                    $msg = lang('you_must_select_file');
                    $this->redirect($msg, "excel_register", false);
                }
            } else {
                $image_arr = array('upload_data' => $this->upload->data());
                $inputFileType = ucfirst(preg_replace("/\./i", "", $image_arr['upload_data']["file_ext"], 1));
                $file_name = $image_arr['upload_data']['full_path'];
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file_name);
                $worksheet = $spreadsheet->getActiveSheet();
                $highestColumn = $worksheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
                // if ($this->MLM_PLAN == "Binary" && $highestColumnIndex == 10)
                //     $col_flag = true;
                // elseif ($highestColumnIndex == 9)
                //     $col_flag = true;
                // else
                //     $col_flag = false;
                // if (!$col_flag) {
                //     unlink($file_name);
                //     $msg = lang('please_provide_excel_as_given_format');
                //     $this->redirect($msg, 'excel_register', false);
                // }
                $rows = $worksheet->toArray();
                $counter = 0;

                $binary_leg = $this->configuration_model->getSignupBinaryLeg();
                $exclude_ancestor = '';
                if($binary_leg != 'any') {
                    $binary_leg_exclude = ($binary_leg == 'L') ? 'R' : 'L' ;
                    $exclude_ancestor = $this->excel_register_model->getChildId($this->ADMIN_USER_ID, $binary_leg_exclude);
                    $this->load->model('tree_model');
                }
                foreach ($rows as $value) {
                    if ($counter++ == 0)
                        continue;
                    $i = 0;
                    $require_flag = true;
                    $reg = [
                        'id' => $require_flag = $value[$i++] ?? false,
                        'sponsor_user_name' => $require_flag = $value[$i++] ?? false,
                        'position' => ($this->MLM_PLAN == "Binary") ? ($require_flag = $value[$i++] ?? false) : "",
                        'product_id' => ($this->MODULE_STATUS['product_status'] == "yes") ? ($require_flag = $value[$i++] ?? false) : 0,
                        'first_name' => $require_flag = $value[$i++] ?? false,
                        'dob' => date('Y-m-d', strtotime($require_flag = $value[$i++] ?? false)),
                        'email' => $require_flag = $value[$i++] ?? false,
                        'mobile' => $require_flag = $value[$i++] ?? false,
                        'user_name_entry' => $require_flag = $user = $value[$i++] ?? false,
                        'pswd' => $require_flag = $value[$i++] ?? false,
                    ];
                    if($binary_leg != 'any') {
                        if($reg['sponsor_user_name'] == $this->ADMIN_USER_NAME) {
                            if($reg['position'] == $binary_leg) {
                                $msg = $reg['user_name_entry'] . ' - ' . lang('invalid_position');
                                $this->redirect($msg, 'excel_register', false);
                            }
                        } else {
                            $sponsor_id = $this->validation_model->userNameToID($reg['sponsor_user_name']);
                            if($this->tree_model->checkAncestor($exclude_ancestor,$sponsor_id)) {
                                $msg = $reg['user_name_entry'] . ' - ' . lang('invalid_position');
                                $this->redirect($msg, 'excel_register', false);
                            }
                        }
                    }
                    $reg_array[] = $reg;
                    $user_array[] = $user;
                }
                if (!$require_flag) {
                    unlink($file_name);
                    $msg = lang('please_provide_all_feields');
                    $this->redirect($msg, 'excel_register', false);
                }
                $this->validate_user_array($user_array);
                $this->validate_excel_register($reg_array);
                $product_id = 0;
                $product_name = 'NA';
                $product_pv = '0';
                $product_amount = '0';
                $product_validity = "";
                $regr = [];
                $result = false;
                foreach ($reg_array as $value) {
                    $regr = $value;
                    $regr['mlm_plan'] = $this->MLM_PLAN;
                    $regr['lang_id'] = $this->LANG_ID;
                    if ($this->MODULE_STATUS['product_status'] == "yes") {
                        $this->load->model('product_model');
                        $product_id = $this->product_model->getProdId($regr['product_id'], $this->MODULE_STATUS, "registration");
                        $product_details = $this->product_model->getProductDetails($product_id, 'yes');
                        $product_name = $product_details[0]['product_name'];
                        $product_pv = $product_details[0]['pair_value'];
                        $product_amount = $product_details[0]['product_value'];
                        if ($this->MODULE_STATUS['subscription_status'] == "yes") {
                            $product_validity = $this->product_model->calculateProductValidity($product_details[0]['subscription_period']);
                        }
                    }
                    $regr['product_status'] = $this->MODULE_STATUS['product_status'];
                    $regr['product_id'] = $product_id;
                    $regr['product_name'] = $product_name;
                    $regr['product_pv'] = $product_pv;
                    $regr['product_amount'] = $product_amount;
                    $regr['product_validity'] = $product_validity;
                    $regr['registration_fee'] = round(($this->register_model->getRegisterAmount()) * $this->DEFAULT_CURRENCY_VALUE, 8);
                    $regr['reg_amount'] = $regr['registration_fee'];
                    $regr['total_reg_amount'] = $regr['registration_fee'] + $regr['product_amount'];
                    $regr['total_amount'] = $regr['total_reg_amount'];
                    $regr['date_of_birth'] = $regr['dob'];
                    $regr['position'] = $regr['position'];
                    $regr['last_name'] = "";
                    // $regr['mobile_code'] = "+91";
                    $regr['cpswd'] = $regr['pswd'];
                    $regr['sponsor_id'] = $this->validation_model->userNameToID($regr['sponsor_user_name']);
                    $regr['placement_id'] = $regr['sponsor_id'];
                    $regr['sponsor_full_name'] = $this->validation_model->getFullName($regr['sponsor_id']);
                    $regr['placement_user_name'] = $regr['sponsor_user_name'];
                    $regr['placement_full_name'] = $regr['sponsor_full_name'];
                    $regr['payment_type'] = "free_join";
                    $regr['by_using'] = "free_join";

                    $this->register_model->begin();
                    $regr['reg_from_tree'] = false;
                    $regr['user_name_type'] = 'static';
                    $regr['joining_date'] = date('Y-m-d H:i:s');
                    $res = $this->register_model->confirmRegister($regr, $this->MODULE_STATUS);
                    if (isset($res['status']) && $res['status']) {
                        $this->register_model->commit();
                        $user_id = $res['user_id'];
                        $result = true;
                        if ($this->LOG_USER_TYPE == 'employee') {
                            $this->validation_model->insertEmployeeActivity($this->LOG_USER_ID, $this->LOG_USER_ID, 'user_register', 'User Registered', $data = '');
                        }
                    } else {
                        $this->register_model->rollback();
                        $msg = lang('error_excel_registration_of') . $regr['user_name_entry'];
                        $this->redirect($msg, 'excel_register', false);
                    }
                }
                if ($result) {
                    $msg = lang('registration_completed_successfully');
                    $this->redirect($msg, 'excel_register', true);
                } else {
                    unlink($file_name);
                    $msg = lang('error_excel_registration');
                    $this->redirect($msg, 'excel_register', false);
                }
            }
        }
        $this->setView();
    }
    function validate_excel_register($reg_array)
    {
        if (!empty($reg_array)) {
            $flag = false;
            foreach ($reg_array as $key => $value) {
                $this->form_validation->set_data($value);
                $product_status = $this->MODULE_STATUS['product_status'];
                $username_config = $this->configuration_model->getUsernameConfig();
                $user_name_type = $username_config["type"];
                $this->form_validation->set_rules('sponsor_user_name', lang('sponsor_user_name'), 'required|callback_validate_username|trim');

                if ($this->MLM_PLAN == 'Binary') {
                    $this->form_validation->set_rules('position', lang('position'), 'trim|required|in_list[L,R]|callback_position_usable[' . $value['sponsor_user_name'] . ']', ['in_list' => lang('you_must_select_your_position')]);
                }

                if ($product_status == "yes") {
                    $this->form_validation->set_rules('product_id', lang('product'), 'trim|required|callback_valid_product[registration]');
                }

                if ($user_name_type == 'static') {
                    $this->form_validation->set_rules('user_name_entry', lang('user_name_entry'), 'trim|required|alpha_numeric|min_length[6]|max_length[12]|callback_is_username_available');
                }

                $this->form_validation->set_rules('pswd', lang('password'), 'trim|required|min_length[6]|max_length[32]|callback__alpha_password');

                $this->form_validation->set_rules('first_name', lang('first_name'), 'trim|required|min_length[3]|max_length[32]|callback__alpha_space');

                $this->form_validation->set_rules('dob', lang('date_of_birth'), 'trim|required|callback_validate_age_year');
                $this->form_validation->set_rules('email', lang('email'), 'trim|required|valid_email');
                $this->form_validation->set_rules('mobile', lang('mobile_no'), 'trim|required|is_natural|min_length[5]|max_length[10]');


                $this->form_validation->set_message('exact_length', lang('the_%s_field_must_be_exactly_10_digit'));
                $this->form_validation->set_error_delimiters("<div style='color:#b94a48;'>", "</div>");
                $validation_status = $this->form_validation->run();
                if ($validation_status == true) {
                    $flag = true;
                    continue;
                } else {
                    unlink($this->upload->data('full_path'));
                    $msg = validation_errors();
                    $this->redirect($msg, "excel_register", false);
                }
            }
            return $flag;
        } else {
            unlink($this->upload->data('full_path'));
            $msg = validation_errors();
            $this->redirect($msg, "excel_register", false);
        }
    }
    function valid_product($product_id, $type)
    {
        $prod_id = $this->product_model->getProdId($product_id, $this->MODULE_STATUS, $type);
        $res = $this->product_model->isActiveProduct($prod_id, $type);
        $this->form_validation->set_message('valid_product', lang('you_must_select_product'));
        return ($res > 0);
    }

    function _alpha_password($str_in = '')
    {
        if (!preg_match("/^[0-9a-zA-Z\s\r\n@!#\$\^%&*()+=\-\[\]\\\';,\.\/\{\}\|\":<>\?\_\`\~]+$/i", $str_in)) {
            $this->form_validation->set_message('_alpha_password', lang('password_characters_allowed'));
            return false;
        } else {
            return true;
        }
    }

    public function _alpha_space($str = '')
    {
        if (!$str) {
            return true;
        }
        $res = (bool)preg_match('/^[A-Z ]*$/i', $str);
        if (!$res) {
            $this->form_validation->set_message('_alpha_space', lang('only_alpha_space'));
        }
        return $res;
    }

    public function validate_age($dob)
    {
        if (!$this->input->post('year') || $this->input->post('month') < 0 || !$this->input->post('day')) {
            return true;
        }
        $age_limit = $this->configuration_model->getAgeLimitSetting();
        if ($age_limit == 0) {
            return true;
        }
        $date1 = new DateTime($dob);
        $date1->add(new DateInterval("P{$age_limit}Y"));
        $date2 = new DateTime();
        if ($date1 <= $date2) {
            return true;
        } else {
            $this->form_validation->set_message('validate_age', sprintf(lang('You_should_be_atleast_n_years_old'), $age_limit));
            return false;
        }
    }

    public function validate_age_year($dob)
    {
        if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dob)) {
            $this->form_validation->set_message('validate_age_year', lang('date_format'));
            return false;
        }
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

    function validate_username($ref_user = '')
    {
        if ($ref_user != '') {
            $flag = false;
            if ($this->register_model->isUserAvailable($ref_user)) {
                return true;
            }
            $this->form_validation->set_message('validate_username', sprintf(lang('the_sponsor_username_n_is_not_available'), $ref_user));
            return $flag;
        } else {
            $echo = 'no';
            $username = ($this->input->post('username', true));

            if ($this->register_model->isUserAvailable($username)) {
                $echo = "yes";
            }
            echo $echo;
            exit();
        }
    }

    function check_leg_availability()
    {

        $echo = 'no';
        if ($this->input->post('sponsor_leg') && $this->input->post('sponsor_user_name')) {
            if ($this->register_model->checkLeg($this->input->post('sponsor_leg'), $this->input->post('sponsor_user_name'), $this->MLM_PLAN)) {
                $echo = "yes";
            }
        }
        echo $echo;
        exit();
    }

    function get_sponsor_full_name()
    {
        $username = ($this->input->post('sponsor_user_name', true));
        $user_id = $this->validation_model->userNameToID($username);
        $referral_name = $this->register_model->getReferralName($user_id);
        echo $referral_name;
        exit();
    }

    function get_total_registration_fee()
    {
        $product_id = $this->input->post('product_id', true);
        $product_amount = 0;
        if ($product_id) {
            $product_amount = $this->register_model->getProductAmount($product_id);
        }
        $registration_fee = $this->register_model->getRegisterAmount();
        $total_fee = $product_amount + $registration_fee;
        echo "$registration_fee==$product_amount==$total_fee";
        exit();
    }

    function get_states($country_id)
    {
        $state_select = '';

        $state_string = $this->country_state_model->viewState($country_id);
        if ($state_string != '') {
            $state_select .= "<option value =''>" . $this->lang->line('select_state') . "</option>";
            $state_select .= $state_string;
        } else {
            $state_select .= "<option value='0'>" . $this->lang->line('no_data_available') . "</option>";
        }
        $state_select .= '</select></div>';
        echo $state_select;
        exit();
    }

    function get_phone_code($country_id)
    {
        $country_telephone_code = $this->country_state_model->getCountryTelephoneCode($country_id);
        echo "+$country_telephone_code";
    }
    public function is_username_available($user_name)
    {
        if (!$user_name) {
            return false;
        }
        $is_username_exists = $this->validation_model->isUsernameExists($user_name);
        if ($is_username_exists) {
            $this->form_validation->set_message('is_username_available', sprintf(lang('the_username_n_is_not_available'), $user_name));
            return false;
        } else {
            return true;
        }
    }
    public function validate_user_array($array)
    {
        if (count(array_unique($array)) < count($array)) {
            $msg = lang('enter_unique_username');
            $this->redirect($msg, "excel_register", false);
        } else {
            return true;
        }
    }
    function position_usable($postion, $placement_user)
    {
        // print_r($postion);exit();
        $placement_id = $this->validation_model->userNameToID($placement_user);
        $position_array = ['R', 'L'];
        if (in_array($postion, $position_array) && $placement_id) {
            $psition_lang = $postion == 'R' ? lang('right') : lang('left');
            // $binary_leg_allowed = $this->tree_model->getAllowedBinaryLeg($placement_id, $this->LOG_USER_TYPE, $this->LOG_USER_ID);
            $binary_leg_allowed = 'any';
            if ($binary_leg_allowed != 'any' && $binary_leg_allowed != $postion) {
                $this->form_validation->set_message('position_usable', sprintf(lang('the_position_is_not_usable'), $psition_lang, $placement_user));
                return false;
            }
        }
        return true;
    }
}
