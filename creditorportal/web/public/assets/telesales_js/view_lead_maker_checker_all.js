// 03-02-2022 - SVK005
var global_show = "show";
var global_alert = "hide";
load_data();
$(document).ready(function (){
	if ($("#is_admin_check").val() == '1') {

		$("#lead_table_div").show();
	}
	else{
		$("#lead_table_div").hide();
	}

	$('body').on("keyup", "#first_name", function () {
		if ($(this).val().match(/[^a-zA-Z ]/g)) {
			$(this).val($(this).val().replace(/[^a-zA-Z ]/g, ""));
		}
		$(this).val($(this).val().toUpperCase());
	});

	$('body').on("keyup", "#last_name", function () {
		if ($(this).val().match(/[^a-zA-Z. ]/g)) {
			$(this).val($(this).val().replace(/[^a-zA-Z. ]/g, ""));
		}
		$(this).val($(this).val().toUpperCase());
	});
});
var column_sort = {};
function reassign_agent(emp_id, reference,agent_id) {
	$("#agent_reassign").html("");

	$.ajax({
		url: "/tele_get_all_agents",
		type: "POST",
		// async: false,
		// dataType: "json",
		beforeSend: function() {
			$("#agent_reassign").html('<option value="">Loading...</option>');
			$("#agent_reassign").attr('disabled', true);
		},
		success: function (response) {
			$("#agent_reassign").attr('disabled', false);
			$("#agent_reassign").html('<option value="">Select Agent</option>');
			$("#agent_reassign").append(response);
		}
	});
	$("#agent_reassign option[value="+agent_id+"]").hide();
	global_agent_id = agent_id;
	$("#agentModal").modal('show');
	$('#agentModal').data('element', reference);
	$('#agentModal').data('empid', emp_id);

}

function payment_call(lead_id) {
	$("#txno").val('');
	$("#txndate").val('');
	$("#txn-error").hide();
	$("#txndate-error").hide();
	$("#paymentModal").modal('show');
	$('#paymentModal').data('lead_id', lead_id);

}
function get_audit_trail(emp_id){
	emp_id = emp_id || '';
	if (emp_id) {
		$.ajax({
			url: "/get_audit_trail",
			type: "POST",
			async: false,
			data: { emp_id: emp_id },
			dataType: "json",
			success: function (response) {

				$("#payment_details_tbody_audit").html('');

				$.each(response, function (key,val) {
					debugger;
					$("#payment_details_tbody_audit").append('<tr>');
					$("#payment_details_tbody_audit").append('<td>'+val["Dispositions"]+'</td>');
					$("#payment_details_tbody_audit").append('<td>'+val["Sub-dispositions"]+'</td>');
					$("#payment_details_tbody_audit").append('<td>'+val["date"]+'</td>');
					$("#payment_details_tbody_audit").append('<td>'+val["agent_name"]+'</td>');
					$("#payment_details_tbody_audit").append('<td>'+((val["remarks"]) ? val["remarks"] : '')+'</td>');
					//upendra - maker/checker - 30-07-2021
					$("#payment_details_tbody_audit").append('<td>'+((val["type"]) ? val["type"] : 'AV')+'</td>');
					$("#payment_details_tbody_audit").append('</tr>');
				});

				$("#auditmodal").modal('show');
			}
		});
	}
}
function retrigger_pg_link(emp_id){
	emp_id = emp_id || '';
	if (emp_id) {
		$.ajax({
			url: "/tls_payment_url_send",
			type: "POST",
			async: false,
			data: { emp_id: emp_id },
			dataType: "json",
			success: function (response) {

			}
		});
	}
	swal({
			title: 'Success',
			text: 'Link Sent Successfully',
			type: 'success',
			showCancelButton: false,
			confirmButtonText: "Ok!",
			closeOnConfirm: true
		},
		function () {

			location.reload();





		});

}
// function create_proposal(emp_id) {
// 	emp_id = emp_id || '';
// 	if (emp_id) {
// 		$.ajax({
// 			url: "/tls_redirect_proposal",
// 			type: "POST",
// 			async: false,
// 			data: { emp_id: emp_id },
// 			dataType: "json",
// 			success: function (response) {
// 				if (response.status) {
// 					location.replace('/tele_create_proposal?leadid='+response.lead_id);
// 				}
// 			}
// 		});
// 	}
// }
// tele_update_ref_no

