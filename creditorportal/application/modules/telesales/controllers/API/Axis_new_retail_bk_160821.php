<?php if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

//require_once(APPPATH."controllers/MY_TelesalesSessionCheck.php");
//class Axis_telesale extends MY_TelesalesSessionCheck
require_once(APPPATH.'razorpay_php/Razorpay.php');
use Razorpay\Api\Api;

class Axis_new_retail extends CI_controller
{
	public $algoMethod;
    public $hashMethod;
    public $hash_key;
	public $encrypt_key;
	
    function __construct()
    {
        parent::__construct();	
	
		$this->algoMethod = 'aes-128-ecb';
        $this->hashMethod = 'SHA512';
        $this->hash_key = 'razorpay';
        $this->encrypt_key = 'axisbank12345678';

        $this->productCode = 'D2C2';
		
		$this->load->model("API/Payment_new_retail_m", "obj_api", true);
		$this->load->model("API/Payment_integration_freedom_plus", "external_obj_api", true);
		$this->load->model("Logs_m");

		$this->db = $this
            ->load
            ->database('axis_retail', true);
		
		// ini_set('display_errors', 0);
		// ini_set('display_startup_errors', 0);
		// error_reporting(E_ALL);
		
		//echo encrypt_decrypt_password(620547);
    }
	
	public function agent_coi_mail_trigger() 
	{
		echo json_encode($this->obj_api->agent_coi_mail_trigger_m());
	}
	
	/* cron */
	public function update_rejected()
	{
		$this->obj_api->update_rejected_m();
	}

    public function payment_url_send() 
	{
		$this->obj_api->payment_url_send_m();
		echo json_encode(['status' =>'success']);
	}
	
	public function agent_policy_create() 
	{
		echo json_encode($this->obj_api->agent_policy_create_m());
	}
	
