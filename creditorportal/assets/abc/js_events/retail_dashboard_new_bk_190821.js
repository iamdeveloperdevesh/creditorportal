var allProducts = null;
var allFamilyConstructs = null;
var employeeData = null;
var prevIndex = "";
var employeeToProductOnLoad = null;
var selectedComboPremium = null;
var occupationList = null;
var occupationSelected = null;
var annualIncomeSelected = null;

var previousCheckedRadio = null;
// var loading = $('#resultLoadingNew');
//Attach the event handler to any element

// $(document)
// .ajaxStart(function () {
//     //ajax request went so show the loading image
//     // var loading = $('#resultLoadingNew');
//     
//  })
// .ajaxStop(function () {
//     //got response so hide the loading image
//     // var loading = $('#resultLoadingNew');
//     
// });

// $( document ).ajaxStart(function() {
// 	// write code for show loader here
// 	var loading = $('#resultLoadingNew');
// 	loading.show();
// });
// $( document ).ajaxComplete(function() {
// 	// write code for hide loader here
// 	var loading = $('#resultLoadingNew');
// 	loading.hide();
// });

$( document ).ready(function() {
	// return false;
	/* Get Employee To Product Data*/
	employeeToProductOnLoad = getEmployeeToProductData();
	var constructSelected = null;
	var adultSelectedConstruct = null;
	var childSelectedConstruct = null;
	var relationsSelected = null;
	if(employeeToProductOnLoad){
		constructSelected = employeeToProductOnLoad.family_construct;
		if(constructSelected){
			constructSelected = constructSelected.split("+");
			adultSelectedConstruct = constructSelected[0];
			childSelectedConstruct = constructSelected[1] ? constructSelected[1] : '0K';
		}
		if(employeeToProductOnLoad.relationship_id){
			relationsSelected = employeeToProductOnLoad.relationship_id.split(",");
		}
		occupationSelected = employeeToProductOnLoad.occupation_id;
		annualIncomeSelected = employeeToProductOnLoad.annual_income;
	}
	/* Get Family Construct Detail*/
	var familyConstructAjaxOnLoad = $.ajax({
		url: "/get_family_construct_retail",
		type: "GET",				
		dataType: "json",
		// async:false,
		success: function (response) 
		{
			allFamilyConstructs = response.adult_child;
			var edit_res = '';
			var split_data;
			if(typeof(response.edit_data) != "undefined" && response.edit_data !== null) {
				if(response.edit_data!= '')
				{
					edit_res = response.edit_data;
					split_data = edit_res.familyConstruct.split('+');
					sum_insured = edit_res.policy_mem_sum_insured;
					getpremium(family_construct,response.suminsured[0].sum_insured);	
					$('#dec_family_check').prop('checked',true);
				}
			}
			var arr = [];
			var child = [];
			var adultNew = [];
			var adultsHtml;
			var childs;
			$("#adults_no").append('');
			$("#child_no").append('');
			$("#sumInsured").append('');

			for(var i = 0; i < response.adult_child.length; i++)
			{
				if(response.adult_child[i].adult_child == 'A')
				{					
					adultNew.push({'fr_id':response.adult_child[i].fr_id,'fr_name':response.adult_child[i].fr_name});
			 	}
				if(response.adult_child[i].adult_child == 'C')
				{
					child.push(response.adult_child[i].fr_id);
				}				
		   	}

			for(var a = 0; a < adultNew.length; a++){
				var adultNewCheck = '';
				if(jQuery.inArray(adultNew[a]['fr_id'], relationsSelected) !== -1){
					adultNewCheck = 'checked';
				}
				adultsHtml = '<div class="col-md-3 col-4"><div class="form-group"><div class="custom-control custom-checkbox custom-control-inline"> <input type="checkbox" onclick = changes(this); name = "adults" '+adultNewCheck+' class="custom-control-input relation-input" data-opt="" id="adult_new_family_check_'+a+'" value="1A_'+a+'" fr_id="'+adultNew[a]['fr_id']+'" data-toggle="modal"><label class="custom-control-label" for="adult_new_family_check_'+a+'">'+adultNew[a]['fr_name']+'</label> </div></div></div>'; 
				$("#adults_no_new").append(adultsHtml);
			}

			var kid_fr_id = '';			
			for(var k = 0; k <= child.length; k++)
			{
				var childCheck = '';
				var defaultCheck = '';
				var child_nos = k+'K';
				if(k != 0){
					kid_fr_id += ','+child[k-1];
				}
				if(child_nos == childSelectedConstruct){
					childCheck = 'checked';
				}

				if(k == 0 && childCheck == ''){
					defaultCheck = 'checked';
				}

				var kid_fr_id = kid_fr_id.replace(/(^,)|(,$)/g, "");
				childs = '<div class="col-md-3 col-4"><div class="form-group"><div class="custom-control custom-checkbox custom-control-inline"> <input type="radio" onclick = changes(this); name = "childs" '+childCheck+' '+defaultCheck+' class="custom-control-input relation-input" data-opt="" id="child_family_check_'+k+'" value="'+k+'" fr_id="'+(kid_fr_id == '' ? undefined : kid_fr_id)+'" data-toggle="modal"><label class="custom-control-label" for="child_family_check_'+k+'">'+k+'</label> </div></div></div>'; 
				$("#child_no").append(childs);
			}					
		}
	});
	/* Fetch All Products Detail*/
	var productDetailAjaxOnLoad = $.ajax({
		url: "/get_products_retail",
		type: "GET",				
		dataType: "json",
		// async: false,
		success: function (productResponse) 
		{
			allProducts = productResponse;
			// console.log(allProducts);
			var product_box_html = "";
			$.each(allProducts, function(key, product){
				var partition = "";
				var active_product_class = '';
				if(employeeToProductOnLoad && (product.product_code == employeeToProductOnLoad.main_product_code)){
					active_product_class = 'cover-act-btn';
					getSumInsured(product.product_name);
				}
				if((allProducts.length - key) > 1){
					partition = "<span class='info-i mr-1'></span>";
				}
				product_box_html += "<input type='button' onclick='changeProduct(this)' id='product_"+key+"' product-code='"+product.product_code+"' class='btn cover-btn product_button "+active_product_class+"' value='"+product.product_name+"'><i tool-type='product' product-name='"+product.product_name+"' class='fa fa-info-circle mr-2 ml-1 info-tool-tip' onclick='showToolTipModal(this)'></i>"+partition;
			});
			$("#product_box").append(product_box_html);
			
		}	
	});
	/* Fetch selected combo & premium with breakup */
	if(employeeToProductOnLoad && employeeToProductOnLoad.combination_id){
		selectedComboPremium = getSelectedComboPremiumBreakup();
		setPremiumWithBreakup(selectedComboPremium);
	}
	$.when(familyConstructAjaxOnLoad, productDetailAjaxOnLoad).done(function(){
		previousCheckedRadio = $('input[name=childs]:checked');
		occupationLogicBoxShowHide();
	});
});

