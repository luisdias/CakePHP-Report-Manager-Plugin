<?php
/*
Copyright (c) 2012-2013 Luis E. S. Dias - www.smartbyte.com.br

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
?>
<h1><?php echo ($reportName == '' ? 'Report Manager' : $reportName);?></h1>
<div id="reportManagerDisplay">
    <?php 
    $counter = 0;
    $columns = 0;
    $floatFields = array();
    ?>     
    <?php if (!empty($reportData)):?>
    <table cellpadding = "0" cellspacing = "0" class="report" width="<?php echo $tableWidth;?>">
        <colgroup>
            <?php foreach ($tableColumnWidth as $field => $width): ?>
            <col width="<?php echo $width;?>">
            <?php endforeach; ?>                    
        </colgroup>        
        <tr class="header">
                <?php foreach ($fieldList as $field): ?>
                <th>
                <?php
                $columns++;
                $modelClass = substr($field, 0,strpos($field, '.'));
                $displayField = strtolower(substr($field, strpos($field, '.')+1));
                $displayField = ( isset($labelFieldList[$modelClass][$displayField]) ? $labelFieldList[$modelClass][$displayField] : ( isset($labelFieldList['*'][$displayField]) ? $labelFieldList['*'][$displayField] : $displayField ));
                $displayField = str_replace('_', ' ', $displayField);
                $displayField = ucfirst($displayField);
                echo $displayField; 
                if ( $fieldsType[$field] == 'float') // init array for float fields sum
                    $floatFields[$field] = 0;
                ?>
                </th>
                <?php endforeach; ?>
        </tr>
        <?php 
        $i = 0;        
        foreach ($reportData as $reportItem): 
            $counter++;
            $class = null;
            if ($i++ % 2 == 0) {
                $class = ' altrow';
            } 
        ?>
            <tr class="body<?php echo $class;?>">
                <?php foreach ($fieldList as $field): ?>
                    <td>
                    <?php                     
                    $params = explode('.',$field);
                    if ( $fieldsType[$field] == 'float') {
                        echo $this->element('format_float',array('f'=>$reportItem[$params[0]][$params[1]]));
                        $floatFields[$field] += $reportItem[$params[0]][$params[1]];
                    }                        
                    else
                        echo $reportItem[$params[0]][$params[1]]; 
                    ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        <?php if ( count($floatFields)>0 ) { ?>
            <tr class="footer">
                <?php foreach ($fieldList as $field): ?>
                <td>
                <?php
                if ( $fieldsType[$field] == 'float') 
                    echo $this->element('format_float',array('f'=>$floatFields[$field]));
                ?>
                </td>
                <?php endforeach; ?>
            </tr>
         <?php } ?>
    </table>
    <?php if ( $showRecordCounter ) { ?>    
        <div class="counter"><?php echo __d('report_manager','Count:',true); ?><?php echo $counter;?></div>
    <?php } ?>
    <div class="timestamp"><?php echo __d('report_manager','Report Created',true) . ' : ' . date('Y-m-d H:i:s'); ?></div>
    <?php endif; ?>
</div>