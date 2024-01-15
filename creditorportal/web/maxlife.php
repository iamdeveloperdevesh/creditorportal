<?php


$curl = curl_init();

/* MIBL  Start */
/* curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://uatapi.maxlifeinsurance.com/is/security/oauth/token',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => '{
    "request": {
        "header": {
            "correlationId": "25478965874",
            "appId": "MAHINDRA"
        },
        "payload": {
            "clientId": "63om3c4n1f3blaofo31ovscsgc",
            "clientSecret": "97v10l5r5rllp64hbfdjl45ldrtvb16jst9710ccrnpee9o6kbe"
        }
    }
}',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'x-api-key: KK43WE3Kyz5p13z2iOWPc53Fc3xZnQEeaBXxVO7Z'
    ),
)); */
/* MIBL  End */

/* Sample  Start */

/* curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://uatapi.maxlifeinsurance.com/is/security/oauth/token',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => '{
    "request": {
        "header": {
            "correlationId": "25478965874",
            "appId": "AXIS"
        },
        "payload": {
            "clientId": "28j0k108t2bli7vs5angnh11f1",
            "clientSecret": "9bm0b8m1172li6b3u4n55249avn8q1ht6242o8ps4qnb0gff8sl"
        }
    }
}',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'x-api-key: slVK8dUJA6aeiRAtEGGzg8QSrzDSEfR38sJtZ6Xp'
    ),
)); */

/* Sample  End */

/* PALM  Start */

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://uatapi.maxlifeinsurance.com/is/security/oauth/token',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => '{
    "request": {
        "header": {
            "correlationId": "25478965874",
            "appId": "PALM"
        },
        "payload": {
            "clientId": "2ag0jcm446ba9eif8f4vg45n24",
            "clientSecret": "1cb1a1nli6kmdlkqsrtgs1i6odaj45rl3a3g99ki2q9jaif4o914"
        }
    }
}',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'x-api-key: ySgG86N3BFnXHHmVkh2F73bQgaNqT556unX42nFb'
    ),
));

/* PALM  End */


echo $token_data = curl_exec($curl); die;

curl_close($curl);
//echo $token;

