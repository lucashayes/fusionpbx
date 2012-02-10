<?php
	//application details
		$apps[$x]['name'] = 'Contacts';
		$apps[$x]['uuid'] = '04481e0e-a478-c559-adad-52bd4174574c';
		$apps[$x]['category'] = '';
		$apps[$x]['subcategory'] = '';
		$apps[$x]['version'] = '';
		$apps[$x]['license'] = 'Mozilla Public License 1.1';
		$apps[$x]['url'] = 'http://www.fusionpbx.com';
		$apps[$x]['description']['en'] = '';

	//menu details
		$apps[$x]['menu'][$y]['title']['en'] = 'Contacts';
		$apps[$x]['menu'][$y]['uuid'] = 'f14e6ab6-6565-d4e6-cbad-a51d2e3e8ec6';
		$apps[$x]['menu'][$y]['parent_uuid'] = 'fd29e39c-c936-f5fc-8e2b-611681b266b5';
		$apps[$x]['menu'][$y]['category'] = 'internal';
		$apps[$x]['menu'][$y]['path'] = '/mod/contacts/v_contacts.php';
		//$apps[$x]['menu'][$y]['groups'][] = 'user';
		$apps[$x]['menu'][$y]['groups'][] = 'admin';
		$apps[$x]['menu'][$y]['groups'][] = 'superadmin';

	//permission details
		$apps[$x]['permissions'][0]['name'] = 'contacts_view';
		$apps[$x]['permissions'][0]['groups'][] = 'superadmin';
		//$apps[$x]['permissions'][0]['groups'][] = 'user';
		$apps[$x]['permissions'][0]['groups'][] = 'admin';

		$apps[$x]['permissions'][1]['name'] = 'contacts_add';
		$apps[$x]['permissions'][1]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][1]['groups'][] = 'admin';

		$apps[$x]['permissions'][2]['name'] = 'contacts_edit';
		$apps[$x]['permissions'][2]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][2]['groups'][] = 'admin';
		//$apps[$x]['permissions'][2]['groups'][] = 'user';

		$apps[$x]['permissions'][3]['name'] = 'contacts_delete';
		$apps[$x]['permissions'][3]['groups'][] = 'superadmin';
		$apps[$x]['permissions'][3]['groups'][] = 'admin';

	//schema details
		$y = 0; //table array index
		$z = 0; //field array index
		$apps[$x]['db'][$y]['table'] = 'v_contacts';
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'id';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'contact_id';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'integer';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'contact_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'primary';
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
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'type';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the type.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'org';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the organization.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'n_given';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the given name.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'n_family';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the family name.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'nickname';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the nickname.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'title';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the title.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'role';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the role.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'email';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the email address.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'url';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the website address.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'tz';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the time zone.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'note';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the notes.';
		$z++;

		$y = 1; //table array index
		$apps[$x]['db'][$y]['table'] = 'v_contacts_adr';
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'id';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'contacts_adr_id';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'integer';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'contact_adr_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'primary';
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
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'contact_id';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Contact ID';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'contact_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'foreign';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'adr_type';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the address type.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'adr_street';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the street address.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'adr_extended';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter teh extended address.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'adr_locality';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the city.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'adr_region';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the state or province.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'adr_postal_code';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the postal code.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'adr_country';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the country.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'adr_latitude';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the latitude';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'adr_longitude';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the longitude';
		$z++;

		$y = 2; //table array index
		$apps[$x]['db'][$y]['table'] = 'v_contacts_tel';
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'id';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'contacts_tel_id';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'integer';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'contact_tel_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'primary';
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
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'contact_id';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Contact ID';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'contact_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'foreign';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'tel_type';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the  telephone type.';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'tel_number';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Enter the telephone number.';
		$z++;

		$y = 3; //table array index
		$apps[$x]['db'][$y]['table'] = 'v_contact_notes';
		$apps[$x]['db'][$y]['fields'][$z]['name']['text'] = 'id';
		$apps[$x]['db'][$y]['fields'][$z]['name']['deprecated'] = 'contacts_note_id';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'serial';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'integer';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'INT NOT NULL AUTO_INCREMENT';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'contact_note_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['key'] = 'primary';
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
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'contact_id';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'numeric';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = 'Contact ID';
		$apps[$x]['db'][$y]['fields'][$z]['deprecated'] = 'true';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'contact_uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['pgsql'] = 'uuid';
		$apps[$x]['db'][$y]['fields'][$z]['type']['sqlite'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['type']['mysql'] = 'char(36)';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'notes';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'last_mod_date';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
		$apps[$x]['db'][$y]['fields'][$z]['name'] = 'last_mod_user';
		$apps[$x]['db'][$y]['fields'][$z]['type'] = 'text';
		$apps[$x]['db'][$y]['fields'][$z]['description'] = '';
		$z++;
?>