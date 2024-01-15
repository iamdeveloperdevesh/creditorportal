$(document).ready(function() {
    $.ajax({
        url: "/broker/get_all_employer",
        type: "POST",
        dataType: "json",
        success: function(response) {
            $('#employer').empty();
            $('#employer').append('<option value=""> Select employer</option>');
            $.each(response, function(index, value) {
                $('#employer').append('<option  value="' + value['company_id'] + '">' + value['comapny_name'] + '</option>');

            });
        }
    });

 $('#btnCancel').on("click", function () {
    
      location.reload();
         //$('#member_form')[0].reset();
      
    });
	 $('#removeCancel').on("click", function () {
    
      location.reload();
         //$('#member_form')[0].reset();
      
    });

// onload of policytype



    /*$.ajax({
     url: "/get_all_policy_type",
     type: "POST",
     dataType: "json",
     success: function (response) {
     console.log(response);
     $('#policy_type').empty();
     $('#policy_type').append('<option value=""> Select Policy type</option>');
     $.each(response, function (index, value) {

     $('#policy_type').append('<option  value="' + value['policy_sub_type_id'] + '">' + value['policy_sub_type_name']  + '</option>');

     });
     }
     }); */

    $(document).on('change', '#employer', function() {
        var employer_id = $(this).val();
		
        $("#employer_name").val(employer_id);
        $("#employers_re_name").val(employer_id);
        $("#employers_correc_name").val(employer_id);
        $("#employers_endorsement_name").val(employer_id);

        $.ajax({
            url: "/broker/get_all_policy_type_as_per_employer",
            type: "POST",
            data: {
                "employer_id": employer_id
            },
            dataType: "json",
            success: function(response) {
				
                $('#policy_type').empty();
                if (response.length != 0) {
                    $('#policy_type').append('<option value=""> Select Policy type</option>');
                    $.each(response, function(index, value) {
                        $('#policy_type').append('<option  value="' + value['policy_sub_type_id'] + '">' + value['policy_sub_type_name'] + '</option>');

                    });

                }
            }
        });
    });


    $(document).on('change', '#policy_type', function() {
        var policy_type = $(this).val();
        $('#policy_type_form').val(policy_type);
        $('#policy_re_types_form').val(policy_type);
        $('#policy_correc_types_form').val(policy_type);
        $('#policy_endorsement_form').val(policy_type);
        var employer_id = $('#employer').val();


        $.ajax({
            url: "/get_all_data_policy_type",
            type: "POST",
            data: {
                "policy_type": policy_type,
                "employer_id": employer_id
            },
            dataType: "json",
            success: function(response) {
                console.log(response);
                $('#policy_no').empty();
                if (response.length != 0) {
                    $('#policy_no').append('<option value=""> Select Policy no</option>');
                    $.each(response, function(index, value) {
                        $('#policy_no').append('<option  value="' + value['policy_detail_id'] + '">' + value['policy_no'] + '</option>');

                    });
                }
            }
        });
    });

    $(document).on('change', '#policy_no', function() {
        var policy_type = $(this).val();
        $('#policy_name').val(policy_type);
        $('#policy_re_names').val(policy_type);
        $('#policy_correc_names').val(policy_type);
        $('#policy_endorsement_names').val(policy_type);
    });



// upload policy member



    $("#member_form").validate({
        rules: {
            uploadFile: "required",
            policy_name: "required",
            policy_type_id: "required",
            employer_name: "required",

        },
        messages: {
            uploadFile: "Please choose a file to upload.",
            policy_name: "Please select policy no.",
            policy_type: "Please select policy type.",
            employer_name: "Please select employer name.",

        },
        submitHandler: function(form, event) {

            $button_submit = $('#btnSave');
            var policy_no = $("#policy_no").val();
            var policy_type = $("#policy_type").val();
            var employer = $("#employer").val();
            event.preventDefault();
            var buttontext = $.trim($button_submit.text().toLowerCase());
            var buttonvalue = $.trim($button_submit.val());
            var url = "";
            if (buttontext == 'submit')
                url = "/broker/endorsement_request/uploadData";

            var formData = new FormData();
            formData.append("uploadFile", $('#add_member').val());
            formData.append('uploadFile', $('#add_member').get(0).files[0]);
            formData.append("policy_name", $('#policy_name').val());
            formData.append("policy_type_id", $('#policy_type_form').val());
            formData.append("employer_name", $('#employer_name').val());
            var prev_text = $button_submit.text();
            $button_submit.text('Saving...');
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                dataType: 'json',
                contentType: false,
                processData: false,
                mimetype: "multipart/form-data",
                success: function(returndata) {
                    $("#upload_error_success").html(createMessage(returndata));
                    if (returndata.status == "true") {
                        $('#container').html('Download report');
                        $('#container').css('display', 'inline-block');
                        $button_submit.text(prev_text);
                    } else
                        $button_submit.text(prev_text);
                }
            });
        }
    });


    $("#remove_mem_form").validate({
        rules: {
            removeFile: "required",
            policy_re_names: "required",
            policy_re_types_form: "required",
            employers_re_name: "required",

        },
        messages: {
            removeFile: "Please choose a file to upload.",
            policy_re_names: "Please Select Policy",
            policy_re_types_form: "Please Select Policy type",
            employers_re_name: "Please Select employer"
        },
        submitHandler: function(form, event) {
            var policy_no = $("#policy_re_names").val();
            var policy_type = $("#policy_re_types_form").val();
            var employer = $("#employers_re_name").val();

            $button_submit = $('#btnRemoveSave');
            event.preventDefault();
            var buttontext = $.trim($button_submit.text().toLowerCase());
            var buttonvalue = $.trim($button_submit.val());
            var url = "";
            if (buttontext == 'submit')
                url = "/broker/endorsement_request/removeData";

            var formData = new FormData();
            formData.append("removeFile", $('#remove_member').val());
            formData.append("removeFile", $('#remove_member').get(0).files[0]);
            formData.append("policy_name", $('#policy_re_names').val());
            formData.append("policy_re_types_form", $('#policy_re_types_form').val());
            formData.append("employers_re_name", $('#employers_re_name').val());
            var prev_text = $button_submit.text();
            $button_submit.text('Saving...');
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                dataType: 'json',
                contentType: false,
                processData: false,
                mimetype: "multipart/form-data",
                success: function(returndata) {
                    $("#remove_error_success").html(createMessage(returndata));
                    if (returndata.status == "true") {
                        $('#remove').html('Download report');
                        $('#remove').css('display', 'inline-block');
                        $button_submit.text(prev_text);
                    } else
                        $button_submit.text(prev_text);
                }
            });
        }
    });


    function createMessage(response, close_choice) {
        var division = "";

        if (response.status == "false") {
            division = "<div class='alert alert-danger' id='forgot_error'>";

            if (close_choice == "true") {
                division += "<button type='button' class='close' data-dismiss='alert'>";
                division += "<i class='ace-icon fa fa-times'></i>";
                division += "</button>";
            }

            division += response.message.replace(/\\n/g, "<br>");
            division += "</div>";
        } else {
            division = "<div class='alert alert-success' id='forgot_success'>";

            if (close_choice == "true") {
                division += "<button type='button' class='close' data-dismiss='alert'>";
                division += "<i class='ace-icon fa fa-times'></i>";
                division += "</button>";
            }

            division += response.message.replace(/\\n/g, "<br>");
            division += "</div>";
        }

        return division;
    }

    $("#correction_mem_form").validate({
        rules: {
            uploadFile: "required",
            policy_name: "required",
            policy_type_id: "required",
            employer_name: "required",

        },
        messages: {
            uploadFile: "Please choose a file to upload.",
            policy_name: "Please select policy no.",
            policy_type: "Please select policy type.",
            employer_name: "Please select employer name.",

        },
        submitHandler: function(form, event) {

            $button_submit = $('#btncorrectionSave');
            var policy_no = $("#policy_correc_names").val();
            var policy_type = $("#policy_correc_types_form").val();
            var employer = $("#employers_correc_name").val();
            event.preventDefault();
            var buttontext = $.trim($button_submit.text().toLowerCase());
            var buttonvalue = $.trim($button_submit.val());
            var url = "";
            if (buttontext == 'submit')
                url = "/broker/endorsement_request/correctiondData";

            var formData = new FormData();
            formData.append("uploadFile", $('#correction_member').val());
            formData.append('uploadFile', $('#correction_member').get(0).files[0]);
            formData.append("policy_name", $('#policy_correc_names').val());
            formData.append("policy_type_id", $('#policy_correc_types_form').val());
            formData.append("employer_name", $('#employers_correc_name').val());
            var prev_text = $button_submit.text();
            $button_submit.text('Saving...');
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                dataType: 'json',
                contentType: false,
                processData: false,
                mimetype: "multipart/form-data",
                success: function(returndata) {
                    $("#correction_error_success").html(createMessage(returndata));
                    if (returndata.status == "true") {
                        $('#correction').html('Download report');
                        $('#correction').css('display', 'inline-block');
                        $button_submit.text(prev_text);
                    } else
                        $button_submit.text(prev_text);
                }
            });
        }
    });

    $("#endorsement_mem_form").validate({
        rules: {
            uploadFile: "required",
            policy_name: "required",
            policy_type_id: "required",
            employer_name: "required",

        },
        messages: {
            uploadFile: "Please choose a file to upload.",
            policy_name: "Please select policy no.",
            policy_type: "Please select policy type.",
            employer_name: "Please select employer name.",

        },
        submitHandler: function(form, event) {

            $button_submit = $('#btnendorsementSave');
            var policy_no = $("#policy_endorsement_names").val();
            var policy_type = $("#policy_endorsement_form").val();
            var employer = $("#employers_endorsement_name").val();
            event.preventDefault();
            var buttontext = $.trim($button_submit.text().toLowerCase());
            var buttonvalue = $.trim($button_submit.val());
            var url = "";
            if (buttontext == 'submit')
                url = "/broker/endorsement_request/endorsementData";

            var formData = new FormData();
            formData.append("uploadFile", $('#endorsementdata_member').val());
            formData.append('uploadFile', $('#endorsementdata_member').get(0).files[0]);
            formData.append("policy_name", $('#policy_endorsement_names').val());
            formData.append("policy_type_id", $('#policy_endorsement_form').val());
            formData.append("employer_name", $('#employers_endorsement_name').val());
            var prev_text = $button_submit.text();
            $button_submit.text('Saving...');
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                dataType: 'json',
                contentType: false,
                processData: false,
                mimetype: "multipart/form-data",
                success: function(returndata) {
                    $("#endorsement_error_success").html(createMessage(returndata));
                    if (returndata.status == "true") {
                        $('#endorsement').html('Download report');
                        $('#endorsement').css('display', 'inline-block');
                        $button_submit.text(prev_text);
                    } else
                        $button_submit.text(prev_text);
                }
            });
        }
    });


});
