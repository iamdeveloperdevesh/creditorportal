$('document').ready(function () {
    var startDate = $('#startDate').val().split("-");

    var startminDate = new Date();
    var endminDate = new Date();

    endminDate.setDate(endminDate.getDate() + 2);


    $('#startDate').datepicker({
        dateFormat: 'dd-mm-yy',
        showButtonPanel: true,
        changeMonth: true,
        minDate: startminDate,
        changeYear: true
    });

    $('#endDate').datepicker({
        dateFormat: 'dd-mm-yy',
        showButtonPanel: true,
        changeMonth: true,
        minDate: endminDate,
        changeYear: true
    });

    $("#empwindow").validate({
        rules: {
            startDate: {
                required: true
            },

            endDate: {
                required: true
            }
        },
        messages: {
            startDate: "Please select Start Date",
            endDate: "Please select End Date"
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
        submitHandler: function (form) {
            startDate = $('#startDate').val().split("-");
            endDate = $('#endDate').val().split("-");

            startDate = new Date(Number(startDate[2]), Number(startDate[1]) - 1, Number(startDate[0]));
            endDate = new Date(Number(endDate[2]), Number(endDate[1]) - 1, Number(endDate[0]));
            if (startDate.getTime() >= endDate.getTime()) {
                swal("","Enrollment End Date should be greater than Enrollment Start Date");
                return false;
            }

            

            $.ajax({
                url: "/employer/settings_save",
                type: "POST",
                data: $("#empwindow").serialize(),
                success: function (response) {
					swal({
                         title: "Success",
						//title: "Sorry You can not add more than 2 kids",
                        text: "Enrollment Successfully Updated",
                        type: "success",
                        showCancelButton: false,
                        confirmButtonText: "Ok!",
                        closeOnConfirm: true
                    },
                    function ()
                    {
                         location.reload();
                    });
                }
            });
        }
    });
});