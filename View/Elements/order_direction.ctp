    <!-- Copyright (c) 2012 Luis E. S. Dias - www.smartbyte.com.br -->
    <fieldset>
        <legend><?php echo __('Order Direction'); ?></legend>
        <table class="reportManagerOrderDirectionSelector" cellpadding="0" cellspacing="0">
	<?php
        $directionOptions = array(
            'ASC'=>'ASC',
            'DESC'=>'DESC'
            );
            echo '<tr>';         
            echo '<td>';
            echo $this->Form->input('OrderDirection',array('type'=>'select','options'=>$directionOptions,'label'=>false));            
            echo '</td>';             
            echo '</tr>';
        ?>
        </table>
    </fieldset>