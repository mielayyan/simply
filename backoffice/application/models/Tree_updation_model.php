<?php

class Tree_updation_model extends inf_model {

    function __construct() {
        parent::__construct();
        $this->load->model('tree_model');
    }

    public function deleteUser($user_id, $mlm_plan, $opencart_status) {
        $this->startTransaction();

        // $tree_details = $this->getTreeDetailsToDelete($user_id); unused fun
        // $sponsor_tree_details = $this->getTreeDetailsToDelete($user_id, 'sponsor_tree'); unused fun

        // gather required data
        $customer_id = $this->validation_model->getOcCustomerId($user_id);
        $user_data = $this->getUserData($user_id, $mlm_plan, $opencart_status, $customer_id);
        $father_id = unserialize($user_data["ft_details"])["father_id"];
        $position = unserialize($user_data["ft_details"])["position"];
        $leg_position = unserialize($user_data["ft_details"])["leg_position"]; 

        // delete user details entries
        $this->deleteUserData($user_id, $mlm_plan, $opencart_status, $customer_id);
        // $this->deleteUserFromFt($user_id); may use
        $this->markDeletedUser($user_id);

        // remove from genealogy and sponsor trees
        $this->db->where("ancestor", $user_id)
                ->or_where("descendant", $user_id)
                ->delete("treepath");
        $this->db->where("ancestor", $user_id)
                ->or_where("descendant", $user_id)
                ->delete("sponsor_treepath");

        // $this->deleteTreeNode($tree_details); unused fun
        // $this->deleteTreeNode($sponsor_tree_details, 'sponsor_tree'); unused fun

        // $this->updateSiblingNodes($tree_details, $mlm_plan); unused fun

        // update siblings leg_position
        if($mlm_plan != "Binary") {
            $this->db->set("position", "position - 1", false)
                    ->set("position", "position - 1", false)
                    ->where("father_id", $father_id)
                    ->where("position >", $position)
                    ->where("leg_position >", $leg_position)
                    ->update("ft_individual");
        }
        // save deleted user data
        $this->insertDeletedUserData($user_data);
        ///

        $this->finishTransaction();
        return $this->isTransactionSuccess();
    }

    public function markDeletedUser($user_id)
    {
        return $this->db->where("id", $user_id)
                ->update("ft_individual", [
                    "position" => NULL,
                    "leg_position" => NULL,
                    "father_id" => NULL,
                    "sponsor_id" => NULL,
                    "user_level" => NULL,
                    "sponsor_level" => NULL,
                    "delete_status" => "deleted",
                ]);
    }

