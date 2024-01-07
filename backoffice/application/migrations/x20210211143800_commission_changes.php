<?php
class Migration_commission_changes extends CI_Migration
{

    public function up() {        
        $dbPrefix = $this->db->dbprefix;
        $this->db->query("ALTER TABLE `{$dbPrefix}sales_level_commision` ADD UNIQUE(`level_no`)");
        if($dbPrefix == "14997") {
            $this->db->query("CREATE UNIQUE INDEX unique_level_pck_id ON {$dbPrefix}sales_commissions(level,pck_id)");
        }
        
    }

    public function down() {
        $dbPrefix = $this->db->dbprefix;
        $this->db->query("DROP INDEX level_no on {$dbPrefix}sales_level_commision");	
        if($dbPrefix == "14997") {
            $this->db->query("DROP INDEX unique_level_pck_id on {$dbPrefix}sales_commissions");
        }
	}
}
