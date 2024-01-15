var family_constrcut='1-0';
var requestData = {};

$(document).ready(function(){
	get_self_data_on_load();

	function GetParameterValues(param) {  
        var url = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');  
        for (var i = 0; i < url.length; i++) {  
            var urlparam = url[i].split('=');  
            if (urlparam[0] == param) {  
                return urlparam[1];  
            }  
        }  
    }
    function getCheckedProductsInfo(){
    	var productsArr = [];
	    var policyDetailIds = [];
	    var policyNumbers = [];
		$.each($("input[common-attr='family_floater']:checked"), function(){
		  productsArr.push($(this).data('name'));
		  policyDetailIds.push($(this).data('policydetailid'));
		  policyNumbers.push($(this).data('policynumber'));
		});        
	    $.ajax({
	      // url: "/update_product_info", // Should only get the data of checked products (create new function for this)
	      url: "/get_checked_product_info",
	      type: "POST",
	      async: false,
	      data: {product_name: productsArr,policyDetailIds: policyDetailIds,policyNumbers:policyNumbers},
	      dataType: 'json',
	      success: function (response) {
	        $('.family_floater_selected_prod').html(response.html_card);
	        $('.drop_prem').html(response.html_premium);
	        $('.total_premium').html(response.total_premium);
	      }
	    });
    }
	if(GetParameterValues('i') == 'edit'){
		var newURL = window.location;
		var splitURL=newURL.toString().split("/");
		var last_element = splitURL[splitURL.length-1].toString().split("?");
		last_element = last_element[0];
		$.ajax({
			url: "/update_modal_data",
			type: "POST",
			async: false,
			data: {policy_detail_id: last_element},
			dataType: 'json',
			success: function (response) {
				var family_construct = '';
				var family_sum_insured = '';
				// debugger;
				// if(response.policy_sub_type_id == 1){
					$('.age_title').html('Enter age of the eldest member');
				// }else{
					// $('.age_title').html('Member Age');
				// }
				if(response.saved_data.max_age > '20'){
					//$('#max_age').prop("readonly", true);
				}
				$('.family_flot_prod_name').html(response.product_name);
				$('.family_flot_policy_detail_id').val(response.policy_detail_id);
				$('.family_float_prod_img').attr('src', response.product_image);
				$('#max_age').val(response.saved_data.max_age);
				$('#loaderNew').show();
				$.each(response.family_construct, function( index, value ) {
					if(value.fr_id == 2){
						value.fr_name = 'Kid 1';
					}
					if(value.fr_id == 3){
						value.fr_name = 'Kid 2';
					}
					if(value.fr_id == 25){
						value.fr_name = 'Kid 3';
					}
					if(value.fr_id == 26){
						value.fr_name = 'Kid 4';
					}
					var check = '';

					var items = response.saved_data.relationship_id.split(',');
					if(jQuery.inArray(value.fr_id,items) !='-1'){
						check = 'checked';
					}
					
				  	family_construct += '<div class="col-md-3 col-6"><div class="custom-control custom-checkbox"><input type="checkbox" name="family_float_construct" class="custom-control-input" data-id="'+value.fr_id+'" id="customfrid'+value.fr_id+'" '+check+'><label class="custom-control-label" for="customfrid'+value.fr_id+'"><span>'+value.fr_name+'</span></label></div></div>';
				});
				$.each(response.sum_insured_data, function( index, value ) {
					var radio_check = '';
					if(response.saved_data.sum_insure == value.sum_insured_word){
						radio_check = 'checked';
					}
				    if(!(typeof value.sum_insured_combination === 'undefined' || value.sum_insured_combination === null)){
						family_sum_insured += '<div class="col-md-12 col-12"><div class="custom-control custom-radio form-group"><input type="radio" name="family_float_sum" class="custom-control-input" data-name="'+value.sum_insured_word+'" id="customRadio'+index+'" '+radio_check+'><label class="custom-control-label" for="customRadio'+index+'"><span>( '+value.sum_insured_combination+' )</span></label></div></div>';
					} else {
						family_sum_insured += '<div class="col-md-3 col-6"><div class="custom-control custom-radio form-group"><input type="radio" name="family_float_sum" class="custom-control-input" data-name="'+value.sum_insured_word+'" id="customRadio'+index+'" '+radio_check+'><label class="custom-control-label" for="customRadio'+index+'"><span>'+value.sum_insured_word+'</span></label></div></div>';
					}
				});
				$('#loaderNew').hide();
				$('.family_flot_construct').html(family_construct);
				$('.family_flot_sum_insured').html(family_sum_insured);
				$("#editdetails-float").modal('show');
			}
		});
	}
	//check if product selected
	$(document).on("click",".memProposerBtn",function(){
		
    	var policyDetailIds = [];
        $.each($("input[common-attr='family_floater']:checked"), function(){
            policyDetailIds.push($(this).data('policydetailid'));
        });    

        if(policyDetailIds.length == ''){
        	swal("Alert","Please Select Product.", "warning");
        	return false;
        }else{
        	return true;
        }
		
	})
    // Call to get checked products	
	getCheckedProductsInfo();	
	// $(document).on("change",".product_name",function(){
	$('input[common-attr="family_floater"]').on('change', function() {
		var prod_id = $("#product_id_hidden").val();
		var otherPolicyIds = null;
		// if(prod_id == 'HERO_FINCORP'){
			var otherPolicyIds = [];
			var otherPolicy = $('input[name="' + this.name + '"]').not(this);
			otherPolicy.prop('checked', false);
			// $('input[name="' + this.name + '"]').not(this).prop('checked', false);
			$.each(otherPolicy, function( index, value ) {
				otherPolicyIds.push($(this).attr('data-policyDetailId'));
			});
		// }

		var isChecked = $(this).prop('checked');
		var productId = $(this).data('policydetailid');
		var allCheckedProducts = $("input[common-attr='family_floater']:checked");
		if(isChecked) {
			$.ajax({
				url: "/insert_product_on_check",
				type: "POST",
				async: false,
				data: {
					productId: productId,
					otherPolicyIds: otherPolicyIds
				},
				dataType: 'json',
				success: function (data) {
					if(data){
						// Call to get checked products	
						getCheckedProductsInfo();
					}
				}
			})
		} else {
			//Check for approval
			/*var checkedProductsLen = $("input[name='family_floater']:checked").length;
			if(checkedProductsLen == 0){
				swal("Alert","Need atleast one product to procees further !!", "warning");
				return false;
			}*/
			$.ajax({
				url: "/delete_product_on_uncheck",
				type: "POST",
				async: false,
				data: {productId: productId},
				dataType: 'json',
				success: function (data) {
					if(data){
						// Call to get checked products	
						getCheckedProductsInfo();
					}
				}
			})
		}
	});

	$(document).on("click",".openModal",function(){
		var policy_detail_id = $(this).data('id');
		$.ajax({
			url: "/update_modal_data",
			type: "POST",
			async: false,
			data: {policy_detail_id: policy_detail_id},
			dataType: 'json',
			success: function (response) {
				var family_construct = '';
				var family_sum_insured = '';
				// console.log(response);
				// console.log(response.policy_sub_type_id);
				// debugger;
				// if(response.policy_sub_type_id == 1){
					$('.age_title').html('Enter age of the eldest member');
				// }else{
					// $('.age_title').html('Member Age');
				// }

				if(response.saved_data.max_age > '20'){
					//$('#max_age').prop("readonly", true);
				}

				$('.family_flot_prod_name').html(response.product_name);
				$('.family_flot_policy_detail_id').val(response.policy_detail_id);
				$('.family_float_prod_img').attr('src', response.product_image);
				$('#max_age').val(response.saved_data.max_age);
				$('#loaderNew').show();
				$.each(response.family_construct, function( index, value ) {
					if(value.fr_id == 2){
						value.fr_name = 'Kid 1';
					}
					if(value.fr_id == 3){
						value.fr_name = 'Kid 2';
					}
					if(value.fr_id == 25){
						value.fr_name = 'Kid 3';
					}
					if(value.fr_id == 26){
						value.fr_name = 'Kid 4';
					}
					var check = '';
					// console.log(value.fr_id+'=='+response.saved_data.relationship_id);
					var items = response.saved_data.relationship_id.split(',');
					if(jQuery.inArray(value.fr_id,items) !='-1'){
						check = 'checked';
					}
				  	family_construct += '<div class="col-md-3 col-6"><div class="custom-control custom-checkbox"><input type="checkbox" name="family_float_construct" class="custom-control-input" data-id="'+value.fr_id+'" id="customfrid'+value.fr_id+'" '+check+'><label class="custom-control-label" for="customfrid'+value.fr_id+'"><span>'+value.fr_name+'</span></label></div></div>';
				});
				$.each(response.sum_insured_data, function( index, value ) {
					var radio_check = '';
					if(response.saved_data.sum_insure == value.sum_insured_word){
						radio_check = 'checked';
					}
					if(!(typeof value.sum_insured_combination === 'undefined' || value.sum_insured_combination === null)){
						family_sum_insured += '<div class="col-md-12 col-12"><div class="custom-control custom-radio form-group"><input type="radio" name="family_float_sum" class="custom-control-input" data-name="'+value.sum_insured_word+'" id="customRadio'+index+'" '+radio_check+'><label class="custom-control-label" for="customRadio'+index+'"><span>( '+value.sum_insured_combination+' )</span></label></div></div>';
					} else {
						family_sum_insured += '<div class="col-md-3 col-6"><div class="custom-control custom-radio form-group"><input type="radio" name="family_float_sum" class="custom-control-input" data-name="'+value.sum_insured_word+'" id="customRadio'+index+'" '+radio_check+'><label class="custom-control-label" for="customRadio'+index+'"><span>'+value.sum_insured_word+'</span></label></div></div>';
					}
				    
				});
				$('#loaderNew').hide();
				$('.family_flot_construct').html(family_construct);
				$('.family_flot_sum_insured').html(family_sum_insured);
				$("#editdetails-float").modal('show');
			}
		});
	});

	$(document).on("click",".familyFloatBtn",function(){
		var policy_detail_id = $('.family_flot_policy_detail_id').val();
		var max_age = $('#max_age').val();
		var frIds = [];
		var sumInsures = [];
        $.each($("input[name='family_float_construct']:checked"), function(){
            frIds.push($(this).data('id'));
        });
        $.each($("input[name='family_float_sum']:checked"), function(){
            sumInsures.push($(this).data('name'));
        });
        //alert(frIds.toString());
        // Commented validations as GHI is not present.
        // if(frIds == '0,2,3' || frIds == '1,2,3'){
        // 	swal("Alert","1A + 2K family construct not allowed.", "warning");
        // 	return false;
        // }
        // if(frIds == '0,2' || frIds == '0,3' || frIds == '1,2' || frIds == '1,3'){
        // 	swal("Alert","1A + 1K family construct not allowed.", "warning");
        // 	return false;
        // }
        if (sumInsures.length === 0) {
		    swal("Alert","Select Sum Insured.", "warning");
        	return false;
		}
        // if((jQuery.inArray(0, frIds) !== -1 || jQuery.inArray(1, frIds) !== -1)){
			$('#loaderNew').show();
        	$.ajax({
				url: "/update_family_floter",
				type: "POST",
				async: false,
				data: {policy_detail_id: policy_detail_id,frIds: frIds,sumInsures :sumInsures,max_age:max_age},
				dataType: 'json',
				success: function (response) {	
					$('#loaderNew').hide();
					if(response.status == 0){
						swal("Alert",response.message, "warning");
					}else{
						// Call to get checked products	
						getCheckedProductsInfo();	

						$("#editdetails-float").modal('hide');
						if(GetParameterValues('i') == 'edit'){
							window.location = '/member_review';
						}
					}

				}
			});
		// }else{
		// 	swal("Alert","add Adult member to policy", "warning");
		// }
        
		
	})

	

})



