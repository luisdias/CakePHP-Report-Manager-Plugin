<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $this->Html->charset(); ?>
<title>
    <?php echo $title_for_layout; ?>
</title>
<?php
    echo $this->Html->meta('icon');
    echo $this->Html->css('/ReportManager/css/generic');
    echo $this->Html->css('/ReportManager/css/'.$reportStyle);
    echo $scripts_for_layout;
?>
</head>
<body>
<div id="main">    
    <?php echo $content_for_layout; ?>    
    <div id="copyright"><br/>© 2012 Report Manager developed by <a href="mailto:smartbyte.systems@gmail.com">Luis E. S. Dias</a> - <a target="blank" href="http://www.smartbyte.com.br/site/contato/">Smartbyte</a></div>        
    <?php echo $this->element('sql_dump'); ?>      
</div>
</body>
</html>