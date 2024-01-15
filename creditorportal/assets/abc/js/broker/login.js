
$("document").ready(function () {

    $("#forgotPassword").validate({
        ignore: ".ignore",
        rules: {
            email: {
                required: true,
                email: true

            }
        },
        messages: {
            broker_email: "Please Enter Email",
        },
        invalidHandler: function (f, v) {

        },
        errorElement: 'div',
        errorPlacement: function (error, element) {

            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error);
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function () {
            var datastring = $("#forgotPassword").serialize();

            $.ajax({
                type: "POST",
                url: "/forgotPassword",
                data: datastring,
                async: false,
                success: function (data) {
                    if (data == 1) {
                        swal("", "Password reset instructions is send to you by Email.");
                    } else {
                        swal("", "Email Not Found");
                    }
                },
                error: function () {
                    alert('Something went Wrong');
                }
            });
        }
    });

    $('#broker_form').validate({
        ignore: ".ignore",
        rules: {
            broker_email: {
                required: true,

            },
            broker_pwd: {
                required: true
            }
        },
        messages: {
            broker_email: "Please provide correct username",
            broker_pwd: "Please provide correct password."

        },
        invalidHandler: function (f, v) {

        },
        errorElement: 'div',
        errorPlacement: function (error, element) {

            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error);
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function () {
            var datastring = $("#broker_form").serialize();

            $.ajax({
                type: "POST",
                url: "/broker/get_login_details",
                data: datastring,
                async: false,
                success: function (data) {
                    if (data == 1) {
                        location.replace("/broker/user_home");
                    } else {
                        alert("Incorrect Id or Password");
                    }
                },
                error: function () {
                    alert('Something went Wrong');
                }
            });
        }
    });
});