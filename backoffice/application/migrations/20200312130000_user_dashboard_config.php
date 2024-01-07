<?php

class Migration_user_dashboard_config extends CI_Migration
{

	public function up()
	{
		$dbPrefix = $this->db->dbprefix;
		$query = [];

		$query[] = "CREATE TABLE `{$dbPrefix}user_dashboard_items` (
					  `id` int(11) NOT NULL,
					  `item` varchar(50) NOT NULL,
					  `master_item` varchar(50) NOT NULL DEFAULT '' COMMENT 'only if the item is sub of another item',
					  `status` tinyint(1) NOT NULL DEFAULT '1'
					) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

		$query[] = "ALTER TABLE `{$dbPrefix}user_dashboard_items` ADD PRIMARY KEY (`id`);";

		$query[] = "ALTER TABLE `{$dbPrefix}user_dashboard_items` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;";

		$query[] = "ALTER TABLE `{$dbPrefix}user_dashboard_items` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;";

		$query[] = "INSERT INTO `{$dbPrefix}user_dashboard_items` (`item`, `master_item`) VALUES
					('commission_earned', ''),
					('payout_released', ''),
					('payout_pending', ''),
					('total_sales', ''),
					('ewallet', ''),
					('member_joinings', ''),
					('summary_or_promotions', ''),
					('members_map', ''),
					('rank_details', ''),
					('earnings_nd_expenses', ''),
					('earnings', 'earnings_nd_expenses'),
					('expenses', 'earnings_nd_expenses'),
					('payout_status', 'earnings_nd_expenses'),
					('team_perfomance', ''),
					('top_earners', 'team_perfomance'),
					('top_recruiters', 'team_perfomance'),
					('package_overview', 'team_perfomance'),
					('rank_overview', 'team_perfomance'),
					('latest_news', '');";

		foreach ($query as $qry) {
			$this->db->query($qry);
		}
		$this->db->set_dbprefix('');
		if($this->db->table_exists('infinite_mlm_demo_tables')) {
			if(!$this->db->where("table_name", "user_dashboard_items")->count_all_results("infinite_mlm_demo_tables")) {
				$this->db->insert("infinite_mlm_demo_tables", [
					"table_name" => "user_dashboard_items",
					"category" => "common",
					"insert_data" => "yes"
				]);
			}
		}
		$this->db->set_dbprefix($dbPrefix);
	}

	public function down()
	{
		$dbPrefix = $this->db->dbprefix;
		$this->db->query("DROP TABLE IF EXISTS `{$dbPrefix}user_dashboard_items`;");
		$this->db->set_dbprefix('');
		$this->db->where("table_name", "user_dashboard_items")->delete("infinite_mlm_demo_tables");
		$this->db->set_dbprefix($dbPrefix);
	}
}