function create_proposal(emp_id,edit_status=1) {
	emp_id = emp_id || '';
	if (emp_id) {
		$.ajax({
			url: "/tele_update_ref_no_vl",
			type: "POST",
			async: false,
			data: { emp_id: emp_id},
			success: function (response) {

				// alert(response);
				$.ajax({
					url: "/tls_redirect_proposal",
					type: "POST",
					async: false,
					data: { emp_id: emp_id },
					dataType: "json",
					success: function (response) {
						if (response.status) {
							location.replace('/tele_create_proposal?leadid='+response.lead_id+'&editstatus='+btoa(edit_status));
						}
					}
				});

			}
		});
	}
}



// 03-02-2022 - SVK005
async function load_data(data_search) {
	$.fn.dataTable.ext.errMode = 'none';
	data_search = data_search || {};
	var oldExportAction = function (self, e, dt, button, config) {
		if (button[0].className.indexOf('buttons-excel') >= 0) {
			if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
				$.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
			} else {
				$.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
			}
		} else if (button[0].className.indexOf('buttons-print') >= 0) {
			$.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
		}
	};

	var newExportAction = function (e, dt, button, config) {
		var self = this;
		var oldStart = dt.settings()[0]._iDisplayStart;

		dt.one('preXhr', function (e, s, data) {

			data.start = 0;
			data.length = 2147483647;

			dt.one('preDraw', function (e, settings) {

				oldExportAction(self, e, dt, button, config);

				dt.one('preXhr', function (e, s, data) {

					settings._iDisplayStart = oldStart;
					data.start = oldStart;
				});


				setTimeout(dt.ajax.reload, 0);


				return false;
			});
		});


		dt.ajax.reload();
	};
	debugger;
	var dataTable = $('#leadMasterTable').DataTable({
		dom: 'Bfrtip',
		responsive: true,
		searching: false,
		buttons: [{
			extend: 'excel',

			text: 'Export<i class="fa fa-file-excel-o"></i>',
			titleAttr: 'Excel',
			action: newExportAction,



			/*exportOptions: {
                columns :  ['.admin'],
                config.exportOptions = { orthogonal: 'sort', columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 16,17, 18, 19, 20, 21, 22,23, 24, 25, 26,27,28,29,30,31] };
                columns: 'th:not(:last-child)'
            },
            */


			init: function (dt, node, config) {
				debugger;
				if ($("#is_admin_check").val() == '1') {
					if ($("#is_region_check").val() != '') {

						config.exportOptions = { orthogonal: 'sort', columns: ['.export_region'] };
					}
					else{
						config.exportOptions = { orthogonal: 'sort', columns: ['.export_admin'] };
					}


					config.columnDefs = [{
						"targets": [0, 34,1,2],
						"orderable": false,



					},
					]
				} else {
					config.exportOptions = { orthogonal: 'sort', columns: ['.export_agent'] };
					config.columnDefs = [{
						"targets": [0, 10],
						"orderable": false,


					},
					]
				}

			},
			// 	customizeData: function (data) {
			// 		/*for (var i = 0; i < data.body.length; i++) {
			// 			for (var j = 0; j < data.body[i].length; j++) {
			// 				data.body[i][j] = '\u200C' + data.body[i][j];
			// 			}
			// 		}*/
			// 	}
		}],
		"processing": true,
		"serverSide": true,
		"createdRow": function( row, data, dataIndex ) {


			$(row).children(':nth-child(3)').addClass('wordallign');
			$(row).children(':nth-child(13)').addClass('wordallign');


		},
		"order": [],
		"ajax": {
			url: 'tls_lead_tbl_ajax',
			type: "POST",
			data: function (d) {
				d.mobno = $('[name=mobno]').val();
				d.sakshamid = $('[name=sakshamid]').val();
				d.moddate = $('[name=moddate]').val();
				d.issuancedate = $('[name=issuancedate]').val();
				d.status = $('[name=status]').val();
				d.location = $('[name=location]').val();
				d.policyNumber = $('[name=policyNumber]').val();

				//maker/checker post
				d.is_maker_checker = "yes";

				//upendra - maker-checker - 30-07-2021
				d.axis_process_filter = $('[name=axis_process_filter]').val();
			},
			complete: function (response) {
				// 03-02-2022 - SVK005
				if ( ! dataTable.data().any() && global_alert == "show") {
					$("#lead_table_div").hide();
					swal( 'No Record Found' );
					global_show = "hide";
					return;
				}
				global_show = "show";
				// $("#lead_table_div").show();

				if ($("#is_admin_check").val() == '1') {
					dataTable.columns('.admin').visible(false);
				}
				if(response.responseJSON !== undefined){
					if (response.responseJSON.data.length != 0) {
						$('#leadgrid').show();
					}
				}
			}

		},

	});
	if ($("#is_admin_check").val() == '1') {
		dataTable.columns('.admin').visible(false);
	}

	dataTable.columns('.lead_column').visible(false);
}

