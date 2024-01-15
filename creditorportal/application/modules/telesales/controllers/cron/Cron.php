<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include(APPPATH.'razorpay_php/Razorpay.php');
use Razorpay\Api\Api;



//use GCM\AESGCM\AESGCM;
include(APPPATH.'libraries/aes_gcm/src/AESGCM.php');
use AESGCM\AESGCM;


class Cron extends CI_Controller {
     

    function __construct() {
        parent::__construct();
        $this->load->model('cron/cron_m');
		$this
            ->load
            ->model("Logs_m", "Logs_m", true);
            /*ini_set('display_errors', 1);
         ini_set('display_startup_errors', 1);
         error_reporting(E_ALL);*/
    }
function encrypt($key, $textToEncrypt){
    $cipher = 'aes-256-gcm';
    $iv_len = 12;
    $tag_length = 16;
    $version_length = 3;
    $version = "v01";
    $iv = openssl_random_pseudo_bytes($iv_len);
    $tag = ""; // will be filled by openssl_encrypt
    $ciphertext = openssl_encrypt($textToEncrypt, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag, "", $tag_length);
    $encrypted = base64_encode($version.$iv.$ciphertext.$tag);
    return $encrypted;
}
    public function thanos_api_call($data,$type){
		//echo 123;die;
//use AESGCM\AESGCM;

// The Key Encryption Key
/*$K = hex2bin('feffe9928665731c6d6a8f9467308308feffe9928665731c');
// The data to encrypt (can be null for authentication)
$P = hex2bin('d9313225f88406e5a55909c5aff5269a86a7a9531534f7da2e4c303d8a318a721c3c0c95956809532fcf0e2449a6b525b16aedf5aa0de657ba637b39');

// Additional Authenticated Data
$A = hex2bin('feedfacedeadbeeffeedfacedeadbeefabaddad2');

// Initialization Vector
$IV = hex2bin('cafebabefacedbaddecaf888');

// $C is the encrypted data ($C is null if $P is null)
// $T is the associated tag
list($C, $T) = AESGCM::encrypt($K, $IV, $P, $A);
// The value of $C should be hex2bin('3980ca0b3c00e841eb06fac4872a2757859e1ceaa6efd984628593b40ca1e19c7d773d00c144c525ac619d18c84a3f4718e2448b2fe324d9ccda2710')
// The value of $T should be hex2bin('2519498e80f1478f37ba55bd6d27618c')

$P = AESGCM::decrypt($K, $IV, $C, $A, $T);
// The value of $P should be hex2bin('d9313225f88406e5a55909c5aff5269a86a7a9531534f7da2e4c303d8a318a721c3c0c95956809532fcf0e2449a6b525b16aedf5aa0de657ba637b39')
print_r($P);
die;*/
$key = '12345678901234567890123456789012';
$plaintext = 'The quick brown fox jumps over the lazy dog';
$ciphertext = $this->encrypt($key, $plaintext);
echo 'ciphertext: ' . $ciphertext . PHP_EOL;
die;

       $b64Doc = chunk_split(base64_encode(file_get_contents($data['pdf_url'])));
        $request = [
            "partnerName" => "ABHI",
            "channelName" => "NA",
            "documentDetails"=> [
                "leadId"=> $data['lead_id'],
                "applicationNo"=> "",
                "proposalNo"=>"",
                "policyNo"=> "",
                "memberId"=> $data['certificate_number'], 
                "issuanceDt"=> date("d-m-Y",strtotime($data['issuance_date'])),
                "docName"=> $data['pdf_file_name'],
                "docType"=> "OTP Log File",
                "docKey"=> "",
                "docFile"=> $b64Doc,
                "docURL"=> $data['pdf_url']
            ]

        ];
        

      //  echo json_encode($request);exit;
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://sakshamuat.axisbank.co.in/gateway/api/rcm/v1/docupload",
            CURLOPT_PROXY => "185.46.212.88",
            CURLOPT_PROXYPORT => 80,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 180,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($request),
            CURLOPT_SSLCERT => "ABHIAXIS.p12",
            CURLOPT_SSLCERTTYPE => "P12",
            CURLOPT_SSLCERTPASSWD => "abhi",
            CURLOPT_HTTPHEADER => array(
                "Content-Type:  application/json",
                "X-IBM-Client-Id: 2df37d2c-7415-4773-bf8f-0f395543b395",
                "X-IBM-Client-Secret: E5vI7qD5aO1sX2oP5oK6cP8lC6tH5bL3mW6wO0kR4lW3eT2kR7",
                "x-fapi-epoch-millis: ".time(),
                "x-fapi-channel-id: ABHI",
                "x-fapi-uuid : ".rand(),
                "cache-control: no-cache"
            ),
        ));
        $response = curl_exec($curl);
        //print_r(curl_error($curl));exit; 
        $req_send = ['req' => $request,'headers' => array(
                "Content-Type:  application/json",
                "X-IBM-Client-Id: 2df37d2c-7415-4773-bf8f-0f395543b395",
                "X-IBM-Client-Secret: E5vI7qD5aO1sX2oP5oK6cP8lC6tH5bL3mW6wO0kR4lW3eT2kR7",
                "x-fapi-epoch-millis: ".time(),
                "x-fapi-channel-id: ABHI",
                "x-fapi-uuid : ".rand(),
                "cache-control: no-cache"
            )];
        $logs_array['data'] = ["type" => "thanos_push_document",
                             "req" => json_encode($req_send), 
                             "res" => json_encode($response), 
                             "lead_id" => $data['lead_id'], 
                             "product_id" => $data['product_id']];
        $this->Logs_m->insertLogs($logs_array);
        return $response;
        
    }

    public function thanos_record_processing(){
        
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        //$this->db2 = $this->load->database('retail_view_external',TRUE);
       
        //$data = $this->db2->select('*')->from('OTP_REQUEST')->get()->result_array();
        //var_dump($data);exit;
        
       // $q = "SELECT ed.emp_id,ed.email,ed.mob_no,ed.otp_sent_on,ed.otp_validated_on,ed.lead_id,ed.product_id,ed.otp,ed.emp_firstname,ed.emp_lastname from employee_details ed, proposal p WHERE p.emp_id = ed.emp_id and p.status IN ('Success','Payment Received') AND  date(ed.otp_validated_on) = date(now()) AND ed.is_thanos_pdf_generated = 0 GROUP BY p.emp_id ";
         //$q = "SELECT ed.emp_id,ed.email,ed.mob_no,ed.otp_sent_on,ed.otp_validated_on,ed.lead_id,ed.product_id,ed.otp,ed.emp_firstname,ed.emp_lastname from employee_details ed, proposal p WHERE p.emp_id = ed.emp_id and p.status IN ('Success','Payment Received') AND  date(ed.otp_validated_on) = '2022-03-23' AND ed.is_thanos_pdf_generated = 0 GROUP BY p.emp_id ";
        $q = "SELECT apr.created_date AS issuance_date,GROUP_CONCAT(apr.certificate_number) as certificate_number,ed.emp_id,ed.email,ed.mob_no,ed.otp_sent_on,ed.otp_validated_on,ed.lead_id,ed.product_id,ed.otp,ed.emp_firstname,ed.emp_lastname from employee_details ed, proposal p, api_proposal_response apr WHERE p.emp_id = ed.emp_id AND p.id=apr.proposal_id and p.status IN ('Success','Payment Received') AND  ed.lead_id IN (202232300,20223191) AND ed.is_thanos_pdf_generated = 0 GROUP BY p.emp_id ";
        $data = $this->db->query($q)->result_array();
      
        /* Dompdf code start */
        
        include_once APPPATH . 'third_party/dompdf/autoload.inc.php';
        
        
        if (!is_dir(APPPATH.'resources/uploads/thanos/')) {                 
            mkdir(APPPATH.'resources/uploads/thanos/', 0777, true);
        }
        
        foreach ($data as $key => $value) {
            //check if leadid is already present in processing table or not else make entry
            /*$checkData = $this->db->get_where("thanos_group_records_processing",["lead_id" => $value['lead_id']])->row_array();
            if(empty($checkData)){
                //insert into table
                $arr = ["lead_id" => $value['lead_id'],
                        "product_id" => $value['product_id'],
                        "status" => "Pending",
                        "created_date" => date("Y-m-d H:i:s"),
                        "updated_date" => date("Y-m-d H:i:s")];
                $this->db->insert("thanos_group_records_processing",$arr);
                
            }*/
            $dompdf = new Dompdf\Dompdf(); 
            $value['msg'] = "Hi, use OTP ".$value['otp']." to raise a service request. OTP is valid for 15 mins. Regards, Aditya Birla Health Insurance";
            $html = $this->load->view("thanos_pdf",$value,TRUE);
            $dompdf->loadHtml($html);
            $dompdf->render();
            $output_pdf = $dompdf->output();
            file_put_contents(APPPATH . 'resources/uploads/thanos/' . "otp_log_".$value['lead_id'] . '.pdf', $output_pdf);

            $fdata = [
            'lead_id' => $value['lead_id'],
            'req' => base_url() . 'resources/uploads/thanos/' . "otp_log_".$value['lead_id'] . '.pdf',
            'res' => '',
            'product_id' => $value['product_id'],
            'type' => "thanos_otp_log",
            ];

            $this->db->insert('logs_docs', $fdata);
            echo $value['lead_id'].' -- '.$value['product_id'].'<br><br>';
            $dataArr = $value;
            /* Convert PDF to byte array */
            /*$file = file_get_contents(APPPATH.'resources/uploads/thanos/'.$value['lead_id'].'.pdf');
            $byte_array = unpack("C*",$file);
            $base64_encode = base64_encode(serialize($byte_array));
            var_dump($base64_encode);*/ 
            //convert pdf into base64 encoded string
            //$content = file_get_contents(APPPATH.'resources/uploads/thanos/'.$value['lead_id'].'.pdf');
            //Decode pdf content
            //$pdf_encoded = base64_encode($content);
            //$dataArr['encoded_pdf'] = $pdf_encoded;            
            $dataArr['pdf_file_name'] = "otp_log_".$value['lead_id'] . '.pdf';
            $dataArr['pdf_url'] = base_url().'resources/uploads/thanos/' . "otp_log_".$value['lead_id'] . '.pdf';
            $data = $this->thanos_api_call($dataArr , 'Group');exit;

            //update flag in employee
            $update_query = $this->db->query("update employee_details set is_thanos_pdf_generated = 1, thanos_pdf_status = 'Success', thanos_status_updated_date = '".date("Y-m-d H:i:s")."' where emp_id = '".$value['emp_id']."'");
            
        }
        echo "Cron Executed Successfully ! on ".count($data). " records !!";
    }
