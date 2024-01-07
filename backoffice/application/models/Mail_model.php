<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mail
 *
 * @author pavanan
 */
class mail_model extends inf_model
{

    public $MEMBER_DETAILS;
    public $user_downlines;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('validation_model');
        $this->load->model('configuration_model');
    }

    public function getUsers()
    {

        $user_arr = array();
        $this->db->select('id');
        $this->db->from('ft_individual');
        $this->db->where('user_type !=', 'admin');
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $user_arr[] = $row->id;
        }
        return $user_arr;
    }

    public function userNameToId($user_name)
    {

        return $this->validation_model->userNameToID($user_name);
    }

    public function sendMessageToUser($user_id, $subject, $message, $dt, $from_user = 'admin', $thread = '')
    {
        // THIS_VERSION_ONLY
        // $thread = '';
        if ($thread == '') {
            $thread = $this->selectMaxThreadNumber() + 1;
        } else {
            $thread = $thread;
        }
        $data = array(
            'mailtoususer' => $user_id,
            'mailfromuser' => $from_user,
            'mailtoussub'  => $subject,
            'mailtousmsg'  => $message,
            'mailtousdate' => $dt,
            'thread'       => $thread,
            //'thread_from' => $from_user,
        );
        $res = $this->db->insert('mailtouser', $data);
        return $res;
    }

    public function sendMessageToUserCumulative($user_id, $subject, $message, $dt, $type)
    {
        $data = array(
            'mailtoususer' => $user_id,
            'mailtoussub' => $subject,
            'mailtousmsg' => $message,
            'mailtousdate' => $dt,
            'type' => $type
        );
        $res = $this->db->insert('mailtouser_cumulativ', $data);
        return $res;
    }

    public function sendMesageToAdmin($from, $message, $subject, $dt, $table_prefix = '', $thread = '')
    {

        // THIS_VERSION_ONLY
        // $thread = '';
        if ($thread == '') {
            $thread = $this->selectMaxThreadNumber() + 1;
        } else {
            $thread = $thread;
        }
        $data = array(
            'mailaduser' => $from,
            'mailadsubject' => $subject,
            'mailadidmsg' => $message,
            'status' => 'yes',
            'mailadiddate' => $dt,
            'thread' => $thread,
            //'thread_from' => $from

        );
        $res = $this->db->insert($table_prefix . 'mailtoadmin', $data);
        return $res;
    }

    public function getAdminMessages($page, $limit)
    {
        $message = [];
        $this->db->select('m.mailadid,m.mailaduser,f.user_name,m.mailadsubject,m.mailadiddate,m.status,m.mailadidmsg,m.read_msg,m.thread,u.user_detail_refid,u.user_detail_name,u.user_detail_second_name ');
        $this->db->from('mailtoadmin as m');
        $this->db->join('user_details as u','m.mailaduser = u.user_detail_refid');
        $this->db->join('ft_individual f','f.id = u.user_detail_refid');
        $this->db->where('status', 'yes');
        $where = array('m.status' => 'no', 'm.deleted_by != ' => $this->LOG_USER_ID, 'm.deleted_by !=' => 'both');
        $this->db->or_group_start()
            ->where($where);
        $this->db->group_end();
        $this->db->group_by('m.thread');
        $this->db->order_by('mailadiddate', 'desc');
        $this->db->limit($limit, $page);
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $message[$i]['id'] = $row['mailadid'];
            $message[$i]['mailaduser'] = $row['mailaduser'];
            $message[$i]['mailadsubject'] = $row['mailadsubject'];
            $message[$i]['mailadiddate'] = $row['mailadiddate'];
            $message[$i]['status'] = $row['status'];
            $message[$i]['mailadidmsg'] = stripslashes($row['mailadidmsg']);
            $message[$i]['read_msg'] = $row['read_msg'];
            $message[$i]['type'] = "admin";
            $message[$i]['flag'] = 1;
            $message[$i]['thread'] = $row['thread'];
            $message[$i]['fullname'] = $row['user_detail_name']." ".$row['user_detail_second_name'];
            $message[$i]['user_name'] = $row['user_name'];

            $i++;
        }
        return $message;
    }

    public function getContactMessages($page, $limit, $logged_id)
    {
        $message = array();
        $this->db->select('*');
        $this->db->from('contacts');
        $this->db->where('owner_id', $logged_id);
        $this->db->where('status', 'yes');
        $this->db->order_by('mailadiddate', 'desc');
        $this->db->limit($limit, $page);
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $message[$i]['id'] = $row['id'];
            $message[$i]['mailaduser'] = $row['contact_name'];
            $message[$i]['mailadsubject'] = $row['contact_name'] . " Contacted You";
            $message[$i]['mailadiddate'] = $row['mailadiddate'];
            $message[$i]['status'] = $row['status'];
            if ($row['contact_info'] == '') {
                $message[$i]['mailadidmsg'] = "Name:" . $row['contact_name'] . "<br>Email:" . $row['contact_email'] . "<br>Address:" . $row['contact_address'] . "<br>Phone:" . $row['contact_phone'] . "<br>Describtion:NA";
            } else {
                $message[$i]['mailadidmsg'] = "Name:" . $row['contact_name'] . "<br>Email:" . $row['contact_email'] . "<br>Address:" . $row['contact_address'] . "<br>Phone:" . $row['contact_phone'] . "<br>Describtion:" . $row['contact_info'];
            }
            $message[$i]['read_msg'] = $row['read_msg'];
            $message[$i]['fullname'] = $row['contact_name'];
            $message[$i]['type'] = "contact";
            $message[$i]['flag'] = '';
            $message[$i]['user_name'] = $row['contact_name'];
            $i++;
        }
        return $message;
    }

    public function getAdminMessagesSent($page, $limit)
    {
        $user_id = $this->LOG_USER_ID;
        $message = array();
        $this->db->select('m.mailtousid,m.mailtoususer,m.mailtoussub,m.mailtousdate,m.status,m.mailtousmsg,m.mailtoususer,m.thread,f.user_name,u.user_detail_refid,u.user_detail_name,u.user_detail_second_name' );
        $this->db->from('mailtouser as m');
        $this->db->join('user_details as u','m.mailtoususer = u.user_detail_refid');
        $this->db->join('ft_individual f','f.id = u.user_detail_refid');
        $where1 = array('status' => 'yes', 'mailfromuser' => $user_id);
        $where2 = array('status' => 'no', 'deleted_by != ' => $user_id, 'mailfromuser' => $user_id, 'deleted_by !=' => 'both');
        $this->db->group_start()
            ->where($where1)
            ->or_group_start()
            ->where($where2)
            ->group_end()
            ->group_end();
        $this->db->group_by('thread');
        $this->db->order_by('mailtousdate', 'desc');
        $this->db->limit($limit, $page);
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $row = $this->validation_model->stripSlashResultArray($row);
            $message[$i]['id'] = $row['mailtousid'];
            $message[$i]['mailtoususer'] = $row['mailtoususer'];
            $message[$i]['mailtoussub'] = $row['mailtoussub'];
            $message[$i]['mailtousdate'] = $row['mailtousdate'];
            $message[$i]['status'] = $row['status'];
            $message[$i]['type'] = "user";
            $message[$i]['mailtousmsg'] = html_entity_decode($row['mailtousmsg']);
            $message[$i]['fullname'] = $row['user_detail_name']." ".$row['user_detail_second_name'];
            $message[$i]['user_name'] = $row['user_name'];
            $message[$i]['thread'] = $row['thread'];

            $i++;
        }
        $this->db->select('*');
        $this->db->from('mailtouser_cumulativ');
        $this->db->where('type', 'ext_mail');
        $this->db->where('status', 'yes');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $row = $this->validation_model->stripSlashResultArray($row);
            $message[$i]['id'] = $row['mailtousid'];
            $message[$i]['mailtoususer'] = $row['mailtoususer'];
            $message[$i]['mailtoussub'] = $row['mailtoussub'];
            $message[$i]['mailtousdate'] = $row['mailtousdate'];
            $message[$i]['status'] = $row['status'];
            $message[$i]['type'] = $row['type'];
            $message[$i]['mailtousmsg'] = html_entity_decode($row['mailtousmsg']);
            $message[$i]['user_name'] = $row['mailtoususer'];

            $i++;
        }
        function cmp($a, $b)
        {
            $a["date"] = strtotime($a["mailtousdate"]);
            $b["date"] = strtotime($b["mailtousdate"]);
            return ($a["date"] > $b["date"]) ? 1 : -1;
        }

        usort($message, "cmp");
        return $message;
    }

    public function getCountAdminMessages()
    {
        $this->db->select('*');
        $this->db->from('mailtoadmin');
        $this->db->where('status', 'yes');
        $count = $this->db->count_all_results();
        return $count;
    }

    public function getCountContactMessages($user_id)
    {
        $this->db->select('*');
        $this->db->from('contacts');
        $this->db->where('owner_id', $user_id);
        $this->db->where('status', 'yes');

        $count = $this->db->count_all_results();
        return $count;
    }

    public function getCountAdminMessagesSent($user_id)
    {

        $this->db->select('*');
        $this->db->from('mailtouser');
        $where1 = array('status' => 'yes', 'mailfromuser' => $user_id);
        $where2 = array('status' => 'no', 'deleted_by != ' => $user_id, 'mailfromuser' => $user_id, 'deleted_by !=' => 'both');
        $this->db->group_start()
            ->where($where1)
            ->or_group_start()
            ->where($where2)
            ->group_end()
            ->group_end();
        $this->db->group_by('thread');

        $count = $this->db->count_all_results();
        return $count;
    }

    public function getCountUserMessages($user_id)
    {

        $this->db->select('*');
        $this->db->from('mailtouser');
        $this->db->where('status', 'yes');
        $this->db->where('mailtoususer', $user_id);
        $count = $this->db->count_all_results();
        return $count;
    }

    public function getAdminOneMessage($id)
    {
        $this->db->select('*');
        $this->db->from('mailtoadmin');
        $this->db->where('mailadid', $id);
        //$this->db->where('status', 'yes');
        $res = $this->db->get();
        return $res;
    }

    public function updateAdminOneMessage($msg_id)
    {
        $data = array(
            'read_msg' => 'yes',
        );
        $this->db->where('mailadid', $msg_id);
        $this->db->where('status', 'yes');
        $this->db->update('mailtoadmin', $data);
    }

    public function updateUserOneMessage($msg_id, $this_prefix = '')
    {
        $data = array(
            'read_msg' => 'yes',
        );
        $this->db->where('mailtousid', $msg_id);
        $this->db->where('status', 'yes');
        $this->db->update($this_prefix . 'mailtouser', $data);
    }

    public function getUserOneMessage($id, $user_id)
    {
        $this->db->select('*');
        $this->db->from('mailtouser');
        $this->db->where('mailtousid', $id);
        $this->db->where('mailtoususer', $user_id);
        //$this->db->where('status', 'yes');
        $res = $this->db->get();
        return $res;
    }

    public function getUserMessages($user_id, $page, $limit = '', $table_prefix = '')
    {
        $message = array();

        $this->db->select('m.mailfromuser,m.mailtousdate,m.thread,m.mailtousid,m.mailtoususer,m.mailtoussub,m.mailtousmsg,m.mailtousdate,m.status,m.read_msg,f.user_name,u.user_detail_refid,u.user_detail_name,u.user_detail_second_name');
        $this->db->from($table_prefix . 'mailtouser as m');

        $this->db->join('user_details as u','m.mailfromuser = u.user_detail_refid');
        $this->db->join('ft_individual f','f.id = u.user_detail_refid');
        $where1 = array('status' => 'yes', 'mailtoususer' => $user_id);
        $where2 = array('status' => 'no', 'deleted_by != ' => $user_id, 'mailtoususer' => $user_id, 'deleted_by !=' => 'both');
        $this->db->group_start()
            ->where($where1)
            ->or_group_start()
            ->where($where2)
            ->group_end()
            ->group_end();
        $this->db->group_by('m.thread');
        $this->db->order_by('mailtousdate', 'desc');
        if ($limit != '') {
            $this->db->limit($limit, $page);
        }
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $row = $this->validation_model->stripSlashResultArray($row);
            $message[$i]['mailtousid'] = $row['mailtousid'];
            $message[$i]['mailtoususer'] = $row['mailtoususer'];
            $message[$i]['mailtoussub'] = $row['mailtoussub'];
            $message[$i]['mailtousmsg'] = $row['mailtousmsg'];
            $message[$i]['mailtousdate'] = $row['mailtousdate'];
            $message[$i]['status'] = $row['status'];
            $message[$i]['read_msg'] = $row['read_msg'];
            $message[$i]['type'] = "user";
            $message[$i]['flag'] = 1;
            $message[$i]['fullname'] = $row['user_detail_name']." ".$row['user_detail_second_name'];
            $message[$i]['user_name'] = $row['user_name'];
            if ($row['mailfromuser'] != 'admin') {
                $message[$i]['from_user_name'] = $row['user_name'];
            } else {
                $message[$i]['from_user_name'] = $this->ADMIN_USER_NAME;
            }
            $message[$i]['thread'] = $row['thread'];
            $i++;
        }
        return $message;
    }

    public function getUserContactMessages($user_id, $page, $limit = '', $table_prefix = '')
    {
        $message = array();
        $this->db->select('*');
        $this->db->from('contacts');
        $this->db->where('owner_id', $user_id);
        $this->db->where('status', 'yes');
        $this->db->order_by('mailadiddate', 'desc');
        $this->db->limit($limit, $page);
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $message[$i]['mailtousid'] = $row['id'];
            $message[$i]['mailtoususer'] = $row['contact_name'];
            $message[$i]['mailtoussub'] = $row['contact_name'] . " Contacted You";
            if ($row['contact_info'] == '') {
                $message[$i]['mailtousmsg'] = "Name:" . $row['contact_name'] . "<br>Email:" . $row['contact_email'] . "<br>Address:" . $row['contact_address'] . "<br>Phone:" . $row['contact_phone']
                    . "<br>Describtion:NA";
            } else {
                $message[$i]['mailtousmsg'] = "Name:" . $row['contact_name'] . "<br>Email:" . $row['contact_email'] . "<br>Address:" . $row['contact_address'] . "<br>Phone:" . $row['contact_phone']
                    . "<br>Describtion:" . $row['contact_info'];
            }
            $message[$i]['mailtousdate'] = $row['mailadiddate'];
            $message[$i]['status'] = $row['status'];
            $message[$i]['read_msg'] = $row['read_msg'];
            $message[$i]['type'] = "contact";
            $message[$i]['flag'] = '';
            $message[$i]['user_name'] = $row['contact_name'] . " Contacted You";
            $message[$i]['from_user_name'] = $row['contact_name'];
            $message[$i]['fullname'] = $row['contact_name'];
            $i++;
        }
        return $message;
    }

    public function getCountUserContactMessages($user_id)
    {

        $this->db->select('*');
        $this->db->from('contacts');
        $this->db->where('status', 'yes');
        $this->db->where('owner_id', $user_id);
        $count = $this->db->count_all_results();
        return $count;
    }

    public function getUserMessagesSent($user_id, $page, $limit = '', $table_prefix = '')
    {
        $mails = array();
        $this->db->select('*');
        $this->db->from('mailtoadmin');
        $where1 = array('status' => 'yes', 'mailaduser' => $user_id);
        $where2 = array('status' => 'no', 'deleted_by != ' => $user_id, 'mailaduser' => $user_id, 'deleted_by !=' => 'both');
        $this->db->group_start()
            ->where($where1)
            ->or_group_start()
            ->where($where2)
            ->group_end()
            ->group_end();
        $this->db->group_by('thread');
        $this->db->order_by('mailadiddate', 'desc');
        if ($limit != '') {
            $this->db->limit($limit, $page);
        }
        $res = $this->db->get();
        foreach ($res->result_array() as $row) {
            $row['mailadidmsg'] = stripslashes($row['mailadidmsg']);
            $row['user_name'] = $this->validation_model->getAdminUsername();
            $row['type'] = 'to_admin';
            $row["fullname"] = $this->validation_model->getFullName($this->validation_model->getAdminId());

            $mails[] = $row;
        }
        $this->db->select('*');
        $this->db->from('mailtouser');
        $where1 = array('status' => 'yes', 'mailfromuser' => $user_id);
        $where2 = array('status' => 'no', 'deleted_by != ' => $user_id, 'mailfromuser' => $user_id, 'deleted_by !=' => 'both');
        $this->db->group_start()
            ->where($where1)
            ->or_group_start()
            ->where($where2)
            ->group_end()
            ->group_end();
        $this->db->group_by('thread');
        $this->db->order_by('mailtousdate', 'desc');
        if ($limit != '') {
            $this->db->limit($limit, $page);
        }
        $res = $this->db->get();
        foreach ($res->result_array() as $row) {
            $row['mailadidmsg'] = html_entity_decode($row['mailtousmsg']);
            $row['user_name'] = $this->validation_model->idToUserName($row['mailtoususer']);
            $row["fullname"] = $this->validation_model->getFullName($row['mailtoususer']);
            $row['mailadid'] = $row['mailtousid'];
            $row['mailadmsg'] = $row['mailtousmsg'];
            $row['mailadsubject'] = $row['mailtoussub'];
            $row['mailadiddate'] = $row['mailtousdate'];
            $row['type'] = 'to_user';
            $mails[] = $row;
        }
        $this->db->select('*');
        $this->db->from('mailtouser_cumulativ');
        $this->db->where('type', 'ext_mail_user');
        $this->db->where('status', 'yes');
        $this->db->order_by('mailtousdate', 'desc');
        if ($limit != '') {
            $this->db->limit($limit, $page);
        }
        $res = $this->db->get();
        foreach ($res->result_array() as $row) {
            $row['mailadidmsg'] = html_entity_decode($row['mailtousmsg']);
            $row['user_name'] = $row['mailtoususer'];
            $row['mailadid'] = $row['mailtousid'];
            $row["fullname"] = $this->validation_model->getFullName($row['mailtousid']);
            $row['mailadmsg'] = $row['mailtousmsg'];
            $row['mailadsubject'] = $row['mailtoussub'];
            $row['mailadiddate'] = $row['mailtousdate'];
            $row['type'] = 'ext_mail_user';
            $mails[] = $row;
        }

        return $mails;
    }

    public function updateAdminMessage($msg_id, $flag = '')
    {
        $this->db->select('deleted_by');
        $this->db->from('mailtoadmin');
        if ($flag != '') {
            $this->db->where('thread', $msg_id);
        } else {
            $this->db->where('mailadid', $msg_id);
        }
        $query = $this->db->get();
        if(!$query->row_array() || !count($query->row_array())) {
            return true;
        }
        $result = $query->row_array()['deleted_by'];
        if ($result == 0 || $result == $this->LOG_USER_ID) {
            $data = array(
                'status' => 'no',
                'deleted_by' => $this->LOG_USER_ID
            );
        } else {
            $data = array(
                'status' => 'no',
                'deleted_by' => 'both'
            );
        }
        if ($flag != '') {
            $this->db->where('thread', $msg_id);
        } else {
            $this->db->where('mailadid', $msg_id);
        }
        $res = $this->db->update('mailtoadmin', $data);
        return $res;
    }

    public function updateContactMessage($msg_id)
    {
        $data = array(
            'status' => 'no'
        );
        $this->db->where('id', $msg_id);
        $res = $this->db->update('contacts', $data);
        return $res;
    }

    public function updateAdminSentMessage($msg_id)
    {
        $data = array(
            'status' => 'no'
        );
        $this->db->where('mailtousid', $msg_id);
        $res = $this->db->update('mailtouser_cumulativ', $data);
        return $res;
    }

    public function updateUserMessage($msg_id, $flag = '')
    {
        $this->db->select('deleted_by');
        $this->db->from('mailtouser');
        if ($flag != '') {
            $this->db->where('thread', $msg_id);
        } else {
            $this->db->where('mailtousid', $msg_id);
        }
        $query = $this->db->get();
        if(!$query->row_array() || !count($query->row_array())) {
            
        }
        $result = $query->row_array()['deleted_by'] ?? 0;

        if ($result == 0 || $result == $this->LOG_USER_ID) {
            $data = array(
                'status' => 'no',
                'deleted_by' => $this->LOG_USER_ID
            );
        } else {
            $data = array(
                'status' => 'no',
                'deleted_by' => 'both'
            );
        }
        if ($flag != '') {
            $this->db->where('thread', $msg_id);
        } else {
            $this->db->where('mailtousid', $msg_id);
        }
        $res = $this->db->update('mailtouser', $data);
        return $res;
    }

    public function updateDownlineSendMessage($msg_id)
    {
        $data = array(
            'status' => 'deleted'
        );
        $this->db->where('mail_id', $msg_id);
        $res = $this->db->update('mail_from_lead_cumulative', $data);
        return $res;
    }

    public function updateDownlineFromMessage($msg_id)
    {

        $data = array(
            'status' => 'deleted'
        );
        $this->db->where('mail_id', $msg_id);
        $res = $this->db->update('mail_from_lead', $data);
        return $res;
    }

    public function updateuserContactMessage($msg_id)
    {
        $data = array(
            'status' => 'no'
        );
        $this->db->where('id', $msg_id);
        $res = $this->db->update('contacts', $data);
        return $res;
    }

    public function updateUserMessageSent($msg_id, $type = '')
    {
        if ($type == 'to_user' || $type == 'user') {
            $this->db->select('deleted_by');
            $this->db->from('mailtouser');
            $this->db->where('thread', $msg_id);
            $query = $this->db->get();
            $result = $query->row_array()['deleted_by'];
            if ($result == 0 || $result == $this->LOG_USER_ID) {
                $data = array(
                    'status' => 'no',
                    'deleted_by' => $this->LOG_USER_ID
                );
            } else {
                $data = array(
                    'status' => 'no',
                    'deleted_by' => 'both'
                );
            }
            $this->db->where('thread', $msg_id);
            $res = $this->db->update('mailtouser', $data);
        } elseif ($type == 'to_admin') {
            $this->db->select('deleted_by');
            $this->db->from('mailtoadmin');
            $this->db->where('thread', $msg_id);
            $query = $this->db->get();
            $result = $query->row_array()['deleted_by'];
            if ($result == 0 || $result == $this->LOG_USER_ID) {
                $data2 = array(
                    'status' => 'no',
                    'deleted_by' => $this->LOG_USER_ID
                );
            } else {
                $data2 = array(
                    'status' => 'no',
                    'deleted_by' => 'both'
                );
            }
            $this->db->where('thread', $msg_id);
            $res = $this->db->update('mailtoadmin', $data2);
        }
        return $res;
    }

    public function updateMsgStatus($msg_id, $thread = '')
    {
        $count = "";
        $user_name = $this->LOG_USER_NAME;
        $user_type = $this->LOG_USER_TYPE;
        $user_id = $this->LOG_USER_ID;
        $reslt_admin_read = "";
        $reslt_user_read = "";
        if ($user_type == 'admin' || $user_type == "employee") {
            $data = array(
                'read_msg' => 'yes'
            );

            if ($thread != '') {
                $this->db->where('thread', $thread);
            } else {
                $this->db->where('mailadid', $msg_id);
            }

            $reslt_admin_read = $this->db->update('mailtoadmin', $data);
        } else {
            $data = array(
                'read_msg' => 'yes'
            );
            if ($thread != '') {
                $this->db->where('thread', $thread);
            } else {
                $this->db->where('mailtousid', $msg_id);
            }
            $reslt_user_read = $this->db->update('mailtouser', $data);
        }
        if ($reslt_admin_read) {
            $this->db->select('mailaduser');
            $this->db->where('read_msg', 'no');
            $this->db->from('mailtoadmin');
            $count = $this->db->count_all_results();
            return $count;
        }
        if ($reslt_user_read) {
            $this->db->select('mailtoususer');
            $this->db->where('read_msg', 'no');
            $this->db->where('mailtoususer', $user_id);
            $this->db->from('mailtouser');
            $count = $this->db->count_all_results();
            return $count;
        }
    }

    public function getAdminId()
    {
        return $this->validation_model->getAdminId();
    }

    public function getEmailId($user_id, $mailBodyDetails = '', $subject = '')
    {
        if ($this->table_prefix == "") {
            $this->table_prefix = $_SESSION['table_prefix'];
        }
        $user_details = $this->table_prefix . "user_details";
        $dbprefix = $this->db->dbprefix;
        $this->db->set_dbprefix('');
        $this->db->select('user_detail_email');
        $this->db->where('user_detail_refid', $user_id);
        $query = $this->db->get($user_details);
        $this->db->set_dbprefix($dbprefix);

        $email = $query->row_array()['user_detail_email'];
        if ($email) {
            $this->sendEmail($mailBodyDetails, $email, $subject);
        }
    }

    public function sendEmail($mailBodyDetails, $email, $mail_subject = '', $attachments = array())
    {

        //$attachments = array(BASEPATH . "../public_html/images/logos/logo.png");

        $this->load->library('inf_phpmailer', null, 'phpmailer');

        $site_info = $this->validation_model->getSiteInformation();
        $common_mail_settings = $this->configuration_model->getMailDetails();

        //$mail_type = $common_mail_settings['reg_mail_type']; //normal/smtp
        $mail_type = 'normal'; //normal/smtp
        $smtp_data = array();
        if ($mail_type == "smtp") {
            $smtp_data = array(
                "SMTPAuth" => $common_mail_settings['smtp_authentication'],
                "SMTPSecure" => ($common_mail_settings['smtp_protocol'] == "none") ? "" : $common_mail_settings['smtp_protocol'],
                "Host" =>$common_mail_settings['smtp_host'],
                "Port" => $common_mail_settings['smtp_port'],
                "Username" =>$common_mail_settings['smtp_username'],
                "Password" =>$common_mail_settings['smtp_password'],
                "Timeout" => $common_mail_settings['smtp_timeout'],
                    //"SMTPDebug" => 3 //uncomment this line to check for any errors
            );
        }
        $mail_to = array("email" => $email, "name" => $email);
        $mail_from = array("email" => $site_info['email'], "name" => $site_info['company_name']);
        $mail_reply_to = $mail_from;

        $send_mail = $this->phpmailer->send_mail($mail_from, $mail_to, $mail_reply_to, $mail_subject, $mailBodyDetails, $mailBodyDetails, $mail_type, $smtp_data, $attachments);

        if (!$send_mail['status']) {
            $data["message"] = "Error: " . $send_mail['ErrorInfo'];
        } else {
            $data["message"] = "Message sent correctly!";
        }

        return $send_mail;
    }

    /////////////////______________________________|\


    public function getAllReadMessages($type)
    {
        $inf_sess = $this->session->userdata('inf_logged_in');
        $user_name = $inf_sess['user_name'];
        $id = $this->userNameToId($user_name);
        if ($type == "admin") {
            $mail = 'mailtoadmin';
            $this->db->select('mailadid');
            $this->db->from($mail);
            $this->db->where('status', 'yes');
            $this->db->where('read_msg', 'yes');
        } else if ($type == "user") {
            $mail = 'mailtouser';
            $this->db->select('mailtousid');
            $this->db->from($mail);
            $this->db->where('mailtoususer', $id);
            $this->db->where('status', 'yes');
            $this->db->where('read_msg', 'yes');
        }
        $numrows = $this->db->count_all_results(); // Number of rows returned from above query.
        return $numrows;
    }

    public function getAllUnreadMessages($type)
    {
        $inf_sess = $this->session->userdata('inf_logged_in');
        $user_name = $inf_sess['user_name'];
        $id = $this->userNameToId($user_name);

        if ($type == "admin") {
            $mail = 'mailtoadmin';
            $this->db->select('mailadid');
            $this->db->where('status', 'yes');
            $this->db->where('read_msg', 'no');
            $this->db->from($mail);
        } else {
            $mail = 'mailtouser';
            $this->db->select('mailtousid');
            $this->db->where('mailtoususer', $id);
            $this->db->where('status', 'yes');
            $this->db->where('read_msg', 'no');
            $this->db->from($mail);
        }
        $numrows = $this->db->count_all_results(); // Number of rows returned from above query.
        return $numrows;
    }

    public function getCountUserUnreadMessages($type, $id)
    {

        $this->db->select('*');
        $this->db->where('status', 'yes');
        $this->db->where('read_msg', 'no');
        $this->db->where('mailtoususer', $id);
        $this->db->from('mailtouser');

        $count = $this->db->count_all_results();
        return $count;
    }

    public function getUnreadMessages($type)
    {

        $this->db->select('*');
        $this->db->from('mailtouser');
        $this->db->where('status', 'yes');
        $this->db->where('read_msg', 'no');
        $this->db->where('mailtoususer', $type);
        $count = $this->db->count_all_results();

        return $count;
    }

    public function getAllMessagesToday($type)
    {
        $count = 0;
        $date = date("Y-m-d");

        if ($type == "admin") {
            $mail = 'mailtoadmin';
            $this->db->select('mailadid');
            $this->db->from($mail);
            $this->db->where('status', 'yes');
            $this->db->like('mailadiddate', $date);
        } else if ($type == "user") {
            $inf_sess = $this->session->userdata('inf_logged_in');
            $user_name = $inf_sess['user_name'];
            $id = $this->userNameToId($user_name);
            $mail = 'mailtouser';
            $this->db->select('mailtousid');
            $this->db->from($mail);
            $this->db->where('status', 'yes');
            $this->db->where('mailtoususer', $id);
            $this->db->like('mailtousdate', $date);
        }
        $query = $this->db->get();
        $numrows = $query->num_rows(); // Number of rows returned from above query.
        return $numrows;
    }

    public function getAdminUsername()
    {

        $this->db->select('user_name');
        $this->db->from('ft_individual');
        $this->db->where('user_type', 'admin');
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $user_name = $row->user_name;
        }
        return $user_name;
    }

    function getUserDownlinesAll($user_id)
    {
        $arr1[] = $user_id;
        $this->referals = null;
        //$limit = $this->getDepthCeiling();
        $i = 0;
        $level_arr = $this->getAllReferrals($arr1, $i);

        return $level_arr;
    }

    public function getAllReferrals($user_id_arr, $i)
    {
        $temp_user_id_arr = array();
        $temp = 0;
        if (count($user_id_arr)) {
            $qr = $this->createQuery($user_id_arr);
            $res = $this->db->query("$qr");
            foreach ($res->result_array() as $row) {
                $user_array = array(
                    "user_id" => $row['id'],
                    //"customer_id" => $row['oc_customer_ref_id'],
                    "user_name" => $row['user_name'],
                );
                $this->user_downlines[$i][] = $user_array;
                $temp_user_id_arr[] = $row['id'];
                $temp = $row['id'];
            }
        }
        $i = $i + 1;

        if ($temp) {
            $this->getAllReferrals($temp_user_id_arr, $i);
        }

        return $this->user_downlines;
    }

    public function createQuery($user_id_arr)
    {
        $this->load->database();
        $db_prefix = $this->db->dbprefix;
        $ft_individual = $db_prefix . "ft_individual";
        $arr_len = count($user_id_arr);
        $where_qr = '';
        for ($i = 0; $i < $arr_len; $i++) {
            $user_id = $user_id_arr[$i];
            if ($i == 0) {
                $where_qr = "father_id = '$user_id'";
            } else {
                $where_qr .= " OR father_id = '$user_id'";
            }
        }
        $qr = "Select id, user_name from $ft_individual where ($where_qr)";

        return $qr;
    }

    function sendMessageToAllDownlines($mailBodyDetails, $from_user_id, $user_downlines, $subject)
    {
        foreach ($user_downlines as $levels) {
            foreach ($levels as $users) {
                $to_user_id = $users['user_id'];
                //$user_name = $this->validation_model->IdToUserName($to_user_id);
                //$email = $this->getUserEmail($user_name);
                //$this->sendMailUser($email, $mailBodyDetails, $subject);
                $this->sendMessageToDownlines($subject, $from_user_id, $to_user_id, $mailBodyDetails);
            }
        }
        return true;
    }

    public function sendMessageToDownlines($subject, $from_id, $to_id, $message)
    {
        $date = date("Y-m-d H:i:s");
        $data = array(
            'mail_sub' => $subject,
            'mail_from' => $from_id,
            'mail_to' => $to_id,
            'message' => $message,
            'mail_date' => $date
        );
        $res = $this->db->insert('mail_from_lead', $data);
        if ($res) {
            $thread = $this->selectMaxThreadNumber() + 1;
            $this->sendMessageToUser($to_id, $subject, $message, $date, $from_id, $thread);
        }
        return $res;
    }

    public function sendMessageToDownlinesCumulative($subject, $from_id, $to_id, $message, $type)
    {
        $data = array(
            'mail_sub' => $subject,
            'mail_from' => $from_id,
            'mail_to' => $to_id,
            'message' => $message,
            'type' => $type,
            'mail_date' => date("Y-m-d H:i:s")
        );
        $res = $this->db->insert('mail_from_lead_cumulative', $data);
        return $res;
    }

    public function sendAllEmails($type = 'notification', $regr = array(), $attachments = array(), $user_id = '', $user_type = "")
    {

        if (!TEST_SEND_EMAIL) {
            if (!SEND_EMAIL || DEMO_STATUS == 'yes') {
            return;
            }
        }
        $this->lang->load('mail_lang');

        $this->load->library('inf_phpmailer', null, 'phpmailer');

        $site_info = $this->validation_model->getSiteInformation();
        $common_mail_settings = $this->configuration_model->getMailDetails();
        if(TEST_SEND_EMAIL) {
            $mail_type='smtp';
        } else {
            $mail_type = $common_mail_settings['reg_mail_type']; //normal/smtp
        }
        if(TEST_SEND_EMAIL) {
            $smtp_data = array(
                "SMTPAuth" => $common_mail_settings['smtp_authentication'],
                "SMTPSecure" => ($common_mail_settings['smtp_protocol'] == "none") ? "" : $common_mail_settings['smtp_protocol'],
                "Host" =>'smtp.mailtrap.io',
                "Port" =>2525,
                "Username" =>'8ba986e336614b',
                "Password" =>'1ecffcd4f328c4',
                "Timeout" => $common_mail_settings['smtp_timeout'],
            );

        } else {
            $smtp_data = array();
            if ($mail_type == "smtp") {
                $smtp_data = array(
                    "SMTPAuth" => $common_mail_settings['smtp_authentication'],
                    "SMTPSecure" => ($common_mail_settings['smtp_protocol'] == "none") ? "" : $common_mail_settings['smtp_protocol'],
                    "Host" => $common_mail_settings['smtp_host'],
                    "Port" => $common_mail_settings['smtp_port'],
                    "Username" => $common_mail_settings['smtp_username'],
                    "Password" => $common_mail_settings['smtp_password'],
                    "Timeout" => $common_mail_settings['smtp_timeout'],
                    //"SMTPDebug" => 3 //uncomment this line to check for any errors
                );
            }
        }

        $mail_to = array("email" => $regr['email'], "name" => $regr['first_name'] . " " . $regr['last_name']);
        if ($type == "ext_mail") {
            $mail_from = array("email" => $regr['email_from'], "name" => $regr['full_name']);
        } else {
            $mail_from = array("email" => $site_info['email'], "name" => $site_info['company_name']);
        }
        $mail_reply_to = $mail_from;
        $mail_subject = "Notification";

        if ($type == "registration") {
            $mail_status = $this->configuration_model->getSignupMailSendStatus();
            if($mail_status == 'no'){
                return;
            }
            $content = $this->getMailContent($type,$this->LANG_ID);
            $mail_subject = $mail_altbody = html_entity_decode($content['subject']) ?: "Registration";
            $mailBodyDetails = $content['mail_content'];
            $mailBodyDetails .= $this->smarty->view('mail/registration_success.tpl', [
                'regr' => $regr,
                'username' => $regr['username'],
                'table_prefix' => $this->table_prefix,
                'user_id' => $regr['userid'],
                'user_password' =>$regr['pswd'],
                'site_url' => SITE_URL,
                'module_status' => $this->MODULE_STATUS
            ], true);

            $mailBodyDetails = str_replace("{fullname}", $regr['first_name'] . " " . $regr['last_name'], $mailBodyDetails);
            $mailBodyDetails = str_replace("{username}", $regr['user_name_entry'], $mailBodyDetails);
            $mailBodyDetails = str_replace("{company_name}", $site_info['company_name'], $mailBodyDetails);
            $mailBodyDetails = str_replace("{company_address}", $site_info['company_address'], $mailBodyDetails);
            $mailBodyDetails = str_replace("{sponsor_username}", $regr['sponsor_user_name'], $mailBodyDetails);
            $mailBodyDetails = str_replace("{payment_type}", $regr['payment_type'], $mailBodyDetails);
           
        } elseif ($type == "payout_release") {
            $check_status = $this->mail_model->checkMailStatus($type);
            if($check_status == 'no'){
               return;
            }
            $lang_id = $this->payout_model->getUserLanguage($user_id);  
            $content = $this->getMailContent($type,$lang_id);
            $mail_subject = $mail_altbody = html_entity_decode($content['subject']) ?: "Payout Release";
            $mailBodyDetails = html_entity_decode($content['mail_content']);
            $mailBodyDetails = str_replace("{fullname}", $regr['first_name'] . " " . $regr['last_name'], $mailBodyDetails);
            $mailBodyDetails = str_replace("{company_name}", $site_info['company_name'], $mailBodyDetails);
            $mailBodyDetails = str_replace("{company_address}", $site_info['company_address'], $mailBodyDetails);
             
        } elseif ($type == "autoresponder") {
            $mail_content = html_entity_decode($regr['mail_content']);
            $mailBodyDetails = $mail_content;
            $mail_subject = $regr['subject'];
            $mail_altbody = $mail_subject;
            $mailBodyDetails = str_replace("{visitor_name}", $regr['user_name'], $mail_content);
            $mailBodyDetails = str_replace("{member_name}", $regr['sponser_name'], $mail_content);
            $mailBodyDetails = str_replace("{member_email}", $regr['sponser_email'], $mail_content);
        } elseif ($type == "change_password") {
            $content = $this->getMailContent($type,$this->LANG_ID);
            $mail_subject = $mail_altbody = $content['subject'] ?: "Change Password";
            $mailBodyDetails = html_entity_decode($content['mail_content']);
            $mailBodyDetails = str_replace("{full_name}", $regr['full_name'], $mailBodyDetails);
            $mailBodyDetails = str_replace("{new_password}", $regr['new_password'], $mailBodyDetails);
        } elseif ($type == "send_tranpass") {
            $content = $this->getMailContent($type,$this->LANG_ID);
            $mail_subject = $mail_altbody = $content['subject'] ?: "Transaction Password";
            $mailBodyDetails = !empty($content) ? html_entity_decode($content['mail_content']) : lang('na');
            $mailBodyDetails = str_replace("{first_name}", $regr['first_name'], $mailBodyDetails);
            $mailBodyDetails = str_replace("{password}", $regr['tranpass'], $mailBodyDetails);
        } elseif ($type == 'payout_request') {
            $content = $this->getMailContent($type,$this->LANG_ID);
            $mail_subject = $mail_altbody = $content['subject'] ?: "Payout Request";

            $mailBodyDetails = !empty($content) ? html_entity_decode($content['mail_content']) : lang('na');
            $mailBodyDetails = str_replace("{admin_user_name}", $this->ADMIN_USER_NAME, $mailBodyDetails);
            $mailBodyDetails = str_replace("{username}", $regr['username'], $mailBodyDetails);
            $mailBodyDetails = str_replace("{payout_amount}", $regr['payout_amount'], $mailBodyDetails);
        } elseif ($type == 'epin_transfer') {
            $content = $this->getMailContent($type,$this->LANG_ID);
            $mail_subject = $mail_altbody = $content['subject'] ?: "Epin Transfer";

            $mailBodyDetails = !empty($content) ? html_entity_decode($content['mail_content']) : lang('na');
            $mailBodyDetails = str_replace("{user_name}", $this->LOG_USER_NAME, $mailBodyDetails);
            $mailBodyDetails = str_replace("{to_user_name}", $regr['to_user_name'], $mailBodyDetails);
        } elseif ($type == 'epin_received') {
            $content = $this->getMailContent($type,$this->LANG_ID);
            $mail_subject = $mail_altbody = $content['subject'] ?: "Epin Received";

            $mailBodyDetails = !empty($content) ? html_entity_decode($content['mail_content']) : lang('na');
            $mailBodyDetails = str_replace("{user_name}", $regr['to_user_name'] , $mailBodyDetails);
            $mailBodyDetails = str_replace("{from_user_name}", $this->LOG_USER_NAME, $mailBodyDetails);
        } elseif ($type == 'invaite_mail') {
            $mail_subject = "Invite Email";
            $mail_altbody = "Invite Email";
            $mailBodyDetails = $regr['mail_content'];
        } elseif ($type == 'forgot_password') {
            $lang_id = $this->getUserLanguage($regr['user_id']);
            $content = $this->getMailContent($type, $lang_id);
            $mail_subject = $content['subject'] ?: "Forgot Password";
            $mail_altbody = $mail_subject;
            $mailBodyDetails = !empty($content) ? html_entity_decode($content['mail_content']) : lang('na');
            
            // Link
            $keyword = $this->login_model->getKeyWord($regr['user_id']);
            $keyword_encode = $this->encryption->encrypt($keyword);
            $keyword_encode = str_replace(["=", "/", "+"], ["-", "~", "."], $keyword_encode);
            $link = base_url() . "login/reset_password/$keyword_encode";
            $mailBodyDetails = str_replace("{link}", $link, $mailBodyDetails);
        } elseif ($type == 'reset_googleAuth') {
            $lang_id = $this->getUserLanguage($regr['user_id']);
            $content = $this->getMailContent($type, $lang_id);
            $mail_subject = $content['subject'] ?: "Forgot Password";
            $mail_altbody = $mail_subject;
            $mailBodyDetails = !empty($content) ? html_entity_decode($content['mail_content']) : lang('na');
            
            // Link
            $keyword = $regr['user_id'];
            $keyword_encode = $this->encryption->encrypt($keyword);
            $keyword_encode = str_replace(["=", "/", "+"], ["-", "~", "."], $keyword_encode);
            $this->load->model('login_model');
            $random_key = $this->login_model->getAuthKeyWord($regr['user_id'], $user_type);
            $random_key = $this->encryption->encrypt($random_key);
            $random_key = str_replace(["=", "/", "+"], ["-", "~", "."], $random_key);
            if($user_type == "employee") {
                $link = base_url() . "login/employee_one_tme_password_generate/$keyword_encode/$random_key";
            } else {
                $link = base_url() . "login/one_tme_password_generate/$keyword_encode/$random_key";
            }

            $mailBodyDetails = str_replace("{link}", $link, $mailBodyDetails);
        } elseif ($type == 'lcp_reply') {
            $mail_subject = "Your Lead Reply";
            $mail_altbody = "User updated your query..";
            $mailBodyDetails = $this->smarty->view('mail/lcp_reply.tpl', [
                'first_name'    => $regr['first_name'],
                'last_name'     => $regr['last_name'],
                'admin_comment' => $regr['admin_comment'],
                'new_status'    => $regr['new_status'],
                'lead_status'   => $regr['lead_status']
             ], true);
            $mailBodyDetails = $this->getLCPMailBody($regr);
        } elseif ($type == 'forgot_transaction_password') {
            $content = $this->getMailContent($type, $this->LANG_ID);
            $mail_subject = $content['subject'] ?: "Forgot Transaction Password";
            $mail_altbody = $mail_subject;
            $mailBodyDetails = !empty($content) ? html_entity_decode($content['mail_content']) : lang('na');
            // variables
            $mailBodyDetails = str_replace("{fullname}", $regr['full_name'], $mailBodyDetails);
            $mailBodyDetails = str_replace("{company_name}", $site_info['company_name'], $mailBodyDetails);
            $mailBodyDetails = str_replace("{company_address}", $site_info['company_address'], $mailBodyDetails);
            // Link
            $this->load->model('tran_pass_model');
            $admin_username = $this->validation_model->getAdminUsername();
            $keyword = $this->tran_pass_model->getKeyWord($regr['user_id']);
            $keyword_encode = $this->encryption->encrypt($keyword);
            $keyword_encode = str_replace(["=", "/", "+"], ["-", "~", "."], $keyword_encode);
            $link = base_url() . "login/reset_tran_password/$keyword_encode/$admin_username";
            
            $mailBodyDetails = str_replace("{link}", $link, $mailBodyDetails);
        } elseif ($type == 'external_mail') {
            $mail_subject = $regr['subject'];
            $mail_altbody = $regr['subject'];
            $content = $this->getMailContent($type, $this->LANG_ID);
            $mailBodyDetails = !empty($content) ? html_entity_decode($content['mail_content']) : lang('na');
            $mailBodyDetails = str_replace("{subject}", $regr['subject'], $mailBodyDetails);
            $mailBodyDetails = str_replace("{content}", html_entity_decode($regr['content']), $mailBodyDetails);
        } elseif ( $type == 'registration_email_verification'){
            $email_verification = $this->configuration_model->getEmailVerificationStatus();
            if($email_verification == 'no'){
               return;
            } 
            $admin_username = '';
            if(DEMO_STATUS == 'yes'){
                $admin_username = $this->session->userdata('admin_name');
            }
            if($this->LANG_ID == ''){
                $lang_id = $this->getUserLanguage($this->validation_model->getAdminId());
            } else{
                $lang_id = $this->LANG_ID; 
            }
                
            $content = $this->getMailContent($type, $lang_id);
            $mail_subject = $content['subject'] ?: "Registration Email Verification";
            $mail_altbody = $mail_subject;
            $mailBodyDetails = !empty($content) ? html_entity_decode($content['mail_content']) : lang('na');
            
            $keyword_encode = $regr['user_name_entry'];

            $mailBodyDetails = str_replace("{full_name}", $regr['first_name'] . " " .$regr['last_name'], $mailBodyDetails);
            $mailBodyDetails = str_replace("{company_name}", $site_info['company_name'], $mailBodyDetails);
            $link = base_url() . "login/confirm_email/$keyword_encode/$admin_username";
            echo $link;die;
            $mailBodyDetails = str_replace("{link}",$link, $mailBodyDetails);

        } elseif ($type == "send_mail_otp") {
            $content = $this->getMailContent($type,$this->LANG_ID);
            $mail_subject = $mail_altbody = $content['subject'] ?? "OTP For Simply37 Registration";
            $mailBodyDetails = !empty($content) ? html_entity_decode($content['mail_content']) : lang('na');
            $mailBodyDetails = str_replace("{first_name}", $regr['first_name'], $mailBodyDetails);
            $mailBodyDetails = str_replace("{otp}", $regr['otp'], $mailBodyDetails);
        }else {
            return;
        }

        $mailBodyDetails = str_replace("{banner_img}", $this->PUBLIC_URL . 'images/banners/banner.jpg', $mailBodyDetails);

        $data = [
            'company_name'    => $site_info['company_name'],
            'site_logo'       => $site_info['login_logo'],
            'company_address' => $site_info['company_address'],
            'site_url'        => SITE_URL, 
            'company_email'   => $site_info['email'] ,
            'company_phone'   => $site_info['phone'],
            'mail_content'    => $mailBodyDetails
        ];
        $mail_body = $this->smarty->view('layout/mail.tpl', $data, true);
    
        $time = time();
        // file_put_contents("/var/www/html/WC/ci_trunk/uploads/mail/{$type}_{$time}.html", $mail_body);
        
        // return true;
        
        if ($this->validation_model->getModuleStatusByKey('mail_gun_status') == 'yes') {
            $this->load->model('mail_gun_model');
            return $this->mail_gun_model->sendEmail($mailBodyDetails, $mail_to['email'], $mail_subject);
        } else {
            $send_mail = $this->phpmailer->send_mail($mail_from, $mail_to, $mail_reply_to, $mail_subject, $mail_body, $mail_altbody, $mail_type, $smtp_data, $attachments);
            
        }
        if (!$send_mail['status']) {
            $data["message"] = "Error: " . $send_mail['ErrorInfo'];
        } else {
            $data["message"] = "Message sent correctly!";
        }
        return $send_mail;
    }

    public function getHeaderDetails($site_info)
    {
        $data = [
            'company_name'    => $site_info['company_name'],
            'site_logo'       => $site_info['logo'],
            'company_address' => $site_info['company_address'],
            'site_url'        => SITE_URL, 
            'company_email'   => $site_info['email'] ,
            'company_phone'   => $site_info['phone']
        ];
        $mailBodyHeaderDetails = $this->smarty->view('layout/mail.tpl', $data, true);
        $mailBodyHeaderDetails = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <title>' . $site_info['company_name'] . '</title>
        </head>
        <body style="margin:0px;">
            <div class="container" style="font-family: roboto;width:830px;margin-left:auto;margin-right:auto;background:#f9f9f9;border-top:20px solid #ed0000;">

                <div class="header" style="height:117px;">
                    <div style="float: left;">
                        <img src="' . SITE_URL . '/uploads/images/logos/' . $site_info['logo'] . '" style="margin: 15px 0px 10px 19px;"/>
                    </div>
                </div>
                <div>
                    <p style="font-size: 17px; line-height: 27px; color: ##353535;">' . $site_info["company_name"] . ', ' . $site_info["company_address"] . '
                    </p>
                </div>';
        return $mailBodyHeaderDetails;
    }

    public function checkMailStatus($type)
    {
        $mail_status = 'no';
        if($type == 'registration' ) {
            $this->db->select('mail_status ');
            $this->db->where('mail_type', $type);
            $this->db->from('common_mail_settings');        
        }
        elseif($type == 'payout_release') {
            $this->db->select('payout_mail_status as mail_status');
            $this->db->from('configuration');
        }
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $mail_status = $row->mail_status;
        }
        return $mail_status;
    }

    public function getLCPMailBody($details)
    {
        $mailBodyDetails = '<div class="banner" style="background: url({banner_img});
    height: 58px;
    color: #fff;
    font-size: 21px;
    padding: 43px 20px 20px 40px;">
    Your Lead Capture is updated
</div>
<div class="body_text" style="padding:25px 65px 25px 45px; color:#333333;">
<h1 style="font-size:18px; color:#333333; font-weight: normal; font-weight: 300;">Dear <span style="font-weight:bold;">' . $details['first_name'] . " " . $details['last_name'] . ',</span></h1>';
        if (isset($details['admin_comment']) && ($details['admin_comment'] != "")) {

            $mailBodyDetails .= '<p style="font-size: 14px; line-height: 27px;">&emsp; &emsp; The user ' . $this->LOG_USER_NAME . ' commented as ' . $details['admin_comment'] . ',</p>';
        }
        if ($details['new_status'] == $details['lead_status']) {

            $mailBodyDetails .= '<p style="font-size: 14px; line-height: 27px;">&emsp; &emsp; Your Leads Status updated to ' . $details['new_status'] . '....</p>';
        }

        $mailBodyDetails .= '
    </div>';
        return $mailBodyDetails;
    }
    public function getMailDetails($id, $type, $thread = "")
    {
        if ($type == 'admin') {
            $result = array();
            $arr = array();
            $this->db->select('*');
            $this->db->from('mailtoadmin');
            $this->db->where([
                'status'        => 'yes',
                'deleted_by !=' => 'both',
                'deleted_by !=' => $this->LOG_USER_ID
            ]);
            if ($thread != '') {
                $this->db->where('thread', $thread);
            }
            $this->db->order_by("mailadid", "DESC");
            $query = $this->db->get();
            $i = 0;
            foreach ($query->result_array() as $row) {
                $arr[$i]["id"] = $row["mailadid"];
                $arr[$i]["to_user"] = "admin";
                $arr[$i]["subject"] = $row["mailadsubject"];
                $arr[$i]["message"] = $row["mailadidmsg"];
                $arr[$i]["date"] = $row["mailadiddate"];
                $arr[$i]["status"] = $row["status"];
                $arr[$i]["from"] = $row["mailaduser"];
                $arr[$i]['user_name'] = $this->validation_model->idToUserName($row['mailaduser']);
                $arr[$i]["read_msg"] = $row["read_msg"];
                $arr[$i]["delete_status"] = $row["deleted_by"];
                $arr[$i]["fullname"] = $this->validation_model->getUserFullName($row['mailaduser']);
                $arr[$i]["thread"] = $row["thread"];

                $i++;
            }
            $this->db->select('*');
            $this->db->from('mailtouser');
            $this->db->where([
                'status'        => 'yes',
                'deleted_by !=' => 'both',
                'deleted_by !=' => $this->LOG_USER_ID
            ]);
            $this->db->where('thread', $thread);
            $this->db->order_by("mailtousid", "DESC");
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                $arr[$i]["id"] = $row["mailtousid"];
                $arr[$i]["to_user"] = $row["mailtoususer"];
                $arr[$i]['user_name'] = $this->validation_model->idToUserName($row['mailfromuser']);
                $arr[$i]["fullname"] = $this->validation_model->getUserFullName($row['mailfromuser']);
                $arr[$i]["subject"] = $row["mailtoussub"];
                $arr[$i]["message"] = $row["mailtousmsg"];
                $arr[$i]["date"] = $row["mailtousdate"];
                $arr[$i]["status"] = $row["status"];
                $arr[$i]["from"] = $row["mailfromuser"];
                $arr[$i]["read_msg"] = $row["read_msg"];
                $arr[$i]["delete_status"] = $row["deleted_by"];
                $arr[$i]["thread"] = $row["thread"];

                $i++;
            }
            function cmp($a, $b)
            {
                $a["date"] = strtotime($a["date"]);
                $b["date"] = strtotime($b["date"]);
                return ($a["date"] > $b["date"]) ? -1 : 1;
            }

            usort($arr, "cmp");
            return $arr;
        } else {
            $result = array();
            $this->db->select('*');
            $this->db->from('contacts');
            $this->db->where('id', $id);
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                $result = $row;
            }
            return $result;

        }

    }
    public function updateContactMsgStatus($msg_id)
    {
        $data = array(
            'read_msg' => 'yes'
        );
        $this->db->where('id', $msg_id);
        $res = $this->db->update('contacts', $data);
        return $res;
    }
    public function getSentMailDetails($msg_id)
    {
        $result = array();
        $this->db->select('*');
        $this->db->from('mailtouser');
        $this->db->where('thread', $msg_id);
        $this->db->where('status', 'yes');
        $where = array('status' => 'no', 'deleted_by != ' => $this->LOG_USER_ID, 'deleted_by !=' => 'both');
        $this->db->or_group_start()
            ->where($where)
            ->group_end();
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $row['type'] = "individual";
            $row['to'] = $this->validation_model->idToUserName($row['mailtoususer']);
            $row['fullname'] =$this->validation_model->getUserFullName($row['mailtoususer']);
            $result[] = $row;
        }
        return $result;
    }
    public function getSentMailDetailsExt($msg_id)
    {
        $result = array();
        $this->db->select('*');
        $this->db->from('mailtouser_cumulativ');
        $this->db->where('mailtousid', $msg_id);
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $row['type'] = "individual";
            $row['to'] = $row['mailtoususer'];
            $result[] = $row;
        }
        return $result;
    }
    public function getUserMailDetails($id, $type, $thread = '')
    {
        if ($type == 'user') {
            $result = array();
            $arr = array();
            $this->db->select('m.mailfromuser,m.mailtousid,m.mailtoususer,m.mailfromuser,m.mailtoussub,m.mailtousmsg,m.mailtousdate,m.status,m.read_msg,m.deleted_by,m.thread,f.user_name,u.user_detail_refid,u.user_detail_name,u.user_detail_second_name');
            $this->db->from('mailtouser as m');
            $this->db->join('user_details as u','m.mailfromuser = u.user_detail_refid');
            $this->db->join('ft_individual f','f.id = u.user_detail_refid');
        //$this->db->join('mailtoadmin AS ma', 'mu.thread = ma.thread');
        //$this->db->where('mailtousid', $id);
            $this->db->where('thread', $thread);
            $query = $this->db->get();
//        foreach ($query->result() as $row) {
//            $result = $row;
//        }
            $i = 0;
            foreach ($query->result_array() as $row) {
                $arr[$i]["id"] = $row["mailtousid"];
                $arr[$i]["to_user"] = $row["mailtoususer"];
                $arr[$i]['user_name'] = $row["user_name"];
                $arr[$i]["subject"] = $row["mailtoussub"];
                $arr[$i]["message"] = $row["mailtousmsg"];
                $arr[$i]["date"] = $row["mailtousdate"];
                $arr[$i]["status"] = $row["status"];
                $arr[$i]["from"] = $row["mailfromuser"];
                $arr[$i]["fullname"] = $row['user_detail_name']." ".$row['user_detail_second_name'];
                $arr[$i]["read_msg"] = $row["read_msg"];
                $arr[$i]["delete_status"] = $row["deleted_by"];
                $arr[$i]["thread"] = $row["thread"];

                $i++;
            }
            $this->db->select('*');
            $this->db->from('mailtoadmin');
            $this->db->where('thread', $thread);
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                $arr[$i]["id"] = $row["mailadid"];
                $arr[$i]["to_user"] = "admin";
                $arr[$i]["subject"] = $row["mailadsubject"];
                $arr[$i]["message"] = $row["mailadidmsg"];
                $arr[$i]["date"] = $row["mailadiddate"];
                $arr[$i]["status"] = $row["status"];
                $arr[$i]["from"] = $row["mailaduser"];
                $arr[$i]['user_name'] = $this->validation_model->idToUserName($row['mailaduser']);
                $arr[$i]["read_msg"] = $row["read_msg"];
                $arr[$i]["delete_status"] = $row["deleted_by"];
                $arr[$i]["thread"] = $row["thread"];

                $i++;
            }
            function cmp($a, $b)
            {
                $a["date"] = strtotime($a["date"]);
                $b["date"] = strtotime($b["date"]);
                return ($a["date"] > $b["date"]) ? -1 : 1;
            }

            usort($arr, "cmp");
            return $arr;
        } else {
            $result = array();
            $this->db->select('*');
            $this->db->from('contacts');
            $this->db->where('id', $id);
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                $result = $row;
            }
            return $result;

        }
    }
    public function getUserSentMailDetails($msg_id, $type, $user_id = '')
    {

        if ($type == 'to_admin') {
            $result = array();
            $this->db->select('*');
            $this->db->from('mailtoadmin');
            $this->db->where('thread', $msg_id);
            $this->db->where('status', 'yes');
            $where = array('status' => 'no', 'deleted_by != ' => $this->LOG_USER_ID, 'deleted_by !=' => 'both');
            $this->db->or_group_start()
                ->where($where)
                ->group_end();
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                $row['to'] = $this->validation_model->getAdminUsername();
                $row["fullname"] = $this->validation_model->getFullName($this->validation_model->getAdminId());
                $result[] = $row;
                
            }
            return $result;
        } else if ($type == 'ext_mail_user') {
            $result = array();
            $this->db->select('*');
            $this->db->from('mailtouser_cumulativ');
            $this->db->where('mailtousid', $msg_id);
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                $row['to'] = $row['mailtoususer'];
                $row["fullname"] = $this->validation_model->getFullName($this->validation_model->userNameToID($row['mailtoususer']));
                $result[] = $row;
            }
            return $result;
        } else {
            $result = array();
            $this->db->select('*');
            $this->db->from('mailtouser');
            $this->db->where('thread', $msg_id);
            if ($user_id != '')
                $this->db->where('mailfromuser', $user_id);
            $this->db->where('status', 'yes');
            $where = array('status' => 'no', 'deleted_by != ' => $this->LOG_USER_ID, 'deleted_by !=' => 'both');
            $this->db->or_group_start()
                ->where($where)
                ->group_end();
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                $row['to'] = $this->validation_model->idToUserName($row['mailtoususer']);
                $row["fullname"] = $this->validation_model->getFullName($row['mailtoususer']);
                $result[] = $row;
            }

            return $result;

        }
    }
    public function selectMaxThreadNumber()
    {
        $max_id = 0;
        $this->db->select('MAX(thread) as number');
        $query = $this->db->get('mailtoadmin');

        foreach ($query->result_array() as $row) {

            $max_id = $row['number'];
        }
        $user_thread = $this->selectMaxThreadNumberUser();
        if ($user_thread >= $max_id) {
            $max_id = $user_thread;
        }

        return $max_id;
    }
    public function selectMaxThreadNumberUser()
    {
        $max_id = 0;
        $this->db->select('MAX(thread) as number');
        $query = $this->db->get('mailtouser');

        foreach ($query->result_array() as $row) {

            $max_id = $row['number'];
        }
        return $max_id;
    }
