// Copyright (c) 2012 Luis E. S. Dias - www.smartbyte.com.br
//
// Permission is hereby granted, free of charge, to any person obtaining
// a copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to
// permit persons to whom the Software is furnished to do so, subject to
// the following conditions:
//
// The above copyright notice and this permission notice shall be
// included in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
// EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
// NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
// LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
// OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
// WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

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
    
    $('#wizardSubmit').hide();
    
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
    
    $( ".sortable2 tbody" ).sortable({
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