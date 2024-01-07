<?php

class Migration_user_dashboard_items extends CI_Migration
{
    public function up()
    {
        $dbPrefix = $this->db->dbprefix;
        $query = [];
        
        if ($this->db->table_exists("user_dashboard_items")) {
            $query[] = "INSERT INTO `{$dbPrefix}user_dashboard_items` (`id`, `item`, `master_item`, `status`) VALUES (NULL, 'profile', '', '1');";
            $query[] = "INSERT INTO `{$dbPrefix}user_dashboard_items` (`id`, `item`, `master_item`, `status`) VALUES (NULL, 'pv', '', '1');";
            $query[] = "INSERT INTO `{$dbPrefix}user_dashboard_items` (`id`, `item`, `master_item`, `status`) VALUES (NULL, 'new_members', '', '1');";
        }

        foreach ($query as $qry) {
            $this->db->query($qry);
        }
    }

    public function down()
    {
        $dbPrefix = $this->db->dbprefix;
        $query = [];

        if ($this->db->table_exists("user_dashboard_items")) {
            $query[] = "DELETE FROM `{$dbPrefix}user_dashboard_items` WHERE `item` = 'new_members'";
            $query[] = "DELETE FROM `{$dbPrefix}user_dashboard_items` WHERE `item` = 'pv'";
            $query[] = "DELETE FROM `{$dbPrefix}user_dashboard_items` WHERE `item` = 'profile'";
        }

        foreach ($query as $qry) {
            $this->db->query($qry);
        }
        
    }
}