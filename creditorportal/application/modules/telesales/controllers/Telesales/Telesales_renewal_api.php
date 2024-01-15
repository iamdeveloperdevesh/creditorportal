<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once(APPPATH . "controllers/MY_TelesalesSessionCheck.php");

class Telesales_renewal_api extends MY_TelesalesSessionCheck
{
    public $algoMethod;
    public $hashMethod;
    public $hash_key;
	public $encrypt_key;
    public function __construct()
    {
        parent::__construct();

        // if (!$this->session->userdata('telesales_session')) 
		// {
        //     redirect('login');
        // }
		$telSalesSession = $this->session->userdata('telesales_session');

        $this->algoMethod = 'aes-128-ecb';
        $this->hashMethod = 'SHA512';
        $this->hash_key = 'razorpay';
        $this->encrypt_key = 'axisbank12345678';

        $this->load->model("API/Payment_telesale_m", "obj_api", true);
        $this->load->model("API/Payment_integration_freedom_plus", "external_obj_api", true);
        $this->load->model("Logs_m");

        $this->load->model('Telesales/Renewal_telesales_m', "renewal", true);
    }

    public function create_lead_id_test(){
        echo $this->renewal->getleadid();
    }

    
    public function renewal_token()
    {

        $header = apache_request_headers();

        $avanacode = $header['avnacode'];
        $ekey = $header['key'];

        $key = $this->renewal->check_token($avanacode, $ekey, '');

        header('Content-Type:application/json');

        echo json_encode($key);
    }

    public function get_renewal_data()
    {

        $header = apache_request_headers();
        $accesskey = $header['accesskey'];

        $verify = $this->renewal->check_token('', '', $accesskey);

        if ($verify['Error']["Status"] == 'Token Expired' || $verify['Error']["Status"] == 'Invaid Credentials') {

            header('Content-Type:application/json');
            echo json_encode($verify);
            exit;
        }

        $file = file_get_contents("php://input");
        $request = json_decode($file, true);
        if ($request == NULL) {
            $error = ['Status' => 'Invalid Json'];
            header('Content-Type:application/json');
            echo json_encode($error);
        } else {
            $this->renewal->check_renewal_policy($request['refNo']);
        }
    }


    public function check_bitly_renewal($lead_id_encrypt)
    {
       
        $trigger_id = encrypt_decrypt_password($lead_id_encrypt, 'D');

        $check_policy =  $this->db->select("*")
        ->from("tele_renewal_triggers")
        ->where("id", $trigger_id)
        ->where("status", '1')
        ->where("valid_till >= DATE_SUB(NOW(), INTERVAL 72 HOUR)")
        ->get()
        ->row_array();

      
        if($check_policy){
            $lead_id = $check_policy['lead_id'];
            $ref_id = $this->encryptString($lead_id);
            $ref_id = urlencode($ref_id);
            // redirect('https://www.adityabirlacapital.com/healthinsurance/#!/renewal-renew-policy?Referenceid='.$ref_id.'');
            redirect('https://hpre.adityabirlahealth.com/buy-online-health-v2/axis-telesales-renew-policy?Referenceid='.$ref_id.'');
        }else{
            echo 'Link expired';
        }
    }


