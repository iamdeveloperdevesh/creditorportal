<div class="col-md-4 offset-md-4" style="padding-top: 100px;padding-bottom: 50px;">
    <div class="card animate-modal" style="text-align: center;border: 1px solid #deff;">
        <div class="card-body">
            <b>
                <h6 class="" style="font-size: 20px;font-weight: 800;">Lead Number is : <?php echo $trace_id; ?></h6>
            </b>
            <br>
            <div class="row">
                <?php

                if (isset($check)) {

                    if ($check == 1) {

                ?>
                        <h5 id="one">An internal system error occurred, please try again later!</h5>
                    <?php
                    } else if ($check == 2) {

                    ?>
                        <h5 id="two">Thank You !
                            We have received your payment . You will receive the policy copy on your email <?= $email_id; ?> shortly !
                            In case of any queries please call our helpline 1800-270-7000 or email us at care.healthinsurance@adityabirlacapital.com
                        </h5>
                    <?php
                    } else if ($check == 3) {

                    ?>
                        <h5 id="three" style="display:none;">Try again(exceeded)</h5>
                    <?php
                    } else if ($check == 4) {

                    ?>
                        <h5 id="four">Application error occurred, please try again later!</h5>
                <?php
                    }
                }
                ?>
            </div>
            <br>
            <?php /* ?>
                        <input type="hidden" id="leadIdHidden" value="<?= $lead_id; ?>">
                        <?php */ ?>
            <div id="div_hide"><a id="set_id" href="javascript:void(0);"><button class="btn mt-4 cnl-btn" id="btn_id">Try Again</button></a></div>
        </div>
    </div>
</div>
</div>
<!-- main content area end -->
<script>
    $('#set_id').on('click', function() {

        <?php
        
            if(isset($status) && $status == 1){
                
                ?>
                $(this).attr('href', location.href);
                <?php
            }
            else if(isset($status) && $status == 2){

                ?>
                alert('Try again later or please contact support for further assistance');
                <?php
            }
        
        ?>
    });
</script>
<?php /* ?>
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
<?php */ ?>