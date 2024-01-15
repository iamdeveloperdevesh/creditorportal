$(document).on('change', '#emp_pincode', function(){
    $.ajax({
        url: "/get_state_city",
        type: "POST",
        data: {
            "pincode": $(this).val()
        },
        async: false,
        dataType: "json",
        success: function (response) {
            console.log(response);
        $("#emp_state").val(response.state_name);
          $("#emp_city").val(response.city_name);
    }
});
 });

$(document).ready(function () {
$("#dob").datepicker({
 dateFormat: "dd-mm-yy"
});
    $("#bank_name").empty();
    $("#bank_branch").empty();
    $("#bank_city").empty();
    $("#ifsc_code").empty();
    $("#bank_name").append('<option value="" selected>Select Bank</option>');
    $.post("/employee/get_bank_name", {}, function(e) {
        var ifscMasters = JSON.parse(e);
        for(i = 0; i < ifscMasters.length; ++i) {
            $("#bank_name").append("<option value='"+ifscMasters[i].bank_name+"'>"+ifscMasters[i].bank_name+"</option>");
        }
    });
    $('#bank_name').on('change', function() {
        $('#ifsc_code').val("");
        $("#bank_city").val("");
        $("#bank_branch").val("");
        getBankCity(this.value);
    });
    if($('#ifsc_code').val() && $('#ifsc_code').val().trim().length > 0)
        getIfscCode($('#ifsc_code').val());
});
$("#bank_city").on("change", function(e) {
    getBankBranch(this.value);
});
function getBankCity(bank_name, bank_name_value) {
    $.post("/employee/get_bank_city", {
        "bank_name":bank_name
    }, function(e) {
        var obj = JSON.parse(e);
        $('#bank_city').empty();
        $('#bank_city').append('<option value="" selected>Select City</option>');
        for(i = 0; i < obj.length; ++i) {
            $("#bank_city").append("<option value='"+obj[i].bank_city+"'>"+obj[i].bank_city+"</option>");
        }
        if(bank_name_value)
            $("#bank_city").val(bank_name_value);
    });
}
$('#bank_branch').on('change', function() {
    $('#ifsc_code').val(this.selectedOptions[0].getAttribute("data-ifsc"));
});
$('#ifsc_code').on('keyup', function() {
    if(this.value.length == 11) {
        getIfscCode(this.value);
    }
});
function getIfscCode(code) {
    $.post('/employee/get_ifsc_code', {
        'ifsc_code':code
    }, function(e) {
        var obj = JSON.parse(e);
        $('#bank_name').val(obj.bank_name);
        getBankCity(obj.bank_name,obj.bank_city);
        getBankBranch(obj.bank_city,obj.bank_branch);
        $('#ifsc_code').val(obj.ifsc_code);
    });
}
function getBankBranch(bank_city, bank_city_value) {
    $.post("/employee/get_bank_branch", {
        "bank_name":$('#bank_name').val(),
        "bank_city":bank_city,
    }, function(e) {
        var obj = JSON.parse(e);
        $('#bank_branch').empty();
        $('#bank_branch').append('<option value="" selected>Select Branch</option>');
        for(i = 0; i < obj.length; ++i) {
            $("#bank_branch").append("<option data-id='"+obj[i].bank_id +"' data-ifsc='"+obj[i].ifsc_code +"' value='"+obj[i].bank_branch+"'>"+obj[i].bank_branch+"</option>");
        }
        if(bank_city_value)
            $("#bank_branch").val(bank_city_value);
    });
}
$('#empDetForm').validate({
     ignore: ".ignore",
      rules: {
        dob: {
            required: true 
        },
        emg_cont_name: {
            required: true
        },
        alt_email: {
            required: true
        },
        mob_no: {
            required: true,
number: true
        },
        email: {
            required: true
        },
        emg_cno: {
            required: true,
number: true
        }
    },
    messages: {
        dob: "Please provide date of birth",
        emg_cont_name: "Please provide Emergency Contact Name.",
        mob_no: "Please provide contact No.",
        email: "Please provide Email.",
        emg_cno: "Please provide Emergency Contact No.",
    },
    invalidHandler: function(f, v) {
    },
    errorElement : 'div',
        errorPlacement: function(error, element) {
        var placement = $(element).data('error');
        if (placement) {
            $(placement).append(error);
        } else {
            error.insertAfter(element);
        }
    },
       submitHandler: function(form) {
        var data = new FormData();

        data.append("mob_no",$("#mob_no").val());
        data.append("address",$("#address").val());
        data.append("emp_pincode",$("#emp_pincode").val());
        data.append("emp_city",$("#emp_city").val());
        data.append("emp_state",$("#emp_state").val());
        
        $.ajax({
            type: "POST",
            url: "/employee/profile_save",
            data: data,
            processData:false,
            contentType:false,
            cache:false,
            async:false,
            success: function(data) {
var res = JSON.parse(data);
if(res.emp_detail == "success") {
swal("","profile Successfully saved");
return;
}


console.log(res.messages);

if(res.messages){
                       $.each(res.messages, function(key, value){
                       var element = $('#' + key);
                       element.closest('div.res').find('.error').remove();
                       element.after(value);
                        });
                    }

            },
            error: function() {
                swal('','Something went Wrong');
            }
        });
      }
    });
    $('#acc_no').keyup(function(e) {
        var $th = $(this);
        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^0-9]/g, function(str) { return ''; } ) );
        }return;
    });
    $('#ac_holder_name').keyup(function(e) {
        var $th = $(this);

        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[0-9]/g, function(str) { return ''; } ) );
        }return;
    });

    $("#bankIdDoc").on("change", function() {
        var input = document.querySelector("input[name='bankIdDoc']");
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("#bankIdPreview").attr("data-href",e.target.result);
              
            };

            reader.readAsDataURL(input.files[0]);
        }
    });

    $("#bankIdPreview").on("click", function() {
        var attr = $(this).attr("data-href");
        console.log(attr);

        var image = new Image();
                    image.src = attr;

                    var w = window.open("");
                    w.document.write(image.outerHTML);
    });

    $("#bankDoc").on("change", function() {
        var input = document.querySelector("input[name='bankDoc']");
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("#bankPreview").attr("data-href",e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    });

    $("#bankPreview").on("click", function() {
        var attr = $(this).attr("data-href");
        console.log(attr);

        var image = new Image();
                    image.src = attr;

                    var w = window.open("");
                    w.document.write(image.outerHTML);
    });

    $('#bankForm').validate({
        ignore: ".ignore",
          rules: {
            ifsc_code: {
                required: true 
            },
            bank_name: {
                required: true 
            },
            bank_city: {
                required: true 
            },
            bank_branch: {
                required: true 
            },
            acc_name: {
                required: true 
            },
            acc_no: {
                required: true,
                number:true 
            },
            re_acc_no: {
                required: true,
                equalTo: "#acc_no"
            },
            bankDoc: {
                required: true
            },
            bankIdDoc: {
                required: true
            },
        },
        messages: {
            ifsc_code: "Please provide Ifsc code",
            bank_name: "Please select Bank Name",
            bank_branch: "Please select Branch of bank",
            acc_name: "Please provide Account Name",
            acc_no: "Please provide Account Number",
            re_acc_no: "Please Re-enter Account Number",
            bankDoc: "Please Provide last year bank statement",
            bankIdDoc: "Please Provide Bank Id Proof",
            bank_city: "Please select city of bank",
        },
        invalidHandler: function(f, v) {
        },
        errorElement : 'div',
            errorPlacement: function(error, element) {
            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error);
            } else {
                error.insertAfter(element);
            }
        },
          submitHandler: function(form) {
            var bankDoc = document.querySelector("input[name='bankDoc']").files[0];
            var bankIdDoc = document.querySelector("input[name='bankIdDoc']").files[0];
            var data = new FormData();
            data.append('bank_id', $("#bank_branch").find('option:selected').attr("data-id"));
            data.append('acc_name', $("#acc_name").val());
            data.append('ifsc_code', $("#ifsc_code").val());
            data.append('acc_no', $("#acc_no").val());
            data.append('bankDoc', bankDoc);
            data.append('bankIdDoc', bankIdDoc);
            $.ajax({
                type: "POST",
                url: "/employee/bank_save",
                data: data,
                processData:false,
                contentType:false,
                cache:false,
                async:false,
                success: function(data) {
                    
                    if(data == "1") {
                        swal("","Bank Details Successfully saved");
                    }else {
                        swal("","Invalid File Format");
                    }
                }
            });
          }
        });

        $("#docName").change(function(){
            readURL(this);
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#docPreview').attr('href', e.target.result).show();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $('#identityProof').validate({
            ignore: ".ignore",
              rules: {
                family_id: {
                    required: true 
                },
                idType: {
                    required: true 
                },
                idNo: {
                    required: true 
                },
                docName: {
                    required: true 
                }
            },
            messages: {
                family_id: "Please select family member",
                idType: "Please select Id Proof",
                idNo: "Please provide Id No.",
                idNo: "Please provide Account Name",
                docName: "Please provide related Documents"
            },
            invalidHandler: function(f, v) {
            },
            errorElement : 'div',
                errorPlacement: function(error, element) {
                var placement = $(element).data('error');
                if (placement) {
                    $(placement).append(error);
                } else {
                    error.insertAfter(element);
                }
            },
              submitHandler: function(form) {
                var docName = document.querySelector("input[name='docName']").files[0];
                var data = new FormData();
                data.append('family_id', $("#family_id").val());
                data.append('idType', $("#idType").val());
                data.append('idNo', $("#idNo").val());
                data.append('docName', docName);
                $.ajax({
                    type: "POST",
                    url: "/employee/identity_proof_save",
                    data: data,
                    processData:false,
                    contentType:false,
                    cache:false,
                    async:false,
                    success: function(data) {
                        var e = JSON.parse(data);
                        if(e.errorCode == "0") { 
                            $("#bankPreview")
                            .attr("href",data.path)
                            .css("display","inline");
                            swal("","Identity Proof Successfully saved");
                        }else if(e.errorCode == "0") {
                            swal("","Invalid File Format");
                        }else {
                            swal("","Something went Wrong");
                        }
                    }
                });
              }
            });

        $('#passwordForm').validate({
            ignore: ".ignore",
              rules: {
                oldPass: {
                    required: true 
                },
                newPass: {
                    required: true 
                },
                rePass: {
                    required: true,
                    equalTo:"#newPass"
                }
            },
            messages: {
                oldPass: "Please enter old Password",
                newPass: "Please enter New Password",
                rePass: "Please Re-enter password"
            },
            invalidHandler: function(f, v) {
            },
            errorElement : 'div',
                errorPlacement: function(error, element) {
                var placement = $(element).data('error');
                if (placement) {
                    $(placement).append(error);
                } else {
                    error.insertAfter(element);
                }
            },
              submitHandler: function(form) {
                var datastring = $("#passwordForm").serialize();
                $.ajax({
                    type: "POST",
                    url: "/employee/save_password",
                    data: datastring,
                    success: function(data) {
                        var data = JSON.parse(data);
                        if(data.error)
                            swal(data.error);
                        else 
                            swal("","Incorrect File format");
                    },
                    error: function() {
                        swal('','Something went Wrong');
                    }
                });
              }
            });

$('#emg_cno').keyup(function(e) {
        var $th = $(this);

        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^0-9]/g, function(str) { return ''; } ) );
        }return;
    });
$("#acc_name").keyup(function(e) {
var $th = $(this);
        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^A-Za-z\s]/g, function(str) { return ''; } ) );
        }return;
   });

$('#mob_no').keyup(function(e) {
        var $th = $(this);

        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^0-9]/g, function(str) { return ''; } ) );
        }return;
    });

$('#re_acc_no').keyup(function(e) {
        var $th = $(this);

        if(e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
            $th.val( $th.val().replace(/[^0-9]/g, function(str) { return ''; } ) );
        }return;
    });