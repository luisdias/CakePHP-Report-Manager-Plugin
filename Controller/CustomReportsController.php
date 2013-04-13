<?php
/**
 * Copyright (c) 2013 TribeHR Corp - http://tribehr.com
 * Copyright (c) 2012 Luis E. S. Dias - www.smartbyte.com.br
 * 
 * Licensed under The MIT License. See LICENSE file for details.
 * Redistributions of files must retain the above copyright notice.
 */

class CustomReportsController extends CustomReportingAppController {
    
    public $uses = array('CustomReporting.CustomReport');
    public $helpers = array('Number', 'Form');

    public $path = null;
    
    public function index() {
	
        if (empty($this->data)) {
	
			// Get the lists of models and saved reports, and pass them to the view
			$models = $this->_getFilteredListOfModels();
			$customReports = $this->CustomReport->find('list');			
            $this->set(compact('models', 'customReports'));

        } else {
            if (isset($this->data['new'])) {
                $reportButton = 'new';
                $modelClass = $this->data['ReportManager']['model'];
                $oneToManyOption = $this->data['ReportManager']['one_to_many_option'];
                $this->redirect(array('action'=>'wizard',$reportButton, $modelClass, $oneToManyOption));
            }
                
            if (isset($this->data['load'])) {
                $reportButton = 'load';
                $fileName = $this->data['ReportManager']['saved_report_option'];
                $this->redirect(array('action'=>'wizard',$reportButton, urlencode($fileName)));                
            }
                
            $this->redirect(array('action'=>'index'));
        }
    }
    
    public function ajaxGetOneToManyOptions() {
        if ($this->request->is('ajax')) {
            Configure::write('debug',0);
            $this->autoRender = false;
            $this->layout = null;

            $modelClass = $this->request->data['model'];
            $this->loadModel($modelClass);
            $associatedModels = $this->{$modelClass}->getAssociated('hasMany');
            $associatedModels = array_combine($associatedModels, $associatedModels);

            $modelIgnoreList = Configure::read('ReportManager.modelIgnoreList');
            if ( isset($modelIgnoreList) && is_array($modelIgnoreList)) {
                foreach ($modelIgnoreList as $model) {
                    if (isset($associatedModels[$model]));
                        unset($associatedModels[$model]);
                }                
            }            
            
            $this->set('associatedModels',$associatedModels);
            $this->render('list_one_to_many_options');
        }
    }

    // calculate the html table columns width
    public function getTableColumnWidth($fieldsLength=array(),$fieldsType=array()) {
        $minWidth = 4;
        $maxWidth = 50;
        $tableColumnWidth = array();
        foreach ($fieldsLength as $field => $length): 
            if ( $length != '') {
                if ( $length < $maxWidth ) 
                    $width = $length * 9;
                else
                    $width = $maxWidth * 9;
                if ( $length < $minWidth ) 
                    $width = $length * 40;                
                $tableColumnWidth[$field] = $width;
            } else {
                $fieldType = $fieldsType[$field];
                switch ($fieldType) {
                    case "date":
                        $width = 120;
                        break;
                    case "float":
                        $width = 150;
                        break;                
                    default:
                        $width = 120;
                        break;
                }
                $tableColumnWidth[$field] = $width;
            }
        endforeach; 
        return $tableColumnWidth;
    }
    
    // calculate the html table width
    public function getTableWidth($tableColumnWidth = array()) {
        $tableWidth = array_sum($tableColumnWidth);
        return $tableWidth;
    }

    public function export2Xls(&$reportData = array(),&$fieldsList=array(), &$fieldsType=array(), &$oneToManyOption=null, &$oneToManyFieldsList=null, &$oneToManyFieldsType = null, &$showNoRelated = false ) {
        App::import('Vendor', 'ReportManager.Excel');
        $xls = new Excel();      
        $xls->buildXls($reportData,$fieldsList, $fieldsType, $oneToManyOption, $oneToManyFieldsList, $oneToManyFieldsType, $showNoRelated );
    }
 
