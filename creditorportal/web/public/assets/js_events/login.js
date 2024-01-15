//  $("#exampleModalCenter").modal('show');
// $("body").css({
// "visibility": "hidden"
// });

// $("#exampleModalCenter").css({
// "visibility": "initial"
// })

//updated by upendra for DO login

$(document).ready(function () {
	
	
	$("#ajaxHeader").prepend("<marquee behavior='alternate' style=\"font-size: 12px; font-weight: bold; color: red;\"> Only following Special characters allowed : <table style='display: inline;vertical-align: middle;'><tr><td style='border: 2px solid #dddddd;padding: 0px 2px 0px 2px'>0 to 9</td><td style='border: 2px solid #dddddd;padding: 0px 2px 0px 2px'>a to z</td><td style='border: 2px solid #dddddd;padding: 0px 2px 0px 2px'>A to Z</td><td style='border: 2px solid #dddddd;padding: 0px 2px 0px 2px'>-</td><td style='border: 2px solid #dddddd;padding: 0px 2px 0px 2px'>@</td><td style='border: 2px solid #dddddd;padding: 0px 2px 0px 2px'>%</td><td style='border: 2px solid #dddddd;padding: 0px 2px 0px 2px'>_</td><td style='border: 2px solid #dddddd;padding: 0px 2px 0px 2px'>,</td><td style='border: 2px solid #dddddd;padding: 0px 2px 0px 2px'>.</td></tr></table></marquee>");
	//var validation_regex = new RegExp("^[0-9a-zA-Z\-@%_,.&*]");
	var validation_regex = new RegExp("^[0-9a-zA-Z\-@#%_,.&*]");
	$("input").attr("autocomplete", "off");
	// $("input").attr("maxlength", "255");
	$(document).on("keypress", "input", function (event) {
		validate_input_characters(event);
	});
	$(document).on("paste", "input", function (event) {  
		validate_input_characters_paste(event);
	});
	//setTimeout used sinnce commonLayout takes time to load
	setTimeout(function () {
		$("input").attr("autocomplete", "off");
		// $("input").attr("maxlength", "255");
	}, 2000);

	function validate_input_characters(event) {
		let inputs = Array.from(document.querySelectorAll('[id^=chassis_number]')).map(({id}) => id);
		if (inputs.length) {
			return true;
		}
		
		if (event.keyCode == '13' || event.keyCode == '32') {
			return true;
		}
		// var validation_regex = new RegExp("^[0-9a-zA-Z\-!@#$%&*?_]");
		// var validation_regex = new RegExp("^[0-9a-zA-Z\-@%_,.]");
		var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
		if (!validation_regex.test(key)) {
			event.preventDefault();
			return false;
		}
	}

	function validate_input_characters_paste(event) {
		let inputs = Array.from(document.querySelectorAll('[id^=chassis_number]')).map(({id}) => id);
		if (inputs.length) {
			return true;
		}
		var pastedData = event.originalEvent.clipboardData.getData('text');
        var inputType = event.originalEvent.input;

        // console.log(event.originalEvent);

        // console.log('Coming');
        // console.log(inputType);

		var validation_regex = new RegExp("^[0-9a-zA-Z\-@%_,.]");
		if (!validation_regex.test(pastedData)) {
            console.log('#'+event.target.id);
            alert("Special characters found in pasted string");
			// window.ajaxStatus.showStatus("Special characters found in pasted string ");
			// setTimeout(function () { window.ajaxStatus.hideStatus() }, 2000);
            $('#'+event.target.id).val('');
			event.preventDefault();
			return false;
		}
	}
	
	
	
});

$('#agent_type').on('change', function () {
    if (this.value == "2") {
        $("#digital_role_id").show();
    } else if (this.value == "1") {
        $("#digital_role_id").hide();
    }
});

