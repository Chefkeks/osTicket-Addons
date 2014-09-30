<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
       "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>osTicket LDAP User Information Addon</title>
</head>
<body>

<?php
require_once 'class_function.php';

// Initialization of config and functions:
$config = new GlobalConfig();
//$functions = new Functions($config, $mysqli);
$functions = new Functions($config);

// MySQL connection to osTicket database
$mysqli = new mysqli($config->mysql_host, $config->mysql_user, $config->mysql_pw, $config->mysql_db);

?>
<h1>osTicket LDAP User Information Addon <?php echo $config->version ?></h1>

<h3>Configuration:
	<br>
	Please enter the data to connect to your osTicket database and LDAP.<br><br>
<span style="color:red">*</span> = Required config field</h3>
<form action="config_ui.php" method="post">
<table style="text-align:left">
	<th><em><strong><br>osTicket MySQL Database</strong></em></th>
	<tr>
		<td>MySQL Hostname</td>
		<td><input type='text' name='mysql_host' size='30' value='<?php echo ((isset($_POST['mysql_host']) && !empty($_POST['mysql_host'])) ? $_POST['mysql_host'] : $config->mysql_host)?>' /><span style="color:red">*</span></td>
	</tr>
	<tr>
		<td>MySQL Database</td>
		<td><input type='text' name='mysql_db' size='30' value='<?php echo ((isset($_POST['mysql_db']) && !empty($_POST['mysql_db'])) ? $_POST['mysql_db'] : $config->mysql_db)?>' /><span style="color:red">*</span></td>
	</tr>
	<tr>
		<td>MySQL User</td>
		<td><input type='text' name='mysql_user' size='30' value='<?php echo ((isset($_POST['mysql_user']) && !empty($_POST['mysql_user'])) ? $_POST['mysql_user'] : $config->mysql_user)?>' /><span style="color:red">*</span></td>
	</tr>
	<tr>
		<td>MySQL Password</td>
		<td><input type='password' name='mysql_pw' size='30' value='<?php echo ((isset($_POST['mysql_pw']) && !empty($_POST['mysql_pw'])) ? $_POST['mysql_pw'] : $config->mysql_pw)?>' /><span style="color:red">*</span></td>
	</tr>
	<th><em><strong><br>LDAP Settings</strong></em></th>
	<tr>
		<td>LDAP Hostname(s)</td>
		<td><input type='text' name='ldap_host' size='75' value='<?php echo ((isset($_POST['ldap_host']) && !empty($_POST['ldap_host'])) ? $_POST['ldap_host'] : $config->ldap_host)?>' /><span style="color:red">*</span></td>
	</tr>
	<tr>
		<td>LDAP Bind Username</td>
		<td><input type='text' name='ldap_binddn' size='75' value='<?php echo ((isset($_POST['ldap_binddn']) && !empty($_POST['ldap_binddn'])) ? $_POST['ldap_binddn'] : $config->ldap_binddn)?>' /><span style="color:red">*</span></td>
	</tr>
	<tr>
		<td>LDAP Bind Password </td>
		<td><input type='password' name='ldap_bindpw' size='75' value='<?php echo ((isset($_POST['ldap_bindpw']) && !empty($_POST['ldap_bindpw'])) ? $_POST['ldap_bindpw'] : $config->ldap_bindpw)?>' /><span style="color:red">*</span></td>
	</tr>
	<tr>
		<td>LDAP Searchbase</td>
		<td><input type='text' name='ldap_basedn' size='75' value='<?php echo ((isset($_POST['ldap_basedn']) && !empty($_POST['ldap_basedn'])) ? $_POST['ldap_basedn'] : $config->ldap_basedn)?>' /><span style="color:red">*</span></td>
	</tr>
	<tr>
		<td>LDAP Attributes</td>
		<?php 
		$ldap_attributes = "";
		foreach (array_values($config->ldap_attributes) as $ldap_attribute)
		{
			if($ldap_attributes == "") {
				$ldap_attributes = $ldap_attribute;
			} else {
				$ldap_attributes = $ldap_attributes.",".$ldap_attribute;
			}
		}
		?>
		<td><input type='text' name='ldap_attributes' size='75' value='<?php echo ((isset($_POST['ldap_attributes']) && !empty($_POST['ldap_attributes'])) ? $_POST['ldap_attributes'] : $ldap_attributes)?>' /><span style="color:red">*</span></td>
	</tr>
	<tr>
		<td>LDAP Usage of TLS</td>
		<td>		
		<select name="ldap_tls">
    	<option value="true" <?php if(((isset($_POST['ldap_tls']) && !empty($_POST['ldap_tls'])) ? $_POST['ldap_tls'] : $config->ldap_tls) == 'true'){ echo ' selected="selected"'; } ?>>YES</option>
		<option value="false" <?php if(((isset($_POST['ldap_tls']) && !empty($_POST['ldap_tls'])) ? $_POST['ldap_tls'] : $config->ldap_tls) == 'false'){ echo ' selected="selected"'; } ?>>NO</option>
		</select>
		<span style="color:red">*</span>
		</td>
	</tr>
	<th><em><strong><br>Debugging/Logging</strong></em></th>
	<tr>
		<td>Debug</td>
		<td>
		<select name="debug">
		<option value="true" <?php if(((isset($_POST['debug']) && !empty($_POST['debug'])) ? $_POST['debug'] : $config->debug) == 'true'){ echo ' selected="selected"'; } ?>>ON</option>
		<option value="false" <?php if(((isset($_POST['debug']) && !empty($_POST['debug'])) ? $_POST['debug'] : $config->debug) == 'false'){ echo ' selected="selected"'; } ?>>OFF</option>
		</select>
		<span style="color:red">*</span>
		</td>
	</tr>
	<tr>
		<td>Path to logfile</td>
		<td><input type='text' name='logpath' size='30' value='<?php echo ((isset($_POST['logpath']) && !empty($_POST['logpath'])) ? $_POST['logpath'] : $config->logpath)?>' /><span style="color:red">*</span></td>
	</tr>
	<tr>
		<td>Filename of logfile</td>
		<td><input type='text' name='logfilename' size='30' value='<?php echo ((isset($_POST['logfilename']) && !empty($_POST['logfilename'])) ? $_POST['logfilename'] : $config->logfilename)?>' /><span style="color:red">*</span></td>
	</tr>
