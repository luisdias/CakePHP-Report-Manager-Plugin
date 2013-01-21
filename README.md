# Report Manager Plugin for CakePHP 2.x  

## The report manager plugin can help users to create reports based on the application's models.

**Changelog for version 0.4.5.1**  
* bugfix: php keyword added to saveReport method  
* bugfix: translation function correction   

**Changelog for version 0.4.5**  
* bugfix: delete saved report does not refresh the list properly  
* bugfix: array index errors on loading reports  

**Changelog for version 0.4.4**  
* enhancement: JS changes to work on non-root URLs  

**Changelog for version 0.4.3**  
* bugfix: default.js : update after renumber position  

**Changelog for version 0.4.2**  
* bugfix: order.ctp - test if OrderBy1 and OrderBy2 are set  
* bugfix: ReportsController listReports method - handle empty array  

**Changelog for version 0.4**  
* Load and Save reports  
* Export to XLS  

**Changelog for version 0.3**  
* One to many reports  
* Sortable fields by drag and drop ( step 1 )  
* Click to add field change background color ( step 1 )  
* Click in model name check/uncheck all fields ( step 1 )  
* SmartWizard validation ( step 1 )  
* Datepicker for date fields ( step 2 )  
* Checkbox to enable counter option ( step 4 )  
* Check box for one to many reports : show items with no related records ( step 4 )  
* Both jquery and jquery ui libraries are loaded from google  


## Installation  

1. Download the plugin from github or sourceforge  

http://sourceforge.net/projects/repomancakephp/  

https://github.com/luisdias/CakePHP-Report-Manager-Plugin  

2. Extract the zip file on the app/Plugin folder ( the plugin folder must be named ReportManager )  

3. Add the following line to your bootstrap.php file ( located at app/Config folder )  

CakePlugin::load('ReportManager',array('bootstrap' => true));  

4. Go to the url http://mycakeapp/report_manager/reports to see the main page listing all models  


## Using the plugin  

The wizard interface is self explanatory.  

1. On the first tab you can select the fields and their position  

2. On the second tab you can define a filter  

3. On the third tab you can select up to two fields to sort  

4. On the last tab you can enter a name for your report and choose between 5 style options  


## Configuration:  

Some parameters could be configured in the app/Plugin/ReportManager/Config/bootstrap.php  

* Display foreign keys  

* Ignore List for global fields ( affects all models )  

* Ignore list for models  

* Ignore list for specific model's fields  

* Reports directory path  


## Notes:  

It was inspired by the Report Creator Component by Gene Kelly from Nov 9th 2006.  

http://bakery.cakephp.org/articles/Gkelly/2006/11/09/report-creator-component  

It also uses a Jquery plugin called SmartWizard by Tech Laboratory.  

http://techlaboratory.net/products.php?product=smartwizard  

Since version 0.3 the Report Manager Plugin load the jQuery and jQuery UI libraries from Google  

Collaborators:  
Suman (USA)  
Santana (Brazil)  
Tamer Solieman (Egypt)  
jasonchua89  
Tony George (Singapore)  

Luis E. S. Dias  
Contact: smartbyte.systems@gmail.com