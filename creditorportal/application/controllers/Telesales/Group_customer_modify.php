<?php

use Mpdf\Output\Destination;
use Mpdf\Tag\P;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once(APPPATH . "controllers/MY_TelesalesSessionCheck.php");

class Group_customer_modify extends MY_TelesalesSessionCheck
{
    
    public function __construct()
    {
        parent::__construct();
        
        $this->algoMethod = 'aes-128-ecb';
        $this->hashMethod = 'SHA512';
        $this->hash_key = 'razorpay';
        $this->encrypt_key = 'axisbank12345678';

        $this->load->model('Telesales/Group_renewal_do_m', "renewal_do_m", true);

        $this->db1=$this->load->database('axis_retail',TRUE);   

    }


    // public function index()
    // {
       
    //     $this->load->telesales_template("group_renewal_do_home.php");
    // }

    

    public function test_issuance(){
        $lead_id = "1636705776";
        $issuance = $this->renewal_do_m->group_customer_modify_issuance($lead_id);
    }

    public function grp_renewal_sub_to_av(){
        extract($this->input->post(null, true));
        // $lead_id = encrypt_decrypt_password($lead_id, "D");

        $update_policy = 
        $this->db->set('in_av_bucket', 1);
        $this->db->set('wip', 0);
        $this->db->set('disposition_master_id', $disposition_master_id);
        $this->db->set('claim_status', $claim_status);
        $this->db->set('ped_status', $ped_status);
        $this->db->set('hr_amount_status', $hr_amount_status);
        $this->db->set('remark', $remarks);
        // 23-12-2021
        $this->db->set('updated_at', date("Y-m-d H:i:s"));
        $this->db->where('lead_id', $lead_id);
        $this->db->update('group_mod_create');

        $fdata = [
            'lead_id' => $lead_id,
            'disposition' => $disposition_master_id,
            'type' => "",
            'agent_type' => "DO",
            'agent_id' => encrypt_decrypt_password($session_data['agent_id'],"D"),
        ];
        $this->db->insert('group_mod_logs', $fdata);
        $this->db->insert('group_mod_disposition', $fdata);

        $output = ['url' => base_url()."group_renewal_do_home"];
        echo json_encode($output);
    }

