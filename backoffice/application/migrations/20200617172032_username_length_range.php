<?php

class Migration_username_length_range extends CI_Migration
{

	public function up()
	{
		$dbPrefix = $this->db->dbprefix;
		$query = [];

		if($this->db->table_exists("{$dbPrefix}username_config")) {
			$query[] = "ALTER TABLE `{$dbPrefix}username_config` CHANGE `length` `length` VARCHAR(30) NOT NULL DEFAULT '17';";
		}

		foreach ($query as $qry) {
			$this->db->query($qry);
		}
	}

	public function down()
	{
		// no need of down
	}
}
