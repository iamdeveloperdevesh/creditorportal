 $(document).ready(function(){
    $.ajax({
        url: "/get_all_insurer",
        type: "POST",
        success: function (response) {
        	var data_res = JSON.parse(response);
        	 $('#insurer_id').empty();
        	 $('#insurer_id').append('<option value=""> Select Insurer Name</option>');
        	   $.each(data_res, function (index, value) {
        	   	 $('#insurer_id').append('<option class="" value="' + value.insurer_id  + '">' + value.ins_co_name + '</option>');
    	 });
}
        });
         // on insurer id policy type
 $("#insurer_id").change(function(){
 	 var insurer_id = $(this).val();
 	  $.ajax({
        url: "/get_policytype_on_insurer",
        type: "POST",
        data:{insurer_id:insurer_id},
        success: function (response) {
        	var data_res = JSON.parse(response);
        	 $('#policy_type').empty();
        	 $('#policy_type').append('<option value=""> Select Policy Type</option>');
        	   $.each(data_res, function (index, value) {
        	   	$('#policy_type').append('<option class="" value="' + value.policy_sub_type_id  + '">' + value.policy_sub_type_name + '</option>');
    	 });
}
        });
 });
         // on policy_type get policy_no
  $("#policy_type").change(function(){
  	 var policy_type = $(this).val();
                        var insurer_id = $('#insurer_id').val();
  	  $.ajax({
        url: "/get_policyno_on_policytype",
        type: "POST",
        data:{policy_type:policy_type,insurer_id:insurer_id},
        success: function (response) {
        	var data_res = JSON.parse(response);
        	 $('#policy_no').empty();
        	 $('#policy_no').append('<option value=""> Select Policy #</option>');
        	   $.each(data_res, function (index, value) {
        	   	$('#policy_no').append('<option class="" value="' + value.policy_no  + '">' + value.policy_no + '</option>');
 });
}
        });

  });
          $("#from_date").datepicker
            ({
                dateFormat: "dd-mm-yy",
                prevText: '<i class="fa fa-angle-left"></i>',
                nextText: '<i class="fa fa-angle-right"></i>',
                changeMonth: true,
                changeYear: true,
                yearRange: "-100Y:",
                onSelect: function(date) {
                    var date = $(this).datepicker("getDate");
                    var tempStartDate = new Date(date);
                    var $returning_on = $("#to_date");
                    tempStartDate.setDate(date.getDate() + 1);
                    $returning_on.datepicker("option", "minDate", tempStartDate);
                    $returning_on.datepicker("option", "maxDate", new Date());
                },
                maxDate: new Date(),
                minDate: "-100Y +1D"
            });
            $("#to_date").datepicker({
                dateFormat: "dd-mm-yy",
                prevText: '<i class="fa fa-angle-left"></i>',
                nextText: '<i class="fa fa-angle-right"></i>',
                changeMonth: true,
                changeYear: true,
                yearRange: "-100Y:",
                maxDate: new Date(),
                // minDate: "-100Y +1D"
            });
            
            $("#sub_btn").click(function(){
                var insurer_id = $('#insurer_id option:selected').val();
                var policy_type = $('#policy_type option:selected').val();
                var policy_no = $('#policy_no option:selected').val();
                var from_date = $("#from_date").val();
                var to_date = $("#to_date").val();
                if (insurer_id == "") {
                    swal("", "please select employer name");
                    return false;
                }
                if (from_date == "") {
                    swal("", "please select from date");
                    return false;
                }
                if (to_date == "") {
                    swal("", "please select to date");
                    return false;
                }
                $.ajax({
                    url: "/employer/get_details_from_float",
                    type: "POST",
                    async: false,
                    data: {policy_no: policy_no, insurer_id: insurer_id, policy_type: policy_type, from_date: from_date, to_date: to_date},
                    dataType: "json",
                    success: function(response) {

                        var data_str = '';
                    $("#tbody_data").html("");
                    for (i = 0; i < response.length; i++) {
                        data_str += '<tr>';
                        data_str += '<td><div class="form-group"><input class="form-control align-center" type="text" value="' + response[i].self_count['self_count'] + '" id="total_emp" readonly="" style="width: 100px;"></div></td>';
                        data_str += '<td><div class="form-group"><input class="form-control align-center" type="text" value="' + response[i].member_count['member_count'] + '" id="total_mem" readonly="" style="width: 100px;"></div></td>';
                        data_str += '<td><div class="form-group"><input class="form-control align-center" type="text" value="' + response[i].all_data[0]['sum_insured'] + '" id="sum_insured" readonly="" style="width: 100px;"></div></td>';
                        data_str += '<td><div class="form-group"><input class="form-control align-center" type="text" value="' + parseInt((response[i].all_data[0]['sum_premium'])).toFixed(2)+ '" id="sum_premium" readonly="" style="width: 160px;"></div></td>';
                        data_str += '<td><div class="form-group"><input class="form-control align-center" type="text" value="'+response[i].all_data[0]['policy_no']+'" id="policy_no" readonly="" style="width: 160px;"></div></td>';
                        data_str += '<td> <div class=""><button type="button" class="mb-3 view_btn" style="background: transparent; border: none;" id="" value="' + response[i].all_data[0]['policy_no'] + '"><img src="/public/assets/images/new-icons/view_new.png"></button></div></td>';
                        data_str += '<td> <div class=""><button type="button" class="mb-3 pdf_btn" style="background: transparent; border: none;" id="" value="' + response[i].all_data[0]['policy_no'] + '"><img src="/public/assets/images/new-icons/namu.png"></button></div></td>';
                        //data_str += '<td> <div class=""><button type="button" class="btn sub-btn btn-md mb-3 cd_balance_pdf_btn" style="background-color: #046d66;  color: #fff; !important" id="" value="' + response[i].all_data[0]['policy_no'] + '">Download Pdf</button></div></td>';//
                        data_str += '<td> <div class=""><button type="button" class="mb-3 cd_balance_excel_btn" style="background: transparent; border: none;" id="" value="' + response[i].all_data[0]['policy_no'] + '"><img src="/public/assets/images/new-icons/excel1.png"></button></div></td>';
                        data_str += '</tr>';
                    }
                    $("#tbody_data").html(data_str);
                    }
                });
            });
            $(document).on('click', '.view_btn', function() {
                var policy_no = $(this).val();
                if (policy_no != '')
                {
                    $(".float_summary").removeAttr('style', 'display:none');
                    $(".float_data").removeAttr('style', 'display:none');
                    $.ajax({
                        url: "/employer/get_summary_of_float",
                        type: "POST",
                        async: false,
                        data: {policy_no: policy_no},
                        dataType: "json",
                        success: function(response) {
                            $("#total_deposite").val(response.amount);
                            $("#policy_num").val(policy_no);
                        }
                    });
                    $.ajax({
                        url: "/employer/get_summary_from_policy_member",
                        type: "POST",
                        async: false,
                        data: {policy_no: policy_no,from_date:$("#from_date").val(),to_date:$("#to_date").val()},
                        dataType: "json",
                        success: function(response) {
                            $("#float_utilized_value").val(response.sum_insured);
                            var total_dep = parseInt($("#total_deposite").val().replace(/,/g, ""));
                            var utilized = parseInt((response.sum_insured).replace(/,/g, ""));
                            var data = (total_dep - utilized);
                            $("#balance_amt").val(data);
                        }
                    });
                }
            });
            var d = new Date();
            var strDate = d.getDate() + "-" + (d.getMonth() + 1) + "-" + d.getFullYear();
            var date = strDate.split("-");
            var display_date = date[1] + '-' + date[0] + '-' + date[2];
            $("#deposite_date").datepicker({
                changeMonth: true,
                changeYear: true,
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: "dd-mm-yy"
            });
            $("#date").datepicker({
                changeMonth: true,
                changeYear: true,
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: "dd-mm-yy"
            });
            $('#amount').keyup(function(e) {
                var $th = $(this);
                if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
                    $th.val($th.val().replace(/[^0-9]/g, function(str) {
                        return '';
                    }));
                }
                return;
            });

            $("body").on('keyup', "#transaction_no", function(e) {
                var $th = $(this);
                if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
                    $th.val($th.val().replace(/[^A-Z a-z-,0-9\s]/g, function(str) {
                        return '';
                    }));
                }
                return;
            });

            $("#bank_name").keyup(function(e) {
                var $th = $(this);
                if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
                    $th.val($th.val().replace(/[^A-Z a-z\s]/g, function(str) {
                        return '';
                    }));
                }
                return;
            });

            $("#branch_name").keyup(function(e) {
                var $th = $(this);
                if (e.keyCode != 46 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) {
                    $th.val($th.val().replace(/[^A-Z a-z\s]/g, function(str) {
                        return '';
                    }));
                }
                return;
            });
            $("#form_data").validate({
                rules: {
                    float_id: {
                        required: true
                    },
                    deposite_date: {
                        required: true
                    },
                    payment_mode: {
                        required: true
                    },
                    amount: {
                        required: true
                    },
                    transaction_no: {
                        required: true
                    },
                    date: {
                        required: true
                    },
                    bank_name: {
                        required: true
                    },
                    branch_name: {
                        required: true
                    },
                },
                messages: {
                    float_id: "Please specify float id",
                    deposite_date: "Please specify deposite date",
                    payment_mode: "Please specify payment mode",
                    amount: "Please specify amount",
                    transaction_no: "Please specify transaction no",
                    date: "Please specify date",
                    bank_name: "Please specify bank name",
                    branch_name: "Please specify branch name",

                },
                submitHandler: function(form) {
                    var all_data = $("#form_data").serialize();
                    $.ajax({
                        type: 'POST',
                        url: '/employer/save_cd_balance',
                        data: all_data,
                        success: function(result) {
                            var data_response = JSON.parse(result);
                            if (data_response == true) {
                                swal("", "CD Balance Inserted Successfully");
                                window.location.reload();
                            } else
                            {
                                $.each(data_response.messages, function(key, value) {
                                    var element = $('#' + key);
                                    element.closest('div.data').find('p').remove();
                                    element.after(value);
                                });
                            }
                        }
                    });
                }
            });
            $(document).on('click', '.pdf_btn', function() {
                var policy_no = $(this).val();
                window.location = '/employer/cd_balance_pdf_generation?policy_no=' + policy_no + "&from_date=" + $("#from_date").val() + "&to_date=" + $("#to_date").val();

            });
            $(document).on('click', '.cd_balance_pdf_btn', function() {
                var policy_no = $(this).val();
                var from_date = $("#from_date").val();
                var to_date = $("#to_date").val();
                window.location = '/employer/cd_balance_report_pdf_generation?policy_no=' + policy_no + '&from_date=' + from_date + '&to_date=' + to_date;

            });
            $(document).on('click', '.cd_balance_excel_btn', function() {
                var policy_no = $(this).val();
                var from_date = $("#from_date").val();
                var to_date = $("#to_date").val();
                window.location = '/employer/cd_balance_report_excel_generation?policy_no=' + policy_no + '&from_date=' + from_date + '&to_date=' + to_date;

            });
});