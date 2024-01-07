<?php

class Migration_Add_subscription_status_demo_column_in_module_status extends CI_Migration {
    public function up() {
        $db_prefix = $this->db->dbprefix;
        $this->db->query("ALTER TABLE `{$db_prefix}module_status` ADD `subscription_status_demo` VARCHAR(3) NOT NULL DEFAULT 'no' AFTER `subscription_status`");
    }

    public function down() {
        $db_prefix = $this->db->dbprefix;
        $this->db->query("ALTER TABLE `{$db_prefix}module_status` DROP `subscription_status_demo`");
    }
}
