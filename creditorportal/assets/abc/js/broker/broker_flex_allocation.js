$(document).ready(function() {
    $.ajax({
        url: "/broker/get_all_employer",
        type: "POST",
        dataType: "json",
        success: function(response) {
            $('#employer_name').empty();
            $('#employer_name').append('<option value=""> Select Employer name</option>');
            for (i = 0; i < response.length; i++) {
                $('#employer_name').append('<option value="' + response[i].company_id + '">' + response[i].comapny_name + '</option>');
            }
        }
    });

    $("body").on('keyup', ".sodexo", function(e) {

        var $th = $(this);
        if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val($th.val().replace(/[^0-9,]/g, function(str) {
                return '';
            }));
        }
        return;
    });

    $("#employer_name").change(function() {
        var company_name = $('#employer_name option:selected').val();
        $.ajax({
            url: "/broker/get_flex_allocation_from_company",
            type: "POST",
            data: {company_name: company_name},
            dataType: "json",
            success: function(response) {
                if (response.flex_allocation == 'Y')
                {
                    $("#flex_applicable option[value='Yes']").attr("selected", 'selected');
                    if ($('#flex_applicable option:selected').val() == 'Yes')
                    {

                        $(".main_div").removeAttr('style', 'display:none');
                        //     get_data against employer
                        $.ajax({
                            url: "/broker/get_all_benefit_against_company",
                            type: "POST",
                            dataType: "json",
                            data: {company_name: company_name},
                            success: function(response) {
//                            console.log(response);return;
//                    $('.main_div').html("");
                                if (response.length != 0)
                                {
                                    for (i = 0; i < response.length; i++) {
                                        var name = response[i].flexi_benefit_name;

                                        if (response[i].is_active == 'Y')
                                        {
                                            if (response[i].flexi_benefit_name == 'Sodexo')
                                            {
                                                $('.apppend_div').append('<div class="col-md-2"><div class="custom-control custom-checkbox mar-bottom"> <input type="checkbox" checked="" name="' + name + '" class="custom-control-input" id="' + response[i].flexi_benefit_name + '"><label class="custom-control-label" for="' + response[i].flexi_benefit_name + '"> <span>' + response[i].flexi_benefit_name + '</span> </label> </div></div><div class="col-md-4"><div class="form-group"><input type="text" name="sodexo_value" class="sodexo" id="sodexo_value" value="' + response[i].amount + '"></div>');
                                            } else
                                            {
                                                $('.apppend_div').append('<div class="col-md-6"><div class="custom-control custom-checkbox mar-bottom"> <input type="checkbox" checked="" name="' + name + '" class="custom-control-input" id="' + response[i].flexi_benefit_name + '"><label class="custom-control-label" for="' + response[i].flexi_benefit_name + '"> <span>' + response[i].flexi_benefit_name + '</span> </label> </div></div>');
                                            }
                                        } else
                                        {
                                            if (response[i].flexi_benefit_name == 'Sodexo')
                                            {
                                                $('.apppend_div').append('<div class="col-md-2"><div class="custom-control custom-checkbox mar-bottom"> <input type="checkbox" name="' + name + '" class="custom-control-input" id="' + response[i].flexi_benefit_name + '"><label class="custom-control-label" for="' + response[i].flexi_benefit_name + '"> <span>' + response[i].flexi_benefit_name + '</span> </label> </div></div><div class="col-md-4"><div class="form-group"><input type="text" name="sodexo_value" id="sodexo_value" class="sodexo"></div>');
                                            } else
                                            {
                                                $('.apppend_div').append('<div class="col-md-6"><div class="custom-control custom-checkbox mar-bottom"> <input type="checkbox" name="' + name + '" class="custom-control-input" id="' + response[i].flexi_benefit_name + '"><label class="custom-control-label" for="' + response[i].flexi_benefit_name + '"> <span>' + response[i].flexi_benefit_name + '</span> </label> </div></div>');
                                            }
                                        }

                                    }
                                } else
                                {
                                    //                        all flexi benefit
                                    $.ajax({
                                        url: "/broker/get_all_flexi_benefit",
                                        type: "POST",
                                        dataType: "json",
                                        success: function(response) {
                                            console.log(response);
                                            for (i = 0; i < response.length; i++) {
                                                var name = response[i].flexi_benefit_name;
                                                if (response[i].flexi_benefit_name == 'Sodexo')
                                                {
                                                    $('.apppend_div').append('<div class="col-md-2"><div class="custom-control custom-checkbox mar-bottom"> <input type="checkbox" name="' + name + '" class="custom-control-input" id="' + response[i].flexi_benefit_name + '"><label class="custom-control-label" for="' + response[i].flexi_benefit_name + '"> <span>' + response[i].flexi_benefit_name + '</span> </label> </div></div><div class="col-md-4"><div class="form-group"><input type="text" name="sodexo_value" id="sodexo_value" class="sodexo"></div>');
                                                } else
                                                {
                                                    $('.apppend_div').append('<div class="col-md-6"><div class="custom-control custom-checkbox mar-bottom"> <input type="checkbox" name="' + name + '" class="custom-control-input" id="' + response[i].flexi_benefit_name + '"><label class="custom-control-label" for="' + response[i].flexi_benefit_name + '"> <span>' + response[i].flexi_benefit_name + '</span> </label> </div></div>');
                                                }
                                            }
                                        }
                                    });
                                }
                            }
                        });
                    } else
                    {

                        $(".apppend_div").attr('style', 'display:none');
                    }

                } else
                {
                    $("#flex_applicable").change(function() {
                        if ($('#flex_applicable option:selected').val() == 'Yes')
                        {
                            $(".apppend_div").removeAttr('style', 'display:none');
                            $(".main_div").removeAttr('style', 'display:none');
                            var company_name = $('#employer_name option:selected').val();
                            $.ajax({
                                url: "/broker/get_all_benefit_against_company",
                                type: "POST",
                                dataType: "json",
                                data: {company_name: company_name},
                                success: function(response) {
                                    if (response.length != 0)
                                    {
                                        for (i = 0; i < response.length; i++) {
                                            var name = response[i].flexi_benefit_name;
                                            if (response[i].is_active == 'Y')
                                            {
                                                if (response[i].flexi_benefit_name == 'Sodexo')
                                                {
                                                    $('.apppend_div').append('<div class="col-md-2"><div class="custom-control custom-checkbox mar-bottom"> <input type="checkbox" checked="" name="' + name + '" class="custom-control-input" id="' + response[i].flexi_benefit_name + '"><label class="custom-control-label" for="' + response[i].flexi_benefit_name + '"> <span>' + response[i].flexi_benefit_name + '</span> </label> </div></div><div class="col-md-4"><div class="form-group"><input type="text" name="sodexo_value" id="sodexo_value" class="sodexo" value="' + response[i].amount + '"></div>');
                                                } else
                                                {
                                                    $('.apppend_div').append('<div class="col-md-6"><div class="custom-control custom-checkbox mar-bottom"> <input type="checkbox" checked="" name="' + name + '" class="custom-control-input" id="' + response[i].flexi_benefit_name + '"><label class="custom-control-label" for="' + response[i].flexi_benefit_name + '"> <span>' + response[i].flexi_benefit_name + '</span> </label> </div></div>');
                                                }
                                            } else
                                            {
                                                if (response[i].flexi_benefit_name == 'Sodexo')
                                                {
                                                    $('.apppend_div').append('<div class="col-md-2"><div class="custom-control custom-checkbox mar-bottom"> <input type="checkbox" name="' + name + '" class="custom-control-input" id="' + response[i].flexi_benefit_name + '"><label class="custom-control-label" for="' + response[i].flexi_benefit_name + '"> <span>' + response[i].flexi_benefit_name + '</span> </label> </div></div><div class="col-md-4"><div class="form-group"><input type="text" name="sodexo_value" id="sodexo_value" class="sodexo"></div>');
                                                } else
                                                {
                                                    $('.apppend_div').append('<div class="col-md-6"><div class="custom-control custom-checkbox mar-bottom"> <input type="checkbox" name="' + name + '" class="custom-control-input" id="' + response[i].flexi_benefit_name + '"><label class="custom-control-label" for="' + response[i].flexi_benefit_name + '"> <span>' + response[i].flexi_benefit_name + '</span> </label> </div></div>');
                                                }
                                            }
                                        }
                                    } else
                                    {
                                        $('.apppend_div').html("");
                                        //    all flexi benefit
                                        $.ajax({
                                            url: "/broker/get_all_flexi_benefit",
                                            type: "POST",
                                            dataType: "json",
                                            success: function(response) {
                                                for (i = 0; i < response.length; i++) {
                                                    var name = response[i].flexi_benefit_name;
                                                    if (response[i].flexi_benefit_name == 'Sodexo')
                                                    {
                                                        $('.apppend_div').append('<div class="col-md-2"><div class="custom-control custom-checkbox mar-bottom"> <input type="checkbox" name="' + name + '" class="custom-control-input" id="' + response[i].flexi_benefit_name + '"><label class="custom-control-label" for="' + response[i].flexi_benefit_name + '"> <span>' + response[i].flexi_benefit_name + '</span> </label> </div></div><div class="col-md-4"><div class="form-group"><input type="text" name="sodexo_value" id="sodexo_value" class="sodexo"></div>');
                                                    } else
                                                    {
                                                        $('.apppend_div').append('<div class="col-md-6"><div class="custom-control custom-checkbox mar-bottom"> <input type="checkbox" name="' + name + '" class="custom-control-input" id="' + response[i].flexi_benefit_name + '"><label class="custom-control-label" for="' + response[i].flexi_benefit_name + '"> <span>' + response[i].flexi_benefit_name + '</span> </label> </div></div>');
                                                    }
                                                }
                                            }
                                        });
                                    }
                                }
                            });
                        } else
                        {
                            $(".apppend_div").attr('style', 'display:none');
                        }
                    });
                }
            }
        });
    });

    $("#flex_applicable").change(function() {
        if ($('#flex_applicable option:selected').val() == 'No')
        {
            $(".apppend_div").attr('style', 'display:none');
        } else
        {
            var company_name = $('#employer_name option:selected').val();
            $(".apppend_div").removeAttr('style', 'display:none');
//            $.ajax({
//                                url: "/broker/get_all_benefit_against_company",
//                                type: "POST",
//                                dataType: "json",
//                                data:{company_name:company_name},
//                                success: function (response) {
//                                    if(response.length != 0)
//                                    {
//                                        for (i = 0; i < response.length; i++) {
//                                            var name = response[i].flexi_benefit_name;
//                                            if(response[i].is_active == 'Y')
//                                            {
//                                                if(response[i].flexi_benefit_name == 'Sodexo')
//                                                {
//                                                     $('.apppend_div').append('<div class="col-md-2"><div class="custom-control custom-checkbox mar-bottom"> <input type="checkbox" checked="" name="'+name+'" class="custom-control-input" id="'+response[i].flexi_benefit_name+'"><label class="custom-control-label" for="'+response[i].flexi_benefit_name+'"> <span>'+response[i].flexi_benefit_name+'</span> </label> </div></div><div class="col-md-4"><div class="form-group"><input type="text" name="sodexo_value" id="sodexo_value" value="'+response[i].amount+'"></div>');
//                                                }
//                                                else
//                                                {
//                                                    $('.apppend_div').append('<div class="col-md-6"><div class="custom-control custom-checkbox mar-bottom"> <input type="checkbox" checked="" name="'+name+'" class="custom-control-input" id="'+response[i].flexi_benefit_name+'"><label class="custom-control-label" for="'+response[i].flexi_benefit_name+'"> <span>'+response[i].flexi_benefit_name+'</span> </label> </div></div>');
//                                                }
//                                            }
//                                            else
//                                            {
//                                                if(response[i].flexi_benefit_name == 'Sodexo')
//                                                {
//                                                     $('.apppend_div').append('<div class="col-md-2"><div class="custom-control custom-checkbox mar-bottom"> <input type="checkbox" name="'+name+'" class="custom-control-input" id="'+response[i].flexi_benefit_name+'"><label class="custom-control-label" for="'+response[i].flexi_benefit_name+'"> <span>'+response[i].flexi_benefit_name+'</span> </label> </div></div><div class="col-md-4"><div class="form-group"><input type="text" name="sodexo_value" id="sodexo_value"></div>');
//                                                }
//                                                else
//                                                {
//                                                    $('.apppend_div').append('<div class="col-md-6"><div class="custom-control custom-checkbox mar-bottom"> <input type="checkbox" name="'+name+'" class="custom-control-input" id="'+response[i].flexi_benefit_name+'"><label class="custom-control-label" for="'+response[i].flexi_benefit_name+'"> <span>'+response[i].flexi_benefit_name+'</span> </label> </div></div>');
//                                                }
//                                            }
//                                        }
//                                    }
//                                    else
//                                    {
//                                          //    all flexi benefit
//                                        $.ajax({
//                                            url: "/broker/get_all_flexi_benefit",
//                                            type: "POST",
//                                            dataType: "json",
//                                            success: function (response) {
//                                                    for (i = 0; i < response.length; i++) {
//                                                        var name = response[i].flexi_benefit_name;
//                                                        if(response[i].flexi_benefit_name == 'Sodexo')
//                                                        {
//                                                             $('.apppend_div').append('<div class="col-md-2"><div class="custom-control custom-checkbox mar-bottom"> <input type="checkbox" name="'+name+'" class="custom-control-input" id="'+response[i].flexi_benefit_name+'"><label class="custom-control-label" for="'+response[i].flexi_benefit_name+'"> <span>'+response[i].flexi_benefit_name+'</span> </label> </div></div><div class="col-md-4"><div class="form-group"><input type="text" name="sodexo_value" id="sodexo_value"></div>');
//                                                        }
//                                                        else
//                                                        {
//                                                            $('.apppend_div').append('<div class="col-md-6"><div class="custom-control custom-checkbox mar-bottom"> <input type="checkbox" name="'+name+'" class="custom-control-input" id="'+response[i].flexi_benefit_name+'"><label class="custom-control-label" for="'+response[i].flexi_benefit_name+'"> <span>'+response[i].flexi_benefit_name+'</span> </label> </div></div>');
//                                                        }
//                                                    }
//                                            }
//                                        });
//                                    }
//                                }
//                            });
        }
    });

//    $(document).on("keyup","#sodexo_value",function() {
//        var $th = $(this);
//         if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
//            $th.val($th.val().replace(/^[0-9,]+$/, function (str) {
//                return '';
//            }));
//        }
//        return;
////        return;
//});
    $("#btn_save").click(function() {
        var sodexo_value = $("#sodexo_value").val();
        var employer_id = $('#employer_name option:selected').val();
        var flex_applicable = $('#flex_applicable option:selected').val();
        var form_data = new FormData();
        if (employer_id == "")
        {
            swal({
                title: "Success",
                text: "please select employer name",
                type: "warning",
                showCancelButton: false,
                confirmButtonText: "Ok!",
                closeOnConfirm: true
            })
                    .then((willDelete) => {
                        if (willDelete) {
                            window.location.reload();
                        }
                    });
        } else if (flex_applicable == "") {

            swal({
                title: "Success",
                text: "please select flex benefit applicable",
                type: "warning",
                showCancelButton: false,
                confirmButtonText: "Ok!",
                closeOnConfirm: true
            })
                    .then((willDelete) => {
                        if (willDelete) {
                            window.location.reload();
                        }
                    });
        } else
        {
            form_data.append("sodexo_value", sodexo_value);
            form_data.append("employer_id", employer_id);
            form_data.append("flex_applicable", flex_applicable);
            $.each($("input[type='checkbox']"), function() {
                var name = $(this).attr('id');
                form_data.append(name, $(this).is(":checked"));
            });

            $.ajax({
                type: 'POST',
                url: "/broker/save_flexi_benefit",
                data: form_data,
                dataType: "json",
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response == true) {
                        swal({
                            title: "Success",
                            text: "Flex Inserted Successfully",
                            type: "warning",
                            showCancelButton: false,
                            confirmButtonText: "Ok!",
                            closeOnConfirm: true
                        })
                                .then((willDelete) => {
                                    if (willDelete) {
                                        window.location.reload();
                                    }



                                });
                    }
                }
            });
        }
    });
});


