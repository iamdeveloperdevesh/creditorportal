
<?php
$customer_details=$customer_details['customer_details'];
//$nominee_relations=$nominee_relations;
$logo=$this->session->userdata('logo');
?>
<style type="text/css">
.proceedBtn{
    display: none;
}
.Seebtn{
    display: none;
}
    @media only screen and (max-width: 576px) {
        .qut.mobile_vsb {
            display: none;
        }
        .rightpannel.mobile_vsb {
            display: none;
        }
        .btn.mobile_vsb { display: none; }
        button.btn.btn-buy.proceedBtn {
            height: 30px;
        }
        .proposal-form b.prdctName {
            line-height: unset;
        }
        .proposal-form figure {
            margin-bottom: 0;
        }
        proposal-form.mobile_section {
            padding-bottom: 20px;
        }
        .proceedBtn{
            display: block;
        }
        .Seebtn{
            display: block;
            height: 30px;
            margin-left: 5px;
        }
        button.btn.btn-buy.proceedBtn {
            height: 40px;
        }
        img.prdct_mblImg {
            height: 27px;
        }
        .proposal-form figure {

            text-align: center;
            padding: 2px 2px;

            height: 37px;
            max-width: 40px;
            width: 41px;
        }
        .proposal-form b.prdctName {
            line-height: unset;
            font-size: 10px;
            text-align: center;
        }
        button.btn.btn-orange.btn-sm.Seebtn {
            font-size: 10px;
        }
        .Seebtn .fa-angle-right {
            font-size: 13px !important;
            padding-left: 1px;
        }

        button.btn.btn-buy.proceedBtn {
            font-size: 12px !important;
            margin-left: 5px;
            margin-top: 0;
        }
        .d-flex.fotter_box {
            align-items: center;
            justify-content: space-between;
        }
        .proposal-form figure {
            margin-bottom: 0;
        }
        .proposal-form {
           /* margin-right: 1rem !important;
            margin-left: 1rem;*/
        }
        .proposal-form {
            margin-right: 0 !important;
            margin-left: 0;
        }
        .proposal-form {

            padding: 10px 16px 0px;
        }
        .mobile_fxd.proposal-form {
            position: fixed;
            bottom: 0;
            border: 1px solid #e9e9e9;
            border-radius: 0;
        }
        .mobile_propsalForm.proposal-form.form {
            margin-bottom: 58px;
        }
    }

</style>

            <div class="container m-b-0 mt-5 quotes genralCompare">
                <div class="row">
                    <div class="col-sm-12 dflex-btn">
                        <div class="d-flex">
                            <a onclick="history.back()"> <div class="back-btn">
                                <button><i class="fa fa-long-arrow-left"></i> Go Back</button>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="sideimg">
                        <img src="/assets/gadget/img/vector-2.png">
                    </div>
                    <div class="col-sm-7 mobile_propsalForm proposal-form form mt-3">
                        <h3 class="compare">Proposal form</h3>
                        <form id="proposal_form">
                            <div class="row mt-4">
                                <div class="col-sm-2 form-group namelabel pr-0">
                                    <label class="mt-3"></label><select class="form-control" disabled="" name="salutation"id="salutation">
                                            <option value="Mr"   <?php if($customer_details['salutation'] == "Mr"){echo"selected";}?>>Mr.</option>
                                            <option value="Mrs"  <?php if($customer_details['salutation'] == "Mrs"){echo"selected";}?>>Mrs.</option>

                                    </select>
                                </div>
                                <div class="col-sm-4 form-group pl-0">
                                    <label class="m-l5">First Name</label>
                                    <input type="text" class="form-control details" readonly name="first_name"id="first_name" value="<?php echo $customer_details['first_name'];?>" placeholder="Enter Full Name">
                                </div>
                                <div class="col-sm-6 form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="last_name"id="last_name" readonly class="form-control details" value="<?php echo $customer_details['last_name'];?>" placeholder="Enter Last Name">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 form-group">
                                    <label>Gender</label>
                                    <div class="d-flex form-control pt-3">
                                        <label class="checkquote">
                                            Male
                                            <input <?php if($customer_details['gender'] == "Male"){echo"checked";}?> onclick="return false" type="radio" class="details" name="gender" value="Male">
                                            <span class="checkmark"></span>
                                        </label>
                                        <label class="checkquote">
                                            Female
                                            <input <?php if($customer_details['gender'] == "Female"){echo"checked";}?> onclick="return false" type="radio" class="details" name="gender" value="Female">
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 form-group">
                                    <label>Email Id</label>
                                    <input type="text" class="form-control details" readonly value="<?php echo $customer_details['email_id'];?>"  name="email"id="email" placeholder="Enter Email">
                                </div>
                            </div>
                           <!-- <div class="row">
                                <div class="col-sm-6 form-group">
                                    <label>Nominee Name</label>
                                    <input type="text" class="form-control details" value="<?php /*echo $NomineeDetails->nominee_first_name ;*/?>"  name="nominee_name" id="nominee_name" placeholder="Enter Name">
                                </div>
                                <div class="col-sm-6 form-group">
                                    <label>Nominee Relationship</label>
                                    <select class="form-control details" id="NomineeRelation" name="NomineeRelation">
                                        <?php