    public function group_renewal_modify_view($lead_id_encrypt){

        // echo 'leadid is'.$lead_id_encrypt;exit;
        
        $decrypt =  encrypt_decrypt_password($lead_id_encrypt, 'D');

        if (strpos($decrypt, '_') !== false) {
            // echo 1234;exit;
            $lead_id = encrypt_decrypt_password($lead_id_encrypt, "D");
            $data["lead_id"] = $lead_id;
    
            $policy_data = $this
            ->db
            ->select("*")
            ->from("group_mod_create a")
            ->where("lead_id", $lead_id)
            ->get()
            ->row_array();
            
            // 31-12-2021
            if($policy_data['link_for_customer'] == '1'){
                if($policy_data['status'] == "0" || $policy_data['disable_link'] == "yes"){
                    echo "Link expired";
                    exit;
                }
            }
    
            
        }else{
            $trigger_id = encrypt_decrypt_password($lead_id_encrypt, 'D');

            // echo $trigger_id;exit;
    
            $check_policy =  $this->db->select("*")
            ->from("group_mod_tele_renewal_triggers")
            ->where("id", $trigger_id)
            ->get()
            ->row_array();
    
            // print_r($check_policy);exit;
    
            $check_policy_link_customer =  $this->db->select("link_for_customer,status,disable_link")
            ->from("group_mod_create")
            ->where("lead_id", $check_policy['lead_id'])
            ->get()
            ->row_array();

            // print_r($check_policy);exit;
    
            if($check_policy_link_customer['link_for_customer'] == '1'){
                if($check_policy['status'] != '1'){
                    echo 'Link Expired.';
                     exit;
                }
                if($check_policy_link_customer['status'] == "0" || $check_policy_link_customer['disable_link'] == "yes"){
                    echo "Link expired";
                    exit;
                }
            }
    
            // $lead_id = encrypt_decrypt_password($lead_id_encrypt, "D");
            $lead_id = $check_policy['lead_id'];
            $lead_id_encrypt = encrypt_decrypt_password($lead_id, "E");
        }

        $database = $policy_data['database'];

        $data["lead_id"] = $lead_id;

        $policy_data = $this
        ->db
        ->select("*")
        ->from("group_mod_create a")
        ->where("lead_id", $lead_id)
        ->get()
        ->row_array();

        if(strtolower($policy_data['payment_status']) == "success"){
            redirect('/group_customer_modify_pd/'.$lead_id_encrypt);
            exit;
       }
        

        $customer_details = $this
        ->db
        ->select("*")
        ->from("group_mod_members")
        ->where("lead_id", $lead_id)
        ->where("Relation IN ('self', 'Self')")
        ->get()
        ->row_array();

        // if(!$customer_details){

        //     $customer_details = $this
        //     ->db
        //     ->select("*")
        //     ->from("group_mod_members")
        //     ->where("lead_id", $lead_id)
        //     ->where("Relation IN ('spouse', 'Spouse')")
        //     ->get()
        //     ->row_array();

        // }
       
        $member_data = $this
        ->db
        ->select("*")
        ->from("group_mod_members")
        ->where("lead_id", $lead_id)
        // ->where("coi_number LIKE ('%GHI%') ")
        ->group_by("NAME")
        ->order_by('fr_id')
        ->get()
        ->result_array();


    

        $family_construct = $this-> get_family_construct_single_lead($lead_id);


        //get sum insured select list - start
        if($database == "axis_retail"){
            $this->db=$this->load->database('axis_retail',TRUE);
        }
        $parent_id = "";
        $product_code = $policy_data['product_code'];
        $get_suminsured_data = $this->renewal_do_m->get_suminsured_data($parent_id,$product_code);


        foreach($get_suminsured_data[0] as $key => $value) {
            $suminsured_type =  $key;
        }

        $suminsured_select = array();
        foreach($get_suminsured_data[0][$suminsured_type] as $single_sum_insured){
            array_push($suminsured_select, $single_sum_insured['sum_insured']);
        }
        $suminsured_select = array_unique($suminsured_select);
        $suminsured_select = array_values($suminsured_select);

        // print_r($suminsured_select);exit;
        //get sum insured select list - end



         //get family_construct list - start
        $get_policy_detail_id = $this
        ->db
        ->select("b.suminsured_type,b.policy_detail_id")
        ->from("product_master_with_subtype a, employee_policy_detail b")
        ->where("a.id = b.product_name")
        ->where("a.product_code", $product_code)
        ->where("a.policy_subtype_id", "1")
        ->get()
        ->row_array();

        // print_r($get_policy_detail_id);exit;

        $sumInsured = $member_data[0]['SumInsured'];
        $table = $get_policy_detail_id['suminsured_type'];
        $policyNo =$get_policy_detail_id['policy_detail_id'];

        $get_family_construct = $this->renewal_do_m->get_family_construct($sumInsured, $table,$policyNo);

        $family_construct_select = array();
        foreach($get_family_construct as $single_fc){
            array_push($family_construct_select, $single_fc['family_type']);
        }

        //get family_construct list - end


        //get master relation select - start
        $master_relation = $this
        ->db
        ->select("*")
        ->from("master_family_relation")
        ->get()
        ->result_array();
        //get master relation select - end

         //get master relation select allowed- start

         $get_relationship_id = $this
        ->db
        ->select("*")
        ->from("master_broker_ic_relationship")
        ->where("policy_id", $policyNo)
        ->get()
        ->row_array();

        $get_relationship_id = $get_relationship_id['relationship_id'];
        $get_relationship_id = explode(',',$get_relationship_id);

         $master_relation_allowed = $this
         ->db
         ->select("*")
         ->from("master_family_relation")
         ->where_in("fr_id", $get_relationship_id)
         ->get()
         ->result_array();
         //get master relation select allowed- end


         $get_policy_sub_type_ids = $this
         ->db
         ->select("group_concat(DISTINCT(policy_sub_type_id)) AS policy_sub_type_id")
         ->from("group_mod_members")
         ->where("lead_id", $lead_id)
         ->get()
         ->row_array();

         $policy_sub_type_ids = $get_policy_sub_type_ids['policy_sub_type_id'];
         $policy_sub_type_ids = explode(",",$policy_sub_type_ids);


         //
         $address_api_res = $policy_data['address_api_res'];
         $address_api_res = json_decode($address_api_res, TRUE);

        //  print_pre($address_api_res);exit;

        //  echo $address_api_res['Response']['Home_Address_1'];exit;
        

        

        $api_res = $policy_data['res'];
        $api_res = json_decode($api_res, TRUE);
        if (!empty($api_res['response']['policyData'][0]['Sum_insured_type'])) {
            $policy_fi_type = "Family Floater";
        } else if (!empty($api_res['response']['policyData']['Sum_insured_type'])) {
            $policy_fi_type = "Individual";
        }

        if ($policy_fi_type == "Family Floater") {  
            $old_master_policy =  $api_res['response']['policyData'][0]['MaterPolicyNumber'];    
            $Renewed_Masterpolicy_Number = $api_res['response']['policyData'][0]['Renewed_Masterpolicy_Number'];   
            $Policy_start_date = $api_res['response']['policyData'][0]['Policy_start_date'];   
            $Policy_expiry_date = $api_res['response']['policyData'][0]['Policy_expiry_date'];   
        }
        if ($policy_fi_type == "Individual") {  
            $old_master_policy =  $api_res['response']['policyData']['MaterPolicyNumber'];
            $Renewed_Masterpolicy_Number = $api_res['response']['policyData']['Renewed_Masterpolicy_Number']; 
            $Policy_start_date = $api_res['response']['policyData']['Policy_start_date']; 
            $Policy_expiry_date = $api_res['response']['policyData']['Policy_expiry_date']; 
        }
       
        $Policy_start_date = date("Y-m-d", strtotime($Policy_start_date));
        $Policy_expiry_date = date("Y-m-d", strtotime($Policy_expiry_date));

        //previous paid premium

        $coi_number_arr = $policy_data['coi_number'];
        $coi_number_arr = explode(",",$coi_number_arr);
        $previous_premium = 0;
        foreach($coi_number_arr as $single_coi){
            $get_previous_data = $this->renewal_do_m->tele_renewal_group_do_address_api($single_coi);

            $get_previous_data = json_decode($get_previous_data, TRUE);
            // print_pre($get_previous_data);
            $previous_premium += $get_previous_data["Response"]["Premium"];
            
        }

        // echo 'previous premium is'.$previous_premium;

        //get HR Amount
        $get_total_hr_amount = $this
         ->db
         ->select("SUM(Hr_Amount) as total_hr_amount")
         ->from("group_mod_members")
         ->where("lead_id", $lead_id)
        //  ->where("Relation", "Self")
        //  ->where("coi_number LIKE '%GHI%'")
         ->group_by("NAME")
         ->get()
         ->result_array();

         $total_hr_amount = 0;

         foreach($get_total_hr_amount as $single_hr_amount){
            $total_hr_amount += $single_hr_amount['total_hr_amount'];
         }

        //  echo $total_hr_amount;exit;

        //  echo $this->db->last_query();exit;

        //  echo 'total hr '.$total_hr_amount;exit;

        //get disposition and subdisposition
        $dis_subdis = $this
         ->db
         ->select("disposition,subdisposition")
         ->from("group_mod_disposition_master")
         ->where("id", $policy_data['disposition_master_id'])
         ->get()
         ->row_array();

         $disposition = $dis_subdis['disposition'];
         $subdisposition = $dis_subdis['subdisposition'];


        
        if($database == "axis_retail"){
            $this->db=$this->load->database('axis_retail',false);
        }

       
        // $decode_policy_data = json_decode($policy_data['address_api_res'],TRUE);
        // // $decode_policy_data = $decode_policy_data['Response']['PolicyOwnerName'];
        
        // $gender = $decode_policy_data['Response']['Gender'];
        // $ProposerDob = $decode_policy_data['Response']['ProposerDob'];
        // print_pre($decode_policy_data);exit; 
        // echo $ProposerDob;exit;
        // print_pre($decode_policy_data['Response']['PolicyOwnerName']);exit;

        $data["member_data"] = $member_data;
        $data["policy_data"] = $policy_data;
        $data["customer_details"] = $customer_details;
        $data["family_construct"] = $family_construct;
        $data["suminsured_select"] = $suminsured_select;
        $data["family_construct_select"] = $family_construct_select;
        $data["master_relation"] = $master_relation;
        $data["master_relation_allowed"] = $master_relation_allowed;
        $data["policy_sub_type_ids"] = $policy_sub_type_ids;
        $data["address_api_res"] = $address_api_res;
        $data["old_master_policy"] = $old_master_policy;
        $data["Renewed_Masterpolicy_Number"] = $Renewed_Masterpolicy_Number;
        $data["Policy_start_date"] = $Policy_start_date;
        $data["Policy_expiry_date"] = $Policy_expiry_date;
        $data["previous_premium"] = $previous_premium;
        $data["total_hr_amount"] = $total_hr_amount;
        $data["disposition"] = $disposition;
        $data["subdisposition"] = $subdisposition;
        // print_r($data);exit;
        // $this->load->telesales_template("group_renewal_modify_view_backup", $data);
        $this->load->telesales_template_new_design("group_renewal_modify_view", $data);
    }

   


