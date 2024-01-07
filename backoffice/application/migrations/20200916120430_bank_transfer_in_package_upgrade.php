<?php

class Migration_bank_transfer_in_package_upgrade extends CI_Migration {
	public function up() {
		$dbPrefix = $this->db->dbprefix;
		$query = [];
		if($this->db->table_exists("package_upgrade_history")) {
			$query[] = "ALTER TABLE `{$dbPrefix}package_upgrade_history`  ADD `status` VARCHAR(10) NULL  AFTER `done_by`,  ADD `payment_receipt` VARCHAR(255) NULL  AFTER `status`;";
		}

		foreach ($query as $qry) {
			$this->db->query($qry);
		}
	}

	public function down() {
		$dbPrefix = $this->db->dbprefix;
		if($this->db->table_exists("package_upgrade_history")) {
			$this->db->query("ALTER TABLE `{$dbPrefix}package_upgrade_history` DROP `status`, DROP `payment_receipt`;");
		}
	}
}
