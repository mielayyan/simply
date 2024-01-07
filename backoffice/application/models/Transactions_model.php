<?php

class transactions_model extends inf_model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('validation_model');
		$this->load->model('configuration_model');
	}

	/*
		`transactions` table
		- flow : 'in'[money flows into the user], 'out'[money flows out of the user], null[not flowing, just recording]
		- bussiness_flow : 'income', 'expense', 'bonus', ..., null[not flowing, just recording]
		- reference : any extradata needed [optional]
		- transaction_id : unique id for grouping the parts of same transfers[payout + payout fee, fund transfer + fund transfer fee]
	*/

	function insertTransaction($amount, $user_id, $transaction_type, $flow, $bussiness_flow, $mode, $reference = null, $transaction_id = null)
	{
		$datetime = date('Y-m-d H:i:s');
		return $this->db->insert(
				'transactions',
				compact('amount', 'user_id', 'transaction_type', 'flow', 'bussiness_flow', 'mode', 'reference', 'transaction_id', 'datetime')
			);
	}

}