    public function saveReport($modelClass = null,$oneToManyOption = null) {
        $content='<?php $reportFields=';
        $content.= var_export($this->data,1);
        $content.='; ?>'; 


        
        if ($this->data['Report']['ReportName'] != '') {
            $reportName = str_replace('.', '_', $this->data['Report']['ReportName']);
            $reportName = str_replace(' ', '_', $this->data['Report']['ReportName']);
        } else {
            $reportName = date('Ymd_His');
        }
        
        $oneToManyOption = ( $oneToManyOption == '' ? $oneToManyOption : $oneToManyOption . '.' );
        $fileName = $modelClass . '.' . $oneToManyOption . $reportName.".crp";
        $file = new File(APP.$this->path.$fileName, true, 777);
        $file->write($content,'w',true);
        $file->close();
    }

    public function loadReport($fileName) {
        require(APP.$this->path.$fileName);
        $this->data = $reportFields;
        $this->set($this->data);
    }

    public function deleteReport($fileName) {
        if ($this->request->is('ajax')) {
            Configure::write('debug',0);
            $this->autoRender = false;
            $this->layout = null;
            
            $fileName = APP.$this->path.$fileName;
            $file = new File($fileName, false, 777);
            $file->delete();
            $this->set('files',$this->listReports());
            $this->render('list_reports');
        }
    }

