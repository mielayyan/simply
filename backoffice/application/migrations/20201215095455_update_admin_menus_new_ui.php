<?php

class Migration_Update_admin_menus_new_ui extends CI_Migration
{

    public function up() {        
        $dbPrefix = $this->db->dbprefix;

        // Business
        $this->db->set('link', 'business')
            ->where('id', 293)
            ->update('infinite_urls');
        
        // Ewallet
        $this->db->set('link', 'ewallet')
            ->where('id', 294)
            ->update('infinite_urls');

        $this->db->set('perm_dist', 1)
            ->where('id', 68)
            ->update('infinite_mlm_menu');

        // ./Ewallet
        
        // Payout
        $this->db->set('link', 'payout')
            ->where('id', 295)
            ->update('infinite_urls');

        $this->db->set('perm_dist', 1)
            ->where('id', 69)
            ->update('infinite_mlm_menu');
        // ./Payout

        // epin
        $this->db->set('link', 'epin')
            ->where('id', 296)
            ->update('infinite_urls');

        $this->db->set('perm_dist', 1)
            ->where('id', 70)
            ->update('infinite_mlm_menu');
        // ./epin


    }

    public function down() {
        $dbPrefix = $this->db->dbprefix;

        // Business
        $this->db->set('link', 'admin/business')->where('id', 293)->update('infinite_urls');

        // Ewallet
        $this->db->set('link', 'admin/ewallet')->where('id', 294)->update('infinite_urls');
        $this->db->set('perm_dist', 0)->where('id', 68)->update('infinite_mlm_menu');
        
        // Payout
        $this->db->set('link', 'admin/payout')->where('id', 295)->update('infinite_urls');
        $this->db->set('perm_dist', 0)->where('id', 69)->update('infinite_mlm_menu');

        // Epin
        $this->db->set('link', 'admin/epin')->where('id', 296)->update('infinite_urls');
        $this->db->set('perm_dist', 0)->where('id', 70)->update('infinite_mlm_menu');
    }
}
