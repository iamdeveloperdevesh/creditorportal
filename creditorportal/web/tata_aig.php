<?php
echo "darshana";die;
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://uatapigw.tataaig.com/travel/v1/quote',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "quote_id": "",
    "quote_no": "",
    "proposal_no": "",
    "proposal_id": "",
    "policy_id": "",
    "product_id": "M100000000003",
    "selected_product": "Travel Guard",
    "selected_plan": "Annual Gold",
    "selected_zone": "AN - Worldwide 1 - 45",
    "departure_date": "12/02/2021",
    "return_date_single": "11/02/2022",
    "member_count": "1",
    "dob_1": "12/12/1992",
    "dob_2": "",
    "dob_3": "",
    "dob_4": "",
    "dob_5": "",
    "dob_6": "",
    "dob_7": "",
    "dob_8": "",
    "dob_9": "",
    "dob_10": "",
    "age_1": "27",
    "age_2": "",
    "age_3": "",
    "age_4": "",
    "age_5": "",
    "age_6": "",
    "age_7": "",
    "age_8": "",
    "age_9": "",
    "age_10": "",
    "baseunit_1": "",
    "baseunit_2": "",
    "baseunit_3": "",
    "baseunit_4": "",
    "baseunit_5": "",
    "baseunit_6": "",
    "baseunit_7": "",
    "baseunit_8": "",
    "baseunit_9": "",
    "baseunit_10": "",
    "application_date": "19/01/2021",
    "triptype": "Multi Trip",
    "coverage_period": "365",
    "user_name": "SOUBHAGYA PARIDA",
    "producer_name": "Soubhagya Parida",
    "producer_code": "1262620000",
    "gstin": "",
    "proposer_title": "Mr.",
    "fname_proposer": "muruga",
    "mname_proposer": "",
    "lname_proposer": "nantham",
    "email": "muruganantham@godbtech.com",
    "mobile_no": "6382254339",
    "prd_IntShort_code": "TG",
    "customer_pincode": "680020",
    "office_location": "SERVICE CENTER",
    "customer_state": "KERALA",
    "office_location_code": "90000",
    "agent_id": "soubhagya.nucsoft@tataaig.com",
    "branch_gstin_no": "27AABCT3518Q1ZW",
    "producer_state": "MAHARASHTRA",
    "txt_posp": "",
    "txt_aadhar_number": "",
    "txt_license_no": "23434234234234",
    "txt_pan_no": "FFFFF2222F",
    "channel": "Direct",
    "category": "DIRECT",
    "selected_new_product_name": "Travel Guard Policy",
    "policy_duration": "365 Days",
    "gc_product_code": "1601"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'authorizationToken: Bearer eyJraWQiOiJXcnViZzNPTVdjeTNXdG9cL0I4bVRHSGQwY3QwSXI5ZXdhcFY0czhLNXBEZz0iLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiI1cHAwbmUwMnY3N2w4M2kxZXU0Ymx2Y3U2MiIsInRva2VuX3VzZSI6ImFjY2VzcyIsInNjb3BlIjoiaHR0cHM6XC9cL3VhdGFwaWd3LnRhdGFhaWcuY29tXC90cmF2ZWxcL3JlYWQiLCJhdXRoX3RpbWUiOjE2NDgxMTg2NTgsImlzcyI6Imh0dHBzOlwvXC9jb2duaXRvLWlkcC5hcC1zb3V0aC0xLmFtYXpvbmF3cy5jb21cL2FwLXNvdXRoLTFfU2NzOGZtY1hLIiwiZXhwIjoxNjQ4MTE5ODU4LCJpYXQiOjE2NDgxMTg2NTgsInZlcnNpb24iOjIsImp0aSI6IjU5MWNlNjc2LTQxOTAtNDJjZS1iYjgyLTYxZjhjMjM4MjM4ZCIsImNsaWVudF9pZCI6IjVwcDBuZTAydjc3bDgzaTFldTRibHZjdTYyIn0.KS5WnfbxHcCla62FRtxKsUXlA2XtlvnTjN0VvqHPQ-3UxEipDqc-iuwV0y-fgTe98wq7hiBSo5b3Yr6I4Zsta2StlT9cMEDuBSGs5QhhlU0Hg3b1rj05sQ1I9gVhyM359f8GRNQDJ6e03DXheJvM7qhIuxzF0qU6aa3_oSlhyrCK8s1rhSnN7AnUQvOfRc1M90w7lCWd6JCDPqPcLSbtvuJcmcsFNO8y5q5T3MDLgrqrlD0aiQXtpuHWjzfLmRIBj5oKDhsT1PkDK6gOLYZJUjlCxCGLnpkj4jBv1R4-Sefb34uxHlJbeQy6gRu2W9LBsLPpm7jNNHUayIH28CaATg',
    'x-api-key: 5QerRezeZs3PrVdLQu79c1v9Nsh5S7BOan26zc7P'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
