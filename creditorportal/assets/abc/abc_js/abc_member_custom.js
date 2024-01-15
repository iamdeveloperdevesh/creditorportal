$(function () {
          
  $(".hasDatepicker1").datepicker({
    dateFormat: "mm/dd/yy",
    
    prevText: '<i class="fa fa-angle-left"></i>',
    nextText: '<i class="fa fa-angle-right"></i>',
    changeMonth: true,
    changeYear: true,
    yearRange: "-100:+0",
    maxDate: "0",
  });

  
  $("#nominee_dob").datepicker({
    dateFormat: "dd-mm-yy",
    prevText: '<i class="fa fa-angle-left"></i>',
    nextText: '<i class="fa fa-angle-right"></i>',
    changeMonth: true,
    changeYear: true,
    maxDate: 0,
    yearRange: "-100: +0",
    onSelect: function (dateText, inst) {
        $(this).val(dateText);
        //get_age_family(dateText, eValue);
    },
    onClose: function() {$(this).valid();},
});

});


$.ajax({
      url: "/get_premium_abc",
      type: "POST",
      async: false,
      dataType: 'json',
      success: function (response) {
        $('.drop_prem').html(response.html_premium);
        $('.total_premium').html(response.total_premium);
      }
    });

   $.ajax({
      url: "/get_review_cards",
      type: "POST",
      async: false,
      dataType: 'json',
      success: function (response) {
        $('.memCardShow').html(response.html_card);
      }
    });

   $.ajax({
      url: "/get_policy_member_details",
      type: "POST",
      async: false,
      dataType: 'json',
      success: function (response1) {
      	$('.memDetailsDiv').html(response1.html);
      }
    });

  function deleteProduct(elem){
    var policyId = $(elem).data("policy");
    $.ajax({
      url: "/delete_selected_product",
      type: "POST",
      async: false,
      data:{
        policyId: policyId
      },
      dataType: 'json',
      success: function (response) {
        if(response.status == 'success'){
          swal("Alert","Product deleted successfully !!", "warning"); 
          $.ajax({
            url: "/get_premium_abc",
            type: "POST",
            async: false,
            dataType: 'json',
            success: function (response) {
              $('.drop_prem').html(response.html_premium);
              $('.total_premium').html(response.total_premium);
            }
          });
         $.ajax({
            url: "/get_review_cards",
            type: "POST",
            async: false,
            dataType: 'json',
            success: function (response) {
              $('.memCardShow').html(response.html_card);
            }
          });
         $.ajax({
            url: "/get_policy_member_details",
            type: "POST",
            async: false,
            dataType: 'json',
            success: function (response1) {
              $('.memDetailsDiv').html(response1.html);
            }
          }); 
         $.ajax({
            url: "/required_members",
            type: "POST",
            async: false,
            dataType: 'json',
            success: function (response) {
              if(response.count > 0){
                $.each(response.not_required_relations, function(key, data){
                  let element = $("#member_card_for_"+data);
                  element.remove();
                });
              }
            }
          });
        }
      }
    });
  }	

  function clearNomineeForm(e){
    $('#nomineeFields select').prop('selectedIndex',0);
    $('#nomineeFields input').val('');
  }
   $.ajax({
      url: "/get_nominee_details",
      type: "POST",
      async: false,
      dataType: 'json',
      success: function (response) {
        var show_member_tab = $('.show_member_tab').val();
      	if (response.nominee_details.relation_data != null) {
          if(show_member_tab == 1){
            progressline(event, 'review-del-link')
            //$('#memSec1').addClass("active1");
            //$('#memSec2').addClass("active1");
            //$('#memSec3').addClass("active");
            
          }else{
            progressline(event, 'mem-del-link')
            //$('#memSec1').addClass("active");
            
          }
      		if(response.nominee_details.nominee_lname == '.'){
      			response.nominee_details.nominee_lname = '';
      		}
      		if(response.nominee_details.nominee_lname == ''){
      			var name = response.nominee_details.nominee_fname;
      		}else{
      			var name = response.nominee_details.nominee_fname+' '+response.nominee_details.nominee_lname;
      		}
      		var nominee_dob = response.nominee_details.nominee_dob;
      		var nominee_no = response.nominee_details.nominee_no;
      		$('#nominee_full_name').val(name);
    			$('#nominee_dob').val(nominee_dob);
    			$('#nominee_phone').val(nominee_no);
    			$('.rev_nominee_name').html(name);
    			$('.rev_nominee_dob').html(nominee_dob);
    			$('.rev_nominee_relation').html(response.nominee_details.relation_data.fr_name);
    			$('.rev_nominee_phone').html(nominee_no);
          if(show_member_tab == 1){
            document.getElementById('memSec2').setAttribute("onclick","progressline(event, 'nom-del-link')");
            document.getElementById('memSec3').setAttribute("onclick","progressline(event, 'review-del-link')");
          }
    			var optionHtml = '';
    			optionHtml += '<option value="">Select Relation</option>';
    			$.each(response.nominee_relation, function(key, data){
    				var check = "";
    				if(data.relation_code == response.nominee_details.Relationship_of_Nominee){
    					check = "selected";
    				}
  			    optionHtml += '<option data-id="'+data.nominee_id+'" value="'+data.relation_code+'" '+check+'>'+data.nominee_type+'</option>';
  			});
  			$("#nominee_relation").empty();
  			$('#nominee_relation').append(optionHtml);
  		}else{
        
        if(show_member_tab == 1){
          progressline(event, 'nom-del-link');
          //$('#memSec1').addClass("active1");
          //$('#memSec2').addClass("active");
        }
      }		
    }
  });

  //Member details form validtion

  $("#nominee_phone").keyup(function() {
    $("#nominee_phone").val(this.value.match(/[0-9]*/));
});

	$.validator.addMethod("lettersonly", function(value, element) {
      return this.optional(element) || value == value.match(/^[a-zA-Z /\.]*$/);
  });

  $.validator.addMethod("firstLastName", function(value, element) {
    	return this.optional(element) || value == value.match( /^[a-zA-Z]+( [a-zA-Z]+)+$/);
 	});		

