$(document).ready(function () {

    $.ajax({
        url: "/flexi_benifit/get_utilised_data",
        type: "POST",
        async: false,
        dataType: "json",
        success: function (response) {
            $(".alloted_index").attr("data-value", response.ed_flex_amount);
            $('.alloted_index').html("<i class='fa fa-inr'></i> " + response.ed_flex_amount);
        }
    });
    $.ajax({
        url: "/flexi_benifit/all_flex_data",
        type: "POST",
        dataType: "json",
        success: function (response) {
            //debugger;
            $("#core_list").html("");
            $("#non_core_list").html("");
            var flex_utilised_index = 0;
            var pay_index = 0;
            var core_benifits = [];
            var non_core_benifits = [];
            var core_premium_flex = 0;
            var core_benifits_deduction_type = "";
            var core_premium_payroll = 0;
            var core_premium_final = 0;
            var core_premium_flex_name = "";
            var core_premium_flex_id = 0;
            var non_core_premium_flex_name = "";
            var non_core_premium_flex_id = 0;
            var non_core_benifits_deduction_type = "";
            var non_core_premium_flex = 0;
            var non_core_premium_payroll = 0;
            var non_core_premium_final = 0;
            $.each(response, function (index, value) {
                
                //console.log(value);
                if (value.transac_type == 'C')
                {
                    //console.log(core_benifits);
                    if (!core_benifits.hasOwnProperty(value.master_flexi_benefit_id)) {
                        core_benifits[value.master_flexi_benefit_id] = [];
                    }
                    core_benifits[value.master_flexi_benefit_id].push(value);

                    //$("#core_list").append('<tr data-benifit="'+value.master_flexi_benefit_id+'" data-amount="'+value.final_amount+'" data-si="'+value.sum_insured+'" data-name="'+value.flexi_benefit_name+'" data-deduction="'+value.deduction_type+'"><td>'+value.flexi_benefit_name+'</td><td>'+value.flex_amount+'</td><td>'+value.pay_amount+'</td><td>'+value.final_amount+'</td></tr>');
                } else
                {
                    if (!non_core_benifits.hasOwnProperty(value.master_flexi_benefit_id)) {
                        non_core_benifits[value.master_flexi_benefit_id] = [];
                    }
                    non_core_benifits[value.master_flexi_benefit_id].push(value);

                    //$("#non_core_list").append('<tr data-benifit="'+value.master_flexi_benefit_id+'" data-amount="'+value.final_amount+'" data-si="'+value.sum_insured+'" data-name="'+value.flexi_benefit_name+'" data-deduction="'+value.deduction_type+'"><td>'+value.flexi_benefit_name+'</td><td>'+value.flex_amount+'</td><td>'+value.pay_amount+'</td><td>'+value.final_amount+'</td></tr>');
                }

                var data_flex_utilised_index = (value.flex_amount == "") ? "0" : value.flex_amount;

                flex_utilised_index += parseInt(data_flex_utilised_index.replace(/,/g, ""));
                var data_pay_index = (value.pay_amount == "") ? "0" : value.pay_amount;
                pay_index += parseInt(data_pay_index.replace(/,/g, ""));
            });

            var core_benifits = core_benifits.filter(function (item) {
                return item !== undefined;
            });

            var non_core_benifits = non_core_benifits.filter(function (item) {
                return item !== undefined;
            });

            var i = 0;


            for (i = 0; i < core_benifits.length; i++)
            {
                for (j = 0; j < core_benifits[i].length; j++)
                {

                    if (core_benifits[i][j].master_flexi_benefit_id == 2)
                    {
                        core_benifits_deduction_type = core_benifits[i][0].deduction_type;
                        core_premium_flex_name = core_benifits[i][j].flexi_benefit_name;
                        core_premium_flex_id = core_benifits[i][j].master_flexi_benefit_id;
                        core_premium_flex += parseInt(core_benifits[i][j].flex_amount);
                        core_premium_payroll += parseInt(core_benifits[i][j].pay_amount);
                        core_premium_final += parseInt(core_benifits[i][j].final_amount);
                        if (core_premium_flex != 0) {
                            $("#core_list").append('<tr data-benifit="' + core_premium_flex_id + '" data-amount="' + core_premium_final + '" data-name="' + core_premium_flex_name + '" data-deduction="' + core_benifits_deduction_type + '"><td>' + core_premium_flex_name + '</td><td>' + core_premium_flex + '</td><td>' + core_premium_payroll + '</td><td>' + core_premium_final + '</td></tr>');
                        } else {
                            $("#core_list").append('<tr data-benifit="' + core_premium_flex_id + '" data-amount="' + core_premium_final + '" data-name="' + core_premium_flex_name + '" data-deduction="' + core_benifits_deduction_type + '"><td>' + core_premium_flex_name + '</td><td>' + core_premium_payroll + '</td><td>' + core_premium_final + '</td></tr>');
                        }
                    } else {
                        if (core_benifits[i][j].flex_amount != 0) {
                            $("#core_list").append('<tr data-benifit="' + core_benifits[i][0].master_flexi_benefit_id + '" data-amount="' + core_benifits[i][j].final_amount + '" data-si="' + core_benifits[i][0].sum_insured + '" data-name="' + core_benifits[i][0].flexi_benefit_name + '" data-deduction="' + core_benifits[i][0].deduction_type + '"><td>' + core_benifits[i][0].flexi_benefit_name + '</td><td>' + core_benifits[i][j].flex_amount + '</td><td>' + core_benifits[i][j].pay_amount + '</td><td>' + core_benifits[i][j].final_amount + '</td></tr>');
                        } else {
                            $("#core_list").append('<tr data-benifit="' + core_benifits[i][0].master_flexi_benefit_id + '" data-amount="' + core_benifits[i][j].final_amount + '" data-si="' + core_benifits[i][0].sum_insured + '" data-name="' + core_benifits[i][0].flexi_benefit_name + '" data-deduction="' + core_benifits[i][0].deduction_type + '"><td>' + core_benifits[i][0].flexi_benefit_name + '</td><td>' + core_benifits[i][j].flex_amount + '</td><td>' + core_benifits[i][j].pay_amount + '</td><td>' + core_benifits[i][j].final_amount + '</td></tr>');
                        }
                    }

                }

            }

            // $("#core_list").append('<tr data-benifit="'+core_premium_flex_id+'" data-amount="'+core_premium_final+'" data-name="'+core_premium_flex_name+'" data-deduction="'+core_benifits_deduction_type+'"><td>'+core_premium_flex_name+'</td><td>'+core_premium_flex+'</td><td>'+core_premium_payroll+'</td><td>'+core_premium_final+'</td></tr>');

            for (i = 0; i < non_core_benifits.length; i++)
            {
                for (j = 0; j < non_core_benifits[i].length; j++)
                {
                    // non_core_premium_flex += parseInt(non_core_benifits[i][j].flex_amount);
                    // non_core_premium_payroll += parseInt(non_core_benifits[i][j].pay_amount);
                    // non_core_premium_final += parseInt(non_core_benifits[i][j].final_amount);
                    non_core_premium_flex = parseInt(non_core_benifits[i][j].flex_amount);
                    non_core_premium_payroll = parseInt(non_core_benifits[i][j].pay_amount);
                    non_core_premium_final = parseInt(non_core_benifits[i][j].final_amount);
                }

                $("#non_core_list").append('<tr data-benifit="' + non_core_benifits[i][0].master_flexi_benefit_id + '" data-amount="' + non_core_premium_final + '" data-si="' + non_core_benifits[i][0].sum_insured + '" data-name="' + non_core_benifits[i][0].flexi_benefit_name + '" data-deduction="' + non_core_benifits[i][0].deduction_type + '"><td>' + non_core_benifits[i][0].flexi_benefit_name + '</td><td>' + non_core_premium_flex + '</td><td>' + non_core_premium_payroll + '</td><td>' + non_core_premium_final + '</td></tr>');
            }

            var allotedAmt = $('.alloted_index').attr('data-value');

            var balance_index = parseInt(allotedAmt - flex_utilised_index);
            // allotedAmt =  parseInt(allotedAmt.replace(/,/g, ""));
            $(".balance_index").html("<i class='fa fa-inr'></i> " + balance_index);
            $(".flex_utilised_index").html("<i class='fa fa-inr'></i> " + flex_utilised_index);
            $(".salary_deduction_index").html("<i class='fa fa-inr'></i> " + pay_index);
        }
    });
    // $("#pay_hide").val($('.salary_deduction').attr('data-value'));
    var checkCount = 0;
    var frIdAlreadyExist = [];
    $(".pc_member").change(function () {

        if (checkCount > 0 && $("#add_parential").is(":visible")) {
            $("#add_parential").hide();
            $("#add_pc_members").show();
        }

    });
    $('#hc_total_premium').keyup(function (e) {
        var $th = $(this);
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('#dc_total_premium').keyup(function (e) {
        var $th = $(this);
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('#gym_total_premium').keyup(function (e) {
        var $th = $(this);
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('#ec_total_premium').keyup(function (e) {
        var $th = $(this);
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('#dnc_total_premium').keyup(function (e) {
        var $th = $(this);
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('#yz_total_premium').keyup(function (e) {
        var $th = $(this);
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('#cmp_total_premium').keyup(function (e) {
        var $th = $(this);
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('#nutri_total_premium').keyup(function (e) {
        var $th = $(this);
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('#home_health_total_premium').keyup(function (e) {
        var $th = $(this);
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });



    // $.ajax({
    // url:'/check_enrollment',
    // type: "POST",
    // async: false,
    // dataType: "json",
    // success: function (response) {
    // if (response.length == 0) 
    // {
    // $(".arrow-proceed").css('pointer-events','none');
    // $("#health_checkup").css('pointer-events','none');
    // $("#dependent_care").css('pointer-events','none');
    // $("#dental_care").css('pointer-events','none');
    // $("#yoga").css('pointer-events','none');
    // $("#elder_care").css('pointer-events','none');
    // $("#gym_care").css('pointer-events','none');
    // $("#cmp_care").css('pointer-events','none');
    var utilized = $(".flex_utilised").attr('data-value');
    // $.ajax({
    // url:'flexi_benifit/amount_moved_to_sodexo',
    // type: "POST",
    // dataType: "json",
    // success: function (data_res) {
    // console.log(data_res);
    // }
    // });
    // }
    // }
    // });
    $.ajax({
        url: '/flexi_benefit/get_emp_data_flexi',
        type: "POST",
        async: false,
        dataType: "json",
        success: function (response) {

            $.each(response, function (index, value) {

                if (value.policy_sub_type_name == 'Group Mediclaim')
                {
                    $("#gmc_id").removeAttr('style', 'display:none');
                    $("#si").removeAttr('style', 'display:none');
                    /* $("#si").text(value.policy_mem_sum_insured); */
                    $('#si').append('<div class=""> <span class="color-bul">' + value.policy_mem_sum_insured + '</span></div>');
                }

                if (value.policy_sub_type_name == 'Group Term Life')
                {
                    $("#gtli_id").removeAttr('style', 'display:none');
                    $("#si2").text(value.policy_mem_sum_insured);
                }

                if (value.policy_sub_type_name == 'Group Personal Accident')
                {
                    $("#gpa_id").removeAttr('style', 'display:none');
                    $("#si1").text(value.policy_mem_sum_insured);
                }
            });
        }
    });
    $.ajax({
        url: '/flexi_benifit/get_ghi_policy_member',
        type: "POST",
        async: false,
        dataType: "json",
        success: function (response) {
            var full_name = $("#full_name").val();
            $("#gmc_id").append('<div class=""><span class="color-bul">' + full_name + '</span></div><div class="col-md-6"><span class="color-bul"></span></div>');
            $.each(response, function (index, value) {
                $("#gmc_id").append('<div class=""><span class="color-bul">' + value.name + ' ' + value.last_name + '</span></div><div class="col-md-6"><span class="color-bul"></span></div>');

            });
        }
    });

    // suminsured and premium 
    /*$.ajax({
     url: "/employee/get_all_topuppolicy_suminsured",
     type: "POST",
     async: false,
     dataType: "json",
     success: function (response) {
     // console.log(response);
     }
     });    */
    $.ajax({
        url: "/flexi_benifit/get_utilised_data",
        type: "POST",
        async: false,
        dataType: "json",
        success: function (response) {
            $('#allotedAmt').html("<i class='fa fa-inr'></i>" + response.ed_flex_amount);
            $("#benifit_2").find(".estimate_box").html("<i class='fa fa-inr'></i> " + response.flex_parent_premium);
            $("#benifit_2").find(".estimate_box").attr("data-value", response.flex_parent_premium);
            if (response.flex_data == null)
            {
                $(".flex_utilised").html("<i class='fa fa-inr'></i>0");
                $(".flex_utilised").attr('data-value', 0);

            } else
            {
                $(".flex_utilised").html("<i class='fa fa-inr'></i>" + response.flex_data);
                $(".flex_utilised").attr('data-value', response.flex_data);

            }
            var allotedAmt = $('#allotedAmt').text();
            allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
            var utilized = $('#flex_utilised').attr('data-value');
            utilized = parseInt(utilized.replace(/,/g, ""));
            if (allotedAmt > utilized)
            {
                var data = (allotedAmt - utilized);
                $(".balance").html("<i class='fa fa-inr'></i>" + data);
                $(".balance").attr('data-value', data);
            } else {
                $(".balance").attr('data-value', 0);
            }

            if (response.salary_data == null)
            {
                $(".salary_deduction").html("<i class='fa fa-inr'></i>0");
                $(".salary_deduction").attr('data-value', 0);
            } else
            {
                $(".salary_deduction").html("<i class='fa fa-inr'></i>" + response.salary_data);
                $(".salary_deduction").attr('data-value', response.salary_data);
            }
            if (response.benifit_data !== null)
            {
                var sum_amt_gtli = 0;
                var sum_amt_gmc = 0;
                $.each(response.benifit_data, function (index, value) {

                    if (value.master_flexi_benefit_id == 3 && value.deduction_type == 'S')
                    {
                        $("#benifit_" + value.master_flexi_benefit_id).find("#ghi_estimate_box").html("<i class='fa fa-inr'></i> " + value.final_amount);
                        $("#benifit_" + value.master_flexi_benefit_id).find("#ghi_estimate_box").attr("data-value", value.final_amount);
                        $("#benifit_" + value.master_flexi_benefit_id).find("#ghi_estimate_box").attr("data-type", value.deduction_type);
                    }

                    if (value.master_flexi_benefit_id == 4 && value.deduction_type == 'S')
                    {
                        $("#benifit_" + value.master_flexi_benefit_id).find("#gpa_estimate_box").html("<i class='fa fa-inr'></i> " + value.final_amount);
                        $("#benifit_" + value.master_flexi_benefit_id).find("#gpa_estimate_box").attr("data-value", value.final_amount);
                        $("#benifit_" + value.master_flexi_benefit_id).find("#gpa_estimate_box").attr("data-type", value.deduction_type);
                    }

                    if (value.master_flexi_benefit_id == 6 && value.deduction_type == 'S')
                    {
                        $("#benifit_" + value.master_flexi_benefit_id).find("#hc_estimate_box").html("<i class='fa fa-inr'></i> " + value.final_amount);
                        $("#benifit_" + value.master_flexi_benefit_id).find("#hc_estimate_box").attr("data-value", value.final_amount);
                        $("#benifit_" + value.master_flexi_benefit_id).find("#hc_estimate_box").attr("data-type", value.deduction_type);
                    }

                    if (value.master_flexi_benefit_id == 5 && value.deduction_type == 'S')
                    {
                        $("#benifit_" + value.master_flexi_benefit_id).find("#gtli_estimate_box").html("<i class='fa fa-inr'></i> " + value.final_amount);
                        $("#benifit_" + value.master_flexi_benefit_id).find("#gtli_estimate_box").attr("data-value", value.final_amount);
                        $("#benifit_" + value.master_flexi_benefit_id).find("#gtli_estimate_box").attr("data-type", value.deduction_type);
                    }

                    if (value.master_flexi_benefit_id != 2) {
                        $("#benifit_" + value.master_flexi_benefit_id).find(".estimate_box").html("<i class='fa fa-inr'></i> " + value.final_amount);
                        $("#benifit_" + value.master_flexi_benefit_id).find(".estimate_box").attr("data-value", value.final_amount);
                    }
                    $("#benifit_" + value.master_flexi_benefit_id).find(".estimate_box").attr("data-type", value.deduction_type);
                    $("#benifit_" + value.master_flexi_benefit_id).find(".tick-1").removeClass('hidden');
                    $("#benifit_" + value.master_flexi_benefit_id).find(".payment_type[value='" + value.deduction_type + "']").click();
                    // $("#benifit_"+value.master_flexi_benefit_id).find(".sum_insured_check[value='"+value.sum_insured+"']").click();
                    $("#benifit_" + value.master_flexi_benefit_id).find(".sum_insured_check").html("<i class='fa fa-inr' style='font-size: 14px;'> " + value.sum_insured + "</i>");
                    if (value.deduction_type == 'F')
                    {
                        $("#benifit_" + value.master_flexi_benefit_id).find(".payment_type").html("Wallet");
                    } else
                    {
                        $("#benifit_" + value.master_flexi_benefit_id).find(".payment_type").html("Payroll");
                    }

                    $("#benifit_" + value.master_flexi_benefit_id).find(".blocked_amount").val(value.block_amount);
                    if (value.master_flexi_benefit_id == '1')
                    {
                        $("#benifit_" + value.master_flexi_benefit_id).find("input[value='" + (value.final_amount / 12) + "']").click();
                    }

                    if (value.master_flexi_benefit_id == 2) {
                        $('#type_payment').val(value.deduction_type);
                    }
                    if (value.flexi_type == 'C')
                    {
                        if (value.deduction_type == 'F')
                        {
                            $("#voluntary_list").append('<tr><td>' + value.flexi_benefit_name + ' </td><td><span class="bor-dashed" id="sudexo_estimate_summary"> <i class="fa fa-inr"></i>' + value.final_amount + '</span></td><td>Wallet</td></tr>');
                        } else
                        {
                            $("#voluntary_list").append('<tr><td>' + value.flexi_benefit_name + ' </td><td><span class="bor-dashed" id="sudexo_estimate_summary"> <i class="fa fa-inr"></i>' + value.final_amount + '</span></td><td>Payroll</td></tr>');
                        }
                    } else
                    {
                        $("#wellness_list").append('<tr><td>' + value.flexi_benefit_name + ' </td><td><span class="bor-dashed" id="sudexo_estimate_summary"> <i class="fa fa-inr"></i>' + value.final_amount + '</span></td><td>Wallet</td></tr>');
                    }

                    if (value.master_flexi_benefit_id == 5)
                    {
                        var final_gtli = value.final_amount;
                        sum_amt_gtli += parseInt(final_gtli.replace(/,/g, ""));
                        $("#benifit_5").find(".estimate_box").html("<i class='fa fa-inr'></i> " + sum_amt_gtli);
                        $("#benifit_5").find(".estimate_box").attr("data-value", sum_amt_gtli);
                    }
                    if (value.master_flexi_benefit_id == 3)
                    {
                        var final_gmc = value.final_amount;
                        sum_amt_gmc += parseInt(final_gmc.replace(/,/g, ""));
                        $("#benifit_3").find(".estimate_box").html("<i class='fa fa-inr'></i> " + sum_amt_gmc);
                        $("#benifit_3").find(".estimate_box").attr("data-value", sum_amt_gmc);
                    }


                });
            }
        }
    });

    $.ajax({
        url: '/flexi_benifit/all_confirmed_flex_page_data',
        type: "POST",
        async: false,
        dataType: "json",
        success: function (response) {
            if (response != null)
            {
                if (response.benifit_all_data != '')
                {
                    $("#allocate_sodexo").attr('style', 'display:none');
                    $("#resetSudexo").attr('style', 'display:none');
                    $("#allocate_parent").attr('style', 'display:none');
                    $("#resetparent").attr('style', 'display:none');
                    $("#ghi_modal_btn").attr('style', 'display:none');
                    $("#resetghi").attr('style', 'display:none');
                    $("#allocate_gpa").attr('style', 'display:none');
                    $("#reset_gpa").attr('style', 'display:none');
                    $("#allocate_gtli").attr('style', 'display:none');
                    $("#reset_gtli").attr('style', 'display:none');
                    $("#hc_modal_btn").attr('style', 'display:none');
                    $("#dc_modal_btn").attr('style', 'display:none');
                    $("#gym_modal_btn").attr('style', 'display:none');
                    $("#ec_modal_btn").attr('style', 'display:none');
                    $("#dnc_modal_btn").attr('style', 'display:none');
                    $("#yz_modal_btn").attr('style', 'display:none');
                    $("#cmp_modal_btn").attr('style', 'display:none');
                    $("#nutri_modal_btn").attr('style', 'display:none');
                    $("#home_health_modal_btn").attr('style', 'display:none');
                    $("#submit_flex_data").attr('style', 'display:none');
                    $("#customCheck51").attr('disabled');
                }
            }

        }
    })

    $.ajax({
        url: "/flexi_benefit/get_all_active_flexi_from_master",
        type: "POST",
        async: false,
        dataType: "json",
        success: function (response) {
            $.each(response, function (i, v) {
                $("#benifit_" + v.master_flexi_benefit_id + "").removeAttr('style', 'display:none');
            })
        }
    });

    $('#resetSudexo').click(function () {
        $("#sudexo_estimate").attr('data-value', 0);
        $("#sudexo_estimate").html("<i class='fa fa-inr'></i> " + 0);
        var current_val = $("#sudexo_estimate").attr('data-value');
        var selected_val = $("input[name='sudexo_radio']:checked").val();
        var anual = selected_val * 12;
        var data_benefit = $(this).attr('data-value');
        $.ajax({
            type: 'POST',
            url: '/flexi_benifit/reset_flexi_data',
            data: {benefit: data_benefit},
            success: function (response) {
                var data_res = JSON.parse(response);
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + data_res.flex_amount);
                $(".flex_utilised").attr("data-value", data_res.flex_amount);
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + data_res.pay_amount);
                $(".salary_deduction").attr("data-value", data_res.pay_amount);
                var allotedAmt = $('#allotedAmt').text();

                allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                var utilized = $('.flex_utilised').attr('data-value');
                utilized = parseInt(utilized.replace(/,/g, ""));
                var data_balance = (allotedAmt - utilized);
                $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                $(".balance").attr('data-value', data_balance);
                swal("", "Successfully submitted");
                setTimeout("location.reload(true);", 1000);
            }
        })
    });
    $('#resetparent').click(function () {
        $("#parental_estimate").attr('data-value', 0);
        $("#parental_estimate").html("<i class='fa fa-inr'></i> " + 0);
        //       var flex_utilised =  parseInt(($(".flex_utilised").attr('data-value')) - parseInt($("#parent_data").val()));
        // $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
        //       $(".flex_utilised").attr("data-value",flex_utilised);
        var data_benefit = $(this).attr('data-value');
        $.ajax({
            type: 'POST',
            url: '/flexi_benifit/reset_flexi_data',
            data: {benefit: data_benefit},
            success: function (response) {
                var data_res = JSON.parse(response);
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + data_res.flex_amount);
                $(".flex_utilised").attr("data-value", data_res.flex_amount);
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + data_res.pay_amount);
                $(".salary_deduction").attr("data-value", data_res.pay_amount);
                var allotedAmt = $('#allotedAmt').text();

                allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                var utilized = $('.flex_utilised').attr('data-value');
                utilized = parseInt(utilized.replace(/,/g, ""));
                var data_balance = (allotedAmt - utilized);
                $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                $(".balance").attr('data-value', data_balance);
            }
        })
    });
    $('#resetghi').click(function () {
        $("#ghi_estimate").attr('data-value', 0);
        $("#ghi_estimate").html("<i class='fa fa-inr'></i> " + 0);
        //       var flex_utilised =  parseInt(($(".flex_utilised").attr('data-value')) - parseInt($("#ghi_total_premium").text()));
        // $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
        //      $(".flex_utilised").attr("data-value",flex_utilised);
        var data_benefit = $(this).attr('data-value');
        $.ajax({
            type: 'POST',
            url: '/flexi_benifit/reset_flexi_data',
            data: {benefit: data_benefit},
            success: function (response) {
                var data_res = JSON.parse(response);
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + data_res.flex_amount);
                $(".flex_utilised").attr("data-value", data_res.flex_amount);
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + data_res.pay_amount);
                $(".salary_deduction").attr("data-value", data_res.pay_amount);
                var allotedAmt = $('#allotedAmt').text();

                allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                var utilized = $('.flex_utilised').attr('data-value');
                utilized = parseInt(utilized.replace(/,/g, ""));
                var data_balance = (allotedAmt - utilized);
                $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                $(".balance").attr('data-value', data_balance);
            }
        })
    });
    $('#resetgpa').click(function () {
        $("#gpa_estimate").attr('data-value', 0);
        $("#gpa_estimate").html("<i class='fa fa-inr'></i> " + 0);
        //       var flex_utilised =  parseInt(($(".flex_utilised").attr('data-value')) - parseInt($("#gpa_total_premium").text()));
        // $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
        //      $(".flex_utilised").attr("data-value",flex_utilised);
        var data_benefit = $(this).attr('data-value');
        $.ajax({
            type: 'POST',
            url: '/flexi_benifit/reset_flexi_data',
            data: {benefit: data_benefit},
            success: function (response) {
                var data_res = JSON.parse(response);
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + data_res.flex_amount);
                $(".flex_utilised").attr("data-value", data_res.flex_amount);
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + data_res.pay_amount);
                $(".salary_deduction").attr("data-value", data_res.pay_amount);
                var allotedAmt = $('#allotedAmt').text();

                allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                var utilized = $('.flex_utilised').attr('data-value');
                utilized = parseInt(utilized.replace(/,/g, ""));
                var data_balance = (allotedAmt - utilized);
                $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                $(".balance").attr('data-value', data_balance);
            }
        })
    });
    $('#resetgtli').click(function () {
        $("#glta_estimate").attr('data-value', 0);
        $("#glta_estimate").html("<i class='fa fa-inr'></i> " + 0);
        //       var flex_utilised =  parseInt(($(".flex_utilised").attr('data-value')) - parseInt($("#gtli_total_premium").text()));
        // $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
        //      $(".flex_utilised").attr("data-value",flex_utilised);
        var data_benefit = $(this).attr('data-value');
        $.ajax({
            type: 'POST',
            url: '/flexi_benifit/reset_flexi_data',
            data: {benefit: data_benefit},
            success: function (response) {
                var data_res = JSON.parse(response);
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + data_res.flex_amount);
                $(".flex_utilised").attr("data-value", data_res.flex_amount);
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + data_res.pay_amount);
                $(".salary_deduction").attr("data-value", data_res.pay_amount);
                var allotedAmt = $('#allotedAmt').text();

                allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                var utilized = $('.flex_utilised').attr('data-value');
                utilized = parseInt(utilized.replace(/,/g, ""));
                var data_balance = (allotedAmt - utilized);
                $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                $(".balance").attr('data-value', data_balance);
            }
        })
    });

    $("#submitSudexo").click(function () {
        var selected_val = $("input[name='sudexo_radio']:checked").val();
        var anual = selected_val * 12;
        var current_val = $("#sudexo_estimate").attr('data-value');
        var flex_utilised = parseInt($(".flex_utilised").attr('data-value')) + parseInt(anual) - parseInt((current_val == '') ? 0 : current_val);
        if (flex_utilised > $(".flex_alloted").attr('data-value'))
        {
            $("#getCodeModal").modal("toggle");
            $("#getCode").html('Flex balance is not enough');
            return false;
        }
        $(".flex_utilised").html("<i class='fa fa-inr'></i> " + flex_utilised);
        $(".flex_utilised").attr("data-value", flex_utilised);
        $("#sudexo_estimate").html("<i class='fa fa-inr'></i> " + anual);
        $("#sudexo_estimate").attr("data-value", anual);
        var benifit_type = 1;
        var name = 'Sodexo';
        var deduction_type = 'F';
        var transac_type = 'C';
        $.ajax({
            url: "/flexi_benifit/save_all_data",
            type: "POST",
            data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#sudexo_estimate").attr("data-value"), name: name, deduction_type: deduction_type, flex_amount: $("#sudexo_estimate").attr("data-value")},
            success: function (response) {
                var data_res = JSON.parse(response);
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + data_res.flex_amount);
                $(".flex_utilised").attr("data-value", data_res.flex_amount);
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + data_res.pay_amount);
                $(".salary_deduction").attr("data-value", data_res.pay_amount);
                var allotedAmt = $('#allotedAmt').text();

                allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                var utilized = $('.flex_utilised').attr('data-value');
                utilized = parseInt(utilized.replace(/,/g, ""));
                var data_balance = (allotedAmt - utilized);
                $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                $(".balance").attr('data-value', data_balance);
                swal("", "Successfully submitted");
                setTimeout("location.reload(true);", 1000);

            }
        });
    });

    $('input[type=radio][name=ghi_si]').change(function () {
        var ghi_data = $("input[name='ghi_si']:checked").val();

        if (ghi_data == 300000) {
            $("#ghi_total_premium").html('10000');
        }

        if (ghi_data == 500000) {
            $("#ghi_total_premium").html('14000');
        }

        if (ghi_data == 1000000) {
            $("#ghi_total_premium").html('18000');
        }
        if (ghi_data == 1500000)
        {
            $("#ghi_total_premium").html('20000');
        }
        // $("#ghi_total_premium").html($("input[name='ghi_si']:checked").val()*600);
    });

    $(".data_final").click(function () {
        var btn_val = $(this).val();
        if (btn_val == 'yes')
        {
            var data_amt = parseInt($("#ghi_estimate").attr('data-value').replace(/,/g, ""));
            var pay_minus_amt = (data_amt - parseInt($(".balance").attr('data-value').replace(/,/g, "")));
            var wallet_bal_gmc = parseInt($(".balance").attr('data-value'));
            var benifit_type = 3;
            var name = 'Mediclaim Top-Up';
            var sum_insured = $("input[name='ghi_si']:checked").val();
            var deduction_type = 'F';
            var transac_type = 'C';
            $.ajax({
                url: "/flexi_benifit/save_all_data",
                type: "POST",
                data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#ghi_estimate").attr("data-value"), name: name, deduction_type: deduction_type, sum_insured: sum_insured, flex_amount: wallet_bal_gmc, pay_amount: pay_minus_amt},
                dataType: "json",
                success: function (response) {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                    $(".flex_utilised").attr("data-value", response.flex_amount);
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                    $(".salary_deduction").attr("data-value", response.pay_amount);
                    var allotedAmt = $('#allotedAmt').text();

                    allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                    var utilized = $('.flex_utilised').attr('data-value');
                    utilized = parseInt(utilized.replace(/,/g, ""));
                    var data_balance = (allotedAmt - utilized);
                    $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                    $(".balance").attr('data-value', data_balance);
                    // $("#ghi_estimate").attr("data-value",parseInt($(".balance").attr('data-value')));
                }
            });
        } else
        {
            window.location.reload();
        }
    });
    $("#submitGHI").click(function () {
        $('input[type=radio][name=ghi_si]').change();
        var deduction_type = $("input[name='ghi_type']:checked").val();
        var current_val = $("#ghi_estimate").attr('data-value');
        var balance = parseInt($(".balance").attr('data-value'));
        if (deduction_type == 'F')
        {
            if ($("#ghi_estimate").attr("data-type") == 'S')
            {
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#ghi_estimate").text())));
                $(".salary_deduction").attr("data-value", (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#ghi_estimate").text())));
                $("#ghi_estimate").attr('data-value', '0');
            }
            var flex_utilised = parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#ghi_total_premium").text()) - parseInt((($("#ghi_estimate").attr('data-value') == '') ? 0 : $("#ghi_estimate").attr('data-value')));
            if ((balance < ($("#ghi_total_premium").text())) && balance != 0)
            {

                $('#wallet_gmc').html("<i class='fa fa-inr'></i> " + balance);
                var cut_amt = (parseInt($("#ghi_total_premium").text()) - balance);
                $('#pay_gmc').html("<i class='fa fa-inr'></i> " + cut_amt);
                $("#getCodeModal1").modal("toggle");
            } else if (balance == 0)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Flex balance is not enough');
                return false;
            } else
            {
                var benifit_type = 3;
                var name = 'Mediclaim Top-Up';
                var sum_insured = $("input[name='ghi_si']:checked").val();
                var transac_type = 'C';
                $.ajax({
                    url: "/flexi_benifit/save_all_data",
                    type: "POST",
                    data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#ghi_total_premium").text(), name: name, deduction_type: deduction_type, sum_insured: sum_insured, flex_amount: $("#ghi_total_premium").text()},
                    dataType: "json",
                    success: function (response) {
                        $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                        $(".flex_utilised").attr("data-value", response.flex_amount);
                        $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                        $(".salary_deduction").attr("data-value", response.pay_amount);
                        var allotedAmt = $('#allotedAmt').text();

                        allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                        var utilized = $('.flex_utilised').attr('data-value');
                        utilized = parseInt(utilized.replace(/,/g, ""));
                        var data_balance = (allotedAmt - utilized);
                        $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                        $(".balance").attr('data-value', data_balance);
                    }
                });
            }
            $(".flex_utilised").html("<i class='fa fa-inr'></i> " + flex_utilised);
            $(".flex_utilised").attr("data-value", flex_utilised);
            $("#ghi_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#ghi_total_premium").text()));
            $("#ghi_estimate").attr("data-value", parseInt($("#ghi_total_premium").text()));
            $("#ghi_estimate").attr("data-type", 'F');
        } else
        {
            if ($("#ghi_estimate").attr("data-type") == 'F')
            {
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#ghi_estimate").text())));
                $(".flex_utilised").attr("data-value", (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#ghi_estimate").attr('data-value'))));
                $("#ghi_estimate").attr('data-value', '0');
            }
            var salary_deduction = parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#ghi_total_premium").text()) - parseInt(($("#ghi_estimate").attr('data-value') == '') ? 0 : $("#ghi_estimate").attr('data-value'));
            if (salary_deduction > $("#total_salary").val())
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Please contact HR');
                return false;
            }
            $(".salary_deduction").html("<i class='fa fa-inr'></i> " + salary_deduction);
            $(".salary_deduction").attr("data-value", salary_deduction);
            $("#ghi_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#ghi_total_premium").text()));
            $("#ghi_estimate").attr("data-value", parseInt($("#ghi_total_premium").text()));
            $("#ghi_estimate").attr("data-type", 'S');
            var benifit_type = 3;
            var name = 'Mediclaim Top-Up';
            var sum_insured = $("input[name='ghi_si']:checked").val();
            var transac_type = 'C';
            $.ajax({
                url: "/flexi_benifit/save_all_data",
                type: "POST",
                data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#ghi_estimate").attr("data-value"), name: name, deduction_type: deduction_type, sum_insured: sum_insured, pay_amount: $("#ghi_estimate").attr("data-value")},
                dataType: "json",
                success: function (response) {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                    $(".flex_utilised").attr("data-value", response.flex_amount);
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                    $(".salary_deduction").attr("data-value", response.pay_amount);
                    var allotedAmt = $('#allotedAmt').text();

                    allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                    var utilized = $('.flex_utilised').attr('data-value');
                    utilized = parseInt(utilized.replace(/,/g, ""));
                    var data_balance = (allotedAmt - utilized);
                    $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                    $(".balance").attr('data-value', data_balance);
                }
            });
        }
    });
    // parental
    $("#add_parential").click(function () {
        $("#hid_data").val($(this).attr('data-value'));
        var deduction_type = $("input[name='pc_type']:checked").val();
        var current_val = $("#parental_estimate").attr('data-value');
        $("#parental_total_premium").val(current_val);
        var balance = parseInt($(".balance").attr('data-value'));
        if (deduction_type == 'F')
        {
            if ($("#parental_estimate").attr("data-type") == 'S')
            {
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#parental_estimate").text())));
                $(".salary_deduction").attr("data-value", (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#parental_estimate").text())));
                $("#parental_estimate").attr('data-value', '0');
            }
            var flex_utilised = parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#parental_total_premium").val()) - parseInt((($("#parental_estimate").attr('data-value') == '') ? 0 : $("#parental_estimate").attr('data-value')));
            if (flex_utilised > $(".flex_alloted").attr('data-value') && balance != 0)
            {
                $("#getCodeModal5").modal("toggle");
            } else if (balance == 0)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Flex balance is not enough');
                return false;
            }
            $(".flex_utilised").html("<i class='fa fa-inr'></i> " + flex_utilised);
            $(".flex_utilised").attr("data-value", flex_utilised);
            $("#parental_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#parental_total_premium").val()));
            $("#parental_estimate").attr("data-value", parseInt($("#parental_total_premium").val()));
            $("#parental_estimate").attr("data-type", 'F');
        } else
        {
            if ($("#parental_estimate").attr("data-type") == 'F')
            {
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#parental_estimate").text())));
                $(".flex_utilised").attr("data-value", (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#parental_estimate").attr('data-value'))));
                $("#parental_estimate").attr('data-value', '0');
            }
            var salary_deduction = parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#parental_total_premium").val()) - parseInt(($("#parental_estimate").attr('data-value') == '') ? 0 : $("#parental_estimate").attr('data-value'));
            if (salary_deduction > $("#total_salary").val())
            {
                alert('Please contact HR');
                return false;
            }
            $(".salary_deduction").html("<i class='fa fa-inr'></i> " + salary_deduction);
            $(".salary_deduction").attr("data-value", salary_deduction);
            $("#parental_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#parental_total_premium").val()));
            $("#parental_estimate").attr("data-value", parseInt($("#parental_total_premium").val()));
            $("#parental_estimate").attr("data-type", 'S');
        }
    });
    // gpa
    $('input[type=radio][name=gpa_si]').change(function () {
        if ($('input[type=radio][name=gpa_si]:checked').val() == 2500000)
        {
            $("#gpa_total_premium").html(1000);
        } else
        {
            $("#gpa_total_premium").html(2000);
        }
        // $("#gpa_total_premium").html($("input[name='gpa_si']:checked").val()*40);
    });

    $(".data_final_gpa").click(function () {
        var btn_val = $(this).val();
        if (btn_val == 'yes')
        {
            var data_amt_gpa = parseInt($("#gpa_estimate").attr('data-value').replace(/,/g, ""));
            var minus_amt_gpa = (data_amt_gpa - parseInt($(".balance").attr('data-value').replace(/,/g, "")));
            var wallet_bal_gpa = parseInt($(".balance").attr('data-value'));
            var benifit_type = 4;
            var name = 'Personal Accident Top-Up';
            var sum_insured = $("input[name='gpa_si']:checked").val();
            var deduction_type = 'F';
            var transac_type = 'C';
            $.ajax({
                url: "/flexi_benifit/save_all_data",
                type: "POST",
                data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#gpa_estimate").attr('data-value'), name: name, deduction_type: deduction_type, sum_insured: sum_insured, flex_amount: wallet_bal_gpa, pay_amount: minus_amt_gpa},
                dataType: "json",
                success: function (response) {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                    $(".flex_utilised").attr("data-value", response.flex_amount);
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                    $(".salary_deduction").attr("data-value", response.pay_amount);
                    var allotedAmt = $('#allotedAmt').text();

                    allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                    var utilized = $('.flex_utilised').attr('data-value');
                    utilized = parseInt(utilized.replace(/,/g, ""));
                    var data_balance = (allotedAmt - utilized);
                    $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                    $(".balance").attr('data-value', data_balance);
                }
            });
        } else
        {
            window.location.reload();
        }

    });

    $("#submitGPA").click(function () {
        var deduction_type = $("input[name='gpa_type']:checked").val();
        $('input[type=radio][name=gpa_si]').change();
        var current_val = $("#gpa_estimate").attr('data-value');
        var balance = parseInt($(".balance").attr('data-value'));
        if (deduction_type == 'F')
        {
            if ($("#gpa_estimate").attr("data-type") == 'S')
            {
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#gpa_estimate").attr('data-value'))));
                $(".salary_deduction").attr("data-value", (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#gpa_estimate").attr('data-value'))));
                $("#gpa_estimate").attr('data-value', '0');
            }
            var flex_utilised = parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#gpa_total_premium").text()) - parseInt(($("#gpa_estimate").attr('data-value') == '') ? 0 : $("#gpa_estimate").attr('data-value'));
            if ((balance < ($("#gpa_total_premium").text())) && balance != 0)
            {
                $('#wallet_gpa').html("<i class='fa fa-inr'></i> " + balance);
                var cut_amt = (parseInt($("#gpa_total_premium").text()) - balance);
                $('#pay_gpa').html("<i class='fa fa-inr'></i> " + cut_amt);
                $("#getCodeModal2").modal("toggle");
            } else if (balance == 0)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Flex balance is not enough');
                return false;
            } else
            {
                var benifit_type = 4;
                var name = 'Personal Accident Top-Up';
                var sum_insured = $("input[name='gpa_si']:checked").val();
                var transac_type = 'C';
                $.ajax({
                    url: "/flexi_benifit/save_all_data",
                    type: "POST",
                    data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#gpa_total_premium").text(), name: name, deduction_type: deduction_type, sum_insured: sum_insured, flex_amount: $("#gpa_total_premium").text()},
                    dataType: "json",
                    success: function (response) {
                        $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                        $(".flex_utilised").attr("data-value", response.flex_amount);
                        $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                        $(".salary_deduction").attr("data-value", response.pay_amount);
                        var allotedAmt = $('#allotedAmt').text();

                        allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                        var utilized = $('.flex_utilised').attr('data-value');
                        utilized = parseInt(utilized.replace(/,/g, ""));
                        var data_balance = (allotedAmt - utilized);
                        $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                        $(".balance").attr('data-value', data_balance);
                    }
                });
            }
            $(".flex_utilised").html("<i class='fa fa-inr'></i> " + flex_utilised);
            $(".flex_utilised").attr("data-value", flex_utilised);
            $("#gpa_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#gpa_total_premium").text()));
            $("#gpa_estimate").attr("data-value", parseInt($("#gpa_total_premium").text()));
            $("#gpa_estimate").attr("data-type", 'F');
        } else
        {
            if ($("#gpa_estimate").attr("data-type") == 'F')
            {
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#gpa_estimate").attr('data-value'))));
                $(".flex_utilised").attr("data-value", (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#gpa_estimate").attr('data-value'))));
                $("#gpa_estimate").attr('data-value', '0');
            }
            var salary_deduction = parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#gpa_total_premium").text()) - parseInt(($("#gpa_estimate").attr('data-value') == '') ? 0 : $("#gpa_estimate").attr('data-value'));
            if (salary_deduction > $("#total_salary").val())
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Please contact HR');
                return false;
            }
            $(".salary_deduction").html("<i class='fa fa-inr'></i> " + salary_deduction);
            $(".salary_deduction").attr("data-value", salary_deduction);
            $("#gpa_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#gpa_total_premium").text()));
            $("#gpa_estimate").attr("data-value", parseInt($("#gpa_total_premium").text()));
            $("#gpa_estimate").attr("data-type", 'S');
            var benifit_type = 4;
            var name = 'Personal Accident Top-Up';
            var sum_insured = $("input[name='gpa_si']:checked").val();
            var transac_type = 'C';
            $.ajax({
                url: "/flexi_benifit/save_all_data",
                type: "POST",
                data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#gpa_estimate").attr("data-value"), name: name, deduction_type: deduction_type, sum_insured: sum_insured, pay_amount: $("#gpa_estimate").attr("data-value")},
                dataType: "json",
                success: function (response) {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                    $(".flex_utilised").attr("data-value", response.flex_amount);
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                    $(".salary_deduction").attr("data-value", response.pay_amount);
                    var allotedAmt = $('#allotedAmt').text();

                    allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                    var utilized = $('.flex_utilised').attr('data-value');
                    utilized = parseInt(utilized.replace(/,/g, ""));
                    var data_balance = (allotedAmt - utilized);
                    $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                    $(".balance").attr('data-value', data_balance);
                }
            });
        }

    });
    //glta
    $(".data_final_gtli").click(function () {
        var btn_val = $(this).val();
        if (btn_val == 'yes')
        {
            var data_amt = parseInt($("#glta_estimate").attr('data-value').replace(/,/g, ""));
            var minus_amt = (data_amt - parseInt($(".balance").attr('data-value').replace(/,/g, "")));
            var wallet_bal_gtli = parseInt($(".balance").attr('data-value'));
            var benifit_type = 5;
            var name = 'Voluntary Term Life';
            var sum_insured = $("input[name='glta_si']:checked").val();
            var deduction_type = 'F';
            var transac_type = 'C';
            $.ajax({
                url: "/flexi_benifit/save_all_data",
                type: "POST",
                data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#glta_total_premium").text(), name: name, deduction_type: deduction_type, sum_insured: sum_insured, flex_amount: wallet_bal_gtli, pay_amount: minus_amt},
                dataType: "json",
                success: function (response) {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                    $(".flex_utilised").attr("data-value", response.flex_amount);
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                    $(".salary_deduction").attr("data-value", response.pay_amount);
                    var allotedAmt = $('#allotedAmt').text();

                    allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                    var utilized = $('.flex_utilised').attr('data-value');
                    utilized = parseInt(utilized.replace(/,/g, ""));
                    var data_balance = (allotedAmt - utilized);
                    $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                    $(".balance").attr('data-value', data_balance);
                }
            });
        } else
        {
            window.location.reload();
        }

    });
    $("#submitGLTA").click(function () {
        getPremiumCalc();

        var deduction_type = $("input[name='glta_type']:checked").val();
        // var current_val = $("#glta_estimate").attr('data-value');
        // $("#glta_total_premium").text(current_val);
        var balance = parseInt($(".balance").attr('data-value'));
        if (deduction_type == 'F')
        {
            if ($("#glta_estimate").attr("data-type") == 'S')
            {
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#glta_estimate").attr('data-value'))));
                $(".salary_deduction").attr("data-value", (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#glta_estimate").attr('data-value'))));
                $("#glta_estimate").attr('data-value', '0');
            }

            var flex_utilised = parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#glta_total_premium").text()) - parseInt(($("#glta_estimate").attr('data-value') == '') ? 0 : $("#glta_estimate").attr('data-value'));

            if ((balance < ($("#glta_total_premium").text())) && balance != 0)
            {
                $('#wallet_gtli').html("<i class='fa fa-inr'></i> " + balance);
                var cut_amt = (parseInt($("#glta_total_premium").text()) - balance);
                $('#pay_gtli').html("<i class='fa fa-inr'></i> " + cut_amt);
                $("#getCodeModal4").modal("toggle");
            } else if (balance == 0)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Flex balance is not enough');
                return false;
            } else
            {
                var benifit_type = 5;
                var name = 'Voluntary Term Life';
                var sum_insured = $("input[name='glta_si']:checked").val();
                var transac_type = 'C';
                $.ajax({
                    url: "/flexi_benifit/save_all_data",
                    type: "POST",
                    data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#glta_total_premium").text(), name: name, deduction_type: deduction_type, sum_insured: sum_insured, flex_amount: $("#glta_total_premium").text()},
                    dataType: "json",
                    success: function (response) {
                        $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                        $(".flex_utilised").attr("data-value", response.flex_amount);
                        $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                        $(".salary_deduction").attr("data-value", response.pay_amount);
                        var allotedAmt = $('#allotedAmt').text();

                        allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                        var utilized = $('.flex_utilised').attr('data-value');
                        utilized = parseInt(utilized.replace(/,/g, ""));
                        var data_balance = (allotedAmt - utilized);
                        $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                        $(".balance").attr('data-value', data_balance);
                    }
                });
            }
            $(".flex_utilised").html("<i class='fa fa-inr'></i> " + flex_utilised);
            $(".flex_utilised").attr("data-value", flex_utilised);
            $("#glta_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#glta_total_premium").text()));
            $("#glta_estimate").attr("data-value", parseInt($("#glta_total_premium").text()));
            $("#glta_estimate").attr("data-type", 'F');
        } else
        {
            if ($("#glta_estimate").attr("data-type") == 'F')
            {
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#glta_estimate").attr('data-value'))));
                $(".flex_utilised").attr("data-value", (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#glta_estimate").attr('data-value'))));
                $("#glta_estimate").attr('data-value', '0');
            }
            var salary_deduction = parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#glta_total_premium").text()) - parseInt(($("#glta_estimate").attr('data-value') == '') ? 0 : $("#glta_estimate").attr('data-value'));
            if (salary_deduction > $("#total_salary").val())
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Please contact HR');
                return false;
            }
            $(".salary_deduction").html("<i class='fa fa-inr'></i> " + salary_deduction);
            $(".salary_deduction").attr("data-value", salary_deduction);
            $("#glta_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#glta_total_premium").text()));
            $("#glta_estimate").attr("data-value", parseInt($("#glta_total_premium").text()));
            $("#glta_estimate").attr("data-type", 'S');
            var benifit_type = 5;
            var name = 'Voluntary Term Life';
            var sum_insured = $("input[name='glta_si']:checked").val();
            var transac_type = 'C';
            $.ajax({
                url: "/flexi_benifit/save_all_data",
                type: "POST",
                data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#glta_estimate").attr("data-value"), name: name, deduction_type: deduction_type, sum_insured: sum_insured, pay_amount: $("#glta_estimate").attr("data-value")},
                dataType: "json",
                success: function (response) {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                    $(".flex_utilised").attr("data-value", response.flex_amount);
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                    $(".salary_deduction").attr("data-value", response.pay_amount);
                    var allotedAmt = $('#allotedAmt').text();

                    allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                    var utilized = $('.flex_utilised').attr('data-value');
                    utilized = parseInt(utilized.replace(/,/g, ""));
                    var data_balance = (allotedAmt - utilized);
                    $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                    $(".balance").attr('data-value', data_balance);
                }
            });
        }
    });
    //health checkup
    $("#hc_modal_btn").click(function () {
        $("#hc_total_premium").val($("#hc_estimate").attr("data-value"));
    });

    $(".data_final_health").click(function () {
        var btn_val = $(this).val();
        if (btn_val == 'yes')
        {
            var data_amt = parseInt($("#hc_estimate").attr('data-value').replace(/,/g, ""));
            var pay_minus_amt = (data_amt - parseInt($(".balance").attr('data-value').replace(/,/g, "")));
            var wallet_bal_health = parseInt($(".balance").attr('data-value'));
            var benifit_type = 6;
            var name = 'Health Check Up';
            var deduction_type = 'F,S';
            var transac_type = 'C';
            $.ajax({
                url: "/flexi_benifit/save_all_data",
                type: "POST",
                data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#hc_estimate").attr("data-value"), name: name, deduction_type: deduction_type, flex_amount: wallet_bal_health, pay_amount: pay_minus_amt},
                dataType: "json",
                success: function (response) {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                    $(".flex_utilised").attr("data-value", response.flex_amount);
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                    $(".salary_deduction").attr("data-value", response.pay_amount);
                    var allotedAmt = $('#allotedAmt').text();

                    allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                    var utilized = $('.flex_utilised').attr('data-value');
                    utilized = parseInt(utilized.replace(/,/g, ""));
                    var data_balance = (allotedAmt - utilized);
                    $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                    $(".balance").attr('data-value', data_balance);
                    swal("", "Successfully submitted");
                    setTimeout("location.reload(true);", 1000);
                    // $("#ghi_estimate").attr("data-value",parseInt($(".balance").attr('data-value')));
                }
            });
        } else
        {
            window.location.reload();
        }

    });
    $("#hc_total_premium").on('blur', function () {
        $("#hc_temp").val($(this).val());
    });

    $("#submitHC").click(function () {
        var deduction_type = $("input[name='hc_type']:checked").val();
        var current_val = $("#hc_estimate").attr('data-value');
        var block_amount = $("#blocked_amount_hc").val();
        block_amount = parseInt(block_amount.replace(/,/g, ""));
        var restval = $("#hc_total_premium").val();
        restval = parseInt(restval.replace(/,/g, ""));
        var balance = parseInt($(".balance").attr('data-value'));
        if (deduction_type == 'F')
        {
            if ($("#hc_estimate").attr("data-type") == 'S')
            {
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#hc_estimate").attr('data-value'))));
                $(".salary_deduction").attr("data-value", (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#hc_estimate").attr('data-value'))));
                $("#hc_estimate").attr('data-value', '0');
            }
            var flex_utilised = parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#hc_total_premium").val()) - parseInt(($("#hc_estimate").attr('data-value') == '') ? 0 : $("#hc_estimate").attr('data-value'));
            if ((balance < ($("#hc_temp").val())) && balance != 0)
            {
                $('#wallet_health').html("<i class='fa fa-inr'></i> " + balance);
                var cut_amt = (parseInt($("#hc_temp").val()) - balance);
                $('#pay_health').html("<i class='fa fa-inr'></i> " + cut_amt);
                $("#getCodeModal3").modal("toggle");
            } else if (balance == 0)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Flex balance is not enough');
                return false;
            } else
            {
                var benifit_type = 6;
                var name = 'Health Check Up';
                var transac_type = 'N';
                $.ajax({
                    url: "/flexi_benifit/save_all_data",
                    type: "POST",
                    data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#hc_temp").val(), name: name, deduction_type: deduction_type, flex_amount: $("#hc_temp").val()},
                    dataType: "json",
                    success: function (response) {
                        $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                        $(".flex_utilised").attr("data-value", response.flex_amount);
                        $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                        $(".salary_deduction").attr("data-value", response.pay_amount);
                        var allotedAmt = $('#allotedAmt').text();

                        allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                        var utilized = $('.flex_utilised').attr('data-value');
                        utilized = parseInt(utilized.replace(/,/g, ""));
                        var data_balance = (allotedAmt - utilized);
                        $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                        $(".balance").attr('data-value', data_balance);
                        swal("", "Successfully submitted");
                        setTimeout("location.reload(true);", 1000);
                    }
                });
            }

            if (block_amount > restval)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('your transaction has benn proceed');
                return false;
            }

            $(".flex_utilised").html("<i class='fa fa-inr'></i> " + flex_utilised);
            $(".flex_utilised").attr("data-value", flex_utilised);
            $("#hc_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#hc_total_premium").val()));
            $("#hc_estimate").attr("data-value", parseInt($("#hc_total_premium").val()));
            $("#hc_estimate").attr("data-type", 'F');
        } else
        {
            if ($("#hc_estimate").attr("data-type") == 'F')
            {
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#hc_estimate").attr('data-value'))));
                $(".flex_utilised").attr("data-value", (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#hc_estimate").attr('data-value'))));
                $("#hc_estimate").attr('data-value', '0');
            }
            var salary_deduction = parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#hc_total_premium").val()) - parseInt(($("#hc_estimate").attr('data-value') == '') ? 0 : $("#hc_estimate").attr('data-value'));
            if (salary_deduction > $("#total_salary").val())
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Please contact HR');
                return false;
            }
            if (block_amount > restval)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('your transaction has benn proceed');
                return false;
            }
            $(".salary_deduction").html("<i class='fa fa-inr'></i> " + salary_deduction);
            $(".salary_deduction").attr("data-value", salary_deduction);
            $("#hc_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#hc_total_premium").val()));
            $("#hc_estimate").attr("data-value", parseInt($("#hc_total_premium").val()));
            $("#hc_estimate").attr("data-type", 'S');
            var benifit_type = 6;
            var name = 'Health Check Up';
            var transac_type = 'N';
            $.ajax({
                url: "/flexi_benifit/save_all_data",
                type: "POST",
                data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#hc_estimate").attr("data-value"), name: name, deduction_type: deduction_type, pay_amount: $("#hc_estimate").attr("data-value")},
                dataType: "json",
                success: function (response) {
                    $(".flex_utilised").attr("data-value", response.flex_amount);
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                    $(".salary_deduction").attr("data-value", response.pay_amount);
                    var allotedAmt = $('#allotedAmt').text();

                    allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                    var utilized = $('.flex_utilised').attr('data-value');
                    utilized = parseInt(utilized.replace(/,/g, ""));
                    var data_balance = (allotedAmt - utilized);
                    $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                    $(".balance").attr('data-value', data_balance);
                    swal("", "Successfully submitted");
                    setTimeout("location.reload(true);", 1000);
                }
            });
        }

    });
    //dependant care

    $("#dc_modal_btn").click(function () {
        $("#dc_total_premium").val($("#dc_estimate").attr("data-value"));
    });

    $("#submitDC").click(function () {
        var deduction_type = $("input[name='dc_type']:checked").val();
        var current_val = $("#dc_estimate").attr('data-value');
        var block_amount = $(".blocked_amount_dc").val();
        block_amount = parseInt(block_amount.replace(/,/g, ""));
        var restval = $("#dc_total_premium").val();
        restval = parseInt(restval.replace(/,/g, ""));
        if (deduction_type == 'F')
        {
            if ($("#dc_estimate").attr("data-type") == 'S')
            {
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#dc_estimate").attr('data-value'))));
                $(".salary_deduction").attr("data-value", (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#dc_estimate").attr('data-value'))));
                $("#dc_estimate").attr('data-value', '0');
            }
            var flex_utilised = parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#dc_total_premium").val()) - parseInt(($("#dc_estimate").attr('data-value') == '') ? 0 : $("#dc_estimate").attr('data-value'));
            if (flex_utilised > $(".flex_alloted").attr('data-value'))
            {

                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Flex balance is not enough');
                return false;
            }
            if (block_amount > restval)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('your transaction has benn proceed');
                return false;
            }
            $(".flex_utilised").html("<i class='fa fa-inr'></i> " + flex_utilised);
            $(".flex_utilised").attr("data-value", flex_utilised);
            $("#dc_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#dc_total_premium").val()));
            $("#dc_estimate").attr("data-value", parseInt($("#dc_total_premium").val()));
            $("#dc_estimate").attr("data-type", 'F');
        } else
        {
            if ($("#dc_estimate").attr("data-type") == 'F')
            {
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#dc_estimate").attr('data-value'))));
                $(".flex_utilised").attr("data-value", (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#dc_estimate").attr('data-value'))));
                $("#dc_estimate").attr('data-value', '0');
            }
            var salary_deduction = parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#dc_total_premium").val()) - parseInt(($("#dc_estimate").attr('data-value') == '') ? 0 : $("#dc_estimate").attr('data-value'));
            if (salary_deduction > $("#total_salary").val())
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Please contact HR');
                return false;
            }
            if (block_amount > restval)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('your transaction has benn proceed');
                return false;
            }
            $(".salary_deduction").html("<i class='fa fa-inr'></i> " + salary_deduction);
            $(".salary_deduction").attr("data-value", salary_deduction);
            $("#dc_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#dc_total_premium").val()));
            $("#dc_estimate").attr("data-value", parseInt($("#dc_total_premium").val()));
            $("#dc_estimate").attr("data-type", 'S');
        }
        var benifit_type = 14;
        var name = 'Smart Watch';
        var transac_type = 'N';
        $.ajax({
            url: "/flexi_benifit/save_all_data",
            type: "POST",
            data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#dc_estimate").attr("data-value"), name: name, deduction_type: deduction_type, flex_amount: $("#dc_estimate").attr("data-value")},
            dataType: "json",
            success: function (response) {
                $(".flex_utilised").attr("data-value", response.flex_amount);
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                $(".salary_deduction").attr("data-value", response.pay_amount);
                var allotedAmt = $('#allotedAmt').text();

                allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                var utilized = $('.flex_utilised').attr('data-value');
                utilized = parseInt(utilized.replace(/,/g, ""));
                var data_balance = (allotedAmt - utilized);
                $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                $(".balance").attr('data-value', data_balance);
                swal("", "Successfully submitted");
                setTimeout("location.reload(true);", 1000);
            }
        });
    });
    //dental care
    $("#dnc_modal_btn").click(function () {
        $("#dnc_total_premium").val($("#dnc_estimate").attr("data-value"));
    });
    $("#submitDNC").click(function () {
        var deduction_type = $("input[name='dnc_type']:checked").val();
        var current_val = $("#dnc_estimate").attr('data-value');
        var block_amount = $(".blocked_amount_dnc").val();
        block_amount = parseInt(block_amount.replace(/,/g, ""));
        var restval = $("#dnc_total_premium").val();
        restval = parseInt(restval.replace(/,/g, ""));

        if (deduction_type == 'F')
        {
            if ($("#dnc_estimate").attr("data-type") == 'S')
            {
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#dnc_estimate").attr('data-value'))));
                $(".salary_deduction").attr("data-value", (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#dnc_estimate").attr('data-value'))));
                $("#dnc_estimate").attr('data-value', '0');
            }
            var flex_utilised = parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#dnc_total_premium").val()) - parseInt(($("#dnc_estimate").attr('data-value') == '') ? 0 : $("#dnc_estimate").attr('data-value'));
            if (flex_utilised > $(".flex_alloted").attr('data-value'))
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Flex balance is not enough');
                return false;
            }
            if (block_amount > restval)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('your transaction has benn proceed');
                return false;
            }
            $(".flex_utilised").html("<i class='fa fa-inr'></i> " + flex_utilised);
            $(".flex_utilised").attr("data-value", flex_utilised);
            $("#dnc_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#dnc_total_premium").val()));
            $("#dnc_estimate").attr("data-value", parseInt($("#dnc_total_premium").val()));
            $("#dnc_estimate").attr("data-type", 'F');
        } else
        {
            if ($("#dnc_estimate").attr("data-type") == 'F')
            {
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#dnc_estimate").attr('data-value'))));
                $(".flex_utilised").attr("data-value", (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#dnc_estimate").attr('data-value'))));
                $("#dnc_estimate").attr('data-value', '0');
            }
            var salary_deduction = parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#dnc_total_premium").val()) - parseInt(($("#dnc_estimate").attr('data-value') == '') ? 0 : $("#dnc_estimate").attr('data-value'));
            if (salary_deduction > $("#total_salary").val())
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Please contact HR');
                return false;
            }
            if (block_amount > restval)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('your transaction has benn proceed');
                return false;
            }
            $(".salary_deduction").html("<i class='fa fa-inr'></i> " + salary_deduction);
            $(".salary_deduction").attr("data-value", salary_deduction);
            $("#dnc_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#dnc_total_premium").val()));
            $("#dnc_estimate").attr("data-value", parseInt($("#dnc_total_premium").val()));
            $("#dnc_estimate").attr("data-type", 'S');
        }
        var benifit_type = 15;
        var name = 'Vaccination & Immunization Care';
        var transac_type = 'N';
        $.ajax({
            url: "/flexi_benifit/save_all_data",
            type: "POST",
            data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#dnc_estimate").attr("data-value"), name: name, deduction_type: deduction_type, flex_amount: $("#dnc_estimate").attr("data-value")},
            dataType: "json",
            success: function (response) {
                $(".flex_utilised").attr("data-value", response.flex_amount);
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                $(".salary_deduction").attr("data-value", response.pay_amount);
                var allotedAmt = $('#allotedAmt').text();

                allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                var utilized = $('.flex_utilised').attr('data-value');
                utilized = parseInt(utilized.replace(/,/g, ""));
                var data_balance = (allotedAmt - utilized);
                $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                $(".balance").attr('data-value', data_balance);
                swal("", "Successfully submitted");
                setTimeout("location.reload(true);", 1000);
            }
        });
    });
    //teleconsultation
    $("#tc_modal_btn").click(function () {
        $("#tc_total_premium").val($("#tc_estimate").attr("data-value"));
    });

    $("#submitTC").click(function () {
        var deduction_type = $("input[name='tc_type']:checked").val();
        var current_val = $("#tc_estimate").attr('data-value');
        if (deduction_type == 'F')
        {
            if ($("#tc_estimate").attr("data-type") == 'S')
            {
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#tc_estimate").attr('data-value'))));
                $(".salary_deduction").attr("data-value", (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#tc_estimate").attr('data-value'))));
                $("#tc_estimate").attr('data-value', '0');
            }
            var flex_utilised = parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#tc_total_premium").val()) - parseInt(($("#tc_estimate").attr('data-value') == '') ? 0 : $("#tc_estimate").attr('data-value'));
            if (flex_utilised > $(".flex_alloted").attr('data-value'))
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Flex balance is not enough');
                return false;
            }
            $(".flex_utilised").html("<i class='fa fa-inr'></i> " + flex_utilised);
            $(".flex_utilised").attr("data-value", flex_utilised);
            $("#tc_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#tc_total_premium").val()));
            $("#tc_estimate").attr("data-value", parseInt($("#tc_total_premium").val()));
            $("#tc_estimate").attr("data-type", 'F');
        } else
        {
            if ($("#tc_estimate").attr("data-type") == 'F')
            {
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#tc_estimate").attr('data-value'))));
                $(".flex_utilised").attr("data-value", (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#tc_estimate").attr('data-value'))));
                $("#tc_estimate").attr('data-value', '0');
            }
            var salary_deduction = parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#tc_total_premium").val()) - parseInt(($("#tc_estimate").attr('data-value') == '') ? 0 : $("#tc_estimate").attr('data-value'));
            if (salary_deduction > $("#total_salary").val())
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Please contact HR');
                return false;
            }
            $(".salary_deduction").html("<i class='fa fa-inr'></i> " + salary_deduction);
            $(".salary_deduction").attr("data-value", salary_deduction);
            $("#tc_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#tc_total_premium").val()));
            $("#tc_estimate").attr("data-value", parseInt($("#tc_total_premium").val()));
            $("#tc_estimate").attr("data-type", 'S');
        }
    });
    //yoga zumba
    $("#yz_modal_btn").click(function () {
        $("#yz_total_premium").val($("#yz_estimate").attr("data-value"));
    });

    $("#submitYZ").click(function () {
        var deduction_type = $("input[name='yz_type']:checked").val();
        var current_val = $("#yz_estimate").attr('data-value');
        var block_amount = $(".blocked_amount_yz").val();
        block_amount = parseInt(block_amount.replace(/,/g, ""));
        var restval = $("#yz_total_premium").val();
        restval = parseInt(restval.replace(/,/g, ""));
        if (deduction_type == 'F')
        {
            if ($("#yz_estimate").attr("data-type") == 'S')
            {
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#yz_estimate").attr('data-value'))));
                $(".salary_deduction").attr("data-value", (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#yz_estimate").attr('data-value'))));
                $("#yz_estimate").attr('data-value', '0');
            }
            var flex_utilised = parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#yz_total_premium").val()) - parseInt(($("#yz_estimate").attr('data-value') == '') ? 0 : $("#yz_estimate").attr('data-value'));
            if (flex_utilised > $(".flex_alloted").attr('data-value'))
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Flex balance is not enough');
                return false;
            }
            if (block_amount > restval)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('your transaction has benn proceed');
                return false;
            }
            $(".flex_utilised").html("<i class='fa fa-inr'></i> " + flex_utilised);
            $(".flex_utilised").attr("data-value", flex_utilised);
            $("#yz_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#yz_total_premium").val()));
            $("#yz_estimate").attr("data-value", parseInt($("#yz_total_premium").val()));
            $("#yz_estimate").attr("data-type", 'F');
        } else
        {
            if ($("#yz_estimate").attr("data-type") == 'F')
            {
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#yz_estimate").attr('data-value'))));
                $(".flex_utilised").attr("data-value", (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#yz_estimate").attr('data-value'))));
                $("#yz_estimate").attr('data-value', '0');
            }
            var salary_deduction = parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#yz_total_premium").val()) - parseInt(($("#yz_estimate").attr('data-value') == '') ? 0 : $("#yz_estimate").attr('data-value'));
            if (salary_deduction > $("#total_salary").val())
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Please contact HR');
                return false;
            }
            if (block_amount > restval)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('your transaction has benn proceed');
                return false;
            }

            $(".salary_deduction").html("<i class='fa fa-inr'></i> " + salary_deduction);
            $(".salary_deduction").attr("data-value", salary_deduction);
            $("#yz_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#yz_total_premium").val()));
            $("#yz_estimate").attr("data-value", parseInt($("#yz_total_premium").val()));
            $("#yz_estimate").attr("data-type", 'S');
        }
        var benifit_type = 10;
        var name = 'Yoga / zumba';
        var transac_type = 'N';
        $.ajax({
            url: "/flexi_benifit/save_all_data",
            type: "POST",
            data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#yz_estimate").attr("data-value"), name: name, deduction_type: deduction_type, flex_amount: $("#yz_estimate").attr("data-value")},
            dataType: "json",
            success: function (response) {
                $(".flex_utilised").attr("data-value", response.flex_amount);
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                $(".salary_deduction").attr("data-value", response.pay_amount);
                var allotedAmt = $('#allotedAmt').text();

                allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                var utilized = $('.flex_utilised').attr('data-value');
                utilized = parseInt(utilized.replace(/,/g, ""));
                var data_balance = (allotedAmt - utilized);
                $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                $(".balance").attr('data-value', data_balance);
                swal("", "Successfully submitted");
                setTimeout("location.reload(true);", 1000);
            }
        });

    });
    //elder care
    $("#ec_modal_btn").click(function () {
        $("#ec_total_premium").val($("#ec_estimate").attr("data-value"));
    });

    $("#submitEC").click(function () {
        var deduction_type = $("input[name='ec_type']:checked").val();
        var current_val = $("#ec_estimate").attr('data-value');
        var block_amount = $(".blocked_amount_ec").val();
        block_amount = parseInt(block_amount.replace(/,/g, ""));
        var restval = $("#ec_total_premium").val();
        restval = parseInt(restval.replace(/,/g, ""));
        if (deduction_type == 'F')
        {
            if ($("#ec_estimate").attr("data-type") == 'S')
            {
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#ec_estimate").attr('data-value'))));
                $(".salary_deduction").attr("data-value", (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#ec_estimate").attr('data-value'))));
                $("#ec_estimate").attr('data-value', '0');
            }
            var flex_utilised = parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#ec_total_premium").val()) - parseInt(($("#ec_estimate").attr('data-value') == '') ? 0 : $("#ec_estimate").attr('data-value'));
            if (flex_utilised > $(".flex_alloted").attr('data-value'))
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Flex balance is not enough');
                return false;
            }
            if (block_amount > restval)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('your transaction has benn proceed');
                return false;
            }
            $(".flex_utilised").html("<i class='fa fa-inr'></i> " + flex_utilised);
            $(".flex_utilised").attr("data-value", flex_utilised);
            $("#ec_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#ec_total_premium").val()));
            $("#ec_estimate").attr("data-value", parseInt($("#ec_total_premium").val()));
            $("#ec_estimate").attr("data-type", 'F');
        } else
        {
            if ($("#ec_estimate").attr("data-type") == 'F')
            {
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#ec_estimate").attr('data-value'))));
                $(".flex_utilised").attr("data-value", (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#ec_estimate").attr('data-value'))));
                $("#ec_estimate").attr('data-value', '0');
            }
            var salary_deduction = parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#ec_total_premium").val()) - parseInt(($("#ec_estimate").attr('data-value') == '') ? 0 : $("#ec_estimate").attr('data-value'));
            if (salary_deduction > $("#total_salary").val())
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Please contact HR');
                return false;
            }
            if (block_amount > restval)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('your transaction has benn proceed');
                return false;
            }
            $(".salary_deduction").html("<i class='fa fa-inr'></i> " + salary_deduction);
            $(".salary_deduction").attr("data-value", salary_deduction);
            $("#ec_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#ec_total_premium").val()));
            $("#ec_estimate").attr("data-value", parseInt($("#ec_total_premium").val()));
            $("#ec_estimate").attr("data-type", 'S');
        }
        var benifit_type = 11;
        var name = 'Elder Care';
        var transac_type = 'N';
        $.ajax({
            url: "/flexi_benifit/save_all_data",
            type: "POST",
            data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#ec_estimate").attr("data-value"), name: name, deduction_type: deduction_type, flex_amount: $("#ec_estimate").attr("data-value")},
            dataType: "json",
            success: function (response) {
                $(".flex_utilised").attr("data-value", response.flex_amount);
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                $(".salary_deduction").attr("data-value", response.pay_amount);
                var allotedAmt = $('#allotedAmt').text();

                allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                var utilized = $('.flex_utilised').attr('data-value');
                utilized = parseInt(utilized.replace(/,/g, ""));
                var data_balance = (allotedAmt - utilized);
                $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                $(".balance").attr('data-value', data_balance);
                swal("", "Successfully submitted");
                setTimeout("location.reload(true);", 1000);
            }
        });
    });
    //gym
    $("#gym_modal_btn").click(function () {
        $("#gym_total_premium").val($("#gym_estimate").attr("data-value"));
    });
    $("#submitGYM").click(function () {
        var deduction_type = $("input[name='gym_type']:checked").val();
        var current_val = $("#gym_estimate").attr('data-value');
        var block_amount = $(".blocked_amount_gym").val();
        block_amount = parseInt(block_amount.replace(/,/g, ""));
        var restval = $("#gym_total_premium").val();
        restval = parseInt(restval.replace(/,/g, ""));
        if (deduction_type == 'F')
        {
            if ($("#gym_estimate").attr("data-type") == 'S')
            {
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#gym_estimate").attr('data-value'))));
                $(".salary_deduction").attr("data-value", (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#gym_estimate").attr('data-value'))));
                $("#gym_estimate").attr('data-value', '0');
            }
            var flex_utilised = parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#gym_total_premium").val()) - parseInt(($("#gym_estimate").attr('data-value') == '') ? 0 : $("#gym_estimate").attr('data-value'));
            if (flex_utilised > $(".flex_alloted").attr('data-value'))
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Flex balance is not enough');
                return false;
            }
            if (block_amount > restval)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('your transaction has benn proceed');
                return false;
            }
            $(".flex_utilised").html("<i class='fa fa-inr'></i> " + flex_utilised);
            $(".flex_utilised").attr("data-value", flex_utilised);
            $("#gym_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#gym_total_premium").val()));
            $("#gym_estimate").attr("data-value", parseInt($("#gym_total_premium").val()));
            $("#gym_estimate").attr("data-type", 'F');
        } else
        {
            if ($("#gym_estimate").attr("data-type") == 'F')
            {
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#gym_estimate").attr('data-value'))));
                $(".flex_utilised").attr("data-value", (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#gym_estimate").attr('data-value'))));
                $("#gym_estimate").attr('data-value', '0');
            }
            var salary_deduction = parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#gym_total_premium").val()) - parseInt(($("#gym_estimate").attr('data-value') == '') ? 0 : $("#gym_estimate").attr('data-value'));
            if (salary_deduction > $("#total_salary").val())
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Please contact HR');
                return false;
            }
            if (block_amount > restval)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('your transaction has benn proceed');
                return false;
            }
            $(".salary_deduction").html("<i class='fa fa-inr'></i> " + salary_deduction);
            $(".salary_deduction").attr("data-value", salary_deduction);
            $("#gym_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#gym_total_premium").val()));
            $("#gym_estimate").attr("data-value", parseInt($("#gym_total_premium").val()));
            $("#gym_estimate").attr("data-type", 'S');
        }
        var benifit_type = 12;
        var name = 'Gym';
        var transac_type = 'N';
        $.ajax({
            url: "/flexi_benifit/save_all_data",
            type: "POST",
            data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#gym_estimate").attr("data-value"), name: name, deduction_type: deduction_type, flex_amount: $("#gym_estimate").attr("data-value")},
            dataType: "json",
            success: function (response) {
                $(".flex_utilised").attr("data-value", response.flex_amount);
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                $(".salary_deduction").attr("data-value", response.pay_amount);
                var allotedAmt = $('#allotedAmt').text();

                allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                var utilized = $('.flex_utilised').attr('data-value');
                utilized = parseInt(utilized.replace(/,/g, ""));
                var data_balance = (allotedAmt - utilized);
                $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                $(".balance").attr('data-value', data_balance);
                swal("", "Successfully submitted");
                setTimeout("location.reload(true);", 1000);
            }
        });
    });
    //cmp
    $("#cmp_modal_btn").click(function () {
        $("#cmp_total_premium").val($("#cmp_estimate").attr("data-value"));
    });
    $("#submitcmp").click(function () {
        var deduction_type = $("input[name='cmp_type']:checked").val();
        var current_val = $("#cmp_estimate").attr('data-value');
        var block_amount = $(".blocked_amount_cmp").val();
        block_amount = parseInt(block_amount.replace(/,/g, ""));
        var restval = $("#cmp_total_premium").val();
        restval = parseInt(restval.replace(/,/g, ""));
        if (deduction_type == 'F')
        {
            if ($("#cmp_estimate").attr("data-type") == 'S')
            {
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#cmp_estimate").attr('data-value'))));
                $(".salary_deduction").attr("data-value", (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#cmp_total_premium").attr('data-value'))));
                $("#cmp_estimate").attr('data-value', '0');
            }
            var flex_utilised = parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#cmp_total_premium").val()) - parseInt(($("#cmp_estimate").attr('data-value') == '') ? 0 : $("#cmp_estimate").attr('data-value'));
            if (flex_utilised > $(".flex_alloted").attr('data-value'))
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Flex balance is not enough');
                return false;
            }

            if (block_amount > restval)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('your transaction has benn proceed');
                return false;
            }
            $(".flex_utilised").html("<i class='fa fa-inr'></i> " + flex_utilised);
            $(".flex_utilised").attr("data-value", flex_utilised);
            $("#cmp_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#cmp_total_premium").val()));
            $("#cmp_estimate").attr("data-value", parseInt($("#cmp_total_premium").val()));
            $("#cmp_estimate").attr("data-type", 'F');
        } else
        {
            if ($("#cmp_estimate").attr("data-type") == 'F')
            {
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#cmp_estimate").attr('data-value'))));
                $(".flex_utilised").attr("data-value", (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#cmp_estimate").attr('data-value'))));
                $("#cmp_estimate").attr('data-value', '0');
            }
            var salary_deduction = parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#cmp_total_premium").val()) - parseInt(($("#cmp_estimate").attr('data-value') == '') ? 0 : $("#cmp_estimate").attr('data-value'));
            if (salary_deduction > $("#total_salary").val())
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Please contact HR');
                return false;
            }

            if (block_amount > restval)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('your transaction has benn proceed');
                return false;
            }
            $(".salary_deduction").html("<i class='fa fa-inr'></i> " + salary_deduction);
            $(".salary_deduction").attr("data-value", salary_deduction);
            $("#cmp_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#cmp_total_premium").val()));
            $("#cmp_estimate").attr("data-value", parseInt($("#cmp_total_premium").val()));
            $("#cmp_estimate").attr("data-type", 'S');
        }
        var benifit_type = 13;
        var name = 'Condition Mgmt Program';
        var transac_type = 'N';
        $.ajax({
            url: "/flexi_benifit/save_all_data",
            type: "POST",
            data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#cmp_estimate").attr("data-value"), name: name, deduction_type: deduction_type, flex_amount: $("#cmp_estimate").attr("data-value")},
            dataType: "json",
            success: function (response) {
                $(".flex_utilised").attr("data-value", response.flex_amount);
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                $(".salary_deduction").attr("data-value", response.pay_amount);
                var allotedAmt = $('#allotedAmt').text();

                allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                var utilized = $('.flex_utilised').attr('data-value');
                utilized = parseInt(utilized.replace(/,/g, ""));
                var data_balance = (allotedAmt - utilized);
                $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                $(".balance").attr('data-value', data_balance);
                swal("", "Successfully submitted");
                setTimeout("location.reload(true);", 1000);
            }
        });
    });

    //nutri
    $("#nutri_modal_btn").click(function () {
        $("#nutri_total_premium").val($("#nutri_estimate").attr("data-value"));
    });
    $("#submitnutri").click(function () {
        var deduction_type = $("input[name='nutri_type']:checked").val();
        var current_val = $("#nutri_estimate").attr('data-value');
        var block_amount = $(".blocked_amount_nutri").val();
        block_amount = parseInt(block_amount.replace(/,/g, ""));
        var restval = $("#nutri_total_premium").val();
        restval = parseInt(restval.replace(/,/g, ""));
        if (deduction_type == 'F')
        {
            if ($("#nutri_estimate").attr("data-type") == 'S')
            {
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#nutri_estimate").attr('data-value'))));
                $(".salary_deduction").attr("data-value", (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#nutri_total_premium").attr('data-value'))));
                $("#nutri_estimate").attr('data-value', '0');
            }
            var flex_utilised = parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#nutri_total_premium").val()) - parseInt(($("#nutri_estimate").attr('data-value') == '') ? 0 : $("#nutri_estimate").attr('data-value'));
            if (flex_utilised > $(".flex_alloted").attr('data-value'))
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Flex balance is not enough');
                return false;
            }
            if (block_amount > restval)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('your transaction has benn proceed');
                return false;
            }
            $(".flex_utilised").html("<i class='fa fa-inr'></i> " + flex_utilised);
            $(".flex_utilised").attr("data-value", flex_utilised);
            $("#nutri_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#nutri_total_premium").val()));
            $("#nutri_estimate").attr("data-value", parseInt($("#nutri_total_premium").val()));
            $("#nutri_estimate").attr("data-type", 'F');
        } else
        {
            if ($("#nutri_estimate").attr("data-type") == 'F')
            {
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#nutri_estimate").attr('data-value'))));
                $(".flex_utilised").attr("data-value", (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#nutri_estimate").attr('data-value'))));
                $("#nutri_estimate").attr('data-value', '0');
            }
            var salary_deduction = parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#nutri_total_premium").val()) - parseInt(($("#nutri_estimate").attr('data-value') == '') ? 0 : $("#nutri_estimate").attr('data-value'));
            if (salary_deduction > $("#total_salary").val())
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Please contact HR');
                return false;
            }
            if (block_amount > restval)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('your transaction has benn proceed');
                return false;
            }
            $(".salary_deduction").html("<i class='fa fa-inr'></i> " + salary_deduction);
            $(".salary_deduction").attr("data-value", salary_deduction);
            $("#nutri_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#nutri_total_premium").val()));
            $("#nutri_estimate").attr("data-value", parseInt($("#nutri_total_premium").val()));
            $("#nutri_estimate").attr("data-type", 'S');
        }
        var benifit_type = 16;
        var name = 'Nutrition & Dietician Counselling';
        var transac_type = 'N';
        $.ajax({
            url: "/flexi_benifit/save_all_data",
            type: "POST",
            data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#nutri_estimate").attr("data-value"), name: name, deduction_type: deduction_type, flex_amount: $("#nutri_estimate").attr("data-value")},
            dataType: "json",
            success: function (response) {
                $(".flex_utilised").attr("data-value", response.flex_amount);
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                $(".salary_deduction").attr("data-value", response.pay_amount);
                var allotedAmt = $('#allotedAmt').text();

                allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                var utilized = $('.flex_utilised').attr('data-value');
                utilized = parseInt(utilized.replace(/,/g, ""));
                var data_balance = (allotedAmt - utilized);
                $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                $(".balance").attr('data-value', data_balance);
                swal("", "Successfully submitted");
                setTimeout("location.reload(true);", 1000);
            }
        });
    });
    //home_health
    $("#home_health_modal_btn").click(function () {
        $("#home_health_total_premium").val($("#home_health_estimate").attr("data-value"));
    });
    $("#submithome_health").click(function () {
        var deduction_type = $("input[name='home_health_type']:checked").val();
        var current_val = $("#home_health_estimate").attr('data-value');
        var block_amount = $(".blocked_amount_health").val();
        block_amount = parseInt(block_amount.replace(/,/g, ""));
        var restval = $("#home_health_total_premium").val();
        restval = parseInt(restval.replace(/,/g, ""));

        if (deduction_type == 'F')
        {
            if ($("#home_health_estimate").attr("data-type") == 'S')
            {
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#home_health_estimate").attr('data-value'))));
                $(".salary_deduction").attr("data-value", (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#home_health_total_premium").attr('data-value'))));
                $("#home_health_estimate").attr('data-value', '0');
            }
            var flex_utilised = parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#home_health_total_premium").val()) - parseInt(($("#home_health_estimate").attr('data-value') == '') ? 0 : $("#home_health_estimate").attr('data-value'));
            if (flex_utilised > $(".flex_alloted").attr('data-value'))
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Flex balance is not enough');
                return false;
            }
            if (block_amount > restval)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('your transaction has benn proceed');
                return false;
            }
            $(".flex_utilised").html("<i class='fa fa-inr'></i> " + flex_utilised);
            $(".flex_utilised").attr("data-value", flex_utilised);
            $("#home_health_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#home_health_total_premium").val()));
            $("#home_health_estimate").attr("data-value", parseInt($("#home_health_total_premium").val()));
            $("#home_health_estimate").attr("data-type", 'F');
        } else
        {
            if ($("#home_health_estimate").attr("data-type") == 'F')
            {
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#home_health_estimate").attr('data-value'))));
                $(".flex_utilised").attr("data-value", (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#home_health_estimate").attr('data-value'))));
                $("#home_health_estimate").attr('data-value', '0');
            }
            var salary_deduction = parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#home_health_total_premium").val()) - parseInt(($("#home_health_estimate").attr('data-value') == '') ? 0 : $("#home_health_estimate").attr('data-value'));
            if (salary_deduction > $("#total_salary").val())
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Please contact HR');
                return false;
            }
            if (block_amount > restval)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('your transaction has benn proceed');
                return false;
            }
            $(".salary_deduction").html("<i class='fa fa-inr'></i> " + salary_deduction);
            $(".salary_deduction").attr("data-value", salary_deduction);
            $("#home_health_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#home_health_total_premium").val()));
            $("#home_health_estimate").attr("data-value", parseInt($("#home_health_total_premium").val()));
            $("#home_health_estimate").attr("data-type", 'S');
        }
        var benifit_type = 17;
        var name = 'Home HealthCare';
        var transac_type = 'N';
        $.ajax({
            url: "/flexi_benifit/save_all_data",
            type: "POST",
            data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#home_health_estimate").attr("data-value"), name: name, deduction_type: deduction_type, flex_amount: $("#home_health_estimate").attr("data-value")},
            dataType: "json",
            success: function (response) {
                $(".flex_utilised").attr("data-value", response.flex_amount);
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                $(".salary_deduction").attr("data-value", response.pay_amount);
                var allotedAmt = $('#allotedAmt').text();

                allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                var utilized = $('.flex_utilised').attr('data-value');
                utilized = parseInt(utilized.replace(/,/g, ""));
                var data_balance = (allotedAmt - utilized);
                $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                $(".balance").attr('data-value', data_balance);
                swal("", "Successfully submitted");
                setTimeout("location.reload(true);", 1000);
            }
        });
    });

    $("#contact1-tab").click(function () {
        $.ajax({
            url: "/flexi_benifit/all_flex_data",
            type: "POST",
            dataType: "json",
            success: function (response) {
                //debugger;
                $("#core_list").empty();
                $("#non_core_list").empty();
                var flex_utilised_index = 0;
                var pay_index = 0;
                var core_benifits = [];
                var non_core_benifits = [];
                var core_premium_flex = 0;
                var core_benifits_deduction_type = "";
                var core_premium_payroll = 0;
                var core_premium_final = 0;
                var core_premium_flex_name = "";
                var core_premium_flex_id = 0;
                var non_core_premium_flex_name = "";
                var non_core_premium_flex_id = 0;
                var non_core_benifits_deduction_type = "";
                var non_core_premium_flex = 0;
                var non_core_premium_payroll = 0;
                var non_core_premium_final = 0;
                $.each(response, function (index, value) {
                    // if (value.flexi_type == 'C') 
                    // {
                    //      $("#core_list").append('<tr data-benifit="'+value.master_flexi_benefit_id+'" data-amount="'+value.final_amount+'" data-si="'+value.sum_insured+'" data-name="'+value.flexi_benefit_name+'" data-deduction="'+value.deduction_type+'"><td>'+value.flexi_benefit_name+'</td><td>'+value.flex_amount+'</td><td>'+value.pay_amount+'</td><td>'+value.final_amount+'</td></tr>');
                    // }
                    // else
                    // {
                    //       $("#non_core_list").append('<tr data-benifit="'+value.master_flexi_benefit_id+'" data-amount="'+value.final_amount+'" data-si="'+value.sum_insured+'" data-name="'+value.flexi_benefit_name+'" data-deduction="'+value.deduction_type+'"><td>'+value.flexi_benefit_name+'</td><td>'+value.flex_amount+'</td><td>'+value.pay_amount+'</td><td>'+value.final_amount+'</td></tr>');
                    // }
                    if (value.transac_type == 'C')
                    {
                        if (!core_benifits.hasOwnProperty(value.master_flexi_benefit_id)) {
                            core_benifits[value.master_flexi_benefit_id] = [];
                        }
                        core_benifits[value.master_flexi_benefit_id].push(value);

                        //$("#core_list").append('<tr data-benifit="'+value.master_flexi_benefit_id+'" data-amount="'+value.final_amount+'" data-si="'+value.sum_insured+'" data-name="'+value.flexi_benefit_name+'" data-deduction="'+value.deduction_type+'"><td>'+value.flexi_benefit_name+'</td><td>'+value.flex_amount+'</td><td>'+value.pay_amount+'</td><td>'+value.final_amount+'</td></tr>');
                    } else
                    {
                        if (!non_core_benifits.hasOwnProperty(value.master_flexi_benefit_id)) {
                            non_core_benifits[value.master_flexi_benefit_id] = [];
                        }
                        non_core_benifits[value.master_flexi_benefit_id].push(value);

                        //$("#non_core_list").append('<tr data-benifit="'+value.master_flexi_benefit_id+'" data-amount="'+value.final_amount+'" data-si="'+value.sum_insured+'" data-name="'+value.flexi_benefit_name+'" data-deduction="'+value.deduction_type+'"><td>'+value.flexi_benefit_name+'</td><td>'+value.flex_amount+'</td><td>'+value.pay_amount+'</td><td>'+value.final_amount+'</td></tr>');
                    }
                });
                var core_benifits = core_benifits.filter(function (item) {
                    return item !== undefined;
                });

                var non_core_benifits = non_core_benifits.filter(function (item) {
                    return item !== undefined;
                });
				
                var i = 0;


                 for (i = 0; i < core_benifits.length; i++)
            {
                for (j = 0; j < core_benifits[i].length; j++)
                {

                    if (core_benifits[i][j].master_flexi_benefit_id == 2)
                    {
                        core_benifits_deduction_type = core_benifits[i][0].deduction_type;
                        core_premium_flex_name = core_benifits[i][j].flexi_benefit_name;
                        core_premium_flex_id = core_benifits[i][j].master_flexi_benefit_id;
                        core_premium_flex += parseInt(core_benifits[i][j].flex_amount);
                        core_premium_payroll += parseInt(core_benifits[i][j].pay_amount);
                        core_premium_final += parseInt(core_benifits[i][j].final_amount);
                        if (core_premium_flex != 0) {
                            $("#core_list").append('<tr data-benifit="' + core_premium_flex_id + '" data-amount="' + core_premium_final + '" data-name="' + core_premium_flex_name + '" data-deduction="' + core_benifits_deduction_type + '"><td>' + core_premium_flex_name + '</td><td>' + core_premium_flex + '</td><td>' + core_premium_payroll + '</td><td>' + core_premium_final + '</td></tr>');
                        } else {
                            $("#core_list").append('<tr data-benifit="' + core_premium_flex_id + '" data-amount="' + core_premium_final + '" data-name="' + core_premium_flex_name + '" data-deduction="' + core_benifits_deduction_type + '"><td>' + core_premium_flex_name + '</td><td><td>'+ core_premium_flex + '</td><td>' + core_premium_payroll + '</td><td>' + core_premium_final + '</td></tr>');
                        }
                    } else {
                        if (core_benifits[i][j].flex_amount != 0) {
                            $("#core_list").append('<tr data-benifit="' + core_benifits[i][0].master_flexi_benefit_id + '" data-amount="' + core_benifits[i][j].final_amount + '" data-si="' + core_benifits[i][0].sum_insured + '" data-name="' + core_benifits[i][0].flexi_benefit_name + '" data-deduction="' + core_benifits[i][0].deduction_type + '"><td>' + core_benifits[i][0].flexi_benefit_name + '</td><td>' + core_benifits[i][j].flex_amount + '</td><td>' + core_benifits[i][j].pay_amount + '</td><td>' + core_benifits[i][j].final_amount + '</td></tr>');
                        } else {
                            $("#core_list").append('<tr data-benifit="' + core_benifits[i][0].master_flexi_benefit_id + '" data-amount="' + core_benifits[i][j].final_amount + '" data-si="' + core_benifits[i][0].sum_insured + '" data-name="' + core_benifits[i][0].flexi_benefit_name + '" data-deduction="' + core_benifits[i][0].deduction_type + '"><td>' + core_benifits[i][0].flexi_benefit_name + '</td><td>'+ core_benifits[i][j].flex_amount +'</td><td>' + core_benifits[i][j].pay_amount + '</td><td>' + core_benifits[i][j].final_amount + '</td></tr>');
                        }
                    }

                }

            }
                //$("#core_list").append('<tr data-benifit="' + core_premium_flex_id + '" data-amount="' + core_premium_final + '" data-name="' + core_premium_flex_name + '" data-deduction="' + core_benifits_deduction_type + '"><td>' + core_premium_flex_name + '</td><td>' + core_premium_flex + '</td><td>' + core_premium_payroll + '</td><td>' + core_premium_final + '</td></tr>');

                for (i = 0; i < non_core_benifits.length; i++)
                {
					
                    for (j = 0; j < non_core_benifits[i].length; j++)
                    {
                        // non_core_premium_flex += parseInt(non_core_benifits[i][j].flex_amount);
                        // non_core_premium_payroll += parseInt(non_core_benifits[i][j].pay_amount);
                        // non_core_premium_final += parseInt(non_core_benifits[i][j].final_amount);
                        non_core_premium_flex = parseInt(non_core_benifits[i][j].flex_amount);
                        non_core_premium_payroll = parseInt(non_core_benifits[i][j].pay_amount);
                        non_core_premium_final = parseInt(non_core_benifits[i][j].final_amount);
                    }

                    $("#non_core_list").append('<tr data-benifit="' + non_core_benifits[i][0].master_flexi_benefit_id + '" data-amount="' + non_core_premium_final + '" data-si="' + non_core_benifits[i][0].sum_insured + '" data-name="' + non_core_benifits[i][0].flexi_benefit_name + '" data-deduction="' + non_core_benifits[i][0].deduction_type + '"><td>' + non_core_benifits[i][0].flexi_benefit_name + '</td><td>' + non_core_premium_flex + '</td><td>' + non_core_premium_payroll + '</td><td>' + non_core_premium_final + '</td></tr>');
                }
            }
        });
    });
    $("#submit_flex_data").click(function () {
        if (Number($("#allotedAmt").html()) >= Number($("#coreAmt").html())) {
            alert("Wallet utilization exceeds Flex wallet");
            return;
        }
        swal({
            title: "Are you sure?",
            text: "Do you wish to confirm enrollment?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
        },
                function (isConfirm) {
                    if (isConfirm == true)
                    {
                        var flex_data = [];
                        var salary_data = [];
                        $("#data tbody tr").each(function () {
                            flex_data.push({
                                benifit_type: $(this).attr('data-benifit'),
                                amount: $(this).attr('data-amount'),
                                sum_insured: $(this).attr('data-si'),
                                name: $.trim($(this).attr('data-name')) + ",",
                                deduction_type: $(this).attr('data-deduction')
                            });
                        });
                        $.ajax({
                            url: "/flexi_benifit/submit_flex_data",
                            type: "POST",
                            async: false,
                            data: {flex_data: flex_data},
                            dataType: "json",
                            success: function (response) {
                                if (response.status == 'true')
                                {
                                    if (response.parent_cover == 'true')
                                    {
                                        var relations = $(".pc_member:checked")
                                                .map(function () {
                                                    return $(this).val();
                                                })
                                                .get();
                                        var numberChecked = $(".pc_member:checked").length;
                                        if (numberChecked == 1)
                                        {
                                            var final_amount = '12000';
                                        } else
                                        {
                                            var final_amount = '24000';
                                        }
                                        var storage = window["localStorage"];
                                        storage.setItem('type', $("input[name='pc_type']:checked").val());
                                        storage.setItem('relations', relations);
                                        storage.setItem('policy_subtype_no', '1');
                                        storage.setItem('parental_cover', '2');
                                        storage.setItem('final_amount', final_amount);
                                        window.location = "/employee/policy_member_parent/";
                                    } else
                                    {
                                        window.location.reload();
                                    }
                                } else
                                {
                                    window.location.reload();
                                }
                            }
                        });
                    } else
                    {
                        return false;
                    }
                }
        );
    });
    $("#show_summary").click(function () {
        $("#flex_summary-tab").trigger('click');
    });
    $(".pc_member").click(function () {
        var numberChecked = $(".pc_member:checked").length;
        if (numberChecked > 2)
        {
            return false;
        }
    });
    $(".data_final_parent").click(function () {
        var btn_val = $(this).val();
        if (btn_val == 'yes')
        {
            var data_amt = parseInt($("#parental_estimate").attr('data-value').replace(/,/g, ""));
            var pay_minus_amt = (data_amt - parseInt($(".balance").attr('data-value').replace(/,/g, "")));
            var wallet_bal_parent = parseInt($(".balance").attr('data-value'));
            var benifit_type = 2;
            var deduction_type = 'F';
            var name = 'Parental Cover';
            var transac_type = 'C';
            var val = [];
            $('.pc_member:checked').each(function (i) {
                val[i] = $(this).val();
            });
            $.ajax({
                url: "/flexi_benifit/save_all_data",
                type: "POST",
                data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#parental_estimate").attr("data-value"), name: name, deduction_type: deduction_type, fr_id: val, flex_amount: wallet_bal_parent, pay_amount: pay_minus_amt},
                dataType: "json",
                success: function (response) {
                    $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                    $(".flex_utilised").attr("data-value", response.flex_amount);
                    $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                    $(".salary_deduction").attr("data-value", response.pay_amount);
                    var allotedAmt = $('#allotedAmt').text();

                    allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                    var utilized = $('.flex_utilised').attr('data-value');
                    utilized = parseInt(utilized.replace(/,/g, ""));
                    var data_balance = (allotedAmt - utilized);
                    $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                    $(".balance").attr('data-value', data_balance);
                }
            });
        } else
        {
            window.location.reload();
        }
    });

    $("#add_pc_members").click(function () {
        $("#hid_data_add_pc").val($(this).attr('data-value'));
        var deduction_type = $("input[name='pc_type']:checked").val();
        var numberChecked = $(".pc_member:checked").length;
        var current_val = 0;
        var balance = parseInt($(".balance").attr('data-value'));
        var val = [];
        $('.pc_member:checked').each(function (i) {
            val[i] = $(this).val();
        });
        if (numberChecked == 1)
        {
            $("#parental_total_premium").val(6000);

        } else
        {
            $("#parental_total_premium").val(12000);

        }

        if (deduction_type == 'F')
        {
            if ($("#parental_estimate").attr("data-type") == 'S')
            {
                $(".salary_deduction").html("<i class='fa fa-inr'></i> " + (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#parental_estimate").attr('data-value'))));
                $(".salary_deduction").attr("data-value", (parseInt($(".salary_deduction").attr("data-value")) - parseInt($("#parental_estimate").attr('data-value'))));
                $("#parental_estimate").attr('data-value', '0');
            }
            var flex_utilised = parseInt($(".flex_utilised").attr('data-value')) + parseInt($("#parental_total_premium").val()) - parseInt(($("#parental_estimate").attr('data-value') == '') ? 0 : $("#parental_estimate").attr('data-value'));
            if ((balance < ($("#parental_total_premium").val())) && balance != 0)
            {
                $('#wallet_parent').html("<i class='fa fa-inr'></i> " + balance);
                var cut_amt = (parseInt($("#parental_total_premium").val()) - balance);
                $('#pay_parent').html("<i class='fa fa-inr'></i> " + cut_amt);
                $("#getCodeModal5").modal("toggle");
            } else if (balance == 0)
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Flex balance is not enough');
                return false;
            } else
            {
                var benifit_type = 2;
                var name = 'Parental Cover';
                var transac_type = 'C';
                $.ajax({
                    url: "/flexi_benifit/save_all_data",
                    type: "POST",
                    data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#parental_total_premium").val(), name: name, deduction_type: deduction_type, fr_id: val, flex_amount: $("#parental_total_premium").val()},
                    dataType: "json",
                    success: function (response) {
                        $(".flex_utilised").html("<i class='fa fa-inr'></i> " + response.flex_amount);
                        $(".flex_utilised").attr("data-value", response.flex_amount);
                        $(".salary_deduction").html("<i class='fa fa-inr'></i> " + response.pay_amount);
                        $(".salary_deduction").attr("data-value", response.pay_amount);
                        var allotedAmt = $('#allotedAmt').text();

                        allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                        var utilized = $('.flex_utilised').attr('data-value');
                        utilized = parseInt(utilized.replace(/,/g, ""));
                        var data_balance = (allotedAmt - utilized);
                        $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                        $(".balance").attr('data-value', data_balance);
                    }
                });
            }
            $(".flex_utilised").html("<i class='fa fa-inr'></i> " + flex_utilised);
            $(".flex_utilised").attr("data-value", flex_utilised);
            $("#parental_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#parental_total_premium").val()));
            $("#parental_estimate").attr("data-value", parseInt($("#parental_total_premium").val()));
            $("#parental_estimate").attr("data-type", 'F');
        } else
        {
            if ($("#parental_estimate").attr("data-type") == 'F')
            {
                $(".flex_utilised").html("<i class='fa fa-inr'></i> " + (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#parental_estimate").attr('data-value'))));
                $(".flex_utilised").attr("data-value", (parseInt($(".flex_utilised").attr("data-value")) - parseInt($("#parental_estimate").attr('data-value'))));
                $("#parental_estimate").attr('data-value', '0');
            }
            var salary_deduction = parseInt($(".salary_deduction").attr('data-value')) + parseInt($("#parental_total_premium").val()) - parseInt(($("#parental_estimate").attr('data-value') == '') ? 0 : $("#parental_estimate").attr('data-value'));
            if (salary_deduction > $("#total_salary").val())
            {
                $("#getCodeModal").modal("toggle");
                $("#getCode").html('Please contact HR');
                return false;
            }
            $(".salary_deduction").html("<i class='fa fa-inr'></i> " + salary_deduction);
            $(".salary_deduction").attr("data-value", salary_deduction);
            $("#parental_estimate").html("<i class='fa fa-inr'></i> " + parseInt($("#parental_total_premium").val()));
            $("#parental_estimate").attr("data-value", parseInt($("#parental_total_premium").val()));
            $("#parental_estimate").attr("data-type", 'S');
            var benifit_type = 2;
            var name = 'Parental Cover';
            var transac_type = 'C';
            $.ajax({
                url: "/flexi_benifit/save_all_data",
                type: "POST",
                data: {benifit_type: benifit_type, transac_type: transac_type, amount: $("#parental_total_premium").val(), name: name, deduction_type: deduction_type, fr_id: val, pay_amount: $("#parental_total_premium").val()},
                dataType: "json",
                success: function (response) {
                    console.log(response);
                }
            });
        }

    });

    $("#data").click(function () {
        window.location.reload();
    });
    // reimbursement 
    $.ajax({
        url: "/flexi_benifit/get_utilised_data",
        type: "POST",
        async: false,
        dataType: "json",
        success: function (data) {
            $.each(data.benifit_data, function (i, v) {
                $("#benifit_" + v.master_flexi_benefit_id).find(".sum_insured_check[value='" + v.sum_insured + "']").click();
                // $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box").html("<i class='fa fa-inr'></i> "+v.final_amount);
                // $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box").attr("data-value",v.final_amount);
                // $("#benifit_"+v.master_flexi_benefit_id).find(".estimate_box").attr("data-type",v.deduction_type);           
                // $("#benifit_"+v.master_flexi_benefit_id).find(".tick-1").removeClass('hidden');
                // $("#benifit_"+v.master_flexi_benefit_id).find(".payment_type[value='"+v.deduction_type+"']").click();
                // $("#benifit_"+v.master_flexi_benefit_id).find(".sum_insured_check[value='"+v.sum_insured+"']").click();
                if (v.master_flexi_benefit_id == 6)
                {
                    if (v.balance_amount == 0)
                    {
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_bal").attr("data-value", v.final_amount);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_bal").html("<i class='fa fa-inr'></i> " + v.final_amount);
                    } else
                    {
                        amt = v.final_amount - v.reimbursement_ingst_amount;
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_bal").attr("data-value", amt);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_bal").html("<i class='fa fa-inr'></i> " + amt);
                    }
                    $("#health_checkup").click(function () {
                        window.location.href = "/employee/health_checkup_flexi_benefit";
                    });
                } else
                {
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_bal").attr("data-value", 0);
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_bal").html("<i class='fa fa-inr'></i> " + "0");
                }
                if (v.master_flexi_benefit_id == 8)
                {
                    if (v.balance_amount == 0)
                    {
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_bal_8").attr("data-value", v.final_amount);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_bal_8").html("<i class='fa fa-inr'></i> " + v.final_amount);
                    } else
                    {
                        amt = v.final_amount - v.reimbursement_ingst_amount;
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_bal_8").attr("data-value", amt);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_bal_8").html("<i class='fa fa-inr'></i> " + amt);
                    }
                    $("#dental_care").click(function () {
                        window.location.href = "/employee/dental_flexi_benefit";
                    });
                } else
                {
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_bal_8").attr("data-value", 0);
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_bal_8").html("<i class='fa fa-inr'></i> " + "0");
                }
                if (v.master_flexi_benefit_id == 11)
                {
                    if (v.balance_amount == 0)
                    {
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_11").attr("data-value", v.final_amount);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_11").html("<i class='fa fa-inr'></i> " + v.final_amount);
                    } else
                    {
                        amt = v.final_amount - v.reimbursement_ingst_amount;
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_11").attr("data-value", amt);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_11").html("<i class='fa fa-inr'></i> " + amt);
                    }
                    $("#elder_care").click(function () {
                        window.location.href = "/employee/childcare_flexi_benefit";
                    });
                } else
                {
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_11").attr("data-value", 0);
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_11").html("<i class='fa fa-inr'></i> " + "0");
                }
                if (v.master_flexi_benefit_id == 12)
                {
                    if (v.balance_amount == 0)
                    {
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_12").attr("data-value", v.final_amount);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_12").html("<i class='fa fa-inr'></i> " + v.final_amount);
                    } else
                    {
                        amt = v.final_amount - v.reimbursement_ingst_amount;
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_12").attr("data-value", amt);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_12").html("<i class='fa fa-inr'></i> " + amt);
                    }
                    $("#gym_care").click(function () {
                        window.location.href = "/employee/gym_flexi_benefit";
                    });
                } else
                {
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_12").attr("data-value", 0);
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_12").html("<i class='fa fa-inr'></i>" + "0");
                }
                if (v.master_flexi_benefit_id == 14)
                {
                    if (v.balance_amount == 0)
                    {
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_7").attr("data-value", v.final_amount);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_7").html("<i class='fa fa-inr'></i> " + v.final_amount);
                    } else
                    {
                        amt = v.final_amount - v.reimbursement_ingst_amount;
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_7").attr("data-value", amt);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_7").html("<i class='fa fa-inr'></i> " + amt);
                    }
                    $("#wearable_care").click(function () {
                        window.location.href = "/employee/wearabledevice_smartwatch_flexi_benefit";
                    });
                } else
                {
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_7").attr("data-value", 0);
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_7").html("<i class='fa fa-inr'></i> " + "0");
                }
                if (v.master_flexi_benefit_id == 10)
                {
                    if (v.balance_amount == 0)
                    {
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_10").attr("data-value", v.final_amount);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_10").html("<i class='fa fa-inr'></i> " + v.final_amount);
                    } else
                    {
                        amt = v.final_amount - v.reimbursement_ingst_amount;
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_10").attr("data-value", amt);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_10").html("<i class='fa fa-inr'></i> " + amt);
                    }
                    $("#yoga").click(function () {
                        window.location.href = "/employee/yogazumba_flexi_benefit";
                    });
                } else
                {
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_10").attr("data-value", 0);
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_10").html("<i class='fa fa-inr'></i> " + "0");
                }

                if (v.master_flexi_benefit_id == 9)
                {
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_9").attr("data-value", v.final_amount);
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_9").html("<i class='fa fa-inr'></i> " + v.final_amount);
                } else
                {
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_9").attr("data-value", 0);
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_9").html("<i class='fa fa-inr'></i> " + "0");
                }
                if (v.master_flexi_benefit_id == 13)
                {
                    if (v.balance_amount == 0)
                    {
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_13").attr("data-value", v.final_amount);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_13").html("<i class='fa fa-inr'></i> " + v.final_amount);
                    } else
                    {
                        amt = v.final_amount - v.reimbursement_ingst_amount;
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_13").attr("data-value", amt);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_13").html("<i class='fa fa-inr'></i> " + amt);
                    }
                    $("#cmp_care").click(function () {
                        window.location.href = "/employee/cmp_flexi_benefit";
                    });
                } else
                {
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_13").attr("data-value", 0);
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_13").html("<i class='fa fa-inr'></i> " + "0");
                }
                if (v.master_flexi_benefit_id == 15)
                {
                    if (v.balance_amount == 0)
                    {
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_bal_15").attr("data-value", v.final_amount);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_bal_15").html("<i class='fa fa-inr'></i> " + v.final_amount);
                    } else
                    {
                        amt = v.final_amount - v.reimbursement_ingst_amount;
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_bal_15").attr("data-value", amt);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_bal_15").html("<i class='fa fa-inr'></i> " + amt);
                    }
                    $("#vaccination_care").click(function () {
                        window.location.href = "/employee/vaccination_immunization_flexi_benefit";
                    });
                } else
                {
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_bal_15").attr("data-value", 0);
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_bal_15").html("<i class='fa fa-inr'></i> " + "0");
                }
                if (v.master_flexi_benefit_id == 16)
                {
                    if (v.balance_amount == 0)
                    {
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_16").attr("data-value", v.final_amount);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_16").html("<i class='fa fa-inr'></i> " + v.final_amount);
                    } else
                    {
                        amt = v.final_amount - v.reimbursement_ingst_amount;
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_16").attr("data-value", amt);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_16").html("<i class='fa fa-inr'></i> " + amt);
                    }
                    $("#nutri_care").click(function () {
                        window.location.href = "/employee/nutrition_dietician_counselling_flexi_benefit";
                    });
                } else
                {
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_16").attr("data-value", 0);
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_16").html("<i class='fa fa-inr'></i> " + "0");
                }
                if (v.master_flexi_benefit_id == 17)
                {
                    if (v.balance_amount == 0)
                    {
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_17").attr("data-value", v.final_amount);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_17").html("<i class='fa fa-inr'></i> " + v.final_amount);
                    } else
                    {
                        amt = v.final_amount - v.reimbursement_ingst_amount;
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_17").attr("data-value", amt);
                        $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_17").html("<i class='fa fa-inr'></i> " + amt);
                    }
                    $("#homehealth_care").click(function () {
                        window.location.href = "/employee/home_healthCare_flexi_benefit";
                    });
                } else
                {
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_17").attr("data-value", 0);
                    $("#benifit_" + v.master_flexi_benefit_id).find(".estimate_box_17").html("<i class='fa fa-inr'></i> " + "0");
                }

            });
        }
    });
    $.ajax({
        url: "/get_parential_transaction_data_on_empid",
        type: "POST",
        dataType: "json",
        async: false,
        success: function (response) {
            if (response.length > 0) {
                var count = 0;
                $.each(response, function (index, value) {
                    count = value.fr_id.length - 1;
                    deduction_type = value.deduction_type;
                    reltionship = value.fr_id;
                    transaction_id = value.employee_flexi_benefit_transaction_id;
                });

                $("#employee_flexi_benefit_transaction_id").val(transaction_id);
                var fr_id = reltionship.split(",");
                $.each(fr_id, function (i, v) {
                    if (v == 4)
                    {
                        frIdAlreadyExist.push("4");
                        checkCount++;
                        $("#customCheck2").prop('checked', true);
                    }
                    if (v == 5)
                    {
                        frIdAlreadyExist.push("5");
                        checkCount++;
                        $("#customCheck1").prop('checked', true);
                    }
                    if (v == 6)
                    {
                        frIdAlreadyExist.push("6");
                        checkCount++;
                        $("#customCheck3").prop('checked', true);
                    }
                    if (v == 7)
                    {
                        frIdAlreadyExist.push("7");
                        checkCount++;
                        $("#customCheck4").prop('checked', true);
                    } else
                    {
                        $("#customCheck4").prop('checked', false);
                    }
                })
                if (count == 2)
                {
                    if (deduction_type == 'F')
                    {
                        $('#pc_type2').prop('checked', true);
                    } else
                    {
                        $('#pc_type1').prop('checked', true);
                    }
                    // $("#add_pc_members").attr('style','display:none');
                    // $("#add_parential").removeAttr('style','display:none');
                }
            }
        }
    });
    $.ajax({
        url: "/get_policy_flexi_transaction_data",
        type: "POST",
        dataType: "json",
        async: false,
        success: function (response) {
            $.each(response, function (i, v) {
                if (v.policy_sub_type_id == "1")
                {
                    $('#submitGHI').css('pointer-events', 'auto');
                }

                if (v.policy_sub_type_id == "2")
                {
                    $('#submitGPA').css('pointer-events', 'auto');
                }

                if (v.policy_sub_type_id == "3")
                {
                    $('#submitGLTA').css('pointer-events', 'auto');
                }

            });

        }
    });
    $("#add_parential").click(function () {
        var deduction_type = $("input[name='pc_type']:checked").val();
        var final_amount = $("#parental_estimate").attr('data-value');
        var employee_flexi_benefit_transaction_id = $("#employee_flexi_benefit_transaction_id").val();
        $.ajax({
            url: "/update_parential_transaction_data",
            type: "POST",
            data: {deduction_type: deduction_type, employee_flexi_benefit_transaction_id: employee_flexi_benefit_transaction_id, final_amount: final_amount},
            dataType: "json",
            async: false,
            success: function (result) {

            }
        });
    });

    $("#data").click(function () {
        window.location.reload();
    });

    $('input[name="customCheck51"]').click(function () {
        if ($('#customCheck51').prop("checked") == true) {

            $("#submit_flex_data").css('pointer-events', 'auto');

        } else
        {
            $("#submit_flex_data").css('pointer-events', 'none');
        }
    });
