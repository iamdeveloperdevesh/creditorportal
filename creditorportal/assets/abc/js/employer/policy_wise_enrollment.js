function show_chart(data){
        var chart = AmCharts.makeChart("chartdiv2", {
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
                "balloonText": "Total Employee:[[value]]",
                "fillAlphas": 0.8,
                "id": "AmGraph-1",
                "lineAlpha": 0.2,
                "title": "Member Added",
                "type": "column",
                "valueField": "Member Added",
                "fillColorsField": "color"

            },
            {
                "balloonText": "Enrolled:[[value]]",
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
            url: "/employer/get_total_employee_policy_wise_enrollment",
            type: "POST",
            async: false,
            data: {from_date: from_date, to_date: to_date, type: type},
            dataType: "json",
            success: function (response) {
				
                $.ajax({
                    url: "/employer/get_total_employee_policy_wise_enrollment_members",
                    type: "POST",
                    async: false,
                    data: {from_date: from_date, to_date: to_date, type: type},
                    dataType: "json",
                    success: function (response1) {
                       if (response1.gmc_data != 0){
                           var data = [{
                                        "year": "GMC",
                                        "Member Added": 0,
                                        "Member Removed": 0,
                                        "color": "#8ad7bc",
                                        "color2": "#243664"
                                    }];
                                data[0]['Member Added'] = response.gmc;
                                data[0]['Member Removed'] = response1.gmc;
                       }
                       if (response1.gpa_data != 0) {
                           var data = [{
                                        "year": "GPA",
                                        "Member Added": 0,
                                        "Member Removed": 0,
                                        "color": "#8ad7bc",
                                        "color2": "#243664"
                                    }];
                                data[0]['Member Added'] = response.gpa;
                                data[0]['Member Removed'] = response1.gpa;
                       }
                       if (response1.gtla_data != 0) {
                           var data = [{
                                        "year": "GTL",
                                        "Member Added": 0,
                                        "Member Removed": 0,
                                        "color": "#8ad7bc",
                                        "color2": "#243664"
                                    }];
                                data[0]['Member Added'] = response.gtl;
                                data[0]['Member Removed'] = response1.gtl;
                       }
                       if ((response1.gmc_data != 0) && (response1.gpa_data != 0)){
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
                                data[0]['Member Added'] = response.gmc;
                                data[0]['Member Removed'] = response1.gmc;
                                data[1]['Member Added'] = response.gpa;
                                data[1]['Member Removed'] = response1.gpa;
                       }
                       if ((response1.gmc_data != 0) && (response1.gtla_data != 0)){
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
                                data[0]['Member Added'] = response.gmc;
                                data[0]['Member Removed'] = response1.gmc;
                                data[1]['Member Added'] = response.gtl;
                                data[1]['Member Removed'] = response1.gtl;
                       }
                       if ((response1.gpa_data != 0) && (response1.gtla_data != 0)){
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
                                data[0]['Member Added'] = response.gpa;
                                data[0]['Member Removed'] = response1.gpa;
                                data[1]['Member Added'] = response.gtl;
                                data[1]['Member Removed'] = response1.gtl;
                       }
                       if ((response1.gmc_data != 0) && (response1.gpa_data != 0) && (response1.gtla_data != 0)) {
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
                                data[0]['Member Added'] = response.gmc;
                                data[0]['Member Removed'] = response1.gmc;
                                data[1]['Member Added'] = response.gpa;
                                data[1]['Member Removed'] = response1.gpa;
                                data[2]['Member Added'] = response.gtl;
                                data[2]['Member Removed'] = response1.gtl;
                       }
                       show_chart(data);
                    }
                });
            }
        });
//
    });
    });
