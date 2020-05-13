<?php 

class appYuserClient {

	const APPYUSER_API_ENDPOINT = '???';

    public function requestAppYuserAPI($url) {
     
        $data = http_build_query(
            array(
                'url'            => $url,
                'evaluationMode' => 'fullEvaluation',
                'language'       => 'fr',
                'mute'           => '',
                'device'         => 'desktop'
            )
        );

        $opts = array('http' =>
                    array(
                        'method'  => 'GET',
                        'timeout' => 60
                    )
        );

        $context        = stream_context_create($opts);
        $request_url    = self::APPYUSER_API_ENDPOINT.'EvaluationServlet2'."?".$data;
        $response  = file_get_contents($request_url, false, $context);
        
        return $response; // json
    }
}

?>