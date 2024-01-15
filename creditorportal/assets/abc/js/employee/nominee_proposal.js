
// function ecard_link(member_id, policy_no, status) {
    // if (status == 1) {
        // $.ajax({
            // url: "/get_ecard",
            // type: "POST",
            // data: {
                // "policy_no": policy_no, "member_id": member_id,
            // },
            // async: false,
            // dataType: "json",
            // success: function (response) {
                // console.log(response);
                // var ecard_link_url = response.soapBody.getECardDownloadLinkResponse.getECardDownloadLinkResult.ECardDownloadLink;
                // window.location.replace(ecard_link_url);

            // }
        // });
    // } else {
        // swal("", "member is still not active");
        // alert("member is still not active");
    // }

// }
var selectChange = "";
var value = $("#nominee_relation").find("option");
var aee = new Array();

var eValue = "";

function getgender(e) {
//console.log(e.value);

    eValue = e.value;
    var family_gender = $(e).closest(".row").find("#family_gender");
    if (eValue == 2 || eValue == 4 || eValue == 7) {

        family_gender.empty();
        family_gender.append('<option value="Male">Male</option>');
    }
    if (eValue == 3 || eValue == 5 || eValue == 6) {
        family_gender.empty();
        family_gender.append('<option value="Female">Female</option>');

    }

    if (eValue == 1) {
        family_gender.empty();
        family_gender.append('<option value="Male">Male</option>');
        family_gender.append('<option value="Female">Female</option>');
        family_gender.append('<option value="Transgender">Transgender</option>');
    }
}

// delete nominee
function delete_func(e)
{
    swal({
        title: "Are you sure?",
        text: "Your will not be able to recover this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes, delete it!",
        closeOnConfirm: false
    },
            function () {
                $.ajax({
                    url: "/employee/delete_nominee",
                    type: "POST",
                    data: {nominee_id: e},
                    async: false,
                    dataType: "json",
                    success: function (response) {
                        if (response == true)
                        {
                            /* swal("","Nominee Deleted Successfully");
                             window.location.reload(); */
                            swal({
                                title: "Success",
                                text: "Nominee Deleted Successfully",
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
                                    function ()
                                    {
                                        // location.reload();
                                    });
                        } else
                        {
                            /* swal("","Something Went Wrong"); */
                            swal({
                                title: "Delete",
                                text: "Something Went Wrong",
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
                                    function ()
                                    {
                                        // location.reload();
                                    });
                        }
                    }
                });
            });
}

// set nominee in table
function update_func(nominee_id)
{
    var select = $("#update_record_" + nominee_id).closest('tr').find("input").removeAttr("readonly");
    $("#update_record_" + nominee_id).closest('tr').find("input").addClass("editable");
    $("#update_record_" + nominee_id).closest('tr').find(".nominee_date").attr("readonly");
    $("#update_record_" + nominee_id).closest('tr').find(".guardina_date").attr("readonly");
    $("#update_record_" + nominee_id).closest('tr').find("button[name='update_btn']").removeAttr("hidden");
    $("#update_record_" + nominee_id).closest('tr').find("button[name='edit_btn']").attr("hidden", true);
    var dobs = $("#update_record_" + nominee_id).closest('tr').find('.nominee_date').datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100Y:+0",
        maxDate: new Date(),
        minDate: "-100Y +1D",
        onSelect: function (dateText, inst) {
            $(this).val(dateText);
        }
    });
    $("#update_record_" + nominee_id).closest('tr').find('.guardina_date').datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100Y:-18Y",
        maxDate: "-18Y",
        minDate: "-100Y:-18Y",
        onSelect: function (dateText, inst) {
            $(this).val(dateText);
        }
    });
    // nominee table datepicker
    // $(dobs).on('focus', '.nominee_date',  function(e){
    //   debugger;
    //     $(this).removeClass("hasDatepicker");
    //     $(this).datepicker({
    //       dateFormat: "dd-mm-yy",
    //       prevText: '<i class="fa fa-angle-left"></i>',
    //       nextText: '<i class="fa fa-angle-right"></i>',
    //       changeMonth: true,
    //       changeYear: true,
    //       yearRange: "-100Y:+0",
    //       maxDate: new Date(),
    //       minDate: "-100Y +1D",
    //       onSelect: function (dateText, inst) {
    //         $(this).val(dateText);
    //       }
    //     });
    // });

    // guardian table datepicker
    // $(dobs).on('focus', '.guardina_date',  function(e){
    //   $(this).removeClass("hasDatepicker");
    //   $(this).datepicker({
    //     dateFormat: "dd-mm-yy",
    //     prevText: '<i class="fa fa-angle-left"></i>',
    //     nextText: '<i class="fa fa-angle-right"></i>',
    //     changeMonth: true,
    //     changeYear: true,
    //     yearRange: "-100Y:-18Y",
    //     maxDate: "-18Y",
    //     minDate: "-100Y:-18Y",
    //     onSelect: function (dateText, inst) {
    //      $(this).val(dateText);
    // }
    // });
    // });
}

