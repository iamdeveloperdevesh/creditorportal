$(document).ready(function () {

    $('#table_id').DataTable({
        "pageLength": 10
    });

});


// Display all desination
$.ajax({
    type: "GET",
    url: "/broker/get_all_designation",
    async: false,
    success: function (e) {
        var data = JSON.parse(e);
        $("#designation").empty();
        $("#designation").append("<option value=''>Select Designation</option>");
        for (i = 0; i < data.length; ++i) {
            $("#designation").append("<option value='" + data[i].master_desg_id + "'>" + data[i].designation_name + "</option>");

        }
    },
    error: function () {

    }
});


$("#doj").datepicker({
    dateFormat: 'dd-mm-yy',
    changeMonth: true,
    changeYear: true,
    showButtonPanel: true,
    //minDate: new Date()
    yearRange: "-100Y:",
    maxDate: new Date(),
    //minDate: "-100Y:-18Y",
    
});

var date = new Date();
//date.setDate(date.getDate() + 1);
$("#dob").datepicker({
    dateFormat: 'dd-mm-yy',
    changeMonth: true,
    changeYear: true,
    showButtonPanel: true,
    yearRange: "-100Y:-18Y",
    maxDate: "-18Y",
    minDate: "-100Y:-18Y",
});
$("#addRightBtn").on("click", function () {
    var d = $("#addRightsSelect").val();
    var userRights = $("#allRights").val();

    var empId = $("#empId").val();
    var empCode = $("#empCode").val();

    $.post("/broker/edit_rights", {
        empId: empId,
        userRights: d,
        empCode: empCode,
        // subRights:subRights,
       // data: data
    }, function (e) {
        if (!e)
            location.reload();
        else {
            alert("Something went Wrong");
        }
    });
    // }
});

