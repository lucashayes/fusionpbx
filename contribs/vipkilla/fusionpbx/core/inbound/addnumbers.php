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

if (ifgroup("superadmin")) {
	//access allowed
}
else {
	echo "access denied";
	return;
}

$domain = check_str($_POST["domain"]);
$ingroup = check_str($_POST["ingroup"]);
$class = check_str($_POST["class"]);
$number1 = check_str($_POST["number1"]);
$number2 = check_str($_POST["number2"]);

if (count($_POST)>0 && check_str($_POST["persistform"]) != "1") {

	$msgerror = '';

	//--- begin captcha verification ---------------------
		//session_start(); //make sure sessions are started
		if (strtolower($_SESSION["captcha"]) != strtolower($_REQUEST["captcha"]) || strlen($_SESSION["captcha"]) == 0) {
			//$msgerror .= "Captcha Verification Failed<br>\n";
		}
		else {
			//echo "verified";
		}
	//--- end captcha verification -----------------------

	//username is already used.
	if (strlen($number1) == 0) {
		$msgerror .= "Please provide a first number.<br>\n";
	}
	else {
		if((strlen($number2) != 0) && ($number2 > $number1)) {
			$c = 0;
			for($number1;$number1<=$number2;$number1++) {
				$sql = "SELECT * FROM v_numbers ";
				$sql .= "where name = '$number1' ";
				$prepstatement = $db->prepare(check_sql($sql));
				$prepstatement->execute();
				if (!(count($prepstatement->fetchAll()) > 0)) {
					$numbers[$c] = $number1;
					$c++;
				}
			}
		} else {
			$sql = "SELECT * FROM v_numbers ";
			$sql .= "where name = '$number1' ";
			$prepstatement = $db->prepare(check_sql($sql));
			$prepstatement->execute();
			if (count($prepstatement->fetchAll()) > 0) {
				$msgerror .= "Please choose a number that is not already in system.<br>\n";
			}
		}
	}

	if (strlen($ingroup) == 0) { $msgerror .= "Please choose an ingroup.<br>\n"; }
	if (strlen($class) == 0) { $msgerror .= "Please choose a class.<br>\n"; }
	if (strlen($domain) == 0) { $msgerror .= "Please choose a domain.<br>\n"; }

	if (strlen($msgerror) > 0) {
		require_once "includes/header.php";
		echo "<div align='center'>";
		echo "<table><tr><td>";
		echo $msgerror;
		echo "</td></tr></table>";
		require_once "includes/persistform.php";
		echo persistform($_POST);
		echo "</div>";
		require_once "includes/footer.php";
		return;
	}

        $sql = "select v_id from v_system_settings where v_domain='$domain'";
        $prepstatement = $db->prepare(check_sql($sql));
        $prepstatement->execute();
        $result = $prepstatement->fetchAll();
        foreach($result as $field) {
                $t_id = $field[v_id];
        }

	if(isset($numbers)) {
		foreach($numbers as $key => $value) {
			$condition_expression_1 = "^$value$";
			$sql = "insert into v_numbers ";
			$sql .= "(";
			$sql .= "v_id, ";
			$sql .= "domain, ";
			$sql .= "ingroup, ";
			$sql .= "class, ";
			$sql .= "number, ";
			$sql .= "condition_expression_1 ";
			$sql .= ") ";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$t_id', ";
			$sql .= "'$domain', ";
			$sql .= "'$ingroup', ";
			$sql .= "'$class', ";
			$sql .= "'$value', ";
			$sql .= "'$condition_expression_1' ";
			$sql .= ")";
			$db->exec(check_sql($sql));
			unset($sql);
		}
	} else {
		$condition_expression_1 = "^$number1$";
		$sql = "insert into v_numbers ";
		$sql .= "(";
		$sql .= "v_id, ";
		$sql .= "domain, ";
		$sql .= "ingroup, ";
		$sql .= "class, ";
		$sql .= "number, ";
		$sql .= "condition_expression_1 ";
		$sql .= ") ";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'$t_id', ";
		$sql .= "'$domain', ";
		$sql .= "'$ingroup', ";
		$sql .= "'$class', ";
		$sql .= "'$number1', ";
		$sql .= "'$condition_expression_1' ";
		$sql .= ")";
		$db->exec(check_sql($sql));
		unset($sql);
	}

	//log the success
	//$logtype = 'user'; $logstatus='add'; $logadduser=$_SESSION["username"]; $logdesc= "username: ".$username." user added.";
	//logadd($db, $logtype, $logstatus, $logdesc, $logadduser, $_SERVER["REMOTE_ADDR"]);

	require_once "includes/header.php";
	if(isset($_GET["cid"])) {
		echo "<meta http-equiv=\"refresh\" content=\"3;url=listgroupnumbers.php?cid=".$ingroup."\">\n";
	} else {
		echo "<meta http-equiv=\"refresh\" content=\"3;url=listnumbers.php\">\n";
	}
	echo "<div align='center'>Add Complete</div>";
	require_once "includes/footer.php";
	return;
}

