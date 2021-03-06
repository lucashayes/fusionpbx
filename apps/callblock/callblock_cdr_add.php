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
	
	Callblock is written by Gerrit Visser <gerrit308@gmail.com>
*/
require_once "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";

if (permission_exists('callblock_edit') || permission_exists('callblock_add')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//action add from cdr
	if (isset($_REQUEST["cdr_id"])) {
		$action = "cdr_add";
		$cdr_uuid = check_str($_REQUEST["cdr_id"]);
	}

	// get the caller id info from cdr that user chose
	$sql = "select caller_id_name, caller_id_number from v_xml_cdr ";
	$sql .= "where uuid = '$cdr_uuid' ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetch();
	unset ($prep_statement);
	
	$blocked_caller_name = $result["caller_id_name"];
	$blocked_caller_number = $result["caller_id_number"];
	$block_call_enabled = "true";
	$block_call_action = "Reject";

	$sql = "insert into v_callblock ";
	$sql .= "(";
	$sql .= "domain_uuid, ";
	$sql .= "blocked_caller_uuid, ";
	$sql .= "blocked_caller_name, ";
	$sql .= "blocked_caller_number, ";
	$sql .= "blocked_call_count, ";
	$sql .= "blocked_call_action, ";
	$sql .= "block_call_enabled, ";
	$sql .= "date_added ";
	$sql .= ") ";
	$sql .= "values ";
	$sql .= "(";
	$sql .= "'$domain_uuid', ";
	$sql .= "'".uuid()."', ";
	$sql .= "'$blocked_caller_name', ";
	$sql .= "'$blocked_caller_number', ";
	$sql .= "0, ";
	$sql .= "'$block_call_action', ";
	$sql .= "'$block_call_enabled', ";
	$sql .= "'".time()."' ";
	$sql .= ")";
	$db->exec(check_sql($sql));
	unset($sql);

	require_once "includes/header.php";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=callblock.php\">\n";
	echo "<div align='center'>\n";
	echo "Add Complete\n";
	echo "</div>\n";
	require_once "includes/footer.php";
	return;

?>