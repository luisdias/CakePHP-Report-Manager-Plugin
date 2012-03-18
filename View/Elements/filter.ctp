    <!-- Copyright (c) 2012 Luis E. S. Dias - www.smartbyte.com.br -->
    <fieldset>
        <legend><?php echo $modelClass; ?></legend>
        <table class="reportManagerFilterSelector" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    
                    <td><?php echo __('Field'); ?></td>
                    <td class="reportManagerFilterNot"><?php echo __('Not'); ?></td>
                    <td class="reportManagerFilterCriteria"><?php echo __('Criteria'); ?></td>
                    <td class="reportManagerFilterExample"><?php echo __('Example'); ?></td>
                </tr>
            </thead>
	<?php
        $filterOptions = array(          
          '='   => '=',
          'LIKE' => 'LIKE',
          '>' => '>',
          '<' => '<',
          '>=' => '>=',
          '<=' => '<=',
          'null' => 'is Null'
        );
        $logicalOptions = array(
            ''=> '<None>',
            'AND'=>'AND',
            'OR'=>'OR'
            );
        $i = 1;
	foreach ($modelSchema as $field => $attributes): 
            echo '<tr>';         
            echo '<td>';
            echo $field;
            echo '</td>';
            echo '<td >';
            if (isset($this->data[$modelClass][$field]['Not']))
                $modelFieldNot = $this->data[$modelClass][$field]['Not'];
            else
                $modelFieldNot = false;            
            echo $this->Form->checkbox($modelClass.'.'.$field.'.'.'Not',array('hiddenField' => true,'checked'=>$modelFieldNot));
            echo '</td>';            
            echo '<td>';
            echo $this->Form->input($modelClass.'.'.$field.'.'.'Filter',array('type'=>'select','options'=>$filterOptions,'label'=>false));
            echo '</td>';                   
            echo '<td>';
            if ($attributes['type']=='date' || $attributes['type']=='datetime')
                $class = "datepicker";
            else
                $class = null;
            echo $this->Form->input($modelClass.'.'.$field.'.'.'Example',array('type'=>'text','label'=>false,'class'=>$class));
            echo '</td>';          
            echo '</tr>';
            $i++;
        endforeach;
        ?>
        </table>
    </fieldset>