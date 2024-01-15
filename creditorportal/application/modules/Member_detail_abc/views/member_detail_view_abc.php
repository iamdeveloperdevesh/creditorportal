<?php

error_reporting('0');
$result = json_decode($result, true);

$totalfamily_construct = $result['family_construct'];

$self_details = $result['self_details'];


$nominee_relation=json_decode($nominee_relation,true);

$nominee_details_review_page=$review_page_details['nominee_details'];
$member_proposer_review_page=$review_page_details['proposal_member'];
// echo "<pre>";
// print_r($nominee_details_review_page);
// print_r($member_proposer_review_page);
// exit;
?>
<style>
  #entered_captcha_sendotp::-webkit-input-placeholder,
  #entered_captcha_resendotp::-webkit-input-placeholder {
    font-size: small;
  }
</style>
<div class="header-pre sticky-pre" id="myHeader-pre">
  <span class="premium-top dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Premium : <span class="total_premium" id="total_premium"> </span> <i class="fa fa-angle-down down-pre"></i></span>
  <div id="premium_calculations_data" class="dropdown-menu drop_prem" x-placement="bottom-start" style="position: absolute; transform: translate3d(15px, 44px, 0px); top: 0px; left: 0px; will-change: transform;overflow-y: scroll;height: 400px;">
    <!-- <p class="text-cover"><img src="/public/assets/abc/images/heartsecure.png" width="25"> <span class="cover-lbl">Heart Secure</span></p>
                <p class="cover-premium"> Premium <span class="amt-drop"> 7650 <span class="incl">(incl gst)</span></span></p> -->
  </div>
