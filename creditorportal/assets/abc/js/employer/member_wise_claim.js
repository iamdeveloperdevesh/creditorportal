function show_chart(data){
        var chart = AmCharts.makeChart("ambarchart6", {
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
      
 $(document).ready(function () {
	 
     var data =  [{
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
            "Enrolled":50,
            "Claim": 25,
             "color": "#31a2ab"
        },
						 {
            "year": "father",
            "Enrolled":80,
            "Claim": 35,
            "color": "#31a2ab"
        },{
            "year": "Mother",
            "Enrolled": 72,
            "Claim": 55,
             "color": "#31a2ab"
        }];
     $("#from_date").datepicker({
        dateFormat: "dd-mm-yy",
        prevText: '<i class="fa fa-angle-left"></i>',
        nextText: '<i class="fa fa-angle-right"></i>',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100Y:",
        maxDate: new Date(),
		 minDate: "-100Y +1D",
		 onSelect: function(dateText, inst){
     $("#to_date").datepicker("option","minDate",
     $("#from_date").datepicker("getDate"));
  }
       
		
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
                async: false,
                data : { employer : "true"},
                dataType: "json",
                success: function (response) {
                     $('#policy_no').empty();
                     $('#policy_no').append('<option value=""> Select policy type</option>');
                    for (i = 0; i < response.length; i++) { 
                        var date = response[i].end_date.split("-");
                        var date = new Date(Number(date[0]), Number(date[1])-1, Number(date[2]));
                        var current_date = new Date();
//                        if(date > current_date){
                                $('#policy_no').append('<option value="' + response[i].policy_no + '">' + (response[i].policy_sub_type_name + response[i].desgn_name) + '</option>');
//                            } 
                        }
                }
            }); 
    var self = 0;
    var son = 0;
    var daughter = 0;
    var spouse = 0;
    var mother = 0;
    var father = 0;
     $('#apply').on('click', function() {
             var from_date = $("#from_date").val();
             var policy_no = $("#policy_no").val();
             var to_date = $("#to_date").val();
             if(policy_no == ""){
                 swal("","please select policy no");
                 return false;
             }
             if(from_date == ""){
                 swal("","please select from date");
                 return false;
             }
              if(to_date == ""){
                 swal("","please select to date");
                 return false;
             }
        $.ajax({
                url: "/employer/get_member_enrolled_from_policy_no",
                type: "POST",
                async: false,
                data:{policy_no : $('#policy_no option:selected').val() ,employer:true, to_date:to_date, from_date:from_date},
                dataType: "json",
                success: function (response) {
                      var self = 0;
    var son = 0;
    var daughter = 0;
    var spouse = 0;
    var mother = 0;
    var father = 0;
                  if(response.length != 0) {
                    for (i = 0; i < response.length; i++) { 
                      
                    if(response[i]["relationship"] == "Self"){
                        self++;
                    }
                     if(response[i]["relationship"] == "Son"){
                        son++;
                    }
                     if(response[i]["relationship"] == "Daughter"){
                        daughter++;
                    }
                     if(response[i]["relationship"] == "Mother"){
                        mother++;
                    }
                     if(response[i]["relationship"] == "Father"){
                        father++;
                    }
                     if(response[i]["relationship"] == "Spouse"){
                        spouse++;
                    }
                }
                    data[0]["Enrolled"] = self;
                    data[1]["Enrolled"] = spouse;
                    data[2]["Enrolled"] = daughter;
                    data[3]["Enrolled"] = son;
                    data[4]["Enrolled"] = father;
                    data[5]["Enrolled"] = mother;
                     show_chart(data);
            }
            else{
               // swal("","no members are present in this policy");
            }
        }
            });
            $.ajax({
                url: "/employer/dashboard/get_member_claims_from_policy_no",
                type: "POST",
                async: false,
                data:{policy_no : $('#policy_no option:selected').val(),to_date:to_date, from_date:from_date},
                dataType: "json",
                success: function (response) {
                  if(response) {
                    data[0]["Claim"] = response.self;
                    data[1]["Claim"] = response.spouse;
                    data[2]["Claim"] = response.daughter;
                    data[3]["Claim"] = response.son;
                    data[4]["Claim"] = response.father;
                    data[5]["Claim"] = response.mother;
                     show_chart(data);
            }
            else{
                swal("","no members are present in this policy");
            }
        }
            });
            });
 });
