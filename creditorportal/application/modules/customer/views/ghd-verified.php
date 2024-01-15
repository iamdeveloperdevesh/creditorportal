<?php //echo "<pre>";print_r($coi_data);?>

            <div class="col-lg-12 mt-2">
                <div class="card login-card" style="border-radius:20px;">
                    <div class="card-body">
                        <p class="text-center thank-spell mb-5 mt-4">Thankyou for choosing Aditya Birla Health Insurance</p>
                        <div class="row sp-10">
                            <div class="col-md-5 col-lg-3">
                                <img src="<?php echo base_url(); ?>assets/images/ad-thankyou2.png">
                            </div>
                            <div class="col-md-7 col-lg-9 display-sm-none">
                                <div class="lead-head mb-5">
                                    <span class="id-lead-p">Your Lead ID: <span class="id-span"><?php echo encrypt_decrypt_password($lead_id, 'D'); ?></span></span>
                                </div>
                                <?php

                                //$coi_data = explode(',', $coi_data['certificate_number']);
                                /*
                                ?>
                                <div class="coi-table mt-3 coi-tbl">
                                    <div class="single-table">
                                        <div class="table-responsive">
                                            <table class="table table-bordered text-center">
                                                <thead class="table-coi-tr">
                                                    <tr>
                                                        <th scope="col">Policy</th>
                                                        <th scope="col">Member Details</th>
                                                        <th scope="col">COI #</th>
                                                        <th scope="col">Download COI</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php for($i=0;$i<sizeof($coi_data);$i++){ ?>
                                                        <tr>
                                                            <th><?php echo $coi_data[$i]['code'];?></th>
                                                            <th><?php echo $coi_data[$i]['policy_member_first_name'].' '.$coi_data[$i]['policy_member_last_name'].'('.$coi_data[$i]['member_type'].')';?></th>
                                                            <th><?php echo $coi_data[$i]['certificate_number'];?></th>
                                                            <td><button class="btn-down-ld btn" id="<?php echo $coi_data[$i]['certificate_number'];?>" onclick="call_function('<?php echo $coi_data[$i]['certificate_number'];?>')"> <i class="ti-file" style="font-weight: bold;"></i> Download</button></td>
                                                        </tr>
                                                    <?php } ?>


                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                */
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
       
<!-- end: Content -->
<script type="text/javascript">
    /*function call_function(certificate_no) {
        $("#" + certificate_no).html("Please wait...");
        $("#" + certificate_no).attr("disabled", true);

        $.post("<?php echo base_url();?>/customer/coi_download", {
                "certificate_no": certificate_no
            },
            function(e) {
                var obj = JSON.parse(e);
                $("#" + certificate_no).html('<i class="fa fa-download"></i> Download');
                $("#" + certificate_no).attr("disabled", false);
                if (obj.status == 'success') {
                    //document.getElementById("set_id_"+certificate_no).href = obj.url;
                    var a = document.createElement('a');
                    var url = obj.url;
                    a.href = url;
                    a.download = url;
                    document.body.append(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);

                } else {
                    alert("Try Again Later...");
                }

            });
    }*/
</script>