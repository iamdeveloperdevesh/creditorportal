<div class="col-md-10">
    <div class="content-section mt-3">
        <div class="card">
            <div class="cre-head">
                <div class="row">
                    <div class="col-md-10 col-10">
                        <p>Theme Configuartion - <i class="ti-user"></i></p>
                    </div>
                    <div class="col-md-2 col-2"></div>
                </div>
            </div>
            <div class="card-body">
                <form  id="form-validate" method="post" action="#">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="employee_fname" class="col-form-label">Theme For<span class="lbl-star">*</span></label>
                            <div class="input-group">
                               <select class="form-control" id="theme_for" name="theme_for" onchange="show_partner(this.value)">
                                   <option value="1">Admin</option>
                                   <option value="2">Partner</option>
                               </select>
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3" id="partner_div" style="display: none">
                            <label for="employee_fname" class="col-form-label">Partner Name<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <select class="form-control" id="creditor_id" name="creditor_id" onchange="getPartnerTheme(this.value)">

                                </select>
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend"><span class="material-icons">account_circle</span></span>
                                </div>
                            </div>
                        </div>
                        </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="employee_fname" class="col-form-label">Primary Color<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <input type="color" name="primary_color" id="primary_color" >
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="employee_fname" class="col-form-label">Secondary Color<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <input type="color" name="secondary_color" id="secondary_color">
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="employee_fname" class="col-form-label">Text Color<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <input type="color" name="text_color" id="text_color">
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="employee_fname" class="col-form-label">Background Color<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <input type="color" name="background_color" id="background_color">
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="employee_fname" class="col-form-label">CTA/Button Color<span class="lbl-star">*</span></label>
                            <div class="input-group">
                                <input type="color" name="cta_color" id="cta_color">
                            </div>
                        </div>
                        <div class="col-md-3" id="logo_div" style="display: none">
                            <label for="logo_url" class="col-form-label">Logo Image</label>
                            <div class="input-group" id="logo_image_div">
                                <input type="file" class="form-control" id="logo_url" name="logo_url" tabindex="47">
                                <!--                                <div id=""></div>-->
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-1 col-6 text-left">
                            <button type="submit" class="btn smt-btn btn-primary">Save</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    function show_partner(id){
        $("#partner_div").hide();
        $("#logo_div").show();
        if(id==2){
            $("#logo_div").hide();
            $("#partner_div").show();
        }
    }
    window.onload = function () {

        getClientName();
        getPartnerTheme();
    }
    $(function() {
        $("#date_of_joining").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true,
            maxDate: new Date()
        });

    });
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
    function getPartnerTheme(creditor_id=null) {
        var theme_for=$('#theme_for').val();
        $.ajax({
            url: "/ThemeConfiguaration/getPartnerTheme",
            type: "POST",
            dataType: 'json',
            data:{creditor_id,theme_for},
            success: function(response) {
                if(response.code == 200){
                    $("#primary_color").val(response.data.primary_color);
                    $("#secondary_color").val(response.data.secondary_color);
                    $("#text_color").val(response.data.text_color);
                    $("#background_color").val(response.data.background_color);
                    $("#cta_color").val(response.data.cta_color);
                    var logo_url= getImagePreview(response.data.logo_url,'logo_url');
                    $("#logo_image_div").html(logo_url);
                    $('#theme_for').trigger('change')
                }else{

                    return false;
                }


            }
        });
    }
    var vRules = {
        theme_for:{required:true},
        creditor_id:{required:true},
    };



    $("#form-validate").validate({
        rules: vRules,
        submitHandler: function(form)
        {
            $("#form-validate").ajaxSubmit({
                url: "<?php echo base_url();?>ThemeConfiguaration/submitForm",
                type: 'post',
                dataType: 'JSON',
                cache: false,
                clearForm: false,
                beforeSubmit : function(arr, $form, options){
                   // $(".btn-primary").hide();
                    //return false;
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

    document.title = "My Profile";
    var prevHtml = [];
    function getImagePreview(url,name){
        html='<div class="input-group-prepend">\n'+

            '<input type="hidden" name="'+name+'"   value="'+url+'">                                <a href="'+url+'" target="_blank" class="p-btn">Preview <i class="ti-eye"></i></a>&nbsp;&nbsp;\n'+
            '                                    <a href="javascript:void(0);" class="file-change f-btn" data-file="'+name+'">Change <i class="ti-pencil"></i></a>\n'+
            '                                </div>';
        return html;
    }
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

</script>
</body>
</html>


