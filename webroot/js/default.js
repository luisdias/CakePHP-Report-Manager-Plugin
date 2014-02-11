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
		.addClass( "ui-helper-clearfix" )
		.addClass( "ui-tabs-vertical" );
	$( "#newtabs li" )
		.removeClass( "ui-corner-top" )
		.addClass( "ui-corner-left" );
	
	
	// apply the master/slave relationship to the checkboxes
	function checkthemall(master, slaves){
		master.click(function(){
			
			if ($('#Filters .filter').length > 0){
				if ($(this).prop('checked') == false){
					if (confirm('Unchecking all fields will also remove all filters. Continue?')){
						$('#Filters .filter').remove();
					} else {
						return false;
					}
				}
			}
			
			slaves.prop('checked',$(this).prop('checked'));
			onCheckboxChange()
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
	onCheckboxChange();
	

	
	$('.filter .closeButton').click(function(){
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
		
		$html = $('#filter-template').clone();
		$html.removeClass('hidden');
		$html.attr('id', 'filter-'+filterId);
		$html.find('select,input').each(function(idx, elem){
			$elem = $(elem);
			$name = $elem.attr("name").replace('template',filterId);
			$elem.attr("name",$name);
		});
		
		$html.find('.closeButton').click(function(){
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
		
		return false;
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
	
	function onCheckboxChange(){
		var newoptionlist = [];
		var checkboxes = $('.reportManager .fieldCheckbox');
		var selects = $('.fieldSelectBox');
		
		// change all the fieldselectors, so they only show options for the checked fields
		var options = '';
		var numChecked = 0;
		for(var i=0;i<checkboxes.length;i++){
			var $checkbox = $(checkboxes[i]);
			if($checkbox.prop('checked')){
				options += '<option value="' + $checkbox.data('fieldname') + '">' + fieldsArray[$checkbox.data('fieldname')] + '</option>';
				numChecked++;
			}
		}
		
		selects.each(function(idx, elem){
			$elem = $(elem);
			// get the selected option
			var selectedoption = $elem.val();
			// replace all the options with the new list
			$elem.children().remove().end().append($(options));
			// re-select it, if it exists!
			$elem.val(selectedoption);
			// force in the selected attribute, so that previews work
			$elem.children().each(function(idx, opt){
				$opt = $(opt);
				if ($opt.attr('value') == selectedoption){
					$opt.attr('selected','selected');
				} else {
					$opt.removeAttr('selected');
				}
			})
		});
		
		// if there are no checkboxes checked, that changes some other things... you can't add a filter, and you 
		// also can't do any sorting
		if (numChecked == 0){
			$('#AddNewFilter').prop('disabled', true);
		} else {
			$('#AddNewFilter').prop('disabled', false);
		}
		
	}
	
	/* crazy field & filter dependency behaviours. woot! */
	$('.reportManager .fieldCheckbox').click(function(){
		
		// if we're unchecking a box, we might also be removing a filter that uses this field
		if ($(this).prop('checked')){
			// we add this field to the list that populates all the select boxes!
			//alert('is checked');
		} else {
			// check if this is one of the selected options in any of the filters
			
			var fieldname = $(this).data('fieldname');
			
			var filterstoremove = [];
			$('#Filters select.fieldSelectBox').each(function(idx, elem){
				if (fieldname == $(elem).val()){
					filterstoremove.push(elem); // we're pushing the actual element into the array. handy!
				}
			});
			if (filterstoremove.length > 0) {
				if (confirm('Hiding this field will remove ' +(filterstoremove.length)+ ' filter'+(filterstoremove.length==1?'':'s')+' that use'+(filterstoremove.length==1?'s':'')+' this field. Are you OK with that?')){
					for(var i=0;i<filterstoremove.length;i++){
						$(filterstoremove[i]).closest('.filter').remove();
						showhidefilterlogic();
					}
				} else {
					return false;
				}
			}
			
		}
		
		onCheckboxChange();
		
		
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

	$( "#sortableList" ).sortable({
		items: "li.sortable-field",
		handle: '.handle',
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
	$("#AdHocReportSave").click(function(e) {
		e.preventDefault();
		var $saveForm = $(this).parents('form');
		
		// Depending on whether there is an ID already, we're going
		// to be editing or saving
		var id = $("#AdHocReportId").val();
		if(id == undefined || id == '') {
			$saveForm.attr('action', '/ad_hoc_reporting/ad_hoc_reports/add');
		} else {
			$saveForm.attr('action', '/ad_hoc_reporting/ad_hoc_reports/edit/'+id);
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
