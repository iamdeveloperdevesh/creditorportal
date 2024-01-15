<?php  

/*Database IP: 10.21.204.144/10.21.204.81
Port number: 1443 (Default Port Number)
Database name: ABHIHDFC_BRMS1
Table name: OTP_REQUEST
Username: ABHI_TC
Password: birla@123
Data base type: mssql*/

//echo phpinfo();

 $curl = curl_init();
                curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://esbpre.adityabirlahealth.com/ABHICL_NB/Service1.svc/GHI",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 90,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS =>"{\"ClientCreation\":{\"Member_Customer_ID\":\"166081253511\",\"salutation\":\"MR\",\"firstName\":\"Ddfdsfemo\",\"middleName\":\"\",\"lastName\":\"Perfdfson\",\"dateofBirth\":\"02\\/20\\/1974\",\"gender\":\"M\",\"educationalQualification\":null,\"pinCode\":\"302019\",\"uidNo\":\"\",\"maritalStatus\":null,\"nationality\":\"Indian\",\"occupation\":\"O553\",\"primaryEmailID\":\"LHMUE.SHAH@ARVIND.IN\",\"contactMobileNo\":\"9992223232\",\"stdLandlineNo\":null,\"panNo\":null,\"passportNumber\":null,\"contactPerson\":null,\"annualIncome\":null,\"remarks\":null,\"startDate\":\"2022-08-18\",\"endDate\":null,\"IdProof\":\"Adhaar Card\",\"residenceProof\":null,\"ageProof\":null,\"others\":null,\"homeAddressLine1\":\"11-BAVA F INSTAL RN IRAJALAWRAP K   RVANPGNAURA\",\"homeAddressLine2\":\"\",\"homeAddressLine3\":null,\"homePinCode\":\"302019\",\"homeArea\":null,\"homeContactMobileNo\":null,\"homeContactMobileNo2\":null,\"homeSTDLandlineNo\":null,\"homeFaxNo\":null,\"sameAsHomeAddress\":\"1\",\"mailingAddressLine1\":null,\"mailingAddressLine2\":null,\"mailingAddressLine3\":null,\"mailingPinCode\":null,\"mailingArea\":null,\"mailingContactMobileNo\":null,\"mailingContactMobileNo2\":null,\"mailingSTDLandlineNo\":null,\"mailingSTDLandlineNo2\":null,\"mailingFaxNo\":null,\"bankAccountType\":null,\"bankAccountNo\":null,\"ifscCode\":null,\"GSTIN\":null,\"GSTRegistrationStatus\":\"Consumers\",\"IsEIAavailable\":\"0\",\"ApplyEIA\":\"0\",\"EIAAccountNo\":null,\"EIAWith\":\"0\",\"AccountType\":null,\"AddressProof\":null,\"DOBProof\":null,\"IdentityProof\":null},\"PolicyCreationRequest\":{\"Quotation_Number\":\"\",\"MasterPolicyNumber\":\"71-21-00117-00-00\",\"GroupID\":\"GRP001\",\"Product_Code\":\"4211\",\"SumInsured_Type\":null,\"Policy_Tanure\":\"1\",\"Member_Type_Code\":\"M209\",\"intermediaryCode\":\"2108233\",\"AutoRenewal\":\"0\",\"AutoDebit\":\"0\",\"intermediaryBranchCode\":\"10MHMUM01\",\"agentSignatureDate\":null,\"Customer_Signature_Date\":null,\"businessSourceChannel\":null,\"AssignPolicy\":\"0\",\"AssigneeName\":null,\"leadID\":\"300085308243\",\"Source_Name\":\"AXIS_D2C_COMBI_EW\",\"SPID\":\"\",\"TCN\":null,\"CRTNO\":null,\"RefCode1\":\"\",\"RefCode2\":\"002\",\"Employee_Number\":\"6251\",\"enumIsEmployeeDiscount\":null,\"QuoteDate\":null,\"IsPayment\":\"0\",\"PaymentMode\":null,\"PolicyproductComponents\":[{\"PlanCode\":\"4211\",\"SumInsured\":\"500000\",\"SchemeCode\":\"4112000003\"}]},\"MemObj\":{\"Member\":[{\"MemberNo\":1,\"Salutation\":\"Mr\",\"First_Name\":\"Ddfdsfemo\",\"Middle_Name\":null,\"Last_Name\":\"Perfdfson\",\"Gender\":\"M\",\"DateOfBirth\":\"02\\/20\\/1974\",\"Relation_Code\":\"R001\",\"Marital_Status\":null,\"height\":\"0.00\",\"weight\":\"0\",\"occupation\":\"O553\",\"PrimaryMember\":\"Y\",\"MemberproductComponents\":[{\"PlanCode\":\"4211\",\"MemberQuestionDetails\":[{\"QuestionCode\":null,\"Answer\":null,\"Remarks\":null}]}],\"MemberPED\":{\"PEDCode\":null,\"Remarks\":null},\"exactDiagnosis\":null,\"dateOfDiagnosis\":null,\"lastDateConsultation\":null,\"detailsOfTreatmentGiven\":null,\"doctorName\":null,\"hospitalName\":null,\"phoneNumberHosital\":null,\"Nominee_First_Name\":\"df\",\"Nominee_Last_Name\":\".\",\"Nominee_Contact_Number\":\"\",\"Nominee_Home_Address\":null,\"Nominee_Relationship_Code\":\"R010\"}]},\"ReceiptCreation\":{\"officeLocation\":\"\",\"modeOfEntry\":\"\",\"cdAcNo\":null,\"expiryDate\":null,\"payerType\":\"\",\"payerCode\":null,\"paymentBy\":\"\",\"paymentByName\":null,\"paymentByRelationship\":null,\"collectionAmount\":\"\",\"collectionRcvdDate\":null,\"collectionMode\":\"\",\"remarks\":null,\"instrumentNumber\":null,\"instrumentDate\":null,\"bankName\":null,\"branchName\":null,\"bankLocation\":null,\"micrNo\":null,\"chequeType\":null,\"ifscCode\":\"\",\"PaymentGatewayName\":\"\",\"TerminalID\":\"\",\"CardNo\":null}}\r\n",
                        CURLOPT_HTTPHEADER => array(

                                "Cache-Control: no-cache",
                                "Connection: keep-alive",
                               
                                "Content-Type: application/json",

                        ) ,
                ));

                $response = curl_exec($curl);

                //Monolog::saveLog("full_quote_reponse1", "I", json_encode($response));


                $err = curl_error($curl);

                curl_close($curl);
				echo $response;
				die;


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://esbpre.adityabirlahealth.com/ABHICL_NB/Service1.svc/GHI",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS =>"{\"ClientCreation\":{\"Member_Customer_ID\":\"166081253511\",\"salutation\":\"MR\",\"firstName\":\"Ddfdsfemo\",\"middleName\":\"\",\"lastName\":\"Perfdfson\",\"dateofBirth\":\"02\\/20\\/1974\",\"gender\":\"M\",\"educationalQualification\":null,\"pinCode\":\"302019\",\"uidNo\":\"\",\"maritalStatus\":null,\"nationality\":\"Indian\",\"occupation\":\"O553\",\"primaryEmailID\":\"LHMUE.SHAH@ARVIND.IN\",\"contactMobileNo\":\"9992223232\",\"stdLandlineNo\":null,\"panNo\":null,\"passportNumber\":null,\"contactPerson\":null,\"annualIncome\":null,\"remarks\":null,\"startDate\":\"2022-08-18\",\"endDate\":null,\"IdProof\":\"Adhaar Card\",\"residenceProof\":null,\"ageProof\":null,\"others\":null,\"homeAddressLine1\":\"11-BAVA F INSTAL RN IRAJALAWRAP K   RVANPGNAURA\",\"homeAddressLine2\":\"\",\"homeAddressLine3\":null,\"homePinCode\":\"302019\",\"homeArea\":null,\"homeContactMobileNo\":null,\"homeContactMobileNo2\":null,\"homeSTDLandlineNo\":null,\"homeFaxNo\":null,\"sameAsHomeAddress\":\"1\",\"mailingAddressLine1\":null,\"mailingAddressLine2\":null,\"mailingAddressLine3\":null,\"mailingPinCode\":null,\"mailingArea\":null,\"mailingContactMobileNo\":null,\"mailingContactMobileNo2\":null,\"mailingSTDLandlineNo\":null,\"mailingSTDLandlineNo2\":null,\"mailingFaxNo\":null,\"bankAccountType\":null,\"bankAccountNo\":null,\"ifscCode\":null,\"GSTIN\":null,\"GSTRegistrationStatus\":\"Consumers\",\"IsEIAavailable\":\"0\",\"ApplyEIA\":\"0\",\"EIAAccountNo\":null,\"EIAWith\":\"0\",\"AccountType\":null,\"AddressProof\":null,\"DOBProof\":null,\"IdentityProof\":null},\"PolicyCreationRequest\":{\"Quotation_Number\":\"\",\"MasterPolicyNumber\":\"71-21-00117-00-00\",\"GroupID\":\"GRP001\",\"Product_Code\":\"4211\",\"SumInsured_Type\":null,\"Policy_Tanure\":\"1\",\"Member_Type_Code\":\"M209\",\"intermediaryCode\":\"2108233\",\"AutoRenewal\":\"0\",\"AutoDebit\":\"0\",\"intermediaryBranchCode\":\"10MHMUM01\",\"agentSignatureDate\":null,\"Customer_Signature_Date\":null,\"businessSourceChannel\":null,\"AssignPolicy\":\"0\",\"AssigneeName\":null,\"leadID\":\"300085308243\",\"Source_Name\":\"AXIS_D2C_COMBI_EW\",\"SPID\":\"\",\"TCN\":null,\"CRTNO\":null,\"RefCode1\":\"\",\"RefCode2\":\"002\",\"Employee_Number\":\"6251\",\"enumIsEmployeeDiscount\":null,\"QuoteDate\":null,\"IsPayment\":\"0\",\"PaymentMode\":null,\"PolicyproductComponents\":[{\"PlanCode\":\"4211\",\"SumInsured\":\"500000\",\"SchemeCode\":\"4112000003\"}]},\"MemObj\":{\"Member\":[{\"MemberNo\":1,\"Salutation\":\"Mr\",\"First_Name\":\"Ddfdsfemo\",\"Middle_Name\":null,\"Last_Name\":\"Perfdfson\",\"Gender\":\"M\",\"DateOfBirth\":\"02\\/20\\/1974\",\"Relation_Code\":\"R001\",\"Marital_Status\":null,\"height\":\"0.00\",\"weight\":\"0\",\"occupation\":\"O553\",\"PrimaryMember\":\"Y\",\"MemberproductComponents\":[{\"PlanCode\":\"4211\",\"MemberQuestionDetails\":[{\"QuestionCode\":null,\"Answer\":null,\"Remarks\":null}]}],\"MemberPED\":{\"PEDCode\":null,\"Remarks\":null},\"exactDiagnosis\":null,\"dateOfDiagnosis\":null,\"lastDateConsultation\":null,\"detailsOfTreatmentGiven\":null,\"doctorName\":null,\"hospitalName\":null,\"phoneNumberHosital\":null,\"Nominee_First_Name\":\"df\",\"Nominee_Last_Name\":\".\",\"Nominee_Contact_Number\":\"\",\"Nominee_Home_Address\":null,\"Nominee_Relationship_Code\":\"R010\"}]},\"ReceiptCreation\":{\"officeLocation\":\"\",\"modeOfEntry\":\"\",\"cdAcNo\":null,\"expiryDate\":null,\"payerType\":\"\",\"payerCode\":null,\"paymentBy\":\"\",\"paymentByName\":null,\"paymentByRelationship\":null,\"collectionAmount\":\"\",\"collectionRcvdDate\":null,\"collectionMode\":\"\",\"remarks\":null,\"instrumentNumber\":null,\"instrumentDate\":null,\"bankName\":null,\"branchName\":null,\"bankLocation\":null,\"micrNo\":null,\"chequeType\":null,\"ifscCode\":\"\",\"PaymentGatewayName\":\"\",\"TerminalID\":\"\",\"CardNo\":null}}\r\n",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json"
  ),
));

$response = curl_exec($curl);

curl_close($curl);
//echo $response;
exit;

ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
$serverName = "10.21.204.81"; 
$uid = "ABHI_TC";   
$pwd = "birla@123";  
$databaseName = "ABHIHDFC_BRMS1"; 


$serverName = "10.21.204.81, 1443"; //serverName\instanceName, portNumber (default is 1433)
$connectionInfo = array( "Database"=>$databaseName, "UID"=>$uid, "PWD"=>$pwd,'CharacterSet' => 'UTF-8',);
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( $conn ) {
     echo "Connection established.<br />";
}else{
     echo "Connection could not be established.<br />";
     die("<PRE>". print_r(sqlsrv_errors(), true));
}
?>


?>
