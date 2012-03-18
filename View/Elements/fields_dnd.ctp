    <!-- Copyright (c) 2012 Luis E. S. Dias - www.smartbyte.com.br -->
	<?php
	foreach ($modelSchema as $field => $attributes): 
            echo '<tr>';
            echo '<td>';
            if (isset($this->data[$modelClass][$field]['Add']))
                $modelFieldAdd = $this->data[$modelClass][$field]['Add'];
            else
                $modelFieldAdd = true;
            echo $this->Form->checkbox($modelClass.'.'.$field.'.'.'Add',array('hiddenField' => true,'checked'=>$modelFieldAdd));
            echo '</td>';
            echo '<td>';
            echo '<b><span class="checkAll">'.$modelClass.'</span></b>.'.$field;
            echo '</td>';
            echo '<td>';
            echo $this->Form->input($modelClass.'.'.$field.'.'.'Position',array('label'=>'','size'=>'4','maxlength'=>'4','class'=>'position'));
            echo $this->Form->input($modelClass.'.'.$field.'.'.'Type',array('type'=>'hidden','value'=>$attributes['type']));
            echo $this->Form->input($modelClass.'.'.$field.'.'.'Length',array('type'=>'hidden','value'=>$attributes['length']));
            echo '</td>';          
            echo '</tr>';
        endforeach;
        ?>