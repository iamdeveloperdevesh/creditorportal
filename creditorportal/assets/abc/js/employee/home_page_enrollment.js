ids = [];
var selectChange = "";
var eValue = "";
var son_age = 0;
var tpa_id = "";
var emp_id = $("#empIdHidden").val() || "";
var parent_id = $("#parentIdHidden").val();
var gci_confirmation = false;
var GCI_optional_data = '';
var annual_income_data = '';
var apply_onload = 0;
var is_self_purchased = 0;
var self_purcased_with_si = 0;
var product_name;
var refresh = 0;
//ankita single journey added 2 global variable
var is_non_integrated_single_journey = 0;
var acc_type = '';
var is_domestic_pincode = 1;
/*added by ankita on click of payment mode hide document div - 31 oct 20*/
$('input[type=radio][name=payment_modes]').change(function() {
    if (this.value == 'Pay U') {
        $('.documentDiv').hide();
    }else{
    	$('.documentDiv').show();
    }
})

$("body").on("blur", ".first_name, .last_name, .nominee_fname, .nominee_lname", function() {
 
     $(this).val($(this).val().trim());
    });
	


		
$("#auto_renewal_flag").click(function(){
	if($(this).prop("checked") == true){
		var total_amount = $('#TotalPremium').text();	
		
		if(total_amount != ''){
			$("#si_amount").val(Math.ceil(total_amount*1.5)+ " (up to 150% of Initial Premium Amount)");
		}else if($("#premium").val() != ''){
			$("#si_amount").val(Math.ceil($("#premium").val()*1.5)+ " (up to 150% of Initial Premium Amount)"); 
		}
		var acc_no = $('#bankAcNo').val();
		var trailingCharsIntactCount = 4;
		var si_account_number = get_adhar_mask(acc_no);//new Array(acc_no.length - trailingCharsIntactCount + 1).join('x')+ acc_no.slice(-trailingCharsIntactCount);
		$('#si_account_number').val(si_account_number);
        $(this).val("Y");
        $("#siprocessdiv").show();
    }else if($(this).prop("checked") == false){
        $(this).val("N");
        $("#siprocessdiv").hide();
    }
})

function auto_renewal_flag(){

	if($("#auto_renewal_flag").prop("checked") == true){
		var total_amount = $('#TotalPremium').text();	
		
		if(total_amount != ''){
			$("#si_amount").val(Math.ceil(total_amount*1.5)+ " (up to 150% of Initial Premium Amount)");
		}else if($("#premium").val() != ''){
			$("#si_amount").val(Math.ceil($("#premium").val()*1.5)+ " (up to 150% of Initial Premium Amount)"); 
		}
		var acc_no = $('#bankAcNo').val();
		var trailingCharsIntactCount = 4;
		var si_account_number = get_adhar_mask(acc_no);//new Array(acc_no.length - trailingCharsIntactCount + 1).join('x')+ acc_no.slice(-trailingCharsIntactCount);
		$('#si_account_number').val(si_account_number);
        $(this).val("Y");
        $("#siprocessdiv").show();
    }else if($(this).prop("checked") == false){
        $(this).val("N");
        $("#siprocessdiv").hide();
    }
}

/*code ends here*/

/*updating annual income in database */

function insert_annual_income(emp_id,annual_income,insert){
$.ajax({
			url: "/employee/update_annual_income",
			type: "POST",
			async: false,
			data: { emp_id: emp_id, annual_income : annual_income, insert : insert},
			success: function (response) {
				if (response) {
					
					
					$("#annual_income").val(response);
					var annual_income_data = response;
					if($("#patForm").find("table tbody").children().length != 0)
					{
						//$("#annual_income").removeAttr('readonly','readonly');
					}
					else
					{
						//$("#annual_income").removeAttr('readonly','readonly');
					}
				}
			}
		});

}
/*ends*/
	/*R07*/
function annual_income_hide_show(sum_insured)
{
		
	var sumInsuresValue = $("#sum_insures :selected").val();
	var isSumAssuredMoreThan = 1000000;
	if(sumInsuresValue > isSumAssuredMoreThan) 
	{
		$("#annual_income_label").show();
		$("#occupation").show();
		$(".occu_label").html('Occupation<span style="color:#FF0000">*</span>');
		//$("#annual_income").removeClass('ignore');		
		$("#occupation").removeClass('ignore');	
		//dedupe changes
		//alert(is_self_purchased)
		if(is_self_purchased == 1){
			$('#occupation').addClass('ignore');
			$('#annual_income').addClass('ignore');
			$(".annual_label").html('Annual Income');
			$(".occu_label").html('Occupation');
		}
	}
	else
	{
		$("#annual_income_label").hide();
		$("#annual_income").addClass('ignore');
		$("#occupation").addClass('ignore');
$(".occu_label").html('Occupation');
		//$("#occupation").hide();
	}
	if(annual_income_data != '' && $("#patForm").find("table tbody").children().length != 0)
	{
		//$("#annual_income").attr('readonly','readonly');
	}
	else
	{
		$("#annual_income").val('');
	}
	
}
/*combo Family Construct And Age */
function get_premium_family_age()
{
	var policyNo = $("#sum_insures :selected").attr("data-policyno");
	var sum_insures = $("#sum_insures").val();
	var familyConstruct = $("#patFamilyConstruct").val();
	
	var gci_option = $("input[name='GCI_optional']:checked").val();
	localStorage.clear();
	$.post("/get_premium_family_age", { "policyNo": policyNo, "sum_insures": sum_insures, "familyConstruct": familyConstruct, "gci_option":gci_option,"emp_id": emp_id }, function (e) {
	e = JSON.parse(e);
	$("#premiumModalHidden").val(e);
	$(".ti-eye").show();
	$("#premiumBif").css("pointer-events","auto");
	var premium = 0;
	var PremiumServiceTax;
	$("#premiumModalBody").html("");
	es = e.premium;
	es.forEach(function (e1) {
	$('#getPremiumConstAgeModal').modal('hide');
	if(e1.ew_status == 1)
	{
	PremiumServiceTax = e1.EW_PremiumServiceTax;
	}
	else
	{
	PremiumServiceTax = e1.PremiumServiceTax;
	}
	str = "<div style='display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;'><div><span style='color:#da8085;'> Name:  " + "<br/></span><span style='color:#000;'>" + e1.policy_sub_type_id + "</span></div>";
	str += "<div class='mr-3'><span style='color:#da8085;'> Sum Insured </span>" + "<br/><span style='color:#000;'>" + e1.sum_insured + "</span></div>";
	str += "<div><span style='color:#da8085;'> Premium" + "</span><br/><span style='color:#000;'>" + parseFloat(PremiumServiceTax).toFixed(2) + "</span></div></div>";
	$("#premiumModalBody").append(str);
	premium += parseFloat(PremiumServiceTax);
	});
	$("#premium").val(premium.toFixed(2));
	localStorage.setItem('premium_store',premium.toFixed(2));
	if(e.message !== null)
	{
		swal("Alert", e.message, "Alert");
	}
	
	})
}
/*R07*/
function add_gci_name(){
	if(parent_id == 'RMxC5efb5c5a320e7'){
		//check if radio button value is 1
		if($('input[name="GCI_optional"]:checked').val() =='No'){
	$("#Total_premium").html("("+$(".personal_accident").text()+")")}else{$("#Total_premium").html("("+$(".personal_accident").text()+"+"+$(".voluntary_term").text()+")")}

		
	}
	


}
$(document).ready(function () {
	auto_renewal_flag();

	$(document).on("change","#cust_decl_form",function(){
		if($(this).val() != ''){
			$('.docts_0').remove();
			$('.upd').find('span').remove();
		}
	})

	 if(parent_id == 'RMxC5efb5c5a320e7'){
	$("#auto_renewal").closest('td').css('pointer-events','');
	}
	$("#benifit_5s").css('display','none');
	$.ajax({
	url: "/employee/get_master_salutation",
	type: "POST",
	async: false,
	dataType: "json",
	success: function (response) {
	$("#salutation").empty();
	$("#salutation").append("<option value = ''>Select salutation</option>");
	for (i = 0; i < response.length; i++) 
	{
		//console.log(response);
		$("#salutation").append("<option  value =" + response[i]['s_id'] + ">" + response[i]['salutation'] + "</option>");
	}
	}
	});	
	$.ajax({
	url: "/employee/get_master_nominee",
	type: "POST",
	async: false,
	dataType: "json",
	success: function (response) {
		$("#nominee_relation").empty();
		$("#nominee_relation").append("<option value = ''>Select Nominee</option>");
		for (i = 0; i < response.length; i++) {
		 $("#nominee_relation").append("<option  value =" + response[i]['nominee_id'] + ">" + response[i]['nominee_type'] + "</option>");
		}
	}
	});
	//dedupe changes if gpa already purchased for self then remove validation for annual income and occupation
	$.ajax({
	url: "/employee/gpa_policy_purchased_for_self",
	type: "POST",
	async: false,
	dataType: "json",
	data: {"emp_id": emp_id},
	success: function (response) {
		if(response.flag){
			is_self_purchased = response.flag;
			self_purcased_with_si = response.sum_insure;
		}
		//alert(is_self_purchased)
		if(is_self_purchased == 1){
			$('#occupation').addClass('ignore');
			$('#annual_income').addClass('ignore');
		}
	}
	});

	if (emp_id && parent_id) {
		$('#edit_btn').show();
		$('#add_btn').hide();
		$("#bankdiv").show();
		$("#documentDiv").show();
		get_nominee_data();
		get_profile_data();
		$("#update_emp_id").val(emp_id);
		$('#update_declare_id').val(parent_id);
	}

	$.ajax({
	url: "/get_all_doc_type",
	type: "POST",
	async: false,
	dataType: "json",
	success: function (response) {
		$("#docs_type_master").empty();
		$("#docs_type_master").append("<option value = ''>Select Id Type</option>");
		for (i = 0; i < response.length; i++) {
			$("#docs_type_master").append("<option  value =" + response[i]['master_doc_id'] + ">" + response[i]['doc_type'] + "</option>");
		}
		$("#docLabel").html("Customer Declaration Form <span style='color:#FF0000'>*</span>");
	}
	});
	if(is_non_integrated_single_journey == 0){ //single link journey - 28-9-21
		$.ajax({
		url: "/employee/get_imd",
		type: "POST",
		data: {"parent_id": parent_id,"emp_id": emp_id},
		success: function (response) {
		if(response == 0){
		swal({
		title: "Alert",
		text: " IMD code is not created for this branch. Please share IMD code creation request with ABHI team to process further",
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
		function () {}
		);
		return;
		}
		return false;
		}
		});
	}
	/*nri changes*/
	var nri_flag = $("#ISNRI").val();
	if(nri_flag != 'N'){
		var count = 0;
		$.ajax({
			url: "/axis_pincode_get_state_city",
			type: "POST",
			async: false,
			data:{'pincode':$('#pin_code').val()},
			dataType: "json",
			success: function (response) {
				if(response && response.city && response.state) {
				 count = 0;	//			return true;
				}else{
				 count++;
				 is_domestic_pincode = 0;
				} 
			}
		});
		//alert(count);
		if(count > 0){
			$('#comAdd').removeAttr("readonly");
			$('#pin_code').removeAttr("readonly");
			var addr = $('#comAdd').val();
			var json_addr = $('#hidden_address').val();
			//alert(addr +"== "+json_addr);
			if(addr == json_addr){
				swal("Alert","Change your address & pincode","warning");
			}
			$("#edit_btn_new").parent().show();
		}
		
	}
});
function setMasPolicy(type, e1){
	
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
	if (type == "combo_diff_construct" && e1["combo_flag"] == 'Y') {
		e1["policy_detail_id"] = e1["policy_detail_ids"]
		
	}
	$("#" + id).append("<option data-type='" + type + "' data-policyNo='" + e1["policy_detail_id"] + "' data-customer = '" + e1.premium + "'  data-id = '" + e1.PremiumServiceTax + "' family_child= '" + e1.child + "' family_adult= '" + e1.adult + "' value =" + e1.sum_insured + ">" + e1.sum_insured + "</option>");
	}
    }
