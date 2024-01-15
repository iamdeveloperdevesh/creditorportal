$(document).ready(function() {
    var sum_1 = 0;
    $(".1_data").blur(function() {
        sum_1 += parseInt($(this).val());
        $("#1_age_total_emp").val(sum_1);
    });

    var sum_2 = 0;
    $(".2_data").blur(function() {
        sum_2 += parseInt($(this).val());
        $("#2_age_total_emp").val(sum_2);
    });

    var sum_3 = 0;
    $(".3_data").blur(function() {
        sum_3 += parseInt($(this).val());
        $("#3_age_total_emp").val(sum_3);
    });

    var sum_4 = 0;
    $(".4_data").blur(function() {
        sum_4 += parseInt($(this).val());
        $("#4_age_total_emp").val(sum_4);
    });

    $("body").on('blur', "#age_band_4", function() {
        event.stopPropagation();
        var data = $('#age_band_4').val();
        if (data != '') {
            counter1 = 5;
            $('#btn_add').removeAttr('style', 'display:none');
        } else {
            $('#btn_add').attr('style', 'display:none');
        }
    });
    $('#mobile_number').keyup(function(e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function(str) {
                return '';
            }));
        }
        return;
    });
    $('#mobile_number').keyup(function(e) {
        var $th = $(this).val();
        if ($th > 10)
        {
            return false;
        }
    });
    $(".age_band").keypress(function(e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && String.fromCharCode(e.which) != '-' && (e.which < 48 || e.which > 57)) {
            //display error message
            swal("", "please Enter Valid Age Band");
            return false;
        }
    });
    $(".num").keypress(function(e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            //display error message
            swal("", "please Enter Only Numbers");
            return false;

        }
    });
    $("#btn_add").click(function(e) {
        e.stopPropagation();
        var newRow = $("<tr>");
        var cols = "";
        cols += '<th scope="row"><h6><input class="form-control age_band" type="text" name="age_band_' + counter1 + '" id="age_band_' + counter1 + '"></h6></th>';
        cols += '<td style="width: 130px;"><div class="form-group"><input class="form-control num ' + counter1 + '_data" type="text" value="" name="' + counter1 + '_age_one_lakh" id="' + counter1 + '_age_one_lakh"></div></td>';
        cols += '<td style="width: 130px;"><div class="form-group"><input class="form-control num ' + counter1 + '_data" type="text" value="" name="' + counter1 + '_age_two_lakh" id="' + counter1 + '_age_two_lakh"></div></td>';
        cols += '<td style="width: 130px;"><div class="form-group"><input class="form-control num ' + counter1 + '_data" type="text" value="" name="' + counter1 + '_age_three_lakh" id="' + counter1 + '_age_three_lakh"></div></td>';
        cols += '<td style="width: 130px;"><div class="form-group"><input class="form-control num ' + counter1 + '_data" type="text" value="" name="' + counter1 + '_age_four_lakh" id="' + counter1 + '_age_four_lakh"></div></td>';
        cols += '<td style="width: 130px;"><div class="form-group"><input class="form-control num ' + counter1 + '_data" type="text" value="" name="' + counter1 + '_age_dropdown" id="' + counter1 + '_age_dropdown"></div></td>';
        cols += '<td style="width: 130px;"><div class="form-group"><input class="form-control num" readonly="" type="text" value="" name="' + counter1 + '_age_total_emp" id="' + counter1 + '_age_total_emp"></div></td>';
        cols += '<td style="width: 130px;"><div class="form-group"><input type="button" name="del_btn" class="form-control del_btn" value="Delete"></div></td>';
        newRow.append(cols);
        $("#add_tbody").append(newRow);
        counter1++;
        $('#btn_add').attr('data-id', counter1);
        if (counter1 > 4) {
            var countr_data = (counter1 - 1);
            var sum_data = 0;
            $("." + countr_data + "_data").blur(function() {
                sum_data += parseInt($(this).val());
                $("#" + countr_data + "_age_total_emp").val(sum_data);
            });
        }

    });

    $("body").on('click', ".del_btn", function() {
        $(this).parent().parent().parent().remove();
    });
    var counter = 1;
    $("#btn_submit").click(function() {
        var data_string = '';
        $('#add_tbody input[type=text]').each(function() {
            var lastCounter = $('#btn_add').attr("data-id");
            var age_band_data = $('#age_band_' + counter).val();
            if (age_band_data != undefined) {
                data_string += age_band_data + ',';
                counter++;
            }
        });
        var data_string = data_string.replace(/,\s*$/, "");

        var finalcount = 0;
        var spilit_data = data_string.split(",");
        for (var i = 0; i < spilit_data.length; i++) {
            var data_spilit = spilit_data[i].split("-");
            if (i < (spilit_data.length - 1)) {
                if (spilit_data[i].split("-")[1] > spilit_data[i + 1].split("-")[0]) {

                    swal("", "please Enter Valid Age Band" + spilit_data[i + 1] + "");
                    window.location.reload();
                } else {
                    finalcount++;
                }
            }
        }
//        alert(spilit_data);
        if (spilit_data[finalcount].split("-")[1] > 100)
        {
            swal("", "please Enter Valid Age Band");
            return false;
        }


        if ((finalcount + 1) == (counter - 1)) {
            var th = document.querySelectorAll("table thead th");
            var arr = [];
            th.forEach(function(e) {
//                console.log(e.querySelector("select"))
                if (e.querySelector("select"))
                    arr.push($(th[5].querySelector("select")).val());
                else
                    arr.push(e.innerText.split(" ")[0]);
            });

            var form_data = $("#form_data").serialize() + "&arr=" + JSON.stringify(arr);

            $.ajax({
                url: "/broker/submit_quote_data",
                type: "POST",
                async: false,
                data: form_data,
                dataType: "json",
                success: function(response) {
//                    console.log(response['status']);
                    if (response['status'] == true) {
                        window.location = '/broker_show_quote?company_id=' + response['id'];
                    } else
                    {
                        swal("", "please Enter All Data");
                        return false;
                    }
                }
            });
        }
    });
    $.ajax({
        url: "/get_quote_all_data",
        type: "POST",
        async: false,
        dataType: "json",
        data: {company_id: $("#company_id").val()},
        success: function(response) {
            if (response != null) {
                if (response['maternity_benefit'] == 1) {
                    $("#maternity_option_1 option[value='1']").attr("selected", "selected");
                } else {
                    $("#maternity_option_1 option[value='0']").attr("selected", "selected");
                }

                if (response['maternity_benefit_second'] == 1) {
                    $("#maternity_option_2 option[value='1']").attr("selected", "selected");
                } else {
                    $("#maternity_option_2 option[value='0']").attr("selected", "selected");
                }

                if (response['maternity_benefit_third'] == 1) {
                    $("#maternity_option_3 option[value='1']").attr("selected", "selected");
                } else {
                    $("#maternity_option_3 option[value='0']").attr("selected", "selected");
                }

                if (response['disease_specific_waiting_period'] == 1) {
                    $("#disease_specific_waiting_period_option_1 option[value='1']").attr("selected", "selected");
                } else {
                    $("#disease_specific_waiting_period_option_1 option[value='0']").attr("selected", "selected");
                }

                if (response['disease_specific_waiting_period_second'] == 1) {
                    $("#disease_specific_waiting_period_option_2 option[value='1']").attr("selected", "selected");
                } else {
                    $("#disease_specific_waiting_period_option_2 option[value='0']").attr("selected", "selected");
                }

                if (response['disease_specific_waiting_period_third'] == 1) {
                    $("#disease_specific_waiting_period_option_3 option[value='1']").attr("selected", "selected");
                } else {
                    $("#disease_specific_waiting_period_option_3 option[value='0']").attr("selected", "selected");
                }

                if (response['corporate_buffer'] != 0) {
                    $("#corporate_buffer_option_1").find('option[value="' + response['corporate_buffer'] + '"]').attr("selected", "selected");
                }

                if (response['corporate_buffer_second'] != 0) {
                    $("#corporate_buffer_option_2").find('option[value="' + response['corporate_buffer_second'] + '"]').attr("selected", "selected");
                }

                if (response['corporate_buffer_third'] != 0) {
                    $("#corporate_buffer_option_3").find('option[value="' + response['corporate_buffer_third'] + '"]').attr("selected", "selected");
                }
            }
        }
    });
    $("#maternity_option_1").change(function() {
        var value = $(this).val();
        if (value == 1) {
            var option_1_value = $("#option_1").val();
            var new_option1_val = ($("#get_lives").val() * 25);
            var add_data = parseInt(option_1_value) + parseInt(new_option1_val);
            $("#display_option_1").text(add_data);
            $.ajax({
                url: "/submit_quote_all_data",
                type: "POST",
                async: false,
                dataType: "json",
                data: {option_1: add_data, company_id: $("#company_id").val(), option_2: $("#option_2").val(), option_3: $("#option_3").val(), maternity_benefit: value, maternity_benefit_second: 0, maternity_benefit_third: 0},
                success: function(response) {
                    if (response == true) {
                        window.location.reload();
                    }
                }
            });
        } else
        {
            var option_1_value = $("#option_1").val();
            var new_option1_val = ($("#get_lives").val() * 25);
            var add_data = parseInt(option_1_value) - parseInt(new_option1_val);
            $("#display_option_1").text(add_data);
            $.ajax({
                url: "/submit_quote_all_data",
                type: "POST",
                async: false,
                dataType: "json",
                data: {option_1: add_data, company_id: $("#company_id").val(), option_2: $("#option_2").val(), option_3: $("#option_3").val(), maternity_benefit: value, maternity_benefit_second: 0, maternity_benefit_third: 0},
                success: function(response) {
                    if (response == true) {
                        window.location.reload();
                    }
                }
            });
        }
    });
    $("#disease_specific_waiting_period_option_1").change(function() {
        var value = $(this).val();
//        if (value == 1) {
//            var option_1_value = $("#option_1").val();
//            var new_option1_val = (option_1_value * 25);
//            $("#option_1").val(new_option1_val);
//            $("#display_option_1").text(new_option1_val);
//        }
        if (value == 1) {
            var option_1_value = $("#option_1").val();
            var new_option1_val = ($("#get_lives").val() * 25);
            var add_data = parseInt(option_1_value) + parseInt(new_option1_val);
            $("#display_option_1").text(add_data);
            $.ajax({
                url: "/submit_quote_diseases_all_data",
                type: "POST",
                async: false,
                dataType: "json",
                data: {option_1: add_data, company_id: $("#company_id").val(), option_2: $("#option_2").val(), option_3: $("#option_3").val(), disease_specific_waiting_period: value, disease_specific_waiting_period_second: 0, disease_specific_waiting_period_third: 0},
                success: function(response) {
                    if (response == true) {
                        window.location.reload();
                    }
                }
            });
        } else
        {
            var option_1_value = $("#option_1").val();
            var new_option1_val = ($("#get_lives").val() * 25);
            var add_data = parseInt(option_1_value) - parseInt(new_option1_val);
            $("#display_option_1").text(add_data);
            $.ajax({
                url: "/submit_quote_all_data",
                type: "POST",
                async: false,
                dataType: "json",
                data: {option_1: add_data, company_id: $("#company_id").val(), option_2: $("#option_2").val(), option_3: $("#option_3").val(), disease_specific_waiting_period: value, disease_specific_waiting_period_second: 0, disease_specific_waiting_period_third: 0},
                success: function(response) {
                    console.log(response);
                }
            });
        }
    });
    $("#corporate_buffer_option_1").change(function() {
        var value = $(this).val();
//        if (value != '') {
//            var option_1_value = $("#option_1").val();
//            var new_option1_val = (option_1_value * 25);
//            $("#option_1").val(new_option1_val);
//            $("#display_option_1").text(new_option1_val);
//        }
        if (value != '') {
            var option_1_value = $("#option_1").val();
            var new_option1_val = ($("#get_lives").val() * 25);
            var add_data = parseInt(option_1_value) + parseInt(new_option1_val);
            $("#display_option_1").text(add_data);
            $.ajax({
                url: "/submit_quote_corporate_all_data",
                type: "POST",
                async: false,
                dataType: "json",
                data: {option_1: add_data, company_id: $("#company_id").val(), option_2: $("#option_2").val(), option_3: $("#option_3").val(), corporate_buffer: value},
                success: function(response) {
                    if (response == true) {
                        window.location.reload();
                    }
                }
            });
        }
    });
    $("#maternity_option_2").change(function() {
        var value = $(this).val();
//        if (value == 1) {
//            var option_2_value = $("#option_2").val();
//            var new_option2_val = (option_2_value * 25);
//            $("#option_2").val(new_option2_val);
//            $("#display_option_2").text(new_option2_val);
//        }
        if (value == 1) {
            var option_2_value = $("#option_2").val();
            var new_option1_val = ($("#get_lives").val() * 25);
            var add_data = parseInt(option_2_value) + parseInt(new_option1_val);
            $("#display_option_2").text(add_data);
            $.ajax({
                url: "/submit_quote_all_data",
                type: "POST",
                async: false,
                dataType: "json",
                data: {option_1: $("#option_1").val(), company_id: $("#company_id").val(), option_2: add_data, option_3: $("#option_3").val(), maternity_benefit: 0, maternity_benefit_second: value, maternity_benefit_third: 0},
                success: function(response) {
                    if (response == true) {
                        window.location.reload();
                    }
                }
            });
        } else
        {
            var option_2_value = $("#option_2").val();
            var new_option1_val = ($("#get_lives").val() * 25);
            var add_data = parseInt(option_2_value) - parseInt(new_option1_val);
            $("#display_option_1").text(add_data);
            $.ajax({
                url: "/submit_quote_all_data",
                type: "POST",
                async: false,
                dataType: "json",
                data: {option_1: $("#option_1").val(), company_id: $("#company_id").val(), option_2: add_data, option_3: $("#option_3").val(), maternity_benefit: 0, maternity_benefit_second: value, maternity_benefit_third: 0},
                success: function(response) {
                    if (response == true) {
                        window.location.reload();
                    }
                }
            });
        }
    });
    $("#disease_specific_waiting_period_option_2").change(function() {
        var value = $(this).val();
//        if (value == 1) {
//            var option_2_value = $("#option_2").val();
//            var new_option2_val = (option_2_value * 25);
//            $("#option_2").val(new_option2_val);
//            $("#display_option_2").text(new_option2_val);
//        }
        if (value == 1) {
            var option_2_value = $("#option_2").val();
            var new_option1_val = ($("#get_lives").val() * 25);
            var add_data = parseInt(option_2_value) + parseInt(new_option1_val);
            $("#display_option_2").text(add_data);
            $.ajax({
                url: "/submit_quote_diseases_all_data",
                type: "POST",
                async: false,
                dataType: "json",
                data: {option_1: $("#option_1").val(), company_id: $("#company_id").val(), option_2: add_data, option_3: $("#option_3").val(), disease_specific_waiting_period: 0, disease_specific_waiting_period_second: value, disease_specific_waiting_period_third: 0},
                success: function(response) {
                    if (response == true) {
                        window.location.reload();
                    }
                }
            });
        } else
        {
            var option_2_value = $("#option_2").val();
            var new_option1_val = ($("#get_lives").val() * 25);
            var add_data = parseInt(option_2_value) - parseInt(new_option1_val);
            $("#display_option_2").text(add_data);
            $.ajax({
                url: "/submit_quote_diseases_all_data",
                type: "POST",
                async: false,
                dataType: "json",
                data: {option_1: $("#option_1").val(), company_id: $("#company_id").val(), option_2: add_data, option_3: $("#option_3").val(), disease_specific_waiting_period: 0, disease_specific_waiting_period_second: value, disease_specific_waiting_period_third: 0},
                success: function(response) {
                    if (response == true) {
                        window.location.reload();
                    }
                }
            });
        }
    });
    $("#corporate_buffer_option_2").change(function() {
        var value = $(this).val();
//        if (value != 0) {
//            var option_2_value = $("#option_2").val();
//            var new_option2_val = (option_2_value * 25);
//            $("#option_2").val(new_option2_val);
//            $("#display_option_2").text(new_option2_val);
//        }
        if (value != '') {
            var option_2_value = $("#option_2").val();
            var new_option1_val = ($("#get_lives").val() * 25);
            var add_data = parseInt(option_2_value) + parseInt(new_option1_val);
            $("#display_option_2").text(add_data);
            $.ajax({
                url: "/submit_quote_corporate_all_data_second",
                type: "POST",
                async: false,
                dataType: "json",
                data: {option_1: $("#option_1").val(), company_id: $("#company_id").val(), option_2: add_data, option_3: $("#option_3").val(), corporate_buffer: value},
                success: function(response) {
                    if (response == true) {
                        window.location.reload();
                    }
                }
            });
        }
    });
    $("#maternity_option_3").change(function() {
        var value = $(this).val();
//        if (value == 1) {
//            var option_3_value = $("#option_3").val();
//            var new_option3_val = (option_3_value * 25);
//            $("#option_3").val(new_option3_val);
//            $("#display_option_2").text(new_option3_val);
//        }
        if (value == 1) {
            var option_3_value = $("#option_3").val();
            var new_option1_val = ($("#get_lives").val() * 25);
            var add_data = parseInt(option_3_value) + parseInt(new_option1_val);
            $("#display_option_3").text(add_data);
            $.ajax({
                url: "/submit_quote_all_data",
                type: "POST",
                async: false,
                dataType: "json",
                data: {option_1: $("#option_1").val(), company_id: $("#company_id").val(), option_2: $("#option_2").val(), option_3: add_data, maternity_benefit: 0, maternity_benefit_second: 0, maternity_benefit_third: value},
                success: function(response) {
                    if (response == true) {
                        window.location.reload();
                    }
                }
            });
        } else
        {
            var option_3_value = $("#option_3").val();
            var new_option1_val = ($("#get_lives").val() * 25);
            var add_data = parseInt(option_3_value) - parseInt(new_option1_val);
            $("#display_option_1").text(add_data);
            $.ajax({
                url: "/submit_quote_all_data",
                type: "POST",
                async: false,
                dataType: "json",
                data: {option_1: $("#option_1").val(), company_id: $("#company_id").val(), option_2: $("#option_2").val(), option_3: add_data, maternity_benefit: 0, maternity_benefit_second: 0, maternity_benefit_third: value},
                success: function(response) {
                    if (response == true) {
                        window.location.reload();
                    }
                }
            });
        }
    });
    $("#disease_specific_waiting_period_option_3").change(function() {
        var value = $(this).val();
//        if (value == 1) {
//            var option_3_value = $("#option_3").val();
//            var new_option3_val = (option_3_value * 25);
//            $("#option_3").val(new_option3_val);
//            $("#display_option_2").text(new_option3_val);
//        }
        if (value == 1) {
            var option_3_value = $("#option_3").val();
            var new_option1_val = ($("#get_lives").val() * 25);
            var add_data = parseInt(option_3_value) + parseInt(new_option1_val);
            $("#display_option_3").text(add_data);
            $.ajax({
                url: "/submit_quote_diseases_all_data",
                type: "POST",
                async: false,
                dataType: "json",
                data: {option_1: $("#option_1").val(), company_id: $("#company_id").val(), option_2: $("#option_2").val(), option_3: add_data, disease_specific_waiting_period: 0, disease_specific_waiting_period_second: 0, disease_specific_waiting_period_third: value},
                success: function(response) {
                    if (response == true) {
                        window.location.reload();
                    }
                }
            });
        } else
        {
            var option_3_value = $("#option_3").val();
            var new_option1_val = ($("#get_lives").val() * 25);
            var add_data = parseInt(option_3_value) - parseInt(new_option1_val);
            $("#display_option_3").text(add_data);
            $.ajax({
                url: "/submit_quote_diseases_all_data",
                type: "POST",
                async: false,
                dataType: "json",
                data: {option_1: $("#option_1").val(), company_id: $("#company_id").val(), option_2: $("#option_2").val(), option_3: add_data, disease_specific_waiting_period: 0, disease_specific_waiting_period_second: 0, disease_specific_waiting_period_third: value},
                success: function(response) {
                    if (response == true) {
                        window.location.reload();
                    }
                }
            });
        }
    });
    $("#corporate_buffer_option_3").change(function() {
        var value = $(this).val();
//        if (value != 0) {
//            var option_3_value = $("#option_3").val();
//            var new_option3_val = (option_3_value * 25);
//            $("#option_3").val(new_option3_val);
//            $("#display_option_2").text(new_option3_val);
//        }
        if (value != '') {
            var option_3_value = $("#option_3").val();
            var new_option1_val = ($("#get_lives").val() * 25);
            var add_data = parseInt(option_3_value) + parseInt(new_option1_val);
            $("#display_option_3").text(add_data);
            $.ajax({
                url: "/submit_quote_corporate_all_data_third",
                type: "POST",
                async: false,
                dataType: "json",
                data: {option_1: $("#option_1").val(), company_id: $("#company_id").val(), option_2: $("#option_2").val(), option_3: add_data, corporate_buffer: value},
                success: function(response) {
                    if (response == true) {
                        window.location.reload();
                    }
                }
            });
        }
    });
});


