<?php

/* --- INFORMATIONS ------------------------------------------------------------------------------------------ */
// 
// INDEX.PHP : fichier racine du site
// 
// - Gère les inclusions des feuilles de style
// - Gère les inclusions des fichiers PHP et des scripts communs 
// - Intègre la structure HTML du site
// - Identifie la page demandée et charge les contenus en fonction des paramètres $_GET
// 
/* ----------------------------------------------------------------------------------------------------------- */

session_start();

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/inc/log_manager.php';
$lm = new LogManager();
$lm->open();
$lm->clearLogs();
$lm->close();

require_once __DIR__ . '/inc/pdo_connect.php';
require_once __DIR__ . '/inc/functions.php';    


// IDENTIFICATION DE LA PAGE DEMANDEE

if(!empty($_GET['page']) && is_file('pages/'.$_GET['page'].'.php')) {

    $page = $_GET['page'];
    $content = 'pages/'.$page.'.php';
}
else {
    header('Location: index.php?page=home');
}

// STRUCTURE HTML
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="generator" content="Bootply">
        <meta name="description" content="Analysez la performance de votre site web avec MultiEvaluationTool, situez-vous par rapport à la concurrence dans votre secteur d'activité grâce à cet outil proposé par la société Quadran.">
        <title>Multi Evaluation Tool</title>
        <link rel="shortcut icon" href="https://www.quadran.eu/wp-content/uploads/2019/09/favicon.ico">
        
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic, 700italic,800italic,300,400,600,700,800" media="all">

        <!-- My Styles -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
        <link rel="stylesheet" href="style/met_style.css">

        <!-- INIT jQuery & CORE functions -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
        <script src="js/functions.js"></script>
    </head>

    <body>
        <div id="top-content">
            <?php require_once 'struct/navbar.php'; ?>

            <main class="container">
                <?php require_once $content; ?>
            </main>
        </div>
        <?php require_once 'struct/footer.php'; ?>

        <!-- SCRIPTS -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>  
        <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>

        <!-- Mes JS -->
        <script src="js/datatables.js"></script>
        <script src="js/mycharts_toggle.js"></script>
    </body>

</html>