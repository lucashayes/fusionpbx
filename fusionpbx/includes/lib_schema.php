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
//include "root.php";
//require_once "includes/config.php";

function db_table_exists_alternate ($db, $db_type, $table_name) {
	$sql = "select count(*) from $table_name ";
	$result = $db->query($sql);
	if ($result > 0) {
		return true; //table exists
	}
	else {
		return false; //table doesn't exist
	}
}

function db_table_exists ($db, $db_type, $db_name, $table_name) {
	$sql = "";
	if ($db_type == "sqlite") {
		$sql .= "SELECT * FROM sqlite_master WHERE type='table' and name='$table_name' ";
	}
	if ($db_type == "pgsql") {
		$sql .= "select * from pg_tables where schemaname='public' and tablename = '$table_name' ";
	}
	if ($db_type == "mysql") {
		$sql .= "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = '$db_name' and TABLE_NAME = '$table_name' ";
	}
	$prepstatement = $db->prepare(check_sql($sql));
	$prepstatement->execute();
	$result = $prepstatement->fetchAll();
	if (count($result) > 0) {
		return true; //table exists
	}
	else {
		return false; //table doesn't exist
	}
}

function db_column_exists ($db, $db_type, $db_name, $tmp_table_name, $tmp_column_name) {
	global $display_type;

	//check if the column exists
		$sql = "";
		if ($db_type == "sqlite") {
			$sql .= "SELECT * FROM sqlite_master WHERE type='table' and name='$tmp_table_name' and sql like '%$tmp_column_name%' ";
		}
		if ($db_type == "pgsql") {
			$sql .= "SELECT attname FROM pg_attribute WHERE attrelid = (SELECT oid FROM pg_class WHERE relname = '$tmp_table_name') AND attname = '$tmp_column_name'; ";
		}
		if ($db_type == "mysql") {
			//$sql .= "SELECT * FROM information_schema.COLUMNS where TABLE_SCHEMA = '$db_name' and TABLE_NAME = '$tmp_table_name' and COLUMN_NAME = '$tmp_column_name' ";
			$sql .= "show columns from $tmp_table_name where field = '$tmp_column_name' ";
		}
		$prepstatement = $db->prepare(check_sql($sql));
		$prepstatement->execute();
		$result = $prepstatement->fetchAll();
		if (!$result) {
			//return false;
		}
		if (count($result) > 0) {
			//echo "table $tmp_table_name $tmp_column_name exists $result<br />\n";
			return true;
		}
		else {
			//echo "table $tmp_table_name $tmp_column_name does not exist<br />\n";
			return false;
		}
		unset ($prepstatement);
}

function db_create_table ($apps, $db_type, $table) {
	foreach ($apps as $x => &$app) {
		foreach ($app['db'] as $y => $row) {
			if ($row['table'] == $table) {
				$sql = "CREATE TABLE " . $row['table'] . " (\n";
				$field_count = 0;
				foreach ($row['fields'] as $field) {
					if ($field_count > 0 ) { $sql .= ",\n"; }
					if (is_array($field['name'])) {
						$sql .= $field['name']['text'] . " ";
					}
					else {
						$sql .= $field['name'] . " ";
					}
					if (is_array($field['type'])) {
						$sql .= $field['type'][$db_type];
					}
					else {
						$sql .= $field['type'];
					}
					$field_count++;
				}
				$sql .= ");\n\n";
				return $sql;
			}
		}
	}
}

function db_insert_into ($apps, $db_type, $table) {
	foreach ($apps as $x => &$app) {
		foreach ($app['db'] as $y => $row) {
			if ($row['table'] == $table) {
				$sql = "INSERT INTO " . $row['table'] . " (";
				$field_count = 0;
				foreach ($row['fields'] as $field) {
					if ($field_count > 0 ) { $sql .= ","; }
					if (is_array($field['name'])) {
						$sql .= $field['name']['text'];
					}
					else {
						$sql .= $field['name'];
					}
					$field_count++;
				}
				$sql .= ")\n";
				$sql .= "SELECT ";
				$field_count = 0;
				foreach ($row['fields'] as $field) {
					if ($field_count > 0 ) { $sql .= ","; }
					if (is_array($field['name'])) {
						if (db_column_exists ($db, $db_type, $db_name, $table_name, $field['name']['deprecated'])) {
							$sql .= $field['name']['deprecated'];
						}
						else {
							$sql .= $field['name']['text'];
						}
					}
					else {
						$sql .= $field['name'];
					}
					$field_count++;
				}
				$sql .= " FROM tmp_".$row['table'].";\n\n";	
				return $sql;
			}
		}
	}
}
	
