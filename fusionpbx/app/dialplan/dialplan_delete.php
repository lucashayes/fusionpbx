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
if (permission_exists('dialplan_delete') 
	|| permission_exists('inbound_route_delete')
	|| permission_exists('outbound_route_delete')
	|| permission_exists('time_conditions_delete')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

if (count($_GET)>0) {
    $dialplan_uuid = check_str($_GET["id"]);
}

if (strlen($dialplan_uuid)>0) {
	//get the dialplan data
		$sql = "";
		$sql .= "select * from v_dialplans ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll();
		foreach ($result as &$row) {
			$database_dialplan_uuid = $row["dialplan_uuid"];
			$dialplan_context = $row["dialplan_context"];
			break; //limit to 1 row
		}
		unset ($prep_statement);

	//start the atomic transaction
		$count = $db->exec("BEGIN;");

	//delete child data
		$sql = "";
		$sql .= "delete from v_dialplan_details ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
		$db->query($sql);
		unset($sql);

	//delete parent data
		$sql = "";
		$sql .= "delete from v_dialplans ";
		$sql .= "where domain_uuid = '$domain_uuid' ";
		$sql .= "and dialplan_uuid = '$dialplan_uuid' ";
		$db->query($sql);
		unset($sql);

	//commit the atomic transaction
		$count = $db->exec("COMMIT;");

	//synchronize the xml config
		sync_package_v_dialplan();
}

//redirect the user
	require_once "includes/header.php";
	if ($dialplan_context == "public") {
		echo "<meta http-equiv=\"refresh\" content=\"2;url=dialplans.php?dialplan_context=public\">\n";
	}
	else {
		echo "<meta http-equiv=\"refresh\" content=\"2;url=dialplans.php\">\n";
	}
	echo "<div align='center'>\n";
	echo "Delete Complete\n";
	echo "</div>\n";

	require_once "includes/footer.php";
	return;
?>