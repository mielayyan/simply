<?php

class select_report_model extends inf_model {

    public function __construct() {
        parent::__construct();
        $this->load->model('validation_model');
    }

    public function getPayoutType() {
        $payout_release = "";
        $this->db->select("payout_release");
        $this->db->from("configuration");
        $query = $this->db->get();

        foreach ($query->result() as $row) {
            $payout_release = $row->payout_release;
        }
        return $payout_release;
    }

    public function selectUser($letters) {

        $this->db->select('id,user_name');
        $this->db->from("ft_individual");
        $this->db->where('active !=', 'terminated');
        $this->db->like('user_name', $letters, 'after');
        $this->db->order_by('id');
        $this->db->limit(500);
        $query = $this->db->get();
        $user_detail = "";
        foreach ($query->result() as $row) {
            $user_detail .= $row->id . "###" . $row->user_name . "|";
        }
        
        return $user_detail;
    }

    public function selectEpin($letters) {
        $this->db->select('pin_id,pin_numbers');
        $this->db->from("pin_numbers");
        $this->db->where('status !=', 'delete');
        $this->db->like('pin_numbers', $letters, 'after');
        $this->db->order_by('pin_id');
        $this->db->limit(500);
        $query = $this->db->get();
        $pin_details = "";
        foreach ($query->result() as $row) {
            $pin_details .= $row->pin_id . "###" . $row->pin_numbers . "|";
        }
        return $pin_details;
    }

    public function getAllRank() {
        $rank_arr = array();
        $this->db->select('rank_name');
        $this->db->select('rank_id');
        $this->db->from("rank_details");
        $this->db->where('delete_status', 'yes');
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result() as $row) {
            $rank_arr[$i]["rank_name"] = $row->rank_name;
            $rank_arr[$i]["rank_id"] = $row->rank_id;
            $i++;
        }
        return $rank_arr;
    }

    public function getCommissinTypes() {
        $commission_types = array();
        $mlm_plan = $this->MODULE_STATUS['mlm_plan'];
        $binary_types = ['leg', 'repurchase_leg', 'upgrade_leg'];
        $board_types = ['board_commission'];
        $stairstep_types = ['stair_step', 'override_bonus'];
        $donation_types = ['donation', 'purchase_donation'];
        $this->db->select('db_amt_type');
        $this->db->from('amount_type');
        $this->db->where('status', 'yes');
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            if ($mlm_plan != 'Binary' && in_array($row['db_amt_type'], $binary_types)) {
                continue;
            }
            if ($mlm_plan != 'Board' && in_array($row['db_amt_type'], $board_types)) {
                continue;
            }
            if ($mlm_plan != 'Stair_Step' && in_array($row['db_amt_type'], $stairstep_types)) {
                continue;
            }
            if ($mlm_plan != 'Donation' && in_array($row['db_amt_type'], $donation_types)) {
                continue;
            }
            $commission_types["$i"]["db_amt_type"] = $row['db_amt_type'];
            $i++;
        }
        return $commission_types;
    }

    public function getTopEarners($offset="",$limit='') {
        $top_earners = array();
        $this->db->select('SUM(total_amount) as total_amount');
        $this->db->select('user_id');
        $this->db->from('leg_amount as l');
        $this->db->join("ft_individual as f", "f.id=l.user_id", "INNER");
        $this->db->where("f.delete_status", "active");
        $this->db->group_by('user_id');
        $this->db->order_by('total_amount', 'DESC');
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $top_earners["details$i"]["user_name"] = $this->validation_model->IdToUserName($row['user_id']);
            $top_earners["details$i"]["name"] = $this->validation_model->getUserFullName($row['user_id']);
            $top_earners["details$i"]["current_balance"] = round($this->validation_model->getUserBalanceAmount($row['user_id']), $this->PRECISION);
            $top_earners["details$i"]["total_earnings"] = round($row['total_amount'], $this->PRECISION);
            $i++;
        }
        return $top_earners;
    }
    
    public function getTopEarnersCount() {
        $this->db->group_by('user_id');
        return $this->db->count_all_results('leg_amount');
    }
    
        public function selectRankDetails($edit_id) {
        $obj_arr = array();
        $this->db->where('rank_id', $edit_id);

        $query = $this->db->get('rank_details');

        foreach ($query->result_array() as $row) {
            $obj_arr['rank_id'] = $row['rank_id'];
            $obj_arr['rank_name'] = $row['rank_name'];
            $obj_arr['referal_count'] = $row['referal_count'];
            $obj_arr['rank_bonus'] = $row['rank_bonus'];
        }
        return $obj_arr;
    }
    
    public function getNextRank($referal_count) {

        $rank_id = array();
        $i= 0;
        $this->db->select('*');
        $this->db->where('referal_count >', $referal_count);
        $this->db->where('rank_status', 'active'); 
        $this->db->where('delete_status', 'yes');
        $this->db->order_by("referal_count", "ASC");
        $res = $this->db->get('rank_details');
       
        foreach ($res->result() as $row) {
            $rank_id[$i] = $row;
            $i++;
        }
        return $rank_id;   
    }

    public function getAllProducts($type="registration") {
        
        $product_details = array();
        $MODULE_STATUS = $this->trackModule();
        $lang_product = $this->lang->line('select_package');
        $products = '<option value="">' . $lang_product . '</option>';
        if ($MODULE_STATUS['opencart_status'] != "yes" || $MODULE_STATUS['opencart_status_demo'] != "yes") {
            $i = 0;
            $this->db->select('*');
            $this->db->from('package');
            $this->db->where('active!=', "deleted");
            $this->db->where('type_of_package', $type);
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                $products.='<option value="' . $row['product_id'] . '">' . $row['product_name'] . '</option>';
            }
        } else {
            $i = 0;
            $this->db->select('*');
            $this->db->from("oc_product");
            $this->db->where('package_type', $type);
            $query = $this->db->get();
            foreach ($query->result_array() as $row) {
                $products.='<option value="' . $row['product_id'] . '">' . $row['model'] . '</option>';
            }
        }
        return $products;
    }

     public function getPackageName()
 {
        $this->db->select('product_name,product_id');
        $this->db->from('package');
        $this->db->where('type_of_package',"registration");
        $query=$this->db->get();
        return $query->result_array();


 } 

 public function getPackageUpgradeDetails($user_id,$product_id,$offset='',$limit='')
 {
   $this->db->select('ft.user_name, pu.payment_amount, pu.payment_type, pu.date_added, pu.user_id, ft.delete_status');
   $this->db->from('package_upgrade_history AS pu');
   if($this->MODULE_STATUS['opencart_status'] == 'yes') {
      $this->db->select('p.model as new_package, pf.model current_package');
      $this->db->join("oc_product AS p", "pu.new_package_id=p.package_id");
      $this->db->join("oc_product AS pf", "pu.current_package_id=pf.package_id");
   } else {
      $this->db->select('p.product_name as new_package, pf.product_name current_package');
      $this->db->join("package AS p", "pu.new_package_id=p.prod_id");
      $this->db->join("package AS pf", "pu.current_package_id=pf.prod_id");
   }

   $this->db->join("ft_individual AS ft", "pu.user_id=ft.id");
   if($user_id!="")
   {
   $this->db->where('pu.user_id',$user_id);
   }
   if($product_id!="")
   {
   $this->db->where_in('pu.new_package_id',$product_id);
   }
   if($user_id!="" && $product_id!="")
   {
    $this->db->where('pu.user_id',$user_id);
    $this->db->where_in('pu.new_package_id',$product_id);

   }
   if($limit!="" && $offset="")
   {
      $this->db->limit($limit,$offset);
   }
   $array = $this->db->get()->result_array();
   for($i = 0;$i < count($array);$i++) {
       $array[$i]["full_name"] = $this->validation_model->getUserFullName($array[$i]["user_id"]);
      if($array[$i]["payment_type"] == "pp_express") {
          $array[$i]["payment_type"] = "Paypal";
      }
   }
   return $array;

 }

