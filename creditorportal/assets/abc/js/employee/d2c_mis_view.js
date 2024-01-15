$(window).load(function() {
     
	/*$('a[href*="/d2c_mis_view"]').each(function(){
		
		$(this).addClass("navigate-mis");
	});*/
	
	$('input[name="misviewdates"]').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        },
		"dateLimit": {
                "month": 1
            },
    });
	
});

/*Function Created By Shardul Kulkarni on 28-July-2020 for invalid Redirect Data Start */
$( "#mis_report_type" ).change(function() {
  var misType = $( "#mis_report_type option:selected" ).val();
  $("#user_stages_invald_data_redirection").hide();
  $("#user_stages").show();
  
  if(misType == 2) {
	  $("#user_stages_invald_data_redirection").show();
	  $("#user_stages").hide();
  }
});
/*Function Created By Shardul Kulkarni on 28-July-2020 for invalid Redirect Data End */
		
$(document).ready(function(){
	
	$(document).on('click', '#misview_clearsearch', function(e){		
		e.preventDefault();
		/*$("#clear-search-filter").val(1);
		$("#search-applogs").val(0);
		$("#applogs_form").submit();*/
		window.location.href = "/d2c_mis_view/";
	});	
	
	$(document).on('click', '#date_search_misview', function(e){
		e.preventDefault();			
		$("#export_excel-mis").val(0);	
	   $("#misview_form").submit();
		
	});
	
	/*$(document).on('click', '.navigate-mis', function(e){
		debugger;
		e.preventDefault();
		var attr = $(this).attr("href");		
		$("#export_excel-mis").val(0);
		$('#misview_form').attr('action', attr).submit();
		
	});*/
	
	$(document).on('click', '#exportMISViewExcel', function(e){
		$("#export_excel-mis").val(1);	
		$("#misview_form").submit();
		
	});
	
});