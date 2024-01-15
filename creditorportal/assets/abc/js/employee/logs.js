$(window).load(function() {
      $('a[href*="/post_log"]').each(function(){
		
		$(this).addClass("navigate");
	});
});

$(document).ready(function(){
	$('input[name="dates"]').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        },
		"dateLimit": {
                "month": 1
            },
    });
	
	$('input[name="dates_n"]').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        },
    });
	
	
	
	$(document).on('click', '.navigate', function(e){
		debugger;
		e.preventDefault();
		var attr = $(this).attr("href");
		if(attr == 'http://eb.benefitz.in/all_logs'){
			attr == 'http://eb.benefitz.in/all_logs/1';
		}
		$('#lead_form').attr('action', attr).submit();
		
		
	});
	
	$(document).on('click', '#lead_search,#date_search', function(e){
		debugger;
		e.preventDefault();
		
		 var product = $('#product_type option:selected').val();
	 if(product == ''){
		 alert('Please Select Product Type'); return false;
	 }
	 
	 if(e.target.id == 'date_search'){
		 var lead_id = $('#lead_id').val();
		 $('#lead_id').val('');
		 
	 }
	 else{
		 var lead_id = $('#lead_id').val();
	  if(lead_id == ''){
		 alert('Please Enter lead id'); return false;
	 }
	 }
	 
	 
	 
	 
	 
	 $("#lead_form").submit();
	 
		
		
		
		
		
		
		
	});
});