    public function deleteUserFromFt($user_id)
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS=0;');
        $reference_columns = [ // "tables" => "columns" which refers ft_individual.id as foreign key
            "amount_paid" => ["paid_user_id"],
            "authorize_payment_details" => ["user_id"],
            "bitcoin_payment_details" => ["user_id"],
            "bitcoin_payout_release_error_report" => ["from_user","to_user_id"],
            "bitgo_payment_history" => ["user_id"],
            "bitgo_payout_release_history" => ["user_id"],
            "blockchain_payout_release_history" => ["user_id"],
            "epin_transfer_history" => ["from_user_id","user_id"],
            "ewallet_history" => ["from_id","user_id"],
            "ewallet_payment_details" => ["used_user_id","user_id"],
            "feedback" => ["feedback_user_id"],
            "ft_individual" => ["father_id","sponsor_id"],
            "fund_transfer_details" => ["from_user_id","to_user_id"],
            "infinite_user_registration_details" => ["placement_id","sponsor_id","user_id"],
            "invite_history" => ["user_id"],
            "kyc_docs" => ["user_id"],
            "leg_amount" => ["from_id","user_id"],
            "mail_from_lead" => ["mail_from","mail_to"],
            "mail_from_lead_cumulative" => ["mail_from"],
            "mailtoadmin" => ["mailaduser"],
            "mailtouser" => ["mailfromuser","mailtoususer"],
            "package_upgrade_history" => ["user_id"],
            "package_validity_extend_history" => ["user_id"],
            "password_reset_table" => ["user_id"],
            "payment_registration_details" => ["user_id"],
            "payout_release_requests" => ["requested_user_id"],
            "pending_registration" => ["updated_id"],
            "pin_numbers" => ["allocated_user_id","generated_user_id"],
            "pin_purchases" => ["allocated_user_id","generated_user_id","used_user"],
            "pin_request" => ["req_user_id"],
            "pin_used" => ["used_user"],
            "placement_change_history" => ["new_placement_id","old_placement_id","user_id"],
            "purchase_wallet_history" => ["from_user_id","user_id"],
            "rank_history" => ["user_id"],
            "replica_banner" => ["user_id"],
            "repurchase_address" => ["user_id"],
            "repurchase_order" => ["user_id"],
            "sales_order" => ["user_id"],
            "sponsor_change_history" => ["new_sponsor_id","old_sponsor_id","user_id"],
            "ticket_tickets" => ["user_id"],
            "to_do_list" => ["user_id"],
            "tran_password" => ["user_id"],
            "tran_password_reset_table" => ["user_id"],
            "tree_parser" => ["ft_id"],
            "upgrade_sales_order" => ["user_id"],
            "user_activation_deactivation_history" => ["user_id"],
            "user_balance_amount" => ["user_id"],
            // "user_deletion_history" => ["user_id"],
            "user_details" => ["user_detail_refid","user_details_ref_user_id"],
        ];

        foreach ($reference_columns as $table => $columns) {
            foreach ($columns as $column) {
                $this->db->set($column, NULL)
                        ->where($column, $user_id)
                        ->update($table);
            }
        }

        $this->db->where("id", $user_id)->delete("ft_individual");
        $this->db->query('SET FOREIGN_KEY_CHECKS=1;');