$(document).on("change", "#masPolicy", function () {
	$("#resets").css("display", "none");
	var policy_detail_id = $("#masPolicy option:selected").text();
	$("#pr_name").text(policy_detail_id);
	product_name = policy_detail_id
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

	$.ajax({
	url: "/get_all_policy_datsa",
	type: "POST",
	data: {
	product_name: policy_detail_id
	},
	async: false,
	dataType: "json",
	success: function (response) {
	


				//check if product code is R07 if yes then show occupation dropdown_sumIsured
		if(response[0].product_code == 'R07'){
			$('#gci_id').show();
			$("#occupation").show();
			$("#vtlShow").hide();
			$("#benifit_8s").css("display", "block");
			$('#premiumforGci').show();
			$('#accordion3').addClass('show');
			$('.hideLabel').hide();
			$('.total_premium').show();
			
		}else{
			$('#gci_id').hide();
			$("#occupation").hide();
			$("#annual_income").addClass('ignore');
			$("#vtlShow").show();
			$("#benifit_8s").css("display", "none");
			$('#premiumforGci').hide();
			$('#accordion3').removeClass('show');
			$('.hideLabel').show();
			$('.total_premium').hide();
		}
		//
		
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
	
		var strs_gpa = response[i].policy_sub_type_name;
		var check_implode = strs_gpa.includes("+");
		if(check_implode == true)
		{
			var strs_gpa = strs_gpa.split("+");
			$(".gpa_accident").text(strs_gpa[1]);
			
		}
		$("#benifit_4s").css("display", "block");
		$("#subtype_text").val(response[i].policy_sub_type_id);
		if ((response[i].max_adult > 1 && response[i].fr_id == 1) ||response[i].fr_id == 0) {
		$("#family_members_id1").append('<option data-opt="' +response[i].gender_option +'" value="' +response[i].fr_id +'">' +response[i].fr_name +"</option>");
		} else if (response[i].max_adult == 2 &&response[i].max_child == 0 &&(response[i].fr_id == 4 ||response[i].fr_id == 5 ||response[i].fr_id == 6 ||response[i].fr_id == 7)) {
		$("#family_members_id1").append('<option data-opt="' +response[i].gender_option +'" value="' +response[i].fr_id +'">' +response[i].fr_name +"</option>");
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
		'<option data-opt="' +response[i].gender_option +'"value="' +
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
		'<option data-opt="' +response[i].gender_option +'"value="' +
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
		'<option data-opt="' +response[i].gender_option +'" value="' +
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
		'<option data-opt="' +response[i].gender_option +'" value="' +
		response[i].fr_id +
		'">' +
		response[i].fr_name +
		"</option>"
		);
		} else if (response[i].fr_id == 2 || response[i].fr_id == 3) {
		$("#family_members_id2").append(
		'<option data-opt="' +response[i].gender_option +'" value="' +
		response[i].fr_id +
		'">' +
		response[i].fr_name +
		"</option>"
		);
		}
		}
		}
		}
		 if(response[0].product_code == 'R07'){
		   
		   $("#Total_premium").html("("+$(".personal_accident").text()+"+"+$(".voluntary_term").text()+")")
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

	} else {
	$("#custDetDiv").hide();
	$("#cusDet").addClass("ignore");
	}

	$("#policySubType").val($("#masPolicy option:selected").attr("data-id"));
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
		var policyid = [];
		
		for (i = 0; i < response.length; ++i) {

	
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
		if (response[i].combo_diff_construct) {
		response[i].combo_diff_construct.forEach(function (e) {
		//policyid.push(e['policy_sub_type_id']);
		//if (e['combo_flag'] == 'Y') {
		if (!arr.includes(e['sum_insured'])) {
		arr.push(e['sum_insured']);
		
		setMasPolicy("combo_diff_construct", e);
		} 
		
		/*else {
		}.
		}*/

		});
		}
		if (response[i].memberAge) {
		response[i].memberAge.forEach(function (e) {
		setMasPolicy("memberAge", e);
		});
		}
		}
		get_all_policy_data();

		}
	}

	});
});
$("select[name='family_members_id']").on("change", function() {
enableDisableFamilyConstruct($("#family_members_id1")[0]);
});
function setFamilyConstruct(value) {

		$("#getPremiumConstAgeModal").modal('hide');
	var tax_with_premium = $("#sum_insures option:selected").data("id");
	if ($("#sum_insures :selected").attr("data-type") == "family_construct" || $("#sum_insures :selected").attr("data-type") == "family_construct_age" || $("#sum_insures :selected").attr("data-type") == "combo_diff_construct") {
	var imd_status = get_imd_status();
	var PremiumServiceTax;
var policyNo = $("#sum_insures :selected").attr("data-policyno");
	$.post("/get_family_construct", {
	"policyNo": $("#sum_insures :selected").attr("data-policyno"),
	"sumInsured": $("#sum_insures :selected").val(),
	"table": $("#sum_insures :selected").attr("data-type"),
	"emp_id": emp_id,
	}, function (e) {
	$("#patFamilyConstruct").empty();
	$("#patFamilyConstruct").html('<option value="" selected>Select</option>');
	e = JSON.parse(e);
	$("#patFamilyConstructDiv").show();
	if (e) {
	e.forEach(function (e1) {
	if(imd_status == 1)
	{
	PremiumServiceTax = e1.EW_PremiumServiceTax;
	}
	else
	{
	PremiumServiceTax = e1.PremiumServiceTax;
	}
	$("#patFamilyConstruct").append("<option value='" + e1.family_type + "' data-premium='" + PremiumServiceTax + "'data-policy='" + policyNo + "' modal-ag-cal ='"+e1.modal_age_cal+"'>" + e1.family_type + "</option>");
	});
	if(value) {
	$("#patFamilyConstruct").val(value);
	}
	if(value) {

	$("#patFamilyConstruct").change();
	

	}
	}
		apply_button();
	});
	}
	else {
	$("#patFamilyConstructDiv").hide();
	}

	enableDisableFamilyConstruct($("#family_members_id1")[0]);
}

