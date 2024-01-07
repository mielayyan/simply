<?php

class Migration_Create_user_menus_new_ui extends CI_Migration
{

    public function up()
    {        
        $dbPrefix = $this->db->dbprefix;

        // Ewallet
        $this->db->insert('infinite_urls', [
            'id' => 297,
            'link' => 'user/ewallet',
            'sub_menu_ref_id' => 0,
            'status' => "yes",
        ]);

        $this->db->insert('infinite_mlm_menu', [
            'id' => 71,
            'link_ref_id' => 297,
            'icon' => 'fa fa-briefcase',
            'status' => "yes",
            'perm_admin' => 0,
            'perm_dist' => 1,
            'perm_emp' => 0,
            'main_order_id' => 9,
        ]);
        // ./ Ewallet

        // Epin
        $this->db->insert('infinite_urls', [
            'id' => 298,
            'link' => 'user/epin',
            'sub_menu_ref_id' => 0,
            'status' => "yes",
        ]);
        $this->db->insert('infinite_mlm_menu', [
            'id' => 72,
            'link_ref_id' => 298,
            'icon' => 'fa fa-bookmark-o',
            'status' => "yes",
            'perm_admin' => 0,
            'perm_dist' => 1,
            'perm_emp' => 0,
            'main_order_id' => 10,
        ]);
        // ./Epin

        // Payout
        $this->db->insert('infinite_urls', [
            'id' => 299,
            'link' => 'user/payout',
            'sub_menu_ref_id' => 0,
            'status' => "yes",
        ]);


        $this->db->insert('infinite_mlm_menu', [
            'id' => 73,
            'link_ref_id' => 299,
            'icon' => 'fa fa-money',
            'status' => "yes",
            'perm_admin' => 0,
            'perm_dist' => 1,
            'perm_emp' => 0,
            'main_order_id' => 11,
        ]);
        // ./Payout
        
    }

    public function down()
    {
        $dbPrefix = $this->db->dbprefix;
        $this->db->where('id', 297)->delete('infinite_urls');
        $this->db->where('id', 71)->delete('infinite_mlm_menu');
        $this->db->where('id', 298)->delete('infinite_urls');
        $this->db->where('id', 72)->delete('infinite_mlm_menu');
        $this->db->where('id', 299)->delete('infinite_urls');
        $this->db->where('id', 73)->delete('infinite_mlm_menu');
    }
}
