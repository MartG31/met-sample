<?php


/*********************************************************************************************************************************************************************************************/
// 
// 
// PARSING URL


function checkUrl($url) {

	$validHeaders = [
		'HTTP/1.1 200 OK', 
		'HTTP/1.1 301 Moved Permanently',
		'HTTP/1.1 302 Found',
	];

	$headers = @get_headers($url);

	if(!empty($headers) && in_array($headers[0], $validHeaders)) {
		return true;
	}
	else {
		return false;
	}
}

function formatUrl($url) {

	// $lm = new LogManager();
	// $lm->open();
	// $lm->writeLog($url . ' // formatUrl begin');
	// $lm->close();

	/* 	-------
		OPTIONS
		-------  */

	$restrictToIndex = true;

	/*  -------  */

	// echo 'initial_url = '.$url.'<br>';

	// CHECK http
	$scheme1 = 'https://';
	$scheme2 = 'http://';

	if(!strstr($url, $scheme1)) {
		if(!strstr($url, $scheme2)) {
			$url = $scheme1.$url;
		}
		else {
			$url = str_replace($scheme1, $scheme2, $url);
		}
	}

	// echo 'scheme_url = '.$url.'<br>';

	// PARSING
	$exp_url = parse_url($url);
	// print_r($exp_url);
	
	$scheme = $exp_url['scheme'];
	$host = $exp_url['host'];
	$path = (isset($exp_url['path']) && !$restrictToIndex) ? $exp_url['path'] : '/';

	// CHECK www.
	// if(substr($host, 0, 4) === 'www.') {

	// 	$host_root = str_replace('www.', '', $host);
	// 	$url_root = $scheme.'://'.$host_root.$path;
	// 	$host = checkUrl($url_root) ? $host_root : $host;
	// }
	
	$url = $scheme.'://'.$host.$path;
	// echo 'parsed_url = '.$url.'<br>';

	

	if(checkUrl($url)) {

		// BUILDING FINAL URL (with REDIRECTION)
		$ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_NOBODY, 1);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // follow redirects
	    curl_setopt($ch, CURLOPT_AUTOREFERER, 1); // set referer on redirect
	    curl_exec($ch);
	    $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	    curl_close($ch);

	    // echo 'final_url = '.$url.'<br>';

	    // $lm->open();
	    // $lm->writeLog($url . ' // formatUrl success');
	    // $lm->close();

	    return $url;
	}
	else {

		// $lm->open();
		// $lm->writeLog($url . ' // formatUrl failed');
		// $lm->close();

		return false;
	}
}



/*********************************************************************************************************************************************************************************************/

// 
// 
// BAREMES

function baremeAppydex($score_appydex) {

	if($score_appydex >= 0.94 && $score_appydex <= 1) {
		return "Excellent";
	}
	elseif($score_appydex >= 0.85 && $score_appydex < 0.94) {
		return "Bon";
	}
	elseif($score_appydex >= 0.70 && $score_appydex < 0.85) {
		return "Acceptable";
	}
	elseif($score_appydex >= 0.50 && $score_appydex < 0.70) {
		return "Mauvais";
	}
	elseif($score_appydex >= 0 && $score_appydex < 0.50) {
		return "Innacceptable";
	}
}

function baremeWPT($speed_index) {

	if($speed_index > 0 && $speed_index <= 0.5) {
		return 100;
	}
	elseif($speed_index > 0.5 && $speed_index <= 1) {
		return 95;
	}
	elseif($speed_index > 1 && $speed_index <= 2) {
		return 75;
	}
	elseif($speed_index > 2 && $speed_index <= 3) {
		return 55;
	}
	elseif($speed_index > 3 && $speed_index <= 4) {
		return 35;
	}
	elseif($speed_index > 4 ) {
		return 15;
	}
	elseif($speed_index > 5 && $speed_index <= 10) {
		return 5;
	}
	elseif($speed_index > 10) {
		return 0;
	}
}


/*********************************************************************************************************************************************************************************************/

// 
// 
// OTHER


function strExtract($string, $length = 10) {

	$sub = substr($string, 0, $length);
	
	if(strlen($sub) != strlen($string)) {
		$sub .= '...';
	}

	return $sub;
}

?>