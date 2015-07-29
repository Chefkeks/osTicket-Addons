<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
       "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>osTicket LDAP User Information Addon</title>
</head>
<body>

<?php
// Inclusion of Net_LDAP2 package. config and functions file:
require_once 'Net/LDAP2.php';
require_once 'config.php';
require_once 'class_function.php';

// Initialization of config and functions:
$config = new GlobalConfig();
//$functions = new Functions($config, $mysqli);
$functions = new Functions($config);

// MySQL connection to osTicket database
$mysqli = new mysqli($config->mysql_host, $config->mysql_user, $config->mysql_pw, $config->mysql_db);

//Start logging
echo @date('[Y-m-d @ H:i:s]')." Execution of ".$_SERVER['PHP_SELF']." started...<br>";
echo $functions->logg("Logging started...");
echo $functions->logg("------------------------------------------------------------------------------------------------");


// The ldap configuration array:
$ldap_config = array (
    'binddn'    => $config->ldap_binddn,
    'bindpw'    => $config->ldap_bindpw,
    'basedn'    => $config->ldap_basedn,
    'host'      => ($config->ldap_host),
    'port'		=> $config->ldap_port,
    'starttls'	=> $config->ldap_tls
);

// Connecting using the configuration:
$ldap = Net_LDAP2::connect($ldap_config);

// Testing for connection error
if (Net_LDAP2::isError($ldap)) {
	echo $functions->logg('Could not connect to LDAP-server: '.$ldap->getMessage());
	echo $functions->logg("------------------------------------------------------------------------------------------------");
	echo $functions->logg("End logging. Looks like something went wrong! (>_<)");
	echo "Error executing ".$_SERVER['PHP_SELF']."<br>";
	echo @date('[Y-m-d @ H:i:s]')." Execution of ".$_SERVER['PHP_SELF']." ended...<br>";
    die();
}

// LDAP Filter
$filter = $config->ldap_filter;

// Comments are copied from the Net_LDAP2 manual...
// We define a custom searchbase here. If you pass NULL, the basedn provided
// in the Net_LDAP2 configuration will be used. This is often not what you want.
$searchbase = $ldap_config->basedn;

// Some options:
// It is a good practice to limit the requested attributes to only those you actually want to use later.
// However, note that it is faster to select unneeded attributes than refetching an entry later to just get those attributes.
$options = array(
	'scope' => 'sub',
    'attributes' => $config->ldap_attributes
);

// Perform the search!
$ldap_search = $ldap->search($searchbase, $filter, $options);

// Test for search errors:
if (Net_LDAP2::isError($ldap_search)) {
	echo $functions->logg($ldap_search->getMessage());
	echo $functions->logg("------------------------------------------------------------------------------------------------");
	echo $functions->logg("End logging. Looks like something went wrong! (>_<)");
	echo "Error executing ".$_SERVER['PHP_SELF']."<br>";
	echo @date('[Y-m-d @ H:i:s]')." Execution of ".$_SERVER['PHP_SELF']." ended...<br>";
    die();
}

// DEBUG and LOGG number of ldap entries found
echo $functions->logg("Found " . $ldap_search->count() . " ldap entries!");

// Fetching all entries
$ldap_result = $ldap_search->sorted_as_struct();

// DEBUG: Show complete array and stop after that
//print_r($ldap_result);
//die();

// DEBUG: Show single entry of array, here: Office of the first users from the array
// print_r($ldap_result[0][physicalDeliveryOfficeName][0]);

// Check for error
// If everything is ok, fetch all users with primary key sAMAaccountName
if (Net_LDAP2::isError($ldap_result)) {
	echo $functions->logg('Could not fetch entry: '.$ldap_result->getMessage());
	echo $functions->logg("------------------------------------------------------------------------------------------------");
	echo $functions->logg("End logging. Looks like something went wrong! (>_<)");
	echo "Error executing ".$_SERVER['PHP_SELF']."<br>";
	echo @date('[Y-m-d @ H:i:s]')." Execution of ".$_SERVER['PHP_SELF']." ended...<br>";
    die();
} else {
	// Fetch all users 
	$array_ad_users = array();
	for($i=0 ; $i<$ldap_search->count() ; $i++)
	{
		$array_ad_users[$ldap_result[$i]['sAMAccountName'][0]] = array_change_key_case($ldap_result[$i], CASE_LOWER); // save all users with sAMAccountName as primary key
	}
}

// DEBUG: Show array and stop afterwards
//print_r($array_ad_users);
//die();

