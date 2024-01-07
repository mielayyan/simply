<?php

class Migration_Add_state_in_oc_address extends CI_Migration {
    public function up() {
        if($this->db->table_exists('oc_address')) {
        	$this->db->set('zone_id', 1490)
        	->where('country_id', 99)
        	->update('oc_address');
        }
    }

    public function down() {
    	if($this->db->table_exists('oc_address')) {
        	$this->db->set('zone_id', 0)
        	->where('country_id', 99)
        	->update('oc_address');
        }
    }
}