function showToolTipModal(elem){
	// console.log(elem);
	$("#tooltip-del").modal('hide');
	$('#tooltip-header').empty();
	$('#tooltip-content').empty();
	$('#tooltip-content').parent().removeClass('md-scroll');

	var policyImage = {
		'5 to 25 Lacs':{
			'1':'/public/assets_new/images/hp1.png',
			'2':'/public/assets_new/images/hp2.png',
			'3':'/public/assets_new/images/hp3.png'
		},
		'50 to 100 Lacs':{
			'1':'/public/assets_new/images/hpi1.png',
			'2':'/public/assets_new/images/hpi2.png',
		}
	};

	if($(elem).attr('tool-type') == 'product'){
		$('#tooltip-header').html('Group Active Health');
		if($(elem).attr('product-name') == '5 to 25 Lacs'){
			$('#tooltip-content').html("<p class='mb-2'><b>Make your health more secure with Health PRO</b></p><img src='/public/assets_new/images/HP.JPG'>");
		} else if ($(elem).attr('product-name') == '50 to 100 Lacs') {
			$('#tooltip-content').html("<p class='mb-2'><b>We go to infinite lengths to secure your family’s health. Presenting #HealthProInfinity - a plan that offers optimal protection for your family’s health.</b></p><img src='/public/assets_new/images/HPI.JPG'>");
		}
	} else if ($(elem).attr('tool-type') == 'combination') {
		var selectedProduct = $(elem).attr('selected-product');
		var combinationId = $(elem).attr('combination-id');
		var deductible = $(elem).attr('deductible');
		var selectedProductCombos = allProducts.filter(function(obj) {
        	return obj.product_name == selectedProduct;
        });
        var selectedCombo = selectedProductCombos[0].combinations.filter(function(obj) {
        	return obj.id == combinationId;
        });

        var headerString = '';
        var imageHtml = '';
        // console.log(selectedCombo);
        $.each(selectedCombo[0].policies, function(key, policy){
        	headerString += policy.policy_no+' + ';
        	if(deductible == '0'){
        		imageHtml += "<img src='"+policyImage[selectedProduct][policy.policy_sub_type_id]+"'>";
        	} else {
        		imageHtml = "<img src='/public/assets_new/images/hpi3.png'>";
        	}
        });
        headerString = headerString.slice(0,-3);
        $('#tooltip-header').html(headerString);
        $('#tooltip-content').html(imageHtml);
        $('#tooltip-content').parent().addClass('md-scroll');
	}
	$("#tooltip-del").modal('show');
}

