<div class="main-content-inner" style="min-height: calc(96vh - 100px);">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 mt-2">
                <div class="card login-card">
                    <div class="card-body">
                        <p class="text-center thank-spell mb-5 mt-4">Thankyou for choosing Aditya Birla</p>
                        <div class="row sp-10">
                            <div class="col-md-5 col-lg-3">
                                <img src="<?php echo base_url(); ?>assets/images/ad-thankyou2.png">
                            </div>
                            <div class="col-md-7 col-lg-9">
                                <div class="lead-head mb-5">
                                    <span class="id-lead-p">Your Lead ID: <span class="id-span"><?= $coi_data['lead_id']; ?></span></span>
                                </div>
                                <?php

                                $coi_data = explode(',', $coi_data['certificate_number']);

                                ?>
                                <div class="coi-table mt-3">
                                    <div class="single-table">
                                        <div class="table-responsive">
                                            <table class="table table-bordered text-center">
                                                <thead class="table-coi-tr">
                                                    <tr>
                                                        <th scope="col">COI #</th>
                                                        <th scope="col">Download COI</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($coi_data  as $val) { ?>
                                                        <tr>
                                                            <th><?= $val; ?></th>
                                                            <td><button class="btn-down-ld btn" id="<?= $val; ?>" onclick="call_function('<?php echo $val; ?>')"> <i class="ti-file" style="font-weight: bold;"></i> Download</button></td>
                                                        </tr>
                                                    <?php } ?>


                                                </tbody>
                                            </table>
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
<!-- end: Content -->
<script type="text/javascript">
    function call_function(certificate_no) {
        $("#" + certificate_no).html("Please wait...");
        $("#" + certificate_no).attr("disabled", true);

        $.post("/api2/coi_download", {
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
    }
</script>