function searchTable() {
	search("leadMasterTable");
}

function clearFilter() {
	$('#fullName').val('');
	$('#ddlCountry').val(0);
	$('#ddlState').val(0);
	search("leadMasterTable");
}
function manual_search(reference) {
	$('#leadMasterTable').DataTable().destroy();
	reference = reference || '';
	var parameters = {};
	if (reference) {
		if (column_sort && column_sort['sortby'] == $(reference).text()) {
			column_sort['order'] = (column_sort['order'] == 'desc') ? 'asc' : 'desc';
		} else {
			column_sort['sortby'] = $(reference).text();
			column_sort['order'] = 'asc';
			column_sort['number'] = $(reference).data('id');
		}
		parameters['sortby'] = column_sort['sortby'];
		parameters['order'] = column_sort['order'];
		parameters['number'] = column_sort['number'];

	}
	$('.search').each(function (i, obj) {
		if ($(obj).val()) {
			var key = $(obj).attr('name');
			var value = $(obj).val();
			parameters[key] = value;
		}

	});
	var status = $('#status :selected').val();
	if (status) {
		parameters['status'] = $('#status :selected').val();
	}

	load_data(parameters);

}
$(document).ready(function () {
	/*$('input[name="moddate"]').daterangepicker({
		locale: {
			format: 'DD/MM/YYYY'
		},
		"dateLimit": {
			"month": 1
		},
	});
	$('input[name="moddate"]').val('');
	//ankita added new filed in filter
	$('input[name="issuancedate"]').daterangepicker({
		locale: {
			format: 'DD/MM/YYYY'
		},
		"dateLimit": {
			"month": 1
		},
	});*/
	$('body').on("keyup", "input[name=issuancedate]", function () {
		if ($(this).val().match(/[^0-9- :]/g)) {
			$(this).val($(this).val().replace(/[^0-9- :]/g, ""));
		}
	});

	$('input[name="issuancedate"]').val('');
	$.validator.addMethod('valid_mobile', function (value, element, param) {
		var re = new RegExp('^[6-9][0-9]{9}$');
		return this.optional(element) || re.test(value);
	}, 'Enter valid 10 digit no starting from 6 to 9');
	$.validator.addMethod('valid_mobile_last_ten_digits', function (value, element, param) {
		debugger;

		var str1 = value;
		var n1 = 10;
		var newvalue = str1.substring(str1.length - n1);
		$(element).val(newvalue);
		var re = new RegExp('^[6-9][0-9]{9}$');
		return this.optional(element) || re.test(newvalue);

	}, 'Enter valid 10 digit no starting from 6 to 9');
	$.validator.addMethod('validateEmail', function (value, element, param) {
		var reg = /^([\w-]+(?:\.[\w-]+)*)@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		//var reg = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/;

		return reg.test(value);
	}, 'Please enter a valid Email Address.');

	$('body').on("keyup", "input[name=first_name]", function () {
		if ($(this).val().match(/[^a-zA-Z ]/g)) {
			$(this).val($(this).val().replace(/[^a-zA-Z ]/g, ""));
		}
	});
	$('body').on("keyup", "input[name=last_name]", function () {
		if ($(this).val().match(/[^a-zA-Z. ]/g)) {
			$(this).val($(this).val().replace(/[^a-zA-Z. ]/g, ""));
		}
	});
	$('body').on("keyup", "#mob_no, #saksham_id, #lead_id,input[name=sakshamid],input[name=mobno]", function () {
		if ($(this).val().match(/[^0-9]/g)) {
			$(this).val($(this).val().replace(/[^0-9]/g, ""));
		}
	});
	$('body').on("keyup", "input[name=moddate]", function () {
		if ($(this).val().match(/[^0-9- :]/g)) {
			$(this).val($(this).val().replace(/[^0-9- :]/g, ""));
		}
	});
	$('body').on("change", "#salutation", function () {
		if ($(this).val()) {
			var gender = ($(this).val() == 'Mr'  || $(this).val() == 'Master') ? 'Male' : 'Female';
			$("#gender1").val(gender);
		}
	});
	$('body').on("change", "#gender1", function () {
		if ($(this).val() && $(this).val() != 'Transgender') {
			var salutation = ($(this).val() == 'Male') ? 'Mr' : 'Mrs';
			$("#salutation").val(salutation);
		}
	});
	if ($("#is_admin_check").val() == '1') {
		$('body').on("keyup change", ".search", function () {


			if($(this).attr('name') == 'mobno' || $(this).attr('name') == 'sakshamid'){

				if($(this).val().length < 10 && $(this).val().length > 0){
					return false;
				}

			}

			global_alert = "show";


			if($(this).val()){
				$(".search").attr("disabled", true);
				if($(this).attr('name') != 'moddate'){
					$('#status').attr("disabled", true);
					$('#location').attr("disabled", true);

				}
				$('#leadMasterTable').DataTable().destroy();
				$(this).attr("disabled", false);
				load_data();
			}else{

				$(this).attr("disabled", false);
				$(".search").attr("disabled", false);
				$('#status').attr("disabled", false);
				$('#location').attr("disabled", false);
			}
		});


	}else{
		$('body').on("change", ".search", function () {

			if($(this).val()){
				$(".search").attr("disabled", true);
				if($(this).attr('name') != 'moddate'){
					$('#status').attr("disabled", true);
					$('#location').attr("disabled", true);

				}

				$('#leadMasterTable').DataTable().destroy();
				$("#lead_table_div").hide();
				$(this).attr("disabled", false);

			}else{
				$('#leadMasterTable').DataTable().destroy();
				$("#lead_table_div").hide();
				$(this).attr("disabled", false);
				$(".search").attr("disabled", false);
				$('#status').attr("disabled", false);
				$('#location').attr("disabled", false);
			}
		});
	}
	//upendra - maker-checker - 30-07-2021 - add #axis_process_filter
	$('body').on("change", "#status,#location,#axis_process_filter", function () {
		$(".search").attr("disabled", true);
		$("[name='moddate']").attr("disabled", false);
		if($("#status").val() == '' && $("#location").val() == ''){
			$(".search").attr("disabled", false);
		}
		//	$('#leadMasterTable').DataTable().destroy();
		//load_data();
	});
	$('body').on("change", "#agent_reassign", function () {
		if ($(this).val()) {
			$("#agent-error").hide();
		}
	});
	$('body').on("click", "#clear_filter", function () {
		location.reload();
	});

	// 03-02-2022 - SVK005
	$('body').on("click", "#search_form", function () {
		//debugger;
		var srchLeadId = $('.srchLeadId').val();
		var srchmobno = $('.srchmobno').val();
		var srchmoddate = $('.srchmoddate').val();
		var srchissuancedate = $('.srchissuancedate').val();
		var srchPolicyNumber = $('.srchPolicyNumber').val();
		var srchstatus = $('.srchstatus').val();
		if(srchmobno == '' && srchLeadId == '' && srchmoddate == '' && srchissuancedate == '' && srchstatus == '' && srchPolicyNumber==''){
			swal("Alert","Please enter Valid Data.","warning");
			$(".search").attr("disabled", false);
			$("#lead_table_div").hide();return false;
		}

		if(srchmobno != '' || srchLeadId != ''){
			if(srchmobno!='')
			{	$(".search").attr("disabled", true);
				$('.srchmobno').attr("disabled", false);
			}
			else if(srchLeadId !='')
			{
				$(".search").attr("disabled", true);
				$('.srchLeadId').attr("disabled", false);
			}
			else
			{
				$(".search").attr("disabled", false);
			}

			if(/^\d{10}$/.test(srchmobno) && srchmobno != ''){
				global_alert = "show";

				$('#leadMasterTable').DataTable().destroy();
				load_data();
				setTimeout(function () {
					if(global_show == "show"){
						$("#lead_table_div").show();
					}
				}, 2500);
			}

			else if(/^!*(\d!*){10,}$/.test(srchLeadId) && srchLeadId != ''){
				global_alert = "show";

				$('#leadMasterTable').DataTable().destroy();
				load_data();
				setTimeout(function () {
					if(global_show == "show"){
						$("#lead_table_div").show();
					}
				}, 2500);
			}else{
				swal("Alert","Enter valid digits.","warning"); $("#lead_table_div").hide();return false;
			}
		}else{
			global_alert = "show";

			$('#leadMasterTable').DataTable().destroy();
			load_data();
			setTimeout(function () {
				if(global_show == "show"){
					$("#lead_table_div").show();
				}
			}, 2500);
		}


	});

	$("#dob").datepicker({
		dateFormat: "dd-mm-yy",
		prevText: '<i class="fa fa-angle-left"></i>',
		nextText: '<i class="fa fa-angle-right"></i>',
		changeMonth: true,
		changeYear: true,
		yearRange: "-100Y:-18Y",
		maxDate: "-18Y",
		minDate: "-56Y +1D"

	});
	$("#txndate").datepicker({
		dateFormat: "dd-mm-yy",
		prevText: '<i class="fa fa-angle-left"></i>',
		nextText: '<i class="fa fa-angle-right"></i>',
		changeMonth: true,
		changeYear: true,
		yearRange: "-100Y:-0Y",


	});

	if($('#is_region_check').val() != ''){
		$('#location').val($('#is_region_check').val());
		$('#location').attr('disabled', 'disabled');

	}
	load_data();


	// $(document).on('change','#axis_process',function(){
	// 	var getv=$(this).val();
	// 	if(getv=='Outbond Call Center (OCC)'){

	// 		$('#saksham_id').attr('required',false);
	// 		$('#saksham_id').attr('disabled',true);
	// 		$('#saksham_id').css({'pointer-events':'none'});
	// 	}else{
	// 		$('#saksham_id').attr('disabled',false);
	// 		$('#saksham_id').attr('required',true);

	// 		$('#saksham_id').css({'pointer-events':'auto'});

	// 	}
	// });



	$("#lead-form").validate({
		ignore: ".ignore",
		rules: {
			lead_id: {
				required: true
			},
			axis_process: {
				required: true
			},
			salutation: {
				required: true
			},
			first_name: {
				required: true
			},

			gender1: {
				required: true
			},
			mob_no: {
				required: true,
				valid_mobile: true,
				/*valid_mobile_last_ten_digits : true*/

			},
			dob: {
				required: true
			},
			email: {
				required: true,
				validateEmail: true
			}
		}
	});
});
$(document).on('click', '#submit_lead', function (event) {
	event.preventDefault();
	$('#gender1').prop("disabled", false);
	if (!$("#lead-form").valid()) {
		$('#gender1').prop("disabled", true);
		return false;
	}

	$.ajax({
		url: "/tls_insert_lead",
		type: "POST",
		async: false,
		data: $("#lead-form").serialize(),
		dataType: "json",
		success: function (response) {
			if (response) {
				var title = (response.status) ? 'Success' : 'Warning';
				var type = (response.status) ? 'success' : 'warning';
				swal({
						title: title,
						text: response.message,
						type: type,
						showCancelButton: false,
						confirmButtonText: "Ok!",
						closeOnConfirm: true
					},
					function () {
						if (response.status) {
							location.replace('/tele_create_proposal?leadid='+response.lead_id);
						} else {
							if(response.message == 'Saksham id already exists'){
								return;
							}else{
								location.reload();
							}


						}

					});

			}
		}
	});
});
$(document).on('click', '#agentreassign-btn', function (event) {
	event.preventDefault();
	$("#agent-error").hide();
	if (!$("#agent_reassign").val()) {
		$("#agent-error").show();
		return false;
	}


	$.ajax({
		url: "/tls_reassign_agent",
		type: "POST",
		async: false,
		data: { "emp_id": $('#agentModal').data('empid'), "agent_id": $('#agent_reassign option:selected').val() },
		dataType: "json",
		success: function (response) {
			if (response) {
				$("#agentModal").modal('hide');
				var title = (response.status) ? 'Success' : 'Warning';
				var type = (response.status) ? 'success' : 'warning';
				swal({
						title: title,
						text: response.message,
						type: type,
						showCancelButton: false,
						confirmButtonText: "Ok!",
						closeOnConfirm: true
					},
					function () {
						if (response.status) {
							var ref = $('#agentModal').data('element');
							var update_agent_name = $(ref).closest('td').siblings()[10];
							$(update_agent_name).text(response.agent_name.agent_name);

							$("#agent_reassign").val('');
							location.reload();


						}
					});

			}
		}
	});
});

