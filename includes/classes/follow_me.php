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
	Copyright (C) 2010
	All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";

//define the follow me class
	class follow_me {
		var $domain_uuid;
		var $db_type;
		var $follow_me_uuid;
		var $extension;
		var $follow_me_enabled;
		var $follow_me_type;
		var $hunt_group_call_prompt;
		var $hunt_group_timeout;

		var $destination_data_1;
		var $destination_type_1;
		var $destination_timeout_1;

		var $destination_data_2;
		var $destination_type_2;
		var $destination_timeout_2;

		var $destination_data_3;
		var $destination_type_3;
		var $destination_timeout_3;

		var $destination_data_4;
		var $destination_type_4;
		var $destination_timeout_4;

		var $destination_data_5;
		var $destination_type_5;
		var $destination_timeout_5;

		var $destination_profile = 'internal';
		var $destination_timeout = '';
		var $destination_order = 1;
		var $destination_enabled = 'true';
		var $destination_descr = 'follow me';

		function follow_me_add() {
			global $db;
			$hunt_group_uuid = uuid();
			$hunt_group_extension = $this->extension;
			$hunt_group_name = 'follow_me_'.$this->extension;
			$hunt_group_type = $this->follow_me_type;
			$hunt_group_context = 'default';
			$hunt_group_timeout = $this->hunt_group_timeout;
			$hunt_group_timeout_destination = $this->extension;
			$hunt_group_timeout_type = 'voicemail';
			$hunt_group_ring_back = 'us-ring';
			//$hunt_group_cid_name_prefix = '';
			//$hunt_group_pin = '';
			$huntgroup_caller_announce = 'false';
			//$hunt_group_user_list = '';
			$hunt_group_enabled = $this->follow_me_enabled;
			$hunt_group_descr = 'follow me '.$this->extension;

			$sql = "insert into v_hunt_groups ";
			$sql .= "(";
			$sql .= "domain_uuid, ";
			$sql .= "hunt_group_uuid, ";
			$sql .= "hunt_group_extension, ";
			$sql .= "hunt_group_name, ";
			$sql .= "hunt_group_type, ";
			$sql .= "hunt_group_context, ";
			$sql .= "hunt_group_timeout, ";
			$sql .= "hunt_group_timeout_destination, ";
			$sql .= "hunt_group_timeout_type, ";
			$sql .= "hunt_group_ringback, ";
			$sql .= "hunt_group_cid_name_prefix, ";
			$sql .= "hunt_group_pin, ";
			$sql .= "hunt_group_call_prompt, ";
			$sql .= "hunt_group_caller_announce, ";
			$sql .= "hunt_group_user_list, ";
			$sql .= "hunt_group_enabled, ";
			$sql .= "hunt_group_descr ";
			$sql .= ")";
			$sql .= "values ";
			$sql .= "(";
			$sql .= "'$this->domain_uuid', ";
			$sql .= "'$hunt_group_uuid', ";
			$sql .= "'$hunt_group_extension', ";
			$sql .= "'$hunt_group_name', ";
			$sql .= "'$hunt_group_type', ";
			$sql .= "'$hunt_group_context', ";
			$sql .= "'$hunt_group_timeout', ";
			$sql .= "'$hunt_group_timeout_destination', ";
			$sql .= "'$hunt_group_timeout_type', ";
			$sql .= "'$hunt_group_ring_back', ";
			$sql .= "'$hunt_group_cid_name_prefix', ";
			$sql .= "'$hunt_group_pin', ";
			$sql .= "'$hunt_group_call_prompt', ";
			$sql .= "'$huntgroup_caller_announce', ";
			$sql .= "'$hunt_group_user_list', ";
			$sql .= "'$hunt_group_enabled', ";
			$sql .= "'$hunt_group_descr' ";
			$sql .= ")";
			if ($v_debug) {
				echo $sql."<br />";
			}
			$db->exec(check_sql($sql));
			$this->follow_me_uuid = $follow_me_uuid;
			unset($sql);
			$this->follow_me_destinations();
		} //end function

		function follow_me_update() {
			global $db;

			$hunt_group_extension = $this->extension;
			$hunt_group_name = 'follow_me_'.$this->extension;
			$hunt_group_type = $this->follow_me_type;
			$hunt_group_context = 'default';
			$hunt_group_timeout = $this->hunt_group_timeout;
			$hunt_group_timeout_destination = $this->extension;
			$hunt_group_timeout_type = 'voicemail';
			$hunt_group_ring_back = 'us-ring';
			//$hunt_group_cid_name_prefix = '';
			//$hunt_group_pin = '';
			$huntgroup_caller_announce = 'false';
			//$hunt_group_user_list = '';
			$hunt_group_enabled = $this->follow_me_enabled;
			$hunt_group_descr = 'follow me '.$this->extension;

			$sql = "update v_hunt_groups set ";
			$sql .= "hunt_group_extension = '$hunt_group_extension', ";
			$sql .= "hunt_group_name = '$hunt_group_name', ";
			$sql .= "hunt_group_type = '$hunt_group_type', ";
			$sql .= "hunt_group_context = '$hunt_group_context', ";
			$sql .= "hunt_group_timeout = '$hunt_group_timeout', ";
			$sql .= "hunt_group_timeout_destination = '$hunt_group_timeout_destination', ";
			$sql .= "hunt_group_timeout_type = '$hunt_group_timeout_type', ";
			$sql .= "hunt_group_ringback = '$hunt_group_ring_back', ";
			$sql .= "hunt_group_cid_name_prefix = '$hunt_group_cid_name_prefix', ";
			$sql .= "hunt_group_pin = '$hunt_group_pin', ";
			$sql .= "hunt_group_call_prompt = '$this->hunt_group_call_prompt', ";
			$sql .= "hunt_group_caller_announce = '$huntgroup_caller_announce', ";
			$sql .= "hunt_group_user_list = '$hunt_group_user_list', ";
			$sql .= "hunt_group_enabled = '$hunt_group_enabled', ";
			$sql .= "hunt_group_descr = '$hunt_group_descr' ";
			$sql .= "where domain_uuid = '$this->domain_uuid' ";
			$sql .= "and hunt_group_uuid = '$this->follow_me_uuid'";
			$db->exec(check_sql($sql));
			unset($sql);
			$this->follow_me_destinations();
		} //end function

		function follow_me_destinations() {
			global $db;

			//delete related v_hunt_group_destinations
				$sql = "delete from v_hunt_group_destinations where hunt_group_uuid = '$this->follow_me_uuid' ";
				$db->exec(check_sql($sql));

			//insert the v_hunt_group_destinations set destination_data_1
				if (strlen($this->destination_data_1) > 0) {
					$sql = "insert into v_hunt_group_destinations ";
					$sql .= "(";
					$sql .= "domain_uuid, ";
					$sql .= "hunt_group_uuid, ";
					$sql .= "destination_data, ";
					$sql .= "destination_type, ";
					$sql .= "destination_profile, ";
					$sql .= "destination_timeout, ";
					$sql .= "destination_order, ";
					$sql .= "destination_enabled, ";
					$sql .= "destination_descr ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$this->domain_uuid', ";
					$sql .= "'$this->follow_me_uuid', ";
					$sql .= "'$this->destination_data_1', ";
					$sql .= "'$this->destination_type_1', ";
					$sql .= "'$this->destination_profile', ";
					$sql .= "'$this->destination_timeout_1', ";
					$sql .= "'$this->destination_order', ";
					$sql .= "'$this->destination_enabled', ";
					$sql .= "'$this->destination_descr' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					$this->destination_order++;
					unset($sql);
				}
				if (strlen($this->destination_data_2) > 0) {
					$sql = "insert into v_hunt_group_destinations ";
					$sql .= "(";
					$sql .= "domain_uuid, ";
					$sql .= "hunt_group_uuid, ";
					$sql .= "destination_data, ";
					$sql .= "destination_type, ";
					$sql .= "destination_profile, ";
					$sql .= "destination_timeout, ";
					$sql .= "destination_order, ";
					$sql .= "destination_enabled, ";
					$sql .= "destination_descr ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$this->domain_uuid', ";
					$sql .= "'$this->follow_me_uuid', ";
					$sql .= "'$this->destination_data_2', ";
					$sql .= "'$this->destination_type_2', ";
					$sql .= "'$this->destination_profile', ";
					$sql .= "'$this->destination_timeout_2', ";
					$sql .= "'$this->destination_order', ";
					$sql .= "'$this->destination_enabled', ";
					$sql .= "'$this->destination_descr' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					$this->destination_order++;
					unset($sql);
				}
				if (strlen($this->destination_data_3) > 0) {
					$sql = "insert into v_hunt_group_destinations ";
					$sql .= "(";
					$sql .= "domain_uuid, ";
					$sql .= "hunt_group_uuid, ";
					$sql .= "destination_data, ";
					$sql .= "destination_type, ";
					$sql .= "destination_profile, ";
					$sql .= "destination_timeout, ";
					$sql .= "destination_order, ";
					$sql .= "destination_enabled, ";
					$sql .= "destination_descr ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$this->domain_uuid', ";
					$sql .= "'$this->follow_me_uuid', ";
					$sql .= "'$this->destination_data_3', ";
					$sql .= "'$this->destination_type_3', ";
					$sql .= "'$this->destination_profile', ";
					$sql .= "'$this->destination_timeout_3', ";
					$sql .= "'$this->destination_order', ";
					$sql .= "'$this->destination_enabled', ";
					$sql .= "'$this->destination_descr' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					$this->destination_order++;
					unset($sql);
				}
				if (strlen($this->destination_data_4) > 0) {
					$sql = "insert into v_hunt_group_destinations ";
					$sql .= "(";
					$sql .= "domain_uuid, ";
					$sql .= "hunt_group_uuid, ";
					$sql .= "destination_data, ";
					$sql .= "destination_type, ";
					$sql .= "destination_profile, ";
					$sql .= "destination_timeout, ";
					$sql .= "destination_order, ";
					$sql .= "destination_enabled, ";
					$sql .= "destination_descr ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$this->domain_uuid', ";
					$sql .= "'$this->follow_me_uuid', ";
					$sql .= "'$this->destination_data_4', ";
					$sql .= "'$this->destination_type_4', ";
					$sql .= "'$this->destination_profile', ";
					$sql .= "'$this->destination_timeout_4', ";
					$sql .= "'$this->destination_order', ";
					$sql .= "'$this->destination_enabled', ";
					$sql .= "'$this->destination_descr' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					$this->destination_order++;
					unset($sql);
				}
				if (strlen($this->destination_data_5) > 0) {
					$sql = "insert into v_hunt_group_destinations ";
					$sql .= "(";
					$sql .= "domain_uuid, ";
					$sql .= "hunt_group_uuid, ";
					$sql .= "destination_data, ";
					$sql .= "destination_type, ";
					$sql .= "destination_profile, ";
					$sql .= "destination_timeout, ";
					$sql .= "destination_order, ";
					$sql .= "destination_enabled, ";
					$sql .= "destination_descr ";
					$sql .= ")";
					$sql .= "values ";
					$sql .= "(";
					$sql .= "'$this->domain_uuid', ";
					$sql .= "'$this->follow_me_uuid', ";
					$sql .= "'$this->destination_data_5', ";
					$sql .= "'$this->destination_type_5', ";
					$sql .= "'$this->destination_profile', ";
					$sql .= "'$this->destination_timeout_5', ";
					$sql .= "'$this->destination_order', ";
					$sql .= "'$this->destination_enabled', ";
					$sql .= "'$this->destination_descr' ";
					$sql .= ")";
					$db->exec(check_sql($sql));
					$this->destination_order++;
					unset($sql);
				}
		} //function
	} //class

?>