//   jQuery.validator.addMethod("maxlength", function (value, element, param) {
//     console.log(value.length+'element= ' + $(element).attr('name') + ' param= ' + param )
//     if (value.length == param) {
//         return true;
//     } else {
//         return false;
//     }
// }, "You have reached the maximum number of characters allowed for this field.");


  $.validator.addMethod('valid_mobile_last_ten_digits', function (value, element, param) {
    var str1 = value;
    var n1 = 10;
    var newvalue = str1.substring(str1.length - n1);
    $(element).val(newvalue);
    var re = new RegExp('^[6-9][0-9]{9}$');
    return this.optional(element) || re.test(newvalue);

  }, 'Enter valid 10 digit no starting from 6 to 9');

  $.validator.addMethod('validMobileNominee',function(value, element, param){
    var mobileInput = value;
    var validMobRe = new RegExp('^[6-9][0-9]{9}$');
    return this.optional(element) || validMobRe.test(mobileInput) && mobileInput.length > 0;
  }, 'Enter valid mobile number');

  function isAdultDob(dateString) {
    var dateSplit = dateString.split("-");
    var day = dateSplit[0];
    var month = dateSplit[1];
    var year = dateSplit[2];
    var age = 18;
    var mydate = new Date();
    mydate.setFullYear(year, month-1, day);
    var currdate = new Date();
    var setDate = new Date();         
    setDate.setFullYear(mydate.getFullYear() + age, month-1, day);
    if ((currdate - setDate) >= 0){
      return true;
    }else{
      return false;
    }
  }

  $.validator.addMethod('validAdultDob',function(value, element, param){
    var dobInput = value;
    return isAdultDob(dobInput);
  }, 'Age should be above 18 years');

  let timerOn = true;
  let interval = null;

  function timer(remaining) {
    var m = Math.floor(remaining / 60);
    var s = remaining % 60;
    
    m = m < 10 ? '0' + m : m;
    s = s < 10 ? '0' + s : s;
    document.getElementById('timer').innerHTML = m + ':' + s;
    remaining -= 1;
    
    if(remaining >= 0 && timerOn) {
      setTimeout(function() {
          timer(remaining);
      }, 1000);
      return;
    }

    if(!timerOn) {
      // Do validate stuff here
      return;
    }
    // Do timeout stuff here
    $("#resendOtp").show();
  }

  function newTimer(seconds){
    interval = setInterval(function() {
      var m = Math.floor(seconds / 60);
      var s = seconds % 60;
      
      m = m < 10 ? '0' + m : m;
      s = s < 10 ? '0' + s : s;
      seconds--;
      // seconds -= 1;
        $("#timer").html(m + ':' + s);
        if (seconds == 0) {
        // Display a login box
        $("#timer").html('');
        clearInterval(interval);
        $("#resendOtp").show();
        $("#resendOtp :input").val('');
        refresh_captcha('resendotp');
      }
     }, 1000);
  }

  function checkValidation(e){
    //alert($("input[name='dc_type']:checked").val());
    var ghdResponseMaxLength = 200;
    var ghdResponse = $.trim($("#ghdResponse").val());
    var responseLength = ghdResponse.length;
    var validText = new RegExp('^[\.a-zA-Z0-9, ]*$');

    if($("input[name='dc_type']:checked").val() == '' || $("input[name='dc_type']:checked").val() == undefined) {
      $('#insert_proposal_button').attr("data-target","");
      swal("Alert","In order to proceed further please answer the health related questions.", "warning");     
      e.preventDefault();    
      $('#msg-del').modal('hide');
      $('#insert_proposal_button').attr("data-target","");
    } else if($("input[name='dc_type']:checked").val() == '1') {
      swal("Alert","This policy is not available to you or other proposed members. Please visit nearest branch for a suitable Plan.", "warning");
      e.preventDefault();
      $('#msg-del').modal('hide');
      $('#insert_proposal_button').attr("data-target","");
    }else{
      var error = false;
      if(responseLength > ghdResponseMaxLength){
        error = true;
        var message = "Length of response should be 1-200 characters!!"; 
      }
      if (!validText.test(ghdResponse)) {
        error = true;
        var message = "Special characters not allowed in response!!";
      }
      if(error){
        swal("",message, "warning");
        e.preventDefault();
        $('#msg-del').modal('hide');
        $('#insert_proposal_button').attr("data-target","");
        return !error;
      }
      
      $.ajax({
        url: "/get_remaining_products",
        type: "POST",
        async: false,
        dataType: 'json',
        success: function (response) {
          if(response.count > 0){
            let productNamesArr = [];
            let productIdArr = [];
            $.each(response.products, function(key, data){
              productNamesArr.push(data.product_name);
              productIdArr.push(data.encrypted_policy_detail_id);
            });
            let addLink = window.location.origin+'/generate_quote/'+productIdArr[0]+'?i=edit';
            let createProposalRedirectLink = window.location.origin+'/createProposal_abc/0';
            $('#insert_proposal_button').attr("data-target","#msg-del");
            $('#remaining_product_link').attr("href",addLink);
            $('#createProposalRedirectlink').attr("href",createProposalRedirectLink);
            $('#remaining_products_modal_txt').html(productNamesArr.join(' OR '));
          } else {
            window.top.location.href = "/createProposal_abc/0";
          }
        }
      });
    }
  }

  function showHealthDeclaration(e){
    var product_id = $("#product_id_hidden").val();
    if(product_id == 'HERO_FINCORP'){
      var tncCheck = +$("#t_and_c_check").is(':checked');
      if(tncCheck){
        $("#insert_proposal_button").attr("data-target","#good-health-declaration");     
      } else {
        swal("","Please select/accept proposal T&C to proceed further", "warning");
        e.preventDefault();
        $('#good-health-declaration').modal('hide');
        $("#insert_proposal_button").attr("data-target","");
      }
    } else if(product_id == 'MUTHOOT'){
      $("#insert_proposal_button").attr("data-target","#good-health-declaration");
    }
  }

  // function sendOtp(e){
  //   var product_id = $("#product_id_hidden").val();

  //   var autoRenewal = $("#auto_renewal_check").val();

  //   // var goGreen = +$("#go_green_check").is(':checked');


  //   if(product_id == 'HERO_FINCORP'){
  //     var url = '/send_otp_hero';
  //   }else if(product_id == 'MUTHOOT'){
  //     var url = '/send_otp_muthoot';
  //   }

  //   var allowGhdValue = '1';
  //   if(product_id == 'HERO_FINCORP'){
  //     allowGhdValue = '0';
  //   }

  //   if($("input[name='ghd_muthoot']:checked").val() == '' || $("input[name='ghd_muthoot']:checked").val() == undefined){
  //     $('#insert_proposal_button').attr("data-target","");
  //     swal("Alert","In order to proceed further please answer the health related questions.", "warning");     
  //     e.preventDefault();    
  //     $('#good-health-declaration').modal('show');
  //     $('#sent-otp-btn').attr("data-target","");
  //   }else if($("input[name='ghd_muthoot']:checked").val() != allowGhdValue){
  //     swal("Alert","This policy is not available to you or other proposed members. Please visit nearest branch for a suitable Plan.", "warning");
  //     e.preventDefault();
  //     $('#good-health-declaration').modal('show');
  //     $('#sent-otp-btn').attr("data-target","");
  //   }else{
  //     $.ajax({
  //       url: url,
  //       type: "POST",
  //       async: false,
  //       data: {
  //         autoRenewal:autoRenewal,
  //         // goGreen:goGreen
  //       },
  //       dataType: 'json',
  //       success: function (response) {
  //         if(response.status == 'success'){
  //           $('#good-health-declaration').modal('hide');
  //           $('#sent-otp-btn').attr("data-target","#sendOtp");
  //           newTimer(60);
  //         } else {
  //           swal("Alert",response.message, "warning");
  //           e.preventDefault();
  //         }
  //       }
  //     });
  //   }
  // }

  function sendOtp(e){
    var product_id = $("#product_id_hidden").val();

    var autoRenewal = $("#auto_renewal_check").val();

    // var goGreen = +$("#go_green_check").is(':checked');


    if(product_id == 'HERO_FINCORP'){
      var url = '/send_otp_hero';
    }else if(product_id == 'MUTHOOT'){
      var url = '/send_otp_muthoot';
    }

    var allowGhdValue = '1';
    if(product_id == 'HERO_FINCORP'){
      allowGhdValue = '0';
    }

    //captcha - update - upendra - 16-10-2021
    var entered_captcha = $("#entered_captcha_sendotp").val();
    if(entered_captcha == ''){
      $("#captcha_error_sendotp").show();
      $('#captcha_error_sendotp').css("display" , "block");
      $("#captcha_error_sendotp").html("Please enter captcha text");
      return;
    }else{
      $("#captcha_error_sendotp").hide();
    }
    if($("input[name='ghd_muthoot']:checked").val() == '' || $("input[name='ghd_muthoot']:checked").val() == undefined){
      $('#insert_proposal_button').attr("data-target","");
      swal("Alert","In order to proceed further please answer the health related questions.", "warning");     
      e.preventDefault();    
      $('#good-health-declaration').modal('show');
      $('#sent-otp-btn').attr("data-target","");
    }else if($("input[name='ghd_muthoot']:checked").val() != allowGhdValue){
      swal("Alert","This policy is not available to you or other proposed members. Please visit nearest branch for a suitable Plan.", "warning");
      e.preventDefault();
      $('#good-health-declaration').modal('show');
      $('#sent-otp-btn').attr("data-target","");
    }else{
      $.ajax({
        url: url,
        type: "POST",
        async: false,
        data: {
          autoRenewal:autoRenewal,
          entered_captcha:entered_captcha,
          // goGreen:goGreen
        },
        dataType: 'json',
        success: function (response) {
          if(response.status == 'success'){
            $('#good-health-declaration').modal('hide');
            $('#sent-otp-btn').attr("data-target","#sendOtp");
            newTimer(5);
            //timer(60);
          } else {
            swal("Alert",response.message, "warning");
            e.preventDefault();
          }
        },
        error: function (xhr){
          $("#entered_captcha_sendotp").val('');
          swal("Alert",JSON.parse(xhr.responseText).message, "warning");
          e.preventDefault();
        }
      });
    }
  }

  $('#sendOtp').on('hidden.bs.modal', function () {
    clearInterval(interval);
  });
  
  // function resendOtp(e){
  //   var autoRenewal = $("#auto_renewal_check").val();

  //   var product_id = $("#product_id_hidden").val();
  //   if(product_id == 'HERO_FINCORP'){
  //     var url = '/send_otp_hero';
  //   }else if(product_id == 'MUTHOOT'){
  //     var url = '/send_otp_muthoot';
  //   }

  //   $.ajax({
  //     url: url,
  //     type: "POST",
  //     async: false,
  //     data: {autoRenewal:autoRenewal},
  //     dataType: 'json',
  //     success: function (response) {
  //       if(response.status == 'success'){
  //         $('#resendOtp').hide();
  //         $("[name='otp[]']").val('');
  //         clearInterval(interval);
  //         newTimer(5);
  //       } else {
  //         swal("Alert",response.message, "warning");
  //         e.preventDefault();
  //       }
  //     }
  //   });   
  // }

  function resendOtp(e){
    var autoRenewal = $("#auto_renewal_check").val();
    var product_id = $("#product_id_hidden").val();
    if(product_id == 'HERO_FINCORP'){
      var url = '/send_otp_hero';
    }else if(product_id == 'MUTHOOT'){
      var url = '/send_otp_muthoot';
    }

    //captcha - update - upendra - 16-10-2021
    var entered_captcha = $("#entered_captcha_resendotp").val();
    if(entered_captcha == ''){
      $("#captcha_error_resendotp").show();
      $('#captcha_error_resendotp').css("display" , "block");
      $("#captcha_error_resendotp").html("Please enter captcha text");
      return;
    }else{
      $("#captcha_error_resendotp").hide();
    }

    $.ajax({
      url: url,
      type: "POST",
      async: false,
      data: {
        autoRenewal:autoRenewal,
        entered_captcha:entered_captcha
      },
      dataType: 'json',
      success: function (response) {
        if(response.status == 'success'){
          $('#resendOtp').hide();
          $("[name='otp[]']").val('');
          //timer(60);
          clearInterval(interval);
          newTimer(5);
        } else {
          swal("Alert",response.message, "warning");
          e.preventDefault();
        }
      },
      error: function (xhr){
        $("#entered_captcha_resendotp").val('');
        swal("Alert",JSON.parse(xhr.responseText).message, "warning");
        e.preventDefault();
      }
    });   
  }

  // function validateOtp(e){
    
  //   // get OTP
  //   var autoRenewalCheck = $('#auto_renewal_check').prop("checked");

  //   var goGreen = +$("#go_green_check").is(':checked');

  //   if(autoRenewalCheck){
  //     autoRenewalCheck = 1;
  //   } else {
  //     autoRenewalCheck = 0;
  //   }

  //   var product_id = $("#product_id_hidden").val();
  //   if(product_id == 'HERO_FINCORP'){
  //     var url = '/check_otp_hero';
  //   }else if(product_id == 'MUTHOOT'){
  //     var url = '/check_otp_muthoot';
  //   }

  //   var values = $("input[name='otp[]']").map(function(){return $(this).val();}).get();
  //   var otpEntered = values.join('');

  //   if(otpEntered.length == 4){
  //     $.ajax({
  //       url: url,
  //       type: "POST",
  //       async: false,
  //       data: {
  //         otpEntered:otpEntered,
  //         goGreen:goGreen
  //       },
  //       dataType: 'json',
  //       success: function (response) {
  //         if(response.status == 'success'){
  //           // redirect to create proposal
  //           $('#invalidOtpMsg').hide();
  //           $('#sendOtp').modal('hide');
  //           $("input[name='otp[]']").val('');
  //           ajaxindicatorstart();
  //           window.top.location.href = "/createProposal_abc/"+autoRenewalCheck;
  //         }  else {
  //           swal("Alert",response.message, "warning");
  //           $("input[name='otp[]']").val('');
  //           e.preventDefault();
  //         }
  //       }
  //     });
  //   } else {
  //     swal("Alert","Enter valid otp", "warning");
  //   }
  // }

  function validateOtp(e){
    
    // get OTP
    var autoRenewalCheck = $('#auto_renewal_check').prop("checked");

    var goGreen = +$("#go_green_check").is(':checked');

    if(autoRenewalCheck){
      autoRenewalCheck = 1;
    } else {
      autoRenewalCheck = 0;
    }

    var product_id = $("#product_id_hidden").val();
    if(product_id == 'HERO_FINCORP'){
      var url = '/check_otp_hero';
    }else if(product_id == 'MUTHOOT'){
      var url = '/check_otp_muthoot';
    }

    var values = $("input[name='otp[]']").map(function(){return $(this).val();}).get();
    var otpEntered = values.join('');

    if(otpEntered.length == 4){
      $.ajax({
        url: url,
        type: "POST",
        async: false,
        data: {
          otpEntered:otpEntered,
          goGreen:goGreen
        },
        dataType: 'json',
        success: function (response,) {
          // console.log(xhr.status);
          if(response.status == 'success'){
            // redirect to create proposal
            $('#invalidOtpMsg').hide();
            $('#sendOtp').modal('hide');
            $("input[name='otp[]']").val('');
            ajaxindicatorstart();
            window.top.location.href = "/createProposal_abc/"+autoRenewalCheck;
          }  else {
            swal("Alert",response.message, "warning");
            $("input[name='otp[]']").val('');
            e.preventDefault();
          }
        },
        error: function (xhr){
          swal("Alert",JSON.parse(xhr.responseText).message, "warning");
          $("input[name='otp[]']").val('');
          e.preventDefault();
        }
      });
    } else {
      swal("Alert","Enter valid otp", "warning");
    }
  }

  $('input[type=radio][name=dc_type]').change(function () {
        if ($(this).val() == '1') {
          $('#insert_proposal_button').attr("disabled", true);
          swal("Alert","This policy is not available to you or other proposed members. Please visit nearest branch for a suitable Plan.", "warning");
        } else {
          $('#insert_proposal_button').attr("disabled", false);
        }
  });

	$('#memberform').validate({
        rules: {
            Self_full_name: {
                required: true,
                lettersonly: true
            },
            Self_dob: {
                required: true
            },
            Spouse_full_name: {
                required: true,
                lettersonly: true
            },
            Spouse_dob: {
                required: true
            },
            Kid1_full_name: {
                required: true,
                lettersonly: true
            },
            Kid1_dob: {
                required: true
            },
            Kid2_full_name: {
                required: true,
                lettersonly: true
            },
            Kid2_dob: {
                required: true
            },
            Kid3_full_name: {
              required: true,
              lettersonly: true
            },
            Kid3_dob: {
                required: true
            },
            Kid4_full_name: {
                required: true,
                lettersonly: true
            },
            Kid4_dob: {
                required: true
            },
        },
        messages:{
	        Self_full_name:{
		        required: "Self full name is required",
		        lettersonly: "Enter only alphabates"
		    },
	    	Self_dob:{
		        required: "Self DOB is required"
		    },
		    Spouse_full_name:{
		        required: "Spouse full name is required",
		        lettersonly: "Enter only alphabates"
		    },
	    	Spouse_dob:{
		        required: "Spouse DOB is required"
		    },
		    Kid1_full_name:{
		        required: "Kid1 full name is required",
		        lettersonly: "Enter only alphabates"
		    },
	    	Kid1_dob:{
		        required: "Kid1 DOB is required"
		    },
		    Kid2_full_name:{
		        required: "Kid2 full name is required",
		        lettersonly: "Enter only alphabates"
		    },
	    	Kid2_dob:{
		        required: "Kid2 DOB is required"
		    },
        Kid3_full_name:{
            required: "Kid3 full name is required",
            lettersonly: "Enter only alphabates"
        },
        Kid3_dob:{
            required: "Kid3 DOB is required"
        },
        Kid4_full_name:{
            required: "Kid4 full name is required",
            lettersonly: "Enter only alphabates"
        },
        Kid4_dob:{
            required: "Kid4 DOB is required"
        }
        },
        submitHandler: function (form) { // for demo
            //alert('valid form');
            //$('.memSec2').trigger('click');
            $('#loaderNew').show();
            var form = $("#memberform").serialize();
            //console.log(form);

            $.ajax({
    					url: "/Member_detail_abc/saveFamilyDetails",
    					type: "POST",
    					async: false,
    					data: form,
    					dataType: 'json',
    					success: function (response) {
                var msg='';
                console.log(response);
                var check_status=true;
                $.each(response, function(key,val) {
                  console.log(val.success);
                  
                  if(val.success==false){
                    msg +=val.msg+"\n";
                    check_status=false; 
                  }else{
                    $("#member_id"+key).val(val.member_id);
                  }

                });


                
                $('#loaderNew').hide();

                if(check_status == false){
                  
                  swal("Alert",msg, "warning");

                  //alert(response.message);
                  return false;
                }else{
                  check_status=true;
                  progressline(event, 'nom-del-link');
                  //$('#memSec1').addClass("active1");
                  $('.show_member_tab').val('1');
                  document.getElementById('memSec2').setAttribute("onclick","progressline(event, 'nom-del-link')");
                  //$('.memSec1').addClass('active');
                  //$('.memSec2').addClass('active');
                  $.ajax({
                    url: "/get_nominee_details",
                    type: "POST",
                    async: false,
                    dataType : 'json',
                    success: function(responseNominee){
                      if (responseNominee.nominee_details.relation_data != null) {
                        if(responseNominee.nominee_details.nominee_lname == '.'){
                          responseNominee.nominee_details.nominee_lname = '';
                        }
                        if(responseNominee.nominee_details.nominee_lname == ''){
                          var name = responseNominee.nominee_details.nominee_fname;
                        }else{
                          var name = responseNominee.nominee_details.nominee_fname+' '+responseNominee.nominee_details.nominee_lname;
                        } 
                        var nominee_dob = responseNominee.nominee_details.nominee_dob;
                        var nominee_no = responseNominee.nominee_details.nominee_no;
                        $('#nominee_full_name').val(name);
                        $('#nominee_dob').val(nominee_dob);
                        $('#nominee_relation').val(responseNominee.nominee_details.Relationship_of_Nominee);
                        $('#nominee_phone').val(nominee_no);                
                      } else {
                        $("#nomineeform")[0].reset();  
                      } 
                    }
                  });
                  $.ajax({
                        url: "/get_premium_abc",
                        type: "POST",
                        async: false,
                        dataType: 'json',
                        success: function (response) {
                          $('.drop_prem').html(response.html_premium);
                          $('.total_premium').html(response.total_premium);
                        }
                  });
                  $.ajax({
                      url: "/get_policy_member_details",
                      type: "POST",
                      async: false,
                      dataType: 'json',
                      success: function (response1) {
                        $('.memDetailsDiv').html(response1.html);
                      }
                    });
                  $.ajax({
                    url: "/get_review_cards",
                    type: "POST",
                    async: false,
                    dataType: 'json',
                    success: function (response) {
                      $('.memCardShow').html(response.html_card);
                    }
                  });
                }	
    					}
    				});		
			
            return false;            
        }
    });

	$(document).on("click", ".membSaveBtn", function(){		
    // alert('Testing');
		$('#memberform').valid();
	});

  $("#nominee_full_name").blur(function(){
    $(this).val(jQuery.trim($(this).val()));
  });

	$('#nomineeform').validate({
        rules: {
            nominee_full_name: {
                required: true,
                lettersonly: true,
            },
            nominee_relation: {
                required: true
            },
            nominee_dob: {
              required: true,
              validAdultDob: {
                depends: function (element) {
                  return ($('#product_id_hidden').val() == 'HERO_FINCORP') ? true : false;
                }
              },
            },
            nominee_phone:{
              required : function(element) {
                return ($('#product_id_hidden').val() == 'ABC');
              },
              validMobileNominee:true
            }
        },
        messages:{
	        nominee_full_name:{
		        required: "Nominee full name is required",
            lettersonly: "Enter only alphabates",
		        firstLastName: "Enter a valid name",
		    },
	    	nominee_relation:{
		        required: "Nominee Relation is required"
		    },
		    nominee_dob:{
		        required: "Nominee DOB is required",
		    },
	    	nominee_phone:{
		        required: "Nominee phone is required",
            maxlength: "Nominee phone required 10 digit"
		    }
        },
        submitHandler: function (form) { // for demo
          var form = $("#nomineeform").serialize();
          $.ajax({
            
					url: "/Policyproposal/submitForm1",
					type: "POST",
					async: false,
					data: form,
					dataType: 'json',
					success: function (response) {
            document.getElementById('memSec3').setAttribute("onclick","progressline(event, 'review-del-link')");
            progressline(event, 'review-del-link');
            return;
						$.ajax({
					      url: "/get_policy_member_details",
					      type: "POST",
					      async: false,
					      dataType: 'json',
					      success: function (response1) {
					      	//console.log(response1);
					      	$('.memDetailsDiv').html(response1.html);
					      	$.ajax({
						      url: "/get_nominee_details",
						      type: "POST",
						      async: false,
						      dataType: 'json',
						      success: function (response) {
						      	if (response.nominee_details.relation_data != null) {
						      		if(response.nominee_details.nominee_lname == '.'){
						      			response.nominee_details.nominee_lname = '';
						      		}
						      		if(response.nominee_details.nominee_lname == ''){
						      			var name = response.nominee_details.nominee_fname;
						      		}else{
						      			var name = response.nominee_details.nominee_fname+' '+response.nominee_details.nominee_lname;
						      		}	
						      		var nominee_dob = response.nominee_details.nominee_dob;
      								var nominee_no = response.nominee_details.nominee_no;					      		
    									$('.rev_nominee_name').html(name);
    									$('.rev_nominee_dob').html(nominee_dob);
    									$('.rev_nominee_relation').html(response.nominee_details.relation_data.fr_name);
    									$('.rev_nominee_phone').html(nominee_no);									
  							    }	else {
                      $("#nomineeform")[0].reset();  
                    } 
                document.getElementById('memSec3').setAttribute("onclick","progressline(event, 'review-del-link')");
								progressline(event, 'review-del-link');
                //$('.memSec1').addClass('active1');
                //$('.memSec2').addClass('active1');
						      }
						    });				        

					      }
					    });	
             $.ajax({
                url: "/get_review_cards",
                type: "POST",
                async: false,
                dataType: 'json',
                success: function (response) {
                  $('.memCardShow').html(response.html_card);
                }
              });
						
					}
				});		
			
            return false;            
        }
    });

	$(document).on("click",".BtnNominee",function(){
		$('#nomineeform').valid();
	})

	//get nominee details 
	$(document).on("change",".nominee_relation",function(){
    // alert('Testing');  
    let formData = $('#nomineeform').serializeArray();
    console.log(formData);
    $.ajax({
        url: "/policyproposal/populateNomineeRelation",
        type: 'post',
        dataType: 'json',
        data: formData,
        cache: false,
        clearForm: false,
        async: false,
        success: function(response) {
          console.log(response);
            if (!response) {
                return;
            }
  
            $('#nomineeform').find('[name="nominee_full_name"]').val(response.policy_member_first_name);
            //$('#nomineeform').find('[name="nominee_phone"]').val(response.policy_member_last_name);
            $('#nomineeform').find('[name="nominee_dob"]').datepicker('setDate', response.policy_member_dob);
        }
    });
    })

  function returnSelectedNominee(e){
    var radioElement = $('input[name="selectedNominee"]:checked');
    if(radioElement.length){
      if(radioElement.attr('id') == 'createNew'){
        $('#nominee_full_name').val('').removeAttr('readonly');
        $('#nominee_dob').val('').removeAttr('readonly').css({"pointer-events":""});
        $('#nominee_phone').val('');
        // $('#policy_mem_id').val('');
        $("#nomineeMultiple").modal('hide');
      } else {
        var nomineeName = radioElement.attr("data-name");
        var nomineeDob = radioElement.attr("data-dob");
        var memId = radioElement.attr("data-memId");
        $("#nomineeMultiple").modal('hide');
        $('#nominee_full_name').val(nomineeName).attr('readonly', 'true');
        // $('#policy_mem_id').val(memId).attr('readonly', 'true');
        $('#nominee_dob').val(nomineeDob).attr('readonly', 'true').css({"pointer-events":"none"});
      }
    } else {
      swal("Alert","Select atleast one option!!", "warning"); 
    }
  }






  $(".datepicker_dob").datepicker({
    dateFormat: "dd-mm-yy",
    prevText: '<i class="fa fa-angle-left"></i>',
    nextText: '<i class="fa fa-angle-right"></i>',
    changeMonth: true,
    changeYear: true,
    maxDate: 0,
    yearRange: "-100: +0",
    onSelect: function (dateText, inst) {
        $(this).val(dateText);
        //get_age_family(dateText, eValue);
    },
    onClose: function() {$(this).valid();},
});

