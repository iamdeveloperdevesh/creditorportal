$(document).ready(function () {
    var proposal_id = 0;
    $(document).on( 'click', 'table tr', function (e) {
        if(e.target.textContent == "view") { //last view
            var row = $(this).closest('tr');
            var data = $('#example').dataTable().fnGetData(row);

            var emp_id = data[10];
            var policy_detail_id = data[11];
            proposal_id = data[12];

            $.post("/broker/getMemberProposalDet", {
                emp_id: emp_id,
                policy_detail_id: policy_detail_id
            }, function(e) {
                e = JSON.parse(e);

                if(e.length > 0) {
                    $("#familyModal").modal();
                    $("#familyTable").html("");
                    e.forEach(function(e) {
                        var str = "<tr><td>"+e.fr_name+"</td>";
                        str += "<td>"+e.emp_firstname+"</td>";
                        str += "<td>"+e.emp_lastname+"</td>";
                        str += "<td>"+e.gender+"</td>";
                        str += "<td>"+e.bdate+"</td>";
                        str += "<td>"+e.age+"</td>";
                        str += "<td>"+e.age_type+"</td>";
                        str += "<td>"+e.policy_mem_sum_insured+"</td></tr>";

                        $("#familyTable").append(str);
                    });

                }else {
                    alert("No Member");
                }
                console.log(e);
            })
        }
    });



    function saveProposalData(status) {
        $.post("/broker/proposalSave", {
            proposal_id: proposal_id,
            status: status
        }, function(e) {
            swal({
                title: "Success",
                text: status+" SuccessFully",
                type: "success",
                showCancelButton: false,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Ok",
                closeOnConfirm: true
              },
              function(){
                location.reload();
              });
        });
    }

    $("#proposalApprove").on("click", function() {
        saveProposalData("Approved");
    });

    $("#proposalReject").on("click", function() {
        saveProposalData("Rejected");
    });

    var table = $('#example').DataTable({
        "columnDefs": [
            {
                "targets": [ 10 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 11 ],
                "visible": false
            },
            {
                "targets": [ 12 ],
                "visible": false
            }
        ]
    });

    $.ajax({
        url: "/get_all_policy_numbers",
        type: "POST",
        async: false,
        dataType: "json",
        success: function (response) {
            // debugger;
            $("#masPolicy").empty();
            $("#masPolicy").append("<option value = ''>Select Policy Number</option>");
            for (i = 0; i < response.length; i++) {
                $("#masPolicy").append("<option data-customer = '" + response[i]['customer_search_status'] + "'  data-id = '" + response[i]['policy_sub_type_name'] + "' value =" + response[i]['policy_detail_id'] + ">" + response[i]['policy_no'] + "</option>");


            }
        }
    });




    $(document).on('change', '#masPolicy', function () {

        //customer search
        var policy_detail_id = $('#masPolicy option:selected').val();
        var customer = ($('#masPolicy option:selected').attr('data-customer'));
        if (policy_detail_id && customer == 'Y') {
            $("#customer_serach_button").show();



        }
        else {
            $("#customer_serach_button").hide();
        }
        if (policy_detail_id) {
            $("#subtypediv").show();
            $("#policySubType").removeClass("ignore");
        }
        else {
            $("#subtypediv").hide();
            $("#policySubType").addClass("ignore");
        }

        $("#policySubType").val($('#masPolicy option:selected').attr('data-id'));
        $("#policy_no").val($('#masPolicy option:selected').val());
        var policy_no = $('#masPolicy option:selected').val();

        $.post("/broker/getProposalData", {
            policy_no: policy_no
        }, function(e) {
            table
                .clear()
                .draw();
                
            e = JSON.parse(e);
            e.forEach(e1 => {
                arr = [
                    e1.proposal_no,
                    e1.emp_firstname,
                    e1.emp_lastname,
                    e1.proposal_no,
                    e1.status,
                    e1.mob_no,
                    e1.adhar,
                    e1.pancard,
                    e1.email,
                    e1.proposal_no,
                    e1.emp_id,
                    e1.policy_detail_id,
                    e1.proposal_id,
                    "view"
                ],

                
                table.row.add(arr).draw();
            });

            
        });
    });
});