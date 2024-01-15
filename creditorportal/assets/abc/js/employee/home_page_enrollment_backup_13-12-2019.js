ids = [];

$("select[name='family_members_id']").on("change", function() {
    enableDisableFamilyConstruct($("#family_members_id1")[0]);
});
$.ajax({
    url: "/employee/get_master_salutation",
    type: "POST",
    async: false,
    dataType: "json",
    success: function (response) {

        // debugger;
        $("#salutation").empty();
        $("#salutation").append("<option value = ''>Select salutation</option>");
        for (i = 0; i < response.length; i++) {

            $("#salutation").append("<option  value =" + response[i]['s_id'] + ">" + response[i]['salutation'] + "</option>");


        }
    }
});
function setFamilyConstruct(value) {
    var tax_with_premium = $("#sum_insures option:selected").data("id");

        if ($("#sum_insures :selected").attr("data-type") == "family_construct" || $("#sum_insures :selected").attr("data-type") == "family_construct_age") {


            $.post("/get_family_construct", {
                "policyNo": $("#sum_insures :selected").attr("data-policyno"),
                "sumInsured": $("#sum_insures :selected").val(),
                "table": $("#sum_insures :selected").attr("data-type"),
            }, function (e) {
                $("#patFamilyConstruct").empty();
                $("#patFamilyConstruct").html('<option value="" selected>Select</option>');
                e = JSON.parse(e);
                $("#patFamilyConstructDiv").show();
                if (e) {
                    e.forEach(function (e1) {
                        // console.log(e1);
                        $("#patFamilyConstruct").append("<option value='" + e1.family_type + "' data-premium='" + e1.PremiumServiceTax + "'>" + e1.family_type + "</option>");
                    });
                    if(value) {
                        $("#patFamilyConstruct").val(value);
                    }
                    if(value) {
                        $("#patFamilyConstruct").change();
                    }
                }
            });


        }
        else {
            $("#patFamilyConstructDiv").hide();
        }
        
        enableDisableFamilyConstruct($("#family_members_id1")[0]);
}

function enableDisableFamilyConstruct(e) {
    if(e.value == 0) {
        // $(e).closest("")
        // $(e).closest("select[name='family_members_id']").css("pointer-events","none");
        $(e).closest("form").find("input[name='first_name']").css("pointer-events","none");
        $(e).closest("form").find("input[name='last_name']").css("pointer-events","none");
        $(e).closest("form").find("input[name='family_date_birth']").css("pointer-events","none");
        // $("#family_date_birth1").css("pointer-events", "none");
    }else {
        // $(e).closest("form").find("select[name='family_members_id']").css("pointer-events","auto");
        $(e).closest("form").find("input[name='first_name']").css("pointer-events","auto");
        $(e).closest("form").find("input[name='last_name']").css("pointer-events","auto");
        $(e).closest("form").find("input[name='family_date_birth']").css("pointer-events","auto");
    }
}
function getRelation(){
	
 var fam_rel = $('select[name=familyConstruct]').val().split("+");
 var opt = $("#family_members_id1 option");
 
	opt.each(function(e) {
		$(this).css("display", "block");
	});
 
  //kids & self
	 if(fam_rel[0].substr(0,1) == '1')
	 {
		opt.each(function(e) {
			if(this.value == 0) {
				$(this).css("display", "block");
			}else {
				$(this).css("display", "none");
			}
		});
     }

     if(fam_rel[0].substr(0,1) > '1')
	 {
		opt.each(function(e) {
			if(this.value != 2 && this.value != 3) {
				$(this).css("display", "block");
			}else {
				$(this).css("display", "none");
			}
		});
     }

     if(fam_rel[1] && fam_rel[1].substr(0,1) >= 1) {
        opt.each(function(e) {
			if(this.value == 2 || this.value == 3) {
				$(this).css("display", "block");
			}
		});
     }
     
 
}
function getPremium() {
    $(".ti-eye").show();
    $("#premiumBif").css("pointer-events","auto");
    // var patPremium = $("#patFamilyConstruct :selected").attr("data-premium");
    $.post("/get_premium_new", { "sum_insured": $("#sum_insures").val(), "policy_detail_id": $("#sum_insures :selected").attr("data-policyno"), "family_construct": $("#patFamilyConstruct").val() }, function (e) {
        $("#premiumModalHidden").val(e);
        e = JSON.parse(e);
        var premium = 0;
        $("#premiumModalBody").html("");

        e.forEach(function (e1) {
            str = "<div style='display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;'><div><span style='color:#da8085;'> Name:  " + "<br/></span><span style='color:#000;'>" + e1.policy_sub_type_id + "</span></div>";
            str += "<div><span style='color:#da8085;'> Sum Insured </span>" + "<br/><span style='color:#000;'>" + e1.sum_insured + "</span></div>";
            str += "<div><span style='color:#da8085;'> Premium" + "</span><br/><span style='color:#000;'>" + parseFloat(e1.PremiumServiceTax).toFixed(2) + "</span></div></div>";
            // $("#premiumModalBody").append("<div style='display:flex;'><span style='width:35px;'> Name:  " + "<br/>" + e1.policy_sub_type_id + "</span>" );
            // $("#premiumModalBody").append();
            $("#premiumModalBody").append(str);
            premium += parseFloat(e1.PremiumServiceTax);
        });
        
        $("#premium").val(premium.toFixed(2));
    });
}

$(document).on("click", "input[type='radio']", function (e) {

    var product = $('#masPolicy').val();

    var id = e.target.id;

    var value = $(this).val();
    var mycontent = $('.mycontent').val();

    if (value == 'Yes') {
        var id = e.target.id;
    }

});


$.ajax({
    url: "/get_all_doc_type",
    type: "POST",
    async: false,
    dataType: "json",
    success: function (response) {

        // debugger;
        $("#docs_type_master").empty();
        $("#docs_type_master").append("<option value = ''>Select Id Type</option>");
        for (i = 0; i < response.length; i++) {

            $("#docs_type_master").append("<option  value =" + response[i]['master_doc_id'] + ">" + response[i]['doc_type'] + "</option>");


        }
        $("#docLabel").html("Customer Declaration Form <span style='color:#FF0000'>*</span>");
        //$("#auto_renewal").prop("checked", true);
        //$("#auto_renewal").css("pointer-events", "none");
        //$("#docLabelCheckBox").html("Cheque Copy");
        // $("#renewals").show();
    }
});
function addDependentForm(tbody, elem) {
	 
    var edit = $("#"+tbody).closest("form").find("input[name='edit']");
    console.log("edit", edit);
    edit.val(0);
    var addDependent = null;
	var premium = 0;
    // debugger;
    // alert(elem);
	
    $("#" + tbody).html("");
    elem.data.forEach(function (e) {
        if (e.message) {
            addDependent = {
                "message": e.message,
                "premium": e.new_premium,
            }
        }
		premium = e.policy_mem_sum_premium;
        var str = "<tr>";
        str += "<td>" + e.relationship + "</td>";
        str += "<td>" + e.firstname + "</td>";
        str += "<td>" + e.lastname + "</td>";
        str += "<td>" + e.gender + "</td>";
        str += "<td>" + e.dob + "</td>";
        str += "<td>" + e.age + "</td>";
        str += "<td>" + e.age_type + "</td>";

        

        if(e.fr_id == 0) {
            
            str += "<td></td>";

        }else {
            str += "<td><button class='btn-none-xd'  type='button' style='border: 2px solid #da9089;background: #fff; padding: 3px 12px;border-radius: 50px;font-weight: 600;' data-tableId='" + tbody + "' data-emp-id=" + emp_id + " data-policy-member-id=" + e.policy_member_id + " onclick='editPopulate(this)' ><i class='ti-marker-alt del_xd_new'></i></td>";
        }

        str += "<td><button class='btn-none-xd' type='button' style='border: 2px solid red;background: #fff; padding: 3px 12px;border-radius: 50px;font-weight: 600;' data-tableId='" + tbody + "' data-emp-id=" + emp_id + " data-policy-member-id=" + e.policy_member_id + " onclick='delPopulate(this)'><i class='ti-trash del_xd_new'></i></td>";
        str += "</tr>";

       
        $("#" + tbody).append(str);
    });
    
    if (addDependent && addDependent.message && addDependent.premium) {
        swal("Alert", addDependent.message, "warning");
        $("#premium").val(addDependent.premium)
    }

    if($("#patTable tr").length == 0) {
        $("#refreshEdit").val("1");
    }

    if($("#patTable tr").length > 0 && Number($("#premium").val()) == 0) {
        $("#premium").val(premium)
    }
}

// $("#delBtnTable").on("click", function() {
//     console.log($(this).attr("data-emp-id"))
// });

function changes(e) {

    selectChange = $(e).closest(".row");

    var mem_id = [];
    getgender(e);

    var parent_id = $(e).val();

    if (parent_id == 4 || parent_id == 5 || parent_id == 6 || parent_id == 7) {
        $.ajax({
            type: "POST",
            url: "/employee/policy_parent_data",
            data: {
                parent_id: parent_id
            },
            dataType: "json",
            success: function (result) {
                console.log(result);
                if (
                    result.employee_contri > 0 &&
                    result.employee_contri != 0 &&
                    result.flex_allocate != "" &&
                    result.payroll_allocate != ""
                ) {
                    $("#payment_parent_wrapper").removeAttr("style", "display:none");
                    $("#premium_parent").val(result.premium);
                    $("#contri_parent").val(result.employee_contri);
                } else {
                    $("#premium_parent").val(result.premium);
                    $("#contri_parent").val(result.employee_contri);
                }
            }
        });
    }
    else {
        $("#payment_parent_wrapper").attr('style', 'display:none');
    }

    $.ajax({
        url: "/home/get_family_details_from_relationship",
        type: "POST",
        data: {
            relation_id: $(e).val(),
            emp_id: emp_id
        },
        async: false,
        dataType: "json",
        success: function (response) {
            // if($("#family_members_id1").val() == 0) {
            //     $("#patForm").css("pointer-events", "none");
            // }else {
            //     $("#patForm").css("pointer-events", "auto");
            // }
            //  debugger;
            // $('#family_gender1').show();
            // $('#show_data').hide();

            //console.log(response);
            selectChange.find("#first_name1").val("");
            selectChange.find("#last_name1").val("");
            selectChange.find("#family_id").val("");
            selectChange
                .find("input[type='text'][name='family_date_birth']")
                .val("");
            selectChange.find("#age1").val("");
            selectChange.find("#age_type1").val("");
            selectChange.find("#family_gender1").val("");
            selectChange.find("div .disable").attr("style", "display:none");
            selectChange.find("div .unmarried").attr("style", "display:none");
            var family_detail = response.family_data;
            if (true) {

                // if (family_detail[0].fr_id == "2" || family_detail[0].fr_id == "3") {
                //     $("#body_modal").html("");
                //     for ($i = 0; $i < family_detail.length; $i++) {
                //         $("#body_modal").append(
                //             '<input type="radio" name ="radio_option" value= ' +
                //             family_detail[$i]["family_id"] +
                //             "> " +
                //             family_detail[$i].family_firstname +
                //             "<br>"
                //         );
                //     }
                //     //$("#myModal").modal();
                // } else
					
				var genders = $("#gender1").val();
				alert(genders);
                if (family_detail.length != 0) {
                    if (family_detail[0].fr_id == "0") {
                        selectChange
                            .find("#first_name1")
                            .val(family_detail[0].emp_firstname);
                        selectChange.find("#last_name1").val(family_detail[0].emp_lastname);
                        selectChange.find("#family_id").val(family_detail[0].family_id);
                        selectChange
                            .find("input[type='text'][name='family_date_birth']")
                            .val(family_detail[0].bdate);
                        selectChange
                            .find("input[name='marriage_date']")
                            .val(family_detail[0].marriage_date);
                        // selectChange
                        //     .find("select[name='family_gender']")
                        //     .val(family_detail[0].gender);

                        if($("#gender1").val()=='')
						{
							
							selectChange.find('[name="family_gender"]').html("<option value=''>Select</option>");
						}
						else if(genders!=family_detail[0].gender)
						{
							selectChange
                        .find("select[name='family_gender']")
                        .html("<option selected='selected' value='"+genders+"' >"+genders+"</option>");
							
						}
						else
						{
							  selectChange
                        .find("select[name='family_gender']")
                        .html("<option selected='selected' value='"+family_detail[0].gender+"' >"+family_detail[0].gender+"</option>");
							
						}
                        // $('#family_gender1').hide();
                        // $('#show_data').show();
                        // $("#show_data").attr("readonly", true);
                        // $('#show_data').val(family_detail[0].gender);



                        get_age_family(family_detail[0].bdate, family_detail[0].family_id);
                    }
                    else {
                        var gen = $("#family_members_id1 :selected").html();
                        var dataOpt = $("#family_members_id1 :selected").attr("data-opt");

                        if (gen == 'Spouse') {


                            if ($("#gender1").val() == 'Male') {

                                $('#family_gender1').html("<option value=''>Select</option><option selected='selected  value='Female'>Female</option>");

                            }
                            else {
                                $('#family_gender1').html("<option value=''>Select</option><option selected='selected value='Male'>Male</option>");
                            }
                        }
                        else {

                            $('#family_gender1').html("<option value=''>Select</option><option selected='selectedvalue='" + dataOpt + "'>" + dataOpt + "</option>");

                        }

                        // selectChange
                        //     .find("#first_name")
                        //     .val(family_detail[0].family_firstname);
                        // selectChange
                        //     .find("#last_name")
                        //     .val(family_detail[0].family_lastname);
                        // selectChange.find("#family_id").val(family_detail[0].family_id);

                        // selectChange
                        //     .find("input[type='text'][name='family_date_birth']")
                        //     .val(family_detail[0].family_dob);
                        // selectChange
                        //     .find("input[name='marriage_date']")
                        //     .val(family_detail[0].marriage_date);
                        // get_age_family(
                        //     family_detail[0].family_dob,
                        //     family_detail[0].family_id
                        // );
                    }
                }

                else {
                    // debugger;
                    var gen = $("#family_members_id1 :selected").html();
                    var dataOpt = $("#family_members_id1 :selected").attr("data-opt");

                    if (gen == 'Spouse/Partner') {


                        if ($("#gender1").val() == 'Male') {

                            $('#family_gender1').html("<option value=''>Select</option><option selected='selected value='Female'>Female</option>");

                        }
                        else {
                            $('#family_gender1').html("<option value=''>Select</option><option selected='selected  value='Male'>Male</option>");
                        }
                    }
                    else {

                        $('#family_gender1').html("<option value=''>Select</option><option selected='selected  value='" + dataOpt + "'>" + dataOpt + "</option>");

                    }

                    selectChange
                        .find("#first_name")
                        .val(family_detail[0].family_firstname);
                    selectChange
                        .find("#last_name")
                        .val(family_detail[0].family_lastname);
                    selectChange.find("#family_id").val(family_detail[0].family_id);

                    selectChange
                        .find("input[type='text'][name='family_date_birth']")
                        .val(family_detail[0].family_dob);
                    selectChange
                        .find("input[name='marriage_date']")
                        .val(family_detail[0].marriage_date);
                    get_age_family(
                        family_detail[0].family_dob,
                        family_detail[0].family_id
                    );
                }
            }
        }
    });
};
var selectChange = "";
var eValue = "";
var son_age = 0;
var tpa_id = "";
var emp_id = $("#empIdHidden").val() || "";
var parent_id = $("#parentIdHidden").val();




function get_all_policy_data() {
    $.ajax({
        url: "/employee/get_all_policy_data",
        type: "POST",
        dataType: "json",
        data: {
            "parent_id": parent_id,
            "emp_id": emp_id
        },
        success: function (response) {
            $("#sum_insures1").css("pointer-events", "none");
            $("#vtlFamilyConstruct").css("pointer-events", "none");
            $("#premium1").css("pointer-events", "none");

            var sum_insures1 = $("#sum_insures1").val();
            var vtlFamilyConstruct = $("#vtlFamilyConstruct").val();
            var premium1 = $("#premium1").val();

            document.getElementById("vtlForm").reset();

            // $("#sum_insures1").val(sum_insures1);
            // $("#vtlFamilyConstruct").val(vtlFamilyConstruct);
            // $("#premium1").val(premium1);

            if (response.constructor == String) {

                addDependentForm("vtlTable", JSON.parse(response));
            } else {
                var patTable = {};
                patTable.data = [];
                var vtlTable = {};
                vtlTable.data = [];
                // console.log(response.data);
                // debugger;
                if(response && response.data && response.data.length > 0) {
                    $("#sum_insures").val(response.data[0]['policy_mem_sum_insured']);
                    
                    // $('#sum_insures').change();
                    setFamilyConstruct(response.data[0]['familyConstruct']);
                    $("#sum_insures").css("pointer-events", "none");
                    $("#patFamilyConstruct").css("pointer-events", "none");
                }
                // $('#patFamilyConstruct').val(response.data[0]['familyConstruct']);
                
                for (var i = 0; i < response.data.length; i++) {
                    
                    if (response.data[i]['policy_sub_type_id'] == 3) {
                        vtlTable.data.push(response.data[i]);

                    } else if (response.data[i]['policy_sub_type_id'] == 1) {
                        patTable.data.push(response.data[i]);
                    }
                }

                addDependentForm("patTable", patTable);
                addDependentForm("vtlTable", vtlTable);
            }

            // console.log(response);
        }
    });
}

function get_other_data(prop_no, proposal_id) {

    $.ajax({
        url: "/employee/get_other_data",
        type: "POST",
        dataType: "json",
        data: {
            "emp_id": emp_id,
            "parent_id": parent_id
        },
        success: function (response) {

            if (response.question) {
                $.each(response.question, function (k, v) {
                    $('input:radio[name="' + v.content + '"][value="' + v.format + '"]')
                        .attr('checked', true);


                    $.ajax({
                        type: "POST",
                        url: "/employee/declarelabel_comment",
                        data: {
                            parent_id: parent_id,
                            declare_id: v.p_declare_id
                        },
                        dataType: "html",
                        success: function (result) {
                            // debugger;
                            // console.log(result);
                            $('.' + v.p_declare_id).html(result);
                            $('#' + v.p_declare_id + '_test').val(v.remark);
                        }
                    });

                });
            }


            if (response.document) {

                $.each(response.document, function (k, v) {
                    $('#show_tbl').show();

                    $("#editDocTable").append("<tr><td>" + v.ops_type + "</td><td><a target='_blank' href='" + v.path + "'><img onError='/public/images/pdf1' style='width:50px;' src='" + v.path + "' /></a></td><td><button class='btn-none-xd' data-id='" + v.id + "'onclick='deleteThis(this);'><i class='ti-trash del_xd_new'></i></button></td></tr>")

                    $("#ops_type").val(v.ops_type);

                });
            }

            if (response.payment) {
                $.each(response.payment, function (k, v) {

                    // $("#bankName").val(v.bank_name);
                    // $("#branchName").val(v.branch);
                    // $("#cheque_no").val(v.cheque_no);
                    // $("#bankAcNo").val(v.account_no);
                    // $("#cheque_date").val(v.cheque_date);
                    ids.push(v.id);
                });

                $("#bankId").val("1");

            }

        }

    });

}

function get_nominee_data() {
    $.ajax({
        url: "/employee/get_all_nominee",
        type: "POST",
        dataType: "json",
        data: {
            "emp_id": emp_id
        },
        success: function (response) {
            if (response) {
                $.each(response, function (index, value) {
                    var tag = $("#hidden_tag").val();
                    if (tag == 'yes') {
                        $("#table_tbody").append('<tr><th scope="row"><div class="form-group"><input class="form-control align-center rel_nominee" autocomplete="off" type="text" class="rel_nominee" id="rel_nominee" name="rel_nominee" value="' + value.fr_id + '" readonly></div></th><td><div class="form-group"><input autocomplete="off" class="form-control align-center nominee_firstname" type="text" name="nominee_firstname" class="nominee_firstname" value="' + value.nominee_fname + '" id="nominee_firstname" readonly></div></td><td><div class="form-group"><input autocomplete="off" class="form-control align-center nominee_lastname" type="text" name="nominee_lastname" class="nominee_lastname" value="' + value.nominee_lname + '" id="nominee_lastname" readonly></div></td><td><div class="form-group"><input autocomplete="off" class="form-control align-center nominee_date" name="nominee_date" class="nominee_date" value="' + value.nominee_dob + '" readonly></div></td><td>' + value.share_percentile + '%</td><td><div class="form-group"><input autocomplete="off" class="form-control align-center guardian_name" type="text" name="guardian_name" class="guardian_name" value="' + value.guardian_fname + '" id="guardian_name" readonly></div></td><td><div class="form-group"><input autocomplete="off" class="form-control align-center guardina_date" name="guardina_date" value="' + value.guardian_dob + '" readonly></div></td><td><div class="form-group"><input class="form-control align-center guardian_rel" autocomplete="off" type="text" name="guardian_rel" class="guardian_rel" value="' + value.guardian_relation + '" id="guardian_rel" readonly></div></td><td hidden><div class="form-group"> <input class="form-control align-center" type="hidden"  id="share_per" value="' + value.share_percentile + '" name="share_per" readonly=readonly> </div></td><td><button type="button" onclick="update_func(' + value.nominee_id + ')" name="edit_btn" id="update_record_' + value.nominee_id + '" class="btn update_record" value="update_record">Edit</button><button hidden type="button" id="update_nominee_btn_' + value.nominee_id + '" name="update_btn" onclick="update_nominee_data(' + value.nominee_id + ')" class="btn update_btn">Update</button></td><td><button type="button" id="delete_btn_' + value.nominee_id + '" onclick="delete_func(' + value.nominee_id + ')" class="btn delete_btn">Delete</button></td></tr>');
                    } else {
                        $("#table_tbody").append('<tr><th scope="row"><div class="form-group"><input class="form-control align-center rel_nominee" autocomplete="off" type="text" class="rel_nominee" id="rel_nominee" name="rel_nominee" value="' + value.fr_id + '" readonly></div></th><td><div class="form-group"><input autocomplete="off" class="form-control align-center nominee_firstname" type="text" name="nominee_firstname" class="nominee_firstname" value="' + value.nominee_fname + '" id="nominee_firstname" readonly></div></td><td><div class="form-group"><input autocomplete="off" class="form-control align-center nominee_lastname" type="text" name="nominee_lastname" class="nominee_lastname" value="' + value.nominee_lname + '" id="nominee_lastname" readonly></div></td><td><div class="form-group"><input autocomplete="off" class="form-control align-center nominee_date" name="nominee_date" class="nominee_date" value="' + value.nominee_dob + '" readonly></div></td><td>' + value.share_percentile + '%</td><td><div class="form-group"><input autocomplete="off" class="form-control align-center guardian_name" type="text" name="guardian_name" class="guardian_name" value="' + value.guardian_fname + '" id="guardian_name" readonly></div></td><td><div class="form-group"><input autocomplete="off" class="form-control align-center guardina_date" name="guardina_date" value="' + value.guardian_dob + '" readonly></div></td><td><div class="form-group"><input class="form-control align-center guardian_rel" autocomplete="off" type="text" name="guardian_rel" class="guardian_rel" value="' + value.guardian_relation + '" id="guardian_rel" readonly></div></td><td hidden><div class="form-group"> <input class="form-control align-center" type="hidden"  id="share_per" value="' + value.share_percentile + '" name="share_per" readonly=readonly> </div></td></tr>');


                    }
                });
            }
        }
    });
}
function get_adhar_mask(str) {

    if (/[,\-]/.test(str)) {
        var k = str.replace(/-/g, "");
        var strs = k.replace(/\w(?=\w{4})/g, "X");
        var finalVal = strs.match(/.{1,4}/g).join('-');
        return finalVal;
    }
    else {

        var finalVal = str.replace(/\w(?=\w{4})/g, "X");


        return finalVal;
    }
}
function get_profile_data() {
    $.ajax({
        url: "/employee/get_employee_data_new",
        type: "POST",
        async: false,
        data: {
            "emp_id": emp_id
        },
        dataType: "json",
        success: function (response) {
            // debugger;
             console.log(response);
            var adhar = get_adhar_mask(response.adhar);


            $("#firstname").val(response.emp_firstname);
            $("#lastname").val(response.emp_lastname);
            $("#panCard").val(response.pancard);
            try {
                $("#addCard").val(adhar);
            }catch(e) {

            }
            $("#lead_id_product").html(response.lead_id);
            $("#comAdd").val(response.address);
            $("#perAdd").val(response.comm_address);
            $("#gender1").val(response.gender);
            $("#dob").val(response.bdate);
            $("#ref1").val(response.ref2);
            $("#ref2").val(response.ref1);
            $("#ISNRI").val(response.ISNRI);
            $("#mob_no").val(response.mob_no);
            $("#email").val(response.email);
			console.log(response.salutation);
	
		$("#salutation option").filter(function(){
    return $(this).text() === response.salutation ? $(this).prop("selected", true) : false;
});
		  
            $("#marital_status").val('');
            $("#pin_code").val(response.emp_pincode);
            $("#city").val(response.emp_city);
            $("#state").val(response.emp_state);
            $("#ifscCode").val(response.ifsc_code);
            // $("#alt_email").val(response.alt_email);
            // $("#emg_contact_per").val(response.emp_emg_cont_name);
            // $("#emg_contact_num").val(response.emg_cno);
            // $("#designation").val(response.designation_name);

            var json_quote = JSON.parse(response.json_qote);

            $('#bankAcNo').val(json_quote['ACCOUNTNUMBER']);
			debugger;
			if(json_quote['AXISBANKACCOUNT'] == 'N')
			{
				$("#bank_branch_name").show();
				$("#bankBranch").attr("readonly", false); 
				$("#ifscCode").attr("readonly", false); 
				$("#bankName").attr("readonly", false); 
				$("#bankAcNo").attr('readonly',false);
				$("#ifscCode").val(''); 
				$("#bankName").val(''); 
				$('#cheque_dates').show();
				$('#cheque_number').show();
				
				$('#ifscCode').removeClass('ignore');
				$('#cheque_no').removeClass('ignore');
				$('#micr_no').removeClass('ignore');
				$('#cheque_date').removeClass('ignore');
				$('#cheque_date').removeClass('ignore');
				$('#micr_number').show();
				$('#cheque_types').show();
				$('.cheque_up').show();
				$('#payment_types').show();
				
				
			}
			else
			{
				$("#payment_type").attr("value","");
				$("#cheque_type").attr("value","");
				 $('.cheque_up').hide();
				$("#bank_branch_name").hide();
				$('#cheque_dates').hide();
				$('#cheque_types').hide();
				$('#payment_types').hide();
				$('#cheque_number').hide();
				$('#micr_number').hide();
				$('#payment_type').val('');
				$('#cheque_type').val('');
				   getIfscCode(response.ifsc_code);
				  
			}
            //$('#cheque_date').val(json_qote['ACCOUNTNUMBER']);
            //$('#cheque_no').val(json_qote['ACCOUNTNUMBER']);

            if (response.ISNRI == 'Y') {
                $("#comAdd").attr("readonly", false);
            }

         

        }
    });
}



