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
if (permission_exists('music_on_hold_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

require_once "includes/paging.php";

$sampling_rate_dirs = Array(8000, 16000, 32000, 48000);
$dir_music_on_hold = $_SESSION['switch']['sounds']['dir'].'/music';
ini_set(max_execution_time,7200);

$order_by = $_GET["order_by"];
$order = $_GET["order"];

if ($_GET['a'] == "download") {
	$category_folder = $_GET['category'];
	$sampling_rate_dir = $_GET['sampling_rate'];

	if ($category_folder != '') {
		$path_mod = $category_folder."/";

		if (count($_SESSION['domains']) > 1) {
			$path_mod = $_SESSION["domain_name"]."/".$path_mod;
		}
	}

	session_cache_limiter('public');
	if ($_GET['type'] = "moh") {
		if (file_exists($dir_music_on_hold."/".$path_mod.$sampling_rate_dir."/".base64_decode($_GET['filename']))) {
			$fd = fopen($dir_music_on_hold."/".$path_mod.$sampling_rate_dir."/".base64_decode($_GET['filename']), "rb");
			if ($_GET['t'] == "bin") {
				header("Content-Type: application/force-download");
				header("Content-Type: application/octet-stream");
				header("Content-Type: application/download");
				header("Content-Description: File Transfer");
				header('Content-Disposition: attachment; filename="'.base64_decode($_GET['filename']).'"');
			}
			else {
				$file_ext = substr(base64_decode($_GET['filename']), -3);
				if ($file_ext == "wav") {
					header("Content-Type: audio/x-wav");
				}
				if ($file_ext == "mp3") {
					header("Content-Type: audio/mp3");
				}
			}
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
			header("Content-Length: " . filesize($dir_music_on_hold."/".$path_mod.$sampling_rate_dir."/".base64_decode($_GET['filename'])));
			fpassthru($fd);
		}
	}
	exit;
}


if (($_POST['submit'] == "Upload") && is_uploaded_file($_FILES['upload_file']['tmp_name'])) {
	$file_ext = strtolower(pathinfo($_FILES['upload_file']['name'], PATHINFO_EXTENSION));
	if ($file_ext == 'wav' || $file_ext == 'mp3') {
		if ($_POST['type'] == 'moh' && permission_exists('music_on_hold_add')) {

			$new_file_name = str_replace(' ', '-', $_FILES['upload_file']['name']); // replace any spaces in the filename with dashes

			$sampling_rate_dir = $_POST['upload_sampling_rate'] * 1000; // convert sampling rate from value passed by form

			if (count($_SESSION['domains']) > 1) {
				$path_mod = $_SESSION["domain_name"]."/"; // if multi-tenant, modify folder paths
			}

			// create new category, if necessary
			if ($_POST['upload_category'] == '_NEW_CAT_' && $_POST['upload_category_new'] != '') {
				$new_category_name = str_replace(' ', '_', $_POST['upload_category_new']);
				if (!is_dir($dir_music_on_hold."/".$path_mod.$new_category_name."/".$sampling_rate_dir)) {
					@mkdir($dir_music_on_hold."/".$path_mod.$new_category_name."/".$sampling_rate_dir, 0777, true);
				}
				if (is_dir($dir_music_on_hold."/".$path_mod.$new_category_name."/".$sampling_rate_dir)) {
					move_uploaded_file($_FILES['upload_file']['tmp_name'], $dir_music_on_hold."/".$path_mod.$new_category_name."/".$sampling_rate_dir."/".$new_file_name);
					$target_folder = $dir_music_on_hold."/".$path_mod.$new_category_name."/".$sampling_rate_dir;
				}
			}
			// use existing category folder
			else if ($_POST['upload_category'] != '' && $_POST['upload_category'] != '_NEW_CAT_') {
				if (!is_dir($dir_music_on_hold."/".$path_mod.$_POST['upload_category']."/".$sampling_rate_dir)) {
					@mkdir($dir_music_on_hold."/".$path_mod.$_POST['upload_category']."/".$sampling_rate_dir, 0777, true);
				}
				if (is_dir($dir_music_on_hold."/".$path_mod.$_POST['upload_category']."/".$sampling_rate_dir)) {
					move_uploaded_file($_FILES['upload_file']['tmp_name'], $dir_music_on_hold."/".$path_mod.$_POST['upload_category']."/".$sampling_rate_dir."/".$new_file_name);
					$target_folder = $dir_music_on_hold."/".$path_mod.$_POST['upload_category']."/".$sampling_rate_dir;
				}
			}
			// use default folder
			else if ($_POST['upload_category'] == '') {
				if (!is_dir($dir_music_on_hold."/".$sampling_rate_dir)) {
					@mkdir($dir_music_on_hold."/".$sampling_rate_dir, 0777, true);
				}
				if (is_dir($dir_music_on_hold."/".$sampling_rate_dir)) {
					move_uploaded_file($_FILES['upload_file']['tmp_name'], $dir_music_on_hold."/".$sampling_rate_dir."/".$new_file_name);
					$target_folder = $dir_music_on_hold."/".$sampling_rate_dir;
				}
			}
			else { exit(); }

			$savemsg = "Uploaded file to ".$target_folder."/".htmlentities($_FILES['upload_file']['name']);
			unset($_POST['txtCommand']);
		}
	}
}

if ($_GET['act'] == "del" && permission_exists('music_on_hold_delete')) {
	if ($_GET['type'] == 'moh') {
		$sampling_rate_dir = $_GET['sampling_rate'];
		$category_folder = $_GET['category'];

		if ($category_folder != '') {
			$path_mod = $category_folder."/";

			if (count($_SESSION['domains']) > 1) {
				$path_mod = $_SESSION["domain_name"]."/".$path_mod;
			}
		}

		unlink($dir_music_on_hold."/".$path_mod.$sampling_rate_dir."/".base64_decode($_GET['filename']));
		header("Location: music_on_hold.php");
		exit;
	}

	if ($_GET['type'] == 'cat') {
		$category_folder = $_GET['category'];

		if (count($_SESSION['domains']) > 1) {
			$path_mod = $_SESSION["domain_name"]."/";
		}

		// remove sampling rate folders (if any)
		foreach ($sampling_rate_dirs as $sampling_rate_dir) {
			rmdir($dir_music_on_hold."/".$path_mod.(base64_decode($category_folder))."/".$sampling_rate_dir);
		}

		// remove category folder
		if (rmdir($dir_music_on_hold."/".$path_mod.(base64_decode($category_folder)))) {
			sleep(5); // allow time for the OS to catch up (at least Windows, anyway)
		}

		header("Location: music_on_hold.php");
		exit;
	}
}


//include the header
	require_once "includes/header.php";

//begin the content
	echo "<script language='JavaScript' type='text/javascript' src='".PROJECT_PATH."/includes/javascript/reset_file_input.js'></script>\n";
	echo "<script>\n";
	echo "function EvalSound(soundobj) {\n";
	echo "	var thissound= eval(\"document.\"+soundobj);\n";
	echo "	thissound.Play();\n";
	echo "}\n";
	echo "</script>";

	echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";
	echo "	<tr>\n";
	echo "		<td align='left'>\n";
	echo "			<p><span class=\"vexpl\">\n";
	echo "			<strong>Music on Hold</strong><br><br>\n";
	echo "			Music on hold can be in WAV or MP3 format. To play an MP3 file you must have\n";
	echo "			mod_shout enabled on the 'Modules' tab. You can adjust the volume of the MP3\n";
	echo "			audio from the 'Settings' tab. For best performance upload 16 bit, 8/16/32/48 kHz <i>mono</i> WAV files.\n";
	echo "			</span></p>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	echo "\n";
	echo "<br><br>\n";
	echo "\n";

//begin upload moh form ********************************************************************************************************************************************

	if (permission_exists('music_on_hold_add')) {
		echo "<b>Upload Music</b>\n";
		echo "<br><br>\n";
		echo "<form action='' method='POST' enctype='multipart/form-data' name='frmUpload' id='frmUpload' onSubmit=''>\n";
		echo "<input name='type' type='hidden' value='moh'>\n";
		echo "<table cellpadding='0' cellspacing='0' border='0'>\n";
		echo "	<tr>\n";
		echo "		<td style='padding-right: 5px;' nowrap>\n";
		echo "			File Path<br>\n";
		echo "			<input name='upload_file' type='file' class='button' size='50' id='upload_file'><input type='button' class='button' value='Clear' onclick=\"reset_file_input('upload_file');\">\n";
		echo "		</td>\n";
		echo "		<td style='padding-right: 5px;' nowrap>Sampling<br>\n";
		echo "			<select id='upload_sampling_rate' name='upload_sampling_rate' class='formfld' style='width: auto;'>\n";
		echo "				<option value='8'>8 kHz</option>\n";
		echo "				<option value='16'>16 kHz</option>\n";
		echo "				<option value='32'>32 kHz</option>\n";
		echo "				<option value='48'>48 kHz</option>\n";
		echo "			</select>\n";
		echo "		</td>\n";
		echo "		<td nowrap>Category<br>\n";
		echo "			<select id='upload_category' name='upload_category' class='formfld' style='width: auto;' onchange=\"if (this.options[this.selectedIndex].value == '_NEW_CAT_') { this.style.display='none'; document.getElementById('upload_category_new').style.display=''; document.getElementById('upload_category_return').style.display=''; document.getElementById('upload_category_new').focus(); }\">\n";
		echo "				<option value='' style='font-style: italic;'>Default</option>\n";

		if (count($_SESSION['domains']) > 1) {
			$dir_music_on_hold_category_parent_folder = $dir_music_on_hold."/".$_SESSION['domain_name'];
		}
		else {
			$dir_music_on_hold_category_parent_folder = $dir_music_on_hold;
		}

		if ($handle = opendir($dir_music_on_hold_category_parent_folder)) {
			while (false !== ($folder = readdir($handle))) {
				if (
					$folder != "." &&
					$folder != ".." &&
					$folder != "8000" &&
					$folder != "16000" &&
					$folder != "32000" &&
					$folder != "48000" &&
					is_dir($dir_music_on_hold_category_parent_folder."/".$folder)
					) {
					echo "<option value='".$folder."'>".(str_replace('_', ' ', $folder))."</option>\n";
					$category_folders[] = $folder; // array used to output category folder contents below
				}
			}
			closedir($handle);
		}

		echo "				<option value='_NEW_CAT_' style='font-style: italic;'>New...</option>\n";
		echo "			</select>\n";
		echo "			<input class='formfld' style='width: 150px; display: none;' type='text' name='upload_category_new' id='upload_category_new' maxlength='255' value=''>";
		echo "		</td>\n";
		echo "		<td>&nbsp;<br>\n";
		echo "			<input id='upload_category_return' type='button' class='button' style='display: none;' value='<' onclick=\"this.style.display='none'; document.getElementById('upload_category_new').style.display='none'; document.getElementById('upload_category_new').value=''; document.getElementById('upload_category').style.display=''; document.getElementById('upload_category').selectedIndex = 0;\" title='Double-Click to Select an Existing Category'>";
		echo "		</td>\n";
		echo "		<td style='padding-left: 5px;'>&nbsp;<br>\n";
		echo "			<input name='submit' type='submit' class='btn' id='upload' value='Upload'>\n";
		echo "		</td>\n";
		echo "	<tr>\n";
		echo "</table>\n";
		echo "</form>\n";
		echo "<br><br>\n";
	}

//begin default moh section ********************************************************************************************************************************************

	echo "<b><i>Default</i></b>\n";
	if (count($_SESSION['domains']) > 1) {
		echo "&nbsp;&nbsp;- Available to All Domains\n";
	}
	echo "<br><br>\n";
	echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-bottom: 3px;\">\n";
	echo "	<tr>\n";
	echo "		<th width=\"30%\" class=\"listhdrr\">Download</th>\n";
	echo "		<th width=\"30%\" class=\"listhdrr\">Play</th>\n";
	echo "		<th width=\"30%\" class=\"listhdr\">Uploaded</th>\n";
	echo "		<th width=\"10%\" class=\"listhdr\" nowrap>File Size</th>\n";
	echo "		<th width=\"10%\" class=\"listhdr\" nowrap>Sampling</th>\n";
	echo "		<td width='22px' align=\"center\"></td>\n";
	echo "	</tr>";

	$c = 0;
	$row_style["0"] = "row_style0";
	$row_style["1"] = "row_style1";

	foreach ($sampling_rate_dirs as $sampling_rate_dir) {
		if ($handle = opendir($dir_music_on_hold."/".$sampling_rate_dir)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && is_file($dir_music_on_hold."/".$sampling_rate_dir."/".$file)) {
					$tmp_filesize = filesize($dir_music_on_hold."/".$sampling_rate_dir."/".$file);
					$tmp_filesize = byte_convert($tmp_filesize);

					echo "<tr>\n";
					echo "	<td class='".$row_style[$c]."'><a href=\"music_on_hold.php?a=download&sampling_rate=".$sampling_rate_dir."&type=moh&t=bin&filename=".base64_encode($file)."\">".$file."</a></td>\n";
					echo "	<td class='".$row_style[$c]."'>\n";
					echo "		<a href=\"javascript:void(0);\" onclick=\"window.open('music_on_hold_play.php?a=download&sampling_rate=".$sampling_rate_dir."&type=moh&filename=".base64_encode($file)."', 'play',' width=420,height=40,menubar=no,status=no,toolbar=no')\">\n";
								$tmp_file_array = explode("\.",$file);
					echo "		".$tmp_file_array[0];
					echo "		</a>";
					echo "	</td>\n";
					echo "	<td class='".$row_style[$c]."'>".date ("F d Y H:i:s", filemtime($dir_music_on_hold."/".$sampling_rate_dir."/".$file))."</td>\n";
					echo "	<td class='".$row_style[$c]."'>".$tmp_filesize."</td>\n";
					echo "	<td class='".$row_style[$c]."'>".($sampling_rate_dir / 1000)." kHz</td>\n";
					echo "	<td align=\"center\" width='22' nowrap class=\"list\">\n";
					if (permission_exists('music_on_hold_delete')) {
						echo "	<a href=\"music_on_hold.php?type=moh&act=del&sampling_rate=".$sampling_rate_dir."&filename=".base64_encode($file)."\" onclick=\"return confirm('Do you really want to delete this file?')\">$v_link_label_delete</a>\n";
					}
					echo "	</td>\n";
					echo "</tr>\n";
					$c = ($c==0) ? 1 : 0;
				}
			}
			closedir($handle);
		}
	}

	echo "</table>\n";
	if ($v_path_show) {
		echo "<div style='font-size: 10px; text-align: right; margin-right: 25px;'><b>Location:</b> ".$dir_music_on_hold."</div>\n";
	}
	echo "<br><br><br><br>\n";

