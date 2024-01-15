<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payment_new_retail_m extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		
		//$this->load->model("API/Payment_integration_freedom_plus", "external_obj_api", true);
		$this->load->model("Logs_m");
		$this->load->model("Logs_m");
		$this->algoMethod = 'aes-128-ecb';
        $this->hashMethod = 'SHA512';
        $this->hash_key = 'razorpay';
        $this->encrypt_key = 'axisbank12345678';
	}

	function send_message($lead_id,$type)
	{
			// $query_check = $this->db->query("SELECT ed.emp_id,ed.lead_id,ed.email,ed.mob_no,ed.customer_name,ed.product_id,mpst.click_pss_url,mpst.product_name,ee.EMandateFailureReason,ee.Registrationmode,ee.MandateLink,sum(p.premium) as total_amt FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,emandate_data as ee WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.product_name AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.lead_id = ee.lead_id AND ed.lead_id='".$lead_id."'")->row_array();


			$query_check = $this->db->query("SELECT ed.emp_id,ed.lead_id,ed.email,ed.mob_no,ed.customer_name,ed.product_id,
			mpst.click_pss_url,mpst.product_name,ee.EMandateFailureReason,ee.Registrationmode,ee.MandateLink,
			sum(p.premium) as total_amt FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,
			employee_details AS ed,proposal as p,emandate_data as ee WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id 
			AND mpst.policy_subtype_id = epd.policy_sub_type_id 
			AND p.policy_detail_id=epd.policy_detail_id 
			AND ed.lead_id = ee.lead_id AND ed.lead_id='".$lead_id."'")->row_array();
			
			// echo $this->db->last_query();exit;
			// echo $lead_id;			
			// print_r($query_check);exit;						
			if($query_check){
				// echo "inquery";exit;
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
					
				// print_r($parameters);exit;

				curl_close($curl);
				
				$request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($parameters),"res" => json_encode($response) ,"product_id"=> $query_check['product_id'], "type"=>"sms_logs_emandate_".$type];
				// print_r($request_arr);exit;
				$dataArray['tablename'] = 'logs_docs'; 
				$dataArray['data'] = $request_arr; 
				$this->Logs_m->insertLogs($dataArray);
		
		  }
	}
	
	function get_all_quote_call($emp_id)
	{ 
		$get_data = $this->db->query('SELECT ed.emp_id,p.id,mpst.policy_subtype_id,p.policy_detail_id,mpst.HB_policy_type FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.emp_id="' .$emp_id. '" ')->result_array();
			
		foreach ($get_data as $vall)
		{
			
			if($vall['HB_policy_type'] == 'ProposalWise'){
				
				$query_check_ghi = $this->db->query("select id from ghi_quick_quote_response where policy_subtype_id = '".$vall['policy_subtype_id']."' and emp_id='$emp_id' and status = 'success'")->row_array();
				
				if($query_check_ghi){
					$last_array[] = 'Success';
				}else{
					$GHI_quote = $this->get_quote_data($emp_id, $vall['policy_detail_id']);
					$last_array[] = $GHI_quote['status'];
				}
				
			}else{
				
				$member_data = (array)$this->get_all_member_data($emp_id,$vall['policy_detail_id']);

				$query_start = $this->db->query("select count(id) as total_success from ghi_quick_quote_response where policy_subtype_id = '".$vall['policy_subtype_id']."' and emp_id='$emp_id' and status = 'success'")->row_array();
			
				if($member_data[0]['familyConstruct'] == '1A'){
					$check_status = ($query_start['total_success'] == '1')?'Success':'error';
				}
				
				if($member_data[0]['familyConstruct'] == '2A'){
					$check_status = ($query_start['total_success'] == '2')?'Success':'error';
				}

				if($check_status == 'error')
				{
					foreach ($member_data as $key => $value)
					{
						$value['key_id'] = $key+1;
						$mem_data[0] = $value;
							
						$is_data = $this->db->query("select * from ghi_quick_quote_response where policy_subtype_id != 1 and emp_id='$emp_id' and policy_subtype_id = '".$value['policy_subtype_id']."' and fr_id = '".$value['fr_id']."' ")->row_array();
						if(!empty($is_data)){
							
						$query_check = $this->db->query("select * from ghi_quick_quote_response where policy_subtype_id != 1 and emp_id='$emp_id' and policy_subtype_id = '".$value['policy_subtype_id']."' and fr_id = '".$value['fr_id']."' and status = 'error'")->row_array();
						
							if($query_check){
							  $policy_data = $this->get_quote_data($emp_id, $value['policy_detail_id'], $mem_data);
							}
							
						}else{
							
							$arr = ["emp_id" => $emp_id,"policy_subtype_id" => $value['policy_subtype_id'],"fr_id"=>$value['fr_id'],"status"=>"error","proposal_id"=>$vall['id']];

							$this->db->insert("ghi_quick_quote_response", $arr);
							
							$policy_data = $this->get_quote_data($emp_id, $value['policy_detail_id'], $mem_data);
						}
					}
				}
				
				$query_last = $this->db->query("select count(id) as total_success from ghi_quick_quote_response where policy_subtype_id = '".$vall['policy_subtype_id']."' and emp_id='$emp_id' and status = 'success'")->row_array();
				
				if($member_data[0]['familyConstruct'] == '1A'){
					$check_status = ($query_last['total_success'] == '1')?'Success':'error';
					$last_array[] = $check_status;
				}
				
				if($member_data[0]['familyConstruct'] == '2A'){
					$check_status = ($query_last['total_success'] == '2')?'Success':'error';
					$last_array[] = $check_status;
				}
				
			}
		
		}
		
		if(in_array("error", $last_array)){
			$proposal_status = 'error';
		}else{
			$proposal_status = 'Success';
		}
		
		return $return_data = array(
						'status'=>$proposal_status,
						"msg" => "testing"
					);
	}
	
	function check_error_data_m()
	{
		$emp_id = $this->session->userdata('emp_id');
		$emp_id_encrypt = encrypt_decrypt_password($emp_id);
		
		$query_check = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and status = 'success'")->row_array();
		
		if($query_check > 0){

		$query = $this->db->query("select pd.TxStatus,p.status as proposal_status,p.count as p_count from employee_details as ed,proposal as p,payment_details as pd where ed.emp_id=p.emp_id and p.id=pd.proposal_id and ed.emp_id = '$emp_id' group by p.id")->row_array();
		
		if($query['TxStatus'] == 'success' && $query['proposal_status'] != 'Success' && $query['p_count'] < 3){
			// quote genarate,payment done but policy pending
			$data = array(
			"status" => "1",
			"check" => "2",
			"url" => base_url('tls_payment_return_view/'.$emp_id_encrypt),  
			);
			
		}else if($query['TxStatus'] == 'success' && $query['proposal_status'] != 'Success' && $query['p_count'] >= 3){
			// policy pending 3 count hit exceeded
			$data = array(
			"status" => "2",
			"check" => "3",
			"url" => "#"
			);
		}else{
			// quote genarate but payment pending
			$data = array(
			"status" => "1",	
			"check" => "1",
			"url" => base_url('tls_payment_redirect_view/'.$emp_id_encrypt),
			);
			
		}
			
		}else{
			
		$query = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and count < 3 and status = 'error'")->row_array();
		
		if($query > 0){
			// quote pending,payment pending
			$data = array(
			"status" => "1",
			"check" => "1",		
			"url" => base_url('tls_payment_redirect_view/'.$emp_id_encrypt),
			);
		}else{
			// quote pending 3 count hit exceeded
			$data = array(
			"status" => "2",
			"check" => "3",
			"url" => "#"
			);
		}
		
		}
		
		return $data;
	}
	
	function Memberwise_policy_call($emp_id, $policy_detail_id,$cron_policy_check = '')
	{ 
	    $member_data = (array)$this->get_all_member_data($emp_id, $policy_detail_id);
		// print_r($member_data);
		$query = $this->db->query("select multi_status from proposal where emp_id='$emp_id' and policy_detail_id = '$policy_detail_id'")->row_array();
		
		$update_arr = ["emp_id"=>$emp_id,"policy_detail_id"=>$policy_detail_id];
		
		if(empty($query['multi_status']))
		{
			
			if($member_data[0]['familyConstruct'] == '1A'){
				$arr = ["multi_status" => '0'];
			}else{
				$arr = ["multi_status" => '0,0'];
			}
			
			$this->db->where($update_arr);
			$this->db->update("proposal",$arr);
		}
		
		$query_again = $this->db->query("select multi_status from proposal where emp_id='$emp_id' and policy_detail_id = '$policy_detail_id'")->row_array();
			
		$status_arr = explode(",",$query_again['multi_status'],2);
		
		foreach ($member_data as $key => $value)
		{
			$value['key_id'] = $key+1;
			$mem_data[0] = $value;
			//print_r($value);
			if($status_arr[$key] == 0)
			{
				$policy_data = $this->GHI_GCI_api_call($emp_id, $policy_detail_id,$mem_data,$cron_policy_check);
				
				if($policy_data['status'] == 'Success')
				{
					$status_arr[$key] = 1;
					
					$status_string = implode(",",$status_arr);

					$arr = ["multi_status" => $status_string];
					
					$this->db->where($update_arr);
					$this->db->update("proposal",$arr);
				
				}
				
			}
			
		}
		
		$query_last = $this->db->query("select multi_status from proposal where emp_id='$emp_id' and policy_detail_id = '$policy_detail_id'")->row_array();
		
		if($member_data[0]['familyConstruct'] == '1A'){
			$proposal_status = ($query_last['multi_status'] == '1')?'Success':'error';
		}
		
		if($member_data[0]['familyConstruct'] == '2A'){
			$proposal_status = ($query_last['multi_status'] == '1,1')?'Success':'error';
		}
		
		
		return $return_data = array(
						'status'=>$proposal_status,
						"msg" => "GPA GCI GP call"
					);
		
		
	}
	
	
	function agent_policy_create_m()
	{
		$lead_id = $this->input->post('lead_id');	
		$TxRefNo = $this->input->post('TxRefNo');	
		$txndate = $this->input->post('txndate');	
		
		$lead_id = encrypt_decrypt_password($lead_id,'D');

		$query = $this->db->query("SELECT ed.product_id,ed.lead_id,ed.emp_id,ed.email,p.premium,p.id as proposal_id,p.proposal_no FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND p.status = 'Payment Pending' AND ed.lead_id=".$lead_id)->row_array();
		
		if($query){
		
			$request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($this->input->post()),"product_id"=> $query['product_id'], "type"=>"payment_response_agent"];
			
			$dataArray['tablename'] = 'logs_docs'; 
			$dataArray['data'] = $request_arr; 
			$this->Logs_m->insertLogs($dataArray);
			
			if($TxRefNo){
				
				$request_arr = ["payment_status" => "No Error","payment_type" => 'netbanking',"pgRespCode" => "","merchantTxnId" => '',"SourceTxnId" => '',"txndate" => date('Y-m-d', strtotime($txndate)),"TxRefNo" => $TxRefNo,"TxStatus"=>"success","bank_name"=>'',"json_quote_payment"=>json_encode($this->input->post())];
				
				$this->db->where("proposal_id",$query['proposal_id']);
				$this->db->update("payment_details",$request_arr);
			}
			
			$proposal_id =  $query['proposal_id'];
			$payment_data = $this->db->query("select TxStatus from payment_details where proposal_id=$proposal_id ")->row_array();
			
			if($payment_data['TxStatus'] == 'success'){
				
				$data_res = $this->policy_creation_call($query['lead_id']);
				
				if($data_res['Status'] == 'Success'){
					
					$return_data = array(
							"ErrorCode" => "1",
							"ErrorDescription" => "Payment received and Policy created"
						);
					
				}else{
					
					$return_data = array(
							"ErrorCode" => "2",
							"ErrorDescription" => $data_res['ErrorDescription']
						);
				}
			
			}else{
				
				$return_data = array(
							"ErrorCode" => "2",
							"ErrorDescription" => "Payment failed"
						);
			}
			
		return $return_data;
			
		}
	}
	
  	function policy_creation_call($CRM_Lead_Id,$cron_policy_check = '')
	{
		$update_data = $this
			->db
			->query('SELECT p.id,p.emp_id,p.policy_detail_id,p.status,p.count,e.policy_subtype_id,e.product_code,e.HB_policy_type
				FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,
				employee_details AS ed
				where epd.product_name = e.id
				AND p.emp_id = ed.emp_id
				AND ed.lead_id = "' .$CRM_Lead_Id. '"
				AND ed.product_id IN ("D01","D02")
				AND epd.policy_detail_id = p.policy_detail_id');
		$update_data = $update_data->result_array();

		// update lead status - Payment Received
		// GET employee lead data
        $leadData = $this->db->select('*')->from('employee_details')->where('emp_id', $update_data[0]['emp_id'])->get()->row_array();
//Revese feed change status - QE
		$quote_exp_check = common_quote_expired($update_data[0]['emp_id']);
		
                if($quote_exp_check['status'] == 1){
					$data = array(
						'Status' => 'error',
						'ErrorCode' => '5',
						'ErrorDescription' => $quote_exp_check['msg'],
					);
        	return $data;
                  
                }
        if(!empty($leadData) && $leadData['lead_status'] == 'Payment Pending'){
        	$this->db->where('emp_id', $update_data[0]['emp_id']);
        	$updateLeadStatus = $this->db->update('employee_details',['lead_status'=>'Payment Received']);
        } else if ($leadData['lead_status'] == 'Cancelled'){
        	$data = array(
						'Status' => 'error',
						'ErrorCode' => '5',
						'ErrorDescription' => 'Lead is cancelled !!',
					);
        	return $data;
        }

		foreach ($update_data as $update_payment)
		{
		
			// payment confirmation hit count update

			$arr_new = ["count" => $update_payment['count'] + 1];
			$this->db->where('id', $update_payment['id']);
			$this->db->update("proposal", $arr_new);  // Commented by Kshitiz Mittal
			 
			if($update_payment['status']!='Success'){
		  		// check first hit or not
				if($update_payment['count'] < 3){
					// update proposal status - Payment Received
					$arr_new = ["status" => "Payment Received"];
					$this->db->where('id', $update_payment['id']);
					$this->db->update("proposal", $arr_new);

					// For GHI,GPA,GCI policy check
					$query = $this->db->query("select policy_detail_id from employee_policy_detail where policy_detail_id = '" . $update_payment['policy_detail_id'] . "' and policy_sub_type_id in(1,2,3)")->row_array();

					if($query)
					{
						if($update_payment['HB_policy_type'] == 'ProposalWise')
						{
							$api_response_tbl = $this->GHI_GCI_api_call($update_payment['emp_id'], $update_payment['policy_detail_id'],'',$cron_policy_check);
						}else{
							$api_response_tbl['status']='error';
							$query2 = $this->db->query("SELECT p.status FROM product_master_with_subtype AS e,employee_details AS ed,proposal AS p,employee_policy_detail AS epd where ed.emp_id=p.emp_id AND epd.product_name = e.id AND epd.policy_detail_id = p.policy_detail_id AND ed.lead_id = '".$CRM_Lead_Id."' AND e.policy_subtype_id = 1")->row_array();
							if ($query2['status']=='Success')
							{
								$api_response_tbl = $this->Memberwise_policy_call($update_payment['emp_id'], $update_payment['policy_detail_id'],$cron_policy_check);
							}
						}

						// print_pre($api_response_tbl['status']);

						if($api_response_tbl['status']=='error'){
						    $return_data['check'][] = 'error';
							$return_data['code'] = '0';
							$return_data['msg'] = $api_response_tbl['msg'];
						}else{
							// update proposal status - Success  
							$arr = ["status" => "Success"]; // Commented by Kshtix mittal change to success
							$this->db->where('id', $update_payment['id']);
							$this->db->update("proposal", $arr);

							$return_data['check'][] = 'Success';
							$return_data['code'] = '1';
							$return_data['msg'] = $api_response_tbl['msg'];
						}
					}
				
				}else{
						$return_data['check'][] = 'error';
						$return_data['code'] = '0';
						$return_data['msg'] = '3 times fail count exceeded';
			   }
		  	}else{
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
	
	
	public function get_quote_data($emp_id,$policy_detail_id,$mem_data = '')
	{ 
		$data['customer_data'] = (array)$this->get_profile($emp_id);
		$data['customer_quote_data'] = (array)$this->get_customer_quote_data($emp_id);
		$data['member_data'] = (array)$this->get_all_member_data($emp_id, $policy_detail_id);
		$data['nominee_data'] = (array)$this->get_all_nominee($emp_id);
		$data['proposal_data'] = (array)$this->getProposalData($emp_id, $policy_detail_id);
	
		$url = trim($data['proposal_data']['api_url']);
		$source_name = $data['proposal_data']['HB_source_code'];

		//replaced cust_id with unique_ref_no on 17-05-2021 - by upendra
		// $cust_id = $data['customer_data']['cust_id'];
		$unique_ref_no = $data['customer_data']['unique_ref_no'];
		
		if($url == '')
		{
			return array(
				"status" => "error",
				"msg" => "Something Went Wrong"
			);
		}
		
		if($data['proposal_data']['HB_policy_type'] == 'MemberWise')
		{
			$data['member_data'] = $mem_data;
			$concat_string = $data['proposal_data']['HB_custid_concat_string'];

			//replaced cust_id with unique_ref_no on 17-05-2021 - by upendra
			// $cust_id = $data['customer_data']['cust_id'].$concat_string.$data['member_data'][0]['key_id'];
			$unique_ref_no = $data['customer_data']['unique_ref_no'].$concat_string.$data['member_data'][0]['key_id'];
			
		}
		
		
		$totalMembers = count($data['member_data']);
		$member = [];
		
		$occupation = "O553";
		if ($data['proposal_data']['product_code'] == 'D01' || $data['proposal_data']['product_code'] == 'D03' ){
			$occupation_check = $this->db->query('SELECT occupation_id from master_occupation where occupation_id = "' . $data['customer_quote_data']['occupation_id'] . '" ')->row_array();	
			if (isset($occupation_check['occupation_id']))
			{
				$occupation = $occupation_check['occupation_id'];
			}
		}

		//added by upendra on 07-04-2021 (deductable logic for T03)
		if ($data['proposal_data']['product_code'] == 'D02' && $data['proposal_data']['policy_sub_type_id'] == 1)
		{
			if($data['customer_quote_data']['deductible_amount'] != '0'){

				$data['proposal_data']['sum_insured'] = ($data['proposal_data']['sum_insured'] - $data['customer_quote_data']['deductible_amount']);
				
				$GHI_supertopup_data = $this
					->db
					->query("select * from master_group_code where si_group = '" . $data['proposal_data']['sum_insured'] . "' and family_construct = '" . $data['proposal_data']['familyConstruct'] . "' and product_code = '".$data['proposal_data']['product_code']."'")->row_array();
				
				$data['proposal_data']['group_code'] = $GHI_supertopup_data['group_code'];
				$data['proposal_data']['spouse_group_code'] = $GHI_supertopup_data['spouse_group_code'];
				
			}
		}


		$explode_name_nominee = explode(" ", trim($data['nominee_data']['nominee_fname']) , 2);
	
		for ($i = 0;$i < $totalMembers;$i++)
		{

			if ($data['member_data'][$i]['fr_id'] == 2 || $data['member_data'][$i]['fr_id'] == 3 || $data['member_data'][$i]['fr_id'] == 25 || $data['member_data'][$i]['fr_id'] == 26){
                if(strtolower($data['member_data'][$i]['gender']) == "male"){
                    $data['member_data'][$i]['relation_code'] = "R003";
                }else if(strtolower($data['member_data'][$i]['gender']) == "female"){
                    $data['member_data'][$i]['relation_code'] = "R004";
                }else {
                    $data['member_data'][$i]['relation_code'] = ""; 
                }
            }

			//check 2 adults
            if(strpos($data['proposal_data']['familyConstruct'], '2A') !== false){

                if($data['member_data'][$i]['fr_id'] != 0){
                    $occupation_mem = "O553";
                }else{
                    $occupation_mem = $occupation;
                }

            }
            //check 1 adult 
            else{

                if($data['member_data'][$i]['fr_id'] != 0 && $data['member_data'][$i]['fr_id'] != 1){
                    $occupation_mem = "O553";
                }else{
                    $occupation_mem = $occupation;
                }
                
            }
			//dedupe logic TO BE COMMENTED AS OF NOW
			/*$check_dedupe = common_function_ref_id_exist($data['customer_data']['lead_id']);
            if($check_dedupe['status'] == 'error'){
                echo $check_dedupe['msg']; exit;
            }*/
			if(($data['proposal_data']['familyConstruct'] == '1A+1K' || $data['proposal_data']['familyConstruct'] == '1A+2K') && $data['member_data'][$i]['fr_id'] == 1){
				$data['proposal_data']['group_code'] = $data['proposal_data']['spouse_group_code'];
            }


			// $query = $this
			// ->db
			// ->query('SELECT pds.sub_member_code from employee_declare_member_sub_type as edmsp JOIN policy_declaration_subtype as pds ON edmsp.declare_sub_type_id = pds.declare_subtype_id where edmsp.emp_id="'.$emp_id.'" AND edmsp.policy_member_id = "'.$data['member_data'][$i]['policy_member_id'].'" ')->result_array();
			
			// $abc = [];
			// if(!empty($query))
			// {
			// 	foreach ($query as $key => $value) {
			// 		$abc[] = ["PEDCode" => $value['sub_member_code'], "Remarks" => null];
			// 	}
			// }else{
			// 	$abc[] = ["PEDCode" => null, "Remarks" => null];
			// }
			//added by upendra on 07-04-2021 (memberwise remark logic)
			// $memberPEDARRAY = [];
			// if ($data['proposal_data']['product_code'] == 'D02' || $data['proposal_data']['product_code'] == 'D01'){
			// 	$new_remarks = $data['customer_data']['new_remark'];
			// 	$new_remarks = stripslashes(html_entity_decode($new_remarks));
			// 	$new_remarks = json_decode($new_remarks, TRUE);
			// 	$current_relation_code = trim($data['member_data'][$i]['family_relation_id']);
			// 	foreach ($new_remarks as $k=>$v){ 
			// 		if($v['relation_code'] == $current_relation_code){
			// 			$v_remark = trim($v['remark']);
			// 			if($v_remark != ""){
			// 			//	$memberPEDARRAY  = ["PEDCode" => "PE0186", "Remarks" => $v['remark']];
			// 				$memberPEDARRAY  = ["PEDCode" => "PE675", "Remarks" => $v['remark']];
			// 				$abc = [];
			// 				$abc = $memberPEDARRAY;
			// 			}else{

			// 				$memberPEDARRAY  = ["PEDCode" => null, "Remarks" => null];
			// 				$abc = [];
			// 				$abc = $memberPEDARRAY;
			// 			}		
			// 		}
			// 	}
			// }

			$abc = ["PEDCode" => null, "Remarks" => null];

			$AnnualIncome=null;
			if($data['customer_quote_data']['annual_income']>0)
			{
				$AnnualIncome=$data['customer_quote_data']['annual_income'];
			}

			$explode_name_member = explode(" ", trim($data['member_data'][$i]['firstname']) , 2);

			$member[] = ["MemberNo" => $i + 1, "Salutation" => (($data['member_data'][$i]['gender'] == "Male") ? 'Mr' : (($data['member_data'][$i]['age'] >= 18) ? 'Mrs' : 'Ms')) , "First_Name" => $explode_name_member[0], "Middle_Name" => null, "Last_Name" => !empty($explode_name_member[1]) ? $explode_name_member[1] : '.', "Gender" => ($data['member_data'][$i]['gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['dob'])) , "Relation_Code" => trim($data['member_data'][$i]['relation_code']), "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => $occupation_mem, "PrimaryMember" => (($i == 0) ? "Y" : "N") , "MemberproductComponents" => [["PlanCode" => $data['proposal_data']['plan_code'], "MemberQuestionDetails" => [["QuestionCode" => null, "Answer" => null, "Remarks" => null]]]], "MemberPED" => $abc, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $explode_name_nominee[0], "Nominee_Last_Name" => !empty($explode_name_nominee[1]) ? $explode_name_nominee[1] : '.', "Nominee_Contact_Number" => $data['nominee_data']['nominee_contact'], "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data']['relation_code']), ];
		}

		//replaced cust_id to unique_ref_no on 15-05-2021 by upendra
        //updated by upendra on 10-05-2021 - dedupe logic updated
        $unique_ref_no_rows_check = $this
        ->db
        ->query("select ed.lead_id from employee_details as ed,proposal as p 
        where ed.emp_id = p.emp_id and p.status in('Success','Payment Received','Cancelled') 
        and ed.unique_ref_no = '" . $data['customer_data']['unique_ref_no'] . "' 
        and ed.product_id = '".$data['proposal_data']['product_code']."' 
        and ed.lead_id != '".$data['customer_data']['lead_id']."'
        group by p.emp_id")->num_rows();

        if($unique_ref_no_rows_check > 0){
            $unique_ref_no_rows_check = $unique_ref_no_rows_check + 1;
            $unique_ref_no = $unique_ref_no."_".$unique_ref_no_rows_check;
        }

        $explode_name = explode(" ", trim($data['customer_data']['customer_name']) , 2);
		//replaced cust_id to unique_ref_no on 15-05-2021 by upendra
		$fqrequest = ["ClientCreation" => ["Member_Customer_ID" => $unique_ref_no, "salutation" => $data['customer_data']['salutation'], "firstName" => $explode_name[0], "middleName" => "", "lastName" => !empty(trim($explode_name[1])) ? $explode_name[1] : '.', "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['bdate'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['emp_pincode'], "uidNo" => $data['customer_data']['adhar'], "maritalStatus" => null, "nationality" => "Indian", "occupation" => $occupation, "primaryEmailID" => $data['customer_data']['email'], "contactMobileNo" => substr(trim($data['customer_data']['mob_no']), -10), "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => $AnnualIncome, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address'], "homeAddressLine2" => $data['customer_data']['comm_address'], "homeAddressLine3" => $data['customer_data']['comm_address1'], "homePinCode" => $data['customer_data']['emp_pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => "", "MasterPolicyNumber" => $data['proposal_data']['master_policy_no'], "GroupID" => $data['proposal_data']['group_code'], "Product_Code" => $data['proposal_data']['plan_code'],"SumInsured_Type"=> null,"Policy_Tanure"=> "1","Member_Type_Code"=> "M209", "intermediaryCode" => $data['proposal_data']['IMDCode'], "AutoRenewal" => '0', "AutoDebit" => '0', "intermediaryBranchCode" => "10MHMUM01", "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $data['customer_data']['lead_id'], "Source_Name" => $source_name, "SPID" => $data['customer_data']['ref2'], "TCN" => null, "CRTNO" => null, "RefCode1" => $data['customer_data']['ref1'], "RefCode2" => $data['proposal_data']['branch_code'], "Employee_Number" => $data['proposal_data']['emp_id'],"enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "0", "PaymentMode" => null, "PolicyproductComponents" => [["PlanCode" => $data['proposal_data']['plan_code'], "SumInsured" => $data['proposal_data']['sum_insured'], "SchemeCode" => "4112000003"]]], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "", "modeOfEntry" => "", "cdAcNo" => null, "expiryDate" => null, "payerType" => "", "payerCode" => null, "paymentBy" => "", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => "", "collectionRcvdDate" => null, "collectionMode" => "", "remarks" => null, "instrumentNumber" => null, "instrumentDate" => null, "bankName" => null, "branchName" => null, "bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => "", "PaymentGatewayName" => "", "TerminalID" => "", "CardNo" => null]];

		//Monolog::saveLog("full_quote_request1", "I", json_encode($fqrequest));

		$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($fqrequest) ,"product_id"=> $data['proposal_data']['product_code'], "type"=>"full_quote_request1_".$data['proposal_data']['policy_sub_type_id']];
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

		//Monolog::saveLog("full_quote_reponse1", "I", json_encode($response));

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
				$premiumDetail = $newArr['premium'];

				// $policydetail['MemberCustomerID'] = $policydetail['MemberCustomerID'];
				
				// if(empty($policydetail['MemberCustomerID'])){
				// 	$policydetail['MemberCustomerID'] = "";
				// }
				

				if($data['proposal_data']['HB_policy_type'] == 'ProposalWise')
				{
					$query_one = $this
					->db
					->query("select * from ghi_quick_quote_response where emp_id='".$emp_id."' and policy_subtype_id = '".$data['proposal_data']['policy_sub_type_id']."'")->row_array();
					
					if($query_one > 0){
						
						$arr = ["QuotationNumber" => $policydetail['QuotationNumber'], "PolicyNumber" => $policydetail['PolicyNumber'], "GrossPremium" => $premiumDetail['GrossPremium'], "MemberCustomerID" => $policydetail['MemberCustomerID'],"status"=>"success"];
						
						$update_where = ["emp_id"=>$emp_id,"policy_subtype_id" => $data['proposal_data']['policy_sub_type_id']];
						
						$this->db->where($update_where);
						$this->db->update("ghi_quick_quote_response",$arr);
					}else{
						
						$arr = ["emp_id" => $emp_id,"status"=>"success","policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'],"QuotationNumber" => $policydetail['QuotationNumber'], "MemberCustomerID" => $policydetail['MemberCustomerID'], "PolicyNumber" => $policydetail['PolicyNumber'], "GrossPremium" => $premiumDetail['GrossPremium'],"proposal_id" => $data['proposal_data']['id']];
						
						$this->db->insert("ghi_quick_quote_response", $arr);
					}
					
				}else{
					
					$query_one = $this
					->db
					->query("select * from ghi_quick_quote_response where emp_id='".$emp_id."' and policy_subtype_id = '".$data['proposal_data']['policy_sub_type_id']."' and fr_id = '".$data['member_data'][0]['fr_id']."'")->row_array();
					
					if($query_one > 0){
						
						$arr = ["QuotationNumber" => $policydetail['QuotationNumber'], "PolicyNumber" => $policydetail['PolicyNumber'], "GrossPremium" => $premiumDetail['GrossPremium'], "MemberCustomerID" => $policydetail['MemberCustomerID'],"status"=>"success"];
						
						$update_where = ["emp_id"=>$emp_id,"policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'],"fr_id" => $data['member_data'][0]['fr_id']];
						
						$this->db->where($update_where);
						$this->db->update("ghi_quick_quote_response",$arr);
					}else{
						
						$arr = ["emp_id" => $emp_id,"status"=>"success","policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'],"fr_id" => $data['member_data'][0]['fr_id'],"QuotationNumber" => $policydetail['QuotationNumber'], "GrossPremium" => $premiumDetail['GrossPremium'],"proposal_id" => $data['proposal_data']['proposal_id']];
						
						$this->db->insert("ghi_quick_quote_response", $arr);
					}
					
				}
				
				return array(
					"status" => "Success",
					"msg" => $policydetail['QuotationNumber']
				);
			}else{
				
				if($data['proposal_data']['HB_policy_type'] == 'ProposalWise')
				{
					$query_one = $this
					->db
					->query("select * from ghi_quick_quote_response where emp_id='".$emp_id."' and policy_subtype_id = '".$data['proposal_data']['policy_sub_type_id']."'")->row_array();
					
					if($query_one > 0){
						
						$arr = ["count" => $query_one['count']+1,"status"=>"error"];
						$update_where = ["emp_id"=>$emp_id,"policy_subtype_id" => $data['proposal_data']['policy_sub_type_id']];
						
						$this->db->where($update_where);
						$this->db->update("ghi_quick_quote_response",$arr);
					}else{
						
						$arr = ["emp_id" => $emp_id,"status"=>"error","policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'],"proposal_id" => $data['proposal_data']['proposal_id']];

						$this->db->insert("ghi_quick_quote_response", $arr);
					}
					
				}else{
					
					$query_one = $this
					->db
					->query("select * from ghi_quick_quote_response where emp_id='".$emp_id."' and policy_subtype_id = '".$data['proposal_data']['policy_sub_type_id']."' and fr_id = '".$data['member_data'][0]['fr_id']."'")->row_array();
					
					if($query_one > 0){
						
						$arr = ["count" => $query_one['count']+1,"status"=>"error"];
						$update_where = ["emp_id"=>$emp_id,"policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'],"fr_id" => $data['member_data'][0]['fr_id']];
						
						$this->db->where($update_where);
						$this->db->update("ghi_quick_quote_response",$arr);
					}else{
						
						$arr = ["emp_id" => $emp_id,"status"=>"error","policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'],"fr_id" => $data['member_data'][0]['fr_id'],"proposal_id" => $data['proposal_data']['proposal_id']];

						$this->db->insert("ghi_quick_quote_response", $arr);
					}
					
				}
				
				///------- @author : Guru --------------------------//
				$request_failure_arr = ["lead_id" => $data['customer_data']['lead_id'], "product_id" => $data['proposal_data']['product_code'], "res" => $errorObj['ErrorMessage'], "type" => "failure_reason_".$data['proposal_data']['policy_sub_type_id']];
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


public function GHI_GCI_api_call($emp_id, $policy_no,$mem_data = '',$cron_policy_check = '')
{ 			
	$data['customer_data'] = (array)$this->get_profile($emp_id);
	$data['customer_quote_data'] = (array)$this->get_customer_quote_data($emp_id);
	$data['member_data'] = (array)$this->get_all_member_data($emp_id, $policy_no);
	$data['nominee_data'] = (array)$this->get_all_nominee($emp_id);
	$data['proposal_data'] = (array)$this->getProposalData($emp_id, $policy_no);

	$url = trim($data['proposal_data']['api_url']);
	$source_name = $data['proposal_data']['HB_source_code'];
	
	if($url == '')
	{
		return array(
			"status" => "error",
			"msg" => "Something Went Wrong"
		);
	}
	
	$explode_name = explode(" ", trim($data['customer_data']['customer_name']) , 2);
	$transaction_date = explode(" ",$data['proposal_data']['txndate']);
	$trans_date = date("Y-m-d", strtotime($transaction_date[0]));
	$mem_fr_id = '';
	
	$collection_amt['pay_amt'] = $data['proposal_data']['premium'];
	
	$query_quote = [];
	$data['proposal_data']['bank_name'] = empty($data['proposal_data']['bank_name']) ? "Axis Bank Limited" : ucwords($data['proposal_data']['bank_name']);

	//replaced cust_id with unique_ref_no on 17-05-2021 - by upendra
	// $cust_id = $data['customer_data']['cust_id'];
	$unique_ref_no = $data['customer_data']['unique_ref_no'];
	$source_name = $data['proposal_data']['HB_source_code'];
	
	if($data['proposal_data']['HB_policy_type'] == 'ProposalWise')
	{
		
		$query_quote = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and policy_subtype_id = '".$data['proposal_data']['policy_sub_type_id']."' and status = 'success'")->row_array();
		
		if(empty($query_quote)){
			$quote_data = $this->get_quote_data($emp_id, $policy_no);
			if($quote_data['status'] == 'Success')
			{
				$query_quote['QuotationNumber'] = $quote_data['msg'];
			}
		}
		$mem_fr_id = 0;
	}else{
		
		$query_quote = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and policy_subtype_id = '".$mem_data[0]['policy_subtype_id']."' and fr_id = '".$mem_data[0]['fr_id']."' and status = 'success'")->row_array();
		
		if(empty($query_quote)){
			$quote_data = $this->get_quote_data($emp_id, $policy_no, $mem_data);
			if($quote_data['status'] == 'Success')
			{
				$query_quote['QuotationNumber'] = $quote_data['msg'];
			}
		}
		
		$data['member_data'] = $mem_data;
		$collection_amt['pay_amt'] = $data['member_data'][0]['policy_mem_sum_premium'];
		$mem_fr_id = $data['member_data'][0]['fr_id'];
		$concat_string = $data['proposal_data']['HB_custid_concat_string'];
		
		$unique_ref_no = $data['customer_data']['unique_ref_no'].$concat_string.$data['member_data'][0]['key_id'];
		
	}
	if($query_quote['QuotationNumber']){
		
		$combi_check = $this->db->query('SELECT e.master_policy_no,e.plan_code,pm.familyConstruct FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,proposal_member AS pm,family_relation AS fr, employee_details AS ed,payment_details as pd where epd.product_name = e.id AND ed.lead_id = "' .$data['customer_data']['lead_id']. '"  AND epd.policy_detail_id = p.policy_detail_id AND p.id = pm.proposal_id AND e.policy_subtype_id = epd.policy_sub_type_id AND p.id = pd.proposal_id AND pm.family_relation_id = fr.family_relation_id AND fr.emp_id = ed.emp_id group by p.id')->result_array();
	
		$combi_count = 0;
		$combi_product_count = 0;
		$no_of_product = count($combi_check);
		$combi_flag = ($no_of_product > 1)?'1':'0';

		foreach ($combi_check as $key => $value) 
		{
			$check_adult = explode('A',$value['familyConstruct']);

			if($check_adult[0] == 1 && $data['proposal_data']['HB_policy_type'] == 'MemberWise'){
				$combi_count += 1; 

				if($data['proposal_data']['master_policy_no'] == $value['master_policy_no']){
					$combi_product_count += 1; 
				}
			}

			if($check_adult[0] == 2 && $data['proposal_data']['HB_policy_type'] == 'MemberWise'){
			 	$combi_count += 2; 
			 
			 	if($data['proposal_data']['master_policy_no'] == $value['master_policy_no']){
				 	$combi_product_count += 2; 
			  	}
			  
			}
			if($data['proposal_data']['HB_policy_type'] == 'ProposalWise'){
			 	$combi_count += 1; 
			 
			 	if($data['proposal_data']['master_policy_no'] == $value['master_policy_no']){
				 	$combi_product_count += 1; 
			  	}
			}
		}
		
		$totalMembers = count($data['member_data']);
		$member = [];
		
		$occupation = "O553";
		if ($data['proposal_data']['product_code'] == 'D01'  || $data['proposal_data']['product_code'] == 'D02' ){
			$occupation_check = $this->db->query('SELECT occupation_id from master_occupation where occupation_id = "' . $data['customer_quote_data']['occupation_id'] . '" ')->row_array();
			if (isset($occupation_check['occupation_id']))
			{
				$occupation = $occupation_check['occupation_id'];
			}
		}

		//added by upendra on 07-04-2021 (deductable logic for T03)
		if ($data['proposal_data']['product_code'] == 'D02' && $data['proposal_data']['policy_sub_type_id'] == 1)
		{
			if(!empty($data['customer_quote_data']['deductible_amount'])){

				$data['proposal_data']['sum_insured'] = ($data['proposal_data']['sum_insured'] - $data['customer_quote_data']['deductible_amount']);
				
				$GHI_supertopup_data = $this
					->db
					->query("select * from master_group_code where si_group = '" . $data['proposal_data']['sum_insured'] . "' and family_construct = '" . $data['proposal_data']['familyConstruct'] . "' and product_code = '".$data['proposal_data']['product_code']."'")->row_array();
				
				$data['proposal_data']['group_code'] = $GHI_supertopup_data['group_code'];
				$data['proposal_data']['spouse_group_code'] = $GHI_supertopup_data['spouse_group_code'];
				
			}
		}

		$explode_name_nominee = explode(" ", trim($data['nominee_data']['nominee_fname']) , 2);
		$memFrIdArr = [];
		for ($i = 0;$i < $totalMembers;$i++)
		{
			$memFrIdArr[$i] = $data['member_data'][$i]['fr_id'];
			//check 2 adults

			if ($data['member_data'][$i]['fr_id'] == 2 || $data['member_data'][$i]['fr_id'] == 3 || $data['member_data'][$i]['fr_id'] == 25 || $data['member_data'][$i]['fr_id'] == 26){
                if(strtolower($data['member_data'][$i]['gender']) == "male"){
                    $data['member_data'][$i]['relation_code'] = "R003";
                }else if(strtolower($data['member_data'][$i]['gender']) == "female"){
                    $data['member_data'][$i]['relation_code'] = "R004";
                }else {
                    $data['member_data'][$i]['relation_code'] = ""; 
                }
            }

            if(strpos($data['proposal_data']['familyConstruct'], '2A') !== false){

                if($data['member_data'][$i]['fr_id'] != 0){
                    $occupation_mem = "O553";
                }else{
                    $occupation_mem = $occupation;
                }

            }
            //check 1 adult 
            else{

                if($data['member_data'][$i]['fr_id'] != 0 && $data['member_data'][$i]['fr_id'] != 1){
                    $occupation_mem = "O553";
                }else{
                    $occupation_mem = $occupation;
                }
                
            }			



			if(($data['proposal_data']['familyConstruct'] == '1A+1K' || $data['proposal_data']['familyConstruct'] == '1A+2K') && $data['member_data'][$i]['fr_id'] == 1){
              $data['proposal_data']['group_code'] = $data['proposal_data']['spouse_group_code'];
             }
			 
			// $query = $this
			// ->db
			// ->query('SELECT pds.sub_member_code from employee_declare_member_sub_type as edmsp JOIN policy_declaration_subtype as pds ON edmsp.declare_sub_type_id = pds.declare_subtype_id where edmsp.emp_id="'.$emp_id.'" AND edmsp.policy_member_id = "'.$data['member_data'][$i]['policy_member_id'].'" ')->result_array();
			
			// $abc = [];
			// if(!empty($query))
			// {
			// 	foreach ($query as $key => $value) {
					
			// 		$abc[] = ["PEDCode" => $value['sub_member_code'], "Remarks" => null];
					
			// 	}
			// }else{
				
			// 	$abc[] = ["PEDCode" => null, "Remarks" => null];
			// }
			


			//added by upendra on 07-04-2021 (memberwise remark logic)
			// $memberPEDARRAY = [];
			// if ($data['proposal_data']['product_code'] == 'D01' || $data['proposal_data']['product_code'] == 'D02'){
				
			// 	$new_remarks = $data['customer_data']['new_remark'];
			// 	$new_remarks = stripslashes(html_entity_decode($new_remarks));
			// 	$new_remarks = json_decode($new_remarks, TRUE);
			// 	$current_relation_code = trim($data['member_data'][$i]['family_relation_id']);
			// 	foreach ($new_remarks as $k=>$v){ 

			// 		if($v['relation_code'] == $current_relation_code){
						
			// 			$v_remark = trim($v['remark']);
			// 			if($v_remark != ""){

			// 			//	$memberPEDARRAY  = ["PEDCode" => "PE0186", "Remarks" => $v['remark']];
			// 				$memberPEDARRAY  = ["PEDCode" => "PE675", "Remarks" => $v['remark']];
			// 				$abc = [];
			// 				$abc = $memberPEDARRAY;

			// 			}else{

			// 				$memberPEDARRAY  = ["PEDCode" => null, "Remarks" => null];
			// 				$abc = [];
			// 				$abc = $memberPEDARRAY;

			// 			}
				
			// 		}

			// 	}
			// }

			$abc = ["PEDCode" => null, "Remarks" => null];

			$AnnualIncome=null;
			if($data['customer_quote_data']['annual_income']>0)
			{
				$AnnualIncome=$data['customer_quote_data']['annual_income'];
			}

			$explode_name_member = explode(" ", trim($data['member_data'][$i]['firstname']) , 2);

			$member[] = ["MemberNo" => $i + 1, "Salutation" => (($data['member_data'][$i]['gender'] == "Male") ? 'Mr' : (($data['member_data'][$i]['age'] >= 18) ? 'Mrs' : 'Ms')) , "First_Name" => $explode_name_member[0], "Middle_Name" => null, "Last_Name" => !empty($explode_name_member[1]) ? $explode_name_member[1] : '.', "Gender" => ($data['member_data'][$i]['gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['dob'])) , "Relation_Code" => trim($data['member_data'][$i]['relation_code']), "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => $occupation_mem, "PrimaryMember" => (($i == 0) ? "Y" : "N") , "MemberproductComponents" => [["PlanCode" => $data['proposal_data']['plan_code'], "MemberQuestionDetails" => [["QuestionCode" => null, "Answer" => null, "Remarks" => null]]]], "MemberPED" => $abc, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $explode_name_nominee[0], "Nominee_Last_Name" => !empty($explode_name_nominee[1]) ? $explode_name_nominee[1] : '.', "Nominee_Contact_Number" => $data['nominee_data']['nominee_contact'], "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data']['relation_code']), ];

		}

		//replaced cust_id with unique_ref_no on 17-05-2021 - by upendra
		$unique_ref_no_rows_check = $this
        ->db
        ->query("select ed.lead_id from employee_details as ed,proposal as p 
        where ed.emp_id = p.emp_id and p.status in('Success','Payment Received','Cancelled') 
        and ed.unique_ref_no = '" . $data['customer_data']['unique_ref_no'] . "' 
        and ed.product_id = '".$data['proposal_data']['product_code']."' 
        and ed.lead_id != '".$data['customer_data']['lead_id']."'
        group by p.emp_id")->num_rows();

        if($unique_ref_no_rows_check > 0){
            $unique_ref_no_rows_check = $unique_ref_no_rows_check + 1;
            $unique_ref_no = $unique_ref_no."_".$unique_ref_no_rows_check;
        }

		//replaced cust_id to unique_ref_no on 17-05-2021 by upendra
		$fqrequest = ["ClientCreation" => ["Member_Customer_ID" => $unique_ref_no, "salutation" => $data['customer_data']['salutation'], "firstName" => $explode_name[0], "middleName" => "", "lastName" => !empty($explode_name[1]) ? $explode_name[1] : '.', "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['bdate'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['emp_pincode'], "uidNo" => $data['customer_data']['adhar'], "maritalStatus" => null, "nationality" => "Indian", "occupation" => $occupation, "primaryEmailID" => $data['customer_data']['email'], "contactMobileNo" => substr(trim($data['customer_data']['mob_no']), -10), "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => $AnnualIncome, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address'], "homeAddressLine2" => $data['customer_data']['comm_address'], "homeAddressLine3" => $data['customer_data']['comm_address1'], "homePinCode" => $data['customer_data']['emp_pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null,  "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null],  "PolicyCreationRequest" => ["Quotation_Number" => $query_quote['QuotationNumber'], "MasterPolicyNumber" => $data['proposal_data']['master_policy_no'], "GroupID" => $data['proposal_data']['group_code'], "Product_Code" => $data['proposal_data']['plan_code'],"SumInsured_Type"=> null,"Policy_Tanure"=> "1","Member_Type_Code"=> "M209", "intermediaryCode" => $data['proposal_data']['IMDCode'], "AutoRenewal" => '0', "AutoDebit" => '0', "intermediaryBranchCode" => "10MHMUM01", "agentSignatureDate" => null, "Customer_Signature_Date" => null,  "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $data['customer_data']['lead_id'], "Source_Name" => $source_name, "SPID" => $data['customer_data']['ref2'], "TCN" => null, "CRTNO" => null, "RefCode1" => $data['customer_data']['ref1'], "RefCode2" => $data['proposal_data']['branch_code'], "Employee_Number" => $data['proposal_data']['emp_id'], "enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "1", "PaymentMode" => null, "PolicyproductComponents" => [["PlanCode" => $data['proposal_data']['plan_code'], "SumInsured" => $data['proposal_data']['sum_insured'], "SchemeCode" => "4112000003"]]], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "Mumbai", "modeOfEntry" => "Direct", "cdAcNo" => null, "expiryDate" => null, "payerType" => "Customer", "payerCode" => null, "paymentBy" => "Customer", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => round($collection_amt['pay_amt'],2),  "collectionRcvdDate" => $trans_date, "collectionMode" => "online", "remarks" => null, "instrumentNumber" => $data['proposal_data']['TxRefNo'],  "instrumentDate" => $trans_date, "bankName" => $data['proposal_data']['bank_name'], "branchName" => $data['proposal_data']['branch'], "bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => $data['proposal_data']['ifscCode'], "PaymentGatewayName" => "AXIS_NETB", "TerminalID" => "EuxJCz8cZV9V63", "CardNo" => null],"CombiCreation" => ["Combi_flag" => $combi_flag, "Combi_identifier" => "leadid", "Combi_value" => $data['customer_data']['lead_id'], "Combi_code" => $source_name, "Combi_Count" => $combi_count, "Combi_product_count" => $combi_product_count]];
 
		//Monolog::saveLog("full_quote_request2", "I", json_encode($fqrequest));
		
		// print_pre(json_encode($fqrequest, JSON_PRETTY_PRINT));

		$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($fqrequest) ,"product_id"=> $data['proposal_data']['product_code'], "type"=>"full_quote_request2_".$data['proposal_data']['policy_sub_type_id']];
		$this->db->insert("logs_docs",$request_arr);
		$insert_id = $this->db->insert_id();

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

		//Monolog::saveLog("full_quote_reponse2", "I", json_encode($response));

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
			            "member_fr_id" => $mem_fr_id,
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
						"PolicyStatus" => $newArr['policyDtls']['PolicyStatus'],
						"letter_url" => $newArr['policyDtls']['LetterURL'],
						"MemberCustomerID" => $newArr['policyDtls']['MemberCustomerID'],
						"CustomerID" => $newArr['policyDtls']['MemberCustomerID'],
						"create_policy_type" => $create_policy_type,
						"ReceiptNumber" => $newArr['receiptObj']['ReceiptNumber'],
						//"COI_url" => $newArr['policyDtls']['COIUrl'],
						"relationship_code" => $rel_code,
		                "fr_ids" => $fr_ids,
					);
					
					$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($api_insert) ,"product_id"=> $data['proposal_data']['product_code'], "type"=>"api_insert"];
					
					$dataArray['tablename'] = 'logs_docs'; 
					$dataArray['data'] = $request_arr; 
					$this->Logs_m->insertLogs($dataArray);
					
					$this->db->insert('api_proposal_response', $api_insert);

				}else{
					
					///------- @author : Guru --------------------------//
					$request_failure_arr = ["lead_id" => $data['customer_data']['lead_id'],"product_id" => $data['proposal_data']['product_code'], "res" => $errorObj['ErrorMessage'], "type" => "failure_reason_".$data['proposal_data']['policy_sub_type_id']];
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
			
			return $return_data = array(
				'status'=>'error',
				"msg" => "Quote error"
			);
		}

}


function get_profile($emp_id)
{
	return $this->db->query("select e.* from employee_details as e left join master_salutation as m ON e.salutation = m.s_id where e.emp_id='$emp_id'")->row();
}

function get_customer_quote_data($emp_id){
	return $this->db->query("select etp.* from employee_to_product as etp where etp.emp_id = '$emp_id'")->row();
}

function getProposalData($emp_id,$policy_no)
{
	$memberwise_check = $this
		->db
		->query('SELECT e.HB_policy_type from product_master_with_subtype AS e,employee_policy_detail epd where e.id = epd.product_name and e.policy_subtype_id = epd.policy_sub_type_id and  epd.policy_detail_id = "' . $policy_no . '"')->row_array() ['HB_policy_type'];

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
	->query('SELECT p.created_date,p.id,p.IMDCode,p.proposal_no,p.emp_id,p.sum_insured,p.premium,epd.policy_no,mgc.group_code,e.master_policy_no,e.product_name,pd.txndate,pd.payment_type,e.plan_code,e.api_url,e.product_code,pd.TxRefNo,pd.bank_name,epd.start_date,p.branch_code,pm.familyConstruct,mgc.spouse_group_code,epd.policy_sub_type_id,pd.payment_status,e.SourceSystemName_api,e.HB_source_code,e.HB_policy_type,e.HB_custid_concat_string FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,proposal_member AS pm,family_relation AS fr, employee_details AS ed,master_group_code AS mgc,payment_details as pd where epd.product_name = e.id AND p.emp_id = "' . $emp_id . '" AND p.policy_detail_id = "' . $policy_no . '" AND epd.policy_detail_id = p.policy_detail_id AND p.id = pm.proposal_id AND e.policy_subtype_id = epd.policy_sub_type_id '.$extra_condition.' AND p.sum_insured = mgc.si_group AND e.product_code = mgc.product_code AND p.id = pd.proposal_id AND pm.family_relation_id = fr.family_relation_id AND fr.emp_id = ed.emp_id group by p.id');

	
	if ($query)
	{
		$query = $query->row_array();
	}
	else
	{
		$query = [];
	}
	//echo $this->db->last_query();exit;
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
	$response = $this->db->select('*,mfr.relation_code,mfr.fr_name')
	->from('member_policy_nominee AS mpn,master_family_relation as mfr')
	->where('mpn.emp_id', $emp_id)
	->where('mpn.fr_id = mfr.fr_id')
	->get()->row_array();
	if ($response) {
		return $response;
	}
}

function real_pg_check($lead_id)
{
	$check_pg = false;

	$query = $this
	    ->db
	    ->query("SELECT ed.lead_id,ed.emp_id,product_id FROM employee_details as ed where ed.lead_id ='" . $lead_id."' AND ed.product_id IN ('D01','D02')")->row_array();
	if ($query)
	{

	    
	    $vertical = 'AXATGRP';
	    $pmode = 'PP';

	    $CKS_data = "AX|" . $vertical . "|LEADID|" . $query['lead_id'] . "|" . $this->hash_key;

	    $CKS_value = hash($this->hashMethod, $CKS_data);

	    $url = "https://pg_uat.adityabirlahealth.com/PGMANDATE/service/api/enquirePayment";
	    $fqrequest = array(
	        "signature" => $CKS_value,
	        "Source" => "AX",
	        "Vertical" => $vertical,
	        "SearchMode" => "LEADID",
	        "UniqueIdentifierValue" => $query['lead_id'],
	        "PaymentMode" => $pmode
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
		
	    if ($err)
	    {
	        $request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest) , "res" => json_encode($err) , "product_id" => $query['product_id'], "type" => "pg_real_fail"];

	        $dataArray['tablename'] = 'logs_docs';
	        $dataArray['data'] = $request_arr;
	        $this
	            ->Logs_m
	            ->insertLogs($dataArray);

	    }
	    else
	    {

	        if ($result && $result['PaymentStatus'] == 'PI')
	        {
	            $arr_update = ["is_payment_initiated" => 1];
	            $this
	                ->db
	                ->where("lead_id", $query['lead_id']);
	            $this
	                ->db
	                ->update("employee_details", $arr_update);
	        }

	        if ($result && $result['PaymentStatus'] == 'PR')
	        {

	            $TxStatus = "success";
	            $TxMsg = "No Error";

	            $request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest) , "res" => json_encode($result) , "product_id" => $query['product_id'], "type" => "pg_real_success"];

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
	                    ->query("select * from emandate_data where lead_id=" . $query['lead_id'])->row_array();

	                if ($result['EMandateStatus'] == 'MS')
	                {
	                    $mandate_status = 'Success';
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
	                        ->where("lead_id", $query['lead_id']);
	                    $this
	                        ->db
	                        ->update("emandate_data", $arr);
	                }
	                else
	                {

	                    $arr = ["lead_id" => $query['lead_id'], "TRN" => $result['EMandateRefno'], "status_desc" => $result['EMandateStatusDesc'], "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($result['EMandateDate'])) , "Registrationmode" => $result['Registrationmode'], "EMandateFailureReason" => $result['EMandateFailureReason'], "MandateLink" => $result['MandateLink']];

	                    $this
	                        ->db
	                        ->insert("emandate_data", $arr);
	                }

	                if ($mandate_status == 'Success')
	                {
	                    $this->send_message($query['lead_id'], 'success');
	                }

	                if ($mandate_status == 'Fail')
	                {
	                    $this->send_message($query['lead_id'], 'fail');
	                }

	                if ($result['paymentMode'] == 'PP' && ($result['Registrationmode'] == 'SAD' || $result['Registrationmode'] == 'EMI'))
	                {
	                    $this->send_message($query['lead_id'], 'SAD_EMI_one');
	                    $this->send_message($query['lead_id'], 'SAD_EMI_two');
	                }

	            }

	            $check_pg = true;

	        }
	        else
	        {

	            $request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest) , "res" => json_encode($result) , "product_id" => $query['product_id'], "type" => "pg_real_fail"];

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


function update_rejected_m()
{
	$query = $this
	->db
	->query("select ed.lead_id,ed.emp_id,p.modified_date,ed.created_at from employee_details as ed,user_payu_activity as ua,proposal as p where ed.emp_id = ua.emp_id AND ed.emp_id = p.emp_id AND p.status = 'Payment Pending'  AND ua.status = 0 AND ed.product_id in('R06','R11','T01','T03') group by p.emp_id")->result_array();
	
	if($query)
	{
		foreach($query as $val){
			
			//604800 - 7days
			//1296000 - 15days
			
			$start_date = strtotime($val['modified_date']);
			$end_date = strtotime(date('Y-m-d H:i:s'));
			$date_diff = ($end_date - $start_date)/60/60/24;
			
			$start_date_lead = strtotime($val['created_at']);
			$end_date_lead = strtotime(date('Y-m-d H:i:s'));
			$date_diff_lead = ($end_date_lead - $start_date_lead)/60/60/24;
			
			if($date_diff > 6 || $date_diff_lead > 14){
				//echo $val['emp_id'];
				//echo $date_diff;exit;
				$this->db->where("emp_id",$val['emp_id']);
				$this->db->update("proposal",["status"=>"Rejected"]);
				
				$this->db->where("emp_id",$val['emp_id']);
				$this->db->update("user_payu_activity",["status"=>"1"]);
				
				//employee disposition status add
				$agent_data = $this->db->query("select a.agent_name from employee_details ed,tls_agent_mst a where ed.assigned_to = a.id and ed.emp_id='".$val['emp_id']."'")->row_array();
				
				$this->db->insert("employee_disposition",["emp_id" => $val['emp_id'],"disposition_id" => 50,"agent_name" => $agent_data['agent_name'],"date" => date('Y-m-d H:i:s')]);
						
			}
			
			
		}
		
	}
	
}

function payment_url_send_m() 
{
		$aTLSession = $this->session->userdata('telesales_session');
		$product_id = $this->input->post('product_id');
		if(empty($product_id)){
			$product_id = 'R06';
		}
		/*if($product_id == 'R11'){
			$product_id = 'R11';
		}else{
		$product_id = 'R06';
		}*/
		if($aTLSession && $aTLSession['emp_id']){
		  $emp_id = $aTLSession['emp_id'];
		}else{
		  $emp_id_encrypt = $this->input->post('emp_id');
		  $emp_id = encrypt_decrypt_password($emp_id_encrypt,"D");
		}
		
		// $emp_id = '620520';
		// print_r($_SESSION);

		if($emp_id){

		$query_check = $this->db->query("select ed.product_id,ed.emp_id,ed.lead_id,p.created_date,ed.email,ed.mob_no,ed.emp_firstname,p.id from employee_details as ed,proposal as p where ed.emp_id = p.emp_id and p.status = 'Payment Pending' and ed.emp_id = '$emp_id' group by p.emp_id")->row_array();
		
		if($query_check){

			$res_arr = ["modified_date" => date('Y-m-d H:i:s')];	
			$this->db->where("emp_id",$query_check['emp_id']);
			$this->db->update("proposal",$res_arr);
			
			$this->db->query("DELETE FROM ghi_quick_quote_response WHERE emp_id = '".$emp_id."'");
			
			$click_url =$this->db->query("select click_pss_url from product_master_with_subtype where product_code = 'R06'")->row_array();
			
				$emp_id_encrypt = encrypt_decrypt_password($query_check['emp_id']);
				
					// if(!empty($this->input->post('emp_id'))){
						// $url = base_url("tls_payment_redirect_view/".$emp_id_encrypt);
						// $name_data = "payu";
					// }else{
						$url = base_url("tele_proposal_summary/".$emp_id_encrypt."/".encrypt_decrypt_password(date('Y-m-d H:i:s'))."/".$product_id);
						$name_data = "summary";
					//}
				
					$url_req = "https://api-alerts.kaleyra.com/v5/?api_key=A6ef09c1692b5f41a8cd5bf4ac5e22695&method=txtly.create&url=".urlencode($url)."&title=xyz";
					
					$curl = curl_init();
				
					curl_setopt_array($curl, array(
					  CURLOPT_URL => $url_req,
					  CURLOPT_PROXY => "185.46.212.88",
					   CURLOPT_PROXYPORT => 443,
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
					
					$request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($url_req),"res" => json_encode($result) ,"product_id"=> $query_check['product_id'], "type"=>"bitly_url_".$name_data];
					
					$dataArray['tablename'] = 'logs_docs'; 
					$dataArray['data'] = $request_arr; 
					$this->Logs_m->insertLogs($dataArray);
					
					$data = json_decode($result,true);
					
					if($data['txtly'] == ''){
						$data['txtly'] = $url;
					}
					
					$senderID = 1;
					
					if (!preg_match("@^[hf]tt?ps?://@", $data['txtly'])) {
						$data['txtly'] = "http://" . $data['txtly'];
					}

					$name  = strtoupper($query_check['emp_firstname']);
					$alertMode = 3;
					if(strlen($name) > 30){
						$name = substr($name, 0, 30);
					}
					if ($data['txtly'] == $url)
						{
							if(strlen($url) > 30){
								$alertMode = 1;
							}
						}

					$AlertV1 = $name;
					$AlertV2 = $data['txtly'];
					$AlertV3 = date('m-d-Y', strtotime($query_check['created_date']. ' + 20 days'));
					$AlertV4 = $query_check['lead_id'];
					$AlertV5 = 'PaymentSupport.HealthInsurance@adityabirlacapital.com';
				
								
					$parameters =[
					"RTdetails" => [
				   
						"PolicyID" => '',
						"AppNo" => 'HD100017934',
						"alertID" => 'A1413',
						"channel_ID" => 'Axis Telesales',
						"Req_Id" => 1,
						"field1" => '',
						"field2" => '',
						"field3" => '',
						"Alert_Mode" => $alertMode,
						"Alertdata" => 
							[
								"mobileno" => $query_check['mob_no'],
								"emailId" => $query_check['email'],
								"AlertV1" => $AlertV1,
								"AlertV2" => $AlertV2,
								"AlertV3" => $AlertV3,
								"AlertV4" => $AlertV4,
								"AlertV5" => $AlertV5,
								"AlertV6" => '',
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
					
					$query = $this
					->db
					->query("select * from user_payu_activity where emp_id='".$query_check['emp_id']."'")->row_array();
					
					if(empty($query)){
						$this->db->insert("user_payu_activity",["emp_id" => $query_check['emp_id']]);
					}
				
					$res_data = json_decode($response,true);
					
					/* comment this on prod envirment start */
					$res_arr = ["sms_trigger_status" => '1'];	
					$this->db->where("emp_id",$query_check['emp_id']);
					$this->db->update("proposal",$res_arr);
					/* end */
						
					if($res_data && $res_data['STATUS']=='0'){
						//link trigger check update
						$res_arr = ["sms_trigger_status" => '1'];
						
						$this->db->where("emp_id",$query_check['emp_id']);
						$this->db->update("proposal",$res_arr);
						
						//employee disposition status add
						$agent_data = $this->db->query("select a.agent_name from employee_details ed,tls_agent_mst a where ed.assigned_to = a.id and ed.emp_id='".$query_check['emp_id']."'")->row_array();
						
						$this->db->insert("employee_disposition",["emp_id" => $query_check['emp_id'],"disposition_id" => 45,"agent_name" => $agent_data['agent_name'],"date" => date('Y-m-d H:i:s'),"remarks"=>""]);
					}
					
					$request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($parameters),"res" => json_encode($response) ,"product_id"=> $query_check['product_id'], "type"=>"sms_logs_redirect_".$name_data];
						
					$dataArray['tablename'] = 'logs_docs'; 
					$dataArray['data'] = $request_arr; 
					$this->Logs_m->insertLogs($dataArray);
		}

	}
		
}



function agent_coi_mail_trigger_m() 
{
	$emp_id_encrypt = $this->input->post('emp_id');
	$emp_id = encrypt_decrypt_password($emp_id_encrypt,"D");
	//$emp_id = $this->input->post('emp_id');
		  
	$data_policy = $this->db->query("SELECT GROUP_CONCAT(DISTINCT(apr.certificate_number)) certificate_number,ed.email,ed.emp_firstname,ed.emp_lastname,pd.txndate FROM api_proposal_response apr,employee_details ed,proposal p,payment_details pd WHERE apr.emp_id = ed.emp_id and ed.emp_id = p.emp_id and p.id = pd.proposal_id and apr.emp_id = '".$emp_id."' GROUP BY apr.emp_id")->row_array();
	
	$return_data = array(
						'status'=>"error",
						"msg" => "Re-trigger Failure"
					);
	
	if(!empty($data_policy['certificate_number'])){
		
		$this->load->library('email');
		$arr_coi = explode(',',$data_policy['certificate_number']);
	
		foreach($arr_coi as $key => $val){ 
			$coi_data = $this->external_obj_api->coi_download_m($val);
		
			if($coi_data['status'] == 'success'){
				
				$pay_date = explode(' ',$data_policy['txndate']);
				$pre_due_date = date('d-m-Y',strtotime($pay_date[0]. ' + 364 days'));
				
				$this->email->clear(TRUE); 
				$this->email->from('communication.abh@adityabirlacapital.com', 'ABHI');
				//$this->email->from('clientfeedback.abibl@adityabirlainsurancebrokers.com', 'ABHI');
				$this->email->to($data_policy['email']); 
				$this->email->cc('Harshada.Deolekar-v@adityabirlacapital.com'); 
				$this->email->bcc('shruthi.Nair@qualitykiosk.com'); 
				$this->email->subject('Certificate Number'.($key+1));
				$this->email->message('<html>
				   <head>
					  <meta name="viewport" content="width=device-width, initial-scale=1.0">
					  <style type="text/css">
						 .center-text {
						 text-align: center;
						 }
						 p {
						 line-height: 28px;
						 }
						 .table1 td, th {
						 border: double #828282;
						 text-align: left;
						 padding: 8px;
						 }
						 .container-fluid {
						 padding-left: 50px;
						 padding-right: 50px;
						 }
						 body {
						 font-family: calibri;
						 }
						 body {
						 font-family: calibri;
						 font-size: 18px;
						 }
						 .mem-title {
						 font-size: 18px;
						 border: 1px solid;
						 text-align: center;
						 font-weight: 600;
						 padding: 5px;
						 }
					  </style>
				   </head>
				   <body>
					  <div class="">
						 <div>
							<p> <b>Dear Customer, </b></p>
							<p>Greetings from Aditya Birla Health Insurance Co. Limited!
							   <br>
							   We thank you for the trust you have shown by choosing Aditya Birla Health Insurances Group Activ Health plan as your preferred choice, for your health insurance needs. It is our pleasure to have you as a valued customer.
							   <br>
							   Your plan details are as follows:
							</p>
						 </div>
						 <table class="table1 mar-20">
							<tr style="font-weight: bold;">
							   <td>Insured Name</td>
							   <td>Plan Name</td>
							   <td>Certificate Of Insurance No.</td>
							   <td>Cover Period</td>
							   <td> Annual Premium Due Date</td>
							</tr>
							<tr>
							   <td>'.$data_policy['emp_firstname'].' '.$data_policy['emp_lastname'].'</td>
							   <td>Group Activ Health</td>
							   <td>'.$val.'</td>
							   <td>1 Year</td>
							   <td>'.$pre_due_date.'</td>
							</tr>
						 </table>
						 <div>
							<p>Your Certificate of Insurance is attached herewith.
							   <br>
							   It would be our pleasure to assist you in case you require any help. Do call our helpline 1800-270-7000 or email us at care.healthinsurance@adityabirlacapital.com
							   <br>
							   We look forward to a long lasting relationship with you and assure you of our best services at all times.
							   <br>
							   <br>
							   <b>Warm Regards,</b><br>
							   <b> Aditya Birla Health Insurance Co. Ltd. <b>
							</p>
						 </div>
					  </div>
				   </body>
				</html>');  

				//$this->email->attach(base_url().'application'.$coi_data['url']);
				
				$att = APPPATH.$coi_data['url'];
				$att = str_replace("//","/",$att);
				$this->email->attach($att);

				$this->email->send();
				//print_pre($this->email->print_debugger());exit;
				
				$return_data = array(
						'status'=>"success",
						"msg" => "Re-trigger Success"
					);
			}
		}
		
		/*$this->load->library('email');
		$this->email->clear(TRUE); 
		$this->email->from('communication.abh@adityabirlacapital.com', 'Siddhi');
		//$this->email->from('clientfeedback.abibl@adityabirlainsurancebrokers.com', 'Siddhi');
		$this->email->to('siddhi.fyntune@gmail.com'); 
		$this->email->cc('bhushan.bist@fyntune.com'); 
		$this->email->subject('COI');
		$this->email->message('PFA');

		$is_coi_success = 0;
		
		$arr_coi = explode(',',$data_policy['certificate_number']);
	
		foreach($arr_coi as $key => $val){ 
		
			$coi_data = $this->external_obj_api->coi_download_m($val);
		
			if($coi_data['status'] == 'success'){
				$is_coi_success = 1;
				$arr[] = str_replace("//","/",APPPATH.$coi_data['url']);
				
			}
		}
		//print_r($arr);exit;
		
		if($is_coi_success){
			$this->email->attach($arr);
			$this->email->send();
			//print_pre($this->email->print_debugger());exit;
			$return_data = array(
						'status'=>"success",
						"msg" => "Re-trigger Success"
					);
		}*/
	
	}
	
	return $return_data;

}



	
	
	

}



