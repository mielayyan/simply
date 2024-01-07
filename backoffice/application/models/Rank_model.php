<?php

class rank_model extends inf_model {

    public function __construct() {
        parent::__construct();
        $this->load->model('validation_model');
        $this->load->model('configuration_model');
        $this->load->model('calculation_model');
    }

//For repurchase & upgrade starts
    public function updateUplineRank($purchse_user_id) {

        $rank_configuration = $this->configuration_model->getRankConfiguration();
        $rank_commission_status = $this->validation_model->getCompensationConfig(['rank_commission_status']);
        if($rank_configuration['rank_expiry'] == 'fixed') {
            if ($rank_configuration['joinee_package']) {
                $old_rank = $this->validation_model->getUserRank($purchse_user_id);   
                $new_rank = $this->checkNewRank(0, 0, 0, $purchse_user_id, $old_rank);
                if ($new_rank != $old_rank) {
                    $this->updateUserRank($purchse_user_id, $new_rank);
                    if($rank_commission_status == 'yes') {
                        $this->rankBonus($new_rank, $purchse_user_id, $purchse_user_id);
                    }
                }
                return true;
            } else {
                $sponsor_upline = $this->validation_model->getUplinesFromSponsorTreePath($purchse_user_id);

                foreach($sponsor_upline as $uplines) {
                    $user_id        = $uplines["id"];
                    $personal_pv    = $uplines["personal_pv"];
                    $group_pv       = $uplines["gpv"];
                    $old_rank       = $uplines["user_rank_id"];
                    $user_status    = $uplines["active"];
                    $referal_count  = $this->validation_model->getReferalCount($user_id);
                    if ($user_status == 'yes') {
                            $new_rank = $this->checkNewRank($referal_count, $personal_pv, $group_pv, $user_id, $old_rank);
                        if ($new_rank != $old_rank) {
                            $this->updateUserRank($user_id, $new_rank);

                            if($rank_commission_status == 'yes') {
                                //$this->rankBonus($new_rank, $user_id, $purchse_user_id);
                            }
                            if($new_rank>=4&&$old_rank<4) {
                                $gold_sponsor_upline = $this->validation_model->getUplinesFromSponsorTreePath($user_id);
                                $user_ids = array();
                                foreach($gold_sponsor_upline as $gold_uplines){
                                    if(isset($gold_uplines['sponsor_id']))
                                    $user_ids[] = $gold_uplines['sponsor_id'];
                                }
                                if(!empty($user_ids))
                                $this->updateGoldLegsCount($user_ids);
                            }
                        }
                    }
                }
            }
        }
    }
    
    public function updateGoldLegsCount($user_ids) {
        $this->db->where_in('user_id',$user_ids);
        $this->db->set('gold_leg_count', 'gold_leg_count + 1', FALSE);
        $this->db->update('summary_info');
    }

    public function rankBonus($new_rank, $user_id, $purchse_user_id) {

        $rank_bonus     = $this->configuration_model->getActiveRankDetails($new_rank);
        $date_of_sub    = date("Y-m-d H:i:s");
        $amount_type    = "rank_bonus";
        $obj_arr        = $this->validation_model->getSettings();
        $tds_db         = $obj_arr["tds"];
        $service_charge = $obj_arr["service_charge"];
        $rank_amount    = $rank_bonus[0]['rank_bonus'];
        $tds_amount     = ($rank_amount * $tds_db) / 100;
        $service_charge = ($rank_amount * $service_charge) / 100;
        $amount_payable = $rank_amount - ($tds_amount + $service_charge);

        $this->calculation_model->insertRankBonus($user_id, $rank_amount, $amount_payable, $tds_amount, $service_charge, $date_of_sub, $amount_type, $purchse_user_id, 1);
    }

    public function checkNewRank($referal_count, $personal_pv, $group_pv, $user_id, $curent_rank)
    {
        
        $summary_details = $this->getRankSummary($user_id);
        
        if(empty($group_pv))
        {
            $group_pv = 0;
        }
        $criteria = [
            'referal_count' => FALSE,
            'joinee_package' => FALSE,
            'personal_pv' =>FALSE,
            'group_pv' =>FALSE,
            'downline_member_count' => FALSE,
            'downline_purchase_count' => FALSE,
            'downline_rank' => FALSE,
        ];

       $rank_configuration = $this->configuration_model->getRankConfiguration();
        if ($rank_configuration['referal_count']) {
            $criteria['referal_count'] = TRUE;
        }

        if ($rank_configuration['personal_pv']) {
            $criteria['personal_pv'] = TRUE;
        }
        if ($rank_configuration['group_pv']) {
            $criteria['group_pv'] = TRUE;
        }

        if ($rank_configuration['downline_member_count'] && in_array($this->MLM_PLAN, ['Binary', 'Matrix'])) {
            $total_downline_count =  $this->getLeftRightDownlineUsersCount($user_id,'father');
            $criteria['downline_member_count'] = TRUE;
        }

        if ($rank_configuration['downline_purchase_count']) {
            $criteria['downline_purchase_count'] = TRUE;
        }

        if ($rank_configuration['downline_rank']) {
            $criteria['downline_rank'] = TRUE;
        }

        if ($rank_configuration['joinee_package']) {

            $criteria = ['referal_count' => FALSE, 'personal_pv' =>FALSE, 'group_pv' =>FALSE, 'downline_member_count' => FALSE, 'downline_purchase_count' => FALSE, 'downline_rank' => FALSE];
            $criteria['joinee_package'] = TRUE;
            $user_package_id = $this->validation_model->getProductId($user_id);
            $joinee_rank_id = $this->getJoineeRankId($user_package_id);
            
        }

        if ($criteria['downline_purchase_count']) {

            $downline_package_details = $this->getLeftRightDownlinePackageCount($user_id, 'father');
            if ($downline_package_details) {
                $this->db->select('rd.rank_id');
                foreach ($downline_package_details as $v) {

                    $this->db->select("COALESCE(SUM(CASE WHEN package_id='{$v['package_id']}' THEN package_count END), 0) AS {$v['package_id']}", FALSE);
                    $package_columns[] = $v['package_id'];

                }
                $this->db->from('purchase_rank as r1');
                $this->db->where('rd.rank_status', 'active');
                $this->db->where('rd.delete_status', 'yes');
                $this->db->join("rank_details AS rd", 'rd.rank_id = r1.rank_id','right');
                $this->db->group_by('rd.rank_id');
                $downline_package_query = $this->db->get_compiled_select();

            }
        }

        if ($criteria['downline_rank']) {
            $downline_rank_details = $this->getLeftRightDownlineRankWiseCount($user_id);
            if ($downline_rank_details) {
                $this->db->select('rd.rank_id');
                foreach ($downline_rank_details as $v) {
                    $this->db->select("COALESCE(SUM(CASE WHEN r1.downline_rank_id='{$v['rank_id']}' THEN rank_count END), 0) AS {$v['rank_name']}", FALSE);
                    $rank_columns[] = $v['rank_name'];
                }
                $this->db->from('downline_rank as r1');
                $this->db->where('rd.rank_status', 'active');
                $this->db->where('rd.delete_status', 'yes');
                $this->db->join("rank_details AS rd", 'rd.rank_id = r1.rank_id','right');
                $this->db->group_by('rd.rank_id');
                $downline_rank_query = $this->db->get_compiled_select();
            }
        }

        $this->db->select('r.rank_id,r.rank_name');
        $this->db->from('rank_details r');

        if ($criteria['referal_count']) {
            $this->db->where('r.referal_count <=', $referal_count);
            $this->db->where('r.binary_max <=', $summary_details['binary_max']);
            $this->db->where('r.gold_leg_count <=', $summary_details['gold_leg_count']);
        }
        if ($criteria['joinee_package']) {
            $this->db->where('r.rank_id', $joinee_rank_id);
        }

        if ($criteria['personal_pv']) {
            $this->db->where('r.personal_pv <=', $personal_pv);
        }
        if ($criteria['group_pv']) {

            $this->db->where('r.gpv <=', $group_pv);
        }
        if($curent_rank != NULL) {
            $this->db->where('r.rank_id >', $curent_rank);
        }

        if ($criteria['downline_member_count']) {
            $this->db->where('r.downline_count <=', $total_downline_count);
        }

        if ($criteria['downline_purchase_count']) {
            $package_columns = implode(',', $package_columns);
            $this->db->select($package_columns);
            $this->db->join("({$downline_package_query}) AS p", 'p.rank_id=r.rank_id');
            foreach($downline_package_details as $d) {
                $this->db->where("p.{$d['package_id']} <=", $d['count']);
            }
        }

        if ($criteria['downline_rank']) {
            $rank_columns = implode(',', $rank_columns);
            $this->db->select($rank_columns);
            $this->db->join("({$downline_rank_query}) AS dwr", 'dwr.rank_id=r.rank_id');
            foreach($downline_rank_details as $d) {
               $this->db->where("dwr.{$d['rank_name']} <=", $d['count']);
            }
        }


        $this->db->where('r.rank_status', 'active');
        $this->db->where('r.delete_status', 'yes');
        $this->db->order_by('r.rank_id', 'ASC');

        $query = $this->db->get();

        $rank_id = $curent_rank;

        foreach ($query->result() as $row) {
            $rank_id = $row->rank_id;
            $this->insertIntoRankHistory($curent_rank, $rank_id, $user_id);
            $curent_rank = $rank_id;
        }

        return $rank_id;
    }