//     public function getExtMail($regr)
//     {

//         $mailBodyDetails = '<div class="banner" style="background: url({banner_img});
//     height: 58px;
//     color: #fff;
//     font-size: 21px;
//     padding: 43px 20px 20px 40px;">
// </div>
// <div class="body_text" style="padding:25px 65px 25px 45px;">
//     <h1 style="font-size:18px; color:#333333; font-weight: normal; font-weight: 300;">Subject: <span style="font-weight:bold;">' . $regr['subject'] . '</span></h1>

// <table border="0" width="800" align="center">
// <tr>
// <td    colspan="4"valign="top" >
// <br>
// <font size="3" face="Trebuchet MS">
// <p syte="pading-left:20px;"><b>Message,</b></p>
//      <p syte="pading-left:40px;"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $regr['content'] . '.</p>
//   <br>
//   </td>
// </tr>
// </font>
// </table>
// </div>';
//         return $mailBodyDetails;
//     }
    public function sendSingleEmail($details, $user_id, $mail_subject, $attachments = array())
    {

        $regr = $this->getUserEmailDetails($user_id);
        $mailBodyDetails = $this->getDonationMailDetails($details);
        $this->load->library('Inf_PHPMailer');
        $mail = new Inf_PHPMailer();

        $site_info = $this->validation_model->getSiteInformation();
        $common_mail_settings = $this->configuration_model->getMailDetails();

        //$mail_type = $common_mail_settings['reg_mail_type']; //normal/smtp
        $mail_type = 'normal'; //normal/smtp
        $smtp_data = array();
        if ($mail_type == "smtp") {
            $smtp_data = array(
                "SMTPAuth" => $common_mail_settings['smtp_authentication'],
                "SMTPSecure" => ($common_mail_settings['smtp_protocol'] == "none") ? "" : $common_mail_settings['smtp_protocol'],
                "Host" => $common_mail_settings['smtp_host'],
                "Port" => $common_mail_settings['smtp_port'],
                "Username" => $common_mail_settings['smtp_username'],
                "Password" => $common_mail_settings['smtp_password'],
                "Timeout" => $common_mail_settings['smtp_timeout'],
                    //"SMTPDebug" => 3 //uncomment this line to check for any errors
            );
        }
        $mail_to = array("email" => $regr['email'], "name" => $regr['first_name'] . " " . $regr['last_name']);
        $mail_from = array("email" => $site_info['email'], "name" => $site_info['company_name']);
        $mail_reply_to = $mail_from;

        $mailBodyHeaderDetails = $this->getHeaderDetails($site_info);

        $mail_altbody = $mail_subject;

       // $mailBodyFooterDetails = $this->getFooterDetails($site_info);

        $mail_body = $mailBodyHeaderDetails . $mailBodyDetails  . "</br></br></br></br></br>";

        $send_mail = $mail->send_mail($mail_from, $mail_to, $mail_reply_to, $mail_subject, $mail_body, $mail_altbody, $mail_type, $smtp_data, $attachments);

        if (!$send_mail['status']) {
            $data["message"] = "Error: " . $send_mail['ErrorInfo'];
        } else {
            $data["message"] = "Message sent correctly!";
        }

        return $send_mail;
    }

    public function sendSingleEmailMissed($details, $user_id, $mail_subject, $attachments = array())
    {

        $regr = $this->getUserEmailDetails($user_id);
        $mailBodyDetails = $details;
        $this->load->library('Inf_PHPMailer');
        $mail = new Inf_PHPMailer();

        $site_info = $this->validation_model->getSiteInformation();
        $common_mail_settings = $this->configuration_model->getMailDetails();

        //$mail_type = $common_mail_settings['reg_mail_type']; //normal/smtp
        $mail_type = 'normal'; //normal/smtp
        $smtp_data = array();
        if ($mail_type == "smtp") {
            $smtp_data = array(
                "SMTPAuth" => $common_mail_settings['smtp_authentication'],
                "SMTPSecure" => ($common_mail_settings['smtp_protocol'] == "none") ? "" : $common_mail_settings['smtp_protocol'],
                "Host" => $common_mail_settings['smtp_host'],
                "Port" => $common_mail_settings['smtp_port'],
                "Username" => $common_mail_settings['smtp_username'],
                "Password" => $common_mail_settings['smtp_password'],
                "Timeout" => $common_mail_settings['smtp_timeout'],
                    //"SMTPDebug" => 3 //uncomment this line to check for any errors
            );
        }
        $mail_to = array("email" => $regr['email'], "name" => $regr['first_name'] . " " . $regr['last_name']);
        $mail_from = array("email" => $site_info['email'], "name" => $site_info['company_name']);
        $mail_reply_to = $mail_from;

        $mailBodyHeaderDetails = $this->getHeaderDetails($site_info);

        $mail_altbody = $mail_subject;

        //$mailBodyFooterDetails = $this->getFooterDetails($site_info);

        $mail_body = $mailBodyHeaderDetails . $mailBodyDetails . "</br></br></br></br></br>";

        $send_mail = $mail->send_mail($mail_from, $mail_to, $mail_reply_to, $mail_subject, $mail_body, $mail_altbody, $mail_type, $smtp_data, $attachments);

        if (!$send_mail['status']) {
            $data["message"] = "Error: " . $send_mail['ErrorInfo'];
        } else {
            $data["message"] = "Message sent correctly!";
        }

        return $send_mail;
    }

    public function getDonationMailDetails($regr)
    {

        $mailBodyDetails = "<div class='banner' style='background: url({banner_img});
height: 58px;
color: #000000;
font-size: 21px;
padding: 43px 20px 20px 40px;'>
<strong>Congratulations!!!You are now eligible to upgrade to the next level!</strong>
</div>
<div class='body_text' style='padding: 25px 65px 25px 45px;'><strong>Hello " . $regr['user_name'] . "!</strong><br><br><br>A donation you submitted has been approved!<br>----
<br><br><br><table style='color: #222222; font-family: arial, sans-serif; font-size: 12.8px; font-style: normal; font-variant: normal; font-weight: normal; letter-spacing: normal; line-height: normal; orphans: auto; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 1; word-spacing: 0px; -webkit-text-stroke-width: 0px; background-color: #ffffff;'>
<tbody>
<tr>
<td style='font-family: arial, sans-serif; margin: 0px;'>Submitted:</td>
<td style='font-family: arial, sans-serif; margin: 0px;'>" . $regr['submitted_date'] . "</td>
</tr>
<tr>
<td style='font-family: arial, sans-serif; margin: 0px;'>Approved:</td>
<td style='font-family: arial, sans-serif; margin: 0px;'>" . $regr['approved_date'] . "</td>
</tr>
<tr>
<td style='font-family: arial, sans-serif; margin: 0px;'>Donation payment method:</td>
<td style='font-family: arial, sans-serif; margin: 0px;'>" . $regr['payment_method'] . "</td>
</tr>
<tr>
<td style='font-family: arial, sans-serif; margin: 0px;'>Transaction ID:</td>
<td style='font-family: arial, sans-serif; margin: 0px;'>" . $regr['trasaction_id'] . "</td>
</tr>
<tr>
<td style='font-family: arial, sans-serif; margin: 0px;'>Description:</td>
<td style='font-family: arial, sans-serif; margin: 0px;'>Level " . $regr['level'] . "</td>
</tr>
<tr>
<td style='font-family: arial, sans-serif; margin: 0px;'>Amount:</td>
<td style='font-family: arial, sans-serif; margin: 0px;'>Ƀ" . $regr['amount'] . "</td>
</tr>
</tbody>
</table><br><br><br>
--- <br><br>Congratulations! You are now eligible to upgrade to the next level. Thank you for your continued support. <br><br><br><br>The STREAMSPLUS Team</div>";

        return $mailBodyDetails;
    }
    public function getUserEmailDetails($user_id)
    {
        $email_details = array();
        $this->db->select("user_detail_email,user_detail_name,user_detail_second_name");
        $this->db->from("user_details");
        $this->db->where("user_detail_refid", $user_id);
        $this->db->limit(1);
        $res = $this->db->get();
        foreach ($res->result() as $row) {
            $email_details['email'] = $row->user_detail_email;
            $email_details['first_name'] = $row->user_detail_name;
            $email_details['last_name'] = $row->user_detail_second_name;
        }
        return $email_details;
    }
    public function sendOtpMail($otp, $email, $type = "")
    {
        $site_info = $this->validation_model->getSiteInformation();
        $subject = $type . " authentication OTP";
        $dt = date('Y-m-d h:i:s');

        $mailBodyDetails = "<html>
        <head>
            <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
        </head>
        <body >
            <table id='Table_01' width='600'   border='0' cellpadding='0' cellspacing='0'>
             <tr><td COLSPAN='3'></td></tr>

             <td width='50px'></td>
             <td   width='520px'  > <br>
              <p>
               <table border='0' cellpadding='0' width='60%' >
                   <tr>
                    <td colspan='2' align='center'><b>Your authentication OTP is : " . $otp . "</b></td>
                </tr>
                <tr>
                    <td colspan='2'>Thanking you,</td>
                </tr>

                <tr>
                    <td colspan='2'><p align='left'>" . $site_info['company_name'] . "<br />Date:" . $dt . "<br /></p></td>
                </tr>
            </table>
            <tr>
               <td COLSPAN='3'>
               </td>
           </tr>
       </table>
   </body>
   </html>";

        if ($this->validation_model->getModuleStatusByKey('mail_gun_status') == 'yes') {
            $this->load->model('mail_gun_model');
            return $this->mail_gun_model->sendEmail($mailBodyDetails, $email, $subject);
        } else {
            return $this->sendEmail($mailBodyDetails, $email, $subject);
        }
    }

    public function getCountUserMessagesSent($user_id) {

        $this->db->from('mailtoadmin');
        $where1 = array('status' => 'yes', 'mailaduser' => $user_id);
        $where2 = array('status' => 'no', 'deleted_by != ' => $user_id, 'mailaduser' => $user_id, 'deleted_by !=' => 'both');
        $this->db->group_start()
            ->where($where1)
            ->or_group_start()
            ->where($where2)
            ->group_end()
            ->group_end();
        $this->db->group_by('thread');
        $count1 = $this->db->count_all_results();

        $this->db->from('mailtouser');
        $where3 = array('status' => 'yes', 'mailfromuser' => $user_id);
        $where4 = array('status' => 'no', 'deleted_by != ' => $user_id, 'mailfromuser' => $user_id, 'deleted_by !=' => 'both');
        $this->db->group_start()
            ->where($where3)
            ->or_group_start()
            ->where($where4)
            ->group_end()
            ->group_end();
        $this->db->group_by('thread');
        $count2 = $this->db->count_all_results();

        $this->db->from('mailtouser_cumulativ');
        $this->db->where('type', 'ext_mail_user');
        $this->db->where('status', 'yes');
        $count3 = $this->db->count_all_results();

        return ($count1+$count2+$count3);
    }

    public function getMailContent($type, $lang_id) {
        $this->db->select('*')
            ->from('common_mail_settings')
            ->where(['mail_type' => $type, 'lang_ref_id' =>$lang_id]);
        $query = $this->db->get();
        if($this->db->affected_rows() == 1) {
            return $query->row_array();
        }
        return [];
    }

    public function getUserLanguage($user_id) {
        $this->db->select('default_lang');
        $this->db->where('id',$user_id);
        $this->db->from('ft_individual');
        $lang_id = $this->db->get()->row('default_lang');
        return $lang_id;
    }

}
