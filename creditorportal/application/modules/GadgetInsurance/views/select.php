<style>

    .form-group.Icon-inside input[type=file]::file-selector-button {
        background: #005478;
        border: #005478;
        border-radius: 2px;
          color: #fff;
    }
    .sk-circle-wrapper {
        background: rgb(57 57 57);
        opacity: 0.7;
        width: 100%;
        height: 100%;
        text-align: center;
        vertical-align: middle;
        position: fixed;
        inset: 0px;
        margin: auto;
        font-size: 16px;
        z-index: 10;
        color: rgb(0, 0, 0);
    }

    .sk-circle {
        width: 120px;
        height: 120px;
        position: absolute;
        inset: 5% 0px 0px;
        margin: auto;
    }
    .sk-circle .sk-child {
        width: 100%;
        height: 100%;
        position: absolute;
        left: 0;
        top: 0;
    }
    .sk-circle .sk-child:before {
        content: '';
        display: block;
        margin: 0 auto;
        width: 15%;
        height: 15%;
        background-color:#2cd44a;;
        border-radius: 100%;
        -webkit-animation: sk-circleBounceDelay 1.2s infinite ease-in-out both;
        animation: sk-circleBounceDelay 1.2s infinite ease-in-out both;
    }
    .sk-circle .sk-circle2 {
        -webkit-transform: rotate(30deg);
        -ms-transform: rotate(30deg);
        transform: rotate(30deg); }
    .sk-circle .sk-circle3 {
        -webkit-transform: rotate(60deg);
        -ms-transform: rotate(60deg);
        transform: rotate(60deg); }
    .sk-circle .sk-circle4 {
        -webkit-transform: rotate(90deg);
        -ms-transform: rotate(90deg);
        transform: rotate(90deg); }
    .sk-circle .sk-circle5 {
        -webkit-transform: rotate(120deg);
        -ms-transform: rotate(120deg);
        transform: rotate(120deg); }
    .sk-circle .sk-circle6 {
        -webkit-transform: rotate(150deg);
        -ms-transform: rotate(150deg);
        transform: rotate(150deg); }
    .sk-circle .sk-circle7 {
        -webkit-transform: rotate(180deg);
        -ms-transform: rotate(180deg);
        transform: rotate(180deg); }
    .sk-circle .sk-circle8 {
        -webkit-transform: rotate(210deg);
        -ms-transform: rotate(210deg);
        transform: rotate(210deg); }
    .sk-circle .sk-circle9 {
        -webkit-transform: rotate(240deg);
        -ms-transform: rotate(240deg);
        transform: rotate(240deg); }
    .sk-circle .sk-circle10 {
        -webkit-transform: rotate(270deg);
        -ms-transform: rotate(270deg);
        transform: rotate(270deg); }
    .sk-circle .sk-circle11 {
        -webkit-transform: rotate(300deg);
        -ms-transform: rotate(300deg);
        transform: rotate(300deg); }
    .sk-circle .sk-circle12 {
        -webkit-transform: rotate(330deg);
        -ms-transform: rotate(330deg);
        transform: rotate(330deg); }
    .sk-circle .sk-circle2:before {
        -webkit-animation-delay: -1.1s;
        animation-delay: -1.1s;
        background-color:#0182ff;             }
    .sk-circle .sk-circle3:before {
        -webkit-animation-delay: -1s;
        animation-delay: -1s;
        background-color:#2cd44a; }
    .sk-circle .sk-circle4:before {
        -webkit-animation-delay: -0.9s;
        animation-delay: -0.9s;
        background-color:#2cd44a; }
    .sk-circle .sk-circle5:before {
        -webkit-animation-delay: -0.8s;
        animation-delay: -0.8s;
        background-color:#0182ff; }
    .sk-circle .sk-circle6:before {
        -webkit-animation-delay: -0.7s;
        animation-delay: -0.7s;
        background-color:#2cd44a; }
    .sk-circle .sk-circle7:before {
        -webkit-animation-delay: -0.6s;
        animation-delay: -0.6s;
        background-color:#0182ff; }
    .sk-circle .sk-circle8:before {
        -webkit-animation-delay: -0.5s;
        animation-delay: -0.5s;
        background-color:#2cd44a; }
    .sk-circle .sk-circle9:before {
        -webkit-animation-delay: -0.4s;
        animation-delay: -0.4s;
        background-color:#0182ff; }
    .sk-circle .sk-circle10:before {
        -webkit-animation-delay: -0.3s;
        animation-delay: -0.3s;
        background-color:#2cd44a; }
    .sk-circle .sk-circle11:before {
        -webkit-animation-delay: -0.2s;
        animation-delay: -0.2s;
        background-color:#0182ff; }
    .sk-circle .sk-circle12:before {
        -webkit-animation-delay: -0.1s;
        animation-delay: -0.1s;
        background-color:#2cd44a; }

    @-webkit-keyframes sk-circleBounceDelay {
        0%, 80%, 100% {
            -webkit-transform: scale(0);
            transform: scale(0);
        } 40% {
              -webkit-transform: scale(1);
              transform: scale(1);
          }
    }

    @keyframes sk-circleBounceDelay {
        0%, 80%, 100% {
            -webkit-transform: scale(0);
            transform: scale(0);
        } 40% {
              -webkit-transform: scale(1);
              transform: scale(1);
          }
    }
</style>
<?php
$lead_id=$_GET['Lead'];
$customer_id=$_GET['customer_id'];
?>


            <div class="container my-5">
                <div class="sk-circle-wrapper" style="display: none">
                    <div class="sk-circle">
                        <div class="sk-circle1 sk-child"></div>
                        <div class="sk-circle2 sk-child"></div>
                        <div class="sk-circle3 sk-child"></div>
                        <div class="sk-circle4 sk-child"></div>
                        <div class="sk-circle5 sk-child"></div>
                        <div class="sk-circle6 sk-child"></div>
                        <div class="sk-circle7 sk-child"></div>
                        <div class="sk-circle8 sk-child"></div>
                        <div class="sk-circle9 sk-child"></div>
                        <div class="sk-circle10 sk-child"></div>
                        <div class="sk-circle11 sk-child"></div>
                        <div class="sk-circle12 sk-child"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6" data-aos="fade-right" data-aos-offset="500"
                    data-aos-duration="500">
                    <h2 class="heading head_name">Gadgets Insurance</h2>
                    <p id="insurance_text">Protect your valuable gadgets with our comprehensive gadget insurance. From smartphones and laptops to tablets and cycles, our policy covers accidental damage, theft, and loss so you can know that your devices are always secure. With affordable premiums and an easy claims process, you can enjoy your gadgets without worrying about the unexpected.

                        <br>  <b>Get your gadget insurance today!</b></p>
                    <div class="home-img mt-5">
                        <img id="desc_img"src="/assets/gadget/img/home-1.png">
                    </div>
                    </div>
                    <div class="col-sm-6" data-aos="fade-left">
                        <div class="form">
                            <h5>Select Your Cover </h5>
                            <div class="progress">
                                <div class="progress-bar bg-orange" style="width:100%">                                    
                                </div>                                    
                            </div>
                            <ul class="nav nav-pills" role="tablist">
                                <?php
                                if(isset($subtype)){
                                    $i=0;
                                    $hiddden=0;
                                    foreach ($subtype as $item){
                                        $active='';
                                        if($i == 0){
                                            $active='';
                                            $hiddden=$item->policy_sub_type_id;
                                        }
                                        ?>
                                        <li class="nav-item">
                                           <?php echo $abc= '<a class="nav-link '.$active.'" onclick="getPolicysubtype_id(\''.$item->policy_sub_type_id.'\',\''.$item->description.'\',\''.$item->logo.'\',\''.$item->policy_sub_type_name.'\',\''.$item->desc_img.'\')" data-toggle="pill" href="#home"><img width="38" heigh="39" src="'.$item->logo.'"></a>'.$item->policy_sub_type_name; ?>
                                        </li>
                                  <?php
                                    $i++;
                                    }
                                }
                                ?>


                              </ul>
                              <div class="tab-content">
                                <div id="home" class="tab-pane active" style="display: none"><br>

                                    <div class="form-group Icon-inside">
                                        <label>Upload Invoice</label>
                                        <form id = "fileUploadForm" action="<?php //echo base_url() . "/GadgetInsurance/uploadInvoice" ?>" enctype="multipart/form-data" method="POST">

                                        <input id="demo2" class=" form-control details"  type="file" placeholder="Drag and drop, or Browser yor file" name="file" style="" onchange="ocrupload()">
                                        </form>


                                                  </div>
                                    <form>

                                        <div class="form-group Icon-inside">
                                            <label>How much did you buy it for?</label>
                                            <input type="number"  class="form-control p-l-3 price details" placeholder="Enter Price">
                                            <i class="fa fa-inr" aria-hidden="true"></i>
                                        </div>
                                         <div class="form-group Icon-inside">
                                            <label>When did you purchase your Gadget?</label>
                                            <input type="date"  class="form-control p-l-3 daterange details" name="daterange" >
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <div class="form-group Icon-inside marinediv" style="display: none">
                                            <label>Subject Matter Insured</label>
                                           <select class="form-control" id="subject_matter_insured" name="subject_matter_insured">
                                               <option id="Household Goods">Household Goods</option>
                                               <option id="Car">Car</option>
                                           </select>
                                            <!--<i class="fa fa-inr mr-2" style="left: 31px;" aria-hidden="true"></i>-->
                                        </div>
                                        <div class="form-group Icon-inside marinediv" style="display: none">
                                            <label>Type of Shipment</label>
                                            <select class="form-control" id="type_of_shipment" name="type_of_shipment">
                                                <option id="Inter">Inter-City</option>
                                                <option id="Intra">Intra-City</option>
                                            </select>
                                           <!-- <i class="fa fa-inr" style="left: 31px;" aria-hidden="true"></i>-->
                                        </div>
                                       <input type="hidden" id="policy_subtype_id" value="<?php echo $hiddden; ?>">
                                       <input type="hidden" id="logo" value="">
                                         <div class="dflex-btn">
                                             <a href="/GadgetInsurance"> <div class="previuos-btn">
                                                 <button><i class="fa fa-long-arrow-left"></i></button>
                                                 </div>
                                             </a>
                                              <div class="next-btn">
                                                 Next  <button type="button" class="submitPurchaseDetail"><i class="fa fa-long-arrow-right"></i></button>

                                         </a>
                                     </div>
                                    </form>
                                </div>


                              </div>                            
                        </div>                       
                    </div>   
                </div>
                <div class="">
                    <img src="/assets/gadget/img/vector1.png" class="vector-img">
                </div>
            </div>
    </div> 
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
    function ocrupload()
        {
            var formData = new FormData();
            formData.append('imageFile', $('#demo2')[0].files[0]);
            $('.sk-circle-wrapper').show();

            $.ajax({
                url : '/GadgetInsurance/uploadInvoice',
                type : 'POST',
                data : formData,
                processData: false,
                contentType: false,
                success : function(data) {
                    debugger;
                    $('.sk-circle-wrapper').hide();

                    var res = JSON.parse(data);
                  //  console.log(res.total);
                    if(res.msg=="success") {
                        $(".price").val(res.total);
                        $(".daterange").val(res.daterange);
                    }



                }
            });
        }
        AOS.init({
        duration: 0,
        });
        /*$(function() {
          $('input[name="daterange"]').daterangepicker({
            opens: 'left'
          }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
          });
        });*/
        function getPolicysubtype_id(id,desc,logo,head_name,desc_img){
            $('.marinediv').hide();
            if(id == 19){
                $('.marinediv').show();
            }
            $("#insurance_text").html('');
            $("#insurance_text").html(desc);
            $("#home").show();
            $("#policy_subtype_id").val(id);
            $("#logo").val(logo);
            $(".head_name").html(head_name);
            $("#desc_img").attr('src',desc_img);

        }
        $('.submitPurchaseDetail').on('click', function(event) {

         //   $('.details').closest('div').find('span.error').remove();

            var price = $.trim($('.price').val());
            var daterange = $.trim($('.daterange').val());
            var policy_subtype_id = $.trim($('#policy_subtype_id').val());
            var logo = $.trim($('#logo').val());
            var subject_matter_insured = $.trim($('#subject_matter_insured').val());
            var type_of_shipment = $.trim($('#type_of_shipment').val());
            //var re = new RegExp('^[6-9][0-9]{9}$');
            $('.details').closest('div').find('span.error').remove();
            hasError = false;

            if (price == '') {
              //  alert();
                hasError = true;
                $('.price').closest('div').append('<span class="error">Purchase price is required</span>');
            }
            if (daterange == '') {

                hasError = true;
                $('.daterange').closest('div').append('<span class="error">Purchase date is required</span>');
            }


            if (hasError) {
                event.stopImmediatePropagation();
            }


            if (!hasError) {

                data = {};
                data.price = price;
                data.daterange = daterange,
                data.lead_id = '<?php echo $lead_id; ?>',
                data.logo = logo,
                data.subject_matter_insured = subject_matter_insured,
                data.type_of_shipment = type_of_shipment,
                    data.policy_subtype_id = policy_subtype_id;

                $.ajax({

                    url: '/GadgetInsurance/updateDetails',
                    method: 'POST',
                    data: data,
                    async: false,
                    cache: false,
                    success: function(response) {
                        var res = JSON.parse(response);
                        if(res.status_code == 200){
                            location.href = "/GadgetInsurance/quote?Lead="+data.lead_id;
                        }else{
                            alert("Something went wrong!");

                        }


                    }
                });
            }
            return false;
        });
        </script>
        
   <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script src="/assets/gadget/js/jquery.slim.min.js"></script>
    <script src="/assets/gadget/js/popper.min.js"></script>
    <script src="/assets/gadget/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>-->
   
</body>
</html>
