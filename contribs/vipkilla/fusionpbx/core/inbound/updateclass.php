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
	T_Dot_Zilla <vipkilla@gmail.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2010
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
	T_Dot_Zilla <vipkilla@gmail.com>
*/
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (permission_exists("ingroup_add") ||
	permission_exists("ingroup_edit") || 
	permission_exists("ingroup_delete") ||
	ifgroup("superadmin")) {
	//access allowed
}
else {
	echo "access denied";
	return;
}

//get data from the db
	if (strlen($_REQUEST["id"])> 0) {
		$id = $_REQUEST["id"];
	}

//get the username from v_users
	$sql = "";
	$sql .= "select * from v_number_classes ";
	$sql .= "where id = '$id' ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$name = $row["name"];
		break; //limit to 1 row
	}
	unset ($prepstatement);

//required to be a superadmin to update an account that is a member of the superadmin group
	$superadminlist = superadminlist($db);
	if (ifsuperadmin($superadminlist, $username)) {
		if (!ifgroup("superadmin")) { 
			echo "access denied";
			return;
		}
	}

//delete the group from the user
/*
	if ($_GET["a"] == "delete" && permission_exists("ingroup_delete")) {
		//set the variables
			$groupid = check_str($_GET["groupid"]);
		//delete the group from the users
			$sql = "delete from v_group_members ";
			$sql .= "where v_id = '$v_id' ";
			$sql .= "and groupid = '$groupid' ";
			$sql .= "and username = '$username' ";
			$db->exec(check_sql($sql));
		//redirect the user
			require_once "includes/header.php";
			echo "<meta http-equiv=\"refresh\" content=\"2;url=updategroup.php?id=$id\">\n";
			echo "<div align='center'>Update Complete</div>";
			require_once "includes/footer.php";
			return;
	}
*/

if (count($_POST)>0 && $_POST["persistform"] != "1") {
	$id = $_REQUEST["id"];
	$description = check_str($_POST["description"]);
	$fee = check_str($_POST["fee"]);
	$fee_type = check_str($_POST["fee_type"]);
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
		if (strlen($_SESSION["v_template_name"]) == 0) {
			//set the session theme for the active user
			if ($_SESSION["username"] == $username) {
				$_SESSION["template_name"] = $ingroup_template_name;
			}
		}

	//sql update
		$sql  = "update v_number_classes set ";
		$sql .= "description = '$description', ";
		$sql .= "fee = '$fee', ";
		$sql .= "fee_type = '$fee_type' ";
		$sql .= "where id = '$id' ";
		$count = $db->exec(check_sql($sql));

	//clear the template so it will rebuild in case the template was changed
		$_SESSION["template_content"] = '';

	//redirect the user
		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=listclasses.php\">\n";
		echo "<div align='center'>Update Complete</div>";
		require_once "includes/footer.php";
		return;
}
else {
	$sql = "";
	$sql .= "select * from v_number_classes ";
	//allow admin access
	if (ifgroup("superadmin")) {
		if (strlen($id)> 0) {
			$sql .= "where id = '$id' ";
		}
	}
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	foreach ($result as &$row) {
		$description = $row["description"];
		$fee = $row["fee"];
		$fee_type = $row["fee_type"];
		break; //limit to 1 row
	}

}
//include the header
	require_once "includes/header.php";

//show the content
	$tablewidth ='width="100%"';
	echo "<form method='post' action=''>";
	echo "<br />\n";

	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr>\n";
	echo "<td>\n";

	echo "<table $tablewidth cellpadding='3' cellspacing='0' border='0'>";
	echo "<td align='left' width='90%' nowrap><b>Inbound Number Class Manager</b></td>\n";
	echo "<td nowrap='nowrap'>\n";
	echo "	<input type='submit' name='submit' class='btn' value='Save'>";
	echo "	<input type='button' class='btn' onclick=\"window.location='listclasses.php'\" value='Back'>";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align='left' colspan='2'>\n";
	echo "	Edit inbound group information. \n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<br />\n";

	echo "<table $tablewidth cellpadding='6' cellspacing='0' border='0'>";
	echo "<tr>\n";
	echo "	<th class='th' colspan='2' align='left'>Inbound Number Class Info</th>\n";
	echo "</tr>\n";

	echo "	<tr>";
	echo "		<td width='30%' class='vncellreq'>Name:</td>";
	echo "		<td width='70%' class='vtable'>$name</td>";
	echo "	</tr>";

	echo "	<tr>";
	echo "		<td class='vncell'>Description: </td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='description' value=\"$description\"></td>";
	echo "	</tr>";

        echo "  <tr>";
        echo "          <td class='vncell'>Fee:</td>";
        echo "          <td class='vtable'><input type='text' class='formfld' name='fee' value=\"$fee\"></td>";
        echo "  </tr>";

        echo "  <tr>";
        echo "          <td class='vncell'>Fee type:</td>";
        echo "          <td class='vtable'>";
        if($fee_type=='none')
                echo "          <input type='radio' name='fee_type' value='none' checked>None <br>";
        else
                echo "          <input type='radio' name='fee_type' value='none'>None <br>";
        if($fee_type=='onetime')
                echo "          <input type='radio' name='fee_type' value='onetime' checked>One Time <br>";
        else
                echo "          <input type='radio' name='fee_type' value='onetime'>One Time <br>";
        if($fee_type=='monthly')
                echo "          <input type='radio' name='fee_type' value='monthly' checked>Monthly <br>";
        else
                echo "          <input type='radio' name='fee_type' value='monthly'>Monthly <br>";
        if($fee_type=='quarterly')
                echo "          <input type='radio' name='fee_type' value='quarterly' checked>Quarterly <br>";
        else
                echo "          <input type='radio' name='fee_type' value='quarterly'>Quarterly <br>";
        if($fee_type=='annually')
                echo "          <input type='radio' name='fee_type' value='annually' checked>Annually ";
        else
                echo "          <input type='radio' name='fee_type' value='annually'>Annually ";
        echo "          </td>";
        echo "  </tr>";

	echo "</table>";

	echo "<br>";
	echo "<br>";
	echo "</div>";
	echo "</form>";

//include the footer
	require_once "includes/footer.php";

?>
