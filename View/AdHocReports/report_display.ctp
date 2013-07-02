<?php
/**
 * Copyright (c) 2013 TribeHR Corp - http://tribehr.com
 * Copyright (c) 2012 Luis E. S. Dias - www.smartbyte.com.br
 * 
 * Licensed under The MIT License. See LICENSE file for details.
 * Redistributions of files must retain the above copyright notice.
 */
?>


<img src="/img/tribehr_logo.png" alt="TribeHR logo" width="116" height="83" class="logo" /> 
<div class="details">
	<h1><?php echo ($reportName == '' ? 'Ad Hoc Report' : $reportName);?></h1>
	<h2><?php echo h($settings['Config']['name']); ?></h2>
	<div class="timestamp">Report Generated : <strong><?php echo date("Y-m-d H:i:s"); ?></strong></div>
</div>


<div id="reportManagerDisplay">
	<?php 
	$counter = 0;
	$columns = 0;
	$floatFields = array();
	
    if (!empty($reportData)) {
	
    echo '<table cellpadding = "0" cellspacing = "0" class="report" width="'. $tableWidth.'">';
	
	echo '<tr class="header">';
		foreach ($fieldList as $field) { ?>
			<th>
				<?php
				$columns++;
				$displayField = substr($field, strpos($field, '.')+1);
				$displayField = str_replace('_', ' ', $displayField);
				$displayField = ucfirst($displayField);
				echo $displayField; 
				if ( $fieldsType[$field] == 'float') {// init array for float fields sum
					$floatFields[$field] = 0;
				}
				?>
			</th>
		<?php } ?>
	</tr>
	
	<?php 
	$i = 0;        
	foreach ($reportData as $reportItem) { 
		$counter++;
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' altrow';
		} 
		echo '<tr class="body' . $class . '">';
		foreach ($fieldList as $field) {
			echo '<td>';
		
			$params = explode('.',$field);
			if ( $fieldsType[$field] == 'float') {
				echo $this->element('format_float',array('f'=>$reportItem[$params[0]][$params[1]]));
				$floatFields[$field] += $reportItem[$params[0]][$params[1]];
			} else {
				echo h($reportItem[$params[0]][$params[1]]);
			}
			echo '</td>';
		} 
		echo '</tr>';
	}

	if ( count($floatFields)>0 ) {
		echo '<tr class="footer">';
		foreach ($fieldList as $field) {
			echo '<td>';
			if ( $fieldsType[$field] == 'float') {
				echo $this->element('format_float',array('f'=>$floatFields[$field]));
			}
			echo '</td>';
		}
		echo '</tr>';
	}
	
	     
	echo '</table>';
	
	if ( $showRecordCounter ) {
		echo '<div class="counter">Count: ' . $counter . '</div>';
	}
}
?>
</div>