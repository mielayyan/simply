<?php

require_once "Tree_view_model.php";
require_once "Validation_model.php";

class tree_model extends inf_model
{

    public $OBJ_TREE_VIEW;
    public $OBJ_VAL;
    public $OBJ_AUTH;
    public $board_view;
    public $auto_filling;
    public $board_array = array();
    public $board_tooltip_array = array();
    public $tree_array = array();
    public $tree_tooltip_array = array();
    public $display_tree = "";
    public $step_tooltip_array = array();
    public $step_array = array();

    public function __construct()
    {
        parent::__construct();

        $this->OBJ_TREE_VIEW = new tree_view_model();
        $this->OBJ_VAL = new validation_model();

        //define(TREE_LEVEL, 5);
        //print_r($_POST);die;
        $this->db->query('SET SESSION sql_mode = ""');

        if ($this->MLM_PLAN == "Board") {
            require_once 'Boardview_model.php';
            $this->board_view = new boardview_model();
            $this->load->model("auto_board_filling_model");
            $this->load->model("joining_class_model");
        }
    }

    public function renderTree($user_id, $module_status, $type = 'tree')
    {
        $log_user_id = ($this->LOG_USER_TYPE == 'employee') ? $this->ADMIN_USER_ID : $this->LOG_USER_ID;
        $mlm_plan = $module_status['mlm_plan'];
        $rank_status = $module_status['rank_status'];
        $width_ceiling = $this->validation_model->getColoumnFromTable('configuration', 'width_ceiling');
        // get downline users array
        $tree_user_details = $this->getTreeDownlines($mlm_plan, $rank_status, $user_id, $type);
        $this->tree_tooltip_array = $tree_user_details;

        $assets = [];
        $assets['image_path'] = base_url() . "public_html/images/tree/";
        $assets['profile_image_path'] = SITE_URL . "/uploads/images/profile_picture/";
        $assets['package_image_path'] = SITE_URL . "/uploads/images/tree/";
        $assets['up_icon'] = "{$assets['image_path']}up.png";
        $assets['down_icon'] = "{$assets['image_path']}down.png";
        $assets['user_icon'] = "{$assets['image_path']}active.png";
        $assets['user_icon_inactive'] = "{$assets['image_path']}inactive.png";
        $assets['temp_icon'] = "{$assets['image_path']}add.png";
        $assets['temp_icon_inactive'] = "{$assets['image_path']}add_disabled.png";
        $assets['label_active'] = "demo_name_style";
        $assets['label_inactive'] = "demo_name_style_red";
        $assets['label_disabled'] = "demo_name_style";
        $assets['label_vacant'] = "demo_name_style_blue";

        $this->display_tree = '<ul id="tree_view" style="display:none">';
        // initialize an empty stack for keeping temporary nodes
        $this->node_stack = [];
        // set initial tree level
        $this->current_level = -1;
        // iterate downline user array

        $legLockingAncestor = '';
        $user_locked_binary_leg = 'any';
        if ($log_user_id != $this->ADMIN_USER_ID && $type == 'tree' && $mlm_plan == 'Binary') {
            $user_locked_binary_leg = $this->configuration_model->getUserWiseSignupBinaryLeg($log_user_id);
            if ($user_locked_binary_leg != 'any') {
                $check_pos = ($user_locked_binary_leg == 'R') ? 'L' : 'R';
                $legLockingAncestor = $this->db->select('id')->where('father_id', $log_user_id)->where('position', $check_pos)->limit(1)->get('ft_individual')->result_array()[0]['id'] ?? '';
            }

        }

        // $tree_user_details = array_shift($tree_user_details);
        while (!empty($tree_user_details)) {
            // remove first user from downline array and keep in a variable
            $user_detail = array_shift($tree_user_details);

            // check if downline array empty
            $tree_empty = empty($tree_user_details);
            $user_detail['encrypted_user_id'] = $this->validation_model->IdToUserName($user_detail['user_id']); //$this->OBJ_TREE_VIEW->getEncrypt($user_detail['user_id']);
            $image_path = SITE_URL . "/uploads/images/profile_picture/";

            $user_icon = $user_detail['photo'];

            //$assets['user_icon'] = file_exists(IMG_DIR.'profile_picture/'.$user_icon) ? "{$image_path}$user_icon" : "{$image_path}nophoto.jpg";

            // render removed user in tree view
            $this->renderTreeNode($user_detail, $log_user_id, $type, $assets);
            // set tree level to removed user's level
            $this->current_level = $user_detail['depth'];
            if ($type == 'tree') {
                // temporary node insertion (only for genealogy tree)

                // leg_lock_user
                $binaryDisabledLeg = '';
                if ($user_locked_binary_leg != 'any') {
                    if ($log_user_id == $user_detail['user_id']) {
                        $binaryDisabledLeg = ($user_locked_binary_leg == 'L') ? 'R' : 'L';
                    } else {
                        if ($legLockingAncestor) {
                            if ($this->db->where('ancestor', $legLockingAncestor)->where('descendant', $user_detail['user_id'])->count_all_results('treepath') > 0) {
                                $binaryDisabledLeg = 'both';
                            }
                        }
                    }
                }
                // leg_lock_user - end
                
                //blocking inner leg registration//
                // if($user_id != $user_detail['user_id']){
                //   $binaryDisabledLeg = ($user_detail['position'] == 'R') ? 'L' : 'R';
                // }
                $this->processTempNode($mlm_plan, $width_ceiling, $user_detail, $tree_empty, $assets, $binaryDisabledLeg,$log_user_id);
            }
            // end of downline user array iteration
            if ($tree_empty) {
                $this->display_tree .= str_repeat('</li></ul>', $this->current_level);
                $this->display_tree .= '</li>';
            }
        }
        $this->display_tree .= '</ul>';

    }

    public function renderTreeNode($user_detail, $log_user_id, $tree_type, $assets)
    {

        // remove and insert temporary nodes from stack in reverse order having level below user's level
        foreach (array_reverse($this->node_stack) as $node) {
            if ($node['level'] > $user_detail['depth']) {
                $this->renderTempNode([$node], $assets);
                array_pop($this->node_stack);
            } else {
                break;
            }
        }
        $parent_id = ($tree_type == 'tree') ? $user_detail['father_id'] : $user_detail['sponsor_id'];
        $user_name = $user_detail['user_name'];
        $userName = str_replace(".", "_", $user_name);
        $level = $user_detail['depth'];
        $up_icon_flag = ($level < 1) && ($user_detail['user_id'] != $log_user_id);
        $down_icon_flag = ($level + 1 >= TREE_LEVEL);
        if ($up_icon_flag || $down_icon_flag) {
            $parent_username = $this->validation_model->IdToUserName($parent_id);
        }
        $tree_icon_based = $this->configuration_model->getTreeBasedOnConfig();

        if ($tree_icon_based == 'member_status') {

            $active_icon = $this->validation_model->getMemberStatusIcon('active');
            $inactive_icon = $this->validation_model->getMemberStatusIcon('inactive');

            $assets['user_icon_active'] = "{$assets['package_image_path']}" . $active_icon;

            $assets['user_icon_inactive'] = "{$assets['package_image_path']}" . $inactive_icon;

            $tree_icon = ($user_detail['active'] == 'yes') ? $assets['user_icon_active'] : $assets['user_icon_inactive'];

            $tree_label = ($user_detail['active'] == 'yes') ? "{$assets['label_active']}" : "{$assets['label_inactive']}";

        } else if ($tree_icon_based == 'profile_image') {

            $assets['user_icon_profile'] = "{$assets['profile_image_path']}" . $user_detail['photo'];

            $tree_icon = file_exists(IMG_DIR . "profile_picture/" . $user_detail['photo']) ? "{$assets['user_icon_profile']}" : "{$assets['profile_image_path']}nophoto.jpg";

            $tree_label = ($user_detail['active'] == 'yes') ? "{$assets['label_active']}" : "{$assets['label_inactive']}";

        } else if ($tree_icon_based == "member_pack") {

            $prod_id = $this->validation_model->getProductId($user_detail['user_id']);

            $product_id = $this->validation_model->getProductidFromProdID($prod_id);

            $tree_icon_package = $this->validation_model->getTreeIconFromProductid($product_id);

            if ($tree_icon_package == '') {

                $tree_icon_package = $this->configuration_model->getDefualtIcon('package');

            }

            $assets['user_icon_package'] = "{$assets['package_image_path']}" . $tree_icon_package;

            $tree_icon = "{$assets['user_icon_package']}";

            $tree_label = ($user_detail['active'] == 'yes') ? "{$assets['label_active']}" : "{$assets['label_inactive']}";

        } else if ($tree_icon_based == "rank") {

            $tree_icon_rank = $this->validation_model->getTreeIconFromRankId($user_detail['rank_id']);

            if ($tree_icon_rank == '') {

                $tree_icon_rank = $this->configuration_model->getDefualtIcon('rank');

            }

            $assets['user_icon_rank'] = "{$assets['package_image_path']}" . $tree_icon_rank;

            $tree_icon = "{$assets['user_icon_rank']}";

            $tree_label = ($user_detail['active'] == 'yes') ? "{$assets['label_active']}" : "{$assets['label_inactive']}";

        }

        if ($level == $this->current_level) {
            $this->display_tree .= '</li>';
        }
        if ($level > $this->current_level && $level) {
            $this->display_tree .= '<ul>';
        }
        if ($level < $this->current_level) {
            $this->display_tree .= str_repeat('</li></ul>', $this->current_level - $level);
        }
        $this->display_tree .= '<li>';
        $root_class = $level == 0 ? 'root_node' : '';
        if ($up_icon_flag) {
            $this->display_tree .= "<div class='root_div'><img class='tree_up_icon' src='{$assets['up_icon']}' onclick='getGenologyTree(\"{$parent_username}\",event);'/></div>";
        }
        $this->display_tree .= "<img class='tree_icon with_tooltip {$root_class}' src='{$tree_icon}' ondblclick='getGenologyTree(\"{$user_name}\",event);' data-tooltip-content='#user_{$userName}'/>"; // double click/tap to view that user's tree
        $this->display_tree .= "<p class='{$tree_label}'>{$user_name}</p>";
        if ($down_icon_flag) {
            $this->display_tree .= "<div><img src='{$assets['down_icon']}' class='tree_down_icon' onclick='expandGenologyTree(\"{$user_name}\",event);'/></div>";
        }

    }

