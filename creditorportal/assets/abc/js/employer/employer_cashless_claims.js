$(document).ready(function(){
	var d = new Date();
var strDate = d.getDate()+ "-" + (d.getMonth()+1) + "-"  +d.getFullYear();
var date = strDate.split("-");
var display_date = date[1]+'-'+date[0]+'-'+date[2];
$("#planned_date").datepicker({
		changeMonth: true,
	    changeYear: true,
	    showOtherMonths: true,
	    selectOtherMonths: true,
	    dateFormat: "dd-mm-yy",
	    minDate: new Date(display_date)
	 });
$('body').on('focus',"#discharge_date", function(){
	var data = $("#planned_date").val();
	var date = data.split("-");
	var display_date1 = date[1]+'-'+date[0]+'-'+date[2];
	$(this).datepicker({
	 changeMonth: true,
    changeYear: true,
    showOtherMonths: true,
    selectOtherMonths: true,
     dateFormat: "dd-mm-yy",
     minDate: new Date(display_date1)
	 });
});
/* $('body').on('focus',"#discharge_date", function(){
	var data = $("#planned_date").val();
	var date = data.split("-");
	var display_date = date[1]+'-'+date[0]+'-'+date[2];
	$(this).datepicker({
	 changeMonth: true,
    changeYear: true,
    showOtherMonths: true,
    selectOtherMonths: true,
     dateFormat: "dd-mm-yy",
     minDate: new Date(display_date)
	 });
}); */
$("#emp_name").change(function(){
		var emp_id = $('#emp_name').val();
		get_policy(emp_id);
	});

	$("#emp_id").change(function(){
		var emp_id = $('#emp_id').val();
		get_policy(emp_id);
	});

	$.ajax({
        url: "/employer/get_all_policy_numbers",
        type: "POST",
        dataType: "json",
          data : { employer : "true"},
        success: function (response) {
        	 $('#policy_no').empty();
			 $('#policy_no').append('<option value=""> Select policy type</option>');
				$.each(response, function (index, value) {
			  	var date = value['end_date'].split("-");
			  	var date = new Date(Number(date[0]), Number(date[1])-1, Number(date[2]));
			  	var current_date = new Date();
			  	if(date > current_date){
			  		if (value['policy_sub_type_name'] == 'Group Mediclaim') 
			  		{
			  			$('#policy_no').append('<option selected class="active" value="' + value['policy_no'] + '">' + value['policy_sub_type_name']  + '</option>');

			  		}
			  		// $('#policy_no').append('<option class="active" value="' + value['policy_no'] + '">' + value['policy_sub_type_name']  + '</option>');
			  	}
			  });
        }
    });

    $.ajax({
        url: "/get_emp_details",
        type: "POST",
        dataType: "json",
        success: function (response) {
        	 $('#emp_name').empty();
        	  $('#emp_id').empty();
        	  	$('#emp_name').append('<option value=""> Select Employee Name</option>');
        	  		$('#emp_id').append('<option value=""> Select Employee Id</option>');
        	   $.each(response, function (index, value) {
        	   	$('#emp_name').append('<option class="em_name" value="' + value.emp_id  + '">' + value.emp_firstname + '</option>');
        	   	$('#emp_id').append('<option class="em_id" value="' + value.emp_id  + '">' + value.emp_id  + '</option>');
				});
			}
        });

  //    $("#emp_name").change(function(){
		// $('#policy_no').css('pointer-events','auto');
		// // var options = $('#policy_no option:selected').attr('class');
		// // if (options == "inactive") 
		// // {
		// // 	$('#form_id').css('pointer-events','none');
		// // }
		// // else
		// // {
		// 	$('#form_id').css('pointer-events','auto');
		// 	var value = 1;
			
		// 	var emp_id = $('#emp_name').val();
		// 	$.ajax({
		// 		type: 'POST',
		// 		url: '/employer/get_family_membername_from_policy_no',
		// 		data : {policy_no:value,emp_id:emp_id},
		// 		success:function(res){
		// 			var data_res = JSON.parse(res);
		// 			 $('#patient_name').empty();
		// 				$('#patient_name').append('<option value=""> Select patient name</option>');
		// 			   $.each(data_res, function (index, value) {
		// 			  		$('#patient_name').append('<option data-rel="' + value.family_id + '" value="'+ value.emp_member_id +'">' + value.name + '</option>');
		// 				});
		// 		}
		// 	});
		// // }
	 // });
	  
	  $("#patient_name").on('change',function(){
		 var patient_id=$(this).val();
		 if($('#emp_id').val())
			{
				var emp_id = $('#emp_id').val();
			}
			else
			{
				var emp_id = $('#emp_name').val();
			}
    	$.ajax({
    		type: 'POST',
			url: '/get_emp_member_details',
			data : {patient_id:patient_id,emp_id:emp_id},
			success:function(response){
				var data_res = JSON.parse(response);
				 $.each(data_res, function (index, value) {
				 		$("#mob_no").val(value.mob_no);
						$("#email").val(value.email);
				 });
			}
    	});
	});

	// $("#policy_no").change(function(){
		var value = $(this).val();
		$.ajax({
        	url: "/employer/get_all_states",
            type: "POST",
            async: false,
            dataType: "json",
            success: function (response) {
            	$('#state_names').empty();
            	$('#state_names').append('<option value="">select</option>');
            for (i = 0; i < response.length; i++) { 
				$('#state_names').append('<option value="' + response[i].state_id + '">' + response[i].state_name + '</option>');
				}
                }
            });
	// });

	$('#state_names').on('change', function() {
			    $.ajax({
			        url: "/employer/get_city_from_states",
			        type: "POST",
			        data: {
			                    "state_names": this.value
			        },
			        async: false,
			        dataType: "json",
			        success: function (response) {

						$('#cities').empty();
						$('#cities').append('<option>Select City</option>');
						for (i = 0; i < response.length; i++) { 

						$('#cities').append('<option value="' + response[i].city_id + '">' + response[i].city_name + '</option>');
						   }
						} 
			}); 
		})

	$("#cities").change(function(){
		var policy_no = $('#policy_no').val();
		var state_name = $('#state_names option:selected').text();
		var city_names = $('#cities option:selected').text();
		$.ajax({
			type: 'POST',
			url: '/get_hospital_name',
			data : {policy_no:policy_no,state_names:state_name,city_names:city_names},
			success:function(res){
				var res_data = JSON.parse(res);
				$('#hospital_name').empty();
				$('#hospital_name').append('<option data-rel="" value="">selected</option>');
				$.each(res_data, function (index, value) {
				        $('#hospital_name').append('<option data-insureid="'+value['network_hospital_id']+'" value="'+value['network_hospital_id']+'">'+value['HOSPITAL_NAME']+'</option>');
				    });
			}
		});
	});

	  $(".doctor_name").keyup(function(e) {
		var $th = $(this);
        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^A-Za-z\s]/g, function(str) { return ''; } ) );
        }return;
   });
	    $('#mob_no').keyup(function(e) {
        var $th = $(this);
        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^0-9]/g, function(str) { return ''; } ) );
        }return;
    });
	 $("#reason").keyup(function(e) {
		var $th = $(this);
        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^A-Z a-z,\s]/g, function(str) { return ''; } ) );
        }return;
   });
	    $.validator.addMethod('valid_mobile', function(value, element, param) {
    var re = new RegExp('^[7-9][0-9]{9}$');
    return this.optional(element) || re.test( value ); // Compare with regular expression
},'Enter a valid 10 digit mobile number');
	     $.validator.addMethod('validateEmail', function(value, element, param) {
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        return reg.test( value ); // Compare with regular expression
    },'Please enter a valid email address.');
	      $('body').on("keyup","#file_no",function(e) {  
var $th = $(this);

        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^A-Za-z- ^0-9]/g, function(str) { return ''; } ) );
        }return;
   });

	  $("#form_id").validate({
  rules: {
    patient_name: {
      required: true
    },
	email: {
      required: true,
	    email: true,
	    validateEmail:true
    },
	planned_date: {
      required: true
    },
	mob_no: {
      required: true,
      valid_mobile:true
    },
    reason: {
      required: true
    },
    file_no: {
      required: true
    },
    state_names: {
      required: true
    },
    cities:{
    	 required: true
    },
    reason: {
      required: true
    },
    hospital_name:{
        required: true
    }
  },
  messages: {
    patient_name: "Please specify patient name",
	email: "Please specify email id",
	email: "please enter valid email",
	state_names:"please enter specify state name",
	cities : "please enter specify city name",
	file_no: "Please specify file no",
	planned_date: "Please specify admission date",
	mob_no: "Please enter valid mobile number",
	reason: "Please specify admitted for",
	reason:"Please specify reason",
        hospital_name:"Please specify hospital name"
  },

    submitHandler: function(form) {
  		var all_data = $("#form_id").serialize() +'&data_override='+'NO';
		$.ajax({
			type:'POST',
			url: '/employer_cashless_claims_add',
			data:all_data,
			success:function(result){
				var data_response = JSON.parse(result);
				if (data_response.success) {
					$("#getCodeModal").modal("toggle");
					$("#getCode").html(data_response.success);
					// window.location.reload(true);
				}
				else if(data_response.success_new_claim)
				{
					var checkstr =  confirm(data_response.success_new_claim);
					if(checkstr == true)
					{
						var all_data = $("#form_id").serialize() +'&data_override='+'YES';
						$.ajax({
								type:'POST',
								url: 'employer_cashless_claims_add',
								data:all_data,
								success:function(result){
									var data_response = JSON.parse(result);
									$("#getCodeModal").modal("toggle");
									$("#getCode").html(data_response.success);
									setTimeout("location.reload(true);", 1000);
								}
							});
					}
					else
					{
						setTimeout("location.reload(true);", 1000);
					}
				}
				else if(data_response.success_new_date_range)
				{
					$("#getCodeModal").modal("toggle");
					$("#getCode").html(data_response.success_new_date_range);
					setTimeout("location.reload(true);", 1000);
				}
				else
				{
					 $.each(data_response.messages, function(key, value){
					 	 var element = $('#' + key);
                            element.closest('div.data').find('p').remove();
                            element.after(value);
					 });
					// $("#getCodeModal").modal("toggle");
					// $("#getCode").html('Some Error Has Been Occured');
				}
			}
		});
  }
});
	$("#data").click(function(){
  		window.location.reload();
  });
	$("#cancelled").click(function(){
  		window.location.reload();
  });


    });
