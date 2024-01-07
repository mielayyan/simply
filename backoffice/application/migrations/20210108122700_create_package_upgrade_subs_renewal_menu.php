<?php

class Migration_Create_package_upgrade_subs_renewal_menu extends CI_Migration {
    public function up() {        
        // Remove submenu in user side
        $this->db->set('perm_dist', 0)
            ->where_in('sub_id', [93, 119]) //93 -> subscription renewal // 119 package upgrade
            ->update('infinite_mlm_sub_menu');

        $this->db->set('main_order_id', 16)
            ->where_in('id', [61, 46])
            ->update('infinite_mlm_menu');
        // Create URL and Menu in User side
        
        // Package Upgrade

        $this->db->insert('infinite_mlm_menu', [
            'id' => 71,
            'link_ref_id' => 196,
            'icon' => 'fa fa-level-up',
            'status' => "yes",
            'perm_admin' => 0,
            'perm_dist' => 0,
            'perm_emp' => 0,
            'main_order_id' => 14,
        ]);

        // Subscription Renewal
        $this->db->insert('infinite_mlm_menu', [
            'id' => 72,
            'link_ref_id' => 163,
            'icon' => 'fa fa-clock-o',
            'status' => "yes",
            'perm_admin' => 0,
            'perm_dist' => 0,
            'perm_emp' => 0,
            'main_order_id' => 15,
        ]);
    }

    public function down() {
        // ADD submenu in user side
        $this->db->set('perm_dist', 1)
            ->where_in('sub_id', [93, 119]) //93 -> subscription renewal // 119 package upgrade
            ->update('infinite_mlm_sub_menu');

        $this->db->set('main_order_id', 15)
            ->where_in('id', [61, 46])
            ->update('infinite_mlm_menu');

        $this->db->where('id', 71)->delete('infinite_mlm_menu');
        $this->db->where('id', 72)->delete('infinite_mlm_menu');
    }
}
