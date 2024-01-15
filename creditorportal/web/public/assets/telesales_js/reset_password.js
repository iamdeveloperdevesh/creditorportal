$('document').ready(function () {
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
            confirmPassword: "Password donot match",
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
                url: "/tls_resetpass",
                data: datastring,
                async: false,
                success: function (data) {
                    var title = (data == 0) ? 'Success' : 'Warning';
                    var type = (data == 0) ? 'success' : 'warning';
                    var message = (data == 0) ? 'Password Reset Successfully.Your current password will expire after 30 days.' : 'Invalid Old Password';
                    swal({
                        title: title,
                        text: message,
                        type: type,
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                        function () {
                            if (data == 0) {
                                location.replace('/');
                            }
                        });

                },
                error: function () {
                    alert('Something went Wrong');
                }
            });
        }
    });
});

