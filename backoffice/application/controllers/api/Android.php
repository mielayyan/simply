<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of android
 *
 * @author ioss
 */
class Android extends CI_Controller{

    function __construct() {
        parent::__construct();
        $this->load->model('android/android_model');            
    }
    
    public function checkValid(){       
        if($this->input->post('tag') == FALSE){          
            exit('error');           
        }
    }
    
    public function login(){
        $adminName = $this->input->post('adminName');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $password = password_hash($password, PASSWORD_DEFAULT);
            
        $user = $this->android_model->getUserByUsernameAndPassword($adminName, $username,  $password);
        if($user){
            $this->android_model->updateActivity($user['id'],$username,'Login');
            $response['success'] = 1;
            $response['logindetail'] = $user;
            echo json_encode($response);
        }else{            
            $response['success'] = 0;
            echo json_encode($response);
        }
       		
    }
    public function updateJoin(){
        
        $userid = $this->input->post('userid');
        $prefix = $this->input->post('tprefix');
        
        $data =  $this->android_model->updateJoining($userid,$prefix);
        $response['join'] = $data;                    
        echo json_encode($response);
    }
    
    public function sendMessage() {
        
        $userid = $this->input->post('userid');
        $prefix = $this->input->post('tprefix');
        $sub = $this->input->post('sub');
        $msg = $this->input->post('msg');
              
        $data = $this->android_model->sendMessage($userid,$prefix,$sub,$msg);
        $response['detail'] = $data;
        echo json_encode($response);       
    }
    public function getEwallet(){
        
        $userid = $this->input->post('userid');
        $tprefix = $this->input->post('tprefix');
        if($tprefix == NULL){
            print_r('error');die();
        }
        $res =  $this->android_model->getEwallet($userid,$tprefix);
        if($res != null){                        
            $data = array('amount' => $res);                      
        }else{            
            $data = array('amount' => 0);
        }
        $response['ewallet'] = $data;
        echo json_encode($response);    
    }
    public function getBonus(){
        
        $userid = $this->input->post('userid');
        $tprefix = $this->input->post('tprefix');
        $res = $this->android_model->getBonus($userid, $tprefix);
        $response['details'] = $res;
        echo json_encode($response);                    
    }
    public function getProfile(){
        
        $userid = $this->input->post('userid');
        $tprefix = $this->input->post('tprefix');
        
        $response = array();
        $res = $this->android_model->getUserDetails($userid, $tprefix);
        $response['profile'] = $res;
        echo json_encode($response);
    }
    public function getMailCount(){
        
        $userid = $this->input->post('userid');
        $tprefix = $this->input->post('tprefix');
        
        $response = array();
        $res = $this->android_model->getMailCount($userid, $tprefix);
        $response['mail'] = $res;
        echo json_encode($response);
    }
    public function checkPassword(){
        
        $userid = $this->input->post('userid');
        $tprefix = $this->input->post('tprefix');
        $oldpass = $this->input->post('old');
       
        $response = array();
        $res = $this->android_model->checkPassword($userid,$tprefix,$oldpass);
        $response['detail'] = $res;
        echo json_encode($response);
    }
    public function updateProfile(){
        
        $userid = $this->input->post('userid');
        $tprefix = $this->input->post('tprefix');
        $detail['user_detail_gender'] = $this->input->post('gender');          
        $detail['user_detail_address'] = $this->input->post('address');        
        $detail['user_detail_acnumber'] = $this->input->post('bankac');          
        $detail['user_detail_nbank'] = $this->input->post('bankname');      
        $detail['user_detail_nbranch'] = $this->input->post('branchname');  
        $detail['user_detail_country'] = $this->input->post('country');        
        $detail['user_detail_state'] = $this->input->post('state');            
        $detail['user_detail_pin'] = $this->input->post('pin');                
        $detail['user_detail_mobile'] = $this->input->post('mobile');          
        $detail['user_detail_land'] = $this->input->post('land');              
        $detail['user_detail_email'] = $this->input->post('email');            
        $detail['user_detail_pan'] = $this->input->post('pan');                
        $detail['user_detail_ifsc'] = $this->input->post('ifsc');              
        $detail['user_detail_facebook'] = $this->input->post('face');              
        $detail['user_detail_twitter'] = $this->input->post('twitter'); 
        $detail['user_photo'] = $this->input->post('photo');
        
        $response['detail'] = $this->android_model->updateProfile($userid,$tprefix,$detail);
        echo json_encode($response);                        
    }
    public function changePass(){
        
        $userid = $this->input->post('userid');
        $tprefix = $this->input->post('tprefix');
        $newpass = $this->input->post('newpass');
        $res = $this->android_model->changePassword($userid,$tprefix,$newpass);
        $response['detail'] = $res;
        echo json_encode($response);
    }
    public function updateMailStatus(){
        
        $mailid = $this->input->post('mailid');
        $tprefix = $this->input->post('tprefix');
        
        $response['detail']  = $this->android_model->updateMailStatus($tprefix,$mailid);
        echo json_encode($response);
    }
    public function getCountry(){
        
        $res = $this->android_model->getCountry();
        $response['detail'] = $res;
        echo json_encode($response);
    }
    public function getState(){
        
        $res = $this->android_model->getState();
        $response['detail'] = $res;
        echo json_encode($response);
    }
    public function getRefferalDetail(){
        
        $userid = $this->input->post('userid');
        $tprefix = $this->input->post('tprefix');
        $action = $this->input->post('action');
        $startid = $this->input->post('startid');
        $limit = $this->input->post('limit');
        
        $maxid = $this->android_model->getStartId($tprefix,$userid);
        $minid = $this->android_model->getLastid($tprefix,$userid);
        
        if($action == 'next'){
            if($startid == -1){
                $startid =$maxid+1;
            }  
            $res = $this->android_model->getRefferalDetailNext($userid, $tprefix, $startid,$limit);                        
        }else if($action == 'previous'){             
            $res = $this->android_model->getRefferalDetailPrevious($userid, $tprefix,$startid, $limit);              
        }
        
        $response['maxid'] = $maxid;
        $response['minid'] = $minid;
        $response['detail'] = $res;
        echo json_encode($response);
    }
    public function getMessageDetail(){
        
        $userid = $this->input->post('userid');
        $tprefix = $this->input->post('tprefix');
        $action = $this->input->post('action');
        $startid = $this->input->post('startid');
        $limit = $this->input->post('limit');
        
        $maxid = $this->android_model->getMailStartId($tprefix,$userid);
        $minid = $this->android_model->getMailLastid($tprefix,$userid);
         
        if($action == 'next'){  
            if($startid == -1){
                $startid =$maxid+1;
            }  
            $res = $this->android_model->getMailDetailNext($userid, $tprefix, $startid,$limit);        
        }else if($action  == 'previous'){
            $res = $this->android_model->getMailDetailPrevious($userid, $tprefix,$startid, $limit);
        }
        $response['maxid'] = $maxid;
        $response['minid'] = $minid;
        $response['detail'] = $res;
        echo json_encode($response);
    }
    public function updateRefferal(){
        
        $userid = $this->input->post('userid');
        $tprefix = $this->input->post('tprefix');
        $action = $this->input->post('action');
        $startid = $this->input->post('startid');
        $limit = $this->input->post('limit');
                
        $maxid = $this->android_model->getStartId($tprefix,$userid);
        $minid = $this->android_model->getLastid($tprefix,$userid);
        if($action == 'next'){        
            if($startid == -1){                
                $startid =$maxid+1;            
            }            
            $res = $this->android_model->getRefferalNameNext($userid, $tprefix, $startid,$limit);            
        }else if($action  == 'previous'){        
            $res = $this->android_model->getRefferalNamePrevious($userid, $tprefix,$startid, $limit);
        }
        $response['maxid'] = $maxid;
        $response['minid'] = $minid;
        $response['detail'] = $res;
        echo json_encode($response);        
    }
    public function register(){
        
        if($this->input->post('tag') == 'validateRegister'){
            $response =Array();            
            $username = $this->input->post('username');
            $tprefix = $this->input->post('tprefix');
            $leg = $this->input->post('leg');
            
            $response['validUser'] = $this->android_model->checkValidSponser($username,$tprefix);
            
            if($response['validUser']){
                $response['userfullname'] = $this->android_model->getRefferalName($username,$tprefix);
                $sponserid = $this->android_model->userNameToID($username,$tprefix);
                $placementid = $this->android_model->getPlacement($sponserid, $leg,$tprefix);
                $response['placementuser'] = $this->android_model->IdToUserName($placementid,$tprefix);
                $response['placementfull'] = $this->android_model->idToFullName($placementid,$tprefix);
                $product_status = $this->android_model->getProductStatus($tprefix);
                if($product_status == 'yes'){
                    $productAdded = $this->android_model->isProductAdded($tprefix);
                    
                    if($productAdded == 'yes'){ 
                        $response['product_status'] = 'yes';
                        $response['product'] = $this->android_model->getProducts($tprefix);   
                    }else{
                        $response['product_status'] = 'No Product Added';
                    }
                }else{
                    $response['product_status'] = 'No Product';
                }
                echo json_encode($response);
                
            }else{
                echo json_encode($response);
                exit();
            }
        }else if($this->input->post('tag') == 'getUserNameConfig'){
            $tprefix = $this->input->post('tprefix');            
            $response['username'] = $this->android_model->getUserNameConfig($tprefix);
            echo json_encode($response);
        }else if($this->input->post('tag') == 'getLicense'){
            $tprefix = $this->input->post('tprefix');
            $response['license'] = $this->android_model->getLicesnse($tprefix);
            echo json_encode($response);
        }else if($this->input->post('tag') == 'registerUser'){           
            $tprefix = $this->input->post('tprefix');
            $pay_type = $this->input->post('paytype');
            $details = array();
            $pin_detail = array();
            $details['address'] = $this->input->post('address');
            $details['banckacno'] = $this->input->post('banckacno');
            $details['bankname'] = $this->input->post('bankname');
            $details['branchname'] = $this->input->post('branchname');
            $details['country'] = $this->input->post('country');
            $details['dob'] = $this->input->post('dob');
            $details['email'] = $this->input->post('email');
            $details['gender'] = $this->input->post('gender');
            $details['ifsc'] = $this->input->post('ifsc');
            $details['landphone'] = $this->input->post('landphone');
            $details['mobile'] = $this->input->post('mobile');
            $details['name'] = $this->input->post('name');
            $details['pan'] = $this->input->post('pan');
            $details['password'] = $this->input->post('password');
            $details['pin'] = $this->input->post('pin');
            $details['placementfullname'] = $this->input->post('placementfullname');
            $details['placementusername'] = $this->input->post('placementusername');
            $details['position'] = $this->input->post('position');
            $details['product'] = $this->input->post('product');
            $details['productid'] = $this->input->post('productid');
            $details['productvalue'] = $this->input->post('productvalue');
            $details['sponserfullname'] = $this->input->post('sponserfullname');
            $details['sponserusername'] = $this->input->post('sponserusername');
            $details['state'] = $this->input->post('state');
            $details['username'] = $this->input->post('username');
            $details['loginuser'] = $this->input->post('loginUser');
            if($pay_type == 'E-Pin'){
                $length = intval($this->input->post('epinCount'));
                for ($i = 0; $i < $length; $i++){
                    $pin_detail['pin'.$i] = $this->input->post('epin'.$i);
                    $pin_detail['amount'.$i] = $this->input->post('epinBalance'.$i);
                }
            }
            $msg  = $this->android_model->confirmRegister($tprefix,$details,$pay_type);
            if(!($details['product'] == 'No Product')){
                 $this->android_model->insertIntoSalesOrder($tprefix,$details['username'], $details['productid'], $pay_type);  
            }
            if($pay_type == 'Paypal'){
                $paypal_details['payer_id']='android_'.$details['username'];
                $paypal_details['order_id'] = $this->input->post('orderid');
               
                $this->android_model->updatepaymentDetails($tprefix,$paypal_details);
            }
            if($msg['status'] == 'true' && $pay_type == 'E-Pin' ){
                $this->android_model->insertUsedPin($tprefix,$pin_detail,$length,$details['username']);
                $this->android_model->updateUsedEpin($tprefix,$pin_detail, $length,$details['username']);
            }
            echo json_encode($msg);   
        }else if($this->input->post('tag') == 'payways'){
            $tprefix = $this->input->post('tprefix');
            $payways = $this->android_model->getPayways($tprefix);
            echo json_encode($payways);
        }else if($this->input->post('tag') == 'regamount'){
            $tprefix = $this->input->post('tprefix');
            $amount['amount'] = $this->android_model->getRegisterAmount($tprefix);
            echo json_encode($amount);
        }else if($this->input->post('tag') == 'getepin'){
            $tprefix = $this->input->post('tprefix');
            $epin = $this->input->post('epin');
            $epinArray = $this->android_model-> getEpin($tprefix,$epin);
            echo json_encode($epinArray);
        }
       
    }
    public function validate(){
        
        $tprefix = $this->input->post('tprefix');
        $username = $this->input->post('username');
        $result['result'] = $this->android_model->validate($tprefix,$username);
        echo json_encode($result);
    }
    public function checkUpdate(){
        
        $version = $this->input->post('version');
        $result['status'] = $this->android_model->checkUpdate($version);
        echo json_encode($result);
    }
    