/*                                        $option='<option selected disabled>Select</option>';
                                            foreach ($nominee_relations as $nom){
                                               // print_r($nom);
                                                $select='';
                                                if($NomineeDetails->nominee_relation == $nom['id']){
                                                    $select='selected';
                                                }
                                                 $option .='<option '.$select.' value="'.$nom['id'].'"  >'.$nom['name'].'</option>';
                                            }
                                            echo $option;
                                        */?>
                                    </select>
                                </div>
                            </div>-->
                            <div class="row">

                                <div class="col-sm-6 form-group">
                                    <label>Make</label>
                                    <input type="text" name="make"id="make" value="<?php echo $customer_details['make'];?>" class="form-control details" placeholder="Make">
                                </div>
                                <div class="col-sm-6 form-group">
                                    <label> Model</label>
                                    <input type="text" name="unique_number"id="unique_number" value="<?php echo $customer_details['unique_number'];?>" class="form-control details" placeholder="Model">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 form-group">
                                    <label>Pin Code</label>
                                    <input type="text" name="pincode"id="pincode" value="<?php echo $customer_details['pincode'];?>" class="form-control details" placeholder="Enter pincode">
                                </div>
                                <!--<div class="col-sm-6 form-group">
                                    <label>Upload Invoice</label>
                                    <input id="demo2" class=" details" type="file"  placeholder="Drag and drop, or Browser yor file" name="demo2" />
                                  <input type="text" class="form-control" placeholder="Enter Full Name">
                                </div>-->
                            </div>
                            <div class="">
                                <p><label class="checkquote">
                                        I confirm all the details shared are correct and accurate as per my knowledge and I agree with all the Terms and Conditions.
                                    <input type="checkbox" id="termscondition"  class="details" name="termscondition">
                                    <span class="checkmark"></span>
                                  </label></p>
                            </div>


                    </div>

                    <div class="col-sm-4 mobile_fxd proposal-form mt-3" >
                        <div class="d-flex fotter_box mb-3">
                            <figure>
                                <img src="<?php echo $logo; ?>">
                            </figure>
                            <b class="ml-2 prdctName"><?php echo $post_data['plan_name']; ?></b>
                            <button class="btn btn-orange btn-sm  Seebtn" type="button" data-toggle="modal"
                                    data-target="#detailModal">See Details <i class="fa fa-angle-right" aria-hidden="true"></i>
                            </button>
                            <button class="btn btn-buy proceedBtn" type="button" onclick="proceedTopay()">PROCEED TO  PAY </button>
                        </div>
                        <div class="row dflex  mt-3 qut mobile_vsb">
                            <div class="border-right">
                                <p>Sum Insured (in ₹)</p>
                                <b><?php echo $post_data['cover']; ?></b>
                            </div>
                            <div class="border-right">
                                <p>Premium</p>
                                <b><i class="fa fa-inr" aria-hidden="true"></i><?php echo $post_data['premium']; ?></b>
                            </div>
                            <div>
                                <p>Tenure (Year)</p>
                                <b>1</b>
                            </div>
                        </div>
                        <div class="mt-4 rightpannel mobile_vsb">
                            <?php
                            if(count($features) >0 ){
                                foreach ($features as $item){ ?>


                                    <p><img src="/assets/gadget/img/icon.png">

                                        <?php echo $item->short_description ;?> <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo strip_tags($item->long_description) ;?>"></i></p>
                            <?php    }
                            }else{
                                ?>

                                <p><img src="/assets/gadget/img/icon.png"> Complimentary Add-on Benefit <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title=" Complimentary Add-on Benefit"></i></p>
                                <p><img src="/assets/gadget/img/icon.png">Emergency Travel Assistance & Worldwide coverage <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title=" Complimentary Add-on Benefit"></i></p>
                                <p><img src="/assets/gadget/img/icon.png"> Emergency Roadside Assistance <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title=" Complimentary Add-on Benefit"></i></p>
                                <p><img src="/assets/gadget/img/icon.png"> Emergency Roadside Assistance <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title=" Complimentary Add-on Benefit"></i></p>
                                <p><img src="/assets/gadget/img/icon.png"> Emergency Travel Assistance & Worldwide coverage <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title=" Complimentary Add-on Benefit"></i></p>
                                <p><img src="/assets/gadget/img/icon.png"> Why do we use it? <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title=" Complimentary Add-on Benefit"></i></p>
                                <p><img src="/assets/gadget/img/icon.png"> Where can I get some? <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title=" Complimentary Add-on Benefit"></i></p>
                                <?php
                            }
                            ?>

                        </div>
                    </div>
                    <div class="row prposal">
                        <button class="btn btn-buy mobile_vsb" type="button" onclick="proceedTopay()">PROCEED TO  PAY </button>
                    </div>
                </div>
                </form>
            </div>
            <div class="sideimg-left">
                <img src="/assets/gadget/img/Vector-3.png">
            </div>

    </div>
