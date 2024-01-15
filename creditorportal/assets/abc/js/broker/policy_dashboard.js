$('document').ready(function () {

    $.ajax({
        type: "POST",
        url: "/broker/get_all_dashboard_employer",
        async: false,
        success: function (e) {
            // debugger;
            var data = JSON.parse(e);
            if (data.length != 0) {
                $('#employer_name').append('<option value=""> Select Employer</option>');
                 $('#employers_name').append('<option value=""> Select Employer</option>');
                for (i = 0; i < data.length; i++) {
                    $('#employer_name').append('<option   data-id = ' + data[i].company_id + '  value="' + data[i].company_id + '">' + data[i].comapny_name + '</option>');
                     $('#employers_name').append('<option   data-id = ' + data[i].company_id + '  value="' + data[i].company_id + '">' + data[i].comapny_name + '</option>');

                }
            }
        },
        error: function () {

        }
    });


    $("#employer_name").change(function () {
        var employer_id = $("#employer_name").val();
        $.ajax({
            url: "/broker/get_all_policy_type_per_employer",
            type: "POST",
            data: {
                "employer_id": employer_id
            },
            dataType: "json",
            success: function (response) {
                $('#policy_type').empty();
                if (response.length != 0) {
                    $('#policy_type').append('<option value=""> Select Policy type</option>');
                    $.each(response, function (index, value) {
                        $('#policy_type').append('<option  value="' + value['policy_type_id'] + '">' + value['policy_name'] + '</option>');

                    });

                }
            }
        });

    });


    $("#policy_type").change(function () {
        var policy_type_id = $("#policy_type").val();
        var employer_id = $("#employer_name").val();
        $.ajax({
            url: "/broker/get_all_policy_subtype_per_employer",
            type: "POST",
            data: {
                "employer_id": employer_id,
                "policy_type_id": policy_type_id
            },
            dataType: "json",
            success: function (response) {
                console.log(response);
                $('#policy_sub_type').empty();
                if (response.length != 0) {
                    $('#policy_sub_type').append('<option value=""> Select Policy type</option>');
                    for (i = 0; i < response.length; i++) {

                        $('#policy_sub_type').append('<option value="' + response[i].policy_sub_type_id + '">' + response[i].policy_sub_type_name + '</option>');

                    }

                }
            }
        });

    });

    $("#policy_sub_type").change(function () {
        var policy_sub_type = $("#policy_sub_type").val();
        var employer_name = $('#employer_name option:selected').val();
        var policy_type = $('#policy_type option:selected').val();

        $.ajax({
            url: "/broker/get_all_policy_as_per_subtype",
            type: "POST",
            data: {
                "policy_sub_type": policy_sub_type,
                "employer_name": employer_name,
                "policy_type": policy_type

            },
            dataType: "json",
            success: function (response) {
                var data_str = '';
                var flag = false;
                
                for (i = 0; i < response.length; i++) {
                // alert(response[i].TPA_id);
                    if(response[i].TPA_id != null || response[i].TPA_id != '' ){
                        flag = true;
                    }
                    data_str += '<tr>';
                    data_str += '<td><div class="form-group"><input class="form-control align-center wd-90" type="text" value="' + response[i].policy_no + '" id="member_id" style="width: 170px;" readonly=""></div></td>';

                    data_str += '<td><div class="form-group"><input class="form-control align-center wd-90" type="text" value="' + response[i].ins_co_name + '" id="member_id" style="width: 170px;" readonly=""></div></td>';
                    data_str += '<td class="tpa_name" style="display:none"><div class="form-group"><input class="form-control align-center wd-90" type="text" value="' + response[i].TPA_name + '"  style="width: 170px; " readonly=""></div></td>';
                    data_str += '<td><div class="form-group"><input class="form-control align-center wd-90" type="text" value="' + response[i].comapny_name + '" id="member_id" style="width: 170px;" readonly=""></div></td>';

                    data_str += '<td><div class="form-group"><input class="form-control align-center wd-90" type="text" value="' + response[i].start_date + '" id="member_id" style="width: 100px;" readonly=""></div></td>';
                    data_str += '<td><div class="form-group"><input class="form-control align-center wd-90" type="text" value="' + response[i].end_date + '" id="member_id" style="width: 100px;" readonly=""></div></td>';
                    data_str += '<td><div class="form-group"><input class="form-control align-center wd-90" type="text" value="' + response[i].policy_mem_sum_insured + '" id="member_id" style="width: 160px;" readonly=""></div></td>';
                    data_str += '<td><div class="form-group"><input class="form-control align-center wd-90" type="text" value="' + response[i].policy_mem_sum_premium + '" id="member_id" style="width: 160px;" readonly=""></div></td>';
                    data_str += '<td><div class="form-group"><input class="form-control align-center wd-90" type="text" value="' + response[i].member_count + '" id="member_id"  readonly=""></div></td>';
                    data_str += '<td><div class="form-group"> <input type="button" data-id="' + response[i].policy_id + '" id="export_excel" name="export_excel"  onclick="myFunction(' + response[i].policy_id + ')"  class="btn sub-btn" value="Export"></div></td>';
                    data_str += '</tr>';
                }
                $("#tdata").html(data_str);
              
                    if(flag){
					
					 $(".tpa_name").attr("style", "display:block");
					 //$("#tpa_name").attr("style", "display:block");
					
					
				}
               

            }

        });

    });


// new function for display policy onchange of employer name


 $("#employers_name").change(function () {
     debugger;
        var employer_id = $("#employers_name").val();
        $.ajax({
            url: "/broker/get_all_policy_per_employer",
            type: "POST",
            data: {
                "employer_id": employer_id
            },
            dataType: "json",
            success: function (response) {
               var data_str1 = '';
               var count  = 0;
               console.log(response);
                if (response.length != 0) {
                  for (i = 0; i < response.length; i++) {
                    count ++;
                        data_str1 += '<tr>';
                     data_str1 += '<td><div class="form-group"><input class="form-control align-center wd-90" type="text" value="' + count + '" id="member_id" style="width: 170px;" readonly=""></div></td>';
                    data_str1 += '<td><div class="form-group"><input class="form-control align-center wd-90" type="text" value="' + response[i].policy_no + '" id="member_id" style="width: 170px;" readonly=""></div></td>';

                    data_str1 += '<td><div class="form-group"><input class="form-control align-center wd-90" type="text" value="' + response[i].ins_co_name + '" id="member_id" style="width: 170px;" readonly=""></div></td>';
                    data_str1 += '<td class="tpa_name" style="display:none"><div class="form-group"><input class="form-control align-center wd-90" type="text" value="' + response[i].TPA_name + '"  style="width: 170px; " readonly=""></div></td>';
                    data_str1 += '<td><div class="form-group"><input class="form-control align-center wd-90" type="text" value="' + response[i].comapny_name + '" id="member_id" style="width: 170px;" readonly=""></div></td>';

                  data_str1 += '<td><div class="form-group"><button type="button" data-id=' + response[i].policy_detail_id + ' data-mem-id=' + response[i].policy_detail_id + ' onclick="update_data(' + response[i].policy_detail_id + ')" value="' + response[i].policy_detail_id + '" href="" class=" btn update_mem" name="update_mem" id=' + response[i].policy_detail_id + ' >Update</button></div></td>';
                  
                    data_str1 += '</tr>';
                }
                $("#tdatas").html(data_str1); 

                }
            }
        });

    });



});

function myFunction(id) {
    $name = $('#employer_name option:selected').val();
    $policy_typ = $('#policy_type option:selected').val();
    $sub_type = $('#policy_sub_type option:selected').val();

    window.location = '/broker/get_excel_policy_no?employer_name=' + $('#employer_name option:selected').val() + '&policytype_id=' + $('#policy_type option:selected').val() + '&policy_id=' + id;
}

function update_data(id){
   window.location="get_policy?policy_id="+id; 
  
}
