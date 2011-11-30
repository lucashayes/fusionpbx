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
if (permission_exists('ingroup_delete')) {
	//access allowed
}
else {
	echo "access denied";
	return;
}

//get the id
	$id = check_str($_GET["id"]);

//required to be a superadmin to delete a member of the superadmin group
	$superadminlist = superadminlist($db);
	if (ifsuperadmin($superadminlist, $username)) {
		if (!ifgroup("superadmin")) { 
			echo "access denied";
			return;
		}
	}

//delete any dialplan entries for the number
        $sql = "select v_id, public_include_id from v_numbers where id='".$id."'";
        $prepstatement = $db->prepare(check_sql($sql));
        $prepstatement->execute();
        $result = $prepstatement->fetchAll();
        foreach ($result as &$row) {
                $v_id = $row["v_id"];
                $public_include_id = $row["public_include_id"];
                break;
        }
	unset($sql,$prepstatement);
        $sqldelete = "delete from v_public_includes_details where public_include_id='".$public_include_id."' and v_id='".$v_id."'";
        if (!$db->exec($sqldelete)) {
                $info = $db->errorInfo();
                print_r($info);
        }

        $sqldelete = "delete from v_public_includes where public_include_id='".$public_include_id."' and v_id='".$v_id."'";
        if (!$db->exec($sqldelete)) {
                $info = $db->errorInfo();
                print_r($info);
        }
	sync_package_v_public_includes();

//delete the groups the user is assigned to
        $sqldelete = "delete from v_numbers ";
        $sqldelete .= "where id = '$id' ";
        if (!$db->exec($sqldelete)) {
                $info = $db->errorInfo();
                print_r($info);
        }

//redirect the user
	if(strlen($_GET["ingroup"])) {
		header("Location: listgroupnumbers.php?cid=".$_GET["ingroup"]);
	} else {
		header("Location: listnumbers.php");
	}

?>
