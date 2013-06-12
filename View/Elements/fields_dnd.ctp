<?php
	list($class,$field) = explode(".",$fieldname);
	echo '<div class="sortable-field">';
		
		echo '<div class="left">';
			$modelFieldAdd = true;
			if (isset($fieldarray['add'])) {
				$modelFieldAdd = $fieldarray['add'];
			}
			echo $this->Form->checkbox($fieldname.'.Add',array('hiddenField' => true,'checked'=>$modelFieldAdd, 'class'=>'fieldCheckbox'));
		echo '</div>';
		
		echo '<div>';
			
			echo Inflector::humanize(Inflector::underscore($class)) .' &gt; '. Inflector::humanize($field);
			
			echo $this->Form->input($fieldname.'.Position',array('label'=>'','size'=>'4','maxlength'=>'4','class'=>'position','type'=>'hidden'));

			$currType = ( isset($fieldarray['type']) ? $fieldarray['type'] : $fieldarray['Type'] );
			echo $this->Form->input($fieldname.'.Type',array('type'=>'hidden','value'=>$currType));
			
			$currLength = ( isset($fieldarray['length']) ? $fieldarray['length'] : ( isset($fieldarray['Length']) ? $fieldarray['Length'] : 10) );			
			echo $this->Form->input($fieldname.'.Length',array('type'=>'hidden','value'=>$currLength));
			
		echo '</div>';          
	echo '</div>';
?>