if (!$mysqli->connect_errno) {
	// Agents
	// Check if agents shall be updated with LDAP info
	if ($config->agents == "true") {
		// Select all osTicket Agents
		$qry_ostagents = "SELECT ost_staff.username FROM ost_staff
			WHERE ost_staff.username IS NOT NULL";
			
		$res_ostagents = $mysqli->query($qry_ostagents);
		
		// DEBUG and LOGG number of osTicket agents
		echo $functions->logg("Number of osTicket agents: " . $res_ostagents->num_rows);	
	
		// Go thru every osTicket agent and modify every osTicket agents information	
		while ($row_ostagents = $res_ostagents->fetch_assoc()) {
		    // DEBUG and LOGG osTicket user which gets modified now
		    echo $functions->logg("Modifiying now the following osTicket agent: " . $row_ostagents['username']);
		    
		    // Check if osTicket agent is also an LDAP user
			if ($row_ostagents['username'] == $array_ad_users[$row_ostagents['username']]['samaccountname'][0]) {
				//DEBUG Stuff
				//echo "Agent Username:".$row_ostagents['username']."<br>";
				//echo "LDAP Username:".$array_ad_users[$row_ostagents['username']]['samaccountname'][0]."<br>";
				//echo "Phone Number:".$array_ad_users[$row_ostagents['username']]['telephonenumber'][0]."<br>";
				//echo "Mobile Number:".$array_ad_users[$row_ostagents['username']]['mobile'][0]."<br>";
				
				// Just update telephone and mobile number for agents
				// Telephonenumber
				$qry_update_ostagent_telephonenumber = "update ost_staff 
					SET ost_staff.phone='" . $array_ad_users[$row_ostagents['username']]['telephonenumber'][0] . "'
					WHERE (ost_staff.username='" . $array_ad_users[$row_ostagents['username']]['samaccountname'][0] . "')";
					
				$res_update_ostagent_detail_telephonenumber = $mysqli->query($qry_update_ostagent_telephonenumber);
				
				// Mobile Number
				$qry_update_ostagent_mobile = "update ost_staff 
					SET ost_staff.mobile='" . $array_ad_users[$row_ostagents['username']]['mobile'][0] . "'
					WHERE (ost_staff.username='" . $array_ad_users[$row_ostagents['username']]['samaccountname'][0] . "')";
					
				$res_update_ostagent_detail_mobile = $mysqli->query($qry_update_ostagent_mobile);
				
				// DEBUG and LOGG number of changed database entries for that osTicket user
				echo $functions->logg("Number of changed database entries for that agent: " . $mysqli->affected_rows);

				} else {
				// DEBUG and LOGG OsTicket Agent not in LDAP
				echo $functions->logg("osTicket Agent: ".$row_ostagents['username']." not existing in LDAP. Skipping this agent...");
			}
		}
	} else {
	echo $functions->logg("Configuration to update agents is set to " . $config->agents . ". Skipping osTicket Agents...");
	}
	// Users
	// Query additional fields from osTicket user information form (form_id=1)
	// Default form fields have ID's 1 to 4, so query greater id 4
	$qry_user_info_fields = "SELECT id,form_id,label,name FROM ost_form_field
		WHERE (ost_form_field.form_id='1' AND ost_form_field.name='phone') 
		OR (ost_form_field.form_id='1' AND ost_form_field.id>'4')";

	//OLD SQL QUERY WITHOUT PHONE:	
	//$qry_user_info_fields = "SELECT id,form_id,label,name FROM ost_form_field
	//WHERE (ost_form_field.form_id='1' AND ost_form_field.id>'4')";
	
	$res_user_info_fields = $mysqli->query($qry_user_info_fields);
	
	//Select all osTicket Users who have a username
	$qry_ostusers = "SELECT ost_user_account.username FROM ost_user
		LEFT JOIN ost_user_account on ost_user.id=ost_user_account.user_id
		WHERE ost_user_account.username IS NOT NULL";
	
	$res_ostusers = $mysqli->query($qry_ostusers);
	
	// DEBUG and LOGG number of osTicket users
	echo $functions->logg("Number of osTicket users: " . $res_ostusers->num_rows);
	
	// Go thru every osTicket user and modify every osTicket user's contact information
	while ($row_ostusers = $res_ostusers->fetch_assoc()) {
	    // DEBUG and LOGG osTicket user which gets modified now
	    echo $functions->logg("Modifiying now the following osTicket user: " . $row_ostusers['username']);
	    
	    //Just for debug...
	    //$user_cn = $array_ad_users[$row_ostusers['username']]['cn'][0];
	    //$user_mail = $array_ad_users[$row_ostusers['username']]['mail'][0];
		//$user_office = $array_ad_users[$row_ostusers['username']]['physicaldeliveryofficename'][0];
		//$user_phone = $array_ad_users[$row_ostusers['username']]['telephonenumber'][0];
		//$user_samaccountname = $array_ad_users[$row_ostusers['username']]['samaccountname'][0];
		//$user_mobile = $array_ad_users[$row_ostusers['username']]['mobile'][0];
		//print_r($array_ad_users[$row_ostusers['username']]['cn']);
		
		$logString = "User information from LDAP: ";
		foreach($config->ldap_attributes as $attribute) {
			$array_user_ldap_attribute[$attribute] = $array_ad_users[$row_ostusers['username']][$attribute][0];
			$logString = $logString."'".$attribute."'=".$array_user_ldap_attribute[$attribute]." ; ";
		}
		
		//DEBUG: Show array
		//print_r($array_user_ldap_attribute);
		
		// DEBUG and LOGG Show user information from AD
		echo $functions->logg($logString);
	       
	    // Check if osTicket user is also an LDAP user
		if ($row_ostusers['username'] == $array_user_ldap_attribute['samaccountname']) {
		    // Update LDAP Attributes from AD for the osTicket user configured in config.php
		    foreach($config->ost_contact_info_fields as $ost_contact_info_field_name => $ost_contact_info_field_ldapattr) {
		    	//Even more debug stuff... :D
		    	/*echo "Key: ".$ost_contact_info_field_name." Value: ".$ost_contact_info_field_ldapattr."<br>";
		    	echo "\$user_office: ".$user_office."<br>";
		    	echo "\$array_user_ldap_attribute['".$ost_contact_info_field_ldapattr."']: ".$array_user_ldap_attribute[$ost_contact_info_field_ldapattr]."<br>";*/
				$qry_update_ostuser_office = "update ost_user 
					LEFT JOIN ost_user_account on ost_user.id=ost_user_account.user_id
					LEFT JOIN ost_form_entry on ost_user.id=ost_form_entry.object_id
					LEFT JOIN ost_form_entry_values on ost_form_entry.id=ost_form_entry_values.entry_id
					LEFT JOIN ost_form_field on ost_form_entry_values.field_id=ost_form_field.id
					SET ost_form_entry_values.value='" . $array_user_ldap_attribute[$ost_contact_info_field_ldapattr] . "'
					WHERE (ost_form_field.name='" . $ost_contact_info_field_name . "' AND ost_user_account.username='" . $row_ostusers['username'] . "')";
					
				$res_update_ostuser_details = $mysqli->query($qry_update_ostuser_office);
			
				// DEBUG and LOGG number of changed database entries for that osTicket user
				echo $functions->logg("Number of changed database entries for that user: " . $mysqli->affected_rows . " on " . $ost_contact_info_field_name);
			}
		} else {
			// DEBUG and LOGG OsTicket User not in LDAP
			echo $functions->logg("osTicket User: ".$row_ostusers['username']." not existing in LDAP. Skipping this user...");
		}
		
		if (!($config->itdb_database == ""))
		{
			// ITDB: http://www.sivann.gr/software/itdb/
			//		 https://github.com/sivann/itdb
			//
			// TODO: Move ITDB code to own addon or at least an option to NOT use ITDB... for LDAP only...
			// 		 should work anyway... even without ITDB config specified, just not "clean"
			//
			// Connect to ITDB SQLite3 database		
			// Check if database exists and read out information
			try {
		        // connect to your database
		        $db = new $config->itdb_connection($config->itdb_database,$config->itdb_open_mode);
		        
		        // Get ITDB Database-ID, label and hostname for each user
				//$qry_ostuser_computer = ('SELECT id,label,dnsname FROM items WHERE label =\''. $array_user_ldap_attribute['cn'] ." (". $array_user_ldap_attribute['samaccountname'] . ")" . '\';');
				$qry_ostuser_computer = ('SELECT id,label,dnsname FROM items WHERE dnsname IS NOT "" AND label =\''. $array_user_ldap_attribute['cn'] ." (". $array_user_ldap_attribute['samaccountname'] . ")" . '\';');
				$res_ostuser_computer = $db->query($qry_ostuser_computer);
				
				//DEBUG and LOGG Query and result performend on ITDB
				echo $functions->logg("Query performed on ITDB: " . $qry_ostuser_computer);
				echo $functions->logg("Result of the query: " . $res_ostuser_computer->numColumns()." Columns found!");
				
				// Create 2 arrays and fill with one with computers/hostnames and the other with ITDB database IDs (in case a user has multiple computers)
				$array_computers = array();
				$array_computer_ids = array();
				while($row_ostuser_computer = $res_ostuser_computer->fetchArray() ) {
					$array_computers[] = $row_ostuser_computer['dnsname'];
					$array_computer_ids[] = $row_ostuser_computer['id'];
					//DEBUG and LOGG computers assigned to the user
					echo $functions->logg("ID: ".$row_ostuser_computer['id']);
					echo $functions->logg("User: ".$row_ostuser_computer['label']);
					echo $functions->logg("Hostname: ".$row_ostuser_computer['dnsname']);
				}
				
				// Create Link to ITDB database and Software deployment web interface
				$users_computers = "";
				for($i=0 ; $i < count($array_computers) ; $i++) {
					// Show hostname, add links to software deployment web ui and ITDB behind the name
					// href_1: Use computer name for link creation
					// href_2: Use ITDB database ID for link creation
					$users_computers = $users_computers."".$array_computers[$i]." ";
					$users_computers = $users_computers."<a href=" . $config->href_1 . $array_computers[$i] . ">" . $config->href_text_1 . "</a>" . " ";
                                        $users_computers = $users_computers."<a href=" . $config->href_2 . $array_computer_ids[$i] . ">" . $config->href_text_2 . "</a>" . "<br> ";	
				}
				# Update computers for the osTicket user and write them into database				
				$qry_update_ostuser_computer = "update ost_user 
				LEFT JOIN ost_user_account on ost_user.id=ost_user_account.user_id 
				LEFT JOIN ost_form_entry on ost_user.id=ost_form_entry.object_id 
				LEFT JOIN ost_form_entry_values on ost_form_entry.id=ost_form_entry_values.entry_id 
				LEFT JOIN ost_form_field on ost_form_entry_values.field_id=ost_form_field.id 
				SET ost_form_entry_values.value='".$users_computers."' 
				WHERE (ost_form_field.name='" . $config->ost_contact_info_special_fields[0] . "' AND ost_user_account.username='" . $row_ostusers['username'] . "')";
									
				$res_update_ostuser_computer = $mysqli->query($qry_update_ostuser_computer);
				
				// DEBUG and LOGG Show number of affected database rows
				echo $functions->logg("Number of changed database entries for that user: " . $mysqli->affected_rows . " on ".$config->ost_contact_info_special_fields[0]);
			}
			catch (Exception $exception) {
				// Show error if database connection fails
				echo $functions->logg("Error! Check database file permissions or connection to ITDB database!");
				echo $functions->logg("Exception Message: ".$exception->getMessage());
				echo $functions->logg("------------------------------------------------------------------------------------------------");
				echo $functions->logg("End logging. Looks like something went wrong! (>_<)");
				echo "Error executing ".$_SERVER['PHP_SELF']."<br>";
				echo @date('[Y-m-d @ H:i:s]')." Execution of ".$_SERVER['PHP_SELF']." ended...<br>";
		    	die();
			}
			
			// Close ITDB SQLite3 database connection
			$db->close();
		}
		
		// DEBUG and LOGG End logging for that user
		echo $functions->logg("Finished with the user: ".$array_user_ldap_attribute['cn']);
		
		/*
		// DEBUG:	
		// Select of all osTicket users with ID, name, username, FormField-Label, FormField-Variable to that Label und the value of it
		$qry_ostuser_details = "select ost_user.id,ost_user.name,ost_user_account.username,ost_form_field.label,ost_form_field.name,ost_form_entry_values.value from ost_user 
			LEFT JOIN ost_user_account on ost_user.id=ost_user_account.user_id 
			LEFT JOIN ost_form_entry on ost_user.id=ost_form_entry.object_id  
			LEFT JOIN ost_form_entry_values on ost_form_entry.id=ost_form_entry_values.entry_id 
			LEFT JOIN ost_form_field on ost_form_entry_values.field_id=ost_form_field.id 
			WHERE ((ost_form_field.name='contact_info_office' OR ost_form_field.name='contact_info_computername') AND ost_user_account.username='" . $row_ostusers['username'] . "')";
		*/
	
		/*
		// DEBUG:	
		// Show FormField-Labels und their values, e.g. computername: computer123, office: Room 123, and so on...
		$res_ostuser_details = $mysqli->query($qry_ostuser_details);
		while ($row_ostuser_details = $res_ostuser_details->fetch_assoc()) {
		    //echo " id = " . $row_ostuser_details['id'] . "<br>";
		    //echo " Name = " . $row_ostuser_details['username'] . "<br>";
		    //echo " Variablenname = " . $row_ostuser_details['name'] . "<br>";
		    echo $row_ostuser_details['label'] . ": " . $row_ostuser_details['value'] . "<br>";
		}
		*/
	}
} else {
	echo $functions->logg("Failed to connect to OsTicket-Database: " . $mysqli->connect_error);
	echo "Error executing ".$_SERVER['PHP_SELF']."<br>";
	echo @date('[Y-m-d @ H:i:s]')." Execution of ".$_SERVER['PHP_SELF']." ended...<br>";
	die();
}
// DEBUG and LOGG End logging
echo $functions->logg("------------------------------------------------------------------------------------------------");
echo $functions->logg("End logging. We hope everything went fine! \(^_^)/");
echo @date('[Y-m-d @ H:i:s]')." Execution of ".$_SERVER['PHP_SELF']." ended successfully!<br>";

?>
</body>
</html>


