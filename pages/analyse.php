<?php

/* --- INFORMATIONS ------------------------------------------------------------------------------------------ */
// 
// La page Analyse assure le suivi des analyses en cours
// - Récupère et traite les données de formulaire (mode single_analyse OU mode batch)
// - Remplit le tableau d'analyse $toAnalyse
// - Convertit le tableau en JavaScript et instancie un objet Analyse par élément du tableau
// - Les analyses sont effectuées de manière dynamique grâce à des requêtes Ajax
// - Stocke toutes les analyses réussies en base de données
// - Propose un suivi des résultats d'analyse en temps réel grâce à la classe ProgressBar
// 
/* ----------------------------------------------------------------------------------------------------------- */

/* VERIFICATION DES FORMULAIRES */

// echo '<pre class="alert alert-info">';
// echo '$_POST<br>';
// print_r($_POST);
// echo '$_FILES<br>';
// print_r($_FILES);
// echo '</pre>';

if(!empty($_POST)) {

	$post = array_map('trim', array_map('strip_tags', $_POST));
	$toAnalyse = [];

	// 
	// 
	// 
	// 
	// FORMULAIRE : SINGLE-ANALYSE (analyse d'une url unique)

	if(isset($post['action']) && $post['action'] == 'single-analyse') {

		$single_errors = [];

		// echo '<pre class="alert alert-info">';
		// echo '--- Single Analyse --- <br>';

		if(!formatUrl($post['url'])) {
			$single_errors[] = 'L\'url renseignée est invalide';
		}
		elseif(!isset($post['id_secteur'])) {
			$single_errors[] = 'Vous devez sélectionner un secteur d\'activité';	
		}

		// echo 'url : '.$url.'<br>';

		if(count($single_errors) === 0) {

			$data = [];
			$data['url'] = $post['url'];
			$data['id_secteur'] = $post['id_secteur'] != '' ? $post['id_secteur'] : null;

			if(isset($post['score_appydex']) && isset($post['score_pagespeed'])) {

				$data['score_appydex'] = $post['score_appydex'];
				$data['score_pagespeed'] = $post['score_pagespeed'];
			}

			$toAnalyse[] = $data;
			// $single_ready = true;

			// print_r($data);
			// echo '</pre>';
		}
		else {
			header('Location: index.php?page=home&single_error='.$single_errors[0]);
		}
	}
	// 
	// 
	// 
	// 
	// FORMULAIRE : BATCH-MODE (upload et traitement d'un fichier csv)

	elseif(!empty($_FILES) && isset($post['action']) && $post['action'] == 'batch-mode') {
			
		$uploadDir = 'files/';
		$upload_errors = [];

		if($_FILES['csv-file']['error'] != UPLOAD_ERR_OK) {
		    $upload_errors[] = 'Une erreur est survenue lors de la sélection du fichier';
		}
		else {

		    // Init MIME Type & EXTENSION
		    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
		    $mimeType = finfo_file($fileInfo, $_FILES['csv-file']['tmp_name']);
		    finfo_close($fileInfo);
		    $extension = strtolower(pathinfo($_FILES['csv-file']['name'])['extension']);

		    // echo '<pre class="alert alert-info">';
		    // echo 'UPLOAD<br>';
		    // echo $mimeType.'<br>';
		    // echo $extension.'<br>';

		    $allowedMimes = ['text/plain', 'text/csv'];

		    // VERIFICATION DU FORMAT DE FICHIER
		    if(!in_array($mimeType, $allowedMimes) || $extension != 'csv') {
		    	$upload_errors[] = 'Le type de fichier est invalide';
		    }
		    else {
		        $dt = new DateTime();
		        $fileName = $dt->format('Ymd-His').'.'.$extension;
		        $finalPath = $uploadDir.$fileName;
		        // echo $fileName.'<br>';
		        // echo $finalPath.'<br>';

		        // ENREGISTREMENT DU FICHIER
			    if(!move_uploaded_file($_FILES['csv-file']['tmp_name'], $finalPath)) {
			    	$upload_errors[] = 'Erreur lors de l\'écriture du fichier';
			    }
		    }
		}

	    if(count($upload_errors) === 0) {

	        $upload_success = true;
	        // echo 'upload_success !<br>';
	        // echo '</pre>';

	        // LECTURE DU FICHIER
	        $csv = fopen($finalPath, 'r');

	        // echo '<pre class="alert alert-info">';
	        // echo 'READ CSV<br>';

	        $csv_data = [];
	        while($data = fgetcsv($csv, 1024, ',')) {

	        	if(count($data) == 2 && is_numeric($data[1])) {

		        	$csv_data[] = [
		        		'url' => $data[0],
		        		'id_secteur' => $data[1],
		        	];
	        	}
	        }

	        // echo 'Nombre d\'url récupérées : '. count($csv_data) .'<br>';
	        // echo '<hr>';

	        foreach ($csv_data as $lig) {
		        $toAnalyse[] = $lig;
	        }

	        // echo '--- Batch Mode --- <br>';
	        // echo 'Nombre d\'url à analyser : '.count($toAnalyse).'<br>';
	        // print_r($toAnalyse);
	        // echo '</pre>';
	        
	        unlink($finalPath);  

	        if(count($toAnalyse) === 0){
	        	$msg = 'Le fichier choisi ne contient aucune donnée valide à analyser';
	        	header('Location: index.php?page=home&upload_error='.$msg);
	        }

	        // 
	        // Limitation temporaire du nombre d'analyses simultanées
	        // 
	        // $toAnalyse = array_slice($toAnalyse, 286, 34);
	        // shuffle($toAnalyse);

	    }
	    else {
	    	header('Location: index.php?page=home&upload_error='.$upload_errors[0]);
	    }
	}
}
else {
	header('Location: index.php?page=home');
}

