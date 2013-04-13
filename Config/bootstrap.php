<?php

Configure::write('ReportManager.displayForeignKeys', 0);
Configure::write('ReportManager.globalFieldIgnoreList', array(
    'id',
    'created',
    'modified'
));

/**
 * A Whitelist of Models to report on
 *
 * If set to an array, then we will only allow the users to run
 * reports on this list of models. If set to false, then the
 * system will present the full list of models in the system.
 * In either case, we will also remove any models included in
 * the CustomReporting.modelBlackList setting.
 *
 */
Configure::write('CustomReporting.modelWhitelist',	array(
    'User',
	'AssignmentRecord',
	'Application',
));

/**
 * A Blacklist of Models that should not be made available
 *
 * If set to an array, then we will not allow the users to run
 * reports on on any models. In this list. Note, this is only 
 * for the root model. Any models included with a BelongsTo or
 * HasMany relationship will not reference this list.
 *
 */
Configure::write('CustomReporting.modelBlacklist',array(
    'AppModel',
	'Config',
	'Setting',
));


Configure::write('ReportManager.modelFieldIgnoreList',array(
    'MyModel' => array(
        'field1'=>'field1',
        'field2'=>'field2',
        'field3'=>'field3'
    )
));

?>