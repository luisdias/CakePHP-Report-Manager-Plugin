<?php
/*
Copyright (c) 2012 Luis E. S. Dias - www.smartbyte.com.br

based on an article from AppServ Open Project
http://www.appservnetwork.com/modules.php?name=News&file=article&sid=8

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
class Excel {
    
    public function sendHeaders() { 
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
        header("Content-type: application/vnd.ms-excel");
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
    
    public function buildXls(&$reportData = array(),&$fieldList=array(), &$fieldsType=array(), &$oneToManyOption=null, &$oneToManyFieldsList=null, &$oneToManyFieldsType = null, &$showNoRelated = false) {
        $this->sendHeaders();
        $row = 0;
        $col = 0;         
        if (!empty($reportData)):
            $this->xlsBOF();
            foreach ($reportData as $reportItem): 
                if ( $oneToManyOption !='' && !$showNoRelated && count($reportItem[$oneToManyOption])==0 )
                    continue;
                if ( $row == 0 ) {
                    $col = 0;                        
                    foreach ($fieldList as $field): 
                        $displayField = substr($field, strpos($field, '.')+1);
                        $displayField = str_replace('_', ' ', $displayField);
                        $displayField = ucfirst($displayField);
                        $this->xlsWriteLabel($row, $col, $displayField); 
                        $col++;
                    endforeach; 
                    $row++;
                }
                $col = 0;
                foreach ($fieldList as $field): 
                    $params = explode('.',$field);
                    if ( $fieldsType[$field] == 'float') {
                        $this->xlsWriteNumber($row, $col, $reportItem[$params[0]][$params[1]]);
                    }                        
                    else
                        $this->xlsWriteLabel($row, $col, $reportItem[$params[0]][$params[1]]); 
                    $col++;
                endforeach;
                $row++;
                if ( $oneToManyOption != '') {
                    if ( count($reportItem[$oneToManyOption])>0 ) {
                        $row++;
                        $col = 1;
                        foreach ($oneToManyFieldsList as $field): 
                            $displayField = substr($field, strpos($field, '.')+1);
                            $displayField = str_replace('_', ' ', $displayField);
                            $displayField = ucfirst($displayField);
                            $this->xlsWriteLabel($row, $col, $displayField); 
                            $col++;
                        endforeach; 
                        $row++;
                        foreach ($reportItem[$oneToManyOption] as $oneToManyReportItem): 
                            $col = 1;
                            foreach ($oneToManyFieldsList as $field): 
                                $params = explode('.',$field);
                                if ( $oneToManyFieldsType[$field] == 'float') {
                                    $this->xlsWriteNumber($row, $col, $oneToManyReportItem[$params[1]]);
                                }                        
                                else
                                    $this->xlsWriteLabel($row, $col, $oneToManyReportItem[$params[1]]); 
                                $col++;
                            endforeach;
                            $row++;
                        endforeach; 
                        $row++;
                    }; 
                };
            endforeach;
            $this->xlsEOF();
        endif;                 
    }
    
}
?>