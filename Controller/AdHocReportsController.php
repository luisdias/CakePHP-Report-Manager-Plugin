<?php
/**
 * Copyright (c) 2013 TribeHR Corp - http://tribehr.com
 * Copyright (c) 2012 Luis E. S. Dias - www.smartbyte.com.br
 * 
 * Licensed under The MIT License. See LICENSE file for details.
 * Redistributions of files must retain the above copyright notice.
 */

class AdHocReportsController extends AdHocReportingAppController {
	
	public $uses = array('AdHocReporting.AdHocReport');
	public $helpers = array('Number', 'Form');

	public function index() {
	
		if (empty($this->request->data)) {
	
			// Get the lists of models and saved reports, and pass them to the view
			$models = $this->_getFilteredListOfModels();
			$adHocReports = $this->AdHocReport->find('all',array(
				'fields' => array('id','title','created'),
				'recursive' => -1
			));			
			$this->set(compact('models', 'adHocReports'));

		} else {
	
			if (isset($this->request->data['AdHocReport']['model'])) {
				// TODO: validate the modelClass name - don't trust it
				$modelIndex = $this->data['AdHocReport']['model'];
				$this->redirect(array('action' => 'wizard', $modelIndex));
				return;
			}
			
			// Submitted data we couldn't handle, so simply redirect to the index.
			$this->redirect(array('action'=>'index'));
			return;
		}
	}

