<?php
	//application details
		$apps[$x]['name'] = "Caller Route";
		$apps[$x]['guid'] = '00999E30-37ED-5B84-EC8E-866C7293C443';
		$apps[$x]['category'] = 'PBX';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = 'Route inbound calls based on their CID.';

	//menu details
		$apps[$x]['menu'][0]['title']['en'] = 'Caller Routes';
		$apps[$x]['menu'][0]['guid'] = 'A3630914-BFD6-2F28-2D7F-27689A9FCFD1';
		$apps[$x]['menu'][0]['parent_guid'] = 'B94E8BD9-9EB5-E427-9C26-FF7A6C21552A';
		$apps[$x]['menu'][0]['category'] = 'internal';
		$apps[$x]['menu'][0]['path'] = '/mod/caller_route/v_caller_route.php';
		$apps[$x]['menu'][0]['groups'][] = 'admin';
		$apps[$x]['menu'][0]['groups'][] = 'superadmin';

	//permission details
		$apps[$x]['permissions'][0]['name'] = 'caller_route_view';
		$apps[$x]['permissions'][0]['groups'][] = 'admin';
		$apps[$x]['permissions'][0]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][1]['name'] = 'caller_route_add';
		$apps[$x]['permissions'][1]['groups'][] = 'admin';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][2]['name'] = 'caller_route_edit';
		$apps[$x]['permissions'][2]['groups'][] = 'admin';
		$apps[$x]['permissions'][2]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][3]['name'] = 'caller_route_delete';
		$apps[$x]['permissions'][3]['groups'][] = 'admin';
		$apps[$x]['permissions'][3]['groups'][] = 'superadmin';

        //schema details
                $y = 0;
                $apps[$x]['db'][0]['table'] = 'v_caller_routes';
                $apps[$x]['db'][0]['fields'][$y]['name'] = 'caller_route_id';
                $apps[$x]['db'][0]['fields'][$y]['type']['pgsql'] = 'serial';
                $apps[$x]['db'][0]['fields'][$y]['type']['sqlite'] = 'integer PRIMARY KEY';
                $apps[$x]['db'][0]['fields'][$y]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY';
                $apps[$x]['db'][0]['fields'][$y]['description'] = '';
                $y++;
                $apps[$x]['db'][0]['fields'][$y]['name'] = 'v_id';
                $apps[$x]['db'][0]['fields'][$y]['type'] = 'numeric';
                $apps[$x]['db'][0]['fields'][$y]['description'] = '';
                $y++;
                $apps[$x]['db'][0]['fields'][$y]['name'] = 'name';
                $apps[$x]['db'][0]['fields'][$y]['type'] = 'text';
                $apps[$x]['db'][0]['fields'][$y]['description'] = '';
                $y++;
                $apps[$x]['db'][0]['fields'][$y]['name'] = 'cid_prefix';
                $apps[$x]['db'][0]['fields'][$y]['type'] = 'text';
                $apps[$x]['db'][0]['fields'][$y]['description'] = '';
                $y++;
                $apps[$x]['db'][0]['fields'][$y]['name'] = 'cid_action';
                $apps[$x]['db'][0]['fields'][$y]['type'] = 'text';
                $apps[$x]['db'][0]['fields'][$y]['description'] = '';
                $y++;
                $apps[$x]['db'][0]['fields'][$y]['name'] = 'cid_destination';
                $apps[$x]['db'][0]['fields'][$y]['type'] = 'text';
                $apps[$x]['db'][0]['fields'][$y]['description'] = '';
                $y++;
                $apps[$x]['db'][0]['fields'][$y]['name'] = 'public_include_id';
                $apps[$x]['db'][0]['fields'][$y]['type'] = 'numeric';
                $apps[$x]['db'][0]['fields'][$y]['description'] = '';
                $y++;
                $apps[$x]['db'][0]['fields'][$y]['name'] = 'enabled';
                $apps[$x]['db'][0]['fields'][$y]['type'] = 'text';
                $apps[$x]['db'][0]['fields'][$y]['description'] = '';
                $y++;
                $apps[$x]['db'][0]['fields'][$y]['name'] = 'description';
                $apps[$x]['db'][0]['fields'][$y]['type'] = 'text';
                $apps[$x]['db'][0]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][0]['fields'][$y]['name'] = 'publicorder';
                $apps[$x]['db'][0]['fields'][$y]['type'] = 'numeric';
                $apps[$x]['db'][0]['fields'][$y]['description'] = '';

?>
