<?php

require_once 'Inf_Controller.php';

class multi_language extends Inf_Controller {

    function __construct() {
        parent::__construct();
    }

    /*public function language_management() {
        $title = lang('multi_lang_settings');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('multi_lang_settings');
        $this->HEADER_LANG['page_top_small_header'] = lang('');
        $this->HEADER_LANG['page_header'] = lang('multi_lang_settings');
        $this->HEADER_LANG['page_small_header'] = lang('');

        $this->load_langauge_scripts();

        $help_link = "Language Management";
        $this->set("help_link", $help_link);

        if ($this->input->post('update')) {
            if ($this->validate_language_management()) {
                $det = $this->input->post(NULL, TRUE);
                $det = $this->validation_model->stripTagsPostArray($det);
                $res = $this->multi_language_model->insertLanguageDetails($det);
                if ($res) {
                    $msg = lang('language_added');
                    $this->redirect($msg, 'multi_language/language_management', TRUE);
                } else {
                    $msg = lang('Unable_to_add_language');
                    $this->redirect($msg, 'multi_language/language_management', TRUE);
                }
            }
        }

        $language_details = $this->multi_language_model->getLanguageDetails();
        $this->set('language_details', $language_details);

        $this->setView();
    }

    public function edit_language($lang_id = '') {
        $title = 'Edit Language Details';
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = 'Edit Language Details';
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = '';
        $this->HEADER_LANG['page_small_header'] = 'Edit Language Details';

        $this->load_langauge_scripts();

        $help_link = 'Edit Language Details';
        $this->set("help_link", $help_link);

        $lang_details = $this->multi_language_model->getLangDetailsById($lang_id);

        $this->set('lang_details', $lang_details);
        if ($this->input->post('update')) {
            if ($this->validate_language_management()) {
                $det = $this->input->post(NULL, TRUE);
                $det = $this->validation_model->stripTagsPostArray($det);
                $res = $this->multi_language_model->updateLangDetails($det);
                if ($res) {
                    $data = serialize($det);
                    $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'language edited', $this->LOG_USER_ID, $data);
                    $msg = lang('language_updated');
                    $this->redirect($msg, 'multi_language/language_management', TRUE);
                } else {
                    $msg = lang('Unable_to_update_language');
                    $this->redirect($msg, 'multi_language/language_management', FALSE);
                }
            }
        }
        $this->setView();
    }*/

    public function set_default_language() {
        if($this->CACHE_STATUS) {
            $this->redis->del('languagesArray');
            $this->redis->del('defaultLang');
        }
        
        $lang_id = $this->input->post('lang_id', TRUE);
        $user_id = $this->LOG_USER_ID;
        $res = $this->multi_language_model->setDefaultLanguage($lang_id, $user_id);
        if ($res) {
            $data_array['lang_id'] = $lang_id;
            $data = serialize($data_array);
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'default language updated', $this->LOG_USER_ID, $data);
            $msg = lang('default_language_updated');
            $this->redirect($msg, 'configuration/language_settings', TRUE);
        } else {
            $msg = lang('unable_to_update_default_language');
            $this->redirect($msg, 'configuration/language_settings', false);
        }
    }

    /*function validate_language_management() {

        $this->form_validation->set_rules('lang_code', lang('lang_code'), 'required');
        $this->form_validation->set_rules('lang_name', lang('lang_name'), 'required');
        $this->form_validation->set_rules('lang_name_in_english', lang('lang_name_in_english'), 'required');
        $this->form_validation->set_rules('status', lang('status'), 'required');
        $res_val = $this->form_validation->run();
        return $res_val;
    }

    function delete($delete_id = '') {
        $result = $this->multi_language_model->deleteLanguage($delete_id);
        if ($result) {
            $data_array['lang_id'] = $delete_id;
            $data = serialize($data_array);
            $this->validation_model->insertUserActivity($this->LOG_USER_ID, 'language deleted', $this->LOG_USER_ID, $data);
            $msg = lang('language_deleted');
            $this->redirect($msg, 'multi_language/language_management', TRUE);
        } else {
            $msg = lang('Unable_to_delete_language');
            $this->redirect($msg, 'multi_language/language_management', FALSE);
        }
    }*/

}
