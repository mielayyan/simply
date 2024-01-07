<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_URL, 'https://mlmdemo.simply37.com/backoffice/admin/cron/binary_pending_pair');
$store = curl_exec($ch);
curl_close($ch);
?>