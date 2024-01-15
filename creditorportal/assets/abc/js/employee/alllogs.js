$(window).load(function() {
     /* $('a[href*="/all_logs"]').each(function(){
		
		$(this).addClass("navigate");
	});*/
	
	$('input[name="applogdates"]').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        },
		"dateLimit": {
                "month": 1
            },
    });
	setTimeout(function(){ $('#product_type').trigger('change');}, 500);
	
	
	
});
$(document).ready(function(){
	
	
	$(document).on('click', '#applogs_clearsearch', function(e){		
		e.preventDefault();
		/*$("#clear-search-filter").val(1);
		$("#search-applogs").val(0);
		$("#applogs_form").submit();*/
		window.location.href = "/all_logs/";
	});	
	
	$(document).on('change','#product_type',function(){
		
		if($(this).val() != "") {
			
			if ($(this).val() == 'R13'  || $(this).val() == 'R14'){
				$('#hr13').hide();
				$('#sor').hide();
				$('#tor').hide();
				$('#hmn').hide();
				$('#hec').hide();
				$('#email_r13').hide();
				$('#hftime').hide();
				$('#httime').hide();				
								
			}else if ($(this).val() == 'R05'){
				$('#filter_type_ro3').hide();
				$('#ro3_div').hide();
				$('#filter_type_ro5').show();
			} else {
				
				$('#filter_type_ro3').show();
				$('#filter_type_ro5').hide();
				$('#ro3_div').show();				
				
			}	
		}			
		
	});
	
	
	$(document).on('click', '#applogsdate_search', function(e){
		debugger;
		e.preventDefault();
		
		var product = $('#product_type option:selected').val();
		if(product == ''){
			alert('Please Select Product Type'); return false;
		}
	 
	 /*if(e.target.id == 'date_search'){
		 var lead_id = $('#lead_id').val();
		 $('#lead_id').val('');
		 
	 }
	 else{
		 var lead_id = $('#lead_id').val();
		 var cert_no = $('#certificate_no').val();
		  if(lead_id == '' && cert_no == ''){
			 alert('Please Enter lead id or Cerificate no'); return false;
		 }
	 }*/
	  $("#export_excel-mis").val(0);
	 $("#applogs_form").submit();
		
	});
	
	/*$(document).on('click', '.navigate', function(e){
		debugger;
		e.preventDefault();
		var attr = $(this).attr("href");
		if(attr == 'http://eb.benefitz.in/post_log'){
			attr == 'http://eb.benefitz.in/post_log/1';
		}
		$('#applogs_form').attr('action', attr).submit();
		
	});*/
	
	/*$(document).on('click', '#exportExcel', function(e){
		debugger;
			 var product = $('#product_type option:selected').val();
			if(product == ''){
			alert('Please Select Product Type'); return false;
			}
	 
		var attr = 'http://eb.benefitz.in/all_logs_download'
		$('#applogs_form').attr('action', attr).submit();
		
	});*/
	
	$(document).on('click', '#exportApplogsExcel', function(e){
		$("#export_excel-applogs").val(1);	
		$("#applogs_form").submit();
		
	});
	
});