// selection of broker display subtype
$("#addRightsSelect").change(function () {
    var id = $("#addRightsSelect").val();
    var subRightsid = $("#subRights").val();
    var userSubRights = $("#allRights").val();
    if (id == 3 || id == 2) {
        $.post("/broker/get_subrights_broker", {
            id: id,
            userSubRights: userSubRights
        }, function (e) {
            e = JSON.parse(e);
            $("#add_subtype_access").empty();

            $("#add_subtype_access").append("<option value='''>Select Subtype</option>");
            e.forEach(function (e) {
                $("#add_subtype_access").append("<option parent_id='" + e.reporting_id + "'value='" + e.access_right_id + "'>" + e.role_name + "</option>");
            });
        });
        $("#add_subtype_access_div").show();
    }
    else
    {
         $("#add_subtype_access_div").hide();
    }

})
function addRights(userRights, empId, empCode) {
 

    $("#allRights").val(userRights);
    $("#empId").val(empId);
    $("#empCode").val(empCode);
    //$("#subRights").val(subRights);

    $.post("/broker/get_all_access_rights", {
        "userRights": userRights
    }, function (e) {

        e = JSON.parse(e);
        $("#addRightsSelect").empty();
        $("#addRightsSelect").append("<option value=''' >Select </option>");
        e.forEach(function (e) {
            $("#addRightsSelect").append("<option value='" + e.access_right_id + "'>" + e.role_name + "</option>");
        });

        $("#addRights").modal("show");
    });
}
function removeRight(userRights, empId) {
    $("#allRights").val(userRights);
    $("#empId").val(empId);
    $.post("/broker/get_all_access_name", {
        "userRights": userRights
    }, function (e) {

        e = JSON.parse(e);
        $("#removeRightsSelect").empty();
        e.forEach(function (e) {
            console.log(e);
            $("#removeRightsSelect").append("<option value='" + e.access_right_id + "'>" + e.role_name + "</option>");
        });

        $("#removeRights").modal("show");
    });
}
function settings(user_id ,empId, emp_code) {

    $("#emp_id").val(empId);
    $("#emp_codes").val(emp_code);
    $("#user_id").val(user_id);

    $.post("/broker/role_access_module", {
        "empId": empId,
        "emp_code": emp_code,
        "user_id": user_id
    }, function (e) {

        e = JSON.parse(e);
        var modul = e.module_acc;
        console.log(modul.length);
        var all_modul = e.all_modulle;

        $("#modal_settings").empty();
        var mod = 0;
        var check = [];
       if(modul.length > 0){
		   console.log(modul);
            var module = modul[0].module_access_rights.split(',');
			console.log(module);
        all_modul.forEach(function (e) {
            for (var i = 0; i < module.length; i++) {

                if (e.role_module_id == module[i]) {
                    check[mod] = 'checked="checked"';

                    break;
                } else {
                    check[mod] = '';
                    //break;
                }
            }

            $("#modal_settings").append('<div class="form-check form-check-inline"><input id="my-input" class="form-check-input" name="mod_name" ' + check[mod] + ' value=' + e.role_module_id + ' type="checkbox"><label for="my-input" class="form-check-label"> ' + e.acc_module_name + '</label></div>');
            mod++;
        });
        $("#settings").modal("show");
    }
    else {
        swal ("", 'Sorry you are not applicable');
         $("#settings").modal("hide");
    }

    });
   // $("#settings").modal("show");
}
$("#settingsBtn").on("click", function () {
    var emp_id = $("#emp_id").val();
    var emp_codes = $("#emp_codes").val();

    var module_names = [];
    //var d = $('input[name="mod_name"]:checked').val();
    $.each($("input[name='mod_name']:checked"), function () {
        module_names.push($(this).val());
    });
    module_names = JSON.stringify(module_names),
            $.post("/broker/save_module_access", {
                module_names: module_names,
                emp_id: emp_id,
                emp_codes: emp_codes
            }, function (e) {
                if (!e)
                    location.reload();
                else {
                    alert("Something went Wrong");
                }
            });

});
$("#removeRightBtn").on("click", function () {

    if ($("#removeRightsSelect>option").length == 1) {
        alert("Cannot Remove the Only Right");
        return;
    }

    var userRights = $("#allRights").val().split(",");
    var empId = $("#empId").val();
    var removeRightsSelect = $("#removeRightsSelect").val();
    var newRights = "";
    userRights.forEach(function (e) {
        if (removeRightsSelect != e) {
            newRights += "," + e;
        }
    });

    $.post("/broker/edit_rights", {
        empId: empId,
        userRights: newRights.substr(1)
    }, function (e) {
        if (!e)
            location.reload();
        else {
            alert("Something went Wrong");
        }
    });
});
$.ajax({
    type: "GET",
    url: "/broker/get_all_branch",
    async: false,
    success: function (e) {
        var data = JSON.parse(e);
        $("#branch").empty();
        $("#branch").append("<option value=''>Select Branch</option>");
        for (i = 0; i < data.length; ++i) {
            $("#branch").append("<option value='" + data[i].branch_id + "'>" + data[i].branch_name + "</option>");

        }
    },
    error: function () {

    }
});

