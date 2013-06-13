/**
 * Copyright (c) 2013 TribeHR Corp - http://tribehr.com
 * Copyright (c) 2012 Luis E. S. Dias - www.smartbyte.com.br
 * 
 * Licensed under The MIT License. See LICENSE file for details.
 * Redistributions of files must retain the above copyright notice.
 */

$(document).ready(function(){

	// we need to do this so that the form clones properly when we want a preview of it.
	// as the "selected" property is changed, the selected attribute does not! Thus a cloned
	// copy of the form does not retain the selected value in a select box. The workaround for
	// this is to write the selected attribute into the markup on the change event.
	$('#ReportWizardForm select').change(function(){
		$val = $(this).val();
		$(this).children().each(function(idx, elem){
			$elem = $(elem);
			if ($elem.attr('value') == $val){
				$elem.attr('selected','selected');
			} else {
				$elem.removeAttr('selected');
			}
		});
	});
	
	$('#buttonpreview').click(function(){
		// create a clone of the form, give it a _blank target, then submit it. Viola: Instant preview.
		// this is probably the coolest function I've written so far in 2013. ~ ir
		$($(this).closest('form'))
			.clone(true)
			.attr('target', '_blank')
			.appendTo($('body'))
			.submit()
			.remove();
	});
	
	// these classes were suggested in the jQuery UI vertical menu demo
	$('#newtabs')
		.tabs()
		.addClass( "ui-tabs-vertical ui-helper-clearfix" );
	$( "#newtabs li" )
		.removeClass( "ui-corner-top" )
		.addClass( "ui-corner-left" );
	
	
	// apply the master/slave relationship to the checkboxes
	function checkthemall(master, slaves){
		master.click(function(){
			slaves.prop('checked',$(this).prop('checked'));
		});
		var testslaves = function(){
			var allchecked = true;
			slaves.each(function(idx,elem){
				if ( $(elem).prop('checked') === false){
					master.prop('checked',false);
					allchecked = false;
					return;
				}
			});
			master.prop('checked',allchecked);
			return true;
		};
		testslaves();
		slaves.click(testslaves);
	}
	checkthemall($('#fieldsCheckAll'), $('.sortable-field .fieldCheckbox'));
	
	
	$('.filter .close-button').click(function(){
		$(this).closest('.filter').remove();
		showhidefilterlogic();
	});
	
	// show the logical operator (AND or OR) if there are two or more filters
	function showhidefilterlogic() {
		if ($('#Filters').children().length > 1){
			$('#FilterLogicContainer').show();
		} else {
				$('#FilterLogicContainer').hide();
		}
	}
	showhidefilterlogic();
	
	$('#AddNewFilter').click(function(){
		
		var filterId = Math.floor((Math.random()*10000000)+1); 
		// it would be nice if we didn't use a random ID. However I don't think the form 
		// would work right without it. In the POST submission, the names of the form elements
		// need to be grouped by something.
		
		var options = '';
		for(f in fieldsArray){
			options += '<option value="'+f+'">'+fieldsArray[f]+'</option>';
		}
		
		// this markup is the same as that which is genreated by the View.
		html = '';
		html +='	<div class="filter" id="filter-'+filterId+'">';
		html +='		<div class="close-button">X</div>';
		html +='		<div>';
		html +='			<label>Filter By: </label>';
		html +='			<select name="data[Filters]['+filterId+'][Field]">';
		html +='			'+options+'';
		html +='			</select>';
		html +='		</div>';
		html +='		<div class="grid_2">';
		html +='			<input type="checkbox" name="data[Filters]['+filterId+'][Not]" value="1" /><label>IS NOT</label>';
		html +='		</div>';
		html +='		<div class="grid_2">';
		html +='			<select name="data[Filters]['+filterId+'][Operator]">';
		html +='				<option value="=" >equals</option>';
		html +='				<option value="&gt;" >is greater than</option>';
		html +='				<option value="&lt;" >is less than</option>';
		html +='			</select>';
		html +='		</div>';
		html +='		<div class="grid_4">';
		html +='			<input type="text" name="data[Filters]['+filterId+'][Value]" />';
		html +='		</div>';
		html +='	</div>';
		
		$html = $(html);
		$html.find('.close-button').click(function(){
			$(this).closest('.filter').remove();
			showhidefilterlogic();
		});
		$html.appendTo($('#Filters'));
		
		// we need to apply the unusual select behaviour here, to the new select boxes
		// TODO: put this in a jQuery plugin or something generic like that
		$('#filter-'+filterId+' select').change(function(){
			$val = $(this).val();
			$(this).children().each(function(idx, elem){
				$elem = $(elem);
				if ($elem.attr('value') == $val){
					$elem.attr('selected','selected');
				} else {
					$elem.removeAttr('selected');
				}
			});
		});
		
		
		showhidefilterlogic();
	});
	
	$('#step-1-next').click(function(){
		$('#newtabs').tabs({'active':1});
	});
	$('#step-2-prev').click(function(){
		$('#newtabs').tabs({'active':0});
	});
	$('#step-2-next').click(function(){
		$('#newtabs').tabs({'active':2});
	});
	$('#step-3-prev').click(function(){
		$('#newtabs').tabs({'active':1});
	});
	$('#step-3-next').click(function(){
		$('#newtabs').tabs({'active':3});
	});
	$('#step-4-prev').click(function(){
		$('#newtabs').tabs({'active':2});
	});
	
});






$(document).ready(function() {
	
	/* TODO: investigate whether this older stuff can be removed */
	
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

    $( ".sortable1" ).sortable({
        items: "div.sortable-field",
        axis: 'y',
        stop: function(event, ui) {
            $('input.position').reNumberPosition();
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
        $(this).val(i); // this is jQuery's way to set a value
        $(this).attr("value",i); // bugfix for issues with clone()
        i++;
    });      
};