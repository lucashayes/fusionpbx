<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2012
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";
if (permission_exists("user_account_settings_view")) {
	//access granted
}
else {
	echo "access denied";
	return;
}

//get data from the db
	if (strlen($_REQUEST["id"])> 0) {
		$id = $_REQUEST["id"];
	}
	else {
		if (strlen($_SESSION["username"]) > 0) {
			$username = $_SESSION["username"];
		}
	}

//get the username from v_users
	$sql = "";
	$sql .= "select * from v_users ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and user_uuid = '$id' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	foreach ($result as &$row) {
		$username = $row["username"];
		break; //limit to 1 row
	}
	unset ($prep_statement);

//required to be a superadmin to update an account that is a member of the superadmin group
	$superadmin_list = superadmin_list($db);
	if (if_superadmin($superadmin_list, $username)) {
		if (!if_group("superadmin")) { 
			echo "access denied";
			return;
		}
	}

if (count($_POST)>0 && $_POST["persistform"] != "1") {
	$id = $_REQUEST["id"];
	$password = check_str($_POST["password"]);
	$confirm_password = check_str($_POST["confirm_password"]);
	$user_status = check_str($_POST["user_status"]);
	$user_template_name = check_str($_POST["user_template_name"]);
	$user_time_zone = check_str($_POST["user_time_zone"]);
	$user_email = check_str($_POST["user_email"]);
	$group_member = check_str($_POST["group_member"]);

	//if (strlen($password) == 0) { $msgerror .= "Password cannot be blank.<br>\n"; }
	if (strlen($username) == 0) { $msgerror .= "Please provide the username.<br>\n"; }
	if ($password != $confirm_password) { $msgerror .= "Passwords did not match.<br>\n"; }
	//if (strlen($user_email) == 0) { $msgerror .= "Please provide an email.<br>\n"; }
	//if (strlen($user_time_zone) == 0) { $msgerror .= "Please provide an time zone.<br>\n"; }

	if (strlen($msgerror) > 0) {
		require_once "includes/header.php";
		echo "<div align='center'>";
		echo "<table><tr><td>";
		echo $msgerror;
		echo "</td></tr></table>";
		echo "<br />\n";
		require_once "includes/persistform.php";
		echo persistform($_POST);
		echo "</div>";
		require_once "includes/footer.php";
		return;
	}

	//if the template has not been assigned by the superadmin
		if (strlen($_SESSION['domain']['template']['name']) == 0) {
			//set the session theme for the active user
			if ($_SESSION["username"] == $username) {
				$_SESSION['domain']['template']['name'] = $user_template_name;
			}
		}

	//sql update
		$sql  = "update v_users set ";
		if (if_group("admin") && strlen($_POST["username"])> 0) {
			$sql .= "username = '$username', ";
		}
		if (strlen($password) > 0 && $confirm_password == $password) {
			//salt used with the password to create a one way hash
				$salt = generate_password('20', '4');
			//set the password
				$sql .= "password = '".md5($salt.$password)."', ";
				$sql .= "salt = '".$salt."', ";
		}
		$sql .= "user_status = '$user_status', ";
//		$sql .= "user_template_name = '$user_template_name', ";
		$sql .= "user_time_zone = '$user_time_zone', ";
		$sql .= "user_email = '$user_email' ";
		if (strlen($id)> 0) {
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and user_uuid = '$id' ";
		}
		else {
			$sql .= "where domain_uuid = '$domain_uuid' ";
			$sql .= "and username = '$username' ";
		}
		if (permission_exists("user_account_settings_edit")) {
			$count = $db->exec(check_sql($sql));
		}

	//update the user_status
		$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
		$switch_cmd .= "callcenter_config agent set status ".$username."@".$domain_name." '".$user_status."'";
		$switch_result = event_socket_request($fp, 'api '.$switch_cmd);

	//update the user state
		$cmd = "api callcenter_config agent set state ".$username."@".$domain_name." Waiting";
		$response = event_socket_request($fp, $cmd);

	//clear the template so it will rebuild in case the template was changed
		$_SESSION["template_content"] = '';

	//redirect the browser
		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=".PROJECT_PATH."/\">\n";
		echo "<div align='center'>Update Complete</div>";
		require_once "includes/footer.php";
		return;
}
else {

	$sql = "";
	$sql .= "select * from v_users ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and username = '$username' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	foreach ($result as &$row) {
		if (if_group("admin")) {
			$username = $row["username"];
		}
		$password = $row["password"];
		$user_email = $row["user_email"];
		$user_status = $row["user_status"];
		$user_template_name = $row["user_template_name"];
		$user_time_zone = $row["user_time_zone"];
		break; //limit to 1 row
	}

	//get the groups the user is a member of
	//group_members function defined in config.php
	$group_members = group_members($db, $username);
}

