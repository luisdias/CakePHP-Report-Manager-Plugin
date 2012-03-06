<?php
/*
Copyright (c) 2012 Luis E. S. Dias - www.smartbyte.com.br

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
App::uses('AppController', 'Controller');

class ReportsController extends AppController {
    
    public $uses = null;
    public $helpers = array('Number');
    public function index() {
        if (empty($this->data)) {        
            $modelIgnoreList = Configure::read('ReportManager.modelIgnoreList'); 
            
            $models = App::objects('Model');
            $models = array_combine($models,$models);            
            
            if ( isset($modelIgnoreList) && is_array($modelIgnoreList)) {
                foreach ($modelIgnoreList as $model) {
                    if (isset($models[$model]));
                        unset($models[$model]);
                }                
            }
            
            $this->set('models',$models);
        } else {
            $this->redirect(array('action'=>'wizard',$this->data['ReportManager']['model'],$this->data['ReportManager']['one_to_many_option']));
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
        $maxWidth = 50;
        $tableColumnWidth = array();
        foreach ($fieldsLength as $field => $length): 
            if ( $length != '') {
                if ( $length < $maxWidth ) 
                    $width = $length * 9;
                else
                    $width = $maxWidth * 9;
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


    public function wizard($modelClass = null,$oneToManyOption = null) {
        if (is_null($modelClass)) {
            $this->Session->setFlash(__('Please select a model'));
            $this->redirect(array('action'=>'index'));
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
                $this->loadModel($key);
                $associatedModelsSchema[$key] = $this->{$key}->schema();
                
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
        } else {
            Configure::write('debug',0);
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
                                if ( isset($parameters['Add']) ) {
                                    $fieldsPosition[$model.'.'.$field] = ( $parameters['Position']!='' ? $parameters['Position'] : 0 );
                                    $fieldsType[$model.'.'.$field] = $parameters['Type'];
                                    $fieldsLength[$model.'.'.$field] = $parameters['Length'];
                                }
                                $criteria = '';                                    
                                if ($parameters['Example'] != '' && $parameters['Filter']!='null' ) {
                                    if ( isset($parameters['Not']) ) {
                                        $criteria = ' !';
                                        $criteria .= $parameters['Filter'];
                                    } else {
                                        if ($parameters['Filter']!='=') 
                                            $criteria .= ' '.$parameters['Filter'];
                                    }

                                    if ($parameters['Filter']=='LIKE')
                                        $example = '%'. mysql_real_escape_string($parameters['Example']) . '%';
                                    else
                                        $example = mysql_real_escape_string($parameters['Example']);

                                    $conditionsList[$model.'.'.$field.$criteria] = $example;
                                }
                                if ( $parameters['Filter']=='null' ) {
                                    if ( isset($parameters['Not']) )
                                        $criteria = ' !=';                                        
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
            } else {
                $oneToManyTableColumnWidth = $this->getTableColumnWidth($oneToManyFieldsLength,$oneToManyFieldsType);
                $oneToManyTableWidth = $this->getTableWidth($oneToManyTableColumnWidth);                
                asort($oneToManyFieldsPosition);
                $oneToManyFieldsList = array_keys($oneToManyFieldsPosition);
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

            if ($oneToManyOption == '')
                $this->render('report_display');
            else {
                $this->set('oneToManyOption',$oneToManyOption);
                $this->set('oneToManyFieldsList',$oneToManyFieldsList);
                $this->set('oneToManyFieldsType',$oneToManyFieldsType);
                $this->set('oneToManyTableColumnWidth',$oneToManyTableColumnWidth);
                $this->set('oneToManyTableWidth',$oneToManyTableWidth);
                $this->set('showNoRelated',$this->data['Report']['ShowNoRelated']);
                $this->render('report_display_one_to_many');
            }
                
        }
    }
}