function setvtlFamilyConstruct(value) {

	var tax_with_premium = $("#sum_insures1 option:selected").data("id");
	if ($("#sum_insures1 :selected").attr("data-type") == "family_construct" || $("#sum_insures1 :selected").attr("data-type") == "family_construct_age" || $("#sum_insures1 :selected").attr("data-type") == "combo_diff_construct") {
	var imd_status = get_imd_status();
	var PremiumServiceTax;
	$.post("/get_family_construct", {
	"policyNo": $("#sum_insures1 :selected").attr("data-policyno"),
	"sumInsured": $("#sum_insures1 :selected").val(),
	"table": $("#sum_insures1 :selected").attr("data-type"),
	"emp_id": emp_id,
	}, function (e) {
	$("#vtlFamilyConstruct").empty();
	$("#vtlFamilyConstruct").html('<option value="" selected>Select</option>');
	e = JSON.parse(e);
	$("#vtlFamilyConstructDiv").show();
	if (e) {
	e.forEach(function (e1) {
	if(imd_status == 1)
	{
	PremiumServiceTax = e1.EW_PremiumServiceTax;
	}
	else
	{
	PremiumServiceTax = e1.PremiumServiceTax;
	}
	$("#vtlFamilyConstruct").append("<option value='" + e1.family_type + "' data-premium='" + PremiumServiceTax + "'>" + e1.family_type + "</option>");
	});
	if(value) {
	$("#vtlFamilyConstruct").val(value);
	}
	else
	{
	$("#vtlFamilyConstruct").change();
	}

	
	}
	});
	}
	else {
	$("#vtlFamilyConstructDiv").hide();
	}
	//enableDisableFamilyConstruct($("#family_members_id2")[0]);
}
function get_imd_status()
{
	var data;
	    $.ajax({
            type: "POST",
            url: "/imd_status",
            data: {
                emp_id: emp_id,
				parent_id:parent_id
            },
            async : false,
            success: function (result) {
			data = result;
            }
        });
	return data;
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

function getRelation(){	
	var fam_rel = $('select[name=familyConstruct]').val().split("+");
	var opt = $("#family_members_id1 option");
	opt.each(function(e) {
		$(this).css("display", "block");
	});
  /*kids & self*/
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
function getPremium() {

	  $(".ti-eye").show();
    $("#premiumBif").css("pointer-events","auto");
    $.post("/get_premium_new", { "sum_insured": $("#sum_insures").val(), "policy_detail_id": $("#sum_insures :selected").attr("data-policyno"), "family_construct": $("#patFamilyConstruct").val(),'emp_id':emp_id }, function (e) {
	$("#premiumModalHidden").val(e);
	e = JSON.parse(e);
	var premium = 0;
	$("#premiumModalBody").html("");
	var PremiumServiceTax;
	e.forEach(function (e1) {
	if(e1.ew_status == 1){
	PremiumServiceTax = e1.EW_PremiumServiceTax;
	}
	else{
	PremiumServiceTax = e1.PremiumServiceTax;
	}
	str = "<div style='display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;'><div><span style='color:#da8085;'> Name:  " + "<br/></span><span style='color:#000;'>" + e1.policy_sub_type_id + "</span></div>";
	str += "<div><span style='color:#da8085;'> Sum Insured </span>" + "<br/><span style='color:#000;'>" + e1.sum_insured + "</span></div>";
	str += "<div class='mr-3'><span style='color:#da8085;'> Premium" + "</span><br/><span style='color:#000;'>" + parseFloat(PremiumServiceTax).toFixed(2) + "</span></div></div>";
	$("#premiumModalBody").append(str);
	premium += parseFloat(PremiumServiceTax);
	});
	if(PremiumServiceTax == undefined)
	{
	
	$('#patFamilyConstruct').val('');
	$('#premium').val('');
	$("#premiumModalBody").html('');
	}
	else
	{
		$("#premium").val(premium.toFixed(2));
	}
	
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
function addDependentForm(tbody, elem) {
debugger;
	edit = $("#"+tbody).closest("form").find("input[name='edit']");
    edit.val(0);
    var addDependent = null;
	var premium = 0;
	var premium_gpa = 0;
	var premium_vtl = 0;
	var premium_ghi;
	var ind_premium_gpa = 0;
    $("#" + tbody).html("");
		if(tbody == 'getTable')
		{
			$("#patTable").html("");
			$("#gpaTable").html("");
			$("#vtlTable").html("");

		}
		
    elem.data.forEach(function (e) {
		// alert("test2");
        if (e.message) {
            addDependent = {
                "message": e.message,
                "premium": e.new_premium,
            }
        }
		premium = e.policy_mem_sum_premium;
		if(tbody == 'getTable')
		{
			
			
			var DelEdit = false;
			if(e.policy_sub_type_id == 1)
			{tbodys = 'patTable';
			DelEdit = true;
			premium_ghi = parseFloat(e.policy_mem_sum_premium);
			}
			if(e.policy_sub_type_id == 2)
			{
				tbodys = 'gpaTable';
				DelEdit = false;
				premium_gpa += parseFloat(e.policy_mem_sum_premium);
				


			}
			if(e.policy_sub_type_id == 3)
			{
				tbodys = 'vtlTable';
				DelEdit = false;
				premium_vtl += parseFloat(e.policy_mem_sum_premium);
				ind_premium = e.policy_mem_sum_premium;
				


			}
	    var str = "<tr class = "+e.relationship+">";
        str += "<td>" + e.relationship + "</td>";
        str += "<td style = 'word-break:break-all;'>" + e.policy_member_first_name + "</td>";
        str += "<td style = 'word-break:break-all;'>" + e.policy_member_last_name + "</td>";
        str += "<td>" + e.policy_mem_gender + "</td>";
        str += "<td>" + e.policy_mem_dob + "</td>";
        str += "<td>" + e.age + "</td>";
        str += "<td>" + e.age_type + "</td>";
		if(DelEdit == false){

		str += "<td>" + e.policy_mem_sum_insured + "</td>";
        str += "<td>" + e.policy_mem_sum_premium + "</td>";
				}
		
		if(DelEdit == true){
        if(e.fr_id == 0) {
            str += "<td></td>";
        }else {
            str += "<td><button class='btn-none-xd'  type='button' style='border: 2px solid #da9089;background: #fff; padding: 3px 12px;border-radius: 50px;font-weight: 600;' data-tableId='" + tbody + "' data-emp-id=" + emp_id + " data-policy-member-id=" + e.policy_member_id + " onclick='editPopulate(this)' ><img src='/public/images/new-icons/edit16.png' class = 'del_xd_new'></img></td>";
        }
		
        str += "<td><button class='btn-none-xd' type='button' style='border: 2px solid red;background: #fff; padding: 3px 12px;border-radius: 50px;font-weight: 600;' data-tableId='" + tbody + "' data-emp-id=" + emp_id + " data-policy-member-id=" + e.policy_member_id + " onclick='delPopulate(this)'><img src='/public/images/new-icons/delete24.png' class = 'del_xd_new'></img></td>";
        }
		str += "</tr>";
        $("#" + tbodys).append(str);
		//console.log(premium_gpa);
		//console.log(premium_vtl);
	
		}
		else
		{
        var str = "<tr>";
        str += "<td>" + e.relationship + "</td>";
        str += "<td style = 'word-break:break-all;'>" + e.firstname + "</td>";
        str += "<td style = 'word-break:break-all;'>" + e.lastname + "</td>";
        str += "<td>" + e.gender + "</td>";
        str += "<td>" + e.dob + "</td>";
        str += "<td>" + e.age + "</td>";
        str += "<td>" + e.age_type + "</td>";
        if(e.fr_id == 0) {
            str += "<td></td>";
        }else {
            str += "<td><button class='btn-none-xd'  type='button' style='border: 2px solid #da9089;background: #fff; padding: 3px 12px;border-radius: 50px;font-weight: 600;' data-tableId='" + tbody + "' data-emp-id=" + emp_id + " data-policy-member-id=" + e.policy_member_id + " onclick='editPopulate(this)' ><img src='/public/images/new-icons/edit16.png' class = 'del_xd_new'></img></td>";
        }
		
        str += "<td><button class='btn-none-xd' type='button' style='border: 2px solid red;background: #fff; padding: 3px 12px;border-radius: 50px;font-weight: 600;' data-tableId='" + tbody + "' data-emp-id=" + emp_id + " data-policy-member-id=" + e.policy_member_id + " onclick='delPopulate(this)'><img src='/public/images/new-icons/delete24.png' class = 'del_xd_new'></img></td>";
        str += "</tr>";
        $("#" + tbody).append(str);
		}
    });
		$("#premiumgpa").val(premium_gpa);
		$('#premiumgci').val(premium_vtl);
		$("#TotalPremium").html(premium_ghi+premium_gpa+premium_vtl);
	var selectChange = $("#" + tbody).closest("form");
	
    if (addDependent && addDependent.message && addDependent.premium) {
        swal("Alert", addDependent.message, "warning");
		 selectChange.find("input[name='premium']").val(addDependent.premium);
 
    }
    if($("#patTable tr").length == 0) {
        $("#refreshEdit").val("1");
    }
	
 //var selectChange = $("#" + tbody).closest("form");
    if($("#patTable tr").length > 0 && Number($("#premium").val()) == 0) {
        $("#premium").val(premium)
		
    }
	get_Table_data_show_hide();
auto_renewal_flag();
}


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

	var dataOpt = selectChange.find("[name='family_members_id'] :selected").attr("data-opt");
	selectChange.find("input[name='first_name']").val("");
	selectChange.find("input[name='last_name']").val("");
	selectChange.find("input[name='family_id']").val("");
	selectChange.find("input[type='text'][name='family_date_birth']").val("");
	selectChange.find('input[name="age"]').val("");
	selectChange.find("input[name='age_type']").val("");
	selectChange.find("select[name='family_gender']")
	selectChange.find("div .disable").attr("style", "display:none");
	selectChange.find("div .unmarried").attr("style", "display:none");
	var family_detail = response.family_data;
	if (true) {
	var genders = $("#gender1").val();
	if (family_detail.length != 0) {
	if (family_detail[0].fr_id == "0") {

	selectChange.find("input[name='first_name']").css("pointer-events","none");
	selectChange.find("input[name='last_name']").css("pointer-events","none");
	selectChange.find("input[name='family_date_birth']").css("pointer-events","none");

	selectChange.find("input[name='first_name']").val(family_detail[0].emp_firstname);
	selectChange.find("input[name='last_name']").val(family_detail[0].emp_lastname);
	selectChange.find("input[name='family_id']").val(family_detail[0].family_id);
	selectChange.find("input[type='text'][name='family_date_birth']").val(family_detail[0].bdate);
	selectChange.find("input[name='marriage_date']").val(family_detail[0].marriage_date);
	if($("#gender1").val()==''){
	selectChange.find('[name="family_gender"]').html("<option value=''>Select</option>");
	}
	else if(genders!=family_detail[0].gender){
	selectChange.find("select[name='family_gender']").html("<option selected='selected' value='"+genders+"' >"+genders+"</option>");	
	}
	else
	{
	selectChange.find("select[name='family_gender']").html("<option selected='selected' value='"+family_detail[0].gender+"' >"+family_detail[0].gender+"</option>");
	}
	get_age(family_detail[0].bdate,selectChange);
	}
	else {
		$(e).closest("form").find("input[name='first_name']").css("pointer-events","auto");
		$(e).closest("form").find("input[name='last_name']").css("pointer-events","auto");
		$(e).closest("form").find("input[name='family_date_birth']").css("pointer-events","auto");	
	var gen = selectChange.find("select[name='family_members_id'] :selected").html();
	var dataOpt = selectChange.find("select[name='family_members_id'] :selected").attr("data-opt");
	if (gen == 'Spouse' || gen == 'Spouse/Partner') {
	if ($("#gender1").val() == 'Male') {
	selectChange.find('[name="family_gender"]').html("<option value=''>Select</option><option selected='selected  value='Female'>Female</option>");
	}
	else {
	selectChange.find('[name="family_gender"]').html("<option value=''>Select</option><option selected='selected value='Male'>Male</option>");
	}
	selectChange.find("input[name='first_name']").val(family_detail[0].policy_member_first_name);
	selectChange.find("input[name='last_name']").val(family_detail[0].policy_member_last_name);
	selectChange.find("input[name='family_id']").val(family_detail[0].family_id);
	selectChange.find("input[type='text'][name='family_date_birth']").val(family_detail[0].policy_mem_dob);
	selectChange.find("input[name='marriage_date']").val(family_detail[0].marriage_date);
	get_age(family_detail[0].policy_mem_dob,selectChange);	
	}
	else {
	selectChange.find('[name="family_gender"]').html("<option value=''>Select</option><option selected='selectedvalue='" + dataOpt + "'>" + dataOpt + "</option>");
selectChange.find("input[type='text'][name='family_date_birth']").val(family_detail[0].policy_mem_dob);

get_age(family_detail[0].policy_mem_dob,selectChange);	

	}     

	}
	}
	else {    
	$(e).closest("form").find("input[name='first_name']").css("pointer-events","auto");
	$(e).closest("form").find("input[name='last_name']").css("pointer-events","auto");
	$(e).closest("form").find("input[name='family_date_birth']").css("pointer-events","auto");
	var gen = selectChange.find("select[name='family_members_id'] :selected").html();
	var dataOpt = selectChange.find("select[name='family_members_id'] :selected").attr("data-opt");
	if (gen == 'Spouse') {
	if ($("#gender1").val() == 'Male') {
	selectChange.find('[name="family_gender"]').html("<option value=''>Select</option><option selected='selected value='Female'>Female</option>");
	}
	else {
	selectChange.find('[name="family_gender"]').html("<option value=''>Select</option><option selected='selected  value='Male'>Male</option>");
	}
	}
	else {
	selectChange.find('[name="family_gender"]').html("<option value=''>Select</option><option selected='selected  value='" + dataOpt + "'>" + dataOpt + "</option>");
	}
	
	}
	}
	}
	});
};


function get_all_policy_data() {
	//ankita ped changes
	if(parent_id == "iWL65dcd54d9c5a65"){
		$('.myremark').hide();
	}
	
	$.ajax({
	url: "/employee/get_all_policy_data",
	type: "POST",
	dataType: "json",
	data: {
	"parent_id": parent_id,
	"emp_id": emp_id
	},
	success: function (response) {
var z =0;
	var sum_insures1_element_count = document.getElementById("sum_insures1").length;
	if(sum_insures1_element_count == 0) {
		$("#sum_insures1").css("pointer-events", "none");
	}

	var vtlFamilyConstruct = document.getElementById("vtlFamilyConstruct").length;
	if(sum_insures1_element_count == 0) {
	$("#vtlFamilyConstruct").css("pointer-events", "none");
	}
	var sum_insures1 = $("#sum_insures1").val();
	var vtlFamilyConstruct = $("#vtlFamilyConstruct").val();
	var premium1 = $("#premium1").val();
	document.getElementById("vtlForm").reset();
	if (response.constructor == String) {
	addDependentForm("vtlTable", JSON.parse(response));
	} else {
	var patTable = {};
	patTable.data = [];
	var vtlTable = {};
	vtlTable.data = [];      
    var check_already_executed = []; 
	for (var i = 0; i < response.data.length; i++) {		
	if (response.data[i]['policy_sub_type_id'] == 3) {
	$("#sum_insures1").val(response.data[i]['policy_mem_sum_insured']);
	$("#premium1").val(response.data[i]['policy_mem_sum_premium']);
	$("#vtlFamilyConstruct :selected").html(response.data[i]['familyConstruct']);
	$("#sum_insures1").attr("disabled", "disabled");
	$("#vtlFamilyConstruct").attr("disabled", "disabled");
	vtlTable.data.push(response.data[i]);	
	} else if (response.data[i]['policy_sub_type_id'] == 1) {		
	//if (!check_already_executed.includes(response.data[i]['policy_sub_type_id']))
	//check_already_executed.push(response.data[i]['policy_sub_type_id']);
		if(parent_id == 'RMxC5efb5c5a320e7'){
			if(z == 0){
				z = 1;
				$("#sum_insures").val(response.data[i]['policy_mem_sum_insured']);  	
				setFamilyConstruct(response.data[i]['familyConstruct']);

	
					//updated by upendra - PED loggic
					$.ajax({
						url: "/get_ped_table_bb",
						type: "POST",
						data: {emp_id: emp_id},
						async: false,
						success: function (response) {
							$("#hpi_ghd_td").html();
							$("#hpi_ghd_td").html(response);

						
						}
					});
					
					
				

				
			}
			
			
		}else{
			if(parent_id == 'iWL65dcd54d9c5a65'){ //ankita ped changes
				$.ajax({
							url: "/get_ped_table_bb",
							type: "POST",
							data: {emp_id: emp_id},
							async: false,
							success: function (response) {
								$("#hpi_ghd_td").html();
								$("#hpi_ghd_td").html(response);

							
							}
						});
			}
			$("#sum_insures").val(response.data[i]['policy_mem_sum_insured']);  	
			setFamilyConstruct(response.data[i]['familyConstruct']);
			$("#sum_insures").attr("disabled", "disabled");
			$("#patFamilyConstruct").attr("disabled", "disabled");	
			patTable.data.push(response.data[i]);
		}
	}
	}
	if(parent_id == 'RMxC5efb5c5a320e7'){
		addDependentForm("getTable", response);	
	}else{
	addDependentForm("patTable", patTable);
	addDependentForm("vtlTable", vtlTable);
	}
	if(GCI_optional_data != '' && $("#patForm").find("table tbody").children().length != 0)
	{
		//$("input[name='GCI_optional']").prop('disabled',true);
		$("input[name='GCI_optional'][value='"+GCI_optional_data+"']").prop('checked',true);
	}
	else
	{
		//$("input[name='GCI_optional']").prop('disabled',false);
	}	
	if(parent_id == 'RMxC5efb5c5a320e7'){
		add_gci_name();
		//addDependentForm("patTable", patTable);
		if($("#patForm").find("table tbody").children().length == 0)
		{	annual_income_hide_show();
			$("#annual_income").val('');
		}
		else
		{			
			annual_income_hide_show();
			$("#annual_income").val(annual_income_data);
		}
	}
	}
	refresh = 1;
	if(response.data == '')
	{
		apply_onload = 2;
		refresh = 2;
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
	success: function (response) {
	if (response.question) {
	$.each(response.question, function (k, v) {
	$('input:radio[name="' + v.content + '"][value="' + v.format + '"]').attr('checked', true);
	$.ajax({
	type: "POST",
	url: "/employee/declarelabel_comment",
	data: {
	parent_id: parent_id,
	declare_id: v.p_declare_id
	},
	dataType: "html",
	success: function (result) {
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
	is_non_integrated_single_journey = response.is_non_integrated_single_journey;//ankita single journey
	acc_type = response.account_type;//ankita single journey
	if(response.email == '')
	{
	$("#email").attr("readonly", false); 
	}
	if(response.emp_pincode == '' || response.emp_pincode == '.')
	{
	$("#pin_code").attr("readonly", false); 
	}
	var adhar = get_adhar_mask(response.adhar);
	$("#firstname").val(response.emp_firstname);
	$("#lastname").val(response.emp_lastname);

	$("#panCard").val(get_card_mask(response.pancard));
	try {
	$("#addCard").val(adhar);
	}catch(e) {
	}
	GCI_optional_data = response.GCI_optional;
	annual_income_data = response.annual_income;
	$("#lead_id_product").html(response.lead_id);
/*si process add lead id to hidden filed*/
	$("#hidden_lead_id").val(response.lead_id);
	/*si process ends*/	
		$("#annual_income").html(response.annual_income);
	$("#comAdd").val(response.address);
	$("#perAdd").val(response.comm_address);
	$("#gender1").val(response.gender);
	$("#dob").val(response.bdate);
	$("#ref1").val(response.ref2);
	$("#ref2").val(response.ref1);
	$("#ISNRI").val(response.ISNRI);
	$("#mob_no").val(response.mob_no);
	$("#email").val(response.email);
	$("#occupation").val(response.occupation);
	$("#salutation option").filter(function(){
	return $(this).text() === response.salutation ? $(this).prop("selected", true) : false;
	});
	//salutation value is as per master make it non editable - 07-10-21(ankita)
	if($("#salutation option:selected").text() == response.salutation){
		$("#salutation").css({"pointer-events": "none"});
	}
	
	$("#marital_status").val('');
	$("#pin_code").val(response.emp_pincode);
	$("#city").val(response.emp_city);
	$("#state").val(response.emp_state);
	$("#ifscCode").val(response.ifsc_code);
	var json_quote = JSON.parse(response.json_qote);
	$('#bankAcNo').val(json_quote['ACCOUNTNUMBER']);
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
	if(response.is_non_integrated_single_journey == 1 && acc_type != 'Saving Bank Account Holder'){//ankita single journey
		$("#ifscCode").attr("readonly", false); 
		$("#bankName").attr("readonly", true); 
		$("#bankAcNo").attr('readonly',false);
		$("#ifscCode").addClass("ignore"); 
		$("#bankName").addClass("ignore"); 
		$("#bankAcNo").addClass("ignore");
		$('.bDet').hide();
	}
	$("#payment_type").attr("value","");
	$("#cheque_type").attr("value","");
	$('#payment_type').val('');
	$('#cheque_type').val('');
	$('.cheque_up').hide();
	$("#bank_branch_name").hide();
	$('#cheque_dates').hide();
	$('#cheque_types').hide();
	$('#payment_types').hide();
	$('#cheque_number').hide();
	$('#micr_number').hide();
	getIfscCode(response.ifsc_code);
	}
	if (response.ISNRI == 'Y') {
	//$("#comAdd").attr("readonly", false);
	}
	}
	});
}
$(document).ready(function () {

	//ankita ghd changes
	//do not allow special characters for ghd remark
$(document).on("keyup", ".textAreaGHD", function()
	{
        var desc = $(this).val(); 
        var lastChar = desc.slice(-1);
        var spc = (!((lastChar.charCodeAt()>=48&&lastChar.charCodeAt()<=57)||(lastChar.charCodeAt()>=65&&lastChar.charCodeAt()<=90)||(lastChar.charCodeAt()>=97&&lastChar.charCodeAt()<=122)) || (!(lastChar.charCodeAt()==44)));

        if (spc) 
        { 
            var rp = this.value.replace(/[&!@^_=\/\\#+{}|[\]()$~%.'":;*?<>]/g, "");
            $(this).val(rp);
        }

    });

	$("#cheque_date").datepicker({
	dateFormat: "dd-mm-yy",
	prevText: '<i class="fa fa-angle-left"></i>',
	nextText: '<i class="fa fa-angle-right"></i>',
	minDate: '-90',
	maxDate: new Date(),
	changeMonth: true,
	changeYear: true
	});
    /*adhar validation*/
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
		
			var get_family_const = $("#family_const_id").val();
			selectChange = $("#"+get_family_const).closest("form");
			
            var policyNo = selectChange.find("select[name='sum_insure'] :selected").attr("data-policyno");
            var sum_insures = selectChange.find("select[name='sum_insure'] :selected").val();
            var familyConstruct = $("#"+get_family_const).val();
            var maxPremiumAge = $("#getMaxPremiumAge").val();
            $.post("/get_premium_age", { "policyNo": policyNo, "sum_insures": sum_insures, "familyConstruct": familyConstruct, "maxPremiumAge": maxPremiumAge }, function (e) {
                e = JSON.parse(e);
                selectChange.find("input[name='premium']").val("");
                if (e.status) {
					if(get_family_const == 'patFamilyConstruct')
					{
						$("#premium").val(e.premium);
					}
					else
					{
						selectChange.find("input[name='premium']").val(e.premium);
					}
                    
                    $('#getMaxPremiumAgeModal').modal('hide');
                } else {
                    swal("Alert", e.message, "warning");
                }
            })
        }
    });
    function getPremiumByAge(id) {
	
	var familyConstruct = $("#"+id).val();
	$("#family_const_id").val(id);
	$("#getMaxPremiumAgeLabel").html('');
	if(familyConstruct == '1A')
	{
	$("#getMaxPremiumAgeLabel").html('Enter Member Age');
	}
	else		 
	{
	$("#getMaxPremiumAgeLabel").html('Enter Eldest Member Age');
	}
	if(parent_id == 'RMxC5efb5c5a320e7')
	{

	}
	else
	{
	$("#premiumBif").attr('disabled', 'disabled');
	$(".ti-eye").attr('disabled', 'disabled').hide();
	}
	var edit = $("#"+id).closest("form").find("input[name='edit']").val();
	$("#getMaxPremiumAge").val("");
	if($("#refreshEdit").val() == "0") {
	console.log("patTable", $("#patTable"));
	$("#refreshEdit").val("1");
	return;
	}
	if(familyConstruct !='')
	{
		if(edit == 0 || edit == "")
	$("#getMaxPremiumAgeModal").modal(); 
    }
	}
	
	
    $("#patFamilyConstruct").on("change", function () {
	debugger;
	
        var sumInsuresType = $("#sum_insures :selected").attr("data-type");
		if(parent_id == 'RMxC5efb5c5a320e7')
			{
				if($("#patTable tr").length > 0) {
					
						apply_button();
						  $("#helathFamilyModal").modal('hide');
					
				}
			}
		
        if (sumInsuresType == "family_construct") {
           
			/*if(parent_id == 'RMxC5efb5c5a320e7')
			{
				insert_annual_income(emp_id,'',false);
				getPremiumconstByAge("patFamilyConstruct");
				$("#getPremiumConstAgeModal").modal('hide');
				//annual_income_logic();
			}*/
			/*var annual_income = $("#annual_income").val();*/
			
			getPremium();
			
        } 
		else if(sumInsuresType == "combo_diff_construct")
		{
			
			combo_premium();
		}		
		else if (sumInsuresType == "family_construct_age") {
            getPremiumByAge("patFamilyConstruct");
		 //	getRelation();
        }
    });
		function combo_premium()
		{
		var modal_age_cal = $("#patFamilyConstruct :selected").attr("modal-ag-cal");
		var fam_const = $('select[name=familyConstruct]').val().split("+");
		var adult = 'A';
		var kid = 'K';
		var adult_concat;
		var kid_concat;
		var max_adult;
		var child;
		var str ='';
		var str_1 ='';
		var str1 = '';
		var arr = [];
		var i;
		var policy_no =  $("#patFamilyConstruct :selected").attr("data-policy");



		if(modal_age_cal == 'Y')
		{
		$.post("/employee/get_combo_rel", {
		"policy_no": policy_no,
		}, function (e) {

		res = JSON.parse(e);
		if (res) {
		$('.adult_data').html('');
		$('.adult_row').html('');

		res.forEach(function (e1) {


		max_adult = e1.max_adult;
		adult_concat = max_adult.concat(adult);

		if(e1.multiple_allowed == 'N')
		{

		if(fam_const[0] < adult_concat)
		{
		$(".check_adult").removeAttr("style", "display:none");
		str = "<div class='custom-control custom-radio'><input type='radio' class='custom-control-input' name='adult_fr_id' id='adult_fr_id_"+e1.fr_id+"'  value='"+e1.fr_id+"'><label class='custom-control-label' for='adult_fr_id_"+e1.fr_id+"'>"+e1.fr_name+"</label></div>";	
		$('.adult_data').append(str);


		}
		else
		{

		$(".check_adult").attr("style", "display:none");
		if(e1.fr_id !=0){
		$(".adult_row").removeAttr("style", "display:none");
		other_than_self_dropdown(e1.fr_id,e1.fr_name);
		}
		}

		}
		if(fam_const[1] != '')
		{
		if(e1.multiple_allowed == 'Y'){

		arr.push([e1.fr_name,e1.fr_id,e1.gender_option]);

		}
		}
		});
		$('.kids_row').html('');
		if(fam_const[1] != '' && fam_const[1] != undefined)
		{	
		var fam_rel = fam_const[1].substr(0,1);
		for(i = 1;i<=fam_rel;i++)
		{

		str1 = "<div class='mb-2 col-md-12 row kid"+i+"_div'><div class='col-md-6'><div class='form-group'><label class='col-form-label'>Relation With Proposer<span style='color:#FF0000'>*</span></label> <select class='form-control' name='kid_rel[kid_"+i+"][kid_"+i+"_rel]' id='kid_"+i+"_rel'><option value=''>Select Relation</option>";
		arr.forEach(function(arrs)
		{ str1+ = "<option data-opt='"+arrs[2]+"' value='"+arrs[1]+"'>"+arrs[0]+"</option>";
		});
		str1+ = "</select> </div> </div><div class='col-md-6'> <div class='form-group'> <label class='col-form-label'>Date of Birth<span style='color:#FF0000'>*</span></label> <input class='form-control kid"+i+"_date_birth modal_dob ad_valid' autocomplete='off' type='text' id='kid_"+i+"_date_birth' name='kid_rel[kid_"+i+"][kid_"+i+"_date_birth]'></div> </div></div>";
		$('.kids_row').append(str1);
		combo_set_premium_data();
		$("#kid_"+i+"_date_birth").datepicker({
		dateFormat: "dd-mm-yy",
		prevText: '<i class="fa fa-angle-left"></i>',
		nextText: '<i class="fa fa-angle-right"></i>',
		changeMonth: true,
		changeYear: true,
		maxDate: 0,
		yearRange: "-100: +0",
		onSelect: function (dateText, inst) {
		}
		});


		}}

		if(refresh == 1)
		{
		if($("#patTable tr").length > 0) {
			
		set_premium_const();
		$("#helathFamilyModal").modal('hide');
		
		}
		else{
		$("#helathFamilyModal").modal({backdrop: 'static'});
		$(".product_name").text(product_name);
		}
		}else{
		$("#helathFamilyModal").modal({backdrop: 'static'});
		$(".product_name").text(product_name);



		}


		}
		});



		}

		}
	

	
	
	$(document).on("click",".validateFamCon",function(){
	var cons = $('#patFamilyConstruct').val().split("+");

	if(cons[0] == '1A'){
	if($('input[name="adult_fr_id"]:checked').val() == undefined){
	swal("Alert","Please Select Adult","warning");
	return false;
	}
	}
	$('.ad_valid').each(function(i, obj) {
	if($(this).val() == '')
	{
	swal("Alert", 'Please fill details');

	return false;
	}


	var z = $(this).attr('id').split('_')[0]+"_rel";
	if(z == '' && z== undefined)
	{

	swal("Alert", 'Please fill details');
	//form.stopPropagation();
	return;
	}

	});
	});
		
		
		
    $("#premiumBif").on("click", function () {
        
        if ($("#premiumModalBody").html().trim()) {}
            $("#premiumModal").modal();
    });

    $("#vtlFamilyConstruct").on("change", function () {
		 var sumInsuresType = $("#sum_insures1 :selected").attr("data-type");
		 if(sumInsuresType == "combo_diff_construct")
		{
		 getPremiumByAge("vtlFamilyConstruct");
		}
    });

    $(document).on("change", "#sum_insures", function () {
		
		
		if(parent_id == 'RMxC5efb5c5a320e7'){
		
		annual_income_hide_show();}
        setFamilyConstruct();
		
        // $("#premium").val(tax_with_premium);
    });
    $(document).on("change", "#sum_insures1", function () {
        setvtlFamilyConstruct();
        // $("#premium").val(tax_with_premium);
    });

    $(document).on("change", "#sum_insure", function () {
	apply_button();
	var tax_with_premium = $("#sum_insure option:selected").data("id");
	var imd_status = get_imd_status();
	
	var PremiumServiceTax;
	if ($("#sum_insures :selected").attr("data-type") == "family_construct" || $("#sum_insures :selected").attr("data-type") == "family_construct_age" || $("#sum_insures :selected").attr("data-type") == "combo_diff_construct") {
	$.post("/get_family_construct", {
	"policyNo": $("#sum_insures :selected").attr("data-policyno"),
	"sumInsured": $("#sum_insures :selected").val()
	}, function (e) {
	$("#patFamilyConstruct").empty();
	$("#patFamilyConstruct").html('<option value="" selected>Select</option>');
	e = JSON.parse(e);
	$("#patFamilyConstructDiv").show();
	if (e) {
	e.forEach(function (e1) {
		console.log(e1.modal_age_cal);
		if(imd_status == 1)
		{
			PremiumServiceTax = e1.EW_PremiumServiceTax;
			
		}
		else
		{
			PremiumServiceTax = e1.PremiumServiceTax;
		}
	$("#patFamilyConstruct").append("<option value='" + e1.family_type + "' data-premium='" + PremiumServiceTax +"'>" + e1.family_type + "</option>");
	});
	}
	});
	} else {
	$("#patFamilyConstructDiv").hide();
	}

    });

    $(document).on("change", "#sum_insures1", function () {
        var tax_with_premium = $("#sum_insures1 option:selected").data("id");
        if ($("#sum_insures1 :selected").attr("data-type") == "family_construct" ||  $("#sum_insures1 :selected").attr("data-type") == "combo_diff_construct") {
		$.post("/get_family_construct", {
		"policyNo": $("#sum_insures1 :selected").attr("data-policyno"),
		"sumInsured": $("#sum_insures1 :selected").val()
		}, function (e) {
		$("#vtlFamilyConstruct").empty();
		e = JSON.parse(e);
		$("#vtlFamilyConstructDiv").show();
		if (e) {
			$("#vtlFamilyConstruct").html('<option value="" selected>Select</option>');
		e.forEach(function (e1) {
		$("#vtlFamilyConstruct").append("<option value='" + e1.family_type + "' data-premium='" + e1.PremiumServiceTax + "'>" + e1.family_type + "</option>");
		});
		$("#vtlFamilyConstruct").change();
		}
		});
        } else {
            $("#vtlFamilyConstructDiv").hide();
        }
    });
    /*onchnage event customer serach type*/
    $(document).on("change", "#cusDet", function () {
        var customer_serach_value = $("#cusDet option:selected").val();
        var subtype = $("#policySubType").val();
        if (customer_serach_value && subtype) {
            $("#customer_serach_button").show();
        } else {
            $("#customer_serach_button").hide();
        }
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
    // already existing child
    $(document).on("click", "#modal-submit", function () {
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

    /* date of birth*/
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
            get_age(dateText, eValue,selectChange);
        }
    });

    /* new child add*/
    $(document).on("click", "#add_new", function () {
        selectChange.find("#first_name").val("");
        selectChange.find("#last_name").val("");
        selectChange.find("#family_id").val("");
        selectChange.find("input[type='text'][name='family_date_birth']").val("");
        $("#myModal").modal("hide");
    });
    /*get date of spouse for set kids DOB*/
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

	$('body').on("keyup", "#comAdd", function() {
	if ($(this).val().match(/[{}"\\]/g)) {
		   $(this).val($(this).val().replace(/[{}"\\ ]/g, ""));
	   }
	});


	//updated by upendra on 24-05-2021
$(document).on("click",".rd_ghd_mem",function(){
    // alert('called');
    var radio_val = $(this).val();
    var radio_name = $(this).attr("name");
    var text_name = radio_name.replace("radio", "text");
    // alert(text_name);
	$(".ghd_span").hide();

    if(radio_val == 'yes'){
        $("."+text_name).show();
        $("."+text_name).prop('required',true);
    }else{
        $("."+text_name).hide();
        $("."+text_name).val('');
        $("."+text_name).prop('required',false);
    }


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
	//required: true
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
		swal("Alert", data.message, "success");
	var data_arr = data.data;

	$("#vtlForm").find("input[name='edit']").val("0");
	$("#sum_insures1").css("pointer-events", "none");
	$("#vtlFamilyConstruct").css("pointer-events", "none");
	$("#premium1").css("pointer-events", "none");
	var sum_insures1 = $("#sum_insures1").val();
	var vtlFamilyConstruct = $("#vtlFamilyConstruct").val();
	
	document.getElementById("vtlForm").reset();
	$("#sum_insures1").val(sum_insures1);
	$("#vtlFamilyConstruct").val(vtlFamilyConstruct);
	$("#premium1").val(data_arr[0].policy_mem_sum_premium);
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
	familyConstruct: {
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
	//required: true
	},
	family_date_birth: {
	required: true
	},
	age: {
	required: true
	},
	age_type: {
	required: true
	},
	annual_income: {
	required: true
	},
	},
	messages: {

	},
	submitHandler: function (form) {
		
	if (!Number($("#premium").val())) {
	swal("Alert", "invalid Premium", "warning");
	return;
	}

	if(!$("#emp_form_old").valid()) {
	ajaxindicatorstop();
	$("#salutation").focus();
	return;
	}

	if (!emp_id) {
	alert("Please add Customer Details First");
	return;
	}
		var annual_income = $('#annual_income').val();
	var sumInsuresValue = $("#sum_insures :selected").val();
	var isSumAssuredMoreThan = 1000000;
	if(sumInsuresValue > isSumAssuredMoreThan) 
	{
		//alert(is_self_purchased);
		if(is_self_purchased != 1){ // added if dedupe logic
			if(annual_income == '' || annual_income == 0)
			{
				swal("Alert", "Annual Income Cannot Be Blank", "warning");
				return;
			}
		}
	}
	var TableData = [];

	var LabelData = []; //          TableData = [];

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
	
	$("#sum_insures").prop('disabled',false);
	$("#family_members_id1").prop('disabled',false);
	$("#patFamilyConstruct").prop('disabled',false);
//	$("input[name='GCI_optional']").prop('disabled',false);
	var policyNo = $("#sum_insures :selected").attr("data-policyno");
	var product = $("#masPolicy option:selected").val();
	var premium_pat = $('#patFamilyConstruct option:selected').data('premium');
	var familyDataType = $("#sum_insures1 :selected").attr("data-type");
	var gender = $('#gender1').val();

	var occupation = $('#occupation').val();
	var gci_option = $('input[name="GCI_optional"]:checked').val();
	

	/*for R07 ask for confirmation if opted for gci policy*/
if(parent_id == 'RMxC5efb5c5a320e7'){
	var annual_check = annual_income_logic();
	if(!annual_check)
	{
		return;
	}

/*ask confirmation only if no member inserted and gci opted and global variable value false */
	/*if($("#patForm").find("table tbody").children().length == 0  && gci_confirmation == false){
		/*call swal and on click of ok set gci_confirmation to true*/
		/*   swal({
                    title: "Alert",
                    text: "Once the Member is submitted you cannot change the Plan and Cover.",
                    type: "warning",
                    confirmButtonText: "OK",
					showCancelButton: true,
                    closeOnConfirm: true,
                    allowOutsideClick: false,
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    dangerMode: true,
                    allowEscapeKey: false
                }, function() {
					//again submit form 
					gci_confirmation = true;
					$('#patForm').submit();
					//dfdf
				
				});
	
	}
	else{
		gci_confirmation = true
	}*/
}
/*if(parent_id == 'RMxC5efb5c5a320e7'){
if(!gci_confirmation){
return;
}
}*/

	//dedupe 
	if($('#family_members_id1').val() == 1){
		//alert(self_purcased_with_si +" <= "+$('#sum_insures').val())
		if(is_self_purchased == 1){
			if(parseInt(self_purcased_with_si) < parseInt($('#sum_insures').val())){
				swal("Alert", 'SI should be less or equal to '+self_purcased_with_si+' !', "warning");
				return false;
			}
		}
		
	}
	
    var form = $("#emp_form_old").serialize() +'&'+$("#patForm").serialize() + "&premium=" + premium_pat + "&policyNo=" + $("#sum_insures :selected").attr("data-policyno") + "&familyDataType=" + familyDataType + "&empId=" + emp_id + "&product=" + product + "&declare=" + JSON.stringify(TableData)+"&gender="+gender+"&gci_options="+gci_option;
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
	$("#sum_insures").attr('disabled', 'disabled');
	$("#patFamilyConstruct").attr('disabled', 'disabled');
	$("#premium").attr('disabled', 'disabled');
	
	
	//$("input[name='GCI_optional']").prop('disabled',true);
	 if ($("#sum_insures :selected").attr("data-type") == "combo_diff_construct")
	 {
		 
		 //get_premium_family_age();
		 }
		 var sum_insures = $("#sum_insures").val();
	
		var patFamilyConstruct = $("#patFamilyConstruct").val();
		var premium = $("#premium").val();
	document.getElementById("patForm").reset();
	
	$("#sum_insures").val(sum_insures);

	$("input[name='GCI_optional'][value='"+gci_option+"']").prop('checked',true);

	$("#patFamilyConstruct").val(patFamilyConstruct);
	$("#premium").val(premium);
	$("#occupation").val(occupation);
	if(parent_id == 'RMxC5efb5c5a320e7'){
		
	addDependentForm("getTable",JSON.parse(e));

	//updated by upendra on 24-05-2021 - for PED Update
if(product == "RMxC5efb5c5a320e7"){
    // alert("p r07");
    $.ajax({
        url: "/get_ped_table_bb",
        type: "POST",
        data: {emp_id: emp_id},
        async: false,
        success: function (response) {
            $("#hpi_ghd_td").html();
            $("#hpi_ghd_td").html(response);
        }
    });
}


	


	
	}
else{
	//ankita ped changes
	if(product == "iWL65dcd54d9c5a65"){
	    // alert("p r07");
	    $.ajax({
	        url: "/get_ped_table_bb",
	        type: "POST",
	        data: {emp_id: emp_id},
	        async: false,
	        success: function (response) {
	            $("#hpi_ghd_td").html();
	            $("#hpi_ghd_td").html(response);
	        }
	    });
	}
	addDependentForm("patTable", JSON.parse(e));
	}
	annual_income_hide_show();
	$('#annual_income').val(annual_income);
	//$("#annual_income").attr('readonly', 'readonly');
	});
	}
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

 $.validator.addMethod("valid_mobile", function(value, element, param) {
        if ($("#ISNRI").val() == 'N') {
            var re = new RegExp("^[6-9][0-9]{9}$");// cr change ankita allow number starting with 6, 7, 8 & 9 ankita 31-08-2021
        } else {
            var len = value.length;
            if (len < 10 && len > 20) {
                return false;
            }
            var re = new RegExp("^[0-9][0-9]{" + (len - 1) + "}$");
        }
        return this.optional(element) || re.test(value);
    }, "Enter a valid 10 digit mobile number");

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
	"validate_pincode",
	function (value, element, param) {
	var regs = /^\d{6}$/g;
	return this.optional(element) || regs.test(value); // Compare with regular expression
	},
	"Enter a valid Pin Code"
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
	count = 0;	//			return true;
	}else{
	count++;
	} 
	}
	});
	}
	if(count > 0){
	$("#pin_code").removeAttr("readonly"); 
	$('#city').val('');
	$('#state').val('');
	return false;}else{return true;}
	},
	"Pincode is unavailable in the pincode master. Please get in touch with ABHI Operations team to get the Pincode added in the master."
	);
    //isnri change
	$.validator.addMethod(
	"validate_comAdd",
	function (value, element, param) {
		// alert($("#ISNRI").val()+" =="+ valid_pincode_flag);
		if($("#ISNRI").val() != 'N' && is_domestic_pincode == 0){
			var addr = $('#comAdd').val();
			var json_addr = $('#hidden_address').val();
			//alert(addr+" =="+ json_addr);
			if(addr == json_addr){
				return false;
			}else{
				return true;
			}
		}else{
			return true;
		}
	},
	"change your address."
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
	"Please enter a valid Email ID. Correct Customer Email ID is mandatory to process further."
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
	
    /* edit emp data*/
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
	gender1: {
	required: true
	},
	comAdd: {
	required: true,
	validate_comAdd: true
	},
	pin_code:{
	required: true,
	validate_pincode: true,
	validate_postal_code:true
	},
	city:{
	required: true
	},
	state:{
	required: true
	},

	},
	messages: {
	email: {
	required: "Email ID is a mandatory field. Please fill correct customer email ID in the field to proceed.",
	},
	alt_email: "Please specify email id",
	alt_email: "please enter valid email",
	emg_contact_num: "Please specify contact number"
	},
	submitHandler: function (form) {
		
		/*isnri changes*/
		if($("#ISNRI").val() != 'N'){

			var count_res = 0;
			$.ajax({
				url: "/axis_pincode_get_state_city",
				type: "POST",
				async: false,
				data:{'pincode':$('#pin_code').val()},
				dataType: "json",
				success: function (response) {
					console.log(response)
					if(response && response.city && response.state) {
						
					 count_res = 0;	//			return true;
					}else{
					 count_res++;
					} 
				}
			});
			//alert("count = "+count_res);
			if(count_res == 0){
				var addr = $('#comAdd').val();
				var json_addr = $('#hidden_address').val();
				// alert(addr+" =="+ json_addr);
				if(addr == json_addr){
					swal("Alert","Change your address","warning");
				}else{
					var all_data = "emp_id="+ emp_id+"&comAdd="+$('#comAdd').val()+"&pin_code="+$('#pin_code').val()+"&city="+$("#city").val()+"&state="+$("#state").val();
					$.ajax({
						url: "/employee/update_emp_details",
						type: "POST",
						data: all_data,
						async: false,
						dataType: "json",
						success: function (response) {
							swal("Alert","Customer Details Updated Successfully !","warning");
						}
					});
					
				}
			}
				
		}
		/*isnri changes ends*/
	}
	});
        
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
	if (res.length != 0) {
	$("input[name='s_date']").val(res.family_dob);
	}
	}
	}
	});
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
	});
    $(".hide_ready").removeClass("hide");
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
function convertDate(inputFormat) {
   var b = inputFormat.split(/\D/);
   return b.reverse().join('-');
}
function get_age(dateStrings) {	
	var dateString = convertDate(dateStrings);
	var age_type;
	var today = new Date();
	var birthDate = new Date(dateString);
	var age = today.getFullYear() - birthDate.getFullYear() ;
	var m = today.getMonth() - birthDate.getMonth();
	if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
	age--;
	}
	if(age != 0)
	{
	age_type = " years";
	}else
	{
	var  oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
	var  firstDate = new Date();
	var  secondDate = new Date(dateString);

	var  diffDays = Math.round(Math.abs((firstDate - secondDate) / oneDay));
	age =  diffDays -1 ;
	age_type = "days";
	}
	selectChange.find('input[name="age"]').val(age);
	selectChange.find("input[name='age_type']").val(age_type);
	//$("#age1").val(age);
	//$("#age_type1").val(age_type);
}
function get_age_family(e, data) {
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
$("input[name='payment_modes']").change(function(){
      var payment_mods = $("input[name='payment_modes']:checked").val();
	  
	  if(payment_mods == 'Pay U')
	  {
		  $("#auto_renewal").prop('checked',false);
	  }
	  else{
		  $("#auto_renewal").prop('checked',true);
	  }
     
      });
$("#submitDoc").on("click", function () {
	var payment_mod = $("input[name='payment_modes']:checked").val();
	if(payment_mod == 'Easy Pay')
	{
    var count1 = saveDocuments();
	}
	else{
		var count1 = 0;
		
	}
    if (count1 == 0) {
        saveDocumentsAfterValidate();
    }
    else{
        ajaxindicatorstop();
    }
});

function saveDocuments() {
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
    if (ops_type.length > 0) {
        for (var i = 0; i < ops_path.length; ++i) {
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
	if (data == 0) {
	setTimeout(function () {
	ajaxindicatorstop();
	swal("Alert", "Please check documents uploaded, Supported documents PDF, PNG, JPEG & File Size should be less than 5MB");
	}, 500);
	return;
	}
	else {
	$.ajax({
	url:"/send_si_summary_link",//"/redirect_url_send",
	type: "POST",
	async: false,
	data: {emp_id: emp_id},
	success: function(response) {
	}
	});	
	swal({
	title: "Success",
	text: "Link sent to customer for proposal details authentication through OTP verification",
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
	//summary_detail();
$("#dummyFormThankYou").submit();

	});
	}
	}
	});
}
 
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
	payment_modes: {
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


	}
});

