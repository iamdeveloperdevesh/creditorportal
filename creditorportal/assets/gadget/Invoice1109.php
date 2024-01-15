


<style>
     @media screen and (max-width: 576px){

    .h2_01 {
        font-weight: 600;
        color: #005478;
        font-size: 20px;
        width: 160px;
        position: absolute;
    }
     .thanks-img {
        text-align: right;

    }
    .thanks-img img {
        width: 142px;
    }
    .msgBox {
        width: 100%;       
    }

    h2.heading.h2_01::after {
       display: none !important;
    }
}
</style>

    <div class="">
        <img src="/assets/gadget/img/vector4.png" class="vector4img">
    </div>
    <div class="container my-5">
        <div class="row">
            <div class="col-sm-6" data-aos="fade-right">
                <h2 class="heading h2_01">Your <?php echo $gadgetNames; ?> has been insured.</h2>

                <div class="thanks-img">
                    <img src="/assets/gadget/img/thanks.png">
                </div>
            </div>
            <div class="col-sm-6" data-aos="fade-left">
                <div class="msg form msgBox">
                    <h4 class="heading">Thank You for choosing us.</h4>
                    <img src="/assets/gadget/img/thanksicon.png">
                    <input type="hidden" id="lead_id" value="<?php echo  $lead_id; ?>">
                    <p class="text-center"><b>Certificate No:<?php echo $coi_no; ?></b></p>
                    <button class="btn btn-buy" id="coidownload">Download COI <i class="fa fa-download"></i>                           </button>
                </div>
            </div>
        </div>
        <div class="thq">
            <img src="/assets/gadget/img/vector1.png" class="vector-img">
        </div>
    </div>
</div>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script type="text/javascript" src='<?PHP echo base_url('assets/js/html2pdf.bundle.js', PROTOCOL); ?>'></script>
<script>
    AOS.init({
        duration: 0,
    });
    $(document).on("click", "#coidownload", function() {
       // ajaxindicatorstart("Downloading...");
        var lead_id = $("#lead_id").val();
        $.ajax({
            url: "/GadgetInsurance/coidownload",
            type: "POST",
            data: {
                'lead_id': lead_id,
            },
            dataType: 'html',
            success: function(response) {
                html2pdf()
                    .set({
                        filename: 'COI_' + lead_id + '.pdf'
                    })
                    .from(response)
                    .save();
                setTimeout(function() {
                    ajaxindicatorstop();
                }, 5000);
            }
        });
    });
</script>
</body>
</html>
