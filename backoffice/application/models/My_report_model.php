<?php

class my_report_model extends inf_model
{

    public $referals;
    public $obj_cal;

    public function __construct()
    {
        parent::__construct();
        $this->referals = array();
        $this->load->model('validation_model');
    }

    public function getAllUnilevel($id, $limit = '', $page = '', $level_value = '')
    {
        $arr = $this->getDownlineUsersForHistory($id, 'left_sponsor', 'right_sponsor', $limit, $page, 'unilevel', $level_value);
        return $arr;
    }

    public function getDownlineDetailsBinary($id, $limit = '', $page = '', $level_value = '')
    {
        $arr = $this->getDownlineBinary($id,$limit, $page, 'binary', $level_value);
        return $arr;
    }
    
    public function getDownlineDetailsUnilevel($id, $limit = '', $page = '', $level_value = '')
    {
        $arr = $this->getDownlineUnilevel($id, $limit, $page, 'unilevel', $level_value);
        return $arr;
    }

    public function findUserlevel($id, $logged_user, $level = 1, $plan)
    {

        $table = "ft_individual";
        if ($plan == 'unilevel') {

            $this->db->select('sponsor_id');
            $this->db->where('id', $id);
            $this->db->limit(1);
            $res = $this->db->get($table);
            foreach ($res->result() as $row) {

                if ($logged_user == $row->sponsor_id) {

                    return $level;
                } else {
                    $ret_level = $this->findUserlevel($row->sponsor_id, $logged_user, $level + 1, $plan);

                    return $ret_level;
                }
            }
        } else {
            $this->db->select('father_id');
            $this->db->where('id', $id);
            $this->db->limit(1);
            $res = $this->db->get($table);
            foreach ($res->result() as $row) {

                if ($logged_user == $row->father_id) {

                    return $level;
                } else {
                    $ret_level = $this->findUserlevel($row->father_id, $logged_user, $level + 1, $plan);

                    return $ret_level;
                }
            }
        }
    }

