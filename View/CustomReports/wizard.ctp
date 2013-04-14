<?php
/**
 * Copyright (c) 2013 TribeHR Corp - http://tribehr.com
 * Copyright (c) 2012 Luis E. S. Dias - www.smartbyte.com.br
 * 
 * Licensed under The MIT License. See LICENSE file for details.
 * Redistributions of files must retain the above copyright notice.
 */
?>
<?php

echo $this->Html->css('/CustomReporting/css/report_manager.css');
echo $this->Html->css('/CustomReporting/css/smart_wizard.css');
echo $this->Html->script(array('https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'));
echo $this->Html->script(array('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js'));
echo $this->Html->script(array('/CustomReporting/js/jquery.smartWizard-2.0.js','/CustomReporting/js/default.js'));
?>

<div class="reportManager form">
	<h2><?php echo __('Custom Report'); ?></h2>
	<?php echo $this->Form->create('CustomReport',array('url' => array('action' => 'wizard', $modelClass), 'target'=>'_blank', 'id' => 'ReportWizardForm', $modelClass)); ?>
	<?php echo $this->Form->input('CustomReport.modelClass',array('type'=>'hidden','value'=>$modelClass)); ?>
	<?php echo $this->Form->input('CustomReport.Title',array('size'=>'80','label' => 'Title', 'maxlength'=>'100')); ?>
	<?php echo $this->Form->input('CustomReport.id',array('type' => 'hidden')); ?>
	<?php echo $this->Form->submit(__('Save'), array('id'=>'CustomReportSave')); ?>
	<div id="wizard" class="swMain">
	  <ul>
	    <li><a href="#step-1">
	          <label class="stepNumber">1</label>
	          <span class="stepDesc">
	             Content<br />
	             <small>Select Columns</small>
	          </span>
	      </a></li>
	    <li><a href="#step-2">
	          <label class="stepNumber">2</label>
	          <span class="stepDesc">
	             Filters<br />
	             <small>Filter Results</small>
	          </span>
	      </a></li>
	    <li><a href="#step-3">
	          <label class="stepNumber">3</label>
	          <span class="stepDesc">
	             Sorting<br />
	             <small>Sort by Column</small>
	          </span>                   
	       </a></li>
	    <li><a href="#step-4">
	          <label class="stepNumber">4</label>
	          <span class="stepDesc">
	             Style<br />
	             <small>Output Options</small>
	          </span>                   
	       </a></li>       
	  </ul>

	  <div id="step-1">   
	      <h2 class="StepTitle">Step 1 Fields</h2>
	        <div class="reportManager index">

	        <?php

	        echo $this->Element('fields_dnd_table_header',array(
	            'plugin '=> 'CustomReporting',
	            'title' => __('Custom Reporting'),
	            'sortableClass' => 'sortable1'));        

			foreach($modelSchema as $model => $schema) {
		        echo $this->Element('fields_dnd',array(
		            'plugin' => 'ReportManager',
		            'modelClass' => $model,
		            'modelSchema' => $schema));
			}
        
	        echo $this->Element('fields_dnd_table_close',array('plugin'=>'CustomReporting'));

	        ?>

	        </div>
	  </div>
	  <div id="step-2">
	      <h2 class="StepTitle">Step 2 Filter</h2> 
	        <?php      
	        echo $this->Element('logical_operator');
			foreach($modelSchema as $model => $schema) {
	        	echo $this->Element('filter',array('plugin'=>'CustomReporting','modelClass' => $model, 'modelSchema' => $schema));
			}
	        ?> 
	  </div>                      
	  <div id="step-3">
	      <h2 class="StepTitle">Step 3 Sort</h2>   
	        <?php
	        echo $this->Element('order_direction');
			foreach($modelSchema as $model => $schema) {
	        	echo $this->Element('order',array('plugin'=>'CustomReporting','modelClass'=>$model,'modelSchema'=>$schema));
	        }
	        ?> 
	  </div>
	  <div id="step-4">
	      <h2 class="StepTitle">Step 4 Style</h2>   
	        <?php
	        	echo $this->Element('report_style',array('plugin'=>'ReportManager'));
	        ?> 
	  </div>    
	</div>
	<?php echo $this->Form->end() ;?>
</div>