    public function processTempNode($mlm_plan, $width_ceiling, $user_detail, $tree_empty, $assets, $binaryDisabledLeg = '',$log_user_id=0)
    {
        $nodes = [];
        $position = $user_detail['position'];
        $user_detail['child_count'] = $this->validation_model->getTotalChildcount($user_detail['user_id']);
        $level = $user_detail['depth'] + 1;
        if ($level < TREE_LEVEL) {
            $child_count = $user_detail['child_count'];
        } else {
            $child_count = 0;
        }

        // temporary node insertion for binary plan (only if child count less than 2)
        if ($mlm_plan == 'Binary' && $child_count < 2) {
            $child_position = $user_detail['child_position'];
            //$child_position = $this->validation_model->getUserPosition($user_detail['user_id']);
            $left_node_disabled = $right_node_disabled = false;
            $left_node_url = base_url() . 'register/user_register/' . $user_detail['encrypted_user_id'] . '/L';
            $right_node_url = base_url() . 'register/user_register/' . $user_detail['encrypted_user_id'] . '/R';

            if($log_user_id){
                $user_position = $this->getUserPositionParentBase($user_detail['user_id'], $log_user_id);
                $check_position=$this->checkUserPositionParentBase($user_detail['user_id'], $log_user_id,$user_detail['position'],$user_position);
            }else{
                $user_position = $this->getUserPositionParentBase($user_detail['user_id'], $this->ADMIN_USER_ID);
            }
            $binary_leg = $this->configuration_model->getSignupBinaryLeg();
            if($check_position && $user_detail['user_id']!=$log_user_id){
                $binaryDisabledLeg='both';
            }elseif(($user_detail['position']=='L') && $user_position='R' && $user_detail['user_id']!=$log_user_id){
                $binaryDisabledLeg='R';
            }elseif(($user_detail['position']=='R') AND $user_position='L' && $user_detail['user_id']!=$log_user_id){
                $binaryDisabledLeg='L';
            }else{
                
            }
            if ($binary_leg != 'any') {

                if ($binary_leg == 'L') {

                    if ($user_position == 'R') {
                        $left_node_disabled = $right_node_disabled = true;
                        $left_node_url = $right_node_url = 'javascript:void(0);';
                    }

                } else {
                    if ($user_position == 'L') {
                        $left_node_disabled = $right_node_disabled = true;
                        $left_node_url = $right_node_url = 'javascript:void(0);';
                    }

                }

            } else {
                if ($binaryDisabledLeg) {
                    if ($binaryDisabledLeg == 'L' || $binaryDisabledLeg == 'both') {
                        $left_node_disabled = true;
                        $left_node_url = 'javascript:void(0);';
                    }
                    if ($binaryDisabledLeg == 'R' || $binaryDisabledLeg == 'both') {
                        $right_node_disabled = true;
                        $right_node_url = 'javascript:void(0);';
                    }
                }
            }
            // insert left child node into tree view if no children or child is on right
            if ($child_count == 0 || ($child_count == 1 && $child_position == 'R')) {
                $nodes[] = [
                    'position' => 'L',
                    'level' => $level,
                    'url' => $left_node_url,
                    'disabled' => $left_node_disabled,
                ];
            }
            // insert right child node into tree view if no children
            if ($child_count == 0) {
                $nodes[] = [
                    'position' => 'R',
                    'level' => $level,
                    'url' => $right_node_url,
                    'disabled' => $right_node_disabled,
                ];
            }
            // add right child node in stack array for later insertion (if child is on left)
            if ($child_count == 1 && $child_position == 'L') {
                $this->node_stack[] = [
                    'position' => 'R',
                    'level' => $level,
                    'url' => $right_node_url,
                    'disabled' => $right_node_disabled,
                ];
            }
        }
        // temporary node insertion for all other plans
        // for matrix plan, child count must be less than width
        elseif (($mlm_plan == 'Matrix' && $child_count < $width_ceiling) || ($mlm_plan != 'Binary' && $mlm_plan != 'Matrix')) {
            $node_disabled = false;
            $node_url = base_url() . 'register/user_register/' . $user_detail['encrypted_user_id'] . '/' . ($child_count + 1);
            if ($mlm_plan == 'Unilevel' && $this->LOG_USER_ID != $user_detail['user_id'] && $this->LOG_USER_ID != $this->ADMIN_USER_ID) {
                $node_disabled = true;
                $node_url = 'javascript:void(0);';
            }
            // insert child node into tree view if no children
            if ($child_count == 0) {
                $nodes[] = [
                    'position' => $child_count + 1,
                    'level' => $level,
                    'url' => $node_url,
                    'disabled' => $node_disabled,
                ];
            }
            // add child node in stack array for later insertion (if child exists)
            if ($child_count > 0) {
                $this->node_stack[] = [
                    'position' => $child_count + 1,
                    'level' => $level,
                    'url' => $node_url,
                    'disabled' => $node_disabled,
                ];
            }
        }

        $this->renderTempNode($nodes, $assets);

        // remove and insert temporary nodes from stack in reverse order (only if last iteration)
        if ($tree_empty) {
            foreach (array_reverse($this->node_stack) as $node) {
                $this->renderTempNode([$node], $assets);
                array_pop($this->node_stack);
            }
        }
    }

    // actual insertion of temporary nodes into tree view
    public function renderTempNode($nodes, $assets)
    {
        $node_text = lang('ADD_HERE');
        foreach ($nodes as $node) {
            // exclude nodes having level greater than maximum tree level
            if ($node['level'] < TREE_LEVEL) {
                $node_icon = $node['disabled'] ? $assets['temp_icon_inactive'] : $assets['temp_icon'];
                if ($node['level'] == $this->current_level) {
                    $this->display_tree .= '</li>';
                }
                if ($node['level'] > $this->current_level && $node['level']) {
                    $this->display_tree .= '<ul>';
                }
                if ($node['level'] < $this->current_level) {
                    $this->display_tree .= str_repeat('</li></ul>', $this->current_level - $node['level']);
                }
                $this->display_tree .= '<li>';
                $this->display_tree .= "<img class='tree_icon add-icon' src='{$node_icon}' onclick='goToLink(\"{$node['url']}\");'/>";
                if (!$node['disabled']) {
                    $tree_label = $assets['label_vacant'];
                    $this->display_tree .= "<br><p class='{$tree_label} add-btn'>{$node_text}</p>";
                }

                $this->current_level = $node['level'];
            }
        }
    }

