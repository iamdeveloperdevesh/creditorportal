$(document).ready(function() {
    $.ajax({
        url: "/employer/get_all_policy_numbers",
        type: "POST",
        async: false,
        dataType: "json",
        data: {"employer": "true"},
        success: function(response) {
            // console.log(response);
            $('.policy_no').empty();
            $('.policy_no').append('<option value=""> Select policy type</option>');
            $('.policy_no1').empty();
            $('.policy_no1').append('<option value=""> Select policy type</option>');
            $('.policy_no2').empty();
            $('.policy_no2').append('<option value=""> Select policy type</option>');
            $('.policy_no3').empty();
            $('.policy_no3').append('<option value=""> Select policy type</option>');
            for (i = 0; i < response.length; i++) {
                var date = response[i].end_date.split("-");
                var date = new Date(Number(date[0]), Number(date[1]) - 1, Number(date[2]));
                var current_date = new Date();
                //if (date > current_date) {
                $('.policy_no').append('<option value="' + response[i].policy_detail_id + '">' + (response[i].policy_sub_type_name + response[i].desgn_name) + '</option>');
                $('.policy_no1').append('<option value="' + response[i].policy_detail_id + '">' + response[i].policy_sub_type_name + '</option>');
                $('.policy_no2').append('<option value="' + response[i].policy_detail_id + '">' + (response[i].policy_sub_type_name + response[i].desgn_name) + '</option>');
                $('.policy_no3').append('<option value="' + response[i].policy_detail_id + '">' + (response[i].policy_sub_type_name + response[i].desgn_name) + '</option>');
                // }
            }
        }

    });

    $("#nominee_form").validate({
        rules: {
            uploadFile: "required",
            policy_name: "required"
        },
        messages: {
            uploadFile: "Please choose a file to upload.",
            policy_name: "Please Select Policy"
        },
        submitHandler: function(form, event) {
            $button_submit = $('#nomineeSave');
            event.preventDefault();
            var buttontext = $.trim($button_submit.text().toLowerCase());
            var buttonvalue = $.trim($button_submit.val());
            var url = "";
            if (buttontext == 'submit')
                url = "/employer/add_nominee/uploadData";

            var formData = new FormData();
            formData.append("uploadFile", $('#nominee_member').val());
            formData.append('uploadFile', $('#nominee_member').get(0).files[0]);
            formData.append("policy_name", $('.policy_no1').val());
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
                    $("#nominee_form_error_success").html(createMessage(returndata));
                    if (returndata.status == "true") {
                        $('#nominee').html('Download report');
                        $button_submit.text(prev_text);
                    } else
                        $button_submit.text(prev_text);
                }
            });
        }
    });
    $("#remove_form").validate({
        rules: {
            removeFile: "required",
            policy_name: "required"
        },
        messages: {
            removeFile: "Please choose a file to upload.",
            policy_name: "Please Select Policy"
        },
        submitHandler: function(form, event) {
            $button_submit = $('#removebtn');
            event.preventDefault();
            var buttontext = $.trim($button_submit.text().toLowerCase());
            var buttonvalue = $.trim($button_submit.val());
            var url = "";
            if (buttontext == 'submit')
                url = "/employer/add_nominee/removeData";

            var formData = new FormData();
            formData.append("removeFile", $('#remove_member').val());
            formData.append('removeFile', $('#remove_member').get(0).files[0]);
            formData.append("policy_name", $('.policy_no2').val());
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
                    $("#remove_member_error_success").html(createMessage(returndata));
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

    $("#first_form").validate({
        rules: {
            uploadFile: "required",
            policy_name: "required"
        },
        messages: {
            uploadFile: "Please choose a file to upload.",
            policy_name: "Please Select Policy"
        },
        submitHandler: function(form, event) {
            $button_submit = $('#btnSave');
            event.preventDefault();
            var buttontext = $.trim($button_submit.text().toLowerCase());
            var buttonvalue = $.trim($button_submit.val());
            var url = "";
            if (buttontext == 'submit')
                url = "/employer/import/uploadData";

            var formData = new FormData();
            formData.append("uploadFile", $('#add_member').val());
            formData.append('uploadFile', $('#add_member').get(0).files[0]);
            formData.append("policy_name", $('.policy_no').val());
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

    $(".cancel_member").on("click", function() {
        $('#first_form')[0].reset();
    });
    $(".cancel_mem").on("click", function() {
        $('#remove_form')[0].reset();
    });

    $("#second_form").validate({
        rules: {
            correctionFile: "required",
            policy_name1: "required"
        },
        messages: {
            correctionFile: "Please choose a file to upload.",
            policy_name1: "Please Select Policy"
        },
        submitHandler: function(form, event) {
            $button_submit = $('#correctionbtn');
            event.preventDefault();
            var buttontext = $.trim($button_submit.text().toLowerCase());
            var buttonvalue = $.trim($button_submit.val());
            var url = "";
            if (buttontext == 'submit')
                url = "/employer/import/correctionuploadData";

            var formData = new FormData();
            formData.append("uploadFile", $('#correction_member').val());
            formData.append('uploadFile', $('#correction_member').get(0).files[0]);
            formData.append("policy_name", $('.policy_no3').val());
            formData.append("correction", 'correction');
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
                    $("#upload_error_success1").html(createMessage(returndata));
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

});
