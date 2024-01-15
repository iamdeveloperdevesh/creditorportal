
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
      url: "/get_nominee_details",
      type: "POST",
      async: false,
      dataType: 'json',
      success: function (response) {
      	console.log(response);
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
          document.getElementById('memSec2').setAttribute("onclick","progressline(event, 'nom-del-link')");
          document.getElementById('memSec3').setAttribute("onclick","progressline(event, 'review-del-link')");
    			var optionHtml = '';
    			optionHtml += '<option value="">Select Relation</option>';
    			$.each(response.nominee_relation, function(key, data){
            console.log(data.fr_id);
    				var check = "";
    				if(data.relation_code == response.nominee_details.Relationship_of_Nominee){
    					check = "selected";
    				}
  			    optionHtml += '<option data-id="'+data.nominee_id+'" value="'+data.relation_code+'" '+check+'>'+data.nominee_type+'</option>';
  			});
  			console.log(optionHtml);
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

  jQuery.validator.addMethod("maxlength", function (value, element, param) {
    console.log(value.length+'element= ' + $(element).attr('name') + ' param= ' + param )
    if (value.length == param) {
        return true;
    } else {
        return false;
    }
}, "You have reached the maximum number of characters allowed for this field.");


  $.validator.addMethod('valid_mobile_last_ten_digits', function (value, element, param) {
    var str1 = value;
    var n1 = 10;
    var newvalue = str1.substring(str1.length - n1);
    $(element).val(newvalue);
    var re = new RegExp('^[6-9][0-9]{9}$');
    return this.optional(element) || re.test(newvalue);

  }, 'Enter valid 10 digit no starting from 6 to 9');

  function checkValidation(e){
    //alert($("input[name='dc_type']:checked").val());
    if($("input[name='dc_type']:checked").val() == '' || $("input[name='dc_type']:checked").val() == undefined){
      $('#insert_proposal_button').attr("data-target","");
      swal("Alert","In order to proceed further please answer the health related questions.", "warning");     
      e.preventDefault();    
      $('#msg-del').modal('hide');
    }else if($("input[name='dc_type']:checked").val() == '1'){
      swal("Alert","This policy is not available to you or other proposed members. Please visit nearest branch for a suitable Plan.", "warning");
      e.preventDefault();
      $('#msg-del').modal('hide');
    }else{
      $('#insert_proposal_button').attr("data-target","#msg-del");
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
		    }
        },
        submitHandler: function (form) { // for demo
            //alert('valid form');
            //$('.memSec2').trigger('click');
            $('#loaderNew').show();
            var form = $("#memberform").serialize();
            $.ajax({
    					url: "/add_member_details",
    					type: "POST",
    					async: false,
    					data: form,
    					dataType: 'json',
    					success: function (response) {
                $('#loaderNew').hide();
                if(response.status == 'error'){
                  swal("Alert",response.message, "warning");
                  //alert(response.message);
                }else{

                  progressline(event, 'nom-del-link');
                  //$('#memSec1').addClass("active1");
                  $('.show_member_tab').val('1');
                  document.getElementById('memSec2').setAttribute("onclick","progressline(event, 'nom-del-link')");
                  //$('.memSec1').addClass('active');
                  //$('.memSec2').addClass('active');
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
		$('#memberform').valid();
	});

	$('#nomineeform').validate({
        rules: {
            nominee_full_name: {
                required: true,
                lettersonly: true
            },
            nominee_relation: {
                required: true
            },
            nominee_dob: {
                required: true
            },
            nominee_phone: {
                required: true,
                maxlength: 10,
                valid_mobile_last_ten_digits : true
            },
        },
        messages:{
	        nominee_full_name:{
		        required: "Nominee full name is required",
		        lettersonly: "Enter only alphabates"
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
            //alert('valid form');
            //$('.memSec2').trigger('click');
            var form = $("#nomineeform").serialize();
            $.ajax({
					url: "/add_nominee_details",
					type: "POST",
					async: false,
					data: form,
					dataType: 'json',
					success: function (response) {
							
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
						      	console.log(response);
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
		var emp_id = $('.emp_id').val();
    var relation_id = $(this).val();
    //alert(relation_id+$(this).val()+$(this).data('id'));
		$.ajax({
			url: "/get_family_details_from_relationship",
			type: "POST",
			data: {
				relation_id: relation_id,
				emp_id: emp_id
			},
			async: false,
			dataType: "json",
			success: function (response) {
        if(response.family_data.length != ''){
            $('#nominee_full_name').val(response.family_data[0].policy_member_first_name);
            $('#nominee_dob').val(response.family_data[0].policy_mem_dob);
        }else{
            $('#nominee_full_name').val('');
            $('#nominee_dob').val('');
            //$('#nominee_phone').val('');
        }
        
			
      
			}
		});
	})

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
        }
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
        }
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
        }
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
        }
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
        }
    });