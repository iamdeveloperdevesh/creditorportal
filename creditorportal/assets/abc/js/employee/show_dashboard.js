$(document).ready(function () {
	
	$('input[name="dates"]').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        }
    });
	
	
	
	$("#from_date").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100Y:",
        onSelect: function(date) {

            var selectedDate = new Date(date);
            var msecsInADay = 86400000;
            var endDate = new Date(selectedDate.getTime() + msecsInADay);

            //Set Minimum Date of EndDatePicker After Selected Date of StartDatePicker
            $("#to_date").datepicker("option", "minDate", endDate);
            // $("#endDatePicker").datepicker( "option", "maxDate", '+2y' );

        },
        maxDate: new Date(),
        minDate: "-100Y +1D"


    });
    $("#to_date").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100Y:",
        maxDate: new Date(),
        // minDate: "-100Y +1D"
    });
	
	$.ajax({
        url: "/get_all_policy_numbers",
        type: "POST",
        async: false,
        dataType: "json",
        success: function (response) {

            // debugger;
            $("#masPolicy").empty();
            $("#masPolicy").append("<option value = ''>Select Product Name</option>");
            for (i = 0; i < response.length; i++) {
        
                $("#masPolicy").append("<option data-customer = '" + response[i]['customer_search_status'] + "'  data-id = '" + response[i]['policy_subtype_id'] + "' value =" + response[i]['parent_policy_id'] + ">" + response[i]['product_name'] + "</option>");


            }
        }
    });
	
	
	
	
	 // $(document).on('change', '#masPolicy', function () {
		 
		 // var policy_detail_id = $('#masPolicy option:selected').val();
		 
		 // $.ajax({
        // url: "/get_dashboard_data_latest1", 
        // type: "POST",
        // async: false,
		// data : {"policy_detail_id" : policy_detail_id},
        // dataType: "json",
        // success: function(response) {
			// $("#append_data").empty();
			
			 // $.each(response, function (key, val) {
					// $("#append_data").append("<tr>");
				// for(var i=0; i < val.length; i++){
					
					// $("#append_data").append("<td>"+val[i]+"</td>");
					
				// }
				// $("#append_data").append("</tr>");
			// });
			
			
			
          
        // }
    // });
		 
	 // });
	
});


 $(document).on('click', '#submit', function () {
	 
	 var policy_detail_id = $('#masPolicy option:selected').val();
	 var dates = $('#dates').val();
	 
	 if(policy_detail_id != ''){
	 	 
		 $.ajax({
        url: "/get_dashboard_data_latest1", 
        type: "POST",
        async: false,
		data : {"policy_detail_id" : policy_detail_id,"dates":dates},
        dataType: "json",
        success: function(response) {
			$("#append_data").empty();
			
			 $.each(response, function (key, val) {
					$("#append_data").append("<tr>");
				
				 for(var i=0; i < val.length; i++){
                        //      debugger;
                                                if(i == 2){
                                                $("#append_data").append("<td>"+(val[i])+"</td>");
}
                                        else{
                                        $("#append_data").append("<td>"+Number(val[i]).toFixed(2)+"</td>");
}

                                }
				$("#append_data").append("</tr>");
			});
			
			
			
          
        }
    });
 }else{
	 alert('Please Select Product');
 }
	
	  });