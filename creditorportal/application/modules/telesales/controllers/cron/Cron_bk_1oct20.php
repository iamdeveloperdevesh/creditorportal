<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include(APPPATH.'razorpay_php/Razorpay.php');
use Razorpay\Api\Api;


class Cron extends CI_Controller {
     

    function __construct() {
        parent::__construct();
        $this->load->model('cron/cron_m');
		$this
            ->load
            ->model("Logs_m", "Logs_m", true);
    }
    	//changes sahil
	public function get_premium_from_policy($policy_detail_id,$sum_insure,$family_construct,$age)
   {
      
      
      $ew_status  = 1;
      $premium_value = '';
   
      $premium1 = $this->db
      ->select('*')
      ->from('master_broker_ic_relationship as fc')
      ->where('fc.policy_id', $policy_detail_id)
      ->get()
      ->row_array();	
      $family_construct1 = explode("+", $family_construct);
   
      if ($premium1['max_adult'] != 0 && 	$premium1['max_child'] == 0 && $family_construct1[1] != '')
      {
         $member_id = $family_construct1[0];
      }
      else
      {
         $member_id = $family_construct;
      }
      $check_gmc = $this->db
      ->select('*')
      ->from('employee_policy_detail as epd')
      ->join('master_policy_sub_type as mpst', "epd.policy_sub_type_id = mpst.policy_sub_type_id")
      ->where('epd.policy_detail_id', $policy_detail_id)
      ->get()
      ->row_array();
      if($check_gmc['suminsured_type'] == 'family_construct')	
      {
         
            $checks = $this->db->select("PremiumServiceTax,sum_insured,EW_PremiumServiceTax")
            ->from("family_construct_wise_si")
            ->where("sum_insured", $sum_insure)
            ->where("family_type", $member_id)
            ->where("policy_detail_id", $policy_detail_id)
            ->get()
            ->row_array();
            
            
            if($EW_status = 1)
            {
               $premium_value  = $checks['EW_PremiumServiceTax'];
            }
            else
            {
               $premium_value  = $checks['PremiumServiceTax'];
            }
            
            return $premium_value;
            
         
      }
      
      if($check_gmc['suminsured_type'] == 'family_construct_age'){
         $check = $this->db->select("age_group,PremiumServiceTax,sum_insured,EW_PremiumServiceTax")
      ->from("family_construct_age_wise_si")
      ->where("sum_insured",$sum_insure)
      ->where("family_type", $member_id)
      ->where("policy_detail_id", $policy_detail_id)
      ->get()
      ->result_array();
   
         foreach($check as $values){
      $min_max_age = explode("-",$values['age_group']);
      
      
      if($age >= $min_max_age[0] && $age <= $min_max_age[1]){
         
      if($EW_status = 1)
            {
               
               
               $premium_value  = $values['EW_PremiumServiceTax'];
            }
            else
            {
               
               
               $premium_value  = $values['PremiumServiceTax'];
            }
            return $premium_value;
            
      }
      }
      }
      
      if($check_gmc['suminsured_type'] == 'memberAge'){
         $check_age = $this->db->select("policy_age,premium_with_tax,sum_insured,EW_premium_with_tax")
      ->from("policy_creation_age")
      ->where("sum_insured",$sum_insure)
      ->where("policy_id", $policy_detail_id)
      ->get()
      ->result_array();
   
         foreach($check_age as $values_age){
   
      $min_max_age = explode("-",$values_age['policy_age']);
      
      
      if($age >= $min_max_age[0] && $age <= $min_max_age[1]){
         
      if($EW_status = 1)
            {
               
               
                $premium_value  = $values_age['EW_premium_with_tax'];
            }
            else
            {
               
               
                $premium_value  = $values_age['premium_with_tax'];
            }
         
            return $premium_value;
            
      }
      }
      }
   
   }
       function get_child_count($emp_id, $policy_detail_id) {
            $response = $this->db
            ->query('SELECT epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
               epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type"
               FROM 
               employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
               master_family_relation AS mfr
               WHERE epd.policy_detail_id = epm.policy_detail_id
               AND epm.family_relation_id = fr.family_relation_id
   
               AND fr.family_id = efd.family_id 
               AND efd.fr_id = mfr.fr_id
               and mfr.fr_id  IN ("2", "3")
               AND fr.emp_id = ' . $emp_id . '
               AND `epd`.`policy_detail_id` = ' . $policy_detail_id)->result_array();
            return ["count" => count($response)];
       }
   function get_adult_count($emp_id, $policy_detail_id) {
           $response = $this->db
           ->query('SELECT epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
               epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type"
               FROM 
               employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_details AS ed
               WHERE epd.policy_detail_id = epm.policy_detail_id
               AND epm.family_relation_id = fr.family_relation_id
               AND fr.family_id = 0
               AND fr.emp_id = ed.emp_id
               AND ed.emp_id = ' . $emp_id . '
               AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' UNION all SELECT epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
               epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type"
               FROM 
               employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
               master_family_relation AS mfr
               WHERE epd.policy_detail_id = epm.policy_detail_id
               AND epm.family_relation_id = fr.family_relation_id
               AND fr.family_id = efd.family_id 
               AND efd.fr_id = mfr.fr_id
               and mfr.fr_id NOT IN ("2", "3")
               AND fr.emp_id = ' . $emp_id . '
               AND `epd`.`policy_detail_id` = '.$policy_detail_id)->result_array();
            return ["count" => count($response)];
       }
   public function check_validations_adult_count($family_construct,$emp_id,$policy_detail_id)
    {
		
           if(!$family_construct)
      {
         return false;
      }      
      $data = explode("+",$family_construct);
      if($data[0]){
      preg_match_all('!\d+!', $data[0], $matches);
      //get total adult count
      $count = $this->get_adult_count($emp_id,$policy_detail_id);
      
   
      if($count['count'] != $matches[0][0]){
      return false;
      }
      }
      if($data[1]){
      preg_match_all('!\d+!', $data[1], $matches);
      //get total adult count
      $count = $this->get_child_count($emp_id,$policy_detail_id);
      // print_Pre($count);exit;
      if($count['count'] != $matches[0][0]){
      return false;
      }
      }
	  
      return true;
   
   }
   public function sahil(){
	   
	   $file_path =  APPPATH.'uploads/hbdatepatching.xlsx';
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
							
							if($sheetdatas['B'] == 'AXIS_D2C'){
							$test[] = trim($sheetdatas['A']);
							}else{
								continue;
							}
							continue;


  
   
   $lead_id = $sheetdatas['A'];
   $sheet['premium'] = $sheetdatas['E'];
   
   $emp_id = $this->db->select("emp_id")
                        ->from("employee_details")
                        ->where("lead_id", $lead_id)
                        ->get()->row_array();
						
	$emp_id = $emp_id['emp_id'];
	
	if(empty($emp_id)){
		continue;
	}
   $member_data = $this->db
                           ->query('SELECT epm.policy_mem_dob,epm.policy_mem_gender,epm.family_relation_id,epm.policy_mem_sum_insured,epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
               epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type"
               FROM 
               employee_policy_detail AS epd,proposal_member AS epm,family_relation AS fr,employee_details AS ed
               WHERE epd.policy_detail_id = epm.policy_detail_id
               AND epm.family_relation_id = fr.family_relation_id
               AND fr.family_id = 0
               AND fr.emp_id = ed.emp_id
               AND ed.emp_id = ' . $emp_id . '
               AND `epd`.`policy_detail_id` = ' . 454 . ' UNION all SELECT epm.policy_mem_dob,epm.policy_mem_gender,epm.family_relation_id,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
               epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type"
               FROM 
               employee_policy_detail AS epd,proposal_member AS epm,family_relation AS fr,employee_family_details AS efd,
               master_family_relation AS mfr
               WHERE epd.policy_detail_id = epm.policy_detail_id
               AND epm.family_relation_id = fr.family_relation_id
   
               AND fr.family_id = efd.family_id 
               AND efd.fr_id = mfr.fr_id
               and mfr.fr_id  IN ("1")
               AND fr.emp_id = ' . $emp_id . '
               AND `epd`.`policy_detail_id` = ' . 454)->result_array();
            $family_construct1 = explode("+", $member_data[0]['familyConstruct']);
            $member = $family_construct1[0];
            $gpa_array = [];
          for ($i = 0;$i < count($member_data);$i++)
                           {
                               if ($member_data[$i]['lastname'] == '')
                               {
                                   $last_names = '.';
                               }
                               else
                               {
                                   $last_names = $member_data[$i]['lastname'];
                               }
                        $premium_data = $this->get_premium_from_policy(455,$member_data[$i]['policy_mem_sum_insured'],$member,$member_data[$i]['age']);
                        
                        if($member == '2A')
                           {
                              $premium_data = $premium_data/2;
                           }
   
                               $member_arrays = ["policy_detail_id" => 455, "family_relation_id" => $member_data[$i]['family_relation_id'], "policy_mem_sum_insured" => $member_data[$i]['policy_mem_sum_insured'], "policy_mem_sum_premium" => $premium_data, "policy_mem_gender" => $member_data[$i]['policy_mem_gender'], "policy_mem_dob" => $member_data[$i]['policy_mem_dob'], "age" => $member_data[$i]['age'], "age_type" => $member_data[$i]['age_type'], "member_status" => "confirmed", "fr_id" => $member_data[$i]['fr_id'], "policy_member_first_name" => $member_data[$i]['firstname'], "policy_member_last_name" => $last_names, "familyConstruct" => $member];
                        
                        $check_member = $this->db->select("*")
                                       ->from("employee_policy_member")
                                       ->where("family_relation_id", $member_data[$i]['family_relation_id'])
                                       ->where("policy_detail_id", 455)
                                       ->get()->row_array();
									   
                        if(empty($check_member)){
                           $this
                                   ->db
                                   ->insert("employee_policy_member", $member_arrays);
                           $logs_array['data'] = ["type" => "gpa_patch_insert", "req" => json_encode($member_arrays) , "lead_id" => $lead_id, "product_id" => 'R07'];
                               $this
                                   ->Logs_m
                                   ->insertLogs($logs_array);
                           $gpa_array[] = 	$member_arrays;
                        }
						//temporary
						$gpa_array[] = 	$member_arrays;
                               
                           
                        
                               
                           }


							
                     
                     $check = $this
                   ->check_validations_adult_count($member, $emp_id, 455);
				  
                if (!$check)
               {
               $logs_array['data'] = ["type" => "gpa_patch_insert_error", "req" => '' , "lead_id" => $lead_id, "product_id" => 'R07'];
                               $this
                                   ->Logs_m
                                   ->insertLogs($logs_array);
                   CONTINUE;
                   
               }
               
                  $proposal_no = $this
                   ->db
                   ->select("*")
                   ->from("proposal_unique_number")
                   ->get()
                   ->row_array();
               //echo strtotime($date) ."==". strtotime($proposal_no['date']);exit;
               if (strtotime($date) == strtotime($proposal_no['date']))
               {
                   $number = ++$proposal_no['number'];
                   $array = ["number" => $number];
                   $this
                       ->db
                       ->where('id', $proposal_no['id']);
                   $this
                       ->db
                       ->update('proposal_unique_number', $array);
                   $propsal_number = "P-" . $number;
   
               }
               else
               {
                   $number = date('Ymd') . '0000';
                   $propsal_number = "P-" . $number;
                   $array = ["number" => $number, "id" => 1, "date" => date('Y-m-d') ];
                   $this
                       ->db
                       ->where('id', '1');
                   $this
                       ->db
                       ->delete('proposal_unique_number');
                   $this
                       ->db
                       ->insert('proposal_unique_number', $array);
               }
            
               $EasyPay_PayU_status = '';
               $employee_details_new_api = $this
               ->db
               ->where("emp_id", $emp_id)->get('employee_details')
               ->row_array();
			   
           $response_api = json_decode($employee_details_new_api['json_qote'], true);
           $branch_code = $response_api['branch_sol_id'];
		   if(empty($branch_code)){
			  $branch_code = $response_api['BRANCH_SOL_ID']; 
		   }
           $IMDCode = $this
               ->db
               ->where('BranchCode', $branch_code)
            ->get('master_imd')
               ->row_array() ['IMDCode'];
            
            //echo $this->db->last_query();
			
            $check_proposal = $this->db->select("*")
                                       ->from("proposal")
                                       ->where("emp_id", $emp_id)
                                       ->where("policy_detail_id", 455)
                                       ->get()->row_array();
									   
									  
            $check_proposal_ghi = $this->db->select("*")
                                       ->from("proposal")
                                       ->where("emp_id", $emp_id)
                                       ->where("policy_detail_id", 454)
                                       ->get()->row_array();	
									   
                                       
									   
               if(empty($check_proposal)){
                  $proposal_array = ["sum_insured" => $member_data[0]['policy_mem_sum_insured'],"proposal_no" => $propsal_number, "policy_detail_id" => 455, "product_id" => $check_proposal_ghi['product_id'], "created_by" => $emp_id, "status" => "Payment Pending", "branch_code" => $branch_code, "IMDCode" => $IMDCode, "created_date" => date('Y-m-d H:i:s') , "EasyPay_PayU_status" => $check_proposal_ghi['EasyPay_PayU_status'], "emp_id" => $emp_id];	
            
            
           // print_pre($proposal_array);exit;
            
            $this->db->insert("proposal", $proposal_array);
                  $proposal_id = $this
                           ->db
                           ->insert_id();
                  
               }else{
                   $proposal_update = [
                   "premium" => $sheet['premium'],
                   "count" => "10",
               ];
   
               $this->db->where(['id' => $check_proposal['id']])
                   ->update("proposal", $proposal_update);
               
			   
			   
               $proposal_id = $check_proposal['id'];
                  
               
   
               }
            
            
                    // print_pre($gpa_array);exit;
                     
                     foreach($gpa_array as $value){
                     
                     $member = $this
                               ->db
                               ->where("family_relation_id", $value["family_relation_id"])
                               ->where("policy_detail_id", $value["policy_detail_id"])
							   ->get("employee_policy_member")
                               ->row_array();
							  // echo $this->db->last_query();exit;
                           $member['proposal_id'] = $proposal_id;
                     
                     $check_member_proposal = $this->db->select("*")
                                       ->from("proposal_member")
                                       ->where("family_relation_id", $value['family_relation_id'])
                                       ->where("policy_detail_id", 455)
                                       ->get()->row_array();
									   //print_pre($member);exit;
                        if(empty($check_member_proposal)){
                           $this
                               ->db
                               ->insert("proposal_member", $member);
                           $logs_array['data'] = ["type" => "gpa_patch_insert_proposal", "req" => json_encode($member) , "lead_id" => $lead_id, "product_id" => 'R07'];
                               $this
                                   ->Logs_m
                                   ->insertLogs($logs_array);
                           
                        }
                      
                           
                     
                     
                     }
                      $this
                           ->db
                           ->where('id', $proposal_id);
                       $this
                           ->db
                           ->update('proposal', ["premium" => $sheet['premium']]);
                     
					 
					 //
					 
                     
                     $payment_details = $this->db->select("*")
                           ->from("payment_details")
                           ->where("proposal_id", $proposal_id)
                           ->get()->row_array();
						  
                     if(empty($payment_details)){
                        
                        $ghi_proposal_id  = $this->db->select("*")
                           ->from("proposal")
                           ->where("emp_id", $emp_id)
                           ->where("policy_detail_id", 454)
                           ->get()->row_array();
                        $payment_details_ghi = $this->db->select("*")
                           ->from("payment_details")
                           ->where("proposal_id", $ghi_proposal_id['id'])
                           ->get()->row_array();
						   
//print_pre($payment_details_ghi);exit;
$payment_details_ghi['proposal_id'] = $proposal_id;
unset($payment_details_ghi['payment_id']);
                     // $u_data = array(
         // 'bank_name' => $payment_details_ghi['bank_name'],
         // 'branch' => $payment_details_ghi['branch'],
         // 'account_no' => $payment_details_ghi['account_no'],
         // "ifscCode" => $payment_details_ghi['ifscCode'],
         // "bankCity" => $payment_details_ghi['bankCity'],
         // 'proposal_id' => $proposal_id,
         // 'cheque_no' => $payment_details_ghi['cheque_no'],
         // 'cheque_date' => $payment_details_ghi['cheque_date'],
         // 'cheque_type' => $payment_details_ghi['cheque_type'],
         // 'payment_type' => $payment_details_ghi['payment_type'],
         // 'TxStatus' => $payment_details_ghi['TxStatus'],
         // 'premium_amount' => $payment_details_ghi['premium_amount'],
         // 'TxRefNo' => $payment_details_ghi['TxRefNo'],
         // 'micr_no' => $payment_details_ghi['micr_no'],
         // 'payment_status' => $payment_details_ghi['payment_status'],
         // 'payment_mode' => $payment_details_ghi['payment_mode'],
         // 'txndate' => date("Y-m-d"),
         // );
         //print_pre($u_data);exit;
         
         $this->db->insert('payment_details',$payment_details_ghi);
         
         $logs_array['data'] = ["type" => "gpa_patch_insert_payment", "req" => json_encode($payment_details_ghi) , "lead_id" => $lead_id, "product_id" => 'R07'];
                               $this
                                   ->Logs_m
                                   ->insertLogs($logs_array);
                     }
                     
                     
                     
   
   }
					}
				}
			}
		}
		echo "checking"; exit;
   }


	
	
	
	
	
	
	
	
	
	//sahil changes ends
	public function demo(){
		echo 'demo';
	}
	public function d2c_fail_policy_create()
	{
		$this->db1 = $this->load->database('axis_retail',TRUE);
		
	   $query = $this
		->db1
		->query("SELECT ed.lead_id,ed.emp_id,ed.email,ed.mob_no,p.premium,g.QuotationNumber,mpst.payu_info_url FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,proposal AS p,ghi_quick_quote_response AS g WHERE epd.product_name = mpst.id AND p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND g.`status` = 'success' AND p.status != 'Success' AND p.count = 0 AND p.status = 'Payment Pending' order by ed.emp_id desc limit 15")->result_array();
	
		if($query)
		{
			
			foreach($query as $val1){
				
				$query_new = $this->db1->query("SELECT ed.lead_id,ed.emp_id,pt.txt_id,pt.pg_type FROM employee_details as ed,payment_txt_ids as pt where ed.lead_id = pt.lead_id and ed.emp_id =".$val1['emp_id'])->result_array();
				
				$this->db1->where("emp_id",$val1['emp_id']);
				$this->db1->update("proposal",["count"=>"1"]);
				
				if(!empty($query_new)){
					
					foreach($query_new as $val){
						
						if($val['pg_type'] == 'PayU'){
						
						$key = PAYU_INFO_KEY;
						$salt = PAYU_INFO_SALT;
						$wsUrl = PAYU_INFO_WSURL;
						$command = "verify_payment";
						$var1 = $val['txt_id']; // SourceTxnId

						$hash_str = $key  . '|' . $command . '|' . $var1 . '|' . $salt ;
						$hash = strtolower(hash('sha512', $hash_str));

						$r = array('key' => $key , 'hash' =>$hash , 'var1' => $var1, 'command' => $command);
						$qs= http_build_query($r);

						$c = curl_init();
						curl_setopt($c, CURLOPT_URL, $wsUrl);
						curl_setopt($c, CURLOPT_POST, 1);
						curl_setopt($c, CURLOPT_POSTFIELDS, $qs);
						curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
						curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
						curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);

						$o = curl_exec($c);
						$err = curl_error($c);

						curl_close($c);
						
						if ($err) {
						  $request_arr = ["lead_id" => $val1['lead_id'],"req" => json_encode($qs), "res" => json_encode($err) , "type"=>"pg_real_fail"];
						  $this->db1->insert("logs_docs",$request_arr);
						  
						}else{
							$valueSerialized = @unserialize($o);
							
							if($o === 'b:0;' || $valueSerialized !== false) {
								
								$request_arr = ["lead_id" => $val1['lead_id'], "req" => json_encode($qs) ,"res" => json_encode($o), "type"=>"pg_real_fail"];
								$this->db1->insert("logs_docs",$request_arr);
							}

							$rs = json_decode($o,true);
							$payUStatus = $rs['status'];
							$result = $rs['transaction_details'];
							$response_arr = $result[$val['txt_id']];

							if($payUStatus && $response_arr['status'] == 'success'){
								
								$request_arr = ["lead_id" => $val1['lead_id'], "req" => json_encode($qs) ,"res" => json_encode($o), "type"=>"pg_real_success"];
								$this->db1->insert("logs_docs",$request_arr);
								
								$date = new DateTime($response_arr['addedon']);
								$txt_date = $date->format('m/d/Y g:i:s A'); 
								
								$arr = ["payment_status" => "No Error","premium_amount" => round($response_arr['transaction_amount'],2),"payment_type" => $response_arr['PG_TYPE'],"pgRespCode" => $response_arr['error_code'],"merchantTxnId" => $response_arr['txnid'],"SourceTxnId" => $response_arr['txnid'],"txndate" => $txt_date,"TxRefNo" => $response_arr['mihpayid'],"TxStatus"=>$response_arr['status'],"json_quote_payment"=>json_encode($response_arr)];
						
								$proposal_ids = $this->db1->query("select id as proposal_id from proposal where emp_id='".$val1['emp_id']."'")->row_array();
								
								$this->db1->where("proposal_id",$proposal_ids['proposal_id']);
								$this->db1->update("payment_details",$arr);	
							
								$check_result = $this->policy_creation_call_cron($val1['lead_id']);
								
							}else{
								
							   $request_arr = ["lead_id" => $val1['lead_id'], "req" => json_encode($qs) ,"res" => json_encode($o), "type"=>"pg_real_fail"];
							   $this->db1->insert("logs_docs",$request_arr);
							   
							   //$this->db1->where("emp_id",$val1['emp_id']);
							   //$this->db1->update("proposal",["count"=>"1"]);
						
							}
						
						}
						
						}elseif($val['pg_type'] == 'Razorpay'){
							
						// Decleration for Razor Pay Key Id, Key Secret & Currency Type
						$key_id = RAZOR_KEY_ID;
						$key_secret = RAZOR_KEY_SECRET;
						$razcurrency = PAYMENTGATEWAY_CURRENCY;
						$razcheckoutmethod = PAYMENTGATEWAY_CHECKOUT_METHOD_AUTOMATIC; 
						
						$api = new Api($key_id, $key_secret);
						$payment_obj = $api->order->payments($val['txt_id']);
						
						$payment = (array)$payment_obj;
						
						//$this->db1->where("emp_id",$val1['emp_id']);
						//$this->db1->update("proposal",["count"=>"1"]);
						
						if(!empty($payment_obj['items'])){
							
							foreach ($payment_obj['items'] as $value){
								if($value['status']=='captured'){
									$request_arr = ["lead_id" => $val1['lead_id'],"req" => $val['txt_id'], "res" => json_encode($payment), "type"=>"pg_real_success"];
									$this->db1->insert("logs_docs",$request_arr);

									$arr = ["payment_status" => "No Error","premium_amount" => ($value['amount']/100),"payment_type" => $value['method'],"pgRespCode" => "","merchantTxnId" => $value['order_id'],"SourceTxnId" => $value['order_id'],"txndate" => date('m/d/Y h:i A', $value['created_at']),"TxRefNo" => $value['id'],"TxStatus"=>"success","bank_name"=>$value['bank'],"json_quote_payment"=>json_encode($payment)];
							
									$proposal_ids = $this->db1->query("select id as proposal_id from proposal where emp_id='".$val1['emp_id']."'")->row_array();
									
									$this->db1->where("proposal_id",$proposal_ids['proposal_id']);
									$this->db1->update("payment_details",$arr);
									
									$check_result = $this->policy_creation_call_cron($val1['lead_id']);
									echo $check_result['status']."hii".$val1['lead_id'];
									
								}else{
									
									$request_arr = ["lead_id" => $val1['lead_id'],"req" => $val['txt_id'], "res" => json_encode($payment), "type"=>"pg_real_fail"];
									$this->db1->insert("logs_docs",$request_arr);
							   
								}
							}
						
						}
						
						}
						
					}
				
				}		
				
			}
			
		}else{
			
		$query_r = $this
		->db1
		->query("SELECT ed.lead_id,ed.emp_id,ed.email,ed.mob_no,p.premium,g.QuotationNumber,mpst.payu_info_url FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,proposal AS p,ghi_quick_quote_response AS g WHERE epd.product_name = mpst.id AND p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND g.`status` = 'success' AND p.status != 'Success' AND p.count < 5 AND p.status = 'Payment Received' order by ed.emp_id desc limit 5")->result_array();
		
		if($query_r)
		{
			foreach($query_r as $val_r){
				$check_result = $this->policy_creation_call_cron($val_r['lead_id']);
				 echo $check_result['status']."hii".$val_r['lead_id'];
			}
		}
			
		}
		
	}
	
	public function policy_creation_call_cron($CRM_Lead_Id)
	  {
		  $this->load->model("API/Payment_integration_retail", "obj_api", true);
		  
		  $this->db1 = $this->load->database('axis_retail',TRUE);
		  
			$message = '';
			$update_data = $this
			  ->db1
			  ->query('SELECT p.id,p.emp_id,p.policy_detail_id,p.product_id,p.status,p.count
				FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal AS p,
				employee_details AS ed
				where epd.product_name = e.id
				AND p.emp_id = ed.emp_id
				AND ed.lead_id = "' .$CRM_Lead_Id. '"
				AND e.policy_subtype_id = 1
				AND epd.policy_detail_id = p.policy_detail_id');
			$update_payment = $update_data->row_array();
					
			 if($update_payment['status'] != 'Success'){
			  $arr_new = ["count" => $update_payment['count'] + 1];
			  
			  $this->db1->where('id', $update_payment['id']);
			  $this->db1->update("proposal", $arr_new);
			 }
			 
			 $request_arr = ["type" => "6"];
				$this->db1->where("emp_id",$update_payment['emp_id']);
				$this->db1->update("user_activity",$request_arr);
						 
			 if($update_payment['status']!='Success'){
	
		   // update proposal status - Payment Received
			   $arr_new = ["status" => "Payment Received"];
			   $this->db1->where('id', $update_payment['id']);
			   $this->db1->update("proposal", $arr_new);
	   
		  // check is policy already created?
				$query_check = $this->db1->query("select id from proposal where id = '" . $update_payment['id'] . "' and status != 'Success'")->row_array();
				if($query_check)
				{
				 // GHI API call
				 $api_response_tbl = $this->obj_api->GHI_GCI_api_call($update_payment['emp_id'], $update_payment['policy_detail_id']);

				 if($api_response_tbl['status']=='Success'){
				  // update proposal status - Success
				  $arr = ["status" => "Success"];
				  $this->db1->where('id', $update_payment['id']);
				  $this->db1->update("proposal", $arr);

				 }
				 return $api_response_tbl;
			  }
	   
		  }

	  }

    function getSmsDeliveryStatus() {
        $this->cron_m->getSmsDeliveryStatus();
    }

    function send_messages() {
        $this->cron_m->send_messages();
    }

    function send_emails() {
		
		$query = 'SELECT d.id,d.ops_type,ed.lead_id,d.path FROM proposal AS p,employee_details AS ed,documents AS d
WHERE p.emp_id = ed.emp_id
AND FIND_IN_SET(p.id,d.proposal_id) and p.created_date < "2020-01-28" group by d.id';
$result = $this->db->query($query)->result_array();
//print_pre($result);exit;
for($i =10; $i <count($result); $i++){
			  if (!is_dir(APPPATH.'resources/'.$result[$i]['lead_id'])) {
				 // echo 1;exit;
            mkdir(APPPATH.'resources/'.$result[$i]['lead_id'], 0777, true);
        }
		
		$z = explode('/',$result[$i]['path']);
		$file_name = end($z);
		//print_pre($file_name);exit;
		$extension = end(explode(".",$file_name));
		//print_Pre($extension);exit;
		if($result[$i]['ops_type'] == 'Y'){
			//echo APPPATH.'resources/'.$result[$i]['lead_id'].'/Auto_renewal.'.$extension;exit;
			rename(APPPATH.'resources/proposal/'.$file_name, APPPATH.'resources/'.$result[$i]["lead_id"].'/Auto_renewal.'.$extension);
			$this->db->where('id', $result[$i]['id']);
			$this->db->update('documents',['path' => '/application/resources/'.$result[$i]["lead_id"].'/Auto_renewal.'.$extension]);
			$postField = [
				   "Identifier" => "ByteArray",
				   "UploadRequest" => [
						[
							 "CategoryID"=>"1003",
							 "DataClassParam"=> [
								[
								   "Value"=>$result[$i]['lead_id'],
								   "DocSearchParamId"=>"22"
								]
							 ],
							 "Description"=>"",
							 "ReferenceID"=>"3100",
							 "FileName"=> "Auto_renewal".$extension,
							 "DocumentID"=>"2224",
							 "ByteArray"=> '/application/resources/'.$result[$i]["lead_id"].'/Auto_renewal.'.$extension,
							 "SharedPath"=>""
						]
					],
				   "SourceSystemName"=>"Axis"
				];
				
				$this->db->insert("scheduler", ["postRequest" => json_encode($postField), "status" => 0]);
			
		}
		else{
			
			//echo APPPATH.'resources/'.$result[$i]['lead_id'].'/custome_declaration_form.'.$extension; exit;
			rename(APPPATH.'resources/proposal/'.$file_name, APPPATH.'resources/'.$result[$i]["lead_id"].'/custome_declaration_form.'.$extension);
			$this->db->where('id', $result[$i]['id']);
			$this->db->update('documents',['path' => '/application/resources/'.$result[$i]["lead_id"].'/custome_declaration_form.'.$extension]);
			//echo $this->db->last_query();exit;
			$postField = [
				   "Identifier" => "ByteArray",
				   "UploadRequest" => [
						[
							 "CategoryID"=>"1003",
							 "DataClassParam"=> [
								[
								   "Value"=>$result[$i]['lead_id'],
								   "DocSearchParamId"=>"22"
								]
							 ],
							 "Description"=>"",
							 "ReferenceID"=>"3100",
							 "FileName"=> "custome_declaration_form".$extension,
							 "DocumentID"=>"2224",
							 "ByteArray"=> '/application/resources/'.$result[$i]["lead_id"].'/custome_declaration_form.'.$extension,
							 "SharedPath"=>""
						]
					],
				   "SourceSystemName"=>"Axis"
				];
				
				$this->db->insert("scheduler", ["postRequest" => json_encode($postField), "status" => 0]);
				//secho $this->db->last_query();exit;
		}
		
		
		
}
	

    }

    function send_leads() {
        $this->cron_m->send_leads();
    }

    function send_policy_reminder() {
        $this->cron_m->send_policy_reminder();
    }
	
	public function run_scheduler() {
			
$row = $this->db->where(["status"=> "0"])->get("scheduler");
	
	if($row->num_rows() > 0) {
		$row = $row->result_array();
		 
		foreach($row as $result) {
			$postField = json_decode($result['postRequest'], true);
			$saveField = json_decode($result['postRequest'], true);
			
			$img = file_get_contents($postField['UploadRequest'][0]['ByteArray']); 
			
	  
			// Encode the image string data into base64 
			$postField['UploadRequest'][0]['ByteArray'] = base64_encode($img);
			
			//$postField = $result;
			
			$this->db->where(["id" => $result['id']])->update("scheduler", [
				"status" => 1
			]);
//print_pre($postField);print_pre($saveField);
$this->docServiceCal($postField, $saveField);
//			$this->cron_m->docServiceCal($postField, $saveField);
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

    function send_daily_newsletter() {
        $this->cron_m->send_daily_newsletter();
    }

    function send_weekly_newsletter() {
        $this->cron_m->send_weekly_newsletter();
    }

    function send_weekly_articles() {
        $this->cron_m->send_weekly_articles();
    }

    function clear_all_dumps() {
        $this->cron_m->clear_all_dumps();
    }

    function fetch_mmv_bajaj_allianz() {
        $this->cron_m->fetch_mmv_bajaj_allianz();
    }

    function get_paramount_data($envelope) {

        $this->load->library("paramount");
        $data = $this->paramount->getdata($envelope, 'https://webintegrations.paramounttpa.com/TataMotorsWebAPI/Service1.svc/GeFamilyEnrollmentDetails', array(
            "Content-Type: application/json",
            "cache-control: no-cache"
        ));
        $aa = json_decode($data["GeFamilyEnrollmentDetailsResult"], true);
        if (isset($aa["Table"])) {
            return $aa["Table"];
        }
    }

    //update member id of employee and members deoending on tpa

    function get_tpa_member_id() {
		//echo "here";exit;
        $check_fhpl_policy_nos = [];


//        $str = "<Envelope xmlns='http://www.w3.org/2003/05/soap-envelope'><Body><GetenrollmentDetails_TATA xmlns='http://tempuri.org/'><UserName>TataMotors</UserName><Password>fhgn179ta</Password><GroupCode>16137</GroupCode><PolicyNumber>71250034180400000035</PolicyNumber></GetenrollmentDetails_TATA></Body></Envelope>";
//
//        $curl = curl_init();
//
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => "https://m.fhpl.net/Bunnyconnect/BCService.svc",
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => "",
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 30,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => "POST",
//            CURLOPT_POSTFIELDS => $str,
//            CURLOPT_HTTPHEADER => array(
//                "Content-Type: application/soap+xml;charset=UTF-8;",
//                "Content-Length:" . strlen($str),
//                "SOAPAction: \"http://tempuri.org/IBCService/GetenrollmentDetails_TATA\""
//            ),
//        ));
//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
//
//        $response = curl_exec($curl);
//        $err = curl_error($curl);
//        $info = curl_getinfo($curl);
//        print_pre($info);
//        curl_close($curl);
//
//        if ($err) {
//            echo "cURL Error #:" . $err;
//        } else {
//            echo $response;
//        }
//
//        exit;
        $data = $this->db
                ->select("tpa.TPA_name,epd.policy_no,ed.emp_code,ed.emp_id")
                ->from("master_company as mc,tpa_masters as tpa,employee_policy_detail as epd, employee_policy_member as epm,family_relation as fr,employee_details as ed")
                ->where("epd.policy_detail_id = epm.policy_detail_id")
                ->where("epm.family_relation_id = fr.family_relation_id")
                ->where("fr.family_id", 0)
                ->where("fr.emp_id = ed.emp_id")
                ->where("mc.company_id = ed.company_id")
                ->where("epd.TPA_id = tpa.TPA_id")
                ->get()
                ->result_array();
				
				//print_pre($data);exit;
//print_pre($data);exit;
  //     $data[0]["TPA_name"] = "FHPL";
   //    $data[0]["policy_no"] = '71250034180400000035';
//        $data[0]["emp_code"] = 'KIN-005';


//print_pre($data);exit;
//$data[0]["TPA_name"] = "health_india";
        if (count($data) > 0) {

            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]["TPA_name"] == "mediast") {
                    $this->cron_m->mediast($data[$i]);
                } else if ($data[$i]["TPA_name"] == "paramount") {
                    $this->cron_m->paramount($data[$i]);
                } elseif ($data[$i]["TPA_name"] == "health_india") {
                    $this->cron_m->health_india_enrollment($data[$i]);
                }
                  elseif ($data[$i]["TPA_name"] == "FHPL") {
                     if(in_array($data[$i]["policy_no"],$check_fhpl_policy_nos)){
                         continue;
                     }
                     else{
                         $this->cron_m->fhpl_enrollment($data[$i]);
                         $check_fhpl_policy_nos[] = $data[$i]["policy_no"];
                     }
                    
                }
            }
        }
    }

    function update_network_hospital() {
        
         $check_fhpl_policy_nos = [];

        $data = $this->db
                ->select("epd.policy_no,tpa.TPA_name")
                ->from("employee_policy_detail as epd,tpa_masters as tpa")
                ->where("epd.TPA_id = tpa.TPA_id")
                ->get()
                ->result_array();
				
				//print_pre($data);exit;
				
				
				
				
				
        //$data[0]["TPA_name"] = "FHPL";
		//print_pre($data);exit;
        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]["TPA_name"] == "mediast") {
                    //$this->cron_m->mediast_network_hospital($data[$i]);
                } else if ($data[$i]["TPA_name"] == "paramount") {
                    $this->cron_m->paramount_network_hospital($data[$i]);
                } else if ($data[$i]["TPA_name"] == "health_india") {
                    $this->cron_m->health_india_network_hospitals($data[$i]);
                }
                else if ($data[$i]["TPA_name"] == "FHPL") {
                    if(empty($check_fhpl_policy_nos)){
                        $this->cron_m->fhpl_network_hospitals($data[$i]);
                     }
                     else{
                         
                         $check_fhpl_policy_nos[] = $data[$i]["policy_no"];
                         continue;
                     }
                    
                    
                    
                    
                    
                }
            }
        }
    }

    function submit_claim() {

//       $imagedata = file_get_contents(APPPATH."resources/uploads/policy_member/1/cannot_connect.png");
//           
//        $base64 = base64_encode($imagedata);
//        echo $base64;exit;
//        $output_file = APPPATH."resources/uploads/policy_member/1/upload_document_id44444444.jpg";
//        
//       $ifp = fopen( $output_file, 'a' );
//       
//        fwrite( $ifp, base64_decode( $base64 ));
//        fclose( $ifp ); 
//        echo "done";exit;

        $data = $this->db
                ->select("ecr.claim_reimb_id,epd.TPA_id,epd.policy_no,epm.tpa_member_id,epm.policy_member_id")
                ->from("employee_policy_detail as epd, employee_policy_member as epm,employee_claim_reimb as ecr")
                ->where("epd.policy_detail_id = epm.policy_detail_id")
                ->where("epm.policy_member_id = ecr.policy_member_id")
                ->get()
                ->result_array();
				//print_Pre($data);exit;
       //$data[0]["TPA_id"] = 2;
        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]["TPA_id"] == 1) {
                    $this->cron_m->mediast_submit_claims($data[$i]);
                } else if ($data[$i]["TPA_id"] == 2) {
                    $this->cron_m->paramount_submit_claims($data[$i]);
                }
                else if ($data[$i]["TPA_id"] == 4) {
                    $this->cron_m->health_india_submit_claims($data[$i]);
                }
            }
        }
    }

    function get_claim_details() {
        $data = $this->db->select('ecr.claim_reimb_id,epd.TPA_id,epd.policy_no,ecr.claim_no')
                        ->from('employee_policy_detail as epd,employee_policy_member as epm, employee_claim_reimb as ecr')
                        ->where('epd.policy_detail_id = epm.policy_detail_id')
                        ->where('epm.policy_member_id = ecr.policy_member_id')
                        ->get()->result_array();

         //print_Pre($data);exit;
        //$data[0]["TPA_id"] = 1;
       // $data[0]["claim_no"] = 4265283;

        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]["TPA_id"] == 1) {
                    $this->cron_m->mediast_claim_details($data[$i]);
                } else if ($data[$i]["TPA_id"] == 2) {
                    $this->cron_m->paramount_claim_details($data[$i]);
                }
            }
        }
    }



