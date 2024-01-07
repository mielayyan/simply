<?php

class multi_language_model extends inf_model {

    public function __construct() {
        parent::__construct();
    }

    public function setDefaultLanguage($lang_id, $user_id) {
        $this->db->set('default_id', 0);
        $this->db->update('infinite_languages');

        $this->db->set('default_id', 1);
        $this->db->where('lang_id', $lang_id);
        $query = $this->db->update('infinite_languages');
        if ($query) {
            $this->updateProjectDefaultLanguage($lang_id, $user_id);
        }
        return $query;
    }

    public function updateProjectDefaultLanguage($lang_id, $user_id) {
        $this->setUserDefaultLanguage($lang_id, $user_id);

        $this->db->set('default_lang', $lang_id);
        $query1 = $this->db->update('site_information');
        $this->load->dbforge();
        $fields = array(
        'default_lang' => array(
                'type' => 'int(11)',
                'default' => $lang_id
        ),
);
        $this->dbforge->modify_column('ft_individual', $fields);
    }

    public function setUserDefaultLanguage($lang_id, $user_id) {
        $this->db->set('default_lang', $lang_id);
        $this->db->where('id', $user_id);
        if(!$this->db->update('ft_individual'))
            return false;
        if($this->MODULE_STATUS["opencart_status"] == "yes") {
            $lang_code = $this->languageIdtoCode($lang_id);
            $customer_id = $this->validation_model->getOcCustomerId($user_id);
            if($customer_id) {
                $array = $this->db->select("language_id, code")
                                ->where("lang_code", $lang_code)
                                ->get("oc_language")->result_array();
                if(count($array)) {
                    $oc_sess_data = $this->inf_model->get_store_session_data();
                    if (isset($_COOKIE['OCSESSID'])) {
                        
                        if(isset($oc_sess_data['customer_id'])) {
                            $oc_sess_data['language'] = $array[0]['code'];
                        }
                        $oc_sess_file = dirname(dirname(dirname(__DIR__))) . '/store/system/storage/session/sess_' . $_COOKIE['OCSESSID'];
                        if (is_file($oc_sess_file)) {
                            $handle = fopen($oc_sess_file, 'w');
                            flock($handle, LOCK_EX);
                            $oc_sess_data = serialize($oc_sess_data);
                            fwrite($handle, $oc_sess_data);
                            fflush($handle);
                            flock($handle, LOCK_UN);
                            fclose($handle);
                        }
                    }
                    return $this->db->set("language_id", $array[0]['language_id'])
                                    ->where("customer_id", $customer_id)
                                    ->update("oc_customer");
                }
            }
        }
        return true;
    }

    public function updateAllUserDefaultLanguage($lang_id, $new_lang_id) {
        $this->db->set('default_lang', $new_lang_id);
        $this->db->where('default_lang', $lang_id);
        $query2 = $this->db->update('ft_individual');
    }

    public function getActiveLangaugeID() {
        $lang_id = 1;
        $this->db->select('lang_id');
        $this->db->where('status', 'yes');
        $this->db->order_by('lang_id', 'ASC');
        $this->db->limit(1);
        $query = $this->db->get('infinite_languages');
        foreach ($query->result_array() AS $row) {
            $lang_id = $row['lang_id'];
        }
        return $lang_id;
    }
    public function setUserDefaultLanguageForUnapprovedUser($lang_id, $user_id){
        
        $pending_array = array('pending', 'email');
        $this->db->select('data');
        $this->db->where_in('status', $pending_array);
        $this->db->where('id', $user_id);
        $query = $this->db->get('pending_registration');
        $details = $query->row_array();
        
        $unserialized_data = json_decode($details['data'],true);
        $unserialized_data['lang_id'] = $lang_id;

        $serialized_data = json_encode($unserialized_data, true);
        
        $this->db->where('id', $user_id);
        $this->db->set('data', $serialized_data);
        $query = $this->db->update('pending_registration');

        return $query;

        
    }

    function languageIdtoCode($lang_id) {
        $lang = 0;
        $this->db->select('lang_code');
        $this->db->from('infinite_languages');
        $this->db->where("lang_id", $lang_id);
        $this->db->limit(1);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $lang = $row->lang_code;
        }
        return $lang;
    }

    function languageCodetiId($lang_code) {
        $lang = 0;
        $this->db->select('lang_id');
        $this->db->from('infinite_languages');
        $this->db->where("lang_code", $lang_code);
        $this->db->limit(1);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $lang = $row->lang_id;
        }
        return $lang;
    }

}