$('input[type=radio][name=digital_role]').change(function () {
    if (this.value == 'agent') {
        $("#agent_do_lable").html("Agent-Code");
        $("#agent_type option[value='3']").remove();
        $("<option/>").val("2").text("Digital Portal").appendTo("#agent_type");
        $("#agent_type").val("2");
    }
    else if (this.value == 'do') {
        $("#agent_do_lable").html("Digital Officer Code");
        $("#agent_type option[value='2']").remove();
        $("<option/>").val("3").text("Digital Portal").appendTo("#agent_type");
        $("#agent_type").val("3");
    }
});
//end - updated by upendra for DO login

// var owl = $('.owl-carousel');
// owl.owlCarousel({
//     items: 4,
//     loop: true,
//     margin: 10,
//     autoplay: true,
//     autoplayTimeout: 2000,
//     autoplayHoverPause: true
// });



var slideIndex = 0;
showSlides();

function showSlides() {
    var i;
    var slides = document.getElementsByClassName("mySlides");

    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    slideIndex++;
    if (slideIndex > slides.length) { slideIndex = 1 }
    for (i = 0; i < slides.length; i++) {
        slides[i].className = slides[i].className.replace(" active", "");
    }
    //   slides[slideIndex-1].style.display = "block";  
    //   slides[slideIndex-1].className += " active";
    setTimeout(showSlides, 3000); // Change image every 2 seconds
}




$(window).scroll(function () {
    var headerScroll = $(this).scrollTop();
    if (headerScroll > 0.1) {
        $('header').addClass('active');
    }
    else {
        $('header').removeClass('active');
    };
});

function modalform(inputform) {
    var i;
    var x = document.getElementsByClassName("modal-form");
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
    }
    document.getElementById(inputform).style.display = "block";
}

