<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payment_integration_retail extends CI_Model
{
    function __construct()
    {
        parent::__construct();
		$this->db= $this->load->database('axis_retail',TRUE);
		$this->load->model("Logs_m");
    }
	
	function emandate_enquiry_HB_call_m()
	{
		echo "if PaymentMode change from PO to PP then start this cron for emandate enquiry and change created date";exit;
		
		$query = $this->db->query("select ed.lead_id,ed.product_id,emd.status from proposal as p,payment_details as pd,employee_details as ed left join emandate_data as emd on emd.lead_id = ed.lead_id where ed.emp_id = p.emp_id and p.id = pd.proposal_id and pd.payment_status = 'No Error' and emd.status NOT IN('Success','Fail') and p.status IN('Payment Received','Success') and date(p.created_date) > '2020-11-17' group by p.emp_id order by ed.emp_id desc limit 20")->result_array();
			
		if($query)
		{
			foreach($query as $val){
				$this->real_pg_check($val['lead_id']);
			}
			
		}
	}

	
	function policy_creation_call($CRM_Lead_Id){
			$message = '';
			$update_data = $this
			  ->db
			  ->query('SELECT p.id,p.emp_id,p.policy_detail_id,p.product_id,p.status,p.count
				FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,
				employee_details AS ed
				where epd.product_name = e.id
				AND p.emp_id = ed.emp_id
				AND ed.lead_id = "' .$CRM_Lead_Id. '"
				AND e.policy_subtype_id = 1
				AND epd.policy_detail_id = p.policy_detail_id');
			$update_payment = $update_data->row_array();
			  
		// GHI policy creation start
		   
			 if($update_payment['status']!='Success'){
				 
			//update payment confirmation hit count
				$this->db->query("UPDATE proposal SET count = count + 1 WHERE emp_id ='".$update_payment['emp_id']."'");
			
			// check first hit or not
			  if($update_payment['count'] < 3){
				
		   // update proposal status - Payment Received
			   $arr_new = ["status" => "Payment Received"];
			   $this->db->where('id', $update_payment['id']);
			   $this->db->update("proposal", $arr_new);

				
		  // check is policy already created?
				$query_check = $this->db->query("select id from proposal where id = '" . $update_payment['id'] . "' and status != 'Success'")->row_array();
				if($query_check)
				{
				 // GHI API call
				 $api_response_tbl = $this->GHI_GCI_api_call($update_payment['emp_id'], $update_payment['policy_detail_id']);

				 if($api_response_tbl['status']=='error'){
				  
				   $data = array (
					'Status' => 'error',
					'ErrorCode' => '0',
					'ErrorDescription' => $api_response_tbl['msg'],
				  );

				 }else{
				 // update proposal status - Success  
				  $arr = ["status" => "Success"];
				  $this->db->where('id', $update_payment['id']);
				  $this->db->update("proposal", $arr);

				  $data = array(
					'Status' => "Success",
					'ErrorCode' => '1',
					'ErrorDescription' => $api_response_tbl['msg'],

				  );
				} 

			  }else{
				$data = array(
				  'Status' => 'error',
				  'ErrorCode' => '0',
				  'ErrorDescription' => 'Failure',
				);
			  }
			
				

			}else{
			 $data = array(
			  'Status' => 'error',
			  'ErrorCode' => '0',
			  'ErrorDescription' => 'Failure',
			);
			 
		   }
		   
		  }else{
			  $data = array(
			  'Status' => 'Success',
			  'ErrorCode' => '2',
			  'ErrorDescription' => 'Already genarate',
			);
		  }

		return $data;
	}
	
	function coi_url_call_m($emp_id){

		$data_coi = $this->db->query("select cg.url,ed.lead_id from employee_details as ed,coi_genarate_url as cg where ed.lead_id = cg.lead_id AND ed.emp_id = '$emp_id'")->row_array();
		
		$result = ["status"=>"error","url"=>""];
		
		if($data_coi['url'] == "false" || $data_coi['url'] == ""){
			
			$db_req = $this->db->query("select req from logs_docs where lead_id = '".$data_coi['lead_id']."' AND type = 'coi_genarate'")->row_array();
			
			$db_url =$this->db->query("select coi_url from product_master_with_subtype where product_code = 'R05'")->row_array();
			
			if($db_req){
			
				$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => $db_url['coi_url'],
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 60,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS => $db_req['req'],
				  CURLOPT_HTTPHEADER => array(
					"Accept: */*",
					"Cache-Control: no-cache",
					"Connection: keep-alive",
					"Content-Length: " . strlen($db_req['req']) ,
					"Content-Type: application/json",
					"Host: bizpre.adityabirlahealth.com"
				  ) ,
				));

				$response = curl_exec($curl);
				
				$err = curl_error($curl);
				
				$request_arr = ["lead_id" => $data_coi['lead_id'], "req" => json_encode($db_req['req']),"res" => json_encode($response) , "type"=>"coi_genarate_onclick", "product_id"=> "R05"];
				$this->db->insert("logs_docs",$request_arr);
				
				  if ($err)
					{

					  $result = array(
						"status" => "error",
						"url" => $err
					  );
					}
					else
					{
				  
					$res_url = json_decode($response,true);
					  $coi_url = $res_url['COIUrl'];
				
						 if($coi_url){
							 
						  $request_arr = ["url" => $coi_url];
							$this->db->where("lead_id",$data_coi['lead_id']);
							$this->db->update("coi_genarate_url",$request_arr);

							$result = array(
								"status" => "success",
								"url" => $coi_url
							  );

						}
					}	
			}
		}else{
			$result = array(
							"status" => "success",
							"url" => $data_coi['url']
						  );
		}
		
		return $result;
	}
	
	 function real_pg_check($lead_id){
			$check_pg = false;
				
			$query = $this->db->query("SELECT ed.lead_id,ed.emp_id,product_id FROM employee_details as ed where ed.lead_id ='" . $lead_id."'")->row_array();
			
			if($query){
				
					$CKS_data = "AX|AXDCGRP|LEADID|".$query['lead_id']."|razorpay";
					
					$CKS_value = hash('SHA512', $CKS_data);
			
					$url = "https://pg_uat.adityabirlahealth.com/PGMANDATE/service/api/enquirePayment";
					$fqrequest = array(
							"signature"=> $CKS_value,
							"Source"=> "AX",
							"Vertical"=> "AXDCGRP",
							"SearchMode"=> "LEADID",
							"UniqueIdentifierValue"=> $query['lead_id'],
							"PaymentMode"=> "PP"
							);
							
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
							"Content-Type: application/json"
						) ,
					));


					$response = curl_exec($curl);
					$err = curl_error($curl);

					curl_close($curl);
					
					$result = json_decode($response, true);
					
					// print_r($response); 
					// echo json_encode($fqrequest); exit;
					
					if ($err) {
					  $request_arr = ["lead_id" => $query['lead_id'],"req" => json_encode($fqrequest), "res" => json_encode($err) ,"product_id"=> $query['product_id'],"type"=>"pg_real_fail"];
					  
						$dataArray['tablename'] = 'logs_docs'; 
						$dataArray['data'] = $request_arr; 
						$this->Logs_m->insertLogs($dataArray);
					  
					}else{
						
						if($result && $result['PaymentStatus'] == 'PR'){
					
							$TxStatus = "success";
							$TxMsg = "No Error";
											
							$request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest) ,"res" => json_encode($result),"product_id"=> $query['product_id'], "type"=>"pg_real_success"];
							
							$dataArray['tablename'] = 'logs_docs'; 
							$dataArray['data'] = $request_arr; 
							$this->Logs_m->insertLogs($dataArray);
							
							// $date = new DateTime($result['txnDateTime']);
							// $txt_date = $date->format('m/d/Y g:i:s A'); 
							
							$arr = ["payment_status" => $TxMsg,"premium_amount" => $result['amount'],"payment_type" => $result['paymentMode'],"txndate" => $result['txnDateTime'],"TxRefNo" => $result['TxRefNo'],"TxStatus"=>$TxStatus,"json_quote_payment"=>json_encode($result)];
					
							$proposal_ids = $this->db->query("select id as proposal_id from proposal where emp_id='".$query['emp_id']."'")->result_array();
							
							foreach ($proposal_ids as $query_val)
							{
								$this->db->where("proposal_id",$query_val['proposal_id']);
								$this->db->where('TxStatus != ','success');
								$this->db->update("payment_details",$arr);	
							}
							
							if($result['Registrationmode']){
					
								$query_emandate = $this->db->query("select * from emandate_data where lead_id=".$query['lead_id'])->row_array();
								
								if($result['EMandateStatus'] == 'MS'){
									$mandate_status = 'Success';
									//HB emandate call
									//$this->emandate_HB_call($query['emp_id']);
								}elseif($result['EMandateStatus'] == 'MI'){
									$mandate_status = 'Emandate Pending';
								}elseif($result['EMandateStatus'] == 'MR'){
									$mandate_status = 'Emandate Received';
								}elseif($result['EMandateStatus'] == ''){
									$mandate_status = 'Emandate Pending';
								}else{
									$mandate_status = 'Fail';
								}
							
								if($query_emandate > 0){
									
									$arr = ["TRN" => $result['EMandateRefno'],"status_desc" => $result['EMandateStatusDesc'],"status" => $mandate_status,"mandate_date" => date('Y-m-d h:i:s',strtotime($result['EMandateDate'])),"Registrationmode" => $result['Registrationmode'], "EMandateFailureReason" => $result['EMandateFailureReason'],"MandateLink" => $result['MandateLink']];
									
									$this->db->where("lead_id",$query['lead_id']);
									$this->db->update("emandate_data",$arr);
								}else{
									
									$arr = ["lead_id" => $query['lead_id'],"TRN" => $result['EMandateRefno'],"status_desc" => $result['EMandateStatusDesc'],"status" => $mandate_status,"mandate_date" => date('Y-m-d h:i:s',strtotime($result['EMandateDate'])),"Registrationmode" => $result['Registrationmode'], "EMandateFailureReason" => $result['EMandateFailureReason'],"MandateLink" => $result['MandateLink']];
									
									$this->db->insert("emandate_data", $arr);
								}
								
								if($mandate_status == 'Success'){
									$this->send_message($query['lead_id'],'success');
								}
								
								if($mandate_status == 'Fail'){
									$this->send_message($query['lead_id'],'fail');
								}
								
								if($result['paymentMode'] == 'PP' && ($result['Registrationmode'] == 'SAD' || $result['Registrationmode'] == 'EMI')){
									$this->send_message($query['lead_id'],'SAD_EMI_one');
									$this->send_message($query['lead_id'],'SAD_EMI_two');
								}
								
							}
			
							$check_pg = true;
							
						}else{
							
						   $request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest) ,"res" => json_encode($result), "product_id"=> $query['product_id'],"type"=>"pg_real_fail"];
						   
						   $dataArray['tablename'] = 'logs_docs'; 
							$dataArray['data'] = $request_arr; 
							$this->Logs_m->insertLogs($dataArray);
					
						}
					
					}
			}

			return $check_pg;
	}
	
  function emandate_HB_call($emp_id)
	{ 	
		$query_check = $this->db->query("select ed.lead_id,ed.product_id,ed.acc_no,apr.certificate_number,apr.proposal_no,apr.pr_api_id from employee_details as ed,proposal as p,api_proposal_response as apr,emandate_data as emd where ed.emp_id = p.emp_id and p.proposal_no = apr.proposal_no_lead and p.status in('Success','Payment Received') and apr.mandate_send_status = 0 and emd.lead_id = ed.lead_id and emd.status = 'Success' and ed.emp_id = '$emp_id' group by p.emp_id")->result_array();

		if($query_check){ 
		
		foreach ($query_check as $val)
		{
			
			//BIZ HB call start
			
			$url = 'https://bizpre.adityabirlahealth.com/ABHICL_HealthCoreXML/Service1.svc/AddEmendateDetails';
			
			$req_arr = [
					  'EmendateDeatails' => 
					  [
						'EmendateList' => 
						[ 
						  [
							'Bank_Name' => 'Axis Bank',
							'Debit_Account_Number' => empty($val['acc_no'])?'':$val['acc_no'],
							'Mandate_Start_Date' => '10-02-2020',
							'Mandate_End_Date' => '11-09-2020',
							'Account_Type' => 'Saving',
							'Bank_Branch_Name' => null,
							'MICR' => '123',
							'IFSC' => null,
							'Frequency' => "Annual",
							'Policy_Number' => $val['certificate_number'],
						  ],
						],
					  ],
					];
					
				   $curl = curl_init();

					curl_setopt_array($curl, array(
						CURLOPT_URL => $url,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => "",
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 60,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => "POST",
						CURLOPT_POSTFIELDS => json_encode($req_arr) ,
						CURLOPT_HTTPHEADER => array(
							"Accept: */*",
							"Cache-Control: no-cache",
							"Connection: keep-alive",
							"Content-Length: " . strlen(json_encode($req_arr)) ,
							"Content-Type: application/json",
							"Host: bizpre.adityabirlahealth.com"
						) ,
					));

				   $response = curl_exec($curl);
				   
				   curl_close($curl);
				   
				   // if($response){
					   // $response = json_decode($response);
				   // }
				   
				   $update_arr = ["mandate_send_status" => 1];
						
					$this->db->where("pr_api_id",$val['pr_api_id']);
					$this->db->update("api_proposal_response",$update_arr);
				   
				   $request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($req_arr),"res" => json_encode($response),"product_id"=> $val['product_id'], "type"=>"emandate_HB_post"];
				
					$dataArray['tablename'] = 'logs_docs'; 
					$dataArray['data'] = $request_arr; 
					$this->Logs_m->insertLogs($dataArray);
					//BIZ HB call end
					
		}
		
		}
	}
	
 function send_message($lead_id,$type)
{
		$query_check = $this->db->query("SELECT ed.emp_id,ed.lead_id,ed.email,ed.mob_no,ed.customer_name,mpst.product_code,mpst.click_pss_url,mpst.product_name,ee.EMandateFailureReason,ee.Registrationmode,ee.MandateLink,sum(p.premium) as total_amt FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,emandate_data as ee WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.lead_id = ee.lead_id AND ed.lead_id=".$lead_id)->row_array();
		
		if($query_check){
			
			$senderID = 1;
			$full_name = $query_check['customer_name'];
			if(strlen($full_name) > 30){
				$full_name = substr($full_name, 0, 30);
			}

			$alertMode = 2;

			$AlertV1 = $full_name;
			$AlertV2 = (($query_check['total_amt'] * 1.5) + $query_check['total_amt']);
			$AlertV3 = $query_check['product_name'];
			$AlertV4 = '';
			$AlertV5 = '';
			
			$alertID = '';
			
			if($type == 'success'){
				
				if($query_check['Registrationmode'] == 'EMNB' || $query_check['Registrationmode'] == 'EMDC'){
					$alertID = 'A1407';
				}
				
				if($query_check['Registrationmode'] == 'SICC' || $query_check['Registrationmode'] == 'SIEMI'){
					$alertID = 'A1408';
				}
				
			}
			
			if($type == 'fail'){
				
				if($query_check['Registrationmode'] == 'EMNB' || $query_check['Registrationmode'] == 'EMDC'){
					$alertID = 'A1409';
				}
				
				if($query_check['Registrationmode'] == 'SICC' || $query_check['Registrationmode'] == 'SIEMI'){
					$alertID = 'A1411';
				}
				
				$AlertV4 = $query_check['EMandateFailureReason'];
				//$AlertV5 = 'https://www.adityabirlacapital.com/healthinsurance/#!/our-branches';
				
				$AlertV5 = 'klr.pw/A0Wir';//branch locator bittly
                if(strlen($AlertV5) > 30){
                    $alertMode = 1;
                }
			}
			
			if($type == 'SAD_EMI_one'){
				$alertID = 'A1405';
				$AlertV2 = $query_check['product_name'];
				$AlertV3 = $query_check['MandateLink'];
				if(strlen($query_check['MandateLink']) > 30){
                    $alertMode = 1;
                }
			}
			
			if($type == 'SAD_EMI_two'){
				$alertID = 'A1406';
				$AlertV1 = $query_check['MandateLink'];;
				if(strlen($query_check['MandateLink']) > 30){
                    $alertMode = 1;
                }
			}
			
			if(empty($alertID)){
				exit;
			}
				
			
			$parameters =[
				"RTdetails" => [
			   
					"PolicyID" => '',
					"AppNo" => 'HD100017934',
					"alertID" => $alertID,
					"channel_ID" => 'D2C Application',
					"Req_Id" => 1,
					"field1" => '',
					"field2" => '',
					"field3" => '',
					"Alert_Mode" => $alertMode,
					"Alertdata" => 
						[
							"mobileno" => substr(trim($query_check['mob_no']), -10),
							"emailId" => $query_check['email'],
							"AlertV1" => $AlertV1,
							"AlertV2" => $AlertV2,
							"AlertV3" => $AlertV3,
							"AlertV4" => $AlertV4,
							"AlertV5" => $AlertV5,
						
						]

					]

				];
				 $parameters = json_encode($parameters);
				 $curl = curl_init();
				
				curl_setopt_array($curl, array(
				  CURLOPT_URL => $query_check['click_pss_url'],
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS => $parameters,
				  CURLOPT_HTTPHEADER => array(
					"cache-control: no-cache",
					"content-type: application/json",
				   
				  ),
				));

			$response = curl_exec($curl);
			
			curl_close($curl);
			
			$request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($parameters),"res" => json_encode($response) ,"product_id"=> $query_check['product_code'], "type"=>"sms_logs_emandate_".$type];
			
			$dataArray['tablename'] = 'logs_docs'; 
			$dataArray['data'] = $request_arr; 
			$this->Logs_m->insertLogs($dataArray);
	
	  }
}
	
  public function get_quote_data($emp_id,$policy_detail_id)
  { 
  
	$data['customer_data'] = (array)$this->get_profile($emp_id);
	$data['member_data'] = (array)$this->get_all_member_data($emp_id, $policy_detail_id);
	$data['nominee_data'] = (array)$this->get_all_nominee($emp_id);
	$data['proposal_data'] = (array)$this->getProposalData($emp_id, $policy_detail_id);
	
	$url = trim($data['proposal_data']['api_url']);
	if($url == '')
	{
		return array(
		"status" => "error",
		"msg" => "Something Went Wrong"
		);
	}
	 
	$totalMembers = count($data['member_data']);
	$member = [];
	
	$explode_name = explode(" ", trim($data['customer_data']['customer_name']),2);
  
    $explode_name_nominee = explode(" ", trim($data['nominee_data']['nominee_fname']),2);
	
	
   
	for ($i = 0;$i < $totalMembers;$i++)
	{
		if($data['member_data'][$i]['fr_id'] == 2 || $data['member_data'][$i]['fr_id'] ==3){
			if($data['member_data'][$i]['fr_id'] == 2 && $data['member_data'][$i]['gender'] != "Male"){
				$data['member_data'][$i]['relation_code'] = "R004";
			}elseif($data['member_data'][$i]['fr_id'] == 3 && $data['member_data'][$i]['gender'] != "Female"){
				$data['member_data'][$i]['relation_code'] = "R003";
			}
		}
		
	
		$query = $this
		->db
		->query('SELECT md.sub_member_code,ed.emp_id,ed.fr_id FROM employee_disease AS ed,master_disease AS md WHERE ed.disease_id = md.id AND ed.value = 1 AND ed.emp_id = "'.$emp_id.'"  AND ed.fr_id = "'.$data['member_data'][$i]['fr_id'].'" ')->result_array();
		
		$abc = [];
		if(!empty($query))
		{
			foreach ($query as $key => $value) {
				
				$abc[] = ["PEDCode" => $value['sub_member_code'], "Remarks" => null];
				
			}
		}else{
			
			$abc[] = ["PEDCode" => null, "Remarks" => null];
		}
		
	$explode_name_member = explode(" ", trim($data['member_data'][$i]['firstname']),2);
	 
	
	$member[] = ["MemberNo" => $i + 1, "Salutation" => (($data['member_data'][$i]['gender'] == "Male") ? 'Mr' : (($data['member_data'][$i]['age'] >= 18) ? 'Mrs' : 'Ms')) , "First_Name" => $explode_name_member[0], "Middle_Name" => null, "Last_Name" => !empty($explode_name_member[1])?$explode_name_member[1]:'.', "Gender" => ($data['member_data'][$i]['gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['dob'])) , "Relation_Code" => trim($data['member_data'][$i]['relation_code']), "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => "O553", "PrimaryMember" => (($i == 0) ? "Y" : "N") , "MemberproductComponents" => [["PlanCode" => $data['proposal_data']['plan_code'], "MemberQuestionDetails" => [["QuestionCode" => null, "Answer" => null, "Remarks" => null]]]], "MemberPED" => $abc, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $explode_name_nominee[0], "Nominee_Last_Name" => !empty($explode_name_nominee[1])?$explode_name_nominee[1]:'.', "Nominee_Contact_Number" => $data['nominee_data']['nominee_contact'], "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data']['relation_code']), ];

  }
  

$fqrequest = ["ClientCreation" => ["Member_Customer_ID" =>  $data['customer_data']['cust_id'] , "salutation" => $data['customer_data']['salutation'], "firstName" => $explode_name[0], "middleName" => "", "lastName" => !empty($explode_name[1])?$explode_name[1]:'.', "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['bdate'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['emp_pincode'], "uidNo" => $data['customer_data']['adhar'], "maritalStatus" => null, "nationality" => "Indian", "occupation" => "O553", "primaryEmailID" => $data['customer_data']['email'], "contactMobileNo" => substr(trim($data['customer_data']['mob_no']), -10), "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => null, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['comm_address'], "homeAddressLine2" => null, "homeAddressLine3" => null, "homePinCode" => $data['customer_data']['emp_pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => "", "MasterPolicyNumber" => $data['proposal_data']['master_policy_no'], "GroupID" => $data['proposal_data']['group_code'], "Product_Code" => $data['proposal_data']['plan_code'], "intermediaryCode" => $data['proposal_data']['IMDCode'], "AutoRenewal" => '0', "AutoDebit" => '0', "intermediaryBranchCode" => "10MHMUM01", "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $data['customer_data']['lead_id'], "Source_Name" => $data['proposal_data']['SourceSystemName_api'], "SPID" => $data['customer_data']['ref2'], "TCN" => null, "CRTNO" => null, "RefCode1" => $data['customer_data']['ref1'], "RefCode2" => $data['customer_data']['branch_sol_id'], "Employee_Number" => $data['proposal_data']['emp_id'],"enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "0", "PaymentMode" => "", "PolicyproductComponents" => [["PlanCode" => $data['proposal_data']['plan_code'], "SumInsured" => $data['proposal_data']['sum_insured'], "SchemeCode" => "4112000003"]]], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "", "modeOfEntry" => "", "cdAcNo" => null, "expiryDate" => null, "payerType" => "", "payerCode" => null, "paymentBy" => "", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => "", "collectionRcvdDate" => null, "collectionMode" => "", "remarks" => null, "instrumentNumber" => null, "instrumentDate" => null, "bankName" => null, "branchName" => null, "bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => "", "PaymentGatewayName" => "", "TerminalID" => "", "CardNo" => null]];


$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($fqrequest) , "type"=>"full_quote_request1_".$data['proposal_data']['policy_subtype_id'], "product_id"=> "R05"];
$this->db->insert("logs_docs",$request_arr);
$insert_id = $this->db->insert_id();

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 90,
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
}else{

  $new = simplexml_load_string($response);
  $con = json_encode($new);
  $newArr = json_decode($con, true);
  $errorObj = $newArr['errorObj'];
	
	  if($errorObj['ErrorNumber'] == '00'){
		  
		  $policydetail = $newArr['policyDtls'];
		  
		  $arr = ["emp_id" => $emp_id, "QuotationNumber" => $policydetail['QuotationNumber'], "PolicyNumber" => $policydetail['PolicyNumber'],"status"=>"success"];
		  
		  $query = $this
			->db
			->query("select * from ghi_quick_quote_response where emp_id='$emp_id'")->row_array();
			
			if($query > 0){
				
				$this->db->where("emp_id",$emp_id);
				$this->db->update("ghi_quick_quote_response",$arr);
			}else{
				
				$this->db->insert("ghi_quick_quote_response", $arr);
			}
		 
		  
			return array(
        			"status" => "Success",
        			"msg" => $policydetail['QuotationNumber']
        		);
	  }else{
		  
		  $query = $this
			->db
			->query("select * from ghi_quick_quote_response where emp_id='$emp_id'")->row_array();
			
			if($query > 0){
				
				$arr = ["count" => $query['count']+1,"status"=>"error"];
				
				$this->db->where("emp_id",$emp_id);
				$this->db->update("ghi_quick_quote_response",$arr);
			}else{
				
				$arr = ["emp_id" => $emp_id,"status"=>"error"];
				
				$this->db->insert("ghi_quick_quote_response", $arr);
			}
			
			///------- @author : Guru --------------------------//
			$request_failure_arr = ["lead_id" => $data['customer_data']['lead_id'],/* "req" => json_encode($fqrequest) ,*/ "product_id" => "R05", "res" => $errorObj['ErrorMessage'], "type" => "failure_reason_".$data['proposal_data']['policy_subtype_id']];
			$dataArray['tablename'] = 'logs_docs';
			$dataArray['data'] = $request_failure_arr;
			$this
				->Logs_m
				->insertLogs($dataArray);
		  
			return array(
				"status" => "error",
				"msg" => $errorObj['ErrorMessage']
			);
      }
	  
}


}


public function GHI_GCI_api_call($emp_id, $policy_no,$cron_policy_check = '')
  { 
	/*check for payment done or not and prevent multiple policy create at same time*/
	$extra_check_data = $this
		->db
		->query("select pd.payment_status,pd.TxRefNo,pd.TxStatus,ed.is_policy_issue_initiated from proposal as p,employee_details as ed,payment_details as pd  where ed.emp_id = p.emp_id and p.id = pd.proposal_id and ed.emp_id ='" . $emp_id . "'")->row_array();
	/*if ($extra_check_data['payment_status'] == 'No Error' && !empty($extra_check_data['TxRefNo']) && $extra_check_data['TxStatus'] == 'success')
	{*/
	if (($extra_check_data['payment_status'] == 'No Error' && !empty($extra_check_data['TxRefNo']) && $extra_check_data['TxStatus'] == 'success') && $extra_check_data['is_policy_issue_initiated'] == 0)
	{

		$extra_arr_update = ["is_policy_issue_initiated" => 1];
		$this
			->db
			->where("emp_id", $emp_id);
		$this
			->db
			->update("employee_details", $extra_arr_update);
				
	$data['customer_data'] = (array)$this->get_profile($emp_id);
	$data['member_data'] = (array)$this->get_all_member_data($emp_id, $policy_no);
	$data['nominee_data'] = (array)$this->get_all_nominee($emp_id);
	$data['proposal_data'] = (array)$this->getProposalData($emp_id, $policy_no);
	
	$query_quote = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id'")->row_array();
	
	$transaction_date = explode(" ",$data['proposal_data']['txndate']);
	$trans_date = date("Y-m-d", strtotime($transaction_date[0]));
	
	if($query_quote > 0){
			
	$url = trim($data['proposal_data']['api_url']);
	
	if($url == '')
	{
		return array(
		"status" => "error",
		"msg" => "Something Went Wrong"
		);
	}
	 
	$totalMembers = count($data['member_data']);
	$member = [];
	
	$explode_name = explode(" ", trim($data['customer_data']['customer_name']),2);
  
    $explode_name_nominee = explode(" ", trim($data['nominee_data']['nominee_fname']),2);
   
	for ($i = 0;$i < $totalMembers;$i++)
	{
		if($data['member_data'][$i]['fr_id'] == 2 || $data['member_data'][$i]['fr_id'] ==3){
			if($data['member_data'][$i]['fr_id'] == 2 && $data['member_data'][$i]['gender'] != "Male"){
				$data['member_data'][$i]['fr_name'] = "Dependent Daughter";
				$data['member_data'][$i]['relation_code'] = "R004";
			}elseif($data['member_data'][$i]['fr_id'] == 3 && $data['member_data'][$i]['gender'] != "Female"){
				$data['member_data'][$i]['fr_name'] = "Dependent Son";
				$data['member_data'][$i]['relation_code'] = "R003";
			}
		}
	
		$query = $this
		->db
		->query('SELECT md.sub_member_code,ed.emp_id,ed.fr_id,md.disease FROM employee_disease AS ed,master_disease AS md WHERE ed.disease_id = md.id AND ed.value = 1 AND ed.emp_id = "'.$emp_id.'"  AND ed.fr_id = "'.$data['member_data'][$i]['fr_id'].'" ')->result_array();
		
		$abc = [];
		
		if(!empty($query))
		{
			foreach ($query as $key => $value) {
				
				if($value['disease'] == 'Diabetes'){
					$value['disease'] = "Diabetes Mellitus";
				}
				
				$abc[] = ["PEDCode" => $value['sub_member_code'], "Remarks" => null];
				$abcd[$i][] = $value['disease'];
				
			}
		}else{
			
			$abc[] = ["PEDCode" => null, "Remarks" => null];
			$abcd[$i] = [];
		}
	
    $explode_name_member = explode(" ", trim($data['member_data'][$i]['firstname']),2);
	 
	
	$member[] = ["MemberNo" => $i + 1, "Salutation" => (($data['member_data'][$i]['gender'] == "Male") ? 'Mr' : (($data['member_data'][$i]['age'] >= 18) ? 'Mrs' : 'Ms')) , "First_Name" => $explode_name_member[0], "Middle_Name" => null, "Last_Name" => !empty($explode_name_member[1])?$explode_name_member[1]:'.', "Gender" => ($data['member_data'][$i]['gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['dob'])) , "Relation_Code" => trim($data['member_data'][$i]['relation_code']), "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => "O553", "PrimaryMember" => (($i == 0) ? "Y" : "N") , "MemberproductComponents" => [["PlanCode" => $data['proposal_data']['plan_code'], "MemberQuestionDetails" => [["QuestionCode" => null, "Answer" => null, "Remarks" => null]]]], "MemberPED" => $abc, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $explode_name_nominee[0], "Nominee_Last_Name" => !empty($explode_name_nominee[1])?$explode_name_nominee[1]:'.', "Nominee_Contact_Number" => $data['nominee_data']['nominee_contact'], "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data']['relation_code']), ];

  }
    

$fqrequest = ["ClientCreation" => ["Member_Customer_ID" =>  $data['customer_data']['cust_id'] , "salutation" => $data['customer_data']['salutation'], "firstName" => $explode_name[0], "middleName" => "", "lastName" => !empty($explode_name[1])?$explode_name[1]:'.', "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['bdate'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['emp_pincode'], "uidNo" => $data['customer_data']['adhar'], "maritalStatus" => null, "nationality" => "Indian", "occupation" => "O553", "primaryEmailID" => $data['customer_data']['email'], "contactMobileNo" => substr(trim($data['customer_data']['mob_no']), -10), "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => null, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address'], "homeAddressLine2" => null, "homeAddressLine3" => null, "homePinCode" => $data['customer_data']['emp_pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => $query_quote['QuotationNumber'], "MasterPolicyNumber" => $data['proposal_data']['master_policy_no'], "GroupID" => $data['proposal_data']['group_code'], "Product_Code" => $data['proposal_data']['plan_code'], "intermediaryCode" => $data['proposal_data']['IMDCode'], "AutoRenewal" => '0', "AutoDebit" => '0', "intermediaryBranchCode" => "10MHMUM01", "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $data['customer_data']['lead_id'], "Source_Name" => $data['proposal_data']['SourceSystemName_api'], "SPID" => $data['customer_data']['ref2'], "TCN" => null, "CRTNO" => null, "RefCode1" => $data['customer_data']['ref1'], "RefCode2" => $data['customer_data']['branch_sol_id'], "Employee_Number" => $data['proposal_data']['emp_id'],"enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "1", "PaymentMode" => "", "PolicyproductComponents" => [["PlanCode" => $data['proposal_data']['plan_code'], "SumInsured" => $data['proposal_data']['sum_insured'], "SchemeCode" => "4112000003"]]], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "Mumbai", "modeOfEntry" => "Direct", "cdAcNo" => null, "expiryDate" => null, "payerType" => "Customer", "payerCode" => null, "paymentBy" => "Customer", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => $data['proposal_data']['premium'], "collectionRcvdDate" => $trans_date, "collectionMode" => "online", "remarks" => null, "instrumentNumber" => $data['proposal_data']['TxRefNo'], "instrumentDate" => $trans_date, "bankName" => null, "branchName" => null, "bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => null, "PaymentGatewayName" => null, "TerminalID" => "EuxJCz8cZV9V63", "CardNo" => null]];

/*"TerminalID" =>"76010098"*/

$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($fqrequest) , "type"=>"full_quote_request2_".$data['proposal_data']['policy_subtype_id'], "product_id"=> "R05"];
$this->db->insert("logs_docs",$request_arr);
$insert_id = $this->db->insert_id();

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 90,
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

$request_arr = ["res" => json_encode($response)];
$this->db->where("id",$insert_id);
$this->db->update("logs_docs",$request_arr);

$err = curl_error($curl);

curl_close($curl);

$extra_arr_update = ["is_policy_issue_initiated" => 0];
$this
	->db
	->where("emp_id", $emp_id);
$this
	->db
	->update("employee_details", $extra_arr_update);

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
		$return_data = [];

		if($errorObj['ErrorNumber']=='00' || ($errorObj['ErrorNumber']=='302' && $data['proposal_data']['master_policy_no'] == $newArr['policyDtls']['PolicyNumber'])){
		  
		  $return_data['status'] = 'Success';
		  $return_data['msg'] = $errorObj['ErrorMessage'];
		  
		  $create_policy_type = 0;
			if($cron_policy_check){
				$create_policy_type = 1;
			}

		  $api_insert = array(
			"emp_id" => $emp_id,
			"proposal_id" => $data['proposal_data']['id'],
			"client_id" => $newArr['policyDtls']['ClientID'],
			"certificate_number" => $newArr['policyDtls']['CertificateNumber'],
			"quotation_no" => $newArr['policyDtls']['QuotationNumber'],
			"proposal_no" => $newArr['policyDtls']['ProposalNumber'],
			"policy_no" => $newArr['policyDtls']['PolicyNumber'],
			"gross_premium" => empty($newArr['premium']['GrossPremium'])?'':$newArr['premium']['GrossPremium'],
			"status" => "Success",
			//"status" => $errorObj['ErrorMessage'],
			"start_date" => $newArr['policyDtls']['startDate'],
			"end_date" => $newArr['policyDtls']['EndDate'],
			"created_date" => date('Y-m-d H:i:s'),
			"proposal_no_lead"=>$data['proposal_data']['proposal_no'],
			"CustomerID" => $newArr['policyDtls']['MemberCustomerID'],
			"PolicyStatus" => $newArr['policyDtls']['PolicyStatus'],
			"MemberCustomerID" => $newArr['policyDtls']['MemberCustomerID'],
			"letter_url" => $newArr['policyDtls']['LetterURL'],
			"create_policy_type" => $create_policy_type,
			"ReceiptNumber" => $newArr['receiptObj']['ReceiptNumber'],
			//"COI_url" => $newArr['policyDtls']['COIUrl'],

		  );

		  $request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($api_insert) , "type"=>"api_insert_retail", "product_id"=> "R05"];
		  $this->db->insert("logs_docs",$request_arr);
		  
		  $this->db->insert('api_proposal_response', $api_insert);
		  
		  //HB emandate call
		  //$this->emandate_HB_call($emp_id);
		  
		  //Generate COI call
		  $this->generate_coi($data,$newArr,$abcd);

		}else{
			
		///------- @author : Guru --------------------------//
		$request_failure_arr = ["lead_id" => $data['customer_data']['lead_id'], /* "req" => json_encode($fqrequest) ,*/ "product_id" => "R05", "res" => $errorObj['ErrorMessage'], "type" => "failure_reason_".$data['proposal_data']['policy_subtype_id']];
		$dataArray['tablename'] = 'logs_docs';
		$dataArray['data'] = $request_failure_arr;
		$this
			->Logs_m
			->insertLogs($dataArray);

		  $return_data = array(
			'status'=>'error',
			"msg" => $errorObj['ErrorMessage']
		  );
		}
		return $return_data;
	 
	}
	
	}else{
		
		$extra_arr_update = ["is_policy_issue_initiated" => 0];
		$this
			->db
			->where("emp_id", $emp_id);
		$this
			->db
			->update("employee_details", $extra_arr_update);
		
		return $return_data = array(
			'status'=>'error',
			"msg" => "Quote error"
		  );
	}
	}
  }
  
  function generate_coi($data,$policy_data,$mem_disease){
	  //get coi member id data
		  $coi_url = trim($data['proposal_data']['coi_url']);
		  $coi_uid_url = trim($data['proposal_data']['coi_uid_url']);
	
		  $coi_uid_request = ["COINumber" => $policy_data['policyDtls']['CertificateNumber']];
		  
		  $curl_new = curl_init();

			curl_setopt_array($curl_new, array(
			  CURLOPT_URL => $coi_uid_url,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 60,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => json_encode($coi_uid_request) ,
			  CURLOPT_HTTPHEADER => array(
				"Accept: */*",
				"Cache-Control: no-cache",
				"Connection: keep-alive",
				"Content-Length: " . strlen(json_encode($coi_uid_request)) ,
				"Content-Type: application/json",
				"Host: bizpre.adityabirlahealth.com"
			  ) ,
			));

		  $response_new = curl_exec($curl_new);
		  
		  $request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($coi_uid_request),"res" => json_encode($response_new) , "type"=>"coi_uid_genarate", "product_id"=> "R05"];
		  $this->db->insert("logs_docs",$request_arr);
		  
		  $result_data = json_decode($response_new,true);
		  $result_data = $result_data['clsReturn'];
		  
		  if(!empty($result_data)){
			  
			  foreach ($result_data as $t => $v)
			  {
				$uid[$t] = $v['strMemberCode'];
			  }

		  $totalMembers = count($data['member_data']);
		  $exclude_amt = $data['proposal_data']['net_premium'];
		  $include_amt = $data['proposal_data']['premium'];
		  $other_amt = ($include_amt - $exclude_amt)/2;
		  
		  $start_date = explode(" ",$policy_data['policyDtls']['startDate']);
		  $start_date = date("d/m/Y", strtotime($start_date[0]));
		  
		  $end_date = explode(" ",$policy_data['policyDtls']['EndDate']);
		  $end_date = date("d/m/Y", strtotime($end_date[0]));
		  $end_date_new = date("d-m-Y", strtotime($end_date));
		 
		 $transaction_date = explode(" ",$data['proposal_data']['txndate']);
		 $trans_date = date("Y-m-d", strtotime($transaction_date[0]));
		  
		   $coi_request = [
			   "ClientCode" => "AXIS_D2C", 
			   "ProdCode" => "ABHI010", 
			   "NoOfMembers" => $totalMembers, 
			   "MPN" => $data['proposal_data']['master_policy_no'], 
			   "MPHN" => "M/s Axis Bank Limited", 
			   "MPStartDt" => "26/02/2020", 
			   "MPEndDt" => "25/02/2021", 
			   "PHMobNo" => $data['customer_data']['mob_no']." ".$data['customer_data']['email'], 
			   "PHEmail" => $data['customer_data']['email'], 
			   "PHContactDtls" => "", 
			   "PrmPaymntMode" => "Yearly", 
			   "InstNum" => $data['proposal_data']['TxRefNo'], 
			   "InstDt" => $trans_date, 
			   "BankName" => "Axis Bank Limited", 
			   "PIO" => "Aditya Birla Health Insurance Company Limited,10th Floor,R-Tech Park,Nirlon Compound,Next To HUB Mall,Off Western Express Highway,Goregaon East,Mumbai-400063", 
			   "PSO" => "Aditya Birla Health Insurance Company Limited, 7th floor, C building, Modi Business Centre, Kasarvadavali, Mumbai, Thane West 400 615", 
			   "PHN" => $data['customer_data']['customer_name'], 
			   "PN" => "", 
			   "PHAdd" => "", 
			   "PPN" => "Group Activ Health", 
			   "ProdName" => $policy_data['policyDtls']['ProductName'], 
			   "PlanName" => "", 
			   "PStartDt" => $start_date, 
			   "PEndDt" => $end_date, 
			   "IMDCode" => $data['proposal_data']['IMDCode'], 
			   "IMDName" => "", 
			   "IMDEmail" => "", 
			   "IMDCDtls" => "", 
			   "CN" => $policy_data['policyDtls']['CertificateNumber'], 
			   "PHNameAndAdd" => $data['customer_data']['customer_name']." ".$data['customer_data']['address']." ".$data['customer_data']['emp_city']." ".$data['customer_data']['emp_state']." ".$data['customer_data']['emp_pincode'], 
			   "PHCommAdd" => "", 
			   "UID" => $data['customer_data']['lead_id'], 
			   "CvrType" => "", 
			   "MID1" => $uid[0], 
			   "IPName1" => $data['member_data'][0]['firstname'], 
			   "IPDOB1" => $data['member_data'][0]['dob'], 
			   "IPGender1" => ($data['member_data'][0]['gender'] == "Male") ? "M" : "F", 
			   "IPNomName1" => $data['nominee_data']['nominee_fname'], 
			   "IPNomRel1" => "Self", 
			   "SumInsured" => $data['proposal_data']['sum_insured'], 
			   "IPRel1" => ucfirst($data['nominee_data']['fr_name']), 
			   "MID2" => isset($uid[1])?$uid[1]:"", 
			   "IPName2" => isset($data['member_data'][1]['firstname'])?$data['member_data'][1]['firstname']:"", 
			   "IPDOB2" => isset($data['member_data'][1]['dob']) ? $data['member_data'][1]['dob']:"", 
			   "IPGender2" => isset($data['member_data'][1]['gender']) ? (($data['member_data'][1]['gender'] == "Male") ? "M" : "F") :"", 
			   "IPNomName2" => "", 
			   "IPNomRel2" => isset($data['member_data'][1]['fr_name']) ? $data['member_data'][1]['fr_name'] :"", 
			   "MID3" => isset($uid[2])?$uid[2]:"", 
			   "IPName3" => isset($data['member_data'][2]['firstname'])?$data['member_data'][2]['firstname']:"", 
			   "IPDOB3" => isset($data['member_data'][2]['dob']) ? $data['member_data'][2]['dob']:"", 
			   "IPGender3" => isset($data['member_data'][2]['gender']) ? (($data['member_data'][2]['gender'] == "Male") ? "M" : "F") :"", 
			   "IPNomName3" => "", 
			   "IPNomRel3" => isset($data['member_data'][2]['fr_name']) ? $data['member_data'][2]['fr_name'] :"", 
			   "MID4" => isset($uid[3])?$uid[3]:"", 
			   "IPName4" => isset($data['member_data'][3]['firstname'])?$data['member_data'][3]['firstname']:"", 
			   "IPDOB4" => isset($data['member_data'][3]['dob']) ? $data['member_data'][3]['dob']:"", 
			   "IPGender4" => isset($data['member_data'][3]['gender']) ? (($data['member_data'][3]['gender'] == "Male") ? "M" : "F") :"", 
			   "IPNomName4" => "", 
			   "IPNomRel4" => isset($data['member_data'][3]['fr_name']) ? $data['member_data'][3]['fr_name'] :"", 
			   "InitCNAndStartDate" => "", 
			   "PayoutBasis" => "", 
			   "Options" => isset($mem_disease[3])?implode(",", $mem_disease[3]):"", 
			   "ApplicabilityOpt1" => "Applicable", 
			   "SumInsrdLmtOpt1" => "", 
			   "PEDDtls1" => isset($mem_disease[0])?implode(",", $mem_disease[0]):"", 
			   "PEDDtls2" => isset($mem_disease[1])?implode(",", $mem_disease[1]):"", 
			   "NP" => $exclude_amt, 
			   "CGST"=> "",
				"SGST"=> "",
				"IGST"=>($other_amt*2),
				"GP"=> $include_amt,
			   "ClmAssistance" => "Static", 
			   "UWNotesAndImpTrmsConditns" => "Static", 
			   "COINo1" => $policy_data['policyDtls']['CertificateNumber'], 
			   "CvrgType1" => "", 
			   "PrmAmt1" => "", 
			   "PaymntDt1" => "", 
			   "FY1" => isset($mem_disease[2])?implode(",", $mem_disease[2]):"", 
			   "YrWiseProportionatePrmAmt1" => "", 
			   "StampDutyAmt" => "", 
			   "StampDuty" => "", 
			   "PolIssDt" => $start_date, 
			   "Place" => "Mumbai", 
			   "SumInsured1" => $data['proposal_data']['sum_insured'], 
			   "SumInsured2" => "", 
			   "SumInsured3" => "", 
			   "SumInsured4" => "" 
			]; 
		  
		  
		  $curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => $coi_url,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 60,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => json_encode($coi_request) ,
			  CURLOPT_HTTPHEADER => array(
				"Accept: */*",
				"Cache-Control: no-cache",
				"Connection: keep-alive",
				"Content-Length: " . strlen(json_encode($coi_request)) ,
				"Content-Type: application/json",
				"Host: bizpre.adityabirlahealth.com"
			  ) ,
			));

			$response = curl_exec($curl);
			
			$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($coi_request),"res" => json_encode($response) , "type"=>"coi_genarate", "product_id"=> "R05"];
			$this->db->insert("logs_docs",$request_arr);
			
			  if ($err)
				{

				  return array(
					"status" => "error",
					"msg" => $err
				  );
				}
				else
				{
			  
			  $res_url = json_decode($response,true);
              $coi_url = $res_url['COIUrl'];

			  $request_arr = ["lead_id" => $data['customer_data']['lead_id'], "url" => $coi_url];
					$this->db->insert("coi_genarate_url",$request_arr);
					
				$click_url =$this->db->query("select click_pss_url from product_master_with_subtype where product_code = 'R05'")->row_array();
			
				$url_req = "https://api-alerts.kaleyra.com/v4/?api_key=A6ef09c1692b5f41a8cd5bf4ac5e22695&method=txtly.create&url=".urlencode($coi_url)."&title=xyz";
				
				$curl = curl_init();
			
				curl_setopt_array($curl, array(
				  CURLOPT_URL => $url_req,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "GET",
				  //CURLOPT_POSTFIELDS => $parameters,
				  CURLOPT_HTTPHEADER => array(
					"cache-control: no-cache",
					"content-type: application/json",
				   
				  ),
				));

				$result = curl_exec($curl);
				
				curl_close($curl);
				
				$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($url_req),"res" => json_encode($result) , "type"=>"bitly_url_coi", "product_id"=> "R05"];
				$this->db->insert("logs_docs",$request_arr);
				
				$data_url = json_decode($result,true);
				
				if($data_url['txtly'] == ''){
					$data_url['txtly'] = $coi_url;
				}
				

				$senderID = 1;

				$full_n =$data['customer_data']['customer_name'];
				if (strlen($full_n) > 30) {
					$full_n = substr($full_n, 0, 30);
				}

				$AlertV1 = $full_n;
				$AlertV2 = 'Group Activ Health';
				$AlertV3 = $start_date;
				$AlertV4 = $data['customer_data']['customer_name'];
				$AlertV5 = $policy_data['policyDtls']['CertificateNumber'];
				$AlertV6 = $start_date;
				$AlertV7 = $end_date;
				$AlertV8 = date('d/m/Y', strtotime($end_date_new. ' + 1 day'));
				$AlertV9 = $data_url['txtly'];
				$AlertV10 = 'Annual';
				$AlertV11 = 'Health Insurance Policy';
				$AlertV12 = $data['proposal_data']['sum_insured'];
			
				// Added By Shardul For Validating ISNRI on 20-Aug-2020
				$dataArray['emp_id'] = !empty($data['customer_data']['emp_id']) ? $data['customer_data']['emp_id'] : "";
				$dataArray['isNri'] = !empty($data['customer_data']['ISNRI']) ? $data['customer_data']['ISNRI'] : "";
				$dataArray['product_id'] = !empty($data['customer_data']['product_id']) ? $data['customer_data']['product_id'] : "";
				$alertMode = helper_validate_is_nri($dataArray);

				if ($data['txtly'] == $coi_url) {
					if (strlen($coi_url) > 30) {
						$alertMode = 1;
					}
				}
				
				$parameters =[
				"RTdetails" => [
			   
					"PolicyID" => '',
					"AppNo" => 'DC100025765',
					"alertID" => 'A735',
					"channel_ID" => 'D2C Application',
					"Req_Id" => 1,
					"field1" => '',
					"field2" => '',
					"field3" => '',
					// "Alert_Mode" => 2,
					"Alert_Mode" => $alertMode,
					"Alertdata" => 
						[
							"mobileno" => substr(trim($data['customer_data']['mob_no']), -10),
							"emailId" => $data['customer_data']['email'],
							"AlertV1" => $AlertV1,
							"AlertV2" => $AlertV2,
							"AlertV3" => $AlertV3,
							"AlertV4" => $AlertV4,
							"AlertV5" => $AlertV5,
							"AlertV6" => $AlertV6,
							"AlertV7" => $AlertV7,
							"AlertV8" => $AlertV8,
							"AlertV9" => $AlertV9,
							"AlertV10" => $AlertV10,
							"AlertV11" => $AlertV11,
							"AlertV12" => $AlertV12,
							"AlertV13" => '',
							
						]

					]

				];
				 $parameters = json_encode($parameters);
				 $curl = curl_init();
				
				curl_setopt_array($curl, array(
				  CURLOPT_URL => $click_url['click_pss_url'],
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS => $parameters,
				  CURLOPT_HTTPHEADER => array(
					"cache-control: no-cache",
					"content-type: application/json",
				   
				  ),
				));

				$response = curl_exec($curl);
				
				curl_close($curl);
				
				$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($parameters),"res" => json_encode($response) , "type"=>"sms_logs_coi", "product_id"=> "R05"];
				$this->db->insert("logs_docs",$request_arr);
			
				
				}
				
				}
  }