    public function wizard($param1 = null,$param2 = null, $param3 = null) {
        if (is_null($param1) || is_null($param2)) {
            $this->Session->setFlash(__('Please select a model or a saved report'));
            $this->redirect(array('action'=>'index'));
        }
        
        $reportAction = $param1;
        $modelClass = null;
        $oneToManyOption = null;
        $fileName = null;
        
        if ( $reportAction == "new" ) {
            $modelClass = $param2;
            $oneToManyOption = $param3;
        }
        
        if ( $reportAction == "load" ) {
            $fileName = urldecode($param2);            

            if ($fileName!='') {
                $params = explode('.', $fileName);
                if (count($params)>=3) {
                    $modelClass = $params[0];
                    if (count($params)>3) {
                        $oneToManyOption = $params[1];
                    } 
                }
            }             
        }
        
        if (empty($this->data)) {        
            $displayForeignKeys = Configure::read('ReportManager.displayForeignKeys');
            $globalFieldIgnoreList = Configure::read('ReportManager.globalFieldIgnoreList');
            $modelFieldIgnoreList = Configure::read('ReportManager.modelFieldIgnoreList');

            $this->loadModel($modelClass);
            $modelSchema = $this->{$modelClass}->schema();
            
            if (isset($globalFieldIgnoreList) && is_array($globalFieldIgnoreList)) {
                foreach ($globalFieldIgnoreList as $field) {
                    unset($modelSchema[$field]);
                }                
            }
            
            if (isset($displayForeignKeys) && !$displayForeignKeys) {               
                foreach($modelSchema as $field => $value) {
                    if ( substr($field,-3)=='_id' )
                        unset($modelSchema[$field]);
                }
            }
            
            $associatedModels = $this->{$modelClass}->getAssociated();
            $associatedModelsSchema = array();

            foreach ($associatedModels as $key => $value) {
                $className = $this->{$modelClass}->{$value}[$key]['className'];
                //$this->loadModel($key);
                $this->loadModel($className);
                //$associatedModelsSchema[$key] = $this->{$key}->schema();
                $associatedModelsSchema[$key] = $this->{$className}->schema();

                if (isset($globalFieldIgnoreList) && is_array($globalFieldIgnoreList)) {
                    foreach ($globalFieldIgnoreList as $value) {
                        unset($associatedModelsSchema[$key][$value]);
                    }
                }
                
                if (isset($displayForeignKeys) && !$displayForeignKeys) {
                    foreach($associatedModelsSchema as $model => $fields) {
                        foreach($fields as $field => $values) {
                            if ( substr($field,-3)=='_id' )
                                unset($associatedModelsSchema[$model][$field]);                            
                        }
                    }
                }
                foreach($associatedModelsSchema as $model => $fields) {
                    foreach($fields as $field => $values) {
                        if ( isset($modelFieldIgnoreList[$model][$field]) )
                            unset($associatedModelsSchema[$model][$field]);                            
                    }
                }                
            }

            $this->set('modelClass',$modelClass);
            $this->set('modelSchema',$modelSchema);
            $this->set('associatedModels',$associatedModels);
            $this->set('associatedModelsSchema',$associatedModelsSchema);
            $this->set('oneToManyOption',$oneToManyOption);
            
            if (!is_null($fileName))
                $this->loadReport($fileName);

        } else {
            $this->loadModel($modelClass);
            $associatedModels = $this->{$modelClass}->getAssociated();
            $oneToManyOption = $this->data['Report']['OneToManyOption'];
            
            $fieldsList = array();
            $fieldsPosition = array();
            $fieldsType = array();
            $fieldsLength = array();
            
            $conditions = array();
            $conditionsList = array();
            
            $oneToManyFieldsList  = array();
            $oneToManyFieldsPosition  = array();
            $oneToManyFieldsType  = array();
            $oneToManyFieldsLength = array();
            
            foreach ($this->data  as $model => $fields) {
                if ( is_array($fields) ) {
                    foreach ($fields  as $field => $parameters) {
                        if ( is_array($parameters) ) {                          
                            if ( (isset($associatedModels[$model]) && 
                                    $associatedModels[$model]!='hasMany') || 
                                    ($modelClass == $model) 
                                ) {
                                if ( $parameters['Add'] ) {
                                    $fieldsPosition[$model.'.'.$field] = ( $parameters['Position']!='' ? $parameters['Position'] : 0 );
                                    $fieldsType[$model.'.'.$field] = $parameters['Type'];
                                    $fieldsLength[$model.'.'.$field] = $parameters['Length'];
                                }
                                $criteria = '';                                    
                                if ($parameters['Example'] != '' && $parameters['Filter']!='null' ) {
                                    if ( $parameters['Not'] ) {
                                        switch ($parameters['Filter']) {
                                            case '=':
                                                $criteria .= ' !'.$parameters['Filter'];
                                                break;
                                            case 'LIKE':
                                                $criteria .= ' NOT '.$parameters['Filter'];
                                                break;
                                            case '>':
                                                $criteria .= ' <=';
                                                break;
                                            case '<':
                                                $criteria .= ' >=';
                                                break;
                                            case '>=':
                                                $criteria .= ' <';
                                                break;
                                            case '<=':
                                                $criteria .= ' >';
                                                break;
                                            case 'null':
                                                $criteria = ' !=';
                                                break;
                                        }
                                    } else {
                                        if ($parameters['Filter']!='=') 
                                            $criteria .= ' '.$parameters['Filter'];
                                    }

                                    if ($parameters['Filter']=='LIKE')
                                        //$example = '%'. mysql_real_escape_string($parameters['Example']) . '%';
                                        $example = '%'.$parameters['Example'] . '%';
                                    else
                                        //$example = mysql_real_escape_string($parameters['Example']);
                                        $example = $parameters['Example'];

                                    $conditionsList[$model.'.'.$field.$criteria] = $example;
                                }
                                if ( $parameters['Filter']=='null' ) {
                                    $conditionsList[$model.'.'.$field.$criteria] = null;                                        
                                }
                            }
                            // One to many reports
                            if ( $oneToManyOption != '') {
                                if ( isset($parameters['Add']) && $model == $oneToManyOption ) {
                                    $oneToManyFieldsPosition[$model.'.'.$field] = ( $parameters['Position']!='' ? $parameters['Position'] : 0 );
                                    $oneToManyFieldsType[$model.'.'.$field] = $parameters['Type'];
                                    $oneToManyFieldsLength[$model.'.'.$field] = $parameters['Length'];
                                }                                    
                            }

                        } // is array parameters
                    } // foreach field => parameters
                    if (count($conditionsList)>0) {
                        $conditions[$this->data['Report']['Logical']] = $conditionsList;
                    }
                } // is array fields
            } // foreach model => fields
            asort($fieldsPosition);
            $fieldsList = array_keys($fieldsPosition);
            $order = array();
            if ( isset($this->data['Report']['OrderBy1']) )
                $order[] = $this->data['Report']['OrderBy1'] . ' ' . $this->data['Report']['OrderDirection'];
            if ( isset($this->data['Report']['OrderBy2']) )
                $order[] = $this->data['Report']['OrderBy2'] . ' ' . $this->data['Report']['OrderDirection'];
            
            $tableColumnWidth = $this->getTableColumnWidth($fieldsLength,$fieldsType);
            $tableWidth = $this->getTableWidth($tableColumnWidth);
            
            if ($oneToManyOption == '') {
                $recursive = 0;
                $showNoRelated = false;
            } else {
                $oneToManyTableColumnWidth = $this->getTableColumnWidth($oneToManyFieldsLength,$oneToManyFieldsType);
                $oneToManyTableWidth = $this->getTableWidth($oneToManyTableColumnWidth);                
                asort($oneToManyFieldsPosition);
                $oneToManyFieldsList = array_keys($oneToManyFieldsPosition);
                $showNoRelated = $this->data['Report']['ShowNoRelated'];
                $recursive = 1;
            }
            
            $reportData = $this->{$modelClass}->find('all',array(
                'recursive'=>$recursive,
                'fields'=>$fieldsList,
                'order'=>$order,
                'conditions'=>$conditions
            ));
            
            $this->layout = 'report';
                        
            $this->set('tableColumnWidth',$tableColumnWidth);
            $this->set('tableWidth',$tableWidth);
            
            $this->set('fieldList',$fieldsList);
            $this->set('fieldsType',$fieldsType);
            $this->set('fieldsLength',$fieldsLength);
            $this->set('reportData',$reportData);
            $this->set('reportName',$this->data['Report']['ReportName']);
            $this->set('reportStyle',$this->data['Report']['Style']);
            $this->set('showRecordCounter',$this->data['Report']['ShowRecordCounter']);

            if ( $this->data['Report']['Output'] == 'html') {
                if ($oneToManyOption == '')
                    $this->render('report_display');
                else {
                    $this->set('oneToManyOption',$oneToManyOption);
                    $this->set('oneToManyFieldsList',$oneToManyFieldsList);
                    $this->set('oneToManyFieldsType',$oneToManyFieldsType);
                    $this->set('oneToManyTableColumnWidth',$oneToManyTableColumnWidth);
                    $this->set('oneToManyTableWidth',$oneToManyTableWidth);
                    $this->set('showNoRelated',$showNoRelated);
                    $this->render('report_display_one_to_many');
                }
            } else { // Excel file
                $this->layout = null;
                $this->export2Xls(
                        $reportData, 
                        $fieldsList, 
                        $fieldsType, 
                        $oneToManyOption, 
                        $oneToManyFieldsList, 
                        $oneToManyFieldsType, 
                        $showNoRelated );
            }
            if ($this->data['Report']['SaveReport'])
                $this->saveReport($modelClass,$oneToManyOption);
        }
    }

	/**
	 * Get a list of all the Models we can report on, properly
	 * respecting the Whitelist and Blacklist configurations
	 * set in the bootstrap file.
	 *
	 * @return array Listing of Model Names
	 */
	function _getFilteredListOfModels() {
		
		// If we have a whitelist then we will use that. If there is no whitelist,
		// then we will start with the complete list of models in the application.
		if (Configure::read('CustomReporting.modelWhitelist') == false) {
			$models = App::objects('Model');
		} else {
			$models = Configure::read('CustomReporting.modelWhitelist');
		}			
		
		// Now remove any models from the list that also exist on the blacklist
		$modelBlacklist = Configure::read('ReportManager.modelBlackist');            
        if ($modelBlacklist != false) {
            foreach ($models as $index => $model) {
				if (in_array($modelBlacklist, $model)) {
					unset($models[$index]);
				}
            }                
        }

		return $models;
	}
}