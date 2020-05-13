<?php

$post = array_map('trim', array_map('strip_tags', $_POST));

require_once '../inc/log_manager.php';
$lm = new LogManager();
$lm->open('ajax');
$lm->writeLog($post['url'] . ' // ajax_save begin');

// 
// 

require_once '../inc/pdo_connect.php';


// RECHERCHE DU SITE DANS LA BDD

$qSearch = $pdo->prepare(' 	SELECT id FROM site 
							WHERE url = :url 		');

$qSearch->execute([':url' => $post['url']]);

if($id_site = $qSearch->fetchColumn()) {
	$site_exists = true;
	$lm->writeLog($post['url'] . ' // ajax_save site_exists id: ' . $id_site);
}
else { $site_exists = false; }

if(!$site_exists) {
	$qInsertSite = $pdo->prepare('	INSERT INTO site (secteur_id, url) 
									VALUES (:secteur_id, :url)			
								');
	$qInsertSite->execute([
							':secteur_id' => $post['id_secteur'],
							':url' => $post['url'],
						]);

	$id_site = $pdo->lastInsertId();
	$lm->writeLog($post['url'] . ' // ajax_save !site_exists id: ' . $id_site);
}

$lm->writeLog($post['url'] . ' // ajax_save site id: ' . $id_site);

// ENREGISTREMENT DE L'ANALYSE

$qInsertAnalyse = $pdo->prepare(' 	INSERT INTO analyse (site_id, dt_analyse, score_appydex, score_pagespeed, speed_index_wpt, fullyloaded_wpt)
									VALUES (:site_id, NOW(), :score_appydex, :score_pagespeed, :speed_index_wpt, :fullyloaded_wpt)
								');

$qInsertAnalyse->execute([
							':site_id' => $id_site,
							':score_appydex' => $post['score_appydex'],
							':score_pagespeed' => $post['score_pagespeed'],
							':speed_index_wpt' => $post['speed_index_wpt'],
							':fullyloaded_wpt' => $post['fullyloaded_wpt'],
						]);

echo $pdo->lastInsertId();

// 
// 

$lm->writeLog($post['url'] . ' // ajax_save end');
$lm->close();




?>