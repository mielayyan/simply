<?php

class Migration_Report_menu_user_side extends CI_Migration
{

	public function up()
	{
		$this->db->set('perm_dist', 0)
			->where([
				'sub_id' => 103,
				'sub_link_ref_id' => 183
			])
			->update('infinite_mlm_sub_menu');
	}

	public function down()
	{
		$this->db->set('perm_dist', 1)
			->where([
				'sub_id' => 103,
				'sub_link_ref_id' => 183
			])
			->update('infinite_mlm_sub_menu');
	}
}
