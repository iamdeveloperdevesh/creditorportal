<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once(APPPATH . "controllers/MY_TelesalesSessionCheck.php");

class Group_renewal_do extends MY_TelesalesSessionCheck
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('telesales_session')) {
            redirect('login');
        }

        if ($_SESSION['telesales_session']['is_redirect_allow'] != "1")
        {
            redirect('login');
        }

//         ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
      
        $this->load->model('Telesales/Group_renewal_do_m', "renewal_do_m", true);
        if (!$this->input->is_ajax_request()) {
                moduleCheck();
        }

    }


    public function index()
    {
        $session_data = $this->session->userdata('telesales_session');
       //  print_r($_SESSION['telesales_session']['agent_id']);exit;
       // echo encrypt_decrypt_password($_SESSION['agent_id'],"D");exit;
        if($_SESSION['telesales_session']['outbound'] == '2'){
            $do_id = $session_data['agent_id'];
            $do_id = encrypt_decrypt_password($do_id, "D");
    
            $data = $this->db->query("select * FROM tls_master_do where id = '$do_id'")->row_array();
        }

        if($_SESSION['telesales_session']['outbound'] == '1'){
            $agent_id = $session_data['agent_id'];
            $agent_id = encrypt_decrypt_password($agent_id, "D");
            // echo $agent_id;exit;
            $data = $this->db->query("select * FROM tls_agent_mst_outbound where id = '$agent_id'")->row_array();
        }
        
       
        // $this->load->telesales_template("group_renewal_do_home", $data);
        $data['disposition_master'] = $this->db->select("*")
        ->from("group_mod_disposition_master")
        ->where("agent_type", "maker")
        ->where("display IN ('1','2')")
        ->group_by("disposition")
        ->order_by("id")
        ->get()
        ->result_array();

       
        
        $this->load->telesales_template_new_design("group_renewal_do_home", $data);
    }


    public function get_subdispostion(){
        extract($this->input->post(null, true));

        $data = $this->db->select("disposition")
        ->from("group_mod_disposition_master")
        ->where("id", $disposition_val)
        ->get()
        ->row_array();

        $disposition_text = $data['disposition'];

        $agent_type_id = $_SESSION['telesales_session']['outbound'];

        if ($agent_type_id == '2') {
          $agent_type = 'DO';
        }

        if ($agent_type_id == '1') {
            $agent_type = 'AV';
        }

        if(isset($proposal_page)){
            if($proposal_page == 1){
                if($agent_type == 'DO'){
                    $subdisposition_data = $this->db->select("*")
                    ->from("group_mod_disposition_master")
                    ->where("agent_type", "maker")
                    ->where("disposition", $disposition_text)
                    ->where("display IN ('1','3')")
                    ->get()
                    ->result_array();
                }   
        
                if($agent_type == 'AV'){
                    $subdisposition_data = $this->db->select("*")
                    ->from("group_mod_disposition_master")
                    ->where("agent_type", "checker")
                    ->where("disposition", $disposition_text)
                    ->where("display IN ('1','3')")
                    ->get()
                    ->result_array();
                }   
        
            }
        }else{
            $subdisposition_data = $this->db->select("*")
            ->from("group_mod_disposition_master")
            // ->where("agent_type", "checker")
            ->where("disposition", $disposition_text)
            // ->where("display IN ('1','3')")
            ->get()
            ->result_array();
        }
        

        
      
        
        // echo $this->db->last_query();
        // print_pre($subdisposition_data);
        // exit;

       

        $output = "";

        foreach( $subdisposition_data as $single_subdisposition ){
            $output .= '<option value="'.$single_subdisposition['id'].'">'.$single_subdisposition["subdisposition"].'</option>';
        }
        
        echo $output;
    }


    public function tele_renewal_group_do(){

        extract($this->input->post(null, true));

         $link_for_customer = 0;

         //check in agent login - if lead is created by DO before or not
         if($_SESSION['telesales_session']['outbound'] == '1' && $_SESSION['telesales_session']['is_admin'] != "1"){
            $check_do_lead = $this->db->query("select id from group_mod_create where coi_number LIKE '%".$confirm_policy_coi_number."%'  AND status = '1' AND in_av_bucket = '1'")->row_array();
             //echo $this->db->last_query();exit;
            // print_r($_SESSION['telesales_session']);exit;
            if(!$check_do_lead){
                $output = ['error' => ['ErrorCode' => '0070', 'ErrorMessage' => 'Failed', 'output_msg' => "Record does not exist in master"]];
                echo json_encode($output);
                exit;
            }
        }

        $check_payment = $this->db->query("select id from group_mod_create where coi_number LIKE '%".$confirm_policy_coi_number."%'  AND LOWER(`payment_status`) = 'success'")->row_array();

        // uncomment before moving
        if($check_payment){
            $output = ['error' => ['ErrorCode' => '0071', 'ErrorMessage' => 'Failed', 'output_msg' => "Payment already received for this COI"]];
            echo json_encode($output);
            exit;
        }


        // 27-12-2021
        if($_SESSION['telesales_session']['outbound'] == '2'){
            // $link_sent_by = "DO";

            $session_data = $this->session->userdata('telesales_session');
            $do_id = $session_data['agent_id'];
            $do_id = encrypt_decrypt_password($do_id, "D");

            $check_wip = $this->db->query("select id from group_mod_create where coi_number LIKE '%".$confirm_policy_coi_number."%'  AND `status` = '1' AND wip = 1 AND do_id != '".$do_id."'")->row_array();

       
            if($check_wip){
                    // $output = ['error' => ['ErrorCode' => '0072', 'ErrorMessage' => 'Failed', 'output_msg' => "Work in process for this COI by another DO"]];
                    // echo json_encode($output);
                    // exit;
               
            }
         }
 
         if($_SESSION['telesales_session']['outbound'] == '1'){

            $session_data = $this->session->userdata('telesales_session');
            $av_id = $session_data['agent_id'];
            $av_id = encrypt_decrypt_password($av_id, "D");

            $check_wip = $this->db->query("select id from group_mod_create where coi_number LIKE '%".$confirm_policy_coi_number."%'  AND `status` = '1' AND wip = 1 AND updated_at >= NOW() - INTERVAL 3 MINUTE AND wip_av_id != '".$av_id."'")->row_array();

            // echo $this->db->last_query();exit;
       
            // if($check_wip){
                // if(!isset($previous_av_id)){
                //     $output = ['error' => ['ErrorCode' => '0072', 'ErrorMessage' => 'Failed', 'output_msg' => "Work in process for this COI by another AV"]];
                //     echo json_encode($output);
                //     exit;
                // }
               
            // }
 
         }
        

        // echo $send_trigger;exit;
        // $get_lead_id_grp =  $this->db->select("lead_id")
        // ->from("telesales_renewal_group")
        // ->where("hb_certificate_no", $confirm_policy_coi_number)
        // ->get()
        // ->row_array();
        // if (!empty($get_lead_id_grp)) {
        //     $lead_id_grp = $get_lead_id_grp['lead_id'];
           
        // } else {
        //     $output = ['error' => ['ErrorCode' => '0001', 'ErrorMessage' => 'Failed', 'output_msg' => "Record does not exist in master"]];
        //     echo json_encode($output);
        //     exit;
        // }
        // echo "cs ".$claim_status;
        // echo "ps ".$ped_status;
        // exit;
        $response = $this->renewal_do_m->tele_renewal_group_do($confirm_policy_coi_number);
        

      
        $djson = json_decode($response, TRUE);

       
        $ErrorCode = $djson['error'][0]['ErrorCode'];
        $ErrorMessage = $djson['error'][0]['ErrorMessage'];

        // echo $ErrorCode;exit;

        if ($ErrorCode != '00' && !empty($djson)) {
            $output = ['error' => ['ErrorCode' => $ErrorCode, 'ErrorMessage' => 'Failed', 'output_msg' => $ErrorMessage]];
            echo json_encode($output);
            exit;
        }else if(empty($djson)){
            $output = ['error' => ['ErrorCode' => '0010', 'ErrorMessage' => 'Failed', 'output_msg' => $ErrorMessage]];
            echo json_encode($output);
            exit;
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
        } else if ($policy_fi_type = "Individual") {
            $Policy_lapsed_flag =  $djson['response']['policyData']['Policy_lapsed_flag'];
            $Renewed_Flag =  $djson['response']['policyData']['Renewed_Flag'];
            $Renewable_Flag =  $djson['response']['policyData']['Renewable_Flag'];
        }


        // $Renewed_Flag =  "no";
        // $Renewable_Flag =  "yes";

        if($send_trigger == "check"){

            // $check_duplicate_record = $this->db->query("select id from group_mod_create where lead_id_grp = ".$lead_id_grp." ")->row_array();
            $check_duplicate_record = $this->db->query("select id,disposition_master_id,bitly_link from group_mod_create where coi_number LIKE '%".$confirm_policy_coi_number."%' AND status = '1' ")->row_array();

            if(isset($check_duplicate_record) && $reHit == 0){

                if(in_array($check_duplicate_record['disposition_master_id'],['4']) && $_SESSION['telesales_session']['outbound'] == '2' ) {
                    $output = ['error' => ['ErrorCode' => '0025', 'ErrorMessage' => 'Failed', 'output_msg' => "COI is in AV's bucket, Still want to proceed?", 'is_policy_exist_grp' => 'Renewal Link Triggered']];
                    echo json_encode($output);
                    exit;
                }

                else if(in_array($check_duplicate_record['disposition_master_id'],['47'])){

                    $is_policy_exist_grp = 'Regenerate Lead';

                }
                else if(in_array($check_duplicate_record['disposition_master_id'],['5','48'])){

                   
                    $is_policy_exist_grp = 'Renewal Link Triggered';

                }
                else if(in_array($check_duplicate_record['disposition_master_id'],['6']) && !empty($check_duplicate_record['bitly_link'])){

                    $is_policy_exist_grp = 'Renewal Link Triggered';

                }else{

                    $is_policy_exist_grp = 'Open for Renewal';
                }
               
            }else{
                $is_policy_exist_grp = 'Open for Renewal';
            }

            
           

            
            $output = ['error' => ['ErrorCode' => '00', 'ErrorMessage' => 'Success', 'output_msg' => $ErrorMessage], 'policy_lapsed_flag' => $Policy_lapsed_flag, "renewal_status" => $Renewable_Flag, "renewed_flag" => $Renewed_Flag, "is_policy_exist_grp" => $is_policy_exist_grp];

            //exit if its only renewal check, proceed for link trigger
            // 1-12-2021
            if(strtolower($Renewed_Flag) != 'yes'){
                echo json_encode($output);
                exit;
            }   
        }

        
        // 1-12-2021
        if(strtolower($Renewed_Flag) == 'yes'){
            $subdisposition_grp = '3';
        }   


        //view proposal for agent
        if($submit_btn == 'view_proposal_agent'){

            $check_do_lead = $this->db->query("select id,lead_id,disposition_master_id from group_mod_create where coi_number LIKE '%".$confirm_policy_coi_number."%'  AND status = '1' AND in_av_bucket = '1'")->row_array();
            $check_do_lead_id = $check_do_lead['lead_id'];
            $check_do_lead_id_encrypt = encrypt_decrypt_password($check_do_lead_id);

            // 28-11-2021
            $check_trigger = $this->db->query("select id from group_mod_tele_renewal_triggers where lead_id = '".$check_do_lead_id."'  AND status = '1'")->row_array();
            $check_trigger_encrypt = encrypt_decrypt_password($check_trigger['id'], "E");
            $url = base_url('group_renewal_modify_view/' . $check_trigger_encrypt);
            $output_msg = $url;

            $session_data = $this->session->userdata('telesales_session');
            $av_id = $session_data['agent_id'];
            $av_id = encrypt_decrypt_password($av_id, "D");
            $data_session = $this->db->query("select center FROM tls_agent_mst_outbound where id = '$av_id'")->row_array();

            
            // 27-12-2021
            if($check_do_lead['disposition_master_id'] == '48'){
                $this->db->set('link_for_customer', '0');
                $this->db->set('wip_av_id', $av_id);
                $this->db->set('disable_link', '');
                $this->db->set('updated_at', date("Y-m-d H:i:s"));
                $this->db->where('lead_id', $check_do_lead_id);
                $this->db->update('group_mod_create');
            }else{
                $update_policy = 
                $this->db->set('av_id', $av_id);
                $this->db->set('wip_av_id', $av_id);
                $this->db->set('av_location', $data_session['center']);
                $this->db->set('link_for_customer', '0');
                $this->db->set('disable_link', '');
                $this->db->set('updated_at', date("Y-m-d H:i:s"));
                $this->db->where('lead_id', $check_do_lead_id);
                $this->db->update('group_mod_create');
            }
         


            $output = ['error' => ['ErrorCode' => '00', 'ErrorMessage' => 'Success', 'output_msg' => $output_msg], 'policy_lapsed_flag' => $Policy_lapsed_flag, "renewal_status" => $Renewable_Flag, "renewed_flag" => $Renewed_Flag];

            echo json_encode($output);

            exit;
        }


        $get_address_api = $this->renewal_do_m->tele_renewal_group_do_address_api($confirm_policy_coi_number);
        $get_address_api_url =  "https://bizpre.adityabirlahealth.com/ABHICL_HealthCoreXML/Service1.svc/PolicyDetail/".$confirm_policy_coi_number."/null";
        $get_address_api_arr = json_decode($get_address_api, TRUE);

        $com_api_CustomerCode =  $get_address_api_arr['Response']['CustomerCode'];

        if(empty($com_api_CustomerCode)){
            $output = ['error' => ['ErrorCode' => '0011', 'ErrorMessage' => 'Failed', 'output_msg' => 'could not get details, please try again!']];
            echo json_encode($output);
            exit;
        }

        $leads_count=$this->db
            ->select('id,lead_id')
            ->from('group_mod_create')
            ->like('coi_number', $confirm_policy_coi_number)
            ->get()
            ->result_array();

        $concat_lead_no = count($leads_count) + 1;
        $concat_lead_no = str_pad($concat_lead_no, 2, '0', STR_PAD_LEFT);
        
        // 23-12-2021
        if(count($leads_count) == 0){
            $lead_id = '2'.$this->renewal_do_m->generate_lead_id().'_'.$concat_lead_no;
        }else{
            $old_lead_id = $leads_count[0]['lead_id'];
            $old_lead_id = explode('_',$old_lead_id);
            $old_lead_id = $old_lead_id[0];
            $lead_id = $old_lead_id.'_'.$concat_lead_no;
        }
        

         $update_policy = $this->db->set('status', 0);
                $this->db->like('coi_number', $confirm_policy_coi_number);
                $this->db->update('group_mod_create');
        
        $req = array(
            "Lead_Id" => "",
            "master_policy_number" => "",
            "certificate_number" => $confirm_policy_coi_number,
            "dob" => "",
            "proposer_mobileNumber" => "",
        );
        $req = json_encode($req);

        $res = $response;

        $edit_access_to = "customer";

        if($claim_status == "no" && $ped_status == "no"){
            $is_editable = "yes";
        }else{
            $is_editable = "no";
        }

        //setting is_editable = "no" because we are deploying as non editable
        $is_editable = "no";

        $status = "1";
        $status_description = "Renewal Link Triggered";

       
        
        $product_array = $djson['response']['policyData'];
        $certs_fetched = '';
        // $product_name = '';
        $plan_code = '';
        $FinalPremium = 0;
        $FinalPremiumBreakup = '';

        //23-11-2021
        $product_name_full = "";
        $old_master_policy_no = "";
        $new_master_policy_no = "";
        $proposal_no = "";
        
        // 27-12-2021
        $old_paid_premium = 0;
        foreach ($product_array as $key => $single_product) {
            $certs_fetched .= $single_product["Certificate_number"] . ',';
            //  $product_name .= $single_product["Name_of_product"] . ',';
             $plan_code .= $single_product['PolicyproductComponents'][0]['SchemeCode'] . ',';
             $FinalPremiumBreakup .= $single_product['premium']['Renewal_Gross_Premium'] . ',';
            $FinalPremium = $FinalPremium + $single_product['premium']['Renewal_Gross_Premium'];

            //23-11-2021
            $product_name_full .= $single_product["Name_of_product"] . ',';
            //24-11-2021
            $old_master_policy_no .= $single_product["MaterPolicyNumber"] . ',';
            $new_master_policy_no .= $single_product["Renewed_Masterpolicy_Number"] . ',';

            $get_address_api_loop = $this->renewal_do_m->tele_renewal_group_do_address_api($single_product["Certificate_number"]);
            $get_address_api_url_loop =  "https://bizpre.adityabirlahealth.com/ABHICL_HealthCoreXML/Service1.svc/PolicyDetail/".$single_product["Certificate_number"]."/null";
            $get_address_api_arr_loop = json_decode($get_address_api_loop, TRUE);

            $com_api_CustomerCode_loop =  $get_address_api_arr_loop['Response']['CustomerCode'];

            

            if(empty($com_api_CustomerCode_loop)){
                $output = ['error' => ['ErrorCode' => '00111', 'ErrorMessage' => 'Failed', 'output_msg' => 'could not get details, please try again!']];
                echo json_encode($output);
                exit;
            }

            // 27-12-2021
            $old_paid_premium += $get_address_api_arr_loop['Response']['TotalPremium'];

            $proposal_no .=  $get_address_api_arr_loop['Response']['ProposalNumber']. ',';

            $fdata = [
                'lead_id' => $lead_id,
                'req' =>  $get_address_api_url_loop ,
                'res' =>  $get_address_api_loop ,
                'type' => "communication_api",
            ];
    
            $this->db->insert('group_mod_logs', $fdata);


        }
        
        $certs_fetched = rtrim($certs_fetched, ',');
        $FinalPremiumBreakup = rtrim($FinalPremiumBreakup, ',');
        // $product_name = rtrim($product_name, ',');
        $plan_code = rtrim($plan_code, ',');

        //24-11-2021
        $old_master_policy_no = rtrim($old_master_policy_no, ',');
        $old_master_policy_no = explode(',',$old_master_policy_no);
        $old_master_policy_no = array_unique($old_master_policy_no);
        $old_master_policy_no = implode(',',$old_master_policy_no);

        $new_master_policy_no = rtrim($new_master_policy_no, ',');
        $new_master_policy_no = explode(',',$new_master_policy_no);
        $new_master_policy_no = array_unique($new_master_policy_no);
        $new_master_policy_no = implode(',',$new_master_policy_no);

        $proposal_no = rtrim($proposal_no, ',');

        //23-11-2021
        $product_name_full = rtrim($product_name_full, ',');
        $product_name_full = explode(",", $product_name_full);
        $product_name_full = array_unique($product_name_full);
        $product_name_full = implode(",", $product_name_full);
        // 1-12-2021
        $product_name_full = str_replace(',', ' and ', $product_name_full);

        // echo $product_name_full;exit;

        
        $certs_fetched_arr = explode(",",$certs_fetched);
        $cert_count = count($certs_fetched_arr);
        if($cert_count > 1){
            $combi_flag = "yes";
        }else{
            $combi_flag = "no";
        }
      

        $lead_id_encrypt = encrypt_decrypt_password($lead_id);
        

        $dob_format = ($dob != '') ? date("Y-m-d", strtotime($dob)) : null;

        if ($policy_fi_type == "Family Floater") {
           
            $CustomerName =  $djson['response']['policyData'][0]['Name_of_the_proposer'];
            $Email =  $djson['response']['policyData'][0]['Proposer_Email'];
            $PhoneNo =  $djson['response']['policyData'][0]['Proposer_MobileNo'];
            $Policy_renewal_date =  $djson['response']['policyData'][0]['Policy_renewal_date'];
            $policynumber =  $djson['response']['policyData'][0]['Certificate_number'];
            $Name_of_the_proposer =  $djson['response']['policyData'][0]['Name_of_the_proposer'];
            $customer_code =  $djson['response']['policyData'][0]['Customer_Code'];
            $Policy_expiry_date =  $djson['response']['policyData'][0]['Policy_expiry_date'];
            $Policy_renewal_date =  $djson['response']['policyData'][0]['Policy_renewal_date'];
            $MaterPolicyNumber =  $djson['response']['policyData'][0]['MaterPolicyNumber'];
            $member_array = $djson['response']['policyData'][0]['Members'];

            $nominee_name =  $djson['response']['policyData'][0]['Nominee_Details']['Nominee_Name'];
            $nominee_contact =  $djson['response']['policyData'][0]['Nominee_Details']['Nominee_Contact_No'];
            $nominee_address =  $djson['response']['policyData'][0]['Nominee_Details']['Nominee_Address'];


        } else if ($policy_fi_type == "Individual") {
            
            $CustomerName =  $djson['response']['policyData']['Name_of_the_proposer'];
            $Email =  $djson['response']['policyData']['Proposer_Email'];
            $PhoneNo =  $djson['response']['policyData']['Proposer_MobileNo'];
            $Policy_renewal_date =  $djson['response']['policyData']['Policy_renewal_date'];
            $policynumber =  $djson['response']['policyData']['Certificate_number'];
            $Name_of_the_proposer =  $djson['response']['policyData']['Name_of_the_proposer'];
            $customer_code =  $djson['response']['policyData']['Customer_Code'];
            $Policy_expiry_date =  $djson['response']['policyData']['Policy_expiry_date'];
            $Policy_renewal_date =  $djson['response']['policyData']['Policy_renewal_date'];
            $MaterPolicyNumber =  $djson['response']['policyData']['MaterPolicyNumber'];
            $member_array = $djson['response']['policyData']['Members'];

            $nominee_name =  $djson['response']['policyData']['Nominee_Details']['Nominee_Name'];
            $nominee_contact =  $djson['response']['policyData']['Nominee_Details']['Nominee_Contact_No'];
            $nominee_address =  $djson['response']['policyData']['Nominee_Details']['Nominee_Address'];

        }


        //get product of previous policy, if using previous coi number
        $previous_product =  $this->db->select("b.product_id,c.product_name, b.occupation, b.annual_income")
        ->from("api_proposal_response a, employee_details b, product_master_with_subtype c")
        ->where("a.emp_id = b.emp_id")
        ->where("b.product_id = c.product_code")
        ->where("a.certificate_number", $confirm_policy_coi_number)
        // ->where("a.certificate_number", "GHI-AG-21-2001566")
        ->group_by("b.product_id")
        ->get()
        ->row_array();

        $database = 'affinity';
        $occupation = $previous_product['occupation'];
        $annual_income = $previous_product['annual_income'];

        // if(empty($previous_product)){
        //     $this->db1 = $this->load->database('axis_retail', true);
        //     $previous_product =  $this->db1->select("b.product_id,c.product_name, b.occupation")
        //     ->from("api_proposal_response a, employee_details b, product_master_with_subtype c")
        //     ->where("a.emp_id = b.emp_id")
        //     ->where("b.product_id = c.product_code")
        //     ->where("a.certificate_number", $confirm_policy_coi_number)
        //     // ->where("a.certificate_number", "GHI-DC-19-1000045")
        //     ->group_by("b.product_id")
        //     ->get()
        //     ->row_array();

        //     $database = 'axis_retail';

        //     $occupation = $previous_product['occupation'];
        //     $annual_income = "";
        // }

        if($occupation == "undefined"){
            $occupation = "";
        }
        if($annual_income == "undefined"){
            $annual_income = "";
        }

        // echo $this->db->last_query();exit;

        // if(empty($previous_product)){
        //     $output = ['error' => ['ErrorCode' => '017', 'ErrorMessage' => 'Failed', 'output_msg' => "Could not find product details"], 'policy_lapsed_flag' => $Policy_lapsed_flag, "renewal_status" => $Renewable_Flag, "renewed_flag" => $Renewed_Flag];

        //     echo json_encode($output);
        //     exit;
        // }

        $product_id = $previous_product['product_id'];
        $product_name = $previous_product['product_name'];

        

       
        $nominee_relationship = $get_address_api_arr['Response']['Relationship'];

        //session data
        $session_data = $this->session->userdata('telesales_session');

        $do_id = $session_data['agent_id'];
        $do_id = encrypt_decrypt_password($do_id, "D");
        $data_session = $this->db->query("select * FROM tls_master_do where id = '$do_id'")->row_array();
       
        

        

        $fdata = [
            'lead_id' => $lead_id,
            'disposition' => $subdisposition_grp,
            'type' => "lead_generate",
        ];

        $this->db->insert('group_mod_disposition', $fdata);


        // 28-11-2021
      
        $update_policy_renewal_link =
        $this->db->set('status', '0');
        $this->db->like('policy_number', $confirm_policy_coi_number);
        $this->db->update('group_mod_tele_renewal_triggers');

        $fdata_link = [
            'lead_id' => $lead_id,
            'lead_id_grp' => '',
            'policy_number' => $certs_fetched,
            'status' => "1",
        ];
        $this->db->insert('group_mod_tele_renewal_triggers', $fdata_link);
        $insert_id_trigger = $this->db->insert_id();
        $insert_id_trigger_encrypt = encrypt_decrypt_password($insert_id_trigger, 'E');
        
        $url = base_url('group_renewal_modify_view/' . $insert_id_trigger_encrypt);
        // $url = base_url('group_renewal_modify_view/' . $lead_id_encrypt);
        // end - 28-11-2021

        $bitly_link = NULL;
        $link_sent_by = NULL;
       
        // 1-12-2021
        if(strtolower($Renewed_Flag) != 'yes'){
           

        $bitly_req = "https://api-alerts.kaleyra.com/v5/?api_key=A6ef09c1692b5f41a8cd5bf4ac5e22695&method=txtly.create&url=" . urlencode($url) . "&title=xyz";
        $bitly_res = $this->renewal_do_m->create_short_url($insert_id_trigger_encrypt);
        $bitly_res_arr = json_decode($bitly_res, TRUE);
        $bitly_link = $bitly_res_arr["txtly"];
        $link_sent_by = "DO";

        if(isset($previous_link_sent_by)){
            $link_sent_by = $previous_link_sent_by;
        }

        


        $fdata = [
            'lead_id' => $lead_id,
            'req' =>  $bitly_req ,
            'res' =>  $bitly_res ,
            'type' => "bitly_link",
        ];

        $this->db->insert('group_mod_logs', $fdata);

        // 28-11-2021
        $update_policy =
        $this->db->set('link', $bitly_link);
        $this->db->set('created_at',  date('Y-m-d H:i:s'));
        $this->db->set('valid_till', date('Y-m-d H:i:s', strtotime("+60 days")));
        $this->db->where('id', $insert_id_trigger);
        $this->db->update('group_mod_tele_renewal_triggers');
        // end 28-11-2021

        if ($combi_flag == 'Yes') {
            $certs_fetched_arr = explode(',', $certs_fetched);
            $first_coi = $certs_fetched_arr[0];
            $second_coi = $certs_fetched_arr[1];
        } else {
            $first_coi = $certs_fetched;
        }

        // 1-12-2021
        // $AlertV1 =  $bitly_link;
        // $AlertV2 = $FinalPremium;
        // $AlertV3 = $Name_of_the_proposer;
        // $AlertV4 = $product_name;
        // $AlertV5 = $first_coi;
        // $AlertV6 = ($combi_flag == 'Yes') ? $Policy_renewal_date :  $Policy_expiry_date;

        // $AlertV7 = ($combi_flag == 'Yes') ? $product_name : '';
        // $AlertV8 = ($combi_flag == 'Yes') ? $second_coi : '';
        // $AlertV9 = ($combi_flag == 'Yes') ? $Policy_expiry_date : $Policy_renewal_date;

        // 27-12-2021
        $FinalPremiumBreakup_arr = explode(',',$FinalPremiumBreakup);

        $certs_fetched_arr = explode(",",$certs_fetched);
        $cert_count = count($certs_fetched_arr);

       if($cert_count > 1){

            $plan_code_arr = explode(',',$plan_code);
            $product_name_arr_alert = array();
            foreach($plan_code_arr as $single_plan_code){
                if($single_plan_code == '4112' || $single_plan_code == '4216'){
                    array_push($product_name_arr_alert, 'Group Activ Secure');
                }
                if($single_plan_code == '4211'){
                    array_push($product_name_arr_alert, 'Group Activ Health');
                }
                if($single_plan_code == '4224'){
                    array_push($product_name_arr_alert, 'Group Protect');
                }
            }

            $AlertV1 =  $bitly_link;
            $AlertV2 = $FinalPremium;
            // $AlertV3 = $Name_of_the_proposer;
            // 23-12-2021
            $AlertV3 = 'Axis Bank';

            $AlertV4 = $Policy_renewal_date;
            $AlertV5 = $product_name_arr_alert[0];
            // $AlertV6 = $Name_of_the_proposer;
            $AlertV6 =  $certs_fetched_arr[0];

            $AlertV7 = (isset($certs_fetched_arr[1])) ? $Policy_renewal_date : '';
            $AlertV8 = (isset($certs_fetched_arr[1])) ? $product_name_arr_alert[1] : '';
            $AlertV9 = (isset($certs_fetched_arr[1])) ? $certs_fetched_arr[1] : '';



            $AlertV10 = (isset($certs_fetched_arr[2])) ? $Policy_renewal_date : '';
            $AlertV11 = (isset($certs_fetched_arr[2])) ? $product_name_arr_alert[2] : '';
            $AlertV12 = (isset($certs_fetched_arr[2])) ? $certs_fetched_arr[2] : '';


            $AlertV13 = (isset($certs_fetched_arr[3])) ? $Policy_renewal_date : '';
            $AlertV14 =  (isset($certs_fetched_arr[3])) ? $product_name_arr_alert[3] : '';
            $AlertV15 = (isset($certs_fetched_arr[3])) ? $certs_fetched_arr[3] : '';

            $AlertV16 = (isset($certs_fetched_arr[4])) ? $Policy_renewal_date : '';
            $AlertV17 = (isset($certs_fetched_arr[4])) ? $product_name_arr_alert[4] : '';
            $AlertV18 = (isset($certs_fetched_arr[4])) ? $certs_fetched_arr[4] : '';

            $AlertV19 = '';
           

            $alertID = 'A1648';

        }else{
            $AlertV1 =  $bitly_link;
            $AlertV2 = $FinalPremium;
            // $AlertV3 = $Name_of_the_proposer;
            // 23-12-2021
            $AlertV3 = 'Axis Bank';
            $AlertV4 = $Policy_renewal_date;
            $AlertV5 = $product_name_full;
            // $AlertV6 = $Name_of_the_proposer;
            $AlertV6 = 'Axis Bank';

            $AlertV7 = $certs_fetched_arr[0];

            $AlertV8 = (isset($certs_fetched_arr[1])) ? $certs_fetched_arr[1] : '';
            $AlertV9 = (isset($certs_fetched_arr[1])) ? $Policy_renewal_date : '';
            $AlertV10 = (isset($certs_fetched_arr[1])) ? $FinalPremiumBreakup_arr[1] : '';

            $AlertV11 = (isset($certs_fetched_arr[2])) ? $certs_fetched_arr[2] : '';
            $AlertV12 = (isset($certs_fetched_arr[2])) ? $Policy_renewal_date : '';
            $AlertV13 = (isset($certs_fetched_arr[2])) ? $FinalPremiumBreakup_arr[2] : '';

            $AlertV14 = (isset($certs_fetched_arr[3])) ? $certs_fetched_arr[3] : '';
            $AlertV15 = (isset($certs_fetched_arr[3])) ? $Policy_renewal_date : '';
            $AlertV16 = (isset($certs_fetched_arr[3])) ? $FinalPremiumBreakup_arr[3] : '';

            $AlertV17 = (isset($certs_fetched_arr[4])) ? $certs_fetched_arr[4] : '';
            $AlertV18 = (isset($certs_fetched_arr[4])) ? $Policy_renewal_date : '';
            $AlertV19 = (isset($certs_fetched_arr[4])) ? $FinalPremiumBreakup_arr[4] : '';

            $alertID = 'A1647';
        }


        // 1-12-2012
        // if ($combi_flag == 'yes') {
        //     $alertID = 'A1648';
        // }

        $alertMode = 3;

        if (strlen($AlertV1) > 30) {
            $alertMode = 1;
        }

        if (empty($Email)) {
            $alertMode = 2;
        }

        // 1-12-2021
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
                    "AlertV3" => $AlertV3,
                    "AlertV4" => $AlertV4,
                    "AlertV5" => $AlertV5,
                    "AlertV6" => $AlertV6,
                    "AlertV7" => $AlertV7,
                    "AlertV8" => $AlertV8,
                    "AlertV9" => $AlertV9,
                    "AlertV10" => $AlertV10,
                    "AlertV11" => $AlertV11,
                    "AlertV12" => $AlertV12,
                    "AlertV13" => $AlertV13,
                    "AlertV14" => $AlertV14,
                    "AlertV15" => $AlertV15,
                    "AlertV16" => $AlertV16,
                    "AlertV17" => $AlertV17,
                    "AlertV18" => $AlertV18,
                    "AlertV19" => $AlertV19,
                ]

            ]

        ];

        $parameters = json_encode($parameters);

        if($submit_btn != 'send_renewal_link_grp'){
            //don't add link in table if link is not triggered
            $bitly_link = NULL;
            $link_sent_by = NULL;
        }

        // 22-12-2021
       
         // only if link triggger - start
         if($submit_btn == 'send_renewal_link_grp'){

             // 22-12-2021
        $link_for_customer = 1;

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
            'lead_id' => $lead_id,
            'req' => json_encode($parameters),
            'res' => json_encode($response_click_pss),
            'type' => "click_pss",
        ];

        $this->db->insert('group_mod_logs', $fdata);
    

        

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


        }else if($submit_btn == 'proceed_for_renewal_btn'){
            $output_msg = $url;
        }else if($submit_btn == 'only_save'){
            $output_msg = "Disposition saved successfully!";
        }
        //only if link trigger - end
    }


    // 1-12-2021
    if(strtolower($Renewed_Flag) == 'yes'){
        $output_msg = "Disposition saved successfully!";
    }

        if(isset($previous_av_id)){
            $av_id = $previous_av_id;
            $av_location = $previous_av_location;
        }else{
            $av_id = "";
            $av_location = "";
        }


        if(isset($previous_av_id)){
            $do_id = $digitalOfficer_grp;
            $do_location = $location_grp;
        }else{
            // $do_id = $do_id;
            $do_location = $data_session['center'];
        }

        if(isset($in_av_bucket)){
            $in_av_bucket = $in_av_bucket;
        }else{
            $in_av_bucket = 0;
        }
        

        if(isset($hr_amount_status)){
            $hr_amount_status = $hr_amount_status;
        }else{
            $hr_amount_status = null;
        }
    
        $fdata = [
            'lead_id' => $lead_id,
            'lead_id_grp' => "",
            'do_id' => $do_id,
            'do_location' => $do_location,
            'av_id' => $av_id,
            'av_location' => $av_location,
            'dob' => $dob_format,
            'mobile' => $mobile_number_grp,
            'coi_number' => $certs_fetched,
            'old_master_policy_no' => $old_master_policy_no,
            'new_master_policy_no' => $new_master_policy_no,
            'proposal_no' => $proposal_no,
            'req' => $req,
            'res' => $res,
            'address_api_req' => $get_address_api_url,
            'address_api_res' => $get_address_api,
            'plan_code' => $plan_code,
            'premium' => $FinalPremium,
            // 27-12-2021
            'old_premium' => $old_paid_premium,
            'premium_break' => $FinalPremiumBreakup,
            'proposer_name' => $Name_of_the_proposer,
            
            'proposer_email' => $Email,
            'proposer_mobile' => $PhoneNo,

            'nominee_name' => $nominee_name,
            'nominee_relation' => $nominee_relationship,
            'nominee_contact' => $nominee_contact,
            'nominee_address' => $nominee_address,
            
            'occupation' => $occupation,
            'annual_income' => $annual_income,
            
            'customer_code' => $customer_code,
            //23-11-2021
            'product_name' => $product_name_full,
            'product_code' => $product_id,
            'claim_status' => $claim_status,
            'hr_amount_status' => $hr_amount_status,
            'ped_status' => $ped_status,
            'edit_access_to' => $edit_access_to,
            'remark' => $remark_group,
            'disposition_master_id' => $subdisposition_grp,
            'in_av_bucket' => $in_av_bucket,
            'is_editable' => $is_editable,
            'status' => $status,
            'click_pss_parameters' => $parameters,
            'bitly_link' => $bitly_link,
            'link_sent_by' => $link_sent_by,
            'status_description' => $status_description,
            'database' => $database,
            // 22-12-2021
            'link_for_customer' => $link_for_customer,
        ];
        // print_r($fdata);exit;
        $this->db->insert('group_mod_create', $fdata);

        foreach($djson['response']['policyData'] as $key=>$single_policy_data){

            foreach($djson['response']['policyData'][$key]['Members'] as $mem_key=>$single_mem){

                $Name = $djson['response']['policyData'][$key]['Members'][$mem_key]['Name'];
                $Member_Code = $djson['response']['policyData'][$key]['Members'][$mem_key]['Member_Code'];
                $DoB = $djson['response']['policyData'][$key]['Members'][$mem_key]['DoB'];

                $DoB_format = ($DoB != '') ? date("Y-m-d", strtotime($DoB)) : null;
                $birthdate = new DateTime($DoB_format);
		        $today   = new DateTime('today');
		        $age = $birthdate->diff($today)->y;


                $Gender = $djson['response']['policyData'][$key]['Members'][$mem_key]['Gender'];
                $Email_mem = $djson['response']['policyData'][$key]['Members'][$mem_key]['Email'];
                $Mobile_Number = $djson['response']['policyData'][$key]['Members'][$mem_key]['Mobile_Number'];
                $Relation = $djson['response']['policyData'][$key]['Members'][$mem_key]['Relation'];


                
                $PlanCode = $djson['response']['policyData'][$key]['Members'][$mem_key]['MemberproductComponents'][0]['PlanCode'];
                $SumInsured = $djson['response']['policyData'][$key]['Members'][$mem_key]['MemberproductComponents'][0]['SumInsured'];
                $NetPremium = $djson['response']['policyData'][$key]['Members'][$mem_key]['MemberproductComponents'][0]['NetPremium'];
                $NetPremium_U = $djson['response']['policyData'][$key]['Members'][$mem_key]['MemberproductComponents'][0]['NetPremium_U'];
                $CB = $djson['response']['policyData'][$key]['Members'][$mem_key]['MemberproductComponents'][0]['CB'];
                $Hr_Amount = $djson['response']['policyData'][$key]['Members'][$mem_key]['MemberproductComponents'][0]['Hr_Amount'];

                $coi_number_mem = $djson['response']['policyData'][$key]['Certificate_number'];

                $status_mem = 'active';

                $modify_status = 'active';
                if(strtolower($Relation) == 'self'){
                    $modify_status = 'inactive';
                }

                $is_adult = "no";
                if(strtolower($Relation) == 'self' || strtolower($Relation) == 'spouse'){
                    $is_adult = "yes";
                }

                if($Gender == "M"){
                    $salutation_mem = "Mr";
                }

                if($Gender == "F" && ((strtolower($Relation) == 'spouse')  || (strtolower($Relation) == 'self')  ) ){
                    $salutation_mem = "Mrs";
                }else if($Gender == "F"){
                    $salutation_mem = "Ms";
                }

                $policy_type_mem = '';

                $get_fr_id = $this->db->query("select fr_id from master_family_relation where fr_name = '".$Relation."'")->row_array();
                $fr_id = $get_fr_id['fr_id'];

                $PlanCode = $djson['response']['policyData'][$key]['PolicyproductComponents'][0]['SchemeCode'];

                // echo $PlanCode;exit;
                
                if($database == 'axis_retail'){
                    $this->db=$this->load->database('axis_retail',TRUE);  
                }
                $get_policy_detail_id = $this
                ->db
                ->select("b.policy_detail_id,b.policy_sub_type_id")
                ->from("product_master_with_subtype a, employee_policy_detail b")
                ->where("a.id = b.product_name")
                ->where("a.product_code", $product_id)
                ->where("a.plan_code", $PlanCode)
                ->get()
                ->row_array();
                
                $policy_detail_id = $get_policy_detail_id['policy_detail_id'];
                $policy_sub_type_id = $get_policy_detail_id['policy_sub_type_id'];


                if($database == 'axis_retail'){
                    $this->db=$this->load->database('axis_retail',false);  
                    $this->load->database();
                      
                }
                
                // echo $this->db->database;exit;
                // if($is_adult == "no" && $age > 21){
                //         //this will not include child above age 21
                //         $update_policy = $this->db->set('is_any_member_deleted', "yes");
                //         $this->db->where('lead_id', $lead_id);
                //         $this->db->update('group_mod_create');
                // }else{
                //     $fdata = [
                //         'lead_id' => $lead_id,
                //         'lead_id_grp' => "",
                //         'policy_type' => $policy_type_mem,
                //         'policy_detail_id' => $policy_detail_id,
                //         'policy_sub_type_id' => $policy_sub_type_id,
                //         'coi_number' => $coi_number_mem,
                //         'PlanCode' => $PlanCode,
                //         'SumInsured' => $SumInsured,
                //         'PreviousSumInsured' => $SumInsured,
                //         'NetPremium' => $NetPremium,
                //         'NetPremium_U' => $NetPremium_U,
                //         'CB' => $CB,
                //         'Hr_Amount' => $Hr_Amount,
                //         'Name' => $Name,
                //         'Member_Code' => $Member_Code,
                //         'DoB' => $DoB_format,
                //         'age' => $age,
                //         'is_adult' => $is_adult,
                //         'Gender' => $Gender,
                //         'Email' => $Email_mem,
                //         'Mobile_Number' => $Mobile_Number,
                //         'Relation' => $Relation,
                //         'fr_id' => $fr_id,
                //         'salutation' => $salutation_mem,
                //         'status' => $status_mem,
                //         'modify_status' => $modify_status
                //     ];
                //     $this->db->insert('group_mod_members', $fdata);
                // }

                $fdata = [
                    'lead_id' => $lead_id,
                    'lead_id_grp' => "",
                    'policy_type' => $policy_type_mem,
                    'policy_detail_id' => $policy_detail_id,
                    'policy_sub_type_id' => $policy_sub_type_id,
                    'coi_number' => $coi_number_mem,
                    'PlanCode' => $PlanCode,
                    'SumInsured' => $SumInsured,
                    'PreviousSumInsured' => $SumInsured,
                    'NetPremium' => $NetPremium,
                    'NetPremium_U' => $NetPremium_U,
                    'CB' => $CB,
                    'Hr_Amount' => $Hr_Amount,
                    'Name' => $Name,
                    'Member_Code' => $Member_Code,
                    'DoB' => $DoB_format,
                    'age' => $age,
                    'is_adult' => $is_adult,
                    'Gender' => $Gender,
                    'Email' => $Email_mem,
                    'Mobile_Number' => $Mobile_Number,
                    'Relation' => $Relation,
                    'fr_id' => $fr_id,
                    'salutation' => $salutation_mem,
                    'status' => $status_mem,
                    'modify_status' => $modify_status
                ];
                $this->db->insert('group_mod_members', $fdata);
                
                


          }
      }


        $output = ['error' => ['ErrorCode' => '00', 'ErrorMessage' => 'Success', 'output_msg' => $output_msg], 'policy_lapsed_flag' => $Policy_lapsed_flag, "renewal_status" => $Renewable_Flag, "renewed_flag" => $Renewed_Flag];


        echo json_encode($output);

       

    }


    public function group_renewal_modify_view($lead_id_encrypt){
        $lead_id = encrypt_decrypt_password($lead_id_encrypt, "D");
        $data["lead_id"] = $lead_id;
        $this->load->telesales_template("group_renewal_modify_view", $data);
    }


    public function grp_renewal_av_send_pss(){

        extract($this->input->post(null, true));

        $update_policy = 
        $this->db->set('remark', $remarks);
        $this->db->set('wip', 0);
        $this->db->where('lead_id', $lead_id);
        $this->db->update('group_mod_create');

        $click_url = $this->db->query("select click_pss_url from product_master_with_subtype where product_code = 'R06'")->row_array();

        $lead_details = $this->db->query("select click_pss_parameters,proposer_email,proposer_mobile,coi_number from group_mod_create where lead_id = '".$lead_id."'")->row_array();

        $parameters = $lead_details['click_pss_parameters'];
        $PhoneNo = $lead_details['proposer_mobile'];
        $Email = $lead_details['proposer_email'];
        $certs_fetched = $lead_details['coi_number'];
        $parameters_arr = json_decode($parameters, TRUE);
        // print_r($parameters_arr['RTdetails']);
        

        $alertMode = $parameters_arr['RTdetails']['Alert_Mode'];

        // 28-11-2021
        $update_policy_renewal_link =
        $this->db->set('status', '0');
        $this->db->where('lead_id', $lead_id);
        $this->db->update('group_mod_tele_renewal_triggers');

        $fdata_link = [
            'lead_id' => $lead_id,
            'lead_id_grp' => '',
            'policy_number' => $certs_fetched,
            'status' => "1",
        ];
        $this->db->insert('group_mod_tele_renewal_triggers', $fdata_link);
        $insert_id_trigger = $this->db->insert_id();
        $insert_id_trigger_encrypt = encrypt_decrypt_password($insert_id_trigger, 'E');
        $url = base_url('group_renewal_modify_view/' . $insert_id_trigger_encrypt);
        // end 28-11-2021
        // echo $url;exit;
        
        $short_link_sent =  $url;

        $bitly_req = "https://api-alerts.kaleyra.com/v5/?api_key=A6ef09c1692b5f41a8cd5bf4ac5e22695&method=txtly.create&url=" . urlencode($url) . "&title=xyz";
        $bitly_res = $this->renewal_do_m->create_short_url($insert_id_trigger_encrypt);
        $bitly_res_arr = json_decode($bitly_res, TRUE);
        $bitly_link = $bitly_res_arr["txtly"];

        $parameters_arr['RTdetails']['Alertdata']['AlertV1'] = $bitly_link;

        $parameters = json_encode($parameters_arr);

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

        
        // 28-11-2021
        $update_policy =
        $this->db->set('link', $bitly_link);
        $this->db->set('created_at',  date('Y-m-d H:i:s'));
        $this->db->set('valid_till', date('Y-m-d H:i:s', strtotime("+60 days")));
        $this->db->where('id', $insert_id_trigger);
        $this->db->update('group_mod_tele_renewal_triggers');
        // end-28-11-2021

        

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


        if($_SESSION['telesales_session']['outbound'] == '2'){
           $link_sent_by = "DO";
        }

        if($_SESSION['telesales_session']['outbound'] == '1'){
            $link_sent_by = "AV";

            // 27-12-2021
            $session_data = $this->session->userdata('telesales_session');
            $av_id = $session_data['agent_id'];
            $av_id = encrypt_decrypt_password($av_id, "D");
            $data_session = $this->db->query("select center FROM tls_agent_mst_outbound where id = '$av_id'")->row_array();
            $update_policy = 
            $this->db->set('av_id', $av_id);
            $this->db->set('av_location', $data_session['center']);
            $this->db->set('disable_link', '');
            $this->db->where('lead_id', $lead_id);
            $this->db->update('group_mod_create');

        }

        $update_policy = $this->db->set('link_sent_by', $link_sent_by);
        $this->db->set('bitly_link', $short_link_sent);
        $this->db->set('click_pss_parameters', $parameters);
         // 22-12-2021
         $this->db->set('link_for_customer', 1);
        $this->db->set('disposition_master_id', $disposition_master_id);
        $this->db->set('claim_status', $claim_status);
        $this->db->set('ped_status', $ped_status);
        $this->db->set('hr_amount_status', $hr_amount_status);
        // 23-12-2021
        $this->db->set('updated_at', date("Y-m-d H:i:s"));
            $this->db->where('lead_id', $lead_id);
            $this->db->update('group_mod_create');

            $fdata = [
                'lead_id' => $lead_id,
                'req' => json_encode($parameters),
                'res' => json_encode($response_click_pss),
                'type' => "click_pss",
            ];
    
            $this->db->insert('group_mod_logs', $fdata);

            $session_data = $this->session->userdata('telesales_session');

            $fdata = [
                'lead_id' => $lead_id,
                'disposition' => $disposition_master_id,
                'type' => "pg_link_triggered",
                'agent_type' => $link_sent_by,
                'agent_id' => encrypt_decrypt_password($session_data['agent_id'],"D"),
            ];
            $this->db->insert('group_mod_logs', $fdata);
            $this->db->insert('group_mod_disposition', $fdata);


        $output = ['ErrorCode' => '00', 'url' => base_url().'group_renewal_do_home', 'output_msg' => $output_msg];
        echo json_encode($output);
        exit;

    }

    //update 27-12-2021
    public function grp_renewal_av_save(){
        extract($this->input->post(null, true));

        if($disposition_master_id == "47"){
            $update_policy = 
            $this->db->set('in_av_bucket', '0');
            $this->db->where('lead_id', $lead_id);
            $this->db->update('group_mod_create');
        }

        if($_SESSION['telesales_session']['outbound'] == '2'){
            $login_type = "DO";
         }
 
         if($_SESSION['telesales_session']['outbound'] == '1'){
             $login_type = "AV";
 
             // 27-12-2021
             $session_data = $this->session->userdata('telesales_session');
             $av_id = $session_data['agent_id'];
             $av_id = encrypt_decrypt_password($av_id, "D");
             $data_session = $this->db->query("select center FROM tls_agent_mst_outbound where id = '$av_id'")->row_array();
             $update_policy = 
             $this->db->set('av_id', $av_id);
             $this->db->set('av_location', $data_session['center']);
             $this->db->set('disable_link', 'yes');
             $this->db->where('lead_id', $lead_id);
             $this->db->update('group_mod_create');
 
         }

        $fdata = [
            'lead_id' => $lead_id,
            'disposition' => $disposition_master_id,
            'type' => "",
            'agent_type' => $login_type,
            'agent_id' => encrypt_decrypt_password($session_data['agent_id'],"D"),
        ];
        $this->db->insert('group_mod_logs', $fdata);
        $this->db->insert('group_mod_disposition', $fdata);

        $update_policy = 
			$this->db->set('disposition_master_id', $disposition_master_id);
			$this->db->set('remark', $remarks);
            $this->db->set('wip', 0);
            $this->db->set('claim_status', $claim_status);
            $this->db->set('ped_status', $ped_status);
            $this->db->set('hr_amount_status', $hr_amount_status);
            // 23-12-2021
            $this->db->set('updated_at', date("Y-m-d H:i:s"));
			$this->db->where('lead_id', $lead_id);
			$this->db->update('group_mod_create');

        $output = ['ErrorCode' => '00', 'url' => base_url().'group_renewal_do_home','output_msg' => 'Disposition saved!'];
        echo json_encode($output);
        exit;
    }


    public function group_renewal_phasetwo(){
        // exit('Testing');
        $this->load->telesales_template("telesale_grp_renewal_phase2");
    }

   

    public function get_telesales_data_phasetwo(){
        // print_r($this->input->post());exit;


        $fetch_data = $this->renewal_do_m->all_retail_data_group_phase2();
        // echo $this->db->last_query();exit;
        // print_pre($fetch_data);
        // exit;
        $i = 1;

        foreach ($fetch_data as $row) {
            $api_res = json_decode($row['res'], TRUE);
            $api_comm_res = json_decode($row['address_api_res'], TRUE);
            $new_renewal_api_res = json_decode($row['new_renewal_api_res'], TRUE);
            $new_renewal_api_req = json_decode($row['new_renewal_api_req'], TRUE);
            $new_renewal_api_req = json_decode($new_renewal_api_req, TRUE);
            $disposition_master_id = $row['disposition_master_id'];
            $triggerLink = "Trigger Link";
            if(in_array($disposition_master_id, ['4'])){
                
                $current_status= "Pending for Verification";
               
                }elseif(in_array($disposition_master_id, ['5','48'])){
                    $current_status= "Renewal Link Triggered";
                }
                elseif(in_array($disposition_master_id, ['6']) && !empty($row['bitly_link'])){
                    $current_status="Renewal Link Triggered";
                }
                elseif(in_array($disposition_master_id, ['51'])){
                    $current_status= "Policy Renewed";
                }
                else{
                    $current_status= "Disposition Saved";
                }

            $disabled = "";
           
            if($row['payment_status'] == "success" || (!in_array($disposition_master_id, ['5','48'])) ){
                $disabled = "disabled";
            }

            if(in_array($disposition_master_id, ['6']) && !empty($row['bitly_link'])){
                $disabled = "";
             }

             // print_r($_SESSION['telesales_session']);exit;

             if($_SESSION['telesales_session']['outbound'] == '2'){
                    $agent_type = 'DO';                    
                    $agent_id = $_SESSION['telesales_session']['do_id'];
                    if($row['in_av_bucket'] == '1'){
                        $disabled = "disabled";
                    }

                    if($row['link_sent_by'] == $agent_type && $row['do_id']==$agent_id){
                        $triggerLink = "Retrigger Link";
                    }else{
                        $disabled = "disabled";                                
                    }

              }
            if($_SESSION['telesales_session']['outbound'] == '1'){
                    $agent_type = 'AV';
                    if($row['link_sent_by'] == $agent_type){
                        $disabled = "disabled";                
                    }

            }


            if($row['renewed_from_other_mode'] == 'yes'){
                $disabled = "disabled";
                $current_status= "Renewed From Other Mode";
            }
            
            // if((in_array($disposition_master_id, [3,49,51,2])) ){
            //     $disabled = "disabled";
            // }

            // print_pre($new_renewal_api_req['receiptobj'][0]['collection_amount']);exit;
            $sub_data = [];

            // policy expiry date
            $sub_data[]= date("d/m/Y", strtotime($api_res['response']['policyData'][0]['Policy_expiry_date']));

            // COI Number
            $sub_data[]=$row['coi_number'];

            // Cust Name
            $sub_data[]=$row['proposer_name'];

            // product name
            $sub_data[]=$row['product_name'];

            // OLD IMD
            $sub_data[]=$api_comm_res['Response']['Intermediary_x0020_Code'];

            // Old Gross Premium
            $sub_data[]= $row['old_premium'];

            // Renewal Gross Premium
            $sub_data[]=$row['premium'];

            // DO ID
            $sub_data[]=$row['do_id_display'];

            // AV ID
            $sub_data[]=$row['agent_id_display'];

            // Axis Center
            $sub_data[]=$row['do_location'];

            // Reference ID or Proposal ID
            $sub_data[]=$row['lead_id'];

            // disposition
            $sub_data[]=$row['disposition'];

            

            // subdisposition
            $sub_data[]=$row['subdisposition'];

            // Current status
            $sub_data[]= $current_status;

            // retrigger
            $coi_number = $row['coi_number'];
            $coi_number_arr = explode(',',$coi_number);
            $single_coi = $coi_number_arr[0];
            $sub_data[] = "
            <div class='text-center'>
            <button type='button' class='btn btn-cta btn-xs tele_re_dt_tl'   ".$disabled." digital-officer='".$row['do_id']."' location='".$row['do_location']."' dob='".$row['dob']."'  mobile='".$row['mobile']."'  claim_status='".$row['claim_status']."' ped_status='".$row['ped_status']."' coi_number='".$single_coi."' id='trigger' data-subdisposition='".$row['disposition_master_id']."' data-linktrigger='".$row['link_sent_by']."' data-avid='".$row['av_id']."' data-location='".$row['av_location']."' data-hramount='".$row['hr_amount_status']."' data-avbucket='".$row['in_av_bucket']."'>".$triggerLink."<i class='ti-link'></i></button>
            <br>
            <button coi_number='".$single_coi."'  class='btn btn-cta btn-xs tele_re_dt_audit' id='audit'>Audit <i class='ti-pencil-alt'></i></button>
            </div>
            ";

            //Last Modified Date and Time
            $sub_data[]=date("d/m/Y H:i:s", strtotime($row['updated_at']));
            
            // Renewed Policy Number
            if($row['renewed_from_other_mode'] == 'yes'){
                $row['new_certificate_number'] = "Renewed From Other Mode";
            }
            $sub_data[]=$row['new_certificate_number'];

               
            // Renewal Gross Premium
            $sub_data[]= $row['premium'];

            // Renewed Net Premium
            $product_array = $api_res['response']['policyData'];
            $net_premium = 0;
            foreach ($product_array as $key => $single_product) {
                $net_premium += $single_product['PolicyproductComponents'][0]['NetPremium'] . ',';
            }
            $sub_data[]= $net_premium;

            // Inception Date
            if(!empty($new_renewal_api_res['response'][0]['policy_start_date'])){
                $inception_date = date("d/m/Y", strtotime($new_renewal_api_res['response'][0]['policy_start_date']));
            }else{
                $inception_date = "";
            }
            $sub_data[]= $inception_date;
            

            // issuance date
            if(!empty($row['issuance_datetime'])){
                $issuance_datetime = date("d/m/Y H:i:s", strtotime($row['issuance_datetime']));
            }else{
                $issuance_datetime = "";
            }
            $sub_data[]=$issuance_datetime;
            
            // New IMD post renewal
            $sub_data[]=$new_renewal_api_req['policyData'][0]['intermediary_code'];

            // HR Amount
            $sub_data[]=$this->renewal_do_m->group2_hr_amount($row['lead_id']);

            // HR Flag
            $sub_data[]=$row['hr_amount_status'];

            // Cumulative attempts
            $sub_data[]=$this->renewal_do_m->get_total_attempts($row['lead_id'], "attempts");

            // cumulative connects
            $sub_data[]=$this->renewal_do_m->get_total_attempts($row['lead_id'], "connects");
            
            // wip status
            $telSalesSession = $this->session->userdata('telesales_session');
            // print_pre($telSalesSession);exits;
            if($row['renewed_from_other_mode'] == 'yes'){
                $row['wip'] = '0';
            }
            if($row['wip'] == '1'){
               $make_actionable = "<button type='button' class='btn btn-cta btn-xs make_actionable' id='ma_".$row['lead_id']."' lead-id='".$row['lead_id']."'>Make Actionable</button>";
            }else{
                $make_actionable = "<button type='button' class='btn btn-cta btn-xs make_actionable' id='ma_".$row['lead_id']."' lead-id='".$row['lead_id']."' disabled>Make Actionable</button>";
            }
            if($telSalesSession['is_admin'] == "1"){
                $sub_data[]= $make_actionable;
            }
       

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
            "recordsTotal" => $this->renewal_do_m->all_retail_data_group_count(),
            "recordsFiltered" => $this->renewal_do_m->all_retail_data_group_count(),
            "data" => $data,
        );

        echo json_encode($output);

    }

    public function telesales_group_renewalphase2_audit(){

        extract($this->input->post(null,true));

        
        
                $this->db->select('gmc.coi_number,gmc.proposer_name,gmc.product_name,tmd.do_id as do_id_display,IFNULL(tamo.agent_id, "") as agent_id_display,gmc.do_location,gmc.lead_id,gmdm.disposition,gmdm.subdisposition,DATE_FORMAT(gmc.updated_at,"%d/%m/%Y %H:%i:%s") as updated_at,gmc.premium,gmc.address_api_res,gmm.SumInsured,COUNT(DISTINCT gmm.NAME) AS member_count,gmc.status,gmc.payment_status,gmc.renewed_from_other_mode');
                $this->db->from('group_mod_create as gmc');

                $this->db->join('group_mod_disposition_master gmdm', 'gmc.disposition_master_id = gmdm.id', 'left');
                $this->db->join('tls_master_do tmd', 'gmc.do_id = tmd.id', 'left');
                $this->db->join('tls_agent_mst_outbound tamo', 'gmc.av_id = tamo.id', 'left');
                $this->db->join('group_mod_members gmm', 'gmc.lead_id = gmm.lead_id', 'left');
                $this->db->like('gmc.coi_number',$coi_number);
                $this->db->order_by('gmc.id','DESC');
                $this->db->group_by('gmc.lead_id');
                
                $all_data=$this->db->get()->result_array();
                // echo $this->db->last_query();exit;
                // print_pre($all_data);
                // $all_data = array_map('utf8_encode', $all_data);
                 echo json_encode($all_data);
    }

    public function update_res_grp_2()
    {

        $data = $this->db->select("lead_id")
        ->from("group_mod_create")
        ->where("status","1")
        ->get()
        ->result_array();
        

        foreach($data as $single_data){

            $data_coi = $this->db->select("coi_number")
            ->from("group_mod_create")
            ->where("lead_id", $single_data['lead_id'])
            ->get()
            ->row_array();

            $data_coi = explode(',',$data_coi['coi_number']);


            $old_paid_premium = 0;

            foreach($data_coi as $single_coi){
                $get_address_api_loop = $this->renewal_do_m->tele_renewal_group_do_address_api($single_coi);
                $get_address_api_url_loop =  "https://bizpre.adityabirlahealth.com/ABHICL_HealthCoreXML/Service1.svc/PolicyDetail/".$single_coi."/null";
                $get_address_api_arr_loop = json_decode($get_address_api_loop, TRUE);
    
                $com_api_CustomerCode_loop =  $get_address_api_arr_loop['Response']['CustomerCode'];
               
                
    
                if(empty($com_api_CustomerCode_loop)){
                    $output = ['error' => ['ErrorCode' => '00111', 'ErrorMessage' => 'Failed', 'output_msg' => 'could not get details, please try again!']];
                    echo json_encode($output);
                    exit;
                }
    
                $old_paid_premium += $get_address_api_arr_loop['Response']['TotalPremium'];
            }
           
            echo $old_paid_premium;
            echo '<br>';
            echo '--';
            $update_policy = $this->db->set('old_premium', $old_paid_premium);
            $this->db->where('lead_id', $single_data['lead_id']);
            $this->db->update('group_mod_create');
            
        }
      
    }


    public function group_renewal_wip_update(){
        extract($this->input->post(null,true));

        $update_policy = 
        $this->db->set('wip', 0);
        $this->db->where('lead_id', $lead_id);
        $this->db->update('group_mod_create');

       echo 'Lead is actionable now!';

    }


    // 31-12-2021
    public function check_regenerate_lead_group_renewal()
    {
        extract($this->input->post(null,true));

        $check_do_lead = $this->db->query("select id,do_id,lead_id from group_mod_create where coi_number LIKE '%".$confirm_policy_coi_number."%'  AND status = '1'")->row_array();

        $session_data = $this->session->userdata('telesales_session');
        $do_id_session = $session_data['agent_id'];
        $do_id_session = encrypt_decrypt_password($do_id_session, "D");

        // $data_do_details = $this->db->query("select * FROM tls_master_do where id = '$do_id'")->row_array();

        if($do_id_session == $check_do_lead['do_id']){

            $output = array(
                "status" => 'success',
                "msg" => 'redirect',
                "url" => base_url('group_renewal_modify_view/' . encrypt_decrypt_password($check_do_lead['lead_id'], "E")),
            );
    
           
        }else{

            $output = array(
                "status" => 'failed',
                "msg" => 'proceed',
                "url" => '',
            );
            
        }

        echo json_encode($output);


    }



   


}
