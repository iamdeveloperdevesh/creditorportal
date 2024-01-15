<!-- page title area end -->
<style>
	.error {
		color: red;
		position: inherit;
	}

	.success-txt {
		font-weight: 600;
		letter-spacing: 0.4px;
	}

	.brand-name {
		position: inherit;
	}
	.error {
        color: red;
        position: relative;
        top: 0;
    }
    .fa-long-arrow-alt-right:before {
        content: "\f30b";
    }
    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus, 
    input:-webkit-autofill:active{
        -webkit-box-shadow: 0 0 0 40px white inset !important;
    }
</style>
<div class="main-content-inner" style="min-height: calc(100vh - 100px);">
	<div class="container">
		<div class="welcome-msg text-center mt-3 mb-3">
			<b>Welcome to Affinity Portal</b>
		</div>
		<div class="row mt-3">
			<div class="col-lg-7 text-center display-none-sm">
				<img class="login-img-1" src="<?php echo base_url(); ?>assets/images/Login3.gif" style="width: 550px;">
			</div>
			<div class="col-lg-4 offset-lg-0 col-md-8 offset-md-2 mt-4">
				<div class="card login-card" style="background: url(assets/images/back2.gif) no-repeat;">
					<div class="card-body">
					                                    <div class="head-login">

					 <nav id="nav-tb mb-3">
                                            <div class="nav nav-tabs bor-none" id="nav-tab" role="tablist">
                                                <a class="nav-item nav-link  active-nav active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Login</a>
                                               <!--<a class="nav-item nav-link  active-nav" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Tele Login</a>-->
                                            </div>
                                        </nav>
										</div>
						<div class="tab-content" id="nav-tabContent">
                                            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">

						<form class="form-horizontal auth-form" id="form-validate" method="post">
							<div class="body-login">
								<div class="row">
									<div class="col-lg-10 offset-lg-1">
										<div class="form-group">
											<label for="example-text-input" class="col-form-label">Username<span class="lbl-star">*</span></label>
											<input class="form-control login-form-control" type="text" placeholder="Enter Username" id="username" name="username" />
										</div>
									</div>
									<div class="col-lg-10 offset-lg-1">
										<div class="form-group">
											<label for="example-text-input" class="col-form-label">Password<span class="lbl-star">*</span></label>
											<input class="form-control login-form-control" type="password" placeholder="Enter Password" id="password" name="password" />
										</div>
									</div>

									<div id="show_msg" class="success-txt col-md-12" style="text-align:center;"></div>

									<div class="col-lg-10 offset-lg-1 text-center mt-3 mb-3">
										<button class="btn btn-login" type="submit">Submit <i class="fas fa-long-arrow-alt-right rht-aw"></i></button></button>
									</div>



								</div>
							</div>
						</form>
						</div>
						
						          <div class="tab-pane fade " id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                            <form action="#" method="post" id="agent_form">
                                                <input class="form-control" type="hidden"  name="enckey" value="<?php echo $randomString; ?>"  id="enckey">
                                                <div class="offset-md-0 mt-2">
                                                    <!-- upendra - maker/checker - 30-07-2021  -->
                                                    <?php
                                                    if (!isset($_GET['login_type']) && $_GET['login_type'] != 'maker_checker') {
                                                    ?>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <select class="form-control" id="agent_type" name="agent_type">
                                                                    <option value=1>Agent Login</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    <?php
                                                    } else if (isset($_GET['login_type']) && $_GET['login_type'] == 'maker_checker') {
                                                    ?>

                                                        <input type="hidden" name="agent_type" id="agent_type" value="4">

                                                    <?php
                                                    }
                                                    ?>

                                                    <div class="col-md-12" id="digital_role_id" style="display: none;">
                                                        <div class="form-group">
                                                        <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="digital_role" id="agent_radio" value="agent" checked>
                                                        <label class="form-check-label" for="agent_radio">Agent</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="digital_role" id="do_radio" value="do">
                                                        <label class="form-check-label" for="do_radio">Digital Officer</label>
                                                    </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <!-- <label for="example-text-input" class="col-form-label">Agent Code</label> -->
                                                            <input class="form-control" type="text" id="agent_code" name="agent_code" placeholder="Enter Agent Code" autofocus>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <!-- <label for="example-text-input" class="col-form-label">Password</label> -->
                                                            <input class="form-control" type="password" id="agent_pwd" name="agent_pwd" autocomplete="off" placeholder="Enter Password">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- captcha update - upendra -->
                                                  <div class="col-md-12 row agent_captcha" style="display:none;">
                                                    <div class="form-group col-md-6 col-12">
                                                        <label for="example-text-input" class="col-form-label">Captcha Text</label>
                                                        <input class="form-control ignore" type="text" maxlength="10" placeholder="Enter Captcha Text" name="entered_captcha_agent" id="entered_captcha_agent" autocomplete="off"/>

                                                        <span id="captcha_error"></span>
                                                     </div>
                                                     <div class="col-md-6 text-center mt-2 agent_captcha position: absolute;"  style="display:none;">
                                                        <div class="form-group btn-generate row" style="background: none; border: none;">
                                                          <span id="captcha_image_span_agent">
                                                            <?php
                                                              echo $captcha_image['image'];
                                                            ?>
                                                          </span>
                                                          <span style="cursor: pointer;" id="refresh_captcha_agent">
                                                              <i class="fa fa-refresh  mt-2 ml-1 " style = "position: absolute;" aria-hidden="true"></i>
                                                          </span>
                                                        </div>
                                                      </div>
                                                  </div>

                                                  <!-- captcha update - upendra -->
                                                <div id="show_msg_tele" class="success-txt col-md-12" style="text-align:center;"></div>

                                                <div class="col-md-12 text-center mt-4">
                                                    <button type="submit" name="submit" class="btn btn-verify" style="color:#fff;"> Submit <i class="ti-check"></i> </button>
                                                </div>

                                            </form>
                                            </div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- main content area end -->
