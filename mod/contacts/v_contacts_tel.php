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
require_once "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";
if (permission_exists('contacts_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}
require_once "includes/header.php";
require_once "includes/paging.php";

//get variables used to control the order
	$order_by = $_GET["order_by"];
	$order = $_GET["order"];

//show the content
	//echo "<div align='center'>";
	//echo "<table width='100%' border='0' cellpadding='0' cellspacing='2'>\n";
	//echo "<tr class='border'>\n";
	//echo "	<td align=\"center\">\n";
	//echo "		<br>";

	echo "<table width='100%' border='0'>\n";
	echo "<tr>\n";
	echo "<td width='50%' nowrap><b>Phone Numbers</b></td>\n";
	echo "<td width='50%' align='right'>&nbsp;</td>\n";
	echo "</tr>\n";
	//echo "<tr>\n";
	//echo "<td colspan='2'>\n";
	//echo "	List of phone numbers for the contact.<br /><br />\n";
	//echo "</td>\n";
	//echo "</tr>\n";
	echo "</table>\n";

	//prepare to page the results
		$sql = "";
		$sql .= " select count(*) as num_rows from v_contacts_tel ";
		$sql .= " where domain_uuid = '$domain_uuid' ";
		$sql .= " and contact_uuid = '$contact_uuid' ";
		if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; }
		$prep_statement = $db->prepare($sql);
		if ($prep_statement) {
		$prep_statement->execute();
			$row = $prep_statement->fetch(PDO::FETCH_ASSOC);
			if ($row['num_rows'] > 0) {
				$num_rows = $row['num_rows'];
			}
			else {
				$num_rows = '0';
			}
		}

	//prepare to page the results
		$rows_per_page = 10;
		$param = "";
		$page = $_GET['page'];
		if (strlen($page) == 0) { $page = 0; $_GET['page'] = 0; } 
		list($paging_controls, $rows_per_page, $var_3) = paging($num_rows, $param, $rows_per_page); 
		$offset = $rows_per_page * $page; 

	//get the contact list
		$sql = "";
		$sql .= " select * from v_contacts_tel ";
		$sql .= " where domain_uuid = '$domain_uuid' ";
		$sql .= " and contact_uuid = '$contact_uuid' ";
		if (strlen($order_by)> 0) { $sql .= "order by $order_by $order "; }
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
	echo thorder_by('tel_type', 'Type', $order_by, $order);
	echo thorder_by('tel_number', 'Numbers', $order_by, $order);
	echo "<th>Tools</th>\n";
	echo "<td align='right' width='42'>\n";
	echo "	<a href='v_contacts_tel_edit.php?contact_uuid=".$_GET['id']."' alt='add'>$v_link_label_add</a>\n";
	echo "</td>\n";
	echo "<tr>\n";

	if ($result_count > 0) {
		foreach($result as $row) {
			echo "<tr >\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".$row['tel_type']."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'>".format_phone($row['tel_number'])."&nbsp;</td>\n";
			echo "	<td valign='top' class='".$row_style[$c]."'><a href=\"/mod/xml_cdr/v_xml_cdr.php?caller_id_number=".$row['tel_number']."&destination_number=".$row['tel_number']."\">CDR</a>&nbsp;</td>\n";
			echo "	<td valign='top' align='right'>\n";
			echo "		<a href='v_contacts_tel_edit.php?contact_uuid=".$row['contact_uuid']."&id=".$row['contact_tel_uuid']."' alt='edit'>$v_link_label_edit</a>\n";
			echo "		<a href='v_contacts_tel_delete.php?contact_uuid=".$row['contact_uuid']."&id=".$row['contact_tel_uuid']."' alt='delete' onclick=\"return confirm('Do you really want to delete this?')\">$v_link_label_delete</a>\n";
			echo "	</td>\n";
			echo "</tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $row_count);
	} //end if results


	echo "<tr>\n";
	echo "<td colspan='4' align='left'>\n";
	echo "	<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "	<tr>\n";
	echo "		<td width='33.3%' nowrap>&nbsp;</td>\n";
	echo "		<td width='33.3%' align='center' nowrap>$paging_controls</td>\n";
	echo "		<td width='33.3%' align='right'>\n";
	echo "			<a href='v_contacts_tel_edit.php?contact_uuid=".$_GET['id']."' alt='add'>$v_link_label_add</a>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
 	echo "	</table>\n";
	echo "</td>\n";
	echo "</tr>\n";

	echo "</table>";
	echo "</div>";

	//echo "</td>";
	//echo "</tr>";
	//echo "</table>";
	//echo "</div>";
	//echo "<br><br>";

//include the footer
	//require_once "includes/footer.php";
?>