$(document).on('click', '#txnaction-btn', function (event) {
	event.preventDefault();
	$("#txn-error").hide();
	$("#txndate-error").hide();

	if (!$("#txno").val()) {
		$("#txn-error").show();
		return false;
	}
	if (!$("#txndate").val()) {
		$("#txndate-error").show();
		return false;
	}

	$.ajax({
		url: "/tls_agent_policy_create",
		type: "POST",
		async: false,
		data: { "lead_id": $('#paymentModal').data('lead_id'), "TxRefNo": $("#txno").val(),"txndate" : $("#txndate").val() },
		dataType: "json",
		success: function (response) {

			if (response) {
				$("#paymentModal").modal('hide');
				var title = (response.ErrorCode == '1') ? 'Success' : 'Warning';
				var type = (response.ErrorCode == '1') ? 'success' : 'warning';
				var message = (response.ErrorCode == '1') ? 'Policy Created Successfully' : 'Erron In Policy Creation';
				swal({
						title: title,
						text: message,
						type: type,
						showCancelButton: false,
						confirmButtonText: "Ok!",
						closeOnConfirm: true
					},
					function () {

						location.reload();



					});

			}
		}
	});
});

function call_function(certificate_no)
{
	$("#"+certificate_no).html("Please wait...");
	$("#"+certificate_no).attr("disabled", true);

	$.post("/coi_download_call",
		{ "certificate_no":certificate_no},
		function (e) {
			var obj = JSON.parse(e);
			$("#"+certificate_no).html("Download");
			$("#"+certificate_no).attr("disabled", false);
			if(obj.status == 'success'){
				//document.getElementById("set_id_"+certificate_no).href = obj.url;
				var a = document.createElement('a');
				var url = obj.url;
				a.href = url;
				a.download = url;
				document.body.append(a);
				a.click();
				a.remove();
				window.URL.revokeObjectURL(url);

			}else{
				alert("Try Again Later...");
			}

		});
}

