<?php

error_reporting('0');

header('Content-Type: application/json');
print_r($data);
exit;

?>

<div class="header-pre sticky-pre" id="myHeader-pre">
  <span class="premium-top dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Premium : <span class="total_premium"> 15000 </span> <i class="fa fa-angle-down down-pre"></i></span>
  <div class="dropdown-menu drop_prem" x-placement="bottom-start" style="position: absolute; transform: translate3d(15px, 44px, 0px); top: 0px; left: 0px; will-change: transform;overflow-y: scroll;height: 400px;">
    <p class="text-cover"><img src="/public/assets/abc/images/heartsecure.png" width="25"> <span class="cover-lbl">Heart Secure</span></p>
    <p class="cover-premium"> Premium <span class="amt-drop"> 7650 <span class="incl">(incl gst)</span></span></p>
  </div>
</div>
<!-- header area end -->
<!-- page title area end -->
<div class="main-content-inner" style="min-height: calc(96vh - 100px);">
  <div class="container">
    <div class="row mt-2">
      <div class="col-lg-7 offset-lg-1 col-md-12 mt-2 mb-2 pd8">
        <div class="card login-card mb-3">
          <div class="card-body">
            <div class="generate-head head-tittle">
              <?php if ($this->product_id == 'ABC' || $this->product_id == 'HERO_FINCORP') { ?>
                Generate Your Quote
              <?php } else { ?>
                Know Your Premium
              <?php } ?>
            </div>
            <div class="border-bot2"></div>
            <div class="generate-body">
              <?php if ($this->product_id == 'ABC' || $this->product_id == 'HERO_FINCORP') { ?>
                <!-- <p class="lbl-cover">Select Cover</p> -->
                <p class="lbl-cover"></p>
                <div class="cover-tab">
                  <div class="tab-part">
                    <button class="tablinks-cover active" onclick="covertab1(event, 'fam-float-id')" id="defaultactive">Family Floater</button>
                    <!-- <button class="tablinks-cover" onclick="covertab1(event, 'indi-id')">Individual</button> -->
                    <!-- <button class="tablinks-cover">Individual</button> -->
                  </div>
                </div>
              <?php } ?>
              <div id="fam-float-id" class="tabcontent-cover mb-3" style="display: block;">
                <p class="lbl-cover mt-2">Select Product</p>
                <div class="row family_floater">
                  <?php
                  $i = 1;
                  foreach ($product_details as $key => $value) {
                    if (in_array($value['policy_sub_type_id'], $multiSelectPolicyArr)) {
                      $group = $value['policy_sub_type_id'] . '_' . $value['policy_detail_id'];
                    } else {
                      $group = $value['policy_sub_type_id'];
                    }
                  ?>
                    <div class="se-product col-md-4 col-6 mt-1 col-sm-4">
                      <div class="mt-1">
                        <div class="div-pro-range">
                          <div class="custom-control custom-checkbox range-control check10">
                            <?php
                            $check_heart = '';
                            if (in_array($value['policy_detail_id'], $policy_ids)) {
                              $check_heart = 'checked';
                            }

                            if ($product_name == $value['policy_detail_id']) {
                              $check_heart = 'checked'; //'checked disabled';
                            }

                            ?>
                            <input type="checkbox" class="custom-control-input product_name" common-attr="family_floater" data-policyDetailId="<?php echo $value['policy_detail_id']; ?>" data-name="<?php echo $value['product_name']; ?>" name="family_floater<?php echo $group; ?>" id="customCheck<?php echo $i; ?>" <?php echo $check_heart; ?>>
                            <label class="custom-control-label" for="customCheck<?php echo $i; ?>"><img src="<?php echo $value['product_image']; ?>" width="30"> <br><span><?php echo $value['display_name']; ?></span></label>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php $i++;
                  } ?>
                </div>
                <p class="lbl-cover mt-2 mb-2">Selected Product</p>
                <div class="family_floater_selected_prod">
                  <div class="row mt-3">
                    <div class="col-md-5 col-sm-6 col-12">
                      <div class="card-product-del">
                        <div class="card box-gen ">
                          <div class="card-body">
                            <div class="head-del">
                              <span><img class="del-ht-img" src="/public/assets/abc/images/supertopup.png" width="40"></span>
                              <span class="del-ht">Heart Secure</span>
                              <span class="fl-right" data-toggle="modal" data-target="#editdetails-float"> <i class="fa fa-pencil"></i></span>
                              <div class="border-bot3"></div>
                              <div class="body-product-del mt-4 mb-3">
                                <span class="mem-del"> Self <span>+</span> Spouse <span> + </span> Kid 1</span>
                                <p class="sum-del mt-4">Sum Insured <span class="amt-del">5 Lakh</span></p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div id="indi-id" class="tabcontent-cover mb-3">
                <p class="lbl-cover mt-2">Select Product</p>
                <div class="row">
                  <div class="se-product col-md-3 col-6 mt-1 col-sm-4">
                    <div class="mt-1">
                      <div class="div-pro-range">
                        <div class="custom-control custom-checkbox range-control">
                          <input type="checkbox" class="custom-control-input" id="customCheck2">
                          <label class="custom-control-label" for="customCheck2"><img src="/public/assets/abc/images/heartsecure.png" width="30"> <br><span>Heart Secure</span></label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="se-product col-md-3 col-6 mt-1 col-sm-4">
                    <div class="mt-1">
                      <div class="div-pro-range">
                        <div class="custom-control custom-checkbox range-control">
                          <?php
                          $check_super = '';
                          if ($product_name == 'supertopup') {
                            $check_super = 'checked';
                          } ?>
                          <input type="checkbox" class="custom-control-input" id="customCheck3" <?php echo $check_super; ?>>

                          <label class="custom-control-label" for="customCheck3"><img src="/public/assets/abc/images/supertopup.png" width="30"> <br><span>Super Top Up</span></label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="se-product col-md-3 col-6 mt-1 col-sm-4">
                    <div class="mt-1">
                      <div class="div-pro-range">
                        <div class="custom-control custom-checkbox range-control">
                          <input type="checkbox" class="custom-control-input" id="customCheck4">
                          <label class="custom-control-label" for="customCheck4"><img src="/public/assets/abc/images/cancer.png" width="30"> <br><span>Cancer secure</span></label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="se-product col-md-3 col-6 mt-1 col-sm-4">
                    <div class="mt-1">
                      <div class="div-pro-range">
                        <div class="custom-control custom-checkbox range-control">
                          <input type="checkbox" class="custom-control-input" id="customCheck5">
                          <label class="custom-control-label" for="customCheck5"><img src="/public/assets/abc/images/combo.png" width="30"> <br><span>Combo</span></label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <p class="lbl-cover mt-2">Selected Product</p>
                <div class="row mt-3">
                  <div class="col-md-5 col-sm-6 col-12">
                    <div class="card-product-del">
                      <div class="card box-gen ">
                        <div class="card-body">
                          <div class="head-del">
                            <span><img class="del-ht-img" src="/public/assets/abc/images/supertopup.png" width="40"></span>
                            <span class="del-ht">Heart Secure</span>
                            <span class="fl-right" data-toggle="modal" data-target="#editdetails-indi"> <i class="fa fa-pencil"></i></span>
                            <div class="border-bot3"></div>
                            <div class="body-product-del mt-4 mb-3">
                              <div class="row col-md-12 pro-del-indi">
                                <div class="div mt-2 mb-2">
                                  <span class="mem-del mt-2"><span class="mem-indi"> Self</span> <span class="bor-indi"> <b class="amt-indi">5 Lakh </b></span></span>
                                </div>
                                <div class="div mt-2 mb-2">
                                  <span class="mem-del mt-2"><span class="mem-indi"> Spouse</span> <span class="bor-indi"> <b class="amt-indi">5 Lakh </b></span></span>
                                </div>
                                <div class="div mt-2 mb-2">
                                  <span class="mem-del mt-2"><span class="mem-indi"> Kid 1</span> <span class="bor-indi"> <b class="amt-indi">5 Lakh </b></span></span>
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
            <div class="generate-footer text-center">
              <a href="<?php echo base_url('comprehensive_products'); ?>" class="btn back-btn">Back</a>
              <a href="<?php echo base_url('member_proposer_detail'); ?>" class="btn save-proceed-mem memProposerBtn ClickBtn">Save & Proceed <i class="fa fa-long-arrow-right"></i></a>
              <!--  <span class="buy-now-cta memProposerBtn">Buy Now <i class="fa fa-long-arrow-right"></i></span> -->
            </div>
            <!-- <div class="generate-footer text-center">
                    <a href="<?php echo base_url('comprehensive_product_abc'); ?>" class="btn back-btn">Back</a>
                   <a href="<?php echo base_url('member_proposer_detail'); ?>">  <span class="buy-now-cta">Buy Now <i class="fa fa-long-arrow-right"></i></span></a>
                    </div> -->
          </div>
        </div>
      </div>
      <div class="col-lg-3 mt-2 display-none-sm">
        <div class="card login-card" style="background:#E5E0D6; border-radius: 30px;padding-bottom: 0px;">
          <div class="card-body" style="background: url(/public/assets/abc/images/family2.png) no-repeat bottom;  background-size: 100%; 
                  height: 617px; border-radius: 30px;">
            <?php if ($this->product_id == 'MUTHOOT') { ?>
              <p class="txt-rght"> Get yourself covered aganist uncertainities of life.</p>
              <div class="text-center"><button class="btn save-proceed-mem  rght-btn">Presenting Muthoot Health Care</button></div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- main content area end -->
<!-- Member Details Float -->
<div class="modal fade" id="editdetails-float" style="display: none; padding-right: 16px;">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content login-card">
      <div class="modal-header">
        <h5 class="modal-title head-tittle"><img class="family_float_prod_img" src="/public/assets/abc/images/supertopup.png" width="30"> <span class="family_flot_prod_name">Heart Secure</span></h5>
        <button type="button" class="close" data-dismiss="modal"><span class="">×</span></button>
      </div>
      <div class="modal-body">
        <p class="ml-tittle">Select Family Construct</p>
        <div class="row family_flot_construct">

        </div>
        <p class="ml-tittle mt-3">Select Sum Insured</p>
        <div class="row family_flot_sum_insured">

        </div>

        <?php
        $showMaxAge = 1;
        if ($this->product_id == 'HERO_FINCORP') {
          $showMaxAge = 0;
        }
        ?>

        <p class="ml-tittle mt-3 age_title" style="<?php echo (!$showMaxAge ? "display:none;" : ""); ?>">Max Age</p>
        <div class="row">
          <div class="form-group col-md-6">
            <input class="form-control" type="<?php echo (!$showMaxAge ? "hidden" : "text"); ?>" maxlength="3" name="max_age" id="max_age">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <input type="hidden" class="family_flot_policy_detail_id" name="family_flot_policy_detail_id" value="">
        <button type="button" class="btn btn-submit familyFloatBtn">Submit <i class="ti-check"></i></button>
      </div>
    </div>
  </div>
</div>
<!-- end of Float -->

<!-- Member Details Individual -->
<div class="modal fade" id="editdetails-indi" style="display: none; padding-right: 16px;">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content login-card">
      <div class="modal-header">
        <h5 class="modal-title head-tittle"><img src="/public/assets/abc/images/supertopup.png" width="30"> <span>Heart Secure</span></h5>
        <button type="button" class="close" data-dismiss="modal"><span class="">×</span></button>
      </div>
      <div class="modal-body">
        <p class="ml-tittle">Select Family Contsruct</p>
        <div class="row">
          <div class="col-md-6 col-6 mt-2">
            <div id="accordion2" class="according accordion-s2">
              <div class="card">
                <div class="card-header">
                  <a class="card-link collapsed" data-toggle="collapse" href="#accordion21">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="customCheck33">
                      <label class="custom-control-label" for="customCheck33"><span>Self</span></label>
                    </div>
                  </a>
                </div>
                <div id="accordion21" class="collapse" data-parent="#accordion2">
                  <div class="card-body">
                    <p class="ml-indi">Select Sum Insured</p>
                    <div class="row">
                      <div class="col-md-6 col-12">
                        <div class="custom-control custom-radio">
                          <input type="radio" class="custom-control-input" id="customRadio21">
                          <label class="custom-control-label lbl-indi" for="customRadio21"><span>5 Lakh</span></label>
                        </div>
                      </div>
                      <div class="col-md-6 col-12">
                        <div class="custom-control custom-radio">
                          <input type="radio" class="custom-control-input" id="customRadio22">
                          <label class="custom-control-label lbl-indi" for="customRadio22"><span>10 Lakh</span></label>
                        </div>
                      </div>
                      <div class="col-md-6 col-12">
                        <div class="custom-control custom-radio">
                          <input type="radio" class="custom-control-input" id="customRadio23">
                          <label class="custom-control-label lbl-indi" for="customRadio23"><span>15 Lakh</span></label>
                        </div>
                      </div>
                      <div class="col-md-6 col-12">
                        <div class="custom-control custom-radio">
                          <input type="radio" class="custom-control-input" id="customRadio24">
                          <label class="custom-control-label lbl-indi" for="customRadio24"><span>20 Lakh</span></label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-6 mt-2">
            <div id="accordion2" class="according accordion-s2">
              <div class="card">
                <div class="card-header">
                  <a class="card-link collapsed" data-toggle="collapse" href="#accordion22">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="customCheck33">
                      <label class="custom-control-label" for="customCheck33"><span>Spouse</span></label>
                    </div>
                  </a>
                </div>
                <div id="accordion22" class="collapse" data-parent="#accordion2">
                  <div class="card-body">
                    <p class="ml-indi">Select Sum Insured</p>
                    <div class="row">
                      <div class="col-md-6 col-12">
                        <div class="custom-control custom-radio">
                          <input type="radio" class="custom-control-input" id="customRadio71">
                          <label class="custom-control-label lbl-indi" for="customRadio71"><span>5 Lakh</span></label>
                        </div>
                      </div>
                      <div class="col-md-6 col-12">
                        <div class="custom-control custom-radio">
                          <input type="radio" class="custom-control-input" id="customRadio72">
                          <label class="custom-control-label lbl-indi" for="customRadio72"><span>10 Lakh</span></label>
                        </div>
                      </div>
                      <div class="col-md-6 col-12">
                        <div class="custom-control custom-radio">
                          <input type="radio" class="custom-control-input" id="customRadio73">
                          <label class="custom-control-label lbl-indi" for="customRadio73"><span>15 Lakh</span></label>
                        </div>
                      </div>
                      <div class="col-md-6 col-12">
                        <div class="custom-control custom-radio">
                          <input type="radio" class="custom-control-input" id="customRadio74">
                          <label class="custom-control-label lbl-indi" for="customRadio74"><span>20 Lakh</span></label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-6 mt-2">
            <div id="accordion2" class="according accordion-s2">
              <div class="card">
                <div class="card-header">
                  <a class="card-link collapsed" data-toggle="collapse" href="#accordion23">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="customCheck33">
                      <label class="custom-control-label" for="customCheck33"><span>Kid 1</span></label>
                    </div>
                  </a>
                </div>
                <div id="accordion23" class="collapse" data-parent="#accordion2">
                  <div class="card-body">
                    <p class="ml-indi">Select Sum Insured</p>
                    <div class="row">
                      <div class="col-md-6 col-12">
                        <div class="custom-control custom-radio">
                          <input type="radio" class="custom-control-input" id="customRadio81">
                          <label class="custom-control-label lbl-indi" for="customRadio81"><span>5 Lakh</span></label>
                        </div>
                      </div>
                      <div class="col-md-6 col-12">
                        <div class="custom-control custom-radio">
                          <input type="radio" class="custom-control-input" id="customRadio82">
                          <label class="custom-control-label lbl-indi" for="customRadio82"><span>10 Lakh</span></label>
                        </div>
                      </div>
                      <div class="col-md-6 col-12">
                        <div class="custom-control custom-radio">
                          <input type="radio" class="custom-control-input" id="customRadio83">
                          <label class="custom-control-label lbl-indi" for="customRadio83"><span>15 Lakh</span></label>
                        </div>
                      </div>
                      <div class="col-md-6 col-12">
                        <div class="custom-control custom-radio">
                          <input type="radio" class="custom-control-input" id="customRadio84">
                          <label class="custom-control-label lbl-indi" for="customRadio84"><span>20 Lakh</span></label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-6 mt-2">
            <div id="accordion2" class="according accordion-s2">
              <div class="card">
                <div class="card-header">
                  <a class="card-link collapsed" data-toggle="collapse" href="#accordion24">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="customCheck33">
                      <label class="custom-control-label" for="customCheck33"><span>Kid 2</span></label>
                    </div>
                  </a>
                </div>
                <div id="accordion24" class="collapse" data-parent="#accordion2">
                  <div class="card-body">
                    <p class="ml-indi">Select Sum Insured</p>
                    <div class="row">
                      <div class="col-md-6 col-12">
                        <div class="custom-control custom-radio">
                          <input type="radio" class="custom-control-input" id="customRadio91">
                          <label class="custom-control-label lbl-indi" for="customRadio91"><span>5 Lakh</span></label>
                        </div>
                      </div>
                      <div class="col-md-6 col-12">
                        <div class="custom-control custom-radio">
                          <input type="radio" class="custom-control-input" id="customRadio92">
                          <label class="custom-control-label lbl-indi" for="customRadio92"><span>10 Lakh</span></label>
                        </div>
                      </div>
                      <div class="col-md-6 col-12">
                        <div class="custom-control custom-radio">
                          <input type="radio" class="custom-control-input" id="customRadio93">
                          <label class="custom-control-label lbl-indi" for="customRadio93"><span>15 Lakh</span></label>
                        </div>
                      </div>
                      <div class="col-md-6 col-12">
                        <div class="custom-control custom-radio">
                          <input type="radio" class="custom-control-input" id="customRadio94">
                          <label class="custom-control-label lbl-indi" for="customRadio94"><span>20 Lakh</span></label>
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
      <div class="modal-footer">
        <button type="button" class="btn btn-submit">Submit <i class="ti-check"></i></button>
      </div>
    </div>
  </div>
</div>
<!-- end of individual -->

<?php
$js_files = [
  '../assets/abc/abc_js/abc_custom.js',
  '../assets/abc/js_events/other_msg_abc.js',
];

for ($i = 0; $i < count($js_files); $i++) {
  echo "<script src=" . $js_files[$i] . "></script>\n";
}

?>