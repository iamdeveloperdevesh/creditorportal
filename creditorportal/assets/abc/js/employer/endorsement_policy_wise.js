function show_chart(data) {
    var chart = AmCharts.makeChart("ambarchart3", {
        "type": "serial",
        "theme": "light",
        "categoryField": "year",
        "rotate": true,
        "startDuration": 1,
        "categoryAxis": {
            "gridPosition": "start",
            "position": "left"
        },
        "trendLines": [],
        "graphs": [{
                "balloonText": "Member Added:[[value]]",
                "fillAlphas": 0.8,
                "id": "AmGraph-1",
                "lineAlpha": 0.2,
                "title": "Member Added",
                "type": "column",
                "valueField": "Member Added",
                "fillColorsField": "color"

            },
            {
                "balloonText": "Member Removed:[[value]]",
                "fillAlphas": 0.8,
                "id": "AmGraph-2",
                "lineAlpha": 0.2,
                "title": "Member Removed",
                "type": "column",
                "valueField": "Member Removed",
                "fillColorsField": "color2",
                "columnWidth": 1
            }
        ],
        "guides": [],
        "valueAxes": [{
                "id": "ValueAxis-1",
                "position": "top",
                "axisAlpha": 0
            }],
        "allLabels": [],
        "balloon": {},
        "titles": [],
        "dataProvider": data,
        "export": {
            "enabled": false
        }

    });
}
//var data = [{
//        "year": "GMC",
//        "Member Added": 0,
//        "Member Removed": 0,
//        "color": "#8ad7bc",
//        "color2": "#243664"
//    }, {
//        "year": "GTL",
//        "Member Added": 0,
//        "Member Removed": 0,
//        "color": "#8ad7bc",
//        "color2": "#243664"
//    }, {
//        "year": "GPA",
//        "Member Added": 0,
//        "Member Removed": 0,
//        "color": "#8ad7bc",
//        "color2": "#243664"
//    }];