function ecard_link(member_name, member_id, policy_no, status, tpa_id) {
    if (status == 1) {
        $.ajax({
            url: "/get_ecard",
            type: "POST",
            data: {
                policy_no: policy_no,
                member_name: member_name,
                tpa_id: tpa_id,
                member_id: member_id
            },
            async: false,
            // dataType: "json",
            success: function (response) {
                if (response) {
                    var response = JSON.parse(response);
                    if (response.tpa_id == "2") {
                        //var ecard_link_url = response.sBody.EcardResponse.EcardData.EcardData[0].EcardLink.toString();
                        window.location.replace(response.response);
                    } else if (response.tpa_id == "4") {
                        window.location.replace(response.response);
                    } else {
                        //var response =  JSON.parse(response);
                        var ecard_link_url = response.sBody.EcardResponse.EcardData.EcardData.EcardLink.toString();
                        window.location.replace(ecard_link_url);
                    }
                }
            }
        });
    } else {
        swal("", "Policy not issue");
    }
}
$(document).ready(function () {



    $("#cheque_date").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
		minDate: '-90',
		maxDate: new Date(),
        changeMonth: true,
        changeYear: true
    });


    //employeedob datepicker
    // $("#dob").datepicker({
    // dateFormat: "dd-mm-yy",
    // prevText: '<i class="fa fa-angle-left"></i>',
    // nextText: '<i class="fa fa-angle-right"></i>',
    // changeMonth: true,
    // changeYear: true,
    // minDate: "-100Y",
    // maxDate: "-18Y",
    // yearRange: "-100Y:-18Y"
    // });
    // $("#date_join").datepicker({
    //   dateFormat: "dd-mm-yy",
    //   prevText: '<i class="fa fa-angle-left"></i>',
    //   nextText: '<i class="fa fa-angle-right"></i>',
    //   changeMonth: true,
    //   changeYear: true
    // });

    //adhar validation
    $("#addCard").keyup(function (e) {
        var $th = $(this);
        if (
            e.keyCode != 46 &&
            e.keyCode != 8 &&
            e.keyCode != 37 &&
            e.keyCode != 38 &&
            e.keyCode != 39 &&
            e.keyCode != 40
        ) {
            $th.val(
                $th.val().replace(/[^0-9]/g, function (str) {
                    return "";
                })
            );
        }
        return;
    });

    $(document).on("click", "#payment_redirection", function () {
        $("#payment").submit();
    });
    $(document).on("change", "#masPolicy", function () {
        // debugger;

        $("#resets").css("display", "none");

        var policy_detail_id = $("#masPolicy option:selected").text();
        $("#pr_name").text(policy_detail_id);

        var policy_id = $("#masPolicy option:selected").val();

        if (policy_id != '') {
            $.ajax({
                url: "/get_declaration",
                type: "POST",
                data: {
                    policy_id: policy_id
                },
                async: false,
                dataType: "html",
                success: function (data) {
                    $('#policy_declare').html(data);
                }


            });
        }


        //customer search
        $.ajax({
            url: "/get_all_policy_datsa",
            type: "POST",
            data: {
                product_name: policy_detail_id
            },
            async: false,
            dataType: "json",
            success: function (response) {
                //console.log(response);
                // debugger;
                $("#family_members_id").empty();
                $("#benifit_3s").css("display", "none");
                $("#benifit_4s").css("display", "none");
                $("#benifit_5s").css("display", "none");
                $("#family_members_id1").empty();
                $("#family_members_id2").empty();
                if (response.length > 0) {
                    $("#family_members_id").append(
                        "<option value = ''>Select Relation</option>"
                    );
                    $("#family_members_id1").append(
                        "<option value = ''>Select Relation</option>"
                    );
                    $("#family_members_id2").append(
                        "<option value = ''>Select Relation</option>"
                    );

                    for (i = 0; i < response.length; i++) {

                        if (response[i].policy_sub_type_id == 1) {

                            $(".personal_accident").text(response[i].policy_sub_type_name);
                            $("#benifit_4s").css("display", "block");
                            // $("#benifit_3s").css("display", "none");
                            $("#subtype_text").val(response[i].policy_sub_type_id);

                            if (
                                (response[i].max_adult > 1 && response[i].fr_id == 1) ||
                                response[i].fr_id == 0
                            ) {
                                $("#family_members_id1").append(
                                    '<option data-opt="' +
                                    response[i].gender_option +
                                    '" value="' +
                                    response[i].fr_id +
                                    '">' +
                                    response[i].fr_name +
                                    "</option>"
                                );
                            } else if (
                                response[i].max_adult == 2 &&
                                response[i].max_child == 0 &&
                                (response[i].fr_id == 4 ||
                                    response[i].fr_id == 5 ||
                                    response[i].fr_id == 6 ||
                                    response[i].fr_id == 7)
                            ) {
                                $("#family_members_id1").append(
                                    '<option data-opt="' +
                                    response[i].gender_option +
                                    '" value="' +
                                    response[i].fr_id +
                                    '">' +
                                    response[i].fr_name +
                                    "</option>"
                                );
                            } else if (
                                response[i].max_adult == 4 &&
                                response[i].max_child == 0 &&
                                (response[i].fr_id == 4 ||
                                    response[i].fr_id == 5 ||
                                    response[i].fr_id == 6 ||
                                    response[i].fr_id == 7)
                            ) {
                                $("#family_members_id1").append(
                                    '<option data-opt="' +
                                    response[i].gender_option +
                                    '" value="' +
                                    response[i].fr_id +
                                    '">' +
                                    response[i].fr_name +
                                    "</option>"
                                );
                            } else if (
                                response[i].max_adult > 2 &&
                                (response[i].fr_id == 4 ||
                                    response[i].fr_id == 5 ||
                                    response[i].fr_id == 6 ||
                                    response[i].fr_id == 7 ||
                                    response[i].fr_id == 1 ||
                                    response[i].fr_id == 0)
                            ) {
                                $("#family_members_id1").append(
                                    '<option data-opt="' +
                                    response[i].gender_option +
                                    '" value="' +
                                    response[i].fr_id +
                                    '">' +
                                    response[i].fr_name +
                                    "</option>"
                                );
                            } else if (response[i].fr_id == 2 || response[i].fr_id == 3) {
                                $("#family_members_id1").append(
                                    '<option data-opt="' +
                                    response[i].gender_option +
                                    '" value="' +
                                    response[i].fr_id +
                                    '">' +
                                    response[i].fr_name +
                                    "</option>"
                                );
                            }
                        }

                        if (response[i].policy_sub_type_id == 2) {

                            $("#benifit_3s").css("display", "block");
                            $(".group_mediclaim").text(response[i].policy_sub_type_name);
                            $("#subtype_text2").val(response[i].policy_sub_type_id);
                            if (
                                (response[i].max_adult > 1 && response[i].fr_id == 1) ||
                                response[i].fr_id == 0
                            ) {
                                $("#family_members_id").append(
                                    '<option data-opt="' +
                                    response[i].gender_option +
                                    '" value="' +
                                    response[i].fr_id +
                                    '">' +
                                    response[i].fr_name +
                                    "</option>"
                                );
                            } else if (
                                response[i].max_adult == 2 &&
                                response[i].max_child == 0 &&
                                (response[i].fr_id == 4 ||
                                    response[i].fr_id == 5 ||
                                    response[i].fr_id == 6 ||
                                    response[i].fr_id == 7)
                            ) {
                                $("#family_members_id").append(
                                    '<option data-opt="' +
                                    response[i].gender_option +
                                    '" value="' +
                                    response[i].fr_id +
                                    '">' +
                                    response[i].fr_name +
                                    "</option>"
                                );
                            } else if (
                                response[i].max_adult == 4 &&
                                response[i].max_child == 0 &&
                                (response[i].fr_id == 4 ||
                                    response[i].fr_id == 5 ||
                                    response[i].fr_id == 6 ||
                                    response[i].fr_id == 7)
                            ) {
                                $("#family_members_id").append(
                                    '<option data-opt="' +
                                    response[i].gender_option +
                                    '" value="' +
                                    response[i].fr_id +
                                    '">' +
                                    response[i].fr_name +
                                    "</option>"
                                );
                            } else if (
                                response[i].max_adult > 2 &&
                                (response[i].fr_id == 4 ||
                                    response[i].fr_id == 5 ||
                                    response[i].fr_id == 6 ||
                                    response[i].fr_id == 7 ||
                                    response[i].fr_id == 1 ||
                                    response[i].fr_id == 0)
                            ) {
                                $("#family_members_id").append(
                                    '<option data-opt="' +
                                    response[i].gender_option +
                                    '" value="' +
                                    response[i].fr_id +
                                    '">' +
                                    response[i].fr_name +
                                    "</option>"
                                );
                            } else if (response[i].fr_id == 2 || response[i].fr_id == 3) {
                                $("#family_members_id").append(
                                    '<option data-opt="' +
                                    response[i].gender_option +
                                    '" value="' +
                                    response[i].fr_id +
                                    '">' +
                                    response[i].fr_name +
                                    "</option>"
                                );
                            }
                        }

                        if (response[i].policy_sub_type_id == 3) {
                            $(".voluntary_term").text(response[i].policy_sub_type_name);
                            $("#benifit_5s").css("display", "block");
                            $("#subtype_text3").val(response[i].policy_sub_type_id);

                            if (
                                (response[i].max_adult > 1 && response[i].fr_id == 1) ||
                                response[i].fr_id == 0
                            ) {
                                $("#family_members_id2").append(
                                    '<option value="' +
                                    response[i].fr_id +
                                    '">' +
                                    response[i].fr_name +
                                    "</option>"
                                );
                            } else if (
                                response[i].max_adult == 2 &&
                                response[i].max_child == 0 &&
                                (response[i].fr_id == 4 ||
                                    response[i].fr_id == 5 ||
                                    response[i].fr_id == 6 ||
                                    response[i].fr_id == 7)
                            ) {
                                $("#family_members_id2").append(
                                    '<option value="' +
                                    response[i].fr_id +
                                    '">' +
                                    response[i].fr_name +
                                    "</option>"
                                );
                            } else if (
                                response[i].max_adult == 4 &&
                                response[i].max_child == 0 &&
                                (response[i].fr_id == 4 ||
                                    response[i].fr_id == 5 ||
                                    response[i].fr_id == 6 ||
                                    response[i].fr_id == 7)
                            ) {
                                $("#family_members_id2").append(
                                    '<option value="' +
                                    response[i].fr_id +
                                    '">' +
                                    response[i].fr_name +
                                    "</option>"
                                );
                            } else if (
                                response[i].max_adult > 2 &&
                                (response[i].fr_id == 4 ||
                                    response[i].fr_id == 5 ||
                                    response[i].fr_id == 6 ||
                                    response[i].fr_id == 7 ||
                                    response[i].fr_id == 1 ||
                                    response[i].fr_id == 0)
                            ) {
                                $("#family_members_id2").append(
                                    '<option value="' +
                                    response[i].fr_id +
                                    '">' +
                                    response[i].fr_name +
                                    "</option>"
                                );
                            } else if (response[i].fr_id == 2 || response[i].fr_id == 3) {
                                $("#family_members_id2").append(
                                    '<option value="' +
                                    response[i].fr_id +
                                    '">' +
                                    response[i].fr_name +
                                    "</option>"
                                );
                            }
                        }
                    }
                }
            }
        });

        var customer = $("#masPolicy option:selected").attr("data-customer");
        if (policy_detail_id && customer == "Y") {
            $("#customer_serach_button").show();
        } else {
            $("#customer_serach_button").hide();
        }
        if (policy_detail_id) {
            $("#subtypediv").show();
            $("#policySubType").removeClass("ignore");
        } else {
            $("#subtypediv").hide();
            $("#policySubType").addClass("ignore");
        }
        if (customer == "Y") {
            $("#custDetDiv").show();
            $("#cusDet").removeClass("ignore");

            //get value from mapping table
            /* $.ajax({
              url: "/get_customer_serach",
              type: "POST",
              async: false,
              dataType: "json",
              data: {
                policy_detail_id: policy_detail_id
              },
              success: function(response) {
                $("#cusDet").empty();
                $("#cusDet").append(
                  "<option value = ''>Select Serach Value</option>"
                );
                for (i = 0; i < response.length; i++) {
                  $("#cusDet").append(
                    "<option  value =" +
                      response[i]["id"] +
                      ">" +
                      response[i]["display_value"] +
                      "</option>"
                  );
                }
              }
          }); */
        } else {
            $("#custDetDiv").hide();
            $("#cusDet").addClass("ignore");
        }

        $("#policySubType").val($("#masPolicy option:selected").attr("data-id"));
        //$("#policy_no").val($('#masPolicy option:selected').val());
        var policy_no = $("#masPolicy option:selected").val();
        var policy_details_id = $("#masPolicy option:selected").text();

        $.ajax({
            url: "/employee/get_all_parent_gmc",
            type: "POST",

            data: {
                product_name: policy_details_id
            },

            async: false,
            dataType: "json",
            success: function (response) {
                //var reponse = JSON.parse(response);
                // console.log(response);
                if (response.length != "") {

                    $("#sum_insure").empty();
                    $("#sum_insures").empty();
                    $("#sum_insures1").empty();
                    $("#sum_insure").append(
                        "<option value = ''>Select Sum insured</option>"
                    );
                    $("#sum_insures").append(
                        "<option value = ''>Select Sum insured</option>"
                    );
                    $("#sum_insures1").append(
                        "<option value = ''>Select Sum insured</option>"
                    );


                    console.log(response);
                    var arr = [];
                    for (i = 0; i < response.length; ++i) {

                        // debugger;
                        if (response[i].flate) {

                            response[i].flate.forEach(function (e) {
                                var sumInsured = e["sum_insured"].split(",");
                                var PremiumServiceTax = e["PremiumServiceTax"].split(",");

                                setMasPolicy("flate", e);
                            });
                        }

                        if (response[i].family_construct) {

                            // console.log(response[i].family_construct);
                            response[i].family_construct.forEach(function (e) {
                                if (e['combo_flag'] == 'Y') {
                                    if (!arr.includes(e['sum_insured'])) {
                                        arr.push(e['sum_insured']);
                                        setMasPolicy("family_construct", e);
                                    } else {

                                    }
                                }
                                else {
                                    setMasPolicy("family_construct", e);
                                }
                            });
                        }

                        if (response[i].family_construct_age) {

                            // console.log(response[i].family_construct);
                            response[i].family_construct_age.forEach(function (e) {
                                if (e['combo_flag'] == 'Y') {
                                    if (!arr.includes(e['sum_insured'])) {
                                        arr.push(e['sum_insured']);
                                        setMasPolicy("family_construct_age", e);
                                    } else {

                                    }
                                }
                                else {
                                    setMasPolicy("family_construct_age", e);
                                }
                            });
                        }

                        if (response[i].memberAge) {
                            response[i].memberAge.forEach(function (e) {
                                setMasPolicy("memberAge", e);
                            });
                        }
                    }

                    /*$(".s").empty();  
          
          
          if(response.suminsured_type == "parent"){
           $(".parent_si_div").css("display", "block"); 
           $("#premium_type_text").val(response.suminsured_type);
           
            for (i = 0; i < response.gmc_premium.length; i++) {
             
           $(".s").append('<div class="row"><div class="col-md-12"><div class="col-md-12"><div class="custom-control custom-radio"><input type="radio" id="gmc_parent_' + response.gmc_suminsured[i] + '" name="gmc" class=" sum_insured_check" data-id="' + response.gmc_premium[i] + '"  data-ids="' + response.gmc_premium1[i] + '"value="' + response.gmc_suminsured[i] + '"> <label class="" for="gmc_' + response.gmc_suminsured[i] + '"><i class="fa fa-inr"></i> <span id=" "> ' + response.gmc_suminsured[i] + '</span></label></div><div> </div><div>');

                $("#gmc_parent_" + response.gmc_suminsured[i] + "").click(function () {
                  $("#gmc_parent_estimate").html("<i class='fa fa-inr'></i> " + parseInt($(this).attr('data-id')));
                  $("#gmc_parent_estimate").attr("data-value", parseInt($(this).attr('data-id')));                   
                  $("#gmc_parent_estimate1").html("<i class='fa fa-inr'></i> " + parseInt($(this).attr('data-ids')));
                  $("#parent_si").text($(this).val());
                  $("#parent_premium").text($(this).attr('data-id'));
                });
              }
          
         } 
         else if(response.suminsured_type == "flate" && (response.premium_type == "flate1" || response.premium_type == "designation1" || response.premium_type == "age1")){
          
           $(".flat_si_div").css("display", "block"); 
           $("#premium_type_text").val(response.suminsured_type);
           
            for (i = 0; i < response.gmc_suminsured.length; i++) {
             if(response.premium_type == "designation1" || response.premium_type == "age1"){
                 $(".s").append('<div class="row"><div class="custom-control custom-radio"><input type="radio" id="gmc_parent_' + response.gmc_suminsured[i] + '" name="gmc" class=" sum_insured_check"  data-ids="' + response.gmc_premium1[i] + '"value="' + response.gmc_suminsured[i] + '"> <label class="" for="gmc_' + response.gmc_suminsured[i] + '"><i class="fa fa-inr"></i> <span id=" "> ' + response.gmc_suminsured[i] + '</span></label></div><div>');
                          $(".mar-70").css("display","none");
                
             }
             else {
              $(".s").append('<div class="row"><div class="custom-control custom-radio"><input type="radio" id="gmc_parent_' + response.gmc_suminsured[i] + '" name="gmc" class=" sum_insured_check"  data-ids="' + response.gmc_premium1[i] + '"value="' + response.gmc_suminsured[i] + '"> <label class="" for="gmc_' + response.gmc_suminsured[i] + '"><i class="fa fa-inr"></i> <span id=" "> ' + response.gmc_suminsured[i] + '</span></label></div><div>');
                           $("#gmc_parent_" + response.gmc_suminsured[i] + "").click(function () {
                   $("#gmc_parent_estimate1").html("<i class='fa fa-inr'></i> " + parseInt($(this).attr('data-ids')));
                  $("#parent_si").text($(this).val());
                  $("#parent_premium").text($(this).attr('data-id'));
                });
             }
              }
         }
         else if(response.suminsured_type == "family_construct" ){
          
           $(".flat_si_div").css("display", "block"); 
           $("#premium_type_text").val(response.suminsured_type);
           
            for (i = 0; i < response.gmc_suminsured.length; i++) {
            
              $(".s").append('<div class="row"><div class="custom-control custom-radio"><input type="radio" id="gmc_parent_' + response.gmc_suminsured[i] + '" name="gmc" class=" sum_insured_check"  data-ids="' + response.gmc_premium1[i] + '"value="' + response.gmc_suminsured[i] + '"> <label class="" for="gmc_' + response.gmc_suminsured[i] + '"><i class="fa fa-inr"></i> <span id=" "> ' + response.gmc_suminsured[i] + '</span></label></div><div>');
                           $("#gmc_parent_" + response.gmc_suminsured[i] + "").click(function () {
                   $("#gmc_parent_estimate1").html("<i class='fa fa-inr'></i> " + parseInt($(this).attr('data-ids')));
                  $("#parent_si").text($(this).val());
                  $("#parent_premium").text($(this).attr('data-id'));
                });
            
              }
         }
         
         
            else {
           $(".parent_si_div").css("display", "none"); 
          $(".flat_si_div").css("display", "none"); 
      } */
                }
            }
        });
    });

    $.ajax({
        url: "/get_all_policy_numbers",
        type: "POST",
        async: false,
        dataType: "json",
        success: function (response) {
            // debugger;
            $("#masPolicy").empty();
            $("#masPolicy").append(
                "<option value = ''>Select Policy Number</option>"
            );
            for (i = 0; i < response.length; i++) {
                $("#masPolicy").append(
                    "<option data-customer = '" +
                    response[i]["customer_search_status"] +
                    "'  data-id = '" +
                    response[i]["policy_sub_type_id"] +
                    "' value =" + response[i]['parent_policy_id'] + ">" +
                    response[i]["product_name"] +
                    "</option>"
                );
            }

            if (emp_id && parent_id) {

                $("#masPolicy").val(parent_id);
                // $('#masPolicy').change();
                $('#masPolicy').trigger("change");
                get_other_data();
                $('#finalConfirmDiv').show();

            }
        }
    });

    function sortNumber(a, b) {
        return a - b;
    }
      
    function sortSumInsures(id, opt) {
        var selVal = $("#"+id+" : selected").val();
        var vals = $(opt).map(function() {if(this.value) {return this.value};}).get();
        
        vals.sort(sortNumber);
        
        var str = [];
        str.push(opt[0]);
         for(i = 0; i < vals.length; ++i) {
            console.log($("#"+id + " option[value="+vals[i] +"]"))
            str.push($("#"+id + " option[value="+vals[i] +"]")[0]);

        }

        $("#" + id).empty();
        $("#"+id).append(str);

        $("#"+id).val(selVal);
        $("#"+id).change();
    }

    function setMasPolicy(type, e1) {

        var policy =
            e1["policy_sub_type_id"] == 1 ?
                "policy_no" :
                e1["policy_sub_type_id"] == 2 ?
                    "policy_no2" :
                    "policy_no3";
        var id =
            e1["policy_sub_type_id"] == 1 ?
                "sum_insures" :
                e1["policy_sub_type_id"] == 2 ?
                    "sum_insure" :
                    "sum_insures1";


        $("#" + policy).val(e1["policy_detail_id"]);

        if (type == "flate") {
            var sumInsured = e1["sum_insured"].split(",");
            var PremiumServiceTax = e1["PremiumServiceTax"].split(",");
            // var premium =  e1["premium"].split(",");

            for (k = 0; k < sumInsured.length; ++k) {
                $("#" + id).append(
                    "<option data-type='" + type + "' data-policyNo='" + e1["policy_detail_id"] + "' data-customer = '" +
                    premium[k] +
                    "'  data-id = '" +
                    PremiumServiceTax[k] +
                    "' family_child= '" +
                    e1.child +
                    "' family_adult= '" +
                    e1.adult +
                    "' value =" +
                    sumInsured[k] +
                    ">" +
                    sumInsured[k] +
                    "</option>"
                );
            }
        } else {

            if (type == "memberAge") {
                $("#patFamilyConstructDiv").hide();
                e1["PremiumServiceTax"] = e1["premium"];

            }


            $("#" + id).append("<option data-type='" + type + "' data-policyNo='" + e1["policy_detail_id"] + "' data-customer = '" + e1.premium + "'  data-id = '" + e1.PremiumServiceTax + "' family_child= '" + e1.child + "' family_adult= '" + e1.adult + "' value =" + e1.sum_insured + ">" + e1.sum_insured + "</option>");
        }
        // debugger;
        // sortSumInsures("sum_insures", document.querySelectorAll("#sum_insures option"));
    }

    /* $("#patFamilyConstruct").on("change", function() {
        var patPremium = $("#patFamilyConstruct :selected").attr("data-premium");
        $("#premium").val(patPremium);
    }); */

    $("#getMaxPremiumAgeForm").validate({
        ignore: ".ignore",
        rules: {
            getMaxPremiumAge: {
                required: true,
                number: true
            },
        },
        messages: {

        },
        submitHandler: function (form) {
            // debugger;
            var policyNo = $("#sum_insures :selected").attr("data-policyno");
            var sum_insures = $("#sum_insures").val();
            var familyConstruct = $("#patFamilyConstruct").val();
            var maxPremiumAge = $("#getMaxPremiumAge").val();

            // var data = {"policyNo": policyNo, "sum_insures": sum_insures, "familyConstruct": familyConstruct};

            $.post("/get_premium_age", { "policyNo": policyNo, "sum_insures": sum_insures, "familyConstruct": familyConstruct, "maxPremiumAge": maxPremiumAge }, function (e) {
                e = JSON.parse(e);
                $("#premium").val("");
                
                if (e.status) {
                    $("#premium").val(e.premium);
                    $('#getMaxPremiumAgeModal').modal('hide');
                } else {
                    swal("Alert", e.message, "warning");
                }

            })
        }
    });

    function getPremiumByAge(id) {

        $("#premiumBif").css("pointer-events","none");
        $(".ti-eye").hide();
        var edit = $("#"+id).closest("form").find("input[name='edit']").val();
        $("#getMaxPremiumAge").val("");
        
        if($("#refreshEdit").val() == "0") {
            console.log("patTable", $("#patTable"));
            $("#refreshEdit").val("1");
            return;
        }

        if(edit == 0 || edit == "")
            $("#getMaxPremiumAgeModal").modal();
        
        
    }

    $("#patFamilyConstruct").on("change", function () {
        var sumInsuresType = $("#sum_insures :selected").attr("data-type");

        if (sumInsuresType == "family_construct") {
            getPremium();
			getRelation();
        } else if (sumInsuresType == "family_construct_age") {
            getPremiumByAge("patFamilyConstruct");
			getRelation();
        }
    });

    $("#premiumBif").on("click", function () {
        
        if ($("#premiumModalBody").html().trim()) {}
            $("#premiumModal").modal();
    });

    $("#vtlFamilyConstruct").on("change", function () {
        var vtlPremium = $("#vtlFamilyConstruct :selected").attr("data-premium");
        $("#premium1").val(vtlPremium);
    });

    $(document).on("change", "#sum_insures", function () {
        setFamilyConstruct();
        // $("#premium").val(tax_with_premium);
    });
    
    $(document).on("change", "#sum_insure", function () {
        var tax_with_premium = $("#sum_insure option:selected").data("id");

        if ($("#sum_insures :selected").attr("data-type") == "family_construct") {
            $.post("/get_family_construct", {
                "policyNo": $("#sum_insures :selected").attr("data-policyno"),
                "sumInsured": $("#sum_insures :selected").val()
            }, function (e) {
                $("#patFamilyConstruct").empty();
                $("#patFamilyConstruct").html('<option value="" selected>Select</option>');
                e = JSON.parse(e);
                $("#patFamilyConstructDiv").show();
                if (e) {
                    console.log(e);
                    e.forEach(function (e1) {
                        // console.log(e1);
                        $("#patFamilyConstruct").append("<option value='" + e1.family_type + "' data-premium='" + e1.PremiumServiceTax + "'>" + e1.family_type + "</option>");
                    });

                    // $("#patFamilyConstruct").change();
                }
            });


        } else {
            $("#patFamilyConstructDiv").hide();
        }

        // $("#premium2").val(tax_with_premium);
    });

    $(document).on("change", "#sum_insures1", function () {
        var tax_with_premium = $("#sum_insures1 option:selected").data("id");

        if ($("#sum_insures1 :selected").attr("data-type") == "family_construct") {
            $.post("/get_family_construct", {
                "policyNo": $("#sum_insures1 :selected").attr("data-policyno"),
                "sumInsured": $("#sum_insures1 :selected").val()
            }, function (e) {
                $("#vtlFamilyConstruct").empty();

                e = JSON.parse(e);
                $("#vtlFamilyConstructDiv").show();
                if (e) {
                    e.forEach(function (e1) {

                        $("#vtlFamilyConstruct").append("<option value='" + e1.family_type + "' data-premium='" + e1.PremiumServiceTax + "'>" + e1.family_type + "</option>");
                    });

                    $("#vtlFamilyConstruct").change();
                }
            });


        } else {
            $("#vtlFamilyConstructDiv").hide();
        }

        // $("#premium1").val(tax_with_premium);
    });

    //onchnage event customer serach type
    $(document).on("change", "#cusDet", function () {
        var customer_serach_value = $("#cusDet option:selected").val();
        var subtype = $("#policySubType").val();
        if (customer_serach_value && subtype) {
            $("#customer_serach_button").show();
        } else {
            $("#customer_serach_button").hide();
        }
    });

    $.post("/employee/get_smallest_date", function (e) {
        e = JSON.parse(e);
        if (e.length != "") {
            son_age = e.date.split("-");
            son_age = new Date(
                Number(son_age[2]),
                Number(son_age[1]) - 1,
                Number(son_age[0])
            );
            son_age.setDate(son_age.getDate() + 1);
        }
    });

    // $.ajax({
    // url: "/employee/get_smallest_date",
    // type: "POST",
    // async: false,
    // dataType: "json",
    // success: function (response) {
    // if(response.length>0) {

    // son_age = response.date.split("-");
    // son_age = new Date(Number(son_age[2]), Number(son_age[1]) - 1, Number(son_age[0]));
    // son_age.setDate(son_age.getDate() + 1);

    // }
    // }
    // });

    $(document).on("click", ".ecard_link_download", function () {
        var z = $(this).attr("data-id");
        var d = z.split(",");
        ecard_link(d[0], d[1], d[2], d[3], d[4]);
    });

    $("body").on("change", "input[name=upload_document]", function () {
        readURL(this);
    });

    $("body").on("change", "input[name=upload_modal]", function () {
        readURL(this);
    });

    $("#resets").on("click", function () {
        location.reload();
        /* $('#enrollment_data')[0].reset();
        $('#family_gender').empty(); */
    });
    // append all data of policy member in table view

    // update data


    // already existing child
    $(document).on("click", "#modal-submit", function () {
        //debugger;
        if ($("input[name='radio_option']:checked").val() == undefined) {
            swal("please select at least one member");
            return false;
        }
        
        $.ajax({
            url: "/get_individual_family_details",
            type: "POST",
            data: {
                family_id: $("input[name='radio_option']:checked").val(),
                emp_id: emp_id
            },
            async: false,
            dataType: "json",
            success: function (response) {
                if (response.length != 0) {
                    $("#nominee_fname").val(response.policy_member_first_name);
                    $("#nominee_lname").val(response.policy_member_last_name);
                    //$("input[type='text'][name='nominee_dob']").val(response.family_dob);
                    // selectChange.find("#first_name").val(response.family_firstname);
                    // selectChange.find("#last_name").val(response.family_lastname);
                    // selectChange
                    // .find("input[type='text'][name='family_date_birth']")
                    // .val(response.family_dob);
                    // selectChange.find("#family_id").val(response.family_id);
                    // get_age_family(response.family_dob, eValue);
                }
                $("#myModal").modal("hide");
            }
        });
    });
    $(document).on("click", "#modal-child-submit", function () {
        if ($("input[name='child_kid_modal']:checked").val() == undefined) {
            swal("", "please select at least one member");
            return false;
        } else {
            var row = $("#targetRow").val();

            if ($("input[name='child_kid_modal']:checked").val() == "disable_child")
                var rowObj = ($("#" + row)
                    .closest("tr")
                    .find("#disable_c_d")
                    .get(0).files = $("#upload_modal").get(0).files);
            else
                var rowObj = ($("#" + row)
                    .closest("tr")
                    .find("#disable_c_d")
                    .get(0).files = null);

            var id = $("input[name='child_kid_modal']:checked").val();
            $("#" + row)
                .closest("tr")
                .find("#disable_c")
                .val(id);
            $("#MYModal").modal("hide");
        }
    });
    // bdate of new table of enrollment

    // date of birth

    $("#family_date_birth1").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        maxDate: 0,
        yearRange: "-100: +0",
        onSelect: function (dateText, inst) {
            $(this).val(dateText);
            get_age_family(dateText, eValue);
        }
    });

    //    $(document).on('click', '.family_date_birth', function (e) {
    //
    //        selectChange = $(this).closest(".row");
    //$(this).removeClass("hasDatepicker");
    //        var $family_date_birth = $(this);
    //        $family_date_birth.val('');
    //        $.ajax({
    //            url: "/employee/get_min_max_age",
    //            type: "POST",
    //            data:{rel_id:$("#family_members_id").val(),policy_id:$("#policy_no option:selected").val()},
    //            dataType: "json",
    //            success: function (response) {
    //                    if ($("#family_members_id").val() == 2 || $("#family_members_id").val() == 3)
    //                    {
    //                        if(response.max_age == 0)
    //                        {
    //                            $family_date_birth.datepicker("option", "yearRange", "-100:+0");
    //                            $family_date_birth.datepicker("option", "minDate", "-100Y");
    //                            $family_date_birth.datepicker("option", "maxDate", "0d");
    //                        }
    //                        else
    //                        {
    //                            $family_date_birth.datepicker("option", "yearRange", "-"+response.max_age+":+0");
    //                            $family_date_birth.datepicker("option", "minDate", "-"+response.max_age+"Y");
    //                            $family_date_birth.datepicker("option", "maxDate", "0d");
    //                        }
    //                    }
    //                    else
    //                    {
    //                        if(response.min_age == 0 && response.max_age == 0)
    //                        {
    //                            $family_date_birth.datepicker("option", "yearRange", "-100:-18");
    //                            $family_date_birth.datepicker("option", "minDate", "-100Y");
    //                            $family_date_birth.datepicker("option", "maxDate", "-18Y");
    //                        }
    //                        else
    //                        {
    //                            $family_date_birth.datepicker("option", "yearRange", "-"+response.max_age+":-"+response.min_age);
    //                            $family_date_birth.datepicker("option", "minDate", "-"+response.max_age+"Y");
    //                            $family_date_birth.datepicker("option", "maxDate", "-"+response.min_age+"Y");
    //                        }
    //                    }
    //            }
    //        });

    //$(this).datepicker('show');
    //    });

    // new child add
    $(document).on("click", "#add_new", function () {
        selectChange.find("#first_name").val("");
        selectChange.find("#last_name").val("");
        selectChange.find("#family_id").val("");
        selectChange.find("input[type='text'][name='family_date_birth']").val("");
        $("#myModal").modal("hide");
    });

    // get date of spouse for set kids DOB
    $("body").on("keyup", ".first_name", function (e) {
        var $th = $(this);
        if (
            e.keyCode != 46 &&
            e.keyCode != 8 &&
            e.keyCode != 37 &&
            e.keyCode != 38 &&
            e.keyCode != 39 &&
            e.keyCode != 40
        ) {
            $th.val(
                $th.val().replace(/[^A-Za-z ]/g, function (str) {
                    return "";
                })
            );
        }
        return;
    });
    $("body").on("keyup", ".last_name", function (e) {
        var $th = $(this);
        if (
            e.keyCode != 46 &&
            e.keyCode != 8 &&
            e.keyCode != 37 &&
            e.keyCode != 38 &&
            e.keyCode != 39 &&
            e.keyCode != 40
        ) {
            $th.val(
                $th.val().replace(/[^A-Za-z ]/g, function (str) {
                    return "";
                })
            );
        }
        return;
    });

    $("body").on("keyup", "#fname", function (e) {
        var $th = $(this);
        if (
            e.keyCode != 46 &&
            e.keyCode != 8 &&
            e.keyCode != 37 &&
            e.keyCode != 38 &&
            e.keyCode != 39 &&
            e.keyCode != 40
        ) {
            $th.val(
                $th.val().replace(/[^A-Za-z]/g, function (str) {
                    return "";
                })
            );
        }
        return;
    });
    $("body").on("keyup", "#lname", function (e) {
        var $th = $(this);
        if (
            e.keyCode != 46 &&
            e.keyCode != 8 &&
            e.keyCode != 37 &&
            e.keyCode != 38 &&
            e.keyCode != 39 &&
            e.keyCode != 40
        ) {
            $th.val(
                $th.val().replace(/[^A-Za-z]/g, function (str) {
                    return "";
                })
            );
        }
        return;
    });
    $("body").on("keyup", "#policy_sum", function (e) {
        var $th = $(this);

        if (
            e.keyCode != 46 &&
            e.keyCode != 8 &&
            e.keyCode != 37 &&
            e.keyCode != 38 &&
            e.keyCode != 39 &&
            e.keyCode != 40
        ) {
            $th.val(
                $th.val().replace(/[^0-9]/g, function (str) {
                    return "";
                })
            );
        }
        return;
    });
    $("body").on("keyup", "#policy_premium", function (e) {
        var $th = $(this);

        if (
            e.keyCode != 46 &&
            e.keyCode != 8 &&
            e.keyCode != 37 &&
            e.keyCode != 38 &&
            e.keyCode != 39 &&
            e.keyCode != 40
        ) {
            $th.val(
                $th.val().replace(/[^0-9]/g, function (str) {
                    return "";
                })
            );
        }
        return;
    });

    // click on checkbox upload button
    $(document).on("change", 'input[type="radio"]', function () {
        if (this.name != "radio_option" && this.name != "child_kid_modal") {
            var selectedValue = $("input[name='" + this.name + "']:checked").val();
            if (selectedValue == "disable_child") {
                $("#disable_child").prop("checked", true);
                $("#unmarried_child").prop("checked", false);
                //$(this).closest("div .disable").append('<input type="file" name="upload_document" id="upload_document"><img src="" id="schild" name="" style="display:none;width:200px; height:100px">');
                $(".disable_file_wrapper").css("display", "block");
            } else {
                $("#unmarried_child").prop("checked", true);
                $("#disable_child").prop("checked", false);
                //$("input[name$='upload_document'], #schild").css("display", "none");
                $(".disable_file_wrapper").css("display", "none");
            }
        }
    });

    $("#data_final_parent").click(function () {
        var flex_amount = $("#wallet_parent").text();
        var pay_amount = $("#pay_parent").text();
        var employee_contri = $("#contri_parent").val();
        if (employee_contri != 0) {
            var premium =
                (parseInt($("#premium_parent").val()) * employee_contri) / 100;
        } else {
            var premium = $("#premium_parent").val();
        }

        submit_member("F,S", flex_amount, pay_amount, premium);
    });

    $(document).on("change", 'input[name="child_kid_modal"]', function () {
        if (this.name != "radio_option") {
            var selectedValue = $("input[name='child_kid_modal']:checked").val();
            if (selectedValue == "disable_child") {
                $("input[name$='upload_modal']").css("display", "block");
            } else {
                $("input[name$='upload_modal']").css("display", "none");
                $("#upload_modal").get(0).files = null;
            }
        }
    });
    $("#parent_sumInsure").on("click", function () {
        var policy_id = $("#policy_no").val();
        var deduction_type = $("input[name='parent_type']:checked").val();
        var current_val = $("#gmc_parent_estimate").attr("data-value");
        var both_premium = $("input[name='gmc']:checked").attr("data-ids");
        var sum_insured = $("#parent_si").text();
        var premium_text = $("#premium_type_text").val();

        $.ajax({
            url: "/employee/parent_sum_update",
            type: "POST",
            data: {
                premium_text: premium_text,
                policy_id: policy_id,
                both_premium: both_premium,
                sum_insured: sum_insured,
                current_val: current_val
            },
            dataType: "json",
            success: function (response) {
                // debugger;
                swal({
                    title: "Alert",
                    text: response.message,
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
                    function () {
                        // location.reload();
                    }
                );
            }
        });
    });
    // submit all data
    $("#submit").on("click", function () {
        var selected_val = $("input[name='dc_type']:checked").val();
        var employee_contri = $("#contri_parent").val();
        //var deduction_type = "";

        if ($("#payment_parent_wrapper").is(":visible")) {
            if (employee_contri != 0) {
                var premium =
                    (parseInt($("#premium_parent").val()) * employee_contri) / 100;
            } else {
                var premium = $("#premium_parent").val();
            }

            var wallet_bal = parseInt($("#wallet_ut").text());

            if (selected_val !== undefined) {
                if (selected_val == "F") {
                    if (premium > wallet_bal && wallet_bal != 0) {
                        $("#wallet_parent").html("<i class='fa fa-inr'></i> " + wallet_bal);
                        var cut_amt = parseInt(premium - wallet_bal);
                        $("#pay_parent").html("<i class='fa fa-inr'></i> " + cut_amt);
                        $("#getCodeModal_parent").modal("toggle");
                    } else if (wallet_bal == 0 && premium > wallet_bal) {
                        $("#getCodeModal").modal("toggle");
                        $("#getCode").html("Flex balance is not enough");
                        return false;
                    } else {
                        submit_member("F", premium, "", premium);
                    }
                } else {
                    submit_member("S", "", premium, premium);
                }
            } else {
                swal({
                    title: "Alert",
                    text: "Please Select Deduction Type",
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
                    function () {
                        //location.reload();
                    }
                );
                return;
            }
        } else {
            submit_member("", "", "", "");
        }

        /* $.post("/employee/get_family_details", {
             "policy_idArr":JSON.stringify(policy_idArr),
             "family_members_idArr":JSON.stringify(family_members_idArr),
             "first_nameArr":JSON.sjingify(first_nameArr),
             "last_nameArr":JSON.stringify(last_nameArr),
             "family_date_birthArr":JSON.stringify(family_date_birthArr),
             "family_idArr":JSON.stringify(family_idArr),
             "family_genderArr":JSON.stringify(family_genderArr),
             "marriage_dateArr":JSON.stringify(marriage_dateArr),
             "disable_childArr":JSON.stringify(disable_childArr),
             //"unmarried_childArr":JSON.stringify(unmarried_childArr),
             "age_typeArr":JSON.stringify(age_typeArr),
             "ageArr":JSON.stringify(ageArr)
             
             }, function(e) {
             }); */
    });

    $("#vtlForm").validate({
        ignore: ".ignore",
        rules: {
            sum_insure: {
                required: true
            },
            patFamilyConstruct: {
                required: true
            },
            premium: {
                required: true
            },
            family_members_id: {
                required: true
            },
            family_gender: {
                required: true
            },
            first_name: {
                required: true
            },
            last_name: {
                required: true
            },
            family_date_birth: {
                required: true
            },
            age: {
                required: true
            },
            age_type: {
                required: true
            }

        },
        messages: {

        },
        submitHandler: function (form) {

            if (!emp_id) {
                alert("Please add Customer Details First");
                return;
            }

            var premium_vtl = $('#vtlFamilyConstruct option:selected').data('premium');
            var form = $("#vtlForm").serialize() + "premium=" + premium_vtl + "&policyNo=" + $("#sum_insures1 :selected").attr("data-policyno") + "&empId=" + emp_id;

            $.post("/employee/get_family_details", form, function (e) {
                var data = JSON.parse(e);
                if (!data.status) {
                    swal({
                        title: "Alert",
                        text: data.message,
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
                        function () {
                            //location.reload();
                        });
                    return;
                }

                $("#vtlForm").find("input[name='edit']").val("0");
                $("#sum_insures1").css("pointer-events", "none");
                $("#vtlFamilyConstruct").css("pointer-events", "none");
                $("#premium1").css("pointer-events", "none");

                var sum_insures1 = $("#sum_insures1").val();
                var vtlFamilyConstruct = $("#vtlFamilyConstruct").val();
                var premium1 = $("#premium1").val();

                document.getElementById("vtlForm").reset();
				
                $("#sum_insures1").val(sum_insures1);
                $("#vtlFamilyConstruct").val(vtlFamilyConstruct);
                $("#premium1").val(premium1);

                addDependentForm("vtlTable", JSON.parse(e));
            });
        }
    });

    $("#patForm").validate({
        ignore: ".ignore",
        rules: {
            sum_insure: {
                required: true
            },
            patFamilyConstruct: {
                required: true
            },
            premium: {
                required: true
            },
            family_members_id: {
                required: true
            },
            family_gender: {
                required: true
            },
            first_name: {
                required: true
            },
            last_name: {
                required: true
            },
            family_date_birth: {
                required: true
            },
            age: {
                required: true
            },
            age_type: {
                required: true
            }

        },
        messages: {

        },
        submitHandler: function (form) {
			if(!$("#emp_form_old").valid()) {
		ajaxindicatorstop();
		$("#salutation").focus();
		return;
	}
            if (!Number($("#premium").val())) {
                swal("Alert", "invalid Premium", "warning");
                return;
            }

            if (!emp_id) {
                alert("Please add Customer Details First");
                return;
            }
            var TableData = [];

            var LabelData = []; //          TableData = [];

            $('#mydatasmember tr').each(function (row, tr) {
                var LabelData = {};
                //TableData[row] = {};
                var label_content;
                var tds = $(tr).find('td:eq(0)').text();
                var label = $(tr).find('.label_id').text();
                var content = $(tr).find('.mycontent').val();




                TableData[row] = {
                    "question": content,
                    "format": $(tr).find('input[name="' + content + '"]:checked').val(),
                }



            });

            var policyNo = $("#sum_insures :selected").attr("data-policyno");
            var product = $("#masPolicy option:selected").val();
            var premium_pat = $('#patFamilyConstruct option:selected').data('premium');
            var familyDataType = $("#sum_insures1 :selected").attr("data-type");
            var form = $("#patForm").serialize() + "&premium=" + premium_pat + "&policyNo=" + $("#sum_insures :selected").attr("data-policyno") + "&familyDataType=" + familyDataType + "&empId=" + emp_id + "&product=" + product + "&declare=" + JSON.stringify(TableData);

            $.post("/employee/get_family_details", form, function (e) {

                var data = JSON.parse(e);

                if (!data.status) {

                    if (data.check == "declaration") {
                        $('#confr').hide();
                    }


                    swal({
                        title: "Alert",
                        text: data.message,
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
                        function () {
                            //location.reload();
                        });
                    return;
                }

			
				swal("Alert", data.message, "success");
				
    $('#chronic th input[type="checkbox"]').prop('checked',false);


			 $(".quest_declare_benifit_4s").empty();
				$("#patFormSubmit").closest("form").find("select[name='family_members_id']").css("pointer-events",'auto');
                $('#confr').show();
                $("#patForm").find("input[name='edit']").val("0");
                $("#sum_insures").css("pointer-events", "none");
                $("#patFamilyConstruct").css("pointer-events", "none");
                $("#premium").css("pointer-events", "none");

                var sum_insures = $("#sum_insures").val();
                var patFamilyConstruct = $("#patFamilyConstruct").val();
                var premium = $("#premium").val();

                document.getElementById("patForm").reset();

                $("#sum_insures").val(sum_insures);
                $("#patFamilyConstruct").val(patFamilyConstruct);
                $("#premium").val(premium);

				
                addDependentForm("patTable", JSON.parse(e));
            });
        }
    });


	
    $("input[type='radio']").on("change", function () {
        var deduction_type = $("input[name='parent_type']:checked").val();
        var current_val = $("#gmc_parent_estimate").attr("data-value");
        var both_val = $("#gmc_parent_estimate1").attr("data-value");

        var deduction_type = $("input[name='ghi_type']:checked").val();
        var current_val = $("#ghi_estimate").attr("data-value");
    });

    //submit parent data

    $("#submitparent").click(function () {
        var deduction_type = $("input[name='parent_type']:checked").val();
        var current_val = $("#ghi_estimate").attr("data-value");
        var sum_insured = $("#ghi_si").text();
        var balance = parseInt($(".balance").attr("data-value"));
        if (current_val == "") {
            swal("", "Please Select Sum Insured");
            return false;
        } else if (deduction_type == undefined) {
            swal("", "Please Select Deduction Type");
            return false;
        } else {
            if (deduction_type == "F") {
                var flex_utilised =
                    parseInt($(".flex_utilised").attr("data-value")) +
                    parseInt($("#ghi_total_premium").text()) -
                    parseInt(
                        $("#ghi_estimate").attr("data-value") == "" ?
                            0 :
                            $("#ghi_estimate").attr("data-value")
                    );
                if (balance < $("#ghi_total_premium").text() && balance != 0) {
                    $("#wallet_gmc").html("<i class='fa fa-inr'></i> " + balance);
                    var cut_amt = parseInt($("#ghi_total_premium").text()) - balance;
                    $("#pay_gmc").html("<i class='fa fa-inr'></i> " + cut_amt);
                    $("#getCodeModal1").modal("toggle");
                } else if (balance == 0) {
                    $("#getCodeModal").modal("toggle");
                    $("#getCode").html("Flex balance is not enough");
                    return false;
                } else {
                    var benifit_type = 3;
                    var name = "Mediclaim Top-Up";
                    var transac_type = "C";
                    $.ajax({
                        url: "/flexi_benifit/save_all_data",
                        type: "POST",
                        data: {
                            benifit_type: benifit_type,
                            transac_type: transac_type,
                            amount: current_val,
                            name: name,
                            deduction_type: deduction_type,
                            sum_insured: sum_insured,
                            flex_amount: current_val
                        },
                        dataType: "json",
                        success: function (response) {
                            $(".flex_utilised").html(
                                "<i class='fa fa-inr'></i> " + response.flex_amount
                            );
                            $(".flex_utilised").attr("data-value", response.flex_amount);
                            $(".salary_deduction").html(
                                "<i class='fa fa-inr'></i> " + response.pay_amount
                            );
                            $(".salary_deduction").attr("data-value", response.pay_amount);
                            var allotedAmt = $("#allotedAmt").text();

                            allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                            var utilized = $(".flex_utilised").attr("data-value");
                            utilized = parseInt(utilized.replace(/,/g, ""));
                            var data_balance = allotedAmt - utilized;
                            $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                            $(".balance").attr("data-value", data_balance);
                            $(".flex_utilised").html(
                                "<i class='fa fa-inr'></i> " + flex_utilised
                            );
                            $(".flex_utilised").attr("data-value", flex_utilised);
                            $("#ghi_estimate").html(
                                "<i class='fa fa-inr'></i> " +
                                parseInt($("#ghi_total_premium").text())
                            );
                            $("#ghi_estimate").attr(
                                "data-value",
                                parseInt($("#ghi_total_premium").text())
                            );
                            $("#ghi_estimate").attr("data-type", "F");
                            swal("", "Successfully submitted");
                            setTimeout("location.reload(true);", 1000);
                        }
                    });
                }
            } else if (deduction_type == "S") {
                var salary_deduction =
                    parseInt($(".salary_deduction").attr("data-value")) +
                    parseInt($("#ghi_total_premium").text()) -
                    parseInt(
                        $("#ghi_estimate").attr("data-value") == "" ?
                            0 :
                            $("#ghi_estimate").attr("data-value")
                    );
                if (salary_deduction > $("#total_salary").val()) {
                    $("#getCodeModal").modal("toggle");
                    $("#getCode").html("Please contact HR");
                    return false;
                }
                $(".salary_deduction").html(
                    "<i class='fa fa-inr'></i> " + salary_deduction
                );
                $(".salary_deduction").attr("data-value", salary_deduction);
                $("#ghi_estimate").html(
                    "<i class='fa fa-inr'></i> " +
                    parseInt($("#ghi_total_premium").text())
                );
                $("#ghi_estimate").attr(
                    "data-value",
                    parseInt($("#ghi_total_premium").text())
                );
                $("#ghi_estimate").attr("data-type", "S");
                var benifit_type = 3;
                var name = "Mediclaim Top-Up";
                var transac_type = "C";
                $.ajax({
                    url: "/flexi_benifit/save_all_data",
                    type: "POST",
                    data: {
                        benifit_type: benifit_type,
                        transac_type: transac_type,
                        amount: current_val,
                        name: name,
                        deduction_type: deduction_type,
                        sum_insured: sum_insured,
                        pay_amount: current_val
                    },
                    dataType: "json",
                    success: function (response) {
                        $(".flex_utilised").html(
                            "<i class='fa fa-inr'></i> " + response.flex_amount
                        );
                        $(".flex_utilised").attr("data-value", response.flex_amount);
                        $(".salary_deduction").html(
                            "<i class='fa fa-inr'></i> " + response.pay_amount
                        );
                        $(".salary_deduction").attr("data-value", response.pay_amount);
                        var allotedAmt = $("#allotedAmt").text();

                        allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                        var utilized = $(".flex_utilised").attr("data-value");
                        utilized = parseInt(utilized.replace(/,/g, ""));
                        var data_balance = allotedAmt - utilized;
                        $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                        $(".balance").attr("data-value", data_balance);
                        swal("", "Successfully submitted");
                        setTimeout("location.reload(true);", 1000);
                        // $("#ghi_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#ghi_total_premium").text()));
                        // $("#ghi_estimate").attr("data-value",parseInt($("#ghi_total_premium").text()));
                        // $("#ghi_estimate").attr("data-type",'S');
                    }
                });
            }
        }
    });

    // submitghi
    $("#submitGHI").click(function () {
        var deduction_type = $("input[name='ghi_type']:checked").val();
        var current_val = $("#ghi_estimate").attr("data-value");
        var sum_insured = $("#ghi_si").text();
        var balance = parseInt($(".balance").attr("data-value"));
        if (current_val == "") {
            swal("", "Please Select Sum Insured");
            return false;
        } else if (deduction_type == undefined) {
            swal("", "Please Select Deduction Type");
            return false;
        } else {
            if (deduction_type == "F") {
                var flex_utilised =
                    parseInt($(".flex_utilised").attr("data-value")) +
                    parseInt($("#ghi_total_premium").text()) -
                    parseInt(
                        $("#ghi_estimate").attr("data-value") == "" ?
                            0 :
                            $("#ghi_estimate").attr("data-value")
                    );
                if (balance < $("#ghi_total_premium").text() && balance != 0) {
                    $("#wallet_gmc").html("<i class='fa fa-inr'></i> " + balance);
                    var cut_amt = parseInt($("#ghi_total_premium").text()) - balance;
                    $("#pay_gmc").html("<i class='fa fa-inr'></i> " + cut_amt);
                    $("#getCodeModal1").modal("toggle");
                } else if (balance == 0) {
                    $("#getCodeModal").modal("toggle");
                    $("#getCode").html("Flex balance is not enough");
                    return false;
                } else {
                    var benifit_type = 3;
                    var name = "Mediclaim Top-Up";
                    var transac_type = "C";
                    $.ajax({
                        url: "/flexi_benifit/save_all_data",
                        type: "POST",
                        data: {
                            benifit_type: benifit_type,
                            transac_type: transac_type,
                            amount: current_val,
                            name: name,
                            deduction_type: deduction_type,
                            sum_insured: sum_insured,
                            flex_amount: current_val
                        },
                        dataType: "json",
                        success: function (response) {
                            $(".flex_utilised").html(
                                "<i class='fa fa-inr'></i> " + response.flex_amount
                            );
                            $(".flex_utilised").attr("data-value", response.flex_amount);
                            $(".salary_deduction").html(
                                "<i class='fa fa-inr'></i> " + response.pay_amount
                            );
                            $(".salary_deduction").attr("data-value", response.pay_amount);
                            var allotedAmt = $("#allotedAmt").text();

                            allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                            var utilized = $(".flex_utilised").attr("data-value");
                            utilized = parseInt(utilized.replace(/,/g, ""));
                            var data_balance = allotedAmt - utilized;
                            $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                            $(".balance").attr("data-value", data_balance);
                            $(".flex_utilised").html(
                                "<i class='fa fa-inr'></i> " + flex_utilised
                            );
                            $(".flex_utilised").attr("data-value", flex_utilised);
                            $("#ghi_estimate").html(
                                "<i class='fa fa-inr'></i> " +
                                parseInt($("#ghi_total_premium").text())
                            );
                            $("#ghi_estimate").attr(
                                "data-value",
                                parseInt($("#ghi_total_premium").text())
                            );
                            $("#ghi_estimate").attr("data-type", "F");
                            swal("", "Successfully submitted");
                            setTimeout("location.reload(true);", 1000);
                        }
                    });
                }
            } else if (deduction_type == "S") {
                var salary_deduction =
                    parseInt($(".salary_deduction").attr("data-value")) +
                    parseInt($("#ghi_total_premium").text()) -
                    parseInt(
                        $("#ghi_estimate").attr("data-value") == "" ?
                            0 :
                            $("#ghi_estimate").attr("data-value")
                    );
                if (salary_deduction > $("#total_salary").val()) {
                    $("#getCodeModal").modal("toggle");
                    $("#getCode").html("Please contact HR");
                    return false;
                }
                $(".salary_deduction").html(
                    "<i class='fa fa-inr'></i> " + salary_deduction
                );
                $(".salary_deduction").attr("data-value", salary_deduction);
                $("#ghi_estimate").html(
                    "<i class='fa fa-inr'></i> " +
                    parseInt($("#ghi_total_premium").text())
                );
                $("#ghi_estimate").attr(
                    "data-value",
                    parseInt($("#ghi_total_premium").text())
                );
                $("#ghi_estimate").attr("data-type", "S");
                var benifit_type = 3;
                var name = "Mediclaim Top-Up";
                var transac_type = "C";
                $.ajax({
                    url: "/flexi_benifit/save_all_data",
                    type: "POST",
                    data: {
                        benifit_type: benifit_type,
                        transac_type: transac_type,
                        amount: current_val,
                        name: name,
                        deduction_type: deduction_type,
                        sum_insured: sum_insured,
                        pay_amount: current_val
                    },
                    dataType: "json",
                    success: function (response) {
                        $(".flex_utilised").html(
                            "<i class='fa fa-inr'></i> " + response.flex_amount
                        );
                        $(".flex_utilised").attr("data-value", response.flex_amount);
                        $(".salary_deduction").html(
                            "<i class='fa fa-inr'></i> " + response.pay_amount
                        );
                        $(".salary_deduction").attr("data-value", response.pay_amount);
                        var allotedAmt = $("#allotedAmt").text();

                        allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                        var utilized = $(".flex_utilised").attr("data-value");
                        utilized = parseInt(utilized.replace(/,/g, ""));
                        var data_balance = allotedAmt - utilized;
                        $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                        $(".balance").attr("data-value", data_balance);
                        swal("", "Successfully submitted");
                        setTimeout("location.reload(true);", 1000);
                        // $("#ghi_estimate").html("<i class='fa fa-inr'></i> "+parseInt($("#ghi_total_premium").text()));
                        // $("#ghi_estimate").attr("data-value",parseInt($("#ghi_total_premium").text()));
                        // $("#ghi_estimate").attr("data-type",'S');
                    }
                });
            }
        }
    });

    // ghi wallet modal
    $(".data_final").click(function () {
        var btn_val = $(this).val();
        if (btn_val == "yes") {
            var data_amt = parseInt(
                $("#ghi_estimate")
                    .attr("data-value")
                    .replace(/,/g, "")
            );
            var pay_minus_amt =
                data_amt -
                parseInt(
                    $(".balance")
                        .attr("data-value")
                        .replace(/,/g, "")
                );
            var wallet_bal_gmc = parseInt($(".balance").attr("data-value"));
            var benifit_type = 3;
            var name = "Mediclaim Top-Up";
            var sum_insured = $("#ghi_si").text();
            var deduction_type = "F,S";
            var transac_type = "C";
            $.ajax({
                url: "/flexi_benifit/save_all_data",
                type: "POST",
                data: {
                    benifit_type: benifit_type,
                    transac_type: transac_type,
                    amount: $("#ghi_estimate").attr("data-value"),
                    name: name,
                    deduction_type: deduction_type,
                    sum_insured: sum_insured,
                    flex_amount: wallet_bal_gmc,
                    pay_amount: pay_minus_amt
                },
                dataType: "json",
                success: function (response) {
                    $(".flex_utilised").html(
                        "<i class='fa fa-inr'></i> " + response.flex_amount
                    );
                    $(".flex_utilised").attr("data-value", response.flex_amount);
                    $(".salary_deduction").html(
                        "<i class='fa fa-inr'></i> " + response.pay_amount
                    );
                    $(".salary_deduction").attr("data-value", response.pay_amount);
                    var allotedAmt = $("#allotedAmt").text();

                    allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                    var utilized = $(".flex_utilised").attr("data-value");
                    utilized = parseInt(utilized.replace(/,/g, ""));
                    var data_balance = allotedAmt - utilized;
                    $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                    $(".balance").attr("data-value", data_balance);
                    swal("", "Successfully submitted");
                    setTimeout("location.reload(true);", 1000);
                }
            });
        } else {
            window.location.reload();
        }
    });

    $("input[type='radio']").on("change", function () {
        var deduction_type = $("input[name='gpa_type']:checked").val();
        var current_val = $("#gpa_estimate").attr("data-value");
    });

    // submitgpa
    $("#submitGPA").click(function () {
        var deduction_type = $("input[name='gpa_type']:checked").val();
        var current_val = $("#gpa_estimate").attr("data-value");
        var sum_insured = $("#gpa_si").text();
        var balance = parseInt($(".balance").attr("data-value"));
        if (current_val == "") {
            swal("", "Please Select Sum Insured");
            return false;
        } else if (deduction_type == undefined) {
            swal("", "Please Select Deduction Type");
            return false;
        } else {
            if (deduction_type == "F") {
                var flex_utilised =
                    parseInt($(".flex_utilised").attr("data-value")) +
                    parseInt($("#gpa_total_premium").text()) -
                    parseInt(
                        $("#gpa_estimate").attr("data-value") == "" ?
                            0 :
                            $("#gpa_estimate").attr("data-value")
                    );
                if (balance < $("#gpa_total_premium").text() && balance != 0) {
                    $("#wallet_gpa").html("<i class='fa fa-inr'></i> " + balance);
                    var cut_amt = parseInt($("#gpa_total_premium").text()) - balance;
                    $("#pay_gpa").html("<i class='fa fa-inr'></i> " + cut_amt);
                    $("#getCodeModal2").modal("toggle");
                } else if (balance == 0) {
                    $("#getCodeModal").modal("toggle");
                    $("#getCode").html("Flex balance is not enough");
                    return false;
                } else {
                    var benifit_type = 4;
                    var name = "Personal Accident Top-Up";
                    var transac_type = "C";
                    $.ajax({
                        url: "/flexi_benifit/save_all_data",
                        type: "POST",
                        data: {
                            benifit_type: benifit_type,
                            transac_type: transac_type,
                            amount: current_val,
                            name: name,
                            deduction_type: deduction_type,
                            sum_insured: sum_insured,
                            flex_amount: current_val
                        },
                        dataType: "json",
                        success: function (response) {
                            $(".flex_utilised").html(
                                "<i class='fa fa-inr'></i> " + response.flex_amount
                            );
                            $(".flex_utilised").attr("data-value", response.flex_amount);
                            $(".salary_deduction").html(
                                "<i class='fa fa-inr'></i> " + response.pay_amount
                            );
                            $(".salary_deduction").attr("data-value", response.pay_amount);
                            var allotedAmt = $("#allotedAmt").text();

                            allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                            var utilized = $(".flex_utilised").attr("data-value");
                            utilized = parseInt(utilized.replace(/,/g, ""));
                            var data_balance = allotedAmt - utilized;
                            $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                            $(".balance").attr("data-value", data_balance);
                            $(".flex_utilised").html(
                                "<i class='fa fa-inr'></i> " + flex_utilised
                            );
                            $(".flex_utilised").attr("data-value", flex_utilised);
                            $("#gpa_estimate").html(
                                "<i class='fa fa-inr'></i> " +
                                parseInt($("#gpa_total_premium").text())
                            );
                            $("#gpa_estimate").attr(
                                "data-value",
                                parseInt($("#gpa_total_premium").text())
                            );
                            $("#gpa_estimate").attr("data-type", "F");
                            swal("", "Successfully submitted");
                            setTimeout("location.reload(true);", 1000);
                        }
                    });
                }
            } else if (deduction_type == "S") {
                var salary_deduction =
                    parseInt($(".salary_deduction").attr("data-value")) +
                    parseInt($("#gpa_total_premium").text()) -
                    parseInt(
                        $("#gpa_estimate").attr("data-value") == "" ?
                            0 :
                            $("#gpa_estimate").attr("data-value")
                    );
                //alert($("#total_salary").val());return;
                if (salary_deduction > $("#total_salary").val()) {
                    $("#getCodeModal").modal("toggle");
                    $("#getCode").html("Please contact HR");
                    return false;
                }
                $(".salary_deduction").html(
                    "<i class='fa fa-inr'></i> " + salary_deduction
                );
                $(".salary_deduction").attr("data-value", salary_deduction);
                $("#gpa_estimate").html(
                    "<i class='fa fa-inr'></i> " +
                    parseInt($("#gpa_total_premium").text())
                );
                $("#gpa_estimate").attr(
                    "data-value",
                    parseInt($("#gpa_total_premium").text())
                );
                $("#gpa_estimate").attr("data-type", "S");
                var benifit_type = 4;
                var name = "Personal Accident Top-Up";
                var transac_type = "C";
                $.ajax({
                    url: "/flexi_benifit/save_all_data",
                    type: "POST",
                    data: {
                        benifit_type: benifit_type,
                        transac_type: transac_type,
                        amount: current_val,
                        name: name,
                        deduction_type: deduction_type,
                        sum_insured: sum_insured,
                        pay_amount: current_val
                    },
                    dataType: "json",
                    success: function (response) {
                        $(".flex_utilised").html(
                            "<i class='fa fa-inr'></i> " + response.flex_amount
                        );
                        $(".flex_utilised").attr("data-value", response.flex_amount);
                        $(".salary_deduction").html(
                            "<i class='fa fa-inr'></i> " + response.pay_amount
                        );
                        $(".salary_deduction").attr("data-value", response.pay_amount);
                        var allotedAmt = $("#allotedAmt").text();

                        allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                        var utilized = $(".flex_utilised").attr("data-value");
                        utilized = parseInt(utilized.replace(/,/g, ""));
                        var data_balance = allotedAmt - utilized;
                        $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                        $(".balance").attr("data-value", data_balance);
                        swal("", "Successfully submitted");
                        setTimeout("location.reload(true);", 1000);
                    }
                });
            }
        }
    });

    // gpa wallet modal
    $(".data_final_gpa").click(function () {
        var btn_val = $(this).val();
        if (btn_val == "yes") {
            var data_amt_gpa = parseInt(
                $("#gpa_estimate")
                    .attr("data-value")
                    .replace(/,/g, "")
            );
            var minus_amt_gpa =
                data_amt_gpa -
                parseInt(
                    $(".balance")
                        .attr("data-value")
                        .replace(/,/g, "")
                );
            var wallet_bal_gpa = parseInt($(".balance").attr("data-value"));
            var benifit_type = 4;
            var name = "Personal Accident Top-Up";
            var sum_insured = $("#gpa_si").text();
            var deduction_type = "F,S";
            var transac_type = "C";
            $.ajax({
                url: "/flexi_benifit/save_all_data",
                type: "POST",
                data: {
                    benifit_type: benifit_type,
                    transac_type: transac_type,
                    amount: $("#gpa_estimate").attr("data-value"),
                    name: name,
                    deduction_type: deduction_type,
                    sum_insured: sum_insured,
                    flex_amount: wallet_bal_gpa,
                    pay_amount: minus_amt_gpa
                },
                dataType: "json",
                success: function (response) {
                    $(".flex_utilised").html(
                        "<i class='fa fa-inr'></i> " + response.flex_amount
                    );
                    $(".flex_utilised").attr("data-value", response.flex_amount);
                    $(".salary_deduction").html(
                        "<i class='fa fa-inr'></i> " + response.pay_amount
                    );
                    $(".salary_deduction").attr("data-value", response.pay_amount);
                    var allotedAmt = $("#allotedAmt").text();

                    allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                    var utilized = $(".flex_utilised").attr("data-value");
                    utilized = parseInt(utilized.replace(/,/g, ""));
                    var data_balance = allotedAmt - utilized;
                    $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                    $(".balance").attr("data-value", data_balance);
                    swal("", "Successfully submitted");
                    setTimeout("location.reload(true);", 1000);
                }
            });
        } else {
            window.location.reload();
        }
    });

    $("input[type='radio']").on("change", function () {
        var deduction_type = $("input[name='glta_type']:checked").val();
        var current_val = $("#gtli_estimate").attr("data-value");
    });

    // submitgtli
    $("#submitGtli").click(function () {
        var deduction_type = $("input[name='glta_type']:checked").val();
        var current_val = $("#gtli_estimate").attr("data-value");
        var sum_insured = $("#gtli_si").text();
        var balance = parseInt($(".balance").attr("data-value"));
        if (current_val == "") {
            swal("", "Please Select Sum Insured");
            return false;
        } else if (deduction_type == undefined) {
            swal("", "Please Select Deduction Type");
            return false;
        } else {
            if (deduction_type == "F") {
                var flex_utilised =
                    parseInt($(".flex_utilised").attr("data-value")) +
                    parseInt($("#gtli_total_premium").text()) -
                    parseInt(
                        $("#glta_estimate").attr("data-value") == "" ?
                            0 :
                            $("#glta_estimate").attr("data-value")
                    );

                if (balance < $("#gtli_total_premium").text() && balance != 0) {
                    $("#wallet_gtli").html("<i class='fa fa-inr'></i> " + balance);
                    var cut_amt = parseInt($("#gtli_total_premium").text()) - balance;
                    $("#pay_gtli").html("<i class='fa fa-inr'></i> " + cut_amt);
                    $("#getCodeModal4").modal("toggle");
                } else if (balance == 0) {
                    $("#getCodeModal").modal("toggle");
                    $("#getCode").html("Flex balance is not enough");
                    return false;
                } else {
                    var benifit_type = 5;
                    var name = "Voluntary Term Life";
                    var transac_type = "C";
                    $.ajax({
                        url: "/flexi_benifit/save_all_data",
                        type: "POST",
                        data: {
                            benifit_type: benifit_type,
                            transac_type: transac_type,
                            amount: current_val,
                            name: name,
                            deduction_type: deduction_type,
                            sum_insured: sum_insured,
                            flex_amount: current_val
                        },
                        dataType: "json",
                        success: function (response) {
                            $(".flex_utilised").html(
                                "<i class='fa fa-inr'></i> " + response.flex_amount
                            );
                            $(".flex_utilised").attr("data-value", response.flex_amount);
                            $(".salary_deduction").html(
                                "<i class='fa fa-inr'></i> " + response.pay_amount
                            );
                            $(".salary_deduction").attr("data-value", response.pay_amount);
                            var allotedAmt = $("#allotedAmt").text();

                            allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                            var utilized = $(".flex_utilised").attr("data-value");
                            utilized = parseInt(utilized.replace(/,/g, ""));
                            var data_balance = allotedAmt - utilized;
                            $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                            $(".balance").attr("data-value", data_balance);
                            $("#gtli_estimate").html(
                                "<i class='fa fa-inr'></i> " +
                                parseInt($("#gtli_total_premium").text())
                            );
                            $("#gtli_estimate").attr(
                                "data-value",
                                parseInt($("#gtli_total_premium").text())
                            );
                            $("#gtli_estimate").attr("data-type", "F");
                            swal("", "Successfully submitted");
                            setTimeout("location.reload(true);", 1000);
                        }
                    });
                }
            } else if (deduction_type == "S") {
                var salary_deduction =
                    parseInt($(".salary_deduction").attr("data-value")) +
                    parseInt($("#glta_total_premium").text()) -
                    parseInt(
                        $("#glta_estimate").attr("data-value") == "" ?
                            0 :
                            $("#glta_estimate").attr("data-value")
                    );
                if (salary_deduction > $("#total_salary").val()) {
                    $("#getCodeModal").modal("toggle");
                    $("#getCode").html("Please contact HR");
                    return false;
                }
                var benifit_type = 5;
                var name = "Voluntary Term Life";
                var transac_type = "C";
                $.ajax({
                    url: "/flexi_benifit/save_all_data",
                    type: "POST",
                    data: {
                        benifit_type: benifit_type,
                        transac_type: transac_type,
                        amount: current_val,
                        name: name,
                        deduction_type: deduction_type,
                        sum_insured: sum_insured,
                        pay_amount: current_val
                    },
                    dataType: "json",
                    success: function (response) {
                        $(".flex_utilised").html(
                            "<i class='fa fa-inr'></i> " + response.flex_amount
                        );
                        $(".flex_utilised").attr("data-value", response.flex_amount);
                        $(".salary_deduction").html(
                            "<i class='fa fa-inr'></i> " + response.pay_amount
                        );
                        $(".salary_deduction").attr("data-value", response.pay_amount);
                        var allotedAmt = $("#allotedAmt").text();

                        allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                        var utilized = $(".flex_utilised").attr("data-value");
                        utilized = parseInt(utilized.replace(/,/g, ""));
                        var data_balance = allotedAmt - utilized;
                        $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                        $(".balance").attr("data-value", data_balance);
                        $("#gtli_estimate").html(
                            "<i class='fa fa-inr'></i> " +
                            parseInt($("#gtli_total_premium").text())
                        );
                        $("#gtli_estimate").attr(
                            "data-value",
                            parseInt($("#gtli_total_premium").text())
                        );
                        $("#gtli_estimate").attr("data-type", "S");
                        swal("", "Successfully submitted");
                        setTimeout("location.reload(true);", 1000);
                    }
                });
            }
        }
    });

    // gtli wallet modal
    $("#data_final_gtli").click(function () {
        var btn_val = $(this).val();
        if (btn_val == "yes") {
            var data_amt = parseInt(
                $("#gtli_estimate")
                    .attr("data-value")
                    .replace(/,/g, "")
            );
            var minus_amt =
                data_amt -
                parseInt(
                    $(".balance")
                        .attr("data-value")
                        .replace(/,/g, "")
                );
            var wallet_bal_gtli = parseInt($(".balance").attr("data-value"));
            var benifit_type = 5;
            var name = "Voluntary Term Life";
            var sum_insured = $("#gtli_si").text();
            var deduction_type = "F,S";
            var transac_type = "C";
            $.ajax({
                url: "/flexi_benifit/save_all_data",
                type: "POST",
                data: {
                    benifit_type: benifit_type,
                    transac_type: transac_type,
                    amount: $("#gtli_total_premium").text(),
                    name: name,
                    deduction_type: deduction_type,
                    sum_insured: sum_insured,
                    flex_amount: wallet_bal_gtli,
                    pay_amount: minus_amt
                },
                dataType: "json",
                success: function (response) {
                    $(".flex_utilised").html(
                        "<i class='fa fa-inr'></i> " + response.flex_amount
                    );
                    $(".flex_utilised").attr("data-value", response.flex_amount);
                    $(".salary_deduction").html(
                        "<i class='fa fa-inr'></i> " + response.pay_amount
                    );
                    $(".salary_deduction").attr("data-value", response.pay_amount);
                    var allotedAmt = $("#allotedAmt").text();

                    allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                    var utilized = $(".flex_utilised").attr("data-value");
                    utilized = parseInt(utilized.replace(/,/g, ""));
                    var data_balance = allotedAmt - utilized;
                    $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                    $(".balance").attr("data-value", data_balance);
                    swal("", "Successfully submitted");
                    setTimeout("location.reload(true);", 1000);
                }
            });
        } else {
            window.location.reload();
        }
    });

    $('input[name="customCheck51"]').click(function () {
        if ($("#customCheck51").prop("checked") == true) {
            $("#submit_flex_data").css("pointer-events", "auto");
        } else {
            $("#submit_flex_data").css("pointer-events", "none");
        }
    });

    $("#submit_all_data").click(function () {
        swal({
            title: "Do you want to proceed?",
            text: "if Yes the data submitted by you will be considered final",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
        },
            function (isConfirm) {
                if (isConfirm == true) {
                    $.ajax({
                        url: "/confirmed_policy_member",
                        type: "POST",
                        async: false,
                        dataType: "json",
                        data: {
                            policy_no: $("#policy_numberss option:selected").data("id"),
                            policy_id: $("#policy_numberss option:selected").val()
                        },
                        success: function (response) {
                            if (response.status == "true") {
                                //send enrollment mail
                                $.ajax({
                                    url: "/set_enrollment_mails",
                                    type: "POST",
                                    async: false,
                                    dataType: "json",
                                    data: {
                                        policy_no: $("#policy_numberss option:selected").data("id"),
                                        policy_id: $("#policy_numberss option:selected").val()
                                    },
                                    success: function (response) { }
                                });
                                $.ajax({
                                    url: "/flexi_benifit/enrollment_submit_flex_data",
                                    type: "POST",
                                    async: false,
                                    dataType: "json",
                                    success: function (response) {
                                        if (response.status == "true") {
                                            swal("", "successfully submitted");
                                            $(".bd-example-modal-lg").modal("toggle");
                                            $("#payment_redirection").removeAttr(
                                                "style",
                                                "display:none;"
                                            );
                                            $("#submit_flex_data").hide();
                                            //window.location.reload();
                                        } else {
                                            // window.location.reload();
                                        }
                                    }
                                });
                            } else {
                                //window.location.reload();
                            }
                        }
                    });
                }
            }
        );
    });

    $("#resetghi").click(function () {
        $("#ghi_estimate").attr("data-value", 0);
        $("#ghi_estimate").html("<i class='fa fa-inr'></i> " + 0);
        var data_benefit = $(this).attr("data-value");
        $.ajax({
            type: "POST",
            url: "/flexi_benifit/reset_flexi_data",
            data: {
                benefit: data_benefit
            },
            success: function (response) {
                var data_res = JSON.parse(response);
                if (data_res.flex_amount != null && data_res.pay_amount != null) {
                    $(".flex_utilised").html(
                        "<i class='fa fa-inr'></i> " + data_res.flex_amount
                    );
                    $(".flex_utilised").attr("data-value", data_res.flex_amount);
                    $(".salary_deduction").html(
                        "<i class='fa fa-inr'></i> " + data_res.pay_amount
                    );
                    $(".salary_deduction").attr("data-value", data_res.pay_amount);
                    var allotedAmt = $("#allotedAmt").text();

                    allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                    var utilized = $(".flex_utilised").attr("data-value");
                    utilized = parseInt(utilized.replace(/,/g, ""));
                    var data_balance = allotedAmt - utilized;
                    $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                    $(".balance").attr("data-value", data_balance);
                    swal("", "successfully submitted");
                    setTimeout("location.reload(true);", 1000);
                } else {
                    swal("", "successfully submitted");
                    setTimeout("location.reload(true);", 1000);
                }
            }
        });
    });

    // reset parent data

    $("#resetparent").click(function () {
        $("#ghi_estimate").attr("data-value", 0);
        $("#ghi_estimate").html("<i class='fa fa-inr'></i> " + 0);
        var data_benefit = $(this).attr("data-value");
        $.ajax({
            type: "POST",
            url: "/flexi_benifit/reset_flexi_data",
            data: {
                benefit: data_benefit
            },
            success: function (response) {
                var data_res = JSON.parse(response);
                if (data_res.flex_amount != null && data_res.pay_amount != null) {
                    $(".flex_utilised").html(
                        "<i class='fa fa-inr'></i> " + data_res.flex_amount
                    );
                    $(".flex_utilised").attr("data-value", data_res.flex_amount);
                    $(".salary_deduction").html(
                        "<i class='fa fa-inr'></i> " + data_res.pay_amount
                    );
                    $(".salary_deduction").attr("data-value", data_res.pay_amount);
                    var allotedAmt = $("#allotedAmt").text();

                    allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                    var utilized = $(".flex_utilised").attr("data-value");
                    utilized = parseInt(utilized.replace(/,/g, ""));
                    var data_balance = allotedAmt - utilized;
                    $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                    $(".balance").attr("data-value", data_balance);
                    swal("", "successfully submitted");
                    setTimeout("location.reload(true);", 1000);
                } else {
                    swal("", "successfully submitted");
                    setTimeout("location.reload(true);", 1000);
                }
            }
        });
    });

    $("#resetgpa").click(function () {
        $("#gpa_estimate").attr("data-value", 0);
        $("#gpa_estimate").html("<i class='fa fa-inr'></i> " + 0);
        //       var flex_utilised =  parseInt(($(".flex_utilised").attr('data-value')) - parseInt($("#gpa_total_premium").text()));
        // $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
        //      $(".flex_utilised").attr("data-value",flex_utilised);
        var data_benefit = $(this).attr("data-value");
        $.ajax({
            type: "POST",
            url: "/flexi_benifit/reset_flexi_data",
            data: {
                benefit: data_benefit
            },
            success: function (response) {
                var data_res = JSON.parse(response);
                if (data_res.flex_amount != null && data_res.pay_amount != null) {
                    $(".flex_utilised").html(
                        "<i class='fa fa-inr'></i> " + data_res.flex_amount
                    );
                    $(".flex_utilised").attr("data-value", data_res.flex_amount);
                    $(".salary_deduction").html(
                        "<i class='fa fa-inr'></i> " + data_res.pay_amount
                    );
                    $(".salary_deduction").attr("data-value", data_res.pay_amount);
                    var allotedAmt = $("#allotedAmt").text();

                    allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                    var utilized = $(".flex_utilised").attr("data-value");
                    utilized = parseInt(utilized.replace(/,/g, ""));
                    var data_balance = allotedAmt - utilized;
                    $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                    $(".balance").attr("data-value", data_balance);
                    swal("", "successfully submitted");
                    setTimeout("location.reload(true);", 1000);
                } else {
                    swal("", "successfully submitted");
                    setTimeout("location.reload(true);", 1000);
                }
            }
        });
    });

    $("#resetgtli").click(function () {
        $("#glta_estimate").attr("data-value", 0);
        $("#glta_estimate").html("<i class='fa fa-inr'></i> " + 0);
        //       var flex_utilised =  parseInt(($(".flex_utilised").attr('data-value')) - parseInt($("#gtli_total_premium").text()));
        // $(".flex_utilised").html("<i class='fa fa-inr'></i> "+flex_utilised);
        //      $(".flex_utilised").attr("data-value",flex_utilised);
        var data_benefit = $(this).attr("data-value");
        $.ajax({
            type: "POST",
            url: "/flexi_benifit/reset_flexi_data",
            data: {
                benefit: data_benefit
            },
            success: function (response) {
                var data_res = JSON.parse(response);
                if (data_res.flex_amount != null && data_res.pay_amount != null) {
                    $(".flex_utilised").html(
                        "<i class='fa fa-inr'></i> " + data_res.flex_amount
                    );
                    $(".flex_utilised").attr("data-value", data_res.flex_amount);
                    $(".salary_deduction").html(
                        "<i class='fa fa-inr'></i> " + data_res.pay_amount
                    );
                    $(".salary_deduction").attr("data-value", data_res.pay_amount);
                    var allotedAmt = $("#allotedAmt").text();

                    allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
                    var utilized = $(".flex_utilised").attr("data-value");
                    utilized = parseInt(utilized.replace(/,/g, ""));
                    var data_balance = allotedAmt - utilized;
                    $(".balance").html("<i class='fa fa-inr'></i>" + data_balance);
                    $(".balance").attr("data-value", data_balance);
                    swal("", "successfully submitted");
                    setTimeout("location.reload(true);", 1000);
                } else {
                    swal("", "successfully submitted");
                    setTimeout("location.reload(true);", 1000);
                }
            }
        });
    });

    //var max_fields      = 5; //maximum input boxes allowed
    var wrapper = $(".form_row");
    var add_button = $(".add_more");
    var y = 1;
    add_button.click(function (e) {
        var family_date_birth = $("input[name=family_date_birth]").val();
        var first_name = $("input[name=first_name]").val();
        var last_name = $("input[name=last_name]").val();
        if (family_date_birth == "" || first_name == "" || last_name == "") {
            swal("", "Please First fill the form");
        } else {
            e.preventDefault();
            //if(y < max_fields){
            y++;

            var clone = $(".form_row_data:first")
                .clone()
                .find("input[type='text'] ")
                .val("")
                .end()
                .find(".family_date_birth")
                .removeAttr("id")
                .end()
                .append('<a  href="#" class="remove_field">Remove</a>');

            clone
                .find('div[name="disable_child"]')
                .children()
                .css("display", "none");
            //clone.find('div[name="disable_child"]').children()[1].style.display = "none";
            clone
                .find('div[name="disable_child"]')
                .find("input[type='radio']")
                .removeAttr("name")
                .prop("checked", false);
            clone
                .find('div[name="disable_child"]')
                .find("input[type='file']")
                .val("");

            var disable_child = document.getElementsByName("disable_child");
            wrapper.append(clone);

            for (i = 0; i < disable_child.length; ++i) {
                var disable_check = disable_child[i].querySelectorAll(
                    "input[type='radio']"
                );
                // console.log("i="+disable_check[0].checked);
                // console.log("i="+disable_check[1].checked);
                disable_check[0].name = "dis" + i;
                disable_check[1].name = "dis" + i;
                if (disable_check[0].checked) {
                    disable_check[0].checked = true;
                }

                if (disable_check[1].checked) {
                    disable_check[1].checked = true;
                }
            }
            //}
        }

    });
    wrapper.on("click", ".remove_field", function (e) {
        e.preventDefault();
        $(this)
            .parent("div")
            .remove();
        x--;
    });

    $("#emg_contact_num").keyup(function (e) {
        var $th = $(this);
        if (
            e.keyCode != 46 &&
            e.keyCode != 8 &&
            e.keyCode != 37 &&
            e.keyCode != 38 &&
            e.keyCode != 39 &&
            e.keyCode != 40
        ) {
            $th.val(
                $th.val().replace(/[^0-9]/g, function (str) {
                    return "";
                })
            );
        }
        return;
    });

    $.validator.addMethod(
        "valid_mobile",
        function (value, element, param) {
            var re = new RegExp("^[7-9][0-9]{9}$");
            return this.optional(element) || re.test(value); // Compare with regular expression
        },
        "Enter a valid 10 digit mobile number"
    );

    $.validator.addMethod(
        "validate_aadhar",
        function (value, element, param) {
            var reg = /^\d{12}$/g;
            return this.optional(element) || reg.test(value); // Compare with regular expression
        },
        "Enter a valid aadhar number"
    );

    $.validator.addMethod(
        "valid_pan_card",
        function (value, element, param) {
            var re = new RegExp("(^([a-zA-Z]{5})([0-9]{4})([a-zA-Z]{1})$)");
            return this.optional(element) || re.test(value); // Compare with regular expression
        },
        "Enter a valid PAN card number"
    );

    $.validator.addMethod(
        "validateEmail",
        function (value, element, param) {
            if (value.length == 0) {
                return true;
            }
            var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
            return reg.test(value); // Compare with regular expression
        },
        "Please enter a valid email address."
    );

    $("#emg_contact_per").keyup(function (e) {
        var $th = $(this);
        if (
            e.keyCode != 46 &&
            e.keyCode != 8 &&
            e.keyCode != 37 &&
            e.keyCode != 38 &&
            e.keyCode != 39 &&
            e.keyCode != 40
        ) {
            $th.val(
                $th.val().replace(/[^A-Z a-z\s]/g, function (str) {
                    return "";
                })
            );
        }
        return;
    });

    $("#mob_no,#nominee_contact").keyup(function (e) {
        var $th = $(this);
        if (
            e.keyCode != 46 &&
            e.keyCode != 8 &&
            e.keyCode != 37 &&
            e.keyCode != 38 &&
            e.keyCode != 39 &&
            e.keyCode != 40
        ) {
            $th.val(
                $th.val().replace(/[^0-9]/g, function (str) {
                    return "";
                })
            );
        }
        return;
    });
   $("#cheque_no,#bankAcNo").keyup(function (e) {
	  
        var $th = $(this);
        if (
            e.keyCode != 46 &&
            e.keyCode != 8 &&
            e.keyCode != 37 &&
            e.keyCode != 38 &&
            e.keyCode != 39 &&
            e.keyCode != 40
        ) {
            $th.val(
                $th.val().replace(/[^0-9]/g, function (str) {
                    return "";
                })
            );
        }
        return;
    });
    // edit emp data
    $("#edit_emp").click(function () {
        $("#alt_email").removeAttr("readonly", "readonly");
        $("#emg_contact_per").removeAttr("readonly", "readonly");
        $("#emg_contact_num").removeAttr("readonly", "readonly");
        $("#update_emp").removeAttr("style", "display:none");
        $("#mob_no").removeAttr("readonly", "readonly");
        $("#edit_emp").attr("style", "display:none");
        $("#alt_email,#emg_contact_per,#emg_contact_num,#mob_no").addClass(
            "editable"
        );
    });

    $("#emp_form").validate({
        ignore: ".ignore",
        rules: {
            masPolicy: {
                required: true
            },

            policySubType: {
                required: true
            },
            cusDet: {
                required: true
            }
        },
        messages: {
            alt_email: "Please specify email id",
            alt_email: "please enter valid email",
            emg_contact_num: "Please specify contact number"
        },
        submitHandler: function (form) { }
    });

    $("#emp_form_old").validate({
        ignore: ".ignore",
        rules: {
            firstname: {
                required: true
            },
			 salutation: {
                required: true
            },
            lastname: {
                required: true
            },

            dob: {
                required: true
            },
            date_join: {
                required: true
            },
            mob_no: {
                valid_mobile: true,
                required: true
            },
            email: {
                validateEmail: true,
                required: true
            },
            // panCard: {
                // valid_pan_card: true,
                // required: true
            // },
            // addCard: {
            //     validate_aadhar: true,
            //     required: true
            // },
            gender1: {
                required: true
            },
            comAdd: {
               // required: true
            },
            // perAdd: {
            //     required: true
            // }
        },
        messages: {
            alt_email: "Please specify email id",
            alt_email: "please enter valid email",
            emg_contact_num: "Please specify contact number"
        },
        submitHandler: function (form) {
            // debugger;

               var ISNRI = $("#ISNRI").val();
									var salutation_val = $("#salutation option:selected").val() 
									var salutation =$("#salutation option:selected").text();
									var gender = $('#gender1').val();
                                    if (ISNRI == 'Y' || salutation_val!='' ) {

                                        var all_data = $("#emp_form_old").serialize()+'&update_emp_id='+emp_id+'&salutation='+salutation+'&gender1='+gender;
                                        $.ajax({
                                            type: "POST",
                                            url: "/employee/save_emp_data",
                                            data: all_data,
                                            dataType: "json",
                                            success: function (result) {
                                                
                                            }
                                        });
                                    }
        }
    });

    // $.ajax({
    //   url: "/employee/get_all_policy_no",
    //   type: "POST",
    //   async: false,
    //   dataType: "json",
    //   success: function (response) {
    //     //$("#policy_no").val(response[0].policy_detail_id);
    //     $("#policy_nos").empty();
    //     $("#policy_nos").append('<option value=""> Select policy type</option>');
    //     for (i = 0; i < response.length; i++) {
    //       var date = response[i].end_date.split("-");
    //       var date = new Date(
    //         Number(date[0]),
    //         Number(date[1]) - 1,
    //         Number(date[2])
    //       );
    //       var current_date = new Date();
    //       if (date > current_date) {
    //         if (response[i].policy_sub_type_id == 1) {
    //           $("#policy_numberss").append(
    //             '<option  data-id = "' +
    //             response[i].policy_no +
    //             '" selected value="' +
    //             response[i].policy_detail_id +
    //             '">ds</option>'
    //           );
    //         }
    //       }
    //     }
    //   }
    // });

    $.ajax({
        url: "/employee/get_employee_data",
        type: "POST",
        async: false,
        dataType: "json",
        success: function (response) {
            // $("#firstname").val(response.emp_firstname + ' ' + response.emp_lastname);
            // $("#dob").val(response.bdate);
            // $("#date_join").val(response.doj);
            // $("#mob_no").val(response.mob_no);
            // $("#email").val(response.email);
            // $("#alt_email").val(response.alt_email);
            // $("#emg_contact_per").val(response.emp_emg_cont_name);
            // $("#emg_contact_num").val(response.emg_cno);
            // $("#designation").val(response.designation_name);
        }
    });

    // reltionship
    /* $.ajax({
          url: "/employee/get_all_gmc_policy",
          type: "POST",
          data: {
              
          },
          async: true,
          dataType: "json",
          success: function (response) {
              if (response == '')
              {
                  $('#family_members_id').empty();
                  $('#policy_id').empty();
              } else
              {
                  var policy_id = response[0].policy_id;
                  $('#policy_id').val(policy_id);
                  $('#family_members_id').empty();
                  $('#family_members_id').append('<option value=""> Select</option>');
                  if(response[0].premium_type == "flate"){
                      for (i = 0; i < response.length; i++) {


                          if (response[i].max_adult > 1 && response[i].fr_id == 1 || response[i].fr_id == 0) {
                              $('#family_members_id').append('<option value="' + response[i].fr_id + '">' + response[i].fr_name + '</option>');
                          } else if ((response[i].max_adult == 2 && response[i].max_child == 0) && (response[i].fr_id == 4 || response[i].fr_id == 5 || response[i].fr_id == 6 || response[i].fr_id == 7)) {

                              $('#family_members_id').append('<option value="' + response[i].fr_id + '">' + response[i].fr_name + '</option>');
                          } else if ((response[i].max_adult == 4 && response[i].max_child == 0) && (response[i].fr_id == 4 || response[i].fr_id == 5 || response[i].fr_id == 6 || response[i].fr_id == 7)) {

                              $('#family_members_id').append('<option value="' + response[i].fr_id + '">' + response[i].fr_name + '</option>');
                          } else if (response[i].max_adult > 2 && (response[i].fr_id == 4 || response[i].fr_id == 5 || response[i].fr_id == 6 || response[i].fr_id == 7 || response[i].fr_id == 1 || response[i].fr_id == 0)) {

                              $('#family_members_id').append('<option value="' + response[i].fr_id + '">' + response[i].fr_name + '</option>');
                          } else if (response[i].fr_id == 2 || response[i].fr_id == 3) {

                              $('#family_members_id').append('<option value="' + response[i].fr_id + '">' + response[i].fr_name + '</option>');
                          }
                     
                  }
                  }
                  

                 else {  
                     for (i = 0; i < response.length; i++) {

                      if (response[i].fr_id != 0) {

                          if (response[i].max_adult > 1 && response[i].fr_id == 1) {
                              $('#family_members_id').append('<option value="' + response[i].fr_id + '">' + response[i].fr_name + '</option>');
                          } else if ((response[i].max_adult == 2 && response[i].max_child == 0) && (response[i].fr_id == 4 || response[i].fr_id == 5 || response[i].fr_id == 6 || response[i].fr_id == 7)) {

                              $('#family_members_id').append('<option value="' + response[i].fr_id + '">' + response[i].fr_name + '</option>');
                          } else if ((response[i].max_adult == 4 && response[i].max_child == 0) && (response[i].fr_id == 4 || response[i].fr_id == 5 || response[i].fr_id == 6 || response[i].fr_id == 7)) {

                              $('#family_members_id').append('<option value="' + response[i].fr_id + '">' + response[i].fr_name + '</option>');
                          } else if (response[i].max_adult > 2 && (response[i].fr_id == 4 || response[i].fr_id == 5 || response[i].fr_id == 6 || response[i].fr_id == 7 || response[i].fr_id == 1)) {

                              $('#family_members_id').append('<option value="' + response[i].fr_id + '">' + response[i].fr_name + '</option>');
                          } else if (response[i].fr_id == 2 || response[i].fr_id == 3) {

                              $('#family_members_id').append('<option value="' + response[i].fr_id + '">' + response[i].fr_name + '</option>');
                          }
                      }
                  } }
                 

                 
              }
          }
      }); */

    /*$.ajax({
        url: "/employee/get_policy_member_details",
        type: "POST",
        async: false,
        dataType: "json",
        success: function (response) {
          
					if(response.length == 0){
						$(".submitparents").hide();  
					  }
            if (response.length > 0) {
                var arr = [];
                var tag = $("#hidden_tag").val();
                var sum_data = 0;
                var flag = false;
                var flag1 = false;
                var flag2 = false;
                
               
                $.each(response, function (index, value) {
					
                    
                    var status = 0
                    if (value.tpa_member_id) {
                        status = 1;
                    }
                    if (value.TPA_id) {
                        tpa_id = value.TPA_id;
                    } else {
                        tpa_id = '';
                    }
                    if (value.employee_policy_mem_sum_premium != 0) {
                        sum_data += parseInt(value.employee_policy_mem_sum_premium);
                        flag = true;
                    }
                    if (value.sum_insured_type == "individual" || value.sum_insured_type == "familyIndividual") {

                        flag1 = true;
                    }
                    if (value.sum_insured_type == "familyGroup") {

                        flag2 = true;
                    }
					
					if(value.show_payment == "Y"){
					var payment = value.show_payment; 
					var payment_url = value.payment_url;
					$(".payment_yrld").show();
					}
					
                    arr.push(value.tpa_member_name + "," + value.tpa_member_id + "," + value.policy_no + "," + status + "," + tpa_id);
                    var str = value.tpa_member_name + "," + value.tpa_member_id + "," + value.policy_no + "," + status + "," + tpa_id;
                   if(value.premium_type == "parent"){
					  
					   if(index == 0){
						    $("input[name='gmc'][value='"+value.policy_mem_sum_insured+"']").prop("checked",true);
							$("#gmc_parent_" + value.policy_mem_sum_insured + "").trigger("click");
							$("input[type='radio']").trigger("change");
							
					   }
					   
					  
					   
				   if (tag == 'no') {

                            var markup = '<tr><td scope="row"><div class="form-group"> <input class="form-control align-center"  type="text" value=' + value.fr_name + ' name="relation_name" id="relation_name" readonly=readonly> </div></td><td hidden><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.fr_id + ' id="fr_id" name="fr_id" readonly=readonly > </div></td> <td hidden><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.family_id + ' id="family_id" readonly=readonly > </div></td><td><div class="form-group"> <input class="form-control align-center"   style="width: 150px;" type="tel" value="' + value.emp_firstname + '" id="fname" name="fname" readonly=readonly > </div></td><td><div class="form-group"> <input  style="width: 150px;" class="form-control align-center" type="text" value=' + value.emp_lastname + ' id="lname" name="lname" readonly=readonly> </div></td><td style="width:200px"><div class="form-group"> <input class="form-control align-center" type="text" value=' + value.gender + ' id="gender" name="gender" readonly=readonly > </div></td><td><div style="width: 105px;" class="form-group"> <input class="form-control align-center bdate_family"  type="text" name="bdate"  value=' + value.bdate + '  readonly=readonly> </div></td><td><div class="form-group"> <input class="form-control align-center" type="text" value=' + value.age + ' name="age" id="age" readonly=readonly> </div></td><td><div class="form-group"> <input class="form-control align-center" type="text" value=' + value.age_type + ' id="age_types" name="age_types" readonly=readonly> </div></td><td class="sumInsure_data" style="display:none"><div class="form-group"> <input class="form-control align-center " type="text" value=' + value.policy_mem_sum_insured + ' id="policy_sum" name="policy_sum" readonly=readonly > </div></td><td class="premium_data" style="display:none"><div class="form-group"> <input class="form-control align-center "  type="text" value=' + value.employee_policy_mem_sum_premium + ' id="policy_premium"  name="policy_premium" readonly=readonly> </div></td><td hidden><div class="form-group"> <input class="form-control align-center" type="hidden"  id="pay_type" value="' + value.pay_type + '" name="pay_type" readonly=readonly> </div></td><td hidden><div class="form-group"> <input class="form-control align-center" type="text"  id="disable_c"  name="disable_c" value="" readonly=readonly> </div></td> <td hidden><div class="form-group"> <input class="form-control align-center" type="file"  id="disable_c_d" value="" name="disable_c_d" readonly=readonly> </div></td> <td hidden><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.policy_member_id + ' id="member_id" name="member_id" readonly=readonly > </div></td><td hidden><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.policy_detail_id + ' id="lname" readonly=readonly> </div></td><td><img class= "ecard_link_download" src="/public/assets/images/new-icons/pdf.png" data-id="' + str + '"   style="cursor: pointer;"></td></tr>';

                      
                    } else {
                        

                            var markup = '<tr><td scope="row"><div class="form-group"> <input class="form-control align-center"  type="text" value=' + value.fr_name + ' name="relation_name" id="relation_name" readonly=readonly> </div></td><td hidden><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.fr_id + ' id="fr_id" name="fr_id" readonly=readonly > </div></td> <td hidden><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.family_id + ' id="family_id" readonly=readonly > </div></td><td><div class="form-group"> <input class="form-control align-center"   style="width: 150px;" type="tel" value="' + value.emp_firstname + '" id="fname" name="fname" readonly=readonly > </div></td><td><div class="form-group"> <input  style="width: 150px;" class="form-control align-center" type="text" value=' + value.emp_lastname + ' id="lname" name="lname" readonly=readonly> </div></td><td style="width:200px"><div class="form-group"> <input class="form-control align-center" type="text" value=' + value.gender + ' id="gender" name="gender" readonly=readonly > </div></td><td><div style="width: 105px;" class="form-group"> <input class="form-control align-center bdate_family"  type="text" name="bdate"  value=' + value.bdate + '  readonly=readonly> </div></td><td><div class="form-group"> <input class="form-control align-center" type="text" value=' + value.age + ' name="age" id="age" readonly=readonly> </div></td><td><div class="form-group"> <input class="form-control align-center" type="text" value=' + value.age_type + ' id="age_types" name="age_types" readonly=readonly> </div></td><td class="sumInsure_data" style="display:none"><div class="form-group"> <input class="form-control align-center  " type="text" value=' + value.policy_mem_sum_insured + ' id="policy_sum" name="policy_sum" readonly=readonly > </div></td><td class="premium_data" style="display:none"><div class="form-group"> <input class="form-control align-center "  type="text" value=' + value.employee_policy_mem_sum_premium + ' id="policy_premium"  name="policy_premium" readonly=readonly> </div></td><td hidden><div class="form-group"> <input class="form-control align-center" type="hidden"  id="pay_type" value="' + value.pay_type + '" name="pay_type" readonly=readonly> </div></td><td hidden><div class="form-group"> <input class="form-control align-center" type="text"  id="disable_c"  name="disable_c" value="" readonly=readonly> </div></td> <td hidden><div class="form-group"> <input class="form-control align-center" type="file"  id="disable_c_d" value="" name="disable_c_d" readonly=readonly> </div></td> <td hidden><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.policy_member_id + ' id="member_id" name="member_id" readonly=readonly > </div></td><td hidden><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.policy_detail_id + ' id="lname" readonly=readonly> </div></td><td><img class= "ecard_link_download" src="/public/assets/images/new-icons/pdf.png" data-id="' + str + '"   style="cursor: pointer;"></td> <td><button type="button" data-id=' + value.family_relation_id + ' data_memid=' + value.policy_member_id + ' onclick="update_fun(' + value.family_relation_id + ')" value="' + value.family_relation_id + '" class="btn edit_mem" name="edit_mem" id=' + value.family_relation_id + ' >Edit</button> <button hidden type="button" data-id=' + value.family_relation_id + ' data-mem-id=' + value.policy_member_id + ' onclick="update_data(' + value.family_relation_id + ')" value="' + value.family_relation_id + '" class=" btn update_mem" name="update_mem" id=' + value.family_relation_id + ' >Update</button></td><td><input type="button" onclick="delete_data(' + value.policy_member_id + ')"  data-id="' + value.policy_detail_id + '"class="btn delete_mem" name="delete_mem" id=' + value.policy_member_id + ' value="Delete"></td></tr>';

                        
                    }
				   }
				   
				   else {
				   if (tag == 'no') {
                        if (value.fr_name == 'Self') {
                            var markup = '<tr><td scope="row" style="width:100%;"><div class="form-group"> <input class="form-control align-center" type="tel" value="' + value.fr_name + '" id="relation_name" readonly=readonly> </div></td> <td style="width:100px;"><div class="form-group"> <input style="width: 150px;" class="form-control align-center" type="tel" value="' + value.emp_firstname + '" id="fname" readonly=readonly > </div></td><td style="width:100px;"><div class="form-group"> <input class="form-control align-center" style="width: 150px;" type="tel" value=' + value.emp_lastname + ' id="lname" readonly=readonly> </div></td><td><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.gender + ' id="example-tel-input" readonly=readonly > </div></td><td><div class="form-group" style="width: 105px;"> <input class="form-control align-center bdate_family"  type="tel" value=' + value.bdate + ' id="example-tel-input" readonly=readonly> </div></td><td><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.age + ' id="example-tel-input" readonly=readonly> </div></td><td><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.age_type + ' id="example-tel-input" readonly=readonly> </div></td><td class="sumInsure_data" style="display:none"><div class="form-group"> <input class="form-control align-center" type="tel" id="policy_sum" value=' + value.policy_mem_sum_insured + ' id="example-tel-input" readonly=readonly > </div></td><td class="premium_data" style="display:none"><div class="form-group"> <input class="form-control align-center" id="policy_premium" type="tel" value=' + value.employee_policy_mem_sum_premium + ' id="example-tel-input" readonly=readonly> </div></td> <td hidden><div class="form-group"> <input class="form-control align-center" type="hidden"  id="pay_type" value="' + value.pay_type + '" name="pay_type" readonly=readonly> </div></td><td><img class= "ecard_link_download" src="/public/assets/images/new-icons/pdf.png" data-id="' + str + '" style="cursor: pointer;"></td><td class="payment_yrld" style="display:none"><a href="'+payment_url+'"><img class= "" src="/public/assets/images/new-icons/pdf.png" data-id="' + payment + '" style="cursor: pointer;"></a></td></tr>';
                        } else {

                            var markup = '<tr><td scope="row" style="width:100%;"><div class="form-group"> <input class="form-control align-center"  type="text" value=' + value.fr_name + ' name="relation_name" id="relation_name" readonly=readonly> </div></td><td hidden><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.fr_id + ' id="fr_id" name="fr_id" readonly=readonly > </div></td> <td hidden><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.family_id + ' id="family_id" readonly=readonly > </div></td><td><div class="form-group"> <input class="form-control align-center"   style="width: 150px;" type="tel" value="' + value.emp_firstname + '" id="fname" name="fname" readonly=readonly > </div></td><td><div class="form-group"> <input  style="width: 150px;" class="form-control align-center" type="text" value=' + value.emp_lastname + ' id="lname" name="lname" readonly=readonly> </div></td><td style="width:200px"><div class="form-group"> <input class="form-control align-center" type="text" value=' + value.gender + ' id="gender" name="gender" readonly=readonly > </div></td><td><div style="width: 105px;" class="form-group"> <input class="form-control align-center bdate_family"  type="text" name="bdate"  value=' + value.bdate + '  readonly=readonly> </div></td><td><div class="form-group"> <input class="form-control align-center" type="text" value=' + value.age + ' name="age" id="age" readonly=readonly> </div></td><td><div class="form-group"> <input class="form-control align-center" type="text" value=' + value.age_type + ' id="age_types" name="age_types" readonly=readonly> </div></td><td class="sumInsure_data" style="display:none"><div class="form-group"> <input class="form-control align-center policy_sums" type="text" value=' + value.policy_mem_sum_insured + ' id="policy_sum" name="policy_sum" readonly=readonly > </div></td><td class="premium_data" style="display:none; width:100px"><div class="form-group"> <input class="form-control align-center "  type="text" value=' + value.employee_policy_mem_sum_premium + ' id="policy_premium"  name="policy_premium" readonly=readonly> </div></td><td hidden><div class="form-group"> <input class="form-control align-center" type="hidden"  id="pay_type" value="' + value.pay_type + '" name="pay_type" readonly=readonly> </div></td><td hidden><div class="form-group"> <input class="form-control align-center" type="text"  id="disable_c"  name="disable_c" value="" readonly=readonly> </div></td> <td hidden><div class="form-group"> <input class="form-control align-center" type="file"  id="disable_c_d" value="" name="disable_c_d" readonly=readonly> </div></td> <td hidden><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.policy_member_id + ' id="member_id" name="member_id" readonly=readonly > </div></td><td hidden><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.policy_detail_id + ' id="lname" readonly=readonly> </div></td><td><img class= "ecard_link_download" src="/public/assets/images/new-icons/pdf.png" data-id="' + str + '"   style="cursor: pointer;"></td><td class="payment_yrld" style="display:none"></td></tr>';

                        }
                    } else {
                        if (value.fr_name == 'Self') {
                            var markup = '<tr><td scope="row" style="width:100%;"><div class="form-group"> <input class="form-control align-center" type="tel" value="' + value.fr_name + '" id="relation_name" readonly=readonly> </div></td> <td><div class="form-group"> <input style="width: 150px;" class="form-control align-center" type="tel" value="' + value.emp_firstname + '" id="fname" readonly=readonly > </div></td><td><div class="form-group"> <input class="form-control align-center" style="width: 150px;" type="tel" value=' + value.emp_lastname + ' id="lname" readonly=readonly> </div></td><td style="width:100px"><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.gender + ' id="example-tel-input" readonly=readonly > </div></td><td><div class="form-group" style="width: 105px;"> <input class="form-control align-center bdate_family"  type="tel" value=' + value.bdate + ' id="example-tel-input" readonly=readonly> </div></td><td style="width:100px"><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.age + ' id="example-tel-input" readonly=readonly> </div></td><td style="width:100px"><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.age_type + ' id="example-tel-input" readonly=readonly> </div></td><td class="sumInsure_data" style="width:100px; display:none"><div class="form-group"> <input class="form-control align-center" type="tel" id="policy_sum" value=' + value.policy_mem_sum_insured + ' id="example-tel-input" readonly=readonly > </div></td><td class="premium_data" style="display:none"><div class="form-group"> <input class="form-control align-center" id="policy_premium" type="tel" value=' + value.employee_policy_mem_sum_premium + ' id="example-tel-input" readonly=readonly> </div></td> <td hidden><div class="form-group"> <input class="form-control align-center" type="hidden"  id="pay_type" value="' + value.pay_type + '" name="pay_type" readonly=readonly> </div></td><td><img class= "ecard_link_download" src="/public/assets/images/new-icons/pdf.png" data-id="' + str + '" style="cursor: pointer;"></td><td  class="payment_yrld" style="display:none"><a href="'+payment_url+'"><img class= "" src="/public/assets/images/new-icons/pdf.png" data-id="' + payment + '" style="cursor: pointer;"></a></td></tr>';
                        } else {

                            var markup = '<tr><td scope="row" style="width:100%;"><div class="form-group"> <input class="form-control align-center"  type="text" value=' + value.fr_name + ' name="relation_name" id="relation_name" readonly=readonly> </div></td><td hidden><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.fr_id + ' id="fr_id" name="fr_id" readonly=readonly > </div></td> <td hidden><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.family_id + ' id="family_id" readonly=readonly > </div></td><td><div class="form-group"> <input class="form-control align-center"   style="width: 150px;" type="tel" value="' + value.emp_firstname + '" id="fname" name="fname" readonly=readonly > </div></td><td><div class="form-group"> <input  style="width: 150px;" class="form-control align-center" type="text" value=' + value.emp_lastname + ' id="lname" name="lname" readonly=readonly> </div></td><td style="width:200px"><div class="form-group"> <input class="form-control align-center" type="text" value=' + value.gender + ' id="gender" name="gender" readonly=readonly > </div></td><td style="width:100px"><div style="width: 105px;" class="form-group"> <input class="form-control align-center bdate_family"  type="text" name="bdate"  value=' + value.bdate + '  readonly=readonly> </div></td><td><div class="form-group"> <input class="form-control align-center" type="text" value=' + value.age + ' name="age" id="age" readonly=readonly> </div></td><td><div class="form-group"> <input class="form-control align-center" type="text" value=' + value.age_type + ' id="age_types" name="age_types" readonly=readonly> </div></td><td class="sumInsure_data" style="display:none; width:100px"><div class="form-group"> <input class="form-control align-center policy_sums " type="text" value=' + value.policy_mem_sum_insured + ' id="policy_sum" name="policy_sum" readonly=readonly > </div></td><td class="premium_data" style="display:none"><div class="form-group"> <input class="form-control align-center "  type="text" value=' + value.employee_policy_mem_sum_premium + ' id="policy_premium"  name="policy_premium" readonly=readonly> </div></td><td hidden><div class="form-group"> <input class="form-control align-center" type="hidden"  id="pay_type" value="' + value.pay_type + '" name="pay_type" readonly=readonly> </div></td><td hidden><div class="form-group"> <input class="form-control align-center" type="text"  id="disable_c"  name="disable_c" value="" readonly=readonly> </div></td> <td hidden><div class="form-group"> <input class="form-control align-center" type="file"  id="disable_c_d" value="" name="disable_c_d" readonly=readonly> </div></td> <td hidden><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.policy_member_id + ' id="member_id" name="member_id" readonly=readonly > </div></td><td hidden><div class="form-group"> <input class="form-control align-center" type="tel" value=' + value.policy_detail_id + ' id="lname" readonly=readonly> </div></td><td><img class= "ecard_link_download" src="/public/assets/images/new-icons/pdf.png" data-id="' + str + '"   style="cursor: pointer;"></td><td class="payment_yrld" style="display:none"></td> <td><button type="button" data-id=' + value.family_relation_id + ' data_memid=' + value.policy_member_id + ' onclick="update_fun(' + value.family_relation_id + ')" value="' + value.family_relation_id + '" class="btn edit_mem" name="edit_mem" id=' + value.family_relation_id + ' >Edit</button> <button hidden type="button" data-id=' + value.family_relation_id + ' data-mem-id=' + value.policy_member_id + ' onclick="update_data(' + value.family_relation_id + ')" value="' + value.family_relation_id + '" class=" btn update_mem" name="update_mem" id=' + value.family_relation_id + ' >Update</button></td><td><input type="button" onclick="delete_data(' + value.policy_member_id + ')"  data-id="' + value.policy_detail_id + '"class="btn delete_mem" name="delete_mem" id=' + value.policy_member_id + ' value="Delete"></td></tr>';

                        }
                    }
				   }




                    $("table #members_policy_enroll").append(markup);


                });
                if (flag) {

                    $("#pre_id").removeAttr('style', 'display:none');
                    $(".premium_data").attr("style", "display:block");
                    $(".agree_spa").attr("style", "display:block");
                    $(".salary_gmc_deductions").text(sum_data);


                }
                if (flag1) {

                    $(".sumInsure_data").removeAttr("style", "display:none");
                    $("#sum_insured_td").removeAttr('style', 'display:none');

                }
                if (flag2) {

                    $(".sumInsure_data").removeAttr("style", "display:none");
                    $("#sum_insured_td").removeAttr('style', 'display:none');
                    $(".policy_sums").val(0);

                }
            }

        }
    }); */

    $.ajax({
        url: "/employee/get_all_dob",
        type: "POST",
        data: {
            emp_id: emp_id
        },
        async: false,
        dataType: "json",
        success: function (response) {
            if (response.famil_date != null) {
                var res = response.famil_date;
                selectChange = $(this).closest(".form_row_data");
                // console.log(selectChange);
                if (res.length != 0) {
                    $("input[name='s_date']").val(res.family_dob);
                }
            }
        }
    });

    //    $.ajax({
    //        url: "/get_all_policy_no",
    //        type: "POST",
    //        async: false,
    //        dataType: "json",
    //        success: function (response) {
    //
    //            $('#policy_no').empty();
    //            $('#policy_no').append('<option value=""> Select policy type</option>');
    //            for (i = 0; i < response.length; i++) {
    //                var date = response[i].end_date.split("-");
    //                var date = new Date(Number(date[0]), Number(date[1]) - 1, Number(date[2]));
    //                var current_date = new Date();
    //                if (date > current_date) {
    //                    if (response[i].policy_sub_type_id == 1) {
    //                        $('#policy_no').append('<option selected value="' + response[i].policy_detail_id + '">' + response[i].policy_detail_id + '</option>');
    //                    }
    //                }
    //            }
    //        }
    //    });

    // suminsured and premium
    $.ajax({
        url: "/employee/get_all_topuppolicy_suminsured",
        type: "POST",
        async: false,
        dataType: "json",
        success: function (response) {
            if (response.gmc_topup != "") {
                $.each(response.gmc_topup, function (index, value) {
                    for (i = 0; i < response.gmc_topup_suminsured.length; i++) {
                        $(".gmctopup_div").before(
                            '<div class="col-md-3"><div class="custom-control custom-radio"><input type="radio" id="gmc_' +
                            response.gmc_topup_suminsured[i] +
                            '" name="gmc" class="custom-control-input sum_insured_check" data-id="' +
                            response.gmc_premium[i] +
                            '" value="' +
                            response.gmc_topup_suminsured[i] +
                            '"><label class="custom-control-label" for="gmc_' +
                            response.gmc_topup_suminsured[i] +
                            '"><i class="fa fa-inr"></i><span id="">' +
                            response.gmc_topup_suminsured[i] +
                            "</span></label></div><div>"
                        );

                        $("#gmc_" + response.gmc_topup_suminsured[i] + "").click(
                            function () {
                                $("#ghi_estimate").html(
                                    "<i class='fa fa-inr'></i> " +
                                    parseInt($(this).attr("data-id"))
                                );
                                $("#ghi_estimate").attr(
                                    "data-value",
                                    parseInt($(this).attr("data-id"))
                                );
                                $("#ghi_si").text($(this).val());
                                $("#ghi_total_premium").text($(this).attr("data-id"));
                            }
                        );
                    }
                });
            }

            if (response.gpa_topup != "") {
                $.each(response.gpa_topup, function (index, value) {
                    for (i = 0; i < response.gpa_topup_suminsured.length; i++) {
                        //console.log(response.gpa_topup_suminsured.length);
                        //alert(response.gpa_topup_suminsured);
                        $(".gpa_topupdiv").before(
                            '<div class="col-md-3"><div class="custom-control custom-radio"><input type="radio" id="gpa_' +
                            response.gpa_topup_suminsured[i] +
                            '" name="gpa" class="custom-control-input sum_insured_check" data-id="' +
                            response.gpa_premium[i] +
                            '" value="' +
                            response.gpa_topup_suminsured[i] +
                            '"><label class="custom-control-label" for="gpa_' +
                            response.gpa_topup_suminsured[i] +
                            '"><i class="fa fa-inr"></i><span id="">' +
                            response.gpa_topup_suminsured[i] +
                            "</span></label></div><div>"
                        );
                        // $("#gpa_estimate").html("<i class='fa fa-inr'></i> "+"");
                        // $("#gpa_estimate").attr("data-value","");
                        $("#gpa_" + response.gpa_topup_suminsured[i] + "").click(
                            function () {
                                $("#gpa_estimate").html(
                                    "<i class='fa fa-inr'></i> " +
                                    parseInt($(this).attr("data-id"))
                                );
                                $("#gpa_estimate").attr(
                                    "data-value",
                                    parseInt($(this).attr("data-id"))
                                );
                                $("#gpa_si").text($(this).val());
                                $("#gpa_total_premium").text($(this).attr("data-id"));
                            }
                        );
                    }
                });
            }

            if (response.gtli_topup != "") {
                $.each(response.gtli_topup, function (index, value) {
                    for (i = 0; i < response.gtli_topup_suminsured.length; i++) {
                        $(".gtli_topupdiv").before(
                            '<div class="col-md-3"><div class="custom-control custom-radio"><input type="radio" id="gtli_' +
                            response.gtli_topup_suminsured[i] +
                            '" name="gtli" class="custom-control-input sum_insured_check" data-id="' +
                            response.gtli_premium[i] +
                            '" value="' +
                            response.gtli_topup_suminsured[i] +
                            '"><label class="custom-control-label" for="gtli_' +
                            response.gtli_topup_suminsured[i] +
                            '"><i class="fa fa-inr"></i><span id="">' +
                            response.gtli_topup_suminsured[i] +
                            "</span></label></div><div>"
                        );

                        $("#gtli_" + response.gtli_topup_suminsured[i] + "").click(
                            function () {
                                getPremiumCalc($(this).val());

                                //                        $("#gtli_estimate").html("<i class='fa fa-inr'></i> " + parseInt($(this).attr('data-id')));
                                //                        $("#gtli_estimate").attr("data-value", parseInt($(this).attr('data-id')));
                                $("#gtli_si").text($(this).val());
                                //                        $("#gtli_total_premium").text($(this).attr('data-id'));
                            }
                        );
                    }
                });
            }
        }
    });

    // get all utilized data
    /*$.ajax({
      url: "/flexi_benifit/get_utilised_data",
      type: "POST",
      async: false,
      dataType: "json",
      success: function (response) {
        if (response.flex_data == null) {
          $(".flex_utilised").html("<i class='fa fa-inr'></i>0");
          $(".flex_utilised").attr("data-value", 0);
        } else {
          $(".flex_utilised").html(
            "<i class='fa fa-inr'></i>" + response.flex_data
          );
          $(".flex_utilised").attr("data-value", response.flex_data);
        }

        if (response.salary_data == null) {
          $(".salary_deduction").html("<i class='fa fa-inr'></i>0");
          $(".salary_deduction").attr("data-value", 0);
        } else {
          $(".salary_deduction").html(
            "<i class='fa fa-inr'></i>" + response.salary_data
          );
          $(".salary_deduction").attr("data-value", response.salary_data);
        }
        var allotedAmt = $("#allotedAmt").text();
        allotedAmt = parseInt(allotedAmt.replace(/,/g, ""));
        var utilized = $(".flex_utilised").attr("data-value");

        try {
          utilized = parseInt(utilized.replace(/,/g, ""));
        } catch (utilized) {
          utilized = 0;
        }
        if (allotedAmt > utilized) {
          var data = allotedAmt - utilized;
          $(".balance").html("<i class='fa fa-inr'></i>" + data);
          $(".balance").attr("data-value", data);
          $("#wallet_ut").text(data);
        } else {
          $(".balance").attr("data-value", 0);
          $("#wallet_ut").text(0);
        }

        if (response.benifit_data !== null) {
          var pay_bal = 0;
          $.each(response.benifit_data, function (index, value) {
            // console.log(value.final_amount);
            $("#benifit_" + value.master_flexi_benefit_id)
              .find(".estimate_box")
              .html("<i class='fa fa-inr'></i> " + value.final_amount);
            $("#benifit_" + value.master_flexi_benefit_id)
              .find(".estimate_box")
              .attr("data-value", value.final_amount);
            $("#benifit_" + value.master_flexi_benefit_id)
              .find(".estimate_box")
              .attr("data-type", value.deduction_type);

            if (value.master_flexi_benefit_id == 3) {
              var spilit_val = value.deduction_type.split(",");
              if (spilit_val.length > 1) {
                $("#benifit_" + value.master_flexi_benefit_id)
                  .find(".payment_type[value='" + spilit_val[0] + "']")
                  .click();
              } else {
                $("#benifit_" + value.master_flexi_benefit_id)
                  .find(".payment_type[value='" + value.deduction_type + "']")
                  .click();
              }
              pay_bal += parseFloat(value.pay_amount);
              $("#ghi_si").text(value.sum_insured);
              $("#ghi_total_premium").text(value.final_amount);
            }

            if (value.master_flexi_benefit_id == 4) {
              var spilit_val = value.deduction_type.split(",");
              if (spilit_val.length > 1) {
                $("#benifit_" + value.master_flexi_benefit_id)
                  .find(".payment_type[value='" + spilit_val[0] + "']")
                  .click();
              } else {
                $("#benifit_" + value.master_flexi_benefit_id)
                  .find(".payment_type[value='" + value.deduction_type + "']")
                  .click();
              }
              pay_bal += parseFloat(value.pay_amount);
              $("#gpa_si").text(value.sum_insured);
              $("#gpa_total_premium").text(value.final_amount);
            }
            if (value.master_flexi_benefit_id == 5) {
              var spilit_val = value.deduction_type.split(",");
              if (spilit_val.length > 1) {
                $("#benifit_" + value.master_flexi_benefit_id)
                  .find(".payment_type[value='" + spilit_val[0] + "']")
                  .click();
              } else {
                $("#benifit_" + value.master_flexi_benefit_id)
                  .find(".payment_type[value='" + value.deduction_type + "']")
                  .click();
              }
              pay_bal += parseFloat(value.pay_amount);
              $("#gtli_si").text(value.sum_insured);
              $("#glta_total_premium").text(value.final_amount);
            }
            // $("#benifit_"+value.master_flexi_benefit_id).find("input[value='"+value.final_amount+"']").attr('checked',true);
            setTimeout(function () {
              $("#benifit_" + value.master_flexi_benefit_id)
                .find("input[value='" + value.sum_insured + "']")
                .attr("checked", true);
            }, 10);
          });
          $(".salary_gmc_deduction").text(pay_bal);
        }
      }
  });*/

    /*$.ajax({
      url: "/flexi_benefit/get_emp_data_flexi",
      type: "POST",
      async: true,
      dataType: "json",
      success: function (response) {
        // $(".submit_ghi").css('pointer-events','none');
        $.each(response, function (index, value) {
          // gmc
          if (value.policy_sub_type_id == 1) {
            $(".submitghi").removeAttr("style", "pointer-events:none");
            $(".ghi_err").attr("style", "display:none");
          }
          // // gpa
          if (value.policy_sub_type_id == 2) {
            $(".submitgpa").removeAttr("style", "pointer-events: none");
            $(".gpa_err").attr("style", "display:none");
          }
          // gtli
          if (value.policy_sub_type_id == 3) {
            $(".submitgtli").removeAttr("style", "pointer-events: none");
            $(".gtli_err").attr("style", "display:none");
          }
        });
      }
  });*/

    /*$.ajax({
      url: "/flexi_benifit/all_confirmed_flex_data",
      type: "POST",
      async: false,
      data: { policy_no: $("#policy_no").val() },
      dataType: "json",
      success: function (response) {
        if (response != null) {
          if (
            response.benifit_all_data != null &&
            response.nominee_data != null &&
            response.subQuery1 == "Y"
          ) {
            $("#payment_redirection").removeAttr("style", "display:none");
            $("#submit_flex_data").attr("style", "display:none");
            $("#customCheck51").attr("disabled");
            $("#submitGHI").attr("style", "display:none");
            $("#submitGPA").attr("style", "display:none");
            $("#submitGtli").attr("style", "display:none");
            $("#resetghi").attr("style", "display:none");
            $("#resetgpa").attr("style", "display:none");
            $("#resetgtli").attr("style", "display:none");
            $("#policy_nominee_form_data").css("pointer-events", "none");
            $("#submit_nominee").css("pointer-events", "none");
            $("#add_more_form").css("pointer-events", "none");
            $("#table_tbody").css("pointer-events", "none");
            $("#enrollment_data").css("pointer-events", "none");
            $("#members_policy_enroll").css("pointer-events", "none");
            $("#edit_emp").css("pointer-events", "none");

            // $("#submitGHI").attr('style','float: right');
            // $("#submitGtli").attr('style','float: right');
            // $("#submitGPA").attr('style','float: right');
          } else if (
            response.benifit_all_data != null ||
            response.subQuery1 == "Y"
          ) {
            $("#payment_redirection").removeAttr("style", "display:none");
            // $(".all_div").css('pointer-events','none');
            $("#submit_flex_data").attr("style", "display:none");
            $("#customCheck51").attr("disabled");
            $("#submitGHI").attr("style", "display:none");
            $("#submitGPA").attr("style", "display:none");
            $("#submitGtli").attr("style", "display:none");
            $("#resetghi").attr("style", "display:none");
            $("#resetgpa").attr("style", "display:none");
            $("#resetgtli").attr("style", "display:none");
            $("#policy_nominee_form_data").css("pointer-events", "none");
            $("#submit_nominee").css("pointer-events", "none");
            $("#add_more_form").css("pointer-events", "none");
            $("#table_tbody").css("pointer-events", "none");
            $("#enrollment_data").css("pointer-events", "none");
            $("#members_policy_enroll").css("pointer-events", "none");
            $("#edit_emp").css("pointer-events", "none");
            // // $("#resetgpa").attr('style','display:none');
            // // $("#resetgtli").attr('style','display:none');
            // $(".all_div").css('pointer-events','none');
          } else {
            $("#payment_redirection").removeAttr("style", "display:none");
            $("#submit_flex_data").removeAttr("style", "display:none");
            $("#customCheck51").removeAttr("disabled");
            $("#submitGHI").removeAttr("style", "display:none");
            $("#submitGPA").removeAttr("style", "display:none");
            $("#submitGtli").removeAttr("style", "display:none");
            $("#resetghi").removeAttr("style", "display:none");
            $("#resetgpa").removeAttr("style", "display:none");
            $("#resetgtli").removeAttr("style", "display:none");
            $("#policy_nominee_form_data").css("pointer-events", "auto");
            $("#submit_nominee").css("pointer-events", "auto");
            $("#add_more_form").css("pointer-events", "auto");
            $("#table_tbody").css("pointer-events", "auto");
            $("#enrollment_data").css("pointer-events", "auto");
            $("#members_policy_enroll").css("pointer-events", "auto");
            $("#edit_emp").css("pointer-events", "auto");
          }
        } else {
          $("#payment_redirection").removeAttr("style", "display:none");
          $("#submit_flex_data").removeAttr("style", "display:none");
          $("#customCheck51").removeAttr("disabled");
          $("#submitGHI").removeAttr("style", "display:none");
          $("#submitGPA").removeAttr("style", "display:none");
          $("#submitGtli").removeAttr("style", "display:none");
          $("#resetghi").removeAttr("style", "display:none");
          $("#resetgpa").removeAttr("style", "display:none");
          $("#resetgtli").removeAttr("style", "display:none");
          $("#policy_nominee_form_data").css("pointer-events", "auto");
          $("#submit_nominee").css("pointer-events", "auto");
          $("#add_more_form").css("pointer-events", "auto");
          $("#table_tbody").css("pointer-events", "auto");
          $("#enrollment_data").css("pointer-events", "auto");
          $("#members_policy_enroll").css("pointer-events", "auto");
          $("#edit_emp").css("pointer-events", "auto");
        }
      }
  });*/

    $("#submit_flex_data").css("pointer-events", "none");
    $("#modal_edit").click(function () {
        $(".bd-example-modal-lg").modal("hide");
    });

    $(".bd-example-modal-lg").on("shown.bs.modal", function (e) {
        var policy_members_str = "";
        $("#policy_members_modal_body").html("");
        $("#members_policy_enroll tr").each(function () {
            policy_members_str += "<tr>";
            policy_members_str +=
                "<td>" +
                $(this)
                    .find("#relation_name")
                    .val() +
                "</td>";
            policy_members_str +=
                "<td>" +
                $(this)
                    .find("#fname")
                    .val() +
                " " +
                $(this)
                    .find("#lname")
                    .val() +
                "</td>";
            policy_members_str +=
                "<td>" +
                $(this)
                    .find(".bdate_family")
                    .val() +
                "</td>";
            policy_members_str +=
                "<td>" +
                $(this)
                    .find("#policy_sum")
                    .val() +
                "</td>";
            if (
                $(this)
                    .find("#policy_premium")
                    .val() != 0
            ) {
                policy_members_str +=
                    "<td>" +
                    $(this)
                        .find("#policy_premium")
                        .val() +
                    "</td>";
                policy_members_str +=
                    "<td>" +
                    $(this)
                        .find("#pay_type")
                        .val() +
                    "</td>";
            }

            policy_members_str += "</tr>";
        });

        $("#policy_members_modal_body").html(policy_members_str);

        var nominee_str = "";
        $("#nominee_modal_body").html("");

        $("#table_tbody tr").each(function () {
            nominee_str += "<tr>";
            nominee_str +=
                "<td>" +
                $(this)
                    .find("#rel_nominee")
                    .val() +
                "</td>";
            nominee_str +=
                "<td>" +
                $(this)
                    .find("#nominee_firstname")
                    .val() +
                " " +
                $(this)
                    .find("#nominee_lastname")
                    .val() +
                "</td>";
            nominee_str +=
                "<td>" +
                $(this)
                    .find(".nominee_date")
                    .val() +
                "</td>";
            nominee_str +=
                "<td>" +
                $(this)
                    .find("#share_per")
                    .val() +
                "%</td>";
            nominee_str +=
                "<td>" +
                $(this)
                    .find("#guardian_rel")
                    .val() +
                "</td>";
            nominee_str +=
                "<td>" +
                $(this)
                    .find("#guardian_name")
                    .val() +
                "</td>";
            nominee_str +=
                "<td>" +
                $(this)
                    .find(".guardina_date")
                    .val() +
                "</td>";
            nominee_str += "</tr>";
        });
        $("#nominee_modal_body").html(nominee_str);
        if ($("#table_tbody tr").length == 0) {
            $(".nominee_details_td").hide();
        }

        /*$.ajax({
          type: "POST",
          url: "/flexi_benifit/get_utilised_data",
          dataType: "json",
          async: false,
          success: function (response) {
            if (response.benifit_data.length != 0) {
              var topup_policy_str = "";
              $("#topup_policy_modal_body").html("");
              $.each(response.benifit_data, function (index, value) {
                if (
                  value.master_flexi_benefit_id == 3 ||
                  value.master_flexi_benefit_id == 4 ||
                  value.master_flexi_benefit_id == 5
                ) {
                  topup_policy_str += "<tr>";
                  topup_policy_str += "<td>" + value.flexi_benefit_name + "</td>";
                  topup_policy_str += "<td>" + value.sum_insured + "</td>";
                  topup_policy_str += "<td>" + value.final_amount + "</td>";
                  topup_policy_str += "</tr>";
                }
              });
              $("#topup_policy_modal_body").html(topup_policy_str);
            } else {
              $(".topup_policy_table").hide();
            }
          }
      });*/

        /*$.ajax({
          type: "POST",
          url: "/employee/get_base_policy_record",
          dataType: "json",
          async: false,
          success: function (response) {
            var flag1 = false;
            var policy_type = [];
            $.each(response, function (index, value) {
              if (value.employee_policy_mem_sum_premium != 0) {
                flag1 = true;
              }

              if (!policy_type.hasOwnProperty(value.policy_sub_type_id)) {
                policy_type[value.policy_sub_type_id] = [];
              }
              policy_type[value.policy_sub_type_id].push(value);
            });

            var content = "";
            $("#base_policy_modal_body").html("");

            policy_type = policy_type.filter(function (item) {
              return item !== undefined;
            });

            for (var i = 0; i < policy_type.length; i++) {
              var sum_insured = 0;
              var premium = 0;
              content += "<tr>";
              $.each(policy_type[i], function (k, v) {
                if (v.sum_insured_type != "familyGroup") {
                  sum_insured += parseInt(v.policy_mem_sum_insured);
                } else {
                  sum_insured = parseInt(v.policy_mem_sum_insured);
                }
                premium += parseInt(v.employee_policy_mem_sum_premium);
              });
              content += "<td>" + policy_type[i][0].policy_sub_type_name + "</td>";
              content += "<td>" + sum_insured + "</td>";
              content += "<td class = pre_tax>" + premium + "</td>";
              content += "</tr>";
            }
            $("#base_policy_modal_body").html(content);
            if (!flag1) {
              $(".pre_tax").hide();
              $(".premium_tax").hide();
            }
          }
      });*/
        $("#bal_modal").text($("#wallet_ut").text());
        /*$.ajax({
          type: "POST",
          url: "/employee/get_flex_active_record",
          dataType: "json",
          async: false,
          success: function (response1) {
            $("#wall_bal_modal").html(response1[0].flex_amount);
            $("#pay_bal_modal").html(response1[0].pay_amount);
          }
      });*/
    });
    $(".hide_ready").removeClass("hide");
});

function submit_member(deduction_type, flex_amount, pay_amount, final_amount) {

    var count = 0;

    if ($("#premium_type_text").val() == "parent") {
        var current_val = $("#gmc_parent_estimate").attr('data-value');
    } else {
        var current_val = $("input[name='gmc']:checked").attr("data-ids");


    }


    var policy_id = $("#policy_no").val();
    var deduction_type = $("input[name='parent_type']:checked").val();


    var sum_insured = $("#parent_si").text();
    var premium_text = $("#premium_type_text").val();

    if (!policy_id) {
        swal("Alert", "Employee not added in any Policy", "warning");
        return;
    }

    var family_members_id = document.getElementsByName("family_members_id");
    var first_name = document.getElementsByName("first_name");
    var last_name = document.getElementsByName("last_name");
    var family_date_birth = document.getElementsByName("family_date_birth");
    var family_gender = document.getElementsByName("family_gender");
    var family_id = document.getElementsByName("family_id");
    var marriage_date = document.getElementsByName("marriage_date");
    var s_date = document.getElementsByName("s_date");
    var upload_document = document.getElementsByName("upload_document");

    var disable_child = document.getElementsByName("disable_child");

    var age = document.getElementsByName("age");
    var age_type = document.getElementsByName("age_type");

    var policy_idArr = [];
    var family_members_idArr = [];
    var first_nameArr = [];
    var last_nameArr = [];
    var family_date_birthArr = [];
    var family_genderArr = [];
    var family_idArr = [];
    var marriage_dateArr = [];
    var s_dateArr = [];
    var disable_childArr = [];
    var upload_documentArr = [];

    var ageArr = [];
    var age_typeArr = [];

    var form = new FormData();
    for (i = 0; i < family_members_id.length; ++i) {
        if (first_name[i].nextElementSibling) {
            first_name[i].nextElementSibling.innerText = '';
        }
        if (family_members_id[i].nextElementSibling) {
            family_members_id[i].nextElementSibling.innerText = '';
        }
        if (last_name[i].nextElementSibling) {
            last_name[i].nextElementSibling.innerText = '';
        }
        if (family_date_birth[i].nextElementSibling) {
            family_date_birth[i].nextElementSibling.innerText = '';
        }
        if (family_gender[i].nextElementSibling) {
            family_gender[i].nextElementSibling.innerText = '';
        }

        policy_idArr.push(policy_id);
        family_members_idArr.push(family_members_id[i].value);
        first_nameArr.push(first_name[i].value);
        last_nameArr.push(last_name[i].value);
        family_date_birthArr.push(family_date_birth[i].value);
        family_genderArr.push(family_gender[i].value);
        family_idArr.push(family_id[i].value);
        marriage_dateArr.push(marriage_date[i].value);
        if (upload_document[i])
            form.append("files" + i, upload_document[i].files[0]);
        else
            form.append("files" + i, "");

        s_dateArr.push(s_date[i].value);
        var isCheckedValue = $("input[type='radio'][name='dis']:checked");

        if (isCheckedValue) {
            isCheckedValue = isCheckedValue.val();
        }


        disable_childArr.push(isCheckedValue);
        ageArr.push(age[i].value);
        age_typeArr.push(age_type[i].value);

        if (family_members_id[i].value.trim().length > 0) {
            family_members_id[i].style = "border-color:black";
        } else {
            family_members_id[i].style = "border-color:red";
            $(family_members_id[i]).after("<span style='color:red;'>Family member is required</span>");
            ++count;
        }
        if (first_name[i].value.trim().length > 0) {
            first_name[i].style = "border-color:black";
        } else {
            first_name[i].style = "border-color:red";
            $(first_name[i]).after("<span style='color:red;'>First name is required</span>");
            ++count;
        }
        if (last_name[i].value.trim().length > 0) {
            last_name[i].style = "border-color:black";
        } else {
            last_name[i].style = "border-color:red";
            $(last_name[i]).after("<span style='color:red;'>Last name is required</span>");
            ++count;
        }
        if (family_date_birth[i].value.trim().length > 0) {
            family_date_birth[i].style = "border-color:black";
        } else {
            family_date_birth[i].style = "border-color:red";
            $(family_date_birth[i]).after("<span style='color:red;'>Date of birth is required</span>");
            ++count;
        }
        if (family_gender[i].value.trim().length > 0) {
            family_gender[i].style = "border-color:black";
        } else {
            family_gender[i].style = "border-color:red";
            $(family_gender[i]).after("<span style='color:red;'>Gender is required</span>");
            ++count;
        }
    }

    if (count > 0) {
        return;
    }


    form.append("policy_idArr", JSON.stringify(policy_idArr));
    form.append("family_members_idArr", JSON.stringify(family_members_idArr));
    form.append("first_nameArr", JSON.stringify(first_nameArr));
    form.append("last_nameArr", JSON.stringify(last_nameArr));
    form.append("family_date_birthArr", JSON.stringify(family_date_birthArr));
    form.append("family_idArr", JSON.stringify(family_idArr));
    form.append("family_genderArr", JSON.stringify(family_genderArr));
    form.append("marriage_dateArr", JSON.stringify(marriage_dateArr));
    form.append("disable_childArr", JSON.stringify(disable_childArr));
    form.append("age_typeArr", JSON.stringify(age_typeArr));
    form.append("ageArr", JSON.stringify(ageArr));
    form.append("flex_amount", flex_amount);
    form.append("pay_amount", pay_amount);
    form.append("deduction_type", deduction_type);
    form.append("final_amount", final_amount);
    form.append("sum_insured", sum_insured);
    form.append("current_val", current_val);

    form.append("premium_text", premium_text);

    $.ajax({
        type: 'POST',
        url: "/employee/get_family_details",
        data: form,
        async: false,
        contentType: false,
        processData: false,
        success: function (response) {
            //console.log(data);
            try {
                var data1 = data.count;
                if (data1.status == "error") {
                    swal({
                        title: "Alert",
                        text: "Sorry You can not add more than 2 kids",
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
                        function () {
                            //location.reload();
                        });
                    return;
                }
                if (data1.status == "Same child") {
                    swal({
                        title: "Alert",
                        text: "Same Child Already added",
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
                        function () {
                            //location.reload();
                        });
                    return;
                }
                if (data1.status == "Adults") {
                    swal({
                        title: "Alert",
                        text: data1.message,
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
                        function () {
                            //location.reload();
                        });
                    return;
                }

                if (data1.status == "2 added") {
                    swal({
                        title: "Alert",
                        text: "You can not add more than 2 adults",
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
                        function () {
                            //location.reload();
                        });
                    return;
                }
                if (data1.status == "4 added") {
                    swal({
                        title: "Alert",
                        text: "You can not add more than 4 adults",
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
                        function () {
                            // location.reload();
                        });
                    return;
                }
                if (data1.status == "max_child_limit") {
                    swal({
                        title: "Alert",
                        text: "Maximum Child Limit Exceeded!",
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
                        function () { });
                    return;
                }
                if (data1.status == "age_difference_error") {
                    swal({
                        title: "Alert",
                        text: "Employee and family member age difference must be atleast 18 years",
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
                        function () { });
                    return;
                }
                if (data1.status == "incorrect Family") {
                    swal({
                        title: "Alert",
                        text: "Parents Cross Selection Not Allowed",
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
                        function () {
                            // location.reload();
                        });
                    return;
                }
            } catch (e) { }
            try {
                var data1 = data.count;
                if (data1.status == "Empty") {
                    swal({
                        title: "Alert",
                        text: "Please upload file",
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
                        function () {
                            //  location.reload();
                        });
                    return;
                }
            } catch (e) { }

            try {
                var data1 = data.count;
                var msg = data1.msg;
                if (msg.status == "error") {
                    swal({
                        title: "Alert",
                        text: "Sorry You can not add more",
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
                        function () {
                            //location.reload();
                        });
                    return;
                }
            } catch (e) {

            }

            try {
                if (data.status == "error") {
                    swal({
                        title: "Alert",
                        text: "Sorry You can not add more than 2 kids",
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
                        function () {
                            // location.reload();
                        });
                    return;
                }
            } catch (e) {

            }
            try {
                var errors = data.count;
                if (errors.status == "Enrollment") {
                    swal("", "Enrollment window closed");
                    return;
                }
            } catch (e) {

            }
            try {
                var data1 = data.count;
                var msg = data1.msg;
                if (msg.status == "error") {
                    swal({
                        title: "Alert",
                        text: "Sorry You can not add more",
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
                        function () {
                            // location.reload();
                        });
                } else if (data1[0] || data1.msg == true) {
                    swal({
                        title: "Success",
                        text: "Dependent Member Added Successfully",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true,
                        allowOutsideClick: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        dangerMode: true,
                        allowEscapeKey: false
                    },
                        function () {



                            location.reload();

                        });
                } else {
                    $.each(data.messages, function (key, value) {
                        var element = $('#' + key);
                        element.closest('div.data').find('.error').remove();
                        element.after(value);
                    });
                }
            } catch (e) {

            }
        }
    });

}

function getgender(e) {
    eValue = e.value;
    var family_gender = $(e)
        .closest(".row")
        .find("#family_gender");
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

function get_age_family(e, data = "") {

    var today = new Date();
    var dob = new Date(e);
    var z = e.split("-");
    var dob = new Date(z[2], z[1] - 1, z[0]);
    var today_mon = today.getMonth();
    var dob_mon = dob.getMonth();
    var dob_day = dob.getDate();
    var today_day = today.getDate();

    var age_distance = today.getFullYear() - dob.getFullYear();
    if (today_mon >= dob_mon && today_day >= dob_day) {
        if (age_distance > 0) {
            $("#age1").val(age_distance);
            $("#age_type1").val("years");
        } else {
            var month = "" + (today.getMonth() + 1);

            if (month.length < 2) {
                var strDate =
                    "0" + today.getDate() + "-" + "0" + month + "-" + today.getFullYear();
            }
            var strDate =
                today.getDate() + "-" + month + "-" + today.getFullYear();
            var date = strDate.split("-");
            var display_date = date[2] + "-" + date[1] + "-" + date[0];
            var date = e.split("-");
            var date_bday = date[2] + "-" + date[1] + "-" + date[0];
            var day = Date.parse(display_date) - Date.parse(date_bday);
            var days = Math.floor(day / (1000 * 60 * 60 * 24));
            $("#age1").val(days);
            $("#age_type1").val("days");
        }
    } else {
        if (age_distance > 1) {
            $("#age1").val(age_distance - 1);
            $("#age_type1").val("years");
            var age_distances = age_distance - 1;
        } else if (age_distance == 1) {
            var month = "" + (today.getMonth() + 1);

            if (month.length < 2) {
                var strDate =
                    "0" + today.getDate() + "-" + "0" + month + "-" + today.getFullYear();
            }
			var strDate =
                    today.getDate() + "-" + month + "-" + today.getFullYear();
            var date = strDate.split("-");
            var display_date = date[2] + "-" + date[1] + "-" + date[0];
            var date = e.split("-");
            var date_bday = date[2] + "-" + date[1] + "-" + date[0];
            var day = Date.parse(display_date) - Date.parse(date_bday);
            var days = Math.floor(day / (1000 * 60 * 60 * 24));
            $("#age1").val(days);
            $("#age_type1").val("days");
        } else {
            var month = "" + (today.getMonth() + 1);

            if (month.length < 2) {
                var strDate =
                    "0" + today.getDate() + "-" + "0" + month + "-" + today.getFullYear();
            }
            var strDate = today.getDate() + "-" + month + "-" + today.getFullYear();
            var date = strDate.split("-");
            var display_date = date[2] + "-" + date[1] + "-" + date[0];
            var date = e.split("-");
            var date_bday = date[2] + "-" + date[1] + "-" + date[0];
            var day = Date.parse(display_date) - Date.parse(date_bday);
            var days = Math.floor(day / (1000 * 60 * 60 * 24));

            $("#age1").val(days);
            $("#age_type1").val("days");
        }
    }

    /*$.ajax({
      url: "/employee/check_dis_unm_child",
      type: "POST",
      data:{emp_id: emp_id},
      dataType: "json",
      success: function(response) {
        var ages = $("#age").val();
        var age_types = $("#age_type").val();
        var dis = response.special_child_check;
        var unm = response.unmarried_child_check;
        

        if (
          dis == 1 &&
          unm == 1 &&
          ages >= 25 &&
          age_types == "years" &&
          (data == 2 || data == 3)
        ) {
          selectChange.find("div .disable").removeAttr("style", "display:none");
          selectChange.find("div .unmarried").removeAttr("style", "display:none");
          selectChange
            .find("div .unmarried")
            .find(".unmarried_child")
            .prop("checked", true);
          $("#disable_child_wrapper").removeAttr("style", "display:none");
        } else {
          selectChange.find("div .disable").attr("style", "display:none");
          selectChange.find("div .unmarried").attr("style", "display:none");
          selectChange
            .find("div .unmarried")
            .find(".unmarried_child")
            .prop("checked", false);
          $("#disable_child_wrapper").attr("style", "display:none");
        }
      }
  }); */
}

function get_dob() {
    var mdate = $("input[name='s_date']")
        .val()
        .split("-");
    startDate = new Date(
        Number(mdate[2]),
        Number(mdate[1]) - 1,
        Number(mdate[0])
    );
    startDate.setMonth(startDate.getMonth() + 9);
    return startDate;
}

function update_fun(ett) {
    var select = $("#" + ett)
        .closest("tr")
        .find("input")
        .removeAttr("readonly");
    $("#" + ett)
        .closest("tr")
        .find("input[name='age']")
        .attr("readonly", true);
    $("#" + ett)
        .closest("tr")
        .find("input[name='age']")
        .addClass("editable");
    $("#" + ett)
        .closest("tr")
        .find("input[name='age_types']")
        .attr("readonly", true);
    $("#" + ett)
        .closest("tr")
        .find("input[name='age_types']")
        .addClass("editable");
    $("#" + ett)
        .closest("tr")
        .find("input[name='relation_name']")
        .attr("readonly", true);
    $("#" + ett)
        .closest("tr")
        .find("input[name='relation_name']")
        .addClass("editable");
    $("#" + ett)
        .closest("tr")
        .find("input[name='bdate']")
        .attr("readonly", true);
    $("#" + ett)
        .closest("tr")
        .find("input[name='bdate']")
        .addClass("editable");
    $("#" + ett)
        .closest("tr")
        .find("input[name='gender']")
        .attr("readonly", true);
    $("#" + ett)
        .closest("tr")
        .find("input[name='gender']")
        .addClass("editable");
    $("#" + ett)
        .closest("tr")
        .find("input[name='policy_sum']")
        .attr("readonly", true);
    $("#" + ett)
        .closest("tr")
        .find("input[name='policy_sum']")
        .addClass("editable");
    $("#" + ett)
        .closest("tr")
        .find("input[name='policy_premium']")
        .attr("readonly", true);
    $("#" + ett)
        .closest("tr")
        .find("input[name='policy_premium']")
        .addClass("editable");
    $("#" + ett)
        .closest("tr")
        .find("input[name='fname']")
        .addClass("editable");
    $("#" + ett)
        .closest("tr")
        .find("input[name='lname']")
        .addClass("editable");
    $("#" + ett)
        .closest("tr")
        .find("input[type='file']")
        .attr("disabled", true);
    $("#" + ett)
        .closest("tr")
        .find("input[name='disable_c']")
        .attr("readonly", true);

    var member_id = $("#" + ett)
        .closest("tr")
        .find("input[name='member_id']")
        .val();
    // var member_id = $(this).attr('data_memid');

    // console.log(select);
    var btn_update = $("#" + ett)
        .closest("tr")
        .find("button[name='update_mem']")
        .removeAttr("hidden");
    var ntn_edit = $("#" + ett)
        .closest("tr")
        .find("button[name='edit_mem']")
        .attr("hidden", true);
    var dobs = $("#" + ett).closest("tr");

    $(dobs).on("focus", ".bdate_family", function (e) {
        var relation_id = $("#" + ett)
            .closest("tr")
            .find("input")[1].value;
        $(this).removeClass("hasDatepicker");
        var selectr = $("#" + ett)
            .closest("tr")
            .find("input")[1].value;
        // console.log(value.fr_id);
        if (selectr == 2) {
            $(this).datepicker({
                dateFormat: "dd-mm-yy",
                prevText: '<i class="fa fa-angle-left"></i>',
                nextText: '<i class="fa fa-angle-right"></i>',
                changeMonth: true,
                changeYear: true,
                yearRange: son_age.getYear() + ":",
                minDate: son_age,
                maxDate: new Date(),
                onSelect: function (dateText, inst) {
                    $(this).val(dateText);
                    get_age_family_type(dateText, relation_id, ett, member_id);
                }
            });
        }
        if (selectr == 3) {
            $(this).datepicker({
                dateFormat: "dd-mm-yy",
                prevText: '<i class="fa fa-angle-left"></i>',
                nextText: '<i class="fa fa-angle-right"></i>',
                changeMonth: true,
                changeYear: true,
                yearRange: son_age.getYear() + ":",
                minDate: son_age,
                maxDate: new Date(),
                onSelect: function (dateText, inst) {
                    $(this).val(dateText);
                    get_age_family_type(dateText, relation_id, ett, member_id);
                }
            });
        } else {
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
                    get_age_family_type(dateText, relation_id, ett, member_id);
                }
            });
        }
    });
}

function update_data(ett) {
    $(this).attr("data-mem-id");
    swal({
        title: "Are you sure?",
        text: "You want to update the data !",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes, Update it!",
        closeOnConfirm: false,
        allowOutsideClick: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
        dangerMode: true,
        allowEscapeKey: false
    },
        function () {
            var relation_name = $("#" + ett)
                .closest("tr")
                .find("input")[0].value;
            var relation_id = $("#" + ett)
                .closest("tr")
                .find("input")[1].value;
            var family_id = $("#" + ett)
                .closest("tr")
                .find("input")[2].value;
            var f_name = $("#" + ett)
                .closest("tr")
                .find("input")[3].value;
            var l_name = $("#" + ett)
                .closest("tr")
                .find("input")[4].value;
            var gender = $("#" + ett)
                .closest("tr")
                .find("input")[5].value;
            var dob_date = $("#" + ett)
                .closest("tr")
                .find("input")[6].value;
            var age = $("#" + ett)
                .closest("tr")
                .find("input")[7].value;
            var age_type = $("#" + ett)
                .closest("tr")
                .find("input")[8].value;
            var sum = $("#" + ett)
                .closest("tr")
                .find("input")[9].value;
            var premium = $("#" + ett)
                .closest("tr")
                .find("input")[10].value;
            var member_id = $("#" + ett)
                .closest("tr")
                .find("input")[14].value;
            var document_id = $("#" + ett)
                .closest("tr")
                .find("input")[12].value;
            var document_name = "";

            if (
                $("#" + ett)
                    .closest("tr")
                    .find("input")[13].files.length != 0
            )
                document_name = $("#" + ett)
                    .closest("tr")
                    .find("input")[13].files[0];

            var policy_detail_id = $("#" + ett)
                .closest("tr")
                .find("input")[15].value;
            var formData = new FormData();
            if (document_name) {
                formData.append("upload_document_id", document_name);
            } else {
                formData.append("upload_document_id", null);
            }
            formData.append("relation_name", relation_name);
            formData.append("relation_id", relation_id);
            formData.append("family_id", family_id);
            formData.append("f_name", f_name);
            formData.append("l_name", l_name);
            formData.append("gender", gender);
            formData.append("dob_date", dob_date);
            formData.append("age", age);
            formData.append("age_type", age_type);
            formData.append("sum", sum);
            formData.append("premium", premium);
            formData.append("member_id", member_id);
            formData.append("document_id", document_id);
            formData.append("policy_detail_id", policy_detail_id);

            $.ajax({
                url: "/employee/update_policy",
                data: formData,
                async: false,
                /* data:{relation_name: relation_name,
                             relation_id: relation_id,
                             f_name: f_name,
                             l_name: l_name,
                             gender:gender,
                             dob_date:dob_date,
                             age:age,
                             age_type:age_type,
                             sum:sum,
                             premium:premium,
                             family_id:family_id,
                             member_id:member_id,
                             family_relation_id:ett,
                             document_id:document_id,
                             document_name:document_name,
                         } */
                type: "POST",
                async: false,
                dataType: "json",
                mimetype: "multipart/form-data",
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status == "error") {
                        swal({
                            title: "Alert",
                            text: "You can not add more child",
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
                            function () {
                                $("#" + ett)
                                    .closest("tr")
                                    .find("button[name='edit_mem']")
                                    .removeAttr("hidden");
                                $("#" + ett)
                                    .closest("tr")
                                    .find("button[name='update_mem']")
                                    .attr("hidden", true);
                                location.reload();
                            }
                        );
                    } else if (response.status == "age_difference_error") {
                        swal({
                            title: "Alert",
                            text: "Employee and family member age difference must be atleast 18 years",
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
                            function () {
                                $("#" + ett)
                                    .closest("tr")
                                    .find("button[name='edit_mem']")
                                    .removeAttr("hidden");
                                $("#" + ett)
                                    .closest("tr")
                                    .find("button[name='update_mem']")
                                    .attr("hidden", true);
                                location.reload();
                            }
                        );
                        return;
                    } else if (response.status == "successfully updated") {
                        swal({
                            title: "Success",
                            text: "Data updated successfully",
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
                            function () {
                                $("#" + ett)
                                    .closest("tr")
                                    .find("button[name='edit_mem']")
                                    .removeAttr("hidden");
                                $("#" + ett)
                                    .closest("tr")
                                    .find("button[name='update_mem']")
                                    .attr("hidden", true);
                                location.reload();
                            }
                        );
                        return;
                    }
                }
            });
            swal("Ok !", "Go ahead.", "success");
        }
    );
}

function delete_data(mem_id) {
    var policy_id = $("input[name=delete_mem]").attr("data-id");

    swal({
        title: "Are you sure?",
        text: "Are you sure You Want to Delete!",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes, delete it!",
        closeOnConfirm: false,
        allowOutsideClick: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
        dangerMode: true,
        allowEscapeKey: false
    },
        function () {
            $.ajax({
                url: "/employee/delete_policy",
                data: {
                    member_id: mem_id,
                    policy_id: policy_id
                },
                type: "POST",
                async: false,
                dataType: "json",
                success: function (response) {
                    if (response == true) {
                        location.reload();
                    }
                }
            });
        }
    );
}

function get_age_family_type(e, data = "", ett, member_id) {
    // console.log($("#"+ett).closest('tr').find("input")[7]);
    var today = new Date();
    var dob = new Date(e);
    var z = e.split("-");
    var dob = new Date(z[2], z[1] - 1, z[0]);
    var today_mon = today.getMonth();
    var dob_mon = dob.getMonth();
    var dob_day = dob.getDate();
    var today_day = today.getDate();

    var age_distance = today.getFullYear() - dob.getFullYear();
    if (today_mon >= dob_mon && today_day >= dob_day) {
        if (age_distance > 0) {
            $("#" + ett)
                .closest("tr")
                .find("input")[7].value = age_distance;
            $("#" + ett)
                .closest("tr")
                .find("input")[8].value = "years";
        } else {
            var month = "" + (today.getMonth() + 1);

            if (month.length < 2) {
                var strDate = '0' + today.getDate() + "-" + '0' + month + "-" + today.getFullYear();
            }

            var date = strDate.split("-");
            var display_date = date[2] + "-" + date[1] + "-" + date[0];
            var date = e.split("-");
            var date_bday = date[2] + "-" + date[1] + "-" + date[0];
            var day = Date.parse(display_date) - Date.parse(date_bday);
            var days = Math.floor(day / (1000 * 60 * 60 * 24));
            $("#" + ett)
                .closest("tr")
                .find("input")[7].value = days;
            $("#" + ett)
                .closest("tr")
                .find("input")[8].value = "days";
        }
    } else {
        if (age_distance > 1) {
            $("#" + ett)
                .closest("tr")
                .find("input")[7].value = age_distance - 1;
            $("#" + ett)
                .closest("tr")
                .find("input")[8].value = "years";
        } else if (age_distance == 1) {
            var month = "" + (today.getMonth() + 1);

            if (month.length < 2) {
                var strDate =
                    "0" + today.getDate() + "-" + "0" + month + "-" + today.getFullYear();
            }
            var date = strDate.split("-");
            var display_date = date[2] + "-" + date[1] + "-" + date[0];
            var date = e.split("-");
            var date_bday = date[2] + "-" + date[1] + "-" + date[0];
            var day = Date.parse(display_date) - Date.parse(date_bday);
            var days = Math.floor(day / (1000 * 60 * 60 * 24));
            $("#" + ett)
                .closest("tr")
                .find("input")[7].value = days;
            $("#" + ett)
                .closest("tr")
                .find("input")[8].value = "days";
        } else {
            var month = "" + (today.getMonth() + 1);

            if (month.length < 2) {
                var strDate =
                    "0" + today.getDate() + "-" + "0" + month + "-" + today.getFullYear();
            }
            var date = strDate.split("-");
            var display_date = date[2] + "-" + date[1] + "-" + date[0];
            var date = e.split("-");
            var date_bday = date[2] + "-" + date[1] + "-" + date[0];
            var day = Date.parse(display_date) - Date.parse(date_bday);
            var days = Math.floor(day / (1000 * 60 * 60 * 24));
            $("#" + ett)
                .closest("tr")
                .find("input")[7].value = days;
            $("#" + ett)
                .closest("tr")
                .find("input")[8].value = "days";
        }
    }

    /*$.ajax({
      url: "/employee/check_dis_unm_child",
      type: "POST",
      data: {emp_id:emp_id},
      dataType: "json",
      async: false,
      success: function(response) {
        //console.log(response);
        var dis = response.special_child_check;
        var unm = response.unmarried_child_check;

        if (
          dis == 1 &&
          unm == 1 &&
          age_distance >= 25 &&
          (data == 2 || data == 3)
        ) {
          $("#targetRow").val(ett);
          $("#re_id").val(data);
          $("#age_no").val(age_distance);
          $("#mem_id").val(member_id);
          $("#upload_modal").get(0).files = null;
          $("#MYModal").modal("show");
        } else {
          $("#targetRow").val(ett);
          $("#MYModal").modal("hide");
        }
      }
  });*/
}

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(input)
                .next()
                .attr("src", e.target.result);
            $(input)
                .next()
                .css("display", "block");
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function getPremiumCalc(sumValue) {
    $.post(
        "/flexi_benefit/getGtliTopUpcalc", {
        sumValue: sumValue
    },
        function (e) {
            // $("#glta_estimate").html('<i class="fa fa-inr"></i>'+ e.toLocaleString('en')+ '</span>');
            // $("#glta_estimate").attr('data-value', e.toLocaleString('en'));
            //$("#glta_total_premium").text(e);
            $("#gtli_estimate").html("<i class='fa fa-inr'></i> " + e);
            $("#gtli_estimate").attr("data-value", e);
            $("#gtli_total_premium").text(e);
        }
    );
}

$("#btn_add").click(function () {
    var newRow = $("<tr class='mt-2'>");
    var cols = "";

    cols += '<td><input type="text" placeholder="Ops Type" class="form-control mt-3 ml-2" name="ops_type"></td><td><input type="file" name="ops_path" style="margin-left: 20px;margin-top: 18px;" class="padding-left: 48px; padding-top: 17px;"></td>';
    cols += '<td><input type="button" name="del_btn" class="form-control del_btn" value="Delete" style="border: 2px solid red; background: #fff;padding: 3px 12px;border-radius: 50px;font-weight: 600;"></td>';
    newRow.append(cols);
    $("#add_tbody").append(newRow);
});

$("body").on('click', ".del_btn", function () {
    $(this).parent().parent().remove();
});


$("#submitDoc").on("click", function () {
    // debugger;
    var count1 = saveDocuments();

    if (count1 == 0) {
        saveDocumentsAfterValidate();
    }
    else{
        ajaxindicatorstop();
    }
});


function saveDocuments() {
    // ajaxindicatorstop();
      var ops_type = document.getElementsByName("ops_type");
      var ops_path = document.getElementsByName("ops_path");
  
      var ops_type_value = [];
      var ops_path_value = [];
  
      var count = 0;
  
  
      if (ops_type.length > 0) {
          for (var i = 0; i < ops_path.length; ++i) {
              
              if(ops_path[i].classList.contains("ignore"))
              {
                 
                  continue;
              }
              if (ops_type[i].value.trim().length == 0 || ops_path[i].files.length == 0) {
                  ++count;
                  if (ops_path[i].nextElementSibling && ops_path[i].nextElementSibling.tagName.toLowerCase() == "span")
                      ops_path[i].nextElementSibling.remove();
  
                  var spanTag = document.createElement("span");
                  spanTag.style.color = "red";
                  spanTag.innerHTML = "Field is Required";
                  ops_path[i].parentNode.insertBefore(spanTag, ops_type[0].nextElementSibling);
              } else {
                  ops_type_value.push(ops_type[i].value.trim());
                  ops_path_value.push(ops_path[i].files[0]);
              }
          }
      }
  
     
      return count;
  
  }

function saveDocumentsAfterValidate() {
    var ops_type = document.getElementsByName("ops_type");
    var ops_path = document.getElementsByName("ops_path");

    var ops_type_value = [];
    var ops_path_value = [];

    var data = new FormData();

    // data.append("qcComplianceApprovedFile", qcComplianceApprovedFile[0]);
    if (ops_type.length > 0) {
        for (var i = 0; i < ops_path.length; ++i) {
			if(ops_path[i].classList.contains("ignore"))
              {
                 
                  continue;
              }
            if (ops_type[i].value.trim().length == 0 || ops_path[i].files.length == 0) {
                // ++count;
                // if (ops_path[i].nextElementSibling && ops_path[i].nextElementSibling.tagName.toLowerCase() == "span")
                // ops_path[i].nextElementSibling.remove();

                // var spanTag = document.createElement("span");
                // spanTag.style.color = "red";
                // spanTag.innerHTML = "Field is Required";
                // ops_path[i].parentNode.insertBefore(spanTag, ops_type[0].nextElementSibling);
            } else {
                ops_type_value.push(ops_type[i].value.trim());
                ops_path_value.push(ops_path[i].files[0]);
            }
        }
    }

    data.append("ops_type", JSON.stringify(ops_type_value));
    for (i = 0; i < ops_type_value.length; ++i) {
        data.append(i, ops_path_value[i]);
    }

    data.append("ids", ids);

    $.ajax({
        type: "POST",
        url: "/upload_new",
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        mimeType: "multipart/form-data",
        success: function (data) {
            
            // debugger;
            if (data == 0) {
                setTimeout(function () {
                    ajaxindicatorstop();
                    swal("Alert", "Please check documents uploaded, Supported documents PDF, PNG, JPEG & File Size should be less than 2MB");
                }, 500);
                return;
            }else {
				saveBankDet();
			}
            // setTimeout(function() {
            //     swal({
            //             title: "Success",
            //             text: "Your Proposal Submitted successfully",
            //             type: "success",
            //             showCancelButton: false,
            //             confirmButtonColor: "#C7222A;",
            //             confirmButtonText: "ok",
            //             closeOnConfirm: false,
            //             closeOnCancel: false
            //         },
            //         function() {
            //$("#bankDetForm").submit();
            // $.ajax({
                // url: "/coi_generate",
                // type: "POST",
                // async: false,
                // dataType: "json",
                // data: {
                    // emp_id: emp_id,
                    // parent_id: parent_id,
                // },
                // success: function (response) {

                // }
            // });


            // location.reload();
            // });
            // }, 500);


        }
    });
}
// $("#masPolicy").click(function (e) {
//   e.preventDefault();
//   var policy_detail = $(this).val();

//   if (policy_detail != '') {
//     $.ajax({


//       url: "/get_declaration",
//       type: "POST",
//       data: { 'policy_id': policy_detail },
//       async: true,
//       success: function (data) {


//         $('#policy_declare').html(data);



//       }
//     });
//   }


// }); 

$("#bankDetForm").validate({
	
    ignore: ".ignore",
    rules: {
        bankName: {
            required: true
        },
		ifscCode: {
            required: true
        },
         branchName: {
            required: true
         },
        bankAcNo: {
            required: true
        },
        cheque_no: {
          required: true
        },
        cheque_date: {
          required: true
        },
		micr_no: {
          required: true
        },
		
    },
    messages: {

    },
    submitHandler: function (form) {
        // debugger;
        
    }
});

function saveBankDet() {
	var data = $("#bankDetForm").serialize() + "&ids=" + ids;
        $.post("/bank_save_details", data, function (e) {
            e = JSON.parse(e);
            if (e.status == true) {
                // $("#documentDiv").show();

                // var policy_no = $('#policy_no').val();

                // $.ajax({
                // type: "POST",
                // url: "/api_request",
                // data: {emp_id:emp_id,parent_id:parent_id,policy_no:policy_no},
                // dataType: "json",
                // success: function(result) {
                // console.log(result);
                // }
                // });

                 ajaxindicatorstop();

                swal({
                    title: "Success",
                    text: "Proposal Submitted Successfully",
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
                    function () {

                        get_employee_lead_id();
                    });

            }
            else{
                ajaxindicatorstop();
            }
        })
}

function get_employee_lead_id() {
	
    $("#emp_idDummyForm").val(emp_id);
    $("#dummyForm").submit();
    /* $.ajax({
                           url: "/employee/get_employee_lead_id",
                           type: "POST",
                           async: false,
                         
                           data: {
                               emp_id: emp_id,
                              
                           },
                           success: function(response) {
                               debugger;
                               $('body').html(response);
                           }
                       }); */
}

function editPopulate(e1) {
    // debugger;
    var emp_edit_id = $(e1).attr("data-emp-id");
    var emp_edit_policy_mem = $(e1).attr("data-policy-member-id");
    $.post("/get_edit_member", {
        "emp_id": $(e1).attr("data-emp-id"),
        "policy_member_id": $(e1).attr("data-policy-member-id"),
    }, function (e) {
        e = JSON.parse(e);
        // console.log(e);
        var tableid = $(e1).attr("data-tableid");
        // debugger;
        var sum_insure_select = $(e1).closest("form").find("select[name='sum_insure']")
        var family_members_id = $(e1).closest("form").find("select[name='family_members_id']")
        var family_gender = $(e1).closest("form").find("select[name='family_gender']")
        var familyConstruct = $(e1).closest("form").find("select[name='familyConstruct']")
        var first_name = $(e1).closest("form").find("input[name='first_name']")
        var last_name = $(e1).closest("form").find("input[name='last_name']")
        var family_date_birth = $(e1).closest("form").find("input[name='family_date_birth']")
        var age = $(e1).closest("form").find("input[name='age']")
        var age_type = $(e1).closest("form").find("input[name='age_type']")
        var edit = $(e1).closest("form").find("input[name='edit']")
first_name.focus();
        edit.val($(e1).attr("data-policy-member-id"));
        // sum_insure_select.css("pointer-events", "auto");
        sum_insure_select.val(e.policy_mem_sum_insured);
        family_members_id.val(e.fr_id);
		family_members_id.css("pointer-events",'none');
        family_gender.html("<option selected='selected' value='"+e.policy_mem_gender+"' >"+e.policy_mem_gender+"</option>");
        first_name.val(e.policy_member_first_name);
        last_name.val(e.policy_member_last_name);
        family_date_birth.val(e.policy_mem_dob);
        age.val(e.age);
        age_type.val(e.age_type);
        sum_insure_select.change();
        // $("#patFamilyConstruct").change();
        $("#vtlFamilyConstruct").change();
        declarepopoulate(emp_edit_id, emp_edit_policy_mem);
        // familyConstruct.val(e.familyConstruct);
        setFamilyConstruct(e.familyConstruct);
        familyConstruct.css("pointer-events", "none");
        


    });
}

function delPopulate(e1) {
    var edit = $(e1).closest("form").find("input[name='edit']");
    console.log("edit", edit);
    edit.val(0);
    $.post("/delete_member_new", {
        "emp_id": $(e1).attr("data-emp-id"),
        "policy_member_id": $(e1).attr("data-policy-member-id"),
    }, function (e) {
        
        if (!e) {
			$(e1).closest("form").find("select[name='family_members_id']").css("pointer-events",'auto')
            swal("Success", "Member Deleted Successfully", "success");
            $(e1).closest("tr").remove();
			
$(".quest_declare_benifit_4s").empty();


    $('#chronic th input[type="checkbox"]').prop('checked',false);




            if($("#patTable tr").length == 0) {
                $("#sum_insures").css("pointer-events", "auto");
                $("#patFamilyConstruct").css("pointer-events", "auto");
            }
        }
    });
}

function deleteThis(e1) {

    var id1 = $(e1).attr("data-id");

    $.post("/delete_doc", {
        "id": id1
    }, function (e) {
        $(e1).closest("tr").remove();
    });
}

$('#bankName').on('change', function () {
    $('#ifscCode').val("");
    $("#bankCity").val("");
    $("#bankBranch").val("");


    getBankCity(this.value);
});

$("#bankCity").on("change", function (e) {
    getBankBranch(this.value);
});



function getBankCity(bank_name, bank_name_value) {
    $.post("/getBankCity", {
        "bank_name": bank_name
    }, function (e) {
        var obj = JSON.parse(e);
        $('#bankCity').empty();
        $('#bankCity').append('<option value="" disabled selected>Select City</option>');
        for (i = 0; i < obj.length; ++i) {
            $("#bankCity").append("<option value='" + obj[i].bank_city + "'>" + obj[i].bank_city + "</option>");
        }

        if (bank_name_value)
            $("#bankCity").val(bank_name_value);
    });
}

$("#bank_city").on("change", function (e) {
    getBankBranch(this.value);
});

function getBankBranch(bank_city, bank_city_value) {

    $.post("/getBankBranch", {
        "bank_name": $('#bankName').val(),
        "bank_city": bank_city,
    }, function (e) {
        var obj = JSON.parse(e);
        $('#bankBranch').empty();
        $('#bankBranch').append('<option value="" disabled selected>Select Branch</option>');

        for (i = 0; i < obj.length; ++i) {
            $("#bankBranch").append("<option data-ifsc='" + obj[i].ifsc_code + "' value='" + obj[i].bank_branch + "'>" + obj[i].bank_branch + "</option>");
        }

        if (bank_city_value)
            $("#bankBranch").val(bank_city_value);
    });
}

$('#bankBranch').on('change', function () {
    $('#ifscCode').val(this.selectedOptions[0].getAttribute("data-ifsc"));
});

$('#ifscCode').on('keyup', function () {
    if (this.value.length == 11) {
        //set all value by ifsc code
        getIfscCode(this.value);
    }
});

function getIfscCode(code) {
    $.post('/getIfscCode', {
        'ifsc_code': code
    }, function (e) {

        var obj = JSON.parse(e);
        console.log($('#bankName'));

        $('#bankName').val(obj.bank_name);
        //populate city by bank name
        getBankCity(obj.bank_name, obj.bank_city);
        //populate branch by city
        getBankBranch(obj.bank_city, obj.bank_branch);

        $('#ifscCode').val(obj.ifsc_code);
    });
}

if (emp_id && parent_id) {

    $('#edit_btn').show();
    $('#add_btn').hide();

    $("#bankdiv").show();
    $("#documentDiv").show();

    get_nominee_data();
    get_profile_data();
    get_all_policy_data();

    $('#update_declare_id').val(parent_id);

}
$(document).ready(function () {



    $.ajax({
        url: "/employee/get_member_declare_data",
        type: "POST",
        dataType: "html",
        data: { "parent_id": parent_id, "emp_id": emp_id },
        success: function (response) {
            $(".member_declare_benifit_4s").html(response)
            // console.log(response);
        }
    });



});




   $(document).on("click","input:checkbox[name='sub_type_check[]']",function() {

	   var sub_type_id = $(this).val();
		 if($(this).prop("checked") == true){
		
	   $.ajax({
        url: "/employee/member_declaration_question",
        type: "POST",
        dataType: "html",
        data: { "parent_id": parent_id, "emp_id": emp_id,'sub_type_id':sub_type_id},
        success: function (response) {
		
			//$("div .quest_declare_benifit_4s").setAttribute("id", "sub_type_id");
             $(".quest_declare_benifit_4s").append('<div id =di'+sub_type_id+'>'+response+'</div>');
        
        }
    });
   }
   else{
	
$("#di"+sub_type_id).remove();
   }
  
    });


function get_fam_data(elem) {
    // debugger;
    $.ajax({
        url: "/home/get_family_details_from_relationship",
        type: "POST",
        data: {
            relation_id: elem.value,
            emp_id: emp_id
        },
        async: false,
        dataType: "json",
        success: function (response) {
            // debugger;

            $("#nominee_fname").val("");
            $("#nominee_lname").val("");
			$('#nominee_contact').val('');
            $("input[type='text'][name='nominee_dob']").val("");

            var family_detail = response.family_data;

            if (family_detail.length != 0) {


                if (family_detail[0].fr_id == "2" || family_detail[0].fr_id == "3") {

                    $("#body_modal").html("");
                    for ($i = 0; $i < family_detail.length; $i++) {
                        $("#body_modal").append(
                            '<input type="radio" name ="radio_option" value= ' +
                            family_detail[$i]["policy_member_id"] +
                            "> " +
                            family_detail[$i].policy_member_first_name +
                            "<br>"
                        );
                    }

                    $("#myModal").modal();

                } else if (family_detail[0].fr_id == "0") {

                    $("#nominee_fname").val(family_detail[0].policy_member_first_name);
                    $("#nominee_lname").val(family_detail[0].policy_member_last_name);
                    //$("input[type='text'][name='nominee_dob']").val(family_detail[0].family_dob);

                } else {

                    $("#nominee_fname").val(family_detail[0].policy_member_first_name);
                    $("#nominee_lname").val(family_detail[0].policy_member_last_name);
                    //$("input[type='text'][name='nominee_dob']").val(family_detail[0].family_dob);
                }

            }
        }
    });
}

function submitNominee() {
	var family_members_idArray = {};
	var total = 0;
	var status = true;
	ajaxindicatorstart("Please wait...");
	$('#policy_nominee_form_data .form_row_data1').each(function (key, value) {

            total += Number($(this).find('.share_per').val());
            if ($(this).find('.nominee_relation').val() == '') {
                if($('.test').length == 0){
                    ajaxindicatorstop();
                    ($(this).find('.nominee_relation')).after("<span style='color:red;' class='test'>family member is required</span>");
                }
                
                //location.reload();
                status = false;
            }

            if ($(this).find('.nominee_fname').val() == '') {
                // alert('nominee_fname');
                if($('.test1').length == 0){
                    ajaxindicatorstop();
                ($(this).find('.nominee_fname')).after("<span style='color:red;' class='test1'>First name is required</span>");
                }
                //location.reload();
                status = false;
            }

            if ($(this).find('.nominee_lname').val() == '') {
                if($('.test2').length == 0){
                    ajaxindicatorstop();
                ($(this).find('.nominee_lname')).after("<span style='color:red;' class='test2'>Last name is required</span>");
                }
                //location.reload();
                status = false;
            }

            // if ($(this).find('.nominee_dob').val() == '') {
            // ($(this).find('.nominee_dob')).after("<span style='color:red;'>D.O.B is required</span>");
            // location.reload();
            // status = false;
            // }
            if ($(this).find('.nominee_contact').val() == '') {
                if($('.test3').length == 0){
                    ajaxindicatorstop();
                ($(this).find('.nominee_contact')).after("<span style='color:red;' class='test3'>Contact No is required</span>");
                
                }
                //location.reload();
                status = false;
            }

            if ($(this).find('.share_per').val() == '') {
                if($('.test4').length == 0){
                    ajaxindicatorstop();
                ($(this).find('.share_per')).after("<span style='color:red;' class='test4'>Share is required</span>");
                }
                //location.reload();
                status = false;
            }

            // if ($(this).find('.guardian_relation').val() == '') 
            // {
            //   ($(this).find('.guardian_relation')).after("<span style='color:red;'>family member is required</span>");
            // }

            // if ($(this).find('.guardian_fname').val() == '') 
            // {
            //   ($(this).find('.guardian_fname')).after("<span style='color:red;'>First name is required</span>");
            // }

            // if ($(this).find('.guardian_lname').val() == '') 
            // {
            //   ($(this).find('.guardian_lname')).after("<span style='color:red;'>Last name is required</span>");
            // }

            // if ($(this).find('.guardian_dob').val() == '') 
            // {
            //   ($(this).find('.guardian_dob')).after("<span style='color:red;'>D.O.B is required</span>");
            // }
            else {
                family_members_idArray["form" + key] = {
                    "family_members_idArr": $(this).find('.nominee_relation').val(),
                    "first_nameArr": $(this).find('.nominee_fname').val(),
                    "last_nameArr": $(this).find('.nominee_lname').val(),
                    "family_date_birthArr": $(this).find('.nominee_dob').val(),
                    "nominee_contact": $(this).find('.nominee_contact').val(),
                    "share_perArr": $(this).find('.share_per').val(),
                    "guardian_relationArr": $(this).find('.guardian_relation').val(),
                    "guardian_fnameArr": $(this).find('.guardian_fname').val(),
                    "guardian_lnameArr": $(this).find('.guardian_lname').val(),
                    "guardian_dobArr": $(this).find('.guardian_dob').val()
                };
            }
        })
	
	$.ajax({
                url: "/employee/add_nominee",
                type: "post",
                data: { family_members_idArray: family_members_idArray, "emp_id": emp_id },
                dataType: "json",
                success: function (response) {
                    // console.log(response);
                    // return false;

                    if (response.msg == true) {


                        var update_declare_id = $('#update_declare_id').val();
                        var TableData = [];

                        var LabelData = []; //          TableData = [];
					
                        $('#mydatas tr').each(function (row, tr) {
                            var LabelData = {};
                            //TableData[row] = {};
                            var label_content;
                            var tds = $(tr).find('td:eq(0)').text();


                            //alert($(tr).find('input[type="radio"]:checked').val());
                            var label = $(tr).find('.label_id').text();
                            var content = $(tr).find('.mycontent').val();

                            var mylabel = $(tr).find('.mycontents').val();

                            var mylabval = $(tr).find('.mylabval').val();

                            if (label != '') {
                                LabelData = mylabel + ':' + mylabval;
                            } else {
                                LabelData = mylabval;
                            }


                            TableData[row] = {
                                "question": content,
                                "format": $("#mydatas input[type='radio']:checked").val(),
                                "label": LabelData
                            }
                            // $.ajax({
                            // url: "/check_policy_combo",
                            // type: "POST",
                            // async: false,
                            // dataType: "json",
                            // data: {
                            // "policy_no": $("#masPolicy option:selected").text(),
                            // "emp_id": emp_id,
                            // "family_construct": $("#patFamilyConstruct :selected").val()
                            // "declare": TableData,
                            // "update_declare_id": update_declare_id
                            // "declare": TableData,

                            // },
                            // success: function (response) {
                            // }
                            // });



                        });

                        $.ajax({
                            url: "/aprove_status",
                            type: "POST",
                            async: false,
                            dataType: "json",
                            data: {
                                "policy_no": $("#masPolicy option:selected").text(),
                                "emp_id": emp_id,
                                "declare": TableData,
                                "update_declare_id": update_declare_id,
                                "family_construct": $("#patFamilyConstruct :selected").val()
                                // "declare": TableData,

                            },
                            success: function (response) {
                                // debugger;



                                if (response.status == false) {

                                     ajaxindicatorstop();
                                    swal({
                                        title: "Alert",
                                        text: response.message,
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
                                        function () {
                                            //location.reload();
                                        }); return;
                                }


                                if (response.status == true) {

                                    ids = response.proposal_ids;


                                    // var ISNRI = $("#ISNRI").val();
									// var salutation_val = $("#salutation option:selected").val() 
									// var salutation =$("#salutation option:selected").text();
									// var gender = $('#gender1').val();
                                    // if (ISNRI == 'Y' || salutation_val!='' ) {

                                        // var all_data = $("#emp_form_old").serialize()+'&update_emp_id='+emp_id+'&salutation='+salutation+'&gender1='+gender;
                                        // $.ajax({
                                            // type: "POST",
                                            // url: "/employee/save_emp_data",
                                            // data: all_data,
                                            // dataType: "json",
                                            // success: function (result) {
                                                
                                            // }
                                        // });
                                    // }


                                    $('#submitDoc').trigger("click");
                                    






                                }
                            }
                        });

                        //alert(1);
                        // swal({
                        //     title: "Success",
                        //     text: "Submitted Successfully",
                        //     type: "warning",
                        //     showCancelButton: false,
                        //     confirmButtonText: "Ok!",
                        //     closeOnConfirm: true,
                        //     allowOutsideClick: false,
                        //     closeOnClickOutside: false,
                        //     closeOnEsc: false,
                        //     dangerMode: true,
                        //     allowEscapeKey: false
                        // },
                        //         function ()
                        //         {
                        $("#finalConfirmDiv").show();

                        get_nominee_data();
                        // location.reload();
                        // });
                    } else if (response.status == 'error') {
                        swal({
                            title: "Warning",
                            text: "Please Fill All Nominee And Guardian Details",
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
                            function () {
                                // location.reload();
                            });
                    } else if (response.status == 'error1') {
                        swal({
                            title: "Warning",
                            text: "You cannot add nominee until employee is not enrolled",
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
                            function () {
                                // location.reload();
                            });
                    } else if (response.status == 'error2') {
                        swal({
                            title: "Warning",
                            text: "You cannot add 0 share percentage",
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
                            function () {
                                //                                     location.reload();
                            });
                    } else {
                        swal({
                            title: "Warning",
                            text: "Nominees Share % Cannot Exceed 100%",
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
                            function () {
                                // location.reload();
                            });
                    }
                }
            });
}

$('#submit_nominee').on("click", function () {
    // debugger;

    if(!$("#bankDetForm").valid()) {
		ajaxindicatorstop();
		return;
	}
	
	
	
    var family_members_idArray = {};
    var total = 0;
    var status = true;
    $('#policy_nominee_form_data .form_row_data1').each(function (key, value) {
        if (Number($(this).find('.share_per').val()) == 0) {
            status = false;
        }
    });

    if (!status) {
        // ajaxindicatorstop();
        swal("", "Share percentage can not be zero");

        return false;
    } else {
       

        status = true;
        $('#policy_nominee_form_data .form_row_data1').each(function (key, value) {

            total += Number($(this).find('.share_per').val());
            if ($(this).find('.nominee_relation').val() == '') {
                if($('.test').length == 0){
                    ajaxindicatorstop();
                    ($(this).find('.nominee_relation')).after("<span style='color:red;' class='test'>family member is required</span>");
                }
                
                //location.reload();
                status = false;
            }

            if ($(this).find('.nominee_fname').val() == '') {
                // alert('nominee_fname');
                if($('.test1').length == 0){
                    ajaxindicatorstop();
                ($(this).find('.nominee_fname')).after("<span style='color:red;' class='test1'>First name is required</span>");
                }
                //location.reload();
                status = false;
            }

            if ($(this).find('.nominee_lname').val() == '') {
                if($('.test2').length == 0){
                    ajaxindicatorstop();
                ($(this).find('.nominee_lname')).after("<span style='color:red;' class='test2'>Last name is required</span>");
                }
                //location.reload();
                status = false;
            }

            // if ($(this).find('.nominee_dob').val() == '') {
            // ($(this).find('.nominee_dob')).after("<span style='color:red;'>D.O.B is required</span>");
            // location.reload();
            // status = false;
            // }
            if ($(this).find('.nominee_contact').val() == '') {
                if($('.test3').length == 0){
                    ajaxindicatorstop();
                ($(this).find('.nominee_contact')).after("<span style='color:red;' class='test3'>Contact No is required</span>");
                
                }
                //location.reload();
                status = false;
            }

            if ($(this).find('.share_per').val() == '') {
                if($('.test4').length == 0){
                    ajaxindicatorstop();
                ($(this).find('.share_per')).after("<span style='color:red;' class='test4'>Share is required</span>");
                }
                //location.reload();
                status = false;
            }

            // if ($(this).find('.guardian_relation').val() == '') 
            // {
            //   ($(this).find('.guardian_relation')).after("<span style='color:red;'>family member is required</span>");
            // }

            // if ($(this).find('.guardian_fname').val() == '') 
            // {
            //   ($(this).find('.guardian_fname')).after("<span style='color:red;'>First name is required</span>");
            // }

            // if ($(this).find('.guardian_lname').val() == '') 
            // {
            //   ($(this).find('.guardian_lname')).after("<span style='color:red;'>Last name is required</span>");
            // }

            // if ($(this).find('.guardian_dob').val() == '') 
            // {
            //   ($(this).find('.guardian_dob')).after("<span style='color:red;'>D.O.B is required</span>");
            // }
            else {
                family_members_idArray["form" + key] = {
                    "family_members_idArr": $(this).find('.nominee_relation').val(),
                    "first_nameArr": $(this).find('.nominee_fname').val(),
                    "last_nameArr": $(this).find('.nominee_lname').val(),
                    "family_date_birthArr": $(this).find('.nominee_dob').val(),
                    "nominee_contact": $(this).find('.nominee_contact').val(),
                    "share_perArr": $(this).find('.share_per').val(),
                    "guardian_relationArr": $(this).find('.guardian_relation').val(),
                    "guardian_fnameArr": $(this).find('.guardian_fname').val(),
                    "guardian_lnameArr": $(this).find('.guardian_lname').val(),
                    "guardian_dobArr": $(this).find('.guardian_dob').val()
                };
            }
        });

        // if(!status)
        var count1 = saveDocuments();
        
        if (count1 == 0) {

        } else {
            ajaxindicatorstop();
            swal("Alert", "Please add documents", 'warning');
            return;
        }

        if (status) {
			
			otp_generate();
            
            // $.ajax({
            //     url: "/employee/get_share_per_nominee",
            //     type: "POST",
            //     dataType: "json",
            //     data: { "emp_id": emp_id },
            //     success: function (response1) {
            //         if (response1 != 0) {
            //             var balance = parseInt(100 - response1);
            //             if (balance == 0 && total == 0) {
            //                 swal({
            //                     title: "Alert",
            //                     text: "Nominees Share % Cannot Exceed 100%",
            //                     type: "warning",
            //                     showCancelButton: false,
            //                     confirmButtonText: "Ok!",
            //                     closeOnConfirm: true,
            //                     allowOutsideClick: false,
            //                     closeOnClickOutside: false,
            //                     closeOnEsc: false,
            //                     dangerMode: true,
            //                     allowEscapeKey: false
            //                 },
            //                     function () {
            //                         // location.reload();
            //                     });
            //                 return;
            //             } else if (balance == total) {
            //                 $.ajax({
            //                     url: "/employee/deactivate_replace_add_nominee",
            //                     type: "post",
            //                     data: { family_members_idArray: family_members_idArray },
            //                     dataType: "json",
            //                     success: function (res) {
            //                         if (res.msg == true) {
            //                             //alert();
            //                             // swal({
            //                             //     title: "Success",
            //                             //     text: "Submitted Successfully",
            //                             //     type: "warning",
            //                             //     showCancelButton: false,
            //                             //     confirmButtonText: "Ok!",
            //                             //     closeOnConfirm: true,
            //                             //     allowOutsideClick: false,
            //                             //     closeOnClickOutside: false,
            //                             //     closeOnEsc: false,
            //                             //     dangerMode: true,
            //                             //     allowEscapeKey: false
            //                             // },
            //                             //         function ()
            //                             //         {
            //                             //             // location.reload();
            //                             //         });
            //                         } else if (res.status == 'error') {
            //                             swal({
            //                                 title: "Warning",
            //                                 text: "Please Fill All Nominee And Guardian Details",
            //                                 type: "warning",
            //                                 showCancelButton: false,
            //                                 confirmButtonText: "Ok!",
            //                                 closeOnConfirm: true,
            //                                 allowOutsideClick: false,
            //                                 closeOnClickOutside: false,
            //                                 closeOnEsc: false,
            //                                 dangerMode: true,
            //                                 allowEscapeKey: false
            //                             },
            //                                 function () {
            //                                     // location.reload();
            //                                 });
            //                             return;
            //                         } else {
            //                             swal({
            //                                 title: "Warning",
            //                                 text: "Nominees Share % Cannot Exceed 100%",
            //                                 type: "warning",
            //                                 showCancelButton: false,
            //                                 confirmButtonText: "Ok!",
            //                                 closeOnConfirm: true,
            //                                 allowOutsideClick: false,
            //                                 closeOnClickOutside: false,
            //                                 closeOnEsc: false,
            //                                 dangerMode: true,
            //                                 allowEscapeKey: false
            //                             },
            //                                 function () {
            //                                     // location.reload();
            //                                 });
            //                             return;
            //                         }
            //                     }
            //                 });
            //             } else if (balance == 0) {
            //                 swal({
            //                     title: "Warning",
            //                     text: "Nominees Share % Cannot Exceed 100%",
            //                     type: "warning",
            //                     showCancelButton: false,
            //                     confirmButtonText: "Ok!",
            //                     closeOnConfirm: true,
            //                     allowOutsideClick: false,
            //                     closeOnClickOutside: false,
            //                     closeOnEsc: false,
            //                     dangerMode: true,
            //                     allowEscapeKey: false
            //                 },
            //                     function () {
            //                         // location.reload();
            //                     });
            //                 return;
            //             } else {
            //                 swal({
            //                     title: "Warning",
            //                     text: "Share percent should be " + balance + "%",
            //                     type: "warning",
            //                     showCancelButton: false,
            //                     confirmButtonText: "Ok!",
            //                     closeOnConfirm: true,
            //                     allowOutsideClick: false,
            //                     closeOnClickOutside: false,
            //                     closeOnEsc: false,
            //                     dangerMode: true,
            //                     allowEscapeKey: false
            //                 },
            //                     function () {
            //                         // location.reload();
            //                     });
            //                 return;
            //             }
            //         } else {
            //             if (total > 100 || total < 100) {
            //                 swal({
            //                     title: "Warning",
            //                     text: "Share percent should be 100%",
            //                     type: "warning",
            //                     showCancelButton: false,
            //                     confirmButtonText: "Ok!",
            //                     closeOnConfirm: true,
            //                     allowOutsideClick: false,
            //                     closeOnClickOutside: false,
            //                     closeOnEsc: false,
            //                     dangerMode: true,
            //                     allowEscapeKey: false
            //                 },
            //                     function () {
            //                         //location.reload();
            //                     });
            //                 return;
            //             } else {

            //             }


            //         }
            //     }
            // });
        }else {
            ajaxindicatorstop();
        }
    }

});
	function otp_generate() {

    $("#myModal1").modal("hide");
    $("#sms_body").html('');
    ajaxindicatorstop();

    $.ajax({
        url: "/send_otp",
        type: "POST",
        data: {
            emp_id: emp_id,
        },
        async: false,
        dataType: "json",
        success: function (response) {
            //alert(data);
            $("#pos_otp").val("");
            $("#myModal1").modal("show");
			
          
        }
    });
}

function confirm() {
    ajaxindicatorstart("Please wait...");
    var mydeclare = $("#mydatas input[type='radio']:checked").val();

    if (mydeclare == 'Yes') {
        ajaxindicatorstop();
        swal("Alert","As the Good Health Declaration Question is YES, the proposal cannot be processed", "warning");
		
		$("#myModal1").modal("hide");
		$("#sms_body").html('');
        return false;

    }
     $('#emp_form_old').submit();
    $('#submit_nominee').trigger("click");
}

function declarepopoulate(emp_edit_id, emp_edit_policy_mem) {


$.ajax({
    url: "/edit_declare_member_data",
    type: "POST",
    async: false,
    data:{'emp_edit_id':emp_edit_id,'emp_policy_mem':emp_edit_policy_mem},
    dataType: "html",
    success: function (response) {
$(".quest_declare_benifit_4s").html(response);
       
    }
});

    $.post('/employee/get_subtype_id', {
        'emp_edit_id': emp_edit_id,
        'emp_policy_mem': emp_edit_policy_mem,
    }, function (e) {

        var obj = JSON.parse(e);
        var len = obj.length;
        var i;
        for (i = 0; i < len; i++) {
            var member = obj[i];
			console.log(member);
			 if (member.declare_sub_type_id) {
                $("#subdeclare_" + member.declare_sub_type_id).prop("checked", true);
            }
            else {

                $("#subdeclare_" + member.declare_sub_type_id + "_1").prop("checked", true);
            }
          /*   if (member.format == 'Yes') {
                $("#" + member.p_member_id).prop("checked", true);
            }
            else {

                $("#" + member.p_member_id + "_1").prop("checked", true);
            } */

        }
    });

}


$(document).on("click", "#auto_renewal", function (e) {

    if (this.checked) {
        $("#renewals").show();
    }
    else {
        $("#renewals").hide();
        $('#auto_renewal').val('');
    }

});

$.ajax({
    url: "/employee/get_master_nominee",
    type: "POST",
    async: false,
    dataType: "json",
    success: function (response) {

        // debugger;
        $("#nominee_relation").empty();
        $("#nominee_relation").append("<option value = ''>Select Nominee</option>");
        for (i = 0; i < response.length; i++) {

            $("#nominee_relation").append("<option  value =" + response[i]['nominee_id'] + ">" + response[i]['nominee_type'] + "</option>");


        }
    }
});
$(document).on("keyup", "#comAdd", function() {
    if ($(this).val().match(/[{}/"]/g)) {
        $(this).val($(this).val().replace(/[{}/"]/g, ""));
    }
});
$(document).on("click", "#validate_otp", function() {
	 $.ajax({
        url: "/validate_otp",
        type: "POST",
        data: {
            otp: $("#pos_otp").val(),
        },
        async: false,
        dataType: "json",
        success: function (response) {
            //alert(data);
		
			if(response.status == 'true'){
				submitNominee();
			$("#sms_body").html('<p>'+response.message+'</p>')
			$("#myModal1").modal("hide");
	
			}
			else{
			$("#sms_body").html('<p>'+response.message+'</p>')
			$("#pos_otp").val("");
			return;
			}
			//else
            $("#pos_otp").val("");
			
            
          
        }
    });
	
	
	
});
$(document).on("click", "#resend_otp", function() {
	
	
	otp_generate();
});




$('#salutation').click(function(){
	var salutation = $(this).val();
$("select[name=family_gender]").empty();
	$("select[name=family_members_id]").val("");
	$("select[name=family_gender]").append("<option value=''>Select Gender</option>");
	if(salutation=='')
	{
		$("#gender1").append("<option value=''>Select Gender</option>");
	}
if(salutation == '1')
{
	
	$("#gender1").val('Male');
}
else
{
	
	$("#gender1").val("Female");
}
	
});


