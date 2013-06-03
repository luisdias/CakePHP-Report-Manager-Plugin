    <!-- Copyright (c) 2012-2013 Luis E. S. Dias - www.smartbyte.com.br -->
    <fieldset>
        <legend><?php echo __d('report_manager','Order Direction'); ?></legend>
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