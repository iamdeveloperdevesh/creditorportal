$(document).ready(function () {
    $.ajax({
        url: "/broker/get_all_employer",
        type: "POST",
        dataType: "json",
        success: function (response) {
            $('#employer_name').empty();
            $('#employer_name').append('<option value=""> Select Employer name</option>');
            for (i = 0; i < response.length; i++) {
                $('#employer_name').append('<option value="' + response[i].company_id + '">' + response[i].comapny_name + '</option>');
            }
        }
    });
    $("#employer_name").change(function () {
        var company_id = $(this).val();
        $.ajax({
            url: "/broker/get_policy_type",
            type: "POST",
            data: { company_id: company_id },
            dataType: "json",
            success: function (response) {
                $('#policy_no').empty();
                $('#policy_no').append('<option value=""> Select Policy</option>');
                for (i = 0; i < response.length; i++) {
                    $('#policy_no').append('<option value="' + response[i].policy_no + '">' + (response[i].policy_sub_type_name + response[i].desgn_name) + '</option>');
                }
            }
        });
    });
    $("#from_date").datepicker
        ({
            dateFormat: "dd-mm-yy",
            prevText: '<i class="fa fa-angle-left"></i>',
            nextText: '<i class="fa fa-angle-right"></i>',
            changeMonth: true,
            changeYear: true,
            yearRange: "-100Y:",
            onSelect: function (date) {
                var date = $(this).datepicker("getDate");
                var tempStartDate = new Date(date);
                var $returning_on = $("#to_date");
                tempStartDate.setDate(date.getDate() + 1);
                $returning_on.datepicker("option", "minDate", tempStartDate);
                $returning_on.datepicker("option", "maxDate", new Date());
                //                var selectedDate = new Date(date);
                //                var msecsInADay = 86400000;
                //                var endDate = new Date(selectedDate.getTime() + msecsInADay);
                //               //Set Minimum Date of EndDatePicker After Selected Date of StartDatePicker
                //                $("#to_date").datepicker( "option", "minDate", endDate );

            },
            maxDate: new Date(),
            minDate: "-100Y +1D"
        });
    $("#to_date").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100Y:",
        maxDate: new Date(),
        // minDate: "-100Y +1D"
    });
    $("#export_data").click(function () {
        var employer_id = $('#employer_name').val();
        var policy_no = $("#policy_no").val();
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        if (employer_id == "") {
            swal("", "please select employer name");
            return false;
        } else if (policy_no == "") {
            swal("", "please select policy no");
            return false;
        } else if (from_date == "") {
            swal("", "please select from date");
            return false;
        } else if (to_date == "") {
            swal("", "please select to date");
            return false;
        } else {
            window.location = '/broker/claim_utilization_excel_data?employer_id=' + employer_id + '&policy_no=' + policy_no + '&from_date=' + from_date + '&to_date=' + to_date;

        }
    });

    $("#btn_save").click(function () {
        var employer_id = $('#employer_name').val();
        var policy_no = $("#policy_no").val();
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        if (employer_id == "") {
            swal("", "please select employer name");
            return false;
        } else if (policy_no == "") {
            swal("", "please select policy no");
            return false;
        } else if (from_date == "") {
            swal("", "please select from date");
            return false;
        } else if (to_date == "") {
            swal("", "please select to date");
            return false;
        } else {
            $.ajax({
                url: "/show_all_claim_utilization_report_data",
                type: "POST",
                data: { employer_id: employer_id, policy_no: policy_no, from_date: from_date, to_date: to_date },
                dataType: "json",
                success: function (response) {
                    $('#male_emp').text(response.male_emp);
                    $('#female_emp').text(response.female_emp);
                    $('#grand_emp_total').text(response.grand_emp_total);
                    $('#share_emp').text(response.share_emp);
                    $('#male_spouse_count').text(response.male_spouse_count);
                    $('#female_spouse_count').text(response.female_spouse_count);
                    $('#total_spouse_count').text(response.total_spouse_count);
                    $('#share_spouse').text(response.share_spouse);
                    $('#male_child_count').text(response.male_child_count);
                    $('#female_child_count').text(response.female_child_count);
                    $('#total_child_count').text(response.total_child_count);
                    $('#share_child').text(response.share_child);
                    $('#male_parent_count').text(response.male_parent_count);
                    $('#female_parent_count').text(response.female_parent_count);
                    $('#total_parent_count').text(response.total_parent_count);
                    $('#share_parent').text(response.male_parent_count);
                    $('#zero_ten_age_emp').text(response.zero_ten_age_emp);
                    $('#zero_ten_age_spouse').text(response.zero_ten_age_spouse);
                    $('#zero_ten_age_child').text(response.zero_ten_age_child);
                    $('#zero_ten_age_parent').text(response.zero_ten_age_parent);
                    $('#total_zero_ten').text(response.total_zero_ten);
                    $('#eleven_twenty_age_emp').text(response.eleven_twenty_age_emp);
                    $('#eleven_twenty_age_spouse').text(response.eleven_twenty_age_spouse);
                    $('#eleven_twenty_age_child').text(response.eleven_twenty_age_child);
                    $('#eleven_twenty_age_parent').text(response.eleven_twenty_age_parent);
                    $('#total_eleven_twenty').text(response.total_eleven_twenty);
                    $('#twentyone_thirty_age_emp').text(response.twentyone_thirty_age_emp);
                    $('#twentyone_thirty_age_spouse').text(response.twentyone_thirty_age_spouse);
                    $('#twentyone_thirty_age_child').text(response.twentyone_thirty_age_child);
                    $('#twentyone_thirty_age_parent').text(response.twentyone_thirty_age_parent);
                    $('#total_twentyone_thirty').text(response.total_twentyone_thirty);
                    $('#thirtyone_forty_age_emp').text(response.thirtyone_forty_age_emp);
                    $('#thirtyone_forty_age_spouse').text(response.thirtyone_forty_age_spouse);
                    $('#thirtyone_forty_age_child').text(response.thirtyone_forty_age_child);
                    $('#thirtyone_forty_age_parent').text(response.thirtyone_forty_age_parent);
                    $('#total_thirtyone_forty').text(response.total_thirtyone_forty);
                    $('#fortyone_fifty_age_emp').text(response.fortyone_fifty_age_emp);
                    $('#fortyone_fifty_age_spouse').text(response.fortyone_fifty_age_spouse);
                    $('#fortyone_fifty_age_child').text(response.fortyone_fifty_age_child);
                    $('#fortyone_fifty_age_parent').text(response.fortyone_fifty_age_parent);
                    $('#total_fortyone_fifty').text(response.total_fortyone_fifty);
                    $('#fiftyone_sixty_age_emp').text(response.fiftyone_sixty_age_emp);
                    $('#fiftyone_sixty_age_spouse').text(response.fiftyone_sixty_age_spouse);
                    $('#fiftyone_sixty_age_child').text(response.fiftyone_sixty_age_child);
                    $('#fiftyone_sixty_age_parent').text(response.fiftyone_sixty_age_parent);
                    $('#total_fiftyone_sixty').text(response.total_fiftyone_sixty);
                    $('#sixtyone_seventy_age_emp').text(response.sixtyone_seventy_age_emp);
                    $('#sixtyone_seventy_age_spouse').text(response.sixtyone_seventy_age_spouse);
                    $('#sixtyone_seventy_age_child').text(response.sixtyone_seventy_age_child);
                    $('#sixtyone_seventy_age_parent').text(response.sixtyone_seventy_age_parent);
                    $('#total_sixtyone_seventy').text(response.total_sixtyone_seventy);
                    $('#greaterthan_seventy_age_emp').text(response.greaterthan_seventy_age_emp);
                    $('#greaterthan_seventy_age_spouse').text(response.greaterthan_seventy_age_spouse);
                    $('#greaterthan_seventy_age_child').text(response.greaterthan_seventy_age_child);
                    $('#greaterthan_seventy_age_parent').text(response.greaterthan_seventy_age_parent);
                    $('#total_greaterthan_seventy').text(response.total_greaterthan_seventy);
                    $('#total_emp_age').text(response.total_emp_age);
                    $('#total_spouse_age').text(response.total_spouse_age);
                    $('#total_child_age').text(response.total_child_age);
                    $('#total_parent_age').text(response.total_parent_age);
                    $('#all_total').text(response.all_total);
                    $('#count_settled').text(response.count_settled);
                    $('#claim_amount_settled').text(response.claim_amount_settled);
                    $('#settled_amt_settled_status').text(response.settled_amt_settled_status);
                    $('#amt_per_settled').text(response.amt_per_settled);
                    $('#count_outstanding').text(response.count_outstanding);
                    $('#sum_outstanding').text(response.sum_outstanding);
                    $('#sum_settled_amount_outstanding').text(response.sum_settled_amount_outstanding);
                    $('#amt_per_outstanding').text(response.amt_per_outstanding);
                    $('#count_rejected').text(response.count_rejected);
                    $('#sum_rejected').text(response.sum_rejected);
                    $('#sum_settled_amount_rejected').text(response.sum_settled_amount_rejected);
                    $('#amt_per_rejected').text(response.amt_per_rejected);
                    $('#count_cancelled').text(response.count_cancelled);
                    $('#sum_cancelled').text(response.sum_cancelled);
                    $('#sum_settled_amount_cancelled').text(response.sum_settled_amount_cancelled);
                    $('#amt_per_cancelled').text(response.amt_per_cancelled);
                    $('#Members').text(response.Members);
                    $('#claim_count').text(response.claim_count);
                    $('#Claim_Count_end_of_policy1').text((response.Claim_Count_end_of_policy1).toFixed(2));
                    $('#Incidence_Ratio1').text((response.Incidence_Ratio1).toFixed(2));
                    $('#Incidence_Ratio_end_of_policy1').text((response.Incidence_Ratio_end_of_policy1).toFixed(2));
                    $('#Members_spouse').text(response.Members_spouse);
                    $('#claim_count_spouse').text(response.claim_count_spouse);
                    $('#spouse_Claim_Count_end_of_policy1').text(response.spouse_Claim_Count_end_of_policy1);
                    $('#spouse_Incidence_Ratio1').text(response.spouse_Incidence_Ratio1);
                    $('#spouse_Incidence_Ratio_end_of_policy1').text(response.spouse_Incidence_Ratio_end_of_policy1);
                    $('#Members_child').text(response.Members_child);
                    $('#claim_child_count').text(response.claim_child_count);
                    $('#Claim_child_Count_end_of_policy1').text(response.Claim_child_Count_end_of_policy1);
                    $('#Incidence_child_Ratio1').text(response.Incidence_child_Ratio1);
                    $('#Incidence_child_Ratio_end_of_policy1').text(response.Incidence_child_Ratio_end_of_policy1);
                    $('#Members_parent').text(response.Members_parent);
                    $('#claim_parent_count').text(response.claim_parent_count);
                    $('#Claim_parent_Count_end_of_policy1').text(response.Claim_parent_Count_end_of_policy1);
                    $('#Incidence_parent_Ratio1').text(response.Incidence_parent_Ratio1);
                    $('#Incidence_parent_Ratio_end_of_policy1').text(response.Incidence_parent_Ratio_end_of_policy1);
                    $('#total_Members').text(response.total_Members);
                    $('#total_claim_count').text(response.total_claim_count);
                    $('#total_Claim_Count_end_of_policy1').text((response.total_Claim_Count_end_of_policy1).toFixed(2));
                    $('#total_Incidence_Ratio1').text((response.total_Incidence_Ratio1).toFixed(2));
                    $('#total_Incidence_Ratio_end_of_policy1').text(response.total_Incidence_Ratio_end_of_policy1);

                    $('#second_head1').text(response.second_head1);
                    $('#third_head1').text(response.third_head1);
                    $('#forth_head1').text(response.forth_head1);
                    $('#fifth_head1').text(response.fifth_head1);
                    $('#sixth_head1').text(response.sixth_head1);
                    $('#second_head2').text(response.second_head2);
                    $('#third_head2').text(response.third_head2);
                    $('#forth_head2').text(response.forth_head2);
                    $('#fifth_head2').text(response.fifth_head2);
                    $('#sixth_head2').text(response.sixth_head2);
                    $('#second_head3').text(response.second_head3);
                    $('#third_head3').text(response.third_head3);
                    $('#forth_head3').text(response.forth_head3);
                    $('#fifth_head3').text(response.fifth_head3);
                    $('#sixth_head3').text(response.sixth_head3);
                    $('#second_head4').text(response.second_head4);
                    $('#third_head4').text(response.third_head4);
                    $('#forth_head4').text(response.forth_head4);
                    $('#fifth_head4').text(response.fifth_head4);
                    $('#sixth_head4').text(response.sixth_head4);
                    $('#second_head5').text(response.second_head5);
                    $('#third_head5').text(response.third_head5);
                    $('#forth_head5').text(response.forth_head5);
                    $('#fifth_head5').text(response.fifth_head5);
                    $('#sixth_head5').text(response.sixth_head5);
                    $('#second_head6').text(response.second_head6);
                    $('#third_head6').text(response.third_head6);
                    $('#forth_head6').text(response.forth_head6);
                    $('#fifth_head6').text(response.fifth_head6);
                    $('#sixth_head6').text(response.sixth_head6);
                    $('#second_head7').text(response.second_head7);
                    $('#third_head7').text(response.third_head7);
                    $('#forth_head7').text(response.forth_head7);
                    $('#fifth_head7').text(response.fifth_head7);
                    $('#sixth_head7').text(response.sixth_head7);
                    $('#second_head8').text(response.second_head8);
                    $('#third_head8').text(response.third_head8);
                    $('#forth_head8').text(response.forth_head8);
                    $('#fifth_head8').text(response.fifth_head8);
                    $('#sixth_head8').text(response.sixth_head8);
                    $('#second_head9').text(response.second_head9);
                    $('#third_head9').text(response.third_head9);
                    $('#forth_head9').text(response.forth_head9);
                    $('#fifth_head9').text(response.fifth_head9);
                    $('#sixth_head9').text(response.sixth_head9);
                    $('#second_head10').text(response.second_head10);
                    $('#third_head10').text(response.third_head10);
                    $('#forth_head10').text(response.forth_head10);
                    $('#fifth_head10').text(response.fifth_head10);
                    $('#sixth_head10').text(response.sixth_head10);
                    $('#third_headhospital1').text(response.third_headhospital1);
                    $('#forth_headhospital1').text(response.forth_headhospital1);
                    $('#third_headhospital2').text(response.third_headhospital2);
                    $('#forth_headhospital2').text(response.forth_headhospital2);
                    $('#third_headhospital3').text(response.third_headhospital3);
                    $('#forth_headhospital3').text(response.forth_headhospital3);
                    $('#third_headhospital4').text(response.third_headhospital4);
                    $('#forth_headhospital4').text(response.forth_headhospital4);
                    $('#third_headhospital5').text(response.third_headhospital5);
                    $('#forth_headhospital5').text(response.forth_headhospital5);
                    $('#third_headhospital6').text(response.third_headhospital6);
                    $('#forth_headhospital6').text(response.forth_headhospital6);
                    $('#third_headhospital7').text(response.third_headhospital7);
                    $('#forth_headhospital7').text(response.forth_headhospital7);
                    $('#third_headhospital8').text(response.third_headhospital8);
                    $('#forth_headhospital8').text(response.forth_headhospital8);
                    $('#third_headhospital9').text(response.third_headhospital9);
                    $('#forth_headhospital9').text(response.forth_headhospital9);
                    $('#third_headhospital10').text(response.third_headhospital10);
                    $('#forth_headhospital10').text(response.forth_headhospital10);
                    $('#third_headhospital11').text(response.third_headhospital11);
                    $('#forth_headhospital11').text(response.forth_headhospital11);
                    $('#count_settled_data').text(response.count_settled);
                    $('#claim_amount_settled_data').text(response.claim_amount_settled);
                    $('#settled_amt_settled_status_data').text(response.settled_amt_settled_status);
                    $('#amt_per_settled_data').text(response.amt_per_settled);
                    $('#count_outstanding_data').text(response.count_outstanding);
                    $('#sum_outstanding_data').text(response.sum_outstanding);
                    $('#sum_settled_amount_outstanding_data').text(response.sum_settled_amount_outstanding);
                    $('#amt_per_outstanding_data').text(response.amt_per_outstanding);
                    $('#count_rejected_data').text(response.count_rejected);
                    $('#sum_rejected_data').text(response.sum_rejected);
                    $('#sum_settled_amount_rejected_data').text(response.sum_settled_amount_rejected);
                    $('#amt_per_rejected_data').text(response.amt_per_rejected);
                    $('#Grand_count_settled_data').text(response.count_settled_data);
                    $('#Grand_claim_amount_settled_data').text(response.claim_amount_settled_data);
                    $('#Grand_settled_amt_settled_status_data').text(response.settled_amt_settled_status_data);
                    $('#amt_per_settled_data').text(response.amt_per_settled_data);
                    $("#total_Incidence_Ratio_end_of_policy1").text($("#Incidence_Ratio_end_of_policy1").text() * 1 + $("#spouse_Incidence_Ratio_end_of_policy1").text() * 1 + $("#Incidence_parent_Ratio_end_of_policy1").text() * 1 + $("#Incidence_child_Ratio_end_of_policy1").text() * 1)
                    var sum_insured = response.total_sum_insured || 0;
                    var sum_count = response.count_revised || 0;
                    var sum_avg_data = response.settled_amount || 0;
                     var cols = "";
                     cols = '<tr class="color-tab-report"><td>' + sum_insured + '</td><td>' + sum_count + '</td><td>' + sum_avg_data + '</td><td>' + ((sum_avg_data / sum_insured) * 100).toFixed(2) + '</td></tr>';
                    /* $.each(response.total_sum_insured, function (index, value) {
                        sum_insured = response.total_sum_insured;
                        sum_count += parseFloat(value['count']);
                        sum_avg_data += parseFloat((value['sum_amt'] / sum_insured) * 100);
                        

                    }); */
                    $("#sum_assured").append(cols);
                    /* var tr_cols = $("<tr class='color-tab-report'>");
                    var td_cols = "<td>Total</td><td>" + sum_count + "</td><td>" + sum_insured + "</td><td>" + sum_avg_data + "</td>";
                    tr_cols.append(td_cols); 
                    $("#sum_assured_grand_total").append(tr_cols);*/
                    $('#second_head_maternity1').text(response.second_head_maternity1);
                    $('#third_head_maternity1').text(response.third_head_maternity1);
                    $('#second_head_maternity2').text(response.second_head_maternity2);
                    $('#third_head_maternity2').text(response.third_head_maternity2);
                    $('#second_head_maternity3').text(response.second_head_maternity3);
                    $('#third_head_maternity3').text(response.third_head_maternity3);
                    $('#count_total_maternity').text(response.count_total_maternity);
                    $('#sum_claim_total_maternity').text(response.sum_claim_total_maternity);
                    $('#bill_amount').text(response.bill_amount);
                    $('#disallowence_amount').text(response.disallowence_amount);
                    $('#settled_amount').text(response.settled_amount);
                    $('#Restricted_agreed_tariff_data').text(response.Restricted_agreed_tariff_data);
                    $('#not_payable').text(response.not_payable);
                    $('#Not_pre_hospitlaisation_period_policy').text(response.Not_pre_hospitlaisation_period_policy);
                    $('#No_report').text(response.No_report);
                    $('#Exceeding_room_limit_policy').text(response.Exceeding_room_limit_policy);
                    $('#Exceeding_sublimit_set_policy').text(response.Exceeding_sublimit_set_policy);
                    $('#No_Breakup').text(response.No_Breakup);
                    $('#No_date_on_bill_no').text(response.No_date_on_bill_no);
                    $('#Not_Proper_format').text(response.Not_Proper_format);
                    $('#Others').text(response.Others);
                    $('#ExcessPaidByPatient').text(response.ExcessPaidByPatient);
                    $('#mou_discount_amount').text(response.mou_discount_amount);
                    $('#copayment_amount').text(response.copayment_amount);
                    $('#count_Less_than_twenty_four_hr').text(response.count_Less_than_twenty_four_hr);
                    $('#total_Less_than_twenty_four_hr').text(response.total_Less_than_twenty_four_hr);
                    $('#count_Cosmetic_not_payble').text(response.count_Cosmetic_not_payble);
                    $('#total_Cosmetic_not_payble').text(response.total_Cosmetic_not_payble);
                    $('#count_OPD_Not_covered').text(response.count_OPD_Not_covered);
                    $('#total_OPD_Not_covered').text(response.total_OPD_Not_covered);
                    $('#count_External_conjenital_not_payble').text(response.count_External_conjenital_not_payble);
                    $('#total_External_conjenital_not_payble').text(response.total_External_conjenital_not_payble);
                    $('#count_Document_not_submited').text(response.count_Document_not_submited);
                    $('#total_Document_not_submited').text(response.total_Document_not_submited);
                    $('#count_Dental_Vision_not_covered').text(response.count_Dental_Vision_not_covered);
                    $('#total_Dental_Vision_not_covered').text(response.total_Dental_Vision_not_covered);
                    $('#count_Standard_Exclusion').text(response.count_Standard_Exclusion);
                    $('#total_Standard_Exclusion').text(response.total_Standard_Exclusion);
                    $('#Report_Data_as_on').text(response.Report_Data_as_on);
                    $('#Policy_Inception_Date').text(response.Policy_Inception_Date);
                    $('#Policy_Expiry_Date').text(response.Policy_Expiry_Date);
                    $('#Days_from_policy_inception').text(response.Days_from_policy_inception);
                    $('#Policy_remaining_Days').text(response.Policy_remaining_Days);
                    $('#Total_amount').text(response.Total_amount);
                    $('#Net_Premium').text(response.Net_Premium);
                    $('#Projected_Full_Year_Claims').text(response.Projected_Full_Year_Claims);
                    $('#Projected_Full_Year_Claims_ibnr').text(response.Projected_Full_Year_Claims_ibnr);
                    $('#Projected_Full_Year_Claims2').text(response.Projected_Full_Year_Claims2);
                    $('#Claim_Ratio_Net_Premium').text(response.Claim_Ratio_Net_Premium);
                    $('#Claim_Ratio_Earned_Premium').text(response.Claim_Ratio_Earned_Premium);
                    $('#Claim_Ratio_ibnr').text(response.Claim_Ratio_ibnr);
                    $('#no_emp').text(response.no_emp);
                    $('#no_lives').text(response.no_lives);
                    $('#Premium_Cost_per_Employee').text(response.Premium_Cost_per_Employee);
                    $('#no_claim').text(response.no_claim);
                    $('#no_claim_amount').text(response.no_claim_amount);
                    $('#no_paid_claim_amount').text(response.no_paid_claim_amount);
                    $('#avg_claim_amount').text(response.avg_claim_amount);
                    $('#avg_paid_amount').text(response.avg_paid_amount);
                    // $('#no_claim_amount').text(response.no_claim_amount);

                    $("#alignmentAnalysis").html("");
                    
                    var str = "";
                    for (i in response.icdarray) {
                        str += "<tr><td>" + i + "</td>";
                        //settled amount / no of claims
                        //((settled amount / no of claims) / response.settled_amount) * 100
                        response.icdarray[i]['avg_settled'] = response.icdarray[i]['settled_amount'] / response.icdarray[i]['no_of_claims'];
                        response.icdarray[i]['settled_amount_per'] = (((response.icdarray[i]['settled_amount'] / response.icdarray[i]['no_of_claims']) / response.settled_amount) * 100).toFixed(2)

                        for (j in response.icdarray[i]) {
                            str += "<td>"+ response.icdarray[i][j] +"</td>";
                        }

                        str += "</tr>";

                    }
                    $("#alignmentAnalysis").html(str);
                    str = "";
                    $("#bestHosp").html("");
                   
                    for (i in response.hospital_utilization) {
                        
                        str += "<tr><td>" + i + "</td>";
                        //settled amount / no of claims
                        //((settled amount / no of claims) / response.settled_amount) * 100

                        for (j in response.hospital_utilization[i]) {
                            debugger;
                            var temp = response.hospital_utilization[i][j] || 0; 
                            str += "<td>"+ temp +"</td>";
                        }

                        str += "</tr>";

                    }


                    $("#bestHosp").html(str);
                    
                }
            });
        }
    });

    $("#btn_save_pdf").click(function () {
        var employer_id = $('#employer_name').val();
        var policy_no = $('#policy_no').val();
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        if (employer_id == "") {
            swal("", "please select employer name");
            return false;
        } else if (from_date == "") {
            swal("", "please select from date");
            return false;
        } else if (to_date == "") {
            swal("", "please select to date");
            return false;
        } else {
            window.location = '/broker/claim_utilization_pdf_data?employer_id=' + employer_id + '&policy_no=' + policy_no + '&from_date=' + from_date + '&to_date=' + to_date;
        }
    });
});


