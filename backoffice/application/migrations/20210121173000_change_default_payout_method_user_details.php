<?php
class Migration_change_default_payout_method_user_details extends CI_Migration {
	public function up() {
		$dbPrefix = $this->db->dbprefix;
		$this->db->query("ALTER TABLE `{$dbPrefix}user_details` CHANGE `payout_type` `payout_type` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'Bank Transfer'");
		$this->db->query("UPDATE `{$dbPrefix}user_details` SET `payout_type` = 'Bank Transfer'");
	}

	public function down()
	{
		$dbPrefix = $this->db->dbprefix;
		$this->db->query("ALTER TABLE `{$dbPrefix}user_details` CHANGE `payout_type` `payout_type` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'Bank'");
		$this->db->query("UPDATE `{$dbPrefix}user_details` SET `payout_type` = 'Bank'");
	}
}