    public function getTreeDownlines2($mlm_plan, $rank_status, $user_id, $tree_type = 'tree')
    {
        $level = $this->validation_model->getUserTreeLevel($user_id, $tree_type);
        $user_left_right_node = $this->getUserLeftRightNode($user_id, $tree_type);

        if ($user_left_right_node && $level != '') {
            $this->db->select('f.id user_id,f.user_name,f.active,f.position,f.father_id,f.sponsor_id,f.user_level,f.sponsor_level,f.date_of_joining join_date,f.user_rank_id rank_id,u.user_detail_name first_name,u.user_detail_second_name last_name,CONCAT(u.user_detail_name,u.user_detail_second_name) full_name,u.user_photo photo', false);
            $this->db->from('ft_individual f');
            $this->db->join('user_details u', 'f.id = u.user_detail_refid', 'LEFT');

            if ($mlm_plan == 'Binary') {
                $this->db->select('l.total_left_count left,l.total_right_count right,l.total_left_carry left_carry,l.total_right_carry right_carry');
                $this->db->join('leg_details l', 'l.id = f.id', 'LEFT');
            }

            if ($mlm_plan == 'Stair_Step') {
                $this->db->select('p.total_pv personal_pv,p.total_gpv group_pv');
                $this->db->join('user_pv_details p', 'p.user_id = f.id', 'LEFT');
            } else {
                $this->db->select('f.personal_pv,f.gpv group_pv');
            }

            if ($mlm_plan == 'Donation') {
                $this->db->select('d.level_name donation_level');
                $this->db->join('donation_rate d', 'd.id = f.current_level', 'LEFT');
            }

            if ($rank_status == 'yes') {
                $this->db->select('r.rank_name,r.rank_color');
                $this->db->join('rank_details r', 'r.rank_id = f.user_rank_id', 'LEFT');
            }

            if ($tree_type == 'tree') {
                $this->db->select("(f.user_level - {$level}) depth", false);
                $this->db->select("(t.right_father - t.left_father) node_diff", false);
                $this->db->join('tree_parser t', 't.ft_id = f.id', 'LEFT');
                $this->db->select("COUNT(f2.id) child_count");
                if ($mlm_plan == 'Binary') {
                    $this->db->select('f2.position child_position');
                }
                $this->db->join('ft_individual f2', 'f2.father_id = f.id', 'LEFT');
                $this->db->where("f.user_level - {$level} < ", TREE_LEVEL, false);
                $this->db->where("t.left_father >=", $user_left_right_node['left']);
                $this->db->where("t.left_father <=", $user_left_right_node['right']);
                $this->db->group_by('f.id');
                $this->db->order_by('t.left_father');
            } elseif ($tree_type == 'sponsor_tree') {
                $this->db->select("(f.sponsor_level - {$level}) depth,t.left_sponsor,t.right_sponsor", false);
                $this->db->join('tree_parser t', 't.ft_id = f.id', 'LEFT');
                $this->db->where("f.sponsor_level - {$level} < ", TREE_LEVEL, false);
                $this->db->where("t.left_sponsor >=", $user_left_right_node['left']);
                $this->db->where("t.left_sponsor <=", $user_left_right_node['right']);
                $this->db->order_by('t.left_sponsor');
            }

            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function getTreeDownlines($mlm_plan, $rank_status, $user_id, $tree_type = 'tree')
    {
        $level = $this->validation_model->getUserTreeLevel($user_id, $tree_type);
        if ($level != '') {
            $this->db->select('f.id user_id,f.active,f.position,f.user_name,f.father_id,f.sponsor_id,f.user_level,f.sponsor_level,f.date_of_joining join_date,f.user_rank_id rank_id,u.user_detail_name first_name,u.user_detail_second_name last_name,CONCAT(u.user_detail_name,u.user_detail_second_name) full_name,CONCAT(u5.user_detail_name," ",u5.user_detail_second_name) sponsor_full_name,u.user_photo photo,c.country_name', false);
            $this->db->from('ft_individual f');
            $this->db->join('user_details u', 'f.id = u.user_detail_refid', 'LEFT');
            $this->db->join('infinite_countries c', 'c.country_id = u.user_detail_country', 'LEFT');
            $this->db->join('user_details u5', 'u5.user_detail_refid = f.sponsor_id', 'LEFT');

            if ($mlm_plan == 'Binary') {
                $this->db->select('l.total_left_count left,l.total_right_count right,l.total_left_carry left_carry,l.total_right_carry right_carry');
                $this->db->join('leg_details l', 'l.id = f.id', 'LEFT');
            }

            if ($mlm_plan == 'Stair_Step') {
                $this->db->select('p.total_pv personal_pv,p.total_gpv group_pv');
                $this->db->join('user_pv_details p', 'p.user_id = f.id', 'LEFT');
            } else {
                $this->db->select('f.personal_pv,f.gpv group_pv');
            }

            if ($mlm_plan == 'Donation') {
                $this->db->select('d.level_name donation_level');
                $this->db->join('donation_rate d', 'd.id = f.current_level', 'LEFT');
            }

            if ($rank_status == 'yes') {
                $this->db->select('r.rank_name,r.rank_color');
                $this->db->join('rank_details r', 'r.rank_id = f.user_rank_id', 'LEFT');
                $this->db->select('r1.rank_name as  promo_rank');
                $this->db->join('rank_promo r1', 'r1.id = f.promo_rank', 'LEFT');
            }

            if ($tree_type == 'tree') {
                $this->db->select("(f.user_level - {$level}) depth", false);
                //$this->db->select("f.id, f.user_name, GROUP_CONCAT(DISTINCT CONCAT(f2.user_level, LPAD(f2.leg_position, 8, '0'))  ORDER BY f2.user_level SEPARATOR '') as br");
                $this->db->select("f.id, f.user_name, GROUP_CONCAT(DISTINCT CONCAT(f2.user_level - {$level}, LPAD(f2.leg_position, 8, '0'))  ORDER BY f2.user_level SEPARATOR '') as br");
                if ($mlm_plan == 'Binary') {
                    $this->db->select('f3.position child_position');
                }
                $this->db->join('ft_individual f3', 'f3.father_id = f.id', 'LEFT');

                $this->db->join('treepath t', 't.descendant = f.id');
                $this->db->join('treepath crumbs', 'crumbs.descendant = t.descendant');
                $this->db->where("f.user_level - {$level} < ", TREE_LEVEL, false);
                //$this->db->join('ft_individual f2', 'f2.id = crumbs.ancestor');
                $this->db->join('ft_individual f2', 'f2.id = crumbs.ancestor AND f2.user_level >= '.$level);
                $this->db->where('t.ancestor', $user_id);
                $this->db->group_by('f.id');
                $this->db->order_by('br');
            } elseif ($tree_type == 'sponsor_tree') {
                $this->db->select("(f.sponsor_level - {$level}) depth,f.id, f.user_name, GROUP_CONCAT(DISTINCT CONCAT(f2.sponsor_level, LPAD(f2.leg_position, 8, '0'), crumbs.ancestor)  ORDER BY f2.sponsor_level  SEPARATOR '') as br");
                $this->db->join('sponsor_treepath t', 't.descendant = f.id');
                $this->db->join('sponsor_treepath crumbs', 'crumbs.descendant = t.descendant');
                $this->db->join('ft_individual f2', 'f2.id = crumbs.ancestor');
                $this->db->where('t.ancestor', $user_id);
                $this->db->having("depth < ", TREE_LEVEL);
                $this->db->group_by('f.id');
                $this->db->order_by('br');
            }
            $query = $this->db->get();
            
            return $query->result_array();
        }
    }

    /*public function getTreeView($user_id, $type = "tree") {

    $tree_user_details = $this->getUserDownlineTreeDetails($user_id, $type);

    $this->display_tree = '<ul id="tree_view" style="display:none">';

    $mlm_plan = $this->validation_model->getMLMPlan();

    $current_depth = -1;
    while (!empty($tree_user_details)) {
    $u = array_shift($tree_user_details);
    if ($type == 'tree') {
    $father_id_encrypt = $this->OBJ_TREE_VIEW->getEncrypt($u['father_id']);
    $user_icon_link = $this->getUserIconAndLink($u['user_id'], $u['user_name'], $u['active'], $u['father_id'], $father_id_encrypt, $u['position'], $u['depth']);
    } elseif ($type == 'sponsor_tree') {
    $sponsor_id_encrypt = $this->OBJ_TREE_VIEW->getEncrypt($u['sponsor_id']);
    $user_icon_link = $this->getUserIconAndLink($u['user_id'], $u['user_name'], $u['active'], $u['sponsor_id'], $sponsor_id_encrypt, $u['position'], $u['depth']);
    }
    $user_icon = $user_icon_link["icon"];
    $user_link = $user_icon_link["link"];
    $user_up_link = $user_icon_link["up_link"];
    $user_up_link_image = $user_icon_link["up_link_image"];
    $user_text = $user_icon_link["text"];
    $tree_color = $user_icon_link["tree_color"];
    $tree_border = "border: 2px solid $tree_color !important;";
    $tree_background = "background: $tree_color !important;";
    $user_onclick_link = ($u['active'] != "server") ? 'onclick=\'getGenologyTree("' . $u['user_name'] . '",event);\'' : '';

    $this->setTreeTooltipDetails($u);

    $check_root_user = false;
    if ($u['depth'] < 1) {
    if ($this->LOG_USER_TYPE != 'employee') {
    if ($u['user_id'] != $this->LOG_USER_ID) {
    $check_root_user = true;
    }
    } else {
    if ($u['user_id'] != $this->ADMIN_USER_ID) {
    $check_root_user = true;
    }
    }
    }

    if ($u['depth'] == $current_depth) {
    $this->display_tree .= '</li>';
    }
    if ($u['depth'] > $current_depth && $u['depth']) {
    $this->display_tree .= '<ul>';
    }
    if ($u['depth'] < $current_depth) {
    $this->display_tree .= str_repeat('</li></ul>', $current_depth - $u['depth']);
    }
    $this->display_tree .= '<li>';
    if ($check_root_user) {
    $this->display_tree .= '<div class="root_div">'
    . '<a href="javascript:void(0)">'
    . '<img class="tree_up_icon" src="' . $user_up_link_image . '" alt="' . $user_text . '" ' . $user_up_link . '/>'
    . '</a>'
    . '</div>';
    }

    $active = $this->tree_model->getActiveType($u['user_id']);
    $tooltip_array = $this->tree_tooltip_array;
    $MODULE_STATUS = $this->trackModule();
    $plan = $MODULE_STATUS['mlm_plan'];
    if ($active != 'server') {
    foreach ($tooltip_array as $row) {
    if ($row['user_id'] == $u['user_id']) {
    $details = $row;
    }
    }

    $userName = str_replace(".", "_", $details['user_name']);
    $html = "<div id='user_".$userName."' class='tooltip_div' style='background-color: white; '> ";
    $html.= "<div class='img_bg tree_common'>";
    $html.= "<img width='80px' height='80px' src='".SITE_URL. "/uploads/images/profile_picture/" .$details['photo']."' alt='".$details['photo']."' align='absmiddle'/>";
    $html.= "<span class='span_username tooltip_username'>" . $details['user_name'] . "</span>"   ;
    $html.= "</div>
    <div class='img_bg tree_common'></div>
    <br clear='all' />
    <div class='tooltip_details' style=' background-color: white;'>
    <table class='tooltip_table' style=' background-color: #fff; height:100px; '>
    <tr><td>" . $details['full_name'] . "</td></tr>
    <tr><td><b style='margin-right: 10px;'>".lang('join_date')." :</b>". $details['join_date'] ."</td></tr>";
    if ($plan == 'Binary') {
    $html.=" <tr>
    <td><b style='margin-right: 10px;'>" . lang('left') ." :</b>" . round($details['left'],2) . "</td>
    </tr>
    <tr>
    <td><b style='margin-right: 10px;'>" .lang('right'). ":</b>" . round($details['right'],2) . "</td>
    </tr>
    <tr>
    <td><b style='margin-right: 10px;'>" .lang('left_carry'). " :</b> " .round($details['left_carry'],2) ."</td>
    </tr>
    <tr>
    <td><b style='margin-right: 10px;'> " . lang('right_carry')." :</b>" . round($details['right_carry'],2) . "</td>
    </tr>";
    }
    $details['personal_pv'] = ($details['personal_pv'] != ''? $details['personal_pv'] : 0) ;
    $details['gpv'] = ($details['gpv'] != ''? $details['gpv'] : 0) ;

    $html.="<tr><td><b style='margin-right: 10px;'>".lang('personal_PV') .":</b>". $details['personal_pv'] ."</td></tr>";
    $html.="<tr><td><b style='margin-right: 10px;'>".lang('group_PV') .":</b>". $details['gpv'] ."</td></tr>";
    if ($plan == 'Donation') {
    if($details['donation_level'] != 'NA')
    $html.="<tr><td><b>". $details['donation_level'] ."</b></td></tr>";
    }
    $html.= "</table>
    </div>";
    if ($MODULE_STATUS['rank_status'] == 'yes' && $details['rank_name'] !='NA' ){
    $html.= "
    <div style='height: 25px; width: 100%;text-align: center;'>
    <div class='btn btn-bricky badge fadeIn' style='margin-top: 0.8em;'>
    <b style='color:#fff'>" . $details['rank_name']. "</b>
    </div>
    </div>";
    }
    $html.= "</div>";

    }else {
    $html = '';
    }
    $html.="<div id='tree' class='orgChart'></div>";

    if ($active != 'server') {
    $this->display_tree .= '<a href="' . $user_link . '" id="level-' . $u['depth'] . '">'. '<img class="tree_icon" src="' . $user_icon . '" alt="' . $user_text . '" id= "userlink_' . $u['user_name'] . '" ' . $user_onclick_link . ' style=" background-color: white;' . $tree_border . '" data-toggle="tooltip" data-html="true" data-trigger="hover" title="' . $html . '"  data-placement="bottom"/>'. '</a>';
    } else{
    $this->display_tree .= '<a href="' . $user_link . '" id="level-' . $u['depth'] . '">'. '<img class="tree_icon" src="' . $user_icon . '" alt="' . $user_text . '" id= "userlink_' . $u['user_name'] . '" ' . $user_onclick_link . ' style="' . $tree_border . '" />'. '</a>';
    }

    if ($user_text != '') {
    $theme_folder = "default";
    if ($this->LOG_USER_TYPE == "user") {
    $theme_folder = $this->USER_THEME_FOLDER;
    } else {
    $theme_folder = $this->ADMIN_THEME_FOLDER;
    }
    $image_path = base_url() . "public_html/images/themes/$theme_folder/tree/";
    $user_down_link_image=$image_path."down.png";
    $this->display_tree .= '<br><div class="line down"></div><div class="username" title=" "  data-placement="bottom" style="' . $tree_background . '">' . $user_text . '</div>';
    if ($u['depth']>=3 &&$active != 'server' ) {
    $this->display_tree .=   '<div class="tree_downline_arrow" style="  width: 100px;" ><a href="javascript:void(0)">'
    . '<img class="" src="' . $user_down_link_image . '" alt="' . $user_text . '" ' . $user_up_link . '/>'
    . '</a></div>';
    }
    }
    $current_depth = $u['depth'];
    if (empty($tree_user_details)) {
    $this->display_tree .= str_repeat('</li></ul>', $current_depth);
    $this->display_tree .= '</li>';
    }
    }

    $this->display_tree .= '</ul>';
    }*/

    public function setTreeTooltipDetails($user_details)
    {
        if ($user_details['active'] != 'server') {
            $tooltip_array = [
                'user_id' => $user_details['user_id'],
                'user_name' => $user_details['user_name'],
                'join_date' => $user_details['join_date'],
                'photo' => $user_details['photo'],
                'join_date' => $user_details['join_date'],
                'full_name' => $user_details['full_name'],
                'personal_pv' => $user_details['personal_pv'],
                'gpv' => $user_details['gpv'],
            ];
            $MODULE_STATUS = $this->trackModule();
            if ($MODULE_STATUS['mlm_plan'] == 'Binary') {
                $leg_arr = $this->OBJ_TREE_VIEW->getLegLeftRightCount($user_details['user_id']);
                $tooltip_array['left'] = $leg_arr['total_left_count'];
                $tooltip_array['left_carry'] = $leg_arr['total_left_carry'];
                $tooltip_array['right'] = $leg_arr['total_right_count'];
                $tooltip_array['right_carry'] = $leg_arr['total_right_carry'];
            } elseif ($MODULE_STATUS['mlm_plan'] == 'Stair_Step') {
                $tooltip_array['personal_pv'] = $this->validation_model->getPersonalPV($user_details['user_id']);
                $tooltip_array['group_pv'] = $this->validation_model->getGroupPV($user_details['user_id']);
            } elseif ($MODULE_STATUS['mlm_plan'] == 'Donation') {
                $d_level = $this->validation_model->getCurrentLevelDonation($user_details['user_id']);
                $tooltip_array['donation_level'] = $this->validation_model->getDonationLevelName($d_level);
            }

            if ($MODULE_STATUS['rank_status'] == 'yes' && $user_details['rank_id']) {
                $tooltip_array['rank_name'] = $this->validation_model->getRankName($user_details['rank_id']);
            } else {
                $tooltip_array['rank_name'] = 'NA';
            }
            $this->tree_tooltip_array[] = $tooltip_array;
        }
    }

    // public function getUserDownlineTreeDetails($user_id, $tree_type = 'tree') {
    //     $level = $this->validation_model->getUserTreeLevel($user_id, $tree_type);
    //     $user_left_right_node = $this->getUserLeftRightNode($user_id, $tree_type);

    //     if ($user_left_right_node && $level != '') {
    //         $this->db->select('f.id user_id,f.user_name,f.active,f.position,f.father_id,f.sponsor_id,f.user_level,f.sponsor_level,f.date_of_joining join_date,f.user_rank_id rank_id,u.user_detail_name first_name,u.user_detail_second_name last_name,CONCAT(u.user_detail_name,u.user_detail_second_name) full_name,u.user_photo photo,f.personal_pv,f.gpv', FALSE);
    //         $this->db->from('ft_individual f');
    //         $this->db->join('tree_parser t', 't.ft_id = f.id', 'LEFT');
    //         $this->db->join('user_details u', 'f.id = u.user_detail_refid', 'LEFT');
    //         if ($tree_type == 'tree') {
    //             $this->db->select("(f.user_level - {$level}) depth", FALSE);
    //             $this->db->where("f.user_level - {$level} < ", TREE_LEVEL, FALSE);
    //             //$this->db->where("f.left_sponsor BETWEEN {$user_left_right_node['left']} AND {$user_left_right_node['right']}");
    //             $this->db->where("t.left_father >=", $user_left_right_node['left']);
    //             $this->db->where("t.left_father <=", $user_left_right_node['right']);
    //             $this->db->order_by('t.left_father');
    //         } elseif ($tree_type == 'sponsor_tree') {
    //             $this->db->select("(f.sponsor_level - {$level}) depth,t.left_sponsor,t.right_sponsor", FALSE);
    //             $this->db->where("f.sponsor_level - {$level} < ", TREE_LEVEL, FALSE);
    //             $this->db->where("f.active != ", 'server');
    //             //$this->db->where("f.left_sponsor BETWEEN {$user_left_right_node['left']} AND {$user_left_right_node['right']}");
    //             $this->db->where("t.left_sponsor >=", $user_left_right_node['left']);
    //             $this->db->where("t.left_sponsor <=", $user_left_right_node['right']);
    //             $this->db->order_by('t.left_sponsor');
    //         }
    //         $query = $this->db->get();
    //         return $query->result_array();
    //     }
    // }

    // public function getUserLeftRightNode($user_id, $tree_type = 'tree') {
    //     if ($tree_type == 'tree') {
    //         $this->db->select('left_father left,right_father right');
    //     } elseif ($tree_type == 'sponsor_tree') {
    //         $this->db->select('left_sponsor left,right_sponsor right');
    //     }
    //     $this->db->where('ft_id', $user_id);
    //     $query = $this->db->get('tree_parser');
    //     return $query->row_array();
    // }

    public function getChildren($id)
    {

        $children = array();
        $this->db->select('id,father_id,active,user_name,position');
        $this->db->from('ft_individual');
        $this->db->where("father_id", $id);
        $this->db->where("active!=", "server");
        $this->db->order_by('position', 'ASC');
        $res = $this->db->get();
        foreach ($res->result_array() as $row) {
            $title = $row['user_name'];
            $children[] = array(
                'title' => $title,
                'id' => $title,
                // 'icon' => 'icon-user',
                'lazy' => true,
            );
        }
        return json_encode($children);
    }

    public function getBoardNumberFromBoardUserDetails($user_id, $board_number)
    {

        $board_seriel_no = 0;
        if ($user_id != 0) {
            $query = $this->db->select_max("board_serial_no")->where("user_id", "$user_id")->where("board_table_name", "$board_number")->get("board_user_detail");

            foreach ($query->result() as $row) {
                if ($row->board_serial_no != "") {
                    $board_seriel_no = $row->board_serial_no;
                }
            }
        } else {
            $board_seriel_no = 1;
        }
        return $board_seriel_no;
    }

    public function getUserBoard($user_board_id, $board_no)
    {
        $this->auto_board_filling_model->setBoardWidthAndDepth($board_no);
        $board_width = $this->auto_board_filling_model->BOARD_WIDTH;
        $board_depth = $this->auto_board_filling_model->BOARD_DEPTH;
        $board_slno = $this->auto_board_filling_model->getBoardNumberFromBoardUserDetails($user_board_id, $board_no);
        $board_top_id = $this->auto_board_filling_model->getBoardTopID($board_slno, $board_no);

        $this->getAllBoardUsers($board_top_id, $board_slno, $board_no, $board_width, $board_depth);
        $board_arr['board_users'] = $this->board_array;
        $board_arr['board_depth'] = $board_depth;
        $board_arr['board_width'] = $board_width;
        return $board_arr;
    }

    public function getAllBoardUsers($board_id, $board_slno, $board_no, $board_width, $board_depth, $level = 0, $order = 0)
    {

        if ($level == 0) {
            $level = $board_depth;
            $this->board_array[$level][$order]['id'] = $board_id;
            $this->board_array[$level][$order]['user_name'] = $this->OBJ_VAL->IdToUserNameBoard($board_id, $board_no);

            $user_id = $this->validation_model->getUserIDByBoardID($board_id, $board_no);
            $ft_user_details = $this->validation_model->getUserFTDetails($user_id);
            $user_details = $this->validation_model->getAllUserDetails($user_id);
            $referral_count = $this->validation_model->getUserReferralCount($user_id);
            $board_username = $this->validation_model->IdToUserNameBoard($board_id, $board_no);
            $person_pv = ($ft_user_details["personal_pv"] != '' ? $ft_user_details["personal_pv"] : 0);
            $gpv = ($ft_user_details["gpv"] != '' ? $ft_user_details["gpv"] : 0);
            $tooltip_array = array(
                "user_id" => $this->board_array[$level][$order]['id'],
                "user_name" => $this->board_array[$level][$order]['user_name'],
                "board_username" => $board_username,
                "date_of_joining" => $ft_user_details["date_of_joining"],
                "user_photo" => $user_details["user_photo"],
                "full_name" => $user_details['user_detail_name'] . " " . $user_details["user_detail_second_name"],
                "referral_count" => $referral_count,
                "personal_pv" => $person_pv,
                "gpv" => $gpv,
            );
            $MODULE_STATUS = $this->trackModule();
            if ($MODULE_STATUS['rank_status'] == "yes" && $ft_user_details['user_rank_id']) {
                $tooltip_array["user_rank"] = $this->validation_model->getRankName($ft_user_details['user_rank_id']);
            } else {
                $tooltip_array["user_rank"] = "NA";
            }
            $this->board_tooltip_array[] = $tooltip_array;
        }
        if ($level) {
            $level--;
            $child_nodes = $this->auto_board_filling_model->getUserChildNodes($board_id, $board_no);

            for ($k = 0; $k < $board_width; $k++) {
                $child_id = 0;
                $user_name = "NA";
                if (array_key_exists($k, $child_nodes)) {
                    $child_id = $child_nodes[$k];
                    $user_name = $this->OBJ_VAL->IdToUserNameBoard($child_id, $board_no);

                    $user_id = $this->validation_model->getUserIDByBoardID($child_id, $board_no);
                    $ft_user_details = $this->validation_model->getUserFTDetails($user_id);
                    $user_details = $this->validation_model->getAllUserDetails($user_id);
                    $referral_count = $this->validation_model->getUserReferralCount($user_id);
                    $board_username = $this->validation_model->IdToUserNameBoard($child_id, $board_no);
                    $person_pv = ($ft_user_details["personal_pv"] != '' ? $ft_user_details["personal_pv"] : 0);
                    $gpv = ($ft_user_details["gpv"] != '' ? $ft_user_details["gpv"] : 0);
                    $tooltip_array = array(
                        "user_id" => $child_id,
                        "user_name" => $user_name,
                        "board_username" => $board_username,
                        "date_of_joining" => $ft_user_details["date_of_joining"],
                        "user_photo" => $user_details["user_photo"],
                        "full_name" => $user_details['user_detail_name'] . " " . $user_details["user_detail_second_name"],
                        "referral_count" => $referral_count,
                        "personal_pv" => $person_pv,
                        "gpv" => $gpv,
                    );
                    $MODULE_STATUS = $this->trackModule();
                    if ($MODULE_STATUS['rank_status'] == "yes" && $ft_user_details['user_rank_id']) {
                        $tooltip_array["user_rank"] = $this->validation_model->getRankName($ft_user_details['user_rank_id']);
                    } else {
                        $tooltip_array["user_rank"] = "NA";
                    }
                    $this->board_tooltip_array[] = $tooltip_array;
                }
                $order++;
                $this->board_array[$level][$order]['id'] = $child_id;
                $this->board_array[$level][$order]['user_name'] = $user_name;

                if ($level) {
                    $order = $this->getAllBoardUsers($child_id, $board_slno, $board_no, $board_width, $board_depth, $level, $order);
                }
            }
        }
        return $order;
    }

    public function getUserLeftAndRight($user_id, $type)
    {
        $this->db->select("left_$type, right_$type");
        $this->db->where('ft_id', $user_id);
        $result = $this->db->get('tree_parser');
        $result = $result->result_array();
        return $result[0];
    }

    public function getAllTreeUsers($user_id, $type = "tree")
    {
        $this->display_tree = '<ul id="tree_view" style="display:none">';
        if ($type == "tree") {
            $this->getDisplayTree($user_id);
        } else if ($type == "sponsor_tree") {
            $this->getDisplaySponsorTree($user_id);
        }
        $this->display_tree .= '</ul>';
    }

    public function getDisplayTree($user_id, $level = 0)
    {
        if ($level < TREE_LEVEL) {

            $tree_user_detail = $this->getTreeUserDetails($user_id);

            $user_name = $tree_user_detail["user_name"];
            $user_active = $tree_user_detail["active"];
            $father_id = $tree_user_detail["father_id"];
            $father_id_encrypt = $tree_user_detail["father_id_encrypt"];
            $user_position = $tree_user_detail["position"];

            $user_icon_link = $this->getUserIconAndLink($user_id, $user_name, $user_active, $father_id, $father_id_encrypt, $user_position, $level);

            $user_icon = $user_icon_link["icon"];
            $user_link = $user_icon_link["link"];
            $user_up_link = $user_icon_link["up_link"];
            $user_up_link_image = $user_icon_link["up_link_image"];
            $user_text = $user_icon_link["text"];
            $tree_color = $user_icon_link["tree_color"];

            $tree_border = "border: 2px solid $tree_color !important;";
            $tree_background = "background: $tree_color !important;";

            $user_onclick_link = ($user_active != "server") ? 'onclick=\'getGenologyTree("' . $user_name . '",event);\'' : '';

            $this->display_tree .= '<li>';
            $check_root_user = false;
            if ($level < 1) {
                if ($this->LOG_USER_TYPE != 'employee') {
                    if ($user_id != $this->LOG_USER_ID) { //root user
                        $check_root_user = true;
                    }
                } else {
                    if ($user_id != $this->ADMIN_USER_ID) { //root user
                        $check_root_user = true;
                    }
                }
            }

            if ($check_root_user) { //root user
                $this->display_tree .= '<div class="root_div">'
                    . '<a href="javascript:void(0)">'
                    . '<img class="tree_up_icon" src="' . $user_up_link_image . '" alt="' . $user_text . '" ' . $user_up_link . '/>'
                    . '</a>'
                    . '</div>';
            }
            $this->display_tree .= '<a href="' . $user_link . '" id="level-' . $level . '">'
                . '<img class="tree_icon" src="' . $user_icon . '" alt="' . $user_text . '" id= "userlink_' . $user_name . '" ' . $user_onclick_link . ' style="' . $tree_border . '"/>'
                . '</a>';

            if ($user_text != '') {
                $this->display_tree .= '<br><div class="line down"></div><div class="username" style="' . $tree_background . '">' . $user_text . '</div>';
            }

            $child_nodes = $this->getUserChildNodes($user_id);

            $child_count = count($child_nodes);
            if ($child_count) {
                $new_level = $level + 1;
                if ($new_level < TREE_LEVEL) {
                    $this->display_tree .= '<ul>';
                    for ($k = 0; $k < $child_count; $k++) {
                        $child_id = $child_nodes[$k];
                        $this->getDisplayTree($child_id, $new_level);
                    }
                    $this->display_tree .= '</ul>';
                }
            }
            $this->display_tree .= '</li>';
        }
        return $this->tree_array;
    }

    public function getTreeUserDetails($user_id)
    {

        $ft_user_details = $this->validation_model->getUserFTDetails($user_id);
        $user_details = $this->validation_model->getAllUserDetails($user_id);

        $tree_user_detail["user_id"] = $user_id;

        $tree_user_detail["user_name"] = $ft_user_details["user_name"];

        $tree_user_detail["active"] = $ft_user_details["active"];

        $tree_user_detail["position"] = $ft_user_details["position"];

        $tree_user_detail["father_id"] = $ft_user_details["father_id"];

        $tree_user_detail["father_id_encrypt"] = $this->OBJ_TREE_VIEW->getEncrypt($ft_user_details["father_id"]);

        $tree_user_detail["sponsor_id"] = $ft_user_details["sponsor_id"];

        $tree_user_detail["sponsor_id_encrypt"] = $this->OBJ_TREE_VIEW->getEncrypt($ft_user_details["sponsor_id"]);

        if ($ft_user_details["active"] != "server") {
            $tooltip_array = array(
                "user_id" => $user_id,
                "user_name" => $ft_user_details["user_name"],
                "join_date" => $ft_user_details["date_of_joining"],
            );
            if ($user_details) {
                $tooltip_array["photo"] = $user_details["user_photo"];
                $tooltip_array["full_name"] = $user_details['user_detail_name'] . " " . $user_details["user_detail_second_name"];

                $MODULE_STATUS = $this->trackModule();
                if ($MODULE_STATUS['mlm_plan'] == "Binary") {
                    $leg_arr = $this->OBJ_TREE_VIEW->getLegLeftRightCount($user_id);
                    $tooltip_array["left"] = $leg_arr['total_left_count'];
                    $tooltip_array["left_carry"] = $leg_arr['total_left_carry'];
                    $tooltip_array["right"] = $leg_arr['total_right_count'];
                    $tooltip_array["right_carry"] = $leg_arr['total_right_carry'];
                } elseif ($MODULE_STATUS['mlm_plan'] == "Stair_Step") {
                    $tooltip_array["personal_pv"] = $this->validation_model->getPersonalPV($user_id);
                    $tooltip_array["group_pv"] = $this->validation_model->getGroupPV($user_id) + $tooltip_array["personal_pv"];
                }

                if ($MODULE_STATUS['rank_status'] == "yes" && $ft_user_details['user_rank_id']) {
                    $tooltip_array["rank_name"] = $this->validation_model->getRankName($ft_user_details['user_rank_id']);
                } else {
                    $tooltip_array["rank_name"] = "NA";
                }
            } else {
                $tooltip_array["photo"] = "NA";
                $tooltip_array["full_name"] = "NA";
                $tooltip_array["rank_name"] = "NA";
            }
            $this->tree_tooltip_array[] = $tooltip_array;
        }
        return $tree_user_detail;
    }

    public function getUserChildNodes($user_id, $type = "tree")
    {
        $child_nodes = array();
        if ($user_id) {
            $this->db->select('id');
            if ($type == "sponsor_tree") {
                $this->db->where("sponsor_id", $user_id);
                $this->db->where("active !=", "server");
            } else {
                $this->db->where("father_id", $user_id);
            }
            if ($this->MLM_PLAN != "Binary") {
                $this->db->order_by("order_id", "ASC");
            } else {
                $this->db->order_by("position", "ASC");
            }
            $query = $this->db->get("ft_individual");
            foreach ($query->result_array() as $rows) {
                $child_nodes[] = $rows['id'];
            }
        }
        return $child_nodes;
    }

    public function getUserIconAndLink($user_id, $user_name, $user_active, $father_id, $father_id_encrypt, $user_position, $level)
    {

        $father_user_name = $this->validation_model->IdToUserName($father_id);
        $theme_folder = "default";
        if ($this->LOG_USER_TYPE == "user") {
            $theme_folder = $this->USER_THEME_FOLDER;
        } else {
            $theme_folder = $this->ADMIN_THEME_FOLDER;
        }

        $image_path = base_url() . "public_html/images/themes/$theme_folder/tree/";
        $user_icon = "inactive.png";
        $user_link = "javascript:void(0)";
        $user_up_link = $user_up_link_image = "";
        $user_text = ($user_active != "server") ? $user_name : lang('ADD_HERE');
        switch ($user_active) {
            case "yes":
                $user_icon = "active.png";
                break;
            case "no":
                $user_icon = "inactive.png";
                break;
            case "terminated":
                $user_icon = "terminate.png";
                break;
            case "server":
                $user_icon = "add.png";
                if (!$this->FROM_MOBILE) {
                    $MODULE_STATUS = $this->trackModule();
                    if ($MODULE_STATUS['mlm_plan'] == "Unilevel") {
                        if (($father_id == $this->LOG_USER_ID) || ($this->LOG_USER_ID == $this->ADMIN_USER_ID)) {
                            $user_link = base_url() . "register/user_register/$father_id_encrypt/$user_position";
                        } else {
                            $user_icon = "add_disabled.png";
                            $user_link = "javascript:void(0)";
                            $user_text = '';
                        }
                    } else {
                        $user_link = base_url() . "register/user_register/$father_id_encrypt/$user_position";
                    }
                } else {
                    $user_text = '';
                }
                $mlm_plan = $this->validation_model->getMLMPlan();
                if ($mlm_plan == 'Binary') {
                    // $binary_leg_allowed = $this->getAllowedBinaryLeg($father_id, $this->LOG_USER_TYPE, $this->LOG_USER_ID);
                    $binary_leg_allowed = null;
                    if ($binary_leg_allowed != 'any' && $binary_leg_allowed != $user_position) {
                        $user_icon = "add_disabled.png";
                        $user_link = "javascript:void(0)";
                        $user_text = '';
                    }
                }
                break;
            default:
                $user_icon = "inactive.png";
        }

        if ($user_id != $this->LOG_USER_ID) {
            $user_up_link = 'onclick=\'getGenologyTree("' . $father_user_name . '",event);\'';
            $user_up_link_image = $image_path . "up.png";
        }

        switch ($theme_folder) {
            case "Dandelion":
                $tree_color = "#807979";
                break;
            case "TrueBlue":
                $tree_color = "#275E7F";
                break;
            default:
                $tree_color = "#454552";
        }

        $user_icon_link = array("icon" => $image_path . $user_icon, "link" => $user_link, "up_link" => $user_up_link, "up_link_image" => $user_up_link_image, "text" => $user_text, "tree_color" => $tree_color);
        return $user_icon_link;
    }

    public function getDisplaySponsorTree($user_id, $level = 0)
    {
        if ($level < TREE_LEVEL) {

            $tree_user_detail = $this->getTreeUserDetails($user_id);

            $user_name = $tree_user_detail["user_name"];
            $user_active = $tree_user_detail["active"];
            $father_id = $tree_user_detail["sponsor_id"];
            $father_id_encrypt = $tree_user_detail["sponsor_id_encrypt"];
            $user_position = $tree_user_detail["position"];

            $user_icon_link = $this->getUserIconAndLink($user_id, $user_name, $user_active, $father_id, $father_id_encrypt, $user_position, $level);

            $user_icon = $user_icon_link["icon"];
            $user_link = $user_icon_link["link"];
            $user_up_link = $user_icon_link["up_link"];
            $user_up_link_image = $user_icon_link["up_link_image"];
            $user_text = $user_icon_link["text"];
            $tree_color = $user_icon_link["tree_color"];

            $tree_border = "border: 2px solid $tree_color !important;";
            $tree_background = "background: $tree_color !important;";

            $user_onclick_link = ($user_active != "server") ? 'onclick=\'getGenologyTree("' . $user_name . '",event);\'' : '';

            $this->display_tree .= '<li>';

            $check_root_user = false;
            if ($level < 1) {
                if ($this->LOG_USER_TYPE != 'employee') {
                    if ($user_id != $this->LOG_USER_ID) { //root user
                        $check_root_user = true;
                    }
                } else {
                    if ($user_id != $this->ADMIN_USER_ID) { //root user
                        $check_root_user = true;
                    }
                }
            }

            if ($check_root_user) { //root user
                $this->display_tree .= '<div class="root_div">'
                    . '<a href="javascript:void(0)">'
                    . '<img class="tree_up_icon" src="' . $user_up_link_image . '" alt="' . $user_text . '" ' . $user_up_link . '/>'
                    . '</a>'
                    . '</div>';
            }
            $this->display_tree .= '<a href="' . $user_link . '" id="level-' . $level . '">'
                . '<img class="tree_icon" src="' . $user_icon . '" alt="' . $user_text . '" id= "userlink_' . $user_name . '" ' . $user_onclick_link . ' style="' . $tree_border . '"/>'
                . '</a>'
                . '<br><div class="line down"></div><div class="username" style="' . $tree_background . '">' . $user_text . '</div>';

            $child_nodes = $this->getUserChildNodes($user_id, "sponsor_tree");

            $child_count = count($child_nodes);

            if ($child_count) {
                $new_level = $level + 1;
                if ($new_level < TREE_LEVEL) {
                    $this->display_tree .= '<ul>';
                    for ($k = 0; $k < $child_count; $k++) {
                        $child_id = $child_nodes[$k];
                        $this->getDisplaySponsorTree($child_id, $new_level);
                    }
                    $this->display_tree .= '</ul>';
                }
            }
            $this->display_tree .= '</li>';
        }
        return $this->tree_array;
    }

    public function getDownlines($next_lines, $down_lines)
    {
        // $this->db->select('id,user_name');
        // $this->db->from('ft_individual');
        // $this->db->where('active !=', 'server');
        // $this->db->where('active !=', 'terminated');
        // $this->db->where_in('father_id', $next_lines );
        // $query = $this->db->get();
        // $next_lines = array();
        // foreach ($query->result_array() as $row) {
        //     array_push($next_lines, $row['id']);
        //     array_push($down_lines, $row);
        // }
        // if (empty($next_lines)) {
        //     return $down_lines;
        // } else {
        //     return $this->getDownlines($next_lines, $down_lines);
        // }
        $array = $this->db->select('descendant')
            ->from('ft_individual')
            ->where('ancestor', $next_lines)
            ->get()->result_array();
        for ($i = 0; $i < count($array); $i++) {
            array_push($down_lines, $array[$i]['descendant']);
        }
        return $down_lines;
    }

    public function getAllStepUsers($user_id)
    {

        $MODULE_STATUS = $this->trackModule();
        $max_step_id = $this->validation_model->getUserStairStepId($user_id);
        $order = 0;
        $this->step_array['width'] = 0;

        if(!$max_step_id) {
            return true;
        }

        $referral_count = $this->validation_model->getUserReferralCount($user_id);
        $rank_id = $this->validation_model->getRankId($user_id);

        $this->step_array[$max_step_id][$order]['id'] = $user_id;
        $this->step_array[$max_step_id][$order]['user_name'] = $this->validation_model->IdToUserName($user_id);

        $tooltip_array = array(
            "user_id" => $this->step_array[$max_step_id][$order]['id'],
            "user_name" => $this->step_array[$max_step_id][$order]['user_name'],
            "date_of_joining" => $this->validation_model->getJoiningData($user_id),
            "user_photo" => $this->validation_model->getUserImage($user_id),
            "full_name" => $this->validation_model->getUserFullName($user_id),
            "referral_count" => $referral_count,
            'group_pv' => $this->validation_model->getGroupPV($user_id),
            'personal_pv' => $this->validation_model->getPersonalPV($user_id),
        );
        if ($MODULE_STATUS['rank_status'] == "yes" && $rank_id) {
            $tooltip_array["user_rank"] = $this->validation_model->getRankName($rank_id);
        } else {
            $tooltip_array["user_rank"] = "NA";
        }
        $this->step_tooltip_array[] = $tooltip_array;

        $this->db->select('st.*,ft.user_name,ft.date_of_joining,ft.user_rank_id');
        $this->db->where('st.leader_id', $user_id);
        $this->db->where('st.step_id !=', 0);
        $this->db->from('ft_individual as ft');
        $this->db->join('stair_step as st', 'st.user_id = ft.id');
        $this->db->where('st.breakaway_status', 'no');

        $result = $this->db->get();
        foreach ($result->result_array() as $key => $value) {

            $user_id = $value['user_id'];
            $step_id = $value['step_id'];
            $user_rank_id = $value['user_rank_id'];

            $referral_count = $this->validation_model->getUserReferralCount($user_id);

            $this->step_array[$step_id][$order]['id'] = $user_id;
            $this->step_array[$step_id][$order]['user_name'] = $value['user_name'];

            $tooltip_array = array(
                "user_id" => $user_id,
                "user_name" => $value['user_name'],
                "date_of_joining" => $value["date_of_joining"],
                "user_photo" => $this->validation_model->getUserImage($user_id),
                "full_name" => $this->validation_model->getUserFullName($user_id),
                "referral_count" => $referral_count,
                'group_pv' => $this->validation_model->getGroupPV($user_id),
                'personal_pv' => $this->validation_model->getPersonalPV($user_id),
            );
            if ($MODULE_STATUS['rank_status'] == "yes" && $user_rank_id) {
                $tooltip_array["user_rank"] = $this->validation_model->getRankName($user_rank_id);
            } else {
                $tooltip_array["user_rank"] = "NA";
            }
            $order++;
            $this->step_tooltip_array[] = $tooltip_array;

            $this->step_array['width'] = isset($this->step_array[$step_id - 1]) ? ((count($this->step_array[$step_id]) > count($this->step_array[$step_id - 1])) ? count($this->step_array[$step_id]) : count($this->step_array[$step_id - 1])) : count($this->step_array[$step_id]);
        }
        for ($i = $max_step_id; $i > 0; $i--) {
            if (!isset($this->step_array[$i])) {
                $this->step_array[$i] = array();
            }
        }
        krsort($this->step_array);
    }

    public function getDownlineUsersCount($user_id, $type, $from, $to)
    {
        $this->db->from('treepath t');
        $this->db->join('ft_individual f', 't.descendant = f.id', 'LEFT');
        $this->db->where("t.ancestor", $user_id);
        $this->db->where("f.active !=", "server");
        $where = "f.date_of_joining between '$from' and '$to'";
        $this->db->where($where);
        $numrows = $this->db->count_all_results();
        return $numrows;
    }

    public function getLeftRightDownlineUsersCount($user_id, $type, $from = "", $to = "")
    {
        if ($type == "sponsor") {
            $this->db->from('sponsor_treepath t');
        } else {
            $this->db->from('treepath t');
        }
        $this->db->join('ft_individual f', 't.descendant = f.id', 'LEFT');
        $this->db->where("t.ancestor", $user_id);
        $where = "f.date_of_joining between '$from' and '$to'";
        if ($from != "" && $to != "") {
            $this->db->where($where);
        }

        $numrows = $this->db->count_all_results();
        return $numrows;
    }

    public function updateLeg($user_id, $leg)
    {
        $this->db->set("binary_leg", $leg);
        $this->db->where("id =", $user_id);
        $res = $this->db->update("ft_individual");
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    public function get_leg_type($user_id)
    {
        $this->db->select('binary_leg');
        $this->db->where("id =", $user_id);
        $this->db->limit(1);
        $query = $this->db->get('ft_individual');
        foreach ($query->result_array() as $rows) {
            $binary_leg = $rows['binary_leg'];
        }
        return $binary_leg;

    }

    public function get_bnary_leg_setng()
    {
        $this->db->select('binary_leg');
        $query = $this->db->get('signup_settings');
        foreach ($query->result_array() as $rows) {
            $binary_leg = $rows['binary_leg'];
        }
        return $binary_leg;
    }
    public function checkAncestor($ancestor, $descendant, $table = "treepath")
    {
        $numrows = $this->db->where("ancestor", $ancestor)
            ->where("descendant", $descendant)
            ->count_all_results($table);
        return ($numrows > 0);
    }
    public function getUserLeftRightNode($user_id)
    {
        $data = ['L' => '', 'R' => ''];
        $array = $this->db->select('id,position')->where('father_id', $user_id)->get('ft_individual')->result_array();
        foreach ($array as $row) {
            if ($row['position'] == "L") {
                $data['L'] = $row['id'];
            } else {
                $data['R'] = $row['id'];
            }
        }
        return $data;
    }
    public function getUserPositionFromParent($user_id, $parent_id)
    {
        $user_node = $this->getUserLeftRightNode($user_id, 'tree');

        $this->db->select('t.left_father left,t.right_father right');
        $this->db->from('ft_individual f');
        $this->db->join('tree_parser t', 't.ft_id = f.id', 'LEFT');
        $this->db->where('f.father_id', $parent_id);
        $query1 = $this->db->get();
        $parent_child_node = $query1->result_array();

        $this->db->select('f.position');
        $this->db->from('ft_individual f');
        $this->db->join('tree_parser t', 't.ft_id = f.id', 'LEFT');
        $this->db->where('t.left_father <=', $user_node['left']);
        $this->db->where('t.right_father >=', $user_node['right']);
        if ($parent_child_node) {
            $this->db->group_start();
            foreach ($parent_child_node as $k => $v) {
                if ($k === 1) {
                    $this->db->or_group_start();
                }
                $this->db->where('t.left_father', $v['left']);
                $this->db->where('t.right_father', $v['right']);
                if ($k === 1) {
                    $this->db->group_end();
                }
            }
            $this->db->group_end();
        }
        $query2 = $this->db->get();
        return $query2->row_array()['position'];
    }

    // public function getAllowedBinaryLeg($user_id, $log_user_type, $log_user_id)
    // {
    //     if (!$log_user_type) {
    //         return 'any';
    //     }
    //     $admin_id = $this->validation_model->getAdminId();
    //     if ($log_user_type == 'employee') {
    //         $log_user_id = $admin_id;
    //     }
    //     $this->load->model('configuration_model');
    //     $binary_leg = $this->configuration_model->getSignupBinaryLeg();
    //     if ($binary_leg == 'any') {
    //         if ($log_user_type == 'admin' || $log_user_type == 'employee') {
    //             return 'any';
    //         }
    //         else {
    //             $user_binary_leg = $this->configuration_model->getUserWiseSignupBinaryLeg($log_user_id);
    //             if ($user_binary_leg == 'any') {
    //                 return 'any';
    //             }
    //             else {
    //                 $parent_id = $log_user_id;

    //                 if ($parent_id == $user_id) {
    //                     return $user_binary_leg;
    //                 }
    //                 else {
    //                     $position_from_parent = $this->getUserPositionFromParent($user_id, $parent_id);
    //                     if ($position_from_parent == $user_binary_leg) {
    //                         return 'any';
    //                     }
    //                     else {
    //                         return '';
    //                     }
    //                 }
    //             }
    //         }
    //     }
    //     else {
    //         $parent_id = $admin_id;
    //         if ($parent_id == $user_id) {
    //             return $binary_leg;
    //         }
    //         else {
    //             $position_from_parent = $this->getUserPositionFromParent($user_id, $parent_id);
    //             if ($position_from_parent == $binary_leg) {
    //                 return 'any';
    //             }
    //             else {
    //                 return '';
    //             }
    //         }
    //     }
    // }

    public function getActiveType($user_id)
    {
        $this->db->select('active');
        $this->db->from('ft_individual');
        $this->db->where('id', $user_id);
        $query = $this->db->get();
        return $query->row("active");
    }

    public function getAvailablePositions($placement_id, $position)
    {
        $this->load->model('configuration_model');
        $admin_id = $this->validation_model->getAdminId();
        $available_positions = $this->configuration_model->getSignupBinaryLeg();
        if ($available_positions == 'any') {
            return ['L', 'R', 'any'];
        } elseif ($placement_id == $admin_id) {
            return [$available_positions];
        } elseif ($this->isLeftRightDownline($admin_id, $placement_id, $position)) {
            return ['L', 'R'];
        } else {
            return [];
        }

    }

    public function isLeftRightDownline($root_id, $placement_id, $position)
    {
        $this->db->select('id');
        $this->db->where('father_id', $root_id);
        $this->db->where('position', $position);
        $query = $this->db->get('ft_individual');
        $left_right_id = $query->row_array()['id'];

        $this->db->where('ancestor', $left_right_id);
        $query2 = $this->db->get_compiled_select('treepath');
        $query3 = $this->db->query("SELECT EXISTS ({$query2}) AS found");
        return $query3->row_array()['found'];
    }

    public function getUserPositionParentBase($user_id, $parent_id)
    {

        while ($user_id != $parent_id) {

            $father_id = $this->validation_model->getFatherId($user_id);
            if ($father_id == $parent_id) {
                $position = $this->validation_model->getUserPosition($user_id);
                return $position;
            }
            $user_id = $father_id;

        }

    }
    public function IsPlacementUnderSponsor($placement_id, $sponsor_id)
    {

        $flag = false;
        if ($placement_id == $sponsor_id) {
            $flag = true;
            return $flag;
        }
        $user_id = $placement_id;
        $admin_id = $this->validation_model->getAdminId();

        while ($user_id != $admin_id) {

            $father_id = $this->validation_model->getFatherId($user_id);
            if ($father_id == $sponsor_id) {
                $flag = true;
                return $flag;
            }
            $user_id = $father_id;
        }
        return $flag;

    }
    public function checkUserPositionParentBase($user_id, $parent_id,$current_position,$user_position)
    {

        while ($user_id != $parent_id) {

            $father_id = $this->validation_model->getFatherId($user_id);
            if ($father_id == $parent_id) {
                $position = $this->validation_model->getUserPosition($user_id);
                if($position!=$user_position){
                    return TRUE;
                }else{
                    return FALSE;
                }
            }
            $position = $this->validation_model->getUserPosition($user_id);
            if($position!=$user_position){
                return TRUE;
            }
            $user_id = $father_id;

        }

    }
}