	public function check_error_data() {
		$emp_id_encrypt = $this->session->userdata('d2c_session')['emp_id'];
		$emp_id = encrypt_decrypt_password($emp_id_encrypt,'D');
		
		$query_check = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and status = 'success'")->row_array();
		
		if($query_check > 0){
			$query = $this->db->query("select pd.payment_status,p.status as proposal_status,p.count as p_count from employee_details as ed,proposal as p,payment_details as pd where ed.emp_id=p.emp_id and p.id=pd.proposal_id and ed.emp_id = '$emp_id' group by p.id")->row_array();

			if($query['payment_status'] == 'No Error' && $query['proposal_status'] != 'Success' && $query['p_count'] < 3){
				// quote genarate,payment done but policy pending
				$data = array(
				"status" => "1",
				"check" => "2",
				"url" => base_url('retail/payment_return_view/'.$emp_id_encrypt),  
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
				"url" => base_url('payment_redirection_retail/'.$emp_id_encrypt),
				);
				
			}
		}else{
			
		$query = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and count < 3 and status = 'error'")->row_array();
		
		if($query > 0){
			// quote pending,payment pending
			$data = array(
			"status" => "1",
			"check" => "1",		
			"url" => base_url('payment_redirection_retail/'.$emp_id_encrypt),
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
	
	public function payment_error_view()
	{
		$emp_id_encrypt = $this->session->userdata('d2c_session')['emp_id'];
		$emp_id = encrypt_decrypt_password($emp_id_encrypt,'D');
		$lead_arr = $this->db->query("select lead_id,email from employee_details where emp_id = '$emp_id' ")->row_array();
		$lead_id = $lead_arr['lead_id'];
		$email = $lead_arr['email'];
		$this->load->retail_template('Retail/payment_error_view',compact('emp_id','lead_id','email'));
	}

	/**
	 * This Function is use for Updating dropoff_flag value to 0
	 * @param : $emp_id
	 * @author Shardul Kulkarni<shardul.kulkarni@fyntune.com>
	 */ 
	function updateDropOffFlagValue($emp_id = 0) {
		if(!empty($emp_id)) {
			$seconds = 30;
			$date_now = date("Y-m-d H:i:s");
			$moddate = date("Y-m-d H:i:s", (strtotime(date($date_now)) + $seconds));
			$request_arr_dropoff = ["dropoff_flag" => "0", 'modified_date'=>$moddate];
			$this->db->where("emp_id",$emp_id);
			$this->db->update("employee_details", $request_arr_dropoff);
		}		
	}

	function setCustomerSession($aD2CSession) {
        //unset previous user data from session.	
        if ($this->session->userdata('d2c_session')) {
            $this->session->unset_userdata('d2c_session');
        }
        //set new user data in session.		
        $this->session->set_userdata($aD2CSession);
        /* Regenerate a new session upon successful authentication. Any session token used prior to 
          login should be discarded and only the new token should be assigned for the user till the user
          logs out.
          This session token should be properly expired when the user logs out. */
        $this->session->regenerate_id();
        $session_id = session_id();
        $aD2CSession = $this->session->userdata('d2c_session');
        $emp_id = encrypt_decrypt_password($aD2CSession['emp_id'], 'D');
        $rsEmp = $this->db->select("id, updated_time")->where(["emp_id" => $emp_id])->get("tbl_leadid_session");
        if ($rsEmp->num_rows() > 0) {
            //update record			
            $aRow = $rsEmp->row();
            $id = $aRow->id;

            $data = array(
                'sessionid' => $session_id,
                'updated_time' => time(),
            );
            $this->db->where('id', $id);
            $this->db->update('tbl_leadid_session', $data);
			
        } else {
			$aLeadSession = ["emp_id" => $emp_id, "sessionid" => $session_id, "updated_time" => time()];
            $this->db->insert("tbl_leadid_session", $aLeadSession);
			
        }
		/* Added By Shardul Kulkarni on 07-08-2020 for making dropoff_flag value to 0 Start */
		$this->updateDropOffFlagValue($emp_id);
		/* Added By Shardul Kulkarni on 07-08-2020 for making dropoff_flag value to 0 End */
    }

	public function payment_redirect_view($emp_id_encrypt)
	{
		
		$emp_id = encrypt_decrypt_password($emp_id_encrypt,'D');
		$this->session->set_userdata('emp_id', $emp_id);

		$request_arr = ["type" => "4"];
		$this->db->where("emp_id",$emp_id);
		$this->db->update("user_activity",$request_arr);
		
		if($emp_id){

			$query = $this->db->query("SELECT ed.customer_name,mpst.product_name,ed.lead_id,ed.emp_id,epd.policy_detail_id,mpst.payment_source_code,ed.email,ed.mob_no,sum(p.premium) as premium,mpst.payment_url,p.status,mpst.product_code,pd.bank_name,ed.acc_no FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,payment_details as pd WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND p.id = pd.proposal_id AND ed.emp_id=".$emp_id)->row_array();
			
			$productname="Group Activ Health";

			if(!empty($query))
			{
				if($query['status'] != 'Payment Pending'){
					// redirect(base_url('tls_payment_return_view/'.$emp_id_encrypt));
					print_pre("Return to payment return view Proposal status in not Pending");
				}else{
					$lead_data = $this->obj_api->get_all_quote_call($query['emp_id']);
					if($lead_data['status'] == 'Success'){
						
						/*$check_pg = $this->external_obj_api->real_pg_check($query['lead_id']);
							
						if($check_pg){
							redirect(base_url('tls_payment_return_view/'.$emp_id_encrypt));
							exit;
						}*/
						/*if($query['product_code']=='T01')
							{
	                         $productname="Axis Tele Inbound Affinity Portal for ABHI";
							}
							else {
							$productname="Axis Telesales";
							
						}	*/
                                           
						$CKS_data = "AX|AXATGRP|PP|".base_url('retail/payment_return_view/'.$emp_id_encrypt)."|LEADID|".$query['lead_id']."|".$query['customer_name']."|".$query['email']."|".substr(trim($query['mob_no']), -10)."|".round($query['premium'],2)."|".$productname."|".$this->hash_key;

						$CKS_value = hash($this->hashMethod, $CKS_data);
						
						$manDateInfo = array(
								//"ApplicationNo"=> $lead_data['msg'],
								"ApplicationNo"=> $query['lead_id'],
								"AccountHolderName"=> $query['customer_name'],
								"BankName"=> null,
								"AccountNumber"=> null,
								"AccountType"=> null,
								"BankBranchName"=> null,
								"MICRNo"=> null,
								"IFSC_Code"=> null,
								"Frequency"=> "As and when presented");

						$dataPost = array(
									"signature"=> $CKS_value,
									"Source"=> "AX",
									"Vertical"=> "AXATGRP",
									"PaymentMode"=> "PP",
									"ReturnURL"=> base_url('retail/payment_return_view/'.$emp_id_encrypt),
									"UniqueIdentifier" => "LEADID",
									"UniqueIdentifierValue" => $query['lead_id'],
									"CustomerName"=> $query['customer_name'],
									"Email"=> $query['email'],
									"PhoneNo"=> substr(trim($query['mob_no']), -10),
									"FinalPremium"=> round($query['premium'],2),
									"ProductInfo"=> $productname,
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

							$redirectLogArr = ["lead_id" => $query['lead_id'], "req" => 'redirected to payment' ,"res"=>'redirected to payment' , "type"=>"redirection_to_payment", "product_id"=> $query['product_code']];
							$this->db->insert("logs_docs",$redirectLogArr);
							//echo "WELCOME To ABHI";
							redirect($result['PaymentLink']);
						}else{
							if($result['ErrorList'][0]['ErrorCode'] == 'E005'){
								$check_pg = $this->obj_api->real_pg_check($query['lead_id']);
								if($check_pg){
									redirect(base_url('retail/payment_return_view/'.$emp_id_encrypt));
								}else{
									echo "Error in Enquiry API";
								}
							}else{
								echo $result['ErrorList'][0]['Message'];
							}
							
						}
					}else{	
						redirect(base_url('retail/payment_error_view_call'));
					}
				}
				
			}else{
				echo "Error in data";
			}
		}
	}

	public function payment_return_view_new($emp_id_encrypt)
	{
		
		//$emp_id = $this->emp_id;	
		$emp_id = encrypt_decrypt_password($emp_id_encrypt,'D');
		$aD2CSession['d2c_session'] = array(
            'emp_id' => $emp_id_encrypt,
            'product_id' => $this->productCode,
        );
		$this->setCustomerSession($aD2CSession);
		
		$query = $this->db->query("SELECT  ed.source_id,ed.product_id,ed.emp_id,ed.lead_id,epd.policy_detail_id,mpst.payment_source_code,ed.email,ed.mob_no,p.premium,mpst.payment_url,GROUP_CONCAT(p.id) as proposal_id FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND p.policy_detail_id=epd.policy_detail_id AND ed.emp_id=".$emp_id." GROUP BY p.emp_id")->row_array();

		$returnLogArr = ["lead_id" => $query['lead_id'], "req" => 'returned after payment' ,"res"=>'returned after payment' , "type"=>"redirection_after_payment", "product_id"=> $query['product_id']];
		$this->db->insert("logs_docs",$returnLogArr);

		if($query){
			
			$encrypted = $this->input->post('RESPONSE');
			// print_pre($encrypted);
			if($encrypted)
			{
				$decrypted = openssl_decrypt($encrypted, "aes-128-ecb", "axisbank12345678", 0);
				$post_data = json_decode($decrypted,true);
				$post_data = array_map( 'trim', $post_data );//to trim the array element

				// print_pre($post_data);

				foreach($post_data as $key=>$val){
					$post_fields[$key] = $val;
				}
				
				$TxMsg = !empty($post_fields['TxMsg']) ? (string)$post_fields['TxMsg'] : 0;	
				$amount = !empty($post_fields['amount']) ? (float)$post_fields['amount'] : 0;	
				$paymentMode = !empty($post_fields['paymentMode']) ? (string)$post_fields['paymentMode'] : 0;	
				$txnDateTime = !empty($post_fields['txnDateTime']) ? (string)$post_fields['txnDateTime'] : 0;	
				$TxRefNo = !empty($post_fields['TxRefNo']) ? (string)$post_fields['TxRefNo'] : 0;	
				$TxStatus = !empty($post_fields['TxStatus']) ? (string)$post_fields['TxStatus'] : 0;
				$PaymentStatus = !empty($post_fields['PaymentStatus']) ? (string)$post_fields['PaymentStatus'] : 0;
				$EMandateStatus = !empty($post_fields['EMandateStatus']) ? (string)$post_fields['EMandateStatus'] : '';
				$EMandateStatusDesc = !empty($post_fields['EMandateStatusDesc']) ? (string)$post_fields['EMandateStatusDesc'] : 0;
				$EMandateRefno = !empty($post_fields['EMandateRefno']) ? (string)$post_fields['EMandateRefno'] : 0;
				$EMandateDate = !empty($post_fields['EMandateDate']) ? (string)$post_fields['EMandateDate'] : 0;
				$Registrationmode = !empty($post_fields['Registrationmode']) ? (string)$post_fields['Registrationmode'] : 0;
				$EMandateFailureReason = !empty($post_fields['EMandateFailureReason']) ? (string)$post_fields['EMandateFailureReason'] : 0;
				$MandateLink = !empty($post_fields['MandateLink']) ? (string)$post_fields['MandateLink'] : 0;
				
				if($TxStatus && ($TxMsg == 'Payment Received' || $TxMsg == 'success') && $PaymentStatus == 'PR'){
					$TxStatus = "success";
					$TxMsg = "No Error";
				}
			}

			// print_pre($decrypted);

			$request_arr = ["lead_id" => $query['lead_id'], "req" => $encrypted ,"res"=>$decrypted , "type"=>"payment_response_post", "product_id"=> $query['product_id']];
  	        $this->db->insert("logs_docs",$request_arr);

			if(isset($TxRefNo)){
				// $request_arr = ["lead_id" => $query['lead_id'], "req" => $encrypted ,"res"=>$decrypted , "type"=>"payment_response_post", "product_id"=> $query['product_id']];
	  			// $this->db->insert("logs_docs",$request_arr);

				$request_arr = ["payment_status" => $TxMsg,"premium_amount" => $amount,"payment_type" => $paymentMode,"txndate" => $txnDateTime,"TxRefNo" => $TxRefNo,"TxStatus"=>$TxStatus,"json_quote_payment"=>json_encode($post_fields)];

				$this->db->where_in('proposal_id', [$query['proposal_id']], false);
                $this->db->where('TxStatus != ', 'success');
                $this->db->update("payment_details", $request_arr);
			}
			
			if(isset($Registrationmode))
			{
				
				$query_emandate = $this->db->query("select * from emandate_data where lead_id=".$query['lead_id'])->row_array();
				
				if($EMandateStatus == 'MS'){
					$mandate_status = 'Success';
				}elseif($EMandateStatus == 'MI'){
					$mandate_status = 'Emandate Pending';
				}elseif($EMandateStatus == 'MR'){
					$mandate_status = 'Emandate Received';
				}elseif ($EMandateStatus == '')
                {
                    $mandate_status = 'Emandate Pending';
                }else{
					$mandate_status = 'Fail';
				}
			
				if($query_emandate > 0){
					
					$arr = ["TRN" => $EMandateRefno,"status_desc" => $EMandateStatusDesc,"status" => $mandate_status,"mandate_date" => date('Y-m-d h:i:s',strtotime($EMandateDate)),"Registrationmode" => $Registrationmode, "EMandateFailureReason" => $EMandateFailureReason,"MandateLink" => $MandateLink];
					
					$this->db->where("lead_id",$query['lead_id']);
					$this->db->update("emandate_data",$arr);
				}else{
					
					$arr = ["lead_id" => $query['lead_id'],"TRN" => $EMandateRefno,"status_desc" => $EMandateStatusDesc,"status" => $mandate_status,"mandate_date" => date('Y-m-d h:i:s',strtotime($EMandateDate)),"Registrationmode" => $Registrationmode, "EMandateFailureReason" => $EMandateFailureReason,"MandateLink" => $MandateLink];
					
					$this->db->insert("emandate_data", $arr);
				}
				
				if($mandate_status == 'Success'){
					$this->obj_api->send_message($query['lead_id'],'success');
				}
				
				if($mandate_status == 'Fail'){
					$this->obj_api->send_message($query['lead_id'],'fail');
				}
				
				if($paymentMode == 'PP' && ($Registrationmode == 'SAD' || $Registrationmode == 'EMI' || $Registrationmode == 'UPI')){
					$this->obj_api->send_message($query['lead_id'],'SAD_EMI_one');
					$this->obj_api->send_message($query['lead_id'],'SAD_EMI_two');
				}
			}
			
			if(isset($PaymentStatus) && $PaymentStatus == 'PI'){
				$check_pg = $this->obj_api->real_pg_check($query['lead_id']);
				if($check_pg){
					redirect(base_url('retail/payment_return_view/'.$emp_id_encrypt));
				}else{
					
					$arr_update = ["is_payment_initiated" => 1];
					
					$this->db->where("lead_id",$query['lead_id']);
					$this->db->update("employee_details",$arr_update);
					
					echo "Response on payment status is pending. Post payment confirmation, proposal will be initiated. Thanks !!";
					exit;
				}
			}
			
			$proposal_id =  $query['proposal_id'];
			// $payment_data = $this->db->query("select payment_status,TxStatus from payment_details where proposal_id=$proposal_id ")->row_array();
			$payment_data = $this->db->query("select payment_status,TxStatus from payment_details where proposal_id IN ($proposal_id)")->row_array();
			
			if($payment_data['TxStatus'] == 'success'){
				$check_result = $this->obj_api->policy_creation_call($query['lead_id']);
				if($check_result['Status'] == 'Success'){

					$request_arr = ["type" => "6"];
					$this->db->where("emp_id",$emp_id);
					$this->db->update("user_activity",$request_arr);
					
					// $customer_data['premium'] = $query['premium'];
					$customer_data['email'] = $query['email'];
					$customer_data['source_id'] = $query['source_id'];
					//dont remove this//
					$customer_data['lead_id'] = $query['lead_id'];
					
					$MandateLink_data = $this->db->query("select MandateLink,Registrationmode from emandate_data where lead_id = '".$query['lead_id']."'")->row_array();
					
					$data_policy = $this->db->query("SELECT apr.*,p.policy_detail_id,epd.policy_no, SUM(apr.gross_premium) AS sum_premium, GROUP_CONCAT(apr.certificate_number) AS all_coi_number, GROUP_CONCAT(fr_ids) AS all_fr_ids FROM api_proposal_response AS apr, proposal AS p,employee_policy_detail AS epd WHERE apr.emp_id = '$emp_id' AND p.id = apr.proposal_id AND epd.policy_detail_id = p.policy_detail_id GROUP BY apr.proposal_no_lead")->result_array();
					foreach ($data_policy as $key => $value) {
						if ($value['relationship_code'] != '')
                        {
                            $temp = explode(",", $value['all_fr_ids']);
                            $temp = implode(",",$temp);
                            $qry = "SELECT GROUP_CONCAT(reference_name) as members from master_family_relation WHERE fr_id IN (" . $temp . ")";
                            $data_policy[$key]['members'] = $this->db->query($qry)->row_array()['members'];
                        }
					}
					
					if($data_policy > 0){
					
					// Shardul CRM Addition Part Start
						// Create Lead In CRM Start
						// $lead_id = json_decode($this->cron_m->createCRMLeadDropOff($emp_id),true); // Commented by Kshitiz
						// Create Lead In CRM End
						
						// Create Member In CRM Start
						if(!empty($lead_id['LeadId'])) {
							//$this->cron_m->insertMemberCRMDropOff($emp_id,$lead_id['LeadId']); // Commented by Kshitiz
						}
						// Create Member In CRM End	
					// Shardul CRM Addition Part End
					$this->load->retail_template("Retail/thankyou",compact('data_policy','customer_data','MandateLink_data'));
					}
				}else{
					// print_pre("Failed Due to Full quote fail");exit;
					$request_arr = ["type" => "6"];
					$this->db->where("emp_id",$emp_id);
					$this->db->update("user_activity",$request_arr);
					redirect(base_url('retail/payment_error_view_call'));
				}
			}else{
				// print_pre($payment_data['TxStatus']);
				// print_pre("Failed due to TxStatus");exit;
				$request_arr = ["type" => "5"];
				$this->db->where("emp_id",$emp_id);
				$this->db->update("user_activity",$request_arr);				
				redirect(base_url('retail/payment_error_view_call'));
			}
			
			
		}
		
		
	}
	
	public function payment_return_view($emp_id_encrypt){
		
		//$emp_id = $this->session->userdata('emp_id');
		$emp_id = encrypt_decrypt_password($emp_id_encrypt,'D');
		$this->session->set_userdata('emp_id', $emp_id);
		
		$encrypted = $this->input->post('RESPONSE');
		
		if($encrypted)
		{
			$decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);
			$post_data = json_decode($decrypted,true);
			
			extract($post_data);
			
			if($TxStatus && ($TxMsg == 'Payment Received' || $TxMsg == 'success') && $PaymentStatus == 'PR'){
				$TxStatus = "success";
				$TxMsg = "No Error";
			}
		}
		
		$query = $this->db->query("SELECT GROUP_CONCAT(p.id) proposal_id,ed.emp_id,ed.lead_id,mpst.product_code FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,payment_details AS pd WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND p.id = pd.proposal_id AND ed.emp_id='".$emp_id."' GROUP BY p.emp_id")->row_array();
		
		if(!empty($query['proposal_id'])){
			
			$ids = explode(',',$query['proposal_id']);
			
			if(isset($TxRefNo)){
				
				$request_arr = ["lead_id" => $query['lead_id'], "req" => $encrypted ,"res"=>$decrypted,"product_id"=> $query['product_code'], "type"=>"payment_response_post"];
				
  	            $dataArray['tablename'] = 'logs_docs'; 
				$dataArray['data'] = $request_arr; 
				$this->Logs_m->insertLogs($dataArray);
			
				$request_arr = ["payment_status" => $TxMsg,"premium_amount" => $amount,"payment_type" => $paymentMode,"txndate" => $txnDateTime,"TxRefNo" => $TxRefNo,"TxStatus"=>$TxStatus,"json_quote_payment"=>json_encode($post_data)];
					
					$this->db->where_in('proposal_id', $ids);
					$this->db->where('TxStatus != ','success');
					$this->db->update("payment_details",$request_arr);

			}
			
			if($Registrationmode)
			{
				$query_emandate = $this->db->query("select * from emandate_data where lead_id=".$query['lead_id'])->row_array();
				
				if($EMandateStatus == 'MS'){
					$mandate_status = 'Success';
				}elseif($EMandateStatus == 'MI'){
					$mandate_status = 'Emandate Pending';
				}elseif($EMandateStatus == 'MR'){
					$mandate_status = 'Emandate Received';
				}elseif($EMandateStatus == ''){
					$mandate_status = 'Emandate Pending';
				}else{
					$mandate_status = 'Fail';
				}
			
				if($query_emandate > 0){
					
					$arr = ["TRN" => $EMandateRefno,"status_desc" => $EMandateStatusDesc,"status" => $mandate_status,"mandate_date" => date('Y-m-d h:i:s',strtotime($EMandateDate)),"Registrationmode" => $Registrationmode, "EMandateFailureReason" => $EMandateFailureReason,"MandateLink" => $MandateLink];
					
					$this->db->where("lead_id",$query['lead_id']);
					$this->db->update("emandate_data",$arr);
				}else{
					
					$arr = ["lead_id" => $query['lead_id'],"TRN" => $EMandateRefno,"status_desc" => $EMandateStatusDesc,"status" => $mandate_status,"mandate_date" => date('Y-m-d h:i:s',strtotime($EMandateDate)),"Registrationmode" => $Registrationmode, "EMandateFailureReason" => $EMandateFailureReason,"MandateLink" => $MandateLink];
					
					$this->db->insert("emandate_data", $arr);
				}
				
				if($mandate_status == 'Success'){
					$this->external_obj_api->send_message($query['lead_id'],'success');
				}
				
				if($mandate_status == 'Fail'){
					$this->external_obj_api->send_message($query['lead_id'],'fail');
				}
				
				if($paymentMode == 'PP' && ($Registrationmode == 'SAD' || $Registrationmode == 'EMI' || $Registrationmode == 'UPI')){
					$this->external_obj_api->send_message($query['lead_id'],'SAD_EMI_one');
					$this->external_obj_api->send_message($query['lead_id'],'SAD_EMI_two');
				}
				
			}
			
			if(isset($PaymentStatus) && $PaymentStatus == 'PI'){
				$check_pg = $this->external_obj_api->real_pg_check($query['lead_id']);
				if($check_pg){
					redirect(base_url("payment_success_view_call_axis/".$emp_id_encrypt));
				}else{
					
					$arr_update = ["is_payment_initiated" => 1];
					
					$this->db->where("lead_id",$query['lead_id']);
					$this->db->update("employee_details",$arr_update);
					
					echo "Response on payment status is pending. Post payment confirmation, proposal will be initiated. Thanks !!";
					exit;
				}
			}
			
			$proposal_id = $ids[0];
			
			$payment_data = $this->db->query("select payment_status,TxStatus,txndate from payment_details where proposal_id='$proposal_id'")->row_array();
			
			
			if($payment_data['TxStatus'] == 'success'){
			
				$data_res = $this->obj_api->policy_creation_call($query['lead_id']);
				
				if($data_res['Status'] == 'Success'){
					
					$data_policy = $this->db->query("select GROUP_CONCAT(DISTINCT(certificate_number)) certificate_number from api_proposal_response where emp_id='$emp_id' GROUP BY emp_id")->row_array();
					
					if($data_policy){
						
						$data['proposal_no'] = $query['proposal_no'];
						$data['lead_id'] = $query['lead_id'];
						$data['txndate'] = $payment_data['txndate'];
						
						$MandateLink_data = $this->db->query("select MandateLink,Registrationmode from emandate_data where lead_id = '".$query['lead_id']."'")->row_array();
						
						$this->load->telesales_template("thankyou",compact('data_policy','data','MandateLink_data'));
					}
					
				}else{
					
					redirect(base_url('tls_payment_error_view'));
					
				}
			
			}else{	
				 redirect(base_url('tls_payment_error_view'));
			}
			
			
		}else{
			
			echo "Error in data";
	
		}
		
		
	}
	

	
	

/* cron */
public function tele_fail_policy_create($check)
{

  if($check == 2){
//	echo "8 clock cron pending";exit;
	
	$query_r = $this
	->db
	->query("SELECT ed.lead_id,ed.emp_id,ed.product_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,proposal AS p,ghi_quick_quote_response AS g WHERE epd.product_name = mpst.id AND p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND g.`status` = 'success' AND p.status = 'Payment Received' AND ed.product_id in('R06','T01','T03') AND date(p.created_date) = date(now())")->result_array();
	
	if($query_r)
	{
		foreach($query_r as $val_r){
			
			$where_arr = ["emp_id"=>$val_r['emp_id'],"status"=>"Payment Received"];
				$arr = ["count" => 2];
				$this->db->where($where_arr);
				$this->db->update("proposal",$arr);
			//echo $val_r['emp_id'];exit;	
			$check_result = $this->obj_api->policy_creation_call($val_r['lead_id'],1);
					
			$request_arr = ["lead_id" => $val_r['lead_id'], "req" => json_encode($check_result),"res" => json_encode($check_result) ,"product_id"=> $val_r['product_id'], "type"=>"8clock_cron"];
				$dataArray['tablename'] = 'logs_docs'; 
				$dataArray['data'] = $request_arr; 
				$this->Logs_m->insertLogs($dataArray);

			//echo $check_result['Status']."hii".$val_r['lead_id'];
		}
	}

}else if($check == 1){

   /*
   // till 2020-11-17 (for old PG real pg status check)
   $query = $this
	->db
	->query("SELECT ed.lead_id,ed.emp_id,ed.email,ed.mob_no,p.premium,g.QuotationNumber,mpst.payu_info_url,ed.product_id,pt.txt_id,pt.pg_type,pt.id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,proposal AS p,ghi_quick_quote_response AS g,payment_txt_ids as pt WHERE epd.product_name = mpst.id AND p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND ed.lead_id = pt.lead_id AND g.`status` = 'success' AND p.status IN('Payment Pending','Rejected')  AND ed.product_id in('R06','T01','T03') AND date(p.created_date) >= '2020-10-06' AND date(p.created_date) <= '2020-11-17' AND pt.cron_count < 2  limit 15")->result_array();

	if($query)
	{
		
		foreach($query as $val1){
			
			$this->db->query("UPDATE payment_txt_ids SET cron_count = cron_count + 1 WHERE id =".$val1['id']);

			if($val1['pg_type'] == 'Razorpay'){
				
			// Decleration for Razor Pay Key Id, Key Secret & Currency Type
			$key_id = RAZOR_KEY_ID;
			$key_secret = RAZOR_KEY_SECRET;
			$razcurrency = PAYMENTGATEWAY_CURRENCY;
			$razcheckoutmethod = PAYMENTGATEWAY_CHECKOUT_METHOD_AUTOMATIC; 
			
			$api = new Api($key_id, $key_secret);
			$payment_obj = $api->order->payments($val1['txt_id']);
			
			$payment = (array)$payment_obj;
			
					   
			if(!empty($payment_obj['items'])){
				
				foreach ($payment_obj['items'] as $value){
					if($value['status']=='captured'){
						$request_arr = ["lead_id" => $val1['lead_id'],"req" => $val1['txt_id'], "res" => json_encode($payment), "type"=>"pg_real_success_cron"];
						$this->db->insert("logs_docs",$request_arr);

						$arr = ["payment_status" => "No Error","premium_amount" => ($value['amount']/100),"payment_type" => $value['method'],"pgRespCode" => "","merchantTxnId" => $value['order_id'],"SourceTxnId" => $value['order_id'],"txndate" => date('m/d/Y h:i A', $value['created_at']),"TxRefNo" => $value['id'],"TxStatus"=>"success","bank_name"=>$value['bank'],"json_quote_payment"=>json_encode($payment)];
				
						$proposal_ids = $this->db->query("select id as proposal_id from proposal where emp_id='".$val1['emp_id']."'")->row_array();
						
						$this->db->where("proposal_id",$proposal_ids['proposal_id']);
						$this->db->update("payment_details",$arr);
						
						$check_result = $this->obj_api->policy_creation_call($val1['lead_id']);
						//echo $check_result['Status']."hii".$val1['lead_id'];								

					}else{
						
						$request_arr = ["lead_id" => $val1['lead_id'],"req" => $val1['txt_id'], "res" => json_encode($payment), "type"=>"pg_real_fail_cron"];
						$this->db->insert("logs_docs",$request_arr);
						
					}
				}
			
			}
			
			}
				
		}
		
	}*/
	
	// after 2020-11-17 (for new PG real pg status check)
		//echo 'test9';	
	$query1 = $this->db->query("SELECT ed.lead_id,pt.id FROM employee_details as ed,proposal AS p,payment_txt_ids as pt WHERE ed.emp_id=p.emp_id AND ed.lead_id = pt.lead_id AND p.status IN('Payment Pending','Rejected') AND ed.product_id in('R06','T01','T03') AND pt.pg_type = 'New' AND pt.cron_count < 2  limit 15")->result_array();
	
	//echo 'test0';exit;
	if($query1)
	{
	//echo 'test1';exit;	
		foreach($query1 as $val1){
			
			$this->db->query("UPDATE payment_txt_ids SET cron_count = cron_count + 1 WHERE id =".$val1['id']);
						
				$check_pg = $this->external_obj_api->real_pg_check($val1['lead_id']);
						
				if($check_pg){
					$check_result = $this->obj_api->policy_creation_call($val1['lead_id'],1);
				}
		}
		
	}else{
	//echo 'test123';exit;
	$query_r = $this
	->db
	->query("SELECT ed.lead_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,proposal AS p,ghi_quick_quote_response AS g WHERE epd.product_name = mpst.id AND p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND g.`status` = 'success' AND  p.count < 5 AND p.status = 'Payment Received' AND ed.product_id  in('R06','T01','T03') AND date(p.created_date) = date(now()) limit 5")->result_array();
       //	echo $this->db->last_query();	
	if($query_r)
	{
		foreach($query_r as $val_r){
			//echo $val_r['lead_id'];exit;
			$check_result = $this->obj_api->policy_creation_call($val_r['lead_id'],1);
			//echo $check_result['Status']."hii".$val_r['lead_id'];
		}
	}

	//echo "cron cases finished";
	}
  }
	
}



	
}


