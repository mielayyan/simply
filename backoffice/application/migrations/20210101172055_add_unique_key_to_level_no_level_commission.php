<?php
class Migration_Add_unique_key_to_level_no_level_commission extends CI_Migration
{

    public function up() {        
        $dbPrefix = $this->db->dbprefix;
        $this->db->query("ALTER TABLE `{$dbPrefix}level_commision` ADD UNIQUE(`level_no`)");
    }

    public function down() {
        $dbPrefix = $this->db->dbprefix;
        $this->db->query("DROP INDEX level_no on {$dbPrefix}level_commision");
	}
}
