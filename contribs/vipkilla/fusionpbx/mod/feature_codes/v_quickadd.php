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
	Copyright (C) 2008-2010 All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>

	This page written by:
	T_Dot_Zilla <vipkilla@gmail.com>
*/
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
require_once "includes/email_address_validator.php";
if (permission_exists('feature_codes_add') || permission_exists('feature_codes_edit')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

$sql = "select email_login from v_settings where setting_id='1'";
$prepstatement = $db->prepare(check_sql($sql));
$prepstatement->execute();
$res = $prepstatement->fetchAll();
foreach($res as $field) {
        $email_login = $field["email_login"];
        }
unset($sql);

//get the http values and set them as php variables
	if (count($_POST)>0) {
		//get the values from the HTTP POST and save them as PHP variables
			$useremail = check_str($_POST["useremail"]);
			$extension = check_str($_POST["extension"]);
			$password = check_str($_POST["password"]);

		//get the values from the HTTP POST and save them as PHP variables
			$vm_password = check_str($_POST["vm_password"]);
			$effective_caller_id_name = check_str($_POST["effective_caller_id_name"]);
			$outbound_caller_id_name = check_str($_POST["outbound_caller_id_name"]);
			$outbound_caller_id_number = check_str($_POST["outbound_caller_id_number"]);
			$vm_enabled = "true";
			if(strlen($_POST["vm_mailto"]) == 0) 
				$vm_mailto = $useremail;
			else
				$vm_mailto = check_str($_POST["vm_mailto"]);
			$vm_attach_file = check_str($_POST["vm_attach_file"]);
			$vm_keep_local_after_email = check_str($_POST["vm_keep_local_after_email"]);
			$toll_allow = check_str($_POST["toll_allow"]);
			$enabled = check_str($_POST["enabled"]);
			$description = check_str($_POST["description"]);
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {
		$msg = '';
	        $sql = "select count(*) as num_rows from v_users where useremail='".$useremail."'";
	        $prepstatement = $db->prepare(check_sql($sql));
		if ($prepstatement) {
	                $prepstatement->execute();
	                $row = $prepstatement->fetch(PDO::FETCH_ASSOC);
			if ($row['num_rows'] > 0) {
				$msg .= "Please choose an Email address that is not already in use.<br>\n";	
	        	}
		}
	        unset($prepstatement, $result);

	//check for all required data
        // email address atleast looks valid
		if (strlen($useremail) == 0) { $msg .= "Please provide: E-mail address<br>\n"; }
		else {
			$validator = new EmailAddressValidator;
			if (!$validator->check_email_address($useremail)) {
				$msg .= "Please provide a VALID email address.<br>\n";
        		}
		}
		if (strlen($extension) == 0) { $msg .= "Please provide: Extension<br>\n"; }
		if (strlen($enabled) == 0) { $msg .= "Please provide: Enabled<br>\n"; }
		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			require_once "includes/header.php";
			require_once "includes/persistformvar.php";
			echo "<div align='center'>\n";
			echo "<table><tr><td>\n";
			echo $msg."<br />";
			echo "</td></tr></table>\n";
			persistformvar($_POST);
			echo "</div>\n";
			require_once "includes/footer.php";
			return;
		}

	//set the default user context
		if (ifgroup("superadmin")) {
			//allow a user assigned to super admin to change the user_context
		}
		else {
			//if the user_context was not set then set the default value
			if (strlen($user_context) == 0) { 
				if (count($_SESSION["domains"]) > 1) {
					$user_context = $v_domain;
				}
				else {
					$user_context = "default";
				}
			}
		}

	//add to the database
	if ($_POST["persistformvar"] != "true") {
		if (permission_exists('feature_codes_add')) {
			$user_first_name = 'extension';
			$user_last_name = $extension;
			$user_password = generate_password();
			if($email_login == 1) 
				$username = $useremail;
			else 
				$username = $extension;
			user_add($username, $user_password, $user_first_name, $user_last_name, $useremail);
			unset($auto_user);

			$db->beginTransaction();
			if (extension_exists($extension)) {
				//extension exists
			}
			else {
				$sql = "select * from v_system_settings where v_id='".$v_id."'";
				$prepstatement = $db->prepare(check_sql($sql));
				$prepstatement->execute();
				$res = $prepstatement->fetchAll();
				$toll_allow = "";
				$c = false;
				foreach($res as $field) {
					if($field["v_disable_local_calls"]!=1) {
						$toll_allow .= "local";
						$c = true;
					}
					if($field["v_disable_domestic_calls"]!=1) {
						if($c == true)
							$toll_allow .= ",";
						$toll_allow .= "domestic";
						$c = true;
					}
					if($field["v_disable_international_calls"]!=1) {
						if($c == true)
							$toll_allow .= ",";
						$toll_allow .= "international";
					}
				}                               
				unset($sql); 
				//extension does not exist add it
				$password = generate_password();
				$sql = "insert into v_extensions ";
				$sql .= "(";
				$sql .= "v_id, ";
				$sql .= "extension, ";
				$sql .= "number_alias, ";
				$sql .= "password, ";
				$sql .= "provisioning_list, ";
				$sql .= "user_list, ";
				$sql .= "vm_password, ";
				$sql .= "effective_caller_id_name, ";
				$sql .= "effective_caller_id_number, ";
				$sql .= "outbound_caller_id_name, ";
				$sql .= "outbound_caller_id_number, ";
				$sql .= "limit_max, ";
				$sql .= "limit_destination, ";
				$sql .= "vm_enabled, ";
				$sql .= "vm_mailto, ";
				$sql .= "vm_attach_file, ";
				$sql .= "vm_keep_local_after_email, ";
				$sql .= "user_context, ";
				$sql .= "toll_allow, ";
				$sql .= "callgroup, ";
				$sql .= "auth_acl, ";
				$sql .= "cidr, ";
				$sql .= "sip_force_contact, ";
				$sql .= "sip_bypass_media, ";
				$sql .= "enabled, ";
				$sql .= "description ";
				$sql .= ")";
				$sql .= "values ";
				$sql .= "(";
				$sql .= "'$v_id', ";
				$sql .= "'$extension', ";
				$sql .= "'', ";
				$sql .= "'$password', ";
				$sql .= "'$provisioning_list', ";
				$sql .= "'|".$username."|', ";
				$sql .= "'user-choose', ";
				$sql .= "'$effective_caller_id_name', ";
				$sql .= "'$extension', ";
				$sql .= "'$outbound_caller_id_name', ";
				$sql .= "'$outbound_caller_id_number', ";
				$sql .= "'5', ";
				$sql .= "'$limit_destination', ";
				$sql .= "'$vm_enabled', ";
				$sql .= "'$vm_mailto', ";
				$sql .= "'$vm_attach_file', ";
				$sql .= "'$vm_keep_local_after_email', ";
				$sql .= "'$user_context', ";
				$sql .= "'$toll_allow', ";
				$sql .= "'$callgroup', ";
				$sql .= "'$auth_acl', ";
				$sql .= "'$cidr', ";
				$sql .= "'$sip_force_contact', ";
				$sql .= "'$sip_bypass_media', ";
				$sql .= "'$enabled', ";
				$sql .= "'$description' ";
				$sql .= ")";
				$db->exec(check_sql($sql));
				unset($sql);
			}

			$extension++;
			$db->commit();

			//syncrhonize configuration
				sync_package_v_extensions();

			//write the provision files
				if (strlen($provisioning_list)>0) {
					require_once "mod/provision/v_provision_write.php";
				}

			//show the action and redirect the user
				require_once "includes/header.php";
				echo "<br />\n";
				echo "<div align='center'>\n";
				//action add
				echo "  <table width='40%' border='0' cellpadding='0' cellspacing='0'>\n";
                                echo "          <tr>\n";
                                echo "                  <td colspan='2'><strong>New User Account</strong></td>\n";
                                echo "          </tr>\n";
                                echo "          <tr>\n";
                                echo "                  <th>Username</th>\n";
                                echo "                  <th>Password</th>\n";
                                echo "          </tr>\n";
				echo "          <tr>\n";
				echo "                  <td valign='top' class='rowstyle0'>".$useremail."</td>\n";
                                echo "                  <td valign='top' class='rowstyle0'>".$user_password."</td>\n";
                                echo "          </tr>\n";
				echo "  </table>";
				require_once "includes/footer.php";
				return;
		} //if ($action == "add")
	} //if ($_POST["persistformvar"] != "true")
} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//set the defaults
	if (strlen($limit_max) == 0) { $limit_max = '5'; }

//begin the page content
	require_once "includes/header.php";

	echo "<script type=\"text/javascript\" language=\"JavaScript\">\n";
	echo "\n";
	echo "function enable_change(enable_over) {\n";
	echo "	var endis;\n";
	echo "	endis = !(document.iform.enable.checked || enable_over);\n";
	echo "	document.iform.range_from.disabled = endis;\n";
	echo "	document.iform.range_to.disabled = endis;\n";
	echo "}\n";
	echo "\n";
	echo "function show_advanced_config() {\n";
	echo "	document.getElementById(\"showadvancedbox\").innerHTML='';\n";
	echo "	aodiv = document.getElementById('showadvanced');\n";
	echo "	aodiv.style.display = \"block\";\n";
	echo "}\n";
	echo "\n";
	echo "function hide_advanced_config() {\n";
	echo "	document.getElementById(\"showadvancedbox\").innerHTML='';\n";
	echo "	aodiv = document.getElementById('showadvanced');\n";
	echo "	aodiv.style.display = \"none\";\n";
	echo "}\n";
	echo "</script>";

	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";

	echo "<form method='post' name='frm' action=''>\n";
	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='6' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<td width='30%' nowrap align='left' valign='top'><b>Quick Add</b></td>\n";
	echo "<td width='70%' align='right' valign='top'>\n";
	echo "	<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "	<input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_extensions.php'\" value='Back'>\n";
	echo "	<br /><br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
        echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
        echo "    E-mail address:\n";
        echo "</td>\n";
        echo "<td class='vtable' align='left'>\n";
        echo "    <input class='formfld' type='text' name='useremail' maxlength='255' value=\"$useremail\">\n";
        echo "<br />\n";
        echo "The email address will be used as the user's login.<br>\n";
        echo "<input type=\"hidden\" name=\"autogen_users\" value=\"true\">";
        echo "</td>\n";
        echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Extension:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='extension' maxlength='255' value=\"$extension\">\n";
	echo "<br />\n";
	echo "Enter the alphanumeric extension. The default configuration allows 2 - 7 digit extensions.<br>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Internal Caller ID Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='effective_caller_id_name' maxlength='255' value=\"$effective_caller_id_name\">\n";
	echo "<br />\n";
	echo "Enter the internal caller id name here.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Outbound Caller ID Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='outbound_caller_id_name' maxlength='255' value=\"$outbound_caller_id_name\">\n";
	echo "<br />\n";
	echo "Enter the external caller id name here.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Outbound Caller ID Number:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
//	echo "    <input class='formfld' type='text' name='outbound_caller_id_number' maxlength='255' value=\"$outbound_caller_id_number\">\n";
        echo "    <select class='formfld' name='outbound_caller_id_number'>\n";
        echo "    <option value=''></option>\n";
        $nsql = "select number from v_numbers where v_id='${v_id}'";
        $prepstatement = $db->prepare(check_sql($nsql));
        $prepstatement->execute();
        $res = $prepstatement->fetchAll();
	foreach($res as $field) {
		if($outbound_caller_id_number==$field['number'])
			echo "    <option value='".$field['number']."' selected>".$field['number']."</option>\n";
		else
	        	echo "    <option value='".$field['number']."'>".$field['number']."</option>\n";
        }
        unset($nsql);
        echo "    </select>";
	echo "<br />\n";
	echo "Enter the external caller id number here.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Voicemail Mail To:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='hidden' name='vm_enabled' value='true'>";
	echo "	  <input class='formfld' type='text' name='vm_mailto' maxlength='255' value=\"$vm_mailto\">\n";
	echo "<br />\n";
	echo "Optional: Enter the email address to send voicemail to.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Voicemail Attach File:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='vm_attach_file'>\n";
	echo "    <option value=''></option>\n";
	if ($vm_attach_file == "true") { 
		echo "    <option value='true' selected >true</option>\n";
	}
	else {
		echo "    <option value='true'>true</option>\n";
	}
	if ($vm_attach_file == "false") { 
		echo "    <option value='false' selected >false</option>\n";
	}
	else {
		echo "    <option value='false'>false</option>\n";
	}
	echo "    </select>\n";
	echo "<br />\n";
	echo "Choose whether to attach the file to the email.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    VM Keep Local After Email:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='vm_keep_local_after_email'>\n";
	echo "    <option value=''></option>\n";
	if ($vm_keep_local_after_email == "true") { 
		echo "    <option value='true' selected >true</option>\n";
	}
	else {
		echo "    <option value='true'>true</option>\n";
	}
	if ($vm_keep_local_after_email == "false") { 
		echo "    <option value='false' selected >false</option>\n";
	}
	else {
		echo "    <option value='false'>false</option>\n";
	}
	echo "    </select>\n";
	echo "<br />\n";
	echo "Keep local file after sending the email. \n";
	echo "</td>\n";
	echo "</tr>\n";

	if (ifgroup("superadmin")) {
		if (strlen($user_context) == 0) { 
			if (count($_SESSION["domains"]) > 1) {
				$user_context = $v_domain;
			}
			else {
				$user_context = "default";
			}
		}
		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "    User Context:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "    <input class='formfld' type='text' name='user_context' maxlength='255' value=\"$user_context\">\n";
		echo "<br />\n";
		echo "Enter the user context here.\n";
		echo "</td>\n";
		echo "</tr>\n";
	}

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Enabled:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='enabled'>\n";
	echo "    <option value=''></option>\n";
	if ($enabled == "true" || strlen($enabled) == 0) { 
		echo "    <option value='true' selected >true</option>\n";
	}
	else {
		echo "    <option value='true'>true</option>\n";
	}
	if ($enabled == "false") { 
		echo "    <option value='false' selected >false</option>\n";
	}
	else {
		echo "    <option value='false'>false</option>\n";
	}
	echo "    </select>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap>\n";
	echo "    Description:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <textarea class='formfld' name='description' rows='4'>$description</textarea>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	echo "				<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "		</td>\n";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";


	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";


require_once "includes/footer.php";
?>
