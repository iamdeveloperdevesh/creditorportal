<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use \Firebase\JWT\JWT;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

// session_start(); //we need to call PHP's session object to access it through CI
class Api2 extends CI_Controller 
{
	public $algoMethod;
    public $hashMethod;
    public $hash_key;
	public $encrypt_key;
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('apimodel','',TRUE);
		// Load these helper to create JWT tokens
		$this->load->helper(['core_helper','jwt','authorization_helper']);
		
        //$this->load->helper(['jwt', 'authorization']);
		
		ini_set( 'memory_limit', '25M' );
		ini_set('upload_max_filesize', '25M');  
		ini_set('post_max_size', '25M');  
		ini_set('max_input_time', 3600);  
		ini_set('max_execution_time', 3600);
		ini_set('memory_limit', '-1');
		allowCrossOrgin();
		
		$this->algoMethod = 'aes-128-ecb';
        $this->hashMethod = 'SHA512';
        $this->hash_key = 'razorpay';
        $this->encrypt_key = 'axisbank12345678';
	}
	
	function getallheaders_new() {
		return $response_headers = getallheaders_values();
	}
	
	//For generating random string
	private function generateRandomString($length = 8,$charset="") {
		if($charset == 'N'){
			$characters = '0123456789';
		}else{
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	private function verify_request($token)
	{
	    // Get all the headers
	    //$headers = $this->input->request_headers();
	    // Extract the token
	    //$token = $headers['Authorization'];
	    // Use try-catch
	    // JWT library throws exception if the token is not valid
	    try {
	        // Validate the token
	        // Successfull validation will return the decoded user data else returns false
	        $data = AUTHORIZATION::validateToken($token);
	        if ($data === false) {
	        	//$this->errorResponse['message'] = 'Unauthorized Access!';
				//$this->responseData($this->errorResponse);
				
				return json_encode(array("status_code" => "401", "Metadata" => array("Message" => "Unauthorized Access!" ), "Data" => NULL ));
				exit;
	        } else {
	            return $data;
	        }
	    } catch (Exception $e) {
	    	
	        // Token is invalid
	        // Send the unathorized access message
	        //$this->errorResponse['message'] = 'Unauthorized Access!';
			//$this->responseData($this->errorResponse);
			
			return json_encode(array("status_code" => "401", "Metadata" => array("Message" => "Unauthorized Access!" ), "Data" => NULL ));
			exit;
	    }
	}
	function getpolicypremiumflat($id,$suminsured){
		$ptype = $this->db->get_where("master_policy",array("master_policy_id"=> $id))->row()->premium_type;
		if($ptype == 1){
		$rate = $this->apimodel->getdata1("master_policy_premium","*"," master_policy_id = $id AND sum_insured = $suminsured");}else{
		$rate = $this->apimodel->getdata1("master_policy_premium","*"," master_policy_id = $id");	
		}
		if(!empty($rate)){
			if($ptype == 1){
			$r['amount'] = $rate[0]->premium_rate;}else{
			$r['amount'] = ($suminsured/1000)*$rate[0]->premium_rate;
			}
			if($rate[0]->is_taxable){
				$r['tax'] = $r['amount']*$this->config->item('tax')/100;
			}
		}else{
			$r = 0;
		}
		return $r;
	}
	function getpolicypremiumfamilyconstruct($policy,$sum_insured,$adult,$child){
		$ptype = $this->db->get_where("master_policy",array("master_policy_id"=> $policy))->row()->premium_type;
		if($ptype == 1){
		$rate = $this->apimodel->getdata1("master_policy_premium","*"," master_policy_id = $policy AND sum_insured = $sum_insured AND adult_count = $adult AND child_count = $child");}else{
		$rate = $this->apimodel->getdata1("master_policy_premium","*"," master_policy_id = $policy AND adult_count = $adult AND child_count = $child");	
		}
		if(!empty($rate)){
			if($ptype == 1){
			$r['amount'] = $rate[0]->premium_rate;}else{
			$r['amount'] = ($sum_insured/1000)*$rate[0]->premium_rate;	
			}
			if($rate[0]->is_taxable){
				$r['tax'] = $r['amount']*$this->config->item('tax')/100;
			}
		}else{
			$r = 0;
		}
		return $r;
	}
	function getpolicypremiumfamilyconstructage($proposal_details_id,$policy,$sum_insured,$adult,$child,$age){
		$ptype = $this->db->get_where("master_policy",array("master_policy_id"=> $policy))->row()->premium_type;
		if($ptype == 1){
		$rate = $this->apimodel->getdata1("master_policy_premium","*"," master_policy_id = $policy AND sum_insured = $sum_insured AND adult_count = $adult AND child_count = $child AND min_age >= $age AND max_age <= $age");}else{
		$rate = $this->apimodel->getdata1("master_policy_premium","*"," master_policy_id = $policy AND adult_count = $adult AND child_count = $child AND min_age >= $age AND max_age <= $age");	
		}
		if($ptype == 1){
			$pamount = $rate[0]->premium_rate;
		}else{
			$pamount = ($sum_insured/1000)*$rate[0]->premium_rate;
		}
		
		if(!empty($rate)){
			$trate = $this->apimodel->getdata1("proposal_policy_member","premium"," policy_id = $proposal_details_id AND lead_id = $lead_id ORDER BY premium DESC");
			if(!empty($trate) && $trate[0]->premium > $pamount){
				$r['amount'] = $trate[0]->premium;
				if($rate[0]->is_taxable){
					$r['tax'] = $trate[0]->premium*$this->config->item('tax')/100;
				}
			}else{
				$r['amount'] = $pamount;
				if($rate[0]->is_taxable){
					$r['tax'] = $pamount*$this->config->item('tax')/100;
				}
			}
		}else{
			$r = 0;
		}
		return $r;
	}
	function getpolicypremiummemberage($policy,$sum_insured,$age){
		$ptype = $this->db->get_where("master_policy",array("master_policy_id"=> $policy))->row()->premium_type;
		if($ptype == 1){
		$rate = $this->apimodel->getdata1("master_policy_premium","*"," master_policy_id = $id AND sum_insured = $sum_insured AND min_age >= $age AND max_age <= $age");}else{
		$rate = $this->apimodel->getdata1("master_policy_premium","*"," master_policy_id = $id AND min_age >= $age AND max_age <= $age");	
		}
		if(!empty($rate)){
			if($ptype == 1){
			$r['amount'] = $rate[0]->premium_rate;}else{
			$r['amount'] = ($sum_insured/1000)*$rate[0]->premium_rate;	
			}
			if($rate[0]->is_taxable){
				$r['tax'] = $trate[0]->premium*$this->config->item('tax')/100;
			}
		}else{
			$r = 0;
		}
		return $r;
	}
	function gettotalpremium($id,$sibasis,$lead_id,$policy_id){
		
		$rates = $this->apimodel->getdata1("proposal_policy_member","premium,tax"," proposal_policy_id = $id AND lead_id = $lead_id AND policy_id = $policy_id");
		$total['amount'] = 0;
		$total['tax'] = 0;
		if(!empty($rates)){
		if($sibasis == 1){
			foreach($rates as $rate){
				$total['amount'] = $total['amount'] + $rate->premium;
				$total['tax'] = $total['tax'] + $rate->tax;
			}
		}
		if($sibasis == 2){
			foreach($rates as $rate){
				$total['amount'] = $rate->premium;
				$total['tax'] = $rate->tax;
			}
		}
		if($sibasis == 3){  
			$count = count($rates);  
			$value = 0;
			$tax = 0;
			foreach($rates as $rate){
				if($rate->premium > $value)
				$value = $rate->premium;
				$tax = $rate->tax;
			}
			$total['amount'] = $value;
			$total['tax'] = $tax;
		}
		if($sibasis == 4){
			foreach($rates as $rate){
				$total['amount'] = $total['amount'] + $rate->premium;
				$total['tax'] = $total['tax'] + $rate->tax;
			}
		}
	}
		return $total;
		
	}
	// Previous code
	/* function gettotalpremium($id,$sibasis,$lead_id){
		
		$rates = $this->apimodel->getdata1("proposal_policy_member","premium,tax"," proposal_policy_id = $id AND lead_id = $lead_id");
		$total['amount'] = 0;
		$total['tax'] = 0;
		if(!empty($rates)){
		if($sibasis == 1){
			foreach($rates as $rate){
				$total['amount'] = $total['amount'] + $rate->premium;
				$total['tax'] = $total['tax'] + $rate->tax;
			}
		}
		if($sibasis == 2){
			foreach($rates as $rate){
				$total['amount'] = $total['amount'] + $rate->premium;
				$total['tax'] = $total['tax'] + $rate->tax;
			}
		}
		if($sibasis == 3){  
			$count = count($rates);  
			$value = 0;
			$tax = 0;
			foreach($rates as $rate){
				if($rate->premium > $value)
				$value = $rate->premium;
				$tax = $rate->tax;
			}
			$total = $count*$value;
			$total['amount'] = $count*$value;
			$total['tax'] = $count*$tax;
		}
		if($sibasis == 4){
			foreach($rates as $rate){
				$total['amount'] = $total['amount'] + $rate->premium;
				$total['tax'] = $total['tax'] + $rate->tax;
			}
		}
	}
		return $total;
		
	} */
	
	function checkmemberage($id,$member_type_id,$age){
		$memberage = $this->apimodel->getdata1("master_policy_family_construct","*"," master_policy_id = $id AND member_type_id = $member_type_id AND isactive = 1");
		
		if(count($memberage) > 0){
			$minage = (!empty($memberage[0]->member_min_age))?$memberage[0]->member_min_age:0;
			$maxage = (!empty($memberage[0]->member_max_age))?$memberage[0]->member_max_age:125;
			if($age >= $minage && $age <= $maxage){
				return 1;
			}else{
				return "Member age must be between $minage and $maxage";
			}
		}else{
			return 1;
		}
	}
	function addProposalMember(){
			$checkToken = $this->verify_request($_POST['utoken']);
			if(!empty($checkToken->username)){
			$member_type_id = (!empty($_POST['family_members_id'])) ? $_POST['family_members_id'] : '';
			$age = (!empty($_POST['age'])) ? $_POST['age'] : '';
			$sitypes = (!empty($_POST['sitypes'])) ? $_POST['sitypes'] : '';
			$policy_nos = (!empty($_POST['policy_nos'])) ? $_POST['policy_nos'] : '';
			$sibasis = (!empty($_POST['sibasis'])) ? $_POST['sibasis'] : '';
			$sum_insured = (!empty($_POST['sum_insured'])) ? $_POST['sum_insured'] : '';
			$i = 0;
			$member_details = array();
			$total_premium = array();
			$key = time();
			$member_details['member_unique_id'] = $key;
			$member_details['lead_id'] = $_POST['lead_id'];
			$member_details['trace_id'] = $_POST['trace_id'];
			
			$member_details['policy_member_age'] = $age;
			$member_details['relation_with_proposal'] = (!empty($_POST['family_members_id'])) ? $_POST['family_members_id'] : '';
			$adult = (!empty($_POST['adultcount'])) ? $_POST['adultcount'] : '0';
			$child = (!empty($_POST['childcount'])) ? $_POST['childcount'] : '0';
			$member_details['policy_member_salutation'] = (!empty($_POST['family_salutation'])) ? $_POST['family_salutation'] : '';
			$member_details['policy_member_gender'] = (!empty($_POST['family_gender'])) ? $_POST['family_gender'] : '';
			$member_details['policy_member_first_name'] = (!empty($_POST['first_name'])) ? $_POST['first_name'] : '';
			$member_details['policy_member_last_name'] = (!empty($_POST['last_name'])) ? $_POST['last_name'] : '';
			$member_details['policy_member_pan'] = (!empty($_POST['policy_member_pan'])) ? $_POST['policy_member_pan'] : '';
			$member_details['policy_member_dob'] = (!empty($_POST['family_date_birth'])) ? date('Y-m-d',strtotime($_POST['family_date_birth'])) : '';
			$proposal_details_id = $_POST['proposal_details_id'];
			foreach($policy_nos as $policy){
				$pdetails = $this->apimodel->checkproposalpolicy($member_details['lead_id'],$policy,$proposal_details_id);
				if(count($pdetails) > 0){
					$member_details['policy_id'] = $pdetails[0]->policy_id;
					if(empty($pdetails[0]->sum_insured)){
						$ipolicy = array();
						$ipolicy['sum_insured'] = $sum_insured;
						$ipolicy['adult_count'] = $_POST['adultcount'];
						$ipolicy['child_count'] = $_POST['childcount'];
						$this->apimodel->updateRecordarr('proposal_policy',$ipolicy,array('master_policy_id'=>$policy,'proposal_details_id'=>$proposal_details_id));
					}
				}else{
					$masteredetails = $this->apimodel->getPolicyDetails($policy);
					$ipolicy = array();
					$ipolicy['lead_id'] = $member_details['lead_id'];
					$ipolicy['trace_id'] = $member_details['trace_id'];
					$ipolicy['master_policy_id'] = $policy;
					$ipolicy['proposal_details_id'] = $policy;
					$ipolicy['policy_sub_type_id'] = $masteredetails[0]->policy_sub_type_id;
					$policysubtypename = $this->apimodel->getsubtypenamebyid($masteredetails[0]->policy_sub_type_id);
					$ipolicy['policy_sub_type_name'] = $policysubtypename;
					$ipolicy['insurer_id'] = $masteredetails[0]->insurer_id;
					$ipolicy['policy_number'] = $masteredetails[0]->policy_number;
					$ipolicy['policy_number'] = $masteredetails[0]->policy_number;
					$ipolicy['pdf_type'] = $masteredetails[0]->pdf_type;
					$ipolicy['is_combo'] = $masteredetails[0]->is_combo;
					$ipolicy['is_optional'] = $masteredetails[0]->is_optional;
					$ipolicy['sum_insured'] = $sum_insured;
					$ipolicy['adult_count'] = $_POST['adultcount'];
					$ipolicy['proposal_details_id'] = $proposal_details_id;
					$ipolicy['child_count'] = $_POST['childcount'];
					$insert_id = $this->apimodel->insertData('proposal_policy',$ipolicy);
					$member_details['policy_id'] = $insert_id;
				}
			}
			foreach($policy_nos as $policy){
				$ageresponse = $this->checkmemberage($policy,$member_type_id,$age);
				if($ageresponse != 1){
					echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => $ageresponse)));
					exit;
				}
				
			}
			foreach($policy_nos as $policy){
				if($sibasis[$i] == 1){
					$rate = $this->getpolicypremiumflat($policy,$sum_insured);
					if($rate != 0){
						$member_details['proposal_policy_id'] = $policy;
						$member_details['premium'] = $rate['amount'];
						if(!empty($rate['tax'])){
						$member_details['tax'] = $rate['tax'];}
						$this->apimodel->insertData('proposal_policy_member',$member_details);
						$total_premium[$policy] = $this->gettotalpremium($policy,$sibasis[$i],$member_details['lead_id'],$member_details['policy_id']);
						$this->apimodel->updateRecordarr('proposal_policy',array('premium_amount'=>$total_premium[$policy]['amount'],'tax_amount'=>$total_premium[$policy]['tax']),array('proposal_details_id'=>$proposal_details_id,'master_policy_id'=>$policy));
					}
				}else if($sibasis[$i] == 2){
					
					$rate = $this->getpolicypremiumfamilyconstruct($policy,$sum_insured,$adult,$child);
					
					if($rate != 0){
						$member_details['proposal_policy_id'] = $policy;
						$member_details['premium'] = $rate['amount'];
						if(!empty($rate['tax'])){
						$member_details['tax'] = $rate['tax'];}
						$this->apimodel->insertData('proposal_policy_member',$member_details);
						$total_premium[$policy] = $this->gettotalpremium($policy,$sibasis[$i],$member_details['lead_id'],$member_details['policy_id']);
						$this->apimodel->updateRecordarr('proposal_policy',array('premium_amount'=>$total_premium[$policy]['amount'],'tax_amount'=>$total_premium[$policy]['tax']),array('proposal_details_id'=>$proposal_details_id,'master_policy_id'=>$policy));
					
					}
				}else if($sibasis[$i] == 3){
					$rate = $this->getpolicypremiumfamilyconstructage($member_details['policy_id'],$policy,$sum_insured,$adult,$child,$age);
					if($rate != 0){
						$member_details['proposal_policy_id'] = $policy;
						$member_details['premium'] = $rate['amount'];
						if(!empty($rate['tax'])){
						$member_details['tax'] = $rate['tax'];}
						$this->apimodel->insertData('proposal_policy_member',$member_details);
						$total_premium[$policy] = $this->gettotalpremium($policy,$sibasis[$i],$member_details['lead_id'],$member_details['policy_id']);
						$this->apimodel->updateRecordarr('proposal_policy',array('premium_amount'=>$total_premium[$policy]['amount'],'tax_amount'=>$total_premium[$policy]['tax']),array('proposal_details_id'=>$proposal_details_id,'master_policy_id'=>$policy));
					
					}
				}else if($sibasis[$i] == 4){
					$rate = $this->getpolicypremiummemberage($policy,$sum_insured,$age);
					if($rate != 0){
						$member_details['proposal_policy_id'] = $policy;
						$member_details['premium'] = $rate['amount'];
						if(!empty($rate['tax'])){
						$member_details['tax'] = $rate['tax'];}
						$this->apimodel->insertData('proposal_policy_member',$member_details);
						$total_premium[$policy] = $this->gettotalpremium($policy,$sibasis[$i],$member_details['lead_id'],$member_details['policy_id']);
						$this->apimodel->updateRecordarr('proposal_policy',array('premium_amount'=>$total_premium[$policy]['amount'],'tax_amount'=>$total_premium[$policy]['tax']),array('proposal_details_id'=>$proposal_details_id,'master_policy_id'=>$policy));
					
					}
				}
				$i++;
			}
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => "Success"), "Data" => $total_premium, "Key"=>$key));
			exit;
			}else{
			echo $checkToken;
		}
	}
	
	function deletePolicyMember(){
		
		$policy_nos = (!empty($_POST['policy_nos'])) ? $_POST['policy_nos'] : '';
		$lead_id = (!empty($_POST['lead_id'])) ? $_POST['lead_id'] : '';
		$sibasis = (!empty($_POST['sibasis'])) ? $_POST['sibasis'] : '';
		$member_unique_id = (!empty($_POST['id'])) ? $_POST['id'] : '';
		$this->apimodel->delrecord('proposal_policy_member','member_unique_id',$member_unique_id);
		$i = 0;
		$total_premium = array();
		foreach($policy_nos as $policy){
				if($sibasis[$i] == 1){
					$total_premium[$policy] = $this->gettotalpremium($policy,$sibasis[$i],$lead_id);
					$this->apimodel->updateRecordarr('proposal_policy',array('premium_amount'=>$total_premium[$policy]['amount'],'tax_amount'=>$total_premium[$policy]['tax']),array('lead_id'=>$lead_id,'master_policy_id'=>$policy));
					
				}else if($sibasis[$i] == 2){
					$total_premium[$policy] = $this->gettotalpremium($policy,$sibasis[$i],$lead_id);
					$this->apimodel->updateRecordarr('proposal_policy',array('premium_amount'=>$total_premium[$policy]['amount'],'tax_amount'=>$total_premium[$policy]['tax']),array('lead_id'=>$lead_id,'master_policy_id'=>$policy));
					
				}else if($sibasis[$i] == 3){
					$total_premium[$policy] = $this->gettotalpremium($policy,$sibasis[$i],$lead_id);
					$this->apimodel->updateRecordarr('proposal_policy',array('premium_amount'=>$total_premium[$policy]['amount'],'tax_amount'=>$total_premium[$policy]['tax']),array('lead_id'=>$lead_id,'master_policy_id'=>$policy));
					
				}else if($sibasis[$i] == 4){
					$total_premium[$policy] = $this->gettotalpremium($policy,$sibasis[$i],$lead_id);
					$this->apimodel->updateRecordarr('proposal_policy',array('premium_amount'=>$total_premium[$policy]['amount'],'tax_amount'=>$total_premium[$policy]['tax']),array('lead_id'=>$lead_id,'master_policy_id'=>$policy));
					
				}
				$i++;
			}
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => "Success"), "Data" => $total_premium));
			exit;
	}
	
	function get_tiny_url($url)  { 
		$ch = curl_init();  
		$timeout = 5;  
		curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
		$data = curl_exec($ch);  
		curl_close($ch);  
		return $data;  
	}
	function proposalFinalSubmit(){
		$checkToken = $this->verify_request($_POST['utoken']);
		if(!empty($checkToken->username)){
			$data = array();
			$utoken = $this->input->post('utoken');
			$lead_id = $this->input->post('lead_id');
			$trace_id = $this->input->post('trace_id');
			$data['mode_of_payment'] = $this->input->post('mode_of_payment');
			$data['preffered_contact_date'] = $this->input->post('preffered_contact_date');
			$data['remark'] = $this->input->post('remark');
			$data['preffered_contact_time'] = $this->input->post('preffered_contact_time');
			if($data['mode_of_payment'] == "Cheque"){
				if (!file_exists(ABSOLUTE_DOC_ROOT.'assets/leaddocuments/'.$lead_id)) {
					mkdir(ABSOLUTE_DOC_ROOT.'assets/leaddocuments/'.$lead_id, 0777, true);
				}
				$file_ext = pathinfo($_FILES['testfile']['name'], PATHINFO_EXTENSION);
				$savename = "shani_12".'.'.$file_ext;
				$result = $this->do_upload($config,'testfile',$savename);
				$config = array();
				$config['upload_path']   = ABSOLUTE_DOC_ROOT.'assets/leaddocuments/'.$lead_id.'/'; 
				$config['allowed_types'] = '*'; 
				$config['max_size']      = 2048; 
				$enrollment_form = $_FILES['enrollment_form']['name'];
				$cheque_copy = $_FILES['cheque_copy']['name'];
				$itr = $_FILES['itr']['name'];
				$cam = $_FILES['cam']['name'];
				$medical = $_FILES['medical']['name'];
				if(!empty($cheque_copy)){
					$file_ext = pathinfo($_FILES['cheque_copy']['name'], PATHINFO_EXTENSION);
					$savename = 'Creditor-'.$lead_id.'-Cheque_Copy'.'.'.$file_ext;
					$res = $this->do_upload($config,'cheque_copy',$savename,$lead_id);
				}
				if(!empty($enrollment_form)){
					$file_ext = pathinfo($_FILES['enrollment_form']['name'], PATHINFO_EXTENSION);
					$savename = 'Creditor-'.$lead_id.'-Enrollment_form'.'.'.$file_ext;
					$res = $this->do_upload($config,'enrollment_form',$savename,$lead_id);
				}
				if(!empty($itr)){
					$file_ext = pathinfo($_FILES['itr']['name'], PATHINFO_EXTENSION);
					$savename = 'Creditor-'.$lead_id.'-ITR'.'.'.$file_ext;
					$res = $this->do_upload($config,'itr',$savename,$lead_id);
				}
				if(!empty($cam)){
					$file_ext = pathinfo($_FILES['cam']['name'], PATHINFO_EXTENSION);
					$savename = 'Creditor-'.$lead_id.'-CAM_report'.'.'.$file_ext;
					$res = $this->do_upload($config,'cam',$savename,$lead_id);
				}
				if(!empty($medical)){
					$file_ext = pathinfo($_FILES['medical']['name'], PATHINFO_EXTENSION);
					$savename = 'Creditor-'.$lead_id.'-Medical_report'.'.'.$file_ext;
					$res = $this->do_upload($config,'medical',$savename,$lead_id);
				}
				$data['bank_name'] = $this->input->post('bank_name');
				$data['bank_branch'] = $this->input->post('bank_branch');
				$data['bank_city'] = $this->input->post('bank_city');
				$data['ifsc_code'] = $this->input->post('ifsc_code');
				$data['account_number'] = $this->input->post('account_number');
				$data['cheque_number'] = $this->input->post('cheque_number');
				$data['cheque_date'] = date('Y-m-d',strtotime($this->input->post('cheque_date')));
				$data['status'] = "BO-Approval-Awaiting";
			}else if($data['mode_of_payment'] == "NEFT"){
				$data['status'] = "CO-Approval-Awaiting";
			}else if($data['mode_of_payment'] == "Online"){
				$id = rtrim(strtr(base64_encode("id=".$lead_id), '+/', '-_'), '=');
				$url = base_url()."policyproposal/customerpaymentform?text=$id";
				$actual_url  = base_url()."policyproposal/customerpaymentform?text=$lead_id";
				$short_url = $this->get_tiny_url($url);
				$shorturl_data = array();
				$shorturl_data['long_url'] = $actual_url;
				$shorturl_data['short_code'] = $short_url;
				$shorturl_data['proposal_id'] = $trace_id;
				$shorturl_data['lead_id'] = $lead_id;
				$result = $this->apimodel->insertData('short_urls', $shorturl_data, 1);
				$data['status'] = "Customer-Payment-Awaiting";
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Invalid Payment Mode Selected.'  ), "Data" => NULL ));
				exit;
			}
			if(!empty($data)){
				$result1 = $this->apimodel->updateRecord('proposal_details', $data, "lead_id='".$lead_id."' ");
			}
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record Updated Successfully'  )));
			exit;
		}else{
			echo $checkToken;
		}
	}
	public function test_upload(){
		$config = array();
		$config['upload_path']   = ABSOLUTE_DOC_ROOT.'assets/leaddocuments/'; 
		$config['allowed_types'] = '*'; 
		$config['max_size']      = 2048; 
		//$filename = $this->input->post('testfile');
		//echo json_encode($_FILES);exit;
		$file_ext = pathinfo($_FILES['testfile']['name'], PATHINFO_EXTENSION);
		$savename = "shani_12".'.'.$file_ext;
		$result = $this->do_upload($config,'testfile',$savename);
		echo json_encode($result);
	}
	public function do_upload($config,$filename,$savename,$lead_id="") {
		 
		 $config['file_name'] = $savename;
		 
         $this->load->library('upload', $config);
			
         if ( ! $this->upload->do_upload($filename)) {
            $error = array('error' => $this->upload->display_errors()); 
            return $error;
         }
			
         else { 
			if($lead_id!=""){
			$img = file_get_contents($filename); 
			$fields = '{"Identifier":"ByteArray","UploadRequest":[{"CategoryID":"1003","DataClassParam":[{"Value":"$lead_id","DocSearchParamId":"22"}],"Description":"","ReferenceID":"3100","FileName":"$savename","DocumentID":"2224","ByteArray":"$filename","SharedPath":""}],"SourceSystemName":"Axis"}';
			$postField = json_decode($fields,true);
			$saveField = json_decode($fields,true);
			$postField['UploadRequest'][0]['ByteArray'] = base64_encode($img);
			$postField['UploadRequest'][0]['FileName'] = $savename;
			$this->docServiceCal($postField, $saveField);
            $data = array('upload_data' => $this->upload->data()); 
            return $data;}else{
				return true;
			}
         } 
      } 
	  
	  function docServiceCal($postField, $saveField) {
		
		$this->db->insert("logs_docs", [
			"req" => json_encode($saveField),
			"lead_id" => $saveField['UploadRequest'][0]['DataClassParam'][0]['Value'],
			"type" => "OmniDocs"
		]);
		
		$id = $this->db->insert_id();	
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://bizpre.adityabirlahealth.com/ABHICL_OmniDocs/Service1.svc/uploadRequest",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => json_encode($postField),
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: application/json",
			"password: esb@axis",
			"postman-token: a3f0ed2e-f9cc-f767-09ae-4c594e38d5f2",
			"username: esb_axis"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			$this->db->where([
				"id" => $id
			])->update("logs_docs", [
				"res" => json_encode($err),
				"type" => "OmniDocs"
			]);
			// Monolog::saveLog("omniDocResError", "I", $err);
		  // echo "cURL Error #:" . $err;
		  
		} else {
			$this->db->where([
				"id" => $id
			])->update("logs_docs", [
				"res" => json_encode($response),
				"type" => "OmniDocs"
			]);
			// Monolog::saveLog("omniDocRes", "I", $response);
		  // echo $response;
		}
	}
	
	
	function getLeadDetails(){
	    $data = array();
		$lead_id = $this->input->post('lead_id');
	
		$data['customer_details'] = $this->apimodel->getLeadDetails($lead_id);
		$data['plan_details'] = $this->apimodel->getProductDetailsAll($data['customer_details'][0]->plan_id);
		$data['payment_modes'] = $this->apimodel->getPlanPayments($data['customer_details'][0]->plan_id);
		$data['proposal_details'] = $this->apimodel->getPlanProposal($data['customer_details'][0]->plan_id,$lead_id);
		$data['family_members'] = $this->apimodel->getMembers();
		$data['policy_declaration'] = $this->apimodel->getPolicyDeclaration($data['customer_details'][0]->plan_id);
		$data['assignment_declaration'] = $this->apimodel->getAssignmentDeclaration($data['customer_details'][0]->plan_id);
		foreach($data['plan_details'] as $plandetail){
		    $plandetail->family_construct = $this->apimodel->getPolicyFamilyConstruct($plandetail->policy_id);
		}
		foreach($data['plan_details'] as $plandetail){
		    $plandetail->policy_premium = $this->apimodel->getPolicyPremium($plandetail->policy_id);
		}
		$i = 0;
		foreach($data['proposal_details'] as $proposaldetail){
			$proposal_policy_details = $this->apimodel->getProposalPolicy1($proposaldetail->proposal_details_id);
			$proposaldetail->proposal_policy_details = $proposal_policy_details;
			foreach($proposaldetail->proposal_policy_details as $policydetail){
				$policydetail->policy_members = $this->apimodel->getProposalPolicyMember($policydetail->proposal_policy_id);
			}
		}
		
		echo json_encode($data);
	 
	}
	function getLeadDetailsCustomer(){
	    $data = array();
		$lead_id = $this->input->post('lead_id');
		$otp = $this->input->post('otp');
		$data['customer_details'] = $this->apimodel->getLeadDetails($lead_id);
		$data['plan_details'] = $this->apimodel->getProductDetailsAll($data['customer_details'][0]->plan_id);
		$data['payment_modes'] = $this->apimodel->getPlanPayments($data['customer_details'][0]->plan_id);
		$data['proposal_details'] = $this->apimodel->getPlanProposal($data['customer_details'][0]->plan_id,$lead_id);
		$data['family_members'] = $this->apimodel->getMembers();
		foreach($data['plan_details'] as $plandetail){
		    $plandetail->family_construct = $this->apimodel->getPolicyFamilyConstruct($plandetail->policy_id);
		}
		foreach($data['plan_details'] as $plandetail){
		    $plandetail->policy_premium = $this->apimodel->getPolicyPremium($plandetail->policy_id);
		}
		foreach($data['proposal_details'] as $proposaldetail){
			$proposal_policy_details = $this->apimodel->getProposalPolicy1($proposaldetail->proposal_details_id);
			$proposaldetail->proposal_policy_details = $proposal_policy_details;
			foreach($proposal_policy_details as $policydetail){
				$policydetail->policy_members = $this->apimodel->getProposalPolicyMember($policydetail->proposal_policy_id);
			}
		}
		
		echo json_encode($data);
	  
	}
	function sendcustomerpaymentformotp(){
		$lead_id = $_POST['lead_id'];
		$isactive = $this->db->get_where('short_urls',array('lead_id'=>$lead_id))->row();
		if($isactive->status == 1){
			$this->db->where('lead_id',$lead_id);
			$this->db->insert('short_urls', array('otp'=>'1234'));
			$count = $isactive->hits + 1;
			$this->db->where('lead_id',$lead_id);
			$this->db->update('short_urls',array('hits'=>$count));
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success')));
		}else{
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Link Expired')));
		}
	}
	function checkLeadotpCustomer(){
		$lead_id = $_POST['lead_id'];
		$otp = $_POST['otp'];
		$isactive = $this->db->get_where('short_urls',array('lead_id'=>$lead_id))->row();
		if($isactive->status == 1 && $isactive->otp == $otp){
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'), "Data" => array("lead_id"=>$lead_id,"otp"=>$otp)));
		}else{
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Incorrect OTP')));
		}
	}
	function getPolicyUpdateDetails(){
		$checkToken = $this->verify_request($_POST['utoken']);
		if(!empty($checkToken->username)){
	    $data = array();
	    $utoken = $this->input->post('utoken');
	    $id = $this->input->post('id');
	    $data['plan_id'] = $id;
		$policy_id = $this->input->post('policy_id');
		if(!empty($policy_id)){
		    $data['family_construct'] = $this->apimodel->getPolicyFamilyConstruct($policy_id);
    		$data['policy_premium'] = $this->apimodel->getPolicyPremium($policy_id);
    		$data['si_type'] = $this->apimodel->getPolicySiType($policy_id);
    		$data['premium_basis'] = $this->apimodel->getPolicyPremiumBasis($policy_id);
    		$data['policydetails'] = $this->apimodel->getProductDetails($id,$policy_id);
		}
		$data['details'] = $this->apimodel->getProductDetails($id);
		$data['sitypes'] = $this->apimodel->getSiType();
		$data['insurers'] = $this->apimodel->getInsurer();
		$data['sipremiumbasis'] = $this->apimodel->getSiPremiumBasis();
		$data['members'] = $this->apimodel->getMembers();
		
		echo json_encode($data);
		}else{
			echo $checkToken;
		}
	}
	
	function PolicySubTypeListing(){
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
		$get_result = $this->apimodel->getPolicySubTypeList($_POST);
		//echo "<pre>";print_r($get_result);exit;
		echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
		exit;
		}else{
			echo $checkToken;
		}
	}
	function getproposalpolicybylead(){
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
		$get_result = $this->apimodel->getproposalpolicybylead($_POST['leads']);
		//echo "<pre>";print_r($get_result);exit;
		echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
		exit;
		}else{
			echo $checkToken;
		}
	}
	function FamilyConstructListing(){
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
		$get_result = $this->apimodel->getFamilyConstructList($_POST);
		//echo "<pre>";print_r($get_result);exit;
		echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
		exit;
		}else{
			echo $checkToken;
		}
	}
	function getFamilyConstructFormData(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getFamilyConstructFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
	}
	function ProductsListing(){
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
		$get_result = $this->apimodel->getProductsList($_POST);
		//echo "<pre>";print_r($get_result);exit;
		echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
		exit;
		}else{
			echo $checkToken;
		}
	}
	
	function getPolicySubTypeFormData(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getPolicySubTypeFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
	}
	
	function getPolicyTypes(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getPolicyType();
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
	}
	
	function addEditPolicySubType(){
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$data = array();
			$data['policy_sub_type_name'] = (!empty($_POST['policy_sub_type_name'])) ? $_POST['policy_sub_type_name'] : '';
			$data['policy_type_id'] = (!empty($_POST['policy_type_id'])) ? $_POST['policy_type_id'] : '';
			
			$data['isactive'] = (!empty($_POST['isactive'])) ? $_POST['isactive'] : 0;
			
			if(!empty($_POST['policy_sub_type_id'])){
				$result = $this->apimodel->updateRecord('master_policy_sub_type', $data, "policy_sub_type_id='".$_POST['policy_sub_type_id']."' ");
			}else{
				$result = $this->apimodel->insertData('master_policy_sub_type', $data, 1);
			}
			
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
			
		}else{
			echo $checkToken;
		}
		
	}
	function addEditFamilyConstruct(){
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$data = array();
			$data['member_type'] = (!empty($_POST['member_type'])) ? $_POST['member_type'] : '';
			
			if(!empty($_POST['id'])){
				$result = $this->apimodel->updateRecord('family_construct', $data, "id='".$_POST['id']."' ");
			}else{
				$result = $this->apimodel->insertData('family_construct', $data, 1);
			}
			
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
			
		}else{
			echo $checkToken;
		}
		
	}
	//Delete Insurer
	function delPolicySubType(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('master_policy_sub_type', $data, "policy_sub_type_id='".$_POST['id']."' ");
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
	
	}
	function delFamilyConstruct(){
		//echo "<pre>post";print_r($_POST);exit;
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('family_construct', $data, "id='".$_POST['id']."' ");
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
	
	}
	
	function insurerListing(){
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
		$get_result = $this->apimodel->getInsurerList($_POST);
		//echo "<pre>";print_r($get_result);exit;
		echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
		exit;
		}else{
			echo $checkToken;
		}
	}
	function getInsurerFormData(){
		    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getInsurerFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	function addEditInsurer(){
		    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
	
			$data = array();
			$data['insurer_name'] = (!empty($_POST['insurer_name'])) ? $_POST['insurer_name'] : '';
			$data['insurer_code'] = (!empty($_POST['insurer_code'])) ? $_POST['insurer_code'] : '';
			
			$data['isactive'] = (!empty($_POST['isactive'])) ? $_POST['isactive'] : 0;
			
			if(!empty($_POST['insurer_id'])){
				$result = $this->apimodel->updateRecord('master_insurer', $data, "insurer_id='".$_POST['insurer_id']."' ");
			}else{
				$result = $this->apimodel->insertData('master_insurer', $data, 1);
			}
			
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//Delete Insurer
	function delInsurer(){
		    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('master_insurer', $data, "insurer_id='".$_POST['id']."' ");
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
	
	}
	
	function suminsuredListing(){
		$checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
		$get_result = $this->apimodel->getSuminsuredList($_POST);
		//echo "<pre>";print_r($get_result);exit;
		echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
		exit;
		}else{
			echo $checkToken;
		}
	}
	function getSuminsuredFormData(){
		    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$get_result = $this->apimodel->getSuminsuredFormData($_POST['id']);
			//echo "<pre>";print_r($get_result);exit;
			if(!empty($get_result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Success'  ), "Data" => $get_result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	function addEditSuminsured(){
		    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
	
			$data = array();
			$data['suminsured_type'] = (!empty($_POST['suminsured_type'])) ? $_POST['suminsured_type'] : '';
			$data['isactive'] = (!empty($_POST['isactive'])) ? $_POST['isactive'] : 0;
			
			if(!empty($_POST['suminsured_type_id'])){
				$result = $this->apimodel->updateRecord('master_suminsured_type', $data, "suminsured_type_id='".$_POST['suminsured_type_id']."' ");
			}else{
				$result = $this->apimodel->insertData('master_suminsured_type', $data, 1);
			}
			
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
		
	}
	
	//Delete Insurer
	function delSuminsured(){
		    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
			$data = array();
			$data['isactive'] = 0;
			$result = $this->apimodel->updateRecord('master_suminsured_type', $data, "suminsured_type_id='".$_POST['id']."' ");
			if(!empty($result)){
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created/updated successfully.'  ), "Data" => $result ));
				exit;
			}else{
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
				exit;
			}
		}else{
			echo $checkToken;
		}
	
	}
	
	function addMasterPlan(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
	    $data = array();
		$id = $this->input->post('id');
		$data['creditor_id'] = $this->input->post('creditor_id');
		$data['plan_name'] = $this->input->post('plan_name');
		$data['policy_type_id'] = $this->input->post('policy_type_id');
		$data['isactive'] = intval ($this->input->post('status'));
		if(!empty($id)){
		    $where = array('plan_id'=>$id);
		    $data = $this->apimodel->updateRecordarr('master_plan',$data,$where);
		    return 1;
		}else{
		    $this->apimodel->insertData('master_plan',$data);
		    return 1;
		}
		}else{
			echo $checkToken;
		}
	}
	function addMasterPolicy(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
	    $data = array();
		$id = $this->input->post('id');
		$data['plan_id'] = $this->input->post('plan_id');
		$data['policy_sub_type_id'] = $this->input->post('policy_sub_type_id');
		$data['insurer_id'] = $this->input->post('insurer_id');
		$data['policy_number'] = $this->input->post('policy_number');
		$data['policy_start_date'] = date('Y-m-d', strtotime($this->input->post('policy_start_date')));
		$data['policy_end_date'] = date('Y-m-d', strtotime($this->input->post('policy_end_date')));
		$data['pdf_type'] = $this->input->post('pdf_type');
		$data['is_optional'] = $this->input->post('is_optional');
		$data['max_member_count'] = $this->input->post('max_member_count');
		$data['policy_type_id'] = $this->input->post('policy_type_id');
		$data['isactive'] = intval ($this->input->post('status'));
		if(!empty($id)){
		    $where = array('policy_id'=>$id);
		    $data = $this->apimodel->updateRecordarr('master_policy',$data,$where);
		    return 1;
		}else{
		    $this->apimodel->insertData('master_policy',$data);
		    return 1;
		}
		}else{
			echo $checkToken;
		}
	}
    function addMasterPolicyType(){
        $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
	    $data = array();
		$id = $this->input->post('id');
		$data['policy_type_name'] = $this->input->post('policy_type_name');
		$data['isactive'] = intval ($this->input->post('status'));
		if(!empty($id)){
		    $where = array('policy_type_id'=>$id);
		    $data = $this->apimodel->updateRecordarr('master_policy_type',$data,$where);
		    return 1;
		}else{
		    $this->apimodel->insertData('master_policy_type',$data);
		    return 1;
		}
		}else{
			echo $checkToken;
		}
	}
	function addMasterPolicySubType(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
	    $data = array();
		$id = $this->input->post('id');
		$data['policy_sub_type_name'] = $this->input->post('policy_sub_type_name');
		$data['policy_type_id'] = $this->input->post('policy_type_id');
		$data['isactive'] = intval ($this->input->post('status'));
		if(!empty($id)){
		    $where = array('policy_sub_type_id'=>$id);
		    $data = $this->apimodel->updateRecordarr('master_policy_sub_type',$data,$where);
		    return 1;
		}else{
		    $this->apimodel->insertData('master_policy_sub_type',$data);
		    return 1;
		}
		}else{
			echo $checkToken;
		}
	}
	function addFamilyConstruct(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
	    $data = array();
		$id = $this->input->post('id');
		$data['member_type'] = $this->input->post('member_type');
		if(!empty($id)){
		    $where = array('id'=>$id);
		    $data = $this->apimodel->updateRecordarr('family_construct',$data,$where);
		    return 1;
		}else{
		    $this->apimodel->insertData('family_construct',$data);
		    return 1;
		}
		}else{
			echo $checkToken;
		}
	}
	function addMasterPolicyFamilyConstruct(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
	    $data = array();
		$id = $this->input->post('id');
		$data['master_policy_id'] = $this->input->post('master_policy_id');
		$data['member_type_id'] = $this->input->post('member_type_id');
		$data['member_min_age'] = $this->input->post('member_min_age');
		$data['member_max_age'] = $this->input->post('member_max_age');
		$data['isactive'] = intval ($this->input->post('status'));
		if(!empty($id)){
		    $where = array('family_construct_id'=>$id);
		    $data = $this->apimodel->updateRecordarr('master_policy_family_construct',$data,$where);
		    return 1;
		}else{
		    $this->apimodel->insertData('master_policy_family_construct',$data);
		    return 1;
		}
		}else{
			echo $checkToken;
		}
	}
	function addSiPremiumBasis(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
	    $data = array();
		$id = $this->input->post('id');
		$data['si_premium_basis'] = $this->input->post('si_premium_basis');
		$data['isactive'] = intval ($this->input->post('status'));
		if(!empty($id)){
		    $where = array('si_premium_basis_id'=>$id);
		    $data = $this->apimodel->updateRecordarr('master_si_premium_basis',$data,$where);
		    return 1;
		}else{
		    $this->apimodel->insertData('master_si_premium_basis',$data);
		    return 1;
		}
		}else{
			echo $checkToken;
		}
	}
	function uploadexcel(){
	    $data = array();
	    $records = json_decode($this->input->post('exceldata'));
	    $this->apimodel->insertBatchData('master_policy_premium',$records);
	}
	function uploadcoexcel(){
	    $data = array();
		$this->load->library('excel');
		
           $path = $_POST['path'];
           $object = PHPExcel_IOFactory::load($path);
           foreach($object->getWorksheetIterator() as $worksheet)
           {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
           
                for($row=2; $row<=$highestRow; $row++)
                {
					
					 $proposal_policy_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
					 $lead_id = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					 $hb_receipt_number = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
					 $reference_no = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
					 $amount = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
					 $payment_date = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
					 $status = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
					 $remark = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
					 if(!empty($proposal_policy_id)){
						 $policy_details_id = $this->db->get_where('proposal_policy',array('proposal_policy_id'=>$proposal_policy_id))->row()->proposal_details_id;
						 $this->db->where('proposal_details_id',$policy_details_id);
						 $this->db->update('proposal_details',array('transaction_date'=>$payment_date,'hb_receipt_number'=>$hb_receipt_number,'payment_remark'=>$remark,'transaction_number'=>$reference_no));
					}
				}
		   }
	}
	function addSuminsuredType(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
	    $data = array();
		$id = $this->input->post('id');
		$data['suminsured_type'] = $this->input->post('suminsured_type');
		$data['isactive'] = intval ($this->input->post('status'));
		if(!empty($id)){
		    $where = array('suminsured_type_id'=>$id);
		    $data = $this->apimodel->updateRecordarr('master_suminsured_type',$data,$where);
		    return 1;
		}else{
		    $this->apimodel->insertData('master_suminsured_type',$data);
		    return 1;
		}
		}else{
			echo $checkToken;
		}
	}
	function addNewProduct(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
	    $data = array();
	    $utoken = $this->input->post('utoken');
		$data['plan_name'] = $this->input->post('plan_name');
		$data['creditor_id'] = $this->input->post('creditor_id');
		$data['policy_type_id'] = $this->input->post('policy_type_id');
		$policy_sub_type_ids = explode(',',$this->input->post('policy_sub_type_id'));
		$payment_modes = explode(',',$this->input->post('payment_modes'));
		$insert_id = $this->apimodel->insertData('master_plan', $data, 1);
		if(!empty($insert_id)){
		    $data2 = array();
		    foreach($policy_sub_type_ids as $subtype){
		        $data2['isactive'] = 1;
		        $data2['plan_id'] = $insert_id;
		        $data2['policy_sub_type_id'] = $subtype;
		        $this->apimodel->insertData('master_policy', $data2, 1);
		    }
		    
		    foreach($payment_modes as $modes){
		        $data3 = array();
		        $data3['master_plan_id'] = $insert_id;
		        $data3['payment_mode_id'] = $modes;
		        $this->apimodel->insertData('plan_payment_mode',$data3);
		    }
		    
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created successfully.'  ), "Data" => $insert_id ));
			exit;
		}else{
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
			exit;
		}
		}else{
			echo $checkToken;
		}
		
	}
	function UpdateProduct(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
	    $data = array();
	    $utoken = $this->input->post('utoken');
	    $plan_id = $this->input->post('plan_id');
		$data['plan_name'] = $this->input->post('plan_name');
		$data['creditor_id'] = $this->input->post('creditor_id');
		$data['policy_type_id'] = $this->input->post('policy_type_id');
		$policy_sub_type_ids = explode(',',$this->input->post('policy_sub_type_id'));
		$payment_modes = explode(',',$this->input->post('payment_modes'));
		$this->apimodel->updateRecordarr('master_plan', $data, array('plan_id'=>$plan_id));
		
	    $data2 = array();
	    $allsubtype = $this->apimodel->getpolicysubtypeofplan($plan_id);
	    $allsubtypes = array();
	    foreach($allsubtype as $subtype){
	        array_push($allsubtypes,$subtype->policy_sub_type_id);
	    }
	    foreach($policy_sub_type_ids as $subtype){
	        if(!in_array($subtype,$allsubtypes)){
	        $data2['isactive'] = 0;
	        $data2['plan_id'] = $plan_id;
	        $data2['policy_sub_type_id'] = $subtype;
	        $this->apimodel->insertData('master_policy', $data2);}
	    }
	    foreach($allsubtypes as $subtype){
	        if(!in_array($subtype,$policy_sub_type_ids)){
	            $this->apimodel->deletemasterpolicy($subtype,$plan_id);
	        }
	    }
	    $allpayment = $this->apimodel->getpaymentofplan($plan_id);
	    $allpayments = array();
	    foreach($allpayment as $subtype){
	        array_push($allpayments,$subtype->payment_mode_id);
	    }
	    $this->apimodel->deletepolicypayment($plan_id);
	    foreach($payment_modes as $modes){
	        if(!in_array($modes,$allpayments)){
	        $data3 = array();
	        $data3['master_plan_id'] = $plan_id;
	        $data3['payment_mode_id'] = $modes;
	        $data3['isactive'] = 1;
	        $this->apimodel->insertData('plan_payment_mode',$data3);}else{
	            $this->apimodel->updatepolicypayment($plan_id, $modes);
	        }
	    }
	    
		echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record updated successfully.'  ), "Data" => $plan_id ));
		exit;
		}else{
			echo $checkToken;
		}
		
	}
	function addNewPolicy(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
	    $data = array();
	    $utoken = $this->input->post('utoken');
	    $plan_id = $this->input->post('plan_id');
	    $policy_sub_type_id = $this->input->post('policy_sub_type_id');
	    $data['policy_number'] = $this->input->post('policy_number');
	    $data['is_optional'] = intval($this->input->post('is_optional'));
	    $data['is_combo'] = intval($this->input->post('is_combo'));
		$data['premium_type'] = $this->input->post('premium_type');
	    $data['isactive'] = 1;
		$data['pdf_type'] = $this->input->post('pdf_type');
	    $data['insurer_id'] = $this->input->post('insurer_id');
	    $data['policy_start_date'] = date('Y-m-d', strtotime($this->input->post('policy_start_date')));
		$data['policy_end_date'] = date('Y-m-d', strtotime($this->input->post('policy_end_date')));
	    $data['max_member_count'] = $this->input->post('max_member_count');
	    $sitype = $this->input->post('sitype');
	    $sibasis = $this->input->post('sibasis');
	    if($sibasis != 1){
	        $exceldata = json_decode($this->input->post('exceldata'));
	    }else{
	        $sum_insured_opt = explode(',',$this->input->post('sum_insured_opt'));
    	    $premium_opt = explode(',',$this->input->post('premium_opt'));
    	    $tax_opt = explode(',',$this->input->post('tax_opt'));
	    }
	    $members = explode(',',$this->input->post('members'));
	    $minages = explode(',',$this->input->post('minage'));
	    $maxages = explode(',',$this->input->post('maxage'));
		
		$result = $this->apimodel->updateRecordarr('master_policy',$data,array('policy_id'=>$policy_sub_type_id));
		
		if($result){
		    $i=0;$data2=array();
    		for($i = 0;$i < count($members);$i++){
    		    $data2[$i]['master_policy_id']=$policy_sub_type_id;
    		    $data2[$i]['member_type_id']=$members[$i];
    		    $data2[$i]['member_min_age']=$minages[$i];
    		    $data2[$i]['member_max_age']=$maxages[$i];
    		}
    		$this->apimodel->insertBatchData('master_policy_family_construct', $data2);
    		$data3 = array('master_policy_id'=>$policy_sub_type_id,'si_premium_basis_id'=>$sibasis);
    		$this->apimodel->insertData('master_policy_premium_basis_mapping', $data3);
    		$data4 = array('master_policy_id'=>$policy_sub_type_id,'suminsured_type_id'=>$sitype);
    		$this->apimodel->insertData('master_policy_si_type_mapping', $data4);
    		if($sibasis != 1){
    		    $this->apimodel->insertBatchData('master_policy_premium', $exceldata);
    		}else{
    		$j=0;$data5=array();
    		for($j = 0;$j < count($sum_insured_opt);$j++){
    		    $data5[$j]['master_policy_id']=$policy_sub_type_id;
    		    $data5[$j]['sum_insured']=$sum_insured_opt[$j];
    		    $data5[$j]['premium_rate']=$premium_opt[$j];
    		    $data5[$j]['is_taxable']=$tax_opt[$j];
				$data5[$j]['is_absolute']=$data['premium_type'];
    		}
    		$this->apimodel->insertBatchData('master_policy_premium', $data5);}
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created successfully.'  ), "Data" => $plan_id ));
			exit;
		}else{
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
			exit;
		}
		}else{
			echo $checkToken;
		}
		
	}
	function updateNewPolicy(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
	    $data = array();
	    $utoken = $this->input->post('utoken');
	    $plan_id = $this->input->post('plan_id');
	    $policy_sub_type_id = $this->input->post('policy_sub_type_id');
	    $data['policy_number'] = $this->input->post('policy_number');
	    $data['is_optional'] = intval($this->input->post('is_optional'));
		$data['premium_type'] = $this->input->post('premium_type');
	    $data['is_combo'] = intval($this->input->post('is_combo'));
	    $data['isactive'] = 1;
		$data['pdf_type'] = $this->input->post('pdf_type');
	    $data['insurer_id'] = $this->input->post('insurer_id');
	    $data['policy_start_date'] = date('Y-m-d', strtotime($this->input->post('policy_start_date')));
		$data['policy_end_date'] = date('Y-m-d', strtotime($this->input->post('policy_end_date')));
	    $data['max_member_count'] = $this->input->post('max_member_count');
	    $sitype = $this->input->post('sitype');
	    $sibasis = $this->input->post('sibasis');
	    if($sibasis != 1){
	        $exceldata = json_decode($this->input->post('exceldata'));
	    }else{
	        $sum_insured_opt = explode(',',$this->input->post('sum_insured_opt'));
    	    $premium_opt = explode(',',$this->input->post('premium_opt'));
    	    $tax_opt = explode(',',$this->input->post('tax_opt'));
	    }
	    $members = explode(',',$this->input->post('members'));
	    $minages = explode(',',$this->input->post('minage'));
	    $maxages = explode(',',$this->input->post('maxage'));
		
		$this->apimodel->updateRecordarr('master_policy',$data,array('policy_id'=>$policy_sub_type_id));
		 $i=0;$data2=array();
    		for($i = 0;$i < count($members);$i++){
    		    $data2[$i]['master_policy_id']=$policy_sub_type_id;
    		    $data2[$i]['member_type_id']=$members[$i];
    		    $data2[$i]['member_min_age']=$minages[$i];
    		    $data2[$i]['member_max_age']=$maxages[$i];
    		}
    		$this->apimodel->inactivateRecord('master_policy_family_construct', array('master_policy_id'=>$policy_sub_type_id));
    		$this->apimodel->insertBatchData('master_policy_family_construct', $data2);
    		$this->apimodel->inactivateRecord('master_policy_premium_basis_mapping', array('master_policy_id'=>$policy_sub_type_id));
    		$data3 = array('master_policy_id'=>$policy_sub_type_id,'si_premium_basis_id'=>$sibasis);
    		$this->apimodel->insertData('master_policy_premium_basis_mapping', $data3);
    		$this->apimodel->inactivateRecord('master_policy_si_type_mapping', array('master_policy_id'=>$policy_sub_type_id));
    		$data4 = array('master_policy_id'=>$policy_sub_type_id,'suminsured_type_id'=>$sitype);
    		$this->apimodel->insertData('master_policy_si_type_mapping', $data4);
    		
    		if($sibasis != 1){
    		    if(count($exceldata) > 0 && !empty($exceldata)){
    		    $this->apimodel->inactivateRecord('master_policy_premium', array('master_policy_id'=>$policy_sub_type_id));
    		    $this->apimodel->insertBatchData('master_policy_premium', $exceldata);}
    		}else{
    		$this->apimodel->inactivateRecord('master_policy_premium', array('master_policy_id'=>$policy_sub_type_id));
    		$j=0;$data5=array();
    		for($j = 0;$j < count($sum_insured_opt);$j++){
    		    $data5[$j]['master_policy_id']=$policy_sub_type_id;
    		    $data5[$j]['sum_insured']=$sum_insured_opt[$j];
    		    $data5[$j]['premium_rate']=$premium_opt[$j];
    		    $data5[$j]['is_taxable']=$tax_opt[$j];
				$data5[$j]['is_absolute']=$data['premium_type'];
    		}
    		$this->apimodel->insertBatchData('master_policy_premium', $data5);}
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created successfully.'  ), "Data" => $plan_id ));
			exit;
		
		}else{
			echo $checkToken;
		}
		
	}
	function UpdatePolicy(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
	    $data = array();
	    $utoken = $this->input->post('utoken');
	    $plan_id = $this->input->post('plan_id');
	    $policy_sub_type_id = $this->input->post('policy_sub_type_id');
	    $data['policy_number'] = $this->input->post('policy_number');
	    $data['is_optional'] = intval($this->input->post('is_optional'));
	    $data['is_combo'] = intval($this->input->post('is_combo'));
	    $data['isactive'] = 1;
		$data['pdf_type'] = $this->input->post('pdf_type');
	    $data['insurer_id'] = $this->input->post('insurer_id');
	    $data['policy_start_date'] = $this->input->post('policyStartDate');
	    $data['policy_end_date'] = $this->input->post('policyEndDate');
	    $data['max_member_count'] = $this->input->post('max_member_count');
	    $sitype = $this->input->post('sitype');
	    $sibasis = $this->input->post('sibasis');
	    if($sibasis != 1){
	        $exceldata = json_decode($this->input->post('exceldata'));
	    }else{
	        $sum_insured_opt = explode(',',$this->input->post('sum_insured_opt'));
    	    $premium_opt = explode(',',$this->input->post('premium_opt'));
    	    $tax_opt = explode(',',$this->input->post('tax_opt'));
	    }
	    $members = explode(',',$this->input->post('members'));
	    $minages = explode(',',$this->input->post('minage'));
	    $maxages = explode(',',$this->input->post('maxage'));
		
		$result = $this->apimodel->updateRecordarr('master_policy',$data,array('policy_id'=>$policy_sub_type_id));
		
		if($result){
		    $i=0;$data2=array();
    		for($i = 0;$i < count($members);$i++){
    		    $data2[$i]['master_policy_id']=$policy_sub_type_id;
    		    $data2[$i]['member_type_id']=$members[$i];
    		    $data2[$i]['member_min_age']=$minages[$i];
    		    $data2[$i]['member_max_age']=$maxages[$i];
    		}
    		$this->apimodel->deletempfc($policy_sub_type_id);
    		$this->apimodel->insertBatchData('master_policy_family_construct', $data2);
    		$this->apimodel->deletemppb($policy_sub_type_id);
    		$data3 = array('master_policy_id'=>$policy_sub_type_id,'si_premium_basis_id'=>$sibasis);
    		$this->apimodel->insertData('master_policy_premium_basis_mapping', $data3);
    		$this->apimodel->deletempsi($policy_sub_type_id);
    		$data4 = array('master_policy_id'=>$policy_sub_type_id,'suminsured_type_id'=>$sitype);
    		$this->apimodel->insertData('master_policy_si_type_mapping', $data4);
    		if($sibasis != 1){
    		    $this->apimodel->deletempp($policy_sub_type_id);
    		    $this->apimodel->insertBatchData('master_policy_premium', $exceldata);
    		}else{
    		    $this->apimodel->deletempp($policy_sub_type_id);
        		$j=0;$data5=array();
        		for($j = 0;$j < count($sum_insured_opt);$j++){
        		    $data5[$j]['master_policy_id']=$policy_sub_type_id;
        		    $data5[$j]['sum_insured']=$sum_insured_opt[$j];
        		    $data5[$j]['premium_rate']=$premium_opt[$j];
        		    $data5[$j]['is_taxable']=$tax_opt[$j];
        		}
    		$this->apimodel->insertBatchData('master_policy_premium', $data5);}
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record updated successfully.'  ), "Data" => $plan_id ));
			exit;
		}else{
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'  ), "Data" => NULL ));
			exit;
		}
		}else{
			echo $checkToken;
		}
		
	}
	function checkplanname(){
	    $checkToken = $this->verify_request($_POST['utoken']);
	    if(!empty($checkToken->username)){
	    $plan = $this->input->post('plan');
	    $id = $this->input->post('id');
	    if(empty($id)){
	    $name = $this->apimodel->checkplanname($plan);}else{
	    $name = $this->apimodel->checkplanname($plan,$id);    
	    }
	    if(count($name) > 0){
	        echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Plan name already exist.')));
			exit;
	    }else{
	        echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'No Match Found.')));
			exit;
	    }
	    }else{
			echo $checkToken;
		}
	}
	function checkpolicynumber(){
	    $checkToken = $this->verify_request($_POST['utoken']);
	    if(!empty($checkToken->username)){
	    $policy = $this->input->post('policy');
	    $id = $this->input->post('id');
	    if(empty($id)){
	    $name = $this->apimodel->checkpolicynumber($policy);}else{
	    $name = $this->apimodel->checkpolicynumber($policy,$id);    
	    }
	    if(count($name) > 0){
	        echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'Policy Number already exist.')));
			exit;
	    }else{
	        echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'No Match Found.')));
			exit;
	    }
	    }else{
			echo $checkToken;
		}
	}
	function getproductDetails(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		
		if(!empty($checkToken->username)){
	    $data = array();
	    $utoken = $this->input->post('utoken');
		$id = $this->input->post('id');
		$data['plan_id'] = $id;
		$data['details'] = $this->apimodel->getProductDetails($id);
		$data['sitypes'] = $this->apimodel->getSiType();
		$data['insurers'] = $this->apimodel->getInsurer();
		$data['sipremiumbasis'] = $this->apimodel->getSiPremiumBasis();
		$data['members'] = $this->apimodel->getMembers();
		echo json_encode($data);
		}else{
			echo $checkToken;
		}
	}
	
	function addNewProductView(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		if(!empty($checkToken->username)){
	    $data = array();
	    $data['members'] = $this->apimodel->getMembers();
	    $data['creditors'] = $this->apimodel->getCreditors();
	    $data['payment_modes'] = $this->apimodel->getPaymentModes();
	    $data['policytypes'] = $this->apimodel->getPolicyType();
		$data['policysubtypes'] = $this->apimodel->getPolicySubType();
		echo json_encode($data);
		}else{
			echo $checkToken;
		}
	}
	function editproduct(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		if(!empty($checkToken->username)){
	    $data = array();
	    $data['plan_id'] = $_POST['id'];
	    $data['details'] = $this->apimodel->getProductDetails($_POST['id']);
	    $data['planpayments'] = $this->apimodel->getPlanPayments($_POST['id']);
	    $data['members'] = $this->apimodel->getMembers();
	    $data['creditors'] = $this->apimodel->getCreditors();
	    $data['payment_modes'] = $this->apimodel->getPaymentModes();
	    $data['policytypes'] = $this->apimodel->getPolicyType();
		$data['policysubtypes'] = $this->apimodel->getPolicySubType();
		echo json_encode($data);
		}else{
			echo $checkToken;
		}
	}
	function addEditPolicyProposalCustDetails(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		if(!empty($checkToken->username)){
	    $data = array();
	    $customer_id = $_POST['customer_id'];
	    $data['address_line1'] = $this->input->post('address_line1');
	    $data['address_line2'] = $this->input->post('address_line2');
	    $data['address_line3'] = $this->input->post('address_line3');
	    $data['mobile_no2'] = $this->input->post('mobile_no2');
	    $data['pincode'] = $this->input->post('pin_code');
	    $data['city'] = $this->input->post('city');
	    $data['state'] = $this->input->post('state');
	    $data['email_id'] = $this->input->post('email_id');
		$this->apimodel->updateRecordarr('master_customer',$data,array('customer_id'=>$customer_id));
		echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record Inserted.')));
			exit;
		}else{
			echo $checkToken;
		}
	}
	
	function addEditPolicyProposalCoapplicantDetails(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		if(!empty($checkToken->username)){
	    $data = array();
	    $customer_id = $_POST['customer_id'];
		$lead_id = $_POST['lead_id'];
		$trace_id = $_POST['trace_id'];
		$plan_id = $_POST['plan_id'];
		$data['createdby'] = $this->input->post('user_id');
		$data['first_name'] = $this->input->post('first_name');
		$data['last_name'] = $this->input->post('last_name');
		$data['middle_name'] = $this->input->post('middle_name');
		$data['dob'] = $this->input->post('dob');
		$data['mobile_no'] = $this->input->post('mobile_no');
		$data['salutation'] = $this->input->post('salutation');
		$data['gender'] = $this->input->post('gender');
	    $data['address_line1'] = $this->input->post('address_line1');
	    $data['address_line2'] = $this->input->post('address_line2');
	    $data['address_line3'] = $this->input->post('address_line3');
	    $data['mobile_no2'] = $this->input->post('mobile_no2');
	    $data['pincode'] = $this->input->post('pin_code');
	    $data['city'] = $this->input->post('city');
	    $data['state'] = $this->input->post('state');
	    $data['email_id'] = $this->input->post('email_id');
		if(!empty($customer_id)){
		$this->apimodel->updateRecordarr('master_customer',$data,array('customer_id'=>$customer_id));
		echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record Inserted.', "Data"=> "")));
			exit;
		}else{
			$data['lead_id'] = $lead_id;
			$data['full_name'] = $data['first_name']." ".$data['middle_name']." ".$data['last_name'];
			$customer_insert_id = $this->apimodel->insertData('master_customer',$data,1);
			$data2 = array('lead_id'=>$lead_id,'plan_id'=>$plan_id,'customer_id'=>$customer_insert_id,'trace_id'=>$trace_id,'created_by'=>$data['createdby']);
			$proposal_details_id = $this->apimodel->insertData('proposal_details',$data2,1);
			echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record Inserted.'), "Data" => array('cust_id'=>$customer_insert_id,'prop_id'=>$proposal_details_id)));
			exit;
		}
		
		}else{
			echo $checkToken;
		}
	}
	
	
	function addEditPolicyProposalNomineeDetails(){
	    $checkToken = $this->verify_request($_POST['utoken']);
		if(!empty($checkToken->username)){
	    $data = array();
	    $lead_id = $_POST['lead_id'];
		$proposal_details_id = $_POST['proposal_details_id'];
		$data['nominee_email'] = (!empty($_POST['nominee_email'])) ? $_POST['nominee_email'] : '';
		$data['nominee_contact'] = (!empty($_POST['nominee_contact'])) ? $_POST['nominee_contact'] : '';
		$data['nominee_dob'] = (!empty($_POST['nominee_dob'])) ? date('Y-m-d',strtotime($_POST['nominee_dob'])) : '';
		$data['nominee_gender'] = (!empty($_POST['nominee_gender'])) ? $_POST['nominee_gender'] : '';
		$data['nominee_salutation'] = (!empty($_POST['nominee_salutation'])) ? $_POST['nominee_salutation'] : '';
		$data['nominee_last_name'] = (!empty($_POST['nominee_last_name'])) ? $_POST['nominee_last_name'] : '';
		$data['nominee_first_name'] = (!empty($_POST['nominee_first_name'])) ? $_POST['nominee_first_name'] : '';
		$data['nominee_relation'] = (!empty($_POST['nominee_relation'])) ? $_POST['nominee_relation'] : '';
		$this->apimodel->updateRecordarr('proposal_details',$data,array('proposal_details_id'=>$proposal_details_id));
		echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record Updated.')));
		exit;
		}else{
			echo $checkToken;
		}
	}
	
	//Payment Redirection
	
	public function payment_redirection()
	{
		$emp_id = $_POST['lead_id'];
		
		if($emp_id){

			/*to calculate premium if multiple products are selected*/
			$queryData = $this->db->query("SELECT ms.full_name,mp.payment_url,ed.lead_id,ms.customer_id,epd.proposal_details_id,ms.email_id,ms.mobile_no,SUM(p.premium_amount) as premium,epd.status FROM master_plan AS mp, master_customer AS ms, proposal_details AS epd,lead_details AS ed,proposal_policy as p WHERE mp.plan_id = ed.plan_id AND p.lead_id = ed.lead_id AND p.proposal_details_id=epd.proposal_details_id AND ed.primary_customer_id = ms.customer_id AND ed.lead_id=".$emp_id)->row_array();
            
			//echo "<pre>";print_r($queryData);exit;
            $premiumAmount = $queryData['premium'];
            $leadId = $queryData['lead_id'];
            $email = $queryData['email_id'];
            $mobileNumber = $queryData['mobile_no'];
            $customer_name = $queryData['full_name'];
	        $payment_url = $queryData['payment_url'];
			$status = $queryData['status'];
			/*end of process
			
			$query = $this
			->db
			->query("SELECT ed.acc_type,ed.acc_no,ed.customer_name,ed.lead_id,ed.emp_id,epd.policy_detail_id,mpst.payment_source_code,ed.email,ed.mob_no,p.premium,mpst.payment_url,p.status,p.id as proposal_id,mpst.payu_info_url,mpst.product_code FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.product_name AND p.policy_detail_id=epd.policy_detail_id AND ed.emp_id=".$emp_id." order by p.policy_detail_id desc")->row_array();*/
			//print_pre($query);exit;
			if(!empty($query))
			{
				
				if($status == 'Approved'){
					redirect(base_url("payment_success_view_call_abc/".$emp_id));
				}else{
					
					$lead_data = $this->get_all_quote_call($emp_id);
					//print_r($lead_data);exit;
					
					if($lead_data){
						
						$CKS_data = "ABC|ABCGRP|PO|".base_url("payment_success_view_call_abc/".$emp_id)."|LEADID|".$leadId."|".$customer_name."|".$email."|".substr(trim($mobileNumber), -10)."|".round($premiumAmount,2)."|ABC_".$leadId."|".$this->hash_key;
						
						$CKS_value = hash($this->hashMethod, $CKS_data);
						
						$bank_data = json_decode($query['json_qote'],true);
						
						$manDateInfo = array(
								//"ApplicationNo"=> $lead_data['msg'],
								"ApplicationNo"=> $leadId,
								"AccountHolderName"=> $customer_name,
								"BankName"=> ($bank_data['AXISBANKACCOUNT'] == 'Y')?'Axis Bank':'Other',
								"AccountNumber"=> empty($bank_data['ACCOUNTNUMBER'])?'':$bank_data['ACCOUNTNUMBER'],
								"AccountType"=> null,
								"BankBranchName"=> empty($bank_data['BRANCH_NAME'])?'':$bank_data['BRANCH_NAME'],
								"MICRNo"=> null,
								"IFSC_Code"=> empty($bank_data['IFSCCODE'])?'':$bank_data['IFSCCODE'],
								"Frequency"=> "ANNUALLY");

						$dataPost = array(
									"signature"=> $CKS_value,
									"Source"=> "ABC",
									"Vertical"=> "ABCGRP",
									"PaymentMode"=> "PO",
									"ReturnURL"=> base_url("payment_success_view_call_abc/".$emp_id),
									"UniqueIdentifier" => "LEADID",
									"UniqueIdentifierValue" => $leadId,
									"CustomerName"=> $customer_name,
									"Email"=> $email,
									"PhoneNo"=> substr(trim($mobileNumber), -10),
									"FinalPremium"=> round($premiumAmount,2),
									"ProductInfo"=> "ABC_".$leadId,
									//"Additionalfield1"=> "",
									"MandateInfo"=>$manDateInfo 
									);
									
						$data_string = json_encode($dataPost);

						$encrypted = openssl_encrypt($data_string, $this->algoMethod, $this->encrypt_key, 0);
						$decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);
						
						$url = "https://pg_uat.adityabirlahealth.com/pgmandate/service/home/sourcelanding";
						$data = array('REQUEST'=>$encrypted);
						
						$c = curl_init();
						curl_setopt($c, CURLOPT_URL, $url);
						curl_setopt($c, CURLOPT_POST, 0);
						curl_setopt($c, CURLOPT_POSTFIELDS, $data);
						curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
						curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
						curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);

						$result = curl_exec($c);
						curl_close($c);
						$result = json_decode($result, true);
						
						$request_arr = ["lead_id" => $leadId, "req" => "ecrypt-".json_encode($data)."decrypt-".$decrypted,"res" => json_encode($result),"product_id"=> "ABC", "type"=>"payment_request_post"];
						
						$this->db->insert('logs_docs',$request_arr);
						
						if($result && $result['Status']){
							//echo "WELCOME To ABHI";
							redirect($result['PaymentLink']);
						}else{
							if($result['ErrorList'][0]['ErrorCode'] == 'E005'){
								$check_pg = $this->apimodel->real_pg_check($leadId);
								if($check_pg){
									redirect(base_url("payment_success_view_call_abc/".$emp_id));
								}else{
									echo "Error in Enquiry API";
								}
							}else{
								echo $result['ErrorList'][0]['Message'];
							}
							
						}
						
					}else{
						
						redirect(base_url("/payment_error_view_call_abc/" . $emp_id."/1"));
						
					}
					
				}
				
			}else{
				echo "Payment link has been expired, Please get in touch with your Branch RM";
			}
		
		}
	}
	
	public function get_all_quote_call($id){
		$policy_details = $this->apimodel->getdata("proposal_policy","*","lead_id='".$id."' ");
			//get primary customer
		$primary_customer = $this->db->get_where('lead_details',array('lead_id'=>$id))->row()->primary_customer_id;
		//echo "<pre>";print_r($policy_details);exit;
		
		//Pass nominee details
		
		$count = 1;
		$maxcount = count($policy_details);
		$succ = true;
		foreach($policy_details as $proposal){
			$proposal_details = $this->apimodel->getdata("proposal_details","*","proposal_details_id='".$proposal->proposal_details_id."' ");
			//echo $proposal['master_policy_id'];exit;
			$quick_qoute = $this->apimodel->get_quote_data($proposal_details[0]['lead_id'], $primary_customer, $proposal['master_policy_id'], $proposal['proposal_policy_id'], $proposal_details, $proposal['policy_sub_type_id'], $proposal['sum_insured']);
			
			
			if($quick_qoute['status'] == 'error'){
				return false;
				exit;
			}
		}
		if($succ){
			return true;
		}
	}
	
	public function payment_error_view($lead_id){
		//echo "payment_error_view called";exit();
		//$emp_id = $this->emp_id;
		
		$lead_arr = $this->db->query("select lead_id,email_id from lead_details where lead_id = '$lead_id' ")->row_array();
		$lead_id = $lead_arr['lead_id'];
		$email = $lead_arr['email_id'];
		//echo "in";exit;
		$this->load->abc_portal_template('payment_error_view',compact('lead_id','email'));
		
	}

	public function payment_success_view($emp_id_encrypt) 
	{
		if(!is_numeric($emp_id_encrypt)){   
			$emp_id = encrypt_decrypt_password($emp_id_encrypt,'D');
		}else{
			$emp_id = $emp_id_encrypt;
		}
		//echo $emp_id;exit;
		
		$encrypted = $this->input->post('RESPONSE');
		
		if($encrypted)
		{
			$decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);
			$post_data = json_decode($decrypted,true);
			
			extract($post_data);
			//echo $TxMsg;exit;
			if($TxStatus && ($TxMsg == 'Payment Received' || $TxMsg == 'success') && $PaymentStatus == 'PR'){
				$TxStatus = "success";
				$TxMsg = "Approved";
			}
		}
		
		$query = $this->db->query("SELECT ed.primary_customer_id,ed.lead_id,mpst.plan_name FROM employee_policy_detail AS epd,master_plan AS mpst,lead_details AS ed WHERE mpst.plan_id = ed.lead_id AND ed.lead_id='".$emp_id."'")->row_array();
		/*echo $this->db->last_query();
		print_pre($query);exit;*/
		if($query)
		{
			
			if(isset($TxRefNo))
			{
				$request_arr = ["lead_id" => $query['lead_id'], "req" => $encrypted ,"res"=>$decrypted,"product_id"=> "ABC", "type"=>"payment_response_post"];
				$this->apimodel->insert('logs_docs',$request_arr);
				
				$arr = ["payment_remark" => $TxStatus,"status" => $TxMsg,"premium_amount" => $amount,"transaction_date" => $txnDateTime,"transaction_number" => $TxRefNo];
				
				$proposal_ids = $this->db->query("select id as proposal_id from proposal_details where lead_id='".$query['lead_id']."'")->result_array();
				
				foreach ($proposal_ids as $query_val)
				{
					$this->db->where("proposal_details_id",$query_val['proposal_id']);
					$this->db->update("proposal_details",$arr);	
				}
				$this->db->where("lead_id",$query['lead_id']);
				$this->db->update("lead_details",array('status'=>"Approved"));	
				//echo $this->db->last_query();exit;
			}
			//exit;
			if(isset($EMandateStatus))
			{
				$query_emandate = $this->db->query("select * from emandate_data where lead_id=".$query['lead_id'])->row_array();
				
				if($EMandateStatus == 'MS'){
					$mandate_status = 'Success';
					
					$this->obj_api->send_message($query['lead_id'],'success');
					
				}elseif($EMandateStatus == 'MI'){
					$mandate_status = 'Emandate Pending';
				}elseif($EMandateStatus == 'MR'){
					$mandate_status = 'Emandate Received';
				}else{
					$mandate_status = 'Fail';
					
					$this->obj_api->send_message($query['lead_id'],'fail');
				}
			
				if($query_emandate > 0){
					
					$arr = ["TRN" => $EMandateRefno,"status_desc" => $EMandateStatusDesc,"status" => $mandate_status,"mandate_date" => date('Y-m-d h:i:s',strtotime($EMandateDate))];
					
					$this->db->where("lead_id",$query['lead_id']);
					$this->db->update("emandate_data",$arr);
				}else{
					
					$arr = ["lead_id" => $query['lead_id'],"TRN" => $EMandateRefno,"status_desc" => $EMandateStatusDesc,"status" => $mandate_status,"mandate_date" => date('Y-m-d h:i:s',strtotime($EMandateDate))];
					
					$this->db->insert("emandate_data", $arr);
				}
			}
			
			
			if(isset($TxRefNo))
			{
				//echo $query['lead_id'];exit;
				$check_result = $this->obj_api->policy_creation_call($query['lead_id']);
				//echo $this->db->last_query();
				// print_pre($check_result);exit;exit;
				if($check_result['Status'] == 'Success')
				{
					
					$data_policy[0] = $this->db->query("SELECT GROUP_CONCAT(DISTINCT(certificate_number)) certificate_number FROM api_proposal_response m WHERE m.emp_id = '$emp_id' GROUP BY emp_id")->row_array();
					
					$query_new = $this->db->query("select p.proposal_policy_id,ed.lead_id,p.premium from proposal_policy as p,lead_details as ed  where p.lead_id = ed.lead_id and ed.lead_id ='$emp_id'");
					$data = $query_new->result_array();
						
					$this->load->abc_portal_template("thankyou_view_abc",compact('data_policy','data','amount'));
					
					
				}else{
					//echo "in";exit;
					redirect(base_url("/payment_error_view_call_abc/" . $emp_id));
					
				}
			
			}else{	
					
				$query_new = $this->db->query("select p.proposal_policy_id,ed.lead_id,p.premium from proposal_policy as p,lead_details as ed  where p.lead_id = ed.lead_id and ed.lead_id ='$emp_id'");
				
				$this->load->abc_portal_template("thankyou_view_abc",compact('data','amount'));
			}
			
			
		}else{
			
			echo "Payment link has been expired, Please get in touch with your Branch RM";
	
		}
		
		
	}
	
	//For login all users
	function userLogin() {
		
		//echo "here";exit;
		
		if (!empty($_POST) && isset($_POST)) {
		
			if (empty($_POST['username']) ) {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Please enter username." ), "Data" => NULL ));
				exit;
			}
			 
			if (!empty($_POST['password'])) {
				$password = md5($_POST['password']);
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Please enter password." ), "Data" => NULL ));
				exit;
			}
			
			//echo "<pre>";print_r($_POST);exit;
				 
			$condition = "i.user_name='".$_POST['username']."' &&  i.employee_password='".$password."' ";
			
			$result_login = $this->apimodel->login_check($condition);
			$result_data = $result_login[0];
			
			
			$utoken = $result_data['employee_id']; 
			$success_msg = "Login Successfull. ";
      
			if (is_array($result_login) && count($result_login) > 0) {
				
				//JWT
				/*$kunci = $this->config->item('jwtkey');
				$token['id'] = $result_data['employee_id'];  //From here
				//$token['username'] = $u;
				$date = new DateTime();
				$token['iat'] = $date->getTimestamp();
				$token['exp'] = $date->getTimestamp() + 60*60*5; //To here is to generate token
				$output['token'] = JWT::encode($token,$kunci ); //This is the output token
				*/
				
				//$token = generateToken(['username' => $result_data['employee_id']]);
				$date = new DateTime();
				$tokenData = array('username' => $result_data['employee_id'], 'iat' => $date->getTimestamp(), 'exp'=> $date->getTimestamp() + 60*60*5 );
				$token = AUTHORIZATION::generateToken($tokenData);
				
				//echo "<pre>";print_r($token);exit;
				
				$result_data['utoken'] = $token;  
				echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => $success_msg  ), "Data" => $result_data ));
				exit;
			} else {
				echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Incorrect Username or Password." ), "Data" => NULL ));
				exit;
			}
		} else {
			echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => "Hearder section empty." ), "Data" => NULL ));
			exit;
		}
	}
	
	
	
	
	
}

?>