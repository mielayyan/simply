<?php

class Migration_menu_submenu_changes extends CI_Migration
{
    public function up()
    {
        $dbPrefix = $this->db->dbprefix;
        $query = [];
        if ($this->db->table_exists("infinite_mlm_sub_menu")) {
             $query[] = "DELETE FROM `{$dbPrefix}infinite_mlm_sub_menu` WHERE `sub_id` = 15";
        }

        if ($this->db->table_exists("ft_individual")) {
            $query[] = "ALTER TABLE `{$dbPrefix}ft_individual` CHANGE `leg_position` `leg_position` INT(11) NOT NULL DEFAULT '0';";
        }
        if ($this->db->table_exists("infinite_mlm_menu")) {
            $query[] = "UPDATE `{$dbPrefix}infinite_mlm_menu` SET `main_order_id` = '19' WHERE `id` = 11;";
            $query[] = "UPDATE `{$dbPrefix}infinite_mlm_menu` SET `main_order_id` = '18' WHERE `id` = 23;";
            $query[] = "UPDATE `{$dbPrefix}infinite_mlm_menu` SET `main_order_id` = '20' WHERE `id` = 10;";
        }

        if ($this->db->table_exists("user_details")) {
            $query[] = "ALTER TABLE `{$dbPrefix}user_details` CHANGE `user_detail_second_name` `user_detail_second_name` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `user_detail_address` `user_detail_address` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `user_detail_address2` `user_detail_address2` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `user_detail_city` `user_detail_city` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `bitcoin_address` `bitcoin_address` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL, CHANGE `user_detail_paypal` `user_detail_paypal` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL, CHANGE `user_detail_blockchain_wallet_id` `user_detail_blockchain_wallet_id` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL, CHANGE `user_detail_bitgo_wallet_id` `user_detail_bitgo_wallet_id` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;";
        }

        foreach ($query as $qry) {
            $this->db->query($qry);
        }
    }

    public function down()
    {
        
    }
}
