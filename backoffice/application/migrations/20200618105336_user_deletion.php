<?php

class Migration_user_deletion extends CI_Migration
{

	public function up()
	{
		$dbPrefix = $this->db->dbprefix;
		$query = [];

		$query[] = "ALTER TABLE `{$dbPrefix}ft_individual` ADD `delete_status` VARCHAR(10) NOT NULL DEFAULT 'active' COMMENT 'deleted | active'";

		foreach ($query as $qry) {
			$this->db->query($qry);
		}
	}

	public function down()
	{
		$dbPrefix = $this->db->dbprefix;
		
		$this->db->query("ALTER TABLE `{$dbPrefix}ft_individual` DROP `delete_status`;");
	}
}