function saveBankDet() {
	var data = $("#bankDetForm").serialize() + "&ids=" + ids+ "&emp_id=" + emp_id ;;
	$.post("/bank_save_details", data, function (e) {
	e = JSON.parse(e);
	if (e.status == true) {
	ajaxindicatorstop();
	}
	else{
	ajaxindicatorstop();
	}
	})
}
function get_employee_lead_id() {
    $("#emp_idDummyForm").val(emp_id);
    $("#dummyForm").submit();
}


function delPopulate(e1) {
	
	var edit = $(e1).closest("form").find("input[name='edit']");
	console.log("edit", edit);
	var getTrClass;
	edit.val(0);
	$.post("/delete_member_new", {
	"emp_id": $(e1).attr("data-emp-id"),
	"policy_member_id": $(e1).attr("data-policy-member-id"),
	
	}, function (e) {
		//new flag
		if(e == 1){
			swal("Alert","Proposal is in process","warning");
			return;
		}
	if (!e) {
			debugger;
	$(e1).closest("form").find("select[name='family_members_id']").css("pointer-events",'auto')
	swal("Success", "Member Deleted Successfully", "success");
	getTrClass = $(e1).closest("tr").attr('class');
	if(parent_id == 'RMxC5efb5c5a320e7'){
	
		if(getTrClass == 'Self' || getTrClass == 'Spouse'){
		$("."+getTrClass).remove();
	}
	else{
	$(e1).closest("tr").remove();
	}
	apply_button();

	//updated by upendra on 24-05-2021 - PED logic
	$.ajax({
		url: "/get_ped_table_bb",
		type: "POST",
		data: {emp_id: emp_id},
		async: false,
		success: function (response) {
			$("#hpi_ghd_td").html();
			$("#hpi_ghd_td").html(response);
		}
	});
	}
	//ankita ped changes
	if(parent_id == 'iWL65dcd54d9c5a65'){
		
		$.ajax({
			url: "/get_ped_table_bb",
			type: "POST",
			data: {emp_id: emp_id},
			async: false,
			success: function (response) {
				$("#hpi_ghd_td").html();
				$("#hpi_ghd_td").html(response);
			}
		});
	}
	
	$(e1).closest("tr").remove();
	$(".quest_declare_benifit_4s").empty();
	$('#chronic th input[type="checkbox"]').prop('checked',false);
	
	if($("#patTable tr").length == 0) {
		
	$("#sum_insures").val('');
	$("#premiumgpa").val('');
	$('#premiumgci').val('');
	$("#TotalPremium").html('');
	$("#patFamilyConstruct").val('');
	$("#premium").val('');
	$("#premiumModalBody").html('');
	$("#premiumBif").css("pointer-events","none");
	$("#sum_insures").removeAttr('disabled', 'disabled');
	$("#annual_income").removeAttr('readonly', 'readonly');
	$("#annual_income").val('');
	//$("input[name='GCI_optional']").prop('disabled',false);
	gci_confirmation = false;
	$("#patFamilyConstruct").removeAttr('disabled', 'disabled');
	}
	
	
	
	if($("#vtlTable tr").length == 0) {
	$("#sum_insures1").val('');
	$("#vtlFamilyConstruct").val('');
	$("#premium1").val('');
	//$("#premiumModalBody").html('');
	//$("#premiumBif").css("pointer-events","none");
	$("#sum_insures1").css("pointer-events","auto");
	$("#vtlFamilyConstruct").css("pointer-events","auto");
	$("#sum_insures1").removeAttr('disabled', 'disabled');
	$("#vtlFamilyConstruct").removeAttr('disabled', 'disabled');
	}
	}
	});
	
	get_Table_data_show_hide();
}
function get_card_mask(str) {


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
	if(code != ''){ //ankita single link journey
		$.post('/getIfscCode', {
			'ifsc_code': code
		}, function (e) {
			var obj = JSON.parse(e);
			if(obj == null){
				swal("Alert","Enter valid IFSC Code","warning");
				$("#ifscCode").val('');
			}else if(obj.bank_name.toLowerCase() == "axis bank"){
				$('#bankName').val(obj.bank_name);
				getBankCity(obj.bank_name, obj.bank_city);
				getBankBranch(obj.bank_city, obj.bank_branch);

				$('#ifscCode').val(obj.ifsc_code);
			}else{
				swal("Alert","Enter Axis Bank IFSC Code","warning");
				$("#ifscCode").val('');
			}
			
		});
	}
	
}
$(document).ready(function () {
	//ankita single journey
	if(is_non_integrated_single_journey == 1){
		$("#Pay_U").trigger("click");
		$('#Pay_U').parent().css('float','left');
		$("#Easy_Pay").parent().hide();
	}
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
	$(".quest_declare_benifit_4s").append('<div id =di'+sub_type_id+'>'+response+'</div>');
	}
	});
	}
	else{
	$("#di"+sub_type_id).remove();
	}
});

