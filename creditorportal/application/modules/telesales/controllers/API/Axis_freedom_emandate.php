<?php if (!defined('BASEPATH'))
{
	exit('No direct script access allowed');
}

class Axis_freedom_emandate extends CI_Controller
{
    public $algoMethod;
    public $hashMethod;
    public $hash_key;
	public $encrypt_key;
	
	function __construct()
	{
		parent::__construct();	
		
		$this->algoMethod = 'aes-128-ecb';
        $this->hashMethod = 'sha256';
        $this->hash_key = 'axis';
        $this->encrypt_key = 'axisbank12345678';
		
		$this->load->model("Logs_m");
		
		//echo encrypt_decrypt_password(8453);

		// ini_set('display_errors', 1);
		// ini_set('display_startup_errors', 1);
		// error_reporting(E_ALL);
	}
	

	public function emandate_view_call($emp_id_encrypt) {
		
		// $file_path =  APPPATH.'uploads/GPAfail.xlsx';
		// $inputFileName = $file_path;

		// $this->load->library("excel");
		// $config1   =  [
		// 'filename'    => $inputFileName,              // prove any custom name here
		// 'use_sheet_name_as_key' => true,               // this will consider every first index from an associative array as main headings to the table
		// 'use_first_index'         => true, // if true then it will set every key as sheet name for appropriate sheet
		// ];
		// $sheetdata = [];
		// $sheetdata = Excel::import($inputFileName, $config1); 

		// if(!is_array($sheetdata))
		// {
			// $get_data = array('errorCode' => '1', 'msg' => $sheetdata);

			// $flag = 0;
		// }

		// $temp = 0;
		// if(!empty($sheetdata))
		// {
			// $arr = array();
			// $y = array_keys($sheetdata);
			// foreach($y as $value)
			// {
				// foreach($sheetdata[$value] as $val)
				// {
				
					// if(!empty($val))
					// {
						// $sheetdatas = array_filter($val);
						// if(!empty($sheetdatas))
						// {
							// $test[] = trim($sheetdatas['A']);
							
						// }
					// }
				// }
			// }
		// }
		// echo count($test);
		// echo $ab = implode(",", $test);
		// exit;
		
		
	/*  $file_path =  APPPATH.'uploads/Duplicatepolicyupdated.xlsx';
		$inputFileName = $file_path;
 
		$this->load->library("excel");
		$config1   =  [
		'filename'    => $inputFileName,              // prove any custom name here
		'use_sheet_name_as_key' => true,               // this will consider every first index from an associative array as main headings to the table
		'use_first_index'         => true, // if true then it will set every key as sheet name for appropriate sheet
		];
		$sheetdata = [];
		$sheetdata = Excel::import($inputFileName, $config1); 

		if(!is_array($sheetdata))
		{
			$get_data = array('errorCode' => '1', 'msg' => $sheetdata);

			$flag = 0;
		}

		$temp = 0;
		if(!empty($sheetdata))
		{
			$arr = array();
			$y = array_keys($sheetdata);
			foreach($y as $value)
			{
				foreach($sheetdata[$value] as $val)
				{
				
					if(!empty($val))
					{
						$sheetdatas = array_filter($val);
						if(!empty($sheetdatas))
						{
							if(trim($sheetdatas['F']) == 'R07'){
								
								$query = $this
								->db
								->query("select ed.lead_id,apr.certificate_number from employee_details as ed,proposal as p,api_proposal_response as apr where ed.emp_id = p.emp_id and p.proposal_no = apr.proposal_no_lead and p.policy_detail_id in(454) and ed.lead_id = '".trim($sheetdatas['A'])."' and apr.certificate_number != '".trim($sheetdatas['G'])."'")->row_array();
															
								if($query){
									//print_pre($this->db->last_query());
									$queryn = $this
									->db
									->query("SELECT ed.lead_id,ed.emp_id,p.proposal_no,p.policy_detail_id FROM employee_details as ed,proposal AS p WHERE ed.emp_id=p.emp_id AND p.status in('Success') and p.policy_detail_id = '454' and ed.lead_id = '".trim($sheetdatas['A'])."' ")->row_array();
									
									if($queryn){
										
										$arr = array(
											"certificate_number" => trim($sheetdatas['G'])
										);
										
										$where_arr = ["emp_id"=>$queryn['emp_id'],"proposal_no_lead"=>$queryn['proposal_no']];
										
										$this->db->where($where_arr);
										$this->db->update("api_proposal_response",$arr);
										
										print_pre($this->db->last_query());
										
										
										$request_arr = ["lead_id" => $queryn['lead_id'],"req" => json_encode($arr), "res" => json_encode($where_arr) ,"product_id"=> "R07", "type"=>"update_certificate_10_10_2020"];
										
										$this->db->insert('logs_docs', $request_arr);
										
									} 
									
									
									
								}
								
							}
							
						}
					}
				}
			}
		}
		echo 22;
		exit; */
		
		
		$emp_id = encrypt_decrypt_password($emp_id_encrypt,'D');
		$this->session->set_userdata('emp_id', $emp_id);
		if($emp_id){
			
		$data = $this->db->query("select ed.lead_id,ed.emp_id,ed.emp_firstname,ed.emp_lastname,ed.json_qote,sum(p.premium) as total_amt from employee_details as ed,proposal as p where ed.emp_id = p.emp_id and ed.emp_id = '$emp_id' group by p.emp_id")->row_array();
		
		if($data){
			
			$check_status = $this->db->query("select emd.status from emandate_data as emd where emd.status in('Emandate Received','Success') and emd.lead_id = ".$data['lead_id']);
		
			if($check_status){
				$check_status = $check_status->row_array();
				$data['emadate_status'] = $check_status['status'];
			}
			//print_r($data);
			$this->load->employee_template_api('emandate_view',compact('data'));
		}
		
		}
	}
	
