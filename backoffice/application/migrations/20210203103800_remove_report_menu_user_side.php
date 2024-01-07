
<?php

class Migration_remove_report_menu_user_side extends CI_Migration
{
	
	public function up()
    {

        $this->db->set('perm_dist', 0)
            ->where_in('sub_id', [35, 36, 79, 87, 103]) //Report Menu Id
            ->update('infinite_mlm_sub_menu');

        $this->db->set('perm_dist', 0)
            ->where('id', 16)
            ->update('infinite_mlm_menu');
    }

    public function down()
    {

        $this->db->set('perm_dist', 1)
            ->where_in('sub_id', [35, 36, 79, 87, 103]) //Report Menu Id
            ->update('infinite_mlm_sub_menu');

        $this->db->set('perm_dist', 1)
            ->where('id', 16)
            ->update('infinite_mlm_menu');
    }

}