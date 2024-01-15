	var payment_details_submit = true;
   var get_premium = '';
    var total_premium = 0;
    var apply_onload = 0;
    var proposal_created = 0;
    var show_popup = 0;
    var famconsCount =0;
    var memberCount = 0;
    var deletecalled = 0;
    var proposal_payment_done = 0;
    var TELE_HEALTHPROINFINITY_GHI = '473';
	var TELE_HEALTHPROINFINITY_GPA = '474';
	var TELE_HEALTHPROINFINITY_GHI_GPA = '473,474';
	var TELE_HEALTHPROINFINITY_GHI_ST = '475';
	var TELE_HEALTHPROINFINITY_PRODUCT_NAME = 'Tele - Health Pro Infinity';
	var membersCovered = [];
   $(window).on('load', function() {
	   var boxlen = $('.commonCls').length;
		if(boxlen > 0){
			$("#plan_name").attr('disabled', 'disabled');
			if($('#plan_name').val() == 'T03'){
				$("#sum_insures").attr('disabled', 'disabled');
				$("#patFamilyConstruct").attr('disabled', 'disabled');
			}
		}	
	})

   function set_session(){
   	$.ajax({
				url: "/tele_set_session",
				type: "POST",
				data: {"lead_id": $('#hidden_lead_id').val()},
				async: false,
				dataType: "json",
				success: function (response) {

					
				}
			});
   }

   function unique(array){
  return array.filter(function(el, index, arr) {
      return index == arr.indexOf(el);
  });
}

   /*setTimeout(function() {  }, 2000);*/
