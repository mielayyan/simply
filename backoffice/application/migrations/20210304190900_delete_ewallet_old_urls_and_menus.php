
<?php

class Migration_delete_ewallet_old_urls_and_menus extends CI_Migration {
	public function up() {
        $this->db->where_in('id', [63, 276, 35])->delete('infinite_urls');
        $this->db->where('id', 7)->delete('infinite_mlm_menu');
    }

    public function down()
    {
        
        $this->db->insert_batch('infinite_urls', [
            [
                'id' => 63,
                'link' => 'ewallet/my_ewallet',
                'status' => 'yes',
                'target' => 'none',
                'sub_menu_ref_id' => 0
            ],[
                'id' => 63,
                'link' => 'income_details/income',
                'status' => 'yes',
                'target' => 'none',
                'sub_menu_ref_id' => 0
            ],[
                'id' => 276,
                'link' => 'ewallet/purchase_wallet',
                'status' => 'yes',
                'target' => 'none',
                'sub_menu_ref_id' => 0
            ]
        ]);

        $this->db->insert('infinite_mlm_menu', [
            'id' => 7,
            'link_ref_id' => 63,
            'icon' => 'fa fa-money',
            'status' => 'yes',
            'perm_admin' => 0,
            'perm_dist' => 0,
            'perm_emp' => 0,
            'main_order_id' => 11
           ]
        );
    }

}