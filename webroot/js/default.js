/**
 * Copyright (c) 2013 TribeHR Corp - http://tribehr.com
 * Copyright (c) 2012 Luis E. S. Dias - www.smartbyte.com.br
 * 
 * Licensed under The MIT License. See LICENSE file for details.
 * Redistributions of files must retain the above copyright notice.
 */

$(document).ready(function() {
    $('#wizard').smartWizard({onLeaveStep:leaveAStepCallback,
                              onFinish:onFinishCallback});

    function leaveAStepCallback(obj){
        var step_num= obj.attr('rel'); 
        return validateSteps(step_num); 
    }
                          
    function onFinishCallback(){
        if(validateAllSteps()){
            $('#ReportWizardForm').submit();
        }
    }
    
    function validateSteps(stepnumber){
        var isStepValid = true;
        if(stepnumber == 1){
            isStepValid = false;
            $("tr :checkbox[name*='[Add]']").each(function(){
                if ( $(this).is(':checked')) {
                    isStepValid = true;
                }
            });            
        }
        if ( !isStepValid )
            $('#wizard').smartWizard('showMessage','No field selected');
        return isStepValid;
        
    }
    
    function validateAllSteps(){
        var isStepValid = true;
        // all step validation logic     
        return isStepValid;
    }
    
    // fields position default values
    $('input.position').reNumberPosition();
    
    // change tr background color when checked
    // closest parent element
    $("tr :checkbox[name*='[Add]']").live("click", function() {
        $(this).closest("tr").css("background-color", this.checked ? "#eee" : "");
    });
    
    // init: all checkbox fields are checked
    $("tr :checkbox[name*='[Add]']").each(function(){
        $(this).closest("tr").css("background-color", this.checked ? "#eee" : "");
    });

    $( ".sortable1 tbody" ).sortable({
        items: "tr",
        cancel: "thead",
        axis: 'y',
        stop: function(event, ui) {
            $('input.position').reNumberPosition();
            this.update();            
        }
    });    
    
    $('.checkAll').click(function () {
        model = $(this).text();
        $("tr :checkbox[name^=\"data["+model+"]\"][name*='[Add]']").each(function(){
            $(this).attr('checked', !this.checked);
            $(this).closest("tr").css("background-color", this.checked ? "#eee" : "");
        });
    });
    
    $( ".datepicker" ).datepicker({
            showOn: "button",
            buttonImageOnly: true
    });

	/**
	 * When clicking the save button, over-ride the target attribute,
	 * the form action, and then submit the form
	 */
	$("#CustomReportSave").click(function(e) {
		e.preventDefault();
		var $saveForm = $(this).parents('form');
		
		// Depending on whether there is an ID already, we're going
		// to be editing or saving
		var id = $("#CustomReportId").val();
		if(id == undefined || id == '') {
			$saveForm.attr('action', '/custom_reporting/custom_reports/add');
		} else {
			$saveForm.attr('action', '/custom_reporting/custom_reports/edit/'+id);
		}
		$saveForm.attr('target', '');
		$saveForm.submit();			
	});
    
});


jQuery.fn.reNumberPosition = function() {
    var e = $(this);
    var c = null;
    var i = 1;
    e.each(function(){
        this.value = i;
        i++;
    });      
};