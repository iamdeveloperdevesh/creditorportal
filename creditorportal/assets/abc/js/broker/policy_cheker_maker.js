$('document').ready(function () {
    var companyName = [];
    var companyId = [];
    var designationName = [];
    var designationId = [];
    $.ajax({
        type: "POST",
        url: "/broker/getPolicyCreationAutoCompletes",
        async: false,
        success: function (e) {
           
            var data = JSON.parse(e);
            var desination_names = [];
            var comp = data["companies"];
            var desg = data["designation"];
            var desg1 = data["designation_diff"];

            for (i = 0; i < desg1.length; ++i) {

                desination_names.push(desg1[i].designation_name);

            }


            for (i = 0; i < comp.length; ++i) {

                companyName.push(comp[i].comapny_name);
                companyId.push(comp[i].company_id);
            }


            for (i = 0; i < desg.length; ++i) {

                designationName.push(desg[i].designation_name);
                designationId.push(desg[i].master_desg_id);
            }

            $("#comanyName").autocomplete({
                source: companyName
            });
        },
        error: function () {

        }
    });
    /* $.post("/broker/getPolicyCreationAutoCompletes",{
     $('#crsf_name').val():$('#crsf_value').val()
     }, function(e) {
     var data = JSON.parse(e);
     var comp = data["companies"];
     var desg = data["designation"];
     for(i = 0; i < comp.length; ++i) {
     companyName.push(comp[i].comapny_name);
     companyId.push(comp[i].company_id);
     }
     
     for(i = 0; i < desg.length; ++i) {
     designationName.push(desg[i].designation_name);
     designationId.push(desg[i].master_desg_id);
     }
     
     $("#comanyName").autocomplete({
     source: companyName
     });
     }); */

    function getAutCompletId(nameArr, idArr, searchString) {
        var nameIndex = nameArr.indexOf(searchString);
        if (nameIndex != -1)
            return idArr[nameIndex];
        return nameIndex;
    }

    function hasDuplicates(array) {
        var valuesSoFar = Object.create(null);
        for (var i = 0; i < array.length; ++i) {
            var value = array[i];
            if (value in valuesSoFar) {
                return true;
            }
            valuesSoFar[value] = true;
        }
        return false;
    }

    $("body").on('keyup', "input[name='designation']", function () {

        $(this).autocomplete({
            source: designationName
        });
    });


    $("#policyStartDate").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100Y:",
        //minDate: new Date(),

        onSelect: function (dateText, inst) {
            var d = $("#policyStartDate").datepicker('getDate')
            d.setDate(d.getDate() + 1);
            $("#policyEndDate").datepicker("destroy");
            $("#enrolWindowStartDate").datepicker("destroy");

            $('#policyEndDate').datepicker({
                dateFormat: "dd-mm-yy",
                prevText: '<i class="fa fa-angle-left"></i>',
                nextText: '<i class="fa fa-angle-right"></i>',
                changeMonth: true,
                changeYear: true,
                // showButtonPanel: true,
                minDate: d,
                onSelect: function (dateText, inst) {
                    $("#enrolWindowStartDate").datepicker("destroy");
                    $("#enrolWindowEndDate").datepicker("destroy");
                    //$("#policyEndDate-error").remove();
                    $("#enrolWindowStartDate").datepicker({
                        dateFormat: 'dd-mm-yy',
                        changeMonth: true,
                        changeYear: true,
                        //showButtonPanel: true,
                        minDate: $("#policyStartDate").datepicker('getDate'),
                        maxDate: $("#policyEndDate").datepicker('getDate'),
                        onSelect: function (dateText, inst) {
                            $("#enrolWindowStartDate-error").remove();
                            var d = $("#enrolWindowStartDate").datepicker('getDate')
                            d.setDate(d.getDate() + 1);
                            $("#enrolWindowEndDate").datepicker("destroy");
                            $("#enrolWindowEndDate").datepicker({
                                dateFormat: 'dd-mm-yy',
                                changeMonth: true,
                                changeYear: true,
                                // showButtonPanel: true,
                                minDate: $("#enrolWindowStartDate").datepicker('getDate'),
                                maxDate: $("#policyEndDate").datepicker('getDate'),
                                onSelect: function (dateText, inst) {
                                    //$(this).val(dateText);
                                    $("#enrolWindowEndDate-error").remove();
                                }
                            });
                        },

                    });

                }
            });
        },

    });


    $.ajax({
        
        type: "POST",
        url: "/broker/get_policy_creation_det",
        async: false,
        success: function (e) {
            e = JSON.parse(e);
            if (e.policyType) {
                var policyType = e.policyType;
               
                var masterInsurance = e.masterInsurance;
                var tpaCode = e.tpaCode;
                $("#masterInsurance").empty();
                //$("#policyType").empty();
                $("#tpaCode").empty();
                $("#masterInsurance").html("<option value=''>Select</option>");
               //$("#policyType").html("<option value=''> Select </option>");
                $("#tpaCode").html("<option value=''>Select</option>");
                masterInsurance.forEach(function (e) {
                    $("#masterInsurance").append("<option value='" + e.insurer_id + "'>" + e.ins_co_name + "</option>");
                });

                tpaCode.forEach(function (e) {

                    $("#tpaCode").append("<option value='" + e.TPA_id + "'>" + e.TPA_name + "</option>");
                });
            }
        },
        error: function () {

        }
    });


    $("#salesManager").keyup(function (e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z ]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('body').on("keyup", "input[name=designation]", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Za-z ]/g, function (str) {
                return '';
            }));
        }
        return;
    });


    $("#no_times_all_des").keyup(function (e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });


    $('body').on("keyup", "input[name=no_times]", function (e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });


    $('body').on("keyup", "input[name=premium_times]", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });


    $('#hr_mobil_no').keyup(function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('#acc_mobil_no').keyup(function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('#premium_cd_paid').keyup(function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('#cd_balance_thres').keyup(function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });

    $("#policySubType").change(function () {
        
        if ($('#policySubType').val() == "1") {
            $("#forMediclaimDiv").show();
            $("#sumPremiumInsuredDiv").show();
            $("#fileUploadDiv").hide();
        } else if ($('#policySubType').val() == "4") {
            $("#marital_status_chk").hide();
            $("#premium_all").hide();
            $("#sum_isured_all").hide();
            $("#forMediclaimDiv").show();
            $("#sumPremiumInsuredDiv").show();
            $("#fileUploadDiv").hide();
        } else if ($('#policySubType').val() == "5") {
            $("#marital_status_chk").hide();
            $("#premium_all").hide();
            $("#sum_isured_all").hide();
            $("#forMediclaimDiv").show();
            $("#sumPremiumInsuredDiv").show();
            $("#fileUploadDiv").hide();
            $('#companySubTypePolicy').empty();
            $('#companySubTypePolicy').append('<option value=""> Select Sum Insured Type </option>' + ' <option value="designation">Designation Wise</option>');
        } else if ($('#policySubType').val() == "6") {
            $("#marital_status_chk").hide();
            $("#fileUploadgtliTopDiv").show();
            $("#premium_all").hide();
            $("#sum_isured_all").hide();
            $("#forMediclaimDiv").show();
            $("#sumPremiumInsuredDiv").show();
            $("#fileUploadDiv").hide();
            $('#companySubTypePolicy').empty();
            $('#companySubTypePolicy').append('<option value="">  Select Sum Insured Type  </option>' + ' <option value="designation">Designation Wise</option>');
        } else if ($('#policySubType').val() == "2" || $('#policySubType').val() == "3") {

            $(".gpa_pr").css("display", "block");
            $('#companySubTypePolicy').empty();
            $('#companySubTypePolicy').append('<option value=""> Select Sum Insured Type  </option>' + ' <option value="designation">Designation Wise</option>' + ' <option value="grade">Grade Wise</option>');

            // if($("#masterInsurance").val() == 24)
            //$('#companySubTypePolicy').append('<option value="grade">Grade Wise</option>');

            $("#forMediclaimDiv").show();
            $("#sumPremiumInsuredDiv").show();
            $("#fileUploadDiv").hide();
            $("#sum_isured_all").hide();

        } else {

            $("#forMediclaimDiv").hide();
            $("#sumPremiumInsuredDiv").hide();
            $("#fileUploadDiv").show();
            $("#gpa_pr").hide();
            $('#companySubTypePolicy').empty();
            $('#companySubTypePolicy').append('<option value="">  Select Sum Insured Type  </option>' + '<option value="designation">Designation Wise</option>' + '<option value="age">Age Wise</option>' + '<option value="grade">Grade Wise</option>');
        }
    });
    
    $("#policySubType").val('1').trigger('change');
    $("#appFor").change(function () {

        if ($("#policySubType").val() == '2' || $("#policySubType").val() == '3') {
            if ($("#appFor").val() == "designation") {
                $("#no_times_all_des_div").hide();
                $("#premium_all").hide();

            } else {
                $("#no_times_all_des_div").show();
                $("#premium_all").show();
            }
        }

        if ($("#appFor").val() == "designation") {
            $("#tableMediclaim").show();

        } else {

            $("#tableMediclaim").hide();
            var add_tbody = $("#add_tbody > tr");
            $("#add_tbody > tr")[0].querySelector("input[type='text']").value = "";
            for (i = 1; i < add_tbody.length; ++i) {
                add_tbody[i].remove()
            }
        }
    });
    
     $("#appFor").val("designation").trigger('change');
    
    $("#sum_insured_type").change(function () {
        if ($("#policySubType").val() == 4 || $("#policySubType").val() == 5 || $("#policySubType").val() == 6) {
            if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                $("#tableSiTopUp").show();
            } else {
                $("#tableSiTopUp").hide();
                var add_si_tbody = $("#add_si_tbody > tr");
                $("#add_si_tbody > tr")[0].querySelector("input[type='text']").value = "";
                for (i = 1; i < add_si_tbody.length; ++i) {
                    add_si_tbody[i].remove()
                }
            }
        }
    });

    $("#flatpremium").on("change", function() {
        $("#sumInsuredPremiumDiv").show();
        $("#fileUploadperMiliDiv").hide();
        $("#sumInsured").removeClass("ignore");
        $("#sumPremium").removeClass("ignore");
        $("#premiumEmployeeContri").removeClass("ignore");
        $("#premiumEmployerContri").removeClass("ignore");
        $("#perMiliFile").addClass("ignore");
    });

    $("#masterInsurance").on("change", function() {
        // only for aditya birla insurance calculation of permilli rate
        if($("#masterInsurance").val() == 201 && $("#policyType").val() == 1 && $("#policySubType").val() == 2) {
            $("#premiumMiliDiv").show();
            $("#flatpremium").removeClass("ignore");
            $("#premiumMili").removeClass("ignore");
        }else {
            $("#premiumMiliDiv").hide();
            $("#flatpremium").addClass("ignore");
            $("#premiumMili").addClass("ignore");
            $("#perMiliFile").addClass("ignore");
            $("#premiumCalc").addClass("ignore");
        }
    });

    $("#premiumMili").on("change", function() {
        $("#fileUploadperMiliDiv").show();
        $("#sumInsuredPremiumDiv").hide();
        $("#sumInsured").addClass("ignore");
        $("#sumPremium").addClass("ignore");
        $("#premiumEmployeeContri").addClass("ignore");
        $("#premiumEmployerContri").addClass("ignore");
        $("#perMiliFile").removeClass("ignore");
    });


    $("#marital_status").on("change", function () {
        if (this.checked) {
            $("#marital_status_div").show();
            $("#premium_all").hide();
            $("#sum_isured_all").hide();
            $("#singleStatus_si").prop('required', true);
            $("#singleStatus_pre").prop('required', true);
            $("#marriedStatus_si").prop('required', true);

        } else {
            $("#marital_status_div").hide();
            $("#premium_all").show();
            $("#sum_isured_all").show();
        }
    });

    $("#btn_add").click(function () {
        var newRow = $("<tr>");
        var cols = "";
        if ($('#policySubType').val() == "2" || $('#policySubType').val() == "3") {
            cols += '<td style="text-align: right;"><input style="margin-top: 15px;" type="text" placeholder="Designation" class="form-control" name="designation" autocomplete="off"><input type="text" placeholder="No of times" class="form-control" name="no_times" autocomplete="off"><input type="text" placeholder="Premium"  class="form-control" name="premium_times"  autocomplete="off"></td>';
            cols += '<td name="del_btn" class="del_btn" style="width:15%;text-align: right;"><a><i style="margin-top: 15px;" class="fa fa-trash" aria-hidden="true"></i></a></td>';
            newRow.append(cols);
            $("#add_tbody").append(newRow);
        } else {
            cols += '<td style="text-align: right;"><input style="margin-top: 15px;" type="text" placeholder="Designation" class="form-control" name="designation" autocomplete="off"></td>';
            cols += '<td name="del_btn" class="del_btn" style="width:15%;text-align: right;"><a><i style="margin-top: 15px;" class="fa fa-trash" aria-hidden="true"></i></a></td>';
            newRow.append(cols);
            $("#add_tbody").append(newRow);
        }
    });

    $("#btn_add_si").click(function () {
        var newRow = $("<tr>");
        var cols = "";
        cols += '<td style="text-align: right;"><input style="margin-top: 15px;" type="text" placeholder="Sum Insured" class="form-control" name="sum_insured_opt" autocomplete="off"><input style="margin-top: 15px;" type="text" placeholder="premium" class="form-control" name="premium_opt" autocomplete="off"></td>';
        cols += '<td name="del_btn_opt" class="del_btn_opt" style="width:15%;text-align: right;"><a><i style="margin-top: 15px;" class="fa fa-trash" aria-hidden="true"></i></a></td>';
        newRow.append(cols);
        $("#add_si_tbody").append(newRow);
    });

    $("body").on('click', ".del_btn_opt", function () {
        this.parentNode.remove();
    });

    $("body").on('click', ".del_btn", function () {
        this.parentNode.remove();
    });
    $("#policyType").change(function () {
     
        if($("#policySubType").val() != ""){
            $('#policySubType option:eq('+$("#policySubType").val()+')').prop('selected', true)
            
        }
        else{
        $.post("/broker/get_policy_subType", { "policy_type_id": $("#policyType").val() }, function (e) {

            e = JSON.parse(e);
            console.log(e);
            var policySubType = e.policySubType;
            $("#policySubType").empty();
            $("#policySubType").html("<option value=''>Select</option>");
            policySubType.forEach(function (e) {

                $("#policySubType").append("<option value='" + e.policy_sub_type_id + "'>" + e.policy_sub_type_name + "</option>");
            });
        });
    }
    });
    
    $('#policyType').attr("disabled", true); 
    $('#policySubType').attr("disabled", true); 
    
   // $("#policyType").val($("#policyType").val()).trigger('change');
     
    $('#familyConstruct').change(function () {
       
        var ageLimitRadio = $("#ageLimitDiv").find("input[type='radio']");
        var ageLimitText = $("#ageLimitDiv").find("input[type='text']");
        for (i = 0; i < ageLimitRadio.length; ++i) {
            if (((i + 1) % 2) != 0) {
                ageLimitRadio[i].checked = true;
            }
        }

        for (i = 0; i < ageLimitText.length; ++i) {
            ageLimitText[i].value = "0";
            ageLimitText[i].disabled = true;
        }

        var arr = this.value.split(",");
        var adultCount = arr[0];
        var childCount = arr[1];
        if (childCount && childCount > 0) {
            $("#twinsChildLimitDiv").css("display", "block");
        } else {
            $("#twinsChildLimitDiv").css("display", "none");
            $("#twins_child_limit").val("");
        }

        resetDivs();
        if (adultCount == 2) {
            // $('#ageLimitDiv').show();
            $('#unMaryChildDiv1').hide();
            $('#spChildDiv1').hide();
//            $('#sum_insured_type').empty();
//            $('#sum_insured_type').append('<option value="">Select</option>' + '<option value="individual"> Individual </option>' + '<option value="familyIndividual">Family Cover (SI * No of lives)</option>' + '<option value="familyGroup">Family Cover</option>');
            $("#spouseDiv").show();
            $("#spouseDiv").find('[type=checkbox]');
            $("#spousePremiumDiv").show();
            $("#forMediclaimDiv1").show();
            $("#chk_1").prop('required', true);
            $("#chk_0").prop('required', true);
            $("#parent_cross_selection_div").hide();
        } else if (adultCount == 1) {
            $('#unMaryChildDiv1').hide();
            $('#spChildDiv1').hide();
           // $('#ageLimitDiv').show();
//            $('#sum_insured_type').empty();
//            $('#sum_insured_type').append('<option value=""> Select </option>' + ' <option value="individual"> Individual </option>');
            $("#premiumAll").prop("checked", true);
            $("#premiumIndividual").parent().parent().css("display", "none");
            $("#premiumDivCalc").hide();
            $("#premiumDivCalc input[type='text']").val("");
            //$("#spouseDiv").show();
            //$("#spousePremiumDiv").show();
            $("#forMediclaimDiv1").show();
        } else if (adultCount == 4) {
            $("#spouseDiv").show();
            $("#spousePremiumDiv").show();
            $("#adultDiv").show();
            $("#adultPremiumDiv").show();
            $("#forMediclaimDiv1").show();
            $("#parent_cross_selection_div").show();
             $('#unMaryChildDiv1').show();
            $('#spChildDiv1').show();
//            $('#sum_insured_type').empty();
//            $('#sum_insured_type').append('<option value=""> Select </option>' + '<option value="individual"> Individual </option>' + '<option value="familyIndividual">Family Cover (SI * No of lives)</option>' + '<option value="familyGroup">Family Cover</option>');
        }

        if (childCount > 0) {
            $("#childDiv").show();
            $("#childPremiumDiv").show();

        }
    });
 
   $("#familyConstruct").trigger('change');  
//$("#familyConstruct").val($("#familyConstruct option:selected").val()).trigger('change');

    function resetDivs() {
        $("#spouseDiv").hide();
        $("#spousePremiumDiv").hide();
        $("#childDiv").hide();
        $("#childPremiumDiv").hide();
        $("#adultDiv").hide();
        $("#adultPremiumDiv").hide();
        $("#twins_child_limit").val("");
    }

    function hideSubTypePolicy() {
        $("#appForDiv").hide();
        $("#appFor").val("");
        $("#sumInsuredPremiumDiv").hide();
        $("#sumInsured").val("");
        $("#sumPremium").val("");
       // $("#premiumEmployeeContri").val("0");
       // $("#premiumEmployerContri").val("0");
        $("#fileUploadAgeDiv").hide();
        $("#fileUploadGradeDiv").hide();
        $("#tableMediclaim").hide();
        var add_tbody = $("#add_tbody > tr");
        $("#add_tbody > tr")[0].querySelector("input[type='text']").value = "";
        for (i = 1; i < add_tbody.length; ++i) {
            add_tbody[i].remove()
        }
    }

    $("#companySubTypePolicy").on("change", function () {
        
        var str = $(this).val();
       
        //alert($("#companySubTypePolicy").children("option:selected"). val());
        hideSubTypePolicy();
       
        if (str == "designation") {
            //$("#appForDiv").show();
            $("#fileUploaddesignationDiv").show();
            $("#sumInsuredPremiumDiv").show();
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");
            $("#marital_status_chk").parent().parent().css("display", "block");
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Additional Premium";
            $("#premiumAll").parent().parent().css("display", "block");
             $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
        } else if (str == "age") {

            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");
            $("#fileUploadAgeDiv").show();
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Additional Premium";
            $("#marital_status_chk").parent().parent().css("display", "block");
            $("#premiumAll").parent().parent().css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
        } else if (str == "memberAge") {
            $("*[data-name=premium]").parent().css("display", "none");
            $("*[data-name=premium]").addClass("ignore");
            $("#fileUploadAgeDiv").show();
            $("#premiumIndividual").prop("checked", true);
            $("#premiumDivCalc").show();
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Premium Contribution";
            $("#marital_status_chk").parent().parent().css("display", "none");
            $("#premiumAll").parent().parent().css("display", "none");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");

        } else if (str == "grade") {
            //$("#fileUploadGradeDiv").show();
            $("#fileUploadGradeDiv").css("display", "block");
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");
//            $("*[data-name=premium]").parent().css("display", "none");
//            $("*[data-name=premium]").addClass("ignore");
            
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Additional Premium";
            $("#marital_status_chk").parent().parent().css("display", "block");
            $("#premiumAll").parent().parent().css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");


        }
    });
    $("#premiumEmployeeContri").change(function(){
    var data = $("#premiumEmployeeContri").val();
  
    if(data > 0){
       $(".flex_payroll_allocate").show();
    }
    else {
         $(".flex_payroll_allocate").hide();
    }
})
  $("#premiumEmployeeContri").trigger('change'); 
    
    $('#companySubTypePolicy').val($( "#companySubTypePolicy option:selected" ).val()).trigger('change'); 
    $('#formDet').validate({

        rules: {
            selfMinAge: {
                validateAgeLimit: true
            },
            selfMaxAge: {
                validateAgeLimit: true
            },
            spouseMinAge: {
                validateAgeLimit: true
            },
            spouseMaxAge: {
                validateAgeLimit: true
            },
            sonMinAge: {
                validateAgeLimit: true
            },
            sonMaxAge: {
                validateAgeLimit: true
            },
            daughterMinAge: {
                validateAgeLimit: true
            },
            daughterMaxAge: {
                validateAgeLimit: true
            },
            fatherMinAge: {
                validateAgeLimit: true
            },
            fatherMaxAge: {
                validateAgeLimit: true
            },
            motherMinAge: {
                validateAgeLimit: true
            },
            motherMaxAge: {
                validateAgeLimit: true
            },
            fatherInLawMinAge: {
                validateAgeLimit: true
            },
            fatherInLawMaxAge: {
                validateAgeLimit: true
            },
            employeeContriSpChild: {
                specialChildContri: true
            },
            employerContriSpChild: {
                specialChildContri: true
            },
            employeeContriUnMaryChild: {
                employeeContriSpChildContri: true
            },
            employerContriUnMaryChild: {
                employeeContriSpChildContri: true
            },
            premiumEmployeeContri: {
                premiumEmployeeContrivalidation: true
            },
            premiumEmployerContri: {
                premiumEmployeeContrivalidation: true
            },
            spousePremiumEmployeeContri: {
                premiumfEmployeeContrivalidation: true
            },
            spousePremiumEmployerContri: {
                premiumfEmployeeContrivalidation: true
            },
            sonPremiumEmployeeContri: {
                premiumfEmployeeContrivalidation: true
            },
            sonPremiumEmployerContri: {
                premiumfEmployeeContrivalidation: true
            },
            daughterPremiumEmployeeContri: {
                premiumfEmployeeContrivalidation: true
            },
            daughterPremiumEmployerContri: {
                premiumfEmployeeContrivalidation: true
            },
            motherPremiumEmployeeContri: {
                premiumfEmployeeContrivalidation: true
            },
            motherPremiumEmployerContri: {
                premiumfEmployeeContrivalidation: true
            },
            fatherinlawPremiumEmployerContri: {
                premiumfEmployeeContrivalidation: true
            },
            fatherPremiumEmployeeContri: {
                premiumfEmployeeContrivalidation: true
            },
            fatherPremiumEmployerContri: {
                premiumfEmployeeContrivalidation: true
            },
            motherinlawPremiumEmployeeContri: {
                premiumfEmployeeContrivalidation: true
            },
            motherinlawPremiumEmployerContri: {
                premiumfEmployeeContrivalidation: true
            },
            fatherinlawPremiumEmployeeContri: {
                premiumfEmployeeContrivalidation: true
            },

            approval: {
                required: true,
            },
       
            appFor: {
                applictionValidation: true
            },
            policyNo: {
                required: true
            },

            enrolWindowEndDate: {
                required: true,
                checkEnrollDate: "#enrolWindowStartDate"
            },
            enrolWindowStartDate: {
                required: true
            },
            policyEndDate: {
                required: true,
                checkDate: "#policyStartDate"
            },
            policyStartDate: {
                required: true
            },
            masterInsurance: {
                required: true
            },
            salesManager: {
                required: true
            },
            comanyName: {
                required: true
            },
            masterInsurance: {
                required: true
            },
            familyConstruct: {
                required: true
            },
            policyType: {
                required: true
            },
            policySubType: {
                required: true
            },
            brokerPer: {
                brokerValidate: true
            },
            sum_insured_type: {
                required: true
            },
            tpaCode: {
                policySubType: true
            },
            family_cons_rel: {
                family_cons_relValidate: true
            },
            sumInsured: {
                sumInsuredValidate: true
            },
            sumPremium: {
                PrmiumValidate: true,
                required: true
            },
            spousePremium: {
                PrmiumValidate: true,
                required: true
            },
            sonPremium: {
                PrmiumValidate: true,
                required: true
            },
            daughterPremium: {
                PrmiumValidate: true,
                required: true
            },
            motherPremium: {
                PrmiumValidate: true,
                required: true
            },
            fatherPremium: {
                PrmiumValidate: true,
                required: true
            },
            motherinlawPremium: {
                PrmiumValidate: true,
                required: true
            },
            fatherinlawPremium: {
                PrmiumValidate: true,
                required: true
            },

            singleStatus_si: {
                singleSumInsuredValidate: true,
                maritalStatusValidate: true
            },
            singleStatus_pre: {
                singlePremiumValidate: true,
                maritalStatusValidate: true,
                required: true
            },
            marriedStatus_si: {
                marriedSumInsuredValidate: true,
                maritalStatusValidate: true
            },
            marriedStatus_pre: {
                marriedPremiumValidate: true,
                maritalStatusValidate: true,
                required: true
            },
            twins_child_limit: {
                twinsChildLimitCheck: true
            },
          
            cd_balance_thres: {
                cd_balance_thresValidate: true
            },
            premium_cd_paid: {
                premium_cd_paidValidate: true
            },
            account_email: {
                account_emailValidate: true
            },
            hr_email: {
                account_emailValidate: true
            },
            hr_mobil_no: {
                valid_mobile: true,
                required: true
            },
            acc_mobil_no: {
                valid_mobile: true,
                required: true
            },

            no_times: {
                noTimesValidate: true
            },
            premium_times: {
                noTimesValidate: true
            },
            designation: {
                noTimesValidate: true
            },
            no_times_all_des: {
                no_timesValidate: true
            },
            perMiliFile: {
                required: true
            },
            premiumCalc: {
                required: true
            },
            no_times_all_des: {
                no_timesValidate: true
            },
            tax: {
                required: true
            },
            serviceTax: {
                no_timesValidate: true
            },

        },
        success: function (label, element) {
            label.parent().removeClass('error');
            label.remove();
            //var parent = $('.success').parent().get(0); // This would be the <a>'s parent <li>.
            //$(parent).addClass('has-success');	
        },
        messages: {
            ageFile: "Please provide Excel File.",
            //policyEndDate: "Invalid Policy Start & End Date.",
            employeeContriSpChild: "The Employee & Employer total contribution should be 100%.",
            employerContriSpChild: "The Employee & Employer total contribution should be 100%.",
            employeeContriUnMaryChild: "The Employee & Employer total contribution should be 100%.",
            employerContriUnMaryChild: "The Employee & Employer total contribution should be 100%.",
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
        submitHandler: function (form, event) {
            event.preventDefault();
            var form = new FormData();
            var flex_allocate = "N";
            var payroll_allocate = "N";
            var parent_cross_selection = "N";

            if ($("#parent_cross_selection").prop("checked")) {
                parent_cross_selection = "Y";
            }

            if ($("#flex_allocate").prop("checked")) {
                flex_allocate = "Y";
            }

            if ($("#payroll_allocate").prop("checked")) {
                payroll_allocate = "Y";
            }
            $premium_cd_paid = parseInt($("#premium_cd_paid").val());
            $cd_balance_thres = parseInt($("#cd_balance_thres").val());
            if ($premium_cd_paid <= $cd_balance_thres) {
                swal("", "CD balance should be lesser than Premium Paid");
                return false;
            }
            
            var isEnrollStatus = 0;
               if ($('#enroll_status').prop("checked")) {
                    isEnrollStatus = 1;
                }


            form.append("policyNo", $("#policyNo").val());
            form.append("isEnrollStatus", isEnrollStatus);
            form.append("policydetailid", $("#policy_num").val());
           form.append("policyType", $("#policyType").val());
           form.append("policySubType", $("#policySubType").val());
            form.append("masterInsurance", $("#masterInsurance").val());
            form.append("comanyName", $("#comanyName").val());
            form.append("salesManager", $("#salesManager").val());
            form.append("policyStartDate", $("#policyStartDate").val());
            form.append("policyEndDate", $("#policyEndDate").val());
            form.append("familyConstruct", $("#familyConstruct").val());
            form.append("brokerPer", $("#brokerPer").val());
            //form.append("TPA_id", $("#tpaCode").val());
            form.append("sum_insured_type", $("#sum_insured_type").val());
            form.append("flex_allocate", flex_allocate);
            form.append("payroll_allocate", payroll_allocate);
            form.append("tax", $("#tax").val());
            form.append("serviceTax", $("#serviceTax").val());
            form.append("hr_email", $("#hr_email").val());
            form.append("hr_mobil_no", $("#hr_mobil_no").val());
            form.append("account_email", $("#account_email").val());
            form.append("acc_mobil_no", $("#acc_mobil_no").val());
            form.append("premium_cd_paid", $("#premium_cd_paid").val());
            form.append("cd_balance_thres", $("#cd_balance_thres").val());
            form.append("enrolWindowEndDate", $("#enrolWindowEndDate").val());
            form.append("enrolWindowStartDate", $("#enrolWindowStartDate").val());
            form.append("emp_code", $("#approval").val());

            //for aditya birla insurance
            if($("#masterInsurance").val() == 201 && $("#policyType").val() == 1 && $("#policySubType").val() == 2) {
                var premiumCalType = 0;
                if($("#premiumMili").prop("checked")) {
                    premiumCalType = 1;

                    form.append("filename", document.getElementById("perMiliFile").files[0]);

                }



                form.append("premiumCalType", premiumCalType);
            }
             var str = $("#companySubTypePolicy").val();
            if ($("#policySubType").val() == "1") {

                var errorCount = 0;
                var additionalPremium = 0;
                var twins_child_limit = 0;
                var arr = $("#familyConstruct").val().split(",");
                var adultCount = arr[0];
                var childCount = arr[1];
                if (childCount && childCount > 0) {
                    twins_child_limit = $("#twins_child_limit").val();
                }

                if ($("#premiumIndividual").prop("checked")) {
                    additionalPremium = 1;
                }

                var favorite = [];
                $.each($("input[name='family_cons_rel']:checked"), function () {
                    favorite.push($(this).val());
                });
                form.append("family_cons_rel", favorite);

                form.append("additionalPremium", additionalPremium);
                var arr = [];
                var id = [];
                
                form.append("companySubTypePolicy", str);

                if (str == "designation") {

                    form.append("appFor", $("#appFor").val());
                    if ($("#appFor").val() == "designation") {
                        var designation = document.getElementsByName("designation");
                        designation.forEach(function (e) {
                            id.push(getAutCompletId(designationName, designationId, e.value.trim()));
                            arr.push(e.value.trim());
                        });
                        if (hasDuplicates(arr)) {
                            swal("", "Duplicate Designation", "error");
                            return;
                        }
                    }
                    form.append("designation", JSON.stringify(arr));
                    form.append("designationId", JSON.stringify(id));
                    if (document.getElementById("designationFile").files.length > 0) {

                        form.append("filename", document.getElementById("designationFile").files[0]);
                        form.append("fileUploadType", "byDesignation");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }
                } else if (str == "age") {

                    form.append("appFor", "");
                    if (document.getElementById("ageFile").files.length > 0) {

                        form.append("filename", document.getElementById("ageFile").files[0]);
                        form.append("fileUploadType", "byAge");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                } else if (str == "memberAge") {
                    form.append("appFor", "");
                    if (document.getElementById("ageFile").files.length > 0) {
                        form.append("filename", document.getElementById("ageFile").files[0]);
                        form.append("fileUploadType", "byMemberAge");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }
                } else if (str == "grade") {
                    form.append("appFor", "");
                    if (document.getElementById("gradeFile").files.length > 0) {
                        form.append("filename", document.getElementById("gradeFile").files[0]);
                        form.append("fileUploadType", "byGrade");
                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                }
                var isSpChildCheck = 0;
                var ismaritalStatus = 0;
                var employeeContriSpChild = $("#employeeContriSpChild").val();
                var employerContriSpChild = $("#employerContriSpChild").val();
                var isunMaryChildCheck = 0;
                var isSonCheck = 0;
                var isDaughterCheck = 0;
                var employeeContriUnMaryChild = $("#employeeContriUnMaryChild").val();
                var employerContriUnMaryChild = $("#employerContriUnMaryChild").val();
                if ($('#spChild').prop("checked")) {
                    isSpChildCheck = 1;
                }
                if ($('#marital_status').prop("checked")) {
                    ismaritalStatus = 1;
                    form.append("ismaritalStatus", 1);
                    form.append("singleStatus_si", $("#singleStatus_si").val());
                    form.append("singleStatus_pre", $("#singleStatus_pre").val());
                    form.append("marriedStatus_si", $("#marriedStatus_si").val());
                    form.append("marriedStatus_pre", $("#marriedStatus_pre").val());
                }

                if ($('#unMaryChild').prop("checked")) {
                    isunMaryChildCheck = 1;
                }

                if ($('#unMaryChildSon').prop("checked")) {
                    isSonCheck = 1;
                }
                if ($('#unMaryChildDaughter').prop("checked")) {
                    isDaughterCheck = 1;
                }

                form.append("isDaughterCheck", isDaughterCheck);
                form.append("isSonCheck", isSonCheck);
                form.append("isSpChildCheck", isSpChildCheck);
                form.append("employeeContriSpChild", employeeContriSpChild);
                form.append("employerContriSpChild", employerContriSpChild);
                form.append("isunMaryChildCheck", isunMaryChildCheck);
                form.append("employeeContriUnMaryChild", employeeContriUnMaryChild);
                form.append("employerContriUnMaryChild", employerContriUnMaryChild);
                form.append("twins_child_limit", twins_child_limit);
                form.append("parent_cross_selection", parent_cross_selection);
                var ageLimit = $("#ageLimitDiv input[type=text]");
                var arr = [];
                form.append("ageLength", ageLimit.length);
                for (i = 0; i < ageLimit.length; ++i) {

                    if (!arr[ageLimit[i].getAttribute("data-id")]) {
                        arr[ageLimit[i].getAttribute("data-id")] = {};
                        arr[ageLimit[i].getAttribute("data-id")].min = ageLimit[i].value;
                    } else {
                        arr[ageLimit[i].getAttribute("data-id")].max = ageLimit[i].value;
                    }
                }


                arr[0].premium = Number($("#sumPremium").val());
                arr[0].premiumEmployeeContri = Number($("#premiumEmployeeContri").val());
                arr[0].premiumEmployerContri = Number($("#premiumEmployerContri").val());
                var premiumDivCalc = $("#premiumDivCalc input[type='text']");
                for (i = 0; i < premiumDivCalc.length; ++i) {
                    if (premiumDivCalc[i].getAttribute("data-name") == "premium") {

                        if ($("#premiumIndividual").prop("checked")) {
                            if (Number(premiumDivCalc[i].value) == 0) {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premium = Number($("#sumPremium").val());
                            } else {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premium = Number(premiumDivCalc[i].value)
                            }
                        } else {
                            arr[premiumDivCalc[i].getAttribute("data-id")].premium = 0;
                        }

                    } else if (premiumDivCalc[i].getAttribute("data-name") == "employee") {

                        if ($("#premiumIndividual").prop("checked")) {
                            if (Number(premiumDivCalc[i].value) == 0) {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployeeContri = Number($("#premiumEmployeeContri").val());
                            } else {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployeeContri = Number(premiumDivCalc[i].value)
                            }
                        } else {
                            arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployeeContri = 0;
                        }

                    } else if (premiumDivCalc[i].getAttribute("data-name") == "employer") {

                        if ($("#premiumIndividual").prop("checked")) {
                            if (Number(premiumDivCalc[i].value) == 0) {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployerContri = Number($("#premiumEmployerContri").val());
                            } else {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployerContri = Number(premiumDivCalc[i].value)
                            }
                        } else {
                            arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployerContri = 0;
                        }



                    }
                }

                form.append("ageLimit", JSON.stringify(arr));
                form.append("sumInsured", Number($("#sumInsured").val()));
                form.append("sumPremium", Number($("#sumPremium").val()));

                $.ajax({
                    type: "POST",
                    url: "/broker/update_policy",
                    data: form,
                    processData: false,
                    contentType: false,
                    cache: false,
                    async: false,
                    mimeType: "multipart/form-data",
                    success: function (data) {
                        data = JSON.parse(data);
                        swal({
                            title: "Success",
                            text: data.mgs,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true
                        })
                            .then((willDelete) => {
                                if (willDelete) {
                                    //window.location.reload();
                            window.location.replace("/broker/policy_detail");
                                }
                            })
                        return;
                        //swal("", data.mgs);
                    }
                });
            } 
            else if ($("#policySubType").val() == "4") {
                var errorCount = 0;
                var additionalPremium = 0;
                var twins_child_limit = 0;
                var arr = $("#familyConstruct").val().split(",");
                var adultCount = arr[0];
                var childCount = arr[1];
                if (childCount && childCount > 0) {
                    twins_child_limit = $("#twins_child_limit").val();
                }

                if ($("#premiumIndividual").prop("checked")) {
                    additionalPremium = 1;
                }

                form.append("additionalPremium", additionalPremium);
                var arr = [];
                var id = [];
                var si_parr = [];
                var si_earr = [];
                var si_eplrarr = [];
                var si_id = [];
                var str = $("#companySubTypePolicy").val();
                form.append("companySubTypePolicy", str);

                var favorite = [];
                $.each($("input[name='family_cons_rel']:checked"), function () {
                    favorite.push($(this).val());
                });
                form.append("family_cons_rel", favorite);
             
                if (str == "designation") {

                    form.append("appFor", $("#appFor").val());
                    if ($("#appFor").val() == "designation") {
                        var designation = document.getElementsByName("designation");
                        designation.forEach(function (e) {
                            id.push(getAutCompletId(designationName, designationId, e.value.trim()));
                            arr.push(e.value.trim());
                        });
                        if (hasDuplicates(arr)) {
                            swal("", "Duplicate Designation", "error");
                            return;
                        }
                    }


                    form.append("designation", JSON.stringify(arr));
                    form.append("designationId", JSON.stringify(id));
                    if (document.getElementById("designationFile").files.length > 0) {

                        form.append("filename", document.getElementById("designationFile").files[0]);
                        form.append("fileUploadType", "byDesignation");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }
                } else if (str == "age") {
                    form.append("appFor", "");
                    if (document.getElementById("ageFile").files.length > 0) {
                        form.append("filename", document.getElementById("ageFile").files[0]);
                        form.append("fileUploadType", "byAge");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                } else if (str == "memberAge") {
                    form.append("appFor", "");
                    if (document.getElementById("ageFile").files.length > 0) {
                        form.append("filename", document.getElementById("ageFile").files[0]);
                        form.append("fileUploadType", "byMemberAge");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }
                } else if (str == "grade") {
                    form.append("appFor", "");
                    if (document.getElementById("gradeFile").files.length > 0) {
                        form.append("filename", document.getElementById("gradeFile").files[0]);
                        form.append("fileUploadType", "byGrade");
                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                }


                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt");
                    var premium_opt = document.getElementsByName("premium_opt");
                    
                    for (var i = 0; i < sum_insured_option.length; i++) {

                        si_id.push(sum_insured_option[i].value);
                        si_parr.push(premium_opt[i].value);

                    }

                }
                form.append("si_id", JSON.stringify(si_id));
                form.append("si_parr", JSON.stringify(si_parr));
               
                var isSpChildCheck = 0;
                var ismaritalStatus = 0;
                var employeeContriSpChild = $("#employeeContriSpChild").val();
                var employerContriSpChild = $("#employerContriSpChild").val();
                var isunMaryChildCheck = 0;
                var isSonCheck = 0;
                var isDaughterCheck = 0;
                var employeeContriUnMaryChild = $("#employeeContriUnMaryChild").val();
                var employerContriUnMaryChild = $("#employerContriUnMaryChild").val();
                if ($('#spChild').prop("checked")) {
                    isSpChildCheck = 1;
                }

                if ($('#unMaryChild').prop("checked")) {
                    isunMaryChildCheck = 1;
                }
                if ($('#marital_status').prop("checked")) {
                    ismaritalStatus = 1;
                    form.append("ismaritalStatus", 1);
                    form.append("singleStatus_si", $("#singleStatus_si").val());
                    form.append("singleStatus_pre", $("#singleStatus_pre").val());
                    form.append("marriedStatus_si", $("#marriedStatus_si").val());
                    form.append("marriedStatus_pre", $("#marriedStatus_pre").val());
                }

                if ($('#unMaryChildSon').prop("checked")) {
                    isSonCheck = 1;
                }
                if ($('#unMaryChildDaughter').prop("checked")) {
                    isDaughterCheck = 1;
                }

                form.append("isDaughterCheck", isDaughterCheck);
                form.append("isSonCheck", isSonCheck);
                form.append("isSpChildCheck", isSpChildCheck);
                form.append("employeeContriSpChild", employeeContriSpChild);
                form.append("employerContriSpChild", employerContriSpChild);
                form.append("isunMaryChildCheck", isunMaryChildCheck);
                form.append("employeeContriUnMaryChild", employeeContriUnMaryChild);
                form.append("employerContriUnMaryChild", employerContriUnMaryChild);
                form.append("twins_child_limit", twins_child_limit);
                form.append("parent_cross_selection", parent_cross_selection);
                var ageLimit = $("#ageLimitDiv input[type=text]");
                var arr = [];
                form.append("ageLength", ageLimit.length);
                for (i = 0; i < ageLimit.length; ++i) {

                    if (!arr[ageLimit[i].getAttribute("data-id")]) {
                        arr[ageLimit[i].getAttribute("data-id")] = {};
                        arr[ageLimit[i].getAttribute("data-id")].min = ageLimit[i].value;
                    } else {
                        arr[ageLimit[i].getAttribute("data-id")].max = ageLimit[i].value;
                    }
                }


                arr[0].premium = Number($("#sumPremium").val());
                arr[0].premiumEmployeeContri = Number($("#premiumEmployeeContri").val());
                arr[0].premiumEmployerContri = Number($("#premiumEmployerContri").val());
                var premiumDivCalc = $("#premiumDivCalc input[type='text']");
                for (i = 0; i < premiumDivCalc.length; ++i) {
                    if (premiumDivCalc[i].getAttribute("data-name") == "premium") {

                        if ($("#premiumIndividual").prop("checked")) {
                            if (Number(premiumDivCalc[i].value) == 0) {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premium = Number($("#sumPremium").val());
                            } else {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premium = Number(premiumDivCalc[i].value)
                            }
                        } else {
                            arr[premiumDivCalc[i].getAttribute("data-id")].premium = 0;
                        }

                    } else if (premiumDivCalc[i].getAttribute("data-name") == "employee") {

                        if ($("#premiumIndividual").prop("checked")) {
                            if (Number(premiumDivCalc[i].value) == 0) {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployeeContri = Number($("#premiumEmployeeContri").val());
                            } else {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployeeContri = Number(premiumDivCalc[i].value)
                            }
                        } else {
                            arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployeeContri = 0;
                        }

                    } else if (premiumDivCalc[i].getAttribute("data-name") == "employer") {

                        if ($("#premiumIndividual").prop("checked")) {
                            if (Number(premiumDivCalc[i].value) == 0) {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployerContri = Number($("#premiumEmployerContri").val());
                            } else {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployerContri = Number(premiumDivCalc[i].value)
                            }
                        } else {
                            arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployerContri = 0;
                        }
                    }
                }

                form.append("ageLimit", JSON.stringify(arr));
                form.append("sumInsured", Number($("#sumInsured").val()));
                form.append("sumPremium", Number($("#sumPremium").val()));

                $.ajax({
                    type: "POST",
                    url: "/broker/update_policy",
                    data: form,
                    processData: false,
                    contentType: false,
                    cache: false,
                    async: false,
                    mimeType: "multipart/form-data",
                    success: function (data) {
                        data = JSON.parse(data);
                        //swal("", data.mgs);
                        swal({
                            title: "Success",
                            text: data.mgs,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true
                        })
                            .then((willDelete) => {
                                if (willDelete) {
                                    // window.location.reload();
                             window.location.replace("/broker/policy_detail");
                                }
                            })
                        return;
                    }
                });
            }
            else if ($("#policySubType").val() == "2" || $("#policySubType").val() == "3") {

                var errorCount = 0;
                var additionalPremium = 0;
                var twins_child_limit = 0;
                var arr = $("#familyConstruct").val().split(",");
                
                var adultCount = arr[0];
                var childCount = arr[1];
                if (childCount && childCount > 0) {
                    twins_child_limit = $("#twins_child_limit").val();
                }

                if ($("#premiumIndividual").prop("checked")) {
                    additionalPremium = 1;
                }

                form.append("additionalPremium", additionalPremium);
                var favorite = [];
               
              $.each($("input[name='family_cons_rel']:checked"), function () {
                    favorite.push($(this).val());
                });
                form.append("family_cons_rel", favorite);

                var arr = [];
                var id = [];
                var si_parr = [];
                var si_earr = [];
                var si_eplrarr = [];
                var si_id = [];
                var str = $("#companySubTypePolicy").val();
                form.append("companySubTypePolicy", str);

                if (str == "designation") {
                    form.append("appFor", $("#appFor").val());
                    if ($("#appFor").val() == "designation") {
                        var designation = document.getElementsByName("designation");
                        var sum_insured_no_times = document.getElementsByName("no_times");
                        var premium_times = document.getElementsByName("premium_times");
                        designation.forEach(function (e) {
                            id.push(getAutCompletId(designationName, designationId, e.value.trim()));
                            arr.push(e.value.trim());

                        });
                        if (hasDuplicates(arr)) {
                            swal("", "Duplicate Designation", "error");
                            return;
                        }

                        for (var i = 0; i < sum_insured_no_times.length; i++) {

                            si_id.push(sum_insured_no_times[i].value);
                            si_parr.push(premium_times[i].value);

                        }

                    }


                    form.append("designation", JSON.stringify(arr));
                    form.append("designationId", JSON.stringify(id));
                    form.append("si_no_times", JSON.stringify(si_id));
                    form.append("si_parr", JSON.stringify(si_parr));
                    if (document.getElementById("designationFile").files.length > 0) {

                        form.append("filename", document.getElementById("designationFile").files[0]);
                        form.append("fileUploadType", "byDesignation");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }
                } 
                else {
                    
                    form.append("appFor", "");
                    if (str == "age") {

                    form.append("appFor", "");
                    if (document.getElementById("ageFile").files.length > 0) {

                        form.append("filename", document.getElementById("ageFile").files[0]);
                        form.append("fileUploadType", "byAge");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                }
                   else { 
                       if (document.getElementById("gradeFile").files.length > 0) {
                        form.append("filename", document.getElementById("gradeFile").files[0]);
                        form.append("fileUploadType", "byGrade");
                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                    }
                }
                if ($("#appFor").val() == "allEmployee") {
                    form.append("si_no_times_des", $("#no_times_all_des").val());
                }


                var isSpChildCheck = 0;
                var ismaritalStatus = 0;
                var employeeContriSpChild = $("#employeeContriSpChild").val();
                var employerContriSpChild = $("#employerContriSpChild").val();
                var isunMaryChildCheck = 0;
                var isSonCheck = 0;
                var isDaughterCheck = 0;
                var employeeContriUnMaryChild = $("#employeeContriUnMaryChild").val();
                var employerContriUnMaryChild = $("#employerContriUnMaryChild").val();
                if ($('#spChild').prop("checked")) {
                    isSpChildCheck = 1;
                }

                if ($('#unMaryChild').prop("checked")) {
                    isunMaryChildCheck = 1;
                }
                if ($('#marital_status').prop("checked")) {
                    ismaritalStatus = 1;
                    form.append("ismaritalStatus", 1);
                    form.append("singleStatus_si", $("#singleStatus_si").val());
                    form.append("singleStatus_pre", $("#singleStatus_pre").val());
                    form.append("marriedStatus_si", $("#marriedStatus_si").val());
                    form.append("marriedStatus_pre", $("#marriedStatus_pre").val());
                }

                if ($('#unMaryChildSon').prop("checked")) {
                    isSonCheck = 1;
                }
                if ($('#unMaryChildDaughter').prop("checked")) {
                    isDaughterCheck = 1;
                }

                form.append("isDaughterCheck", isDaughterCheck);
                form.append("isSonCheck", isSonCheck);
                form.append("isSpChildCheck", isSpChildCheck);
                form.append("employeeContriSpChild", employeeContriSpChild);
                form.append("employerContriSpChild", employerContriSpChild);
                form.append("isunMaryChildCheck", isunMaryChildCheck);
                form.append("employeeContriUnMaryChild", employeeContriUnMaryChild);
                form.append("employerContriUnMaryChild", employerContriUnMaryChild);
                form.append("twins_child_limit", twins_child_limit);
                form.append("parent_cross_selection", parent_cross_selection);
                var ageLimit = $("#ageLimitDiv input[type=text]");
                var arr = [];
                form.append("ageLength", ageLimit.length);
                for (i = 0; i < ageLimit.length; ++i) {

                    if (!arr[ageLimit[i].getAttribute("data-id")]) {
                        arr[ageLimit[i].getAttribute("data-id")] = {};
                        arr[ageLimit[i].getAttribute("data-id")].min = ageLimit[i].value;
                    } else {
                        arr[ageLimit[i].getAttribute("data-id")].max = ageLimit[i].value;
                    }
                }


                arr[0].premium = Number($("#sumPremium").val());
                arr[0].premiumEmployeeContri = Number($("#premiumEmployeeContri").val());
                arr[0].premiumEmployerContri = Number($("#premiumEmployerContri").val());
                var premiumDivCalc = $("#premiumDivCalc input[type='text']");
                for (i = 0; i < premiumDivCalc.length; ++i) {
                    if (premiumDivCalc[i].getAttribute("data-name") == "premium") {

                        if ($("#premiumIndividual").prop("checked")) {
                            if (Number(premiumDivCalc[i].value) == 0) {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premium = Number($("#sumPremium").val());
                            } else {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premium = Number(premiumDivCalc[i].value)
                            }
                        } else {
                            arr[premiumDivCalc[i].getAttribute("data-id")].premium = 0;
                        }

                    } else if (premiumDivCalc[i].getAttribute("data-name") == "employee") {

                        if ($("#premiumIndividual").prop("checked")) {
                            if (Number(premiumDivCalc[i].value) == 0) {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployeeContri = Number($("#premiumEmployeeContri").val());
                            } else {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployeeContri = Number(premiumDivCalc[i].value)
                            }
                        } else {
                            arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployeeContri = 0;
                        }

                    } else if (premiumDivCalc[i].getAttribute("data-name") == "employer") {

                        if ($("#premiumIndividual").prop("checked")) {
                            if (Number(premiumDivCalc[i].value) == 0) {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployerContri = Number($("#premiumEmployerContri").val());
                            } else {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployerContri = Number(premiumDivCalc[i].value)
                            }
                        } else {
                            arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployerContri = 0;
                        }



                    }
                }

                form.append("ageLimit", JSON.stringify(arr));
                form.append("sumInsured", Number($("#sumInsured").val()));
                form.append("sumPremium", Number($("#sumPremium").val()));

                $.ajax({
                    type: "POST",
                    url: "/broker/update_policy",
                    data: form,
                    processData: false,
                    contentType: false,
                    cache: false,
                    async: false,
                    mimeType: "multipart/form-data",
                    success: function (data) {
                        console.log(data);return;

                        swal({
                            title: "Success",
                            text: data1.mgs,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true
                        })
                            .then((willDelete) => {
                                if (willDelete) {
                                    //window.location.reload();
                             window.location.replace("/broker/policy_detail");
                                }
                            })
                        return;
                    }
                });
            } 
            else if ($("#policySubType").val() == "5") {
                var errorCount = 0;
                var additionalPremium = 0;
                var twins_child_limit = 0;
                var arr = $("#familyConstruct").val().split(",");
                var adultCount = arr[0];
                var childCount = arr[1];
                if (childCount && childCount > 0) {
                    twins_child_limit = $("#twins_child_limit").val();
                }

                if ($("#premiumIndividual").prop("checked")) {
                    additionalPremium = 1;
                }

                form.append("additionalPremium", additionalPremium);
                var favorite = [];
                $.each($("input[name='family_cons_rel']:checked"), function () {
                    favorite.push($(this).val());
                });
                form.append("family_cons_rel", favorite);
                var arr = [];
                var id = [];
                var si_parr = [];
                var si_earr = [];
                var si_eplrarr = [];
                var si_id = [];
                var str = $("#companySubTypePolicy").val();
                form.append("companySubTypePolicy", str);

                if (str == "designation") {

                    form.append("appFor", $("#appFor").val());
                    if ($("#appFor").val() == "designation") {
                        var designation = document.getElementsByName("designation");
                        designation.forEach(function (e) {
                            id.push(getAutCompletId(designationName, designationId, e.value.trim()));
                            arr.push(e.value.trim());
                        });
                        if (hasDuplicates(arr)) {
                            swal("", "Duplicate Designation", "error");
                            return;
                        }
                    }


                    form.append("designation", JSON.stringify(arr));
                    form.append("designationId", JSON.stringify(id));
                    if (document.getElementById("designationFile").files.length > 0) {

                        form.append("filename", document.getElementById("designationFile").files[0]);
                        form.append("fileUploadType", "byDesignation");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }
                }
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt");
                    var premium_opt = document.getElementsByName("premium_opt");
                    for (var i = 0; i < sum_insured_option.length; i++) {

                        si_id.push(sum_insured_option[i].value);
                        si_parr.push(premium_opt[i].value);

                    }

                }
                form.append("si_id", JSON.stringify(si_id));
                form.append("si_parr", JSON.stringify(si_parr));

                var isSpChildCheck = 0;
                var ismaritalStatus = 0;
                var employeeContriSpChild = $("#employeeContriSpChild").val();
                var employerContriSpChild = $("#employerContriSpChild").val();
                var isunMaryChildCheck = 0;
                var isSonCheck = 0;
                var isDaughterCheck = 0;
                var employeeContriUnMaryChild = $("#employeeContriUnMaryChild").val();
                var employerContriUnMaryChild = $("#employerContriUnMaryChild").val();
                if ($('#spChild').prop("checked")) {
                    isSpChildCheck = 1;
                }

                if ($('#unMaryChild').prop("checked")) {
                    isunMaryChildCheck = 1;
                }
                if ($('#marital_status').prop("checked")) {
                    ismaritalStatus = 1;
                    form.append("ismaritalStatus", 1);
                    form.append("singleStatus_si", $("#singleStatus_si").val());
                    form.append("singleStatus_pre", $("#singleStatus_pre").val());
                    form.append("marriedStatus_si", $("#marriedStatus_si").val());
                    form.append("marriedStatus_pre", $("#marriedStatus_pre").val());
                }

                if ($('#unMaryChildSon').prop("checked")) {
                    isSonCheck = 1;
                }
                if ($('#unMaryChildDaughter').prop("checked")) {
                    isDaughterCheck = 1;
                }

                form.append("isDaughterCheck", isDaughterCheck);
                form.append("isSonCheck", isSonCheck);
                form.append("isSpChildCheck", isSpChildCheck);
                form.append("employeeContriSpChild", employeeContriSpChild);
                form.append("employerContriSpChild", employerContriSpChild);
                form.append("isunMaryChildCheck", isunMaryChildCheck);
                form.append("employeeContriUnMaryChild", employeeContriUnMaryChild);
                form.append("employerContriUnMaryChild", employerContriUnMaryChild);
                form.append("twins_child_limit", twins_child_limit);
                form.append("parent_cross_selection", parent_cross_selection);
                var ageLimit = $("#ageLimitDiv input[type=text]");
                var arr = [];
                form.append("ageLength", ageLimit.length);
                for (i = 0; i < ageLimit.length; ++i) {

                    if (!arr[ageLimit[i].getAttribute("data-id")]) {
                        arr[ageLimit[i].getAttribute("data-id")] = {};
                        arr[ageLimit[i].getAttribute("data-id")].min = ageLimit[i].value;
                    } else {
                        arr[ageLimit[i].getAttribute("data-id")].max = ageLimit[i].value;
                    }
                }


                arr[0].premium = Number($("#sumPremium").val());
                arr[0].premiumEmployeeContri = Number($("#premiumEmployeeContri").val());
                arr[0].premiumEmployerContri = Number($("#premiumEmployerContri").val());
                var premiumDivCalc = $("#premiumDivCalc input[type='text']");
                for (i = 0; i < premiumDivCalc.length; ++i) {
                    if (premiumDivCalc[i].getAttribute("data-name") == "premium") {

                        if ($("#premiumIndividual").prop("checked")) {
                            if (Number(premiumDivCalc[i].value) == 0) {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premium = Number($("#sumPremium").val());
                            } else {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premium = Number(premiumDivCalc[i].value)
                            }
                        } else {
                            arr[premiumDivCalc[i].getAttribute("data-id")].premium = 0;
                        }

                    } else if (premiumDivCalc[i].getAttribute("data-name") == "employee") {

                        if ($("#premiumIndividual").prop("checked")) {
                            if (Number(premiumDivCalc[i].value) == 0) {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployeeContri = Number($("#premiumEmployeeContri").val());
                            } else {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployeeContri = Number(premiumDivCalc[i].value)
                            }
                        } else {
                            arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployeeContri = 0;
                        }

                    } else if (premiumDivCalc[i].getAttribute("data-name") == "employer") {

                        if ($("#premiumIndividual").prop("checked")) {
                            if (Number(premiumDivCalc[i].value) == 0) {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployerContri = Number($("#premiumEmployerContri").val());
                            } else {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployerContri = Number(premiumDivCalc[i].value)
                            }
                        } else {
                            arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployerContri = 0;
                        }

                    }
                }

                form.append("ageLimit", JSON.stringify(arr));
                form.append("sumInsured", Number($("#sumInsured").val()));
                form.append("sumPremium", Number($("#sumPremium").val()));

                $.ajax({
                    type: "POST",
                    url: "/broker/update_policy",
                    data: form,
                    processData: false,
                    contentType: false,
                    cache: false,
                    async: false,
                    mimeType: "multipart/form-data",
                    success: function (data) {
                        data = JSON.parse(data);
                        //swal("", data.mgs);
                        swal({
                            title: "Success",
                            text: data.mgs,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true
                        })
                            .then((willDelete) => {
                                if (willDelete) {
                                    //window.location.reload();
                            window.location.replace("/broker/policy_detail");
                                }
                            })
                        return;
                    }
                });
            } 
            else if ($("#policySubType").val() == "6") {
                var errorCount = 0;
                var additionalPremium = 0;
                var twins_child_limit = 0;
                var arr = $("#familyConstruct").val().split(",");
                var adultCount = arr[0];
                var childCount = arr[1];
                if (childCount && childCount > 0) {
                    twins_child_limit = $("#twins_child_limit").val();
                }

                if ($("#premiumIndividual").prop("checked")) {
                    additionalPremium = 1;
                }

                form.append("additionalPremium", additionalPremium);
                var arr = [];
                var id = [];
                var si_parr = [];
                var si_earr = [];
                var si_eplrarr = [];
                var si_id = [];
                var str = $("#companySubTypePolicy").val();
                form.append("companySubTypePolicy", str);

                var favorite = [];
                $.each($("input[name='family_cons_rel']:checked"), function () {
                    favorite.push($(this).val());
                });
                form.append("family_cons_rel", favorite);

                if (str == "designation") {

                    form.append("appFor", $("#appFor").val());
                    if ($("#appFor").val() == "designation") {
                        var designation = document.getElementsByName("designation");
                        designation.forEach(function (e) {
                            id.push(getAutCompletId(designationName, designationId, e.value.trim()));
                            arr.push(e.value.trim());
                        });
                        if (hasDuplicates(arr)) {
                            swal("", "Duplicate Designation", "error");
                            return;
                        }
                    }


                    form.append("designation", JSON.stringify(arr));
                    form.append("designationId", JSON.stringify(id));
                    if (document.getElementById("designationFile").files.length > 0) {

                        form.append("filename", document.getElementById("designationFile").files[0]);
                        form.append("fileUploadType", "byDesignation");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }
                }


                if (document.getElementById("gtliTopupFile").files.length > 0) {
                    form.append("filenamegtl", document.getElementById("gtliTopupFile").files[0]);
                    form.append("fileUploadTypegtl", "gtliTopupFile");
                } else {
                    alert("Please add file to upload.");
                    return;
                }



                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt");
                    var premium_opt = document.getElementsByName("premium_opt");

                    for (var i = 0; i < sum_insured_option.length; i++) {

                        si_id.push(sum_insured_option[i].value);
                        si_parr.push(premium_opt[i].value);

                    }

                }
                form.append("si_id", JSON.stringify(si_id));
                form.append("si_parr", JSON.stringify(si_parr));


                var isSpChildCheck = 0;
                var ismaritalStatus = 0;
                var employeeContriSpChild = $("#employeeContriSpChild").val();
                var employerContriSpChild = $("#employerContriSpChild").val();
                var isunMaryChildCheck = 0;
                var isSonCheck = 0;
                var isDaughterCheck = 0;
                var employeeContriUnMaryChild = $("#employeeContriUnMaryChild").val();
                var employerContriUnMaryChild = $("#employerContriUnMaryChild").val();
                if ($('#spChild').prop("checked")) {
                    isSpChildCheck = 1;
                }

                if ($('#unMaryChild').prop("checked")) {
                    isunMaryChildCheck = 1;
                }
                if ($('#marital_status').prop("checked")) {
                    ismaritalStatus = 1;
                    form.append("ismaritalStatus", 1);
                    form.append("singleStatus_si", $("#singleStatus_si").val());
                    form.append("singleStatus_pre", $("#singleStatus_pre").val());
                    form.append("marriedStatus_si", $("#marriedStatus_si").val());
                    form.append("marriedStatus_pre", $("#marriedStatus_pre").val());
                }

                if ($('#unMaryChildSon').prop("checked")) {
                    isSonCheck = 1;
                }
                if ($('#unMaryChildDaughter').prop("checked")) {
                    isDaughterCheck = 1;
                }

                form.append("isDaughterCheck", isDaughterCheck);
                form.append("isSonCheck", isSonCheck);
                form.append("isSpChildCheck", isSpChildCheck);
                form.append("employeeContriSpChild", employeeContriSpChild);
                form.append("employerContriSpChild", employerContriSpChild);
                form.append("isunMaryChildCheck", isunMaryChildCheck);
                form.append("employeeContriUnMaryChild", employeeContriUnMaryChild);
                form.append("employerContriUnMaryChild", employerContriUnMaryChild);
                form.append("twins_child_limit", twins_child_limit);
                form.append("parent_cross_selection", parent_cross_selection);
                var ageLimit = $("#ageLimitDiv input[type=text]");
                var arr = [];
                form.append("ageLength", ageLimit.length);
                for (i = 0; i < ageLimit.length; ++i) {

                    if (!arr[ageLimit[i].getAttribute("data-id")]) {
                        arr[ageLimit[i].getAttribute("data-id")] = {};
                        arr[ageLimit[i].getAttribute("data-id")].min = ageLimit[i].value;
                    } else {
                        arr[ageLimit[i].getAttribute("data-id")].max = ageLimit[i].value;
                    }
                }


                arr[0].premium = Number($("#sumPremium").val());
                arr[0].premiumEmployeeContri = Number($("#premiumEmployeeContri").val());
                arr[0].premiumEmployerContri = Number($("#premiumEmployerContri").val());
                var premiumDivCalc = $("#premiumDivCalc input[type='text']");
                for (i = 0; i < premiumDivCalc.length; ++i) {
                    if (premiumDivCalc[i].getAttribute("data-name") == "premium") {

                        if ($("#premiumIndividual").prop("checked")) {
                            if (Number(premiumDivCalc[i].value) == 0) {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premium = Number($("#sumPremium").val());
                            } else {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premium = Number(premiumDivCalc[i].value)
                            }
                        } else {
                            arr[premiumDivCalc[i].getAttribute("data-id")].premium = 0;
                        }

                    } else if (premiumDivCalc[i].getAttribute("data-name") == "employee") {

                        if ($("#premiumIndividual").prop("checked")) {
                            if (Number(premiumDivCalc[i].value) == 0) {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployeeContri = Number($("#premiumEmployeeContri").val());
                            } else {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployeeContri = Number(premiumDivCalc[i].value)
                            }
                        } else {
                            arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployeeContri = 0;
                        }

                    } else if (premiumDivCalc[i].getAttribute("data-name") == "employer") {

                        if ($("#premiumIndividual").prop("checked")) {
                            if (Number(premiumDivCalc[i].value) == 0) {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployerContri = Number($("#premiumEmployerContri").val());
                            } else {
                                arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployerContri = Number(premiumDivCalc[i].value)
                            }
                        } else {
                            arr[premiumDivCalc[i].getAttribute("data-id")].premiumEmployerContri = 0;
                        }



                    }
                }

                form.append("ageLimit", JSON.stringify(arr));
                form.append("sumInsured", Number($("#sumInsured").val()));
                form.append("sumPremium", Number($("#sumPremium").val()));

                $.ajax({
                    type: "POST",
                    url: "/broker/update_policy",
                    data: form,
                    processData: false,
                    contentType: false,
                    cache: false,
                    async: false,
                    mimeType: "multipart/form-data",
                    success: function (data) {
                        data = JSON.parse(data);
                        //swal("", data.mgs);
                        swal({
                            title: "Success",
                            text: data.mgs,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true
                        })
                            .then((willDelete) => {
                                if (willDelete) {
                                    //window.location.reload();
                            window.location.replace("/broker/policy_detail");
                                }
                            })
                        return;
                    }
                });
            } else {
                if (document.getElementById("ageFile").files.length > 0) {
                    form.append("filename", document.getElementById("ageFile").files[0]);
                    form.append("fileUploadType", "byAge");
                } else if (document.getElementById("gradeFile").files.length > 0) {
                    form.append("filename", document.getElementById("gradeFile").files[0]);
                    form.append("fileUploadType", "byGrade");
                } else {
                    alert("Please add file to upload.");
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "/broker/update_policy",
                    data: form,
                    processData: false,
                    contentType: false,
                    cache: false,
                    async: false,
                    mimeType: "multipart/form-data",
                    success: function (data) {
                      window.location.replace("/broker/policy_detail");
                    }
                });
            }
        }
    });
    $("#spChild").on("change", function () {
        if (this.checked) {
            $("#spChildDiv").show();
        } else {
            $("#spChildDiv").hide();
            $("#employeeContriSpChild").val("0");
            $("#employerContriSpChild").val("0");
        }
    });
      $("input[name='premiumCheck']").on("change", function () {

      var subtype_policy = $("#companySubTypePolicy").val();
       
        if (this.id == "premiumIndividual") {
           
            if(subtype_policy == "grade" || subtype_policy == "designation"){
            $("#sum_isured_all").hide();
            $("#premium_all").hide();
        }
          $("#premiumDivCalc").show();
        } else {
             if(subtype_policy == "grade" || subtype_policy == "age" || subtype_policy == "designation"){
                $("#sum_isured_all").hide();
                $("#premium_all").hide();
            }
            else {
		        $("#sum_isured_all").show();
                $("#premium_all").show();
	     }
            $("#premiumDivCalc").hide();
            $("#adultPremiumDiv").hide();
            // $("#sum_isured_all").show();
            // $("#premium_all").show();
            $("#premiumDivCalc input[type='text']").each(function (e) {
              //  console.log(e);
            });
        }
    });
   

  $("input[name='premiumCheck']").trigger('change'); 
//       $("input[name='premiumCheck']").val($( "#premiumIndividual option:selected" ).val())
//       .trigger('change'); 
 $("#enroll_status").on("change", function () {
     
        if (this.checked) {
            $("#enrollment_status_chk_div").show();
             $("#enrollment_status_chk_div1").show();
            $("#enrolWindowStartDate").prop('required', true);
            $("#enrolWindowEndDate").prop('required', true);
            

        } else {
            $("#enrollment_status_chk_div").hide();
              $("#enrollment_status_chk_div1").hide();
            
           
        }
    });
   
    $("#enroll_status").trigger('change'); 

    $("#unMaryChild").on("change", function () {
        if (this.checked) {
            $("#unMaryChildDiv").show();
        } else {
            $("#unMaryChildDiv").hide();
            $("#employeeContriUnMaryChild").val("0");
            $("#employerContriUnMaryChild").val("0");
        }
    });
    $.validator.addMethod("policySubType", function (value, element) {
        if ($('#policySubType').val() != "1") {
            return true;
        }

        return element.value.length != 0;
    }, "This field is required.");
    $.validator.addMethod("applictionValidation", function (value, element) {
        if ($("#companySubTypePolicy").val() == "designation") {
            if ($("#appFor").val() == "designation") {
                return true;
            }
        }

        return element.value.length != 0;
    }, "This field is required.");


    $.validator.addMethod("sumInsuredValidate", function (value, element) {

        if ($("#companySubTypePolicy").val() != "designation") {
            return true;
        }

        return element.value.length != 0;
    }, "This field is required.");
    $.validator.addMethod("PrmiumValidate", function (value, element) {

        if ($("#companySubTypePolicy").val() != "designation") {
            return true;
        }
        $sumInsured = parseInt($("#sumInsured").val());
        $sumPremium = parseInt(value);
        if ($sumPremium >= $sumInsured) {
            return false;
        }
        return true;
    }, "Premium should be less than sum insured.");

    $.validator.addMethod("marriedSumInsuredValidate", function (value, element) {

        if ($("#companySubTypePolicy").val() != "designation") {
            return true;
        }

        return element.value.length != 0;
    }, "This field is required.");
    $.validator.addMethod("marriedPremiumValidate", function (value, element) {

        if ($("#companySubTypePolicy").val() != "designation") {
            return true;
        }
        $sumInsured = parseInt($("#marriedStatus_si").val());
        $sumPremium = parseInt(value);
        if ($sumPremium >= $sumInsured) {
            return false;
        }
        return true;
    }, "Premium should be less than sum insured.");
    $.validator.addMethod("singleSumInsuredValidate", function (value, element) {

        if ($("#companySubTypePolicy").val() != "designation") {
            return true;
        }

        return element.value.length != 0;
    }, "This field is required.");
    $.validator.addMethod("singlePremiumValidate", function (value, element) {

        if ($("#companySubTypePolicy").val() != "designation") {
            return true;
        }

        $sumInsured = parseInt($("#singleStatus_si").val());
        $sumPremium = parseInt(value);

        if ($sumPremium >= $sumInsured) {
            return false;
        }
        return true;
    }, "Premium should be less than sum insured.");


    $.validator.addMethod("family_cons_relValidate", function (value, element) {
        var family_con = $("#familyConstruct").val().split(",");

        var form_val = $("#forMediclaimDiv1").find("input[type=checkbox]");
        var adultCount = 0;
        var childCount = 0;

        if (family_con[1] > 1) {
            family_con[1] = 2;
        }

        for (var i = 0; i < form_val.length; i++) {
            if (form_val[i].checked) {
                if (form_val[i].id == "chk_2" || form_val[i].id == "chk_3") {
                    childCount++;
                } else {
                    adultCount++;
                }
            }
        }

        if (Number(adultCount) == Number(family_con[0]) && Number(childCount) == Number(family_con[1])) {
            return true;
        }
        return false;

    }, "Please Select checkbox as per family construct.");


    $.validator.addMethod("maritalStatusValidate", function (value, element) {
        if ($('#marital_status').prop("checked")) {
            if ($("#singleStatus_si").val() > '0' || $("#singleStatus_pre").val() > '0' || $("#marriedStatus_si").val() > '0' || $("#marriedStatus_pre").val() > '0') {
                return true;
            }
            return false;
        }

    }, "This field is required.");
    /*$.validator.addMethod("singleStatus_si", function (value, element) {
     if ($('#policySubType').val() == 2) {
     if($('#sum_insured_type').val() == 'individual' || $('#sum_insured_type').val() == 'familyIndividual' || $('#sum_insured_type').val() == 'familyGroup'){
     if ($("#singleStatus_si").val() > '0' || $("#singleStatus_pre").val() > '0' ||$("#marriedStatus_si").val() > '0' || $("#marriedStatus_pre").val() > '0' ) {
     return true;
     }
     else{
     return false;
     }
     }
     }
     }, "This field is required."); */


    $.validator.addMethod("cd_balance_thresValidate", function (value, element) {
        if ($("#cd_balance_thres").val() > '0' || $("#premium_cd_paid").val() > '0') {
            return true;
        } else {
            return false;
        }
    }, "The CD Balance threshold should be greater than 0");
    $.validator.addMethod("premium_cd_paidValidate", function (value, element) {
        if ($("#premium_cd_paid").val() > '0') {
            return true;
        } else {
            return false;
        }
    }, "The Premium paid balance should be greater than 0");

    $.validator.addMethod('account_emailValidate', function (value, element, param) {

        if (value.length == 0) {
            return false;
        }
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        return reg.test(value); // Compare with regular expression
    }, 'Please enter a valid email address.');

    $.validator.addMethod('valid_mobile', function (value, element, param) {
        var re = new RegExp('^[6-9][0-9]{9}$');
        return this.optional(element) || re.test(value); // Compare with regular expression
    }, 'Enter a valid 10 digit mobile number');



    $.validator.addMethod("no_timesValidate", function (value, element) {

        if ($('#policySubType').val() != 1) {
            if ($("#companySubTypePolicy").val() == "designation") {
                if ($("#appFor").val() == "allEmployee") {
                    if (value == '') {
                        return false;
                    }

                }
            }
        }
        return true;

        //        return element.value.length != 0;
    }, "This field is required.");
    $.validator.addMethod("noTimesValidate", function (value, element) {
        if ($('#policySubType').val() != 1) {
            if ($("#companySubTypePolicy").val() == "designation") {
                if ($("#appFor").val() == "designation") {
                    if (value == '') {
                        return false;
                    }

                }
            }
        }
        return true;
    }, "This field is required.");


    /*$.validator.addMethod("no_timesValidate", function (value, element) {
     if($("#policySubType").val() != 3){
     return true;
     }
     
     if($("#appFor").val() != "designation"){
     return true;
     }
     if($("#policySubType").val() == 3) 
     {
     if($("#appFor").val() == "designation"){
     if($("#no_times").val() > '0')
     {
     return true;
     }
     else {
     return false;
     }
     }
     }
     
     }, "no of timese should be greater than 0"); */
    $.validator.addMethod("sumPremiumValidate", function (value, element) {

        if ($("#companySubTypePolicy").val() != "designation") {
            return true;
        }

        if ($('#policySubType').val() != "1") {
            return true;
        }

        return element.value.length != 0;
    }, "This field is required.");

    $.validator.addMethod("specialChildContri", function (value, element) {

        if ($('#policySubType').val() != "1") {
            return true;
        }

        if ($("#spChild").prop("checked")) {
            var arr = $(element).closest(".row").find("input[type='text']");
            if ((Number(arr[0].value.trim()) + Number(arr[1].value)) == 100) {
                $(arr[0]).next().css("display", "none");
                $(arr[1]).next().css("display", "none");
                return true;
            } else
                return false;
        }

        return true;
    }, "The Employee & Employer contribution should be 100%.");


    //    $.validator.addMethod("specialChildContri", function (value, element) {
    //        if ($('#policySubType').val() != "1") {
    //            return true;
    //        }
    //
    //        if ($("#spChild").prop("checked"))
    //            return (Number($('#employeeContriSpChild').val()) + Number($('#employerContriSpChild').val())) == 100;
    //        else
    //            return true;
    //    }, "The Employee & Employer contribution should be 100%.");
    $.validator.addMethod("twinsChildLimitCheck", function (value, element) {
        if ($("#familyConstruct").val().split(",")[1] > 0) {
            if ($("#twins_child_limit").val() < 1) {
                return false;
            }
        }

        return true;
    }, "Invalid Twin Child Limit.");
    $.validator.addMethod("employeeContriSpChildContri", function (value, element) {
        if ($('#policySubType').val() != "1") {
            return true;
        }

        if ($("#unMaryChild").prop("checked")) {
            var arr = $(element).closest(".row").find("input[type='text']");
            if ((Number(arr[0].value.trim()) + Number(arr[1].value)) == 100) {
                $(arr[0]).next().css("display", "none");
                $(arr[1]).next().css("display", "none");
                return true;
            } else
                return false;
        }

        return true;

    }, "The Employee & Employer contribution should be 100%.");
    $.validator.addMethod("premiumEmployeeContrivalidation", function (value, element) {
        if ($('#policySubType').val() != "1") {
            return true;
        }

        if ($("#premiumAll").prop("checked")) {
            var arr = $(element).closest(".row").find("input[type='text']");
            if ((Number(arr[0].value.trim()) + Number(arr[1].value)) == 100) {
                $(arr[0]).next().css("display", "none");
                $(arr[1]).next().css("display", "none");
                return true;
            } else
                return false;
        }
        if ($("#premiumIndividual").prop("checked")) {
            var arr = $(element).closest(".row").find("input[type='text']");
            if ((Number(arr[0].value.trim()) + Number(arr[1].value)) == 100) {
                $(arr[0]).next().css("display", "none");
                $(arr[1]).next().css("display", "none");
                return true;
            } else
                return false;
        }


        return true;

    }, "The Employee & Employer contribution should be 100%.");

    $.validator.addMethod("premiumfEmployeeContrivalidation", function (value, element) {
        if ($('#policySubType').val() != "1") {
            return true;
        }
        if ($("#premiumIndividual").prop("checked")) {
            var arr = $(element).closest(".row").find("input[type='text']");
            console.log(arr);
            if ((Number(arr[1].value.trim()) + Number(arr[2].value)) == 100) {
                $(arr[0]).next().css("display", "none");
                $(arr[1]).next().css("display", "none");
                return true;
            } else
                return false;
        }


        return true;

    }, "The Employee & Employer contribution should be 100%.");



    //     $.validator.addMethod("employeeContriValidate", function (value, element) {
    //        if ($('#policySubType').val() != "1") {
    //            return true;
    //        }
    //      
    //        if($("input[name='premiumCheck']:checked"))
    //            return (Number($('#premiumEmployeeContri').val()) + Number($('#premiumEmployerContri').val())) == 100;
    //        else
    //            return true;
    //    }, "The Employee & Employer contribution should be 100%.");



    //     $.validator.addMethod("employeeContriValidate", function (value, element) {
    //        if ($('#policySubType').val() != "1") {
    //            return true;
    //        }
    //      
    //        if($("input[name='premiumCheck']:checked"))
    //            return (Number($('#premiumEmployeeContri').val()) + Number($('#premiumEmployerContri').val())) == 100;
    //        else
    //            return true;
    //    }, "The Employee & Employer contribution should be 100%.");

    $.validator.addMethod("brokerValidate", function (value, element) {
        var s = $('#brokerPer').val();
        s = s.replace(/^0+/, '');
        return Number(s) <= 17 && Number(s) > 0;

        return false;

    }, "The Broker Percentage should be 17%.");


    $.validator.addMethod("validateAgeLimit", function (value, element) {

        /*if ($('#policySubType').val() != "1") {
         return true;
         }*/

        var arr = $(element).closest(".row").find("input[type='text']");

        if (arr[0].value.trim().length > 2 || arr[1].value.trim().length > 2) {
            if (Number(arr[1].value) > 100) {
                return false;
            }
        }

        if (Number(arr[0].value) < Number(arr[1].value)) {
            $(arr[0]).next().css("display", "none");
            $(arr[1]).next().css("display", "none");
            return true;
        }

        return false;
        // return Number(arr[0].value) < Number(arr[1].value);

    }, "Invalid Min Max Age Limit");

    $.validator.addMethod("checkDate",
        function () {

            var startDate = $('#policyStartDate').val().split("-");
            var endDate = $('#policyEndDate').val().split("-");
            startDate = new Date(Number(startDate[2]), Number(startDate[1]) - 1, Number(startDate[0]));
            endDate = new Date(Number(endDate[2]), Number(endDate[1]) - 1, Number(endDate[0]));
            return startDate.getTime() < endDate.getTime();
        }, "The field is required");

    $.validator.addMethod("checkEnrollDate",
        function () {

            var startDate = $('#enrolWindowStartDate').val().split("-");
            var endDate = $('#enrolWindowEndDate').val().split("-");
            startDate = new Date(Number(startDate[2]), Number(startDate[1]) - 1, Number(startDate[0]));
            endDate = new Date(Number(endDate[2]), Number(endDate[1]) - 1, Number(endDate[0]));
            return startDate.getTime() < endDate.getTime();
        }, "The field is required");
        
       

});
function enableDisableAge(type, id) {
    if (type == 0) {
        $("#" + id + "MinAge").attr("disabled", "disabled");
        $("#" + id + "MaxAge").attr("disabled", "disabled");
        $("#" + id + "MinAge").val("0");
        $("#" + id + "MaxAge").val("0");
    } else {
        $("#" + id + "MinAge").removeAttr("disabled");
        $("#" + id + "MaxAge").removeAttr("disabled");
        $("#" + id + "MinAge").val("0");
        $("#" + id + "MaxAge").val("0");
    }
}

$("#brokerPer").keyup(function () {

    var z = $("#brokerPer").val();
    if (z.length > 2) {
        z = z.replace(/^0+/, ''); $("#brokerPer").val(z);
    }

});
$('input[type="text"]').keyup(function (e) {
    var id = e.target.id;
    if (id == "brokerPer") {

        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {

            $th.val($th.val().replace(/([^\d]*)(\d*(\.\d{0,2})?)(.*)/, '$2'));
        }
        return;
    }
});

$("#serviceTax").keyup(function () {

    var z = $("#serviceTax").val();
    if (z.length > 2) {
        z = z.replace(/^0+/, ''); $("#serviceTax").val(z);
    }

});

$("#tax").keyup(function () {

    var z = $("#tax").val();
    if (z.length > 2) {
        z = z.replace(/^0+/, ''); $("#tax").val(z);
    }

});

$('input[type="text"]').keyup(function (e) {

    var id = e.target.id;
    if (id == "selfMinAge" || id == "selfMaxAge" || id == "spouseMinAge" || id == "spouseMaxAge" || id == "sonMinAge" || id == "sonMaxAge" || id == "daughterMinAge" || id == "daughterMaxAge" || id == "fatherMinAge" || id == "fatherMaxAge" || id == "motherMinAge" || id == "motherMaxAge" || id == "fatherInLawMinAge" || id == "fatherInLawMaxAge" || id == "motherInLawMinAge" || id == "motherInLawMaxAge" || id == "sumInsured" || id == "sumPremium" || id == "premiumEmployeeContri" || id == "premiumEmployerContri" || id == "spousePremium" || id == "spousePremiumEmployeeContri" || id == "spousePremiumEmployerContri" || id == "sonPremium" || id == "sonPremium" || id == "sonPremiumEmployeeContri" || id == "sonPremiumEmployerContri" || id == "daughterPremium" || id == "daughterPremiumEmployeeContri" || id == "daughterPremiumEmployerContri" || id == "motherPremium" || id == "motherPremiumEmployeeContri" || id == "motherPremiumEmployerContri" || id == "fatherPremium" || id == "fatherPremiumEmployeeContri" || id == "fatherPremiumEmployerContri" || id == "motherinlawPremium" || id == "motherinlawPremiumEmployeeContri" || id == "motherinlawPremiumEmployerContri" || id == "fatherinlawPremium" || id == "fatherinlawPremiumEmployeeContri" || id == "fatherinlawPremiumEmployerContri" || id == "twins_child_limit" || id == "employeeContriSpChild" || id == "employerContriSpChild" || id == "employeeContriUnMaryChild" || id == "employerContriUnMaryChild" || id == "twins_child_limit" || id == "singleStatus_si" || id == "singleStatus_pre" || id == "marriedStatus_pre" || id == "marriedStatus_si") {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    }
    return;
});


// for new approval

$.ajax({
    type: "POST",
    url: "/broker/get_approval_data",
    async: false,
    success: function (e) {
        e = JSON.parse(e);
        if (e.approval_data) {
            var approval = e.approval_data;
            $("#approval").empty();
            $("#approval").html("<option value=''>Select</option>");

            approval.forEach(function (e) {
                $("#approval").append("<option value='" + e.emp_code + "'>" + e.emp_firstname + " " + e.emp_lastname + "</option>");
            });


        }
    },
    error: function () {

    }
});

