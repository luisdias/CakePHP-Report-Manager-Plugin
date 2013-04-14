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
        $outputOptions = array(
            'html' => 'HTML',
            'xls' => 'Excel'
        );
        
            echo '<tr>';
            echo '<td>';
            echo $this->Form->input('CustomReport.Style',array('type'=>'select','options'=>$styleOptions));            
            echo '</td>';             
            echo '</tr>';

            echo '<tr>';
            echo '<td>';
            echo $this->Form->input('CustomReport.Output',array('type'=>'select','options'=>$outputOptions));            
            echo '</td>';             
            echo '</tr>';
            
            echo '<tr>';
            echo '<td>';
            echo __('Show record counter');
            if (isset($this->data['CustomReport']['ShowRecordCounter']))
                $showRecordCounter = $this->data['CustomReport']['ShowRecordCounter'];
            else
                $showRecordCounter = true;
            echo $this->Form->checkbox('CustomReport.ShowRecordCounter',array('hiddenField' => true,'checked'=>$showRecordCounter));                     
            echo '</td>';             
            echo '</tr>';            
            
            echo '<tr>';
            echo '<td>';
            echo __('Show items with no related records');
            if (isset($this->data['CustomReport']['ShowNoRelated']))
                $showNoRelated = $this->data['CustomReport']['ShowNoRelated'];
            else
                $showNoRelated = false;
            echo $this->Form->checkbox('CustomReport.ShowNoRelated',array('hiddenField' => true,'checked'=>$showNoRelated));
            echo '</td>';             
            echo '</tr>';
                  
        ?>
        </table>
    </fieldset>