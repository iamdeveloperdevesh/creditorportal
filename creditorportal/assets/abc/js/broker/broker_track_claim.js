$(document).ready(function(){
	$.ajax({
		 url: "/broker/get_all_employer",
         type: "POST",
         dataType: "json",
         success: function (response) {
         	 $('#employer_name').empty();
         	 $('#employer_name').append('<option value=""> Select Employer name</option>');
         	for (i = 0; i < response.length; i++) { 
         	  	$('#employer_name').append('<option value="'+ response[i].company_id +'">' + response[i].comapny_name+ '</option>');
			}
         }
	});

	// change on employer_name get policy type
	$("#employer_name").change(function(){
		var company_id = $(this).val();
		$.ajax({
			url: "/broker/get_policy_type",
         	type: "POST",
         	data :{company_id:company_id},
         	dataType: "json",
         	success: function (response) {
         		 $('#policy_type').empty();
	         	 $('#policy_type').append('<option value=""> Select Policy Type</option>');
	         	for (i = 0; i < response.length; i++) { 
	         	  	$('#policy_type').append('<option value="'+ response[i].policy_detail_id +'">' + response[i].policy_sub_type_name+ '</option>');
				}
         	}
		});
	});

	// change on policy type get policy no
	$("#policy_type").change(function(){
		var policy_type = $(this).val();
		$.ajax({
			url: "/broker/get_policy_no",
         	type: "POST",
         	data :{policy_type:policy_type},
         	dataType: "json",
         	success: function (response) {
         		 $('#policy_no').empty();
	         	 $('#policy_no').append('<option value=""> Select Policy #</option>');
	         	for (i = 0; i < response.length; i++) { 
	         	  	$('#policy_no').append('<option value="'+ response[i].policy_no +'">' + response[i].policy_no+ '</option>');
				}
         	}
		});
	});

	// change on policy no get employee name 
	$("#policy_no").change(function(){
		var policy_no = $(this).val();
		$.ajax({
			url: "/broker/get_employee_from_policy",
         	type: "POST",
         	data :{policy_no:policy_no},
         	dataType: "json",
         	success: function (response) {
         		$('#emp_name').empty();
	         	 $('#emp_name').append('<option value=""> Select Employee Name</option>');
	         	for (i = 0; i < response.length; i++) { 
	         	  	$('#emp_name').append('<option value="'+ response[i].id +'">' + response[i].name+ '</option>');
				}
         	}
		});
	});

	// change on empname get membername
	$("#emp_name").change(function(){
		var policy_no = $("#policy_no").val();
      	var emp_id = $("#emp_name").val();
      	var company_id = $("#employer_name").val();
      	$.ajax({
         url: "/broker/get_family_membername_from_policy_no",
         type: "POST",
         data : {emp_id:emp_id,policy_no:policy_no,company_id:company_id},
         dataType: "json",
         success: function (response) {
             $('#member_name').empty();
                  $('#member_name').append('<option value=""> Select patient name</option>');
                  $.each(response, function (index, value) {
                  $('#member_name').append('<option data-rel="' + value.family_id + '" value="'+ value.emp_member_id +'">' + value.name + '</option>');
               });
         }
      });
	});

	// change on empname get membername
	$("#member_name").change(function(){
		var policy_no = $('#policy_no').val();
	   	var member_id = $(this).val();
	   	$.ajax({
	   	 url: "/broker/get_claimid_on_member_id",
         type: "POST",
         data : {policy_no:policy_no,member_id:member_id},
         dataType: "json",
         success: function (response) {
         	$('#claim_id_drop').empty();
		    $('#claim_id_drop').append('<option value=""> Select claim Id</option>');
		    $.each(response, function (index, value) {
		        $('#claim_id_drop').append('<option class="" value="' + value.claim_reimb_id  + '">' + value.claim_reimb_id + '</option>');
			});
         }
	   	});
	});

	 // on claim_id get claim_data
	    $("#claim_id_drop").change(function(){
	    	var claim_id = $(this).val();
	    	 $.ajax({
		        url: "/broker/get_dates_on_claim_id",
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
		        url: "/broker/get_datesdocs_on_claim_id",
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

	      $("#search_hopsitals_search").click(function(){
	      	var claim_id = $("#claim_id").val();
	      	 $.ajax({
		        url: "/broker/get_dates_on_claim_id",
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
		        url: "/broker/get_datesdocs_on_claim_id",
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
                url: "/broker/get_dates_on_claim_id",
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
                url: "/broker/get_datesdocs_on_claim_id",
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