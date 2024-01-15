$(document).ready(function(){

	/*if($('#payment_det_sec_accepted').val() == 1){
		$('#4').prop('checked', true);
		$("#4").attr("disabled",true);
	}

	if($('#autorenewal_sec_accepted').val() == 1){
		$('#2').prop('checked', true);
		$("#2").attr("disabled",true);
	}

	if($('#ghd_sec_accepted').val() == 1){
		$('#3').prop('checked', true);
		$("#3").attr("disabled",true);
	}

	if($('#hidden_payment_mode').val() == 'Pay U'){
		if($('#payment_det_sec_accepted').val() == 1 && $('#ghd_sec_accepted').val() == 1){
			$('.final_section').show();
		}else{
			$('.final_section').hide();
		}
	}else{
		if($("#hidden_auto_renew_flag").val() == "Y"){
			if($('#payment_det_sec_accepted').val() == 1 && $('#autorenewal_sec_accepted').val() == 1 && $('#ghd_sec_accepted').val() == 1){
				$('.final_section').show();
			}else{
				$('.final_section').hide();
			}
		}else{
			if($('#payment_det_sec_accepted').val() == 1 && $('#ghd_sec_accepted').val() == 1){
				$('.final_section').show();
			}else{
				$('.final_section').hide();
			}
		}
		
	}*/


	var url = window.location.href;
	var result= url.split('/');
	var emp_id = result[result.length-2];
	$.ajax({
		url: "/si_check_summarylink_status",
		type: "POST",
		data: {
		emp_id: emp_id,
		},
		async: false,
		dataType: "json",
		success: function (response) {	
			console.log(response);
			if(response.status == 'error'){			

				$("#myModal2").modal('show');
			}
			
		}
	});

	$("#close_info_modal").click(function(){
		$("#myModal2").modal('hide');
	})

	$(document).on("click","#accept_payment_sec",function(){
		if($('input[name=4]:checked').val() == undefined || $('input[name=4]:checked').val() == 'No'){
			$('#payment-sec-error').show();
		}else{
			$('#payment_det_sec_accepted').val(1);
			$('#payment-sec-error').hide();
			accept_reject("accept",$(this).data("section"));
			console.log("accept",$(this).data("section"));
			$("#4").attr("disabled",true);
			swal("Success","You have accepted to give consent that the information provided in payment detail section are true and correct in all aspect", "success")
			
		}
		
	})

	$(document).on("click","#accept_auto_renewal_section",function(){
		if($('input[name=3]:checked').val() == undefined || $('input[name=3]:checked').val() == 'No'){
			$('#autorenewal-sec-error').show();
		}else{
			$('#autorenewal_sec_accepted').val(1);
			$('#autorenewal-sec-error').hide();
			accept_reject("accept",$(this).data("section"));
			console.log("accept",$(this).data("section"));
			$("#3").attr("disabled",true);
			swal("Success","You have accepted to give consent that the information provided in auto renewal section are true and correct in all aspect", "success")
			
		}
		
	})

	$(document).on("click","#accept_ghd_section",function(){
		if($('input[name=2]:checked').val() == undefined || $('input[name=2]:checked').val() == 'No'){
			$('#ghd-sec-error').show();
		}else{
			$('#ghd_sec_accepted').val(1);
			$('#ghd-sec-error').hide();
			accept_reject("accept",$(this).data("section"));
			console.log("accept",$(this).data("section"));
			$("#2").attr("disabled",true);
			swal("Success","You have accepted to give consent that the information provided in Helath declaration section are true and correct in all aspect", "success")
			
		}
		
	})

	$(document).on("click",".final_reject",function(){
		swal({
			title: "Proposal Reject",
			text: "You have opted to reject the proposal details as per the information mentioned in the proposal, please note that the proposal will be rejected and further details will not be accessible to resubmit the proposal details",
			type: "warning",
			showCancelButton: true,
			confirmButtonText: "Ok!",
			closeOnConfirm: true,
			allowOutsideClick: false,
			closeOnClickOutside: false,
			closeOnEsc: false,
			dangerMode: true,
			allowEscapeKey: false
			},
			function () {
				$(".accept_otp_btn").hide();
				accept_reject("reject","Final_reject");
				
			});
	})

	$(document).on("click",".BtnReject",function(){
		var sec = $(this).data("section");
		swal({
			title: "",
			text: "You have opted to reject the proposal details as per the information mentioned in the proposal, please note that the proposal will be rejected and further details will not be accessible to resubmit the proposal details",//"Proposal Submitted Successfully",/*SI Process changing text*/
			type: "warning",
			showCancelButton: true,
			confirmButtonText: "Ok!",
			closeOnConfirm: true,
			allowOutsideClick: false,
			closeOnClickOutside: false,
			closeOnEsc: false,
			dangerMode: true,
			allowEscapeKey: false
			},
			function () {

				accept_reject("reject",sec);
				
			});
	})
})