    public function check_bitly_renewal_grp($lead_id_encrypt)
    {
       
        $trigger_id = encrypt_decrypt_password($lead_id_encrypt, 'D');

        $check_policy =  $this->db->select("*")
        ->from("tele_renewal_triggers")
        ->where("id", $trigger_id)
        ->where("status", '1')
        ->where("valid_till >= DATE_SUB(NOW(), INTERVAL 1440 HOUR)")
        ->get()
        ->row_array();

        $url = "https://bizpre.adityabirlahealth.com/ABHICL_GroupRenewal/Service1.svc/CombiGroupRenewalCheck";

        $data = array(
            "Lead_Id" => $check_policy['lead_id_grp'],
            "master_policy_number" => "",
            "certificate_number" => "",
            "dob" => "",
            "proposer_mobileNumber" => "",
        );
        $data_string = json_encode($data);
        // echo $data_string;exit;
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

        $info = curl_getinfo($curl);
        curl_close($curl);
        $djson = json_decode($result, TRUE);

        // print_r(count($djson['response']['policyData']));exit;
        if (!empty($djson['response']['policyData'][0]['Policy_lapsed_flag'])) {
            $Policy_lapsed_flag =  $djson['response']['policyData'][0]['Policy_lapsed_flag'];
            $Renewed_Flag =  $djson['response']['policyData'][0]['Renewed_Flag'];
            $Renewable_Flag =  $djson['response']['policyData'][0]['Renewable_Flag'];
            $product_name=array();
            foreach($djson['response']['policyData'] as $key => $values){
                array_push($product_name,$djson['response']['policyData'][$key]['Name_of_product']);
            }
            $product_name=implode(' and ',$product_name);
            // print_r($product_name);
        }else if(!empty($djson['response']['policyData']['Policy_lapsed_flag'])){
            $Policy_lapsed_flag =  $djson['response']['policyData']['Policy_lapsed_flag'];
            $Renewed_Flag =  $djson['response']['policyData']['Renewed_Flag'];
            $Renewable_Flag =  $djson['response']['policyData']['Renewable_Flag'];
            $product_name=$djson['response']['policyData']['Name_of_product'];
            // print_r($djson['response']['policyData']['Name_of_product']);
        }else {
            $Policy_lapsed_flag =  'not_fetched';
            $Renewed_Flag = 'not_fetched';
            $Renewable_Flag =  'not_fetched';
        }
            // exit;
            $Renewed_Flag = 'no';
            $Renewable_Flag =  'yes';
        
        if($check_policy){
            $lead_id = $check_policy['lead_id'];
            $ref_id = $this->encryptString($lead_id);
            $ref_id = urlencode($ref_id);

            if (strtolower($Policy_lapsed_flag) == "no" && strtolower($Renewed_Flag) == "no" && strtolower($Renewable_Flag) == "yes") {

                // redirect( $check_policy['payment_link']);
                $get_data =  $this->db->select("*")
                ->from("telesales_renewal_logs")
                ->where("lead_id", $lead_id)
                ->get()
                ->row_array();
                $data_r['customer_name'] = $get_data['customer_name'];
                $data_r['premium'] = $get_data['premium'];
                $data_r['mobile_no'] = $get_data['mobile_no'];
                $data_r['payment_link'] = $check_policy['payment_link'];
                $data_r['product_name'] = $product_name;
                // print_r($data_r);exit;
                $this->load->telesales_template("group_renewal_payment.php", $data_r);

            }else if(strtolower($Policy_lapsed_flag) == "not_fetched" && strtolower($Renewed_Flag) == "not_fetched" && strtolower($Renewable_Flag) == "not_fetched"){
                echo 'Could not fetch policy details, please visit again!';
            }else{
                echo 'Policy is not renewable';
            }
        }else{
            echo 'Link expired';
        }
    }


    public function encryptString($data) {
        $key = "ABHIAPIEncryptionLID@12345678";
        $iv = 'encryptionABHILID';
        $key = hash('MD5', $key, true);
        $iv = hash('MD5', $iv, true);
        $encryptedData = base64_encode(openssl_encrypt($data, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv));
        return $encryptedData;
    }
    
    public function decryptString($data) {
        $key = "ABHIAPIEncryptionLID@12345678";
        $iv = 'encryptionABHILID';
        $key = hash('MD5', $key, true);
        $iv = hash('MD5', $iv, true);
        $data = base64_decode($data);
        $data = openssl_decrypt($data, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return $data;
    }



    public function tele_renew_grp_payment_return($lead_id_encrypt){
		
        $lead_id = encrypt_decrypt_password($lead_id_encrypt,'D');
        $encrypted = $this->input->post('RESPONSE');
        // $this->renewal_group_issuance($lead_id);exit;
        
        $get_data =  $this->db->select("*")
            ->from("telesales_renewal_grp_payments")
            ->where("lead_id", $lead_id)
            ->get()
            ->row_array();

    
        if($get_data['payment_status'] ==  'Payment Received' || $get_data['payment_status'] == 'success'){


            $ref=json_decode($get_data['res'],true);
            // $data['status'] = 'success';
            $data['lead_id'] = $ref['TxRefNo'];
            $data['new_coi_number'] = $get_data['new_coi_number'];
            $this->load->telesales_template("thankyou_grp_renewal.php", $data);
        }
            
        else if($encrypted)
        {
            $decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);
            $post_data = json_decode($decrypted,true);
            extract($post_data);


            $fdata = [
                'lead_id' => $lead_id,
                // 'req' => $data,
                'res' => $decrypted,
                'payment_status' => $TxMsg,
            ];
    
            $this->db->insert('telesales_renewal_grp_payments', $fdata);
            
            if($TxStatus && ($TxMsg == 'Payment Received' || $TxMsg == 'success') && $PaymentStatus == 'PR'){

                $update_policy = $this->db->set('status', 0);
                $this->db->where('lead_id', $lead_id);
                $this->db->update('tele_renewal_triggers');

                $cron = "no";
                //$this->renewal->renewal_group_issuance($lead_id, $cron);
                $data['lead_id'] = $post_data['TxRefNo'];
                $this->load->telesales_template("thankyou_grp_renewal.php", $data);

            }else {
                echo 'Payment Failed';
            }
        }


       

    }

   



   



}
