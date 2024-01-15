<script>

function populateQuoteData(data) {
		let applicant_header_html = "";
		let net_premium = 0;
		for (let applicant_key in data) {

			if (applicant_key !== "net_premium") {

				let policy_count = 0;

				if (data[applicant_key]['policies']) {
					policy_count = Object.keys(data[applicant_key]['policies']).length;
				}

				if (!policy_count) {
					continue;
				}

				applicant_header_html += `
							<div class="head-lbl-2 mt-1" id="${applicant_key}_premium">
								<p class="head-lbl-1">${applicant_key}</p>
							</div>`;
			} else if (applicant_key == "net_premium") {
				net_premium = data["net_premium"];
			}
		}
		$('#total_premium').html(net_premium);
		//app_coapp_premium = net_premium
		$('#premium_calculations_data .head-lbl-2').remove();

		$("#premium_calculations_data").append(applicant_header_html);

		for (let applicant_key in data) {

			let policy_data = data[applicant_key];

			let policies = policy_data.policies;

			for (let policy_name in policies) {
				if (policies.hasOwnProperty(policy_name)) {

					$("#" + applicant_key + "_premium").append(
						`<p>${policy_name}<span class="fl-right"><i class="fa fa-inr"></i> ${policies[policy_name]}</span></p>`
					);
				}
			}
		}
	}

	var applicant_validation_object = {
		rules: vRules,
		messages: vMessages,
		submitHandler: function(form) {
			var act = "<?php echo base_url(); ?>policyproposal/submitForm";
			$("#cust_data").ajaxSubmit({
				url: act,
				type: 'post',
				dataType: 'json',
				cache: false,
				clearForm: false,
				beforeSubmit: function(arr, $form, options) {
					var mob = $("#cust_data" + coapplicant_tab_id + " #mobile_no2").val();
					var gender = $("#cust_data" + coapplicant_tab_id + " select[name='gender1']").find('option:selected').val();
					if (mob != '') {
						var filter = /^[6789]\d{9}$/;
						if (filter.test(mob)) {
							$(".moberror_customer").html("").css('display', 'none');
						} else {
							$(".moberror_customer").html("Please enter valid phone number").removeAttr('style');
							return false;
						}
					}

					if (gender == '') {

						$(".moberror_gender").html("Please select gender").removeAttr('style');
						return false;
					} else {

						$(".moberror_gender").html("").css('display', 'none');
					}
					$(".btn-primary").hide();
					//return false;
				},
				success: function(response) {
					$(".btn-primary").show();
					if (response.success) {

						$("#self_age_" + coapplicant_tab_id).val(response.self_age);
						displayMsg("success", response.msg);
						enableNextAccordion("#cust_data");

					} else {
						displayMsg("error", response.msg);
						return false;
					}
				}
			});

		}
	}


    $("#accordion480" + insuredFormCount + " form").validate({
      rules: vRules_member_data,
      messages: vMessages_member_data,
      submitHandler: function(form) {
         var act = "<?php echo base_url(); ?>policyproposal/submitInsuredMemberForm";
         var form = "#accordion480" + insuredFormCount + " form";

         if ($(form + ' #' + current_tab).attr('data-member') != '') {

            $('input[name="member_id"]').val($(form + ' #' + current_tab).attr('data-member'));
         }
         gender = $(form + ' select[name="gender"] option:selected').text();
         $(form + ' input[name="member_salutation"]').val(gender);

         if(family_construct == '1-0'){

            member_type = $(form + ' select[name="member_type_id"]').val();
            if(member_type == 1){

               if($("#accordion460" + insuredFormCount + " form input[name='spouse_age']").val() != ''){

                  $("#accordion460" + insuredFormCount + " form input[name='spouse_age']").val('');
                  $("#accordion460" + insuredFormCount + " form").submit();
               }
            }
         }

         $(form).ajaxSubmit({
            url: act,
            type: 'post',
            dataType: 'json',
            cache: false,
            clearForm: false,
            async: false,
            beforeSubmit: function(arr, $form, options) {
               if (premium >= 50000) {
                  if ($('.pan_added').val() != 'y') {

                     $('.modal').find('.modal-title').text('Proposer PAN Required');
                     $('.modal')
                        .find('.modal-body')
                        .html('<form onsubmit="submitPan(this);return false;" action="<?php echo base_url(); ?>policyproposal/capturecustomerpan"><input type="text" name="proposer_pan">&nbsp;&nbsp;<span class="msg">&nbsp;&nbsp;</span><button type="submit" class="submit-btn-pan btn smt-btn">Save</button></form>');
                     $('.modal-footer').addClass('d-none');
                     $('.modal').modal('show');
                     premium = 0;
                     return false;
                  }
               }
            },
            success: function(response) {

               if (response.success) {
                  //alert("true");
                  $("#accordion480" + insuredFormCount + " form #" + current_tab).attr('data-member', response.member_id);
                  
                  if ($('#accordion480'  + insuredFormCount + " form #" + current_tab).next('.nav-link:visible').hasClass('disabled') == true) {

                     id = $("#accordion480" + insuredFormCount + " form #" + current_tab).next('.nav-link:visible').attr('id');
                     $('#accordion480' + insuredFormCount + ' form #' + id).attr('data-member-added', response.data_added);
                     $("#accordion480" + insuredFormCount + " form #" + id).removeClass('disabled').trigger('click');

                     if ($("#accordion480" + insuredFormCount + " form #" + id).next('.nav-link:visible').length) {

                        $('#accordion480' + insuredFormCount + ' form')[0].reset();
                     }

                     current_tab = id;
                  }
                  else{

                     $('#accordion480' + insuredFormCount + ' form #' + current_tab).trigger('click');
                  }

                  displayMsg("success", response.msg);
                  
               } else {
                  //alert("false");
                  displayMsg("error", response.msg);
                  return false;
               }
            }
         });
      }
   });
</script>