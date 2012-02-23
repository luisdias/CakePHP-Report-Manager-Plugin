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
            $models = array_flip($models);            
            
            if ( isset($modelIgnoreList) && is_array($modelIgnoreList)) {
                foreach ($modelIgnoreList as $model) {
                    if (isset($models[$model]));
                        unset($models[$model]);
                }                
            }
            
            foreach ($models as $key => $value) {             
                $models[$key] = $key;
            }
            
            $this->set('models',$models);
        } else {
            $this->redirect(array('action'=>'wizard',$this->data['ReportManager']['model']));
        }
    }

    public function wizard($modelClass) {      
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
        } else {
            Configure::write('debug',0);
            $this->loadModel($modelClass);
            $associatedModels = $this->{$modelClass}->getAssociated();
            $fieldsList = array();
            $conditions = array();
            $fieldsPosition = array();
            $conditionsList = array();
            $fieldsType = array();
            foreach ($this->data  as $model => $fields) {
                if ($model != 'OrderBy1' && $model != 'OrderBy2') {
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
                            } // is array parameters
                        } // foreach field => parameters
                        if (count($conditionsList)>0) {
                            $conditions[$this->data['Report']['Logical']] = $conditionsList;
                        }
                    } // is array fields
                } // ! OrderBy
            } // foreach model => fields
            asort($fieldsPosition);
            $fieldsList = array_keys($fieldsPosition);
            $order = array();
            if ( isset($this->data['Report']['OrderBy1']) )
                $order[] = $this->data['Report']['OrderBy1'] . ' ' . $this->data['Report']['OrderDirection'];
            if ( isset($this->data['Report']['OrderBy2']) )
                $order[] = $this->data['Report']['OrderBy2'] . ' ' . $this->data['Report']['OrderDirection'];
            
            $reportData = $this->{$modelClass}->find('all',array('recursive'=>0,'fields'=>$fieldsList,'order'=>$order,'conditions'=>$conditions));

            $this->set('fieldsType',$fieldsType);
            $this->set('fieldList',$fieldsList);
            $this->set('reportData',$reportData);
            $this->set('reportName',$this->data['Report']['ReportName']);
            $this->set('reportStyle',$this->data['Report']['Style']);

            $this->layout = 'report';

            $this->render('report_display');
        }
    }
}