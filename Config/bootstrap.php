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
Configure::write('AdHocReporting.displayForeignKeys', false);



/**
 * A Whitelist of Models to report on
 *
 * If set to an array, then we will only allow the users to run
 * reports on this list of models. If set to false, then the
 * system will present the full list of models in the system.
 * In either case, we will also remove any models included in
 * the AdHocReporting.modelBlackList setting.
 *
 * if this list is false, then everything is included.
 * if this list is an array, then only the models in the array are included. It becomes
 * an exclusive whitelist. 
 * The blacklist is still applied to whatever is allowed here.
 */
if (!is_array(Configure::read('AdHocReporting.modelWhitelist'))) {
	Configure::write('AdHocReporting.modelWhitelist',	array(
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
if (!is_array(Configure::read('AdHocReporting.modelBlacklist'))) {
	Configure::write('AdHocReporting.modelBlacklist',array(
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
if (!is_array(Configure::read('AdHocReporting.globalFieldBlacklist'))) {
	Configure::write('AdHocReporting.globalFieldBlacklist', array(
		'id',
		'password',
		'secret',
		'private'
	));
}

if (!is_array(Configure::read('AdHocReporting.modelFieldBlacklist'))) {
	Configure::write('AdHocReporting.modelFieldBlacklist',array(
		'User' => array(
			'api_key',		
			'field1'=>'field1',
			'field2'=>'field2',
			'field3'=>'field3',
		)
	));
}


/**
 *
 * The Explicit list is a list defining exactly what models and fields are allowed.
 * if this is anything other than null, then it does what a blacklist and whitelist 
 * do when combined together. If something is on this list, it's allowed. If it's not
 * on this list, then it's not allowed. 
 *
 * The whitelists and blacklists still work and are still applied. But you could just
 * use this explicit list instead of both of them.
 * The downside of an explicit list is that the reports aren't going to be responsive to
 * changes in the models. If that's OK, then the explicit list is a good thing.
 * 
 */
if (!is_array(Configure::read('AdHocReporting.modelFieldExplicitList'))) {
	Configure::write('AdHocReporting.modelFieldExplicitList', array(
		'Users' => array(
			'first_name',
			'last_name',
			'email',
			'twitter_handle'
		)
	));
}
 

?>