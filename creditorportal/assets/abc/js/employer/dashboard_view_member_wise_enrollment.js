

function show_chart(data) {
    var chart = AmCharts.makeChart("ambarchart4", {
        "type": "serial",
        "theme": "light",
        "marginRight": 70,
        "dataProvider": data,
        "valueAxes": [{
                "axisAlpha": 0,
                "position": "left",
                "title": false
            }],
        "startDuration": 1,
        "graphs": [{
                "balloonText": "<b>[[category]]: [[value]]</b>",
                "fillColorsField": "color",
                "fillAlphas": 0.9,
                "lineAlpha": 0.2,
                "type": "column",
                "valueField": "visits",
                "columnWidth": 0.4
            }],
        "chartCursor": {
            "categoryBalloonEnabled": false,
            "cursorAlpha": 0,
            "zoomable": false
        },
        "categoryField": "country",
        "categoryAxis": {
            "gridPosition": "start"
        },
        "export": {
            "enabled": false
        }

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
        onSelect: function(date) {

            var selectedDate = new Date(date);
            var msecsInADay = 86400000;
            var endDate = new Date(selectedDate.getTime() + msecsInADay);

            //Set Minimum Date of EndDatePicker After Selected Date of StartDatePicker
            $("#to_date").datepicker("option", "minDate", endDate);
            // $("#endDatePicker").datepicker( "option", "maxDate", '+2y' );

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
    var data = [{
            "country": "Self",
            "visits": 0,
            "color": "#ef5354"
        }, {
            "country": "Spouse",
            "visits": 0,
            "color": "#f9d643"
        }, {
            "country": "Daughter",
            "visits": 0,
            "color": "#375d96"
        }, {
            "country": "Son",
            "visits": 0,
            "color": "#f0a07c"
        }, {
            "country": "Father",
            "visits": 0,
            "color": "#3f671b"
        }, {
            "country": "Mother",
            "visits": 0,
            "color": "#df678c"
        },{
            "country": "Mother-in-law",
            "visits": 0,
            "color": "#df678c"
        },{
            "country": "Father-in-law",
            "visits": 0,
            "color": "#df678c"
        }];
    //show_chart(data);
    var self = 0;
    var son = 0;
    var daughter = 0;
    var spouse = 0;
    var mother = 0;
    var father = 0;
	var mother_in_law = 0;
	var father_in_law = 0;
	
    $('#apply').on('click', function() {
        var policy_no = $('#policy_no option:selected').val();
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
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
            url: "/employer/get_member_enrolled_from_policy_no",
            type: "POST",
            async: false,
            data: {policy_no: $('#policy_no option:selected').val(), employer: true, to_date: to_date, from_date: from_date},
            dataType: "json",
            success: function(response) {
                if (response.length != 0) {
					//debugger;
                    self = 0;
                    son = 0;
                    daughter = 0;
                    mother = 0;
                    father = 0;
                    spouse = 0;
					mother_in_law = 0;
					father_in_law = 0;
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
						if (response[i]["relationship"] == "Mother-in-law") {
                            mother_in_law++;
                        }
                        if (response[i]["relationship"] == "Father-in-law") {
                            father_in_law++;
                        }
						
                        data[0]["visits"] = self;
                        data[1]["visits"] = spouse;
                        data[2]["visits"] = daughter;
                        data[3]["visits"] = son;
                        data[4]["visits"] = father;
                        data[5]["visits"] = mother;
						data[6]["visits"] = mother_in_law;
						data[7]["visits"] = father_in_law;
                        
                    }
                    show_chart(data);
                } else {
                    swal("", "no members are present in this policy");
                }
            }
        });
    });
});