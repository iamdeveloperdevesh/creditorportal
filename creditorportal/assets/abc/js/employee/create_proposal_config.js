    $.ajax({
        url: "/get_all_policy_numbers1",
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
	
$(document).on('click','#masPolicy',function(){  
	 var policy_id = $('#masPolicy').val();	
$.ajax({
        type: "POST",
        url: "/edit_view_configure",
		data:{"policy_id":policy_id},

        success: function (response) {
        
		
        }
      
    });
});