// here get and update all nominee and guardian table details
function update_nominee_data(nomineeid)
{
    var rel_nominee = $("#update_nominee_btn_" + nomineeid).closest('tr').find("input")[0].value;
    var nominee_firstname = $("#update_nominee_btn_" + nomineeid).closest('tr').find("input")[1].value;
    var nominee_lastname = $("#update_nominee_btn_" + nomineeid).closest('tr').find("input")[2].value;
    var nominee_date = $("#update_nominee_btn_" + nomineeid).closest('tr').find("input")[3].value;
    var guardian_name = $("#update_nominee_btn_" + nomineeid).closest('tr').find("input")[4].value;
    var guardina_date = $("#update_nominee_btn_" + nomineeid).closest('tr').find("input")[5].value;
    var guardian_rel = $("#update_nominee_btn_" + nomineeid).closest('tr').find("input")[6].value;
    $.ajax({
        url: '/employee/update_nominee_data',
        data: {nomineeid: nomineeid, rel_nominee: rel_nominee, nominee_firstname: nominee_firstname, nominee_lastname: nominee_lastname,
            nominee_date: nominee_date, guardian_name: guardian_name, guardina_date: guardina_date, guardian_rel: guardian_rel},
        type: "POST",
        async: false,
        dataType: "json",
        success: function (response) {
            if (response.error)
            {
                /*  swal("",response.error);
                 return false; */
                swal({
                    title: "Alert",
                    text: response.error,
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
                        function ()
                        {
                            /* location.reload(); */
                        });
            } else
            {
                swal({
                    title: "Success",
                    text: response.success,
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
                        function ()
                        {
                            $("#update_record_" + nomineeid).closest('tr').find("input").attr("readonly");
                            // $("#update_record_"+nomineeid).closest('tr').find("button[name='update_btn']").attr("hidden");
                            // $("#update_record_"+nomineeid).closest('tr').find("button[name='edit_btn']").removeAttr("hidden",true);
                            // window.location.reload();
                        });
                /* swal("",response.success); */

            }
        }
    });
}


$(document).ready(function () {
    $(document).on('click', '#modal-submit', function () {

        if ($("input[name='radio_option']:checked").val() == undefined) {
            swal("", "please select at least one member");
            return false;
        }

        $.ajax({
            url: "/get_individual_family_details",
            type: "POST",
            data: {
                "family_id": $("input[name='radio_option']:checked").val()
            },
            async: false,
            dataType: "json",
            success: function (response) {
                // console.log(response);
                // console.log(response.constructor);
                if (response.length != 0) {


                    selectChange.find("#nominee_fname").val(response.family_firstname);
                    selectChange.find("#nominee_lname").val(response.family_lastname);
                    selectChange.find("input[name='nominee_dob']").val(response.family_dob);
                    selectChange.find("#family_id").val(response.family_id);

                }

                $("#myModal").modal("hide");
            }
        });
    });
    $(document).on('click', '#add_new', function () {


        selectChange.find("#nominee_fname").val("");
        selectChange.find("#nominee_lname").val("");
        selectChange.find("#family_id").val("");
        selectChange.find("input[type='text'][name='nominee_dob']").val("");
        $("#myModal").modal("hide");


    });

    //change of selected tag
    $('body').on('click', '.nominee_dob', function (e) {

        $(this)
                .removeClass('hasDatepicker')
//                .removeData('datepicker')
//                .unbind()
                .datepicker({
                    dateFormat: "dd-mm-yy",
                    prevText: '<i class="fa fa-angle-left"></i>',
                    nextText: '<i class="fa fa-angle-right"></i>',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "-100Y:+0",
                    maxDate: new Date(),
                    minDate: "-100Y +1D",

                    onSelect: function (dateText, inst) {
                        // debugger;
                        selectChange = $(this).closest(".row");
                        $(this).val(dateText);
                        var nominee_bday = dateText.split("-");
                        var today = new Date();
                        var age_distance = (today.getFullYear() - nominee_bday[2]);
                        selectChange.find(".guardian_div").html("");
                        if (age_distance < 18)
                        {
                            selectChange.append('<div class="col-md-12 guardian_div"><h6 class="color-1 mt-5"> Guardian Details </h6> <div class="row"> <div class="col-lg-3"> <div class="form-group"> <label class="col-form-label">Relation With Nominee</label> <input class="form-control guardian_relation" type="text" value="" id="guardian_relation" autocomplete="off" name="guardian_relation"> </div> </div> <div class="col-lg-2"> <div class="form-group"> <label for="example-text-input" class="col-form-label">First Name</label> <input class="form-control guardian_fname" type="text" value="" id="guardian_fname" autocomplete="off" name="guardian_fname"> </div> </div> <div class="col-lg-2"> <div class="form-group"> <label for="example-text-input" class="col-form-label">Last Name</label> <input class="form-control guardian_lname" type="text" autocomplete="off" value="" id="guardian_lname" name="guardian_lname"> </div> </div> <div class="col-lg-2"> <div class="form-group"> <label for="example-date-input" class="col-form-label">Date Of Birth </label> <input class="form-control guardian_dob" type="" autocomplete="off" name="guardian_dob" readonly="readonly"> </div> </div> </div> </div>');
                        } else
                        {
                            selectChange.find(".guardian_div").html("");
                        }
                    }
                });
        $(this).datepicker('show');
//$("a .ui-datepicker-prev").click();
    });

    $(document).on('click', '.guardian_dob', function (e) {
        $(this).removeClass("hasDatepicker");
// $(this).removeAttr("id");
        $(this).datepicker({
            dateFormat: "dd-mm-yy",
            prevText: '<i class="fa fa-angle-left"></i>',
            nextText: '<i class="fa fa-angle-right"></i>',
            changeMonth: true,
            changeYear: true,
            yearRange: "-100Y:-18Y",
            maxDate: "-18Y",
            minDate: "-100Y:-18Y",
            onSelect: function (dateText, inst) {
                $(this).val(dateText);
            }
        });
        $(this).datepicker('show');
    });

    $('#cancl').on("click", function () {
        $('#policy_nominee_form_data')[0].reset();
    });



    $('body').on("keyup", ".nominee_fname", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z s]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('body').on("keyup", ".nominee_lname", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z s]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('body').on("keyup", ".nominee_relation", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z-]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('body').on("keyup", ".guardian_relation", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z-]/g, function (str) {
                return '';
            }));
        }
        return;
    });

    $('body').on("keyup", ".share_per", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });

    $('body').on("keyup", ".nominee_firstname", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z s]/g, function (str) {
                return '';
            }));
        }
        return;
    });

    $('body').on("keyup", ".rel_nominee", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z]/g, function (str) {
                return '';
            }));
        }
        return;
    });

    $('body').on("keyup", ".nominee_lastname", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z s]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('body').on("keyup", ".guardian_name", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z s]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('body').on("keyup", ".guardian_rel", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z-]/g, function (str) {
                return '';
            }));
        }
        return;
    });

    /*  $('body').on("keyup",".guardian_relation",function(e) {  
     var $th = $(this);
     
     if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
     $th.val( $th.val().replace(/[^A-Za-z]/g, function(str) { return ''; } ) );
     }return;
     }); */
    $('body').on("keyup", ".guardian_fname", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z s]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('body').on("keyup", ".guardian_lname", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z s]/g, function (str) {
                return '';
            }));
        }
        return;
    });








    