//show the header
	require_once "includes/header.php";

//show the content
	echo "<div align='center'>";
	echo "<table width='90%' border='0' cellpadding='0' cellspacing='2'>\n";
	echo "<tr>\n";
	echo "	<td align=\"left\">\n";
	echo "      <br>";

	$tablewidth ='width="100%"';
	echo "<form method='post' action=''>";
	echo "<div class='borderlight' style='padding:10px;'>\n";

	echo "<table border='0' $tablewidth cellpadding='6' cellspacing='0'>";
	echo "	<tr>\n";
	echo "		<td width='80%'>\n";
	echo "			<b>To add numbers, please fill out this form completely. </b><br>";
	echo "		</td>\n";
	echo "		<td width='20%' align='right'>\n";
	echo "			<input type='button' class='btn' name='back' alt='back' onclick=\"window.history.back()\" value='Back'>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";

	echo "<table border='0' $tablewidth cellpadding='6' cellspacing='0'>";
	echo "	<tr>";
	echo "		<td class='vncellreq' width='40%'>First number in range: </td>";
	echo "		<td class='vtable' width='60%'><input type='text' class='formfld' name='number1' value='$number1'></td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncellreq'>Last number in range:<br />Leave blank if single number.</td>";
	echo "		<td class='vtable'><input type='text' class='formfld' name='number2' value='$number2'></td>";
	echo "	</tr>";

        echo "  <tr>";
        echo "          <td class='vncellreq'>Inbound Group:</td>";
        echo "          <td class='vtable'>";

        $sql = "select name from v_ingroups order by name";
        $prepstatement = $db->prepare(check_sql($sql));
        $prepstatement->execute();

        echo "                  <select name=\"ingroup\" style='width: 200px;' class='formfld'>\n";
        echo "                          <option value=\"\"></option>\n";
        $result = $prepstatement->fetchAll();
        foreach($result as $field) {
                echo "                  <option value='".$field[name]."'>".$field[name]."</option>\n";
        }
        echo "                  </select>";
        unset($sql, $result);

        echo "          </td>";
        echo "  </tr>";


	echo "	<tr>";
	echo "		<td class='vncellreq'>Class:</td>";
	echo "		<td class='vtable'>";

        $sql = "select name from v_number_classes order by name";
        $prepstatement = $db->prepare(check_sql($sql));
        $prepstatement->execute();

        echo "                  <select name=\"class\" style='width: 200px;' class='formfld'>\n";
        echo "                          <option value=\"\"></option>\n";
        $result = $prepstatement->fetchAll();
        foreach($result as $field) {
                echo "                  <option value='".$field[name]."'>".$field[name]."</option>\n";
        }
        echo "                  </select>";
        unset($sql, $result);

	echo "		</td>";
	echo "	</tr>";
	echo "	<tr>";
	echo "		<td class='vncellreq'>Assign to domain:</td>";
	echo "		<td class='vtable'>";

        $sql = "select v_domain from v_system_settings order by v_domain";
        $prepstatement = $db->prepare(check_sql($sql));
        $prepstatement->execute();

	echo "			<select name=\"domain\" style='width: 200px;' class='formfld'>\n";
        echo "				<option value=\"\"></option>\n";
        $result = $prepstatement->fetchAll();
        foreach($result as $field) {
		echo "			<option value='".$field[v_domain]."'>".$field[v_domain]."</option>\n";
	}
        echo "			</select>";
        unset($sql, $result);

	echo "		</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";

	echo "<div class='' style='padding:10px;'>\n";
	echo "<table $tablewidth>";
	echo "	<tr>";
	echo "		<td colspan='2' align='right'>";
	echo "       <input type='submit' name='submit' class='btn' value='Add Numbers'>";
	echo "		</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</form>";

	echo "	</td>";
	echo "	</tr>";
	echo "</table>";
	echo "</div>";

//show the footer
	require_once "includes/footer.php";
?>