    public function delete_member(){
        extract($this->input->post(null, true));

        $get_member = $this
        ->db
        ->select("*")
        ->from("group_mod_members")
        ->where("id", $member_id)
        ->get()
        ->row_array();

        $lead_id = $get_member['lead_id'];
        $Name = $get_member['Name'];
        $Member_Code = $get_member['Member_Code'];

        $this->db->query("delete from group_mod_members where lead_id = '$lead_id' 
        AND Name = '$Name'");

        $family_construct = $this-> get_family_construct_single_lead($lead_id);
        $data['status'] = "success";
        $data['family_construct'] = $family_construct;

        echo json_encode($data);

    }


    public function add_member(){

        extract($this->input->post(null, true));
        // var_dump($_POST);

        $get_policy_detail = $this
        ->db
        ->select("*")
        ->from("group_mod_create")
        ->where("lead_id", $lead_id)
        ->get()
        ->row_array();

        $database = $get_policy_detail['database'];

        if($mem_form_relation == "0" || $mem_form_relation == "1"){

            $check_already_member = $this
            ->db
            ->select("*")
            ->from("group_mod_members")
            ->where("lead_id", $lead_id)
            ->where("fr_id", $mem_form_relation)
            ->get()
            ->row_array();

            if($check_already_member){
                $response = ["status"=>"failed","description"=>"Member already exists with same relation."];
                echo json_encode($response);
                exit;
            }

           

        }

        $plan_code = $get_policy_detail['plan_code'];
        $coi_number = $get_policy_detail['coi_number'];
        $product_code = $get_policy_detail['product_code'];

        $plan_code = explode(",", $plan_code);
        $coi_number = explode(",", $coi_number);
        $policy_count = count($plan_code);

        foreach($coi_number as $key=>$single_coi){

            if($database == 'axis_retail'){
                $this->db=$this->load->database('axis_retail',TRUE);  
            }
            
            $get_policy_detail_id = $this
            ->db
            ->select("b.policy_detail_id,b.policy_sub_type_id")
            ->from("product_master_with_subtype a, employee_policy_detail b")
            ->where("a.id = b.product_name")
            ->where("a.product_code", $product_code)
            ->where("a.plan_code", $plan_code[$key])
            ->get()
            ->row_array();
              
            $policy_detail_id = $get_policy_detail_id['policy_detail_id'];
            $policy_sub_type_id = $get_policy_detail_id['policy_sub_type_id'];

            
            // echo 'hi ';exit;
            // echo 'testing 123';exit;

            $get_relationship_id_query = $this
            ->db
            ->select("*")
            ->from("master_broker_ic_relationship")
            ->where("policy_id", $policy_detail_id)
            ->get()
            ->row_array();

            if($database == 'axis_retail'){
                $this->db=$this->load->database('axis_retail',false);  
                $this->load->database();
            }
           
            $get_relationship_id = $get_relationship_id_query['relationship_id'];

            $get_relationship_id = explode(',',$get_relationship_id);

            if( in_array($mem_form_relation ,$get_relationship_id ) )
            {
                $today = date("Y-m-d");
                // $today = date("Y-m-d",strtotime(date("Y-m-d") . "-1 days"));
                $diff = date_diff(date_create(date('d-m-Y', strtotime($mem_form_dob))) , date_create($today));
                $age = $diff->format('%y');
                $age_type = 'years';
                if($mem_form_relation == "2" || $mem_form_relation == "3"){
                    $age = $diff->format('%a');
                    $age_type = 'days';
                }
                $res = $this->check_validation_age($policy_detail_id, $mem_form_relation, $age, $age_type);

                if($res['status'] != 'success'){
                    echo json_encode($res);
                     exit;
                }
                
                

            $max_child = $get_relationship_id_query['max_child'];

            if($mem_form_relation == "2" || $mem_form_relation == "3"){
                $get_added_child_query = $this
                ->db
                ->select("COUNT(fr_id) AS child_count")
                ->from("group_mod_members")
                ->where("lead_id", $lead_id)
                ->where("is_adult", "no")
                ->where("PlanCode", $plan_code[$key])
                ->get()
                ->row_array();
               

                $get_added_child = $get_added_child_query['child_count'];

               

                if($get_added_child_query){
                    if($get_added_child >= $max_child){
                        $response = ["status"=>"failed","description"=>"Maximum child already added."];
                        echo json_encode($response);
                        exit;
                    }
                }
                
            }

                //insert member here
                $get_policy_mem_details = $this
                ->db
                ->select("*")
                ->from("group_mod_members")
                ->where("lead_id", $lead_id)
                ->where("PlanCode", $plan_code[$key])
                ->get()
                ->row_array();


                
                $DoB_format = ($mem_form_dob != '') ? date("Y-m-d", strtotime($mem_form_dob)) : null;
                $birthdate = new DateTime($DoB_format);
		        $today   = new DateTime('today');
		        $age = $birthdate->diff($today)->y;

                if($mem_form_relation == "2" ||  $mem_form_relation == "3"){
                    $is_adult = "no";
                }else{
                    $is_adult = "yes";
                }

                if($mem_form_salutation == "Mr"){
                    $Gender = 'M';
                }else{
                    $Gender = 'F';
                }
                
                $fdata = [
                    'lead_id' => $lead_id,
                    'lead_id_grp' =>  $get_policy_mem_details['lead_id_grp'] ,
                    'policy_type' =>  $get_policy_mem_details['policy_type'] ,
                    'policy_detail_id' => $policy_detail_id,
                    'policy_sub_type_id' => $policy_sub_type_id,
                    'coi_number' =>  $get_policy_mem_details['coi_number'] ,
                    'PlanCode' =>  $get_policy_mem_details['PlanCode'] ,
                    'SumInsured' =>  $get_policy_mem_details['SumInsured'] ,
                    'NetPremium' =>  $get_policy_mem_details['NetPremium'] ,
                    'NetPremium_U' =>  $get_policy_mem_details['NetPremium_U'] ,
                    'CB' =>  '0' ,
                    'Hr_Amount' =>  '0' ,
                    'Name' => $mem_form_fname.' '.$mem_form_lname ,
                    'Member_Code' => "" ,
                    'DoB' => $mem_form_dob ,
                    'age' => $age ,
                    'is_adult' => $is_adult ,
                    'Gender' => $Gender ,
                    'Email' => "" ,
                    'Relation' => $relation_desc ,
                    'fr_id' => $mem_form_relation ,
                    'salutation' => $mem_form_salutation,
                    'status' => "active",
                    'modify_status' => "active"
                   
                ];
        
                $this->db->insert('group_mod_members', $fdata);
                
              


            }


        }
        $response = ["status"=>"success","description"=>"Member added successfully!"];
        echo json_encode($response);
        exit;



    }



    public function get_family_construct_single_lead($lead_id){
        //get previous family_construct start
        //update - 16-11-2021
        $family_construct_adult = $this
        ->db
        ->select("count(DISTINCT `NAME`) as adult_count")
        ->from("group_mod_members")
        ->where("lead_id", $lead_id)
        ->where("is_adult", "yes")
        // ->where("coi_number LIKE ('%GHI%') ")
        ->get()
        ->row_array();

        $family_construct_adult = $family_construct_adult['adult_count'].'A';

        $family_construct_kid = $this
        ->db
        ->select("count(DISTINCT `NAME`) as kid_count")
        ->from("group_mod_members")
        ->where("lead_id", $lead_id)
        ->where("is_adult", "no")
        // ->where("coi_number LIKE ('%GHI%') ")
        ->get()
        ->row_array();

        if($family_construct_kid){
              $family_construct_kid = $family_construct_kid['kid_count'].'K';
        }else{
            $family_construct_kid = "";
        }

        // $family_construct = $family_construct_adult.'+'.$family_construct_kid;

        if($family_construct_kid['kid_count'] != "0"){
            $family_construct = $family_construct_adult.'+'.$family_construct_kid;
        }else{
            $family_construct = $family_construct_adult;
        }
        //get previous family_construct end
        return $family_construct;
    }



    public function group_customer_modify_submit(){

        extract($this->input->post(null, true));

        if(isset($ghd_2)){
            if($ghd_2 == "yes"){
                $whatsapp_flag = '1';
            }else{
                $whatsapp_flag = '0';
            }
        }else{
            $whatsapp_flag = '0';
        }
       

        // var_dump($_POST);exit;
        $policy_data = $this
        ->db
        ->select("*")
        ->from("group_mod_create")
        ->where("lead_id", $lead_id)
        ->get()
        ->row_array();


        //get HR Amount
        $get_total_hr_amount = $this
         ->db
         ->select("SUM(Hr_Amount) as total_hr_amount")
         ->from("group_mod_members")
         ->where("lead_id", $lead_id)
        //  ->where("Relation", "Self")
        //  ->where("coi_number LIKE '%GHI%'")
         ->group_by("NAME")
         ->get()
         ->result_array();

         $total_hr_amount = 0;

         foreach($get_total_hr_amount as $single_hr_amount){
            $total_hr_amount += $single_hr_amount['total_hr_amount'];
         }

        $update_policy = 
        $this->db->set('whatsapp_flag', $whatsapp_flag);
        $this->db->set('claim_status', $claim_status);
        $this->db->set('ped_status', $ped_status);
        $this->db->set('hr_amount_status', $hr_amount_status);
        $this->db->set('auto_debit_status', $auto_debit_status);
        $this->db->where('lead_id', $lead_id);
        $this->db->update('group_mod_create');

        // echo $this->db->last_query();exit;
        
         
        $db_otp = $policy_data['otp'];
        $CustomerName = $policy_data['proposer_name'];
        $Email = $policy_data['proposer_email'];
        $PhoneNo = $policy_data['proposer_mobile'];
        $FinalPremium = $policy_data['premium'];

        if($hr_amount_status == "yes"){
            $FinalPremium = $FinalPremium - $total_hr_amount;
        }

        // $FinalPremium = $FinalPremium - $total_hr_amount;

        $ProductInfo = '';
				
		

        $payment_link = $this->payment_redirect($lead_id, $CustomerName, $Email, $PhoneNo, $FinalPremium, $ProductInfo);

        if ($payment_link && $payment_link['Status'] == "1") {

            $return = ["status"=>1,"description"=>"success", "msg" => "", "url" => $payment_link['PaymentLink']];

        }else{
            if(empty($payment_link)){
                $payment_link = "Could not create payment link, please try again.";
            }
            $return = ["status"=>0,"description"=>"failed", "msg" => $payment_link, "url" => ""];
        }

        echo json_encode($return);




    }


    public function group_customer_modify_submit_otp(){

        extract($this->input->post(null, true));

        if(isset($ghd_2)){
            if($ghd_2 == "yes"){
                $whatsapp_flag = '1';
            }else{
                $whatsapp_flag = '0';
            }
        }else{
            $whatsapp_flag = '0';
        }
       

        // var_dump($_POST);exit;

        $update_policy = 
        $this->db->set('whatsapp_flag', $whatsapp_flag);
        $this->db->where('lead_id', $lead_id);
        $this->db->update('group_mod_create');

        // echo $this->db->last_query();exit;


        $policy_data = $this
        ->db
        ->select("proposer_mobile,proposer_email")
        ->from("group_mod_create a")
        ->where("lead_id", $lead_id)
        ->get()
        ->row_array();

        // print_r($policy_data);exit;

        $otp = random_string('numeric', 6);
        //remove following line before production movement
        $otp = "1234";
        $mob_no = $policy_data['proposer_mobile'];
        $email = $policy_data['proposer_email'];
        $this->session->set_userdata('otp_code','1234');
		/*prod uncomment code*/
        //$this->session->set_userdata('otp_code',$otp);   
		
		/**
		* Alert Mode 3 : Send OTP in SMS & Email
		* Alert Mode 2 : Send SMS Only
		* Alert Mode 1 : Send Email Only
		*
		**/
		$alertMode = 3;
		
		$parameters =[
		"RTdetails" => [
       
							"PolicyID" => '',
							"AppNo" => 'HD100017934',
							"alertID" => 'A533',
							"channel_ID" => 'ABHI Diamond',
							"Req_Id" => 1,
							"field1" => '',
							"field2" => '',
							"field3" => '',
							"Alert_Mode" => $alertMode,
							"Alertdata" => 
								[
									"mobileno" => $mob_no,
									"emailId" => $email,
									"AlertV1" => $otp,
									"AlertV2" => '',
								]

						]

		];

		$parameters = json_encode($parameters);
		 
		$curl = curl_init();
				
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "http://10.1.226.32/ABHICL_ClickPSS/Service1.svc/click",
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


		$data = ["lead_id" => $lead_id, "req" => $parameters,"res"=>json_encode($response), "type" => "otp_data"];
        $this->db->insert('group_mod_logs', $data);

        $update_otp = 
                $this->db->set('otp', $otp);
                $this->db->where('lead_id', $lead_id);
                $this->db->update('group_mod_create');
				
		$return = ["status"=>1,"description"=>"success"];

        echo json_encode($return);




    }


    function customer_cancelled(){
        extract($this->input->post(null, true));

        $update_policy = 
        $this->db->set('status', 0);
        $this->db->set('is_cancelled_by_customer', "yes");
        $this->db->where('lead_id', $lead_id);
        $this->db->update('group_mod_create');

        $return = ["status"=>"success","description"=>"lead cancelled","url"=>"https://www.adityabirlacapital.com/healthinsurance/homepage"];

        echo json_encode($return);
    }


    public function validate_otp(){
        extract($this->input->post(null, true));

        $policy_data = $this
        ->db
        ->select("*")
        ->from("group_mod_create")
        ->where("lead_id", $lead_id)
        ->get()
        ->row_array();

         //get HR Amount
        $get_total_hr_amount = $this
        ->db
        ->select("SUM(Hr_Amount) as total_hr_amount")
        ->from("group_mod_members")
        ->where("lead_id", $lead_id)
       //  ->where("Relation", "Self")
       //  ->where("coi_number LIKE '%GHI%'")
        ->group_by("NAME")
        ->get()
        ->result_array();

        $total_hr_amount = 0;

        foreach($get_total_hr_amount as $single_hr_amount){
           $total_hr_amount += $single_hr_amount['total_hr_amount'];
        }

         $update_policy = 
         $this->db->set('hr_amount_status', $hr_amount_status);
         $this->db->where('lead_id', $lead_id);
         $this->db->update('group_mod_create');
 

        $db_otp = $policy_data['otp'];
        $CustomerName = $policy_data['proposer_name'];
        $Email = $policy_data['proposer_email'];
        $PhoneNo = $policy_data['proposer_mobile'];
        $FinalPremium = $policy_data['premium'];

        // $FinalPremium = $FinalPremium - $total_hr_amount;
        if($hr_amount_status == "yes"){
            $FinalPremium = $FinalPremium - $total_hr_amount;
        }

        $ProductInfo = '';

        if($db_otp == $pos_otp){
            
            $payment_link = $this->payment_redirect($lead_id, $CustomerName, $Email, $PhoneNo, $FinalPremium, $ProductInfo);
            // print_pre($payment_link);exit;

            if ($payment_link && $payment_link['Status'] == "1") {

                $return = ["status"=>1,"description"=>"success", "msg" => "", "url" => $payment_link['PaymentLink']];

            }else{
                if(empty($payment_link)){
                    $payment_link = "Could not create payment link, please try again.";
                }
                $return = ["status"=>0,"description"=>"failed", "msg" => $payment_link, "url" => ""];
            }
           
        }else{
            $return = ["status"=>0,"description"=>"failed", "msg" => "OTP did not match!", "url" => ""];
        }

        echo json_encode($return);
    }


    public function payment_redirect($lead_id, $CustomerName, $Email, $PhoneNo, $FinalPremium, $ProductInfo)
    {

        $product_name_data =  $this->db->select("product_name,coi_number")
        ->from("group_mod_create")
        ->where("lead_id", $lead_id)
        ->get()
        ->row_array();

        $coi_numbers = $product_name_data['coi_number'];
        $coi_numbers = explode(',',$coi_numbers);
        $single_coi = $coi_numbers[0];

        $response = $this->renewal_do_m->tele_renewal_group_do($single_coi);
        $djson = json_decode($response, TRUE);
       if(empty($djson)){
             return "could not get details, please try again!";
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
        
        if(strtolower($Renewed_Flag) != 'no' && strtolower($Renewable_Flag) != 'yes' ){
            return 'Policy is not renewable.';
            exit;
        }

        $lead_id_encrypt = encrypt_decrypt_password($lead_id, 'E');

        $productname = $product_name_data['product_name'];

        $Source = "AX";
        $Vertical = "AXATGRP";
        $PaymentMode = "PP";
        $ReturnURL = base_url('group_customer_modify_pd/' . $lead_id_encrypt);
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
        curl_close($c);


        $fdata = [
            'lead_id' => $lead_id,
            'req' => json_encode($data),
            'res' => $result,
            'type' => "payment_request_post",
        ];

        $this->db->insert('group_mod_logs', $fdata);


        $result = json_decode($result, true);


        return $result;
    }



    public function group_customer_modify_pd_old($lead_id_encrypt){
        $lead_id = encrypt_decrypt_password($lead_id_encrypt, 'D');

        $encrypted = $this->input->post('RESPONSE');

        $update_policy = $this->db->set('status', 0);
        $this->db->where('lead_id', $lead_id);
        $this->db->update('group_mod_create');

        $get_data =  $this->db->select("*")
        ->from("group_mod_create")
        ->where("lead_id", $lead_id)
        ->get()
        ->row_array();

        if($get_data['payment_status'] ==  'Payment Received' || $get_data['payment_status'] == 'success'){
           
            if(!empty($get_data['new_certificate_number']) || $get_data['new_certificate_number'] != ""){
                $data['certs'] = $get_data['new_certificate_number'];
                $data['status'] = 'success';
            }else{
                $data['certs'] = "";
                $data['status'] = 'failed';
            }
            // print_r($data);exit;
            $this->load->telesales_template("thankyou_grp_renewal_cust_modi", $data);
        }
            
        else if($encrypted)
        {
            $decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);
            $post_data = json_decode($decrypted,true);
            
            extract($post_data);
            $fdata = [
                'lead_id' => $lead_id,
                'res' => $decrypted,
                'type' => "payment_response_post",
            ];
            $this->db->insert('group_mod_logs', $fdata);
            
            if($TxStatus && ($TxMsg == 'Payment Received' || $TxMsg == 'success') && $PaymentStatus == 'PR'){

                $update_policy = 
                $this->db->set('payment_status', 'success');
                $this->db->where('lead_id', $lead_id);
                $this->db->update('group_mod_create');


                $issuance = $this->renewal_do_m->group_customer_modify_issuance($lead_id);

                if($issuance == "false"){
                    $data['certs'] = "";
                    $data['status'] = 'failed';
                }else{
                    $data['certs'] = $issuance;
                    $data['status'] = 'success';
                }

                $this->load->telesales_template("thankyou_grp_renewal_cust_modi", $data);


            }else {
                echo 'Payment Failed';
            }
        }else {
            echo 'Something went wrong.';
        }
        
    }

    public function group_customer_modify_pd($lead_id_encrypt){
        $lead_id = encrypt_decrypt_password($lead_id_encrypt, 'D');

        $encrypted = $this->input->post('RESPONSE');

        // $update_policy = $this->db->set('status', 0);
        // $this->db->where('lead_id', $lead_id);
        // $this->db->update('group_mod_create');

        $get_data =  $this->db->select("*")
        ->from("group_mod_create")
        ->where("lead_id", $lead_id)
        ->get()
        ->row_array();


        $this->db->select('GROUP_CONCAT(gp.fr_id ORDER BY gp.fr_id) as all_fr_ids,gp.lead_id,gp.is_adult,gp.policy_detail_id,gp.policy_sub_type_id,gp.coi_number');
        $this->db->from('group_mod_members as gp');
        $this->db->where('gp.lead_id',$lead_id);
        $this->db->group_by('gp.coi_number');
        $this->db->order_by('gp.fr_id');

        $all_members=$this->db->get()->result_array();
        
        // print_pre($all_members);exit;

        $newcoi="SELECT certificate_number from group_mod_api_response where lead_id='$lead_id' GROUP BY certificate_number";
        
        $total_newcoi=$this->db->query($newcoi)->result_array();

        // print_pre($all_members);exit;        

        $data_policy=[];

        $icheck=1;
        foreach ($all_members as $key => $value) {
            // if($value['policy_sub_type_id']){
                $newcheckk=array();

                $check=$value['all_fr_ids'];

                $check=explode(',',$check);

                foreach ($check as $newcheck){

                        if($newcheck==0){
                            $label=" Self";
                        }

                        if($newcheck==1){
                            $label=" Spouse";
                        }

                        if($newcheck==2){
                            // $label="Son".$icheck;
                            $label=" Kid".$icheck;
                            $icheck++;
                        }

                        if($newcheck==3){
                            // $label="Daughter".$icheck;
                            $label=" Kid".$icheck;
                            $icheck++;
                        }
                        
                        array_push($newcheckk,$label);
                }
            
                // print_pre($newcheckk);exit;
                $newcheckk=implode(',',$newcheckk);


                $data['members'][$key] = ['members'=>$newcheckk,'lead_id'=>$value['lead_id'],'policy_sub_type_id'=>$value['policy_sub_type_id'],'old_coi_number'=>$value['coi_number'],'new_coi_number'=>$total_newcoi[$key]['certificate_number']];
            // }
        }

        // print_pre($data);exit;


        if($get_data['payment_status'] ==  'Payment Received' || $get_data['payment_status'] == 'success'){
           
            if(!empty($get_data['new_certificate_number']) || $get_data['new_certificate_number'] != ""){
                $data['certs'] = $get_data['new_certificate_number'];
                $data['status'] = 'success';
            }else{
                $data['certs'] = "";
                $data['status'] = 'failed';
            }
            // print_pre($data);exit;
            $this->load->telesales_template_new_design("group_customer_thank_you",$data);            

        }
            
        else if($encrypted)
        {
            $decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);
            $post_data = json_decode($decrypted,true);
            
            extract($post_data);
            $fdata = [
                'lead_id' => $lead_id,
                'res' => $decrypted,
                'type' => "payment_response_post",
            ];
            $this->db->insert('group_mod_logs', $fdata);
            
            if($TxStatus && ($TxMsg == 'Payment Received' || $TxMsg == 'success') && $PaymentStatus == 'PR'){

                $update_policy = 
                $this->db->set('payment_status', 'success');
                $this->db->where('lead_id', $lead_id);
                $this->db->update('group_mod_create');


                $issuance = $this->renewal_do_m->group_customer_modify_issuance($lead_id);

                if($issuance == "false"){
                    $data['certs'] = "";
                    $data['status'] = 'failed';
                }else{
                    $data['certs'] = $issuance;
                    $data['status'] = 'success';
                }
                // print_pre($data);exit;
                $this->load->telesales_template_new_design("group_customer_thank_you", $data);


            }else {
                echo 'Payment Failed';
            }
        }else {
            echo 'Something went wrong.';
        }
        
    }


