<?php

$url = $_POST['url'];

require_once '../inc/log_manager.php';
require_once '../api/api_appydex.php';

$lm = new LogManager();
$lm->open('ajax');
$lm->writeLog($url . ' // ajax_Appydex begin');

// 
// 

$client = new AppYuserClient();
$response = $client->requestAppYuserAPI($url);
echo $response; 

// 
// 

$lm->writeLog($url . ' // ajax_Appydex end');
$lm->close();

?>


