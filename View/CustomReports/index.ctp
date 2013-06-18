<?php

/**
 * Copyright (c) 2013 TribeHR Corp - http://tribehr.com
 * Copyright (c) 2012 Luis E. S. Dias - www.smartbyte.com.br
 * 
 * Licensed under The MIT License. See LICENSE file for details.
 * Redistributions of files must retain the above copyright notice.
 */

?>
<script type="text/javascript">
    firstLevel = "<?php echo Router::url('/'); ?>";
</script>

<?php echo $this->Html->script(array('https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js')); ?>
<?php echo $this->Html->css('/CustomReporting/css/report_manager.css'); ?>

<div class="reportManager index form">
    <h2><?php echo __('Custom Reports');?></h2>
    <?php
        
        echo $this->Form->create();
        echo $this->Form->input('model',array(
            'type'=>'select',
            'label'=>__('Record'),
            'options' => $models,
            'empty'=>__('--Select--')
            ));        
        echo $this->Form->submit(__('New Report'), array('name'=>'new'));
        
    ?>
	<h3><?php echo __('Report Library'); ?></h3>
	<table cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th><?php echo __('Report Name'); ?></th>
				<th><?php echo __('Action'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($customReports as $id => $customReport): ?>
			<tr>
				<td><?php echo $this->Html->link($customReport, array('action' => 'view', $id), array('target' => '_blank')); ?></td>
				<td>
					<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $id)); ?> | 
					<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $id), null, sprintf(__('Are you sure you want to delete "%s"?'), $customReport)); ?> | 
					<?php echo $this->Html->link(__('Copy'), array('action' => 'duplicate', $id)); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>