function get_self_data_on_load(){



	$('.total_premium').html(0);
	$('.family_mmeber_selected').html('Self');
	$('.amt-del').html('');

		
	return
	$('.family_mmeber_selected').html('Self');


	let mapping = {
		'family_members_ac_count': "member_get_policy_details",
		'ghi_cover': "ghi_cover",
		'pa_cover': "gpa_cover",
		'ci_cover': "gci_cover",
		'hospi_cash': "hc_cover",
		'spouse_age': "member_maxage_get_policy_details",
		'tenure': "member_tenure_get_policy_details",
		'super_top_up_cover': "topup_cover",
		'deductable': "member_deductable_get_policy_details",
		'numbers_of_ci': 'numbers_of_ci'		
	};

	
	
	let requestData = {
		plan_id: $("#h_plan_id").val(),
		lead_id: $("#h_lead_id").val(),
		trace_id: $("#h_trace_id").val(),
		customer_id: $("#h_customer_id").val(),
	};

	$('.quote_generation_fields').each(function(i, obj) {

		debugger;
		let current_id = obj.id;
		if (Object.values(mapping).indexOf(current_id) > -1) {
			let key = Object.keys(mapping).find(key => mapping[key] === current_id);
			requestData[key] = obj.value;
		}
	});

	//request data of



	console.log(requestData);
	requestData['source'] = 'customer'

	var quote_action_url = "/policyproposal/generateQuote";

	$.ajax({
		url: quote_action_url,
		data: requestData,
		type: 'post',
		dataType: 'json',
		cache: false,
		clearForm: false,
		success: function(response) {
			//if (response.success) {
			let data = response.data;
			populateQuoteData(data);
			//}
		}
	});



}