function getEmployeeToProductData(){
	var employeeToProductDetail = null;
	$.ajax({
		url: "/get_employee_to_product_detail",
		type: "GET",
		async:false,					
		dataType: "json",
		success: function (response){
			employeeToProductDetail = response;
			setEmployeeToProductData(employeeToProductDetail);
		}
	});
	return employeeToProductDetail;
}


function setEmployeeToProductData(employeeToProductOnLoad){
	if(employeeToProductOnLoad){
		constructSelected = employeeToProductOnLoad.family_construct;
		if(constructSelected){
			constructSelected = constructSelected.split("+");
			adultSelectedConstruct = constructSelected[0];
			childSelectedConstruct = constructSelected[1] ? constructSelected[1] : '0K';
		}
		if(employeeToProductOnLoad.relationship_id){
			relationsSelected = employeeToProductOnLoad.relationship_id.split(",");
		}
		occupationSelected = employeeToProductOnLoad.occupation_id;
		annualIncomeSelected = employeeToProductOnLoad.annual_income;
	}
}

function getSelectedComboPremiumBreakup(){
	
	var selectedComboPremium = null;
	$.ajax({
		url: "/get_employee_combo_detail",
		type: "GET",
		async:false,					
		dataType: "json",
		success: function (response){
			if(response.status == 'success'){
				selectedComboPremium = response;
			}
			
		}
	});
	return selectedComboPremium;
}

function setPremiumWithBreakup(selectedComboPremium){
	if(selectedComboPremium) {
		$("span#premium").html(selectedComboPremium.totalPremium);
		var premiumBreakupHtml = '';
		$.each(selectedComboPremium.premium, function(key, premium){
			premiumBreakupHtml += "<div class='col-md-11 col-12 row pro-bor mt-2'><div class='col-md-6'><p class='pre-ttl'>"+premium.policy_name+" <span class='prim-color'>(SI - "+selectedComboPremium.employeeToProduct.sum_insured_words+")</span></p></div><div class='col-md-6 text-right'><button class='btn premium-btn pre-top'><span>₹ "+premium.premium+" /-</span><span class='sm-pre'>Premium (Incl.Tax) </span></button></div></div>";
		});
		$('#premium-breakup-content').html(premiumBreakupHtml);
	} else {
		$("span#premium").html('0');
		$('#premium-breakup-content').html('');
	}
}

function getEmployeeDetail(){
	
	var employeeDetail = null;
	$.ajax({
		url: "/get_employee_detail_retail",
		type: "GET",	
		async: false,			
		dataType: "json",
		success: function (response){
			employeeDetail = response;
			
		}
	})
	return employeeDetail;	
}

function getpremium(family_construct,sum_insured)
{
	$.post("/get_premium", { "family_construct":family_construct, "sum_insured": sum_insured }, function (e) {
		if(e == 111111){
		  	swal({
		        title: "warning",
		        text: "Please select Propoer Data",
		        type: "warning",
		        showCancelButton: false,
		        confirmButtonText: "Ok",
		        closeOnConfirm: true
		    });
			return false;
		}
		$("#premium").html(e);
		$("#premium_input").val(e);
    });
}

function includeDatePicker(type){
	// if(type == 'childs'){
	// 	var minDateStr = '-252M';
	// 	var maxDateStr = '-91D';
	// } else if(type == 'adults') {
	// 	var minDateStr = '-660M';
	// 	var maxDateStr = '-216M';
	// }
	$(".datepicker").datepicker({
	    dateFormat: "dd-mm-yy",
	    prevText: '<i class="fa fa-angle-left"></i>',
	    nextText: '<i class="fa fa-angle-right"></i>',
	    changeMonth: true,
	    changeYear: true,
	    // minDate: minDateStr,
	    maxDate: 0,
	    yearRange: "-100: +0",
	    onSelect: function (dateText, inst) {
	        $(this).val(dateText);
	        //get_age_family(dateText, eValue);
	    },
	});
}