    public function getDownlineUsersForHistory($user_id, $left_field, $right_field, $limit, $page = 0, $plan = 'binary', $level_value = '')
    {
        $this->load->model('country_state_model');
        $this->db->select("$left_field, $right_field");
        $this->db->where('ft_id', $user_id);
        $root = $this->db->get('tree_parser');
        $root = $root->result_array();
        $left = $root[0]["$left_field"];
        $right = $root[0]["$right_field"];

        $this->db->select('ft.id,ft.user_name,ft.date_of_joining,ft.user_rank_id,ft.active,ud.user_detail_name,ud.user_detail_second_name,ud.user_detail_city,ud.user_detail_country,ud.user_detail_state,ud.user_photo');
        // $this->db->select("*");
        $this->db->from('ft_individual AS ft');
        $this->db->join('tree_parser t', 't.ft_id = ft.id', 'LEFT');
        $this->db->join('user_details AS ud', 'ft.id = ud.user_detail_refid');
        $this->db->where("t.$left_field >", $left);
        $this->db->where("t.$right_field <", $right);
        if ($level_value != '' && $plan == 'unilevel') {
            $this->db->where("ft.sponsor_level", $level_value);
        } else if ($level_value != '' && $plan == 'binary') {
            $this->db->where("ft.user_level", $level_value);
        }
        $this->db->order_by("ft.sponsor_level", "asc");
        $this->db->limit($limit, $page);
        $res = $this->db->get();
        $i = 0;
        $referrals = array();
        foreach ($res->result_array() as $row) {
            $id_encode = $this->encryption->encrypt($row['id']);
            $id_encode = str_replace("/", "_", $id_encode);
            $id_encode = str_replace("+", "-", $id_encode);
            $encrypt_id = urlencode($id_encode);
            $referrals[$i]['id'] = $encrypt_id;
            $referrals[$i]['date_of_joining'] = $row['date_of_joining'];
            $referrals[$i]['name'] = $row['user_detail_name'];
            $referrals[$i]['name'] .= " " . $row['user_detail_second_name'];
            $referrals[$i]['username'] = $row['user_name'];
            $referrals[$i]['active'] = $row['active'];
            if ($this->MODULE_STATUS['rank_status'] == 'yes') {

                $referrals[$i]['rank'] = $this->validation_model->getRankName($row['user_rank_id']);
                $referrals[$i]['rank_color'] = $this->validation_model->getRankColor($row['user_rank_id']);
            }

            $username_encode = $this->encryption->encrypt($row['user_name']);
            $username_encode = str_replace("/", "_", $username_encode);
            $username_encode = urlencode($username_encode);
            $referrals[$i]['username_enc'] = $username_encode;

            if ($row['user_detail_state'] == "") {
                $referrals[$i]['state'] = "NA";
            } else {
                $referrals[$i]['state'] = $this->country_state_model->getStateNameFromId($row['user_detail_state']);
            }
            if ($row['user_detail_city'] == "0") {
                $referrals[$i]['city'] = "NA";
            } else {
                $referrals[$i]['city'] = $row['user_detail_city'];
            }
            $referrals[$i]['country'] = $this->country_state_model->getCountryNameFromId($row['user_detail_country']);
            $referrals[$i]['level'] = $level = $this->findUserlevel($row['id'], $user_id, 1, $plan);
            $i++;
        }
        if (count($referrals) > 0) {
            foreach ($referrals as $key => $row) {
                $arr[$key] = $row['level'];
            }
            array_multisort($arr, SORT_ASC, $referrals);
        }
        return $referrals;
    }
    public function getTotalDownlineUsersForHistory($user_id, $left_field, $right_field, $plan = 'binary', $level_value = '')
    {

        $this->load->model('country_state_model');
        $this->db->select("$left_field, $right_field");
        $this->db->where('ft_id', $user_id);
        $root = $this->db->get('tree_parser');
        $root = $root->result_array();
        $left = $root[0]["$left_field"];
        $right = $root[0]["$right_field"];

        $this->db->select('ft.id,ft.user_name,ft.date_of_joining,ud.user_detail_name,ud.user_detail_second_name,ud.user_detail_city,ud.user_detail_country,ud.user_detail_state,ud.user_photo');
        $this->db->from('ft_individual AS ft');
        $this->db->join('tree_parser t', 't.ft_id = ft.id', 'LEFT');
        $this->db->join('user_details AS ud', 'ft.id = ud.user_detail_refid');
        $this->db->where("t.$left_field >", $left);
        $this->db->where("t.$right_field <", $right);
        if ($level_value != '') {
            $this->db->where("ft.sponsor_level", $level_value);
        }
        $res = $this->db->get();
        $count = count($res->result_array());

        $i = 0;
        $referrals = array();
        foreach ($res->result_array() as $row) {
            $referrals[$i]['level'] = $level = $this->findUserlevel($row['id'], $user_id, 1, $plan);
            $i++;
        }
        if (count($referrals) > 0) {
            foreach ($referrals as $key => $row) {
                $arr[$key] = $row['level'];
            }
            array_multisort($arr, SORT_ASC, $referrals);
        }
        $referrals['count'] = $count;
        return $referrals;
    }
    public function getMaxLevelUser($user_id) {

        $user_level = $this->validation_model->getUserLevel($user_id);

        $this->db->select_max('user_level');
        $this->db->from('ft_individual AS ft');
        $this->db->join('treepath t', 't.descendant = ft.id');
        $this->db->where('t.ancestor', $user_id);
        $result = $this->db->get()->row();
        if (empty($result->user_level)) {
            return 0;
        }
        return $result->user_level - $user_level;
    }
    
    public function getMaxLevelSponsor($user_id) {

        $sponsor_level = $this->validation_model->getUserTreeLevel($user_id, 'sponsor_tree');

        $this->db->select_max('sponsor_level');
        $this->db->from('ft_individual AS ft');
        $this->db->join('sponsor_treepath t', 't.descendant = ft.id');
        $this->db->where('t.ancestor', $user_id);
        $result = $this->db->get()->row();
        if (empty($result->sponsor_level)) {
            return 0;
        }
        return $result->sponsor_level - $sponsor_level;
    }

