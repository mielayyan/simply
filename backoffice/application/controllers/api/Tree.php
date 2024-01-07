<?php

require_once 'Inf_Controller.php';

class Tree extends Inf_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("android/new/android_model");
        $this->load->model('Api_model');
        $this->load->model('tree_model');
        $this->LOG_USER_ID = $this->rest->user_id;
        $this->LOG_USER_TYPE = 'user';
    }

    function index() {
        $this->redirect("", "tree_view");
    }
 
    /**
     * [newtwork_menu_get description]
     * @return [type] [description]
     */
    public function newtwork_menu_get() {
        $this->lang->load('menu', $this->LANG_NAME);
        $this->lang->load('home', $this->LANG_NAME);
        $this->load->model('api_model');
        $data = [];
        $urls = $this->api_model->get_network_menu();
        foreach($urls as $key => $url) {
            $data[$key]['id']    = explode("/",$url->url)[1];
            $data[$key]['title'] = lang($url->title);
            $data[$key]['url']   = base_url('user/'.$url->url).'?access-token='.$this->input->get_request_header('access-token').'&api-key='.$this->input->get_request_header('api-key');
            $data[$key]['register_url'] = base_url('register/user_register');
            if($url->id == 78) { // Downline Members getDownlineUsers
                $data[$key]['data']  = [[
                    'title'        => lang('total_downline_count'),
                    'value'        => (int) $this->api_model->getUserDownlineMembersCount($this->rest->user_id),
                ]];
            } 

            if($url->id == 79) { // Referel Members 
                $data[$key]['data']  = [[
                    'title' => lang('total_referral_count'),
                    'value' => (int) $this->validation_model->getUserReferralCount($this->rest->user_id)
                ]];
            } 
        }
        $this->set_success_response(200, $data);
    }

    //downline Members
    function downline_members_get(){
        $this->form_validation->set_data($this->get());
        $rules = [
            [
                'field' => 'offset',
                'label' => lang("offset"),
                'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
            ],
            [
                'field' => 'limit',
                'label' => lang("limit"),
                'rules' => 'trim|required|numeric|greater_than_equal_to[0]|less_than_equal_to[1000]'
            ],
        ];
        $this->form_validation->set_rules($rules);
        if (!$this->form_validation->run()) {
            $this->set_error_response(422, 1004);
        }

        $this->load->model('my_report_model');
        $this->LOG_USER_ID = $this->rest->user_id;
        $this->LOG_USER_TYPE = 'user';
        $user_id = $this->LOG_USER_ID;
        $data = [];
        $total_downline_count = $this->my_report_model->getTotalDownlineUsersCount($user_id);
        $total_levels = $this->my_report_model->getMaxLevelUser($user_id);
        $data['total_downline_count'] = $total_downline_count;
        $data['total_levels'] = $total_levels;
        $level_value = (int) $this->input->get('level') ?: 'all';
        $page = $this->input->get('offset') ?: 0;
        $limit = $this->input->get('limit') ?: 0;
        if ($level_value != 'all') {
            $binary_level = $level_value;
            $level_value = $this->validation_model->getUserLevel($user_id) + $level_value;
            $level_arr_rs = $this->my_report_model->getTotalDownlineUsersBinary($user_id, $level_value);
            $data['total_downline_count'] = $level_arr_rs;
            $binary = $this->my_report_model->getDownlineDetailsBinary($user_id, $limit, $page, $level_value);
        } else {
            $level_arr_rs = $this->my_report_model->getTotalDownlineUsersBinary($user_id);
            $data['total_downline_count'] = $level_arr_rs;
            $binary = $this->my_report_model->getDownlineDetailsBinary($user_id, $limit, $page);
            $binary_level = 'all';
        }
        foreach ($binary as $key => $value) {
           if (file_exists(IMG_DIR . "profile_picture/" . $binary[$key]['user_photo'])) {
                $binary[$key]['user_photo'] = SITE_URL. "/uploads/images/profile_picture/" . $binary[$key]['user_photo'];
            } else {
                $binary[$key]['user_photo'] = SITE_URL. "/uploads/images/profile_picture/nophoto.png";
            }
        }
        $data['tableData']= $binary;
        $this->set_success_response(200,$data);
    }
    
    public function genealogy_tree_get() {
        $rank_status = "yes";
        $type = "tree";
        if($this->get('user_name')){
            $user_id = $this->validation_model->userNameToID($this->get('user_name'));
            $checkIsDownline = $this->tree_model->checkDownline($this->LOG_USER_ID, $user_id); 
            if(!$checkIsDownline){
                $this->set_error_response(422, 1043);
            }
        }else if($this->get('user_id')){
            if(!$this->validation_model->isUserExists($this->get('user_id'))){
                $this->set_error_response(422, 1043);
            }
            $user_id = $this->get('user_id');
        }else{
            $user_id =$this->LOG_USER_ID;   
        }
        if(!$user_id) {
            $this->set_error_response(422, 1043);
        }


        $array = $this->tree_model->react_tree($this->MLM_PLAN, $rank_status, $user_id, $type);
        // $array = $this->tree_model->getTreeDownlines($this->MLM_PLAN, $rank_status, $user_id, $type);
        $keyed = array();
        foreach($array as $key => $value) { 
            if (file_exists(IMG_DIR . "profile_picture/" . $value['photo'])) {
                $array[$key]['photo'] =  SITE_URL. "/uploads/images/profile_picture/". $value['photo'] ;
            } else {
                $array[$key]['photo'] = SITE_URL. "/uploads/images/profile_picture/nophoto.png";
            }
        }
        foreach($array as &$value) {
            $keyed[$value['user_id']] = &$value; 
        }
        unset($value);
        $array = $keyed;
        unset($keyed);
        
        // tree it
        $tree = array();
        foreach($array as $key => &$value) {
            if (isset($value['father_id']) && $parent = $value['father_id'])
                $array[$parent]['children'][] = &$value;
            else {
                $tree[] = &$value;
            }
        }
        unset($value);
        $array = $tree;
        unset($tree);
        $this->tree_model->renderTree_new($user_id, $this->MODULE_STATUS);
        $tooltip_config = $this->validation_model->getTooltipConfig();
        $data = [
            'TreeData' => $array[0]['children'],
            'tooltip_config' => $tooltip_config
        ];
        if($this->MODULE_STATUS['opencart_status'] == 'yes'){
            $data['store_url'] = $this->register_url();
        }
        $this->set_success_response(200, $data);
    }

    public function sponsor_tree_get() {
        $rank_status = "yes";
        $type = "sponsor_tree";
        $user_id;
        if($this->get('user_name')){
            $user_id = $this->validation_model->userNameToID($this->get('user_name'));
        }else if($this->get('user_id')){
            if(!$this->validation_model->isUserExists($this->get('user_id'))){
                $this->set_error_response(422, 1043);
            }
            $user_id = $this->get('user_id');
        }else{
            $user_id =$this->LOG_USER_ID;   
        }
        if(!$user_id) {
            $this->set_error_response(422, 1043);
        }
        $array = $this->tree_model->getTreeDownlines($this->MLM_PLAN, $rank_status, $user_id, $type);
        $keyed = array();
        foreach($array as $key => $value) { 
            if (file_exists(IMG_DIR . "profile_picture/" . $value['photo'])) {
                $array[$key]['photo'] =  SITE_URL. "/uploads/images/profile_picture/". $value['photo'] ;
            } else {
                $array[$key]['photo'] = SITE_URL. "/uploads/images/profile_picture/nophoto.png";
            }
        }
        foreach($array as &$value) {
            $keyed[$value['user_id']] = &$value; 
        }
        unset($value);
        $array = $keyed;
        unset($keyed);
        // tree it
        $tree = array();
        foreach($array as $key => &$value) {
            if (isset($value['sponsor_id']) && $parent = $value['sponsor_id'])
                $array[$parent]['children'][] = &$value;
            else {
                $tree[] = &$value;
            }
        }
        
        unset($value);
        $array = $tree;
        unset($tree);
        $this->tree_model->renderTree_new($user_id, $this->MODULE_STATUS);
        $tooltip_config = $this->validation_model->getTooltipConfig();
        $data = [
            'TreeData' => $array[0]['children'],
            'tooltip_config' => $tooltip_config
        ];
        $this->set_success_response(200, $data);
    }
    

    public function sponsor_tree_get_old()
    {
        $mlm_plan = $this->MODULE_STATUS['mlm_plan'];
        $rank_status = $this->MODULE_STATUS['rank_status'];
        $user_id = $this->get('user_id') ?: $this->LOG_USER_ID;

        if(!$this->Api_model->checkAncestorDescendant($this->LOG_USER_ID, $user_id, 'sponsor_tree')) {
            $this->set_error_response(422, 1043);
        }

        $tree_details = $this->tree_model->getTreeDownlines($mlm_plan, $rank_status, $user_id, 'sponsor_tree');
        $this->set_success_response(200, [
            'tree_details' => $tree_details
        ]);
    }
    public function referral_members_get()
    {
        $this->form_validation->set_data($this->get());
        $rules = [
            [
                'field' => 'offset',
                'label' => lang("offset"),
                'rules' => 'trim|required|numeric|greater_than_equal_to[0]'
            ],
            [
                'field' => 'limit',
                'label' => lang("limit"),
                'rules' => 'trim|required|numeric|greater_than_equal_to[0]|less_than_equal_to[1000]'
            ],
        ];
        $this->form_validation->set_rules($rules);
        if (!$this->form_validation->run()) {
            $this->set_error_response(422, 1004);
        }
        $this->load->model('my_report_model');
        $this->LOG_USER_ID = $this->rest->user_id;
        $this->LOG_USER_TYPE = 'user';
        $user_id = $this->LOG_USER_ID;
        $data = [];
        $total_downline_count = $this->validation_model->getUserReferralCount($user_id);
        $total_levels = $this->my_report_model->getMaxLevelSponsor($user_id);
        
        $level_value = (int) $this->input->get('level') ?: 'all';
        $page = $this->input->get('offset') ?: 0;
        $limit = $this->input->get('limit') ?: 0;
        if ($level_value != 'all') {
            $binary_level = $level_value;
            $level_value = $this->validation_model->getUserTreeLevel($user_id, 'sponsor_tree') + $level_value;
            $level_arr_rs = $this->my_report_model->getTotalDownlineUsersUnilevel($user_id, $level_value);
            $binary = $this->my_report_model->getDownlineDetailsUnilevel($user_id, $limit, $page, $level_value);
        } else {
            $level_arr_rs = $this->my_report_model->getTotalDownlineUsersUnilevel($user_id);
            $binary = $this->my_report_model->getDownlineDetailsUnilevel($user_id, $limit, $page);
            $binary_level = 'all';
        }

        foreach ($binary as $key => $value) {
           if (file_exists(IMG_DIR . "profile_picture/" . $binary[$key]['user_photo'])) {
                $binary[$key]['user_photo'] = SITE_URL. "/uploads/images/profile_picture/" . $binary[$key]['user_photo'];
            } else {
                $binary[$key]['user_photo'] = SITE_URL. "/uploads/images/profile_picture/nophoto.png";
            }
        }
        $data['total_referral_count'] = $level_arr_rs;
        $data['total_levels'] = $total_levels;
        $data['tableData']= $binary;
        $this->set_success_response(200,$data);
        
    }
    public function tree_view_get(){
        $user_id = $this->get('user_name') ? $this->validation_model->userNameToID($this->get('user_name')) : $this->LOG_USER_ID;
        if(!$user_id) {
            $this->set_error_response(422, 1043);
        }
        $this->tree_model->renderTree_new($user_id, $this->MODULE_STATUS);
        $tooltip_array = $this->tree_model->tree_tooltip_array;
        $tooltip_config = $this->validation_model->getTooltipConfig();
        unset($tooltip_array[0]);
        $data = [
            'data' => json_decode($this->tree_model->getChildren((int)$user_id)),
            // 'user_name' => $this->validation_model->IdToUserName($user_id),
            'tooltip_config' =>$tooltip_config,
            'tooltip_array' => array_values($tooltip_array)
        ];
        $this->set_success_response(200,$data);
    }

    public function board_view_get() {
        $board_id = $this->get('board');
        $user_id = $this->get('user_id');
        if(!$board_id || !$user_id) {
            $this->set_error_response(422, 1043);
        }
        if(!$this->tree_model->boardExists($board_id)) {
            $this->set_error_response(422, 1043);
        }

        if(!$this->validation_model->isUserAvailableinBoard($user_id, $board_id)) {
            $this->set_error_response(422, 1043);
        }

        $board_config = $this->configuration_model->getBoardSettings($board_id);
        $array = $this->tree_model->getBoardDownlines($board_id, $user_id);
        $keyed = array();
        foreach($array as $key => $value) { 
            if (file_exists(IMG_DIR . "profile_picture/" . $value['photo'])) {
                $array[$key]['photo'] =  SITE_URL. "/uploads/images/profile_picture/". $value['photo'] ;
            } else {
                $array[$key]['photo'] = SITE_URL. "/uploads/images/profile_picture/nophoto.png";
            }
        }    
        foreach($array as &$value) {
            $keyed[$value['user_id']] = &$value; 
        }
        unset($value);
        $array = $keyed;
        unset($keyed);
        
        // tree it
        $tree = array();
        foreach($array as $key => &$value) {
            if (isset($value['father_id']) && $parent = $value['father_id'])
                $array[$parent]['children'][] = &$value;
            else {
                $tree[] = &$value;
            }
        }
        unset($value);
        $array = $tree;
        unset($tree);
        $tooltip_config = $this->validation_model->getTooltipConfig();
        $data = [
            'TreeData' => $array[0]['children'],
            'tooltip_config' => $tooltip_config,
            'board_width' => (int)$board_config[0]['board_width'],
            'board_depth' => (int)$board_config[0]['board_depth'],
            'table_status'  => $this->MODULE_STATUS['table_status']
        ];
        $this->set_success_response(200, $data);
    }
    function register_url(){
        $userId = $this->rest->user_id;
        $token = $this->Api_model->get_store_url($userId);
        if(!$token){
            $token = $this->Api_model->create_oc_token($this->LOG_USER_ID,$this->rest->key);
        }else{
            $token = $token['token'];
        }
        $table_prefix = str_replace("_", "", $this->db->dbprefix);
        $url = SITE_URL . "/store/index.php?route=register/mlm&key=$token&id=$table_prefix";
        return $url;   
    }

    public function step_view_get() {
        $steps = $this->tree_model->getAllStepUsersAPI($this->LOG_USER_ID);
        $this->set_success_response(200, [
            'users'      => $steps,
        ]);
    }   

}

?>
