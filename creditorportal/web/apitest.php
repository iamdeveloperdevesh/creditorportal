<?php

$request = [
            "partnerName" => "ABHI",
            "channelName" => "NA",
            "documentDetails"=> [
                "leadID"=> "202232300",
                "applicationNo"=> "",
                "proposalNo"=>"",
                "policyNo"=> "",    
                "memberId"=> "GFB-AS-21-2000530",                       
                "issuanceDt"=> "23-03-2022",
                "docName"=> "otp_log_202232300.pdf",
                "docType"=> "OTP Log File",
                "docKey"=> "",
                "docFile"=> "",
                "docURL"=> "http:\/\/eb.benefitz.in\/resources\/uploads\/thanos\/otp_log_202232300.pdf"
            ]

        ];

      //  echo json_encode($request);exit;
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://sakshamuat.axisbank.co.in/gateway/api/rcm/v1/docupload",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 180,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($request),
          CURLOPT_SSLCERT => "https://affinitycld-uat.adityabirlahealth.com/ABHIAXIS.p12",
          CURLOPT_SSLCERTTYPE => "P12",
          CURLOPT_HTTPHEADER => array(
            "Content-Type:  application/json",
            "X-IBM-Client-Id: 2df37d2c-7415-4773-bf8f-0f395543b395",
            "X-IBM-Client-Secret: E5vI7qD5aO1sX2oP5oK6cP8lC6tH5bL3mW6wO0kR4lW3eT2kR7",
            "x-fapi-epoch-millis: strtotime(date('Y-m-d H:i:s'))",
            "x-fapi-channel-id: ABHI",
            "x-fapi-channel-id:rand()",
            "cache-control: no-cache"
          ),
        ));
        $response = curl_exec($curl);

      
    print_r($response);