function changes(elem)
{
	var selectedFrId = $(elem).attr('fr_id');
	var value = $(elem).val();
	var updatedRelation = '';
	// var updatedRelation = updateRelationshipId();
	var updatedRelation = '';
	var selectedFrIdArr = [];
	if(selectedFrId != 'undefined'){
		var selectedFrIdArr = selectedFrId.split(",");
	}
	var canCloseModal = 0;
	$("#dob-modal-data").html('');


	if($(elem).attr('name') == 'adults'){
		if($(elem).is(':checked')){
			var addedDob = getFamilyDob('A');
			var selfDob = getEmployeeDetail().bdate;
			var spouseDob = '';
			var spouseDobObj = addedDob.filter(function(dobObj) {
				return dobObj.fr_id == 1;
			});
			var selfDobObj = addedDob.filter(function(dobObj) {
				return dobObj.fr_id == 0;
			});
			if(selectedFrId == 1 && spouseDobObj.length > 0){
				var spouseDob = spouseDobObj[0].dob;
				canCloseModal = 1;
				updatedRelation = updateRelationshipId('1','A');
			}
			if(selectedFrId == 0 && selfDobObj.length > 0){
				canCloseModal = 1;
				updatedRelation = updateRelationshipId('0','A');
			}
			var dob_modal_data = "";
			var spouse_dob_error_message = "<span class='er-self'>In case of change in your Date of birth, please contact your nearest Axis Bank branch</span>";
			dob_modal_data_self = "<div class='col-md-6'><div class='form-group'><label for='example-date-input' class='col-form-label'>Self Date Of Birth <sup><img src='/public/assets_new/images/important.png'></sup></label><input class='form-control fm-disable' fr_id='"+selectedFrIdArr[0]+"' name='adult_dob[1]' readonly='true' id='self_dob' placeholder='DD-MM-YYYY' value='"+selfDob+"'>"+spouse_dob_error_message+"<i class='fa fa-calendar'></i></div></div>";
			dob_modal_data_spouse = "<div class='col-md-6'><div class='form-group'><label for='example-date-input' class='col-form-label'>Spouse Date Of Birth <sup><img src='/public/assets_new/images/important.png'></sup></label><input class='form-control datepicker' fr_id='"+selectedFrIdArr[1]+"' name='adult_dob[2]' id='spouse_dob' placeholder='DD-MM-YYYY' value='"+spouseDob+"' onkeydown='event.preventDefault()'><i class='fa fa-calendar'></i></div></div>";
			
			if(selectedFrId == 0){
				dob_modal_data = dob_modal_data_self;
			} else if (selectedFrId == 1) {
				dob_modal_data = dob_modal_data_spouse;
			}
			$("#dob_submit_button").val("A");
			$(elem).attr("data-target","#dob-modal");
			$("#dob-modal-data").html(dob_modal_data);
		} else {
			$(elem).attr("data-target","");
			if($('[name="adults"]:checked').length == 0){
				swal("Alert","Atleast one adult is required!!","warning");
				$(elem).prop('checked', true);
    			return false;
			} else {
				updatedRelation = updateRelationshipId(selectedFrId,'A',1);
			}
		}
		occupationLogicBoxShowHide();
	} else if($(elem).attr('name') == 'childs') {
		var addedDob = getFamilyDob('C');
		dob_modal_data_kid = "";	
		var childFamilyConstructs = allFamilyConstructs.filter(function (construct) { return construct.adult_child == "C" });
		if(addedDob.length >= value){
			var frIdStr = '';
			for (var i = 0; i < value; i++) {
				frIdStr += addedDob[i].fr_id+',';
			}
			updatedRelation = updateRelationshipId(frIdStr.replace(/(^,)|(,$)/g, ""),'C');
			canCloseModal = 1;
		}
		if(value != 0){
			for (var i = 0; i < value; i++) {
				var kidDobObj = addedDob.filter(function(dobObj) {
					return dobObj.child_number == (i+1);
				});
				var kidDob = '';
				var kidRelation = '';
				if(kidDobObj.length > 0){
					var kidDob = kidDobObj[0].dob;
					var kidRelation = kidDobObj[0].fr_id;
				}
				var relationOptionsKid = "";
				$.each(childFamilyConstructs, function(key, childConstruct){
					var selected = '';
					if(childConstruct.fr_id == kidRelation){
						selected = 'selected';
					}
					var modifiedFrName = childConstruct.fr_name.replace('Dependent ','');
					relationOptionsKid += "<option "+selected+" value='"+childConstruct.fr_id+"'>"+modifiedFrName+"</option>";
				});
				dob_modal_data_kid += "<div class='col-md-6 form-group'><label class='col-form-label'> Kid "+(i+1)+"<sup class='star_sup'><img src='/public/assets_new/images/important.png'></sup></label><select class='form-control' name='relation["+(i+1)+"]' id='Kid"+(i+1)+"_relation' type='text' value='' placeholder='Select Kid "+(i+1)+"'><option value='' selected='selected'>Select Kid "+(i+1)+"</option>"+relationOptionsKid+"</select></div><div class='col-md-6'><div class='form-group'><label for='example-date-input' class='col-form-label'> Kid "+(i+1)+" Date Of Birth <sup><img src='/public/assets_new/images/important.png'></sup></label><input class='form-control datepicker' name='kid_dob["+(i+1)+"]' placeholder='DD-MM-YYYY' value='"+kidDob+"' onkeydown='event.preventDefault()'><i class='fa fa-calendar'></i></div></div>";
			}
			$("#dob_submit_button").val("C");
			$(elem).attr("data-target","#dob-modal");
			$("#dob-modal-data").html(dob_modal_data_kid);
		}
	}
	$("#close-dob-modal-btn").show();
	if(canCloseModal == 1){
		$("#close-dob-modal-btn").attr('canClose','1');
		$("#close-dob-modal-btn").attr('check-id',$(elem).attr('id'));
		previousCheckedRadio = $(elem);
		// $("#close-dob-modal-btn").show();
	} else {
		$("#close-dob-modal-btn").attr('canClose','0');
		$("#close-dob-modal-btn").attr('check-id',$(elem).attr('id'));
		// $("#close-dob-modal-btn").hide();
	}
	includeDatePicker($(elem).attr('name'));
}

