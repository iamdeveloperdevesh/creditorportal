<?php if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

class Axis_freedom_redirection extends CI_Controller
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

        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);
        $this
            ->load
            ->model("API/Payment_integration_freedom_plus", "obj_api", true);
        $this
            ->load
            ->model("Logs_m");
        //echo encrypt_decrypt_password(623813);

        $this
        ->load
        ->model("employee/home_m", "obj_home", true);
        
    }

    /*script to update premium for R11
    Author - Ankita
    Date - 22/03/2021*/

    public function update_premium_script(){
        $leads = array('6849490','89989298','320103');//
        foreach ($leads as $key => $value) {
            $emp_data = $this->db->query("SELECT emp_id,deductable from employee_details WHERE lead_id = '".$value."'")->row_array();
            $emp_id = $emp_data['emp_id'];
            $deductable = $emp_data['deductable'];
            $q = "SELECT policy_detail_id,id from proposal where emp_id = '".$emp_id."' AND status = 'Payment Recieved'";
            //echo $q;exit;
            $fail_data = $this->db->query($q)->result_array();
            
            foreach ($fail_data as $key => $pid) {
                echo "Lead Id - ".$value.'<br>';
                $policy_detail_id = $pid['policy_detail_id'];
                $proposal_id = $pid['id'];
                $response = $this->db
                    ->query('SELECT epd.policy_detail_id,epd.policy_sub_type_id,epm.family_relation_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
                        epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type","R001" as relation_code
                        FROM 
                        employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_details AS ed
                        WHERE epd.policy_detail_id = epm.policy_detail_id
                        AND epm.family_relation_id = fr.family_relation_id
                        AND fr.family_id = 0
                        AND fr.emp_id = ed.emp_id
                        AND ed.emp_id = ' . $emp_id . '
                        AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' UNION all SELECT epd.policy_detail_id,epd.policy_sub_type_id,epm.family_relation_id, epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
                        epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type",mfr.relation_code
                        FROM 
                        employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
                        master_family_relation AS mfr
                        WHERE epd.policy_detail_id = epm.policy_detail_id
                        AND epm.family_relation_id = fr.family_relation_id
                        AND fr.family_id = efd.family_id 
                        AND efd.fr_id = mfr.fr_id
                        AND fr.emp_id = ' . $emp_id . '
                        AND `epd`.`policy_detail_id` = ' . $policy_detail_id)->result_array();
                if($response[0]['suminsured_type'] == 'family_construct_age'){
                    $change_premium = false;
                    print_pre($response);//exit;
                    foreach($response as $value){
                        if($value['age_type'] == 'days'){
                            $age[] = 0;
                        }else{
                            $age[] = $value['age'];
                        }
                                            
                    }
                    //print_pre($age);exit;
                    if($policy_detail_id == HEALTHPROXL_GHI_ST){
                       $check = $this->db->select("*")
                                ->from("family_construct_age_wise_si")
                                ->where("sum_insured", $response[0]['policy_mem_sum_insured'])
                                ->where("family_type", $response[0]['familyConstruct'])
                                ->where("policy_detail_id", $policy_detail_id)
                                ->where("deductable", $deductable)
                                ->get()
                                ->result_array(); 
                    }else{
                        $check = $this->db->select("*")
                                ->from("family_construct_age_wise_si")
                                ->where("sum_insured", $response[0]['policy_mem_sum_insured'])
                                ->where("family_type", $response[0]['familyConstruct'])
                                ->where("policy_detail_id", $policy_detail_id)
                                ->get()
                                ->result_array(); 

                    }           
                   echo $this->db->last_query().'<br>';
                    $max_age = max($age);
                    foreach($check as $value){
                        $min_max_age = explode("-",$value['age_group']);
                        if($max_age >= $min_max_age[0] && $max_age <= $min_max_age[1]){
                            $premium = $value['PremiumServiceTax'];
                        }
                    }
                    //echo $premium;
                    foreach($response as $key => $value1){
                        //echo $premium.'<br>';
                        
                        if($response[$key]['policy_sub_type_id'] == 1){
                            
                            // Proposal 
                            $this->db->where('id', $proposal_id);
                            $this->db->update('proposal', ['premium' => $premium]);
                            if($this->db->affected_rows()){
                                echo $this->db->last_query().'<br>';
                            }
                            //employee policy member
                            $response[$key]['policy_mem_sum_premium'] = trim($premium);
                            $this->db->where('policy_member_id', $response[$key]['policy_member_id']);
                            $this->db->update('employee_policy_member', ['policy_mem_sum_premium' => $premium]);
                            if($this->db->affected_rows()){
                                echo $this->db->last_query().'<br>';
                            }
                            // Proposal Member
                            $this->db->where('policy_member_id', $response[$key]['policy_member_id']);
                            $this->db->update('proposal_member', ['policy_mem_sum_premium' => $premium]);
                            if($this->db->affected_rows()){
                                echo $this->db->last_query().'<br>';
                            }
                            
                        }else if($response[$key]['policy_sub_type_id'] == 2){
                            // Proposal 
                            $this->db->where('id', $proposal_id);
                            $this->db->update('proposal', ['premium' => $premium]);
                            if($this->db->affected_rows()){
                                echo $this->db->last_query().'<br>';
                            }
                            $familyConstructs = explode('+',$response[0]['familyConstruct'])[0];
                            //echo $familyConstructs;
                            if($familyConstructs == '2A')
                            {
                                $premium_gpa = $premium/2;
                            }else{
                                $premium_gpa = $premium;
                            }
                            $response[$key]['policy_mem_sum_premium'] = trim($premium_gpa);
                            $this->db->where('policy_member_id', $response[$key]['policy_member_id']);
                            $this->db->update('employee_policy_member', ['policy_mem_sum_premium' => $premium_gpa]);
                            if($this->db->affected_rows()){
                                echo $this->db->last_query().'<br>';
                            }
                            // Proposal Member
                            $this->db->where('policy_member_id', $response[$key]['policy_member_id']);
                            $this->db->update('proposal_member', ['policy_mem_sum_premium' => $premium]);
                            if($this->db->affected_rows()){
                                echo $this->db->last_query().'<br>';
                            }
                        }
                    }
                    //print_r($response);exit;
                }
            }
        }
       // exit;
    }

    public function fetch_policy_data()
    {
        header('Content-Type: application/json');
        echo json_encode($this
            ->obj_api
            ->fetch_policy_data_m());
    }

    public function coi_download()
    {
        echo json_encode($this
            ->obj_api
            ->coi_download_m());
    }
	
	public function acknowledgement_download()
    {
        echo json_encode($this
            ->obj_api
            ->acknowledgement_download_m());
    }

    public function redirect_url_check()
    {
        echo json_encode($this
            ->obj_api
            ->redirect_url_check_m());
    }

    public function redirect_url_send()
    {
        $this
            ->obj_api
            ->redirect_url_send_m();
        echo json_encode(['status' => 'success']);
    }

    /* cron */
    public function update_payu_rejected()
    {
        $this
            ->obj_api
            ->update_payu_rejected_m();
    }

    /* cron */
    public function emandate_enquiry_HB_call()
    {
        $this
            ->obj_api
            ->emandate_enquiry_HB_call_m();
        echo json_encode(['status' => 'success']);
    }

    public function check_error_data()
    {
        echo json_encode($this
            ->obj_api
            ->check_error_data_m());
    }

    public function payment_error_view($emp_id_encrypt)
    {

        $emp_id = encrypt_decrypt_password($emp_id_encrypt, 'D');
        $lead_arr = $this
            ->db
            ->query("select lead_id,email from employee_details where emp_id = '$emp_id' ")->row_array();
        $lead_id = $lead_arr['lead_id'];
        $email = $lead_arr['email'];

        $this
            ->load
            ->employee_template_api('payment_error_view', compact('emp_id', 'lead_id', 'email'));

    }

    public function payment_redirect_view($emp_id_encrypt)
    {
        $emp_id = encrypt_decrypt_password($emp_id_encrypt, 'D');

        if ($emp_id)
        {
			//Dedupe - sonal
			$query_lead = $this
            ->db
            ->query('SELECT emp_id
    FROM employee_details AS ed
    where ed.emp_id = "' . $emp_id . '"
    AND ed.lead_status = "Rejected"');
        if ($query_lead->num_rows() > 0)
        {
                        echo "Lead is Rejected.Please continue journey with Fresh Lead ";
                                                                exit;
                }
//Quote Expired - sonal
      $quote_exp_check = common_quote_expired_bb($emp_id);
                if($quote_exp_check['status'] == 1){
                  echo   $quote_exp_check['msg'];exit;
                }


            /*//AFPP policy expired.Payment has to be stopped.(AFPP plus payment allow)
            $new_check = $this->db->query("SELECT ed.lead_id,p.created_date FROM employee_details AS ed,proposal as p WHERE ed.emp_id = p.emp_id and date(p.created_date) < '2020-12-16' and ed.product_id = 'R03' and p.status = 'Payment Pending' and ed.emp_id= '".$emp_id."' GROUP BY p.emp_id")->row_array();
            
            if(!empty($new_check['lead_id']))
            {
            echo "Please get in touch with your branch / RM for new version of AFPP plan.";exit;
            }*/

            //replaced cust_id with unique_ref_no on 15-05-2021
            $query = $this
                ->db
                ->query("SELECT ed.is_non_integrated_single_journey,p.sum_insured,ed.emp_firstname,ed.emp_lastname,ed.lead_id,ed.emp_id,epd.policy_detail_id,mpst.payment_source_code,ed.email,ed.mob_no,sum(p.premium) as premium,mpst.payment_url,p.status,mpst.product_code,ed.json_qote,ed.unique_ref_no,ed.cust_id FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,payment_details as pd,user_payu_activity as ua WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND p.id = pd.proposal_id AND  ed.emp_id = ua.emp_id and (TIMESTAMPDIFF(SECOND, ua.created_time,now()) < 259200) AND ed.emp_id= '" . $emp_id . "' GROUP BY p.emp_id")->row_array();
            //echo $this->db->last_query();exit;
            if (!empty($query))
            {

                //commented by upendra for updated dedupe logic - 10-05-2021
                // $already_cust_id = $this
                //     ->db
                //     ->query("select ed.lead_id from employee_details as ed,proposal as p where ed.emp_id = p.emp_id and p.status in('Success','Payment Received') and ed.cust_id = '" . $query['cust_id'] . "' and ed.product_id = '" . $query['product_code'] . "' and ed.lead_id != '" . $query['lead_id'] . "' group by p.emp_id")->num_rows();

                    //pm.familyConst 2A,2A+1k,2A+2K //end journey
                    //pm.familyConstruct 1A+1K /1A/1A+2K // self / spouse

                    // $already_cust_id = $this
                    // ->db
                    // ->query("select ed.lead_id,epm.familyConstruct,p.sum_insured from employee_details as ed,proposal 
                    // AS p, family_relation as fr, master_family_relation as mfr,employee_policy_member as epm 
                    // where ed.emp_id = p.emp_id and p.status in('Success','Payment Received') and ed.cust_id = '" . $query['cust_id'] . "' and ed.product_id = '" . $query['product_code'] . "' and ed.lead_id != '" . $query['lead_id'] . "' group by p.emp_id")->row_array();
                
                    //replaced cust_id with unique_ref_no on 15-05-2021


                    //dedupe logic
                    $check_dedupe = common_function_ref_id_exist($query['lead_id']);
                    if($check_dedupe['status'] == 'error'){
                        echo $check_dedupe['msg']; 
                        exit;
                    }


                    if ($query['status'] != 'Payment Pending')
                    {
                        redirect(base_url("payment_success_view_call_axis/" . $emp_id_encrypt));
                    }
                    else
                    {

                        $lead_data = $this
                            ->obj_api
                            ->get_all_quote_call($query['emp_id']);
                        
                        if ($lead_data['status'] == 'Success')
                        {

                            $check_pg = $this
                                ->obj_api
                                ->real_pg_check($query['lead_id']);

                            if ($check_pg)
                            {
                                redirect(base_url("payment_success_view_call_axis/" . $emp_id_encrypt));
                            }
                            else
                            {

                                $ProductInfo = '';
								
								$Source = "AX";
								$Vertical = "AXBBGRP";
								if($query['is_non_integrated_single_journey'] == 1){//ankita single link journey changes
                                    $PaymentMode = "PO";
									$Vertical = "AXSLGRP";
                                }else{
                                    $PaymentMode = "PP";
                                }
								$ReturnURL = base_url("payment_success_view_call_axis/" . $emp_id_encrypt);
								$UniqueIdentifier = "LEADID";
								$UniqueIdentifierValue = $query['lead_id'];
								$CustomerName = $query['emp_firstname']." ".$query['emp_lastname'];
								$Email = $query['email'];
								$PhoneNo = substr(trim($query['mob_no']), -10);
								$FinalPremium = round($query['premium'],2);
								
                                if ($query['product_code'] == 'R03')
                                {
                                    $ProductInfo = 'Axis Freedom Plus';
                                }
                                else if ($query['product_code'] == 'R07')
                                {
                                    $ProductInfo = 'Axis Health Pro';
                                }
                                else if ($query['product_code'] == 'R10')
                                {
                                    $ProductInfo = 'Group Activ Secure';
                                }
								else if ($query['product_code'] == 'R11')
                                {
                                    $ProductInfo = 'Health Pro Infinity';
                                   /* if($query['is_non_integrated_single_journey'] == 1){//ankita single link journey changes
                                        $ProductInfo = 'Group Active Health';
                                    }else{
                                        $ProductInfo = 'Health Pro Infinity';
                                    }*/
                                    
                                }

                                $CKS_data = $Source."|".$Vertical."|".$PaymentMode."|".$ReturnURL."|".$UniqueIdentifier."|".$UniqueIdentifierValue."|".$CustomerName."|".$Email."|".$PhoneNo."|".$FinalPremium."|".$ProductInfo."|".$this->hash_key;

                                $CKS_value = hash($this->hashMethod, $CKS_data);

                                $bank_data = json_decode($query['json_qote'], true);

                                $manDateInfo = array(
                                    "ApplicationNo" => $UniqueIdentifierValue,
                                    "AccountHolderName" => $CustomerName,
                                    "BankName" => ($bank_data['AXISBANKACCOUNT'] == 'Y') ? 'Axis Bank' : 'Other',
                                    "AccountNumber" => empty($bank_data['ACCOUNTNUMBER']) ? '' : $bank_data['ACCOUNTNUMBER'],
                                    "AccountType" => null,
                                    "BankBranchName" => empty($bank_data['BRANCH_NAME']) ? '' : $bank_data['BRANCH_NAME'],
                                    "MICRNo" => null,
                                    "IFSC_Code" => empty($bank_data['IFSCCODE']) ? '' : $bank_data['IFSCCODE'],
                                    "Frequency" => "As and when presented"//commented on - 6/4/21(as per CR by ABHI) "ANNUALLY"
                                );

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
                                $data = array(
                                    'REQUEST' => $encrypted
                                );

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

                                $request_arr = ["lead_id" => $query['lead_id'], "req" => "ecrypt-" . json_encode($data) . "decrypt-" . $decrypted, "res" => json_encode($result) , "product_id" => $query['product_code'], "type" => "payment_request_post"];

                                $dataArray['tablename'] = 'logs_docs';
                                $dataArray['data'] = $request_arr;
                                $this
                                    ->Logs_m
                                    ->insertLogs($dataArray);

                                if ($result && $result['Status'])
                                {

                                    $query_check = $this
                                        ->db
                                        ->query("select * from payment_txt_ids where lead_id='" . $query['lead_id'] . "'")->row_array();

                                    if (empty($query_check))
                                    {
                                        $data_arr = ["lead_id" => $query['lead_id'], "txt_id" => 1, "pg_type" => "New"];
                                        $this
                                            ->db
                                            ->insert("payment_txt_ids", $data_arr);
                                    }
                                    else
                                    {
                                        $update_arr = ["cron_count" => 0];
                                        $this
                                            ->db
                                            ->where("lead_id", $query['lead_id']);
                                        $this
                                            ->db
                                            ->update("payment_txt_ids", $update_arr);
                                    }

                                    //echo "WELCOME To ABHI";
                                    //$a='http://'.$result['PaymentLink'];
                                    //$var = $result['PaymentLink'];

                                    /*if(strpos($var, 'http://') !== 0) {
                                       redirect('http://' . $var,refresh);
                                    } 
                                    else{
                                    redirect($var,refresh);
                                    }*/
                                    redirect($result['PaymentLink']);
                                    
                                }
                                else
                                {
                                    if ($result['ErrorList'][0]['ErrorCode'] == 'E005')
                                    {
                                        //Payment already received - E005
                                        $check_pg = $this
                                            ->obj_api
                                            ->real_pg_check($query['lead_id']);
                                        if ($check_pg)
                                        {
                                            redirect(base_url("payment_success_view_call_axis/" . $emp_id_encrypt));
                                        }
                                        else
                                        {
                                            echo "Response on payment status is received. Post payment confirmation, proposal will be initiated. Thanks !! Error in enquiry API";
                                            exit;
                                        }
                                    }
                                    else if ($result['ErrorList'][0]['ErrorCode'] == 'E006')
                                    {
                                        //Payment initiated - E006
                                        $check_pg = $this
                                            ->obj_api
                                            ->real_pg_check($query['lead_id']);
                                        if ($check_pg)
                                        {
                                            redirect(base_url("payment_success_view_call_axis/" . $emp_id_encrypt));
                                        }
                                        else
                                        {

                                            $arr_update = ["is_payment_initiated" => 1];

                                            $this
                                                ->db
                                                ->where("lead_id", $query['lead_id']);
                                            $this
                                                ->db
                                                ->update("employee_details", $arr_update);

                                            echo "Response on payment status is pending. Post payment confirmation, proposal will be initiated. Thanks !!";
                                            exit;
                                        }
                                    }
                                    else
                                    {
                                        echo $result['ErrorList'][0]['Message'];
                                    }

                                }
                            }

                        }
                        else
                        {
                            redirect(base_url("/payment_error_view_call_axis/" . $emp_id_encrypt));

                        }

                    }

                
                // else
                // {
                //     echo "This policy cannot be processed since the said proposer has already purchased this policy";
                // }

            }
            else
            {
                echo "Payment link has been expired, Please get in touch with your Branch RM";
            }

        }
    }

    public function payment_success_view($emp_id_encrypt)
    {
         $emp_id = encrypt_decrypt_password($emp_id_encrypt, 'D');

        $encrypted = $this
            ->input
            ->post('RESPONSE');

        if ($encrypted)
        {
            $decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);
            $post_data = json_decode($decrypted, true);

            extract($post_data);

            if ($TxStatus && ($TxMsg == 'Payment Received' || $TxMsg == 'success') && $PaymentStatus == 'PR')
            {

               

                $TxStatus = "success";
                $TxMsg = "No Error";
            }
        }

        $query = $this
            ->db
            ->query("SELECT GROUP_CONCAT(p.id) proposal_id,ed.emp_id,ed.lead_id,mpst.product_code FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,payment_details AS pd,user_payu_activity as ua WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND p.id = pd.proposal_id AND ed.emp_id = ua.emp_id and (TIMESTAMPDIFF(SECOND, ua.created_time,now()) < 259200) AND ed.emp_id='" . $emp_id . "' GROUP BY p.emp_id")->row_array();
			
	
        if (!empty($query['proposal_id']))
        {

            $ids = explode(',', $query['proposal_id']);

            if (isset($TxRefNo))
            {
                $request_arr = ["lead_id" => $query['lead_id'], "req" => $encrypted, "res" => $decrypted, "product_id" => $query['product_code'], "type" => "payment_response_post"];

                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this
                    ->Logs_m
                    ->insertLogs($dataArray);

                $request_arr = ["payment_status" => $TxMsg, "premium_amount" => $amount, "payment_type" => $paymentMode, "txndate" => $txnDateTime, "TxRefNo" => $TxRefNo, "TxStatus" => $TxStatus, "json_quote_payment" => json_encode($post_data) ];

                $this
                    ->db
                    ->where_in('proposal_id', $ids);
                $this
                    ->db
                    ->where('TxStatus != ', 'success');
                $this
                    ->db
                    ->update("payment_details", $request_arr);

            }
		

            if (isset($Registrationmode))
            {
                $query_emandate = $this
                    ->db
                    ->query("select * from emandate_data where lead_id='" . $query['lead_id']."' ")->row_array();

                if ($EMandateStatus == 'MS')
                {
                    $mandate_status = 'Success';
                }
                elseif ($EMandateStatus == 'MI')
                {
                    $mandate_status = 'Emandate Pending';
                }
                elseif ($EMandateStatus == 'MR')
                {
                    $mandate_status = 'Emandate Received';
                }
                elseif ($EMandateStatus == '')
                {
                    $mandate_status = 'Emandate Pending';
                }
                else
                {
                    $mandate_status = 'Fail';
                }

                if ($query_emandate > 0)
                {

                    $arr = ["TRN" => $EMandateRefno, "status_desc" => $EMandateStatusDesc, "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($EMandateDate)) , "Registrationmode" => $Registrationmode, "EMandateFailureReason" => $EMandateFailureReason, "MandateLink" => $MandateLink];

                    $this
                        ->db
                        ->where("lead_id", $query['lead_id']);
                    $this
                        ->db
                        ->update("emandate_data", $arr);
					
                }
                else
                {

                    $arr = ["lead_id" => $query['lead_id'], "TRN" => $EMandateRefno, "status_desc" => $EMandateStatusDesc, "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($EMandateDate)) , "Registrationmode" => $Registrationmode, "EMandateFailureReason" => $EMandateFailureReason, "MandateLink" => $MandateLink];

                    $this
                        ->db
                        ->insert("emandate_data", $arr);
                }

                if ($mandate_status == 'Success')
                {
                    //$this->obj_api->send_message($query['lead_id'], 'success');
						//echo 3456;die;
                }

                if ($mandate_status == 'Fail')
                {
                    $this
                        ->obj_api
                        ->send_message($query['lead_id'], 'fail');
                }

                if ($paymentMode == 'PP' && ($Registrationmode == 'SAD' || $Registrationmode == 'EMI'))
                {
                    $this
                        ->obj_api
                        ->send_message($query['lead_id'], 'SAD_EMI_one');
                    $this
                        ->obj_api
                        ->send_message($query['lead_id'], 'SAD_EMI_two');
                }

            }

            if (isset($PaymentStatus) && $PaymentStatus == 'PI')
            {
                // echo 987;die;
                $check_pg = $this
                    ->obj_api
                    ->real_pg_check($query['lead_id']);
                if ($check_pg)
                {
                    redirect(base_url("payment_success_view_call_axis/" . $emp_id_encrypt));
                }
                else
                {
                    // echo 1234;die;

                    $arr_update = ["is_payment_initiated" => 1];

                    $this
                        ->db
                        ->where("lead_id", $query['lead_id']);
                    $this
                        ->db
                        ->update("employee_details", $arr_update);

                    echo "Response on payment status is pending. Post payment confirmation, proposal will be initiated. Thanks !!";
                    exit;
                }
            }

            $proposal_id = $ids[0];

            $payment_data = $this
                ->db
                ->query("select payment_status,TxStatus from payment_details where proposal_id='$proposal_id'")->row_array();

            if ($payment_data['TxStatus'] == 'success')
            {

                $check_result = $this
                    ->obj_api
                    ->policy_creation_call($query['lead_id']);

                if ($check_result['Status'] == 'Success')
                {  

                    $query_lead = $this->db->query("select lead_id from employee_details where emp_id='$emp_id'")->row_array();

                    $lead_id_mis = $query_lead['lead_id'];
    
                    $query_check_mis = $this->db->query("select id from mis_ack_coi where lead_id='$lead_id_mis'")->row_array();
    
                    if(empty($query_check_mis)){
                        $insert_mis_coi_table = $this->obj_home->save_ack_mis($query_lead['lead_id']);
                    }
                    

                    $data_policy[0] = $this
                        ->db
                        ->query("SELECT GROUP_CONCAT(DISTINCT(certificate_number)) certificate_number FROM api_proposal_response m WHERE m.emp_id = '$emp_id' GROUP BY emp_id")->row_array();

                    $data = $this
                        ->db
                        ->query("select ed.AXISBANKACCOUNT,mpst.product_name,p.proposal_no,ed.lead_id,pd.txndate,ed.emp_firstname from employee_policy_detail AS epd,product_master_with_subtype AS mpst,proposal as p,employee_details as ed,payment_details as pd  where epd.product_name = mpst.id and mpst.policy_subtype_id = epd.policy_sub_type_id and p.policy_detail_id=epd.policy_detail_id and p.emp_id = ed.emp_id and p.id = pd.proposal_id and ed.emp_id ='$emp_id'")->result_array();

                    $MandateLink_data = $this
                        ->db
                        ->query("select MandateLink,Registrationmode from emandate_data where lead_id = '" . $query['lead_id'] . "'")->row_array();
						
					$is_cust_journey = 1;

                    $this
                        ->load
                        ->employee_template_api("thankyou", compact('data_policy', 'data', 'MandateLink_data','is_cust_journey'));

                }
                else
                {

                    redirect(base_url("/payment_error_view_call_axis/" . $emp_id_encrypt));

                }

            }
            else
            {

                $data = $this
                    ->db
                    ->query("select p.proposal_no,ed.lead_id,ed.emp_firstname from proposal as p,employee_details as ed  where p.emp_id = ed.emp_id and ed.emp_id ='$emp_id'")->result_array();

                $this
                    ->load
                    ->employee_template_api("thankyou", compact('data'));
            }

        }
        else
        {

            echo "Payment link has been expired, Please get in touch with your Branch RM";

        }

    }

    // new payu old,new  cron
	public function all_cron_payu($check){

		if($check == 2){
			//echo "8 clock cron devolmnt pending";exit;

			$query1 = $this->db->query("SELECT DISTINCT e.lead_id,e.emp_id,e.product_id from proposal p,employee_details e,payment_details as pd WHERE p.emp_id = e.emp_id and p.id = pd.proposal_id and pd.payment_status = 'No Error' and p.status IN('Payment Received') and p.EasyPay_PayU_status = 1 and e.product_id in('R03','R07') and DATE(pd.updated_date) = DATE(NOW())")->result_array();

				if($query1)
				{
					foreach($query1 as $val)
					{
						$where_arr = ["emp_id"=>$val['emp_id'],"status"=>"Payment Received"];
						$arr = ["count" => 2];
						$this->db->where($where_arr);
						$this->db->update("proposal",$arr);
											
						$data = $this->obj_api->policy_creation_call($val['lead_id'], 1);
						//$data = json_decode($check_result,true);
			
						$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($data),"res" => json_encode($data) ,"product_id"=> $val['product_id'], "type"=>"8clock_cron"];
						$dataArray['tablename'] = 'logs_docs'; 
						$dataArray['data'] = $request_arr; 
						$this->Logs_m->insertLogs($dataArray);
						
						//echo $data['Status']." hii".$val['lead_id'];
						
					}
					
				}
			
		}else{
			
			echo "for new pg rollback";exit;
			
			// after 2020-11-17 (for new PG real pg status check)
			
			$query1 = $this->db->query("SELECT ed.lead_id,pt.id FROM employee_details as ed,proposal AS p,payment_txt_ids as pt WHERE ed.emp_id=p.emp_id AND ed.lead_id = pt.lead_id AND p.status IN('Payment Pending','Rejected') AND ed.product_id in('R03','R07') AND pt.pg_type = 'New' AND pt.cron_count < 2  limit 15")->result_array();

			if($query1)
			{
				
				foreach($query1 as $val1){
					
					$this->db->query("UPDATE payment_txt_ids SET cron_count = cron_count + 1 WHERE id =".$val1['id']);
								
						$check_pg = $this->obj_api->real_pg_check($val1['lead_id']);
								
						if($check_pg){
							$check_result = $this->obj_api->policy_creation_call($val1['lead_id'], 1);
						}
				}
				
			}
		
		}
		
	}


	/* cron */
	public function rehit_policy_create()
	{	
		
		$query = $this
		->db
		->query("SELECT ed.lead_id,ed.emp_id,ed.email,ed.mob_no,p.premium,g.QuotationNumber,mpst.payu_info_url,mpst.product_code,pt.txt_id,pt.pg_type,pt.id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,proposal AS p,ghi_quick_quote_response AS g,payment_txt_ids as pt WHERE epd.product_name = mpst.id AND p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND ed.lead_id = pt.lead_id AND g.`status` = 'success' AND p.status IN('Payment Pending','Rejected')  AND ed.product_id in('R03','R07') AND date(p.created_date) >= '2020-10-05' AND pt.cron_count < 2 group by pt.txt_id limit 15")->result_array();
	
		if($query)
		{
			
			foreach($query as $val){
				
				$this->db->query("UPDATE payment_txt_ids SET cron_count = cron_count + 1 WHERE id =".$val['id']);
	
				if($val['pg_type'] == 'PayU'){
						
						$key = "XGaHm4";
						$salt = "dC2qLagI";
						$wsUrl = "https://info.payu.in/merchant/postservice?form=2";
						// $key = "nAtwzQ";
						// $salt = "TqhIAHgl";
						// $wsUrl = "https://test.payu.in/merchant/postservice.php?form=2";
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
							$request_arr = ["lead_id" => $val['lead_id'],"req" => json_encode($qs), "res" => json_encode($err) ,"product_id"=> $val['product_code'], "type"=>"pg_status_curl_error_cron"];
							$dataArray['tablename'] = 'logs_docs'; 
							$dataArray['data'] = $request_arr; 
							$this->Logs_m->insertLogs($dataArray);
							
						}else{
							$valueSerialized = @unserialize($o);
							
							if($o === 'b:0;' || $valueSerialized !== false) {
								
								$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($qs) ,"res" => json_encode($o),"product_id"=> $val['product_code'], "type"=>"pg_status_error1_cron"];
								$dataArray['tablename'] = 'logs_docs'; 
								$dataArray['data'] = $request_arr; 
								$this->Logs_m->insertLogs($dataArray);
							}

							$rs = json_decode($o,true);
							$payUStatus = $rs['status'];
							$result = $rs['transaction_details'];
							$response_arr = $result[$val['txt_id']];

							if($payUStatus && $response_arr['status'] == 'success'){
								
								$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($qs) ,"res" => json_encode($o),"product_id"=> $val['product_code'], "type"=>"pg_status_success"];
								$dataArray['tablename'] = 'logs_docs'; 
								$dataArray['data'] = $request_arr; 
								$this->Logs_m->insertLogs($dataArray);
								
								$date = new DateTime($response_arr['addedon']);
								$txt_date = $date->format('m/d/Y g:i:s A'); 
								
								$arr = ["payment_status" => "No Error","premium_amount" => round($response_arr['transaction_amount'],2),"payment_type" => $response_arr['PG_TYPE'],"pgRespCode" => $response_arr['error_code'],"merchantTxnId" => $response_arr['txnid'],"SourceTxnId" => $response_arr['txnid'],"txndate" => $txt_date,"TxRefNo" => $response_arr['mihpayid'],"TxStatus"=>$response_arr['status'],"json_quote_payment"=>json_encode($response_arr)];
								
								$proposal_ids = $this->db->query("select GROUP_CONCAT(id) proposal_id from proposal where emp_id='".$val['emp_id']."'")->row_array();
								
								$ids = explode(',',$proposal_ids['proposal_id']);
								
								$this->db->where_in('proposal_id', $ids);
								$this->db->update("payment_details",$arr);	
								
								$check_result = $this->obj_api->policy_creation_call($val['lead_id'], 1);
								
							}else{
								
								$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($qs) ,"res" => json_encode($o),"product_id"=> $val['product_code'], "type"=>"pg_status_error2_cron"];
								$dataArray['tablename'] = 'logs_docs'; 
								$dataArray['data'] = $request_arr; 
								$this->Logs_m->insertLogs($dataArray);
								
								$this->db->where("emp_id",$val['emp_id']);
								$this->db->update("proposal",["count"=>"1"]);
								
							}
					}
					
				}
				
				
			}	
	
		}else{
				$query1 = $this->db->query("SELECT DISTINCT lead_id from proposal p,employee_details e,payment_details as pd WHERE p.emp_id = e.emp_id and p.id = pd.proposal_id and pd.payment_status = 'No Error' and p.status IN('Payment Received') and p.EasyPay_PayU_status = 1 and e.product_id in('R03','R07') and p.count < 3 and DATE(pd.updated_date) = DATE(NOW()) limit 5")->result_array();


				if($query1)
				{
					foreach($query1 as $val){
						$check_result = $this->obj_api->policy_creation_call($val['lead_id'], 1);
						//$check_result = json_decode($check_result,true);
						
						$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($check_result),"res" => json_encode($check_result) ,"product_id"=> "", "type"=>"payu_real_check_both"];
						$dataArray['tablename'] = 'logs_docs'; 
						$dataArray['data'] = $request_arr; 
						$this->Logs_m->insertLogs($dataArray);
												
					}
					
				}
				
		}
		
	}

}

?>
