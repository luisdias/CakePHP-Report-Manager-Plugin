    <!-- Copyright (c) 2012 Luis E. S. Dias - www.smartbyte.com.br -->
    <fieldset>
        <legend><?php echo __('Logical Operator'); ?></legend>
        <table class="reportManagerLogicalOperatorSelector" cellpadding="0" cellspacing="0">
	<?php
        $logicalOptions = array(
            'AND'=>'AND',
            'OR'=>'OR'
            );
            echo '<tr>';         
            echo '<td>';
            echo $this->Form->input('Logical',array('type'=>'select','options'=>$logicalOptions,'label'=>false));            
            echo '</td>';             
            echo '</tr>';
        ?>
        </table>
    </fieldset>