function onDobModalClose(elem){
	var canCloseAttr = $(elem).attr("canClose");
	var checkIdAttr = $(elem).attr("check-id");
	if(canCloseAttr == '0'){
		var checkElement = $('#'+checkIdAttr);
		var checkElementType = checkElement.attr('type');
		$('#'+checkIdAttr).prop('checked',false);
		if(checkElementType == 'radio'){
			previousCheckedRadio.prop('checked',true);
		}
	}
}

function updateRelationshipId(selectedFrId,type,removeFlag = 0){
	
	var updated = false;
	$.ajax({
		url: "/update_relationship_id_retail",
		type: "POST",			
		dataType: "json",
		async:false,
		data: {
			frId:selectedFrId,
			type:type,
			removeFlag:removeFlag
		},
		success: function (response){
			if(response.status == 'success'){
				updated = true;
				selectedComboPremium = getSelectedComboPremiumBreakup();
				setPremiumWithBreakup(selectedComboPremium);
			}
			
		}
	});
	return updated;
}

function changeProduct(elem){

	var checkedAdults = $('input[name="adults"]:checked');
	if(checkedAdults.length == 0){
		swal("Alert","Please select atleast 1 adult !!", "warning");
		return false;
	}

	$(".product_button").not($(elem)).removeClass('cover-act-btn');
	if($(elem).hasClass('cover-act-btn')){
		removeProduct(elem);
	} else {
		selectProduct(elem);
	}
	updateProduct(getSelectionObj().mainProduct);
	getSumInsured(getSelectionObj().mainProduct);
	selectedComboPremium = getSelectedComboPremiumBreakup();
	setPremiumWithBreakup(selectedComboPremium);
	occupationLogicBoxShowHide();
	removeOccupationOrAi();
}

function selectProduct(elem){
	$(elem).addClass('cover-act-btn');
}

function removeProduct(elem){
	$(elem).removeClass('cover-act-btn');
}

function onlyNumbers(num){
	if ( /[^0-9]+/.test(num.value) ){
		num.value = num.value.replace(/[^0-9]*/g,"")
	}
	if (num.value.length > num.maxLength) {
		num.value = num.value.slice(0, num.maxLength);
	}
}

function occupationLogicBoxShowHide(){
	var occupationHtml = "";
	var occupationLabel = "";
	var annualIncomeLabel = "";
	var adultsSelected = $('input[name="adults"]:checked');
	
	if(adultsSelected.length == 1){
		var selectedAdultFrId = adultsSelected.attr('fr_id');
		if(selectedAdultFrId == 0 ){
			occupationLabel = "Self Occupation";
			annualIncomeLabel = "Self Annual Income";
		} else if (selectedAdultFrId == 1) {
			occupationLabel = "Spouse Occupation";
			annualIncomeLabel = "Spouse Annual Income";
		}
	} else if (adultsSelected.length == 2) {
		occupationLabel = "Self Occupation";
		annualIncomeLabel = "Self Annual Income";
	} else if (adultsSelected.length == 0) {
		occupationLabel = "Occupation";
		annualIncomeLabel = "Annual Income";
	}


	if(employeeToProductOnLoad && employeeToProductOnLoad.sum_insured_amt){
		occupationList = getOccupationOptions(employeeToProductOnLoad.main_product_code).occupations;
		var occupationListHtml = "";
		$.each(occupationList,function(index, occupation) {
			var selectOccOption = '';
			if(occupationSelected == occupation.occupation_id){
				selectOccOption = 'selected';
			}
			occupationListHtml += "<option value="+occupation.occupation_id+" risk-class="+occupation.Risk_Category+" "+selectOccOption+">"+occupation.name+"</option>"
		});
		if(employeeToProductOnLoad.sum_insured_amt > 1000000){
			if(employeeToProductOnLoad.check_occupation_ai == 1){
				occupationHtml = "<div class='col-md-4 form-group mb-2 occupation-logic-box'><label class='lb-1'>"+occupationLabel+"<sup class='star_sup'><img src='/public/assets_new/images/important.png'></sup></label><select class='form-control' name='occupation' mandatory='true' id='occupation_box' type='text' placeholder='Select Occupation'><option value=''>Select Occupation</option>"+occupationListHtml+"</select></div><div class='col-md-4 form-group mb-2 occupation-logic-box'><label class='lb-1'>"+annualIncomeLabel+"<sup class='star_sup'><img src='/public/assets_new/images/important.png'></sup></label><input class='form-control' mandatory='true' type ='number' maxLength='10' onkeyup='onlyNumbers(this)' name='annual_income' value='"+annualIncomeSelected+"' id='annual_income_box'></div>";
				$(".occupation-logic-box").remove();
				$("#occupation_sum_box").append(occupationHtml);
			} else {
				$(".occupation-logic-box").remove();
			}
		} else {
			if(employeeToProductOnLoad.check_occupation_ai == 1){
				occupationHtml = "<div class='col-md-4 form-group mb-2 occupation-logic-box'><label class='lb-1'>"+occupationLabel+"</label><select class='form-control' mandatory='false' name='occupation' id='occupation_box' type='text' placeholder='Select Occupation'><option value=''>Select Occupation</option>"+occupationListHtml+"</select></div>";
				$(".occupation-logic-box").remove();
				$("#occupation_sum_box").append(occupationHtml);
			} else {
				$(".occupation-logic-box").remove();
			}
		}
	} else {
		$(".occupation-logic-box").remove();
	}
}