<script type="text/javascript">
	var vRules = {
		username: {
			required: true
		},
		password: {
			required: true
		}
	};
	var vMessages = {
		username: {
			required: "Please enter username."
		},
		password: {
			required: "Please enter password."
		}
	};

    var aRules = {
        agent_code: {
            required: true
        },
        agent_pwd: {
            required: true
        }
    };
    var aMessages = {
        agent_code: {
            required: "Please enter Agent Code."
        },
        agent_pwd: {
            required: "Please enter Agent Password."
        }
    };

	$("#form-validate").validate({
		rules: vRules,
		messages: vMessages,
		submitHandler: function(form) {
			var act = "<?php echo base_url(); ?>login/loginvalidate";
			$("#form-validate").ajaxSubmit({
				url: act,
				type: 'POST',
				dataType: 'JSON',
				cache: false,
				clearForm: false,
				success: function(response) {
					// var res = eval('('+response+')');
					//alert("jlf: "+ res['success']);
					if (response.success) {
						$("#show_msg").html('<span style="color:#339900;">' + response.msg + '</span>');
						setTimeout(function() {
							window.location = "<?php echo base_url(); ?>home";
						}, 2000);

					} else {
						$("#show_msg").html('<span style="color:#ff0000;">' + response.msg + '</span>');
						return false;
					}
				}
			});
		}
	});
	document.title = "Login";


    $("#agent_form").validate({
        rules: vRules,
        messages: vMessages,
        submitHandler: function(form) {
            var act = "<?php echo base_url(); ?>login/get_login_details";
            $("#agent_form").ajaxSubmit({
                url: act,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                clearForm: false,
                success: function(response) {
                    if (response.success) {
                        $("#show_msg_tele").html('<span style="color:#339900;">' + response.msg + '</span>');
                        setTimeout(function() {
                            window.location = "<?php echo base_url(); ?>telehome";
                        }, 2000);

                    } else {
                        $("#show_msg_tele").html('<span style="color:#ff0000;">' + response.msg + '</span>');
                        return false;
                    }                }
            });
        }
    });
</script>