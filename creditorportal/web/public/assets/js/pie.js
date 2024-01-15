var myConfig = {
  "type":"ring3d",
  "title":{
    "text":"Health Cover"
  },
    "legend":{},
    "plot":{
      "legend-item":{
          "color":"red",
          "font-size":14
        }
    },
  "series":[
    {"values":[24],
     "text": "No Enrolled",
    "background-color":"#9f9f9f"},
    {"values":[76],
      "text": "Enrolled",
    "background-color":"#e5792f"}
  ]
};
 
zingchart.render({ 
	id : 'myChart', 
	data : myConfig, 
	height: 400, 
	width: "100%" 
});

var myConfig = {
  "type":"ring3d",
  "title":{
    "text":"Claim %"
  },
    "legend":{},
    "plot":{
      "legend-item":{
          "color":"red",
          "font-size":14
        }
    },
  "series":[
    {"values":[14],
     "text": "Total Cover",
     "background-color":"#9f9f9f"},
    {"values":[86],
      "text": "Claim",
     "background-color":"#e5792f"}
  ]
};
 
zingchart.render({ 
	id : 'myChart2', 
	data : myConfig, 
	height: 400, 
	width: "100%" 
});
var myConfig = {
  "type":"ring3d",
  "title":{
    "text":"Life Cover"
  },
      "legend":{},
    "plot":{
      "legend-item":{
          "color":"red",
          "font-size":14
        }
    },
  "series":[
    {"values":[24],
      "text": "No Enrolled",
     "background-color":"#9f9f9f"},
    {"values":[76],
      "text": "Enrolled",
     "background-color":"#e5792f"}
  ]
};
 
zingchart.render({ 
	id : 'myChart3', 
	data : myConfig, 
	height: 400, 
	width: "100%" 
});
var myConfig = {
  "type":"ring3d",
  "title":{
    "text":"Claim %"
  },
      "legend":{},
    "plot":{
      "legend-item":{
          "color":"red",
          "font-size":14
        }
    },
  "series":[
    {"values":[17],
      "text": "Total Employee",
     "background-color":"#9f9f9f"},
    {"values":[83],
      "text": "Death Claim",
     "background-color":"#e5792f"}
  ]
};
 
zingchart.render({ 
	id : 'myChart4', 
	data : myConfig, 
	height: 400, 
	width: "100%" 
});