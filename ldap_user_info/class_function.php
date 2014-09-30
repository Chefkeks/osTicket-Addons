<?php
require_once 'config.php';

class Functions {

    //private $mysqli;
    private $config;
    
    //public function __construct($config, $mysqli) {
    public function __construct($config) {
        //$this->mysqli = $mysqli;
        $this->config = $config;
    }
    
    // Logging function
	public function logg($meldung) {
		// Usage: echo $functions->logg("Logging started...");
		// Set time and log the message to logfile
		$time = @date('[Y-m-d @ H:i:s]');
		$logOK = file_put_contents($this->config->logpath.$this->config->logfilename , $time." ".$meldung."\n", FILE_APPEND | LOCK_EX);
		$logEX = file_put_contents($this->config->logpath.$this->config->loglastexec , $time." ".$meldung."\n", FILE_APPEND | LOCK_EX);		

		// Clear logEX file (delete all content) when logging starts
		if($meldung == "Logging started...") {
			$logEX = file_put_contents($this->config->logpath.$this->config->loglastexec , $time." ".$meldung."\n");
		}

		// If Debug Mode is turned on, check that log was successfully written and if yes (else part) write output messages on the screen
		// Log file with log messages of all executions
		if($this->config->debug == "true") {
			if(!$logOK) {
				return "Error! Unable to write to logfile: " . $this->config->logpath.$this->config->logfilename." => Please check file permissions.<br>";
			} else {
				return $time." ".$meldung."<br>";
			}
		// If Debug mode is turned off, return null to hide debug messages
		} else {
			return null;
		}
		
		// Log file with log messages of the last execution only
		if($this->config->debug == "true") {
			if(!$logEX) {
				return "Error! Unable to write to logfile: " . $this->config->logpath.$this->config->loglastexec." => Please check file permissions.<br>";
			} else {
				// Do not print messages a second time => so we just print null
				//return $time." ".$meldung."<br>";
				return null;
			}
		}
	}
}

?>

