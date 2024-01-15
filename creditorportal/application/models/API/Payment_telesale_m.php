<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payment_telesale_m extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		
		//$this->load->model("API/Payment_integration_freedom_plus", "external_obj_api", true);
		$this->load->model("Logs_m");
	}
	
    function coi_download_m($certificate_no = '')
    {
        if (empty($certificate_no))
        {
            $certificate_no = $this
                ->input
                ->post('certificate_no');
        }
		

        $data['cust_details'] = $this
            ->db
            ->query("select ed.emp_id,ed.lead_id,ed.product_id,apr.certificate_number,apr.COI_url,apr.pr_api_id from employee_details as ed,proposal as p,api_proposal_response as apr where ed.emp_id = p.emp_id and p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id and p.status in('Success','Payment Received') and apr.certificate_number = '$certificate_no' ")->row_array();
        $product_code = $data['product_id'];
		$emp_id = $data['emp_id'];
		$quer = $this->db->query("select GROUP_CONCAT(DISTINCT(master_policy_no)) as master_policy_no,product_name,policy_parent_id,id from product_master_with_subtype where product_code = '$product_code'")->row_array();
		$policy_parent_id = $quer['policy_parent_id'];
		$product_id = $quer['id'];
		$policy_data = $this->db->query("select GROUP_CONCAT(DISTINCT(policy_no)) as policy_name,policy_detail_id,policy_sub_type_id from employee_policy_detail where parent_policy_id = '$policy_parent_id' and product_name = '$product_id'")->result_array();
	//	print_r($this->db->last_query());
	
		foreach($policy_data as $value)
		{
			
			$policy_detail_id = $value['policy_detail_id'];
			$policy_sub_type_id = $value['policy_sub_type_id'];
			$policy_sub_type_name = $this->db->query("select policy_sub_type_name from master_policy_sub_type where policy_sub_type_id = '$policy_sub_type_id'")->row_array();
			
			$quer_det = $this->db->query("select epm.policy_member_first_name,epm.policy_member_last_name,epm.policy_mem_dob,epm.policy_mem_gender,epm.policy_mem_sum_insured as cover from family_relation as fr,employee_policy_member as epm where fr.family_relation_id = epm.family_relation_id and fr.emp_id = '$emp_id' and epm.policy_detail_id = '$policy_detail_id'")->result_array();
			$data_ref['insured_member'][$policy_sub_type_id]['member'] = $quer_det;
			$data_ref['insured_member'][$policy_sub_type_id]['policy_sub_type_name'] = $policy_sub_type_name['policy_sub_type_name'];
		}
$data['insured_details'] =$data_ref; 

        return $result;
    }
	function get_all_quote_call($emp_id)
	{ 
		// echo 'in quote';exit;
		$get_data = $this->db->query('SELECT ed.emp_id,mpst.policy_subtype_id,p.policy_detail_id,mpst.HB_policy_type FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.emp_id="' .$emp_id. '" ')->result_array();
			
		foreach ($get_data as $vall)
		{
			
			if($vall['HB_policy_type'] == 'ProposalWise'){
				
				$query_check_ghi = $this->db->query("select id from ghi_quick_quote_response where policy_subtype_id = '".$vall['policy_subtype_id']."' and emp_id='$emp_id' and status = 'success'")->row_array();
				// print_pre($query_check_ghi);exit;
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
				//echo $check_status;exit;
				if($check_status == 'error')
				{
					foreach ($member_data as $key => $value)
					{
						$value['key_id'] = $key+1;
						$mem_data[0] = $value;
							
						$is_data = $this->db->query("select * from ghi_quick_quote_response where policy_subtype_id != 1 and emp_id='$emp_id' and policy_subtype_id = '".$value['policy_subtype_id']."' and fr_id = '".$value['fr_id']."' ")->row_array();
						
						if($is_data){
							
						$query_check = $this->db->query("select * from ghi_quick_quote_response where policy_subtype_id != 1 and emp_id='$emp_id' and policy_subtype_id = '".$value['policy_subtype_id']."' and fr_id = '".$value['fr_id']."' and status = 'error'")->row_array();
						
							if($query_check){
							  $policy_data = $this->get_quote_data($emp_id, $value['policy_detail_id'], $mem_data);
							}
							
						}else{
							
							$arr = ["emp_id" => $emp_id,"policy_subtype_id" => $value['policy_subtype_id'],"fr_id"=>$value['fr_id'],"status"=>"error"];
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
		//print_r($member_data);
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
	//echo 123;die;	
		//print_r($aTLSession);die;
			$update_data = $this
			->db
			->query('SELECT p.id,p.emp_id,p.policy_detail_id,p.status,p.count,e.policy_subtype_id,e.product_code,e.HB_policy_type
				FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,
				employee_details AS ed
				where epd.product_name = e.id
				AND p.emp_id = ed.emp_id
				AND ed.lead_id = "' .$CRM_Lead_Id. '"
				AND epd.policy_detail_id = p.policy_detail_id');
			$update_data = $update_data->result_array();
			//print_r($update_data);die;
		//update payment confirmation hit count
		$this->db->query("UPDATE proposal SET count = count + 1 WHERE emp_id ='".$update_data[0]['emp_id']."'");
		
		foreach ($update_data as $update_payment)
		{
		
			 if($update_payment['status']!='Success'){

		  // check first hit or not
				  
		   // update proposal status - Payment Received
			   $arr_new = ["status" => "Payment Received","modified_date" => date('Y-m-d H:i:s')];
			   $this->db->where('id', $update_payment['id']);
			   $this->db->update("proposal", $arr_new);
			   
			   //employee disposition status add
$aTLSession = $this->session->userdata('telesales_session');
					$agent_id_decrypt = encrypt_decrypt_password($aTLSession['agent_id'],'D');
					$agent_name = $this->db->select('agent_name')->from('tls_agent_mst')->where('id',$agent_id_decrypt)->get()->row_array()['agent_name'];
				
				$this->db->insert("employee_disposition",["emp_id" => $update_payment['emp_id'],"disposition_id" => 46,"agent_name" => "","date" => date('Y-m-d H:i:s')]);
			   
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
					
					if ($query2['status']=='Success' || $update_payment['product_code'] == 'R10')
					{
						
						$api_response_tbl = $this->Memberwise_policy_call($update_payment['emp_id'], $update_payment['policy_detail_id'],$cron_policy_check);
					
					}
					
				}
						
				 if($api_response_tbl['status']=='error'){
				  
				    $return_data['check'][] = 'error';
					$return_data['code'] = '0';
					$return_data['msg'] = $api_response_tbl['msg'];

				 }else{
				 // update proposal status - Success  
				  $arr = ["status" => "Success","modified_date" => date('Y-m-d H:i:s')];
				  $this->db->where('id', $update_payment['id']);
				  $this->db->update("proposal", $arr);
				  
				  //employee disposition status add
					//$agent_data = $this->db->query("select a.agent_name from employee_details ed,tls_agent_mst a where ed.assigned_to = a.id and ed.emp_id='".$update_payment['emp_id']."'")->row_array();
					$aTLSession = $this->session->userdata('telesales_session');
					
					$agent_id_decrypt = encrypt_decrypt_password($aTLSession['agent_id'],'D');
					$agent_name = $this->db->select('agent_name')->from('tls_agent_mst')->where('id',$agent_id_decrypt)->get()->row_array()['agent_name'];

					//$this->db->insert("employee_disposition",["emp_id" => $update_payment['emp_id'],"disposition_id" => 48,"agent_name" => $agent_name,"date" => date('Y-m-d H:i:s')]);
				  
				  //HB emandate call
				  //$this->external_obj_api->emandate_HB_call($update_payment['emp_id']);

					$return_data['check'][] = 'Success';
					$return_data['code'] = '1';
					$return_data['msg'] = $api_response_tbl['msg'];
				 }
				 
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
		$data['member_data'] = (array)$this->get_all_member_data($emp_id, $policy_detail_id);
		$data['nominee_data'] = (array)$this->get_all_nominee($emp_id);
		$data['proposal_data'] = (array)$this->getProposalData($emp_id, $policy_detail_id);
		// print_r($data);exit;
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
		if ($data['proposal_data']['product_code'] == 'T01' || $data['proposal_data']['product_code'] == 'T03'){
			$occupation_check = $this->db->query('SELECT occupation_id from master_occupation where id = "' . $data['customer_data']['occupation'] . '" ')->row_array();
			if (isset($occupation_check['occupation_id']))
			{
				$occupation = $occupation_check['occupation_id'];
			}
		}


		//added by upendra on 09-04-2021 (deductable logic for T03)
		if ($data['proposal_data']['product_code'] == 'T03' && $data['proposal_data']['policy_sub_type_id'] == 1)
		{
			if(!empty($data['customer_data']['deductable'])){

				$data['proposal_data']['sum_insured'] = ($data['proposal_data']['sum_insured'] - $data['customer_data']['deductable']);
				
				$GHI_supertopup_data = $this
					->db
					->query("select * from master_group_code where si_group = '" . $data['proposal_data']['sum_insured'] . "' and family_construct = '" . $data['proposal_data']['familyConstruct'] . "' and product_code = '".$data['proposal_data']['product_code']."'")->row_array();
				
				$data['proposal_data']['group_code'] = $GHI_supertopup_data['group_code'];
				$data['proposal_data']['spouse_group_code'] = $GHI_supertopup_data['spouse_group_code'];
				
			}
		}
		
		for ($i = 0;$i < $totalMembers;$i++)
		{

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
			
			$check_dedupe = common_function_ref_id_exist($data['customer_data']['lead_id']);
            if($check_dedupe['status'] == 'error'){
                echo $check_dedupe['msg']; exit;
            }

			

			if(($data['proposal_data']['familyConstruct'] == '1A+1K' || $data['proposal_data']['familyConstruct'] == '1A+2K') && $data['member_data'][$i]['fr_id'] == 1){
              $data['proposal_data']['group_code'] = $data['proposal_data']['spouse_group_code'];
             }
			 
			$query = $this
			->db
			->query('SELECT pds.sub_member_code from employee_declare_member_sub_type as edmsp JOIN policy_declaration_subtype as pds ON edmsp.declare_sub_type_id = pds.declare_subtype_id where edmsp.emp_id="'.$emp_id.'" AND edmsp.policy_member_id = "'.$data['member_data'][$i]['policy_member_id'].'" ')->result_array();
			
			$abc = [];
			if(!empty($query))
			{
				foreach ($query as $key => $value) {
					
					$abc[] = ["PEDCode" => $value['sub_member_code'], "Remarks" => null];
					
				}
			}else{
				
				$abc[] = ["PEDCode" => null, "Remarks" => null];
			}



			//added by upendra on 09-04-2021 (memberwise remark logic)
			$memberPEDARRAY = [];
			if ($data['proposal_data']['product_code'] == 'T03' || $data['proposal_data']['product_code'] == 'T01' || $data['proposal_data']['product_code'] == 'R06'){
				
				$new_remarks = $data['customer_data']['new_remarks'];
				$new_remarks = stripslashes(html_entity_decode($new_remarks));
				$new_remarks = json_decode($new_remarks, TRUE);

				$current_relation_code = trim($data['member_data'][$i]['family_relation_id']);
				foreach ($new_remarks as $k=>$v){ 

					if($v['relation_code'] == $current_relation_code){

						$v_remark = trim($v['remark']);
						if($v_remark != ""){

							// $memberPEDARRAY  = ["PEDCode" => "PE0186", "Remarks" => $v['remark']];
							$memberPEDARRAY  = ["PEDCode" => "PE675", "Remarks" => $v['remark']];
							if($abc[0]['PEDCode'] == null){
                                $abc = $memberPEDARRAY;
                            }else{
                                $abc[] = $memberPEDARRAY;
                            }

						}else{

							$memberPEDARRAY  = ["PEDCode" => null, "Remarks" => null];
							if(empty($abc)){
                                $abc[] = $memberPEDARRAY;
                            }

						}
						
					}

				}
			}
			
			$member[] = ["MemberNo" => $i + 1, "Salutation" => (($data['member_data'][$i]['gender'] == "Male") ? 'Mr' : (($data['member_data'][$i]['age'] >= 18) ? 'Mrs' : 'Ms')) , "First_Name" => $data['member_data'][$i]['firstname'], "Middle_Name" => null, "Last_Name" => !empty(trim($data['member_data'][$i]['lastname'])) ? $data['member_data'][$i]['lastname'] : '.', "Gender" => ($data['member_data'][$i]['gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['dob'])) , "Relation_Code" => trim($data['member_data'][$i]['relation_code']), "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => $occupation_mem, "PrimaryMember" => (($i == 0) ? "Y" : "N") , "MemberproductComponents" => [["PlanCode" => $data['proposal_data']['plan_code'], "MemberQuestionDetails" => [["QuestionCode" => null, "Answer" => null, "Remarks" => null]]]], "MemberPED" => $abc, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $data['nominee_data']['nominee_fname'], "Nominee_Last_Name" => !empty(trim($data['nominee_data']['nominee_lname'])) ? $data['nominee_data']['nominee_lname'] : '.', "Nominee_Contact_Number" => $data['nominee_data']['nominee_contact'], "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data']['relation_code']), ];

		}
		
		$AnnualIncome=null;
		if($data['customer_data']['annual_income']>0)
		{
			$AnnualIncome=$data['customer_data']['annual_income'];
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

					//new telesales CR - 15-11-2021 - add code
					if($data['customer_data']['is_makerchecker_journey'] == 'yes'){
						//pass base_caller_code/DO_code in RefCode1/ref1
						$data['customer_data']['ref1'] = $data['customer_data']['agent_id'];
						//pass av_code in SPID/ref2
						$get_pickedup = $this->db->query("select picked_do_by from employee_details where lead_id = '".$data['customer_data']['lead_id']."'")->row_array();
						$get_agent_id = $this->db->query("select agent_id from tls_agent_mst where id = '".$get_pickedup['picked_do_by']."'")->row_array();
						$data['customer_data']['ref2'] = $get_agent_id['agent_id'];
					}else{
						//pass av_code in SPID/ref2 and RefCode1/ref1
						$data['customer_data']['ref1'] = $data['customer_data']['agent_id'];
						$data['customer_data']['ref2'] = $data['customer_data']['av_code'];
					}

$fqrequest = ["ClientCreation" => ["Member_Customer_ID" => $unique_ref_no, "salutation" => $data['customer_data']['salutation'], "firstName" => $data['customer_data']['emp_firstname'], "middleName" => "", "lastName" => !empty(trim($data['customer_data']['emp_lastname'])) ? $data['customer_data']['emp_lastname'] : '.', "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['bdate'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['emp_pincode'], "uidNo" => $data['customer_data']['adhar'], "maritalStatus" => null, "nationality" => "Indian", "occupation" => $occupation, "primaryEmailID" => $data['customer_data']['email'], "contactMobileNo" => substr(trim($data['customer_data']['mob_no']), -10), "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => $AnnualIncome, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address'], "homeAddressLine2" => $data['customer_data']['comm_address'], "homeAddressLine3" => $data['customer_data']['comm_address1'], "homePinCode" => $data['customer_data']['emp_pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => "", "MasterPolicyNumber" => $data['proposal_data']['master_policy_no'], "GroupID" => $data['proposal_data']['group_code'], "Product_Code" => $data['proposal_data']['plan_code'],"SumInsured_Type"=> null,"Policy_Tanure"=> "1","Member_Type_Code"=> "M209", "intermediaryCode" => $data['proposal_data']['IMDCode'], "AutoRenewal" => '0', "AutoDebit" => '0', "intermediaryBranchCode" => "10MHMUM01", "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $data['customer_data']['lead_id'], "Source_Name" => $source_name, "SPID" => $data['customer_data']['ref2'], "TCN" => null, "CRTNO" => null, "RefCode1" => $data['customer_data']['ref1'], "RefCode2" => $data['proposal_data']['branch_code'], "Employee_Number" => $data['proposal_data']['emp_id'],"enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "0", "PaymentMode" => null, "PolicyproductComponents" => [["PlanCode" => $data['proposal_data']['plan_code'], "SumInsured" => $data['proposal_data']['sum_insured'], "SchemeCode" => "4112000003"]]], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "", "modeOfEntry" => "", "cdAcNo" => null, "expiryDate" => null, "payerType" => "", "payerCode" => null, "paymentBy" => "", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => "", "collectionRcvdDate" => null, "collectionMode" => "", "remarks" => null, "instrumentNumber" => null, "instrumentDate" => null, "bankName" => null, "branchName" => null, "bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => "", "PaymentGatewayName" => "", "TerminalID" => "", "CardNo" => null]];

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
		

		if($data['proposal_data']['HB_policy_type'] == 'ProposalWise')
		{
			$query_one = $this
			->db
			->query("select * from ghi_quick_quote_response where emp_id='".$emp_id."' and policy_subtype_id = '".$data['proposal_data']['policy_sub_type_id']."'")->row_array();
			
			if($query_one > 0){
				
				$arr = ["QuotationNumber" => $policydetail['QuotationNumber'], "PolicyNumber" => $policydetail['PolicyNumber'], "MemberCustomerID" => $policydetail['MemberCustomerID'],"status"=>"success"];
				
				$update_where = ["emp_id"=>$emp_id,"policy_subtype_id" => $data['proposal_data']['policy_sub_type_id']];
				
				$this->db->where($update_where);
				$this->db->update("ghi_quick_quote_response",$arr);
			}else{
				
				$arr = ["emp_id" => $emp_id,"status"=>"success","policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'],"QuotationNumber" => $policydetail['QuotationNumber']];
				
				$this->db->insert("ghi_quick_quote_response", $arr);
			}
			
		}else{
			
			$query_one = $this
			->db
			->query("select * from ghi_quick_quote_response where emp_id='".$emp_id."' and policy_subtype_id = '".$data['proposal_data']['policy_sub_type_id']."' and fr_id = '".$data['member_data'][0]['fr_id']."'")->row_array();
			
			if($query_one > 0){
				
				$arr = ["QuotationNumber" => $policydetail['QuotationNumber'], "PolicyNumber" => $policydetail['PolicyNumber'], "MemberCustomerID" => $policydetail['MemberCustomerID'],"status"=>"success"];
				
				$update_where = ["emp_id"=>$emp_id,"policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'],"fr_id" => $data['member_data'][0]['fr_id']];
				
				$this->db->where($update_where);
				$this->db->update("ghi_quick_quote_response",$arr);
			}else{
				
				$arr = ["emp_id" => $emp_id,"status"=>"success","policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'],"fr_id" => $data['member_data'][0]['fr_id'],"QuotationNumber" => $policydetail['QuotationNumber']];
				
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
				
				$arr = ["emp_id" => $emp_id,"status"=>"error","policy_subtype_id" => $data['proposal_data']['policy_sub_type_id']];
				
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
				
				$arr = ["emp_id" => $emp_id,"status"=>"error","policy_subtype_id" => $data['proposal_data']['policy_sub_type_id'],"fr_id" => $data['member_data'][0]['fr_id']];
				
				$this->db->insert("ghi_quick_quote_response", $arr);
			}
			
		}
		
		///------- @author : Guru --------------------------//
		$request_failure_arr = ["lead_id" => $data['customer_data']['lead_id'],/* "req" => json_encode($fqrequest) ,*/ "product_id" => $data['proposal_data']['product_code'], "res" => $errorObj['ErrorMessage'], "type" => "failure_reason_".$data['proposal_data']['policy_sub_type_id']];
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
{//echo 23;die;

	$err = 1;
	/*check for payment done or not and prevent multiple policy create at same time*/
	$extra_check_data = $this
		->db
		->query("select pd.payment_status,pd.TxRefNo,pd.TxStatus,ed.is_policy_issue_initiated from proposal as p,employee_details as ed,payment_details as pd  where ed.emp_id = p.emp_id and p.id = pd.proposal_id and ed.emp_id ='" . $emp_id . "'")->row_array();
	/* ($extra_check_data['payment_status'] == 'No Error' && !empty($extra_check_data['TxRefNo']) && $extra_check_data['TxStatus'] == 'success')
	{*/
	
	
	//print_pre($extra_check_data);die;
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
	//print_pre($data);die;
	$policy_sub_type_id =  $data['proposal_data']['policy_sub_type_id'];
	$sub_type_name = $this->db->query("select short_code from master_policy_sub_type where policy_sub_type_id = '$policy_sub_type_id'")->row_array();
	
	if($data['proposal_data']['HB_policy_type'] == 'ProposalWise')
	{
		
		$collection_amt['pay_amt'] = $data['proposal_data']['premium'];

		
	}else{
		
		
		
		$collection_amt['pay_amt'] = $data['member_data'][0]['policy_mem_sum_premium'];
		
		
	}
	
	$transaction_date = explode(" ",$data['proposal_data']['txndate']);
	$trans_date = date("Y-m-d", strtotime($transaction_date[0]));
	if(!empty($data)){
		//echo 1;die;
		$err = 0;
	 $CertificateNumber = $this->generate_coi($sub_type_name['short_code']);
	// print_R($CertificateNumber);die;
	}
	


if ($err == 1)
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


		$return_data['status'] = 'Success';
		$return_data['msg'] = $errorObj['ErrorMessage'];
		
		$create_policy_type = 0;
		if($cron_policy_check){
			$create_policy_type = 1;
		}
$startDate = date('Y-m-d');
            $EndDate = date('Y-m-d', strtotime($startDate . ' + 364 days'));
		$api_insert = array(
			"emp_id" => $emp_id,
			"proposal_id" => $data['proposal_data']['id'],
            "member_fr_id" => $mem_fr_id,
			"certificate_number" => $CertificateNumber,
			"gross_premium" => $collection_amt['pay_amt'],
			"status" => "Success",
			//"status" => $errorObj['ErrorMessage'],
			"start_date" => $startDate,
			"end_date" => $EndDate,
			"created_date" => date('Y-m-d H:i:s'),
			"proposal_no_lead"=>$data['proposal_data']['proposal_no'],
			
	
			//"COI_url" => $newArr['policyDtls']['COIUrl'],
		);
		
		$request_arr = ["lead_id" => $data['customer_data']['lead_id'], "req" => json_encode($api_insert) ,"product_id"=> $data['proposal_data']['product_code'], "type"=>"api_insert"];
		
		$dataArray['tablename'] = 'logs_docs'; 
		$dataArray['data'] = $request_arr; 
		$this->Logs_m->insertLogs($dataArray);
		
		$this->db->insert('api_proposal_response', $api_insert);

	
	
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



function get_profile($emp_id)
{
	return $this->db->query("select e.* from employee_details as e left join master_salutation as m ON e.salutation = m.s_id where e.emp_id='$emp_id'")->row();
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
	->query('SELECT p.created_date,p.id,p.IMDCode,p.proposal_no,p.emp_id,p.sum_insured,p.premium,epd.policy_no,mgc.group_code,e.master_policy_no,e.product_name,pd.txndate,pd.payment_type,e.plan_code,e.api_url,e.product_code,pd.TxRefNo,pd.ifscCode,pd.branch,pd.bank_name,epd.start_date,e.EW_master_policy_no,p.branch_code,mgc.EW_group_code,pm.familyConstruct,mgc.spouse_group_code,epd.policy_sub_type_id,pd.payment_status,pd.transaction_no,e.SourceSystemName_api,e.imd_refer_product_code,e.HB_source_code,e.HB_policy_type,e.HB_custid_concat_string FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,proposal_member AS pm,family_relation AS fr, employee_details AS ed,master_group_code AS mgc,payment_details as pd where epd.product_name = e.id AND p.emp_id = "' . $emp_id . '" AND p.policy_detail_id = "' . $policy_no . '" AND epd.policy_detail_id = p.policy_detail_id AND p.id = pm.proposal_id AND e.policy_subtype_id = epd.policy_sub_type_id '.$extra_condition.' AND p.sum_insured = mgc.si_group AND e.product_code = mgc.product_code AND p.id = pd.proposal_id AND pm.family_relation_id = fr.family_relation_id AND fr.emp_id = ed.emp_id group by p.id');

	
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
 public function generate_coi($policy_subtype_name)
    {
        $coi_no = $policy_subtype_name . '-XL-AS-' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);

        $proposal_id = $this->db->select('*')
            ->from('api_proposal_response')
            ->where('certificate_number', $coi_no)
            ->limit(1)
            ->get()
            ->row_array();
        if ($proposal_id > 0) {

            $this->generate_coi();
        }
        return $coi_no;
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
					$aTLSession = $this->session->userdata('telesales_session');
		$agent_id_decrypt = encrypt_decrypt_password($aTLSession['agent_id'],'D');
	
						$agent_name = $this->db->select('agent_name')->from('tls_agent_mst')->where('id',$agent_id_decrypt)->get()->row_array()['agent_name'];

				//$agent_data = $this->db->query("select a.agent_name from employee_details ed,tls_agent_mst a where ed.assigned_to = a.id and ed.emp_id='".$val['emp_id']."'")->row_array();
				
				$this->db->insert("employee_disposition",["emp_id" => $val['emp_id'],"disposition_id" => 50,"agent_name" =>$agent_name,"date" => date('Y-m-d H:i:s')]);
						
			}
			
			
		}
		
	}
	
}

function payment_url_send_m() 
{
		$aTLSession = $this->session->userdata('telesales_session');
		$agent_id_decrypt = encrypt_decrypt_password($aTLSession['agent_id'],'D');
	
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
			//print_R($query_check);die;
			$request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($emp_id),"res" => json_encode($query_check) ,"product_id"=> $query_check['product_id'], "type"=>"bitly_url_check"];
						
			$dataArray['tablename'] = 'logs_post_data'; 
			$dataArray['data'] = $request_arr; 
			$this->Logs_m->insertLogs($dataArray);
			
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
					  //CURLOPT_PROXY => "185.46.212.88",
					  //CURLOPT_PROXYPORT => 443,
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

					if (!preg_match("@^[hf]tt?ps?://@", $data['txtly'])) {
						$data['txtly'] = "http://" . $data['txtly'];
					}

					
					$senderID = 1;
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
						
					//if($res_data && $res_data['STATUS']=='0'){
						//link trigger check update
						$res_arr = ["sms_trigger_status" => '1'];
						
						$this->db->where("emp_id",$query_check['emp_id']);
						$this->db->update("proposal",$res_arr);
						
						//employee disposition status add
						//$agent_data = $this->db->query("select a.agent_name from employee_details ed,tls_agent_mst a where ed.assigned_to = a.id and ed.emp_id='".$query_check['emp_id']."'")->row_array();
						
						$agent_name = $this->db->select('agent_name')->from('tls_agent_mst')->where('id',$agent_id_decrypt)->get()->row_array()['agent_name'];

						$this->db->insert("employee_disposition",["emp_id" => $query_check['emp_id'],"disposition_id" => 45,"agent_name" => $agent_name,"date" => date('Y-m-d H:i:s'),"remarks"=>"Payment link trigger"]);
						// $this->db->insert("employee_disposition",["emp_id" => $query_check['emp_id'],"disposition_id" => 45,"agent_name" => $agent_data['agent_name'],"date" => date('Y-m-d H:i:s'),"remarks"=>"Payment link trigger"]);

					//}
					
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
		  
	$data_policy = $this->db->query("SELECT ed.lead_id,ed.product_id,GROUP_CONCAT(DISTINCT(apr.certificate_number)) certificate_number,ed.email,ed.emp_firstname,ed.emp_lastname,pd.txndate FROM api_proposal_response apr,employee_details ed,proposal p,payment_details pd WHERE apr.emp_id = ed.emp_id and ed.emp_id = p.emp_id and p.id = pd.proposal_id and apr.emp_id = '".$emp_id."' GROUP BY apr.emp_id")->row_array();
	
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
				//logs added 
				$request_arr = ["lead_id" => $data_policy['lead_id'], "req" => json_encode($return_data),"product_id"=> $data_policy['product_id'], "type"=>"coi_retrigger_success"];
			
				$dataArray['tablename'] = 'logs_docs'; 
				$dataArray['data'] = $request_arr; 
				$this->Logs_m->insertLogs($dataArray);
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


