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
if (permission_exists('outbound_route_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/header.php";
require_once "includes/paging.php";

$orderby = $_GET["orderby"];
$order = $_GET["order"];

//find the outbound routes from the dialplan include details

	//define the conference array
		$dialplan_array = array ();

	//get the outbound routes and set as the dialplan array
		$sql = "";
		$sql .= "select * from v_dialplan_includes_details ";
		$sql .= "where v_id = '$v_id' ";
		$sql .= "and (";
		$sql .= "field_data like '%sofia/gateway/%' ";
		$sql .= "or field_data like '%freetdm%' ";
		$sql .= "or field_data like '%openzap%' ";
		$sql .= "or field_data like '%dingaling%' ";
		$sql .= "or field_data like '%enum_auto_route%' ";
		$sql .= ") ";
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$x = 0;
		$result = $prepstatement->fetchAll();
		foreach ($result as &$row) {
			$dialplan_include_id = $row["dialplan_include_id"];
			//$tag = $row["tag"];
			//$field_order = $row["field_order"];
			//$field_type = $row["field_type"];
			//$field_data = $row["field_data"];
			$dialplan_array[$x]['dialplan_include_id'] = $dialplan_include_id;
			$x++;
		}
		unset ($prepstatement);

//show the content
	echo "<div align='center'>";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr class='border'>\n";
	echo "<td align=\"center\">\n";
	echo "<br />";

	echo "	<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "	<td align='left'><span class=\"vexpl\"><span class=\"red\"><strong>Outbound Routes\n";
	echo "		</strong></span></span>\n";
	echo "	</td>\n";
	echo "	<td align='right'>\n";
	//echo "		<input type='button' class='btn' value='advanced' onclick=\"document.location.href='v_dialplan_outbound.php';\">\n";
	echo "	</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "	<td align='left' colspan='2'>\n";
	echo "		<span class=\"vexpl\">\n";
	echo "			Outbound dialplans have one or more conditions that are matched to attributes of a call. ";
	echo "			When a call matches the conditions the call is then routed to the gateway.\n";
	echo "		</span>\n";
	echo "	</td>\n";
	echo "\n";
	echo "	</tr>\n";
	echo "	</table>";

	echo "	<br />";
	echo "	<br />";

	$sql = "";
	$sql .= " select * from v_dialplan_includes ";
	if (count($dialplan_array) == 0) {
		//when there are no outbound routes do this to hide all remaining entries
		$sql .= " where v_id = '$v_id' ";
		$sql .= " and context = 'hide' ";
	}
	else {
		$x = 0;
		foreach ($dialplan_array as &$row) {
			if ($x == 0) {
				$sql .= " where v_id = '$v_id' \n";
				$sql .= " and dialplan_include_id = '".$row['dialplan_include_id']."' \n";
			}
			else {
				$sql .= " or v_id = $v_id \n";
				$sql .= " and dialplan_include_id = '".$row['dialplan_include_id']."' \n";
			}
			$x++;
		}
	}
	if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; } else { $sql .= "order by dialplan_order, extension_name asc "; }
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$numrows = count($result);
	unset ($prepstatement, $result, $sql);

	$rowsperpage = 150;
	$param = "";
	$page = $_GET['page'];
	if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
	list($pagingcontrols, $rowsperpage, $var3) = paging($numrows, $param, $rowsperpage); 
	$offset = $rowsperpage * $page;

	$sql = "";
	$sql .= " select * from v_dialplan_includes ";
	if (count($dialplan_array) == 0) {
		//when there are no outbound routes do this to hide all remaining entries
		$sql .= " where v_id = '$v_id' ";
		$sql .= " and context = 'hide' ";
	}
	else {
		$x = 0;
		foreach ($dialplan_array as &$row) {
			if ($x == 0) {
				$sql .= " where v_id = '$v_id' \n";
				$sql .= " and dialplan_include_id = '".$row['dialplan_include_id']."' \n";
			}
			else {
				$sql .= " or v_id = $v_id \n";
				$sql .= " and dialplan_include_id = '".$row['dialplan_include_id']."' \n";
			}
			$x++;
		}
	}
	if (strlen($orderby)> 0) { $sql .= "order by $orderby $order "; } else { $sql .= "order by dialplan_order asc, extension_name asc "; }
	$sql .= " limit $rowsperpage offset $offset ";
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);
	unset ($prepstatement, $sql);

	$c = 0;
	$rowstyle["0"] = "rowstyle0";
	$rowstyle["1"] = "rowstyle1";

	echo "<div align='center'>\n";
	echo "<table width='100%' border='0' cellpadding='0' cellspacing='0'>\n";

	echo "<tr>\n";
	echo thorderby('extension_name', 'Name', $orderby, $order);
	echo thorderby('dialplan_order', 'Order', $orderby, $order);
	echo thorderby('enabled', 'Enabled', $orderby, $order);
	echo thorderby('descr', 'Description', $orderby, $order);
	if (permission_exists('outbound_route_edit')) {
		echo "<td align='right' width='42'>\n";
	}
	else {
		echo "<td align='right' width='21'>\n";
	}
	if (permission_exists('outbound_route_add')) {
		echo "	<a href='v_dialplan_outbound_add.php' alt='add'>$v_link_label_add</a>\n";
	}
	echo "</td>\n";
	echo "<tr>\n";

	if ($resultcount == 0) {
		//no results
	}
	else { //received results
		foreach($result as $row) {
			echo "<tr >\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[extension_name]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[dialplan_order]."</td>\n";
			echo "   <td valign='top' class='".$rowstyle[$c]."'>&nbsp;&nbsp;".$row[enabled]."</td>\n";
			echo "   <td valign='top' class='rowstylebg' width='30%'>".$row[descr]."&nbsp;</td>\n";
			echo "   <td valign='top' align='right'>\n";
			if (permission_exists('outbound_route_edit')) {
				echo "		<a href='v_dialplan_outbound_edit.php?id=".$row[dialplan_include_id]."' alt='edit'>$v_link_label_edit</a>\n";
			}
			if (permission_exists('outbound_route_delete')) {
				echo "		<a href='v_dialplan_outbound_delete.php?id=".$row[dialplan_include_id]."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
			}
			echo "   </td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);
	} //end if results

	echo "<tr>\n";
	echo "<td colspan='5'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$pagingcontrols</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	if (permission_exists('outbound_route_add')) {
		echo "			<a href='v_dialplan_outbound_add.php' alt='add'>$v_link_label_add</a>\n";
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
		echo $v_dialplan_default_dir;
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

//show the footer
	require_once "includes/footer.php";
?>