<div class="modal fade" id="detailModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal body -->
            <div class="modal-body p-0">
                <button type="button" class="close p-2" data-dismiss="modal">&times;</button>
                <div class="row">
                    <div class="p-3  b-g-dark col-md-4 modalquote">
                        <figure>
                            <img src="/assets/gadget/img/img1.png">
                        </figure>
                        <p><b id="prodName"></b></p>
                        <hr/>
                        <div class="modal_compare">
                            <p><span>Sum Insured (in : <i class="fa fa-inr"></i>)</span>

                                <span class="f-right" style="" id="sumInsureModR"><?php echo $post_data['cover']; ?></span>
                            </p>
                            <p><span> Premium (in : <i class="fa fa-inr"></i>)</span> <span class="f-right"
                                                                                            id="PremiumMod"><?php echo $post_data['premium']; ?></span>
                            </p>
                            <p><span>Tenure (Year)</span> <span class="f-right">1</span></p>
                        </div>
                        <div>
                            <button class="btn btn-download"><i class="fa fa-download"></i> Download Brochure
                            </button>
                        </div>

                    </div>
                    <div class="col-md-8 modal-right">
                        <!--  <p> Own a high-value cycle? Love your fitness partner? Make sure you secure it with our
                              Cycle Protect Plan. A plan that ensures you can ride on worry-free. Get worldwide
                              emergency travel & roadside assistance with the entertainment package of annual
                              membership for Live TV (ZEE5). This plan can be availed within 45 days of cycle
                              purchase.</p>-->
                        <h5>Key Features & Benefits</h5>
                        <div class="features">

                            <?php
                            if(count($features) >0 ){
                                foreach ($features as $item){ ?>
                            <div class="detail-box">
                                <b class="p-0"><b><img src="/assets/gadget/img/icon.png">
                                        <?php echo $item->short_description ;?> </b>
                                    <p><?php echo strip_tags($item->long_description) ;?></p>
                            </div>


                                <?php    }
                            }
                            ?>
                        </div>


                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
    <script>
        $(function () {
             $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
    <script src="/assets/gadget/js/script.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 0,
         });
        let post_data = '';
      $(document).ready(function () {
            post_data = JSON.parse('<?php echo json_encode($post_data); ?>');
            console.log(post_data);

        });
        function proceedTopay(){
            $('#salutation').prop('disabled', false);
            var salutation = $.trim($('#salutation').val());
            var first_name = $.trim($('#first_name').val());
            var last_name = $.trim($('#last_name').val());
            var email = $.trim($('#email').val());
            var pincode = $.trim($('#pincode').val());
         //   var nominee_name = $.trim($('#nominee_name').val());
          //  var NomineeRelation = $.trim($('#NomineeRelation').val());
            var unique_number = $.trim($('#unique_number').val());
            var make = $.trim($('#make').val());
            var gender = $.trim($('input[name="gender"]:checked').val());
            var termscondition = $.trim($('input[name="termscondition"]:checked').val());
            $('.details').closest('div').find('span.error').remove();
            hasError = false;
            if (first_name == '') {
                hasError = true;
                $('#first_name').closest('div').append('<span class="error">Name is required</span>');
            }

            if (salutation == '') {
                hasError = true;
                $('#salutation').closest('div').append('<span class="error">Salutation is required</span>');
            }
            if (last_name == '') {
                hasError = true;
                $('#last_name').closest('div').append('<span class="error">Last Name is required</span>');
            }
            if (email == '') {
                hasError = true;
                $('#email').closest('div').append('<span class="error">Email is required</span>');
            }
            if (pincode == '') {
                hasError = true;
                $('#pincode').closest('div').append('<span class="error">Pincode is required</span>');
            }
           /* if (nominee_name == '') {
                hasError = true;
                $('#nominee_name').closest('div').append('<span class="error">Nominee name is required</span>');
            }
            if (NomineeRelation == '') {
                hasError = true;
                $('#NomineeRelation').closest('div').append('<span class="error">Nominee relation is required</span>');
            }*/
            if (unique_number == '') {
                hasError = true;
                $('#unique_number').closest('div').append('<span class="error">This field is required</span>');
            }
            if (make == '') {
                hasError = true;
                $('#make').closest('div').append('<span class="error">This field is required</span>');
            }
            if (gender == '') {
                hasError = true;
                $('#gender').closest('div').append('<span class="error">Gender is required</span>');
            }

            if (termscondition != 'on') {

                hasError = true;
                $('#termscondition').closest('div').append('<span class="error">Please accept Terms & Conditions.</span>');
            }

          /*  if ( document.getElementById("demo2").files.length > 0) {

            } else {
                hasError = true;
                $('#demo2').closest('div').append('<span class="error">Invoice is required</span>');
            }*/
            if(hasError){
                return;
            }
           /* data1 = {};
            data1.salutation = salutation;
            data1.first_name = first_name;
            data1.last_name = last_name;
            data1.email = email,
            data1.gender = gender;
            data1.nominee_name = nominee_name;
            data1. = NomineeRelation;
            data1.pincode = pincode;
            data1.termscondition = termscondition;
            data1.post_data = post_data;*/
            //var form_data = document.getElementById('proposal_form');
            let formData = new FormData();
            formData.append("salutation", salutation);
            formData.append("first_name", first_name);
            formData.append("last_name", last_name);
            formData.append("email", email);
            formData.append("gender", gender);
           /* formData.append("nominee_name", nominee_name);
            formData.append("NomineeRelation", NomineeRelation);*/
            formData.append("pincode", pincode);
            formData.append("termscondition", termscondition);
            formData.append("unique_number", unique_number);
            formData.append("make", make);
         //   formData.append("post_data", post_data);
            for ( var key in post_data ) {
                formData.append(key, post_data[key]);
            }
           /* console.log(document.getElementById("demo2").files.length);
            if ( document.getElementById("demo2").files.length > 0) {
                formData.append("newfile", document.getElementById("demo2").files[0]);
            } else {

            }*/
            $.ajax({
                url: "/GadgetInsurance/update_customer_nominee_details",
                type: "POST",
                data: formData,
                dataType: "json",
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    data = {};
                    data.post_data = post_data;

                    $.ajax({
                        url: "/GadgetInsurance/create_proposal",
                        type: "POST",
                        async: false,
                        data: data,
                        dataType: "json",
                        cache: false,
                        clearForm: false,
                        success: function(response) {

                            // swal("success", "Details updated successfully !", "success");
                            if(response.status == false){
                                alert(response.messages);
                            }else{
                                window.location.href = '/GadgetInsurance/redirect_to_pg';

                            }


                        }
                    });
                }

           // $.post("/GadgetInsurance/update_customer_nominee_details", formData, function(e) {

            });
         //   })


        }

      /*  $("#proposal_form").validate({
            ignore: ".ignore",
            rules: {
                salutation: {
                    required: true
                },
                first_name: {
                    required: true
                },
                last_name: {
                    required: true
                },
                gender: {
                    required: true,
                },
                email: {
                    required: true,
                },
                nominee_name: {
                    required: true,
                },
                NomineeRelation: {
                    required: true,
                },
                /!*invoice: {
                    required: true,
                },*!/
                pincode: {
                    required: true,
                },
                termscondition: {
                    required: true,
                }
            },
            messages: {

            },
            submitHandler: function(form) {
                var form = $("#nominee-form").serialize();
                form.append("post_data", post_data);
                $.post("/GadgetInsurance/update_customer_nominee_details", form, function(e) {

                    var get_policy_id = $('#hiddenpolicyid').val();
                    var get_premium = $('#hiddenpremium').val();

                    data = {};
                    data.policy_id = get_policy_id;
                    data.premium = get_premium;

                    $.ajax({
                        url: "/GadgetInsurance/create_proposal",
                        type: "POST",
                        async: false,
                        data: data,
                        dataType: "json",
                        success: function(response) {

                            swal("success", "Nomineee details updated successfully !", "success");
                            window.location = "/quotes/proposal_summary";


                        }
                    });
                })

            }
        });*/




       </script>
</body>
</html>
