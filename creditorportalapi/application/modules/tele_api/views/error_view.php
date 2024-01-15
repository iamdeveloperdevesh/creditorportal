<div class="main-content-inner" style="min-height: calc(96vh - 100px);">
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4" style="padding-top: 100px;padding-bottom: 50px;">
                <div class="card animate-modal" style="text-align: center;border: 1px solid #deff;">
                    <div class="card-body">
                        <b>
                            <h6 class="" style="font-size: 20px;font-weight: 800;">Lead Number is : <?php echo $lead_id; ?></h6>
                        </b>
                        <br>
                        <div class="row">
                            <h5 id="two" style="display:none">Thank You !
                                We have received your payment . You will receive the policy copy on your email <?= $email_id; ?> shortly !
                                In case of any queries please call our helpline 1800-270-7000 or email us at care.healthinsurance@adityabirlacapital.com
                            </h5>
                            <h5 id="one" style="display:none;">An internal system error occurred, please try again later!</h5>
                            <h5 id="four" style="display:none;">Application error occurred, please try again later!</h5>
                            <h5 id="three" style="display:none;">Try again(exceeded)</h5>
                        </div>
                        <br>
                        <input type="hidden" id="leadIdHidden" value="<?= $lead_id; ?>">
                        <div id="div_hide"><a id="set_id" href="#"><button class="btn mt-4 cnl-btn" id="btn_id">Try Again</button></a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- main content area end -->
<script>
    $(document).ready(function() {

        var lead_id = $("#leadIdHidden").val();

        if (lead_id) {
            check_error(lead_id);
        }

    });

    function check_error(lead_id) {
        $.post("/api2/check_error_data", {
                "lead_id": lead_id
            },
            function(e) {

                var obj = JSON.parse(e);
                var obj = JSON.parse(e);
                //console.log(obj);
                if (obj.check == 1) {
                    $("#one").show();
                    $("#two").hide();
                    $("#three").hide();
                } else if (obj.check == 2) {
                    $("#one").hide();
                    //$("#btn_id").html('<span style="padding-right:10px; font-size: 13px; font-weight: 800; letter-spacing: 1px;">Click Here</span>');
                    $("#div_hide").hide();
                    $("#two").show();
                    $("#three").hide();
                } else if (obj.check == 3) {
                    $("#one").hide();
                    $("#two").hide();
                    $("#three").show();
                }
                if (obj.status == 1) {
                    document.getElementById("set_id").href = obj.url;
                } else if (obj.status == 2) {
                    alert("Try Again Later... URL sent on email");
                }

            });

    }
</script>