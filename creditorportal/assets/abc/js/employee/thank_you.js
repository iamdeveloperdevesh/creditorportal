function call_function(certificate_no, lead_id) {
  $("#" + certificate_no).html("Please wait...");
  $("#" + certificate_no).attr("disabled", true);
  $.ajax({
	url: "/save_ack_mis",
	type: "POST",
	async: false,
	data: {
		lead_id: lead_id,
		certificate_no: certificate_no,
		click_type: 'coi'
	},
	// dataType: "json",
	success: function (response) {
		$.post(
			"/coi_download_call",
			{ certificate_no: certificate_no },
			function (e) {
			  var obj = JSON.parse(e);
			  $("#" + certificate_no).html('<i class="fa fa-download"></i> Download');
			  $("#" + certificate_no).attr("disabled", false);
			  if (obj.status == "success") {
				//document.getElementById("set_id_"+certificate_no).href = obj.url;
				var a = document.createElement("a");
				var url = obj.url;
				a.href = url;
				a.download = url;
				document.body.append(a);
				a.click();
				a.remove();
				window.URL.revokeObjectURL(url);
			  } else {
				alert("Try Again Later...");
			  }
			}
		  );
		
	},
  });

 
}

function call_function_acknowledgement(lead_id) {
  $("#" + lead_id).html("Please wait...");
  $("#" + lead_id).attr("disabled", true);

//  alert("asdasdsa");return

  $.ajax({
	url: "/save_ack_mis",
	type: "POST",
	async: false,
	data: {
		lead_id: lead_id,
		click_type: 'ack'
	},
	// dataType: "json",
	success: function (response) {
		 //document.getElementById("set_id_"+certificate_no).href = obj.url;
		 $.post("/acknowledgement_download_call", { lead_id: lead_id }, function (e) {
			var obj = JSON.parse(e);
			$("#" + lead_id).html('<i class="fa fa-download"></i> Download');
			$("#" + lead_id).attr("disabled", false);
			if (obj.status == "success") {
		
				var a = document.createElement("a");
				var url = obj.url;
				a.href = url;
				a.target = "_blank";
				a.download = url;
				//document.body.append(a);
				document.body.appendChild(a);
				a.click();
				a.remove();
				window.URL.revokeObjectURL(url);
		
			 
			} else {
			  alert("Try Again Later...");
			}
		  });
		
	},
  });


}
