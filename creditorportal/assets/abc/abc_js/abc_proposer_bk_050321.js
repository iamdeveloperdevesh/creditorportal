/*$( "#proposer_dob" ).datepicker({
            changeMonth: true,
            changeYear: true,
            maxDate:new Date()
        });*/
function get_age_family(dateText, eValue){
    alert(dateText+"=="+eValue);

}

$(document).on("change","#salutation",function(){
    if($(this).val() == 'Mr'){
        var html = '<option value = "Male">Male</option>';
    }else if($(this).val() == ''){
        var html = '<option value = "">Select Gender</option><option value = "Male">Male</option><option Value = "Female">Female</option>';
    }else{
         var html = '<option Value = "Female">Female</option>';
    }
    $('#gender').html(html);
})

$('#pincode').on('blur', function () {
    var pincode = $(this).val();
    $.ajax({
            url: 'get_statecity_by_pincode',
            type: 'POST',
            dataType: 'json',
            data: {pincode : pincode},
        success: function (response) {
            if(response.status == 'failure'){
                $('#pincode').val('');
                $('#city').val('');
                $('#state').val('');
                swal("Alert","Pincode is not as per master", "warning");
                return false;
            }else{
                $('#city').val(response.city);
                $('#state').val(response.state);
            }
        }
    });
})

$("#proposer_dob").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        maxDate: 0,
        yearRange: "-100: +0",
        onSelect: function (dateText, inst) {
            $(this).val(dateText);
           // get_age_family(dateText, eValue);
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
//Member details form validtion
    $.validator.addMethod("email", function(value, element) {
      // allow any non-whitespace characters as the host part
      return this.optional( element ) || /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/.test( value );
    }, 'Please enter a valid email address.');

	$.validator.addMethod("lettersonly", function(value, element) {
    	return this.optional(element) || value == value.match(/^[a-zA-Z ]*$/);
 	}, 'Please enter a valid name.');	

    $.validator.addMethod("pincodeonly", function(value,element) {
       return this.optional(element) || value == value.match(/^[1-9][0-9]{5}$/); 
    });

    jQuery.validator.addMethod("accept", function(value, element, param) {
        return this.optional(element) || value == value.match(/^[a-zA-Z ]*$/);
    }, "Please enter a valid name.");

 	$('#proposerForm').validate({
        rules: {
            salutation: {
                required: true,
            },
            first_name: {
                required: true,
                lettersonly: true
            },
            last_name: {
                accept:true
                //required: true,
                //lettersonly: true
            },
            gender: {
                required: true
            },
            email: {
                required: true,
                email: true
            },
            mobile_no: {
                required: true
            },
            proposer_dob: {
                required: true
            },
            address: {
                required: true
            },
            pincode: {
                required: true,
                pincodeonly: true
            },
            city: {
                required: true
            },
            state: {
                required: true
            },
        },
        messages:{
	        salutation:{
		        required: "salutation is required",
		    },
	    	first_name:{
		        required: "First name is required"
		    },
		    /*last_name:{
		        required: "Last name is required",
		        lettersonly: "Enter only alphabates"
		    },*/
	    	gender:{
		        required: "Gender is required"
		    },
		    email:{
		        required: "Email is required"
		    },
	    	mobile_no:{
		        required: "Mobile No is required"
		    },
            proposer_dob: {
                required: "DOB is required"
            },
		    address:{
		        required: "address is required"
		    },
	    	pincode:{
		        required: "pincode is required",
                pincodeonly: "Not a valid pincode"
		    },
		    city:{
		        required: "city is required"
		    },
	    	state:{
		        required: "state is required"
		    }
        },
        submitHandler: function (form) { // for demo
            $('#gender').removeAttr('disabled');
            var form = $("#proposerForm").serialize();
            $.ajax({
					url: "/add_proposer_details",
					type: "POST",
					async: false,
					data: form,
					dataType: 'json',
					success: function (response) {
                        if(response.status == 'success'){
                            window.location.href = "/member_detail_product_abc";
                        }else{
                            swal("Alert",response.message, "warning");
                        }				
						
					}
				});		
			
            return false;            
        }
    });

	$(document).on("click", ".memProposerBtn", function(){		
		$('#proposerForm').valid();
	});