/*$(document).ajaxSend(function(event, request, settings) {
    		//debugger;
    		//alert(event);
	        $.ajax({
				url: "/tele_set_session",
				type: "POST",
				data: {"lead_id": $('#hidden_lead_id').val()},
				async: false,
				dataType: "json",
				success: function (response) {

					
				}
			});
	    });*/

	$("#famConSelected").validate({
		ignore: ".ignore",
		rules: {
			spouse_rel: {
			required: true
			},
			spouse_date_birth: {
			required: true
			},
			kid1_rel: {
			required: true
			},

			kid1_date_birth: {
			required: true
			},
			kid2_rel: {
			required: true
			},
			kid2_date_birth: {
			required: true
			},
		},
		messages: {
		
		},
		submitHandler: function (form) {
			//alert($("#patFamilyConstruct").val());
			debugger; 
			var form = $("#famConSelected").serialize() + "&empId=" + $('#empidaxistele').val()+"&family_construct="+$("#patFamilyConstruct").val()+"&adult_selected="+$('input[name="adult_fr_id"]:checked').val()+"&sum_insures="+$("#sum_insures").val();
			//alert(form);
			$.post("/tele/store_family_dobs", form, function (e) {
				e = JSON.parse(e);
				if(e.message != 'true'){ //error in validation
					swal("Alert", e.message);
				}else{//call premium box
					$('#helathproXlFamilyModal').modal("hide");
					$('#helathproXl').html(e.html);
					$("#helathproXlModal").modal("show");
					membersCovered = e.membersCovered;
					//define allowed family relation
					/*var allowedRel = [0,1,2,3];
					$.each(allowedRel, function( index, value ) {
					  	if(membersCovered.toString().indexOf(value) == -1){
					  		//remove from relation with proposer dropdown
					  		$("#family_members_id1 option[value='"+value+"']").hide();
					  	}else{
					  		$("#family_members_id1 option[value='"+value+"']").show();
					  	}
					});*/
				}
			});
		}
	});
    $(document).ready(function () 
    {
    	/* date of birth*/
		    $("#kid2_date_birth").datepicker({
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


		    $("#spouse_date_birth").datepicker({
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

		    $("#kid1_date_birth").datepicker({
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

		    
    	$(document).on("click",".validateFamCons",function(){
		var cons = $('#patFamilyConstruct').val().split("+");
		
		if(cons[0] == '1A'){
			if($('input[name="adult_fr_id"]:checked').val() == undefined){
				swal("Alert","Please Select Adult","warning");
				return false;
			}else{
				if($('input[name="adult_fr_id"]:checked').val() == 0){
					$('#spouse_rel').addClass('ignore');
					$('#spouse_date_birth').addClass('ignore');
				}else{
					$('#spouse_rel').removeClass('ignore');
					$('#spouse_date_birth').removeClass('ignore');
				}
			}
		}else{
			$('#spouse_rel').removeClass('ignore');
			$('#spouse_date_birth').removeClass('ignore');
		}

		if(cons[1] != undefined){
			if(cons[1] == '1K'){
				$('#kid2_rel').addClass('ignore');
				$('#kid2_date_birth').addClass('ignore');
			}else{
				$('#kid1_rel').removeClass('ignore');
				$('#kid1_date_birth').removeClass('ignore');
				$('#kid2_rel').removeClass('ignore');
				$('#kid2_date_birth').removeClass('ignore');
			}

		}else{
			$('#kid1_rel').addClass('ignore');
			$('#kid1_date_birth').addClass('ignore');
			$('#kid2_rel').addClass('ignore');
			$('#kid2_date_birth').addClass('ignore');
		}
	})

	$(document).on("click",".xlmodalvalidate",function(){
		if($('input[name="policy_selection"]:checked').val() == undefined){
			swal("Alert","Please Select Plan","warning");
		}else{

			var premium = $('input[name="policy_selection"]:checked').data('premium');
			var deductable = $('input[name="policy_selection"]:checked').data('deductable');
			var policy_detail_id = $('input[name="policy_selection"]:checked').val();
			$('#premium').val(premium);
			$('#helathproXlModal').modal("hide");
			if(policy_detail_id == TELE_HEALTHPROINFINITY_GHI_GPA){
				$('.occupation_label').show();
				$('#occupation').removeClass('ignore');
				$("#annual_income_label").show();
				annual_income_hide_show();
			}else{
				$('.occupation_label').hide();
				$('#occupation').addClass('ignore');
				$("#annual_income_label").hide();
				annual_income_hide_show();
			}
			// alert(premium +"--"+deductable+"---"+policy_detail_id);
			$('#hidden_deductable').val(deductable);
			$('#hidden_policy_id').val(policy_detail_id);
			/*if(policy_detail_id == TELE_HEALTHPROINFINITY_GHI){
				$("input[name='GPA_optional'][value='No']").prop('checked',true);
				$('#benifit_6s').hide();
				$('.total_premium').hide();
			}else if(policy_detail_id == TELE_HEALTHPROINFINITY_GHI_GPA){
				$("input[name='GPA_optional'][value='Yes']").prop('checked',true);
				$('#benifit_6s').show();
				$('.total_premium').show();
				//add_gci_name();
			}else if(policy_detail_id == TELE_HEALTHPROINFINITY_GHI_ST){
				$('#benifit_6s').hide();
				$('.total_premium').hide();
			}	*/

			if($('.commonCls').length > 0){
		        	apply_button();
		        	console.log('---->'+deletecalled);
		        	if(deletecalled == 0){
		        		if(famconsCount >= memberCount){
			        		swal("Alert", "Below Member Details Section would get modified or deleted. Press OK to Proceed", "warning");

			        	}else{
			        		famconsCount++;
			        	}
		        	}else{
		        		deletecalled = 0;
		        	}
		        	

		        }
		        
				
				var family_construct = $('#patFamilyConstruct option:selected').val();
				var result = family_construct.split('+');
				
				var add_length = [];
				add_length.push(parseInt(result[0]));
				add_length.push(parseInt(result[1]));
				debugger;
				if(!$('#patFamilyConstruct').is('[disabled=disabled]')){
					// swal("Alert","Below Member Details Section would get modified or deleted. Press OK to Proceed.");
					var boxlen = $('.commonCls').length;	
					var icount;
					if(boxlen == ''){
						icount = 0
					}else{
						icount = 0+boxlen;
					}
					//$("#add_more").empty();
					
					
					for (i = icount; i < sum(add_length); i++) {
						common_append(i);
					}
					$("#add_btn_view").show();
				}
				//alert(unique(membersCovered));
				//getRelationHelathpro(unique(membersCovered));		
		}
	})
    	$("input[name='adult_fr_id']").click(function(){
			//alert($(this).val());
			
			var cons = $("#patFamilyConstruct").val().split("+");
			if(cons[0] == '1A'){
				if($('input[name="adult_fr_id"]:checked').val() == 1){
					$(".spouse_div").show();
				}else{
					$(".spouse_div").hide();
				}
				$(".check_adult").show();
			}else{
				$(".spouse_div").show();
				$(".check_adult").hide();
			}
			
			if(cons[1] != undefined){
				if(cons[1] == '1K'){
	    			$('.kid1_div').show();
	    		}else{
	    			$('.kid1_div').show();
	    			$('.kid2_div').show();
	    		}
			}else{
				$('.kid1_div').hide();
	    		$('.kid2_div').hide();
			}

		})
    		
    	$(document).on("change","#salutation",function(){
		    if($(this).val() == 1){
		    	$('#gender1').val('Male');
		    }else{
		    	$('#gender1').val('Female');
		    }
		})

		$(document).on("click","#edit_payment",function(){
			$("#edit_payment").hide();
			$("#sub_isposition").attr("disabled", false);
			$("#disposition").attr("disabled", false);
		})

		
		$(document).on("click","#edit_nominee",function(){
			$('#edit_nominee').hide();
			$('#nominee_relation').attr('disabled',false);
			$('#nominee_fname').attr('readonly',false);
			$('#nominee_lname').attr('readonly',false);
			$('#nominee_salutation').attr('disabled',false);
			$('#nominee_gender').attr('readonly',false);
			$('#nomineedob').attr('readonly',false);
			$('#nomineedob').attr('disabled',false);
			$('#nominee_contact').attr('readonly',false);
			$('#nominee_email').attr('readonly',false);
		})
    	$(document).on("click","#edit_btn_custsection",function(){
    		$('#edit_btn_custsection').hide();
    		$('#update_emp').show();
    		$('#firstname').focus();
    		if(proposal_created == 1){
    			$('#firstname').attr('readonly',false);
	    		$('#lastname').attr('readonly',false);
    		}else{
    			$('#salutation').attr('disabled',false);
	    		$('#firstname').attr('readonly',false);
	    		$('#lastname').attr('readonly',false);
	    		//$('#gender1').attr('disabled',false);
	    		$('#dob1').attr('disabled',false);
	    		$('#dob1').attr('readonly',false);
	    		//$('#mob_no').attr('readonly',false);
	    		//$('#email').attr('readonly',false);
	    		//$('#gender1').removeClass("dis-col");
				//$('#mob_no').removeClass("dis-col");
				//$('#email').removeClass("dis-col");
    		}
    		
    		
    	})

    	/*healthpro chnages for GPA+GCI risk class 1 and class 2 occupation is allowed*/
    	
    	

		
		$(document).on("change","#sub_isposition",function(){
			var z = $(this);
			enable_disable_proposal(z);
			
		});
		$(document).on("change","#disposition",function(){
			var z = $(this);
			get_sub_disposition(z);
		});
    	$(document).on("change","#occupation",function(){
			var plan_name = $("#plan_name").val();
			if(plan_name == 'T03'){
				$.ajax({
					url: "/tele_verify_occupation",
					type: "POST",
					data: {"occupation_id": $(this).val()},
					async: false,
					dataType: "json",
					success: function (response) {

						if(response.status == 'error'){
							$("#occupation")[0].selectedIndex = 0;
							swal("Alert","Occupation not allowed ", "warning");
						}
					}
				});	
			}
			
		})

		
		$("#patFormSubmit").on("click",function(){
			
			//healthpro chnages annual income validation
			return annual_income_logic();			
			//healthpro changes ends
		})
    	/*ends here*/
		//siddhi
		$.validator.addMethod("notEqual", function(value, element, param) {
			if(value != 2 && value != 3){
				//alert(value);
			  return this.optional(element) || $(param).not(element).get().every(function(item) {
				  return $(item).val() != value;
			  });
		  }else{
			  return true;
		  }
		}, "Please select different member");

		//siddhi
		$.validator.addClassRules("family_members_id1", {
		  notEqual: ".family_members_id1"
		});

		
		$('body').on("keyup", "#avName,#agent_name,#tl_name,#am_name,#om_name,#firstname,#lastname,.first_name1,.last_name1,#nominee_fname,#nominee_lname", function () {
			if ($(this).val().match(/[^a-zA-Z ]/g)) {
			$(this).val($(this).val().replace(/[^a-zA-Z ]/g, ""));
		}
		$(this).val($(this).val().toUpperCase());
	});
   /*changed last_name and customer lastname regex*/
	$('body').on("keyup", ".first_name,.last_name,.lastname", function () {
			if ($(this).val().match(/[^a-zA-Z ]/g)) {
			$(this).val($(this).val().replace(/[^a-zA-Z ]/g, ""));
		}
		$(this).val($(this).val().toUpperCase());
	});
	
	
	
			$('.bd-example-modal-md').removeClass('hide');
			var selectChange = "";
			var emp_id = $("#empIdHidden").val() || "";
			var parent_ids = $("#parentIdHidden").val();
			
			
			$.ajax({
				url: "/tele_master_salutation",
				type: "POST",
				async: false,
				dataType: "json",
				beforeSend: function() {
			        set_session();
			    },
				success: function (response) 
				{

					$("#salutation").empty();
					$("#salutation").append("<option value = ''>Select salutation</option>");
					for (i = 0; i < response.length; i++) 
					{

						$("#salutation").append("<option  value =" + response[i]['s_id'] + ">" + response[i]['salutation'] + "</option>");


					}
				}
		    });
			  $.ajax({
						url: "/tele_axis_location",
						type: "POST",
						async: false,
						dataType: "json",
						success: function (response) 
						{

							$("#axis_location").empty();
							$("#axis_location").append("<option value = ''>Select Axis Location</option>");
							for (i = 0; i < response.length; i++) 
							{

								$("#axis_location").append("<option  value =" + response[i]['axis_loc_id'] + ">" + response[i]['axis_location'] + "</option>");


							}
						}
				    });
			$.ajax({
				url: "/tele_employee_data",
				type: "POST",
				async: false,
				data: {},
				dataType: "json",
				success: function (response) 
				{

					if(response.email == '')
					{
						$("#email").attr("readonly", false); 
					}
					if(response.emp_pincode == '' || response.emp_pincode == '.')
					{
						$("#pin_code").attr("readonly", false); 
					}
					$("#firstname").val(response.emp_firstname.toUpperCase());
					$("#lastname").val(response.emp_lastname.toUpperCase());
					$("#panCard").val(response.pancard);
					$("#annual_income").val(response.annual_income);
					$("#occupation").val(response.occupation);

					try {
					$("#addCard").val(response.adhar);
					}catch(e) {

					}
					$("#lead_id_product").html(response.lead_id);
					$("#lead_id").val(response.lead_id);
					$("#comAdd").val(response.address);
					$("#hidden_policy_id").val(response.pid);
					$("#hidden_deductable").val(response.deductable);
					$("#gender1").val(response.gender);
					$("#dob1").val(response.bdate);
					$("#dob1").attr('disabled', 'disabled');
					$("#mob_no").val(response.mob_no);
					$("#email").val(response.email);
					$("#saksham_id").val(response.saksham_id);
					$("#salutation option").filter(function(){
					return $(this).text() === response.salutation ? $(this).prop("selected", true) : false;
					});
					$("#pin_code").val(response.emp_pincode);
					$("#city").val(response.emp_city);
					$("#state").val(response.emp_state);
					if (response.ISNRI == 'Y') 
					{

						$("#comAdd").attr("readonly", false);

					}
					// alert(response.address+'----'+response.emp_pincode);
					if(response.address != null && response.emp_pincode != null){
						$('#hidden_customer_section').val(1);
					}

				}
			});

			$("#dob").datepicker({
				dateFormat: "dd-mm-yy",
				prevText: '<i class="fa fa-angle-left"></i>',
				nextText: '<i class="fa fa-angle-right"></i>',
				changeMonth: true,
				changeYear: true,
				yearRange: "-100Y:-18Y",
				maxDate: "-18Y",
				//minDate: "-55Y +1D"
			});
 
			$("#dob1").datepicker({
				dateFormat: "dd-mm-yy",
				prevText: '<i class="fa fa-angle-left"></i>',
				nextText: '<i class="fa fa-angle-right"></i>',
				changeMonth: true,
				changeYear: true,
				yearRange: "-100Y:-18Y",
				maxDate: "-18Y",
				minDate: "-55Y +1D"

			});

			$("#nomineedob").datepicker({
				dateFormat: "dd-mm-yy",
				prevText: '<i class="fa fa-angle-left"></i>',
				nextText: '<i class="fa fa-angle-right"></i>',
				changeMonth: true,
				changeYear: true,
				yearRange: "-100Y:-0Y",
				maxDate: "-0Y",
				//minDate: "-55Y +1D"
			});

			$('#preferred_contact_time').timepicker({
				timeFormat: 'h:i p',				 
				showInputs: false,
				showMeridian: false ,
				minuteStep: 1,
				defaultTime: null,
			});

			

       
			$.ajax({
				url: "/tele_agent_details",
				type: "POST",
				async: false,
				dataType: "json",
				success: function (response) 
				{

					
					$("#avCode").val(response.agent_id);
					$("#avName").val(response.agent_name.toUpperCase());

				}
			});
										  
			 $.ajax({
						url: "/tele_master_nominee",
						type: "POST",
						async: false,
						dataType: "json",
						success: function (response) 
						{

							$("#nominee_relation").empty();
							$("#nominee_relation").append("<option value = ''>Select Nominee</option>");
							for (i = 0; i < response.length; i++)
							{

								$("#nominee_relation").append("<option  data-opt = "+ response[i]['gender'] +" value =" + response[i]['nominee_id'] + ">" + response[i]['nominee_type'] + "</option>");


							}
						}
					});

			$("#plan_name").on("change",function(){
				//debugger;
				if($('#plan_name').val() != ''){
					var product_name = $(this).find("option:selected").text();
					//console.log('--->'+product_name+'<------');
					$("#pr_name").html(product_name);
					$("#pr_name").html('Axis Tele Inbound Affinity Portal for ABHI');
				}
				
				$('#occupation').prop('selectedIndex',0);

				 $.ajax({
			
						url: "/tele_get_declaration",
						type: "POST",
						data: {product_id : $('#plan_name').val()},
						
						success: function (data) 
						{debugger;
								
								if(data!='')
								{
									var data = JSON.parse(data);
									var ghd_content = $('#ghd-table').html();
									if(product_id == 'T01'){
									data.ghd.replace('/&lt;/g', '<').replace('/&gt;/g', '>');
									$('#policy_declare_new').html(data.ghd);
									$('.myremark').val('');
									
									
									}else{
										$('#ghd-table').html(ghd_content);
									}
									$('#health_declare').show();
									$('#policy_declare').html(data.employee_declaration);
						  
								}

						}
					});
					$(".sub_isposition").trigger('change');
				if($(this).val() == 'T01'){
					
					
					$('.GCIOptionalDiv').show();
				}else{
					$('.GCIOptionalDiv').hide();
				}
				var product_id = $(this).val();
				$.ajax({
						url: "/tele_suminsured_data",
						type: "POST",
						data: {product_id:product_id},
						async: false,
						dataType: "json",
						success: function (response) 
						{						
						  
							if (response.length != "") {

								$("#sum_insure").empty();
								$("#sum_insures").empty();
							
								$("#sum_insure").append(
								"<option value = ''>Select Sum insured</option>"
								);
								$("#sum_insures").append(
								"<option value = ''>Select Sum insured</option>"
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

								/*healthpro changes */
								var policyid = [];
								if (response[i].combo_diff_construct) {
									response[i].combo_diff_construct.forEach(function (e) {
									
									/*if (e['combo_flag'] == 'Y') {*/
									if (!arr.includes(e['sum_insured'])) {
									arr.push(e['sum_insured']);
									policyid.push(e['policy_sub_type_id']);
									setMasPolicy("family_construct", e);
									} else{
										if (!policyid.includes(e['policy_sub_type_id'])){
											setMasPolicy("combo_diff_construct", e);
										}
									}
									/*else {
									}.
									}*/

									});
								}
								/*healthpro chnages ends here*/

								if (response[i].memberAge) {


									response[i].memberAge.forEach(function (e) {
									if (e['combo_flag'] == 'Y') {
									if (!arr.includes(e['sum_insured'])) {
									arr.push(e['sum_insured']);
									setMasPolicy("memberAge", e);
									} else {

									}
									}
									else {
									setMasPolicy("memberAge", e);
									}
									});
								}
								/*end*/
								}


							}
							get_all_data();

						}
					});
			})

   
				 $.ajax({
        url: "/tele_policy_data_new",
        type: "POST",
        data: {},
        async: false,
        dataType: "json",
        success: function(response) {
			
            $("#benifit_4s").css("display", "block");
            $("#plan_name").html(response);
        }
    });

    $.ajax({
				url: "/tele_policy_data",
				type: "POST",
				data: 
				{},
				async: false,
				dataType: "json",
				success: function (response) 
				{
					//debugger;
					
						$("#benifit_4s").css("display", "block");
						var i;
						for (i = 0; i < response.length; i++) 
						{
							//console.log(response[i].product_name+'---'+response[i].master_policy_no);

								$("#pr_name").html(response[i].product_name);
								$("#pr_name").html('Axis Tele Inbound Affinity Portal for ABHI');
								$("#plan_name").val(response[i].product_code);
								$("#master_policy_number").val(response[i].master_policy_no);
								if (response[i].policy_sub_type_id == 1) 
								{

									$(".personal_accident").text(response[i].policy_sub_type_name);
									$("#benifit_4s").css("display", "block");

									$("#subtype_text").val(response[i].policy_sub_type_id);

									if ((response[i].max_adult > 1 && response[i].fr_id == 1) ||response[i].fr_id == 0)
									{
											$("#family_members_id"+j).append('<option data-opt="' +response[i].gender_option +'" value="'+response[i].fr_id +'">' +response[i].fr_name +"</option>");
									} 
									else if (response[i].max_adult == 2 &&response[i].max_child == 0 &&(response[i].fr_id == 4 ||response[i].fr_id == 5 ||response[i].fr_id == 6 ||response[i].fr_id == 7)) 
									{
											$("#family_members_id"+j).append('<option data-opt="' +response[i].gender_option +'" value="' +response[i].fr_id +'">' +response[i].fr_name +"</option>");
									} 
									else if (response[i].max_adult == 4 &&response[i].max_child == 0 &&response[i].fr_id == 5 ||response[i].fr_id == 6 ||response[i].fr_id == 7)
									{
											$("#family_members_id"+j).append('<option data-opt="' +response[i].gender_option +'" value="' +response[i].fr_id +'">' +response[i].fr_name +"</option>");
									} 
									else if(response[i].max_adult > 2 &&(response[i].fr_id == 4 ||response[i].fr_id == 5 ||response[i].fr_id == 6 ||response[i].fr_id == 7 ||response[i].fr_id == 1 ||response[i].fr_id == 0))
									{
											$("#family_members_id"+j).append('<option data-opt="' +response[i].gender_option +'" value="' +response[i].fr_id +'">' +response[i].fr_name +"</option>");
									}
									else if (response[i].fr_id == 2 || response[i].fr_id == 3)
									{
											$("#family_members_id"+j).append('<option data-opt="' +response[i].gender_option +'" value="' +response[i].fr_id +'">' +response[i].fr_name +"</option>");
									}
								}
								
						}
				}
							   
								
			});
					
			
					  $.ajax({
			
						url: "/tele_get_declaration",
						type: "POST",
						data: {product_id : $('#plan_name').val()},
						
						success: function (data) 
						{
debugger;
								if(data!='')
								{
									var data = JSON.parse(data);
									var ghd_content = $('#ghd-table').html();
									data.ghd.replace(/&lt;/g, '<').replace(/&gt;/g, '>');
									if(product_id == 'T01'){
									$('#policy_declare_new').html(data.ghd);
									$('.myremark').val('');
									}else{
										$('#ghd-table').html(ghd_content);
									}
									$('#health_declare').show();
									$('#policy_declare').html(data.employee_declaration);
						  
								}

						}
					});	
				 var product_id = $('#plan_name').val();
    //alert(product_id);
    if(product_id != '' || product_id != null){
    	$.ajax({
						url: "/tele_suminsured_data",
						type: "POST",
						data: {product_id:product_id},
						async: false,
						dataType: "json",
						success: function (response) 
						{						
						  
							if (response.length != "") {

								$("#sum_insure").empty();
								$("#sum_insures").empty();
							
								$("#sum_insure").append(
								"<option value = ''>Select Sum insured</option>"
								);
								$("#sum_insures").append(
								"<option value = ''>Select Sum insured</option>"
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

								/*healthpro changes */
								var policyid = [];
								if (response[i].combo_diff_construct) {
									response[i].combo_diff_construct.forEach(function (e) {
									
									/*if (e['combo_flag'] == 'Y') {*/
									if (!arr.includes(e['sum_insured'])) {
									arr.push(e['sum_insured']);
									policyid.push(e['policy_sub_type_id']);
									setMasPolicy("family_construct", e);
									} else{
										if (!policyid.includes(e['policy_sub_type_id'])){
											setMasPolicy("combo_diff_construct", e);
										}
									}
									/*else {
									}.
									}*/

									});
								}
								/*healthpro chnages ends here*/

								if (response[i].memberAge) {


									response[i].memberAge.forEach(function (e) {
									if (e['combo_flag'] == 'Y') {
									if (!arr.includes(e['sum_insured'])) {
									arr.push(e['sum_insured']);
									setMasPolicy("memberAge", e);
									} else {

									}
									}
									else {
									setMasPolicy("memberAge", e);
									}
									});
								}
								/*end*/
								}


							}

							get_all_data();

						}
					});
    }
			 /*$.ajax({
						url: "/tele_suminsured_data",
						type: "POST",

						data: {},
						async: false,
						dataType: "json",
						success: function (response) 
						{
							
						  
									if (response.length != "") {

									$("#sum_insure").empty();
									$("#sum_insures").empty();
								
									$("#sum_insure").append(
									"<option value = ''>Select Sum insured</option>"
									);
									$("#sum_insures").append(
									"<option value = ''>Select Sum insured</option>"
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

									if (response[i].memberAge) {
									response[i].memberAge.forEach(function (e) {
									setMasPolicy("memberAge", e);
									});
									}
									}


									}
									get_all_data();

						}
					});*/
				  
			  /*$.ajax({
						url: "/tele_member_declare_data",
						type: "POST",
						dataType: "html",
						data: { },
						success: function (response)
						{
							$(".member_declare_benifit_4s").html(response)
						 
						}
					});*/
					
				
								
		
	/* ghd on load C question show */	
					
					
    });
	$(document).on("change", "#axis_location", function () 
	{
		var axis_loc = $(this).val();
		 $.ajax({
					url: "/tele_axis_vendor",
					type: "POST",
					async: false,
					data:{'axis_loc':axis_loc},
					dataType: "json",
					success: function (response) 
					{

					$("#axis_vendor").empty();
					$("#axis_vendor").append("<option value = ''>Select Axis Vendor</option>");
					for (i = 0; i < response.length; i++) 
					{

					$("#axis_vendor").append("<option  value =" + response[i]['axis_vendor_id'] + ">" + response[i]['axis_vendor'] + "</option>");


					}
							}
				});	
		
	});
	/*$(document).on("change", "#axis_vendor", function () 
	{
		var axis_vendor = $(this).val();
		 $.ajax({
					url: "/tele_axis_lob",
					type: "POST",
					async: false,
					data:{'axis_vendor':axis_vendor},
					dataType: "json",
					success: function (response) 
					{

					$("#axis_lob").empty();
					$("#axis_lob").append("<option value = ''>Select Axis LOB</option>");
					for (i = 0; i < response.length; i++) 
					{

					$("#axis_lob").append("<option  value =" + response[i]['axis_lob_id'] + ">" + response[i]['axis_lob'] + "</option>");


					}
							}
				});	
		
	});*/
	$(document).on("change", "select[name='sum_insure']", function () 
	{


		apply_button();
	  
			selectChange = $(this).closest("form");
			var tax_with_premium = selectChange.find('select[name=sum_insure] option:selected').data("id");
			var select_sum_insured = selectChange.find('select[name=sum_insure] :selected');
			 selectChange.find('select[name=familyConstruct]').empty();
			if(select_sum_insured.val() == '')
			{
				selectChange.find('select[name=familyConstruct]').html('<option value="" selected>Select</option>');
			}
			if (select_sum_insured.attr("data-type") == "family_construct" || select_sum_insured.attr("data-type") == "family_construct_age")
			{
				$("select[name=familyConstruct]").empty();
				$("select[name=familyConstruct]").html('<option value="" selected>Select</option>');

				$.post("/tele_family_construct", 
				{
				  "policyNo": select_sum_insured.attr("data-policyno"),
				  "sumInsured": select_sum_insured.val(),
				  "table": select_sum_insured.attr("data-type"),
				}, function (e)
				{
					selectChange.find('select[name=familyConstruct]').empty();
				   e = JSON.parse(e);
				  
				    if (e) 
					{
						$("select[name=familyConstruct]").html('<option value="" selected>Select</option>');
						e.forEach(function (e1) 
						{
							
						  selectChange.find('select[name=familyConstruct]').append("<option value='" + e1.family_type + "' data-premium='" + e1.PremiumServiceTax + "'>" + e1.family_type + "</option>");
						});

					 
					}
					/*added helathpro changes*/
					$("#patFamilyConstructDiv").show();
					/*end*/
					/*healthpro changes show annualincome if suminsure > 10 lac*/
						if(select_sum_insured.val() > 1000000){
							$("#annual_income_label").show();
							$("#occupation_label").show();
							$("#annual_income").removeClass("ignore");
							$("#occupation").removeClass("ignore");
							
						}else{
							$("#annual_income_label").hide();
							$("#occupation_label").hide();
							$("#annual_income").addClass("ignore");
							$("#occupation").addClass("ignore");
						}
					/*healthpro changes end*/
				 
				});


			}else if(select_sum_insured.attr("data-type") == "memberAge"){
				$("select[name=familyConstruct]").empty();
				$("select[name=familyConstruct]").html('<option value="" selected="">Select</option><option value="1A">1A</option><option value="2A">2A</option>');
				$("#patFamilyConstructDiv").show();
				/*healthpro changes show annualincome if suminsure > 10 lac*/
					if(select_sum_insured.val() > 1000000){
						$("#annual_income_label").show();
						$("#occupation_label").show();
						$("#annual_income").removeClass("ignore");
						$("#occupation").removeClass("ignore");
						
					}else{$("#occupation_label").show();
						$("#annual_income_label").hide();
						$("#occupation_label").hide();
						$("#annual_income").addClass("ignore");
						$("#occupation").addClass("ignore");
					}
				/*healthpro changes end*/
			}

			if($('select[name=sum_insure]').val() != '' && $('select[name=sum_insure] :selected').attr("data-policyno") != '' && $('select[name=familyConstruct]').val() != '' && $('#plan_name').val() != ''){
				$.post("/tele_get_premium", { "sum_insured":  $('select[name=sum_insure]').val(), "policy_detail_id": $('select[name=sum_insure] :selected').attr("data-policyno"), "family_construct":  $('select[name=familyConstruct]').val(),"gci_optional":  $('input[name=GCI_optional]:checked').val(),"product_id" : $('#plan_name').val() }, function (e)
				{
					//debugger;
					$("#premiumModalHidden").val(e);
					e = JSON.parse(e);
					
					var premium = 0;
					$("#premiumModalBody").html("");
					var str = '';
					e.forEach(function (e1) {
						//console.log(e1);
						str += "<div style='display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;'><div><span style='color:#da8085;'> Name:  " + "<br/></span><span style='color:#000;'>" + e1.policy_sub_type_id + "</span></div>";
						str += "<div><span style='color:#da8085;'> Sum Insured </span>" + "<br/><span style='color:#000;'>" + e1.sum_insured + "</span></div>";
						str += "<div><span style='color:#da8085;'> Premium" + "</span><br/><span style='color:#000;'>" + parseFloat(e1.PremiumServiceTax).toFixed(2) + "</span></div></div>";
						
						$("#premiumModalBody").append(str);
						premium += parseFloat(e1.PremiumServiceTax);
					});
					//console.log(get_premium+']]]]]');
					if(str != ''){
						get_premium = str;
						$('#premiumModalBody').html(str);  
						
					}
					 $("#premium").val(premium.toFixed(2));
					 if(parseInt(premium) != 0 && !isNaN(premium)){
					 	total_premium = premium;
						$('#premium').val(premium);  
						
					}
				});
			}
    });
	
	
	function enable_disable_proposal(data){
		if($('#plan_name').val()){
			if($('#plan_name').val() == 'T01'){
				$("#policy_declare_new").show();
				$("#myGhdMember").hide();
			}else{
				$("#policy_declare_new").hide();
				$("#myGhdMember").show();
			}
			$("#show_hide_proposal").show();
		if($(data).val() == 45){
			$("#show_hide_proposal").show();
		}else{
			$("#show_hide_proposal").hide();
		}
		if($('#disposition').val() == 29){
			$('#dobdate').removeClass('ignore');
			$('#preferred_contact_time').removeClass('ignore');
		}else{
			$('#dobdate').addClass('ignore');
			$('#preferred_contact_time').addClass('ignore');
		}
		}
	}

	function getRelationHelathpro(){
		var fam_rel = $('select[name=familyConstruct]').val().split("+");
			var opt = $(".family_members_id1 option");
			
			if($('select[name=familyConstruct]').val() != '1A+1K' || $('select[name=familyConstruct]').val() != '1A+2K'){
				opt.each(function(e) {
					$(this).css("display", "block");
				});
			  	/*kids & self*/
				 if(fam_rel[0].substr(0,1) == '1')
				 {
					opt.each(function(e) {
						if(this.value == 0 || this.value == 1) {
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
	}

	function getRelation(){	
		debugger;
			var fam_rel = $('select[name=familyConstruct]').val().split("+");
			var opt = $(".family_members_id1 option");
			
			if($('select[name=familyConstruct]').val() != '1A+1K' || $('select[name=familyConstruct]').val() != '1A+2K'){
				opt.each(function(e) {
					$(this).css("display", "block");
				});
			  	/*kids & self*/
				 if(fam_rel[0].substr(0,1) == '1')
				 {
					opt.each(function(e) {
						if(this.value == 0 || this.value == 1) {
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
	
	}
	
	$('input[type=radio][name=GCI_optional]').change(function() {
		
		//console.log($('select[name=sum_insure]').val() +"===="+$('select[name=sum_insure] :selected').attr("data-policyno") +"===="+ $('select[name=familyConstruct]').val() +"===="+ $('select[name=plan_name]').val());
		if(($('select[name=sum_insure]').val() != '' || $('select[name=sum_insure]').val() != undefined) && $('select[name=sum_insure] :selected').attr("data-policyno") != '' && $('select[name=familyConstruct]').val() != '' && $('select[name=plan_name]').val() != ''){
			/*add delete member on selection*/
			$.ajax({
				url: "tele_apply_changes",
				type: "POST",
				data: {
				'product_id':$('#plan_name').val(),
				'family_construct':$('select[name=familyConstruct]').val(),
				'sum_insured':$('select[name=sum_insure]').val(),
				'gci' : $('input[name=GCI_optional]:checked').val(),
				'only_gci':'Yes'
				},
				async: false,
				dataType: "json",
				success: function (response) {
					
				}
			});	
			/*ends*/
			//debugger;
			$.post("/tele_get_premium", { "sum_insured":  $('select[name=sum_insure]').val(), "policy_detail_id": $('select[name=sum_insure] :selected').attr("data-policyno"), "family_construct":  $('select[name=familyConstruct]').val(),"gci_optional":  $('input[name=GCI_optional]:checked').val(),"product_id" : $('#plan_name').val() }, function (e)
			{
				//debugger;
				$("#premiumModalHidden").val(e);
				e = JSON.parse(e);
				
				var premium = 0;
				$("#premiumModalBody").html("");
				var str = '';
				e.forEach(function (e1) {
					//console.log(e1);
					str += "<div style='display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;'><div><span style='color:#da8085;'> Name:  " + "<br/></span><span style='color:#000;'>" + e1.policy_sub_type_id + "</span></div>";
					str += "<div><span style='color:#da8085;'> Sum Insured </span>" + "<br/><span style='color:#000;'>" + e1.sum_insured + "</span></div>";
					str += "<div><span style='color:#da8085;'> Premium" + "</span><br/><span style='color:#000;'>" + parseFloat(e1.PremiumServiceTax).toFixed(2) + "</span></div></div>";
					
					$("#premiumModalBody").append(str);
					premium += parseFloat(e1.PremiumServiceTax);
				});
				//console.log(get_premium+']]]]]');
				if(str != ''){
					get_premium = str;
					$('#premiumModalBody').html(str);  
					
				}
				 $("#premium").val(premium.toFixed(2));
				 if(parseInt(premium) != 0 && !isNaN(premium)){
				 	total_premium = premium;
					$('#premium').val(premium);  
					
				}
			});
		}
		 
	})

	function getPremiumNew(){
		$.post("/tele_get_premium", { "sum_insured":  $('select[name=sum_insure]').val(), "policy_detail_id": $('select[name=sum_insure] :selected').attr("data-policyno"), "family_construct":  $('select[name=familyConstruct]').val(),"gci_optional":  $('input[name=GCI_optional]:checked').val(),"product_id" : $('#plan_name').val() }, function (e)
								{
									//debugger;
									$("#premiumModalHidden").val(e);
									e = JSON.parse(e);
									
									var premium = 0;
									$("#premiumModalBody").html("");
									var str = '';
									e.forEach(function (e1) {
										//console.log(e1);
										str += "<div style='display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;'><div><span style='color:#da8085;'> Name:  " + "<br/></span><span style='color:#000;'>" + e1.policy_sub_type_id + "</span></div>";
										str += "<div><span style='color:#da8085;'> Sum Insured </span>" + "<br/><span style='color:#000;'>" + e1.sum_insured + "</span></div>";
										str += "<div><span style='color:#da8085;'> Premium" + "</span><br/><span style='color:#000;'>" + parseFloat(e1.PremiumServiceTax).toFixed(2) + "</span></div></div>";
										
										$("#premiumModalBody").append(str);
										premium += parseFloat(e1.PremiumServiceTax);
									});
									//console.log(get_premium+']]]]]');
									if(str != ''){
										get_premium = str;
										$('#premiumModalBody').html(str);  
										
									}
									 $("#premium").val(premium.toFixed(2));
									 if(parseInt(premium) != 0 && !isNaN(premium)){
									 	total_premium = premium;
										$('#premium').val(premium);  
										
									}
								});
	}

    function getPremium(selectChange) 
	{
		//console.log(get_premium+"/////////"+total_premium+$('input[name=GCI_optional]:checked').val());
		//if your doing changes in this function please do the same changes in above function as well for premium calculation
		$.post("/tele_get_premium", { "sum_insured":  selectChange.find('select[name=sum_insure]').val(), "policy_detail_id":  selectChange.find('select[name=sum_insure] :selected').attr("data-policyno"), "family_construct":  selectChange.find('select[name=familyConstruct]').val(),"gci_optional":  $('input[name=GCI_optional]:checked').val(),"product_id" : $('#plan_name').val() }, function (e)
		{
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
			/*if(get_premium != ''){
				$('#premiumModalBody').html(get_premium);  
				
			}*/
			 $("#premium").val(premium.toFixed(2));
			 /*if(parseInt(total_premium) != 0 && !isNaN(total_premium)){
				$('#premium').val(total_premium);  
				
			}*/
		});
	}
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
	$("#getMaxPremiumAgeModal").modal(); 
	
	/*$("#premiumBif").attr('disabled', 'disabled');
	$(".ti-eye").attr('disabled', 'disabled').hide();
	
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
    }*/
	}
	
	function changes(e) 
	{debugger;
			selectChange = $(e).closest(".row");
			set_non_editable(selectChange,1);
			//alert($('#plan_name').val());
			if($('#plan_name').val() == 'T03'){
				var valueArray = $('.family_members_id1').map(function() {
					if(this.value != 0 && this.value != 1){
						return this.value;
					}
				    
				}).get();
				//alert(valueArray);
				$.ajax
				({
					url: "/tele_family_details_healthproxl",
					type: "POST",
					data: {relation_id: $(e).val(),selectedRelation:valueArray},
					dataType: "json",
					beforeSend: function() {
					        set_session();
					    },
					success: function (response) 
					{

						var dataOpt = selectChange.find("[name='family_members_id[]'] :selected").attr("data-opt");

						selectChange.find("input[name='first_name[]']").val("");
						selectChange.find("input[name='middle_name[]']").val("");
						selectChange.find("input[name='last_name[]']").val("");
						selectChange.find("input[name='family_id']").val("");
						selectChange.find("input[type='text'][name='family_date_birth[]']").val("");
						selectChange.find("input[name='age[]']").val("");
						selectChange.find("input[name='age_type[]']").val("");
						selectChange.find("input[name='family_gender[]']").val("");
						selectChange.find("[name='family_salutation[]']").val("");
						selectChange.find("[name='mem_email_id[]").val("");
						selectChange.find("[name='mem_mob_no[]").val("");
						var set_blank_variable =  selectChange.find("[name='family_members_id[]'] :selected").val();
						set_blank(set_blank_variable);
						
						var family_detail = response.family_data;
						var i ;
						if (true) 
						{

							if(family_detail.length != 0)
							{
								for(i = 0; i < family_detail.length; i++)
								{
									if (family_detail[i].fr_id == "0") 
									{
										set_blank(set_blank_variable);
										selectChange.find("input[name='first_name[]']").val(family_detail[i].emp_firstname.toUpperCase());
										selectChange.find("input[name='last_name[]']").val(family_detail[i].emp_lastname.toUpperCase());
										selectChange.find("input[name='middle_name[]']").val(family_detail[i].emp_middlename);
										selectChange.find("input[name='family_id']").val(family_detail[i].family_id);
										selectChange.find("input[name='family_gender[]']").val(family_detail[i].gender);
										selectChange.find("select[name='family_salutation[]']").val(family_detail[i].salutation);
										selectChange.find("input[type='text'][name='family_date_birth[]']").val(family_detail[i].bdate);
										selectChange.find("[name='mem_email_id[]").val(family_detail[i].email);
										selectChange.find("[name='mem_mob_no[]").val(family_detail[i].mob_no);
										get_age(family_detail[i].bdate,selectChange);
										set_blank(set_blank_variable);
										set_non_editable(selectChange,0);
									}
									else
									{
										var gen = selectChange.find("[name='family_members_id[]'] :selected").html();
										if(gen == 'Spouse')
										{
											set_non_editable(selectChange,1);
											if($("#gender1").val() == 'Male')
											{

												selectChange.find('[name="family_gender[]"]').val("Female");
												selectChange.find("select[name='family_salutation[]']").val("Mrs");
												selectChange.find("select[name='family_salutation[]']").prop('disabled',true);

											}
											else if($("#gender1").val() == 'Female')
											{
												selectChange.find('[name="family_gender[]"]').val("Male");
												selectChange.find("select[name='family_salutation[]']").val("Mr");
												selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
											}
											else
											{
												selectChange.find('[name="family_gender[]"]').val(" ");
												selectChange.find("[name='family_salutation[]']").val("");
												selectChange.find("select[name='family_salutation[]']").prop('disabled',false);
											}
											
											if(family_detail[i].policy_member_first_name){
												selectChange.find("input[name='first_name[]']").val(family_detail[i].policy_member_first_name.toUpperCase());
											}
											if(family_detail[i].policy_member_middle_name){
												selectChange.find("input[name='middle_name[]']").val(family_detail[i].policy_member_middle_name.toUpperCase());
											}
											if(family_detail[i].policy_member_last_name){
												selectChange.find("input[name='last_name[]']").val(family_detail[i].policy_member_last_name.toUpperCase());
											}
											if(family_detail[i].policy_member_email_id){
												selectChange.find("input[name='mem_email_id[]']").val(family_detail[i].policy_member_email_id);
											}
											if(family_detail[i].policy_member_mob_no){
												selectChange.find("input[name='mem_mob_no[]']").val(family_detail[i].policy_member_mob_no);
											}
											
											selectChange.find("#family_id").val(family_detail[i].policy_member_last_name);
											selectChange.find("input[type='text'][name='family_date_birth[]']").val(family_detail[i].policy_mem_dob);
											selectChange.find("input[name='marriage_date']").val(family_detail[i].marriage_date);
											selectChange.find("input[type='text'][name='age[]']").val(family_detail[i].age);
											
											selectChange.find("input[type='text'][name='age_type[]']").val(family_detail[i].age_type);

											get_age(family_detail[i].policy_mem_dob,selectChange);
										}
										else
										{
											//alert("g");
											/*selectChange.find("input[name='first_name']").val(family_detail[i].policy_member_first_name);
											selectChange.find("input[name='middle_name']").val(family_detail[i].policy_member_middle_name);
											selectChange.find("input[name='last_name']").val(family_detail[i].policy_member_last_name);
											selectChange.find("#family_id").val(family_detail[i].policy_member_last_name);
											selectChange.find("input[type='text'][name='family_date_birth']").val(family_detail[i].policy_mem_dob);
											selectChange.find("input[name='marriage_date']").val(family_detail[i].marriage_date);
											selectChange.find("input[type='text'][name='age']").val(family_detail[i].age);
											selectChange.find("input[name='family_gender']").val(family_detail[i].policy_mem_gender)
											selectChange.find("input[type='text'][name='age_type']").val(family_detail[i].age_type);

											get_age_family(family_detail[i].bdate,selectChange);*/
											
											if(gen != 'Spouse' || gen!='Self')
											{
												selectChange.find("select[name='family_salutation[]']").prop('disabled',false);
												selectChange.find('[name="family_gender[]"]').val(dataOpt);
												
												if(selectChange.find('[name="family_gender[]"]').val() == 'Male')
												{
													salutation_hide_show(selectChange,'Male');
												}
												else if(selectChange.find('[name="family_gender[]"]').val() == 'Female')
												{
													salutation_hide_show(selectChange,'Female');
												
												}
												var gen_id = selectChange.find("[name='family_members_id[]'] :selected").val();
												if(gen_id == '2' || gen_id == '3')
												{
													
													if(selectChange.find('[name="family_gender[]"]').val() == 'Male')
													{
														
														selectChange.find("select[name='family_salutation[]']").val("Master");
														selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
													}
													else if(selectChange.find('[name="family_gender[]"]').val() == 'Female')
													{
														selectChange.find("select[name='family_salutation[]']").val("Ms");
														selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
													}
													
												}

												//helathpro change
												selectChange.find("input[type='text'][name='family_date_birth[]']").val(family_detail[i].policy_mem_dob);
												get_age(family_detail[i].policy_mem_dob,selectChange);
											
											}
										}
									}
								}
							}
							else
							{

								var gen = selectChange.find("[name='family_members_id[]'] :selected").html();
								var gen_id = selectChange.find("[name='family_members_id[]'] :selected").val();
								if(gen == 'Spouse')
								{
									set_non_editable(selectChange,1);
									if($("#gender1").val() == 'Male')
									{
										
										selectChange.find('[name="family_gender[]"]').val("Female");
										selectChange.find("select[name='family_salutation[]']").val("Mrs");
										selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
									}
									else if($("#gender1").val() == 'Female')
									{
										selectChange.find('[name="family_gender[]"]').val("Male");
										selectChange.find("select[name='family_salutation[]']").val("Mr");
										selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
									}
									else
									{
										selectChange.find('[name="family_gender[]"]').val(" ");
										selectChange.find("[name='family_salutation[]']").val("");
										selectChange.find("select[name='family_salutation[]']").prop('disabled',false);
									}
								}
								else
								{
									if(gen != 'Spouse' || gen!='Self')
									{
										set_non_editable(selectChange,1);
										selectChange.find("select[name='family_salutation[]']").prop('disabled',false);
										selectChange.find('[name="family_gender[]"]').val(dataOpt);
										if(gen_id == '2' || gen_id == '3')
										{
											
											if(selectChange.find('[name="family_gender[]"]').val() == 'Male')
											{
												
												selectChange.find("select[name='family_salutation[]']").val("Master");
												selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
											}
											else if(selectChange.find('[name="family_gender[]"]').val() == 'Female')
											{
												selectChange.find("select[name='family_salutation[]']").val("Ms");
												selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
											}
											
										}
										else
										{
												if(selectChange.find('[name="family_gender[]"]').val() == 'Male')
												{
													
													selectChange.find('.female_gen'). prop('disabled', true);
													selectChange.find('.female_gen').hide();
													selectChange.find('.male_gen'). prop('disabled', false);
													selectChange.find('.male_gen').show();
												}
												else if(selectChange.find('[name="family_gender[]"]').val() == 'Female')
												{
													selectChange.find('.female_gen'). prop('disabled', false);
													selectChange.find('.female_gen').show();
													selectChange.find('.male_gen'). prop('disabled', true);
													selectChange.find('.male_gen').hide();
												}
										}
										//helathpro change
										selectChange.find("input[type='text'][name='family_date_birth[]']").val(family_detail[i].policy_mem_dob);
										
									}
								}
							}

						}
					}
				});
			}else{
				$.ajax
				({
					url: "/tele_family_details",
					type: "POST",
					data: {relation_id: $(e).val()},
					dataType: "json",
					beforeSend: function() {
					        set_session();
					    },
					success: function (response) 
					{

						var dataOpt = selectChange.find("[name='family_members_id[]'] :selected").attr("data-opt");

						selectChange.find("input[name='first_name[]']").val("");
						selectChange.find("input[name='middle_name[]']").val("");
						selectChange.find("input[name='last_name[]']").val("");
						selectChange.find("input[name='family_id']").val("");
						selectChange.find("input[type='text'][name='family_date_birth[]']").val("");
						selectChange.find("input[name='age[]']").val("");
						selectChange.find("input[name='age_type[]']").val("");
						selectChange.find("input[name='family_gender[]']").val("");
						selectChange.find("[name='family_salutation[]']").val("");
						var set_blank_variable =  selectChange.find("[name='family_members_id[]'] :selected").val();
						set_blank(set_blank_variable);
						
						var family_detail = response.family_data;
						var i ;
						if (true) 
						{

							if(family_detail.length != 0)
							{
								for(i = 0; i < family_detail.length; i++)
								{
									if (family_detail[i].fr_id == "0") 
									{
										set_blank(set_blank_variable);
										selectChange.find("input[name='first_name[]']").val(family_detail[i].emp_firstname.toUpperCase());
										selectChange.find("input[name='last_name[]']").val(family_detail[i].emp_lastname.toUpperCase());
										selectChange.find("input[name='middle_name[]']").val(family_detail[i].emp_middlename);
										selectChange.find("input[name='family_id']").val(family_detail[i].family_id);
										selectChange.find("input[name='family_gender[]']").val(family_detail[i].gender);
										selectChange.find("select[name='family_salutation[]']").val(family_detail[i].salutation);
										selectChange.find("input[type='text'][name='family_date_birth[]']").val(family_detail[i].bdate);
										get_age(family_detail[i].bdate,selectChange);
										set_blank(set_blank_variable);
										set_non_editable(selectChange,0);
									}
									else
									{
										var gen = selectChange.find("[name='family_members_id[]'] :selected").html();
										if(gen == 'Spouse')
										{
											set_non_editable(selectChange,1);
											if($("#gender1").val() == 'Male')
											{

												selectChange.find('[name="family_gender[]"]').val("Female");
												selectChange.find("select[name='family_salutation[]']").val("Mrs");
												selectChange.find("select[name='family_salutation[]']").prop('disabled',true);

											}
											else if($("#gender1").val() == 'Female')
											{
												selectChange.find('[name="family_gender[]"]').val("Male");
												selectChange.find("select[name='family_salutation[]']").val("Mr");
												selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
											}
											else
											{
												selectChange.find('[name="family_gender[]"]').val(" ");
												selectChange.find("[name='family_salutation[]']").val("");
												selectChange.find("select[name='family_salutation[]']").prop('disabled',false);
											}
											
											if(family_detail[i].policy_member_first_name){
												selectChange.find("input[name='first_name[]']").val(family_detail[i].policy_member_first_name.toUpperCase());
											}
											if(family_detail[i].policy_member_middle_name){
												selectChange.find("input[name='middle_name[]']").val(family_detail[i].policy_member_middle_name.toUpperCase());
											}
											if(family_detail[i].policy_member_last_name){
												selectChange.find("input[name='last_name[]']").val(family_detail[i].policy_member_last_name.toUpperCase());
											}
											
											
											selectChange.find("#family_id").val(family_detail[i].policy_member_last_name);
											selectChange.find("input[type='text'][name='family_date_birth[]']").val(family_detail[i].policy_mem_dob);
											selectChange.find("input[name='marriage_date']").val(family_detail[i].marriage_date);
											selectChange.find("input[type='text'][name='age[]']").val(family_detail[i].age);
											
											selectChange.find("input[type='text'][name='age_type[]']").val(family_detail[i].age_type);

											get_age(family_detail[i].bdate,selectChange);
										}
										else
										{
											/*selectChange.find("input[name='first_name']").val(family_detail[i].policy_member_first_name);
											selectChange.find("input[name='middle_name']").val(family_detail[i].policy_member_middle_name);
											selectChange.find("input[name='last_name']").val(family_detail[i].policy_member_last_name);
											selectChange.find("#family_id").val(family_detail[i].policy_member_last_name);
											selectChange.find("input[type='text'][name='family_date_birth']").val(family_detail[i].policy_mem_dob);
											selectChange.find("input[name='marriage_date']").val(family_detail[i].marriage_date);
											selectChange.find("input[type='text'][name='age']").val(family_detail[i].age);
											selectChange.find("input[name='family_gender']").val(family_detail[i].policy_mem_gender)
											selectChange.find("input[type='text'][name='age_type']").val(family_detail[i].age_type);

											get_age_family(family_detail[i].bdate,selectChange);*/
											
											if(gen != 'Spouse' || gen!='Self')
											{
												selectChange.find("select[name='family_salutation[]']").prop('disabled',false);
												selectChange.find('[name="family_gender[]"]').val(dataOpt);
												
												if(selectChange.find('[name="family_gender[]"]').val() == 'Male')
												{
													salutation_hide_show(selectChange,'Male');
												}
												else if(selectChange.find('[name="family_gender[]"]').val() == 'Female')
												{
													salutation_hide_show(selectChange,'Female');
												
												}
											
											}
										}
									}
								}
							}
							else
							{

								var gen = selectChange.find("[name='family_members_id[]'] :selected").html();
								var gen_id = selectChange.find("[name='family_members_id[]'] :selected").val();
								if(gen == 'Spouse')
								{
									set_non_editable(selectChange,1);
									if($("#gender1").val() == 'Male')
									{
										
										selectChange.find('[name="family_gender[]"]').val("Female");
										selectChange.find("select[name='family_salutation[]']").val("Mrs");
										selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
									}
									else if($("#gender1").val() == 'Female')
									{
										selectChange.find('[name="family_gender[]"]').val("Male");
										selectChange.find("select[name='family_salutation[]']").val("Mr");
										selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
									}
									else
									{
										selectChange.find('[name="family_gender[]"]').val(" ");
										selectChange.find("[name='family_salutation[]']").val("");
										selectChange.find("select[name='family_salutation[]']").prop('disabled',false);
									}
								}
								else
								{
									if(gen != 'Spouse' || gen!='Self')
									{
										set_non_editable(selectChange,1);
										selectChange.find("select[name='family_salutation[]']").prop('disabled',false);
										selectChange.find('[name="family_gender[]"]').val(dataOpt);
										if(gen_id == '2' || gen_id == '3')
										{
											
											if(selectChange.find('[name="family_gender[]"]').val() == 'Male')
											{
												
												selectChange.find("select[name='family_salutation[]']").val("Master");
												selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
											}
											else if(selectChange.find('[name="family_gender[]"]').val() == 'Female')
											{
												selectChange.find("select[name='family_salutation[]']").val("Ms");
												selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
											}
											
										}
										else
										{
												if(selectChange.find('[name="family_gender[]"]').val() == 'Male')
												{
													
													selectChange.find('.female_gen'). prop('disabled', true);
													selectChange.find('.female_gen').hide();
													selectChange.find('.male_gen'). prop('disabled', false);
													selectChange.find('.male_gen').show();
												}
												else if(selectChange.find('[name="family_gender[]"]').val() == 'Female')
												{
													selectChange.find('.female_gen'). prop('disabled', false);
													selectChange.find('.female_gen').show();
													selectChange.find('.male_gen'). prop('disabled', true);
													selectChange.find('.male_gen').hide();
												}
										}
									
									}
								}
							}

						}
					}
				});
			}
		
	}
	
	function set_blank(set_blank_variable)
	{
		if(set_blank_variable == '')
		{
			set_non_editable(selectChange,1);
			selectChange.find("input[name='first_name[]']").val("");
			selectChange.find("input[name='middle_name[]']").val("");
			selectChange.find("input[name='last_name[]']").val("");
			selectChange.find("input[name='family_id']").val("");
			selectChange.find("input[type='text'][name='family_date_birth[]']").val("");
			selectChange.find("input[name='age[]']").val("");
			selectChange.find("[name='family_salutation[]']").val("");

			selectChange.find("input[name='age_type[]']").val("");

			selectChange.find("input[name='family_gender[]']").val("");
		}
	}

	function set_non_editable(selectChange,Is_edit)
	{
		if(Is_edit == 0)
		{
			selectChange.find("input[name='first_name[]']").attr('disabled','disabled');
			selectChange.find("input[name='last_name[]']").attr('disabled','disabled');
			selectChange.find("input[name='middle_name[]']").attr('disabled','disabled');
			selectChange.find("input[name='family_id']").attr('disabled','disabled');
			selectChange.find("input[name='family_gender[]']").attr('disabled','disabled');
			//selectChange.find("input[type='text'][name='family_date_birth[]']").attr('disabled','disabled');
			selectChange.find("select[name='family_salutation[]']").attr('disabled','disabled');
			selectChange.find("input[name='mem_email_id[]']").attr('disabled','disabled');
			selectChange.find("input[name='mem_mob_no[]']").attr('disabled','disabled');
		}
		else
		{
			selectChange.find("input[name='first_name[]']").removeAttr('disabled','disabled');
			selectChange.find("input[name='last_name[]']").removeAttr('disabled','disabled');
			selectChange.find("input[name='middle_name[]']").removeAttr('disabled','disabled');
			selectChange.find("input[name='family_id']").removeAttr('disabled','disabled');
			selectChange.find("input[name='family_gender[]']").removeAttr('disabled','disabled');
			//selectChange.find("input[type='text'][name='family_date_birth[]']").removeAttr('disabled','disabled');
			selectChange.find("select[name='family_salutation[]']").removeAttr('disabled','disabled'); 
			selectChange.find("input[name='mem_email_id[]']").removeAttr('disabled','disabled');
			selectChange.find("input[name='mem_mob_no[]']").removeAttr('disabled','disabled'); 
		} 
	}
	
	$(".dobdate").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
		minDate : 0,
		yearRange: "-60: +30",
        onSelect: function (dateText, inst) 
		{
            $(this).val(dateText);
			/*var selectChange=$(this).closest(".row");
            get_age(dateText);*/
        }
    });
	function convertDate(inputFormat) 
	{
		var b = inputFormat.split(/\D/);
		return b.reverse().join('-');
	}


	function get_age(dateStrings,selectChange='') 
	{

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

			var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
			var firstDate = new Date();
			var secondDate = new Date(dateString);

			var diffDays = Math.round(Math.abs((firstDate - secondDate) / oneDay));
		age = diffDays -1 ;
			age_type = "days";
		}


		selectChange.find("input[name='age[]']").val(age);
		selectChange.find("input[name='age_type[]']").val(age_type);
		// $("#age1").val(age);
		// $("#age_type1").val(age_type);
	}
    function get_age_family(e,selectChange) 
    {
    
		var today = new Date();
		var dob = new Date(e);
		var z = e.split("-");
		var dob = new Date(z[2], z[1] - 1, z[0]);
		var today_mon = today.getMonth();
		var dob_mon = dob.getMonth();
		var dob_day = dob.getDate();
		var today_day = today.getDate();

		var age_distance = today.getFullYear() - dob.getFullYear();
		if (today_mon >= dob_mon && today_day >= dob_day) 
		{

			if (age_distance > 0) 
			{
				selectChange.find("input[name='age[]']").val(age_distance);
				selectChange.find("input[name='age_type[]']").val("years");
			} 
			else
			{
				var month = ("0" + (today.getMonth() + 1)).substr(-2);
				var strDate =("0" + today.getDate()).substr("-2") + "-" + ("0" + month).substr("-2") + "-" + today.getFullYear();
				var strDate = today.getDate() + "-" + month + "-" + today.getFullYear();
				var date = strDate.split("-");
				var display_date = date[2] + "-" + date[1] + "-" + date[0];
				var date = e.split("-");
				var date_bday = date[2] + "-" + date[1] + "-" + date[0];
				var day = new Date(display_date).getTime() - new Date(date_bday).getTime();
				var days = Math.floor(day / (1000 * 60 * 60 * 24));
				 selectChange.find("input[name='age[]']").val(days);
				selectChange.find("input[name='age_type[]']").val("days");
			}
		} 
		else 
		{
			if (age_distance > 1) 
			{
				selectChange.find("input[name='age[]']").val(age_distance);
				 selectChange.find("input[name='age_type[]']").val("years");
				var age_distances = age_distance - 1;
			} 
			else if (age_distance == 1) 
			{
				var month = ("0" + (today.getMonth() + 1)).substr(-2);

				var strDate = ("0"+today.getDate()).substr(-2)  + "-" + ("0"+month).substr(-2) + "-" + today.getFullYear();
					   
				var date = strDate.split("-");
				var display_date = date[2] + "-" + date[1] + "-" + date[0];
				var date = e.split("-");
				var date_bday = date[2] + "-" + date[1] + "-" + date[0];
				var day = new Date(display_date).getTime() - new Date(date_bday).getTime();
				var days = Math.floor(day / (1000 * 60 * 60 * 24));
				if(days >= 365)
				{
					selectChange.find("input[name='age[]']").val(1);
					selectChange.find("input[name='age_type[]']").val("years");
				}
				else
				{
					 selectChange.find("input[name='age[]']").val(days);
				     selectChange.find("input[name='age_type[]']").val("days");
				}
				
			} 
			else 
			{
				var month = ("0" + (today.getMonth() + 1)).substr(-2);
				var strDate = ("0" + today.getDate()).substr("-2") + "-" + ("0" + month).substr("-2") + "-" + today.getFullYear();
				var strDate = today.getDate() + "-"  + month + "-" + today.getFullYear();
				var date = strDate.split("-");
				var display_date = date[2] + "-" + date[1] + "-" + date[0];
				var date = e.split("-");
				var date_bday = date[2] + "-" + date[1] + "-" + date[0];
				var day = new Date(display_date).getTime() - new Date(date_bday).getTime();
				var days = Math.floor(day / (1000 * 60 * 60 * 24));
				selectChange.find("input[name='age[]']").val(days);
				selectChange.find("input[name='age_type[]']").val("days");
			}
		}
	
 
    }
	
    $(document).on("change","input:checkbox[name='sub_type_check[]']",function()
	{
		var sub_type_id = $(this).val();
		var id = $(this).attr('id');
		var lastChar = id.slice(-1);
		//console.log(sub_type_id);
		$("#di"+sub_type_id+lastChar).html('');
		$("#di"+sub_type_id+lastChar).remove();
		if($(this).is(":checked") && sub_type_id !='')
		{
			
			get_sub_data(sub_type_id,lastChar);
		}
		else
		{
			$("#di"+sub_type_id+lastChar).html('');
			$("#di"+sub_type_id+lastChar).remove();
		}
  
    });	
 
	function get_sub_data(sub_type_id,lastChar='')
	{
		$('#di'+sub_type_id+lastChar).empty();
			$.ajax({
			url: "/tele_member_declaration_question",
			type: "POST",
			dataType: "html",
			async:false,
			data: {'sub_type_id':sub_type_id,'member_id':lastChar},
			success: function (response) 
			{
				if($('#di'+sub_type_id+lastChar).length == 0)
				{
					$(".quest_declare_benifit_4s"+lastChar).append('<div id =di'+sub_type_id+lastChar+'>'+response+'</div>');
				}
				else
				{
					$("#di"+sub_type_id+lastChar).remove();
				}
			}
			});
	}
	
	

	$("#patForm").validate({
	ignore: ".ignore",
	focusInvalid: true,
	rules: {
	sum_insure: {
	required: true
	},
	anuual_income:{
	required: true,
	//valid_annual_income : true,
	},
	occupation:{
	required: true
	},
	familyConstruct: {
	required: true
	},
	premium: {
	required: true
	},
	'businessType': {
	required: true
	},
	'family_members_id[]': {
	required: true
	},
	'family_gender[]': {
	required: true
	},
	'first_name[]': {
	required: true
	},
	/*'last_name[]': {
	required: true
	},*/
	'family_date_birth[]': {
	required: true
	},
	'age[]': {
	required: true
	},

	'age_type[]': {
	required: true
	},
	'mem_email_id[]': {
	required: true,
	validateEmail: true,
	},
	'mem_mob_no[]': {
	required: true,
	valid_mobile: true
	},


	},

	messages: {
		valid_annual_income : 'SI '
	},
	submitHandler: function (form) {
		set_session();
		//alert("form validated"+$("#premium").val());
				if (!Number($("#premium").val())) {
					
					swal("Alert", "Invalid Premium", "warning");
					return;
				}
				if($("#premium").val() == 'undefined')
				{
					swal("Alert", "Invalid Premium", "warning");
					return;
				}
				
			var family_construct = $('#patFamilyConstruct option:selected').val();
			var result = family_construct.split('+');
				
			var add_length = [];
			add_length.push(parseInt(result[0]));
			add_length.push(parseInt(result[1]));
			
			
			var i;
			var TableData2 = [];
			var TableData3 = [];
			var TableData5 = [];
			var TableData6 = [];
			for (i = 0; i < sum(add_length); i++) {	
			
				var TableData = [];
				var TableData4 = [];
				
				$(".disease"+i).find('input:checkbox').each(function () {
					if($(this).is(":checked")){
						TableData4.push($(this).val());
					}
				});
				
				TableData3[i] = TableData4;
				
				if(TableData4.length > 0){
					TableData6.push(1);
				}
				
				$(".disease"+i).find('#mydatasmember tr').each(function (row, tr)
				{
					//debugger;
					var content = $(tr).find('.mycontent').val();
					
					if($(tr).find('input[name="' + content + '"]:checked').val() == 'Yes'){
						TableData5.push(1);
					}

					TableData[row] = 
					{
						"question": content,
						"format": $(tr).find('input[name="' + content + '"]:checked').val(),
					}
					

				});
				TableData2[i] = TableData;
				
			}
			
			if(TableData6.length > 1){
				swal({
				title: "Alert",
				text: "Only one member is allow with Chronic Disease",
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
					/*$("#sum_insures").attr('disabled', 'disabled');
					$("#patFamilyConstruct").attr('disabled', 'disabled');*/
				});
				return;
			}
			
			if(TableData5.length > 0){
				swal({
				title: "Alert",
				text: "Cannot proceed with Chronic Disease Yes",
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
					/*$("#sum_insures").attr('disabled', 'disabled');
					$("#patFamilyConstruct").attr('disabled', 'disabled');*/
				});
				return;
			}
			
					
		/*var TableData = [];

		var LabelData = []; 
		$('#mydatasmember tr').each(function (row, tr)
		{
			var LabelData = {};
		  
			var label_content;
			var tds = $(tr).find('td:eq(0)').text();
			var label = $(tr).find('.label_id').text();
			var content = $(tr).find('.mycontent').val();

			TableData[row] = 
			{
				"question": content,
				"format": $(tr).find('input[name="' + content + '"]:checked').val(),
			}

		});*/
		
		$('[name="family_date_birth[]"]').each(function(){
         //code
			$(this).prop("disabled", false);
		});
		
		selectChange = $('#patForm').closest("form");
		set_non_editable(selectChange,1);
		var policyNo = $("#sum_insures :selected").attr("data-policyno");
		var plan_name = $('#plan_name').val();//$("#pr_name").html();
		var premium_pat = $('#patFamilyConstruct option:selected').data('premium');
		if(premium_pat == '' || premium_pat == undefined){
			var premium_pat = $("#premium").val();
		} 
		selectChange.find("select[name='family_salutation[]']").removeAttr('disabled', 'disabled');
		var familyDataType = $("#sum_insures :selected").attr("data-type");
		//$("#patFamilyConstruct").removeAttr('disabled', 'disabled');
		//$("#plan_name").removeAttr('disabled', 'disabled');
		//$("#sum_insures").removeAttr('disabled', 'disabled');
		$(".family_members_id1").prop('disabled',false);
		var family_members_id = $(".family_members_id1").val();
		var form = $("#patForm").serialize() + "&plan_name="+$('#plan_name').val()+ "&GCI_optional="+$("input[name='GCI_optional']:checked").val()+"&premium=" + premium_pat + "&policyNo=" + $("#sum_insures :selected").attr("data-policyno") + "&familyDataType=" + familyDataType + "&declare=" + JSON.stringify(TableData2)+ "&chronic=" + JSON.stringify(TableData3);
		var master_policy = $("#master_policy_number").val();
		var GCI_optional = $("input[name='GCI_optional']:checked").val();
		/*if($('#annual_income').val() != '' || $('#annual_income').val() != undefined){
			var annualIncome = $('#annual_income').val();
			insert_annual_income(emp_id,annualIncome,'insert');
		}*/
		
		var agent_details = $("#hidden_agent_section").val();
		var customer_details = $("#hidden_customer_section").val();
		if(agent_details == 0 || customer_details == 0){
			swal({
				title: "Alert",
				text:"Please SAVE the values entered so far to proceed with new Section!",
				type: "warning",
				showCancelButton: false,
				confirmButtonText: "Ok!",
				closeOnConfirm: true,
				allowOutsideClick: false,
				closeOnClickOutside: false,
				closeOnEsc: false,
				dangerMode: true,
				allowEscapeKey: false
			} ,
				function () {
				   
				});  
		return false;
			
		}
		debugger;
		
		
		form = form +  "&empId=" + $('#empidaxistele').val()+"&hidden_policy_id="+$('#hidden_policy_id').val()+"&hidden_deductable="+$('#hidden_deductable').val()+"&sum_insure="+$("#sum_insures").val()+"&familyConstruct="+$("#patFamilyConstruct").val();
		// alert(form);
		if($("#plan_name").val() == 'T03'){
			form = form + "&premium="+$("#premium").val();
		}
		$.post("/tele_family_details_insert", form, function (e)
		{
			$('[name="family_date_birth[]"]').each(function(){
	         //code
				//$(this).prop("disabled", true);
			});


		
	
			var data = JSON.parse(e);

			if (!data.status) 
			{
				if(family_members_id == 0)
				{
					set_non_editable(selectChange,0)
				}
	
				if (data.check == "declaration")
				{
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
				function () 
				{
					//$("#sum_insures").attr('disabled', 'disabled');
					//$("#patFamilyConstruct").attr('disabled', 'disabled');
					//$('input[name=GCI_optional]').attr("disabled",true);
				});
				return;
			}

				swal("Alert", data.message, "success");
				$("#B_data").show();
				$("#C_data").hide();
				$("#D_data").hide();
				$("#E_data").hide();
				$("#B_remark").hide();
				$(".B").hide();
				$('#chronic th input[type="checkbox"]').prop('checked',false);
				$(".tt").empty();
				$("#patFormSubmit").closest("form").find("select[name='family_members_id[]']").css("pointer-events",'auto');
				$('#confr').show();
				$("#patForm").find("input[name='edit']").val("0");
				//$("#sum_insures").attr('disabled', 'disabled');
				//$("#patFamilyConstruct").attr('disabled', 'disabled');
				//$('input[name=GCI_optional]').attr("disabled",true);
				$("#premium").attr('disabled', 'disabled');
				$("#plan_name").attr('disabled', 'disabled');
				if($('#plan_name').val() == 'T03'){
					$("#sum_insures").attr('disabled', 'disabled');
					$("#patFamilyConstruct").attr('disabled', 'disabled');
				}
				var sum_insures = $("#sum_insures").val();
				var patFamilyConstruct = $("#patFamilyConstruct").val();
				var premium = $("#premium").val();
				
				document.getElementById("patForm").reset();
				$("#master_policy_number").val(master_policy);
				$("#sum_insures").val(sum_insures);
				$("#patFamilyConstruct").val(patFamilyConstruct);
				$("#premium").val(premium);
				// alert(plan_name);
				$("#plan_name").val(plan_name);

				//$("#patFormSubmit").hide();
				if(GCI_optional == 'No'){
					$('#GCI_no').prop('checked', true);
				}else{
					$('#GCI_yes').prop('checked', true);
				}
				$("#hidden_policy_section").val(1);
				addDependentForm("patTable", JSON.parse(e));
				//$("#family_members_id1").trigger("change");
				proposal_create_check();
		});
			

		 
	    }
	});
	function get_blank_validation()
	{
		//debugger;
		var abc = $('input[type=radio][name="myGHD[B][format]"]:checked').val();
		var user = $('input[name^="myGHD"]');
		var count =0;
		var k = 0;
		var data = $('input[name$="[format]"]');
		var obj = data.length;
		for(var i = 0 ;i < obj ; i++)
		{
		
			var str = data[i].name;
			var str_id = data[i].id;
			var split_format = str_id.split("_");
			
			var get_tr = split_format[0].replace(/\d+/g, '')+"_data";
			if($("#"+get_tr).css('display')!= 'none')
			{
				if($('input[name$="'+str+'"]'). is(":checked"))
				{
					
				}
				else
				{
					return false;
				}
			}
			
		}
	}
	function get_remark_validation()
	{
		//debugger;
		var user = $('input[name^="myGHD"]');
		var count =0;
		var k = 0;
		var data = $('input[name$="[remark]"]');
		var obj = data.length;

		for(var i = 0 ;i < obj ; i++)
		{
		
			var str = data[i].name;
			var dat = str.replace("[remark]","[format]");
			if($('input[name$="'+dat+'"]'). is(":checked"))
			{
				var split_formats = $('input[name$="'+dat+'"]:checked').val();
				var split_format = $('input[name$="'+dat+'"]:checked').val().split("_");

				var join_split = "#"+split_format[0];
				var test_class =  split_format[0];
				var remark = $(join_split+"_remark").val();
				
				if(split_format[1] == 'Yes')
				{
					$('.'+test_class).show();
					if(remark == "")
					{
						if($('.'+test_class).length == 0)
						{
							$(join_split+"_remark").after("<span style='color:red;' class='"+test_class+"'>Remark  is required</span>");

						}
						count = 1;
					}
				}
				if(split_format[1] == 'No')
				{
					
					$('.'+test_class).hide();

					k++;

				}

			}
			else
			{

			}
		}
		
		if(count == 1)
		{
			return false;
		}
		if(k > =6)
		{
			return true;
		}

	
	}

	$("#premiumBif").on("click", function () {
        
        if ($("#premiumModalBody").html().trim()) {}
            $("#premiumModal").modal();
    });
		function get_fam_data(elem) 
	{

					
					var gen = $("#nominee_relation").val();
					
					if(gen == 1)
					{
						if($("#gender1").val() == 'Male')
						{
							
							$("#nominee_gender").val("Female");
							$(".nominee_female_gen").css('display','block');
							$(".nominee_male_gen").css('display','none');
							$("#nominee_salutation").val("Mrs");

						}
						else if($("#gender1").val() == 'Female')
						{
							
							$("#nominee_gender").val("Male");
							$("#nominee_salutation").val("Mr");
							$(".nominee_female_gen").css('display','none');
							$(".nominee_male_gen").css('display','block');
							
						}
					}
					else
					{
						var dataOpt = $("select[name='nominee_relation'] :selected").attr("data-opt");
						if(dataOpt == 'Male')
						{$(".nominee_female_gen").css('display','none');
						$("#nominee_salutation").val("Mr");
						$(".nominee_male_gen").css('display','block');}else if(dataOpt ==  'Female'){$("#nominee_salutation").val("Ms");$(".nominee_female_gen").css('display','block');
						$(".nominee_male_gen").css('display','none');}
						$("#nominee_gender").val(dataOpt);
					}
		$.ajax({
				url: "/tele_family_details",
				type: "POST",
				data: {
					relation_id: elem.value,
					
				},
				async: false,
				dataType: "json",
				beforeSend: function() {
			        set_session();
			    },
				success: function (response)
				{
				  

					$("#nominee_fname").val("");
					$("#nominee_lname").val("");
					$("input[type='text'][name='nominee_dob']").val("");
				    $('#nominee_contact').val("");
					var family_detail = response.family_data;
					

					if (family_detail.length != 0)
					{
						if (family_detail[0].fr_id == "2" || family_detail[0].fr_id == "3")
						{
							$("#body_modal").html("");
							for ($i = 0; $i < family_detail.length; $i++) 
							{
								
								$("#body_modal").append('<input type="radio" name ="radio_option" value= ' +family_detail[$i]["policy_member_id"] +"> " +family_detail[$i].policy_member_first_name +"<br>");
							}
							$("#myModal").modal();
						

						} 
						else if (family_detail[0].fr_id == "0") 
						{

							$("#nominee_fname").val(family_detail[0].policy_member_first_name.toUpperCase());
							$("#nominee_lname").val(family_detail[0].policy_member_last_name.toUpperCase());
							$("#nominee_gender").val(family_detail[0].policy_mem_gender);
							$("#dob").val(family_detail[0].policy_mem_dob);
							

						} else 
						{
							
							$('#nominee_salutation').attr('disabled',true);
							if(family_detail[0].policy_member_first_name.toUpperCase()){
							$("#nominee_fname").val(family_detail[0].policy_member_first_name.toUpperCase());
							$("#nominee_fname").attr('readonly',true);
							}
							if(family_detail[0].policy_member_last_name.toUpperCase()){
							$("#nominee_lname").val(family_detail[0].policy_member_last_name.toUpperCase());
							$('#nominee_lname').attr('readonly',true);
							}
							if(family_detail[0].policy_mem_gender){
							$("#nominee_gender").val(family_detail[0].policy_mem_gender);
							$('#nominee_gender').attr('readonly',true);
							}
							if(family_detail[0].policy_mem_dob){
							$("input[type='text'][name='nominee_dob']").val(family_detail[0].policy_mem_dob);
							$('#nomineedob').attr('disabled',true);
							}
							
							
						}

					}
				}
		});
	}
	
	function get_fam_data2(elem) 
	{

					
					var gen = $("#nominee_relation").val();
					
					if(gen == 1)
					{
						if($("#gender1").val() == 'Male')
						{
							
							$("#nominee_gender").val("Female");
							$(".nominee_female_gen").css('display','block');
							$(".nominee_male_gen").css('display','none');
							$("#nominee_salutation").val("Mrs");

						}
						else if($("#gender1").val() == 'Female')
						{
							
							$("#nominee_gender").val("Male");
							$("#nominee_salutation").val("Mr");
							$(".nominee_female_gen").css('display','none');
							$(".nominee_male_gen").css('display','block');
							
						}
					}
					else
					{
						var dataOpt = $("select[name='nominee_relation'] :selected").attr("data-opt");
						if(dataOpt == 'Male')
						{$(".nominee_female_gen").css('display','none');
						$("#nominee_salutation").val("Mr");
						$(".nominee_male_gen").css('display','block');}else if(dataOpt ==  'Female'){$("#nominee_salutation").val("");$(".nominee_female_gen").css('display','block');
						$(".nominee_male_gen").css('display','none');}
						$("#nominee_gender").val(dataOpt);
					}
		$.ajax({
				url: "/tele_family_details",
				type: "POST",
				data: {
					relation_id: elem.value,
					
				},
				async: false,
				dataType: "json",
				beforeSend: function() {
				        set_session();
				    },
				success: function (response)
				{
				  

					$("#nominee_fname").val("");
					$("#nominee_lname").val("");
					$("input[type='text'][name='nominee_dob']").val("");
				    $('#nominee_contact').val("");
					var family_detail = response.family_data;
					

					if (family_detail.length != 0)
					{
						if (family_detail[0].fr_id == "2" || family_detail[0].fr_id == "3")
						{
							$("#body_modal").html("");
							for ($i = 0; $i < family_detail.length; $i++) 
							{
								
								$("#body_modal").append('<input type="radio" name ="radio_option" value= ' +family_detail[$i]["policy_member_id"] +"> " +family_detail[$i].policy_member_first_name +"<br>");
							}
							$("#myModal").modal();
						

						} 
						else if (family_detail[0].fr_id == "0") 
						{

							$("#nominee_fname").val(family_detail[0].policy_member_first_name.toUpperCase());
							$("#nominee_lname").val(family_detail[0].policy_member_last_name.toUpperCase());
							$("#nominee_gender").val(family_detail[0].policy_mem_gender);
							$("#dob").val(family_detail[0].policy_mem_dob);
							

						} else 
						{

							$("#nominee_fname").val(family_detail[0].policy_member_first_name.toUpperCase());
							$("#nominee_lname").val(family_detail[0].policy_member_last_name.toUpperCase());
							$("#nominee_gender").val(family_detail[0].policy_mem_gender);
							$("input[type='text'][name='nominee_dob']").val(family_detail[0].policy_mem_dob);
							
							
						}

					}
				}
		});
	}
   $(document).on("click", "#modal-submit", function () 
    {
 
        if ($("input[name='radio_option']:checked").val() == undefined) 
		{
            swal("please select at least one member");
            return false;
        }
        
     $.ajax({
					url: "/tele_nominee_family_details",
					type: "POST",
					data: {
						family_id: $("input[name='radio_option']:checked").val(),
						
					},
					async: false,
					dataType: "json",
					success: function (response)
					{
						
						if (response.length != 0)
						{
							$("#nominee_fname").val(response.policy_member_first_name.toUpperCase());
							$("#nominee_lname").val(response.policy_member_last_name.toUpperCase());
							$("#nominee_gender").val(response.policy_mem_gender);
							$("input[type='text'][name='nominee_dob']").val(response.policy_mem_dob);
					   
						}
						$("#myModal").modal("hide");
					}
			});
    });
    function sortNumber(a, b) 
	{
        return a - b;
    }
      
    function sortSumInsures(id, opt)
	{
        var selVal = $("#"+id+" : selected").val();
        var vals = $(opt).map(function() {if(this.value) {return this.value};}).get();
        vals.sort(sortNumber);
        var str = [];
        str.push(opt[0]);
        for(i = 0; i < vals.length; ++i)
	    {
        
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
        submitHandler: function (form) 
		{
            
            var policyNo = $("#sum_insures :selected").attr("data-policyno");
            var sum_insures = $("#sum_insures").val();
            var familyConstruct = $("#patFamilyConstruct").val();
            var maxPremiumAge = $("#getMaxPremiumAge").val();
            $.post("/get_premium_age_tele", { "policyNo": policyNo, "sum_insures": sum_insures, "familyConstruct": familyConstruct, "maxPremiumAge": maxPremiumAge }, function (e) {
                e = JSON.parse(e);
                $("#premium").val("");
                
                if (e.status == 'success') 
				{
                    $("#premium").val(e.premium);
                    $('#getMaxPremiumAgeModal').modal('hide');
                } 
				else 
				{
                    swal("Alert", e.message, "warning");
                }

            })
        }
    });
	
	
	//siddhi	
	$("select[name='familyConstruct']").on("change", function () 
	{
		debugger;
		var plan_name = $("#plan_name").val();
		//alert(plan_name);
		if(plan_name == 'T03'){
			//alert($('.commonCls').length+'===='+$('#sum_insures').val());
			if($('.commonCls').length == 0 && $('#sum_insures').val() != '' && !$('#patFamilyConstruct').is('[disabled=disabled]')) {
	    		//append saved dob to popup
	    		$.ajax({
					url: "/tele/get_employee_data_new",
					type: "POST",
					async: false,
					data: {
					"emp_id": $('#empidaxistele').val()
					},
					dataType: "json",
					success: function (response) {
						if(response.spouse_dob){
							$("#spouse_date_birth").val(response.spouse_dob);
						}
						if(response.kid1_dob){
							$("#kid1_date_birth").val(response.kid1_dob);
						}
						if(response.kid2_dob){
							$("#kid2_date_birth").val(response.kid2_dob);
						}	
						$("#kid1_rel").val(response.kid1_rel);
						$("#kid2_rel").val(response.kid2_rel);					
					}
				});
	    		var cons = $(this).val().split("+");
	    		var adultCount = 0;
	    		var kidCount = 0;
	    		if(cons[0] == '1A'){
	    			adultCount = 1;
	    			$(".check_adult").show();
	    		}else{
	    			adultCount = 2;
	    			$(".spouse_div").show();
					$(".check_adult").hide();
	    		}
	    		if(cons[1] != undefined){
	    			if(cons[1] == '1K'){
		    			kidCount = 1;
		    		}else{
		    			kidCount = 2;
		    		}
	    		}

	    		if($(this).val() == '2A+1K'){
	    			$(".spouse_div").show();
	    			$(".kid1_div").show();
	    			$(".kid2_div").hide();
					$(".check_adult").hide();
	    		}else if($(this).val() == '2A+2K'){
	    			$(".spouse_div").show();
	    			$(".kid1_div").show();
	    			$(".kid2_div").show();
					$(".check_adult").hide();
	    		}else if($(this).val() == '1A+1K'){
	    			$(".spouse_div").hide();
	    			$(".kid1_div").show();
	    			$(".kid2_div").hide();
					$(".check_adult").show();
	    		}else if($(this).val() == '1A+2K'){
	    			$(".spouse_div").hide();
	    			$(".kid1_div").show();
	    			$(".kid2_div").show();
					$(".check_adult").show();
	    		}else if($(this).val() == '2A'){
	    			$(".spouse_div").show();
	    			$(".kid1_div").hide();
	    			$(".kid2_div").hide();
					$(".check_adult").hide();
	    		}else{
	    			$(".check_adult").show();
	    			$(".spouse_div").hide();
	    			$(".kid1_div").hide();
	    			$(".kid2_div").hide();
	    		}

	    		

	    		$("#helathproXlFamilyModal").modal("show");
				var deductable = $('#hidden_deductable').val();
				var hidden_policy_id = $('#hidden_policy_id').val();
				
			}else{
				var famCons = $(this).val();
				var cons = famCons.split("+");
				var count = parseInt(cons[0]);
				if(cons[1] != undefined){
					count = count + parseInt(cons[1]);
				}
				if(count != $('.commonCls').length)
				$.ajax({
					url: "/tele/get_employee_data_new",
					type: "POST",
					async: false,
					data: {
					"emp_id": $('#empidaxistele').val()
					},
					dataType: "json",
					success: function (response) {

						//remove non selected relation from relation with proposer details
						var membersCovered = [];
						
						if(cons[0] == '1A' && response.policy_for == 0){
							membersCovered.push(0);
						}

						if(cons[0] == '1A' && response.policy_for == 1){
							membersCovered.push(1);
						}

						if(cons[0] == '2A'){
							membersCovered.push(0);
							membersCovered.push(1);
						}		

						if(cons[1] != undefined){
							if(cons[1] == '1K'){
								membersCovered.push(response.kid1_rel);
							}

							if(cons[1] == '2K'){
								membersCovered.push(response.kid1_rel);
								membersCovered.push(response.kid2_rel);
							}
						}		
						
						if(response.spouse_dob){
							/*var spouse_dob = response.spouse_dob.split("-");
							spouse_dob = spouse_dob[2]+'-'+spouse_dob[1]+'-'+spouse_dob[0];*/
							$("#spouse_date_birth").val(response.spouse_dob);
						}
						if(response.kid1_dob){
							/*var kid1_dob = response.kid1_dob.split("-");
							kid1_dob = kid1_dob[2]+'-'+kid1_dob[1]+'-'+kid1_dob[0];*/
							$("#kid1_date_birth").val(response.kid1_dob);
						}
						if(response.kid2_dob){
							/*var kid2_dob = response.kid2_dob.split("-");
							kid2_dob = kid2_dob[2]+'-'+kid2_dob[1]+'-'+kid2_dob[0];*/
							$("#kid2_date_birth").val(response.kid2_dob);
						}	
						$("#kid1_rel").val(response.kid1_rel);
						$("#kid2_rel").val(response.kid2_rel);		


						//define allowed family relation
						var allowedRel = [0,1,2,3];
						$.each(allowedRel, function( index, value ) {
						  	if(membersCovered.toString().indexOf(value) == -1){
						  		//remove from relation with proposer dropdown
						  		$("#family_members_id1 option[value='"+value+"']").hide();
						  	}else{
						  		$("#family_members_id1 option[value='"+value+"']").show();
						  	}
						});		
					}
				});
			}


		}else{
			if($(this).val()){
				var sumInsuresType = $("#sum_insures :selected").attr("data-type");
		        if($('.commonCls').length > 0){
		        	apply_button();
		        	console.log('---->'+deletecalled);
		        	if(deletecalled == 0){
		        		if(famconsCount >= memberCount){
			        		swal("Alert", "Below Member Details Section would get modified or deleted. Press OK to Proceed", "warning");

			        	}else{
			        		famconsCount++;
			        	}
		        	}else{
		        		deletecalled = 0;
		        	}
		        	

		        }
		        
				if (sumInsuresType == "memberAge") {
		            getPremiumByAge("patFamilyConstruct");
				 //	getRelation();
		        }
				var family_construct = $('#patFamilyConstruct option:selected').val();
				var result = family_construct.split('+');
				
				var add_length = [];
				add_length.push(parseInt(result[0]));
				add_length.push(parseInt(result[1]));

				if(!$('#patFamilyConstruct').is('[disabled=disabled]')){
				// swal("Alert","Below Member Details Section would get modified or deleted. Press OK to Proceed.");
				var boxlen = $('.commonCls').length;	
				var icount;
				if(boxlen == ''){
					icount = 0
				}else{
					icount = 0+boxlen;
				}
				//$("#add_more").empty();
				
				
				for (i = icount; i < sum(add_length); i++) {
					common_append(i);
				}
				
				$("#add_btn_view").show();
						
				}	

				if($('select[name=sum_insure]').val() != '' && $('select[name=sum_insure] :selected').attr("data-policyno") != '' && $('select[name=familyConstruct]').val() != '' && $('#plan_name').val() != ''){
					$.post("/tele_get_premium", { "sum_insured":  $('select[name=sum_insure]').val(), "policy_detail_id": $('select[name=sum_insure] :selected').attr("data-policyno"), "family_construct":  $('select[name=familyConstruct]').val(),"gci_optional":  $('input[name=GCI_optional]:checked').val(),"product_id" : $('#plan_name').val() }, function (e)
					{
						//debugger;
						$("#premiumModalHidden").val(e);
						e = JSON.parse(e);
						
						var premium = 0;
						$("#premiumModalBody").html("");
						var str = '';
						e.forEach(function (e1) {
							//console.log(e1);
							str += "<div style='display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;'><div><span style='color:#da8085;'> Name:  " + "<br/></span><span style='color:#000;'>" + e1.policy_sub_type_id + "</span></div>";
							str += "<div><span style='color:#da8085;'> Sum Insured </span>" + "<br/><span style='color:#000;'>" + e1.sum_insured + "</span></div>";
							str += "<div><span style='color:#da8085;'> Premium" + "</span><br/><span style='color:#000;'>" + parseFloat(e1.PremiumServiceTax).toFixed(2) + "</span></div></div>";
							
							$("#premiumModalBody").append(str);
							premium += parseFloat(e1.PremiumServiceTax);
						});
						//console.log(get_premium+']]]]]');
						if(str != ''){
							get_premium = str;
							$('#premiumModalBody').html(str);  
							
						}
						 $("#premium").val(premium.toFixed(2));
						 if(parseInt(premium) != 0 && !isNaN(premium)){
						 	total_premium = premium;
							$('#premium').val(premium);  
							
						}
					});
				}
			} 
			getRelation();	
		}


		
				
	});
		
	//siddhi
	function sum(input) { 
		if (toString.call(input) !== "[object Array]") 
		return false; 
		  
		var total = 0; 
		for(var i=0;i<input.length;i++) {                  
			if(isNaN(input[i])) { 
				continue; 
			} 
			  
			total += Number(input[i]); 
		} 
		return total; 
	} 
	
	function addDependentForm(tbody, elem) 
	{
		//debugger;
		var famrelarr = [];
		var addDependent = null;
		var premium_ghi = premium_gpa = premium_vtl = ind_premium =0;
		$("#add_more").empty();
		var i = 0;
		//console.log(elem);
		var length = 0;
		var members_arr = [];
		elem.data.forEach(function (e) 
		{
			debugger;
				if (!members_arr.includes(e['fr_id'])) {
                     members_arr.push(e['fr_id']);
                                      
				}
			//debugger;
			//alert(e.policy_sub_type_id);
			
			if(e.policy_sub_type_id == 1)
			{
			premium_ghi = parseFloat(e.policy_mem_sum_premium);

			}
			if(e.policy_sub_type_id == 2)
			{
				premium_gpa += parseFloat(e.policy_mem_sum_premium);
				
			}
			if(e.policy_sub_type_id == 3)
			{
				
				premium_vtl += parseFloat(e.policy_mem_sum_premium);
				ind_premium = e.policy_mem_sum_premium;
				
			}
			
			//alert(e['family_relation_id']);
			if (!famrelarr.includes(e['family_relation_id'])) {
				famrelarr.push(e['family_relation_id']);
				common_append(i,e);
				if (e.message) 
				{
					addDependent = {
						"message": e.message,
						"premium": e.new_premium,
					}
				}
				var family_members_id = $("#family_members_id"+i)
				var family_salutation = $("#family_salutation"+i)
				var family_gender = $("#family_gender"+i)
				var first_name = $("#first_name"+i)
				var last_name = $("#last_name"+i)
				var family_date_birth = $("#family_date_birth"+i)
				var age = $("#age"+i)
				var age_type = $("#age_type"+i)
				var mem_email_id = $("#mem_email_id"+i)
				var mem_mob_no = $("#mem_mob_no"+i)

				if(e.firstname == '' || e.firstname == undefined){
					e.firstname = e.policy_member_first_name;
				}
				if(e.lastname == '' || e.lastname == undefined){
					e.lastname = e.policy_member_last_name;
				}
				if(e.gender == '' || e.gender == undefined){
					e.gender = e.policy_mem_gender;
				}
				if(e.dob == '' || e.dob == undefined){
					e.dob = e.policy_mem_dob;
				}
				family_members_id.val(e.fr_id);
				family_members_id.css("pointer-events",'none');
				family_gender.val(e.gender);
				first_name.val(e.firstname.toUpperCase()).prop('disabled',true);
				if(e.lastname != undefined){
					last_name.val(e.lastname.toUpperCase()).prop('disabled',true);
				}else{
					last_name.val(e.lastname).prop('disabled',true);
				}
				
				family_date_birth.val(e.dob).prop('disabled',true);
				age.val(e.age);
				
				if(e.fr_id != 1 && e.fr_id != 0 && e.fr_id != 2 && e.fr_id != 3)
				{
					salutation_hide_show(this,e.policy_mem_gender);
					family_salutation.prop('disabled',false);
				}
				else
				{
					family_salutation.prop('disabled',true);
				}
				
				set_salutation(family_salutation,e.fr_id);
				age_type.val(e.age_type);
				mem_email_id.val(e.policy_member_email_id);
				mem_mob_no.val(e.policy_member_mob_no);
				declarepopoulate(e.emp_id, e.policy_member_id,i);
				
				
				if(e.fr_id != '0'){
					$("#edit_btn"+i).show();
				}

				if($("#plan_name").val() == 'R06'){
					$("#edit_btn"+i).show();
				}
				
				$("#delete_btn"+i).show();
				
				$(".disease"+i).css("pointer-events","none");
			
				i++;
					
				} 
			
			
		  
        });
		
		var family_construct = $('#patFamilyConstruct option:selected').val();
		if(family_construct != ''){
			var result = family_construct.split('+');
		
			var add_length = [];
			add_length.push(parseInt(result[0]));
			add_length.push(parseInt(result[1]));
			debugger;
			if(sum(add_length) > members_arr.length){
				var check_count = (sum(add_length) - members_arr.length);
				var i = 0;
				var x = members_arr.length;
				for (i = 0; i < check_count; i++) {
					//common_append(elem.data.length);
					
					common_append(x++);

				}
			}
			
			$("#add_btn_view").show();

			if(sum(add_length) == elem.data.length){
				//$("#patFormSubmit").hide();
			}

			if(elem.data.length == 0) 
			{
				$("#add_more").empty();
				$("#add_btn_view").hide();
				$("#sum_insures").val('');
				$("#patFamilyConstruct").val('');
				$("select[name=familyConstruct]").html('<option value="" selected>Select</option>');

				$("#premium").val('');
				//$("#sum_insures").removeAttr('disabled', 'disabled');
				$("#plan_name").removeAttr('disabled', 'disabled');
				if($('#plan_name').val() == 'T03'){
							 	$("#sum_insures").removeAttr("disabled", "disabled");
							  	$("#patFamilyConstruct").removeAttr("disabled", "disabled");
							 }				
				//$("#patFamilyConstruct").prop('disabled', false);
				//$('input[name=GCI_optional]').attr("disabled",false);
				
			}
			
		}
    
        if (addDependent && addDependent.message && addDependent.premium)
		{
            swal("Alert", addDependent.message, "warning");
            $("#premium").val(addDependent.premium)
        }

        //change premium after insertion
        //console.log($('#plan_name').val()+"= here ="+$("#sum_insures").val());
        if($('#plan_name').val() != '' && $("#sum_insures").val() != ''){
        	var premHtml = '';
        	//console.log(premium_ghi+ '----' +premium_gpa+ '----'+premium_vtl+'----'+ind_premium+$("#plan_name").val());
        	if(premium_ghi != 0){
        		premHtml += '<div style="display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;"><div><span style="color:#da8085;"> Name:  <br></span><span style="color:#000;">Group Health Insurance</span></div><div><span style="color:#da8085;"> Sum Insured </span><br><span style="color:#000;">'+$("#patFamilyConstruct").val()+'</span></div><div class="mr-3"><span style="color:#da8085;"> Premium</span><br><span style="color:#000;">'+premium_ghi+'</span></div></div>';
        	}
        	if(premium_gpa != 0){
        		premHtml += '<div style="display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;"><div><span style="color:#da8085;"> Name:  <br></span><span style="color:#000;">Group Personal Accident</span></div><div><span style="color:#da8085;"> Sum Insured </span><br><span style="color:#000;">'+$("#patFamilyConstruct").val()+'</span></div><div class="mr-3"><span style="color:#da8085;"> Premium</span><br><span style="color:#000;">'+premium_gpa+'</span></div></div>';
        	}
        	//alert(premium_vtl);
        	if(premium_vtl != 0){
        		if($('#plan_name').val() == 'T01'){
        			premHtml += '<div style="display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;"><div><span style="color:#da8085;"> Name:  <br></span><span style="color:#000;">Group Critical Insurance</span></div><div><span style="color:#da8085;"> Sum Insured </span><br><span style="color:#000;">'+$("#patFamilyConstruct").val()+'</span></div><div class="mr-3"><span style="color:#da8085;"> Premium</span><br><span style="color:#000;">'+premium_vtl+'</span></div></div>';
        			var gci_premium = premium_vtl;
        		}else{
        			premHtml += '<div style="display: flex; flex-direction:row; justify-content:space-between; padding-top:10px;"><div><span style="color:#da8085;"> Name:  <br></span><span style="color:#000;">Group Critical Insurance</span></div><div><span style="color:#da8085;"> Sum Insured </span><br><span style="color:#000;">'+$("#patFamilyConstruct").val()+'</span></div><div class="mr-3"><span style="color:#da8085;"> Premium</span><br><span style="color:#000;">'+premium_vtl+'</span></div></div>';
        			var gci_premium = premium_vtl;
        		}
        	}
        	//debugger;
        	if(premHtml != ''){
        		//console.log(premHtml);
        		//debugger;
        		get_premium = premHtml;
        		$('#premiumModalBody').html(premHtml);        		
        	}
        	var totalPremium = parseInt(premium_ghi) + parseInt(premium_gpa) + parseInt(gci_premium);
        	//alert(totalPremium+'===='+premHtml);
        	if(parseInt(totalPremium) != 0 && !isNaN(totalPremium)){
        		//debugger;
        		//alert("i am here");
        		total_premium = totalPremium;
        		$('#premium').val(totalPremium);
        		$('#premium').text(totalPremium);
        	}

        }else{
        	
        	//debugger;
        	get_premium = '';
			total_premium = 0;
			$("#premium").val('0');
			$("#premiumModalBody").html("");
			$("#sum_insures").val();

        }
      
	}
	
	function common_append(j,e =''){
		//console.log("---- j = "+j+"---"+e);
		debugger;
		
		//marked as display none for other products 
		var new_fileds = '<div class="col-md-3" style="display:none;"><div class="form-group"><label for="example-text-input" class="col-form-label">Email ID <span style="color:#FF0000">*</span></label><input class="form-control mem_email_id commonCls sahil ignore" type="text" value="" id="mem_email_id'+j+'" name="mem_email_id[]" autocomplete="off" maxlength="50"></div></div><div class="col-md-3" style="display:none;"><div class="form-group"><label for="example-text-input" class="col-form-label">Mobile No <span style="color:#FF0000">*</span></label><input class="form-control mem_mob_no commonCls sahil ignore" type="text" value="" id="mem_mob_no'+j+'" name="mem_mob_no[]" autocomplete="off" maxlength="50"></div></div>';
		//adding 2 new fileds for GHI with EW
		if($('#plan_name').val() == 'T03'){
			//alert($('#hidden_policy_id').val());
			if($('#hidden_policy_id').val() == TELE_HEALTHPROINFINITY_GHI){
				var new_fileds = '<div class="col-md-3"><div class="form-group"><label for="example-text-input" class="col-form-label">Email ID <span style="color:#FF0000">*</span></label><input class="form-control mem_email_id commonCls sahil" type="text" value="" id="mem_email_id'+j+'" name="mem_email_id[]" autocomplete="off" maxlength="50"></div></div><div class="col-md-3"><div class="form-group"><label for="example-text-input" class="col-form-label">Mobile No <span style="color:#FF0000">*</span></label><input class="form-control mem_mob_no commonCls sahil" type="text" value="" id="mem_mob_no'+j+'" name="mem_mob_no[]" autocomplete="off" maxlength="50"></div></div>';
			}
		}
		console.log(new_fileds);
		var add_div_new = '<div class="row mt-2 mb-2 divmem'+j+'"><div class="col-md-10">Member '+(j+1)+'</div><div class="col-md-2 text-center"><button class="btn sub-btn hide_proposal" style="margin-right: 4px; background: #FB8C00 !important;border: none;padding: 5px 15px; display:none;" id ="edit_btn'+j+'" type="button" onclick="editMember('+j+')">Edit</button><button class="btn sub-btn hide_proposal del_btn_member" style="background: #E53935 !important;border: none;padding: 5px 15px;" id ="delete_btn'+j+'" type="button" data-emp-id=' + e.emp_id + ' data-policy-member-id=' + e.policy_member_id + ' onclick="deleteMember('+j+')">Delete</button></div></div>';
		
		var add_div = '<div class="col-md-3"><div class="form-group"><label class="col-form-label">Relation With Proposer<span style="color:#FF0000">*</span></label><select class="form-control family_members_id1" id ="family_members_id'+j+'" name="family_members_id[]" onchange="changes(this);"><option value="" >Select</option></select></div></div><div class="col-md-1"><div class="form-group"><label class="col-form-label">Salutation<span style="color:#FF0000">*</span></label><select class="form-control family_salutation" id ="family_salutation'+j+'" name="family_salutation[]" style="padding: 5px 0px; font-size: 13px !important;"><option value="" >Select</option><option value="Mr" class="male_gen">Mr</option><option value="Mrs" class="female_gen">Mrs</option><option value="Ms" class="female_gen">Ms</option><option value="Master" class="male_gen">Master</option></select></div></div><div class="col-md-2"><div class="form-group"><label class="col-form-label">Gender<span style="color:#FF0000">*</span></label><input class="form-control family_gender dis-col" type="text" id ="family_gender'+j+'" name="family_gender[]" id="family_gender1" readonly><p class="p-gender">Auto selected basis salutation value opted.</p></div></div><div class="col-md-3"><div class="form-group"><label for="example-text-input" class="col-form-label">First Name <span style="color:#FF0000">*</span></label><input class="form-control first_name commonCls sahil" type="text" value="" id ="first_name'+j+'" name="first_name[]" autocomplete="off" maxlength = "50" ></div></div><div class="col-md-3"><div class="form-group"><label for="example-text-input" class="col-form-label">Last Name </label><input class="form-control last_name" type="text" value="" id ="last_name'+j+'" name="last_name[]" maxlength = "50" autocomplete="off" ><span id="err_last_nameArr" class="error"></span></div></div><div class="col-md-3"><div class="form-group"><label for="example-date-input" class="col-form-label">Date of Birth<span style="color:#FF0000">*</span></label><input class="form-control family_date_birth" autocomplete="off" type="text" id ="family_date_birth'+j+'" name="family_date_birth[]" readonly="readonly"><span id="err_family_date_birthArr" class="error"></span></div></div><div class="col-md-3" style="display: block" ><div class="form-group"><label for="example-text-input" class="col-form-label">Age<span style="color:#FF0000">*</span></label><input class="form-control dis-col age1" type="text" id ="age'+j+'" name="age[]" readonly=""></div></div>'+new_fileds+'<div class="col-md-3" style="display: none"><div class="form-group"><label for="example-text-input" class="col-form-label">Age Type (Year(s)/Day(s)) <span style="color:#FF0000">*</span></label><input type="text" class="form-control age_type1" id ="age_type'+j+'" name="age_type[]" readonly=""></div></div><div class="col-md-12 disease'+j+'" style="display: block"><div class="member_declare_benifit_4s'+j+' col-md-12"></div><div class="tt quest_declare_benifit_4s'+j+' col-md-12"></div><input type="hidden" name="edit_member_id[]" value=' + e.policy_member_id + '></div>';
			 
			var div = $("<div />");	
			var div_new = $("<div />");	
			div_new.html("<div>"+add_div_new);
			div.html('<div class="col-md-12 bor-rr row mb-2 divmem'+j+' ">'+add_div);
			
			  $("#add_more").append(div_new);
			  $("#add_more").append(div);
		
			selectChange = $("#family_members_id"+j).closest("form");
			var sumInsuresType = selectChange.find('select[name=sum_insure] :selected').attr("data-type");
			var family_construct = selectChange.find('select[name=familyConstruct]');

			
		
		$(".family_date_birth").datepicker({
						dateFormat: "dd-mm-yy",
						prevText: '<i class="fa fa-angle-left"></i>',
						nextText: '<i class="fa fa-angle-right"></i>',
						changeMonth: true,
						changeYear: true,
						yearRange: "-100Y:+1D",
						maxDate: "-1D",
						//minDate: "-55Y +1D"
						onSelect: function (dateText, inst) 
						{
							$(this).val(dateText);
							var selectChange=$(this).closest(".row");
							get_age(dateText,selectChange);
						}
					});
		// debugger;
		$.ajax({
				url: "/tele_policy_data",
				type: "POST",
				data: 
				{},
				async: false,
				dataType: "json",
				success: function (response) 
				{
					debugger;
					
						$("#benifit_4s").css("display", "block");
						var i;
						for (i = 0; i < response.length; i++) 
						{
							//console.log(response[i].product_name+'---'+response[i].master_policy_no);
								$("#hidden_policy_section").val(1);
								$("#pr_name").html(response[i].product_name);
								$("#pr_name").html('Axis Tele Inbound Affinity Portal for ABHI');
								//$("#plan_name").val(response[i].product_code);
								$("#master_policy_number").val(response[i].master_policy_no);
								if (response[i].policy_sub_type_id == 1) 
								{

									$(".personal_accident").text(response[i].policy_sub_type_name);
									$("#benifit_4s").css("display", "block");

									$("#subtype_text").val(response[i].policy_sub_type_id);
									//debugger;
									//console.log(response[i].max_adult+"===="+response[i].fr_id);

									if ((response[i].max_adult > 1 && response[i].fr_id == 1) ||response[i].fr_id == 0)
									{	//console.log(response[i].fr_id)
										$("#family_members_id"+j).append('<option data-opt="' +response[i].gender_option +'" value="'+response[i].fr_id +'">' +response[i].fr_name +"</option>");
									} 
									else if (response[i].max_adult == 2 &&response[i].max_child == 0 &&(response[i].fr_id == 4 ||response[i].fr_id == 5 ||response[i].fr_id == 6 ||response[i].fr_id == 7)) 
									{
											$("#family_members_id"+j).append('<option data-opt="' +response[i].gender_option +'" value="' +response[i].fr_id +'">' +response[i].fr_name +"</option>");
									} 
									else if (response[i].max_adult == 4 &&response[i].max_child == 0 &&response[i].fr_id == 5 ||response[i].fr_id == 6 ||response[i].fr_id == 7)
									{
											$("#family_members_id"+j).append('<option data-opt="' +response[i].gender_option +'" value="' +response[i].fr_id +'">' +response[i].fr_name +"</option>");
									} 
									else if(response[i].max_adult > 2 &&(response[i].fr_id == 4 ||response[i].fr_id == 5 ||response[i].fr_id == 6 ||response[i].fr_id == 7 ||response[i].fr_id == 1 ||response[i].fr_id == 0))
									{
											$("#family_members_id"+j).append('<option data-opt="' +response[i].gender_option +'" value="' +response[i].fr_id +'">' +response[i].fr_name +"</option>");
									}
									else if (response[i].fr_id == 2 || response[i].fr_id == 3)
									{
											$("#family_members_id"+j).append('<option data-opt="' +response[i].gender_option +'" value="' +response[i].fr_id +'">' +response[i].fr_name +"</option>");
									}
								}
								//console.log($("#family_members_id"+j).html());
						}
				}
							   
								
			});
			
			if (sumInsuresType == "family_construct") 
			{
				//debugger;
				getPremium(selectChange);
				if(get_premium != ''){
					$('#premiumModalBody').html(get_premium);  
					
				}
				
				getRelation();
			
			} else if (sumInsuresType == "family_construct_age") 
			{
				if($('#plan_name').val() != 'T03'){
					getPremiumByAge(family_construct);
				}
			
			}
			//console.log($('#plan_name').val())
			if($('#plan_name').val() == 'R06'){
				$.ajax({
					url: "/tele_member_declare_data",
					type: "POST",
					dataType: "html",
					data: {member_id:j},
					success: function (response)
					{
						//console.log(response);
						$(".member_declare_benifit_4s"+j).html(response)
					 
					}
				});
			}
			
					
					
			  
	}
	
	function set_salutation(elem,val){
		var salutation_set = '';
		$.ajax
			({
				url: "/tele_family_details",
				type: "POST",
				data: {relation_id: 0},
				dataType: "json",
				async:false,
				beforeSend: function() {
				        set_session();
				    },
				success: function (response) 
				{
					salutation_set = response.family_data[0].salutation;
					//debugger;
					if(response.family_data[0].product_id == 'T01'){
						$(".GCIOptionalDiv").show();
						if(response.family_data[0].GCI_optional == 'No'){
							$('#GCI_no').prop('checked', true);
						}else{
							$('#GCI_yes').prop('checked', true);
						}
					}
					$('#annual_income').val(response.family_data[0].annual_income);
					$('#occupation').val(response.family_data[0].occupation);
 
				}
			});
				
				if(val == 0){
					elem.val(salutation_set);
				}else{
					
					if(val == 1){
						if(salutation_set == 'Mr'){
							elem.val('Mrs');
						}else{
							elem.val('Mr');
						}
					}else{
						if(val == 2){
							elem.val('Master');
						}else{
							elem.val('Ms');
						}
					}
				}
	}
	
	function  editMember(i) 
	{	
		
		if($("#family_members_id"+i).val() == 0){			
			if(proposal_created == 1){
				swal({
						title: "Alert",
						text: "SELF can be modified in CUSTOMER DETAILS section only.",
						type: "warning",
						showCancelButton: true,
						confirmButtonText: "Ok!",
						closeOnConfirm: true,
						allowOutsideClick: false,
						closeOnClickOutside: false,
						closeOnEsc: false,
						dangerMode: true,
						allowEscapeKey: false
					} ,
						function () {
							setTimeout(function() { $('#firstname').focus(); }, 2000);
							$('#self_edit_clicked').val(1);
							$("#edit_btn_custsection").trigger("click");
						   	return false;
						});
			}else{
				if($("#family_members_id"+i).val() != ''){
					swal({
						title: "Alert",
						text: "SELF can be modified in CUSTOMER DETAILS section only. This would auto-delete Member details of SELF to be refilled by USER",
						type: "warning",
						showCancelButton: true,
						confirmButtonText: "Ok!",
						closeOnConfirm: true,
						allowOutsideClick: false,
						closeOnClickOutside: false,
						closeOnEsc: false,
						dangerMode: true,
						allowEscapeKey: false
					} ,
						function () {
							setTimeout(function() { $('#firstname').focus(); }, 2000);
							$('#self_edit_clicked').val(1);
							$("#edit_btn_custsection").trigger("click");
						   	return false;
						});
				}
				
			}
	
		}else{
			if(proposal_created == 1){
				$("#first_name"+i).prop('disabled',false);
				$("#last_name"+i).prop('disabled',false);
			}else{
				// $("#family_members_id"+i).css('pointer-events','');
				$("#first_name"+i).prop('disabled',false);
				$("#last_name"+i).prop('disabled',false);
				$("#family_date_birth"+i).prop('disabled',false);
				//$("#edit_btn"+i).prop('disabled', true); 
				$("#edit_btn"+i).hide(); 
				$("#patFormSubmit").show();
				$(".disease"+i).css("pointer-events","auto");
				
				$("#first_name"+i).focus();
				if($("#plan_name").val() == 'R06' && $("#family_members_id"+i).val() == 0){

					$("#first_name"+i).prop('disabled',true);
					$("#last_name"+i).prop('disabled',true);
					$("#family_date_birth"+i).prop('disabled',true);
				}
			}
		}
					
		
	}
	
	
	function  deleteMember(i) 
	{
		debugger;
		var mem_id = $("#delete_btn"+i).data("policy-member-id");
		//alert(typeof mem_id);
		if(mem_id != "undefined"){
			
			
			$.ajax({
					url: "/tele_delete_member",
					type: "POST",
					async: false,
					data:{'emp_id':$("#delete_btn"+i).attr("data-emp-id"), 'policy_member_id':$("#delete_btn"+i).attr("data-policy-member-id")},
					success: function (e) 
					{
						if (!e) 
				{
					
					$("#delete_btn"+i).closest("form").find("select[name='family_members_id[]']").css("pointer-events",'auto')
					swal("Success", "Member Deleted Successfully", "success");
					
					$(".quest_declare_benifit_4s").empty();
					$('#chronic th input[type="checkbox"]').prop('checked',false);
					
					get_all_data('deletecalled');
					$("#patFormSubmit").show();
					//var divCount = $('.commonCls').length;
					common_append(i);
					//siddhi
					//$("#patFamilyConstruct").change();
					
				}
					   
					}
			    });
			//alert("in "+mem_id);
			
		}else{
			//alert("out "+mem_id);
			if(($("#patFamilyConstruct").val() != '' || $("#patFamilyConstruct").val() != undefined) && ($("#sum_insures").val() != '' || $("#sum_insures").val() != undefined)){
							var strcon = $("#patFamilyConstruct").val();
							if (strcon.indexOf('+') > -1)
							{
								// alert("+ found");
							  	var res = strcon.split("+");
								count = 0;
								if(res[0] != '' || res[0] != undefined){
								  count = count + parseInt(res[0].replace('A',''));
								}
								if(res[1] != '' || res[1] != undefined){
								  count = count + parseInt(res[1].replace('K',''));
								}
							}else{
								//alert("+ not found");
								count = parseInt(strcon.replace('A',''));
							}
							
							var divCount = $('.commonCls').length;
							// alert(divCount+ '=='+count);
							if(divCount != count ){
								// alert('data deleted');
								$('.divmem'+i).parent().remove();
							}
						}
			
		}

		if($('.commonCls').length == 0){
			$('#hidden_deductable').val('');
			$('#hidden_policy_id').val('');
		}
		
		
		
	}
	

	function declarepopoulate(emp_edit_id, emp_edit_policy_mem, j = '')
	{


		 /*$.ajax({
					url: "/tele_edit_declare_member_data",
					type: "POST",
					async: false,
					data:{'emp_edit_id':emp_edit_id,'emp_policy_mem':emp_edit_policy_mem},
					dataType: "html",
					success: function (response) 
					{
						$(".quest_declare_benifit_4s"+j).html(response);
					   
					}
			    });*/

			$.post('/tele_subtype_id', {
				'emp_edit_id': emp_edit_id,
				'emp_policy_mem': emp_edit_policy_mem,
				}, function (e) 
				{
					
					var obj = JSON.parse(e);
					if (obj != null) 
					{
						
						var len = obj.length;
						var i;
						for (i = 0; i < len; i++) {
						var member = obj[i];

						if (member.declare_sub_type_id) 
						{
							$("#subdeclare_" + member.declare_sub_type_id + j).prop("checked", true);
							get_sub_data(member.declare_sub_type_id,j);
						}
						else 
						{

							$("#subdeclare_" + member.declare_sub_type_id + "_1" + j).prop("checked", true);
						}


						}
					}
				});

		  $.ajax({
					url: "/tele_ghd_emp_declare",
					type: "POST",
					async: false,
					data:{'emp_edit_id':emp_edit_id,'emp_policy_mem':emp_edit_policy_mem},
					dataType: "json",
					success: function (response) 
					{
				
						var obj = response;
						if (obj != null && obj.length > 1) 
						{
							var len = obj.length;
							var i;
							for (i = 0; i < len; i++)
							{
								var member = obj[i];
							
								if (member.format) 
								{
								 
									
									
									  
									  if((member.type == 'C1' && member.format == 'No') || (member.type == 'C2' && member.format == 'No'))
									 {
										
										  $("#C_data").show();
										
									  }
									  if((member.type == 'D1') || (member.type == 'D2') || (member.type == 'D3') || (member.type == 'D4'))
									 {
										
										  $("#D_data").show();
									  }
									  
									   if((member.type == 'E' && member.format == 'No') || (member.type == 'E' && member.format == 'Yes'))
									 {
										
										  $("#E_data").show();
									  }
									 if(member.format == 'Yes')
									 {
										 $("#" + member.type+"_remark").show();
										 $("#" + member.type+"_remark").val(member.remark);
										 
										 $("#" + member.type+"_1").prop("checked", true);
									 }
									 else
									 {
										 $("#" + member.type+"_2").prop("checked", true);
									 }
								
								}
							
						

							}
					    }
					}
			    });

	}


	function setFamilyConstruct(value,eventcheck = '') 
	{
		debugger;
		var tax_with_premium = $("#sum_insures option:selected").data("id");

        if ($("#sum_insures :selected").attr("data-type") == "family_construct" || $("#sum_insures :selected").attr("data-type") == "family_construct_age") 
		{

			var PremiumServiceTax;
			
			$.ajax({
					url: "/tele_family_construct",
					type: "POST",
					async: false,
					data:{
					"policyNo": $("#sum_insures :selected").attr("data-policyno"),
					"sumInsured": $("#sum_insures :selected").val(),
					"table": $("#sum_insures :selected").attr("data-type"),
					},
					beforeSend: function() {
				        set_session();
				    },
					success: function (e) 
					{
						$("#patFamilyConstruct").empty();
						$("#patFamilyConstruct").html('<option value="" selected>Select</option>');
						e = JSON.parse(e);
						$("#patFamilyConstructDiv").show();
						if (e)
						{
							e.forEach(function (e1) 
							{
								PremiumServiceTax = e1.PremiumServiceTax;
								
							 
								$("#patFamilyConstruct").append("<option value='" + e1.family_type + "' data-premium='" + PremiumServiceTax + "'>" + e1.family_type + "</option>");
							});
							if(value) 
							{
								$("#patFamilyConstruct").val(value);
							}
							var is_edit = $("input[name='edit']").val();
							
							if(value && is_edit == 0) 
							{
								if(eventcheck == 'deletecalled'){
									deletecalled = 1;
								}
								$("#patFamilyConstruct").change();
							}
						}
					}
			});
			apply_button();
        }
        else 
		{
            $("#patFamilyConstructDiv").hide();
        }
        
	}

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
	$("#" + id).append("<option data-type='" + type + "' data-policyNo='" + e1["policy_detail_id"] + "' data-customer = '" + e1.premium + "'  data-id = '" + e1.PremiumServiceTax + "' family_child= '" + e1.child + "' family_adult= '" + e1.adult + "' value =" + e1.sum_insured + ">" + e1.sum_insured + "</option>");
	}
    }

	
	/*function setMasPolicy(type, e1)
	{

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

			if (type == "flate") 
			{
				var sumInsured = e1["sum_insured"].split(",");
				var PremiumServiceTax = e1["PremiumServiceTax"].split(",");
				for (k = 0; k < sumInsured.length; ++k) 
				{
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
			} 
			else
			{

				if (type == "memberAge") 
				{
				$("#patFamilyConstructDiv").hide();
				e1["PremiumServiceTax"] = e1["premium"];

				}
				$("#sum_insures").append("<option data-type='" + type + "' data-policyNo='" + e1.policy_detail_id + "' data-customer = '" + e1.premium + "'  data-id = '" + e1.PremiumServiceTax + "' family_child= '" + e1.child + "' family_adult= '" + e1.adult + "' value =" + e1.sum_insured + ">" + e1.sum_insured + "</option>");
			}

	}*/	
			
	function get_all_data(eventcheck = '')
	{
	proposal_create_check();	 

	//debugger; 
	  //alert('Get All data - '+$('#plan_name').val());

	  $.ajax({
					url: "/tele_get_all_data",
					type: "POST",
					dataType: "json",
					data: {product_id:$('#plan_name').val()},
					beforeSend: function() {
				        set_session();
				    },
					success: function (response) 
					{
						//debugger;
						
						if(response.data == '' || jQuery.isEmptyObject(response.data)){
							get_premium = '';
							total_premium = 0;
							$("#premium").val('0');
							$("#premiumModalBody").html("");
							$("#sum_insures").val();
							$("#hidden_policy_section").val(0);
						}else{
							if(eventcheck != ''){
								memberCount = response.data.length+2;
							}else{
								memberCount = response.data.length;
							}
							
							$("#plan_name").attr("disabled", "disabled");
							if($('#plan_name').val() == 'T03'){
							 	$("#sum_insures").attr("disabled", "disabled");
							  	$("#patFamilyConstruct").attr("disabled", "disabled");
							 }
							show_popup = 1;
						}

					  
						//$("#sum_insures").attr("disabled", "disabled");
						//$("#patFamilyConstruct").attr("disabled", "disabled");
						$("#premium1").attr("disabled", "disabled");
						//$('input[name=GCI_optional]').attr("disabled",true);
						var patFamilyConstruct = $("#patFamilyConstruct").val();
						var sum_insures = $("#sum_insures").val();
						if(sum_insures == '')
						{
							 $("#plan_name").removeAttr("disabled", "disabled");
							 if($('#plan_name').val() == 'T03'){
							 	$("#sum_insures").removeAttr("disabled", "disabled");
							  	$("#patFamilyConstruct").removeAttr("disabled", "disabled");
							 }
							  
							  //$('input[name=GCI_optional]').attr("disabled",false);
						}
					    var premium1 = $("#premium1").val();
					    $("#premium1").val(premium1);

					    if (response.constructor == String)
						{

						   addDependentForm("vtlTable", JSON.parse(response));
					    }
					    else 
						{
							var patTable = {};
							patTable.data = [];
							var vtlTable = {};
							vtlTable.data = [];
						
							for (var i = 0; i < response.data.length; i++) 
							{

								/*healthpro changes show annualincome if suminsure > 10 lac*/
									if(response.data[i]['policy_mem_sum_insured'] > 1000000){
										$("#annual_income_label").show();
										$("#occupation_label").show();
										$("#annual_income").removeClass("ignore");
										$("#occupation").removeClass("ignore");
										
									}else{
										$("#annual_income_label").hide();
										$("#occupation_label").hide();
										$("#annual_income").addClass("ignore");
										$("#occupation").addClass("ignore");
									}
								/*healthpro changes end*/
								if (response.data[i]['policy_sub_type_id'] == 3 ) 
								{
														
									$("#sum_insures1").val(response.data[i]['policy_mem_sum_insured']);
									$('#premium1').val(response.data[i]['policy_mem_sum_premium']);
						

								} 
								else if (response.data[i]['policy_sub_type_id'] == 1)
								{
								   //$("#sum_insures").attr("disabled", "disabled");
								  
								   $("#sum_insures").val(response.data[i]['policy_mem_sum_insured']);
							       $('#premium').val(response.data[i]['policy_mem_sum_premium']+".00");
								    //$("#patFamilyConstruct").empty();
								  
									setFamilyConstruct(response.data[i]['familyConstruct'],eventcheck);
									//$("#patFamilyConstruct").attr("disabled", "disabled");
								 // $("#patFamilyConstruct").append('<option value = "'+response.data[i]['familyConstruct']+'" data-premium = "'+response.data[i]['policy_mem_sum_premium']+'">'+response.data[i]['familyConstruct']+'</option>');
								
						        }
						
						    }

						    for (var i = 0; i < response.data.length; i++) 
							{
						        if (response.data[i]['policy_sub_type_id'] == 3)
								{
							
							        vtlTable.data.push(response.data[i]);

						        }
						        else if (response.data[i]['policy_sub_type_id'] == 2) 
								{
							       vtlTable.data.push(response.data[i]);
						        }
								else if (response.data[i]['policy_sub_type_id'] == 1) 
								{
							       vtlTable.data.push(response.data[i]);
						        }
						
						    }

						  
						   addDependentForm("patTable", vtlTable);
						   //addDependentForm("vtlTable", patTable);
					    }  
					    // alert($("#patFamilyConstruct").val()+"==="+$("#sum_insures").val());
					    /*if(($("#patFamilyConstruct").val() != '' || $("#patFamilyConstruct").val() != undefined) && ($("#sum_insures").val() != '' || $("#sum_insures").val() != undefined)){
							var strcon = $("#patFamilyConstruct").val();
							if (strcon.indexOf('+') > -1)
							{
								// alert("+ found");
							  	var res = strcon.split("+");
								count = 0;
								if(res[0] != '' || res[0] != undefined){
								  count = count + parseInt(res[0].replace('A',''));
								}
								if(res[1] != '' || res[1] != undefined){
								  count = count + parseInt(res[1].replace('K',''));
								}
							}else{
								//alert("+ not found");
								count = parseInt(strcon.replace('A',''));
							}
							
							var divCount = $('.commonCls').length;
							// alert(divCount+ '=='+count);
							if(divCount != count ){
								// alert('data deleted');
								common_append(divCount);
							}
						}*/
						debugger;
						if(eventcheck == 'deletecalled'){
							deletecalled = 1;
						}
						$("#patFamilyConstruct").trigger("change");

						if(response.data != '' || !jQuery.isEmptyObject(response.data)){    
							proposal_create_check();
						}	

						/*if(response.data == '')
						{
							apply_onload = 2;
						}*/
					}
					
		    });
		
	}
	function otp_generate() 
	{

		$("#myModal1").modal("hide");
		$("#sms_body").html('');

		ajaxindicatorstop();

		 $.ajax({
					url: "/tele_send_otp",
					type: "POST",
					data: {
					
					},
					async: false,
					dataType: "json",
					success: function (response) 
					{
					 
						$("#pos_otp").val("");
						$("#myModal1").modal("show");
						
					  
					}
			    });
	}
	$(document).on("click", "#validate_otp", function()
	{
	 $.ajax({
				url: "/tele_validate_otp",
				type: "POST",
				data: {otp: $("#pos_otp").val()},
				async: false,
				dataType: "json",
				success: function (response) 
				{
						ajaxindicatorstart();
					if(response.status == 'true')
					{
						
						$("#sms_body").html('<p>'+response.message+'</p>')
						$("#myModal1").modal("hide");
						//proposal_submit_data();
			
					}
					else
					{
						ajaxindicatorstop();
						$("#sms_body").html('<p>OTP validation failed, pls generate it again</p>')
						$("#pos_otp").val("");
						return;
					}
				
					$("#pos_otp").val("");
					
					
				  
				}
			});
		
	});
	$(document).on("click", "#resend_otp", function() 
	{
		otp_generate();
	});
	



	
	$("#emp_agent_data").validate({
			ignore: ".ignore",
			focusInvalid: true,
			rules:  {

					
						axis_location:
						{
							required: true,
						},
						axis_vendor:
						{
							required: true,
						},
						axis_lob:
						{
							required: true,
						},
						axis_process:
						{
							required: true,
						},
						agent_id:
						{
							required: true,
							validate_agent: true,
							
						},
						imd_code:
						{
							required: true,
							
						},
						
				    },
					messages: {
							},
					invalidHandler: function(form, validator) 
					{
						
						validator.focusInvalid();
					},
					submitHandler: function (form) 
					{
						set_session();
						var all_data = $("#emp_agent_data").serialize();
						$.ajax({
						url: "/tele_agent_data_insert",
						type: "POST",
						data: all_data,
						async: false,
						dataType: "json",
						beforeSend: function() {
					        set_session();
					    },
						success: function (response) 
						{
							var res = response;
							if (res.status == false)
							{

								 ajaxindicatorstop();
								 swal({
									title: "Alert",
									text: res.message,
									type: "warning",
									showCancelButton: false,
									confirmButtonText: "Ok!",
									closeOnConfirm: true,
									allowOutsideClick: false,
									closeOnClickOutside: false,
									closeOnEsc: false,
									dangerMode: true,
									allowEscapeKey: false
								} ,
									function () {
									   
									}); return;
							}
							if (res.status == true) 
							{
						
								ajaxindicatorstop();
								swal({
										title: "Success",
										text: "Agent Details Added Successfully",
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
											$("#hidden_agent_section").val(1);
							
									});
								
							}
						}
						});
					}
	});

		$("#emp_data").validate({
			ignore: ".ignore",
			focusInvalid: true,
			rules:  {
				salutation: 
						{
							required: true
						},
						firstname: 
						{
							required: true
						},
						/*lastname: 
						{
							required: true
						},*/
						gender1: 
						{
							required: true
						},
						dob: 
						{
							required: true
						},
						mob_no: 
						{
							required: true
						},
						comAdd: 
						{
							required: true
						},
						mobile_no2:
						{
							//valid_mobile: true,
						},
						pin_code:
						{
							required: true,
							validate_pincode: true,
						},
						city:
						{
							required: true,
						},
						state:
						{
							required: true,
						},
						email:
						{
							required: true,
							validateEmail: true,
						},
				    },
					messages: {
							},
					invalidHandler: function(form, validator) 
					{
						
						validator.focusInvalid();
					},
					submitHandler: function (form) 
					{debugger;
						var agent_details = $("#hidden_agent_section").val();
						//alert(agent_details);
						//created on 10-02-2021 by Akash_Chawan
						if($('#disposition').val()==45||$('#disposition').val()==46){

							if(!$("#emp_agent_data").valid())
							{	
				
								ajaxindicatorstop();
								swal({
									title: "Alert",
									text:"Please FILL Agent Details!",
									type: "warning",
									showCancelButton: false,
									confirmButtonText: "Ok!",
									closeOnConfirm: true,
									allowOutsideClick: false,
									closeOnClickOutside: false,
									closeOnEsc: false,
									dangerMode: true,
									allowEscapeKey: false
								});
				
								return;
							}
							else if(!$("#emp_data").valid())
							{
							
								ajaxindicatorstop();
								swal({
									title: "Alert",
									text:"Please FILL Customer Details!",
									type: "warning",
									showCancelButton: false,
									confirmButtonText: "Ok!",
									closeOnConfirm: true,
									allowOutsideClick: false,
									closeOnClickOutside: false,
									closeOnEsc: false,
									dangerMode: true,
									allowEscapeKey: false
								});
				
								return;
							}				
						}
						/* end  */						
						else if(agent_details == 0){							
							swal({
									title: "Alert",
									text:"Please SAVE the values entered so far to proceed with new Section!",
									type: "warning",
									showCancelButton: false,
									confirmButtonText: "Ok!",
									closeOnConfirm: true,
									allowOutsideClick: false,
									closeOnClickOutside: false,
									closeOnEsc: false,
									dangerMode: true,
									allowEscapeKey: false
								} ,
									function () {
									   
									});  
							return false;
						}
						//var all_data = $("#emp_data").serialize();
						var all_data = $("#emp_data").serialize()+ "&self_edit_clicked="+$("#self_edit_clicked").val()+"&gender1="+$("#gender1").val()+"&dob="+$("#dob1").val()+"&salutation="+$('#salutation').val();
						
						$.ajax({
						url: "/tele_emp_data_insert",
						type: "POST",
						data: all_data,
						async: false,
						dataType: "json",
						beforeSend: function() {
					        set_session();
					    },
						success: function (response) 
						{
							
							var res = response;
							$('#edit_btn_custsection').show();
							if (res.update == 1 && res.proposal_status == 'No')
							{
								if($('#self_edit_clicked').val() == 0){
									swal({
											title: "Alert",
											text: "SELF can be modified in CUSTOMER DETAILS section only. This would auto-delete Member details of SELF to be refilled by USER",
											type: "warning",
											showCancelButton: true,
											confirmButtonText: "Ok!",
											closeOnConfirm: false,
											allowOutsideClick: false,
											closeOnClickOutside: false,
											closeOnEsc: false,
											dangerMode: true,
											allowEscapeKey: false
										} ,
										function () {
											 
										});
								}

								deleteMember(0);								
								
								
								//$("#family_members_id0").trigger("change");							
								
								
								$("#self_edit_clicked").val(0);
								$('#first_name0').focus();
							}else{
								$("#family_members_id0").trigger("change");
							}
							if (res.status == false)
							{

								 ajaxindicatorstop();
								 swal({
									title: "Alert",
									text: res.message,
									type: "warning",
									showCancelButton: false,
									confirmButtonText: "Ok!",
									closeOnConfirm: true,
									allowOutsideClick: false,
									closeOnClickOutside: false,
									closeOnEsc: false,
									dangerMode: true,
									allowEscapeKey: false
								} ,
									function () {
									   
									}); return;
							}
							if (res.status == true) 
							{
						
								ajaxindicatorstop();
								swal({
										title: "Success",
										text: "Customer Details Added Successfully",
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
											debugger;
											
									});
									var empSalutation = $("#salutation").val();
											var empGender = $("#gender1").val();
											if($('#family_members_id1').val() == 1){
												
												if(empSalutation == '2' || empSalutation == '3'){
													$('#family_members_id1').val('Mr');
													//$('#family_gender1').val('Male');
												}else{
													$('#family_members_id1').val('Mrs');
													//$('#family_gender1').val('Female');
												}
												$('#family_members_id1').val(1);
											}
											if($('#family_members_id0').val() == 1){
												
												if(empSalutation == '2' || empSalutation == '3'){
													$('#family_members_id0').val('Mr');
													//$('#family_gender0').val('Male');
													
													
												}else{
													$('#family_members_id0').val('Mrs');
													//$('#family_gender0').val('Female');
												}
												$('#family_members_id0').val(1);
											}
											//common_append(i);
											$("#hidden_customer_section").val(1);
											$('#edit_btn_custsection').show();
    										//$('#update_emp').hide();
											$('#salutation').attr('disabled',true);
								    		$('#firstname').attr('readonly',true);
								    		$('#lastname').attr('readonly',true);
								    		$('#gender1').attr('disabled',true);
								    		$('#dob1').attr('disabled',true);
								    		$('#dob1').attr('readonly',true);
								    		$('#mob_no').attr('readonly',true);
								    		$('#email').attr('readonly',true);
								    		$('#gender1').addClass("dis-col");
											$('#mob_no').addClass("dis-col");
											$('#email').addClass("dis-col");
								
							}
						}
						});
					}
	});
	$("#nominee_data").validate({
			ignore: ".ignore",
			focusInvalid: true,
			rules:  {

				nominee_fname: 
						{
							required: true,
							checkspace_nominee: true
						},
						/*nominee_lname: 
						{
							required: true,
							checkspace_nominee: true
						},*/
						nominee_gender: 
						{
							required: true
						},
						nominee_dob: 
						{
							required: true
						},			
						nominee_relation: 
						{
							required: true
						},
						nominee_contact:
						{
							//required: true,
							//valid_mobile: true
						},
						nominee_salutation:
						{
							required: true,
						},
						nominee_email:
						{
							//required: true,
							//valid_mobile: true
							validateEmail: true,
						},
						
				    },
					messages: {
							},
					invalidHandler: function(form, validator) 
					{
						
						validator.focusInvalid();
					},
					submitHandler: function (form) 
					{
						var agent_details = $("#hidden_agent_section").val();
						var customer_details = $("#hidden_customer_section").val();
						var policy_details = $("#hidden_policy_section").val();
						//alert(agent_details +"====="+customer_details+"====="+policy_details); 


						//created on 10-02-2021 by Akash_Chawan
						if($('#disposition').val()==45||$('#disposition').val()==46){

							if(!$("#emp_agent_data").valid())
							{	
				
								ajaxindicatorstop();
								swal({
									title: "Alert",
									text:"Please FILL Agent Details!",
									type: "warning",
									showCancelButton: false,
									confirmButtonText: "Ok!",
									closeOnConfirm: true,
									allowOutsideClick: false,
									closeOnClickOutside: false,
									closeOnEsc: false,
									dangerMode: true,
									allowEscapeKey: false
								});
				
								return;
							}
							else if(!$("#emp_data").valid())
							{
							
								ajaxindicatorstop();
								swal({
									title: "Alert",
									text:"Please FILL Customer Details!",
									type: "warning",
									showCancelButton: false,
									confirmButtonText: "Ok!",
									closeOnConfirm: true,
									allowOutsideClick: false,
									closeOnClickOutside: false,
									closeOnEsc: false,
									dangerMode: true,
									allowEscapeKey: false
								});
				
								return;
							}				
							else if($('#plan_name').val()==''||$('#sum_insures').val()==''||$('#familyConstruct').val()==''){
		
								ajaxindicatorstop();
								swal({
									title: "Alert",
									text:"Please FILL Policy Details",
									type: "warning",
									showCancelButton: false,
									confirmButtonText: "Ok!",
									closeOnConfirm: true,
									allowOutsideClick: false,
									closeOnClickOutside: false,
									closeOnEsc: false,
									dangerMode: true,
									allowEscapeKey: false
								});
				
								return;
				
				
							}
						}
						/* end  */						
						else if(agent_details == 0 || customer_details == 0 || policy_details == 0){
							swal({
									title: "Alert",
									text:"Please SAVE the values entered so far to proceed with new Section!",
									type: "warning",
									showCancelButton: false,
									confirmButtonText: "Ok!",
									closeOnConfirm: true,
									allowOutsideClick: false,
									closeOnClickOutside: false,
									closeOnEsc: false,
									dangerMode: true,
									allowEscapeKey: false
								} ,
									function () {
									   
									});  
							return false;
						}
						var all_data = $("#nominee_data").serialize()+"&nominee_relation="+$("#nominee_relation").val()+"&nominee_dob="+$('#nomineedob').val()+"&nominee_salutation="+$("#nominee_salutation").val();
						$.ajax({
						url: "/tele_nominee_data_insert",
						type: "POST",
						data: all_data,
						async: false,
						dataType: "json",
						beforeSend: function() {
					        set_session();
					    },
						success: function (response) 
						{
							var res = response;
							if (res.status == false)
							{
								

								 ajaxindicatorstop();
								 swal({
									title: "Alert",
									text: res.message,
									type: "warning",
									showCancelButton: false,
									confirmButtonText: "Ok!",
									closeOnConfirm: true,
									allowOutsideClick: false,
									closeOnClickOutside: false,
									closeOnEsc: false,
									dangerMode: true,
									allowEscapeKey: false
								} ,
									function () {
									   
									}); return;
							}
							if (res.status == true) 
							{
						
								ajaxindicatorstop();
								
								swal({
										title: "Success",
										text: "Nominee Details Added Successfully",
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
										$('#edit_nominee').show();
										$("#hidden_nominee_section").val(1);
									});
								
							}
						}
						});
					}
	});
	

	$("#payment_details").validate({
		ignore: ".ignore",
		focusInvalid: true,
		rules: 
		{
		disposition:
			{
				required: true,
				
			},
			sub_isposition:{
				required:true,
			},
			preferred_contact_date:
			{
				required: true,
				
			},
			preferred_contact_time:
			{
				required: true,
				time_validate:true,			
			},
			/*av_remark:
			{
				required: true,
				
			},*/
		},
		messages: {

		},
		submitHandler: function (form) 
		{
			var agent_details = $("#hidden_agent_section").val();
			var customer_details = $("#hidden_customer_section").val();
			var policy_details = $("#hidden_policy_section").val();
			var nominee_details = $("#hidden_nominee_section").val();


/* updated on 09-02-2021 by Akash_Chawan */			
			if($('#disposition').val()==45||$('#disposition').val()==46){


				if(!$("#emp_agent_data").valid())
				{	
	
					ajaxindicatorstop();
					swal({
						title: "Alert",
						text:"Please FILL Agent Details!",
						type: "warning",
						showCancelButton: false,
						confirmButtonText: "Ok!",
						closeOnConfirm: true,
						allowOutsideClick: false,
						closeOnClickOutside: false,
						closeOnEsc: false,
						dangerMode: true,
						allowEscapeKey: false
					});
	
					return;
				}
				else if(!$("#emp_data").valid())
				{
				
					ajaxindicatorstop();
					swal({
						title: "Alert",
						text:"Please FILL Customer Details!",
						type: "warning",
						showCancelButton: false,
						confirmButtonText: "Ok!",
						closeOnConfirm: true,
						allowOutsideClick: false,
						closeOnClickOutside: false,
						closeOnEsc: false,
						dangerMode: true,
						allowEscapeKey: false
					});
	
					return;
				}
	
				else if($('#plan_name').val()==''||$('#sum_insures').val()==''||$('#familyConstruct').val()==''){
	
					ajaxindicatorstop();
					swal({
						title: "Alert",
						text:"Please FILL Policy Details",
						type: "warning",
						showCancelButton: false,
						confirmButtonText: "Ok!",
						closeOnConfirm: true,
						allowOutsideClick: false,
						closeOnClickOutside: false,
						closeOnEsc: false,
						dangerMode: true,
						allowEscapeKey: false
					});
	
					return;
	
	
				}
	
				else if(!$("#nominee_data").valid())
				{
				
					ajaxindicatorstop();
					swal({
						title: "Alert",
						text:"Please FILL Nominee Details!",
						type: "warning",
						showCancelButton: false,
						confirmButtonText: "Ok!",
						closeOnConfirm: true,
						allowOutsideClick: false,
						closeOnClickOutside: false,
						closeOnEsc: false,
						dangerMode: true,
						allowEscapeKey: false
					});
	
					return;
				}
				
	
				else if(!$("#payment_details").valid()){
					ajaxindicatorstop();
					swal({
						title: "Alert",
						text:"Please FILL Payment Details!",
						type: "warning",
						showCancelButton: false,
						confirmButtonText: "Ok!",
						closeOnConfirm: true,
						allowOutsideClick: false,
						closeOnClickOutside: false,
						closeOnEsc: false,
						dangerMode: true,
						allowEscapeKey: false
					});
	
	
					return;
				}
				
				


			}else{

				if(!$("#emp_agent_data").valid())
				{	
	
					ajaxindicatorstop();
					swal({
						title: "Alert",
						text:"Please FILL Agent Details!",
						type: "warning",
						showCancelButton: false,
						confirmButtonText: "Ok!",
						closeOnConfirm: true,
						allowOutsideClick: false,
						closeOnClickOutside: false,
						closeOnEsc: false,
						dangerMode: true,
						allowEscapeKey: false
					});
	
					return;
				}
	
			}
/* end */			
			
			if(agent_details == 0 || customer_details == 0 || policy_details == 0 || nominee_details == 0){
				swal({
						title: "Alert",
						text:"Please SAVE the values entered so far to proceed with new Section!",
						type: "warning",
						showCancelButton: false,
						confirmButtonText: "Ok!",
						closeOnConfirm: true,
						allowOutsideClick: false,
						closeOnClickOutside: false,
						closeOnEsc: false,
						dangerMode: true,
						allowEscapeKey: false
					} ,
						function () {
						   
						});  
				return false;
			}
			var all_data = $("#payment_details").serialize();
						$.ajax({
						url: "/tele_payment_details_insert",
						type: "POST",
						data: all_data,
						async: false,
						dataType: "json",
						success: function (response) 
						{
							//debugger;
							if(response.disabled == 'Close'){
								$("#sub_isposition").attr("disabled", true);
								$("#disposition").attr("disabled", true);
								
							}
							$("#payment_details_tbody").html('');
							var str = '';
							$.each(response.data, function(index, item) {
								str+= '<tr><td>'+item.Dispositions+'</td><td> '+(item["Sub-dispositions"])+'</td><td>'+item.date+'</td><td>'+item.agent_name+'</td><td>'+((item.remarks) ? item.remarks : '')+'</td></tr>';
						});
						$("#payment_details_tbody").html(str);
							if(proposal_payment_done == 0){
								$('#edit_payment').show();
							}
							
							swal("Success", "Payment Details Saved", "success");
							payment_details_submit = false;
						}
						});
			
		    		
		}
		
	});
		$("#proposal_data").validate({
		ignore: ".ignore",
		focusInvalid: true,
		rules: 
		{
	
			auto_renewal:
			{
				required: true,
				
			},
			
		},
		messages: {

		},
		submitHandler: function (form) 
		{
			 set_session();
			ajaxindicatorstart();
			
			if(!$("#payment_details").valid()){
				ajaxindicatorstop();
				return;
			}
		    if(!$("#emp_agent_data").valid())
		    {
			
				ajaxindicatorstop();
				return;
			}
			 if(!$("#emp_data").valid())
		    {
			
				ajaxindicatorstop();
				return;
			}
			 if(!$("#nominee_data").valid())
		    {
			
				ajaxindicatorstop();
				return;
			}
			
		var check_ghd_blank = get_blank_validation();
		check_ghd_blank = true;
		if(check_ghd_blank == false)
		{
			ajaxindicatorstop();
				swal({
				title: "Alert",
				text: "Please select good health declaration",
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
				return;
		}
		var check_ghd = get_remark_validation();
		if(check_ghd == false)
		{
			return false;
		}
			var mydeclare = $("#mydatas input[type='radio']:checked").val();
			var mydeclare2 = $("#mydatas1 input[type='radio']:checked").val();
			if (mydeclare2 == 'Yes') 
			{
				ajaxindicatorstop();
				swal("Alert","As the Group Health Declaration Question is Yes, the proposal cannot be processed", "warning");
				$("#myModal1").modal("hide");
				$("#sms_body").html('');
				return false;

			}

			if (mydeclare == 'No') 
			{
				ajaxindicatorstop();
				swal("Alert","As the Employee Declaration Question is No, the proposal cannot be processed", "warning");
				$("#myModal1").modal("hide");
				$("#sms_body").html('');
				return false;

			}
			//otp_generate();    
				if(payment_details_submit){
					var all_data = $("#payment_details").serialize();
						$.ajax({
						url: "/tele_payment_details_insert",
						type: "POST",
						data: all_data,
						async: false,
						dataType: "json",
						success: function (response) 
						{
							
						}
						});
				}
			
			
			
				var all_data = $("#emp_agent_data").serialize();
						$.ajax({
						url: "/tele_agent_data_insert",
						type: "POST",
						data: all_data,
						async: false,
						dataType: "json",
						beforeSend: function() {
					        set_session();
					    },
						success: function (response) 
						{
						}
						});
						
						var all_data = $("#emp_data").serialize()+ "&self_edit_clicked="+$("#self_edit_clicked").val()+"&gender1="+$("#gender1").val()+"&dob="+$("#dob1").val()+"&salutation="+$('#salutation').val();
						
						//var all_data = $("#emp_data").serialize();
						$.ajax({
						url: "/tele_emp_data_insert",
						type: "POST",
						data: all_data,
						async: false,
						dataType: "json",
						beforeSend: function() {
					        set_session();
					    },
						success: function (response) 
						{
						}
						});
			
			var all_data = $("#nominee_data").serialize()+"&nominee_relation="+$("#nominee_relation").val()+"&nominee_dob="+$('#nomineedob').val()+"&nominee_salutation="+$("#nominee_salutation").val();
        $.ajax({
            url: "/tele_nominee_data_insert",
            type: "POST",
            data: all_data,
            async: false,
            dataType: "json",
            beforeSend: function() {
					        set_session();
					    },
            success: function(response) {
                
            }
        });
			
		proposal_submit_data();			
		}
		
	});
	function proposal_submit_data()
	{
		  
		var emp_agent_data = $("#emp_agent_data").serializeArray();
		
		var TableData = [];

		var LabelData = []; 
	

		$('#mydatas tr').each(function (row, tr) 
		{
			var LabelData = {};
			
			var label_content;
			var tds = $(tr).find('td:eq(0)').text();


		 
			var label = $(tr).find('.label_id').text();
			var content = $(tr).find('.mycontent').val();

			var mylabel = $(tr).find('.mycontents').val();

			var mylabval = $(tr).find('.mylabval').val();

			if (label != '')
			{
				LabelData = mylabel + ':' + mylabval;
			} 
			else
			{
				LabelData = mylabval;
			}


			TableData[row] = {
				"question": content,
				"format": $("#mydatas input[type='radio']:checked").val(),
				"label": LabelData
			}
		  
		});
		//var form = $("#emp_agent_data").serialize() +'&' +$("#proposal_data").serialize() + "&declares=" +JSON.stringify(TableData);
var remarks = '';
		if($("#plan_name").val() == 'T01'){
			remarks = $(".myremark").val();
		}
		
		var form = $("#proposal_data").serialize() + "&declares=" +JSON.stringify(TableData) + "&product_id=" +$("#plan_name").val() + "&remarks_new=" +remarks;
		set_session();
		$.post("/tele_proposal_validation", form, function (e) 
		{
			var res = JSON.parse(e);
			if (res.status == false)
			{

				 ajaxindicatorstop();
				 swal({
					title: "Alert",
					text: res.message,
					type: "warning",
					showCancelButton: false,
					confirmButtonText: "Ok!",
					closeOnConfirm: true,
					allowOutsideClick: false,
					closeOnClickOutside: false,
					closeOnEsc: false,
					dangerMode: true,
					allowEscapeKey: false
				} ,
					function () {
					   
					}); return;
			}
			if (res.status == true) 
			{
				if($("#plan_name").val() == 'T01'){
					$('#summaryForm').attr('action', "/tele_summary?product_id=T01&leadid="+$('#hidden_lead_id').val()).submit();
				}else{
					$('#summaryForm').attr('action', "/tele_summary?leadid="+$('#hidden_lead_id').val()).submit();
				//$("#summaryForm").submit();
				}
				
			
					
				}
				
				// $.ajax({
				// url: "/tls_payment_url_send",
				// type: "POST",
				// async: false,
				// data: {},
				// success: function(response) {
				// }
				// });

				// ajaxindicatorstop();
				// swal({
						// title: "Success",
						// text: "Proposal Submitted Successfully",
						// type: "success",
						// showCancelButton: false,
						// confirmButtonText: "Ok!",
						// closeOnConfirm: true,
						// allowOutsideClick: false,
						// closeOnClickOutside: false,
						// closeOnEsc: false,
						// dangerMode: true,
						// allowEscapeKey: false
					// },
						// function () {
							// tele_thank_you();
			
					// });
				
			
		});
	}
	$.validator.addMethod("time_validate", function(value, element) { 
	var preferred_contact_time = $("#preferred_contact_time").val();
	if(preferred_contact_time){
    //if (/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/.test(value)) {return true;}else{ return false;}
    var parts = value.split(':');
	if(parts[0] == '' || parts[1] ==''){return false;}
    if (parts[0] > 23 || parts[1] > 59) {return false;}
	  return true;
	
	}
    return true;
}, "Invalid time format.");
	
	$.validator.addMethod
	(

		"valid_mobile",
		function (value, element, param) 
		{
		
			  var re = new RegExp("^[6-9][0-9]{9}$");
			  return this.optional(element) || re.test(value); 
		},
		"Enter a valid 10 digit mobile number and starting from 6 to 9"
    );
	
	$("#pin_code").keyup(function (e) 
	{
		var $th = $(this);
		if (
		  e.keyCode != 46 &&
		  e.keyCode != 8 &&
		  e.keyCode != 37 &&
		  e.keyCode != 38 &&
		  e.keyCode != 39 &&
		  e.keyCode != 40
		)
		{
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
						beforeSend: function() {
					        set_session();
					    },
						success: function (response) 
						{

						   if(response.city!=null && response.state!=null)
						   {
									$("#city").val(response.city);
									$("#state").val(response.state);
						   }


							
						}
				});

	});
	$.validator.addMethod
	(
		"validate_pincode",
		function (value, element, param) 
		{
		   var regs = /^\d{6}$/g;
		   return this.optional(element) || regs.test(value); 
		},
		"Enter a valid Pin Code"
	);
	
	
	
	$("body").on("keyup", ".first_name", function (e) 
	{
        var $th = $(this);
        if (
            e.keyCode != 46 &&
            e.keyCode != 8 &&
            e.keyCode != 37 &&
            e.keyCode != 38 &&
            e.keyCode != 39 &&
            e.keyCode != 40
        ) 
		{
            $th.val(
                $th.val().replace(/[^A-Za-z ]/g, function (str) {
                    return "";
                })
            );
        }
        return;
    });
	/*changed last_name regex*/
		$("body").on("keyup", ".last_name", function (e) 
	{
        var $th = $(this);
        if (
            e.keyCode != 46 &&
            e.keyCode != 8 &&
            e.keyCode != 37 &&
            e.keyCode != 38 &&
            e.keyCode != 39 &&
            e.keyCode != 40
        ) 
		{
            $th.val(   
                $th.val().replace(/[^A-Za-z ]/g, function (str) {
                    return "";
                })
            );
        }
        return;
    });
	

	$('body').on("keyup", "#comAdd2,#comAdd,#comAdd3", function() {
		var $th = $(this);
		 $th.val(
                $th.val().replace(/[{}"\\]/g, function (str) {
                    return "";
                })
            );
	});
	jQuery.validator.addMethod("checkspace_nominee", function(value, element, params) {
	var z = value.trim();
	if(!z){
		return false;
	}
	
	
  return true;
});
	$("#mob_no").keyup(function (e) 
	{
        var $th = $(this);
        if (
            e.keyCode != 46 &&
            e.keyCode != 8 &&
            e.keyCode != 37 &&
            e.keyCode != 38 &&
            e.keyCode != 39 &&
            e.keyCode != 40
        ) 
		{
            $th.val(
                $th.val().replace(/[^0-9]/g, function (str) {
                    return "";
                })
            );
        }
        return;
    });
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
	function salutation_hide_show(selectChange,gender)
	{
		
		if(gender == 'Male')
		{
			selectChange.find('.female_gen'). prop('disabled', true);
			selectChange.find('.female_gen').hide();
			selectChange.find('.male_gen'). prop('disabled', false);
			selectChange.find('.male_gen').show();
		}
		else if(gender == 'Female')
		{
			selectChange.find('.female_gen'). prop('disabled', false);
			selectChange.find('.female_gen').show();
			selectChange.find('.male_gen'). prop('disabled', true);
			selectChange.find('.male_gen').hide();			
		}
	}
    $(document).on("keyup", "#agent_id", function()
	{
		
		$("#tl_id").val('');
		$("#tl_name").val('');
		
		$("#agent_name").val('');
		$("#imd_code").val('');
		
		$("#axis_lob option:selected").removeAttr("selected");

		$("#axis_location option:selected").removeAttr("selected");
		
		$("#axis_vendor option:selected").removeAttr("selected");
		var agent_code = $(this).val();
		 $.ajax({
			url: "/tele_agent_details",
			type: "POST",
			async: false,
			data: {agent_id : agent_code},
			dataType: "json",
			 beforeSend: function() {
		        set_session();
		    },
			success: function (response) 
			{
				if(response !== null && response !== '')
				{
					if(response.tl_name != null){
						var tl_name = response.tl_name.toUpperCase();
					}else{
						var tl_name = '';
					}
					
					$("#tl_id").val(response.tl_emp_id);
					$("#tl_name").val(tl_name);
					$("#tl_id").val(response.tl_emp_id);
					/*$("#am_id").val(response.am_emp_id);
					$("#am_name").val(response.am_name.toUpperCase());					$("#axis_process").html("<option value="+response.axis_process+" seleccted>"+response.axis_process+"</option>");
					$("#axis_process").html("<option value="+response.axis_process+" seleccted>"+response.axis_process+"</option>");
					$("#om_id").val(response.om_emp_id);
					$("#om_name").val(response.om_name.toUpperCase());*/
					$("#agent_name").val(response.base_agent_name.toUpperCase());
					$("#axis_process").html("<option value="+response.axis_process+" seleccted>"+response.axis_process+"</option>");
					$("#imd_code").val(response.imd_code);
					$("#axis_location option:contains("+response.center+")").attr("selected", true);
					$("#axis_location").trigger('change');
					setTimeout(function(){ $("#axis_vendor option:contains("+response.vendor+")").attr("selected", true);
					$("#axis_vendor").trigger('change');}, 2000);
					setTimeout(function(){ $("#axis_lob option:contains("+response.lob+")").attr("selected", true);}, 2000);
					
				}else{
					$("#tl_id").val('');
					$("#tl_name").val('');
					
					$("#agent_name").val('');
					$("#imd_code").val('');
					
					$("#axis_lob option:selected").removeAttr("selected");

					$("#axis_location option:selected").removeAttr("selected");
					
				   $("#axis_vendor option:selected").removeAttr("selected");
				
					
				}
				
				
				
				
			}
		});

	});
	$("#agent_id").bind("keyup keydown change focus blur", function() 
	{
       var agents =  $(this).val();
	      if(agents.length >= 1) 
		  {
			
		
		  }
		  else
		  {
					$("#tl_id").val('');
					$("#tl_name").val('');
					$("#tl_id").val('');
					$("#am_id").val('');
					$("#am_name").val('');
					$("#om_id").val('');
					$("#om_name").val('');
					$("#agent_name").val('');
		  }
    });
	
	$.validator.addMethod(
	"validate_agent",
	function (value, element, param) {

	var count = 1;
	if($("#agent_id").val().length >= 1) {
	var agent_id = $("#agent_id").val();
	$.ajax({
	url: "/tele_agent_details",
	type: "POST",
	async: false,
	data: {agent_id : agent_id},
	dataType: "json",
	success: function (response) {

	if(response != null && response != '' ) {
	count = 0;	
	}else{
	count = 1;
	} 
	}
	});
	}
	else
	{
	count = 1;
	}
	if(count == 1){

	$("#tl_id").val('');
	$("#tl_name").val('');
	$("#tl_id").val('');
	$("#am_id").val('');
	$("#am_name").val('');
	$("#om_id").val('');
	$("#om_name").val('');
	$("#agent_name").val('');
	return false;}else{return true;}
	},
	"Base Agent Id Is not available in master."
	);
 	function ghd_data(e)
	{

		var id = $(e).val();
		var c1 = $('#C1_2:checked').val();
		var c2 = $('#C2_2:checked').val();
		var d1 = $('#D1_2:checked').val();
		var d2 = $('#D2_2:checked').val();
		var d3 = $('#D3_2:checked').val();
		var d4 = $('#D4_2:checked').val();
		if(d1 == 'D1_No' && d2 == 'D2_No' && d3 == 'D3_No' && d4 == 'D4_No')
		{
			 $("#E_data").show();

		 }
	
		
	    if(id == 'B_No')
		{
			$("#B_remark").hide();
			$(".B").hide();
			$(".C1").hide();
			$(".C2").hide();
			$(".D1").hide(); 
			$(".D2").hide(); 
			$(".D3").hide(); 
			$(".D4").hide(); 
			$("#C_data").show();
			$("#D_data").hide();
			$("#E_data").hide();
			$("#C1_remark").hide();
			$("#C2_remark").hide();
			$("#D1_remark").hide();
			$("#D2_remark").hide();
			$("#D3_remark").hide();
			$("#D4_remark").hide();			
			$("#C1_1").prop('checked',false);
			 $("#C1_2").prop('checked',false);
			 
			 $("#C2_1").prop('checked',false);
			 $("#C2_2").prop('checked',false);
			 
			 $("#D1_1").prop('checked',false);
			 $("#D1_2").prop('checked',false);
			 
			 $("#D2_1").prop('checked',false);
			 $("#D2_2").prop('checked',false);
			 
			 $("#D3_1").prop('checked',false);
			 $("#D3_2").prop('checked',false);
			 
			 $("#D4_1").prop('checked',false);
			 $("#D4_2").prop('checked',false);
			 
			 $("#E_1").prop('checked',false);	
			 $("#E_2").prop('checked',false);
		}
	    if(c1 == 'C1_No' && id == 'C2_No' || c2 == 'C2_No' && id == 'C1_No')
		{
			$("#D_data").show();
			$("#D1_remark").hide();
			$("#D2_remark").hide();
			$("#D3_remark").hide();
			$("#D4_remark").hide();
			$("#E_remark").hide();
			$("#E_data").hide();
			$("#E_1").prop('checked',false);	
			$("#E_2").prop('checked',false);
			
		}
	    if(id == 'B_Yes')
		{
			$("#B_remark").show();
			$("#E_remark").hide();
			$("#D1_remark").hide();
			$("#D2_remark").hide();
			$("#D3_remark").hide();
			$("#D4_remark").hide();
			$("#C1_remark").hide();
			$("#C2_remark").hide();
			$(".C1").hide();
			$(".C2").hide();
			$(".D1").hide(); 
			$(".D2").hide(); 
			$(".D3").hide(); 
			$(".D4").hide();
			$(".E1").hide();
			$("#E_data").hide();
			$("#C_data").hide();
			$("#D_data").hide();
			
			 $("#C1_1").prop('checked',false);
			 $("#C1_2").prop('checked',false);
			 
			 $("#C2_1").prop('checked',false);
			 $("#C2_2").prop('checked',false);
			 
			 $("#D1_1").prop('checked',false);
			 $("#D1_2").prop('checked',false);
			 
			 $("#D2_1").prop('checked',false);
			 $("#D2_2").prop('checked',false);
			 
			 $("#D3_1").prop('checked',false);
			 $("#D3_2").prop('checked',false);
			 
			 $("#D4_1").prop('checked',false);
			 $("#D4_2").prop('checked',false);
			 
			 $("#E_1").prop('checked',false);	
			 $("#E_2").prop('checked',false);
		}
		if(d1 != 'D1_No' || d2 != 'D2_No' || d3 != 'D3_No' && d4 != 'D4_No')
		{
			$("#E_data").hide();
			$(".E1").hide();
			$("#E_remark").hide();
			
			$("#E_1").prop('checked',false);	
			$("#E_2").prop('checked',false);
		}
		if (id == 'E_Yes')
		{
			$("#E_remark").show();
		}
		
		if(id == 'E_No')
		{
			$("#E_remark").hide();
			$(".E").hide();
		}
		if (id == 'D1_Yes')
		{
			$("#D1_remark").show();
			$("#E_data").hide();
		}
		if (id == 'D1_No')
		{
			$("#D1_remark").hide();
			$(".D1").hide();
		}
		if (id == 'D2_Yes')
		{
			$("#D2_remark").show();
			$("#E_data").hide();
		}
		if (id == 'D2_No')
		{
			$("#D2_remark").hide();
			$(".D2").hide();
		}
		if (id == 'D3_Yes')
		{
			$("#D3_remark").show();
			$("#E_data").hide();
		
		}
		if (id == 'D3_No')
		{
			$("#D3_remark").hide();
			$(".D3").hide();
		}
		if (id == 'D4_Yes')
		{
			$("#D4_remark").show();
			$("#E_data").hide();
		
		}
		if (id == 'D4_No')
		{
			$("#D4_remark").hide();
			$(".D4").hide();
		}
		if(id == 'C2_No')
		{
			$("#C2_remark").hide();
			$(".C2").hide();
		}
		if(id == 'C1_No')
		{
			$("#C1_remark").hide();
			$(".C1").hide();
		}
		if(id == 'C1_Yes')
		{
			 $("#C1_remark").show();
			 $("#D_data").hide();
			 $("#E_data").hide();
			 $("#E_remark").hide();
			$("#D1_remark").hide();
			$("#D2_remark").hide();
			$("#D3_remark").hide();
			$("#D4_remark").hide();

			$(".D1").hide(); 
			$(".D2").hide(); 
			$(".D3").hide(); 
			$(".D4").hide();
			$(".E1").hide();
			 $("#D1_1").prop('checked',false);
			 $("#D1_2").prop('checked',false);
			 
			 $("#D2_1").prop('checked',false);
			 $("#D2_2").prop('checked',false);
			 
			 $("#D3_1").prop('checked',false);
			 $("#D3_2").prop('checked',false);
			 
			 $("#D4_1").prop('checked',false);
			 $("#D4_2").prop('checked',false);
			 
			 $("#E_1").prop('checked',false);	
			 $("#E_2").prop('checked',false);
		}
		if(id == 'C2_Yes')
		{ 
			 $("#C2_remark").show();
			 $("#D_data").hide();
			 $("#E_data").hide();
			 $("#E_remark").hide();
			$("#D1_remark").hide();
			$("#D2_remark").hide();
			$("#D3_remark").hide();
			$("#D4_remark").hide();

			$(".D1").hide(); 
			$(".D2").hide(); 
			$(".D3").hide(); 
			$(".D4").hide();
			$(".E1").hide();
		     $("#D1_1").prop('checked',false);
			 $("#D1_2").prop('checked',false);
			 
			 $("#D2_1").prop('checked',false);
			 $("#D2_2").prop('checked',false);
			 
			 $("#D3_1").prop('checked',false);
			 $("#D3_2").prop('checked',false);
			 
			 $("#D4_1").prop('checked',false);
			 $("#D4_2").prop('checked',false);
			 
			 $("#E_1").prop('checked',false);	
			 $("#E_2").prop('checked',false);		 
			 
		}
	
	}


	function get_sub_disposition(data){

		var sub_disposition = $('option:selected', data).text().replace(/ /g,"_");		
		$("#sub_isposition").prop("disabled", false);$("#sub_isposition").val('');
		$('[name=show_hide_sub_dispositions]').hide();		
		$("."+sub_disposition).show();

/* updated on 09-02-2021 by Akash_Chawan */					
		if(sub_disposition=='Payment_pending'||sub_disposition=='Payment_done'){
			proposal_create_check();
		}else{
			$('#hidden_agent_section').val('1');
			$('#hidden_customer_section').val('1');
			$('#hidden_nominee_section').val('1');
			$('#hidden_payment_section').val('1');
			$('#hidden_policy_section').val('1');
			$('#hidden_payment_section').val('1');			
			
		}
/* end */
	}

function proposal_create_check()
{
	/*already Proposal Created data show*/
	$.ajax({
	url: "/tele_proposal_created",
	type: "POST",
	async: false,
	dataType: "json",
	success: function (response) 
	{
//debugger;
	if(response.proposal_status == 'Yes'){
		$("#emp_agent_data").css('pointer-events','none');
		$("#firstname").attr("readonly", false); 
		$("#lastname").attr("readonly", false); 
		$("#plan_name").css('pointer-events','none');
		$("#sum_insures").css('pointer-events','none');
		$("#occupation").css('pointer-events','none');
		$("#patFamilyConstruct").css('pointer-events','none');
		$("#annual_income").attr("readonly", true); 
		$('input[name=GCI_optional]').attr("disabled",true);
		$(".del_btn_member").hide();
		proposal_created = 1;
		
	}else{
		$("#emp_agent_data").css('pointer-events','auto');
		$("#firstname").attr("readonly", true); 
		$("#lastname").attr("readonly", true); 
		$("#plan_name").css('pointer-events','auto');
		$("#sum_insures").css('pointer-events','auto');
		$("#occupation").css('pointer-events','auto');
		$("#patFamilyConstruct").css('pointer-events','auto');
		$("#annual_income").attr("readonly", false); 
		$('input[name=GCI_optional]').attr("disabled",false);
		$(".hide_proposal").show();
		proposal_created = 0;
	}

	if(response.status == 'Yes')
	{
		proposal_payment_done = 1;
		$("#edit_btn_custsection").hide();
		$("#edit_nominee").hide();
		$("#edit_payment").hide();
		$("#patFormSubmit").hide();
		$(".hide_proposal").hide();
		$("#av_remark").val(response.audit_data.remarks);
		$(".del_p").hide();
		$("#agent_detail").hide();
		$("#update_emp").hide();
		$("#submit_nominee").hide();
		$("#payment_submit").hide();
		$("#disposition option:contains('Payment pending')").attr("selected", true);
		$("#disposition").trigger('change'); 
		$("#sub_isposition").val(45);
		$("#sub_isposition").trigger('change');
		$("#av_remark").addClass('ignore');
		//$('#edit_payment').show();
		payment_details_submit = false;
		
		
	
		/*nominee data*/
	
		

	agent_cust_prefilled(response);
		/*axis data*/
		// var emp = response.base_agent_details;	
		// var axis_det = response.axis_details;
	    // $('#axis_lob').html('<option value ="'+emp.axis_lob+'" selected>'+axis_det.axis_lob+'</option>');
		
		// $('#axis_vendor').html('<option value ="'+emp.axis_vendor+'" selected>'+axis_det.axis_vendor+'</option>');
		// /*employee data*/
			
		// $('#axis_location').val(emp.axis_location);
		// $('#agent_id').val(emp.base_agent_id);
		// $('#agent_name').val(emp.base_agent_name);
		// $("#comAdd2").val(emp.comm_address);
		// $("#dobdate").val(emp.preferred_contact_date);
		// $("#preferred_contact_time").val(emp.preferred_contact_time);
		// $("#av_remark").val(emp.av_remark);
		// $("#comAdd3").val(emp.comm_address1);
		// $("#mobile_no2").val(emp.emg_cno);
		// $('#tl_id').val(emp.tl_emp_id);
		// $('#tl_name').val(emp.tl_name);
		// $('#om_name').val(emp.om_name);
		// $('#om_id').val(emp.om_emp_id);
		// $('#am_id').val(emp.am_emp_id);
		// $('#am_name').val(emp.am_name);
		$('#confr1').hide();  
	}
	else
	{
		proposal_payment_done = 0;
		$("#edit_btn_custsection").show();
		// $("#edit_nominee").show();
		// $("#edit_payment").show();
		$("#av_remark").val('');
		$(".hide_proposal").show();
		if(response.proposal_status == 'Yes'){
			$(".del_btn_member").hide();
		}
	agent_cust_prefilled(response);
	$("#patFormSubmit").show();
	$(".del_p").show();
	$('#confr1').show();
	$("#agent_detail").show();
	$("#submit_nominee").show();
	$("#payment_submit").show();
	$("#av_remark").removeClass('ignore');
	}

	if(response.emp_details.GCI_optional == 'No'){
		$('#GCI_no').prop('checked', true);
	}else{
		$('#GCI_yes').prop('checked', true);
	}

	}
	});
	var boxlen = $('.commonCls').length;
	if(boxlen > 0){
		$("#plan_name").attr('disabled', 'disabled');
		if($('#plan_name').val() == 'T03'){
							 	$("#sum_insures").attr("disabled", "disabled");
							  	$("#patFamilyConstruct").attr("disabled", "disabled");
							 }
	}
	if($("#plan_name").val() == 'T01'){
					
		
		$('.GCIOptionalDiv').show();
	}else{
		$('.GCIOptionalDiv').hide();
	}


}

function agent_cust_prefilled(response)
{
	// console.log(response);

	var emp = response.base_agent_details;	
	var axis_det = response.axis_details;
	if(axis_det!= null && emp!= null)
	{
    /*$('#axis_lob').html('<option value ="'+emp.axis_lob+'" selected>'+axis_det.axis_lob+'</option>');*/
    $('#axis_lob').val(emp.axis_lob);
	
	$('#axis_vendor').html('<option value ="'+emp.axis_vendor+'" selected>'+axis_det.axis_vendor+'</option>');
	/*employee data*/
	if(emp.new_remarks != 0){$(".myremark").val(emp.new_remarks);}else{$(".myremark").val('');}
		
	$('#axis_location').val(emp.axis_location);
	$('#agent_id').val(emp.base_agent_id);
	$('#agent_name').val(emp.base_agent_name.toUpperCase());
	$("#axis_process").html("<option value="+emp.axis_process+" seleccted>"+emp.axis_process+"</option>");
	$("#comAdd2").val(emp.comm_address);
	$("#dobdate").val(emp.preferred_contact_date);
	$("#preferred_contact_time").val(emp.preferred_contact_time);
	$("#av_remark").val(emp.av_remark);
	$("#comAdd3").val(emp.comm_address1);
	$("#mobile_no2").val(emp.emg_cno);
	$('#tl_id').val(emp.tl_emp_id);
	$('#tl_name').val(emp.tl_name.toUpperCase());
	if(emp.base_agent_id != ''){
		$("#hidden_agent_section").val(1);
	}
	
	/*$('#om_name').val(emp.om_name.toUpperCase());
	$('#om_id').val(emp.om_emp_id);
	$('#am_id').val(emp.am_emp_id);
	$('#am_name').val(emp.am_name.toUpperCase());*/
	$('#imd_code').val(emp.imd_code);
	}
	var nominee = response.nominee_data;
	if(nominee != null){
		$('#nominee_relation').val(nominee.fr_id);
		$('#nominee_fname').val(nominee.nominee_fname.toUpperCase());
		$('#nominee_lname').val(nominee.nominee_lname.toUpperCase());
		$('#nominee_gender').val(nominee.nominee_gender);
		$('#nominee_contact').val(nominee.nominee_contact);
		$('#nominee_email').val(nominee.nominee_email);
		$('#nomineedob').val(nominee.nominee_dob);
		$('#nominee_salutation').val(nominee.nominee_salutation);
		$("#hidden_nominee_section").val(1);
		if(proposal_payment_done == 0){
			$('#edit_nominee').show();
		}
		
		$('#nominee_relation').attr('disabled',true);
		$('#nominee_fname').attr('readonly',true);
		$('#nominee_lname').attr('readonly',true);
		$('#nominee_salutation').attr('disabled',true);
		$('#nominee_gender').attr('readonly',true);
		$('#nomineedob').attr('readonly',true);
		$('#nomineedob').attr('disabled',true);
		$('#nominee_contact').attr('readonly',true);
		$('#nominee_email').attr('readonly',true);
	}

		/*proposer level good health declarartion*/
	var ghd = response.ghd_proposer;
	var len = ghd.length;
	var i;
	for (i = 0; i < len; i++)
	{
		var member = ghd[i];
		if(member.type == 'C1')
		{
			$("#C_data").show();
		}
		if(member.type == 'D1')
		{
			$("#D_data").show();
		}
		if(member.type != '')
		{
			$("#"+member.type+"_data").show();
			if(member.format == 'Yes')
			{
			$("#" + member.type+"_1").prop("checked", true);
			$("#"+member.type+"_remark").show();
			$("#"+member.type+"_remark").val(member.remark);
		
			}
			else
			{
				$("#" + member.type+"_2").prop("checked", true);
				$("#"+member.remark+"_remark").css('display','none');
			}
		}
			
		
	}
	var declare_data = response.emp_declare;
	var lens = declare_data.length;
	for (i = 0; i < lens; i++)
	{
	var declare = declare_data[i];
	//console.log(declare);
	$('input:radio[name="' + declare.p_declare_id + '"][value="' + declare.format + '"]').attr('checked', true);
	}
}

$("#nominee_contact").keyup(function (){
	
		if ($(this).val().match(/[^0-9]/g)) 
		{
			$(this).val($(this).val().replace(/[^0-9]/g, ""));
		}
});

function annual_income_hide_show(sum_insured)
{
		
	var sumInsuresValue = $("#sum_insures :selected").val();
	var isSumAssuredMoreThan = 1000000;
	if(sumInsuresValue > isSumAssuredMoreThan) 
	{
		$("#annual_income_label").show();
		$("#occupation_label").show();
		//$("#annual_income").removeClass('ignore');		
		$("#occupation").removeClass('ignore');	
	}
	else
	{
		$("#annual_income_label").hide();
		$("#occupation_label").hide();
		$("#annual_income").addClass('ignore');
		$("#occupation").addClass('ignore');
		//$("#occupation").hide();
	}
	/*if(annual_income_data != '' && $("#patForm").find("table tbody").children().length != 0)
	{
		//$("#annual_income").attr('readonly','readonly');
	}
	else
	{
		$("#annual_income").val('');
	}*/
	
}
	
	function apply_button() {
		//console.log("show poup => "+show_popup);
		if(show_popup == 0){
			//if(parent_id == 'test123'){
			var edit = $("#patFamilyConstruct").closest("form").find("input[name='edit']").val();
			/*if(edit !=0)
			{
				return;
			}*/
			//if($("#patTable tr").length > 0) {
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
			{sum_annual = annual_income_logic();if(sum_annual == false){return;} }else{
				/*swal("Alert", "please select annual Income"); */ return;
				}}else{annual_income_hide_show();}
			/*if(patFamilyConstruct == '' )
			{		
				swal("Alert", "Please select Family Construct");
				return;
			}*/
			//insert_annual_income(emp_id,annualIncome,'insert');
			$.ajax({
			url: "tele_apply_changes",
			type: "POST",
			data: {
				'product_id':$('#plan_name').val(),
				'family_construct':$('select[name=familyConstruct]').val(),
				'sum_insured':$('select[name=sum_insure]').val(),
				'gci' : $('input[name=GCI_optional]:checked').val(),
			
			},
			async: false,
			dataType: "json",
			success: function (response) {
			
			// alert(apply_onload);
			if(apply_onload > 1)
			{
				if(response.message){
				swal("Alert",response.message);
				}
			}
			else
			{apply_onload++;}

		//addDependentForm("getTable",response);
			
			
			}
			});	
				//}
			//}
		}
	}

		function annual_income_logic()
		{
			//Annual Income Code
			var dropdown_sumIsured;
			var annual_income = $("#annual_income").val();
			var sumInsuresValue = $("#sum_insures :selected").val();
			var ComparesumInsures = $("#sum_insures").val();
			var IncomeSum = annual_income * 8;
			var sumInsuresValues = (sumInsuresValue/8);
			var count = 0;
			//annual income logic for helathpro
			var plan_name = $("#plan_name").val();
			if(plan_name == 'T03'){
				var RiskClass = $("#occupation").find(':selected').data('class');
				if(RiskClass == "RS001"){
					var IncomeSum = annual_income * 10;
					var sumInsuresValues = (sumInsuresValue/10);
				}else{
					var IncomeSum = annual_income * 8;
					var sumInsuresValues = (sumInsuresValue/8);
				}
			}
			if(sumInsuresValue > 1000000)
			{
				if(annual_income == '' && annual_income == 0)
				{
					swal("Alert", "Annual Income Cannot Be blank","warning");
					count = 1;
					return false;
				}
				//code change
				$("#sum_insures option").filter(function(e1){
					dropdown_sumIsured = $(this).val();
					
					if(IncomeSum < sumInsuresValue ){
					
						swal("Alert", "Request to select SI lesser than opted","warning");
						count = 1;
						$('#annual_income').focus();
						return false;
						$("#premiumModalBody").html('');			
					}
				});
			}
			//alert(count);
			if(count == 1)
			{
				return false;
			}
			else
			{
				insert_annual_income('',annual_income,true);
				return true;
			}
			

		}

		function insert_annual_income(emp_id,annual_income,insert){
			$.ajax({
						url: "/tele_update_annual_income",
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