    public function get_family_construct(){

        extract($this->input->post(null, true));
        $policy_data = $this
        ->db
        ->select("*")
        ->from("group_mod_create a")
        ->where("lead_id", $lead_id)
        ->get()
        ->row_array();

        $database = $policy_data['database'];

        if($database == 'axis_retail'){
            $this->db=$this->load->database('axis_retail',TRUE);  
        }
        $get_policy_detail_id = $this
        ->db
        ->select("b.suminsured_type,b.policy_detail_id")
        ->from("product_master_with_subtype a, employee_policy_detail b")
        ->where("a.id = b.product_name")
        ->where("a.product_code", $product_code)
        ->where("a.policy_subtype_id", "1")
        ->get()
        ->row_array();
        // echo $this->db->last_query();exit;
        // $sumInsured = $member_data[0]['SumInsured'];
        $table = $get_policy_detail_id['suminsured_type'];
        $policyNo =$get_policy_detail_id['policy_detail_id'];

        // echo $table;exit;

        $get_family_construct = $this->renewal_do_m->get_family_construct($sum_insured, $table,$policyNo);

        $family_construct_select = array();
        foreach($get_family_construct as $single_fc){
            array_push($family_construct_select, $single_fc['family_type']);
        }
        if($database == 'axis_retail'){
            $this->db=$this->load->database('axis_retail',false);  
            $this->load->database();
              
        }
        echo json_encode($family_construct_select);

    }



