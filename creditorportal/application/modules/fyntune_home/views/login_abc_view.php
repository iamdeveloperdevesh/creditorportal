<style type="text/css">
  #otphide,
  #otphide2,
  #otphide3,
  #mobile_no_error,
  #captcha_error {
    display: none;
  }
</style>
<?php
error_reporting('0');
?>
<div class="main-content-inner" style="min-height: calc(96vh - 100px);">
  <div class="container">
    <div class="welcome-msg">
      <b>Welcome to Affinity Portal.</b>
    </div>
    <div class="row mt-3">
      <div class="col-lg-6 col-md-12">
        <div class="card card1">

          <div class="card-body">
            <div class="head-login">
              Please enter your mobile number followed by OTP to embark on Health Journey.
              <!-- <select id="productToLogin" >
                                <option value="ABC">ABC</option>
                                <option value="MUTHOOT">MUTHOOT</option>
                              </select> -->
            </div>
            <div class="border-bot"></div>
            <div class="body-login mt-2">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="example-text-input" class="col-form-label">Mobile Number</label>
                    <input class="form-control" type="text" maxlength="10" placeholder="Enter Mobile No" id="mob_no" onkeyup="return validatephone(this.value);" required />

                    <span id="mobile_no_error" class="alert alert-danger"></span>
                  </div>
                </div>
                <div class="col-md-6 text-center">
                  <span id="gene_otp">
                    <button type="button" onclick="otp_generate()" class="btn btn-generate" id="generate_otp" name="generate_otp">Generate OTP</button>
                  </span>

                  <span id="otphide">
                    <button type="button" onclick="otp_generate()" class="btn btn-generate" id="generate_otp" name="generate_otp" style="margin-left: 05px; background: #dddddd;color:#000;border:1px solid #ddd;">Resend OTP
                    </button>
                  </span>
                </div>

                <!-- captcha update - upendra -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="example-text-input" class="col-form-label">Captcha Text</label>
                    <input class="form-control" type="text" maxlength="10" placeholder="Enter Captcha Text" id="entered_captcha" autocomplete="off" required />

                    <span id="captcha_error" class="alert alert-danger"></span>
                  </div>
                </div>

                <!-- captcha update - upendra -->
                <div class="col-md-6 text-center mt-1">
                  <div class="form-group btn-generate" style="background: none; border: none;">
                    <span id="captcha_image_span">
                      <?php
                      echo $captcha_image['image'];
                      ?>
                    </span>
                    <span style="cursor: pointer;" id="refresh_captcha">
                      <i class="fa fa-refresh" aria-hidden="true"></i>
                    </span>
                  </div>
                </div>

                <!-- OTP Enter Div -->

                <div class="col-md-6" id="otphide2">
                  <div class="form-group">
                    <label for="example-text-input" class="col-form-label">OTP</label>
                    <input class="form-control" type="text" placeholder="Enter OTP" id="pos_otp">
                  </div>
                </div>

                <div class="col-md-6 text-center" id="otphide3">

                  <!-- <button type="button" onclick="otp_generate()" class="btn btn-generate" id="generate_otp" name="generate_otp" style="margin-right: 4px; background: #dddddd;">Resend OTP
                                         </button>
                                      -->
                  <button type="button" id="validate_otp" name="validate_otp" class="btn btn-verify">
                    Verify OTP
                  </button>

                </div>




              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6 text-center display-none-sm">
        <img class="login-img-1" src="../assets/abc/images/login-img.png" style="width: 86%;">
      </div>
    </div>

  </div>
</div>
<!-- main content area end -->


<?php
$js_files = [
  '../assets/abc/js/vendor/jquery.validate.js',
  '../assets/abc/js/vendor/jquery.validate.js',
  '../assets_new/js/jquery-ui.js',
  '../assets/abc/abc_js/login_abc.js',
  '../assets/abc/abc_js/scripts.js',
  '../assets/abc/abc_js/plugins.js',
  '../assets/abc/js_events/other_msg_abc.js',
  '../assets/abc/js/vendor/sweetalert/dist/sweetalert.min.js',
  '../assets/abc/abc_js/common.js'
];

for ($i = 0; $i < count($js_files); $i++) {
  echo "<script src=" . $js_files[$i] . "></script>\n";
}

?>