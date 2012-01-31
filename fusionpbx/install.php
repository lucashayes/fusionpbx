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
require_once "includes/lib_functions.php";

//set debug to true or false
	$v_debug = true;

//set the default domain_uuid
	$_SESSION["domain_uuid"] = uuid();

//add the menu uuid
	$menu_uuid = 'B4750C3F-2A86-B00D-B7D0-345C14ECA286';

//error reporting
	ini_set('display_errors', '1');
	//error_reporting (E_ALL); // Report everything
	error_reporting (E_ALL ^ E_NOTICE); // Report everything
	//error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ); //hide notices and warnings

//get the domain
	$domain_array = explode(":", $_SERVER["HTTP_HOST"]);
	$domain = $domain_array[0];

//make sure the sys_get_temp_dir exists 
	if ( !function_exists('sys_get_temp_dir')) {
		function sys_get_temp_dir() {
			if( $temp=getenv('TMP') ) { return $temp; }
			if( $temp=getenv('TEMP') ) { return $temp; }
			if( $temp=getenv('TMPDIR') ) { return $temp; }
			$temp=tempnam(__FILE__,'');
			if (file_exists($temp)) {
				unlink($temp);
				return dirname($temp);
			}
			return null;
		}
	}

//if the config file exists then disable the install page
	if (file_exists($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/config.php")) {
		$msg .= "Already Installed";
		header("Location: ".PROJECT_PATH."/index.php?msg=".urlencode($msg));
		exit;
	}

//set the max execution time to 1 hour
	ini_set('max_execution_time',3600);

//save an install log if debug is true
	if ($v_debug) {
		$fp = fopen("/tmp/install.log", "w");
	}

//set php variables with data from http post
	$db_type = $_POST["db_type"];
	$admin_username = $_POST["admin_username"];
	$admin_password = $_POST["admin_password"];
	$db_filename = $_POST["db_filename"];
	$db_host = $_POST["db_host"];
	$db_port = $_POST["db_port"];
	$db_name = $_POST["db_name"];
	$db_username = $_POST["db_username"];
	$db_password = $_POST["db_password"];
	$db_create_username = $_POST["db_create_username"];
	$db_create_password = $_POST["db_create_password"];
	$db_filepath = $_POST["db_filepath"];
	$install_step = $_POST["install_step"];
	$install_secure_dir = $_POST["install_secure_dir"];
	$install_php_dir = $_POST["install_php_dir"];
	$install_tmp_dir = $_POST["install_tmp_dir"];
	$install_backup_dir = $_POST["install_backup_dir"];
	$install_switch_base_dir = $_POST["install_switch_base_dir"];
	$install_v_template_name = $_POST["install_v_template_name"];

//clean up the values
	if (strlen($install_switch_base_dir) > 0) { 
		$install_switch_base_dir = realpath($install_switch_base_dir);
		$install_switch_base_dir = str_replace("\\", "/", $install_switch_base_dir);
	}

	$install_php_dir = realpath($_POST["install_php_dir"]);
	$install_php_dir = str_replace("\\", "/", $install_php_dir);

	$install_tmp_dir = realpath($_POST["install_tmp_dir"]);
	$install_tmp_dir = str_replace("\\", "/", $install_tmp_dir);

	$install_backup_dir = realpath($_POST["install_backup_dir"]);
	$install_backup_dir = str_replace("\\", "/", $install_backup_dir);

//set the default install_secure_dir
	if (strlen($install_secure_dir) == 0) { //secure dir
		$install_secure_dir = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/secure';
	}

//set the default db_filename
	if ($db_type == "sqlite") {
		if (strlen($db_filename) == 0) { $db_filename = "fusionpbx.db"; }
	}

//set the required directories

	//set the php bin directory
		if (file_exists('/usr/local/bin/php') || file_exists('/usr/local/bin/php5')) {
			$install_php_dir = '/usr/local/bin';
		}
		if (file_exists('/usr/bin/php') || file_exists('/usr/bin/php5')) {
			$install_php_dir = '/usr/bin';
		}

	//set the freeswitch bin directory
		if (file_exists('/usr/local/freeswitch/bin')) {
			$install_switch_base_dir = '/usr/local/freeswitch';
			$switch_bin_dir = '/usr/local/freeswitch/bin';
			$parent_dir = '/usr/local';
		}
		if (file_exists('/opt/freeswitch')) {
			$install_switch_base_dir = '/opt/freeswitch';
			$switch_bin_dir = '/opt/freeswitch/bin';
			$parent_dir = '/opt';
		}

	//set the default startup script directory
		if (file_exists('/usr/local/etc/rc.d')) {
			$startup_script_dir = '/usr/local/etc/rc.d';
		}
		if (file_exists('/etc/init.d')) {
			$startup_script_dir = '/etc/init.d';
		}

	//set the default directories
		$switch_bin_dir = $install_switch_base_dir.'/bin'; //freeswitch bin directory
		$switch_conf_dir = $install_switch_base_dir.'/conf';
		$switch_db_dir = $install_switch_base_dir.'/db';
		$switch_htdocs_dir = $install_switch_base_dir.'/htdocs';
		$switch_log_dir = $install_switch_base_dir.'/log';
		$switch_mod_dir = $install_switch_base_dir.'/mod';
		$switch_extensions_dir = $switch_conf_dir.'/directory/default';
		$switch_gateways_dir = $switch_conf_dir.'/sip_profiles';
		$v_dialplan_public_dir = $switch_conf_dir.'/dialplan/public';
		$switch_dialplan_dir = $switch_conf_dir.'/dialplan/default';
		$switch_scripts_dir = $install_switch_base_dir.'/scripts';
		$switch_grammar_dir = $install_switch_base_dir.'/grammar';
		$switch_storage_dir = $install_switch_base_dir.'/storage';
		$switch_voicemail_dir = $install_switch_base_dir.'/storage/voicemail';
		$switch_recordings_dir = $install_switch_base_dir.'/recordings';
		$switch_sounds_dir = $install_switch_base_dir.'/sounds';
		$install_tmp_dir = realpath(sys_get_temp_dir());
		$install_backup_dir = realpath(sys_get_temp_dir());
		$v_download_path = '';

	//set specific alternative directories as required
		switch (PHP_OS) {
		case "FreeBSD":
			//if the freebsd port is installed use the following paths by default.
				if (file_exists('/var/db/freeswitch')) {
					//freebsd port
						//set the default db_filepath
							if (strlen($db_filepath) == 0) { //secure dir
								$db_filepath = '/var/db/fusionpbx';
								if (!is_dir($db_filepath)) { mkdir($db_filepath,0777,true); }
							}
						//set the other default directories
							$switch_bin_dir = '/usr/local/bin'; //freeswitch bin directory
							$switch_conf_dir = '/usr/local/etc/freeswitch/conf';
							$switch_db_dir = '/var/db/freeswitch';
							$switch_htdocs_dir = '/usr/local/www/freeswitch/htdocs';
							$switch_log_dir = '/var/log/freeswitch';
							$switch_mod_dir = '/usr/local/lib/freeswitch/mod';
							$switch_extensions_dir = $switch_conf_dir.'/directory/default';
							$switch_gateways_dir = $switch_conf_dir.'/sip_profiles/external';
							$v_dialplan_public_dir = $switch_conf_dir.'/dialplan/public';
							$switch_dialplan_dir = $switch_conf_dir.'/dialplan/default';
							$switch_scripts_dir = '/usr/local/etc/freeswitch/scripts';
							$switch_grammar_dir = '/usr/local/etc/freeswitch/grammar';
							$switch_storage_dir = '/var/freeswitch';
							$switch_voicemail_dir = '/var/spool/freeswitch/voicemail';
							$switch_recordings_dir = '/var/freeswitch/recordings';
							$switch_sounds_dir = '/usr/local/share/freeswitch/sounds';
				}
				elseif (file_exists('/data/freeswitch')) {
					//freebsd embedded 
						//set the default db_filepath
							if (strlen($db_filepath) == 0) { //secure dir
								$db_filepath = '/data/db/fusionpbx';
								if (!is_dir($db_filepath)) { mkdir($db_filepath,0777,true); }
							}
						//set the other default directories
							$switch_bin_dir = '/usr/local/bin'; //freeswitch bin directory
							$switch_conf_dir = '/usr/local/etc/freeswitch/conf';
							$switch_db_dir = '/data/freeswitch/db';
							$switch_htdocs_dir = '/usr/local/www/freeswitch/htdocs';
							$switch_log_dir = '/data/freeswitch/log';
							$switch_mod_dir = '/usr/local/lib/freeswitch/mod';
							$switch_extensions_dir = $switch_conf_dir.'/directory/default';
							$switch_gateways_dir = $switch_conf_dir.'/sip_profiles/external';
							$v_dialplan_public_dir = $switch_conf_dir.'/dialplan/public';
							$switch_dialplan_dir = $switch_conf_dir.'/dialplan/default';
							$switch_scripts_dir = '/usr/local/etc/freeswitch/scripts';
							$switch_grammar_dir = '/usr/local/etc/freeswitch/grammar';
							$switch_storage_dir = '/data/freeswitch';
							$switch_voicemail_dir = '/data/freeswitch/voicemail';
							$switch_recordings_dir = '/data/freeswitch/recordings';
							$switch_sounds_dir = '/data/freeswitch/sounds';
				}
				else {
					//set the default db_filepath
						if (strlen($db_filepath) == 0) { //secure dir
							$db_filepath = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/secure';
						}
				}
			break;
		case "NetBSD":
			$startup_script_dir = '';
			$install_php_dir = '/usr/local/bin';

			//set the default db_filepath
				if (strlen($db_filepath) == 0) { //secure dir
					$db_filepath = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/secure';
				}
			break;
		case "OpenBSD":
			$startup_script_dir = '';

			//set the default db_filepath
				if (strlen($db_filepath) == 0) { //secure dir
					$db_filepath = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/secure';
				}
			break;
		default:
			//set the default db_filepath
				if (strlen($db_filepath) == 0) { //secure dir
					$db_filepath = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/secure';
				}
		}
		/*
		* CYGWIN_NT-5.1
		* Darwin
		* FreeBSD
		* HP-UX
		* IRIX64
		* Linux
		* NetBSD
		* OpenBSD
		* SunOS
		* Unix
		* WIN32
		* WINNT
		* Windows
		* CYGWIN_NT-5.1
		* IRIX64
		* SunOS
		* HP-UX
		* OpenBSD (not in Wikipedia)
		*/

	//set the dir defaults for windows
		if (substr(strtoupper(PHP_OS), 0, 3) == "WIN") {
			if (substr($_SERVER["DOCUMENT_ROOT"], -3) == "www") {
				//integrated installer
				$install_switch_base_dir = realpath($_SERVER["DOCUMENT_ROOT"]."/..");
				$parent_dir = realpath($_SERVER["DOCUMENT_ROOT"]."/..");
				$install_php_dir = realpath($_SERVER["PHPRC"]."/..");
				$startup_script_dir = '';
			} elseif (is_dir('C:/program files/FreeSWITCH')) {
				$install_switch_base_dir = 'C:/program files/FreeSWITCH';
				$parent_dir = 'C:/program files';
				$startup_script_dir = '';
			} elseif (is_dir('D:/program files/FreeSWITCH')) {
				$install_switch_base_dir = 'D:/program files/FreeSWITCH';
				$parent_dir = 'D:/program files';
				$startup_script_dir = '';
			} elseif (is_dir('E:/program files/FreeSWITCH')) {
				$install_switch_base_dir = 'E:/program files/FreeSWITCH';
				$parent_dir = 'E:/program files';
				$startup_script_dir = '';
			} elseif (is_dir('F:/program files/FreeSWITCH')) {
				$install_switch_base_dir = 'F:/program files/FreeSWITCH';
				$parent_dir = 'F:/program files';
				$startup_script_dir = '';
			} elseif (is_dir('C:/FreeSWITCH')) {
				$install_switch_base_dir = 'C:/FreeSWITCH';
				$parent_dir = 'C:';
				$startup_script_dir = '';
			} elseif (is_dir('D:/FreeSWITCH')) {
				$install_switch_base_dir = 'D:/FreeSWITCH';
				$parent_dir = 'D:';
				$startup_script_dir = '';
			} elseif (is_dir('E:/FreeSWITCH')) {
				$install_switch_base_dir = 'E:/FreeSWITCH';
				$parent_dir = 'E:';
				$startup_script_dir = '';
			} elseif (is_dir('F:/FreeSWITCH')) {
				$install_switch_base_dir = 'F:/FreeSWITCH';
				$parent_dir = 'F:';
				$startup_script_dir = '';
			} else {
				if (is_dir('C:/PHP')) { $install_php_dir = 'C:/PHP'; }
				if (is_dir('D:/PHP')) { $install_php_dir = 'D:/PHP'; }
				if (is_dir('E:/PHP')) { $install_php_dir = 'E:/PHP'; }
				if (is_dir('F:/PHP')) { $install_php_dir = 'F:/PHP'; }
				if (is_dir('C:/FreeSWITCH/wamp/bin/php/php5.3.0')) { $install_php_dir = 'C:/FreeSWITCH/wamp/bin/php/php5.3.0'; }
				if (is_dir('D:/FreeSWITCH/wamp/bin/php/php5.3.0')) { $install_php_dir = 'D:/FreeSWITCH/wamp/bin/php/php5.3.0'; }
				if (is_dir('E:/FreeSWITCH/wamp/bin/php/php5.3.0')) { $install_php_dir = 'E:/FreeSWITCH/wamp/bin/php/php5.3.0'; }
				if (is_dir('F:/FreeSWITCH/wamp/bin/php/php5.3.0')) { $install_php_dir = 'F:/FreeSWITCH/wamp/bin/php/php5.3.0'; }
				if (is_dir('C:/fusionpbx/Program/php')) { $install_php_dir = 'C:/fusionpbx/Program/php'; }
				if (is_dir('D:/fusionpbx/Program/php')) { $install_php_dir = 'D:/fusionpbx/Program/php'; }
				if (is_dir('E:/fusionpbx/Program/php')) { $install_php_dir = 'E:/fusionpbx/Program/php'; }
				if (is_dir('F:/fusionpbx/Program/php')) { $install_php_dir = 'F:/fusionpbx/Program/php'; }
			}
		}
$msg = '';
if ($_POST["install_step"] == "2" && count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {
	//check for all required data
		if (strlen($admin_username) == 0) { $msg .= "Please provide the Admin Username<br>\n"; }
		if (strlen($admin_password) == 0) {
			$msg .= "Please provide the Admin Password<br>\n";
		}
		else {
			if (strlen($admin_password) < 5) {
				$msg .= "Please provide an Admin Password that is 5 or more characters.<br>\n"; 
			}
		}
	//define the step to return to
		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			$_POST["install_step"] = "";
		}
}
if ($_POST["install_step"] == "3" && count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {
	//check for all required data
		if (strlen($db_type) == 0) { $msg .= "Please provide the Database Type<br>\n"; }
		if (PHP_OS == "FreeBSD" && file_exists('/usr/local/etc/freeswitch/conf')) {
			//install_switch_base_dir not required for the freebsd freeswitch port;
		}
		else {
			if (strlen($install_switch_base_dir) == 0) { $msg .= "Please provide the Switch Directory.<br>\n"; }
		}
		if (strlen($install_php_dir) == 0) { $msg .= "Please provide the PHP Directory.<br>\n"; }
		if (strlen($install_tmp_dir) == 0) { $msg .= "Please provide the Temp Directory.<br>\n"; }
		if (strlen($install_backup_dir) == 0) { $msg .= "Please provide the Backup Directory.<br>\n"; }
		if (strlen($install_v_template_name) == 0) { $msg .= "Please provide the Theme.<br>\n"; }

		if (!is_writable($install_switch_base_dir."/conf/vars.xml")) {
			if (substr(strtoupper(PHP_OS), 0, 3) == "WIN") {
				//some windows operating systems report read only but are writable
			}
			else {
				//$msg .= "<b>Write access to ".$install_switch_base_dir." and its sub-directories is required.</b><br />\n";
			}
		}
	//define the step to return to
		if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
			$_POST["install_step"] = "2";
		}
}
//show the error message if one exists
	if (strlen($msg) > 0 && strlen($_POST["persistformvar"]) == 0) {
		require_once "includes/persistformvar.php";
		echo "<br />\n";
		echo "<br />\n";
		echo "<div align='center'>\n";
		echo "<table><tr><td>\n";
		echo $msg."<br />";
		echo "</td></tr></table>\n";
		persistformvar($_POST);
		echo "</div>\n";
		exit;
	}

if ($_POST["install_step"] == "3" && count($_POST)>0 && strlen($_POST["persistformvar"]) == 0) {
	//create the sqlite database
			if ($db_type == "sqlite") {
				//sqlite database will be created when the config.php is loaded and only if the database file does not exist
					try {
						$db_tmp = new PDO('sqlite:'.$db_filepath.'/'.$db_filename); //sqlite 3
						//$db_tmp = new PDO('sqlite::memory:'); //sqlite 3
					}
					catch (PDOException $error) {
						print "error: " . $error->getMessage() . "<br/>";
						die();
					}

				//add additional functions to SQLite - bool PDO::sqliteCreateFunction ( string function_name, callback callback [, int num_args] )
					if (!function_exists('php_now')) {
						function php_now() {
							//return date('r');
							return date("Y-m-d H:i:s");
						}
					}
					$db_tmp->sqliteCreateFunction('now', 'php_now', 0);
					
				//add the database structure
					require_once "includes/classes/schema.php";
					$schema = new schema;
					$schema->db = $db_tmp;
					$schema->domain_uuid = $_SESSION["domain_uuid"];
					$schema->db_type = $db_type;
					$schema->add();

				//get the contents of the sql file
					$filename = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/sql/sqlite.sql';
					$file_contents = file_get_contents($filename);
					unset($filename);

				//replace \r\n with \n then explode on \n
					$file_contents = str_replace("\r\n", "\n", $file_contents);

				//loop line by line through all the lines of sql code
					$db_tmp->beginTransaction();
					$stringarray = explode("\n", $file_contents);
					$x = 0;
					foreach($stringarray as $sql) {
						try {
							$db_tmp->query($sql);
						}
						catch (PDOException $error) {
							echo "error: " . $error->getMessage() . " sql: $sql<br/>";
							//die();
						}
						$x++;
					}
					unset ($file_contents, $sql);
					$db_tmp->commit();
			}

	//create the pgsql database
			if ($db_type == "pgsql") {

				//echo "DB Name: {$db_name}<br>";
				//echo "DB Host: {$db_host}<br>";
				//echo "DB User: {$db_username}<br>";
				//echo "DB Pass: {$db_password}<br>";
				//echo "DB Port: {$db_port}<br>";
				//echo "DB Create User: {$db_create_username}<br>";
				//echo "DB Create Pass: {$db_create_password}<br>";

				//if $db_create_username provided, attempt to create new PG role and database
					if (strlen($db_create_username) > 0) {
						try {
							if (strlen($db_port) == 0) { $db_port = "5432"; }
							if (strlen($db_host) > 0) {
								$db_tmp = new PDO("pgsql:host={$db_host} port={$db_port} user={$db_create_username} password={$db_create_password} dbname=template1");
							} else {
								$db_tmp = new PDO("pgsql:host=localhost port={$db_port} user={$db_create_username} password={$db_create_password} dbname=template1");
							}
						} catch (PDOException $error) {
							print "error: " . $error->getMessage() . "<br/>";
							die();
						}

						//create the database, user, grant perms
						$db_tmp->exec("CREATE DATABASE {$db_name}");
						$db_tmp->exec("CREATE USER {$db_username} WITH PASSWORD '{$db_password}'");
						$db_tmp->exec("GRANT ALL ON {$db_name} TO {$db_username}");

						//close database connection_aborted
						$db_tmp = null;
					}

				//open database connection with $db_name
					try {
						if (strlen($db_port) == 0) { $db_port = "5432"; }
						if (strlen($db_host) > 0) {
							$db_tmp = new PDO("pgsql:host={$db_host} port={$db_port} dbname={$db_name} user={$db_username} password={$db_password}");
						} else {
							$db_tmp = new PDO("pgsql:host=localhost port={$db_port} user={$db_username} password={$db_password} dbname={$db_name}");
						}
					}
					catch (PDOException $error) {
						print "error: " . $error->getMessage() . "<br/>";
						die();
					}

				//add the database structure
					require_once "includes/classes/schema.php";
					$schema = new schema;
					$schema->db = $db_tmp;
					$schema->domain_uuid = $_SESSION["domain_uuid"];
					$schema->db_type = $db_type;
					$schema->add();

				//get the contents of the sql file
					$filename = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/sql/pgsql.sql';
					$file_contents = file_get_contents($filename);

				//replace \r\n with \n then explode on \n
					$file_contents = str_replace("\r\n", "\n", $file_contents);

				//loop line by line through all the lines of sql code
					$stringarray = explode("\n", $file_contents);
					$x = 0;
					foreach($stringarray as $sql) {
						if (strlen($sql) > 3) {
							try {
								$db_tmp->query($sql);
							}
							catch (PDOException $error) {
								echo "error: " . $error->getMessage() . " sql: $sql<br/>";
								die();
							}
						}
						$x++;
					}
					unset ($file_contents, $sql);
			}

	//create the mysql database
			if ($db_type == "mysql") {
				//database connection
					try {
						if (strlen($db_host) == 0 && strlen($db_port) == 0) {
							//if both host and port are empty use the unix socket
							if (strlen($db_create_username) == 0) {
								$db_tmp = new PDO("mysql:host=$db_host;unix_socket=/var/run/mysqld/mysqld.sock;", $db_username, $db_password);
							}
							else {
								$db_tmp = new PDO("mysql:host=$db_host;unix_socket=/var/run/mysqld/mysqld.sock;", $db_create_username, $db_create_password);
							}
						}
						else {
							if (strlen($db_port) == 0) {
								//leave out port if it is empty
								if (strlen($db_create_username) == 0) {
									$db_tmp = new PDO("mysql:host=$db_host;", $db_username, $db_password);
								}
								else {
									$db_tmp = new PDO("mysql:host=$db_host;", $db_create_username, $db_create_password);
								}
							}
							else {
								if (strlen($db_create_username) == 0) {
									$db_tmp = new PDO("mysql:host=$db_host;port=$db_port;", $db_username, $db_password);
								}
								else {
									$db_tmp = new PDO("mysql:host=$db_host;port=$db_port;", $db_create_username, $db_create_password);
								}
							}
						}
						$db_tmp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						$db_tmp->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
					}
					catch (PDOException $error) {
						if ($v_debug) {
							print "error: " . $error->getMessage() . "<br/>";
						}
					}

				//create the table, user and set the permissions only if the db_create_username was provided
					if (strlen($db_create_username) > 0) {
						//select the mysql database
							try {
								$db_tmp->query("USE mysql;");
							}
							catch (PDOException $error) {
								if ($v_debug) {
									print "error: " . $error->getMessage() . "<br/>";
								}
							}

						//create user and set the permissions
							try {
								$tmp_sql = "CREATE USER '".$db_username."'@'%' IDENTIFIED BY '".$db_password."'; ";
								$db_tmp->query($tmp_sql);
							}
							catch (PDOException $error) {
								if ($v_debug) {
									print "error: " . $error->getMessage() . "<br/>";
								}
							}

						//set account to unlimitted use
							try {
								if ($db_host == "localhost" || $db_host == "127.0.0.1") {
									$tmp_sql = "GRANT USAGE ON * . * TO '".$db_username."'@'localhost' ";
									$tmp_sql .= "IDENTIFIED BY '".$db_password."' ";
									$tmp_sql .= "WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0; ";
									$db_tmp->query($tmp_sql);

									$tmp_sql = "GRANT USAGE ON * . * TO '".$db_username."'@'127.0.0.1' ";
									$tmp_sql .= "IDENTIFIED BY '".$db_password."' ";
									$tmp_sql .= "WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0; ";
									$db_tmp->query($tmp_sql);
								}
								else {
									$tmp_sql = "GRANT USAGE ON * . * TO '".$db_username."'@'".$db_host."' ";
									$tmp_sql .= "IDENTIFIED BY '".$db_password."' ";
									$tmp_sql .= "WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0; ";
									$db_tmp->query($tmp_sql);
								}
							}
							catch (PDOException $error) {
								if ($v_debug) {
									print "error: " . $error->getMessage() . "<br/>";
								}
							}

						//create the database and set the create user with permissions
							try {
								$tmp_sql = "CREATE DATABASE IF NOT EXISTS ".$db_name."; ";
								$db_tmp->query($tmp_sql);
							}
							catch (PDOException $error) {
								if ($v_debug) {
									print "error: " . $error->getMessage() . "<br/>";
								}
							}

						//set user permissions
							try {
								$db_tmp->query("GRANT ALL PRIVILEGES ON ".$db_name.".* TO '".$db_username."'@'%'; ");
							}
							catch (PDOException $error) {
								if ($v_debug) {
									print "error: " . $error->getMessage() . "<br/>";
								}
							}

						//make the changes active
							try {
								$tmp_sql = "FLUSH PRIVILEGES; ";
								$db_tmp->query($tmp_sql);
							}
							catch (PDOException $error) {
								if ($v_debug) {
									print "error: " . $error->getMessage() . "<br/>";
								}
							}

					} //if (strlen($db_create_username) > 0)

				//select the database
					try {
						$db_tmp->query("USE ".$db_name.";");
					}
					catch (PDOException $error) {
						if ($v_debug) {
							print "error: " . $error->getMessage() . "<br/>";
						}
					}

				//add the database structure
					require_once "includes/classes/schema.php";
					$schema = new schema;
					$schema->db = $db_tmp;
					$schema->domain_uuid = $_SESSION["domain_uuid"];
					$schema->db_type = $db_type;
					$schema->add();

				//add the defaults data into the database
					//get the contents of the sql file
						$filename = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/includes/install/sql/mysql.sql';
						$file_contents = file_get_contents($filename);

					//replace \r\n with \n then explode on \n
						$file_contents = str_replace("\r\n", "\n", $file_contents);

					//loop line by line through all the lines of sql code
						$stringarray = explode("\n", $file_contents);
						$x = 0;
						foreach($stringarray as $sql) {
							if (strlen($sql) > 3) {
								try {
									if ($v_debug) {
										fwrite($fp, $sql."\n");
									}
									$db_tmp->query($sql);
								}
								catch (PDOException $error) {
									//echo "error on line $x: " . $error->getMessage() . " sql: $sql<br/>";
									//die();
								}
							}
							$x++;
						}
						unset ($file_contents, $sql);
			}

	//add the domain
		$sql = "insert into v_domains ";
		$sql .= "(";
		$sql .= "domain_uuid, ";
		$sql .= "domain_name, ";
		$sql .= "domain_description ";
		$sql .= ") ";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'".$_SESSION["domain_uuid"]."', ";
		$sql .= "'".$domain."', ";
		$sql .= "'' ";
		$sql .= ");";
		if ($v_debug) {
			fwrite($fp, $sql."\n");
		}
		$db_tmp->exec(check_sql($sql));
		unset($sql);

	//add the domain settings
		$x = 0;
		$tmp[$x]['name'] = 'v_menu_uuid';
		$tmp[$x]['value'] = $menu_uuid; //menu_uuid
		$x++;
		$tmp[$x]['name'] = 'v_time_zone'; //time_zone
		$tmp[$x]['value'] = '';
		$x++;
		$tmp[$x]['name'] = 'v_template_name'; //template_name
		$tmp[$x]['value'] = $install_v_template_name;
		$x++;
		$tmp[$x]['name'] = 'v_account_code'; //account_code
		$tmp[$x]['value'] = '';
		foreach($tmp as $row) {
			$sql = "insert into v_domain_settings ";
			$sql .= "(";
			$sql .= "domain_uuid, ";
			$sql .= "domain_setting_uuid, ";
			$sql .= "domain_setting_name, ";
			$sql .= "domain_setting_value ";
			$sql .= ") ";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'".$_SESSION["domain_uuid"]."', ";
			$sql .= "'".uuid()."', ";
			$sql .= "'".$row['name']."', ";
			$sql .= "'".$row['value']."' ";
			$sql .= ");";
			if ($v_debug) {
				fwrite($fp, $sql."\n");
			}
			$db_tmp->exec(check_sql($sql));
			unset($sql);
		}

	//add the server
		$server_uuid = uuid();
		$sql = "insert into v_servers ";
		$sql .= "(";
		$sql .= "domain_uuid, ";
		$sql .= "server_uuid, ";
		$sql .= "server_name ";
		$sql .= ") ";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'".$_SESSION["domain_uuid"]."', ";
		$sql .= "'".$server_uuid."', ";
		$sql .= "'".$domain."' ";
		$sql .= ");";
		if ($v_debug) {
			fwrite($fp, $sql."\n");
		}
		$db_tmp->exec(check_sql($sql));
		unset($sql);

	//replace back slashes with forward slashes
		$web_dir = str_replace("\\", "/", $_SERVER["DOCUMENT_ROOT"]);
		$web_root = str_replace("\\", "/", $_SERVER["DOCUMENT_ROOT"]);
		if (is_dir($_SERVER["DOCUMENT_ROOT"].'/fusionpbx')){ $relative_url = $_SERVER["DOCUMENT_ROOT"].'/fusionpbx'; } else { $relative_url = '/'; }
		$install_switch_base_dir = str_replace("\\", "/", $install_switch_base_dir);
		$parent_dir = str_replace("\\", "/", $parent_dir);
		$install_php_dir = str_replace("\\", "/", $install_php_dir);
		$startup_script_dir = str_replace("\\", "/", $startup_script_dir);
		$install_tmp_dir = str_replace("\\", "/", $install_tmp_dir);
		$install_backup_dir = str_replace("\\", "/", $install_backup_dir);

	//add the server settings
		$x = 0;
		$tmp[$x]['name'] = 'server_protocol';
		$tmp[$x]['value'] = '';
		$x++;
		$tmp[$x]['name'] = 'server_port';
		$tmp[$x]['value'] = '';
		$x++;
		$tmp[$x]['name'] = 'php_dir';
		$tmp[$x]['value'] = $install_php_dir;
		$x++;
		$tmp[$x]['name'] = 'tmp_dir';
		$tmp[$x]['value'] = $install_tmp_dir;
		$x++;
		$tmp[$x]['name'] = 'switch_bin_dir';
		$tmp[$x]['value'] = $switch_bin_dir;
		$x++;
		$tmp[$x]['name'] = 'startup_script_dir';
		$tmp[$x]['value'] = $startup_script_dir;
		$x++;
		$tmp[$x]['name'] = 'switch_base_dir';
		$tmp[$x]['value'] = $install_switch_base_dir;
		$x++;
		$tmp[$x]['name'] = 'parent_dir';
		$tmp[$x]['value'] = $parent_dir;
		$x++;
		$tmp[$x]['name'] = 'backup_dir';
		$tmp[$x]['value'] = $install_backup_dir;
		$x++;
		$tmp[$x]['name'] = 'web_root';
		$tmp[$x]['value'] = $web_root;
		$x++;
		$tmp[$x]['name'] = 'web_dir';
		$tmp[$x]['value'] = $web_dir;
		$x++;
		$tmp[$x]['name'] = 'relative_url';
		$tmp[$x]['value'] = $relative_url;
		$x++;
		$tmp[$x]['name'] = 'switch_conf_dir';
		$tmp[$x]['value'] = $switch_conf_dir;
		$x++;
		$tmp[$x]['name'] = 'switch_db_dir';
		$tmp[$x]['value'] = $switch_db_dir;
		$x++;
		$tmp[$x]['name'] = 'switch_htdocs_dir';
		$tmp[$x]['value'] = $switch_htdocs_dir;
		$x++;
		$tmp[$x]['name'] = 'switch_log_dir';
		$tmp[$x]['value'] = $switch_log_dir;
		$x++;
		$tmp[$x]['name'] = 'switch_extensions_dir';
		$tmp[$x]['value'] = $switch_extensions_dir;
		$x++;
		$tmp[$x]['name'] = 'switch_gateways_dir';
		$tmp[$x]['value'] = $switch_gateways_dir;
		$x++;
		$tmp[$x]['name'] = 'switch_dialplan_dir';
		$tmp[$x]['value'] = $switch_dialplan_dir;
		$x++;
		$tmp[$x]['name'] = 'switch_mod_dir';
		$tmp[$x]['value'] = $switch_mod_dir;
		$x++;
		$tmp[$x]['name'] = 'switch_scripts_dir';
		$tmp[$x]['value'] = $switch_scripts_dir;
		$x++;
		$tmp[$x]['name'] = 'switch_grammar_dir';
		$tmp[$x]['value'] = $switch_grammar_dir;
		$x++;
		$tmp[$x]['name'] = 'switch_storage_dir';
		$tmp[$x]['value'] = $switch_storage_dir;
		$x++;
		$tmp[$x]['name'] = 'switch_voicemail_dir';
		$tmp[$x]['value'] = $switch_voicemail_dir;
		$x++;
		$tmp[$x]['name'] = 'switch_recordings_dir';
		$tmp[$x]['value'] = $switch_recordings_dir;
		$x++;
		$tmp[$x]['name'] = 'switch_sounds_dir';
		$tmp[$x]['value'] = $switch_sounds_dir;
		$x++;
		$tmp[$x]['name'] = 'provisioning_tftp_dir';
		$tmp[$x]['value'] = '';
		$x++;
		$tmp[$x]['name'] = 'provisioning_ftp_dir';
		$tmp[$x]['value'] = '';
		$x++;
		$tmp[$x]['name'] = 'provisioning_https_dir';
		$tmp[$x]['value'] = '';
		$x++;
		$tmp[$x]['name'] = 'provisioning_http_dir';
		$tmp[$x]['value'] = '';
		foreach($tmp as $row) {
			$sql = "insert into v_server_settings ";
			$sql .= "(";
			$sql .= "domain_uuid, ";
			$sql .= "server_uuid, ";
			$sql .= "server_setting_uuid, ";
			$sql .= "server_setting_name, ";
			$sql .= "server_setting_value ";
			$sql .= ") ";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'".$_SESSION["domain_uuid"]."', ";
			$sql .= "'".$server_uuid."', ";
			$sql .= "'".uuid()."', ";
			$sql .= "'".$row['name']."', ";
			$sql .= "'".$row['value']."' ";
			$sql .= ");";
			if ($v_debug) {
				fwrite($fp, $sql."\n");
			}
			$db_tmp->exec(check_sql($sql));
			unset($sql);
		}

	//get the list of installed apps from the core and mod directories
		$config_list = glob($_SERVER["DOCUMENT_ROOT"] . PROJECT_PATH . "/*/*/v_config.php");
		$x=0;
		foreach ($config_list as $config_path) {
			include($config_path);
			$x++;
		}

	//add the groups
		$x = 0;
		$tmp[$x]['group_name'] = 'superadmin';
		$tmp[$x]['group_desc'] = 'Super Administrator Group';
		$x++;
		$tmp[$x]['group_name'] = 'admin';
		$tmp[$x]['group_desc'] = 'Administrator Group';
		$x++;
		$tmp[$x]['group_name'] = 'user';
		$tmp[$x]['group_desc'] = 'User Group';
		$x++;
		$tmp[$x]['group_name'] = 'public';
		$tmp[$x]['group_desc'] = 'Public Group';
		$x++;
		$tmp[$x]['group_name'] = 'agent';
		$tmp[$x]['group_desc'] = 'Call Center Agent Group';
		foreach($tmp as $row) {
			$sql = "insert into v_groups ";
			$sql .= "(";
			$sql .= "domain_uuid, ";
			$sql .= "group_id, ";
			$sql .= "group_desc ";
			$sql .= ") ";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'".$_SESSION["domain_uuid"]."', ";
			$sql .= "'".$row['group_name']."', ";
			$sql .= "'".$row['group_desc']."' ";
			$sql .= ");";
			if ($v_debug) {
				fwrite($fp, $sql."\n");
			}
			$db_tmp->exec(check_sql($sql));
			unset($sql);
		}

	//add the superadmin user account
		//prepare the values
			$user_uuid = uuid();
			$user_type = 'Individual';
			$user_category = 'user';
		//salt used with the password to create a one way hash
			$salt = generate_password('20', '4');
		//add the user account
			$sql = "insert into v_users ";
			$sql .= "(";
			$sql .= "domain_uuid, ";
			$sql .= "user_uuid, ";
			$sql .= "username, ";
			$sql .= "password, ";
			$sql .= "salt, ";
			$sql .= "user_type, ";
			$sql .= "user_category, ";
			$sql .= "user_add_date, ";
			$sql .= "user_add_user ";
			$sql .= ") ";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'".$_SESSION["domain_uuid"]."', ";
			$sql .= "'$user_uuid', ";
			$sql .= "'".$admin_username."', ";
			$sql .= "'".md5($salt.$admin_password)."', ";
			$sql .= "'$salt', ";
			$sql .= "'$user_type', ";
			$sql .= "'$user_category', ";
			$sql .= "now(), ";
			$sql .= "'".$admin_username."' ";
			$sql .= ");";
			if ($v_debug) {
				fwrite($fp, $sql."\n");
			}
			$db_tmp->exec(check_sql($sql));
			unset($sql);

	//add the user to the superadmin group
		$sql = "insert into v_group_members ";
		$sql .= "(";
		$sql .= "domain_uuid, ";
		$sql .= "username, ";
		$sql .= "group_id ";
		$sql .= ") ";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'".$_SESSION["domain_uuid"]."', ";
		$sql .= "'".$admin_username."', ";
		$sql .= "'superadmin' ";
		$sql .= ");";
		if ($v_debug) {
			fwrite($fp, $sql."\n");
		}
		$db_tmp->exec(check_sql($sql));
		unset($sql);

	//assign the default permissions to the groups
		//$db_tmp->beginTransaction();
		foreach($apps as $app) {
			if ($app['permissions']) {
				foreach ($app['permissions'] as $row) {
					if ($v_debug) {
						fwrite($fp, "v_group_permissions\n");
						fwrite($fp, json_encode($row)."\n\n");	
					}

					foreach ($row['groups'] as $group) {
						//add the record
						$sql = "insert into v_group_permissions ";
						$sql .= "(";
						$sql .= "domain_uuid, ";
						$sql .= "permission_id, ";
						$sql .= "group_id ";
						$sql .= ") ";
						$sql .= "values ";
						$sql .= "(";
						$sql .= "'".$_SESSION["domain_uuid"]."', ";
						$sql .= "'".$row['name']."', ";
						$sql .= "'".$group."' ";
						$sql .= ");";
						if ($v_debug) {
							fwrite($fp, $sql."\n");
						}
						$db_tmp->exec(check_sql($sql));
						unset($sql);
					}
				}
			}
		}
		//$db_tmp->commit();

	//unset the temporary database connection
		unset($db_tmp);

	//generate the config.php
		$tmp_config = "<?php\n";
		$tmp_config .= "/* \$Id\$ */\n";
		$tmp_config .= "/*\n";
		$tmp_config .= "	config.php\n";
		$tmp_config .= "	Copyright (C) 2008, 2009 Mark J Crane\n";
		$tmp_config .= "	All rights reserved.\n";
		$tmp_config .= "\n";
		$tmp_config .= "	Redistribution and use in source and binary forms, with or without\n";
		$tmp_config .= "	modification, are permitted provided that the following conditions are met:\n";
		$tmp_config .= "\n";
		$tmp_config .= "	1. Redistributions of source code must retain the above copyright notice,\n";
		$tmp_config .= "	   this list of conditions and the following disclaimer.\n";
		$tmp_config .= "\n";
		$tmp_config .= "	2. Redistributions in binary form must reproduce the above copyright\n";
		$tmp_config .= "	   notice, this list of conditions and the following disclaimer in the\n";
		$tmp_config .= "	   documentation and/or other materials provided with the distribution.\n";
		$tmp_config .= "\n";
		$tmp_config .= "	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,\n";
		$tmp_config .= "	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY\n";
		$tmp_config .= "	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE\n";
		$tmp_config .= "	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,\n";
		$tmp_config .= "	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF\n";
		$tmp_config .= "	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS\n";
		$tmp_config .= "	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN\n";
		$tmp_config .= "	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)\n";
		$tmp_config .= "	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE\n";
		$tmp_config .= "	POSSIBILITY OF SUCH DAMAGE.\n";
		$tmp_config .= "*/\n";
		$tmp_config .= "\n";
		$tmp_config .= "//-----------------------------------------------------\n";
		$tmp_config .= "// settings:\n";
		$tmp_config .= "//-----------------------------------------------------\n";
		$tmp_config .= "\n";
		$tmp_config .= "	//set the database type\n";
		$tmp_config .= "		\$db_type = '".$db_type."'; //sqlite, mysql, pgsql, others with a manually created PDO connection\n";
		$tmp_config .= "\n";
		if ($db_type == "sqlite") {
			$tmp_config .= "	//sqlite: the dbfilename and dbfilepath are automatically assigned however the values can be overidden by setting the values here.\n";
			$tmp_config .= "		\$dbfilename = '".$db_filename."'; //host name/ip address + '.db' is the default database filename\n";
			$tmp_config .= "		\$dbfilepath = '".$db_filepath."'; //the path is determined by a php variable\n";
		}
		$tmp_config .= "\n";
		$tmp_config .= "	//mysql: database connection information\n";
		if ($db_type == "mysql") {
			if ($db_host == "localhost") {
				//if localhost is used it defaults to a Unix Socket which doesn't seem to work.
				//replace localhost with 127.0.0.1 so that it will connect using TCP
				$db_host = "127.0.0.1";
			}
			$tmp_config .= "		\$db_host = '".$db_host."';\n";
			$tmp_config .= "		\$db_port = '".$db_port."';\n";
			$tmp_config .= "		\$db_name = '".$db_name."';\n";
			$tmp_config .= "		\$db_username = '".$db_username."';\n";
			$tmp_config .= "		\$db_password = '".$db_password."';\n";
		}
		else {
			$tmp_config .= "		//\$db_host = '';\n";
			$tmp_config .= "		//\$db_port = '';\n";
			$tmp_config .= "		//\$db_name = '';\n";
			$tmp_config .= "		//\$db_username = '';\n";
			$tmp_config .= "		//\$db_password = '';\n";
		}
		$tmp_config .= "\n";
		$tmp_config .= "	//pgsql: database connection information\n";
		if ($db_type == "pgsql") {
			$tmp_config .= "		\$db_host = '".$db_host."'; //set the host only if the database is not local\n";
			$tmp_config .= "		\$db_port = '".$db_port."';\n";
			$tmp_config .= "		\$db_name = '".$db_name."';\n";
			$tmp_config .= "		\$db_username = '".$db_username."';\n";
			$tmp_config .= "		\$db_password = '".$db_password."';\n";
		}
		else {
			$tmp_config .= "		//\$db_host = '".$db_host."'; //set the host only if the database is not local\n";
			$tmp_config .= "		//\$db_port = '".$db_port."';\n";
			$tmp_config .= "		//\$db_name = '".$db_name."';\n";
			$tmp_config .= "		//\$db_username = '".$db_username."';\n";
			$tmp_config .= "		//\$db_password = '".$db_password."';\n";
		}
		$tmp_config .= "\n";
		$tmp_config .= "	//show errors\n";
		$tmp_config .= "		ini_set('display_errors', '1');\n";
		$tmp_config .= "		//error_reporting (E_ALL); // Report everything\n";
		$tmp_config .= "		//error_reporting (E_ALL ^ E_NOTICE); // Report everything\n";
		$tmp_config .= "		error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ); //hide notices and warnings";
		$tmp_config .= "\n";
		$tmp_config .= "//-----------------------------------------------------\n";
		$tmp_config .= "// warning: do not edit below this line\n";
		$tmp_config .= "//-----------------------------------------------------\n";
		$tmp_config .= "\n";
		$tmp_config .= "	require_once \"includes/lib_php.php\";\n";
		$tmp_config .= "	require \"includes/lib_pdo.php\";\n";
		$tmp_config .= "	require_once \"includes/lib_functions.php\";\n";
		$tmp_config .= "	require_once \"includes/lib_switch.php\";\n";
		$tmp_config .= "\n";
		$tmp_config .= "?>";

		$fout = fopen($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/config.php","w");
		fwrite($fout, $tmp_config);
		unset($tmp_config);
		fclose($fout);

	//include the new config.php file
		require "includes/config.php";

	//set the defaults
		$menu_name = 'default';
		$menu_language = 'en';
		$menu_desc = '';
	//add the parent menu
		$sql = "insert into v_menus ";
		$sql .= "(";
		$sql .= "menu_uuid, ";
		$sql .= "menu_name, ";
		$sql .= "menu_language, ";
		$sql .= "menu_desc ";
		$sql .= ") ";
		$sql .= "values ";
		$sql .= "(";
		$sql .= "'".$menu_uuid."', ";
		$sql .= "'$menu_name', ";
		$sql .= "'$menu_language', ";
		$sql .= "'$menu_desc' ";
		$sql .= ");";
		if ($v_debug) {
			fwrite($fp, $sql."\n");
		}
		$db->exec(check_sql($sql));
		unset($sql);

	//add the menu items
		require_once "includes/classes/menu.php";
		$menu = new menu;
		$menu->db = $db;
		$menu->menu_uuid = $menu_uuid;
		$menu->restore();
		unset($menu);

	//rename the default config files that are not needed
		for ($i=1000; $i<1020; $i++) {
			$file = $switch_extensions_dir.'/'.$i; if (file_exists($file.'.xml')) { rename($file.'.xml', $file.'.noload'); }
		}
		$file = $switch_extensions_dir.'/brian'; if (file_exists($file.'.xml')) { rename($file.'.xml', $file.'.noload'); }
		$file = $switch_extensions_dir.'/example.com'; if (file_exists($file.'.xml')) { rename($file.'.xml', $file.'.noload'); }
		$file = $switch_dialplan_dir.'/99999_enum'; if (file_exists($file.'.xml')) { rename($file.'.xml', $file.'.noload'); }
		$file = $switch_dialplan_dir.'/01_example.com'; if (file_exists($file.'.xml')) { rename($file.'.xml', $file.'.noload'); }
		$file = $v_dialplan_public_dir.'/00_inbound_did'; if (file_exists($file.'.xml')) { rename($file.'.xml', $file.'.noload'); }
		unset($file);

	//create the necessary directories
		if (!is_dir($install_tmp_dir)) { mkdir($install_tmp_dir,0777,true); }
		if (!is_dir($install_backup_dir)) { mkdir($install_backup_dir,0777,true); }
		if (!is_dir($switch_sounds_dir.'/en/us/callie/custom/8000')) { mkdir($switch_sounds_dir.'/en/us/callie/custom/8000',0777,true); }
		if (!is_dir($switch_sounds_dir.'/en/us/callie/custom/16000')) { mkdir($switch_sounds_dir.'/en/us/callie/custom/16000',0777,true); }
		if (!is_dir($switch_sounds_dir.'/en/us/callie/custom/32000')) { mkdir($switch_sounds_dir.'/en/us/callie/custom/32000',0777,true); }
		if (!is_dir($switch_sounds_dir.'/en/us/callie/custom/48000')) { mkdir($switch_sounds_dir.'/en/us/callie/custom/48000',0777,true); }
		if (!is_dir($switch_storage_dir.'/fax/')) { mkdir($switch_storage_dir.'/fax',0777,true); }
		if (!is_dir($switch_log_dir.'')) { mkdir($switch_log_dir.'',0777,true); }
		if (!is_dir($switch_sounds_dir.'')) { mkdir($switch_sounds_dir.'',0777,true); }
		if (!is_dir($switch_recordings_dir.'')) { mkdir($switch_recordings_dir.'',0777,true); }

	//copy the files and directories from includes/install
		require_once "includes/classes/install.php";
		$install = new install;
		$install->domain_uuid = $_SESSION["domain_uuid"];
		$install->v_domain = $domain;
		$install->switch_conf_dir = $switch_conf_dir;
		$install->switch_scripts_dir = $switch_scripts_dir;
		$install->switch_sounds_dir = $switch_sounds_dir;
		$install->switch_recordings_dir = $switch_recordings_dir;
		$install->copy_conf();
		$install->copy();
		//print_r($install->result);

	//create the dialplan/default.xml for single tenant or dialplan/domain.xml
		require_once "includes/classes/dialplan.php";
		$dialplan = new dialplan;
		$dialplan->domain_uuid = $_SESSION["domain_uuid"];
		$dialplan->v_domain = $domain;
		$dialplan->switch_conf_dir = $switch_conf_dir;
		$dialplan->restore_advanced_xml();
		//print_r($dialplan->result);

	//write the xml_cdr.conf.xml file
		xml_cdr_conf_xml();

	//write the switch.conf.xml file
		switch_conf_xml();

	//login the user account
		$_SESSION["username"] = $admin_username;

	//get the groups assigned to the user and then set the groups in $_SESSION["groups"]
		$sql = "SELECT * FROM v_group_members ";
		$sql .= "where domain_uuid=:domain_uuid ";
		$sql .= "and username=:username ";
		$prep_statement = $db->prepare(check_sql($sql));
		$prep_statement->bindParam(':domain_uuid', $_SESSION["domain_uuid"]);
		$prep_statement->bindParam(':username', $_SESSION["username"]);
		$prep_statement->execute();
		$result = $prep_statement->fetchAll(PDO::FETCH_NAMED);
		$_SESSION["groups"] = $result;
		unset($sql, $row_count, $prep_statement);

	//get the permissions assigned to the groups that the user is a member of set the permissions in $_SESSION['permissions']
		$x = 0;
		$sql = "select distinct(permission_id) from v_group_permissions ";
		foreach($_SESSION["groups"] as $field) {
			if (strlen($field['group_id']) > 0) {
				if ($x == 0) {
					$sql .= "where (domain_uuid = '".$_SESSION["domain_uuid"]."' and group_id = '".$field['group_id']."') ";
				}
				else {
					$sql .= "or (domain_uuid = '".$_SESSION["domain_uuid"]."' and group_id = '".$field['group_id']."') ";
				}
				$x++;
			}
		}
		$prep_statementsub = $db->prepare($sql);
		$prep_statementsub->execute();
		$_SESSION['permissions'] = $prep_statementsub->fetchAll(PDO::FETCH_NAMED);
		unset($sql, $prep_statementsub);

	//make sure the database schema and installation have performed all necessary tasks
		$display_results = false;
		$display_type = 'none';
		require_once "core/upgrade/upgrade_schema.php";

	//synchronize the config with the saved settings
		sync_package_freeswitch();

	//do not show the apply settings reminder on the login page
		$_SESSION["reload_xml"] = false;

	//clear the menu
		$_SESSION["menu"] = "";

	//redirect to the login page
		$msg = "install complete";
		header("Location: ".PROJECT_PATH."/logout.php?msg=".urlencode($msg));
}

//set a default template
	if (strlen($_SESSION["template_name"]) == 0) { $_SESSION["template_name"] = 'enhanced'; }

//get the contents of the template and save it to the template variable
	$template = file_get_contents($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/themes/'.$_SESSION["template_name"].'/template.php');

//buffer the content
	ob_end_clean(); //clean the buffer
	ob_start();

//show the html form
	if (!is_writable($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/header.php")) {
		$installmsg .= "<li>Write access to ".$_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/ is required during the install.</li>\n";
	}
	if (strlen($install_switch_base_dir) > 0) {
		if (!is_writable($install_switch_base_dir)) {
			$installmsg .= "<li>Write access to the 'FreeSWITCH Directory' and most of its sub directories is required.</li>\n";
		}
	}
	if (!extension_loaded('PDO')) {
		$installmsg .= "<li>PHP PDO was not detected. Please install it before proceeding.</li>";
	}

	if ($installmsg) {
		echo "<br />\n";
		echo "<div align='center'>\n";
		echo "<table width='75%'>\n";
		echo "<tr>\n";
		echo "<th align='left'>Message</th>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td class='row_style1'><strong><ul>$installmsg</ul></strong></td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</div>\n";
	}

	echo "<div align='center'>\n";
	$msg = '';
	//make sure the includes directory is writable so the config.php file can be written.
		if (!is_writable($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/includes/lib_pdo.php")) {
			$msg .= "<b>Write access to ".$_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."</b><br />";
			$msg .= "and its sub-directories are required during the install.<br /><br />\n";
		}

	//display the message
		if (strlen($msg) > 0) {
			//echo "not writable";
			echo $msg;
			echo "<br />\n";
			echo "<br />\n";
			unset($msg);
			//exit;
		}

// step 1
	if ($_POST["install_step"] == "") {
		echo "<div id='page' align='center'>\n";
		echo "<form method='post' name='frm' action=''>\n";
		echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

		//echo "<tr>\n";
		//echo "<td colspan='2' align='left' width='30%' nowrap><b>Installation</b></td>\n";
		//echo "</tr>\n";
		echo "<tr>\n";
		echo "<td colspan='2' width='100%' align='left'>\n";
		echo "	<strong>The installation is a simple two step process.</strong> \n";
		echo "	<ul>\n";
		echo "	<li>Step 1 is used for selecting the database engine to use. After making that section then ensure the paths are correct and then press next. </li> ";
		echo "	<li>Step 2 requests the database specific settings. When finished press save. The installation will then complete the tasks required to do the install. </li></td>\n";
		echo "	</ul>\n";
		//echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_dialplan_edit.php?id=".$dialplan_uuid."'\" value='Back'></td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td align='left' width='30%' nowrap><b>Step 1</b></td>\n";
		echo "<td width='70%' align='right'>&nbsp;</td>\n";
		//echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"window.location='v_dialplan_edit.php?id=".$dialplan_uuid."'\" value='Back'></td>\n";
		echo "</tr>\n";

		$db_type = $_POST["db_type"];
		$install_step = $_POST["install_step"];

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "	Database Type:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<select name='db_type' id='db_type' class='formfld' id='form_tag' onchange='db_type_onchange();'>\n";
		if (extension_loaded('pdo_pgsql')) {	echo "	<option value='pgsql'>postgresql</option>\n"; }
		if (extension_loaded('pdo_mysql')) {	echo "	<option value='mysql'>mysql</option>\n"; }
		if (extension_loaded('pdo_sqlite')) {	echo "	<option  value='sqlite' selected>sqlite</option>\n"; } //set sqlite as the default
		echo "	</select><br />\n";
		echo "		Select the database type.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "	Username:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='text' name='admin_username' maxlength='255' value=\"$admin_username\"><br />\n";
		echo "	Enter the username to use when logging in with the browser.<br />\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "	Password:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='text' name='admin_password' maxlength='255' value=\"$admin_password\"><br />\n";
		echo "	Enter the password to use when logging in with the browser.<br />\n";
		echo "</td>\n";
		echo "</tr>\n";

		if (PHP_OS == "FreeBSD" && file_exists('/usr/local/etc/freeswitch/conf')) {
			//install_switch_base_dir not required for the freebsd freeswitch port;
		}
		else {
			echo "<tr>\n";
			echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
			echo "	FreeSWITCH Directory:\n";
			echo "</td>\n";
			echo "<td class='vtable' align='left'>\n";
			echo "	<input class='formfld' type='text' name='install_switch_base_dir' maxlength='255' value=\"$install_switch_base_dir\">\n";
			echo "<br />\n";
			echo "Enter the FreeSWITCH directory path.\n";
			echo "</td>\n";
			echo "</tr>\n";
		}

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "	PHP Directory:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='text' name='install_php_dir' maxlength='255' value=\"$install_php_dir\"><br />\n";
		echo "	Enter the path to PHP's bin or executable directory.<br />\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "	<tr>\n";
		echo "	<td width='20%' class=\"vncellreq\" style='text-align: left;'>\n";
		echo "		Theme: \n";
		echo "	</td>\n";
		echo "	<td class=\"vtable\" align='left'>\n";
		echo "		<select id='install_v_template_name' name='install_v_template_name' class='formfld' style=''>\n";
		echo "		<option value=''></option>\n";
		//set the default theme
			$install_v_template_name = "enhanced";
		//add all the themes to the list
			$theme_dir = $_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/themes';
			if ($handle = opendir($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/themes')) {
				while (false !== ($dir_name = readdir($handle))) {
					if ($dir_name != "." && $dir_name != ".." && $dir_name != ".svn" && is_dir($theme_dir.'/'.$dir_name)) {
						$dir_label = str_replace('_', ' ', $dir_name);
						$dir_label = str_replace('-', ' ', $dir_label);
						if ($dir_name == $install_v_template_name) {
							echo "		<option value='$dir_name' selected='selected'>$dir_label</option>\n";
						}
						else {
							echo "		<option value='$dir_name'>$dir_label</option>\n";
						}
					}
				}
				closedir($handle);
			}
		echo "		</select>\n";
		echo "		<br />\n";
		echo "		Select a theme to set as the default.<br />\n";
		echo "	</td>\n";
		echo "	</tr>\n";

		echo "	<tr>\n";
		echo "		<td colspan='2' align='right'>\n";
		echo "			<input type='hidden' name='install_tmp_dir' value='$install_tmp_dir'>\n";
		echo "			<input type='hidden' name='install_backup_dir' value='$install_backup_dir'>\n";
		echo "			<input type='hidden' name='install_step' value='2'>\n";
		echo "			<input type='submit' name='submit' class='btn' value='Next'>\n";
		echo "		</td>\n";
		echo "	</tr>";

		echo "</table>";
		echo "</form>";
		echo "</div>";
	}

// step 2, sqlite
	if ($_POST["install_step"] == "2" && $_POST["db_type"] == "sqlite") {
		echo "<div id='page' align='center'>\n";
		echo "<form method='post' name='frm' action=''>\n";
		echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

		echo "<tr>\n";
		echo "<td align='left' width='30%' nowrap><b>Installation: Step 2 - SQLite</b></td>\n";
		echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"history.go(-1);\" value='Back'></td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' 'valign='top' align='left' nowrap>\n";
		echo "	Database Filename:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='text' name='db_filename' maxlength='255' value=\"$db_filename\"><br />\n";
		echo "	Default: fusiopbx.db. If the field is left empty then the file name is determined by the host or IP address.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' 'valign='top' align='left' nowrap>\n";
		echo "	Database Directory:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "	<input class='formfld' type='text' name='db_filepath' maxlength='255' value=\"$db_filepath\"><br />\n";
		echo "	Set the path to the database directory.\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "	<tr>\n";
		echo "		<td colspan='2' align='right'>\n";
		echo "			<input type='hidden' name='db_type' value='$db_type'>\n";
		echo "			<input type='hidden' name='admin_username' value='$admin_username'>\n";
		echo "			<input type='hidden' name='admin_password' value='$admin_password'>\n";
		echo "			<input type='hidden' name='install_secure_dir' value='$install_secure_dir'>\n";
		echo "			<input type='hidden' name='install_switch_base_dir' value='$install_switch_base_dir'>\n";
		echo "			<input type='hidden' name='install_php_dir' value='$install_php_dir'>\n";
		echo "			<input type='hidden' name='install_tmp_dir' value='$install_tmp_dir'>\n";
		echo "			<input type='hidden' name='install_backup_dir' value='$install_backup_dir'>\n";
		echo "			<input type='hidden' name='install_step' value='3'>\n";
		echo "			<input type='hidden' name='install_v_template_name' value='$install_v_template_name'>\n";
		echo "			<input type='submit' name='submit' class='btn' value='Next'>\n";
		echo "		</td>\n";
		echo "	</tr>";

		echo "</table>";
		echo "</form>";
		echo "</div>";
	}

// step 2, mysql
	if ($_POST["install_step"] == "2" && $_POST["db_type"] == "mysql") {

		//set defaults
			if (strlen($db_host) == 0) { $db_host = 'localhost'; }
			if (strlen($db_port) == 0) { $db_port = '3306'; }
			//if (strlen($db_name) == 0) { $db_name = 'fusionpbx'; }

		//echo "However if preferred the database can be created manually with the <a href='". echo PROJECT_PATH; ."/includes/install/sql/mysql.sql' target='_blank'>mysql.sql</a> script. ";
		echo "<div id='page' align='center'>\n";
		echo "<form method='post' name='frm' action=''>\n";
		echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

		echo "<tr>\n";
		echo "<td align='left' width='30%' nowrap><b>Installation: Step 2 - MySQL</b></td>\n";
		echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"history.go(-1);\" value='Back'></td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "		Database Host:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_host' maxlength='255' value=\"$db_host\"><br />\n";
		echo "		Enter the host address for the database server.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "		Database Port:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_port' maxlength='255' value=\"$db_port\"><br />\n";
		echo "		Enter the port number. It is optional if the database is using the default port.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "		Database Name:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_name' maxlength='255' value=\"$db_name\"><br />\n";
		echo "		Enter the name of the database.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "		Database Username:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_username' maxlength='255' value=\"$db_username\"><br />\n";
		echo "		Enter the database username. \n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "		Database Password:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_password' maxlength='255' value=\"$db_password\"><br />\n";
		echo "		Enter the database password.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "		Create Database Username:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_create_username' maxlength='255' value=\"$db_create_username\"><br />\n";
		echo "		Optional, this username is used to create the database, a database user and set the permissions. \n";
		echo "		By default this username is 'root' however it can be any account with permission to add a database, user, and grant permissions. \n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "		Create Database Password:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_create_password' maxlength='255' value=\"$db_create_password\"><br />\n";
		echo "		Enter the create database password.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "	<tr>\n";
		echo "		<td colspan='2' align='right'>\n";
		echo "			<input type='hidden' name='db_type' value='$db_type'>\n";
		echo "			<input type='hidden' name='admin_username' value='$admin_username'>\n";
		echo "			<input type='hidden' name='admin_password' value='$admin_password'>\n";
		echo "			<input type='hidden' name='install_secure_dir' value='$install_secure_dir'>\n";
		echo "			<input type='hidden' name='install_switch_base_dir' value='$install_switch_base_dir'>\n";
		echo "			<input type='hidden' name='install_php_dir' value='$install_php_dir'>\n";
		echo "			<input type='hidden' name='install_tmp_dir' value='$install_tmp_dir'>\n";
		echo "			<input type='hidden' name='install_backup_dir' value='$install_backup_dir'>\n";
		echo "			<input type='hidden' name='install_step' value='3'>\n";
		echo "			<input type='hidden' name='install_v_template_name' value='$install_v_template_name'>\n";
		echo "			<input type='submit' name='submit' class='btn' value='Next'>\n";
		echo "		</td>\n";
		echo "	</tr>";

		echo "</table>";
		echo "</form>";
		echo "</div>";
	}

// step 2, pgsql
	if ($_POST["install_step"] == "2" && $_POST["db_type"] == "pgsql") {
		if (strlen($db_host) == 0) { $db_host = 'localhost'; }
		if (strlen($db_port) == 0) { $db_port = '5432'; }
		//if (strlen($db_name) == 0) { $db_name = 'fusionpbx'; }

		echo "<div id='page' align='center'>\n";
		echo "<form method='post' name='frm' action=''>\n";
		echo "<table width='100%'  border='0' cellpadding='6' cellspacing='0'>\n";

		echo "<tr>\n";
		echo "<td align='left' width='30%' nowrap><b>Installation: Step 2 - Postgres</b></td>\n";
		echo "<td width='70%' align='right'><input type='button' class='btn' name='' alt='back' onclick=\"history.go(-1);\" value='Back'></td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "		Database Host:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_host' maxlength='255' value=\"$db_host\"><br />\n";
		echo "		Enter the host address for the database server.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "		Database Port:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_port' maxlength='255' value=\"$db_port\"><br />\n";
		echo "		Enter the port number. It is optional if the database is using the default port.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "		Database Name:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_name' maxlength='255' value=\"$db_name\"><br />\n";
		echo "		Enter the name of the database.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "		Database Username:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_username' maxlength='255' value=\"$db_username\"><br />\n";
		echo "		Enter the database username.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncellreq' valign='top' align='left' nowrap>\n";
		echo "		Database Password:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_password' maxlength='255' value=\"$db_password\"><br />\n";
		echo "		Enter the database password.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "		Create Database Username:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_create_username' maxlength='255' value=\"$db_create_username\"><br />\n";
		echo "		Optional, this username is used to create the database, a database user and set the permissions. \n";
		echo "		By default this username is 'pgsql' however it can be any account with permission to add a database, user, and grant permissions. \n";
		echo "		Leave blank if the user and empty database already exist and you do not want them created. \n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<td class='vncell' valign='top' align='left' nowrap>\n";
		echo "		Create Database Password:\n";
		echo "</td>\n";
		echo "<td class='vtable' align='left'>\n";
		echo "		<input class='formfld' type='text' name='db_create_password' maxlength='255' value=\"$db_create_password\"><br />\n";
		echo "		Enter the create database password.\n";
		echo "\n";
		echo "</td>\n";
		echo "</tr>\n";

		echo "	<tr>\n";
		echo "		<td colspan='2' align='right'>\n";
		echo "			<input type='hidden' name='db_type' value='$db_type'>\n";
		echo "			<input type='hidden' name='admin_username' value='$admin_username'>\n";
		echo "			<input type='hidden' name='admin_password' value='$admin_password'>\n";
		echo "			<input type='hidden' name='install_secure_dir' value='$install_secure_dir'>\n";
		echo "			<input type='hidden' name='install_switch_base_dir' value='$install_switch_base_dir'>\n";
		echo "			<input type='hidden' name='install_php_dir' value='$install_php_dir'>\n";
		echo "			<input type='hidden' name='install_tmp_dir' value='$install_tmp_dir'>\n";
		echo "			<input type='hidden' name='install_backup_dir' value='$install_backup_dir'>\n";
		echo "			<input type='hidden' name='install_step' value='3'>\n";
		echo "			<input type='hidden' name='install_v_template_name' value='$install_v_template_name'>\n";
		echo "			<input type='submit' name='submit' class='btn' value='Install'>\n";
		echo "		</td>\n";
		echo "	</tr>";

		echo "</table>";
		echo "</form>";
		echo "</div>";
	}

	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";
	echo "<br />\n";

// add the content to the template and then send output
	$body = $content_from_db.ob_get_contents(); //get the output from the buffer
	ob_end_clean(); //clean the buffer

	ob_start();
	eval('?>' . $template . '<?php ');
	$template = ob_get_contents(); //get the output from the buffer
	ob_end_clean(); //clean the buffer
	$customhead = $customhead.$templatemenucss;

	//$output = str_replace ("\r\n", "<br>", $output);
	$output = str_replace ("<!--{title}-->", $customtitle, $template); //<!--{title}--> defined in each individual page
	$output = str_replace ("<!--{head}-->", $customhead, $output); //<!--{head}--> defined in each individual page
	$output = str_replace ("<!--{menu}-->", $_SESSION["menu"], $output); //defined in /includes/menu.php
	$output = str_replace ("<!--{project_path}-->", PROJECT_PATH, $output); //defined in /includes/menu.php

	$pos = strrpos($output, "<!--{body}-->");
	if ($pos === false) {
		$output = $body; //if tag not found just show the body
	}
	else {
		//replace the body
		$output = str_replace ("<!--{body}-->", $body, $output);
	}

	echo $output;
	unset($output);

?>