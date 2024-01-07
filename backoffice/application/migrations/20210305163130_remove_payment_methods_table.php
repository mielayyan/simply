<?php

class Migration_remove_payment_methods_table extends CI_Migration
{
	
	public function up()
	{
		$dbPrefix = $this->db->dbprefix;
		$this->db->query("DROP TABLE IF EXISTS `{$dbPrefix}payment_methods`;");
		$this->db->set_dbprefix('');
		if($this->db->table_exists('infinite_mlm_demo_tables') && $dbPrefix == 'inf_') {
			$this->db->where('table_name', 'payment_methods')->delete('infinite_mlm_demo_tables');
		}
		$this->db->set_dbprefix($dbPrefix);
	}

	public function down()
	{
		$dbPrefix = $this->db->dbprefix;
		$this->db->query("DROP TABLE IF EXISTS `{$dbPrefix}payment_methods`;");
		$this->db->query("CREATE TABLE `{$dbPrefix}payment_methods` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `payment_type` varchar(100) CHARACTER SET utf8 NOT NULL,
						  `status` varchar(50) CHARACTER SET utf8 NOT NULL,
						  PRIMARY KEY (`id`),
						  KEY `payment_type` (`payment_type`)
						) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;");

		$payment_methds_rows = $this->db->select('gateway_name as payment_type, status')->get('payment_gateway_config')->result_array();

		$this->db->insert('payment_methods',[
			'payment_type' => "Payment Gateway",
			'status' => "yes"
		]);

		$this->db->insert_batch('payment_methods', $payment_methds_rows);
	}
}