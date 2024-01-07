<?php

class Migration_add_tree_icon_column_oc_product extends CI_Migration {

    public function up()
    {
        $dbPrefix = $this->db->dbprefix;
        if($this->db->table_exists("{$dbPrefix}oc_product")) {
            $this->db->query("ALTER TABLE `{$dbPrefix}oc_product` ADD `tree_icon` TEXT NULL AFTER `subscription_period`;");
            // echo 'tree_icon';
        }
    }
    
    public function down()
    {
        $dbPrefix = $this->db->dbprefix;
        if($this->db->table_exists("{$dbPrefix}oc_product")) {
            $dbPrefix = $this->db->dbprefix;
            $this->db->query("ALTER TABLE `{$dbPrefix}oc_product` DROP `tree_icon`;");
        }
    }

}