public function getProdIDFromProductName($product_id="") {
        
        $this->db->select("prod_id");
        $this->db->from("package");
        $this->db->where("product_id", $product_id);
        $query = $this->db->get();
        return $query->row_array();
 }

public function getPVHistoryCount($user_id,$from_date="",$to_date=""){

	$this->db->where('user_id', $user_id);
  if($from_date!=""){
    $this->db->where('date>=', $from_date);
  }
if($to_date!=""){
    $this->db->where('date<=', $to_date);
  }
    return $this->db->count_all_results('pv_history_details');
}

public function  getPVHistory($user_id, $page , $limit,$from_date="", $to_date=""){

    $this->db->select('e.personal_pv,e.group_pv,e.date,e.pv_obtained_by,e.date,e.from_id');
    $this->db->from('pv_history_details as e');
    $this->db->join('ft_individual as f', 'e.user_id = f.id', 'left');
    if($from_date!=""){
    $this->db->where('e.date>=', $from_date);
  }
if($to_date!=""){
    $this->db->where('e.date<=', $to_date);
  }
    $this->db->where('e.user_id', $user_id);
    $this->db->limit($limit, $page);
    $this->db->order_by('e.id');
    $res = $this->db->get();

    $details = array();
    $i = 0;
    foreach($res->result_array() as $row){

       $details[$i]['from_user'] = $this->validation_model->IdtoUsername($row['from_id']);
       
       if($row['personal_pv'] != 0) {
        $details[$i]['pv_type'] = "personal_pv";
        $details[$i]['pv_amount'] = $row['personal_pv'];
       }else{
        $details[$i]['pv_type'] = "group_pv";
        $details[$i]['pv_amount'] = $row['group_pv'];
       } 
       $details[$i]['full_name'] = $this->validation_model->getUserFullName($row['from_id']);
       $details[$i]['personalpv'] = $row['personal_pv'];
       $details[$i]['group_pv'] = $row['group_pv'];
       $details[$i]['date'] = $row['date'];
       $details[$i]['pv_obtained_by'] = $row['pv_obtained_by'];
       $i++;
    }
    
    return $details;
}
public function getCountTopEarners(){
        $this->db->from('leg_amount as l');
        $this->db->join("ft_individual as f", "f.id=l.user_id", "INNER");
        $this->db->where("f.delete_status", "active");
        $this->db->group_by('user_id');
        $this->db->order_by('total_amount', 'DESC');
        $query = $this->db->get();
        return $this->db->affected_rows();

}
public function getCountPackageUpgradeDetails($user_id,$product_id){
  
   $this->db->from('package_upgrade_history AS pu');
   $this->db->join("package AS p", "pu.new_package_id=p.prod_id");
   $this->db->join("package AS pf", "pu.current_package_id=pf.prod_id");
   $this->db->join("ft_individual AS ft", "pu.user_id=ft.id");
   if($user_id!="")
   {
   $this->db->where('pu.user_id',$user_id);
   }
   if($product_id!="")
   {
   $this->db->where_in('pu.new_package_id',$product_id);
   }
   if($user_id!="" && $product_id!="")
   {
    $this->db->where('pu.user_id',$user_id);
    $this->db->where_in('pu.new_package_id',$product_id);

   }
   $array = $this->db->get();
   return $this->db->affected_rows();

}


    
}
