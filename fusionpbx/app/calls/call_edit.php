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
require_once "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";
require_once "app_languages.php";
if (permission_exists('follow_me') || permission_exists('call_forward') || permission_exists('do_not_disturb')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//add multi-lingual support
	foreach($text as $key => $value) {
		$text[$key] = $value[$_SESSION['domain']['language']['code']];                
	}

//define the destination_select function
	function destination_select($select_name, $select_value, $select_default) {
		if (strlen($select_value) == 0) { $select_value = $select_default; }
		echo "	<select class='formfld' style='width: 45px;' name='$select_name'>\n";
		echo "	<option value=''></option>\n";

		$i = 0;
		while($i <= 100) {
			if ($select_value == $i) {
				echo "	<option value='$i' selected='selected'>$i</option>\n";
			}
			else {
				echo "	<option value='$i'>$i</option>\n";
			}
			$i = $i + 5;
		}
		echo "</select>\n";
	}

//get the extension_uuid
	$extension_uuid = check_str($_REQUEST["id"]);

//get the extension number
	$sql = "select * from v_extensions ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and extension_uuid = '$extension_uuid' ";
	if (!(if_group("admin") || if_group("superadmin"))) {
		if (count($_SESSION['user']['extension']) > 0) {
			$sql .= "and (";
			$x = 0;
			foreach($_SESSION['user']['extension'] as $row) {
				if ($x > 0) { $sql .= "or "; }
				$sql .= "extension = '".$row['user']."' ";
				$x++;
			}
			$sql .= ")";
		}
		else {
			//hide any results when a user has not been assigned an extension
			$sql .= "and extension = 'disabled' ";
		}
	}
	$sql .= "and enabled = 'true' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	if (count($result)== 0) {
		echo "access denied";
		exit;
	}
	else {
		foreach ($result as &$row) {
			$extension = $row["extension"];
			$effective_caller_id_name = $row["effective_caller_id_name"];
			$effective_caller_id_number = $row["effective_caller_id_number"];
			$outbound_caller_id_name = $row["outbound_caller_id_name"];
			$outbound_caller_id_number = $row["outbound_caller_id_number"];
			$do_not_disturb = $row["do_not_disturb"];
			$call_forward_all = $row["call_forward_all"];
			$call_forward_busy = $row["call_forward_busy"];
			$follow_me_uuid = $row["follow_me_uuid"];
			break; //limit to 1 row
		}
		if (strlen($do_not_disturb) == 0) {
			$do_not_disturb = "false";
		}
	}
	unset ($prep_statement);

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	//get http post variables and set them to php variables
		if (count($_POST)>0) {
			$call_forward_enabled = check_str($_POST["call_forward_enabled"]);
			$call_forward_number = check_str($_POST["call_forward_number"]);
			$follow_me_enabled = check_str($_POST["follow_me_enabled"]);

			$destination_data_1 = check_str($_POST["destination_data_1"]);
			$destination_delay_1 = check_str($_POST["destination_delay_1"]);
			$destination_timeout_1 = check_str($_POST["destination_timeout_1"]);

			$destination_data_2 = check_str($_POST["destination_data_2"]);
			$destination_delay_2 = check_str($_POST["destination_delay_2"]);
			$destination_timeout_2 = check_str($_POST["destination_timeout_2"]);

			$destination_data_3 = check_str($_POST["destination_data_3"]);
			$destination_delay_3 = check_str($_POST["destination_delay_3"]);
			$destination_timeout_3 = check_str($_POST["destination_timeout_3"]);

			$destination_data_4 = check_str($_POST["destination_data_4"]);
			$destination_delay_4 = check_str($_POST["destination_delay_4"]);
			$destination_timeout_4 = check_str($_POST["destination_timeout_4"]);

			$destination_data_5 = check_str($_POST["destination_data_5"]);
			$destination_delay_5 = check_str($_POST["destination_delay_5"]);
			$destination_timeout_5 = check_str($_POST["destination_timeout_5"]);

			$dnd_enabled = check_str($_POST["dnd_enabled"]);

			if (strlen($call_forward_number) > 0) {
				$call_forward_number = preg_replace("~[^0-9]~", "",$call_forward_number);
			}
			if (strlen($destination_data_1) > 0) {
				$destination_data_1 = preg_replace("~[^0-9]~", "",$destination_data_1);
			}
			if (strlen($destination_data_2) > 0) {
				$destination_data_2 = preg_replace("~[^0-9]~", "",$destination_data_2);
			}
			if (strlen($destination_data_3) > 0) {
				$destination_data_3 = preg_replace("~[^0-9]~", "",$destination_data_3);
			}
			if (strlen($destination_data_4) > 0) {
				$destination_data_4 = preg_replace("~[^0-9]~", "",$destination_data_4);
			}
			if (strlen($destination_data_5) > 0) {
				$destination_data_5 = preg_replace("~[^0-9]~", "",$destination_data_5);
			}
		}

		//check for all required data
			//if (strlen($call_forward_enabled) == 0) { $msg .= "Please provide: Call Forward<br>\n"; }
			//if (strlen($call_forward_number) == 0) { $msg .= "Please provide: Number<br>\n"; }
			//if (strlen($follow_me_enabled) == 0) { $msg .= "Please provide: Follow Me<br>\n"; }
			//if (strlen($destination_data_1) == 0) { $msg .= "Please provide: 1st Number<br>\n"; }
			//if (strlen($destination_timeout_1) == 0) { $msg .= "Please provide: sec<br>\n"; }
			//if (strlen($destination_data_2) == 0) { $msg .= "Please provide: 2nd Number<br>\n"; }
			//if (strlen($destination_timeout_2) == 0) { $msg .= "Please provide: sec<br>\n"; }
			//if (strlen($destination_data_3) == 0) { $msg .= "Please provide: 3rd Number<br>\n"; }
			//if (strlen($destination_timeout_3) == 0) { $msg .= "Please provide: sec<br>\n"; }
			//if (strlen($destination_data_4) == 0) { $msg .= "Please provide: 4th Number<br>\n"; }
			//if (strlen($destination_timeout_4) == 0) { $msg .= "Please provide: sec<br>\n"; }
			//if (strlen($destination_data_5) == 0) { $msg .= "Please provide: 5th Number<br>\n"; }
			//if (strlen($destination_timeout_5) == 0) { $msg .= "Please provide: sec<br>\n"; }
			//if (strlen($destination_data_6) == 0) { $msg .= "Please provide: 6th Number<br>\n"; }
			//if (strlen($destination_timeout_6) == 0) { $msg .= "Please provide: sec<br>\n"; }
			//if (strlen($destination_data_7) == 0) { $msg .= "Please provide: 7th Number<br>\n"; }
			//if (strlen($destination_timeout_7) == 0) { $msg .= "Please provide: sec<br>\n"; }
			//if (strlen($hunt_group_call_prompt) == 0) { $msg .= "Please provide: call prompt<br>\n"; }
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

	//set the default action to add
		$call_forward_action = "add";

	//determine if this is an add or an update
		if (strlen($follow_me_uuid) == 0) {
			$follow_me_action = "add";
		}
		else {
			$follow_me_action = "update";
		}

	//include the classes
		include "includes/classes/switch_call_forward.php";
		include "includes/classes/switch_follow_me.php";
		include "includes/classes/switch_do_not_disturb.php";

	//call forward config
		if (permission_exists('call_forward')) {
			$call_forward = new call_forward;
			$call_forward->domain_uuid = $_SESSION['domain_uuid'];
			$call_forward->db_type = $db_type;
			$call_forward->extension = $extension;
			$call_forward->call_forward_number = $call_forward_number;
			$call_forward->call_forward_enabled = $call_forward_enabled;
			if ($call_forward_enabled == "true") {
				if ($call_forward_action == "add") {
					$call_forward->call_forward_uuid = uuid();
					$call_forward->call_forward_add();
				}
			}
			if ($call_forward_action == "update") {
				$call_forward->call_forward_uuid = $call_forward_uuid;
				$call_forward->call_forward_update();
			}
			unset($call_forward);

			//synchronize the xml config
				save_hunt_group_xml();

			//synchronize the xml config
				save_dialplan_xml();
		}

	//follow me config

		if (permission_exists('follow_me')) {
			$follow_me = new follow_me;
			$follow_me->domain_uuid = $_SESSION['domain_uuid'];
			$follow_me->db_type = $db_type;
			$follow_me->follow_me_enabled = $follow_me_enabled;

			$follow_me->destination_data_1 = $destination_data_1;
			$follow_me->destination_type_1 = $destination_type_1;
			$follow_me->destination_delay_1 = $destination_delay_1;
			$follow_me->destination_timeout_1 = $destination_timeout_1;

			$follow_me->destination_data_2 = $destination_data_2;
			$follow_me->destination_type_2 = $destination_type_2;
			$follow_me->destination_delay_2 = $destination_delay_2;
			$follow_me->destination_timeout_2 = $destination_timeout_2;

			$follow_me->destination_data_3 = $destination_data_3;
			$follow_me->destination_type_3 = $destination_type_3;
			$follow_me->destination_delay_3 = $destination_delay_3;
			$follow_me->destination_timeout_3 = $destination_timeout_3;

			$follow_me->destination_data_4 = $destination_data_4;
			$follow_me->destination_type_4 = $destination_type_4;
			$follow_me->destination_delay_4 = $destination_delay_4;
			$follow_me->destination_timeout_4 = $destination_timeout_4;

			$follow_me->destination_data_5 = $destination_data_5;
			$follow_me->destination_type_5 = $destination_type_5;
			$follow_me->destination_delay_5 = $destination_delay_5;
			$follow_me->destination_timeout_5 = $destination_timeout_5;

			if ($follow_me_enabled == "true") {
				if ($follow_me_action == "add") {
					$follow_me_uuid = uuid();

					$sql = "update v_extensions set ";
					$sql .= "follow_me_uuid = '$follow_me_uuid' ";
					$sql .= "where domain_uuid = '$domain_uuid' ";
					$sql .= "and extension_uuid = '$extension_uuid' ";
					$db->exec(check_sql($sql));
					unset($sql);

					$follow_me->follow_me_uuid = $follow_me_uuid;
					$follow_me->follow_me_add();
					$follow_me->set();
				}
			}
			if ($follow_me_action == "update") {
				$follow_me->follow_me_uuid = $follow_me_uuid;
				$follow_me->follow_me_update();
				$follow_me->set();
			}
			unset($follow_me);

			//synchronize the xml config
				save_dialplan_xml();
		}

	//do not disturb (dnd) config
		if (permission_exists('do_not_disturb')) {
			$dnd = new do_not_disturb;
			$dnd->domain_uuid = $_SESSION['domain_uuid'];
			$dnd->domain_name = $_SESSION['domain_name'];
			$dnd->extension = $extension;
			$dnd->enabled = $dnd_enabled;
			$dnd->set();
			$dnd->user_status();
			unset($dnd);
		}

	//redirect the user
		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"3;url=".PROJECT_PATH."/app/calls/calls.php\">\n";
		echo "<div align='center'>\n";
		echo "".$text['confirm-update']."<br />\n";
		echo "</div>\n";
		require_once "includes/footer.php";
		return;

} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//show the header
	require_once "includes/header.php";

