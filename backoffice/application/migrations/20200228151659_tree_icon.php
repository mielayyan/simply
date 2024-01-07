<?php

class Migration_Tree_icon extends CI_Migration
{

    public function up()
    {        
        $dbPrefix = $this->db->dbprefix;

        $query = [];
        
        $query[] = "ALTER TABLE `{$dbPrefix}configuration`  ADD `tree_icon_based` VARCHAR(30) NOT NULL DEFAULT 'profile_image'  AFTER `payout_fee_mode`,  ADD `active_tree_icon` VARCHAR(500) NOT NULL DEFAULT 'active.jpg'  AFTER `tree_icon_based`,  ADD `inactive_tree_icon` VARCHAR(500) NOT NULL DEFAULT 'inactive.png'  AFTER `active_tree_icon`,  ADD `defualt_package_tree_icon` VARCHAR(500) NOT NULL DEFAULT 'default_package.png'  AFTER `inactive_tree_icon`,  ADD `defualt_rank_tree_icon` VARCHAR(500) NOT NULL DEFAULT 'default_rank.png'  AFTER `defualt_package_tree_icon`;";


        $query[] = "ALTER TABLE `{$dbPrefix}package`  ADD `tree_icon` TEXT NOT NULL  AFTER `subscription_period`;";


        $query[] = "ALTER TABLE `{$dbPrefix}rank_details`  ADD `tree_icon` TEXT NOT NULL  AFTER `pool_status`;";

        $query[] = "UPDATE `{$dbPrefix}package` SET `tree_icon` = 'package1.png' WHERE `product_id` =1 AND `type_of_package`='registration'";

        $query[] = "UPDATE `{$dbPrefix}package` SET `tree_icon` = 'package2.png' WHERE `product_id` =2 AND `type_of_package`='registration'";

        $query[] = "UPDATE `{$dbPrefix}package` SET `tree_icon` = 'package3.png' WHERE `product_id` =3 AND `type_of_package`='registration'";

        $query[] = "UPDATE `{$dbPrefix}package` SET `tree_icon` = 'package4.png' WHERE `product_id` =4 AND `type_of_package`='registration'";


        $query[] = "UPDATE `{$dbPrefix}rank_details` SET `tree_icon` = 'rank1.png' WHERE `rank_id` = 1";
        $query[] = "UPDATE `{$dbPrefix}rank_details` SET `tree_icon` = 'rank2.png' WHERE `rank_id` = 2";
        $query[] = "UPDATE `{$dbPrefix}rank_details` SET `tree_icon` = 'rank3.png' WHERE `rank_id` = 3";
        $query[] = "UPDATE `{$dbPrefix}rank_details` SET `tree_icon` = 'rank4.png' WHERE `rank_id` = 4";


        foreach ($query as $qry) {
                $this->db->query($qry);
        }

        
    }

    public function down()
    {
        $dbPrefix = $this->db->dbprefix;
        $this->db->query("ALTER TABLE `{$dbPrefix}configuration` DROP `tree_icon_based`, DROP `active_tree_icon`, DROP `inactive_tree_icon`, DROP `defualt_package_tree_icon`, DROP `defualt_rank_tree_icon`;");

        $this->db->query("ALTER TABLE `{$dbPrefix}package` DROP `tree_icon`;");

        $this->db->query("ALTER TABLE `{$dbPrefix}rank_details` DROP `tree_icon`;");
    }
}
