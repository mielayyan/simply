<?php

class Migration_profile_submenus_removal extends CI_Migration
{
	public function up()
	{
		$this->db->where_in('sub_id', [47, 93, 119, 157, 209, 218])
				->delete('infinite_mlm_sub_menu');
	}

	public function down()
	{
		$this->db->query("
					INSERT INTO `{$this->db->dbprefix}infinite_mlm_sub_menu` (`sub_id`, `sub_link_ref_id`, `icon`, `sub_status`, `sub_refid`, `perm_admin`, `perm_dist`, `perm_emp`, `sub_order_id`) VALUES
					(47, 24, '', 'yes', 19, 1, 1, 1, 4),
					(93, 163, '', 'no', 19, 1, 0, 1, 7),
					(119, 196, '', 'no', 19, 1, 0, 1, 8),
					(157, 233, '', 'yes', 19, 1, 0, 1, 6),
					(209, 67, '', 'yes', 19, 1, 1, 1, 5),
					(218, 12, '', 'yes', 19, 1, 0, 1, 2);
						");
	}
}