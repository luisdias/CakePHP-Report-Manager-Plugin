<?php
	list($class,$field) = explode(".",$fieldname);
	echo '<li class="sortable-field ui-sortable">';
		
		echo '<div class="handle"><div class="dot"></div><div class="dot"></div><div class="dot"></div></div>';

		echo '<div class="content">';
		
			echo '<div class="checkbox">';
				$modelFieldAdd = true;
				if (isset($fieldarray['add'])) {
					$modelFieldAdd = $fieldarray['add'];
				}
				
				echo $this->Form->checkbox(
					$fieldname.'.Add',
						array(
							'hiddenField' => true,
							'checked'=>$modelFieldAdd, 
							'class'=>'fieldCheckbox',
							'data-fieldName' => $class . "." . $field
						)
					);
//			echo '</div>';
			
//			echo '<div>';
				
				echo '<label for="'.$class . Inflector::camelize($field) . 'Add'.'">';
				echo Inflector::humanize(Inflector::underscore($class)) .' &gt; '. Inflector::humanize($field);
				echo "</label>";
				
				echo $this->Form->input($fieldname.'.Position',array('label'=>'','size'=>'4','maxlength'=>'4','class'=>'position','type'=>'hidden'));
	
				$currType = ( isset($fieldarray['type']) ? $fieldarray['type'] : $fieldarray['Type'] );
				echo $this->Form->input($fieldname.'.Type',array('type'=>'hidden','value'=>$currType));
				
				$currLength = ( isset($fieldarray['length']) ? $fieldarray['length'] : ( isset($fieldarray['Length']) ? $fieldarray['Length'] : 10) );			
				echo $this->Form->input($fieldname.'.Length',array('type'=>'hidden','value'=>$currLength));
			
			echo '</div>';
		echo '</div>';
	echo '</li>';
?>