$("#Self_dob").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        maxDate: 0,
        yearRange: "-100: +0",
        onSelect: function (dateText, inst) {
            $(this).val(dateText);
            //get_age_family(dateText, eValue);
        },
        onClose: function() {$(this).valid();},
    });



$("#Spouse_dob").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        maxDate: 0,
        yearRange: "-100: +0",
        onSelect: function (dateText, inst) {
            $(this).val(dateText);
            //get_age_family(dateText, eValue);
        },
        onClose: function() {$(this).valid();},
    });
$("#Kid1_dob").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        maxDate: 0,
        yearRange: "-100: +0",
        onSelect: function (dateText, inst) {
            $(this).val(dateText);
            //get_age_family(dateText, eValue);
        },
        onClose: function() {$(this).valid();},
    });
$("#Kid2_dob").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        maxDate: 0,
        yearRange: "-100: +0",
        onSelect: function (dateText, inst) {
            $(this).val(dateText);
            //get_age_family(dateText, eValue);
        },
        onClose: function() {$(this).valid();},
    });
$("#Kid3_dob").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        maxDate: 0,
        yearRange: "-100: +0",
        onSelect: function (dateText, inst) {
            $(this).val(dateText);
            //get_age_family(dateText, eValue);
        },
        onClose: function() {$(this).valid();},
    });