//pre-populate the form
	$sql = "select * from v_follow_me ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and follow_me_uuid = '$follow_me_uuid' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
	foreach ($result as &$row) {
		$follow_me_enabled = $row["follow_me_enabled"];

		$sql = "select * from v_follow_me_destinations ";
		$sql .= "where follow_me_uuid = '$follow_me_uuid' ";
		$sql .= "order by follow_me_order asc ";
		$prep_statement_2 = $db->prepare(check_sql($sql));
		$prep_statement_2->execute();
		$result2 = $prep_statement_2->fetchAll(PDO::FETCH_NAMED);
		$x = 1;
		foreach ($result2 as &$row2) {
			//$call_forward_number = $row2["destination_data"];
			if ($x == 1) {
				$destination_data_1 = $row2["follow_me_destination"];
				$destination_delay_1 = $row2["follow_me_delay"];
				$destination_timeout_1 = $row2["follow_me_timeout"];
			}
			if ($x == 2) {
				$destination_data_2 = $row2["follow_me_destination"];
				$destination_delay_2 = $row2["follow_me_delay"];
				$destination_timeout_2 = $row2["follow_me_timeout"];
			}
			if ($x == 3) {
				$destination_data_3 = $row2["follow_me_destination"];
				$destination_delay_3 = $row2["follow_me_delay"];
				$destination_timeout_3 = $row2["follow_me_timeout"];
			}
			if ($x == 4) {
				$destination_data_4 = $row2["follow_me_destination"];
				$destination_delay_4 = $row2["follow_me_delay"];
				$destination_timeout_4 = $row2["follow_me_timeout"];
			}
			if ($x == 5) {
				$destination_data_5 = $row2["follow_me_destination"];
				$destination_delay_5 = $row2["follow_me_delay"];
				$destination_timeout_5 = $row2["follow_me_timeout"];
			}
			$x++;
		}
		unset ($prep_statement_2);
	}
	unset ($prep_statement);

