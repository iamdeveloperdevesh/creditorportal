load_data();

function load_data(data_search) {
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
		"ordering": false,
		"paging":   true,    
		"responsive": true,
		"searching": false,
		buttons: [{
	
			extend: 'excelHtml5',
			text: 'Export<i class="fa fa-file-excel-o"></i>',
	
			init: function (dt, node, config) {
				debugger;
	
			},
			customizeData: function (data) {
				for (var i = 0; i < data.body.length; i++) {
					for (var j = 0; j < data.body[i].length; j++) {
						data.body[i][j] = '\u200C' + data.body[i][j];
					}
				}
			}
	
		}],
		"processing": true,
		"serverSide": true,
		"order": [],
		"ajax": {
			url: '/tele_getrenewalgroupdata',
			type: "POST",
			"data" : {
				"lead_id" : $('#lead_id').val(),
				"certificateno" : $('#certificateno').val(),				
			},			
			complete: function (response) {
				console.log(response);
			}
	
		},
	
	});
	}




$('#lead_id').on("keyup change", function () {
	$('#leadMasterTable').DataTable().destroy();
	$(this).attr("disabled", false);
	load_data();
});

$('#certificateno').on("keyup change", function () {
	$('#leadMasterTable').DataTable().destroy();
	$(this).attr("disabled", false);
	load_data();
});

$(document).on('click','#clear',function(){
	$('#searchpp').val('');
	location.reload();

});

$("#importAv").validate({
	ignore: ".ignore",
	rules: {
	filetoUpload: {
	required: true
	},
	
	},
	messages: {},
	submitHandler: function (form) 
	{   
		ajaxindicatorstart("uploading....");
		var file_agent = $("#file_data");
 
		var file_agent_data = file_agent[0].files;

		if(file_agent_data.length != 0)
		{
			var abc = file_agent_data[0].name.substring(file_agent_data[0].name.lastIndexOf('.') + 1);
		
			var fileExtension = ['xlsx','CSV','xls','csv'];
			if ($.inArray(abc, fileExtension) == -1)
			{
				ajaxindicatorstop();
				swal("Alert", "Please check documents uploaded, Supported documents xlsx, xls,csv");
				return false;
			}
		}
		
		 var data = new FormData(form);
		  $.ajax({
            type: "POST",
         
            url: "/tele_groupimport",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            mimeType: "multipart/form-data",
			dataType: "json",
            success: function(e) {
				console.log(e);
				if(e.errorCode == 1)
				{
					ajaxindicatorstop();
					var er ="" ;
					if($.isArray(e.msg))
					{
						var i=0;
						$.each(e.msg,function(key,msgs)
						{
							// console.log(e.msg[i].fields);
							// alert(e.msg[i].fields);
							er += e.msg[i].fields+'\n';
							// er + = msgs.split("\n");
							i++;
						});
					}
					else
					{
						er = e.msg;
					}
					
					     swal({
								title: "Alert",
								text: er,
								type: "warning",
								showCancelButton: false,
								confirmButtonText: "Ok!",
								closeOnConfirm: true,
								allowOutsideClick: false,
								closeOnClickOutside: false,
								closeOnEsc: false,
								dangerMode: true,
								allowEscapeKey: false
								},
								function () 
								{
										
								});
								return;
					
				}
				else
				{
					ajaxindicatorstop();
					swal({
								title: "Success",
								text: e.msg,
								type: "success",
								showCancelButton: false,
								confirmButtonText: "Ok!",
								closeOnConfirm: true,
								allowOutsideClick: false,
								closeOnClickOutside: false,
								closeOnEsc: false,
								dangerMode: true,
								allowEscapeKey: false
								},
								function () 
								{
									location.reload();	
								});
				}

            }
        });
	}
	});