//   function reminderEmail()
//    {
//        $user = $this->db->query("SELECT epm.policy_member_id,epd.policy_enrollment_end_date,ed.emp_id, ed.emp_firstname, ed.emp_lastname, epd.start_date, epd.end_date, mp.company_email, ed.email, ed.emp_code, mp.company_email, mp.company_id, epd.policy_enrollment_start_date, epd.policy_enrollment_end_date
//        FROM employee_policy_member as epm,employee_policy_detail as epd,family_relation as fr,employee_details as ed,master_company as mp,master_insurance_companies as mip
//        where epm.policy_detail_id = epd.policy_detail_id
//        and fr.family_relation_id = epm.family_relation_id
//        and fr.emp_id = ed.emp_id
//        and fr.family_id = 0
//        and ed.company_id = mp.company_id
//        and epd.insurer_id = mip.insurer_id")->result();
//
//		
//
//        for ($i = 0; $i < count($user); ++$i) {
//			//print_pre($user[$i]);exit;
//             $policy_enrollment_start_date = $user[$i]->policy_enrollment_start_date;
//             $policy_enrollment_end_date = $user[$i]->policy_enrollment_end_date;
//             $policy_enrollment_start_date_plus_one = date("Y-m-d", strtotime($policy_enrollment_start_date . ' +1 day'));
//            $date2 = date("Y-m-d");
//
//            // $policy_enrollment_start_date_plus_one = '29-07-2019';
//            // $policy_enrollment_start_date_plus_one = '31-07-2019';
//            if (!(((strtotime($date2) >= strtotime($policy_enrollment_start_date_plus_one))) && (strtotime($date2) <= strtotime($policy_enrollment_end_date)))){ 
//			//echo "sdsd";exit;
//                continue;
//            }
//			//echo "sdsdsds";exit;
//
//            //            if(date("Y-m-d", strtotime($policy_enrollment_start_date. ' +1 day')) != date("Y-m-d")) {
//            //                
//            //            }
//
//            $data['user'] = $user[$i];
//		
//            $efbt = $this->db->where(["policy_member_id" => $user[$i]->policy_member_id])->select("confirmed_flag")->get("employee_policy_member");
//			
//			
//				
//            if ($efbt->num_rows() > 0 && $efbt->row()->confirmed_flag == "N") {
//                $z = $this->load->view("reminder_mail", $data, true);
//				//echo "here";exit;
//                $details['email'] = [
//                    'to' => $user[$i]->email,
//                    'subject' => 'Enrollment Reminder ',
//                    'message' => preg_replace('~>\s+<~', '><', $this->load->view('email_new', [
//                        'subview' => $z,
//                        'name' => "alsdkalsdjkaldjad"
//                    ], TRUE)),
//                    //'bcc' => CAR_EMAIL
//                ];
//                save_queue($details);
//            }
//        }
//    }
    function reminderEmail()
    {
        $user = $this->db->query("SELECT epm.policy_member_id,epd.policy_enrollment_end_date,ed.emp_id, ed.emp_firstname, ed.emp_lastname, epd.start_date, epd.end_date, mp.company_email, ed.email, ed.emp_code, mp.company_email, mp.company_id, epd.policy_enrollment_start_date, epd.policy_enrollment_end_date
        FROM employee_policy_member as epm,employee_policy_detail as epd,family_relation as fr,employee_details as ed,master_company as mp,master_insurance_companies as mip
        where epm.policy_detail_id = epd.policy_detail_id
        and fr.family_relation_id = epm.family_relation_id
        and fr.emp_id = ed.emp_id
        and fr.family_id = 0
        and ed.company_id = mp.company_id
        and epd.insurer_id = mip.insurer_id")->result_array();

	//print_pre($user);exit;

        for ($i = 0; $i < count($user); ++$i) {
               $policy_enrollment_start_date = $user[$i]["policy_enrollment_start_date"];
              "enddate".$policy_enrollment_end_date = $user[$i]["policy_enrollment_end_date"];
               "startdateplusone".$policy_enrollment_start_date_plus_one = date("Y-m-d", strtotime($policy_enrollment_start_date . ' +1 day'));
              $date2 = date("Y-m-d"); 

            // $policy_enrollment_start_date_plus_one = '29-07-2019';
            // $policy_enrollment_start_date_plus_one = '31-07-2019';
             if (!(((strtotime($date2) >= strtotime($policy_enrollment_start_date_plus_one))) && (strtotime($date2) <= strtotime($policy_enrollment_end_date)))){ 
			
                continue;
            }
			

            //            if(date("Y-m-d", strtotime($policy_enrollment_start_date. ' +1 day')) != date("Y-m-d")) {
            //                
            //            }

            $data['user'] = $user[$i];
			$email = ($data['user']['email']);

            $efbt = $this->db->where(["policy_member_id" => $data['user']['policy_member_id']])->select("confirmed_flag")->get("employee_policy_member");
			
            if ($efbt->num_rows() > 0 && $efbt->row()->confirmed_flag == "N") {
				//echo "here";exit;
                $z = $this->load->view("reminder_mail", $data, true);

                $details['email'] = [
                    'to' => $email,
                    'subject' => 'Enrollment Reminder ',
                    'message' => preg_replace('~>\s+<~', '><', $this->load->view('email_new', [
                        'subview' => $z,
                        'name' => "alsdkalsdjkaldjad"
                    ], TRUE)),
                    //'bcc' => CAR_EMAIL
                ];
				//print_pre($details);exit;
                save_queue($details);
            }
        }
    }
	function enrollment_welcome_mail(){
        //echo "sdsdsd";exit;
        $employee_data = $this->db->get("employee_details")->result_array();
		//print_pre($employee_data);exit;
        for($i = 4381 ;$i < count($employee_data); $i++){
            
            $data = $this->db->query("SELECT group_concat(epd.enrollment_status) as 'status',group_concat(distinct(epd.policy_sub_type_id)) as 'policy_sub_type',mp.company_id,mp.company_cont_name,mp.company_mob,mp.company_email,tm.TPA_name,ed.emp_firstname,epd.policy_enrollment_start_date,ed.company_id,mp.comapny_name,mip.ins_co_name,epd.start_date,epd.end_date,mp.enrollment_start_date,mp.enrollment_end_date,ed.email,ed.emp_code,epd.policy_sub_type_id
FROM employee_policy_member as epm,employee_policy_detail as epd,family_relation as fr,employee_details as ed,master_company as mp,master_insurance_companies as mip,tpa_masters as tm
where epm.policy_detail_id = epd.policy_detail_id
and fr.family_relation_id = epm.family_relation_id
and fr.emp_id = ed.emp_id
and fr.family_id = 0
and ed.company_id = mp.company_id
and epd.insurer_id = mip.insurer_id
and epd.TPA_id = tm.TPA_id
and ed.emp_id = ".$employee_data[$i]["emp_id"])->row_array();
          
		  if(!$data["policy_member_id"]){
			  
			 $data = $this->db->query("SELECT group_concat(epd.enrollment_status) as 'status',group_concat(distinct(epd.policy_sub_type_id)) as 'policy_sub_type',mp.company_id,mp.company_cont_name,mp.company_mob,mp.company_email,tm.TPA_name,ed.emp_firstname,epd.policy_enrollment_start_date,ed.company_id,mp.comapny_name,mip.ins_co_name,epd.start_date,epd.end_date,mp.enrollment_start_date,mp.enrollment_end_date,ed.email,ed.emp_code,epd.policy_sub_type_id
FROM employee_policy_member as epm,employee_policy_detail as epd,family_relation as fr,employee_details as ed,master_company as mp,master_insurance_companies as mip,tpa_masters as tm
where epm.policy_detail_id = epd.policy_detail_id
and fr.family_relation_id = epm.family_relation_id
and fr.emp_id = ed.emp_id
and ed.company_id = mp.company_id
and epd.insurer_id = mip.insurer_id
and epd.TPA_id = tm.TPA_id
and ed.emp_id = ".$employee_data[$i]["emp_id"])->row_array();
			  
			  
			  
		  }
		  
           
            if(count($data) > 0){
                //check whether it is in all three policy types and no enrollment
                //&& (in_array("3", $subtype_ids))
                $subtype_ids = explode(",",$data[policy_sub_type]);
                $status = explode(",",$data["status"]); 
                if ( (in_array("1", $subtype_ids)) &&  (in_array("2", $subtype_ids)) && (in_array("3", $subtype_ids))  && (!in_array("1", $status))){
                    
                    //send gmc gpa gtl template
                    
                }
                else{
                   
//                    send individual templates
                    
                    if((in_array("1", $subtype_ids))){
                       // echo "here";exit;
                        //CHECK ENROLLMENT STATUS
                         $data1 = $this->db->query("SELECT epd.policy_enrollment_end_date,epd.enrollment_status as 'status',epd.policy_sub_type_id as 'policy_sub_type',mp.company_id,mp.company_cont_name,mp.company_mob,mp.company_email,tm.TPA_name,ed.emp_firstname,epd.policy_enrollment_start_date,ed.company_id,mp.comapny_name,mip.ins_co_name,epd.start_date,epd.end_date,mp.enrollment_start_date,mp.enrollment_end_date,ed.email,ed.emp_code,epd.policy_sub_type_id
FROM employee_policy_member as epm,employee_policy_detail as epd,family_relation as fr,employee_details as ed,master_company as mp,master_insurance_companies as mip,tpa_masters as tm
where epm.policy_detail_id = epd.policy_detail_id
and fr.family_relation_id = epm.family_relation_id
and fr.emp_id = ed.emp_id
and fr.family_id = 0
and ed.company_id = mp.company_id
and epd.insurer_id = mip.insurer_id
and epd.TPA_id = tm.TPA_id
and epd.policy_sub_type_id = 1
and ed.emp_id = ".$employee_data[$i]["emp_id"])->row_array();
                        //ENDS
                       // print_pre($data1);exit;
                        $data1["status"] = 0;
                  //check whther enrollment is yes or no in gmc
                         if($data1["status"] == 0){
                             //echo "sdsssssssssssss";exit;
                             $data1["enrollment"] = "no";
                             //gmc no enrollment
                             $details = [];
                 $details['email'] = [
                            'to' => $data["email"],
                            'subject' => 'Enrollment Welcome Mailer ',
                            'message' => preg_replace('~>\s+<~', '><',$this->load->view('email_new', [
                                'subview' => enrollment_welcome_mail_krawler($data1),
                                'name' => "alsdkalsdjkaldjad"
                                
                                    ], TRUE)),
                            //'bcc' => CAR_EMAIL
                        ];
                 print_pre($details);exit;
                  save_queue($details); 
                             
                             
                         }
                         else{
                             //gmc enrollment
                           //  echo "sds";exit;
                             $data1["enrollment"] = "yes";
                              $details = [];
                 $details['email'] = [
                            'to' => $data["email"],
                            'subject' => 'Enrollment Welcome Mailer ',
                            'message' => preg_replace('~>\s+<~', '><',$this->load->view('email_new', [
                                'subview' => enrollment_welcome_mail_krawler($data1),
                                'name' => "alsdkalsdjkaldjad"
                                
                                    ], TRUE)),
                            //'bcc' => CAR_EMAIL
                        ];
                 print_pre($details);exit;
                  save_queue($details); 
                         }
                        
                        
                    }
                    
                    
                    if((in_array("2", $subtype_ids))){
                        //send gpa template
                        
                    }
                    
                     if((in_array("3", $subtype_ids))){
                        //send gpa template
                        
                    }
                    
                    
                    
                    
                }
               
                
            }
            
        }
        
        
		
		$data = $this->db->query("SELECT mp.company_id,mp.company_cont_name,mp.company_mob,mp.company_email,tm.TPA_name,ed.emp_firstname,epd.policy_enrollment_start_date,ed.company_id,mp.comapny_name,mip.ins_co_name,epd.start_date,epd.end_date,mp.enrollment_start_date,mp.enrollment_end_date,ed.email,ed.emp_code,epd.policy_sub_type_id
FROM employee_policy_member as epm,employee_policy_detail as epd,family_relation as fr,employee_details as ed,master_company as mp,master_insurance_companies as mip,tpa_masters as tm
where epm.policy_detail_id = epd.policy_detail_id
and fr.family_relation_id = epm.family_relation_id
and fr.emp_id = ed.emp_id
and fr.family_id = 0
and ed.company_id = mp.company_id
and epd.insurer_id = mip.insurer_id
and epd.TPA_id = tm.TPA_id
and (epd.policy_sub_type_id = 1 or epd.policy_sub_type_id = 4)")->result_array();
                
                print_pre($this->db->last_query());exit;
     // $data[0]["company_id"] = 174; 
     // $data[0]["email"] = "amit.matani@fyntune.com";
      // if($z == $y){ 
        for($i =0 ;$i < count($data); $i++){
            $z = strtotime($data[$i]["created_at"]);
            $y = strtotime(date("Y-m-d"));
            if($z == $y){ 
                if($data[$i]["company_id"] == 174){
                    //for krawler
                    $details = [];
                 $details['email'] = [
                            'to' => $data[$i]["email"],
                            'subject' => 'Enrollment Welcome Mailer ',
                            'message' => preg_replace('~>\s+<~', '><',$this->load->view('email_new', [
                                'subview' => enrollment_welcome_mail_krawler($data[$i]),
                                'name' => "alsdkalsdjkaldjad"
                                
                                    ], TRUE)),
                            //'bcc' => CAR_EMAIL
                        ];
                 //print_pre($details);exit;
                  save_queue($details); 
                  //echo "sdsds";exit;
                }
                else if($data[$i]["company_id"] == 180){
                     $details = [];
                 $details['email'] = [
                            'to' => $data[$i]["email"],
                            'subject' => 'Enrollment Welcome Mailer ',
                            'message' => preg_replace('~>\s+<~', '><',$this->load->view('email_new', [
                                'subview' => enrollment_welcome_mail($data[$i]),
                                'name' => "alsdkalsdjkaldjad"
                                
                                    ], TRUE)),
                            //'bcc' => CAR_EMAIL
                        ];
                    //print_pre($details);exit;
                    save_queue($details); 
                   // echo "check";exit;
                }
               
                  //echo "check";exit;
                
            }
           
        }
      
		
	}
    
    function enrollment11111_welcome_mail(){
		
		$data = $this->db->query("SELECT mp.company_cont_name,mp.company_mob,mp.company_email,tm.TPA_name,ed.emp_firstname,epd.policy_enrollment_start_date,ed.company_id,mp.comapny_name,mip.ins_co_name,epd.start_date,epd.end_date,mp.enrollment_start_date,mp.enrollment_end_date,ed.email,ed.emp_code,epd.policy_sub_type_id
FROM employee_policy_member as epm,employee_policy_detail as epd,family_relation as fr,employee_details as ed,master_company as mp,master_insurance_companies as mip,tpa_masters as tm
where epm.policy_detail_id = epd.policy_detail_id
and fr.family_relation_id = epm.family_relation_id
and fr.emp_id = ed.emp_id
and fr.family_id = 0
and ed.company_id = mp.company_id
and epd.insurer_id = mip.insurer_id
and epd.TPA_id = tm.TPA_id
and (epd.policy_sub_type_id = 1 or epd.policy_sub_type_id = 4)")->result_array();

        // if($z == $y){ 
        for($i = 548 ;$i < count($data); $i++){
            $z = strtotime($data[$i]["policy_enrollment_start_date"]);
            $y = strtotime(date("Y-m-d"));
            if(true){ 
			  if($data[$i]["company_id"] == 174){
                    //for krawler
                    $details = [];
                 $details['email'] = [
                            'to' => $data[$i]["email"],
                            'subject' => 'Enrollment Welcome Mailer ',
                            'message' => preg_replace('~>\s+<~', '><',$this->load->view('email_new', [
                                'subview' => enrollment_welcome_mail_krawler($data[$i]),
                                'name' => "alsdkalsdjkaldjad"
                                
                                    ], TRUE)),
                            //'bcc' => CAR_EMAIL
                        ];
                 //print_pre($details);exit;
                  save_queue($details); 
				 // echo "check details";exit;
                  //echo "sdsds";exit;
                }
				 else if($data[$i]["company_id"] == 180){
                     $details = [];
                 $details['email'] = [
                            'to' => $data[$i]["email"],
                            'subject' => 'Enrollment Welcome Mailer ',
                            'message' => preg_replace('~>\s+<~', '><',$this->load->view('email_new', [
                                'subview' => enrollment_welcome_mail($data[$i]),
                                'name' => "alsdkalsdjkaldjad"
                                
                                    ], TRUE)),
                            //'bcc' => CAR_EMAIL
                        ];
                    //print_pre($details);exit;
                    save_queue($details); 
					//echo "sdsdsdsdsd";exit;
                   // echo "check";exit;
                }
               
                
            }
           
        }
      
		
	}
        
        
        function get_claim_details_from_policy_no(){
            $data = $this->db->select('epd.policy_no,epd.TPA_id')
                        ->from('employee_policy_detail as epd,tpa_masters as tpa')
                        ->where('epd.TPA_id = tpa.TPA_id')
                        ->get()->result_array();
						
						//print_pre($data);exit;
            
            //$data[0]["TPA_id"] = 5;
           if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]["TPA_id"] == 1) {
                    $this->cron_m->mediast_claim_details($data[$i]);
                } else if ($data[$i]["TPA_id"] == 2) {
                    $this->cron_m->paramount_claim_details($data[$i]);
                }
                else if ($data[$i]["TPA_id"] == 4) {
                   
                     $this->cron_m->health_india_claim_details($data[$i]);
                }
                  else if ($data[$i]["TPA_id"] == 5) {
                   
                     $this->cron_m->fhpl_claim_details($data[$i]);
                }
            }
        }
        }
		
		
		
function send_dropoff_reminder_realtime($emp_id = 0){
	$this->db = $this->load->database('axis_retail',TRUE);
	$emp_id = !empty($emp_id) ? $emp_id : 0;

	// INSERT DATA INTO TEMP TABLE
	$request_arr_temp = array("emp_id" => $emp_id,"requestdata"=>"","responsedata"=>"","step"=>"STEP_1");
	$this->db->insert("tempdropoffcapture",$request_arr_temp);
	// INSERT DATA INTO TEMP TABLE
	
	if($this->session->userdata('d2c_session')) {
     				
					
		$aD2CSession = $this->session->userdata('d2c_session');
		$emp_id = encrypt_decrypt_password($aD2CSession['emp_id'],'D');		
	}

	//$emp_id = (!empty($_GET['emp_id'])) ?  $_GET['emp_id'] : 0;
	$sqlStr = "SELECT ed.json_qote,ed.ISNRI,ed.mob_no,ed.email,ed.lead_id,ua.type,ed.emp_id 
	FROM employee_details as ed,user_activity as ua 
	WHERE ed.emp_id = ua.emp_id AND ed.emp_id = ".$emp_id." 
	AND ua.type != 6 AND ed.dropoff_flag = 0";
	//AND ua.status = 0 
	
	$query=$this->db->query($sqlStr)->result_array();
	if($query){
		 // INSERT DATA INTO TEMP TABLE
		$request_arr_temp = array("emp_id" => $emp_id,"requestdata"=>"","responsedata"=>"","step"=>"STEP_2");
		$this->db->insert("tempdropoffcapture",$request_arr_temp);
		// INSERT DATA INTO TEMP TABLE	
		 
		 $click_url =$this->db->query("select click_pss_url from product_master_with_subtype where product_code = 'R05'")->row_array();
		 foreach ($query as $val)
		 {
			$arr = ["emp_id" => $val['emp_id']];	
			$logs_type = "d2c_dropoff_realtime";	
			$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($arr),"type"=>$logs_type];
			$this->db->insert("logs_post_data",$request_arr);			
			$emp_id_encrypt = encrypt_decrypt_password($val['emp_id']);
			$json_array = json_decode($val['json_qote'],true);
			
			$data = $this->db
					->get_where('sms_template', ['module_name' => $val['type']])
					->row_array();
			$premium = "";	
			$query_premium = $this->db->query("select epm.policy_mem_sum_premium from employee_details as ed,family_relation as fr,employee_policy_member as epm where ed.emp_id = fr.emp_id AND epm.family_relation_id = fr.family_relation_id AND fr.family_id =0 AND ed.emp_id =".$val['emp_id'])->row_array();
			$premium = $query_premium['policy_mem_sum_premium'];	
			
			$send_data = [1,2];
			
			// INSERT DATA INTO TEMP TABLE
			$request_arr_temp = array("emp_id" => $emp_id,"requestdata"=>"","responsedata"=>"","step"=>"STEP_3");
			$this->db->insert("tempdropoffcapture",$request_arr_temp);
			// INSERT DATA INTO TEMP TABLE
			
			// Create Lead In CRM Start
			$lead_id = json_decode($this->cron_m->createCRMLeadDropOff($val['emp_id']),true);
			// Create Lead In CRM End
			
			// Create Member In CRM Start
			if(!empty($lead_id['LeadId'])) {
				$this->cron_m->insertMemberCRMDropOff($val['emp_id'],$lead_id['LeadId']);
			}
			// Create Member In CRM End
			
			// INSERT DATA INTO TEMP TABLE
			$request_arr_temp = array("emp_id" => $emp_id,"requestdata"=>"","responsedata"=>"","step"=>"STEP_4");
			$this->db->insert("tempdropoffcapture",$request_arr_temp);
			// INSERT DATA INTO TEMP TABLE
			
			/* Added By Shardul Kulkarni on 07-Aug-2020 for updating employee_details field 'dropoff_flag' to 1 Start */
			 $request_arr_dropoff = ["dropoff_flag" => "1"];
			 $this->db->where("emp_id",$val['emp_id']);
			 $this->db->update("employee_details", $request_arr_dropoff);			 
			 /* Added By Shardul Kulkarni on 07-Aug-2020 for updating employee_details field 'dropoff_flag' to 1 End */
			
			foreach($send_data as $value){
			
				$url = base_url()."customer_dropoff_tmp/".$emp_id_encrypt."/".$value;
			
				$url_req = "https://api-alerts.kaleyra.com/v4/?api_key=A6ef09c1692b5f41a8cd5bf4ac5e22695&method=txtly.create&url=".urlencode($url)."&title=xyz";
					
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
				
				$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($url_req),"res" => json_encode($result) , "type"=>"bitly_url_".$value];
				$this->db->insert("logs_docs",$request_arr);
				
				$data_new = json_decode($result,true);	
				
				if(empty($data_new)){
					$data_new['txtly'] = $url;
				}
				
				$content = [
				'other_data' => [
					'emp_id' => $val['emp_id'],
					'isNri' => $val['ISNRI'],
					'template_id' => $data['template_id'],
					'lead_id' => $val['lead_id'],
					'email' => $val['email'],
					'mob_no' => $val['mob_no'],
					'url' => $data_new['txtly'],
					'product_name' => trim($json_array['product_name']),
					'premium' => $premium,
					'alert_mode' => $value,
				],
			];
					
			$trans_details = $this->sendTransactionalSms($content['other_data'],$click_url['click_pss_url']);
				
			}
			
			$request_arr = ["status" => "1"];
			$this->db->where("emp_id",$val['emp_id']);
			$this->db->update("user_activity",$request_arr);
			
			//Array ( [1] => http://msg.mn/kAX6kp [2] => http://msg.mn/HsBKwh )
			
		 }
		
	}
}		
function send_dropoff_reminder(){
	$this->db = $this->load->database('axis_retail',TRUE);
	$query=$this->db->query("select ed.json_qote,ed.ISNRI,ed.mob_no,ed.email,ed.lead_id,ua.type,ed.emp_id from employee_details as ed,user_activity as ua where ed.emp_id = ua.emp_id AND ua.status = 0 AND ua.type != 6 AND (TIMESTAMPDIFF(SECOND, ua.updated_time,now()) >= 900)")->result_array();
	
	 if($query){
		 
	 $click_url =$this->db->query("select click_pss_url from product_master_with_subtype where product_code = 'R05'")->row_array();
		 
	 foreach ($query as $val)
     {
		$emp_id_encrypt = encrypt_decrypt_password($val['emp_id']);
		$json_array = json_decode($val['json_qote'],true);
		
		$data = $this->db
                ->get_where('sms_template', ['module_name' => $val['type']])
                ->row_array();
		$premium = "";	
		$query_premium = $this->db->query("select epm.policy_mem_sum_premium from employee_details as ed,family_relation as fr,employee_policy_member as epm where ed.emp_id = fr.emp_id AND epm.family_relation_id = fr.family_relation_id AND fr.family_id =0 AND ed.emp_id =".$val['emp_id'])->row_array();
		$premium = $query_premium['policy_mem_sum_premium'];	

		$url = base_url()."customer_dropoff_tmp/".$emp_id_encrypt;
		
		$url_req = "https://api-alerts.kaleyra.com/v4/?api_key=A6ef09c1692b5f41a8cd5bf4ac5e22695&method=txtly.create&url=".urlencode($url)."&title=xyz";
			
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
			
			$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($url_req),"res" => json_encode($result) , "type"=>"bitly_url"];
	        $this->db->insert("logs_docs",$request_arr);
			
			$data_new = json_decode($result,true);	
			if(empty($data_new)){
				$data_new['txtly'] = $url;
			}
			$url = $data_new['txtly'];
		
				
		$content = [
            'other_data' => [
				'emp_id'=>$val['emp_id'],
				'isNri'=>$val['ISNRI'],
				'template_id' => $data['template_id'],
				'lead_id' => $val['lead_id'],
				'email' => $val['email'],
				'mob_no' => $val['mob_no'],
				'url' => $url,
				'product_name' => trim($json_array['product_name']),
				'premium' => $premium,
			],
        ];
		
	
		
		$request_arr = ["status" => "1"];
		$this->db->where("emp_id",$val['emp_id']);
		$this->db->update("user_activity",$request_arr);

	$trans_details = $this->sendTransactionalSms($content['other_data'],$click_url['click_pss_url']);
		
	 }
	
	 }
     

	}
	
