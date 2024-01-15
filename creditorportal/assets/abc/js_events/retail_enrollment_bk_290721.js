
var show_active_based_reload = false;
var current_li_active = '';
var google_analytics;
function active_while_reload() {



    current_li_active = $(".progressbar-step li.active")[0];
    var zz = $(current_li_active).attr("id");
    localStorage.setItem('active', zz);

}
function call_ga_script(source){

	if(window.location.pathname == '/retail_enrollment'){
	if(source == 'self_view'){
		dataLayer.push({
'page': 'insurance/abhi-insurance/personal-details',
'event': 'event_Post_Login_Abhi_Insurance',
'category': 'Insurance',
'action': 'Step 2 | Abhi Insurance | Personal Details',
'label': 'Persoanl Details',
'application-id-abhi-insurance': $("#lead_id_hidden").val(),
});
	}
	if(source == 'show_nominee'){
		dataLayer.push({
'page': 'insurance/abhi-insurance/nominee-details',
'event': 'event_Post_Login_Abhi_Insurance',
'category': 'Insurance',
'action': 'Step 3 | Abhi Insurance | Nominee Details',
'label': 'Nominee Details',
'application-id-abhi-insurance': $("#lead_id_hidden").val(),
});
	}
	if(source == 'summary_view'){
		dataLayer.push({
'page': 'insurance/abhi-insurance/application-review',
'event': 'event_Post_Login_Abhi_Insurance',
'category': 'Insurance',
'action': 'Step 4 | Abhi Insurance | Review Application',
'label': 'Review Application viewed',
'application-id-abhi-insurance': $("#lead_id_hidden").val(),
});
		
	}
	}
}

function emptyfield() {
    if ($('input[name=dc_type12]:checked').val() != '0') {


        if (!$("#disease_disclaimer").val()) {
            if ($("#diclaimer_error").length == 0) {
                $("#disease_disclaimer").after('<label id="diclaimer_error" class="error" for="diclaimer_error">This field is required.</label>');

            }

            return false;
        } else {
            $("#diclaimer_error").remove();
        }
    } else {
        $("#diclaimer_error").remove();
    }

    $('#disclaimer_new').hide();

    $("#disclaimer_content").html($("#disease_disclaimer").val());
    $("#disclaimer_content").show();
    $("#save_employee111").click();

}



$(document).ready(function () {
	
	/*succes page payment google analytics*/
		if(window.location.pathname == '/r_verify' || window.location.pathname == '/payment_success_view_call' ){
	console.log('donepaymentga');
	/*google analytics on disease modal show */
	
dataLayer.push({
'page': 'insurance/abhi-insurance/insurance-success',
'event': 'event_Post_Login_Abhi_Insurance_Success',
'category': 'Insurance',
'action': 'Step 6 | Abhi Insurance | Success',
'label': 'Successfully Booked the Insurance Policy',
'dim30':$("#policy_no_hidden").val(),
'application-id-abhi-insurance': $("#lead_id_hidden").val(),
});
 	
	
}
	

    $('#emp_form_detais').on('keyup change', 'input, select, textarea', function () {

        $("#insert_proposal").css("float", "");
        $('#insert_proposal').html("Please save communication details");
        $('.span_cover').hide();
        $('#insert_proposal_button').attr("disabled", true);
    });



});



$(window).bind('beforeunload', function () {
    active_while_reload();

});

var ask_confirmation_web = false;
var ask_confirmation_mobile = false;
function close_confirmation() {
    $("#yes-no-chronic").hide();



}

function ask_confirmtion() {

    var zz = $("#yes-no-chronic").data("check");

    setTimeout(function () {
        $("#close_confirm").addClass("active");
        $("#ask_confirm").removeClass("active");

    }, 300);

    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        ask_confirmation_mobile = true;
        reset_mobile(zz);

    } else {
        ask_confirmation_web = true;
        modal_dismiss(zz);

    }




}



var ob = {};
var z = {};
var z2 = {};
var this1 = '';
var this3 = '';
var relationss = '';
var checkbox_check = {};
var go = true;
var go_ahead_self = true;

jQuery.validator.addMethod("checkspace_nominee", function(value, element, params) {
	var z = value.trim();
	if(!z){
		return false;
	}
	
	
  return true;
}, jQuery.validator.format("Please enter nominee name"));

$.validator.addMethod('validateEmail', function (value, element, param) {
    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    return reg.test(value.trim());
}, 'Please enter a valid Email Address.');

function DeleteUnsavedImages() {
    $("#save_button").prop("disabled", false);
}
function close_modal() {
    $('#show_messages').modal('toggle');
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {

        var reload_true = $('#reload_check').attr('data-info');
        if (reload_true == 'mobile') {
            $('#reload_check').attr('data-info', '');
            window.location.reload();
        }

    } else {

        var reload_true = $('#reload_check').attr('data-info');
        if (reload_true == 'web') {

            $('#reload_check').attr('data-info', '');
            window.location.reload();
        }

    }



}
function sad_icon(texttoshow, sorryyesno) {

    var sorryyesno = sorryyesno || true;

    if (!sorryyesno) {
        $("#sorry_yes_no").html("")

    } else {
        $("#sorry_yes_no").html("");
        $("#sorry_yes_no").html("Sorry");
    }
    $("#sadicon_text").html("");
    $("#sadicon_text").html(texttoshow);
    $("#show_messages").show();

}