// Start of on load append policy number in add member into policy
    $.ajax({
        url: "/get_all_policy_no",
        type: "POST",
        async: false,
        data: {"employer": "true"},
        dataType: "json",
        success: function (response) {
            // console.log(response);
            $('#policy_detail').empty();
            for (i = 0; i < response.length; i++) {

                $('#policy_detail').append('<option value="' + response[i].policy_detail_id + '">' + response[i].policy_sub_type_name + '</option>');

            }
        }

    });



    var max_fields = 5; //maximum input boxes allowed
    var wrapper = $(".wrapper");
    var add_button = $(".add_more_form");

    var x = 1;
    add_button.click(function (e) {
        var family_date_birth = $("input[name=nominee_dob]").val();

//$(".nominee_relation option[value= "+z+"]").remove();
        var first_name = $("input[name=nominee_fname]").val();
        var last_name = $("input[name=nominee_lname]").val();

        if (family_date_birth == '' || first_name == '' || last_name == '') {
            swal("", "Please First fill the form");

        } else {
            e.preventDefault();
            if (x < max_fields) {
                x++;


                var clone = $(".form_row_data1:first").clone().find(".guardian_div").html("").end().find("span").text("").end().find("input[type='text'] ").val("").end().find(".nominee_dob").attr("id", "").removeClass('hasDatepicker').removeData('datepicker').unbind().end().append('<a  href="#" class="remove_field">Remove</a>');

                wrapper.append(clone);

            }

        }
    });

    wrapper.on("click", ".remove_field", function (e) {
        e.preventDefault();
        $(this).parent('div').remove();
        x--;
    });

    $("#reset").click(function () {
        // window.location.reload();
    });

});