$(document).on('change','.member_get_policy_details,.member_sum_insured_get_policy_details,.member_deductable_get_policy_details,.member_tenure_get_policy_details,.member_maxage_get_policy_details',function(){


	var frIds=[];
	var members=''; 

	var adult_count=0;
	var child_count=0;
	
	$.each($("input[name='family_float_construct']:checked"), function(){

		frIds.push($(this).attr('id'));
		members +=$(this).attr('id')+'+';				
		if($(this).attr('id')=='Self'||$(this).attr('id')=='Spouse'){

			adult_count++;
		}else{
			child_count++;
		}
		
	});


	if(members==''){
		members='Self+'
	}
	// console.log(members.slice(0,-1));
	family_constrcut=adult_count+"-"+child_count;

	members=members.slice(0,-1);

	$('.family_mmeber_selected').html(members);


	// return;
	let mapping = {
		'family_members_ac_count': family_constrcut,
		'ghi_cover': "ghi_cover",
		'pa_cover': "gpa_cover",
		'ci_cover': "gci_cover",
		'hospi_cash': "hc_cover",
		'spouse_age': "member_maxage_get_policy_details",
		'tenure': "member_tenure_get_policy_details",
		'super_top_up_cover': "topup_cover",
		'deductable': "deductable",
		'tenure': "tenure",
		'numbers_of_ci': 'numbers_of_ci'		
	};

	
	
	requestData = {
		plan_id: $("#h_plan_id").val(),
		lead_id: $("#h_lead_id").val(),
		trace_id: $("#h_trace_id").val(),
		customer_id: $("#h_customer_id").val(),
	};

	$('.quote_generation_fields').each(function(i, obj) {

		let current_id = obj.id;
		if (Object.values(mapping).indexOf(current_id) > -1) {
			let key = Object.keys(mapping).find(key => mapping[key] === current_id);
			requestData[key] = obj.value;
		}
	});

	//request data of



	// console.log(requestData);
	requestData['source'] = 'customer'
	requestData['age'] = $('#max_age').val();
	requestData['age'] = $('#max_age').val();
	requestData['family_members_ac_count'] = family_constrcut;

	
	var quote_action_url = "/policyproposal/generateQuote";

	$.ajax({
		url: quote_action_url,
		data: requestData,
		type: 'post',
		dataType: 'json',
		cache: false,
		clearForm: false,
		success: function(response) {
			//if (response.success) {
			let data = response.data;
			populateQuoteData(data);
			//}
		}
	});

	// alert(plan_id);
})

