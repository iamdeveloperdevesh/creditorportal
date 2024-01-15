$(document).ready(function(){
$('#submit_flex').click(function(){
         var $files = $("#flex_upload_form").find("input[type='file']");
        var form_data = new FormData();
        form_data.append("docfile", $files.get(0).files[0]);
       	$.ajax({
			 type:'POST',
			 url: "/employer/upload_flex_allocation",
             data: form_data,
             async: false,
             cache: false,
             dataType:'json',
             contentType: false,
             processData: false,
             mimeType: "multipart/form-data",
			 success : function (data) {
			 	if (data.status == 'true') 
			 	{
			 		$("#getCodeModal").modal("toggle");
					$("#getCode").html(data.message);
			 	}
			 	else
			 	{
			 		$("#getCodeModal").modal("toggle");
					$("#getCode").html(data.message);
			 	}
			 }
		});
    });
});