$("document").ready(function () {
    // alert('hii');
    $.ajax({
        type: "POST",
        url: "/tls_get_login_details",
        data: {
            onloading_page: "1"
        },
        async: false,
        success: function (data) {
            // debugger;
            data = JSON.parse(data);
            if (data.show_captcha == 1) {
                $('.agent_captcha').show();
                $('#entered_captcha_agent').removeClass('ignore');
            } else {
                $('.agent_captcha').hide();
                $('#entered_captcha_agent').addClass('ignore');
            }
        },
        error: function () {
            swal('Something went Wrong');
        }
    });
	 let encryption = new Encryption();
     var nonceValue = $('#enckey').val();
	 var login_load = $("#login_load").val();
     var enctypted_load = encryption.encrypt(login_load, nonceValue);

    $.ajax({
        type: "POST",
        url: "/get_login_details",
        data: {
            onloading_page: enctypted_load
        },
        async: false,
        success: function (data) {
            // debugger;
            data = JSON.parse(data);
            if (data.show_captcha == 1) {
                $('.admin_captcha').show();
                $('#entered_captcha').removeClass('ignore');
            } else {
                $('.admin_captcha').hide();
                $('#entered_captcha').addClass('ignore');
            }
        },
        error: function () {
            swal('Something went Wrong');
        }
    });

    $("#forgotPassword").validate({
        ignore: ".ignore",
        rules: {
            email: {
                required: true,
                email: true

            }
        },
        messages: {
            broker_email: "Please Enter Email",
        },
        invalidHandler: function (f, v) {

        },
        errorElement: 'div',
        errorPlacement: function (error, element) {

            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error);
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function () {
            var datastring = $("#forgotPassword").serialize();

            $.ajax({
                type: "POST",
                url: "/forgotPassword",
                data: datastring,
                async: false,
                success: function (data) {
                    if (data == 1) {
                        swal("", "Password reset instructions is send to you by Email.");
                    } else {
                        swal("", "Email Not Found");
                    }
                },
                error: function () {
                    swal('Something went Wrong');
                }
            });
        }
    });

    $('#agent_form').validate({
        ignore: ".ignore",
        rules: {
            agent_code: {
                required: true,
            },
            agent_pwd: {
                required: true
            },
            agent_type: {
                required: true
            },
            entered_captcha_agent: {
                required: true
            }
        },
        messages: {
            employee_email: "Please enter Agent code",
            employee_pwd: "Please provide correct password.",
            entered_captcha_agent: "Please enter captcha text."
        },
        invalidHandler: function (f, v) { },
        errorElement: 'div',
        errorPlacement: function (error, element) {
            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error);
            } else {
                error.insertAfter(element);
            }
        },

        submitHandler: function () {
            let encryption = new Encryption();
            var nonceValue = $('#enckey').val();

            var datastring = $("#agent_form").serialize();
            // console.log(datastring);
            var newData = datastring.split('&');

            var newArr = jQuery.map(newData, function (val, index) {
                var newDataVal = val.split('=');
                return newDataVal;
            });

            var getData = {};
            var getCount = newArr.length / 2;
            var z = 0;
            var y = 1;
            var key;
            for (var i = 0; i <= getCount; i++) {
                key = newArr[z];
                var values = newArr[y];
                if (key == 'enckey') {
                    getData[key] = nonceValue;
                } else {
                    getData[key] = encryption.encrypt(values, nonceValue);
                }
                z = z + 2;
                y = y + 2
            }
            var enctypted = encryption.encrypt(datastring, nonceValue);
            var decrypted = encryption.decrypt(enctypted, nonceValue);
            var responseEncryption = 'xLoiaASsfnjAJLFJLLjwsjlw';

            $.ajax({
                type: "POST",
                url: "/tls_get_login_details",
                data: getData,
                async: false,
                success: function (data, textStatus, xhr) {
                    data = JSON.parse(data);



                    if (data.show_captcha == 1) {
                        $('.agent_captcha').show();
                        $('#entered_captcha_agent').removeClass('ignore');
                    } else {
                        $('.agent_captcha').hide();
                        $('#entered_captcha_agent').addClass('ignore');
                    }
                    if (data.success == 1) {
                        switch (xhr.status) {
                            case 202:

                                window.location.href = data.msg;
                                return;

                            case 200:
                                swal('Something went Wrong');
                        }

                    } else {
                        swal(data.msg);
                        refresh_captcha_agent();
                        //swal("Incorrect Id or Password");
                    }
                },
                error: function () {
                    swal('Something went Wrong');
                }
            });
        }
    });

    /*    $('#employee_form').validate({
            ignore: ".ignore",
            rules: {
                employee_email: {
                    required: true,
    
                },
                employee_pwd: {
                    required: true
                }
            },
            messages: {
                employee_email: "Please enter the valid e-mail address",
                employee_pwd: "Please provide correct password."
    
            },
            invalidHandler: function (f, v) {
    
            },
            errorElement: 'div',
            errorPlacement: function (error, element) {
    
                var placement = $(element).data('error');
                if (placement) {
                    $(placement).append(error);
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function () {
                var datastring = $("#employee_form").serialize();
    
                $.ajax({
                    type: "POST",
                    url: "/get_login_details",
                    data: datastring,
                    async: false,
                    success: function (data) {
                        if (data == 1) {
                            location.replace("/employee/home");
                        } else {
                            swal("Incorrect Id or Password");
                        }
                    },
                    error: function () {
                        swal('Something went Wrong');
                    }
                });
            }
        });*/

    /*    $('#employer_form').validate({
            ignore: ".ignore",
            rules: {
                employer_email: {
                    required: true
                },
                employer_pwd: {
                    required: true
                }
            },
            messages: {
                employee_email: "Please enter the valid e-mail address",
                employee_pwd: "Please provide correct password."
    
            },
            invalidHandler: function (f, v) {
    
            },
            errorElement: 'div',
            errorPlacement: function (error, element) {
                var placement = $(element).data('error');
                if (placement) {
                    $(placement).append(error);
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function () {
                var datastring = $("#employer_form").serialize();
                datastring = datastring.replace(/employer_/g, "employee_");
    
                $.ajax({
                    type: "POST",
                    url: "/get_login_details",
                    data: datastring,
                    async: false,
                    success: function (data) {
                        if (data == 1) {
                            location.replace("/employer/home");
                        } else {
                            swal("Incorrect Id or Password");
                        }
    
                    },
                    error: function () {
                        swal('Something went Wrong');
                    }
                });
            }
        });*/


    $('#account_verify').validate({
        ignore: ".ignore",
        rules: {
            employer_email: {
                required: true
            },
            employer_pwd: {
                required: true
            }
        },
        messages: {
            employee_email: "Please enter username",
            employee_pwd: "Please provide correct password"

        },
        invalidHandler: function (f, v) { },
        errorElement: 'div',
        errorPlacement: function (error, element) {
            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error);
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function () {
            var datastring = $("#account_verify").serialize();


            datastring = datastring.replace(/employer_/g, "employee_");
			

            $.ajax({
                type: "POST",
                url: "/get_account_login_details",
                data: datastring,
                dataType: 'json',
                success: function (data) {
                    // console.log(data);
                    if (data.success == 1) {
                        // swal("Successfull");						
                        window.location.href = data.mgs;
                        //location.replace(data.mgs);
                    } else {
                        swal("Incorrect Id or Password");
                    }

                },
                error: function () {
                    swal('Something went Wrong');
                }
            });
        }
    });


    $('#employer_form').validate({
        ignore: ".ignore",
        rules: {
            employer_email: {
                required: true
            },
            employer_pwd: {
                required: true
            },
            entered_captcha: {
                required: true
            }
        },
        messages: {
            employee_email: "Please enter the valid e-mail address",
            employee_pwd: "Please provide correct password.",
            entered_captcha: "Please enter captcha text."
        },
        invalidHandler: function (f, v) {

        },
        errorElement: 'div',
        errorPlacement: function (error, element) {
            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error);
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function () {
			
           debugger;
			let encryption = new Encryption();
            var nonceValue = $('#enckey').val();
			var employee_pwd = $('#employee_pwd').val();
			var employee_email = $('#employee_email').val();
			var entered_captcha = $('#entered_captcha').val();
            var employer_pwd = encryption.encrypt(employee_pwd, nonceValue);
			 var employer_email = encryption.encrypt(employee_email, nonceValue);
if(entered_captcha !=''){
var entered_captcha = encryption.encrypt(entered_captcha, nonceValue);}



          //  datastring = datastring.replace(/employer_/g, "employee_");
   

		
            $.ajax({
                type: "POST",
                url: "/get_login_details",
                data: {'nonceValue':nonceValue,'employee_pwd':employer_pwd,'employee_email':employer_email,'entered_captcha':entered_captcha},
                async: false,
                success: function (data) {
                   
                    data = JSON.parse(data);
                    if (data.show_captcha == 1) {
                        $('.admin_captcha').show();
                        $('#entered_captcha').removeClass('ignore');
                    } else {
                        $('.admin_captcha').hide();
                        $('#entered_captcha').addClass('ignore');
                    }
                    if (data.success == 1) {
                        window.location.href = data.mgs;
                        
                    } else {
                        
                        swal(data.msg);
                        refresh_captcha();
                    }

                },
                error: function () {
                    swal('Something went Wrong');
                }
            });
        }
    });

    //   captcha update 
    function refresh_captcha() {
        $("#entered_captcha").val('');
        //ajaxindicatorstart();
        $.ajax({
            url: "/refresh_captcha_login",
            type: "POST",
            data: {

            },
            async: true,
            dataType: "text",
            cache: false,
            success: function (response) {
                // ajaxindicatorstop();
                // alert(response);
                $("#captcha_image_span").html(response);
                $("#entered_captcha").val('');

            },
        });
    }
    setInterval(refresh_captcha, 120000);

    $("#refresh_captcha").on("click", function () {
        refresh_captcha();
    });

    $("#refresh_captcha_agent").on("click", function () {
        refresh_captcha_agent();
    });

    function refresh_captcha_agent() {
        // debugger;
        $("#entered_captcha_agent").val('');
        //ajaxindicatorstart();
        $.ajax({
            url: "/refresh_captcha_login",
            type: "POST",
            data: {

            },
            async: true,
            dataType: "text",
            cache: false,
            success: function (response) {
                // ajaxindicatorstop();
                // alert(response);
                $("#captcha_image_span_agent").html(response);
                $("#entered_captcha_agent").val('');
            },
        });
    }

});