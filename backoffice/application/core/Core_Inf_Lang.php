<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Core_Inf_Lang extends CI_Lang
{
	function __construct()
	{
		parent::__construct();
	}

	public function line($line, $log_errors = TRUE)
	{
		$value = isset($this->language[$line]) ? $this->language[$line] : $line;

		// Because killer robots like unicorns!
		if ($value === FALSE && $log_errors === TRUE)
		{
			log_message('error', 'Could not find the language line "'.$line.'"');
		}

		return $value;
	}
}