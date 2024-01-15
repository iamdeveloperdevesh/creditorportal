$(document).ready(function () {
   
    var $body = $("body");
    $body.on('click', '#vivant_redirect', function () {
	
		$('#vivant_modal').modal('show'); 
		
	});
	 $body.on('click', '#vivant_submit_portal_redirect', function () {

        $.ajax({
            url: "/get_employee_data_for_sso",
            type: "POST",
            async: true,
            dataType: "json",
            success: function (response) {
                 // debugger;
                  console.log('https://tmibasl_uat.vivant.me/login?Data={"EmployeeCode":"'+response.emp_code+'","PartnerCode":"FYNTUNE"}');
                  //return false;
                // $("#Account").val(response.value);
                // $("#d").val(response.d);
                // var hash = CryptoJS.HmacSHA256(response.message, "A1HS8CUR1TY@9812");
                // var hashInBase64 = CryptoJS.enc.Base64.stringify(hash);

                // $("#h").val(hashInBase64);
                //location.href = 'https://tmibasl_uat.vivant.me/login?Data={"EmployeeCode":1234,"PartnerCode":"FYNTUNE"}';
				location.href = 'https://tmibasl_uat.vivant.me/login?Data={"EmployeeCode":"'+response.emp_code+'","PartnerCode":"FYNTUNE"}';
                                

            }
        });
    });
});
