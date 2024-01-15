<?php
error_reporting('0');
?>
<div class="header-pre sticky-pre" id="myHeader-pre">
                <span class="premium-top dropdown-toggle"  data-toggle="dropdown" aria-expanded="true">Premium : <span class="total_premium" id="total_premium"> 15000 </span> <i class="fa fa-angle-down down-pre"></i></span>
                <div id="premium_calculations_data" class="dropdown-menu drop_prem" x-placement="bottom-start" style="position: absolute; transform: translate3d(15px, 44px, 0px); top: 0px; left: 0px; will-change: transform;overflow-y: scroll;height: 400px;">
                <!-- <p class="text-cover"><img src="/public/assets/abc/images/heartsecure.png" width="25"> <span class="cover-lbl">Heart Secure</span></p>
                <p class="cover-premium"> Premium <span class="amt-drop"> 7650 <span class="incl">(incl gst)</span></span></p> -->
               </div>
      </div>
      <!-- page title area end -->
        <div class="main-content-inner" style="min-height: calc(96vh - 100px);">
            <div class="container mt-2 mb-4">
            <div class="row">
              <div class="col-lg-7 offset-lg-1 col-md-12 mt-2">
                <div class="card login-card">
                  <div class="card-body">
                    <div class="head-tittle">
                      Proposer Details
                    </div>
                    <form id="proposerForm">
                    <div class="border-bot2"></div>
                    <div class="generate-body">
                      <div class="row mt-3 mb-3">
                        <div class="col-lg-3 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="col-form-label">Salutation<sup><i class="fa fa-asterisk"></i></sup></label>
                                    <select class="form-control" name="salutation" id="salutation" style="    padding: 7px 10px;">
                                        <option value = "">Select Salutation</option>
                                        <option value = "Mr">Mr</option>
                                        <option value = "Mrs">Mrs</option>
                                        <option value = "Ms">Ms</option>
                                        <option value = "Dr">Dr</option>
                                    </select>   

                                    <!-- <input class="form-control" type="text" name="salutation" value="<?php echo ($proposer_details['salutation'] != '') ? $proposer_details['salutation'] : ''; ?>" placeholder="Mr" id="salutation"> -->
                                 </div>
                             </div>
                               <div class="col-lg-3 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="col-form-label">First Name<sup><i class="fa fa-asterisk"></i></sup></label>
                                    <input class="form-control" type="text" name="first_name" value= "<?php echo ($fname != '') ? $fname : ''; ?>" placeholder="Enter First Name" id="first_name">
                                 </div>
                             </div>
                                 <div class="col-lg-3 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="col-form-label">Last Name</label>
                                    <input class="form-control" type="text" name="last_name" value="<?php echo ($lname != '') ? $lname : ''; ?>" placeholder="Enter Last Name" id="last_name">
                                 </div>
                             </div>
                                 <div class="col-lg-3 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="col-form-label">Gender<sup><i class="fa fa-asterisk"></i></sup></label>
                                    <select class="form-control valid" name= "gender" id = "gender" style="padding: 7px 10px;">
                                        <option value = "">Select Gender</option>
                                        <option value = "Male">Male</option>
                                        <option value = "Female">Female</option>
                                    </select>
                                 </div>
                             </div>
                              <div class="col-lg-3 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="col-form-label">Email Id<sup><i class="fa fa-asterisk"></i></sup></label>
                                    <input class="form-control" type="text" name="email" value="" placeholder="Enter Email Id" id="email">
                                 </div>
                             </div>
                               <div class="col-lg-3 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="col-form-label">Mobile No<sup><i class="fa fa-asterisk"></i></sup></label>
                                    <input class="form-control" type="text" name="mobile_no" value="<?php echo $mobile?>" placeholder="Enter Mobile No" id="mobile_no" readonly="readonly">
                                 </div>
                             </div>
                             <div class="col-lg-3 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="example-date-input" class="col-form-label">Date Of Birth<sup><i class="fa fa-asterisk"></i></sup></label>
                                    <input class="form-control hasDatepicker1" type="text" autocomplete="off" name="proposer_dob" value="" data-date-format="dd/mm/yyyy" placeholder="dd-mm-yyyy" id="proposer_dob" onkeydown="event.preventDefault()">
                                 </div>
                             </div>
                             <?php if($this->product_id == 'MUTHOOT'){ ?>
                              <div class="col-lg-3 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="col-form-label">NRI<sup><i class="fa fa-asterisk"></i></sup></label>
                                    <select class="form-control valid" name= "isnri" id = "isnri" style="padding: 7px 10px;">
                                        <option value = "N">No</option>
                                        <option value = "Y">Yes</option>
                                    </select>
                                 </div>
                              </div>
                              <?php } ?>
                                <div class="col-md-11">
                                <div class="form-group">
                                    <label for="example-text-input" class="col-form-label">Address<sup><i class="fa fa-asterisk"></i></sup></label>
                                    <input class="form-control" type="text" name="address" value="" placeholder="Enter Address" id="address">
                                 </div>
                             </div>
                                <div class="col-lg-3 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="col-form-label">Pincode<sup><i class="fa fa-asterisk"></i></sup></label>
                                    <input class="form-control" type="text" name="pincode" value="" placeholder="Enter Pincode" id="pincode">
                                 </div>
                             </div>
                                <div class="col-lg-3 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="col-form-label">City<sup><i class="fa fa-asterisk"></i></sup></label>
                                    <input class="form-control" type="text" name="city" value="" placeholder="Select City" id="city" readonly>
                                 </div>
                             </div>
                                 <div class="col-lg-3 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="col-form-label">State<sup><i class="fa fa-asterisk"></i></sup></label>
                                    <input class="form-control" type="text" name="state" value="" placeholder="Select State" id="state" readonly>
                                 </div>
                             </div>

                             <input type="hidden" name="member_id" value="" id="member_id">
                      </div>
                    </div>
                    <div class="generate-footer text-center">
                    <a href ="javascript:void(0)" onclick="window.history.back(-1)" class="btn back-btn">Back</a>
                    <button class="btn save-proceed-mem memProposerBtn ClickBtn">Save & Proceed <i class="fa fa-long-arrow-right"></i></button>
                    <!--  <span class="buy-now-cta memProposerBtn">Buy Now <i class="fa fa-long-arrow-right"></i></span> -->
                    </div>
                </form>
                  </div>
                </div>
              </div>
                  <div class="col-lg-3 mt-2 display-none-sm">
                  <div class="card login-card" style="background:#E5E0D6; border-radius: 30px;padding-bottom: 0px;">
                  <div class="card-body" style="background: url(/public/assets/abc/images/family2.png) no-repeat bottom; background-size: 100%; 
                  height: 544px; border-radius: 30px;">
				    <?php if($this->product_id == 'MUTHOOT') {?>
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
        <?php
       
  $js_files = [
    '../assets/abc/js/vendor/jquery.validate.js',
    '../assets/abc/js_events/other_msg_abc.js',
    '../assets/abc/js/daterangepicker.min.js',
    '../assets/abc/abc_js/abc_proposer.js',
    '../assets/abc/abc_js/scripts.js',
    '../assets/abc/abc_js/abc_single_proposer.js',
    '../assets/abc/abc_js/common.js',
      ];

    for ($i = 0; $i < count($js_files); $i++) {
      echo "<script src=" . $js_files[$i] . "></script>\n";
    }
    
    // if($this->product_id == 'MUTHOOT'){
    //   array_push($js_files,'public/assets/muthoot/js/muthoot_proposer.js');   
    // } else if ($this->product_id == 'HERO_FINCORP') {
    //   array_push($js_files,'public/assets/abc_single/js/abc_single_proposer.js');
    // }
    // Globals::setJs(minify_resources($js_files, 'js', 'home_page2'));
    ?>