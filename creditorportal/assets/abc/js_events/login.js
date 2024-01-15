

$.validator.addMethod('emailValidate', function (value, element, param) {

    if (value.length == 0)
    {
        return false;
    }
    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    return reg.test(value); // Compare with regular expression
}, 'Please enter a valid email address.');


$('#login_form').validate({

    rules: {
        email_id: {
            required: true
        },
        password: {
            required: true
        },

        email_id: {
            emailValidate: true
        },
    },
    invalidHandler: function (f, v) {
    },
    errorElement: 'div',
    errorPlacement: function (error, element) {

        var placement = $(element).data('error');

        if (placement) {
            $(placement).append(error);
        }else if(element[0]['type'] == "select-one"){
            error.insertAfter(element.next('.select2-container'));
        } else {
            error.insertAfter(element);
        }
    },
    submitHandler: function (form, event) {
        event.preventDefault();

       
        var datastring = $("#login_form").serialize();

        $.ajax({
            type: "POST",
            url: "/check_login",
            data: datastring,
            async: false,
            success: function(data) {
                var data = JSON.parse(data);
                if(data['success'] == 1){
                    alert("Login successfully");
                    window.location.replace("/Dashboard");
                }else{
                     alert("Invalid Username or Password");
                }
            },
        });
    }
});