$(document).ready(function () {
    $("#from_date").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100Y:",
        onSelect: function (dateText, inst) {
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
        // minDate: "-100Y +1D"
    });
//     $('#apply').on('click', function() {
//             var from_date = $("#from_date").val();
//             var type = $("#policy_type").val();
//             var to_date = $("#to_date").val();
//             if(from_date == ""){
//                 swal("","please select from date");
//                 return false;
//             }
//             if(to_date == ""){
//                 swal("","please select to date");
//                 return false;
//             }
//             if(policy_type == ""){
//                 swal("","please select policy type");
//                 return false;
//             }
//              $.ajax({
//                url: "/employer/endorsement_policy_wise_query",
//                type: "POST",
//                async: false,
//                data:{from_date : from_date, to_date : to_date, type : type},
//                dataType: "json",
//                success: function (response) {
//
//                     if(response.length) {
//                         data[0]['Member Added'] = response.gmc_added;
//                         data[0]['Member Removed'] = response.gmc_removed;
//                         data[1]['Member Added'] = response.gtl_added;
//                         data[1]['Member Removed'] = response.gtl_removed;
//                         data[2]['Member Added'] = response.gpa_added;
//                         data[2]['Member Removed'] = response.gpa_removed;
//                     show_chart(data);
//                 }
//                     else{
//                         swal("","no members are present in this policy");
//                     }
//
//            }
//            });
//        });
    $('#apply').on('click', function () {
        var from_date = $("#from_date").val();
        var type = $("#policy_type").val();
        var to_date = $("#to_date").val();
        if (from_date == "") {
            swal("", "please select from date");
            return false;
        }
        if (to_date == "") {
            swal("", "please select to date");
            return false;
        }
        if (policy_type == "") {
            swal("", "please select policy type");
            return false;
        }
        $.ajax({
            url: "/employer/endorsement_policy_wise_query",
            type: "POST",
            async: false,
            data: {from_date: from_date, to_date: to_date, type: type},
            dataType: "json",
            success: function (response) {
                if (jQuery.isEmptyObject(response) == false) {
                    if (response.gmc_data != 0) {
                        var data = [{
                                "year": "GMC",
                                "Member Added": 0,
                                "Member Removed": 0,
                                "color": "#8ad7bc",
                                "color2": "#243664"
                            }];
                        data[0]['Member Added'] = response.gmc_added;
                        data[0]['Member Removed'] = response.gmc_removed;
                    }
                    if (response.gpa_data != 0) {
                        var data = [{
                                "year": "GPA",
                                "Member Added": 0,
                                "Member Removed": 0,
                                "color": "#8ad7bc",
                                "color2": "#243664"
                            }];
                        data[0]['Member Added'] = response.gpa_added;
                        data[0]['Member Removed'] = response.gpa_removed;
                    }
                    if (response.gtla_data != 0) {
                        var data = [{
                                "year": "GTL",
                                "Total Employee": 0,
                                "Enrolled": 0,
                                "color": "#8ad7bc",
                                "color2": "#243664"
                            }];
                        data[0]['Total Employee'] = response.gtl;
                    }
                    if ((response.gmc_data != 0) && (response.gpa_data != 0)) {
                        var data = [{
                                "year": "GMC",
                                "Member Added": 0,
                                "Member Removed": 0,
                                "color": "#8ad7bc",
                                "color2": "#243664"
                            },
                            {
                                "year": "GPA",
                                "Member Added": 0,
                                "Member Removed": 0,
                                "color": "#8ad7bc",
                                "color2": "#243664"
                            }];
                        data[0]['Member Added'] = response.gmc_added;
                        data[0]['Member Removed'] = response.gmc_removed;
                        data[1]['Member Added'] = response.gpa_added;
                        data[1]['Member Removed'] = response.gpa_removed;
                    }
                    if ((response.gmc_data != 0) && (response.gtla_data != 0)) {
                        var data = [{
                                "year": "GMC",
                                "Member Added": 0,
                                "Member Removed": 0,
                                "color": "#8ad7bc",
                                "color2": "#243664"
                            },
                            {
                                "year": "GTL",
                                "Member Added": 0,
                                "Member Removed": 0,
                                "color": "#8ad7bc",
                                "color2": "#243664"
                            }];
                        data[0]['Member Added'] = response.gmc_added;
                        data[0]['Member Removed'] = response.gmc_removed;
                        data[1]['Member Added'] = response.gtl_added;
                        data[1]['Member Removed'] = response.gtl_removed;
                    }
                    if ((response.gpa_data != 0) && (response.gtla_data != 0)) {
                        var data = [{
                                "year": "GPA",
                                "Member Added": 0,
                                "Member Removed": 0,
                                "color": "#8ad7bc",
                                "color2": "#243664"
                            },
                            {
                                "year": "GTL",
                                "Member Added": 0,
                                "Member Removed": 0,
                                "color": "#8ad7bc",
                                "color2": "#243664"
                            }];
                        data[0]['Member Added'] = response.gpa_added;
                        data[0]['Member Removed'] = response.gpa_removed;
                        data[1]['Member Added'] = response.gtl_added;
                        data[1]['Member Removed'] = response.gtl_removed;
                    }
                    if ((response.gmc_data != 0) && (response.gpa_data != 0) && (response.gtla_data != 0)) {
                        var data = [{
                                "year": "GMC",
                                "Member Added": 0,
                                "Member Removed": 0,
                                "color": "#8ad7bc",
                                "color2": "#243664"
                            }, {
                                "year": "GPA",
                                "Member Added": 0,
                                "Member Removed": 0,
                                "color": "#8ad7bc",
                                "color2": "#243664"
                            },
                            {
                                "year": "GTL",
                                "Member Added": 0,
                                "Member Removed": 0,
                                "color": "#8ad7bc",
                                "color2": "#243664"
                            }];
                        data[0]['Member Added'] = response.gmc_added;
                        data[0]['Member Removed'] = response.gmc_removed;
                        data[1]['Member Added'] = response.gtl_added;
                        data[1]['Member Removed'] = response.gtl_removed;
                        data[2]['Member Added'] = response.gpa_added;
                        data[2]['Member Removed'] = response.gpa_removed;
                    }
//                    data[0]['Member Added'] = response.gmc_added;
//                    data[0]['Member Removed'] = response.gmc_removed;
//                    data[1]['Member Added'] = response.gtl_added;
//                    data[1]['Member Removed'] = response.gtl_removed;
//                    data[2]['Member Added'] = response.gpa_added;
//                    data[2]['Member Removed'] = response.gpa_removed;
                    show_chart(data);
                } else {
                    swal("", "no members are present in this policy");
                }
            }
        });
    });
});
