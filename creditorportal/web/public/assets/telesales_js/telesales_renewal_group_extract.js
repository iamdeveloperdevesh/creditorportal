$(document).ready(function () {
	load_data();
	

	  $(".search_field").on("keyup change", function () {
		// alert('test');
		$("#grp_extract_table").DataTable().destroy();
		$(this).attr("disabled", false);
		load_data();
	  });


	  $("#sgroup").on("change", function () {
		// alert('test');
		$("#grp_extract_table").DataTable().destroy();
		$(this).attr("disabled", false);
		load_data();
	  });
	  
  });

  function get_current_dt() {
	var currentDate = new Date();
	var day = currentDate.getDate();
	var month = currentDate.getMonth() + 1;
	var year = currentDate.getFullYear();
	var hr = currentDate.getHours();
	var min = currentDate.getMinutes();
	var sec = currentDate.getSeconds();
	return "HBRenUpload_"+day + "" + month + "" + year + "_" + hr + "" + min + "" + sec;
  }


  function load_data(){

	var dataTable = $("#grp_extract_table").DataTable({
		// "dom": '<f<t><"#df"<"pull-left" i><"pull-right"p><"pull-right"l>>>',
		paging: true,
		lengthChange: true,
		lengthMenu: [
		  [10, 25, 100, -1],
		  [10, 25, 100, "All"],
		],
		pageLength: 10,
		processing: true,
		bServerSide: true,
		responsive: true,
		searching: false,
		columnDefs: [
		  {
			data: null,
			// "defaultContent": "default content",
			// "targets": ['_all']
		  },
		],
		dom: "lBfrtip",
	
		// buttons: [
		//   {
		// 	extend: "excelHtml5",
		// 	exportOptions: {
		// 	  columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92],
		// 	},
		// 	text: 'Export <i class="ti-export"></i>',
		// 	title:'',
		// 	filename: get_current_dt(),
		// 	init: function (dt, node, config) {
		// 	},
		//   },
		// ],

		buttons: [
            {
                extend: 'excelHtml5',
					text: 'Export <i class="ti-export"></i>',
				title:'',
				filename: get_current_dt(),
                customize: function(xlsx) {
                    //Get the built-in styles
                    //refer buttons.html5.js "xl/styles.xml" for the XML structure
                    var styles = xlsx.xl['styles.xml'];

                    //Create our own style to use the "Text" number format with id: 49
                    var style = '<xf numFmtId="49" fontId="0" fillId="0" borderId="0" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyNumberFormat="1"/>';
                    // Add new node and update counter
                    var el = $('cellXfs', styles);
                    el.append(style).attr('count', parseInt(el.attr('count'))+1);
                    // Index of our new style
                    var styleIdx = $('xf', el).length - 1;

                    //Apply new style to the first (A) column
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    //Set new style default for the column (optional)
                    //$(sheet).attr('style', styleIdx);
					for(var k=0;k<=92;k++){
						$('col:eq('+k+')', sheet).attr('style', styleIdx);
					}
					
                    //Apply new style to the existing rows of the first column ('A'), skipping header row
                    $('row c[r^="A"]', sheet).attr('s', styleIdx);
                    $('row c[r^="B"]', sheet).attr('s', styleIdx);
                    $('row c[r^="C"]', sheet).attr('s', styleIdx);
                    $('row c[r^="D"]', sheet).attr('s', styleIdx);
                    $('row c[r^="E"]', sheet).attr('s', styleIdx);
                    $('row c[r^="F"]', sheet).attr('s', styleIdx);
                    $('row c[r^="G"]', sheet).attr('s', styleIdx);
                    $('row c[r^="H"]', sheet).attr('s', styleIdx);
                    $('row c[r^="I"]', sheet).attr('s', styleIdx);
                    $('row c[r^="J"]', sheet).attr('s', styleIdx);
                    $('row c[r^="K"]', sheet).attr('s', styleIdx);
                    $('row c[r^="L"]', sheet).attr('s', styleIdx);
                    $('row c[r^="M"]', sheet).attr('s', styleIdx);
                    $('row c[r^="N"]', sheet).attr('s', styleIdx);
                    $('row c[r^="O"]', sheet).attr('s', styleIdx);
                    $('row c[r^="P"]', sheet).attr('s', styleIdx);
                    $('row c[r^="Q"]', sheet).attr('s', styleIdx);
                    $('row c[r^="R"]', sheet).attr('s', styleIdx);
                    $('row c[r^="S"]', sheet).attr('s', styleIdx);
                    $('row c[r^="T"]', sheet).attr('s', styleIdx);
                    $('row c[r^="U"]', sheet).attr('s', styleIdx);
                    $('row c[r^="V"]', sheet).attr('s', styleIdx);
                    $('row c[r^="W"]', sheet).attr('s', styleIdx);
                    $('row c[r^="X"]', sheet).attr('s', styleIdx);
                    $('row c[r^="Y"]', sheet).attr('s', styleIdx);
                    $('row c[r^="Z"]', sheet).attr('s', styleIdx);
                },
                exportOptions: {
                    format: {
                        body: function(data, row, column, node) {
                            return column === 0 ? "\0" + data : data;
                        }
                    }
                },
            },
        ],
	
		order: [],
		ajax: {
		  url: "/tele_get_grp_extract_data",
		  type: "POST",
		  data: {
			// product: $("#searchp").val(),
			// policy: $("#searchpp").val(),
			// epolicy: $("#searchep").val(),
			// occcenter: $("#occcenter").val(),
			lmdf: $("#lmdf").val(),
			lmdt: $("#lmdt").val(),
			sgroup:$("#sgroup").val()
			// current_status: $("#current_status").val(),
		  },
		  complete: function (response) {
			// console.log(response);
		  },
		},
	  });
  }

  $(function () {
	var dateFormat = "yy-mm-dd",
	  from = $("#lmdf")
		.datepicker({
		  // defaultDate: "+1w",
		  dateFormat: "yy-mm-dd",
		  changeMonth: true,
		  changeYear: true,
		})
		.on("change", function () {
		  to.datepicker("option", "minDate", getDate(this));
		}),
	  to = $("#lmdt")
		.datepicker({
		  // defaultDate: "+1w",
		  dateFormat: "yy-mm-dd",
		  changeMonth: true,
		  changeYear: true,
		})
		.on("change", function () {
		  from.datepicker("option", "maxDate", getDate(this));
		});
  
	function getDate(element) {
	  var date;
	  try {
		date = $.datepicker.parseDate(dateFormat, element.value);
	  } catch (error) {
		date = null;
	  }
  
	  return date;
	}
  });