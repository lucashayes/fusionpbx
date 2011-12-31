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
require_once "config.php";
if (permission_exists('content_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}


if (count($_POST)>0) {
	$rss_sub_id = check_str($_POST["rss_sub_id"]);
	$rss_id = check_str($_POST["rss_id"]);
	$rss_sub_title = check_str($_POST["rss_sub_title"]);
	$rss_sub_link = check_str($_POST["rss_sub_link"]);
	$rss_sub_desc = check_str($_POST["rss_sub_desc"]);
	$rss_sub_optional_1 = check_str($_POST["rss_sub_optional_1"]);
	$rss_sub_optional_2 = check_str($_POST["rss_sub_optional_2"]);
	$rss_sub_optional_3 = check_str($_POST["rss_sub_optional_3"]);
	$rss_sub_optional_4 = check_str($_POST["rss_sub_optional_4"]);
	$rss_sub_optional_5 = check_str($_POST["rss_sub_optional_5"]);
	$rss_sub_add_date = check_str($_POST["rss_sub_add_date"]);
	$rss_sub_add_user = check_str($_POST["rss_sub_add_user"]);


	require_once "includes/header.php";

	echo "<div align='center'>";
	echo "<table border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";


	$sql = "";
	$sql .= "select * from v_rss_sub ";
	$sql .= "where ";
	if (strlen($v_id) > 0) { $sql .= "and rss_sub_id = '$v_id' "; }
	if (strlen($rss_sub_id) > 0) { $sql .= "and rss_sub_id like '%$rss_sub_id%' "; }
	if (strlen($rss_id) > 0) { $sql .= "and rss_id like '%$rss_id%' "; }
	if (strlen($rss_sub_title) > 0) { $sql .= "and rss_sub_title like '%$rss_sub_title%' "; }
	if (strlen($rss_sub_link) > 0) { $sql .= "and rss_sub_link like '%$rss_sub_link%' "; }
	if (strlen($rss_sub_desc) > 0) { $sql .= "and rss_sub_desc like '%$rss_sub_desc%' "; }
	if (strlen($rss_sub_optional_1) > 0) { $sql .= "and rss_sub_optional_1 like '%$rss_sub_optional_1%' "; }
	if (strlen($rss_sub_optional_2) > 0) { $sql .= "and rss_sub_optional_2 like '%$rss_sub_optional_2%' "; }
	if (strlen($rss_sub_optional_3) > 0) { $sql .= "and rss_sub_optional_3 like '%$rss_sub_optional_3%' "; }
	if (strlen($rss_sub_optional_4) > 0) { $sql .= "and rss_sub_optional_4 like '%$rss_sub_optional_4%' "; }
	if (strlen($rss_sub_optional_5) > 0) { $sql .= "and rss_sub_optional_5 like '%$rss_sub_optional_5%' "; }
	if (strlen($rss_sub_add_date) > 0) { $sql .= "and rss_sub_add_date like '%$rss_sub_add_date%' "; }
	if (strlen($rss_sub_add_user) > 0) { $sql .= "and rss_sub_add_user like '%$rss_sub_add_user%' "; }
	$sql .= "and length(rss_sub_del_date) = 0 ";
	$sql .= "or ";
	if (strlen($v_id) > 0) { $sql .= "and rss_sub_id = '$v_id' "; }
	if (strlen($rss_sub_id) > 0) { $sql .= "and rss_sub_id like '%$rss_sub_id%' "; }
	if (strlen($rss_id) > 0) { $sql .= "and rss_id like '%$rss_id%' "; }
	if (strlen($rss_sub_title) > 0) { $sql .= "and rss_sub_title like '%$rss_sub_title%' "; }
	if (strlen($rss_sub_link) > 0) { $sql .= "and rss_sub_link like '%$rss_sub_link%' "; }
	if (strlen($rss_sub_desc) > 0) { $sql .= "and rss_sub_desc like '%$rss_sub_desc%' "; }
	if (strlen($rss_sub_optional_1) > 0) { $sql .= "and rss_sub_optional_1 like '%$rss_sub_optional_1%' "; }
	if (strlen($rss_sub_optional_2) > 0) { $sql .= "and rss_sub_optional_2 like '%$rss_sub_optional_2%' "; }
	if (strlen($rss_sub_optional_3) > 0) { $sql .= "and rss_sub_optional_3 like '%$rss_sub_optional_3%' "; }
	if (strlen($rss_sub_optional_4) > 0) { $sql .= "and rss_sub_optional_4 like '%$rss_sub_optional_4%' "; }
	if (strlen($rss_sub_optional_5) > 0) { $sql .= "and rss_sub_optional_5 like '%$rss_sub_optional_5%' "; }
	if (strlen($rss_sub_add_date) > 0) { $sql .= "and rss_sub_add_date like '%$rss_sub_add_date%' "; }
	if (strlen($rss_sub_add_user) > 0) { $sql .= "and rss_sub_add_user like '%$rss_sub_add_user%' "; }
	$sql .= "and rss_sub_del_date is null ";

	$sql = trim($sql);
	if (substr($sql, -5) == "where"){ $sql = substr($sql, 0, (strlen($sql)-5)); }
	if (substr($sql, -3) == " or"){ $sql = substr($sql, 0, (strlen($sql)-5)); }
	$sql = str_replace ("where and", "where", $sql);
	$sql = str_replace ("or and", "or", $sql);

	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	$resultcount = count($result);

	$c = 0;
	$rowstyle["0"] = "background-color: #F5F5DC;";
	$rowstyle["1"] = "background-color: #FFFFFF;";

	echo "<b>Search Results</b><br>";
	echo "<div align='left'>\n";
	echo "<table border='0' cellpadding='1' cellspacing='1'>\n";
	echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>";

	if ($resultcount == 0) { //no results
		echo "<tr><td>&nbsp;</td></tr>";
	}
	else { //received results

		echo "<tr>";
		  echo "<th nowrap>&nbsp; &nbsp; Sub ID&nbsp; &nbsp; </th>";
		  echo "<th nowrap>&nbsp; &nbsp; Id&nbsp; &nbsp; </th>";
		  echo "<th nowrap>&nbsp; &nbsp; Title&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; Link&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; rss_sub_desc&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; rss_sub_optional_1&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; rss_sub_optional_2&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; rss_sub_optional_3&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; rss_sub_optional_4&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; rss_sub_optional_5&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; rss_sub_add_date&nbsp; &nbsp; </th>";
		  //echo "<th nowrap>&nbsp; &nbsp; rss_sub_add_user&nbsp; &nbsp; </th>";
		echo "</tr>";
		echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";

		foreach($result as $row) {
		//print_r( $row );
			echo "<tr style='".$rowstyle[$c]."'>\n";
				echo "<td valign='top'><a href='rsssubupdate.php?rss_sub_id=".$row[rss_sub_id]."'>".$row[rss_sub_id]."</a></td>";
				echo "<td valign='top'>".$row[rss_id]."</td>";
				echo "<td valign='top'>".$row[rss_sub_title]."</td>";
				//echo "<td valign='top'>".$row[rss_sub_link]."</td>";
				//echo "<td valign='top'>".$row[rss_sub_desc]."</td>";
				//echo "<td valign='top'>".$row[rss_sub_optional_1]."</td>";
				//echo "<td valign='top'>".$row[rss_sub_optional_2]."</td>";
				//echo "<td valign='top'>".$row[rss_sub_optional_3]."</td>";
				//echo "<td valign='top'>".$row[rss_sub_optional_4]."</td>";
				//echo "<td valign='top'>".$row[rss_sub_optional_5]."</td>";
				//echo "<td valign='top'>".$row[rss_sub_add_date]."</td>";
				//echo "<td valign='top'>".$row[rss_sub_add_user]."</td>";
			echo "</tr>";

			echo "<tr><td colspan='100%'><img src='/images/spacer.gif' width='100%' height='1' style='background-color: #BBBBBB;'></td></tr>\n";
			if ($c==0) { $c=1; } else { $c=0; }
		} //end foreach
		unset($sql, $result, $rowcount);

		echo "</table>\n";
		echo "</div>\n";


		echo "  <br><br>";
		echo "  </td>\n";
		echo "</tr>\n";

	} //end if results

	echo "</table>\n";
	echo "</div>";

	echo "<br><br>";
	require_once "includes/footer.php";

	unset ($resultcount);
	unset ($result);
	unset ($key);
	unset ($val);
	unset ($c);

	}
	else {

		echo "\n";    require_once "includes/header.php";
	echo "<div align='center'>";
	echo "<table border='0' cellpadding='0' cellspacing='2'>\n";

	echo "<tr class='border'>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";


	echo "<form method='post' action=''>";
	echo "<table>";
	  echo "	<tr>";
	  echo "		<td>Sub ID:</td>";
	  echo "		<td><input type='text' class='txt' name='rss_sub_id'></td>";
	  echo "	</tr>";
	  echo "	<tr>";
	  echo "		<td>ID:</td>";
	  echo "		<td><input type='text' class='txt' name='rss_id'></td>";
	  echo "	</tr>";
	  echo "	<tr>";
	  echo "		<td>Sub Title:</td>";
	  echo "		<td><input type='text' class='txt' name='rss_sub_title'></td>";
	  echo "	</tr>";
	  echo "	<tr>";
	  echo "		<td>Sub Link:</td>";
	  echo "		<td><input type='text' class='txt' name='rss_sub_link'></td>";
	  echo "	</tr>";
	  echo "	<tr>";
	  echo "		<td>Sub Desc:</td>";
	  echo "		<td><input type='text' class='txt' name='rss_sub_desc'></td>";
	  echo "	</tr>";
	  //echo "	<tr>";
	  //echo "		<td>rss_sub_optional_1:</td>";
	  //echo "		<td><input type='text' class='txt' name='rss_sub_optional_1'></td>";
	  //echo "	</tr>";
	  //echo "	<tr>";
	  //echo "		<td>rss_sub_optional_2:</td>";
	  //echo "		<td><input type='text' class='txt' name='rss_sub_optional_2'></td>";
	  //echo "	</tr>";
	  //echo "	<tr>";
	  //echo "		<td>rss_sub_optional_3:</td>";
	  //echo "		<td><input type='text' class='txt' name='rss_sub_optional_3'></td>";
	  //echo "	</tr>";
	  //echo "	<tr>";
	  //echo "		<td>rss_sub_optional_4:</td>";
	  //echo "		<td><input type='text' class='txt' name='rss_sub_optional_4'></td>";
	  //echo "	</tr>";
	  //echo "	<tr>";
	  //echo "		<td>rss_sub_optional_5:</td>";
	  //echo "		<td><input type='text' class='txt' name='rss_sub_optional_5'></td>";
	  //echo "	</tr>";
	  //echo "	<tr>";
	  //echo "		<td>rss_sub_add_date:</td>";
	  //echo "		<td><input type='text' class='txt' name='rss_sub_add_date'></td>";
	  //echo "	</tr>";
	  //echo "	<tr>";
	  //echo "		<td>rss_sub_add_user:</td>";
	  //echo "		<td><input type='text' class='txt' name='rss_sub_add_user'></td>";
	  //echo "	</tr>";
	echo "	<tr>";
	echo "		<td colspan='2' align='right'><input type='submit' name='submit' class='btn' value='Search'></td>";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";


	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";


require_once "includes/footer.php";

} //end if not post
?>
