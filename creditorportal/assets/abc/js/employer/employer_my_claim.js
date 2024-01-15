$(document).ready(function(){
	 $.ajax({
                url: "/employer/get_all_policy_numbers",
                type: "POST",
                async: false,
                dataType: "json",
                 data : { employer : "true"},
                success: function (response) {
					 $('#policy_numbers').empty();
					 $('#policy_numbers').append('<option value=""> Select policy type</option>');
					 $('#policy_no').empty();
					 $('#policy_no').append('<option value=""> Select policy type</option>');
                     for (i = 0; i < response.length; i++) { 
						if (response[i].policy_sub_type_name == "Group Mediclaim") 
                        {
                        			var date = response[i].end_date.split("-");
						var date = new Date(Number(date[0]), Number(date[1])-1, Number(date[2]));
						var current_date = new Date();
							if(date > current_date){
							 $('#policy_numbers').append('<option value="' + response[i].policy_no + '">' + response[i].policy_sub_type_name + '</option>');
							$('#policy_no').append('<option value="' + response[i].policy_no + '">' + response[i].policy_sub_type_name + '</option>');

							} 
                        }

                        if (response[i].policy_sub_type_name == "Mediclaim Top-Up") 
                        {
                        	var date = response[i].end_date.split("-");
							var date = new Date(Number(date[0]), Number(date[1])-1, Number(date[2]));
							var current_date = new Date();
							if(date > current_date){
					 			$('#policy_numbers').append('<option value="' + response[i].policy_no + '">' + response[i].policy_sub_type_name + '</option>');
						    	$('#policy_no').append('<option value="' + response[i].policy_no + '">' + response[i].policy_sub_type_name + '</option>');

						    } 
                        }

                        if (response[i].policy_sub_type_name == "Group Personal Accident") 
                        {
                        	var date = response[i].end_date.split("-");
							var date = new Date(Number(date[0]), Number(date[1])-1, Number(date[2]));
							var current_date = new Date();
							if(date > current_date){
					 			$('#policy_numbers').append('<option value="' + response[i].policy_no + '">' + response[i].policy_sub_type_name + '</option>');
						    	$('#policy_no').append('<option value="' + response[i].policy_no + '">' + response[i].policy_sub_type_name + '</option>');
								} 
                        }

                        if (response[i].policy_sub_type_name == "Personal Accident Top-Up") 
                        {
                        	var date = response[i].end_date.split("-");
							var date = new Date(Number(date[0]), Number(date[1])-1, Number(date[2]));
							var current_date = new Date();
							if(date > current_date){
					 			$('#policy_no').append('<option value="' + response[i].policy_no + '">' + response[i].policy_sub_type_name + '</option>');
								$('#policy_numbers').append('<option value="' + response[i].policy_no + '">' + response[i].policy_sub_type_name + '</option>');
						    } 
                        }
					}
				}
            });

	 $("#policy_numbers").change(function(){
	 		var policy_no = $(this).val();
	 		$.ajax({
	        url: "/employer/get_employee_details_from_policy_no",
	        type: "POST",
	        data:{policy_no:policy_no},
	        success: function (response) {
        	var data = JSON.parse(response);
        	  $('#emp_name').empty();
        	  $('#emp_name').append('<option value=""> Select Employee Name</option>');
        	   $.each(data, function (index, value) {
        	   	$('#emp_name').append('<option data-value="'+ value.policy_member_id +'" value="' + value.emp_id  + '">' + value.emp_firstname +' '+value.emp_lastname+ '</option>');
        	  });

			  }
        });

	 });

	 	 $("#policy_no").change(function(){
	 		var policy_no = $(this).val();
	 		$.ajax({
	        url: "/employer/get_employee_details_from_policy_no",
	        type: "POST",
	        data:{policy_no:policy_no},
	        success: function (response) {
        	var data = JSON.parse(response);
    //     	  $('#emp_id').empty();
        	  $('#emp_id').append('<option value=""> Select Employee Name</option>');
        	   $.each(data, function (index, value) {
        	   	$('#emp_id').append('<option data-value="'+ value.policy_member_id +'" value="' + value.emp_id  + '">' + value.emp_firstname +' '+value.emp_lastname+ '</option>');
        	  });

			  }
        });

	 });

	 $("#emp_name").change(function(){
	 	$("#emp_member_id").val($(this).find(':selected').attr('data-value'));
	 	var policy_no = $('#policy_numbers').val();
	 	var emp_id = $(this).val();
	 	$.ajax({
				type: 'POST',
				url: '/employer/get_family_membername_from_policy_no',
				data : {policy_no:policy_no,emp_id:emp_id},
				success:function(res){
					var data_res = JSON.parse(res);
					 $('#members').empty();
						$('#members').append('<option value=""> Select member name</option>');
						$.each(data_res, function (index, value) {
					  		$('#members').append('<option data-rel="' + value.family_id + '" value="'+ value.emp_member_id +'">' + value.name + '</option>');
					  		});
				}
			}); 
	 });

	$("#emp_id").change(function(){
	 	$("#emp_member_id").val($(this).find(':selected').attr('data-value'));
	 	var policy_no = $('#policy_no').val();
	 	var emp_id = $(this).val();
	 	$.ajax({
				type: 'POST',
				url: '/employer/get_family_membername_from_policy_no',
				data : {policy_no:policy_no,emp_id:emp_id},
				success:function(res){
					var data_res = JSON.parse(res);
					 $('#member_id').empty();
						$('#member_id').append('<option value=""> Select member name</option>');
						$.each(data_res, function (index, value) {
					  		$('#member_id').append('<option data-rel="' + value.family_id + '" value="'+ value.emp_member_id +'">' + value.name + '</option>');

					  		});
				}
			}); 
	 });
	 $('#submit_data').click(function(){
	 	if ($('#policy_numbers').val() == '') 
	 	{
	 		return false;
	 	}
	 	else
	 	{
	 			var form_data = $("#form_data").serialize();
			 	$.ajax({
						type: 'POST',
						url: '/employer/get_all_claim',
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
           			 window.location = "/employer/employer_track_claim";
		});

	 $("#submit_data_intimate").click(function(){
	 	if ($('#policy_no').val() == '') 
	 	{
	 		return false;
	 	}
	 	else
	 	{
	 		var form_data1 = $("#form_data_intimate").serialize();
	 		$.ajax({
				type: 'POST',
				url: '/employer/get_all_intimate_claim',
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
	 
	 $("#export_submit").click(function(){
		 //console.log('/employer/excel_export/'+$('#all_first_data').val();
		 window.location = '/employer/excel_export/'+$('#all_first_data').val();
	 });
	 
	 $("#export_intimate").click(function(){
		 window.location = '/employer/excel_intimate_export/'+$('#all_second_data').val();
	 })
	 // $("#export_data").click(function(){
	 // 	 var htmltable= document.getElementById('first_tab');
	 // 	 var html = htmltable.outerHTML;
	 // 	 $.ajax({
	 // 	 	url:'',
	 // 	 	data:{data:html},
	 // 	 	type: 'POST',
	 // 	 	success:function(response){
	 // 	 		console.log()
	 // 	 	}
	 // 	 })
  //      		 // window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
	 // });
	 //  $("#export_data_sec").click(function(){
	 // 	 var htmltable_second= document.getElementById('second_tab');
	 // 	 var html = htmltable_second.outerHTML;
  //      		 window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
	 // });
	  $('#submit_claim_data').click(function(){
	 	if ($('#policy_numbers').val() == '') 
	 	{
	 		return false;
	 	}
	 	else
	 	{
	 			var form_data = $("#form_claim_data").serialize();
			 	$.ajax({
						type: 'POST',
						url: '/employer/get_all_edit_claim',
						data : form_data,
						success:function(res){
						var data_res = 	JSON.parse(res);
						console.log(data_res);
						// $("table #first_table").empty();
						//  $.each(data_res, function (index, value) {
						//  	var dateTime = value.created_at;
						// 	var parts = dateTime.split(/[- :]/);
						// 	var wanted = `${parts[2]}-${parts[1]}-${parts[0]}`;
		    //         	 	 $("table #first_table").append('<tr style="text-align: center;"><td><span class=" color-1 bold-13">'+value.name+'</span></td><td><span class=" color-1 bold-13">'+value.claim_reimb_id+'</span></td><td><span class=" color-1 bold-13">'+wanted+'</span></td><td><span class=" color-1 bold-13">Reimbursement</span></td><td><span class=" color-1 bold-13">'+value.claim_reimb_reason+'</span></td><td><span class=" color-1 bold-13"><i class="fa fa-inr"></i>'+value.total_claim_amount+'</span></td> <td><span class=" color-1 bold-13"><i class="fa fa-inr"></i>'+value.total_claim_amount+'</span></td> <td><input type="button" class="btn sahil_btn" data-id="'+value.claim_reimb_id+'" value="track claim" /></td></tr>');
						//  });
						//  $("#all_first_data").val(res);
						}
					});
	 	}
	 });
})