function reset_nominee() {


    $("#nominee_form").trigger("reset");
}
function reset_mobile(rel2) {

    var show_text_mobile = false;
    $('#' + rel2 + '_mobile_modal input[type=checkbox]').each(function () {
        if ($(this).prop('checked')) {
            show_text_mobile = true;


        }

    });

    if (show_text_mobile) {
        $("#yes-no-chronic_text").html("Do you want to exit");
    } else {
        $("#yes-no-chronic_text").html("No disease selected !");

    }



    if ($('#yes-no-chronic').is(':visible')) {

        $("#yes-no-chronic").hide();
    } else {
        $("#yes-no-chronic").show();
    }



    $("#yes-no-chronic").data("check", rel2);

    if (!ask_confirmation_mobile) {

        return false;
    }




    $('#' + rel2 + '_mobile_modal input[type=checkbox]').each(function () {

        $(this).prop("checked", false);
        var accordian = $(this).attr("id").split("_");

        $("#" + accordian[1] + "_" + accordian[0] + "_mobile_accordian").removeClass("show");
        var check = $(this).attr("id") + "_radio2";
        $('#' + check).prop("checked", true);

    });

    $('#' + rel2 + '_mobile_modal').modal("hide");

    ask_confirmation_mobile = false;
}
function mobile_check_yes_no_again(relation) {


    var z2 = false;
    var go_to_cancel_mobile = false;
    $('#' + relation + '_mobile_modal input[type=checkbox]').each(function () {


        if ($(this).prop('checked')) {
            go_to_cancel_mobile = true;


        }


        var check = $(this).attr("id") + "_radio";

        if ($('input[name="' + check + '"]:checked').val() == 1) {
            z2 = true;
        }




    });

    if (z2) {
        $("#modal-frown").modal("show");
    } else {

        if (!go_to_cancel_mobile) {

            reset_mobile(relation);
        } else {
            $('#' + relation + '_mobile_modal').modal("hide");
        }


    }






}
function show_message_noneditable(rel) {



    if (rel == 'Self') {

        sad_icon("Please contact Axis bank to change your details");





    }
}
function edit_address() {
    $("#emp_pincode").blur();
    $("#employee_div").hide();
    $("#edit_employee").show();




    setTimeout(function () {


        if (!$("#accordion33").hasClass("show")) {

            $('a[href="#accordion33"]').removeClass('collapsed');
            $("#accordion33").addClass("show");
        }

    }, 500);



}
function redirect_dashboard() {

    var y = window.location.href.replace("retail_enrollment", "retail_dashboard");
    location.replace(y);


}
function change_yes_no_mobile(relation, this4) {
	
	if(window.location.pathname == '/retail_enrollment'){
	
	/*google analytics on disease modal show */
	
	dataLayer.push({
'page': 'insurance/abhi-insurance/personal-details/chronic-diease-pop-up/',
'event': 'event_Post_Login_Abhi_Insurance',
'category': 'Insurance',
'action': 'Step 2a | Abhi Insurance | Personal Details | Chronic Disease Pop-up',
'label': 'Chronic Disease Pop-up',
'application-id-abhi-insurance': $("#lead_id_hidden").val(),
});
 	
	
}

    this3 = this4;
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {


        $('#' + relation + '_mobile_modal').unbind();
        $('#' + relation + '_mobile_modal').on('hidden.bs.modal', function () {

            var z2 = false;
            $('#' + relation + '_mobile_modal input[type=checkbox]:checked').each(function () {

                z2 = true;



            })


            if (!z2) {
                var name = $(this3).attr("name");
                $('input[type="radio"][name="' + name + '"]').last().prop('checked', true);



            } else {


            }


            go_ahead_self = false;
            $("#save_button").click();
        });

    }



}
function show_error_on_yes_changed(object322) {
	

    $("#modal-frown").modal("show");

}
function show_error_on_yes(object2) {

    var rel = object2;
    var z = "." + rel + "_modal";
    var z2 = false;
    $('.' + rel + '_modal input[type=radio]:checked').each(function () {
        var check_accordian = $(this).attr("name").split("_");
        if ($("#" + check_accordian[1] + "_" + check_accordian[0] + "_checkbox").is(":checked")) {


            if ($('input[name="' + check_accordian[0] + '_' + check_accordian[1] + '"]:checked').val() == 1) {
                z2 = true;
            }



        }



    })

    if (z2) {
			if(window.location.pathname == '/retail_enrollment'){
	dataLayer.push({
'page': 'insurance/abhi-insurance/personal-details/chronic-diease-pop-up/policy-not-available',
'event': 'event_Post_Login_Abhi_Insurance',
'category': 'Insurance',
'action': 'Step 2b | Abhi Insurance | Personal Details | Chronic Disease Sorry Pop-up',
'label': 'Policy Not Available to this Member',
'application-id-abhi-insurance': $("#lead_id_hidden").val(),
});
	}
        $("#modal-frown").modal("show");
    } else {

        var go_to_cancel = false;
        $('.' + rel + '_modal input[type=checkbox]').each(function () {
            if ($(this).prop('checked')) {
                go_to_cancel = true;


            }
        });


        if (!go_to_cancel) {

            modal_dismiss(rel);

        } else {
            $(z).modal("hide");
        }



    }





}
function modal_dismiss(relation) {

    var show_text_web = false;

    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {


    } else {
        $('.' + relation + '_modal input[type=checkbox]').each(function () {
            if ($(this).prop('checked')) {
                show_text_web = true;


            }


        });


        if (show_text_web) {
            $("#yes-no-chronic_text").html("Do you want to exit");
        } else {
            $("#yes-no-chronic_text").html("No disease selected !");

        }
    }
















    if ($('#yes-no-chronic').is(':visible')) {

        $("#yes-no-chronic").hide();
    } else {
        $("#yes-no-chronic").show();
    }



    $("#yes-no-chronic").data("check", relation);

    if (!ask_confirmation_web) {

        return false;
    }











    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        $('#' + relation + '_mobile_modal input[type=checkbox]:checked').each(function () {

            $(this).prop("checked", false);



        })

        go_ahead_self = false;



        ask_confirmation = false;
        $("#save_button").click();
    } else {
        var sss = $("#" + relation + "_form").find("button")[1];
        var sss1 = $("#" + relation + "_form").find("button")[0];


        $(sss).addClass("active");
        $(sss1).removeClass("active");



        $('.' + relation + '_modal input[type=checkbox]').each(function () {
            $(this).prop('checked', false);

            var uncheck_accordian = $(this).attr('id').split("_");

            $('input[name="' + uncheck_accordian[1] + '_' + uncheck_accordian[0] + '"]').prop('checked', true);

            var acc = uncheck_accordian[0] + "_" + uncheck_accordian[1] + "_show_radio";

            $("#" + acc).css("display", "none");


        });


        $("." + relation + "_modal").modal("hide");
        ask_confirmation_web = false;

    }



}



function reset_accordian(this22, relation) {



    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        $('#' + relation + '_mobile_modal input[type=checkbox]:checked').each(function () {

            $(this).prop("checked", false);



        })

        go_ahead_self = false;



        $("#save_button").click();
    } else {
        var sss = $("#" + relation + "_form").find("button")[1];
        var sss1 = $("#" + relation + "_form").find("button")[0];


        $(sss).removeClass("active");
        $(sss1).addClass("active");



        $('.' + relation + '_modal input[type=checkbox]').each(function () {
            $(this).prop('checked', false);

            var uncheck_accordian = $(this).attr('id').split("_");

            var acc = uncheck_accordian[0] + "_" + uncheck_accordian[1] + "_show_radio";

            $("#" + acc).css("display", "none");


        });
        go_ahead_self = false;

        var z = "." + relation + "_modal";


        $("#save_button").click();
    }
}

function gotonominee() {
    $("#show_nominee").click();
}

function gotoself() {


    $("#self_view").click();
	
}
function change_yes_no(relation, this2) {
if(window.location.pathname == '/retail_enrollment'){
	
	/*google analytics on disease modal show */
	
	dataLayer.push({
'page': 'insurance/abhi-insurance/personal-details/chronic-diease-pop-up/',
'event': 'event_Post_Login_Abhi_Insurance',
'category': 'Insurance',
'action': 'Step 2a | Abhi Insurance | Personal Details | Chronic Disease Pop-up',
'label': 'Chronic Disease Pop-up',
'application-id-abhi-insurance': $("#lead_id_hidden").val(),
});
 	
	
}
    this1 = this2;

    relationss = relation;

    $('.' + relation + '_modal').off();

    $('.' + relation + '_modal').on('hidden.bs.modal', function () {

        var z2 = false;
        $('.' + relation + '_modal input[type=radio]:checked').each(function () {
            var check_accordian = $(this).attr("name").split("_");
            if ($("#" + check_accordian[1] + "_" + check_accordian[0] + "_checkbox").is(":checked")) {


                z2 = true;



            }



        })




        if (!z2) {


            $(this1).removeClass('active');
            $(this1).siblings().addClass('active');



        } else {

            $(this1).addClass('active');
            $(this1).siblings().removeClass('active');

        }


        go_ahead_self = false;

        $("#save_button").click();
    });







}
function nominee_redirect() {

    $("#show_nominee").click();







}


