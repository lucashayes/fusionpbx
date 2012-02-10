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
require_once "includes/config.php";
require_once "includes/checkauth.php";
if (permission_exists('dialplan_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/header.php";
require_once "includes/paging.php";

//set the http values as php variables
	$order_by = $_GET["order_by"];
	$order = $_GET["order"];
	$dialplan_context = $_GET["dialplan_context"];

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "<td align=\"center\">\n";
	echo "<br />";

	echo "	<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "	<td align='left'>\n";
	echo "		<span class=\"vexpl\">\n";
	if ($dialplan_context == "public") {
		echo "			<strong>Inbound Call Routing</strong>\n";
	}
	else {
		echo "			<strong>Dialplan</strong>\n";
	}
	 	
	echo "		</span>\n";
	echo "	</td>\n";
	echo "	<td align='right'>\n";
	if (permission_exists('dialplan_advanced_view') && $dialplan_context != 'public') {
		echo "		<input type='button' class='btn' value='advanced' onclick=\"document.location.href='dialplan_advanced.php';\">\n";
	}
	else {
		echo "&nbsp;\n";
	}
	echo "	</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "	<td align='left' colspan='2'>\n";
	echo "		<span class=\"vexpl\">\n";
	if ($dialplan_context == "public") {
		echo "			The public dialplan is used to route incoming calls to destinations based on one \n";
		echo "			or more conditions and context. It can send incoming calls to an auto attendant, \n";
		echo "			huntgroup, extension, external number, or a script. Order is important when an \n";
		echo "			anti-action is used or when there are multiple conditions that match. \n";
	}
	else {
		if (ifgroup("superadmin")) {
			echo "			The dialplan is used to setup call destinations based on conditions and context.\n";
			echo "			You can use the dialplan to send calls to gateways, auto attendants, external numbers,\n";
			echo "			to scripts, or any destination.\n";
		}
		else {
			echo "			The dialplan provides a view of some of the feature codes, as well as the IVR Menu, \n";
			echo "			Conferences, Queues and other destinations.\n";
		}
	}
	echo "		</span>\n";
	echo "	</td>\n";
	echo "	</tr>\n";
	echo "	</table>";

	echo "	<br />";
	echo "	<br />";

	$sql = "";
	$sql .= "select * from v_dialplans ";
	$sql .= "where domain_uuid = '$domain_uuid' ";
	if ($dialplan_context == "public") {
		$sql .= "and dialplan_context = 'public' ";
	}
	else {
		$sql .= "and dialplan_context <> 'public' ";
	}
	if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; } else { $sql .= "order by dialplan_order asc, dialplan_name asc "; }
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	$num_rows = count($result);
	unset ($prep_statement, $result, $sql);

	$rows_per_page = 150;
	$param = "";
	$page = $_GET['page'];
	if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
	list($paging_controls, $rows_per_page, $var_3) = paging($num_rows, $param, $rows_per_page); 
	$offset = $rows_per_page * $page;

	$sql = "";
	$sql .= " select * from v_dialplans ";
	$sql .= " where domain_uuid = '$domain_uuid' ";
	if ($dialplan_context == "public") {
		$sql .= "and dialplan_context = 'public' ";
	}
	else {
		$sql .= "and dialplan_context <> 'public' ";
	}
	if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; } else { $sql .= "order by dialplan_order asc, dialplan_name asc "; }
	$sql .= " limit $rows_per_page offset $offset ";
	$prep_statement = $db->prepare(check_sql($sql));
	$prep_statement->execute();
	$result = $prep_statement->fetchAll();
	$result_count = count($result);
	unset ($prep_statement, $sql);

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";
	echo "<tr>\n";
	echo thorder_by('dialplan_name', 'Name', $order_by, $order);
	echo thorder_by('dialplan_number', 'Number', $order_by, $order);
	echo thorder_by('dialplan_order', 'Order', $order_by, $order);
	echo thorder_by('dialplan_enabled', 'Enabled', $order_by, $order);
	echo thorder_by('dialplan_description', 'Description', $order_by, $order);
	echo "<td align='right' width='42'>\n";
	if (permission_exists('dialplan_add')) {
		if ($dialplan_context == "public") {
			echo "	<a href='dialplan_public_add.php' alt='add'>$v_link_label_add</a>\n";
		}
		else {
			echo "	<a href='dialplan_add.php' alt='add'>$v_link_label_add</a>\n";
		}
	}
	echo "</td>\n";
	echo "<tr>\n";

	if ($result_count > 0) {
		foreach($result as $row) {
			if (strlen($row['dialplan_number']) == 0) {
				$sql = "";
				$sql .= "select * from v_dialplan_details ";
				$sql .= "where domain_uuid = '$domain_uuid' ";
				$sql .= "and dialplan_uuid = '".$row['dialplan_uuid']."' ";
				$sql .= "and dialplan_detail_type = 'destination_number' ";
				$prep_statement = $db->prepare(check_sql($sql));
				$prep_statement->execute();
				$tmp_result = $prep_statement->fetchAll();
				foreach ($tmp_result as &$tmp) {
					//prepare the extension number
						preg_match_all('/[\|0-9\*]/',$tmp["dialplan_detail_data"], $tmp_match);
						$dialplan_number = implode("",$tmp_match[0]);
						$dialplan_number = str_replace("|", " ", $dialplan_number);
						$row['dialplan_number'] = $dialplan_number;
					//update the extension number
						$sql = "update v_dialplans set ";
						$sql .= "dialplan_number = '$dialplan_number', ";
						$sql .= "where domain_uuid = '$domain_uuid' ";
						$sql .= "and dialplan_uuid = '".$row['dialplan_uuid']."'";
						$db->exec($sql);
						unset($sql);
					break; //limit to 1 row
				}
				unset ($prep_statement);
			}
	
			echo "<tr >\n";
			echo "   <td valign='top' class='".$row_style[$c]."'>&nbsp;&nbsp;".$row['dialplan_name']."</td>\n";
			echo "   <td valign='top' class='".$row_style[$c]."'>&nbsp;&nbsp;".$row['dialplan_number']."</td>\n";
			echo "   <td valign='top' class='".$row_style[$c]."'>&nbsp;&nbsp;".$row['dialplan_order']."</td>\n";
			echo "   <td valign='top' class='".$row_style[$c]."'>&nbsp;&nbsp;".$row['dialplan_enabled']."</td>\n";
			echo "   <td valign='top' class='row_stylebg' width='30%'>".$row['dialplan_description']."&nbsp;</td>\n";
			echo "   <td valign='top' align='right'>\n";
			if (permission_exists('dialplan_add')) {
				echo "		<a href='dialplan_edit.php?id=".$row['dialplan_uuid']."' alt='edit'>$v_link_label_edit</a>\n";
			}
			if (permission_exists('dialplan_edit')) {
				echo "		<a href='dialplan_delete.php?id=".$row['dialplan_uuid']."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
			}
			echo "   </td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $row_count);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='6'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$paging_controls</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	if (permission_exists('dialplan_add')) {
		if ($dialplan_context == "public") {
			echo "			<a href='dialplan_public_add.php' alt='add'>$v_link_label_add</a>\n";
		}
		else {
			echo "			<a href='dialplan_add.php' alt='add'>$v_link_label_add</a>\n";
		}
	}
	else {
		echo "			&nbsp;";
	}
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td colspan='5' align='left'>\n";
	echo "<br />\n";
	if ($v_path_show) {
		echo $_SESSION['switch']['dialplan']['dir'];
	}
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>";
	echo "</div>";
	echo "<br><br>";
	echo "<br><br>";

	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br><br>";

require_once "includes/footer.php";
unset ($result_count);
unset ($result);
unset ($key);
unset ($val);
unset ($c);
?>