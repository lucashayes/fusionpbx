<?php
	$apps[$x]['name'] = "Contacts";
	$apps[$x]['guid'] = 'C82C2D32-301F-D3BD-50AD-34D75419C778';
	$apps[$x]['category'] = 'System';
	$apps[$x]['subcategory'] = '';
	$apps[$x]['version'] = '';
	$apps[$x]['menu'][0]['title']['en'] = 'Contact Manager';
	$apps[$x]['menu'][0]['guid'] = '87AC2EC3-FA23-2A60-6DE6-07AC2A8AA4D9';
	$apps[$x]['menu'][0]['parent_guid'] = 'FD29E39C-C936-F5FC-8E2B-611681B266B5';
	$apps[$x]['menu'][0]['category'] = 'internal';
	$apps[$x]['menu'][0]['path'] = '/mod/contacts/users.php';
	$apps[$x]['menu'][0]['groups'][] = 'user';
	$apps[$x]['menu'][0]['groups'][] = 'admin';
	$apps[$x]['menu'][0]['groups'][] = 'superadmin';
	$apps[$x]['permissions'][] = 'contacts_view';
	$apps[$x]['permissions'][] = 'contacts_add';
	$apps[$x]['permissions'][] = 'contacts_edit';
	$apps[$x]['permissions'][] = 'contacts_delete';
	$apps[$x]['license'] = 'Mozilla Public License 1.1';
	$apps[$x]['url'] = 'http://www.fusionpbx.com';
	$apps[$x]['description']['en'] = 'Manage contacts.';
?>