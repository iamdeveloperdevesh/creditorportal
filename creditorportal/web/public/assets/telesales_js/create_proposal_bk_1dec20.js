    $(document).ready(function () 
    {
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
			$('.bd-example-modal-md').removeClass('hide');
			var selectChange = "";
			var emp_id = $("#empIdHidden").val() || "";
			var parent_ids = $("#parentIdHidden").val();
			
			
			 $.ajax({
						url: "/tele_master_salutation",
						type: "POST",
						async: false,
						dataType: "json",
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

							try {
							$("#addCard").val(response.adhar);
							}catch(e) {

							}
							$("#lead_id_product").html(response.lead_id);
							$("#lead_id").val(response.lead_id);
							$("#comAdd").val(response.address);
							
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
					
			 $.ajax({
						url: "/tele_policy_data",
						type: "POST",
						data: 
						{},
						async: false,
						dataType: "json",
						success: function (response) 
						{
							
								$("#benifit_4s").css("display", "block");
								var i;
								for (i = 0; i < response.length; i++) 
								{

										$("#pr_name").html(response[i].product_name);
										$("#plan_name").val(response[i].product_name);
										$("#master_policy_number").val(response[i].master_policy_no);
										if (response[i].policy_sub_type_id == 1) 
										{

											$(".personal_accident").text(response[i].policy_sub_type_name);
											$("#benifit_4s").css("display", "block");

											$("#subtype_text").val(response[i].policy_sub_type_id);

											if ((response[i].max_adult > 1 && response[i].fr_id == 1) ||response[i].fr_id == 0)
											{
													$(".family_members_id1").append('<option data-opt="' +response[i].gender_option +'" value="'+response[i].fr_id +'">' +response[i].fr_name +"</option>");
										    } 
											else if (response[i].max_adult == 2 &&response[i].max_child == 0 &&(response[i].fr_id == 4 ||response[i].fr_id == 5 ||response[i].fr_id == 6 ||response[i].fr_id == 7)) 
											{
													$(".family_members_id1").append('<option data-opt="' +response[i].gender_option +'" value="' +response[i].fr_id +'">' +response[i].fr_name +"</option>");
											} 
											else if (response[i].max_adult == 4 &&response[i].max_child == 0 &&response[i].fr_id == 5 ||response[i].fr_id == 6 ||response[i].fr_id == 7)
											{
													$(".family_members_id1").append('<option data-opt="' +response[i].gender_option +'" value="' +response[i].fr_id +'">' +response[i].fr_name +"</option>");
											} 
											else if(response[i].max_adult > 2 &&(response[i].fr_id == 4 ||response[i].fr_id == 5 ||response[i].fr_id == 6 ||response[i].fr_id == 7 ||response[i].fr_id == 1 ||response[i].fr_id == 0))
											{
													$(".family_members_id1").append('<option data-opt="' +response[i].gender_option +'" value="' +response[i].fr_id +'">' +response[i].fr_name +"</option>");
											}
											else if (response[i].fr_id == 2 || response[i].fr_id == 3)
											{
													$(".family_members_id1").append('<option data-opt="' +response[i].gender_option +'" value="' +response[i].fr_id +'">' +response[i].fr_name +"</option>");
										    }
										}
								}
						}
									   
										
				    });
					  $.ajax({
			
						url: "/tele_get_declaration",
						type: "POST",
						data: {},
						
						success: function (data) 
						{

								if(data!='')
								{
									$('#health_declare').show();
									$('#policy_declare').html(data);
						  
								}

						}
					});	
			 $.ajax({
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
					});
				  
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
				 
				});


			}
    });
	
	
	function enable_disable_proposal(data){
		if($(data).val() == 45){
			$("#show_hide_proposal").show();
		}else{
			$("#show_hide_proposal").hide();
		}
		if($(data).val() == 30 ||  $(data).val() == 31 ){
			$('#dobdate').removeClass('ignore');
			$('#preferred_contact_time').removeClass('ignore');
		}else{
			$('#dobdate').addClass('ignore');
			$('#preferred_contact_time').addClass('ignore');
		}
	}
	function getRelation(){	
	var fam_rel = $('select[name=familyConstruct]').val().split("+");
	var opt = $(".family_members_id1 option");
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
	
    function getPremium(selectChange) 
	{
		$.post("/tele_get_premium", { "sum_insured":  selectChange.find('select[name=sum_insure]').val(), "policy_detail_id":  selectChange.find('select[name=sum_insure] :selected').attr("data-policyno"), "family_construct":  selectChange.find('select[name=familyConstruct]').val() }, function (e)
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

			 $("#premium").val(premium.toFixed(2));
		});
	}
	function getPremiumByAge(id) 
	{
			var familyConstruct = id.val();
			$("#getMaxPremiumAgeLabel").html('');
			if(familyConstruct == '1A')
			{

			$("#getMaxPremiumAgeLabel").html('Enter Member Age');
			}
			else		 
			{
			$("#getMaxPremiumAgeLabel").html('Enter Eldest Member Age');
			}
			$("#premiumBif").css("pointer-events","none");
			$(".ti-eye").hide();
			var edit = id.closest("form").find("input[name='edit']").val();
			$("#getMaxPremiumAge").val("");

			if($("#refreshEdit").val() == "0") {

			$("#refreshEdit").val("1");
			return;
			}

			if(edit == 0 || edit == "")
			$("#getMaxPremiumAgeModal").modal();
        
        
    }
	
	function changes(e) 
	{
			selectChange = $(e).closest(".row");
			set_non_editable(selectChange,1);
			$.ajax
			({
				url: "/tele_family_details",
				type: "POST",
				data: {relation_id: $(e).val()},
				dataType: "json",
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
											selectChange.find("select[name='family_salutation[]']").val("Mr");
											selectChange.find("select[name='family_salutation[]']").prop('disabled',true);

										}
										else if($("#gender1").val() == 'Female')
										{
											selectChange.find('[name="family_gender[]"]').val("Male");
											selectChange.find("select[name='family_salutation[]']").val("Mrs");
											selectChange.find("select[name='family_salutation[]']").prop('disabled',true);
										}
										else
										{
											selectChange.find('[name="family_gender[]"]').val(" ");
											selectChange.find("[name='family_salutation[]']").val("");
											selectChange.find("select[name='family_salutation[]']").prop('disabled',false);
										}
										
										selectChange.find("input[name='first_name[]']").val(family_detail[i].policy_member_first_name.toUpperCase());
										selectChange.find("input[name='middle_name[]']").val(family_detail[i].policy_member_middle_name.toUpperCase());
										selectChange.find("input[name='last_name[]']").val(family_detail[i].policy_member_last_name.toUpperCase());
										selectChange.find("#family_id").val(family_detail[i].policy_member_last_name);
										selectChange.find("input[type='text'][name='family_date_birth[]']").val(family_detail[i].policy_mem_dob);
										selectChange.find("input[name='marriage_date']").val(family_detail[i].marriage_date);
										selectChange.find("input[type='text'][name='age[]']").val(family_detail[i].age);
										selectChange.find("input[name='family_gender[]']").val(family_detail[i].policy_mem_gender)
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
		}
	}
	
	$(".dobdate").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
		
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
	'last_name[]': {
	required: true
	},
	'family_date_birth[]': {
	required: true
	},
	'age[]': {
	required: true
	},

	'age_type[]': {
	required: true
	},


	},

	messages: {

	},
	submitHandler: function (form) {
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
		var plan_name = $("#pr_name").html();
		var premium_pat = $('#patFamilyConstruct option:selected').data('premium');
		selectChange.find("select[name='family_salutation[]']").removeAttr('disabled', 'disabled');
		var familyDataType = $("#sum_insures :selected").attr("data-type");
		$("#patFamilyConstruct").removeAttr('disabled', 'disabled');
		$("#sum_insures").removeAttr('disabled', 'disabled');
		$(".family_members_id1").prop('disabled',false);
		var family_members_id = $(".family_members_id1").val();
		var form = $("#patForm").serialize() + "&premium=" + premium_pat + "&policyNo=" + $("#sum_insures :selected").attr("data-policyno") + "&familyDataType=" + familyDataType + "&declare=" + JSON.stringify(TableData2)+ "&chronic=" + JSON.stringify(TableData3);
		var master_policy = $("#master_policy_number").val();
		$.post("/tele_family_details_insert", form, function (e)
		{
		$('[name="family_date_birth[]"]').each(function(){
         //code
			$(this).prop("disabled", true);
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
					$("#sum_insures").attr('disabled', 'disabled');
				$("#patFamilyConstruct").attr('disabled', 'disabled');
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
				$("#sum_insures").attr('disabled', 'disabled');
				$("#patFamilyConstruct").attr('disabled', 'disabled');
				$("#premium").attr('disabled', 'disabled');

				var sum_insures = $("#sum_insures").val();
				var patFamilyConstruct = $("#patFamilyConstruct").val();
				var premium = $("#premium").val();
				
				document.getElementById("patForm").reset();
				$("#master_policy_number").val(master_policy);
				$("#sum_insures").val(sum_insures);
				$("#patFamilyConstruct").val(patFamilyConstruct);
				$("#premium").val(premium);
				$("#plan_name").val(plan_name);

				$("#patFormSubmit").hide();
				
				addDependentForm("patTable", JSON.parse(e));
		
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
            $.post("/get_premium_age", { "policyNo": policyNo, "sum_insures": sum_insures, "familyConstruct": familyConstruct, "maxPremiumAge": maxPremiumAge }, function (e) {
                e = JSON.parse(e);
                $("#premium").val("");
                
                if (e.status) 
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
		var family_construct = $('#patFamilyConstruct option:selected').val();
		var result = family_construct.split('+');
		
		var add_length = [];
		add_length.push(parseInt(result[0]));
		add_length.push(parseInt(result[1]));

		if(!$('#patFamilyConstruct').is('[disabled=disabled]')){
		
		$("#add_more").empty();
		
		var i;
		for (i = 0; i < sum(add_length); i++) {
			common_append(i);
		}
		
		$("#add_btn_view").show();
				
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
		var addDependent = null;
		
		$("#add_more").empty();
		var i = 0;
		
		elem.data.forEach(function (e) 
		{
			if (e.message) 
			{
				addDependent = {
					"message": e.message,
					"premium": e.new_premium,
				}
			}
			
			common_append(i,e);
			
			var family_members_id = $("#family_members_id"+i)
			var family_salutation = $("#family_salutation"+i)
			var family_gender = $("#family_gender"+i)
			var first_name = $("#first_name"+i)
			var last_name = $("#last_name"+i)
			var family_date_birth = $("#family_date_birth"+i)
			var age = $("#age"+i)
			var age_type = $("#age_type"+i)

			family_members_id.val(e.fr_id);
			family_members_id.css("pointer-events",'none');
			family_gender.val(e.gender);
			first_name.val(e.firstname.toUpperCase()).prop('disabled',true);
			last_name.val(e.lastname.toUpperCase()).prop('disabled',true);
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
			declarepopoulate(e.emp_id, e.policy_member_id,i);
			
			
			if(e.fr_id != '0'){
				$("#edit_btn"+i).show();
			}
			
			$("#delete_btn"+i).show();
			
			$(".disease"+i).css("pointer-events","none");
		
			i++;
		  
        });
			
		var family_construct = $('#patFamilyConstruct option:selected').val();
		if(family_construct != ''){
			var result = family_construct.split('+');
		
			var add_length = [];
			add_length.push(parseInt(result[0]));
			add_length.push(parseInt(result[1]));
			
			if(sum(add_length) > elem.data.length){
				var check_count = (sum(add_length) - elem.data.length);
				var i;
				for (i = 0; i < check_count; i++) {
					common_append(elem.data.length);
				}
			}
			
			$("#add_btn_view").show();

			if(sum(add_length) == elem.data.length){
				$("#patFormSubmit").hide();
			}

			if(elem.data.length == 0) 
			{
				$("#add_more").empty();
				$("#add_btn_view").hide();
				$("#sum_insures").val('');
				$("#patFamilyConstruct").val('');
				$("select[name=familyConstruct]").html('<option value="" selected>Select</option>');

				$("#premium").val('');
				$("#sum_insures").removeAttr('disabled', 'disabled');
				$("#patFamilyConstruct").prop('disabled', false);
				
			}
			
		}
    
        if (addDependent && addDependent.message && addDependent.premium)
		{
            swal("Alert", addDependent.message, "warning");
            $("#premium").val(addDependent.premium)
        }

      
	}
	
	function common_append(j,e =''){
		var add_div_new = '<div class="row mt-2 mb-2"><div class="col-md-10">Member '+(j+1)+'</div><div class="col-md-2 text-center"><button class="btn sub-btn" style="margin-right: 4px; background: #FB8C00 !important;border: none;padding: 5px 15px; display:none;" id ="edit_btn'+j+'" type="button" onclick="editMember('+j+')">Edit</button><button class="btn sub-btn" style="background: #E53935 !important;border: none;padding: 5px 15px; display:none;" id ="delete_btn'+j+'" type="button" data-emp-id=' + e.emp_id + ' data-policy-member-id=' + e.policy_member_id + ' onclick="deleteMember('+j+')">Delete</button></div></div>';
		
		var add_div = '<div class="col-md-3"><div class="form-group"><label class="col-form-label">Relation With Proposer<span style="color:#FF0000">*</span></label><select class="form-control family_members_id1" id ="family_members_id'+j+'" name="family_members_id[]" onchange="changes(this);"><option value="" >Select</option></select></div></div><div class="col-md-1"><div class="form-group"><label class="col-form-label">Salutation<span style="color:#FF0000">*</span></label><select class="form-control family_salutation" id ="family_salutation'+j+'" name="family_salutation[]" style="padding: 5px 0px; font-size: 13px !important;"><option value="" >Select</option><option value="Mr" class="male_gen">Mr</option><option value="Mrs" class="female_gen">Mrs</option><option value="Ms" class="female_gen">Ms</option><option value="Master" class="male_gen">Master</option></select></div></div><div class="col-md-2"><div class="form-group"><label class="col-form-label">Gender<span style="color:#FF0000">*</span></label><input class="form-control family_gender dis-col" type="text" id ="family_gender'+j+'" name="family_gender[]" id="family_gender1" readonly><p class="p-gender">Auto selected basis salutation value opted.</p></div></div><div class="col-md-3"><div class="form-group"><label for="example-text-input" class="col-form-label">First Name <span style="color:#FF0000">*</span></label><input class="form-control first_name sahil" type="text" value="" id ="first_name'+j+'" name="first_name[]" autocomplete="off" maxlength = "50" ></div></div><div class="col-md-3"><div class="form-group"><label for="example-text-input" class="col-form-label">Last Name <span style="color:#FF0000">*</span></label><input class="form-control last_name" type="text" value="" id ="last_name'+j+'" name="last_name[]" maxlength = "50" autocomplete="off" ><span id="err_last_nameArr" class="error"></span></div></div><div class="col-md-3"><div class="form-group"><label for="example-date-input" class="col-form-label">Date of Birth<span style="color:#FF0000">*</span></label><input class="form-control family_date_birth" autocomplete="off" type="text" id ="family_date_birth'+j+'" name="family_date_birth[]" readonly="readonly"><span id="err_family_date_birthArr" class="error"></span></div></div><div class="col-md-3" style="display: block" ><div class="form-group"><label for="example-text-input" class="col-form-label">Age<span style="color:#FF0000">*</span></label><input class="form-control dis-col age1" type="text" id ="age'+j+'" name="age[]" readonly=""></div></div><div class="col-md-3" style="display: none"><div class="form-group"><label for="example-text-input" class="col-form-label">Age Type (Year(s)/Day(s)) <span style="color:#FF0000">*</span></label><input type="text" class="form-control age_type1" id ="age_type'+j+'" name="age_type[]" readonly=""></div></div><div class="col-md-12 disease'+j+'" style="display: block"><div class="member_declare_benifit_4s'+j+' col-md-12"></div><div class="tt quest_declare_benifit_4s'+j+' col-md-12"></div><input type="hidden" name="edit_member_id[]" value=' + e.policy_member_id + '></div>';
			 
			var div = $("<div />");	
			var div_new = $("<div />");	
			div_new.html("<div>"+add_div_new);
			div.html("<div class='col-md-12 bor-rr row mb-2'>"+add_div);
			
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

								$("#pr_name").html(response[i].product_name);
								$("#plan_name").val(response[i].product_name);
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
			
			if (sumInsuresType == "family_construct") 
			{
			
				getPremium(selectChange);
				getRelation();
			
			} else if (sumInsuresType == "family_construct_age") 
			{
				getPremiumByAge(family_construct);
			
			}
		
		
			$.ajax({
							url: "/tele_member_declare_data",
							type: "POST",
							dataType: "html",
							data: {member_id:j},
							success: function (response)
							{
								$(".member_declare_benifit_4s"+j).html(response)
							 
							}
						});
					
					
			  
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
				success: function (response) 
				{
					salutation_set = response.family_data[0].salutation;
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
		$("#first_name"+i).prop('disabled',false);
		$("#last_name"+i).prop('disabled',false);
		$("#family_date_birth"+i).prop('disabled',false);
		//$("#edit_btn"+i).prop('disabled', true); 
		$("#edit_btn"+i).hide(); 
		$("#patFormSubmit").show();
		$(".disease"+i).css("pointer-events","auto");
		
		$("#first_name"+i).focus();
	}
	
	
	function  deleteMember(i) 
	{
		$.post("/tele_delete_member", {
		"emp_id": $("#delete_btn"+i).attr("data-emp-id"),
        "policy_member_id": $("#delete_btn"+i).attr("data-policy-member-id"),
		}, function (e) 
		{
        
			if (!e) 
			{
				
				$("#delete_btn"+i).closest("form").find("select[name='family_members_id[]']").css("pointer-events",'auto')
				swal("Success", "Member Deleted Successfully", "success");
				
				$(".quest_declare_benifit_4s").empty();
				$('#chronic th input[type="checkbox"]').prop('checked',false);
				
				get_all_data();
				$("#patFormSubmit").show();
				
				//siddhi
				//$("#patFamilyConstruct").change();
				
			}
		});
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


	function setFamilyConstruct(value) 
	{
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
								$("#patFamilyConstruct").change();
							}
						}
					}
			});
	
        }
        else 
		{
            $("#patFamilyConstructDiv").hide();
        }
        
	}

	
	function setMasPolicy(type, e1)
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

	}	
			
	function get_all_data()
	{	  

	  $.ajax({
					url: "/tele_get_all_data",
					type: "POST",
					dataType: "json",
					data: {},
					success: function (response) 
					{

					  
						$("#sum_insures").attr("disabled", "disabled");
						$("#patFamilyConstruct").attr("disabled", "disabled");
						$("#premium1").attr("disabled", "disabled");
						var patFamilyConstruct = $("#patFamilyConstruct").val();
						var sum_insures = $("#sum_insures").val();
						if(sum_insures == '')
						{
							  $("#sum_insures").removeAttr("disabled", "disabled");
							  $("#patFamilyConstruct").removeAttr("disabled", "disabled");;
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
								if (response.data[i]['policy_sub_type_id'] == 3 ) 
								{
														
									$("#sum_insures1").val(response.data[i]['policy_mem_sum_insured']);
									$('#premium1').val(response.data[i]['policy_mem_sum_premium']);
						

								} 
								else if (response.data[i]['policy_sub_type_id'] == 1)
								{
								   $("#sum_insures").attr("disabled", "disabled");
								  
								   $("#sum_insures").val(response.data[i]['policy_mem_sum_insured']);
							       $('#premium').val(response.data[i]['policy_mem_sum_premium']+".00");
								    //$("#patFamilyConstruct").empty();
								  
									setFamilyConstruct(response.data[i]['familyConstruct']);
									$("#patFamilyConstruct").attr("disabled", "disabled");
								 // $("#patFamilyConstruct").append('<option value = "'+response.data[i]['familyConstruct']+'" data-premium = "'+response.data[i]['policy_mem_sum_premium']+'">'+response.data[i]['familyConstruct']+'</option>');
								
						        }
						
						    }

						    for (var i = 0; i < response.data.length; i++) 
							{
						        if (response.data[i]['policy_sub_type_id'] == 3)
								{
							
							        patTable.data.push(response.data[i]);

						        }
								else if (response.data[i]['policy_sub_type_id'] == 1) 
								{
							       vtlTable.data.push(response.data[i]);
						        }
						
						    }

						  
						   addDependentForm("patTable", vtlTable);
						   //addDependentForm("vtlTable", patTable);
					    }  

					proposal_create_check();	
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
						var all_data = $("#emp_agent_data").serialize();
						$.ajax({
						url: "/tele_agent_data_insert",
						type: "POST",
						data: all_data,
						async: false,
						dataType: "json",
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
					{
						var all_data = $("#emp_data").serialize();
						$.ajax({
						url: "/tele_emp_data_insert",
						type: "POST",
						data: all_data,
						async: false,
						dataType: "json",
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
										text: "Employee Details Added Successfully",
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
											
							
									});
								
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
						nominee_lname: 
						{
							required: true,
							checkspace_nominee: true
						},
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
						var all_data = $("#nominee_data").serialize();
						$.ajax({
						url: "/tele_nominee_data_insert",
						type: "POST",
						data: all_data,
						async: false,
						dataType: "json",
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
				
			},sub_isposition:
			{
				required: true,
				
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
			av_remark:
			{
				required: true,
				
			},
		},
		messages: {

		},
		submitHandler: function (form) 
		{
			var all_data = $("#payment_details").serialize();
						$.ajax({
						url: "/tele_payment_details_insert",
						type: "POST",
						data: all_data,
						async: false,
						dataType: "json",
						success: function (response) 
						{
							debugger;
							if(response.disabled == 'Close'){
								$("#sub_isposition").attr("disabled", true);
								$("#disposition").attr("disabled", true);
								
							}
							$("#payment_details_tbody").html('');
							var str = '';
							$.each(response.data, function(index, item) {
								str+= '<tr><td>'+item.Dispositions+'</td><td> '+(item["Sub-dispositions"])+'</td><td>'+item.date+'</td><td>'+item.agent_name+'</td></tr>';
						});
						$("#payment_details_tbody").html(str);
							
							swal("Success", "Payment Details Saved", "success");
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

			if (mydeclare == 'No') 
			{
				ajaxindicatorstop();
				swal("Alert","As the Employee Declaration Question is No, the proposal cannot be processed", "warning");
				$("#myModal1").modal("hide");
				$("#sms_body").html('');
				return false;

			}
			//otp_generate();    
			
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
			
			
				var all_data = $("#emp_agent_data").serialize();
						$.ajax({
						url: "/tele_agent_data_insert",
						type: "POST",
						data: all_data,
						async: false,
						dataType: "json",
						success: function (response) 
						{
						}
						});
						
						
						var all_data = $("#emp_data").serialize();
						$.ajax({
						url: "/tele_emp_data_insert",
						type: "POST",
						data: all_data,
						async: false,
						dataType: "json",
						success: function (response) 
						{
						}
						});
			
			var all_data = $("#nominee_data").serialize();
        $.ajax({
            url: "/tele_nominee_data_insert",
            type: "POST",
            data: all_data,
            async: false,
            dataType: "json",
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

		var form = $("#proposal_data").serialize() + "&declares=" +JSON.stringify(TableData);

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
				$("#summaryForm").submit();
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
				
			}
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
                $th.val().replace(/[^A-Z.a-z ]/g, function (str) {
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
		var agent_code = $(this).val();
		 $.ajax({
			url: "/tele_agent_details",
			type: "POST",
			async: false,
			data: {agent_id : agent_code},
			dataType: "json",
			success: function (response) 
			{
				if(response != null && response != '')
				{
					$("#tl_id").val(response.tl_emp_id);
					$("#tl_name").val(response.tl_name.toUpperCase());
					$("#tl_id").val(response.tl_emp_id);
					/*$("#am_id").val(response.am_emp_id);
					$("#am_name").val(response.am_name.toUpperCase());
					$("#om_id").val(response.om_emp_id);
					$("#om_name").val(response.om_name.toUpperCase());*/
					$("#agent_name").val(response.base_agent_name.toUpperCase());
					$("#imd_code").val(response.imd_code);
					$("#axis_location option:contains("+response.center+")").attr("selected", true);
					$("#axis_location").trigger('change');
					setTimeout(function(){ $("#axis_vendor option:contains("+response.vendor+")").attr("selected", true);
					/*$("#axis_vendor").trigger('change');*/}, 2000);
					/*setTimeout(function(){ $("#axis_lob option:contains("+response.lob+")").attr("selected", true);}, 2000);*/
					
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

	if(response.status == 'Yes')
	{
		$("#patFormSubmit").hide();
		$(".del_p").hide();
		$("#agent_detail").hide();
		$("#update_emp").hide();
		$("#submit_nominee").hide();
	
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
	agent_cust_prefilled(response);
	$("#patFormSubmit").show();
	$(".del_p").show();
	$('#confr1').show();
	$("#agent_detail").show();
	$("#submit_nominee").show();
	}

	}
	});
}

function agent_cust_prefilled(response)
{
	

		var emp = response.base_agent_details;	
		var axis_det = response.axis_details;
		if(axis_det!= null && emp!= null)
		{
	    /*$('#axis_lob').html('<option value ="'+emp.axis_lob+'" selected>'+axis_det.axis_lob+'</option>');
		
		$('#axis_vendor').html('<option value ="'+emp.axis_vendor+'" selected>'+axis_det.axis_vendor+'</option>');
		/*employee data*/
		debugger;
		$('#axis_location').val(emp.axis_location);
		$("#axis_location").trigger('change');
		setTimeout(function(){ $('#axis_vendor').val(emp.axis_vendor);
		/*$("#axis_vendor").trigger('change');*/}, 2000);
		setTimeout(function(){$('#axis_lob').val(emp.axis_lob); }, 2000);
		$('#agent_id').val(emp.base_agent_id);
		$('#agent_name').val(emp.base_agent_name.toUpperCase());
		$("#comAdd2").val(emp.comm_address);
		$("#dobdate").val(emp.preferred_contact_date);
		$("#preferred_contact_time").val(emp.preferred_contact_time);
		$("#av_remark").val(emp.av_remark);
		$("#comAdd3").val(emp.comm_address1);
		$("#mobile_no2").val(emp.emg_cno);
		$('#tl_id').val(emp.tl_emp_id);
		$('#tl_name').val(emp.tl_name.toUpperCase());
		
		/*$('#om_name').val(emp.om_name.toUpperCase());
		$('#om_id').val(emp.om_emp_id);
		$('#am_id').val(emp.am_emp_id);
		$('#am_name').val(emp.am_name.toUpperCase());*/
		$('#imd_code').val(emp.imd_code);
		}
		var nominee = response.nominee_data;
		$('#nominee_relation').val(nominee.fr_id);
		$('#nominee_fname').val(nominee.nominee_fname.toUpperCase());
		$('#nominee_lname').val(nominee.nominee_lname.toUpperCase());
		$('#nominee_gender').val(nominee.nominee_gender);
		$('#nominee_contact').val(nominee.nominee_contact);
		$('#nominee_email').val(nominee.nominee_email);
		$('#dob').val(nominee.nominee_dob);
		$('#nominee_salutation').val(nominee.nominee_salutation);

	
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
$("#nominee_contact").keyup(function () 
  {
	
		if ($(this).val().match(/[^0-9]/g)) 
		{
			$(this).val($(this).val().replace(/[^0-9]/g, ""));
		}
	});
	
	
