<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once(APPPATH . "controllers/MY_TelesalesSessionCheck.php");

class Renewal_telesales extends MY_TelesalesSessionCheck
{

    public function __construct()
    {
        parent::__construct();
        // ini_set('display_errors', 1);
		// ini_set('display_startup_errors', 1);
		// error_reporting(E_ALL);

        if (!$this->session->userdata('telesales_session')) {
            redirect('login');
        }
        $telSalesSession = $this->session->userdata('telesales_session');

        $this->algoMethod = 'aes-128-ecb';
        $this->hashMethod = 'SHA512';
        $this->hash_key = 'razorpay';
        $this->encrypt_key = 'axisbank12345678';

        // $this->load->model("API/Payment_telesale_m", "obj_api", true);
        // $this->load->model("API/Payment_integration_freedom_plus", "external_obj_api", true);
        $this->load->model("Logs_m");

        $this->load->model('Telesales/Renewal_telesales_m', "renewal", true);
        if (!$this->input->is_ajax_request()) {
                moduleCheck();
        }
        
    }


    public function index()
    {
        
        // print_pre($_SESSION);
        $data['data'] = $this->renewal->product_type();
        $this->load->telesales_template("telesales_renewal.php", $data);
    }


    public function group_renewal_phasetwo(){
        // exit('Testing');
        $this->load->telesales_template("telesale_grp_renewal_phase2");
    }

   

    public function get_telesales_data_phasetwo(){
        // print_r($this->input->post());exit;


        $fetch_data = $this->renewal->all_retail_data_group_phase2();

        // echo $this->db->last_query();exit;

        $i = 1;

        foreach ($fetch_data as $row) {


            if (!empty($row['new_certificate_number'])) {
                $disabled = 'disabled';
                
            }   

            $date1 = new DateTime(date('Y-m-d H:i:s',strtotime($row['created_at'])));
            $date2 = new DateTime(date('Y-m-d H:i:s'));
            $interval = $date1->diff($date2);       
            
            $coi_number = $row['coi_number'];
            $coi_number_arr = explode(',',$coi_number);
            $single_coi = $coi_number_arr[0];

            $disposition_master_id = $row['disposition_master_id'];
            // 1-12-2021
            $trigger_ids = ['4','5','51'];

            $sub_data = [];
            $sub_data[]=$i;
            $sub_data[]=$row['lead_id'];
            // $sub_data[]="NA";
            $sub_data[]= $this->renewal->get_do_id($row['do_id']);
            $sub_data[]=$row['do_location'];
            $sub_data[]=$row['coi_number'];

            //get current status
            //update - 16-11-2021
            if(in_array($disposition_master_id, ['4'])){
                
                    $sub_data[]= "Pending for Verification";
                    // $sub_data[]= "Renewal Link Triggered";
                
                // 1-12-2021
            }elseif(in_array($disposition_master_id, ['5','48'])){
                    $sub_data[]= "Renewal Link Triggered";
            }
            elseif(in_array($disposition_master_id, ['6']) && !empty($row['bitly_link'])){
                $sub_data[]= "Renewal Link Triggered";
             }
            elseif(in_array($disposition_master_id, ['51'])){
                // 1-12-2021
                $sub_data[]= "Policy Renewed";
            }
            else{
                $sub_data[]= "Disposition Saved";
            }
            
            // $sub_data[]=$row['payment_status']=='success'?'success':'NA';

            // update - 16-11-2021
            $sub_data[]=$row['premium'];
            $sub_data[]=$this->renewal->group2_net_premium($row['lead_id']);

            $sub_data[]=$row['issuance_datetime'];
            $sub_data[]=$this->renewal->group2_inception_date($row['lead_id']);
            $sub_data[]=$row['days']."/".$interval->h;
            // $sub_data[]=$disposition_master_id;

            $disabled = "";
           
            if($row['payment_status'] == "success" || (!in_array($disposition_master_id, ['5','48'])) ){
                $disabled = "disabled";
            }

            if(in_array($disposition_master_id, ['6']) && !empty($row['bitly_link'])){
                $disabled = "";
             }

            $sub_data[] = "
            <div class='text-center'>
            <button type='button' class='btn btn-cta btn-xs tele_re_dt_tl'   ".$disabled." digital-officer='".$row['do_id']."' location='".$row['do_location']."' dob='".$row['dob']."'  mobile='".$row['mobile']."'  claim_status='".$row['claim_status']."' ped_status='".$row['ped_status']."' coi_number='".$single_coi."' id='trigger' data-subdisposition='".$row['disposition_master_id']."' data-linktrigger='".$row['link_sent_by']."' data-avid='".$row['av_id']."' data-location='".$row['av_location']."'>Trigger Link <i class='ti-link'></i></button>
            <br>
            <button coi_number='".$single_coi."'  class='btn btn-cta btn-xs tele_re_dt_audit' id='audit'>Audit <i class='ti-pencil-alt'></i></button>
            </div>
            ";


            $sub_data[]=$row['updated_at'];
            $sub_data[]=$row['new_certificate_number'];
            $sub_data[]=$this->renewal->group2_last_av_mapped($row['lead_id'])['agent_name'];
            $sub_data[]=$row['av_location'];
            $sub_data[]=$this->renewal->group2_last_dipostion($row['lead_id']);
            $sub_data[]=$this->renewal->group2_hr_amount($row['lead_id']);
            $sub_data[]=$row['hr_amount_status'];

            $i++;

            $data[] = $sub_data;
        }
        

        if (empty($data)) {
            $data = "";
        }

        // print_r($this->renewal->all_retail_data_group_count());
        // echo $this->db->last_query();
        
        // print_r($this->renewal->all_retail_data_group_count());

        $output = array(
            "draw"            => intval($_POST["draw"]),
            "recordsTotal" =>$this->renewal->all_retail_data_group_count(),
            "recordsFiltered" =>$this->renewal->all_retail_data_group_count(),
            "data" => $data,
        );

        echo json_encode($output);

    }


    public function telesales_group_renewalphase2_audit(){

        extract($this->input->post(null,true));

        //1-12-2021 - add a.payment_status
        $alldata=$this->db
                        ->select('a.lead_id,a.do_location,a.coi_number,a.updated_at,a.status,b.do_id,IFNULL(c.agent_id,"") as agent_id, IFNULL(a.av_location,"") as  av_location,a.payment_status')
                        ->from('group_mod_create a')
                        ->join("tls_master_do b", "a.do_id = b.id")
                        ->join("tls_agent_mst_outbound c", "a.av_id = c.id","left")
                        ->like('a.coi_number',$coi_number)
                        ->order_by('a.id','DESC')
                        ->get()
                        ->result_array();

                        // echo $this->db->last_query();exit;

        echo json_encode($alldata);
    }

    public function checkValidAvCode()
    {
        $avcode = $this->input->post('avCode');
        $response = $this->renewal->check_valid_av_code(trim($avcode));
        echo json_encode($response);
    }

    public function checkValiddigitalofficer()
    {
        $digitalofficer = $this->input->post('digitalofficer');
        $response = $this->renewal->check_valid_do(trim($digitalofficer));
        echo json_encode($response);
    }

