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
			  		if (value['policy_sub_type_id'] == 1) 
			  		{
			  			$('#policy_no').append('<option selected value="' + value['policy_no'] + '">' + value['policy_sub_type_name']  + '</option>');

			  		}
			  		// $('#policy_no').append('<option class="active" value="' + value['policy_no'] + '">' + value['policy_sub_type_name']  + '</option>');
				  }
			  });
				 	$.ajax({
			type: 'POST',
			url: '/employee/get_family_membername_from_policy_no',
			data : {policy_no:$('#policy_no option:selected').val()},
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
	// family member name
	// $("#policy_no").change(function(){
		// $('#policy_no').css('pointer-events','auto');
		// var options = $('#policy_no option:selected').attr('class');
		// if (options == "inactive") 
		// {
		// 	$('#form_id').css('pointer-events','none');
		// }
		// else
		// {
		// 	$('#form_id').css('pointer-events','auto');
		// 	var value = 1;
		// $.ajax({
		// 	type: 'POST',
		// 	url: '/employee/get_family_membername_from_policy_no',
		// 	data : {policy_no:value},
		// 	success:function(res){
		// 		var data_res = JSON.parse(res);
		// 		 $('#patient_name').empty();
		// 			$('#patient_name').append('<option value=""> Select patient name</option>');
		// 		   $.each(data_res, function (index, value) {
		// 		  		$('#patient_name').append('<option data-rel="' + value.family_id + '" value="'+ value.emp_member_id +'">' + value.name + '</option>');
		// 		    });
		// 	}
		// });
		// }
	 // });

	$("#patient_name").on('change',function(){
		 var patient_id=$(this).val();
    	$.ajax({
    		type: 'POST',
			url: '/get_member_details',
			data : {patient_id:patient_id},
			success:function(response){
				var data_res = JSON.parse(response);
				 $.each(data_res, function (index, value) {
				 		$("#mob_no").val(value.mob_no);
						$("#email").val(value.email);
				 });
			}
    	});
	});
	
	$('.claim_Amount').keyup(function(e) {
        var $th = $(this);
        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^0-9]/g, function(str) { return ''; } ) );
        }return;
    });

$("#doctor_name").keyup(function(e) {
		var $th = $(this);
        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^A-Z a-z\s]/g, function(str) { return ''; } ) );
        }return;
   }); 	// finally submit 
  $("#patient_name").keyup(function(e) {
		var $th = $(this);
        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^A-Z a-z\s]/g, function(str) { return ''; } ) );
        }return;
   });

  $("#hospital_id").keyup(function(e) {
		var $th = $(this);
        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^A-Z a-z\s]/g, function(str) { return ''; } ) );
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

$('#mob_no').keyup(function(e) {
        var $th = $(this);
        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^0-9]/g, function(str) { return ''; } ) );
        }return;
    });
	 $("#admitted_for").keyup(function(e) {
		var $th = $(this);
        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^A-Z a-z,\s]/g, function(str) { return ''; } ) );
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
   	remark: {
      required: true
    },
	planned_date: {
      required: true
    },
	mob_no: {
      required: true,
      valid_mobile:true
    },
    admitted_for: {
      required: true
    },
    claim_Amount: {
      required: true,
      number: true
    },
    hospital_id:{
        required: true
    }
  },
  messages: {
    patient_name: "Please specify patient name",
	email: "Please specify email id",
	email: "please enter valid email",
	remark: "Please specify remark",
	planned_date: "Please specify admission date",
	mob_no: "Please specify mobile no",
	admitted_for: "Please specify admitted for",
	claim_Amount: "Please specify claim amount",
        hospital_id: "Please specify hospital name"
  },
  submitHandler: function(form) {
  	var all_data = $("#form_id").serialize() +'&data_override='+'NO';
  			$.ajax({
			type:'POST',
			url: '/intimate_claims_add',
			data:all_data,
			success:function(result){
				var data_response = JSON.parse(result);
				if (data_response.success) {
					$("#getCodeModal").modal("toggle");
					$("#getCode").html(data_response.success);
					setTimeout("location.reload(true);", 1000);
				}
				else if(data_response.success_new_claim)
				{
					var checkstr =  confirm(data_response.success_new_claim);
					if(checkstr == true)
					{
						var all_data = $("#form_id").serialize() +'&data_override='+'YES';
						$.ajax({
								type:'POST',
								url: 'intimate_claims_add',
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
						//window.location.reload(true);
					}
				}
				else if(data_response.success_new_date_range)
				{
						$("#getCodeModal").modal("toggle");
						$("#getCode").html(data_response.success_new_date_range);
					//window.location.reload(true);
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
  		//window.location.reload();
  });
$(".cancelled").click(function(){
  		window.location.reload();
  });


})