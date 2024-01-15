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

    Highcharts.chart('socialads1', {
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
            url: "/broker/get_member_active_endorsement_from_policy_no",
            type: "POST",
            async: false,
            data: {employer_name: employer_name, policy_no: policy_no, to_date: to_date, from_date: from_date},
            dataType: "json",
            success: function(response) {
//                    debugger;
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
            url: "/broker/dashboard/get_member_inactive_endorsement_from_policy_no",
            type: "POST",
            async: false,
            data: {employer_name: employer_name, policy_no: policy_no, to_date: to_date, from_date: from_date},
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
})

