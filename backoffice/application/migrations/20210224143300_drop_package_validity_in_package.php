<?php

class Migration_drop_package_validity_in_package extends CI_Migration {
    public function up() {
        $dbPrefix = $this->db->dbprefix;
        if ($this->db->table_exists('package')) {
        	$this->db->query("ALTER TABLE `{$dbPrefix}package`
      DROP `package_validity`");
        }
    	
    }

    public function down() {
        $dbPrefix = $this->db->dbprefix;
        if($this->db->table_exists('package')) {
            $this->db->query("ALTER TABLE `{$dbPrefix}package`  ADD `package_validity` INT(11) NOT NULL DEFAULT 0  AFTER `pair_value`");
        }
    }
}
