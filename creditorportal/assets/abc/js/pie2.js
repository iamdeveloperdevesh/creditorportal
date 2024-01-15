var myConfig = {
  "type":"pie3d",
  "title":{
    "text":"Reimbursement Claims"
  },
    "legend":{},
    "plot":{
      "legend-item":{
          "color":"red",
          "font-size":14
        }
    },
  "series":[
    
    {"values":[40],
     "text":"Claim Settle",
    "background-color":"#046d66"},
    {"values":[35],
      "text":"Discrepancy",
    "background-color":"#e5792f"},
    {"values":[25], 
     "text":"Rejected",
    "background-color":"#9f9f9f"},
  ]
};
 
zingchart.render({ 
	id : 'pie1', 
	data : myConfig, 
	height: 400, 
	width: "100%"
});
zingchart.render({ 
	id : 'pie1', 
	data : myConfig, 
	height: 400, 
	width: "100%" 
});
var myConfig = {
  "type":"pie3d",
  "title":{
    "text":"Cashless Claims"
  },
    "legend":{},
    "plot":{
      "legend-item":{
          "color":"red",
          "font-size":14
        }
    },
  "series":[
    
    {"values":[40],
      "text":"Claim Settle",
    "background-color":"#046d66",},
    {"values":[35],
       "text":"Discrepancy",
    "background-color":"#e5792f"},
    {"values":[25],
      "text":"Rejected",
    "background-color":"#9f9f9f"},
  ]
};
 
zingchart.render({ 
	id : 'pie2', 
	data : myConfig, 
	height: 400, 
	width: "100%" 
});
zingchart.render({ 
	id : 'pie2', 
	data : myConfig, 
	height: 400, 
	width: "100%" 
});