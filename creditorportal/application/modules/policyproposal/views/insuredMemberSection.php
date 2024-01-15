<?php

$salutation = '';
$first_name = '';
$last_name = '';
$gender = '';
$dob = '';
$lead_id = '';
$customer_id = '';
$trace_id = '';
//$proposal_details_id = '';
//$plan_id = '';
$self = '';

$relations = $is_adult = $added_to_list = [];
$min_age = $max_age = 0;
$dob_json = [];
if(!empty($leaddetails->plan_details)){
   $dob_limit_object =  $leaddetails->plan_details[0]->family_construct;
   $dob_limit_array=[];
   $today = date('Y-m-d');
   foreach ($dob_limit_object as $key => $value) {
      $array_value = (array) $value;

      if(!empty($array_value['member_min_age_days'])){
         $end_date=date('Y-m-d',  strtotime('-'.$array_value['member_min_age_days'].' days'));
      }else{
         $end_date =date('Y-m-d',  strtotime('-'.$array_value['member_min_age'].' year'));
      }
      $start_date = date('Y-m-d',  strtotime('-'.$array_value['member_max_age'].' year'));
      $array_value['max_date']=$end_date;
      $array_value['min_date']=$start_date;
      $dob_limit_array[$value->member_type_id]=$array_value;
     
   }
   
}
$dob_json = json_encode($dob_limit_array);
if (!empty($criterias)) {

   foreach ($criterias as $master_policy_id => $member_relations) {

      foreach ($member_relations as $member_relation_id => $member_criteria) {

         $relations[$member_relation_id] = $family_constuct_relation_map[$member_relation_id]['member_type'];
         $is_adult[$member_relation_id] = $family_constuct_relation_map[$member_relation_id]['is_adult'];
      }
   }
}

if (!isset($not_self)) {

   $not_self = 0;
}

if (isset($customer)) {

   if (!is_array($customer)) {

      $customer = json_decode(json_encode($customer), true);
   }
}
?>
<script>
   //var coapplicant_tab_id = $('#coapplicant_tab_id').val();
   var insuredFormCount = "<?php echo $coapplicant_tab_id ?? '' ?>"; //coapplicant_tab_id;
   var self_dob_selector = '';
   var current_tab = 'member-tab-' + insuredFormCount + '1';