	public function emandate_status_check() {
		$val = false;
		$emp_id = $this->session->userdata('emp_id');
		if($emp_id){
		
		$data = $this->db->query("select ed.emp_id,ed.lead_id from employee_details as ed,emandate_data as emd where ed.lead_id = emd.lead_id and emd.status in('Emandate Received','Success') and ed.emp_id = '$emp_id'")->row_array();
		
		if($data){
			$val = true;
		}
		echo json_encode($val);
		}
	}

	
	public function emandate_request_post() {
		
		$emp_id = $this->session->userdata('emp_id');
		
		$query_check = $this->db->query("select ed.emp_id,ed.lead_id,p.created_date,ed.email,ed.mob_no,ed.emp_firstname,ed.product_id,sum(p.premium) as total_amt from employee_details as ed,proposal as p where ed.emp_id = p.emp_id and ed.emp_id = '$emp_id' group by p.emp_id")->row_array();
		
		
		if($query_check){
		$amt = 1.5 * $query_check['total_amt'];
		
		$ramdom_number = $this->getRandomUniqueNumber(8);
		
		$r = array('CID' =>'5507' ,
					'RID' => $ramdom_number,
					'CRN' => $ramdom_number,
					'AMT' => round($amt,2),
					'VER' => '1.0' ,
					'TYP' => 'TEST',  
					'CNY' => 'INR', 
					'RTU' => base_url('emandate_enquiry_response'),
					'PPI' => 'ANNUALLY|914010009305862|'.date("m/d/Y").'|'.date("m/d/Y", strtotime('+79 years')).'|'.round($amt,2),
					'RE1' => 'MN',  
					'RE2' => '', 
					'RE3' => '', 
					'RE4' => '',  
					'RE5' => '', 
					);
					
		$hash_str = $r['CID'].$r['RID'].$r['CRN'].$r['AMT'].$this->hash_key;
		$CKS_value = hash($this->hashMethod, $hash_str);
		
		$r['CKS'] = $CKS_value;
		
		$i = '';
		$numItems = count($r);
		$j = 0;
		foreach ($r as $key => $value){
		
			if(++$j === $numItems)
				$i .= $key.'='.$value;
			else
				$i .= $key.'='.$value.'&';

		}

		$plaintext = $i;
		
		$encrypted = openssl_encrypt($plaintext, $this->algoMethod, $this->encrypt_key, 0);
		$decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);

		$data = array('i'=>$encrypted);
		$url = 'https://uat-etendering.axisbank.co.in/easypay2.0/frontend/index.php/api/payment';
		
		  $query = $this->db->query("select * from emandate_data where lead_id=".$query_check['lead_id'])->row_array();
			
			if($query > 0){
				
				$arr = ["RID" => $r['RID'],"CRN" => $r['CRN'],"status" => "Emandate Pending"];
				
				$this->db->where("lead_id",$query_check['lead_id']);
				$this->db->update("emandate_data",$arr);
			}else{
				
				$arr = ["lead_id" => $query_check['lead_id'],"RID" => $r['RID'],"CRN" => $r['CRN'],"status" => "Emandate Pending"];
				
				$this->db->insert("emandate_data", $arr);
			}
		  
			
		$request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($data), "res" => json_encode($decrypted),"product_id"=> $query_check['product_id'], "type"=>"emandate_request_post"];
		
