<?php

class Migration_Custome_signup extends CI_Migration
{

    public function up()
    {
        $dbPrefix = $this->db->dbprefix;

        $query = [];
        
        $query[] = "CREATE TABLE `{$dbPrefix}custom_fields` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `key` VARCHAR(50) NOT NULL ,  `field_name` VARCHAR(50) NOT NULL ,  `lang` VARCHAR(50) NOT NULL ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB;";


        $query[] = "ALTER TABLE `{$dbPrefix}signup_fields`  ADD `delete_status` VARCHAR(50) NOT NULL DEFAULT 'yes'  AFTER `sort_order`;";

        $query[] = "CREATE TABLE `{$dbPrefix}custom_field_details` ( `id` INT(11) NOT NULL AUTO_INCREMENT ,  `user_id` INT(11) NOT NULL ,  `field_name` VARCHAR(50) NOT NULL ,  `field_value` VARCHAR(50) NULL DEFAULT NULL ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB;";


        foreach ($query as $qry) {
                $this->db->query($qry);
        }

        
    }

    public function down()
    {
        $dbPrefix = $this->db->dbprefix;
        $this->db->query("DROP TABLE `{$dbPrefix}custom_fields`");

        $this->db->query("ALTER TABLE `{$dbPrefix}signup_fields` DROP `delete_status`;");

        $this->db->query("DROP TABLE `{$dbPrefix}custom_field_details`;");
    }
}