function accept_reject(type,section){
	//debugger;
	var url = window.location.href;
	var result= url.split('/');
	var emp_id = result[result.length-2];
	$.ajax({
			url: "/si_accept_reject",
			type: "POST",
			data: {
				emp_id: emp_id,
				type: type,
				section: section
			},
			async: false,
			dataType: "json",
			success: function (response) {
				//debugger;
				/*if(type == "reject"){
					if(section == 'payment_details'){
						$('#payment_det_sec_accepted').val(0);
						$("#4").attr("disabled",false);
						$('#4').prop('checked', false);
					}else if(section == 'autorenewal_details'){
						$('#autorenewal_sec_accepted').val(0);
						$("#3").attr("disabled",false);
						$('#3').prop('checked', false);
					}else if(section == 'ghd_details'){
						$('#ghd_sec_accepted').val(0);
						$("#2").attr("disabled",false);
						$('#2').prop('checked', false);
					}
				}*/
			//alert(data);
				
				/*if($('#hidden_payment_mode').val() == 'Pay U'){
					if($('#payment_det_sec_accepted').val() == 1 && $('#ghd_sec_accepted').val() == 1){
						$('.final_section').show();
					}else{
						$('.final_section').hide();
					}
				}else{
					if($("#hidden_auto_renew_flag").val() == "Y"){
						if($('#payment_det_sec_accepted').val() == 1 && $('#autorenewal_sec_accepted').val() == 1 && $('#ghd_sec_accepted').val() == 1){
							$('.final_section').show();
						}else{
							$('.final_section').hide();
						}
					}else{
						if($('#payment_det_sec_accepted').val() == 1 && $('#ghd_sec_accepted').val() == 1){
							$('.final_section').show();
						}else{
							$('.final_section').hide();
						}
					}
					
				}*/
			}
		});
}

//captcha - update - upendra
function captcha_modal(){
	if($('input[name=role_types]:checked').val() == undefined){
		swal({
				title: "",
				text: "Please select declaration",
				type: "warning",
				showCancelButton: false,
				confirmButtonText: "Ok!",
				closeOnConfirm: true,
				allowOutsideClick: false,
				closeOnClickOutside: false,
				closeOnEsc: false,
				dangerMode: true,
				allowEscapeKey: false
				},
			function () {
				
			});
	}else{
	$("#myModal3").modal("show");
	}
}

//captcha - update - upendra
function check_captcha(){
	var captcha_string = $("#entered_captcha1").val();
	otp_generate_new(captcha_string);
}

	//   captcha update - upendra - 16-10-2021
	function refresh_captcha(){
		ajaxindicatorstart();
		$.ajax({
			url: "/refresh_captcha_bb_summary",
			type: "POST",
			data: { 
				 
			},
			async: true,
			dataType: "text",  
			  cache:false,
			success: function (response) {
				ajaxindicatorstop();
				// alert(response);
				$("#entered_captcha1").val('');
				$("#entered_captcha2").val('');
				$(".captcha_image_span").html(response);
			},
		  });
	  }
	
	  $(document).on("click", ".refresh_captcha", function () {
		refresh_captcha();
	  });

	  //captcha - update - add parameter

