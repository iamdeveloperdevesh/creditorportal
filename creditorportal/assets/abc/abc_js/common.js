function populateQuoteData(data) {
    let applicant_header_html = "";
    let net_premium = 0;
    for (let applicant_key in data) {

        if (applicant_key !== "net_premium") {

            let policy_count = 0;

            if (data[applicant_key]['policies']) {
                policy_count = Object.keys(data[applicant_key]['policies']).length;
            }

            if (!policy_count) {
                continue;
            }

            applicant_header_html += `
                        <div class="head-lbl-2 mt-1" id="${applicant_key}_premium">
                        </div>`;
        } else if (applicant_key == "net_premium") {
            net_premium = data["net_premium"];
        }
    }
    $('#total_premium').html(net_premium);
    //app_coapp_premium = net_premium
    $('#premium_calculations_data .head-lbl-2').remove();

    $("#premium_calculations_data").append(applicant_header_html);

    for (let applicant_key in data) {

        let policy_data = data[applicant_key];

        let policies = policy_data.policies;

        for (let policy_name in policies) {
            if (policies.hasOwnProperty(policy_name)) {

                $("#" + applicant_key + "_premium").append(
                    `<p>${policy_name}<span class="fl-right"><i class="fa fa-inr"></i> ${policies[policy_name]}</span></p>`
                );
            }
        }
    }
}