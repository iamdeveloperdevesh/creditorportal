<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}



class Telesales_retails_update extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Telesales/Renewal_telesales_m", "renewal", true);
    }


    public function check_cron_retails_update(){

        $limit=50;

        $next=0;

        $fetch_renewal=$this->renewal->retails_update_total('retail');

        $total=count($fetch_renewal);
        
        $finaltotal=$total-$limit;

        // echo $total."_".$finaltotal."<br>";

        while($next!=$finaltotal){

            $fetch_renewal=$this->renewal->cron_retails_update('retail',$next,$limit);

            foreach($fetch_renewal as $row){

                $policy_number=$row->policy_number;
                $dob=$row->dob;
                $phone=$row->mobile_no;
    
                //https://hpre.adityabirlahealth.com/buy-online-health-v2/obServicesV2/api/Renew/AxisTelesales_GetPolicyDetails?Policyno=13-20-0009978-00
                // $url = "https://bizpre.adityabirlahealth.com/Renewal/Service1.svc/RenewalCheck";
    
                $url = "https://hpre.adityabirlahealth.com/buy-online-health-v2/obServicesV2/api/Renew/AxisTelesales_GetPolicyDetails?Policyno=".$policy_number;
                // $url = "https://hpre.adityabirlahealth.com/buy-online-health-v2/obServicesV2/api/Renew/AxisTelesales_GetPolicyDetails?Policyno=23-20-0004157-0";
        
                $curl = curl_init();
        
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 90,  
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_HTTPHEADER => array(
                        "x-ob-bypass:1",
                    ),
                ));
        
                $result = curl_exec($curl);
        
                curl_close($curl);            
                
                $this->renewal->telesales_renewal_cron($policy_number,$phone,$url,$result);
                
                $ejson=json_encode($result);
    
                $djson=json_decode($ejson);
    
                $ddjson=json_decode($djson,true);
    
                $refno=$ddjson['ResponseData']['refNo'];            
                $newpolicy_number=$ddjson['ResponseData']['Policynumber'];
                $renewdproposalnumber=$ddjson['ResponseData']['PROPOSAL_NUMBER'];
                $customer_id=$ddjson['ResponseData']['CustomerID'];
                $receiptno=$ddjson['ResponseData']['ReceiptNo'];
                $policy_startdate=date('Y-m-d H:i:s',strtotime($ddjson['ResponseData']['PolicyStartDate']));
                $policy_enddate=date('Y-m-d H:i:s',strtotime($ddjson['ResponseData']['PolicyExpiryDate']));
                $renewal_count=$row->renewal_count;
                $responsecode=$ddjson['ResponseCode'];   
                $responsemessage=$ddjson['ResponseMessage'];         
                $responsepolicy_status=$ddjson['ResponseData']['PolicyStatus'];
        
    
                
    
                if($responsecode=='0'&&!empty($ddjson)){
    
                    
                    $notfound = array(
                        "Policy_Number" =>$policy_number,
                        "DoB" => '',
                        "Proposer_MobileNumber" => $phone
            
                    );
                            
                    $url = "http://bizpre.adityabirlahealth.com/Renewal/Service1.svc/RenewalCheck";
    
                    $curl = curl_init();
            
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 90,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => json_encode($notfound),
                        CURLOPT_HTTPHEADER => array(
                            "Cache-Control: no-cache",
                            "Content-Type: application/json"
                        ),
                    ));
            
                    $result = curl_exec($curl);
                    curl_close($curl);
            
                    $xml = simplexml_load_string($result);
            
                    $ejson = json_encode($xml);
            
                    $djson = json_decode($ejson, true);
                    
                    $policy_lapsed_flag = $djson['response']['policyData']['Policy_lapsed_flag'];
                    $renewal_staus = $djson['response']['policyData']['Renewable_Flag'];
                    $renewed_flag = $djson['response']['policyData']['Renewed_Flag'];
                    
             if($policy_lapsed_flag =='No' && $renewal_staus == 'No' && $renewed_flag == 'Yes'){
                
                        $this->renewal->retails_update_renewal_notfound($policy_number,$dob,$phone,$notfound,$result,$renewal_count);                
                    }else{
                        
                    }                            
                     
    
                }else if($responsecode=='1'&&strtolower($responsepolicy_status)!='pending'&&!empty($ddjson)){
            
                    $this->renewal->retails_update_renewal($policy_number,$refno,$url,$ejson,$newpolicy_number,$renewdproposalnumber,$customer_id,$receiptno,$policy_startdate,$policy_enddate,$renewal_count,$responsecode,$responsemessage);
                }
    


            }


            if($next>=$finaltotal){
                exit;
            }

            $next+=$limit;

        }

    }





    public function retails_update()
    {

        $showall = array();


        $fetch_renewal = $this->renewal->retails_update('retail');

        // print_pre($fetch_renewal);exit;

        foreach ($fetch_renewal as $row) {

            $policy_number = $row->policy_number;
            $dob = $row->dob;
            $phone = $row->mobile_no;



            //https://hpre.adityabirlahealth.com/buy-online-health-v2/obServicesV2/api/Renew/AxisTelesales_GetPolicyDetails?Policyno=13-20-0009978-00
            // $url = "https://bizpre.adityabirlahealth.com/Renewal/Service1.svc/RenewalCheck";

            $url = "https://hpre.adityabirlahealth.com/buy-online-health-v2/obServicesV2/api/Renew/AxisTelesales_GetPolicyDetails?Policyno=" . $policy_number;
            // $url = "https://hpre.adityabirlahealth.com/buy-online-health-v2/obServicesV2/api/Renew/AxisTelesales_GetPolicyDetails?Policyno=23-20-0004157-0";

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 90,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_HTTPHEADER => array(
                    "x-ob-bypass:1",
                ),
            ));

            $result = curl_exec($curl);

            curl_close($curl);

            $this->renewal->telesales_renewal_cron($policy_number, $phone, $url, $result);

            $ejson = json_encode($result);

            $djson = json_decode($ejson);

            $ddjson = json_decode($djson, true);

            $refno = $ddjson['ResponseData']['refNo'];
            $newpolicy_number = $ddjson['ResponseData']['Policynumber'];
            $renewdproposalnumber = $ddjson['ResponseData']['PROPOSAL_NUMBER'];
            $customer_id = $ddjson['ResponseData']['CustomerID'];
            $receiptno = $ddjson['ResponseData']['ReceiptNo'];
            $policy_startdate = date('Y-m-d H:i:s', strtotime($ddjson['ResponseData']['PolicyStartDate']));
            $policy_enddate = date('Y-m-d H:i:s', strtotime($ddjson['ResponseData']['PolicyExpiryDate']));
            $renewal_count = $row->renewal_count;
            $responsecode = $ddjson['ResponseCode'];
            $responsemessage = $ddjson['ResponseMessage'];




            if ($responsecode == '0' && !empty($ddjson)) {


                $notfound = array(
                    "Policy_Number" => $policy_number,
                    "DoB" => '',
                    "Proposer_MobileNumber" => $phone

                );

                $url = "http://bizpre.adityabirlahealth.com/Renewal/Service1.svc/RenewalCheck";

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 90,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($notfound),
                    CURLOPT_HTTPHEADER => array(
                        "Cache-Control: no-cache",
                        "Content-Type: application/json"
                    ),
                ));

                $result = curl_exec($curl);
                curl_close($curl);

                $xml = simplexml_load_string($result);

                $ejson = json_encode($xml);

                $djson = json_decode($ejson, true);

                $policy_lapsed_flag = $djson['response']['policyData']['Policy_lapsed_flag'];
                $renewal_staus = $djson['response']['policyData']['Renewable_Flag'];
                $renewed_flag = $djson['response']['policyData']['Renewed_Flag'];

                if ($policy_lapsed_flag == 'No' && $renewal_staus == 'No' && $renewed_flag == 'Yes') {

                    $this->renewal->retails_update_renewal_notfound($policy_number, $dob, $phone, $notfound, $result, $renewal_count);
                } else {
                }
            }else if($responsecode=='1'&&strtolower($responsepolicy_status)=='if'&&!empty($ddjson)){

                $this->renewal->retails_update_renewal($policy_number, $refno, $url, $ejson, $newpolicy_number, $renewdproposalnumber, $customer_id, $receiptno, $policy_startdate, $policy_enddate, $renewal_count, $responsecode, $responsemessage);
            }
        }
    }


    public function group_renewal_issuance_cron()
    {
        // echo "Testing";exit;
         
        $check = $this->db->select("
                            trl.lead_id,
                            trl.lead_id_group
                        ")
                ->from("telesales_renewal_logs trl")

                ->from("telesales_renewal_grp_payments trgp")

                ->where("trl.product_type","group")

                ->where("trgp.lead_id=trl.lead_id")

                ->where_in("trgp.payment_status",['success','Payment Received'])

                ->where("trl.policy_number!=","")
                ->where("trl.status",1)
                ->where("trl.renewal_status",1)
                ->group_by("trl.lead_id")
                ->order_by("trl.id", "DESC")
                ->get()->result_array();        

        // print_r($this->db->last_query());
        // exit;        
        
        // $this
        //     ->db
        //     ->select("*")
        //     ->from("telesales_renewal_grp_payments")
        //     ->where_in("payment_status", )
        //     // ->where_in("new_coi_number", ['0', ''])
        //     ->get()
        //     ->result_array();
        $cron = "yes";

        foreach ($check as $value1) {
            $lead_id_grp=$value1['lead_id_group'];
            $lead_id=$value1['lead_id'];

            //echo $value1['lead_id'].'<br>';
            $url = "https://bizpre.adityabirlahealth.com/ABHICL_GroupRenewal/Service1.svc/CombiGroupRenewalCheck";

            $data = array(
                "Lead_Id" => $lead_id_grp,
                "master_policy_number" => "",
                "certificate_number" => "",
                "dob" => "",
                "proposer_mobileNumber" => "",
            );

            print_r($data);
            // echo "<br>";

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

            

            $insertdata=array(
                'lead_id'=>$lead_id,
                'policy_number'=>$lead_id_grp,
                'req'=>$data_string,
                'res'=>$result,
                'type'=>'group_cron',
                'cron_date'=>date('Y-m-d H:i:s')

            );
            $this->db->insert('telesales_renewal_cron_logs',$insertdata);

            $info = curl_getinfo($curl);
            $response_time_renewal = $info['total_time'];
            curl_close($curl);
    
    
            $djson = json_decode($result, TRUE);

            $finalcoi=array();
            foreach($djson['Renew_Info'] as $newgrouprenwal){
                array_push($finalcoi,$newgrouprenwal['Renewed_Certificate_Number']);
            }

            $newgrouprenwal=implode(",",$finalcoi);

            $ErrorCode = $djson['error'][0]['ErrorCode'];
            $ErrorMessage = $djson['error'][0]['ErrorMessage'];
    
            if ($ErrorCode != '00' && !empty($djson)) {
                $output = ['error' => ['ErrorCode' => $ErrorCode, 'ErrorMessage' => 'Failed', 'output_msg' => $ErrorMessage]];
                // echo json_encode($output);
                // exit;
            } else if (empty($djson)) {
                $output = ['error' => ['ErrorCode' => '0010', 'ErrorMessage' => 'Failed', 'output_msg' => 'could not get details, please try again!']];
                // echo json_encode($output);
                // exit;
            }
    

            if (!empty($djson['response']['policyData'][0]['Sum_insured_type'])) {
                $policy_fi_type = "Family Floater";
            } else if (!empty($djson['response']['policyData']['Sum_insured_type'])) {
                $policy_fi_type = "Individual";
            }


            if ($policy_fi_type = "Family Floater") {
                        $Policy_lapsed_flag =  $djson['response']['policyData'][0]['Policy_lapsed_flag'];
                        $Renewed_Flag =  $djson['response']['policyData'][0]['Renewed_Flag'];
                        $Renewable_Flag =  $djson['response']['policyData'][0]['Renewable_Flag'];
            }else if ($policy_fi_type = "Individual") {
                        $Policy_lapsed_flag =  $djson['response']['policyData']['Policy_lapsed_flag'];
                        $Renewed_Flag =  $djson['response']['policyData']['Renewed_Flag'];
                        $Renewable_Flag =  $djson['response']['policyData']['Renewable_Flag'];
            }

            // echo $Policy_lapsed_flag."_".$Renewed_Flag."_".$Renewable_Flag;

            if(strtolower($Policy_lapsed_flag)=='no'&&strtolower($Renewed_Flag)=='yes'&&strtolower($Renewable_Flag)=='no'){

                // echo $lead_id."<br>";
                $update=array(
                    'renewed_policy_number'=>$newgrouprenwal,
                    'renewal_status'=>0,
                    'renewal_updated'=>date('Y-m-d H:i:s')
                );
                // print_r($update);

                $this->db->where('lead_id',$lead_id)->update('telesales_renewal_logs',$update);
                // print_r($this->db->last_query());

            }else{
                // echo "Not Updated";
            }


        }
    }}