function sendTransactionalSms($other_data, $click_url)
    {
		
		// Added By Shardul For Validating ISNRI on 20-Aug-2020
		$dataArray['emp_id'] = !empty($other_data['emp_id']) ? $other_data['emp_id'] : "";
		$dataArray['isNri'] = !empty($other_data['isNri']) ? $other_data['isNri'] : "";
		$dataArray['product_id'] = 'R05';
		$alertMode = helper_validate_is_nri($dataArray);
		
		
		$other_data['product_name'] = D2C_PRODUCT_NAME;
		
			$AlertV1 = '';
			$AlertV2 = '';
			$AlertV3 = '';
			
			if($other_data['template_id'] == 'A1293'){
				$AlertV1 = $other_data['product_name'];
				$AlertV2 = $other_data['url'];
			}else{
				$AlertV1 = $other_data['product_name'];
				$AlertV2 = $other_data['url'];
				$AlertV3 = $other_data['premium'];
			}
			
		
        $parameters =[
		"RTdetails" => [
       
            "PolicyID" => '',
            "AppNo" => 'HD100017934',
            "alertID" => $other_data['template_id'],
            "channel_ID" => $other_data['product_name'],
            "Req_Id" => 1,
            "field1" => '',
            "field2" => '',
            "field3" => '',
            //"Alert_Mode" => isset($other_data['alert_mode'])?$other_data['alert_mode']:'3',
            "Alert_Mode" => isset($other_data['alert_mode'])?$other_data['alert_mode'] : $alertMode,
            "Alertdata" => 
                [
                    "mobileno" => substr(trim($other_data['mob_no']), -10),
                    "emailId" => $other_data['email'],
                    "AlertV1" => $AlertV1,
                    "AlertV2" => $AlertV2,
                    "AlertV3" => $AlertV3,
                    "AlertV4" => '',
                    "AlertV5" => '',
                ]

			]

		];
		 $parameters = json_encode($parameters);
		 $curl = curl_init();
		
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $click_url,
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
	
	$request_arr = ["lead_id" => $other_data['lead_id'], "req" => json_encode($parameters),"res" => json_encode($response) , "type"=>"sms_logs"];
	$this->db->insert("logs_docs",$request_arr);
	return $response;
       
   
    }
	
	/**
	 * This Function is use for Sending DropOff Email to user who is on the same page 
	 * More than 10 min.
	 *
	 * @param : 
	 * 
	 * @author Shardul Kulkarni<shardul.kulkarni@fyntune.com>
	 * @return String
	 * @URL : http://eb.benefitz.in/cron_specific_duration_dropoff
	 */ 
	function cron_specific_duration_dropoff(){
		$this->db1 = $this->load->database('axis_retail',TRUE);
		$constDropOffTime = DROPOFF_LIFE_SEC;
		$timeIntervalDropOffQuery = "SELECT * FROM employee_details WHERE modified_date <= date_sub(now(), interval ".$constDropOffTime." minute) AND dropoff_flag = 0";
	
		$query = $this->db1->query($timeIntervalDropOffQuery)->result_array();
		
		$tempEmpIds = "";
		if($query) {
			foreach($query as $val1){
				$empId = $val1['emp_id'];
				$queryUserStages = "SELECT * FROM user_activity WHERE emp_id = '".$empId."'";
				$queryUserStagesData = $this->db1->query($queryUserStages)->result_array();
				if(!empty($queryUserStagesData) && $queryUserStagesData['type'] != '6') {					

					$this->send_dropoff_reminder_realtime($empId);
					// echo $empId.'<br/>';
					if(!empty($tempEmpIds)) {
						$tempEmpIds = $empId; 
					} else {
						$tempEmpIds = $tempEmpIds.', '.$empId;
					}
				}
			}
			// Insert Data into Logs
			$tempEmpIds = !empty($tempEmpIds) ? "Emp Ids : " . $tempEmpIds : "";	
			$logs_type = "d2c_dropoff_cron_job";	
			$request_arr = ["lead_id" => "", "req" => "Cron Job Successfully run. ".$tempEmpIds,"type"=>$logs_type];
			$this->db1->insert("logs_post_data",$request_arr);
		}
				
		echo "Cron Job for Drop Off Run Successfully";
	}
	
}