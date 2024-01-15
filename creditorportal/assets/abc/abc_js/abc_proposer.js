/*$( "#proposer_dob" ).datepicker({
            changeMonth: true,
            changeYear: true,
            maxDate:new Date()
        });*/

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
          });

function get_age_family(dateText, eValue){
    alert(dateText+"=="+eValue);

}

$(document).on("change","#salutation",function(){
    if($(this).val() == 'Mr'){
        var html = '<option value = "Male">Male</option>';
    }else if($(this).val() == '' || $(this).val() == 'Dr'){
        var html = '<option value = "">Select Gender</option><option value = "Male">Male</option><option Value = "Female">Female</option>';
    }else{
         var html = '<option Value = "Female">Female</option>';
    }
    $('#gender').html(html);
})

$('#pincode').on('keyup', function (e) {
    var pincode = $(this).val();
    if(pincode.length == 6){
        /*$.ajax({
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
        });*/

        let name = e.target.name;
		if (name == "pincode") {

			let pincode = e.target.value;

			var state_city_url = "/policyproposal/getStateCity";

			let closest_form_id = e.target.closest("form").id;

			$.ajax({
				url: state_city_url,
				data: {
					pincode: pincode,
                    source: 'customer'
				},
				type: 'post',
				dataType: 'json',
				cache: false,
				clearForm: false,
				success: function(response) {
					if (response.success) {
						let data = response.data;
						$("#" + closest_form_id + " [name='city']").val(data.CITY);
						$("#" + closest_form_id + " [name='state']").val(data.STATE);
					} else {
						displayMsg("error", "Please enter correct pincode");
						$("#" + closest_form_id + " [name='city']").val("");
						$("#" + closest_form_id + " [name='state']").val("");
					}
				}
			});
		}
    }
})

// $(document).ready(function(){
//     $("#proposer_dob").datepicker({
//             dateFormat: "dd-mm-yy",
//             prevText: '<i class="fa fa-angle-left"></i>',
//             nextText: '<i class="fa fa-angle-right"></i>',
//             changeMonth: true,
//             changeYear: true,
//             maxDate: 0,
//             yearRange: "-100: +0",
//             onSelect: function (dateText, inst) {
//                 $(this).val(dateText);
//             // get_age_family(dateText, eValue);
//             }
//         });
// })     

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
      // return this.optional( element ) || /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/.test( value );
      return this.optional( element ) || /^[\w-]+(\.[\w-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)*?\.[a-z]{2,6}|(\d{1,3}\.){3}\d{1,3})(:\d{4})?$/.test( value );
    }, 'Please enter a valid email address.');

	$.validator.addMethod("lettersonly", function(value, element) {
    	return this.optional(element) || value == value.match(/^[a-zA-Z ]*$/);
 	}, "Please enter a valid name.");	

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
                // required: true,
                // lettersonly: true
                accept:true
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
		        required: "Salutation is required",
		    },
	    	first_name:{
		        required: "First name is required"
		    },
		    // last_name:{
		    //     // required: "Last name is required",
		    //     // lettersonly: "Enter only alphabates"
		    // },
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
		        required: "Address is required"
		    },
	    	pincode:{
		        required: "pincode is required",
                pincodeonly: "Not a valid pincode"
		    },
		    city:{
		        required: "City is required"
		    },
	    	state:{
		        required: "State is required"
		    }
        },
        submitHandler: function (form) { // for demo
            $('#gender').removeAttr('disabled');
            var form = $("#proposerForm").serialize();
            $.ajax({
					url: "/Member_detail_abc/saveCustomerDetails",
					type: "POST",
					async: false,
					data: form,
					dataType: 'json',
					success: function (response) {
                        if(response.success || response.proceed){

                            if(response.member_id){

                                $('#member_id').val(response.member_id);
                            }
                            window.location.href = "/Member_detail_abc/member_detailabc";
                        }else{
                            swal("Alert",response.Msg, "warning");
                        }		
					}
				});		
			
            return false;            
        }
    });

	$(document).on("click", ".memProposerBtn", function(){		
        // debugger;
        // alert('Testing');
		$('#proposerForm').valid();
	});