$("#roles").change(function () {
  
    var role_id = $("#roles").val();


    if (role_id == 1) {
	
        $("#broker_section_div").show();
        $("#income_div").hide();
        $("#comapny_div").hide();
        $("#employer_emp_section_div").show();
        $(".broker_rights_div1").show();
		   $(".broker_type_div2").show();
        $.post("/broker/role_access_module", {

        }, function (e) {
            e = JSON.parse(e);

            var all_modul = e.all_modulle;
            $("#broker_rights_div").empty();
            all_modul.forEach(function (e) {

                $("#broker_rights_div").append('<div class="form-check form-check-inline"><input id="my-input' + e.id + '" class="form-check-input" name="mod_names[]"  value=' + e.id + ' type="checkbox"><label for="my-input" class="form-check-label"> ' + e.module_name + '</label></div>');

            });

        });


    } 
    else if(role_id == 2 || role_id == 3)
    { 
		if(role_id == 3)
		{
			$("#com_id_input").html('<select id="company_name2"  name="company_name[]" class="form-control company_name" multiple="multiple">');


		}
		else
		{
			$("#com_id_input").html('<input class="form-control company_name" type="text" value="" id="company_name" name="company_name">');
			

		}


           $('#broker_rights_div').empty();
           $("#broker_rights_div").empty();
           $("#income_div").hide();
           $("#comapny_div").show();
           $("#broker_section_div").show();
           $("#broker_rights_div").show();
           $("#employer_emp_section_div").show();
           $(".broker_rights_div1").show();
          $(".broker_type_div2").show();

           		$('#company_name2').select2({

    placeholder: 'Select',
    search: true
});
            $.ajax({
                      url: "/broker/role_access_module",
                      type: "POST",
                      success: function(data){
                           e = JSON.parse(data);
                           console.log(e);
                           var all_modul = e.all_modulle;
                          // $("#broker_rights_div").empty();
                           all_modul.forEach(function (e) {

                $("#broker_rights_div").append('<div class="form-check form-check-inline col-md-12 mb-2"><input id="my-input' + e.role_module_id + '" class="form-check-input" name="mod_names[]"  value=' + e.role_module_id + ' type="checkbox"><label for="my-input" class="form-check-label"> ' + e.acc_module_name + '</label></div>');

            });
                      }
                  });


		get_all_company();


    }
    else {
      
        $("#income_div").hide();
        $("#comapny_div").hide();
        $("#broker_section_div").hide();
        $(".broker_rights_div1").hide();
        $("#broker_rights_div").hide();
        $("#employer_emp_section_div").hide();
		 $(".broker_type_div2").hide();
    }

});
function get_all_company()
{
	// Display all comapny data
	$.ajax({
		type: "GET",
		url: "/broker/get_all_company",
		async: false,
		success: function (e) {
			var data = JSON.parse(e);
			$("#company_name2").empty();

			for (i = 0; i < data.length; ++i) {
				$("#company_name2").append("<option value='" + data[i].company_id + "'>" + data[i].comapny_name + "</option>");

			}
		},
		error: function () {

		}
	});
}
// submit all form data

$('#user_creation').validate({

    rules: {
        roles: {
            required: true
        },
        fname: {
            required: true
        },
        lname: {
            required: true
        },
        emp_code: {
            required: true
        },
        designation: {
            required: true
        },
        city: {
            required: true
        },
        doj: {
            required: true
        },
        dob: {
            required: true
        },
        address1: {
            required: true
        },
        state: {
            required: true
        },
        pincode: {
            required: true
        },
        street: {
            required: true
        },
        Location: {
            required: true
        },
        mobile: {
            valid_mobile: true
        },
        emg_contact: {
            valid_mobile: true
        },
        email: {
            emailValidate: true
        },
        mod_names: {
            mod_namesValidate: true
        },
        sales: {
            salesValidate: true
        },
        company_name: {
            required: true
        },
        create_mode:{
           create_modeValidate: true
        }

    },
    messages: {
        roles: "Please Select roles",
        fname: "Please provide first name",
        lname: "Please provide last name",
        //gender: "Please Select gender",
        email: "Please provide email",
        emg_contact: "Please provide emergency contact",
        Location: "Please provide location",
        street: "Please provide street",
        pincode: "Please provide pincode",
        state: "Please Select state",
        address1: "Please provide address",
        designation: "Please Select designation",
        doj: "Please provide doj",
        dob: "Please Select dob",

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

  $.ajax({
                url: "/broker/save_user_creation",
                type: "POST",
                data: $("#user_creation").serialize(),
                //async: true,
                success: function(data) {
    
                  if(data == 'true')
                  {
                        swal({
                            title: "success",
                            text: "Added Successfully",
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true
                          },
              function(){
                location.reload();
              });
       			   }
       		
       			   }
            });
    }
});


$.validator.addMethod('emailValidate', function (value, element, param) {

    if (value.length == 0)
    {
        return false;
    }
    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    return reg.test(value); // Compare with regular expression
}, 'Please enter a valid email address.');

