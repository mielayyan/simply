<?php 

if(!function_exists('truncate')) {
	function truncate($string,$length=100,$append="&hellip;") {
	  $string = trim($string);

	  if(strlen($string) > $length) {
	    $string = wordwrap($string, $length);
	    $string = explode("\n", $string, 2);
	    $string = $string[0] . $append;
	  }
	  return $string;
	}
}

if(!function_exists('get_previous_url_query_string')) {
	function get_previous_url_query_string() {
		if(isset($_SERVER['HTTP_REFERER'])) {
			$values = explode('?', $_SERVER['HTTP_REFERER']);
			if(!empty($values[1])) {
				return '?'.$values[1];
			}
		}
		return '';
	}
}
function firstLetter($sentence = "") {
		if(!$sentence) {
			return "";
		}
		$words = explode(" ", $sentence);
		$acronym = "";
		foreach ($words as $w) {
		  $acronym .= $w[0];
		}
		return $acronym;
}

if ( ! function_exists('str_contains')) {
	function str_contains($string, $find) {
		return strpos($string, $find) !== false;
	}
}