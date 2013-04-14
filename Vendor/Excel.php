<?php
/**
 * Copyright (c) 2013 TribeHR Corp - http://tribehr.com
 * Copyright (c) 2013 Luis E. S. Dias - www.smartbyte.com.br
 * 
 * Based on an article from AppServ Open Project
 * http://www.appservnetwork.com/modules.php?name=News&file=article&sid=8
 *
 * Licensed under The MIT License. See LICENSE file for details.
 * Redistributions of files must retain the above copyright notice.
 *
 */
class Excel {
    
    public function sendHeaders() { 
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
        header("Content-type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");        
        header("Content-Disposition: attachment;filename=Report.xls"); 
        header("Content-Transfer-Encoding: binary ");      
    }
    
    public function xlsBOF() { 
        echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);  
    } 

    public function xlsEOF() { 
        echo pack("ss", 0x0A, 0x00); 
    } 

    public function xlsWriteNumber($row, $col, $value) { 
        echo pack("sssss", 0x203, 14, $row, $col, 0x0); 
        echo pack("d", $value); 
    } 

    public function xlsWriteLabel($row, $col, $value ) { 
        $L = strlen($value); 
        echo pack("ssssss", 0x204, 8 + $L, $row, $col, 0x0, $L); 
        echo $value; 
    }
    
    public function buildXls(&$reportData = array(),&$fieldList=array(), &$fieldsType=array(), &$showNoRelated = false) {
        $this->sendHeaders();
        $row = 0;
        $col = 0;         
        if (!empty($reportData)):
            $this->xlsBOF();
            foreach ($reportData as $reportItem): 
                if ( !$showNoRelated && false )
                    continue;
                if ( $row == 0 ) {
                    $col = 0;                        
                    foreach ($fieldList as $field): 
                        $displayField = substr($field, strpos($field, '.')+1);
                        $displayField = str_replace('_', ' ', $displayField);
                        $displayField = ucfirst($displayField);
                        $this->xlsWriteLabel($row, $col, utf8_decode($displayField)); 
                        $col++;
                    endforeach; 
                    $row++;
                }
                $col = 0;
                foreach ($fieldList as $field): 
                    $params = explode('.',$field);
                    if ( $fieldsType[$field] == 'float') {
                        $this->xlsWriteNumber($row, $col, utf8_decode($reportItem[$params[0]][$params[1]]));
                    }                        
                    else
                        $this->xlsWriteLabel($row, $col, utf8_decode($reportItem[$params[0]][$params[1]])); 
                    $col++;
                endforeach;
                $row++;
             endforeach;
            $this->xlsEOF();
        endif;                 
    }
    
}
?>