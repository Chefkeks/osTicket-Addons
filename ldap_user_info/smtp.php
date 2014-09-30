<?php
//Inclusion of NET_SMTP package
require_once(dirname(__FILE__).'/Net/SMTP.php');

// Send report using SMTP
// Define host, sender, recipient(s), header and body of the message
$host = 'mail-server.your.domain.com';
$from = 'osTicket <osticket@your.domain.com>';
$rcpt = array('John.Doe@your.domain.com', 'Jane.Doe@your.domain.com');
$subj = "From:".$from."\nTo:".implode(",", $rcpt)."\nSubject: LDAP User Info Addon | Cronjob Report\n";

// Define username and password to authenticate at the smtp server
// Add username and password (if neccessary)
// Otherwise no username and password will be used (when your smtp server does not require authentication)
// IMPORTANT NOTE: AUTH WITH USERNAME AND PASSWORD WAS NEVER - AND I REALLY MEAN NEVER - TESTED, SO THE CODE DOES MAY NOT WORK!
$smtp_username = '';
$smtp_password = '';

// Get content from log file to fill body of message
$content = file_get_contents('ost_last_exec_user_info.log');
if (!($content == false)) { 
	$body = $content; 
	} else { 
	//$body = "Body Line 1\nBody Line 2";
	$body = "Error reading file " . $content . ".\nPlease check if file exists and permissions are correct!";
} 

// Create a new Net_SMTP object.
if (! ($smtp = new Net_SMTP($host))) {
	die("Unable to instantiate Net_SMTP object\n");
}

// Connect to the SMTP server.
if (PEAR::isError($e = $smtp->connect())) {
	die($e->getMessage() . "\n");
}

// Use username and password when server requires authentication
if (!($smtp_username == '' && $smtp_password == '')){
	//$smtp->auth('username','password');
	$smtp->auth('$smtp_username','$smtp_password');
}

// Send the 'MAIL FROM:' SMTP command.
if (PEAR::isError($smtp->mailFrom($from))) {
	die("Unable to set sender to <$from>\n");
}

// Address the message to each of the recipients.
foreach ($rcpt as $to) {
	if (PEAR::isError($res = $smtp->rcptTo($to))) {
    	die("Unable to add recipient <$to>: " . $res->getMessage() . "\n");
	}
}

// Set the body of the message.
if (PEAR::isError($smtp->data($subj . "\r\n" . $body))) {
	die("Unable to send data\n");
}

// Disconnect from the SMTP server.
$smtp->disconnect();

?>