</table>
<table style="text-align:left">
<th><em><strong><br>ITDB Settings</strong></em></th>
	<tr>
		<td>Database File (itdb.db)</td>
		<td><input type='text' name='itdb_database' size='30' value='<?php echo ((isset($_POST['itdb_database']) && !empty($_POST['itdb_database'])) ? $_POST['itdb_database'] : $config->itdb_database)?>' /></td>	
	</tr>
	<tr>
		<td>osTicket Field for ITDB PCs</td>
		<?php 
		$special_fields = "";
		foreach (array_values($config->ost_contact_info_special_fields) as $special_field)
		{
			if($special_fields == "") {
				$special_fields = $special_field;
			} else {
				$special_fields = $special_fields.",".$special_field;
			}
		}
		?>
		<td><input type='text' name='special_fields' size='30' value='<?php echo ((isset($_POST['special_fields']) && !empty($_POST['special_fields'])) ? $_POST['special_fields'] : $special_fields)?>' /></td>
	</tr>
	<tr>
		<td>Link #1 (Hostname)</td>
		<td><input type='text' name='href_1' size='100' value='<?php echo ((isset($_POST['href_1']) && !empty($_POST['href_1'])) ? $_POST['href_1'] : $config->href_1)?>' /></td>	
	</tr>
	<tr>
		<td>Link-Description #1</td>
		<td><input type='text' name='href_text_1' size='30' value='<?php echo ((isset($_POST['href_text_1']) && !empty($_POST['href_text_1'])) ? $_POST['href_text_1'] : $config->href_text_1)?>' /></td>	
	</tr>
	<tr>
		<td>Link #2 (ITDB ID)</td>
		<td><input type='text' name='href_2' size='100' value='<?php echo ((isset($_POST['href_2']) && !empty($_POST['href_2'])) ? $_POST['href_2'] : $config->href_2)?>' /></td>	
	</tr>
	<tr>
		<td>Link-Description #2</td>
		<td><input type='text' name='href_text_2' size='30' value='<?php echo ((isset($_POST['href_text_2']) && !empty($_POST['href_text_2'])) ? $_POST['href_text_2'] : $config->href_text_2)?>' /></td>	
	</tr>
