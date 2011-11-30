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
	Portions created by the Initial Developer are Copyright (C) 2008-2010
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (permission_exists('caller_route_add')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/paging.php";
$orderby = $_GET["orderby"];
$order = $_GET["order"];


//POST to PHP variables
	if (count($_POST)>0) {
		$name = check_str($_POST["extension_name"]);
		$publicorder = check_str($_POST["publicorder"]);
		$cid_prefix = check_str($_POST["cid_prefix"]);
 		$action_1 = check_str($_POST["action_1"]);
		//$action_1 = "transfer:1001 XML default";
		$action_1_array = explode(":", $action_1);
		$action_application_1 = array_shift($action_1_array);
		$action_data_1 = join(':', $action_1_array);
		$enabled = check_str($_POST["enabled"]);
		$description = check_str($_POST["description"]);
		if (strlen($enabled) == 0) { $enabled = "true"; } //set default to enabled
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {
	//check for all required data
		if (strlen($v_id) == 0) { $msg .= "Please provide: v_id<br>\n"; }
		if (strlen($name) == 0) { $msg .= "Please provide: Extension Name<br>\n"; }
		if (strlen($cid_prefix) == 0) { $msg .= "Please provide: The caller's number<br>\n"; }
		if (strlen($action_application_1) == 0) { $msg .= "Please provide: Destination<br>\n"; }
		//if (strlen($enabled) == 0) { $msg .= "Please provide: Enabled True or False<br>\n"; }
		//if (strlen($description) == 0) { $msg .= "Please provide: Description<br>\n"; }
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

        //remove the invalid characters from the extension name
                $extension_name = str_replace(" ", "_", $name);
                $extension_name = str_replace("/", "", $name);

        //start the atomic transaction
                $count = $db->exec("BEGIN;"); //returns affected rows

        //add the main public include entry
                $sql = "insert into v_public_includes ";
                $sql .= "(";
                $sql .= "v_id, ";
                $sql .= "extensionname, ";
                $sql .= "publicorder, ";
                $sql .= "context, ";
                $sql .= "enabled, ";
                $sql .= "descr, ";
				$sql .= "extensioncontinue, ";
				$sql .= "caller_route ";
                $sql .= ") ";
                $sql .= "values ";
                $sql .= "(";
                $sql .= "'$v_id', ";
                $sql .= "'$extension_name', ";
                $sql .= "'$publicorder', ";
                $sql .= "'default', ";
                $sql .= "'$enabled', ";
                $sql .= "'$description', ";
				$sql .= "'true', ";
				$sql .= "'true' ";
                $sql .= ")";
                if ($db_type == "sqlite" || $db_type == "mysql" ) {
                        $db->exec(check_sql($sql));
                        $public_include_id = $db->lastInsertId($id);
                }
                if ($db_type == "pgsql") {
                        $sql .= " RETURNING public_include_id ";
                        $prepstatement = $db->prepare(check_sql($sql));
                        $prepstatement->execute();
                        $result = $prepstatement->fetchAll();
                        foreach ($result as &$row) {
                                $public_include_id = $row["public_include_id"];
                        }
                        unset($prepstatement, $result);
                }
                unset($sql);

        //add condition public context
                $sql = "insert into v_public_includes_details ";
                $sql .= "(";
                $sql .= "v_id, ";
                $sql .= "public_include_id, ";
                $sql .= "tag, ";
                $sql .= "fieldtype, ";
                $sql .= "fielddata, ";
                $sql .= "fieldorder ";
                $sql .= ") ";
                $sql .= "values ";
                $sql .= "(";
                $sql .= "'$v_id', ";
                $sql .= "'$public_include_id', ";
                $sql .= "'condition', ";
                $sql .= "'context', ";
                $sql .= "'public', ";
                $sql .= "'10' ";
                $sql .= ")";
                $db->exec(check_sql($sql));
                unset($sql);

        //add condition 1
                $sql = "insert into v_public_includes_details ";
                $sql .= "(";
                $sql .= "v_id, ";
                $sql .= "public_include_id, ";
                $sql .= "tag, ";
                $sql .= "fieldtype, ";
                $sql .= "fielddata, ";
                $sql .= "fieldorder ";
                $sql .= ") ";
                $sql .= "values ";
                $sql .= "(";
                $sql .= "'$v_id', ";
                $sql .= "'$public_include_id', ";
                $sql .= "'condition', ";
                $sql .= "'caller_id_number', ";
                $sql .= "'^".$cid_prefix."', ";
                $sql .= "'20' ";
                $sql .= ")";
                $db->exec(check_sql($sql));
                unset($sql);

        //add action 1
                $sql = "insert into v_public_includes_details ";
                $sql .= "(";
                $sql .= "v_id, ";
                $sql .= "public_include_id, ";
                $sql .= "tag, ";
                $sql .= "fieldtype, ";
                $sql .= "fielddata, ";
                $sql .= "fieldorder ";
                $sql .= ") ";
                $sql .= "values ";
                $sql .= "(";
                $sql .= "'$v_id', ";
                $sql .= "'$public_include_id', ";
                $sql .= "'action', ";
                $sql .= "'$action_application_1', ";
                $sql .= "'$action_data_1', ";
                $sql .= "'30' ";
                $sql .= ")";
                $db->exec(check_sql($sql));
                unset($sql);

	//insert entry to v_caller_routes for management purposes
                $sql = "insert into v_caller_routes ";
                $sql .= "(";
                $sql .= "v_id, ";
                $sql .= "public_include_id, ";
		$sql .= "name, ";
		$sql .= "enabled, ";
		$sql .= "description, ";
		$sql .= "publicorder, ";
                $sql .= "cid_prefix, ";
                $sql .= "cid_action, ";
                $sql .= "cid_destination ";
                $sql .= ") ";
                $sql .= "values ";
                $sql .= "(";
                $sql .= "'$v_id', ";
                $sql .= "'$public_include_id', ";
		$sql .= "'$name', ";
		$sql .= "'$enabled', ";
		$sql .= "'$description', ";
		$sql .= "'$publicorder', ";
		$sql .= "'^".$cid_prefix."', ";
                $sql .= "'$action_application_1', ";
                $sql .= "'$action_data_1' ";
                $sql .= ")";
                $db->exec(check_sql($sql));
                unset($sql);

	//commit the atomic transaction
		$count = $db->exec("COMMIT;"); //returns affected rows

	//synchronize the xml config
		sync_package_v_public_includes();

	//redirect the user
		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=v_caller_route.php\">\n";
		echo "<div align='center'>\n";
		echo "Update Complete\n";
		echo "</div>\n";
		require_once "includes/footer.php";
		return;
} //end if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//show the header
	require_once "includes/header.php";

?>

<script type="text/javascript">
<!--
function type_onchange(field_type) {
var field_value = document.getElementById(field_type).value;

//desc_action_data_1
//desc_action_data_2

if (field_type == "condition_field_1") {
	if (field_value == "destination_number") {
		document.getElementById("desc_condition_expression_1").innerHTML = "expression: ^12081231234$";
	}
	else if (field_value == "zzz") {
		document.getElementById("desc_condition_expression_1").innerHTML = "";
	}
	else {
		document.getElementById("desc_condition_expression_1").innerHTML = "";
	}
}
if (field_type == "condition_field_2") {
	if (field_value == "destination_number") {
		document.getElementById("desc_condition_expression_2").innerHTML = "expression: ^12081231234$";
	}
	else if (field_value == "zzz") {
		document.getElementById("desc_condition_expression_2").innerHTML = "";
	}
	else {
		document.getElementById("desc_condition_expression_2").innerHTML = "";
	}
}
</script>

<?php

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "		<br>";

	echo "<form method='post' name='frm' action=''>\n";
	echo "<div align='center'>\n";

	echo " 	<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "		<td align='left'><span class=\"vexpl\">\n";
	echo "			<span class=\"red\"><strong>\n";
	echo "				Caller Routes\n";
	echo "			</strong></span></span>\n";
	echo "		</td>\n";
	echo "		<td align='right'>\n";
	echo "			<input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_caller_route.php'\" value='Back'>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td align='left' colspan='2'>\n";
	echo "			<span class=\"vexpl\">\n";
	echo "				Use Caller Routes to redirect calls based on caller id. \n";
	echo "			</span>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>";

	echo "<br />\n";
	echo "<br />\n";

	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";
	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' style='width: 60%;' type='text' name='extension_name' maxlength='255' value=\"$extension_name\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

        echo "<tr>\n";
        echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
        echo "    Caller's Number:\n";
        echo "</td>\n";
        echo "<td class='vtable' align='left'>\n";
        echo "    <input class='formfld' style='width: 60%;' type='text' name='cid_prefix' maxlength='255' value=\"$cid_prefix\">\n";
        echo "<br />\n";
	echo "Enter the caller id prefix or entire number of the caller's number.<br />For example, if you enter 716, all caller's whose number begins with 716 will be routed to the destination. \n";
        echo "\n";
        echo "</td>\n";
        echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Destination:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";

	//switch_select_destination(select_type, select_label, select_name, select_value, select_style, $action);
	switch_select_destination("dialplan", $action_1, "action_1", $action_1, "width: 60%;", "");

	echo "</td>\n";
	echo "</tr>\n";

	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Order:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "              <select name='publicorder' class='formfld' style='width: 60%;'>\n";
	//echo "              <option></option>\n";
	if (strlen(htmlspecialchars($publicorder))> 0) {
		echo "              <option selected='yes' value='".htmlspecialchars($publicorder)."'>".htmlspecialchars($publicorder)."</option>\n";
	}
	$i=0;
	while($i<=999) {
		if (strlen($i) == 1) { echo "              <option value='00$i'>00$i</option>\n"; }
		if (strlen($i) == 2) { echo "              <option value='0$i'>0$i</option>\n"; }
		if (strlen($i) == 3) { echo "              <option value='$i'>$i</option>\n"; }
		$i++;
	}
	echo "              </select>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Enabled:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='enabled' style='width: 60%;'>\n";
	//echo "    <option value=''></option>\n";
	if ($enabled == "true") { 
		echo "    <option value='true' SELECTED >true</option>\n";
	}
	else {
		echo "    <option value='true'>true</option>\n";
	}
	if ($enabled == "false") { 
		echo "    <option value='false' SELECTED >false</option>\n";
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
	echo "<td colspan='4' class='vtable' align='left'>\n";
	echo "    <input class='formfld' style='width: 60%;' type='text' name='description' maxlength='255' value=\"$description\">\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "	<td colspan='5' align='right'>\n";
	if ($action == "update") {
		echo "			<input type='hidden' name='dialplan_include_id' value='$dialplan_include_id'>\n";
	}
	echo "			<input type='submit' name='submit' class='btn' value='Save'>\n";
	echo "	</td>\n";
	echo "</tr>";

	echo "</table>";
	echo "</div>";
	echo "</form>";

	echo "</td>\n";
	echo "</tr>";
	echo "</table>";
	echo "</div>";

	echo "<br><br>";

//show the footer
	require_once "includes/footer.php";
?>
