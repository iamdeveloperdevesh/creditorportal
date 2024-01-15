<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payment_integration_m extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    $this->load->model("employee/home_m", "obj_home", true);
    }

  public function easy_pay_confirmation_HDFC($loan_acc_no,$subtype_id)
  {
	  $data_push = [];
      $message = '';
     // extract($this->input->post(NULL,true));
	
	  
		$update_data = $this
		  ->db
		  ->query('SELECT p.*,epd.policy_sub_type_id
			FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,
			employee_details AS ed
			where epd.product_name = e.id
			AND p.emp_id = ed.emp_id
			AND ed.loan_acc_no = "' .$loan_acc_no. '"
	
			AND epd.policy_detail_id = p.policy_detail_id');
		$update_data = $update_data->result_array();
	
    // payment confirmation hit count update
        foreach ($update_data as $update_payment)
        {
          $arr_new = ["count" => $update_payment['count'] + 1];
          
          $this->db->where('id', $update_payment['id']);
          $this->db->update("proposal", $arr_new);
        }       

    // GHI,GCI policy creation start
        foreach ($update_data as $update_payment)
        {
			$subtype_id = $update_payment['policy_sub_type_id'];
      // check first hit or not
          if($update_payment['count']== 0){
			  //if(true){
            
       // update proposal status - Payment Received
           $arr_new = ["status" => "Ready For Issuance"];
           $this->db->where('id', $update_payment['id']);
           $this->db->update("proposal", $arr_new);

            
      // check is policy already created?
            $query_check = $this->db->query("select * from proposal where id = '" . $update_payment['id'] . "' and status != 'Issued'")->row_array();
            if($query_check)
            {
             // GHI API call
             $api_response_tbl = $this->GHI_GCI_api_call($update_payment['emp_id'], $update_payment['policy_detail_id'], $update_payment['product_id']);

             if($api_response_tbl['status']=='error'){
              
               $data = array (
                'Status' => '0',
                'ErrorCode' => '0',
                'ErrorDescription' => $api_response_tbl['msg'],
              );

               $insert_arr = ["loan_acc_no" => $loan_acc_no ,"subtype_id" => $subtype_id];
               $this->db->insert("tbl_pos_rehit",$insert_arr);     

             }else{
             // update proposal status - Success  
              $arr = ["status" => "Issued"];
              $this->db->where('id', $update_payment['id']);
              $this->db->update("proposal", $arr);

              $data = array(
                'Status' => "Issued",
                'ErrorCode' => '',
                'ErrorDescription' => $api_response_tbl['msg'],
                'ClientID' => $api_response_tbl['ClientID'],
                'CertificateNumber' => $api_response_tbl['CertificateNumber'],
                'PolicyNumber' => $api_response_tbl['PolicyNumber'],
                'QuotationNumber' => $api_response_tbl['QuotationNumber'],
                'GrossPremium' => $api_response_tbl['GrossPremium'],
                'ProposalNumber' => $api_response_tbl['ProposalNumber'],
				'proposal_db_id' => $update_payment['id']
              );
			array_push($data_push,$data);
            }

          }else{
            $data = array(
              'Status' => '0',
              'ErrorCode' => '0',
              'ErrorDescription' => 'Failure',
            );
			array_push($data_push,$data);
          }
        
        

    }else{
     $data = array(
      'Status' => '0',
      'ErrorCode' => '0',
      'ErrorDescription' => 'Failure',
    );
	array_push($data_push,$data);
     
   }

 }

$message = json_encode($data_push);
return $message;
// return $this->output
// ->set_content_type('Content-Type: application/json')
// ->set_output($message);

}

public function GHI_GCI_api_call($emp_id, $policy_no, $parent_id)
  { 
	//extract($this->input->post(null, true));
	
	//Monolog::saveLog("get_all_data_api_post_field", "I", json_encode($this->input->post()));
	
	$data['customer_data'] = (array)$this->get_profile($emp_id);
	$data['member_data'] = (array)$this->get_all_member_data($emp_id, $policy_no);
	$data['nominee_data'] = (array)$this->get_all_nominee($emp_id);
	$data['proposal_data'] = (array)$this->getProposalData($emp_id, $policy_no);
	
	// print_r($data);
	// exit;

	$url = trim($data['proposal_data'][0]['api_url']);
	if($url == '')
	{
		return array(
		"status" => "error",
		"msg" => "Something Went Wrong"
		);
	}
	 if($data['customer_data']['occupation'] == 'Salaried')
	 {
		 $occupation = 'O002';
	 }
	 elseif($data['customer_data']['occupation'] == 'Self Employed')
	 {
		  $occupation = 'O003';
	 }
	 else
	 {
		 $occupation = '';
	 }
	$totalMembers = count($data['member_data']);
	$member = [];
   
	for ($i = 0;$i < $totalMembers;$i++)
	{
	 
	$member_PED = ["PEDCode" => null, "Remarks" => null];
	
	$date1 = $data['proposal_data'][0]['approved_date']; 
	$date2 = date('Y-m-d',strtotime($data['proposal_data'][0]['cheque_date'])); 
	  
	if ($date1 > $date2) 
		$start_date = $date1;
	else
		$start_date = $date2;
	
	$member[] = ["MemberNo" => $i + 1, "Salutation" => (($data['member_data'][$i]['gender'] == "Male") ? 'Mr' : (($data['member_data'][$i]['age'] >= 18) ? 'Mrs' : 'Ms')) , "First_Name" => $data['member_data'][$i]['firstname'], "Middle_Name" => null, "Last_Name" => $data['member_data'][$i]['lastname'], "Gender" => ($data['member_data'][$i]['gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['dob'])) , "Relation_Code" => trim($data['member_data'][$i]['relation_code']), "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => "O553", "PrimaryMember" => (($i == 0) ? "Y" : "N") , "MemberproductComponents" => [["PlanCode" => $data['proposal_data'][0]['plan_code'], "MemberQuestionDetails" => [["QuestionCode" => null, "Answer" => null, "Remarks" => null]]]], "MemberPED" => $member_PED, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $data['nominee_data'][0]['nominee_fname'], "Nominee_Last_Name" => $data['nominee_data'][0]['nominee_lname'], "Nominee_Contact_Number" => $data['nominee_data'][0]['nominee_contact'], "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data'][0]['relation_code']), ];

  }
//$n=1;
//$rand_num = date('Ym').str_pad($n + 1, 2,0, STR_PAD_LEFT);


$fqrequest = ["ClientCreation" => ["Member_Customer_ID" =>  $data['customer_data']['cust_id'] , "salutation" => $data['customer_data']['salutations'], "firstName" => $data['customer_data']['emp_firstname'], "middleName" => "", "lastName" => $data['customer_data']['emp_lastname'], "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['bdate'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['emp_pincode'], "uidNo" => $data['customer_data']['adhar'], "maritalStatus" => null, "nationality" => "Indian", "occupation" => $occupation, "primaryEmailID" => $data['customer_data']['email'], "contactMobileNo" => $data['customer_data']['mob_no'], "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => null, "remarks" => null, "startDate" => $start_date , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['comm_address'], "homeAddressLine2" => null, "homeAddressLine3" => null, "homePinCode" => $data['customer_data']['emp_pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => "", "MasterPolicyNumber" => $data['proposal_data'][0]['master_policy_no'], "GroupID" => $data['proposal_data'][0]['group_code'], "Product_Code" => $data['proposal_data'][0]['plan_code'], "intermediaryCode" => "5100003", "AutoRenewal" => 'Y', "intermediaryBranchCode" => "10MHMUM01", "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $data['customer_data']['loan_acc_no'], "Source_Name" => "HDFC_PL", "SPID" => $data['customer_data']['ref2'], "TCN" => null, "CRTNO" => null, "RefCode1" => $data['customer_data']['ref1'], "RefCode2" => $data['customer_data']['ref2'], "Employee_Number" => $data['proposal_data'][0]['emp_id'],"enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "0", "PaymentMode" => "Cheque", "PolicyproductComponents" => [["PlanCode" => $data['proposal_data'][0]['plan_code'], "SumInsured" => $data['proposal_data'][0]['sum_insured'], "SchemeCode" => "4112000003"]]], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "", "modeOfEntry" => "", "cdAcNo" => null, "expiryDate" => null, "payerType" => "", "payerCode" => null, "paymentBy" => "", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => "", "collectionRcvdDate" => null, "collectionMode" => "", "remarks" => null, "instrumentNumber" => null, "instrumentDate" => null, "bankName" => null, "branchName" => null, "bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => "", "PaymentGatewayName" => "", "TerminalID" => "", "CardNo" => null]];

// Monolog::saveLog("full_quote_request1", "I", json_encode($fqrequest));

// print_r($fqrequest);
// exit;

$request_arr = ["lead_id" => $data['customer_data']['loan_acc_no'], "req" => json_encode($fqrequest) , "type"=>"full_quote_request1_pos".$data['proposal_data'][0]['product_code']];
$this->db->insert("logs_docs",$request_arr);
$insert_id = $this->db->insert_id();

 // echo json_encode($request_arr);
 // exit;
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 60,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode($fqrequest) ,
  CURLOPT_HTTPHEADER => array(
	"Accept: */*",
	"Cache-Control: no-cache",
	"Connection: keep-alive",
	"Content-Length: " . strlen(json_encode($fqrequest)) ,
	"Content-Type: application/json",
	"Host: bizpre.adityabirlahealth.com"
  ) ,
));


$response = curl_exec($curl);

// Monolog::saveLog("full_quote_reponse1", "I", json_encode($response));
$request_arr = ["res" => json_encode($response)];
$this->db->where("id",$insert_id);
$this->db->update("logs_docs",$request_arr);

$err = curl_error($curl);

curl_close($curl);

if ($err)
{

  return array(
	"status" => "error",
	"msg" => $err
  );
}
else
{

  $new = simplexml_load_string($response);
  $con = json_encode($new);
  $newArr = json_decode($con, true);
  
  $errorObj = $newArr['errorObj'];
  //print_r($errorObj);

  if($errorObj['ErrorNumber'] == '00'){

	$policydetail = $newArr['policyDtls'];
	$premium = $newArr['premium'];
	$instru =rand();
	$fqrequest['PolicyCreationRequest']['Quotation_Number'] = $policydetail['QuotationNumber'];
	$fqrequest['PolicyCreationRequest']['IsPayment'] = "1";
	$fqrequest['ReceiptCreation'] = ["officeLocation" => "Mumbai", "modeOfEntry" => "Direct", "cdAcNo" => null, "expiryDate" => null, "payerType" => "Customer", "payerCode" => null, "paymentBy" => "Customer", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => $data['proposal_data'][0]['premium'], "collectionRcvdDate" => $date1, "collectionMode" => "Cheque", "remarks" => null, "instrumentNumber" => $instru, "instrumentDate" => date('Y-m-d H:i:s'), "bankName" => $data['proposal_data'][0]['bank_name'], "branchName" => $data['proposal_data'][0]['branch'], "bankLocation" => null, "micrNo" => null, "chequeType" => "Local", "ifscCode" => $data['proposal_data'][0]['ifscCode'], "PaymentGatewayName" => null, "TerminalID" => null, "CardNo" => null];
	
	// Monolog::saveLog("full_quote_request2", "I", json_encode($fqrequest));
	$request_arr = ["lead_id" => $data['customer_data']['loan_acc_no'], "req" => json_encode($fqrequest) , "type"=>"full_quote_request2_pos".$data['proposal_data'][0]['product_code']];
	$this->db->insert("logs_docs",$request_arr);
	$insert_id = $this->db->insert_id();

  }else{
	return array(
	  "status" => "error",
	  "msg" => $errorObj['ErrorMessage']
	);
  }
  
  $curl = curl_init();

  curl_setopt_array($curl, array(
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 60,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "POST",
	CURLOPT_POSTFIELDS => json_encode($fqrequest) ,
	CURLOPT_HTTPHEADER => array(
	  "Accept: */*",
	  "Cache-Control: no-cache",
	  "Connection: keep-alive",
	  "Content-Length: " . strlen(json_encode($fqrequest)) ,
	  "Content-Type: application/json",
	  "Host: bizpre.adityabirlahealth.com"
	) ,
  ));

  $response = curl_exec($curl);
  
  // Monolog::saveLog("full_quote_reponse2", "I", json_encode($response));
  $request_arr = ["res" => json_encode($response)];
  $this->db->where("id",$insert_id);
  $this->db->update("logs_docs",$request_arr);
  
  $err = curl_error($curl);

  curl_close($curl);

  if ($err)
  {
   return array(
	"status" => "error",
	"msg" =>  $err
  );
 }
 else
 {
  $new = simplexml_load_string($response);
  $con = json_encode($new);
  $newArr = json_decode($con, true);
  
		$errorObj = $newArr['errorObj'];
		$proposals_numbers = $data['proposal_data'][0]['proposal_no'];
		$data1 = [];

		if($errorObj['ErrorNumber']=='00'){
		  
		  $data1['ClientID'] = $newArr['policyDtls']['ClientID'];
		  $data1['CertificateNumber'] = $newArr['policyDtls']['CertificateNumber'];
		  $data1['QuotationNumber'] = $newArr['policyDtls']['QuotationNumber'];
		  $data1['ProposalNumber'] = $newArr['policyDtls']['ProposalNumber'];
		  $data1['PolicyNumber'] = $newArr['policyDtls']['PolicyNumber'];
		  $data1['GrossPremium'] = $premium['GrossPremium'];
		  $data1['status'] = 'Success';
		  $data1['msg'] = $errorObj['ErrorMessage'];

		  $api_insert = array(
			"client_id" => $newArr['policyDtls']['ClientID'],
			"certificate_number" => $newArr['policyDtls']['CertificateNumber'],
			"quotation_no" => $newArr['policyDtls']['QuotationNumber'],
			"proposal_no" => $newArr['policyDtls']['ProposalNumber'],
			"policy_no" => $newArr['policyDtls']['PolicyNumber'],
			"gross_premium" => $premium['GrossPremium'],
			"status" => $errorObj['ErrorMessage'],
			"start_date" => ($newArr['policyDtls']['startDate']) ? $newArr['policyDtls']['startDate'] : "",
			"end_date" => $newArr['policyDtls']['EndDate'],
			"created_date" => date('Y-m-d H:i:s'),
			"proposal_no_lead"=>$proposals_numbers,
			"CustomerID" => $newArr['policyDtls']['MemberCustomerID'],
			"PolicyStatus" => $newArr['policyDtls']['PolicyStatus'],
			"MemberCustomerID" => $newArr['policyDtls']['MemberCustomerID'],
			"type_login" => "POS"

		  );


		  // Monolog::saveLog("api_insert", "I", json_encode($api_insert));    
		  $request_arr = ["lead_id" => $data['customer_data']['loan_acc_no'], "req" => json_encode($api_insert) , "type"=>"api_insert_pos"];
		  $this->db->insert("logs_docs",$request_arr);
		  
		  $this
		  ->db
		  ->insert('api_proposal_response', $api_insert);

		}else{

		  $data1 = array(
			'status'=>'error',
			"msg" => $errorObj['errormessage']
		  );
		}
		return $data1;
	  }
	}

  }

//*************************************************************************************************
	
	
    function get_profile($emp_id)
    {
    
	return $this->db->query("select e.*,m.salutation as salutations from employee_details as e left join master_salutation as m ON e.salutation = m.s_id where e.emp_id='$emp_id'")->row();
        //return $this->db->select('*')->where(["emp_id" => $emp_id])->get("employee_details")->row();

    }
	
	function getProposalData($emp_id,$policy_no)
          {
           
		   $query = $this
            ->db
            ->query('SELECT p.*,epd.policy_no,mgc.group_code,e.master_policy_no,e.product_name,pd.txndate,pd.payment_type,pd.cheque_date,e.policy_subtype_id,e.plan_code,e.api_url,e.product_code
             FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,proposal_member AS pm,
             family_relation AS fr, employee_details AS ed,master_group_code AS mgc,payment_details as pd
             where epd.product_name = e.id
             AND p.emp_id = "' . $emp_id . '"
             AND p.policy_detail_id = "' . $policy_no . '"
             AND epd.policy_detail_id = p.policy_detail_id
             AND p.id = pm.proposal_id
             AND fr.family_id = 0
             AND e.policy_subtype_id = epd.policy_sub_type_id
             AND pm.familyConstruct = mgc.family_construct
             AND p.sum_insured = mgc.si_group
			 AND e.product_code = mgc.product_code 
             AND p.id = pd.proposal_id
             AND pm.family_relation_id = fr.family_relation_id
             AND fr.emp_id = ed.emp_id group by p.id');

   
            if ($query)
            {
              $query = $query->result_array();
            }
            else
            {
              $query = [];
            }
            return $query;
          }

    function get_all_member_data($emp_id, $policy_detail_id)
    {
        $response = $this->db
        ->query('SELECT epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
            epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type","R001" as relation_code
            FROM 
            employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_details AS ed
            WHERE epd.policy_detail_id = epm.policy_detail_id
            AND epm.family_relation_id = fr.family_relation_id
            AND fr.family_id = 0
            AND fr.emp_id = ed.emp_id
            AND ed.emp_id = ' . $emp_id . '
            AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' UNION all SELECT epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
            epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type",mfr.relation_code
            FROM 
            employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
            master_family_relation AS mfr
            WHERE epd.policy_detail_id = epm.policy_detail_id
            AND epm.family_relation_id = fr.family_relation_id

            AND fr.family_id = efd.family_id 
            AND efd.fr_id = mfr.fr_id
            AND fr.emp_id = ' . $emp_id . '
            AND `epd`.`policy_detail_id` = ' . $policy_detail_id)->result_array();
			
			if($response[0]['suminsured_type'] == 'family_construct_age'){
				$change_premium = false;

				foreach($response as $value){
					$age[] = $value['age'];
					
				}
				
				$check = $this->db->select("*")
                ->from("family_construct_age_wise_si")
                ->where("sum_insured", $response[0]['policy_mem_sum_insured'])
                ->where("family_type", $response[0]['familyConstruct'])
                ->where("policy_detail_id", $policy_detail_id)
                ->get()
                ->result_array();
				 $max_age = max($age);
				
				foreach($check as $value){
					
					$min_max_age = explode("-",$value['age_group']);
					if($max_age >= $min_max_age[0] && $max_age <= $min_max_age[1]){
						$premium = $value['PremiumServiceTax'];
					}
				}
				
				foreach($response as $value1){
					$this->db->where('policy_member_id', $value1['policy_member_id']);
					$this->db->update('employee_policy_member', ['policy_mem_sum_premium' => $premium]);
					if($this->db->affected_rows()){
						$change_premium = true;
					}
				}
				
				if($change_premium){
					
					$response[0]["message"] = "Premium has been changed as per your inputs to ".$premium;
					$response[0]["new_premium"] = $premium;
				}
				
				
			}
		
        return $response;
    }
	
	function get_all_nominee($emp_id)
    {
        $response = $this->db->select('*,mfr.relation_code')
        ->from('member_policy_nominee AS mpn,master_family_relation as mfr')
        ->where('mpn.emp_id', $emp_id)
        ->where('mpn.fr_id = mfr.fr_id')
        ->where('mpn.status', 'active')
        ->get()->result_array();
        if ($response) {
            return $response;
        }
    }
	
}
class XML2Array
        {

          private static $xml = null;
          private static $encoding = 'UTF-8';

    /**
     * Convert an XML to Array
     *
     * @param string $node_name - name of the root node to be converted
     * @param array  $arr       - aray to be converterd
     *
     * @return DOMDocument
     */
    public static function &createArray($input_xml)
    {
      $xml = self::getXMLRoot();
      if (is_string($input_xml))
      {
        $parsed = $xml->loadXML($input_xml);
        if (!$parsed)
        {
          throw new Exception('[XML2Array] Error parsing the XML string.');
        }
      }
      else
      {
        if (get_class($input_xml) != 'DOMDocument')
        {
          throw new Exception('[XML2Array] The input XML object should be of type: DOMDocument.');
        }
        $xml = self::$xml = $input_xml;
      }
      $array[$xml
        ->documentElement->tagName] = self::convert($xml->documentElement);
        self::$xml = null; // clear the xml node in the class for 2nd time use.
        return $array;
      }

      private static function getXMLRoot()
      {
       if (empty(self::$xml))
       {
        self::init();
      }

      return self::$xml;
    }

    /**
     * Initialize the root XML node [optional]
     *
     * @param $version
     * @param $encoding
     * @param $format_output
     */
    public static function init($version = '1.0', $encoding = 'UTF-8', $format_output = true)
    {
      self::$xml = new DOMDocument($version, $encoding);
      self::$xml->formatOutput = $format_output;
      self::$encoding = $encoding;
    }

    /*
     * Get the root XML node, if there isn't one, create it.
    */

    /**
     * Convert an Array to XML
     *
     * @param mixed $node - XML as a string or as an object of DOMDocument
     *
     * @return mixed
     */
    private static function &convert($node)
    {
      $output = array();

      switch ($node->nodeType)
      {
        case XML_CDATA_SECTION_NODE:
        $output['@cdata'] = trim($node->textContent);
        break;

        case XML_TEXT_NODE:
        $output = trim($node->textContent);
        break;

        case XML_ELEMENT_NODE:

                // for each child node, call the covert function recursively
        for ($i = 0, $m = $node
          ->childNodes->length;$i < $m;$i++)
        {
          $child = $node
          ->childNodes
          ->item($i);
          $v = self::convert($child);
          if (isset($child->tagName))
          {
            $t = $child->tagName;

                        // assume more nodes of same kind are coming
            if (!isset($output[$t]))
            {
              $output[$t] = array();
            }
            $output[$t][] = $v;
          }
          else
          {
                        //check if it is not an empty text node
            if ($v !== '')
            {
              $output = $v;
            }
          }
        }

        if (is_array($output))
        {
                    // if only one node of its kind, assign it directly instead if array($value);
          foreach ($output as $t => $v)
          {
            if (is_array($v) && count($v) == 1)
            {
              $output[$t] = $v[0];
            }
          }
          if (empty($output))
          {
                        //for empty nodes
            $output = '';
          }
        }

                // loop through the attributes and collect them
        if ($node
          ->attributes
          ->length)
        {
          $a = array();
          foreach ($node->attributes as $attrName => $attrNode)
          {
            $a[$attrName] = (string)$attrNode->value;
          }
                    // if its an leaf node, store the value in @value instead of directly storing it.
          if (!is_array($output))
          {
            $output = array(
              '@value' => $output
            );
          }
          $output['@attributes'] = $a;
        }
        break;
      }

      return $output;
    }
  }

