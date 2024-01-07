<?php

class Migration_upgrade_and_renewal_hidden_menu extends CI_Migration {
    public function up() {
        $upgrade_status = $this->db->select('package_upgrade')->get('module_status')->row('package_upgrade');
        $upgrade_menu_perm_dist = ($upgrade_status == 'yes')?1:0;

        $renewal_status = $this->db->select('product_validity')->get('module_status')->row('product_validity');
        $renewal_menu_perm_dist = ($renewal_status == 'yes')?1:0;


        $this->db->set('perm_dist', $upgrade_menu_perm_dist)->where('id', 71)->update('infinite_mlm_menu');
        $this->db->set('perm_dist', $renewal_menu_perm_dist)->where('id', 72)->update('infinite_mlm_menu');
        
    }

    public function down() {
        return true;
    }
}
