<?php

class Migration_drop_product_validity_in_module_status extends CI_Migration {
    public function up() {
        $dbPrefix = $this->db->dbprefix;
    	$this->db->query("ALTER TABLE `{$dbPrefix}module_status`
  DROP `product_validity`");
    	
    }

    public function down() {
        $dbPrefix = $this->db->dbprefix;
        $this->db->query("ALTER TABLE `{$dbPrefix}module_status`  ADD `product_validity` VARCHAR(10) NOT NULL DEFAULT 'yes'  AFTER `bitcoin_status`");
    }
}
