<?php
	//application details
		$apps[$x]['name'] = "User Contacts";
		$apps[$x]['uuid'] = 'C82C2D32-301F-D3BD-50AD-34D75419C778';
		$apps[$x]['category'] = 'System';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = 'Manage User Contacts.';

	//menu details
		$apps[$x]['menu'][0]['title']['en'] = 'User Contacts';
		$apps[$x]['menu'][0]['uuid'] = '87AC2EC3-FA23-2A60-6DE6-07AC2A8AA4D9';
		$apps[$x]['menu'][0]['parent_uuid'] = 'FD29E39C-C936-F5FC-8E2B-611681B266B5';
		$apps[$x]['menu'][0]['category'] = 'internal';
		$apps[$x]['menu'][0]['path'] = '/mod/user_contacts/users.php';
		$apps[$x]['menu'][0]['groups'][] = 'admin';
		$apps[$x]['menu'][0]['groups'][] = 'superadmin';

	//permission details
		$apps[$x]['permissions'][0]['name'] = 'contacts_view';
		$apps[$x]['permissions'][0]['groups'][] = 'admin';
		$apps[$x]['permissions'][0]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][1]['name'] = 'contacts_add';
		$apps[$x]['permissions'][1]['groups'][] = 'admin';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][2]['name'] = 'contacts_edit';
		$apps[$x]['permissions'][2]['groups'][] = 'admin';
		$apps[$x]['permissions'][2]['groups'][] = 'superadmin';

		$apps[$x]['permissions'][3]['name'] = 'contacts_delete';
		$apps[$x]['permissions'][3]['groups'][] = 'admin';
		$apps[$x]['permissions'][3]['groups'][] = 'superadmin';

	//schema details
		$y = 0; //table array index
		$z = 0; //field array index
		$apps[$x]['db'][$y]['table'] = 'v_users';
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'id';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'integer';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
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
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_type';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_category';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_first_name';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_last_name';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_company_name';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_physical_address_1';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_physical_address_2';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_physical_city';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_physical_state_province';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_physical_country';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_physical_postal_code';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_mailing_address_1';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_mailing_address_2';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_mailing_city';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_mailing_state_province';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_mailing_country';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_mailing_postal_code';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_billing_address_1';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_billing_address_2';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_billing_city';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_billing_state_province';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_billing_country';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_billing_postal_code';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_shipping_address_1';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_shipping_address_2';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_shipping_city';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_shipping_state_province';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_shipping_country';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_shipping_postal_code';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_url';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_phone_1';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_phone_1_ext';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_phone_2';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_phone_2_ext';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_phone_mobile';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_phone_emergency_mobile';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_email_emergency';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_phone_fax';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_template_name';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_email';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_notes';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_optional_1';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_optional_2';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_status';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_time_zone';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_add_user';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'user_add_date';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'customer_id';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
?>