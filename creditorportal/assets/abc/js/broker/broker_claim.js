$(document).ready(function() {
    var d = new Date();
    var strDate = d.getDate() + "-" + (d.getMonth() + 1) + "-" + d.getFullYear();
    var date = strDate.split("-");
    var display_date = date[1] + '-' + date[0] + '-' + date[2];
    $("#hospitalization_date").datepicker({
        changeMonth: true,
        changeYear: true,
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: "dd-mm-yy",
        maxDate: new Date(display_date),
        onSelect: function(selected) {

            var date = $(this).datepicker("getDate");
            var tempStartDate = new Date(date);
            var $returning_on = $("#discharge_date");
            tempStartDate.setDate(date.getDate() + 1);

            $returning_on.datepicker("option", "minDate", tempStartDate);
            $returning_on.datepicker("option", "maxDate", new Date());
        }
    });
    $("#discharge_date").datepicker({
        changeMonth: true,
        changeYear: true,
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: "dd-mm-yy",
        minDate: new Date(display_date),
        maxDate: new Date(display_date)
    });
    $.ajax({
        url: "/broker/get_all_employer",
        type: "POST",
        dataType: "json",
        success: function(response) {
            console.log(response);
            $('#employer_name').empty();
            $('#employer_name').append('<option value=""> Select Employer name</option>');
            for (i = 0; i < response.length; i++) {
                $('#employer_name').append('<option value="' + response[i].company_id + '">' + response[i].comapny_name + '</option>');
            }
        }
    });

    $("#employer_name").change(function() {
        var company_id = $(this).val();
        $.ajax({
            url: "/broker/get_all_employee_from_employer",
            type: "POST",
            data: {company_id: company_id},
            dataType: "json",
            success: function(response) {
                $('#emp_name').empty();
                $('#emp_name').append('<option value=""> Select Employee name</option>');
                for (i = 0; i < response.length; i++) {
                    $('#emp_name').append('<option value="' + response[i].emp_id + '">' + response[i].emp_firstname + ' ' + response[i].emp_lastname + '</option>');
                }
            }
        });
    });

    $("#emp_name").change(function() {
        var emp_id = $(this).val();
        $.ajax({
            url: "/broker/get_all_policy_no",
            type: "POST",
            data: {emp_id: emp_id},
            dataType: "json",
            success: function(response) {
                $('#policy_no').empty();
                $('#policy_no').append('<option value=""> Select Policy No</option>');
                for (i = 0; i < response.length; i++) {
//         	  	$('#policy_no').append('<option value="'+ response[i].policy_no +'">' + response[i].policy_no +'</option>');
                    if (response[i].policy_sub_type_name == 'Group Mediclaim')
                    {
                        $('#policy_no').append('<option selected class="active" value="' + response[i].policy_no + '">' + response[i].policy_sub_type_name + '</option>');
                    }
                }

                var policy_no = $("#policy_no").find("option:selected").val();
                var emp_id = $("#emp_name").val();
                var company_id = $("#employer_name").val();
                $.ajax({
                    url: "/broker/get_family_membername_from_policy_no",
                    type: "POST",
                    data: {emp_id: emp_id, policy_no: policy_no, company_id: company_id},
                    dataType: "json",
                    success: function(response) {
                        $('#patient_name').empty();
                        $('#patient_name').append('<option value=""> Select patient name</option>');
                        $.each(response, function(index, value) {
                            $('#patient_name').append('<option data-rel="' + value.family_id + '" value="' + value.emp_member_id + '">' + value.name + '</option>');
                        });
                    }
                });
                $.ajax({
                    url: "/broker/get_family_memberrel_from_policy_no",
                    type: "POST",
                    data:
                            {policy_no: policy_no, emp_id: emp_id}
                    ,
                    async: true,
                    dataType: "json",
                    success: function(response) {
                        $('#relationship_status').empty();
                        $('#relationship_status').append('<option value=""> Select Realtionship status</option>');
                        for (i = 0; i < response.length; i++) {
                            $('#relationship_status').append('<option value="' + response[i].fr_id + '">' + response[i].relationship + '</option>');
                        }
                    }
                });
            }
        });
    });



    $('#patient_name').on('change', function() {
        $.ajax({
            url: "/broker/get_member_details",
            type: "POST",
            data: {
                "patient_id": this.value
            },
            async: true,
            dataType: "json",
            success: function(response) {
                for (i = 0; i < response.length; i++) {
                    $('#email_id').val(response[i].email);
                    $('#mobile_no').val(response[i].mob_no);
                    $('#relationship_status').val(response[i].relationship);
                }
            }
        });
    });

    $("#relationship_status").change(function() {
        var emp_id = $('#emp_name').val();
        var fr_id = $(this).val();
        var policy_no = $("#policy_no").val();
        $.ajax({
            url: "/broker/get_family_details_on_relationship",
            type: "POST",
            data: {fr_id: fr_id, policy_no: policy_no, emp_id: emp_id},
            async: true,
            dataType: "json",
            success: function(response) {
                $('#patient_name').empty();
                $.each(response, function(index, value) {
                    $('#patient_name').append('<option data-rel="' + value.family_relation_id + '" value="' + value.policy_member_id + '">' + value.name + '</option>');
                });
                $("#patient_name").trigger("change");
            }
        });
    });
    $('#mobile_no').keyup(function(e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function(str) {
                return '';
            }));
        }
        return;
    });
    $.validator.addMethod('valid_mobile', function(value, element, param) {
        var re = new RegExp('^[6-9][0-9]{9}$');
        return this.optional(element) || re.test(value); // Compare with regular expression
    }, 'Enter a valid 10 digit mobile number');

    $.validator.addMethod('validateEmail', function(value, element, param) {
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        return reg.test(value); // Compare with regular expression
    }, 'Please enter a valid email address.');

    $("#claim_save").validate({
        rules: {
            patient_name: {
                required: true
            },
            email_id: {
                required: true,
                email: true,
                validateEmail: true
            },
            relationship_status: {
                required: true
            },
            hospitalization_date: {
                required: true
            },
            mobile_no: {
                required: true,
                valid_mobile: true
            },
            discharge_date: {
                required: true
            }
        },
        messages: {
            patient_name: "Please specify patient name",
            email_id: "Please specify email id",
            email: "please enter valid email",
            relationship_status: "Please specify relationship_status",
            hospitalization_date: "Please specify hospitalization_date",
            mobile_no: "Please specify mobile_no",
            discharge_date: "Please specify discharge_date",

        },
        errorElement: 'div',
        errorPlacement: function(error, element) {
            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error)
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {

            $.ajax({
                url: "/broker_claims_save",
                type: "POST",
                data: $("#claim_save").serialize() + "&relation=" + $('#relationship_status option:selected').val() + "&patient=" + $('#patient_name option:selected').val() + "&data_override=" + "NO",
                async: true,
                success: function(response) {
                    var data_response = JSON.parse(response);
                    if (data_response.success) {
                        $("#claim_reimb_id").val(data_response.claim_id);
                        $("#claim_reimb_hospitalization_date").val(data_response.hospitalization_date);
                        $("#claim_reimb_discharge_date").val(data_response.discharge_date);
                        $("#v-pills-home").removeClass('active show');
                        $("#v-pills-profile").addClass('active show');
                        $("#hospital_save").removeAttr('style', 'pointer-events:none');
                    } else if (data_response.success_new_claim)
                    {
                        var checkstr = confirm(data_response.success_new_claim);
                        if (checkstr == true)
                        {
                            $.ajax({
                                url: "/employer_claims_save",
                                type: "POST",
                                data: $("#claim_save").serialize() + "&relation=" + $('#relationship_status option:selected').val() + "&patient=" + $('#patient_name option:selected').val() + "&data_override=" + "YES",
                                async: true,
                                success: function(result) {
                                    var data_response = JSON.parse(result);
                                    $("#claim_reimb_id").val(data_response.claim_id);
                                    $("#claim_reimb_hospitalization_date").val(data_response.hospitalization_date);
                                    $("#claim_reimb_discharge_date").val(data_response.discharge_date);
                                    $("#v-pills-home").removeClass('active show');
                                    $("#v-pills-profile").addClass('active show');
                                    $("#hospital_save").removeAttr('style', 'pointer-events:none');
                                }
                            });
                        } else
                        {
                            window.location.reload(true);
                        }
                    } else if (data_response.success_new_date_range)
                    {
                        $("#getCodeModal").modal("toggle");
                        $("#getCode").html(data_response.success_new_date_range);
                        window.location.reload(true);
                    } else
                    {
                        $.each(data_response.messages, function(key, value) {
                            var element = $('#' + key);
                            element.closest('div.data').find('p').remove();
                            element.after(value);
                        });
                        // $("#getCodeModal").modal("toggle");
                        // $("#getCode").html('Some Error Has Been Occured');
                    }
                }
            });
        }
    });

    // get state
    $.ajax({
        url: "/broker/get_all_states",
        type: "POST",
        async: true,
        dataType: "json",
        success: function(response) {
            console.log(response);
            $('#state_names').append('<option value="">select</option>');
            for (i = 0; i < response.length; i++) {
                $('#state_names').append('<option value="' + response[i].state_id + '">' + response[i].state_name + '</option>');
            }
        }
    });

    // state from city
    $('#state_names').on('change', function() {
        $.ajax({
            url: "/broker/get_city_from_states",
            type: "POST",
            data: {
                "state_names": this.value
            },
            async: true,
            dataType: "json",
            success: function(response) {
                $('#cities').empty();
                $('#cities').append('<option>Select City</option>');
                for (i = 0; i < response.length; i++) {
                    $('#cities').append('<option value="' + response[i].city_id + '">' + response[i].city_name + '</option>');
                }
            }
        });
    });
    $("body").on('click', ".bill_date", function() {
        event.stopPropagation();
        var bill_num = $('.bill_no').val();
        if (bill_num != '')
        {
            counter1 = 1;
            $('#btn_add').removeAttr('style', 'display:none');
        } else
        {

            $('#btn_add').attr('style', 'display:none');
        }

    });
    $("#btn_add").click(function(e) {
        e.stopPropagation();
        var newRow = $("<tr>");
        var cols = "";
        cols += '<td><input type="text" class="form-control bill_no" name="bill_no" value=""/></td>';
        cols += '<td><input type="" class="form-control bill_date" name="bill_date"></td>';
        cols += '<td><input type="text" class="form-control claim_amount" maxlength="7" name="claim_amount" value=""/></td>';
        cols += '<td><input type="text" class="form-control" name="comment" id="comment" value=""/></td>';
        cols += '<td><input type="text" name="cost" class="form-control cost" value=""></td>';
        cols += '<td><input type="button" name="del_btn" class="form-control del_btn" value="Delete"></td>';
        newRow.append(cols);
        $("#add_tbody").append(newRow);
        counter1++;
    });

    $("body").on('click', ".del_btn", function() {
        $(this).parent().parent().remove();
    });

    $('body').on('focus', ".bill_date", function() {
        var claim_reimb_hospitalization_date = $("#claim_reimb_hospitalization_date").val();
        var claim_reimb_discharge_date = $("#claim_reimb_discharge_date").val();
        var date = claim_reimb_hospitalization_date.split("-");
        var date1 = claim_reimb_discharge_date.split("-");
        var date = new Date(Number(date[2]), Number(date[1]) - 1, Number(date[0]) - 30).toLocaleString().split(',');
        var date1 = new Date(Number(date1[2]), Number(date1[1]) - 1, Number(date1[0]) + 60).toLocaleString().split(',');

        date = date[0].split("/").join("-").split("-");
        var beforedate = date[1] + '-' + date[0] + '-' + date[2];
        date1 = date1[0].split("/").join("-").split("-");
        var afterdate = date1[1] + '-' + date1[0] + '-' + date1[2];
        $(this).datepicker({
            dateFormat: "dd-mm-yy",
            prevText: '<i class="fa fa-angle-left"></i>',
            nextText: '<i class="fa fa-angle-right"></i>',
            changeMonth: true,
            changeYear: true,
            maxDate: afterdate,
            minDate: beforedate,
        });
    });
    $("body").on('keyup', ".claim_amount", function(e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function(str) {
                return '';
            }));
        }
        return;
    });
    $("#hospital_name").keyup(function(e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z\s]/g, function(str) {
                return '';
            }));
        }
        return;
    });
    $("body").on('keyup', "#diseases", function(e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Z a-z-,0-9\s]/g, function(str) {
                return '';
            }));
        }
        return;
    });
    $("body").on('keyup', "#comment", function(e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Z a-z-,0-9\s]/g, function(str) {
                return '';
            }));
        }
        return;
    });

    $('body').on("blur", "input[name^='bill_date'], input[name^='claim_amount']", function() {
        var sum = 0;
        var sum1 = 0;
        var sum2 = 0;
        var total_sum = 0
        $("#add_table > tbody > tr").each(function(k, v) {
            var $row = $(this);
            var bill_date = $row.find(".bill_date").val();
            var claim_amount = $row.find(".claim_amount").val();
            if (bill_date != '' && claim_amount != '')
            {
                var cost = $row.find(".cost").val();
                if (cost)
                {
                    if (cost == 'Hospitalization')
                    {
                        sum += parseFloat(claim_amount);
                    } else if (cost == 'Post-Hospitalization')
                    {
                        sum1 += parseFloat(claim_amount);
                    } else
                    {
                        sum2 += parseFloat(claim_amount);
                    }
                }
                total_sum += parseFloat(claim_amount);
            }
        });
        $("#post_hos").text(sum1);
        $("#pre_hos").text(sum2);
        $("#hos").text(sum);
        $("#total").text(total_sum);
    });

    $('body').on('change', "tr", function() {
        var bill_date_text = $(this).find(".bill_date").val();
        var cost_text = $(this).find(".cost");
        var claim_reimb_hospitalization_date = $("#claim_reimb_hospitalization_date").val();
        var claim_reimb_discharge_date = $("#claim_reimb_discharge_date").val();
        if (bill_date_text) {
            if ($.datepicker.parseDate('dd-mm-yy', bill_date_text) < $.datepicker.parseDate('dd-mm-yy', claim_reimb_hospitalization_date)) {
                cost_text.val('Pre-Hospitalization');
            } else if ($.datepicker.parseDate('dd-mm-yy', bill_date_text) > $.datepicker.parseDate('dd-mm-yy', claim_reimb_discharge_date))
            {
                cost_text.val('Post-Hospitalization');
            } else
            {
                cost_text.val('Hospitalization');
            }
        }
    });

    $("#submit_add").click(function() {
        var count = 0;
        var hospital_name = $("#hospital_name").val();
        var claim_reimb_id = $("#claim_reimb_id").val();
        var hospital_address = $("#hospital_address").val();
        var state_names = $("#state_names").val();
        var reason = $("#reason").val();
        var cities = $("#cities").val();
        var diseases = $("#diseases").val();
        var bill_no = document.getElementsByName("bill_no");
        var bill_date = document.getElementsByName("bill_date");
        var claim_amount = document.getElementsByName("claim_amount");
        var comment = document.getElementsByName("comment");
        var cost = document.getElementsByName("cost");
        var bill_noArr = [];
        var bill_dateArr = [];
        var claim_amountArr = [];
        var commentArr = [];
        var costArr = [];

        for (i = 0; i < bill_no.length; ++i) {
            if (bill_no[i].value.trim().length > 0) {
                bill_no[i].style = "border-color:black";
            } else {
                bill_no[i].style = "border-color:red";
                ++count;
            }
            if (bill_date[i].value.trim().length > 0) {
                bill_date[i].style = "border-color:black";
            } else {
                bill_date[i].style = "border-color:red";
                ++count;
            }
            if (claim_amount[i].value.trim().length > 0) {
                claim_amount[i].style = "border-color:black";
            } else {
                claim_amount[i].style = "border-color:red";
                ++count;
            }
            if (comment[i].value.trim().length > 0) {
                comment[i].style = "border-color:black";
            } else {
                comment[i].style = "border-color:red";
                ++count;
            }
            if (cost[i].value.trim().length > 0) {
                cost[i].style = "border-color:black";
            } else {
                cost[i].style = "border-color:red";
                ++count;
            }
            bill_noArr.push(bill_no[i].value);
            bill_dateArr.push(bill_date[i].value);
            claim_amountArr.push(claim_amount[i].value);
            commentArr.push(comment[i].value);
            costArr.push(cost[i].value);
        }
        var sorted_arr = bill_noArr.sort();
        var results = [];
        for (var i = 0; i < bill_noArr.length - 1; i++) {
            if (sorted_arr[i + 1] == sorted_arr[i]) {
                results.push(sorted_arr[i]);
            }
        }
        if (results != '')
        {
            alert('cannot enter double bill no');
            return;
        }
        if (hospital_name.length == 0)
        {
            $("#div_hospital_name").removeAttr('style', 'dispaly:none');
            $("#div_hospital_name").attr('style', 'color:red');
            ++count;
        } else
        {
            $("#div_hospital_name").text('');
        }

        if (state_names.length == 0)
        {
            $("#div_state").removeAttr('style', 'dispaly:none');
            $("#div_state").attr('style', 'color:red');
            ++count;
        } else
        {
            $("#div_state").text('');
        }

        if (reason.length == 0)
        {
            $("#div_reason").removeAttr('style', 'dispaly:none');
            $("#div_reason").attr('style', 'color:red');
            ++count;
        } else
        {
            $("#div_reason").text('');
        }
        if (cities == null)
        {
            $("#div_city").removeAttr('style', 'dispaly:none');
            $("#div_city").attr('style', 'color:red');
            ++count;
        } else
        {
            $("#div_city").text('');
        }

        if (diseases.length == 0)
        {
            $("#div_diseases").removeAttr('style', 'dispaly:none');
            $("#div_diseases").attr('style', 'color:red');
            ++count;
        } else
        {
            $("#div_diseases").text('');
        }
        if (count > 0) {
            alert("Plase check for error");
            return;
        }
        $.post("/broker/save_hospitalizationdetails", {
            "bill_noArr": JSON.stringify(bill_noArr),
            "bill_dateArr": JSON.stringify(bill_dateArr),
            "claim_amountArr": JSON.stringify(claim_amountArr),
            "commentArr": JSON.stringify(commentArr),
            "costArr": JSON.stringify(costArr),
            "hospital_name": hospital_name,
            "hospital_address": hospital_address,
            "state_names": state_names,
            "reason": reason,
            "cities": cities,
            'diseases': diseases,
            "claim_reimb_id": claim_reimb_id
        }, function(e) {
            var data = JSON.parse(e);
            if (data.success) {
                if (data.id)
                {
                    $("#claim_reimb_hospital_id").val(data.id);
                }
                $("#v-pills-profile").removeClass('active show');
                $("#v-pills-messages").addClass('active show');
                $("#v-pills-messages-tab").trigger("click");
                $("#bill_upload_form").removeAttr('style', 'pointer-events:none');
            } else {
                $.each(data.messages, function(key, value) {
                    var element = $('#' + key);
                    element.closest('div.data').find('p').remove();
                    element.after(value);
                });
                // alert(data.error);
            }
        });
    });

    var counter = 20;
    $("#file_add").click(function() {
        var newRow = $("<div class='col-md-6'>");
        var cols = "";
        var cols = "<div class='input-group mb-3'><div id= 'bill_doc' class='form-group'><input type='file' name='docfile'><a name='imageClick' data-href='' target='_blank' class='tooltipped' data-position='bottom' data-tooltip='Preview Document'><i class='fa fa-file-image-o' aria-hidden='true'></i></a><input type='button' class='del_doc_btn' name='del_doc_btn' value='Delete'></div></div></div>";
        newRow.append(cols);
        $(".add_file_data").append(newRow);
        counter++;

    });

    $("body").on('click', ".del_doc_btn", function() {
        $(this).parent().parent().remove();
    });

    function readURL(input) {
        if (input.files && input.files[0]) {

            var reader = new FileReader();
            reader.onload = function(e) {
                $(input).next().attr('data-href', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("body").on('click', "a[name='imageClick']", function() {
        var attr = $(this).attr("data-href");
        let iframeWindow = window.open("");
        iframeWindow.document.write("<iframe width='100%' height='100%' src='" + attr + "'></iframe>");
    });

    $("body").on('change', "input[type=file]", function() {
        readURL(this);
    });
    $('#save').click(function() {
        var docfile = document.getElementsByName("docfile");
        var claim_reimb_id = $("#claim_reimb_id").val();
        var claim_reimb_hosp_id = $("#claim_reimb_hospital_id").val();
        var form_data = new FormData();
        for (i = 0; i < docfile.length; ++i) {
            form_data.append("docfile" + i, docfile[i].files[0]);
        }
        form_data.append("fileSize", docfile.length);
        form_data.append("claim_id", claim_reimb_id);
        form_data.append("claim_reimb_hosp_id", claim_reimb_hosp_id);
        $.ajax({
            type: 'POST',
            url: "/broker/save_claims_bill",
            data: form_data,
            async: false,
            cache: false,
            dataType: 'json',
            contentType: false,
            processData: false,
            mimeType: "multipart/form-data",
            success: function(data) {
                if (data.success == true) {
//                    $("#getCodeModal").modal("toggle");
//                    $("#getCode").html("Bill Documents Successfully saved");
                    swal({
                        title: "Success",
                        text: "Bill Documents Successfully savedy",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    })
                            .then((willDelete) => {
                                if (willDelete) {
                                    $("#bill_upload_form")[0].reset();
                                    $("#claim_save")[0].reset();
                                    $("#hospital_save")[0].reset();
                                    window.location.reload();
                                }



                            });

//                    window.location.reload();
                } else {
//                    $("#getCodeModal").modal("toggle");
//                    $("#getCode").html(data.success);
//                    window.location.reload();
                    swal({
                        title: "Success",
                        text: data.success,
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    })
                            .then((willDelete) => {
                                if (willDelete) {
                                    window.location.reload();
                                }



                            });
                }
            }
        });
    });
    $("#data").click(function() {
        window.location.reload();
    });
    $(".cancelled").click(function() {
        window.location.reload();
    });

});