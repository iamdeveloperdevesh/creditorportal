function otp_generate()
	{
		var mob_no = $("#mob_no").val();
		var re = new RegExp('^[6-9][0-9]{9}$');

		$("#pos_otp").val('');

		if(!mob_no.match(/^\d{10}$/))
		{
			$('#mobile_no_error').css("display" , "block");
			$('#mobile_no_error').text("Please Enter/Valid Mobile Number");
			$( "#otphide").hide("");
			$( "#otphide2").hide("");
			$( "#otphide3").hide("");
			// $( "#resend_otp").hide("");
			
			
		}else if(!re.test(mob_no))
		{
			$('#mobile_no_error').css("display" , "block");
			$('#mobile_no_error').text("Enter valid 10 digit no starting from 6 to 9");
			$( "#otphide").hide("");
			$( "#otphide2").hide("");
			$( "#otphide3").hide("");
			// $( "#resend_otp").hide("");
		}else
		{
			$('#mobile_no_error').css("display","none");

			//captcha - update - upendra - 16-10-2021
			// var entered_captcha = $("#entered_captcha").val();
			var entered_captcha = '123456';

			if(entered_captcha == ''){
				$("#captcha_error").show();
				$('#captcha_error').css("display" , "block");
				$("#captcha_error").html("Please enter captcha text");
				return;
			}else{
				$("#captcha_error").hide();
			}	

			// $("#myModal1").modal("hide");
			// $("#sms_body").html('');
			// ajaxindicatorstop();
			 $.ajax({
						url: "fyntune_home/send_otp",
						type: "POST",
						data: {
						"mob_no":mob_no,
						"entered_captcha":entered_captcha
						},
						async: false,
						dataType: "json",
						success: function (response) 
						{
							
							if(response.status == "error"){
								
								swal("Alert", response.message)
							}
							else
							{
								$( "#otphide").show("");
								$( "#otphide2").show("");
								$( "#otphide3").show("");
								$( "#gene_otp").hide("");
								// $( "#resend_otp").show("");

								//captcha - update - upendra - 16-10-2021
								$("#entered_captcha").val('');
								refresh_captcha();
							}
							/*alert(response.status);

							var obj = JSON.parse(response);
	
							if(obj.status == '1'){
								alert('otp success');
							}*/
							// $("#pos_otp").val("");
							// $("#myModal1").modal("show");
						},
						error: function (xhr){
							swal("Alert", JSON.parse(xhr.responseText).message, "warning");
							$("#entered_captcha").val('');
							$("#pos_otp").val('');
				        }
				    });
		}
		 
		
	}


	$(document).on("click", "#validate_otp", function()
	{
	 $.ajax({
				url: "fyntune_home/validate_otp",
				type: "POST",
				data: {otp: $("#pos_otp").val() , mobile_no : $("#mob_no").val()},
				async: false,
				dataType: "json",
				success: function (response) 
				{
					//console.log(response.status);
					//var obj = $.parseJSON(response);
					//console.log(obj);return false;
					if(response.status == '1')
					{

						// location.replace(response.url);
						// document.location.href=response.url;

						// console.log(response.url);
						// console.log(location.origin);
						window.location.replace(location.origin+"/"+response.url);
						// location.replace(response.url);
						// window.location.href=response.url;
					}else
					{
						swal("Alert", response.message)
					}

				},
		        error: function (xhr){
					swal("Alert",JSON.parse(xhr.responseText).message, "warning");
					$("#pos_otp").val('');
		        }
			});
	});


function validatephone(phone)
  	{
  		phone = phone.replace(/[^0-9]/g,'');
  		$("#mob_no").val(phone);
  		if( phone == '' || !phone.match(/^0[0-9]{9}$/) )
  			{
  				// $("#phonefield").css({'background':'#FFEDEF' , 'border':'solid 1px red'});
  				return false;
  			}
  		else
  			{
  				// $("#phonefield").css({'background':'#99FF99' , 'border':'solid 1px #99FF99'});
  			return true;	
  			}
  	}

//   captcha update - upendra - 16-10-2021
function refresh_captcha(){
	$("#entered_captcha").val('');
ajaxindicatorstart();
$.ajax({
	url: "fyntune_home/refresh_captcha_abc",
	type: "POST",
	data: { 
		 
	},
	async: true,
	dataType: "text",  
	  cache:false,
	success: function (response) {
		ajaxindicatorstop();
		// alert(response);
		$("#captcha_image_span").html(response);
	},
  });
}
setInterval(refresh_captcha, 120000);

$(document).on("click", "#refresh_captcha", function () {
refresh_captcha();
});


   