    public function getDownlineBinary($user_id, $limit, $page = 0, $plan = 'binary', $level_value = '')
    {

        $level = $this->validation_model->getUserLevel($user_id);

        $this->db->select("ft.user_level - {$level} as ref_level,ft.user_name,ft.active,ud.user_detail_name,ud.user_detail_second_name,ud.user_photo,ft3.user_name sponsor", false);
        $this->db->from('ft_individual AS ft');
        $this->db->join('treepath t', 't.descendant = ft.id');
        $this->db->where('t.ancestor', $user_id);
        $this->db->where("ft.id !=", $user_id);
        $this->db->join('user_details AS ud', 'ft.id = ud.user_detail_refid');
        $this->db->join('ft_individual ft3', 'ft3.id = ft.sponsor_id', 'left');
        if (in_array($this->MLM_PLAN, ['Binary', 'Matrix'])) {
            $this->db->select('ft2.user_name placement');
            $this->db->join('ft_individual ft2', 'ft2.id = ft.father_id', 'left');
        }
        if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
            $this->db->select('p.model current_package');
            $this->db->join('oc_product p', 'ft.product_id = p.package_id', 'left');
        } elseif ($this->MODULE_STATUS['product_status'] == 'yes') {
            $this->db->select('p.product_name current_package');
            $this->db->join('package p', 'ft.product_id = p.prod_id', 'left');
        }
        if ($this->MODULE_STATUS['rank_status'] == 'yes') {
            $this->db->select('r.rank_name current_rank');
            $this->db->join('rank_details r', 'ft.user_rank_id = r.rank_id', 'left');
        }

        if ($level_value != '' && $plan == 'unilevel') {
            $this->db->where("ft.sponsor_level", $level_value);
        } else if ($level_value != '' && $plan == 'binary') {
            $this->db->where("ft.user_level", $level_value);
        }
        $this->db->order_by("ref_level", "asc");
        $this->db->limit($limit, $page);
        $res = $this->db->get();
        $referrals = $res->result_array();
        return $referrals;
    }

    public function getDownlineUnilevel($user_id,$limit, $page = 0, $plan = 'unilevel', $level_value = '')
    {
        $level = $this->validation_model->getUserTreeLevel($user_id, 'sponsor_tree');

        $this->db->select("ft.sponsor_level - {$level} as ref_level,ft.user_name,ft.active,ft.date_of_joining,ud.user_detail_name,ud.user_photo,ud.user_detail_second_name,ft3.user_name sponsor", false);
        $this->db->from('ft_individual AS ft');
        $this->db->join('sponsor_treepath t', 't.descendant = ft.id');
        $this->db->where('t.ancestor', $user_id);
        $this->db->where("ft.id !=", $user_id);
        $this->db->join('user_details AS ud', 'ft.id = ud.user_detail_refid');
        $this->db->join('ft_individual ft3', 'ft3.id = ft.sponsor_id', 'left');
        if ($this->MODULE_STATUS['opencart_status'] == 'yes') {
            $this->db->select('p.model current_package');
            $this->db->join('oc_product p', 'ft.product_id = p.package_id', 'left');
        } elseif ($this->MODULE_STATUS['product_status'] == 'yes') {
            $this->db->select('p.product_name current_package');
            $this->db->join('package p', 'ft.product_id = p.prod_id', 'left');
        }
        if ($this->MODULE_STATUS['rank_status'] == 'yes') {
            $this->db->select('r.rank_name current_rank');
            $this->db->join('rank_details r', 'ft.user_rank_id = r.rank_id', 'left');
        }
        if ($level_value != '' && $plan == 'unilevel') {
            $this->db->where("ft.sponsor_level", $level_value);
        } else if ($level_value != '' && $plan == 'binary') {
            $this->db->where("ft.user_level", $level_value);
        }
        $this->db->order_by("ref_level", "asc");
        $this->db->limit($limit, $page);
        $res = $this->db->get();
        $referrals = $res->result_array();
        return $referrals;
    }
    public function getTotalDownlineUsersBinary($user_id, $level_value = '')
    {
        
        $this->db->from('ft_individual AS ft');
        $this->db->join('treepath t', 't.descendant = ft.id');
        $this->db->where('t.ancestor', $user_id);
        if ($level_value != '') {
            $this->db->where("ft.user_level", $level_value);
        }
        $count = $this->db->count_all_results();
        return $count;
    }
    public function getTotalDownlineUsersCount($user_id)
    {

        $this->db->from('ft_individual AS ft');
        $this->db->join('treepath t', 't.descendant = ft.id');
        $this->db->where('t.ancestor', $user_id); 
        $this->db->where('ft.id !=', $user_id);       
        $count = $this->db->count_all_results();
        return $count;
    }
    public function getTotalDownlineUsersUnilevel($user_id, $level_value = '')
    {

        $this->db->from('ft_individual AS ft');
        $this->db->join('sponsor_treepath t', 't.descendant = ft.id');
        $this->db->where('t.ancestor', $user_id); 
        $this->db->where('ft.id !=', $user_id);       
        if ($level_value != '') {
            $this->db->where("ft.sponsor_level", $level_value);
        }
        $count = $this->db->count_all_results();
        return $count;
    }
}