	public function wizard($modelClass = null, $reportData = null) {
		if (is_null($modelClass)) {
			$this->Session->setFlash(__('Please select a model or a saved report'));
			$this->redirect(array('action'=>'index'));			
			return;
		}

		if (empty($this->request->data)) {
			// get the list of fields to make available to the report
			$modelSchema = $this->_getCompleteFieldList($modelClass);
			
			// We may be loading data from a previous report
			if (!is_null($reportData)) {
				
				// I've never liked editing the request->data content, but it seems
				// to be the method suggested by the CakePHP docmentation. See:
				// http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html
				$this->request->data = $reportData;				
			}

			$this->set('modelClass',$modelClass);
			$this->set('modelSchema',$modelSchema);

			// Make sure we render the wizard view. We might have arrived here via /load
			$this->render('wizard');

		} else {
		
			// get the list of fields to make available to the report
			$modelSchema = $this->_getCompleteFieldList($modelClass);
			
			
			/*****************/
			/*     FIELDS    */
			$fieldsList = array();
			
			$fieldsPosition = array();
			$fieldsType = array();
			$fieldsLength = array();
			
			$containList = array();
			// loop through the fields, and see if any of them have the checkbox on
			foreach ($this->request->data  as $model => $fields) {
				if ( is_array($fields) ) {
					foreach ($fields  as $field => $parameters) {
						if ( is_array($parameters) ) {					  
							if ( isset($modelSchema[$model]) ) {
								if ( isset($parameters['Add']) && $parameters['Add'] == 1 ) {
									
									// If we haven't previously added it to the contain, then add it
									if ($model != $modelClass && !in_array($model, $containList)) {
										$containList[] = $model;
									}
									$fieldsPosition[$model.'.'.$field] = ( $parameters['Position'] != '' ? $parameters['Position'] : 0 );
									$fieldsType[$model.'.'.$field] = $parameters['Type'];
									$fieldsLength[$model.'.'.$field] = $parameters['Length'];
								}
							}
						} // is array parameters
					} // foreach field => parameters
				} // is array fields
			} // foreach model => fields

			// at this point, $fieldsPosition looks like ["field1"=>3,"field2"=>2,"field3"=>1"]
			asort($fieldsPosition);
			$fieldsList = array_keys($fieldsPosition);
			// now they're in order by position.
			
			
			/*****************/
			/*   FILTERING   */
			$conditions = array();
			$conditionsList = array();
			if (!empty($this->request->data['Filters'])){
				foreach($this->request->data['Filters'] as $filter){
					
					if (empty($filter['Value'])){
						continue;
					}
					
					// a filter looks like this: ['Field':'One', 'Operator':'=', 'Value':'eeeeee', 'Not':1]
					$criteria = '';
					// choose the right operator
					if (isset($filter['Not']) && $filter['Not'] == 1){
						// here we define all the opposites of the operator
						switch ($filter['Operator']) {
							case '=':
								$criteria = '!=';
								break;
							case '>':
								$criteria = '<=';
								break;
							case '<':
								$criteria = '>=';
								break;
							default:
								$criteria = '!=';
								break;
						}
					} else {
						// it's not a not. it is an is. tautologies are.
						switch ($filter['Operator']) {
							case '=':
								$criteria = '';
								break;
							case '>':
								$criteria = '>';
								break;
							case '<':
								$criteria = '<';
								break;
							default:
								$criteria = '';
								break;
						}
					}
					if ($criteria == ''){
						$conditionsList[$filter['Field']] = $filter['Value'];
					} else {
						$conditionsList[$filter['Field']." ".$criteria] = $filter['Value'];
					}
				}
			}
			if (count($conditionsList)>0) {
				// Constrain the values to be either OR or AND
				$logical = (isset($this->data['AdHocReport']['FilterLogic']) && $this->data['AdHocReport']['FilterLogic'] == "OR") ? "OR" : "AND";
				// for eaxmple, $conditions will be like array("OR" => array("Users.name = 'ian'","Users.fieldname < 5") )
				$conditions[$logical] = $conditionsList;
			}
			
			
			/*****************/
			/*  SORT ORDER   */
			$order = array();
			
			if ( isset($this->data['Sort']['primary']) ) {
				$order[] = $this->data['Sort']['primary']['Field'] . ' ' . $this->data['Sort']['primary']['Direction'];
			}
			if ( isset($this->data['Sort']['secondary']) ) {
				$order[] = $this->data['Sort']['secondary']['Field'] . ' ' . $this->data['Sort']['secondary']['Direction'];
			}
			
			
			/*****************/
			/*    OTHER     */
			
			$tableColumnWidth = $this->_getTableColumnWidth($fieldsLength,$fieldsType);
			$tableWidth = $this->_getTableWidth($tableColumnWidth);
			$recursive = 1;

			// here's where the real action takes place.
			// everything prior to this line was just to set up these parameter arrays
			
			// let's build this separately so we can debug and look at it
			$params = array(
				'recursive' => $recursive,
				'fields' => $fieldsList,
				'order' => $order,
				'conditions' => $conditions,
				'contain' => $containList,
			);
			
			$reportData = $this->{$modelClass}->find('all', $params);
			

			$this->layout = 'report';
						
			$this->set('tableColumnWidth',$tableColumnWidth);
			$this->set('tableWidth',$tableWidth);
			
			$this->set('fieldList',$fieldsList);
			$this->set('fieldsType',$fieldsType);
			$this->set('fieldsLength',$fieldsLength);
			$this->set('reportData',$reportData);
			$this->set('reportName',$this->data['AdHocReport']['Title']);
			$this->set('reportStyle',$this->data['AdHocReport']['Style']);
			$this->set('showRecordCounter',$this->data['AdHocReport']['ShowRecordCounter']);

			if ( $this->data['AdHocReport']['Output'] == 'html') {
				$this->render('report_display');
			} else { // Excel file
				$this->layout = null;
				$this->_export2Xls(
					$reportData, 
					$fieldsList, 
					$fieldsType, 
					$showNoRelated 
				);
			}
		}
	}

	/**
	 * Load up the data from a stored report, and push it into
	 * the wizard to manage.
	 */
	public function view($id = null) {
		if (is_null($id)) {
			$this->Session->setFlash(__('Please select a report to view'));
			$this->redirect(array('action'=>'index'));
			return;			
		}
		
		$adHocReport = $this->AdHocReport->find('first', array('conditions' => array('id' => $id)));
		if (!$adHocReport || empty($adHocReport)) {
			$this->Session->setFlash(__('Sorry, we could not load that report'));
			$this->redirect(array('action'=>'index'));
			return;
		} else {
			$reportData = unserialize($adHocReport['AdHocReport']['options']);
			if ($reportData === false) {
				$this->Session->setFlash(__('Sorry, but that report appears to be corrupted.'));
				$this->redirect(array('action'=>'index'));
				return;
			}
			
			$reportData['AdHocReport'] = array_merge($reportData['AdHocReport'], $adHocReport['AdHocReport']);
			$this->request->data = $reportData;				
			
			return($this->wizard($reportData['AdHocReport']['modelClass']));
		}
	}
	
