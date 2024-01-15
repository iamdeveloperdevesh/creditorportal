function show_chart(data) {
    // debugger;
//    var chart = Highcharts.chart('socialads1', {
//        chart: {
//            type: 'column'
//        },
//        title: false,
//        xAxis: {
//            categories: ['Self', 'Spouse', 'Daughter', 'Mother', 'Father','Son']
//        },
//        colors: ['#F5CA3F', '#E5726D', '#12C599', '#5F73F2'],
//        yAxis: {
//        yAxis: {
//            min: 0,
//            title: false
//        },
//        tooltip: {
//            pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
//            shared: true
//        },
//        plotOptions: {
//            column: {
//                stacking: 'column'
//            }
//        },
//        series: [{
//                name: 'Add Member',
//                data: [1,2,3,4,5,6]
//            }, {
//                name: 'Remove Member',
//                data: [7,8,9,10,11]
//            }
//        ]
//    }
//    });

    Highcharts.chart('socialads', {
        chart: {
            type: 'column'
        },
        title: false,
        xAxis: {
            categories: ['Self', 'Spouse', 'Daughter', 'Mother', 'Father', 'Son']
        },
        colors: ['#F5CA3F', '#E5726D', '#12C599', '#5F73F2'],
        yAxis: {
            min: 0,
            title: false
        },
        tooltip: {
            pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
            shared: true
        },
        plotOptions: {
            column: {
                stacking: 'column'
            }
        },
        series: [{
                name: 'Add Member',
                data: [data[0], data[1], data[2], data[3], data[4], data[5]]
            }, {
                name: 'Remove Member',
                data: [data[6], data[7], data[8], data[9], data[10], data[11]]
            }
        ]
    });
}

