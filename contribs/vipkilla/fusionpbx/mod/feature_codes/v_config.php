<?php
	//application details
		$apps[$x]['name'] = "Feature Codes";
		$apps[$x]['guid'] = '52653013-2769-E013-28FA-EF2050D325ED';
		$apps[$x]['category'] = 'PBX';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = 'Used Configure feature codes. ';

	//menu details
		$apps[$x]['menu'][0]['title']['en'] = 'Feature Codes';
		$apps[$x]['menu'][0]['guid'] = '52653013-AB5C-C486-D71C-EF2050D325ED';
		$apps[$x]['menu'][0]['parent_guid'] = 'BC96D773-EE57-0CDD-C3AC-2D91ABA61B55';
		$apps[$x]['menu'][0]['category'] = 'internal';
		$apps[$x]['menu'][0]['path'] = '/mod/feature_codes/index.php';
		$apps[$x]['menu'][0]['groups'][] = 'admin';
		$apps[$x]['menu'][0]['groups'][] = 'superadmin';

	//permission details
		$apps[$x]['permissions'][0]['name'] = 'feature_codes_view';
		$apps[$x]['permissions'][0]['groups'][] = 'admin';
		$apps[$x]['permissions'][0]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][1]['name'] = 'feature_codes_add';
		$apps[$x]['permissions'][1]['groups'][] = 'admin';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][2]['name'] = 'feature_codes_edit';
		$apps[$x]['permissions'][2]['groups'][] = 'admin';
		$apps[$x]['permissions'][2]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][3]['name'] = 'feature_codes_delete';
		$apps[$x]['permissions'][3]['groups'][] = 'admin';
		$apps[$x]['permissions'][3]['groups'][] = 'superadmin';

	//schema details
		$y = 0;
		$apps[$x]['db'][0]['table'] = 'v_feature_codes';
		$apps[$x]['db'][0]['fields'][$y]['name'] = 'feature_code_id';
		$apps[$x]['db'][0]['fields'][$y]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][0]['fields'][$y]['type']['sqlite'] = 'integer PRIMARY KEY';
		$apps[$x]['db'][0]['fields'][$y]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY';
		$apps[$x]['db'][0]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][0]['fields'][$y]['name'] = 'v_id';
		$apps[$x]['db'][0]['fields'][$y]['type'] = 'numeric';
		$apps[$x]['db'][0]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][0]['fields'][$y]['name'] = 'feature_code';
		$apps[$x]['db'][0]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][0]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][0]['fields'][$y]['name'] = 'feature_type';
		$apps[$x]['db'][0]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][0]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][0]['fields'][$y]['name'] = 'feature_id';
		$apps[$x]['db'][0]['fields'][$y]['type'] = 'numeric';
		$apps[$x]['db'][0]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][0]['fields'][$y]['name'] = 'feature_table';
		$apps[$x]['db'][0]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][0]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][0]['fields'][$y]['name'] = 'feature_edit_link';
		$apps[$x]['db'][0]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][0]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][0]['fields'][$y]['name'] = 'feature_delete_link';
		$apps[$x]['db'][0]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][0]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][0]['fields'][$y]['name'] = 'feature_label';
		$apps[$x]['db'][0]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][0]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][0]['fields'][$y]['name'] = 'feature_xml';
		$apps[$x]['db'][0]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][0]['fields'][$y]['description'] = '';
		$y++;
		$apps[$x]['db'][0]['fields'][$y]['name'] = 'feature_display';
		$apps[$x]['db'][0]['fields'][$y]['type'] = 'text';
		$apps[$x]['db'][0]['fields'][$y]['description'] = '';
		$y++;		
?>