		$dataArray['tablename'] = 'logs_docs'; 
		$dataArray['data'] = $request_arr; 
		$this->Logs_m->insertLogs($dataArray);
	  
		$this->load->view('Retail/payment_hidden_submit',compact('url','data'));

		
	}

	}
	
	public function emandate_enquiry_response() {
		$emp_id = $this->session->userdata('emp_id');
		$encrypted = $_GET['i'];
		
		$query_check = $this->db->query("select ed.emp_id,ed.lead_id,ed.product_id from employee_details as ed,proposal as p,emandate_data as emd where ed.emp_id = p.emp_id and ed.lead_id = emd.lead_id and p.EasyPay_PayU_status = 0 and ed.product_id != 'R06' and emd.status = 'Emandate Pending' and ed.emp_id = '$emp_id' group by p.emp_id")->row_array();
		
		if($query_check && !empty($encrypted)){
			
		$lead_id = $query_check['lead_id'];
		
		$decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);
		
		$request_arr = ["lead_id" => $lead_id, "req" => json_encode($encrypted), "res" => $decrypted,"product_id"=> $query_check['product_id'], "type"=>"emandate_response_post"];
		
		$dataArray['tablename'] = 'logs_docs'; 
		$dataArray['data'] = $request_arr; 
		$this->Logs_m->insertLogs($dataArray);
		
		$arr_data = explode('&',$decrypted);
		$data = [];
		$data['lead_id'] = $lead_id;
		$data['product_id'] = $query_check['product_id'];
		foreach ($arr_data as $key => $value){
			$covert_data = explode("=", $value); 
            $data[$covert_data[0]] = $covert_data[1];
		}
		
		$arr = ["BRN" => $data['BRN'],"TRN" => $data['TRN'],"status" => "Emandate Received"];
				
		$this->db->where("lead_id",$lead_id);
		$this->db->update("emandate_data",$arr);
		
		//check emadate status - enquiry api
		$this->enquiry_check($data);
		
		if($data){
			$this->load->employee_template_api('emandate_response_view',compact('data'));
		}
		
		}
	}
	
	public function emandate_cron_check() {
		
		$query = $this->db->query("select ed.lead_id,emd.RID,emd.CRN,emd.BRN,ed.product_id from employee_details as ed,proposal as p,emandate_data as emd where ed.emp_id = p.emp_id and ed.lead_id = emd.lead_id and p.EasyPay_PayU_status = 0 and emd.status = 'Emandate Received' group by p.emp_id")->result_array();
		
		if($query)
		{
			foreach($query as $val){
				//check emadate status - enquiry api
				$this->enquiry_check($val);
			}
		}
		
		
	}
	
	function enquiry_check($data){
		
		$r = array('CID' =>'5507' ,
					'RID' => $data['RID'],
					'CRN' => $data['CRN'],
					'BRN' => $data['BRN'],
					'VER' => '1.0' ,
					'TYP' => 'TEST'
					);
					
		$hash_str = $r['CID'].$r['RID'].$r['CRN'].$this->hash_key;
		$CKS_value = hash($this->hashMethod, $hash_str);
		
		$r['CKS'] = $CKS_value;
		
		$i = '';
		$numItems = count($r);
		$j = 0;
		foreach ($r as $key => $value){
		
			if(++$j === $numItems)
				$i .= $key.'='.$value;
			else
				$i .= $key.'='.$value.'&';

		}

		$encrypted_req = openssl_encrypt($i, $this->algoMethod, $this->encrypt_key, 0);
		$decrypted_req = openssl_decrypt($encrypted_req, $this->algoMethod, $this->encrypt_key, 0);

		$url = 'https://uat-etendering.axisbank.co.in/easypay2.0/frontend/index.php/api/enquiry?i='.$encrypted_req;
				
		$curl = curl_init();
	
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: application/json",
		   
		  ),
		));

		$result = curl_exec($curl);
		
		curl_close($curl);
		
		if($result){
			
			$decrypted_res = openssl_decrypt($result, $this->algoMethod, $this->encrypt_key, 0);
			
			$arr_datan = explode('&',$decrypted_res);
			$data_n = [];
			foreach ($arr_datan as $key => $value){
				$covert_data = explode("=", $value); 
				$data_n[$covert_data[0]] = $covert_data[1];
			}
			
			if($data_n['RMK'] == 'Success'){
				
			$arr = ["TRN" => $data_n['TRN'],"status" => "Success"];	
			$this->db->where("lead_id",$data['lead_id']);
			$this->db->update("emandate_data",$arr);
			
			//send email n sms to customer(success)
			$this->send_message($data['lead_id'],'success');
			
			}else{
			//send email n sms to customer(fail)
			$this->send_message($data['lead_id'],'fail');
			}
			
		}
		
		$request_arr = ["lead_id" => $data['lead_id'], "req" => json_encode($decrypted_req),"res" => json_encode($decrypted_res),"product_id"=> $data['product_id'], "type"=>"emandate_enquiry_post"];
		
		$dataArray['tablename'] = 'logs_docs'; 
		$dataArray['data'] = $request_arr; 
		$this->Logs_m->insertLogs($dataArray);
		
		
	}
	
	function send_message($lead_id,$type){
		
		$query_check = $this->db->query("SELECT ed.emp_id,ed.lead_id,ed.email,ed.mob_no,ed.emp_firstname,mpst.product_code,mpst.click_pss_url,mpst.product_name,ed.json_qote FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.lead_id=".$lead_id)->row_array();
		
		if($query_check){
			
			$json_data = json_decode($query_check['json_qote'],true);
		
			$senderID = 1;
			$full_name = $query_check['emp_firstname'];
			if(strlen($full_name) > 30){
				$full_name = substr($full_name, 0, 30);
			}

			$AlertV1 = $full_name;
			$AlertV2 = $query_check['product_name'];
			$AlertV3 = 'plan name';
			$AlertV4 = 'policy number';
			$AlertV5 = '';
			$AlertV6 = '';
			$AlertV7 = '';
			$AlertV8 = '';
			
			$alertID = '';
			
			if($type == 'success'){
				$alertID = 'A1324';
				$AlertV5 = ($json_data['AXISBANKACCOUNT'] == 'Y')?'Axis Bank':'Other';
				$AlertV6 =  substr($json_data['ACCOUNTNUMBER'], -4);
			}
			
			if($type == 'fail'){
				$alertID = 'A1325';
				$AlertV5 = 'reason';
				$AlertV6 = ($query_check['AXISBANKACCOUNT'] == 'Y')?'Axis Bank':'Other';
				$AlertV7 = substr($json_data['ACCOUNTNUMBER'], -4);
				$AlertV8 = 'link';
			}
				
			
			
			$parameters =[
				"RTdetails" => [
			   
					"PolicyID" => '',
					"AppNo" => 'HD100017934',
					"alertID" => $alertID,
					"channel_ID" => 'Axis Freedom Plus',
					"Req_Id" => 1,
					"field1" => '',
					"field2" => '',
					"field3" => '',
					"Alert_Mode" => 3,
					"Alertdata" => 
						[
							"mobileno" => substr(trim($query_check['mob_no']), -10),
							"emailId" => $query_check['email'],
							"AlertV1" => $AlertV1,
							"AlertV2" => $AlertV2,
							"AlertV3" => $AlertV3,
							"AlertV4" => $AlertV4,
							"AlertV5" => $AlertV5,
							"AlertV6" => $AlertV6,
							"AlertV7" => $AlertV7,
							"AlertV8" => $AlertV8,
							
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
			
			$request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($parameters),"res" => json_encode($response) ,"product_id"=> $query_check['product_code'], "type"=>"sms_logs_emandate_".trim($type)];
			
			$dataArray['tablename'] = 'logs_docs'; 
			$dataArray['data'] = $request_arr; 
			$this->Logs_m->insertLogs($dataArray);
	
	  }
	}
	
	function getRandomUniqueNumber($length = 8) {
        $randNumberLen = $length;
        $numberset = time() . 11111;
        if (strlen($numberset) == $randNumberLen) {
            return $numberset;
        } else {
            if (strlen($numberset) < $randNumberLen) {
                //add if length not match
                $addRandom = $randNumberLen - strlen($numberset);
                $i = 1;
                $ramdom_number = mt_rand(1, 9);
                do {
                    $ramdom_number .= mt_rand(0, 9);
                } while (++$i < $addRandom);
                $numberset .= $ramdom_number;
            } else {
                //substract if length not match
                $substractRandom = strlen($numberset) - $randNumberLen;
                $numberset = substr($numberset, 0, -$substractRandom);
            }

            return $numberset;
        }
    }
	

	
	
	
}
?>