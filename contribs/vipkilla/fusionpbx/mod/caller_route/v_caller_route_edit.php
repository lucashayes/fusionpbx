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
require_once "includes/paging.php";
if (permission_exists('caller_route_edit')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}


//set the action as an add or as an update
	if (isset($_REQUEST["id"])) {
		$action = "update";
		$caller_route_id = check_str($_REQUEST["id"]);
		$publicorder = check_str($_POST["publicorder"]);
	}

//set the http post as a php variable
	if (count($_POST)>0) {
		//$v_id = check_str($_POST["v_id"]);
		$name = check_str($_POST["name"]);
		$enabled = check_str($_POST["enabled"]);
		$description = check_str($_POST["description"]);
	}

if (count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {

	$msg = '';
	if ($action == "update") {
		$caller_route_id = check_str($_POST["caller_route_id"]);
		$public_include_id = check_str($_POST["public_include_id"]);
	}

	//check for all required data
		if (strlen($v_id) == 0) { $msg .= "Please provide: v_id<br>\n"; }
		if (strlen($name) == 0) { $msg .= "Please provide: Extension Name<br>\n"; }
		if (strlen($enabled) == 0) { $msg .= "Please provide: Enabled<br>\n"; }
		if (strlen($publicorder) == 0) { $msg .= "Please provide: Order<br>\n"; }
		//if (strlen($descr) == 0) { $msg .= "Please provide: Description<br>\n"; }
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

	//add or update the database
		if ($_POST["persistformvar"] != "true") {
			if ($action == "update") {
				$sql = "update v_caller_routes set ";
//				$sql .= "v_id = '$v_id', ";
				$sql .= "name = '$name', ";
				$sql .= "publicorder = '$publicorder', ";
//				$sql .= "extensioncontinue = '$extensioncontinue', ";
//				$sql .= "context = '$context', ";
				$sql .= "enabled = '$enabled', ";
				$sql .= "description = '$description' ";
				$sql .= "where v_id = '$v_id' ";
				$sql .= "and caller_route_id = '$caller_route_id'";
				$db->exec(check_sql($sql));
				unset($sql);


	        //remove the invalid characters from the extension name
	        	        $extension_name = str_replace(" ", "_", $name);
		                $extension_name = str_replace("/", "", $name);

                                $sql = "update v_public_includes set ";
//                              $sql .= "v_id = '$v_id', ";
                                $sql .= "extensionname = '$extension_name', ";
                                $sql .= "publicorder = '$publicorder', ";
//                              $sql .= "extensioncontinue = '$extensioncontinue', ";
//                              $sql .= "context = '$context', ";
                                $sql .= "enabled = '$enabled', ";
                                $sql .= "descr = '$description' ";
                                $sql .= "where v_id = '$v_id' ";
                                $sql .= "and public_include_id = '$public_include_id'";
                                $db->exec(check_sql($sql));
                                unset($sql);

				//synchronize the xml config
				sync_package_v_public_includes();
				require_once "includes/header.php";
				echo "<meta http-equiv=\"refresh\" content=\"2;url=v_caller_route.php\">\n";
				echo "<div align='center'>\n";
				echo "Update Complete\n";
				echo "</div>\n";
				require_once "includes/footer.php";
				return;
			} //if ($action == "update")
		} //if ($_POST["persistformvar"] != "true")
} //(count($_POST)>0 && strlen($_POST["persistformvar"]) == 0)

//pre-populate the form
	if (count($_GET)>0 && $_POST["persistformvar"] != "true") {
		$caller_route_id = $_GET["id"];
		$sql = "";
		$sql .= "select * from v_caller_routes ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and caller_route_id = '$caller_route_id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		foreach ($result as &$row) {
			$v_id = $row["v_id"];
			$cid_prefix = trim($row["cid_prefix"],'^');
			$cid_destination = $row["cid_destination"];
			$name = $row["name"];
			$enabled = $row["enabled"];
			$description = $row["description"];
			$publicorder = $row["publicorder"];
			$public_include_id = $row["public_include_id"];
			break; //limit to 1 row
		}
		unset ($prepstatement, $result);
	}

//show the header
	require_once "includes/header.php";

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";

	echo "<form method='post' name='frm' action=''>\n";
	echo "<div align='center'>\n";
	echo "<table width=\"100%\" border=\"0\" cellpadding=\"1\" cellspacing=\"0\">\n";
	echo "  <tr>\n";
	echo "    <td align='left' width='30%'><p><span class=\"vexpl\"><span class=\"red\"><strong>Caller Route<br />\n";
	echo "        </strong></span>\n";
	echo "    </td>\n";
	echo "    <td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_caller_route.php'\" value='Back'></td>\n";
	echo "  </tr>\n";
	echo "  <tr>\n";
	echo "    <td align='left' colspan='2'>\n";
	echo "        Call Forward general settings. \n";
	echo "        </span></p>\n";
	echo "    </td>\n";
	echo "  </tr>\n";
	echo "</table>";
	echo "<br />\n";

	echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Name:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <input class='formfld' type='text' name='name' maxlength='255' value=\"$name\">\n";
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
        echo "Enter the caller id prefix or entire number of the caller's number.<br />For example, if you enter 716, all caller's whose number begins with 716 will be routed to the destination. \n
";
        echo "\n";
        echo "</td>\n";
        echo "</tr>\n";

        echo "<tr>\n";
        echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
        echo "    Destination:\n";
        echo "</td>\n";
        echo "<td class='vtable' align='left'>\n";

        //switch_select_destination(select_type, select_label, select_name, select_value, select_style, $action);
        switch_select_destination_admin("dialplan", $action_1, "action_1", $action_1, "width: 60%;", "", $cid_destination);

        echo "</td>\n";
        echo "</tr>\n";

        echo "<tr>\n";
        echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
        echo "    Order:\n";
        echo "</td>\n";
        echo "<td class='vtable' align='left'>\n";
        echo "              <select name='publicorder' class='formfld'>\n";
        //echo "              <option></option>\n";
        if (strlen(htmlspecialchars($publicorder))> 0) {
                echo "              <option selected='yes' value='".htmlspecialchars($publicorder)."'>".htmlspecialchars($publicorder)."</option>\n";
        }
        $i=0;
        while($i<=999) {
          if (strlen($i) == 1) {
                echo "              <option value='00$i'>00$i</option>\n";
          }
          if (strlen($i) == 2) {
                echo "              <option value='0$i'>0$i</option>\n";
          }
          if (strlen($i) == 3) {
                echo "              <option value='$i'>$i</option>\n";
          }

          $i++;
        }
        echo "              </select>\n";
        //echo "  <input class='formfld' type='text' name='publicorder' maxlength='255' value='$publicorder'>\n";
        echo "<br />\n";
        echo "\n";
        echo "</td>\n";
        echo "</tr>\n";

	echo "<tr>\n";
	echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
	echo "    Enabled:\n";
	echo "</td>\n";
	echo "<td class='vtable' align='left'>\n";
	echo "    <select class='formfld' name='enabled'>\n";
	echo "    <option value=''></option>\n";
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
	echo "<td class='vtable' align='left'>\n";
	echo "    <textarea class='formfld' name='$description' rows='4'>$description</textarea>\n";
	echo "<br />\n";
	echo "\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "	<tr>\n";
	echo "		<td colspan='2' align='right'>\n";
	if ($action == "update") {
		echo "				<input type='hidden' name='caller_route_id' value='$caller_route_id'>\n";
		echo "                          <input type='hidden' name='public_include_id' value='$public_include_id'>\n";
	}
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
