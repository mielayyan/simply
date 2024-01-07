<?php

class Migration_Disable_user_earnings_menu_userside extends CI_Migration {
    public function up() {
    	$this->db->set('perm_dist', 0)
    	   ->where('id', 7)
    	   ->update('infinite_mlm_menu');
    }

    public function down() {
    	$this->db->set('perm_dist', 1)
           ->where('id', 7)
           ->update('infinite_mlm_menu');
    }
}
