<?php

class CustomReportingAppController extends AppController {
	
	// TODO: Remove the auth permission - this is just to make sure I can Dev on this.
	function beforeFilter() {
		$this->Auth->allowedActions = array('*');
		return parent::beforeFilter();		
	}
}

?>