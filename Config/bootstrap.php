<?php

/**
 * Optionally Include Foreign Keys in Reports
 *
 * When there is a belongs_to relationship, the model will have
 * foreign keys (e.g. a comments table may have a field blog_post_id).
 * If set to true, then all these fields will be available for reports.
 * If set to false, then all fields that match the pattern *_id will
 * be excluded from the reports.
 */
Configure::write('CustomReporting.displayForeignKeys', false);



/**
 * A Whitelist of Models to report on
 *
 * If set to an array, then we will only allow the users to run
 * reports on this list of models. If set to false, then the
 * system will present the full list of models in the system.
 * In either case, we will also remove any models included in
 * the CustomReporting.modelBlackList setting.
 */
if (!is_array(Configure::read('CustomReporting.modelWhitelist'))) {
	Configure::write('CustomReporting.modelWhitelist',	array(
		'Users',
		'Categories',
		'Hobbies',
		'Widgets',
		'Foo',
		'Bar'
	));
}

/**
 * A Blacklist of Models that should not be made available
 *
 * If set to an array, then we will not allow the users to run
 * reports on on any models. In this list. Note, this is only 
 * for the root model. Any models included with a BelongsTo or
 * HasMany relationship will not reference this list.
 */
if (!is_array(Configure::read('CustomReporting.modelBlacklist'))) {
	Configure::write('CustomReporting.modelBlacklist',array(
		'SecretThings',
		'PrivateObjects',
	));
}

/**
 * A Blacklist of Fields to be excluded from reports
 *
 * This blacklist will be removed from ALL models that are included
 * in reports. If you have any model-specific fields to exclude, you
 * can add them to the modelFieldBlacklist setting below
 */
if (!is_array(Configure::read('CustomReporting.globalFieldBlacklist'))) {
	Configure::write('CustomReporting.globalFieldBlacklist', array(
		'id',
		'password',
		'secret',
		'private'
	));
}

if (!is_array(Configure::read('CustomReporting.modelFieldBlacklist'))) {
	Configure::write('CustomReporting.modelFieldBlacklist',array(
		'User' => array(
			'api_key',		
			'field1'=>'field1',
			'field2'=>'field2',
			'field3'=>'field3',
		)
	));
}

?>