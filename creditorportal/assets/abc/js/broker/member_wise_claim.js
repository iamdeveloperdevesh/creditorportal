function show_chart(data) {
    var chart = AmCharts.makeChart("chartdiv", {
        "type": "serial",
        "theme": "light",

        "dataProvider": data,
        "valueAxes": [{
                "minorGridAlpha": 0.08,
                "minorGridEnabled": true,
                "position": "top",
                "axisAlpha": 0
            }],
        "startDuration": 1,
        "graphs": [{
                "balloonText": "<span style='font-size:13px;'>[[title]]  [[category]]:<b>[[value]]</b></span>",
                "title": "Enrolled",
                "type": "column",
                "fillAlphas": 1,
                "fillColorsField": "color",
                "valueField": "Enrolled",
                "columnWidth": 0.4

            }, {
                "balloonText": "<span style='font-size:13px;'>[[title]]  [[category]]:<b>[[value]]</b></span>",
                "bullet": "round",
                "bulletBorderAlpha": 1,
                "lineColor": "#ed5151",
                "bulletColor": "#FFFFFF",
                "useLineColorForBulletBorder": false,
                "fillAlphas": 0,
                "lineThickness": 2,
                "lineAlpha": 1,
                "bulletSize": 7,
                "title": "Claim",
                "valueField": "Claim"
            }],
        "rotate": false,
        "categoryField": "year",
        "categoryAxis": {
            "gridPosition": "start"
        },
        "export": {
            "enabled": false
        }

    });
}
$(document).ready(function() {
    $.ajax({
        url: "/broker/get_all_employer",
        type: "POST",
        dataType: "json",
        success: function(response) {
            $('#employer_name').empty();
            $('#employer_name').append('<option value=""> Select Employer name</option>');
            for (i = 0; i < response.length; i++) {
                $('#employer_name').append('<option value="' + response[i].company_id + '">' + response[i].comapny_name + '</option>');
            }
        }
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
//                var selectedDate = new Date(date);
//                var msecsInADay = 86400000;
//                var endDate = new Date(selectedDate.getTime() + msecsInADay);
//               //Set Minimum Date of EndDatePicker After Selected Date of StartDatePicker
//                $("#to_date").datepicker( "option", "minDate", endDate );

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

    // get policy no change on employer name
    $("#employer_name").change(function() {
        var company_id = $(this).val();
        $.ajax({
            url: "/broker/get_policy_type",
            type: "POST",
            data: {company_id: company_id},
            dataType: "json",
            success: function(response) {
                $('#policy_no').empty();
                $('#policy_no').append('<option value=""> Select Policy</option>');
                for (i = 0; i < response.length; i++) {
                    $('#policy_no').append('<option value="' + response[i].policy_no + '">' + (response[i].policy_sub_type_name + response[i].desgn_name) + '</option>');
                }
            }
        });
    });
    var data = [{
            "year": "Self",
            "Enrolled": 80,
            "Claim": 10,
            "color": "#31a2ab"
        }, {
            "year": "Spouse",
            "Enrolled": 90,
            "Claim": 70,
            "color": "#31a2ab"
        }, {
            "year": "Daughter",
            "Enrolled": 30,
            "Claim": 23,
            "color": "#31a2ab"
        }, {
            "year": "Son",
            "Enrolled": 50,
            "Claim": 25,
            "color": "#31a2ab"
        },
        {
            "year": "father",
            "Enrolled": 80,
            "Claim": 35,
            "color": "#31a2ab"
        }, {
            "year": "Mother",
            "Enrolled": 72,
            "Claim": 55,
            "color": "#31a2ab"
        }];
    var self = 0;
    var son = 0;
    var daughter = 0;
    var spouse = 0;
    var mother = 0;
    var father = 0;

    $('#apply').on('click', function() {
        var employer_name = $('#employer_name option:selected').val();
        var policy_no = $('#policy_no option:selected').val();
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        if (employer_name == "") {
            swal("", "please select employer name");
            return false;
        }
        if (policy_no == "") {
            swal("", "please select policy no");
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
            url: "/broker/get_member_enrolled_from_policy_no",
            type: "POST",
            async: false,
            data: {from_date: from_date, to_date: to_date, policy_no: policy_no, employer_name: employer_name},
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
                    for (i = 0; i < response.length; i++) {
                        if (response[i]["relationship"] == "Self") {
                            self++;
                        }
                        if (response[i]["relationship"] == "Son") {
                            son++;
                        }
                        if (response[i]["relationship"] == "Daughter") {
                            daughter++;
                        }
                        if (response[i]["relationship"] == "Mother") {
                            mother++;
                        }
                        if (response[i]["relationship"] == "Father") {
                            father++;
                        }
                        if (response[i]["relationship"] == "Spouse/Partner") {
                            spouse++;
                        }
                        data[0]["Enrolled"] = self;
                        data[1]["Enrolled"] = spouse;
                        data[2]["Enrolled"] = daughter;
                        data[3]["Enrolled"] = son;
                        data[4]["Enrolled"] = father;
                        data[5]["Enrolled"] = mother;
                        
                    }
					show_chart(data);
                } else
                {
                    swal("", "no members are present in this policy");
                }
            }
        });
        $.ajax({
            url: "/broker/dashboard/get_member_claims_from_policy_no",
            type: "POST",
            async: false,
            data: {policy_no: policy_no, to_date: to_date, from_date: from_date, employer_name: employer_name},
            dataType: "json",
            success: function(response1) {
                if (response1) {
                    data[0]["Claim"] = response1.self;
                    data[1]["Claim"] = response1.spouse;
                    data[2]["Claim"] = response1.daughter;
                    data[3]["Claim"] = response1.son;
                    data[4]["Claim"] = response1.father;
                    data[5]["Claim"] = response1.mother;
                    show_chart(data);
                } else {
                    swal("", "no members are present in this policy");
                }
            }
        });
    });
});

