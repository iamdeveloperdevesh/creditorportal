var column_sort = {};
function create_proposal(emp_id) {
	emp_id = emp_id || '';
	if (emp_id) {
		$.ajax({
			url: "/tls_redirect_proposal",
			type: "POST",
			async: false,
			data: { emp_id: emp_id },
			dataType: "json",
			success: function (response) {

				debugger;
				if (response.status) {
					location.replace('/tele_create_proposal');
				}
			}
		});
	}
}
function load_data(data_search) {
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

	var dataTable = $('#leadMasterTable').DataTable({
		dom: 'Bfrtip',
		searching: false,
		buttons: [{
			extend: 'excel',
			text: 'Export <i class="fa fa-file-excel-o"></i>',
			titleAttr: 'Excel',
			action: newExportAction
		}],
		"processing": true,
		"serverSide": true,
		"order": [],
		"ajax": {
			url: 'tls_lead_tbl_ajax',
			type: "POST",
			data: {
				data_search: JSON.stringify(data_search)
			},
			complete: function (response) {
				if (response.responseJSON.data.length != 0) {
					$('#leadgrid').show();
				}
			}
		},
		"columnDefs": [{
			"targets": [0, 10],
			"orderable": false

		},],
	});
	$('.bd-example-modal-md').removeClass('hide');
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
	debugger;
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
	$.validator.addMethod('valid_mobile', function (value, element, param) {
		var re = new RegExp('^[4-9][0-9]{9}$');
		return this.optional(element) || re.test(value);
	}, 'Enter a valid 10 digit mobile number');
	$.validator.addMethod('validateEmail', function (value, element, param) {
		var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		return reg.test(value);
	}, 'Please enter a valid Email Address.');

	$('body').on("keyup", "input[name=first_name],input[name=last_name]", function () {
		if ($(this).val().match(/[^a-zA-Z]/g)) {
			$(this).val($(this).val().replace(/[^a-zA-Z ]/g, ""));
		}
	});
	$('body').on("keyup", ".search", function () {
		manual_search();
	});
	$('body').on("change", "#status", function () {
		manual_search();
	});
	$('body').on("click", ".blue_bg_header", function () {
		manual_search(this);
	});
	$('body').on("keyup", "#mob_no, #saksham_id, #lead_id", function () {
		if ($(this).val().match(/[^0-9]/g)) {
			$(this).val($(this).val().replace(/[^0-9]/g, ""));
		}
	});
	$("#dob").datepicker({
		dateFormat: "dd-mm-yy",
		prevText: '<i class="fa fa-angle-left"></i>',
		nextText: '<i class="fa fa-angle-right"></i>',
		changeMonth: true,
		changeYear: true,
		yearRange: "-100Y:-0Y",
		maxDate: new Date()

	});

	load_data();

	$("#lead-form").validate({
		ignore: ".ignore",
		rules: {
			lead_id: {
				required: true
			},
			saksham_id: {
				required: true
			},
			salutation: {
				required: true
			},
			first_name: {
				required: true
			},
			last_name: {
				required: true
			},
			gender1: {
				required: true
			},
			mob_no: {
				required: true,
				valid_mobile: true
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
	if (!$("#lead-form").valid()) {
		return false;
	}

	$.ajax({
		url: "/tls_insert_lead",
		type: "POST",
		async: false,
		data: $("#lead-form").serialize(),
		dataType: "json",
		success: function (response) {
			debugger;
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
						location.reload();
					});

			}
		}
	});
});