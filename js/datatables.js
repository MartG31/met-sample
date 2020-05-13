/*-----------------------------datatables.js----------------------------- */
/*  Cette page permet la conception des tableaux disponibles sur la page
 *  index.php, classement.php et result.php
 *  
 ************************************************************************/

/*-----------------------------PREREQUIS----------------------------- */
/* 1) Bibliothèques JQuery : 
 *      <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>   
 * 
 * 2) CSS du plugin Datatables de JQuery : 
 *      <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />
 *      
 * 3) Script du Plugin Datatables de JQuery : 
 *      <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>  
 *      <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
 *      
 ************************************************************************/

/*-----------------------------UTILISATION----------------------------- */
/*  Il suffit simplement d'intégrer l'ID voulu dans la balise <table> :
 *  <table id="ranking_sites" class="table table-striped table-bordered">
 *  En respectant la structure <thead>, <tbody>
 *  Puis de modifier les parametres ci-dessous. >>
 ************************************************************************/

//Tableau des derniers sites analysés (index.php) -->

$(document).ready(function () {
    $('#last_analysed_sites').DataTable({
        /*--- Sauvegarder l'etat de la table ---*/
        stateSave: false,
        /*--- Pagination ---*/
        "paging": true,
        /*--- Rechercher ---*/
        "searching": false,
        /*--- Ordre des colonnes ---*/
        "order": [
            [0, "desc"]
        ],
        /*--- Language du tableau ---*/
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
                    /*"lengthMenu": "Afficher _MENU_ enregistrements par page",   
                     "zeroRecords": "Aucun enregistrements trouvés.",
                     "info": "Page _PAGE_ à _PAGES_",
                     "infoEmpty": "Aucun enregistrements disponible",
                     "infoFiltered": "(Filtrés à partir de _MAX_ enregistrements)"*/
        },
        "info": false,
        "lengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
                /*columnDefs: [{
                 targets: 1,
                 render: $.fn.dataTable.render.moment('Do MMM YYYYY')
                 }]*/
    });
});


//Tableau classement selon les outils d'analyse (result.php) -->

$(document).ready(function () {
    var t = $('#ranking_sites').DataTable({
        /*--- Sauvegarder l'etat de la table ---*/
        "stateSave": false,
        /*--- Pagination ---*/
        "paging": true,
        /*--- Rechercher ---*/
        "searching": false,
        /*--- Enlever les fonctions de la colonne ---*/
        "columnDefs": [{
                "searchable": false,
                "orderable": false,
                "targets": 0
            }],
        /*--- Ordre des colonnes ---*/
        "order": [
            [2, "desc"],[3,"desc"],[4,"desc"]
        ],
        /*--- Language du tableau ---*/
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
        },
        "info": true,
        "lengthMenu": [[10, 15, 20, -1], [10, 15, 20, "All"]]
    });
    /*--- Index (classement) ---*/
    t.on('order.dt search.dt', function () {
        t.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();
});



//Tableau classement (classement.php) -->

$(document).ready(function () {
    var t2 = $('#all_sites').DataTable({
        /*--- Sauvegarder l'etat de la table ---*/
        "stateSave": false,
        /*--- Pagination ---*/
        "paging": true,
        /*--- Rechercher ---*/
        "searching": true,
        /*--- Enlever les fonctions de la colonne ---*/
        "columnDefs": [{
                "searchable": false,
                "orderable": false,
                "targets": 0
            }],
        /*--- Ordre des colonnes ---*/
        "order": [
            [4, "desc"],[5,"desc"],[6,"desc"],[7,"asc"]
        ],
        /*--- Language du tableau ---*/
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
        },
        "info": true,
        "lengthMenu": [[10, 15, 20, -1], [10, 15, 20, "All"]]
    });
    /*--- Index (classement) ---*/
    t2.on('order.dt search.dt', function () {
        t2.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();
//            initComplete: function () {
//        this.api().columns(3).every( function () {
//        var column = this;
//        $('#dataTables-example .head .head_hide').html('');
//
//        var select = $('<select id="formfilter" class="filterdropdown"><option value="">'+$(column.header()).text()+'</option></select>')
//            .appendTo( $(column.header()).empty())
//            .on( 'change', function () {
//                var val = $.fn.dataTable.util.escapeRegex(
//                    $(this).val()
//                );
//                column
//                    .search( val ? '^'+val+'$' : '', true, false )
//                    .draw();
//            });
//            column.data().unique().sort().each( function ( d, j ) {
//            select.append( '<option value="'+d+'">'+d+'</option>' )
//        });
//    }); 
});


//Tableau classement (classement.php) -->

$(document).ready(function () {
    var t3 = $('#analyses_table').DataTable({
        /*--- Sauvegarder l'etat de la table ---*/
        "stateSave": false,
        /*--- Pagination ---*/
        "paging": false,
        /*--- Rechercher ---*/
        "searching": true,
        /*--- Enlever les fonctions de la colonne ---*/
        "columnDefs": [{
                "searchable": false,
                "orderable": false,
                "targets": 0
            }],
        /*--- Ordre des colonnes ---*/
        "order": [
            [0, "asc"]
        ],
        /*--- Language du tableau ---*/
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
        },
        "info": true,
        "lengthMenu": [[10, 15, 20, -1], [10, 15, 20, "All"]]
    });
    /*--- Index (classement) ---*/
    t3.on('order.dt search.dt', function () {
        t3.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();
});

$(document).ready(function () {
    var t4 = $('#sectors-table').DataTable({
        /*--- Sauvegarder l'etat de la table ---*/
        "stateSave": false,
        /*--- Pagination ---*/
        "paging": true,
        /*--- Classement ---*/
        // "ordering": false,
        /*--- Rechercher ---*/
        "searching": false,
        /*--- Enlever les fonctions de la colonne ---*/
        "columnDefs": [{
                "searchable": false,
                "orderable": false,
                "targets": 0
            }, {
                "searchable": false,
                "orderable": false,
                "targets": 2
            }],
        /*--- Ordre des colonnes ---*/
        "order": [
            // [0, "asc"]
        ],
        /*--- Language du tableau ---*/
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
        },
        "info": true,
        "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]]
    });
    /*--- Index (classement) ---*/
    t4.on('order.dt search.dt', function () {
        t4.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();
});