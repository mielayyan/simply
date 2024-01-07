<?php

class Migration_block_binary_pv extends CI_Migration
{

	public function up()
	{
		$dbPrefix = $this->db->dbprefix;
		$query = [];

		if($this->db->table_exists("{$dbPrefix}binary_bonus_config")) {
			$query[] = "ALTER TABLE `{$dbPrefix}binary_bonus_config`  ADD `block_binary_pv` VARCHAR(10) NOT NULL DEFAULT 'no'  AFTER `locking_period`;";
		}

		foreach ($query as $qry) {
			$this->db->query($qry);
		}
	}

	public function down()
	{
		$dbPrefix = $this->db->dbprefix;

		if($this->db->table_exists("{$dbPrefix}binary_bonus_config")) {
			$this->db->query("ALTER TABLE `{$dbPrefix}binary_bonus_config` DROP `block_binary_pv`;");
		}
	}
}