public function renewal_check_easypay()
{
$query = $this->db->query("SELECT ed.emp_id,emd.company_uid,pd.premium_amount,ed.lead_id,ed.product_id,pd.account_no,pd.payment_mode,STR_TO_DATE(api.end_date, '%m/%d/%Y ')AS renewalDate,GROUP_CONCAT(api.certificate_number) as certificate_number
                FROM emandate_data AS emd, employee_details AS ed, proposal AS p, payment_details AS pd,api_proposal_response AS api
                WHERE emd.lead_id = ed.lead_id AND p.emp_id = ed.emp_id AND p.id = pd.proposal_id AND ed.product_id IN ('R03','R07','R10','R11')  AND pd.payment_mode = 'Easy Pay' AND p.proposal_no=api.proposal_no_lead AND pd.si_auto_renewal = 'Y' AND is_lead_cron_check = 0 AND STR_TO_DATE(api.end_date, '%m/%d/%Y ') BETWEEN DATE(NOW() - interval 1 day) AND DATE(DATE_ADD(NOW(), INTERVAL 7 DAY)) AND emd.status_desc = 'Success' AND  p.status IN ('Success') GROUP BY emd.lead_id")->result_array();

if(empty($query)){
foreach($query as $data_val)
{//echo 123;die;
	$url = "https://bizpre.adityabirlahealth.com/ABHICL_GroupRenewal/Service1.svc/CombiGroupRenewalCheck";
    $emp_id = $data_val['emp_id'];
		$data = array(
            "Lead_Id" => '',
            "master_policy_number" => "",
            "certificate_number" => "GHI-CM-21-2000521",
            "dob" => "",
            "proposer_mobileNumber" => "",
        );
        $data_string = json_encode($data);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/json"
            ),
        ));

        $result = curl_exec($curl);
//print_pre($result);
        $info = curl_getinfo($curl);
        $response_time_renewal = $info['total_time'];
        curl_close($curl);

		 $status = 'Success';
        $djson = json_decode($result, TRUE);
		$ErrorCode = $djson['error'][0]['ErrorCode'];
        $ErrorMessage = $djson['error'][0]['ErrorMessage'];
	
		     if ($ErrorCode != '00' && !empty($djson)) {
				 $status = 'Failed';
            $output = ['error' => ['ErrorCode' => $ErrorCode, 'ErrorMessage' => 'Failed', 'output_msg' => $ErrorMessage]];
            echo json_encode($output);
            exit;
        }else if(empty($djson)){
			 $status = 'Failed';
            $output = ['error' => ['ErrorCode' => '0010', 'ErrorMessage' => 'Failed', 'output_msg' => 'could not get details, please try again!']];
            echo json_encode($output);
            exit;
        }
		 if (!empty($djson['response']['policyData'][0]['Sum_insured_type'])) {
            $policy_fi_type = "Family Floater";
        } else if (!empty($djson['response']['policyData']['Sum_insured_type'])) {
            $policy_fi_type = "Individual";
        }
		$is_Renewable_Flag = 0;
		$is_Renewed_Flag = 0;
		$$is_Policy_lapsed_flag = 0;
		$is_cron_check = 0;

        if ($policy_fi_type = "Family Floater") {
            $Policy_lapsed_flag =  strtoupper($djson['response']['policyData'][0]['Policy_lapsed_flag']);
            $Renewed_Flag =  strtoupper($djson['response']['policyData'][0]['Renewed_Flag']);
            $Renewable_Flag =  strtoupper($djson['response']['policyData'][0]['Renewable_Flag']);
        } else if ($policy_fi_type = "Individual") {
            $Policy_lapsed_flag =  strtoupper($djson['response']['policyData']['Policy_lapsed_flag']);
            $Renewed_Flag =  strtoupper($djson['response']['policyData']['Renewed_Flag']);
            $Renewable_Flag =  strtoupper($djson['response']['policyData']['Renewable_Flag']);
        }
		  $product_array = $djson['response']['policyData'];
       
        $FinalPremium = 0;
        $FinalPremiumBreakup = '';

        //23-11-2021
     
        $proposal_no = "";
        
        // 27-12-2021
        $old_paid_premium = 0;
			if ($ErrorCode == '00') {
				$sel_query = $this->db->query("select gross_premium,client_id from api_proposal_response where emp_id = '$emp_id'")->result_array();
				foreach($sel_query as $value_query){
					$old_gross_premium = $value_query['gross_premium'];
					$client_id = $value_query['client_id'];
				$update_query = $this->db->query("update api_proposal_response set old_gross_premium = '$old_gross_premium' where client_id = '$client_id'");

					
				}

			}
        foreach ($product_array as $key => $single_product) {
			$renewal_gross_premium_payable = $single_product['premium']['Renewal_Gross_Premium'];
			$customer_code_payable = ltrim($single_product['Customer_Code'],0);
			if ($ErrorCode == '00' ) {
			$update_query = $this->db->query("update api_proposal_response set gross_premium = '$renewal_gross_premium_payable' where client_id = '$customer_code_payable'");
			}
			$FinalPremium = $FinalPremium + $single_product['premium']['Renewal_Gross_Premium'];
        }
		if ($ErrorCode == '00' ) {
			$is_Policy_lapsed_flag = $Policy_lapsed_flag;
			$is_Renewed_Flag = $Renewed_Flag;
			$is_Renewable_Flag = $Renewable_Flag;
			$is_cron_check = 1;
			
		}
		$data_arr = array(
		'is_Renewable_Flag' => $is_Renewable_Flag,
		'is_Renewed_Flag'   => $is_Renewed_Flag,
		'is_lapsed_flag'    =>  $is_Policy_lapsed_flag,
		'is_lead_cron_check' => $is_cron_check,
		'renewal_gross_Premium' => $FinalPremium,
		'renewal_status_check' => $status,
		'renewal_res' => $result
		);

		$this->db->where('lead_id',$data_val['lead_id']);
		$this->db->update('emandate_data',$data_arr);
		
		echo "Record Updated Successfully";
		

}
}else{echo "No Record Found";
}
}
    public function combi_group_do_address_api($coi_number){
		//print_Pre($coi_number);die;
        $otherdataapi="https://bizpre.adityabirlahealth.com/ABHICL_HealthCoreXML/Service1.svc/PolicyDetail/".$coi_number."/null";
        $othercurl = curl_init();
        curl_setopt_array($othercurl, array(
            CURLOPT_URL => $otherdataapi,
            CURLOPT_RETURNTRANSFER => true,    
        ));
        $otherresult = curl_exec($othercurl);
        curl_close($othercurl);        
        // $otherdata=json_encode($otherresult,TRUE);
        $xml_snippet = simplexml_load_string( $otherresult );
        $other_json_convert = json_encode( $xml_snippet,TRUE );
        return $other_json_convert;

    }
 function process_si_debit_txn_data(){

	// echo 123;die;
        $q = "SELECT * FROM tbl_si_otp_debit_process WHERE type = 'upload' AND file_processed = 0 order by id desc limit 1";
        $res = $this->db->query($q)->row_array();

        if(!empty($res)){
            $dq = "SELECT * FROM tbl_si_debit_txn_dump WHERE is_record_processed = 0 AND is_record_validated = 0 AND has_errors = 0 AND excel_id = '".$res['id']."'";
           
            $data = $this->db->query($dq)->result_array();
	
            $file_has_error = 0;
            foreach ($data as $key => $val) {
				
		//print_pre($this->db->last_query());die;
                $this->db->where("id",$res['id']);
                $this->db->update("tbl_si_otp_debit_process",array("status" => "Processing"));
                $arr = [ "reject_reason" => $val['remarks'], "remarks1" => $val['remark1'],"debit_account_no" => $val['debit_account'],"bill_debit_date"=>$val['bill_debit_date'],"debit_bill_amount"=>$val['debit_bill_amount'],"invoice_no"=>$val['invoice_no'],"is_record_debit_processed" => 1];
                if($val['status'] != ''){
                    $arr["status"] = $val['status'];
                    $arr["mandate_date"] = date("Y-m-d H:i:s");
                }

                if($val['lead_id'] != '' || $val['product_id'] != ''){
					
                    $checkdata = $this->db->get_where("emandate_data",["lead_id"=>$val['lead_id'], "product_id"=>$val['product_id'] ])->row_array();
    

		 
			 	
					if(!empty($checkdata)){
						
                        $this->db->where("lead_id",$val['lead_id']);
                        $this->db->where("product_id",$val['product_id']);
                        $this->db->where("is_record_debit_processed",0);
						$this->db->where("is_Renewable_Flag",'YES');
                        $this->db->update("emandate_data",$arr);
							
							
					
                        if($this->db->affected_rows() > 0){
							

					    //si_emandate_data
                            if($val['status'] != ''){
						
							$this->send_communication_si_debit($val['status'],$val['lead_id'],$val['remarks'],$val['invoice_no'],$val['end_date'],$checkdata['registration_status_updated_on'],$checkdata['si_mandate_date'], $checkdata['renewal_gross_Premium']);

                                if(strtolower($val['status']) == 'success'){
									$checkdata_res = $this->db->get_where("emandate_data",["lead_id"=>$val['lead_id'], "product_id"=>$val['product_id'] ])->row_array();

                                    $this->renewal_policy_generate($val['lead_id'],$checkdata_res);
                                }
                               
                            $this->db->where("id",$val['id']);
                           $this->db->update("tbl_si_debit_txn_dump",array("record_processed_on"=>date("Y-m-d H:i:s"),"is_record_processed" => 1,"is_record_validated"=>1,"has_errors"=>0,"error_description" => "data updated successfully!"));
                        
							}
						
                           
                        }else{
                            $file_has_error = 1;
                           $this->db->where("id",$val['id']);
                          $this->db->update("tbl_si_debit_txn_dump",array("record_processed_on"=>date("Y-m-d H:i:s"),"is_record_processed" => 1,"is_record_validated"=>1,"has_errors"=>1,"error_description" => "duplicate lead found"));
                        }
                    }else{
                        $file_has_error = 1;
                          $this->db->where("id",$val['id']);
                          $this->db->update("tbl_si_debit_txn_dump",array("record_processed_on"=>date("Y-m-d H:i:s"),"is_record_processed" => 1,"is_record_validated"=>1,"has_errors"=>1,"error_description" => "lead not found"));
                    }
                    
                }else{
                    $file_has_error = 1;
                  $this->db->where("id",$val['id']);
                  $this->db->update("tbl_si_debit_txn_dump",array("record_processed_on"=>date("Y-m-d H:i:s"),"is_record_processed" => 1,"is_record_validated"=>1,"has_errors"=>1,"error_description" => "lead id or product id not found"));
                    
                }
                
                
                
            }
            if($file_has_error == 1){
                $status = "Failed";
            }else{
                $status = "Success";
            }
            $this->db->where("id",$res['id']);
            $this->db->update("tbl_si_otp_debit_process",array("status" => $status, "file_processed" => 1,"file_has_errors"=>$file_has_error));
            echo "Cron executed successfully !!";
        }else{
            echo "No data found !cron executed successfully get_profile!!";
        }
        
    }

	function renewal_policy_generate($lead_id,$checkdata)
	{
		//print_pre($checkdata);die;
		$res = $checkdata['renewal_res'];
		//print_r($res);die;
		$data['customer_data'] = (array)$this->get_profile_customer($lead_id);
		
		$emp_id = $data['customer_data']['emp_id'];
		$product_id = $data['customer_data']['product_id'];
		$product_master_data =  $this->db->query("select * from proposal where product_id = '$product_id'")->row_array();
		 $proposal_data = $this->db->query("select * from proposal where emp_id = '$emp_id'")->row_array();
		 $res = json_decode($res, TRUE);
		 $product_array = $res['response']['policyData'];
		 $apr_res = [];
		 $apr_proposal_id = [];
		 $data_api_res = $this->db->query("select * from api_proposal_response where emp_id = '$emp_id'")->result_array();
	    	 foreach ($data_api_res as $res_val) {
			$apr_res [] = $res_val['proposal_no_lead'];
			//$apr_proposal_id [] = $res_val['proposal_id'];
			 }
			 //print_r($apr_proposal_id);die;
			 $data_apr_res = array_unique($apr_res);
			 $apr_result =  implode(', ', $data_apr_res);
			 $apr_proposal_ids = implode(', ',$apr_proposal_id);
			
		 $customer_code = $data_api_res[0]['client_id']; 	
		 $data_apr_json = json_encode($data_api_res);
		 $policyData =  array();
			$collection_amount = 0;
         //$data['member_data'] = (array)$this->get_all_member_data($data['customer_data']['emp_id'], $policy_no);
          
		 foreach($product_array as $key => $single_product){
           
            $member_details = array();


            foreach($single_product['Members'] as $member_key => $single_member_res){

                if($hr_amount_status == "yes"){
                    $member_details_single = array(
                        "member_code" => $single_product['Members'][$member_key]["Member_Code"],
                        "sum_insured" => $single_product['Members'][$member_key]['MemberproductComponents'][0]["SumInsured"],
                        "health_return" => $single_product['Members'][$member_key]['MemberproductComponents'][0]["Hr_Amount"],
                    );
                }
                else{
                    $member_details_single = array(
                        "member_code" => $single_product['Members'][$member_key]["Member_Code"],
                        "sum_insured" => $single_product['Members'][$member_key]['MemberproductComponents'][0]["SumInsured"],
                        "health_return" => "0",
                       
                    );
                }
                
                array_push($member_details, $member_details_single);
            }
            $cer_no = $single_product["Certificate_number"];
			$get_source = $this->db->query("select mst.HB_source_code from api_proposal_response as apr,proposal as p,employee_policy_detail as epd,product_master_with_subtype as mst where apr.proposal_no_lead = p.proposal_no and p.policy_detail_id = epd.policy_detail_id and epd.product_name = mst.id  and apr.certificate_number ='$cer_no'")->row_array();
          
		  //1-12-2021
            $policyData_single = array(
            "certificate_number" => $single_product["Certificate_number"],
            "master_policy_number" => $single_product["MaterPolicyNumber"],
            "go_green" => '1',
            "premium" => $single_product['premium']['Renewal_Gross_Premium'],
            "product_code" => $single_product['PolicyproductComponents'][0]['SchemeCode'],
            "ref_code1" =>$data['customer_data']['ref1'] ,
            "ref_code2" => $data['customer_data']['ref2'],
            "intermediary_code" => $proposal_data['IMDCode'],
            "lead_Id" => $lead_id,
            "sp_Id" => "",
            "source_name" =>$get_source['HB_source_code'],
            "member_details" => $member_details,
            );

          
            array_push($policyData, $policyData_single);
            $collection_amount += $single_product['premium']['Renewal_Gross_Premium'];

            $proposar_name = $single_product["Name_of_the_proposer"];
        }
		      $receiptobj[] =  array(
            "company_code"=> "",
            "system_code"=> "",
            "office_location"=> "Mumbai",
            "mode_of_entry"=> "DIRECT",
            "cd_ac_no"=> "",
            "expiry_date"=> "",
            "payer_type"=> "Customer",
            "payer_code"=> $customer_code,
            "payment_by"=> "Customer",
            "payment_by_name"=> $proposar_name,
            "payment_by_relationship"=> "Self",
            "collection_amount"=> $collection_amount,
            "collection_rcvd_date"=> $checkdata['bill_debit_date'],
            "collection_mode"=> "Debit/ Credit Card",
            "remarks"=> "",
            "instrument_number"=> $checkdata['invoice_no'],
            "instrument_date"=>  $checkdata['bill_debit_date'],
            "bank_name"=> "",
            "branch_name"=> "",
            "micr_no"=> "",
            "bank_location"=> "",
            "cheque_type"=> "",
            "ifsc_code"=> "",
            "receipt_type"=> "NEW PAYMENT",
            "deposit_type"=> "",
            "deposit_bank"=> ""
        );
$data = array(

            "policyData" => $policyData,
            "receiptobj" => $receiptobj
        );

		  $data = json_encode($data);
        $data = preg_replace('/\\\"/',"\"", $data);
        // echo $data;exit;
	
	//print_pre($data);die;
	
        $url = "https://bizpre.adityabirlahealth.com/ABHICL_GroupRenewal/Service1.svc/CombiGroupRenewalGeneration";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/json"
            ),
        ));

        $result = curl_exec($curl);
		
		//return true;

        $info = curl_getinfo($curl);
        $response_time_renewal = $info['total_time'];
        curl_close($curl);
      $djson = json_decode($result, TRUE);
  //print_r($djson);die;
		
       
      // print_r($djson);die;
	         $fdata = [
            'lead_id' => $lead_id,
            'req' => json_encode($data),
            'res' => $result,
			'product_id' => $product_id,
            'type' => "full_quote_request_renewal",
        ];

        $this->db->insert('logs_docs', $fdata);
        $data_r['status'] = 'success';
        $data_r['lead_id'] = $lead_id;
        if($djson['error'][0]['ErrorCode'] != '00'){
            $data_r['status'] = 'failed';
        }

        if($djson['error'][0]['ErrorCode'] == '00'){
			
			$renew_arr = ['is_Renewable_Flag' =>'NO','is_Renewed_Flag'=>'YES'];
					$this->db->where('lead_id', $lead_id);
					$this->db->update('emandate_data',$renew_arr);
					//echo 1234;die;
					$exp_data = array(
					'emp_id'=> $emp_id,
					//'proposal_id'=>$apr_proposal_ids,
					'proposal_no_lead'=>$apr_result,
					'json_pr_data'=>$data_apr_json,
					'created_at'=>date("Y-m-d H:i:s")

					);
					//print_Pre($exp_data);die;
					$this->db->insert('expired_certificate',$exp_data);
            $cert_arr = [];
            foreach($djson['response'] as $single_response){
            array_push($cert_arr, $single_response['new_certificate_number']);

					$apr_cer_data = [
					
						'certificate_number' => $single_response['new_certificate_number'],
						'proposal_no' => $proposal_number,
						'start_date'=> $single_response['policy_start_date'],
						'end_date' => $single_response['Policy_end_date'],
						'ReceiptNumber' => $single_response['receipt_no'],
						'Renewed_flag' => 'YES'
			];
			//print_pre($apr_cer_data);die;
			$client_id_cl = $single_response['customer_code'];
			$client_id_remove = ltrim($single_response['customer_code'],0);
			$client_res = $this->db->query("select client_id from api_prorposal_response where client_id = '$client_id_cl' or client_id = '$client_id_remove'")->row_array();
			if(!empty($client_res)){
				$client_up = $client_res['client_id'];
					$this->db->where('client_id', ltrim($single_response['customer_code'],0));
					$this->db->update('api_proposal_response',$apr_cer_data);
					print_pre( "Policy Renewable Number -".$single_response['new_certificate_number']);
					
			}else{ echo 'No Record Found';}
			
            }
	}
	return true;
	}
	public function send_si_otp_reminder_renewal()
	{
		$querys = $this->db->query("SELECT emd.renewal_gross_Premium,ed.lead_id,emd.bill_debit_date,emd.debit_bill_amount
                FROM emandate_data AS emd, employee_details AS ed, proposal AS p, payment_details AS pd,api_proposal_response AS api
                WHERE emd.lead_id = ed.lead_id AND p.emp_id = ed.emp_id AND p.id = pd.proposal_id AND ed.product_id IN ('R03','R07','R10','R11')  AND pd.payment_mode = 'Easy Pay' AND p.proposal_no=api.proposal_no_lead AND pd.si_auto_renewal = 'Y' AND  DATE_ADD(STR_TO_DATE(end_date, '%m/%d/%Y '), INTERVAL 1 DAY) = DATE(DATE_ADD(NOW(), INTERVAL 10 DAY)) AND emd.status_desc = 'Success' AND  p.status IN ('Success') AND emd.renewal_debit_reminder = 0 AND emd.is_Renewable_Flag = 'YES' GROUP BY emd.lead_id");
		
		 if(!empty($querys)){

                foreach($querys->result_array() as $val)

		{
			
			$lead_id = $val['lead_id'];
			$query_check_all = $this->db->query(" SELECT ed.product_id,apr.gross_premium,apr.end_date,p.premium,p.created_date,mpst.policy_subtype_id,ed.emp_id,ed.lead_id,ed.email,ed.mob_no,ed.emp_firstname,ed.emp_middlename,ed.emp_lastname,mpst.product_code,mpst.click_pss_url,mpst.product_name,ed.json_qote,mpst.plan_code AS master_plan,apr.certificate_number AS certificate_number FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal AS p,api_proposal_response AS apr WHERE p.emp_id = ed.emp_id AND epd.product_name = mpst.id AND p.policy_detail_id = epd.policy_detail_id AND apr.proposal_no_lead = p.proposal_no AND ed.lead_id =".$lead_id)->result_array();


        
            //end_date from api_proposal response date format is m/d/Y
            $plan_name = [];
            $coi_no = [];
            $premium_amt = [];
            $total_gross_premium = 0;
            $premium_GFB = 0;
            foreach ($query_check_all as $key => $value) {
                if($value['policy_subtype_id'] == 1){
                    $pro_name = 'Group Activ Health';
                }else if($value['policy_subtype_id'] == 8){
                    $pro_name = 'Group Protect';
                }else if($value['policy_subtype_id'] == 2){
                    $pro_name = 'Group Activ Secure';
                }else if($value['policy_subtype_id'] == 3){
                    $pro_name = 'Group Activ Secure';
                }
                array_push($coi_no, $value['certificate_number']);
                array_push($plan_name, $pro_name);
               array_push($premium_amt, $value['gross_premium']);
                if(is_numeric($value['gross_premium'])){
                    $total_gross_premium += floatval($value['gross_premium']);
                }
                if($value['policy_subtype_id'] != 1){
                    $premium_GFB += floatval($value['gross_premium']);
                }
                
            }
            if($query_check_all[0]['product_id'] == 'R10'){
                $premium_GFB = '';
            }
            
            $query_check = $query_check_all[0];
            if($query_check){
                $si_end_date = date('d/m/Y',strtotime($query_check['end_date']. " + 1 day"));
                //echo $query_check['end_date'].'--'.$si_end_date;exit;
                $json_data = json_decode($query_check['json_qote'],true);
                
                $senderID = 1;
                
                
                $si_renewal_date = '';
                $alertID = '';
                if($si_end_date != ''){
                    /*$si_mandate_date_new = str_replace("/", "-", $si_end_date);                
                    $si_renewal_date1 = date("d-m-Y", strtotime(date("d-m-Y", strtotime($si_mandate_date_new)) . " + 1 day"));
                    $si_renewal_date = str_replace("-", "/", $si_renewal_date1);*/
                    $si_renewal_date = date('d/m/Y',strtotime($query_check['end_date']. " + 1 day"));
                }
                $full_name = trim($query_check['emp_firstname'].' '.$query_check['emp_middlename'].' '.$query_check['emp_lastname']);
                if(strlen($full_name) > 30){
                    $full_name = substr($full_name, 0, 30);
                }
					$data['alertID'] = 'A1609';
                    $data['AlertV1'] = $full_name;
                    $data['AlertV2'] = $val['renewal_gross_Premium'];//waiting for confirmation from abhi
                    $data['AlertV3'] = 'Axis bank Ltd';
                    $data['AlertV4'] = 'GROUP';
                    $data['AlertV5'] = $coi_no[0];//COI Number1
                    $data['AlertV6'] = "";
                    $data['AlertV7'] = (isset($coi_no[1])) ? $coi_no[1] : "";//COI Number2
                    $data['AlertV8'] = (isset($coi_no[0])) ? $si_renewal_date : "";//Date of Auto Debit Due1
                    $data['AlertV9'] = (isset($coi_no[0])) ? $si_renewal_date : "";//Renewal Due Date1
                    $data['AlertV10'] = $premium_amt[0];//Renewal Premium Payable1;
                    $data['AlertV11'] = $registration_status_updated_on;//Date Of Auto Debit Registration1
                    $data['AlertV12'] = (isset($coi_no[1])) ? $si_renewal_date : "";//Renewal Due Date2
                    $data['AlertV13'] = (isset($premium_amt[1])) ? $premium_amt[1] : "";//Renewal Premium Payable2
                    $data['AlertV14'] = $registration_status_updated_on;//Date Of Auto Debit Registration2
					$data['AlertV15'] = (isset($coi_no[2])) ? $si_renewal_date : "";//Date of Auto Debit Due2
                    $data['AlertV16'] = (isset($coi_no[2])) ? $coi_no[2] : "";//COI Number3;
                    $data['AlertV17'] =  (isset($coi_no[2])) ? $si_renewal_date : "";//Renewal Due Date3;
                    $data['AlertV18'] = (isset($premium_amt[2])) ? $premium_amt[2] : "";//Renewal Premium Payable3;
                    $data['AlertV19'] = (isset($coi_no[2])) ? $registration_status_updated_on : "";//Date Of Auto Debit Registration3;         
                    $data['AlertV20'] = (isset($coi_no[2])) ? $si_renewal_date : "";//Date of Auto Debit due3;       
                    $data['AlertV21'] = (isset($coi_no[3])) ? $coi_no[3] : "";//COI Number4;
                    $data['AlertV22'] = (isset($coi_no[3])) ? $si_renewal_date : "";//Renewal Due Date4;
                    $data['AlertV23'] = (isset($premium_amt[3])) ? $premium_amt[3] : "";//Renewal Premium Payable3;
                    $data['AlertV24'] = (isset($coi_no[3])) ? $registration_status_updated_on : "";//Date Of Auto Debit Registration4;                
                    $data['AlertV25'] = (isset($coi_no[3])) ?$si_renewal_date : "";//Date of Auto Debit Due4;                
                    $data['AlertV26'] = (isset($coi_no[4])) ? $coi_no[5] : "";//COI Number5;
                    $data['AlertV27'] = (isset($coi_no[4])) ? $si_renewal_date : "";//Renewal Due Date5;
                    $data['AlertV28'] = (isset($premium_amt[4])) ? $premium_amt[4] : "";//Renewal Premium Payable5;
                    $data['AlertV29'] = (isset($coi_no[4])) ? $registration_status_updated_on : "";//Date Of Auto Debit Registration5;                
                    $data['AlertV30'] = (isset($coi_no[4])) ? $si_renewal_date :"";//Date of Auto Debit Due5;
                    $data['AlertV31'] =  (isset($plan_name[0])) ? $plan_name[0] : "";//Product Name1             
                    $data['AlertV32'] = (isset($plan_name[1])) ? $plan_name[1] : "";//Product Name2
                    $data['AlertV33'] = (isset($plan_name[2])) ? $plan_name[2] : "";//Product Name3
                    $data['AlertV34'] = (isset($plan_name[3])) ? $plan_name[3] : "";//Product Name4
                    $data['AlertV35'] = (isset($plan_name[4])) ? $plan_name[4] : "";//Product Name5
		
		 $parameters =[
                    "RTdetails" => [
                   
                            "PolicyID" => '',
                            "AppNo" => 'HD100017934',
                            "alertID" => $data['alertID'],
                            "channel_ID" => $query_check['product_name'],
                            "Req_Id" => 1,
                            "field1" => '',
                            "field2" => '',
                            "field3" => '',
                            "Alert_Mode" => 3,
                            "Alertdata" => 
                                [
                                    "mobileno" => substr(trim($query_check['mob_no']), -10),
                                    "emailId" => $query_check['email'],
                                    "AlertV1" => $data['AlertV1'],
                                    "AlertV2" => $data['AlertV2'],
                                    "AlertV3" => $data['AlertV3'],
                                    "AlertV4" => $data['AlertV4'],
                                    "AlertV5" => $data['AlertV5'],
                                    "AlertV6" => $data['AlertV6'],
                                    "AlertV7" => $data['AlertV7'],
                                    "AlertV8" => $data['AlertV8'],
                                    "AlertV9" => $data['AlertV9'],
                                    "AlertV10" => $data['AlertV10'],
                                    "AlertV11" => $data['AlertV11'],
                                    "AlertV12" => $data['AlertV12'],
                                    "AlertV13" => $data['AlertV13'],
                                    "AlertV14" => $data['AlertV14'],
									"AlertV15" => $data['AlertV15'],
                                    "AlertV16" => $data['AlertV16'],
                                    "AlertV17" => $data['AlertV17'],
                                    "AlertV18" => $data['AlertV18'],
                                    "AlertV19" => $data['AlertV19'],
                                    "AlertV20" => $data['AlertV20'],
                                    "AlertV21" => $data['AlertV21'],
                                    "AlertV22" => $data['AlertV22'],
                                    "AlertV23" => $data['AlertV23'],
                                    "AlertV24" => $data['AlertV24'],
                                    "AlertV25" => $data['AlertV25'],
                                    "AlertV26" => $data['AlertV26'],
                                    "AlertV27" => $data['AlertV27'],
                                    "AlertV28" => $data['AlertV28'],
                                    "AlertV29" => $data['AlertV29'],
                                    "AlertV30" => $data['AlertV30'],
                                    "AlertV31" => $data['AlertV31'],
                                    "AlertV32" => $data['AlertV32'],
                                    "AlertV33" => $data['AlertV33'],
                                    "AlertV34" => $data['AlertV34'],
                                    "AlertV35" => $data['AlertV35'],
                                   
                                ]

                            ]

                        ];
                         $parameters = json_encode($parameters);
                         //echo $parameters;//exit;
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
					if($response){
                    $response = json_decode($response,true);
                    //echo '=='.$response['ErrorObj'][0]['ErrorMessage'];exit;
                    if($response['ErrorObj'][0]['ErrorMessage'] == 'Success'){
                        $update_arr = ["renewal_debit_reminder" => 1];
                        $this->db->where("lead_id",$val['lead_id']);
                        $this->db->update("emandate_data",$update_arr);
                        //echo $this->db->last_query();
                    }
                }
                    $type = "reminder_due_date";
                    $request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($parameters),"res" => json_encode($response) ,"product_id"=> $query_check['product_code'], "type"=>"sms_logs_emandate_".trim($type)];
                    
                    $dataArray['tablename'] = 'logs_docs'; 
                    $dataArray['data'] = $request_arr; 
                    $this->Logs_m->insertLogs($dataArray);
		
		
		
		
		
		
		}
	}
		 }
	return true;
	}
	    function send_communication_si_debit($type,$lead_id,$reject_reason,$invoice_no,$end_date,$registration_status_updated_on,$si_mandate_date,$total_renewal_gross_premium){

        $query_check_all = $this->db->query(" SELECT ed.product_id,apr.gross_premium,apr.end_date,p.premium,p.created_date,mpst.policy_subtype_id,ed.emp_id,ed.lead_id,ed.email,ed.mob_no,ed.emp_firstname,ed.emp_middlename,ed.emp_lastname,mpst.product_code,mpst.click_pss_url,mpst.product_name,ed.json_qote,mpst.plan_code AS master_plan,apr.certificate_number AS certificate_number FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal AS p,api_proposal_response AS apr WHERE p.emp_id = ed.emp_id AND epd.product_name = mpst.id AND p.policy_detail_id = epd.policy_detail_id AND apr.proposal_no_lead = p.proposal_no AND ed.lead_id =".$lead_id)->result_array();


        
            //end_date from api_proposal response date format is m/d/Y
            $plan_name = [];
            $coi_no = [];
            $premium_amt = [];
            $total_gross_premium = 0;
            $premium_GFB = 0;
            foreach ($query_check_all as $key => $value) {
                if($value['policy_subtype_id'] == 1){
                    $pro_name = 'Group Activ Health';
                }else if($value['policy_subtype_id'] == 8){
                    $pro_name = 'Group Protect';
                }else if($value['policy_subtype_id'] == 2){
                    $pro_name = 'Group Activ Secure';
                }else if($value['policy_subtype_id'] == 3){
                    $pro_name = 'Group Activ Secure';
                }
                array_push($coi_no, $value['certificate_number']);
                array_push($plan_name, $pro_name);
                array_push($premium_amt, $value['gross_premium']);
                if(is_numeric($value['gross_premium'])){
                    $total_gross_premium += floatval($value['gross_premium']);
                }
                if($value['policy_subtype_id'] != 1){
                    $premium_GFB += floatval($value['gross_premium']);
                }
                
            }
            if($query_check_all[0]['product_id'] == 'R10'){
                $premium_GFB = '';
            }
            
            $query_check = $query_check_all[0];
            if($query_check){
                $si_end_date = date('d/m/Y',strtotime($query_check['end_date']. " + 1 day"));
                //echo $query_check['end_date'].'--'.$si_end_date;exit;
                $json_data = json_decode($query_check['json_qote'],true);
                
                $senderID = 1;
                
                
                $si_renewal_date = '';
                $alertID = '';
                if($si_end_date != ''){
                    /*$si_mandate_date_new = str_replace("/", "-", $si_end_date);                
                    $si_renewal_date1 = date("d-m-Y", strtotime(date("d-m-Y", strtotime($si_mandate_date_new)) . " + 1 day"));
                    $si_renewal_date = str_replace("-", "/", $si_renewal_date1);*/
                    $si_renewal_date = date('d/m/Y',strtotime($query_check['end_date']. " + 1 day"));
                }
                $full_name = trim($query_check['emp_firstname'].' '.$query_check['emp_middlename'].' '.$query_check['emp_lastname']);
                if(strlen($full_name) > 30){
                    $full_name = substr($full_name, 0, 30);
                }
                if(strtolower($type) == 'success'){
                    
					$data['alertID'] = 'A1610';
                    $data['AlertV1'] = $full_name;
                    $data['AlertV2'] = 'Axis bank Ltd' ;
                    $data['AlertV3'] = $total_renewal_gross_premium;//changes 
                    $data['AlertV4'] = 'Group';//'Axis bank Ltd';
                    $data['AlertV5'] = $coi_no[0];//COI Number1
                    $data['AlertV6'] = "";
                    $data['AlertV7'] = (isset($coi_no[1])) ? $coi_no[1] : "";//COI Number2
                    $data['AlertV8'] = $invoice_no;//Transactionn ID
                    $data['AlertV9'] = $si_mandate_date;//Date Of Auto Debit Success1
                    $data['AlertV10'] = $si_renewal_date;//Renewal Due Date1
                    $data['AlertV11'] = $registration_status_updated_on;//Date Of Auto Debit Registration1
                    $data['AlertV12'] = $premium_amt[0];//Renewal Premium Payable1
                    $data['AlertV13'] = $si_renewal_date;//Renewal Due Date2
                    $data['AlertV14'] = (isset($premium_amt[1])) ? $premium_amt[1] : "";//Renewal Premium Payable2
					$data['AlertV15'] =  $registration_status_updated_on;//Date Of Auto Debit Registration2
                    $data['AlertV16'] = $si_mandate_date;//Date of Auto Debit Success2
                    $data['AlertV17'] = (isset($coi_no[2])) ? $coi_no[2] : "";//COI Number3;
                    $data['AlertV18'] = (isset($coi_no[2])) ? $si_renewal_date : "";//Renewal Due Date3;
                    $data['AlertV19'] = (isset($premium_amt[2])) ? $premium_amt[2] : "";//Renewal Premium Payable3;
                    $data['AlertV20'] = (isset($coi_no[2])) ? $registration_status_updated_on : "";//Date Of Auto Debit Registration3;                
                    $data['AlertV21'] = (isset($coi_no[2])) ? $si_mandate_date : "";//Date of Auto Debit Success3;
                    $data['AlertV22'] = (isset($coi_no[3])) ? $coi_no[3] : "";//COI Number4;
                    $data['AlertV23'] = (isset($coi_no[3])) ? $si_renewal_date : "";//Renewal Due Date4;
                    $data['AlertV24'] = (isset($premium_amt[3])) ? $premium_amt[3] : "";//Renewal Premium Payable3;
                    $data['AlertV25'] = (isset($coi_no[3])) ? $registration_status_updated_on : "";//Date Of Auto Debit Registration4;                
                    $data['AlertV26'] = (isset($coi_no[3])) ?$si_mandate_date : "";//Date of Auto Debit Success4;                
                    $data['AlertV27'] = (isset($coi_no[4])) ? $coi_no[5] : "";//COI Number5;
                    $data['AlertV28'] = (isset($coi_no[4])) ? $si_renewal_date : "";//Renewal Due Date5;
                    $data['AlertV29'] = (isset($premium_amt[4])) ? $premium_amt[4] : "";//Renewal Premium Payable5;
                    $data['AlertV30'] = (isset($coi_no[4])) ? $registration_status_updated_on : "";//Date Of Auto Debit Registration5;                
					$data['AlertV31'] = 	(isset($coi_no[4])) ?$si_renewal_date:"";//Date of Auto Debit Success5;               
                    $data['AlertV32'] = (isset($plan_name[0])) ? $plan_name[0] : "";//Product Name1
                    $data['AlertV33'] = (isset($plan_name[1])) ? $plan_name[1] : "";//Product Name2
                    $data['AlertV34'] = (isset($plan_name[2])) ? $plan_name[2] : "";//Product Name3
                    $data['AlertV35'] = (isset($plan_name[3])) ? $plan_name[3] : "";//Product Name4
					$data['AlertV36'] = (isset($plan_name[4])) ? $plan_name[4] : "";//Product Name5
                }
                
                if(strtolower($type) == 'fail' || strtolower($type) == 'failure'){
                    $data['alertID'] = 'A1611';
                    $data['AlertV1'] = $full_name;
                    $data['AlertV2'] = $total_renewal_gross_premium; 
                    $data['AlertV3'] = 'Axis bank Ltd' ;
                    $data['AlertV4'] = 'Group';
                    $data['AlertV5'] = $coi_no[0];//COI Number1
                    $data['AlertV6'] = $reject_reason;
                    $data['AlertV7'] = (isset($coi_no[1])) ? $coi_no[1] : "";//COI Number2
                    $data['AlertV8'] = $si_mandate_date;//Date of Auto-debit Attempt Failure
                    $data['AlertV9'] = $si_renewal_date;//Renewal Due Date1
                    $data['AlertV10'] = $registration_status_updated_on;//Date Of Auto Debit Registration1
                    $data['AlertV11'] = $si_mandate_date;//Date of Auto-debit  Failure1
                    $data['AlertV12'] = $premium_amt[0];//Renewal Premium Payable1
                    $data['AlertV13'] = $si_renewal_date;//Renewal Due Date2
                    $data['AlertV14'] = (isset($premium_amt[1])) ? $premium_amt[1] : "";//Renewal Premium Payable2
					$data['AlertV15'] =  $registration_status_updated_on;//Date Of Auto Debit Registration2
                    $data['AlertV16'] = $si_mandate_date;//Date of Auto Debit Attempt Failure2
                    $data['AlertV17'] = (isset($coi_no[2])) ? $coi_no[2] : "";//COI Number3;
                    $data['AlertV18'] = (isset($coi_no[2])) ? $si_renewal_date : "";//Renewal Due Date3;
                    $data['AlertV19'] = (isset($premium_amt[2])) ? $premium_amt[2] : "";//Renewal Premium Payable3;
                    $data['AlertV20'] = (isset($coi_no[2])) ? $registration_status_updated_on : "";//Date Of Auto Debit Registration3;                
                    $data['AlertV21'] = (isset($coi_no[2])) ?$si_renewal_date:"";//Date of Auto Debit Success3;
                    $data['AlertV22'] = (isset($coi_no[3])) ? $coi_no[3] : "";//COI Number4;
                    $data['AlertV23'] = (isset($coi_no[3])) ? $si_renewal_date : "";//Renewal Due Date4;
                    $data['AlertV24'] = (isset($premium_amt[3])) ? $premium_amt[3] : "";//Renewal Premium Payable3;
                    $data['AlertV25'] = (isset($coi_no[3])) ? $registration_status_updated_on : "";//Date Of Auto Debit Registration4;                
                    $data['AlertV26'] = (isset($coi_no[3])) ?$si_mandate_date:"";//Date of Auto Debit failure4;                
                    $data['AlertV27'] = (isset($coi_no[4])) ? $coi_no[5] : "";//COI Number5;
                    $data['AlertV28'] = (isset($coi_no[4])) ? $si_renewal_date : "";//Renewal Due Date5;
                    $data['AlertV29'] = (isset($premium_amt[4])) ? $premium_amt[4] : "";//Renewal Premium Payable5;
                    $data['AlertV30'] = (isset($coi_no[4])) ? $registration_status_updated_on : "";//Date Of Auto Debit Registration5;                
                    $data['AlertV31'] = (isset($coi_no[4])) ?$si_mandate_date:"";//Date of Auto Debit Success5;               
                    $data['AlertV32'] = (isset($plan_name[0])) ? $plan_name[0] : "";//Product Name1
                    $data['AlertV33'] = (isset($plan_name[1])) ? $plan_name[1] : "";//Product Name2
                    $data['AlertV34'] = (isset($plan_name[2])) ? $plan_name[2] : "";//Product Name3
                    $data['AlertV35'] = (isset($plan_name[3])) ? $plan_name[3] : "";//Product Name4
					$data['AlertV36'] = (isset($plan_name[4])) ? $plan_name[4] : "";//Product Name5
                }
                //print_pre($data);exit;
               // foreach ($data as $key => $value) {
                    //print_pre($value);exit;
                    //echo "a-";
                    $parameters =[
                    "RTdetails" => [
                   
                            "PolicyID" => '',
                            "AppNo" => 'HD100017934',
                            "alertID" => $data['alertID'],
                            "channel_ID" => $query_check['product_name'],
                            "Req_Id" => 1,
                            "field1" => '',
                            "field2" => '',
                            "field3" => '',
                            "Alert_Mode" => 3,
                            "Alertdata" => 
                                [
                                    "mobileno" => substr(trim($query_check['mob_no']), -10),
                                    "emailId" => $query_check['email'],
                                    "AlertV1" => $data['AlertV1'],
                                    "AlertV2" => $data['AlertV2'],
                                    "AlertV3" => $data['AlertV3'],
                                    "AlertV4" => $data['AlertV4'],
                                    "AlertV5" => $data['AlertV5'],
                                    "AlertV6" => $data['AlertV6'],
                                    "AlertV7" => $data['AlertV7'],
                                    "AlertV8" => $data['AlertV8'],
                                    "AlertV9" => $data['AlertV9'],
                                    "AlertV10" => $data['AlertV10'],
                                    "AlertV11" => $data['AlertV11'],
                                    "AlertV12" => $data['AlertV12'],
                                    "AlertV13" => $data['AlertV13'],
                                    "AlertV14" => $data['AlertV14'],
									"AlertV15" => $data['AlertV15'],
									"AlertV16" => $data['AlertV16'],
									"AlertV17" => $data['AlertV17'],
									"AlertV18" => $data['AlertV18'],
									"AlertV19" => $data['AlertV19'],
									"AlertV20" => $data['AlertV20'],
									"AlertV21" => $data['AlertV21'],
									"AlertV22" => $data['AlertV22'],
									"AlertV23" => $data['AlertV23'],
									"AlertV24" => $data['AlertV24'],
									"AlertV25" => $data['AlertV25'],
									"AlertV26" => $data['AlertV26'],
									"AlertV27" => $data['AlertV27'],
									"AlertV28" => $data['AlertV28'],
									"AlertV29" => $data['AlertV29'],
									"AlertV30" => $data['AlertV30'],
									"AlertV31" => $data['AlertV31'],
									"AlertV32" => $data['AlertV32'],
									"AlertV33" => $data['AlertV33'],
									"AlertV34" => $data['AlertV34'],
									"AlertV35" => $data['AlertV35'],
									"AlertV36" => $data['AlertV36'],
                                   
                                ]

                            ]

                        ];
                         $parameters = json_encode($parameters);
                         //echo $parameters;//exit;
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
                    
                    $request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($parameters),"res" => json_encode($response) ,"product_id"=> $query_check['product_code'], "type"=>"sms_logs_emandate_debit_".trim($type)];
                    
                    $dataArray['tablename'] = 'logs_docs'; 
                    $dataArray['data'] = $request_arr; 
                    $this->Logs_m->insertLogs($dataArray);
                //}
               // exit;
                
        
          }
		  return true;
    }
	function get_profile_customer($lead_id)
    {

        return $this
            ->db
            ->query("select e.* from employee_details as e left join master_salutation as m ON e.salutation = m.s_id left join product_master_with_subtype as  pmws on e.product_id = pmws.product_code where e.lead_id='$lead_id'")->row();
        //return $this->db->select('*')->where(["emp_id" => $emp_id])->get("employee_details")->row();
        
    }
	function get_all_member_data($emp_id, $policy_detail_id)
    {

        $response = $this
            ->db
            ->query('SELECT epm.family_relation_id,e.policy_subtype_id,epd.policy_detail_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,"Self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type","R001" as relation_code,e.plan_code FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS epm,family_relation AS fr,employee_details AS ed WHERE epd.product_name = e.id AND e.policy_subtype_id = epd.policy_sub_type_id AND epd.policy_detail_id = epm.policy_detail_id AND epm.family_relation_id = fr.family_relation_id AND fr.family_id = 0 AND fr.emp_id = ed.emp_id AND ed.emp_id = ' . $emp_id . ' AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' UNION all SELECT epm.family_relation_id,e.policy_subtype_id,epd.policy_detail_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type",mfr.relation_code,e.plan_code FROM product_master_with_subtype AS e,employee_policy_detail AS epd,proposal_member AS epm,family_relation AS fr,employee_family_details AS efd,master_family_relation AS mfr WHERE epd.product_name = e.id AND e.policy_subtype_id = epd.policy_sub_type_id AND epd.policy_detail_id = epm.policy_detail_id AND epm.family_relation_id = fr.family_relation_id AND fr.family_id = efd.family_id AND efd.fr_id = mfr.fr_id AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' AND fr.emp_id = ' . $emp_id)->result_array();
               //echo $this->db->last_query();exit();
        return $response;
    }
 function process_si_otp_data(){
        $q = "SELECT * FROM tbl_si_otp_registration_process WHERE type = 'upload' AND file_processed = 0 limit 1";
        $res = $this->db->query($q)->row_array();
        if(!empty($res)){
            $dq = "SELECT * FROM tbl_si_dump_table WHERE is_record_processed = 0 AND is_record_validated = 0 AND has_errors = 0 AND excel_id = '".$res['id']."'";
           
            $data = $this->db->query($dq)->result_array();
            $file_has_error = 0;
            foreach ($data as $key => $val) {
                $this->db->where("id",$res['id']);
                $this->db->update("tbl_si_otp_registration_process",array("status" => "Processing"));
                 $arr = ["registration_status_updated_on"=>date('Y-m-d H:i:s'), "reject_reason" => $val['remark'], 
                        "registration_acceptane_no" => $val['registration_acceptance_no'],"si_mandate_date" => $val['mandate_status_date'],"account_type" => $val['account_type'],"micr" => $val['micr'],"is_record_processed" => 1];
                if($val['status'] != ''){
                    $arr["status"] = $val['status'];
                    $arr["mandate_date"] = date("Y-m-d H:i:s");
                }

                if($val['lead_id'] != '' || $val['product_id'] != ''){
                    $checkdata = $this->db->get_where("emandate_data",["lead_id"=>$val['lead_id'], "product_id"=>$val['product_id'] ])->row_array();
                    if(!empty($checkdata)){
                        $this->db->where("lead_id",$val['lead_id']);
                        $this->db->where("product_id",$val['product_id']);
                        $this->db->where("is_record_processed",0);
                        $this->db->update("emandate_data",$arr);
                        if($this->db->affected_rows() > 0){
                            //si_emandate_data
                            if($val['status'] != ''){
                                if(strtolower($val['status']) == 'success'){
                                    $this->emandate_HB_call($val['lead_id']);
                                }
                                
                                $this->send_communication_si($val['status'],$val['lead_id'],$val['remark'],$val['2'],$val['end_date']);
                            }
                            $this->db->where("id",$val['id']);
                            $this->db->update("tbl_si_dump_table",array("record_processed_on"=>date("Y-m-d H:i:s"),"is_record_processed" => 1,"is_record_validated"=>1,"has_errors"=>0,"error_description" => "data updated successfully!"));
                        }else{
                            $file_has_error = 1;
                            $this->db->where("id",$val['id']);
                            $this->db->update("tbl_si_dump_table",array("record_processed_on"=>date("Y-m-d H:i:s"),"is_record_processed" => 1,"is_record_validated"=>1,"has_errors"=>1,"error_description" => "duplicate lead found"));
                        }
                    }else{
                        $file_has_error = 1;
                            $this->db->where("id",$val['id']);
                            $this->db->update("tbl_si_dump_table",array("record_processed_on"=>date("Y-m-d H:i:s"),"is_record_processed" => 1,"is_record_validated"=>1,"has_errors"=>1,"error_description" => "lead not found"));
                    }
                    
                }else{
                    $file_has_error = 1;
                    $this->db->where("id",$val['id']);
                    $this->db->update("tbl_si_dump_table",array("record_processed_on"=>date("Y-m-d H:i:s"),"is_record_processed" => 1,"is_record_validated"=>1,"has_errors"=>1,"error_description" => "lead id or product id not found"));
                    
                }
                
                
                
            }
            if($file_has_error == 1){
                $status = "Failed";
            }else{
                $status = "Success";
            }
            $this->db->where("id",$res['id']);
            $this->db->update("tbl_si_otp_registration_process",array("status" => $status, "file_processed" => 1,"file_has_errors"=>$file_has_error));
            echo "Cron executed successfully !!";
        }else{
            echo "No data found !cron executed successfully !!";
        }
        
    }

    // this cron is used to call emandate HB on emandate Success
    function emandate_HB_call($lead_id)
    {   
       $query_check = $this->db->query("SELECT emd.micr,emd.no_of_tran,emd.customer_uid,p.premium,pd.payment_mode,pd.payment_status,pd.transaction_no,pd.EasyPayId,emd.sid_start_date,emd.sid_end_date,emd.status,ed.lead_id,ed.product_id,ed.json_qote,apr.certificate_number,apr.proposal_no,apr.pr_api_id FROM employee_details AS ed,proposal AS p,api_proposal_response AS apr,emandate_data AS emd, payment_details AS pd WHERE ed.emp_id = p.emp_id AND p.id = pd.proposal_id AND p.proposal_no = apr.proposal_no_lead AND p.emp_id = apr.emp_id AND p.status in('Success','Payment Received') AND pd.si_auto_renewal = 'Y' AND apr.mandate_send_status = 0 AND emd.lead_id = ed.lead_id AND pd.payment_status IN ('No Error','Success') AND emd.status = 'Success' AND emd.is_hb_call = 0 AND ed.lead_id ='".$lead_id."'")->result_array();
        //echo $this->db->last_query();
        
        if($query_check){ 
        
            foreach ($query_check as $val)
            {
                
                if($val['payment_status'] == 'No Error'){
                    $payment_id = $val['transaction_no'];
                }else{
                    $payment_id = $val['EasyPayId'];
                }
                //print_pre($val);
                //BIZ HB call start
                $json_data = json_decode($val['json_qote'],true);
                $curl = curl_init();
                $url = 'https://bizpre.adityabirlahealth.com/ABHICL_Generic/Service1.svc/AddEmendateDetails';
                //$url = 'https://bizpre.adityabirlahealth.com/ABHICL_HealthCoreXML/Service1.svc/AddEmendateDetails';
                
                // echo date('m/d/Y',strtotime($end_date));exit;
                $req_arr = [
                      'EmendateDeatails' => 
                      [
                        'EmendateList' => 
                        [ 
                          [
                            'Bank_Name' => ($json_data['AXISBANKACCOUNT'] == 'Y')?'Axis Bank':'Other',
                            'Debit_Account_Number' => $json_data['ACCOUNTNUMBER'],                             
                            'Mandate_Start_Date' => date('m/d/Y',strtotime($val['sid_start_date'])) ,
                            'Mandate_End_Date' => date('m/d/Y',strtotime($val['sid_end_date'])),
                            'Account_Type' => 'Saving',
                            'Bank_Branch_Name' => $json_data['BRANCH_NAME'],
                            'MICR' => ($val['micr'] != '') ? $val['micr'] : '123',
                            'IFSC' => $json_data['IFSCCODE'],
                            'Frequency' => "1",
                            'Policy_Number' => $val['certificate_number'],
                            "Proposal_Number"=>$val['proposal_no'],
                            "Source"=>"AXIS_GHI",
                            "Mandate_Type"=>"MT",
                            "Payment_ID"=>$payment_id,
                            //"Account_ID"=>"845677",
                            //"Order_ID"=>"09674ODR",
                            //"Customer_ID"=>$val['customer_uid'],
                            //"Token_ID"=>"TKN0001",
                            "Lead_ID"=>$lead_id,
                            "Auto_Debit_Registration_Status"=>"Yes",
                            "Registration_Rejection_Reason"=>"N",
                            "Mandate_Category"=>"N",
                            //"Mandate_Registration_Number"=>"REG0001",
                            //"Debit_Transaction_Reference_Number"=>"DTR001",
                            "Debit_Date"=>date('m/d/Y'),
                            "Debit_Amount"=>$val['premium'],
                            "Debit_Status"=>"Active",
                            "Debit_failure_Reason"=>"N",
                            "Debit_Attempt"=>"1"
                            


                          ],
                        ],
                      ],
                    ];
                // print_pre($req_arr);
                $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 90,
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
              // print_pre($response);exit;
                curl_close($curl);

                if($response){
                    $response = json_decode($response,true);
                    //echo '=='.$response['ErrorObj'][0]['ErrorMessage'];exit;
                    if($response['ErrorObj'][0]['ErrorMessage'] == 'Success'){
                        $update_arr = ["mandate_send_status" => 1];
                        $this->db->where("proposal_no",$val['proposal_no']);
                        $this->db->update("api_proposal_response",$update_arr);
                        //echo $this->db->last_query();
                    }
                }
                
                //  
                
                //echo $response['ErrorObj'][0]['ErrorMessage'];exit;

                $request_arr = ["lead_id" => $lead_id, "req" => json_encode($req_arr),"res" => json_encode($response),"product_id"=> $val['product_id'], "type"=>"emandate_HB_post"];

                $dataArray['tablename'] = 'logs_docs'; 
                $dataArray['data'] = $request_arr; 
                $this->Logs_m->insertLogs($dataArray);
                //BIZ HB call end                        
            }            
        }
        return true;
    }

    function send_communication_si($type,$lead_id,$reject_reason,$si_mandate_date,$end_date){

        
        $query_check_all = $this->db->query(" SELECT ed.product_id,apr.gross_premium,apr.end_date,p.premium,p.created_date,mpst.policy_subtype_id,ed.emp_id,ed.lead_id,ed.email,ed.mob_no,ed.emp_firstname,ed.emp_middlename,ed.emp_lastname,mpst.product_code,mpst.click_pss_url,mpst.product_name,ed.json_qote,mpst.plan_code AS master_plan,apr.certificate_number AS certificate_number FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal AS p,api_proposal_response AS apr WHERE p.emp_id = ed.emp_id AND epd.product_name = mpst.id AND p.policy_detail_id = epd.policy_detail_id AND apr.proposal_no_lead = p.proposal_no AND ed.lead_id =".$lead_id)->result_array();


        
            //end_date from api_proposal response date format is m/d/Y
            $plan_name = [];
            $coi_no = [];
            $premium_amt = [];
            $total_gross_premium = 0;
            $premium_GFB = 0;
            foreach ($query_check_all as $key => $value) {
                if($value['policy_subtype_id'] == 1){
                    $pro_name = 'Group Activ Health';
                }else if($value['policy_subtype_id'] == 8){
                    $pro_name = 'Group Protect';
                }else if($value['policy_subtype_id'] == 2){
                    $pro_name = 'Group Activ Secure';
                }else if($value['policy_subtype_id'] == 3){
                    $pro_name = 'Group Activ Secure';
                }
                array_push($coi_no, $value['certificate_number']);
                array_push($plan_name, $pro_name);
                array_push($premium_amt, $value['gross_premium']);
                if(is_numeric($value['gross_premium'])){
                    $total_gross_premium += floatval($value['gross_premium']);
                }
                if($value['policy_subtype_id'] != 1){
                    $premium_GFB += floatval($value['gross_premium']);
                }
                
            }
            if($query_check_all[0]['product_id'] == 'R10'){
                $premium_GFB = '';
            }
            
            $query_check = $query_check_all[0];
            if($query_check){
                $si_end_date = date('d/m/Y',strtotime($query_check['end_date']. " + 1 day"));
                //echo $query_check['end_date'].'--'.$si_end_date;exit;
                $json_data = json_decode($query_check['json_qote'],true);
                
                $senderID = 1;
                
                
                $si_renewal_date = '';
                $alertID = '';
                if($si_end_date != ''){
                    /*$si_mandate_date_new = str_replace("/", "-", $si_end_date);                
                    $si_renewal_date1 = date("d-m-Y", strtotime(date("d-m-Y", strtotime($si_mandate_date_new)) . " + 1 day"));
                    $si_renewal_date = str_replace("-", "/", $si_renewal_date1);*/
                    $si_renewal_date = date('d/m/Y',strtotime($query_check['end_date']. " + 1 day"));
                }
                $full_name = trim($query_check['emp_firstname'].' '.$query_check['emp_middlename'].' '.$query_check['emp_lastname']);
                if(strlen($full_name) > 30){
                    $full_name = substr($full_name, 0, 30);
                }
                if(strtolower($type) == 'success'){
                    
                    $data['alertID'] = 'A1604';
                    $data['AlertV1'] = $full_name;
                    $data['AlertV2'] = floatval($total_gross_premium * 1.5);
                    $data['AlertV3'] = $plan_name[0];
                    $data['AlertV4'] = (isset($plan_name[1])) ? $plan_name[1] : "";//'Axis bank Ltd';
                    $data['AlertV5'] = 'Axis bank Ltd';//$query_check['certificate_number'];
                    $data['AlertV6'] = $plan_name[0];
                    $data['AlertV7'] = $coi_no[0];
                    $data['AlertV8'] = floatval($premium_amt[0] * 1.5);
                    $data['AlertV9'] = $si_end_date;
                    $data['AlertV10'] = date('d/m/Y');
                    $data['AlertV11'] = $si_renewal_date;
                    $data['AlertV12'] = (isset($plan_name[1])) ? $plan_name[1] : "";
                    $data['AlertV13'] = (isset($coi_no[1])) ? $coi_no[1] : "";
                    $data['AlertV14'] = (isset($premium_amt[1])) ? floatval($premium_amt[1] * 1.5) : "";                
                    $data['AlertV15'] = (isset($plan_name[1])) ? $si_end_date : "";
                    $data['AlertV16'] = (isset($coi_no[1])) ? date('d/m/Y') : "";
                    $data['AlertV17'] = (isset($coi_no[1])) ? $si_renewal_date : "";
                    $data['AlertV18'] = (isset($plan_name[2])) ? $plan_name[2] : "";
                    $data['AlertV19'] = (isset($coi_no[2])) ? $coi_no[2] : "";
                    $data['AlertV20'] = (isset($premium_amt[2])) ? floatval($premium_amt[2] * 1.5) : "";                
                    $data['AlertV21'] = (isset($plan_name[2])) ? $si_end_date : "";
                    $data['AlertV22'] = (isset($coi_no[2])) ? date('d/m/Y') : "";
                    $data['AlertV23'] = (isset($coi_no[2])) ? $si_renewal_date : "";
                    $data['AlertV24'] = (isset($plan_name[3])) ? $plan_name[3] : "";
                    $data['AlertV25'] = (isset($coi_no[3])) ? $coi_no[3] : "";
                    $data['AlertV26'] = (isset($premium_amt[3])) ? floatval($premium_amt[3] * 1.5) : "";                
                    $data['AlertV27'] = (isset($plan_name[3])) ? $si_end_date : "";
                    $data['AlertV28'] = (isset($coi_no[3])) ? date('d/m/Y') : "";
                    $data['AlertV29'] = (isset($coi_no[3])) ? $si_renewal_date : "";
                    $data['AlertV30'] = (isset($plan_name[4])) ? $plan_name[4] : "";
                    $data['AlertV31'] = (isset($coi_no[4])) ? $coi_no[4] : "";
                    $data['AlertV32'] = (isset($premium_amt[4])) ? floatval($premium_amt[4] * 1.5) : "";                
                    $data['AlertV33'] = (isset($plan_name[4])) ? $si_end_date : "";
                    $data['AlertV34'] = (isset($coi_no[4])) ? date('d/m/Y') : "";
                    $data['AlertV35'] = (isset($coi_no[4])) ? $si_renewal_date : "";
                    $data['AlertV36'] = floatval($premium_amt[0] * 1.5);
                    $data['AlertV37'] = (isset($premium_GFB)) ? floatval($premium_GFB * 1.5) : "";         
                    $data['AlertV38'] = floatval($total_gross_premium * 1.5);
                    $data['AlertV39'] = $lead_id;
                }
                
                if(strtolower($type) == 'fail' || strtolower($type) == 'failure'){
                   
                    $data['alertID'] = 'A1606';
                    $data['AlertV1'] = $full_name;
                    $data['AlertV2'] = floatval($total_gross_premium * 1.5);
                    $data['AlertV3'] = $plan_name[0];
                    $data['AlertV4'] = (isset($plan_name[1])) ? $plan_name[1] : "";
                    $data['AlertV5'] = $reject_reason;//'Failure reason';
                    $data['AlertV6'] = 'klr.pw/A0Wir';//branch locator link
                    $data['AlertV7'] = 'Axis bank Ltd';
                    $data['AlertV8'] = $plan_name[0];
                    $data['AlertV9'] = $coi_no[0];
                    $data['AlertV10'] = floatval($premium_amt[0] * 1.5);
                    $data['AlertV11'] = $si_renewal_date;
                    $data['AlertV12'] = (isset($plan_name[1])) ? $plan_name[1] : "";
                    $data['AlertV13'] = (isset($coi_no[1])) ? $coi_no[1] : "";
                    $data['AlertV14'] = (isset($premium_amt[1])) ? floatval($premium_amt[1] * 1.5) : "";     
                    $data['AlertV15'] = (isset($coi_no[1])) ? $si_renewal_date : "";
                    $data['AlertV16'] = (isset($plan_name[2])) ? $plan_name[2] : "";
                    $data['AlertV17'] = (isset($coi_no[2])) ? $coi_no[2] : "";
                    $data['AlertV18'] = (isset($premium_amt[2])) ? floatval($premium_amt[2] * 1.5) : "";     
                    $data['AlertV19'] = (isset($coi_no[2])) ? $si_renewal_date : "";
                    $data['AlertV20'] = (isset($plan_name[3])) ? $plan_name[3] : "";
                    $data['AlertV21'] = (isset($coi_no[3])) ? $coi_no[3] : "";
                    $data['AlertV22'] = (isset($premium_amt[3])) ? floatval($premium_amt[3] * 1.5) : "";     
                    $data['AlertV23'] = (isset($coi_no[3])) ? $si_renewal_date : "";
                    $data['AlertV24'] = (isset($plan_name[4])) ? $plan_name[4] : "";
                    $data['AlertV25'] = (isset($coi_no[4])) ? $coi_no[4] : "";
                    $data['AlertV26'] = (isset($premium_amt[4])) ? floatval($premium_amt[4] * 1.5) : "";     
                    $data['AlertV27'] = (isset($coi_no[4])) ? $si_renewal_date : "";
                    $data['AlertV28'] = floatval($premium_amt[0] * 1.5);
                    $data['AlertV29'] = (isset($premium_GFB)) ? floatval($premium_GFB * 1.5) : "";      
                    $data['AlertV30'] = floatval($total_gross_premium * 1.5);
                    $data['AlertV31'] = $lead_id;
                    $data['AlertV32'] = '';
                    $data['AlertV33'] = '';
                    $data['AlertV34'] = '';
                    $data['AlertV35'] = '';
                    $data['AlertV36'] = '';
                    $data['AlertV37'] = '';
                    $data['AlertV38'] = '';
                    $data['AlertV38'] = '';
                    $data['AlertV39'] = '';
                }
                //print_pre($data);exit;
               // foreach ($data as $key => $value) {
                    //print_pre($value);exit;
                    //echo "a-";
                    $parameters =[
                    "RTdetails" => [
                   
                            "PolicyID" => '',
                            "AppNo" => 'HD100017934',
                            "alertID" => $data['alertID'],
                            "channel_ID" => $query_check['product_name'],
                            "Req_Id" => 1,
                            "field1" => '',
                            "field2" => '',
                            "field3" => '',
                            "Alert_Mode" => 3,
                            "Alertdata" => 
                                [
                                    "mobileno" => substr(trim($query_check['mob_no']), -10),
                                    "emailId" => $query_check['email'],
                                    "AlertV1" => $data['AlertV1'],
                                    "AlertV2" => $data['AlertV2'],
                                    "AlertV3" => $data['AlertV3'],
                                    "AlertV4" => $data['AlertV4'],
                                    "AlertV5" => $data['AlertV5'],
                                    "AlertV6" => $data['AlertV6'],
                                    "AlertV7" => $data['AlertV7'],
                                    "AlertV8" => $data['AlertV8'],
                                    "AlertV9" => $data['AlertV9'],
                                    "AlertV10" => $data['AlertV10'],
                                    "AlertV11" => $data['AlertV11'],
                                    "AlertV12" => $data['AlertV12'],
                                    "AlertV13" => $data['AlertV13'],
                                    "AlertV14" => $data['AlertV14'],
                                    "AlertV15" => $data['AlertV15'],
                                    "AlertV16" => $data['AlertV16'],
                                    "AlertV17" => $data['AlertV17'],
                                    "AlertV18" => $data['AlertV18'],
                                    "AlertV19" => $data['AlertV19'],
                                    "AlertV20" => $data['AlertV20'],
                                    "AlertV21" => $data['AlertV21'],
                                    "AlertV22" => $data['AlertV22'],
                                    "AlertV23" => $data['AlertV23'],
                                    "AlertV24" => $data['AlertV24'],
                                    "AlertV25" => $data['AlertV25'],
                                    "AlertV26" => $data['AlertV26'],
                                    "AlertV27" => $data['AlertV27'],
                                    "AlertV28" => $data['AlertV28'],
                                    "AlertV29" => $data['AlertV29'],
                                    "AlertV30" => $data['AlertV30'],
                                    "AlertV31" => $data['AlertV31'],
                                    "AlertV32" => $data['AlertV32'],
                                    "AlertV33" => $data['AlertV33'],
                                    "AlertV34" => $data['AlertV34'],
                                    "AlertV35" => $data['AlertV35'],
                                    "AlertV36" => $data['AlertV36'],
                                    "AlertV37" => $data['AlertV37'],
                                    "AlertV38" => $data['AlertV38'],
                                    "AlertV39" => $data['AlertV39'],
                                ]

                            ]

                        ];
                         $parameters = json_encode($parameters);
                         //echo $parameters;//exit;
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
                //}
               // exit;
                
        
          }
    }

public function wrong_group_id()
{$data = [];
		$query = $this->db->query("select deductable,lead_id,emp_id  from employee_details where product_id  = 'T03' and deductable !=''order by emp_id desc limit 10 ")->result_array(); 
		//csv file name
                $filename = 'wrong_group_id_'.date('Ymd').'.csv';
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=$filename");
                header("Content-Type: application/csv; ");
// file creation
              $file = fopen('php://output', 'w');

                $header = array("Lead Id","Sum Insured","Family Construct");
                fputcsv($file, $header);
		if(!empty($query))
		{
		foreach($query as $value)
		{
			$emp_id = $value['emp_id'];
			$lead_id = $value['lead_id'];

			$family_rel = $this->db->query("select familyConstruct from family_relation as fr,employee_policy_member as epm where fr.family_relation_id = epm.family_relation_id and fr.emp_id = '$emp_id' order by epm.family_relation_id ASC limit 1")->row_array();

			$data_log = $this->db->query("select req from logs_docs where lead_id = '$lead_id' and type = 'full_quote_request1_1' and product_id  = 'T03' order by id desc limit 1")->row_array();
		
			$json_decode = json_decode($data_log['req']);
			
			$group_id = $json_decode->GroupID;
			$sum_insured = $json_decode->SumInsured;
			$familyConstruct = $family_rel['familyConstruct'];
			
			$group_code = $this->db->query("select group_code from master_group_code where si_group = '$sum_insured' and family_construct = '$familyConstruct' and product_code = 'T03'")->row_array();
			
			$correct_group_id =  $group_code['group_code'];
			
			if($group_code['group_code'] != $group_id)
			{
				$wrong_group_id = $group_id;

			}
			else{
			$correct_group_id =  $group_code['group_code'];
			}
		
				$data[] = array(
'lead_id' => $lead_id,
'sum_insured' => $sum_insured,
'familyConstruct' => $familyConstruct,
'Wrong Group Id' => $wrong_group_id,
'Correct Group Id' => $correct_group_id
);


		 }

		}
		foreach($data as $val)
		{
			fputcsv($file,$val);
		}

		fclose($file);
exit;		
} 
	/*function update_user_activity(){
		$this->db1 = $this->load->database('axis_retail',TRUE);
		$data = $this->db1->select('*')->from('user_activity')->get()->result_array();
		  $arr = [];
		  foreach($data as $values){
			  if(!in_array($values['emp_id'],$arr)){
				  $arr[] = $values['emp_id'];
			  }else{
				  $this->db1->where('id', $values['id']);
				  $this->db1->delete('user_activity');
			  }
			  
		  }
	}*/
	
	/*public function user_activity(){
		$this->db = $this->load->database('axis_retail',TRUE);
		$data = $this->db->query('SELECT t1.lead_id,t1.product_id
FROM employee_details as t1 LEFT JOIN user_activity as t2 
ON t1.emp_id = t2.emp_id 
WHERE t2.emp_id Is Null and date(t1.created_at) < "2021-01-22"')->result_array();
//print_pre($data);
foreach($data as $value){

$emp_id = $this->db->query('SELECT t1.emp_id
FROM employee_details as t1 WHERE t1.lead_id ="'.$value["lead_id"].'"')->row_array();
print_pre($emp_id);
$family_relation_id = $this->db->query('SELECT fr.family_relation_id
FROM family_relation  as fr WHERE fr.emp_id ='.$emp_id["emp_id"].' and fr.family_id = 0')->row_array();

if(empty($family_relation_id["family_relation_id"])){
	$user_activity = ['emp_id' => $emp_id["emp_id"], 'type' => 2, 'status' => 1];
		$this->db->insert("user_activity", $user_activity);
}else{

$member = $this->db->query('SELECT epm.policy_member_id
FROM employee_policy_member  as epm  WHERE epm.family_relation_id ='.$family_relation_id["family_relation_id"])->row_array();

if(count($member) > 0){

$nominee = $this->db->query('SELECT mpn.nominee_id,mpn.nominee_fname
FROM member_policy_nominee  as mpn  WHERE mpn.emp_id ='.$emp_id["emp_id"])->row_array();

	if(count($nominee) > 0 && !empty($nominee['nominee_fname'])){
	
$proposal = $this->db->query('SELECT pr.status
FROM proposal  as pr  WHERE pr.emp_id ='.$emp_id["emp_id"])->row_array();

		if(count($proposal) > 0){
		//print_pre($proposal);exit;
			//check status
			if($proposal['status'] == 'Payment Pending'){
				$user_activity = ['emp_id' => $emp_id["emp_id"], 'type' => 4, 'status' => 1];
				$this->db->insert("user_activity", $user_activity);
			}else if($proposal['status'] == 'Payment Received'){
				$user_activity = ['emp_id' => $emp_id["emp_id"], 'type' => 5, 'status' => 1];
				$this->db->insert("user_activity", $user_activity);
			}else if($proposal['status'] == 'Success'){
				$user_activity = ['emp_id' => $emp_id["emp_id"], 'type' => 6, 'status' => 1];
				$this->db->insert("user_activity", $user_activity);
			}
		
			
		
		}else{
			
			$user_activity = ['emp_id' => $emp_id["emp_id"], 'type' => 3, 'status' => 1];
			$this->db->insert("user_activity", $user_activity);
			
		}
		
	}else{
			
		$user_activity = ['emp_id' => $emp_id["emp_id"], 'type' => 2, 'status' => 1];
		$this->db->insert("user_activity", $user_activity);
		
	}


	
}else{
		$user_activity = ['emp_id' => $emp_id["emp_id"], 'type' => 1, 'status' => 1];
		$this->db->insert("user_activity", $user_activity);
}
}


}
	}*/
	
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
            
            
            if($EW_status == 1)
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
         
      if($EW_status == 1)
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
         
      if($EW_status == 1)
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
	   
	  sleep(30);
$this->db = $this->load->database('axis_retail',TRUE);
	  $this->db->insert('temp_dropoff',['empid' => 100000, 'sendemail' => 'Y', 'identity' => '0008', 'type' => date('dmyhis')]);
						
		echo 'here';
	   print_pre($_POST);
	   echo 144444; echo date('dmyhis');exit;
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
	public function quote_expired_cron_d2c()
	{//echo 123;die;
		$this->db1 = $this->load->database('axis_retail',TRUE);
		$query_r = $this
			->db1
			->query("SELECT ed.lead_status,ed.lead_id,ed.emp_id,ed.product_id,ed.modified_date,ed.created_at,DATE_ADD(DATE(ed.created_at), INTERVAL 1 DAY) as dt ,p.status FROM employee_details as ed left join proposal p on ed.emp_id = p.emp_id  WHERE p.emp_id IS NULL AND (ed.lead_status = 'Proposal Pending' OR ed.lead_status ='Payment Pending')AND DATE_ADD(DATE(ed.created_at), INTERVAL 1 DAY) <
 NOW() - INTERVAL 1 MONTH AND ed.product_id IN('R05','D01','D02','D2C2') UNION ALL (SELECT ed.lead_status,ed.lead_id,ed.emp_id,ed.product_id,ed.modified_date,ed.created_at,DATE_ADD(DATE(ed.created_at), INTERVAL 1 DAY) as dt ,p.status FROM employee_details ed
 JOIN proposal p ON ed.emp_id = p.emp_id
WHERE  (p.status = 'Payment Pending' OR p.`status` = 'Quote Expired')    AND  DATE_ADD(DATE(ed.created_at), INTERVAL 1 DAY) <
 NOW() - INTERVAL 1 MONTH AND ed.product_id IN('R05','D01','D02','D2C2'));")->result_array();
		
		//	if(count($query_r)>0){
				
			foreach($query_r as $val){
				 if($val['lead_status']!='Quote Expired' && $val['status']!='Quote Expired'){
				$emp_id = $val['emp_id'];
				$check_result = ["emp_id"=>$val['emp_id'],"modified_date"=>$val['modified_date']];
				
				
				$proposal_data = $this->db1->query("select * from proposal where emp_id = '$emp_id'")->row_array();
				
				$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($proposal_data['status']),"res" => json_encode($proposal_data['status']) ,"product_id"=> $val['product_id'], "type"=>"update_quote_expired_proposal_cron_10"];				
				$this->db1->insert("logs_docs",$request_arr);	
				
				$where_arr = ["emp_id"=>$val['emp_id']];
				$arr = ["lead_status" => 'Quote Expired'];
				$this->db1->where($where_arr);
				$this->db1->update("employee_details",$arr);
				
				$where_arr = ["emp_id"=>$val['emp_id']];
				$arr = ["status" => 'Quote Expired'];
				$this->db1->where($where_arr);
				$this->db1->update("proposal",$arr);
				
				$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($this->db->last_query()),"res" => json_encode($arr) ,"product_id"=> $val['product_id'], "type"=>"update_quote_expired_proposal_cron_10"];				
				$this->db1->insert("logs_docs",$request_arr);	
			}
			}
			
				
			
	}
	
		public function quote_expired_cron_bb()
	{//echo 123;die;
		
			
				$query_bb = $this
			->db
			->query("SELECT ed.lead_status,ed.lead_id,ed.emp_id,ed.product_id,ed.created_at,DATE_ADD(DATE(ed.created_at), INTERVAL 1 DAY) as dt ,p.status FROM employee_details as ed left join proposal p on ed.emp_id = p.emp_id  WHERE p.emp_id IS NULL AND (ed.lead_status = 'Proposal Pending' OR ed.lead_status ='Payment Pending')AND DATE_ADD(DATE(ed.created_at), INTERVAL 1 DAY) <
 NOW() - INTERVAL 1 MONTH AND ed.product_id IN('R03','R07','R11','R10') UNION ALL (SELECT ed.lead_status,ed.lead_id,ed.emp_id,ed.product_id,ed.created_at,DATE_ADD(DATE(ed.created_at), INTERVAL 1 DAY) as dt ,p.status FROM employee_details ed
 JOIN proposal p ON ed.emp_id = p.emp_id
WHERE  (p.status = 'Payment Pending' OR p.`status` = 'Quote Expired')    AND  DATE_ADD(DATE(ed.created_at), INTERVAL 1 DAY) <
 NOW() - INTERVAL 1 MONTH AND ed.product_id IN('R03','R07','R11','R10'))")->result_array();


foreach($query_bb as $val){

				if($val['status']!='Quote Expired' && $val['lead_status']!='Quote Expired'){
				$emp_id = $val['emp_id'];
				
			
				$proposal_data = $this->db->query("select * from proposal where emp_id = '$emp_id'")->row_array();
				print_pre($proposal_data);
				$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($proposal_data['status']),"res" => json_encode($proposal_data['status']) ,"product_id"=> $val['product_id'], "type"=>"update_quote_expired_proposal_cron_10"];				
				$this->db->insert("logs_docs",$request_arr);	
				
				$where_arr = ["emp_id"=>$val['emp_id']];
				$arr = ["lead_status" => 'Quote Expired'];
				$this->db->where($where_arr);
				$this->db->update("employee_details",$arr);
				
				$where_arr = ["emp_id"=>$val['emp_id']];
				$arr = ["status" => 'Quote Expired'];
				$this->db->where($where_arr);
				$this->db->update("proposal",$arr);
				
				$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($this->db->last_query()),"res" => json_encode($arr) ,"product_id"=> $val['product_id'], "type"=>"update_quote_expired_proposal_cron_10"];				
				$this->db->insert("logs_docs",$request_arr);
				}				
			}
			//}
				
			
	}
	public function d2c_fail_policy_create($check)
	{
		$this->db1 = $this->load->database('axis_retail',TRUE);
		
	if($check == 2){
		//echo "8 clock cron pending";exit;
		
		$query_r = $this
			->db1
			->query("SELECT ed.lead_id,ed.emp_id,ed.product_id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,proposal AS p,ghi_quick_quote_response AS g WHERE epd.product_name = mpst.id AND p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND g.`status` = 'success' AND ed.product_id in('R05','ABC') AND p.status = 'Payment Received' AND date(p.created_date) = date(now())")->result_array();
			
			if($query_r)
			{
				foreach($query_r as $val_r){
					
					$where_arr = ["emp_id"=>$val_r['emp_id'],"status"=>"Payment Received"];
					$arr = ["count" => 2];
					$this->db1->where($where_arr);
					$this->db1->update("proposal",$arr);
					
					/* added the ABC cond here*/
					if($val_r['product_id']=='ABC')
					{
						$check_result = $this->abc_api->policy_creation_call($val_r['lead_id'],1);  
					}
					
					if($val_r['product_id']=='R05')
					{
						$check_result = $this->policy_creation_call_cron($val_r['lead_id']);
					}
		
					$request_arr = ["lead_id" => $val_r['lead_id'], "req" => json_encode($check_result),"res" => json_encode($check_result) ,"product_id"=> $val_r['product_id'], "type"=>"8clock_cron"];
					
					$this->db1->insert("logs_docs",$request_arr);
					
					//echo $check_result['status']."hii".$val_r['lead_id'];
				}
			}
			
		}else if($check == 1){

		/*
			// till 2020-11-17 (for old PG real pg status check)
	   $query = $this
		->db1
		->query("SELECT ed.lead_id,ed.emp_id,ed.email,ed.mob_no,p.premium,g.QuotationNumber,mpst.payu_info_url,ed.product_id,pt.txt_id,pt.pg_type,pt.id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,proposal AS p,ghi_quick_quote_response AS g,payment_txt_ids as pt WHERE epd.product_name = mpst.id AND p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND ed.lead_id = pt.lead_id AND g.`status` = 'success' AND p.status = 'Payment Pending'  AND ed.product_id = 'R05' AND date(p.created_date) >= '2020-10-06' AND pt.cron_count < 2 AND date(p.created_date) <= '2020-11-17'  limit 15")->result_array();
	
		if($query)
		{
			
			foreach($query as $val1){
				
				$this->db1->query("UPDATE payment_txt_ids SET cron_count = cron_count + 1 WHERE id =".$val1['id']);
						
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
									$this->db1->insert("logs_docs",$request_arr);

									$arr = ["payment_status" => "No Error","premium_amount" => ($value['amount']/100),"payment_type" => $value['method'],"pgRespCode" => "","merchantTxnId" => $value['order_id'],"SourceTxnId" => $value['order_id'],"txndate" => date('m/d/Y h:i A', $value['created_at']),"TxRefNo" => $value['id'],"TxStatus"=>"success","bank_name"=>$value['bank'],"json_quote_payment"=>json_encode($payment)];
							
									$proposal_ids = $this->db1->query("select id as proposal_id from proposal where emp_id='".$val1['emp_id']."'")->row_array();
									
									$this->db1->where("proposal_id",$proposal_ids['proposal_id']);
									$this->db1->update("payment_details",$arr);
									
							
									$check_result = $this->policy_creation_call_cron($val1['lead_id']);
							
									//echo $check_result['status']."hii".$val1['lead_id'];		
								}else{
									
									$request_arr = ["lead_id" => $val1['lead_id'],"req" => $val1['txt_id'], "res" => json_encode($payment), "type"=>"pg_real_fail_cron"];
									$this->db1->insert("logs_docs",$request_arr);
							
								}
							}
						
						}
						
						}
					
			}
			
		}*/
		
		// after 2020-11-17 (for new PG real pg status check)
			
			$query1 = $this->db1->query("SELECT ed.lead_id,pt.id,ed.product_id FROM employee_details as ed,proposal AS p,payment_txt_ids as pt WHERE ed.emp_id=p.emp_id AND ed.lead_id = pt.lead_id AND p.status IN('Payment Pending','Rejected') AND ed.product_id in('R05','ABC') AND pt.pg_type = 'New' AND pt.cron_count < 2  limit 15")->result_array();

			if($query1)
			{
				foreach($query1 as $val1){
					$this->db1->query("UPDATE payment_txt_ids SET cron_count = cron_count + 1 WHERE id =".$val1['id']);
						
						/* added the ABC cond here*/
						if($val1['product_id']=='ABC')
						{
							$check_pg = $this->abc_api->real_pg_check($val1['lead_id']); 
						}
						
						if($val1['product_id']=='R05')
						{
							$check_pg = $this->obj_api->real_pg_check($val1['lead_id']); 
						}
				
						/* added the ABC cond here*/
						if($val1['product_id']=='ABC' && $check_pg)
						{
							$check_result = $this->abc_api->policy_creation_call($val1['lead_id'],1);  
						}
						
						if($val1['product_id']=='R05' && $check_pg)
						{
							$check_result = $this->policy_creation_call_cron($val1['lead_id']);
						}
				}
				
			}else{
			
		$query_r = $this
		->db1
		->query("SELECT ed.lead_id,ed.emp_id,ed.email,ed.mob_no,p.premium,g.QuotationNumber,ed.product_id FROM employee_details as ed,proposal AS p,ghi_quick_quote_response AS g WHERE p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND g.`status` = 'success' AND p.count < 5 AND p.status = 'Payment Received' AND ed.product_id in('R05','ABC') AND date(p.created_date) = date(now()) limit 5")->result_array();
		
		if($query_r)
		{
			foreach($query_r as $val_r){
				
				/* added the ABC cond here*/
				if($val_r['product_id']=='ABC')
				{
					$check_result = $this->abc_api->policy_creation_call($val_r['lead_id'],1);  
				}
				
				if($val_r['product_id']=='R05')
				{
					$check_result = $this->policy_creation_call_cron($val_r['lead_id']);
				}
				
				//echo $check_result['status']."hii".$val_r['lead_id'];
			}
		}
			
		}
	  }	
	}
	
	public function policy_creation_call_cron($CRM_Lead_Id)
	  {

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
				 $api_response_tbl = $this->obj_api->GHI_GCI_api_call($update_payment['emp_id'], $update_payment['policy_detail_id'], 1);

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
			"password: esb@axis@ABHI",
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
		
		
function send_dropoff_mails(){  
	$this->db = $this->load->database('axis_retail',TRUE);
	$data = $this->db->select('identity,type')->from('temp_dropoff')->where('sendemail','Y')->get()->result_array();
	foreach($data as $value){
		$this->db->where("identity",$value["identity"]);
		$this->db->delete('temp_dropoff');
	}
	foreach($data as $value){
    if($value['type'] == 'ABC'){
      $this->send_dropoff_reminder_realtime_abc($value["identity"]);
    }else if($value['type'] == 'R05'){
      $this->send_dropoff_reminder_realtime($value["identity"]);
    }
		
		
	}
	
	echo "Cron executed Successfully";
	
	
}		
function send_dropoff_reminder_realtime_bk_160821($emp_id = 0){
	$this->db = $this->load->database('axis_retail',TRUE);
	$emp_id = !empty($emp_id) ? $emp_id : 0;
	
//$emp_id = 3481;

	// INSERT DATA INTO TEMP TABLE
	//$request_arr_temp = array("emp_id" => $emp_id,"requestdata"=>"","responsedata"=>"","step"=>"STEP_1");
	//$this->db->insert("tempdropoffcapture",$request_arr_temp);
	
	// INSERT DATA INTO TEMP TABLE
	if($emp_id == 0){
		
		
	if($this->session->userdata('d2c_session')) {
     				
					
		$aD2CSession = $this->session->userdata('d2c_session');
		$emp_id = encrypt_decrypt_password($aD2CSession['emp_id'],'D');		
		$sqlStr = "SELECT ed.branch_sol_id,ed.json_qote,ed.ISNRI,ed.mob_no,ed.email,ed.lead_id,ua.type,ed.emp_id 
	FROM employee_details as ed,user_activity as ua 
	WHERE ed.emp_id = ua.emp_id AND ed.emp_id = ".$emp_id." AND ed.dropoff_flag = 0";
	}
	}else{
		$sqlStr = "SELECT ed.branch_sol_id,ed.json_qote,ed.ISNRI,ed.mob_no,ed.email,ed.lead_id,ua.type,ed.emp_id 
	FROM employee_details as ed,user_activity as ua 
	WHERE ed.emp_id = ua.emp_id AND ed.emp_id = ".$emp_id;
	}
	

	//$emp_id = (!empty($_GET['emp_id'])) ?  $_GET['emp_id'] : 0;
	
	//AND ua.status = 0 
	// $sqlStr = "SELECT ed.json_qote,ed.ISNRI,ed.mob_no,ed.email,ed.lead_id,ua.type,ed.emp_id 
	// FROM employee_details as ed,user_activity as ua 
	// WHERE ed.emp_id = ua.emp_id AND ed.emp_id = ".$emp_id." 
	// AND ua.type != 6";
	$query=$this->db->query($sqlStr)->result_array();
	
	//echo $this->db->last_query();exit;
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
			if($val['branch_sol_id'] == '002'){
				$lead_id = json_decode($this->cron_m->createCRMLeadDropOff($val['emp_id']),true);
			}
			
			// Create Lead In CRM End
			
			// Create Member In CRM Start
			if($val['branch_sol_id'] == '002'){
			if(!empty($lead_id['LeadId'])) {
				$this->cron_m->insertMemberCRMDropOff($val['emp_id'],$lead_id['LeadId']);
			}
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
			
			//if type == 6 end flow//
				if($val['type'] == 6){
					
					continue;
				}

			 
			 /* Added By Shardul Kulkarni on 07-Aug-2020 for updating employee_details field 'dropoff_flag' to 1 End */
			
			foreach($send_data as $value){
			 //echo 'here
			 
				$url = base_url()."customer_dropoff_tmp/".$emp_id_encrypt."/".$value;
			
				$url_req = "https://api-alerts.kaleyra.com/v5/?api_key=A6ef09c1692b5f41a8cd5bf4ac5e22695&method=txtly.create&url=".urlencode($url)."&title=xyz";
					
				$curl = curl_init();
				
				curl_setopt_array($curl, array(
				CURLOPT_URL => $url_req,
				// CURLOPT_PROXY => "185.46.212.88",
				// CURLOPT_PROXYPORT => 443,
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
				//print_pre($result);
				curl_close($curl);
				
				$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($url_req),"res" => json_encode($result) , "type"=>"bitly_url_".$value];
				$this->db->insert("logs_docs",$request_arr);
				//echo $this->db->last_query();
				$data_new = json_decode($result,true);	
				
				
				if ($data_new['txtly'] == '') {
                        $data_new['txtly'] = $url;
                        if (strlen($url) > 30) {
                            if ($value != 1) {
                                continue;
                            }
                        }
                    }

                if (!preg_match("@^[hf]tt?ps?://@", $data_new['txtly'])) {
                    $data_new['txtly'] = "http://" . $data_new['txtly'];
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
			//print_pre($content);exit;
			//Array ( [1] => http://msg.mn/kAX6kp [2] => http://msg.mn/HsBKwh )
			
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
  //  $emp_id = 3289;


  if($emp_id == 0){
    if($this->session->userdata('d2c_session')) {
      $aD2CSession = $this->session->userdata('d2c_session');
      $emp_id = encrypt_decrypt_password($aD2CSession['emp_id'],'D');   
      $sqlStr = "SELECT ed.branch_sol_id,ed.json_qote,ed.ISNRI,ed.mob_no,ed.email,ed.lead_id,ua.type,ed.emp_id,ed.product_id 
      FROM employee_details as ed,user_activity as ua 
      WHERE ed.emp_id = ua.emp_id AND ed.emp_id = ".$emp_id."  AND ed.dropoff_flag = 0";
    }
  }else{
    $sqlStr = "SELECT ed.branch_sol_id,ed.json_qote,ed.ISNRI,ed.mob_no,ed.email,ed.lead_id,ua.type,ed.emp_id,ed.product_id 
    FROM employee_details as ed,user_activity as ua 
    WHERE ed.emp_id = ua.emp_id AND ed.emp_id = ".$emp_id;
  }

  //AND ua.status = 0 
  // $sqlStr = "SELECT ed.json_qote,ed.ISNRI,ed.mob_no,ed.email,ed.lead_id,ua.type,ed.emp_id 
  // FROM employee_details as ed,user_activity as ua 
  // WHERE ed.emp_id = ua.emp_id AND ed.emp_id = ".$emp_id." 
  // AND ua.type != 6";
  $query=$this->db->query($sqlStr)->result_array();
  //echo $this->db->last_query();exit;i
  //  print_pre($query);exit;
  if($query){
  //echo 1;exit;
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
    //  $request_arr_temp = array("emp_id" => $emp_id,"requestdata"=>"","responsedata"=>"","step"=>"STEP_3");
    //  $this->db->insert("tempdropoffcapture",$request_arr_temp);
      // INSERT DATA INTO TEMP TABLE
      
      // Create Lead In CRM Start
      if($val['branch_sol_id'] == '002'){
       $lead_id = json_decode($this->cron_m->createCRMLeadDropOff($val['emp_id']),true);
      }   
  // Create Lead In CRM End
      
      // Create Member In CRM Start
      if($val['branch_sol_id'] == '002'){
        if(!empty($lead_id['LeadId'])) {
          // $this->cron_m->insertMemberCRMDropOff($val['emp_id'],$lead_id['LeadId']); //OLD
          $this->cron_m->insertMemberCRMDropOffNew($val['emp_id'],$lead_id['LeadId']); //NEW
        }
      }

      // print_pre('llll');exit;
      // Create Member In CRM End
      
      // INSERT DATA INTO TEMP TABLE
    //  $request_arr_temp = array("emp_id" => $emp_id,"requestdata"=>"","responsedata"=>"","step"=>"STEP_4");
    //  $this->db->insert("tempdropoffcapture",$request_arr_temp);
      // INSERT DATA INTO TEMP TABLE
      
      /* Added By Shardul Kulkarni on 07-Aug-2020 for updating employee_details field 'dropoff_flag' to 1 Start */
    //   $request_arr_dropoff = ["dropoff_flag" => "1"];
    //   $this->db->where("emp_id",$val['emp_id']);
    //   $this->db->update("employee_details", $request_arr_dropoff);      
       /* Added By Shardul Kulkarni on 07-Aug-2020 for updating employee_details field 'dropoff_flag' to 1 End */
  //  echo 222;exit;  
      if($val['type'] == 6){
          
          continue;
        }
  //    echo 111l;exit;
      foreach($send_data as $value){
       //echo 'here';i
  //echo 2;exit;
        $url = base_url()."customer_dropoff_tmp/".$emp_id_encrypt."/".$value;
      
        $url_req = "https://api-alerts.kaleyra.com/v5/?api_key=A6ef09c1692b5f41a8cd5bf4ac5e22695&method=txtly.create&url=".urlencode($url)."&title=xyz";
          
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url_req,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        // CURLOPT_POSTFIELDS => $parameters,
        CURLOPT_PROXY => "185.46.212.88",
        CURLOPT_PROXYPORT => 443,
        CURLOPT_HTTPHEADER => array(
          "cache-control: no-cache",
          "content-type: application/json",
          
          ),
        ));

        $result = curl_exec($curl);
        // print_pre($result);exit;
        curl_close($curl);
        
        $request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($url_req),"res" => json_encode($result) , "type"=>"bitly_url_".$value,"product_id"=>$val['product_id']];

        $this->db->insert("logs_docs",$request_arr);
      //  echo $this->db->last_query();
        $data_new = json_decode($result,true);  
        
        //if(empty($data_new)){
        //  $data_new['txtly'] = $url;
      //  }
      
        if($data_new['txtly'] == ''){
          $data_new['txtly'] = $url;
          
              if(strlen($url) > 30){
                if($value != 1){
                  continue;
                }
              }
            
        }

            if (!preg_match("@^[hf]tt?ps?://@", $data_new['txtly'])) {
                $data_new['txtly'] = "http://" . $data_new['txtly'];
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
          'product_id'=>$val['product_id']
        ],
      ];
          
      $trans_details = $this->sendTransactionalSms($content['other_data'],$click_url['click_pss_url']);
        
      }
      
      $activity_request_arr = ["status" => "1"];
      $this->db->where("emp_id",$val['emp_id']);
      $this->db->update("user_activity",$activity_request_arr);
      //print_pre($content);exit;
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
		
		$url_req = "https://api-alerts.kaleyra.com/v5/?api_key=A6ef09c1692b5f41a8cd5bf4ac5e22695&method=txtly.create&url=".urlencode($url)."&title=xyz";
			
			$curl = curl_init();
		
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url_req,
			  // CURLOPT_PROXY => "185.46.212.88",
			  // CURLOPT_PROXYPORT => 443,
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
			if($data_new['txtly'] == ''){
				$data_new['txtly'] = $url;
			}
            if (!preg_match("@^[hf]tt?ps?://@", $data_new['txtly'])) {
                $data_new['txtly'] = "http://" . $data_new['txtly'];
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
		// $dataArray['product_id'] = 'R05';
    $dataArray['product_id'] = $other_data['product_id'];
		//$alertMode = helper_validate_is_nri($dataArray);
		

    // $other_data['product_name'] = D2C_PRODUCT_NAME;
		if($dataArray['product_id'] == 'R05'){
      $other_data['product_name'] = D2C_PRODUCT_NAME;  
    } else if($dataArray['product_id'] == 'ABC'){
      $other_data['product_name'] = D2C_PRODUCT_NAME;  // Pass ABC Product Name
    } else {
      $other_data['product_name'] = D2C_PRODUCT_NAME;
    }
		
		
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
	
	$request_arr = ["lead_id" => $other_data['lead_id'], "req" => json_encode($parameters),"res" => json_encode($response) ,"product_id"=>$dataArray['product_id'], "type"=>"sms_logs"];
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
		//echo 1;exit;
		$this->db1 = $this->load->database('axis_retail',TRUE);
		$constDropOffTime = DROPOFF_LIFE_SEC;
		$timeIntervalDropOffQuery = "SELECT * FROM employee_details WHERE modified_date <= date_sub(now(), interval ".$constDropOffTime." minute) AND dropoff_flag = 0 AND product_id = 'R05'";
	
		$query = $this->db1->query($timeIntervalDropOffQuery)->result_array();
		//echo $this->db1->last_query();exit;
		$tempEmpIds = "";
		if($query) {
			foreach($query as $val1){
				$empId = $val1['emp_id'];
				$queryUserStages = "SELECT * FROM user_activity WHERE emp_id = '".$empId."'";
				$queryUserStagesData = $this->db1->query($queryUserStages)->result_array();
				if(!empty($queryUserStagesData) ) {					

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

  function cron_specific_duration_dropoff_abc(){
    
    $this->db1 = $this->load->database('axis_retail',TRUE);
    $constDropOffTime = DROPOFF_LIFE_SEC;
    $timeIntervalDropOffQuery = "SELECT * FROM employee_details WHERE modified_date <= date_sub(now(), interval ".$constDropOffTime." minute) AND dropoff_flag = 0 AND product_id = 'ABC'";
    //echo $timeIntervalDropOffQuery;exit;
    $query = $this->db1->query($timeIntervalDropOffQuery)->result_array();
   /*echo $this->db->last_query();
    print_pre($query);exit;*/
    $tempEmpIds = "";
    if($query) {
      foreach($query as $val1){
        $empId = $val1['emp_id'];
        $queryUserStages = "SELECT * FROM user_activity WHERE emp_id = '".$empId."'";
        $queryUserStagesData = $this->db1->query($queryUserStages)->result_array();
        //print_pre($queryUserStagesData);
        if(!empty($queryUserStagesData) && $queryUserStagesData['type'] != '7') {         

          $this->send_dropoff_reminder_realtime_abc($empId);
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
      $logs_type = "abc_dropoff_cron_job";  
      $request_arr = ["lead_id" => "", "req" => "Cron Job Successfully run. ".$tempEmpIds,"type"=>$logs_type, "product_id" => "ABC"];
      $this->db1->insert("logs_post_data",$request_arr);
    }
        
    echo "Cron Job for Drop Off Run Successfully";
  }

  function send_dropoff_reminder_realtime_abc($emp_id = 0){
    // echo "in";exit;
    $this->db = $this->load->database('axis_retail',TRUE);
    $emp_id = (!empty($emp_id)) ? $emp_id : 0;
    // echo "test=>".$emp_id."<=";exit;
    if($this->session->userdata('abc_session')) {          
    
    if($emp_id == 0){        
      $aD2CSession = $this->session->userdata('abc_session');
      //print_r($aD2CSession);exit;
      if(!is_numeric($aD2CSession['emp_id'])){
       // echo "in";exit;
        $emp_id = encrypt_decrypt_password($aD2CSession['emp_id'],'D');  
        if($emp_id == null){
          $emp_id = $aD2CSession['emp_id']; 
        }
      }else{
       // echo "out";exit;
        $emp_id = $aD2CSession['emp_id'];
      }
        
      }
    }
    // echo $emp_id;exit; 
    // INSERT DATA INTO TEMP TABLE
    $request_arr_temp = array("emp_id" => $emp_id,"requestdata"=>"","responsedata"=>"","step"=>"STEP_1");
    $this->db->insert("tempdropoffcapture",$request_arr_temp);
    // INSERT DATA INTO TEMP TABLE

    //$emp_id = (!empty($_GET['emp_id'])) ?  $_GET['emp_id'] : 0;
    $sqlStr = "SELECT ed.json_qote,ed.ISNRI,ed.mob_no,ed.email,ed.lead_id,ua.type,ed.emp_id,ed.product_id 
    FROM employee_details as ed,user_activity_abc as ua 
    WHERE ed.emp_id = ua.emp_id AND ed.emp_id = ".$emp_id." 
    AND ua.type != 7 AND ed.dropoff_flag = 0";
    //AND ua.status = 0 
    // echo $sqlStr;exit;
    $query=$this->db->query($sqlStr)->result_array();
    // print_pre($query);exit;
    if($query){
       // INSERT DATA INTO TEMP TABLE
      $request_arr_temp = array("emp_id" => $emp_id,"requestdata"=>"","responsedata"=>"","step"=>"STEP_2");
      $this->db->insert("tempdropoffcapture",$request_arr_temp);
      // INSERT DATA INTO TEMP TABLE  
       
       $click_url =$this->db->query("select click_pss_url from product_master_with_subtype where product_code = 'HS'")->row_array();
       // print_pre($click_url);exit;
       foreach ($query as $val)
       {
        $arr = ["emp_id" => $val['emp_id']];  
        $logs_type = "abc_dropoff_realtime";  
        $request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($arr),"type"=>$logs_type, "product_id" => "ABC"];
        $this->db->insert("logs_post_data",$request_arr);     
        $emp_id_encrypt = encrypt_decrypt_password($val['emp_id']);
        $json_array = json_decode($val['json_qote'],true);
        
        $data = $this->db
            ->get_where('sms_template', ['module_name' => $val['type']])
            ->row_array();
        $premium = "";  
        $query_premium = $this->db->query("select SUM(epm.policy_mem_sum_premium) as policy_mem_sum_premium from employee_details as ed,family_relation as fr,employee_policy_member as epm where ed.emp_id = fr.emp_id AND epm.family_relation_id = fr.family_relation_id AND fr.family_id =0 AND ed.emp_id =".$val['emp_id'])->row_array();
        
        $premium = $query_premium['policy_mem_sum_premium'];  
        
        $send_data = [1,2];
        
        // INSERT DATA INTO TEMP TABLE
        $request_arr_temp = array("emp_id" => $emp_id,"requestdata"=>"","responsedata"=>"","step"=>"STEP_3");
        $this->db->insert("tempdropoffcapture",$request_arr_temp);
        // INSERT DATA INTO TEMP TABLE
       // echo "here";exit;
        // Create Lead In CRM Start
        $lead_id = json_decode($this->cron_m->createCRMLeadDropOffabc($val['emp_id']),true); // Commented on 31-05-2021
        // Create Lead In CRM End
        //echo print_pre($lead_id);exit;
        // Create Member In CRM Start
        /*if(!empty($lead_id['LeadId'])) {
          $this->cron_m->insertMemberCRMDropOff($val['emp_id'],$lead_id['LeadId']);
        }*/ // Commented on 31-05-2021
        // Create Member In CRM End
        
        // INSERT DATA INTO TEMP TABLE
        $request_arr_temp = array("emp_id" => $emp_id,"requestdata"=>"","responsedata"=>"","step"=>"STEP_4");
        $this->db->insert("tempdropoffcapture",$request_arr_temp);
        // INSERT DATA INTO TEMP TABLE
        
        // Added By Shardul Kulkarni on 07-Aug-2020 for updating employee_details field 'dropoff_flag' to 1 Start //
         $request_arr_dropoff = ["dropoff_flag" => "1"];
         $this->db->where("emp_id",$val['emp_id']);
         $this->db->update("employee_details", $request_arr_dropoff);      
         // Added By Shardul Kulkarni on 07-Aug-2020 for updating employee_details field 'dropoff_flag' to 1 End //
        // print_r($send_data);exit;
        foreach($send_data as $value){
          //echo "in";exit;
          $url = base_url()."customer_dropoff_tmp_abc/".$emp_id_encrypt."/".$value;
        
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
          //print_pre($result);exit;
          curl_close($curl);
          
          $request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($url_req),"res" => json_encode($result) , "type"=>"bitly_url_".$value, "product_id" => $val['product_id']];
          $this->db->insert("logs_docs",$request_arr);
          
          $data_new = json_decode($result,true);  
          
        //   if(empty($data_new)){
        //     $data_new['txtly'] = $url;
        //   }

          if ($data_new['txtly'] == '') {
            $data_new['txtly'] = $url;
            if (strlen($url) > 30) {
                if ($value != 1) {
                    continue;
                }
            }
        }
          
          $content = [
          'other_data' => [
            'emp_id' => $val['emp_id'],
            'isNri' => $val['ISNRI'],
            'template_id' => $data['template_id'],
            'lead_id' => $val['lead_id'],
            'product_id' => $val['product_id'],
            'email' => $val['email'],
            'mob_no' => $val['mob_no'],
            'url' => $data_new['txtly'],
            'product_name' => trim($json_array['product_name']),
            'premium' => $premium,
            'alert_mode' => $value,
          ],
        ];
        //print_pre($content);   
        $trans_details = $this->sendTransactionalSms($content['other_data'],$click_url['click_pss_url']);
        //echo "here";exit;
      }
      
      $request_arr = ["status" => "1"];
      $this->db->where("emp_id",$val['emp_id']);
      $this->db->update("user_activity_abc",$request_arr);
      
      //Array ( [1] => http://msg.mn/kAX6kp [2] => http://msg.mn/HsBKwh )
      
     }
    
  }
  
}


	
}