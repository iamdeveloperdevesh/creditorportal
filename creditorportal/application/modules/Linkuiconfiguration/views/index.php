<style>
    .bor-rr {
        border: 1px solid #000;
        border-radius: .25rem;
    }
    .over_border_txt_proposal_add {
        margin-top: -78px;
        position: absolute;
        margin-left: 20px;
        background: #fff;
        font-size: 15px;
        color: #000;
        font-weight: 900;
        padding: 0 5px;
        font-family: 'PFEncoreSansPro-book' !important;
    }
    fieldset.header-border {
        border: 1px groove #ddd !important;
        padding: 0 1.4em 1.4em 1.4em !important;
        margin: 0 0 1.5em 0 !important;
        -webkit-box-shadow:  0px 0px 0px 0px #000;
        box-shadow:  0px 0px 0px 0px #000;
        border-radius: .25rem;

    }

    legend.header-border {
        font-size: 1.2em !important;
        font-weight: bold !important;
        text-align: left !important;
        width:auto;
        padding:0 10px;
        border-bottom:none;
        color:#0B90C4;
    }
</style>
<div class="col-md-10">
    <div class="content-section mt-3">
        <div class="card">
            <div class="cre-head">
                <div class="row">
                    <div class="col-md-10 col-10">
                        <p>Link UI Configuartion - <i class="ti-user"></i></p>
                    </div>
                    <div class="col-md-2 col-2"></div>
                </div>
            </div>
            <div class="card-body">
                <form  id="form-validate" method="post" action="#">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <input type="hidden" id="is_update" name="is_update" value="0">
                            <label for="employee_fname" class="col-form-label">Configuartion For<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <select class="form-control" id="config_for" name="config_for" onchange="show_partner_Product(this.value)">
                                    <option value="" selected disabled>Select</option>
                                    <option value="1">Partner Link</option>
                                    <option value="2">Product Link</option>
                                </select>
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3" id="partner_div" style="display: none">
                            <label for="employee_fname" class="col-form-label">Partner Name<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <select class="form-control" id="creditor_id" name="creditor_id" onchange="getPartnerConfig(this.value)">

                                </select>
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3" id="product_div" style="display: none">
                            <label for="employee_fname" class="col-form-label">Product Name<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <select class="form-control" id="product_id" name="product_id" onchange="getPartnerConfig(this.value)">

                                </select>
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <fieldset class="header-border">
                        <legend class="header-border">Page Header</legend>
                        <div class="row ">
                            <div class="col-md-4">
                                <label for="employee_fname" class="col-form-label">First Page Header<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="fp_header" name="fp_header" placeholder="Enter Header...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="employee_fname" class="col-form-label">First Page Image<span class="lbl-star">*</span></label>
                                <div class="input-group" id="fp_image_div">
                                    <input type="file" class="form-control" id="fp_image" name="fp_image" >
                                    <!--                                <div id=""></div>-->
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="employee_fname" class="col-form-label">Lead page header text</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="lead_header_text" name="lead_header_text" placeholder="Enter Header Text...">
                                    </div>
                            </div>
                            <div class="col-md-4">
                                <label for="employee_fname" class="col-form-label">Loader Text</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="loader_text" name="loader_text" placeholder="Enter Logo Text...">
                                </div>
                            </div>

                        </div>
                    </fieldset>
                    <fieldset class="header-border">
                        <legend class="header-border">Quote Page</legend>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="employee_fname" class="col-form-label">Quote Card Logo<span class="lbl-star">*</span></label>
                                <div class="input-group" id="qt_image_div">
                                    <input type="file" class="form-control" id="qt_image" name="qt_image" >
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="employee_fname" class="col-form-label">Generate Quote Page Image<span class="lbl-star">*</span></label>
                                <div class="input-group" id="g_qt_image_div">
                                    <input type="file" class="form-control" id="g_qt_image" name="g_qt_image" >
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="employee_fname" class="col-form-label">Quote page header Text</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="quote_header_text" name="quote_header_text" placeholder="Enter quote Header Text...">
                                    </div>
                            </div>
                            <div class="col-md-4">
                                <label for="employee_fname" class="col-form-label">Quote page Text (on the right sided tab)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="quote_right_text" name="quote_right_text" placeholder="Enter quote Text...">
                                    </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="input-group" style="align-items: center;">
                                <label for="employee_fname" class="col-form-label">Know more option on quote card</label>
                                <input type="checkbox" class="ml-2" id="know_more_btn" onclick="show_pdf_div()" name="know_more_btn" value="1">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="deductible_text" class="col-form-label">Deductible info text</label>
                                <div class="input-group">
                                    <textarea id="deductible_text" class="form-control" name="deductible_text"></textarea>
                                </div>
                            </div>

                        </div>
                    </fieldset>
                    <fieldset class="header-border">
                        <legend class="header-border">Proposer Form Images</legend>

                        <div class="row">
                            <div class="col-md-3">
                                <label for="employee_fname" class="col-form-label">Proposal  Header Text</label>
                                <div class="input-group" id="proposal_header_text">
                                    <input type="text" class="form-control" id="proposal_text" name="proposal_text" placeholder="Enter Proposal Header Text...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="employee_fname" class="col-form-label">Proposal Details</label>
                                <div class="input-group" id="pf_image1_div">
                                    <input type="file" class="form-control" id="pf_image1" name="pf_image1" >
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="employee_fname" class="col-form-label">Insured Member Details</label>
                                <div class="input-group" id="pf_image2_div">
                                    <input type="file" class="form-control" id="pf_image2" name="pf_image2" >
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="employee_fname" class="col-form-label">Nominee Details</label>
                                <div class="input-group" id="pf_image3_div">
                                    <input type="file" class="form-control" id="pf_image3" name="pf_image3" >
                                </div>
                            </div>

                        </div>
                    </fieldset>

                    <fieldset class="header-border">
                        <legend class=" col-form-label header-border">Summary Page Images</legend>
                        <div class="row">

                            <div class="col-md-3">
                                <label for="employee_fname" class="col-form-label">Proposal Details</label>
                                <div class="input-group" id="sm_image1_div">
                                    <input type="file" class="form-control" id="sm_image1" name="sm_image1" >
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="employee_fname" class="col-form-label">Insured Member Details</label>
                                <div class="input-group" id="sm_image2_div">
                                    <input type="file" class="form-control" id="sm_image2" name="sm_image2" >
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="employee_fname" class="col-form-label">Nominee Details</label>
                                <div class="input-group" id="sm_image3_div">
                                    <input type="file" class="form-control" id="sm_image3" name="sm_image3" >
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="employee_fname" class="col-form-label">Summary Image(Right side)</label>
                                <div class="input-group" id="sm_image4_div">
                                    <input type="file" class="form-control" id="sm_image4" name="sm_image4" >
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <label for="employee_fname" class="col-form-label">First Page Features<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Enter First Page Features" id="features" name="features" >
                                </div>
                            </div>

                            <div class="col-md-4" >
                                <label for="employee_fname" class="col-form-label">Customer Support Number<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                     <input type="text" class="form-control" placeholder="Enter Customer Support Number" id="customer_support_number" name="customer_support_number" >
                                </div>
                            </div>
                            <div class="col-md-4" id="feature_div">

                            </div>
                            <input type="hidden" id="feature_array" name="feature_array[]">
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="employee_fname" class="col-form-label">Terms & Condition Text<span class="lbl-star">*</span></label>
                                <div class="input-group">
                                    <textarea id="tc_text" class="form-control" placeholder="Enter Terms & Condition Text" name="tc_text"></textarea>
                                </div>
                            </div>

                            <div class="col-md-4" id="upload_pdf_div" style="display:none">
                                <label for="employee_fname" class="col-form-label">Upload PDF</label>
                                <div class="input-group" id="know_more_pdf_div">
                                    <input type="file" class="form-control" id="know_more_pdf" name="know_more_pdf" >
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <div class="row mt-4">
                        <div class="col-md-1 col-6 text-left">
                            <button type="submit" class="btn smt-btn btn-primary">Save</button>
                        </div>

                        <div class="col-md-2 col-6 text-right">
                            <a href="<?php echo base_url(); ?>Linkuiconfiguration" tabindex="48">
                                <button type="button" class="btn cnl-btn" tabindex="49">Cancel</button>
                            </a>
                        </div>

                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    function show_partner_Product(id){
        empty_form();
        $("#partner_div").hide();
        $("#product_div").hide();
        if(id==1){
            $("#partner_div").show();
        }else{
            $("#product_div").show();
        }
    }
    function show_pdf_div(){
        $("#upload_pdf_div").hide();
        if ($('#know_more_btn').is(':checked')) {
            $("#upload_pdf_div").show();
        }
    }
    window.onload = function () {

        getClientName();
        getproductName();
    }
    $(function() {
        $("#date_of_joining").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true,
            maxDate: new Date()
        });

    });
    let feature_array=[];
    let count_li=0;
    $('#features').on('keypress',function(e) {

        if(e.which == 13) {
            if(feature_array.length >= 5){
                displayMsg("error", "More than 5 features not allowed");
                $(this).val('');
                return false;
            }
            var feature=$(this).val();
            feature_array.push(feature);
            $('#feature_array').val(feature_array);
            $('#feature_div').append('<li class="feature_li'+count_li+'">'+feature+'<button class="btn btn-link" type="button" onclick="del_feature('+count_li+')" style="margin-top: -4px;"><i class="fa fa-trash" style="color: red"></i></button></li>');
            /*    $.each(feature_array, function (i,val) {

                });*/
            count_li++;
            $(this).val('');
        }

    });
    function del_feature(index) {
        $('.feature_li'+index).remove();
        feature_array.pop(index);
        $('#feature_array').val(feature_array);
    }
    function getClientName() { //client_dropdown
        $.ajax({
            url: "/home/getClientData",
            type: "POST",
            dataType: 'json',
            success: function(response) {
                //<a class="dropdown-item" href="#">Booking Pending</a>
                var     html ='<option  ></option>';
                $.each(response.data, function (i) {
                    //    console.log(response.data[i].creaditor_name);
                    html += '<option value="'+response.data[i].creditor_id+'">'+response.data[i].creaditor_name+'</option>';
                });
                $('#creditor_id').html(html);
                $('#creditor_id').select2({
                    placeholder: "Select"
                });
                // alert();


            }
        });
    }
    function getproductName() { //client_dropdown
        $.ajax({
            url: "/Linkuiconfiguration/getproductName",
            type: "POST",
            dataType: 'json',
            success: function(response) {
                var     html ='<option  ></option>';
                $.each(response.data, function (i) {
                    html += '<option value="'+response.data[i].plan_id+'">'+response.data[i].plan_name+'</option>';
                });
                $('#product_id').html(html);
                $('#product_id').select2({
                    placeholder: "Select"
                });

            }
        });
    }



    $('#form-validate').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
    var vRules = {
        config_for:{required:true},
        creditor_id:{required:true},
        product_id:{required:true},
        fp_header:{required:true},
        fp_image:{required:true},
        qt_image:{required:true},
        g_qt_image:{required:true},
        customer_support_number:{required:true},
        tc_text:{required:true},
        know_more_pdf:{required:true},
    };

    $("#form-validate").validate({
        rules: vRules,
        submitHandler: function(form)
        {
            $("#form-validate").ajaxSubmit({
                url: "<?php echo base_url();?>Linkuiconfiguration/submitForm",
                type: 'post',
                dataType: 'JSON',
                cache: false,
                clearForm: false,
                beforeSubmit : function(arr, $form, options){
                    if(feature_array.length == 0){
                        displayMsg("error", "Features are mandatory");
                        return false;
                    }
                },
                success: function (response)
                {
                    //$(".btn-primary").show();
                    if(response.code==200)
                    {
                        displayMsg("success", response.msg);

                    }
                    else
                    {
                        displayMsg("error", response.msg);
                        return false;
                    }
                }
            });
        }
    });

    function getPartnerConfig(id){
        var config_for=$('#config_for').val();
        $.ajax({
            url: "/Linkuiconfiguration/getconfiguaration",
            type: "POST",
            dataType: 'json',
            data:{id,config_for},
            success: function(response) {
                if(response.code == 200){
                    $("#is_update").val(1);
                    $("#loader_text").val(response.data.Loader_text);
                    $("#lead_header_text").val(response.data.lead_header_text);
                    $("#quote_header_text").val(response.data.quote_header_text);
                    $("#quote_right_text").val(response.data.quote_right_text);
                    $("#fp_header").val(response.data.first_page_header);
                    $("#proposal_text").val(response.data.proposal_header_text);
                    $("#customer_support_number").val(response.data.customer_support_number);
                    $("#tc_text").val(response.data.tc_text);
                    $("#deductible_text").val(response.data.deductible_text);
                    var first_page_features=response.data.first_page_features;
                    feature_array=first_page_features.split(',');
                    $('#feature_array').val(first_page_features);

                    count_li=0;
                    $.each(feature_array, function (i,val) {
                        $('#feature_div').append('<li class="feature_li'+count_li+'">'+val+'<button class="btn btn-link" type="button" onclick="del_feature('+count_li+')" style="margin-top: -4px;"><i class="fa fa-trash" style="color: red"></i></button></li>');
                        count_li++;
                    });
                    var fp_image= getImagePreview(response.data.first_page_image,'fp_image');
                    $("#fp_image_div").html(fp_image);
                    var g_qt_image= getImagePreview(response.data.generate_quote_image,'g_qt_image');
                    $("#g_qt_image_div").html(g_qt_image);
                    var qt_image= getImagePreview(response.data.quote_card_image,'qt_image');
                    $("#qt_image_div").html(qt_image);
                    if(response.data.summary_page_image1 != 0){
                        var sm_image1= getImagePreview(response.data.summary_page_image1,'sm_image1');
                        $("#sm_image1_div").html(sm_image1);
                    }
                    if(response.data.summary_page_image2 != 0) {
                        var sm_image2 = getImagePreview(response.data.summary_page_image2, 'sm_image2');
                        $("#sm_image2_div").html(sm_image2);
                    }

                    if(response.data.summary_page_image3 != 0) {
                        var sm_image3 = getImagePreview(response.data.summary_page_image3, 'sm_image3');
                        $("#sm_image3_div").html(sm_image3);
                    }
                    if(response.data.summary_page_image3 != 0) {
                        var sm_image4 = getImagePreview(response.data.summary_page_image4, 'sm_image4');
                        $("#sm_image4_div").html(sm_image4);
                    }
                    if(response.data.know_more_pdf != 0) {
                        $("#know_more_btn").prop('checked',true);
                        show_pdf_div();
                        var know_more_pdf = getImagePreview(response.data.know_more_pdf, 'know_more_pdf');
                        $("#know_more_pdf_div").html(know_more_pdf);
                    }
                    if(response.data.proposer_details_image != 0 && response.data.proposer_details_image != null){
                        var pf_image1= getImagePreview(response.data.proposer_details_image,'pf_image1');
                        $("#pf_image1_div").html(pf_image1);
                    }
                    if(response.data.insured_detail_image != 0 && response.data.insured_detail_image != null) {
                        var pf_image2 = getImagePreview(response.data.insured_detail_image, 'pf_image2');
                        $("#pf_image2_div").html(pf_image2);
                    }

                    if(response.data.nominee_detail_image != 0 && response.data.nominee_detail_image != null) {
                        var pf_image3 = getImagePreview(response.data.nominee_detail_image, 'pf_image3');
                        $("#pf_image3_div").html(pf_image3);
                    }
                }else{
                    empty_form();

                }


            }
        });
    }
    function empty_form() {
        $("#is_update").val(0);
        $("#loader_text").val('');
        $("#fp_header").val('');
        feature_array=[];
        $('#feature_array').val('');
        $('#feature_div').html('');
        $('.file-change').click();
        $('.file-cancel').hide();
    }
    function getImagePreview(url,name){
        html='<div class="input-group-prepend">\n'+

            '<input type="hidden" name="'+name+'"   value="'+url+'">                                <a href="'+url+'" target="_blank" class="p-btn">Preview <i class="ti-eye"></i></a>&nbsp;&nbsp;\n'+
            '                                    <a href="javascript:void(0);" class="file-change f-btn" data-file="'+name+'">Change <i class="ti-pencil"></i></a>\n'+
            '                                </div>';
        return html;
    }
    var prevHtml = [];

    $('body').on('click', '.file-change', function(e) {

        e.preventDefault();
        attr = $(this).attr('data-file');
        prevHtml[attr] = $(this).closest('div').html();
        $(this).closest('div').html(`
		<input type="file" name="${attr}" class="form-control" id="${attr}"  />
		<a href="javascript:void(0);" class="file-cancel" data-key="${attr}" alt="Cancel"><i class="ti-close"></i></a>
	`);
    });
    $('body').on('click', '.file-cancel', function(e) {

        e.preventDefault();
        attr = $(this).attr('data-key');
        $(this).closest('div').html(prevHtml[attr]);
    });
    document.title = "Link UI Configuartion";

</script>
</body>
</html>


