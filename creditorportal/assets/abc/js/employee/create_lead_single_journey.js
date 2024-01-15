function ajaxindicatorstart(text) {
	text =typeof text !== "undefined"? text: "We are quickly gathering your information to get you started";
  
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

$(document).ready(function(){
	$('.nri_content').hide();
	$("#submit_lead").hide();
	$(document).on("change",".is_axis_bank_customer",function(){
		var value = $(this).val();
		if(value == 'No'){
			// swal("Alert","Account Number is mandatory for Premium debit for Group Products!","warning");
			swal("Alert","This product is allowed only for Axis Bank existing customer, please visit nearest Axis Bank branch to open an account.","warning");
			$("#submit_lead").hide();
			$('.nri_content').hide();
		}else{
			swal("Alert","You have confirmed that Insured person/proposer is an existing Axis Bank customer. Please enter the Axis Bank account detail in the Bank details section.","warning");
			$("#submit_lead").show();
			$('.nri_content').show();
		}
	})

	/*$(document).on("change","input[name='relation_with_bank']",function(){
		if($(this).val() == "Saving Bank Account Holder"){
			$('.bank_det').show();
		}else{
			$('.bank_det').hide();
		}
	})*/

	$(document).on("change","#salutation",function(){
		var value = $(this).val();
		if(value == 'Mr'){
			$('#gender1').val("Male");
		}else{
			$('#gender1').val("Female");
		}
	})

	$("body").on("keyup", ".alphabates_only", function (e) {
		var $th = $(this);
		if (
			e.keyCode != 46 &&
			e.keyCode != 8 &&
			e.keyCode != 37 &&
			e.keyCode != 38 &&
			e.keyCode != 39 &&
			e.keyCode != 40
		) {
			$th.val(
				$th.val().replace(/[^A-Za-z]/g, function (str) {
					return "";
				})
			);
		}
		return;
	});

	$("body").on("keyup", ".numeric_only", function (e) {
		var $th = $(this);
		if (
		e.keyCode != 46 &&
		e.keyCode != 8 &&
		e.keyCode != 37 &&
		e.keyCode != 38 &&
		e.keyCode != 39 &&
		e.keyCode != 40
		) {
		$th.val(
		$th.val().replace(/[^0-9]/g, function (str) {
		return "";
		})
		);
		}
		return;
	});

	$("#dob").datepicker({
		dateFormat: "dd-mm-yy",
		prevText: '<i class="fa fa-angle-left"></i>',
		nextText: '<i class="fa fa-angle-right"></i>',
		changeMonth: true,
		changeYear: true,
		yearRange: "-100Y:-18Y",
		maxDate: "-18Y",
		onSelect: function (dateText, inst) {$(this).val(dateText);},
		onClose: function() {$(this).valid();},
	});
})

$.validator.addMethod("lettersonly", function(value, element) {
	return this.optional(element) || value == value.match(/^[a-zA-Z ]*$/);
}, "Please enter a valid name.");

$.validator.addMethod('validMobileNominee',function(value, element, param){
	var mobileInput = value;
	var validMobRe = new RegExp('^[6-9][0-9]{9}$');
	return this.optional(element) || validMobRe.test(mobileInput) && mobileInput.length > 0;
}, 'Enter valid mobile number');

$.validator.addMethod("validateEmail",function (value, element, param) {
	if (value.length == 0) {
	return true;
	}
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	return reg.test(value); // Compare with regular expression
	},"Please enter a valid Email ID. Correct Customer Email ID is mandatory to process further."
);

$.validator.addMethod("valid_address",function (value, element, param) {
	if (value.length == 0) {
	return true;
	}
	var reg = /^[0-9a-zA-Z/,/-]+$/;
	return reg.test(value); // Compare with regular expression
	},"Please enter a valid address."
);

$.validator.addMethod("validate_pincode",function (value, element, param) {
	var regs = /^\d{6}$/g;
	return this.optional(element) || regs.test(value); 
	},"Enter a valid Pin Code"
);

$.validator.addMethod("validate_pancard",function (value, element, param) {
	var regex = /([A-Z]){5}([0-9]){4}([A-Z]){1}$/;
	return this.optional(element) || regex.test(value.toUpperCase()); 
	},"Enter a valid Pancard Number"
);



$("#pincode").keyup(function (e) 
	{
		var $th = $(this);
		if (
		  e.keyCode != 46 &&
		  e.keyCode != 8 &&
		  e.keyCode != 37 &&
		  e.keyCode != 38 &&
		  e.keyCode != 39 &&
		  e.keyCode != 40
		)
		{
		  $th.val(
			$th.val().replace(/[^0-9]/g, function (str) {
			  return "";
			})
		  );
		}
	    $("#city").val('');
	    $("#state").val('');
	    var pincode = $(this).val();
	    if(pincode.length == 6){
	    	$.ajax({
						url: "/axis_pincode_get_state_city",
						type: "POST",
						async: false,
						data:{'pincode':pincode},
						dataType: "json",
						success: function (response) 
						{
							debugger;
							if(response == null){
								swal("Alert","Pincode is unavailable in the pincode master. Please get in touch with ABHI Operations team to get the Pincode added in the master.","warning");
								//$('#pincode').after('<label id="pincode-error" class="error" for="pincode">Pincode is unavailable in the pincode master. Please get in touch with ABHI Operations team to get the Pincode added in the master.</label>');
						   
							}else if(response.city!=null && response.state!=null)
						    {
								$("#city").val(response.city);
								$("#state").val(response.state);
								//$('#pincode').html('');
						    }


							
						}
				});
	    }
		 

	});



	$('#ifsc_code').on('keyup', function () {
		if (this.value.length == 11) {
			//set all value by ifsc code
			getIfscCode(this.value);
		}else if (this.value.length == 0) {
			$("#bank_name").val('Axis Bank Ltd');
		}
	});
	
	function getIfscCode(code) {
		$.post('/getIfscCode', {
		'ifsc_code': code
		}, function (e) {
		var obj = JSON.parse(e);
		// alert(obj.bank_name);
			if(obj != null){
				$("#bank_name").val('Axis Bank Ltd');
			}else{
				swal("Alert", "Invalid IFSC Code", "warning");
				$("#ifsc_code").val('');
				$("#bank_name").val('Axis Bank Ltd');
				// $("#lead-form").validate().element("#ifsc_code");
			}

		});
	}

	var msg1;
  var dynamicErrorMsg1 = function () {
    return msg1;
  };
  $.validator.addMethod(
    "ifsc_code_validate",
    function (value, element) {
      var code = value;
      code = code.trim();
    
      var status = false;
     
      if (code !== "") {
        $.ajax({
          url: "/getIfscCode",
          type: "POST",
          async: false,
          data: {
			'ifsc_code': code
          },
          dataType: "json",
          success: function (response) {
			msg1 = "Invalid IFSC Code";
			if(response != null){
				
				if(response.bank_name.toLowerCase() == 'axis bank'){
					status = true;
					$("#bank_name").val(response.bank_name);
				}else{
					msg1 = "Axis Bank IFSC Code is required";
					$("#bank_name").val('');
				}
				
			}
          },
        });
      }else{
      	return true;
      }
     
      if (status == true) {
        return true;
      }
    },
    dynamicErrorMsg1
  );

	$.validator.addMethod(
		"valid_mobile",
		function (value, element, param) {
		  var re = new RegExp("^[4-9][0-9]{9}$");
		  return this.optional(element) || re.test(value);
		},
		"Enter a valid 10 digit mobile number"
	  );
	
	  $('#mob_no').keypress(function(event){
	
		if(event.which != 8 && isNaN(String.fromCharCode(event.which))){
			event.preventDefault(); //stop character from entering input
		}
	
	});

	   $('#cus_id').keypress(function(event){
	
		if(event.which != 8 && isNaN(String.fromCharCode(event.which))){
			event.preventDefault(); //stop character from entering input
		}
	
	});




	var msg;
  var dynamicErrorMsg = function () {
    return msg;
  };
  $.validator.addMethod(
    "sol_id_validate",
    function (value, element) {
      var sol_id = value;
      sol_id = sol_id.trim();
      // alert(avCode);
      var status = false;
      //09-04-2021
      //  var output_msg = '';
      if (sol_id !== "") {
        $.ajax({
          url: "/sol_id_validate",
          type: "POST",
          async: false,
          data: {
            sol_id: sol_id,
          },
          dataType: "json",
          success: function (response) {
            msg = response.message;
			// console.log(response);
            if (response.status == true) {
              status = true;
            }
          },
        });
      }else{
        return true;
      }

      if (status == true) {
        return true;
      }
    },
    dynamicErrorMsg
  );

  jQuery.validator.addMethod("alphanumericOnly", function(value, element) {
	return this.optional(element) || /^[a-z,0-9]+$/i.test(value);
  }, "Please enter only letters or numbers."); 

/*
			if($('input[name="relation_with_bank"]:checked').val() == "Saving Bank Account Holder"){
				$.validator.addClassRules("ifsc_code", {
				ifsc_code_validate: ".ifsc_code"
				});
			}*/
		  
		


$("#lead-form").validate({
	ignore: ".ignore",
	rules: {
		salutation: {
			required: true
		},
		first_name: {
			required: true,
			lettersonly: true
		},
		last_name: {
			// required: true,
			lettersonly: true
		},
		gender1: {
			required: true
		},
		mob_no: {
			required: true,
			//validMobileNominee:true,
			valid_mobile:true
		},
		email: {
			validateEmail: true,
			required: true
		},
		dob: {
			required: true
		},
		address: {
			required: true,
			//valid_address: true
		},
		pancard: {
			validate_pancard: function(element){
	            return $('#pancard').val() != "";
	        },
			//required: true
		},
		pincode: {
			validate_pincode: true,
			required: true
		},
		city: {
			required: true
		},
		state: {
			required: true
		},
		emp_code: {
			required: true,
			alphanumericOnly:true
		},
		sol_id: {
			// required: true,
			sol_id_validate: function(element){
	            return $('#sol_id').val() != "";
	        },
		},
		/*channel_txt: {
			required: true
		},
		bank_name: {
			required: function(element){
	            return $('input[name="relation_with_bank"]:checked').val() == "Saving Bank Account Holder";
	        },
		},*/
		ifsc_code: {
			/*required: function(element){
	            return $('input[name="relation_with_bank"]:checked').val() == "Saving Bank Account Holder";
	        },*/
			ifsc_code_validate: function(element){
	            return $('#ifsc_code').val() != "";
	        },
		},
		/*ac_no: {
			required: function(element){
	            return $('input[name="relation_with_bank"]:checked').val() == "Saving Bank Account Holder";
	        },
		},*/
		nri_1: {
			required: true
		},
		cus_id: {
			required: true,
			minlength:9
		},
		relation_with_bank: {
			required: true
		},

	},
	messages: {
		salutation: {
			required: "Salutation is required."
		},
		first_name: {
			required: "First name is required.",
			lettersonly: "Enter only alphabates"
		},
		gender1: {
			required: "Gender is required."
		},
		mob_no: {
			required: "Mobile number is required."
		},
		email: {
			required: "Emai id is required."
		},
		dob: {
			required: "DOB is required."
		},
		emp_code: {
			required: "Emp code is required."
		},
		sol_id: {
			required: "Branch sol id is required."
		},
		channel_txt: {
			required: "Channel is required."
		},
		bank_name: {
			required: "Bank name is required."
		},
		ifsc_code: {
			required: "IFSC code is required.",
		},
		ac_no: {
			required: "A/C number is required."
		},
		nri_1: {
			required: "NRI flag is required."
		},
		cust_id: {
			required: "Customer id is required.",
			minlength : "Customer id should have 9 integers."
		}
	},
	submitHandler: function (form) {

		ajaxindicatorstart("Please wait...");
		if($("#seller_mob_no").val() == $("#mob_no").val()){
			ajaxindicatorstop();
			swal("Alert", "Enter Valid customer mobile number!. Customer/Proposer mobile number should not be same as Relationship manager mobile number", "warning");
			$("#mob_no").focus();
			return false;
		}
		var form = $("#lead-form").serialize()+"&gender1="+$("#gender1").val();
		$.post("/employee/save_single_journey_details", form, function (e) {
			ajaxindicatorstop();
			var data = JSON.parse(e);
			console.log(data);
			if (!data.status) {
				swal({
					title: "Alert",
					text: data.message,
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
					//location.reload();
				});
				return;
			}else{
				 $.ajax({
          url: "/lead_deactivate",
          type: "POST",
          async: false,
          data: {
            "lead_id": data.lead_id,
			"product_code": data.product_code,
			"unique_ref_no":data.unique_ref_no,
			"is_redirect_popup":"0",
			"arr" : data.arr
          },
          dataType: "json",
          success: function (response) {
					ajaxindicatorstop();
			  //var data = JSON.parse(response);
			  console.log(data);
			  if(response.message){
				  if(response.status == 3)
				  {
					                	swal({
					title: "Success",
					text: obj.message,
					type: "success",
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
           	swal({
					title: "Success",
					text: response.message,
					type: "success",
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
					if(response.url){
				
					window.location.href =  response.url;}
				});
				  }
			  }else
			  {
				  window.location.href =  response.url;
			  }
          },
        });
				// swal({
					// title: "Success",
					// text: data.message,
					// type: "success",
					// showCancelButton: false,
					// confirmButtonText: "Ok!",
					// closeOnConfirm: true,
					// allowOutsideClick: false,
					// closeOnClickOutside: false,
					// closeOnEsc: false,
					// dangerMode: true,
					// allowEscapeKey: false
				// },
				// function () {
					// ajaxindicatorstart("Please wait...");
					// window.location.href =  data.url;
				// });
			}
		});
	}
});