function get_fam_data(elem) {
	$.ajax({
	url: "/home/get_family_details_from_relationship",
	type: "POST",
	data: {
	relation_id: elem.value,
	emp_id: emp_id,
	"nominee": 'nominee'
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

function submitNominee() {
	//alert('nominee function called');
	var family_members_idArray = {};
	var total = 0;
	var status = true;
	ajaxindicatorstart("Please wait...");
	$('#policy_nominee_form_data .form_row_data1').each(function (key, value) {
		//debugger;
	total += Number($(this).find('.share_per').val());
	if ($(this).find('.nominee_relation').val() == '') {
	if($('.test').length == 0){
	ajaxindicatorstop();
	($(this).find('.nominee_relation')).after("<span style='color:red;' class='test'>family member is required</span>");
	}
	status = false;
	}
	if ($(this).find('.nominee_fname').val().trim() == '') {
	if($('.test1').length == 0){
	ajaxindicatorstop();
	($(this).find('.nominee_fname')).after("<span style='color:red;' class='test1'>First name is required</span>");
	}
	status = false;
	}
	if ($(this).find('.nominee_lname').val().trim() == '') {
	if($('.test2').length == 0){
	ajaxindicatorstop();
	($(this).find('.nominee_lname')).after("<span style='color:red;' class='test2'>Last name is required</span>");
	}
	status = false;
	}
	if ($(this).find('.nominee_contact').val() == '') {
	if($('.test3').length == 0){
	ajaxindicatorstop();
	($(this).find('.nominee_contact')).after("<span style='color:red;' class='test3'>Contact No is required</span>");

	}
	status = false;
	}
	if ($(this).find('.share_per').val() == '') {
	if($('.test4').length == 0){
	ajaxindicatorstop();
	($(this).find('.share_per')).after("<span style='color:red;' class='test4'>Share is required</span>");
	}
	status = false;
	}
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




	//updated by upendra on 24-05-2021 - PED logic
	
	var sr_count = 0;
	var check_arr = [];
	
	$('.count_sr_ghd_infi').each(function (key, value) {
		var radio_value = $("input[type='radio'][name='ghd_mem_radio_"+sr_count+"']:checked").val();

		if(radio_value == "yes"){

			var ghd_text = $("#ghd_mem_text_"+sr_count).val().trim();
			if(ghd_text == ""){
				($(this).find('#ghd_mem_text_'+sr_count)).next('span').remove();
				($(this).find('#ghd_mem_text_'+sr_count)).after("<span style='color:red;' class='test4 ghd_span'>Remark is required</span>");
				check_arr.push("error");
			}else{
				($(this).find('#ghd_mem_text_'+sr_count)).next('span').remove();
			}
			
		}
		
		sr_count++;
	})

	if(jQuery.inArray("error", check_arr) !== -1) {
		ajaxindicatorstop();
		return;
	}


    //debugger;
	var gender = $('#gender1').val();
	var all_data = $("#emp_form_old").serialize() + "&emp_id="+ emp_id+"&gender="+gender+ "&declare=" + JSON.stringify(family_members_idArray);
	$.post("/employee/add_nominee", all_data, function (e) 
	{
		var data = JSON.parse(e);

		//debugger;
	var response = JSON.parse(e);
	if (response.msg == true) {
	var update_declare_id = $('#update_declare_id').val();
	var TableData = [];
	var LabelData = []; 
	$('#mydatas tr').each(function (row, tr) {
	var LabelData = {};
	var label_content;
	var tds = $(tr).find('td:eq(0)').text();
	var label = $(tr).find('.label_id').text();
	var content = $(tr).find('.mycontent').val();
	var mylabel = $(tr).find('.mycontents').val();
	var myremark = $(tr).find('.myremark').val();
	var mylabval = $(tr).find('.mylabval').val();
	if (label != '') {
		LabelData = mylabel + ':' + mylabval;
	} else {
		LabelData = mylabval;
	}

	//updated by upendra on 24-05-2021 - for PED update
	if($("#masPolicy option:selected").val() == "RMxC5efb5c5a320e7" || $("#masPolicy option:selected").val() == "iWL65dcd54d9c5a65"){//ankita ped changes
		var remarks = '';
		var select_mem_length=$('.count_sr_ghd_infi').length;
		var remark_arr = [];
		
		
		for(var lp=0;lp<select_mem_length;lp++){
			var ghd_hfi_select_val = $("input[type='radio'][name='ghd_mem_radio_"+lp+"']:checked").attr("data");
			var ghd_hfi_relation_code = $("input[type='radio'][name='ghd_mem_radio_"+lp+"']:checked").attr("data-rc");
			var ghd_hfi_select_val_ans = $("input[type='radio'][name='ghd_mem_radio_"+lp+"']:checked").val();
			var ghd_hfi_text_val = $("#ghd_mem_text_"+lp).val();
			remark_arr.push({
				member : ghd_hfi_select_val, 
				ans : ghd_hfi_select_val_ans, 
				remark : ghd_hfi_text_val,
				relation_code : ghd_hfi_relation_code
			});
		}
	
		remarks = 	JSON.stringify(remark_arr);
		remarks =   remarks.replace(/"/g, '\\"');
		myremark = remarks;
}
	// alert(myremark);
	TableData[row] = {
		"question": content,
		"remark": myremark,
		"format": $("#mydatas input[type='radio']:checked").val(),
		"label": LabelData
	}
	});
	//alert('calling ajax');
	//debugger;
	$.ajax({
	url: "/aprove_status",
	type: "POST",
	async: false,
	dataType: "json",
	data: {
		"policy_no": $("#masPolicy option:selected").text(),
		"payment_mod": $("input[name='payment_modes']:checked").val(),
		"emp_id": emp_id,
		"declare": TableData,
		"update_declare_id": update_declare_id,
		"family_construct": $("#patFamilyConstruct :selected").val(),
		"policyNo" : $("#sum_insures :selected").attr("data-policyno")
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
				function () {  
				}); return;
		}
		if (response.status == true) {
			ids = response.proposal_ids;
			saveBankDet();
			$('#submitDoc').trigger("click");
			
		}
	}
	});
	$("#finalConfirmDiv").show();

	/* get_nominee_data();*/

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
	   
	});
	}

	});
}

$('#submit_nominee').on("click", function () {
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
	status = false;
	}
	if ($(this).find('.nominee_fname').val().trim() == '') {
	if($('.test1').length == 0){
	ajaxindicatorstop();
	($(this).find('.nominee_fname')).after("<span style='color:red;' class='test1'>First name is required</span>");
	}
	status = false;
	}
	if ($(this).find('.nominee_lname').val().trim() == '') {
	if($('.test2').length == 0){
	ajaxindicatorstop();
	($(this).find('.nominee_lname')).after("<span style='color:red;' class='test2'>Last name is required</span>");
	}
	status = false;
	}
	if ($(this).find('.nominee_contact').val() == '') {
	if($('.test3').length == 0){
	ajaxindicatorstop();
	($(this).find('.nominee_contact')).after("<span style='color:red;' class='test3'>Contact No is required</span>");

	}
	status = false;
	}
	if ($(this).find('.share_per').val() == '') {
	if($('.test4').length == 0){
	ajaxindicatorstop();
	($(this).find('.share_per')).after("<span style='color:red;' class='test4'>Share is required</span>");
	}
	status = false;
	}
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
	var payment_mod = $("input[name='payment_modes']:checked").val();
	if(payment_mod == 'Easy Pay')
	{
	var count1 = saveDocuments();
	}
	else
	{
	var count1 = 0 ;
	}
	if (count1 == 0) {

	} else {
		ajaxindicatorstop();
		update_flag();
		swal("Alert", "Please add documents", 'warning');
		return;
	}
	if (status) {
		//debugger;
		validate_proposal();
		//otp_generate();
	}else {
		update_flag();
		ajaxindicatorstop();
	}
	}
});

//new flag
function update_flag(){
	var	data = "emp_id=" + emp_id ;
	$.ajax({
		url: "/employee/update_flag",
		type: "POST",
		async: false,
		dataType: "json",
		data:data,
		success: function (response) {
		}
	});
}

function validate_proposal(){


	var policy_no  = $("#masPolicy option:selected").text();
	var gender = $('#gender1').val();	
	var family_construct = $("#patFamilyConstruct :selected").val();
	var policyNo = $("#sum_insures :selected").attr("data-policyno");
	var occupation = $('#occupation').val();
	var	data = $('#emp_form_old').serialize() + "&policy_no=" + policy_no+ "&emp_id=" + emp_id + "&family_construct=" + family_construct+ "&policyNo=" + policyNo+"&gender=" + gender+"&occupation="+ occupation;
	$.ajax({
		url: "/validate_proposal",
		type: "POST",
		async: false,
		dataType: "json",
		data:data,
	success: function (response) {

		if (response.status == false) {
			//alert("error");
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
				}); return;
		}
		if (response.status == true) {
			validate_upload_data();
			//ids = response.proposal_ids;
			//otp_generate();
			
			
		}
	}
	});	
}
function validate_upload_data()
{
	var ops_type = document.getElementsByName("ops_type");
    var ops_path = document.getElementsByName("ops_path");
    var ops_type_value = [];
    var ops_path_value = [];
    var data = new FormData();
    if (ops_type.length > 0) {
        for (var i = 0; i < ops_path.length; ++i) {
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
    }
    
	$.ajax({
	type: "POST",
	url: "/validate_upload_data",
	data: data,
	processData: false,
	contentType: false,
	cache: false,
	mimeType: "multipart/form-data",
	success: function (data) {
	if (data == 0) {
	setTimeout(function () {
	ajaxindicatorstop();
	swal("Alert", "Please check documents uploaded, Supported documents PDF, PNG, JPEG & File Size should be less than 5MB");
	}, 500);
	return;
	}
	else {
submitNominee();
		//otp_generate();
	}
	}
	});
}
function summary_detail()
{
	$("#myModalsummary").modal("hide");
	$.ajax({
	url: "/summary",
	type: "POST",
	data: {
	emp_id: emp_id,
	parent_id : parent_id,
	},
	async: false,
	dataType: "json",
	success: function (response) {
	//alert(data);

	$("#myModalsummary").modal("show");
	$('#cust_summary').html(response);

	}
	});	
}
$(document).on("click", "#summary_ids", function (e) {
	get_employee_lead_id();
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
	if(!$("#emp_form_old").valid()) {
	ajaxindicatorstop();
	$("#salutation").focus();
	return;
	}
	/*isnri changes starts*/
	/*if($("#ISNRI").val() != 'N'){	
		var count = 0;
		$.ajax({
			url: "/axis_pincode_get_state_city",
			type: "POST",
			async: false,
			data:{'pincode':$('#pin_code').val()},
			dataType: "json",
			success: function (response) {
				if(response && response.city && response.state) {
				 count = 0;	//return true;
				}else{
				 count++;
				} 
			}
		});

		if(count > 0){		
			var addr = $('#comAdd').val();
			var json_addr = $('#hidden_address').val();
			if(addr == json_addr){
				swal("Alert","Change your address & pincode","warning");
				return false;
			}
		}
	}*/
	/*isnri changes ends*/
	var mydeclare = $("#mydatas input[type='radio']:checked").val();
	if (mydeclare == 'Yes') {
	ajaxindicatorstop();
	swal("Alert","As the Good Health Declaration Question is YES, the proposal cannot be processed", "warning");	
	$("#myModal1").modal("hide");
	$("#sms_body").html('');
	return false;
	}
	var tmp = null;
	$.ajax({
	url: "/employee/get_imd",
	type: "POST",
	data: {
	"parent_id": parent_id,
	"emp_id": emp_id,
	"update_flag" : 1
	},
	async:false,
	success: function (data) {
	tmp = data;
	}
	});
	if(tmp == 0 && is_non_integrated_single_journey == 0) //single link journey - 28-9-21
	{
	ajaxindicatorstop();
	swal("Alert", "IMD code is not created for this branch. Please share IMD code creation request with ABHI team to process further");
	return false;
	}
	else
	{

	}
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
			 if (member.declare_sub_type_id) {
				$("#subdeclare_" + member.declare_sub_type_id).prop("checked", true);
			}
			else {

				$("#subdeclare_" + member.declare_sub_type_id + "_1").prop("checked", true);
			}

		}
	});

}

