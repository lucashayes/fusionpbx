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

//normalize the mac address
	$sql = "select hardware_phone_uuid, phone_mac_address ";
	$sql .= "from v_hardware_phones ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	$sql .= "and (phone_mac_address like '%-%' or phone_mac_address like '%:%') ";
	$prep_statement = $db->prepare(check_sql($sql));
	if ($prep_statement) {
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		foreach($result as $row) {
			$hardware_phone_uuid = $row["hardware_phone_uuid"];
			$phone_mac_address = $row["phone_mac_address"];
			$phone_mac_address = strtolower($phone_mac_address);
			$phone_mac_address = preg_replace('#[^a-fA-F0-9./]#', '', $phone_mac_address);

			$sql = "update v_hardware_phones set ";
			$sql .= "phone_mac_address = '".$phone_mac_address."' ";
			$sql .= "where hardware_phone_uuid = '".$hardware_phone_uuid."' ";
			$db->exec(check_sql($sql));
			unset($sql);
		}
	}
	unset($prep_statement, $result);

//move the dynamic provision variables that from v_vars table to v_default_settings
	if (count($_SESSION['provision']) == 0 && $domains_processed == 1) {
		$sql = "select * from v_vars ";
		$sql .= "where var_cat = 'Provision' ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		foreach ($result as &$row) {
			$sql = "insert into v_default_settings ";
			$sql .= "(";
			$sql .= "default_setting_uuid, ";
			$sql .= "default_setting_category, ";
			$sql .= "default_setting_subcategory, ";
			$sql .= "default_setting_name, ";
			$sql .= "default_setting_value, ";
			$sql .= "default_setting_enabled, ";
			$sql .= "default_setting_description ";
			$sql .= ") ";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'".uuid()."', ";
			$sql .= "'provision', ";
			$sql .= "'".check_str($row['var_name'])."', ";
			$sql .= "'var', ";
			$sql .= "'".check_str($row['var_value'])."', ";
			$sql .= "'".check_str($row['var_enabled'])."', ";
			$sql .= "'".check_str($row['var_description'])."' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);
		}
		//delete the provision variables from system -> variables
		//$sql = "delete from v_vars ";
		//$sql .= "where var_cat = 'Provision' ";
		//echo $sql ."\n";
		//$db->exec(check_sql($sql));
		//echo "$var_name $var_value \n";
	}

?>