function otp_generate_new(captcha_string) {
	//debugger;
	if($('input[name=role_types]:checked').val() == undefined){
		swal({
				title: "",
				text: "Please select declaration",
				type: "warning",
				showCancelButton: false,
				confirmButtonText: "Ok!",
				closeOnConfirm: true,
				allowOutsideClick: false,
				closeOnClickOutside: false,
				closeOnEsc: false,
				dangerMode: true,
				allowEscapeKey: false
				},
			function () {
				
			});
	}else{
		//debugger; 
		//check all sections accepted or not
		FinalSubmit = 1;//0;
		/*if($('#hidden_payment_mode').val() == 'Pay U'){
			if($('#payment_det_sec_accepted').val() == 1 && $('#ghd_sec_accepted').val() == 1){
				FinalSubmit = 1;
			}else{
				FinalSubmit = 0;
				if($('#payment_det_sec_accepted').val() == 0){
					swal("Alert","Please accept Payment Detail section to proceed with the proposal form authentication process","warning");
					return false;
				}else{
					swal("Alert","Please accept Health declaration section to proceed with the proposal form authentication process","warning");
					return false
				}
			}
		}else{
			if($("#hidden_auto_renew_flag").val() == "Y"){
				if($('#payment_det_sec_accepted').val() == 1 && $('#autorenewal_sec_accepted').val() == 1 && $('#ghd_sec_accepted').val() == 1){
					FinalSubmit = 1;
				}else{
					FinalSubmit = 0;
					if($('#payment_det_sec_accepted').val() == 0){
						swal("Alert","Please accept Payment Detail section to proceed with the proposal form authentication process","warning");
						return false;
					}else if($('#ghd_sec_accepted').val() == 0){
						swal("Alert","Please accept Health declaration section to proceed with the proposal form authentication process","warning");
						return false
					}else if($('#autorenewal_sec_accepted').val() == 0){
						swal("Alert","Please accept Auto renewal section to proceed with the proposal form authentication process","warning");
						return false;
					}
				}
			}else{
				if($('#payment_det_sec_accepted').val() == 1 && $('#ghd_sec_accepted').val() == 1){
					FinalSubmit = 1;
				}else{
					FinalSubmit = 0;
					if($('#payment_det_sec_accepted').val() == 0){
						swal("Alert","Please accept Payment Detail section to proceed with the proposal form authentication process","warning");
						return false;
					}else{
						swal("Alert","Please accept Health declaration section to proceed with the proposal form authentication process","warning");
						return false
					}
				}
			}
			
		}*/
		if(FinalSubmit == 1){
			//first check GHD is yes or no
			//if($("input:radio.radios_out:checked").val() == 'No'){

				//captcha - update - upendra
				//$("#myModal1").modal("hide");

				$("#sms_body").html('');
				ajaxindicatorstop();
				var url = window.location.href;
				var result= url.split('/');
				var emp_id = result[result.length-2];

				$.ajax({
					url: "/send_otp",
					type: "POST",
					data: {
					emp_id: emp_id,
					captcha_string: captcha_string,
					},
					async: false,
					dataType: "json",
					success: function (response) {
					//captcha - update - upendra
					$("#myModal3").modal("hide");
					$("#pos_otp").val("");
					refresh_captcha();
					$("#pos_otp").val("");

					//captcha - update
					$("#myModal1").modal("show");
					},
					//captcha - update - upendra
					error: function(xhr,textStatus,err)
					{
						var response = JSON.parse(xhr.responseText);

						swal({
							title: "",
							text: response.message,
							type: "warning",
							showCancelButton: false,
							confirmButtonText: "Ok!",
							closeOnConfirm: true,
							allowOutsideClick: false,
							closeOnClickOutside: false,
							closeOnEsc: false,
							dangerMode: true,
							allowEscapeKey: false
							},
						function () {
							$("#entered_captcha1").val('');
							$("#entered_captcha2").val('');
							$("#pos_otp").val("");
						});
						
					}
				});
			/*}else{
				swal({
					title: "",
					text: "Dear Customer, Owing to the Good health declaration submitted, we can offer you to apply to our suitable Retail product, subject to Underwriting. Kindly get in touch with your RM for further details",
					type: "warning",
					showCancelButton: false,
					confirmButtonText: "Ok!",
					closeOnConfirm: true,
					allowOutsideClick: false,
					closeOnClickOutside: false,
					closeOnEsc: false,
					dangerMode: true,
					allowEscapeKey: false
					},
				function () {
					
				});
				//swal('Alert',"Dear Customer, Owing to the Good health declaration submitted, we can offer you to apply to our suitable Retail product, subject to Underwriting. Kindly get in touch with your RM for further details"); 
			}*/
		}
			
		
	}
	
	
}

function get_employee_lead_id() {
	var url = window.location.href;
	var result= url.split('/');
	var emp_id = result[result.length-2];
	var emp_id = $('#emp_id_encrypt').val();
    $("#emp_idDummyForm").val(emp_id);
    $("#dummyForm").submit();
}

$(document).on("click", "#validate_otp", function() {
	var url = window.location.href;
	var result= url.split('/');
	var emp_id = result[result.length-2];
	$.ajax({
	url: "/validate_otp",
	type: "POST",
	data: {
	otp: $("#pos_otp").val(),
	emp_id : emp_id,
	payment_mode : $('#hidden_payment_mode').val()
	},
	async: false,
	dataType: "json",
	success: function (response) {
	if(response.status == 'true'){
		swal({
		title: "Success",
		text: "Proposal Submitted Successfully",
		type: "warning",
		showCancelButton: false,
		confirmButtonText: "Ok!",
		closeOnConfirm: true,
		allowOutsideClick: false,
		closeOnClickOutside: false,
		closeOnEsc: false,
		dangerMode: true,
		allowEscapeKey: false
		},
		function () {
			/*si process hiding summary popup*/
			//summary_detail();
			if($('#hidden_payment_mode').val() == 'Pay U'){
				var getUrl = window.location;
				var baseUrl = getUrl .protocol + "//" + getUrl.host + "/";
				if($('#emp_id_encrypt').val() != ""){
					window.location.href = baseUrl+"payment_confirmation_view/"+$('#emp_id_encrypt').val();
				}
				
			}else{
				$.ajax({
					url: "/redirect_url_send",
					type: "POST",
					async: false,
					data: {emp_id: emp_id},
					success: function(response) {
					}
				});	
				get_employee_lead_id();
			}
			
		});
	$("#sms_body").html('<p>'+response.message+'</p>')
	$("#myModal1").modal("hide");
	}
	else{
	$("#sms_body").html('<p>'+response.message+'</p>')
	$("#pos_otp").val("");
	return;
	}
	$("#pos_otp").val("");
	}
	});	
});
$(document).on("click", "#resend_otp", function() {
	var captcha_string = $("#entered_captcha2").val();
	otp_generate_new(captcha_string);
});



