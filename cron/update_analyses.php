<?php 

/******************************************************************************************************************************************************/
// CRON_SCRIPT
// 
// - Récupère en base de données l'analyse la plus récente de chaque site
// - Sélectionne les plus vieilles analyses datant de plus de "min_days_old" jours, dans une limite de "nb_max_analyses"
// - Exécute les requêtes d'API Appydex & WebPageTest et récupère les résultats
// - Ajoute en base les nouvelles analyses
// 
/******************************************************************************************************************************************************/

// 
// --- CONFIG --- //

// Ancienneté minimale pour lancer une nouvelle analyse (en jours)
$analyse_min_days_old = 15;

// Nombre maximum d'analyses sur le même traitement (en nb d'analyses)
$nb_max_analyses = 5;

// -------------- //

require_once '../inc/log_manager.php';
require_once '../inc/pdo_connect.php';

require_once '../api/api_appydex.php';
require_once '../api/api_webpagetest.php';

// -------------- //


// INITIALISATION DES LOGS

$lm = new LogManager();
$lm->open('cron');
$lm->writeLog('cron_script begin');



// SELECTION DES SITES A ANALYSER 

$q = $pdo->prepare('	SELECT * FROM last_analyse_from_sites 
						WHERE datediff(NOW(), dt_analyse) >= :analyse_min_days_old  
						ORDER BY dt_analyse ASC 
						LIMIT 0, :nb_max_analyses
				');

$q->bindValue(':nb_max_analyses', $nb_max_analyses, PDO::PARAM_INT);
$q->bindValue(':analyse_min_days_old', $analyse_min_days_old, PDO::PARAM_INT);
$q->execute();

$analyses_to_update = $q->fetchAll();

echo '<pre class="alert alert-info">';
echo 'Nombre de jours min avant lancement d\'une nouvelle analyse : '.$analyse_min_days_old.'<br>';
echo 'Nombre d\'analyses max par script : '.$nb_max_analyses.'<br>';
echo 'Nombre d\'analyses à effectuer : '.count($analyses_to_update).'<br>';
echo '</pre>';

$lm->writeLog('Nombre d\'analyses à effectuer : '.count($analyses_to_update));


// LANCEMENT DES ANALYSES

foreach ($analyses_to_update as $analyse) {

	echo '<pre class="alert alert-info">';
	print_r($analyse);
	
	$url = $analyse['url'];
	$lm->writeLog('--- '.$url.' ---');

	$api_error = false;

	// 
	// 
	// APPYDEX + PAGESPEED

	$client = new AppYuserClient();
	$response_appydex = json_decode($client->requestAppYuserAPI($url));

	if(isset($response_appydex->error) && $response_appydex->error == 'servletException') {

		$lm->writeLog('Appydex ERROR');
		$api_error = true;
	}
	else {
		
		$score_appydex = $response_appydex->resultat[0]->apdex;
		$score_pagespeed = $response_appydex->speedScoreDesktop;

		echo 'score_appydex = <b>'.strval($score_appydex).'</b><br>';
		echo 'score_pagespeed = <b>'.strval($score_pagespeed).'</b><br>';
		$lm->writeLog('Appydex DONE');
	}

	


	// 
	// 
	// WEBPAGETEST

	if(!$api_error) {

		$wpt_client = new WebPageTestClient();
		$response_wpt = json_decode($wpt_client->requestWPT($url));

		if(isset($response_wpt->error) && $response_wpt->error == 'errorAPI_WPT') {

			$lm->writeLog('WPT ERROR');
			$api_error = true;

		}
		else {
			$speed_index_wpt = $response_wpt->data->median->firstView->SpeedIndex;
			$fullyloaded_wpt = $response_wpt->data->median->firstView->fullyLoaded;

			echo 'speed_index_wpt = <b>'.$speed_index_wpt.'</b><br>';
			echo 'fullyloaded_wpt = <b>'.$fullyloaded_wpt.'</b><br>';
			$lm->writeLog('WPT DONE');
		}
	}


	//
	// 
	// SAVE ANALYSE

	if(!$api_error) {

		$qInsertAnalyse = $pdo->prepare(' 	
			INSERT INTO analyse (site_id, dt_analyse, score_appydex, score_pagespeed, speed_index_wpt, fullyloaded_wpt)
			VALUES (:site_id, NOW(), :score_appydex, :score_pagespeed, :speed_index_wpt, :fullyloaded_wpt)
		');

		if($qInsertAnalyse->execute([
			':site_id' => $analyse['id_site'],
			':score_appydex' => $score_appydex,
			':score_pagespeed' => $score_pagespeed,
			':speed_index_wpt' => $speed_index_wpt,
			':fullyloaded_wpt' => $fullyloaded_wpt,
		])) {

			$lm->writeLog('Analyse SAVED (id: '.$pdo->lastInsertId().')');
		}
	}
	else {
		$lm->writeLog('Analyse FAILED');
	}


	echo '</pre>';

} // endforeach






// FINALISATION DES LOGS

$lm->writeLog('cron_script end');
$lm->insertSeparator();
$lm->close();

?>