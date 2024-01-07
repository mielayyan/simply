<?php

class Migration_subscription_open_cart extends CI_Migration
{

	public function up()
	{
		$dbPrefix = $this->db->dbprefix;
		$query = [];

		if($this->db->table_exists("{$dbPrefix}oc_product")) {
			$query[] = "ALTER TABLE `{$dbPrefix}oc_product`  ADD `subscription_period` INT NOT NULL DEFAULT '0'  AFTER `pair_value`";
		}

		foreach ($query as $qry) {
			$this->db->query($qry);
		}
	}

	public function down()
	{
		$dbPrefix = $this->db->dbprefix;

		if($this->db->table_exists("{$dbPrefix}oc_product")) {
			$this->db->query("ALTER TABLE `{$dbPrefix}oc_product` DROP `subscription_period`;");
		}
	}
}
