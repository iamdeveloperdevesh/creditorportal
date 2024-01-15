<?php header('Access-Control-Allow-Origin: *');
if (!defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Payments extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		//checklogin();
		$this->algoMethod = 'aes-128-ecb';
        $this->hashMethod = 'SHA512';
        $this->hash_key = 'razorpay';
		$this->encrypt_key = 'axisbank12345678';
		$this->RolePermission = getRolePermissions();
	}

	public function doPayment($lead_id){
		$data = $this->db->query("SELECT * FROM proposal_payment_details WHERE lead_id='$lead_id' AND payment_status != 'Success'")->row();

		$masterCustomer = $this->db->query("SELECT * FROM master_customer WHERE lead_id='$lead_id' ORDER BY customer_id ASC")->row();
		
		if(empty($data)){
			echo json_encode([
				"status" => "Error", 
				"message" => "Not found any payment details"
			]);
		}

		$customer_id_encrypt = $this->encrypt_decrypt($masterCustomer->customer_id,"E");

		$CKS_data = "AX|AXATGRP|PP|".base_url('payments/return/'.$customer_id_encrypt)."|CustomerId|".$masterCustomer->customer_id."|".$masterCustomer->first_name." ".$masterCustomer->last_name."|".$masterCustomer->email_id."|".substr(trim($masterCustomer->mobile_no), -10)."|".round($data->premium_with_tax,2)."|Axis Telesales|".$this->hash_key;

		$CKS_value = hash($this->hashMethod, $CKS_data);

		$dataPost = [
			"signature"=> $CKS_value,
			"Source"=> "AX",
			"Vertical"=> "AXATGRP",
			"PaymentMode"=> "PP",
			"ReturnURL"=> base_url('payments/return/'.$customer_id_encrypt),
			"UniqueIdentifier" => "CustomerId",
			"UniqueIdentifierValue" => $masterCustomer->customer_id,
			"CustomerName"=> $masterCustomer->first_name." ".$masterCustomer->last_name,
			"Email"=> $masterCustomer->email_id,
			"PhoneNo"=> substr(trim($masterCustomer->mobile_no), -10),
			"FinalPremium"=> round($data->premium_with_tax,2),
			"ProductInfo"=> "Axis Telesales",
			"MandateInfo"=>[
				"ApplicationNo"=> $masterCustomer->lead_id,
				"AccountHolderName"=> $masterCustomer->first_name." ".$masterCustomer->last_name,
				"BankName"=> "Axis Bank",
				"AccountNumber"=> null,
				"AccountType"=> null,
				"BankBranchName"=> null,
				"MICRNo"=> null,
				"IFSC_Code"=> null,
				"Frequency"=> "ANNUALLY"
			]
		];

		$data_string = json_encode($dataPost);
		$encrypted = openssl_encrypt($data_string, $this->algoMethod, $this->encrypt_key, 0);
		$decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);

		$url = "https://pg_uat.adityabirlahealth.com/pgmandate/service/home/sourcelanding";
		$encryptedData = array('REQUEST'=>$encrypted);
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $url);
		curl_setopt($c, CURLOPT_POST, 0);
		curl_setopt($c, CURLOPT_POSTFIELDS, $encryptedData);
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
		$result = curl_exec($c);
		curl_close($c);
		$result = json_decode($result, true);
		
		$request_arr = [
			"lead_id" => $lead_id,
			"req" => "ecrypt-".json_encode($encryptedData)."decrypt-".$decrypted,
			"res" => json_encode($result),
			//"product_id" => $product_id, 
			"type"=>"payment_request_post", 
			//"proposal_policy_id"=> $proposal_policy_id
		];
		$this->db->insert("logs_docs",$request_arr);
		$insert_id = $this->db->insert_id();
		
		if($result && $result['Status']){
			//echo "WELCOME To ABHI";
			redirect($result['PaymentLink']);
		}
		else
		{
			if($result['ErrorList'][0]['ErrorCode'] == 'E005'){
				echo json_encode([
					"status" => "Error", 
					"message" => $result['ErrorList'][0]['Message'],
				]);
				//echo $result['ErrorList'][0]['Message'];
				//redirect(base_url('payments/doPayment/'.$lead_id));
				
			}else{
				echo json_encode([
					"status" => "Error", 
					"message" => $result['ErrorList'][0]['Message'],
				]);
			}
		}
	}

	public function return($customer_id_encrypt){
		$customer_id = $this->encrypt_decrypt($customer_id_encrypt,"D");
		$TxStatus = $TxMsg = '';
		$encrypted = $this->input->post('RESPONSE');
		$lead_id = $this->apimodel->geLeadId($customer_id);
		if($encrypted){
			$decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);
			$post_data = json_decode($decrypted,true);
			
			$customer_id = isset($post_data["UniqueIdentifierValue"]) ? $post_data["UniqueIdentifierValue"] : "";
			$txnDateTime = isset($post_data["txnDateTime"]) ? $post_data["txnDateTime"] : "";
			$TxRefNo = isset($post_data["TxRefNo"]) ? $post_data["TxRefNo"] : "";
			$email = isset($post_data["email"]) ? $post_data["email"] : "";
			$amount = isset($post_data["amount"]) ? $post_data["amount"] : "";
			$OrderId = isset($post_data["OrderId"]) ? $post_data["OrderId"] : "";
			$EMandateDate = isset($post_data["EMandateDate"]) ? $post_data["EMandateDate"] : "";
			
			extract($post_data);

			if($TxStatus && ($TxMsg == 'Payment Received' || $TxMsg == 'success') && $PaymentStatus == 'PR'){
				$TxStatus = "Success";
				$TxMsg = "No Error";
			}else{
				$TxStatus = "Failure";
				$TxMsg = $TxMsg;
			}

			extract($post_data);

			$proposalPaymentDetails = $this->db->query("SELECT proposal_payment_id FROM proposal_payment_details WHERE lead_id= '$lead_id'")->row();
			if($proposalPaymentDetails > 0){
				$request_arr = [
					"payment_status"=>$TxStatus,
					"transaction_date"=>date('Y-m-d H:i:s',strtotime($txnDateTime)),
					"transaction_number"=>$TxRefNo,
					"payment_date"=>date("Y-m-d H:i:s"),
				];

				$this->db->where("proposal_payment_id",$proposalPaymentDetails->proposal_payment_id);
				$this->db->where("lead_id",$lead_id);
				$this->db->update("proposal_payment_details",$request_arr);
				$insert_id = $proposalPaymentDetails->proposal_payment_id;
			}

			//Do full quote
			if($TxStatus=="Success"){
				$updateProposalPolicyStatus = $this->apimodel->updateProposalPolicyStatus("lead_id", $lead_id, "Payment-Done");
				if($updateProposalPolicyStatus){
					$doFullQuote = $this->apimodel->doFullQuote($lead_id);
					if($doFullQuote["Status"]=="Success"){
						$return = [
							"status" => "Success",
							"message" => "Full quote sucessfully done."
						];
					}else{
						$return = [
							"status" => $doFullQuote["Status"],
							"data" => $doFullQuote
						];
					}
				}
				else
				{
					$return = [
						"status" => "Fail",
						"message" => "Can not update full quote status."
					];
				}
			}
			else
			{
				$return = [
					"status" => $TxStatus,
					"message" => $TxMsg
				];
			}	
		}
		if($return["status"] == "Success"){
			redirect(base_url('payments/thankyou/'.$lead_id));
		}else{
			redirect(base_url('policyproposal/tryagain/'.$lead_id));
		}
		//print_r($return);
		//exit;
		//return $return;
	}

	function thankYou($lead_id)
	{
		$data = array();
		$data['lead_id'] = $lead_id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['status'] = "Full-Quote-Done";
		$result['lead_id'] = $lead_id;
		$result['geProposalPolicyDetails'] = json_decode(curlFunction(SERVICE_URL . '/api2/geProposalPolicyDetails', $data));
		//print_r($result);
		//exit;
		$this->load->view('template/header.php');
		$this->load->view('payments/thankYou',$result);
		$this->load->view('template/footer.php');
	}

	/*
	Array ( [signature] => 6543E400F58140568B6572881B1D7B46F26F0DBF97C61548C19328FECDA18466498C426A9849EC7884EFF54C61DDD42AFF23E714F892D542E2A1F9C3FBD26713 [amount] => 500000.00 [email] => amol.koli@fyntune.com [mobileNo] => 9967890978 [paymentMode] => PP [UniqueIdentifierValue] => 58 [TxRefNo] => pay_GKnXyU3Mkyy9eO [TxMsg] => success [TxStatus] => 1 [txnDateTime] => 2021-01-03 15:47:58.447 [productinfo] => Axis Telesales [MandateLink] => https://pg_uat.adityabirlahealth.com/pgmandate/service/mandate?rdnno=e8ddbc2b-d37f-4d18-b94b-f5680f90673d [PaymentStatus] => PR [PaymentStatusDesc] => Payment Received [EMandateRefno] => [EMandateStatus] => [EMandateStatusDesc] => [EMandateDate] => 2021-01-03 15:47:43.773 [EMandateFailureReason] => [Registrationmode] => SAD [CustomerId] => [OrderId] => order_GKnXsbaDFW25S0 )
	*/

	function encrypt_decrypt($simple_string,$method){
		$ciphering = "AES-128-CTR";
		$iv_length = openssl_cipher_iv_length($ciphering); 
		$options = 0; 
		$iv = '1234567891011121'; 
		$key = "Creditor"; 
		if($method == "E"){
			$string = openssl_encrypt($simple_string, $ciphering, $key, $options, $iv); 
		}else if($method == "D"){
			$string = openssl_decrypt ($simple_string, $ciphering, $key, $options, $iv);
		}
		return $string;
	}
}