    public function get_mem_details(){

        extract($this->input->post(null, true));

        $get_member = $this
        ->db
        ->select("Name")
        ->from("group_mod_members")
        ->where("id", $member_id)
        ->get()
        ->row_array();

        $Name = $get_member["Name"];
        $Name = preg_replace('/\s+/', ' ', $Name);

        $Name = explode(" ",$Name);
        $first_name = explode(" ",$Name[0]);
        $last_name = explode(" ",$Name[1]);

        $output = ["first_name" => $first_name, "last_name" => $last_name];

        echo json_encode($output);

    }


    public function update_mem_submit(){

        extract($this->input->post(null, true));

        $get_member = $this
        ->db
        ->select("Name")
        ->from("group_mod_members")
        ->where("id", $update_member_id)
        ->get()
        ->row_array();

        $Name = $get_member["Name"];

        $new_name = trim($update_first_name)." ".trim($update_last_name);

        $update_policy = 
        $this->db->set('Name', $new_name);
        $this->db->where('lead_id', $lead_id);
        $this->db->where('Name', $Name);
        $this->db->update('group_mod_members');

        // echo $this->db->last_query();exit;

        $output = ["status" => "success", "description" => "Member updated successfully"];

        echo json_encode($output);



    }

    public function dobs($mydob){
        
        $dob=new DateTime($mydob);
        $today=new DateTime(date('Y-m-d'));
        $diff = $today->diff($dob);
        return $diff->y;
    }