	public function add() {
		if (empty($this->request->data)) {
			$this->Session->setFlash(__('Please configure a report to add'));
			$this->redirect(array('action'=>'index'));
			return;
		}
		
		// Format the option data, which we will serialize
		$reportOptions = $this->request->data;
		if (isset($reportOptions['_Token'])) {
			unset($reportOptions['_Token']);
		}
		
		// Set a title, giving it a default if there isn't one already
		$reportTitle = empty($this->request->data['AdHocReport']['Title']) ? 'New Report' : trim($this->request->data['AdHocReport']['Title']);
		
		$data = array('AdHocReport' => array(
			'title' => $reportTitle,
			'options' => serialize($reportOptions),
		));
		
		$this->AdHocReport->create();
		if ($this->AdHocReport->save($data)) {
			$this->redirect(array('action'=>'index'));
			return;
		} else {
			$this->Session->setFlash(__('Sorry, but we could not save your report'));
			return $this->wizard($this->request->data['AdHocReport']['modelClass'], $this->request->data);
		}
	}
	
	public function edit($id = null) {
		if (is_null($id)) {
			$this->Session->setFlash(__('Please select a report to load'));
			$this->redirect(array('action'=>'index'));
			return;			
		}
		
		if (empty($this->request->data)) {
			
			$adHocReport = $this->AdHocReport->find('first', array('conditions' => array('id' => $id)));
			if (!$adHocReport || empty($adHocReport)) {
				$this->Session->setFlash(__('Sorry, we could not load that report'));
				$this->redirect(array('action'=>'index'));
				return;
			} else {
				$reportData = unserialize($adHocReport['AdHocReport']['options']);
				if ($reportData === false) {
					$this->Session->setFlash(__('Sorry, but that report appears to be corrupted.'));
					$this->redirect(array('action'=>'index'));
					return;
				}
				$reportData['AdHocReport'] = array_merge($reportData['AdHocReport'], $adHocReport['AdHocReport']);
				return($this->wizard($reportData['AdHocReport']['modelClass'], $reportData));
			}
			
		} else {
			
			// Format the option data, which we will serialize
			$reportOptions = $this->request->data;
			if (isset($reportOptions['_Token'])) {
				unset($reportOptions['_Token']);
			}				

			$data = array('AdHocReport' => array(
				'id' => $id,
				'title' => $this->request->data['AdHocReport']['Title'],
				'options' => serialize($reportOptions),
			));
		
			if ($this->AdHocReport->save($data)) {
				$this->redirect(array('action'=>'index'));
				return;
			} else {
				$this->Session->setFlash(__('Sorry, but we could not save your report'));
				return $this->wizard($this->request->data['AdHocReport']['modelClass'], $this->request->data);
			}
		}
	}

	function delete($id = null) {
		if (is_null($id)) {
			$this->Session->setFlash(__('Invalid Ad-hoc Report'));
			$this->redirect(array('action'=>'index'));
			return;
		}
		
		if (!$this->AdHocReport->delete($id)) {
			$this->Session->setFlash(__('Delete failed. Please try again.'));
		}
		$this->redirect(array('action'=>'index'));		
	}
	
