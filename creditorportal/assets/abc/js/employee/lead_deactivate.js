$(document).ready(function () {

    var lead_id = $("#lead_id").val();
    var product_id = $("#product_id").val();
	 var unique_ref_no = $("#unique_ref_no").val();
  



    if (lead_id) {
        lead_deactivate(lead_id,unique_ref_no,product_id);
    }

});
/* popup msg */

function lead_deactivate(lead_id,unique_ref_no,product_id)
{
	var text_html = "This product proposal already created with the same customer/proposer";
    var text_button = 'Do you want to continue?';
	swal({
		title: text_html,
		text:text_button,
        
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes, continue with existing!",
        cancelButtonText: "No, create new!",
        closeOnConfirm: false,
        closeOnCancel: false
    },
            function (isConfirm) {
                var create_new = "yes";/*continue with new */
                if (isConfirm) {
                    var create_new = "no"; /*continue with existing */
                }
				debugger;
var is_redirect_popup = 1;
                $.post("/lead_deactivate",
                        {"lead_id": lead_id, "product_id": product_id, "unique_ref_no": unique_ref_no, 'create_new': create_new,'is_redirect_popup':is_redirect_popup},
                        function (e) {
	ajaxindicatorstart("Please wait...");
                           var obj = JSON.parse(e);
						    if (obj.status == 3) {
									ajaxindicatorstop();
                                  	swal({
					title: "warning",
					text: obj.message,
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
				function () {

				});
                            }else{
								ajaxindicatorstop();
								window.location.href = obj.url;
							}
						   // 

                        });
            });

}