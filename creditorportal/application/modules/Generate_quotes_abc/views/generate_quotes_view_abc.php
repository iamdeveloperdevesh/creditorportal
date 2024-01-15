<?php

error_reporting('0');

// header('Content-Type: application/json');
// print_r($data);
// exit;

// foreach ($data as $value){
//   echo $data['plan_name']."\n";
// }

// exit;

$plan_name = $data['plan_name'];
$family_construct = $data['family_construct'];
$tenure = $data['tenure'];
$deductable = $data['deductable'];
$trace_id = $data['trace_id'];
$plan_id = $data['plan_id'];
$lead_id = $data['lead_id'];
$customer_id = $data['customer_id'];

unset($data['plan_name']);
unset($data['family_construct']);
unset($data['tenure']);
unset($data['plan_id']);
unset($data['trace_id']);
unset($data['lead_id']);
unset($data['customer_id']);
unset($data['deductable']);

ksort($family_construct);

?>

<div class="header-pre sticky-pre" id="myHeader-pre">
  <span class="premium-top dropdown-toggle" data-toggle="dropdown" aria-expanded="true">Premium : <span id="total_premium" class="total_premium"> 15000 </span> <i class="fa fa-angle-down down-pre"></i></span>
  <div id="premium_calculations_data" class="dropdown-menu drop_prem" x-placement="bottom-start" style="position: absolute; transform: translate3d(15px, 44px, 0px); top: 0px; left: 0px; will-change: transform;overflow-y: scroll;height: 400px;">
    <!-- <p class="text-cover"><img src="/public/assets/abc/images/heartsecure.png" width="25"> <span class="cover-lbl">Heart Secure</span></p>
    <p class="cover-premium"> Premium <span class="amt-drop"> 7650 <span class="incl">(incl gst)</span></span></p> -->
  </div>
</div>
<!-- header area end -->
<!-- page title area end -->
<div class="main-content-inner" style="min-height: calc(96vh - 100px);">
  <div class="container">
    <div class="row mt-2">
      <div class="col-lg-7 offset-lg-1 col-md-12 mt-2 mb-2 pd8">
        <div class="card login-card mb-3 card1">
          <div class="card-body">
            <div class="generate-head head-tittle">
              <!-- <?php //if($this->product_id == 'ABC' || $this->product_id == 'HERO_FINCORP') { 
                    ?>
                        Generate Your Quote  -->
              <?php //} else { 
              ?>
              Know Your Premiumh
              <?php //} 
              ?>

            </div>
            <div class="border-bot2"></div>
            <div class="generate-body">
              <!-- <p class="lbl-cover">Select Cover</p> -->
              <?php /*
              <p class="lbl-cover"></p>
              <div class="cover-tab">
                <div class="tab-part">
                  <button class="tablinks-cover active" onclick="covertab1(event, 'fam-float-id')" id="defaultactive">Family Floater</button>
                  <!-- <button class="tablinks-cover" onclick="covertab1(event, 'indi-id')">Individual</button> -->
                  <!-- <button class="tablinks-cover">Individual</button> -->
                </div>
              </div> */ ?>

              <div id="fam-float-id" class="tabcontent-cover mb-3" style="display: block;">
                <!-- <p class="lbl-cover mt-2">Select Product</p> -->
                <div class="row family_floater">
                  <div class="se-product col-md-4 col-6 mt-1 col-sm-4">
                    <div class="mt-1">
                      <div class="div-pro-range">
                        <div class="custom-control custom-checkbox range-control check10">
                          <!-- <input type="checkbox" class="custom-control-input" id="customCheck<?php echo $i; ?>"> -->
                          <label class="custom-control-label" for="customCheck<?php echo $i; ?>"><img src="<?php echo $plan_name; ?>" width="30"> <br><span><?php echo $plan_name; ?></span></label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <p class="lbl-cover mt-2 mb-2">Selected Product</p>

                <?php
                //foreach ($data as $key => $value){
                ?>

                <div class="family_floater_selected_prod">
                  <div class="row mt-3">
                    <div class="col-md-5 col-sm-6 col-12">
                      <div class="card-product-del">
                        <div class="card   box-gen ">
                          <div class="card-body cd-11">
                            <div class="head-del">
                              <span><img class="del-ht-img" src="/assets/abc/images/supertopup.png" width="40"></span>
                              <span class="del-ht"><?= $plan_name; ?></span>
                              <span class="fl-right" data-toggle="modal" data-target="#editdetails-float"> <span class="ed-nme"><i class="fa fa-pencil mr-1"></i> Edit Details</span></span>
                              <div class="border-bot3"></div>
                              <div class="body-product-del mt-4 mb-3">
                                <span class="mem-del  family_mmeber_selected"> Self <span>+</span> Spouse <span> + </span> Kid 1</span>
                                <p class="sum-del mt-4">Sum Insured <br>
                                  <span class="amt-del">GHI 25 Lacs - GPA 25 Lacs - GCI 5 Lacs</span>
                                </p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <?php
                //}
                ?>


              </div>
              <div class="generate-footer text-center">

                <a href="javascript:void(0)" onclick="window.history.back(-1)" class="btn back-btn">Back</a>

                <!-- <a href="<?php echo base_url('comprehensive_products'); ?>" class="btn back-btn">Back</a> -->
                <a href="<?php echo base_url('member_proposer_detail'); ?>" class="btn save-proceed-mem memProposerBtn ClickBtn">Proceed <i class="fa fa-long-arrow-right"></i></a>

                <!--  <span class="buy-now-cta memProposerBtn">Buy Now <i class="fa fa-long-arrow-right"></i></span> -->
              </div>
              <!-- <div class="generate-footer text-center">
                    <a href="<?php echo base_url('comprehensive_product_abc'); ?>" class="btn back-btn">Back</a>
                   <a href="<?php echo base_url('member_proposer_detail'); ?>">  <span class="buy-now-cta">Buy Now <i class="fa fa-long-arrow-right"></i></span></a>
                    </div> -->
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 display-none-sm" style="margin-top:2.5rem !important;">
        <div class="card login-card" style="background:#E5E0D6; border-radius: 30px;padding-bottom: 0px;">
          <div class="card-body" style="background: url(/assets/abc/images/family2.png) no-repeat bottom;  background-size: 100%;  height: 458px; border-radius: 30px;">
            <div class="tl-hpage">
              <div class="tl-nme">Benefits of 3 plans Specially for You</div>
              <p class="tl-pname"><i class="fa fa-check mr-2" style="color: #0cc50c;"></i>Group Activ Health Insurance</p>
              <p class="tl-pname"><i class="fa fa-check mr-2" style="color: #0cc50c;"></i>Group Activ Critical Illness</p>
              <p class="tl-pname"><i class="fa fa-check mr-2" style="color: #0cc50c;"></i>Group Activ Personal Accident</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>




