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

$AdHocReportingdisplayForeignKeys = Configure::read('AdHocReporting.displayForeignKeys'); // Configure::read returns null if the key doesn't exist. We don't need to test for isset() below.
if (!is_bool($AdHocReportingdisplayForeignKeys)) { // if it's true, we leave it as true. if it's false, we do nothing. if it's anything else, including null... we make it false.
	Configure::write('AdHocReporting.displayForeignKeys', false);
}

/*
	
	Regarding Whitelists and Blacklists and Explicit Lists
	
	The Schema will return an array of models and fields that may be included in your reports.
	But you probably don't want to have all those fields showing. There are probably `id` columns
	that are meaningless to the user, Models that contain secure information, or fields that contain
	private data.
	
	Once the schema is received from Cake, that list is *pruned* to remove models and fields that are not 
	appropriate for reporting. This plugin provides five such tools for removing inappropriate models and 
	fields from your reports.
	
	You can only apply whitelists and blacklists to fields that are in the Schema.
	
	These five lists do not ADD to the list. They can only REMOVE from it.
	If a list is null, then its effect is moot & it does nothing.
	
	AdHocReporting.modelWhitelist
		- if not null, this list will include only the models that are in the list. 
		- It acts like an exclusive list. If a model is not on this list, it will not be allowed.

	AdHocReporting.modelBlacklist
		- if not null, this list still remove any models that appear in the list.
		- this is a good place to blacklist models that are not appropriate for viewing

	AdHocReporting.globalFieldBlacklist
		- if not null, this list filters out fields by name.
		- here, you might filter out fields like "id", "password", "created_by" and other common field names

	AdHocReporting.modelFieldBlacklist
		- if not null, this list uses a key-value nested array structure to define fields within 
		  specific models that should be removed.

	AdHocReporting.modelFieldExplicitList
		- the explicit list, if you choose to use it, is an explicit list of models and fields in
		  a nested array structure. If something is not on the list, it's omitted. The other black- and 
		  white- lists are still applied.
 	
	ALL of these lists are employed to prune your model & field collection. If any of them are not null, then 
	they apply their inclusion or exclusion to prevent the consumption of inappropriate data.
	
*/


/**
 * A Whitelist of Models to report on
 *
 * if this list is false, then everything is included.
 * if this list is an array, then only the models in the array are included. It becomes
 * an exclusive whitelist. 
 * The blacklist is still applied to whatever is allowed here.
 *
 * Members of this list may have a child array consisting of model names.
 * The child models should be ones that are associated with the parent models via a foreign key,
 * with either a "hasOne" or "belongsTo" relationship. If no association is defined in the 
 * schema, then this plugin will dynamically bind the models with a "belongsTo" relationship.
 * 
 * If a member of this list has no child array, then the default behaviour is to include all
 * models that are associated in the schema.
 */
if (!is_array(Configure::read('AdHocReporting.modelWhitelist'))) {
	Configure::write('AdHocReporting.modelWhitelist',	array(
		'Users',
		'Categories',
		'Hobbies' => array(
			'Arts',
			'Crafts',
			'Collections'
		),
		'Widgets',
		'Foo',
		'Bar'
	));
}

/**
 * A Blacklist of Models that should not be made available
 *
 * this list is an array of models that should not be made available.
 * Note, this is only for the root model. Any models included with a BelongsTo or
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
 * This blacklist of fields will be removed from ALL models that are included
 * in reports. If you have any model-specific fields to exclude, you
 * can add them to the modelFieldBlacklist setting below.
 */
if (!is_array(Configure::read('AdHocReporting.globalFieldBlacklist'))) {
	Configure::write('AdHocReporting.globalFieldBlacklist', array(
		'id',
		'password',
		'secret',
		'private'
	));
}
/**
 * A Blacklist of Specific Model-Field combinations
 * This blacklist will be applied to remove fields from specific models.
 */
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
 * Model-Field Explicit List
 *
 * If this list is not null, then it becomes an exclusive whitelist.
 * If you'd rather use the whitelists and blacklists by themselves, then make this NULL.
 * 
 * Anything on this list is allowed to be included in a report, and anything 
 * omitted from this list is not allowed to be included in a report.
 * 
 * The blacklists defined above are still applied too.
 *
 * @todo - the explicit list currently does not support wildcard notation to "include all"
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