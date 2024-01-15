$(document).ready(function(){
	$.ajax({
		 url: "/broker/get_all_employer",
         type: "POST",
         dataType: "json",
         success: function (response) {
         	 $('#employer_name').empty();
         	  $('#employer_id').empty();
         	 $('#employer_name').append('<option value=""> Select Employer name</option>');
         	  $('#employer_id').append('<option value=""> Select Employer name</option>');
         	for (i = 0; i < response.length; i++) { 
         		$('#employer_name').append('<option value="'+ response[i].company_id +'">' + response[i].comapny_name+ '</option>');
         	  	$('#employer_id').append('<option value="'+ response[i].company_id +'">' + response[i].comapny_name+ '</option>');
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
         		 $('#policy_numbers').empty();
	         	 $('#policy_numbers').append('<option value=""> Select Policy</option>');
	         	for (i = 0; i < response.length; i++) { 
	         	  	$('#policy_numbers').append('<option value="'+ response[i].policy_no +'">' + response[i].policy_sub_type_name+ '</option>');
				}
         	}
		});
	});

	$("#employer_id").change(function(){
		var company_id = $(this).val();
		$.ajax({
			url: "/broker/get_policy_type",
         	type: "POST",
         	data :{company_id:company_id},
         	dataType: "json",
         	success: function (response) {
         		 $('#policy_no').empty();
	         	 $('#policy_no').append('<option value=""> Select Policy</option>');
	         	for (i = 0; i < response.length; i++) { 
	         	  	$('#policy_no').append('<option value="'+ response[i].policy_no +'">' + response[i].policy_sub_type_name+ '</option>');
				}
         	}
		});
	});

	// change on policy no get employee name 
	$("#policy_numbers").change(function(){
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
	         	  	$("#emp_member_id").val(response[i].emp_member_id);
				}
         	}
		});
	});

	$("#policy_no").change(function(){
		var policy_no = $(this).val();
		$.ajax({
			url: "/broker/get_employee_from_policy",
         	type: "POST",
         	data :{policy_no:policy_no},
         	dataType: "json",
         	success: function (response) {
         		$('#emp_id').empty();
	         	 $('#emp_id').append('<option value=""> Select Employee Name</option>');
	         	for (i = 0; i < response.length; i++) { 
	         	  	$('#emp_id').append('<option value="'+ response[i].id +'">' + response[i].name+ '</option>');
	         	  	$("#emp_member_id1").val(response[i].emp_member_id);
				}
         	}
		});
	});

	// change on empname get membername
	$("#emp_name").change(function(){
		var policy_no = $("#policy_numbers").val();
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

	$("#emp_id").change(function(){
		var policy_no = $("#policy_no").val();
      	var emp_id = $("#emp_id").val();
      	var company_id = $("#employer_id").val();
      	$.ajax({
         url: "/broker/get_family_membername_from_policy_no",
         type: "POST",
         data : {emp_id:emp_id,policy_no:policy_no,company_id:company_id},
         dataType: "json",
         success: function (response) {
             $('#member_id').empty();
                  $('#member_id').append('<option value=""> Select patient name</option>');
                  $.each(response, function (index, value) {
                  $('#member_id').append('<option data-rel="' + value.family_id + '" value="'+ value.emp_member_id +'">' + value.name + '</option>');
               });
         }
      });
	});

	$('#submit_data').click(function(){
		if ($('#employer_name').val() == '') 
	 	{
	 		return false;
	 	}
	 	else
	 	{
	 		var form_data = $("#form_data").serialize();
	 		$.ajax({
	 			type: 'POST',
				url: '/broker/get_all_claim',
				data : form_data,
				success:function(res){
					var data_res = 	JSON.parse(res);
					$("table #first_table").empty();
						 $.each(data_res, function (index, value) {
						 	var dateTime = value.created_at;
							var parts = dateTime.split(/[- :]/);
							var wanted = `${parts[2]}-${parts[1]}-${parts[0]}`;
		            	 	 $("table #first_table").append('<tr style="text-align: center;"><td><span class=" color-1 bold-13">'+value.name+'</span></td><td><span class=" color-1 bold-13">'+value.claim_reimb_id+'</span></td><td><span class=" color-1 bold-13">'+wanted+'</span></td><td><span class=" color-1 bold-13">Reimbursement</span></td><td><span class=" color-1 bold-13">'+value.claim_reimb_reason+'</span></td><td><span class=" color-1 bold-13"><i class="fa fa-inr"></i>'+value.total_claim_amount+'</span></td> <td><span class=" color-1 bold-13"><i class="fa fa-inr"></i>'+value.total_claim_amount+'</span></td> <td><input type="button" class="btn sahil_btn" data-id="'+value.claim_reimb_id+'" value="track claim" /></td></tr>');
						 });
                                                 $("#all_first_data").val(res);
				}
	 		});
	 	}
	});

	$('body').on('click', '.sahil_btn', function() {
				  	var claim_reimb_id = $(this).attr('data-id');
				  	var storage = window["localStorage"];
           			storage.setItem('claim_id',claim_reimb_id);
                    storage.setItem('shashi_set','no');
           			 window.location = "/broker_track_claim";
		});

	$("#submit_data_intimate").click(function(){
	 	if ($('#employer_id').val() == '') 
	 	{
	 		return false;
	 	}
	 	else
	 	{
	 		var form_data1 = $("#form_data_intimate").serialize();
	 		$.ajax({
				type: 'POST',
				url: '/broker/get_all_intimate_claim',
				data : form_data1,
				success:function(response){
					var data_res = 	JSON.parse(response);
				$("table #second_table").empty();
				$.each(data_res, function (index, value) {
            	 	var dateTime = value.created_date;
					var parts = dateTime.split(/[- :]/);
					var wanted = `${parts[2]}-${parts[1]}-${parts[0]}`;
            	 	if (value.claim_type == 'reimbursement') 
            	 	{
            	 		 $("table #second_table").append('<tr style="text-align: center;"><td><span class=" color-1 bold-13">'+value.name+'</span></td><td><span class=" color-1 bold-13">'+value.claim_intimate_id+'</span></td><td><span class=" color-1 bold-13">'+wanted+'</span></td><td><span class=" color-1 bold-13">'+value.claim_type+'</span></td><td><span class=" color-1 bold-13">'+value.reason+'</span></td><td><span class=" color-1 bold-13"><i class="fa fa-inr"></i>'+value.claim_Amount+'</span></td></tr>');
            	 	}
            	 	else
            	 	{
            	 		 $("table #second_table").append('<tr style="text-align: center;"><td><span class=" color-1 bold-13">'+value.name+'</span></td><td><span class=" color-1 bold-13">'+value.claim_intimate_id+'</span></td><td><span class=" color-1 bold-13">'+wanted+'</span></td><td><span class=" color-1 bold-13">'+value.claim_type+'</span></td><td><span class=" color-1 bold-13">'+value.reason+'</span></td><td><span class=" color-1 bold-13"><i class="fa fa-inr"></i>0</span></td></tr>');
            	 	}
            	 });
                  $("#all_second_data").val(response);
				}
			});
	 	}
	});
//	 $("#export_data").click(function(){
//	 	 var htmltable= document.getElementById('first_tab');
//	 	 var html = htmltable.outerHTML;
//       		 window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
//	 });
//	  $("#export_data_sec").click(function(){
//	 	 var htmltable_second= document.getElementById('second_tab');
//	 	 var html = htmltable_second.outerHTML;
//       		 window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
//	 });
        $("#export_submit").click(function(){
		 window.location = '/broker/excel_export/'+$('#all_first_data').val();
	 });
	 
	 $("#export_intimate").click(function(){
		 window.location = '/broker/excel_intimate_export/'+$('#all_second_data').val();
	 })
})