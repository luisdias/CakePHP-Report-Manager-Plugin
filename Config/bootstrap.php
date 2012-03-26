<?php
Configure::write('ReportManager.displayForeignKeys', 0);
Configure::write('ReportManager.globalFieldIgnoreList', array(
    'id',
    'created',
    'modified'
));
Configure::write('ReportManager.modelIgnoreList',array(
    'AppModel',
    'MyModel2'
));
Configure::write('ReportManager.modelFieldIgnoreList',array(
    'MyModel' => array(
        'field1'=>'field1',
        'field2'=>'field2',
        'field3'=>'field3'
    )
));
Configure::write('ReportManager.reportPath', 'tmp'.DS.'reports'.DS);
?>