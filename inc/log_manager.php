<?php

class LogManager {

	private $flux;

	public function open($path = 'root') {

		if($path === 'cron') {
			$this->flux = fopen('../files/cron_logs.txt','a');
		}
		elseif($path === 'ajax') {
			$this->flux = fopen('../files/logs.txt','a');
		}
		else {
			$this->flux = fopen('files/logs.txt','a');
		}
	}

	public function writeLog($text) {
		$dt = new DateTime();
		fputs($this->flux, str_replace(':', 'h ' ,
								str_replace('.', 'm ' ,
									str_replace('_', 's' ,$dt->format('Y-m-d / H:i.s_')))).' || '.$text."\r\n");
	}

	public function insertSeparator() {
		fputs($this->flux, 
		'-----------------------------------------------'
		."\r\n");
	}

	public function clearLogs() {
		ftruncate($this->flux, 0);
	}

	public function close() {
		fclose($this->flux);
	}
}

?>
