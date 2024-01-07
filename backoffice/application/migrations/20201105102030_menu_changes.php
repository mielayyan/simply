<?php

class Migration_menu_changes extends CI_Migration
{
    public function up()
    {
        $dbPrefix = $this->db->dbprefix;
        $query = [];
        if ($this->db->table_exists("infinite_mlm_sub_menu")) {
            $this->db->query("DELETE FROM `{$dbPrefix}infinite_mlm_sub_menu` WHERE `sub_id` IN (149, 38)");
        }

        if ($this->db->table_exists("infinite_mlm_menu")) {
            $query[] = "DELETE FROM `{$dbPrefix}infinite_mlm_menu` WHERE `id` IN (18, 51);";
        }

        foreach ($query as $qry) {
            $this->db->query($qry);
        }
    }

    public function down()
    {
        
    }
}
