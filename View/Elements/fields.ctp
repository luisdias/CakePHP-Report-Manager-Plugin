    <!-- Copyright (c) 2012 Luis E. S. Dias - www.smartbyte.com.br -->
    <fieldset>
        <legend><?php echo $modelClass; ?></legend>
        <table class="reportManagerFieldSelector" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td class="reportManagerFieldAdd"><?php echo __('Add'); ?></td>
                    <td><?php echo __('Field'); ?></td>
                    <td class="reportManagerFieldPosition"><?php echo __('Position'); ?></td>
                </tr>
            </thead>
	<?php
	foreach ($modelSchema as $field => $attributes): 
            echo '<tr>';
            echo '<td>';            
            echo $this->Form->checkbox($modelClass.'.'.$field.'.'.'Add',array('hiddenField' => false,'checked'=>true));
            echo '</td>';         
            echo '<td>';
            echo $field;
            echo '</td>';
            echo '<td>';
            echo $this->Form->input($modelClass.'.'.$field.'.'.'Position',array('label'=>'','size'=>'4','maxlength'=>'4','class'=>'position'));
            echo $this->Form->input($modelClass.'.'.$field.'.'.'Type',array('type'=>'hidden','value'=>$attributes['type']));
            echo $this->Form->input($modelClass.'.'.$field.'.'.'Length',array('type'=>'hidden','value'=>$attributes['length']));
            echo '</td>';          
            echo '</tr>';
        endforeach;
        ?>
        </table>
    </fieldset>