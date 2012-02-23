    <!-- Copyright (c) 2012 Luis E. S. Dias - www.smartbyte.com.br -->
    <fieldset>
        <legend><?php echo $modelClass; ?></legend>
        <table class="reportManagerOrderSelector" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td><?php echo __('Field'); ?></td>
                    <td class="reportManagerFieldOrder1"><?php echo __('Order 1'); ?></td>
                    <td class="reportManagerFieldOrder2"><?php echo __('Order 2'); ?></td>                    
                </tr>
            </thead>
	<?php
	foreach ($modelSchema as $field => $attributes): 
            echo '<tr>';
            echo '<td>';
            echo $field;
            echo '</td>';
            echo '<td>';
            echo '<input type="radio" name="data[Report][OrderBy1]" value="' .$modelClass.'.'.$field.'" id="'.$modelClass.$field.'OrderBy1'.'"/>';
            echo '</td>';          
            echo '<td>';
            echo '<input type="radio" name="data[Report][OrderBy2]" value="' .$modelClass.'.'.$field.'" id="'.$modelClass.$field.'OrderBy2'.'"/>';
            echo '</td>';                   
            echo '</tr>';
        endforeach;
        ?>
        </table>
    </fieldset>