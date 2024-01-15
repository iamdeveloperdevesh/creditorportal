<div class="tab-pane fade show active" id="self-tab" role="tabpanel" aria-labelledby="self-tab">
   <div class="alert alert-danger d-none fade show member-tab-error-msg">
      <span>Please fill and save the current tab data and then proceed on the next tab</span>
   </div>
   <div class="col-md-4 mb-3" style="padding-left:0px;">
      <div class="input-group">
         <select name="member_type_id" class="form-control">
            <option value="">Select Member Type</option>
            <?php
               if($current_tab == 'self-tab' || $current_tab == 'spouse-tab'){

                  if(isset($member_added) && $member_added != ''){

                     if($member_added == 1){
                        ?>
                        <option value="2" <?php if(isset($data[0]['relation_with_proposal']) && $data[0]['relation_with_proposal'] == 2){ echo 'selected'; } ?>>Spouse</option>
                        <?php
                     }
                     else if($member_added == 2){

                        ?>
                        <option value="1" <?php if(isset($data[0]['relation_with_proposal']) && $data[0]['relation_with_proposal'] == 1){ echo 'selected'; } ?>>Self</option>
                        <?php
                     }
                  }
                  else if(isset($data[0]['relation_with_proposal']) && $data[0]['relation_with_proposal'] == 1){

                     ?>
                     <option value="1" selected>Self</option>
                     <?php
                  }
                  else if(isset($data[0]['relation_with_proposal']) && $data[0]['relation_with_proposal'] == 2){

                     ?>
                     <option value="1" selected>Spouse</option>
                     <?php
                  }
                  else{

                     ?>
                     <option value="1">Self</option>
                     <option value="2">Spouse</option>
                     <?php
                  }
               ?>
               
               <?php
               }  
               else if($current_tab == 'kid1-tab' || $current_tab == 'kid2-tab'){ 
               ?>
               <option value="5" <?php if(isset($data[0]['relation_with_proposal']) && $data[0]['relation_with_proposal'] == 5){ echo 'selected'; } ?>>Son</option>
               <option value="6" <?php if(isset($data[0]['relation_with_proposal']) && $data[0]['relation_with_proposal'] == 6){ echo 'selected'; } ?>>Daughter</option>
               <?php
               }   
            ?>
         </select>
         <div class="input-group-prepend">
            <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
         </div>
      </div>
   </div>
   <p>
   <div class="row">
      <div class="col-md-4 mb-3">
         <label for="validationCustomUsername" class="col-form-label">Salutation<span class="lbl-star">*</span></label>
         <div class="input-group">
            <select name="gender" class="form-control">
                  <option value="">Select Salutation</option>
                  <?php
                     if($current_tab == 'self-tab' || $current_tab == 'spouse-tab'){
                     ?>
                     <option value="Male" <?php if(isset($data[0]['policy_member_salutation']) && $data[0]['policy_member_salutation'] == 'Mr'){ echo "selected"; } ?>>Mr</option>
                     <option value="Female" <?php if(isset($data[0]['policy_member_salutation']) && $data[0]['policy_member_salutation'] == 'Mrs'){ echo "selected"; } ?>>Mrs</option>
                     <?php
                     }
                     if($current_tab == 'kid1-tab' || $current_tab == 'kid2-tab'){
                     ?>
                     <option value="Male" <?php if(isset($data[0]['policy_member_salutation']) && $data[0]['policy_member_salutation'] == 'Master'){ echo "selected"; } ?>>Master</option>
                     <option value="Female" <?php if(isset($data[0]['policy_member_salutation']) && $data[0]['policy_member_salutation'] == 'Ms'){ echo "selected"; } ?>>Ms</option>
                     <?php
                     }
                  ?>
            </select>
            <div class="input-group-prepend">
               <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
            </div>
         </div>
      </div>
      <div class="col-md-4 mb-3">
         <label for="validationCustomUsername" class="col-form-label">First Name<span class="lbl-star">*</span></label>
         <div class="input-group">
            <input type="text" class="form-control" name="first_name" value="<?php if (isset($data[0]['policy_member_first_name'])) {
                                                                                 echo $data[0]['policy_member_first_name'];
                                                                              } ?>" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
            <div class="input-group-prepend">
               <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
            </div>
         </div>
      </div>
      <div class="col-md-4 mb-3">
         <label for="validationCustomUsername" class="col-form-label">Last Name<span class="lbl-star">*</span></label>
         <div class="input-group">
            <input type="text" class="form-control" name="last_name" value="<?php if (isset($data[0]['policy_member_last_name'])) {
                                                                                 echo $data[0]['policy_member_last_name'];
                                                                              } ?>" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
            <div class="input-group-prepend">
               <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
            </div>
         </div>
      </div>
      <div class="col-md-4 mb-3">
         <label for="validationCustomUsername" class="col-form-label">Gender<span class="lbl-star">*</span></label>
         <div class="input-group">
            <select name="insured_member_gender" class="form-control" disabled>
               <option value="">Gender</option>
               <option value="Male" <?php if (isset($data[0]['policy_member_gender']) && $data[0]['policy_member_gender'] == "Male") {
                                       echo "selected";
                                    } ?>>MALE</option>
               <option value="Female" <?php if (isset($data[0]['policy_member_gender']) && $data[0]['policy_member_gender'] == "Female") {
                                          echo "selected";
                                       } ?>>FEMALE</option>
            </select>
            <div class="input-group-prepend">
               <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
            </div>
         </div>
      </div>
      <div class="col-md-4 mb-3">
         <label for="validationCustomUsername" class="col-form-label">Date of Birth<span class="lbl-star">*</span></label>
         <div class="input-group">
            <input type="hidden" name="member_dob" value="<?php echo (isset($data[0]['policy_member_dob'])) ? date('d-m-Y', strtotime($data[0]['policy_member_dob'])) : ''; ?>" />
            <input class="form-control insured_member_dob" type="text" class="form-control" name="insured_member_dob" value="" placeholder="Enter ..." aria-describedby="inputGroupPrepend" autocomplete="off">
            <div class="input-group-prepend">
               <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">calendar_today</span></span>
            </div>
         </div>
      </div>
      <?php /*<div class="col-md-4 mb-3">
         <label for="validationCustomUsername" class="col-form-label">PAN</label>
         <div class="input-group">
            <input type="text" class="form-control" name="pan" id="pan" value="<?php echo $data[0]['policy_member_pan']?>" placeholder="Enter ..." aria-describedby="inputGroupPrepend">
            <div class="input-group-prepend">
               <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">phone_android</span></span>
            </div>
         </div>
      </div>*/ ?>
   </div>
   </p>
   <div class="row mt-4">
      <input type="hidden" name="lead_id" value="<?php echo $lead_id; ?>" />
      <input type="hidden" name="trace_id" value="<?php echo $trace_id; ?>" />
      <input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>" />
      <input type="hidden" class="customer_id_hidden<?php echo $coapplicant_tab_id ?? '' ?>" name="customer_id" value="<?php echo $customer_id; ?>" />
      <input type="hidden" name="member_id" value="<?php echo $member_id; ?>" />
      <input type="hidden" name="member_salutation" value="" />
      <input type="hidden" name="insured_form_count" value="<?php echo $insured_form_count; ?>" />
      <input type="hidden" class="proposal_id_hidden<?php echo $coapplicant_tab_id ?? '' ?>" name="proposal_id" value="<?php echo $proposal_id; ?>" />
      <div class="col-md-1 col-6 text-left">
         <button class="btn smt-btn">Save</button>
      </div>
      <div class="col-md-2 col-6 text-right">
         <button class="btn cnl-btn">Cancel</button>
      </div>
   </div>
