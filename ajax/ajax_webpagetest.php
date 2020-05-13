<?php

$url = $_POST['url'];

require_once '../inc/log_manager.php';
require_once '../api/api_webpagetest.php';

$lm = new LogManager();
$lm->open('ajax');
$lm->writeLog($url.' // ajax_WPT begin');

// 
// 

$wpt_client = new WebPageTestClient();
$response_wpt = $wpt_client->requestWPT($url);
echo $response_wpt;

// 
// 

$lm->writeLog($url.' // ajax_WPT end');
$lm->close();

?>