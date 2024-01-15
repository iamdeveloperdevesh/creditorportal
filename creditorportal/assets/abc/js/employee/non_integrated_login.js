function ajaxindicatorstart(text) {
	text =
	  typeof text !== "undefined"
		? text
		: "We are quickly gathering your information to get you started";
  
	var res = "";
  
	if ($("body").find("#resultLoading").attr("id") != "resultLoading") {
	  res += "<div id='resultLoading' style='display: none'>";
	  res += "<div id='resultcontent'>";
	  res += "<div id='ajaxloader' class='txt'>";
	  res +=
		'<svg class="lds-curve-bars" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><g transform="translate(50,50)"><circle cx="0" cy="0" r="8.333333333333334" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="26.179938779914945 26.179938779914945" transform="rotate(2.72337)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="0" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="16.666666666666668" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="52.35987755982989 52.35987755982989" transform="rotate(64.7343)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.2" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="25" fill="none" stroke="#ffffff" stroke-width="4" stroke-dasharray="78.53981633974483 78.53981633974483" transform="rotate(150.07)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.4" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="33.333333333333336" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="104.71975511965978 104.71975511965978" transform="rotate(239.433)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.6" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="41.666666666666664" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="130.89969389957471 130.89969389957471" transform="rotate(320.34)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.8" repeatCount="indefinite"></animateTransform></circle></g></svg>';
	  res += "<br/>";
	  res += "<span id='loadingMsg'></span>";
	  res += "</div>";
	  res += "</div>";
	  res += "</div>";
  
	  $("body").append(res);
	}
  
	$("#loadingMsg").html(text);
  
	$("#resultLoading").find("#resultcontent > #ajaxloader").css({
	  position: "absolute",
	  width: "500px",
	  height: "75px",
	});
  
	$("#resultLoading").css({
	  width: "100%",
	  height: "100%",
	  position: "fixed",
	  "z-index": "10000000",
	  top: "0",
	  left: "0",
	  right: "0",
	  bottom: "0",
	  margin: "auto",
	});
  
	$("#resultLoading").find("#resultcontent").css({
	  background: "#ffffff",
	  opacity: "0.7",
	  width: "100%",
	  height: "100%",
	  "text-align": "center",
	  "vertical-align": "middle",
	  position: "fixed",
	  top: "0",
	  left: "0",
	  right: "0",
	  bottom: "0",
	  margin: "auto",
	  "font-size": "16px",
	  "z-index": "10",
	  color: "#000000",
	});
  
	$("#resultLoading").find(".txt").css({
	  position: "absolute",
	  top: "-25%",
	  bottom: "0",
	  left: "0",
	  right: "0",
	  margin: "auto",
	});
  
	$("#resultLoading").fadeIn(300);
  
	$("body").css("cursor", "wait");
  }
  
  function ajaxindicatorstop() {
	$("#resultLoading").fadeOut(300);
  
	$("body").css("cursor", "default");
  }

function otp_generate()
	{
		var mob_no = $("#mob_no").val();
		var product_id = $("#product_id").val();
		var re = new RegExp('^[6-9][0-9]{9}$');


		

		if(!mob_no.match(/^\d{10}$/))
		{
			$('#mobile_no_error').css("display" , "block");
			$('#mobile_no_error').text("Please Enter/Valid Mobile Number");
			$( "#otphide").hide("");
			$( "#otphide2").hide("");
			$( "#otphide3").hide("");
			
			
		}else if(!re.test(mob_no))
		{
			$('#mobile_no_error').css("display" , "block");
			$('#mobile_no_error').text("Enter valid 10 digit no starting from 6 to 9");
			$( "#otphide").hide("");
			$( "#otphide2").hide("");
			$( "#otphide3").hide("");
		}else
		{
			$('#mobile_no_error').css("display","none");

			//captcha - update - upendra - 16-10-2021
			var entered_captcha = $("#entered_captcha").val();
			if(entered_captcha == ''){
				$("#captcha_error").show();
				$("#captcha_error").html("Please enter captcha text");
				return;
			}else{
				$("#captcha_error").hide();
			}
			

			ajaxindicatorstart('Please wait...');
          setTimeout(function () {
			 $.ajax({
						url: "generate_otp_non_integrated",
						type: "POST",
						data: {
						"mob_no":mob_no,
						"product_id":product_id,
						//captcha - update - upendra - 16-10-2021
						"entered_captcha":entered_captcha,
						},
						async: true,
						dataType: "json",
						success: function (response) 
						{	
							$("#entered_captcha").val('');
							$("#pos_otp").val('');
							ajaxindicatorstop();
							if(response.status == "error"){
								swal("Alert", response.message, "warning")
							}
							else
							{
								$("#mob_no").attr("disabled", true);
								$( "#otphide").show("");
								$( "#otphide2").show("");
								$( "#otphide3").show("");
								$( "#gene_otp").hide("");

								//captcha - update - upendra - 16-10-2021
								$("#entered_captcha").val('');
								refresh_captcha();
							}
							
						}
				    });
				}, 1000);
		}
		 
		
	}


	$(document).on("click", "#validate_otp", function()
	{
		var product_id = $("#product_id").val();

		//captcha - update
		var otp = $("#pos_otp").val();
		if(otp.trim() == ""){
			swal("Alert", "OTP can not be blank", "warning");
			return;
		}
		ajaxindicatorstart('Please wait...');
          setTimeout(function () {
	 $.ajax({
				url: "/check_otp_non_integrated",
				type: "POST",
				data: {
					otp: $("#pos_otp").val() , 
					mob_no : $("#mob_no").val(),
					product_id : product_id,
				},
				async: false,
				dataType: "json",
				success: function (response) 
				{
					ajaxindicatorstop();
							if(response.status == '1')
							{
									location.replace(response.url);
							}else
							{
								swal("Alert", response.message, "warning");
							}

				}
			});
		}, 1000);
	});


function validatephone(phone)
  	{
  		phone = phone.replace(/[^0-9]/g,'');
  		$("#mob_no").val(phone);
  		if( phone == '' || !phone.match(/^0[0-9]{9}$/) )
  			{
  				return false;
  			}
  		else
  			{
  			return true;	
  			}
  	}


	//   captcha update - upendra - 16-10-2021
	  function refresh_captcha(){
		ajaxindicatorstart();
		$.ajax({
			url: "/refresh_captcha_single_link",
			type: "POST",
			data: { 
				 
			},
			async: true,
			dataType: "text",  
			  cache:false,
			success: function (response) {
				ajaxindicatorstop();
				// alert(response);
				$("#entered_captcha").val('');
				$("#captcha_image_span").html(response);
			},
		  });
	  }
	  setInterval(refresh_captcha, 120000);
	
	  $(document).on("click", "#refresh_captcha", function () {
		refresh_captcha();
	  });

   