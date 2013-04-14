<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $this->Html->charset(); ?>
<title>
    <?php echo $title_for_layout; ?>
</title>
<?php
    echo $this->Html->meta('icon');
    echo $this->Html->css('/CustomReporting/css/generic');
    echo $this->Html->css('/CustomReporting/css/'.$reportStyle.'.css');
    echo $scripts_for_layout;
?>
</head>
<body>
<div id="main">    
    <?php echo $content_for_layout; ?>    
    <?php echo $this->element('sql_dump'); ?>      
</div>
</body>
</html>