</table>

<table style="text-align:left">
<th><em><strong><br>osTicket Agents Info</strong></em></th>
	<tr>
		<td>Update Agents phone/mobile numbers from LDAP</td>
		<td>
		<select name="agents">
		<option value="true" <?php if(((isset($_POST['agents']) && !empty($_POST['agents'])) ? $_POST['agents'] : $config->agents) == 'true'){ echo ' selected="selected"'; } ?>>ON</option>
		<option value="false" <?php if(((isset($_POST['agents']) && !empty($_POST['agents'])) ? $_POST['agents'] : $config->agents) == 'false'){ echo ' selected="selected"'; } ?>>OFF</option>
		</select>
		<span style="color:red">*</span>
		</td>
	</tr>
</table>


<table style="text-align:left">
	<th><em><strong><br>osTicket User Info Fields</strong></em></th>
	<?php
if (!$mysqli->connect_errno) {
	// TODO: Same query as in manage_custom_user_info.php, maybe move to class_functions.php?!
	// Query additional fields from osTicket user information form (form_id=1)
	// Default form fields have ID's 1 to 4, so query greater id 4
	$qry_user_info_fields = "SELECT id,form_id,label,name FROM ost_form_field
		WHERE (ost_form_field.form_id='1' AND ost_form_field.name='phone') 
		OR (ost_form_field.form_id='1' AND ost_form_field.id>'4')";

	//OLD SQL QUERY WITHOUT PHONE:	
	//$qry_user_info_fields = "SELECT id,form_id,label,name FROM ost_form_field
	//WHERE (ost_form_field.form_id='1' AND ost_form_field.id>'4')";
	
	$res_user_info_fields = $mysqli->query($qry_user_info_fields);
	
	// Create Array for later user
	$array_field_names = array();
	
	// Create a table to select the additional fields and the values from LDAP that should be filled into them
	// Show as much dropdown lists as additional fields are in the osTicket user information form
	// Fetch field names
	while ($row_user_info_fields = $res_user_info_fields->fetch_assoc()) {
		$field_id = $row_user_info_fields['id'];
		$field_form_id = $row_user_info_fields['form_id'];
		$field_label = $row_user_info_fields['label'];
		$field_name = $row_user_info_fields['name'];
		if(!in_array($field_name, $config->ost_contact_info_special_fields)) {
			$array_field_names[] = $field_name;
			echo "<tr><td><a>Fill field<br></a>";
			//Some leftovers from code-rewrite and some testings... left for future experiments
			//$dropdown_fields=$dropdown_fields . "<option value='" . $field_name . "'>" . $field_name . "</option>";
			//echo "</td><td><a>with LDAP attribute(s):<br></a><select name='ldap_attributes_".$count."' multiple='multiple'>";
			echo "<input type='text' value='" . $field_name . "' readonly='true'>";
			echo "</td><td><a>with LDAP attribute:<br></a><select name='ldap_attributes_".$field_name."'>";
			echo "<option value=''>Select LDAP attribute</option>";
			// Put ldap attributes in dropdown list
			foreach (array_values($config->ldap_attributes) as $ldap_attribute)
			{
				echo "<option ";
					if(isset($_POST['ldap_attributes_'.$field_name.'']) && !empty($_POST['ldap_attributes_'.$field_name.''])) {
						if($_POST['ldap_attributes_'.$field_name.''] == $ldap_attribute) {
							echo "selected ";
						}
					} else if($config->ost_contact_info_fields[$field_name] == $ldap_attribute) {
						echo "selected ";
					}
				echo "value=" . $ldap_attribute . ">" . $ldap_attribute . "</option>";
			}
			echo "</td></tr></select>";
		}
	}
} else {
	echo "<tr><td><strong><br>Failed to connect to OsTicket-Database: " . $mysqli->connect_error . "</strong></td></tr>";
}
?>
</table>
<table style="margin-top:20px">
	<tr>
		<td><input type="submit" name="submit" value="Save config"/></td>
		<td width="75"></td>
		<td><input type="submit" name="execute" value="Execute Update & send logfile"/></td>
		<td width="32.5"></td>
		<td><input type="submit" name="smtp" value="Only send logfile"/></td>
	</tr>
