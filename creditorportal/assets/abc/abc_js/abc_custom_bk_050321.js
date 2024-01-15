$(document).ready(function(){
	function GetParameterValues(param) {  
        var url = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');  
        for (var i = 0; i < url.length; i++) {  
            var urlparam = url[i].split('=');  
            if (urlparam[0] == param) {  
                return urlparam[1];  
            }  
        }  
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
				console.log(response.policy_sub_type_id);
				// debugger;
				if(response.policy_sub_type_id == 1){
					$('.age_title').html('Enter age of the eldest member');
				}else{
					$('.age_title').html('Member Age');
				}
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
					var check = '';
					console.log(value.fr_id+'=='+response.saved_data.relationship_id);
					if(jQuery.inArray(value.fr_id,response.saved_data.relationship_id) !='-1'){
						check = 'checked';
					}
				  	family_construct += '<div class="col-md-3 col-6"><div class="custom-control custom-checkbox"><input type="checkbox" name="family_float_construct" class="custom-control-input" data-id="'+value.fr_id+'" id="customfrid'+value.fr_id+'" '+check+'><label class="custom-control-label" for="customfrid'+value.fr_id+'"><span>'+value.fr_name+'</span></label></div></div>';
				});
				$.each(response.sum_insured_data, function( index, value ) {
					var radio_check = '';
					if(response.saved_data.sum_insure == value.sum_insured_word){
						radio_check = 'checked';
					}
				    family_sum_insured += '<div class="col-md-3 col-6"><div class="custom-control custom-radio form-group"><input type="radio" name="family_float_sum" class="custom-control-input" data-name="'+value.sum_insured_word+'" id="customRadio'+index+'" '+radio_check+'><label class="custom-control-label" for="customRadio'+index+'"><span>'+value.sum_insured_word+'</span></label></div></div>';
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
        $.each($("input[name='family_floater']:checked"), function(){
            policyDetailIds.push($(this).data('policydetailid'));
        });    

        if(policyDetailIds.length == ''){
        	swal("Alert","Please Select Product.", "warning");
        	return false;
        }else{
        	return true;
        }
		
	})


	var productsArr = [];
    var policyDetailIds = [];
    var policyNumbers = [];
	$.each($("input[name='family_floater']:checked"), function(){
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

	$(document).on("click",".product_name",function(){
		var isChecked = $(this).prop('checked');
		var productId = $(this).data('policydetailid');
		if(isChecked) {
			$.ajax({
				url: "/insert_product_on_check",
				type: "POST",
				async: false,
				data: {productId: productId},
				dataType: 'json',
				success: function (data) {
					var productsArr = [];
				    var policyDetailIds = [];
				    var policyNumbers = [];
					$.each($("input[name='family_floater']:checked"), function(){
					  productsArr.push($(this).data('name'));
					  policyDetailIds.push($(this).data('policydetailid'));
					  policyNumbers.push($(this).data('policynumber'));
					});
					if(data){
						$.ajax({
							url: "/get_checked_product_info",
							type: "POST",
							async: false,
							data: {product_name: productsArr,policyDetailIds: policyDetailIds,policyNumbers:policyNumbers},
							dataType: 'json',
							success: function (response){
								$('#loaderNew').hide();
								$('.family_floater_selected_prod').html(response.html_card);
								$('.drop_prem').html(response.html_premium);
								$('.total_premium').html(response.total_premium);
							}
						})
					}
				}
			})
		} else {
			$.ajax({
				url: "/delete_product_on_uncheck",
				type: "POST",
				async: false,
				data: {productId: productId},
				dataType: 'json',
				success: function (data) {
					var productsArr = [];
				    var policyDetailIds = [];
				    var policyNumbers = [];
					$.each($("input[name='family_floater']:checked"), function(){
					  productsArr.push($(this).data('name'));
					  policyDetailIds.push($(this).data('policydetailid'));
					  policyNumbers.push($(this).data('policynumber'));
					});
					if(data){
						$.ajax({
							url: "/get_checked_product_info",
							type: "POST",
							async: false,
							data: {product_name: productsArr,policyDetailIds: policyDetailIds,policyNumbers:policyNumbers},
							dataType: 'json',
							success: function (response){
								$('#loaderNew').hide();
								$('.family_floater_selected_prod').html(response.html_card);
								$('.drop_prem').html(response.html_premium);
								$('.total_premium').html(response.total_premium);
							}
						})
					}
				}
			})
		}
	})

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
				console.log(response);
				console.log(response.policy_sub_type_id);
				// debugger;
				if(response.policy_sub_type_id == 1){
					$('.age_title').html('Enter age of the eldest member');
				}else{
					$('.age_title').html('Member Age');
				}

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
					var check = '';
					console.log(value.fr_id+'=='+response.saved_data.relationship_id);
					if(jQuery.inArray(value.fr_id,response.saved_data.relationship_id) !='-1'){
						check = 'checked';
					}
				  	family_construct += '<div class="col-md-3 col-6"><div class="custom-control custom-checkbox"><input type="checkbox" name="family_float_construct" class="custom-control-input" data-id="'+value.fr_id+'" id="customfrid'+value.fr_id+'" '+check+'><label class="custom-control-label" for="customfrid'+value.fr_id+'"><span>'+value.fr_name+'</span></label></div></div>';
				});
				$.each(response.sum_insured_data, function( index, value ) {
					var radio_check = '';
					if(response.saved_data.sum_insure == value.sum_insured_word){
						radio_check = 'checked';
					}
				    family_sum_insured += '<div class="col-md-3 col-6"><div class="custom-control custom-radio form-group"><input type="radio" name="family_float_sum" class="custom-control-input" data-name="'+value.sum_insured_word+'" id="customRadio'+index+'" '+radio_check+'><label class="custom-control-label" for="customRadio'+index+'"><span>'+value.sum_insured_word+'</span></label></div></div>';
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
        if((jQuery.inArray(0, frIds) !== -1 || jQuery.inArray(1, frIds) !== -1)){
			$('#loaderNew').show();
        	$.ajax({
				url: "/update_family_floter",
				type: "POST",
				async: false,
				data: {policy_detail_id: policy_detail_id,frIds: frIds,sumInsures :sumInsures,max_age:max_age},
				dataType: 'json',
				success: function (response) {	
					$('#loaderNew').hide();
					if(response == 0){
						swal("Alert","Age is not as per policy", "warning");
					}else{
						var productsArr = [];
						var policyDetailIds = [];
						$.each($("input[name='family_floater']:checked"), function(){
						    productsArr.push($(this).data('name'));
						    policyDetailIds.push($(this).data('policydetailid'));
						});        
						$.ajax({
							url: "/get_checked_product_info",
							type: "POST",
							async: false,
							data: {product_name: productsArr,policyDetailIds: policyDetailIds},
							dataType: 'json',
							success: function (response) {
								$('.family_floater_selected_prod').html(response.html_card);
								$('.drop_prem').html(response.html_premium);
								$('.total_premium').html(response.total_premium);
							}
						});			
						$("#editdetails-float").modal('hide');
						if(GetParameterValues('i') == 'edit'){
							window.location = '/member_detail_product_abc';
						}
					}

				}
			});
		}else{
			swal("Alert","add Adult member to policy", "warning");
		}
        
		
	})

	

})