</div>
<script>
   var coapplicant_tab_id = $('#coapplicant_tab_id').val();
   var insuredFormCount = coapplicant_tab_id;

   $(document).ready(function() {

      if (insuredFormCount == 0) {

         insuredFormCount = '';
      }

      $("#accordion480" + insuredFormCount + " input[name='insured_member_dob']").val("<?php echo (isset($data[0]['policy_member_dob'])) ? date('d-m-Y', strtotime($data[0]['policy_member_dob'])) : ''; ?>");

      $('body').on('change', '#accordion480' + insuredFormCount + ' select[name="gender"]', function() {

         sal_gender = $(this).find('option:selected').val();
         if (sal_gender != '') {

            $('#accordion480' + insuredFormCount + ' select[name="insured_member_gender"]').val(sal_gender);
         }
      });
   });

   /* for memberform Insured  form **/
   var vRules_member_data = {
      member_type_id: {
         required: true
      },
      first_name: {
         required: true,
         firstnamelettersonly: true
      },
      gender: {
         required: true
      },
      last_name: {
         required: true,
         lastnamevalidate: true
      },
      insured_member_dob: {
         required: true
      }
   };

   var vMessages_member_data = {
      member_type_id: {
         required: "Field is required"
      },
      first_name: {
         required: "Field is required"
      },
      gender: {
         required: "Field is required"
      },
      last_name: {
         required: "Field is required"
      },
      insured_member_dob: {
         required: "Field is required"
      }
   };

   $('body').on('change', '#accordion480' + insuredFormCount + ' select[name="member_type_id"]', function() {

      relation = $(this).find('option:selected').val();
      $(this).closest('form').find('.insured_member_dob').attr('disabled', false).removeClass('spouse_age'+insuredFormCount);
      if (relation == 5) {

         $('#accordion480' + insuredFormCount + ' select[name="gender"]').html('<option value="Male" selected>Master</option>');
         $('#accordion480' + insuredFormCount + ' select[name="insured_member_gender"]').html('<option value="Male" selected>MALE</option>');
      }
      else if (relation == 6) {

         $('#accordion480' + insuredFormCount + ' select[name="gender"]').html('<option value="Female" selected>Ms</option>');
         $('#accordion480' + insuredFormCount + ' select[name="insured_member_gender"]').html('<option value="Female" selected>FEMALE</option>');
      }
      else if(relation == 2){

         isSpouseAgeRequired = $('#is_spouse_age_required').val();

         if(isSpouseAgeRequired == '1'){

            spouse_dob = $('#accordion460' + insuredFormCount + ' input[name="spouse_dob"]').val();
            $(this).closest('form').find('.insured_member_dob').datepicker('setDate', spouse_dob).attr('readonly', true).addClass('spouse_age'+insuredFormCount);
         }
      }
   });

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