</div>
  <input type="hidden" name="emp_id" class="emp_id" value="<?php echo $emp_id; ?>">
  <input type="hidden" name="show_member_tab" class="show_member_tab" value="<?php echo $show_member_tab; ?>">
  <!-- header area end -->
  <!-- page title area end -->
  <div class="main-content-inner" style="min-height: calc(96vh - 100px);">
    <div class="container mt-2 mb-2">
      <div class="row mb-4">
        <div class="col-lg-7 offset-lg-1 col-md-12 mt-2">
          <div class="card login-card card-z">
            <div class="card-body">
              <div class="container-step display-short-none">
                <ul class="progressbar-step">
                  <li class="tablinks-sec memSec1 active" id="memSec1" onclick="progressline(event, 'mem-del-link')"></li>
                  <li class="tablinks-sec memSec2" id="memSec2"></li><!-- onclick="progressline(event, 'nom-del-link')" -->
                  <li class="tablinks-sec memSec3" id="memSec3"></li><!-- onclick="progressline(event, 'review-del-link')" -->
                </ul>
              </div>
              <div id="mem-del-link" class="progress-sec" style="display: none;">
                <div class="head-tittle">
                  Member Details
                </div>
                <div class="border-bot2"></div>
                <div class="generate-body">
                  <form id="memberform">
                    <div class="mt-4 mb-5">
                      <div id="accordion4" class="according accordion-s3 border-none">
                        
                        <?php 
                        for ($i = 0; $i <= $totalfamily_construct-1; $i++) {

                          $family_arr=['Self','Spouse','Kid1','Kid2'];

                        ?>

                          <div class="card" id="member_card_for_<?php echo ($i); ?>">

                            <div class="card-header mem-del-line">
                              <a class="card-link txt-member <?php echo ($activeCls == "show") ? '' : 'collapsed'; ?>" data-toggle="collapse" href="#accordion<?php echo $i; ?>"><?php echo $family_arr[$i]; ?></a>
                            </div>

                            
                            <div id="accordion<?php echo $i; ?>" class="collapse <?php echo $activeCls; ?>" data-parent="#accordion<?php echo $i; ?>">
                              <div class="card-body border-none body-mem">
                                <div class="mt-2 mb-2">
                                  <div class="row">
                                    <div class="col-md-5">
                                      <div class="form-group">
                                        <label for="example-text-input" class="col-form-label">Full Name<sup><i class="fa fa-asterisk"></i></sup></label>
                                        <input class="form-control" name="full_name[]" id="<?php echo $family_arr[$i] . '_full_name'; ?>" value="<?php echo  $policy_name; ?>" type="text" placeholder="Enter Full Name" id="example-text-input" <?php echo $readonly; ?>>
                                      </div>
                                    </div>
                                    <div class="col-md-5">
                                      <div class="form-group">
                                        <label for="example-date-input" class="col-form-label">Date of Birth<sup><i class="fa fa-asterisk"></i></sup></label>
                                        <input class="form-control hasDatepicker1" name="dob[]" id="<?php echo $family_arr[$i] . '_dob'; ?>" autocomplete="off"  type="text" data-date-format="dd/mm/yyyy" placeholder="dd-mm-yyyy">
                                      </div>
                                    </div>
                                    <div class="col-md-12">
                                      <label class="col-form-label">Gender<sup><i class="fa fa-asterisk"></i></sup></label>
                                      
                                      <?php if ($family_arr[$i] == "Self") { ?>
                                        <div class="row">
                                          <div class="col-lg-2 col-md-3 col-6">
                                            <div class="custom-control custom-radio">
                                              <input type="radio" name="gender[<?php echo $i; ?>]" value="male" class="custom-control-input" id="<?php echo $family_arr[$i]; ?>customRadio1" />
                                              <label class="custom-control-label" for="<?php echo $family_arr[$i]; ?>customRadio1"><span>Male</span></label>
                                            </div>
                                          </div>
                                          <div class="col-lg-2 col-md-3 col-6">
                                            <div class="custom-control custom-radio">
                                              <input type="radio" name="gender[<?php echo $i; ?>]" value="female" class="custom-control-input" id="<?php echo $family_arr[$i]; ?>customRadio2" />
                                              <label class="custom-control-label" for="<?php echo $family_arr[$i]; ?>customRadio2"><span>Female</span></label>
                                            </div>
                                          </div>
                                        </div>
                                        <input type="hidden" name="is_adult[]" value="Y" />
                                        <input type="hidden" name="relation[]" value="1" />
                                      <?php } else if ($family_arr[$i] == "Spouse") { ?>
                                        <div class="row">
                                          <div class="col-lg-2 col-md-3 col-6">
                                            <div class="custom-control custom-radio">

                                              <input type="radio" name="gender[<?php echo $i; ?>]" value="male" class="custom-control-input" id="<?php echo $family_arr[$i]; ?>customRadio1" />                                              
                                              <label class="custom-control-label" for="<?php echo $family_arr[$i]; ?>customRadio1"><span>Male</span></label>

                                            </div>
                                          </div>
                                          <div class="col-lg-2 col-md-3 col-6">
                                            <div class="custom-control custom-radio">

                                              <input type="radio" name="gender[<?php echo $i; ?>]" value="female" class="custom-control-input" id="<?php echo $family_arr[$i]; ?>customRadio2">
                                              <label class="custom-control-label" for="<?php echo $family_arr[$i]; ?>customRadio2"><span>Female</span></label>

                                            </div>
                                          </div>
                                        </div>
                                        <input type="hidden" name="is_adult[]" value="Y" />
                                        <input type="hidden" name="relation[]" value="2" />
                                      <?php } else { ?>
                                        
                                        <div class="row">
                                          <div class="col-lg-2 col-md-3 col-6">
                                            <div class="custom-control custom-radio">
                                              <input type="radio" name="gender[<?php echo $i; ?>]" value="male" class="custom-control-input" id="<?php echo $family_arr[$i]; ?>customRadio1">
                                              <label class="custom-control-label" for="<?php echo $family_arr[$i]; ?>customRadio1"><span>Male</span></label>
                                            </div>
                                          </div>
                                          <div class="col-lg-2 col-md-3 col-6">
                                            <div class="custom-control custom-radio">
                                              <input type="radio" name="gender[<?php echo $i; ?>]" value="female" class="custom-control-input" id="<?php echo $family_arr[$i]; ?>customRadio2">
                                              <label class="custom-control-label" for="<?php echo $family_arr[$i]; ?>customRadio2"><span>Female</span></label>
                                            </div>
                                          </div>
                                          <input type="hidden" name="is_adult[]" value="N" />
                                          <input type="hidden" name="relation[]" value="" />
                                        </div>
                                      <?php } ?>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <input type="hidden" name="member_id[]" id="member_id<?php echo $i; ?>" />
                        <?php } ?>

                      </div>

                    </div>
                </div>
                <div class="member-del text-center">

                  <a href="<?php echo base_url('/member_proposer_detail'); ?>" class="back-btn-mem">Back</a>
                  <button class="btn save-proceed-mem membSaveBtn">Save & Proceed</button>

                </div>
                </form>
              </div>

              <div id="nom-del-link" class="progress-sec" style="display: none;">
                <form id="nomineeform">
                  <input type="hidden" name="source" value="customer" />
                  <div class="head-tittle">
                    <div class="row">
                      <div class="col-md-6 col-8">
                        Nominee Details
                      </div>
                      <div class="col-md-6 col-4 text-right">
                        <span style="cursor: pointer;" onClick="return clearNomineeForm(event);">Clear</span>
                      </div>
                    </div>
                  </div>
                  <div class="border-bot2"></div>
                  <div class="generate-body">
                    <div class="mt-4 mb-5">
                      <div class="row" id="nomineeFields">
                        <div class="col-md-5">
                          <div class="form-group mar-bottom">
                            <label for="example-text-input" class="col-form-label">Nominee with Relation<sup><i class="fa fa-asterisk"></i></sup></label>
                            <select class="form-control nominee_relation" name="nominee_relation" id="nominee_relation" style="padding: 5.72px 10.8px;">
                              <option value=''>Select Relation</option>
                              <?php foreach ($nominee_relation['data'] as $key => $val) { ?>
                                <option data-id="<?php echo $val['id']; ?>" value='<?php echo $val['id']; ?>'><?php echo $val['name']; ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-5">
                          <div class="form-group mar-bottom">
                            <label for="example-text-input" class="col-form-label">Nominee Name<sup><i class="fa fa-asterisk"></i></sup></label>
                            <input class="form-control" type="text" name="nominee_full_name" placeholder="Enter Full Name" id="nominee_full_name">
                            <input type="hidden" name="source" value="customer">
                          </div>
                        </div>

                        <div class="col-md-5">
                          <div class="form-group mar-bottom">
                            <label for="example-text-input" class="col-form-label">Nominee Date of Birth<sup><i class="fa fa-asterisk"></i></sup></label>
                            <input class="form-control" name="nominee_dob" type="text" autocomplete="__away" placeholder="dd-mm-yyyy" id="nominee_dob">
                            <p class="span-msg d-none">If nominee updated is a minor please change the same to an adult.</p>
                          </div>
                        </div>
                        <div class="col-md-5">
                          <div class="form-group mar-bottom">

                            <label for="example-text-input" class="col-form-label">Nominee Mobile Number
                              
                            </label>

                            <input class="form-control nominee_contact" data-product="<?php echo ($this->product_id); ?>" name="nominee_contact" maxlength="10" type="text"  placeholder="Enter Mobile Number" id="nominee_contact">

                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="member-del text-center">
                    <a class="back-btn-mem" onclick="progressline(event, 'mem-del-link')">Back</a>
                    <button class="btn save-proceed-mem BtnNominee">Save & Proceed</button>
                  </div>
                </form>
              </div>
              <!-- <div id="review-del-link" class="progress-sec" style="display: none;"> -->
              <div id="review-del-link" class="progress-sec">

                <div class="head-tittle">
                  Review Application
                </div>
                <div class="border-bot2"></div>
                <div class="generate-body">
                  <div class="padding-bot">
                    <div id="accordion3" class="according accordion-s3 border-none">
                      <div class="card">
                        <div class="card-header mem-del-line">
                          <a class="card-link txt-member" data-toggle="collapse" href="#accordion51">
                            <div class="row">
                              <div class="col-md-10 col-8">Product Details</div>
                              <div class="col-md-2 col-4 edit-review">
                                  <i class="fa fa-pencil"></i>
                              </div>
                            </div>
                          </a>
                        </div>
                        <div id="accordion51" class="collapse show memCardShow" data-parent="#accordion3">
                          <div class="card-body border-none body-mem">
                            <div class="mt-2 mb-2">
                              <div class="row mt-3">
                                <div class="col-md-6 col-lg-6 col-sm-6">
                                  <div class="card-product-del">
                                    <div class="card">
                                      <div class="card-body border-none ">
                                        <div class="head-del">
                                          <span><img class="del-ht-img" src="/public/assets/abc/images/supertopup.png" width="40"></span>
                                          <span class="del-ht">Heart Secure</span>
                                          <span class="fl-right" data-toggle="modal" data-target="#editdetails-float"> <i class="fa fa-pencil"></i></span>
                                          <div class="border-bot3"></div>
                                          <div class="body-product-del mt-4 mb-3">
                                            <span class="mem-del"> Self <span>+</span> Spouse <span> + </span> Kid 1</span>
                                            <p class="sum-del mt-4">
                                              <span class="pre-col"> Premium <span class="amt-del">5000</span></span>
                                              <br>
                                              <span> Sum Insured <span class="amt-del">5 Lakh</span></span>
                                            </p>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-header mem-del-line">
                          <a class="collapsed card-link txt-member" data-toggle="collapse" href="#accordion52">
                            <div class="row">
                              <div class="col-md-10 col-8">Proposer Details</div>
                              <div class="col-md-2 col-4 edit-review">
                                  <i class="fa fa-pencil" onClick="window.location='/member_proposer_detail'"></i>
                              </div>
                            </div>
                          </a>
                        </div>
                        <div id="accordion52" class="collapse" data-parent="#accordion3">
                          <div class="card-body border-none body-mem">
                            
                            <div class="mt-2 mb-2">
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="row mem-content">
                                    <div class="col-md-6 mb-2">
                                      <div class="row">
                                        <div class="col-md-4 col-4">
                                          <span class="text_12"> Salutation </span>
                                        </div>
                                        <div class="col-md-2 col-1">:</div>
                                        <div class="col-md-5 col-6 pad_zero"><span class="text_11"> <?php echo ($self_details['salutation'] != '') ? $self_details['salutation'] : ''; ?> </span>
                                        </div>0
                                      </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                      <div class="row">
                                        <div class="col-md-4 col-4">
                                          <span class="text_12"> First Name </span>
                                        </div>
                                        <div class="col-md-2 col-1">:</div>
                                        <div class="col-md-5 col-6 pad_zero">
                                          <span class="text_11"><?php echo ($self_details['first_name'] != '') ? $self_details['first_name'] : ''; ?></span>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                      <div class="row">
                                        <div class="col-md-4 col-4">
                                          <span class="text_12"> Last Name </span>
                                        </div>
                                        <div class="col-md-2 col-1">:</div>
                                        <div class="col-md-5 col-6 pad_zero">
                                          <span class="text_11"><?php echo ($self_details['last_name'] != '') ? $self_details['last_name'] : ''; ?></span>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                      <div class="row">
                                        <div class="col-md-4 col-4">
                                          <span class="text_12"> Gender </span>
                                        </div>
                                        <div class="col-md-2 col-1">:</div>
                                        <div class="col-md-5 col-6 pad_zero">
                                          <span class="text_11"><?php echo ($self_details['gender'] != '') ? $self_details['gender'] : ''; ?></span>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                      <div class="row">
                                        <div class="col-md-4 col-4">
                                          <span class="text_12"> Date Of Birth </span>
                                        </div>
                                        <div class="col-md-2 col-1">:</div>
                                        <div class="col-md-5 col-6 pad_zero">
                                          <span class="text_11"><?php echo ($self_details['dob'] != '') ? $self_details['dob'] : ''; ?></span>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                      <div class="row">
                                        <div class="col-md-4 col-4">
                                          <span class="text_12"> Mobile </span>
                                        </div>
                                        <div class="col-md-2 col-1">:</div>
                                        <div class="col-md-5 col-6 pad_zero">
                                          <span class="text_11"><?php echo ($self_details['mobile_no'] != '') ? $self_details['mobile_no'] : ''; ?></span>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                      <div class="row">
                                        <div class="col-md-4 col-4">
                                          <span class="text_12"> Email </span>
                                        </div>
                                        <div class="col-md-2 col-1">:</div>
                                        <div class="col-md-5 col-6 pad_zero">
                                          <span class="text_13"><?php echo ($self_details['email_id'] != '') ? $self_details['email_id'] : ''; ?></span>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                      <div class="row">
                                        <div class="col-md-2 col-4">
                                          <span class="text_12"> Address </span>
                                        </div>
                                        <div class="col-md-1 col-1 add-dot">:</div>
                                        <div class="col-md-9 col-6 pad_zero">
                                          <span class="text_11"><?php echo ($self_details['address_line1'] != '') ? $self_details['address_line1'] : ''; ?></span>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                      <div class="row">
                                        <div class="col-md-4 col-4">
                                          <span class="text_12"> Pincode </span>
                                        </div>
                                        <div class="col-md-2 col-1">:</div>
                                        <div class="col-md-5 col-6 pad_zero">
                                          <span class="text_11"><?php echo ($self_details['pincode'] != '') ? $self_details['pincode'] : ''; ?></span>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                      <div class="row">
                                        <div class="col-md-4 col-4">
                                          <span class="text_12"> State </span>
                                        </div>
                                        <div class="col-md-2 col-1">:</div>
                                        <div class="col-md-5 col-6 pad_zero">
                                          <span class="text_11"><?php echo ($self_details['state'] != '') ? $self_details['state'] : ''; ?></span>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                      <div class="row">
                                        <div class="col-md-4 col-4">
                                          <span class="text_12"> City </span>
                                        </div>
                                        <div class="col-md-2 col-1">:</div>
                                        <div class="col-md-5 col-6 pad_zero">
                                          <span class="text_11"><?php echo ($self_details['city'] != '') ? $self_details['city'] : ''; ?></span>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-header mem-del-line">
                          <a class="collapsed card-link txt-member" data-toggle="collapse" href="#accordion53">
                            <div class="row">
                              <div class="col-md-10 col-8">Member Details</div>
                              <div class="col-md-2 col-4 edit-review">
                                  <i class="fa fa-pencil" onclick="progressline(event, 'mem-del-link')"></i>
                              </div>
                            </div>
                          </a>
                        </div>
                        <div id="accordion53" class="collapse" data-parent="#accordion3">
                          <div class="card-body border-none body-mem">
                            <div class="mt-2 mb-2">
                              <div class="row memDetailsDiv">
                                <?php foreach ($member_proposer_review_page as $key => $val) {
                                ?>
                                  <div class="col-md-12 mt-2">
                                    <p class="mem-title mb-1"><?php echo $val['member_type']; ?></p>
                                    <div class="row mem-content">
                                      <div class="col-md-6 mb-2">
                                        <div class="row">
                                          <div class="col-md-4 col-4">
                                            <span class="text_12"> Full Name </span>
                                          </div>
                                          <div class="col-md-2 col-1">:</div>
                                          <div class="col-md-5 col-6 pad_zero"><span class="text_11"> <?php echo $val['policy_member_first_name']?$val['policy_member_first_name'].$val['policy_member_last_name'] : '';  ?> </span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-6 mb-2">
                                        <div class="row">
                                          <div class="col-md-4 col-4">
                                            <span class="text_12"> Relation </span>
                                          </div>
                                          <div class="col-md-2 col-1">:</div>
                                          <div class="col-md-5 col-6 pad_zero">
                                            <span class="text_11"><?php echo $val['member_type'] ? $val['member_type'] : '';  ?></span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-6 mb-2">
                                        <div class="row">
                                          <div class="col-md-4 col-4">
                                            <span class="text_12"> Date of Birth </span>
                                          </div>
                                          <div class="col-md-2 col-1">:</div>
                                          <div class="col-md-5 col-6 pad_zero">
                                            <span class="text_11"><?php echo $val['policy_member_dob'] != '' ? date('d-m-Y',strtotime($val['policy_member_dob'])) : '';  ?></span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-6 mb-2">
                                        <div class="row">
                                          <div class="col-md-4 col-4">
                                            <span class="text_12"> Gender </span>
                                          </div>
                                          <div class="col-md-2 col-1">:</div>
                                          <div class="col-md-5 col-6 pad_zero">
                                            <span class="text_11"><?php echo $val['policy_member_gender'] != '' ? $val['policy_member_gender'] : '';  ?></span>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                <?php } ?>


                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card" <?php if ($this->product_id == 'ABC') {
                                          echo ("style='display: none;'");
                                        } ?>>
                        <div class="card-header mem-del-line">
                          <a class="collapsed card-link txt-member" data-toggle="collapse" href="#accordion54">
                            <div class="row">
                              <div class="col-md-10 col-8">Nominee Details</div>
                              <div class="col-md-2 col-4 edit-review"><i class="fa fa-pencil" onclick="progressline(event, 'nom-del-link')"></i></div>
                            </div>
                          </a>
                        </div>
                        <div id="accordion54" class="collapse" data-parent="#accordion3">
                          <div class="card-body border-none body-mem">
                            <div class="mt-2 mb-2">
                              <div class="row mem-content">
                                <div class="col-md-12 mb-2">
                                  <div class="row">
                                    <div class="col-md-3 col-5">
                                      <span class="text_12"> Full Name </span>
                                    </div>
                                    <div class="col-md-2 col-1">:</div>
                                    <div class="col-md-6 col-5 pad_zero"><span class="rev_nominee_name"><?=$nominee_details_review_page['nominee_first_name'].$nominee_details_review_page['nominee_last_name']; ?></span>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-12 mb-2">
                                  <div class="row">
                                    <div class="col-md-3 col-5">
                                      <span class="text_12"> Date of Birth </span>
                                    </div>
                                    <div class="col-md-2 col-1">:</div>
                                    <div class="col-md-6 col-5 pad_zero"><span class="rev_nominee_dob"><?=date('d-m-Y',strtotime($nominee_details_review_page['nominee_dob']));?></span>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-12 mb-2">
                                  <div class="row">
                                    <div class="col-md-3 col-5">
                                      <span class="text_12"> Relation with Nominee </span>
                                    </div>
                                    <div class="col-md-2 col-1">:</div>
                                    <div class="col-md-6 col-5 pad_zero"><span class="rev_nominee_relation"><?=$nominee_details_review_page['name'];?></span>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-12 mb-2">
                                  <div class="row">
                                    <div class="col-md-3 col-5">
                                      <span class="text_12"> Nominee Mobile Number </span>
                                    </div>
                                    <div class="col-md-2 col-1">:</div>
                                    <div class="col-md-6 col-5 pad_zero"><span class="rev_nominee_contact"><?=$nominee_details_review_page['nominee_contact'];?></span>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <?php if ($this->product_id == 'ABC') { ?>
                    <!--- Health Decalartion -->
                    <div class="head-tittle">
                      Good Health Declaration
                    </div>
                    <div class="col-md-12 mt-2" style="padding-left:0px; ">
                      <div class="form-group">
                        <span class="mt-3" data-toggle="modal" data-target="#" style="border: none; background: #fff;">
                          <div class="custom-control-inline"> <span class="disc_final" style=" font-size: 12px; line-height: 15px;">
                              <div class="col-md-12" style="padding-left: 0px;">
                                <b>1.</b> Are you / any proposed member suffering from or have been diagnosed with advised taken treatment or observation is suggested or undergone any investigation or consulted a doctor or undergone or advised surgery for any one or more from the following?<br><br>
                                <b>a.</b> High Blood Pressure, Heart Attack or any other Heart Disease, abnormal lipid levels<br>
                                <b>b.</b> Stroke, Paralysis in any form, or any other Cerebrovascular Disease<br>
                                <b>c.</b> Diabetes or thyroid/parathyroid or any other Endocrinal Disease, Any Kidney Disease<br>
                                <b>d.</b> Acute / Chronic Liver (Failure/ Disease), Cirrhosis of Liver, Alcoholic liver disease; any pancreatic disease<br>
                                <b>e.</b> Any Lung Disease (e.g. Chronic Obstructive Pulmonary Diseases, Parenchymal lung Disease, Pulmonary Embolism etc.)<br>
                                <b>f.</b> Blood Disorders, Gastro-Intestinal Diseases, Ulcer or any other disorder of the bones, spine or muscle;<br>
                                <b>g.</b> Any Cancer or Cancerous growth<br>
                                <b>h.</b> Any Mental or Psychiatric condition, any Genetic Disease, autoimmune or any disease related to central nervous system (disease related to brain); Congenital conditions<br>
                                <b>i.</b> HIV / AIDS or AIDS related complications<br>
                                <b>j.</b> Covid positive in last 3 months<br>
                                <b>k.</b> Any h/o sudden loss of weight in last 1 yr. I do not have any habits e.g. smoking/ 15 cigarettes on an average/day, 350ml alcohol per week.
                                <br><br>
                                <div class="row mb-4">
                                  <div class="col-md-3 col-6">
                                    <div class="custom-control custom-radio"> <input type="radio" id="dc_type1" name="dc_type" class="custom-control-input payment_type" value="1" checked> <label class="custom-control-label" for="dc_type1">Yes</label> </div>
                                  </div>
                                  <div class="col-md-3 col-6">
                                    <div class="custom-control custom-radio" style=" "> <input type="radio" id="dc_type2" name="dc_type" class="custom-control-input payment_type" value="0"> <label class="custom-control-label" for="dc_type2">No</label> </div>
                                  </div>
                                </div>
                                <b>2.</b> Any other pre-existing disease? <textarea class="col-md-12 form-control mt-1" style="padding-left: 4px; padding-top: 4px;" name="response" id="ghdResponse"></textarea>
                              </div>
                            </span>
                          </div>
                        </span>
                      </div>
                    </div>
                    <!-- <div class="row mb-4">
                       <div class="col-md-3 col-6"> <div class="custom-control custom-radio"> <input type="radio" id="dc_type1" name="dc_type" class="custom-control-input payment_type" value="1" checked> <label class="custom-control-label" for="dc_type1">Yes</label> </div> </div>
                        <div class="col-md-3 col-6"> <div class="custom-control custom-radio" style=" "> <input type="radio" id="dc_type2" name="dc_type" class="custom-control-input payment_type" value="0" > <label class="custom-control-label" for="dc_type2">No</label> </div> </div>
                         </div> -->

                  <?php } else if ($this->product_id == 'MUTHOOT') { ?>
                    <div class="row ml-2 mb-4">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="auto_renewal_check" name="auto_renewal_check" class="custom-control-input payment_type" value="1" checked> <label class="custom-control-label" for="auto_renewal_check"> Auto Renewal </label>
                      </div>
                    </div>
                  <?php } else if ($this->product_id == 'HERO_FINCORP') { ?>
                    <div class="row ml-2 mb-4">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="go_green_check" name="go_green_check" class="custom-control-input" value="1" checked> <label class="custom-control-label" for="go_green_check"> I would like to support the<span style="color:#40ab2b;"> 'Go Green' </span> initiative of Aditya Birla Health Insurance Co. Ltd. where I can protect my environment by saving paper. I hereby authorize Aditya Birla Health Insurance Co. to send all my policy and service related communication to the email id as mentioned in this application. </label>
                      </div>
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="t_and_c_check" name="t_and_c_check" class="custom-control-input" value="1"> <label class="custom-control-label" for="t_and_c_check"> I hereby confirm that the feature of the Group Activ Health insurance product and plan selected by me and all other documents incidental to health insurance have been explained to me & I have understood the same. </label>
                      </div>
                    </div>
                  <?php } else if ($this->product_id == 'ABML') { ?>
                    <!--- Health Decalartion -->
                    <div class="head-tittle">
                      Good Health Declaration
                    </div>
                    <div class="col-md-12 mt-2" style="padding-left:0px; ">
                      <div class="form-group">
                        <span class="mt-3" data-toggle="modal" data-target="#" style="border: none; background: #fff;">
                          <div class="custom-control-inline"> <span class="disc_final" style=" font-size: 12px; line-height: 15px;">
                              <div class="col-md-12" style="padding-left: 0px;">
                                <b>1.</b> Are you / any proposed member suffering from or have been diagnosed with advised taken treatment or observation is suggested or undergone any investigation or consulted a doctor or undergone or advised surgery for any one or more from the following?<br><br>

                                <table class="table ghd-table">
                                  <tbody>
                                    <tr>
                                      <th scope="row">a.</th>
                                      <td>High Blood Pressure, Heart Attack or any other Heart Disease, abnormal lipid levels</td>
                                      <td>
                                        <div class="row ghd-radio-set">
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio">
                                              <input type="radio" id="ghd_check_a_y" name="ghd_check_a" class="custom-control-input radio-yes">
                                              <label class="custom-control-label" for="ghd_check_a_y">Yes</label>
                                            </div>
                                          </div>
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio" style=" ">
                                              <input type="radio" name="ghd_check_a" id="ghd_check_a_n" class="custom-control-input radio-no">
                                              <label class="custom-control-label" for="ghd_check_a_n">No</label>
                                            </div>
                                          </div>
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <th scope="row">b.</th>
                                      <td>Stroke, Paralysis in any form, or any other Cerebrovascular Disease</td>
                                      <td>
                                        <div class="row ghd-radio-set">
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio">
                                              <input type="radio" id="ghd_check_b_y" name="ghd_check_b" class="custom-control-input radio-yes">
                                              <label class="custom-control-label" for="ghd_check_b_y">Yes</label>
                                            </div>
                                          </div>
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio" style=" ">
                                              <input type="radio" name="ghd_check_b" id="ghd_check_b_n" class="custom-control-input radio-no">
                                              <label class="custom-control-label" for="ghd_check_b_n">No</label>
                                            </div>
                                          </div>
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <th scope="row">c.</th>
                                      <td>Diabetes or thyroid/parathyroid or any other Endocrinal Disease, Any Kidney Disease</td>
                                      <td>
                                        <div class="row ghd-radio-set">
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio">
                                              <input type="radio" id="ghd_check_c_y" name="ghd_check_c" class="custom-control-input radio-yes">
                                              <label class="custom-control-label" for="ghd_check_c_y">Yes</label>
                                            </div>
                                          </div>
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio" style=" ">
                                              <input type="radio" name="ghd_check_c" id="ghd_check_c_n" class="custom-control-input radio-no">
                                              <label class="custom-control-label" for="ghd_check_c_n">No</label>
                                            </div>
                                          </div>
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <th scope="row">d.</th>
                                      <td>Acute / Chronic Liver (Failure/ Disease), Cirrhosis of Liver, Alcoholic liver disease any pancreatic disease</td>
                                      <td>
                                        <div class="row ghd-radio-set">
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio">
                                              <input type="radio" id="ghd_check_d_y" name="ghd_check_d" class="custom-control-input radio-yes">
                                              <label class="custom-control-label" for="ghd_check_d_y">Yes</label>
                                            </div>
                                          </div>
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio" style=" ">
                                              <input type="radio" name="ghd_check_d" id="ghd_check_d_n" class="custom-control-input radio-no">
                                              <label class="custom-control-label" for="ghd_check_d_n">No</label>
                                            </div>
                                          </div>
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <th scope="row">e.</th>
                                      <td>Any Lung Disease (e.g. Chronic Obstructive Pulmonary Diseases, Parenchymal lung Disease, Pulmonary Embolism etc.</td>
                                      <td>
                                        <div class="row ghd-radio-set">
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio">
                                              <input type="radio" id="ghd_check_e_y" name="ghd_check_e" class="custom-control-input radio-yes">
                                              <label class="custom-control-label" for="ghd_check_e_y">Yes</label>
                                            </div>
                                          </div>
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio" style=" ">
                                              <input type="radio" name="ghd_check_e" id="ghd_check_e_n" class="custom-control-input radio-no">
                                              <label class="custom-control-label" for="ghd_check_e_n">No</label>
                                            </div>
                                          </div>
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <th scope="row">f.</th>
                                      <td>Blood Disorders, Gastro-Intestinal Diseases, Ulcer or any other disorder of the bones, spine or muscle</td>
                                      <td>
                                        <div class="row ghd-radio-set">
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio">
                                              <input type="radio" id="ghd_check_f_y" name="ghd_check_f" class="custom-control-input radio-yes">
                                              <label class="custom-control-label" for="ghd_check_f_y">Yes</label>
                                            </div>
                                          </div>
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio" style=" ">
                                              <input type="radio" name="ghd_check_f" id="ghd_check_f_n" class="custom-control-input radio-no">
                                              <label class="custom-control-label" for="ghd_check_f_n">No</label>
                                            </div>
                                          </div>
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <th scope="row">g.</th>
                                      <td>Any Cancer or Cancerous growth</td>
                                      <td>
                                        <div class="row ghd-radio-set">
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio">
                                              <input type="radio" id="ghd_check_g_y" name="ghd_check_g" class="custom-control-input radio-yes">
                                              <label class="custom-control-label" for="ghd_check_g_y">Yes</label>
                                            </div>
                                          </div>
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio" style=" ">
                                              <input type="radio" name="ghd_check_g" id="ghd_check_g_n" class="custom-control-input radio-no">
                                              <label class="custom-control-label" for="ghd_check_g_n">No</label>
                                            </div>
                                          </div>
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <th scope="row">h.</th>
                                      <td>Any Mental or Psychiatric condition, any Genetic Disease, autoimmune or any disease related to central nervous system (disease related to brain); Congenital conditions</td>
                                      <td>
                                        <div class="row ghd-radio-set">
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio">
                                              <input type="radio" id="ghd_check_h_y" name="ghd_check_h" class="custom-control-input radio-yes">
                                              <label class="custom-control-label" for="ghd_check_h_y">Yes</label>
                                            </div>
                                          </div>
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio" style=" ">
                                              <input type="radio" name="ghd_check_h" id="ghd_check_h_n" class="custom-control-input radio-no">
                                              <label class="custom-control-label" for="ghd_check_h_n">No</label>
                                            </div>
                                          </div>
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <th scope="row">i.</th>
                                      <td>HIV / AIDS or AIDS related complications</td>
                                      <td>
                                        <div class="row ghd-radio-set">
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio">
                                              <input type="radio" id="ghd_check_i_y" name="ghd_check_i" class="custom-control-input radio-yes">
                                              <label class="custom-control-label" for="ghd_check_i_y">Yes</label>
                                            </div>
                                          </div>
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio" style=" ">
                                              <input type="radio" name="ghd_check_i" id="ghd_check_i_n" class="custom-control-input radio-no">
                                              <label class="custom-control-label" for="ghd_check_i_n">No</label>
                                            </div>
                                          </div>
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <th scope="row">j.</th>
                                      <td>Covid positive in last 3 months</td>
                                      <td>
                                        <div class="row ghd-radio-set">
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio">
                                              <input type="radio" id="ghd_check_j_y" name="ghd_check_j" class="custom-control-input radio-yes">
                                              <label class="custom-control-label" for="ghd_check_j_y">Yes</label>
                                            </div>
                                          </div>
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio" style=" ">
                                              <input type="radio" name="ghd_check_j" id="ghd_check_j_n" class="custom-control-input radio-no">
                                              <label class="custom-control-label" for="ghd_check_j_n">No</label>
                                            </div>
                                          </div>
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <th scope="row">k.</th>
                                      <td>Any h/o sudden loss of weight in last 1 yr.</td>
                                      <td>
                                        <div class="row ghd-radio-set">
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio">
                                              <input type="radio" id="ghd_check_k_y" name="ghd_check_k" class="custom-control-input radio-yes">
                                              <label class="custom-control-label" for="ghd_check_k_y">Yes</label>
                                            </div>
                                          </div>
                                          <div class="col-md-6 col-12">
                                            <div class="custom-control custom-radio" style=" ">
                                              <input type="radio" name="ghd_check_k" id="ghd_check_k_n" class="custom-control-input radio-no">
                                              <label class="custom-control-label" for="ghd_check_k_n">No</label>
                                            </div>
                                          </div>
                                        </div>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                                <b>2.</b> Any other pre-existing disease? <textarea class="col-md-12 form-control mt-1" style="padding-left: 4px; padding-top: 4px;" name="response" id="ghdResponse"></textarea>
                                <br>
                                <div class="row ml-0 mb-4">
                                  <div class="custom-control custom-checkbox">
                                    <input type="checkbox" id="abml_t_c_check" class="custom-control-input" checked> <label style="font-weight: 500; font-size: 12px;" class="custom-control-label mt-2" for="abml_t_c_check"> Policy will be null and void if there is Any discrepancy in above two points. </label>
                                  </div>
                                </div>
                              </div>
                            </span>
                          </div>
                        </span>
                      </div>
                    </div>
                  <?php } ?>
                  <!-- end -->

                </div>

                <div class="generate-footer text-center">
                  <button class="btn back-btn" onclick="progressline(event, 'nom-del-link')">Back</button>

                  <?php if ($this->product_id == 'MUTHOOT' || $this->product_id == 'HERO_FINCORP') { ?>
                    <a href="javascript:void(0);" data-toggle="modal" onClick="return showHealthDeclaration(event);" data-target="" class="buy-now-cta" id="insert_proposal_button">Buy Now <i class="fa fa-long-arrow-right"></i></a>
                  <?php } else if ($this->product_id == 'ABC') { ?>
                    <a href="javascript:void(0);" data-toggle="modal" data-target="" class="buy-now-cta" onClick="return checkValidation(event);" id="insert_proposal_button">Buy Now <i class="fa fa-long-arrow-right"></i></a>
                  <?php } else if ($this->product_id == 'ABML') { ?>
                    <a href="javascript:void(0);" data-toggle="modal" data-target="" class="buy-now-cta" onClick="return checkValidationAbml(event);" id="insert_proposal_button">Buy Now <i class="fa fa-long-arrow-right"></i></a>
                  <?php } ?>

                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 mt-2 display-none-sm">
          <div class="card login-card" style="background:#E5E0D6; border-radius: 30px;padding-bottom: 0px;">
            <div class="card-body" style="background: url(/public/assets/abc/images/family2.png) no-repeat bottom;  background-size: 100%; 
                  height: 560px; border-radius: 30px;">
              <?php if ($this->product_id == 'MUTHOOT') { ?>
                <p class="txt-rght"> Get yourself covered aganist uncertainities of life.</p>
                <div class="text-center"><button class="btn save-proceed-mem  rght-btn">Presenting Muthoot Health Care</button></div>
              <?php } ?>
              <p></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="msg-del" style="display: none; padding-right: 0px !important;">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content login-card">
        <div class="modal-header">
          <h5 class="modal-title head-tittle">
            <!-- <img src="/public/assets/abc/images/supertopup.png" width="30">  --><span>Great Choice</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal"><span class="">×</span></button>
        </div>
        <div class="modal-body">
          <p class="mt-3 mb-3 txt-msg-body">Great choice, however you can enhance your policy by adding a <span id="remaining_products_modal_txt"></span>.</p>
        </div>
        <div class="modal-footer">
          <div class="row">
            <div class="col-md-3 col-2">
              <a id="remaining_product_link" href=""> <button type="button" class="btn btn-submit" id="insert_proposal_button">Add <i class="ti-plus display-none-sm"></i></button> </a>
            </div>
            <div class="col-md-2 col-2"> <a id="createProposalRedirectlink" href=""> <button type="button" class="btn btn-submit" id="insert_proposal_button">Proceed with current selection <i class="ti-check display-none-sm"></i></button> </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- end of Msg -->
  <div class="modal fade" id="good-health-declaration" style="display: none; padding-right: 0px !important;">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
      <div class="modal-content login-card" style="background:#fff;">
        <div class="modal-header" style="border:none;">
          <h5 class="modal-title head-tittle col-md-12 text-center mt-2" style="margin-bottom:0px;color: #000 !important;"><span>Good Health Declaration</span>
            <?php if ($this->product_id == 'MUTHOOT') { ?>
              <p class="text-center mt-2"><img src="/public/assets/abc/images/logo-header1.png" alt="logo" width="150"></p>
            <?php } ?>
          </h5>

        </div>
        <div class="modal-body">
          <?php echo ($mainProductDetail['good_health_declaration']); ?>
          <div class="row mt-3 mb-3">
            <div class="col-md-3 col-3">
              <div class="custom-control custom-radio"> <input type="radio" id="ghd_muthoot1" name="ghd_muthoot" class="custom-control-input payment_type" value="1"> <label class="custom-control-label" for="ghd_muthoot1" style=" font-size: 12px; ">Yes</label> </div>
            </div>
            <div class="col-md-3 col-3">
              <div class="custom-control custom-radio" style=" "> <input type="radio" id="ghd_muthoot2" name="ghd_muthoot" class="custom-control-input payment_type" value="0"> <label class="custom-control-label" for="ghd_muthoot2" style=" font-size: 12px; ">No</label></div>
            </div>
            <div class="row">
              <!-- captcha update - upendra -->
              <div class="col-md-4 text-center mt-1">
                <div class="form-group" style="background: none; border: none;">
                  <span id="captcha_image_span_sendotp">
                    <?php
                    echo $captcha_image['image'];
                    ?>
                  </span>
                  <span style="cursor: pointer;" id="refresh_captcha_sendotp">
                    <i class="fa fa-refresh" aria-hidden="true"></i>
                  </span>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <input class="form-control" type="text" maxlength="10" placeholder="Enter Captcha Text" id="entered_captcha_sendotp" autocomplete="off" required />

                  <span id="captcha_error_sendotp" class="alert alert-danger" style="display: none;"></span>
                </div>
              </div>

              <div class="col-md-4 col-12 text-right">
                <button type="button" class="btn save-proceed-mem" id="sent-otp-btn" data-toggle="modal" data-target="" onClick="return sendOtp(event);">Send OTP</button>
              </div>
            </div>
          </div>
        </div>
        <!--      <div class="modal-footer" style="background:#fff; margin-top:-25%;">
                        <button type="button" class="btn save-proceed-mem" id="sent-otp-btn" data-toggle="modal" data-target="" onClick = "return sendOtp(event);" >Send OTP</button>
                      </div> -->
      </div>
    </div>
  </div>

  <div class="modal fade" id="sendOtp" style="display: none; padding-right: 0px !important;">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content login-card">
        <div class="modal-header" style="border:none;">
          <h5 class="modal-title head-tittle col-md-12 text-center" style="margin-bottom:0px; color:#000 !important;"><span>Enter OTP</span></h5>
        </div>
        <p class="text-center col-md-12 mb-3 txt-otp" style="letter-spacing:1px;line-height: 19px;margin-top: -5%;font-size: 12px;">Your OTP has been send to your registered mobile number.</p>
        <div class="modal-body">
          <!--   <div class="form-group col-md-6">
                        <label class="col-form-label"> Enter OTP </label>
                        <input type="text" name="otp" placeholder="Enter OTP" class="form-control">
                        <span style="display: none;" id="invalidOtpMsg"> Invalid OTP </span>
                       </div> -->

          <div class="row">
            <div class="col-md-2 offset-md-2 col-3">
              <input type="text" autocomplete="off" name="otp[]" class="form-control text-center otp-inp" maxlength="1" id="txt1">
            </div>
            <div class="col-md-2 col-3">
              <input type="text" autocomplete="off" name="otp[]" class="form-control text-center otp-inp" maxlength="1" id="txt2">
            </div>
            <div class="col-md-2 col-3">
              <input type="text" autocomplete="off" name="otp[]" class="form-control text-center otp-inp" maxlength="1" id="txt3">
            </div>
            <div class="col-md-2 col-3">
              <input type="text" autocomplete="off" name="otp[]" class="form-control text-center otp-inp" maxlength="1" id="txt4">
            </div>
          </div>
        </div>
        <div id="timer" class="col-md-12 tm-otp text-center"></div>
        <!-- Captcha -->
        <div class="row" style="display: none;" id="resendOtp">
          <div class="col-md-4 text-center mt-1 offset-md-2">
            <div class="form-group" style="background: none; border: none;">
              <span id="captcha_image_span_resendotp">
                <?php
                echo $captcha_image['image'];
                ?>
              </span>
              <span style="cursor: pointer;" id="refresh_captcha_resendotp">
                <i class="fa fa-refresh" aria-hidden="true"></i>
              </span>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <input class="form-control" type="text" maxlength="10" placeholder="Enter Captcha Text" id="entered_captcha_resendotp" autocomplete="off" required />

              <span id="captcha_error_resendotp" class="alert alert-danger" style="display: none;"></span>
            </div>
          </div>
          <div class="col-md-12 mt-2 text-center resend-txt">
            Haven't received OTP yet? <a href="javascript:void(0)" onClick="return resendOtp(event);">Resend OTP</a>
          </div>
        </div>
        <div class="col-md-12 mt-2  text-center">
          <button type="button" class="btn save-proceed-mem" id="check-otp-btn" onClick="return validateOtp(event);" style="float:none;">OK</button>
        </div>
      </div>
    </div>
  </div>


  <div class="modal fade" id="nomineeMultiple" data-keyboard="false" data-backdrop="static" style="display: none; padding-right: 0px !important;">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content login-card">
        <div class="modal-header">
          <h5 class="modal-title head-tittle"><span>Select nominee </span></h5>
        </div>
        <div class="modal-body" id="nomineeModalContent">
          <!-- <input type="text" name="otp" placeholder="Enter OTP">
                        <span style="display: none;" id="invalidOtpMsg"> Invalid OTP </span> -->
          <!-- <p>Sample data</p> -->

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-submit" onClick="returnSelectedNominee(event)" id="selectNomineeBtn">Submit</button>
        </div>
      </div>
    </div>
  </div>
  <!-- main content area end -->

  <?php


  $js_files = [
    '../assets/abc/abc_js/scripts.js',
    '../assets/abc/js/vendor/jquery.validate.js',
    '../assets/abc/abc_js/abc_member_custom.js',
    '../assets/js_events/other_msg_mem_abc.js',
    '../assets/abc/abc_js/common.js',
    '../assets/abc/js/daterangepicker.min.js',
  ];

  for ($i = 0; $i < count($js_files); $i++) {
    echo "<script src=" . $js_files[$i] . "></script>\n";
  }
  // Globals::setJs(minify_resources($js_files, 'js', 'home_page2'));
  ?>