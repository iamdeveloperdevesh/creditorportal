$(document).ready(function () {
	load_data();
	

	  $(".search_field").on("keyup change", function () {
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
	return day + "-" + month + "-" + year + " " + hr + "-" + min + "-" + sec;
  }


  function load_data(){
	var dataTable = $("#grp_extract_table").DataTable({
		// "dom": '<f<t><"#df"<"pull-left" i><"pull-right"p><"pull-right"l>>>',
		paging: true,
		lengthChange: true,
		ordering: false,
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
	
		buttons: [
		  {
			extend: "excelHtml5",
			
			extension: 'csv',
			exportOptions: {
			  columns: [0,1,2,3,4,5,6,7,8,9,10,11,12],
			},
			text: 'Export <i class="ti-export"></i>',
			title: get_current_dt(),
			init: function (dt, node, config) {
			},
		  },
		],
	
		order: [],
		ajax: {
		  url: "/ack_coi_fetch_data",
		  type: "POST",
		  data: {
			// product: $("#searchp").val(),
			// policy: $("#searchpp").val(),
			// epolicy: $("#searchep").val(),
			// occcenter: $("#occcenter").val(),
			lmdf: $("#lmdf").val(),
			lmdt: $("#lmdt").val(),
			product_id: $("#product_id").val(),
			lead_coi: $("#lead_coi").val(),
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