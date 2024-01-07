<?php

class Migration_package_upgrade_opencart extends CI_Migration
{

	public function up()
	{
		$dbPrefix = $this->db->dbprefix;
		$query = [];

		$query[] = "CREATE TABLE `{$dbPrefix}package_upgrade_history` (
					  `id` int(11) NOT NULL,
					  `user_id` int(11) UNSIGNED NOT NULL,
					  `current_package_id` varchar(50) NOT NULL,
					  `new_package_id` varchar(50) NOT NULL,
					  `pv_difference` double NOT NULL,
					  `payment_amount` double NOT NULL,
					  `payment_type` varchar(50) NOT NULL,
					  `done_by` varchar(10) NOT NULL,
					  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
					) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
		$query[] = "ALTER TABLE `{$dbPrefix}package_upgrade_history` ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`);";
		$query[] = "ALTER TABLE `{$dbPrefix}package_upgrade_history` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";

		$query[]= "CREATE TABLE `{$dbPrefix}upgrade_sales_order` (
					  `id` int(11) NOT NULL,
					  `user_id` int(11) UNSIGNED NOT NULL,
					  `package_id` varchar(50) NOT NULL,
					  `amount` double NOT NULL,
					  `total_pv` double NOT NULL DEFAULT '0',
					  `payment_method` varchar(50) NOT NULL,
					  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
					) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
		$query[] = "ALTER TABLE `{$dbPrefix}upgrade_sales_order` ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`);";
		$query[] = "ALTER TABLE `{$dbPrefix}upgrade_sales_order` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";

		$query[] = "CREATE TABLE `{$dbPrefix}upgrade_pendings` (
					  `id` int(11) NOT NULL,
					  `upgrade_data` text NOT NULL,
					  `post_data` text NOT NULL,
					  `status` varchar(30) NOT NULL DEFAULT 'pending'
					) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
		$query[] = "ALTER TABLE `{$dbPrefix}upgrade_pendings` ADD PRIMARY KEY (`id`);";
		$query[] = "ALTER TABLE `{$dbPrefix}upgrade_pendings` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";

		foreach ($query as $qry) {
			$this->db->query($qry);
		}
	}

	public function down()
	{
		$dbPrefix = $this->db->dbprefix;

		$this->db->query("DROP TABLE IF EXISTS `{$dbPrefix}package_upgrade_history`;");
		$this->db->query("DROP TABLE IF EXISTS `{$dbPrefix}upgrade_sales_order`;");
		$this->db->query("DROP TABLE IF EXISTS `{$dbPrefix}upgrade_pendings`;");
	}
}
