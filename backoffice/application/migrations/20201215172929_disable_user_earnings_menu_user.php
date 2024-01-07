<?php

class Migration_Disable_user_earnings_menu_user extends CI_Migration
{

    public function up() {        
        $dbPrefix = $this->db->dbprefix;
        if ($this->db->table_exists('infinite_mlm_menu')) {
            $this->db->set('perm_dist', 0);
            $this->db->where('id', 7);
            $this->db->update('infinite_mlm_menu');
        }
    }

    public function down() {
        $dbPrefix = $this->db->dbprefix;

        if ($this->db->table_exists('infinite_mlm_menu')) {
            $this->db->set('perm_dist', 1);
            $this->db->where('id', 7);
            $this->db->update('infinite_mlm_menu');
        }
    }
}
