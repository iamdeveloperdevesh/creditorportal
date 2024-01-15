<style>
    .container-fluid{
    }
</style>
<script type="text/javascript" src='<?PHP echo base_url('assets/js/html2pdf.bundle.js', PROTOCOL); ?>'></script>
<div class="agn-counter-section-pay">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <img src="/assets/images/Business deal-pana.png" width="500" class="dis-none">
            </div>
            <div class="col-md-6 text-center">
                <img src="/assets/images/success.png" class="mt-3" style="display: unset;">
                <!-- <img src="/assets/images/img_cut.png" class="img_left_cut">
                <img src="/assets/images/img_cut_r.png" class="img_right_cut"> -->
                <h3 class="title text-center">Thank You!</h3>
                <!-- <p class="g-success mt-1 text-center">Your payment was successful</p>
                <div class="color_red text-left back-thnk">
                    <p>Lead ID:
                        <span id="lead_view"> 249</span>
                    </p>
                    <p>Payment ID:
                        <span> pay_ItsX3SD8wyRIOT</span>
                    </p>
                    <p>Certificate Number:
                        <span>GHI-XL-AS-31459489</span>
                    </p>
                </div> -->

                <?php

               echo $data['html'];
 ?>
                <!-- <div class="color_red p_payment_succcess_p text-center"></div> -->
                <!-- <p class="text-center">You are now been redirected to HDFC life website.</p>
                            <p class="text-center">It will take a few seconds. Do not press back or refresh your screen.</p> -->
                <div class="col-md-12 text-center mt-3">
                    <button class="btn btn-primary btn_primary_p_su" id="coidownload"> Download COI <i class="fa fa-download ml-1"></i></button>
                    <!--                        <a href="--><?php //APPPATH ?><!--coi_815.pdf" download=""><button class="btn btn-primary btn_primary_p_su" > Download COI <i class="fa fa-download ml-1"></i></button></a>-->
                </div>
                <div class="col-md-12 text-center mt-1">
						<span class="text-center" style="font-size: 19px; color:#444;">If you have any questions, please call us on <span style="color: #000;"><b>1800263683898</b></span></p>
                </div>
            </div>
            <!-- <a href="contact-us-standard.html" class="contact solid-button-one" style="margin: 0px;" data-toggle="modal" data-target="#whatsapp_m">Talk to Us</a> -->

        </div> <!-- /.container -->
    </div>


</div> <!-- /.full-width-container -->

<script>
    $(document).ready(function() {
        function ajaxindicatorstart(text) {
            text = typeof text !== "undefined" ? text : "Please wait....";

            var res = "";

            if ($("body").find("#resultLoading").attr("id") != "resultLoading") {
                res += "<div id='resultLoading' style='display: none'>";
                res += "<div id='resultcontent'>";
                res += "<div id='ajaxloader' class='txt'>";
                res +=
                    '<svg class="lds-curve-bars" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><g transform="translate(50,50)"><circle cx="0" cy="0" r="8.333333333333334" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="26.179938779914945 26.179938779914945" transform="rotate(2.72337)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="0" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="16.666666666666668" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="52.35987755982989 52.35987755982989" transform="rotate(64.7343)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.2" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="25" fill="none" stroke="#ffffff" stroke-width="4" stroke-dasharray="78.53981633974483 78.53981633974483" transform="rotate(150.07)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.4" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="33.333333333333336" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="104.71975511965978 104.71975511965978" transform="rotate(239.433)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.6" repeatCount="indefinite"></animateTransform></circle><circle cx="0" cy="0" r="41.666666666666664" fill="none" stroke="#861F41" stroke-width="4" stroke-dasharray="130.89969389957471 130.89969389957471" transform="rotate(320.34)"><animateTransform attributeName="transform" type="rotate" values="0 0 0;360 0 0" times="0;1" dur="1s" calcMode="spline" keySplines="0.2 0 0.8 1" begin="-0.8" repeatCount="indefinite"></animateTransform></circle></g></svg>';
                res += "<br/>";
                res += "<span id='loadingMsg'></span>";
                res += "</div>";
                res += "</div>";
                res += "</div>";

                $("body").append(res);
            }

            $("#loadingMsg").html(text);

            $("#resultLoading").find("#resultcontent > #ajaxloader").css({
                position: "absolute",
                width: "500px",
                height: "75px",
            });

            $("#resultLoading").css({
                width: "100%",
                height: "100%",
                position: "fixed",
                "z-index": "10000000",
                top: "0",
                left: "0",
                right: "0",
                bottom: "0",
                margin: "auto",
            });

            $("#resultLoading").find("#resultcontent").css({
                background: "#ffffff",
                opacity: "0.7",
                width: "100%",
                height: "100%",
                "text-align": "center",
                "vertical-align": "middle",
                position: "fixed",
                top: "0",
                left: "0",
                right: "0",
                bottom: "0",
                margin: "auto",
                "font-size": "16px",
                "z-index": "10",
                color: "#000000",
            });

            $("#resultLoading").find(".txt").css({
                position: "absolute",
                top: "-25%",
                bottom: "0",
                left: "0",
                right: "0",
                margin: "auto",
            });

            $("#resultLoading").fadeIn(300);

            $("body").css("cursor", "wait");
        }

        function ajaxindicatorstop() {
            $("#resultLoading").fadeOut(300);

            $("body").css("cursor", "default");
        }
        $(document).on("click", "#coidownload", function() {
            ajaxindicatorstart("Downloading...");
            var lead_id = $("#lead_view").text();
            $.ajax({
                url: "/teleproposal/coi_download",
                type: "POST",
                data: {
                    'lead_id': lead_id,
                },
                dataType: 'html',
                success: function(response) {
                    html2pdf()
                        .set({
                            filename: 'coi_' + lead_id + '.pdf'
                        })
                        .from(response)
                        .save();
                    setTimeout(function() {
                        ajaxindicatorstop();
                    }, 5000);
                }
            });
        });
    })
</script>