$(document).on("click", "#auto_renewal", function (e) {
    if (this.checked) {
        $("#renewals").show();
	 $("#renewals").closest('td').show();
	 $("#renewals").removeClass('ignore');
    }

    else {
	$("#renewals").closest('td').hide();
        $("#renewals").hide();
        $('#auto_renewal').val('');
	 $("#renewals").addClass('ignore');
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
	$("#pos_otp").val("");
	}
	});	
});
$(document).on("click", "#resend_otp", function() {
	otp_generate();
});

$(document).on("click", "#autorenwals", function() {
	ajaxindicatorstart("Downloading...");
	var premium = $("#premium").val();
	$.ajax({
	url: "/autorenewal",
	type: "POST",
	data: {
	'emp_id': emp_id,
	'parent_id' : parent_id,
	'premium' : premium
	},
	dataType:'html',
	success: function (response) {
	html2pdf()
	.set({  filename: 'SampleSI.pdf'})
	.from(response)
	.save();
	setTimeout(function(){ ajaxindicatorstop(); }, 5000);
        }
    });
});

$('#salutation').click(function(){
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

/*pincode*/
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

});

/*combo Family Construct And Age R07*/
$("input[name='GCI_optional']").click(function(){
//$('#patFamilyConstruct').val('');
	// $('#premium').val('');
	// $("#premiumModalBody").html('');
	 add_gci_name();
	apply_button_gci();
	
});
function getPremiumconstByAge(id) 
{
	$(".ti-eye").show();
	$("#premiumBif").css("pointer-events","auto");
	var familyConstruct = $("#patFamilyConstruct").val();
	var sumInsuresValue = $("#sum_insures :selected").val();
	var isSumAssuredMoreThan = 1500000;
	/*if(sumInsuresValue > isSumAssuredMoreThan) 
	{
	$("#annual_income_label").show();
	$("#annual_income").removeClass('ignore');
	$("#getPremiumConstAgeLabel").html('Enter Member Age AND Annual Income');
	if(familyConstruct == '1A')
	{ 
	$("#getPremiumConstAgeLabel").html('Enter Member Age AND Annual Income');
	}
	else		 
	{
	$("#getPremiumConstAgeLabel").html('Enter Age of Eldest Member & Annual Income');
	}
	}
	else
	{
	$("#annual_income_label").hide();
	$("#annual_income").addClass('ignore');
	if(familyConstruct == '1A')
	{ 
	$("#getPremiumConstAgeLabel").html('Enter Member Age');
	}
	else		 
	{
	$("#getPremiumConstAgeLabel").html('Enter Age of Eldest Member');
	}
	}*/
	$("#premiumBif").css("pointer-events","none");
	$(".ti-eye").hide();
	var edit = $("#"+id).closest("form").find("input[name='edit']").val();
	$("#getPremiumConstAge").val("");
	// if($("#refreshEdit").val() == "0") {
	// $("#refreshEdit").val("1");
	// return;
	// }
	var gci_option = $("input[name='GCI_optional']:checked").val();
	if(edit == 0 || edit == "")
	$('.memberage_modal').html('');
if(familyConstruct != '')
	{
	
/*if(gci_option == 'No' && sumInsuresValue < isSumAssuredMoreThan)
{	
$(".ti-eye").show();
	$("#premiumBif").css("pointer-events","auto");
$("#age_const").hide();
	$("#getPremiumConstAgeModal").modal('hide');
	$("#getPremiumConstAge").addClass('ignore');
	
	$('#getPremiumConstAgeForm').submit();
}
if(gci_option == 'No' && sumInsuresValue >= isSumAssuredMoreThan)
{	$("#age_const").hide();
	$("#getPremiumConstAge").addClass('ignore');
	$("#annual_income_label").show();
	
	$("#getPremiumConstAgeModal").modal();
}
if(gci_option == 'Yes' && sumInsuresValue >= isSumAssuredMoreThan)
{
	$("#age_const").show();
	$("#getPremiumConstAge").removeClass('ignore');
	$("#getPremiumConstAgeModal").modal();
	$("#annual_income_label").show();
}*/

if(gci_option == 'Yes')
{
	$("#age_const").show();
	$("#getPremiumConstAge").removeClass('ignore');
	$("#getPremiumConstAgeModal").modal();
}
else
{
		$("#getPremiumConstAge").addClass('ignore');
	$("#getPremiumConstAgeForm").submit();

}	
	}
	else
	{
		$('#premium').val('');
	}
}