<div class="modal fade" id="editdetails-float" style="display: none; padding-right: 16px;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content card1">
      <div class="modal-header">
        <h5 class="modal-title head-tittle"> <span class="family_flot_prod_name"><?= $data['plan_name']; ?></span></h5>
        <button type="button" class="close" data-dismiss="modal"><span class="">×</span></button>
      </div>


      <div class="modal-body">
        <form id="get_policy_details" onsubmit="submitLeadform();return false;">


          <p class="ml-tittle">Select Family Construct</p>

          <div class="row family_flot_construct">
            <div class="col-md-3 col-12">
              <input class="mlt" type="hidden" id="h_lead_id" name="h_lead_id" value="<?= $lead_id; ?>">
            </div>
            <div class="col-md-3 col-12">
              <input class="mlt" type="hidden" id="h_trace_id" name="h_trace_id" value="<?= $trace_id; ?>">
            </div>

            <div class="col-md-3 col-12">
              <input class="mlt" type="hidden" id="h_plan_id" name="h_plan_id" value="<?= $plan_id; ?>">
            </div>
            <div class="col-md-3 col-12">
              <input class="mlt" type="hidden" id="h_customer_id" name="h_customer_id" value="<?= $customer_id; ?>">
            </div>
            <?php
            $i = 0;
            foreach ($family_construct as $key => $member_type) {

              $self_checked = '';
              if (strtolower($member_type) == 'self') {
                $self_checked = 'checked';
              }

            ?>
              <div class="col-md-2 col-12">
                <!-- <input type="checkbox" class="custom-control-input" id="customCheck_<?= $member_type . $i++; ?>"> -->
                <input type="checkbox" class="member_get_policy_details" name='family_float_construct' id="<?= $member_type; ?>" <?php echo $self_checked ? $self_checked : ''; ?> <label class="mlt" for="customCheck_<?= $member_type . $i++; ?>"><span><?= $member_type; ?></span></label>
              </div>
            <?php
              // echo $member_type."<br><br>";
            }


            ?>
          </div>

          <p class="ml-tittle mt-3">Select Sum Insured</p>

          <div class="row family_flot_sum_insured col-md-12">
            <?php
            $i = 0;
            $arr = [];
            foreach ($data as $master_policy_id => $product_data) {
            ?>

              <label class="lbl-indi col-md-8 pad-0" for="customRadio<?= $i++; ?>"><span class="lbl-new"><?php echo $product_data['code'] . "(Applicable members: " . implode(",", $product_data['family_construct']) . ")<br>";  ?></span></label>

              <?php
              // echo $product_data['code'] ."(Applicable members: ".implode(",", $product_data['family_construct']).")<br>";

              ?>

              <select class="member_sum_insured_get_policy_details quote_generation_fields select-new col-md-3" name="<?php echo str_replace(" ", "", strtolower($product_data['code'])); ?>_cover" id="<?php echo str_replace(" ", "", strtolower($product_data['code'])); ?>_cover">
                <?php

                $flag = false;
                foreach ($product_data['sum_insured'] as $key => $sum_insured_data) {

                  if (!$flag) {

                    if ($product_data['is_optional'] == 1) {
                ?>
                      <option value="" />Select</option>
                  <?php

                      $flag = true;
                    }
                  }
                  ?>
                  <!-- <input type="radio" name="customRadio_sum_insured" id="customRadio_sum_insured" value="<?php echo $sum_insured_data;  ?>">
                      <label class="custom-control-label lbl-indi" for="customRadio<?= $i++; ?>"><span><?php echo $sum_insured_data . "<br>";  ?></span></label> -->
                  <option value="<?php echo $sum_insured_data; ?>" /><?= $sum_insured_data; ?></option>

                <?php
                  // echo $sum_insured_data['sum_insured']."<br>";

                }
                ?>
              </select>
              <div class="col-md-12 mt-3"></div>
              <!-- </div> -->

            <?php
            }

            if (!empty($deductable)) {
            ?>
              <label class="lbl-indi"><span class="lbl-new">Deductable</span></label>
              <select class="member_deductable_get_policy_details quote_generation_fields select-new" name="deductable" id="deductable">
                <?php

                //$flag = false;
                foreach ($deductable as $key => $value) {

                  //if(!empty($sum_insured_data['deductable']) && !in_array($sum_insured_data['deductable'], $arr)){

                  //array_push($arr, $sum_insured_data['deductable']);

                  /*if (!$flag) {

                          if ($product_data['is_optional'] == 1) {
                    ?>
                          <option value="" />Select</option>
                      <?php

                            $flag = true;
                          }
                        }*/
                ?>
                  <!-- <input type="radio" name="customRadio_deductible" id="customRadio_deductible" value="<?php echo  $deductable;  ?>">
                        <label class="custom-control-label lbl-indi" for="customRadio<?= $i++; ?>"><span><?php echo $deductable . "<br>";  ?></span></label> -->
                  <option value="<?php echo $value; ?>" /><?= $value; ?></option>
                <?php
                  // echo $sum_insured_data['sum_insured']."<br>";
                  //}
                }
                ?>
              </select>
            <?php
            }

            ?>
            <div class="col-md-12 mt-3"></div>


            <label class="lbl-indi"><span class="lbl-new">Tenure</span></label>
            <select id="tenure" name="tenure" class="member_tenure_get_policy_details quote_generation_fields select-new">
              <?php

              if (!empty($tenure)) {

              ?>
                <label class="custom-control-label lbl-indi"><span>Tenure</span></label>
                <?php
                foreach ($tenure as $key => $value) {

                ?>
                  <!-- <input type="radio" name="customRadio_tenure" for="customRadio<?= $i++; ?>" id="customRadio_tenure" value="<?php echo $value;  ?>">
                  <label class="custom-control-label lbl-indi" for="customRadio<?= $i++; ?>"><span><?php echo $value . "<br>";  ?></span></label> -->
                  <option value="<?php echo $value; ?>" /><?php echo $value; ?></option>
                <?php
                  // echo $sum_insured_data['sum_insured']."<br>";
                }
              } else {

                ?>
                <!-- <input type="radio" name="customRadio_tenure" for="customRadio<?= $i++; ?>" id="customRadio_tenure" value="1" checked="checked" disabled="disabled">
                <label class="custom-control-label lbl-indi" for="customRadio<?= $i++; ?>"><span>1</span></label> -->
                <option value="1" />1</option>
              <?php
              }
              ?>
            </select>

            <!-- <p class="ml-tittle mt-3">Select Sum Insured</p> -->
            <!-- <div class="row family_flot_sum_insured">

                </div> -->
            <div class="col-md-12 mt-3"></div>
            <div class="">
              <label class="lbl-new">Max Age</label>

              <input class="member_maxage_get_policy_details input-new" type="text" maxlength="3" name="max_age" id="max_age">

            </div>

            <div class="col-md-12 text-center mt-3">
              <button type="submit" class="btn btn-submit">Save <i class="ti-check"></i></button>
              <input type="hidden" class="family_flot_policy_detail_id" name="family_flot_policy_detail_id" value="">
        </form>
      </div>
    </div>
  </div>
</div>

<!-- end of Float -->

<!-- end of individual -->

<?php

$js_files = [
  '../assets/abc/abc_js/abc_custom.js',
  '../assets/abc/js_events/other_msg_abc.js',
  '../assets/abc/abc_js/common.js',
];

for ($i = 0; $i < count($js_files); $i++) {
  echo "<script src=" . $js_files[$i] . "></script>\n";
}
?>