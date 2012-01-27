<?php
	//application details
		$apps[$x]['name'] = "User Manager";
		$apps[$x]['uuid'] = '112124B3-95C2-5352-7E9D-D14C0B88F207';
		$apps[$x]['category'] = 'Core';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = 'Add, edit, delete, and search for users.';

	//menu details
		$apps[$x]['menu'][0]['title']['en'] = 'Login';
		$apps[$x]['menu'][0]['uuid'] = 'C85BF816-B88D-40FA-8634-11B456928AFA';
		$apps[$x]['menu'][0]['parent_uuid'] = '';
		$apps[$x]['menu'][0]['category'] = 'internal';
		$apps[$x]['menu'][0]['path'] = '/login.php';
		$apps[$x]['menu'][0]['groups'][] = 'public';
		$apps[$x]['menu'][0]['order'] = '99';

		$apps[$x]['menu'][1]['title']['en'] = 'Logout';
		$apps[$x]['menu'][1]['uuid'] = '0D29E9F4-0C9B-9D8D-CD2D-454899DC9BC4';
		$apps[$x]['menu'][1]['parent_uuid'] = '02194288-6D56-6D3E-0B1A-D53A2BC10788';
		$apps[$x]['menu'][1]['category'] = 'internal';
		$apps[$x]['menu'][1]['path'] = '/logout.php';
		$apps[$x]['menu'][1]['groups'][] = 'user';
		$apps[$x]['menu'][1]['groups'][] = 'admin';
		$apps[$x]['menu'][1]['groups'][] = 'superadmin';

		$apps[$x]['menu'][2]['title']['en'] = 'User Manager';
		$apps[$x]['menu'][2]['uuid'] = '0D57CC1E-1874-47B9-7DDD-FE1F57CEC99B';
		$apps[$x]['menu'][2]['parent_uuid'] = 'BC96D773-EE57-0CDD-C3AC-2D91ABA61B55';
		$apps[$x]['menu'][2]['category'] = 'internal';
		$apps[$x]['menu'][2]['path'] = '/core/users/index.php';
		$apps[$x]['menu'][2]['groups'][] = 'admin';
		$apps[$x]['menu'][2]['groups'][] = 'superadmin';

		$apps[$x]['menu'][3]['title']['en'] = 'Group Manager';
		$apps[$x]['menu'][3]['uuid'] = '3B4ACC6D-827B-F537-BF21-0093D94FFEC7';
		$apps[$x]['menu'][3]['parent_uuid'] = '594D99C5-6128-9C88-CA35-4B33392CEC0F';
		$apps[$x]['menu'][3]['category'] = 'internal';
		$apps[$x]['menu'][3]['path'] = '/core/users/grouplist.php';
		$apps[$x]['menu'][3]['groups'][] = 'superadmin';

	//permission details
		$apps[$x]['permissions'][0]['name'] = 'user_view';
		$apps[$x]['permissions'][0]['groups'][] = 'admin';
		$apps[$x]['permissions'][0]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][1]['name'] = 'user_add';
		$apps[$x]['permissions'][1]['groups'][] = 'admin';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][2]['name'] = 'user_edit';
		$apps[$x]['permissions'][2]['groups'][] = 'admin';
		$apps[$x]['permissions'][2]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][3]['name'] = 'user_delete';
		$apps[$x]['permissions'][3]['groups'][] = 'admin';
		$apps[$x]['permissions'][3]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][4]['name'] = 'group_view';
		$apps[$x]['permissions'][4]['groups'][] = 'admin';
		$apps[$x]['permissions'][4]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][5]['name'] = 'group_add';
		$apps[$x]['permissions'][5]['groups'][] = 'admin';
		$apps[$x]['permissions'][5]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][6]['name'] = 'group_edit';
		$apps[$x]['permissions'][6]['groups'][] = 'admin';
		$apps[$x]['permissions'][6]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][7]['name'] = 'group_delete';
		$apps[$x]['permissions'][7]['groups'][] = 'admin';
		$apps[$x]['permissions'][7]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][8]['name'] = 'group_member_view';
		$apps[$x]['permissions'][8]['groups'][] = 'admin';
		$apps[$x]['permissions'][8]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][9]['name'] = 'group_member_add';
		$apps[$x]['permissions'][9]['groups'][] = 'admin';
		$apps[$x]['permissions'][9]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][10]['name'] = 'group_member_delete';
		$apps[$x]['permissions'][10]['groups'][] = 'admin';
		$apps[$x]['permissions'][10]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][11]['name'] = 'group_permissions';
		$apps[$x]['permissions'][11]['groups'][] = 'superadmin';

	//schema details
		$y = 0; //table array index
		$z = 0; //field array index
		$apps[$x]['db'][$y]['table'] = 'v_group_members';
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'id';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'integer';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'foreign';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'v_id';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'group_id';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'groupid';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		//$z++;
		//$apps[$x]['db'][$y]['fields'][$z]['name'] = 'group_uuid';
		//$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		//$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		//$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		//$apps[$x]['db'][$y]['fields'][$z]['key'] = 'foreign';
		//$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'username';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';

		$y = 1; //table array index
		$z = 0; //field array index
		$apps[$x]['db'][$y]['table'] = 'v_group_permissions';
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'id';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'group_permission_id';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'integer';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'foreign';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'v_id';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'permission_id';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'group_id';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';

		$y = 2; //table array index
		$z = 0; //field array index
		$apps[$x]['db'][$y]['table'] = 'v_groups';
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'id';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'integer';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'foreign';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'v_id';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'group_id';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'groupid';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'group_desc';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'groupdesc';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';

		$y = 3; //table array index
		$z = 0; //field array index
		$apps[$x]['db'][$y]['table'] = 'v_users';
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'id';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'integer';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'domain_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'foreign';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'v_id';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'primary';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'username';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'password';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'salt';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_type';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'usertype';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_category';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'usercategory';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_first_name';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userfirstname';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_last_name';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userlastname';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_company_name';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'usercompanyname';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_physical_address_1';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userphysicaladdress1';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_physical_address_2';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userphysicaladdress2';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_physical_city';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userphysicalcity';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_physical_state_province';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userphysicalstateprovince';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_physical_country';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userphysicalcountry';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_physical_postal_code';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userphysicalpostalcode';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_mailing_address_1';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'usermailingaddress1';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_mailing_address_2';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'usermailingaddress2';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_mailing_city';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'usermailingcity';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_mailing_state_province';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'usermailingstateprovince';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_mailing_country';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'usermailingcountry';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_mailing_postal_code';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'usermailingpostalcode';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_billing_address_1';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userbillingaddress1';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_billing_address_2';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userbillingaddress2';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_billing_city';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userbillingcity';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_billing_state_province';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userbillingstateprovince';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_billing_country';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userbillingcountry';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_billing_postal_code';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userbillingpostalcode';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_shipping_address_1';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'usershippingaddress1';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_shipping_address_2';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'usershippingaddress2';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_shipping_city';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'usershippingcity';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_shipping_state_province';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'usershippingstateprovince';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_shipping_country';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'usershippingcountry';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_shipping_postal_code';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'usershippingpostalcode';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_url';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userurl';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_phone_1';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userphone1';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_phone_1_ext';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userphone1ext';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_phone_2';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userphone2';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_phone_2_ext';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userphone2ext';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_phone_mobile';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userphonemobile';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_phone_emergency_mobile';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userphoneemergencymobile';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_email_emergency';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'useremailemergency';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_phone_fax';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userphonefax';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_template_name';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'usertemplatename';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_email';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'useremail';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_notes';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'usernotes';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_optional_1';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'useroptional1';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_optional_2';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'useroptional2';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_status';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'userstatus';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_time_zone';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'usertimezone';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_add_user';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'useradduser';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'user_add_date';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'useradddate';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'customer_id';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'contact_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
?>