function getOccupationOptions(productId=null){
	var occupationOptions = null;
	$.ajax({
		url: "/get_occupation_list",
		type: "POST",		
		dataType: "json",
		async: false,
		data: {
			productId:productId
		},
		success: function (response){
			occupationOptions = response;
		}
	});
	return occupationOptions;
}

function removeOccupationOrAi(){
	var removeStatus = null;
	$.ajax({
		url: "/remove_occ_ai",
		type: "POST",		
		dataType: "json",
		async: false,
		success: function (response){
			removeStatus = response.status;
		}
	});
	return removeStatus;
}

function getSumInsured(selectedProduct){
	var selectedProductDetail = allProducts.filter(function (product) { return product.product_name == selectedProduct });
	var sumInsuredRange = [];
	if(selectedProductDetail.length > 0){
		var sumInsuredRange = selectedProductDetail[0].combinations[0].policies[0].sum_insured_range;
	}
	var sumInsuredOptions = "<option value=''>Select Sum Insured</option>";
	var siIndex = 1;
	$.each(sumInsuredRange,function(index, el) {
		var selected = '';
		if(employeeToProductOnLoad && el == employeeToProductOnLoad.sum_insured_words){
			selected = 'selected';
			prevIndex = siIndex;
		}
		sumInsuredOptions += "<option "+selected+" value='"+index+"' si_word='"+el+"'>"+el+"</option>";
		siIndex ++;
	});
	$(".sum_insured_box").html(sumInsuredOptions);
}

function updateProduct(selectedProduct){
	prevIndex = "";
	var removeFlag = 0;
	if(selectedProduct == undefined){
		removeFlag = 1;
	}
	$.ajax({
		url: "/update_main_product_selection",
		type: "POST",	
		async: false,			
		dataType: "json",
		data: {
			selectedProduct:selectedProduct,
			removeFlag:removeFlag
		},
		success: function (response){
			swal("Alert","Product Updated", "warning");
			employeeToProductOnLoad = getEmployeeToProductData();
			
		}
	})
}

function updateSi(selectedSi){
	var updated = false;
	var selectedSi = selectedSi;
	$.ajax({
		url: "/update_si_selection",
		type: "POST",		
		dataType: "json",
		async: false,
		data: {
			selectedSi:selectedSi
		},
		success: function (response){
			updated = true;
			employeeToProductOnLoad = getEmployeeToProductData();
		}
	});
	return updated;
}