function call_function_sendmail(emp_id)
{
	$.post("/tls_agent_coi_mail_trigger",
		{ "emp_id":emp_id},
		function (e) {
			var obj = JSON.parse(e);

			if(obj.status == 'success'){
				alert("COI Trigger Successfully");
			}else{
				alert("Try Again Later...");
			}

		});
}

function BackToDo(emp_id,lead_id,agent_name){ // by pooja
	var agent_name=atob(agent_name);

	$.ajax({
		url:'/save_maker_checker_remark',
		type:'post',
		dataType: 'json',
		data:{remark:'Back From AV',remark_lead:lead_id,remark_emp_id:emp_id,remark_agent_name:agent_name,pddisposition:''},
		success:function(response){
			if(response.status==1){
				swal({
					title: "Success",
					text: "Submitted Successfully",
					type: "success",
					showCancelButton: false,
					confirmButtonText: "Ok!",
					closeOnConfirm: true,
					allowOutsideClick: false,
					closeOnClickOutside: false,
					closeOnEsc: false,
					dangerMode: true,
					allowEscapeKey: false
				},function(){
					/*$('#send_remark').val('');
					$('.bs-example-modal').modal('hide');*/

					location.reload();
				});

			}else{

				swal({
					title: "Alert",
					text: response.message,
					type: "warning",
					showCancelButton: false,
					confirmButtonText: "Ok!",
					closeOnConfirm: true,
					allowOutsideClick: false,
					closeOnClickOutside: false,
					closeOnEsc: false,
					dangerMode: true,
					allowEscapeKey: false
				});

			}


		}

	});
}