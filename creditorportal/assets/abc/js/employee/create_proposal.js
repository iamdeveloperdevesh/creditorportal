var ids = [];
var selectChange = "";
var eValue = "";
var son_age = 0;
var tpa_id = "";
var emp_id = $("#empIdHidden").val() || "";
var parent_id = $("#parentIdHidden").val();
var proposalId = $("#proposalId").val();
var proposalEdit = $("#proposalEdit").val();
 var proposalEdit_id = '';
if (emp_id && parent_id) {
	  var update_data_all = 'update';
	  
	  var ids = proposalId;
	
	
	if(proposalEdit!='')
	{
		if(proposalEdit == 'xxx')
		{

			$("#edit_btn").css("display", "none");
			$(".ff").css("display", "none");
			
		}
		else{
			  $('#edit_btn').show();
			  $('.ff').show();
		}
}
else{
	$('#edit_btn').show();
    $('.ff').show();
}
}
$(document).ready(function(){
$.ajax({
    url: "/employee/get_master_nominee",
    type: "POST",   
    dataType: "json",
    success: function (response) {
		
		$("#nominee_relation").empty();
		$("#nominee_relation").append("<option value = ''>Select Nominee</option>");
		for (i = 0; i < response.length; i++) {
			$("#nominee_relation").append("<option  value =" + response[i]['nominee_id'] + ">" + response[i]['nominee_type'] + "</option>");
		}
	}
});

$.ajax({
    url: "/employee/get_master_salutation",
    type: "POST",
    async: false,
    dataType: "json",
    success: function (response) {


        $("#salutation").empty();
        $("#salutation").append("<option value = ''>Select salutation</option>");
        for (i = 0; i < response.length; i++) {

            $("#salutation").append("<option  value =" + response[i]['s_id'] + ">" + response[i]['salutation'] + "</option>");


        }
    }
});
    $('#salutation').on('change touchstart', function() {

	var salutation = $(this).val();


	$("select[name=family_members_id]").val("");
	$("select[name=family_gender]").val('');
if(salutation == '')
{
	
	$("#gender1").val('');
}
else if(salutation == '1')
{
	
	$("#gender1").val('Male');
}

else
{
	
	$("#gender1").val("Female");
}
	
});




if (emp_id && parent_id) {
	var ids = proposalId;
	
	
	if(proposalEdit!='')
	{
		if(proposalEdit == 'xxx')
		{

			$("#edit_btn").css("display", "none");
			$(".ff").css("display", "none");
			
		}
		else{
			  $('#edit_btn').show();
			  $('.ff').show();
		}
}
else{
	$('#edit_btn').show();
    $('.ff').show();
}
	

  $('#add_btn').hide();
$('#bankdiv').show();
$('#documentDiv').show();
  get_nominee_data();
  get_profile_data();
 
  

	   if (emp_id && parent_id) 
	  {
		  
		  	  if(update_data_all == 'update')
	{
	
		    get_edit_proposal_declare();
	}
		   get_all_policy_data();
		   if(proposalEdit!='')
		   {
			   
		   get_all_indivisual_policy_data();
		   }
	  }
	  
	  
	    $.ajax({
    url: "/employee/get_all_dob",
    type: "POST",
    data: { emp_id: emp_id },
    
    dataType: "json",
    success: function (response) {
      if (response.famil_date != null) {
        var res = response.famil_date;
        selectChange = $(this).closest(".form_row_data");
        console.log(selectChange);
        if (res.length != 0) {
          $("input[name='s_date']").val(res.family_dob);
        }
      }
    }
  });

}

  $.ajax({
    url: "/get_all_policy_numbers1",
    type: "POST",

    dataType: "json",
    success: function (response) {
     
      $("#masPolicy").empty();
      $("#masPolicy").append(
        "<option value = ''>Select Product</option>"
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
       
        $('#masPolicy').trigger("change");
        get_other_data();
        $('#finalConfirmDiv').show();

      }
    }
  });

});

$("document").ready(function() {
		setInterval(function() {
			if($("#editDocTable tr").length > 0) {
				
			}else {
				$("input[name='ops_path']").css("pointer-events", "auto");
				
			}
		}, 500);
		if(proposalEdit!='')
		{
			
			var parts = proposalEdit.split("xxx");
			proposalEdit_id = parts[1];
		}
		
	});
function get_format_validation(mydeclare)
{
	var abc;
	  var policy_id = $('#masPolicy').val();

				$.ajax({
					url: "/get_format_declaration",
					type: "POST",
					async: false,
					data: { 'policy_id': policy_id},
					dataType: "json",
					success: function (response) {


        for (i = 0; i < response.length; i++) {
			
			if(response[i]['proposal_continue'] == mydeclare)
			{
				
			 abc="true";
	
				
			}
			else
			{
				 swal("We cannot process your proposal online");
                 return false;
				 break;
             
			}

           


        }


        
    }
});
		return abc;
}

