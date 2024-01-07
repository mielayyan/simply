<?php

require_once 'Inf_Controller.php';

class Package extends Inf_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('product_model');
        $this->load->model('repurchase_model');
        $this->load->library('internal_cart', ['user_id' => $this->rest->user_id], 'cart');
    }

    public function index_get($id = null)
    {
        $retVal = $id ? $this->set_success_response(200, ['products' => $this->Api_model->packageCart($id)]) : $this->set_error_response(400);
    }

    public function membership_get() {
        $user_id = $this->rest->user_id;
        $res = $this->Api_model->getPackageDetails();
        $response = ['products' => $res];
        if ($res) {
            $this->set_success_response(200, $response);
        } else {
            $this->set_error_response(401, 1011);
        }
    }
    
    public function repurchase_product_get() {
        if($this->MODULE_STATUS['repurchase_status'] != 'yes' || $this->MODULE_STATUS['product_status'] != 'yes') {
            $this->set_error_response(422,1057);
        }
        $product = $this->repurchase_model->repurchaseProducts();
        foreach ($product as $key => $value) {
            if($value['image']== 'no'){
                $product[$key]['image'] = SITE_URL. "/uploads/images/product_img/cart.jpg";
            }else if(file_exists(IMG_DIR . "product_img/" . $value['image'])){
                $product[$key]['image'] = IMG_DIR . "product_img/" . $value['image'];
            }else{
                $product[$key]['image'] = SITE_URL. "/uploads/images/product_img/cart.jpg";
            }
        }
        $this->set_success_response(200, [
            'products' => $product
        ]);
    }
    
    public function repurchase_product_details_get() {
        $product_id =$this->input->get('product_id');
        $product_details = $this->repurchase_model->repurchaseProductDetails($product_id);
        $cart_details = array(
            'quantity' => 0,
            'rowid' => ""
        );
        foreach ($this->cart->contents() as $item) {
            if ($item['id'] == $product_id) {
                $cart_details['quantity'] = $item['qty'];
                $cart_details['rowid'] = $item['rowid'];
            }
        }
        if($product_details){
            $this->set_success_response(200, [
                'product' => $product_details,
                'cart' => $cart_details
            ]);
        }else{
            $this->set_error_response(422,1051);
        }
    }
    //update cart
    public function updateItem_get(){
        $rowId = $this->input->get('row_id');
        $quantity = $this->input->get('quantity');
        $this->updateCart($rowId,$quantity);
        // if(!$rowId){
        //     $this->set_error_response(422,1051);
        // }else if ($quantity < 1) {
        //     $this->set_error_response(422,1052);
        // }else if (!filter_var($quantity, FILTER_VALIDATE_INT)) {
        //     $this->set_error_response(422,1053);
        // }
        // $data = $this->cart->update(array(
        //     'rowid' => $rowId,
        //     'qty' => $quantity
        // ));
        // $this->cart->update($data);
        // $this->set_success_response(204);
    }
    //add to cart 
    public function add_to_cart_post(){
        $flag = true;
        $product_id = $this->post('product_id');
        $flag = $this->product_model->isProductAvailable($product_id);
        if (!$flag) {
            $this->set_error_response(422,1050);
        }
        $this->form_validation->set_data($this->post());
        $qty = $this->post('product_qty');
        $this->form_validation->set_rules('product_qty', lang('Quantity_Is_required'), 'trim|required|greater_than[0]');
        if (!$this->form_validation->run()) {
            $this->set_error_response(422,1004);
        }   
        if ($this->cart->in_cart($product_id)) {
            $cart = $this->cart->contents();
            $rowid = $quantity = 0;
            foreach ($cart as $item) {
                if ($item['id'] == $product_id) {
                    $rowid = $item['rowid'];
                    $quantity = $item['qty'] + $qty;
                }
            }
            $this->updateCart($rowid, $quantity);
        }
        $product_details = $this->product_model->getPackageInfoByColumns($product_id, ['product_name', 'product_value', 'prod_img']);
        $insert_data = [
            'id' => $product_id,
            'name' => $product_details['product_name'],
            'price' => $product_details['product_value'],
            'prod_img' => $product_details['prod_img'],
            'qty' => $qty
        ];
        $this->cart->insert($insert_data);
        $this->set_success_response(200);
    }


    //update cart
    public function updateCart($rowId,$quantity){
        if(!$rowId){
            $this->set_error_response(422,1051);
        }else if ($quantity < 1) {
            $this->set_error_response(422,1052);
        }else if (!filter_var($quantity, FILTER_VALIDATE_INT)) {
            $this->set_error_response(422,1053);
        }
        $data = $this->cart->update(array(
            'rowid' => $rowId,
            'qty' => $quantity
        ));
        $this->cart->update($data);
        $this->set_success_response(200);
    }

    //get the intername cart items
    public function getCartItems_get(){
        if($this->MODULE_STATUS['repurchase_status'] != 'yes' || $this->MODULE_STATUS['product_status'] != 'yes') {
            $this->set_error_response(422,1057);
        }
        $data = $this->cart->contents();
        $i = 0;
        if($this->IS_MOBILE){
            $da = [];
            foreach ($data as $key => $value) {
                $da[$i]['id']       = $value['id'];
                $da[$i]['name']     = $value['name'];
                $da[$i]['price']    = $value['price'];
                $da[$i]['prod_img'] = $value['prod_img'];
                $da[$i]['qty']      = $value['qty'];
                $da[$i]['rowid']    = $value['rowid'];
                $da[$i]['subtotal'] = $value['subtotal'];
                $i++;
            }
            $data = $da;
        }
        foreach ($data as $key => $value) {
            if($value['prod_img']== 'no'){
                $data[$key]['prod_img'] = SITE_URL. "/uploads/images/product_img/cart.jpg";
            }else if(file_exists(IMG_DIR . "product_img/" . $value['prod_img'])){
                $data[$key]['prod_img'] = IMG_DIR . "product_img/" . $value['prod_img'];
            }else{
                $data[$key]['prod_img'] = SITE_URL. "/uploads/images/product_img/cart.jpg";
            }
        } 
        $this->set_success_response(200,$data);
    }
    //remove the cart items
    public function removeItems_get(){
        $rowId = $this->get('row_id');
        if(!$rowId){
            $this->set_error_response(422,1050);
        }
        if ($rowId === "all") {
            $this->cart->destroy();
        }else {
            $data = array(
                'rowid' => $rowId,
                'qty' => 0
            );
            $this->cart->update($data);
        }
        $this->set_success_response(200);
    }
    //get the user address details 
    public function getUserAddress_get(){
        $user_id = $this->rest->user_id;
        $user_address = $this->repurchase_model->getUserPurchaseAddress($user_id);
        $default_address = $this->repurchase_model->getUserRepurchaseDefualtAddress($user_id);
        if($default_address){
            foreach ($user_address as $key => $value) {
                if($value['id']==$default_address['id']){
                    $user_address[$key]['default'] = true;
                }else{
                    $user_address[$key]['default'] = false;
                }
            }
        }
        $data = [
            'data' => $user_address
        ];
        $this->set_success_response(200,$data);
    }
    //remove purchase address
    public function RemoveAddress_post(){
        $address_id = $this->post('address_id');
        // dd($this->post());
        if($address_id){
            $deleted = $this->repurchase_model->removePurchaseAddress($address_id);
            $this->set_success_response(200,$deleted);    
        }else{
            $this->set_error_response(422,1023);
        }
    }

    //add address
    public function add_checkout_address_post(){
        $this->form_validation->set_data($this->post());
        if($this->validate_checkout_address()){
            $address = $this->post(null, true);
            $address['full_name'] = $address['name'];
            $address['pin_no'] = $address['zip_code'];
            unset($address['name'],$address['zip_code']);
            $address['user_id'] = $this->rest->user_id;
            if ($this->Api_model->insert_repurchase_address($address)) {
                $this->set_success_response(200);
            }else{
                $this->set_success_response(500);
            }
        }else{
            $this->set_error_response(422, 1004);
        }
    }

    public function validate_checkout_address()
    {
        $this->form_validation->set_rules('name', lang('name'), 'trim|required|min_length[3]|max_length[32]|callback__alpha_space');
        $this->form_validation->set_rules('address', lang('address'), 'trim|required|min_length[3]|max_length[32]');
        $this->form_validation->set_rules('zip_code', lang('zip_code'), 'trim|required|is_natural|min_length[3]|max_length[10]'); 
        $this->form_validation->set_rules('city', lang('city'), 'trim|required|min_length[3]|max_length[32]|callback__alpha_city_address');
        $this->form_validation->set_rules('phone', lang('phone_number'), 'trim|required|is_natural|min_length[5]|max_length[10]');

        $validation_status = $this->form_validation->run();
        return $validation_status;
    }
    function _alpha_space($str = '')
    {
        if (!$str) {
            return true;
        }
        $res = (bool)preg_match('/^[A-Z ]*$/i', $str);
        if (!$res) {
            $this->lang->load('register');
            $this->form_validation->set_message('_alpha_space', lang('only_alpha_space'));
        }
        return $res;
    }

    function _alpha_city_address($str_in = '')
    {
        if (!preg_match("/^([a-zA-Z0-9\s\.\,\-])*$/i", $str_in)) {
            $this->lang->load('register');
            $this->form_validation->set_message('_alpha_city_address', lang('city_field_characters'));
            return false;
        } else {
            return true;
        }
    }
    //change the default address
    public function change_default_address_post(){
        $address_id = $this->post('address_id');
        $user_id = $this->rest->user_id;
        if (!$user_id) {
            // dd($user_id);
            $this->set_error_response(422,1002);
        }
        $res=$this->repurchase_model->updateDefualtAddress($user_id, $address_id);
        if($res){
            $this->set_success_response(200,$res);
        }else{
            $this->set_error_response(422);
        }
    }
    public function cart_get() {
        $this->set_success_response(200, ['products' => $this->Api_model->packageCart()]);
    }
}