if ($token_data) {
    
    $data = json_decode($token_data, true); //print_r($data);
	//$token = isset($data['response']['payload']['accessToken']) ? 'Bearer '.$data['response']['payload']['accessToken'] : ""; 
    echo $token = isset($data['response']['payload']['accessToken']) ? 'Bearer '.$data['response']['payload']['accessToken'] : ""; die;
	
	
	
	$req_data = '{"Request":{"RequestInfo":{"CreationDate":"","CreationTime":"","LastSyncDate":"","RequestorToken":"","SourceInfoName":"","TransactionId":"","UserName":""},"RequestPayload":{"Transactions":[{"TransTrackingID":"","TransactionData":{"Key1":"C101","Key10":"","Key11":"","Key12":"","Key13":"","Key14":"","Key15":"","Key16":"","Key17":"","Key18":"","Key19":"","Key2":"","Key20":"","Key21":"","Key22":"","Key23":"","Key24":"","Key25":"","Key3":"","Key4":"","Key5":"","Key6":"","Key7":"","Key8":"","Key9":"","Type":"illustration","illustrationRuleGroup":"72","illustrationRuleName":"SSP_Illustration","ruleInput":{"InsuredDetails":{"name":"Test Test","title":"Mr.","age":"25","sex":"M","state":"MH","isProposerSameAsInsured":"true","vestingAge":0,"monthlyIncome":0,"emailIdTo":"","emailIdCc":"","isLIEqualsPH":"Yes","policyHolderState":"HR","policyHolderStateNm":"Haryana","maxLifeRegisteredState":"MH","maxLifeRegisteredStateNm":"Maharashtra","isNRI":"No","nationality":"Indian","employeeDiscount":"No","agentDiscount":"No","webDiscount":"No","customerDiscount":"Yes","affinityGroup":"No","isNRIOriginal":"No","lifeStageEventBenefit":"No"},"PayorDetails":{"name":"","title":"Mr.","age":"","sex":""},"PayorDetailsFix":{"name":"Test Test","title":"Mr.","age":"25","sex":"M","DOB":"01-12-1996"},"ChildDetails":{"name":"","age":0,"sex":""},"PolicyDetails":{"sumAssured":"10000000","bonusOption":0,"policyTerm":"10","premiumPayingTerm":"10","premiumPaymentType":"503","premiumMode":"6001","committedPremium":0,"productCode":"154","annuityOption":"SL","solveOption":"No","annualIncome":"1000000","annualIncomeSuitability":0,"sumAssuredOption":0,"maturityValueAmount":0,"premiumPayingTermProduct2":0,"isCombinedPremiumRequired":"No","combinedPremiumAmount":0,"paymentModeName":0,"maturityAge":0,"dateOfProposal":"2022-02-08","discountOption":"NA","qropsRequired":"No","productType":"TRAD","edc":"2022-02-08","gstEfectiveDate":"2017-05-24","deathBenefit":"6010","incomePeriod":"NA","premiumBreakOption":"No","premiumBreakYear1":"11","premiumBreakYear2":"22","premiumBackOption":"No","qrops":"","companyPension":"","annuityPurchasedFrom":"","variant":"","purchasePrice":"","defermentPeriod":"","rop":"","dateOfQuotation":"2022-02-08","testingPlatform":"Others","criticalIllnessSumAssured":0},"CIDisabilityRider":{"id":"","isRequired":"No","sumAssured":"","policyTerm":"","variant":"","emrRequired":"No","emr":"","flatExtraRequired":"No","flatExtraRate":"","flatExtraDuration":""},"Channel":{"id":"9096","name":"Internet Sales \u2013 Direct"},"DDRider":{"id":"0","isRequired":"No","sumAssured":"0","term":"0","emrRequired":"No","emr":0,"emrDuration":0,"flatExtraRequired":"No","flatExtraRate":0,"flatExtraDuration":0},"PABRider":{"id":"0","isRequired":"No","sumAssured":"0","term":"0","emrRequired":"No","emr":0,"emrDuration":0,"flatExtraRequired":"No","flatExtraRate":0,"flatExtraDuration":0},"TermRider":{"id":"0","isRequired":"No","sumAssured":"0","term":"0","emrRequired":"No","emr":0,"emrDuration":0,"flatExtraRequired":"No","flatExtraRate":0,"flatExtraDuration":0},"COVIDRider":{"id":"0","isRequired":"No","sumAssured":"0","term":"0","emrRequired":"No","emr":"","emrDuration":"","flatExtraRequired":"No","flatExtraRate":"","flatExtraDuration":""},"FiveYearRandCRider":{"id":"0","isRequired":"No","sumAssured":0,"term":0,"emrRequired":"No","emr":0,"emrDuration":0,"flatExtraRequired":"No","flatExtraRate":0,"flatExtraDuration":0},"GIORider":{"id":"0","isRequired":"No","sumAssured":0,"term":0,"emrRequired":"No","emr":0,"emrDuration":0,"flatExtraRequired":"No","flatExtraRate":0,"flatExtraDuration":0},"WOPRider":{"id":"0","isRequired":"No","sumAssured":0,"term":0,"emrRequired":"No","emr":0,"emrDuration":0,"flatExtraRequired":"No","flatExtraRate":0,"flatExtraDuration":0},"PayorRider":{"id":"0","isRequired":"No","sumAssured":0,"term":0,"emrRequired":"No","emr":0,"emrDuration":0,"flatExtraRequired":"No","flatExtraRate":0,"flatExtraDuration":0},"FlatExtraBase":{"rate":0,"isRequired":"No","duration":0},"EMR":{"rate":0,"isRequired":"No","duration":0},"CAB":{"isRequired":"No","rate":""},"Loan":{"isRequired":"No","amount":["0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"]},"LoanRepayment":{"isRequired":"No","amount":["0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"]},"OPPB":{"isRequired":"No","premiumValues":"0"},"PartnerCareRider":{"id":0,"isRequired":"No","term":0,"sumAssured":0,"emrRequired":"No","emr":0,"emrDuration":0,"flatExtraRequired":"No","flatExtraRate":0,"flatExtraDuration":0,"patnercarereq":0},"SurrenderOption":{"isRequired":"No","amount":["0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"]},"FeeType":{"coverMultiple":5013,"incomeBooster":5019,"maturityBenefitRate":5015},"TopUp":{"input":["0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0","0"],"pct":0.05,"isRequired":"No"},"IncomeBooster":{"rating1":6,"rating2":10},"InvPension":{"maximiseOption":0,"preserveOption":0},"Funds":{"growthSuperFund":0,"conservativeFund":0,"growthFund":0,"secureFund":0,"balancedFund":0,"highGrowthFund":0},"Extension":{"mliEmpl":"No","rateId":5021,"riskClass":"606","saveMoreTomOptionReqd":0,"dfaOption":"No","stpOption":"No","indexationOption":"","firstName":"Test","lastName":"Test","agentName":""},"AnnuitantDetails":{"secondAnnuitantDob":"","secondAnnuitantName":"","secondAnnuitantAge":null,"secondAnnuitantSex":""},"ADDRider":{"id":0,"isRequired":"No","sumAssured":0,"term":0,"emrRequired":"No","emr":0,"emrDuration":0,"flatExtraRequired":"No","flatExtraRate":0,"flatExtraDuration":0},"WOPPlusRider":{"id":0,"isRequired":"No","sumAssured":0,"term":0,"emrRequired":"No","emr":0,"emrDuration":0,"flatExtraRequired":"No","flatExtraRate":0,"flatExtraDuration":0},"TermPlusRider":{"id":0,"isRequired":"No","sumAssured":0,"term":0,"emrRequired":"No","emr":0,"emrDuration":0,"flatExtraRequired":"No","flatExtraRate":0,"flatExtraDuration":0},"AcceleratedCriticalIllness":{"id":0,"isRequired":"No","sumAssured":0,"term":0,"emrRequired":"No","emr":0,"emrDuration":0,"flatExtraRequired":"No","flatExtraRate":0,"flatExtraDuration":0},"AccidentCover":{"id":"0","isRequired":"No","sumAssured":"50000","emrRequired":"No","emr":"50"},"ComprehensiveAccident":{"isRequired":"No","sumAssured":"0"},"CIEMR":{"isRequired":"No","rate":"30","duration":""},"CIFlatExtra":{"isRequired":"No","rate":"3","duration":"3"},"ADB":{"isRequired":"No","rate":"50","duration":""}},"validationRuleGroup":"72","validationRuleName":"SSP_Validation"}}]}}}';

    $curl1 = curl_init();

    curl_setopt_array($curl1, array(
        CURLOPT_URL => 'https://maxencryption.benefitz.in/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'method=encryption&data=' . $req_data,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response1 = curl_exec($curl1);

    
	//$curl_info = curl_getinfo($curl1);
    //print_r($curl_info);die(" hhh "); 
echo $response1;
//die("hello");
curl_close($curl1);
$str= "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";

$lead_data = [
            'request' => [
                'payload' => $str,
            ],
        ];

$curl2 = curl_init();

curl_setopt_array($curl2, array(
  CURLOPT_URL => 'https://uatapi.maxlifeinsurance.com/is/soa/nb/leadmanagement/productpremium/v1',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>json_encode($lead_data),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'correlationid: 28463278',
    'appid: MAHINDRA',
    'x-api-key: KK43WE3Kyz5p13z2iOWPc53Fc3xZnQEeaBXxVO7Z',
    'Authorization: Bearer eyJraWQiOiJnRm5IUTVDdm9taUFmU0lmRjJwNGF6TkFQYTVpS0dUeGJmdW15Ym9UZFUwPSIsImFsZyI6IlJTMjU2In0.eyJzdWIiOiI2M29tM2M0bjFmM2JsYW9mbzMxb3ZzY3NnYyIsInRva2VuX3VzZSI6ImFjY2VzcyIsInNjb3BlIjoiYWRtaW5cL3dyaXRlIGFkbWluXC9yZWFkIiwiYXV0aF90aW1lIjoxNjQ2NzQ2MTc2LCJpc3MiOiJodHRwczpcL1wvY29nbml0by1pZHAuYXAtc291dGgtMS5hbWF6b25hd3MuY29tXC9hcC1zb3V0aC0xX2tMVkxMT2hGRCIsImV4cCI6MTY0Njc0OTc3NiwiaWF0IjoxNjQ2NzQ2MTc2LCJ2ZXJzaW9uIjoyLCJqdGkiOiI1N2VlMjlmNy01OWNlLTQzZjUtOTliYS04NTI4ZTAwMmQ5MTAiLCJjbGllbnRfaWQiOiI2M29tM2M0bjFmM2JsYW9mbzMxb3ZzY3NnYyJ9.YmjN52RqCAgo6V1FQErt2rbQEquKCKSatpMCMHzk25DdRGR4zxhqA6urp0zau-ArnPuwbaFZ7bbxhKpJeAYJnIWBZhiE3ybJoTKj6MtsYvyBntjWEK2RYl_KanKRA7MUoO5_95Elt5eDqcH0YSBmi61hMswfVWCPF_LdJ7clKufe0gctyPANSq505LlmJorit_-siBPCJXCivQsx-AyGWHoosIfyqPw4BaLojeO9-ivd9fvq2YOO3mSHQlObHiwT-fHIlRsKZN_BUfs4vH2-9pqIcVDliPcPNWFqpMRAkGM8tkRnj27la1DzVoR4w2aR77zuSZr8YAC48wO-SK7yEw'
  ),
));

$response = curl_exec($curl2);

curl_close($curl2);
echo $response;

}