    public function renewal()
    {

        $pnumber = $this->input->post('Policy_Number');
        // $dob = $this->input->post('DoB');
        $phone = $this->input->post('Proposer_MobileNumber');
        $avCode = $this->input->post('avCode');
        $checkStatus = $this->input->post('checkStatus');
        $digitalofficer = $this->input->post('digitalOfficer');
        $location = $this->input->post('location');

        // if(!empty($phone) && (strlen($phone) == 10)){
        //     $dob = '';
        // }


        if (empty($pnumber) && (empty($phone) || empty($dob))) {
            // $error = ["Status" => "Input Fields Cannot be Empty"];
            $error =  ['error' => ['ErrorCode' => '02', 'ErrorMessage' => 'Policy number and Either of Mobile/DOB is required']];
            echo json_encode($error);
            exit;
        }

        $check_policy =  $this->db->select("id")
            ->from("telesales_renewal_logs")
            ->where("policy_number", $pnumber)
            ->order_by("id", "DESC")
            ->get()
            ->row_array();


        //16-04-2021
        if ($check_policy) {
            $is_policy_exist = 'yes';
        } else {
            $is_policy_exist = 'no';
        }

        $data = array(

            "Policy_Number" => $pnumber,
            "DoB" => "",
            "Proposer_MobileNumber" => $phone

        );


        $data_string = json_encode($data);

        $url = "https://bizpre.adityabirlahealth.com/Renewal/Service1.svc/RenewalCheck";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: application/json"
            ),
        ));

        $result = curl_exec($curl);
        // return ["Renewal Response"=>$result];
        $info = curl_getinfo($curl);
        $response_time_renewal = $info['total_time'];
        curl_close($curl);

        $xml = simplexml_load_string($result);

        $ejson = json_encode($xml);

        $djson = json_decode($ejson, true);

        $check = $djson['error']['ErrorMessage'];

        $error_code = $djson['error']['ErrorCode'];

        if ($djson['response']['policyData']['Sum_insured_type'] == "Individual") {
            $policy_fi_type = "Individual";
        }

        if ($djson['response']['policyData'][0]['Sum_insured_type'] == 'Family Floater') {
            $policy_fi_type = "Family Floater";
        }

        // echo $djson['response']['policyData']['Sum_insured_type'];exit;
        if ($policy_fi_type == 'Family Floater') {
            // echo 'here 1';exit;
            $policyrenewal = $djson['response']['policyData'][0]['Policy_renewal_date'];

            $policynumber = $djson['response']['policyData'][0]['Policy_number'];

            $gross_premium_renewal = $djson['response']['policyData'][0]['premium']['Renewal_Gross_Premium'];

            $product_name = $djson['response']['policyData'][0]['Plan_name'];

            $policynumber = $djson['response']['policyData'][0]['Policy_number'];

            $policyrenewal_date = $djson['response']['policyData'][0]['Policy_renewal_date'];

            $combi_flag = $djson['response']['policyData'][0]['Combi_Flag'];

            $policyexpiry = $djson['response']['policyData'][0]['Policy_expiry_date'];

            $policy_lapsed_flag = $djson['response']['policyData'][0]['Policy_lapsed_flag'];
            $renewal_staus = $djson['response']['policyData'][0]['Renewable_Flag'];
            $renewed_flag = $djson['response']['policyData'][0]['Renewed_Flag'];

            // $mmobilenumber = $djson['response']['policyData'][0]['Members'][0]['Mobile_Number'];

            // $cust_email = $djson['response']['policyData'][0]['Members'][0]['Email'];


            // $mdob = $djson['response']['policyData'][0]['Members'][0]['DoB'];

            //$member_arr_count = count($djson['response']['policyData'][0]['Members']);

            if (array_key_exists(0, $djson['response']['policyData'][0]['Members'])) {

                
                $mmobilenumber = $djson['response']['policyData'][0]['Members'][0]['Mobile_Number'];
                $cust_email = $djson['response']['policyData'][0]['Members'][0]['Email'];
                $mdob = $djson['response']['policyData'][0]['Members'][0]['DoB'];
            } else {
                $mmobilenumber = $djson['response']['policyData'][0]['Members']['Mobile_Number'];
                $cust_email = $djson['response']['policyData'][0]['Members']['Email'];
                $mdob = $djson['response']['policyData'][0]['Members']['DoB'];
            }
        } else {
            // echo 'here 2';exit;
            $policyrenewal = $djson['response']['policyData']['Policy_renewal_date'];

            $policynumber = $djson['response']['policyData']['Policy_number'];

            $gross_premium_renewal = $djson['response']['policyData']['premium']['Renewal_Gross_Premium'];

            $product_name = $djson['response']['policyData']['Plan_name'];

            $policynumber = $djson['response']['policyData']['Policy_number'];

            $policyrenewal_date = $djson['response']['policyData']['Policy_renewal_date'];

            $combi_flag = $djson['response']['policyData']['Combi_Flag'];

            $policyexpiry = $djson['response']['policyData']['Policy_expiry_date'];

            $policy_lapsed_flag = $djson['response']['policyData']['Policy_lapsed_flag'];
            $renewal_staus = $djson['response']['policyData']['Renewable_Flag'];
            $renewed_flag = $djson['response']['policyData']['Renewed_Flag'];

            // $mmobilenumber = $djson['response']['policyData']['Members']['Mobile_Number'];

            // $cust_email = $djson['response']['policyData']['Members']['Email'];    

            // $mdob = $djson['response']['policyData']['Members']['DoB'];

            // print_r($djson['response']['policyData']['Members']);exit;


            //$member_arr_count = count($djson['response']['policyData']['Members']);

            if (array_key_exists(0, $djson['response']['policyData']['Members'])) {

                $mmobilenumber = $djson['response']['policyData']['Members'][0]['Mobile_Number'];
                $cust_email = $djson['response']['policyData']['Members'][0]['Email'];
                $mdob = $djson['response']['policyData']['Members'][0]['DoB'];
            } else {
                $mmobilenumber = $djson['response']['policyData']['Members']['Mobile_Number'];
                $cust_email = $djson['response']['policyData']['Members']['Email'];
                $mdob = $djson['response']['policyData']['Members']['DoB'];
            }

            // echo $cust_email;
            // exit;


        }
        // echo $policynumber;exit;
        if ($check == 'Success' && ($policynumber == "" || empty($policynumber))) {
            // $output = ['error' => ['ErrorCode' => '03', 'ErrorMessage' => 'Success'], "Policy_number" => $policynumber, "DoB" => date('Y-m-d', strtotime($mdob)), "Mobile_Number" => $mmobilenumber, "policy_lapsed_flag" => $policy_lapsed_flag, "renewal_status" => $renewal_staus, "renewed_flag" => $renewed_flag, "res" => "", "is_policy_exist" => $is_policy_exist];

            $output = ['error' => ['ErrorCode' => '03', 'ErrorMessage' => 'failed'], "Policy_number" => $policynumber, "DoB" => date('Y-m-d', strtotime($mdob)), "Mobile_Number" => $mmobilenumber, "policy_lapsed_flag" => "error", "renewal_status" => "error", "renewed_flag" => "error", "res" => "", "is_policy_exist" => $is_policy_exist];

            echo json_encode($output);
            exit;
        }

        $mdob = date('Y-m-d', strtotime($mdob));

        $cdate = date('Y-m-d', strtotime("+1 day"));
        $expiry = date('Y-m-d', strtotime($policyexpiry));


        $renewalpolicynumber = $djson['Renew_Info']['Renewed_Policy_Number'];
        $renewalppnumber = $djson['Renew_Info']['Renewed_Policy_Proposal_Number'];
        $renewalpstartdate = $djson['Renew_Info']['Renewed_Policy_Start_Date'];
        $renewalpexpirydate = $djson['Renew_Info']['Renewed_Policy_Start_Date'];


        if (strtotime($cdate) > strtotime($expiry)) {
            $rstatus = "Expired";
        } else if (strtotime($cdate) < strtotime($expiry)) {

            $getdays = strtotime($expiry) - strtotime($cdate);
            $days = $getdays / (60 * 60 * 24);
            $rstatus = $days . " Days Remaining";
        }



        ($check == 'Success') ? $p_status = 1 : $p_status = 2;

        $dob_format = date("Y-m-d", strtotime($mdob));
        $dob_format = ($mdob != '') ? date("Y-m-d", strtotime($mdob)) : null;


        // echo $policyrenewal_date_dmy;exit;

        if (!empty($phone)) {
            $mmobilenumber = $phone;
        }

        // echo $mmobilenumber;exit;

        if ($checkStatus == 'preCheck') {

            //     $lead_id = $this->renewal->getleadid();

            //     $check_policy =  $this->db->select("id")
            //     ->from("telesales_renewal_logs")
            //     ->where("policy_number", $policynumber)
            //     ->where("status", '1')
            //     ->order_by("id", "DESC")
            //     ->get()
            //     ->row_array();


            // if ($check_policy) {
            //     $update_policy = $this->db->set('status', 0);
            //     $this->db->set('lapsedon', date('Y-m-d H:i:s'));
            //     $this->db->where('policy_number', $policynumber);
            //     $this->db->update('telesales_renewal_logs');
            // }







            // $fdata = [
            //     'lead_id' => $lead_id,
            //     'avnacode' => $avCode,
            //     'req' => $data_string,
            //     'res' => $ejson,
            //     'product_type' => 'retail',
            //     'ref_number' => '',
            //     'policy_number' => $data['Policy_Number'],
            //     'dob' => $dob_format,
            //     'mobile_no' => $mmobilenumber,
            //     'response_time' => $response_time_renewal,
            //     'renewal_response' => '',
            //     'login_id' => '',
            //     'status' => $p_status,
            //     'digital_officer' => $digitalofficer,
            //     'renewstatus' => ''
            // ];

            // $this->db->insert('telesales_renewal_logs', $fdata);

            // $lead_id_encrypt = encrypt_decrypt_password($lead_id, 'E');
            $policy_lapsed_flag='no';
            $renewal_staus='yes';
            $renewed_flag='no';
            
            if ($check == 'success') {
                $output = ['error' => ['ErrorCode' => '00', 'ErrorMessage' => 'Success'], "Policy_number" => $policynumber, "DoB" => date('Y-m-d', strtotime($mdob)), "Mobile_Number" => $mmobilenumber, "policy_lapsed_flag" => $policy_lapsed_flag, "renewal_status" => $renewal_staus, "renewed_flag" => $renewed_flag, "res" => "", "is_policy_exist" => $is_policy_exist];
            } else {
                $output = ['error' => ['ErrorCode' => $error_code, 'ErrorMessage' => $check, "lead_id" => ''], "Policy_number" => $policynumber, "DoB" => date('Y-m-d', strtotime($mdob)), "Mobile_Number" => $mmobilenumber, "policy_lapsed_flag" => $policy_lapsed_flag, "renewal_status" => $renewal_staus, "renewed_flag" => $renewed_flag, "res" => "", "is_policy_exist" => $is_policy_exist];
            }
        }
        if ($checkStatus == 'afterCheck') {


            // if($this->input->post('lead_id_hidden')) {
            //     $lead_id = $this->input->post('lead_id_hidden');

            //         $check_lead =  $this->db->select("id,status")
            //         ->from("telesales_renewal_logs")
            //         ->where("lead_id", $lead_id)
            //         ->order_by("id", "DESC")
            //         ->get()
            //         ->row_array();

            //         if($check_lead['status'] == '0'){

            //             $output = ['error' => ['ErrorCode' => 'lead_lapsed', 'ErrorMessage' => 'Can not retrigger as lead is lapsed']];

            //             echo json_encode($output);
            //             exit;

            //         }
            // }



            // $policy_lapsed_flag = 'Yes';
            // $renewal_staus = 'No';
            // $renewed_flag = 'Yes';

            if ($policy_lapsed_flag == 'No' && $renewal_staus == 'No' && $renewed_flag == 'Yes') {
                $output = ['error' => ['ErrorCode' => 'not_open', 'ErrorMessage' => 'Can not trigger as policy is not open for renewal']];
                echo json_encode($output);
                exit;
            }

            $lead_id = $this->renewal->getleadid();

            $check_policy =  $this->db->select("id")
                ->from("telesales_renewal_logs")
                ->where("policy_number", $policynumber)
                ->where("status", '1')
                ->order_by("id", "DESC")
                ->get()
                ->row_array();


            if ($check_policy) {
                $update_policy = $this->db->set('status', 0);
                $this->db->set('lapsedon', date('Y-m-d H:i:s'));
                $this->db->where('policy_number', $policynumber);
                $this->db->update('telesales_renewal_logs');
            }


            $fdata = [
                'lead_id' => $lead_id,
                'avnacode' => $avCode,
                'req' => $data_string,
                'res' => $ejson,
                'product_type' => 'retail',
                'ref_number' => '',
                'policy_number' => $data['Policy_Number'],
                'dob' => $dob_format,
                'mobile_no' => $mmobilenumber,
                'response_time' => $response_time_renewal,
                'renewal_response' => '',
                'login_id' => '',
                'status' => $p_status,
                'digital_officer' => $digitalofficer,
                'location' => $location,
                'bb_code' => "2110705",
                // 'source' => "Axis Telesales",
                'source' => "Customer Portal_Axis_Telesales",
                'renewstatus' => ''
            ];

            $this->db->insert('telesales_renewal_logs', $fdata);

            //get communication details API here
            $url_com = "https://bizpre.adityabirlahealth.com/ABHICL_HealthCoreXML/Service1.svc/PolicyInsured/" . $policynumber . "/null";
            $curl_com = curl_init();
            curl_setopt_array($curl_com, array(
                CURLOPT_URL => $url_com,
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
            $result_com = curl_exec($curl_com);
            $info = curl_getinfo($curl_com);
            $response_time_renewal = $info['total_time'];
            curl_close($curl_com);
            $xml_com = simplexml_load_string($result_com);
            $ejson_com = json_encode($xml_com);
            $djson_com = json_decode($ejson_com, true);

            $com_mobile = $djson_com['Response']['PolicyDetail']['Mobile'];
            $com_email = $djson_com['Response']['PolicyDetail']['Email'];
            $com_mobile = trim($com_mobile);
            $com_email = trim($com_email);
            // echo  $com_mobile. ' & '.$com_email;exit;
            $fdata = [
                'lead_id' => $lead_id,
                'req' => $url_com,
                'res' => $ejson_com,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('tele_renewal_member_logs', $fdata);


            $lead_id_encrypt = encrypt_decrypt_password($lead_id, 'E');
            $click_url = $this->db->query("select click_pss_url from product_master_with_subtype where product_code = 'R06'")->row_array();



            $update_polic_renewal_link =
                $this->db->set('status', '0');
            $this->db->where('policy_number', $policynumber);
            $this->db->update('tele_renewal_triggers');



            $fdata_link = [
                'lead_id' => $lead_id,
                'link' => '',
                'policy_number' => $policynumber,
                'status' => "1",
            ];
            $this->db->insert('tele_renewal_triggers', $fdata_link);


            $insert_id_trigger = $this->db->insert_id();
            $insert_id_trigger_encrypt = encrypt_decrypt_password($insert_id_trigger, 'E');

            $url = base_url('check_bitly_renewal/' . $insert_id_trigger_encrypt);
            // echo $lead_id_encrypt;exit;
            $name_data = "summary";

            $url_req = "https://api-alerts.kaleyra.com/v5/?api_key=A6ef09c1692b5f41a8cd5bf4ac5e22695&method=txtly.create&url=" . urlencode($url) . "&title=xyz";

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
            curl_close($curl);

            $data_txtly = json_decode($result, true);

            if ($data_txtly['txtly'] == '') {
                $data_txtly['txtly'] = $url;
            }


            if (!preg_match("@^[hf]tt?ps?://@", $data_txtly['txtly'])) {
                $data_txtly['txtly'] = "http://" . $data_txtly['txtly'];
            }

            // echo $result;exit;
            $policyrenewal_date_dmy = date("d/m/Y", strtotime($policyrenewal_date));


            $AlertV1 = $data_txtly['txtly'];
            $AlertV2 = $gross_premium_renewal;
            $AlertV3 = $product_name;
            $AlertV4 = $policynumber;
            $AlertV5 = $policyrenewal_date_dmy;
            $AlertV6 = ($combi_flag == 'Yes') ? $product_name : '';
            $AlertV7 = ($combi_flag == 'Yes') ? $policynumber : '';
            $AlertV8 = ($combi_flag == 'Yes') ? $policyrenewal_date_dmy : '';
            $AlertV9 = '';
            $AlertV10 = '';

            $alertID = 'A1645';

            if ($combi_flag == 'Yes') {
                $alertID = 'A1646';
            }

            $alertMode = 3;

            if (strlen($AlertV1) > 30) {
                $alertMode = 1;
            }

            if (empty($com_email)) {
                $alertMode = 2;
            }

            $parameters = [
                "RTdetails" => [

                    "PolicyID" => '',
                    "AppNo" => 'HD100017934',
                    "alertID" =>  $alertID,
                    "channel_ID" => 'Axis Telesales',
                    "Req_Id" => 1,
                    "field1" => '',
                    "field2" => '',
                    "field3" => '',
                    "Alert_Mode" => $alertMode,
                    "Alertdata" =>
                    [
                        "mobileno" => $com_mobile,
                        "emailId" => $com_email,
                        "AlertV1" => $AlertV1,
                        "AlertV2" => $AlertV2,
                        "AlertV3" => $AlertV3,
                        "AlertV4" => $AlertV4,
                        "AlertV5" => $AlertV5,
                        "AlertV6" => $AlertV6,
                        "AlertV7" => $AlertV7,
                        "AlertV8" => $AlertV8,
                        "AlertV9" => $AlertV9,
                        "AlertV10" => $AlertV10,
                    ]

                ]

            ];

            $parameters = json_encode($parameters);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $click_url['click_pss_url'],
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

            $response_click_pss = curl_exec($curl);

            $info = curl_getinfo($curl);
            $response_time_pss = $info['total_time'];

            curl_close($curl);

            $fdata = [
                'req' => json_encode($parameters),
                'res' => json_encode($response_click_pss),
                'lead_id' => $lead_id,
                'response_time' => $response_time_pss,
            ];

            $this->db->insert('telesales_renewal_com_logs', $fdata);


            //update trigger table
            $update_policy =
                $this->db->set('link',  $data_txtly['txtly']);
            $this->db->set('created_at', date('Y-m-d H:i:s'));
            $this->db->set('valid_till', date('Y-m-d H:i:s', strtotime("+3 days")));
            $this->db->where('id', $insert_id_trigger);
            $this->db->update('tele_renewal_triggers');




            // echo $data_txtly['txtly'];exit;
            $update_policy =
                $this->db->set('renewal_response', 'link sent');
            $this->db->set('response_time', $response_time_renewal);
            $this->db->set('renewal_link', $data_txtly['txtly']);
            $this->db->set('digital_officer', $digitalofficer);
            $this->db->set('avnacode', $avCode);
            $this->db->set('last_updated_on', date('Y-m-d H:i:s'));
            $this->db->where('lead_id', $lead_id);
            $this->db->update('telesales_renewal_logs');

            // //lead lapse

            //     $update_policy = $this->db->set('status', 0);
            //     $this->db->set('lapsedon', date('Y-m-d H:i:s'));
            //     $this->db->where('policy_number', $pnumber);
            //     $this->db->where('lead_id !=', $lead_id);
            //     $this->db->update('telesales_renewal_logs');


            //   echo $this->db->last_query(); exit;
            // echo $mmobilenumber;exit;
            $last_4_mob = substr($com_mobile, -4);
            $sms_mob = "******" . $last_4_mob;

            $cust_email_sms = explode("@", $com_email);
            $cust_email_sms_0 = $cust_email_sms[0];
            $email_username_length = strlen($cust_email_sms_0);
            $first_3_letters = substr($cust_email_sms_0, 0, 3);
            $after_3_letters = substr($cust_email_sms_0, 3, $email_username_length - 3);
            $after_3_letters = str_repeat("*", strlen($after_3_letters));

            $email_username = $first_3_letters . $after_3_letters;

            $cust_email_sms_1 = $cust_email_sms[1];

            $cust_email_sms =  $email_username . '@' . $cust_email_sms_1;



            if ($alertMode == 3) {
                $output_msg = "Policy renewal link has been sent to customer registered mobile number " . $sms_mob . " and email id " . $cust_email_sms . " ";
            }
            if ($alertMode == 2) {
                $output_msg = "Policy renewal link has been sent to customer registered mobile number " . $sms_mob . " ";
            }
            if ($alertMode == 1) {
                $output_msg = "Policy renewal link has been sent to customer email id " . $cust_email_sms . " ";
            }


            $output = ['error' => ['ErrorCode' => '00', 'ErrorMessage' => 'Success', 'output_msg' => $output_msg], "Policy_number" => $policynumber, "DoB" => date('Y-m-d', strtotime($mdob)), "Mobile_Number" => $mmobilenumber, "policy_lapsed_flag" => $policy_lapsed_flag, "renewal_status" => $renewal_staus, "renewed_flag" => $renewed_flag, "res" => $ejson, "is_policy_exist" => $is_policy_exist];
        }

        echo json_encode($output);
    }


    public function get_agent_details_renewal()
    {
        $telSalesSession = $this->session->userdata('telesales_session');
        $this->agent_id = encrypt_decrypt_password($telSalesSession['agent_id'], 'D');
        echo json_encode($this->renewal->get_agent_details($this->agent_id));
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


    // public function checkTelesalesRenewalGroup()
    // {

    //     $pnumber = $this->input->post('Policy_Number');
    //     $avCode = $this->input->post('avCode');
    //     $checkStatus = $this->input->post('checkStatus');
    //     $response = $this->renewal->check_renewal_group($pnumber, $checkStatus, $avCode);
    //     echo json_encode($response);
    // }

    public function checkTelesalesRenewalGroup()
    {

        // $pnumber = $this->input->post('Policy_Number');
        // $avCode = $this->input->post('avCode');
        // $checkStatus = $this->input->post('checkStatus');
        // $response = $this->renewal->check_renewal_group($pnumber, $checkStatus, $avCode);
        // echo json_encode($response);

        extract($this->input->post(null, true));

        $confirm_policy_coi_number_check=explode(",",$confirm_policy_coi_number);

        // print_pre($confirm_policy_coi_number_check);exit;

        if(!empty($confirm_policy_coi_number_check)){
            $newconfirm_policy_coi_number=$confirm_policy_coi_number_check[0];
        }else{
            $newconfirm_policy_coi_number=$confirm_policy_coi_number;
        }

        // print_pre($newconfirm_policy_coi_number);exit;
        $otherdataapi="https://bizpre.adityabirlahealth.com/ABHICL_HealthCoreXML/Service1.svc/PolicyDetail/".trim($newconfirm_policy_coi_number)."/null";

        // print_pre($otherdataapi);exit;



        $othercurl = curl_init();
        curl_setopt_array($othercurl, array(
            CURLOPT_URL => $otherdataapi,
            CURLOPT_RETURNTRANSFER => true,    
        ));

        $otherresult = curl_exec($othercurl);

        curl_close($othercurl);        
        $otherdata=json_encode($otherresult,TRUE);
        $xml_snippet = simplexml_load_string( $otherresult );
        $other_json_convert = json_encode( $xml_snippet,TRUE );


        // print_pre($other_json_convert);
        // exit;

        if (isset($retrigger)) {
            if ($retrigger == 'yes') {
                $get_lead_id_grp =  $this->db->select("lead_id_group")
                    ->from("telesales_renewal_logs")
                    ->where("lead_id", $lead_id)
                    ->get()
                    ->row_array();
                // echo $this->db->last_query();exit;
                $lead_id_grp = $get_lead_id_grp['lead_id_group'];
                


            }
        } else {
            $retrigger == 'no';
        }
        // echo $lead_id_grp;exit;
        if (empty($lead_id_grp) || $lead_id_grp == "") {
            $get_lead_id_grp =  $this->db->select("lead_id,amount")
                ->from("telesales_renewal_group")
                ->where("hb_certificate_no", $confirm_policy_coi_number)
                ->get()
                ->row_array();
            if (!empty($get_lead_id_grp)) {
                $lead_id_grp = $get_lead_id_grp['lead_id'];
                //$eamount=$this->db->select('SUM(amount) AS amount')->from('telesales_renewal_group')->where('lead_id',$lead_id_grp)->get()->row_array()['amount']; 
                 $eamount = $get_lead_id_grp['amount'];
            } else {
                $output = ['error' => ['ErrorCode' => '0001', 'ErrorMessage' => 'Failed', 'output_msg' => "Record does not exist in master"]];
                echo json_encode($output);
                exit;
            }
        }
        // print_r($this->db->last_query());
        // print_r($eamount);exit;

        $group_master =  $this->db->select("*")
            ->from("telesales_renewal_group")
            ->where("lead_id", $lead_id_grp)
            ->group_by('hb_certificate_no')
            ->get()
            ->num_rows();

        $check_policy =  $this->db->select("lead_id,id")
            ->from("telesales_renewal_logs")
            ->where("lead_id_group", $lead_id_grp)
            ->order_by("id", "DESC")
            ->get()
            ->row_array();

        // print_r($check_policy);
        //Check Payment Status
        $check_payment=$check_policy['lead_id'];
        $check_payment_status=$this->renewal->check_group_lead_payment_status($check_payment);
        
        if(strtolower($check_payment_status['payment_status'])=='success'){
            $output = ['error' => ['ErrorCode' => "0014", 'ErrorMessage' => 'Failed', 'output_msg' => "Payment already done"]];
            echo json_encode($output);
            exit;
        }

        // echo print_r($check_payment_status);    
        // exit;

        

        //16-04-2021
        if ($check_policy) {
            $is_policy_exist = 'yes';
        } else {
            $is_policy_exist = 'no';
        }

        if ($group_master > 1) {
            // $combi_flag = "yes";
        } else if ($group_master == 1) {
            // $combi_flag = "no";
        } else if ($group_master == 0) {
            $output = ['error' => ['ErrorCode' => '0001', 'ErrorMessage' => 'Failed', 'output_msg' => "Record does not exist in master"]];
            echo json_encode($output);
            exit;
        }

        if ($group_master > 0 && !empty($confirm_policy_coi_number) && $retrigger == 'no') {
            $group_master_check =  $this->db->select("*")
                ->from("telesales_renewal_group")
                ->where("lead_id", $lead_id_grp)
                ->where("hb_certificate_no", $confirm_policy_coi_number)
                ->get()
                ->num_rows();

            if ($group_master_check == 0) {
                $output = ['error' => ['ErrorCode' => '0002', 'ErrorMessage' => 'Failed', 'output_msg' => "Please enter valid COI Number"]];
                echo json_encode($output);
                exit;
            }
        }


        $get_master_policy_no =  $this->db->select("policy_no")
            ->from("api_proposal_response")
            ->where("certificate_number", $confirm_policy_coi_number)
            ->get()
            ->row_array();

        $get_master_policy_no = $get_master_policy_no['policy_no'];

        if (empty($get_lead_id_grp)) {
            $this->db1 = $this->load->database('axis_retail', true);
            $get_master_policy_no =  $this->db1->select("policy_no")
                ->from("api_proposal_response")
                ->where("certificate_number", $confirm_policy_coi_number)
                ->get()
                ->row_array();

            $get_master_policy_no = $get_master_policy_no['policy_no'];
        }

        // if(empty($get_lead_id_grp)){
        //     $output = ['error' => ['ErrorCode' => '0003', 'ErrorMessage' => 'Failed', 'output_msg' => "Master Policy Number Not Found"]];
        //     echo json_encode($output);
        //     exit;
        // }


        $url = "https://bizpre.adityabirlahealth.com/ABHICL_GroupRenewal/Service1.svc/CombiGroupRenewalCheck";



        // $data = array(
        //     "Lead_Id" => $lead_id_grp,
        //     "master_policy_number" => $get_master_policy_no,
        //     "certificate_number" => $confirm_policy_coi_number,
        //     "dob" => $dob,
        //     "proposer_mobileNumber" => $mobile_number
        // );

        $data = array(
            "Lead_Id" => $lead_id_grp,
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
        $response_time_renewal = $info['total_time'];
        curl_close($curl);


        $djson = json_decode($result, TRUE);
        $ErrorCode = $djson['error'][0]['ErrorCode'];
        $ErrorMessage = $djson['error'][0]['ErrorMessage'];

        if ($ErrorCode != '00' && !empty($djson)) {
            if($ErrorCode == null){
                $ErrorCode = "0020";
                $ErrorMessage = "Could not get details, please try again.";
            }
            $output = ['error' => ['ErrorCode' => $ErrorCode, 'ErrorMessage' => 'Failed', 'output_msg' => $ErrorMessage]];
            echo json_encode($output);
            exit;
        } else if (empty($djson)) {
            $output = ['error' => ['ErrorCode' => '0010', 'ErrorMessage' => 'Failed', 'output_msg' => 'could not get details, please try again!']];
            echo json_encode($output);
            exit;
        }



        // if ($djson['response']['policyData'][0]['Sum_insured_type'] == 'Family Floater') {
        //     $policy_fi_type = "Family Floater";
        // } else if ($djson['response']['policyData']['Sum_insured_type'] == 'Individual') {
        //     $policy_fi_type = "Individual";
        // }


        if (!empty($djson['response']['policyData'][0]['Sum_insured_type'])) {
            $policy_fi_type = "Family Floater";
            // $alertpaymentlink='A1648';
        } else if (!empty($djson['response']['policyData']['Sum_insured_type'])) {
            $policy_fi_type = "Individual";
            // $alertpaymentlink='A1647';
        }
        
        

        if ($policy_fi_type = "Family Floater") {
            $Policy_lapsed_flag =  $djson['response']['policyData'][0]['Policy_lapsed_flag'];
            $Renewed_Flag =  $djson['response']['policyData'][0]['Renewed_Flag'];
            $Renewable_Flag =  $djson['response']['policyData'][0]['Renewable_Flag'];
            $CustomerName =  $djson['response']['policyData'][0]['Name_of_the_proposer'];
            $Email =  $djson['response']['policyData'][0]['Proposer_Email'];
            $PhoneNo =  $djson['response']['policyData'][0]['Proposer_MobileNo'];
           // $FinalPremium =  $djson['response']['policyData'][0]['premium']['Renewal_Gross_Premium'];
            $product_name =  $djson['response']['policyData'][0]['Name_of_product'];
            $product_name2 =  $djson['response']['policyData'][1]['Name_of_product'];
            

            $Policy_renewal_date =  date('dd/mm/yyyy',strtotime($djson['response']['policyData'][0]['Policy_renewal_date']));
            $policynumber =  $djson['response']['policyData'][0]['Certificate_number'];
            $Name_of_the_proposer =  $djson['response']['policyData'][0]['Name_of_the_proposer'];
            $Policy_expiry_date =  date('dd/mm/yyyy',strtotime($djson['response']['policyData'][0]['Policy_expiry_date']));
            $Policy_renewal_date =  $djson['response']['policyData'][0]['Policy_renewal_date'];
            $Policy_renewal_date2 =  $djson['response']['policyData'][1]['Policy_renewal_date'];

            $MaterPolicyNumber =  $djson['response']['policyData'][0]['MaterPolicyNumber'];
            $member_array = $djson['response']['policyData'][0]['Members'];

            $payment_page_product_name=array();
            foreach($djson['response']['policyData'] as $key => $values){
                array_push($payment_page_product_name,$djson['response']['policyData'][$key]['Name_of_product']);
            }
            $payment_page_product_name=implode(' and ',$payment_page_product_name);


        } else if ($policy_fi_type = "Individual") {
            $Policy_lapsed_flag =  $djson['response']['policyData']['Policy_lapsed_flag'];
            $Renewed_Flag =  $djson['response']['policyData']['Renewed_Flag'];
            $Renewable_Flag =  $djson['response']['policyData']['Renewable_Flag'];
            $CustomerName =  $djson['response']['policyData']['Name_of_the_proposer'];
            $Email =  $djson['response']['policyData']['Proposer_Email'];
            $PhoneNo =  $djson['response']['policyData']['Proposer_MobileNo'];
            //$FinalPremium =  $djson['response']['policyData']['premium']['Renewal_Gross_Premium'];
            $product_name =  $djson['response']['policyData']['Name_of_product'];
            $Policy_renewal_date =  date('dd/mm/yyyy',strtotime($djson['response']['policyData']['Policy_renewal_date']));
            $policynumber =  $djson['response']['policyData']['Certificate_number'];
            $Name_of_the_proposer =  $djson['response']['policyData']['Name_of_the_proposer'];
            $Policy_expiry_date =  date('dd/mm/yyyy',strtotime($djson['response']['policyData']['Policy_expiry_date']));
            // $Policy_renewal_date =  $djson['response']['policyData']['Policy_renewal_date'];
            $MaterPolicyNumber =  $djson['response']['policyData']['MaterPolicyNumber'];
            $member_array = $djson['response']['policyData']['Members'];
            $payment_page_product_name=$djson['response']['policyData']['Name_of_product'];

        }


        // $Renewed_Flag =  "no";
        // $Renewable_Flag =  "yes";

        // $MaterPolicyNumber = "71-20-00069-00-00";

        // $check_max_age_allowed =  $this->db->select("max(c.max_age) AS max_age")
        //     ->from("product_master_with_subtype a, employee_policy_detail b, policy_age_limit c")
        //     ->where("a.id = b.product_name")
        //     ->where("b.policy_detail_id = c.policy_detail_id ")
        //     ->where("a.master_policy_no", $MaterPolicyNumber)
        //     ->get()
        //     ->row_array();

        // if(!$check_max_age_allowed){
        //     $this->db1 = $this->load->database('axis_retail', true);
        //     $check_max_age_allowed =  $this->db1->select("max(c.max_age) AS max_age")
        //     ->from("product_master_with_subtype a, employee_policy_detail b, policy_age_limit c")
        //     ->where("a.id = b.product_name")
        //     ->where("b.policy_detail_id = c.policy_detail_id ")
        //     ->where("a.master_policy_no", $MaterPolicyNumber)
        //     ->get()
        //     ->row_array();

        // }

        // $max_age_allowed = $check_max_age_allowed['max_age'];
        // if($max_age_allowed == "0" || $max_age_allowed == NULL || $max_age_allowed == ""){
        //     $max_age_allowed = "no_limit";
        // }

       
        // if($max_age_allowed != "no_limit"){
        //     foreach($member_array as $member_key => $single_member_res){
        //         $member_dob =  $member_array[$member_key]["DoB"];

        //         if($member_dob != NULL || $member_dob != ""){
        //             // $diff = (date('Y') - date('Y',strtotime($member_dob)));

        //             $end_date_check = new DateTime($Policy_expiry_date);
        //             // $end_date_check = new DateTime('now');

        //             $end_date_check->modify('+1 day');
                    
        //             $diff = DateTime::createFromFormat('m/d/Y', $member_dob)
        //                  ->diff($end_date_check)
        //                  ->y;
                         
        //             // echo $diff;exit;

        //             if($diff > $max_age_allowed){
        //                 $output = ['error' => ['ErrorCode' => "0012", 'ErrorMessage' => 'Failed', 'output_msg' => "Member age is not allowed for renewal!"]];

        //                 echo json_encode($output);
        //                 exit;
        //             }
        //         }
        //     }
        // }
        

        $product_array = $djson['response']['policyData'];

        if(count($product_array) > 1){
            $combi_flag = "Yes";
        }else{
            $combi_flag = "No";
        }

        $certs_fetched = '';
        $FinalPremium = 0;
        foreach ($product_array as $key => $single_product) {
            $certs_fetched .= $single_product["Certificate_number"] . ',';
            $FinalPremium = $FinalPremium + $single_product['premium']['Renewal_Gross_Premium'];
        }

        // print_r((int)$eamount);
        // echo"<br>"; 
        // print_r($FinalPremium);
        // echo $hb_mismatch;exit;

        $Policy_lapsed_flag='no';
        $Renewed_Flag='no';
        $Renewable_Flag='yes';  

        if($hb_mismatch==0){

                if((int)$eamount!=$FinalPremium){

                    // $output = ['error' => ['ErrorCode' => "0013", 'ErrorMessage' => 'Failed', 'output_msg' => "Renewal premium mismatch with HB, Please click OK to proceed with HB premium!"]];
                    $output = ['error' => ['ErrorCode' => '0013', 'ErrorMessage' => 'Failed', 'output_msg' => "Renewal premium mismatch with HB, Please click OK to proceed with HB premium!", 'is_policy_exist' => $is_policy_exist], 'policy_lapsed_flag' => $Policy_lapsed_flag, "renewal_status" => $Renewable_Flag, "renewed_flag" => $Renewed_Flag];

                    echo json_encode($output);
                    exit;

                }
        }

        $certs_fetched = rtrim($certs_fetched, ',');

        

        // echo $Policy_lapsed_flag;
        // echo "<br>".$Renewed_Flag;
        // echo "<br>".$Renewable_Flag;
        // exit;

       
        if (strtolower($Policy_lapsed_flag) == "no" && strtolower($Renewed_Flag) == "no" && strtolower($Renewable_Flag) == "yes") {

            $output = ['error' => ['ErrorCode' => $ErrorCode, 'ErrorMessage' => 'Success', 'output_msg' => $ErrorMessage, 'is_policy_exist' => $is_policy_exist], 'policy_lapsed_flag' => $Policy_lapsed_flag, "renewal_status" => $Renewable_Flag, "renewed_flag" => $Renewed_Flag];


            if ($send_trigger == "trigger_link") {
                $update_policy = $this->db->set('status', 0);
                $this->db->where('lead_id_group', $lead_id_grp);
                $this->db->update('telesales_renewal_logs');

                $update_policy = $this->db->set('status', 0);
                $this->db->where('lead_id_grp', $lead_id_grp);
                $this->db->update('tele_renewal_triggers');

               
                $previous_leads =    $this->db
                ->select('id')
                ->from('telesales_renewal_logs')
                ->where('lead_id_group', $lead_id_grp)
                ->get();

                $previous_triggers = $previous_leads->num_rows();

                $previous_triggers = $previous_triggers + 1;

                // $lead_id = $avCode_grp.'-'.$confirm_policy_coi_number.'-'.$previous_triggers;
                 $lead_id = $this->renewal->getleadid();

                $fdata = [
                    'lead_id' => $lead_id,
                    'lead_id_group' => $lead_id_grp,
                    'avnacode' => $avCode_grp,
                    'req' => $data_string,
                    'res' => $result,
                    'other_details' => $other_json_convert,                    
                    'customer_name' => $CustomerName,
                    'email' => $Email,
                    'product_type' => 'group',
                    'premium' => $FinalPremium,
                    'ref_number' => '',
                    'ref_number' => '',
                    'policy_number' => $certs_fetched,
                    'dob' => date("Y-m-d", strtotime($dob)),
                    'mobile_no' => $PhoneNo,
                    'response_time' => $response_time_renewal,
                    'renewal_response' => '',
                    'login_id' => '',
                    'status' => '1',
                    'digital_officer' => $digitalOfficer_grp,
                    'location' => $location_grp,
                    'bb_code' => "2110705",
                    'source' => "Axis Telesales",
                    'renewstatus' => ''
                ];

                $this->db->insert('telesales_renewal_logs', $fdata);

                $ProductInfo = "Telesales Group Renewal";

                $payment_link = $this->payment_redirect($lead_id, $CustomerName, $Email, $PhoneNo, $FinalPremium, $ProductInfo,$payment_page_product_name);
                if ($payment_link && $payment_link['Status'] == "1") {


                    $payment_link_cust = $payment_link['PaymentLink'];

                    $fdata_link = [
                        'lead_id' => $lead_id,
                        'lead_id_grp' => $lead_id_grp,
                        'policy_number' => $certs_fetched,
                        'status' => "1",
                    ];
                    $this->db->insert('tele_renewal_triggers', $fdata_link);


                    $insert_id_trigger = $this->db->insert_id();
                    $insert_id_trigger_encrypt = encrypt_decrypt_password($insert_id_trigger, 'E');

                    $url = base_url('check_bitly_renewal_grp/' . $insert_id_trigger_encrypt);
                    // echo $lead_id_encrypt;exit;
                    $name_data = "summary";

                    $url_req = "https://api-alerts.kaleyra.com/v5/?api_key=A6ef09c1692b5f41a8cd5bf4ac5e22695&method=txtly.create&url=" . urlencode($url) . "&title=xyz";

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
                    curl_close($curl);

                    $data_txtly = json_decode($result, true);

                    if ($data_txtly['txtly'] == '') {
                        $data_txtly['txtly'] = $url;
                    }


                    if (!preg_match("@^[hf]tt?ps?://@", $data_txtly['txtly'])) {
                        $data_txtly['txtly'] = "http://" . $data_txtly['txtly'];
                    }

                    $update_policy =
                        $this->db->set('created_date', date('Y-m-d H:i:s'));
                    $this->db->set('last_updated_on', date('Y-m-d H:i:s'));
                    $this->db->set('renewal_link',  $data_txtly['txtly']);
                    $this->db->set('renewal_response', 'link sent');
                    $this->db->where('lead_id', $lead_id);
                    $this->db->update('telesales_renewal_logs');



                    // $fdata = [
                    //     'link' =>  $data_txtly['txtly'],
                    //     'payment_link' =>  $payment_link_cust,
                    //     'created_at' => date('Y-m-d H:i:s'),
                    //     'valid_till' => date('Y-m-d H:i:s', strtotime("+3 days")),
                    // ];

                    // $this->db->insert('tele_renewal_triggers', $fdata);
                    if ($combi_flag == 'Yes') {
                        $certs_fetched_arr = explode(',', $certs_fetched);
                        $first_coi = $certs_fetched_arr[0];
                        $second_coi = $certs_fetched_arr[1];
                    } else {
                        $first_coi = $certs_fetched;
                    }

                    $update_policy =
                        $this->db->set('link', $data_txtly['txtly']);
                    $this->db->set('payment_link', $payment_link_cust);
                    $this->db->set('created_at',  date('Y-m-d H:i:s'));
                    $this->db->set('valid_till', date('Y-m-d H:i:s', strtotime("+60 days")));
                    $this->db->where('id', $insert_id_trigger);
                    $this->db->update('tele_renewal_triggers');

                    $AlertV1 =  $data_txtly['txtly'];
                    $AlertV2 = $FinalPremium;
                    $AlertV3 = $Name_of_the_proposer;
                    $AlertV4 = $product_name;
                    $AlertV5 = $first_coi;
                    $AlertV6 = ($combi_flag == 'Yes') ? $Policy_renewal_date :  $Policy_expiry_date;

                    $AlertV7 = ($combi_flag == 'Yes') ? $product_name2 : '';
                    $AlertV8 = ($combi_flag == 'Yes') ? $second_coi : '';
                    $AlertV9 = ($combi_flag == 'Yes') ? $Policy_renewal_date2:$Policy_expiry_date;


                    // if($combi_flag==true){
                    //     $alertpaymentlink='A1648';
                    // }else{
                    //     $alertpaymentlink='A1647';
                    // }
                    // $alertID = 'A1645';
                    // $alertID = 'A1647';

                    // if ($combi_flag == 'Yes') {
                    //     $alertID = 'A1648';
                    // }else{
                        
                    // }

                    if ($combi_flag == 'Yes') {
                        $alertID = 'A1648';
                        $AlertV1 =  $data_txtly['txtly'];
                        $AlertV2 = $FinalPremium;
                        // $AlertV3 = $Name_of_the_proposer;
                        $AlertV3 = "Axis Bank";
                        $AlertV4 = $product_name;
                        $AlertV5 = $first_coi;
                        $AlertV6 = $Policy_renewal_date;
                        $AlertV7 = $product_name2;
                        $AlertV8 = $second_coi;
                        $AlertV9 = $Policy_expiry_date;
                    }else{
                        $alertID = 'A1647';
                        $AlertV1 =  $data_txtly['txtly'];
                        $AlertV2 = $FinalPremium;
                        // $AlertV3 = $Name_of_the_proposer;
                        $AlertV3 = "Axis Bank";
                        $AlertV4 = $Policy_renewal_date;
                        $AlertV5 = $product_name;
                        // $AlertV6 = $Name_of_the_proposer;
                        $AlertV6 ="Axis Bank";
                        $AlertV7 = $first_coi;
                        $AlertV8 = "";
                        $AlertV9 = "";
                    }

               

                    $alertMode = 3;

                    if (strlen($AlertV1) > 30) {
                        $alertMode = 1;
                    }

                    if (empty($Email)) {
                        $alertMode = 2;
                    }

                    $parameters = [
                        "RTdetails" => [

                            "PolicyID" => '',
                            "AppNo" => 'HD100017934',
                            "alertID" =>  $alertID,
                            "channel_ID" => 'Axis Telesales',
                            "Req_Id" => 1,
                            "field1" => '',
                            "field2" => '',
                            "field3" => '',
                            "Alert_Mode" => $alertMode,
                            "Alertdata" =>
                            [
                                "mobileno" => $PhoneNo,
                                "emailId" => $Email,
                                "AlertV1" => $AlertV1,
                                "AlertV2" => $AlertV2,
                                // "AlertV3" => "Axis Bank",
                               "AlertV3" => $AlertV3,
                                "AlertV4" => $AlertV4,
                                "AlertV5" => $AlertV5,
                                "AlertV6" => $AlertV6,
                                "AlertV7" => $AlertV7,
                                "AlertV8" => $AlertV8,
                                "AlertV9" => $AlertV9,
                                "AlertV10" => $AlertV10,
                            ]

                        ]

                    ];

                    $parameters = json_encode($parameters);

                    // print_pre($parameters);exit;

                    $click_url = $this->db->query("select click_pss_url from product_master_with_subtype where product_code = 'R06'")->row_array();

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $click_url['click_pss_url'],
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

                    $response_click_pss = curl_exec($curl);

                    $info = curl_getinfo($curl);
                    $response_time_pss = $info['total_time'];

                    curl_close($curl);

                    $fdata = [
                        'req' => json_encode($parameters),
                        'res' => json_encode($response_click_pss),
                        'lead_id' => $lead_id,
                        'response_time' => $response_time_pss,
                    ];

                    $this->db->insert('telesales_renewal_com_logs', $fdata);

                    $last_4_mob = substr($PhoneNo, -4);
                    $sms_mob = "******" . $last_4_mob;

                    $cust_email_sms = explode("@", $Email);
                    $cust_email_sms_0 = $cust_email_sms[0];
                    $email_username_length = strlen($cust_email_sms_0);
                    $first_3_letters = substr($cust_email_sms_0, 0, 3);
                    $after_3_letters = substr($cust_email_sms_0, 3, $email_username_length - 3);
                    $after_3_letters = str_repeat("*", strlen($after_3_letters));

                    $email_username = $first_3_letters . $after_3_letters;

                    $cust_email_sms_1 = $cust_email_sms[1];

                    $cust_email_sms =  $email_username . '@' . $cust_email_sms_1;


                    if ($alertMode == 3) {
                        $output_msg = "Policy renewal link has been sent to customer registered mobile number " . $sms_mob . " and email id " . $cust_email_sms . " ";
                    }
                    if ($alertMode == 2) {
                        $output_msg = "Policy renewal link has been sent to customer registered mobile number " . $sms_mob . " ";
                    }
                    if ($alertMode == 1) {
                        $output_msg = "Policy renewal link has been sent to customer email id " . $cust_email_sms . " ";
                    }


                    $output = ['error' => ['ErrorCode' => '00', 'ErrorMessage' => 'Success', 'output_msg' => $output_msg], 'policy_lapsed_flag' => $Policy_lapsed_flag, "renewal_status" => $Renewable_Flag, "renewed_flag" => $Renewed_Flag];
                } else {
                    $output = ['error' => ['ErrorCode' => "0004", 'ErrorMessage' => 'Failed', 'output_msg' => "Could not create payment link, please try again!"]];
                }
            }
        } else {
            // $output = ['error' => ['ErrorCode' => "0005", 'ErrorMessage' => 'Failed', 'output_msg' => "Policy is not renewable"]];
            $output = ['error' => ['ErrorCode' => "0005", 'ErrorMessage' => 'Failed', 'output_msg' => "Policy is not renewable", 'is_policy_exist' => $is_policy_exist], 'policy_lapsed_flag' => $Policy_lapsed_flag, "renewal_status" => $Renewable_Flag, "renewed_flag" => $Renewed_Flag];
        }

        echo json_encode($output);
    }

    public function renewal_view()
    {
        $data['data'] = $this->renewal->product_type();
        $this->load->telesales_template("renewal_view");
    }

    public function group_view()
    {
        $this->load->telesales_template("renewal_group_view_upload");
    }

    public function checkpolicy_status($lead_id,$product_type){
        
        if(strtolower($product_type)=='group'){
            $result=$this->db->select('trl.renewed_policy_number,trgp.res')->from('telesales_renewal_logs as trl,telesales_renewal_grp_payments as trgp')->where('trl.lead_id=trgp.lead_id')->where('trl.renewed_policy_number!=','')->where('status',1)->where('renewal_status',0)->where('trl.lead_id',$lead_id)->get()->row_array();
            $response=json_decode($result['res'],true);
            if($response['txnDateTime']){
                $result=$response['txnDateTime'];
            }else{
                $result='';
            }

        }else if(strtolower($product_type)=='retail'){
            $result=$this->db->select('renewed_policy_number,renewal_res')->from('telesales_renewal_logs as trl')->where('trl.renewed_policy_number!=','')->where('trl.lead_id',$lead_id)->where('status',1)->where('renewal_status',0)->get()->row_array();            
            $response=json_decode($result['renewal_res'],true);
            $response=json_decode($response,true);
                        
            if($response['ResponseData']['PolicyStartDate']){
                $result=date('Y-m-d H:i:s',strtotime($response['ResponseData']['PolicyStartDate']));

            }else{
                $result='';
            }

        }

        return $result;
    }

    public function lastest_link_trigered($lead_id){
        $this->db->select('link,created_at');
        $this->db->from('tele_renewal_triggers');
        $this->db->where('lead_id',$lead_id);
        $this->db->order_by('created_at','DESC');
        $this->db->limit(1);
        $result=$this->db->get()->row_array();
        return $result['created_at'];
    }

    public function getpayment_details_grp($lead_id,$product_type){
        // $lead_id=111503;
        if(strtolower($product_type)=='group'){

            $this->db->select('lead_id,res');
            $this->db->from('telesales_renewal_grp_payments');
            $this->db->where('lead_id',$lead_id);
            $this->db->where('payment_status','success');
            $result=$this->db->get()->row_array();
            $res_result=json_decode($result['res'],true);
            // print_r($res_result['TxRefNo']);            
            $result=$res_result['TxRefNo'];
    
        }else if(strtolower($product_type)=='retail'){
            $result=$this->db->select('renewed_policy_number,renewal_res')->from('telesales_renewal_logs as trl')->where('trl.renewed_policy_number!=','')->where('trl.lead_id',$lead_id)->where('status',1)->where('renewal_status',0)->get()->row_array();            
            $response=json_decode($result['renewal_res'],true);
            $response=json_decode($response,true);
                        
            if($response['ResponseData']['TransactionID']){
                $result=$response['ResponseData']['TransactionID'];

            }else{
                $result='';
            }

        }

        return $result;
    }



    public function get_telesales_data_old()
    {

        $fetch_data = $this->renewal->all_retail_data();
        $i = 1;

        foreach ($fetch_data as $row) {
            // print_pre($row->renewal_res);
            $renewal_res=json_decode($row->renewal_res,true);

            $renewal_res=json_decode($renewal_res,true);

            // print_pre($renewal_res);
            // exit;

            $response=$row->res;
            $dresponse=json_decode($response,true);

            $net_premium = 0;

            if(strtolower($row->product_type)=='group'){
    
                if (!empty($dresponse['response']['policyData'][0]['Sum_insured_type'])) {
                    $policy_fi_type = "Family Floater";
                } else if (!empty($dresponse['response']['policyData']['Sum_insured_type'])) {
                    $policy_fi_type = "Individual";
                }
                // echo $policy_fi_type;
                // print_pre($dresponse['response']['policyData'][0]['premium']['Renewal_Gross_Premium']);
    
                
                if ($policy_fi_type = "Family Floater") {
                    $final='';
                    foreach($dresponse['response']['policyData'] as $key =>$value){
                        $final +=$dresponse['response']['policyData'][$key]['premium']['Renewal_Gross_Premium'];
                        $net_premium +=$dresponse['response']['policyData'][$key]['premium']['Renewal_Net_Premium'];

                    }
    
    
                } else if ($policy_fi_type = "Individual") {
                    $final='';
                    $final=$dresponse['response']['policyData']['premium']['Renewal_Gross_Premium'];
                    $net_premium=$dresponse['response']['policyData']['premium']['Renewal_Net_Premium'];
                }
    
    
            }

            if(strtolower($row->product_type)=='retail'){

                $final=$dresponse['response']['policyData']['premium']['Renewal_Gross_Premium'];
                $net_premium=$dresponse['response']['policyData']['premium']['Renewal_Net_Premium'];

            }

            // print_pre($final);exit;

            $sub_data = [];
            $sub_data[] = $i++;
            $sub_data[] = $row->lead_id;

            $sub_data[] = $row->avnacode;
            $sub_data[] = $row->digital_officer;
            $sub_data[] = $row->location;
            $sub_data[] = ucfirst($row->product_type);
            $sub_data[] = $row->policy_number;
            $disabled = '';
			$issuance_date_time = '';
			$Policy_issuance_date = '';
			$ProductName_det = '';
			$PolicyPaymentStatus = '';
			$TransactionID_det = '';
			$PolicyStatus = '';

            //status 0 is lapsed, 1 is active, 4 is Renewed from other mode
            if ($row->status == "4") {
                $sub_data[] = "Policy is not renewed using Axis portal link ";
                $disabled = 'disabled';
				$issuance_date_time = (!empty($renewal_res['ResponseData']['PaymentReceviedDate'])) ? $renewal_res['ResponseData']['PaymentReceviedDate'] : "Policy is not renewed using Axis portal link";
				$Policy_issuance_date = (!empty($renewal_res['ResponseData']['PolicyIssuedDate'])) ? $renewal_res['ResponseData']['PolicyIssuedDate'] : "Policy is not renewed using Axis portal link";
				$ProductName_det =  (!empty($renewal_res['ResponseData']['ProductName'])) ? $renewal_res['ResponseData']['ProductName'] : "Policy is not renewed using Axis portal link";
				$PolicyPaymentStatus = (!empty($renewal_res['ResponseData']['PolicyPaymentStatus'])) ? $renewal_res['ResponseData']['PolicyPaymentStatus'] : "Policy is not renewed using Axis portal link";
				$TransactionID_det = (!empty($renewal_res['ResponseData']['TransactionID'])) ? $renewal_res['ResponseData']['TransactionID'] : "Policy is not renewed using Axis portal link";
				$PolicyStatus = (!empty($renewal_res['ResponseData']['PolicyStatus'])) ? $renewal_res['ResponseData']['PolicyStatus'] : "Policy is not renewed using Axis portal link";
		  } else if (!empty($row->renewed_policy_number)) {
                $sub_data[] = "Policy Renewed";
				$issuance_date_time = $renewal_res['ResponseData']['PaymentReceviedDate'];
				$Policy_issuance_date = $renewal_res['ResponseData']['PolicyIssuedDate'];
				$ProductName_det =   $renewal_res['ResponseData']['ProductName'];
				$PolicyPaymentStatus =  $renewal_res['ResponseData']['PolicyPaymentStatus'];
				$TransactionID_det = $renewal_res['ResponseData']['TransactionID'];
				$PolicyStatus = $renewal_res['ResponseData']['PolicyStatus'];
		   } else {
                if ($row->renewal_response == 'link sent') {
                    $sub_data[] = "Link Triggered";
					$issuance_date_time = (!empty($renewal_res['ResponseData']['PaymentReceviedDate'])) ? $renewal_res['ResponseData']['PaymentReceviedDate'] : "Payment Not Done";
					$Policy_issuance_date = (!empty($renewal_res['ResponseData']['PolicyIssuedDate'])) ? $renewal_res['ResponseData']['PolicyIssuedDate'] : "Payment Not Done";
					$ProductName_det =  (!empty($renewal_res['ResponseData']['ProductName'])) ? $renewal_res['ResponseData']['ProductName'] : "Payment Not Done";
					$PolicyPaymentStatus = (!empty($renewal_res['ResponseData']['PolicyPaymentStatus'])) ? $renewal_res['ResponseData']['PolicyPaymentStatus'] : "Payment Not Done";
					$TransactionID_det = (!empty($renewal_res['ResponseData']['TransactionID'])) ? $renewal_res['ResponseData']['TransactionID'] : "Payment Not Done";
					$PolicyStatus = (!empty($renewal_res['ResponseData']['TransactionID'])) ? $renewal_res['ResponseData']['PolicyStatus'] : "Payment Not Done";

                } else {
                    $sub_data[] = "Link Trigger Pending";
					$issuance_date_time = (!empty($renewal_res['ResponseData']['PaymentReceviedDate'])) ? $renewal_res['ResponseData']['PaymentReceviedDate'] : "Payment Not Done";
					$Policy_issuance_date = (!empty($renewal_res['ResponseData']['PolicyIssuedDate'])) ? $renewal_res['ResponseData']['PolicyIssuedDate'] : "Payment Not Done";
					$ProductName_det =  (!empty($renewal_res['ResponseData']['ProductName'])) ? $renewal_res['ResponseData']['ProductName'] : "Payment Not Done";
					$PolicyPaymentStatus = (!empty($renewal_res['ResponseData']['PolicyPaymentStatus'])) ? $renewal_res['ResponseData']['PolicyPaymentStatus'] : "Payment Not Done";
					$TransactionID_det = (!empty($renewal_res['ResponseData']['TransactionID'])) ? $renewal_res['ResponseData']['TransactionID'] : "Payment Not Done";
					$PolicyStatus = (!empty($renewal_res['ResponseData']['TransactionID'])) ? $renewal_res['ResponseData']['PolicyStatus'] : "Payment Not Done";

                }
            }

            $sub_data[] = $final;
            // $sub_data[] =$renewal_res['ResponseData']['GrossPermium'];

            
            $sub_data[] = $net_premium;

          //  $sub_data[] = $this->checkpolicy_status($row->lead_id,$row->product_type);
 $sub_data[] = $issuance_date_time;
            if ($row->renewal_updated == '') {

                $sub_data[] = date('Y-m-d H:i:s', strtotime($row->last_updated_on));
            } else {
                $sub_data[] = date('Y-m-d H:i:s', strtotime($row->renewal_updated));
            }

            $digital_officer = $row->digital_officer;

            if (!empty($row->renewed_policy_number)) {
                $disabled = 'disabled';
            }
            
            $check_group_lead_payment_status=$this->renewal->check_group_lead_payment_status($row->lead_id);

            if (!empty($check_group_lead_payment_status)) {
                $disabled = 'disabled';
            }


            $sub_data[] = "
            <div class='text-center'>
            <button type='button' class='btn btn-cta btn-xs tele_re_dt_tl' policy-number='$row->policy_number' data-av='$row->avnacode' dob='$row->dob' mobile-number='$row->mobile_no' product-type='$row->product_type' id='trigger' data-do='$digital_officer' lead-id='$row->lead_id' data-loc='$row->location'  $disabled>Trigger Link <i class='ti-link'></i></button>
            <br>
            <button lead_id='$row->policy_number' class='btn btn-cta btn-xs tele_re_dt_audit' id='audit'>Audit <i class='ti-pencil-alt'></i></button>
            </div>
            ";

            if (!empty($row->renewed_policy_number)  && $row->status == "1" ) {
                $sub_data[] = $row->renewed_policy_number;
            } else if ($row->status == "4") {
                $sub_data[] = "Policy is not renewed using Axis portal link ";
            } else {
                $sub_data[] = "Policy Renewal Pending";
            }

         $sub_data[] = $row->created_date;//$this->lastest_link_trigered($row->lead_id);
            // $sub_data[] =$renewal_res['ResponseData']['GrossPermium'];            
            $sub_data[] = $issuance_date_time;
            $sub_data[] =$Policy_issuance_date;
            $sub_data[] =$ProductName_det;
            $sub_data[] =$PolicyStatus;
            $sub_data[] =$PolicyPaymentStatus;
           // $sub_data[] =$this->getpayment_details_grp($row->lead_id,$row->product_type);
			$sub_data[] = $TransactionID_det;
            
            $data[] = $sub_data;
        }

        // print_pre($data);
        // exit;

        if (empty($data)) {
            $data = "";
        }

        $output = array(
            "draw"            => intval($_POST["draw"]),
            "recordsTotal" => $this->renewal->total_retail_data(),
            "recordsFiltered" => $this->renewal->total_retail_data(),
            "data" => $data,
        );

        echo json_encode($output);
    }


	public function upload_group()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
        $file_name = $_FILES['filetoUpload']['tmp_name'];
        $file_names = $_FILES['filetoUpload']['name'];
        if ($file_names != '' || $file_names != null || !empty($file_names))
            $ext = pathinfo($file_names, PATHINFO_EXTENSION);
        $allowed_types = ['xlsx', 'xls', 'csv'];

        if (!in_array($ext, $allowed_types)) {
            $error = array('errorCode' => '1', 'msg' => 'File type not allowwed');
            echo json_encode($error);
        }

        $inputFileName = $file_name;

        $this->load->library("excel");
        $config1   =  [
            'filename'    => $inputFileName,              // prove any custom name here
            'use_sheet_name_as_key' => false,               // this will consider every first index from an associative array as main headings to the table
            'use_first_index'         => true, // if true then it will set every key as sheet name for appropriate sheet
        ];

        $sheetdata = [];
        $y = [];
        $vs = [];
        $sheetdatas = [];
        $data = [];
        $sheetdata = Excel::import($inputFileName, $config1);

        if (!is_array($sheetdata)) {
            $get_data = array('errorCode' => '1', 'msg' => $sheetdata);

            $flag = 0;
        }

        $temp = 0;
        if (!empty($sheetdata)) {
            $arr = array();
            $y = array_keys($sheetdata);
            foreach ($y as $value) {
                foreach ($sheetdata[$value] as $val) {

                    if (!empty($val)) {
                        $sheetdatas = array_filter($val);
                        if (!empty($sheetdatas)) {
                            // if ($sheetdatas['A'] == 'Lead Id') {
                            //     continue;
                            // }
                            if ($sheetdatas['A'] != 'Lead Id') {
                                $get_data = array('errorCode' => '1', 'msg' => "Please Upload Propoer Excel Sheet");
                            }
                            $temp = 1;
                            $flag = 1;
                            $check_agent_code = $sheetdatas['B'];


                            $lead_id = $sheetdatas['A'];
                            $hb_certificate_no = $sheetdatas['B'];
                            $portal_certificate_no = $sheetdatas['C'];
                            $customer_name = $sheetdatas['F'];
                            $customer_email = $sheetdatas['G'];
                            $customer_contact = $sheetdatas['H'];
                            $amount = $sheetdatas['K'];
                            $eexpiry=$sheetdatas['M'];
                            if(empty($sheetdatas['P'])){
                                $auto_debit='no';
                            }else{
                                $auto_debit=$sheetdatas['P'];
                            }


                            if(empty($sheetdatas['Q'])){
                                $hr_amount=0;
                            }else{
                                $hr_amount=$sheetdatas['Q'];
                            }

// echo $auto_debit;

// echo "<br>";

// echo $hr_amount;
// exit;
//echo $eexpiry;			
//echo $sheetdatas['L'];exit;

                      //      $date=date_create($sheetdatas['L']);

                        //    $expiry = date_format($date,"Y-m-d");

                            if(empty($lead_id)||empty($hb_certificate_no)||empty($portal_certificate_no)||empty($customer_name)||empty($customer_email)||empty($customer_contact)||empty($amount)||empty($eexpiry)){

                                $emp=array("fields"=>"Some fields are empty for lead :".$lead_id);
                                array_push($arr,$emp);

                            }
                            if(!empty($lead_id)){
                                $check_duplicate_record = $this->db->query("select hb_certificate_no from telesales_renewal_group where hb_certificate_no = '$check_agent_code'")->row_array();
                                
                                if (count($check_duplicate_record) > 0) {
                                // $get_row = $i+1;
                                // $msg  = 'Agent Code '.$check_agent_code.' is Duplicate , Row A-'.$get_row.' Not Inserted in database.';
                                // array_push($arr,$msg);
                                
                                $update_array[] = array(
                                    'lead_id' => $sheetdatas['A'],
                                    'hb_certificate_no' => $sheetdatas['B'],
                                    'portal_certificate_no' => $sheetdatas['C'],
                                    'ref_id' => $sheetdatas['D'],
                                    'previous_master_policy_number'=>$sheetdatas['E'],
                                    'customer_name' => $sheetdatas['F'],
                                    'customer_email' => $sheetdatas['G'],
                                    'customer_contact' => $sheetdatas['H'],
                                    'upi_link' => $sheetdatas['I'],
                                    'currency' => $sheetdatas['J'],
                                    'amount' => $sheetdatas['K'],
                                    'description' => $sheetdatas['L'],
                                    'expiry_by' => $sheetdatas['M'],
                                    'partial_payment' => $sheetdatas['N'],
                                    'notes_charge' => $sheetdatas['O'],
                                    'auto_debit'=>$auto_debit,
                                    'hr_amount'=>$hr_amount,
                                    'create_at' => date('Y-m-d H:i:s'),
                                );
                            } else {

                                $data[] = array(
                                    'lead_id' => $sheetdatas['A'],
                                    'hb_certificate_no' => $sheetdatas['B'],
                                    'portal_certificate_no' => $sheetdatas['C'],
                                    'ref_id' => $sheetdatas['D'],
                                    'previous_master_policy_number'=>$sheetdatas['E'],
                                    'customer_name' => $sheetdatas['F'],
                                    'customer_email' => $sheetdatas['G'],
                                    'customer_contact' => $sheetdatas['H'],
                                    'upi_link' => $sheetdatas['I'],
                                    'currency' => $sheetdatas['J'],
                                    'amount' => $sheetdatas['K'],
                                    'description' => $sheetdatas['L'],
                                    'expiry_by' => $sheetdatas['M'],
                                    'partial_payment' => $sheetdatas['N'],
                                    'notes_charge' => $sheetdatas['O'],
                                    'auto_debit'=>$auto_debit,
                                    'hr_amount'=>$hr_amount,
                                    'create_at' => date('Y-m-d H:i:s'),
                                );
                            }
                        }
                        }
                    }
                }
            }

	//	print_pre($data);
	//	print_pre($update_array);exit;
            if (!empty($data)) {
                $this->db->insert_batch('telesales_renewal_group', $data);

            }

            if (!empty($update_array)) {
                $this->db->update_batch('telesales_renewal_group', $update_array, 'hb_certificate_no');
            }
            
            // echo $this->db->last_query();
	    $this->db->where('lead_id','Lead Id')->delete('telesales_renewal_group');
 
            unset($sheetdata);
            unset($y);
            unset($vs);
            unset($sheetdatas);
            unset($data);

            if ($temp == 1) {

                if (count($arr) <= 0) {

                    $get_data_arr = array('errorCode' => '0', 'msg' => 'Inserted Successfully');
                    // $get_data_arr = array('errorCode' => '0', 'msg' => 'Renewal Premium validated successfully');
                    
                    echo json_encode($get_data_arr);

                } else {

                    $get_data_arr = array('errorCode' => '1', 'msg' => $arr);
                    echo json_encode($get_data_arr);
                }

                unset($arr);
            } else {

                echo json_encode($get_data);
            }
        }
    }




    public function upload_group_old()
    {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
        $file_name = $_FILES['filetoUpload']['tmp_name'];
        $file_names = $_FILES['filetoUpload']['name'];
        if ($file_names != '' || $file_names != null || !empty($file_names))
            $ext = pathinfo($file_names, PATHINFO_EXTENSION);
        $allowed_types = ['xlsx', 'xls', 'csv'];

        if (!in_array($ext, $allowed_types)) {
            $error = array('errorCode' => '1', 'msg' => 'File type not allowwed');
            echo json_encode($error);
        }

        $inputFileName = $file_name;

        $this->load->library("excel");
        $config1   =  [
            'filename'    => $inputFileName,              // prove any custom name here
            'use_sheet_name_as_key' => false,               // this will consider every first index from an associative array as main headings to the table
            'use_first_index'         => true, // if true then it will set every key as sheet name for appropriate sheet
        ];

        $sheetdata = [];
        $y = [];
        $vs = [];
        $sheetdatas = [];
        $data = [];
        $sheetdata = Excel::import($inputFileName, $config1);

        if (!is_array($sheetdata)) {
            $get_data = array('errorCode' => '1', 'msg' => $sheetdata);

            $flag = 0;
        }

        $temp = 0;
        if (!empty($sheetdata)) {

            $arr = array();
            $y = array_keys($sheetdata);
            foreach ($y as $value) {
                foreach ($sheetdata[$value] as $val) {

                    if (!empty($val)) {
                        $sheetdatas = array_filter($val);

                        if (!empty($sheetdatas)) {

                            // if ($sheetdatas['A'] == 'Lead Id') {
                            //     continue;
                            // }

                            if ($sheetdatas['A'] != 'Lead Id') {
                                $get_data = array('errorCode' => '1', 'msg' => "Please Upload Propoer Excel Sheet");
                            }
                            // print_pre($sheetdatas['A']);

                            // exit('Coming');

                            $temp = 1;
                            $flag = 1;
                            $check_agent_code = $sheetdatas['B'];


                            $lead_id = $sheetdatas['A'];
                            $hb_certificate_no = $sheetdatas['B'];
                            $portal_certificate_no = $sheetdatas['C'];
                            $customer_name = $sheetdatas['E'];
                            $customer_email = $sheetdatas['F'];
                            $customer_contact = $sheetdatas['G'];
                            $amount = $sheetdatas['J'];
                            $eexpiry=$sheetdatas['L'];

                            // $date=date_create($sheetdatas['L']);

                            // $expiry = date_format($date,"Y-m-d");


//   $cls_date = new DateTime('2021-04-21');
//     echo $cls_date->format('Y-m-d');

                            if(empty($lead_id)||empty($hb_certificate_no)||empty($portal_certificate_no)||empty($customer_name)||empty($customer_email)||empty($customer_contact)||empty($amount)||empty($eexpiry)){

                                $emp=array("fields"=>"Some fields are empty for lead :".$lead_id);
                                array_push($arr,$emp);

                            }

                            
                            // $url = "https://bizpre.adityabirlahealth.com/ABHICL_GroupRenewal/Service1.svc/CombiGroupRenewalCheck";

                            // $newdata = array(
                            //     "Lead_Id" => $lead_id,
                            //     "master_policy_number" => "",
                            //     "certificate_number" => "",
                            //     "dob" => "",
                            //     "proposer_mobileNumber" => "",
                            // );
                    
                            // // echo $newdata;

                            // $newdata_string = json_encode($newdata);

                            // // print_r($newdata_string);
 
                            // $curl = curl_init();
                            // curl_setopt_array($curl, array(
                            //     CURLOPT_URL => $url,
                            //     CURLOPT_RETURNTRANSFER => true,
                            //     CURLOPT_ENCODING => "",
                            //     CURLOPT_MAXREDIRS => 10,
                            //     CURLOPT_TIMEOUT => 90,
                            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            //     CURLOPT_CUSTOMREQUEST => "POST",
                            //     CURLOPT_POSTFIELDS => $newdata_string,
                            //     CURLOPT_HTTPHEADER => array(
                            //         "Cache-Control: no-cache",
                            //         "Content-Type: application/json"
                            //     ),
                            // ));
                    
                            // $result = curl_exec($curl);
                            // $djson = json_decode($result, TRUE);

                            // // print_r($djson);

                            // $info = curl_getinfo($curl);
                            // $response_time_renewal = $info['total_time'];
                            // curl_close($curl);

                            // $product_array = $djson['response']['policyData'];
                            // $certs_fetched = '';
                            // $FinalPremium = 0;
                            // foreach ($product_array as $key => $single_product) {
                            //     $certs_fetched .= $single_product["Certificate_number"] . ',';
                            //     $FinalPremium = $FinalPremium + $single_product['premium']['Renewal_Gross_Premium'];
                            // }
                            
                    
                            // if((int)$amount!=$FinalPremium){
                            //     $emp=array("fields"=>"Renewal Premium validated unsuccessfully for :".$lead_id);
                            //     array_push($arr,$emp);

                            // }else{
                            
                            if(!empty($lead_id)){
                                $check_duplicate_record = $this->db->query("select hb_certificate_no from telesales_renewal_group where hb_certificate_no = '$check_agent_code'")->row_array();
                                

                                if (count($check_duplicate_record) > 0) {
                                // $get_row = $i+1;
                                // $msg  = 'Agent Code '.$check_agent_code.' is Duplicate , Row A-'.$get_row.' Not Inserted in database.';
                                // array_push($arr,$msg);
                                $update_array[] = array(
                                    'lead_id' => $sheetdatas['A'],
                                    'hb_certificate_no' => $sheetdatas['B'],
                                    'portal_certificate_no' => $sheetdatas['C'],
                                    'ref_id' => $sheetdatas['D'],
                                    'customer_name' => $sheetdatas['E'],
                                    'customer_email' => $sheetdatas['F'],
                                    'customer_contact' => $sheetdatas['G'],
                                    'upi_link' => $sheetdatas['H'],
                                    'currency' => $sheetdatas['I'],
                                    'amount' => $sheetdatas['J'],
                                    'description' => $sheetdatas['K'],
                                    'expiry_by' => $sheetdatas['L'],
                                    'partial_payment' => $sheetdatas['M'],
                                    'notes_charge' => $sheetdatas['N'],
                                    'create_at' => date('Y-m-d H:i:s'),
                                );
                            } else {

                                $data[] = array(
                                    'lead_id' => $sheetdatas['A'],
                                    'hb_certificate_no' => $sheetdatas['B'],
                                    'portal_certificate_no' => $sheetdatas['C'],
                                    'ref_id' => $sheetdatas['D'],
                                    'customer_name' => $sheetdatas['E'],
                                    'customer_email' => $sheetdatas['F'],
                                    'customer_contact' => $sheetdatas['G'],
                                    'upi_link' => $sheetdatas['H'],
                                    'currency' => $sheetdatas['I'],
                                    'amount' => $sheetdatas['J'],
                                    'description' => $sheetdatas['K'],
                                    'expiry_by' => $sheetdatas['L'],
                                    'partial_payment' => $sheetdatas['M'],
                                    'notes_charge' => $sheetdatas['N'],
                                    'create_at' => date('Y-m-d H:i:s'),

                                );
                            }
                        // }
                        }
                        }
                    }
                }
            }

            if (!empty($data)) {
                $this->db->insert_batch('telesales_renewal_group', $data);
            }

            if (!empty($update_array)) {
                $this->db->update_batch('telesales_renewal_group', $update_array, 'hb_certificate_no');
            }
                        
            $this->db->where('lead_id','Lead Id')->delete('telesales_renewal_group');

            unset($sheetdata);
            unset($y);
            unset($vs);
            unset($sheetdatas);
            unset($data);

            if ($temp == 1) {

                if (count($arr) <= 0) {

                    $get_data_arr = array('errorCode' => '0', 'msg' => 'Inserted Successfully');
                    // $get_data_arr = array('errorCode' => '0', 'msg' => 'Renewal Premium validated successfully');
                    
                    echo json_encode($get_data_arr);

                } else {

                    $get_data_arr = array('errorCode' => '1', 'msg' => $arr);
                    echo json_encode($get_data_arr);
                }

                unset($arr);
            } else {

                echo json_encode($get_data);
            }
        }
    }




    public function get_telesales_data_type()
    {
        $fetch_data = $this->renewal->all_retail_data();
        $i = 1;

        foreach ($fetch_data as $row) {

            $sub_data = [];
            $sub_data[] = $i++;
            $sub_data[] = $row->lead_id;

            $sub_data[] = $row->avnacode;
            $sub_data[] = $row->product_type;
            $sub_data[] = $row->policy_number;
            if (!empty($row->renewed_policy_number)) {
                $sub_data[] = "Policy Renewed";
            } else {
                $sub_data[] = "Policy Renewed Pending";
            }

            if (!empty($row->renewal_updated)) {
                $sub_data[] = $row->renewal_updated;
            } else {
                $sub_data[] = $row->created_date;
            }

            $sub_data[] = "Action";

            if (!empty($row->renewed_policy_number)) {
                $sub_data[] = $row->renewed_policy_number;
            } else {
                $sub_data[] = "Policy Renewed Pending";
            }

            $data[] = $sub_data;
        }

        // print_pre($data);
        // exit;

        $output = array(
            "draw" => intval($_POST["draw"]),
            "recordsTotal" => $this->renewal->total_retail_data(),
            "recordsFiltered" => $this->renewal->total_retail_data(),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function get_telesales_groupdata()
    {


        $fetch_data = $this->renewal->all_group_retail_data();

        $i = 1;

        foreach ($fetch_data as $row) {



            if($row->customer_contact == 0){
                $customer_contact = "";
            }else{
                $customer_contact =$row->customer_contact;
            }

            
            $sub_data = [];
            $sub_data[] = $i++;

            $sub_data[] = $row->lead_id;
            $sub_data[] = $row->hb_certificate_no;
            $sub_data[] = $row->portal_certificate_no;
            $sub_data[] = $row->ref_id;
            $sub_data[] = $row->previous_master_policy_number;
            $sub_data[] = $row->customer_name;
            $sub_data[] = $row->customer_email;
            
            $sub_data[] = $customer_contact;

            $sub_data[] = $row->upi_link;
            $sub_data[] = $row->currency;
            $sub_data[] = $row->amount;
            $sub_data[] = $row->description;
            $sub_data[] = $row->expiry_by;
            $sub_data[] = $row->partial_payment;
            $sub_data[] = $row->notes_charge;
            $sub_data[] = $row->auto_debit;
            $sub_data[] = $row->hr_amount;

            $data[] = $sub_data;
        }

        // print_pre($data);
        // exit;

        $output = array(
            "recordsTotal" =>$this->renewal->total_group_retail_data(),
            "recordsFiltered" =>$this->renewal->total_group_retail_data(),
            "data" => $data,
        );

        echo json_encode($output);
    }
    public function telesales_audit()
    {
        $lead_id = $this->input->post('lead_id');
        $all_data = $this->renewal->all_audit_data($lead_id);

        echo json_encode($all_data);
    }


    public function check_bitly_renewal($lead_id_encrypt)
    {
        $lead_id = encrypt_decrypt_password($lead_id_encrypt, 'D');

        $check_policy =  $this->db->select("*")
            ->from("telesales_renewal_logs")
            ->where("lead_id", $lead_id)
            ->where("status", '1')
            ->get()
            ->row_array();

        if ($check_policy) {
            redirect('https://www.adityabirlacapital.com/healthinsurance/#!/renewal-renew-policy');
        } else {
            echo 'Link expired';
        }
    }

    public function payment_redirect($lead_id, $CustomerName, $Email, $PhoneNo, $FinalPremium, $ProductInfo,$ProductName)
    {


        $lead_id_encrypt = encrypt_decrypt_password($lead_id, 'E');

        // $productname = "Axis Tele Group Renewal";

        $productname = $ProductName;

        $Source = "AX";
        $Vertical = "AXATGRP";
        $PaymentMode = "PP";
        $ReturnURL = base_url('tele_renew_grp_payment_return/' . $lead_id_encrypt);
        $UniqueIdentifier = "LEADID";
        $UniqueIdentifierValue = $lead_id;

        $CKS_data = $Source . "|" . $Vertical . "|" . $PaymentMode . "|" . $ReturnURL . "|" . $UniqueIdentifier . "|" . $UniqueIdentifierValue . "|" . $CustomerName . "|" . $Email . "|" . $PhoneNo . "|" . $FinalPremium . "|" . $productname . "|" . $this->hash_key;

        //  echo $CKS_data;exit;

        $CKS_value = hash($this->hashMethod, $CKS_data);

        $manDateInfo = array(
            "ApplicationNo" => $UniqueIdentifierValue,
            "AccountHolderName" => $CustomerName,
            "BankName" => "Axis Bank",
            "AccountNumber" => null,
            "AccountType" => null,
            "BankBranchName" => null,
            "MICRNo" => null,
            "IFSC_Code" => null,
            "Frequency" => "As and when presented"
        );

        $dataPost = array(
            "signature" => $CKS_value,
            "Source" => $Source,
            "Vertical" => $Vertical,
            "PaymentMode" => $PaymentMode,
            "ReturnURL" => $ReturnURL,
            "UniqueIdentifier" => $UniqueIdentifier,
            "UniqueIdentifierValue" => $UniqueIdentifierValue,
            "CustomerName" => $CustomerName,
            "Email" => $Email,
            "PhoneNo" => $PhoneNo,
            "FinalPremium" => $FinalPremium,
            "ProductInfo" => $productname,
            //"Additionalfield1"=> "",
            "MandateInfo" => $manDateInfo
        );

        $data_string = json_encode($dataPost);

        $encrypted = openssl_encrypt($data_string, $this->algoMethod, $this->encrypt_key, 0);
        $decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);

        $url = "https://pg_uat.adityabirlahealth.com/pgmandate/service/home/sourcelanding";
        $data = array('REQUEST' => $encrypted);

        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_POST, 0);
        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($c);
        $httpCode = curl_getinfo($c, CURLINFO_HTTP_CODE);

        if ($httpCode == 404) {
            $output = ['error' => ['ErrorCode' => '0008', 'ErrorMessage' => 'Failed', 'output_msg' => "Payment request URL Not found, please try again"]];
            echo json_encode($output);
            exit;
            //  exit;
        }
        curl_close($c);

        $fdata = [
            'lead_id' => $lead_id,
            'req' => json_encode($data),
            'res' => $result,
            'product_id' => "R13",
            'type' => "payment_request_post",
        ];

        $this->db->insert('logs_docs_grp_renewal', $fdata);


        $result = json_decode($result, true);
        //  print_r($result);exit;


        return $result;
    }


    public function tele_grp_extract(){
        // $data['data'] = $this->renewal->product_type();
        $this->load->telesales_template("renewal_group_view");
    }
    

    public function get_group_code($family_construct,$sum_insured,$product_code,$sgroup){



        
        if($sgroup=='GP'){
            $this->db->select('group_code');
            $this->db->from('master_group_code');
            $this->db->where('product_code',$product_code);
            $this->db->where('family_construct','1A');
            $this->db->where('si_per_member',$sum_insured);
            $result=$this->db->get()->row_array();

        }else{

            $this->db->select('group_code');
            $this->db->from('master_group_code');
            $this->db->where('product_code',$product_code);
            $this->db->where('family_construct',$family_construct);
            $this->db->where('si_per_member',$sum_insured);
            $result=$this->db->get()->row_array();

            
        }

        $result=str_replace('GRP0','',$result['group_code']);
        if($result>9){
            $result='Group'.$result;
        }else{
            $result=str_replace('0','',$result);
            $result='Group'.$result;
        }
        return $result;

    }

    public function get_group_previous_master_policy_number($lead_id,$hb_certificate_no){

        $this->db->select('previous_master_policy_number');
        $this->db->from('telesales_renewal_group');
        $this->db->where('lead_id',$lead_id);
        $this->db->like('hb_certificate_no',$hb_certificate_no);
        $result=$this->db->get()->row_array();

        return $result['previous_master_policy_number'];
        

    }

    public function get_grp_extract_data(){

        $sgroup=$this->input->post('sgroup');
        
        $i = 1;

        $fetch_data = $this->renewal->get_grp_extract_data($sgroup);
        // print_r($this->db->last_query());
        // // echo count($fetch_data);
        // exit;

    
            foreach ($fetch_data as $row) {

                $djson=json_decode($row->res,true);

                $other_details=json_decode($row->other_details,true);

                // print_pre($djson['response']['policyData'][0]['Customer_Code']);
                // exit;

                $payment_djson=json_decode($row->payment_res,true);

                // print_r();exit;

                if (!empty($djson['response']['policyData'][0]['Sum_insured_type'])) {
                    $policy_fi_type = "Family Floater";
                } else if (!empty($djson['response']['policyData']['Sum_insured_type'])) {
                    $policy_fi_type = "Individual";
                }
        


                if ($policy_fi_type = "Family Floater") {
                    
                    $Policy_lapsed_flag =  $djson['response']['policyData'][0]['Policy_lapsed_flag'];
                    $Renewed_Flag =  $djson['response']['policyData'][0]['Renewed_Flag'];
                    $Renewable_Flag =  $djson['response']['policyData'][0]['Renewable_Flag'];
                    $CustomerName =  $djson['response']['policyData'][0]['Name_of_the_proposer'];
                    $Email =  $djson['response']['policyData'][0]['Proposer_Email'];
                    $PhoneNo =  $djson['response']['policyData'][0]['Proposer_MobileNo'];
                   // $FinalPremium =  $djson['response']['policyData'][0]['premium']['Renewal_Gross_Premium'];
                    $product_name =  $djson['response']['policyData'][0]['Name_of_product'];
                    $autodebit =  $djson['response']['policyData'][0]['Auto_Debit'];

                    $Policy_renewal_date =  $djson['response']['policyData'][0]['Policy_renewal_date'];
                    $policynumber =  $djson['response']['policyData'][0]['Certificate_number'];
                    $Name_of_the_proposer =  $djson['response']['policyData'][0]['Name_of_the_proposer'];
                    $Policy_expiry_date =  $djson['response']['policyData'][0]['Policy_expiry_date'];
                    $Policy_renewal_date =  $djson['response']['policyData'][0]['Policy_renewal_date'];
                    // $MaterPolicyNumber =  $djson['response']['policyData'][0]['MaterPolicyNumber'];
                    $customer_code=$djson['response']['policyData'];

                    foreach($customer_code as $key =>$check_certificate){
                        // print_r($check_certificate['Certificate_number']);
                        $first_=explode('-',$check_certificate['Certificate_number']);
                        
                        if($first_[0]==$sgroup){
                            $member_array = $djson['response']['policyData'][$key]['Members'];
                            $MaterPolicyNumber = $djson['response']['policyData'][$key]['MaterPolicyNumber'];
                            $policy_expiry_date = $djson['response']['policyData'][$key]['Policy_expiry_date'];
                            $premiumm = $djson['response']['policyData'][$key]['premium']['Renewal_Gross_Premium'];                                                
                            $customer_code=$djson['response']['policyData'][$key]['Customer_Code'];    
                        }

                        // print_r($first_[0]);
                        // echo"<br>";
                    }
                    // exit;

                    // print_r(substr($customer_code[0]['Certificate_number'],0,3));exit;



                    // if($sgroup=='GHI'){
                    //     $member_array = $djson['response']['policyData'][0]['Members'];
                    //     $MaterPolicyNumber = $djson['response']['policyData'][0]['MaterPolicyNumber'];
                    //     $policy_expiry_date = $djson['response']['policyData'][0]['Policy_expiry_date'];
                    //     $premiumm = $djson['response']['policyData'][0]['premium']['Renewal_Gross_Premium'];                                                
                    //     $customer_code=$djson['response']['policyData'][0]['Customer_Code'];
                    // }

                    // if($sgroup=='GP'){
                    //     $member_array = $djson['response']['policyData'][1]['Members'];
                    //     $MaterPolicyNumber = $djson['response']['policyData'][1]['MaterPolicyNumber'];
                    //     $policy_expiry_date = $djson['response']['policyData'][1]['Policy_expiry_date'];
                    //     $premiumm = $djson['response']['policyData'][1]['premium']['Renewal_Gross_Premium'];
                    //     $customer_code=$djson['response']['policyData'][1]['Customer_Code'];
                    // }


                    // if($sgroup=='GCI'){
                    //     $member_array = $djson['response']['policyData'][2]['Members'];
                    //     $MaterPolicyNumber = $djson['response']['policyData'][2]['MaterPolicyNumber'];
                    //     $policy_expiry_date = $djson['response']['policyData'][2]['Policy_expiry_date'];
                    //     $premiumm = $djson['response']['policyData'][2]['premium']['Renewal_Gross_Premium'];
                    //     $customer_code=$djson['response']['policyData'][2]['Customer_Code'];

                    // }


                    // if($sgroup=='GHCB'){
                    //     $member_array = $djson['response']['policyData'][3]['Members'];
                    //     $MaterPolicyNumber = $djson['response']['policyData'][3]['MaterPolicyNumber'];
                    //     $policy_expiry_date = $djson['response']['policyData'][3]['Policy_expiry_date'];
                    //     $premiumm = $djson['response']['policyData'][3]['premium']['Renewal_Gross_Premium'];
                    //     $customer_code=$djson['response']['policyData'][3]['Customer_Code'];

                    // }

                    $plantype_member_array = $djson['response']['policyData'][0]['Members'];

                    $suminsured =  $djson['response']['policyData'][0]['Members'][0]['MemberproductComponents'][0]['SumInsured'];                    
                    $hramount =  $djson['response']['policyData'][0]['Members'][0]['MemberproductComponents'][0]['Hr_Amount'];                    

                    $nomineedetails=$djson['response']['policyData'][0]['Nominee_Details'];

                } else if ($policy_fi_type = "Individual") {
                    $Policy_lapsed_flag =  $djson['response']['policyData']['Policy_lapsed_flag'];
                    $Renewed_Flag =  $djson['response']['policyData']['Renewed_Flag'];
                    $Renewable_Flag =  $djson['response']['policyData']['Renewable_Flag'];
                    $CustomerName =  $djson['response']['policyData']['Name_of_the_proposer'];
                    $Email =  $djson['response']['policyData']['Proposer_Email'];
                    $PhoneNo =  $djson['response']['policyData']['Proposer_MobileNo'];
                    //$FinalPremium =  $djson['response']['policyData']['premium']['Renewal_Gross_Premium'];
                    $product_name =  $djson['response']['policyData']['Name_of_product'];
                    $autodebit =  $djson['response']['policyData']['Auto_Debit'];
                    $Policy_renewal_date =  $djson['response']['policyData']['Policy_renewal_date'];
                    $policynumber =  $djson['response']['policyData']['Certificate_number'];
                    $Name_of_the_proposer =  $djson['response']['policyData']['Name_of_the_proposer'];
                    $Policy_expiry_date =  $djson['response']['policyData']['Policy_expiry_date'];
                    $Policy_renewal_date =  $djson['response']['policyData']['Policy_renewal_date'];
                    $MaterPolicyNumber =  $djson['response']['policyData']['MaterPolicyNumber'];
                    
                    $member_array = $djson['response']['policyData']['Members'];
                    $plantype_member_array = $djson['response']['policyData']['Members'];


                    $customer_code=$djson['response']['policyData'][0]['Customer_Code'];

                    $suminsured =  $djson['response']['policyData'][0]['Members'][0]['MemberproductComponents'][0]['SumInsured'];                    
                    $hramount =  $djson['response']['policyData'][0]['Members'][0]['MemberproductComponents'][0]['Hr_Amount'];                    

                    $nomineedetails=$djson['response']['policyData'][0]['Nominee_Details'];
                }
                    $proposeremail=$djson['response']['policyData'][0]['Proposer_Email'];
                    $proposerphone=$djson['response']['policyData'][0]['Proposer_MobileNo'];
                    $taxtype=$djson['response']['policyData'];
                    $ntaxtype=$taxtype[0]['premium']['Renewal_Tax_Details'][0]['Tax_Type'];

                    $family_construct_adult = 0;
                    $family_construct_kid = 0;

                // print_pre($djson['response']['policyData']);
                


                foreach($plantype_member_array as $member_key => $single_member_res){

                    if(strtolower($single_member_res['Relation'])=='self'||strtolower($single_member_res['Relation'])=='spouse'){
                        $family_construct_adult+=1;   
                    }

                    if(strtolower($single_member_res['Relation'])=='dependent son'||strtolower($single_member_res['Relation'])=='dependent daughter'){
                        $family_construct_kid+=1;   
                    }          
                    

                }
                if($family_construct_kid!=0){
                    $family_construct_kid = $family_construct_kid.'K';
                }else{
                    $family_construct_kid = "";
                }

                if($family_construct_adult!=0){
                    $family_construct_adult = $family_construct_adult.'A';
                }else{
                    $family_construct_adult = "";
                }
                
        
                $family_construct = $family_construct_adult.'+'.$family_construct_kid;


                // $longString = $other_details['Response']['Home_Address_1'];
                // $lines = explode("\n", wordwrap($longString, 40));


                $longString  = $other_details['Response']['Home_Address_1'];

                $words = explode(' ', $longString);
                
                $maxLineLength = 40;
                
                $currentLength = 0;
                $index = 0;
                
                foreach ($words as $word) {
                    // +1 because the word will receive back the space in the end that it loses in explode()
                    $wordLength = strlen($word) + 1;
                
                    if (($currentLength + $wordLength) <= $maxLineLength) {
                        $output[$index] .= $word . ' ';
                        $currentLength += $wordLength;
                    } else {
                        $index += 1;
                        $currentLength = $wordLength;
                        $output[$index] = $word;
                    }
                }


                foreach($member_array as $member_key => $single_member_res){




                    $sub_data = [];

                    $member_dob =  $member_array[$member_key]["DoB"];


                    if($member_dob != NULL || $member_dob != ""){
                        // $diff = (date('Y') - date('Y',strtotime($member_dob)));

                        // $end_date_check = new DateTime($Policy_expiry_date);
                        $end_date_check = new DateTime('now');
    
                        $end_date_check->modify('+1 day');
                        
                        $diff = DateTime::createFromFormat('m/d/Y', $member_dob)
                             ->diff($end_date_check)
                             ->y;
    
                            $cer=explode(",",$row->hb_certificate_no);
                            
                            foreach($cer as $key => $value){
                                $getcer=explode(",",$value);
                                
                                $newcer=explode("-",$value);

                                    if($newcer[0]==$sgroup){

                                        $sub_data[] = $i++;
                                        $sub_data[] = '';//$row->lead_id;
                                        $sub_data[] = $value;
                                        $sub_data[] = $member_array[$member_key]["Member_Code"];
                                        $sub_data[] = '';//$newcer;
                                        $sub_data[] = $this->get_group_previous_master_policy_number($row->lead_id_group,$sgroup);
                                        $sub_data[] = $MaterPolicyNumber;
                                        $sub_data[] = date('d/m/Y',strtotime($payment_djson['txnDateTime']));//date('d-m-Y',strtotime("+1 Day".$policy_expiry_date));
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = $member_array[$member_key]["Name"];
                                        $sub_data[] = date('d/m/Y',strtotime($member_dob));
                                        $sub_data[] = $diff;

                                        if(strtolower($member_array[$member_key]["Gender"])=='m'){
                                            $gender='Male';
                                        }else if(strtolower($member_array[$member_key]["Gender"])=='f'){
                                            $gender='Female';
                                        }

                                        $sub_data[] = $gender;
                                        $sub_data[] = $member_array[$member_key]["Relation"];
                                        $sub_data[] = '';
                                        $sub_data[] = 0;
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        // $sub_data[] = $premiumm;
                                        $sub_data[] = $suminsured;                                        
                                        $sub_data[] = $customer_code;
                                        $sub_data[] = $member_array[$member_key]["Member_Code"];
                                        $sub_data[] = 'No';
                                        $sub_data[] = $sgroup=='GPA'?'Yes':'No';
                                        $sub_data[] = $sgroup=='GPA'?$suminsured:'0';
                                        $sub_data[] = $sgroup=='GCI'?'Yes':'No';
                                        $sub_data[] = $sgroup=='GCI'?$suminsured:'0';
                                        $sub_data[] = $sgroup=='GHCB'?'Yes':'No';
                                        $sub_data[] = $sgroup=='GHCB'?$suminsured:'0';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = 'CLASS-II';
                                        $sub_data[] = $this->get_group_code($family_construct,$suminsured,'R03',$sgroup);
                                        $sub_data[] = $other_details['Response']['PolicyIssueDate']?date('d/m/Y',strtotime($other_details['Response']['PolicyIssueDate'])):'';
                                        $sub_data[] = $other_details['Response']['PolicyIssueDate']?date('d/m/Y',strtotime($other_details['Response']['PolicyIssueDate'])):'';
                                        $sub_data[] = '';
                                        $sub_data[] = $row->lead_id_group;
                                        $sub_data[] = $other_details['Response']['Declared_PreExisting_Diseases']?$other_details['Response']['Declared_PreExisting_Diseases']:'No';
                                        $sub_data[] = $other_details['Response']['Declared_PreExisting_Diseases'];
                                        $sub_data[] = 'N';
                                        $sub_data[] = 0;
                                        $sub_data[] = $other_details['Response']['Home_City'];
                                        $sub_data[] = $output[0];
                                        $sub_data[] = $output[1];//$other_details['Response']['Home_Address_2'];
                                        $sub_data[] = $output[2];//$other_details['Response']['vchAddressLine3'];
                                        $sub_data[] = $other_details['Response']['Home_City'];
                                        $sub_data[] = $other_details['Response']['Home_District'];
                                        $sub_data[] = $other_details['Response']['Home_State'];
                                        $sub_data[] = '';
                                        $sub_data[] = $other_details['Response']['Home_Pincode'];
                                        $sub_data[] = $nomineedetails['Nominee_Name'];
                                        $sub_data[] = $other_details['Response']['Relationship'];
                                        $sub_data[] = $nomineedetails['Nominee_Address']?$nomineedetails['Nominee_Address']:"NA";
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = 'Razorpay';
                                        $sub_data[] = '';
                                        $sub_data[] = $other_details['Response']['BankAccountNumber'];
                                        $sub_data[] = $other_details['Response']['IFSCCode'];
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = $member_array[$member_key]["Email"]?$member_array[$member_key]["Email"]:$proposeremail;
                                        $sub_data[] = $member_array[$member_key]["Mobile_Number"]?$member_array[$member_key]["Mobile_Number"]:$proposerphone;
                                        $sub_data[] =$payment_djson['UniqueIdentifierValue'];
                                        $sub_data[] = 'AXIS_TELE';
                                        $sub_data[] = 'consumers';
                                        $sub_data[] = '';
                                        $sub_data[] = 'GST';
                                        $sub_data[] = $row->digital_officer;
                                        $sub_data[] = $row->avnacode;
                                        $sub_data[] = $other_details['Response']['BankCode'];
                                        $sub_data[] = $row->lead_id;
                                        $sub_data[] = $payment_djson['TxRefNo'];
                                        $sub_data[] = '';
                                        $sub_data[] = 2;
                                        $sub_data[] = 'Debit/Credit Card';
                                        $sub_data[] = $payment_djson['txnDateTime']?date('d/m/Y',strtotime($payment_djson['txnDateTime'])):'';
                                        // $sub_data[] = date('d-m-Y H:i:s',strtotime($payment_djson['txnDateTime']));

                                        $check_premiumm = substr($premiumm, strpos($premiumm, ".") + 1);
                                        
                                        if($check_premiumm>0){
                                            $check_premiumm=$premiumm;                                            
                                        }else{
                                            $check_premiumm=number_format($premiumm, 0, '.', '');                                            
                                        }

                                        $sub_data[] = $check_premiumm;//number_format($premiumm, 0, '.', '');
                                        $sub_data[] = $payment_djson['TxRefNo'];
                                        $sub_data[] = 'Group';
                                        $sub_data[] = $payment_djson['TxRefNo'];
                                        $sub_data[] = $payment_djson['txnDateTime']?date('d/m/Y',strtotime($payment_djson['txnDateTime'])):'';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = $autodebit?$autodebit:'no';
                                        $sub_data[] = $hramount=='0.00'||$hramount==''?'0':$hramount;
               
                                    }


                            }   


                             $data[] = $sub_data;
                                         

                    }
    

                }   

             



            }
    
            // print_pre($data);
            // exit;
            
            if($data==null){
                $data='';
            }


    $output = array(
        "recordsTotal" => $this->renewal->get_grp_extract_data_count($sgroup),
        "recordsFiltered" => $this->renewal->get_grp_extract_data_count($sgroup),
        "data" => $data,
    );
    // print_r($this->db->last_query());exit;

            echo json_encode($output);

    }        
    public function get_grp_extract_data_old_2021_08_07(){

        $sgroup=$this->input->post('sgroup');
        
        $i = 1;

        $fetch_data = $this->renewal->get_grp_extract_data($sgroup);
        // print_r($this->db->last_query());
        // // echo count($fetch_data);
        // exit;

    
            foreach ($fetch_data as $row) {

                $djson=json_decode($row->res,true);
                $payment_djson=json_decode($row->payment_res,true);

                // print_r();exit;

                if (!empty($djson['response']['policyData'][0]['Sum_insured_type'])) {
                    $policy_fi_type = "Family Floater";
                } else if (!empty($djson['response']['policyData']['Sum_insured_type'])) {
                    $policy_fi_type = "Individual";
                }
        


                if ($policy_fi_type = "Family Floater") {
                    
                    $Policy_lapsed_flag =  $djson['response']['policyData'][0]['Policy_lapsed_flag'];
                    $Renewed_Flag =  $djson['response']['policyData'][0]['Renewed_Flag'];
                    $Renewable_Flag =  $djson['response']['policyData'][0]['Renewable_Flag'];
                    $CustomerName =  $djson['response']['policyData'][0]['Name_of_the_proposer'];
                    $Email =  $djson['response']['policyData'][0]['Proposer_Email'];
                    $PhoneNo =  $djson['response']['policyData'][0]['Proposer_MobileNo'];
                   // $FinalPremium =  $djson['response']['policyData'][0]['premium']['Renewal_Gross_Premium'];
                    $product_name =  $djson['response']['policyData'][0]['Name_of_product'];
                    $Policy_renewal_date =  $djson['response']['policyData'][0]['Policy_renewal_date'];
                    $policynumber =  $djson['response']['policyData'][0]['Certificate_number'];
                    $Name_of_the_proposer =  $djson['response']['policyData'][0]['Name_of_the_proposer'];
                    $Policy_expiry_date =  $djson['response']['policyData'][0]['Policy_expiry_date'];
                    $Policy_renewal_date =  $djson['response']['policyData'][0]['Policy_renewal_date'];
                    // $MaterPolicyNumber =  $djson['response']['policyData'][0]['MaterPolicyNumber'];
                    if($sgroup=='GHI'){
                        $member_array = $djson['response']['policyData'][0]['Members'];
                        $MaterPolicyNumber = $djson['response']['policyData'][0]['MaterPolicyNumber'];
                        $policy_expiry_date = $djson['response']['policyData'][0]['Policy_expiry_date'];
                        $premiumm = $djson['response']['policyData'][0]['premium']['Renewal_Gross_Premium'];
                                                
                    }

                    if($sgroup=='GP'){
                        $member_array = $djson['response']['policyData'][1]['Members'];
                        $MaterPolicyNumber = $djson['response']['policyData'][1]['MaterPolicyNumber'];
                        $policy_expiry_date = $djson['response']['policyData'][1]['Policy_expiry_date'];
                        $premiumm = $djson['response']['policyData'][1]['premium']['Renewal_Gross_Premium'];
                    }


                    if($sgroup=='GCI'){
                        $member_array = $djson['response']['policyData'][2]['Members'];
                        $MaterPolicyNumber = $djson['response']['policyData'][2]['MaterPolicyNumber'];
                        $policy_expiry_date = $djson['response']['policyData'][2]['Policy_expiry_date'];
                        $premiumm = $djson['response']['policyData'][2]['premium']['Renewal_Gross_Premium'];

                    }


                    if($sgroup=='GHCB'){
                        $member_array = $djson['response']['policyData'][3]['Members'];
                        $MaterPolicyNumber = $djson['response']['policyData'][3]['MaterPolicyNumber'];
                        $policy_expiry_date = $djson['response']['policyData'][3]['Policy_expiry_date'];
                        $premiumm = $djson['response']['policyData'][3]['premium']['Renewal_Gross_Premium'];

                    }

                    $suminsured =  $djson['response']['policyData'][0]['Members'][0]['MemberproductComponents'][0]['SumInsured'];                    
                    $nomineedetails=$djson['response']['policyData'][0]['Nominee_Details'];

                } else if ($policy_fi_type = "Individual") {
                    $Policy_lapsed_flag =  $djson['response']['policyData']['Policy_lapsed_flag'];
                    $Renewed_Flag =  $djson['response']['policyData']['Renewed_Flag'];
                    $Renewable_Flag =  $djson['response']['policyData']['Renewable_Flag'];
                    $CustomerName =  $djson['response']['policyData']['Name_of_the_proposer'];
                    $Email =  $djson['response']['policyData']['Proposer_Email'];
                    $PhoneNo =  $djson['response']['policyData']['Proposer_MobileNo'];
                    //$FinalPremium =  $djson['response']['policyData']['premium']['Renewal_Gross_Premium'];
                    $product_name =  $djson['response']['policyData']['Name_of_product'];
                    $Policy_renewal_date =  $djson['response']['policyData']['Policy_renewal_date'];
                    $policynumber =  $djson['response']['policyData']['Certificate_number'];
                    $Name_of_the_proposer =  $djson['response']['policyData']['Name_of_the_proposer'];
                    $Policy_expiry_date =  $djson['response']['policyData']['Policy_expiry_date'];
                    $Policy_renewal_date =  $djson['response']['policyData']['Policy_renewal_date'];
                    $MaterPolicyNumber =  $djson['response']['policyData']['MaterPolicyNumber'];
                    
                    $member_array = $djson['response']['policyData']['Members'];

                    $suminsured =  $djson['response']['policyData'][0]['Members'][0]['MemberproductComponents'][0]['SumInsured'];                    
                    $nomineedetails=$djson['response']['policyData'][0]['Nominee_Details'];
                }
                    $proposeremail=$djson['response']['policyData'][0]['Proposer_Email'];
                    $proposerphone=$djson['response']['policyData'][0]['Proposer_MobileNo'];
                    $taxtype=$djson['response']['policyData'];
                    $ntaxtype=$taxtype[0]['premium']['Renewal_Tax_Details'][0]['Tax_Type'];

                foreach($member_array as $member_key => $single_member_res){

                    $sub_data = [];

                    $member_dob =  $member_array[$member_key]["DoB"];


                    if($member_dob != NULL || $member_dob != ""){
                        // $diff = (date('Y') - date('Y',strtotime($member_dob)));

                        // $end_date_check = new DateTime($Policy_expiry_date);
                        $end_date_check = new DateTime('now');
    
                        $end_date_check->modify('+1 day');
                        
                        $diff = DateTime::createFromFormat('m/d/Y', $member_dob)
                             ->diff($end_date_check)
                             ->y;
    
                            $cer=explode(",",$row->hb_certificate_no);
                            
                            foreach($cer as $key => $value){
                                $getcer=explode(",",$value);
                                
                                $newcer=explode("-",$value);

                                    if($newcer[0]==$sgroup){

                                        $sub_data[] = $i++;
                                        $sub_data[] = $value;//$row->lead_id;
                                        $sub_data[] = $value;
                                        $sub_data[] = $member_array[$member_key]["Member_Code"];
                                        $sub_data[] = '';//$newcer;
                                        $sub_data[] = '';
                                        $sub_data[] = $MaterPolicyNumber;
                                        $sub_data[] = date('d-m-Y',strtotime("+1 Day".$policy_expiry_date));
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = $member_array[$member_key]["Name"];
                                        $sub_data[] = date('d-m-Y',strtotime($member_dob));
                                        $sub_data[] = $diff;
                                        $sub_data[] = $member_array[$member_key]["Gender"];
                                        $sub_data[] = $member_array[$member_key]["Relation"];
                                        $sub_data[] = '';
                                        $sub_data[] = 0;
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = $suminsured;
                                        $sub_data[] = '';
                                        $sub_data[] = $member_array[$member_key]["Member_Code"];
                                        $sub_data[] = 'No';
                                        $sub_data[] = $sgroup=='GPA'?'Yes':'No';
                                        $sub_data[] = $sgroup=='GPA'?$suminsured:'0';
                                        $sub_data[] = $sgroup=='GCI'?'Yes':'No';
                                        $sub_data[] = $sgroup=='GCI'?$suminsured:'0';
                                        $sub_data[] = $sgroup=='GHCB'?'Yes':'No';
                                        $sub_data[] = $sgroup=='GHCB'?$suminsured:'0';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = 'N';
                                        $sub_data[] = 0;
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = $nomineedetails['Nominee_Name'];
                                        $sub_data[] = '';
                                        $sub_data[] = $nomineedetails['Nominee_Address']?$nomineedetails['Nominee_Address']:"NA";
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = 'Axis Bank';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = $member_array[$member_key]["Email"]?$member_array[$member_key]["Email"]:$proposeremail;
                                        $sub_data[] = $member_array[$member_key]["Mobile_Number"]?$member_array[$member_key]["Mobile_Number"]:$proposerphone;
                                        $sub_data[] ='';
                                        $sub_data[] = 'Axis Telesales';
                                        $sub_data[] = 'consumers';
                                        $sub_data[] = '';
                                        $sub_data[] = $ntaxtype;
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = $payment_djson['TxRefNo'];
                                        $sub_data[] = '';
                                        $sub_data[] = 1;
                                        $sub_data[] = $payment_djson['paymentMode'];
                                        $sub_data[] = date('d-m-Y',strtotime($payment_djson['txnDateTime']));
                                        $sub_data[] = $premiumm;
                                        $sub_data[] = '';
                                        $sub_data[] = 'Group';
                                        $sub_data[] = $payment_djson['TxRefNo'];
                                        $sub_data[] = date('d-m-Y',strtotime($payment_djson['txnDateTime']));
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
                                        $sub_data[] = '';
           
    
                                    }


                            }   


                             $data[] = $sub_data;
                                         

                    }
    

                }   




            }
    
            // print_pre($data);
            // exit;
            
            if($data==null){
                $data='';
            }


    $output = array(
        "recordsTotal" => $this->renewal->get_grp_extract_data_count($sgroup),
        "recordsFiltered" => $this->renewal->get_grp_extract_data_count($sgroup),
        "data" => $data,
    );
    // print_r($this->db->last_query());exit;

            echo json_encode($output);

    }    
    
    public function get_grp_extract_data_old(){

        $sgroup=$this->input->post('sgroup');

        // print_r($sgroup);
        // exit;
        $i = 1;

        $fetch_data = $this->renewal->get_grp_extract_data();
        // print_r($this->db->last_query());
        // echo count($fetch_data);
        // exit;

    
            foreach ($fetch_data as $row) {

                $djson=json_decode($row->res,true);
                $payment_djson=json_decode($row->payment_res,true);

                // print_r();exit;

                if (!empty($djson['response']['policyData'][0]['Sum_insured_type'])) {
                    $policy_fi_type = "Family Floater";
                } else if (!empty($djson['response']['policyData']['Sum_insured_type'])) {
                    $policy_fi_type = "Individual";
                }
        


                if ($policy_fi_type = "Family Floater") {
                    
                    $Policy_lapsed_flag =  $djson['response']['policyData'][0]['Policy_lapsed_flag'];
                    $Renewed_Flag =  $djson['response']['policyData'][0]['Renewed_Flag'];
                    $Renewable_Flag =  $djson['response']['policyData'][0]['Renewable_Flag'];
                    $CustomerName =  $djson['response']['policyData'][0]['Name_of_the_proposer'];
                    $Email =  $djson['response']['policyData'][0]['Proposer_Email'];
                    $PhoneNo =  $djson['response']['policyData'][0]['Proposer_MobileNo'];
                   // $FinalPremium =  $djson['response']['policyData'][0]['premium']['Renewal_Gross_Premium'];
                    $product_name =  $djson['response']['policyData'][0]['Name_of_product'];
                    $Policy_renewal_date =  $djson['response']['policyData'][0]['Policy_renewal_date'];
                    $policynumber =  $djson['response']['policyData'][0]['Certificate_number'];
                    $Name_of_the_proposer =  $djson['response']['policyData'][0]['Name_of_the_proposer'];
                    $Policy_expiry_date =  $djson['response']['policyData'][0]['Policy_expiry_date'];
                    $Policy_renewal_date =  $djson['response']['policyData'][0]['Policy_renewal_date'];
                    $MaterPolicyNumber =  $djson['response']['policyData'][0]['MaterPolicyNumber'];
                    $member_array = $djson['response']['policyData'][0]['Members'];
                    $suminsured =  $djson['response']['policyData'][0]['Members'][0]['MemberproductComponents'][0]['SumInsured'];                    
                    $nomineedetails=$djson['response']['policyData'][0]['Nominee_Details'];

                } else if ($policy_fi_type = "Individual") {
                    $Policy_lapsed_flag =  $djson['response']['policyData']['Policy_lapsed_flag'];
                    $Renewed_Flag =  $djson['response']['policyData']['Renewed_Flag'];
                    $Renewable_Flag =  $djson['response']['policyData']['Renewable_Flag'];
                    $CustomerName =  $djson['response']['policyData']['Name_of_the_proposer'];
                    $Email =  $djson['response']['policyData']['Proposer_Email'];
                    $PhoneNo =  $djson['response']['policyData']['Proposer_MobileNo'];
                    //$FinalPremium =  $djson['response']['policyData']['premium']['Renewal_Gross_Premium'];
                    $product_name =  $djson['response']['policyData']['Name_of_product'];
                    $Policy_renewal_date =  $djson['response']['policyData']['Policy_renewal_date'];
                    $policynumber =  $djson['response']['policyData']['Certificate_number'];
                    $Name_of_the_proposer =  $djson['response']['policyData']['Name_of_the_proposer'];
                    $Policy_expiry_date =  $djson['response']['policyData']['Policy_expiry_date'];
                    $Policy_renewal_date =  $djson['response']['policyData']['Policy_renewal_date'];
                    $MaterPolicyNumber =  $djson['response']['policyData']['MaterPolicyNumber'];
                    $member_array = $djson['response']['policyData']['Members'];
                    $suminsured =  $djson['response']['policyData'][0]['Members'][0]['MemberproductComponents'][0]['SumInsured'];                    
                    $nomineedetails=$djson['response']['policyData'][0]['Nominee_Details'];
                }
                    $proposeremail=$djson['response']['policyData'][0]['Proposer_Email'];
                    $proposerphone=$djson['response']['policyData'][0]['Proposer_MobileNo'];
                    $taxtype=$djson['response']['policyData'];
                    $ntaxtype=$taxtype[0]['premium']['Renewal_Tax_Details'][0]['Tax_Type'];

                    // print_r($taxtype);
                    // exit;

                foreach($member_array as $member_key => $single_member_res){

                    $sub_data = [];

                    $member_dob =  $member_array[$member_key]["DoB"];


                    if($member_dob != NULL || $member_dob != ""){
                        // $diff = (date('Y') - date('Y',strtotime($member_dob)));

                        // $end_date_check = new DateTime($Policy_expiry_date);
                        $end_date_check = new DateTime('now');
    
                        $end_date_check->modify('+1 day');
                        
                        $diff = DateTime::createFromFormat('m/d/Y', $member_dob)
                             ->diff($end_date_check)
                             ->y;
    
                            $cer=explode(",",$row->hb_certificate_no);
                            $newcer=explode(",",$row->new_coi_number);

 
                            if($sgroup==1){
                                $cer=$cer[1];
                                $newcer=$newcer[1];

                                $ogetghi=explode("-",$cer);
                                $tgetghi=explode("-",$newcer);


                                if(strtolower($ogetghi[0])=='gp'){
                                    
                                    $sub_data[] = $i++;
                                    $sub_data[] = $cer;//$row->lead_id;
                                    $sub_data[] = $cer;
                                    $sub_data[] = $member_array[$member_key]["Member_Code"];
                                    $sub_data[] = '';//$newcer;
                                    $sub_data[] = $MaterPolicyNumber;
                                    $sub_data[] = '';
                                    $sub_data[] = date('d-m-Y',strtotime($payment_djson['txnDateTime']));
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $member_array[$member_key]["Name"];
                                    $sub_data[] = date('d-m-Y',strtotime($member_dob));
                                    $sub_data[] = $diff;
                                    $sub_data[] = $member_array[$member_key]["Gender"];
                                    $sub_data[] = $member_array[$member_key]["Relation"];
                                    $sub_data[] = '';
                                    $sub_data[] = 0;
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $suminsured;
                                    $sub_data[] = '';
                                    $sub_data[] = $member_array[$member_key]["Member_Code"];
                                    $sub_data[] = 'No';
                                    $sub_data[] = 'Yes';
                                    $sub_data[] = $suminsured;
                                    $sub_data[] = 'No';
                                    $sub_data[] = 0;
                                    $sub_data[] = 'No';
                                    $sub_data[] = 0;
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = 'N';
                                    $sub_data[] = 0;
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $nomineedetails['Nominee_Name'];
                                    $sub_data[] = '';
                                    $sub_data[] = $nomineedetails['Nominee_Address']?$nomineedetails['Nominee_Address']:"NA";
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = 'Axis Bank';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $proposeremail;
                                    $sub_data[] = $proposerphone;
                                    $sub_data[] ='';
                                    $sub_data[] = 'Axis Telesales';
                                    $sub_data[] = 'consumers';
                                    $sub_data[] = '';
                                    $sub_data[] = $ntaxtype;
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $payment_djson['TxRefNo'];
                                    $sub_data[] = '';
                                    $sub_data[] = 1;
                                    $sub_data[] = $payment_djson['paymentMode'];
                                    $sub_data[] = date('d-m-Y',strtotime($payment_djson['txnDateTime']));
                                    $sub_data[] = $payment_djson['amount'];
                                    $sub_data[] = '';
                                    $sub_data[] = 'Group';
                                    $sub_data[] = $payment_djson['TxRefNo'];
                                    $sub_data[] = date('d-m-Y',strtotime($payment_djson['txnDateTime']));
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
       
                                    
                                }


                            }else if($sgroup==2){
                                $cer=$cer[2];
                                $newcer=$newcer[2];

                                $ogetghi=explode("-",$cer);
                                $tgetghi=explode("-",$newcer);
                                // print_r($ogetghi);
                                // print_r($newcer);

                                if(strtolower($ogetghi[0])=='gci'){
                                    
                                    $sub_data[] = $i++;
                                    $sub_data[] = $cer;//$row->lead_id;
                                    $sub_data[] = $cer;
                                    $sub_data[] = $member_array[$member_key]["Member_Code"];
                                    $sub_data[] = '';//$newcer;
                                    $sub_data[] = $MaterPolicyNumber;
                                    $sub_data[] = '';
                                    $sub_data[] = date('d-m-Y',strtotime($payment_djson['txnDateTime']));
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $member_array[$member_key]["Name"];
                                    $sub_data[] = date('d-m-Y',strtotime($member_dob));
                                    $sub_data[] = $diff;
                                    $sub_data[] = $member_array[$member_key]["Gender"];
                                    $sub_data[] = $member_array[$member_key]["Relation"];
                                    $sub_data[] = '';
                                    $sub_data[] = 0;
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $suminsured;
                                    $sub_data[] = '';
                                    $sub_data[] = $member_array[$member_key]["Member_Code"];
                                    $sub_data[] = 'No';
                                    $sub_data[] = 'No';
                                    $sub_data[] = 0;
                                    $sub_data[] = 'Yes';
                                    $sub_data[] = $suminsured;
                                    $sub_data[] = 'No';
                                    $sub_data[] = 0;
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = 'N';
                                    $sub_data[] = 0;
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $nomineedetails['Nominee_Name'];
                                    $sub_data[] = '';
                                    $sub_data[] = $nomineedetails['Nominee_Address']?$nomineedetails['Nominee_Address']:"NA";
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = 'Axis Bank';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $proposeremail;
                                    $sub_data[] = $proposerphone;
                                    $sub_data[] ='';
                                    $sub_data[] = 'Axis Telesales';
                                    $sub_data[] = 'consumers';
                                    $sub_data[] = '';
                                    $sub_data[] = $ntaxtype;
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $payment_djson['TxRefNo'];
                                    $sub_data[] = '';
                                    $sub_data[] = 1;
                                    $sub_data[] = $payment_djson['paymentMode'];
                                    $sub_data[] = date('d-m-Y',strtotime($payment_djson['txnDateTime']));
                                    $sub_data[] = $payment_djson['amount'];
                                    $sub_data[] = '';
                                    $sub_data[] = 'Group';
                                    $sub_data[] = $payment_djson['TxRefNo'];
                                    $sub_data[] = date('d-m-Y',strtotime($payment_djson['txnDateTime']));
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
       
                                    
                                }


                            }else if($sgroup==3){
                                $cer=$cer[3];
                                $newcer=$newcer[3];

                                $ogetghi=explode("-",$cer);
                                $tgetghi=explode("-",$newcer);


                                if(strtolower($ogetghi[0])=='ghcb'){
                                    
                                    $sub_data[] = $i++;
                                    $sub_data[] = $cer;//$row->lead_id;
                                    $sub_data[] = $cer;
                                    $sub_data[] = $member_array[$member_key]["Member_Code"];
                                    $sub_data[] = '';//$newcer;
                                    $sub_data[] = '61-19-00057-00-00';
                                    $sub_data[] = $MaterPolicyNumber;
                                    $sub_data[] = date('d-m-Y',strtotime($payment_djson['txnDateTime']));
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $member_array[$member_key]["Name"];
                                    $sub_data[] = date('d-m-Y',strtotime($member_dob));
                                    $sub_data[] = $diff;
                                    $sub_data[] = $member_array[$member_key]["Gender"];
                                    $sub_data[] = $member_array[$member_key]["Relation"];
                                    $sub_data[] = '';
                                    $sub_data[] = 0;
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $suminsured;
                                    $sub_data[] = '';
                                    $sub_data[] = $member_array[$member_key]["Member_Code"];
                                    $sub_data[] = 'No';
                                    $sub_data[] = 'No';
                                    $sub_data[] = 0;
                                    $sub_data[] = 'No';
                                    $sub_data[] = 0;
                                    $sub_data[] = 'Yes';
                                    $sub_data[] = $suminsured;
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = 'N';
                                    $sub_data[] = 0;
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $nomineedetails['Nominee_Name'];
                                    $sub_data[] = '';
                                    $sub_data[] = $nomineedetails['Nominee_Address']?$nomineedetails['Nominee_Address']:"NA";
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = 'Axis Bank';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $proposeremail;
                                    $sub_data[] = $proposerphone;
                                    $sub_data[] ='';
                                    $sub_data[] = 'Axis Telesales';
                                    $sub_data[] = 'consumers';
                                    $sub_data[] = '';
                                    $sub_data[] = $ntaxtype;
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $payment_djson['TxRefNo'];
                                    $sub_data[] = '';
                                    $sub_data[] = 1;
                                    $sub_data[] = $payment_djson['paymentMode'];
                                    $sub_data[] = date('d-m-Y',strtotime($payment_djson['txnDateTime']));
                                    $sub_data[] = $payment_djson['amount'];
                                    $sub_data[] = '';
                                    $sub_data[] = 'Group';
                                    $sub_data[] = $payment_djson['TxRefNo'];
                                    $sub_data[] = date('d-m-Y',strtotime($payment_djson['txnDateTime']));
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
       
                                    
                                }


                            }else{
                                $cer=$cer[0];
                                $newcer=$newcer[0];

                                $ogetghi=explode("-",$cer);
                                $tgetghi=explode("-",$newcer);

                                if(strtolower($ogetghi[0])=='ghi'){

                                    $sub_data[] = $i++;
                                    $sub_data[] = $cer;//$row->lead_id;
                                    $sub_data[] = $cer;
                                    $sub_data[] = $member_array[$member_key]["Member_Code"];
                                    $sub_data[] = '';//$newcer;
                                    $sub_data[] = $MaterPolicyNumber;
                                    $sub_data[] = '';
                                    $sub_data[] = date('d-m-Y',strtotime($payment_djson['txnDateTime']));
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $member_array[$member_key]["Name"];
                                    $sub_data[] = $member_dob;
                                    $sub_data[] = $diff;
                                    $sub_data[] = $member_array[$member_key]["Gender"];
                                    $sub_data[] = $member_array[$member_key]["Relation"];
                                    $sub_data[] = '';
                                    $sub_data[] = 0;
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $suminsured;
                                    $sub_data[] = '';
                                    $sub_data[] = $member_array[$member_key]["Member_Code"];
                                    $sub_data[] = 'No';
                                    $sub_data[] = 'No';
                                    $sub_data[] = 0;
                                    $sub_data[] = 'No';
                                    $sub_data[] = 0;
                                    $sub_data[] = 'No';
                                    $sub_data[] = 0;
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = 'N';
                                    $sub_data[] = 0;
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $nomineedetails['Nominee_Name'];
                                    $sub_data[] = '';
                                    $sub_data[] = $nomineedetails['Nominee_Address']?$nomineedetails['Nominee_Address']:"NA";
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = 'Axis Bank';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $proposeremail;
                                    $sub_data[] = $proposerphone;
                                    $sub_data[] ='';
                                    $sub_data[] = 'Axis Telesales';
                                    $sub_data[] = 'consumers';
                                    $sub_data[] = '';
                                    $sub_data[] = $ntaxtype;
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = $payment_djson['TxRefNo'];
                                    $sub_data[] = '';
                                    $sub_data[] = 1;
                                    $sub_data[] = $payment_djson['paymentMode'];
                                    $sub_data[] = date('d-m-Y',strtotime($payment_djson['txnDateTime']));
                                    $sub_data[] = $payment_djson['amount'];
                                    $sub_data[] = '';
                                    $sub_data[] = 'Group';
                                    $sub_data[] = $payment_djson['TxRefNo'];
                                    $sub_data[] = date('d-m-Y',strtotime($payment_djson['txnDateTime']));
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
                                    $sub_data[] = '';
       

                                }


                                
                            }

                             $data[] = $sub_data;
                                         

                    }
    

                }   




            }
    
            // print_pre($data);
            // exit;
            
            if($data==null){
                $data='';
            }


    $output = array(
        "recordsTotal" => $this->renewal->get_grp_extract_data_count(),
        "recordsFiltered" => $this->renewal->get_grp_extract_data_count(),
        "data" => $data,
    );

            echo json_encode($output);
    }


    public function get_telesales_data()
    {

        $fetch_data = $this->renewal->all_retail_data();
        //echo $this->db->last_query();exit;
        //print_pre($fetch_data);exit;
        $i = 1;

        foreach ($fetch_data as $row) {

            $renewal_res=json_decode($row->renewal_res,true);

            $renewal_res=json_decode($renewal_res,true);
            //print_r($renewal_res);
            $response=$row->res;
            $dresponse=json_decode($response,true);
            $net_premium = 0;
            if(strtolower($row->product_type)=='group'){

                if (!empty($dresponse['response']['policyData'][0]['Sum_insured_type'])) {
                    $policy_fi_type = "Family Floater";
                } else if (!empty($dresponse['response']['policyData']['Sum_insured_type'])) {
                    $policy_fi_type = "Individual";
                }
                // echo $policy_fi_type;
                // print_pre($dresponse['response']['policyData'][0]['premium']['Renewal_Gross_Premium']);


                if ($policy_fi_type = "Family Floater") {
                    $final='';
                    foreach($dresponse['response']['policyData'] as $key =>$value){
                        //             $final .=$dresponse['response']['policyData'][$key]['premium']['Renewal_Gross_Premium'].',';

                        $final +=$dresponse['response']['policyData'][$key]['premium']['Renewal_Gross_Premium'];
                        $net_premium +=$dresponse['response']['policyData'][$key]['premium']['Renewal_Net_Premium'];

                    }


                } else if ($policy_fi_type = "Individual") {
                    $final='';
//                    $final=$dresponse['response']['policyData']['premium']['Renewal_Gross_Premium'].',';

                    $final=$dresponse['response']['policyData']['premium']['Renewal_Gross_Premium'];
                    $net_premium=$dresponse['response']['policyData']['premium']['Renewal_Net_Premium'];


                }

                $final=rtrim($final,',');

            }

            if(strtolower($row->product_type)=='retail'){

//                $final=$dresponse['response']['policyData']['premium']['Renewal_Gross_Premium'];
                $final=$dresponse['response']['policyData']['premium']['Renewal_Gross_Premium'];
                $net_premium=$dresponse['response']['policyData']['premium']['Renewal_Net_Premium'];

            }



            $sub_data = [];
            $sub_data[] = $i++;
            $sub_data[] = $row->lead_id;

            $sub_data[] = $row->avnacode;
            $sub_data[] = $row->digital_officer;
            $sub_data[] = $row->location;
            
            $sub_data[] = ucfirst($row->product_type);
            $sub_data[] = $row->policy_number;
            $disabled = '';


            //status 0 is lapsed, 1 is active, 4 is Renewed from other mode
            if ($row->status == "4") {
                $sub_data[] = "Policy is not renewed using Axis portal link";
                $disabled = 'disabled';
                $issuance_date_time = (!empty($renewal_res['ResponseData']['PaymentReceviedDate'])) ? $renewal_res['ResponseData']['PaymentReceviedDate'
                ] : "Policy is not renewed using Axis portal link";
                $Policy_issuance_date = (!empty($renewal_res['ResponseData']['PolicyIssuedDate'])) ? $renewal_res['ResponseData']['PolicyIssuedDate'] :
                    "Policy is not renewed using Axis portal link";
                $ProductName_det =  (!empty($renewal_res['ResponseData']['ProductName'])) ? $renewal_res['ResponseData']['ProductName'] : "Policy is not
 renewed using Axis portal link";
                $PolicyPaymentStatus = (!empty($renewal_res['ResponseData']['PolicyPaymentStatus'])) ? $renewal_res['ResponseData']['PolicyPaymentStatus
'] : "Policy is not renewed using Axis portal link";
                $TransactionID_det = (!empty($renewal_res['ResponseData']['TransactionID'])) ? $renewal_res['ResponseData']['TransactionID'] : "Policy i
s not renewed using Axis portal link";
                $PolicyStatus = (!empty($renewal_res['ResponseData']['PolicyStatus'])) ? $renewal_res['ResponseData']['PolicyStatus'] : "Policy is not r
enewed using Axis portal link";

            } else if (!empty($row->renewed_policy_number)) {
                $sub_data[] = "Policy Renewed";
                $issuance_date_time = $renewal_res['ResponseData']['PaymentReceviedDate'];
                $Policy_issuance_date = $renewal_res['ResponseData']['PolicyIssuedDate'];
                $ProductName_det =   $renewal_res['ResponseData']['ProductName'];
                $PolicyPaymentStatus =  $renewal_res['ResponseData']['PolicyPaymentStatus'];
                $TransactionID_det = $renewal_res['ResponseData']['TransactionID'];
                $PolicyStatus = $renewal_res['ResponseData']['PolicyStatus'];

            } else {
                if ($row->renewal_response == 'link sent') {
                    $sub_data[] = "Link Triggered";
                    $issuance_date_time = (!empty($renewal_res['ResponseData']['PaymentReceviedDate'])) ? $renewal_res['ResponseData']['PaymentRecev
iedDate'] : "Payment Not Done";
                    $Policy_issuance_date = (!empty($renewal_res['ResponseData']['PolicyIssuedDate'])) ? $renewal_res['ResponseData']['PolicyIssuedD
ate'] : "Payment Not Done";
                    $ProductName_det =  (!empty($renewal_res['ResponseData']['ProductName'])) ? $renewal_res['ResponseData']['ProductName'] : "Payme
nt Not Done";
                    $PolicyPaymentStatus = (!empty($renewal_res['ResponseData']['PolicyPaymentStatus'])) ? $renewal_res['ResponseData']['PolicyPayme
ntStatus'] : "Payment Not Done";
                    $TransactionID_det = (!empty($renewal_res['ResponseData']['TransactionID'])) ? $renewal_res['ResponseData']['TransactionID'] : "
Payment Not Done";
                    $PolicyStatus = (!empty($renewal_res['ResponseData']['TransactionID'])) ? $renewal_res['ResponseData']['PolicyStatus'] : "Paymen
t Not Done";

                } else {
                    $sub_data[] = "Link Trigger Pending";
                    $issuance_date_time = (!empty($renewal_res['ResponseData']['PaymentReceviedDate'])) ? $renewal_res['ResponseData']['PaymentRecev
iedDate'] : "Payment Not Done";
                    $Policy_issuance_date = (!empty($renewal_res['ResponseData']['PolicyIssuedDate'])) ? $renewal_res['ResponseData']['PolicyIssuedD
ate'] : "Payment Not Done";
                    $ProductName_det =  (!empty($renewal_res['ResponseData']['ProductName'])) ? $renewal_res['ResponseData']['ProductName'] : "Payme
nt Not Done";
                    $PolicyPaymentStatus = (!empty($renewal_res['ResponseData']['PolicyPaymentStatus'])) ? $renewal_res['ResponseData']['PolicyPayme
ntStatus'] : "Payment Not Done";
                    $TransactionID_det = (!empty($renewal_res['ResponseData']['TransactionID'])) ? $renewal_res['ResponseData']['TransactionID'] : "
Payment Not Done";
                    $PolicyStatus = (!empty($renewal_res['ResponseData']['TransactionID'])) ? $renewal_res['ResponseData']['PolicyStatus'] : "Paymen
t Not Done";

                }
            }

            $sub_data[] = $final;
            $sub_data[] = $net_premium;

            //$sub_data[] = $this->checkpolicy_status($row->lead_id,$row->product_type);
//              $sub_data[] =$renewal_res['ResponseData']['PaymentReceviedDate'];
            $sub_data[] = $issuance_date_time;


            if ($row->renewal_updated == '') {

                $sub_data[] = date('Y-m-d H:i:s', strtotime($row->last_updated_on));
            } else {
                $sub_data[] = date('Y-m-d H:i:s', strtotime($row->renewal_updated));
            }

            $digital_officer = $row->digital_officer;

            if (!empty($row->renewed_policy_number)) {
                $disabled = 'disabled';
            }

            $check_group_lead_payment_status=$this->renewal->check_group_lead_payment_status($row->lead_id);

            if (!empty($check_group_lead_payment_status)) {
                $disabled = 'disabled';
            }


            $sub_data[] = "
            <div class='text-center'>
            <button type='button' class='btn btn-cta btn-xs tele_re_dt_tl' policy-number='$row->policy_number' data-av='$row->avnacode' dob='$row->dob' mobile-number='$
row->mobile_no' product-type='$row->product_type' id='trigger' data-do='$digital_officer' lead-id='$row->lead_id' data-loc='$row->location'  $disabled>Trigger Link <i c
lass='ti-link'></i></button>
            <br>
            <button lead_id='$row->policy_number' class='btn btn-cta btn-xs tele_re_dt_audit' id='audit'>Audit <i class='ti-pencil-alt'></i></button>
            </div>
            ";

            if (!empty($row->renewed_policy_number)  && $row->status == "1" ) {
                $sub_data[] = $row->renewed_policy_number;
            }elseif(!empty($row->renewed_policy_number)  && $row->status == "4"){

                $sub_data[] = $row->renewed_policy_number;
            }else if ($row->status == "4") {
                $sub_data[] = "Policy is not renewed using Axis portal link";
            } else {
                $sub_data[] = "Policy Renewal Pending";
            }
            //$sub_data[] =$this->lastest_link_trigered($row->lead_id);
            /* $sub_data[] = $row->created_date;
            // $sub_data[] =$renewal_res['ResponseData']['GrossPermium'];
            $sub_data[] =$renewal_res['ResponseData']['PaymentReceviedDate'];
            $sub_data[] =$renewal_res['ResponseData']['PolicyIssuedDate'];
            $sub_data[] =$renewal_res['ResponseData']['ProductName'];
            $sub_data[] =$renewal_res['ResponseData']['PolicyStatus'];
            $sub_data[] =$renewal_res['ResponseData']['PolicyPaymentStatus'];
            //$sub_data[] =$this->getpayment_details_grp($row->lead_id,$row->product_type);
           $sub_data[] = $renewal_res['ResponseData']['TransactionID'];*/
            $sub_data[] = $row->created_date;//$this->lastest_link_trigered($row->lead_id);
            // $sub_data[] =$renewal_res['ResponseData']['GrossPermium'];
            $sub_data[] = $issuance_date_time;
            $sub_data[] =$Policy_issuance_date;
            $sub_data[] =$ProductName_det;
            $sub_data[] =$PolicyStatus;
            $sub_data[] =$PolicyPaymentStatus;
            // $sub_data[] =$this->getpayment_details_grp($row->lead_id,$row->product_type);
            $sub_data[] = $TransactionID_det;
            $data[] = $sub_data;
        }

//        print_pre($data);
        //     exit;

        if (empty($data)) {
            $data = "";
        }

        $output = array(
            "draw"            => intval($_POST["draw"]),
            "recordsTotal" => $this->renewal->total_retail_data(),
            "recordsFiltered" => $this->renewal->total_retail_data(),
            "data" => $data,
        );

        echo json_encode($output);
    }





}