    public function group_renewal_get_premium(){
        extract($this->input->post(null, true));
        $get_product = $this
        ->db
        ->select("product_code,database")
        ->from("group_mod_create")
        ->where("lead_id", $lead_id)
        ->get()
        ->row_array();

        $database = $get_product['database'];

        

        
        if($database == 'axis_retail'){
            $this->db=$this->load->database('axis_retail',TRUE);  
        }
        $product_code = $get_product['product_code'];
        $product=$this->renewal_do_m->product($product_code);
        if($database == 'axis_retail'){
            $this->db=$this->load->database('axis_retail',false);  
            $this->load->database();
              
        }

        $employeedob=$this->renewal_do_m->employeesalldob($lead_id);
        foreach($employeedob as $alldob){
            $fdob[]=$alldob['DoB'];
        }   
        

        $self=$fdob[0];
        $spouse=$fdob[1];

        $selfage = $this->dobs($self);
        $spouseage=$this->dobs($spouse);        

        $age=max($selfage,$spouseage);
        $minage=min($selfage,$spouseage);

        $this->allage=array('0'=>$age,'1'=>$minage);

        $gpolicy = "";

        foreach($product as $policy_detail_id){
            $gpolicy .= '"'.$policy_detail_id['policy_detail_id'].'",';

        }
        
        $gpolicy=rtrim($gpolicy,",");

        // print_r($gpolicy);exit;

        // $family_construct = $this-> get_family_construct_single_lead($lead_id);

       

        $member_policy='';


            $policy_sub_type = "no";

            if($database == 'axis_retail'){
                $this->db=$this->load->database('axis_retail',TRUE);  
                  
            }
            $policy_detail=$this->renewal_do_m->policy_details($gpolicy,$policy_sub_type);
            if($database == 'axis_retail'){
                $this->db=$this->load->database('axis_retail',false);  
                $this->load->database();
                  
            }
            

            // print_pre($policy_detail);exit;
           
            $i=0;
            $j=0;
            $k=0;
            $premium=0;
            $premium_breakup=[];

            foreach($policy_detail as $policy_detail_id){
                

                $sum_insured = $this
                ->db
                ->select("SumInsured")
                ->from("group_mod_members")
                ->where("lead_id", $lead_id)
                ->where("policy_detail_id", $policy_detail_id['policy_detail_id'])
                ->get()
                ->row_array();

                $sum_insured = $sum_insured['SumInsured'];

                // $member_policy=$this->family_construct_wise($sum_insured,$family_construct,"393",$age,$j); 
                // echo "GPA IS ".$member_policy;exit;

                $family_construct_adult = $this
                ->db
                ->select("count(is_adult) as adult_count")
                ->from("group_mod_members")
                ->where("lead_id", $lead_id)
                ->where("is_adult", "yes")
                ->where("policy_detail_id", $policy_detail_id['policy_detail_id'])
                ->get()
                ->row_array();
        
                $family_construct_adult = $family_construct_adult['adult_count'].'A';
        
                $family_construct_kid = $this
                ->db
                ->select("count(is_adult) as kid_count")
                ->from("group_mod_members")
                ->where("lead_id", $lead_id)
                ->where("is_adult", "no")
                ->where("policy_detail_id", $policy_detail_id['policy_detail_id'])
                ->get()
                ->row_array();
        
                if($family_construct_kid){
                      $family_construct_kid = $family_construct_kid['kid_count'].'K';
                }else{
                    $family_construct_kid = "";
                }
        
                if($family_construct_kid['kid_count'] != "0"){
                    $family_construct = $family_construct_adult.'+'.$family_construct_kid;
                }else{
                    $family_construct = $family_construct_adult;
                }
               
                // echo $family_construct;exit;

                if($database == 'axis_retail'){
                    $this->db=$this->load->database('axis_retail',TRUE);  
                }

                if($policy_detail_id['suminsured_type']=='family_construct_age'){
                    $member_policy=$this->family_construct_age_wise($sum_insured,$family_construct,$policy_detail_id['policy_detail_id'],$age,$i,$product_code); 
                    $i++;
                }

                if($policy_detail_id['suminsured_type']=='family_construct'){
                    $member_policy=$this->family_construct_wise($sum_insured,$family_construct,$policy_detail_id['policy_detail_id'],$age,$j); 
                    $j++;
                }

                if($policy_detail_id['suminsured_type']=='memberAge'){
                    $member_policy=$this->family_construct_member_wise($sum_insured,$family_construct,$policy_detail_id['policy_detail_id'],$age,$k); 
                    $k++;
                }
                // $premium_breakup[$policy_detail_id['policy_detail_id']]=$member_policy;
                if($policy_detail_id['policy_sub_type_id'] == "1"){
                    $policy_sub_name = "Group Health Insurance";
                }
                if($policy_detail_id['policy_sub_type_id'] == "2"){
                    $policy_sub_name = "Group Personal Accident";
                }
                if($policy_detail_id['policy_sub_type_id'] == "3"){
                    $policy_sub_name = "Group Critical Insurance";
                }
                if($policy_detail_id['policy_sub_type_id'] == "8"){
                    $policy_sub_name = "Group Protect";
                }

                $premium_breakup_single = ["name" => $policy_sub_name, "sum_insured" => $sum_insured, "premium" => $member_policy];
                array_push($premium_breakup, $premium_breakup_single);
                $premium=$premium+$member_policy;

                if($get_product['database'] == 'axis_retail'){
                    $this->db=$this->load->database('axis_retail',false);  
                }

            }


        // print_pre($premium_breakup);
        if($database == 'axis_retail'){
            $this->db=$this->load->database('axis_retail',false);  
            $this->load->database();
              
        }

        $update_premium_breakup = "";
        $update_total_premium = 0;
        foreach($premium_breakup as $single_breakup){
            $update_premium_breakup .= $single_breakup['premium'].',';
            $update_total_premium += $single_breakup['premium'];
        }

        $update_premium_breakup = rtrim($update_premium_breakup,",");
        
        $update_all_premium = 
        $this->db->set('premium_break', $update_premium_breakup);
        $this->db->set('premium', $update_total_premium);
        $this->db->where('lead_id', $lead_id);
        $this->db->update('group_mod_create');

        echo json_encode($premium_breakup);
    }



