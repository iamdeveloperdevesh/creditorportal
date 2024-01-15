<!DOCTYPE html>
<html lang="en">

<body>

<!-- page-wrapper Start-->
<div class="page-wrapper">
    <div class="authentication-box">
        <div class="container">
            <div class="row">
                <div class="col-md-5 p-0 card-left">
                    <div class="card bg-primary">
						<img src="<?php echo base_url(); ?>assets/images/logo2.png" alt="Chheda Dry Fruits"/>			

                        <div class="single-item">
                            <div>
                                <div>
                                    <h3>Welcome to creditor portal</h3>
                                    <p>Partner portal</p>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="col-md-7 p-0 card-right">
                    <div class="card tab2-card">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-material" id="top-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="top-profile-tab" data-toggle="tab" href="#top-profile" role="tab" aria-controls="top-profile" aria-selected="true"><span class="icon-user mr-2"></span>Login</a>
                                </li>
                                <!-- <li class="nav-item">
                                    <a class="nav-link" id="contact-top-tab" data-toggle="tab" href="#top-contact" role="tab" aria-controls="top-contact" aria-selected="false"><span class="icon-unlock mr-2"></span>Register</a>
                                </li> -->
                            </ul>
                            <div class="tab-content" id="top-tabContent">
                                <div class="tab-pane fade show active" id="top-profile" role="tabpanel" aria-labelledby="top-profile-tab">
                                    <form class="form-horizontal auth-form" id="form-validate"  method="post">
                                        <div class="form-group">
                                            <input required="" name="username" id="username" type="text" class="form-control" placeholder="Username" autofocus>
                                        </div>
                                        <div class="form-group">
                                            <input required="" name="password" id="password" type="password" class="form-control" placeholder="Password">
                                        </div>
                                        <div class="form-terms">
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="customControlAutosizing">
                                                <!--<label class="custom-control-label" for="customControlAutosizing">Remember me</label>
                                                <a href="#" class="btn btn-default forgot-pass">lost your password</a>-->
                                            </div>
                                        </div>
                                        <div class="form-button">
                                            <button class="btn btn-primary" type="submit">Login</button>
                                        </div>
                                        
                                        <div id="show_msg"></div>
                                    </form>
                                </div>
                                

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <a href="index.html" class="btn btn-primary back-btn"><i data-feather="arrow-left"></i>back</a> -->
        </div>
    </div>
</div>


</body>
<script>
var vRules = {
			username:{required:true},
			password:{required:true}
		};
		var vMessages = {
			username:{required:"Please enter username."},
			password:{required:"Please enter password."}
		};

		$("#form-validate").validate({
			rules: vRules,
			messages: vMessages,
			submitHandler: function(form) 
			{
				var act = "<?php echo base_url();?>login/loginvalidate";
				$("#form-validate").ajaxSubmit({
					url: act, 
					type: 'POST',
					dataType: 'JSON',
					cache: false,
					clearForm: false,
					success: function (response) {
						// var res = eval('('+response+')');
						//alert("jlf: "+ res['success']);
						if(response.success)
						{
							$("#show_msg").html('<span style="color:#339900;">'+response.msg+'</span>');
							setTimeout(function(){
								window.location = "<?php echo base_url();?>home";
							},2000);

						}
						else
						{	
							$("#show_msg").html('<span style="color:#ff0000;">'+response.msg+'</span>');
							return false;
						}
					}
				});
			}
		});
</script>
</html>