        return true;

    }

    public function deleteUserOld($user_id, $mlm_plan, $opencart_status) {
        $this->startTransaction();
        $customer_id = $this->validation_model->getOcCustomerId($user_id);
        $user_data = $this->getUserData($user_id, $mlm_plan, $opencart_status, $customer_id);
        $tree_details = $this->getTreeDetailsToDelete($user_id);
        $sponsor_tree_details = $this->getTreeDetailsToDelete($user_id, 'sponsor_tree');
        $this->deleteUserData($user_id, $mlm_plan, $opencart_status, $customer_id);
        $this->deleteTreeNode($tree_details);
        $this->deleteTreeNode($sponsor_tree_details, 'sponsor_tree');
        $this->updateSiblingNodes($tree_details, $mlm_plan);
        $this->insertDeletedUserData($user_data);
        $this->finishTransaction();
        return $this->isTransactionSuccess();
    }

    public function insertDeletedUserData($user_data) {
        $this->db->insert('user_deletion_history', $user_data);
    }

    public function deleteUserData($user_id, $mlm_plan, $opencart_status, $customer_id) {
        $this->db->where('user_detail_refid', $user_id);
        $this->db->delete('user_details');

        // $this->db->where('user_id', $user_id);
        // $this->db->delete('infinite_user_registration_details');

        $this->db->where('user_id', $user_id);
        $this->db->delete('user_balance_amount');

        $this->db->where('user_id', $user_id);
        $this->db->delete('tran_password');

        if($mlm_plan == 'Binary') {
            $this->db->where('id', $user_id);
            $this->db->delete('leg_details');
        }
        elseif($mlm_plan == 'Party') {
            $this->db->where('added_by', $user_id);
            $this->db->delete('party');

            $this->db->where('added_by', $user_id);
            $this->db->delete('party_guest');

            $this->db->where('added_by', $user_id);
            $this->db->delete('party_guest_invited');

            $this->db->where('added_by', $user_id);
            $this->db->delete('party_host');
        }
        elseif($mlm_plan == 'Board') {
            $this->db->where('user_ref_id', $user_id);
            $this->db->delete('auto_board_1');

            $this->db->where('user_ref_id', $user_id);
            $this->db->delete('auto_board_2');

            $this->db->where('board_top_id', $user_id);
            $this->db->delete('board_view');

            $this->db->where('user_id', $user_id);
            $this->db->delete('board_user_detail');
        }
        elseif($mlm_plan == 'Stair_Step') {
            $this->db->where('user_id', $user_id);
            $this->db->delete('stair_step');
        }
        if($opencart_status == 'yes') {
            $this->db->where('customer_id', $customer_id);
            $this->db->delete('oc_customer');

            $this->db->where('customer_id', $customer_id);
            $this->db->delete('oc_address');
        }
    }

    public function getUserData($user_id, $mlm_plan, $opencart_status, $customer_id) {

        $user_name = $this->validation_model->IdToUserName($user_id);

        $this->db->where('id', $user_id);
        $ft_details = $this->db->get('ft_individual')->row_array();

        $this->db->where('user_detail_refid', $user_id);
        $user_details = $this->db->get('user_details')->row_array();

        $this->db->where('user_id', $user_id);
        $registration_details = $this->db->get('infinite_user_registration_details')->row_array();

        $leg_details = array();
        if($mlm_plan == 'Binary') {
            $this->db->where('id', $user_id);
            $leg_details = $this->db->get('leg_details')->row_array();
        }

        $this->db->select('balance_amount');
        $this->db->where('user_id', $user_id);
        $ewallet_balance = $this->db->get('user_balance_amount')->row_array()['balance_amount'];

        $this->db->select('tran_password');
        $this->db->where('user_id', $user_id);
        $tran_password = $this->db->get('tran_password')->row_array()['tran_password'];

        $customer_details = array();
        $customer_address = array();
        if($opencart_status == 'yes') {
            $this->db->where('customer_id', $customer_id);
            $customer_details = $this->db->get('oc_customer')->row_array();

            $this->db->where('customer_id', $customer_id);
            $customer_address = $this->db->get('oc_address')->row_array();
        }

        $user_data = array(
            'user_id' => $user_id,
            'user_name' => $user_name,
            'ewallet_balance' => ($ewallet_balance)?$ewallet_balance:0,
            'tran_password' => ($tran_password)?$tran_password:"",
            'ft_details' => serialize($ft_details),
            'registration_details' => serialize($registration_details),
            'user_details' => serialize($user_details),
            'leg_details' => serialize($leg_details),
            'customer_details' => serialize($customer_details),
            'customer_address' => serialize($customer_address)
        );

        return $user_data;
    }

    public function updateSiblingNodes($tree_details, $mlm_plan) {
        $this->load->model('registersubmit_model');
        if($mlm_plan == 'Matrix' || $mlm_plan == 'Board' || $mlm_plan == "Unilevel" || $mlm_plan == "Stair_Step" || $mlm_plan == "Party" || $mlm_plan == "Donation") {
            $this->db->set('position', 'position - 1', FALSE);
            $this->db->where('position >', $tree_details['position']);
            $this->db->where('father_id', $tree_details['parent_id']);
            $this->db->update('ft_individual');

        }

    }

    public function deleteTreeNode($tree_details, $type = 'tree') {
        $left = $tree_details['left'];
        $right = $tree_details['right'];
        $width = $tree_details['width'];
        $has_leafs = $tree_details['has_leafs'];
        $parent_id = $tree_details['parent_id'];
        if($type == 'tree') {
            $this->db->where("left_father >=", $left);
            $this->db->where("left_father <=", $right);
            $this->db->delete('tree_parser');

            $this->db->set('right_father', "right_father - $width", FALSE);
            $this->db->where('right_father > ', $right);
            $this->db->update('tree_parser');

            $this->db->set('left_father', "left_father - $width", FALSE);
            $this->db->where('left_father > ', $right);
            $this->db->update('tree_parser');
        }
        elseif($type == 'sponsor_tree') {
            if($has_leafs == 1) {
                $this->db->where("left_sponsor BETWEEN $left AND $right");
                $this->db->delete('tree_parser');

                $this->db->set('right_sponsor', "right_sponsor - $width", FALSE);
                $this->db->where('right_sponsor > ', $right);
                $this->db->update('tree_parser');

                $this->db->set('left_sponsor', "left_sponsor - $width", FALSE);
                $this->db->where('left_sponsor > ', $right);
                $this->db->update('tree_parser');
            }
            else {
                $db_prefix  = $this->db->dbprefix;
                $this->db->where('left_sponsor', $left);
                $this->db->delete('tree_parser');

                // $this->db->set('ft.sponsor_id', $parent_id,FALSE);
                // $this->db->update('ft_individual as ft join tree_parser as t on ft.id = t.ft_id');
                // $this->db->where('t.left_sponsor',$left + 1);
                $this->db->query("UPDATE `{$db_prefix}ft_individual` Join `{$db_prefix}tree_parser` on `{$db_prefix}ft_individual`.id = `{$db_prefix}tree_parser`.ft_id  SET `{$db_prefix}ft_individual`.sponsor_id = {$parent_id} WHERE `{$db_prefix}tree_parser`.left_sponsor = $left+1 ;");
                // $this->db->set('t.right_sponsor', "t.right_sponsor - 1", FALSE);
                // $this->db->set('t.left_sponsor', "t.left_sponsor - 1", FALSE);
                // $this->db->set('f.sponsor_level', 'f.sponsor_level - 1', FALSE);
                // $this->db->where("t.left_sponsor BETWEEN $left AND $right");
                // $this->db->update('ft_individual as f join tree_parser as t  on f.id = t.ft_id');
                $this->db->query("UPDATE `{$db_prefix}ft_individual` Join `{$db_prefix}tree_parser` on `{$db_prefix}ft_individual`.id = `{$db_prefix}tree_parser`.ft_id  SET `{$db_prefix}ft_individual`.sponsor_level = `{$db_prefix}ft_individual`.sponsor_level - 1 ,`{$db_prefix}tree_parser`.right_sponsor = `{$db_prefix}tree_parser`.right_sponsor - 1 , `{$db_prefix}tree_parser`.left_sponsor = `{$db_prefix}tree_parser`.left_sponsor - 1  WHERE `{$db_prefix}tree_parser`.left_sponsor BETWEEN {$left} AND {$right} ;");

                $this->db->set('right_sponsor', "right_sponsor - 2", FALSE);
                $this->db->where('right_sponsor > ', $right);
                $this->db->update('tree_parser');

                $this->db->set('left_sponsor', "left_sponsor - 2", FALSE);
                $this->db->where('left_sponsor > ', $right);
                $this->db->update('tree_parser');
            }
        }
    }

    public function hasChildren($user_id, $tree_type = 'tree') {
        if($tree_type == 'tree') {
            $this->db->where('father_id', $user_id);
        }
        elseif($tree_type == 'sponsor_tree') {
            $this->db->where('sponsor_id', $user_id);
        }
        $count = $this->db->count_all_results('ft_individual');
        return ($count > 0);
    }

    public function getTreeDetailsToDelete($user_id, $tree_type = 'tree') {
        if($tree_type == 'tree') {
            $this->db->select('(t.right_father - t.left_father) has_leafs', FALSE);
            $this->db->select('(t.right_father - t.left_father + 1) width', FALSE);
            $this->db->select('t.left_father left,t.right_father right,f.position,f.father_id parent_id');
        }
        elseif($tree_type == 'sponsor_tree') {
            $this->db->select('(t.left_sponsor - t.right_sponsor) has_leafs', FALSE);
            $this->db->select('(t.left_sponsor - t.right_sponsor + 1) width', FALSE);
            $this->db->select('t.left_sponsor left,t.right_sponsor right,f.position,f.sponsor_id parent_id');
        }
        $this->db->from('ft_individual f');
        $this->db->join('tree_parser t', 't.ft_id = f.id', 'LEFT');
        $this->db->where('f.id', $user_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function isNewPlacementInsideTeam($user_id, $new_placement_id, $tree_type = 'tree') {
        $this->db->where("ancestor", $user_id);
        $this->db->where("descendant", $new_placement_id);
        $count = 0;
        if($tree_type == 'tree') {
            $count = $this->db->count_all_results("treepath");
        } elseif($tree_type == 'sponsor_tree') {
            $count = $this->db->count_all_results("sponsor_treepath");
        }
        return ($count > 0);
    }

    public function changeSponsor($user_id, $new_sponsor_id) {
        $current_sposnsor_id = $this->validation_model->getSponsorId($user_id);
        $this->startTransaction();

        // cut off the sub tree from base
        $array1 = $this->db->select("ancestor")
                        ->where("descendant", $user_id)
                        ->where("ancestor !=", $user_id)
                        ->get("sponsor_treepath")->result_array();

        $array2 = $this->db->select("descendant")
                        ->where("ancestor", $user_id)
                        ->get("sponsor_treepath")->result_array();

        $oldRootMembers = array_column($array1, "ancestor");
        $subTreeMembers = array_column($array2, "descendant");

        $this->db->where_in("ancestor", $oldRootMembers)
                ->where_in("descendant", $subTreeMembers)
                ->delete("sponsor_treepath");

        // place the sub tree on new base

        $array3 = $this->db->select("ancestor")
                        ->where("descendant", $new_sponsor_id)
                        ->get("sponsor_treepath")->result_array();
        $newRootMembers = array_column($array3, "ancestor");

        $sponsorTreepathData = [];
        foreach ($newRootMembers as $ancestor) {
            foreach ($subTreeMembers as $descendant) {
                $sponsorTreepathData[] = compact("ancestor", "descendant");
            }
        }

        $this->db->insert_batch("sponsor_treepath", $sponsorTreepathData);

        $this->changeSponsorId($user_id, $new_sponsor_id);

        // update sponsor levels

        $oldRootSposnsorDepth = $this->validation_model->getUserTreeLevel($current_sposnsor_id, 'sponsor_tree');
        $newRootSposnsorDepth = $this->validation_model->getUserTreeLevel($new_sponsor_id, 'sponsor_tree');
        $addLevel = $newRootSposnsorDepth - $oldRootSposnsorDepth;
        if($addLevel != 0) {
            $this->db->set("sponsor_level", "sponsor_level + $addLevel", false)
                    ->where_in("id", $subTreeMembers)
                    ->update("ft_individual");
        }

        ///

        $this->finishTransaction();
        return $this->isTransactionSuccess();
    }

    public function updateSubtreeLevel($user_id, $parent_id, $tree_type = 'tree') {
        $user_left_right = $this->tree_model->getUserLeftRightNode($user_id, $tree_type);
        $user_level = $this->validation_model->getUserTreeLevel($user_id, $tree_type);
        $parent_level = $this->validation_model->getUserTreeLevel($parent_id, $tree_type);
        $level_diff = $parent_level - $user_level + 1;
        $db_prefix  = $this->db->dbprefix;

        if($tree_type == 'tree') {
            // $this->db->set('user_level', "user_level + $level_diff", FALSE);
            // $this->db->where('left_father >=', $user_left_right['left']);
            // $this->db->where('right_father <=', $user_left_right['right']);
            $this->db->query("UPDATE `{$db_prefix}ft_individual` Join `{$db_prefix}tree_parser` on `{$db_prefix}ft_individual`.id = `{$db_prefix}tree_parser`.ft_id  SET `{$db_prefix}ft_individual`.user_level = `{$db_prefix}ft_individual`.user_level + {$level_diff}  WHERE `{$db_prefix}tree_parser`.left_father >= {$user_left_right['left']} AND `{$db_prefix}tree_parser`.right_father <= {$user_left_right['right']};");
        }
        elseif($tree_type == 'sponsor_tree') {
            // $this->db->set('sponsor_level', "sponsor_level + $level_diff", FALSE);
            // $this->db->where('left_sponsor >=', $user_left_right['left']);
            // $this->db->where('right_sponsor <=', $user_left_right['right']);
            $this->db->query("UPDATE `{$db_prefix}ft_individual` Join `{$db_prefix}tree_parser` on `{$db_prefix}ft_individual`.id = `{$db_prefix}tree_parser`.ft_id  SET `{$db_prefix}ft_individual`.sponsor_level = `{$db_prefix}ft_individual`.sponsor_level + {$level_diff}  WHERE `{$db_prefix}tree_parser`.left_sponsor >= {$user_left_right['left']} AND `{$db_prefix}tree_parser`.right_sponsor <= {$user_left_right['right']};");
        }
    }

    public function changeSponsorId($user_id, $new_sponsor_id) {
        $old_sponsor_id = $this->validation_model->getSponsorId($user_id);

        $this->db->set('sponsor_id', $new_sponsor_id);
        $this->db->where('id', $user_id);
        $this->db->update('ft_individual');

        $this->db->set('user_id', $user_id);
        $this->db->set('old_sponsor_id', $old_sponsor_id);
        $this->db->set('new_sponsor_id', $new_sponsor_id);
        $this->db->insert('sponsor_change_history');
    }

    public function changePlacementId($user_id, $new_placement_id, $new_position, $old_placement_id, $old_position, $leg_position) {
        $this->db->set("leg_position", $leg_position);
        $this->db->set('father_id', $new_placement_id);
        $this->db->set('position', $new_position);
        $this->db->where('id', $user_id);
        $this->db->update('ft_individual');

        $this->db->set('user_id', $user_id);
        $this->db->set('old_placement_id', $old_placement_id);
        $this->db->set('new_placement_id', $new_placement_id);
        $this->db->set('old_position', $old_position);
        $this->db->set('new_position', $new_position);
        $this->db->insert('placement_change_history');
    }

    public function isPositionAvailable($placement_id, $position, $mlm_plan) {
        if($mlm_plan == 'Binary') {
            $this->db->where('father_id', $placement_id);
            $this->db->where('position', $position);
            $count = $this->db->count_all_results('ft_individual');
            return !$count;
        }
        elseif($mlm_plan == 'Matrix') {
            $width_ceiling = $this->validation_model->getWidthCieling();

            $this->db->where('father_id', $placement_id);
            $count = $this->db->count_all_results('ft_individual');
            return ($count < $width_ceiling);
        }
        else {
            return TRUE;
        }
    }

    public function changePlacement($user_id, $new_placement_id, $current_placement_id, $mlm_plan, $new_position, $current_position) {
        $this->startTransaction();

        //cut off the sub_tree
        $array1 = $this->db->select("descendant")
                        ->where("ancestor", $user_id)
                        ->get("treepath")->result_array();
        
        $array2 = $this->db->select("ancestor")
                        ->where("descendant", $user_id)
                        ->where("ancestor !=", $user_id)
                        ->get("treepath")->result_array();

        $subTreeMembers = array_column($array1, "descendant");
        $currentRootMembers = array_column($array2, "ancestor");

        $this->db->where_in("ancestor", $currentRootMembers)
                ->where_in("descendant", $subTreeMembers)
                ->delete("treepath");
        
        // place sub_tree on new root
        $array3 = $this->db->select("ancestor")
                        ->where("descendant", $new_placement_id)
                        ->get("treepath")->result_array();

        $newRootMembers = array_column($array3, "ancestor");

        $treepathArray = [];
        foreach ($subTreeMembers as $descendant) {
            foreach ($newRootMembers as $ancestor) {
                $treepathArray[] = compact("ancestor", "descendant");
            }
        }

        $this->db->insert_batch("treepath", $treepathArray);

        // update user levels

        $oldRootUserDepth = $this->validation_model->getUserLevel($current_placement_id);
        $newRootUserDepth = $this->validation_model->getUserLevel($new_placement_id);
        $addLevel = $newRootUserDepth - $oldRootUserDepth;
        if($addLevel != 0) {
            $this->db->set("user_level", "user_level + $addLevel", false)
                    ->where_in("id", $subTreeMembers)
                    ->update("ft_individual");
        }

        ///

        if(strtolower($mlm_plan) == "binary") {
            $leg_position = ($new_position == "R") ? 2 : 1;
        } else {
            // assign new leg position
            $count = $this->db->where("father_id", $new_placement_id)->count_all_results("ft_individual");
            $new_position = $leg_position = $count + 1;
        }

        // change father id and placement position
        $this->changePlacementId($user_id, $new_placement_id, $new_position, $current_placement_id, $current_position, $leg_position);

        if(strtolower($mlm_plan) != "binary") {
            // change leg_position of old siblings
            $where = ["father_id" => $current_placement_id, "position >" => $current_position];
            $update_array = $this->db->select('(position - 1) as position, (position - 1) as leg_position, id')
                    ->where($where)
                    ->order_by('position')
                    ->get('ft_individual')->result_array();
            $query = '';
            // foreach is needed to avoid breaking of uniqueness of (father_id - position)
            foreach($update_array as $update_row) {
                $this->db->where('id', $update_row['id'])
                        ->update("ft_individual",
                            ['position' => $update_row['position'],'leg_position' => $update_row['leg_position']]);
            }
        }

        $this->finishTransaction();
        return $this->isTransactionSuccess();
    }

    function refreshTreepathTables() {
        // trunkate the treepath tables
        $this->db->query("TRUNCATE {$this->db->dbprefix}sponsor_treepath");
        $this->db->query("TRUNCATE {$this->db->dbprefix}treepath");
        //
        
        $treepath_columns = [];
        $user_id_array = $this->db->select("id")
                                ->where("delete_status", "active")
                                ->get("ft_individual")->result_array();
        $user_id_array = array_column($user_id_array, "id");
        //
        foreach ($user_id_array as $user_id) {
            $treepath_columns[] = ["ancestor" => $user_id, "descendant" => $user_id]; // one is one's own ancestor
            $check_user_id = $user_id;
            // finding other ancestors (only)

            findfather: // might want to come back
            $father_id = $this->db->select("father_id")
                                ->where("id", $check_user_id)
                                ->get("ft_individual")
                                ->row_array()["father_id"];

            if ($father_id) {
                $treepath_columns[] = ["ancestor" => $father_id, "descendant" => $user_id]; // add to ancestor
                // going back to find father of the father
                $check_user_id = $father_id;
                goto findfather;
                //
            }
            //
        }

        $sponsor_treepath_columns = [];
        $user_id_array = $this->db->select("id")
                                ->where("delete_status","active")
                                ->get("ft_individual")->result_array();
        $user_id_array = array_column($user_id_array, "id");
        //
        foreach ($user_id_array as $user_id) {
            $sponsor_treepath_columns[] = ["ancestor" => $user_id, "descendant" => $user_id]; // one is one's own ancestor
            $check_user_id = $user_id;
            // finding other ancestors (only)

            findsponsor: // might want to come back
            $sponsor_id = $this->db->select("sponsor_id")
                                ->where("id", $check_user_id)
                                ->get("ft_individual")
                                ->row_array()["sponsor_id"];

            if ($sponsor_id) {
                $sponsor_treepath_columns[] = ["ancestor" => $sponsor_id, "descendant" => $user_id]; // add to ancestor
                // going back to find sponsor of the sponsor
                $check_user_id = $sponsor_id;
                goto findsponsor;
                //
            }
            //
        }

        // insert new tree paths
        if(count($treepath_columns)) {
            $this->db->insert_batch("treepath", $treepath_columns);
        }
        if(count($sponsor_treepath_columns)) {
            $this->db->insert_batch("sponsor_treepath", $sponsor_treepath_columns);
        }
        
        return true;
    }

}
