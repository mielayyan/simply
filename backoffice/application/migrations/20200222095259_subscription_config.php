<?php

class Migration_Subscription_config extends CI_Migration
{

	public function up(){

        $dbPrefix = $this->db->dbprefix;
		$query = [];
        
		$query[] = "ALTER TABLE `{$dbPrefix}module_status` ADD `subscription_status` VARCHAR(30) NOT NULL DEFAULT 'no' AFTER `promotion_status_demo`;";

		$query[] = "CREATE TABLE `{$dbPrefix}subscription_config` ( `based_on` VARCHAR(30) NOT NULL ,  `reg_status` VARCHAR(30) NOT NULL DEFAULT 'yes' ,  `commission_status` VARCHAR(30) NOT NULL DEFAULT 'yes' ,  `payout_status` VARCHAR(30) NOT NULL DEFAULT 'yes', `fixed_amount` INT(11) DEFAULT 0, `subscription_period` INT(11) DEFAULT 0) ENGINE = InnoDB;";
		
		
		$query[] = "ALTER TABLE `{$dbPrefix}package_validity_extend_history`  ADD `renewal_details` TEXT NOT NULL ,ADD `renewal_status` TEXT NOT NULL , ADD `receipt` TEXT NOT NULL ;";  


		if($this->db->table_exists("{$dbPrefix}package")){
		$query[] = "ALTER TABLE `{$dbPrefix}package` ADD `subscription_period` int(11) DEFAULT 1"; 
	    }
        
        if($this->db->table_exists("{$dbPrefix}oc_product")){
		$query[] = "ALTER TABLE `{$dbPrefix}oc_product` ADD `subscription_period` int(11) DEFAULT 1"; 
	    }

		$query[] = "INSERT INTO `{$dbPrefix}subscription_config` (`based_on`) VALUES ('member_package');";

		foreach ($query as $qry) {
				$this->db->query($qry);
		}
	}

	public function down(){

		$dbPrefix = $this->db->dbprefix;
        
        $this->db->query("ALTER TABLE `{$dbPrefix}module_status` DROP `subscription_status`;");
        $this->db->query("DROP TABLE IF EXISTS `{$dbPrefix}subscription_config`;");
        
        $this->db->query("ALTER TABLE `{$dbPrefix}package_validity_extend_history` DROP `renewal_details`, DROP `renewal_status`, DROP `receipt`;");
        
        if($this->db->table_exists("{$dbPrefix}package")){
        	$this->db->query("ALTER TABLE `{$dbPrefix}package` DROP `subscription_period`;");
        }
        if($this->db->table_exists("{$dbPrefix}oc_product")){
        	$this->db->query("ALTER TABLE `{$dbPrefix}oc_product` DROP `subscription_period`;");
        }

	}
}