function getPremiumconstByAge_indivisual(id) 
{
	$(".ti-eye").show();
	$("#premiumBif").css("pointer-events","auto");
	var familyConstruct = $("#patFamilyConstruct").val();
	var sumInsuresValue = $("#sum_insures :selected").val();
	var annual_income = $("#annual_income").val();
	var isSumAssuredMoreThan = 1000000;
	 if(sumInsuresValue > isSumAssuredMoreThan) 
	{
	$("#annual_income_label").show();
	$("#annual_income").removeClass('ignore');
	/*$("#getPremiumConstAgeLabel").html('Enter Member Age AND Annual Income');*/
	$("#getPremiumConstAgeLabel").html('Enter Annual Income');
	if(familyConstruct == '1A')
	{ 
	/*$("#getPremiumConstAgeLabel").html('Enter Member Age AND Annual Income');*/
	$("#getPremiumConstAgeLabel").html('Enter  Annual Income');
	}
	else		 
	{
	$("#getPremiumConstAgeLabel").html('Enter Annual Income');
	}
	}
	else
	{
	$("#annual_income_label").hide();
	$("#annual_income").addClass('ignore');
	
	/*if(familyConstruct == '1A')
	{ 
	$("#getPremiumConstAgeLabel").html('Enter Member Age');
	}
	else		 
	{
	$("#getPremiumConstAgeLabel").html('Enter Age of Eldest Member');
	} */
	} 
	$("#premiumBif").css("pointer-events","none");
	$(".ti-eye").hide();
	var edit = $("#"+id).closest("form").find("input[name='edit']").val();
	$("#getPremiumConstAge").val("");
	if($("#refreshEdit").val() == "0") {
	$("#refreshEdit").val("1");
	return;
	}

if(edit == 0 || edit == "")
	{
	$('.memberage_modal').html('');
	if(sumInsuresValue > isSumAssuredMoreThan && annual_income == ''){
		$("#getPremiumConstAgeModal").modal();
	}
	
	if(annual_income!='')
	{
		annual_income_logic();
	}
	}
	
}
$("#getPremiumConstAgeForm").validate({
	ignore: ".ignore",
	rules: {
	getPremiumConstAge: {
	required: true,
	number: true
	},
	
	},
	messages: {

	},
	submitHandler: function (form) {
	var policyNo = $("#sum_insures :selected").attr("data-policyno");
	var sum_insures = $("#sum_insures").val();
	var familyConstruct = $("#patFamilyConstruct").val();
	var maxPremiumAge = $("#getPremiumConstAge").val();
	var gci_option = $("input[name='GCI_optional']:checked").val();
	$.post("/get_premium_construct_age", { "policyNo": policyNo, "sum_insures": sum_insures, "familyConstruct": familyConstruct,"gci_option":gci_option,"maxPremiumAge": maxPremiumAge,"emp_id":emp_id }, function (e) {
	e = JSON.parse(e);
	if(e.status == false)
	{
	if($('#getPremiumConstAge').val() != '')
	{
	if($('.memberage_modal').length == 0){

	$('#getPremiumConstAge').after("<span style='color:red;' class='memberage_modal'>"+e.message+"</span>");
	}
	}
	}
	else
	{
	$("#premiumModalHidden").val(e);
	$(".ti-eye").show();
	$("#premiumBif").css("pointer-events","auto");
	var premium = 0;
	var PremiumServiceTax;
	$("#premiumModalBody").html("");
	es = e.premium;
	es.forEach(function (e1) {
	$('#getPremiumConstAgeModal').modal('hide');
	if(e1.ew_status == 1)
	{
	PremiumServiceTax = e1.EW_PremiumServiceTax;
	}
	else
	{
	PremiumServiceTax = e1.PremiumServiceTax;
	}
	str = "<div style='display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;'><div><span style='color:#da8085;'> Name:  " + "<br/></span><span style='color:#000;'>" + e1.policy_sub_type_id + "</span></div>";
	str += "<div><span style='color:#da8085;'> Sum Insured </span>" + "<br/><span style='color:#000;'>" + e1.sum_insured + "</span></div>";
	str += "<div><span style='color:#da8085;'> Premium" + "</span><br/><span style='color:#000;'>" + parseFloat(PremiumServiceTax).toFixed(2) + "</span></div></div>";
	$("#premiumModalBody").append(str);
	premium += parseFloat(PremiumServiceTax);
	});
	$("#premium").val(premium.toFixed(2));
	localStorage.setItem('premium_store',premium.toFixed(2));
	//Annual Income Code
//	annual_income_logic();
	}
	})
	}
});