$("#premiumBif").on("click", function () {
	
	if ($("#premiumModalBody").html().trim()) {}
		$("#premiumModal").modal();
});
$("select[name='family_members_id']").on("change", function() {

    enableDisableFamilyConstruct(this);
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
        
        enableDisableFamilyConstruct(this);
}

function enableDisableFamilyConstruct(e) {
    if(e.value == 0) {
      
        $(e).closest("form").find("input[name='first_name']").css("pointer-events","none");
        $(e).closest("form").find("input[name='last_name']").css("pointer-events","none");
        $(e).closest("form").find("input[name='family_date_birth']").css("pointer-events","none");

    }else {
     
        $(e).closest("form").find("input[name='first_name']").css("pointer-events","auto");
        $(e).closest("form").find("input[name='last_name']").css("pointer-events","auto");
        $(e).closest("form").find("input[name='family_date_birth']").css("pointer-events","auto");
    }
}

function getPremiumByAge(id) {

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

$("select[name=familyConstruct]").on("change", function () {
	debugger;
	selectChange=$(this).closest("form");
        var sumInsuresType = selectChange.find("select[name=sum_insure] :selected").attr("data-type");
console.log(sumInsuresType);
        if (sumInsuresType == "family_construct") {
            getPremium(selectChange);
			getRelation(selectChange);
        } else if (sumInsuresType == "family_construct_age") {
            getPremiumByAge("patFamilyConstruct");
			getRelation(selectChange);
        }
    });
	
	function getRelation(selectChange){

	var selected_drop = selectChange.find('select[name=familyConstruct]').val();

	if(selected_drop.includes("+"))
	{
		var fam_rel = selectChange.find('select[name=familyConstruct]').val().split("+");
	}
	else
	{
		var fam_rel = selectChange.find('select[name=familyConstruct]').val();
	}

 var opt = selectChange.find("select[name=family_members_id] option");

 
	opt.each(function(e) {
		$(this).css("display", "block");
	});
 
 

	 if(fam_rel[0].substr(0,1) == '1')
	 {
		opt.each(function(e) {
			if(this.value == 0) {
				$(this).removeAttr("disabled").show();
			}else {
				$(this).attr('disabled', 'disabled').hide();
			}
		});
     }
  

     if(fam_rel[0].substr(0,1) > '1')
	 {
		opt.each(function(e) {
			if(this.value != 2 && this.value != 3) {
				$(this).removeAttr("disabled").show();
			}else {
				$(this).attr('disabled', 'disabled').hide();
			}
		});
     }

     if(fam_rel[1] && fam_rel[1].substr(0,1) >= 1) {
        opt.each(function(e) {
			if(this.value == 2 || this.value == 3) {
				$(this).removeAttr("disabled").show();
			}
		});
     }
     
	
}
	

$(document).on("click", "input[type='radio']", function (e) {
  var product = $('#masPolicy').val();

  var id = e.target.id;

  var value = $(this).val();
  var mycontent = $('.mycontent').val();

  if (value == 'Yes') {
    var id = e.target.id;

    $.ajax({
      type: "POST",
      url: "/employee/declarelabel_comment",
      data: { parent_id: product, declare_id: id },
      dataType: "html",
      success: function (result) {
        console.log(result);
        $('.' + id).html(result)

      }
    });

  }
  if (value == 'No') {
    var id = e.target.id;


    var res = id.split("_");

    $.ajax({
      type: "POST",
      url: "/employee/declarelabel_comment",
      data: { parent_id: product, declare_id: '' },
      dataType: "html",
      success: function (result) {
        console.log(result);
        $('.' + res).html(result)

      }
    });
  }
});


function addDependentForm(tbody, elem) {

console.log(tbody);

  $("#" + tbody).html("");
  elem.data.forEach(function (e) {
    var str = "<tr>";
    str += "<td>" + e.relationship + "</td>";
    str += "<td>" + e.firstname + "</td>";
    str += "<td>" + e.lastname + "</td>";
    str += "<td>" + e.gender + "</td>";
    str += "<td>" + e.dob + "</td>";
    str += "<td>" + e.age + "</td>";
    str += "<td>" + e.age_type + "</td>";
   
	
	 if(e.fr_id == 0 ) {
            
            str += "<td></td>";

        }else {
            str += "<td><button class='btn-none-xd'  type='button' style='border: 2px solid #da9089;background: #fff; padding: 3px 12px;border-radius: 50px;font-weight: 600;' data-tableId='" + tbody + "' data-emp-id=" + emp_id + " data-policy-member-id=" + e.policy_member_id + " onclick='editPopulate(this)' ><i class='ti-marker-alt del_xd_new'></i></td>";
        }

        str += "<td class='delt_dt'><button class='btn-none-xd' type='button' style='border: 2px solid red;background: #fff; padding: 3px 12px;border-radius: 50px;font-weight: 600;' data-tableId='" + tbody + "' data-emp-id=" + emp_id + " data-policy-member-id=" + e.policy_member_id + " onclick='delPopulate(this)'><i class='ti-trash del_xd_new'></i></td>";
        str += "</tr>";
	
    $("#" + tbody).append(str);
	

  });
  
  var selectChange = $("#" + tbody).closest("form");

  var check_empty = selectChange.find('select[name=familyConstruct]').val();

  if(check_empty.length == 0)
  {
	 
  }
  else{
	
	  getRelation(selectChange);
  }



}

$("#delBtnTable").on("click", function () {
  console.log($(this).attr("data-emp-id"))
});

function changes(e) {


  selectChange = $(e).closest(".row");

  var mem_id = [];
  getgender(e);

  var parent_id = $(e).val();

 
  $.ajax({
    type: "POST",
    url: "/employee/policy_parent_data",
    data: { parent_id: parent_id },
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
  
  $.ajax({
    url: "/home/get_family_details_from_relationship",
    type: "POST",
    data: {
        relation_id: $(e).val(),
        emp_id: emp_id
    },
   
    dataType: "json",
    success: function (response) {

        $('#family_gender1').show();
  $('#show_data').hide();
    var dataOpt = selectChange
                    .find("[name='family_members_id'] :selected").attr("data-opt");
        console.log(response);
		
        selectChange.find("input[name='first_name']").val("");
		selectChange.find("input[name='middle_name']").val("");
        selectChange.find("input[name='last_name']").val("");
        selectChange.find("#family_id").val("");
        selectChange
            .find("input[type='text'][name='family_date_birth']")
            .val("");
        selectChange.find("#age1").val("");
		  selectChange.find("#age2").val("");
        selectChange.find("#age_type1").val("");
		 selectChange.find("#age_type2").val("");
        selectChange.find("#family_gender1").val("");
        selectChange.find("div .disable").attr("style", "display:none");
        selectChange.find("div .unmarried").attr("style", "display:none");
        var family_detail = response.family_data;
		console.log(family_detail[0]);
        if (true) {
			
    
            
				
            if(family_detail.length != 0){
                if (family_detail[0].fr_id == "0") {
                    selectChange
                        .find("input[name='first_name']")
                        .val(family_detail[0].emp_firstname);
                    selectChange.find("input[name='last_name']").val(family_detail[0].emp_lastname);
					 selectChange.find("input[name='middle_name']").val(family_detail[0].emp_middlename);
                    selectChange.find("#family_id").val(family_detail[0].family_id);
                    selectChange
                        .find("input[type='text'][name='family_date_birth']")
                        .val(family_detail[0].bdate);
                    selectChange
                        .find("input[name='marriage_date']")
                        .val(family_detail[0].marriage_date);
						console.log(family_detail[0].gender);
					
						if($("#gender1").val()=='')
						{
							
							selectChange.find('[name="family_gender"]').html("<option value=''>Select</option>");
						}
						else
						{
							  selectChange
                        .find("select[name='family_gender']")
                        .html("<option selected='selected' value='"+family_detail[0].gender+"' >"+family_detail[0].gender+"</option>");
							
						}
      
 
				if(selectChange.find("[name='family_members_id'] :selected").val() == '')
				{
					selectChange.find("input[name='first_name']").val("");
					selectChange.find("input[name='last_name']").val("");
					selectChange.find("input[name='middle_name']").val("");
					selectChange.find("#family_id").val("");
					selectChange
					.find("input[type='text'][name='family_date_birth']")
					.val("");
					selectChange.find('input[name="age"]').val("");
					selectChange.find("input[name='age_type']").val("");
					
					
					selectChange.find('[name="family_gender"]').html("<option value=''>Select</option>");
				}
				else{
					 get_age_family(family_detail[0].bdate, family_detail[0].family_id,selectChange);
				}
                    
        
                   
						
                }
                else{
             
			   
                    var gen = selectChange
                    .find("[name='family_members_id'] :selected").html();
				
                    
				
                    
                       if(gen == 'Spouse')
                       {
                 
                    
                            if($("#gender1").val() == 'Male' )
                            {
                               
                              selectChange.find('[name="family_gender"]').html("<option value=''>Select</option><option value='Female' selected='selected'>Female</option>");
        
                            }
                            else if($("#gender1").val() == 'Female'){
                              selectChange.find('[name="family_gender"]').html("<option value=''>Select</option><option value='Male' selected>Male</option>");
                            }
							else
							{
								selectChange.find('[name="family_gender"]').html("<option value=''>Select</option>");
							}
                       }
                       else
                       {

                        selectChange.find('[name="family_gender"]').html("<option value=''>Select</option><option selected value='"+dataOpt+"' >"+dataOpt+"</option>");

                       }
					   
				   if(typeof(family_detail[0].policy_member_first_name) != "undefined" && family_detail[0].policy_member_first_name !== null &&  gen != 'Spouse') {
						pointer_auto(selectChange);
					}
					
					
					
					
					if(typeof(family_detail[0].policy_member_first_name) != "undefined" && family_detail[0].policy_member_first_name !== null && gen == 'Spouse'){
						selectChange
                        .find("input[name='first_name']")
                        .val(family_detail[0].policy_member_first_name);						
						
							pointer_none(selectChange);
							
							  selectChange
							.find("input[name='middle_name']")
							.val(family_detail[0].policy_member_middle_name);
							selectChange.find("input[name='last_name']")
							.val(family_detail[0].policy_member_last_name);
							selectChange.find("#family_id").val(family_detail[0].policy_member_last_name);
				
							selectChange
								.find("input[type='text'][name='family_date_birth']")
								.val(family_detail[0].policy_mem_dob);
							selectChange
								.find("input[name='marriage_date']")
								.val(family_detail[0].marriage_date);
						   selectChange
								.find("input[type='text'][name='age']")
								.val(family_detail[0].age);
								selectChange
								.find("input[type='text'][name='age_type']")
								.val(family_detail[0].age_type);
							
							get_age_family(family_detail[0].bdate, family_detail[0].family_id,selectChange);
					}
				}
            }
                
                else {
             
                     var gen = selectChange
                    .find("[name='family_members_id'] :selected").html();
                    var dataOpt = selectChange
                    .find("[name='family_members_id'] :selected").attr("data-opt");
						if(gen == 'Self')
						{
							if($("#gender1").val()=='')
							{
								
								selectChange.find('[name="family_gender"]').html("<option value=''>Select</option>");
							}
						}
                        else if(gen == 'Spouse')
                        {
                          
                    
                            if($("#gender1").val() == 'Male' )
                            {
                               
                              selectChange.find('[name="family_gender"]').html("<option value=''>Select</option><option value='Female' selected>Female</option>");
        
                            }
                            else if($("#gender1").val() == 'Female'){
                              selectChange.find('[name="family_gender"]').html("<option value=''>Select</option><option value='Male' selected>Male</option>");
                            }
							else
							{
								selectChange.find('[name="family_gender"]').html("<option value=''>Select</option>");
							}
                       }
                       else
                       {

                        selectChange.find('[name="family_gender"]').html("<option value=''>Select</option><option value='"+dataOpt+"' selected>"+dataOpt+"</option>");

                       }
					   if(family_detail.length != 0){
						   
					   if(typeof(family_detail[0].policy_member_first_name) != "undefined" && family_detail[0].policy_member_first_name !== null &&  gen != 'Spouse')
						{
							pointer_auto(selectChange);
						}
						if(typeof(family_detail[0].policy_member_first_name) != "undefined" && family_detail[0].policy_member_first_name !== null && gen == 'Spouse')
						{
							selectChange
							.find("input[name='first_name']")
							.val(family_detail[0].policy_member_first_name);
					
							pointer_none(selectChange);
					
							
							  selectChange
							.find("input[name='middle_name']")
							.val(family_detail[0].policy_member_middle_name);
						selectChange
							.find("input[name='last_name']")
							.val(family_detail[0].policy_member_last_name);
						selectChange.find("#family_id").val(family_detail[0].policy_member_last_name);
			
						selectChange
							.find("input[type='text'][name='family_date_birth']")
							.val(family_detail[0].policy_mem_dob);
						selectChange
							.find("input[name='marriage_date']")
							.val(family_detail[0].marriage_date);
						get_age_family(
							family_detail[0].family_dob,
							family_detail[0].family_id,selectChange
						);
							}
					   }	
                }
        }
    }
});
};




function get_all_policy_data() {

  $.ajax({
    url: "/employee/get_all_policy_data",
    type: "POST",
    dataType: "json",
    data: { "parent_id": parent_id, 'emp_id':emp_id },
    success: function (response) {

      
  $("#sum_insures").attr("disabled", "disabled");
      $("#patFamilyConstruct").attr("disabled", "disabled");
      $("#sum_insures1").attr("disabled", "disabled");
      $("#vtlFamilyConstruct").attr("disabled", "disabled");
      $("#premium1").attr("disabled", "disabled");

      var sum_insures1 = $("#sum_insures1").val();
	   var vtlFamilyConstruct = $("#vtlFamilyConstruct").val();
	    var patFamilyConstruct = $("#patFamilyConstruct").val();
	    var sum_insures = $("#sum_insures").val();
		if(sum_insures == '')
		{
			  $("#sum_insures").removeAttr("disabled", "disabled");
			  $("#patFamilyConstruct").removeAttr("disabled", "disabled");;
		}
		
		if(sum_insures1 == '')
		{
			  $("#sum_insures1").removeAttr("disabled", "disabled");
			  $("#vtlFamilyConstruct").removeAttr("disabled", "disabled");
		}
     
      var premium1 = $("#premium1").val();

      document.getElementById("vtlForm").reset();

      $("#sum_insures1").val(sum_insures1);
      $("#vtlFamilyConstruct").val(vtlFamilyConstruct);
      $("#premium1").val(premium1);

      if (response.constructor == String) {

        addDependentForm("vtlTable", JSON.parse(response));
      }
      else {
        var patTable = {};
        patTable.data = [];
        var vtlTable = {};
        vtlTable.data = [];
		
		 for (var i = 0; i < response.data.length; i++) {
          if (response.data[i]['policy_sub_type_id'] == 3 && response.data[i]['fr_id'] == 0) {
			
		    $("#sum_insures1").attr("disabled", "disabled");
        
			
			$("#sum_insures1").val(response.data[i]['policy_mem_sum_insured']);
			$('#premium1').val(response.data[i]['policy_mem_sum_premium']);
			$("#vtlFamilyConstruct :selected").html(response.data[i]['familyConstruct']);

          } else if (response.data[i]['policy_sub_type_id'] == 1 && response.data[i]['fr_id'] == 0) {
			  
	
         
			 
			       $("#sum_insures").attr("disabled", "disabled");
			  $("#sum_insures").val(response.data[i]['policy_mem_sum_insured']);
			  $('#premium').val(response.data[0]['policy_mem_sum_premium']);
			
			    $("#patFamilyConstruct :selected").html(response.data[i]['familyConstruct']);
				
          }
		
        }

        for (var i = 0; i < response.data.length; i++) {
          if (response.data[i]['policy_sub_type_id'] == 3) {
			
		
            patTable.data.push(response.data[i]);
			
			

          } else if (response.data[i]['policy_sub_type_id'] == 1) {
			  
	
            vtlTable.data.push(response.data[i]);
	
				
          }
		
        }

        addDependentForm("patTable", vtlTable);
        addDependentForm("vtlTable", patTable);
      }

    
    }
  });
}

function get_all_policy_edit_data() {

  $.ajax({
    url: "/employee/get_all_policy_data",
    type: "POST",
    dataType: "json",
    data: { "parent_id": parent_id, 'emp_id':emp_id },
    success: function (response) {

	
    
  

      var sum_insures1 = $("#sum_insures1").val();
      var vtlFamilyConstruct = $("#vtlFamilyConstruct").val();
      var premium1 = $("#premium1").val();

      document.getElementById("vtlForm").reset();

    

      if (response.constructor == String) {

        addDependentForm("vtlTable", JSON.parse(response));
      }
      else {
        var patTable = {};
        patTable.data = [];
        var vtlTable = {};
        vtlTable.data = [];

        for (var i = 0; i < response.data.length; i++) {
          if (response.data[i]['policy_sub_type_id'] == 3) {
            patTable.data.push(response.data[i]);
			
$("#sum_insures1").val(response.data[i]['policy_mem_sum_insured']);
 $("#vtlFamilyConstruct :selected").html(response.data[i]['familyConstruct']);

          } else if (response.data[i]['policy_sub_type_id'] == 1) {
            vtlTable.data.push(response.data[i]);
			
			 $("#sum_insures").val(response.data[i]['policy_mem_sum_insured']);
			   $("#patFamilyConstruct :selected").html(response.data[i]['familyConstruct']);
          }
        }

        addDependentForm("patTable", vtlTable);
        addDependentForm("vtlTable", patTable);
      }

    
    }
  });
}
function get_nominee_data() {
	
  $.ajax({
    url: "/employee/get_all_nominee",
    type: "POST",
    dataType: "json",
    data: { "emp_id": emp_id },
    success: function (response) {
      if (response) {
			$("#nominee_relation").val(response[0].fr_id);
		  
			$("#nominee_fname").val(response[0].nominee_fname);
			$("#nominee_lname").val(response[0].nominee_lname);
			$("#nominee_dob").val(response[0].nominee_dob);
			$("#guardian_name").val(response[0].guardian_fname);
			$("#guardian_rel").val(response[0].guardian_relation);
			$(".guardina_date").val(response[0].guardian_dob);
			$("#nominee_contact").val(response[0].nominee_contact);
			
		  
        $.each(response, function (index, value) {
			
        });
      }
    }
  });
}

function get_profile_data() {
  $.ajax({
    url: "/employee/get_employee_data_new",
    type: "POST",
   
    data: { "emp_id": emp_id },
    dataType: "json",
    success: function (response) {
		
	
      $("#firstname").val(response.emp_firstname);
      $("#lastname").val(response.emp_lastname);
	  $("#middlename").val(response.emp_middlename);
      $("#panCard").val(response.pancard);
	  $("#loan_acc_no").val(response.loan_acc_no);
	  $("#salutation").val(response.salutation);
	  $("#cust_id").val(response.cust_id);
	   $("#pin_code").val(response.emp_pincode);
	   $("#city").val(response.emp_city);
	   $("#state").val(response.emp_state);
	   $("#emp_pay").val(response.emp_pay);
	  $("#occupation").val(response.occupation).attr('checked',true);
      $("#addCard").val(response.adhar);
      $("#comAdd").val(response.comm_address);
      $("#perAdd").val(response.address);
      $("#gender1").val(response.gender);
      $("#dob").val(response.bdate);
      $("#ref1").val(response.ref1);
      $("#ref2").val(response.ref2);
	  $("#loan_acc_no").addClass('ignore');
	  
      $("#mob_no").val(response.mob_no);
      $("#email").val(response.email);
     
    }
  });
}




$(document).ready(function () {

  
    $("#cheque_date").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
		minDate: new Date(),
		
        changeMonth: true,
        changeYear: true
    });

  
  $("#dob").datepicker({
    dateFormat: "dd-mm-yy",
    prevText: '<i class="fa fa-angle-left"></i>',
    nextText: '<i class="fa fa-angle-right"></i>',
    changeMonth: true,
    changeYear: true,
    minDate: "-100Y",
    maxDate: "-18Y",
    yearRange: "-100Y:-18Y"
  });
  

  $("#addCard").keyup(function (e) {
	var adhars= get_adhar_mask($(this).val());
	
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
   

    $("#resets").css("display", "none");

    var policy_detail_id = $("#masPolicy option:selected").text();
$('#prop_declare').hide();
				$('#health_declare').hide();
	
    var policy_id = $("#masPolicy option:selected").val();
	if(policy_id == 'QBTv5db9151ad1c21')
	{
		 $("#enmt_form").val("Enrollment form");		
		 $("#documentDiv").show();
		 $("#docLabel").html("Enrollment form<span style='color:#FF0000'>*</span>");
		 $("#docLabelCheckBox").html("Cheque Copy <span style='color:#FF0000'>*</span><input name='ops_type' value='Cheque Copy' style='display:none;' value='0'>");

               $("#rene").hide();
		 
	}
	else
	{
		 $("#documentDiv").show();
		 $(".cheque_copy_cls").hide();
		   $("#docLabel").html("Customer Declaration<span style='color:#FF0000'>*</span>");
	}

    if (policy_id != '') {
		
		 $.ajax({
      url: "/get_all_policy_datsa",
      type: "POST",
      data: {
          product_name: policy_detail_id
      },
  
      dataType: "json",
      success: function (response) {
         
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
                          $("#family_members_id2").append(
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
                          $("#family_members_id2").append(
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
                          $("#family_members_id2").append(
                              '<option data-opt="' +
                              response[i].gender_option +
                              '" value="' +
                              response[i].fr_id +
                              '">' +
                              response[i].fr_name +
                              "</option>"
                          );
                      } else if (response[i].fr_id == 2 || response[i].fr_id == 3) {
                          $("#family_members_id2").append(
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
              }
          }

      }
	 
  });
  
  
   $("#policySubType").val($("#masPolicy option:selected").attr("data-id"));
   
    var policy_no = $("#masPolicy option:selected").val();
    var policy_details_id = $("#masPolicy option:selected").text();

    $.ajax({
      url: "/employee/get_all_parent_gmc",
      type: "POST",

      data: { product_name: policy_details_id },

 
      dataType: "json",
      success: function (response) {
      
        if (response.length != "") {

          $("#sum_insure").empty();
          $("#sum_insures").empty();
          $("#sum_insures1").empty();
          $("#sum_insure").append(
            "<option value = ''>Select Sum insure</option>"
          );
          $("#sum_insures").append(
            "<option value = ''>Select Sum insure</option>"
          );
          $("#sum_insures1").append(
            "<option value = ''>Select Sum insure</option>"
          );

		  var arr = [];
          for (i = 0; i < response.length; ++i) {
           
            if (response[i].flate) {

              response[i].flate.forEach(function (e) {
                var sumInsured = e["sum_insured"].split(",");
                var PremiumServiceTax = e["PremiumServiceTax"].split(",");

                setMasPolicy("flate", e);
              });
            }
            if (response[i].family_construct) {
              response[i].family_construct.forEach(function (e) {
                     if (e['combo_flag'] == 'Y') {
						 $(".ti-eye").show();
						  $("#premiumBif").css("pointer-events","auto");
						  
						    if (!arr.includes(e['sum_insured'])) {
											
                                        arr.push(e['sum_insured']);
									
                                        setMasPolicy("family_construct", e);
                                    } else {

                                    }
					 }
					 else{
						  $(".ti-eye").hide();
						  $("#premiumBif").css("pointer-events","none");
						   setMasPolicy("family_construct", e);
					 }
					 
                                  
                                
              });
            }
            if (response[i].memberAge) {
              response[i].memberAge.forEach(function (e) {
               
                                    if (!arr.includes(e['sum_insured'])) {
                                        arr.push(e['sum_insured']);
                                        setMasPolicy("family_construct", e);
                                    } else {

                                    }
                                
              });
            }
          }

      
        }
      }
	  
    });
		
	
		  $.post('/getIfscCode', {
'policy_id': policy_id,'bank':'bank'
  }, function(e) {

      var obj = JSON.parse(e);
    console.log($('#bankName'));

      $('#bankName').val(obj);
  
  });
      $.ajax({


        url: "/get_declaration",
        type: "POST",
        data: { 'policy_id': policy_id },
        
        success: function (data) {

				if(data!='')
				{
				$('#health_declare').show();
          $('#policy_declare').html(data);
		  get_format_declare(policy_id);
		  
		}



        }
      });
	  


	  
	      $.ajax({


        url: "/get_declaration_proposal",
        type: "POST",
        data: { 'policy_id': policy_id },
        
        success: function (data) {
			if(data!='')
				{
		$('#prop_declare').show();

          $('#proposal_declare').html(data);
	
	
				}



        }
      });
	  
	
	  
	  	$.ajax({
    url: "/get_payment_type",
    type: "POST",
    async: false,
	data: { 'policy_id': policy_id },
    dataType: "json",
    success: function (response) {

    $("#payment_type").empty();
        $("#payment_type").append("<option value = ''>Select Payment Type</option>");
        for (i = 0; i < response.length; i++) {
			

 $("#payment_type").append("<option  value =" + response[i]['id'] + ">" + response[i]['payment_mode_name'] + "</option>");
var paytm = $('#payment_type ').filter(function(){return $(this).val() == ''}).length;
if(paytm == 1)
{
	if(response[i]['id'] == '4')
	{
		
		$('.cheq_date').show();
		$('.cheq_no').show();
		  $(".cheque_copy_cls").show();
	   $("#docLabelCheckBox").html("Cheque Copy <span style='color:#FF0000'>*</span><input name='ops_type' value='Cheque Copy' style='display:none;' value='0'>");

		  $("#payment_type").prop("selectedIndex", 1);
	  $("#payment_type").css("pointer-events","none");
	}
	$("#payment_type").css("pointer-events","auto");
	
	
}
else
{
	$("#payment_type").css("pointer-events","auto");
}

        }


        
    }
});



    }


   
   

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

    
    } else {
      $("#custDetDiv").hide();
      $("#cusDet").addClass("ignore");
    }

   
  });





  function setMasPolicy(type, e1) {
	 
    var policy =
      e1["policy_sub_type_id"] == 1
        ? "policy_no"
        : e1["policy_sub_type_id"] == 2
          ? "policy_no2"
          : "policy_no3";
    var id =
      e1["policy_sub_type_id"] == 1
        ? "sum_insures"
        : e1["policy_sub_type_id"] == 2
          ? "sum_insure"
          : "sum_insures1";

    $("#" + policy).val(e1["policy_detail_id"]);
    if (type == "flate") {
      var sumInsured = e1["sum_insured"].split(",");
      var PremiumServiceTax = e1["PremiumServiceTax"].split(",");
     
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
		debugger;

      $("#" + id).append("<option data-type='" + type + "' data-policyNo='" + e1["policy_detail_id"] + "' data-customer = '" + e1.premium + "'  data-id = '" + e1.PremiumServiceTax + "' family_child= '" + e1.child + "' family_adult= '" + e1.adult + "' value =" + e1.sum_insured + ">" + e1.sum_insured + "</option>");
    }
  }

  $("#patFamilyConstruct").on("change", function () {
	
    var patPremium = $("#patFamilyConstruct :selected").attr("data-premium");
    $("#premium").val(patPremium);
  });

  $("#vtlFamilyConstruct").on("change", function () {
    var vtlPremium = $("#vtlFamilyConstruct :selected").attr("data-premium");
    $("#premium1").val(vtlPremium);
  });

  $(document).on("change", "#sum_insures", function () {
    var tax_with_premium = $("#sum_insures option:selected").data("id");

   
    if ($("#sum_insures :selected").attr("data-type") == "family_construct" || $("#sum_insures :selected").attr("data-type") == "family_construct_age") {


      $.post("/get_family_construct", {
          "policyNo": $("#sum_insures :selected").attr("data-policyno"),
          "sumInsured": $("#sum_insures :selected").val(),
          "table": $("#sum_insures :selected").attr("data-type"),
      }, function (e) {
          $("#patFamilyConstruct").empty();

          e = JSON.parse(e);
          $("#patFamilyConstructDiv").show();
          if (e) {
              e.forEach(function (e1) {
               
                  $("#patFamilyConstruct").append("<option value='" + e1.family_type + "' data-premium='" + e1.PremiumServiceTax + "'>" + e1.family_type + "</option>");
              });

              $("#patFamilyConstruct").change();
          }
      });


  }
  else {
      $("#patFamilyConstructDiv").hide();
  }

 
  });
  $(document).on("change", "#sum_insure", function () {
    var tax_with_premium = $("#sum_insure option:selected").data("id");

    if ($("#sum_insures :selected").attr("data-type") == "family_construct") {
      $.post("/get_family_construct", {
        "policyNo": $("#sum_insures :selected").attr("data-policyno"),
        "sumInsured": $("#sum_insures :selected").val()
      }, function (e) {
        $("#patFamilyConstruct").empty();

        e = JSON.parse(e);
        $("#patFamilyConstructDiv").show();
        if (e) {
          e.forEach(function (e1) {
            console.log(e1);
            $("#patFamilyConstruct").append("<option value='" + e1.family_type + "' data-premium='" + e1.PremiumServiceTax + "'>" + e1.family_type + "</option>");
          });

          $("#patFamilyConstruct").change();
        }
      });


    } else {
      $("#patFamilyConstructDiv").hide();
    }

    $("#premium2").val(tax_with_premium);
  });
  $(document).on("change", "#sum_insures1", function () {
    var tax_with_premium = $("#sum_insures1 option:selected").data("id");

    
    if ($("#sum_insures1 :selected").attr("data-type") == "family_construct" || $("#sum_insures1 :selected").attr("data-type") == "family_construct_age") {


      $.post("/get_family_construct", {
          "policyNo": $("#sum_insures1 :selected").attr("data-policyno"),
          "sumInsured": $("#sum_insures1 :selected").val(),
          "table": $("#sum_insures1 :selected").attr("data-type"),
      }, function (e) {
          $("#vtlFamilyConstruct").empty();

          e = JSON.parse(e);
          $("#vtlFamilyConstruct").show();
          if (e) {
              e.forEach(function (e1) {
                
                  $("#vtlFamilyConstruct").append("<option value='" + e1.family_type + "' data-premium='" + e1.PremiumServiceTax + "'>" + e1.family_type + "</option>");
              });

              $("#vtlFamilyConstruct").change();
          }
      });


  }
  else {
      $("#vtlFamilyConstruct").hide();
  }

   
  });

  
  $(document).on("change", "#cusDet", function () {
	
    var customer_serach_value = $("#cusDet option:selected").val();
			alert(customer_serach_value);
    var subtype = $("#policySubType").val();
    if (customer_serach_value) {
		alert("customer_serach_value");
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
    
  });
  
    $(document).on("click", "#modal-submit", function() {
     
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
            
            dataType: "json",
            success: function(response) {
                if (response.length != 0) {
                    $("#nominee_fname").val(response.policy_member_first_name);
                    $("#nominee_lname").val(response.policy_member_last_name);
                   
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
  
  $(document).on("click", "#add_new", function () {
    selectChange.find("#first_name").val("");
    selectChange.find("#last_name").val("");
    selectChange.find("#family_id").val("");
    selectChange.find("input[type='text'][name='family_date_birth']").val("");
    $("#myModal").modal("hide");
  });

  
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



  $("#vtlForm").validate({
    ignore: ".ignore",
    rules: {
      sum_insure: {
        required: true
      },
      vtlFamilyConstruct: {
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
	    middle_name: {
       
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
ajaxindicatorstart("Please Wait...");
      if (!emp_id) {
		  ajaxindicatorstop();
        alert("Please add Customer Details First");
        return;
      }
$("#sum_insures1").removeAttr('disabled', 'disabled');
$("#vtlFamilyConstruct").removeAttr('disabled', 'disabled');
$("#family_members_id2").prop('disabled',false);
$("#family_members_id2 :selected").css('display','block');
		
		
		
		
	var product = $("#masPolicy option:selected").val();
	 var premium_vtl = $('#vtlFamilyConstruct option:selected').data('premium');
	 var familyDataType = $("#sum_insures1 :selected").attr("data-type");
      var form = $("#vtlForm").serialize() +  "&premium=" + premium_vtl + "&policyNo=" + $("#sum_insures1 :selected").attr("data-policyno") +  "&familyDataType=" + familyDataType + "&product=" + product + "&empId=" + emp_id;

      $.post("/employee/get_family_details", form, function (e) {
		  ajaxindicatorstop();
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
           
            });
          return;
        }
		
swal("success", data.message, "success");

	
        $("#vtlForm").find("input[name='edit']").val("0");
        $("#sum_insures1").attr('disabled', 'disabled');
        $("#vtlFamilyConstruct").attr('disabled', 'disabled');
        $("#premium1").attr('disabled', 'disabled');

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
			middle_name: {
              
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
            if (!Number($("#premium").val())) {
                swal("Alert", "invalid Premium", "warning");
                return;
            }

            if (!emp_id) {
                alert("Please add Customer Details First");
                return;
            }
            var TableData = [];

            var LabelData = []; 

            $('#mydatasmember tr').each(function (row, tr) {
                var LabelData = {};
                
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
			 $("#patFamilyConstruct").removeAttr('disabled', 'disabled');
			 $("#sum_insures").removeAttr('disabled', 'disabled');
			 $("#family_members_id1").prop('disabled',false);
			 $("#family_members_id1 :selected").css('display','block');
     
            var familyDataType = $("#sum_insures :selected").attr("data-type");
            var form = $("#patForm").serialize() + "&premium=" + premium_pat + "&policyNo=" + $("#sum_insures :selected").attr("data-policyno") + "&familyDataType=" + familyDataType +"&empId=" + emp_id + "&product=" + product + "&declare=" + JSON.stringify(TableData) +"&abc=2a%2B2k";

            $.post("/employee/get_family_details", form, function (e) {

                var data = JSON.parse(e);

                if (!data.status) {

                    if (data.check == "declaration") {
                        $('#confr').hide();
                    }


                    swal({
                        title: "Alert",
                        text: data.message,
                        type: "Alert",
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
                           
                        });
									 $("#sum_insures").attr('disabled', 'disabled');
                    return;
                }


				swal("Success", data.message, "success");
				
    $('#chronic th input[type="checkbox"]').prop('checked',false);


			 $(".quest_declare_benifit_4s").empty();
			 
			 $("#patFormSubmit").closest("form").find("select[name='family_gender']").html('<option value="">Select</option>');
				$("#patFormSubmit").closest("form").find("select[name='family_members_id']").removeAttr('disabled', 'disabled');
                $('#confr').show();
                $("#patForm").find("input[name='edit']").val("0");
                $("#sum_insures").attr('disabled', 'disabled');
                $("#patFamilyConstruct").attr('disabled', 'disabled');
                $("#premium").attr('disabled', 'disabled');

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
  

  $.validator.addMethod(

    "valid_mobile",
    function (value, element, param) {
	
      var re = new RegExp("^[4-9][0-9]{9}$");
      return this.optional(element) || re.test(value); 
    },
    "Enter a valid 10 digit mobile number"
  );

  $.validator.addMethod(
    "validate_aadhar",
    function (value, element, param) {
			if(value == ' ') {
			return true;
}
else{
      var reg = /^\d{4}$/g;
      return this.optional(element) || reg.test(value);
}	  
    },
    "Enter a valid aadhar number"
  );

  $.validator.addMethod(
    "valid_pan_card",
    function (value, element, param) {
      var re = new RegExp("(^([a-zA-Z]{5})([0-9]{4})([a-zA-Z]{1})$)");
	  
      return this.optional(element) || re.test(value); 
    },
    "Enter a valid PAN card number"
  );
  $.validator.addMethod(
    "validate_postal_code",
    function (value, element, param) {
var count = 1;
       if($("#pin_code").val().length >= 6) {
                var pincode = $("#pin_code").val();
                $.ajax({
                        url: "/axis_pincode_get_state_city",
                        type: "POST",
                        async: false,
data:{'pincode':pincode},
                        dataType: "json",
                        success: function (response) {

                           if(response && response.city && response.state) {
                        count = 0;      
                           }else{
count++;
}
                        }
                });
           }
if(count > 0){
 $("#pin_code").attr("readonly", false);
           $('#city').val('');
           $('#state').val('');
           return false;}else{return true;}
    },
    "Enter a valid Pin Code"
  );
$.validator.addMethod(
    "validateLoanAcc",
    function (value, element, param) {
var count = 0;

       if($("#loan_acc_no").val().length > 1) {
		 
                var loan_acc_no = $("#loan_acc_no").val();
                $.ajax({
                        url: "/loan_account_validate",
                        type: "POST",
                        async: false,
data:{'loan_acc_no':loan_acc_no},
                    
                        success: function (response) {

                           if(response=='false') {
                        count = 1;      
                           }
						   else{
							  count = 0;  
						   }
                        }
                });
           }
if(count == 1){

       

           return false;}else{return true;}
    },
    "Loan Account Number Already Exist"
  );
  
  
  $.validator.addMethod(
    "validate_ifsc_code",
    function (value, element, param) {
var count = 1;

       if($("#ifscCode").val().length > 0) {
		 
                var ifscCode = $("#ifscCode").val();
			    var policy_id = $("#masPolicy option:selected").val();
                $.ajax({
                        url: "/ifsc_account_validate",
                        type: "POST",
                        async: false,
						data:{'ifsc_code':ifscCode,'policy_id':policy_id},
                    
                        success: function (response) {

                           if(response=='true') {
                        count = 0;     
                           }
						   else{
							  count = 1;  
						   }
                        }
                });
           }
if(count == 1){

         

           return false;}else{return true;}
    },
    "Ifsc Code Does Not Exist"
  );
  
  
 $.validator.addMethod(
    "validate_pincode",
    function (value, element, param) {
       var regs = /^\d{6}$/g;
      return this.optional(element) || regs.test(value); 
    },
    "Enter a valid Pin Code"
  );
   $.validator.addMethod(
    "validate_annual_income",
    function (value, element, param) {
   
     if($("#emp_pay").val().length < 0  || $("#emp_pay").val() == 0) {
		 
		 return false;
	 }	
	 else
	 {
		 return true;
	 }
	 
    },
    "Enter Annual Income greater than 0"
  );
  $.validator.addMethod(
    "validate_cheque_no",
    function (value, element, param) {
       var regs = /^\d{6}$/g;
      return this.optional(element) || regs.test(value); 
    },
    "Enter a valid Cheque No"
  );
    $.validator.addMethod(
    "validate_account_no",
    function (value, element, param) {

		if(value.length == 0 || value ==' ')
		{
			
			return true;
		}	
	    var re = /^(?=.*?[1-9])\d+(\.\d+)?$/;
			  
			return this.optional(element) || re.test(value); 
		
    },
    "Enter a valid Number"
  );
  
     $.validator.addMethod(
    "validate_alpha_no",
    function (value, element, param) {

		if(value.length == 0 || value ==' ')
		{
			
			return true;
		}	
	
		if(Number(value) == 0)
		{
			var re = /^(?=.*?[1-9])\d+(\.\d+)?$/;
			  
			return this.optional(element) || re.test(value);
			
		 
		}
		else
		{ return true;
		}
		
    },
    "Enter a valid Number"
  );
  $.validator.addMethod(
    "validateEmail",
    function (value, element, param) {
      if (value.length == 0) {
        return true;
      }
      var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
      return reg.test(value); 
    },
    "Please enter a valid email address."
  );



  $("#mob_no,#nominee_contact,#bankAcNo,#cheque_no,#emp_pay").keyup(function (e) {

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
		 masPolicy: {
        required: true
      },
      firstname: {
        required: true
      },
      lastname: {
        required: true
      },
	  middlename: {
    
      },
	  loan_acc_no:
	  {
		   required: true,
		   validateLoanAcc: true,
		  validate_alpha_no : true
	  },
	  ref1:
	  {
		 
		 validate_account_no : true
	  },
	    ref2:
	  {
		  required: true,
		  validate_account_no : true
	  },
	  cust_id:
	  {
		 
		 validate_alpha_no : true
	  },
      dob: {
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
      panCard: {
        valid_pan_card: true,
       
      },
      addCard: {
        validate_aadhar: true,
		validate_account_no : true

       
      },
      gender1: {
       
      },
      comAdd: {
        required: true
      },
	  salutation:{
		  required: true
	  },
	  emp_pay:{
		  required: true,
		  validate_annual_income : true
	  },
	  pin_code:{
		  required: true,
		  validate_pincode: true,
		  validate_postal_code:true
		  },
	  city:{required: true},
	  state:{required: true},
	  occupation:{
		  required: true
	  },
      perAdd: {
        required: true
      },
	  occupation:{
		required: true  
	  }
    },
    messages: {
      alt_email: "Please specify email id",
      alt_email: "please enter valid email",
      emg_contact_num: "Please specify contact number"
    },
    submitHandler: function (form) {
$("#city").prop('disabled',false);
$("#state").prop('disabled',false);
		    ajaxindicatorstart("Please Wait ...");
      var all_data = $("#emp_form_old").serialize()+'&update_data_all='+update_data_all +'&edit_emp_id='+emp_id;
      $.ajax({
        type: "POST",
        url: "/employee/save_emp_data",
        data: all_data,
        dataType: "json",
        success: function (result) {
         
          if (result.status == true) {
			      ajaxindicatorstop();
				  if(update_data_all != 'update')
				  {
					$("#emp_form_old").css("pointer-events", "none");
				  }
   $("#masPolicy").css("pointer-events", "auto");
            emp_id = result.emp_id;
            swal(
              {
                title: "Success",
                text: "Inserted Successfully",
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
              function () { }
            );
          } else {
            swal(
              {
                title: "Alert",
                text: "Something went wrong",
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
                location.reload();
              }
            );
          }
        }
      });
    }
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




  
  });
});

$("#sum_insures1").on("change", function () {

});



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


  
  $("input[name=family_date_birth]").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
		maxDate: 0,
		yearRange: "-100: +0",
        onSelect: function (dateText, inst) {
            $(this).val(dateText);
			var selectChange=$(this).closest(".row");
            get_age_family(dateText, eValue,selectChange);
        }
    });
	
  function get_age_family(e,data,selectChange) {
    
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
            selectChange.find("input[name='age']").val(age_distance);
            selectChange.find("input[name='age_type']").val("years");
        } else {
            var month = ("0" + (today.getMonth() + 1)).substr(-2);


     
                var strDate =
                    ("0" + today.getDate()).substr("-2") + "-" + ("0" + month).substr("-2") + "-" + today.getFullYear();
            
			console.log()
			var strDate =
                    today.getDate() + "-" + month + "-" + today.getFullYear();
            var date = strDate.split("-");
            var display_date = date[2] + "-" + date[1] + "-" + date[0];
            var date = e.split("-");
            var date_bday = date[2] + "-" + date[1] + "-" + date[0];
            var day = new Date(display_date).getTime() - new Date(date_bday).getTime();
            var days = Math.floor(day / (1000 * 60 * 60 * 24));
             selectChange.find("input[name='age']").val(days);
            selectChange.find("input[name='age_type']").val("days");
        }
    } else {
        if (age_distance > 1) {
            selectChange.find("input[name='age']").val(age_distance);
             selectChange.find("input[name='age_type']").val("years");
            var age_distances = age_distance - 1;
        } else if (age_distance == 1) {
            var month = ("0" + (today.getMonth() + 1)).substr(-2);

           
			var strDate =
                   ("0"+today.getDate()).substr(-2)  + "-" + ("0"+month).substr(-2) + "-" + today.getFullYear();
				   
            var date = strDate.split("-");
            var display_date = date[2] + "-" + date[1] + "-" + date[0];
            var date = e.split("-");
            var date_bday = date[2] + "-" + date[1] + "-" + date[0];
            var day = new Date(display_date).getTime() - new Date(date_bday).getTime();
            var days = Math.floor(day / (1000 * 60 * 60 * 24));
			if(days >= 365){
				selectChange.find("input[name='age']").val(1);
				selectChange.find("input[name='age_type']").val("years");
			}
			else{
				 selectChange.find("input[name='age']").val(days);
             selectChange.find("input[name='age_type']").val("days");
			}
            
        } else {
            var month = ("0" + (today.getMonth() + 1)).substr(-2);


           
                var strDate =
                    ("0" + today.getDate()).substr("-2") + "-" + ("0" + month).substr("-2") + "-" + today.getFullYear();
            
			var strDate = today.getDate() + "-"  + month + "-" + today.getFullYear();
            var date = strDate.split("-");
            var display_date = date[2] + "-" + date[1] + "-" + date[0];
            var date = e.split("-");
            var date_bday = date[2] + "-" + date[1] + "-" + date[0];
             var day = new Date(display_date).getTime() - new Date(date_bday).getTime();
            var days = Math.floor(day / (1000 * 60 * 60 * 24));

             selectChange.find("input[name='age']").val(days);
            selectChange.find("input[name='age_type']").val("days");
        }
    }
	
 
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




function get_age_family_type(e, data, ett, member_id) {
 
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


$("#submitDoc").on("click", function() {
     debugger;
    var count1 = saveDocuments();

    if(count1 == 0) {
        saveDocumentsAfterValidate();
    }
});
 $("input[name=ops_path]").on("change", function () {
       var input = this;
       if (input.files && input.files[0]) {
           var reader = new FileReader();

           reader.onload = function (e) {
               console.log(e.target.result);
               $(input).next("input").val(e.target.result);

           };

           reader.readAsDataURL(input.files[0]);
       }
   });
function saveDocumentsAfterValidate(update_data_all) {
      var ops_type = document.getElementsByName("ops_type");
    var ops_path = document.getElementsByName("ops_path");
    var ops_base64 = document.getElementsByName("ops_base64");
    
	var ops_ext = [];
    var ops_type_value = [];
    var ops_path_value = [];
	   var data = new FormData();
  
    if (ops_type.length > 0) {
        for (var i = 0; i < ops_path.length; ++i) {
			if(ops_path[i].classList.contains("ignore") && ops_path[i].files.length == 0)
              {
                 
                  continue;
              }
            if (ops_type[i].value.trim().length == 0 || ops_path[i].files.length == 0) {
               
            } else {
                ops_type_value.push(ops_type[i].value.trim());
				ops_path_value.push(ops_path[i].files[0]);
             
            }
        }
    }
	
    data.append("ops_type", JSON.stringify(ops_type_value));
    for (i = 0; i < ops_type_value.length; ++i) {
      
	   data.append(i, ops_path_value[i]);
        data.append("ext"+i, ops_ext[i]);
    }

        data.append("ids", ids);
data.append("update_datas", update_data_all);
data.append("emp_id",emp_id);
        $.ajax({
            type: "POST",
         
            url: "/upload_new",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            mimeType: "multipart/form-data",
            success: function(data) {
            
                if (data == 0) {
                    setTimeout(function() {
						    ajaxindicatorstop();
                        swal("Alert", "Please check documents uploaded, Supported documents PDF, PNG,JPG,JPEG & File Size should be max 5MB")
                    }, 500);

                    return;
                }
				else{
					
					 ajaxindicatorstop();
					 setTimeout(function() {
                    swal({
                            title: "Success",
                            text: "Your Proposal Submitted successfully",
                            type: "success",
                            showCancelButton: false,
                            confirmButtonColor: "#C7222A;",
                            confirmButtonText: "ok",
                            closeOnConfirm: false,
                            closeOnCancel: false
                        },
                        function() {

                  


                            get_employee_lead_id();
                        });
                }, 500);
				}
              


            }
        });
}


function saveDocuments() {

    var ops_type = document.getElementsByName("ops_type");
    var ops_path = document.getElementsByName("ops_path");

    var ops_type_value = [];
    var ops_path_value = [];

    var count = 0;
	var fileExists = 0;
	if(!update_data_all) { 
    if (ops_type.length > 0) {
        for (var i = 0; i < ops_type.length; ++i) {
		
		
			if(ops_path[i].files.length != 0)
			{
			var abc = ops_path[i].files[0]['name'].substring(ops_path[i].files[0]['name'].lastIndexOf('.') + 1);
			var fileExtension = ['jpeg', 'jpg', 'png', 'PDF', 'pdf'];
			ajaxindicatorstop();
			if ($.inArray(abc, fileExtension) == -1){
			
				
				
				swal("Alert", "Please check documents uploaded, Supported documents PDF, PNG,JPG,JPEG & File Size should be max 5MB")
				return  1;}
			}
			if(ops_path[i].classList.contains("ignore"))
			{
			
				continue;
			}
            if (ops_path[i].files.length == 0 &&  !ops_path[i].classList.contains("ignore")) {
		
                ++count;
                if (ops_path[i].nextElementSibling && ops_path[i].nextElementSibling.tagName.toLowerCase() == "span")
                    ops_path[i].nextElementSibling.remove();
			
                var spanTag = document.createElement("span");
				spanTag.className ="docts_"+i;
                spanTag.style.color = "red";
					if($('.docts_'+i).length == 0){
				
					spanTag.innerHTML = "Field is Required";
				
				}
                
                ops_path[i].parentNode.insertBefore(spanTag, ops_type[0].nextElementSibling);
            } else {
                ops_type_value.push(ops_type[i].value.trim());
                ops_path_value.push(ops_path[i].files[0]);
			$('.docts_'+i).remove();
            }
        }
    }
}

if(update_data_all) 
{ 

if($("#editDocTable tr").length > 0) 
{
var t = $("#editDocTable tr").find("td:eq(0)");
var arr = [];
var arr1 = [];
for(i = 0; i < t.length; ++i)
{
arr.push(t[i].textContent);
}
for (var i = 0; i < ops_type.length; ++i) 
{
arr1.push(ops_type[i].value);
var filtered = arr1.filter(function (el) 
{
return el != null;
});
}
var difference = [];
jQuery.grep(filtered, function(el) 
{
if (jQuery.inArray(el, arr) == -1) difference.push(el);
});

var filtereds = difference.filter(function (el) {
return el != "";
});

for(i = 0; i < t.length; ++i) 
{

if(t.length > 2)
{
continue;
}
else
{
if(filtereds.includes("Enrollment form") && filtereds.includes("Cheque Copy"))
{
count = saveUpdateDocument(ops_type,t[i].textContent.trim(),ops_path);
}
else
{
if((t[i].textContent.trim() == "Enrollment form" || t[i].textContent.trim() == "Cheque Copy"))
{
if(filtereds.includes("Enrollment form") || filtereds.includes("Cheque Copy"))
{
	
count = saveUpdateDocument(ops_type,t[i].textContent,ops_path);

}
else
{
	continue;
}
}
else
{
	continue;
}

}

}
}


}
else{
	var content;
	count = saveUpdateDocument(ops_type,content,ops_path);
}

}
	
if(count > 0) 
{
		swal("Alert", "Please add documents", 'warning');
}

	
    return count;

}
function saveUpdateDocument(ops_type,content,ops_path)
{	debugger;
	var count = 0;

		for (var k = 0; k < ops_type.length; ++k) 
											{
												console.log(ops_type[k].value);
												console.log(content);
												
												
													if(ops_type[k].value != content && !ops_path[k].classList.contains("ignore"))
													{
														if (ops_type[k].value.trim().length == 0 || ops_path[k].files.length == 0) 
														{
															swal("Alert", "Please add documents");
				
															count = 1;
														}
														else{
															continue;
														}
													}
											
											
												
												
												
												}
												
												
												return  count;
}



$('#bankName').on('change', function() {
  $('#ifscCode').val("");
  $("#bankCity").val("");
  $("#bankBranch").val("");


  getBankCity(this.value);
});

$("#bankCity").on("change", function(e) {
  getBankBranch(this.value);
});



function getBankCity(bank_name, bank_name_value) {
  $.post("/getBankCity", {
      "bank_name": bank_name
  }, function(e) {
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

$("#bank_city").on("change", function(e) {
  getBankBranch(this.value);
});

function getBankBranch(bank_city, bank_city_value) {

  $.post("/getBankBranch", {
      "bank_name": $('#bankName').val(),
      "bank_city": bank_city,
  }, function(e) {
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

$('#bankBranch').on('change', function() {
  $('#ifscCode').val(this.selectedOptions[0].getAttribute("data-ifsc"));
});

$('#ifscCode').on('keyup', function() {
  if (this.value.length == 11) {
    
      getIfscCode(this.value);
  }
});

function getIfscCode(code) {
	var policy_id = $("#masPolicy option:selected").val();
	console.log(policy_id);
  $.post('/getIfscCode', {
      'ifsc_code': code,
	  'policy_id': policy_id,
  }, function(e) {

      var obj = JSON.parse(e);
    

      $('#bankName').val(obj.bank_name);
      
      getBankCity(obj.bank_name, obj.bank_city);
      
      getBankBranch(obj.bank_city, obj.bank_branch);

      $('#ifscCode').val(obj.ifsc_code);
  });
}
$(document).on("keyup", "#comAdd", function(e) {

       var $th = $(this);
    $th.val( $th.val().replace(/[^a-zA-Z0-9*,/. ]/, function(str) {  return ''; } ) );
   });

$(document).on("keyup", "#loan_acc_no,#ref1,#ref2,#cust_id", function(e) {

       var $th = $(this);
    $th.val( $th.val().replace(/[^a-zA-Z0-9]/, function(str) {  return ''; } ) );
   });

$("#all_submit_form").validate({ignore: ".ignore",
rules: {
  bankName: {
    required: true
  },

  branchName: {
    required: true
  },
  
  bankAcNo: {
    required: true
  },
  cheque_no: {
    required: true,
	validate_cheque_no :true
  },
  cheque_date: {
    required: true
  },
  
},
  messages: {

  },
  submitHandler: function (form) {

if($('#loan_acc_no').val().length > 0)
{
	$("#loan_acc_no").addClass("ignore");
}
		 if(!$("#emp_form_old").valid()) {
	$("#firstname").focus();
		  ajaxindicatorstop();

  return;
  }

     var mydeclare = $("#mydatas input[type='radio']:checked").val();
		var z = get_format_validation(mydeclare);
		
		if(z!='true'){
			return;
		}
		var mydeclare_prop = $("#mydatas_prop input[type='radio']:checked").val();

            if (mydeclare_prop == 'No') {
				 ajaxindicatorstop();
                swal("","We cannot proceed as the declaration isn’t agreed");
                return false;

            }
			 if(!$("#bankDetForm").valid()) {
				 $("#bankAcNo").focus()
		 ajaxindicatorstop();

 return;
 }	

var count1 = saveDocuments();

    if(count1 == 0) {
	
        $('#submit_nominee').click();
    } else {
		
		


 

           
        }


   
  }

});
$('#submit_nominee').on("click", function () {
 
    var family_members_idArray = {};
    var total = 0;
    var status = true;
    $('#policy_nominee_form_data .form_row_data1').each(function (key, value) {
        
    });

    if (!status)
    {
     
     swal("","Share percentage can not be zero");
     return false;
 } else {

    status = true;
    $('#policy_nominee_form_data .form_row_data1').each(function (key, value) {

        total += Number($(this).find('.share_per').val());
        if ($(this).find('.nominee_relation').val() == '')
        {
			status = false;
			 if($('.test1').length == 0){
					
            ($(this).find('.nominee_relation')).after("<span class='test1' style='color:red;'>family member is required</span>");
                   
             
		}
                }

                if ($(this).find('.nominee_fname').val() == '')
                {
					status = false;
                    
					 if($('.test2').length == 0){
						 ajaxindicatorstop(); 
						($(this).find('.nominee_fname')).after("<span class='test2' style='color:red;'>First name is required</span>");
						
						
					}
                }

                if ($(this).find('.nominee_lname').val() == '')
                {
					  status = false;
					 if($('.test3').length == 0){
						 ajaxindicatorstop(); 
                    ($(this).find('.nominee_lname')).after("<span class='test3' style='color:red;'>Last name is required</span>");
                   
                  
					}
                }

            
				var re = new RegExp("^[4-9][0-9]{9}$");
				 if (!re.test($(this).find('.nominee_contact').val()) && $(this).find('.nominee_contact').val() !='')
                {
					debugger;
					
						status = false;
					 if($('.test4').length == 0){
						 ajaxindicatorstop(); 
					($(this).find('.nominee_contact')).after("<span class='test4' style='color:red;'>Contact No. is starting From 4-9</span>");
					
					
					
					 }
				 
					
                }
                if ($(this).find('.share_per').val() == '')
                {
                    ($(this).find('.share_per')).after("<span style='color:red;'>Share is required</span>");
                  
                    status = false;
                }else
                {
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

    if(status!=false){
		ajaxindicatorstart("Please wait...");
		$.ajax({
            url: "/employee/add_nominee",
            type: "post",
            data: {family_members_idArray: family_members_idArray, "emp_id": emp_id,"update_nominee":update_data_all},
            dataType: "json",
            success: function (response) {
				
                if (response.msg == true) {
                                       
                                      
var TableData = [];

var LabelData = [];       

$('#mydatas tr').each(function (row, tr) {
var LabelData = {};

var label_content;
var tds = $(tr).find('td:eq(0)').text();
var label = $(tr).find('.label_id').text();
var content = $(tr).find('.mycontent').val();

var mylabel = $(tr).find('.mycontents').val();

var mylabval = $(tr).find('.mylabval').val();
if (label != '') {
LabelData = mylabel + ':' + mylabval;
}
else {
LabelData = mylabval;
}


TableData[row] = {
"question": content,
"format": $(tr).find('input[type="radio"]:checked').val(),
"label": LabelData
}



});

var TableDataProp = [];
$('#mydatas_prop tr').each(function (row, tr) {


var tds = $(tr).find('td:eq(0)').text();

var content = $(tr).find('.mycontent_prop').val();



TableDataProp[row] = {
"question_prop": content,
"format_prop": $(tr).find('input[type="radio"]:checked').val()

}

});
			

	  $.ajax({
  url: "/aprove_status",
  type: "POST",
  
  dataType: "json",
  data: {
    "policy_no": $("#masPolicy option:selected").text(),
    "emp_id": emp_id,
    "declare": TableData,
	"declare_prop":TableDataProp,
    "update_data":update_data_all,
    "family_construct": $("#patFamilyConstruct :selected").val(),
    "proposalEdit_id": proposalEdit_id,

  },
  success: function (response) {
 
    
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
            function() {
                            
                        });return;
          }
    if (response.status == true) {
		if(!update_data_all)
			ids = response.proposal_ids; 

   
	  $("#bankName").prop("disabled", false);
bank_save_details(ids,update_data_all,proposalEdit_id);
			 
			 
       


    }
  }
});
			
			
        
      
                                         
                                            }else if (response.status == 'error1') {
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
                                                
                                            });
                                    }

											else
                                            {
                                                swal({
                                                    title: "Warning",
                                                    text: "Nominees cannot be added",
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
                                                   
                                                });
                                            }
                                        }
                                    })
   
}
}

});

$("#bankDetForm").validate({
  rules: {
  bankName: {
    required: true
  },
  bankCity:{  required: true},

  bankBranch: {
    required: true
  },
  ifscCode:{
	      required: true,
		  validate_ifsc_code :true
  },
  payment_type:{required: true},
  bankAcNo: {
    required: true,
	validate_account_no : true
  },
  cheque_no: {
    required: true,
	validate_cheque_no :true
  },
  cheque_date: {
    required: true
  },
  
},
  messages: {

  },

});
function bank_save_details(ids,update_data_all,proposalEdit_id)
{
	  $("#bankName").prop("disabled", false);
	  var data = $("#bankDetForm").serialize() + "&ids=" + ids +"&update_data=" + update_data_all + "&proposalEdit_id="+proposalEdit_id;
    $.post("/bank_save_details", data, function (e) {
      e = JSON.parse(e);
      if (e.status == true) {
       			   saveDocumentsAfterValidate(update_data_all);
      }
    })
}
function confirm() {
  var mydeclare = $("#mydatas input[type='radio']:checked").val();

            
  var TableData = [];

  var LabelData = [];      

  $('#mydatas tr').each(function (row, tr) {
    var LabelData = {};
   
    var label_content;
    var tds = $(tr).find('td:eq(0)').text();
    var label = $(tr).find('.label_id').text();
    var content = $(tr).find('.mycontent').val();

    var mylabel = $(tr).find('.mycontents').val();

    var mylabval = $(tr).find('.mylabval').val();
    if (label != '') {
      LabelData = mylabel + ':' + mylabval;
    }
    else {
      LabelData = mylabval;
    }


    TableData[row] = {
      "question": content,
      "format": $(tr).find('input[type="radio"]:checked').val(),
      "label": LabelData
    }



  });

  $.ajax({
    url: "/aprove_status",
    type: "POST",
   
    dataType: "json",
    data: {
      "policy_no": $("#masPolicy option:selected").text(),
      "emp_id": emp_id,
      "declare": TableData,
      "family_construct": $("#patFamilyConstruct :selected").val()
      

    },
    success: function (response) {
    
      
      if (response.status == true) {
        ids = response.proposal_ids;
        $("#bankdiv").show();

      }
    }
  });
}
function get_employee_lead_id() {
	
    $("#emp_idDummyForm").val(emp_id);
	if(!update_data_all)
		$("#dummyForm").submit();
	else {
		location.replace('/employee/DiscrepancyView');
	}
    
}


function delPopulate(e1) {

    var edit = $(e1).closest("form").find("input[name='edit']");
    console.log("edit", edit);
	
	var id = $(e1).closest("tbody").attr("id");
    edit.val(0);
    $.post("/delete_member_new", {
        "emp_id": $(e1).attr("data-emp-id"),
        "policy_member_id": $(e1).attr("data-policy-member-id"),
    }, function (e) {
        debugger;
        if (!e) {
			$(e1).closest("form").find("select[name='family_members_id']").removeAttr('disabled', 'disabled');
			
            swal("Success", "Member Deleted Successfully", "success");
            $(e1).closest("tr").remove();
			
$(".quest_declare_benifit_4s").empty();


    $('#chronic th input[type="checkbox"]').prop('checked',false);

   


            if($("#"+id+" tr").length == 0) {
				$("#"+id).closest("form").find("select[name='familyConstruct']").html("<option value=''>Select</option>");
				$("#"+id).closest("form").find("select[name='sum_insure']").removeAttr('disabled', 'disabled');
					$("#"+id).closest("form").find("select[name='familyConstruct']").prop('disabled', false);
						$(e1).closest("form").find("select[name='family_members_id']").removeAttr('disabled', 'disabled');
                $("#sum_insures").css("pointer-events", "auto");
				

                $("#"+id).closest("form").find("select[name='familyConstruct']").css("pointer-events", "auto");
				
				          
            }
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
      success: function(response) {

          if (response.question) {
              $.each(response.question, function(k, v) {
                  $('input:radio[name="' + v.p_declare_id + '"][value="' + v.format + '"]')
                  .attr('checked', true);


                

              });
          }


          if (response.document) {
			      var ops;
              $.each(response.document, function(k, v) {
                  $('#show_tbl').show();
					
                                 ops = v.ops_type

                                if(v.autorenewal == 'Y')
                                {
                                    ops = 'Autorenewal : Y';
                                }
                                if(v.doc_type!='' && v.doc_type !=null)
                                {
                                    ops = v.doc_type;
                                }
								

                  $("#editDocTable").append("<tr><td>" + ops + "</td><td><a target='_blank' href='" + v.path + "'><img onError=this.onerror=null;this.src='/public/images/pdf1.jpg'; style='width:50px;' src='" + v.path + "' /></a></td><td class='delt_dt' style=''><button type='button' class='btn-none-xd del_id' data-id='" + v.id + "'onclick='deleteThis(this);'><i class='ti-trash del_xd_new'></i></button></td></tr>")

                  $("#ops_type").val(v.ops_type);

              });
          }
if(proposalEdit == 'xxx')
	{
		
		$('.delt_dt').css("display","none");
		$('#vtlFormSubmit').css("display","none");
		$('#patFormSubmit').css("display","none");

		
	}
          if (response.payment) {
          console.log(response.payment[0]);
            getIfscCode(response.payment[0].ifscCode);
            $("#cheque_no").val(response.payment[0].cheque_no);
               $("#bankAcNo").val(response.payment[0].account_no);
                   $("#cheque_date").val(response.payment[0].cheque_date);
           

              $("#bankId").val("1");

          }

      }

  });

}

function deleteThis(e1) {

  var id1 = $(e1).attr("data-id");

  $.post("/delete_doc", {
      "id": id1
  }, function(e) {
      $(e1).closest("tr").remove();
	  
	  if($("#editDocTable tr").length > 0) {
		
		
	  }else {
		$("input[name='ops_path']").css("pointer-events", "auto");

	  }
	  
  });
  
  
  
}



 $(document).on("click", "#auto_renewal", function(e) {
 var policy_id = $("#masPolicy option:selected").val();
	 if(this.checked) {
		 if(policy_id == 'QBTv5db9151ad1c21')
	{


               $("#renewals").hide();
		 
	}
        else
		{
            $("#renewals").show();
		}
        }
    else
    {
        $("#renewals").hide();
        $('#auto_renewal').val('');
    }

    });

    $.ajax({
    url: "/get_all_doc_type",
    type: "POST",
   
    dataType: "json",
    success: function (response) {


     $("#docs_type_master").empty();
	 
     $("#docs_type_master").append("<option value = ''>Select Document Type</option>");
     for (i = 0; i < response.length; i++) {
		 var str = response[i]['doc_type'].split(" ").join("_");
		
		
        $("#docs_type_master").append("<option  value =" + str + ">" + response[i]['doc_type'] + "</option>");


    }
}
});




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

$("select[name='family_members_id']").on("change", function() {
   
    if(this.value == 0) {
      
        $(this).closest("form").find("input[name='first_name']").css("pointer-events","none");
        $(this).closest("form").find("input[name='last_name']").css("pointer-events","none");
		$(this).closest("form").find("input[name='middle_name']").css("pointer-events","none");
        $(this).closest("form").find("input[name='family_date_birth']").css("pointer-events","none");
       
    }else {
        
        $(this).closest("form").find("input[name='first_name']").css("pointer-events","auto");
        $(this).closest("form").find("input[name='last_name']").css("pointer-events","auto");
				$(this).closest("form").find("input[name='middle_name']").css("pointer-events","auto");
        $(this).closest("form").find("input[name='family_date_birth']").css("pointer-events","auto");
    }
});

/*Member Health Questionner*************/

$('a[href="#accordion2"]').click(function () {

  var parent_id = $('#masPolicy').val();

    $.ajax({
        url: "/employee/get_member_declare_data",
        type: "POST",
        dataType: "html",
        data: { "parent_id": parent_id},
        success: function (response) {
            $(".member_declare_benifit_4s").html(response);
			
			if(response =='')
			{
				member_declaration_question();
			}
           
        }
    });
	
	




});
function member_declaration_question(){
	  var parent_id = $('#masPolicy').val();
	   $.ajax({
        url: "/employee/member_declaration_question",
        type: "POST",
        dataType: "html",
        data: { "parent_id": parent_id},
        success: function (response) {
		
        
        }
    });
}
$(document).on("click","input:checkbox[name='sub_type_check[]']",function() {
  var parent_id = $('#masPolicy').val();
	   var sub_type_id = $(this).val();
		 if($(this).prop("checked") == true){
		
	   $.ajax({
        url: "/employee/member_declaration_question",
        type: "POST",
        dataType: "html",
        data: { "parent_id": parent_id,'sub_type_id':sub_type_id},
        success: function (response) {
		
			
             $(".quest_declare_benifit_4s").append('<div id =di'+sub_type_id+'>'+response+'</div>');
        
        }
    });
   }
   else{
	
$("#di"+sub_type_id).remove();
   }
  
    });




 
    $("#pin_code").keyup(function (e) {
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
   $("#city").val('');
   $("#state").val('');
		var pincode = $(this).val();
		  if(pincode.length == 6) {
						$.ajax({
					url: "/axis_pincode_get_state_city",
					type: "POST",
					async: false,
					data:{'pincode':pincode},
					dataType: "json",
					success: function (response) {

				   if(response.city!=null && response.state!=null)
				   {
							$("#city").val(response.city);
							$("#state").val(response.state);
				   }


						
					}
				});
		  }

	});

$('#payment_type').click(function(){
	
	var payment_type = $(this).val();
	
	if(payment_type == '4')
	{
		console.log(payment_type);
		$('.cheq_date').show();
		$('.cheq_no').show();
	}
	else
	{
		$('.cheq_date').hide();
		$('.cheq_no').hide();
	}
});
function get_format_declare(policy_id)
{
		    	$.ajax({
    url: "/get_format_declaration",
    type: "POST",
    async: false,
	data: { 'policy_id': policy_id },
    dataType: "json",
    success: function (response) {


        for (i = 0; i < response.length; i++) {
			
			if(response[i]['proposal_continue'] == 'Yes')
			{
			
				$("#"+response[i]['p_declare_id']).prop("checked", true);
			}
			else
			{
				$("#"+response[i]['p_declare_id']+"_1").prop("checked", true);
			}

           


        }


        
    }
});
}
	
function get_edit_proposal_declare()
{
	if(update_data_all =='update')
	{
		    	$.ajax({
    url: "/get_edit_proposal_declare",
    type: "POST",
    async: false,
	data: { 'policy_id': parent_id,'emp_id':emp_id },
    dataType: "json",
    success: function (response) {


       
			if(response.format == 'Yes')
			{
		
				$("#"+response.proposal_declare_id).prop("checked", true);
			}
			else
			{
				$("#"+response.proposal_declare_id+"_1").prop("checked", true);
			}

           





        
    }
});
	}
}

function get_fam_data(elem) {

	
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


            $("#nominee_fname").val("");
            $("#nominee_lname").val("");
            $("input[type='text'][name='nominee_dob']").val("");
	    $('#nominee_contact').val("");

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
                   

                } else {

                    $("#nominee_fname").val(family_detail[0].policy_member_first_name);
                    $("#nominee_lname").val(family_detail[0].policy_member_last_name);
                    
                }

            }
        }
    });
}

function pointer_auto(selectChange)
{
		 selectChange.find("input[name='first_name']").css("pointer-events","auto");
         selectChange.find("input[name='last_name']").css("pointer-events","auto");
	     selectChange.find("input[name='middle_name']").css("pointer-events","auto");
         selectChange.find("input[name='family_date_birth']").css("pointer-events","auto");
	
}

function pointer_none(selectChange)
{
		 selectChange.find("input[name='first_name']").css("pointer-events","none");
         selectChange.find("input[name='last_name']").css("pointer-events","none");
		 selectChange.find("input[name='middle_name']").css("pointer-events","none");
         selectChange.find("input[name='family_date_birth']").css("pointer-events","none");
}
function get_all_indivisual_policy_data()
{
	  $.ajax({
    url: "/get_indivisual_policy_data",
    type: "POST",
    dataType: "json",
    data: { "parent_id": parent_id, 'emp_id':emp_id, 'proposal_id':proposalEdit_id},
    success: function (response) {

  

       
          
          if (response[0].policy_sub_type_id == 3) {
        
			$('#benifit_4s').css('display','none');
			 $('#benifit_5s').css('display','block');
			

          } else if (response[0].policy_sub_type_id == 1) {
			
   
          	$('#benifit_5s').css('display','none');
			 $('#benifit_4s').css('display','block');
		
		
        

     
		  }


    }
  });
	
}

$('#docs_master_file').click(function(){
	
var doc_type_masters = $("#docs_type_master").val();
var docs_master_file = $("#docs_master_file").val();
if(doc_type_masters == '')
{
	swal("Alert", "Please Select Document Type", "warning");
	return false;
}

	
	
	
});

function editPopulate(e1) {
	  debugger;
	  $.post("/get_edit_member", {
		"emp_id": $(e1).attr("data-emp-id"),
		"policy_member_id": $(e1).attr("data-policy-member-id"),
	  }, function (e) {

    e = JSON.parse(e);
    console.log(e);

    var tableid = $(e).attr("data-tableid");
	$(e1).closest("form").find("input[name='tenure']").focus();
	var selectChange = $(e1).closest("form");

    var sum_insure_select = $(e1).closest("form").find("select[name='sum_insure']")
    var family_members_id = $(e1).closest("form").find("select[name='family_members_id']")
    var family_gender = $(e1).closest("form").find("select[name='family_gender']")
    var familyConstruct = $(e1).closest("form").find("select[name='familyConstruct']")
    var first_name = $(e1).closest("form").find("input[name='first_name']")
	var middle_name = $(e1).closest("form").find("input[name='middle_name']")
    var last_name = $(e1).closest("form").find("input[name='last_name']")
    var family_date_birth = $(e1).closest("form").find("input[name='family_date_birth']")
    var age = $(e1).closest("form").find("input[name='age']")
    var age_type = $(e1).closest("form").find("input[name='age_type']")
    var edit = $(e1).closest("form").find("input[name='edit']")

  var tenure = $(e1).closest("form").find("input[name='tenure']")
 
  
    sum_insure_select.val(e.policy_mem_sum_insured);
	


	setTimeout(function() {
	familyConstruct.empty();	

	familyConstruct.html("<option selected='selected' value='"+e.familyConstruct+"' >"+e.familyConstruct+"</option>");
	
	}, 500);
	familyConstruct.show();
	
    family_members_id.val(e.fr_id);
	family_members_id.attr('disabled','disabled');
	family_members_id.addClass("ignore");
    family_gender.html("<option selected='selected' value='"+e.policy_mem_gender+"' >"+e.policy_mem_gender+"</option>");
    first_name.val(e.policy_member_first_name);
	
	middle_name.val(e.policy_member_middle_name);
    last_name.val(e.policy_member_last_name);
    family_date_birth.val(e.policy_mem_dob);
    age.val(e.age);
    age_type.val(e.age_type);
	
	
	

	

    familyConstruct.css("pointer-events", "auto");
    edit.val($(e1).attr("data-policy-member-id"));
    familyConstruct.css("pointer-events", "none");


	
    if (tableid == "patTable") {
      $("#patFamilyConstruct").change();
    } else if (tableid == "vtlTable") {
      $("#vtlFamilyConstruct").change();
    }

  });
}

function getPremium(selectChange) {
  
  

   
    $.post("/get_premium_new", { "sum_insured":  selectChange.find('select[name=sum_insure]').val(), "policy_detail_id":  selectChange.find('select[name=sum_insure] :selected').attr("data-policyno"), "family_construct":  selectChange.find('select[name=familyConstruct]').val() }, function (e) {
        $("#premiumModalHidden").val(e);
        e = JSON.parse(e);
        var premium = 0;
        $("#premiumModalBody").html("");

        e.forEach(function (e1) {
            str = "<div style='display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;'><div><span style='color:#da8085;'> Name:  " + "<br/></span><span style='color:#000;'>" + e1.policy_sub_type_id + "</span></div>";
            str += "<div><span style='color:#da8085;'> Sum Insured </span>" + "<br/><span style='color:#000;'>" + e1.sum_insured + "</span></div>";
            str += "<div><span style='color:#da8085;'> Premium" + "</span><br/><span style='color:#000;'>" + parseFloat(e1.PremiumServiceTax).toFixed(2) + "</span></div></div>";
            
            $("#premiumModalBody").append(str);
            premium += parseFloat(e1.PremiumServiceTax);
        });
        
        selectChange.find('input[name=premium]').val(premium.toFixed(2));
    });
}