function get_policy(emp_id) {
	$.ajax({
        url: "/employer/get_all_policy_no",
        type: "POST",
        dataType: "json",
        data : {emp_id:emp_id},
        success: function (response) {
        	console.log(response);
        	 $('#policy_no').empty();
			 $('#policy_no').append('<option value=""> Select policy type</option>');
				$.each(response, function (index, value) {
			  	var date = value['end_date'].split("-");
			  	var date = new Date(Number(date[0]), Number(date[1])-1, Number(date[2]));
			  	var current_date = new Date();
			  	if(date > current_date){
			  		if (value['policy_sub_type_name'] == 'Group Mediclaim') 
			  		{
			  			$('#policy_no').append('<option selected value="' + value['policy_no'] + '">' + value['policy_sub_type_name']  + '</option>');

			  		}
			  		// $('#policy_no').append('<option class="active" value="' + value['policy_no'] + '">' + value['policy_sub_type_name']  + '</option>');
			  	}
			  	});
			  		$.ajax({
				type: 'POST',
				url: '/employer/get_family_membername_from_policy_no',
				data : {policy_no:$('#policy_no option:selected').val(),emp_id:emp_id},
				success:function(res){
					var data_res = JSON.parse(res);
					 $('#patient_name').empty();
						$('#patient_name').append('<option value=""> Select patient name</option>');
					   $.each(data_res, function (index, value) {
					  		$('#patient_name').append('<option data-rel="' + value.family_id + '" value="'+ value.emp_member_id +'">' + value.name + '</option>');
						});
				}
			});
        }
    });
}