//*************************************************************************************************
	
	
    function get_profile($emp_id)
    {
    
	return $this->db->query("select e.* from employee_details as e left join master_salutation as m ON e.salutation = m.s_id where e.emp_id='$emp_id'")->row();

    }
	
	function getProposalData($emp_id,$policy_no)
          {
		   $query = $this
            ->db
            ->query('SELECT p.created_date,p.id,p.IMDCode,p.proposal_no,p.emp_id,p.sum_insured,p.premium,epd.policy_no,mgc.group_code,e.master_policy_no,e.product_name,pd.txndate,pd.payment_type,e.policy_subtype_id,e.plan_code,e.api_url,e.product_code,e.SourceSystemName_api,pd.TxRefNo,e.coi_url,fca.premium AS net_premium,e.coi_uid_url
             FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,proposal_member AS pm,
             family_relation AS fr, employee_details AS ed,master_group_code AS mgc,payment_details as pd,family_construct_age_wise_si as fca
             where epd.product_name = e.id
			 AND p.sum_insured = fca.sum_insured
			 AND pm.familyConstruct = fca.family_type
			 AND p.policy_detail_id = fca.policy_detail_id
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
              $query = $query->row_array();
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
		->query('SELECT epm.family_relation_id,e.policy_subtype_id,epd.policy_detail_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,"Self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type","R001" as relation_code,e.plan_code FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS epm,family_relation AS fr,employee_details AS ed WHERE epd.product_name = e.id AND e.policy_subtype_id = epd.policy_sub_type_id AND epd.policy_detail_id = epm.policy_detail_id AND epm.family_relation_id = fr.family_relation_id AND fr.family_id = 0 AND fr.emp_id = ed.emp_id AND ed.emp_id = ' . $emp_id . '
		AND `epd`.`policy_detail_id` = '.$policy_detail_id.' UNION all SELECT epm.family_relation_id,e.policy_subtype_id,epd.policy_detail_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
		epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type",mfr.relation_code,e.plan_code
		FROM 
		product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS epm,family_relation AS fr,employee_family_details AS efd,
		master_family_relation AS mfr
		WHERE epd.product_name = e.id AND e.policy_subtype_id = epd.policy_sub_type_id AND epd.policy_detail_id = epm.policy_detail_id
		AND epm.family_relation_id = fr.family_relation_id
		AND fr.family_id = efd.family_id 
		AND efd.fr_id = mfr.fr_id AND `epd`.`policy_detail_id` = '.$policy_detail_id.'
		AND fr.emp_id = ' . $emp_id)->result_array();
			
		
        return $response;
    }
	
	function get_all_nominee($emp_id)
    {
        $response = $this->db->select('*,mfr.relation_code,mfr.nominee_type as fr_name')
        ->from('member_policy_nominee AS mpn,master_nominee as mfr')
        ->where('mpn.emp_id', $emp_id)
        ->where('mpn.fr_id = mfr.nominee_id')
        ->get()->row_array();
        if ($response) {
            return $response;
        }
    }
	
	
	
}