    public function getLeftRightDownlineUsersCount($user_id, $type, $user_level= '') {
        $this->db->select('f.id');
        $this->db->from("ft_individual f");
        $this->db->join('treepath t', 'f.id = t.descendant');
        $this->db->where('t.ancestor', $user_id);

        if($user_level != "") {
            $this->db->where('f.user_level',$user_level);
        }
        $numrows = $this->db->count_all_results(); // Number of rows returned from above query.
        return $numrows;
    }
    
    
    public function getRankSummary($user_id) {
        $this->db->select('binary_max,directs,gold_leg_count');
        $this->db->from('summary_info');
        $this->db->where('user_id',$user_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getIndirectDownlineCount($user_id, $type) {
        $this->db->select('f.id');
        $this->db->where("f.father_id !=", $user_id);
        $this->db->join('treepath t', 'f.id = t.descendant');
        $this->db->where('t.ancestor', $user_id);
        $this->db->from("ft_individual f");
        $numrows = $this->db->count_all_results();
        return $numrows;
    }

    public function getDirectDownlineCount($user_id, $type) {
        $this->db->select('f.id');
        $this->db->where("f.father_id =", $user_id);
        $this->db->join('treepath t', 'f.id = t.descendant');
        $this->db->where('t.ancestor', $user_id);
        $this->db->from("ft_individual f");
        $numrows = $this->db->count_all_results();
        return $numrows;
    }

    public function getLeftRightDownlinePackageCount($user_id, $type) {
        $rs    = [];
        //$arr   = $this->validation_model->getUserLeftAndRight($user_id, $type);

        $this->db->select('f.id, f.product_id');
        //$this->db->where("t.left_$type >", $arr["left_$type"]);
        //$this->db->where("t.right_$type <", $arr["right_$type"]);
        $this->db->from("ft_individual f");
        //$this->db->join('tree_parser t', "f.id = t.ft_id", 'LEFT');
        $this->db->join('treepath t', 'f.id = t.descendant');
        $this->db->where('t.ancestor', $user_id);
        $this->db->where('f.id !=', $user_id);
        $downline_users = $this->db->get_compiled_select();

        if ( ($this->MODULE_STATUS['opencart_status'] == "yes" && $this->MODULE_STATUS['opencart_status_demo'] == "yes")) {
            $this->db->select('count( du.id ) AS count,op.package_id,op.product_id');
            $this->db->from("oc_product op");
            $this->db->join("({$downline_users}) AS du", 'op.package_id = du.product_id', 'LEFT');
            $this->db->where('op.package_type', 'registration');
            $this->db->where('op.status', 1);
            $this->db->group_by("op.package_id");
        } else {
            $this->db->select('count( du.id ) AS count,p.prod_id package_id,p.product_id');
            $this->db->from('package as p');
            $this->db->join("({$downline_users}) AS du", 'p.prod_id = du.product_id', 'LEFT');
            $this->db->where('p.type_of_package', 'registration');
            $this->db->group_by('p.prod_id');
        }
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $rs[] = $row;
        }
        return $rs;
    }

    public function updateUserRank($id, $rank) {
        $this->db->set('user_rank_id', $rank);
        $this->db->where('id', $id);
        $result = $this->db->update('ft_individual');
        return $result;
    }

    public function getJoineeRankId($prod_id) {
        $rank_id = '';
        $this->db->select('rd.rank_id');
        $this->db->from('joinee_rank as jr');
        $this->db->where('rd.rank_status', 'active');
        $this->db->where('rd.delete_status', 'yes');
        $this->db->where('jr.package_id', $prod_id);
        $this->db->join("rank_details AS rd", 'rd.rank_id=jr.rank_id','left');
        $query = $this->db->get();
        foreach ($query->result() as $row) {
           $rank_id = $row->rank_id;
        }
        return $rank_id;
    }

    public function insertIntoRankHistory($old_rank, $new_rank, $user_id) {
        $date = date('Y-m-d H:i:s');
        $this->db->set('user_id', $user_id);
        $this->db->set('current_rank', $old_rank);
        $this->db->set('new_rank', $new_rank);
        $this->db->set('date', $date);
        $res = $this->db->insert('rank_history');
        return $res;
    }

    public function getPurchaseRank($rank_id) {
        $details = [];
        $this->db->where('rank_id', $rank_id);
        $query = $this->db->get('purchase_rank');
        foreach ($query->result_array() as $row) {
            $details[] = $row;
        }
        return $details;
    }

    public function getRankCriteria($user_id)
    {
        $criteria = [
            'referal_count' => FALSE,
            'joinee_package' => FALSE,
            'personal_pv' =>FALSE,
            'group_pv' =>FALSE,
            'downline_count' => FALSE,
            'downline_package_count' => FALSE,
            'downline_rank' => FALSE,
        ];

        $rank_configuration = $this->configuration_model->getRankConfiguration();
        if ($rank_configuration['referal_count']) {
            $criteria['referal_count'] = TRUE;
        }

        if ($rank_configuration['joinee_package']) {
            $criteria['joinee_package'] = TRUE;
        }

        if ($rank_configuration['personal_pv']) {
            $criteria['personal_pv'] = TRUE;
        }
        if ($rank_configuration['group_pv']) {
            $criteria['group_pv'] = TRUE;
        }

        if ($rank_configuration['downline_member_count'] && in_array($this->MLM_PLAN, ['Binary', 'Matrix'])) {
            $criteria['downline_count'] = TRUE;
        }

       if ($rank_configuration['downline_purchase_count']) {
            $criteria['downline_package_count'] = TRUE;
       }

        if ($rank_configuration['downline_rank']) {
            $criteria['downline_rank'] = TRUE;
        }

        if ($criteria['downline_count']) {
            $this->db->select('COUNT(f3.id) downline_count');
            $this->db->from('ft_individual f3');
            $this->db->join('treepath t', 'f3.id = t.descendant');
            $this->db->where('t.ancestor', $user_id);
            $this->db->where('f3.id !=', $user_id);
               
            $downline_count = $this->db->get()->row_array()['downline_count'] ?? 0;
          }
         

        $this->db->select('f1.user_rank_id rank_id,r.rank_name');
        $this->db->select('COUNT(f2.id) referal_count');
        $this->db->from('ft_individual f1');
        $this->db->join('treepath t', 'f1.id = t.descendant');
        $this->db->where('t.ancestor', $user_id);
        $this->db->join('rank_details r', 'f1.user_rank_id=r.rank_id', 'LEFT');
        $this->db->join('ft_individual f2', 'f2.sponsor_id=f1.id', 'LEFT');
        if ($criteria['personal_pv']) {
            $this->db->select('f1.personal_pv');
        }
        if ($criteria['group_pv']) {
            $this->db->select('f1.gpv group_pv');
        }

        $this->db->where('f1.id', $user_id);
        $query = $this->db->get();
        $current_rank_details = $query->row_array();
        // dd($current_rank_details);
        if(!array_key_exists('group_pv', $current_rank_details)) {
            $current_rank_details['group_pv'] = 0;
        }
        if ($criteria['downline_count']) {
            $current_rank_details['downline_count'] = $downline_count;
        }


        $next_rank_criteria = [];

        if ($criteria['downline_package_count']) {
            if ($this->MODULE_STATUS['opencart_status'] == "yes") {
                $this->db->select('COUNT(f.id) package_count,op.package_id,op.product_id,op.model product_name');
                $this->db->from("oc_product op");
                $this->db->join('ft_individual f', "op.package_id=f.product_id", 'LEFT');
                $this->db->join('treepath t', 'f.id = t.descendant');
                $this->db->where('op.package_type', 'registration');
                $this->db->where('op.status', 1);
                $this->db->group_by("op.package_id");
                $query2 = $this->db->get();
            } else {
                $this->db->select('COUNT(f.id) package_count,p.prod_id package_id,p.product_id,p.product_name');
                $this->db->from('package p');
                $this->db->join('ft_individual f', "p.prod_id=f.product_id", 'LEFT');
                $this->db->join('treepath t', 'f.id = t.descendant');
                $this->db->where('t.ancestor', $user_id);
                $this->db->where('p.type_of_package', 'registration');
                $this->db->group_by('p.prod_id');
                $query2 = $this->db->get();
            }

            $downline_package_details = $query2->result_array();
            
            if ($downline_package_details) {
                $this->db->select('rd.rank_id');
                foreach ($downline_package_details as $v) {
                    $current_rank_details['downline_package_count']["{$v['package_id']}"] = $v['package_count'];
                    $current_rank_details['package_name']["{$v['package_id']}"] = $v['product_name'];
                    $this->db->select("COALESCE(SUM(CASE WHEN package_id='{$v['package_id']}' THEN package_count END), 0) AS '{$v['package_id']}'", FALSE);
                }
                $this->db->from('purchase_rank as r1');
                $this->db->where('rd.rank_status', 'active');
                $this->db->where('rd.delete_status', 'yes');
                $this->db->join("rank_details AS rd", 'rd.rank_id=r1.rank_id','right');
                $this->db->group_by('rd.rank_id');
                $downline_package_query = $this->db->get_compiled_select();
            }
        }

        if ($current_rank_details) {
            $this->db->select('r.rank_id,r.rank_name,r.referal_count referal_count');
            $this->db->from('rank_details r');
            if ($criteria['personal_pv']) {
                $this->db->select('r.personal_pv');
            }
            if ($criteria['group_pv']) {
                $this->db->select('r.gpv group_pv');
            }
            if ($criteria['downline_count']) {
                $this->db->select('r.downline_count');
            }
            if ($criteria['downline_package_count'] && isset($current_rank_details['downline_package_count'])) {  
                $package_columns = array_map(function ($value) {
                    return 'p.' . "`$value`";
                }, array_keys($current_rank_details['downline_package_count']));
                $package_columns = implode(',', $package_columns);              
                $this->db->select($package_columns);
                $this->db->join("({$downline_package_query}) AS p", 'p.rank_id=r.rank_id');
            }
            $this->db->where('r.rank_status', 'active');
            $this->db->where('r.delete_status', 'yes');
            if (!empty($current_rank_details['rank_id'])) {
                $this->db->where('r.rank_id >', $current_rank_details['rank_id']);
            }
            $this->db->order_by('r.rank_id', 'ASC');
            $this->db->limit(1);
            $query = $this->db->get();
            $next_rank_criteria = $query->row_array();
            
            if ($next_rank_criteria && isset($current_rank_details['downline_package_count'])) {
                
                foreach ($current_rank_details['downline_package_count'] as $package_id => $package_count) {
                    $next_rank_criteria['downline_package_count']["{$package_id}"] = $next_rank_criteria[$package_id];
                }
            }
        }
      
     
        return [
            'criteria' => $criteria,
            'current_rank' => $current_rank_details,
            'next_rank' => $next_rank_criteria,
        ];
    }
    
     public function getRequiredNextRankCount($rank_id){
         $this->db->select('r.rank_name, d.rank_count');
         $this->db->from('downline_rank AS d');
         $this->db->join('rank_details AS r', 'd.downline_rank_id = r.rank_id');
         $this->db->where('d.rank_id', $rank_id);
         $query = $this->db->get()->result_array();
         return $query;
    }

    public function getLeftRightDownlineRankWiseCount($user_id) {
        $rs    = [];
        $this->db->select('f.id, f.user_rank_id');
        $this->db->from("ft_individual f");
        $this->db->join('treepath t', 'f.id = t.descendant');
        $this->db->where('t.ancestor', $user_id);
        $this->db->where('f.id !=', $user_id);
        $downline_users = $this->db->get_compiled_select();
        $this->db->select('count( du.id ) AS count,r.rank_id,r.rank_name');
        $this->db->from("rank_details as r");
        $this->db->join("({$downline_users}) AS du", 'r.rank_id = du.user_rank_id', 'LEFT');
        $this->db->where('r.rank_status', 'active');
        $this->db->group_by('r.rank_id');
        $query = $this->db->get();
        foreach ($query->result_array() as $row) {
            $rs[] = $row;
        } 
        return $rs;
    }

    public function getProductWiseRank($package_id) {

        $this->db->select('rank_name');
        $this->db->from("package");
        $this->db->where('prod_id', $package_id);
        $this->db->where('active', 'yes');
        $this->db->where('type_of_package', 'registration');
        $query = $this->db->get();
        return $query->result_array();

    }

    public function updateDefaultRank($user_id, $rank_id) {
        
        $rank_commission_status = $this->validation_model->getCompensationConfig(['rank_commission_status']);
        $this->rank_model->updateUserRank($user_id, $rank_id);
        if($rank_commission_status == 'yes') {
            //$this->rank_model->rankBonus($rank_id, $user_id, $user_id);
        }
    }
    


    //Referance functions
    // public function updateRank($sponsor_id, $basic_demo_status, $user_id) {
        // $old_rank       = $this->validation_model->getUserRank($sponsor_id);
        // $user_status    = $this->validation_model->getUserStatus($sponsor_id);
        // $referal_count  = $this->validation_model->getReferalCount($sponsor_id);

        // if ($user_status == 'active') {
        //     if ($basic_demo_status == "yes") {
        //         $new_rank       = $this->checkNewRankforbasic($referal_count, $old_rank, $sponsor_id);
        //     } elseif ($basic_demo_status == "no") {
        //         $personal_pv    = (int) $this->validation_model->getPersnlPv($sponsor_id);
        //         $group_pv       = (int) $this->validation_model->getGrpPv($sponsor_id);
        //         $new_rank       = $this->checkNewRank($referal_count, $personal_pv, $group_pv, $sponsor_id, $old_rank);
        //     }
        //     if ($new_rank) {
        //         $this->updateUserRank($sponsor_id, $new_rank);

        //         $rank_bonus     = $this->configuration_model->getActiveRankDetails($new_rank);
        //         $date_of_sub    = date("Y-m-d H:i:s");
        //         $amount_type    = "rank_bonus";
        //         $obj_arr        = $this->validation_model->getSettings();
        //         $tds_db         = $obj_arr["tds"];
        //         $service_charge_db = $obj_arr["service_charge"];
        //         $rank_amount    = $rank_bonus[0]['rank_bonus'];
        //         $tds_amount     = ($rank_amount * $tds_db) / 100;
        //         $service_charge = ($rank_amount * $service_charge_db) / 100;
        //         $amount_payable = $rank_amount - ($tds_amount + $service_charge);

        //         $this->calculation_model->insertRankBonus($sponsor_id, $rank_amount, $amount_payable, $tds_amount, $service_charge, $date_of_sub, $amount_type, $user_id, 1);
        //     }
        // }
    // }


    // public function checkNewRank($referal_count, $personal_pv, $group_pv, $user_id, $curent_rank) {
    //     $rank_id = $curent_rank;
    //     $rs_arr  = [];
    //     $personal_pv_status = $this->MODULE_STATUS["personal_pv"];
    //     $group_pv_status    = $this->MODULE_STATUS["group_pv"];
    //     $downline_status    = $this->MODULE_STATUS["downline_count_rank"];
    //     $downline_member_status = $this->MODULE_STATUS["downline_purchase_rank"];

    //     if (($this->MLM_PLAN == "Binary" || $this->MLM_PLAN == "Matrix") && ($downline_status == 'yes')) {
    //         $downline_count = $this->getLeftRightDownlineUsersCount($user_id, 'sponsor');
    //         $this->db->where('downline_count <=', $downline_count);
    //     }

    //     $this->db->select('rank_id');
    //     if ($personal_pv_status == 'yes') {
    //         $this->db->where('personal_pv <=', $personal_pv);
    //     }
    //     if ($group_pv_status == 'yes') {
    //         $this->db->where('gpv <=', $group_pv);
    //     }
    //     $this->db->where('referal_count <=', $referal_count);
    //     $this->db->where('rank_status', 'active');
    //     if($curent_rank != NULL) {
    //         $this->db->where('rank_id >', $curent_rank);
    //     }
    //     $this->db->where('delete_status', 'yes');
    //     $this->db->order_by('rank_id', 'ASC');
    //     $res = $this->db->get('rank_details');

    //     if (($this->MLM_PLAN == "Binary" || $this->MLM_PLAN == "Matrix") && ($downline_member_status == 'yes')) {
    //         $downline_package = $this->getLeftRightDownlinePackageCount($user_id, 'sponsor');
    //         foreach ($res->result() as $row) {
    //             $rank_det = $this->getPurchaseRank($row->rank_id);
    //             $rs_arr = array_map(function($array1, $array2) {
    //                 return array_merge(isset($array1) ? $array1 : array(), isset($array2) ? $array2 : array());
    //             }, $downline_package, $rank_det);

    //             foreach ($rs_arr as $res) {
    //                 if (isset($res['count']) && isset($res['package_count'])) {
    //                     if($res['count'] >= $res['package_count']) {
    //                         $rank_id = $res['rank_id'];
    //                     } else {
    //                         $rank_id = NULL;
    //                     }
    //                 }
    //             }

    //             if($rank_id != NULL) {
    //                 $this->insertIntoRankHistory($curent_rank, $rank_id, $user_id);
    //                 $curent_rank = $rank_id;
    //             }
    //         }
    //     } else {
    //         foreach ($res->result() as $row) {
    //             $rank_id = $row->rank_id;
    //             $this->insertIntoRankHistory($curent_rank, $rank_id, $user_id);
    //             $curent_rank = $rank_id;
    //         }
    //     }
    //     return $rank_id;
    // }
    public function currentRankName($user_id)
    {
            $this->db->select('r.rank_name,r.rank_id');
            $this->db->from('rank_details as r');
            $this->db->join('ft_individual as ft' ,'r.rank_id = ft.user_rank_id');
            $this->db->where('ft.id',$user_id);
            $query1 = $this->db->get()->row_array();
            return $query1;
    }
    public function NextRankName($rank_id = 0)
    {       
            $this->db->select('r.rank_id,r.rank_name');
            $this->db->from('rank_details r');
            $this->db->where('r.rank_status', 'active');

            $this->db->where('r.rank_id >', $rank_id);
            $this->db->order_by('r.rank_id', 'ASC');
            $this->db->limit(1);
            return $query = $this->db->get()->row_array();
    }
            
    public function getCurrentRankData($user_id)
    {

        $criteria = [
            'referal_count' => FALSE,
            'joinee_package' => FALSE,
            'personal_pv' =>FALSE,
            'group_pv' =>FALSE,
            'downline_count' => FALSE,
            'downline_package_count' => FALSE,
            'downline_rank' => FALSE,
        ];

        $rank_configuration = $this->configuration_model->getRankConfiguration();
        if ($rank_configuration['referal_count']) {
            $criteria['referal_count'] = TRUE;
        }

        if ($rank_configuration['joinee_package']) {
            $criteria['joinee_package'] = TRUE;
        }

        if ($rank_configuration['personal_pv']) {
            $criteria['personal_pv'] = TRUE;
        }
        if ($rank_configuration['group_pv']) {
            $criteria['group_pv'] = TRUE;
        }

        if ($rank_configuration['downline_member_count'] && in_array($this->MLM_PLAN, ['Binary', 'Matrix'])) {
            $criteria['downline_count'] = TRUE;
        }

       if ($rank_configuration['downline_purchase_count']) {
            $criteria['downline_package_count'] = TRUE;
       }

        if ($rank_configuration['downline_rank']) {
            $criteria['downline_rank'] = TRUE;
        }

        $current = [];
        $current['criteria'] = $criteria;

        if($criteria['referal_count'])
        {
            $this->db->select('r.rank_name,r.referal_count as required');
            $this->db->from('rank_details as r');
            $this->db->join('ft_individual as ft' ,'r.rank_id = ft.user_rank_id');
            $this->db->where('ft.id',$user_id);
            $query1 = $this->db->get()->row_array();
            
            $this->db->select('COUNT(f3.id) as achieved');
            $this->db->from('ft_individual as f3');
            $this->db->where('f3.sponsor_id',$user_id);
            $query2 = $this->db->get()->row_array();
            $query2['percentage'] = $this->calculatePercentage($query2['achieved'], $query1['required']);
            $current['referal_count'] = array_merge($query1, $query2);
            
            
            
            $this->db->select('r.rank_name,r.binary_max as required');
            $this->db->from('rank_details as r');
            $this->db->join('ft_individual as ft' ,'r.rank_id = ft.user_rank_id');
            $this->db->where('ft.id',$user_id);
            $query1 = $this->db->get()->row_array();
            $this->db->select('binary_max as achieved');
            $this->db->from('summary_info');
            $this->db->where('user_id',$user_id);
            $query2 = $this->db->get()->row_array();
            $query2['percentage'] = $this->calculatePercentage($query2['achieved'], $query1['required']);
            $current['binary_max'] = array_merge($query1, $query2);
            
            
            $this->db->select('r.rank_name,r.gold_leg_count as required');
            $this->db->from('rank_details as r');
            $this->db->join('ft_individual as ft' ,'r.rank_id = ft.user_rank_id');
            $this->db->where('ft.id',$user_id);
            $query1 = $this->db->get()->row_array();
            $this->db->select('gold_leg_count as achieved');
            $this->db->from('summary_info');
            $this->db->where('user_id',$user_id);
            $query2 = $this->db->get()->row_array();
            $query2['percentage'] = $this->calculatePercentage($query2['achieved'], $query1['required']);
            $current['gold_leg_count'] = array_merge($query1, $query2);
           }

        if($criteria['personal_pv'])
        {
            $this->db->select('r.rank_name,r.personal_pv as required');
            $this->db->select('ft.personal_pv as achieved');
            $this->db->from('rank_details as r');
            $this->db->join('ft_individual as ft' ,'r.rank_id = ft.user_rank_id');
            $this->db->where('ft.id',$user_id);
            $query1 = $this->db->get()->row_array();
            $query1['percentage'] = $this->calculatePercentage($query1['achieved'], $query1['required']);
            $current['personal_pv']=$query1;
        }
        if($criteria['group_pv'])
        {
            $this->db->select('r.rank_name,r.gpv as required');
            $this->db->select('ft.gpv as achieved');
            $this->db->from('rank_details as r');
            $this->db->join('ft_individual as ft' ,'r.rank_id = ft.user_rank_id');
            $this->db->where('ft.id',$user_id);
            $query1 = $this->db->get()->row_array();
            $query1['percentage'] = $this->calculatePercentage($query1['achieved'], $query1['required']);
            $current['group_pv']=$query1;
        }
        if($criteria['downline_count'])
        {
            $this->db->select('r.rank_name,r.downline_count as required');
            $this->db->from('rank_details as r');
            $this->db->join('ft_individual as ft' ,'r.rank_id = ft.user_rank_id');
            $this->db->where('ft.id',$user_id);
            $query1 = $this->db->get()->row_array();

            //$arr   = $this->validation_model->getUserLeftAndRight($user_id, $type='father');

            $this->db->select('COUNT(f.id) achieved');
            //$this->db->where("t.left_$type >", $arr["left_$type"]);
            //$this->db->where("t.right_$type <", $arr["right_$type"]);
            $this->db->from("ft_individual f");
            //$this->db->join('tree_parser t', "f.id = t.ft_id", 'LEFT');

            $query2 = $this->db->get()->row_array();
            $query2['percentage'] = $this->calculatePercentage($query2['achieved'], $query1['required']);
            $current['downline_member_count']=array_merge($query1, $query2);
        }
        if($criteria['downline_package_count'])
        {
            $this->db->select('r.rank_id');
            $this->db->from('rank_details as r');
            $this->db->join('ft_individual as ft' ,'r.rank_id = ft.user_rank_id');
            $this->db->where('ft.id',$user_id);
            $query1 = $this->db->get()->row_array();
          
            $this->db->select('pr.package_count as required, pr.package_id, p.product_name');
            $this->db->from('purchase_rank as pr');
            $this->db->join('package as p', 'p.prod_id = pr.package_id', 'left');
            $this->db->join('rank_details AS r', 'pr.rank_id = r.rank_id', 'left');
            $this->db->where('pr.rank_id', $query1['rank_id']);


            $query2 = $this->db->get()->result_array();
            foreach ($query2 as $key => $package) {
                $query2[$key]['achieved'] = $this->getAchivedPackageCount($package['package_id'], $user_id);
                $query2[$key]['percentage'] = $this->calculatePercentage($query2[$key]['achieved'], $query2[$key]['required']);
            }
           $current['downline_package_count']= $query2;
        }

        if($criteria['downline_rank'])
        {
            $this->db->select('r.rank_name,r.rank_id');
            $this->db->from('rank_details as r');
            $this->db->join('ft_individual as ft' ,'r.rank_id = ft.user_rank_id');
            $this->db->where('ft.id',$user_id);
            $query1 = $this->db->get()->row_array();
            $rs    = [];
            //$arr   = $this->validation_model->getUserLeftAndRight($user_id, $type='father');

            $this->db->select('f.id, f.user_rank_id');
            $this->db->from("ft_individual f");
            $this->db->join('treepath t', 'f.id = t.descendant');
            $this->db->where('t.ancestor', $user_id);
            $downline_users = $this->db->get_compiled_select();
             $this->db->select('r.rank_name, d.rank_count as required,count( du.id ) AS achieved');
             $this->db->from('rank_details AS r');
             $this->db->join('downline_rank AS d', 'd.downline_rank_id = r.rank_id', 'right');
             $this->db->join("({$downline_users}) AS du", 'r.rank_id = du.user_rank_id', 'LEFT');
             $this->db->where('d.rank_id', $query1['rank_id']);
        
             $this->db->where('r.rank_status', 'active');
             $this->db->group_by('r.rank_id');
             $query = $this->db->get()->result_array();
             foreach ($query as $key => $rank) {
               $query[$key]['percentage'] = $this->calculatePercentage($query[$key]['achieved'],$query[$key]['required']);
             }
             $current['downline_rank']=$query;

        }
        return $current;
            
    }
    public function getNextRankData($nrank_id,$user_id) {

         $criteria = [
            'referal_count' => FALSE,
            'joinee_package' => FALSE,
            'personal_pv' =>FALSE,
            'group_pv' =>FALSE,
            'downline_count' => FALSE,
            'downline_package_count' => FALSE,
            'downline_rank' => FALSE,
        ];

        $rank_configuration = $this->configuration_model->getRankConfiguration();
        if ($rank_configuration['referal_count']) {
            $criteria['referal_count'] = TRUE;
        }

        if ($rank_configuration['joinee_package']) {
            $criteria['joinee_package'] = TRUE;
        }

        if ($rank_configuration['personal_pv']) {
            $criteria['personal_pv'] = TRUE;
        }
        if ($rank_configuration['group_pv']) {
            $criteria['group_pv'] = TRUE;
        }

        if ($rank_configuration['downline_member_count'] && in_array($this->MLM_PLAN, ['Binary', 'Matrix'])) {
            $criteria['downline_count'] = TRUE;
        }

       if ($rank_configuration['downline_purchase_count']) {
            $criteria['downline_package_count'] = TRUE;
        }

        if ($rank_configuration['downline_rank']) {
            $criteria['downline_rank'] = TRUE;
        }

        $next = [];

        if($criteria['referal_count'])
        {
            $this->db->select('r.referal_count as required');
            $this->db->from('rank_details as r');
            $this->db->where('r.rank_id',$nrank_id);
            $query1 = $this->db->get()->row_array();
            $this->db->select('COUNT(f3.id) as achieved');
            $this->db->from('ft_individual as f3');
            $this->db->where('f3.sponsor_id',$user_id);
            $query2 = $this->db->get()->row_array();
            $query2['percentage'] = $this->calculatePercentage($query2['achieved'], $query1['required']);
            $next['referal_count'] = array_merge($query1, $query2);
            
            $this->db->select('r.binary_max as required');
            $this->db->from('rank_details as r');
            $this->db->where('r.rank_id',$nrank_id);
            $query1 = $this->db->get()->row_array();
            $this->db->select('binary_max as achieved');
            $this->db->from('summary_info');
            $this->db->where('user_id',$user_id);
            $query2 = $this->db->get()->row_array();
            $query2['percentage'] = $this->calculatePercentage($query2['achieved'], $query1['required']);
            $next['binary_max'] = array_merge($query1, $query2);
            
            $this->db->select('r.gold_leg_count as required');
            $this->db->from('rank_details as r');
            $this->db->where('r.rank_id',$nrank_id);
            $query1 = $this->db->get()->row_array();
            $this->db->select('gold_leg_count as achieved');
            $this->db->from('summary_info');
            $this->db->where('user_id',$user_id);
            $query2 = $this->db->get()->row_array();
            $query2['percentage'] = $this->calculatePercentage($query2['achieved'], $query1['required']);
            $next['gold_leg_count'] = array_merge($query1, $query2);
            
           }

          if($criteria['personal_pv'])
          {
            $this->db->select('r.personal_pv as required');
            $this->db->from('rank_details as r');
            $this->db->where('r.rank_id',$nrank_id);
            $query1 = $this->db->get()->row_array();

            $this->db->select('ft.personal_pv as achieved');
            $this->db->from('ft_individual as ft');
            $this->db->where('ft.id',$user_id);
            $query2 = $this->db->get()->row_array();
            $query2['percentage'] = $this->calculatePercentage($query2['achieved'], $query1['required']);

            $next['personal_pv']=array_merge($query1,$query2);
          } 
           
           if($criteria['group_pv'])
           {
            $this->db->select('r.gpv as required');
            $this->db->from('rank_details as r');
            $this->db->where('r.rank_id',$nrank_id);
            $query1 = $this->db->get()->row_array();
            $this->db->select('IFNULL(NULLIF(ft.gpv,""),0) as achieved');
            $this->db->from('ft_individual as ft');
            $this->db->where('ft.id',$user_id);
            $query2 = $this->db->get()->row_array();
            $query2['percentage'] = $this->calculatePercentage($query2['achieved'], $query1['required']);

            $next['group_pv']=array_merge($query1,$query2);
          } 
           if($criteria['downline_count']) 
           {
            $this->db->select('r.downline_count as required');
            $this->db->from('rank_details as r');
            $this->db->where('r.rank_id',$nrank_id);
            $query1 = $this->db->get()->row_array();
            $this->db->select('COUNT(f.id) achieved');
            $this->db->from("ft_individual f");
            $this->db->join('treepath t', 'f.id = t.descendant');
            $this->db->where('t.ancestor', $user_id);
            $this->db->where('t.descendant !=', $user_id);
            $query2 = $this->db->get()->row_array();
            $query2['percentage'] = $this->calculatePercentage($query2['achieved'], $query1['required']);
            $next['downline_member_count']=array_merge($query1, $query2);

           }
           if($criteria['downline_package_count'])
           {
             if ($this->MODULE_STATUS['opencart_status'] == "yes" && $this->MODULE_STATUS['opencart_status_demo'] == "yes") {
            $this->db->select('pr.package_count as required, oc.package_id, oc.model');
            $this->db->from('purchase_rank as pr');
            $this->db->join('oc_product as oc', 'oc.package_id = pr.package_id', 'left');
            $this->db->join('rank_details AS r', 'pr.rank_id = r.rank_id', 'left');
            $this->db->where('pr.rank_id', $nrank_id);
            }  
            else { 
            $this->db->select('pr.package_count as required, pr.package_id, p.product_name');
            $this->db->from('purchase_rank as pr');
            $this->db->join('package as p', 'p.prod_id = pr.package_id', 'left');
            $this->db->join('rank_details AS r', 'pr.rank_id = r.rank_id', 'left');
            $this->db->where('pr.rank_id', $nrank_id);
            }
            $query2 = $this->db->get()->result_array();
            foreach ($query2 as $key => $package) {
                $query2[$key]['achieved'] = $this->getAchivedPackageCount($package['package_id'], $user_id);
                $query2[$key]['percentage'] = $this->calculatePercentage($query2[$key]['achieved'], $query2[$key]['required']);
            }
           $next['downline_package_count']= $query2;
           }

         if($criteria['downline_rank'])
            {
           
            $rs    = [];

            $this->db->select('f.id, f.user_rank_id');
            $this->db->from("ft_individual f");
            $this->db->join('treepath t', 'f.id = t.descendant');
            $this->db->where('t.ancestor', $user_id);
            $downline_users = $this->db->get_compiled_select();
            
             $this->db->select('r.rank_name, d.rank_count as required,count( du.id ) AS achieved');
             $this->db->from('rank_details AS r');
             $this->db->join('downline_rank AS d', 'd.downline_rank_id = r.rank_id', 'right');
             $this->db->join("({$downline_users}) AS du", 'r.rank_id = du.user_rank_id', 'LEFT');
             $this->db->where('d.rank_id', $nrank_id);
        
             $this->db->where('r.rank_status', 'active');
             $this->db->group_by('r.rank_id');
             $query = $this->db->get()->result_array();
             foreach ($query as $key => $rank) {
               $query[$key]['percentage'] = $this->calculatePercentage($query[$key]['achieved'],$query[$key]['required']);
             }
             $next['downline_rank']=$query;

        }
            return $next;
    }

    public function getAchivedPackageCount($package_id, $user_id) {

         //$arr   = $this->validation_model->getUserLeftAndRight($user_id, $type='father');

         if ($this->MODULE_STATUS['opencart_status'] == "yes" && $this->MODULE_STATUS['opencart_status_demo'] == "yes") {
            $this->db->select('COUNT(f.id) achieved');
            $this->db->where('op.package_id', $package_id);
            $this->db->where('op.package_type', 'registration');
            $this->db->where('op.status', 1);
            $this->db->from("oc_product op");
            $this->db->join('ft_individual f', "op.package_id=f.product_id", 'LEFT');
            $this->db->join('treepath t', 'f.id = t.descendant');
            $this->db->where('t.ancestor', $user_id);
           
        } else {
            $this->db->select('count(f.id) achieved,product_id');
            $this->db->where('f.product_id', $package_id);
            $this->db->from("ft_individual f");
            $this->db->join('treepath t', 'f.id = t.descendant');
            $this->db->where('t.ancestor', $user_id);
        }
        $query2 = $this->db->get();
        return $query2->row()->achieved;
    }

    public function calculatePercentage($achieved, $required) {
        if($required == 0) {
            return 0;
        }
        return (($achieved / $required) * 100) > 100 ? 100 : (($achieved / $required) * 100);
    }
    public function getCurrentPoolData($user_id)
    {

        $criteria = [
            'direct_leg' => TRUE,
            'group_pv' => TRUE,
            'rank' =>TRUE,
        ];
        $current=array();
        if($criteria['direct_leg'])
        {
            $this->db->select('r.direct as required');
            $this->db->from('rank_promo as r');
            $this->db->join('ft_individual as ft' ,'r.id = ft.promo_rank');
            $this->db->where('ft.id',$user_id);
            $query1 = $this->db->get()->row_array();
            $query1['required']=$query1['required']??0;
            $this->db->select('COUNT(f3.id) as achieved');
            $this->db->from('ft_individual as f3');
            $this->db->where('f3.sponsor_id',$user_id);
            $query2 = $this->db->get()->row_array();

            $query2['percentage'] = $this->calculatePercentage($query2['achieved'], $query1['required']);
            $current['referal_count'] = array_merge($query1, $query2);
            
        
           }
        if($criteria['group_pv'])
        {
            $this->db->select('r.group_pv as required,r.group_pv_percent');
            $this->db->select('ft.gpv as achieved');
            $this->db->from('rank_promo as r');
            $this->db->join('ft_individual as ft' ,'r.id = ft.promo_rank');
            $this->db->where('ft.id',$user_id);
            $query1 = $this->db->get()->row_array();
            $query1['required']=$query1['required']??0;
            $query1['group_pv_percent']=$query1['group_pv_percent']??0;
            $query1['achieved']=$this->checkGroupPV($user_id,$query1['group_pv_percent'],$query1['required']);
            $query1['percentage'] = $this->calculatePercentage($query1['achieved'], $query1['required']);
            $current['group_pv']=$query1;
        }
        // if($criteria['rank']){
        //         $this->db->select('r.rank_id');
        //         $this->db->from('rank_promo as r');
        //         $this->db->join('ft_individual as ft' ,'r.id = ft.user_rank_id');
        //         $this->db->where('ft.id',$user_id);
        //         $query3 = $this->db->get()->row_array();
        //         $query3['percentage']=100;
        //         $user_rank_id=$this->validation_model->getUserRank($user_id);
        //         $query3['achieved']=$this->rankPromoName($user_rank_id);
        //         $query3['required']=$this->rankPromoName($query3['rank_id']);
        //         //$current['rank']=$query3;
        //         $query4['percentage']=100;
        //         $query4['bonus_amount']=0;
        //         if(isset($query3['rank_id'])){
        //             $query4['bonus_amount']=$this->getPromoBonus($query3['rank_id']);
        //         }
        //         $current['bonus']=$query4;
        //   }
        
        return $current;
            
    }
    function currentPool($user_id){
        // $this->db->select('promo_rank');
        // $this->db->where('id',$user_id);
        // $res=$this->db->get('ft_individual');
        // return $res->row_array()['promo_rank']??0;
        $this->db->select('r.rank_name as promo_rank,r.id');
        $this->db->from('rank_promo as r');
        $this->db->where('r.status','active');
        $this->db->join('ft_individual as ft' ,'r.id = ft.promo_rank');
        $this->db->where('ft.id',$user_id);
        return $this->db->get()->row_array();
    }
    function getCurrentpoolDetails($rank_id){
        $this->db->select('*');
        $this->db->where('id',$rank_id);
        $this->db->where('status','active');
        $res=$this->db->get('rank_promo');
        return $res->row_array();
    }
    function NextPromoRank($rank_id){
        $this->db->select('group_pv');
        $this->db->where('id',$rank_id);
        $this->db->where('status','active');
        $this->db->limit(1);
        $res=$this->db->get('rank_promo');
        $group_pv=$res->row_array()['group_pv']??0;
        $this->db->select('rank_name as promo_rank,id');
        $this->db->where('group_pv>',$group_pv);
        $this->db->where('status','active');
        $this->db->order_by('group_pv', 'ASC');
        $this->db->limit(1);
        $res=$this->db->get('rank_promo');
        return $res->row_array();
    }
    public function getNextPoolData($nrank,$user_id) {
        //$nrank=$this->getPoolRank($id);
         $criteria = [
            'direct_leg' => TRUE,
            'group_pv' => TRUE,
            'rank' =>TRUE,
        ];

        $next = [];

        if($criteria['direct_leg'])
        {
            $this->db->select('r.direct as required');
            $this->db->where('r.status','active');
            $this->db->from('rank_promo as r');
            $this->db->where('r.id',$nrank);
            $query1 = $this->db->get()->row_array();
            $this->db->select('COUNT(f3.id) as achieved');
            $this->db->from('ft_individual as f3');
            $this->db->where('f3.sponsor_id',$user_id);
            $query2 = $this->db->get()->row_array();
            $query2['percentage'] = $this->calculatePercentage($query2['achieved'], $query1['required']);
            $next['referal_count'] = array_merge($query1, $query2);
            
           }
             $next['group_users']=array();
           if($criteria['group_pv'])
           {
            $this->db->select('r.group_pv as required,r.group_pv_percent');
            $this->db->where('r.status','active');
            $this->db->from('rank_promo as r');
            $this->db->where('r.id',$nrank);
            $query1 = $this->db->get()->row_array();
            // $this->db->select('IFNULL(NULLIF(ft.gpv,""),0) as achieved');
            // $this->db->from('ft_individual as ft');
            // $this->db->where('ft.id',$user_id);
            // $query2 = $this->db->get()->row_array();

            $query2['achieved']=$this->checkGroupPV($user_id,$query1['group_pv_percent'],$query1['required']);
            $query2['percentage'] = $this->calculatePercentage($query2['achieved'], $query1['required']);

            $next['group_pv']=array_merge($query1,$query2);
            $next['group_users']=$this->checkGroupPVByUsers($user_id,$query1['group_pv_percent'],$query1['required']);
            

          } 
        //   if($criteria['rank']){
        //         $this->db->select('id');
        //         $this->db->from('ft_individual');
        //         $this->db->where('user_rank_id',$nrank);
        //         $this->db->where('id',$user_id);
        //         $userid = $this->db->get()->row_array()['id']??0;
        //         $query3['percentage']=0;
        //         if($userid){
        //             $query3['percentage']=100;
        //         }
        //         $query3['required']=$this->rankPromoName($nrank);
        //         $user_rank_id=$this->validation_model->getUserRank($user_id);
        //         $query3['achieved']=$this->rankPromoName($user_rank_id);
        //         $next['rank']=$query3;
        //   }
        //   $query4['percentage']=0;
        //   $query4['bonus_amount']=$this->getPromoBonus($nrank);
        //   $next['bonus']=$query4;
        return $next;
    }
    function getPoolRank($id){
        $this->db->select('id');
        $this->db->where('id',$id);
        $res=$this->db->get('rank_promo');
        return $res->row_array()['id']??0;
    }
    function rankPromoName($rank_id){
        $this->db->select('rank_name');
        $this->db->where('id',$rank_id);
        $res=$this->db->get('rank_details');
        return $res->row_array()['rank_name']??'NA';
    }
    function getGroupPv($user_id){
        $this->db->select('gpv');
        $this->db->where('id',$user_id);
        $res=$this->db->get('ft_individual');
        return $res->row_array()['gpv']??0;;
    }
    function getSponsorCount($user_id){
        $this->db->where('sponsor_id',$user_id);
        return $this->db->count_all_results('ft_individual');
    }
    function getPromoBonus($rank_id){
        $this->db->select('bonus');
        $this->db->where('id',$rank_id);
        $res=$this->db->get('rank_promo');
        return $res->row_array()['bonus']??0;
    }
    function checkGroupPV($user_id,$gpv_percent,$group_pv){
        $promo = $this->validation_model->getConfig(['promo_start_date', 'promo_end_date']);
        $max_per_leg=($group_pv*$gpv_percent)/100;

        $allowed_gpv=0;
        $this->db->select('id,personal_pv,gpv');
        $this->db->where('id',$user_id);
        $res1=$this->db->get('ft_individual');
        $user_gpv=$res1->row_array()['personal_pv']??0;
        $this->db->select('id,personal_pv,gpv,combo_user');
        $this->db->where('sponsor_id',$user_id);
        $res=$this->db->get('ft_individual');
        foreach($res->result_array() as $row){
            //$total_gpv=$row['personal_pv']+$row['gpv'];
            $total_gpv=$this->userGroupPVForDownline($row['id'],$promo);
            $gpv=($total_gpv>$max_per_leg)?$max_per_leg:$total_gpv;
            $allowed_gpv+=$gpv;
        }
        return $allowed_gpv+$user_gpv;
    }
    public function checkNewPromoRank($referal_count, $personal_pv, $group_pv, $user_id, $curent_rank)
    {
        
        //$summary_details = $this->getRankSummary($user_id);
        
        if(empty($group_pv))
        {
            $group_pv = 0;
        }
        $criteria = [
            'referal_count' => TRUE,
            'group_pv' =>TRUE,
        ];
        if($curent_rank != NULL) {
            $this->db->select('group_pv');
            $this->db->where('id',$curent_rank);
            $this->db->limit(1);
            $res=$this->db->get('rank_promo');
            $current_group_pv=$res->row_array()['group_pv']??0;
        }

        $this->db->select('r.id,r.rank_name,r.group_pv,r.group_pv_percent');
        $this->db->from('rank_promo r');

        if ($criteria['referal_count']) {
            $this->db->where('r.direct <=', $referal_count);
        }
        if ($criteria['group_pv']) {

            $this->db->where('r.group_pv <=', $group_pv);
        }
        if($curent_rank != NULL) {
            //$this->db->where('r.id >', $curent_rank);
            $this->db->where('r.group_pv >', $current_group_pv);
        }


        $this->db->where('r.status', 'active');
        $this->db->order_by('r.id', 'ASC');

        $query = $this->db->get();

        $rank_id = $curent_rank;

        foreach ($query->result() as $row) {
            if($this->checkGroupPVNew($user_id,$row->group_pv_percent,$row->group_pv)){
                $rank_id = $row->id;
                $this->insertIntoPromoRankHistory($curent_rank, $rank_id, $user_id);
                $curent_rank = $rank_id;
            }
        }

        return $rank_id;
    }
    function checkGroupPVNew($user_id,$gpv_percent,$group_pv){
        $max_per_leg=($group_pv*$gpv_percent)/100;
        $promo = $this->validation_model->getConfig(['promo_start_date', 'promo_end_date']);
        $allowed_gpv=0;
        $this->db->select('id,personal_pv,gpv');
        $this->db->where('id',$user_id);
        $res1=$this->db->get('ft_individual');
        $user_gpv=$res1->row_array()['personal_pv']??0;
        $this->db->select('id,personal_pv,gpv');
        $this->db->where('sponsor_id',$user_id);
        $res=$this->db->get('ft_individual');
        foreach($res->result_array() as $row){
            //$total_gpv=$row['personal_pv']+$row['gpv'];
            //$total_gpv=$this->userGroupPV($row['id'],$promo);
            $total_gpv=$this->userGroupPVForDownline($row['id'],$promo);
            $gpv=($total_gpv>$max_per_leg)?$max_per_leg:$total_gpv;
            $allowed_gpv+=$gpv;
        }
        $allowed_gpv+=$user_gpv;
        return ($allowed_gpv>=$group_pv)?TRUE:FALSE;
    }
    public function insertIntoPromoRankHistory($old_rank, $new_rank, $user_id) {
        $date = date('Y-m-d H:i:s');
        $this->db->set('user_id', $user_id);
        $this->db->set('current_rank', $old_rank);
        $this->db->set('new_rank', $new_rank);
        $this->db->set('date', $date);
        $res = $this->db->insert('promo_rank_history');
        return $res;
    }
    public function updateUserPromoRank($id, $rank) {
        $this->db->set('promo_rank', $rank);
        $this->db->where('id', $id);
        $result = $this->db->update('ft_individual');
        return $result;
    }
    public function rankPromoBonus($new_rank, $user_id, $purchse_user_id) {

        $rank_bonus     = $this->getPromoBonus($new_rank);
        if($rank_bonus){
            $date_of_sub    = date("Y-m-d H:i:s");
            $amount_type    = "rank_promo_bonus";
            $obj_arr        = $this->validation_model->getSettings();
            $tds_db         = $obj_arr["tds"];
            $service_charge = $obj_arr["service_charge"];
            $rank_amount    = $rank_bonus;
            $tds_amount     = ($rank_amount * $tds_db) / 100;
            $service_charge = ($rank_amount * $service_charge) / 100;
            $amount_payable = $rank_amount - ($tds_amount + $service_charge);
    
            $this->calculation_model->insertRankBonus($user_id, $rank_amount, $amount_payable, $tds_amount, $service_charge, $date_of_sub, $amount_type, $purchse_user_id, 1);
            $voucher=$this->getPromoVoucher($new_rank);
            if($voucher){
                $this->load->model('cron_model');
                $this->cron_model->insertVoucherHistory($user_id,$voucher,$new_rank);
            }
        }
    }
    function getPromoVoucher($rank_id){
        $this->db->select('voucher');
        $this->db->where('id',$rank_id);
        $res=$this->db->get('rank_promo');
        return $res->row_array()['voucher']??0;
    }
    function userGroupPV($user_id,$promo){
        $this->db->select('sum(group_pv) as gpv,sum(personal_pv) as pv');
        $this->db->where('user_id',$user_id);
        $this->db->where('date>=',$promo['promo_start_date']);
        $this->db->where('date<=',$promo['promo_end_date']);
        $res=$this->db->get('pv_history_details');
        $gpv=$res->row_array()['gpv']??0;
        $pv=$res->row_array()['pv']??0;
        $gpv=0;
        return $gpv+$pv;
    }
    function checkGroupPVByUsers($user_id,$gpv_percent,$group_pv){
        $promo = $this->validation_model->getConfig(['promo_start_date', 'promo_end_date']);
        $max_per_leg=($group_pv*$gpv_percent)/100;
        $allowed_gpv=0;
        $details=array();
        $this->db->select('id,user_name,personal_pv,gpv');
        $this->db->where('id',$user_id);
        $res1=$this->db->get('ft_individual');
        $user_gpv=$res1->row_array()['personal_pv']??0;
        $details[0]=$res1->row_array();
        $details[0]['gpv']=$user_gpv;
        $this->db->select('id,user_name,personal_pv,gpv');
        $this->db->where('sponsor_id',$user_id);
        $res=$this->db->get('ft_individual');
        
        $i=1;
        foreach($res->result_array() as $row){
            //$total_gpv=$row['personal_pv']+$row['gpv'];
            //$total_gpv=$this->userGroupPV($row['id'],$promo);
            $total_gpv=$this->userGroupPVForDownline($row['id'],$promo);
            $gpv=($total_gpv>$max_per_leg)?$max_per_leg:$total_gpv;
            $details[$i]=$row;
            $details[$i]['gpv']=$gpv;
            $i++;
        }
        return $details;
    }
    function userGroupPVForDownline($user_id,$promo){
        $this->db->select('sum(group_pv) as gpv,sum(personal_pv) as pv');
        $this->db->where('user_id',$user_id);
        $this->db->where('date>=',$promo['promo_start_date']);
        $this->db->where('date<=',$promo['promo_end_date']);
        $res=$this->db->get('pv_history_details');
        $gpv=$res->row_array()['gpv']??0;
        $pv=$res->row_array()['pv']??0;
        $pv=0;
        //$gpv=0;
        return $gpv+$pv;
    }


}