//begin moh categories ********************************************************************************************************************************************

	foreach ($category_folders as $category_number => $category_folder) {
		$c = 0;

		echo "<b>".(str_replace('_', ' ', $category_folder))."</b>\n";
		echo "<br><br>\n";
		echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin-bottom: 3px;\">\n";
		echo "	<tr>\n";
		echo "		<th width=\"30%\" class=\"listhdrr\">Download</th>\n";
		echo "		<th width=\"30%\" class=\"listhdrr\">Play</th>\n";
		echo "		<th width=\"30%\" class=\"listhdr\">Uploaded</th>\n";
		echo "		<th width=\"10%\" class=\"listhdr\" nowrap>File Size</th>\n";
		echo "		<th width=\"10%\" class=\"listhdr\" nowrap>Sampling</th>\n";
		echo "		<td width='22px' align=\"center\" style=\"padding: 2px;\"><span id='category_".$category_number."_delete_icon'></span></td>\n";
		echo "	</tr>";

		$moh_found = false;

		foreach ($sampling_rate_dirs as $sampling_rate_dir) {
			if ($handle = opendir($dir_music_on_hold_category_parent_folder."/".$category_folder."/".$sampling_rate_dir)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != ".." && is_file($dir_music_on_hold_category_parent_folder."/".$category_folder."/".$sampling_rate_dir."/".$file)) {

						$tmp_filesize = filesize($dir_music_on_hold_category_parent_folder."/".$category_folder."/".$sampling_rate_dir."/".$file);
						$tmp_filesize = byte_convert($tmp_filesize);

						echo "<tr>\n";
						echo "	<td class='".$row_style[$c]."'><a href=\"music_on_hold.php?a=download&category=".$category_folder."&sampling_rate=".$sampling_rate_dir."&type=moh&t=bin&filename=".base64_encode($file)."\">".$file."</a></td>\n";
						echo "	<td class='".$row_style[$c]."'>\n";
						echo "		<a href=\"javascript:void(0);\" onclick=\"window.open('music_on_hold_play.php?a=download&category=".$category_folder."&sampling_rate=".$sampling_rate_dir."&type=moh&filename=".base64_encode($file)."', 'play',' width=420,height=40,menubar=no,status=no,toolbar=no')\">\n";
									$tmp_file_array = explode("\.",$file);
						echo "		".$tmp_file_array[0];
						echo "		</a>";
						echo "	</td>\n";
						echo "	<td class='".$row_style[$c]."'>".date ("F d Y H:i:s", filemtime($dir_music_on_hold_category_parent_folder."/".$category_folder."/".$sampling_rate_dir."/".$file))."</td>\n";
						echo "	<td class='".$row_style[$c]."'>".$tmp_filesize."</td>\n";
						echo "	<td class='".$row_style[$c]."'>".($sampling_rate_dir / 1000)." kHz</td>\n";
						echo "	<td align=\"center\" width='22' nowrap class=\"list\">\n";
						if (permission_exists('music_on_hold_delete')) {
							echo "	<a href=\"music_on_hold.php?type=moh&act=del&category=".$category_folder."&sampling_rate=".$sampling_rate_dir."&filename=".base64_encode($file)."\" onclick=\"return confirm('Do you really want to delete this file?')\">$v_link_label_delete</a>\n";
						}
						echo "	</td>\n";
						echo "</tr>\n";
						$c = ($c==0) ? 1 : 0;

						$moh_found = true;
					}
				}
				closedir($handle);
			}
		}

		if (!$moh_found) {
			echo "<tr>\n";
			echo "	<td colspan='5' align='left' class='".$row_style[$c]."'>\n";
			echo "		No files found.";
			echo "		<script>document.getElementById('category_".$category_number."_delete_icon').innerHTML = \"<a href='music_on_hold.php?type=cat&act=del&category=".base64_encode($category_folder)."' title='Delete Category'>".$v_link_label_delete."</a>\";</script>\n";
			echo "	</td>\n";
			echo "</tr>\n";
		}


		echo "</table>\n";
		if ($v_path_show) {
			echo "<div style='font-size: 10px; text-align: right; margin-right: 25px;'><b>Location:</b> ".$dir_music_on_hold_category_parent_folder."/".$category_folder."</div>\n";
		}
		echo "<br><br>\n";

	}

//include the footer
	require_once "includes/footer.php";

?>