function onSelectSumInsure(elem){
	var currIndex = $(".sum_insured_box").prop('selectedIndex');
	// console.log("prevIndex   "+prevIndex);
	// console.log("currIndex   "+currIndex);
    if( currIndex > 0 )
    {
        if( prevIndex != currIndex )
        {
            /* Call Combination modal with selected product and sum insured*/
            // var selectedObj = getSelectionObj();
            // $('#resultLoadingNew').show();
            var selectedObj = getEmployeeToProductData();
            var selectedSi = $('option:selected', $(elem)).attr('si_word');

            /* Update SI in database */
            var updatedSi = updateSi(selectedSi);
            if(!updatedSi){
            	return false;
            }
            var product = allProducts.filter(function(obj) {
            	return obj.product_name == selectedObj.product_name;
            });
            var combinations = product[0].combinations;
            var combinationHtml = "";
            var comboIdDeductibleArr = [];
            var innerKey = 0;
            $.each(combinations, function(key, combination){
            	var sumInsureHtml = "";
            	var premiumButtonHtml = "";
            	var mainBox = "";
            	var deductibleArr = JSON.parse(combination.deductible_json);
            	policiesHtml = "";
            	$.each(combination.policies, function(policyKey, policy){
            		policiesHtml += "<span><i class='fa fa-check mr-2 prim-color'></i>"+policy.policy_no+"</span><br>";
            	});
            	if(deductibleArr != null){
            		innerKey = 0;
            		if(deductibleArr[selectedSi] != undefined){
            			$.each(deductibleArr[selectedSi], function(deductible, newSi){
            				comboIdDeductibleArr.push({"combo_box_id":'combo_box_'+combination.id+'_'+innerKey,"combo_id":combination.id,"sum_insured_words":selectedSi,"deductible":deductible});
            				var sumInsureHtml = "";
            				var premiumButtonHtml = "";
            				sumInsureHtml += "<span class='prim-color ml-4'>("+newSi+" with "+deductible+" deductible) </span>";
		            		// premiumButtonHtml += "<div class='col-md-6 text-right'><button class='btn premium-btn'><span>₹ 17600 /-</span><span class='sm-pre'>Premium (Incl.Tax) </span></button></div>";
		            		mainBox += "<div class='col-md-11 col-12 row pro-bor mt-2' id='combo_box_"+combination.id+'_'+innerKey+"'><div class='col-md-6'><div class='custom-control custom-radio custom-control-inline'><input type='radio' name='combo' sum-insured-word='"+selectedSi+"' class='custom-control-input' id='combo"+combination.id+'_'+deductible+"' value='"+combination.id+"' deductible='"+deductible+"'><label class='custom-control-label product-lbl rd-cus' for='combo"+combination.id+'_'+deductible+"'>"+policiesHtml+sumInsureHtml+"</label><i tool-type='combination' selected-product='"+selectedObj.product_name+"' combination-id='"+combination.id+"' deductible='"+deductible+"' onclick='showToolTipModal(this)' class='fa fa-info-circle info-pop'></i></div></div></div>";
            				innerKey ++
            			});
            		}
            	} else {
            		innerKey = 0;
            		comboIdDeductibleArr.push({"combo_box_id":'combo_box_'+combination.id+'_'+innerKey,"combo_id":combination.id,"sum_insured_words":selectedSi,"deductible":"0"});
            		sumInsureHtml += "<span class='prim-color ml-4'>(SI - "+selectedSi+") </span>";
            		// premiumButtonHtml += "<div class='col-md-6 text-right'><button class='btn premium-btn'><span>₹ 17600 /-</span><span class='sm-pre'>Premium (Incl.Tax) </span></button></div>";
            		mainBox += "<div class='col-md-11 col-12 row pro-bor mt-2' id='combo_box_"+combination.id+'_'+innerKey+"'><div class='col-md-6'><div class='custom-control custom-radio custom-control-inline'><input type='radio' name='combo' sum-insured-word='"+selectedSi+"' class='custom-control-input' id='combo"+combination.id+"' value='"+combination.id+"' deductible='0'><label class='custom-control-label product-lbl rd-cus' for='combo"+combination.id+"' >"+policiesHtml+sumInsureHtml+"</label><i tool-type='combination' selected-product='"+selectedObj.product_name+"' combination-id='"+combination.id+"' deductible='0' onclick='showToolTipModal(this)' class='fa fa-info-circle info-pop'></i></div></div></div>";
            	}
            	combinationHtml += mainBox;
            });
            var premiumAllCombinations = getPremiumPerCombination(comboIdDeductibleArr);
            $("#combination-modal-data").html(combinationHtml);
           	$.each(premiumAllCombinations, function(key, premiumObj){
           		$('#'+premiumObj.combo_box_id).append("<div class='col-md-6 text-right'><button class='btn premium-btn'><span>₹ "+premiumObj.totalPremium+" /-</span><span class='sm-pre'>Premium (Incl.Tax) </span></button></div>");
           		$("#"+premiumObj.combo_box_id+" :input").attr('premium',premiumObj.totalPremium);
           	});
           	// $('#resultLoadingNew').hide();
            $("#combination-modal").modal('show');
            prevIndex = currIndex;
        }
        else
        {
        	// console.log("000");
            prevIndex = "";
        }
    }
}
// $('.sum_insured_box').mouseleave(function () {
// 	prevIndex = $(".sum_insured_box").prop('selectedIndex');
// });



function getFamilyDob(type='All'){
	// var type = type;
	
	var dobObj = null;
	$.ajax({
		url: "/get_family_dob",
		type: "POST",	
		async: false,			
		dataType: "json",
		data: {
			type:type
		},
		success: function (response){
			dobObj = response;
			
		}
	})
	return dobObj;
}

function getPremiumPerCombination(combinationArr){
	// 
	var combinationArrWithPremium;
	var combinationArr = combinationArr;
	$.ajax({
		url: "/get_premium_for_combinations",
		type: "POST",	
		async: false,			
		dataType: "json",
		data: {
			combinationArr:combinationArr
		},
		success: function (response){
			combinationArrWithPremium = response;
			// 
		}
	})
	return combinationArrWithPremium;
}

