<?php
// Execute update file
$cron_ldap = shell_exec('php update_user_info.php');
$cron_smtp = shell_exec('php smtp.php');

//TODO: Write result into ost_syslog :D
//osTicket Logg:
//SELECT log_id FROM `ost_syslog` ORDER BY `log_id` DESC LIMIT 1;
//SELECT * FROM `ost_syslog` ORDER BY `log_id` DESC LIMIT 100;
	
?>