// 
// 
// 
// 
// 

?>

<!-- BREADCRUMB -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i></a></li>
        <li class="breadcrumb-item active" aria-current="page">Suivi d'analyse</li>
    </ol>
</nav>

<!-- TITLE -->
<div class="title-container">
    <h1><span class="bring-out">Suivi</span> des analyses <i class="far fa-chart-bar"></i></h1>
    <h2>Notre site calcule vos scores de performance, les résultats seront disponibles dans quelques instants</h2>
</div>


<!-- CONTENT -->
<div class="row">

	<!-- PROGRESS BAR -->

	<div class="col-lg-12">
		<div class="mycharts mycharts-default">

            <div class="mycharts-heading">
                <h3><i class="fas fa-list-ol"></i>Progression</h3>
            	<button type="button" class="mycharts-toggle-btn btn btn-sm"><i class="fas fa-caret-down"></i></button>
            </div>

            <div class="mycharts-body">
		
				<div class="progress mb-0">
					<div id="pb_analyses" class="progress-bar progress-bar-striped bg-success" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
				</div>
				<div id="pb_percent" class="text-center font-weight-bold"></div>

				<ul id="pb_stats"></ul>
			</div>

		</div>
	</div>

	<!-- TABLEAU DE SUIVI -->

    <div class="col-lg-12">
        <div class="mycharts mycharts-default">

            <div class="mycharts-heading">
                <h3><i class="fas fa-list-ol"></i>Liste des sites à analyser</h3>
            	<button type="button" class="mycharts-toggle-btn btn btn-sm"><i class="fas fa-caret-down"></i></button>
            </div>

            <div class="mycharts-body">
                <div class="table-responsive">  
                    <table id="analyses_table" class="datatable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Site</th>
                                <th>appYdex_eu</th>
                                <th>pageSpeed</th>
                                <th>index_WPT</th>
                                <th>full_WPT</th>
                                <th>Durée</th>
                                <th class="no-sort"></th>
                                <th class="no-sort"></th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?php $i=0; ?>
                        	<?php foreach($toAnalyse as $data): ?>
                        		<tr id="site-<?= $i; ?>">
                        			<td><?= ++$i; ?></td>
                        			<td class="url"><?= $data['url']; ?></td>
                        			<td align="right" class="score_appydex"></td>
                        			<td align="right" class="score_pagespeed"></td>
                        			<td align="right" class="speed_index_wpt"></td>                            			
                        			<td align="right" class="fullyloaded_wpt"></td>
                        			<td align="right" class="duration"></td>
                        			<td align="center" class="analyse_done"></td>
                        			<td align="center" class="analyse_save"></td>
                        		</tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
	$(document).ready(function() {


		// 
		// --- CLASS ANALYSE --- // 
		// 
		function Analyse() {

			// ATTRIBUTES

			this.rank;

			this.inputURL;
			this.url;
			this.id_secteur;

			this.dt_begin;

			this.analyse_status;
			
			this.url_success;
			this.appydex_success;
			this.wpt_success;

			this.score_appydex; 
			this.score_pagespeed; 
			this.speed_index_wpt;
			this.fullyloaded_wpt;

			this.analyse_success;
			this.save_success;
			this.save_id;

			// METHODS

			this.launchAnalyse = function() {

				this.url = this.inputURL;
				this.dt_begin = new Date();

				this.parseURL();
			}

			// STATUS FUNCTIONS

			this.checkResponses = function() {

				if(typeof(this.url_success) === 'undefined') {
					this.analyse_status = 'parsing';
				}
				else if(this.url_success && (typeof(this.appydex_success) === 'undefined' || typeof(this.wpt_success) === 'undefined')) {
					this.analyse_status = 'pending';
				}
				else if(this.url_success == false || this.appydex_success == false || this.wpt_success == false) {
					this.analyse_status = 'done';
					this.analyse_success = false;
				}
				else if(this.url_success == true && this.appydex_success == true && this.wpt_success == true) {
					this.analyse_status = 'done';
					this.analyse_success = true;
					this.saveAnalyse();	
				}

				if(typeof(this.url_success) != 'undefined') { console.log('url_success = '+this.url_success); }
				if(typeof(this.appydex_success) != 'undefined') {console.log('appydex_success = '+this.appydex_success); }
				if(typeof(this.wpt_success) != 'undefined') {console.log('wpt_success = '+this.wpt_success); }
				if(typeof(this.analyse_status) != 'undefined') {console.log('analyse_status = '+this.analyse_status); }
				if(typeof(this.analyse_success) != 'undefined') {console.log('analyse_success = '+this.analyse_success); }

				if(this.analyse_status == 'done') {
					$(this).trigger('done');
				}

				this.refreshDisplay();
			}

			this.refreshDisplay = function() {

				var lig = $('#site-'+this.rank);
				lig.find('.score_appydex').text(this.score_appydex);
				lig.find('.score_pagespeed').text(this.score_pagespeed);
				lig.find('.speed_index_wpt').text(this.speed_index_wpt);
				lig.find('.fullyloaded_wpt').text(this.fullyloaded_wpt);

				if(this.url_success) {
					lig.find('.url').html('<i title="URL validée" class="fas fa-check-circle"></i>&nbsp;<strong>'+this.url+'</strong>');
				}

				if(this.analyse_status == 'done') {
					if(this.analyse_success) {
						if(this.save_success) {
							lig.find('.analyse_done').html('<i title="Analyse enregistrée avec succès" class="fas fa-check-circle text-success"></i>');
							lig.find('.analyse_save').html('<a title="Voir les résultats" href="index.php?page=result&id_analyse='+this.save_id+'" target="blank"><i class="far fa-file-alt"></i></a>');
							pb.increase();
						}
						else if(this.save_success == false) {
							lig.find('.analyse_done').html('<i title="Erreur lors de l\'enregistrement de l\'analyse" class="fas fa-times-circle text-danger"></i>');
							pb.increaseErrors();
						}
					} 
					else if(this.url_success == true && this.analyse_success == false) {
						lig.find('.analyse_done').html('<i title="Erreur lors des analyses" class="fas fa-times-circle text-danger"></i>');
						pb.increaseErrors();
					}
					else {
						lig.find('.analyse_done').html('<i title="URL invalide" class="fas fa-times-circle text-danger"></i>');
						pb.increaseErrors();
					}
					lig.find('.duration').html(timeSince(this.dt_begin));
				}
			}

			// ASYNC PROCESS

			this.parseURL = async function() {

				var obj = this;

			 	$.ajax({
			 	   	url : 'ajax/ajax_url_parse.php',
			 	   	type : 'POST',
			 	   	data: { url: obj.url },
			 	   	dataType : 'text',

			 	   	success : function(finalURL){

			 	   		console.log('parseURL ajax success : ' + finalURL);

			 	   		if(finalURL) {
			 	   			obj.url_success = true;
			 	   			obj.url = finalURL;
			 	   			obj.launchAPI();
			 	   		}
			 	   		else {
			 	   			obj.url_success = false;
			 	   		}
			 	   		obj.checkResponses();
			 	    },

					error : function(resultat, statut, erreur){
						console.log('----------');
						console.log('SaveAnalyse ajax error : ' + obj.url);
						console.log(erreur);
						console.log('----------');

						obj.url_success = false;
						obj.checkResponses();
					}
			 	});
			}

			this.launchAPI = function() {

				this.callAppydex();
				this.callWebPageTest();
			}

			this.callAppydex = async function() {

				var obj = this;
				console.log('calling ajax Appydex : ' + obj.url);

			 	$.ajax({
			 	   	url : 'ajax/ajax_appydex.php',
			 	   	type : 'POST',
			 	   	data: {url: obj.url},
			 	   	dataType : 'json',

			 	   	success : function(json){
			 	   		console.log('----------');
			 	       	console.log('Appydex ajax success : ' + obj.url);
			 	       	console.log(json);
			 	       	logTimeSince(dtBegin);
			 	   		console.log('----------');

			 	   		
			 	   		if(typeof json.error != 'undefined' && json.error == 'servletException') {
			 	   			
			 	   			obj.score_appydex = 'servlet';
				 	       	obj.score_pagespeed = 'servlet';

			 	       		obj.appydex_success = false;
			 	   		}
			 	   		else if(typeof json.resultat == 'undefined' || json.resultat.length == 0) {

   			 	   			obj.score_appydex = 'undef';
   				 	       	obj.score_pagespeed = 'undef';

   			 	       		obj.appydex_success = false;
			 	   		}
			 	   		else {
			 	   			obj.score_appydex = parseFloat(json.resultat[0].apdex);
				 	       	obj.score_pagespeed = parseInt(json.speedScoreDesktop);

   							if(obj.score_appydex != 0 && obj.score_pagespeed != 0) {
   			 	       			obj.appydex_success = true;
   							}
   							else {
   			 	       			obj.appydex_success = false;
   							}
			 	   		}
			 	       	obj.checkResponses();
			 	    },

					error : function(resultat, statut, erreur){
			 	   		console.log('----------');
					 	console.log('Appydex ajax error : ' + obj.url);
					 	console.log(erreur);
					 	console.log('----------');

	 	 	       		obj.score_appydex = 'ajax_err';
	 		 	       	obj.score_pagespeed = 'ajax_err';

					 	obj.appydex_success = false;
					 	
					 	obj.checkResponses();
					}
			 	});
			}

			this.callWebPageTest = async function() {

				var obj = this;
				console.log('calling ajax WebPageTest : ' + obj.url);
				
				$.ajax({
				   	url : 'ajax/ajax_webpagetest.php',
				   	type : 'POST',
				   	data: {url: obj.url},
				   	dataType : 'json',

				   	success : function(json){
						console.log('----------');
				    	console.log('WebPageTest ajax success : ' + obj.url);
				    	console.log(json);
				    	logTimeSince(dtBegin);
						console.log('----------');

						if(typeof(json.error) != 'undefined' && json.error == 'errorAPI_WPT') {
							obj.speed_index_wpt = 'error';
							obj.fullyloaded_wpt = 'error';

			 	       		obj.wpt_success = false;
						}
						else {
							obj.speed_index_wpt = parseInt(json.data.median.firstView.SpeedIndex);
							obj.fullyloaded_wpt = parseInt(json.data.median.firstView.fullyLoaded);

							if(obj.speed_index_wpt != 0 && obj.fullyloaded_wpt != 0) {
			 	       			obj.wpt_success = true;
							}
							else {
			 	       			obj.wpt_success = false;
							}
						}
						obj.checkResponses();
				   	},

				   	error : function(resultat, statut, erreur){
				     	console.log('----------');
				     	console.log('WebPageTest ajax error : ' + obj.url);
				     	console.log(erreur);
				     	console.log('----------');

     	 	       		obj.speed_index_wpt = 'ajax_err';
     					obj.fullyloaded_wpt = 'ajax_err';

				     	obj.wpt_success = false;

				     	obj.checkResponses();
				   	}
				});
			}

			this.saveAnalyse = async function() {

				var obj = this;
				console.log(obj);

			 	$.ajax({
			 	   	url : 'ajax/ajax_analyse_save.php',
			 	   	type : 'POST',
			 	   	data: 	{
			 	   				url: obj.url,
			 	   				id_secteur: obj.id_secteur,
			 	   				score_appydex: obj.score_appydex,
			 	   				score_pagespeed: obj.score_pagespeed,
			 	   				speed_index_wpt: obj.speed_index_wpt,
			 	   				fullyloaded_wpt: obj.fullyloaded_wpt
			 	   			},
			 	   	dataType : 'text',

			 	   	success : function(id_analyse){

			 	   		console.log('----------');
			 	   		console.log('Analyse ajoutée à la bdd (id: '+id_analyse+')');
			 	   		console.log('----------');

			 	   		obj.save_success = true;
			 	   		obj.save_id = id_analyse;
			 	   		obj.refreshDisplay();
			 	    },

					error : function(resultat, statut, erreur){
						console.log('----------');
						console.log('SaveAnalyse ajax error : ' + obj.url);
						console.log(erreur);
						console.log('----------');

						obj.save_success = false;
					}
			 	});
			}
		}


		// 
		// --- CLASS PROGRESS BAR --- // 
		//
		function ProgressBar() {

			// ATTRIBUTES

			this.dt_begin = new Date();
			this.timer;

			this.min = 0;
			this.now = 0;
			this.max = toAnalyse.length;
			this.errors = 0;

			// METHODS

			this.init = function() {
				var obj = this;
				this.timer = setInterval(function() {
					obj.refresh();
				}, 1000);
			}

			this.refresh = function() {

				var percent;
				var percentSuccess;
				if(this.now == 0) { percent = percentSuccess = 0; }
				else {
					percent = (this.now / this.max)*100;
					percentSuccess = 100 - (this.errors / this.now)*100;
				}

				$('#pb_analyses').attr('aria-valuemin', this.min);
				$('#pb_analyses').attr('aria-valuenow', this.now);
				$('#pb_analyses').attr('aria-valuemax', this.max);
				$('#pb_analyses').css('width', percent + '%');

				$('#pb_percent').html('<strong>'+Math.round(percent) + '%</strong>');
				
				$('#pb_stats').html(
					'<li>Réponses : ' + this.now + '/' + this.max + ' analyses</li>' +
					'<li>Taux de réussite : ' + Math.round(percentSuccess) + '% (' + parseInt(this.now - this.errors) + '/' + this.now +')</li>' + 
					'<li>Temps analyse écoulé : ' + timeSince(this.dt_begin) + '</li>' + 
					'<li>Temps restant estimé : ' + timeRemaining(this.dt_begin, percent) + '</li>' 
				);

				if(this.now == this.max) {
					clearInterval(this.timer);
				}
			}

			this.increase = function() {
				this.now += 1;
				// this.refresh();
			}

			this.increaseErrors = function() {
				this.now += 1;
				this.errors += 1;
				// this.refresh();
			}
		}


		// 
		// --- ANALYSES --- //
		// 


		// CONVERSION DU TABLEAU D'ANALYSE EN OBJET JS
		var toAnalyse = <?= json_encode($toAnalyse); ?>;
		console.log(toAnalyse);


		// INIT PROGRESS BAR
		var pb = new ProgressBar();
		pb.init();


		// INIT TIME REF
		var dtBegin = new Date();


		// INSTANCIATION DES ANALYSES
		var countAnalyses = 0;
		var totalAnalyses = toAnalyse.length;

		var analyses = [];
		toAnalyse.forEach(function(data, i) {

			var a = new Analyse();
			a.rank = i;
			a.inputURL = data.url;
			a.id_secteur = data.id_secteur;
			
			analyses.push(a);
		});


		// LANCEMENT DES ANALYSES GRACE AUX EVENEMENTS

		var idx = 0;

		$(analyses).each(function(i, analyse) {

			$(analyse).on('launch', function(e) {

				$(analyse).on('done', function(e) {

					console.log('--- ANALYSE TERMINEE : '+analyse.url+' ---');
					console.log('-------------------------------------------');
					console.log('-------------------------------------------');

					setTimeout(function() {
						$(analyses[idx]).trigger('launch');

					}, 500);
				});

				console.log('--- NOUVELLE ANALYSE : '+analyse.inputURL+' ---');

				// LANCEMENT DE L'ANALYSE SUIVANTE
				analyse.launchAnalyse();
				idx++;		
			});
		});

		// LANCEMENT PREMIERE(S) ANALYSE(S)
		var nb_analyses_simult = 1;

		for(var n=0; n<nb_analyses_simult;n++) {
			$(analyses[n]).trigger('launch');
		}

	});
</script>
