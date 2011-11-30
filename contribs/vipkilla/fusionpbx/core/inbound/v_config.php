<?php
	//application details
		$apps[$x]['name'] = "Inbound Group Manager";
		$apps[$x]['guid'] = '912124B3-95C2-5352-7E9D-D14C0B88F207';
		$apps[$x]['category'] = 'Core';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = 'Add, edit, delete, and search for inbound groups.';

	//menu details
		$apps[$x]['menu'][0]['title']['en'] = 'Inbound Groups';
		$apps[$x]['menu'][0]['guid'] = '90767BCC-3FC8-CD62-0260-8F8C9802526F';
		$apps[$x]['menu'][0]['parent_guid'] = '02194288-6D56-6D3E-0B1A-D53A2BC10788';
		$apps[$x]['menu'][0]['category'] = 'internal';
		$apps[$x]['menu'][0]['path'] = '/core/inbound/index.php';
		$apps[$x]['menu'][0]['groups'][] = 'superadmin';

                $apps[$x]['menu'][1]['title']['en'] = 'Inbound Classes';
                $apps[$x]['menu'][1]['guid'] = '2D451010-298D-10E4-1A20-49CC5C8AA3CA';
                $apps[$x]['menu'][1]['parent_guid'] = '02194288-6D56-6D3E-0B1A-D53A2BC10788';
                $apps[$x]['menu'][1]['category'] = 'internal';
                $apps[$x]['menu'][1]['path'] = '/core/inbound/listclasses.php';
                $apps[$x]['menu'][1]['groups'][] = 'superadmin';

                $apps[$x]['menu'][2]['title']['en'] = 'Inbound Numbers';
                $apps[$x]['menu'][2]['guid'] = 'F598F800-4873-CB39-CB8A-A7F948C7BCC9';
                $apps[$x]['menu'][2]['parent_guid'] = '02194288-6D56-6D3E-0B1A-D53A2BC10788';
                $apps[$x]['menu'][2]['category'] = 'internal';
                $apps[$x]['menu'][2]['path'] = '/core/inbound/listnumbers.php';
                $apps[$x]['menu'][2]['groups'][] = 'superadmin';

	//permission details
		$apps[$x]['permissions'][0]['name'] = 'ingroup_view';
		$apps[$x]['permissions'][0]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][1]['name'] = 'ingroup_add';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][2]['name'] = 'ingroup_edit';
		$apps[$x]['permissions'][2]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][3]['name'] = 'ingroup_delete';
		$apps[$x]['permissions'][3]['groups'][] = 'superadmin';

	//schema details
		$apps[$x]['db'][0]['table'] = 'v_ingroups';
		$apps[$x]['db'][0]['fields'][0]['name'] = 'id';
		$apps[$x]['db'][0]['fields'][0]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][0]['fields'][0]['type']['sqlite'] = 'integer PRIMARY KEY';
		$apps[$x]['db'][0]['fields'][0]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY';
		$apps[$x]['db'][0]['fields'][0]['description'] = '';
		$apps[$x]['db'][0]['fields'][1]['name'] = 'name';
		$apps[$x]['db'][0]['fields'][1]['type'] = 'text';
		$apps[$x]['db'][0]['fields'][1]['description'] = '';
		$apps[$x]['db'][0]['fields'][2]['name'] = 'description';
		$apps[$x]['db'][0]['fields'][2]['type'] = 'text';
		$apps[$x]['db'][0]['fields'][2]['description'] = '';
		$apps[$x]['db'][0]['fields'][3]['name'] = 'areacode';
		$apps[$x]['db'][0]['fields'][3]['type'] = 'text';
		$apps[$x]['db'][0]['fields'][3]['description'] = '';
		$apps[$x]['db'][0]['fields'][4]['name'] = 'country';
		$apps[$x]['db'][0]['fields'][4]['type'] = 'text';
		$apps[$x]['db'][0]['fields'][4]['description'] = '';

		$apps[$x]['db'][1]['table'] = 'v_numbers';
		$apps[$x]['db'][1]['fields'][0]['name'] = 'id';
                $apps[$x]['db'][1]['fields'][0]['type']['pgsql'] = 'serial';
                $apps[$x]['db'][1]['fields'][0]['type']['sqlite'] = 'integer PRIMARY KEY';
                $apps[$x]['db'][1]['fields'][0]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY';
		$apps[$x]['db'][1]['fields'][0]['description'] = '';
                $apps[$x]['db'][1]['fields'][1]['name'] = 'v_id';
                $apps[$x]['db'][1]['fields'][1]['type'] = 'numeric';
                $apps[$x]['db'][1]['fields'][1]['description'] = '';
		$apps[$x]['db'][1]['fields'][2]['name'] = 'domain';
		$apps[$x]['db'][1]['fields'][2]['type'] = 'text';
		$apps[$x]['db'][1]['fields'][2]['description'] = '';
		$apps[$x]['db'][1]['fields'][3]['name'] = 'ingroup';
		$apps[$x]['db'][1]['fields'][3]['type'] = 'text';
		$apps[$x]['db'][1]['fields'][3]['description'] = '';
		$apps[$x]['db'][1]['fields'][4]['name'] = 'class';
		$apps[$x]['db'][1]['fields'][4]['type'] = 'text';
		$apps[$x]['db'][1]['fields'][4]['description'] = '';
		$apps[$x]['db'][1]['fields'][5]['name'] = 'number';
		$apps[$x]['db'][1]['fields'][5]['type'] = 'numeric';
		$apps[$x]['db'][1]['fields'][5]['description'] = '';
                $apps[$x]['db'][1]['fields'][6]['name'] = 'condition_expression_1';
                $apps[$x]['db'][1]['fields'][6]['type'] = 'text';
                $apps[$x]['db'][1]['fields'][6]['description'] = '';
		$apps[$x]['db'][1]['fields'][7]['name'] = 'public_include_id';
		$apps[$x]['db'][1]['fields'][7]['type'] = 'numeric';
		$apps[$x]['db'][1]['fields'][7]['description'] = '';

                $apps[$x]['db'][2]['table'] = 'v_number_classes';
                $apps[$x]['db'][2]['fields'][0]['name'] = 'id';
                $apps[$x]['db'][2]['fields'][0]['type']['pgsql'] = 'serial';
                $apps[$x]['db'][2]['fields'][0]['type']['sqlite'] = 'integer PRIMARY KEY';
                $apps[$x]['db'][2]['fields'][0]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY';
                $apps[$x]['db'][2]['fields'][0]['description'] = '';
                $apps[$x]['db'][2]['fields'][1]['name'] = 'name';
                $apps[$x]['db'][2]['fields'][1]['type'] = 'text';
                $apps[$x]['db'][2]['fields'][1]['description'] = '';
                $apps[$x]['db'][2]['fields'][2]['name'] = 'description';
                $apps[$x]['db'][2]['fields'][2]['type'] = 'text';
                $apps[$x]['db'][2]['fields'][2]['description'] = '';
		$apps[$x]['db'][2]['fields'][3]['name'] = 'fee';
		$apps[$x]['db'][2]['fields'][3]['type'] = 'numeric';
		$apps[$x]['db'][2]['fields'][3]['description'] = '';
		$apps[$x]['db'][2]['fields'][4]['name'] = 'fee_type';
		$apps[$x]['db'][2]['fields'][4]['type'] = 'text';
		$apps[$x]['db'][2]['fields'][4]['description'] = '';

?>