function generateQuote(form_id) {
  
	let mapping = {
		'family_members_ac_count': "family_members_ac_count",
		'ghi_cover': "sum_insured1",
		'pa_cover': "sum_insured2",
		'ci_cover': "sum_insured3",
		'hospi_cash': "sum_insured6",
		'spouse_age': "spouse_age",
		'tenure': "tenure",
		'super_top_up_cover': "sum_insured5_1",
		'deductable': "deductable",
		'numbers_of_ci': 'numbers_of_ci'
	};

	requestData = {
		plan_id: $("#" + form_id + " [name='plan_id']").val(),
		lead_id: $("#" + form_id + " [name='lead_id']").val(),
		trace_id: $("#" + form_id + " [name='trace_id']").val(),
		customer_id: $("#" + form_id + " [name='customer_id']").val(),
	};

	$spouseDob = $("#" + form_id + " [name='spouse_dob']");

	if (!$spouseDob.prop('disabled')) {
		requestData.spouse_age = $("#" + form_id + " [name='spouse_age']").val();
		requestData.spouse_dob = $("#" + form_id + " [name='spouse_dob']").val();
	}

	let $current_generate_quote_acc = $("#" + form_id).parent();

	if (!requestData.customer_id && $current_generate_quote_acc.hasClass('show')) {
		displayMsg('error', 'Please fill customer details first');
		return;
	}

	$('#' + form_id + ' .quote_generation_fields').each(function(i, obj) {
		let current_id = obj.id;
		if (Object.values(mapping).indexOf(current_id) > -1) {
			let key = Object.keys(mapping).find(key => mapping[key] === current_id);
			requestData[key] = obj.value;
		}
	});

	var quote_action_url = "<?php echo base_url(); ?>policyproposal/generateQuote";

	$.ajax({
		url: quote_action_url,
		data: requestData,
		type: 'post',
		dataType: 'json',
		cache: false,
		clearForm: false,
		success: function(response) {
			//if (response.success) {
			let data = response.data;
			populateQuoteData(data);
			//}
		}
	});
}

function submitLeadform(){

	$.ajax({

		url:'/policyproposal/submitLeadForm',
		data: requestData,
		type: 'post',
		dataType: 'json',
		cache: false,
		clearForm: false,
		success: function(response) {
			
			if (response.success) {

				//let quote_ids = response.data.quote_ids;
				//$(form_id).find("[name='master_quote_id']").val(quote_ids.join());
				
				if(response.policy_errors){

					swal("Alert", response.policy_errors, "warning");
					return false;
				}
				else {

					// swal("Alert", response.msg).then((value) => {
					// 		window.location.replace(location.origin+"/Member_detail_abc/member_proposer_detail");
					// });

					swal({
						title: "Success",
						text: response.msg,
						icon: "success",
						buttons: true
					  })
					  .then((willDelete) => {
						
						window.location.replace(location.origin+"/Member_detail_abc/member_proposer_detail");
					});
				}
			}
			else{

				if(response.policy_errors){

					swal("Alert", response.policy_errors, "warning");
				}
				else {

					swal("Alert", response.msg, "warning");
				}
				return false;
			}
		}
	});

	return false;
}