function db_upgrade_schema ($db, $db_type, $db_name, $display_results) {
	global $display_type;

	//PHP PDO check if table or column exists
		//check if table exists
			// SELECT * FROM sqlite_master WHERE type='table' AND name='v_cdr'
		//check if column exists
			// SELECT * FROM sqlite_master WHERE type='table' AND name='v_cdr' AND sql LIKE '%caller_id_name TEXT,%'
		//aditional information
			// http://www.sqlite.org/faq.html#q9

		//postgresql
			//list all tables in the database
				// SELECT tablename FROM pg_tables WHERE schemaname='public';
			//check if table exists
				// SELECT * FROM pg_tables WHERE schemaname='public' AND tablename = 'v_groups'
			//check if column exists
				// SELECT attname FROM pg_attribute WHERE attrelid = (SELECT oid FROM pg_class WHERE relname = 'v_cdr') AND attname = 'caller_id_name'; 
		//mysql
			//list all tables in the database
				// SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = 'fusionpbx'
			//check if table exists
				// SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = 'fusionpbx' AND TABLE_NAME = 'v_groups'
			//check if column exists
				// SELECT * FROM information_schema.COLUMNS where TABLE_SCHEMA = 'fusionpbx' AND TABLE_NAME = 'v_cdr' AND COLUMN_NAME = 'context'
		//oracle
			//check if table exists
				// SELECT TABLE_NAME FROM ALL_TABLES

	//get the $apps array from the installed apps from the core and mod directories
		$config_list = glob($_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . "/*/*/v_config.php");
		$x=0;
		foreach ($config_list as &$config_path) {
			include($config_path);
			$x++;
		}

	//update the app db array add exists true or false
		$sql = '';
		foreach ($apps as $x => &$app) {
			foreach ($app['db'] as $y => &$row) {
				$table_name = $row['table'];
				if (strlen(table_name) > 0) {
					//check if the table exists
						if (db_table_exists($db, $db_type, $db_name, $table_name)) {
							$apps[$x]['db'][$y]['exists'] = 'true';
						}
						else {
							$apps[$x]['db'][$y]['exists'] = 'false';
						}
					//check if the column exists
						foreach ($row['fields'] as $z => $field) {
							if (is_array($field['name'])) {
								$field_name = $field['name']['text'];
							}
							else {
								$field_name = $field['name'];
							}
							if (strlen(field_name) > 0) {
								if (db_column_exists ($db, $db_type, $db_name, $table_name, $field_name)) {
									//found
									$apps[$x]['db'][$y]['fields'][$z]['exists'] = 'true';
								}
								else {
									//not found
									$apps[$x]['db'][$y]['fields'][$z]['exists'] = 'false';
								}
							}
							unset($field_name);
						}
					unset($table_name);
				}
			}
		}

	//prepare the variables
		$sql_update = '';
		$var_id = $_GET["id"];

	//add missing tables and fields
		foreach ($apps as $x => &$app) {
			foreach ($app['db'] as $y => &$row) {
				$table_name = $row['table'];
				//check if the table exists
					if ($row['exists'] == "true") {
						if (count($row['fields']) > 0) {
							foreach ($row['fields'] as $z => $field) {
								//get the data type
									if (is_array($field['type'])) {
										$field_type = $field['type'][$db_type];
									}
									else {
										$field_type = $field['type'];
									}
								//find missing fields and add them
									if (is_array($field['name'])) {
										if ($field['exists'] == "false" && $field['name']['deprecated'] == "false") {
											$sql_update .= "ALTER TABLE ".$table_name." ADD ".$field['name']['text']." ".$field_type.";\n";
										}
									}
									else {
										if ($field['exists'] == "false") {
											$sql_update .= "ALTER TABLE ".$table_name." ADD ".$field['name']." ".$field_type.";\n";
										}
									}
								//rename fields where the name has changed
									if (is_array($field['name'])) {
										if (db_column_exists ($db, $db_type, $db_name, $table_name, $field['name']['deprecated'])) {
											if ($db_type == "pgsql") {
												$sql_update .= "ALTER TABLE ".$table_name." RENAME COLUMN ".$field['name']['deprecated']." to ".$field['name']['text'].";\n";
											}
											if ($db_type == "mysql") {
												$sql_update .= "ALTER TABLE ".$table_name." CHANGE ".$field['name']['deprecated']." ".$field['name']['text']." ".$field_type.";\n";
											}
											if ($db_type == "sqlite") {
												//a change has been made to the field name
												$apps[$x]['db'][$y]['rebuild'] = 'true';
											}
										}
									}
								//change the data type if it has been changed
									//if the data type in the app db array is different than the type in the database then change the data type
									//if (db_column_data_type ($db, $db_type, $db_name, $table_name, $field['name']['deprecated']) != $field_type) {
										//if ($db_type == "pgsql") {
											//$sql_update .= "ALTER TABLE ".$table_name." ALTER COLUMN ".$field['name']." TYPE ".$field_type.";\n";
										//}
										//if ($db_type == "mysql") {
											//$sql_update .= "ALTER TABLE ".$table_name." modify ".$field['name']." ".$field_type.";\n";
										//}
										//if ($db_type == "sqlite") {
											//a change has been made to the field type
											//$apps[$x]['db'][$y]['rebuild'] = 'true';
										//}
									//}
							}
							unset($column_array);
						}
					}
					else {
						//create table
						$sql_update .= db_create_table($apps, $db_type, $row['table']);
					}
			}
		}
	//rebuild and populate the table
		foreach ($apps as $x => &$app) {
			foreach ($app['db'] as $y => &$row) {
				$table_name = $row['table'];
				if ($row['rebuild'] == "true") {
					if ($db_type == "sqlite") {
						//rename the table
							$sql_update .= "ALTER TABLE ".$table_name." RENAME TO tmp_".$table_name.";\n";
						//create the table
							$sql_update .= db_create_table($apps, $db_type, $table_name);
						//insert the data into the new table
							$sql_update .= db_insert_into($apps, $db_type, $table_name);
						//drop the old table
							$sql_update .= "DROP TABLE tmp_".$table_name.";\n";
					}
				}
			}
		}
	//display results as html
		if ($display_results && $display_type == "html") {
			//show the database type
				echo "<strong>Database Type: ".$db_type. "</strong><br /><br />";
			//start the table
				echo "<table width='100%' border='0' cellpadding='20' cellspacing='0'>\n";
			//show the changes
				if (strlen($sql_update) > 0) {
					echo "<tr>\n";
					echo "<td class='rowstyle1' colspan='3'>\n";
					echo "<br />\n";
					echo "<strong>SQL Changes:</strong><br />\n";
					echo "<pre>\n";
					echo $sql_update;
					echo "</pre>\n";
					echo "<br />\n";
					echo "</td>\n";
					echo "</tr>\n";
				}
			//list all tables
				echo "<tr>\n";
				echo "<th>Table</th>\n";
				echo "<th>Exists</th>\n";
				echo "<th>Details</th>\n";
				echo "<tr>\n";
			//build the html while looping through the app db array
				$sql = '';
				foreach ($apps as &$app) {
					foreach ($app['db'] as $row) {
						$table_name = $row['table'];
						echo "<tr>\n";

						//check if the table exists
							if ($row['exists'] == "true") {
								echo "<td valign='top' class='rowstyle1'><strong>table</strong><br />$table_name</td>\n";
								echo "<td valign='top' class='vncell' style=''>true</td>\n";

								if (count($row['fields']) > 0) {
									echo "<td class='rowstyle1'>\n";
									//show the list of columns
										echo "<table border='0' cellpadding='10' cellspacing='0'>\n";
										echo "<tr>\n";
										echo "<th>name</th>\n";
										echo "<th>type</th>\n";
										echo "<th>exists</th>\n";
										echo "</tr>\n";
										foreach ($row['fields'] as $field) {
											if (is_array($field['name'])) {
												$field_name = $field['name']['text'];
											}
											else {
												$field_name = $field['name'];
											}
											if (is_array($field['type'])) {
												$field_type = $field['type'][$db_type];
											}
											else {
												$field_type = $field['type'];
											}
											echo "<tr>\n";
											echo "<td class='rowstyle1' width='200'>".$field_name."</td>\n";
											echo "<td class='rowstyle1'>".$field_type."</td>\n";
											if ($field['exists'] == "true") {
												echo "<td class='rowstyle0' style=''>true</td>\n";
												echo "<td>&nbsp;</td>\n";
											}
											else {
												echo "<td class='rowstyle1' style='background-color:#444444;color:#CCCCCC;'>false</td>\n";
												echo "<td>&nbsp;</td>\n";
											}
											echo "</tr>\n";
										}
										unset($column_array);
										echo "	</table>\n";
										echo "</td>\n";
								}
							}
							else {
								echo "<td valign='top' class='rowstyle1'><strong>table</strong><br />$table_name</td>\n";
								echo "<td valign='top' class='rowstyle1' style='background-color:#444444;color:#CCCCCC;'><strong>exists</strong><br />false</td>\n";
								echo "<td valign='top' class='rowstyle1'>&nbsp;</td>\n";
							}
							echo "</tr>\n";
					}
				}
				unset ($prepstatement);
			//end the list of tables
				echo "</table>\n";
				echo "<br />\n";			
		}

		//loop line by line through all the lines of sql code
			$x = 0;
			if (strlen($sql_update) == 0 && $display_type == "text") {
				echo "	Schema:			no change\n";
			}
			else {
				if ($display_type == "text") {
					echo "	Schema:\n";
				}
				$db->beginTransaction();
				$update_array = explode(";", $sql_update);
				foreach($update_array as $sql) {
					if (strlen(trim($sql))) {
						try {
							$db->query(trim($sql));
							if ($display_type == "text") {
								echo " 	$sql\n";
							}
						}
						catch (PDOException $error) {
							if ($display_results) {
								echo "	error: " . $error->getMessage() . "	sql: $sql<br/>";
							}
						}
					}
				}
				$db->commit();
				echo "\n";
				unset ($file_contents, $sql_update, $sql);
			}

} //end function

?>