    public function payment(){
        
        $paypal_details = array();
        $tprefix = $this->input->post('tprefix');
        $paypal_details['type']='paypal';
        $paypal_details['user_id'] = $this->input->post('loginUser');
        $paypal_details['acceptance'] = '';
        $paypal_details['payer_id']='android';
        $paypal_details['order_id'] = $this->input->post('orderid');
        $paypal_details['amount'] = $this->input->post('totalamount');
        $paypal_details['currency'] = $this->input->post('currency');
        $paypal_details['status']='';
        $paypal_details['card_number']='';
        $paypal_details['ED']='';
        $paypal_details['card_holder_name']='';
        $paypal_details['date_of_submission']=  date('Y-m-d H:i:s');
        $paypal_details['pay_id']='';
        $paypal_details['error_status']='';
        $paypal_details['brand']='';
        
        $this->android_model->insertpaymentDetails($tprefix,$paypal_details);
        $data['status'] = 'success';
        echo json_encode($data);
    }
    public function logout(){
        
        $userid = $this->input->post('userid');
        $tprefix = $this->input->post('tprefix');
        if($tprefix == ''){
            die();
        }
        $result['data'] = $this->android_model->updateActivity($tprefix,$userid,'logout');
        echo json_encode($result);
    }
    public function checkUser(){
        
        $userid = $this->input->post('userid');
        $tprefix = $this->input->post('tprefix');
        if($tprefix == ''){
            die();
        }
        $data['status'] = $this->android_model->checkUser($userid,$tprefix);
        echo json_encode($data);
    }
    
    public function uploadImage(){
        
        $target_path  = './public_html/images/profile_picture/';
        
        $imagename = $this->input->post('imagename');
        $image = $this->input->post('image');
        
        $binary=base64_decode($image);
        header('Content-Type: bitmap; charset=utf-8');
        $file = fopen($target_path.$imagename, 'wb');
        fwrite($file, $binary);
        fclose($file);
        $data['status'] = 'success';
        echo json_encode($data);
    }
    
}
