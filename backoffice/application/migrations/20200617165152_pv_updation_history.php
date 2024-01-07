<?php

class Migration_pv_updation_history extends CI_Migration
{

	public function up()
	{
		$dbPrefix = $this->db->dbprefix;
		$query = [];

		$query[] = "CREATE TABLE `{$dbPrefix}pv_history_details` ( `id` INT(100) NOT NULL AUTO_INCREMENT ,  `user_id` INT(11) NOT NULL ,  `from_id` INT(11) NOT NULL ,  `personal_pv` INT(100) NOT NULL ,  `group_pv` INT NOT NULL ,  `pv_obtained_by` TEXT NOT NULL ,  `date` DATETIME NOT NULL ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB;";

		foreach ($query as $qry) {
			$this->db->query($qry);
		}
	}

	public function down()
	{
		$dbPrefix = $this->db->dbprefix;

		$this->db->query("DROP TABLE IF EXISTS `{$dbPrefix}pv_history_details`;");
	}
}
