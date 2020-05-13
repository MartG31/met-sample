<?php

$url = $_POST['url'];

require_once '../inc/log_manager.php';
require_once '../inc/functions.php';

$lm = new LogManager();
$lm->open('ajax');
$lm->writeLog($url . ' // ajax_parseURL begin');

// 
// 

echo formatUrl($url);



// 
// 

$lm->writeLog($url . ' // ajax_parseURL end');
$lm->close();


?>