</script>
<div class="card-body pad-0">
   <div class="col-lg-12 mt-1">
      <nav>
         <div class="nav nav-tabs bor-done" id="nav-tab" role="tablist">
            <?php

            $k = 1;
            $adult_count = 0;
            $generated_tabs = [];
            foreach ($member_count as $key => $value) {

               for ($i = 0; $i < $value; $i++) {

                  if ($key == 'adult_count') {

                     $label = 'Adult ' . ($i + 1);
                     $adult_count += $i + 1;
                  } else if ($key == 'child_count') {

                     $label = 'Kid ' . ($i + 1);
                  }

                  if ($i == 0 && $key == 'adult_count') {
            ?>
                     <a class="nav-item nav-link insured-member active link-done" id="<?= "member-tab-" . ($coapplicant_tab_id ?? '') . ($k); ?>" href="#<?= 'tab-' . ($coapplicant_tab_id ?? '') . ($k); ?>" data-toggle="tab" role="tab" aria-selected="true"><?= $label; ?><span class="material-icons rad-span rad-span1">person_outline</span></a>
                  <?php

                  } else {

                  ?>
                     <a class="nav-item nav-link insured-member" id="<?= "member-tab-" . ($coapplicant_tab_id ?? '') . ($k); ?>" href="#<?= 'tab-' . ($coapplicant_tab_id ?? '') . ($k); ?>" data-toggle="tab" role="tab" aria-selected="false"><?= $label; ?><span class="material-icons rad-span rad-span1">person_outline</span></a>
            <?php
                  }

                  $generated_tabs[$k] = strtolower(str_replace(' ', '-', $label));
                  $k++;
               }
            }
            ?>
         </div>
      </nav>
      <div class="tab-content mt-3 insured-member-section" id="nav-tabContent">
         <?php

         $is_last_tab = '';
         for ($i = 1; $i <= count($generated_tabs); $i++) {

            if ($i == 1) {

               if (!$not_self) {

                  $self = 'selected';
               }

               if (isset($customer['dob']) && $customer['dob'] != '' && !$not_self) {

                  $dob = date('d-m-Y', strtotime($customer['dob']));
               }

               if (isset($customer['salutation']) && !$not_self) {

                  $salutation = $customer['salutation'];
               }

               if (isset($customer['first_name']) && !$not_self) {

                  $first_name = $customer['first_name'];
               }

               if (isset($customer['last_name']) && !$not_self) {

                  $last_name = $customer['last_name'];
               }

               if (isset($customer['gender']) && !$not_self) {

                  $gender = $customer['gender'];
               }

               if (isset($customer['lead_id'])) {

                  $lead_id = $customer['lead_id'];
               }

               if (isset($customer['trace_id'])) {

                  $trace_id = $customer['trace_id'];
               }

               if (isset($customer['proposal_details_id'])) {

                  $proposal_details_id = $customer['proposal_details_id'];
               }

               if (isset($customer['customer_id'])) {

                  $customer_id = $customer['customer_id'];
               }

               if (isset($customer['plan_id'])) {

                  $plan_id = $customer['plan_id'];
               }
            } else {

               $salutation = '';
               $first_name = '';
               $last_name = '';
               $gender = '';
               $dob = '';
               $self = '';
            }

            if ($i == count($generated_tabs)) {

               $is_last_tab = 'is_last_tab';
            }
         ?>
            <div class="<?php if (strpos($generated_tabs[$i], 'adult') !== false) {
                           echo "adult-tab ";
                        } ?>tab-pane fade<?php if ($i == 1) { ?> show active<?php } ?>" id="tab-<?php echo $coapplicant_tab_id ?? '' ?><?= $i ?>" role="tabpanel" aria-labelledby="tab-<?php echo $coapplicant_tab_id ?? '' ?><?= $i ?><?= $i ?>">
               <form id="memberform<?php echo $coapplicant_tab_id ?? '' ?><?= $i; ?>" name="memberform<?php echo $coapplicant_tab_id ?? '' ?><?= $i; ?>" method="post" action="<?php echo base_url(); ?>policyproposal/submitInsuredMemberForm">
                  <div class="alert alert-danger d-none fade show member-tab-error-msg">
                     <span>Please fill and save the current tab data and then proceed on the next tab</span>
                  </div>
                  <div class="col-md-4 mb-3" style="padding-left:0px;">
                     <div class="input-group">
                        <select name="member_type_id" class="form-control">
                           <option value="">Select Member Type</option>
                           <?php
                           $current_member_self = false;
                           foreach ($relations as $member_type_id => $member_relation) {

                              if (strpos($generated_tabs[$i], 'adult') !== false) { //if current tab is adult tab

                                 if ($is_adult[$member_type_id] == 'Y') { //if current member relation is an adult

                                    if ($adult_count == 1) {

                                       if (!$not_self) {

                                          if ($member_type_id == 1) {
                                             $current_member_self = true;
                           ?>
                                             <option value="<?= $member_type_id; ?>" <?= $self; ?>><?= $member_relation; ?></option>
                                          <?php
                                             break;
                                          }
                                       } else {

                                          if ($member_type_id > 1) {
                                          ?>
                                             <option value="<?= $member_type_id; ?>"><?= $member_relation; ?></option>
                                          <?php
                                             break;
                                          }
                                       }
                                    } else if ($adult_count > 1) {

                                       if ($i == 1 && $member_type_id == 1) {

                                          if (!in_array($member_type_id, $added_to_list)) {
                                             $current_member_self = true;
                                             $added_to_list[$member_type_id] = $member_type_id;
                                          ?>
                                             <option value="<?= $member_type_id; ?>" <?= $self; ?>><?= $member_relation; ?></option>
                                          <?php
                                             break;
                                          }
                                       } else {

                                          if (!in_array($member_type_id, $added_to_list)) {

                                             $added_to_list[$member_type_id] = $member_type_id;
                                          ?>
                                             <option value="<?= $member_type_id; ?>"><?= $member_relation; ?></option>
                                       <?php
                                             break;
                                          }
                                       }
                                       ?>
                                    <?php
                                    }
                                 }
                              } else {

                                 if ($is_adult[$member_type_id] == 'N') { //if current member relation is a kid
                                    ?>
                                    <option value="<?= $member_type_id; ?>"><?= $member_relation; ?></option>
                           <?php
                                 }
                              }
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
                        <label for="validationCustomUsername" class="col-form-label">Salutation</label>
                        <div class="input-group">
                           <select name="gender" class="mem_salutation form-control" <?php if($current_member_self){ echo 'readonly="readonly"'.' '.'style="pointer-events: none;"'; } ?>>
                              <option value="">Select Salutation</option>
                              <?php

                              if (strpos($generated_tabs[$i], 'adult') !== false) {
                              ?>
                                 <option value="Male" <?php echo ($salutation == 'Mr') ? 'selected' : '' ?>>Mr</option>
                                 <option value="Female" <?php echo ($salutation == 'Mrs') ? 'selected' : '' ?>>Mrs</option>
                                 <option value="Female" <?php echo ($salutation == 'Ms') ? 'selected' : '' ?>>Ms</option>
                                 <option value="" <?php echo ($salutation == 'Dr') ? 'selected' : '' ?>>Dr</option>
                              <?php
                              } else {
                              ?>
                                 <option value="Male" <?php echo ($salutation == 'Master') ? 'selected' : '' ?>>Master</option>
                                 <option value="Female" <?php echo ($salutation == 'Ms') ? 'selected' : '' ?>>Ms</option>
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
                           <input type="text" class="form-control validname" name="first_name" value="<?php echo $first_name; ?>" placeholder="Enter First Name" aria-describedby="inputGroupPrepend" <?php if($current_member_self){ echo 'readonly="readonly"'; } ?>>
                           <div class="input-group-prepend">
                              <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-4 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">Last Name<span class="lbl-star">*</span></label>
                        <div class="input-group">
                           <input type="text" class="form-control validname" name="last_name" value="<?php echo $last_name; ?>" placeholder="Enter Last Name" aria-describedby="inputGroupPrepend" <?php if($current_member_self){ echo 'readonly="readonly"'; } ?>>
                           <div class="input-group-prepend">
                              <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">assignment_ind</span></span>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-4 mb-3">
                        <label for="validationCustomUsername" class="col-form-label">Gender<span class="lbl-star">*</span></label>
                        <div class="input-group">
                           <select name="insured_member_gender" class="form-control mem_gender" disabled>
                              <option value="">Select Gender</option>
                              <option value="Male" <?php if ($gender == "Male") {
                                                      echo "selected";
                                                   } ?>>MALE</option>
                              <option value="Female" <?php if ($gender == "Female") {
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
                           <input class="form-control insured_member_dob <?php if($current_member_self){ echo 'selfDob'.$coapplicant_tab_id ?? ''; } ?>" type="text" name="insured_member_dob<?php echo $coapplicant_tab_id ?? '' ?><?php echo $i; ?>" value="<?= $dob; ?>" id="insured_member_dob<?php echo $coapplicant_tab_id ?? '' ?><?php echo $i; ?>" placeholder="DD-MM-YYYY" onkeydown="return false;" aria-describedby="inputGroupPrepend" autocomplete="off" <?php if($current_member_self){ echo 'readonly="readonly"'.' '.'style="pointer-events: none;"'; } ?>>
                           <div class="input-group-prepend">
                              <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">calendar_today</span></span>
                           </div>
                        </div>
                     </div>
                  </div>
                  </p>
                  <div class="row mt-4 form-buttons">
                     <input type="hidden" name="lead_id" value="<?php echo $lead_id; ?>" />
                     <input type="hidden" name="trace_id" value="<?php echo $trace_id; ?>" />
                     <input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>" />
                     <input type="hidden" class="customer_id_hidden<?php echo $coapplicant_tab_id ?? '' ?>" name="customer_id" value="<?php echo $customer_id; ?>" />
                     <input type="hidden" name="member_id" value="" />
                     <input type="hidden" name="self_dob" value="<?= $dob; ?>" />
                     <input type="hidden" name="co_applicant_tab_id" value="<?php echo $coapplicant_tab_id ?? '' ?>" />
                     <input type="hidden" name="tab_id" value="<?= $i; ?>" />
                     <input type="hidden" name="member_salutation" value="" />
                     <input type="hidden" class="proposal_id_hidden<?php echo $coapplicant_tab_id ?? '' ?>" name="proposal_id" value="<?php echo (isset($proposal_details_id)) ? $proposal_details_id : ''; ?>" />
                     <div class="col-md-1 col-6 text-left">
                        <button type="submit" class="btn smt-btn">Save</button>
                     </div>
                  </div>
               </form>
            </div>
            <script type="text/javascript">
               var premium = 0;
               if (<?= $i; ?> == 1) {

                  self_dob_selector = 'insured_member_dob' + insuredFormCount + '<?= $i; ?>';
               }
               $("#accordion480" + insuredFormCount + " #memberform" + insuredFormCount + "<?= $i; ?>").on('submit', function() {
                  
                  member_type_id = $('select[name="member_type_id"] option:selected', this).val();
                  gender = $('select[name="gender"] option:selected', this).val();
                  gender_text = $('select[name="gender"] option:selected', this).text();
                  first_name = $('input[name="first_name"]', this).val();
                  last_name = $('input[name="last_name"]', this).val();
                  insured_member_gender = $('select[name="insured_member_gender"] option:selected', this).val();
                  insured_member_dob = $('input[name="insured_member_dob' + insuredFormCount + '<?= $i; ?>"]', this).val();
                  isLastTab = "<?= $is_last_tab; ?>";
                  formID = $(this).attr('id');

                  hasError = 0;
                  if ($.trim(member_type_id) == '') {

                     hasError = 1;
                     showError("#accordion480" + insuredFormCount + " #memberform" + insuredFormCount + "<?= $i; ?> select[name='member_type_id']", 'Field is required');
                  } else {

                     removeError("#accordion480" + insuredFormCount + " #memberform" + insuredFormCount + "<?= $i; ?> select[name='member_type_id']");
                  }

                  if ($.trim(gender) == '' && $.trim(gender_text) == 'Select Salutation') {

                     hasError = 1;
                     showError("#accordion480" + insuredFormCount + " #memberform" + insuredFormCount + "<?= $i; ?> select[name='gender']", 'Field is required');
                  } else {

                     removeError("#accordion480" + insuredFormCount + " #memberform" + insuredFormCount + "<?= $i; ?> select[name='gender']");
                     gender = $('select[name="gender"] option:selected', this).text();
                  }

                  if ($.trim(first_name) == '') {

                     hasError = 1;
                     showError("#accordion480" + insuredFormCount + " #memberform" + insuredFormCount + "<?= $i; ?> input[name='first_name']", 'Field is required');
                  } else if (!/^[a-z\s]+$/i.test(first_name)) {

                     hasError = 1;
                     showError("#accordion480" + insuredFormCount + " #memberform" + insuredFormCount + "<?= $i; ?> input[name='first_name']", 'Only alphabets allowed');
                  } else {

                     removeError("#accordion480" + insuredFormCount + " #memberform" + insuredFormCount + "<?= $i; ?> input[name='first_name']");
                  }

                  if ($.trim(last_name) == '') {

                     hasError = 1;
                     showError("#accordion480" + insuredFormCount + " #memberform" + insuredFormCount + "<?= $i; ?> input[name='last_name']", 'Field is required');
                  } else if (!/^[a-z\s]+$/i.test(last_name) && !/^\.$/i.test(last_name)) {

                     hasError = 1;
                     showError("#accordion480" + insuredFormCount + " #memberform" + insuredFormCount + "<?= $i; ?> input[name='last_name']", 'Only alphabetical characters or a single period allowed');
                  } else {

                     removeError("#accordion480" + insuredFormCount + " #memberform" + insuredFormCount + "<?= $i; ?> input[name='last_name']");
                  }

                  if ($.trim(insured_member_gender) == '') {

                     hasError = 1;
                     showError("#accordion480" + insuredFormCount + " #memberform" + insuredFormCount + "<?= $i; ?> select[name='insured_member_gender']", 'Field is required');
                  } else {

                     removeError("#accordion480" + insuredFormCount + " #memberform" + insuredFormCount + "<?= $i; ?> select[name='insured_member_gender']");
                  }

                  if ($.trim(insured_member_dob) == '') {

                     hasError = 1;
                     showError("#accordion480" + insuredFormCount + " #memberform" + insuredFormCount + "<?= $i; ?> input[name='insured_member_dob" + insuredFormCount + "<?= $i ?>']", 'Field is required');
                  } else {

                     removeError("#accordion480" + insuredFormCount + " #memberform" + insuredFormCount + "<?= $i; ?> input[name='insured_member_dob" + insuredFormCount + "<?= $i ?>']");
                  }

                  if (!hasError) {

                     if ($('#' + current_tab, this).attr('data-member') != '') {

                        $('input[name="member_id"]').val($('#' + current_tab, this).attr('data-member'));
                     }

                     $('input[name="member_salutation"]', this).val(gender);

                     family_construct = $("#accordion460" + insuredFormCount + " form #family_members_ac_count option:selected").val();
                     if (family_construct == '1-0') {

                        if (member_type_id == 1) {

                           if ($("#accordion460" + insuredFormCount + " form input[name='spouse_age']").val() != '') {

                              $("#accordion460" + insuredFormCount + " form input[name='spouse_age']").val('');
                              $("#accordion460" + insuredFormCount + " form").submit();
                           }
                        }
                     }

                     if(gender_text == 'Dr'){

                        $('select[name="insured_member_gender"]', this).removeAttr('disabled');
                     }

                     action = $(this).attr('action');
                     data = $(this).serialize();

                     $.ajax({

                        url: action,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        cache: false,
                        clearForm: false,
                        async: false,
                        beforeSend: function() {
                           
                           /*if(premium == 0){

                              quote = getQuoteDetails();
                              if (quote) {

                                 $.each(quote, function(key, value) {

                                    if (value.premium_with_tax != null) {
                                       premium += parseFloat(value.premium_with_tax);
                                    }
                                 });
                              }

                              if (premium >= 50000) {

                                 if ($('.pan_added').val() != 'y') {

                                    $('.age-diff-modal .modal-title').text('Proposer PAN Required');
                                    $('.age-diff-modal .modal-body')
                                       .html('<form onsubmit="submitPan(this);return false;" action="<?php echo base_url(); ?>policyproposal/capturecustomerpan"><input type="text" name="proposer_pan">&nbsp;&nbsp;<span class="msg">&nbsp;&nbsp;</span><button type="submit" class="submit-btn-pan btn smt-btn">Save</button></form>');
                                    $('.age-diff-modal .modal-footer').addClass('d-none');
                                    $('.age-diff-modal').modal('show');

                                    //premium = 0;
                                    return false;
                                 }
                              }
                           }*/
                        },
                        success: function(response) {

                           if (response.success) {

                              //alert("true");
                              $("#accordion480" + insuredFormCount + " #memberform" + insuredFormCount + "" + "<?= $i; ?>" + " input[name='member_id']").val(response.member_id);

                              //if ($('#accordion480' + insuredFormCount + " form #" + current_tab).next('.nav-link:visible').hasClass('disabled') == true) {

                              id = $("#accordion480" + insuredFormCount + " #" + current_tab).next('.nav-link:visible').attr('id');
                              //$('#accordion480' + insuredFormCount + ' #' + id).attr('data-member-added', response.data_added);
                              $("#accordion480" + insuredFormCount + " #" + id).trigger('click');

                              /*if ($("#accordion480" + insuredFormCount + " #" + id).next('.nav-link:visible').length) {

                                 $('#accordion480' + insuredFormCount + ' form')[0].reset();
                              }*/

                              current_tab = id;
                              //} else {

                              //$('#accordion480' + insuredFormCount + ' #' + current_tab).trigger('click');
                              //}

                              displayMsg("success", response.msg);

                              if (isLastTab) {

                                 enableNextAccordion("#" + formID);
                              }

                           } else {
                              //alert("false");
                              displayMsg("error", response.msg);

                           }
                        }
                     });
                  }

                  return false;
               });
            </script>
         <?php
         }
         ?>
      </div>
   </div>
</div>

<script>
   $(document).ready(function() {

      if (insuredFormCount == 0) {

         insuredFormCount = '';
      }

      //$("#accordion480" + insuredFormCount + " input[name='insured_member_dob']").val("<?php echo (isset($data[0]['policy_member_dob'])) ? date('d-m-Y', strtotime($data[0]['policy_member_dob'])) : ''; ?>");
      $('body').on('keyup blur',".validname",function(){ 
        var node = $(this);
        node.val(node.val().replace(/[^a-zA-Z ]/g,'') ); }
      );

      $('body').on('change', '#accordion480' + insuredFormCount + ' select[name="gender"]', function() {

         sal_gender = $(this).find('option:selected').val();
         formID = $(this).closest('form').attr('id');
         if (sal_gender != '') {

            $('#accordion480' + insuredFormCount + ' #' + formID + ' select[name="insured_member_gender"]').val(sal_gender).attr('disabled', 'disabled');
         }
         else{

            $('#accordion480' + insuredFormCount + ' #' + formID + ' select[name="insured_member_gender"]').removeAttr('disabled');
         }
      });

      $('body').on('change', '#accordion480' + insuredFormCount + ' select[name="member_type_id"]', function() {
debugger;
         relation = $(this).find('option:selected').val();
         formID = $(this).closest('form').attr('id');

         $('#' + formID).find('.insured_member_dob').attr('readonly', false).removeClass('spouse_age' + insuredFormCount);
         if (relation == '5') {

            $('#accordion480' + insuredFormCount + ' #' + formID + ' select[name="gender"]').html('<option value="Male" selected>Master</option>');
            $('#accordion480' + insuredFormCount + ' #' + formID + ' select[name="insured_member_gender"]').html('<option value="Male" selected>MALE</option>');
         } else if (relation == '6') {

            $('#accordion480' + insuredFormCount + ' #' + formID + ' select[name="gender"]').html('<option value="Female" selected>Ms</option>');
            $('#accordion480' + insuredFormCount + ' #' + formID + ' select[name="insured_member_gender"]').html('<option value="Female" selected>FEMALE</option>');
         } else if (relation == '2') {

            isSpouseAgeRequired = $('#is_spouse_age_required').val();
            var self_salutation = $("#salutation").val();
            var spouse_salutation = '';
            var spouse_gender = '';
            if(self_salutation == 'Mr'){
               spouse_salutation = 'Female';
               spouse_gender = 'Female';
            }else if(self_salutation == 'Mrs'){
               spouse_salutation = 'Male';
               spouse_gender = 'Male';
            }
            $('#' + formID).closest('form').find('.mem_salutation').val(spouse_salutation).css('pointer-events', 'none');
            $('#' + formID).closest('form').find('.mem_gender').val(spouse_gender);




            if (isSpouseAgeRequired == '1') {              
               spouse_dob = $('#accordion460' + insuredFormCount + ' input[name="spouse_dob"]').val();
               $('#' + formID).closest('form').find('.insured_member_dob').datepicker('setDate', spouse_dob).attr('readonly', true).addClass('spouse_age' + insuredFormCount);               
            }

             
            
         }
         $('.insured_member_dob').datepicker({
         changeMonth: true,
         changeYear: true,
         maxDate: new Date(),
         dateFormat: 'dd-mm-yy',
         yearRange: "-100:" + new Date('Y'),
         onSelect: function(dateText) {

               if ($(this).closest('form').find('select[name="member_type_id"]').val() == 2) {

                  checkRelation(dateText);
               }
            },
            beforeShow: function(i) {
               if ($(i).attr('readonly')) {
                  return false;
               }
            }
         });
         
         var dob_json = <?php echo $dob_json;?>;
         if(Object.keys(dob_json).length>0){
            var relation = parseInt(relation);
            $('#' + formID).closest('form').find('.insured_member_dob').val('')
            $('#' + formID).closest('form').find('.insured_member_dob').datepicker('option', 'minDate', new Date(dob_json[relation]['min_date']));
            $('#' + formID).closest('form').find('.insured_member_dob').datepicker('option', 'maxDate', new Date(dob_json[relation]['max_date']));
         }
            

         

      });

      $('.insured_member_dob').datepicker({
         changeMonth: true,
         changeYear: true,
         maxDate: new Date(),
         dateFormat: 'dd-mm-yy',
         yearRange: "-100:" + new Date('Y'),
         onSelect: function(dateText) {

            if ($(this).closest('form').find('select[name="member_type_id"]').val() == 2) {

               //checkRelation(dateText);
            }
         },
         beforeShow: function(i) {
            if ($(i).attr('readonly')) {
               return false;
            }
         }
      });

      member_relation = $('#accordion480' + insuredFormCount + ' #tab-<?php echo $coapplicant_tab_id ?? '' ?>1 select[name="member_type_id"] option:selected').val();
      dob = $('#accordion480' + insuredFormCount + ' #tab-<?php echo $coapplicant_tab_id ?? '' ?>1 input[name="self_dob"]').val();

      if (member_relation == 1) {

         $('#accordion480' + insuredFormCount + ' ' + self_dob_selector).datepicker('setDate', dob);
      } else {

         $('#accordion480' + insuredFormCount + ' ' + self_dob_selector).datepicker('setDate', "");
      }
   });

   function showError(selector, msg) {

      $(selector).parent().append("<label class='error'>" + msg + "</label>");
   }

   function removeError(selector) {

      $(selector).parent().find("label.error").remove();
   }
</script>