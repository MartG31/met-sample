<?php

/* ---------------------------------------------- WebPageTest -> FullyLoaded Time + Speed Index --------------------------------- */
/*  Besoin de l'API : clé pour API (disponible à cette adresse : https://www.webpagetest.org/getkey.php)
 *  API : widgetsburritos/webpagetest
 *  Toutes les données pouvant être rajoutées sont disponible dans ./fixtures/testresults-complete.json
 *  DOCUMENTATION : https://sites.google.com/a/webpagetest.org/docs/advanced-features/webpagetest-restful-apis
 *  /!\ LIMITATION /!\ : 200 sites par jours.
 *  Actuellement configuré sur 2 runs (pas de repeat view) et les données récupérées sont celles de la médianne des 2 runs
 * ***************************************************************************************************************************** */

require('../vendor/autoload.php');

use WidgetsBurritos\WebPageTest\WebPageTest;
use Teapot\StatusCode;

class WebPageTestClient {

    public function requestWPT($url) {

        $wpt = new WebPageTest(self::API_KEY);

        // Options de test
        $options = [
            "runs" => 2,
            "fvonly" => 1,
        ];

        // Test de l'URL

        if ($response = $wpt->runTest($url, $options)) {
            if ($response->statusCode == StatusCode::OK) {
                $test_id = $response->data->testId;
            } 
            else if ($response->statusCode == StatusCode::CONTINUING) {
                // echo false;
                // exit();
            } 
            else if ($response->statusCode == StatusCode::SWITCHING_PROTOCOLS) {
                // echo false;
                // exit();
            } 
            else if ($response->statusCode == StatusCode::PAYMENT_REQUIRED) {
                return json_encode(['error' => 'PAYMENT_REQUIRED']);
            } 
            else {
                return json_encode(['error' => 'errorAPI_WPT']);
            }
        }

        // Attente de la fin du test

        if ($response = $wpt->getTestStatus($test_id)) {

            //On voit où en est le status du test
            $statut_code = $response->statusCode;
            $statut_text = $response->statusText;

            while ($statut_code != 200 && $statut_text != 'Test Complete') {
                $response = $wpt->getTestStatus($test_id);

                //Code et texte du status terminé
                $statut_code = $response->statusCode;
                $statut_text = $response->statusText;
            }
        }

        // Attente & récupération des resultats

        if ($response = $wpt->getTestResults($test_id)) {

            //On voit où en est le status de la récupération des résultats
            $statut_code = $response->statusCode;
            $statut_text = $response->statusText;

            while ($statut_code != 200 && $statut_text != 'Test Complete') {
                $response = $wpt->getTestResults($test_id);

                //Code et texte du status terminé
                $statut_code = $response->statusCode;
                $statut_text = $response->statusText;
            }
        }

        return json_encode($response);
    }
}

?>