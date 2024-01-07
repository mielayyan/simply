<?php

class Migration_user_profile_menu extends CI_Migration
{

	public function up()
	{
		$dbPrefix = $this->db->dbprefix;

		$this->db->query("UPDATE `{$dbPrefix}infinite_mlm_menu` set `perm_dist` = '0' WHERE `id` = '19';");

		$this->db->query("INSERT INTO `{$dbPrefix}infinite_mlm_menu` (`id`, `link_ref_id`, `icon`, `status`, `perm_admin`, `perm_dist`, `perm_emp`, `main_order_id`) VALUES ('73', '22', 'fa fa-address-book-o', 'yes', '0', '1', '0', '13');");
	}

	public function down()
	{
		$dbPrefix = $this->db->dbprefix;
		$this->db->query("UPDATE `{$dbPrefix}infinite_mlm_menu` set `perm_dist` = '1' WHERE `id` = '19';");
		$this->db->query("DELETE FROM `{$dbPrefix}infinite_mlm_menu` WHERE `id` = '73';");
	}
}
