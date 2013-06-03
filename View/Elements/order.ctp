    <!-- Copyright (c) 2012-2013 Luis E. S. Dias - www.smartbyte.com.br -->
    <fieldset>
        <legend><?php echo $modelClass; ?></legend>
        <table class="reportManagerOrderSelector" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td><?php echo __d('report_manager','Field'); ?></td>
                    <td class="reportManagerFieldOrder1"><?php echo __d('report_manager','Order 1'); ?></td>
                    <td class="reportManagerFieldOrder2"><?php echo __d('report_manager','Order 2'); ?></td>                    
                </tr>
            </thead>
	<?php
	foreach ($modelSchema as $field => $attributes): 
            echo '<tr>';
            echo '<td>';
            echo ( isset($labelFieldList[$modelClass][$field]) ? $labelFieldList[$modelClass][$field] : ( isset($labelFieldList['*'][$field]) ? $labelFieldList['*'][$field] : $field ));            
            echo '</td>';
            echo '<td>';
            echo $this->Form->input($modelClass.'.'.$field.'.'.'OrderBy1',
                    array('name'=>'data[Report][OrderBy1]',
                        'legend'=>false,
                        'label'=>'',
                        'type'=>'radio',
                        'default'=>( isset($this->data['Report']['OrderBy1']) && $this->data['Report']['OrderBy1'] == $modelClass.'.'.$field ? $modelClass.'.'.$field : ''),
                        'hiddenField' => false,
                        'options'=>array($modelClass.'.'.$field => ''))
                    );
            echo '</td>';          
            echo '<td>';
            echo $this->Form->input($modelClass.'.'.$field.'.'.'OrderBy2',
                    array('name'=>'data[Report][OrderBy2]',
                        'legend'=>false,
                        'label'=>'',
                        'type'=>'radio',
                        'default'=>( isset($this->data['Report']['OrderBy2']) && $this->data['Report']['OrderBy2'] == $modelClass.'.'.$field ? $modelClass.'.'.$field : ''),
                        'hiddenField' => false,
                        'options'=>array($modelClass.'.'.$field => ''))
                    );            
            echo '</td>';                   
            echo '</tr>';
        endforeach;
        ?>
        </table>
    </fieldset>