$("#getPremiumConstAgeForm1").validate({
	ignore: ".ignore",
	rules: {
	/*getPremiumConstAge: {
	required: true,
	number: true
	},*/
	anuual_income: {
	required: true,
	number: true
	},
	},
	messages: {

	},
	submitHandler: function (form) {
		annual_income_logic();
		//getPremium();
}
});
/*R07*/
function annual_income_logic()
{
	//dedupe if self already purchased remove validation
	if(is_self_purchased == 1){
		return true;
	}
	
	//Annual Income Code
	var dropdown_sumIsured;
	var annual_income = $("#annual_income").val();
	var sumInsuresValue = $("#sum_insures :selected").val();
	var ComparesumInsures = $("#sum_insures").val();
	var IncomeSum = annual_income * 8;
	var sumInsuresValues = (sumInsuresValue/8);
	var count = 0;
	if(sumInsuresValue > 1000000)
	{
		if(annual_income == '' && annual_income == 0)
		{
			swal("Alert", "Annual Income Cannot Be blank");
		count = 1;
	// $('#sum_insures').val('');
	// $('#patFamilyConstruct').val('');
	// $('#premium').val('');
		return false;
		}
	//code change
	$("#sum_insures option").filter(function(e1)
	{
	dropdown_sumIsured = $(this).val();
	//show all sum insured which are eligible as per annual income and hide which are not eligible
	if(dropdown_sumIsured <=  IncomeSum){						
	//$("#sum_insures option[value='" + dropdown_sumIsured + "']").removeAttr("style", "display:none");
	}else{
	//$("#sum_insures option[value='" + dropdown_sumIsured + "']").attr("style", "display:none");
	}
	if(IncomeSum == sumInsuresValue ){
	//$("#sum_insures option[value='500000']").removeAttr("style", "display:none");
	//$("#sum_insures option[value='1000000']").removeAttr("style", "display:none");
	}
	if(IncomeSum < sumInsuresValue ){
	
	swal("Alert", "Request to select SI lesser than opted");
	count = 1;
	// $('#sum_insures').val('');
	// $('#patFamilyConstruct').val('');
	// $('#premium').val('');
	return false;
	$("#premiumModalBody").html('');
	//$("#sum_insures option[value='500000']").removeAttr("style", "display:none");
	//$("#sum_insures option[value='1000000']").removeAttr("style", "display:none");
	}
	});

	// if(annual_income!='')
	// {	//update annual income in database
		//3 parameter is for insert or fetch if true then insert else fetch
		// if($("#patForm").find("table tbody").children().length == 0)
		// {
				// insert_annual_income(emp_id,annual_income,true);
		// }
		// else
		// {
		// }
		// insert_annual_income(emp_id,annual_income,true);
		//$("#annual_income").attr('readonly','readonly');}
	
			//$("#getPremiumConstAgeModal").modal('hide');
}
if(count == 1)
{
	return false;
}
else
{
	insert_annual_income(emp_id,annual_income,true);
	return true;
}
	

}
function editPopulate(e1) {
	var emp_edit_id = $(e1).attr("data-emp-id");
	var emp_edit_policy_mem = $(e1).attr("data-policy-member-id");
	$.post("/get_edit_member", {
	"emp_id": $(e1).attr("data-emp-id"),
	"policy_member_id": $(e1).attr("data-policy-member-id"),
	}, function (e) {
	e = JSON.parse(e);
	var tableid = $(e1).attr("data-tableid");
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
	familyConstruct.change();
	declarepopoulate(emp_edit_id, emp_edit_policy_mem);
	setvtlFamilyConstruct(e.familyConstruct);
	setFamilyConstruct(e.familyConstruct);
	familyConstruct.css("pointer-events", "none");
	get_Table_data_show_hide();
	});
	
}
/*R07*/
function get_Table_data_show_hide()
{
	if(parent_id == 'RMxC5efb5c5a320e7'){
		enableSumFamily();
	if($("#patTable tr").length > 0)
		{
			$("#appybutton").hide();
		}
		else
		{
			$("#appybutton").hide();
		}	
		}
}
/*R07*/
function enableSumFamily()
{
$("#patFamilyConstruct").css('pointer-events','auto');
	$("#sum_insures").removeAttr('disabled', 'disabled');
		$("#patFamilyConstruct").removeAttr('disabled', 'disabled');
	$("#annual_income").removeAttr('readonly', 'readonly');

	//$("input[name='GCI_optional']").prop('disabled',false);
	$("#patFamilyConstruct").removeAttr('disabled', 'disabled');
}
/*R07*/
function apply_button() {

	if(parent_id == 'RMxC5efb5c5a320e7'){
	var edit = $("#patFamilyConstruct").closest("form").find("input[name='edit']").val();
	if(edit !=0)
	{
		return;
	}
	if($("#patTable tr").length > 0) {
		var GCI_optional;
	var sumInsuresValue = $("#sum_insures :selected").val();
	var annualIncome = $("#annual_income").val();
	var patFamilyConstruct = $("#patFamilyConstruct").val();
	var sum_annual;
	

	if(sumInsuresValue == '')
	{		
		swal("Alert", "Please select Sum Insure");
		$("#patFamilyConstruct").val('');
		return;
	}
	if(sumInsuresValue >= 1500000){
		
		if(annualIncome != '' && annualIncome != 0)
		{
			sum_annual = annual_income_logic();
			if(sum_annual == false){
				return;
			} 
		}else
		{
			//dedupe
			if(is_self_purchased == 0){
				swal("Alert", "please select annual Income");return;
			}
		}
	}else{
		annual_income_hide_show();
	}
	if(patFamilyConstruct == '')
	{		
		swal("Alert", "Please select Family Construct");
		return;
	}
	insert_annual_income(emp_id,annualIncome,'insert');
	$.ajax({
	url: "/employee/apply_changes",
	type: "POST",
	data: {
	'parent_id':parent_id,
	'family_construct':$("#patFamilyConstruct").val(),
	'sum_insured':$("#sum_insures").val(),
	'gci' : $('input[name="GCI_optional"]:checked').val(),
	'emp_id' : emp_id
	},
	async: false,
	dataType: "json",
	success: function (response) {
	
	
	if(apply_onload > 1)
	{
		swal("Alert",response.message);
	}
	else
	{apply_onload++;}

addDependentForm("getTable",response);
	
	
	}
	});	
		}
}
}
/*R07*/
function apply_button_gci() {
	if($("#patTable tr").length > 0) {
	var GCI_optional;
	var sumInsuresValue = $("#sum_insures :selected").val();
	if(sumInsuresValue == '')
	{		
		swal("Alert", "Please select Sum Insure");
		return;
	}	
	$.ajax({
	url: "/employee/apply_changes",
	type: "POST",
	data: {
	'parent_id':parent_id,
	'family_construct':$("#patFamilyConstruct").val(),
	'sum_insured':$("#sum_insures").val(),
	'gci' : $('input[name="GCI_optional"]:checked').val(),
	'only_gci':'Yes',
	'emp_id' : emp_id
	},
	async: false,
	dataType: "json",
	success: function (response) {
	addDependentForm("getTable",response);
	}
	});	
	}
}
/*R07*/
$("#annual_income").on('blur', function() {
	if($("#patTable tr").length > 0)
		{
	apply_button();
		}
});

	$(document).on("click","input[name='adult_fr_id']",function() {

	var cons = $("#patFamilyConstruct").val().split("+");
	if(cons[0] == '1A'){
	if($('input[name="adult_fr_id"]:checked').val() != 0){
	var fr_id = $('input[name="adult_fr_id"]:checked').val();

	var fr_name = $('input[name="adult_fr_id"]:checked').next('label').text();

	$('.adult_row').html('');
	other_than_self_dropdown(fr_id,fr_name);
	$(".adult_row").removeAttr("style", "display:none");
	}else{
	$('.adult_row').attr("style", "display:none");
	}

	}else{
	//$(".check_adult").Attr("style", "display:none");
	}
	});
	function other_than_self_dropdown(fr_id,fr_name)
	{

	str = "<div class='mb-2 col-md-12 row spouse_div'><div class='col-md-6'><div class='form-group'><label class='col-form-label'>Relation With Proposer<span style='color:#FF0000'>*</span></label> <select class='form-control ' name='rel_name["+fr_name+"]["+fr_name+"_rel]' id='"+fr_name+"_rel'><option data-opt='null' value='"+fr_id+"'>"+fr_name+"</option></select> </div> </div> <div class='col-md-6'> <label class='col-form-label'>Date of Birth<span style='color:#FF0000'>*</span></label> <input class='form-control "+fr_name+"_date_birth modal_dob  ad_valid' autocomplete='off' type='text'  id='"+fr_name+"_date_birth' name=rel_name["+fr_name+"]["+fr_name+"_datebirth]' ><input type ='hidden' name = 'rel_name["+fr_name+"]["+fr_name+"_adult]' value ='A'></div> </div></div>";
	$('.adult_row').append(str);
	$("#"+fr_name+"_date_birth").datepicker({
	dateFormat: "dd-mm-yy",
	prevText: '<i class="fa fa-angle-left"></i>',
	nextText: '<i class="fa fa-angle-right"></i>',
	changeMonth: true,
	changeYear: true,
	maxDate: 0,
	yearRange: "-100: +0",
	onSelect: function (dateText, inst) {
	}
	});
	combo_set_premium_data();
	}

	$("#famConSelected1").validate({
	ignore: ".ignore",
	rules: {

	},
	messages: {

	},
	submitHandler: function (form) {



	//alert($("#patFamilyConstruct").val());
	var form = $("#famConSelected1").serialize() + "&empId=" + emp_id+"&family_construct="+$("#patFamilyConstruct").val()+"&policy_detail_id="+ $("#sum_insures :selected").attr("data-policyno")+"&sumInsured="+ $("#sum_insures :selected").val();
	$.post("/employee/familyconst_dob", form, function (e) {
	e = JSON.parse(e);

	if(e.message != 'true'){ //error in validation
	swal("Alert", e.message);
	}else{
	var getPremium = e.get_premium;
	if(getPremium){
	get_age_wise_premium(getPremium);
	}


	var opt = e.memArr;
	var rel_allowed = e.rel_allowed;



	console.log(rel_allowed);

	$.each(rel_allowed, function( index, value ) {
	if(opt.toString().indexOf(value) == -1){
	console.log(opt);
	//remove from relation with proposer dropdown
	$("#family_members_id1 option[value='"+value+"']").hide();
	}else{
	console.log(opt);
	$("#family_members_id1 option[value='"+value+"']").show();
	}
	});
	}
	});
	}
	});
	function get_age_wise_premium(e){
	console.log(e);
	$("#helathFamilyModal").modal('hide');
	var premium = 0;
	$("#premiumModalBody").html("");
	var PremiumServiceTax;
	e.forEach(function (e1) {


	PremiumServiceTax = e1.premium;

	str = "<div style='display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;'><div><span style='color:#da8085;'> Name:  " + "<br/></span><span style='color:#000;'>" + e1.policy_sub_type_name + "</span></div>";
	str += "<div><span style='color:#da8085;'> Sum Insured </span>" + "<br/><span style='color:#000;'>" + e1.sumInsured + "</span></div>";
	str += "<div class='mr-3'><span style='color:#da8085;'> Premium" + "</span><br/><span style='color:#000;'>" + parseFloat(PremiumServiceTax).toFixed(2) + "</span></div></div>";
	$("#premiumModalBody").append(str);

	premium += parseFloat(PremiumServiceTax);
	$("#premiumBif").css("pointer-events","auto");
	});
	if(PremiumServiceTax == undefined)
	{

	$('#patFamilyConstruct').val('');
	$('#premium').val('');
	$("#premiumModalBody").html('');
	}
	else
	{
	$("#premium").val(premium.toFixed(2));
	}


	}
	function set_premium_const(){
	var tabl =  "&empId=" + emp_id+"&family_construct="+$("#patFamilyConstruct").val()+"&policy_detail_id="+ $("#sum_insures :selected").attr("data-policyno")+"&sumInsured="+ $("#sum_insures :selected").val();
	$.post("/employee/get_refresh_const_premium", tabl, function (e) {
	e = JSON.parse(e);
	var getPremium = e.get_premium;

	get_age_wise_premium(getPremium);
	var opt = e.get_rel;
	var rel_allowed = e.rel_allowed;



	console.log(rel_allowed);

	$.each(rel_allowed, function( index, value ) {
	if(opt.toString().indexOf(value) == -1){
	console.log(opt);
	//remove from relation with proposer dropdown
	$("#family_members_id1 option[value='"+value+"']").hide();
	}else{
	console.log(opt);
	$("#family_members_id1 option[value='"+value+"']").show();
	}
	});
	});
refresh = 0;
	}
	function combo_set_premium_data()
	{
	var tabl =  "&empId=" + emp_id+"&family_construct="+$("#patFamilyConstruct").val()+"&policy_detail_id="+ $("#sum_insures :selected").attr("data-policyno")+"&sumInsured="+ $("#sum_insures :selected").val();
	$.post("/employee/combo_set_data", tabl, function (e) {

	e = JSON.parse(e);
	e.forEach(function (e1) {
	var date_birth = e1.rel_name+'_date_birth';
	var rel_name = e1.rel_name+'_rel';
	console.log(date_birth);
	$("#"+date_birth).val(e1.dob);
	$("#"+rel_name).val(e1.fr_id);


	});



	});
	}