	function duplicate($id = null) {
		if (is_null($id)) {
			$this->Session->setFlash(__('Invalid Ad-hoc Report'));
			$this->redirect(array('action'=>'index'));
			return;
		}
		
		$adHocReport = $this->AdHocReport->find('first', array('conditions' => array('id' => $id)));
		
		// Clear out the ID, update the title, and save a copy
		unset($adHocReport['AdHocReport']['id']);
		$adHocReport['AdHocReport']['title'] = 'Copy of ' . $adHocReport['AdHocReport']['title'];
		
		// Also update the title that's stored in the form data
		$adHocReportOptions = unserialize($adHocReport['AdHocReport']['options']);
		$adHocReportOptions['AdHocReport']['Title'] = $adHocReport['AdHocReport']['title'] ;
		$adHocReport['AdHocReport']['options'] = serialize($adHocReportOptions);
		
		$this->AdHocReport->create();
		if (!$this->AdHocReport->save($adHocReport)) {
			$this->Session->setFlash(__('Sorry, but we could not save a copy of this report'));			
		}
		$this->redirect(array('action'=>'index'));		
				
	}
	/**
	 * Get a list of all the Models we can report on, properly
	 * respecting the Whitelist and Blacklist configurations
	 * set in the bootstrap file. Only include the top-level models
	 * not the associated models.
	 *
	 * @return array Listing of Model Names
	 */
	function _getFilteredListOfModels() {
		
		// If we have a whitelist then we will use that. If there is no whitelist,
		// then we will start with the complete list of models in the application.
		if (Configure::read('AdHocReporting.modelWhitelist') == false) {
			$models = App::objects('Model');
		} else {
			$models = Configure::read('AdHocReporting.modelWhitelist');
			// Note, some of the whitelist entries might not be string values,
			// but instead array values of whitelisted associated models. In these
			// cases, the actual model name is the *index* not the *value*. Let's
			// get rid of the 2nd level arrays, and simply include the model name.
			foreach ($models as $index => $value) {
				if (!is_numeric($index)) {
					unset($models[$index]);
					$models[] = $index;
				}
			}
		}
		
		// remove any models that are not in the explicit list.
		$explicitList = Configure::read('AdHocReporting.modelFieldExplicitList');
		if (!empty($explicitList)){ // only do this if the list is defined.
			foreach ($models as $index => $model) {
				if (!isset($explicitList[$model])) {
					unset($models[$index]);
				}
			}				 
		}
		
		
		// Now remove any models from the list that also exist on the blacklist
		$modelBlacklist = Configure::read('AdHocReporting.modelBlacklist');
		if ($modelBlacklist !== false) {
			foreach ($models as $index => $model) {
				if (in_array($model, $modelBlacklist)) {
					unset($models[$index]);
				}
			}				 
		}

		// Let's alphabetize the list for consistency, then return
		// an array with the indexes and the values the model names.
		// TODO: Replace the values with more human-friendly values
		sort($models);
		
		return array_combine($models, $models);
	}
	
	/**
	 * Get a complete list of all the fields that we can report on
	 * or filter by.
	 * 
	 * @return array Listing of all fields that are available
	 *				  array (
	 *					'PrimaryModel' => array(
	 *						'field1' => array (schema info...),
	 *						'field2' => array (schema info...),
	 *						 ...
	 *					 ),
	 *					'AssociatedModel1' => array(
	 *						'field1' => array (schema info...),
	 *						'field2' => array (schema info...),
	 *						 ...
	 *					 )
	 *					'AssociatedModel2' => array(
	 *						'field1' => array (schema info...),
	 *						'field2' => array (schema info...),
	 *						 ...
	 *					 )
	 *				  )
	 */	
	function _getCompleteFieldList($baseModelClass) {
		$modelWhitelist = Configure::read('AdHocReporting.modelWhitelist');
		$modelBlacklist = Configure::read('AdHocReporting.modelBlacklist');
		$modelFieldExplicitList = Configure::read('AdHocReporting.modelFieldExplicitList');
		
		// Start with the base model
		$completeSchema = array($baseModelClass => $this->_getFilteredListOfModelFields($baseModelClass));
		// Add any associated models.
		$associatedModels = $this->$baseModelClass->getAssociated();
		
		/**
		* For each child-model in the whitelist, we check if there is an association in the schema.
		* If there is no association in the schema, we do a dynamic binding. Provided the foreign keys 
		* exist in the database, this will enable us to join models into a single report using a 
		* "belongsTo" relationship.
		* 
		* Inclusion into a report like this is only effective if the relationship is "belongsTo" or "hasOne".
		* As you can see a little farther below, it's only those two relationships that are allowed to be 
		* included in a report anyhow. For our dynamic bindings, we try a "belongsTo" relationship.
		* 
		*/ 
		
		// dynamic bindings, based on the structure defined AdHocReporting.modelWhitelist
		if (isset($modelWhitelist[$baseModelClass])) {
			foreach ($modelWhitelist[$baseModelClass] as $anotherModel) {
				// if the model is not bound, then bind it!
				if (!isset( $associatedModels[$anotherModel] )){
					
					$this->$baseModelClass->bindModel(
						array( 'belongsTo' => array(
								$anotherModel => array(
									'className' => $anotherModel
								)
							) 
						)
					);
					
				}
				
			}
		}
		$associatedModels = $this->$baseModelClass->getAssociated();
		
		foreach ($associatedModels as $key => $value) {
			// Only consider an associated model if it is a "HasOne" or "BelongsTo"
			// Releationship. For HasMany or HasAndBelongsToMany, you should be using
			// the association (or the relationship) model as the key centrepiece.
			if ($value == 'belongsTo' || $value == 'hasOne') {
				
				// Compare these models to the list of allowed models in
				// the whitelists and blacklists.
				$allowThisAssociatedModel = true;

				if (is_array($modelFieldExplicitList)){
					if (!isset($modelFieldExplicitList[$key])){
						$allowThisAssociatedModel = false;
					}
				}
				if (is_array($modelBlacklist)) {
					if (in_array($key, $modelBlacklist)){
						$allowThisAssociatedModel = false;
					}
				} 

				if (isset($modelWhitelist[$baseModelClass]) && is_array($modelWhitelist[$baseModelClass])){
					if (!in_array($key, $modelWhitelist[$baseModelClass])){
						$allowThisAssociatedModel = false;
					}
				}
				
				if (!$allowThisAssociatedModel){
					unset($associatedModels[$key]);
				} else {
					$associatedModelClassName = $this->{$baseModelClass}->{$value}[$key]['className'];
					$completeSchema[$key] = $this->_getFilteredListOfModelFields($associatedModelClassName);				
				}
			}
		}
				
		return $completeSchema;
	}
	
