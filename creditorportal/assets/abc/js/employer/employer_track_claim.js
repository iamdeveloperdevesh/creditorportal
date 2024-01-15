$(document).ready(function(){
	 $.ajax({
        url: "/get_all_insurer",
        type: "POST",
        success: function (response) {
        	var data_res = JSON.parse(response);
        	 $('#insurer_data').empty();
        	 $('#insurer_data').append('<option value=""> Select Insurer Name</option>');
        	   $.each(data_res, function (index, value) {
        	   		$('#insurer_data').append('<option class="" value="' + value.insurer_id  + '">' + value.ins_co_name + '</option>');
    			});
			}
        });
	 // on insurer id policy type
	 $("#insurer_data").change(function(){
	 		var insurer_id = $(this).val();
	 		 $.ajax({
		        url: "/get_policytype_on_insurer",
		        type: "POST",
		        data:{insurer_id:insurer_id},
		        success: function (response) {
		        	var data_res = JSON.parse(response);
		        	 $('#policy_type').empty();
		        	 $('#policy_type').append('<option value=""> Select Policy Type</option>');
		        	   $.each(data_res, function (index, value) {
		        	   	$('#policy_type').append('<option class="" value="' + value.policy_sub_type_id  + '">' + value.policy_sub_type_name + '</option>');
		    		});
					}
		        });
	 });
	 // on policy_type get policy_no
	  $("#policy_type").change(function(){
	  		var policy_type = $(this).val();
	  		var insurer_id = $("#insurer_data").val();
	  		 $.ajax({
		        url: "/get_policyno_on_policytype",
		        type: "POST",
		        data:{policy_type:policy_type,insurer_id:insurer_id},
		        success: function (response) {
		        	var data_res = JSON.parse(response);
		        	 $('#policy_no').empty();
		        	 $('#policy_no').append('<option value=""> Select Policy #</option>');
		        	   $.each(data_res, function (index, value) {
		        	   	$('#policy_no').append('<option class="" value="' + value.policy_no  + '">' + value.policy_no + '</option>');
					 });
					}
		        });

	  });
	  // on policy_no get employee
	   $("#policy_no").change(function(){
	   		var policy_no = $(this).val();
	  		 $.ajax({
		        url: "/get_employee_on_policy_no",
		        type: "POST",
		        data:{policy_no:policy_no},
		        success: function (response) {
		        	var data_res = JSON.parse(response);
		        	 $('#emp_name').empty();
		        	 $('#emp_name').append('<option value=""> Select Employee Name</option>');
		        	   $.each(data_res, function (index, value) {
		        	   	$('#emp_name').append('<option class="" value="' + value.id  + '">' + value.name + '</option>');
					 });
					}
		        });
	   });
	   // on emp_id get family member
	   $("#emp_name").change(function(){
	   		var policy_no = $('#policy_no').val();
	   		var emp_id = $(this).val();
	  		 $.ajax({
		        url: "/employer/get_employee_family_on_emp_id",
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
	   // on member_id get claim_id
	   $("#member_name").change(function(){
	   	var policy_no = $('#policy_no').val();
	   		var member_id = $(this).val();
	   		 $.ajax({
		        url: "/employer/get_claimid_on_member_id",
		        type: "POST",
		        data:{member_id:member_id,policy_no:policy_no},
		        success: function (response) {
		        	console.log(response);
		        	var data_res = JSON.parse(response);
		        	 $('#claim_id_drop').empty();
		        	 $('#claim_id_drop').append('<option value=""> Select claim Id</option>');
		        	   $.each(data_res, function (index, value) {
		        	   	$('#claim_id_drop').append('<option class="" value="' + value.claim_reimb_id  + '">' + value.claim_reimb_id + '</option>');
					 });
					}
		        });
	   });
	   // on claim_id get claim_data
	    $("#claim_id_drop").change(function(){
	    	var claim_id = $(this).val();
	    	 $.ajax({
		        url: "/employer/get_dates_on_claim_id",
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
		        	   		}
					 });
					}
		        });
	    	 	$.ajax({
		        url: "/employer/get_datesdocs_on_claim_id",
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
		        url: "/employer/get_dates_on_claim_id",
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
		        url: "/employer/get_datesdocs_on_claim_id",
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
	    })
	     // localStorage.clear();
		  var shashi_set = localStorage.getItem("shashi_set");
		if (shashi_set == 'no') 
		{
			var claim_id1 = localStorage.getItem("claim_id");
          $.ajax({
                url: "/employer/get_dates_on_claim_id",
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
                url: "/employer/get_datesdocs_on_claim_id",
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