<?php

class binary_model extends inf_model
{

    public $upline_id_arr;
    public $upline_sponsor_arr;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('calculation_model');
        $this->load->model('validation_model');
        $this->load->model('configuration_model');
        $this->upline_id_arr = [];
        $this->upline_sponsor_arr = [];
    }

    public function getPlacementAndPositionOld($placement_id, $position, $reg_from_tree = FALSE)
    {
        if ($reg_from_tree) {
            return NULL;
        }
        else {
            $placement_array = NULL;
            $this->db->select('id');
            $this->db->from('ft_individual');
            $this->db->where('father_id', $placement_id);
            $this->db->where('position', $position);
            $query = $this->db->get();
            $row_count = $query->num_rows();
            if ($row_count > 0) {
                return $this->getPlacementAndPositionOld($query->row_array()['id'], $position);
            }
            else {
                $placement_array['id'] = $placement_id;
                $placement_array['position'] = $position;
            }
            return $placement_array;
        }
    }

    public function getPlacementAndPosition($placement_id, $position, $reg_from_tree = FALSE)
    {
        if ($reg_from_tree) {
            return NULL;
        }

        $target_position = ($position == 'R')?2:1;
        $exclude_position = 3 - $target_position;
        $placement_id_level = $this->validation_model->getUserLevel($placement_id);
        if(!$placement_id_level) {
            $placement_id_level = 0;
        }
        $array = $this->db->select("ft.id, (SELECT COUNT(*) FROM `{$this->db->dbprefix}ft_individual` WHERE `father_id` = `tp`.`descendant` AND `leg_position` = '$target_position') AS leg_used, GROUP_CONCAT(`fta`.`leg_position` ORDER BY `fta`.`user_level` ASC SEPARATOR '') as positionString")
                ->from('treepath as tp')
                ->join('ft_individual as ft', 'ft.id = tp.descendant')
                ->join('treepath as tpa', 'tpa.descendant = tp.descendant', 'LEFT')
                ->join('ft_individual as fta', 'fta.id = tpa.ancestor')
                ->group_by('tp.descendant')
                ->where('tp.ancestor', $placement_id)
                ->where('fta.user_level >', $placement_id_level)
                ->having('leg_used <=', 0)
                ->having("positionString NOT LIKE '%$exclude_position%'")
                ->limit(1)->get()->row_array();
                
        if($array && count($array)) {
            return ['id' => $array['id'], 'position' => $position];
        }
        return ['id' => $placement_id, 'position' => $position];
    }

    public function addBySpecificPlan($user_id)
    {
        $this->db->set('id', $user_id);
        $this->db->insert('leg_details');
    }

    public function runCalculation($action, $user_id, $product_id, $product_pv, $product_amount, $oc_order_id, $upline_id, $quantity, $position, $data)
    {
        $this->calculateBinaryCommission($action, $user_id, $upline_id, $position, $product_id, $product_pv, $product_amount, $oc_order_id);

        $level_commission_status = $this->validation_model->getCompensationConfig('sponsor_commission_status');

        if ($level_commission_status == 'yes') {
            $_is_sale_ok = $this->validation_model->getCompensationConfig(['sales_commission']);
            if($_is_sale_ok == "yes" && $action == "repurchase"){
                $this->calculation_model->calculateSaleCommission($action, $user_id, $data['sponsor_id'], $product_id, $product_pv, $product_amount, $oc_order_id, $quantity);
            }else{
                $this->calculation_model->calculateLevelCommission($action, $user_id, $data['sponsor_id'], $product_id, $product_pv, $product_amount, $oc_order_id, $quantity);
            }
        }

        $this->calculation_model->updatePersonalPV($user_id, $product_pv,$action);
        $this->calculation_model->updateGroupPV($user_id, $product_pv,$user_id,$action);

        $this->calculation_model->calculatePerformanceBonus($user_id, $product_pv);

        return TRUE;
    }

    public function calculateBinaryCommission($action, $from_id, $father_id, $child_position, $product_id = '', $product_value = '', $product_amount = '', $oc_order_id = 0)
    {

        $this->load->model('product_model');
        $this->load->model('settings_model');

        $binary_config = $this->configuration_model->getBinaryBonusConfig();

        $product_status = $this->MODULE_STATUS['product_status'];

        if ($action == 'repurchase') {
            $amount_type = 'repurchase_leg';
        }
        elseif ($action == 'upgrade') {
            $amount_type = 'upgrade_leg';
        }
        else {
            $amount_type = 'leg';
        }

        if ($product_status == 'no') {
            $product_value = $binary_config['point_value'];
            $pv_value = $binary_config['point_value'];
        } else {
            if ($binary_config['calculation_criteria'] == 'sales_volume') {
                $pv_value = $product_value;
            } else {
                $pv_value = $product_amount;
            }
        }

        // Renshif: No pv is considered for binary. Matching is done using user registration pair
        $pv_value = 1;
        $this->upline_id_arr = [];
        $this->getAllUplineId($father_id, 0, $child_position);

        if ($pv_value > 0) {
            $check=$this->checkAlreadyGotPV($from_id,$father_id);
            if($check){
                return TRUE;
            }
            $this->updateAllUpline($pv_value, $from_id, $amount_type);
        }

        $binary_users = $this->upline_id_arr;

        if ($binary_config['calculation_period'] == 'instant') {
            $this->setBinaryCommission($binary_users, $binary_config, $action, $amount_type);
        }

        return TRUE;
    }

    public function setBinaryCommission($binary_users, $binary_config, $action, $amount_type)
    {
        $matching_bonus_status = $this->validation_model->getCompensationConfig('matching_bonus');
        if ($matching_bonus_status == 'yes') {
            $matching_bonus_config = $this->settings_model->getMatchingBonusConfig();
        }

        $config_details = $this->validation_model->getConfig(['tds', 'service_charge', 'start_date']);

        $product_status = $this->MODULE_STATUS['product_status'];

        if ($product_status == 'no') {
            $pair_price = $binary_config['pair_commission'];
        }

        $first_pair_plan = $binary_config['pair_type'];

        $ceiling_user = $binary_config['flush_out_limit'];

        $pair_value = $binary_config['pair_value'];

        $pair_commission_type = $binary_config['commission_type'];

        $tds_db = $config_details["tds"];

        $service_charge_db = $config_details["service_charge"];

        $week_start = $config_details['start_date'];

        $pair_ceiling_type = 'none';

        if ($binary_config['flush_out'] == 'yes') {
            if ($binary_config['calculation_period'] == 'instant') {
                $pair_ceiling_type = $binary_config['flush_out_period'];
            } else {
                $pair_ceiling_type = $binary_config['calculation_period'];
            }
        } elseif ($binary_config['calculation_period'] != 'instant') {
            $pair_ceiling_type = $binary_config['calculation_period'];
        }

        if ($pair_ceiling_type == "monthly") {
            if ($binary_config['calculation_period'] == 'instant') {
                $from_date = date('Y-m-1');
                $to_date = date('Y-m-t');
            } else {
                $from_date = date('Y-m-d', strtotime('first day of last month'));
                $to_date = date('Y-m-d', strtotime('last day of last month'));
            }
        } elseif ($pair_ceiling_type == "weekly") {
            $week_arr = $this->getWeekDateRange($week_start);
            $from_date = $week_arr["start"];
            $to_date = $week_arr["end"];
            if ($binary_config['calculation_period'] != 'instant') {
                $from_date = date("Y-m-d", strtotime("$from_date - 7 days"));
                $to_date = date("Y-m-d", strtotime("$to_date - 7 days"));
            }
        } elseif ($pair_ceiling_type == "daily") {
            $from_date = date("Y-m-d");
            $to_date = date("Y-m-d");
            if ($binary_config['calculation_period'] != 'instant') {
                $from_date = date('Y-m-d', strtotime("last day"));
                $to_date = date('Y-m-d', strtotime("last day"));
            }
        } else {
            $from_date = "";
            $to_date = "";
        }
        if ($from_date) {
            $from_date = date('Y-m-d 00:00:00', strtotime("$from_date"));
        }
        if ($to_date) {
            $to_date = date('Y-m-d 23:59:59', strtotime("$to_date"));
        }

        $binary_commission_status = $this->validation_model->getCompensationConfig('plan_commission_status');
        if ($binary_commission_status == 'yes') {

            foreach ($binary_users as $user) {
                $user_id = $user["id"];

                $user_left_leg = $user["left_carry"];

                $user_right_leg = $user["right_carry"];

                $product_id = $user["product_id"];

                $status = $user["active"];

                if ($product_status == 'yes') {
                    $membership_package = $user["product_id"];
                    $pair_price = $this->product_model->getPackagePairPrice($membership_package, $this->MODULE_STATUS, 'registration');
                }

                if ($first_pair_plan == "21") {
                    $is_first_pair = $this->isFirstPair($user_id);
                    $user_pair_arr = $this->getUserPair21($user_id, $user_left_leg, $user_right_leg, $ceiling_user, $pair_value, $from_date, $to_date, $pair_ceiling_type, $pair_commission_type, $is_first_pair, $binary_config['calculation_period'], $binary_config['flush_out']);
                } else {
                    $is_first_pair = 0;
                    $user_pair_arr = $this->getUserPair11($user_id, $user_left_leg, $user_right_leg, $ceiling_user, $pair_value, $from_date, $to_date, $pair_ceiling_type, $pair_commission_type, $binary_config['calculation_period'], $binary_config['flush_out']);
                }

                $user_pair = $user_pair_arr['pair'];
                $left_leg = $user_pair_arr['left_leg'];
                $right_leg = $user_pair_arr['right_leg'];
                $left_updated = $user_pair_arr['left_updated'];
                $right_updated = $user_pair_arr['right_updated'];
                $left_volume_deducted = $user_pair_arr['left_volume_deducted'];
                $right_volume_deducted = $user_pair_arr['right_volume_deducted'];

                if ($user_pair > 0) {

                    if ($pair_commission_type == 'percentage') {
                        $total_amount = $user_pair * ($pair_price / 100);
                    } else {
                        $total_amount = $user_pair * $pair_price;
                    }

                    $tds_amount = ($total_amount * $tds_db) / 100;

                    $service_charge = ($total_amount * $service_charge_db) / 100;

                    $amount_payable = $total_amount - ($tds_amount + $service_charge);

                    $date_of_sub = date("Y-m-d H:i:s");

                    if ($product_status == 'yes') {
                        $res = $this->calculation_model->insertInToLegAmount($user_id, $total_amount, $amount_payable, $tds_amount, $service_charge, $date_of_sub, 0, $amount_type, 0, $product_id, 0, 0, 0, $left_leg, $right_leg, $user_pair);
                    } else {
                        $res = $this->calculation_model->insertInToLegAmount($user_id, $total_amount, $amount_payable, $tds_amount, $service_charge, $date_of_sub, 0, $amount_type, 0, 0, 0, 0, 0, $left_leg, $right_leg, $user_pair);
                    }
                    $this->insertInToBinaryBonusHistory($user_id, $user_pair, $left_leg, $right_leg, $binary_config['calculation_period'], $from_date, $to_date);

                    if ($res && $matching_bonus_status == 'yes' && $amount_payable > 0) {
                        $this->calculation_model->calculateMatchingBonus($action, $user_id, $amount_payable, $date_of_sub, $tds_db, $service_charge_db, $matching_bonus_config);
                    }

                    if (in_array($amount_type, ['leg', 'repurchase_leg', 'upgrade_leg'])) {
                        $details = array(
                            'id' => $user_id,
                            'left_carry' => $left_volume_deducted,
                            'right_carry' => $right_volume_deducted
                        );
                        $this->insertIntoBusinessVolume($details, $user_id, $amount_type, "deducted");
                    }
                    $this->updateFTIndividualForPair($user_id, $left_updated, $right_updated, $user_pair);
                    if ($binary_config['calculation_period'] != 'instant' && $binary_config['carry_forward'] == 'yes') {
                        $this->updateLeftRightCarryForward($user_id, $left_updated, $right_updated);
                    }
                } else {
                    if($binary_config['calculation_period'] != 'instant' && $binary_config['carry_forward'] == 'yes') {
                        $this->updateLeftRightCarryForward($user_id, $left_leg, $right_leg);
                    }
                }

                if ($binary_config['flush_out'] == 'yes' && $user_pair_arr['pair_flushed'] > 0) {
                    //$left_updated_flushed = $user_pair_arr['left_updated_flushed'];
                    //$right_updated_flushed = $user_pair_arr['right_updated_flushed'];
                    // $left_volume_deducted_flushed = $user_pair_arr['left_volume_deducted_flushed'];
                    // $right_volume_deducted_flushed = $user_pair_arr['right_volume_deducted_flushed'];
                    // if (in_array($amount_type, ['leg', 'repurchase_leg', 'upgrade_leg'])) {
                    //     $details = [
                    //         'id' => $user_id,
                    //         'left_carry' => $left_volume_deducted_flushed,
                    //         'right_carry' => $right_volume_deducted_flushed
                    //     ];
                    //     $this->insertIntoBusinessVolume($details, $user_id, $amount_type, "deducted_without_pair");
                    // }
                    //$this->updateFTIndividualForPair($user_id, $left_updated_flushed, $right_updated_flushed, 0);
                    $this->updateUserPairFlushed($user_id, $user_pair_arr['pair_flushed']);
                    // if ($binary_config['calculation_period'] != 'instant' && $binary_config['carry_forward'] == 'yes') {
                    //     $this->updateLeftRightCarryForward($user_id, $left_updated_flushed, $right_updated_flushed);
                    // }
                }
            }
        }
    }

    public function getLeftRightPointsBetween($user_id, $from_date, $to_date)
    {
        $this->db->select_sum('left_carry');
        $this->db->select_sum('right_carry');
        $this->db->where('user_id', $user_id);
        $this->db->where('action', 'added');
        $this->db->where_in('amount_type', ['user_join', 'user_repurchase', 'user_upgrade']);
        $this->db->where('date_of_submission >=', $from_date);
        $this->db->where('date_of_submission <=', $to_date);
        $query = $this->db->get('business_volume');
        return $query->row_array();
    }

    public function getLeftRightCarryForward($user_id)
    {
        $this->db->select('left_carry_forward,right_carry_forward');
        $this->db->where('id', $user_id);
        $query = $this->db->get('leg_details');
        return $query->row_array();
    }

    public function updateLeftRightCarryForward($user_id, $left_leg, $right_leg)
    {
        $this->db->set('left_carry_forward', $left_leg);
        $this->db->set('right_carry_forward', $right_leg);
        $this->db->where('id', $user_id);
        $result = $this->db->update('leg_details');
    }

    public function getUserPair11($user_id, $left_leg, $right_leg, $ceiling_user, $pair_value, $from_date, $to_date, $pair_ceiling_type, $pair_commission_type, $calculation_period, $flush_out_status)
    {
        if ($calculation_period != 'instant') {
            $left_right_points = $this->getLeftRightPointsBetween($user_id, $from_date, $to_date);
            $left_right_carry_left = $this->getLeftRightCarryForward($user_id);
            $left_leg = $left_right_points['left_carry'] + $left_right_carry_left['left_carry_forward'];
            $right_leg = $left_right_points['right_carry'] + $left_right_carry_left['right_carry_forward'];
        }

        $left_leg_initial = $left_leg;
        $right_leg_initial = $right_leg;

        $pair = 0;
        $pair_flushed = 0;
        $first_pair = FALSE;

        if ($pair_commission_type == 'flat') {
            if ($pair_value > 0) {
                $min_leg = min($left_leg, $right_leg);
                if ($min_leg >= $pair_value) {
                    $pair = intval($min_leg / $pair_value);
                }
            }
        }
        else {
            if ($left_leg > 0 && $right_leg > 0) {
                $pair = min($left_leg, $right_leg);
            }
        }

        if ($pair_ceiling_type != 'none' && $flush_out_status == 'yes') {
            /* -------- Check here for pair ceiling --------- */
            $week_total = $this->getWeekTotal($user_id, $from_date, $to_date);
            $week_added_total = $week_total + $pair;
            if ($week_added_total > $ceiling_user) {
                if ($week_total >= $ceiling_user) {
                    $pair_flushed = $pair;
                    $pair = 0;
                } else {
                    $pair_flushed = $pair;
                    $pair = $ceiling_user - $week_total;
                    $pair_flushed -= $pair;
                }
            }

            if ($week_added_total == $ceiling_user) {
                $this->addToSummary($user_id,$from_date,$to_date);
            }
        }

        if ($pair_commission_type == 'percentage') {
            $total_user_pair = $pair;
            $total_user_pair_flushed = $pair_flushed;
        }
        else {
            $total_user_pair = $pair * $pair_value;
            $total_user_pair_flushed = $pair_flushed * $pair_value;
        }

        $left_updated = $left_leg_initial - $total_user_pair;
        $left_updated_flushed = $left_updated - $total_user_pair_flushed;
        $right_updated = $right_leg_initial - $total_user_pair;
        $right_updated_flushed = $right_updated - $total_user_pair_flushed;

        $left_volume_deducted = $total_user_pair;
        $left_volume_deducted_flushed = $total_user_pair_flushed;
        $right_volume_deducted = $total_user_pair;
        $right_volume_deducted_flushed = $total_user_pair_flushed;

        return [
            'pair' => $pair,
            'pair_flushed' => $pair_flushed,
            'first_pair' => $first_pair,
            'left_leg' => $left_leg,
            'right_leg' => $right_leg,
            'left_updated' => $left_updated,
            'left_updated_flushed' => $left_updated_flushed,
            'right_updated' => $right_updated,
            'right_updated_flushed' => $right_updated_flushed,
            'left_volume_deducted' => $left_volume_deducted,
            'left_volume_deducted_flushed' => $left_volume_deducted_flushed,
            'right_volume_deducted' => $right_volume_deducted,
            'right_volume_deducted_flushed' => $right_volume_deducted_flushed
        ];
    }

    public function getUserPair21($user_id, $left_leg, $right_leg, $ceiling_user, $pair_value, $from_date, $to_date, $pair_ceiling_type, $pair_commission_type, $is_first_pair, $calculation_period, $flush_out_status)
    {

        if ($calculation_period != 'instant') {
            $left_right_points = $this->getLeftRightPointsBetween($user_id, $from_date, $to_date);
            $left_right_carry_left = $this->getLeftRightCarryForward($user_id);
            $left_leg = $left_right_points['left_carry'] + $left_right_carry_left['left_carry_forward'];
            $right_leg = $left_right_points['right_carry'] + $left_right_carry_left['right_carry_forward'];
        }

        $left_leg_initial = $left_leg;
        $right_leg_initial = $right_leg;

        $pair = 0;
        $pair_flushed = 0;
        $first_pair = FALSE;
        if ($pair_commission_type == 'flat') {
            if ($pair_value > 0 && $left_leg >= $pair_value && $right_leg >= $pair_value) {
                if ($is_first_pair) {
                    if (($left_leg >= (2 * $pair_value)) or ($right_leg >= (2 * $pair_value))) {
                        if ($left_leg < $right_leg) {
                            $right_leg = $right_leg - $pair_value;
                            $first_pair = "right";
                        }
                        else {
                            $left_leg = $left_leg - $pair_value;
                            $first_pair = "left";
                        }
                        $data = array ('first_pair' => $first_pair);
                        $this->db->where('id', $user_id);
                        $result = $this->db->update('ft_individual', $data);
                    }
                    else {
                        $left_leg = 0;
                        $right_leg = 0;
                        $pair = 0;
                    }
                }

                $pair = min(intval($left_leg / $pair_value), intval($right_leg / $pair_value));

            }
        }
        elseif ($pair_commission_type == 'percentage') {
            $min_leg = min($left_leg, $right_leg);
            $max_leg = max($left_leg, $right_leg);
            if ($min_leg > 0) {
                if ($is_first_pair) {
                    if ($max_leg >= (2 * $min_leg)) {
                        if ($left_leg < $right_leg) {
                            $right_leg = $right_leg - $min_leg;
                            $first_pair = 'right';
                        }
                        else {
                            $left_leg = $left_leg - $min_leg;
                            $first_pair = 'left';
                        }
                        $data = array ('first_pair' => $first_pair);
                        $this->db->where('id', $user_id);
                        $result = $this->db->update('ft_individual', $data);
                    }
                    else {
                        $left_leg = 0;
                        $right_leg = 0;
                        $pair = 0;
                    }
                }
                $pair = min($left_leg, $right_leg);
            }
        }

        if ($pair_ceiling_type != 'none' && $flush_out_status == 'yes') {
            $adjust_on_first_pair = FALSE;
            $week_total = $this->getWeekTotal($user_id, $from_date, $to_date);
            $week_added_total = $week_total + $pair;

            if ($week_added_total > $ceiling_user) {
                if ($week_total >= $ceiling_user) {
                    $pair_flushed = $pair;
                    $pair = 0;
                } else {
                    $pair_flushed = $pair;
                    $pair = $ceiling_user - $week_total;
                    $pair_flushed -= $pair;
                    $adjust_on_first_pair = TRUE;
                }
            }

            if ($adjust_on_first_pair) {
                if ($first_pair && $pair_commission_type == 'percentage') {
                    if ($first_pair == 'left') {
                        $left_leg = $left_leg + $right_leg - $pair;
                    }
                    if ($first_pair == 'right') {
                        $right_leg = $right_leg + $left_leg - $pair;
                    }
                }
            }
        }

        if ($pair_commission_type == 'percentage') {
            $total_user_pair = $pair;
            $total_user_pair_flushed = $pair_flushed;
        } else {
            $total_user_pair = $pair * $pair_value;
            $total_user_pair_flushed = $pair_flushed * $pair_value;
        }

        $left = $left_leg - $total_user_pair;
        $left_flushed = $left - $total_user_pair_flushed;
        $right = $right_leg - $total_user_pair;
        $right_flushed = $right - $total_user_pair_flushed;
        $left_deducted = $total_user_pair;
        $left_deducted_flushed = $total_user_pair_flushed;
        $right_deducted = $total_user_pair;
        $right_deducted_flushed = $total_user_pair_flushed;

        if ($first_pair == 'left') {
            if ($pair_commission_type == 'flat')
                $left_deducted = $left_deducted + $pair_value;
            else
                $left_deducted = $left_deducted + $total_user_pair;
        }
        elseif ($first_pair == 'right') {
            if ($pair_commission_type == 'flat')
                $right_deducted = $right_deducted + $pair_value;
            else
                $right_deducted = $right_deducted + $total_user_pair;
        }

        return [
            'pair' => $pair,
            'pair_flushed' => $pair_flushed,
            'left_leg' => $left_leg_initial,
            'right_leg' => $right_leg_initial,
            'left_updated' => $left,
            'left_updated_flushed' => $left_flushed,
            'right_updated' => $right,
            'right_updated_flushed' => $right_flushed,
            'left_volume_deducted' => $left_deducted,
            'left_volume_deducted_flushed' => $left_deducted_flushed,
            'right_volume_deducted' => $right_deducted,
            'right_volume_deducted_flushed' => $right_deducted_flushed
        ];
    }

    public function isFirstPair($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('amount_type', 'leg');
        $count = $this->db->count_all_results('leg_amount');

        return ($count <= 0);
    }
     public function getBinaryPvBlockStatus(){
      
        $this->db->select('block_binary_pv');
        $res = $this->db->get('binary_bonus_config');

        return $res->result_array()[0]['block_binary_pv'];
 
    }

    public function updateAllUpline($product_amount, $from_id, $amount_type = 'leg')
    {

        if ($amount_type == 'leg') {
            $type = 'user_join';
        }
        elseif ($amount_type == 'repurchase_leg') {
            $type = 'user_repurchase';
        }
        elseif ($amount_type == 'upgrade_leg') {
            $type = 'user_upgrade';
        }

        $user_left_id = array ();
        $user_right_id = array ();

        $total_len = count($this->upline_id_arr);

        
        $binary_pv_block = $this->getBinaryPvBlockStatus();        

        for ($i = 0; $i < $total_len; $i++) {

            $user_id = $this->upline_id_arr[$i]["id"];

            $user_active = $this->validation_model->isUserActive($user_id); 

            if($binary_pv_block == 'yes'){
                
               if(!$user_active){
                 
                 continue;
               }
               
            }

            $position = $this->upline_id_arr[$i]["child_position"];

            if ($position == "L") {

                $user_left_id[] = $user_id;

                $this->upline_id_arr[$i]["left_carry"] += $product_amount;
            }
            else
                if ($position == "R") {

                    $user_right_id[] = $user_id;

                    $this->upline_id_arr[$i]["right_carry"] += $product_amount;
                }
        }

        $letf_id_count = count($user_left_id);

        if ($letf_id_count > 0) {

            if ($letf_id_count >= 5000) {

                $input_array = $user_left_id;

                $split_arr_left = array_chunk($input_array, intval($letf_id_count / 4));

                for ($i = 0; $i < count($split_arr_left); $i++) {

                    $left_id_qry = $this->createQuery($split_arr_left[$i]);

                    $this->db->set('total_left_carry', 'ROUND(total_left_carry +' . $product_amount . ',8)', FALSE);

                    $this->db->where($left_id_qry);

                    $result = $this->db->update('ft_individual');

                    $active = $this->session->userdata('inf_active');

                    if ($active == 'yes') {

                        $this->db->set('total_left_carry', 'ROUND(total_left_carry +' . $product_amount . ',8)', FALSE);
                        $this->db->set('total_left_count', 'ROUND(total_left_count +' . $product_amount . ',8)', FALSE);
                        $this->db->set('total_active', 'ROUND(total_active +' . $product_amount . ',8)', FALSE);
                        $this->db->where($left_id_qry);
                    }
                    else {

                        $this->db->set('total_left_carry', 'ROUND(total_left_carry +' . $product_amount . ',8)', FALSE);
                        $this->db->set('total_left_count', 'ROUND(total_left_count +' . $product_amount . ',8)', FALSE);
                        $this->db->set('total_inactive', 'ROUND(total_inactive +' . $product_amount . ',8)', FALSE);
                        $this->db->where($left_id_qry);
                    }
                    $result = $this->db->update('leg_details');
                    foreach ($user_left_id as $left_id) {
                        $details['id'] = $left_id;
                        $details['left_carry'] = $product_amount;
                        $details['right_carry'] = 0;
                        $this->insertIntoBusinessVolume($details, $from_id, $type);
                    }
                }
            }
            else {

                $left_id_qry = $this->createQuery($user_left_id);

                $this->db->set('total_left_carry', 'ROUND(total_left_carry +' . $product_amount . ',8)', FALSE);

                $this->db->where($left_id_qry);

                $result = $this->db->update('ft_individual');

                $active = $this->session->userdata('inf_active');

                if ($active == 'yes') {


                    $this->db->set('total_left_carry', 'ROUND(total_left_carry +' . $product_amount . ',8)', FALSE);
                    $this->db->set('total_left_count', 'ROUND(total_left_count +' . $product_amount . ',8)', FALSE);
                    $this->db->set('total_active', 'ROUND(total_active +' . $product_amount . ',8)', FALSE);

                    $this->db->where($left_id_qry);
                }
                else {


                    $this->db->set('total_left_carry', 'ROUND(total_left_carry +' . $product_amount . ',8)', FALSE);
                    $this->db->set('total_left_count', 'ROUND(total_left_count +' . $product_amount . ',8)', FALSE);
                    $this->db->set('total_inactive', 'ROUND(total_inactive +' . $product_amount . ',8)', FALSE);

                    $this->db->where($left_id_qry);
                }
                $result = $this->db->update('leg_details');
                foreach ($user_left_id as $left_id) {
                    $details['id'] = $left_id;
                    $details['left_carry'] = $product_amount;
                    $details['right_carry'] = 0;
                    $this->insertIntoBusinessVolume($details, $from_id, $type);
                }
            }
        }



        $right_id_count = count($user_right_id);

        if ($right_id_count > 0) {

            if ($right_id_count >= 5000) {

                $input_array = $user_right_id;

                $split_arr_right = array_chunk($input_array, intval($right_id_count / 4));

                for ($i = 0; $i < count($split_arr_right); $i++) {

                    $right_id_qry = $this->createQuery($split_arr_right[$i]);

                    $this->db->set('total_right_carry', 'ROUND(total_right_carry +' . $product_amount . ',8)', FALSE);

                    $this->db->where($right_id_qry);

                    $result = $this->db->update('ft_individual');

                    $active = $this->session->userdata('inf_active');

                    if ($active == 'yes') {

                        $this->db->set('total_right_carry', 'ROUND(total_right_carry +' . $product_amount . ',8)', FALSE);
                        $this->db->set('total_right_count', 'ROUND(total_right_count +' . $product_amount . ',8)', FALSE);
                        $this->db->set('total_active', 'ROUND(total_active +' . $product_amount . ',8)', FALSE);
                        $this->db->where($right_id_qry);
                    }
                    else {


                        $this->db->set('total_right_carry', 'ROUND(total_right_carry +' . $product_amount . ',8)', FALSE);
                        $this->db->set('total_right_count', 'ROUND(total_right_count +' . $product_amount . ',8)', FALSE);
                        $this->db->set('total_inactive', 'ROUND(total_inactive +' . $product_amount . ',8)', FALSE);
                        $this->db->where($right_id_qry);
                    }

                    $result = $this->db->update('leg_details');
                    foreach ($user_right_id as $right_id) {
                        $details['id'] = $right_id;
                        $details['right_carry'] = $product_amount;
                        $details['left_carry'] = 0;
                        $this->insertIntoBusinessVolume($details, $from_id, $type);
                    }

                }
            }
            else {


                $right_id_qry = $this->createQuery($user_right_id);


                $this->db->set('total_right_carry', 'ROUND(total_right_carry +' . $product_amount . ',8)', FALSE);


                $this->db->where($right_id_qry);

                $result = $this->db->update('ft_individual');

                $active = $this->session->userdata('inf_active');

                if ($active == 'yes') {

                    $this->db->set('total_right_carry', 'ROUND(total_right_carry +' . $product_amount . ',8)', FALSE);
                    $this->db->set('total_right_count', 'ROUND(total_right_count +' . $product_amount . ',8)', FALSE);
                    $this->db->set('total_active', 'ROUND(total_active +' . $product_amount . ',8)', FALSE);
                    $this->db->where($right_id_qry);
                }
                else {

                    $this->db->set('total_right_carry', 'ROUND(total_right_carry +' . $product_amount . ',8)', FALSE);
                    $this->db->set('total_right_count', 'ROUND(total_right_count +' . $product_amount . ',8)', FALSE);
                    $this->db->set('total_inactive', 'ROUND(total_inactive +' . $product_amount . ',8)', FALSE);
                    $this->db->where($right_id_qry);
                }
                $result = $this->db->update('leg_details');
                foreach ($user_right_id as $right_id) {
                    $details['id'] = $right_id;
                    $details['right_carry'] = $product_amount;
                    $details['left_carry'] = 0;
                    $this->insertIntoBusinessVolume($details, $from_id, $type);
                }
            }
        }
    }

    public function createQuery($all_id)
    {


        $len = count($all_id);
        $qry = '';
        for ($i = 0; $i < $len; $i++) {

            if ($i == 0)
                $qry = "id = $all_id[$i]";
            else
                $qry .= " OR id = $all_id[$i]";
        }

        return $qry;
    }

    public function getAllUplineIdOld($id, $i, $child_position = '')
    {
        $this->db->select('father_id,total_leg,total_left_carry,total_right_carry,product_id,position,active');
        $this->db->from('ft_individual');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $cnt = $query->num_rows();

        if ($cnt > 0) {
            foreach ($query->result() as $row) {

                $father_id = $row->father_id;
                $this->upline_id_arr[$i]["id"] = $id;
                $this->upline_id_arr[$i]["position"] = $row->position;
                $this->upline_id_arr[$i]["active"] = $row->active;

                if ($i == 0) {
                    $this->upline_id_arr[$i]["child_position"] = $child_position;
                }
                else {
                    $k = $i - 1;
                    $this->upline_id_arr[$i]["child_position"] = $this->upline_id_arr[$k]["position"];
                }

                $this->upline_id_arr[$i]["left_carry"] = $row->total_left_carry;
                $this->upline_id_arr[$i]["right_carry"] = $row->total_right_carry;
                $this->upline_id_arr[$i]["product_id"] = $row->product_id;
                $this->upline_id_arr[$i]["user_level"] = $i + 1;
                $i = $i + 1;
            }
            $this->getAllUplineIdOld($father_id, $i);
        }
        return TRUE;
    }

    public function getAllUplineId($id, $i, $child_position = '')
    {
        $array = $this->db->select('ft.id, ft.father_id, ft.total_leg, ft.total_left_carry, ft.total_right_carry, ft.product_id, ft.position, ft.active')
                    ->from('treepath as tp')
                    ->join('ft_individual as ft', 'tp.ancestor = ft.id')
                    ->where('tp.descendant', $id)
                    ->order_by('user_level', 'DESC')
                    ->get()->result_array();
                    
        for ($i=0; $i < count($array); $i++) {
            $this->upline_id_arr[$i]["id"] = $array[$i]['id'];
            $this->upline_id_arr[$i]["position"] = ($array[$i]['position'])?$array[$i]['position']:'L';
            $this->upline_id_arr[$i]["active"] = $array[$i]['active'];

            if ($i == 0) {
                $this->upline_id_arr[$i]["child_position"] = $child_position;
            } else {
                $this->upline_id_arr[$i]["child_position"] = $array[$i-1]['position'];
            }

            $this->upline_id_arr[$i]["left_carry"] = $array[$i]['total_left_carry'];
            $this->upline_id_arr[$i]["right_carry"] = $array[$i]['total_right_carry'];
            $this->upline_id_arr[$i]["product_id"] = $array[$i]['product_id'];
            $this->upline_id_arr[$i]["user_level"] = $i + 1;
            $this->upline_id_arr[$i]['left_count'] = $this->validation_model->getUserCountByPosition($array[$i]['id'], 'L');
            $this->upline_id_arr[$i]['right_count'] = $this->validation_model->getUserCountByPosition($array[$i]['id'], 'R');
        }
        return TRUE;
    }

    public function getWeekDateRange($start_day)
    {
        $start = strtotime("last $start_day");
        $start = date('w', $start) == date('w') ? $start + 7 * 86400 : $start;

        $end = strtotime(date("Y-m-d", $start) . " +6 days");

        $this_week_sd = date("Y-m-d", $start);
        $this_week_ed = date("Y-m-d", $end);

        return [
            'start' => $this_week_sd,
            'end' => $this_week_ed
        ];
    }

    public function getWeekStartEndDates($startDate, $endDate)
    {

        if (date("l") == $startDate)
            $this_sat = date("Y-m-d 23:59:59");
        else {

            $a = strtotime("next $startDate");


            $this_sat = date("Y-m-d 23:59:59", $a);
        }

        if (date("l") == $endDate)
            $last_sat = date("Y-m-d 00:00:00");
        else {

            $a = strtotime("last $endDate");

            $last_sat = date(
                    "Y-m-d 00:00:00", $a);
        }

        $arr['startDate'] = $last_sat;

        $arr['endDate'] = $this_sat;

        return $arr;
    }

    function getWeekTotal($user_id, $from_date, $to_date)
    {
        $this->db->select_sum('total_leg', 'tot_amt');
        $this->db->where('user_id', $user_id);
        $this->db->where('from_date >=', $from_date);
        $this->db->where('to_date <=', $to_date);
        $this->db->from('binary_bonus_history');
        $query = $this->db->get();

        return $query->row_array()['tot_amt'];
    }

    public function updateFTIndividualForPair($user_id, $left_leg, $right_leg, $user_pair)
    {

        $this->db->set('total_left_carry', $left_leg);
        $this->db->set('total_right_carry', $right_leg);

        $this->db->set('total_leg', 'ROUND(total_leg +' . $user_pair . ',8)', FALSE);

        $this->db->where('id', $user_id);

        $this->db->limit(1);

        $result = $this->db->update('ft_individual');

        $data = array ('total_left_carry' => $left_leg,
            'total_right_carry' => $right_leg
        );
        $this->db->where('id', $user_id);
        $this->db->limit(1);
        $result1 = $this->db->update('leg_details', $data);

        return $result;
    }

    public function insertIntoBusinessVolume($details, $from_id, $amount_type, $action = 'added')
    {
        $date_of_submission = date("Y-m-d H:i:s");
        $user_id = $details['id'];
        $leg_details = $this->getUserLegDetails($user_id);
        if (in_array($amount_type, ['leg', 'repurchase_leg', 'upgrade_leg'])) {
            $left_leg = $leg_details['total_left_carry'] - $details['left_carry'];
            $right_leg = $leg_details['total_right_carry'] - $details['right_carry'];
        }
        else {
            $left_leg = $leg_details['total_left_carry'];
            $right_leg = $leg_details['total_right_carry'];
        }

        $this->db->set('user_id', $user_id);
        $this->db->set('from_id', $from_id);
        $this->db->set('amount_type', $amount_type);
        $this->db->set('left_leg', $left_leg);
        $this->db->set('right_leg', $right_leg);
        $this->db->set('action', $action);
        $this->db->set('left_carry', $details['left_carry']);
        $this->db->set('right_carry', $details['right_carry']);
        $this->db->set('date_of_submission', $date_of_submission);
        $res = $this->db->insert('business_volume');
        return $res;
    }

    public function getUserLegDetails($user_id)
    {
        $total_left_count = $total_right_count = $total_left_carry = $total_right_carry = 0;
        $this->db->select('total_left_count,total_right_count');
        $this->db->select('total_left_carry,total_right_carry');
        $this->db->from('leg_details');
        $this->db->where('id', $user_id);
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $total_left_count = $row->total_left_count;
            $total_right_count = $row->total_right_count;
            $total_left_carry = $row->total_left_carry;
            $total_right_carry = $row->total_right_carry;
        }
        $arr = array ();
        $arr['total_left_count'] = $total_left_count;
        $arr['total_left_carry'] = $total_left_carry;
        $arr['total_right_count'] = $total_right_count;
        $arr['total_right_carry'] = $total_right_carry;
        return $arr;
    }

    // public function getAllUplineSponserId($id, $i)
    // {

    //     $this->db->select('sponsor_id, active, personal_pv, gpv');
    //     $this->db->from('ft_individual');
    //     $this->db->where('id', $id);
    //     $query = $this->db->get();
    //     $cnt = $query->num_rows();
    //     if ($cnt > 0) {
    //         foreach ($query->result() as $row) {
    //             $sponsor_id = $row->sponsor_id;
    //             $this->upline_sponsor_arr["detail$i"]["id"] = $id;
    //             $this->upline_sponsor_arr["detail$i"]["active"] = $row->active;
    //             ;
    //             $this->upline_sponsor_arr["detail$i"]["gpv"] = $row->gpv;
    //             $this->upline_sponsor_arr["detail$i"]["personal_pv"] = $row->personal_pv;
    //             $i = $i + 1;
    //         }
    //         $this->getAllUplineSponserId($sponsor_id, $i);
    //     }
    //     return TRUE;
    // }

    public function insertInToBinaryBonusHistory($user_id, $total_leg, $left_leg, $right_leg, $cal_type, $from_date, $to_date) {

        $skip_blocked_users_commission = $this->configuration_model->getConfiguration('skip_blocked_users_commission');
        $is_user_active = $this->validation_model->isUserActive($user_id);

        if(!$is_user_active && $skip_blocked_users_commission == 'yes') {
            return true;
        }

        $result = false;
        $date_of_sub = date("Y-m-d H:i:s");

        $this->db->set('user_id', $user_id);
        $this->db->set('total_leg', $total_leg);
        $this->db->set('left_leg', $left_leg);
        $this->db->set('right_leg', $right_leg);
        $this->db->set('date_added', $date_of_sub);
        $this->db->set('calculation_type', $cal_type);
        $this->db->set('from_date', $from_date);
        $this->db->set('to_date', $to_date);
        $result = $this->db->insert('binary_bonus_history');
   
        return $result;
    }

    public function updateUserPairFlushed($user_id, $pair_flushed) {
        //$this->db->set('pair_flushed_carry_forward', "pair_flushed_carry_forward + $pair_flushed", FALSE);
        $this->db->set('pair_flushed_carry_forward', $pair_flushed);
        $this->db->where('id', $user_id);
        $result = $this->db->update('leg_details');
    }

    public function getFlushedPairCarryForward($user_id) {
        $this->db->select('pair_flushed_carry_forward');
        $this->db->where('id', $user_id);
        $query = $this->db->get('leg_details');
        return $query->row_array();
    }

    public function addToSummary($user_id,$first_date,$last_date) {
        $this->db->where('user_id',$user_id);
        $this->db->where('date >=',$first_date);
        $this->db->where('date <=',$last_date);
        $count = $this->db->count_all_results('binary_max_history');
        if(!$count) {
            $this->db->where('user_id', $user_id);
            $this->db->set('binary_max', 'binary_max + 1', FALSE);
            $this->db->update('summary_info');
            
            $this->db->set('user_id',$user_id);
            $this->db->set('date',$first_date);
            $this->db->insert('binary_max_history');

        }
        $this->load->model('rank_model');
        $this->rank_model->updateUplineRank($user_id);
        return;
    }
    public function updateFTIndividualForPairCron($user_id, $left_leg, $right_leg, $user_pair)
    {
        $this->db->set('total_left_carry', "total_left_carry - $left_leg", FALSE);
        $this->db->set('total_right_carry', "total_right_carry - $right_leg", FALSE);

        $this->db->set('total_leg', 'ROUND(total_leg +' . $user_pair . ',8)', FALSE);

        $this->db->where('id', $user_id);

        $this->db->limit(1);

        $result = $this->db->update('ft_individual');

        $data = array ('total_left_carry' => $left_leg,
            'total_right_carry' => $right_leg
        );
        $this->db->set('total_left_carry', "total_left_carry - $left_leg", FALSE);
        $this->db->set('total_right_carry', "total_right_carry - $right_leg", FALSE);
        $this->db->where('id', $user_id);
        $this->db->limit(1);
        $result1 = $this->db->update('leg_details');

        return $result;
    }
    public function checkAlreadyGotPV($from_id,$father_id){
        $this->db->where('user_id',$father_id);
        $this->db->where('from_id',$from_id);
        $this->db->where('amount_type','user_join');
        return $this->db->count_all_results('business_volume');
    }

}