</table>
<?php
if(isset($_POST['submit'])) {
	if(!empty($_POST['mysql_host']) &&
		!empty($_POST['mysql_db']) &&
		!empty($_POST['mysql_user']) &&
		!empty($_POST['mysql_pw']) &&
		!empty($_POST['ldap_host']) &&
		!empty($_POST['ldap_binddn']) &&
		!empty($_POST['ldap_bindpw']) &&
		!empty($_POST['ldap_basedn']) &&
		!empty($_POST['ldap_attributes']) &&
		!empty($_POST['ldap_tls']) &&
		!empty($_POST['agents']) &&
		!empty($_POST['debug']) &&
		!empty($_POST['logpath']) &&
		!empty($_POST['logfilename'])) {
		
		$oldconfig = file_get_contents("config.php");
		$config = $oldconfig;
		$array_field_names[] = NULL;	//If the counter below in the switch/case is too high, value is then changed to NULL
		$array_field_names_counter = 0;	//Counter for fieldnames-array
		$contact_info_fields_string = "public \$ost_contact_info_fields = array(";
		foreach ($_POST as $key => $value) {
			//Debugging echo
			//echo "Value: ".$value." with Key: ".$key."<br>";
			switch ($key) {
			case "submit":
				break;
			case "ldap_attributes":
				$valueString = "";
				$array_ldap_attributes = explode(",",$value);
				$array_ldap_attributes = array_unique($array_ldap_attributes);
				foreach ($array_ldap_attributes as $attribute) {
					if($attribute != "samaccountname" && $attribute != "cn" && $attribute != "telephonenumber" && $attribute != "mobile") {
						$valueString = $valueString . "'" . $attribute."',";
					}
				}
				//Debugging echo
				//echo $key . ' has the value of ' . $value . "<br>";
				//Check if valuestring is empty, neccessary since it will break the config with one comma too much at the end of the ldap_attributes: samaccountname,cn,sn, <--- comma at the end breaks config
				if ($valueString == "")	{
   					$newconfig = preg_replace('/public \$'.$key.' = array\(\'samaccountname\',\'cn\',\'telephonenumber\',\'mobile\'(,["\'](.+)?["\'])?\)/', 'public $'.$key.' = array(\'samaccountname\',\'cn\',\'telephonenumber\',\'mobile\''.substr($valueString, 0, -1).')', $config);
			   	} else {
			   		$newconfig = preg_replace('/public \$'.$key.' = array\(\'samaccountname\',\'cn\',\'telephonenumber\',\'mobile\'(,["\'](.+)?["\'])?\)/', 'public $'.$key.' = array(\'samaccountname\',\'cn\',\'telephonenumber\',\'mobile\','.substr($valueString, 0, -1).')', $config);
			   	}
				$config = $newconfig;
				break;
			case "special_fields":
				$valueString = "";
				$array_special_fields = explode(",",$value);
				$array_special_fields = array_unique($array_special_fields);
				foreach ($array_special_fields as $attribute) {
					$valueString = $valueString . "'" . $attribute."',";
				}
				//Debugging echo
				//echo $key . ' has the value of ' . $value . "<br>";
   				//BEFORE regex re-visiting: $newconfig = preg_replace('/(public \$){1}(ost_contact_info_special_fields = array\(){1}["\'].+["\']\)/', 'public $ost_contact_info_special_fields = array('.substr($valueString, 0, -1).')', $config);
   				$newconfig = preg_replace('/(public \$){1}(ost_contact_info_special_fields = array\(){1}(.+)?(\))/', 'public $ost_contact_info_special_fields = array('.substr($valueString, 0, -1).')', $config);
				$config = $newconfig;
				break;
			case "ldap_attributes_".$array_field_names[$array_field_names_counter]."":	
				//Debugging echo
				//echo "ldap_attributes_".$array_field_names[$array_field_names_counter]." has the value: ".$value."<br>";
				if(!empty($_POST['ldap_attributes_'.$array_field_names[$array_field_names_counter].'']))
				{
   					$contact_info_fields_string = $contact_info_fields_string . "'" .$array_field_names[$array_field_names_counter]."' => '".$value."',";
					$array_field_names_counter++;
   				} else {
					$array_field_names_counter++;
				}
				if($array_field_names_counter == (count($array_field_names) - 1)) {
					if(substr($contact_info_fields_string, -1) == "(") {
						$contact_info_fields_string = $contact_info_fields_string . ","; //If no fields are entered, add a character, so substr(-1) works below / can cut off the last char :D :)
					}
					//echo "<br>".substr($contact_info_fields_string, 0, -1).")<br>";
   					//BEFORE regex re-visiting: $newconfig = preg_replace('/(public \$)+(ost_contact_info_fields = array\()+(["\']).+(["\'])+([ => \'])+(["\']).+(["\'])\)/', substr($contact_info_fields_string, 0, -1).')', $config);
   					$newconfig = preg_replace('/(public \$){1}(ost_contact_info_fields = array\(){1}(.+)?(\))/', substr($contact_info_fields_string, 0, -1).')', $config);
   					$config = $newconfig;
   				}
				break;
			default:
				//Debugging echo
				//echo "Default: ".$key . ' has the value of ' . $value . "<br>";
   				$newconfig = preg_replace('/(public \$){1}('.$key.' = ){1}(["\'])(.+)?(["\'])/', 'public $'.$key.' = ${3}'.$value.'${3}', $config);
   				$config = $newconfig;
				break;
			}
		}
   		$error_writing_config = file_put_contents("config.php",$config);
   		if($error_writing_config == false) {
			?>
			<script language="javascript">  
			var error_permissions = alert("Error writing config file.\nPlease check file permissions!");  
			</script>
			<?php
   		} else {
   			//Before javascript:
   			//echo "Config successfully saved! ".$error_writing_config." Bytes written...";
   			//JavaScript: Reload page from server and not from local cache by using 'true' in location.reload(true)
			?>
			<script language="javascript">  
			var reload = confirm("Config successfully saved! <?php echo $error_writing_config; ?> Bytes written.\n\nPress 'OK' to reload page. Verify config afterwards!");  
			if(reload)
			location.reload(true)
			</script>
			<?php
   		}
	} else {
		?>
		<script language="javascript">  
		var error_missing_data = alert("Configuration is not complete!\nPlease insert the missing data!");  
		</script>
		<?php
	}
}

if(isset($_POST['execute'])) {
	$output = shell_exec('php update_user_info.php');
	echo $output;
	$outexecsmtp = shell_exec('php smtp.php');
	echo @date('[Y-m-d @ H:i:s]')." Logfile sent, at least we hope so... Check your inbox now!<br>";
	echo $outexecsmtp;
}

	//shell_exec('php smtp.php');
	
if(isset($_POST['smtp'])) {
	$outsmtp = shell_exec('php smtp.php');
	echo @date('[Y-m-d @ H:i:s]')." Logfile sent, at least we hope so... Check your inbox now!<br>";
	echo $outsmtp;
}

?>
</form>
</body>
</html>