    public function family_construct_wise($sum_insured,$family_construct,$policy_id,$age,$i){

        if($i==0){
            $newage=$this->allage[0];
        }else if($i==1){
            $newage=$this->allage[1];

        }

        $getallfamily=explode("+",$family_construct);
        if($i==0){
            $final_construct=$family_construct;
        }else if($i==1){
            $final_construct=$getallfamily[0];
        }
        
        $premium=$this->db
                        ->select('PremiumServiceTax')
                        ->from('family_construct_wise_si')
                        ->where('sum_insured',$sum_insured)
                        ->where('family_type',$final_construct)
                        ->where('policy_detail_id',$policy_id)
                        ->get()
                        ->row_array();

        // print_r($this->db->last_query());
        return $premium['PremiumServiceTax'];
        
    }


    public function family_construct_age_wise($sum_insured,$family_construct,$policy_id,$age,$i,$product_code){

        if($i==0){
            $newage=$this->allage[0];
        }else if($i==1){
            $newage=$this->allage[1];

        }

        $getallfamily=explode("+",$family_construct);
        if($i==0){
            $final_construct=$family_construct;
        }else if($i==1){
            $final_construct=$getallfamily[0];
        }

        // print_r($getallfamily);
        // print_r($getallfamily[0]);
        if($product_code=='R05'){

            $getdetails=$this->db
            ->select('age_group,PremiumServiceTax')
            ->from('family_construct_age_wise_si')
            ->where('family_type',$final_construct)
            ->where('sum_insured',$sum_insured)
            ->where('policy_detail_id',$policy_id)
            ->get()
            ->result_array();
            // echo"\n";
    
            foreach($getdetails as $agetdetails){
    
            $getage=explode("-",$agetdetails['age_group']);  
            if($newage>$getage[0]&&$newage<$getage[1]){
            $final=$agetdetails['PremiumServiceTax'];            
            }
    
            }
    

        }else{

            if(empty($this->deductable)){

                $getdetails=$this->db
                ->select('age_group,EW_PremiumServiceTax')
                ->from('family_construct_age_wise_si')
                ->where('family_type',$final_construct)
                ->where('sum_insured',$sum_insured)
                ->where('policy_detail_id',$policy_id)
                ->get()
                ->result_array();
                // echo"\n";
    
            }else{


            $getdetails=$this->db
            ->select('age_group,EW_PremiumServiceTax')
            ->from('family_construct_age_wise_si')
            ->where('family_type',$final_construct)
            ->where('sum_insured',$sum_insured)
            ->where('deductable',$this->deductable)
            ->where('policy_detail_id',$policy_id)
            ->get()
            ->result_array();
            // echo"\n";
            
            }
            
            foreach($getdetails as $agetdetails){
    
                $getage=explode("-",$agetdetails['age_group']);  
                if($newage>$getage[0]&&$newage<$getage[1]){
                $final=$agetdetails['EW_PremiumServiceTax'];            
                }
        
                }
                
    
        }
        return $final;        
    }


