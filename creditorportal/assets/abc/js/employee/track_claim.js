$(document).ready(function(){
		$.ajax({
        url: "/get_all_policy_no",
        type: "POST",
        dataType: "json",
        success: function (response) {
        	 $('#policy_no').empty();
			 $('#policy_no').append('<option value=""> Select policy type</option>');
			  $.each(response, function (index, value) {
			  	var date = value['end_date'].split("-");
			  	var date = new Date(Number(date[0]), Number(date[1])-1, Number(date[2]));
			  	var current_date = new Date();
			  	if(date > current_date){
			  		$('#policy_no').append('<option class="" value="' + value['policy_no'] + '">' + value['policy_sub_type_name']  + '</option>');
				}
			  });
        }
    });
		// on policy no get emp member
		$("#policy_no").change(function(){
	   		var policy_no = $(this).val();
	   		var emp_id = $("#emp_id").val();
	  		 $.ajax({
		        url: "/get_employee_family_on_emp_id",
		        type: "POST",
		        data:{policy_no:policy_no,emp_id:emp_id},
		        success: function (response) {
		        	var data_res = JSON.parse(response);
		        	 $('#member_name').empty();
		        	 $('#member_name').append('<option value=""> Select Member Name</option>');
		        	   $.each(data_res, function (index, value) {
		        	   	$('#member_name').append('<option class="" value="' + value.emp_member_id  + '">' + value.name + '</option>');
		    		});
					}
		        });
	   });
		// member id get claim id
		 $("#member_name").change(function(){
	   	var policy_no = $('#policy_no').val();
	   		var member_id = $(this).val();
	   		 $.ajax({
		        url: "/get_claimid_on_member_id",
		        type: "POST",
		        data:{member_id:member_id,policy_no:policy_no},
		        success: function (response) {
		        	var data_res = JSON.parse(response);
		        	 $('#claim_id_drop').empty();
		        	 $('#claim_id_drop').append('<option value=""> Select claim Id</option>');
		        	   $.each(data_res, function (index, value) {
		        	   	$('#claim_id_drop').append('<option class="" value="' + value.claim_reimb_id  + '">' + value.claim_reimb_id + '</option>');   	 
					 });
					}
		        });
	   });

		  $("#claim_id_drop").change(function(){
	    	var claim_id = $(this).val();
	    	 $.ajax({
		        url: "/get_dates_on_claim_id",
		        type: "POST",
		        data:{claim_id:claim_id},
		        success: function (response) {
		        	var data_res = JSON.parse(response);
		        	   $.each(data_res, function (index, value) {
		        	   		if (value.register_date) 
		        	   		{
		        	   			var date_register = new Date(value.register_date);
								var reg_date = date_register.toDateString();
		        	   			$("#claim_register").text(reg_date);
		        	   			$("#claim_register1").attr('class','active');
		        	   			$("#document_submit1").removeAttr('class','active');
		        	   		}
					 });
					}
		        });

	    	 	 $.ajax({
		        url: "/get_datesdocs_on_claim_id",
		        type: "POST",
		        data:{claim_id:claim_id},
		        success: function (response) {
		        	var data_res1 = JSON.parse(response);
		        	   $.each(data_res1, function (index, value1) {
		        	   		if (value1.doc_submitted) 
		        	   		{
		        	   			var date_doc = new Date(value1.doc_submitted);
								var doc_date = date_doc.toDateString();
		        	   			$("#document_submit").text(doc_date);
		        	   			$("#document_submit1").attr('class','active');
		        	   		}
					 });
					}
		        });
	    });
		
		$("#claim_id").on("keyup", function(e) {
			if(e.keyCode == 13) {
				$('#search_hopsitals_search').click();
			}
		});

		    $("#search_hopsitals_search").click(function(){
	    	var claim_id = $("#claim_id").val();
	    	 $.ajax({
		        url: "/get_dates_on_claim_id",
		        type: "POST",
		        data:{claim_id:claim_id},
		        success: function (response) {
		        	var data_res = JSON.parse(response);
		        	   $.each(data_res, function (index, value) {
		        	   		if (value.register_date) 
		        	   		{
		        	   			var date_register = new Date(value.register_date);
								var reg_date = date_register.toDateString();
		        	   			$("#claim_register").text(reg_date);
		        	   			$("#claim_register1").attr('class','active');
		        	   			$("#document_submit1").removeAttr('class','active');
		        	   		}
					 });
					}
		        });

	    	 	 $.ajax({
		        url: "/get_datesdocs_on_claim_id",
		        type: "POST",
		        data:{claim_id:claim_id},
		        success: function (response) {
		        	var data_res1 = JSON.parse(response);
		        	   $.each(data_res1, function (index, value1) {
		        	   		if (value1.doc_submitted) 
		        	   		{
		        	   			var date_doc = new Date(value1.doc_submitted);
								var doc_date = date_doc.toDateString();
		        	   			$("#document_submit").text(doc_date);
		        	   			$("#document_submit1").attr('class','active');
		        	   		}
					 });
					}
		        });
	    });
		 // localStorage.clear();
		  var shashi_set = localStorage.getItem("shashi_set");
		if (shashi_set == 'no') 
		{
			var claim_id1 = localStorage.getItem("claim_id");
          $.ajax({
                url: "/get_dates_on_claim_id",
                type: "POST",
                data:{claim_id:claim_id1},
                success: function (response) {
                    var data_res = JSON.parse(response);
                       $.each(data_res, function (index, value) {
                            if (value.register_date) 
                            {
                                var date_register = new Date(value.register_date);
                                var reg_date = date_register.toDateString();
                                $("#claim_register").text(reg_date);
                                $("#claim_register1").attr('class','active');
                                $("#document_submit1").removeAttr('class','active');
                            }
                     });
                    }
                });

                 $.ajax({
                url: "/get_datesdocs_on_claim_id",
                type: "POST",
                data:{claim_id:claim_id1},
                success: function (response) {
                    var data_res1 = JSON.parse(response);
                       $.each(data_res1, function (index, value1) {
                            if (value1.doc_submitted) 
                            {
                                var date_doc = new Date(value1.doc_submitted);
                                var doc_date = date_doc.toDateString();
                                $("#document_submit").text(doc_date);
                                $("#document_submit1").attr('class','active');
                            }
                     });
                    }
                });
                 localStorage.clear();
		}
		else
		{
			localStorage.clear();
		}


	});