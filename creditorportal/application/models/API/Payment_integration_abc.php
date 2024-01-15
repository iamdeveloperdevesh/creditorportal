<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payment_integration_abc extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->db = $this
            ->load
            ->database('axis_retail', true);
        $this
            ->load
            ->model("Logs_m");
    }

    function coi_download_m($certificate_no = '')
    {
        if (empty($certificate_no))
        {
            $coi =  $this->input->post('certificate_no');
            $coi = str_replace("order_","",$coi);
            //echo $coi;exit;
            $certificate_no = $this
                ->input
                ->post('certificate_no');
            $certificate_no= encrypt_decrypt_password($coi,'D');

        }

        $data = $this
            ->db
            ->query("select ed.customer_name,ed.lead_id,ed.product_id,apr.certificate_number,apr.COI_url,apr.pr_api_id from employee_details as ed,proposal as p,api_proposal_response as apr where ed.emp_id = p.emp_id and p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id and p.status in('Success','Payment Received') and apr.certificate_number = '$certificate_no' ")->row_array();

        $result = ["customer_name" => $data['customer_name'], "status" => "error", "url" => ""];

        if ($data['COI_url'] == "false" || $data['COI_url'] == "")
        {

            //$db_url =$this->db->query("select coi_url from product_master_with_subtype where product_code = 'R05'")->row_array();
            $url = 'https://bizpre.adityabirlahealth.com/ABHICL_OmniDocs/Service1.svc/searchRequest';

            $req_arr = [
                            "SearchRequest" => [        
                                                    [
                                                        "CategoryID" => "", 
                                                        "DocumentID" => "", 
                                                        "ReferenceID" => "", 
                                                        "FileName" => "", 
                                                        "Description" => "", 
                                                        "DataClassParam" => [
                                                            [
                                                                "DocSearchParamId" => "23", 
                                                                // "Value" => "GHI-20-2000458",
                                                                "Value"=>$certificate_no,
                                                            ]
                                                        ]
                                                    ]
                                                ],
                            "SourceSystemName" => "Axis", 
                            "SearchOperator" => "Or"
                        ];

            $request = json_encode($req_arr);

            $res = $this->curl_call($url, $request);
            $response = json_decode($res, true);

            $request_arr = ["lead_id" => $data['lead_id'], "product_id" => $data['product_id'], "req" => $request , "res" => json_encode($response) , "type" => "coi_genarate_req1"];

            $dataArray['tablename'] = 'logs_docs';
            $dataArray['data'] = $request_arr;
            $this
                ->Logs_m
                ->insertLogs($dataArray);

            if ($response['SearchResponse'])
            {
                $ress = $response['SearchResponse'];

                if ($ress[0]['Error'][0]['Code'] == 0)
                {
                    $check_data = $ress[0]['OmniDocImageIndex'];
                }

            }

            if (!empty($check_data))
            {

                $url = 'https://bizpre.adityabirlahealth.com/ABHICL_OmniDocs/Service1.svc/downloadRequest';

                $req_arr = ['DownloadRequest' => [['GlobalId' => "", 'OmniDocImageIndex' => $ress[0]['OmniDocImageIndex'], 'FileName' => $ress[0]['FileName'], ], ], 'Identifier' => 'ByteArray', 'SourceSystemName' => 'Axis', ];

                $request = json_encode($req_arr);

                $res = $this->curl_call($url, $request);
                $response = json_decode($res, true);

                $request_arr = ["lead_id" => $data['lead_id'], "product_id" => $data['product_id'], "req" => json_encode($request) , "res" => json_encode($response['DownloadResponse'][0]['Error'][0]['Code']) , "type" => "coi_genarate_req2"];

                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this
                    ->Logs_m
                    ->insertLogs($dataArray);

                if ($response)
                {
                    $ress = $response['DownloadResponse'];

                    if ($ress[0]['Error'][0]['Code'] == 0)
                    {

                        $decoded = base64_decode($ress[0]['ByteArray']);
                        $file = $ress[0]['FileName'];
                        $path = APPPATH . '/resources/' . $file;
                        file_put_contents($path, $decoded);
                        // move_uploaded_file($a, $path);
                        $update_arr = ["COI_url" => '/resources/' . $file];

                        $this
                            ->db
                            ->where("pr_api_id", $data['pr_api_id']);
                        $this
                            ->db
                            ->update("api_proposal_response", $update_arr);

                        $result = array(
                            "customer_name" => $data['customer_name'],
                            "status" => "success",
                            "url" => '/resources/' . $file
                        );

                    }
                }

            }

        }
        else
        {
            $result = array(
                "customer_name" => $data['customer_name'],
                "status" => "success",
                "url" => $data['COI_url']
            );
        }

        return $result;
    }

    public function curl_call($url, $request)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_HTTPHEADER => array(
                "username: esb_axis",
                "password: esb@axis",
                "Accept: */*",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Content-Length: " . strlen($request) ,
                "Content-Type: application/json",
                "Host: bizpre.adityabirlahealth.com"
            ) ,
        ));

        return $response = curl_exec($curl);

    }
	
	function emandate_enquiry_HB_call_m()
    {
        $query = $this
            ->db
            ->query("select ed.lead_id,ed.product_id,emd.status from proposal as p,payment_details as pd,employee_details as ed left join emandate_data as emd on emd.lead_id = ed.lead_id where ed.emp_id = p.emp_id and p.id = pd.proposal_id and pd.payment_status = 'No Error' and emd.status NOT IN('Success','Fail') and p.status IN('Payment Received','Success') and ed.product_id IN ('ABC','MUTHOOT') group by p.emp_id order by ed.emp_id desc limit 20")
            ->result_array();

        if ($query)
        {
            foreach ($query as $val)
            {
                $this->real_pg_check($val['lead_id']);
            }

        }
    }

    function real_pg_check($lead_id)
    {
        $check_pg = false;

        $query = $this
            ->db
            ->query("SELECT ed.lead_id,ed.emp_id,ed.product_id,mpm.payment_source,mpm.payment_vertical,mpm.payment_mode FROM employee_details as ed,main_product_master as mpm where ed.lead_id ='".$lead_id."' AND ed.product_id = mpm.main_product_id")->row_array();

        if ($query)
        {
            if($query['product_id'] == 'ABC') {
                $ProductInfo = $query['product_id']."_".$leadId;
            } else if($query['product_id'] == 'MUTHOOT') {
                $ProductInfo = 'Muthoot Group';
            } else if($query['product_id'] == 'HERO_FINCORP') {
                $ProductInfo = 'Hero Fin Corp';
            } else if ($query['product_id'] == 'ABML') {
                $ProductInfo = 'Aditya Birla Money';
            }

            $Source = $query["payment_source"];
            $Vertical = $query["payment_vertical"];
            $PaymentMode = $query["payment_mode"];

            // $CKS_data = "ABC|" . $vertical . "|LEADID|" . $query['lead_id'] . "|" . $this->hash_key;
            $CKS_data = $Source."|".$Vertical."|LEADID|".$query['lead_id']."|".$this->hash_key;

            $CKS_value = hash($this->hashMethod, $CKS_data);

            $url = "https://pg_uat.adityabirlahealth.com/PGMANDATE/service/api/enquirePayment";
            $fqrequest = array(
                "signature" => $CKS_value,
                "Source" => $Source,
                "Vertical" => $Vertical,
                "SearchMode" => "LEADID",
                "UniqueIdentifierValue" => $query['lead_id'],
                "PaymentMode" => "PP"
            );
            // print_pre($fqrequest);exit;
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
                    "Content-Type: application/json"
                ) ,
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            $result = json_decode($response, true);

            // print_r($response);
            // echo json_encode($fqrequest); exit;
            if ($err)
            {
                $request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest) , "res" => json_encode($err) , "product_id" => 'ABC', "type" => "pg_real_fail"];

                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this
                    ->Logs_m
                    ->insertLogs($dataArray);

            }
            else
            {

                if ($result && $result['PaymentStatus'] == 'PR')
                {

                    $TxStatus = "success";
                    $TxMsg = "No Error";

                    $request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest) , "res" => json_encode($result) , "product_id" => 'ABC', "type" => "pg_real_success"];

                    $dataArray['tablename'] = 'logs_docs';
                    $dataArray['data'] = $request_arr;
                    $this
                        ->Logs_m
                        ->insertLogs($dataArray);

                    // $date = new DateTime($result['txnDateTime']);
                    // $txt_date = $date->format('m/d/Y g:i:s A');
                    $arr = ["payment_status" => $TxMsg, "premium_amount" => $result['amount'], "payment_type" => $result['paymentMode'], "txndate" => $result['txnDateTime'], "TxRefNo" => $result['TxRefNo'], "TxStatus" => $TxStatus, "json_quote_payment" => json_encode($result) ];

                    $proposal_ids = $this
                        ->db
                        ->query("select id as proposal_id from proposal where emp_id='" . $query['emp_id'] . "'")->result_array();

                    foreach ($proposal_ids as $query_val)
                    {
                        $this
                            ->db
                            ->where("proposal_id", $query_val['proposal_id']);
                        $this
                            ->db
                            ->where('TxStatus != ', 'success');
                        $this
                            ->db
                            ->update("payment_details", $arr);
                    }

                    if ($result['Registrationmode'])
                    {

                        $query_emandate = $this
                            ->db
                            ->query("select * from emandate_data where lead_id='" . $query['lead_id'] . "' AND product_id = '".$query['product_id']."'")->row_array();

                        if ($result['EMandateStatus'] == 'MS')
                        {
                            $mandate_status = 'Success';
                            //HB emandate call
                            $this->emandate_HB_call($query['emp_id']);
                        }
                        elseif ($result['EMandateStatus'] == 'MI')
                        {
                            $mandate_status = 'Emandate Pending';
                        }
                        elseif ($result['EMandateStatus'] == 'MR')
                        {
                            $mandate_status = 'Emandate Received';
                        }
                        elseif ($result['EMandateStatus'] == '')
                        {
                            $mandate_status = 'Emandate Pending';
                        }
                        else
                        {
                            $mandate_status = 'Fail';
                        }

                        if ($query_emandate > 0)
                        {

                            $arr = ["TRN" => $result['EMandateRefno'], "status_desc" => $result['EMandateStatusDesc'], "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($result['EMandateDate'])) , "Registrationmode" => $result['Registrationmode'], "EMandateFailureReason" => $result['EMandateFailureReason'], "MandateLink" => $result['MandateLink']];

                            $this
                                ->db
                                ->where(["lead_id"=>$query['lead_id'],"product_id"=>$query['product_id']]);
                            $this
                                ->db
                                ->update("emandate_data", $arr);
                        }
                        else
                        {

                            $arr = ["lead_id" => $query['lead_id'],"product_id"=>$query['product_id'],"TRN" => $result['EMandateRefno'], "status_desc" => $result['EMandateStatusDesc'], "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($result['EMandateDate'])) , "Registrationmode" => $result['Registrationmode'], "EMandateFailureReason" => $result['EMandateFailureReason'], "MandateLink" => $result['MandateLink']];

                            $this
                                ->db
                                ->insert("emandate_data", $arr);

                            // Insert emandate insert log in logs_docs
                            $request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($arr), "res" => "" , "product_id"=>$query['product_id'], "type" => "insert_emandate_data"];
                            $dataArray['tablename'] = 'logs_docs';
                            $dataArray['data'] = $request_arr;
                            $this
                                ->Logs_m
                                ->insertLogs($dataArray);

                        }

                        if ($mandate_status == 'Success')
                        {
                            $this->send_message($query['lead_id'], 'success');
                        }

                        if ($mandate_status == 'Fail')
                        {
                            $this->send_message($query['lead_id'], 'fail');
                        }
						
						if($result['paymentMode'] == 'PP' && ($result['Registrationmode'] == 'SAD' || $result['Registrationmode'] == 'EMI')){
							$this->send_message($query['lead_id'],'SAD_EMI_one');
							$this->send_message($query['lead_id'],'SAD_EMI_two');
						}
								
                    }

                    $check_pg = true;

                }
                else
                {

                    $request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest) , "res" => json_encode($result) , "product_id" => 'ABC', "type" => "pg_real_fail"];

                    $dataArray['tablename'] = 'logs_docs';
                    $dataArray['data'] = $request_arr;
                    $this
                        ->Logs_m
                        ->insertLogs($dataArray);

                }

            }
        }

        return $check_pg;
    }


    function send_message($lead_id,$type)
	{
			$query_check = $this->db->query("SELECT ed.emp_id,ed.lead_id,ed.email,ed.mob_no,ed.customer_name,ed.product_id,mpst.click_pss_url,mpst.product_name,ee.EMandateFailureReason,ee.Registrationmode,ee.MandateLink,sum(p.premium) as total_amt FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,emandate_data as ee WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.product_name AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.lead_id = ee.lead_id AND ed.lead_id='".$lead_id."'")->row_array();
			
			if($query_check){
				
				$senderID = 1;
				$AlertV1 = $query_check['customer_name'];
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
					$AlertV5 = 'https://www.adityabirlacapital.com/healthinsurance/#!/our-branches';
				}
				
				if($type == 'SAD_EMI_one'){
					$alertID = 'A1405';
					$AlertV2 = $query_check['product_name'];
					$AlertV3 = $query_check['MandateLink'];
				}
				
				if($type == 'SAD_EMI_two'){
					$alertID = 'A1406';
					$AlertV1 = $query_check['MandateLink'];;
				}
					
				
				$parameters =[
					"RTdetails" => [
				   
						"PolicyID" => '',
						"AppNo" => 'HD100017934',
						"alertID" => $alertID,
						"channel_ID" => 'ABC Application',
						"Req_Id" => 1,
						"field1" => '',
						"field2" => '',
						"field3" => '',
						"Alert_Mode" => 2,
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
				
				$request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($parameters),"res" => json_encode($response) ,"product_id"=> $query_check['product_id'], "type"=>"sms_logs_emandate_".$type];
				
				$dataArray['tablename'] = 'logs_docs'; 
				$dataArray['data'] = $request_arr; 
				$this->Logs_m->insertLogs($dataArray);
		
		  }
	}

    public function send_coi_alert($lead_id,$coiNo){
        $query = "SELECT ed.emp_id,ed.lead_id,ed.email,ed.mob_no,ed.customer_name,ed.product_id,mpst.click_pss_url,mpst.master_policy_no,mpst.product_name,apr.certificate_number FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,api_proposal_response as apr WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.product_name AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND apr.emp_id = ed.emp_id AND ed.lead_id='".$lead_id."' AND apr.certificate_number = '".$coiNo."'";

        $leadData = $this->db->query($query)->row_array();

        if(!empty($leadData)){
            $parameters = [
                "RTdetails"=> [
                    "PolicyID"=> "GHI-HF1-21-2000075",
                    "AppNo"=> "1000110010000388",
                    "alertID"=> "A1883",
                    "channel_ID"=> "HFL",
                    "Req_Id"=> "CAS-P9Pfndscjn",
                    "field1"=> "",
                    "field2"=> "",
                    "field3"=> "",
                    "Alert_Mode"=> "3",
                    "Attachment"=> [
                        "Flag"=> "1",
                        "Details"=> [
                            [
                                "Document"=> [
                                    [
                                        "Key"=> "2",
                                        "Value"=> "GHI-HF1-21-2000075"
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "Alertdata"=> [
                        "mobileno"=> "7838637938",
                        "emailId"=> "puneet.dwivedi@adityabirlacapital.com",
                        "AlertV1"=> "Puneet",
                        "AlertV2"=> "GHI",
                        "AlertV3"=> "GHI-HF1-21-2000075",
                        "AlertV4"=> "02-07-2021",
                        "AlertV5"=> "",
                        "AlertV6"=> "",
                        "AlertV7"=> "",
                        "AlertV8"=> "",
                        "AlertV9"=> "",
                        "AlertV10"=> "",
                        "AlertV11"=> "",
                        "AlertV12"=> "",
                        "AlertV13"=> "",
                        "AlertV14"=> "",
                        "AlertV15"=> "",
                        "AlertV16"=> "",
                        "AlertV17"=> "",
                        "AlertV18"=> "",
                        "AlertV19"=> "",
                        "AlertV20"=> "",
                        "AlertV21"=> "",
                        "AlertV22"=> "",
                        "AlertV23"=> "",
                        "AlertV24"=> "",
                        "AlertV25"=> "",
                        "AlertV26"=> "",
                        "AlertV27"=> "",
                        "AlertV28"=> "",
                        "AlertV29"=> "",
                        "AlertV30"=> "",
                        "AlertV31"=> "",
                        "AlertV32"=> "",
                        "AlertV33"=> "",
                        "AlertV34"=> "",
                        "AlertV35"=> "",
                        "AlertV36"=> "",
                        "AlertV37"=> "",
                        "AlertV38"=> "",
                        "AlertV39"=> "",
                        "AlertV40"=> "",
                        "AlertV41"=> "",
                        "AlertV42"=> "",
                        "AlertV43"=> "",
                        "AlertV44"=> "",
                        "AlertV45"=> "",
                        "AlertV46"=> "",
                        "AlertV47"=> "",
                        "AlertV48"=> "",
                        "AlertV49"=> "",
                        "AlertV50"=> ""
                    ]
                ]
            ];
            $parameters = json_encode($parameters);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://bizpre.adityabirlahealth.com/ABHICL_ClickPSS/Service1.svc/click",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 60,
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

            $request_arr = [
                "lead_id" => $leadData['lead_id'], 
                "req" => $parameters,
                "res" => json_encode($response),
                "product_id"=> $leadData['product_id'], 
                "type"=>"coi_response_alert_msg"
            ];

            $dataArray['tablename'] = 'logs_docs'; 
            $dataArray['data'] = $request_arr; 
            $this->Logs_m->insertLogs($dataArray);
        }
    }

    // For MUTHOOT ONLY
    public function updatePolicyStatusResponse($leadId,$coiNo){
        $leadData = $this->db->query("SELECT ed.emp_id,ed.cust_id,ed.json_qote,ed.lead_id,ed.email,ed.mob_no,ed.customer_name,ed.product_id,mpst.click_pss_url,mpst.master_policy_no,mpst.product_name,apr.certificate_number,apr.start_date,apr.end_date,apr.gross_premium,epd.policy_sub_type_id as premium FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,api_proposal_response as apr WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.product_name AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND apr.emp_id = ed.emp_id AND ed.lead_id='".$lead_id."' AND apr.certificate_number = '".$coiNo."'")->row_array();

        $jsonFields = $leadData['json_qote'];
        $jsonFieldsArr = json_decode($jsonFields,true);
        $token = $jsonFieldsArr['token'];

        $requestArr = [
            "saveJson"=>[
                "MethodName"=>"UpdatePolicyStatusResponse",
                "Token"=>$token,
                "CustomerId"=>$leadData['cust_id'],
                "ClientCode"=>"ABI",
                "ProductId"=>"1",
                "ProductName"=>"Insurance",
                "PolicyNumber"=>$leadData['certificate_number'],
                "PolicyAmount"=>$leadData['gross_premium'],
                "PolicyDate"=>date('Y-m-d',strtotime($leadData['start_date'])),
                "ExpiryDate"=>date('Y-m-d',strtotime($leadData['end_date'])),
                "CreatedThrough"=>"1",
                "PremiumAmount"=>$leadData['gross_premium'],
                "ApiVersion"=>"1"
            ]
        ];

        $url = 'http://59.145.109.140:14340/Save.ashx/UpdatePolicyStatusResponse';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($requestArr),
            CURLOPT_HTTPHEADER => [
                "Accept: */*", 
                "Cache-Control: no-cache", 
                "Connection: keep-alive", 
                "Content-Type: application/json"
            ],
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        // Store Log in logs docs 
        $request_arr = ["lead_id" => $leadData['lead_id'], "product_id" => $leadData['product_id'], "req" => json_encode($requestArr) , "res" => json_encode($response) , "type" => "update_policy_status_".$leadData['policy_subtype_id']];
        $dataArray['tablename'] = 'logs_docs';
        $dataArray['data'] = $request_arr;
        $this
            ->Logs_m
            ->insertLogs($dataArray);
        // Store Local log ends

        return true;
    }

    // For ABC ONLY
    public function crmLeadUpdatePolicyIssuedAbc($emp_id) {
        // Create LEAD CRM Start
        // $emp_id = '2602';
        $this->db1 = $this->load->database('axis_retail',TRUE);
        
        // Get Employee Details
        $sqlEmpDetailsStr = "SELECT * FROM employee_details WHERE emp_id = ".$emp_id;
        $empDetailsArray  = $this->db1->query($sqlEmpDetailsStr)->result_array()[0];
        
        $customerName = !empty($empDetailsArray['customer_name']) ? explode(" ",trim($empDetailsArray['customer_name'])) : "";
        
        // Get DOB
        $dateOfBirth = date("Y-m-d",strtotime($dob));
        $empDOB = !empty($empDetailsArray['bdate']) ? $empDetailsArray['bdate'] : "";
        $empAge = 0;
        // DOB to Age Convertation Part Start
        if(!empty($empDOB)) {
            $today = date("Y-m-d");
            $diff = date_diff(date_create($empDOB), date_create($today));
            $empAge = !empty($diff->format('%y')) ? $diff->format('%y') : 0;            
        }
        // DOB to Age Convertation Part End
        
        $policy_detail_id = $this->db->get_where("employee_product_details",array("emp_id" => $emp_id))->row_array();
        $policy_id = $policy_detail_id['policy_id'];
        // Customer JSON Data
        $jsonData = !empty($empDetailsArray['json_qote']) ? json_decode($empDetailsArray['json_qote'], true) : "";
        
        // Get Drop Off Long URL Start
        $sqlStr = "select ed.json_qote,ed.mob_no,ed.email,ed.lead_id,ua.type,ed.emp_id
        FROM employee_details as ed INNER JOIN user_activity_abc as ua
        ON ed.emp_id = ua.emp_id AND ua.emp_id = $emp_id";
        //echo $sqlStr;exit;
        $aResult = $this->db1->query($sqlStr)->row_array();
        $activityType = $aResult['type'];
        
        // Activity Stage
        // $activityStage = "";
        // $rating = '';
        // switch ($activityType) {
        //     case 1:
        //         $activityStage = "at Product Page";
        //         $rating = "Cold";
        //         break;
        //     case 2:
        //         $activityStage = "at know your premium page";
        //         $rating = "Cold";
        //         break;
        //     case 3:
        //         $activityStage = "at member enrollement page";
        //         $rating = "Warm";
        //         break;  
        //     case 4:
        //         $activityStage = "at review page";
        //         $rating = "Hot";
        //         break;  
        //     case 5:
        //         $activityStage = "at redirected to payment page";
        //         $rating = "Very Hot";
        //         break;  
        //     case 6:
        //         $activityStage = "at payment page";
        //         $rating = "Super-Hot";
        //         break; 
        //     case 7:
        //         $activityStage = "at thank you page";
        //         $rating = "Won";
        //         break;      
        // }
        
    
        // switch ($activityType) {
        //     case 1:
        //         $longUrl = base_url('comprehensive_product_abc');
        //         break;
        //     case 2:
        //         $longUrl = base_url('quotes_abc/'.$policy_id);
        //         break;
        //     case 3:
        //         $longUrl = base_url('member_proposer_detail');
        //         break;
        //     case 4:
        //         $longUrl = base_url('member_detail_product_abc');
        //         break;
        //      case ($activityType == 5 || $activityType == 6):
        //         $longUrl = base_url('payment_redirection');
        //         break;
        //     case 7:
        //         $longUrl = base_url('payment_success_view_call_abc/'.$emp_id);
        //         break;
        // }
        // Get Drop Off Long URL End
        //echo $longUrl;exit;
        // Get Premium Start
        $query_premium = $this->db1->query("select SUM(p.premium) as premium from proposal as p where p.emp_id = ".$emp_id." GROUP BY p.emp_id")->row_array();
        
        $premium = !empty($query_premium['premium']) ? $query_premium['premium'] : 0;
        // $familyConstruct = !empty($query_premium['familyConstruct']) ? $query_premium['familyConstruct'] : 0;
        // $sumInsured = !empty($query_premium['policy_mem_sum_insured']) ? $query_premium['policy_mem_sum_insured'] : 0;
        // Get Premium End
        
        // Get Proposal Details Start
        $queryProposal = "SELECT * FROM proposal WHERE emp_id = ".$emp_id;
        $query_proposal = $this->db1->query($queryProposal)->row_array();
        $proposalNumber = !empty($query_proposal['proposal_no']) ? $query_proposal['proposal_no'] : "";
        $proposalStatus = !empty($query_proposal['status']) ? $query_proposal['status'] : "";
        // Get Proposal Details End
        
        // Get Certificate Details Start
        $certificateNumber = NULL;
        if(!empty($proposalNumber)) {
            $queryCertificateDetails = "SELECT * FROM api_proposal_response WHERE proposal_no_lead = '".$proposalNumber."'";
            $query_CertificateDetails = $this->db1->query($queryCertificateDetails)->row_array();
            $certificateNumber = !empty($query_CertificateDetails['certificate_number']) ? $query_CertificateDetails['certificate_number'] : "";
        }
        // Get Certificate Details End
        
        // Get Nominee Details Start
        $queryNomineeDetails = "SELECT mpn.nominee_fname,mn.nominee_type FROM member_policy_nominee as mpn, master_nominee as mn WHERE mpn.fr_id = mn.nominee_id AND emp_id = ".$emp_id;
        $query_nominee = $this->db1->query($queryNomineeDetails)->row_array();
        $nomineeName = !empty($query_nominee['nominee_fname']) ? $query_nominee['nominee_fname'] : "";
        $nomineeRelation = !empty($query_nominee['nominee_type']) ? $query_nominee['nominee_type'] : "";
        // Get Nominee Details End
        
        // First Name, Last Name, Middle name
        $firstName = !empty($customerName[0]) ? $customerName[0] : "";
        $lastName = !empty($customerName) && count($customerName) > 2 ? $customerName[2] : $customerName[1];
        $middleName = "";
        $lastname = "";
        if(!empty($customerName) && count($customerName) > 2) {
            $middleName = !empty($customerName[1]) ? $customerName[1] : ".";
        } 
        
        $pinCode = !empty($empDetailsArray['emp_pincode']) ? $empDetailsArray['emp_pincode'] : "";
        
        // Create Lead API Array
        $leadCreateArray = array(
                        "AADHARNO"=>"",
                        "PINCODE"=>"",
                        "ACTIVITYDESCRIPTION"=>"",
                        "ACTIVITYSUBECT"=>"",
                        "ACTIVITYTYPE"=>NULL,
                        "ADDRESSLINE1"=>"",
                        "ADDRESSLINE2"=>"",
                        "ADDRESSLINE3"=>"", // NOT IN NEW
                        "AGE"=>$empAge,
                        "ALCOHOLTABBACOCONSUMPTION"=>"",
                        "APPLICATIONNO"=>!empty($empDetailsArray['lead_id']) ? $empDetailsArray['lead_id'] : "",
                        "PartnerLeadId"=>"", // NOT IN NEW
                        "ATTACHMENTS"=>array(),
                        "Adgroup"=>"",
                        "AffiliateDiscountFlag"=>"",
                        "CITY"=>!empty($empDetailsArray['emp_city']) ? $empDetailsArray['emp_city'] : "",
                        "CONTACTNUMBER"=>!empty($empDetailsArray['mob_no']) ? $empDetailsArray['mob_no'] : "",
                        "COPAYMENTWAIVER"=>"",
                        "COVER"=>"Self",
                        "CUSTOMERTYPE"=>"",
                        // "DATEOFBIRTH"=>$empDOB,
                        "DATEOFBIRTH"=>date("d/m/Y", strtotime($empDOB)),
                        "DEDUCTIBLES"=>"",
                        "DRIVINGLICENSENO"=>"",
                        "EMAIL"=>!empty($empDetailsArray['email']) ? $empDetailsArray['email'] : "",
                        "EXISTINGINSURANCE"=>"",
                        "EXISTINGINSURANCECOVER"=>"",
                        "EXISTINGINSURER"=>"",
                        "EmployeeDiscountFlag"=>"",
                        "EmployeeID"=>"",
                        "FAMILYCONSTRUCT"=>"", // NOT IN NEW
                        "FIRSTNAME"=>$firstName,
                        "MIDDLENAME" => $middleName,
                        "LASTNAME"=>$lastName,                      
                        "GCLID"=>"",
                        "GENDER"=>!empty($empDetailsArray['gender']) ? $empDetailsArray['gender'] : "",
                        "HEIGHT"=>"",
                        "HOSPITALCASHBENEFIT"=>"",
                        "AutoRenewalFlag"=>"", // NOT IN NEW
                        "Rating"=> "", // NOT IN NEW
                        "IDNUMBER"=>"",
                        "IDTYPE"=>NULL,
                        "INTERMEDIARYCODE"=>"",
                        "Keyword"=>"",
                        "LEADCREATEDBY"=>"",
                        "LEADOWNER"=>"",
                        "LEADREFERREDBY"=>"",
                        "LEADREFERREDBYID"=>"",
                        "LEADSTAGE"=> "",
                        "LEADSTATUS"=>"Policy issued",
                        "LEADTYPE"=>"",
                        "LONGURL"=>"",
                        "LeadId"=>"",
                        "LemniskId"=>"",
                        "MATERNITYANDVACINATION"=>"",
                        "MIDDLENAME"=>"",
                        "MOBILE"=>!empty($empDetailsArray['mob_no']) ? $empDetailsArray['mob_no'] : "",
                        "NOMINEENAME"=>"",
                        "NOMINEERELATION"=>"",
                        "NOTESDESCRIPTION"=>"",
                        "NOTESTITLE"=>"",
                        "OPDEXPENSES"=>"",
                        "OneABCData"=>array(
                            "ABCinId"=>"",
                            "AddressLine3"=>"",
                            "Anniversary"=>"",
                            "CallBackDateTime"=>"",
                            "Category"=>"",
                            "Comments"=>"",
                            "CreatedByInSource"=>"ABCPortal",
                            "CustOKForCallOut"=>"",
                            "Description"=>"",
                            "GoldenId"=>"",
                            "Income"=>"",
                            "LOBPresence"=>"",
                            "LeadQualityCode"=>"",
                            "LeadSubSource"=>"",
                            "RefDepartment"=>"",
                            "RefEmpEmail"=>"",
                            "RefEmpId"=>"",
                            "RefEmpName"=>"",
                            "RefEmpPhone"=>"",
                            "Remarks"=>"",
                            "SourceBusinessName"=>NULL,
                            "SourceFunctionName"=>NULL,
                            "SourceLeadId"=>"",
                            "SourceOwnerId"=>"",
                            "SourceOwnerName"=>"",
                            "SourceUserEmail"=>"",
                            "SourceUserId"=>"",
                            "SourceUserLOB"=>"",
                            "SourceUserLocation"=>"",
                            "SourceUserName"=>"",
                            "SpecificProductIntrest"=>"",
                            "SubSource"=>"",
                            "Telephone"=>""
                        ),
                        "PAN"=>"",
                        "PASSPORTNO"=>"",
                        "PAYMENTAMOUNT"=>"",
                        "PAYMENTSTATUS"=>"",
                        //"POLICYNO"=>!empty($proposalNumber) ? $proposalNumber : NULL,
                        "POLICYNO"=>$certificateNumber,
                        "PREEXISTINGDISEASEMEDICALCONDITION"=>"",
                        "PREFERREDCONTACTIBLETIME"=>"",
                        "PREFERREDMODEOFCONTACT"=>"",
                        "PREMIUM"=>$premium,
                        "PRODUCT"=>"Sampoorna",
                        "PROPOSALTYPE"=>!empty($certificateNumber) ? "STP" : "NSTP",
                        "QUOTENUMBER"=>"",
                        "REFERENCENUMBER"=>!empty($empDetailsArray['lead_id']) ? $empDetailsArray['lead_id'] : "",
                        "RESIDENTPHONE"=>"",
                        "ROOMTYPE"=>"",
                        "SALUTATION"=>"",
                        "SHORTURL"=>"",
                        "SOURCE"=>"ABC_Xsell_1cr",
                        "SOURCEBUSINESSNAME"=>NULL,
                        "SOURCEFUNCTION"=>NULL,
                        "STATE"=>"",
                        "SUBSOURCE"=>"ABC_Xsell_1cr",
                        "SUMINSURED"=>"",
                        "Salary"=>"",
                        "TENURE"=>"",
                        "URL"=>"",
                        "UTMContent"=>"",
                        "VERIFIED"=>"",
                        "WEIGHT"=>"",
                        "ZONE"=>NULL,
                        "LEADSUBSOURCE"=>""
                   );
        //print_pre($leadCreateArray);exit;               
        $jsonData = json_encode($leadCreateArray);
        
        $request_arr = ["lead_id" => $empDetailsArray['lead_id'], "req" => $jsonData,"res" => json_encode($response) , "type"=>"CREATE_LEAD_CRM_JSON_POLICY_ISSUED", "product_id" => "ABC"];
        $this->db->insert("logs_docs",$request_arr);
        
        // Prepare new cURL resource
        $ch = curl_init('http://10.1.226.39:8080/LeadAPI/Service1.svc/Lead/CreateLead');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
         
        // Set HTTP Header for POST request 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData))
        );
         
        // Submit the POST request
        $result = curl_exec($ch);
        
        if($result === false){
            $response =  'Curl error: ' . curl_error($ch);
        } else {
            $response = $result;
        }

       // print_pre($response);
        
        // Insert Data into Drop Off Log Start
        $this->db1->insert("drop_off_crm_data", [
        "type" => 'create_lead_crm',
        "jsondata" => $jsonData,
        "leadid" => $empDetailsArray['lead_id'],
        "created_date" => date("Y-m-d h:i:s"),
        "requestresponse"=> $response
        ]);
        // Insert Data into Drop Off Log End
        
        return $response;
    }

    function get_all_quote_call($emp_id,$combineCoiCount)
    {   
        // echo("Inside Model get_all_quote_call <br>");
        /*$get_data = $this
            ->db
            ->query('SELECT ed.emp_id,mpst.policy_subtype_id,p.policy_detail_id,p.id,mpst.HB_policy_type FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.product_name AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.emp_id="' . $emp_id . '" ')->result_array();*/
        // Combine COI logic starts
            // echo "Combine COI Count : ".$combineCoiCount."<br>";
        $employeeDetail = $this->db->query("SELECT * from employee_details where emp_id = ".$emp_id)->row_array();
        if($combineCoiCount){
            if($employeeDetail['product_id'] == 'ABML'){
                // FOR ABML
                // echo 'ABML';
                $get_data = $this
                    ->db
                    ->query('SELECT ed.emp_id,GROUP_CONCAT(mpst.policy_subtype_id) as policy_subtype_id,GROUP_CONCAT(p.policy_detail_id) as policy_detail_id, GROUP_CONCAT(p.id) as id,mpst.HB_policy_type FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.product_name AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.emp_id="' . $emp_id . '" GROUP BY epd.is_combo')->result_array();
            } else {
                // FOR ABC
                $get_data = $this
                    ->db
                    ->query('SELECT ed.emp_id,mpst.policy_subtype_id,p.policy_detail_id,p.id,mpst.HB_policy_type FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.product_name AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.emp_id="' . $emp_id . '" AND epd.is_parent = 0 UNION ALL SELECT ed.emp_id,GROUP_CONCAT(mpst.policy_subtype_id) as policy_subtype_id,GROUP_CONCAT(p.policy_detail_id) as policy_detail_id, GROUP_CONCAT(p.id) as id,mpst.HB_policy_type FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.product_name AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.emp_id="' . $emp_id . '" GROUP BY epd.is_parent HAVING epd.is_parent != 0')->result_array();
            }
            // echo 'Combine';
        } else {
            $get_data = $this
                ->db
                ->query('SELECT ed.emp_id,mpst.policy_subtype_id,p.policy_detail_id,p.id,mpst.HB_policy_type FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.product_name AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.emp_id="' . $emp_id . '" ')->result_array(); 
        }
        // Combine COI logic ends

        // print_pre($this->db->last_query());
        // print_pre($get_data);exit;
        
		foreach ($get_data as $vall)
        {

            if ($vall['HB_policy_type'] == 'ProposalWise')
            {

                $query_check_ghi = $this
                    ->db
                    ->query("select id from ghi_quick_quote_response where policy_subtype_id = '" . $vall['policy_subtype_id'] . "' and emp_id='$emp_id' and proposal_id = '" . $vall['id'] . "' and status = 'success'")->row_array();

                if ($query_check_ghi)
                {
                    $last_array[] = 'Success';
                }
                else
                {
                    $GHI_quote = $this->get_quote_data($emp_id, $vall['policy_detail_id']);
                    $last_array[] = $GHI_quote['status'];
                }
            }
            else
            {

                $member_data = (array)$this->get_all_member_data($emp_id, $vall['policy_detail_id']);

                $query_start = $this
                    ->db
                    ->query("select count(id) as total_success from ghi_quick_quote_response where policy_subtype_id = '" . $vall['policy_subtype_id'] . "' and emp_id='$emp_id' and proposal_id = '" . $vall['id'] . "' and status = 'success'")->row_array();
                
                if ($member_data[0]['familyConstruct'] == '1A')
                {
                    $check_status = ($query_start['total_success'] == '1') ? 'Success' : 'error';
                }

                if ($member_data[0]['familyConstruct'] == '2A')
                {
                    $check_status = ($query_start['total_success'] == '2') ? 'Success' : 'error';
                }

                if ($check_status == 'error')
                {
                    foreach ($member_data as $key => $value)
                    {
                        $value['key_id'] = $key + 1;
                        $mem_data[0] = $value;

                        $is_data = $this
                            ->db
                            ->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and policy_subtype_id = '" . $value['policy_subtype_id'] . "' and fr_id = '" . $value['fr_id'] . "' and proposal_id = '" . $vall['id'] . "' ")->row_array();

                        if ($is_data)
                        {

                            $query_check = $this
                                ->db
                                ->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and policy_subtype_id = '" . $value['policy_subtype_id'] . "' and fr_id = '" . $value['fr_id'] . "' and proposal_id = '" . $vall['id'] . "' and status = 'error'")->row_array();

                            if ($query_check)
                            {
                                $policy_data = $this->get_quote_data($emp_id, $value['policy_detail_id'], $mem_data);
                            }

                        }
                        else
                        {

                            $arr = ["emp_id" => $emp_id, "policy_subtype_id" => $value['policy_subtype_id'], "fr_id" => $value['fr_id'], "status" => "error","proposal_id" => $vall['id']];
                            $this
                                ->db
                                ->insert("ghi_quick_quote_response", $arr);

                            $policy_data = $this->get_quote_data($emp_id, $value['policy_detail_id'], $mem_data);
                        }
                    }
                }

                $query_last = $this
                    ->db
                    ->query("select count(id) as total_success from ghi_quick_quote_response where policy_subtype_id = '" . $vall['policy_subtype_id'] . "' and emp_id='$emp_id' and proposal_id = '" . $vall['id'] . "' and status = 'success'")->row_array();

                if ($member_data[0]['familyConstruct'] == '1A')
                {
                    $check_status = ($query_last['total_success'] == '1') ? 'Success' : 'error';
                    $last_array[] = $check_status;
                }

                if ($member_data[0]['familyConstruct'] == '2A')
                {
                    $check_status = ($query_last['total_success'] == '2') ? 'Success' : 'error';
                    $last_array[] = $check_status;
                }

            }

        }

        if (in_array("error", $last_array))
        {
            $proposal_status = 'error';
        }
        else
        {
            $proposal_status = 'Success';
        }

        return $return_data = array(
            'status' => $proposal_status,
            "msg" => "testing"
        );
    }

    public function get_quote_data($emp_id, $policy_detail_id, $mem_data = '')
    {

        // Check if policy has combined COI STARTS
        $policy_detail_id_arr = explode(',',$policy_detail_id);
        // Check if policy has combined COI ENDS

        $data['customer_data'] = (array)$this->get_profile($emp_id);
        $data['nominee_data'] = (array)$this->get_all_nominee($emp_id);
        $data['member_data'] = (array)$this->get_all_member_data($emp_id, $policy_detail_id_arr[0]);
        $data['proposal_data'] = (array)$this->getProposalData($emp_id, $policy_detail_id_arr[0]);

        $data['combined_coi_different_data'] = (array)$this->getCombinedCoiDifferentData($emp_id,$policy_detail_id_arr);
        // print_pre($data);exit;
        $url = trim($data['proposal_data']['api_url']);
        if ($url == '')
        {
            return array(
                "status" => "error",
                "msg" => "Something Went Wrong"
            );
        }

        $explode_name = explode(" ", trim($data['customer_data']['customer_name']) , 2);

        $explode_name_nominee = explode(" ", trim($data['nominee_data']['nominee_fname']) , 2);

        $source_name = $data['proposal_data']['HB_source_code'];
        
		
		if ($data['proposal_data']['HB_policy_type'] == 'MemberWise')
		{
			$data['member_data'] = $mem_data;
			$concat_string = $data['proposal_data']['HB_custid_concat_string'];

			$data['customer_data']['cust_id'] = $data['customer_data']['cust_id'] . $concat_string . $data['member_data'][0]['key_id'];

		} else {
            $concat_string = $data['proposal_data']['HB_custid_concat_string'];
            $data['customer_data']['cust_id'] = $data['customer_data']['cust_id'] . $concat_string;
        }
		

        // Plan Code & Sum Insured for combined COI
        $totalPlanCodes = count($data['combined_coi_different_data']);
        $planCodeSi = [];
        $memberObjPlanCode = [];
        for ($i = 0;$i < $totalPlanCodes;$i++) {
            $planCodeSi[] = [
                "PlanCode" => $data['combined_coi_different_data'][$i]['plan_code'], 
                "SumInsured" => $data['combined_coi_different_data'][$i]['sum_insured'], 
                "SchemeCode" => $data['customer_data']['scheme_code']
            ];
            $memberObjPlanCode[] = [
                "PlanCode" => $data['combined_coi_different_data'][$i]['plan_code'], 
                "MemberQuestionDetails" => [
                    [
                        "QuestionCode" => null, "Answer" => null, 
                        "Remarks" => null]
                    ]
                ];
        }
        // Plan Code & Sum Insured for combined COI

		$totalMembers = count($data['member_data']);
        $member = [];

        for ($i = 0;$i < $totalMembers;$i++)
        {
            if ($data['member_data'][$i]['fr_id'] == 2 || $data['member_data'][$i]['fr_id'] == 3 || $data['member_data'][$i]['fr_id'] == 25 || $data['member_data'][$i]['fr_id'] == 26)
            {
                // if ($data['member_data'][$i]['fr_id'] == 2 && $data['member_data'][$i]['gender'] != "Male")
                // {
                //     $data['member_data'][$i]['relation_code'] = "R004";
                // }
                // elseif ($data['member_data'][$i]['fr_id'] == 3 && $data['member_data'][$i]['gender'] != "Female")
                // {
                //     $data['member_data'][$i]['relation_code'] = "R003";
                // }

                // New Relation Code Logic in Kid
                if($data['member_data'][$i]['gender'] == "Male"){
                    $data['member_data'][$i]['relation_code'] = "R003";
                }else if($data['member_data'][$i]['gender'] == "Female"){
                    $data['member_data'][$i]['relation_code'] = "R004";
                }else {
                    $data['member_data'][$i]['relation_code'] = ""; 
                }
            }

            $abc = ["PEDCode" => null, "Remarks" => null];

            $explode_name_member = explode(" ", trim($data['member_data'][$i]['firstname']) , 2);

            $member[] = ["MemberNo" => $i + 1, "Salutation" => (($data['member_data'][$i]['gender'] == "Male") ? 'Mr' : (($data['member_data'][$i]['age'] >= 18) ? 'Mrs' : 'Ms')) , "First_Name" => $explode_name_member[0], "Middle_Name" => null, "Last_Name" => !empty($explode_name_member[1]) ? $explode_name_member[1] : '.', "Gender" => ($data['member_data'][$i]['gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['dob'])) , "Relation_Code" => trim($data['member_data'][$i]['relation_code']) , "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => "O553", "PrimaryMember" => (($i == 0) ? "Y" : "N") , "MemberproductComponents" => $memberObjPlanCode, "MemberPED" => $abc, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $explode_name_nominee[0], "Nominee_Last_Name" => !empty($explode_name_nominee[1]) ? $explode_name_nominee[1] : '.', "Nominee_Contact_Number" => $data['nominee_data']['nominee_contact'], "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data']['relation_code']) , ];

        }
        
        $fqrequest = ["ClientCreation" => ["Member_Customer_ID" => $data['customer_data']['cust_id'], "salutation" => $data['customer_data']['salutation'], "firstName" => $explode_name[0], "middleName" => "", "lastName" => !empty(trim($explode_name[1])) ? $explode_name[1] : '.', "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['bdate'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['emp_pincode'], "uidNo" => $data['customer_data']['adhar'], "maritalStatus" => null, "nationality" => "Indian", "occupation" => "O553", "primaryEmailID" => $data['customer_data']['email'], "contactMobileNo" => substr(trim($data['customer_data']['mob_no']) , -10) , "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => null, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address'], "homeAddressLine2" => null, "homeAddressLine3" => null, "homePinCode" => $data['customer_data']['emp_pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => "", "MasterPolicyNumber" => $data['proposal_data']['master_policy_no'], "GroupID" => $data['proposal_data']['group_code'], "Product_Code" => $data['proposal_data']['plan_code'], "SumInsured_Type" => null, "Policy_Tanure" => "1", "Member_Type_Code" => "M209", "intermediaryCode" => $data['proposal_data']['IMDCode'], "AutoRenewal" => $data['proposal_data']['si_auto_renewal'], "AutoDebit" => $data['proposal_data']['si_auto_renewal'], "intermediaryBranchCode" => "10MHMUM01", "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $data['customer_data']['lead_id'], "Source_Name" => $source_name, "SPID" => $data['customer_data']['ref2'], "TCN" => null, "CRTNO" => null, "RefCode1" => $data['customer_data']['ref1'], "RefCode2" => $data['customer_data']['branch_sol_id'], "Employee_Number" => $data['proposal_data']['emp_id'], "enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "0", "PaymentMode" => null, "PolicyproductComponents" => $planCodeSi], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "", "modeOfEntry" => "", "cdAcNo" => null, "expiryDate" => null, "payerType" => "", "payerCode" => null, "paymentBy" => "", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => "", "collectionRcvdDate" => null, "collectionMode" => "", "remarks" => null, "instrumentNumber" => null, "instrumentDate" => null, "bankName" => null, "branchName" => null, "bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => "", "PaymentGatewayName" => "", "TerminalID" => "", "CardNo" => null]];

        if($data['customer_data']['product_id'] == 'HERO_FINCORP'){
            $fqrequest["PolicyCreationRequest"]["goGreen"] = $data['customer_data']['go_green_flag'];
        }

        $request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($fqrequest) , "product_id" => $data['customer_data']['product_id'], "type" => "full_quote_request1_".$data['proposal_data']['policy_sub_type_id']];
        $this
            ->db
            ->insert("logs_docs", $request_arr);
        $insert_id = $this
            ->db
            ->insert_id();

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
        // print_pre($response);exit;
        $err = curl_error($curl);
        if ($response == '' || $response == NULL)
        {
            $response = $err;
        }
        $request_arr = ["res" => json_encode($response) ];
        $this
            ->db
            ->where("id", $insert_id);
        $this
            ->db
            ->update("logs_docs", $request_arr);

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

            if ($errorObj['ErrorNumber'] == '00')
            {

                $policydetail = $newArr['policyDtls'];

                if ($data['proposal_data']['HB_policy_type'] == 'ProposalWise')
                {
                    foreach ($policy_detail_id_arr as $key => $policyDetailId) {
                        // Find Proposal ID Start
                        $proposalRecord = $this->db->query("SELECT *,epd.policy_sub_type_id as policy_sub_type_id from proposal as p, employee_policy_detail as epd where p.policy_detail_id = epd.policy_detail_id  AND p.emp_id = '".$emp_id."' AND p.policy_detail_id = ".$policyDetailId)->row_array();
                        // Find Proposal ID End
                        $query_one = $this
                        ->db
                        ->query("select * from ghi_quick_quote_response where emp_id='" . $emp_id . "' and policy_subtype_id = '" . $proposalRecord['policy_sub_type_id'] . "' and proposal_id = '" . $proposalRecord['id'] . "'")->row_array();

                        if ($query_one > 0)
                        {
                            $arr = ["QuotationNumber" => $policydetail['QuotationNumber'], "PolicyNumber" => $policydetail['PolicyNumber'], "MemberCustomerID" => $policydetail['MemberCustomerID'], "status" => "success"];

                            $update_where = ["emp_id" => $emp_id, "policy_subtype_id" => $proposalRecord['policy_sub_type_id'],"proposal_id" => $proposalRecord['id']];

                            $this
                                ->db
                                ->where($update_where);
                            $this
                                ->db
                                ->update("ghi_quick_quote_response", $arr);
                        }
                        else
                        {
                            $arr = ["emp_id" => $emp_id, "status" => "success", "policy_subtype_id" => $proposalRecord['policy_sub_type_id'], "QuotationNumber" => $policydetail['QuotationNumber'],"proposal_id" => $proposalRecord['id']];

                            $this
                                ->db
                                ->insert("ghi_quick_quote_response", $arr);
                        }
                    }
                }
                else
                {

                    $query_one = $this
                        ->db
                        ->query("select * from ghi_quick_quote_response where emp_id='" . $emp_id . "' and policy_subtype_id = '" . $data['proposal_data']['policy_sub_type_id'] . "' and fr_id = '" . $data['member_data'][0]['fr_id'] . "' and proposal_id = '" . $data['proposal_data']['proposal_id'] . "'")->row_array();

                    if ($query_one > 0)
                    {

                        $arr = ["QuotationNumber" => $policydetail['QuotationNumber'], "PolicyNumber" => $policydetail['PolicyNumber'], "MemberCustomerID" => $policydetail['MemberCustomerID'], "status" => "success"];

                        $update_where = ["emp_id" => $emp_id, "policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'], "fr_id" => $data['member_data'][0]['fr_id'],"proposal_id" => $data['proposal_data']['proposal_id']];

                        $this
                            ->db
                            ->where($update_where);
                        $this
                            ->db
                            ->update("ghi_quick_quote_response", $arr);
                    }
                    else
                    {

                        $arr = ["emp_id" => $emp_id, "status" => "success", "policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'], "fr_id" => $data['member_data'][0]['fr_id'], "QuotationNumber" => $policydetail['QuotationNumber'],"proposal_id" => $data['proposal_data']['proposal_id']];

                        $this
                            ->db
                            ->insert("ghi_quick_quote_response", $arr);
                    }

                }

                return array(
                    "status" => "Success",
                    "msg" => $policydetail['QuotationNumber']
                );
            }
            else
            {

                if ($data['proposal_data']['HB_policy_type'] == 'ProposalWise')
                {
                    foreach ($policy_detail_id_arr as $key => $policyDetailId) {
                        // Find Proposal ID Start
                        $proposalRecord = $this->db->query("SELECT *,epd.policy_sub_type_id as policy_sub_type_id from proposal as p, employee_policy_detail as epd where p.policy_detail_id = epd.policy_detail_id  AND p.emp_id = '".$emp_id."' AND p.policy_detail_id = ".$policyDetailId)->row_array();
                        // Find Proposal ID End

                        $query_one = $this
                        ->db
                        ->query("select * from ghi_quick_quote_response where emp_id='" . $emp_id . "' and policy_subtype_id = '" . $proposalRecord['policy_sub_type_id'] . "' and proposal_id = '" . $proposalRecord['id'] . "'")->row_array();

                        if ($query_one > 0)
                        {

                            $arr = ["count" => $query_one['count'] + 1, "status" => "error"];
                            $update_where = ["emp_id" => $emp_id, "policy_subtype_id" => $proposalRecord['policy_sub_type_id'],"proposal_id" => $proposalRecord['id']];

                            $this
                                ->db
                                ->where($update_where);
                            $this
                                ->db
                                ->update("ghi_quick_quote_response", $arr);
                        }
                        else
                        {

                            $arr = ["emp_id" => $emp_id, "status" => "error", "policy_subtype_id" => $proposalRecord['policy_sub_type_id'],"proposal_id" => $proposalRecord['id']];

                            $this
                                ->db
                                ->insert("ghi_quick_quote_response", $arr);
                        }
                    }
                }
                else
                {

                    $query_one = $this
                        ->db
                        ->query("select * from ghi_quick_quote_response where emp_id='" . $emp_id . "' and policy_subtype_id = '" . $data['proposal_data']['policy_sub_type_id'] . "' and fr_id = '" . $data['member_data'][0]['fr_id'] . "' and proposal_id = '" . $data['proposal_data']['proposal_id'] . "'")->row_array();

                    if ($query_one > 0)
                    {

                        $arr = ["count" => $query_one['count'] + 1, "status" => "error"];
                        $update_where = ["emp_id" => $emp_id, "policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'], "fr_id" => $data['member_data'][0]['fr_id'],"proposal_id" => $data['proposal_data']['proposal_id']];

                        $this
                            ->db
                            ->where($update_where);
                        $this
                            ->db
                            ->update("ghi_quick_quote_response", $arr);
                    }
                    else
                    {

                        $arr = ["emp_id" => $emp_id, "status" => "error", "policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'], "fr_id" => $data['member_data'][0]['fr_id'],"proposal_id" => $data['proposal_data']['proposal_id']];

                        $this
                            ->db
                            ->insert("ghi_quick_quote_response", $arr);
                    }

                }
				
				///------- @author : Guru --------------------------//
				$request_failure_arr = ["lead_id" => $data['customer_data']['lead_id'], "product_id" => $data['customer_data']['product_id'], "res" => $errorObj['ErrorMessage'], "type" => "failure_reason_".$data['proposal_data']['policy_sub_type_id']];
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

    function GHI_GCI_api_call_bk_070421($emp_id, $policy_no, $mem_data = '',$cron_policy_check = '')
    {  

        // Check if policy has combined COI STARTS
        $policy_detail_id_arr = explode(',',$policy_no);
        // Check if policy has combined COI ENDS

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
            $data['member_data'] = (array)$this->get_all_member_data($emp_id, $policy_detail_id_arr[0]);
            $data['nominee_data'] = (array)$this->get_all_nominee($emp_id);
            $data['proposal_data'] = (array)$this->getProposalData($emp_id, $policy_detail_id_arr[0]);
            // print_pre($data);exit;

            $data['combined_coi_different_data'] = (array)$this->getCombinedCoiDifferentData($emp_id,$policy_detail_id_arr);
            print_pre($data['combined_coi_different_data']);exit;

            $url = trim($data['proposal_data']['api_url']);

            if ($url == '')
            {
                return array(
                    "status" => "error",
                    "msg" => "Something Went Wrong"
                );
            }
            $explode_name = explode(" ", trim($data['customer_data']['customer_name']) , 2);
            $transaction_date = explode(" ", $data['proposal_data']['txndate']);
            $trans_date = date("Y-m-d", strtotime($transaction_date[0]));
            $collectionMode = "online";
            $terminal_id = "EuxJCz8cZV9V63";
    		$mem_fr_id = "";
            $collection_amt['pay_amt'] = $data['proposal_data']['premium'];
            $query_quote = [];
            $source_name = $data['proposal_data']['HB_source_code'];
		
            if ($data['proposal_data']['HB_policy_type'] == 'ProposalWise')
            {
                $query_quote = $this
                    ->db
                    ->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and proposal_id = '" . $data['proposal_data']['proposal_id'] . "' and policy_subtype_id = '" . $data['proposal_data']['policy_sub_type_id'] . "' and status = 'success'")->row_array();

                if (empty($query_quote))
                {
                    $quote_data = $this->get_quote_data($emp_id, $policy_no);
                    if ($quote_data['status'] == 'Success')
                    {
                        $query_quote['QuotationNumber'] = $quote_data['msg'];
                    }
                }

            }
            else
            {

                $query_quote = $this
                    ->db
                    ->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and proposal_id = '" . $data['proposal_data']['proposal_id'] . "' and policy_subtype_id = '" . $mem_data[0]['policy_subtype_id'] . "' and fr_id = '" . $mem_data[0]['fr_id'] . "' and status = 'success'")->row_array();

                if (empty($query_quote))
                {
                    $quote_data = $this->get_quote_data($emp_id, $policy_no, $mem_data);
                    if ($quote_data['status'] == 'Success')
                    {
                        $query_quote['QuotationNumber'] = $quote_data['msg'];
                    }
                }

                $data['member_data'] = $mem_data;
                $collection_amt['pay_amt'] = $data['member_data'][0]['policy_mem_sum_premium'];
    			$mem_fr_id = $data['member_data'][0]['fr_id'];
                $concat_string = $data['proposal_data']['HB_custid_concat_string'];

                $data['customer_data']['cust_id'] = $data['customer_data']['cust_id'] . $concat_string . $data['member_data'][0]['key_id'];

            }

            if ($query_quote['QuotationNumber'])
            {
                // Combi Flag Logic Starts
                $policyDetail = $this->db->query("SELECT * FROM employee_policy_detail where policy_detail_id = ".$policy_no."")->row_array();
                if($policyDetail['is_parent'] == 0){
                    $parentPolicyId = $policyDetail['policy_detail_id'];
                } else {
                    $parentPolicyId = $policyDetail['is_parent'];
                }

    			$combi_check = $this
                    ->db
                    ->query('SELECT e.master_policy_no,e.master_policy_no,e.plan_code,pm.familyConstruct FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,proposal_member AS pm,family_relation AS fr, employee_details AS ed,payment_details as pd where epd.product_name = e.product_name AND ed.lead_id = "' . $data['customer_data']['lead_id'] . '"  AND epd.policy_detail_id = p.policy_detail_id AND p.id = pm.proposal_id AND e.policy_subtype_id = epd.policy_sub_type_id AND p.id = pd.proposal_id AND pm.family_relation_id = fr.family_relation_id AND fr.emp_id = ed.emp_id AND e.combo_flag = "Y" AND epd.is_combo = '.$parentPolicyId.' group by p.id')->result_array();
                
                $combi_count = 0;
                $combi_product_count = 0;
    			$no_of_product = count($combi_check);
    			$combi_flag = ($no_of_product > 1)?'1':'0';
                foreach ($combi_check as $key => $value)
                {
                    $check_adult = explode('A', $value['familyConstruct']);

                    if ($check_adult[0] == 1 && $data['proposal_data']['HB_policy_type'] == 'MemberWise')
                    {
                        $combi_count += 1;

                        if ($data['proposal_data']['master_policy_no'] == $value['master_policy_no'])
                        {
                            $combi_product_count += 1;
                        }

                    }
                    if ($check_adult[0] == 2 && $data['proposal_data']['HB_policy_type'] == 'MemberWise')
                    {
                        $combi_count += 2;

                        if ($data['proposal_data']['master_policy_no'] == $value['master_policy_no'])
                        {
                            $combi_product_count += 2;
                        }

                    }
                    if ($data['proposal_data']['HB_policy_type'] == 'ProposalWise')
                    {
                        $combi_count += 1;

                        if ($data['proposal_data']['master_policy_no'] == $value['master_policy_no'])
                        {
                            $combi_product_count += 1;
                        }

                    }
                }
                // Combi Flag Logic Ends
    			
    			if ($data['member_data'][$i]['fr_id'] == 2 || $data['member_data'][$i]['fr_id'] == 3)
                {
                    if ($data['member_data'][$i]['fr_id'] == 2 && $data['member_data'][$i]['gender'] != "Male")
                    {
                        $data['member_data'][$i]['relation_code'] = "R004";
                    }
                    elseif ($data['member_data'][$i]['fr_id'] == 3 && $data['member_data'][$i]['gender'] != "Female")
                    {
                        $data['member_data'][$i]['relation_code'] = "R003";
                    }
                }
    			
                $totalMembers = count($data['member_data']);
                $member = [];

                for ($i = 0;$i < $totalMembers;$i++)
                {
                    
                    $abc = ["PEDCode" => null, "Remarks" => null];
                    
                    $member[] = ["MemberNo" => $i + 1, "Salutation" => (($data['member_data'][$i]['gender'] == "Male") ? 'Mr' : (($data['member_data'][$i]['age'] >= 18) ? 'Mrs' : 'Ms')) , "First_Name" => $data['member_data'][$i]['firstname'], "Middle_Name" => null, "Last_Name" => !empty(trim($data['member_data'][$i]['lastname'])) ? $data['member_data'][$i]['lastname'] : '.', "Gender" => ($data['member_data'][$i]['gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['dob'])) , "Relation_Code" => trim($data['member_data'][$i]['relation_code']) , "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => "O553", "PrimaryMember" => (($i == 0) ? "Y" : "N") , "MemberproductComponents" => [["PlanCode" => $data['proposal_data']['plan_code'], "MemberQuestionDetails" => [["QuestionCode" => null, "Answer" => null, "Remarks" => null]]]], "MemberPED" => $abc, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $data['nominee_data']['nominee_fname'], "Nominee_Last_Name" => !empty(trim($data['nominee_data']['nominee_lname'])) ? $data['nominee_data']['nominee_lname'] : '.', "Nominee_Contact_Number" => $data['nominee_data']['nominee_contact'], "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data']['relation_code']) , ];
                }
                
                $fqrequest = ["ClientCreation" => ["Member_Customer_ID" => $data['customer_data']['cust_id'], "salutation" => $data['customer_data']['salutation'], "firstName" => $explode_name[0], "middleName" => "", "lastName" => !empty($explode_name[1]) ? $explode_name[1] : '.', "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['bdate'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['emp_pincode'], "uidNo" => $data['customer_data']['adhar'], "maritalStatus" => null, "nationality" => "Indian", "occupation" => "O553", "primaryEmailID" => $data['customer_data']['email'], "contactMobileNo" => substr(trim($data['customer_data']['mob_no']) , -10) , "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => null, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address'], "homeAddressLine2" => null, "homeAddressLine3" => null, "homePinCode" => $data['customer_data']['emp_pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => $query_quote['QuotationNumber'], "MasterPolicyNumber" => $data['proposal_data']['master_policy_no'], "GroupID" => $data['proposal_data']['group_code'], "Product_Code" => $data['proposal_data']['plan_code'], "SumInsured_Type" => null, "Policy_Tanure" => "1", "Member_Type_Code" => "M209", "intermediaryCode" => $data['proposal_data']['IMDCode'], "AutoRenewal" => $data['proposal_data']['si_auto_renewal'], "AutoDebit" => $data['proposal_data']['si_auto_renewal'], "intermediaryBranchCode" => "10MHMUM01", "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $data['customer_data']['lead_id'], "Source_Name" => $source_name, "SPID" => $data['customer_data']['ref2'], "TCN" => null, "CRTNO" => null, "RefCode1" => $data['customer_data']['ref1'], "RefCode2" => $data['customer_data']['branch_id'], "Employee_Number" => $data['proposal_data']['emp_id'], "enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "1", "PaymentMode" => null, "PolicyproductComponents" => [["PlanCode" => $data['proposal_data']['plan_code'], "SumInsured" => $data['proposal_data']['sum_insured'], "SchemeCode" => $data['customer_data']['scheme_code']]]], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "Mumbai", "modeOfEntry" => "Direct", "cdAcNo" => null, "expiryDate" => null, "payerType" => "Customer", "payerCode" => null, "paymentBy" => "Customer", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => round($collection_amt['pay_amt'], 2) , "collectionRcvdDate" => $trans_date, "collectionMode" => $collectionMode, "remarks" => null, "instrumentNumber" => $data['proposal_data']['TxRefNo'], "instrumentDate" => $trans_date, "bankName" => null, "branchName" => null, "bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => $data['proposal_data']['ifscCode'], "PaymentGatewayName" => $source_name, "TerminalID" => $terminal_id, "CardNo" => null], "CombiCreation" => ["Combi_flag" => $combi_flag, "Combi_identifier" => "leadid", "Combi_value" => $data['customer_data']['lead_id'], "Combi_code" => $source_name, "Combi_Count" => $combi_count, "Combi_product_count" => $combi_product_count]];
                $request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($fqrequest) , "product_id" => $data['customer_data']['product_id'], "type" => "full_quote_request2_".$data['proposal_data']['policy_sub_type_id']];
                $this
                    ->db
                    ->insert("logs_docs", $request_arr);
                $insert_id = $this
                    ->db
                    ->insert_id();

                $rel_code = array_column($member, 'Relation_Code');
                $rel_code = implode(',', $rel_code);
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
                $request_arr = ["res" => json_encode($response) ];
                $this
                    ->db
                    ->where("id", $insert_id);
                $this
                    ->db
                    ->update("logs_docs", $request_arr);

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
                    $premium = $newArr['premium'];
                    $return_data = [];

                    if ($errorObj['ErrorNumber'] == '00' || ($errorObj['ErrorNumber'] == '302' && $data['proposal_data']['master_policy_no'] == $newArr['policyDtls']['PolicyNumber']))
                    {

                        $return_data['status'] = 'Success';
                        $return_data['msg'] = $errorObj['ErrorMessage'];
    					
    					$create_policy_type = 0;
    					if($cron_policy_check){
    						$create_policy_type = 1;
    					}

                        $api_insert = array(
                            "emp_id" => $emp_id,
    						"proposal_id" => $data['proposal_data']['proposal_id'],
                            "member_fr_id" => $mem_fr_id,
                            "client_id" => $newArr['policyDtls']['ClientID'],
                            "certificate_number" => $newArr['policyDtls']['CertificateNumber'],
                            "quotation_no" => $newArr['policyDtls']['QuotationNumber'],
                            "proposal_no" => $newArr['policyDtls']['ProposalNumber'],
                            "policy_no" => $newArr['policyDtls']['PolicyNumber'],
                            "gross_premium" => empty($premium['GrossPremium']) ? '' : $premium['GrossPremium'],
                            "status" => "Success",
                            //"status" => $errorObj['ErrorMessage'],
                            "start_date" => $newArr['policyDtls']['startDate'],
                            "end_date" => $newArr['policyDtls']['EndDate'],
                            "created_date" => date('Y-m-d H:i:s') ,
                            "proposal_no_lead" => $data['proposal_data']['proposal_no'],
                            "PolicyStatus" => $newArr['policyDtls']['PolicyStatus'],
                            "letter_url" => $newArr['policyDtls']['LetterURL'],
                            "CustomerID" => $newArr['policyDtls']['MemberCustomerID'],
                            "MemberCustomerID" => $newArr['policyDtls']['MemberCustomerID'],
    						"ReceiptNumber" => $newArr['receiptObj']['ReceiptNumber'],
                            "relationship_code" => $rel_code,
    						"create_policy_type" => $create_policy_type,
                        );

                        $this
                            ->db
                            ->insert('api_proposal_response', $api_insert);


                        $request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($api_insert) , "product_id" => $data['proposal_data']['product_code'], "type" => "api_insert"];
                        $dataArray['tablename'] = 'logs_docs';
                        $dataArray['data'] = $request_arr;
                        $this
                            ->Logs_m
                            ->insertLogs($dataArray);

                    }
                    else
                    {
    					///------- @author : Guru --------------------------//
                        $request_failure_arr = ["lead_id" => $data['customer_data']['lead_id'], "product_id" => $data['customer_data']['product_id'], "res" => $errorObj['ErrorMessage'], "type" => "failure_reason_".$data['proposal_data']['policy_sub_type_id']];
                        $dataArray['tablename'] = 'logs_docs';
                        $dataArray['data'] = $request_failure_arr;
                        $this
                            ->Logs_m
                            ->insertLogs($dataArray);


                        $return_data = array(
                            'status' => 'error',
                            "msg" => $errorObj['ErrorMessage']
                        );
                    }

                    return $return_data;

                }

            }
            else
            {
    			$extra_arr_update = ["is_policy_issue_initiated" => 0];
                    $this
                        ->db
                        ->where("emp_id", $emp_id);
                    $this
                        ->db
                        ->update("employee_details", $extra_arr_update);

                return $return_data = array(
                    'status' => 'error',
                    "msg" => "Quote error"
                );
            }
        }
    }

    function GHI_GCI_api_call($emp_id, $policy_no, $mem_data = '',$cron_policy_check = ''){
        // Check if policy has combined COI STARTS
        $policy_detail_id_arr = explode(',',$policy_no);
        // Check if policy has combined COI ENDS
        $extra_check_data = $this
            ->db
            ->query("select pd.payment_status,pd.TxRefNo,pd.TxStatus,ed.is_policy_issue_initiated from proposal as p,employee_details as ed,payment_details as pd  where ed.emp_id = p.emp_id and p.id = pd.proposal_id and ed.emp_id ='" . $emp_id . "'")->row_array();
        
        // print_pre($extra_check_data); 
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
            $data['member_data'] = (array)$this->get_all_member_data($emp_id, $policy_detail_id_arr[0]);
            $data['nominee_data'] = (array)$this->get_all_nominee($emp_id);
            $data['proposal_data'] = (array)$this->getProposalData($emp_id, $policy_detail_id_arr[0]);
            // print_pre($data);exit;

            $data['combined_coi_different_data'] = (array)$this->getCombinedCoiDifferentData($emp_id,$policy_detail_id_arr);
            // print_pre($data['combined_coi_different_data']);

            $url = trim($data['proposal_data']['api_url']);
            if ($url == '')
            {
                return array(
                    "status" => "error",
                    "msg" => "Something Went Wrong"
                );
            }

            $explode_name = explode(" ", trim($data['customer_data']['customer_name']) , 2);
            $transaction_date = explode(" ", $data['proposal_data']['txndate']);
            $trans_date = date("Y-m-d", strtotime($transaction_date[0]));
            $instrumentDate = date("Y-m-d", strtotime($transaction_date[0]));
            if($data['customer_data']['product_id'] == 'HERO_FINCORP'){
                $instrumentDate = date("m/d/Y", strtotime($transaction_date[0]));
            }
            $collectionMode = "online";
            $terminal_id = "EuxJCz8cZV9V63";
            $mem_fr_id = "";
            // $collection_amt['pay_amt'] = $data['proposal_data']['premium'];
            $query_quote = [];
            $source_name = $data['proposal_data']['HB_source_code'];
        
            if ($data['proposal_data']['HB_policy_type'] == 'ProposalWise')
            {
                $query_quote = $this
                    ->db
                    ->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and proposal_id = '" . $data['proposal_data']['proposal_id'] . "' and policy_subtype_id = '" . $data['proposal_data']['policy_sub_type_id'] . "' and status = 'success'")->row_array();

                if (empty($query_quote))
                {
                    $quote_data = $this->get_quote_data($emp_id, $policy_no);
                    if ($quote_data['status'] == 'Success')
                    {
                        $query_quote['QuotationNumber'] = $quote_data['msg'];
                    }
                }

                $concat_string = $data['proposal_data']['HB_custid_concat_string'];
                $data['customer_data']['cust_id'] = $data['customer_data']['cust_id'].$concat_string;
                if(count($policy_detail_id_arr) > 1){
                    foreach ($data['combined_coi_different_data'] as $key => $value) {
                        $collection_amt['pay_amt'] += $value['premium'];
                    }
                } else {
                    $collection_amt['pay_amt'] = $data['proposal_data']['premium'];
                }
                $mem_fr_id = 0;   // To avoid DB error
            } else {

                $query_quote = $this
                    ->db
                    ->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and proposal_id = '" . $data['proposal_data']['proposal_id'] . "' and policy_subtype_id = '" . $mem_data[0]['policy_subtype_id'] . "' and fr_id = '" . $mem_data[0]['fr_id'] . "' and status = 'success'")->row_array();

                if (empty($query_quote))
                {
                    $quote_data = $this->get_quote_data($emp_id, $policy_no, $mem_data);
                    if ($quote_data['status'] == 'Success')
                    {
                        $query_quote['QuotationNumber'] = $quote_data['msg'];
                    }
                }

                $data['member_data'] = $mem_data;
                $collection_amt['pay_amt'] = $data['member_data'][0]['policy_mem_sum_premium'];
                $mem_fr_id = $data['member_data'][0]['fr_id'];
                $concat_string = $data['proposal_data']['HB_custid_concat_string'];

                $data['customer_data']['cust_id'] = $data['customer_data']['cust_id'] . $concat_string . $data['member_data'][0]['key_id'];

            }
            if ($query_quote['QuotationNumber'])
            {
                // Combi Flag Logic Starts
                $policyDetail = $this->db->query("SELECT * FROM employee_policy_detail where policy_detail_id = ".$policy_detail_id_arr[0]."")->row_array();
                if($policyDetail['is_parent'] == 0){
                    $parentPolicyId = $policyDetail['policy_detail_id'];
                } else {
                    $parentPolicyId = $policyDetail['is_parent'];
                }

                $combi_check = $this
                    ->db
                    ->query('SELECT e.master_policy_no,e.master_policy_no,e.plan_code,pm.familyConstruct FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,proposal_member AS pm,family_relation AS fr, employee_details AS ed,payment_details as pd where epd.product_name = e.product_name AND ed.lead_id = "' . $data['customer_data']['lead_id'] . '"  AND epd.policy_detail_id = p.policy_detail_id AND p.id = pm.proposal_id AND e.policy_subtype_id = epd.policy_sub_type_id AND p.id = pd.proposal_id AND pm.family_relation_id = fr.family_relation_id AND fr.emp_id = ed.emp_id AND e.combo_flag = "Y" AND epd.is_combo = '.$parentPolicyId.' group by p.id')->result_array();
                
                $combi_count = 0;
                $combi_product_count = 0;
                $no_of_product = count($combi_check);
                $combi_flag = ($no_of_product > 1)?'1':'0';

                if($data['customer_data']['product_id'] == 'ABML'){
                    $combi_flag = '0';
                }
                
                foreach ($combi_check as $key => $value)
                {
                    $check_adult = explode('A', $value['familyConstruct']);

                    if ($check_adult[0] == 1 && $data['proposal_data']['HB_policy_type'] == 'MemberWise')
                    {
                        $combi_count += 1;

                        if ($data['proposal_data']['master_policy_no'] == $value['master_policy_no'])
                        {
                            $combi_product_count += 1;
                        }

                    }
                    if ($check_adult[0] == 2 && $data['proposal_data']['HB_policy_type'] == 'MemberWise')
                    {
                        $combi_count += 2;

                        if ($data['proposal_data']['master_policy_no'] == $value['master_policy_no'])
                        {
                            $combi_product_count += 2;
                        }

                    }
                    if ($data['proposal_data']['HB_policy_type'] == 'ProposalWise')
                    {
                        $combi_count += 1;

                        if ($data['proposal_data']['master_policy_no'] == $value['master_policy_no'])
                        {
                            $combi_product_count += 1;
                        }

                    }
                }
                // Combi Flag Logic Ends

                if ($data['member_data'][$i]['fr_id'] == 2 || $data['member_data'][$i]['fr_id'] == 3 || $data['member_data'][$i]['fr_id'] == 25 || $data['member_data'][$i]['fr_id'] == 26)
                {
                    // if ($data['member_data'][$i]['fr_id'] == 2 && $data['member_data'][$i]['gender'] != "Male")
                    // {
                    //     $data['member_data'][$i]['relation_code'] = "R004";
                    // }
                    // elseif ($data['member_data'][$i]['fr_id'] == 3 && $data['member_data'][$i]['gender'] != "Female")
                    // {
                    //     $data['member_data'][$i]['relation_code'] = "R003";
                    // }

                    // New Relation Code Logic in Kid
                    if($data['member_data'][$i]['gender'] == "Male"){
                        $data['member_data'][$i]['relation_code'] = "R003";
                    }else if($data['member_data'][$i]['gender'] == "Female"){
                        $data['member_data'][$i]['relation_code'] = "R004";
                    }else {
                        $data['member_data'][$i]['relation_code'] = ""; 
                    }
                }

                // Plan Code & Sum Insured for combined COI
                $totalPlanCodes = count($data['combined_coi_different_data']);
                $planCodeSi = [];
                $memberObjPlanCode = [];
                for ($i = 0;$i < $totalPlanCodes;$i++) {
                    $planCodeSi[] = [
                        "PlanCode" => $data['combined_coi_different_data'][$i]['plan_code'], 
                        "SumInsured" => $data['combined_coi_different_data'][$i]['sum_insured'], 
                        "SchemeCode" => $data['customer_data']['scheme_code']
                    ];
                    $memberObjPlanCode[] = [
                        "PlanCode" => $data['combined_coi_different_data'][$i]['plan_code'], 
                        "MemberQuestionDetails" => [
                            [
                                "QuestionCode" => null, "Answer" => null, 
                                "Remarks" => null
                            ]
                        ]
                    ];
                }
                // Plan Code & Sum Insured for combined COI
                
                $totalMembers = count($data['member_data']);
                $member = [];
                // member_fr_id Arr
                $memFrIdArr = [];

                for ($i = 0;$i < $totalMembers;$i++)
                {
                    $memFrIdArr[$i] = $data['member_data'][$i]['fr_id'];

                    if ($data['member_data'][$i]['fr_id'] == 2 || $data['member_data'][$i]['fr_id'] == 3 || $data['member_data'][$i]['fr_id'] == 25 || $data['member_data'][$i]['fr_id'] == 26)
                    {
                        // if ($data['member_data'][$i]['fr_id'] == 2 && $data['member_data'][$i]['gender'] != "Male")
                        // {
                        //     $data['member_data'][$i]['relation_code'] = "R004";
                        // }
                        // elseif ($data['member_data'][$i]['fr_id'] == 3 && $data['member_data'][$i]['gender'] != "Female")
                        // {
                        //     $data['member_data'][$i]['relation_code'] = "R003";
                        // }

                        // New Relation Code Logic in Kid
                        if($data['member_data'][$i]['gender'] == "Male"){
                            $data['member_data'][$i]['relation_code'] = "R003";
                        }else if($data['member_data'][$i]['gender'] == "Female"){
                            $data['member_data'][$i]['relation_code'] = "R004";
                        }else {
                            $data['member_data'][$i]['relation_code'] = ""; 
                        }
                    }
                    
                    $abc = ["PEDCode" => null, "Remarks" => null];
                        
                    $explode_name_member = explode(" ", trim($data['member_data'][$i]['firstname']) , 2);

                    $member[] = ["MemberNo" => $i + 1, "Salutation" => (($data['member_data'][$i]['gender'] == "Male") ? 'Mr' : (($data['member_data'][$i]['age'] >= 18) ? 'Mrs' : 'Ms')) , "First_Name" => $explode_name_member[0], "Middle_Name" => null, "Last_Name" => !empty($explode_name_member[1]) ? $explode_name_member[1] : '.', "Gender" => ($data['member_data'][$i]['gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['dob'])) , "Relation_Code" => trim($data['member_data'][$i]['relation_code']) , "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => "O553", "PrimaryMember" => (($i == 0) ? "Y" : "N") , "MemberproductComponents" => $memberObjPlanCode, "MemberPED" => $abc, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $data['nominee_data']['nominee_fname'], "Nominee_Last_Name" => !empty(trim($data['nominee_data']['nominee_lname'])) ? $data['nominee_data']['nominee_lname'] : '.', "Nominee_Contact_Number" => $data['nominee_data']['nominee_contact'], "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data']['relation_code']) , ];
                }
                
                $fqrequest = ["ClientCreation" => ["Member_Customer_ID" => $data['customer_data']['cust_id'], "salutation" => $data['customer_data']['salutation'], "firstName" => $explode_name[0], "middleName" => "", "lastName" => !empty($explode_name[1]) ? $explode_name[1] : '.', "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['bdate'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['emp_pincode'], "uidNo" => $data['customer_data']['adhar'], "maritalStatus" => null, "nationality" => "Indian", "occupation" => "O553", "primaryEmailID" => $data['customer_data']['email'], "contactMobileNo" => substr(trim($data['customer_data']['mob_no']) , -10) , "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => null, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address'], "homeAddressLine2" => null, "homeAddressLine3" => null, "homePinCode" => $data['customer_data']['emp_pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => $query_quote['QuotationNumber'], "MasterPolicyNumber" => $data['proposal_data']['master_policy_no'], "GroupID" => $data['proposal_data']['group_code'], "Product_Code" => $data['proposal_data']['plan_code'], "SumInsured_Type" => null, "Policy_Tanure" => "1", "Member_Type_Code" => "M209", "intermediaryCode" => $data['proposal_data']['IMDCode'], "AutoRenewal" => $data['proposal_data']['si_auto_renewal'],"AutoDebit" => $data['proposal_data']['si_auto_renewal'], "intermediaryBranchCode" => "10MHMUM01", "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $data['customer_data']['lead_id'], "Source_Name" => $source_name, "SPID" => $data['customer_data']['ref2'], "TCN" => null, "CRTNO" => null, "RefCode1" => $data['customer_data']['ref1'], "RefCode2" => $data['customer_data']['branch_id'], "Employee_Number" => $data['proposal_data']['emp_id'], "enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "1", "PaymentMode" => null, "PolicyproductComponents" => $planCodeSi], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "Mumbai", "modeOfEntry" => "Direct", "cdAcNo" => null, "expiryDate" => null, "payerType" => "Customer", "payerCode" => null, "paymentBy" => "Customer", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => round($collection_amt['pay_amt'], 2) , "collectionRcvdDate" => $trans_date, "collectionMode" => $collectionMode, "remarks" => null, "instrumentNumber" => $data['proposal_data']['TxRefNo'], "instrumentDate" => $instrumentDate, "bankName" => null, "branchName" => null, "bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => $data['proposal_data']['ifscCode'], "PaymentGatewayName" => $source_name, "TerminalID" => $terminal_id, "CardNo" => null], "CombiCreation" => ["Combi_flag" => $combi_flag, "Combi_identifier" => "leadid", "Combi_value" => $data['customer_data']['lead_id'], "Combi_code" => $source_name, "Combi_Count" => $combi_count, "Combi_product_count" => $combi_product_count]];

                if($data['customer_data']['product_id'] == 'HERO_FINCORP'){
                    $fqrequest["PolicyCreationRequest"]["goGreen"] = $data['customer_data']['go_green_flag'];
                }

                $request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($fqrequest) , "product_id" => $data['customer_data']['product_id'], "type" => "full_quote_request2_".$data['proposal_data']['policy_sub_type_id']];
                $this
                    ->db
                    ->insert("logs_docs", $request_arr);
                $insert_id = $this
                    ->db
                    ->insert_id();

                $rel_code = array_column($member, 'Relation_Code');
                $rel_code = implode(',', $rel_code);


                // fr_id store in api_proposal_response tbl
                $fr_ids = implode(",", $memFrIdArr);


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

                $request_arr = ["res" => json_encode($response) ];
                $this
                    ->db
                    ->where("id", $insert_id);
                $this
                    ->db
                    ->update("logs_docs", $request_arr);

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
                    $premium = $newArr['premium'];
                    $return_data = [];

                    if ($errorObj['ErrorNumber'] == '00' || ($errorObj['ErrorNumber'] == '302' && $data['proposal_data']['master_policy_no'] == $newArr['policyDtls']['PolicyNumber']))
                    {

                        $return_data['status'] = 'Success';
                        $return_data['msg'] = $errorObj['ErrorMessage'];
                        
                        $create_policy_type = 0;
                        if($cron_policy_check){
                            $create_policy_type = 1;
                        }

                        foreach ($policy_detail_id_arr as $key => $policyDetailId) {
                            // Find Proposal ID Start
                            $proposalRecord = $this->db->query("SELECT * from proposal as p where p.emp_id = '".$emp_id."' AND policy_detail_id = ".$policyDetailId)->row_array();
                            // Find Proposal ID End
                            $api_insert = array(
                                "emp_id" => $emp_id,
                                // "proposal_id" => $data['proposal_data']['proposal_id'],
                                "proposal_id" => $proposalRecord['id'],
                                "member_fr_id" => $mem_fr_id,
                                "client_id" => $newArr['policyDtls']['ClientID'],
                                "certificate_number" => $newArr['policyDtls']['CertificateNumber'],
                                "quotation_no" => $newArr['policyDtls']['QuotationNumber'],
                                "proposal_no" => $newArr['policyDtls']['ProposalNumber'],
                                "policy_no" => $newArr['policyDtls']['PolicyNumber'],
                                "gross_premium" => empty($premium['GrossPremium']) ? '' : $premium['GrossPremium'],
                                "status" => "Success",
                                //"status" => $errorObj['ErrorMessage'],
                                "start_date" => $newArr['policyDtls']['startDate'],
                                "end_date" => $newArr['policyDtls']['EndDate'],
                                "created_date" => date('Y-m-d H:i:s') ,
                                "proposal_no_lead" => $proposalRecord['proposal_no'],
                                "PolicyStatus" => $newArr['policyDtls']['PolicyStatus'],
                                "letter_url" => $newArr['policyDtls']['LetterURL'],
                                "CustomerID" => $newArr['policyDtls']['MemberCustomerID'],
                                "MemberCustomerID" => $newArr['policyDtls']['MemberCustomerID'],
                                "ReceiptNumber" => $newArr['receiptObj']['ReceiptNumber'],
                                "COI_url" => $newArr['policyDtls']['COIUrl'],
                                "relationship_code" => $rel_code,
                                "fr_ids" => $fr_ids,
                                "create_policy_type" => $create_policy_type,
                            );

                            $this
                                ->db
                                ->insert('api_proposal_response', $api_insert);

                            /* Call COI generate alert in case of Hero FInCorp only*/
                            if($data['customer_data']['product_id'] == 'HERO_FINCORP'){
                                // Call COI alert function here
                                $this->send_coi_alert($data['customer_data']['lead_id'],$newArr['policyDtls']['CertificateNumber']);
                            }

                            /* Call COI generate API in case of MUTHOOT only*/
                            if($data['customer_data']['product_id'] == 'MUTHOOT'){
                                // Call COI alert function here
                                $this->updatePolicyStatusResponse($data['customer_data']['lead_id'],$newArr['policyDtls']['CertificateNumber']);
                            }
                        }

                        $request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($api_insert) , "product_id" => $data['proposal_data']['product_code'], "type" => "api_insert"];
                        $dataArray['tablename'] = 'logs_docs';
                        $dataArray['data'] = $request_arr;
                        $this
                            ->Logs_m
                            ->insertLogs($dataArray);

                    }
                    else
                    {
                        ///------- @author : Guru --------------------------//
                        $request_failure_arr = ["lead_id" => $data['customer_data']['lead_id'], "product_id" => $data['customer_data']['product_id'], "res" => $errorObj['ErrorMessage'], "type" => "failure_reason_".$data['proposal_data']['policy_sub_type_id']];
                        $dataArray['tablename'] = 'logs_docs';
                        $dataArray['data'] = $request_failure_arr;
                        $this
                            ->Logs_m
                            ->insertLogs($dataArray);


                        $return_data = array(
                            'status' => 'error',
                            "msg" => $errorObj['ErrorMessage']
                        );
                    }

                    return $return_data;

                }
            }
            else
            {
                $extra_arr_update = ["is_policy_issue_initiated" => 0];
                    $this
                        ->db
                        ->where("emp_id", $emp_id);
                    $this
                        ->db
                        ->update("employee_details", $extra_arr_update);

                return $return_data = array(
                    'status' => 'error',
                    "msg" => "Quote error"
                );
            }
        }  
    }

    function policy_creation_call($CRM_Lead_Id,$cron_policy_check = '')
    {
        // print_pre("ayay");exit;
        /*$update_data = $this
            ->db
            ->query('SELECT p.id,p.emp_id,p.policy_detail_id,p.status,p.count,e.policy_subtype_id,e.product_code,e.HB_policy_type
		FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,
		employee_details AS ed
		where epd.product_name = e.product_name
		AND p.emp_id = ed.emp_id
		AND ed.lead_id = "' . $CRM_Lead_Id . '"
		AND epd.policy_detail_id = p.policy_detail_id');*/

        // $update_data = $this->db->query('SELECT ed.emp_id,mpst.policy_subtype_id,p.policy_detail_id,p.id,p.status,p.count,mpst.HB_policy_type FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.product_name AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.lead_id="' . $CRM_Lead_Id . '" AND epd.is_parent = 0 UNION ALL SELECT ed.emp_id,GROUP_CONCAT(mpst.policy_subtype_id) as policy_subtype_id,GROUP_CONCAT(p.policy_detail_id) as policy_detail_id, GROUP_CONCAT(p.id) as id,GROUP_CONCAT(p.status) as status,GROUP_CONCAT(p.count) as count,mpst.HB_policy_type FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.product_name AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.lead_id="' . $CRM_Lead_Id . '" GROUP BY epd.is_parent HAVING epd.is_parent != 0');

        $employeeDetail = $this->db->query("SELECT * from employee_details where lead_id = ".$CRM_Lead_Id)->row_array();

        if($employeeDetail['product_id'] == 'ABML'){
            // For ABML
            $update_data = $this->db->query('SELECT ed.emp_id,GROUP_CONCAT(mpst.policy_subtype_id) as policy_subtype_id,GROUP_CONCAT(p.policy_detail_id) as policy_detail_id, GROUP_CONCAT(p.id) as id,GROUP_CONCAT(p.status) as status,GROUP_CONCAT(p.count) as count,mpst.HB_policy_type FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.product_name AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.lead_id="' . $CRM_Lead_Id . '" GROUP BY epd.is_combo');
        } else {
            // For Rest Products
            $update_data = $this->db->query('SELECT ed.emp_id,mpst.policy_subtype_id,p.policy_detail_id,p.id,p.status,p.count,mpst.HB_policy_type FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.product_name AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.lead_id="' . $CRM_Lead_Id . '" AND epd.is_parent = 0 UNION ALL SELECT ed.emp_id,GROUP_CONCAT(mpst.policy_subtype_id) as policy_subtype_id,GROUP_CONCAT(p.policy_detail_id) as policy_detail_id, GROUP_CONCAT(p.id) as id,GROUP_CONCAT(p.status) as status,GROUP_CONCAT(p.count) as count,mpst.HB_policy_type FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.product_name AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.lead_id="' . $CRM_Lead_Id . '" GROUP BY epd.is_parent HAVING epd.is_parent != 0');
        }

        $update_data = $update_data->result_array();

        // print_pre($update_data);exit;
        
		//update payment confirmation hit count
		$this->db->query("UPDATE proposal SET count = count + 1 WHERE emp_id ='".$update_data[0]['emp_id']."'");
        // GHI,GCS,GPA,GCI policy creation start
        $combiArr = [];

        foreach ($update_data as $update_payment)
        {
            // Check Combi COI request Starts
            $combiArr['id'] = explode(',',$update_payment['id']);
            $combiArr['policy_subtype_id'] = explode(',',$update_payment['policy_subtype_id']);
            $combiArr['policy_detail_id'] = explode(',',$update_payment['policy_detail_id']);
            $combiArr['status'] = explode(',',$update_payment['status']);
            $combiArr['count'] = explode(',',$update_payment['count']);
            $combiArr['HB_policy_type'] = explode(',',$update_payment['HB_policy_type']);

            $combiProposalCount = count($combiArr['id']);
            // print_pre($combiArr);exit;
            // Check Combi COI request Ends
            if($combiArr['status'][0] != 'Success')
            {              
                // check first hit or not
                if($combiArr['count'][0] < 3)
                {

                    // update proposal status - Payment Received
                    $arr_new = ["status" => "Payment Received"];
                    $this
                        ->db
                        ->where_in('id', $combiArr['id']);
                    $this
                        ->db
                        ->update("proposal", $arr_new);

                    // For GHI,GPA,GCI policy check
                    $query = $this
                        ->db
                        ->query("select policy_detail_id from employee_policy_detail where policy_detail_id IN (" . $update_payment['policy_detail_id'] . ") and policy_sub_type_id in (1,2,3,8)")->row_array();
                    // print_pre($update_payment);print_pre($query);exit;
                    if ($query)
                    {
                        $api_response_tbl['status'] = 'error';
                        
                        if ($update_payment['HB_policy_type'] == 'ProposalWise')
                        {
                            $api_response_tbl = $this->GHI_GCI_api_call($update_payment['emp_id'], $update_payment['policy_detail_id'],'',$cron_policy_check);
                        }
                        else
                        {
                            $api_response_tbl = $this->Memberwise_policy_call($update_payment['emp_id'], $update_payment['policy_detail_id'],$cron_policy_check);  
                        }
                        $api_response_tbl['msg'] = (isset($api_response_tbl['msg'])) ? $api_response_tbl['msg'] : '';

                        if ($api_response_tbl['status'] == 'error' || strtolower($api_response_tbl) == 'null')
                        {
                            $return_data['check'][] = 'error';
                            $return_data['code'] = '0';
                            $return_data['msg'] = $api_response_tbl['msg'];

                        }
                        else
                        {
                            $arr = ["status" => "Success"];
                            $this
                                ->db
                                ->where_in('id', $combiArr['id']);
                            $this
                                ->db
                                ->update("proposal", $arr);

                            $return_data['check'][] = 'Success';
                            $return_data['code'] = '1';
                            $return_data['msg'] = $api_response_tbl['msg'];
                        }

                    }

                }
                else
                {
                    $return_data['check'][] = 'error';
                    $return_data['code'] = '0';
                    $return_data['msg'] = '3 times fail count exceeded';

                }

            }
            else
            {
                $return_data['check'][] = 'Success';
                $return_data['code'] = '2';
                $return_data['msg'] = 'Already genarate';
            }

        }
        
        if (in_array("error", $return_data['check']))
        {
            $data = array(
                'Status' => 'error',
                'ErrorCode' => '0',
                'ErrorDescription' => $return_data['msg'],
            );
        }
        else
        {
            $data = array(
                'Status' => 'Success',
                'ErrorCode' => $return_data['code'],
                'ErrorDescription' => $return_data['msg'],
            );
        }

        return $data;

    }

    //*************************************************************************************************
    

    function get_profile($emp_id)
    {

        return $this
            ->db
            // ->query("select e.* from employee_details as e left join master_salutation as m ON e.salutation = m.s_id where e.emp_id='$emp_id'")->row();
            ->query("SELECT e.*,mpm.scheme_code FROM employee_details AS e LEFT JOIN master_salutation AS m ON e.salutation = m.s_id LEFT JOIN main_product_master AS mpm ON e.product_id = mpm.main_product_id WHERE e.emp_id = '$emp_id'")->row();

    }

    function getProposalData($emp_id, $policy_no)
    {
        $memberwise_check = $this
            ->db
            ->query('SELECT e.HB_policy_type from product_master_with_subtype AS e,employee_policy_detail epd where e.product_name = epd.product_name and e.policy_subtype_id = epd.policy_sub_type_id and  epd.policy_detail_id = "' . $policy_no . '"')->row_array() ['HB_policy_type'];

        if ($memberwise_check == 'ProposalWise')
        {
            $extra_condition = 'AND pm.familyConstruct = mgc.family_construct';
        }
        else
        {
            $extra_condition = 'AND mgc.family_construct = "1A"';
        }
        $query = $this
            ->db
            ->query('SELECT p.created_date,p.id,p.IMDCode,p.proposal_no,p.emp_id,p.sum_insured,p.premium,p.si_auto_renewal,epd.policy_no,mgc.group_code,mgc.spouse_group_code,e.master_policy_no,e.product_name,pd.txndate,pd.payment_type,e.plan_code,e.api_url,e.product_code,pd.TxRefNo,pd.bank_name,epd.start_date,p.branch_code,pm.familyConstruct,epd.policy_sub_type_id,pd.payment_status,e.SourceSystemName_api,p.id as proposal_id,p.policy_detail_id,e.HB_source_code,e.HB_policy_type,e.HB_custid_concat_string
						FROM 
						product_master_with_subtype AS e,employee_policy_detail AS epd,
						proposal AS p,proposal_member AS pm,family_relation AS fr, 
						employee_details AS ed,master_group_code AS mgc,
						payment_details as pd
						where 
						epd.product_name = e.product_name 
						AND p.emp_id = "' . $emp_id . '"
             			AND p.policy_detail_id = "' . $policy_no . '"
						AND epd.policy_detail_id = p.policy_detail_id 
						AND p.id = pm.proposal_id 
						AND e.policy_subtype_id = epd.policy_sub_type_id 
						' . $extra_condition . '
						AND p.sum_insured = mgc.si_group 
						AND e.product_code = mgc.product_code 
						AND p.id = pd.proposal_id 
						AND pm.family_relation_id = fr.family_relation_id 
						AND fr.emp_id = ed.emp_id group by p.id');

        // echo $this->db->last_query();exit;
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
        $response = $this
            ->db
            ->query('SELECT epm.family_relation_id,e.policy_subtype_id,epd.policy_detail_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,"Self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type","R001" as relation_code,e.plan_code FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS epm,family_relation AS fr,employee_details AS ed WHERE e.policy_subtype_id = epd.policy_sub_type_id AND epd.policy_detail_id = epm.policy_detail_id AND epm.family_relation_id = fr.family_relation_id AND fr.family_id = 0 AND fr.emp_id = ed.emp_id AND ed.emp_id = ' . $emp_id . '
		AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' GROUP BY epm.policy_member_id UNION all SELECT epm.family_relation_id,e.policy_subtype_id,epd.policy_detail_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
		epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type",mfr.relation_code,e.plan_code
		FROM 
		product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS epm,family_relation AS fr,employee_family_details AS efd,
		master_family_relation AS mfr
		WHERE e.policy_subtype_id = epd.policy_sub_type_id AND epd.policy_detail_id = epm.policy_detail_id
		AND epm.family_relation_id = fr.family_relation_id
		AND fr.family_id = efd.family_id 
		AND efd.fr_id = mfr.fr_id AND `epd`.`policy_detail_id` = ' . $policy_detail_id . '
		AND fr.emp_id = ' . $emp_id . ' GROUP BY epm.policy_member_id')->result_array();

        //echo $this->db->last_query();
        return $response;
    }

    function getCombinedCoiDifferentData($emp_id,$policyDetailArr){
        $memberwise_check = $this
            ->db
            ->query('SELECT e.HB_policy_type from product_master_with_subtype AS e,employee_policy_detail epd where e.product_name = epd.product_name and e.policy_subtype_id = epd.policy_sub_type_id and  epd.policy_detail_id = "' . $policyDetailArr[0] . '"')->row_array() ['HB_policy_type'];

        if ($memberwise_check == 'ProposalWise')
        {
            $extra_condition = 'AND pm.familyConstruct = mgc.family_construct';
        }
        else
        {
            $extra_condition = 'AND mgc.family_construct = "1A"';
        }
        $query = $this
            ->db
            ->query('SELECT p.created_date,p.id,p.IMDCode,p.proposal_no,p.emp_id,p.sum_insured,p.premium,epd.policy_no,mgc.group_code,mgc.spouse_group_code,e.master_policy_no,e.product_name,pd.txndate,pd.payment_type,e.plan_code,e.api_url,e.product_code,pd.TxRefNo,pd.bank_name,epd.start_date,p.branch_code,pm.familyConstruct,epd.policy_sub_type_id,pd.payment_status,e.SourceSystemName_api,p.id as proposal_id,p.policy_detail_id,e.HB_source_code,e.HB_policy_type,e.HB_custid_concat_string
                        FROM 
                        product_master_with_subtype AS e,employee_policy_detail AS epd,
                        proposal AS p,proposal_member AS pm,family_relation AS fr, 
                        employee_details AS ed,master_group_code AS mgc,
                        payment_details as pd
                        where 
                        epd.product_name = e.product_name 
                        AND p.emp_id = "' . $emp_id . '"
                        AND p.policy_detail_id IN (' . implode(',',$policyDetailArr) . ')
                        AND epd.policy_detail_id = p.policy_detail_id 
                        AND p.id = pm.proposal_id 
                        AND e.policy_subtype_id = epd.policy_sub_type_id 
                        ' . $extra_condition . '
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

    function Memberwise_policy_call($emp_id, $policy_detail_id,$cron_policy_check = '')
    {
        $member_data = (array)$this->get_all_member_data($emp_id, $policy_detail_id);
        //print_r($member_data);exit;
        $query = $this
            ->db
            ->query("select multi_status from proposal where emp_id='$emp_id' and policy_detail_id = '$policy_detail_id'")->row_array();
        $update_arr = ["emp_id" => $emp_id, "policy_detail_id" => $policy_detail_id];

        if (empty($query['multi_status']))
        {

            if ($member_data[0]['familyConstruct'] == '1A')
            {
                $arr = ["multi_status" => '0'];
            }
            else
            {
                $arr = ["multi_status" => '0,0'];
            }

            $this
                ->db
                ->where($update_arr);
            $this
                ->db
                ->update("proposal", $arr);
        }

        $query_again = $this
            ->db
            ->query("select multi_status from proposal where emp_id='$emp_id' and policy_detail_id = '$policy_detail_id'")->row_array();

        $status_arr = explode(",", $query_again['multi_status'], 2);

        foreach ($member_data as $key => $value)
        {
            $value['key_id'] = $key + 1;
            $mem_data[0] = $value;
            //print_r($value);
            if ($status_arr[$key] == 0)
            {
                $policy_data = $this->GHI_GCI_api_call($emp_id, $policy_detail_id, $mem_data,$cron_policy_check);

                if ($policy_data['status'] == 'Success')
                {
                    $status_arr[$key] = 1;

                    $status_string = implode(",", $status_arr);

                    $arr = ["multi_status" => $status_string];

                    $this
                        ->db
                        ->where($update_arr);
                    $this
                        ->db
                        ->update("proposal", $arr);

                }

            }

        }

        $query_last = $this
            ->db
            ->query("select multi_status from proposal where emp_id='$emp_id' and policy_detail_id = '$policy_detail_id'")->row_array();

        if ($member_data[0]['familyConstruct'] == '1A')
        {
            $proposal_status = ($query_last['multi_status'] == '1') ? 'Success' : 'error';
        }

        if ($member_data[0]['familyConstruct'] == '2A')
        {
            $proposal_status = ($query_last['multi_status'] == '1,1') ? 'Success' : 'error';
        }

        return $return_data = array(
            'status' => $proposal_status,
            "msg" => "GPA GCI GP call"
        );

    }

    function get_all_nominee($emp_id)
    {
        $response = $this
            ->db
            ->select('*,mfr.relation_code,mfr.nominee_type as fr_name')
            ->from('member_policy_nominee AS mpn,master_nominee as mfr')
            ->where('mpn.emp_id', $emp_id)->where('mpn.fr_id = mfr.nominee_id')
            ->get()
            ->row_array();
        if ($response)
        {
            return $response;
        }
    }

    function emandate_HB_call($emp_id)
    {  
        $query_check = $this->db->query("SELECT ed.lead_id,ed.product_id,ed.acc_no,pd.TxRefNo,apr.certificate_number,apr.proposal_no,GROUP_CONCAT(apr.pr_api_id) AS pr_api_id,SUM(p.premium) AS premium,apr.start_date,apr.end_date FROM employee_details AS ed,proposal AS p,api_proposal_response AS apr,emandate_data AS emd,payment_details AS pd WHERE ed.emp_id = p.emp_id AND p.proposal_no = apr.proposal_no_lead AND p.status in('Success','Payment Received') AND apr.mandate_send_status = 0 AND emd.lead_id = ed.lead_id AND emd.status = 'Success' AND pd.proposal_id = p.id AND ed.emp_id = ".$emp_id." GROUP BY apr.certificate_number")->result_array();
        // print_pre($query_check);exit;
        if($query_check)
        { 
            foreach ($query_check as $val)
            {
                if($val['product_id'] == 'ABC'){
                    $Source = "ABC";
                } else if ($val['product_id'] == 'MUTHOOT'){
                    $Source = "MTH";
                }
                //BIZ HB call start
                // $url = 'https://bizpre.adityabirlahealth.com/ABHICL_HealthCoreXML/Service1.svc/AddEmendateDetails';
                $url = 'https://bizpre.adityabirlahealth.com/ABHICL_Generic/Service1.svc/AddEmendateDetails';
                $req_arr = [
                        'EmendateDeatails' => 
                        [
                            'EmendateList' => 
                                [ 
                                    [
                                        // 'Bank_Name' => 'Axis Bank',
                                        // 'Debit_Account_Number' => empty($val['acc_no'])?'':$val['acc_no'],
                                        // 'Mandate_Start_Date' => '10-02-2020',
                                        // 'Mandate_End_Date' => '11-09-2020',
                                        // 'Account_Type' => 'Saving',
                                        // 'Bank_Branch_Name' => null,
                                        // 'MICR' => '123',
                                        // 'IFSC' => null,
                                        // 'Frequency' => "Annual",
                                        // 'Policy_Number' => $val['certificate_number'],

                                        'Bank_Name' => 'Axis Bank', //Ask to Pooja
                                        'Debit_Account_Number' => '', //Ask to Pooja                           
                                        'Mandate_Start_Date' => date('m/d/Y',strtotime($val['start_date'])) ,//api_proposal_response
                                        'Mandate_End_Date' => date('m/d/Y',strtotime($val['end_date'])),//api_proposal_response
                                        'Account_Type' => 'Saving',
                                        'Bank_Branch_Name' => '', //Ask to Pooja
                                        'MICR' => '', //Ask to Pooja
                                        'IFSC' => '', //Ask to Pooja
                                        'Frequency' => "1",
                                        'Policy_Number' => $val['certificate_number'], //api_proposal_response
                                        "Proposal_Number"=>$val['proposal_no'], //api_proposal_response
                                        "Source"=>$Source,  //As per condition ABC/Muthoot
                                        "Mandate_Type"=>"MT",
                                        "Payment_ID"=>$val['TxRefNo'], //payment_details `transaction_no`
                                        //"Account_ID"=>"845677",
                                        //"Order_ID"=>"09674ODR",
                                        //"Customer_ID"=>$val['customer_uid'],
                                        //"Token_ID"=>"TKN0001",
                                        "Lead_ID"=>$val['lead_id'],
                                        "Auto_Debit_Registration_Status"=>"Yes",
                                        "Registration_Rejection_Reason"=>"N",
                                        "Mandate_Category"=>"N",
                                        //"Mandate_Registration_Number"=>"REG0001",
                                        //"Debit_Transaction_Reference_Number"=>"DTR001",
                                        "Debit_Date"=>date('m/d/Y'),
                                        "Debit_Amount"=>$val['premium'], // proposal & api_proposal_response
                                        "Debit_Status"=>"Active",
                                        "Debit_failure_Reason"=>"N",
                                        "Debit_Attempt"=>"1"
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
                // $this->db->where("pr_api_id",$val['pr_api_id']);
                $this->db->where_in("pr_api_id",explode(',',$val['pr_api_id']));
                $this->db->update("api_proposal_response",$update_arr);
                $request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($req_arr),"res" => json_encode($response),"product_id"=> $val['product_id'], "type"=>"emandate_HB_post"];
                $dataArray['tablename'] = 'logs_docs'; 
                $dataArray['data'] = $request_arr; 
                $this->Logs_m->insertLogs($dataArray);
                //BIZ HB call end       
            }
        
        }
    }

}