$.validator.addMethod('mod_namesValidate', function (value, element, param) {

    if ($("#roles").val() != 3 && $("#roles").val() != 2)
    {
        return false;
    }
    var checkedNum = $("input[type='checkbox'][name='mod_names']:checked").length;
    if (checkedNum == 0) {
        return false;
    }
    return true;

}, 'Please select atleast one modules');
$.validator.addMethod('salesValidate', function (value, element, param) {

    if ($("#roles").val() != 3 && $("#roles").val() != 2)
    {
        return false;
    }
    var checkedNum = $("input[type='checkbox'][name='sales']:checked").length;
    if (checkedNum == 0) {
        return false;
    }
    return true;
}, 'Please select atleast roles');

$.validator.addMethod('create_modeValidate', function (value, element, param) {

    if ($("#roles").val() != 3 && $("#roles").val() != 2)
    {
        return false;
    }
    if($("#policy_conf").val() == 11){
    var checkedNum = $("input[type='checkbox'][name='create_mode']:checked").length;
    if (checkedNum == 0) {
        return false;
    }
    }
    return true;
}, 'Please select atleast one checker');

$.validator.addMethod('valid_mobile', function (value, element, param) {
    var re = new RegExp('^[6-9][0-9]{9}$');
    return this.optional(element) || re.test(value); // Compare with regular expression
}, 'Enter a valid 10 digit mobile number');


$('#mobile').keyup(function (e) {
    var $th = $(this);

    if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
        $th.val($th.val().replace(/[^0-9]/g, function (str) {
            return '';
        }));
    }
    return;
});

$('#emg_contact').keyup(function (e) {
    var $th = $(this);

    if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
        $th.val($th.val().replace(/[^0-9]/g, function (str) {
            return '';
        }));
    }
    return;
});

$('#annual_income').keyup(function (e) {
    var $th = $(this);

    if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
        $th.val($th.val().replace(/[^0-9]/g, function (str) {
            return '';
        }));
    }
    return;
});
$('#pincode').keyup(function (e) {
    var $th = $(this);

    if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
        $th.val($th.val().replace(/[^0-9]/g, function (str) {
            return '';
        }));
    }
    return;
});



$("#fname").keyup(function (e) {
    var $th = $(this);
    if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
        $th.val($th.val().replace(/[^A-Za-z ]/g, function (str) {
            return '';
        }));
    }
    return;
});

$("#lname").keyup(function (e) {
    var $th = $(this);
    if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
        $th.val($th.val().replace(/[^A-Za-z ]/g, function (str) {
            return '';
        }));
    }
    return;
});
$("#city").keyup(function (e) {
    var $th = $(this);
    if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
        $th.val($th.val().replace(/[^A-Za-z ]/g, function (str) {
            return '';
        }));
    }
    return;
});


$("body").on('keyup', "#street", function(e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Z a-z-,0-9\s]/g, function(str) {
                return '';
            }));
        }
        return;
    });
    
    $("body").on('keyup', "#address1", function(e) {
        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^A-Z a-z-,0-9\s]/g, function(str) {
                return '';
            }));
        }
        return;
    });
    
    


$("#Location").keyup(function (e) {
    var $th = $(this);
    if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
        $th.val($th.val().replace(/[^A-Za-z ]/g, function (str) {
            return '';
        }));
    }
    return;
});


$("#pincode").keyup(function (e) {
	var pincode = $(this).val();
     $.ajax({
                url: "/axis_pincode_get_state_city",
                type: "POST",
                data: {'pincode':pincode},
                async: false,
				//dataType:'JSON',
                success: function(data) 
				{
			
					var dat = JSON.parse(data);
				
					if (dat != null) 
					{
						$('#city').val(dat.city);
						$('#state').val(dat.state);
					}
					else 
					{
						$('#city').val('');
						$('#state').val('');
					}
				
				
       			}
            }); 
});