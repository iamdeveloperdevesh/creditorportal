$(document).ready(function () {
    var document_type;
    $.ajax({
        url: "/broker/get_all_employer/form_center",
        type: "POST",
        dataType: "json",
        success: function (response) {
            $('#employer_name').empty();
            $('#employer_name').append('<option value=""> Select Employer name</option>');
            for (i = 0; i < response.length; i++) {
                $('#employer_name').append('<option value="' + response[i].company_id + '">' + response[i].comapny_name + '</option>');
            }
        }
    });

    //on chnage of document type upload it to form center or policy docs
    function test(text, selector) {
      
    var wrapped = $("<div>" + text + "</div>");
    var q = wrapped.find(selector).html();
    return q
}


    $(document).on('change', '#document_type', function () {

        var type = $('option:selected', this).val();
        if (type == 1) {
            document_type = 1;
            $("#document_text").show();
            $("#policy_doc").show();
            $("#policy_no_content").show();
           // $("#policy_doc").hide();
           // $("#policy_no_content").hide();

//               add ignore class
           // $("#policy_type").addClass("intro");
           // $("#policy_no").addClass("intro");




        } else {
            //add ignore class
             document_type = 2;
            $("#document_name").addClass("intro");
            $("#document_name").addClass("ignore");
            $("#document_text").hide();
            $("#policy_doc").show();
            $("#policy_no_content").show();

        }


    });



    //    get all insurer
//    $("#employer_name").change(function(){
//        var company_id = $(this).val();
//        $.ajax({
//        url: "/get_all_insuer_against_company",
//        type: "POST",
//        data :{company_id:company_id},
//        dataType: "json",
//        success: function (response) {
//                $('#insurer_id').empty();
//         	$('#insurer_id').append('<option value=""> Select Insurer name</option>');
//         	for (i = 0; i < response.length; i++) { 
//         	  	$('#insurer_id').append('<option value="'+ response[i].ID +'">' + response[i].insurer_name+ '</option>');
//}
//        }
//    });
//   });

    // get policy no change on employer name
    $("#employer_name").change(function () {
        var company_id = $(this).val();
        $.ajax({
            url: "/broker/formcenter/get_policy_type",
            type: "POST",
            data: {company_id: company_id},
            dataType: "json",
            success: function (response) {
                $('#policy_type').empty();
                $('#policy_type').append('<option value=""> Select Policy</option>');
                for (i = 0; i < response.length; i++) {
                    $('#policy_type').append('<option data-id ="'+response[i].policy_sub_type_id+'" value="' + response[i].policy_no + '">' + (response[i].policy_sub_type_name + response[i].desgn_name) + '</option>');
                }
//                            $('#policy_type').append('<option value="1">Group</option>');
//                            $('#policy_type').append('<option value="2">Voluntry</option>');
            }
        });
    });
//         get policy no change on employer name
    $("#policy_type").change(function () {

        var company_id = $('#employer_name option:selected').val();
        // var insurer_id = $('#insurer_id option:selected').val();
        var policy_no = $('#policy_type option:selected').val();
        $.ajax({
            url: "/broker/formcenter/get_all_policy_against_company_and_insurer",
            type: "POST",
            data: {company_id: company_id, policy_no: policy_no},
            dataType: "json",
            success: function (response) {
                $('#policy_no').empty();
                //$('#policy_no').append('<option value=""> Select Policy</option>');
                for (i = 0; i < response.length; i++) {
                    $('#policy_no').append('<option value="' + response[i].policy_no + '">' + response[i].policy_no + '</option>');
                }
            }
        });
    });

    $('#form_center_form').validate({
        ignore: ".ignore",
        rules: {
            employer_name: {
                required: true
            },
            document_type: {
                required: true
            },
            userfile: {
                required: true
            },
            document_name: {
                required: true
            },
             policy_type: {
                required: true
            },
             policy_no: {
                required: true
            },
             document_name: {
                required: true
            }
            
            
            
//            family_contact: {
//                valid_mobile: true
//
//            },
//            family_email: {
//                validateEmail: true
//            }
            /*  family_flat: {
             required: true
             },
             family_location: {
             required: true
             },
             family_street: {
             required: true
             }, 
             family_city: {
             required: true
             },
             family_state: {
             required: true
             },
             family_pincode: {
             required: true
             },
             family_contact: {
             valid_mobile : true
             
             } */
        },
        messages: {
            employer_name: "Please select comapny name",
            document_type: "Please select documrnt type",
            userfile: "Please select file",
            document_name: "Please select doccumrnt name",
            //family_contact: "Please provide your valid mobile number.",
            //family_email: "Please provide your Email address."
            /* family_flat: "Please provide Building and Flat No.",
             family_location: "Please provide your location.",
             family_street: "Please provide street",
             family_city: "Please provide City name.",
             family_state: "Please provide State name",
             family_pincode: "Please provide you Pincode", */
            //family_email: "Please provide your Email address."
        },
//        invalidHandler: function(f, v) {},
//        errorElement: 'div',
//        errorPlacement: function(error, element) {
//            var placement = $(element).data('error');
//            if (placement) {
//                $(placement).append(error);
//            } else {
//                error.insertAfter(element);
//            }
//        },
        submitHandler: function (form, event) {
            event.preventDefault();
            var fd = new FormData();
            debugger;
            fd.append('file', $('#userfile')[0].files[0]);
            fd.append('employer_name', $('#employer_name option:selected').val());

            fd.append('doccument_type', $('#document_type option:selected').val());
               if(document_type == 1){
            fd.append('document_name', $('#document_name').val());
            fd.append('document_type', document_type);
            fd.append('policy_type', $("#policy_type option:selected").attr("data-id"));
        }else{
            
            fd.append('document_name', $('#policy_no option:selected').val());
            //fd.append('document_type', document_type);
        }

            $.ajax({
                url: '/broker/do_upload',
                type: "post",
                data: fd,
                processData: false,
                contentType: false,
                cache: false,
                async: false,
                success: function (data) {
                    var response = JSON.parse(data);
                   if(!response.error){
                        swal({
                                title: "Warning",
                                text: "File uploaded successfully",
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            })
                            .then((willDelete) => {
          if (willDelete) {
            window.location.reload();
            }
           
          
                           
                   })
               }
               else{
                  
                   var removedSpanString = response.error;
                var z = test(removedSpanString,"p");
                      swal({
                                title: "Warning",
                                text: z,
                                type: "warning",
                                showCancelButton: false,
                                confirmButtonText: "Ok!",
                                closeOnConfirm: true
                            })
                                                    .then((willDelete) => {
          if (willDelete) {
            window.location.reload();
            }
           
          
                           
                   })
                   
               }
                
            }
            });

        }
    });
});