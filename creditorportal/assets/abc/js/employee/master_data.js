  $.validator.addMethod(
        "validateEmail",
        function (value, element, param) {
            if (value.length == 0) {
                return true;
            }
            var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
            return reg.test(value); // Compare with regular expression
        },
        "Please enter a valid email address."
    );
  
  $("#validate_form_email").validate({
        ignore: ".ignore",
        rules: {
			email: {
                validateEmail: true,
               required: true
            },
		lead_id_new: {
		  required: true,
		 },
		 
        },
  
        submitHandler: function (form) {

						var all_data = $("#validate_form_email").serialize();
						$.ajax({
							type: "POST",
							url: "/update_email_data",
							data: all_data,
							dataType: "json",
							success: function (result) {
								if(result == '1'){
									$("#lead_id_new").val('');
									$("#email").val('');
									alert('Data Update Success');
								}
							}
						});
                }
            });
  
  $("#IMDCode, #PINCODE, #lead_id, #lead_id_new").keyup(function (e) {
	  
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

	 });
	

	
$.validator.addMethod(
    "validate_imd_code",
    function (value, element, param) {
			var count = 0;
            var BranchCode = $("#BranchCode").val();
            var product_type = $("#product_type").val();
			
                $.ajax({
                        url: "/check_imd_exist",
                        type: "POST",
                        async: false,
                        data:{'BranchCode':BranchCode,'product_type':product_type},
                        dataType: "json",
                        success: function (response) {
                           if(response == 1) {
                           count = 1;    
                           }else{
							count--;
						   }
                        }
                });
          
			if(count > 0)
			{
			return false;
			}
			else
			{
			return true;
			}
    },
    "BranchCode already exist"
  );
  
  function add_data(){
	  $('#validate_form').submit();
  }
  

   $("#validate_form").validate({
        ignore: ".ignore",
        rules: {
		BranchCode: {
		  required: true,
		  validate_imd_code:true
		  },
		 IMDCode: {
		  required: true,
		  }, 
		  product_type: {
		  required: true,
		  }, 
        },
  
        submitHandler: function (form) {

						var all_data = $("#validate_form").serialize();
						$.ajax({
							type: "POST",
							url: "/save_master_data",
							data: all_data,
							dataType: "json",
							success: function (result) {
								if(result != ''){
									
									$("#BranchCode").val('');
									$("#IMDCode").val('');
									alert('Data Add Success');
								}
							}
						});
                }
            });
			
function add_data_new(){
	  $('#validate_form_new').submit();
  }
			
$.validator.addMethod(
    "validate_pin_code",
    function (value, element, param) {
			var count = 0;
            var PINCODE = $("#PINCODE").val();
			
                $.ajax({
                        url: "/check_pincode_exist",
                        type: "POST",
                        async: false,
                        data:{'PINCODE':PINCODE},
                        dataType: "json",
                        success: function (response) {
                           if(response == 1) {
                           count = 1;    
                           }else{
							count--;
						   }
                        }
                });
          
			if(count > 0)
			{
			return false;
			}
			else
			{
			return true;
			}
    },
    "Pincode already exist"
  );
  
  $("#validate_form_new").validate({
        ignore: ".ignore",
        rules: {
		PINCODE: {
		  required: true,
		  validate_pin_code:true
		  },
        STATE: 
		 {
		  required: true,
		  },
		 CITY: 
		  {
		  required: true,
		  },
		  STANDARD_ZONE: 
		  {
		  required: true,
		  },
		  ABHI_ZONE_MAP: 
		  {
		  required: true,
		  },
		   },
  
        submitHandler: function (form) {

						var all_data = $("#validate_form_new").serialize();
						$.ajax({
							type: "POST",
							url: "/save_master_data_new",
							data: all_data,
							dataType: "json",
							success: function (result) {
								if(result != ''){
									
									$("#PINCODE").val('');
									$("#STATE").val('');
									$("#CITY").val('');
									$("#STANDARD_ZONE").val('');
									$("#ABHI_ZONE_MAP").val('');
									$("#STATE_CODE").val('');
									alert('Data Add Success');
								}
							}
						});
                }
            });
			
$("#validate_form_lead").validate({
        ignore: ".ignore",
        rules: {
		lead_id: {
		  required: true,
		 },
		 
        },
  
        submitHandler: function (form) {

						var all_data = $("#validate_form_lead").serialize();
						$.ajax({
							type: "POST",
							url: "/update_proposal_data",
							data: all_data,
							dataType: "json",
							success: function (result) {
								if(result == '1'){
									$("#lead_id").val('');
									alert('Data Update Success');
								}
							}
						});
                }
            });

$("#validate_form_payment").validate({
        ignore: ".ignore",
        rules: {
		lead_id: {
		  required: true,
		 },
		 
        },
  
        submitHandler: function (form) {

						var all_data = $("#validate_form_payment").serialize();
						$.ajax({
							type: "POST",
							url: "/update_payment_link",
							data: all_data,
							dataType: "json",
							success: function (result) {
								if(result == '1'){
									$("#lead_id").val('');
									alert('Data Update Success');
								}
							}
						});
                }
            });