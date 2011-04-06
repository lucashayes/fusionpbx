<?php
	//application details
		$apps[$x]['name'] = "System";
		$apps[$x]['guid'] = 'B7EF56FD-57C5-D4E8-BB4B-7887EEDE2E78';
		$apps[$x]['category'] = 'System';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = 'Displays information for CPU, HDD, RAM and more.';

	//menu details
		$apps[$x]['menu'][0]['title']['en'] = 'System Status';
		$apps[$x]['menu'][0]['guid'] = '5243E0D2-0E8B-277A-912E-9D8B5FCDB41D';
		$apps[$x]['menu'][0]['parent_guid'] = '0438B504-8613-7887-C420-C837FFB20CB1';
		$apps[$x]['menu'][0]['category'] = 'internal';
		$apps[$x]['menu'][0]['path'] = '/mod/system/system.php';
		$apps[$x]['menu'][0]['groups'][] = 'admin';
		$apps[$x]['menu'][0]['groups'][] = 'superadmin';

	//permission details
		$apps[$x]['permissions'][0]['name'] = 'system_view_info';
		$apps[$x]['permissions'][0]['groups'][] = 'admin';
		$apps[$x]['permissions'][0]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][1]['name'] = 'system_view_cpu';
		$apps[$x]['permissions'][1]['groups'][] = 'admin';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin'

		$apps[$x]['permissions'][2]['name'] = 'system_view_hdd';
		$apps[$x]['permissions'][2]['groups'][] = 'admin';
		$apps[$x]['permissions'][2]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][3]['name'] = 'system_view_ram';
		$apps[$x]['permissions'][3]['groups'][] = 'admin';
		$apps[$x]['permissions'][3]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][4]['name'] = 'system_view_backup';
		$apps[$x]['permissions'][4]['groups'][] = 'superadmin';
?>