$("#Kid4_dob").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        maxDate: 0,
        yearRange: "-100: +0",
        onSelect: function (dateText, inst) {
            $(this).val(dateText);
            //get_age_family(dateText, eValue);
        },
        onClose: function() {$(this).valid();},
    });

$('#txt1').keyup(function() {
   if(this.value.length == $(this).attr('maxlength')) {
       $('#txt2').focus();
   }
});

$('#txt2').keyup(function() {
   if(this.value.length == $(this).attr('maxlength')) {
       $('#txt3').focus();
   }
});

$('#txt3').keyup(function() {
   if(this.value.length == $(this).attr('maxlength')) {
       $('#txt4').focus();
   }
});

//   captcha update - upendra - 16-10-2021
function refresh_captcha(origin){
$("#entered_captcha"+"_"+origin).val('');
ajaxindicatorstart();
$.ajax({
    url: "/refresh_captcha_abc",
    type: "POST",
    data: { 
       
    },
    async: true,
    dataType: "text",  
    cache:false,
    success: function (response) {
      ajaxindicatorstop();
      $("#captcha_image_span"+"_"+origin).html(response);
    },
  });
}

$(document).on("click", "#refresh_captcha_sendotp", function () {
  $("#entered_captcha"+"_"+"sendotp").val('');
  $("#captcha_error_sendotp").hide();
  refresh_captcha('sendotp');
});

$(document).on("click", "#refresh_captcha_resendotp", function () {
  $("#entered_captcha"+"_"+"resendotp").val('');
  $("#captcha_error_resendotp").hide();
  refresh_captcha('resendotp');
});