// $('input[name="customCheck52"]').click(function(){
//       if($('#customCheck52'). prop("checked") == true && $('#customCheck51'). prop("checked") == true){

//             $("#submit_flex_data").css('pointer-events','auto');

//     }
//     else
//     {
//       $("#submit_flex_data").css('pointer-events','none');
//     }
//   });

    $.ajax({
        url: "/flexi_benifit/get_utilised_data",
        type: "POST",
        async: false,
        dataType: "json",
        success: function (response) {
            if (response.benifit_data !== null)
            {
                $.each(response.benifit_data, function (index, value) {
                    if (value.master_flexi_benefit_id == 15) {
                        $(".blocked_amount_dnc").val(value.block_amount);
                    }
                    if (value.master_flexi_benefit_id == 6) {
                        $("#blocked_amount_hc").val(value.block_amount);
                    }
                    if (value.master_flexi_benefit_id == 14) {
                        $(".blocked_amount_dc").val(value.block_amount);
                    }
                    if (value.master_flexi_benefit_id == 12) {
                        $(".blocked_amount_gym").val(value.block_amount);
                    }
                    if (value.master_flexi_benefit_id == 11) {
                        $(".blocked_amount_ec").val(value.block_amount);
                    }
                    if (value.master_flexi_benefit_id == 11) {
                        $(".blocked_amount_ec").val(value.block_amount);
                    }
                    if (value.master_flexi_benefit_id == 10) {
                        $(".blocked_amount_yz").val(value.block_amount);
                    }
                    if (value.master_flexi_benefit_id == 13) {
                        $(".blocked_amount_cmp").val(value.block_amount);
                    }
                    if (value.master_flexi_benefit_id == 16) {
                        $(".blocked_amount_nutri").val(value.block_amount);
                    }
                    if (value.master_flexi_benefit_id == 17) {
                        $(".blocked_amount_health").val(value.block_amount);
                    }

                });
            }
        }
    });


    /* $("#summery_benefit").click(function(){
     $.ajax({
     url: "/flexi_benifit/all_flex_data",
     type: "POST",
     dataType: "json",
     success: function (response) {
     $("#core_list").html("");
     $("#non_core_list").html("");
     var flex_utilised_index = 0;
     var pay_index = 0;
     $.each(response, function( index, value ) {
     
     if (value.flexi_type == 'C') 
     {
     $("#core_list").append('<tr data-benifit="'+value.master_flexi_benefit_id+'" data-amount="'+value.final_amount+'" data-si="'+value.sum_insured+'" data-name="'+value.flexi_benefit_name+'" data-deduction="'+value.deduction_type+'"><td>'+value.flexi_benefit_name+'</td><td>'+value.flex_amount+'</td><td>'+value.pay_amount+'</td><td>'+value.final_amount+'</td></tr>');
     }
     else
     {
     $("#non_core_list").append('<tr data-benifit="'+value.master_flexi_benefit_id+'" data-amount="'+value.final_amount+'" data-si="'+value.sum_insured+'" data-name="'+value.flexi_benefit_name+'" data-deduction="'+value.deduction_type+'"><td>'+value.flexi_benefit_name+'</td><td>'+value.flex_amount+'</td><td>'+value.pay_amount+'</td><td>'+value.final_amount+'</td></tr>');
     }
     var data_flex_utilised_index = value.flex_amount;
     flex_utilised_index += parseInt(data_flex_utilised_index.replace(/,/g, ""));
     var data_pay_index = value.pay_amount;
     pay_index += parseInt(data_pay_index.replace(/,/g, ""));
     });
     var allotedAmt = $('.alloted_index').attr('data-value');
     var balance_index = parseInt(allotedAmt - flex_utilised_index);
     // allotedAmt =  parseInt(allotedAmt.replace(/,/g, ""));
     $(".balance_index").html("<i class='fa fa-inr'></i> "+balance_index);
     $(".flex_utilised_index").html("<i class='fa fa-inr'></i> "+flex_utilised_index);
     $(".salary_deduction_index").html("<i class='fa fa-inr'></i> "+pay_index);
     }
     });
     }); */




});

var slideIndex = 1;
showDivs(slideIndex);

function plusDivs(n, id) {
    showDivs(slideIndex += n, id);
}

function showDivs(n, id) {
    var i;
    var x = document.getElementsByClassName(id);

    if (n > x.length) {
        slideIndex = 1
    }
    if (n < 1) {
        slideIndex = x.length
    }
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
    }
    try {
        x[slideIndex - 1].style.display = "block";
    } catch (e) {
    }

}
function getPremiumCalc() {
    $.post("/flexi_benefit/getGtliTopUpcalc", {
        "sumValue": $("input[name='glta_si']:checked").val()
    }, function (e) {
        // $("#glta_estimate").html('<i class="fa fa-inr"></i>'+ e.toLocaleString('en')+ '</span>');
        // $("#glta_estimate").attr('data-value', e.toLocaleString('en'));
        $("#glta_total_premium").text(e);
    });
}

$("#glta_si2").change(function () {
    getPremiumCalc();
});

$("#glta_si1").change(function () {
    getPremiumCalc();
});

