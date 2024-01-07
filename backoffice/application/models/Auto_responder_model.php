<?php

class auto_responder_model extends inf_model {

    public function __construct() {
        parent::__construct();
        $this->load->model('validation_model');
    }

    public function getAutoResponderData($mail_id = '') {
        $mail_details = array();
        $this->db->select('*');
        $this->db->from('autoresponder_setting');
        if ($mail_id) {
            $this->db->where('mail_number', $mail_id);
        } else {
            $this->db->limit(1);
        }
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $mail_details = $row;
        }

        return $mail_details;
    }

    public function getAuto($limit = '', $offset = '') {
        $mail_details = array();
        $this->db->select('*');
        $this->db->from('autoresponder_setting');
        $this->db->order_by('mail_number');
        if($limit) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $mail_details[$i] = $row;
            $mail_number_encode = $this->encryption->encrypt($row['mail_number']);
            $mail_number_encode = str_replace("/", "_", $mail_number_encode);
            $mail_number_encode = urlencode($mail_number_encode);
            $mail_details[$i]['mail_number_encrypt'] = $row['mail_number'];
            $i++;
        }
        return $mail_details;
    }
    
    public function getAutoCount() {
        return $this->db->count_all_results('autoresponder_setting');
    }

    function insertIntoAutoResponder($settings) {

        if ($settings['mail_number'] != 'NA') {
            $this->db->set('subject', $settings['subject']);
            $this->db->set('content', $settings['mail_content']);
            $this->db->set('date_to_send', $settings['date_to_send']);
            $this->db->where('mail_number', $settings['mail_number']);
            $res = $this->db->update('autoresponder_setting');
        } else {

            $this->db->set('subject', $settings['subject']);
            $this->db->set('content', $settings['mail_content']);
            $this->db->set('date_to_send', $settings['date_to_send']);
            $res = $this->db->insert('autoresponder_setting');
        }
        return $res;
    }

    public function DeleteAutoResponderData($mail_id) {
        $this->db->where('mail_number', $mail_id);
        return $this->db->delete('autoresponder_setting');
    }

    public function getCurrentmailDate($mail_id) {
        $mail_date = 0;
        $this->db->select('date_to_send');
        $this->db->from('autoresponder_setting');
        $this->db->where('mail_number', $mail_id);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $mail_date = $row->date_to_send;
        }
        return $mail_date;
    }
    
      public function getVisitordetails() {
        $result = NULL;
        $this->db->select("*");
        $this->db->from("crm_leads");
        $this->db->where('lead_status', 'Ongoing');
        $res = $this->db->get();
        $i = 0;
        foreach ($res->result_array() as $row) {
            $result[$i]['name'] = $row['first_name']." ".$row['last_name'];
            $result[$i]['id'] = $row['id'];
            $result[$i]['email'] = $row['email_id'];
            $result[$i]['user_id'] = $row['added_by'];
            $date = strtotime($row['date']);
            $newformat = date('d-m-Y', $date);
            $result[$i]['date'] = $newformat;
            $i++;
        }

        return $result;
    }

}
