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
	E. Schmidbauer <e.schmidbauer@gmail.com>
*/
include "root.php";
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (permission_exists('extension_delete')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

if (count($_GET)>0) {
	$id = $_GET["id"];
	$lua_action_id = $_GET["lua_action_id"];
	$fc = $_GET["fc"];
}

//delete the extension and lua_route for extension
	if ((strlen($id)>0) && strlen($lua_action_id)) {
		$sql = "";
		$sql .= "delete from v_extensions ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and extension_id = '$id' ";
		$sql .= "and lua_action_id = '$lua_action_id' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		unset($sql);
		
		dialplan_lua_delete_route($lua_action_id);
		//syncrhonize configuration
		sync_package_v_extensions();
	}

	if (strlen($fc)>0) {
		$sql = "";
		$sql .= "delete from v_feature_codes ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and feature_table = 'v_extensions' ";
		$sql .= "and feature_code_id = '$fc' ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		unset($sql);
                require_once "includes/header.php";
                echo "<meta http-equiv=\"refresh\" content=\"2;url=/mod/feature_codes/index.php\">\n";
                echo "<br />\n";
                echo "<div align='center'>\n";
                echo "  <table width='40%'>\n";
                echo "          <tr>\n";
                echo "                  <th align='left'>Message</th>\n";
                echo "          </tr>\n";
                echo "          <tr>\n";
                echo "                  <td class='rowstyle1'><strong>Delete Complete</strong></td>\n";
                echo "          </tr>\n";
                echo "  </table>\n";
                echo "  <br />\n";
                echo "</div>\n";
                require_once "includes/footer.php";
                return;
	} else {
//redirect the user
		require_once "includes/header.php";
		echo "<meta http-equiv=\"refresh\" content=\"2;url=v_extensions.php\">\n";
		echo "<br />\n";
		echo "<div align='center'>\n";
		echo "	<table width='40%'>\n";
		echo "		<tr>\n";
		echo "			<th align='left'>Message</th>\n";
		echo "		</tr>\n";
		echo "		<tr>\n";
		echo "			<td class='rowstyle1'><strong>Delete Complete</strong></td>\n";
		echo "		</tr>\n";
		echo "	</table>\n";
		echo "	<br />\n";
		echo "</div>\n";
		require_once "includes/footer.php";
		return;
	}

?>