    public function family_construct_member_wise($sum_insured,$family_construct,$policy_id,$age,$i){

            if($i==0){
                $newage=$this->allage[0];
            }else if($i==1){
                $newage=$this->allage[1];
    
            }    
        
        $getdetails=$this->db
                            ->select('policy_age,premium')
                            ->from('policy_creation_age')
                            ->where('sum_insured',$sum_insured)
                            ->where('policy_id',$policy_id)
                            ->get()
                            ->result_array();

        foreach($getdetails as $agetdetails){

            $getage=explode("-",$agetdetails['policy_age']);  
            if($newage>$getage[0]&&$newage<$getage[1]){
                $final=$agetdetails['premium'];            
            }

        }
        

        return $final;            

    }    


    public function update_sum_insured(){
        extract($this->input->post(null, true));

        $update_policy = 
        $this->db->set('SumInsured', $sum_insured);
        $this->db->where('lead_id', $lead_id);
        $this->db->update('group_mod_members');

        $output = ["status" => "success", "description" => "Sum insured updated successfully"];

        echo json_encode($output);

    }


    public function check_is_editable(){
        extract($this->input->post(null, true));

        $policy_data = $this
        ->db
        ->select("is_editable")
        ->from("group_mod_create a")
        ->where("lead_id", $lead_id)
        ->get()
        ->row_array();

        $is_editable = $policy_data['is_editable'];

        echo $is_editable;

    }


    public function nominee_update(){

        extract($this->input->post(null, true));

        $nominee_name = $nominee_first_name.' '.$nominee_last_name;

        $update_policy = 
        $this->db->set('nominee_relation', $nominee_relation);
        $this->db->set('nominee_name', $nominee_name);
        $this->db->set('nominee_contact', $nominee_contact);
        $this->db->where('lead_id', $lead_id);
        $this->db->update('group_mod_create');

        $output = ["status" => "success", "description" => "Nominee details updated!"];

        echo json_encode($output);
        
    }

    public function check_validation_age($policy_detail_id, $relation_id, $age, $age_type){

        // echo $policy_detail_id." ";
        // echo $relation_id." ";
        // echo $age." ";
        // echo $age_type." ";

        // exit;

        $check_min_max = $this->db->select("*")->from("policy_age_limit,master_family_relation")->where("policy_detail_id", $policy_detail_id)->where("policy_age_limit.relation_id", $relation_id)
        ->where("master_family_relation.fr_id = policy_age_limit.relation_id")
        ->get()->row_array();

        $min_age_allow = $check_min_max['min_age'];
        $max_age_allow = $check_min_max['max_age'];

        // echo $policy_detail_id." ";
        // echo $min_age_allow.' ';
        // echo $max_age_allow.' ';
        // echo $check_min_max['fr_name'];

        if($age_type == 'days' && ($relation_id == 2 || $relation_id == 3))
			{
				if($age < 91)
				{
					$output = ["status" => "failed","description" => $check_min_max['fr_name']." age should be greater than 91 days"];			
			
				}
                else{
                    $output = ["status" => "success","description" =>""];
                }

                if($age > $check_min_max['max_age']){

                    $output = ["status" => "failed","description" => $check_min_max['fr_name']." age should be less than ".$check_min_max['max_age']." years"];	

                }

                else{
                    $output = ["status" => "success","description" =>""];
                }


            }

            if($age_type == "years")
			{
				if (!($age >= $check_min_max['min_age'] && $age <= $check_min_max['max_age']))
				{
					if($check_min_max['min_age'] == 0){
						$check_min_max['min_age'] = "91 days";
					}

					$output = ["status" => "failed", "description" => "Min age for ".$check_min_max['fr_name']. " is ".$check_min_max['min_age']." and max age is ".$check_min_max['max_age']. " years" ];

                }else{
                    $output = ["status" => "success","description" =>""];
                }
            }

            return $output;


    }


    public function group_renewal_phase2_cron(){

         $policy_data = $this
        ->db
        ->select("lead_id,payment_status,coi_number")
        ->from("group_mod_create")
        ->where('(LOWER(`payment_status`) != "success" OR payment_status IS NULL)')
        ->where("status = '1'")
        ->where("disposition_master_id != '51'")
        ->where('(LOWER(`renewed_from_other_mode`) != "yes" OR renewed_from_other_mode IS NULL)')
        ->where("DATE(created_at) > '2021-12-29'")
        // ->limit("1")
        ->get()
        ->result_array();

        // print_pre($policy_data);exit;

        foreach($policy_data as $single_lead){
        
                $coi_number = $single_lead['coi_number'];
                $coi_number = explode(',',$coi_number);
                $coi_number = $coi_number[0];

                $data_request = array(
                    "Lead_Id" => "",
                    "master_policy_number" => "",
                    "certificate_number" => $coi_number,
                    "dob" => "",
                    "proposer_mobileNumber" => "",
                );
                $data_request = json_encode($data_request);

                $response = $this->renewal_do_m->tele_renewal_group_do($coi_number);

                $fdata = [
                    'lead_id' => $single_lead['lead_id'],
                    'req' =>  $data_request ,
                    'res' =>  $response ,
                    'type' => "cron_logs",
                ];
        
                $this->db->insert('group_mod_logs', $fdata);
                    
                $djson = json_decode($response, TRUE);

            
                $ErrorCode = $djson['error'][0]['ErrorCode'];
                $ErrorMessage = $djson['error'][0]['ErrorMessage'];


                if(empty($djson)){
                    $output = ['error' => ['ErrorCode' => '0010', 'ErrorMessage' => 'Failed', 'output_msg' => 'could not get details, please try again!']];
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
                
                // $Renewed_Flag = 'yes';

                if(strtolower($Renewed_Flag) == 'yes'){
                    $double_check = $this
                    ->db
                    ->select("payment_status,status")
                    ->from("group_mod_create")
                    ->where("lead_id", $single_lead['lead_id'])
                    ->get()
                    ->row_array();


                    if(strtolower($double_check['payment_status']) != 'success' && $double_check['status'] == '1'){

                        $check_other_mode = $this
                        ->db
                        ->select("id")
                        ->from("group_mod_logs")
                        ->where_in("type", ['payment_request_post','payment_response_post'])
                        ->where("lead_id", $single_lead['lead_id'])
                        ->get()
                        ->result_array();

                        // print_pre($check_other_mode);exit;

                        if(count($check_other_mode) == 0){
                            $this->db->set('renewed_from_other_mode', 'yes');
                            $this->db->set('disable_link', 'yes');
                            $this->db->set('updated_at', date("Y-m-d H:i:s"));
                            $this->db->where("lead_id", $single_lead['lead_id']);
                            $this->db->update('group_mod_create');
                        }

                    }

                }




        }

    }


}


