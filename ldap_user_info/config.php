<?php

class GlobalConfig {
	// Version and Info
	public $version = "v0.4";
	
	// DEBUG and LOGG
    public $debug = "true";
	public $logpath = "/var/log/apache2/";
	public $logfilename = "ost_update_user_info.log";  
	public $loglastexec = "ost_last_exec_user_info.log";

	//osTicket MySQL Database
    public $mysql_host = "localhost";
    public $mysql_db = "osticket";
    public $mysql_user = "osticket-user";
    public $mysql_pw = "osticket-pass";
    
    // Net LDAP2 Connection
    public $ldap_host = 'dc.your.domain.com, another-dc.your.domain.com, one-more-dc.your.domain.com';
    public $ldap_port = '3268'; //hide from config_ui to guarantee functionality
    public $ldap_binddn = 'cn=ldap_bind_user,cn=users,dc=your,dc=domain,dc=com';
    public $ldap_bindpw = 'ldap_bind_user_pass';
    public $ldap_basedn = 'dc=your,dc=domain,dc=com';
    public $ldap_tls = 'true';
    public $ldap_attributes = array('samaccountname','cn','telephonenumber','mobile');
    public $ldap_filter = '(&(sAMAccountType=805306368)(!(userAccountControl=514))(!(userAccountControl=66050))(mail=*))'; //hide from config_ui to guarantee functionality
   
    // LDAP filter explained in detail:
    // Normal User Accounts:								sAMAccountType=805306368
    // NOT Disabled Accounts:								!(userAccountControl=514)
    // NOT Disabled Accounts with password never expire: 	!(userAccountControl=66050)
    // Accounts with mail address:							mail=*

    
    // osTicket Agents and User Contact information field variables
   	public $agents = 'true';
   	public $ost_contact_info_fields = array('phone' => 'telephonenumber');
   	public $ost_contact_info_special_fields = array('');

   	
   	// ITDB Database
    public $itdb_connection = "SQLite3"; //hide from config_ui to guarantee functionality
    public $itdb_open_mode = SQLITE3_OPEN_READONLY; //hide from config_ui to guarantee functionality
    public $itdb_database = "";
    
    // Computername Links
    public $href_1 = "";
    public $href_2 = "";
    public $href_text_1 = "";
    public $href_text_2 = "";
}
?>
