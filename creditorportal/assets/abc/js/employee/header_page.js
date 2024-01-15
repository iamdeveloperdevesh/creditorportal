 $.ajax({
          url:'/check_enrollment',
          type: "POST",
		   async: false,
              dataType: "json",
              success: function (response) {
                if (response.length == 0) 
                {
                    //$("ul li .add_mem").css('pointer-events','none');
                  //$("form#policy_member_form_data button#submit").css('pointer-events','none');
                  //$("form#policy_member_form_data #add_more_form").css('pointer-events','none');
                  $("form#policy_nominee_form_data #add_more_form").css('pointer-events','none');
                  $("form#policy_nominee_form_data").css('pointer-events','none');
				  $("form#policy_nominee_form_data span#enroll_msg").html("Enrollment window is closed").css("color", "red"); 
				 
                  $("form#policy_nominee_form_data button#submit").css('pointer-events','none');
                  $("form#member_form_nominee button#nominee_submit").css('pointer-events','none');
                  $("form#member_form_nominee").css('pointer-events','none');
				 // $("form#member_form_nominee span#enroll_msg").css("color", "red");
				   $("form#member_form_nominee span#enroll_msg").html("Enrollment window is closed").css("color", "red");
				  
                }
              }
        });