//include the header
	require_once "includes/header.php";

//show the content
	$table_width ='width="100%"';
	echo "<form method='post' action=''>";
	echo "<br />\n";

	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr>\n";
	echo "<td>\n";

	echo "<table $table_width cellpadding='3' cellspacing='0' border='0'>";
	echo "<td align='left' width='90%' nowrap><b>Account Settings</b></td>\n";
	echo "<td nowrap='nowrap'>\n";
	echo "	<input type='submit' name='submit' class='btn' value='Save'>";
	echo "	<input type='button' class='btn' onclick=\"window.location='index.php'\" value='Back'>";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' colspan='2'>\n";
	echo "	Edit account information. \n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<br />\n";

	echo "<table $table_width cellpadding='6' cellspacing='0' border='0'>";
	echo "<tr>\n";
	echo "	<th class='th' colspan='2' align='left'>User Info</th>\n";
	echo "</tr>\n";

	echo "	<tr>";
	echo "		<td width='30%' class='vncellreq'>Username:</td>";
	echo "		<td width='70%' class='vtable'>$username</td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td class='vncell'>Password:</td>";
	echo "		<td class='vtable'><input type='password' autocomplete='off' class='formfld' name='password' value=\"\"></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncell'>Confirm Password:</td>";
	echo "		<td class='vtable'><input type='password' autocomplete='off' class='formfld' name='confirm_password' value=\"\"></td>";
	echo "	</tr>";

	//echo "	<tr>";
	//echo "		<td class='vncell'>First Name:</td>";
	//echo "		<td class='vtable'><input type='text' class='formfld' name='user_first_name' value=\"$user_first_name\"></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td class='vncell'>Last Name:</td>";
	//echo "		<td class='vtable'><input type='text' class='formfld' name='user_last_name' value=\"$user_last_name\"></td>";
	//echo "	</tr>";
	//echo "	<tr>";
	//echo "		<td class='vncell'>Company Name:</td>";
	//echo "		<td class='vtable'><input type='text' class='formfld' name='user_company_name' value=\"$user_company_name\"></td>";
	//echo "	</tr>";





	echo "		</td>";
	echo "	</tr>";
	echo "</table>";
	echo "<br>";
	echo "<br>";

	echo "<table $table_width cellpadding='6' cellspacing='0'>";
	echo "	<tr>\n";
	echo "	<th class='th' colspan='2' align='left'>Additional Info</th>\n";
	echo "	</tr>\n";

	echo "	<tr>";
	echo "		<td width='30%' class='vncell'>Email:</td>";
	echo "		<td width='70%' class='vtable'><input type='text' class='formfld' name='user_email' value=\"$user_email\"></td>";
	echo "	</tr>";
	if ($_SESSION['user_status_display'] == "false") {
		//hide the user_status when it is set to false
	}
	else {
		echo "	<tr>\n";
		echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
		echo "		Status:\n";
		echo "	</td>\n";
		echo "	<td class=\"vtable\">\n";
		$cmd = "'/app/calls_active/v_calls_exec.php?cmd=callcenter_config+agent+set+status+".$_SESSION['username']."@".$domain_name."+'+this.value";
		echo "		<select id='user_status' name='user_status' class='formfld' style='' onchange=\"send_cmd($cmd);\">\n";
		echo "		<option value=''></option>\n";
		if ($user_status == "Available") {
			echo "		<option value='Available' selected='selected'>Available</option>\n";
		}
		else {
			echo "		<option value='Available'>Available</option>\n";
		}
		if ($user_status == "Available (On Demand)") {
			echo "		<option value='Available (On Demand)' selected='selected'>Available (On Demand)</option>\n";
		}
		else {
			echo "		<option value='Available (On Demand)'>Available (On Demand)</option>\n";
		}
		if ($user_status == "Logged Out") {
			echo "		<option value='Logged Out' selected='selected'>Logged Out</option>\n";
		}
		else {
			echo "		<option value='Logged Out'>Logged Out</option>\n";
		}
		if ($user_status == "On Break") {
			echo "		<option value='On Break' selected='selected'>On Break</option>\n";
		}
		else {
			echo "		<option value='On Break'>On Break</option>\n";
		}
		if ($user_status == "Do Not Disturb") {
			echo "		<option value='Do Not Disturb' selected='selected'>Do Not Disturb</option>\n";
		}
		else {
			echo "		<option value='Do Not Disturb'>Do Not Disturb</option>\n";
		}
		echo "		</select>\n";
		echo "		<br />\n";
		echo "		Select a the user status.<br />\n";
		echo "	</td>\n";
		echo "	</tr>\n";
	}

	//if the template has not been assigned by the superadmin
		/*
		if (strlen($_SESSION['domain']['template']['name']) == 0) {
			echo "	<tr>\n";
			echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
			echo "		Template: \n";
			echo "	</td>\n";
			echo "	<td class=\"vtable\">\n";
			echo "		<select id='user_template_name' name='user_template_name' class='formfld' style=''>\n";
			echo "		<option value=''></option>\n";
			$theme_dir = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/themes';
			if ($handle = opendir($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/themes')) {
				while (false !== ($dir_name = readdir($handle))) {
					if ($dir_name != "." && $dir_name != ".." && $dir_name != ".svn" && is_dir($theme_dir.'/'.$dir_name)) {
						$dir_label = str_replace('_', ' ', $dir_name);
						$dir_label = str_replace('-', ' ', $dir_label);
						if ($dir_name == $user_template_name) {
							echo "		<option value='$dir_name' selected='selected'>$dir_label</option>\n";
						}
						else {
							echo "		<option value='$dir_name'>$dir_label</option>\n";
						}
					}
				}
				closedir($handle);
			}
			echo "	</select>\n";
			echo "	<br />\n";
			echo "	Select a template to set as the default and then press save.<br />\n";
			echo "	</td>\n";
			echo "	</tr>\n";
		}
		*/
	echo "	<tr>\n";
	echo "	<td width='20%' class=\"vncell\" style='text-align: left;'>\n";
	echo "		Time Zone: \n";
	echo "	</td>\n";
	echo "	<td class=\"vtable\" align='left'>\n";
	echo "		<select id='user_time_zone' name='user_time_zone' class='formfld' style=''>\n";
	echo "		<option value=''></option>\n";
	//$list = DateTimeZone::listAbbreviations();
    $time_zone_identifiers = DateTimeZone::listIdentifiers();
	$previous_category = '';
	$x = 0;
	foreach ($time_zone_identifiers as $key => $row) {
		$tz = explode("/", $row);
		$category = $tz[0];
		if ($category != $previous_category) {
			if ($x > 0) {
				echo "		</optgroup>\n";
			}
			echo "		<optgroup label='".$category."'>\n";
		}
		if ($row == $user_time_zone) {
			echo "			<option value='".$row."' selected='selected'>".$row."</option>\n";
		}
		else {
			echo "			<option value='".$row."'>".$row."</option>\n";
		}
		$previous_category = $category;
		$x++;
	}
	echo "		</select>\n";
	echo "		<br />\n";
	echo "		Select the default time zone.<br />\n";
	echo "	</td>\n";
	echo "	</tr>\n";

	echo "	</table>";
	echo "<br>";

	echo "<div class='' style='padding:10px;'>\n";
	echo "<table $table_width>";
	echo "	<tr>";
	echo "		<td colspan='2' align='right'>";
	echo "			<input type='hidden' name='id' value=\"$id\">";
	echo "			<input type='hidden' name='username' value=\"$username\">";
	echo "			<input type='submit' name='submit' class='btn' value='Save'>";
	echo "		</td>";
	echo "	</tr>";
	echo "</table>";

	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";
	echo "</form>";

//include the footer
	require_once "includes/footer.php";

?>