$(document).ready(function() {
    $("#from_date").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100Y:",
        onSelect: function(dateText, inst) {
            $("#to_date").datepicker("option", "minDate",
                    $("#from_date").datepicker("getDate"));
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
        //minDate: "-100Y +1D"
    });
    $.ajax({
        url: "/employer/get_all_policy_numbers",
        type: "POST",
        data: {employer: "true"},
        async: false,
        dataType: "json",
        success: function(response) {
            $('#policy_no').empty();
            $('#policy_no').append('<option value=""> Select policy type</option>');
            for (i = 0; i < response.length; i++) {
                var date = response[i].end_date.split("-");
                var date = new Date(Number(date[0]), Number(date[1]) - 1, Number(date[2]));
                var current_date = new Date();
//                        if(date > current_date){
                $('#policy_no').append('<option value="' + response[i].policy_no + '">' + (response[i].policy_sub_type_name + response[i].desgn_name) + '</option>');
//                        }
            }
        }
    });

    var self = 0;
    var son = 0;
    var daughter = 0;
    var spouse = 0;
    var mother = 0;
    var father = 0;
    var inactive_self = 0;
    var inactive_daughter = 0;
    var inactive_son = 0;
    var inactive_spouse = 0;
    var inactive_mother = 0;
    var inactive_father = 0;
    data = [];
//          $('#apply').on('click', function() {
//             var from_date = $("#from_date").val();
//             var policy_no = $("#policy_no").val();
//             var to_date = $("#to_date").val();
//              if(policy_no == ""){
//                 swal("","please select policy_no");
//                 return false;
//             }
//             if(from_date == ""){
//                 swal("","please select from date");
//                 return false;
//             }
//              if(to_date == ""){
//                 swal("","please select to date");
//                 return false;
//             }
//        $.ajax({
//                url: "/get_family_details_from_policy_no",
//                type: "POST",
//                async: false,
//                data:{policy_no : $('#policy_no option:selected').val(), from_date: from_date, to_date:to_date,status : "Active,In-process" ,employer: true},
//                dataType: "json",
//                success: function (response) {
//                   console.log(response);
//                  if(response.length != 0) {
//                    for (i = 0; i < response.length; i++) {
//
//                    if(response[i]["relationship"] == "Self"){
//                        self++;
//                    }
//                     if(response[i]["relationship"] == "Son"){
//                        son++;
//                    }
//                     if(response[i]["relationship"] == "Daughter"){
//                        daughter++;
//                    }
//                     if(response[i]["relationship"] == "Mother"){
//                        mother++;
//                    }
//                     if(response[i]["relationship"] == "Father"){
//                        father++;
//                    }
//                     if(response[i]["relationship"] == "Spouse"){
//                        spouse++;
//                    }
//                   data = [];
//                   data[0] = self
//                   data[1] = spouse
//                   data[2] = daughter
//                   data[3] = mother
//                   data[4] = father
//                   data[5] = son
//                    show_chart(data);
//        }
//    }
//        else{
//                swal("","no members are present in this policy");
//            }
//        }
//            });
//             $.ajax({
//                url: "/get_family_details_from_policy_no",
//                type: "POST",
//                async: false,
//                data:{policy_no : $('#policy_no option:selected').val(), status : "In-active" ,employer: true},
//                dataType: "json",
//                success: function (response) {
//                  if(response.length != 0) {
//                    for (i = 0; i < response.length; i++) {
//
//                    if(response[i]["relationship"] == "Self"){
//                        inactive_self++;
//                    }
//                     if(response[i]["relationship"] == "Son"){
//                        inactive_son++;
//                    }
//                     if(response[i]["relationship"] == "Daughter"){
//                        inactive_daughter++;
//                    }
//                     if(response[i]["relationship"] == "Mother"){
//                        inactive_mother++;
//                    }
//                     if(response[i]["relationship"] == "Father"){
//                        inactive_father++;
//                    }
//                     if(response[i]["relationship"] == "Spouse"){
//                        inactive_spouse++;
//                    }
//                   data[6] = inactive_self
//                   data[7] = inactive_spouse
//                   data[8] = inactive_daughter
//                   data[9] = inactive_mother
//                   data[10] = inactive_father
//                   data[11] = inactive_son
//                    show_chart(data);
//                }
//            }
//        }
//       });
//    });
    $('#apply').on('click', function() {
        var from_date = $("#from_date").val();
        var policy_no = $("#policy_no").val();
        var to_date = $("#to_date").val();
        if (policy_no == "") {
            swal("", "please select policy_no");
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
            url: "/employer/dashboard/get_member_active_endorsement_from_policy_no",
            type: "POST",
            async: false,
            data: {policy_no: $('#policy_no option:selected').val(), from_date: from_date, to_date: to_date},
            dataType: "json",
            success: function(response) {
                if (response.length != 0)
                {
                    self = 0;
                    son = 0;
                    daughter = 0;
                    mother = 0;
                    father = 0;
                    spouse = 0;
                    for (i = 0; i < Object.keys(response).length; i++) {
                        if (response.self) {
                            self = response.self;
                        }
                        if (response.son) {
                            son = response.son;
                        }
                        if (response.daughter) {
                            daughter = response.daughter;
                        }
                        if (response.mother) {
                            mother = response.mother;
                        }
                        if (response.father) {
                            father = response.father;
                        }
                        if (response.spouse) {
                            spouse = response.spouse;
                        }


                    }
                    data[0] = self
                    data[1] = spouse
                    data[2] = daughter
                    data[3] = mother
                    data[4] = father
                    data[5] = son

                    show_chart(data);
                } else
                {
                    swal("", "no members are present in this policy");
                }
            }
        });
        $.ajax({
            url: "/employer/dashboard/get_member_inactive_endorsement_from_policy_no",
            type: "POST",
            async: false,
            data: {policy_no: $('#policy_no option:selected').val(), from_date: from_date, to_date: to_date},
            dataType: "json",
            success: function(response) {
                if (response.length != 0) {
                    for (i = 0; i < Object.keys(response).length; i++) {

                        if (response.self) {
                            inactive_self = response.self;
                        }
                        if (response.son) {
                            inactive_son = response.son;
                        }
                        if (response.daughter) {
                            inactive_daughter = response.daughter;
                        }
                        if (response.mother) {
                            inactive_mother = response.mother;
                        }
                        if (response.father) {
                            inactive_father = response.father;
                        }
                        if (response.spouse) {
                            inactive_spouse = response.spouse;
                        }


                    }
                    data[6] = inactive_self
                    data[7] = inactive_spouse
                    data[8] = inactive_daughter
                    data[9] = inactive_mother
                    data[10] = inactive_father
                    data[11] = inactive_son
                    show_chart(data);
                }

            }
        });
    });
});
