<?php

require_once 'Inf_Controller.php';

class Order extends Inf_Controller {

    function __construct() {
        parent::__construct();
        $this->LOG_USER_ID = $this->rest->user_id;  
        if ($this->MODULE_STATUS['mlm_plan'] == 'Hyip' || $this->MODULE_STATUS['mlm_plan'] == 'X-Up') {
            $this->load->model('Unilevel_model', 'plan_model');
        }
        else {
            $this->load->model($this->MODULE_STATUS['mlm_plan'] . '_model', 'plan_model');
        }
    }

    public function index_post()
    {
        $user_id = $this->rest->user_id;
        $post_arr = $this->validation_model->stripTagsPostArray($this->post());
        $this->form_validation->set_data($post_arr);
        
        if($this->validate_order_details()) {

            $total_pv = $post_arr['total_pv'];
            $total_amount = $post_arr['total_amount'];
            $total_quantity = $post_arr['total_quantity'];

            $update_pv = TRUE;
            $upline_id = $this->validation_model->getFatherId($user_id);
            $position = $this->validation_model->getUserPosition($user_id);
            $sponsor_id = $this->validation_model->getSponsorId($user_id);
            if($this->MODULE_STATUS['mlm_plan'] == 'Matrix') {
                $upline_id = $sponsor_id;
            }
            $data = [];
            $data['sponsor_id'] = $sponsor_id;

            $update_pv = $this->plan_model->runCalculation('repurchase', $user_id, 0, $total_pv, $total_amount, 0, $upline_id, 0, $position, $data);
            if ($this->MODULE_STATUS['rank_status'] == "yes") {
                $this->load->model('rank_model');
                $this->rank_model->updateUplineRank($user_id);
            }
            if ($update_pv) {
                $this->set_success_response(204);
            } else {
                $this->set_error_response(500);
            }
        } else {
            $this->set_error_response(422, 1004);
        } 
    }

    public function validate_order_details()
    {
        $this->form_validation->set_rules('total_pv', lang('total_pv'), 'trim|required|greater_than_equal_to[0]|max_length[10]');
        $this->form_validation->set_rules('total_amount', lang('total_amount'), 'trim|required|greater_than_equal_to[0]|max_length[10]');
        $this->form_validation->set_rules('total_quantity', lang('total_quantity'), 'trim|required|greater_than_equal_to[0]|max_length[5]');        
        return $this->form_validation->run();
    }
    //order history
    public function order_history_get(){
        if($this->MODULE_STATUS['opencart_status'] != 'yes'){
            $this->set_error_response(422,1057);
        }
        $this->load->model('order_model');
        $order_details = array();
        $shipping_details = array();
        $check_date='';
        $base_url = base_url() . "order/order/order_history";
        $config['base_url'] = $base_url;
        $config['per_page'] = $this->PAGINATION_PER_PAGE;
        $page = 0;
        if ($this->uri->segment(4) != "") {
            $page = $this->uri->segment(4);
        } else {
            $page = 0;
        }
        $user_id = $this->LOG_USER_ID;
        if(!$user_id){
            $this->set_error_response(422,1002);
        }
        // dd($user_id);
        $customer_id = $this->validation_model->getOcCustomerId($user_id);
        $total_count = $this->order_model->getOrderHistoryCount($customer_id, $check_date);
        $config['total_rows'] = $total_count;
        $this->pagination->initialize($config);
        $order_details = $this->order_model->getOrderDetails($page, $config['per_page'], $customer_id, $check_date);
        $count = count($order_details);
        $data =[
            'count' => $count,
            'order_details' =>$order_details
        ];
        $this->set_success_response(200,$data);
    }

}