//set the default
	if (!isset($dnd_enabled)) {
		//set the value from the database
		$dnd_enabled = $do_not_disturb;
	}

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing=''>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"center\">\n";
	echo "		<br>";

	echo "<form method='post' name='frm' action=''>\n";
	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<td align='left' width='30%' nowrap='nowrap'>\n";
	echo "	<b>".$text['title']."</b>\n";
	echo "</td>\n";
	echo "<td width='70%' align='right'>\n";
	echo "	<input type='button' class='btn' name='' alt='back' onclick=\"window.location='calls.php'\" value='".$text['button-back']."'>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' colspan='2'>\n";
	echo "	".$text['description']."  $extension.<br /><br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	<strong>".$text['label-call-forward'].":</strong>\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	$on_click = "document.getElementById('follow_me_enabled').checked=true;";
	$on_click .= "document.getElementById('follow_me_disabled').checked=true;";
	$on_click .= "document.getElementById('dnd_enabled').checked=false;";
	$on_click .= "document.getElementById('dnd_disabled').checked=true;";
	if ($call_forward_enabled == "true") {
		echo "	<input type='radio' name='call_forward_enabled' id='call_forward_enabled' onclick=\"$on_click\" value='true' checked='checked'/> ".$text['label-enabled']." \n";
	}
	else {
		echo "	<input type='radio' name='call_forward_enabled' id='call_forward_enabled' onclick=\"$on_click\" value='true' /> ".$text['label-enable']." \n";
	}
	if ($call_forward_enabled == "false" || $call_forward_enabled == "") {
		echo "	<input type='radio' name='call_forward_enabled' id='call_forward_disabled' onclick=\"\" value='false' checked='checked' /> ".$text['label-disabled']." \n";
	}
	else {
		echo "	<input type='radio' name='call_forward_enabled' id='call_forward_disabled' onclick=\"\" value='false' /> ".$text['label-disable']." \n";
	}
	unset($on_click);
	echo "<br />\n";
	echo "<br />\n";
	//echo "Enable or disable call forward.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-number'].":\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='call_forward_number' maxlength='255' value=\"$call_forward_number\">\n";
	echo "<br />\n";
	//echo "Enter the call forward number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td colspan='2'>\n";
	echo "	<br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	<strong>".$text['label-follow-me'].":</strong>\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	$on_click = "document.getElementById('call_forward_enabled').checked=true;";
	$on_click .= "document.getElementById('call_forward_disabled').checked=true;";
	$on_click .= "document.getElementById('dnd_enabled').checked=false;";
	$on_click .= "document.getElementById('dnd_disabled').checked=true;";
	if ($follow_me_enabled == "true") {
		echo "	<input type='radio' name='follow_me_enabled' id='follow_me_enabled' value='true' onclick=\"$on_click\" checked='checked'/> ".$text['label-enabled']." \n";
	}
	else {
		echo "	<input type='radio' name='follow_me_enabled' id='follow_me_enabled' value='true' onclick=\"$on_click\" /> ".$text['label-enable']." \n";
	}
	if ($follow_me_enabled == "false" || $follow_me_enabled == "") {
		echo "	<input type='radio' name='follow_me_enabled' id='follow_me_disabled' value='false' onclick=\"\" checked='checked' /> ".$text['label-disabled']." \n";
	}
	else {
		echo "	<input type='radio' name='follow_me_enabled' id='follow_me_disabled' value='false' onclick=\"\" /> ".$text['label-disable']." \n";
	}
	unset($on_click);
	echo "<br />\n";
	echo "<br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-ring-1'].":\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='destination_data_1' maxlength='255' value=\"$destination_data_1\">\n";
	echo "	".$text['label-ring-delay']."\n"; 
	destination_select('destination_delay_1', $destination_delay_1, '0');
	echo "	".$text['label-ring-timeout']."\n"; 
	destination_select('destination_timeout_1', $destination_timeout_1, '10');
	//echo "<br />\n";
	//echo "This number rings first.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-ring-2'].":\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='destination_data_2' maxlength='255' value=\"$destination_data_2\">\n";
	echo "	".$text['label-ring-delay']."\n"; 
	destination_select('destination_delay_2', $destination_delay_2, '0');
	echo "	".$text['label-ring-timeout']."\n"; 
	destination_select('destination_timeout_2', $destination_timeout_2, '30');
	//echo "<br />\n";
	//echo "Enter the destination number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-ring-3'].":\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='destination_data_3' maxlength='255' value=\"$destination_data_3\">\n";
	echo "	".$text['label-ring-delay']."\n"; 
	destination_select('destination_delay_3', $destination_delay_3, '0');
	echo "	".$text['label-ring-timeout']."\n"; 
	destination_select('destination_timeout_3', $destination_timeout_3, '30');
	//echo "<br />\n";
	//echo "Enter the destination number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-ring-4'].":\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='destination_data_4' maxlength='255' value=\"$destination_data_4\">\n";
	echo "	".$text['label-ring-delay']."\n"; 
	destination_select('destination_delay_4', $destination_delay_4, '0');
	echo "	".$text['label-ring-timeout']."\n"; 
	destination_select('destination_timeout_4', $destination_timeout_4, '30');
	//echo "<br />\n";
	//echo "Enter the destination number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	".$text['label-ring-5'].":\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "	<input class='formfld' type='text' name='destination_data_5' maxlength='255' value=\"$destination_data_5\">\n";
	echo "	".$text['label-ring-delay']."\n"; 
	destination_select('destination_delay_5', $destination_delay_5, '0');
	echo "	".$text['label-ring-timeout']."\n"; 
	destination_select('destination_timeout_5', $destination_timeout_5, '30');
	//echo "<br />\n";
	//echo "Enter the destination number.\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td colspan='2'>\n";
	echo "	<br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncell' valign='top' align='left' nowrap='nowrap'>\n";
	echo "	<strong>".$text['label-dnd'].":</strong>\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	$on_click = "document.getElementById('call_forward_enabled').checked=true;";
	$on_click .= "document.getElementById('call_forward_disabled').checked=true;";
	$on_click .= "document.getElementById('follow_me_enabled').checked=true;";
	$on_click .= "document.getElementById('follow_me_disabled').checked=true;";
	if ($dnd_enabled == "true") {
		echo "	<input type='radio' name='dnd_enabled' id='dnd_enabled' value='true' onclick=\"$on_click\" checked='checked'/> ".$text['label-enabled']." \n";
	}
	else {
		echo "	<input type='radio' name='dnd_enabled' id='dnd_enabled' value='true' onclick=\"$on_click\"/> ".$text['label-enable']." \n";
	}
	if ($dnd_enabled == "false" || $dnd_enabled == "") {
		echo "	<input type='radio' name='dnd_enabled' id='dnd_disabled' value='false' onclick=\"\" checked='checked' /> ".$text['label-disabled']." \n";
	}
	else {
		echo "	<input type='radio' name='dnd_enabled' id='dnd_disabled' value='false' onclick=\"\" /> ".$text['label-disable']." \n";
	}
	echo "	<br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td colspan='2'>\n";
	echo "	<br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td colspan='2'>\n";
	echo "	<br />\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='id' value='$extension_uuid'>\n";
	}
	echo "				<input type='submit' name='submit' class='btn' value='".$text['button-save']."'>\n";
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