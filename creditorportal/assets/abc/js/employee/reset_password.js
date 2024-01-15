$('document').ready(function() {
    $("#resetPassword").validate({
        ignore: ".ignore",
        rules: {
            oldPass: {
                required: true
            },
            newPass: {
                required: true
            },
            confirmPassword: {
                required: true,
                equalTo: "#newPass"
            },
        },
        messages: {
            oldPass: "Field is Required",
            newPass: "Field is Required",
            confirmPassword: "Field is Required",
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
            var datastring = $("#resetPassword").serialize();

            $.ajax({
                type: "POST",
                url: "/employee/resetPassword",
                data: datastring,
                async: false,
                success: function (data) {
                    if (data == 0) {
                        swal("", "Password Reset Success.");
                    } else {
                        swal("", "Invalid Old Password");
                    }
                },
                error: function () {
                    alert('Something went Wrong');
                }
            });
        }
    });
});

