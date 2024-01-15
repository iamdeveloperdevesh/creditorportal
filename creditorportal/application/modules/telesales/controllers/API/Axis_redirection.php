<?php if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

require_once(APPPATH."controllers/MY_RetailSessionCheck.php");

class Axis_redirection extends MY_RetailSessionCheck
{
	public $emp_id;
	public $parent_id;
	
	public $algoMethod;
    public $hashMethod;
    public $hash_key;
	public $encrypt_key;
	
    function __construct()
    {
        parent::__construct();	
	
		//d2c_session check in construct
		if (!$this->session->userdata('d2c_session')) {
            redirect('render_session_timeoutpage');
		}
		
		//d2c_session get value in
		$aD2CSession = $this->session->userdata('d2c_session');
		$this->emp_id = encrypt_decrypt_password($aD2CSession['emp_id'],'D');
		$this->parent_id = $aD2CSession['policy_parent_id'];

		$this->db= $this->load->database('axis_retail',TRUE);
		
		$this->load->model("API/Payment_integration_retail", "obj_api", true);
		$this->load->model("Logs_m");
		
		$this->algoMethod = 'aes-128-ecb';
        $this->hashMethod = 'SHA512';
        $this->hash_key = 'razorpay';
        $this->encrypt_key = 'axisbank12345678';
		
		//echo encrypt_decrypt_password(620181);
		// ini_set('display_errors', 1);
		// ini_set('display_startup_errors', 1);
		// error_reporting(E_ALL);
    }

	
	public function payment_redirection()
	{
		//echo 1;exit;
		$emp_id = $this->emp_id;
		
		$request_arr = ["type" => "4"];
		$this->db->where("emp_id",$emp_id);
		$this->db->update("user_activity",$request_arr);
				
		$emp_id_encrypt = encrypt_decrypt_password($emp_id);
		
		$query = $this
		->db
		->query("SELECT ed.acc_type,ed.acc_no,ed.customer_name,ed.lead_id,ed.emp_id,epd.policy_detail_id,mpst.payment_source_code,ed.email,ed.mob_no,p.premium,mpst.payment_url,p.status,p.id as proposal_id,mpst.payu_info_url,mpst.product_code,imd.branch_name FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,master_imd imd WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id and p.policy_detail_id=epd.policy_detail_id and ed.branch_sol_id = imd.BranchCode and ed.emp_id=".$emp_id)->row_array();
			
	
		if(!empty($query))
		{
			
		if($query['status'] != 'Payment Pending'){			
			redirect(base_url('payment_success_view_call/'.$emp_id_encrypt));
		}else{
					
		$query_data = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id'")->row_array();
				
		if($query_data > 0){
			
			if($query_data['status'] == 'success'){
				
				$check_pg = $this->obj_api->real_pg_check($query['lead_id']);
				
				if($check_pg){
					redirect(base_url('payment_success_view_call/'.$emp_id_encrypt));
				}else{
					$lead_data = array(
							"status" => "Success",
							"msg" => $query_data['QuotationNumber']
						);
				}
				
			}else{
				
			$lead_data = $this->obj_api->get_quote_data($query['emp_id'], $query['policy_detail_id']);
			
			}
			
		}else{
			
			$lead_data = $this->obj_api->get_quote_data($query['emp_id'], $query['policy_detail_id']);	
		
		}
			
		if($lead_data['status'] == 'Success'){
			
			$Source = "AX";
			$Vertical = "AXDCGRP";
			$PaymentMode = "PP";
			$ReturnURL = base_url('payment_success_view_call/'.$emp_id_encrypt);
			$UniqueIdentifier = "LEADID";
			$UniqueIdentifierValue = $query['lead_id'];
			$CustomerName = $query['customer_name'];
			$Email = $query['email'];
			$PhoneNo = substr(trim($query['mob_no']), -10);
			$FinalPremium = round($query['premium'],2);
			$ProductInfo = "Group Activ Health";
			
			$CKS_data = $Source."|".$Vertical."|".$PaymentMode."|".$ReturnURL."|".$UniqueIdentifier."|".$UniqueIdentifierValue."|".$CustomerName."|".$Email."|".$PhoneNo."|".$FinalPremium."|".$ProductInfo."|".$this->hash_key;
						
			$CKS_value = hash($this->hashMethod, $CKS_data);
			
			$manDateInfo = array(
					"ApplicationNo"=> $UniqueIdentifierValue,
					"AccountHolderName"=> $CustomerName,
					"BankName"=> "Axis Bank",
					"AccountNumber"=> empty($query['acc_no'])?'':$query['acc_no'],
					"AccountType"=> empty($query['acc_type'])?'':$query['acc_type'],
					"BankBranchName"=> empty($query['branch_name'])?'':$query['branch_name'],
					"MICRNo"=> null,
					"IFSC_Code"=> null,
					"Frequency"=> "As and when presented");

			$dataPost = array(
						"signature"=> $CKS_value,
						"Source"=> $Source,
						"Vertical"=> $Vertical,
						"PaymentMode"=> $PaymentMode,
						"ReturnURL"=> $ReturnURL,
						"UniqueIdentifier" => $UniqueIdentifier,
						"UniqueIdentifierValue" => $UniqueIdentifierValue,
						"CustomerName"=> $CustomerName,
						"Email"=> $Email,
						"PhoneNo"=> $PhoneNo,
						"FinalPremium"=> $FinalPremium,
						"ProductInfo"=> $ProductInfo,
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
			
			$request_arr = ["lead_id" => $query['lead_id'], "req" => "ecrypt-".json_encode($data)."decrypt-".$decrypted,"res" => json_encode($result),"product_id"=> $query['product_code'], "type"=>"payment_request_post"];
			
			$dataArray['tablename'] = 'logs_docs'; 
			$dataArray['data'] = $request_arr; 
			$this->Logs_m->insertLogs($dataArray);
			
			if($result && $result['Status']){
				
				$query_check = $this->db->query("select * from payment_txt_ids where lead_id='".$query['lead_id']."'")->row_array();
							
				if(empty($query_check)){
					$data_arr = ["lead_id" => $query['lead_id'], "txt_id" => 1,"pg_type" => "New"];
					$this->db->insert("payment_txt_ids",$data_arr);
				}else{
					$update_arr = ["cron_count" => 0];
					$this->db->where("lead_id",$query['lead_id']);
					$this->db->update("payment_txt_ids",$update_arr);
				}
							
				//echo "WELCOME To ABHI";
				$var = $result['PaymentLink'];

				/*if(strpos($var, 'http://') !== 0) {
				   redirect('http://' . $var,refresh);
				} 
				else{
				redirect($var,refresh);
				}*/
				redirect($result['PaymentLink']);
			}else{
				if($result['ErrorList'][0]['ErrorCode'] == 'E005'){
					$check_pg = $this->obj_api->real_pg_check($query['lead_id']);
					if($check_pg){
						redirect(base_url('payment_success_view_call/'.$emp_id_encrypt));
					}else{
						echo "Error in Enquiry API";
					}
				}else{
					echo $result['ErrorList'][0]['Message'];
				}
				
			}
			
		}else{		
		
			redirect(base_url('payment_error_view_call'));
			
		}
		
		}
		
		}else{
			echo "Error in proposal create";	
		}
		
	}
	
	
  public function coi_url_call()
	{
		$emp_id = $this->emp_id;
		echo json_encode($this->obj_api->coi_url_call_m($emp_id));				
	}
	
  public function payment_error_view(){
		
		$emp_id = $this->emp_id;
		
		$lead_arr = $this->db->query("select lead_id,email from employee_details where emp_id = '$emp_id' ")->row_array();
		$lead_id = $lead_arr['lead_id'];
		$email = $lead_arr['email'];
		
		$this->load->retail_template('Retail/payment_error_view',compact('emp_id','lead_id','email'));
		
	}
	
  public function check_error_data() {
	$emp_id = $this->emp_id;
	$emp_id_encrypt = encrypt_decrypt_password($this->emp_id);
	
	$query_check = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and status = 'success'")->row_array();
	
	if($query_check > 0){

	$query = $this->db->query("select pd.payment_status,p.status as proposal_status,p.count as p_count from employee_details as ed,proposal as p,payment_details as pd where ed.emp_id=p.emp_id and p.id=pd.proposal_id and ed.emp_id = '$emp_id' group by p.id")->row_array();
	
	if($query['payment_status'] == 'No Error' && $query['proposal_status'] != 'Success' && $query['p_count'] < 3){
		// quote genarate,payment done but policy pending
		$data = array(
		"status" => "1",
		"check" => "2",
		"url" => base_url('payment_success_view_call/'.$emp_id_encrypt),  
		);
		
	}else if($query['payment_status'] == 'No Error' && $query['proposal_status'] != 'Success' && $query['p_count'] >= 3){
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
		"url" => base_url('api/payment_redirection'),
		);
		
	}
		
	}else{
		
	$query = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and count < 3 and status = 'error'")->row_array();
	
	if($query > 0){
		// quote pending,payment pending
		$data = array(
		"status" => "1",
		"check" => "1",		
		"url" => base_url('api/payment_redirection'),
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
	
	echo json_encode($data);
	
 }

  

	public function razorpay_returnurl(){
		$post_data = $this->input->post(NULL, TRUE); //returns all POST items with XSS filter
		
		$this->load->view('PG_view/razor_returnpage', compact('post_data'));
	}

	public function redirect_pg_url(){
		$post_data = array();
		$this->load->view('PG_view/razor_redirect_pg_page', compact('post_data'));
	}
	
}