	/**
	 * Get a list of the fields we can report on for this model, properly
	 * respecting the global and model-specific blacklists defined in the
	 * configuration.
	 */
	function _getFilteredListOfModelFields($modelClass) {
		
		$displayForeignKeys = Configure::read('AdHocReporting.displayForeignKeys');
		$globalFieldBlacklist = Configure::read('AdHocReporting.globalFieldBlacklist');
		$modelFieldBlacklist = Configure::read('AdHocReporting.modelFieldBlacklist');
		$explicitList = Configure::read('AdHocReporting.modelFieldExplicitList');
		
		// if the model is explicitly blocked, there's no need to look up the fields in it
		if (is_array($explicitList)) {
			if (!isset($explicitList[$modelClass])) {
				return null;
			}
		}
		
		$this->loadModel($modelClass); // @todo: handle if the modelClass is not a real class 
		$modelSchema = $this->{$modelClass}->schema();
		
		if (is_array($explicitList)) {
			foreach($modelSchema as $field => $value) {
				if (!in_array($field, $explicitList[$modelClass]) ) {
					unset($modelSchema[$field]);
				}
			}
		}
		
		if (is_array($globalFieldBlacklist)) {
			foreach ($globalFieldBlacklist as $field) {
				unset($modelSchema[$field]);
			}				 
		}

		if (isset($modelFieldBlacklist[$modelClass])) {
			foreach ($modelFieldBlacklist[$modelClass] as $field) {
				unset($modelSchema[$field]);
			}				 			
		}
		
		if (isset($displayForeignKeys) && $displayForeignKeys == false) { 
			foreach($modelSchema as $field => $value) {
				if ( substr($field,-3) == '_id' ) {
					unset($modelSchema[$field]);
				}
			}
		}
		return $modelSchema;
	}

	// calculate the html table columns width
	public function _getTableColumnWidth($fieldsLength=array(),$fieldsType=array()) {
		$minWidth = 4;
		$maxWidth = 25;
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
	public function _getTableWidth($tableColumnWidth = array()) {
		$tableWidth = array_sum($tableColumnWidth);
		return $tableWidth;
	}

	public function _export2Xls(&$reportData = array(),&$fieldsList=array(), &$fieldsType=array(), &$showNoRelated = false ) {
		App::import('Vendor', 'AdHocReporting.Excel');
		$xls = new Excel();		 
		$xls->buildXls($reportData, $fieldsList, $fieldsType, $showNoRelated);
	}


}
