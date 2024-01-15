<?php
//echo 13;die;
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://betaaffinity.elephant.in/GadgetInsurance/CreateProposalapi123',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{
  "ClientCreation": {
      "token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6IjU2IiwiaWF0IjoxNjg5MTYxMjAwLCJleHAiOjE2ODkxNzkyMDB9.cKbcCana24OoYKe22up38J0q1zA5Knm-Ac5v5hyUGAI",
    "Api_type": "issuance",
    "Invoice_number": "785236fd6", 
	"plan_id": 666,
    "policy_id": 1106,
	"salutation": "Mr",
	"first_name": "Sagar",
	"middle_name": "",
	"last_name": "Das",
	"gender": "Male",
	"mobile_number": "919009922285",
    "pincode": "400001",
    "Address1": "Alliance",
    "Address2": "Elephant",
    "Address3": "Juhu",
	"email_id": "sagar@elephant.in",
    "mode_of_shipment": "Road",
    "from_country": "India",
    "to_country": "India",
    "from_city": "BANGALORE",
    "to_city": "BANGALORE",
    "type_of_shipment": "Intra",
    "currency_type": "INR",
    "cargo_value": "100000",
    "rate_of_exchange": "null",
    "date_of_shipment": "26/07/2023",
    "Bill_number": "",
    "Bill_date": "12-07-2023",
    "credit_number": "",
    "credit_description": "",
    "place_of_issuence": "BANGALORE",
    "Invoice_date": "10/07/2023",
    "subject_matter_insured": "Household Goods",
    "marks_number": "",
    "vessel_name": "",
    "Consignee_name": "",
    "Consignee_add": "",
    "Financier_name": "",
    "SumInsured": "100000",
    "userId":"49"
  }
}',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: text/plain',
        'Cookie: ci_session=tkudh5lh43v4kch8iiou57gndaeho2d0'
    ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
