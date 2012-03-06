    <!-- Copyright (c) 2012 Luis E. S. Dias - www.smartbyte.com.br -->
    <fieldset>
        <legend><?php echo __('Report Style'); ?></legend>
        <table class="reportManagerReportStyleSelector" cellpadding="0" cellspacing="0">
	<?php
        $styleOptions = array(
            'executive'=>'Executive',
            'ledger'=>'Ledger',
            'banded'=>'Banded',
            'presentation'=>'Presentation',
            'casual'=>'Casual'
            );
            echo '<tr>';
            echo '<td>';
            echo $this->Form->input('ReportName',array('size'=>'80','maxlength'=>'80'));            
            echo '</td>';
            echo '</tr>';
            
            echo '<tr>';
            echo '<td>';
            echo $this->Form->input('Style',array('type'=>'select','options'=>$styleOptions));            
            echo '</td>';             
            echo '</tr>';
            
            echo '<tr>';
            echo '<td>';
            echo __('Show record counter');
            echo $this->Form->checkbox('ShowRecordCounter',array('hiddenField' => false,'checked'=>true));                     
            echo '</td>';             
            echo '</tr>';            
            
            if ($oneToManyOption != '') {
                echo '<tr>';
                echo '<td>';
                echo __('Show items with no related records');
                echo $this->Form->checkbox('ShowNoRelated',array('hiddenField' => false,'checked'=>false));
                echo '</td>';             
                echo '</tr>';
            }
            
        ?>
        </table>
    </fieldset>