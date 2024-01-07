<?php
# mlm V12.0.5 Released on  11-03-2021#
if (isset($_SERVER['HTTPS'])) {
	$protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
}
else {
	$protocol = 'http';
}
$host = $protocol . "://" . $_SERVER['HTTP_HOST'];
if (isset($_SERVER['DOCUMENT_ROOT'])) {
	$web_root = $_SERVER['DOCUMENT_ROOT'];
	$dir = __DIR__;
	$path = str_replace($web_root, '', $dir);
}
else {
	die('project path not specified..');
}
$site_url = $host . $path;

#START_ADMIN_URL#
$admin_url = $site_url . '/backoffice';
#END_ADMIN_URL#
$user_url = $site_url . '/backoffice';

$db_hostname = 'localhost';
$db_username = 'mlmdemosimply37_mlm_user';
$db_password = 'S*!6U1I8l9{b';
//$db_database = 'demo4inf_majeed_demo';
// $db_database = 'demo4inf_simply_live2';
$db_database = 'mlmdemosimply37_mlm';
$db_prefix = '39_';
$db_ocprefix = '39_oc_';


return [
	'db_hostname' => $db_hostname,
	'db_username' => $db_username,
	'db_password' => $db_password,
	'db_database' => $db_database,
	'db_prefix' => $db_prefix,
	'db_ocprefix' => $db_ocprefix,
	'pagination' => '20',
	'precision' => '2',
	'demo_status' => 'no',
	'system_mode' => 'development',
	'error_page_title' => 'Simply37.com',
    'site_url' => $site_url,
    'base_dir' => __DIR__,
    'SEND_EMAIL' => true,
    'default_language' => [
        'code' => 'en',
        'name' => 'english',
        'oc_lang_id' => '1', // id of langauge in oc_languages table
    ],
    'default_currency' => [
        'value' => 1,
        'code' => 'USD',
        'symbol_left' => '$',
        'symbol_right' => ''
    ],
	'public_vars' => [
		'ADMIN_URL'    => $site_url . '/backoffice',
		'USER_URL'     => $site_url . '/backoffice'
	]
];
