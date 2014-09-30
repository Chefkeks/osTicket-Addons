<?php
require_once 'config.php';

class Functions {
    
    private $mysqli;
    private $config;
    
    public function __construct($config, $mysqli) {
        $this->mysqli = $mysqli;
        $this->config = $config;
    }
    
    // Logging function
	public function logg($meldung) {
		// Usage: echo $functions->logg("Logging started...");
		// Set time and log the message to logfile
		$time = @date('[Y-m-d @ H:i:s]');
		$logOK = file_put_contents($this->config->logpath.$this->config->logfilename , $time." ".$meldung."\n", FILE_APPEND | LOCK_EX);
		// If Debug Mode is turned on, check that log was successfully written and if yes (else part) write output messages on the screen
		if($this->config->debug == "true") {
			if(!$logOK) {
				return "Error! Unable to write to logfile: " . $this->config->logpath.$this->config->logfilename." => Please check File Permissions.<br>";
			} else {
				return $time." ".$meldung."<br>";
			}
		// If Debug mode is turned off, return null to hide debug messages
		} else {
			return null;
		}
	} 
}

?>