function edit_redirect(relation) {

    $("#self_view").click();

    var yourtext = relation;
    var uppercase_relation = yourtext.substr(0, 1).toUpperCase() + yourtext.substr(1);
    $('*[data-check="' + uppercase_relation + '"]').click();





}
$(document).ready(function () {

    $('body').on("keyup", "#disease_disclaimer", function () {

        $("#disclaimer_new").show();
    });

    $('input[type=radio][name=dc_type12]').change(function () {

        if ($(this).val() == '1') {
            $("#disclaimer_change").show();
            $("#disclaimer_content").hide();


            $("#disclaimer_new").show();

            $("#save_employee111").click();

        } else {
            $("#disclaimer_change").hide();
            $("#disclaimer_content").html('');
            $("#disease_disclaimer").val('');
            $("#disclaimer_new").hide();

            emptyfield();


        }


    });

    $('input[type=radio][name=dc_type]').change(function () {

        if ($(this).val() == '1') {
				if(window.location.pathname == '/retail_enrollment'){
					
					dataLayer.push({
'page': 'insurance/abhi-insurance/application-review/disease-pop-up/policy-not-available',
'event': 'event_Post_Login_Abhi_Insurance',
'category': 'Insurance',
'action': 'Step 5a | Abhi Insurance | Review Application|Pop-up',
'label': 'Policy is Not Available',
'application-id-abhi-insurance': $("#lead_id_hidden").val(),
});
				}
			

            $('#insert_proposal_button').attr("disabled", true);


            sad_icon("This policy is not available to you or other proposed members. Please visit nearest Axis Bank branch for a suitable Plan.");
            $("#save_employee111").click();

        } else {
            $('#insert_proposal_button').attr("disabled", false);
            $("#save_employee111").click();


        }


    });


    $(document).on('click', '#save_employee111', function () {
        $("#save_employee111").hide();


        var data = {"id": $("#disease_disclaimer").val()};
        if ($("input[name='dc_type']").is(':checked')) {
            var disclaimer_1 = $("input[name='dc_type']:checked").val();
            var disclaimer_1 = {"discliamer_1": disclaimer_1};
            data = $.extend(disclaimer_1, data);
        }
        if ($("input[name='dc_type12']").is(':checked')) {
            var disclaimer_2 = $("input[name='dc_type12']:checked").val();
            var disclaimer_2 = {"discliamer_2": disclaimer_2};
            data = $.extend(disclaimer_2, data);
        }






        $.ajax({
            url: "/retail/add_disclaimer",
            type: "POST",
            async: true,
            data: data,
            dataType: "json",
            success: function (response) {
                if (response) {


                }
            }
        });



    });



    ajaxindicatorstart("Processing");
    $(document).on('change', '#nominee_relation', function () {

        if ($(this).val()) {
            $.ajax({
                url: "/retail/nominee_details",
                type: "POST",
                async: false,
                data: {"id": $(this).val()},
                dataType: "json",
                success: function (response) {
                    if (response) {


                        if (response.nominee_fname) {
                            $("#nominee_name").val(response.nominee_fname);
                        }
                        if (response.nominee_dob) {
                            var date_display = response.nominee_dob.split("-");
                            var y22 = new Date(date_display[2], (date_display[1] - 1), date_display[0]);
                            $("#nominee_dob").datepicker('setDate', y22);
                        }

                        var html = '';



                        html += '<div class="card-body card_acc"><div class=""><div class="row mb-2"><div class="col-md-4 col-6 mb-2"><span class="text_12">  Nominee Name </span></div><div class="col-md-1 col-1">:</div><div class="col-md-6 col-4 pad_zero"><span id ="nominee_firstname_review" class="text_11">  ' + response.nominee_fname + ' </span></div></div><div class="row mb-2"><div class="col-md-4 col-6 mb-2"><span class="text_12">  Nominee Date of Birth </span></div><div class="col-md-1 col-1">:</div><div class="col-md-7 col-4 pad_zero"><span id = "nominee_dob_review" class="text_11"> ' + response.nominee_dob + '</span></div></div><div class="row mb-2"><div class="col-md-4 col-6 mb-2"><span class="text_12">  Nominee With Relationship </span></div><div class="col-md-1 col-1">:</div><div class="col-md-7 col-4 pad_zero"><span id = "nominee_fr_review" class="text_11">' + response.fr_name + ' </span></div></div><div class="row mb-2"><div class="col-md-4 col-6 mb-2"><span class="text_12">  Nominee Mobile Number </span></div><div class="col-md-1 col-1">:</div><div class="col-md-7 col-4 pad_zero"><span id = "nominee_no_review"class="text_11"> ' + response.nominee_no + ' </span></div></div></div><div class="row mb-2"></div></div>';



                        $("#nominee_append").empty();
                        $("#nominee_append").append(html);




                    } else {
                        $("#nominee_name").val('');
                        $("#nominee_dob").val();
                    }
                }
            });


        }

    });



    $.validator.addMethod(
            "validate_pincode",
            function (value, element, param) {
                var regs = /^\d{6}$/g;
                return this.optional(element) || regs.test(value);
            },
            "Enter a valid Indian Pin Code"
            );

    $.validator.addMethod(
            "validate_postal_code",
            function (value, element, param) {
                var count = 1;


                if ($("#emp_pincode").val().length >= 6) {
                    var pincode = $("#emp_pincode").val();
                    $.ajax({
                        url: "/retail/axis_pincode_get_state_city",
                        type: "POST",
                        async: false,
                        data: {'pincode': pincode},
                        dataType: "json",
                        success: function (response) {

                            if (response && response.city && response.state) {
                                count = 0;
                                $('#emp_city').val(response.city);
                                $('#emp_state').val(response.state);
                            } else {
                                count++;
                            }
                        }
                    });
                }
                if (count > 0) {

                    return false;
                } else {
                    return true;
                }
            },
            "Enter a valid Indian Pin Code"
            );




    $("#emp_form_detais").validate({
        ignore: ".ignore",
        rules: {
            emp_address: {
                required: true
            },

            emp_city: {
                required: true
            },

            emp_pincode: {
                required: true
            },
            emp_email: {
                required: true,
                validateEmail: true
            },

            emp_pincode: {
                required: true,
                validate_pincode: true,
                validate_postal_code: true

            },

        }
    });


    $(document).on("change", "#dec_final12", function () {
        if ($(this).prop('checked') == true) {
            $('#insert_proposal_button').attr("disabled", true);
        } else {
            $('#insert_proposal_button').attr("disabled", false);
        }

    })






    $(document).on("click", "#save_employee", function () {


        if ($("#emp_form_detais").valid()) {

            var employee_data = $("#emp_form_detais").serialize() + "&state=" + $("#emp_state").val() + "&city=" + $("#emp_city").val() + "&emp_email=" + $("#emp_email").val();


            $.ajax({
                url: "/retail/edit_details",
                type: "POST",
                async: false,
                data: employee_data,
                dataType: "json",
                success: function (response) {
                    if (response.status) {


                        $("#address").html(response.data.address);
                        $("#pincode").html(response.data.emp_pincode);
                        $("#city").html(response.data.emp_city);
                        $("#edit_employee").hide();
                        $("#employee_div").show();
                        $("#email").html(response.data.email);
                        $('.span_cover').show();
                        $('#insert_proposal').html("Submit");
                        $("#insert_proposal").css("float", "right");
                        $('#insert_proposal_button').attr("disabled", false);





                        return;
                    } else {
                        sad_icon(response.message);
                    }



                    return;
                }
            });


        }


    });






    $('#show_nominee').addClass('hello1');



    var selector1 = '.btn_perform';




    $('body').on("click", selector1, function (e) {

        $(this).siblings().removeClass('active');

        $(this).addClass('active');
    });

    var emp_id = $("#empIdHidden").val() || "";
    var parent_id = $("#parentIdHidden").val();
    $('body').on("keyup", "input[name=firstname],input[name=nominee_name], input[name=nominee_relation]", function () {
        if ($(this).val().match(/[^a-zA-Z]/g)) {
            $(this).val($(this).val().replace(/[^a-zA-Z ]/g, ""));
        }


    });
    $('body').on("keyup", "input[name=emp_address]", function () {
        if ($(this).val().match(/[{}"\\]/g)) {
            $(this).val($(this).val().replace(/[{}"\\ ]/g, ""));
        }


    });

    $('body').on("keyup", "input[name=nominee_no]", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });

    $.validator.addMethod('valid_mobile', function (value, element, param) {
        var re = new RegExp('^[4-9][0-9]{9}$');
        return this.optional(element) || re.test(value);
    }, 'Enter a valid 10 digit mobile number');



    jQuery.validator.addMethod("checkspace", function (value, element, params) {

        var z = value.indexOf(' ');

        var y = value.replace(/ /g, '');
        return this.optional(element) || (value.indexOf(' ') >= 0 && typeof y[z] !== "undefined");
    }, jQuery.validator.format("Please enter full name"));



    $("#nominee_dob").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100Y:-0Y",
        maxDate: new Date()

    });

    $('#nominee_form').validate({
        rules: {
            nominee_name: {
                required: true,
				checkspace_nominee: true
            },
            nominee_relation: {
                required: true
            },
            nominee_dob: {
                required: true
            },
            nominee_no: {

                valid_mobile: true
            },

        },

    });



    if (emp_id && parent_id) {

        get_member_data(emp_id, parent_id);

    }

    $(document).on('click', '#nominee_form_submit', function () {

		ajaxindicatorstart("Processing");

        if ($("#nominee_form").valid()) {
            var nominee_data = $("#nominee_form").serialize();
            $.ajax({
                url: "/retail/add_nominee",
                type: "POST",
                async: false,
                data: nominee_data,
                dataType: "json",
                success: function (response) {
					ajaxindicatorstop();	
                    if (response.status) {


                        var member_data = response.data;
                        $("#summary_view").css("pointer-events", "auto");
                        $("#summary_view").click();

                        $('#summary_view').addClass('active');
                        $('#summary_view').removeClass('hello2');

                        $('#self_view').addClass('active1');
                        $('#show_nominee').addClass('active1');

						call_ga_script('summary_view');

                        var html = '';



                        html += '<div class="card-body card_acc"><div class=""><div class="row mb-2"><div class="col-md-4 col-6 mb-2"><span class="text_12">  Nominee Name </span></div><div class="col-md-1 col-1">:</div><div class="col-md-5 col-4 pad_zero"><span id = "nominee_firstname_review" class="text_11">  ' + member_data["nominee_fname"] + ' </span></div></div><div class="row mb-2"><div class="col-md-4 col-6 mb-2"><span class="text_12">  Nominee Date of Birth </span></div><div class="col-md-1 col-1">:</div><div class="col-md-7 col-3 pad_zero"><span id ="nominee_dob_review" class="text_11"> ' + member_data["nominee_dob"] + '</span></div></div><div class="row mb-2"><div class="col-md-4 col-6 mb-2"><span class="text_12">  Nominee With Relationship </span></div><div class="col-md-1 col-1">:</div><div class="col-md-7 col-3 pad_zero"><span id ="nominee_fr_review" class="text_11">' + member_data["relation"] + ' </span></div></div><div class="row mb-2"><div class="col-md-4 col-6 mb-2"><span class="text_12">  Nominee Mobile Number </span></div><div class="col-md-1 col-1">:</div><div class="col-md-7 col-3 pad_zero"><span id ="nominee_no_review" class="text_11"> ' + member_data["nominee_no"] + ' </span></div></div></div><div class="row mb-2"></div></div>';



                        $("#nominee_append").empty();
                        $("#nominee_append").append(html);









                        return;
                    } else {
                        sad_icon(response.message);
                    }



                    return;
                }
            });

        }







    }
    );




    $(document).on('click', '#save_button', function () {
ajaxindicatorstart('Processing');
var relation = $("#myTab a.active").data("check");
var form_check = $("#myTab a.active").data("check") + "_form";
var active_li = $("#myTab a.active").data("redirection");


var z1 = false;
if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
z = {};
z2 = {};
$('#' + relation + '_mobile_modal input[type=radio]:checked').each(function () {

var check_accordian = $(this).attr('id').split('_');


var check_mobile_radio = check_accordian[1] + '_' + check_accordian[0] + '_' + check_accordian[2] + '_' + 'accordian';

var dummy = check_accordian[1] + '_' + check_accordian[0];

var key222 = check_accordian[1];



if ($('a[href="#' + check_mobile_radio + '"]').find('input[type=checkbox]').is(":checked")) {

z[dummy] = 1;
}
else {
z[dummy] = 0;
}











if ($('a[href="#' + check_mobile_radio + '"]').find('input[type=checkbox]').is(":checked")) {
if ($(this).val() == 1) {
var name = $(this).attr("name");
$("input[name=" + name + "]").val([0]);
go = false;



$('.' + relation + '_modal input[type=radio]:checked').each(function () {

var name2 = $(this).attr("name");
$("input[name=" + name2 + "]").val([0]);


});
ajaxindicatorstop();
return false;




}



else {
go = true;
}
z1 = true;
var key = key222;
z2[key] = 1;




} else {
go = true;
}



});


}
else {



z = {};
z2 = {};
checkbox_check = {};
var diseases = $('.' + relation + '_modal input[type=radio]:checked').each(function () {
var check_accordian = $(this).attr("name").split("_");

if ($("#" + check_accordian[1] + "_" + check_accordian[0] + "_checkbox").is(":checked")) {
z[$(this).attr("name")] = 1;
}
else {

z[$(this).attr("name")] = 0;
}





if ($("#" + check_accordian[1] + "_" + check_accordian[0] + "_checkbox").is(":checked")) {
if ($(this).val() == 1) {
var name = $(this).attr("name");
$("input[name=" + name + "]").val([0]);
go = false;



$('.' + relation + '_modal input[type=checkbox]').each(function () {
$(this).prop('checked', false);
var uncheck_accordian = $(this).attr('id').split("_");

var acc = uncheck_accordian[0] + "_" + uncheck_accordian[1] + "_show_radio";

$("#" + acc).css("display", "none");
});
$('.' + relation + '_modal input[type=radio]:checked').each(function () {

var name2 = $(this).attr("name");
$("input[name=" + name2 + "]").val([0]);




});

var sss = $("#" + relation + "_form").find("button")[1];
var sss1 = $("#" + relation + "_form").find("button")[0];
$(sss).addClass("active");
$(sss1).removeClass("active");
$(sss).html("");
$(sss1).html("No");
ajaxindicatorstop();
return false;




} else {
go = true;
}

z1 = true;
var key = $(this).attr("name").split("_")[0];
z2[key] = $(this).attr("value").split("_")[0];

var checkbox_id = $("#" + check_accordian[1] + "_" + check_accordian[0] + "_checkbox").attr("id");
checkbox_check[checkbox_id] = 1;



} else {
go = true;
}



});


}

if (!go) {
ajaxindicatorstop();
return false;
}



ob[relation + "_disease"] = (Object.keys(z2)).join(",");

var data = $("#" + form_check).serialize() + "&disease=" + JSON.stringify(z) + "&relation=" + relation + "&disease_selected=" + z1 + "&checkbox_validation=" + JSON.stringify(checkbox_check);


if ($("#" + form_check).valid()) {

$.ajax({
url: "/retail/get_family_details",
type: "POST",
async: false,
data: data,
dataType: "json",
success: function (response) {


if (!response.status) {

sad_icon(response.message);
if (response.server_side) {
ajaxindicatorstop();
return false;


}



$(".pulseWarning").attr("class", "sa-warning sa-icon pulseWarning");
$("#save_button").prop("disabled", false);
if (response.checkbox_reset) {
if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
$('#' + relation + '_mobile_modal input[type=checkbox]:checked').each(function () {

$(this).prop("checked", false);

var accordian = $(this).attr("id").split("_");

$("#" + accordian[1] + "_" + accordian[0] + "_mobile_accordian").removeClass("show");
var check = $(this).attr("id") + "_radio2";
$('#' + check).prop("checked", true);








});
var name = relation + '_mobile_checkbox';
$('input[type="radio"][name="' + name + '"]').last().prop('checked', true);



$('#reload_check').attr('data-info', 'mobile');
}



var sss = $("#" + relation + "_form").find("button")[1];
var sss1 = $("#" + relation + "_form").find("button")[0];

$(sss).addClass("active");
$(sss1).removeClass("active");




$('.' + relation + '_modal input[type=checkbox]').each(function () {
$(this).prop('checked', false);

var uncheck_accordian = $(this).attr('id').split("_");

var acc = uncheck_accordian[0] + "_" + uncheck_accordian[1] + "_show_radio";

$("#" + acc).css("display", "none");




});

if (! /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
$('#reload_check').attr('data-info', 'web');
}


}










ajaxindicatorstop();

return;
}
else {








var member_data = response.data;
var html = '';


for (var i = 0; i < member_data.length; i++) {

var yourtext = member_data[i]["relationship"];
var get_disease = yourtext.substr(0, 1).toUpperCase() + yourtext.substr(1);



var disease_selected_member = ob[get_disease + "_disease"];
if (!disease_selected_member) {
disease_selected_member = 'None';
}





html += '<div class="mb-2 bordered_insured"><div class="row"><div class="col-md-11 col-10"><p class="hr_title">' + get_disease + '</p></div><div class="col-md-1 col-2"><button onclick=edit_redirect("' + member_data[i]["relationship"] + '") style="border: none;background: none;color: #97144d;"><i class="fa fa-pencil" aria-hidden="true"></i></button></div></div><div class="row"><div class="col-md-6 mb-2"><div class="row"><div class="col-md-5 col-4"><span class="text_12">   Name </span></div><div class="col-md-2 col-1">:</div><div class="col-md-5 col-6 pad_zero"><span id = '+member_data[i]["relationship"]+'_firstname_review class="text_11"> ' + member_data[i]["firstname"] + ' </span></div></div></div><div class="col-md-6 mb-2"><div class="row"><div class="col-md-5 col-4"><span class="text_12">   Relationship  </span></div><div class="col-md-2 col-1">:</div><div class="col-md-5 col-6 pad_zero"><span class="text_11">' + get_disease + '</span></div></div></div></div><div class="row"><div class="col-md-6 mb-2"><div class="row"><div class="col-md-5 col-4"><span class="text_12">  Date of Birth </span></div><div class="col-md-2 col-1">:</div><div class="col-md-5 col-6 pad_zero"><span id = '+member_data[i]["relationship"]+'_dob_review class="text_11">  ' + member_data[i]["dob"] + '  </span></div></div></div><div class="col-md-6 mb-2"><div class="row"><div class="col-md-5 col-4"><span class="text_12"> Gender</span></div><div class="col-md-2 col-1">:</div><div class="col-md-5 col-6 pad_zero"><span class="text_11">' + member_data[i]["gender"] + '</span></div></div></div></div><div class="row mb-2"><div id = id = '+member_data[i]["relationship"]+'_gender_review class="col-md-3  col-5 mb-2"><span class="text_12">  Chronic Disease </span></div><div class="col-md-8  col-7 pad_zero"><span class="text_11">' + disease_selected_member + ' </span></div><div class="col-md-8 col-4"><span class="text_12"> </span></div></div></div>'

}





$("#member_append").empty();
$("#member_append").append(html);

if (go_ahead_self) {
if (active_li == 'nominee') {
	call_ga_script('show_nominee');
$("#show_nominee").css("pointer-events", "auto");
$("#show_nominee").click();
$("#show_nominee").addClass("active");

$('#summary_view').addClass('hello2');
$('#show_nominee').removeClass('hello1');


}
else {
$("#" + active_li + "_link").parent().css("pointer-events", "auto");
$("#" + active_li + "_link").click();
}
}
else {
go_ahead_self = true;
}










ajaxindicatorstop();

}








}





});
}

});

    function get_member_data(emp_id, parent_id) {

        var droppoff = $("#droppoff").val();
        if (droppoff) {

            localStorage.clear();
        }

        ajaxindicatorstart('Processing');
        $.ajax({
            type: "POST",
            url: "/get_member_details_retail",

            dataType: "json",
            async: false,
            success: function (result) {


                $("#myTab").empty();
                $("#myTabContent").empty();
                for (var i = 0; i < result.total_relations; i++) {




                    var j = i + 1;
                    if (j == result.total_relations) {
                        var redirection = 'nominee';
                    } else {
                        var redirection = result.adult_relations[j];
                    }

                    if (i == 0) {
                        var show = 'show active';
                    } else {
                        show = '';
                        var disabled = "pointer-events : none";
                    }



                    $("#myTab").append('<li style = "' + disabled + '" class="nav-item wd_15"><a data-check =' + result.adult_relations[i] + ' data-redirection =' + redirection + ' class="nav-link ' + show + ' nav_link_sm" id= ' + result.adult_relations[i] + '_link data-toggle="tab" href= #' + result.adult_relations[i] + '_div role="tab" aria-controls="self" aria-selected="true">' + result.adult_relations[i] + '</a></li>');
                    $("#myTabContent").append('<div class="tab-pane fade' + show + '" id=' + result.adult_relations[i] + '_div role="tabpanel" aria-labelledby="self-link"><form id=' + result.adult_relations[i] + '_form method="post" novalidate="novalidate"><div class="personal_del mt-3"><div class="row"><div class="col-md-6"><div class="form-group"><div onclick = show_message_noneditable("' + result.adult_relations[i] + '")><label for="example-text-input" class="col-form-label">Full Name<sup><img src="/public/assets_new/images/important.png"></sup></label><input class="form-control" type="text" name=firstname id= ' + result.adult_relations[i] + '_full_name1 value="" placeholder="Enter Full Name"></div></div></div><div class="col-md-6"><div class="form-group"><div onclick = show_message_noneditable("' + result.adult_relations[i] + '")><label for="dob1" class="col-form-label">Date of Birth (DD-MM-YYYY)</label><sup><img src="/public/assets_new/images/important.png"></sup><input class="form-control" autocomplete="off" type="text" name=dob1 id= ' + result.adult_relations[i] + '_dob1 placeholder="DD-MM-YYYY"></div></div></div><div class="col-md-6"><div class="col-md-12"><div class="row mb-2"><label for="example-text-input" class="col-form-label col-md-12" style="padding: 0px;margin-bottom: 14px; margin-top: 10px;">Gender<sup><img src="/public/assets_new/images/important.png"></sup></label><div class="custom-control custom-checkbox custom-control-inline"> <input type="radio" class="custom-control-input" name=gender id="' + result.adult_relations[i] + '_family_check_1" value="Male" checked=""><label class="custom-control-label" for="' + result.adult_relations[i] + '_family_check_1"> Male </label> </div><div class="custom-control custom-checkbox custom-control-inline"> <input type="radio" class="custom-control-input" name=gender  id="' + result.adult_relations[i] + '_family_check_2" value="Female"><label class="custom-control-label" for="' + result.adult_relations[i] + '_family_check_2" > Female </label> </div></div></div></div></div><div class="row mt-2" style="padding: 0px;"><div class="col-md-8 mt-2"><p class="" style="font-weight:600; font-size:14px; margin-left:0px;">Do you have a Chronic Diseases ? <a href="#" data-toggle="modal" data-target=".bd-example-modal-sm"> <img src="/public/assets_new/images/info.png"></a></p></div><div class="col-md-4 hidden_on_this mt-2"><div class="row"><div class="col-md-4 yes-btn11">Yes</div><div class="col-md-5"><div class="btn_yes_no" style ="height: 20px; width: 59px;"><button  type="button" class="btn_perform" data-toggle="modal"  onclick = change_yes_no("' + result.adult_relations[i] + '",this) data-target=".' + result.adult_relations[i] + '_modal"></button> <button type="button"  onclick = reset_accordian(this,"' + result.adult_relations[i] + '") class="btn_perform active"></i></button></div></div><div class="col-md-3 no-btn11">No</div></div></div></div><div class="col-12 hidden_on_long"><div class="row"><div class="col-4"><div class="custom-control custom-radio"><input type="radio"  onclick = change_yes_no_mobile("' + result.adult_relations[i] + '",this) id="' + result.adult_relations[i] + '_mobile_checkbox1" name="' + result.adult_relations[i] + '_mobile_checkbox" class="custom-control-input" data-toggle="modal" data-target="#' + result.adult_relations[i] + '_mobile_modal"><label class="custom-control-label" for="' + result.adult_relations[i] + '_mobile_checkbox1">Yes</label></div></div><div class="col-4"><div class="custom-control custom-radio"><input type="radio" checked onclick = reset_accordian(this,"' + result.adult_relations[i] + '") id="' + result.adult_relations[i] + '_mobile_checkbox2" name="' + result.adult_relations[i] + '_mobile_checkbox" class="custom-control-input"><label class="custom-control-label" for="' + result.adult_relations[i] + '_mobile_checkbox2">No</label></div></div></div></div><!-- <div class="col-md-12" style="margin-top: 30px;text-align: right;"><button type="submit" class="btn btn_save_proceed">Save and Proceed </button></div>--></div></form></div>');
                    $("#" + result.adult_relations[i] + "_dob1").datepicker({
                        dateFormat: "dd-mm-yy",
                        prevText: '<i class="fa fa-angle-left"></i>',
                        nextText: '<i class="fa fa-angle-right"></i>',
                        changeMonth: true,
                        changeYear: true,
                        yearRange: "-100Y:-0Y",
                        maxDate: new Date()
                    }).attr('readonly', 'readonly');
                    $("#" + result.adult_relations[i] + "_form").validate();
                    $("#" + result.adult_relations[i] + "_form :input").each(function (el) {

                        if ($(this).attr("name") == 'firstname') {
                            $(this).rules('add', {
                                required: true,
                                /*checkspace: true*/
                            });
                        } else {
                            $(this).rules('add', {
                                required: true
                            });
                        }


                    });


                    var disease_mobile = '';
                    var disease = result.disease_array[result.adult_relations[i]];
                    var disease_append = '';
                    var disease_append_8 = '';
                    $.each(disease, function (key, value) {


                        ﻿


                                ﻿


                                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {

                            disease_mobile += '<div class=""><div class="card-header"><a class="card-link  border_card collapsed" data-toggle="collapse" href="#' + key + '_' + result.adult_relations[i] + '_mobile_accordian" aria-expanded="false"><div class="row"><div class="col-10">' + key + '</div><div class="col-2"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="' + result.adult_relations[i] + '_' + key + '_mobile"><label class="custom-control-label btn" onclick=testing_new(this,"' + result.adult_relations[i] + '") for="' + result.adult_relations[i] + '_' + key + '_mobile"></label></div></div></div></a></div><div id="' + key + '_' + result.adult_relations[i] + '_mobile_accordian" class="collapse" data-parent="#accordionMob1" style=""><div class="card-body"><div class="row"> <div class="col-2"> <span class="count_span">1</span></div><div class="col-10 dis_sm"> ' + value + ' </div></div><div class="col-12 mt-2"><div class="row"><div class="col-4"><div class="custom-control custom-radio"><input value ="1"  type="radio" onclick = show_error_on_yes_changed(this); id="' + result.adult_relations[i] + '_' + key + '_mobile_radio1" name="' + result.adult_relations[i] + '_' + key + '_mobile_radio" class="custom-control-input"><label class="custom-control-label" for="' + result.adult_relations[i] + '_' + key + '_mobile_radio1">Yes</label></div></div><div class="col-4"><div class="custom-control custom-radio"><input value ="0" type="radio" checked="" id="' + result.adult_relations[i] + '_' + key + '_mobile_radio2"  name="' + result.adult_relations[i] + '_' + key + '_mobile_radio"class="custom-control-input"><label class="custom-control-label" for="' + result.adult_relations[i] + '_' + key + '_mobile_radio2">No</label></div></div></div></div></div></div></div>';
                        } else {
                            disease_append += '<div class="col-md-12 text-center" style="margin-top: 10px; margin-bottom:10px;"> <span class="btn" style=" width: 80%;color: #97144d;border: 1.5px solid #97144d; padding-top: 8px;background: #E5CBD6;text-align:left;"><div class="custom-control custom-checkbox custom-control-inline"><input type="checkbox" class="custom-control-input" id="' + result.adult_relations[i] + '_' + key + '_checkbox" onclick=open_div(this,"' + result.adult_relations[i] + '_' + key + '_show_radio","' + result.adult_relations[i] + '")><label class="custom-control-label" for="' + result.adult_relations[i] + '_' + key + '_checkbox" style="padding-right:10px; font-size: 13px; font-weight: 800; letter-spacing: 1px; margin-top: 5px;">' + key + '</label></div><hr style=" margin-left: -16px;margin-bottom: -11px;margin-right: -16px;margin-top: 13px;"></span> </div>';

                            disease_append_8 += '<div id= "' + result.adult_relations[i] + '_' + key + '_show_radio" style="display: none;"><div class="card-header"><a class="card-link border_cardlong" data-toggle="collapse" href="#' + result.adult_relations[i] + '_' + key + '_accordian" aria-expanded="true">' + key + '</a></div><div id=' + result.adult_relations[i] + '_' + key + '_accordian class="collapse show" data-parent="#"' + result.adult_relations[i] + '"_accordian" style=""><div class="card-body"><div id="asthma" class="diseases11"><div class="header_ques" style="margin-top: 10px; margin-bottom: 10px;font-size: 14px;color: #97144d;font-weight: 800;letter-spacing: 1px;text-align:left;"></div><div class="body_ques"> <div><div class="row"> <div class="col-md-1"> <span class="count_span">1</span></div><div class="col-md-11 ques_text"> ' + value + '</div></div><div class="col-12"><div class="row"><div class="col-md-4"><div class="custom-control custom-radio"><input value =1  onclick = show_error_on_yes_changed(this) type="radio" id="' + result.adult_relations[i] + '_' + key + 'keys1" name="' + key + '_' + result.adult_relations[i] + '" class="custom-control-input"><label class="custom-control-label" for="' + result.adult_relations[i] + '_' + key + 'keys1">Yes</label></div></div><div class="col-md-4"><div class="custom-control custom-radio"><input value =0 type="radio" id="' + result.adult_relations[i] + '_' + key + 'keys2" name="' + key + '_' + result.adult_relations[i] + '"   class="custom-control-input" checked=""><label class="custom-control-label" for="' + result.adult_relations[i] + '_' + key + 'keys2">No</label></div></div></div></div></div></div></div></div></div></div>';
                        }













                    });


                    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                        var z = '<div class="modal fade mobileSelfModal"  data-keyboard="false" data-backdrop="static"  id="' + result.adult_relations[i] + '_mobile_modal"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" style="font-size: 12px; font-weight: 600;">Do you have one or more of below mentioned chronic disease ?</h5><button type="button" onclick = reset_mobile("' + result.adult_relations[i] + '") class="close"  ><span>×</span></button></div><div class="modal-body"><p></p><div id="accordionMob1" class="according">' + disease_mobile + '</div></div><div class="modal-footer"><button id="save1123_button"  onclick = mobile_check_yes_no_again("' + result.adult_relations[i] + '") type="button" class="btn btn-chronic">Proceed</button></div></div></div></div>';
                        if (disease) {


                            $('.sahil').append(z);
                        }
                        ;



                    } else {
                        var z = '<div class="modal fade ' + result.adult_relations[i] + '_modal"  data-keyboard="false" data-backdrop="static"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h6 class="modal-title" style="font-family: Lato; font-weight: 600; margin-left: 13px;">Do you have one or more of below mentioned chronic disease ?</h6><button type="button" class="close" onclick = modal_dismiss("' + result.adult_relations[i] + '") ><span>×</span></button></div><div class="modal-body"><div class="row"><div class="col-md-4">' + disease_append + '</div><div class="col-md-8"><div id="' + result.adult_relations[i] + '_accordian" class="according accordion-s3">' + disease_append_8 + '</div></div></div></div><div class="modal-footer"><button id="save11_button"  onclick = show_error_on_yes("' + result.adult_relations[i] + '") type="button" class="btn btn-chronic">Proceed</button></div></div>';
                        if (disease) {
                            $('.sahil').append(z);
                        }
                        ;
                    }





                }

                $("#myTabContent").after('<div class="col-md-12" style="margin-top: 30px;text-align: right;"><button id ="save_button" type="button" class="btn btn_save_proceed" style="color:#fff !important;">Save and Proceed </button></div>');



            }
        });






        $.ajax({
            url: "/retail/get_data",
            type: "POST",
			 async: false,
            dataType: "json",
            success: function (response) {
                if (response.disclaimer) {


                    if (response.disclaimer.discliamer_1) {
                        $("input[name=dc_type][value=" + response.disclaimer.discliamer_1 + "]").prop('checked', true);

                        if (response.disclaimer.discliamer_1 == '1') {
                            $('#insert_proposal_button').attr("disabled", true);
                        }
                    }

                    if (response.disclaimer.discliamer_2) {
                        $("input[name=dc_type12][value=" + response.disclaimer.discliamer_2 + "]").prop('checked', true);
                        if (response.disclaimer.discliamer_2 == '1') {
                            $("#disease_disclaimer").val(response.disclaimer.disclaimer);
                            $("#disclaimer_change").show();
                            $("#disclaimer_new").hide();


                        }

                    }



                }
                if (response.disease) {
                    $.each(response.disease, function (key, value) {

                        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {

                            var relationship1 = value['reference_name'];
                            relationship1 = relationship1.substr(0, 1).toUpperCase() + relationship1.substr(1);
                            $("#" + relationship1 + "_mobile_checkbox1").prop("checked", true);
                            $("#" + relationship1 + "_mobile_checkbox2").prop("checked", false);
                            $("#" + relationship1 + "_" + value['disease'] + "_mobile").prop("checked", true);






                        } else {
                            var relationship1 = value['reference_name'];
                            relationship1 = relationship1.substr(0, 1).toUpperCase() + relationship1.substr(1);
                            var checkbox_yes_no = $("#" + relationship1 + "_form").find("button")[0];

                            $(checkbox_yes_no).addClass("active");



                            var checkbox_yes_no1 = $("#" + relationship1 + "_form").find("button")[1];

                            $(checkbox_yes_no1).removeClass("active");


                            $("#" + relationship1 + "_" + value['disease'] + "_checkbox").click();






                        }



                    });
                }

                if (response.nominee) {

                    if (response.nominee.length != 0) {

                        $("#nominee_relation").val(response.nominee[0]['fr_id']);
                        if (response.nominee[0]['fr_id'] == '0') {

                            $("#nominee_relation").val('');
                        }

                        $("#nominee_name").val(response.nominee[0]['nominee_fname']);
                        $("#nominee_dob").val(response.nominee[0]['nominee_dob']);


                        $("#show_nominee").css("pointer-events", "auto");



                        if (response.show_all) {
                            $("#show_nominee").click();
                            $("#show_nominee").addClass("active");

                            $('#summary_view').addClass('hello2');
                            $('#show_nominee').removeClass('hello1');
                            $("#summary_view").css("pointer-events", "auto");
                            $("#summary_view").click();

                            $('#summary_view').addClass('active');
                            $('#summary_view').removeClass('hello2');

                            $('#self_view').addClass('active1');
                            $('#show_nominee').addClass('active1');
							
                            if (response.on_nominee_tab) {
                                $("#show_nominee").click();
								
								if(localStorage.getItem('active') === "undefined") {
									google_analytics = 'show_nominee;'
									
								}
								
                            }
							else{
								if(localStorage.getItem('active') === "undefined") {
									
									google_analytics = 'summary_view';
								}
								
								
								
							}
                        } else {
							if(localStorage.getItem('active') === "undefined") {
									
									google_analytics = 'self_view';
								}
							
                            $("#show_nominee").css("pointer-events", "none");

                        }


                    }

                }



                if (response.members) {
                    z2 = {};


                    $("#premium1").html(response.members[0].policy_mem_sum_premium);

                    $.each(response.members, function (key, value) {



                        var relationship = value['relationship'];
                        relationship = relationship.substr(0, 1).toUpperCase() + relationship.substr(1);



                        relation = relationship;


                        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {

                            if ($("#" + relation + "_mobile_checkbox1").prop("checked") == true) {
                                $('#' + relation + '_mobile_modal input[type=checkbox]:checked').each(function () {
                                    var key = $(this).attr("id").split("_")[1];
                                    z2[key] = 0;
                                    ob[relation + "_disease"] = (Object.keys(z2)).join(", ");
                                });

                            }


                        }





                        var diseases = $('.' + relation + '_modal input[type=radio]:checked').each(function () {
                            var check_accordian = $(this).attr("name").split("_");


                            if ($("#" + check_accordian[1] + "_" + check_accordian[0] + "_checkbox").is(":checked")) {

                                var key = $(this).attr("name").split("_")[0];
                                z2[key] = $(this).attr("value").split("_")[0];
                                ob[relation + "_disease"] = (Object.keys(z2)).join(", ");
                            }

                        });







                        $("#" + relationship + "_link").parent().css("pointer-events", "auto");
                        $("#" + relationship + "_full_name1").val(value['policy_member_first_name']);
                        var date_display = value['dob'].split("-");
                        var member_date = new Date(date_display[2], (date_display[1] - 1), date_display[0]);
                        $("#" + relationship + "_dob1").datepicker('setDate', member_date);



                        if (value['gender'] == 'Female') {
                            $("#" + relationship + "_family_check_2").click();
                        } else {
                            $("#" + relationship + "_family_check_1").click();
                        }

                    });




                    var member_data = response.members;
                    var html = '';

                    for (var i = 0; i < member_data.length; i++) {

                        var yourtext = member_data[i]["relationship"];
                        var get_disease = yourtext.substr(0, 1).toUpperCase() + yourtext.substr(1);


                        var disease_selected_member = ob[get_disease + "_disease"];
                        if (!disease_selected_member) {
                            disease_selected_member = 'None';
                        }




                        html += '<div class="mb-2 bordered_insured"><div class="row"><div class="col-md-11 col-10"><p class="hr_title">' + get_disease + '</p></div><div class="col-md-1 col-2"><button onclick=edit_redirect("' + member_data[i]["relationship"] + '") style="border: none;background: none;color: #97144d;"><i class="fa fa-pencil" aria-hidden="true"></i></button></div></div><div class="row"><div class="col-md-6 mb-2"><div class="row"><div class="col-md-5 col-4"><span class="text_12">   Name </span></div><div class="col-md-2 col-1">:</div><div class="col-md-5 col-6 pad_zero"><span id = '+member_data[i]["relationship"]+'_firstname_review class="text_11"> ' + member_data[i]["firstname"] + ' </span></div></div></div><div class="col-md-6 mb-2"><div class="row"><div class="col-md-5 col-4"><span class="text_12">   Relationship  </span></div><div class="col-md-2 col-1">:</div><div class="col-md-5 col-6 pad_zero"><span class="text_11">' + get_disease + '</span></div></div></div></div><div class="row"><div class="col-md-6 mb-2"><div class="row"><div class="col-md-5 col-4"><span class="text_12">  Date of Birth </span></div><div class="col-md-2 col-1">:</div><div class="col-md-5 col-6 pad_zero"><span id = '+member_data[i]["relationship"]+'_dob_review class="text_11">  ' + member_data[i]["dob"] + '  </span></div></div></div><div class="col-md-6 mb-2"><div class="row"><div class="col-md-5 col-4"><span class="text_12"> Gender</span></div><div class="col-md-2 col-1">:</div><div class="col-md-5 col-6 pad_zero"><span id = '+member_data[i]["relationship"]+'_gender_review class="text_11">' + member_data[i]["gender"] + '</span></div></div></div></div><div class="row mb-2"><div class="col-md-3 col-5 mb-2"><span class="text_12">  Chronic Disease </span></div><div class="col-md-8 col-7 pad_zero"><span class="text_11">' + disease_selected_member + ' </span></div><div class="col-md-8 col-4"><span class="text_12"> </span></div></div></div>'

                    }





                    $("#member_append").empty();
                    $("#member_append").append(html);
















                }


                if (response.members) {

                    if (response.members[0].gender == 'Male') {

                        $("#Self_family_check_1").prop("checked", true);
                        $("#Self_family_check_2").prop("checked", false);

                    } else {
                        $("#Self_family_check_2").prop("checked", true);
                        $("#Self_family_check_1").prop("checked", false);
                    }
                }

                if (localStorage.getItem('active')) {
                    $("#" + localStorage.getItem('active')).click();
					if(localStorage.getItem('active') !== "undefined") { 
						google_analytics = localStorage.getItem('active');
						
					}
					
					
                }
				
				
                ajaxindicatorstop();
            }
				
        });
        var name = $("#employee_name").val();
        var dobself = $("#employee_dob").val().split("-");

        var member_date = new Date(dobself[2], (dobself[1] - 1), dobself[0]);
        $("#Self_dob1").datepicker('setDate', member_date);


        $("#Self_full_name1").val(name);
        $("#Self_full_name1").css("background", "#efefef");

        $("#Self_dob1").css("pointer-events", "none");
        $("#Self_full_name1").css("pointer-events", "none");
        $("#Self_dob1").css("background", "#efefef");
		
	/*analytics code to check user is oon which tab for eg->personal details,nominee,review page */
	
			
call_ga_script(google_analytics);

		
        ajaxindicatorstop();

    }
    setTimeout(function () {
        $("#Self_family_check_1").trigger("change");
    }, 3000);
    $("#validate_form").validate({
        ignore: ".ignore",
        rules: {
            full_name: {
                required: true,
            },
            dob1: {
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
                    if (result != '') {

                        $("#BranchCode").val('');
                        $("#IMDCode").val('');

                    }
                }
            });
        }
    });



    $('body').on("change", '#Self_family_check_1,#Self_family_check_2', function (e) {



        if ($("#Self_family_check_1").prop("checked")) {
            var check_gender_onload = 'Male';
        } else {
            check_gender_onload = 'Female';
        }
        if (check_gender_onload == 'Female') {
            $("#Spouse_family_check_2").parent().hide();
            $("#Spouse_family_check_1").parent().show();
            $("#Spouse_family_check_1").prop("checked", true);
        } else {
            $("#Spouse_family_check_1").parent().hide();
            $("#Spouse_family_check_2").parent().show();
            $("#Spouse_family_check_2").prop("checked", true);

        }
    });

    $(document).on('click', '#insert_proposal_button', function () {

		ajaxindicatorstart('Processing...');


        var go_ahead_proposal = true;
        if (!($("input[name='dc_type']").is(':checked') && $("input[name='dc_type12']").is(':checked'))) {
            if (!$("#dec_final2").prop('checked')) {
                sad_icon("In order to proceed further please accept the terms and conditions & answer the health related questions", false);
                return false;
            } else {
                go_ahead_proposal = false;
                sad_icon("In order to proceed further please answer the health related questions", false);
                return false;
            }



        }

        if ($('#disclaimer_new').is(':visible')) {
            go_ahead_proposal = false;
            sad_icon("Please enter and save the details", false);
            return false;

        }




        $("#London11").show();
        $("#myTab li").each(function (idx, li) {
            var forms1 = $(this).find("a").data("redirection");
            if (!$("#" + forms1 + "_form").valid()) {
                go_ahead_proposal = false;
                sad_icon("Please fill personal details");




            }

        });
		
        $("#London11").hide();



        $("#Paris11").show();
        if (!$("#nominee_form").valid()) {
            go_ahead_proposal = false;
            sad_icon("Please fill nominee details");


            $("#Paris11").hide();
            return false;
        }
        $("#Paris11").hide()


        $("#Tokyo11").show();
		$("#edit_employee").show();
        if (!$("#emp_form_detais").valid()) {
			$("#edit_employee").hide();
            go_ahead_proposal = false;
            sad_icon("Communication details are incomplete.");
            edit_address();
			return false;

        }



        if (!$("#dec_final2").prop('checked')) {
            go_ahead_proposal = false;

            sad_icon("In order to proceed further please accept the terms and conditions.");




            return false;
        }



        if (go_ahead_proposal) {
			if(window.location.pathname == '/retail_enrollment'){
				dataLayer.push({
'page': 'insurance/abhi-insurance/payment-portal-proceed',
'event': 'event_Post_Login_Abhi_Insurance',
'category': 'Insurance',
'action': 'Step 5 | Abhi Insurance | Review Application Submitted',
'label': 'User Proceeded To Payment Portal',
'application-id-abhi-insurance': $("#lead_id_hidden").val(),
});
			}


            $.ajax({
                url: "/retail/create_proposal",
                type: "POST",
                async: false,

                dataType: "json",
                success: function (response) {

					

                    if (response.status) {

						
                        //return false;
                        /*var y = window.location.origin + "/api/payment_redirection";
                        window.location.href = y;*/
						
						/*window.location.replace('/api/payment_redirection');	*/
						window.location.replace('/redirect_pg');
						
                    }else{
						sad_icon(response.message);
						
					}






                }
            });

        }



    });



});
function openCity500(cityName, test) {
    var i;
    var x = document.getElementsByClassName("city111");
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
    }
    document.getElementById(cityName).style.display = "block";
    $(test).siblings('li').removeClass('active');
    $(test).addClass('active');
}

function open_div(val, hide_div, rel) {
    var z = "." + rel + "_modal";
    var z2 = false;
    $('.' + rel + '_modal input[type=radio]:checked').each(function () {
        var check_accordian = $(this).attr("name").split("_");
        if ($("#" + check_accordian[1] + "_" + check_accordian[0] + "_checkbox").is(":checked")) {


            z2 = true;




        }



    })









    if ($(val).prop("checked") == true) {




        document.getElementById(hide_div).style.display = "block";


    } else if ($(val).prop("checked") == false) {




        document.getElementById(hide_div).style.display = "none";
    }

}

function testing_new(this_val, rel) {







    var closeCheckbox = $(this_val).closest("a.border_card").find("input[type=checkbox]");
    if (closeCheckbox.prop('checked') == false) {





        closeCheckbox.prop('checked', true);
    } else {

        closeCheckbox.prop('checked', false);
    }

    var relation = rel;


    var z = ".mobile" + rel + "Modal";
    var z2 = false;
    $('#' + relation + '_mobile_modal input[type=checkbox]:checked').each(function () {

        z2 = true;



    });


}

function openCity(questions) {
    var i;
    var x = document.getElementsByClassName("diseases11");
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
    }
    document.getElementById(questions).style.display = "block";
}
