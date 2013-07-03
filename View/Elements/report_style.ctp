    <fieldset>
        <legend><?php echo __('Report Style'); ?></legend>
        <table class="reportManagerReportStyleSelector" cellpadding="0" cellspacing="0">
	<?php
        $styleOptions = array(
            'executive'=>'Executive',
            'ledger'=>'Ledger',
            'banded'=>'Banded',
            'presentation'=>'Presentation',
            'casual'=>'Casual',
            'simple'=>'Simple',
            'styled'=>'Styled',
        );
        $outputOptions = array(
            'html' => 'HTML',
            'xls' => 'Excel'
        );
        
            echo '<tr>';
            echo '<td>';
            echo $this->Form->input('AdHocReport.Style',array('type'=>'select','options'=>$styleOptions));            
            echo '</td>';             
            echo '</tr>';

            echo '<tr>';
            echo '<td>';
            echo $this->Form->input('AdHocReport.Output',array('type'=>'select','options'=>$outputOptions));            
            echo '</td>';             
            echo '</tr>';
            
            echo '<tr>';
            echo '<td>';
            echo __('Show record counter');
            if (isset($this->data['AdHocReport']['ShowRecordCounter']))
                $showRecordCounter = $this->data['AdHocReport']['ShowRecordCounter'];
            else
                $showRecordCounter = true;
            echo $this->Form->checkbox('AdHocReport.ShowRecordCounter',array('hiddenField' => true,'checked'=>$showRecordCounter));                     
            echo '</td>';             
            echo '</tr>';            
            
            echo '<tr>';
            echo '<td>';
            echo __('Show items with no related records');
            if (isset($this->data['AdHocReport']['ShowNoRelated']))
                $showNoRelated = $this->data['AdHocReport']['ShowNoRelated'];
            else
                $showNoRelated = false;
            echo $this->Form->checkbox('AdHocReport.ShowNoRelated',array('hiddenField' => true,'checked'=>$showNoRelated));
            echo '</td>';             
            echo '</tr>';
                  
        ?>
        </table>
    </fieldset>