function getSelectionObj(){
	var selectedCombination = {
		adults:$('input[name="adults"]:checked').val(),
		childs:$('input[name="childs"]:checked').val(),
		mainProduct:$(".product_button.cover-act-btn").val(),
		sumInsured:$(".sum_insured_box").val(),
		productCombination:null
	}
	return selectedCombination;
}

$('#dob_form').validate({
	rules:{
        // Self_dob: {
        //     required: true
        // },
        "adult_dob[2]": {
            required: true
        },
        "kid_dob[1]": {
            required: true
        },
        "relation[1]": {
            required: true
        },
        "kid_dob[2]": {
            required: true
        },
        "relation[2]": {
            required: true
        },
	},
	messages:{
		// Self_dob: {
  //           required: "Self DOB is required"
  //       },
        "adult_dob[2]": {
            required: "Spouse DOB is required"
        },
        "kid_dob[1]": {
            required: "Kid 1 DOB is required"
        },
        "relation[1]": {
            required: "Kid 1 Relation is required"
        },
        "kid_dob[2]": {
            required: "Kid 2 DOB is required"
        },
        "relation[2]": {
            required: "Kid 2 Relation is required"
        },
	},
    submitHandler: function (form) {
    	
    	var form = $("#dob_form").serialize();
    	$.ajax({
    		url: "/add_family_dob",
			type: "POST",
			async: false,
			data: form,
			dataType: 'json',
			success: function (response) {
				if(response.status == 'success'){
					$('#dob-modal').modal('hide');
					selectedComboPremium = getSelectedComboPremiumBreakup();
					setPremiumWithBreakup(selectedComboPremium);
					previousCheckedRadio = $('input[name=childs]:checked');
				} else if(response.status == 'error') {
					swal("Alert",response.message, "warning");
				}
			}
    	});      
    }
});

function submitDob(elem){
	$('#dob_form').valid();
}

function submitCombination(elem){
	var checkedComboElement = $('input[name="combo"]:checked');
	if(checkedComboElement.length == 0){
		swal("Alert","Please select combination!!", "warning");
	}
	var selectedCombinationId = checkedComboElement.val();
	var selectedDeductible = checkedComboElement.attr('deductible');
	var totalPremium = checkedComboElement.attr('premium');
	
 	$.ajax({
		url: "/update_combo_selection",
		type: "POST",
		async: false,
		data: {
			totalPremium:totalPremium,
			selectedCombinationId:selectedCombinationId,
			selectedDeductible:selectedDeductible
		},
		dataType: 'json',
		success: function (response) {
			if(response.status == 'success'){
				$("#combination-modal").modal('hide');
				employeeToProductOnLoad = getEmployeeToProductData();
				selectedComboPremium = getSelectedComboPremiumBreakup();
				setPremiumWithBreakup(selectedComboPremium);
				occupationLogicBoxShowHide();
				removeOccupationOrAi();
			}
		}
	});
}

function validateConfirmation(){
	var occupationBox = $("[name='occupation']");
	var annualIncomeBox = $("[name='annual_income']");
	var occupationData = null;
	var aiData = null;
	if(occupationBox.length){
		occupationData = {
			'mandatory': occupationBox.attr('mandatory'),
			'value': occupationBox.val(), 
			'riskClass' : occupationBox.find('option:selected').attr('risk-class')
		};
	}
	if(annualIncomeBox.length){
		aiData = {
			'mandatory': annualIncomeBox.attr('mandatory'),
			'value': annualIncomeBox.val()
		};
	}
	var combinedData = {'occupationData':occupationData,'aiData':aiData};
	$.ajax({
		url: "/validate_confirmation_retail",
		type: "POST",
		async:false,
		data: combinedData, 
		dataType: "json",
		success: function (response) 
		{
			if(response.status == 'error'){
				swal({
	                title: "warning",
	                text: response.message[0],
	                type: "warning",
	                showCancelButton: false,
	                confirmButtonText: "Ok",
	                closeOnConfirm: true
	            });
			} else if(response.status == 'success') {
				localStorage.clear();
				location.replace("/retail_enrollment");
			}
		}
	});
}

function confirm()
{
	if($('#dec_family_check:checked').length <= 0)
	{
		$("#modal-frown").modal("show");
		return false;
	}
	var all_data = $("#know_premium").serialize();
	$.ajax({
		url: "/create_know_premium",
		type: "POST",
		data: all_data,
		dataType: "json",
		success: function (response) 
		{
			if(response.statusErr == 1)
			{			
				ajaxindicatorstart('We are quickly gathering your information <br> to get you started');
				localStorage.clear();
				location.replace("/retail_enrollment");
			}
			else
			{
				swal({
                    title: "warning",
                    text: "Please select Propoer Data",
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonText: "Ok",
                    closeOnConfirm: true
                });
			}
		}
	});
}
