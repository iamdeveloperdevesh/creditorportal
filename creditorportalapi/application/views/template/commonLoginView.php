<div id="login_popup" class="pop_up popup_hide">        
        <div class="pop_up_close"></div>
        <div class="pop-up-container">
        		<div class="row" id="loginbox">
					<?php 
						$pageUrl = $this->uri->segment(1);
						//echo "pageUrl: ".$pageUrl;
					?>
                	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 mb20 border-right-solid">
                    	<h2 class="mb20">Register</h2>
                        <p id="reg_display_msg"></p>
                        <form id="register_form" method="post" enctype="multipart/form-data">
                            <fieldset>
								<input type="hidden" id="pageUrl" name="pageUrl" value="<?php echo $pageUrl; ?>" />
								<input type="text" id="firstname" name="firstname" placeholder="First Name" class="form-control mb20 login-input">
								<input type="text" id="mobile" name="mobile" maxlength="10" placeholder="Mobile Number" class="form-control mb20 login-input">
								<input type="text" id="email_reg" name="email_reg" placeholder="Email" class="form-control mb20 login-input">
								<input type="password" id="conf_pass1" name="conf_pass1" placeholder="Enter Password" class="form-control mb20 login-input">
								<input type="password" id="conf_pass2" name="conf_pass2" placeholder="Re Enter Password" class="form-control mb20 login-input">
								<label><input type="checkbox" name="agree"> I, hereby agree that the information provided by me is correct and agree to all <a href="<?php echo base_url(); ?>privacy_policy">policies</a> and <a href="<?php echo base_url(); ?>terms_of_use">terms and conditions.</a></label><br />
								<button type="submit" id="register_form_button" class="btn btn-custom btn-block mb20">Register</button>
                            </fieldset>
                        </form>
                        <form style="display:none" id="otp_form_regis"  method="post" enctype="multipart/form-data">
                        <fieldset>
							<input type="hidden" id="pageUrl" name="pageUrl" value="<?php echo $pageUrl; ?>" />
                          <input id="otp" type="text" name="otp" placeholder="ENTER OTP" class="form-control mb20 login-input">
                          <button type="submit" id="otp_form_button_regis" class="btn btn-custom btn-block mb20">Submit</button>
                        </fieldset>
                        </form>
                        
                    </div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 mb20">
                		<h2 class="mb20">Sign In</h2>
                                <p id="dispaly_error"><p>
                        <form id="login_form" method="post" enctype="multipart/form-data">
                            <fieldset>
							<input type="hidden" id="pageUrl" name="pageUrl" value="<?php echo $pageUrl; ?>" />
                            <input id="username" type="text" name="username" placeholder="Username" class="form-control mb20 login-input">
                            <input id="password" type="password" name="password" placeholder="Password" class="form-control mb20 login-input">
                            <section class="mb20 text-center">
                                <a href="#" onClick="$('#loginbox').hide(); $('#forgotbox').show()" class="black_text">Forgot Password? </a>
                            </section>
                            <button type="submit" id="login_form_button" class="btn btn-custom btn-block mb20">Submit</button>
                            </fieldset>
                        </form>
                        <form style="display:none" id="otp_form"  method="post" enctype="multipart/form-data">
							<fieldset>
								<input type="hidden" id="pageUrl" name="pageUrl" value="<?php echo $pageUrl; ?>" />
							  <input id="otp" type="text" name="otp" placeholder="ENTER OTP" class="form-control mb20 login-input">
							  <button type="submit" id="otp_form_button" class="btn btn-custom btn-block mb20">Submit</button>
							</fieldset>
                        </form>        
						
						<?php 
							if(empty($pageUrl) && ($pageUrl != "course_description" || $pageUrl != "module_description") ){
						?>
							<ul class="list-inline">
								<li id="fbloginshow" class="mb20">
									<a href="#">
										<img src="<?php echo base_url();?>images/login_fb_icon.png" alt="Login with Facebook" href="javascript:void(0);" onclick="fbLogin();" />
									</a>
								</li>                           
								<li class="mb20">
									<a href="#">
										<span class="g-signin loginFbMobile" data-scope="email" data-clientid="610830668928-g8m05f4mbpel944jarkdj6gb026gqvf6.apps.googleusercontent.com" data-callback="onSignInCallback" data-theme="dark" data-cookiepolicy="single_host_origin">
											<img src="<?php echo base_url();?>images/login_google_icon.png" alt="Login with Google" onclick="loginwithgoogle();" />
										</span>	
									</a>
								</li>                           
							</ul>
						<?php }?>
						
            		</div>	                                   
                </div> 
            <div class="row"  id="forgotbox" style="display:none;">
                <div class="col-lg-6 col-md-6 col-sm-12 colxs-12 col-lg-offset-3 col-md-offset-3 mb20">                   
                        <h2 class="mb20">Forgot Password</h2> 
                        <p id="dispaly_msg"><p>
                        <form id="forgot_pass" method="post" enctype="multipart/form-data">
                            <fieldset> 
							<input type="hidden" id="pageUrl" name="pageUrl" value="<?php echo $pageUrl; ?>" />
                            <input type="text" id="email_id" name="email_id" placeholder="Enter email" class="form-control mb20 login-input">                          
                            <button type="submit" id="forgot_pas_button" class="btn btn-custom btn-block mb20">Submit</button>
                            <section class="text-center">
                                <a id="signinlink" href="#" onclick="$('#forgotbox').hide(); $('#loginbox').show()" class="black_text">Back to Login </a>
                            </section>
                            </fieldset> 
                        </form>           
                </div>
            </div>
        </div>
    </div>
	<script>
	var lb = new $.LoadingBox({loadingImageSrc: "<?php echo  base_url(); ?>images/loader.gif"});
	$("#loading-box").hide();
	</script>