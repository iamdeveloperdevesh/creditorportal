$(document).ready(function() {
	
    var son_age = 0;
	var show_delete = "true";
    var $max_age = 0;
	
	$.validator.addMethod('valid_mobile', function(value, element, param) {
        var re = new RegExp('^[7-9][0-9]{9}$');
        return this.optional(element) || re.test(value); // Compare with regular expression
    }, 'Enter a valid 10 digit mobile number');
	
    $.validator.addMethod('validateEmail', function(value, element, param) {
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        return reg.test(value); // Compare with regular expression
    }, 'Please enter a valid email address.');

	$.validator.addMethod('validate_email', function(value, element, param) {
        var reg = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i;
        return reg.test(value);
    }, 'Enter a valid email address');
	
	$(document).on("keyup", "#FirstName, #family_firstname, #family_lastname", function() {
        if ($(this).val().match(/[^a-zA-Z]/g)) {
            $(this).val($(this).val().replace(/[^a-zA-Z]/g, ""));
        }
    });

    // state from city
    $(document).on('click', '#cancel', function() {
        window.location.reload();
    });
    
	$(document).on('click', '#delete', function() {
        var result = confirm("Want to delete member?");
        if (result) {
            //Logic to delete the item
            $.ajax({
                url: "/employee/delete_family_member",
                type: "POST",
                async: false,
                dataType: "json",
                data: {
                    id: $(this).data("id")
                },
                success: function(response) {
                    if (response == true) {
                        swal({
                                title: "Warning",
                                text: "member deleted successfully",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            },
                            function() {
                                location.reload();
                            });
                        /* alert("member deleted successfully");
                           window.location.reload(); */
                    }
                }
            });
        }
    });
    
	$('#state_names').on('change', function() {
        getStatefromCity("");
    });
    
    $(document).on('click', '#edit', function() {
        $("#cancel").hide();
        $("#edit").hide();
        $("#family_details_form").css("pointer-events", "auto");
        $("#family_firstname ,#family_lastname, #family_bdate, #family_email, #family_contact, #family_flat ,#family_location,#family_street,#family_pincode,#cities,#state_names").addClass("editable");
        $("#btn_update").show();
        if (show_delete == "true") {
            $("#delete").show();
        }
    });
	
    $(document).on('click', '#members', function() {
        $.ajax({
            url: "/employee/check_member_exits_in_policy",
            type: "POST",
            data: {
                "family_id": $(this).attr("data-id")
            },
            async: false,
            dataType: "json",
            success: function(response) {
                if (response.length != 0) {
                    show_delete = "false";
                } else {
                    $("#cancel").hide();
                    $("#delete").show();
                    show_delete = "true";
                }
            }
        });
        $("#family_details_form").css("pointer-events", "none");
        $("#edit").css("pointer-events", "auto");
        $("#cancel").show();
        $("#cancel").css("pointer-events", "auto");
        $("#delete").hide();
        $("#btn_update").hide();
        $("#edit").show();
        $("#btn_update").text('Update');
        $.ajax({
            url: "/get_family_member_details",
            type: "POST",
            data: {
                "family_id": $(this).attr("data-id")
            },
            async: false,
            dataType: "json",
            success: function(response) {
                //debugger;
                if (response[0].fr_id == 1) {
                    $("#mar_date").css('display', 'block');
                    $("#spouse_mdate").val(response[0].marriage_date);
                } else {
                    $("#mar_date").css('display', 'none');
                }
                $("#delete").attr('data-id', response[0].family_id);
                $('#family_relation').val(response[0].fr_id);
                $("#family_relation").attr('disabled', 'disabled');
                $("#family_relation option[value=" + response[0].fr_id + "]").removeAttr('disabled');
                $('#family_firstname').val(response[0].family_firstname);
                $('#family_lastname').val(response[0].family_lastname);
                $('#family_bdate').val(response[0].family_dob);
                $('#family_flat').val(response[0].family_flat);
                $('#family_location').val(response[0].family_location);
                $('#family_street').val(response[0].family_street);
                $('#family_pincode').val(response[0].family_pincode);
                $('#family_contact').val(response[0].family_contact);
                $('#family_email').val(response[0].family_email);
                $("#family_id").val(response[0].family_id);
                $("#btn_update").val("Update");
                $("#family_pincode").trigger("change");
            }
        });
    });
    
	$(document).on('change', '#family_pincode', function() {
        $.ajax({
            url: "/get_state_city",
            type: "POST",
            data: {
                "pincode": $(this).val()
            },
            async: false,
            dataType: "json",
            success: function(response) {
                console.log(response);
                $("#state_names").val(response.state_name);
                $("#cities").val(response.city_name);
            }
        });
    });
    
	$(document).on("click", "#get_emp_address", function() {
        if ($(this).is(":checked")) {
            $.ajax({
                url: "/get_employee_address",
                type: "POST",
                async: false,
                dataType: "json",
                success: function(response) {
                    $("#family_flat").val(response[0].emp_address);
                    $("#family_street").val(response[0].street);
                    $("#family_location").val(response[0].location);
                    $("#family_pincode").val(response[0].emp_pincode);
                    $("#family_pincode").trigger("change");
                }
            });
        }
    });
    
	$('#family_contact').keyup(function(e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function(str) {
                return '';
            }));
        }
        return;
    });
	
    $('#family_details_form').validate({
        rules: {
            family_relation: {
                required: true
            },
            family_firstname: {
                required: true
            },
            family_lastname: {
                required: true
            },
            family_bdate: {
                required: true
            },
            family_contact: {
                valid_mobile: true

            },
            family_email: {
                validateEmail: true
            }
            /*  family_flat: {
                 required: true
             },
             family_location: {
                 required: true
             },
             family_street: {
                 required: true
             }, 
             family_city: {
                 required: true
             },
             family_state: {
                 required: true
             },
             family_pincode: {
                 required: true
             },
             family_contact: {
                 valid_mobile : true
                 
             } */
        },
        messages: {
            family_relation: "Please provide your realtion with the person.",
            family_firstname: "Please provide your First Name.",
            family_lastname: "Please provide your Last Name.",
            family_bdate: "Please provide date of birth",
            family_contact: "Please provide your valid mobile number.",
            family_email: "Please provide your Email address."
            /* family_flat: "Please provide Building and Flat No.",
            family_location: "Please provide your location.",
            family_street: "Please provide street",
            family_city: "Please provide City name.",
            family_state: "Please provide State name",
            family_pincode: "Please provide you Pincode", */
            //family_email: "Please provide your Email address."
        },
        invalidHandler: function(f, v) {},
        errorElement: 'div',
        errorPlacement: function(error, element) {
            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error);
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form, event) {
event.preventDefault();
            var spouse_date_check = "true";
            var datastring = $("#family_details_form").serialize();
            var relation = $('#family_relation option:selected').val();
            if (relation == 1) {
                var spouse_dob = $("#family_bdate").val();
                var q = spouse_dob.split("-");
                spouse_dob = new Date(q[2], q[1] - 1, q[0]);

                var marriage_date = $("#spouse_mdate").val();
                if (marriage_date != "") {
                    var z = marriage_date.split("-");
                    marriage_date = new Date(z[2], z[1] - 1, z[0]);
                }
                if (marriage_date == "") {
                    spouse_date_check = "false";
                    swal({
                        title: "Information",
                        text: "please enter marriage date",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    });


                }

                if (marriage_date < spouse_dob) {
                    spouse_date_check = "false";
                    swal({
                            title: "Information",
                            text: "marriage date cannot be greater than date of birth",
                            type: "warning",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true
                        },
                        function() {
                            //return false;

                            //location.reload();
                        });
                }
            }
            if (spouse_date_check == "true") {
                var update = $("#btn_update").val();
                if (update == "Update") {

                    var family_id = $("#family_id").val();
                    datastring += "&family_id=" + family_id
                }
                var insert = 'true';
                if (update != "Update") {
                    //debugger;

                    $.ajax({
                        type: "POST",
                        url: "/employee/check_employee_exists",
                        data: datastring,
                        async: false,
                        success: function(data) {
							
                            var data = JSON.parse(data);
                            if (data.length != 0) {
                                if (data[0].multiple_allowed == 'Y') {
                                    insert = 'true';
                                    //alert(1);
                                } else {
									
									//alert(data[0].fr_name);
									insert = 'false';
                                    /* $("#getCodeModal").modal("toggle"); */
                                    /* $("#getCode").html("you can add only 1"+data[0].fr_name); */
                                    // alert("you can add only 1"+data[0].fr_name);
                                    //alert(2);
                                    swal({
                                            title: "Information",
                                            text: "you can add only 1 " + data[0].fr_name,
                                            type: "warning",
                                            showCancelButton: false,
                                            confirmButtonText: "Ok!",
                                            closeOnConfirm: true
                                        },
                                        function() {
                                            //return false;

                                            location.reload();
                                            
                                        });
                                }
                            } else {
                                insert = 'true';
                            }
                        },
                    });
                }
                if (insert == 'true') {

                    $.ajax({
                        type: "POST",
                        url: "/employee/save_family_members",
                        data: datastring,
                        success: function(data) {
                            var response = JSON.parse(data);
                            $("#getCodeModal").modal("toggle");
                            if (response.status) {

                                //console.log(response.family_data.constructor);
                                console.log(response);
                                $("#getCode").html(response.status);
                                swal({
                                        title: "Information",
                                        text: response.status,
                                        type: "warning",
                                        showCancelButton: false,
                                        confirmButtonText: "Ok!",
                                        closeOnConfirm: true
                                    },
                                    function() {
                                        location.reload();
                                    });
                                /* alert(response.status);
                                 window.location.reload(); */
                            } else {
                                $.each(response.messages, function(key, value) {
                                    var element = $('#' + key);
                                    element.closest('div.response').find('.error').remove();
                                    element.after(value);
                                });
                            }

                        },
                    });
                }
            }
        }
    });

    // validation for pincode
    $('body').on("keyup", "#family_pincode", function(e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function(str) {
                return '';
            }));
        }
        return;
    });

    $('body').on("keyup", "#family_location", function(e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z\s]/g, function(str) {
                return '';
            }));
        }
        return;
    });

    $(document).on('change', '#family_relation', function() {
        var relation_id = $('#family_relation').val();
        if (relation_id == 1) {
            $("#mar_date").css('display', 'block');

        } else {
            $("#mar_date").css('display', 'none');
        }
        if (relation_id == 1 || relation_id == 4 || relation_id == 5 || relation_id == 6 || relation_id == 7) {
            $("#family_bdate").datepicker("destroy");
            $("#family_bdate").datepicker({
                dateFormat: "dd-mm-yy",
                prevText: '<i class="fa fa-angle-left"></i>',
                nextText: '<i class="fa fa-angle-right"></i>',
                changeMonth: true,
                changeYear: true,
                yearRange: "-100Y:-18Y",
                maxDate: "-18Y",
                minDate: "-100Y +1D"
            });
        } else {
            // alert($max_age);
            $("#family_bdate").datepicker("destroy");

            $("#family_bdate").datepicker({
                dateFormat: "dd-mm-yy",
                prevText: '<i class="fa fa-angle-left"></i>',
                nextText: '<i class="fa fa-angle-right"></i>',
                changeMonth: true,
                changeYear: true,
                yearRange: "-" + $max_age + "Y:+0",
                minDate: son_age,
                maxDate: new Date()
            });

        }
    });
	
	$("#spouse_mdate").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100Y:+0",
        maxDate: new Date(),
        minDate: "-100Y +1D"
    });
	
	$.ajax({
        url: "/get_smallest_date",
        type: "POST",
        async: false,
        dataType: "json",
        success: function(response) {
		
            son_age = response.date.split("-");

            son_age = new Date(Number(son_age[2]), Number(son_age[1]) - 1, Number(son_age[0]))

            son_age.setDate(son_age.getDate() + 1)
            console.log(son_age);
            var z = new Date();
            $max_age = z.getFullYear() - son_age.getFullYear();
            //alert($max_age);
            $("#family_relation").change();
        }
    });
	
	$.ajax({
        url: "/get_all_states",
        type: "POST",
        async: false,
        dataType: "json",
        success: function(response) {
            $('#state_names').append('<option value="">select</option>');
            for (i = 0; i < response.length; i++) {
                $('#state_names').append('<option value="' + response[i].state_id + '">' + response[i].state_name + '</option>');
            }
        }
    });
	
	$.ajax({
        url: "/get_family_details_from_employee", 
        type: "POST",
        async: false,
        dataType: "json",
        success: function(response) {
			
            $('#show_members').empty();
            for (i = 0; i < response.length; i++) {
                if (response[i].fr_name == "Spouse/Partner") {
                    response[i].fr_name = "spouse"
                }
                $('#show_members').append('<li>  <img src="/public/assets/images/new-icons/' + response[i].fr_name.toLowerCase() + '.png">  ' + response[i].fr_name + ' <span style = "cursor:pointer;" id = members name="check_members" data-id = ' + response[i].family_id + ' class="bor-dashed" > ' + response[i].family_firstname + ' <span class="width-span"></span> </span>  </li>');
            }
        }
    });
});

function getStatefromCity(city) {
	$.ajax({
		url: "/get_city_from_states",
		type: "POST",
		data: {
			"state_names": $("#state_names").val()
		},
		async: false,
		dataType: "json",
		success: function(response) {
			$('#cities').empty();
			$('#cities').append('<option>Select City</option>');
			for (i = 0; i < response.length; i++) {
				$('#cities').append('<option value="' + response[i].city_id + '">' + response[i].city_name + '</option>');
			}
			if (city) {
				$("#cities").val(city);
			}
		}
	});
}