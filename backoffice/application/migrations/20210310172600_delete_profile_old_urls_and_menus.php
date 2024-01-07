
<?php

class Migration_delete_profile_old_urls_and_menus extends CI_Migration {
	public function up() {
        $this->db->where_in('id', [11, 27, 38, 12])->delete('infinite_urls');
    }

    public function down() {
        
        $this->db->insert_batch('infinite_urls', [
            [
                'id' => 11,
                'link' => 'configuration/my_referal',
                'status' => 'yes',
                'target' => 'none',
                'sub_menu_ref_id' => 0
            ],[
                'id' => 12,
                'link' => 'profile/user_account',
                'status' => 'yes',
                'target' => 'none',
                'sub_menu_ref_id' => 0
            ],[
                'id' => 27,
                'link' => 'epin/epin_management',
                'status' => 'yes',
                'target' => 'none',
                'sub_menu_ref_id' => 0
            ],[
                'id' => 38,
                'link' => 'payout/my_income',
                'status' => 'yes',
                'target' => 'none',
                'sub_menu_ref_id' => 0
            ]

        ]);

    }

}