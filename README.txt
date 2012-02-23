Report Manager Plugin for CakePHP 2

The report manager plugin can help users to create reports based on the application's models.


Installation

1. Download the plugin from github or sourceforge

https://github.com/luisdias/CakePHP-Report-Manager-Plugin

2. Extract the zip file on the app/Plugin folder ( the plugin folder must be named ReportManager )

3. Add the following line to your bootstrap.php file ( located at app/Config folder )

CakePlugin::load('ReportManager',array('bootstrap' => true));

4. Go to the url http://mycakeapp/report_manager/reports to see the main page listing all models


Using the plugin

The wizard interface is self explanatory. 

1. On the first tab you can select the fields and their position

2. On the second tab you can define a filter

3. On the third tab you can select up to two fields to sort

4. On the last tab you can enter a name for your report and choose between 5 style options


Configuration:

Some parameters could be configured in the app/Plugin/ReportManager/Config/bootstrap.php

* Display foreign keys 

* Ignore List for global fields ( affects all models )

* Ignore list for models

* Ignore list for specific model's fields


Notes: 

It was inspired by the Report Creator Component by Gene Kelly from Nov 9th 2006.

http://bakery.cakephp.org/articles/Gkelly/2006/11/09/report-creator-component

It also uses a Jquery plugin called SmartWizard by Tech Laboratory.

http://techlaboratory.net/products.php?product=smartwizard

The Report Manager Plugin does not load the jQuery library you must load it by yourself


To do list for next releases:

* Improve the query builder.

* One to many reports. This version does not handle related models by hasMany relation

* Save and load actions


Luis E. S. Dias