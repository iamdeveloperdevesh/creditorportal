var count_subtype = 0;
var random_data = Math.random();
function get_start(){
$('document').ready(function () {
	
    var companyName = [];
    var companyId = [];
    var designationName = [];
    var designationId = [];
var obj = {};var output = [];
var obj1 = {};
    $.ajax({
        type: "POST",
        url: "/employee/getPolicyCreationAutoCompletes",
        async: false,
        success: function (e) {
            // debugger;
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
        // showButtonPanel: true,
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

   
	

	
	
	
	
	

    //  for family construct
    $(document).on('change', '#familyConstruct', function () {
        var e;

        $.ajax({
            type: "POST",
            url: "/employee/get_family_relation",
            async: false,
            success: function (res) {
                e = JSON.parse(res);
                console.log(e);

            },
            error: function () {

            }
        });

        var z = $(this).val();
        var q = z.split(",");
      
        if (q[0] == 1 && q[1] == 0) {
            $("#family_construct_relations ul").empty();
            for (var i = 0; i <= e.length; i++) {

                if (e[i].fr_id == 0) {
                    $("#family_construct_relations ul").append("<li><input id='chk_" + e[i].fr_id + "' type='checkbox' checked name='family_cons_rel' value='" + e[i].fr_id + "' /><label class='lbl_chk' for='chk_" + e[i].fr_id + "'>" + e[i].fr_name + "</label></li>");
                }
            }
        } else if (q[0] == 2 && q[1] == 0) {
            $("#family_construct_relations ul").empty();
            for (var i = 0; i <= e.length; i++) {

                if (e[i].fr_id == 0 || e[i].fr_id == 1) {
                    $("#family_construct_relations ul").append("<li><label for='chk_" + e[i].fr_id + "'>" + e[i].fr_name + "</label><input checked id='chk_" + e[i].fr_id + "' type='checkbox' name='family_cons_rel' value='" + e[i].fr_id + "' /></li>");
                }
            }
        } 
        
        else if ((q[0] == 2 && q[1] == 2) || (q[0] == 2 && q[1] == 4) || (q[0] == 2 && q[1] == 3)) {
            $("#family_construct_relations ul").empty();
            for (var i = 0; i <= e.length; i++) {
                $("#family_construct_relations ul").append("<li><input id='chk_" + e[i].fr_id + "' type='checkbox' checked name='family_cons_rel' value='" + e[i].fr_id + "' /><label  class='lbl_chk' for='chk_" + e[i].fr_id + "'>" + e[i].fr_name + "</label></li>");
            }
        }
        else if(q[0] == 3){
            
            $("#family_construct_relations ul").empty();
            for (var i = 0; i <= e.length; i++) {
                 if (e[i].fr_id == 4 || e[i].fr_id == 5) {
                $("#family_construct_relations ul").append("<li><label for='chk_" + e[i].fr_id + "'>" + e[i].fr_name + "</label><input checked id='chk_" + e[i].fr_id + "' type='checkbox' name='family_cons_rel' value='" + e[i].fr_id + "' /></li>");
            }
        }
        }
        
         else if(q[0] == 4 && q[1] == 0){
            
            $("#family_construct_relations ul").empty();
            for (var i = 0; i <= e.length; i++) {
                 if (e[i].fr_id == 4 || e[i].fr_id == 5 || e[i].fr_id == 6 || e[i].fr_id == 7) {
                $("#family_construct_relations ul").append("<li><label for='chk_" + e[i].fr_id + "'>" + e[i].fr_name + "</label><input checked id='chk_" + e[i].fr_id + "' type='checkbox' name='family_cons_rel' value='" + e[i].fr_id + "' /></li>");
            }
        }
        }
        
        else {
            $("#family_construct_relations ul").empty();
            for (var i = 0; i <= e.length; i++) {
                $("#family_construct_relations ul").append("<li><label for='chk_" + e[i].fr_id + "'>" + e[i].fr_name + "</label><input checked id='chk_" + e[i].fr_id + "' type='checkbox' name='family_cons_rel' value='" + e[i].fr_id + "' /></li>");
            }
        }
    });





    /* $.post("/broker/get_policy_creation_det", {
     $('#crsf_name').val():$('#crsf_value').val()
     }, function (e) {
     
     
     }); */
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

	// for flate
	
	$('body').on("keyup", "input[name=sum_insured_opt11]", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });
    $('body').on("keyup", "input[name=sum_insured_opt1]", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });
	
	 $('body').on("keyup", "input[name=premium_opt]", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });
	 $('body').on("keyup", "input[name=tax_opt]", function (e) {
        var $th = $(this);

        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9]/g, function (str) {
                return '';
            }));
        }
        return;
    });
	 $('body').on("keyup", "input[name=premiumWithTax_opt]", function (e) {
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
            $(".marital_status_div1").hide();
        } else if ($('#policySubType').val() == "4") {
            $(".marital_status_div1").hide();
            $("#marital_status_chk").hide();
            $("#premium_all").hide();
            $("#sum_isured_all").hide();
            $("#forMediclaimDiv").show();
            $("#sumPremiumInsuredDiv").show();
            $("#fileUploadDiv").hide();
        } else if ($('#policySubType').val() == "5") {
            $(".marital_status_div1").hide();
            $("#marital_status_chk").hide();
            $("#premium_all").hide();
            $("#sum_isured_all").hide();
            $("#forMediclaimDiv").show();
            $("#sumPremiumInsuredDiv").show();
            $("#fileUploadDiv").hide();
            //$('#companySubTypePolicy').empty();
            //$('#companySubTypePolicy').append('<option value=""> Select Sum Insured Type </option>' + ' <option value="designation">Designation Wise</option>');
        } else if ($('#policySubType').val() == "6") {
            $(".marital_status_div1").hide();
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
              $(".marital_status_div1").hide();
            $(".gpa_pr").css("display", "block");
           // $('#companySubTypePolicy').empty();
            //$('#companySubTypePolicy').append('<option value=""> Select Sum Insured Type  </option>' + ' <option value="designation">Designation Wise</option>' + ' <option value="grade">Grade Wise</option>');

            // if($("#masterInsurance").val() == 24)
            //$('#companySubTypePolicy').append('<option value="grade">Grade Wise</option>');

            $("#forMediclaimDiv").show();
            $("#sumPremiumInsuredDiv").show();
            $("#fileUploadDiv").hide();
            $("#sum_isured_all").hide();
            $("#no_times_all_des_div").hide();

        } else {
           $(".marital_status_div1").hide();
            $("#forMediclaimDiv").hide();
            $("#sumPremiumInsuredDiv").hide();
            $("#fileUploadDiv").show();
            $("#gpa_pr").hide();
            //$('#companySubTypePolicy').empty();
           // $('#companySubTypePolicy').append('<option value="">  Select Sum Insured Type  </option>' + '<option value="designation">Designation Wise</option>' + '<option value="age">Age Wise</option>' + '<option value="grade">Grade Wise</option>');
        }
    });
    $("#appFor").change(function () {

        if ($("#policySubType").val() == '2' || $("#policySubType").val() == '3') {
            if ($("#appFor").val() == "designation") {
                $("#no_times_all_des_div").hide();
                $("#premium_all").hide();

            } else {
                $("#no_times_all_des_div").hide();
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
    $("#sum_insured_type").change(function () {
       
        if ($("#policySubType").val() == 4 || $("#policySubType").val() == 5 || $("#policySubType").val() == 6) {
            if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                $("#tableSiTopUp").hide();
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
            $("#premiumMiliDiv").hide();
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


     $("#enroll_status").on("change", function () {
        if (this.checked) {
            $("#enrollment_status_chk_div").show();
            $("#enrolWindowStartDate").prop('required', true);
            $("#enrolWindowEndDate").prop('required', true);
            

        } else {
            $("#enrollment_status_chk_div").hide();
           
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
    var bo_parent_chk = 0;  
    var si_parent_chk = 0;  
    $("#single_parent").change(function() {
      if (this.checked) {
         si_parent_chk = 1;
       $("#si_parent").css("display","block");  
     
      }
    });
     $("#both_parent").change(function() {
         
      if (this.checked) {
        bo_parent_chk = 1;
       $("#bo_parent").css("display","block");  
     
      }
    });
    
    
     $("#btn_add_si_flat").click(function () {
        var cols = "";  
        var newRow = $("<tr>");
        debugger;
        cols += '<td style="text-align: right;" class="row mt-4"> <div class="col-md-3"><label style="font-size: 13px; float: left;">Enter Sum Insured</label><input type="text" placeholder="Sum Insured" class="form-control" name="sum_insured_opt1" autocomplete="off"></div><div class="col-md-3"><label  style="font-size: 13px; float: left;">Enter Premium</label><input type="text" placeholder="premium" class="form-control" name="premium_opt" autocomplete="off"> </div><div class="col-md-3"><label  style="font-size: 13px; float: left;">Enter Tax</label>  <input type="text" placeholder="Tax" class="form-control" name="tax_opt" autocomplete="off"> </div><div class="col-md-3"><label  style="font-size: 13px; float: left;">Enter Premiium With Tax</label> <input type="text" placeholder="Premium Tax" class="form-control" name="premiumWithTax_opt" autocomplete="off"> </div></td> ';
       
        cols += '<td name="del_btn_opt" class="del_btn_opt" style="width:15%;text-align: right;" ><a><i style="margin-top: 15px;" class="fa fa-trash" aria-hidden="true"></i></a></td>';
        newRow.append(cols);
        $("#add_si_tbody").append(newRow);
    });
	
	 $("#btn_add_si_flat_other").click(function () {
		debugger;
        var cols = "";  
        var newRows = $("<tr>");
        
        cols += '<td style="text-align: right;"> <label style="margin-right: 64px;">Enter Sum Insured</label><input type="text" placeholder="Sum Insured" class="form-control" name="sum_insured_opt11" autocomplete="off"></td>';
       
        cols += '<td name="del_btn_opt" class="del_btn_opt" style="width:15%;text-align: right;"><a><i style="margin-top: 15px;" class="fa fa-trash" aria-hidden="true"></i></a></td>';
        newRows.append(cols);
        $("#add_si_tbody_other").append(newRows);
    });
     
    $("#btn_add_si").click(function () {
        var cols = "";  
        var newRow = $("<tr>");
        
        cols += '<td style="text-align: right;"><input style="margin-top: 15px;" type="text" placeholder="Sum Insured" class="form-control" name="sum_insured_opt" autocomplete="off"> </td>';
       
       if (si_parent_chk == 1) {
               
        cols += '<td style="text-align: right;"><label class="si_parent"style="margin-right: 64px;">Enter Premium<input type="text" placeholder="premium" class="form-control" name="premium_opt" autocomplete="off"></label></td>';
       
                } 
                 if (bo_parent_chk == 1) {
            
              cols += '<td style="text-align: right;"><label class="bo_parent" style="margin-right: 64px;">Enter Premium<input type="text" placeholder="premium" class="form-control" name="premium_opts" autocomplete="off"></label></td>';
       }
        
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
           $("#spouseDiv").show();
		    $("#selfDiv").show();
            $("#spouseDiv").find('[type=checkbox]');
            $("#spousePremiumDiv").show();
            $("#forMediclaimDiv1").show();
            $("#chk_1").prop('required', true);
            $("#chk_0").prop('required', true);
            $("#parent_cross_selection_div").hide();
        } 
        else if (adultCount == 1) {
           $("#selfDiv").show();
            $("#premiumAll").prop("checked", true);
            $("#premiumIndividual").parent().parent().css("display", "none");
            $("#premiumDivCalc").hide();
            $("#premiumDivCalc input[type='text']").val("");
            $("#forMediclaimDiv1").show();
        } 
        else if(adultCount == 3){
             $("#forMediclaimDiv1").show();
             $("#adultDiv").show();
             $("#selfDiv").hide();
             $("#adultPremiumDiv").show();
        }
        else if(adultCount == 4 && childCount == 0){
             $("#forMediclaimDiv1").show();
             $("#adultDiv").show();
             $("#selfDiv").hide();
             $(".parents_inlow").css("display", "block");
             $(".parentsInlowpre").css("display", "block");
             if($("#premiumAll").prop("checked", true)){
                  $("#premium_emp").css("display", "block");  
              }
             $("#adultPremiumDiv").show();
            
        }
        else if (adultCount == 4) {
            $("#selfDiv").show();
            $("#spouseDiv").show();
            $("#spousePremiumDiv").show();
            $("#adultDiv").show();
            $("#adultPremiumDiv").show();
            $("#forMediclaimDiv1").show();
            $("#parent_cross_selection_div").show();
        }

        if (childCount > 0) {
            $("#childDiv").show();
            $("#childPremiumDiv").show();
        }
    });
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
        $("#premiumEmployeeContri").val("0");
        $("#premiumEmployerContri").val("0");
        $("#fileUploadAgeDiv").hide();
        $("#fileUploadGradeDiv").hide();
         $("#fileUploadAgeDiv1").hide();
        $("#fileUploadGradeDiv1").hide();
        $("#tableMediclaim").hide();
        var add_tbody = $("#add_tbody > tr");
        $("#add_tbody > tr")[0].querySelector("input[type='text']").value = "";
        for (i = 1; i < add_tbody.length; ++i) {
            add_tbody[i].remove()
        }
    }
 
    $("#companySubTypePolicy").on("change", function () {
        var str = $(this).val();
        hideSubTypePolicy();

      if (str == "designation") {
        $("#tableSiflat_other").css("display","none");
            $("#prem_div_new").css("display", "block");     
            $("#fileUploaddesignationDiv").show();
            $("#sumInsuredPremiumDiv").show();
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");
          
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
            if($("#familyConstruct").val() == "3,0" || $("#familyConstruct").val() == "4,0") {
            $("#premiumAll")[0].nextSibling.nodeValue = "Fixed Premium";  
           }
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Additional Premium";
            $("#premiumAll").parent().parent().css("display", "block");
			$("#fileUploadfamilyageDiv").css("display","none");
        } else if (str == "age") {
            $("#tableSiflat_other").css("display","none");
			$("#prem_div_new").css("display", "block");
            $("#fileUploaddesignationDiv").hide();
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");
            $("#fileUploadAgeDiv").show();
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Additional Premium";
         
            $("#premiumAll").parent().parent().css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
			$("#fileUploadfamilyageDiv").css("display","none");
        } else if (str == "memberAge") {
		
            $("#tableSiflat_other").css("display","none");
			$("#fileUploadfamilyDiv").css("display","none");
			 $("#tableSiflat").css("display","none");
			$("#prem_div_new").css("display", "block");
            $("#fileUploaddesignationDiv").hide();
            $("*[data-name=premium]").parent().css("display", "none");
            $("*[data-name=premium]").addClass("ignore");
            $("#fileUploadAgeDiv").show();
            $("#premiumIndividual").prop("checked", true);
            $("#premiumDivCalc").show();
            //$("#premiumIndividual")[0].nextSibling.nodeValue = "Premium Contribution";
          
            $("#premiumAll").parent().parent().css("display", "none");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
			$("#fileUploadfamilyageDiv").css("display","none");
			

        } else if (str == "grade") {
            $("#tableSiflat_other").css("display","none");
			$("#prem_div_new").css("display", "block");
            $("#fileUploaddesignationDiv").hide();
            $("#fileUploadGradeDiv").show();
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");

             if($("#familyConstruct").val() == "3,0" || $("#familyConstruct").val() == "4,0") {
            $("#premiumAll")[0].nextSibling.nodeValue = "Fixed Premium";  
           }
           // $("#premiumIndividual")[0].nextSibling.nodeValue = "Additional Premium";
     
            $("#premiumAll").parent().parent().css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
           
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
			$("#fileUploadfamilyageDiv").css("display","none");

    }
    else if (str == "parent") {
           
            $("#tableSiTopUp").css("display","block");
            $("#tableSiflat_other").css("display","none");
            $("#sumInsuredPremiumDiv").show();
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
            if($("#familyConstruct").val() == "3,0" || $("#familyConstruct").val() == "4,0") {
            $("#premiumAll")[0].nextSibling.nodeValue = "Fixed Premium";  
			$("#prem_div_new").css("display", "none");
		$("#fileUploadfamilyDiv").css("display","none");
        }
		$("#fileUploadfamilyageDiv").css("display","none");

    } else if (str == "flate" ) {
           
            $("#tableSiflat").css("display","block");
            $("#sumInsuredPremiumDiv").show();
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
			//$("#prem_div_new").css("display", "none");
		    $("#fileUploadfamilyDiv").css("display","none");
	       $("#fileUploadfamilyageDiv").css("display","none");

    }
	else if(str == "family_construct"){
		$("#prem_div_new").css("display", "none");
		$("#fileUploadfamilyDiv").css("display","block");
         $("#tableSiflat").css("display","none");
         $("#tableSiflat_other").css("display","none");
		  $("#sumInsuredPremiumDiv").show();
		   $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
			 $("#fileUploadAgeDiv").hide();
			 $("#fileUploadfamilyageDiv").css("display","none");

	}
	else if(str == "family_construct_age"){
		$("#prem_div_new").css("display", "none");
		$("#fileUploadfamilyageDiv").css("display","block");
         $("#tableSiflat").css("display","none");
         $("#tableSiflat_other").css("display","none");
		  $("#sumInsuredPremiumDiv").show();
		   $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
			 $("#fileUploadAgeDiv").hide();
			 $("#fileUploadfamilyDiv").css("display","none");

	}
    });
    
    
    /*premium*/
        $("#companySubTypepremiumPolicy").on("change", function () {
        var str = $(this).val();
        
        hideSubTypePolicy();
     
        if (str == "designation1" ) {
			$("#prem_div_new").css("display", "block");
		    $("#fileUploadfamilyDiv").css("display","none");
		   if ($("#companySubTypePolicy").val() == "designation") {
       
            $("#fileUploaddesignationDiv").show();
            $("#sumInsuredPremiumDiv").show();
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");
          
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
            if($("#familyConstruct").val() == "3,0" || $("#familyConstruct").val() == "4,0") {
            $("#premiumAll")[0].nextSibling.nodeValue = "Fixed Premium";  
           }
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Additional Premium";
            $("#premiumAll").parent().parent().css("display", "block");
            $("#tableSiflat_other").hide();
        } else if ($("#companySubTypePolicy").val() == "age") {
            $("#fileUploaddesignationDiv").hide();
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");
            $("#fileUploadAgeDiv").show();
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Additional Premium";
         
            $("#premiumAll").parent().parent().css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
            $("#tableSiflat_other").hide();
        } else if ($("#companySubTypePolicy").val() == "memberAge") {
            $("#fileUploaddesignationDiv").hide();
            $("*[data-name=premium]").parent().css("display", "none");
            $("*[data-name=premium]").addClass("ignore");
            $("#fileUploadAgeDiv").show();
            $("#premiumIndividual").prop("checked", true);
            $("#premiumDivCalc").show();
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Premium Contribution";
          
            $("#premiumAll").parent().parent().css("display", "none");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
            $("#tableSiflat_other").hide();

        } else if ($("#companySubTypePolicy").val() == "grade") {
            $("#fileUploaddesignationDiv").hide();
            $("#fileUploadGradeDiv").show();
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");

             if($("#familyConstruct").val() == "3,0" || $("#familyConstruct").val() == "4,0") {
            $("#premiumAll")[0].nextSibling.nodeValue = "Fixed Premium";  
           }
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Additional Premium";
     
            $("#premiumAll").parent().parent().css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");

    }
		   else if($("#companySubTypePolicy").val() == "flate"){
			   $("#tableSiflat_other").show();
			    $("#tableSiflat").hide();
		   }
            $("#fileUploaddesignationDiv1").show();
           
        } else if (str == "age1") { 
			$("#prem_div_new").css("display", "block");
		  $("#fileUploadfamilyDiv").css("display","none");
			if ($("#companySubTypePolicy").val() == "designation") {
       
            $("#fileUploaddesignationDiv").show();
            $("#sumInsuredPremiumDiv").show();
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");
          
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
            if($("#familyConstruct").val() == "3,0" || $("#familyConstruct").val() == "4,0") {
            $("#premiumAll")[0].nextSibling.nodeValue = "Fixed Premium";  
           }
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Additional Premium";
            $("#premiumAll").parent().parent().css("display", "block");
            $("#tableSiflat_other").hide();
        } else if ($("#companySubTypePolicy").val() == "age") {
            $("#fileUploaddesignationDiv").hide();
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");
            $("#fileUploadAgeDiv").show();
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Additional Premium";
         
            $("#premiumAll").parent().parent().css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
            $("#tableSiflat_other").hide();
        } else if ($("#companySubTypePolicy").val() == "memberAge") {
            $("#fileUploaddesignationDiv").hide();
            $("*[data-name=premium]").parent().css("display", "none");
            $("*[data-name=premium]").addClass("ignore");
            $("#fileUploadAgeDiv").show();
            $("#premiumIndividual").prop("checked", true);
            $("#premiumDivCalc").show();
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Premium Contribution";
          
            $("#premiumAll").parent().parent().css("display", "none");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
            $("#tableSiflat_other").hide();

        } else if ($("#companySubTypePolicy").val() == "grade") {
            $("#fileUploaddesignationDiv").hide();
            $("#fileUploadGradeDiv").show();
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");

             if($("#familyConstruct").val() == "3,0" || $("#familyConstruct").val() == "4,0") {
            $("#premiumAll")[0].nextSibling.nodeValue = "Fixed Premium";  
           }
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Additional Premium";
     
            $("#premiumAll").parent().parent().css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
            $("#tableSiflat_other").hide();

    }
		   else if($("#companySubTypePolicy").val() == "flate"){
			   $("#tableSiflat_other").show();
			    $("#tableSiflat").hide();
		   }
            $("#fileUploaddesignationDiv1").hide();
          
            $("#fileUploadAgeDiv1").show();
          
        } else if (str == "memberAge1") {
			
			$("#prem_div_new").css("display", "block");
		   $("#fileUploadfamilyDiv").css("display","none");
			if ($("#companySubTypePolicy").val() == "designation") {
                $("#tableSiflat_other").hide();
            $("#fileUploaddesignationDiv").show();
            $("#sumInsuredPremiumDiv").show();
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");
          
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
            if($("#familyConstruct").val() == "3,0" || $("#familyConstruct").val() == "4,0") {
            $("#premiumAll")[0].nextSibling.nodeValue = "Fixed Premium";  
           }
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Additional Premium";
            $("#premiumAll").parent().parent().css("display", "block");
        } else if ($("#companySubTypePolicy").val() == "age") {
            $("#tableSiflat_other").hide();
            $("#fileUploaddesignationDiv").hide();
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");
            $("#fileUploadAgeDiv").show();
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Additional Premium";
         
            $("#premiumAll").parent().parent().css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
        } else if ($("#companySubTypePolicy").val() == "memberAge") {
            $("#tableSiflat_other").hide();
            $("#fileUploaddesignationDiv").hide();
            $("*[data-name=premium]").parent().css("display", "none");
            $("*[data-name=premium]").addClass("ignore");
            $("#fileUploadAgeDiv").show();
            $("#premiumIndividual").prop("checked", true);
            $("#premiumDivCalc").show();
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Premium Contribution";
          
            $("#premiumAll").parent().parent().css("display", "none");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");

        } else if ($("#companySubTypePolicy").val() == "grade") {
            $("#tableSiflat_other").hide();
            $("#fileUploaddesignationDiv").hide();
            $("#fileUploadGradeDiv").show();
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");

             if($("#familyConstruct").val() == "3,0" || $("#familyConstruct").val() == "4,0") {
            $("#premiumAll")[0].nextSibling.nodeValue = "Fixed Premium";  
           }
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Additional Premium";
     
            $("#premiumAll").parent().parent().css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");

    }
		   else if($("#companySubTypePolicy").val() == "flate"){
			   $("#tableSiflat_other").show();
			    $("#tableSiflat").hide();
		   }
            $("#fileUploaddesignationDiv1").hide();
           
            $("#fileUploadAgeDiv1").show();
           

        } else if (str == "grade1") {
			$("#prem_div_new").css("display", "block");
		$("#fileUploadfamilyDiv").css("display","none");
            $("#fileUploaddesignationDiv1").hide();
            $("#fileUploadGradeDiv1").show();
         if ($("#companySubTypePolicy").val() == "designation") {
            $("#tableSiflat_other").hide();  
            $("#fileUploaddesignationDiv").show();
            $("#sumInsuredPremiumDiv").show();
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");
          
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
            if($("#familyConstruct").val() == "3,0" || $("#familyConstruct").val() == "4,0") {
            $("#premiumAll")[0].nextSibling.nodeValue = "Fixed Premium";  
           }
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Additional Premium";
            $("#premiumAll").parent().parent().css("display", "block");
        } else if ($("#companySubTypePolicy").val() == "age") {
            $("#tableSiflat_other").hide();
            $("#fileUploaddesignationDiv").hide();
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");
            $("#fileUploadAgeDiv").show();
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Additional Premium";
         
            $("#premiumAll").parent().parent().css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");
        } else if ($("#companySubTypePolicy").val() == "memberAge") {
            $("#tableSiflat_other").hide();
            $("#fileUploaddesignationDiv").hide();
            $("*[data-name=premium]").parent().css("display", "none");
            $("*[data-name=premium]").addClass("ignore");
            $("#fileUploadAgeDiv").show();
            $("#premiumIndividual").prop("checked", true);
            $("#premiumDivCalc").show();
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Premium Contribution";
          
            $("#premiumAll").parent().parent().css("display", "none");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");

        } else if ($("#companySubTypePolicy").val() == "grade") {
            $("#fileUploaddesignationDiv").hide();
            $("#tableSiflat_other").hide();
            $("#fileUploadGradeDiv").show();
            $("*[data-name=premium]").parent().css("display", "block");
            $("*[data-name=premium]").removeClass("ignore");

             if($("#familyConstruct").val() == "3,0" || $("#familyConstruct").val() == "4,0") {
            $("#premiumAll")[0].nextSibling.nodeValue = "Fixed Premium";  
           }
            $("#premiumIndividual")[0].nextSibling.nodeValue = "Additional Premium";
     
            $("#premiumAll").parent().parent().css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").css("display", "block");
            $("#sumInsuredPremiumDiv").find("#sum_isured_all").css("display", "none");
            $("#sumInsuredPremiumDiv").find("#premium_all").css("display", "none");

    }
		   else if($("#companySubTypePolicy").val() == "flate"){
			   $("#tableSiflat_other").show();
			    $("#tableSiflat").hide();
		   }

    }
   
	
    });
	
	
    $("#familyConstruct").on("change", function () {
        var data = $(this).val();

        var data = $(this).val();
        if (data == "1,0") {
            $('#unMaryChildDiv1').hide();
            $('#spChildDiv1').hide();
           // $('#ageLimitDiv').show();
            $('#sum_insured_type').empty();
            $('#sum_insured_type').append('<option value=""> Select </option>' + ' <option value="individual"> Individual </option>');
        } else if (data == "2,0") {
           // $('#ageLimitDiv').show();
            $('#unMaryChildDiv1').hide();
            $('#spChildDiv1').hide();
            $('#sum_insured_type').empty();
            $('#sum_insured_type').append('<option value="">Select</option>' + '<option value="individual"> Individual </option>' + '<option value="familyIndividual">Family Cover (SI * No of lives)</option>' + '<option value="familyGroup">Family Cover</option>');
        } 
        else if (data == "3,0" || data == "4,0") {
            $('#unMaryChildDiv1').hide();
            $('#spChildDiv1').hide();
            $('#sum_insured_type').empty();
            $('#sum_insured_type').append('<option value="">Select</option>' + '<option value="individual"> Individual </option>' + '<option value="familyIndividual">Family Cover (SI * No of lives)</option>' + '<option value="familyGroup">Family Cover</option>');
            
             $('#companySubTypePolicy').empty();
            $('#companySubTypePolicy').append('<option value="">Select</option>' + '<option value="designation">Designation Wise</option>' + '<option value="memberAge">Member Age Wise</option>' + '<option value="grade">Grade Wise</option>' + '<option value="parent">Parent Wise</option>');
        } 
        else {
            $('#unMaryChildDiv1').hide();
            $('#spChildDiv1').hide();
            $('#sum_insured_type').empty();
            $('#sum_insured_type').append('<option value=""> Select </option>' + '<option value="individual"> Individual </option>' + '<option value="familyIndividual">Family Cover (SI * No of lives)</option>' + '<option value="familyGroup">Family Cover</option>');
        }

    });



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
                required: true
            },
            sonMaxAge: {
                required: true
            },
            daughterMinAge: {
                required: true
            },
            daughterMaxAge: {
                required: true
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
                //family_cons_relValidate: true
                required:true
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
            tpaFile: {
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

        },
        success: function (label, element) {
            label.parent().removeClass('error');
            label.remove();
           	
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
			var productName = $("#productName").val();
           
            if ($("#parent_cross_selection").prop("checked")) {
                parent_cross_selection = "Y";
            }

            if ($("#flex_allocate").prop("checked")) {
                flex_allocate = "Y";
            }

            if ($("#payroll_allocate").prop("checked")) {
                payroll_allocate = "Y";
            }
			 $("#companySubTypePolicy").removeAttr('disabled', 'disabled');
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
				
				var isMatoryStatus = 0;
		
               if ($('#mandatory_option').prop("checked")) {
                    isMatoryStatus = 1;
                }
				
				// siddhi
				var isComboStatus = 0;
		
               if ($('#Combo_option').prop("checked")) {
                    isComboStatus = 1;
                }
			   var product_parent_id = $("#product_parent_id").val();
			   
					
			var policy_type = $('#policySubType  :selected').val();
			var pdf_type = $('#pdf_type  :selected').val();
			
			
			
            form.append("master_policy_no", $("#master_policy_no").val().trim());
            form.append("EW_master_policy_no", $("#EW_master_policy_no").val().trim());
            form.append("HB_source_code", $("#HB_source_code").val().trim());
            form.append("HB_policy_type", $("#HB_policy_type").val().trim());
            form.append("plan_code", $("#plan_code").val().trim());
            form.append("HB_custid_concat_string", $("#HB_custid_concat_string").val().trim());
            form.append("api_url", $("#api_url").val().trim());
            form.append("click_pss_url", $("#click_pss_url").val().trim());
            form.append("payu_info_url", $("#payu_info_url").val().trim());
            form.append("imd_refer_product_code", $("#imd_refer_product_code").val().trim());


            // if (document.getElementById("GroupCodeFile").files.length > 0) {
            //     form.append("GroupCodeFile", document.getElementById("GroupCodeFile").files[0]);
            // } else {
            //     alert("Please add group code file to upload.");
            //     return;
            // }

            
            form.append("policyNo", $("#policyNo").val());
            form.append("isEnrollStatus", isEnrollStatus);
			form.append("isMatoryStatus", isMatoryStatus);
			//siddhi
			form.append("isComboStatus", isComboStatus);
			form.append("product_parent_id", product_parent_id);
			
			form.append("pdf_type", pdf_type);
            form.append("policyType", $("#policyType").val());
            form.append("policySubType", $("#policySubType").val());
            form.append("masterInsurance", $("#masterInsurance").val());
            form.append("comanyName", $("#comanyName").val());
            form.append("salesManager", $("#salesManager").val());
            form.append("policyStartDate", $("#policyStartDate").val());
            form.append("policyEndDate", $("#policyEndDate").val());
            form.append("familyConstruct", $("#familyConstruct").val());
            form.append("brokerPer", $("#brokerPer").val());
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
			form.append("payment_mode", $("#payment_mode").val());
			form.append("proposal_approval", $("#proposal_approval").val());
			form.append("customer_search", $("#customer_search").val());
			form.append("customer_search_field", $("#customer_search_fi").val());
			form.append("product_name", $('#policySubType option:selected').data('id')); 
           
                
              
            //for aditya birla insurance
            if($("#masterInsurance").val() == 201 && $("#policyType").val() == 1 && $("#policySubType").val() == 2) {
                var premiumCalType = 0;
                if($("#premiumMili").prop("checked")) {
                    premiumCalType = 1;

                    form.append("filename", document.getElementById("perMiliFile").files[0]);

                }
                form.append("premiumCalType", premiumCalType);
            }
			
			  var fl_suminsure =[];
			  var test = $( "#policySubType option:selected" ).text();	
				var arrd = [];
			     var d ;

            if ($("#policySubType").val() == "1") {
                  // alert();return;
                var errorCount = 0;
                var additionalPremium = 0;
                var twins_child_limit = 0;
               
                var si_parr = [];
                var si_parr1 = [];
                var si_earr = [];
                var si_eplrarr = [];
                var si_id = [];
                var si_parents = 0;
                var bi_parents = 0;
               var tax_Arr = [];
                var premium_taxArr = [];
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
                var str = $("#companySubTypePolicy").val();
                var strpremium = $("#companySubTypepremiumPolicy").val();
                
                form.append("companySubTypePolicy", str);
                form.append("companySubTypepremiumPolicy", strpremium);

                // alert(str);

                if (str == "designation") {

                    form.append("appFor", $("#appFor").val());
                    if ($("#appFor").val() == "designation") {
                       // alert()
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

                    form.append("appFor", "");
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
				else if (str == "family_construct") {
					
                    form.append("appFor", "");
                    if (document.getElementById("familyConstructFile").files.length > 0) {
                        form.append("filename", document.getElementById("familyConstructFile").files[0]);
                        form.append("fileUploadType", "byFamilyConstruct");
                       
                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                }
				else if (str == "family_construct_age") {
                    form.append("appFor", "");
                    if (document.getElementById("agefamilyConstructFile").files.length > 0) {
                        form.append("filename", document.getElementById("agefamilyConstructFile").files[0]);
                        form.append("fileUploadType", "byFamilyAgeConstruct");
                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                }
               else if(str =="parent"){
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt");
                    var premium_opt = document.getElementsByName("premium_opt");
                    var premium_opts = document.getElementsByName("premium_opts");
                   
                    for (var i = 0; i < sum_insured_option.length; i++) {

                        si_id.push(sum_insured_option[i].value);
                        si_parr.push(premium_opt[i].value);
                        si_parr1.push(premium_opts[i].value);

                    }

                } 
               if($("#single_parent").prop("checked", true)){
                   si_parents = 1;
               } if($("#both_parent").prop("checked", true)){
                   bi_parents = 1;
               } 
                form.append("bo_parents", bi_parents);
                form.append("si_parents", si_parents);
                form.append("si_id", JSON.stringify(si_id));
                form.append("si_parr", JSON.stringify(si_parr));
                form.append("si_parr1", JSON.stringify(si_parr1));
             }
               else if(str == "flate"){
				if(strpremium == "memberAge1" || strpremium == "designation1"){
					
						
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt11");
                  
                    for (var i = 0; i < sum_insured_option.length; i++) {
                        fl_suminsure.push(sum_insured_option[i].value);
                    }
                }
                form.append("si_id", JSON.stringify(fl_suminsure));
            }
            		

			else {
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt1");
                    var premium_opt = document.getElementsByName("premium_opt");
                    var tax_opt = document.getElementsByName("tax_opt");
                   
                     var premiumWithTax_opt = document.getElementsByName("premiumWithTax_opt");
                     console.log(premiumWithTax_opt);
                    for (var i = 0; i < sum_insured_option.length; i++) {

                        si_id.push(sum_insured_option[i].value);
                        si_parr.push(premium_opt[i].value);
                        tax_Arr.push(tax_opt[i].value);
                        premium_taxArr.push(premiumWithTax_opt[i].value);

                    }

                }
              
                form.append("si_id", JSON.stringify(si_id));
                form.append("si_parr", JSON.stringify(si_parr));
                form.append("tax_Arr", JSON.stringify(tax_Arr));
                form.append("premium_taxArr", JSON.stringify(premium_taxArr));
			}
            } 
            var strs = $("#companySubTypePolicy").val();
            
            if (strpremium == "designation1") {
			 	
				
                    //alert(strpremium);
                    form.append("appFor", $("#appFor").val());
                    if ($("#appFor").val() == "designation1") {
                       // alert(1111);return;
                        var designation = document.getElementsByName("designation1");
                        designation.forEach(function (e) {
                            id.push(getAutCompletId(designationName, designationId, e.value.trim()));
                            arr.push(e.value.trim());
                        });
                        if (hasDuplicates(arr)) {
                            swal("", "Duplicate Designation", "error");
                            return;
                        }
                    }
                    form.append("designationpremium", JSON.stringify(arr));
                    form.append("designationpremiumId", JSON.stringify(id));

                    form.append("appFor", "");
                    if (document.getElementById("designationFile1").files.length > 0) {

                        form.append("premiumfilename", document.getElementById("designationFile1").files[0]);
                        form.append("premiumfileUploadType", "byDesignation");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }
                    


                } else if (strpremium == "age1") {
				   

                    form.append("appFor", "");
                    if (document.getElementById("ageFile1").files.length > 0) {

                        form.append("premiumfilename", document.getElementById("ageFile1").files[0]);
                        form.append("premiumfileUploadType", "byAge");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                } else if (strpremium == "memberAge1") {
					
	        
                    form.append("appFor", "");
                    if (document.getElementById("ageFile1").files.length > 0) {
                        form.append("premiumfilename", document.getElementById("ageFile1").files[0]);
                        form.append("premiumfileUploadType", "byMemberAge");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }
                } else if (strpremium == "grade1") {
					
	          if(strs == "flate"){
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt11");
                  
                    for (var i = 0; i < sum_insured_option.length; i++) {
                        fl_suminsure.push(sum_insured_option[i].value);
                    }
                }
                form.append("si_id", JSON.stringify(fl_suminsure));
            }  
                    form.append("appFor", "");
                    if (document.getElementById("gradeFile1").files.length > 0) {
                        form.append("premiumfilename", document.getElementById("gradeFile1").files[0]);
                        form.append("premiumfileUploadType", "byGrade");
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
					//append in object 
					
					
					
				// console.log(form);return;	
                $.ajax({
                    type: "POST",
                    url: "/employee/create_policy",
                    data: form,
                    processData: false,
                    contentType: false,
                    cache: false,
                    async: false,
                    mimeType: "multipart/form-data",
                    success: function (data) {
                        // return;
						debugger;
                        data = JSON.parse(data);
						var suminsuretypes = 'N';
					
						 $("#policySubType option[value='"+policy_type+"']").remove();
						   $("#policySubType option[value='']").remove();
						   $("#policySubType  option").each(function() {
							 
							  
							if($("#policySubType option:selected").text() != "Select" || $("#policySubType option:selected").text() == "" ){
						   
									arrd.push(this.text);
							}
							
													
							 });
							 if($("#policySubType option:selected").text() == "Select" || $("#policySubType option:selected").text() == "" ){
								d = test +" "+ data.mgs;
								 if(data.combo_flag == 'N')
							 {
								 suminsuretypes = 'N';
							 }
							 }
							 else {
							 d = test +" "+ data.mgs +" " +"You have  to add more" + " " +arrd;
							 if(data.combo_flag == 'Y')
							 {
								 suminsuretypes = data.SumInsuredType;
							 }
							 }
                        swal({
                            title: "Success",
                            text: d,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true
                        }, function() {
                               
									// return;
									 // $("#policySubType option[value='"+policy_type+"']").remove();
								
									if(data.mgs == "Policy Successfully created"){
										 $("#formDet")[0].reset();
                                  $("#payment_mode").val(null).trigger('change'); 
								  	  $('#companySubTypepremiumPolicy').trigger("change");
								  if(suminsuretypes == 'N')
								  {
									   $('#companySubTypePolicy').trigger("change");
									   $("#companySubTypePolicy").removeAttr('disabled', 'disabled');
								  }
								  else
								  {
									   $('#companySubTypePolicy').val(suminsuretypes);
									    $("#companySubTypePolicy").attr('disabled', 'disabled');
								  }
								  
									
                                     --count_subtype;
									if(count_subtype == 0){
										//status active
									$.ajax({
									type: "POST",
									url: "/employee/update_policy_with_product",
									data: {
										product_name:productName,random_data:random_data
									},
									
									 success: function (data) {
										
                                    data = JSON.parse(data);
									if(data.mgs == "Policy Successfully created"){
									 window.location.reload();
									}
									 }
									});
										
									}
									}
                                
                            });
                        return;
                    }
                });
				
				
            } 
            else if ($("#policySubType").val() == "8") {
                  // alert();return;
                var errorCount = 0;
                var additionalPremium = 0;
                var twins_child_limit = 0;
               
                var si_parr = [];
                var si_parr1 = [];
                var si_earr = [];
                var si_eplrarr = [];
                var si_id = [];
                var si_parents = 0;
                var bi_parents = 0;
               var tax_Arr = [];
                var premium_taxArr = [];
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
                var str = $("#companySubTypePolicy").val();
                var strpremium = $("#companySubTypepremiumPolicy").val();
                
                form.append("companySubTypePolicy", str);
                form.append("companySubTypepremiumPolicy", strpremium);

                if (str == "designation") {

                    form.append("appFor", $("#appFor").val());
                    if ($("#appFor").val() == "designation") {
                       // alert()
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

                    form.append("appFor", "");
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
				else if (str == "family_construct") {
					
                    form.append("appFor", "");
                    if (document.getElementById("familyConstructFile").files.length > 0) {
                        form.append("filename", document.getElementById("familyConstructFile").files[0]);
                        form.append("fileUploadType", "byFamilyConstruct");
                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                }
               else if(str =="parent"){
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt");
                    var premium_opt = document.getElementsByName("premium_opt");
                    var premium_opts = document.getElementsByName("premium_opts");
                   
                    for (var i = 0; i < sum_insured_option.length; i++) {

                        si_id.push(sum_insured_option[i].value);
                        si_parr.push(premium_opt[i].value);
                        si_parr1.push(premium_opts[i].value);

                    }

                } 
               if($("#single_parent").prop("checked", true)){
                   si_parents = 1;
               } if($("#both_parent").prop("checked", true)){
                   bi_parents = 1;
               } 
                form.append("bo_parents", bi_parents);
                form.append("si_parents", si_parents);
                form.append("si_id", JSON.stringify(si_id));
                form.append("si_parr", JSON.stringify(si_parr));
                form.append("si_parr1", JSON.stringify(si_parr1));
             }
               else if(str == "flate"){
				if(strpremium == "memberAge1" || strpremium == "designation1"){
					
						
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt11");
                  
                    for (var i = 0; i < sum_insured_option.length; i++) {
                        fl_suminsure.push(sum_insured_option[i].value);
                    }
                }
                form.append("si_id", JSON.stringify(fl_suminsure));
            }
            		

			else {
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt1");
                    var premium_opt = document.getElementsByName("premium_opt");
                    var tax_opt = document.getElementsByName("tax_opt");
                   
                     var premiumWithTax_opt = document.getElementsByName("premiumWithTax_opt");
                     console.log(premiumWithTax_opt);
                    for (var i = 0; i < sum_insured_option.length; i++) {

                        si_id.push(sum_insured_option[i].value);
                        si_parr.push(premium_opt[i].value);
                        tax_Arr.push(tax_opt[i].value);
                        premium_taxArr.push(premiumWithTax_opt[i].value);

                    }

                }
              
                form.append("si_id", JSON.stringify(si_id));
                form.append("si_parr", JSON.stringify(si_parr));
                form.append("tax_Arr", JSON.stringify(tax_Arr));
                form.append("premium_taxArr", JSON.stringify(premium_taxArr));
			}
            } 
            var strs = $("#companySubTypePolicy").val();
            
            if (strpremium == "designation1") {
			 	
				
                    //alert(strpremium);
                    form.append("appFor", $("#appFor").val());
                    if ($("#appFor").val() == "designation1") {
                       // alert(1111);return;
                        var designation = document.getElementsByName("designation1");
                        designation.forEach(function (e) {
                            id.push(getAutCompletId(designationName, designationId, e.value.trim()));
                            arr.push(e.value.trim());
                        });
                        if (hasDuplicates(arr)) {
                            swal("", "Duplicate Designation", "error");
                            return;
                        }
                    }
                    form.append("designationpremium", JSON.stringify(arr));
                    form.append("designationpremiumId", JSON.stringify(id));

                    form.append("appFor", "");
                    if (document.getElementById("designationFile1").files.length > 0) {

                        form.append("premiumfilename", document.getElementById("designationFile1").files[0]);
                        form.append("premiumfileUploadType", "byDesignation");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }
                    


                } else if (strpremium == "age1") {
				   

                    form.append("appFor", "");
                    if (document.getElementById("ageFile1").files.length > 0) {

                        form.append("premiumfilename", document.getElementById("ageFile1").files[0]);
                        form.append("premiumfileUploadType", "byAge");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                } else if (strpremium == "memberAge1") {
					
	        
                    form.append("appFor", "");
                    if (document.getElementById("ageFile1").files.length > 0) {
                        form.append("premiumfilename", document.getElementById("ageFile1").files[0]);
                        form.append("premiumfileUploadType", "byMemberAge");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }
                } else if (strpremium == "grade1") {
					
	          if(strs == "flate"){
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt11");
                  
                    for (var i = 0; i < sum_insured_option.length; i++) {
                        fl_suminsure.push(sum_insured_option[i].value);
                    }
                }
                form.append("si_id", JSON.stringify(fl_suminsure));
            }  
                    form.append("appFor", "");
                    if (document.getElementById("gradeFile1").files.length > 0) {
                        form.append("premiumfilename", document.getElementById("gradeFile1").files[0]);
                        form.append("premiumfileUploadType", "byGrade");
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
					//append in object 
					
					
					
					
                $.ajax({
                    type: "POST",
                    url: "/employee/create_policy",
                    data: form,
                    processData: false,
                    contentType: false,
                    cache: false,
                    async: false,
                    mimeType: "multipart/form-data",
                    success: function (data) {
                        data = JSON.parse(data);
						var suminsuretypes = 'N';
						 $("#policySubType option[value='"+policy_type+"']").remove();
						   $("#policySubType option[value='']").remove();
						   $("#policySubType  option").each(function() {
							 
							  
							if($("#policySubType option:selected").text() != "Select" || $("#policySubType option:selected").text() == "" ){
						   
									arrd.push(this.text);
							}
							
													
							 });
							 if($("#policySubType option:selected").text() == "Select" || $("#policySubType option:selected").text() == "" ){
								d = test +" "+ data.mgs;
								suminsuretypes = 'N';
							 }
							 else {
							 d = test +" "+ data.mgs +" " +"You have  to add more" + " " +arrd;
							  if(data.combo_flag == 'Y')
							 {
								 suminsuretypes = data.SumInsuredType;
							 }
							 }
                        swal({
                            title: "Success",
                            text: d,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true
                        }, function() {
                               
									
									 // $("#policySubType option[value='"+policy_type+"']").remove();
								
									if(data.mgs == "Policy Successfully created"){
										 $("#formDet")[0].reset();
                                  $("#payment_mode").val(null).trigger('change'); 
								   $('#companySubTypePolicy').trigger("change");
								   	if(suminsuretypes == 'N')
								  {
									   $('#companySubTypePolicy').trigger("change");
									    $("#companySubTypePolicy").removeAttr('disabled', 'disabled');
								  }
								  else
								  {
									   $('#companySubTypePolicy').val(suminsuretypes);
									    $("#companySubTypePolicy").attr('disabled', 'disabled');
								  }
										  $('#companySubTypepremiumPolicy').trigger("change");
                                     --count_subtype;
									if(count_subtype == 0){
										//status active
									$.ajax({
									type: "POST",
									url: "/employee/update_policy_with_product",
									data: {
										product_name:productName,random_data:random_data
									},
									
									 success: function (data) {
										
                                    data = JSON.parse(data);
									if(data.mgs == "Policy Successfully created"){
									 window.location.reload();
									}
									 }
									});
										
									}
									}
                                
                            });
                        return;
                    }
                });
				
				
            } 
            else if ($("#policySubType").val() == "4") {
                var si_parr = [];
                var si_parr1 = [];
                var si_earr = [];
                var si_eplrarr = [];
                var si_id = [];
                var si_parents = 0;
                var bi_parents = 0;
               var tax_Arr = [];
                var premium_taxArr = [];

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
              
                var str = $("#companySubTypePolicy").val();
                form.append("companySubTypePolicy", str);
                var strpremium = $("#companySubTypepremiumPolicy").val();
                form.append("companySubTypepremiumPolicy", strpremium);

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

                    form.append("appFor", "");
                    if (document.getElementById("designationFile").files.length > 0) {

                        form.append("filename", document.getElementById("designationFile").files[0]);
                        form.append("premiumfileUploadType", "byDesignation");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                } else if (str == "age") {
                    form.append("appFor", "");
                    if (document.getElementById("ageFile").files.length > 0) {
                        form.append("filename", document.getElementById("ageFile").files[0]);
                        form.append("premiumfileUploadType", "byAge");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                } else if (str == "memberAge") {
                    form.append("appFor", "");
                    if (document.getElementById("ageFile").files.length > 0) {
                        form.append("filename", document.getElementById("ageFile").files[0]);
                        form.append("premiumfileUploadType", "byMemberAge");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }
                } else if (str == "grade") {
                    form.append("appFor", "");
                    if (document.getElementById("gradeFile").files.length > 0) {
                        form.append("filename", document.getElementById("gradeFile").files[0]);
                        form.append("premiumfileUploadType", "byGrade");
                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                }
				else if (str == "family_construct") {
                    form.append("appFor", "");
                    if (document.getElementById("familyConstructFile").files.length > 0) {
                        form.append("filename", document.getElementById("familyConstructFile").files[0]);
                        form.append("fileUploadType", "byFamilyConstruct");
                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                }
				

            if(str == "parent"){
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt");
                    var premium_opt = document.getElementsByName("premium_opt");
                    var premium_opts = document.getElementsByName("premium_opts");
                    
                    for (var i = 0; i < sum_insured_option.length; i++) {

                        si_id.push(sum_insured_option[i].value);
                        si_parr.push(premium_opt[i].value);
                        si_parr1.push(premium_opts[i].value);

                    }

                }
                 if($("#single_parent").prop("checked", true)){
                   si_parents = 1;
               } if($("#both_parent").prop("checked", true)){
                   bi_parents = 1;
               } 
                form.append("bo_parents", bi_parents);
                form.append("si_parents", si_parents);
                form.append("si_id", JSON.stringify(si_id));
                form.append("si_parr", JSON.stringify(si_parr));
                form.append("si_parr1", JSON.stringify(si_parr1));
            }
            
             if(str == "flate"){
				 
				 if(strpremium == "memberAge1"){
					
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt11");
                  
                    for (var i = 0; i < sum_insured_option.length; i++) {
                        fl_suminsure.push(sum_insured_option[i].value);
                    }
                }
                form.append("si_id", JSON.stringify(fl_suminsure));
          
				 }
				 else {
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt");
                    var premium_opt = document.getElementsByName("premium_opt");
                    var tax_opt = document.getElementsByName("tax_opt");
                     var premiumWithTax_opt = document.getElementsByName("premiumWithTax_opt");
                    
                    for (var i = 0; i < sum_insured_option.length; i++) {

                        si_id.push(sum_insured_option[i].value);
                        si_parr.push(premium_opt[i].value);
                        tax_Arr.push(tax_opt[i].value);
                        premium_taxArr.push(premiumWithTax_opt[i].value);

                    }

                }
              
                form.append("si_id", JSON.stringify(si_id));
                form.append("si_parr", JSON.stringify(si_parr));
                form.append("tax_Arr", JSON.stringify(tax_Arr));
                form.append("premium_taxArr", JSON.stringify(premium_taxArr));
              
			 } }
            
            //premium
            
            if (strpremium == "designation1") {
				 if(strs == "flate"){
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt11");
                  
                    for (var i = 0; i < sum_insured_option.length; i++) {
                        fl_suminsure.push(sum_insured_option[i].value);
                    }
                }
                form.append("si_id", JSON.stringify(fl_suminsure));
            }  

                    form.append("appFor", $("#appFor").val());
                    if ($("#appFor").val() == "designation1") {
                        var designation = document.getElementsByName("designation1");
                        designation.forEach(function (e) {
                            id.push(getAutCompletId(designationName, designationId, e.value.trim()));
                            arr.push(e.value.trim());
                        });
                        if (hasDuplicates(arr)) {
                            swal("", "Duplicate Designation", "error");
                            return;
                        }
                    }


                    form.append("designation1", JSON.stringify(arr));
                    form.append("designationId", JSON.stringify(id));

                    form.append("appFor", "");
                    if (document.getElementById("designationFile1").files.length > 0) {

                        form.append("premiumfilename", document.getElementById("designationFile1").files[0]);
                        form.append("premiumfileUploadType", "byDesignation");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                } else if (strpremium == "age1") {
					 if(strs == "flate"){
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt11");
                  
                    for (var i = 0; i < sum_insured_option.length; i++) {
                        fl_suminsure.push(sum_insured_option[i].value);
                    }
                }
                form.append("si_id", JSON.stringify(fl_suminsure));
            }  
                    form.append("appFor", "");
                    if (document.getElementById("ageFile1").files.length > 0) {
                        form.append("premiumfilename", document.getElementById("ageFile1").files[0]);
                        form.append("premiumfileUploadType", "byAge");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                } else if (strpremium == "memberAge1") {
					 if(strs == "flate"){
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt11");
                  
                    for (var i = 0; i < sum_insured_option.length; i++) {
                        fl_suminsure.push(sum_insured_option[i].value);
                    }
                }
                form.append("si_id", JSON.stringify(fl_suminsure));
            }  
                    form.append("appFor", "");
                    if (document.getElementById("ageFile1").files.length > 0) {
                        form.append("premiumfilename", document.getElementById("ageFile1").files[0]);
                        form.append("premiumfileUploadType", "byMemberAge");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }
                } else if (strpremium == "grade1") {
					 if(strs == "flate"){
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt11");
                  
                    for (var i = 0; i < sum_insured_option.length; i++) {
                        fl_suminsure.push(sum_insured_option[i].value);
                    }
                }
                form.append("si_id", JSON.stringify(fl_suminsure));
            }  
                    form.append("appFor", "");
                    if (document.getElementById("gradeFile1").files.length > 0) {
                        form.append("premiumfilename", document.getElementById("gradeFile1").files[0]);
                        form.append("premiumfileUploadType", "byGrade");
                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                }
                /* form.append("si_earr", JSON.stringify(si_earr));
                 form.append("si_eplrarr", JSON.stringify(si_eplrarr));*/

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
                    url: "/employee/create_policy",
                    data: form,
                    processData: false,
                    contentType: false,
                    cache: false,
                    async: false,
                    mimeType: "multipart/form-data",
                    success: function (data) {
                        data = JSON.parse(data);
						var suminsuretypes = 'N';
                        $("#policySubType option[value='"+policy_type+"']").remove();
						   $("#policySubType option[value='']").remove();
						   $("#policySubType  option").each(function() {
							 
							  
							if($("#policySubType option:selected").text() != "Select" || $("#policySubType option:selected").text() == "" ){
						   
									arrd.push(this.text);
							}
							
													
							 });
							 if($("#policySubType option:selected").text() == "Select" || $("#policySubType option:selected").text() == "" ){
								d = test +" "+ data.mgs;
								suminsuretypes = 'N';
							 }
							 else {
							 d = test +" "+ data.mgs +" " +"You have  to add more" + " " +arrd;
							 if(data.combo_flag == 'Y')
							 {
								 suminsuretypes = data.SumInsuredType;
							 }
							 }
                        swal({
                            title: "Success",
                            text: d,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true
                        }, function() {
                               
									
									 // $("#policySubType option[value='"+policy_type+"']").remove();
								
									if(data.mgs == "Policy Successfully created"){
										 $("#formDet")[0].reset();
                                  $("#payment_mode").val(null).trigger('change'); 
								   $('#companySubTypePolicy').trigger("change");
										  $('#companySubTypepremiumPolicy').trigger("change");
										  	if(suminsuretypes == 'N')
								  {
									   $('#companySubTypePolicy').trigger("change");
									   $("#companySubTypePolicy").removeAttr('disabled', 'disabled');
								  }
								  else
								  {
									   $('#companySubTypePolicy').val(suminsuretypes);
									    $("#companySubTypePolicy").attr('disabled', 'disabled');
								  }
                                     --count_subtype;
									if(count_subtype == 0){
										//status active
									$.ajax({
									type: "POST",
									url: "/employee/update_policy_with_product",
									data: {
										product_name:productName,random_data:random_data
									},
									
									 success: function (data) {
										
                                    data = JSON.parse(data);
									if(data.mgs == "Policy Successfully created"){
									 window.location.reload();
									}
									 }
									});
										
									}
									}
                                
                            })
                        return;
                    }
                });
            }
            else if ($("#policySubType").val() == "2" || $("#policySubType").val() == "3") {
                
                var si_parr = [];
                var si_parr1 = [];
                var si_earr = [];
                var si_eplrarr = [];
                var si_id = [];
                var si_parents = 0;
                var bi_parents = 0;
               var tax_Arr = [];
                var premium_taxArr = [];

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
                var strpremium = $("#companySubTypepremiumPolicy").val();
                form.append("companySubTypepremiumPolicy", strpremium);
                
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

                     form.append("appFor", "");
                    if (document.getElementById("designationFile").files.length > 0) {

                        form.append("filename", document.getElementById("designationFile").files[0]);
                        form.append("premiumfileUploadType", "byDesignation");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                } 
				else if (str == "family_construct") {
                    form.append("appFor", "");
                    if (document.getElementById("familyConstructFile").files.length > 0) {
                        form.append("filename", document.getElementById("familyConstructFile").files[0]);
                        form.append("fileUploadType", "byFamilyConstruct");
                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                }
                else if (str == "family_construct_age") {
					
                    form.append("appFor", "");
                    if (document.getElementById("agefamilyConstructFile").files.length > 0) {
                        form.append("filename", document.getElementById("agefamilyConstructFile").files[0]);
                        form.append("fileUploadType", "byFamilyAgeConstruct");
                        
                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                }
               else if(str =="parent"){
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt");
                    var premium_opt = document.getElementsByName("premium_opt");
                    var premium_opts = document.getElementsByName("premium_opts");
                   
                    for (var i = 0; i < sum_insured_option.length; i++) {

                        si_id.push(sum_insured_option[i].value);
                        si_parr.push(premium_opt[i].value);
                        si_parr1.push(premium_opts[i].value);

                    }

                } 
               if($("#single_parent").prop("checked", true)){
                   si_parents = 1;
               } if($("#both_parent").prop("checked", true)){
                   bi_parents = 1;
               } 
                form.append("bo_parents", bi_parents);
                form.append("si_parents", si_parents);
                form.append("si_id", JSON.stringify(si_id));
                form.append("si_parr", JSON.stringify(si_parr));
                form.append("si_parr1", JSON.stringify(si_parr1));
             }
                else if (str == "grade"){
                    form.append("appFor", "");
                    if (document.getElementById("gradeFile").files.length > 0) {
                        form.append("filename", document.getElementById("gradeFile").files[0]);
                        form.append("premiumfileUploadType", "byGrade");
                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

               
                }
				
				if(str == "flate" ){
					if(strpremium == "memberAge1" || strpremium == "designation1"){
					
						
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt11");
                  
                    for (var i = 0; i < sum_insured_option.length; i++) {
                        fl_suminsure.push(sum_insured_option[i].value);
                    }
                }
                form.append("si_id", JSON.stringify(fl_suminsure));
            }
             else {
					
                 
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt1");
                    var premium_opt = document.getElementsByName("premium_opt");
                    var tax_opt = document.getElementsByName("tax_opt");
                     var premiumWithTax_opt = document.getElementsByName("premiumWithTax_opt");
                    console.log(sum_insured_option.length);
                    for (var i = 0; i < sum_insured_option.length; i++) {
                        si_id.push(sum_insured_option[i].value);
                        si_parr.push(premium_opt[i].value);
                        tax_Arr.push(tax_opt[i].value);
                        premium_taxArr.push(premiumWithTax_opt[i].value);

                    }

                }
              
                form.append("si_id", JSON.stringify(si_id));
                form.append("si_parr", JSON.stringify(si_parr));
                form.append("tax_Arr", JSON.stringify(tax_Arr));
                form.append("premium_taxArr", JSON.stringify(premium_taxArr));
				}
              
            }
                
                
                
                
              
				 if (strpremium == "designation1") {
                    form.append("appFor", $("#appFor").val());
                    if ($("#appFor").val() == "designation1") {
                       // alert(1111);return;
                        var designation = document.getElementsByName("designation1");
                        designation.forEach(function (e) {
                            id.push(getAutCompletId(designationName, designationId, e.value.trim()));
                            arr.push(e.value.trim());
                        });
                        if (hasDuplicates(arr)) {
                            swal("", "Duplicate Designation", "error");
                            return;
                        }
                    }
                    form.append("designationpremium", JSON.stringify(arr));
                    form.append("designationpremiumId", JSON.stringify(id));

                    form.append("appFor", "");
                    if (document.getElementById("designationFile1").files.length > 0) {

                        form.append("premiumfilename", document.getElementById("designationFile1").files[0]);
                        form.append("premiumfileUploadType", "byDesignation");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }
                    


                } else if (strpremium == "age1") {
				   

                    form.append("appFor", "");
                    if (document.getElementById("ageFile1").files.length > 0) {

                        form.append("premiumfilename", document.getElementById("ageFile1").files[0]);
                        form.append("premiumfileUploadType", "byAge");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                } else if (strpremium == "memberAge1") {
					
	        
                    form.append("appFor", "");
                    if (document.getElementById("ageFile1").files.length > 0) {
                        form.append("premiumfilename", document.getElementById("ageFile1").files[0]);
                        form.append("premiumfileUploadType", "byMemberAge");

                    } else {
                        alert("Please add file to upload.");
                        return;
                    }
                } else if (strpremium == "grade1") {
					
	          if(strs == "flate"){
                if ($("#sum_insured_type").val() == "individual" || $("#sum_insured_type").val() == "familyIndividual" || $("#sum_insured_type").val() == "familyGroup") {
                    var sum_insured_option = document.getElementsByName("sum_insured_opt11");
                  
                    for (var i = 0; i < sum_insured_option.length; i++) {
                        fl_suminsure.push(sum_insured_option[i].value);
                    }
                }
                form.append("si_id", JSON.stringify(fl_suminsure));
            }  
                    form.append("appFor", "");
                    if (document.getElementById("gradeFile1").files.length > 0) {
                        form.append("premiumfilename", document.getElementById("gradeFile1").files[0]);
                        form.append("premiumfileUploadType", "byGrade");
                    } else {
                        alert("Please add file to upload.");
                        return;
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
				
				var policytype= $("#policySubType").val(); 
				

                $.ajax({
                    type: "POST",
                    url: "/employee/create_policy",
                    data: form,
                    processData: false,
                    contentType: false,
                    cache: false,
                    async: false,
                    mimeType: "multipart/form-data",
                    success: function (data) {
                        data = JSON.parse(data);
						var suminsuretypes = 'N';
						 $("#policySubType option[value='"+policy_type+"']").remove();
						   $("#policySubType option[value='']").remove();
						   $("#policySubType  option").each(function() {
							 
							  
							if($("#policySubType option:selected").text() != "Select" || $("#policySubType option:selected").text() == "" ){
						   
									arrd.push(this.text);
							}
							
													
							 });
							 if($("#policySubType option:selected").text() == "Select" || $("#policySubType option:selected").text() == "" ){
								d = test +" "+ data.mgs;
								suminsuretypes = 'N';
							 }
							 else {
							 d = test +" "+ data.mgs +" " +"You have  to add more" + " " +arrd;
							 if(data.combo_flag == 'Y')
							 {
								 suminsuretypes = data.SumInsuredType;
							 }
							 }
                        swal({
                            title: "Success",
                            text: d,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true
                        }, function() {
                               
									
									 // $("#policySubType option[value='"+policy_type+"']").remove();
								
									if(data.mgs == "Policy Successfully created"){
										 $("#formDet")[0].reset();
                                  $("#payment_mode").val(null).trigger('change'); 
								   $('#companySubTypePolicy').trigger("change");
										  $('#companySubTypepremiumPolicy').trigger("change");
										  if(suminsuretypes == 'N')
								  {
									   $('#companySubTypePolicy').trigger("change");
									   $("#companySubTypePolicy").removeAttr('disabled', 'disabled');
								  }
								  else
								  {
									   $('#companySubTypePolicy').val(suminsuretypes);
									    $("#companySubTypePolicy").attr('disabled', 'disabled');
								  }
                                     --count_subtype;
									if(count_subtype == 0){
										//status active
									$.ajax({
									type: "POST",
									url: "/employee/update_policy_with_product",
									data: {
										product_name:productName,random_data:random_data
									},
									
									 success: function (data) {
										
                                    data = JSON.parse(data);
									if(data.mgs == "Policy Successfully created"){
									 window.location.reload();
									}
									 }
									});
										
									}
									}
                                
                            });
							
                           
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
                var strpremium = $("#companySubTypepremiumPolicy").val();
                form.append("companySubTypepremiumPolicy", strpremium);
               if (str == "family_construct") {
                    form.append("appFor", "");
                    if (document.getElementById("familyConstructFile").files.length > 0) {
                        form.append("filename", document.getElementById("familyConstructFile").files[0]);
                        form.append("fileUploadType", "byFamilyConstruct");
                    } else {
                        alert("Please add file to upload.");
                        return;
                    }

                }
                
                if (strpremium == "designation1") {
                    form.append("appFor", $("#appFor").val());
                    if ($("#appFor").val() == "designation1") {
                        var designation = document.getElementsByName("designation1");
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

                       form.append("appFor", "");
                    if (document.getElementById("designationFile1").files.length > 0) {

                        form.append("premiumfilename", document.getElementById("designationFile1").files[0]);
                        form.append("premiumfileUploadType", "byDesignation");

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
                console.log(form);
                $.ajax({
                    type: "POST",
                    url: "/employee/create_policy",
                    data: form,
                    processData: false,
                    contentType: false,
                    cache: false,
                    async: false,
                    mimeType: "multipart/form-data",
                    success: function (data) {
                        data = JSON.parse(data);
						var suminsuretypes = 'N';
						
                       $("#policySubType option[value='"+policy_type+"']").remove();
						   $("#policySubType option[value='']").remove();
						   $("#policySubType  option").each(function() {
							 
							  
							if($("#policySubType option:selected").text() != "Select" || $("#policySubType option:selected").text() == "" ){
						   
									arrd.push(this.text);
							}
							
													
							 });
							 if($("#policySubType option:selected").text() == "Select" || $("#policySubType option:selected").text() == "" ){
								d = test +" "+ data.mgs;
								suminsuretypes = 'N';
							 }
							 else {
							 d = test +" "+ data.mgs +" " +"You have  to add more" + " " +arrd;
							 if(data.combo_flag == 'Y')
							 {
								 suminsuretypes = data.SumInsuredType;
							 }
							 }
                        swal({
                            title: "Success",
                            text:d,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true
                        } , function() {
                               
									
									 // $("#policySubType option[value='"+policy_type+"']").remove();
								
									if(data.mgs == "Policy Successfully created"){
										 $("#formDet")[0].reset();
                                  $("#payment_mode").val(null).trigger('change'); 
								   $('#companySubTypePolicy').trigger("change");
								   if(suminsuretypes == 'N')
								  {
									   $('#companySubTypePolicy').trigger("change");
									   $("#companySubTypePolicy").removeAttr('disabled', 'disabled');
								  }
								  else
								  {
									   $('#companySubTypePolicy').val(suminsuretypes);
									    $("#companySubTypePolicy").attr('disabled', 'disabled');
								  }
										  $('#companySubTypepremiumPolicy').trigger("change");
                                     --count_subtype;
									if(count_subtype == 0){
										//status active
									$.ajax({
									type: "POST",
									url: "/employee/update_policy_with_product",
									data: {
										product_name:productName,random_data:random_data
									},
									
									 success: function (data) {
										
                                    data = JSON.parse(data);
									if(data.mgs == "Policy Successfully created"){
									 window.location.reload();
									}
									 }
									});
										
									}
									}
                                
                            });
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
                     form.append("appFor", "");
                    if (document.getElementById("designationFile").files.length > 0) {

                        form.append("filename", document.getElementById("designationFile").files[0]);
                        form.append("premiumfileUploadType", "byDesignation");

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
                    url: "/employee/create_policy",
                    data: form,
                    processData: false,
                    contentType: false,
                    cache: false,
                    async: false,
                    mimeType: "multipart/form-data",
                    success: function (data) {
                        data = JSON.parse(data);
						var suminsuretypes = 'N';
                       $("#policySubType option[value='"+policy_type+"']").remove();
						   $("#policySubType option[value='']").remove();
						   $("#policySubType  option").each(function() {
							 
							  
							if($("#policySubType option:selected").text() != "Select" || $("#policySubType option:selected").text() == "" ){
						   
									arrd.push(this.text);
							}
							
													
							 });
							 if($("#policySubType option:selected").text() == "Select" || $("#policySubType option:selected").text() == "" ){
								d = test +" "+ data.mgs;
								suminsuretypes = 'N';
							 }
							 else {
							 d = test +" "+ data.mgs +" " +"You have  to add more" + " " +arrd;
							  if(data.combo_flag == 'Y')
							 {
								 suminsuretypes = data.SumInsuredType;
							 }
							 }
                        swal({
                            title: "Success",
                            text: d,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true
                        } , function() {
                               
									
									//  $("#policySubType option[value='"+policy_type+"']").remove();
								
									if(data.mgs == "Policy Successfully created"){
										 $("#formDet")[0].reset();
                                  $("#payment_mode").val(null).trigger('change'); 
								   $('#companySubTypePolicy').trigger("change");
								   if(suminsuretypes == 'N')
								  {
									   $('#companySubTypePolicy').trigger("change");
									   $("#companySubTypePolicy").removeAttr('disabled', 'disabled');
								  }
								  else
								  {
									   $('#companySubTypePolicy').val(suminsuretypes);
									    $("#companySubTypePolicy").attr('disabled', 'disabled');
								  }
										  $('#companySubTypepremiumPolicy').trigger("change");
                                     --count_subtype;
									if(count_subtype == 0){
										//status active
									$.ajax({
									type: "POST",
									url: "/employee/update_policy_with_product",
									data: {
										product_name:productName,random_data:random_data
									},
									
									 success: function (data) {
										
                                    data = JSON.parse(data);
									if(data.mgs == "Policy Successfully created"){
									 window.location.reload();
									}
									 }
									});
										
									}
									}
                                
                            });
                        return;
                    }
                });
            } 
            else {
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
                    url: "/employee/create_policy",
                    data: form,
                    processData: false,
                    contentType: false,
                    cache: false,
                    async: false,
                    mimeType: "multipart/form-data",
                    success: function (data) {

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
       if($("#familyConstruct").val() == '3,0' || $("#familyConstruct").val() == '4,0'){
           if (this.id == "premiumIndividual") {
            $("#premium_emp").attr("style","display:none;");
           } 
       }
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
            
            if ($("#companySubTypepremiumPolicy").val() == "designation1") {
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
            
            if ($("#companySubTypepremiumPolicy").val() == "designation1") {
                if ($("#appFor").val() == "designation1") {
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
        
        if ($("#companySubTypepremiumPolicy").val() != "designation1") {
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
}
/*$("#premiumEmployeeContri").change(function(){
    var data = $("#premiumEmployeeContri").val();
  
    if(data > 0){
       $(".flex_payroll_allocate").show();
    }
    else {
         $(".flex_payroll_allocate").hide();
    }
}) */
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
    url: "/employee/get_approval_data",
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

$('#payment_mode').select2({
    columns: 1,
    placeholder: 'Select',
    search: true
});

$('#policySubTypes').select2({
    columns: 1,
    placeholder: 'Select',
    search: true
});

$('#customer_search_fi').select2({
    columns: 1,
    placeholder: 'Select',
    search: true
});



 


// for new payment mode

$.ajax({
    type: "POST",
    url: "/employee/get_payment_mode_data",
    async: false,
    success: function (e) {
		
        e = JSON.parse(e);
		console.log(e);
        if (e) {
            var approval = e;
            $("#payment_mode").empty();
            $("#payment_mode").html("<option value=''>Select</option>");

            approval.forEach(function (e) {
                $("#payment_mode").append("<option value='" + e.id + "'>" + e.payment_mode_name +  "</option>");
            });


        }
    },
    error: function () {

    }
});


// for payment type show data in dropdown
$( "#payment_type" ) .change(function () {   
    var id = $('select#payment_type option:selected').val(); 
    if(id == "Y"){
    $(".pay_mode_div").css("display", "block");
    $("#premium_cd_div").css("display", "none");	
    }
    else {
        $("#premium_cd_div").css("display", "block");
        $(".pay_mode_div").css("display", "none");
    }
    });

// for customer search data in dropdown
$( "#customer_search" ) .change(function () {   
var id = $('select#customer_search option:selected').val(); 
if(id == "Y"){
$(".customer_search_div").css("display", "block");
$.ajax({
    type: "POST",
    url: "/employee/get_customer_search_data",
    async: false,
    success: function (e) {
		
        e = JSON.parse(e);
		console.log(e);
        if (e) {
            var approval = e;
            $("#customer_search_fi").empty();
            $("#customer_search_fi").html("<option value=''>Select</option>");

            approval.forEach(function (e) {
                $("#customer_search_fi").append("<option value='" + e.id + "'>" + e.search_by +  "</option>");
            });


        }
    },
    error: function () {

    }
});
	
}
else {
	$(".customer_search_div").css("display", "none");
}

}); 




//  $('#formdata').on("submit", function () {
    $("#formdata").submit(function(e) {
        e.preventDefault();
        var form = new FormData();
	//alert();
	//  var policy_subtypes_id = $("#policySubTypes").val();
	//  var policy_subtypes_name = $("#policySubTypes option:selected").text();
	// // console.log(policy_subtypes_id.length);
	// // return false;
	//  var policy_types_id = $("#policyType").val();
	//  var product_name = $("#productName").val();
	//  var database_name = $("#database_name").val();
	//  var product_code = $("#product_code").val();
    //  var GroupCodeFile = "";

     form.append("policy_subtypes_id", $("#policySubTypes").val());
     form.append("policy_subtypes_name", $("#policySubTypes option:selected").val());
     form.append("policy_types_id", $("#policyType").val());
     form.append("product_name", $("#productName").val());
     form.append("database_name", $("#database_name").val());
     form.append("product_code", $("#product_code").val());


     if (document.getElementById("GroupCodeFile").files.length > 0) {
                // var GroupCodeFile =  document.getElementById("GroupCodeFile").files[0];
                form.append("GroupCodeFile", document.getElementById("GroupCodeFile").files[0]);
            } else {
                alert("Please add group code file to upload.");
                return;
            }

            if (document.getElementById("PDeclaration").files.length > 0) {
              
                form.append("PDeclaration", document.getElementById("PDeclaration").files[0]);
            } else {
                alert("Please add policy declaration file to upload.");
                return;
            }
        
	    $.ajax({
		url: "/employee/save_tem_data",
        type: "POST",
        data: form,
        processData: false,
        contentType: false,
        cache: false,
        async: false,
        mimeType: "multipart/form-data",
        success: function (e) {
            get_start();
            $("#formDet").show();
           
		 $("#policySubType").empty();
		 var data_res = JSON.parse(e);
		
		 var data = data_res.subtype;
		 if(data_res == false){
			  alert("product existing , please choose other product");
		 }else {
            $("#formdata :input").prop("disabled", true);
		
		if(data.length > 0){
			$("#subtype").css("display","none");
			$('#productName').attr('readonly', 'true');
			$('#policySubTypes').attr('disabled', 'true');
			
			$('#policyType').attr('disabled', 'true');
			
			$('#combo_flag').show();
			
			 $("#policySubType").append("<option value=''>Select</option>");
             count_subtype = data.length;
            
			
			for(i = 0; i < data.length; i++){
				$("#policySubType").append("<option data-id = '"+ data[i].id +"' value='" +data[i].policy_subtype_id + "'>" + data[i].policy_sub_type_name + "</option>");
				$('#product_parent_id').val(data[i].policy_parent_id);
		}
			
		
		} }
			
          
    }
	 
 });
 });

 $.ajax({
    type: "POST",
    url: "/employee/get_policy_creation_det",
    async: false,
    success: function (e) {
        e = JSON.parse(e);
        if (e.policyType) {
            var policyType = e.policyType;
            var masterInsurance = e.masterInsurance;
            var tpaCode = e.tpaCode;
            $("#masterInsurance").empty();
            $("#policyType").empty();
            $("#tpaCode").empty();
            $("#masterInsurance").html("<option value=''>Select</option>");
            $("#policyType").html("<option value=''> Select </option>");
            $("#tpaCode").html("<option value=''>Select</option>");
            masterInsurance.forEach(function (e) {
                $("#masterInsurance").append("<option value='" + e.insurer_id + "'>" + e.ins_co_name + "</option>");
            });
            policyType.forEach(function (e) {

                if (e.policy_type_id != 3)
                    $("#policyType").append("<option value='" + e.policy_type_id + "'>" + e.policy_name + "</option>");
            });
            tpaCode.forEach(function (e) {

                $("#tpaCode").append("<option value='" + e.TPA_id + "'>" + e.TPA_name + "</option>");
            });
        }
    },
    error: function () {

    }
});
 

$("#policyType").change(function () {

    $.post("/employee/get_policy_subType", { "policy_type_id": $("#policyType").val() }, function (e) {

        e = JSON.parse(e);
        var policySubType = e.policySubType;
        $("#policySubTypes").empty();
        $("#policySubTypes").html("<option value=''>Select</option>");
        policySubType.forEach(function (e) {

            $("#policySubTypes").append("<option value='" + e.policy_sub_type_id + "'>" + e.policy_sub_type_name + "</option>");
        });
    });
});




