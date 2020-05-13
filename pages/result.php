<?php

/* --- INFORMATIONS ------------------------------------------------------------------------------------------ */
// 
// La page Result permet d'afficher les résultats d'une analyse :
// - récupère les résultats de l'analyse actuelle et calcule les scores de satisfaction
// - récupère les analyses des autres sites pour calculer le classement du site étudié selon les différents indicateurs
// - convertit les données d'analyse en JavaScript pour la mise en forme de graphiques de comparaison
// - intègre deux types de comparatifs : par secteur / par rapport à l'ensemble des sites de l'application
// - Graphiques :
//      - 2 graphiques type Bubble (configurés dans chart_bubble.js)
//      - 1 graphique type Radar (configuré dans chart_radar.js)
// - affiche un tableau récapitulatif des classements
// 
/* ----------------------------------------------------------------------------------------------------------- */


// echo '<pre class="alert alert-info">';
// echo '$_SERVER<r>';
// print_r($_SERVER);
// echo '</pre>';


// RECUPERATION DES DONNEES DE L'URL

if(!empty($_GET)) {

    $get = array_map('trim', array_map('strip_tags', $_GET));

    if (isset($_GET['id_analyse'])) {

        $id_analyse = $_GET['id_analyse'];

        $analyseExists = $pdo->prepare('    SELECT id FROM analyse
                                            WHERE id = :id
        ');

        $analyseExists->execute([':id' => $id_analyse]);

        if(!$analyseExists->fetch()) {
            header("Location: index.php");
        }

    } else {
        header("Location: index.php");
    }

    // Récupération du filtre de comparatif
    $result_filter = isset($_GET['filter']) ? $_GET['filter'] : 'secteur'; 
}
else {
    header("Location: index.php");
}



// RECUPERATION DE L'ANALYSE

$qAnalyse = $pdo->prepare('SELECT * FROM last_analyse_from_sites WHERE id_analyse = :id_analyse');
$qAnalyse->execute([':id_analyse' => $id_analyse]);
$analyse = $qAnalyse->fetch();
$analyse['si_wpt_bareme'] = baremeWPT($analyse['si_wpt_sec']);



// RECUPERATION DES ANALYSES LES PLUS RECENTES

$sorts = ['score_appydex', 'score_pagespeed', 'speed_index_wpt', 'fullyloaded_wpt'];
$tabs = [];

foreach ($sorts as $sort) {
    
    $sql = 'SELECT * FROM last_analyse_from_sites ';

    if($result_filter == 'secteur') { 
        $sql .= 'WHERE id_secteur = '.$analyse['id_secteur'].' '; 
    }
    if($sort == 'speed_index_wpt' || $sort == 'fullyloaded_wpt') {
        $sql .= 'ORDER BY '.$sort.' ASC';
    } 
    else {
        $sql .= 'ORDER BY '.$sort.' DESC';
    }

    $q = $pdo->query($sql);
    $tabs[$sort] = $q->fetchAll();
}



// CALCUL DES RANGS DE L'ANALYSE

$ranks = [];

foreach ($tabs as $sort => $tab) {
    foreach ($tab as $key => $lig) {
        if($lig['id_analyse'] == $analyse['id_analyse']) {
            $rank = $key+1;
            if($rank == 1) { 
                $str = '1er'; 
            }
            else { 
                $str = $rank.'ème'; 
            }
            $ranks[$sort] = $str;
        }
    }
}

?>

<!-- BREADCRUMB -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i></a></li>
        <li class="breadcrumb-item active" aria-current="page">Résultats</li>
    </ol>
</nav>

<!-- TITLE -->
<div class="title-container">
    <h1><span class="bring-out">Résultats</span> des analyses <i class="fas fa-chart-line"></i></h1>
    <h2><em><?= $analyse['url']; ?></em></h2>
</div>



<!-- TABS PICKER -->

<div id="tabs-selector">
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" <?php if($result_filter == 'secteur') { echo 'class="active"'; } ?>>
            <a href="index.php?page=result&id_analyse=<?=$id_analyse;?>&filter=secteur" aria-controls="secteur" role="tab">Par secteur (<em><?= $analyse['libelle']; ?></em>)</a>
        </li>
        <li role="presentation" <?php if($result_filter == 'all') { echo 'class="active"'; } ?>>
            <a href="index.php?page=result&id_analyse=<?=$id_analyse;?>&filter=all" aria-controls="profile" role="tab">Tous les sites</a>
        </li>
    </ul>
</div>

<!-- TABS CONTAINER -->

<div id="tabs-container">

    <!-- CONTENT-TOP -->

    <div class="row">

        <!-- RECAPITULATIFS -->

        <div class="col-lg-6">

            <!-- RESULTATS -->

            <div class="mycharts mycharts-default">

                <div class="mycharts-heading">
                    <h3><i class="fas fa-project-diagram"></i> Récapitulatif des résultats</h3>
                    <button type="button" class="mycharts-toggle-btn btn btn-sm"><i class="fas fa-caret-down"></i></button>
                </div>

                <div class="mycharts-body">
                    <ul>
                        <li>
                            Indice de satisfaction appYdex : 
                            <strong><?= $analyse['score_appydex']. ' ('.baremeAppydex($analyse['score_appydex']).')';?></strong>
                        </li>
                        <li>
                            Score de Page Speed Insight (Google) : 
                            <strong><?= $analyse['score_pagespeed']; ?></strong>
                        </li>
                        <li>
                            Speed Index : 
                            <strong><?= $analyse['si_wpt_sec']; ?></strong> secondes 
                        </li>
                        <li>
                            Fully Loaded : 
                            <strong><?= $analyse['fl_wpt_sec']; ?></strong> secondes 
                        </li>
                    </ul>
                </div>
            </div>

            <!-- CLASSEMENTS -->

            <div class="mycharts mycharts-default">

                <div class="mycharts-heading">
                    <h3><i class="far fa-chart-bar"></i> Récapitulatif des classements (sur <?= count($tabs['score_appydex']); ?> sites)</h3>
                    <button type="button" class="mycharts-toggle-btn btn btn-sm"><i class="fas fa-caret-down"></i></button>
                </div>

                <div class="mycharts-body">
                    <ul>
                        <li>Vous êtes <strong style="color: #d55f37;"><?= $ranks['score_appydex']; ?></strong> (indice appYdex)</li>
                        <li><strong><?= $ranks['score_pagespeed']; ?></strong> (score Page Speed)</li>
                        <li><strong><?= $ranks['speed_index_wpt']; ?></strong> (WebPageTest Speed Index)</li>
                        <li><strong><?= $ranks['fullyloaded_wpt']; ?></strong> (WebPageTest Fully Loaded)</li> 
                    </ul>
                </div>
            </div>

        </div>

        <!-- RADAR CHART - COMPARATIF METRIQUES -->

        <div class="col-lg-6">
            <div class="mycharts mycharts-default">

                <div class="mycharts-heading">
                    <h3><i class="fas fa-project-diagram"></i> Comparaison des métriques</h3>
                    <button type="button" class="mycharts-toggle-btn btn btn-sm"><i class="fas fa-caret-down"></i></button>
                </div>

                <div class="mycharts-body"> 
                    <div align="center" style="color: #d55f37;"><em><strong><?= $analyse['url']; ?></strong></em></div>
                    <div><canvas id="radar-chart"></canvas></div>
                </div>

            </div>
        </div>

    </div>


    <!-- CONTENT-MID -->

    <div class="row">

        <!-- BUBBLE CHART -->

        <div class="col-lg-6">
            <div class="mycharts mycharts-default">

                <div class="mycharts-heading">
                    <h3><i class="far fa-chart-bar"></i> appYdex / pageSpeed</h3>
                    <button type="button" class="mycharts-toggle-btn btn btn-sm"><i class="fas fa-caret-down"></i></button>
                </div>
                
                <div class="mycharts-body">
                    <div align="center" style="color: #d55f37;"><em><strong><?= $analyse['url']; ?></strong></em></div>
                    <div><canvas id="bubble-chart"></canvas></div>
                </div>

            </div>
        </div>

        <!-- BUBBLE CHART WPT -->

        <div class="col-lg-6">
            <div class="mycharts mycharts-default">

                <div class="mycharts-heading">
                    <h3><i class="far fa-chart-bar"></i> Indices WebPageTest</h3>
                    <button type="button" class="mycharts-toggle-btn btn btn-sm"><i class="fas fa-caret-down"></i></button>
                </div>

                <div class="mycharts-body">
                    <div align="center" style="color: #d55f37;"><em><strong><?= $analyse['url']; ?></strong></em></div>
                    <div><canvas id="bubble-chart-wpt"></canvas></div>
                </div>

            </div>
        </div>

    </div>


    <!-- CONTENT-BOT -->

    <div class="row">
    
        <!-- CLASSEMENT -->

        <div class="col-lg-12">
            <div class="mycharts mycharts-default">

                <div class="mycharts-heading">
                    <h3><i class="fas fa-trophy"></i> Classements</h3>
                    <button type="button" class="mycharts-toggle-btn btn btn-sm"><i class="fas fa-caret-down"></i></button>
                </div>

                <div class="mycharts-body">
                    <div class="table-responsive">
                        <table id="ranking_sites" class="datatable table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th style="width: 30%;">site</th>
                                    <th>appYdex_eu</th>
                                    <th>pageSpeed</th>
                                    <th>index_WPT</th>
                                    <th>full_WPT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($tabs['score_appydex']) > 0): ?>
                                    <?php foreach($tabs['score_appydex'] as $lig): ?>
                                        <?php if($lig['id_analyse'] == $analyse['id_analyse']): ?>
                                        <tr style="font-weight: bold; color: #d55f37;">
                                        <?php else: ?>
                                        <tr>
                                        <?php endif; ?>
                                            <td></td>
                                            <td><?= $lig['url']; ?></td>
                                            <td align="right"><?= $lig['score_appydex']; ?></td>
                                            <td align="right"><?= $lig['score_pagespeed']; ?></td>
                                            <td align="right"><?= $lig['speed_index_wpt']; ?></td>
                                            <td align="right"><?= $lig['fullyloaded_wpt']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    var analyse = <?= json_encode($analyse); ?>;
    console.log('analyse :');
    console.log(analyse);
    var tabs = <?= json_encode($tabs); ?>;
    console.log('tabs :');
    console.log(tabs);
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="js/chart_bubble.js"></script>
<script src="js/chart_radar.js"></script>

<!-- <script src='https://cdn3.devexpress.com/jslib/18.1.3/js/dx.all.js'></script> -->