<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once(APPPATH."controllers/MY_TelesalesSessionCheck.php");

class Create_proposal extends MY_TelesalesSessionCheck
{

    public function __construct()
    {
        parent::__construct();
		
		$this->load->model("Telesales/create_proposal_m", "obj_home", true);
		$this->load->model("Logs_m", "Logs_m", true);
	
		if (!$this->session->userdata('telesales_session')) 
		{
            redirect('login');
        }

	
        if($this->input->get('leadid', TRUE)){
           // echo "in";exit;
            $lead_id = encrypt_decrypt_password($_REQUEST['leadid'],'D');
            $res = $this->db->select('employee_details.emp_id,employee_details.product_id,product_master_with_subtype.policy_parent_id')
 		    ->from('employee_details,product_master_with_subtype')
                    ->where('employee_details.product_id = product_master_with_subtype.product_code')
                    ->where('employee_details.lead_id',$lead_id)
                    ->get()->row_array();
//echo $this->db->last_query();exit;
	   
            if(!empty($res)){            
                $_SESSION['telesales_session']['emp_id'] = $res['emp_id'];
                $_SESSION['telesales_session']['product_code'] = $res['product_id'];
                $_SESSION['telesales_session']['parent_id'] = $res['policy_parent_id'];
            }else{
                echo "No Data Found!";exit;
            } 
        }

		
		
		$telSalesSession = $this->session->userdata('telesales_session');
		$this->agent_id = encrypt_decrypt_password($telSalesSession['agent_id'],'D');
		$this->emp_id = $telSalesSession['emp_id'];
		$this->parent_id = $telSalesSession['parent_id'];
		$this->db->query("SET GLOBAL  sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        //print_pre($_SESSION);exit;
	
    }

    //update ref no on change of plan name dedupe changes

    public function tele_update_ref_no(){
        $product_id = $_POST['product_id'];
        $emp_id = $this->emp_id;
		//echo $emp_id;exit;
        if($product_id != '' && $emp_id != ''){
            $this->db->where("emp_id", $emp_id);
            $this->db->update("employee_details", array("product_id" => $product_id));
            $res =  $this->update_ref_no($emp_id,$product_id);
            if($res){
                echo "1";exit;
            }
        }else{
            echo "0";
        }
    }

  //dedupe logic update unique ref no

    public function update_ref_no($emp_id,$product_id){
        $res = $this->db->query("select unique_ref_no,emp_firstname,emp_lastname,mob_no,bdate from employee_details where emp_id = '".$emp_id."'")->row_array();
        $resArr = $this->db->query("select unique_ref_no,lead_id,product_id,emp_id from employee_details where product_id='".$product_id."' AND emp_firstname = '".$res['emp_firstname']."' AND emp_lastname = '".$res['emp_lastname']."' AND mob_no = '".$res['mob_no']."' AND bdate = '".$res['bdate']."' AND emp_id != ".$emp_id." ")->row_array();
        //echo $this->db->last_query();exit;
        if($resArr['unique_ref_no'] == ''){
            
            $unique_ref_no = $this->generate_unique_ref_no();
            
        }else{

            $unique_ref_no = $resArr['unique_ref_no'];
        }
        //echo $unique_ref_no;exit;
                //check if any other leads are having same name , dob, product id & mob no then update ref no for all leads
        $check_unique_ref_no = $this
                            ->db
                            ->query("select unique_ref_no,lead_id,product_id,emp_id from employee_details where product_id='".$product_id."' AND emp_firstname = '".$res['emp_firstname']."' AND emp_lastname = '".$res['emp_lastname']."' AND mob_no = '".$res['mob_no']."' AND bdate = '".$res['bdate']."' ")->result_array();
      // print_pre($check_unique_ref_no);exit;              
        //if unique_ref_no is blank update same
        if(!empty($check_unique_ref_no)){
            foreach ($check_unique_ref_no as $key => $value) {
               // if($value['unique_ref_no'] == ''){
                    // echo $emp_id;exit;
                    $this->db->where("emp_id", $value['emp_id']);
                    $this->db->update("employee_details", array("unique_ref_no" => $unique_ref_no));
                    //echo $this->db->last_query();exit;
                //}
            }                           
        }

        return true;
    }

 /*dedupe logic changes function to generate unique reference no*/

    public function generate_unique_ref_no()
    {
        $unique_ref_no = time();
        $EmployeeData = $this
            ->db
            ->get_where("employee_details", array(
            "unique_ref_no" => $unique_ref_no
        ))->row_array();
        if (!empty($EmployeeData))
        {
            $this->generate_unique_ref_no();
        }
        return $unique_ref_no;
    }

	public function tele_update_ref_no_vl(){
        $emp_id =$_POST['emp_id'];
		$emp_id = encrypt_decrypt_password($emp_id,'D');

		$output_query = $this
		->db
		->query("SELECT ed.product_id, ed.unique_ref_no, ed.emp_id
		FROM 
		employee_details ed, family_relation fr, 
		employee_policy_member epm
		WHERE ed.emp_id = fr.emp_id 
		AND fr.family_relation_id = epm.family_relation_id 
		AND ed.emp_id = '$emp_id' GROUP BY epm.family_relation_id")->row_array();

		if(!empty($output_query)){
			$unique_ref_no = $output_query['unique_ref_no'];
			$product_id = $output_query['product_id'];
			if(empty($unique_ref_no)){
				$res =  $this->update_ref_no($emp_id,$product_id);
				if($res){
					echo "success";
				}
			}
			else{
				
			}
			
		}
		
		else{
			
		}

	}

	//dedupe changes
    function gpa_policy_purchased_for_self_bkkkkkkkk(){
        $emp_id = $this->emp_id;
        $this->db->select("product_id,unique_ref_no");
        $data = $this->db->get_where("employee_details",array("emp_id" => $emp_id))->row_array();   
        if($data['product_id'] == 'R11'){
            $cond = "AND pm.policy_detail_id IN (".HEALTHPROXL_GPA.")"; 
        }else if($data['product_id'] == 'T03'){
            $cond = "AND pm.policy_detail_id IN (".TELE_HEALTHPROINFINITY_GPA.")"; 
        }else{
            $cond = "";
        }     
        //check policy already purchased for same unique ref no
        $res = $this->db->query("select pm.policy_mem_sum_insured,pm.familyConstruct from proposal as p,employee_details as ed,payment_details as pd,proposal_member AS pm where pm.proposal_id = p.id AND p.emp_id = ed.emp_id and p.id = pd.proposal_id and p.status in('Success','Payment Received') and ed.unique_ref_no = '" . $data['unique_ref_no'] . "' and ed.product_id = '" . $data['product_id'] . "' and ed.emp_id != '" . $emp_id . "' AND pm.fr_id = 0 ".$cond." order by pm.policy_mem_sum_insured desc")->result_array();
        // echo $this->db->last_query();exit;                
        if(!empty($res)){
            echo json_encode(["flag"=>1,"sum_insure"=> $res[0]['policy_mem_sum_insured']]);
        }else{
           echo json_encode(["flag"=>0,"sum_insure"=> 0]); 
        }
    }//dedupe changes
    function gpa_policy_purchased_for_self(){
        $emp_id = $this->emp_id;
        $this->db->select("product_id,unique_ref_no");
        $data = $this->db->get_where("employee_details",array("emp_id" => $emp_id))->row_array();   
        if($data['product_id'] == 'R11'){
            $cond = "AND pm.policy_detail_id IN (".HEALTHPROXL_GHI_GPA.")"; 
        }else if($data['product_id'] == 'T03'){
            $cond = "AND pm.policy_detail_id IN (".TELE_HEALTHPROINFINITY_GHI_GPA.")"; 
        }else{
            $cond = "";
        }     
        //check policy already purchased for same unique ref no
        $res = $this->db->query("select pm.policy_mem_sum_insured,pm.familyConstruct from proposal as p,employee_details as ed,payment_details as pd,proposal_member AS pm where pm.proposal_id = p.id AND p.emp_id = ed.emp_id and p.id = pd.proposal_id and p.status in('Success','Payment Received') and ed.unique_ref_no = '" . $data['unique_ref_no'] . "' and ed.product_id = '" . $data['product_id'] . "' and ed.emp_id != '" . $emp_id . "' AND pm.fr_id = 0 ".$cond." order by pm.policy_mem_sum_insured desc")->result_array();
        // echo $this->db->last_query();exit;                
        if(!empty($res)){
            echo json_encode(["flag"=>1,"sum_insure"=> $res[0]['policy_mem_sum_insured']]);
        }else{
           echo json_encode(["flag"=>0,"sum_insure"=> 0]); 
        }
    }

    /*healthproinfinity changes*/
    public function get_employee_data_new()
    {
     
        $emp_id = $_POST['emp_id'];
        
        $data =  $this->db->select('policy_for,spouse_dob,kid1_dob,kid2_dob,kid1_rel,kid2_rel,GCI_optional,annual_income,occupation,loan_acc_no,cust_id,emp_middlename,occupation,ISNRI,lead_id, json_qote,emp_id,emp_code,emp_firstname,fr_id,company_id,emp_lastname,gender,bdate,mob_no,email,emp_grade,emp_designation,emp_address,emp_city,emp_state,emp_pincode,street,location,flex_amount,total_salary,gmc_grade_id,emp_pay,doj,pancard,adhar,address,comm_address,ref1,ref2,salutation,emp_city,emp_state,emp_pincode,ifsc_code,annual_income')->where(["emp_id" => $emp_id])->get("employee_details")->row();
        echo json_encode($data);
    }

	//dedupe
    public function check_dedupe_logic(){
        $emp_id = $this->emp_id;
        $this->db->select('product_id,unique_ref_no');
        $data = $this->db->get_where("employee_details",array("emp_id" => $emp_id))->row_array();
        $product_id = $data['product_id'];
        $unique_ref_no = $data['unique_ref_no'];
        $already_cust_id = $this
                            ->db
                            ->query("select pm.familyConstruct,GROUP_CONCAT(p.status) proposal_status,GROUP_CONCAT(p.proposal_no) proposal_no,p.status,ed.lead_id,ed.emp_firstname,pd.txndate,ed.emp_id,ed.emp_lastname from proposal as p,employee_details as ed,payment_details as pd,proposal_member AS pm where pm.proposal_id = p.id AND p.emp_id = ed.emp_id and p.id = pd.proposal_id and p.status in('Success','Payment Received','Payment Pending') and ed.unique_ref_no = '" . $unique_ref_no . "' and ed.product_id = '" . $product_id . "' and ed.emp_id != '" . $emp_id . "' group by p.emp_id");
        $already_cust_id_count = $already_cust_id->result_array();
        $temp_adult_count = [];
        //echo $this->db->last_query();
        //print_pre($already_cust_id_count);exit;
        foreach ($already_cust_id_count as $key => $val) {
            if($val['status'] == 'Success' || $val['status'] == 'Payment Received'){
                
                $fam_cons = explode('+', $val['familyConstruct']);
                $fam_con = str_replace('A', '', $fam_cons[0]);
                if($fam_con > 0){
                    array_push($temp_adult_count, $fam_con);
                }
            }
        }

        $adult_count = array_sum($temp_adult_count);
        if($adult_count == 2){
           return ["status" => "error", "msg"=> 'This Customer has already purchased policy with combination for 2 Adults.'];
        }else{
        	
		$count_qry = "SELECT p.status from employee_details ed , proposal p where ed.emp_id = p.emp_id and ed.unique_ref_no = '".$unique_ref_no."' AND ed.product_id = '".$product_id."' AND ed.emp_id != '".$emp_id."' AND p.status IN ('Success','Payment Pending','Payment Received') group by ed.emp_id";
            //echo $count_qry;exit;
            $proposal_count = count($this->db->query($count_qry)->result_array());
            if($proposal_count >= 3){
                return ["status" => "error", "msg"=> 'Maximum allowed 3 proposal is already created for the given customer family construct, complete the existing proposal!'];
            }else{
                return ["status" => "success", "msg"=> ''];
            }

        }
    }

    function get_family_details_from_relationship_healthpro_xl()
    {
        $data['family_data'] = $this->obj_home->get_family_details_from_relationship_healthpro_xl();
        echo json_encode($data);
    }
    function store_family_dobs(){
        //print_r($_POST);exit;
        $empId = $_POST['empId'];
        //echo $empId;exit;
        $familyConstruct = $_POST['family_construct'];
        $familyConstruct = str_replace(' ', '+', $familyConstruct);        
        //kept default GHI ID to do age_validation
        $policy_detail_id = TELE_HEALTHPROINFINITY_GHI;
        $cons = explode("+", $familyConstruct);
        $memArr = [];
        $adultAgeArr = [];
        $membersCovered = [];
        if($cons[0] == '2A' || ($cons[0] == '1A' && $_POST['adult_selected'] == '1')){
            if(isset($_POST['spouse_date_birth']) && $_POST['spouse_date_birth'] != ''){
               $today = date("Y-m-d");
               // $today = date("Y-m-d",strtotime(date("Y-m-d") . "-1 days"));                
                $diff = date_diff(date_create(date('d-m-Y', strtotime($_POST['spouse_date_birth']))) , date_create($today));
                $age = $diff->format('%y');
                $age_type = 'years';
                if($age == 0){
                    $age = $diff->format('%a');
                    $age_type = 'days';
                }
                $res = $this->obj_home->check_validations($_POST['spouse_rel'], $policy_detail_id, $empId, $age,$age_type,$familyConstruct);
                
                if($res['message'] != 'true'){
                    //echo "error";exit;
                    echo json_encode($res); exit;
                }else{
                    //echo "sss";exit;
                    $memArr['spouse_dob'] = date('d-m-Y', strtotime($_POST['spouse_date_birth']));
                    //array_push($memArr, ['kid_title' => 'spouse','age_type' => $age_type,'age' => $age,'dob' => date('d-m-Y', strtotime($_POST['spouse_date_birth'])),"fr_id" => $_POST['spouse_rel']]);
                    array_push($adultAgeArr,$age);
                    array_push($membersCovered, $_POST['spouse_rel']);
                }
            }
        }

        if($cons[0] == '2A' || ($cons[0] == '1A' && $_POST['adult_selected'] == '0')){
            array_push($membersCovered, 0);
        }
        

        if(isset($cons[1])){
            if($cons[1] == '1K' || $cons[1] == '2K'){
                if(isset($_POST['kid1_date_birth']) && $_POST['kid1_date_birth'] != ''){
                    $today = date("Y-m-d");
                   // $today = date("Y-m-d",strtotime(date("Y-m-d") . "-1 days"));
                    $diff = date_diff(date_create(date('d-m-Y', strtotime($_POST['kid1_date_birth']))) , date_create($today));
                    $age = $diff->format('%y');
                    $age_type = 'years';
                    if($age == 0){
                        $age = $diff->format('%a');
                        $age_type = 'days';
                    }
                    $res = $this->obj_home->check_validations($_POST['kid1_rel'], $policy_detail_id, $empId, $age,$age_type,$familyConstruct);
                    if($res['message'] != 'true'){
                        echo json_encode($res); exit;
                    }else{
                        $memArr['kid1_dob'] = date('d-m-Y', strtotime($_POST['kid1_date_birth']));
                        $memArr['kid1_rel'] = $_POST['kid1_rel'];
                        array_push($membersCovered, $_POST['kid1_rel']);
                    }
                }
            }
            
            if($cons[1] == '2K'){               
               if(isset($_POST['kid2_date_birth']) && $_POST['kid2_date_birth'] != ''){
                    $today = date("Y-m-d");
            	    //$today = date("Y-m-d",strtotime(date("Y-m-d") . "-1 days"));
                    $diff = date_diff(date_create(date('d-m-Y', strtotime($_POST['kid2_date_birth']))) , date_create($today));
                    $age = $diff->format('%y');
                    $age_type = 'years';
                    if($age == 0){
                        $age = $diff->format('%a');
                        $age_type = 'days';
                    }
                    $res = $this->obj_home->check_validations($_POST['kid2_rel'], $policy_detail_id, $empId, $age,$age_type,$familyConstruct);
                    if($res['message'] != 'true'){
                        echo json_encode($res); exit;
                    }else{
                        $memArr['kid2_dob'] = date('d-m-Y', strtotime($_POST['kid2_date_birth']));
                        $memArr['kid2_rel'] = $_POST['kid2_rel'];
                        //echo "in";exit;
                        array_push($membersCovered, $_POST['kid2_rel']);
                    }
                } 
            }
            
        }

        //print_pre($membersCovered);exit;
        //echo $empId;exit;
	
	//upendra - 29-06-2021
		$self_policy_for = false;
		if(($cons[0] == '1A' && $_POST['adult_selected'] == '0')){
			$self_policy_for = true;
		}



        if(!empty($memArr)  || ($self_policy_for == true)  ){
            if($_POST['adult_selected'] != undefined){
                $memArr['policy_for'] = $_POST['adult_selected'];
            }
            $this->db->where('emp_id', $empId);
            $this->db->update('employee_details', $memArr);            
        }
        $res = $this->show_policy_selection_popup($familyConstruct,$_POST['sum_insures'],$empId,$adultAgeArr,$_POST['adult_selected']);
        $res['membersCovered'] = $membersCovered;
        echo json_encode($res); exit;     
    }
    function show_policy_selection_popup($family_construct,$sum_insures,$emp_id,$adultAgeArr,$policy_for){
       //echo $family_construct.'----'.$sum_insures.'----'.$emp_id.'----'.$policy_for;exit;
        $cons = explode('+', $family_construct);

        //print_pre($memArr);exit;
        /*$family_construct = $_POST['family_construct'];
        $sum_insures = $_POST['sum_insures'];
        $emp_id = encrypt_decrypt_password($_POST['emp_id'],'D');*/
        $empData = $this->db->select('bdate')
            ->from('employee_details')
            ->where('emp_id', $emp_id)
            ->get()
            ->row_array();
        $age = get_date_diff('year', $empData['bdate']);
        if($cons[0] == '2A'){
            array_push($adultAgeArr,$age);
        }else{
            if($policy_for == 0 && empty($adultAgeArr)){
                array_push($adultAgeArr,$age);
            }
        }

        $arr = array("GHI" => TELE_HEALTHPROINFINITY_GHI,
                     "GHI + GPA" => TELE_HEALTHPROINFINITY_GHI_GPA,
                     "GHI + SUPERTOPUP" => TELE_HEALTHPROINFINITY_GHI_ST);
        $si_word = substr($sum_insures, 0, -5) . ' Lacs';//str_replace('00000', ' Lacs', $sum_insures);
        $html = '<table class="table table-bordered" style=""><tbody>';
        foreach ($arr as $key => $value) {
            
            if($value != TELE_HEALTHPROINFINITY_GHI_ST){
                //echo "in";//exit;
                if($value == TELE_HEALTHPROINFINITY_GHI){
                    $name = 'GHI - ( SI - '.$si_word.' )';
                }else if($value == TELE_HEALTHPROINFINITY_GHI_GPA){
                    $name = 'GHI + GPA - ( SI - '.$si_word.' )';
                }
                $html .= "<tr>";
                $policy_detail_id = explode(',', $value);
                $premium = 0;
                $i = 1;
                foreach ($policy_detail_id as $key => $val) {
                    $i++;
                    if($val == TELE_HEALTHPROINFINITY_GPA){
                        $family_construct_new = explode('+',$family_construct)[0];
                    }else{
                        $family_construct_new = $family_construct;
                    }
                    
                    $check = $this
                        ->db
                        ->select("*")
                        ->from("family_construct_age_wise_si")
                        ->where("sum_insured", $sum_insures)->where("family_type", $family_construct_new)->where("policy_detail_id", $val)->get()
                        ->result_array();
                    //echo $this->db->last_query();//exit;
                    foreach ($check as $value1)
                    {
                        //print_pre($value);
                        $min_max_age = explode("-", $value1['age_group']);
                        if (max($adultAgeArr) >= $min_max_age[0] && max($adultAgeArr) <= $min_max_age[1])
                        {
                            $premium += $value1['PremiumServiceTax'];
                            $deductable = $value1['deductable'];
                        }
                    }
                }
                $html .= '<th scope="row"><div class="custom-control custom-radio"> <input type="radio" class="custom-control-input" name="policy_selection" data-premium="'.$premium.'"  data-deductable="'.$deductable.'" id="policy_selection'.$i.'" value="'.$value.'"><label class="custom-control-label" for="policy_selection'.$i.'">'.$name.' <span data-toggle="modal" data-target="#modalimg'.$i.'"><a style="color: #2424ff;">Know More </a><i class="fa fa-info" style="border: 1px solid #88add1;color: blue;border-radius: 12px;padding: 1px 5px;margin-left: 4px;font-size: 10px;"></i></span> </label></div></th><td style="color: #fc499b;">Premium (Incl.Tax)</td>';
                $html .= '<td> &#x20B9;</td><td>'.$premium.'</td></tr>';
            }else{
                //echo 'out';//exit;
                $check = $this
                        ->db
                        ->select("*")
                        ->from("family_construct_age_wise_si")
                        ->where("sum_insured", $sum_insures)->where("family_type", $family_construct)->where("policy_detail_id", $value)->get()
                        ->result_array();
                //echo $this->db->last_query();//exit;
                        $j = 4;
                foreach ($check as $value1)
                {
                    //print_pre($value1);
                    $min_max_age = explode("-", $value1['age_group']);
                    if (max($adultAgeArr) >= $min_max_age[0] && max($adultAgeArr) <= $min_max_age[1])
                    {
                        $deductable = substr($value1['deductable'], 0, -5) . ' Lacs';
                        $remaining_amt = $sum_insures - $value1['deductable'];
                        $remaining_amt = substr($remaining_amt, 0, -5) . ' Lacs';
                        $name = 'GHI ( '.$remaining_amt.' with '.$deductable. ' deductible )';
                        $html .= "<tr>";
                        $premium = $value1['PremiumServiceTax'];
                        $deductable = $value1['deductable'];
                        $html .= '<th scope="row"><div class="custom-control custom-radio"><input type="radio" class="custom-control-input" name="policy_selection" data-premium="'.$premium.'"  data-deductable="'.$deductable.'" id="policy_selection'.$j.'" value="'.$value.'"><label class="custom-control-label" for="policy_selection'.$j.'">'.$name.' <span data-toggle="modal" data-target="#modalimg1"><a style="color: #2424ff;">Know More </a><i class="fa fa-info" style="border: 1px solid #88add1;color: blue;border-radius: 12px;padding: 1px 5px;margin-left: 4px;font-size: 10px;"></i></span> </label></div></th><td style="color: #fc499b;">Premium (Incl.Tax)</td>';
                        $html .= '<td> &#x20B9;</td><td>'.$premium.'</td></tr>';
                    }
                    $j++;
                }
                
            }
            
        }
        //exit;
        $html .= '</tbody></table>';
        $res = ['status' => true, 'message'=>'true', 'html' => $html];
        return $res;

    }
    /*--------------------------------*/


    function tele_set_session(){
        $lead_id = encrypt_decrypt_password($_POST['lead_id'],'D');
        $res = $this->db->select('employee_details.emp_id,employee_details.product_id,product_master_with_subtype.policy_parent_id')->from('employee_details,product_master_with_subtype')
        ->where('employee_details.product_id = product_master_with_subtype.product_code')
        ->where('employee_details.lead_id',$lead_id)
        ->get()->row_array();
        if(!empty($res)){            
            $_SESSION['telesales_session']['emp_id'] = $res['emp_id'];
            $_SESSION['telesales_session']['product_code'] = $res['product_id'];
            $_SESSION['telesales_session']['parent_id'] = $res['policy_parent_id'];
        }
        echo json_encode(array("status"=>true));
    }

    /*healthpro changes*/
     //apply button changesss
    /*healthpro changes*/
     //apply button changesss
    function apply_changes()
    {

        extract($this
            ->input
            ->post());
        $gci_sent = $gci;
        $gci = ($gci == 'Yes') ? true : false;

        $emp_id = $this->emp_id;
        $ghi_policy_detail_id = $this
            ->db
            ->select('policy_detail_id')
            ->from('employee_policy_detail')
            ->where('parent_policy_id', $this->parent_id)->where('policy_sub_type_id', 1)
            ->get()
            ->row_array() ['policy_detail_id'];
        $child_count = $this
            ->obj_home
            ->get_child_count($emp_id, $ghi_policy_detail_id) ['count'];
        $adult_count = $this
            ->obj_home
            ->get_adult_count($emp_id, $ghi_policy_detail_id) ['count'];

        $family_construct_selected = $family_construct;
        $family_construct = explode('+', $family_construct);
        $selected_child_count = (!empty($family_construct[1])) ? $family_construct[1][0] : 0;
        $selected_adult_count = (!empty($family_construct[0])) ? $family_construct[0][0] : 0;

        if (!empty($adult_count))
        {

            $compare_adult = ($adult_count == $selected_adult_count) ? false : true;
            $compare_child = ($child_count == $selected_child_count) ? false : true;

            if ($compare_adult || $compare_child)
            {

                $message = 'Please Add Or Delete Member To Buy Policy';

            }
            else
            {
                $message = 'Changes Applied';
            }
            $only_gci_insert = false;
            if (!empty($only_gci))
            {

                $only_gci_insert = true;

                $this
                    ->db
                    ->where('emp_id', $this->emp_id);
                $this
                    ->db
                    ->update('employee_details', ['GCI_optional' => $gci_sent]);

                $message = ($gci) ? 'You have successfully opted for gci policy' : 'You have successfully opted out from gci policy';

            }
            //echo "here";exit;
            $data = $this
                ->obj_home
                ->get_all_member_data_new_gpa($product_id,'update', $ghi_policy_detail_id, $sum_insured, $family_construct_selected, true, $gci, '', $this->emp_id, $only_gci_insert);
            $return_array = ['message' => $message, 'data' => $data];
            echo json_encode($return_array);

        }
        else
        {

            $message = 'Please Add member';
            $message = '';
            $return_array = ['message' => $message, 'data' => ''];
            echo json_encode($return_array);
        }
    }

	 public function proposal_no_checker()
    {

        $proposal_no = 'P-'.hexdec(uniqid());

        $proposal_id = $this->db->select('id')
            ->from('proposal')
            ->where('proposal_no', $proposal_no)
            ->limit(1)
            ->get()
            ->row_array();
        if (count($proposal_id) > 0) {

            $this->proposal_no_checker();
        }

        return $proposal_no;
	 }

public function aprove_status_new()
    {
		
        //product mapping policy number
        //get all members in the above master policy and update their status
        //emp id of whom the proposal is created
		$emp_id = $this->emp_id;
		
	
        extract($this
            ->input
            ->post());
			//echo $product_id;exit;
			if($product_id == 'T01'){
				$parent_id = 'test123';
			}else{
				$parent_id = $this->parent_id;
			}
       $get_lead_id = $this->db->query("select lead_id,imd_code,pid,product_id from employee_details where emp_id = '$emp_id' ")->row_array();
			if($get_lead_id['lead_id'] != 0)
			{
				$lead_id = $get_lead_id['lead_id'];
				if($product_id == 'T01'){
					$product_id = 'T01';
				}else if($product_id == 'T03'){
                    $product_id = 'T03';
                }else{
					$product_id = 'R06';
				}
				
			}
        else
        {
            $lead_id = $get_lead_id['loan_acc_no'];
            $product_id = 'H01';
        }
		
		
        $logs_array['data'] = ["type" => "post_proposal", "req" => json_encode($_POST) , "lead_id" => $lead_id, "product_id" => $product_id];
        $this
            ->Logs_m
            ->insertLogs($logs_array);
        //get interm code
        $employee_details_new_api = $this
            ->db
            ->where("emp_id", $emp_id)->get('employee_details')
            ->row_array();
        $response_api = json_decode($employee_details_new_api['json_qote'], true);
        //print_pre($response_api);exit;
        $branch_code = $response_api['BRANCH_SOL_ID'];
        $IMDCode = $this
            ->db
            ->where('BranchCode', $branch_code)
			->get('master_imd')
            ->row_array() ['IMDCode'];
			$branch_code = '';
		$IMDCode = $get_lead_id['imd_code'];
        $proposal_array1234 = [];
        //if combo policy addd member into another policy
      
        extract($this
            ->input
            ->post(null));
        //$family_construct1 = explode("+", $family_construct);
        if($get_lead_id['product_id'] == 'T03'){
            $policy_detail_ids = (explode(",",$get_lead_id['pid']));
            
            $policies = [];
            foreach($policy_detail_ids as $key => $value_id){
                $policies[$key]['policy_detail_id'] = $value_id;
                $parent_id = $this->db->select('pms.policy_parent_id')
                ->from('employee_policy_detail epd,product_master_with_subtype pms')
                ->where('epd.product_name = pms.id')
                ->where('epd.policy_detail_id',$value_id)
                ->get('')
                ->row_array()['policy_parent_id'];
                $policies[$key]['policy_parent_id'] = $parent_id;
            }
            
        }else{
        $policies = $this->db->select("ed.policy_detail_id,p.combo_flag,p.policy_parent_id")
						->from("product_master_with_subtype AS p,employee_policy_detail AS ed")
						->where("ed.product_name = p.id")->where("p.policy_parent_id", $parent_id)
						->order_by("ed.policy_sub_type_id")->get()->result_array();
        }

        $gmc_id = "";
        $q = 0;

        

       //print_pre($policies);exit;
        //start transaction check whether there is an error in any of the policies
        foreach ($policies as $value)
        {
            //create individual proposals
            //get workflow from policy detail table
            //ends
            //get all members in particular policy
            $subQuery1 = $this
                ->db
                ->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id,
						epd.policy_type_id, epd.policy_sub_type_id, epd.insurer_id, epd.broker_id,
						epm.policy_detail_id, epm.family_relation_id, epm.family_id, epd.policy_no,
						mps.policy_type_id, mpst.policy_sub_type_id, mpst.policy_sub_type_name, mpst.policy_sub_type_image_path,
						mf.family_relation_id, mf.family_id, mf.emp_id, ic.insurer_id, ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, "0" AS fr_id, "Self" AS fr_name')
                ->from('employee_details AS ed,
						family_relation AS mf,
						employee_policy_member AS epm,
						employee_policy_detail AS epd,
						master_policy_sub_type AS mpst,
						master_policy_type AS mps,
						master_insurance_companies AS ic')
                ->where('ed.emp_id = mf.emp_id')
                ->where('mf.family_relation_id = epm.family_relation_id')
                ->where('epm.policy_detail_id = epd.policy_detail_id')
                ->where('epd.policy_sub_type_id = mpst.policy_sub_type_id')
                ->where('mpst.policy_type_id = mps.policy_type_id')
                ->where('epd.insurer_id = ic.insurer_id')
                ->where('epm.status != ', 'Inactive')
                ->where('mf.family_id', 0)
                ->where('mf.emp_id', $emp_id)->where('epd.policy_detail_id', $value['policy_detail_id'])->get_compiled_select();
            $op = $this
                ->db
                ->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id, epd.policy_type_id, epd.policy_sub_type_id,
						epd.insurer_id, epd.broker_id, epm.policy_detail_id,
						epm.family_relation_id, epm.family_id, epd.policy_no,
						mps.policy_type_id, mpst.policy_sub_type_id,
						mpst.policy_sub_type_name, mpst.policy_sub_type_image_path, fr.family_relation_id,
						fr.family_id, fr.emp_id, ic.insurer_id,
						ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, mfr.
						fr_id, mfr.fr_name')
                ->from('employee_policy_member` AS `epm`, `employee_policy_detail` AS `epd`, `master_policy_type` AS `mps`, `master_policy_sub_type` AS `mpst`, `master_insurance_companies` AS `ic`, `family_relation` AS `fr`, `master_family_relation` AS `mfr`, `employee_family_details` AS `efd`')
                ->where('epd`.`policy_detail_id` = `epm`.`policy_detail_id')
                ->where('mps`.`policy_type_id` = `epd`.`policy_type_id')
                ->where('mpst`.`policy_sub_type_id` = `epd`.`policy_sub_type_id')
                ->where('ic`.`insurer_id` = `epd`.`insurer_id')
                ->where('fr`.`family_relation_id` = `epm`.`family_relation_id')
                ->where('fr`.`family_id` = `efd`.`family_id')
                ->where('epm.status!=', 'Inactive')
                ->where('efd`.`fr_id` = `mfr`.`fr_id')
                ->where('fr`.`emp_id`', $emp_id)->where('epd.policy_detail_id', $value['policy_detail_id'])->get_compiled_select();
            $response = $this
                ->db
                ->query($subQuery1 . ' UNION ALL ' . $op)->result_array();
				//print_pre($this->db->last_query());exit;
			//print_pre($response);
            //now change their status based on each policy and create proposal independently
            //print_R($response);
            /*$check = $this
                ->obj_home
                ->check_validations_adult_count($response[0]['familyConstruct'], $emp_id, $value['policy_detail_id']);
            //Check validations for ".$response[0]['policy_sub_type_name']." policy"
            if (!$check)
            {
                $array = ["status" => false, "message" => "Member Not Added As Per Selection in " . $response[0]['policy_sub_type_name'] . " policy"];
                print_r(json_encode($array));
                return;
            }*/
            //policy creation
            //print_r($value);exit;

            $policy_details_individual = $this
                ->db
                ->where("policy_detail_id", $value['policy_detail_id'])->get("employee_policy_detail")
                ->row_array();
            //print_pre($policy_details_individual);
            if ($policy_details_individual['proposal_approval'] == 'Y')
            {
                $status = "Ready For Issuance";
            }
            else
            {
                /// $value['policy_detail_id']
                $check_payment = $this
                    ->db
                    ->select("*")
                    ->from("policy_payment_customer_mapping AS ppcm,master_payment_mode AS mpm")
                    ->where("ppcm.policy_id", $value['policy_detail_id'])->where("ppcm.mapping_id = mpm.id")
                    ->where("ppcm.type", "P")
                    ->group_by("ppcm.mapping_id")
                    ->get()
                    ->result_array();
                if ($check_payment[0]['payment_mode_name'] != "Cheque" && $check_payment[0]['id'] != 4)
                {
                    $status = "Payment Pending";
                }
                else
                {
                    $status = "Ready For Issuance";
                }
            }
            $date = date('Y-m-d');
            $proposal_no = $this
                ->db
                ->select("*")
                ->from("proposal_unique_number")
                ->get()
                ->row_array();
            //echo strtotime($date) ."==". strtotime($proposal_no['date']);exit;
            // if (strtotime($date) == strtotime($proposal_no['date']))
            // {
                // $number = ++$proposal_no['number'];
                // $array = ["number" => $number];
                // $this
                    // ->db
                    // ->where('id', $proposal_no['id']);
                // $this
                    // ->db
                    // ->update('proposal_unique_number', $array);
                // $propsal_number = "P-" . $number;
				// echo $this->db->last_query();

            // }
            // else
            // {
                // $number = date('Ymd') . '0000';
                // $propsal_number = "P-" . $number;
                // $array = ["number" => $number, "id" => 1, "date" => date('Y-m-d') ];
                // $this
                    // ->db
                    // ->where('id', '1');
                // $this
                    // ->db
                    // ->delete('proposal_unique_number');
                // $this
                    // ->db
                    // ->insert('proposal_unique_number', $array);
					// echo $this->db->last_query();
            // }
			$propsal_number = $this->proposal_no_checker();
			
			//echo $this->db->last_query();
			
			
			
			

            if ($update_data == 'update')
            {
                $policy_details_ids = $value['policy_detail_id'];
                $get_proposal = $this->db->query("select * from proposal where  emp_id = '$emp_id' AND policy_detail_id = '$policy_details_ids'")->row_array();
                $prop_ids = $get_proposal['id'];
                $check_proposal_entry = $this
                    ->db
                    ->query("select count(*) as count from proposal_member where  proposal_id = '$prop_ids'")->row_array();
                if ($check_proposal_entry['count'] > 0)
                {
                }
                else
                {
                    $check_proposal_entrys = $this
                        ->db
                        ->query("delete  from proposal where  id = '$prop_ids'");
                }
            }
            //echo count($response);exit;
            if (count($response) > 0)
            {
                if ($payment_mod == 'Pay U')
                {
                    $EasyPay_PayU_status = 1;
                }
                else
                {
                    $EasyPay_PayU_status = 0;
                }
				

                $proposal_array = ["proposal_no" => $propsal_number, "policy_detail_id" => $value['policy_detail_id'], "product_id" => $value['policy_parent_id'], "created_by" => $this->emp_id, "status" => $status, "branch_code" => $branch_code, "IMDCode" => $IMDCode, "created_date" => date('Y-m-d H:i:s') , "EasyPay_PayU_status" => 1, "emp_id" => $emp_id,"modified_date" => date('Y-m-d H:i:s')];

                if ($update_data == 'update')
                {
                }
                else
                {
					$policy_details_ids = $value['policy_detail_id'];
               	$get_proposal = $this->db->query("select id,emp_id from proposal where  emp_id = '$emp_id' AND policy_detail_id = '$policy_details_ids'")->row_array();
				//echo $this->db->last_query();
				if(count($get_proposal)<= 0){
					
					
					$this->db->insert("proposal", $proposal_array);
					
					$proposal_id = $this
                        ->db
                        ->insert_id();
						$pay_data = array(
												'proposal_id' => $proposal_id,
												'txndate' => date('Y-m-d H:i:s'),
												'payment_status' => 'Payment Pending',
												'payment_mode'=> $mode_of_payment,
												'emp_id'=>$emp_id
												
											 );
							
							$this->db->insert("payment_details", $pay_data);
							//echo $this->db->last_query();
				  
				}else{
					//will have to update modified date
					$proposal_id = $get_proposal['id'];
					$update_date = ["modified_date" => date('Y-m-d H:i:s')];
					$this->db->where('emp_id',$emp_id);
					$this->db->update("proposal", $update_date);
				}

                    $logs_array['data'] = ["type" => "insert_proposal", "req" => json_encode($proposal_array) , "lead_id" => $lead_id, "product_id" => $product_id];
                    $this
                        ->Logs_m
                        ->insertLogs($logs_array);
                    $proposal_array1234[] = $proposal_id;
                }
                if (!empty($declare_prop))
                {
                    foreach ($declare_prop as $declare_m_prop)
                    {
                        $data1_prop = array(
                            "emp_id" => $emp_id,
                            "proposal_number" => $propsal_number,
                            "product_id" => $value['policy_parent_id'],
                            "remark" => $declare_m_prop['label'],
                            "proposal_declare_id" => $declare_m_prop['question_prop'],
                            "format" => $declare_m_prop['format_prop'],
                            "created_by" => $this->emp_id
                        );
                        if ($update_data == 'update')
                        {
                            $updata_prop = array(
                                "format" => $declare_m_prop['format_prop']
                            );
                            $this
                                ->db
                                ->where(['emp_id' => $emp_id, 'emp_proposal_declare_id' => $declare_m_prop['question_prop']]);
                            $this
                                ->db
                                ->update("employee_declare_proposal_data", $updata_prop);
                            $logs_array['data'] = ["type" => "update_ghd_proposal", "req" => json_encode($updata_prop) , "lead_id" => $lead_id, "product_id" => $product_id];
                            $this
                                ->Logs_m
                                ->insertLogs($logs_array);
                        }
                        else
                        {
                            $this
                                ->db
                                ->insert('employee_declare_proposal_data', $data1_prop);
                            $logs_array['data'] = ["type" => "insert_ghd_proposal", "req" => json_encode($data1_prop) , "lead_id" => $lead_id, "product_id" => $product_id];
                            $this
                                ->Logs_m
                                ->insertLogs($logs_array);
                        }
                    }
                }
                if (!empty($declare))
                {

                    foreach ($declare as $declare_m)
                    {

                        $data1 = array(
                            "emp_id" => $emp_id,
                            "proposal_number" => $propsal_number,
                            "product_id" => $value['policy_parent_id'],
                            "remark" => $declare_m['remark'],
                            "p_declare_id" => $declare_m['question'],
                            "format" => $declare_m['format'],
                            "created_by" => $this->emp_id
                        );

                        if ($update_data == 'update')
                        {
                            $updata = array(
                                "format" => $declare_m['format']
                            );
                            $this
                                ->db
                                ->where(['emp_id' => $emp_id, 'p_declare_id' => $declare_m['question']]);
                            $this
                                ->db
                                ->update("employee_declare_data", $updata);
                            $logs_array['data'] = ["type" => "update_ghd_health_proposal", "req" => json_encode($updata) , "lead_id" => $lead_id, "product_id" => $product_id];
                            $this
                                ->Logs_m
                                ->insertLogs($logs_array);
                        }
                        else
                        {
                            $this
                                ->db
                                ->insert('employee_declare_data', $data1);
                            $logs_array['data'] = ["type" => "insert_ghd_health_proposal", "req" => json_encode($data1) , "lead_id" => $lead_id, "product_id" => $product_id];
                            $this
                                ->Logs_m
                                ->insertLogs($logs_array);
                        }
                    }
                }
                $sum = 0;
				$del_proposal_mem = $this->db ->query("delete  from proposal_member where  proposal_id = '$proposal_id'");
                foreach ($response as $value)
                {
                    //update staus to confirmed
                    // print_pre($value);
                    $update_array = ["member_status" => "confirmed"];
                    //sahil
                    if(count($get_proposal)<= 0){//if ($update_data == 'update'){
                        $member = $this
                            ->db
                            ->where("policy_member_id", $value["policy_member_id"])->get("employee_policy_member")
                            ->row_array();
                        $policy_details_ids = $value['policy_detail_id'];
                        $policy_member_id = $value['policy_member_id'];
                        $get_proposal_id = $this
                            ->db
                            ->query("select * from proposal_member where  policy_member_id = '$policy_member_id'")->row_array();
                        if (count($get_proposal_id) > 0)
                        {
                            $proposal_id = $get_proposal_id['proposal_id'];
                            $this
                                ->db
                                ->where('policy_member_id', $value['policy_member_id']);
                            $this
                                ->db
                                ->update('proposal_member', $member);
                            $logs_array['data'] = ["type" => "update_proposal_member", "req" => json_encode($member) , "lead_id" => $lead_id, "product_id" => $product_id];
                            $this
                                ->Logs_m
                                ->insertLogs($logs_array);
                        }
                        else
                        {
                            $get_proposal = $this
                                ->db
                                ->query("select * from proposal where  emp_id = '$emp_id' AND policy_detail_id = '$policy_details_ids'")->row_array();
                            if (count($get_proposal) > 0)
                            {
                            }
                            else
                            {
                                $this
                                    ->db
                                    ->insert("proposal", $proposal_array);
                                $proposal_id = $this
                                    ->db
                                    ->insert_id();
                                $logs_array['data'] = ["type" => "update_proposal", "req" => json_encode($proposal_array) , "lead_id" => $lead_id, "product_id" => $product_id];
                                $this
                                    ->Logs_m
                                    ->insertLogs($logs_array);
                            }
                            $member['proposal_id'] = $proposal_id;
                            $this
                                ->db
                                ->insert("proposal_member", $member);
                            $logs_array['data'] = ["type" => "update_proposal_member", "req" => json_encode($member) , "lead_id" => $lead_id, "product_id" => $product_id];
                            $this
                                ->Logs_m
                                ->insertLogs($logs_array);
                        }
                    }
                    else
                    {
                        $this
                            ->db
                            ->where('policy_member_id', $value['policy_member_id']);
                        $this
                            ->db
                            ->update('employee_policy_member', $update_array);
                        $logs_array['data'] = ["type" => "update_proposal_policy_member", "req" => json_encode($update_array) , "lead_id" => $lead_id, "product_id" => $product_id];
                        $this
                            ->Logs_m
                            ->insertLogs($logs_array);
							
                        //get member details row
						 $policy_member_id = $value['policy_member_id'];
						 
                      
                        $member = $this
                            ->db
                            ->where("policy_member_id", $value["policy_member_id"])->get("employee_policy_member")
                            ->row_array();
                        $member['proposal_id'] = $proposal_id;
						 
                        $this
                            ->db
                            ->insert("proposal_member", $member);
						$logs_array['data'] = ["type" => "insert_proposal_member", "req" => json_encode($member) , "lead_id" => $lead_id, "product_id" => $product_id];

						
                        $this
                            ->Logs_m
                            ->insertLogs($logs_array);
                    }

                    if ($product_id == 'T01' || $product_id == 'T03')
                    {
                        //print_R($value['policy_sub_type_id']);
                        if ($value['policy_sub_type_id'] != 1)
                        {

                            $sum += $value['policy_mem_sum_premium'];
                        }
                        else
                        {
                            $sum = $value['policy_mem_sum_premium'];
                        }

                    }
                    else
                    {
                        $sum = $value['policy_mem_sum_premium'];
                    }
					
                    $this
                        ->db
                        ->where('id', $proposal_id);
                    $this
                        ->db
                        ->update('proposal', ["sum_insured" => $value['policy_mem_sum_insured'], "premium" => $sum]);
					

                    $logs_array['data'] = ["type" => "update_proposal_suminsured_premium", "req" => json_encode($value['policy_mem_sum_insured']."".$sum) , "lead_id" => $lead_id, "product_id" => $product_id];
                    $this
                        ->Logs_m
                        ->insertLogs($logs_array);

                }

            }

        }

       
        $array = ["status" => true, "proposal_ids" => $proposal_array1234];
        print_r(json_encode($array));

    }


	 public function aprove_status_newww_bk()
    {
		
        //product mapping policy number
        //get all members in the above master policy and update their status
        //emp id of whom the proposal is created
		$emp_id = $this->emp_id;
		
	
        extract($this
            ->input
            ->post());
			//echo $product_id;exit;
			if($product_id == 'T01'){
				$parent_id = 'test123';
			}else{
				$parent_id = $this->parent_id;
			}
       $get_lead_id = $this->db->query("select lead_id,imd_code from employee_details where emp_id = '$emp_id' ")->row_array();
			if($get_lead_id['lead_id'] != 0)
			{
				$lead_id = $get_lead_id['lead_id'];
				if($product_id == 'T01'){
					$product_id = 'T01';
				}else if($product_id == 'T03'){
                    $product_id = 'T03';
                }else{
					$product_id = 'R06';
				}
				
			}
        else
        {
            $lead_id = $get_lead_id['loan_acc_no'];
            $product_id = 'H01';
        }
		
		
        $logs_array['data'] = ["type" => "post_proposal", "req" => json_encode($_POST) , "lead_id" => $lead_id, "product_id" => $product_id];
        $this
            ->Logs_m
            ->insertLogs($logs_array);
        //get interm code
        $employee_details_new_api = $this
            ->db
            ->where("emp_id", $emp_id)->get('employee_details')
            ->row_array();
        $response_api = json_decode($employee_details_new_api['json_qote'], true);
        //print_pre($response_api);exit;
        $branch_code = $response_api['BRANCH_SOL_ID'];
        $IMDCode = $this
            ->db
            ->where('BranchCode', $branch_code)
			->get('master_imd')
            ->row_array() ['IMDCode'];
			$branch_code = '';
		$IMDCode = $get_lead_id['imd_code'];
        $proposal_array1234 = [];
        //if combo policy addd member into another policy
      
        extract($this
            ->input
            ->post(null));
        //$family_construct1 = explode("+", $family_construct);
        $policies = $this->db->select("ed.policy_detail_id,p.combo_flag,p.policy_parent_id")
						->from("product_master_with_subtype AS p,employee_policy_detail AS ed")
						->where("ed.product_name = p.id")->where("p.policy_parent_id", $parent_id)
						->order_by("ed.policy_sub_type_id")->get()->result_array();

        $gmc_id = "";
        $q = 0;

        

       //print_pre($policies);exit;
        //start transaction check whether there is an error in any of the policies
        foreach ($policies as $value)
        {
            //create individual proposals
            //get workflow from policy detail table
            //ends
            //get all members in particular policy
            $subQuery1 = $this
                ->db
                ->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id,
						epd.policy_type_id, epd.policy_sub_type_id, epd.insurer_id, epd.broker_id,
						epm.policy_detail_id, epm.family_relation_id, epm.family_id, epd.policy_no,
						mps.policy_type_id, mpst.policy_sub_type_id, mpst.policy_sub_type_name, mpst.policy_sub_type_image_path,
						mf.family_relation_id, mf.family_id, mf.emp_id, ic.insurer_id, ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, "0" AS fr_id, "Self" AS fr_name')
                ->from('employee_details AS ed,
						family_relation AS mf,
						employee_policy_member AS epm,
						employee_policy_detail AS epd,
						master_policy_sub_type AS mpst,
						master_policy_type AS mps,
						master_insurance_companies AS ic')
                ->where('ed.emp_id = mf.emp_id')
                ->where('mf.family_relation_id = epm.family_relation_id')
                ->where('epm.policy_detail_id = epd.policy_detail_id')
                ->where('epd.policy_sub_type_id = mpst.policy_sub_type_id')
                ->where('mpst.policy_type_id = mps.policy_type_id')
                ->where('epd.insurer_id = ic.insurer_id')
                ->where('epm.status != ', 'Inactive')
                ->where('mf.family_id', 0)
                ->where('mf.emp_id', $emp_id)->where('epd.policy_detail_id', $value['policy_detail_id'])->get_compiled_select();
            $op = $this
                ->db
                ->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id, epd.policy_type_id, epd.policy_sub_type_id,
						epd.insurer_id, epd.broker_id, epm.policy_detail_id,
						epm.family_relation_id, epm.family_id, epd.policy_no,
						mps.policy_type_id, mpst.policy_sub_type_id,
						mpst.policy_sub_type_name, mpst.policy_sub_type_image_path, fr.family_relation_id,
						fr.family_id, fr.emp_id, ic.insurer_id,
						ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, mfr.
						fr_id, mfr.fr_name')
                ->from('employee_policy_member` AS `epm`, `employee_policy_detail` AS `epd`, `master_policy_type` AS `mps`, `master_policy_sub_type` AS `mpst`, `master_insurance_companies` AS `ic`, `family_relation` AS `fr`, `master_family_relation` AS `mfr`, `employee_family_details` AS `efd`')
                ->where('epd`.`policy_detail_id` = `epm`.`policy_detail_id')
                ->where('mps`.`policy_type_id` = `epd`.`policy_type_id')
                ->where('mpst`.`policy_sub_type_id` = `epd`.`policy_sub_type_id')
                ->where('ic`.`insurer_id` = `epd`.`insurer_id')
                ->where('fr`.`family_relation_id` = `epm`.`family_relation_id')
                ->where('fr`.`family_id` = `efd`.`family_id')
                ->where('epm.status!=', 'Inactive')
                ->where('efd`.`fr_id` = `mfr`.`fr_id')
                ->where('fr`.`emp_id`', $emp_id)->where('epd.policy_detail_id', $value['policy_detail_id'])->get_compiled_select();
            $response = $this
                ->db
                ->query($subQuery1 . ' UNION ALL ' . $op)->result_array();
				//print_pre($this->db->last_query());exit;
			//print_pre($response);
            //now change their status based on each policy and create proposal independently
            //print_R($response);
            /*$check = $this
                ->obj_home
                ->check_validations_adult_count($response[0]['familyConstruct'], $emp_id, $value['policy_detail_id']);
            //Check validations for ".$response[0]['policy_sub_type_name']." policy"
            if (!$check)
            {
                $array = ["status" => false, "message" => "Member Not Added As Per Selection in " . $response[0]['policy_sub_type_name'] . " policy"];
                print_r(json_encode($array));
                return;
            }*/
            //policy creation
            //print_r($value);exit;

            $policy_details_individual = $this
                ->db
                ->where("policy_detail_id", $value['policy_detail_id'])->get("employee_policy_detail")
                ->row_array();
            //print_pre($policy_details_individual);
            if ($policy_details_individual['proposal_approval'] == 'Y')
            {
                $status = "Ready For Issuance";
            }
            else
            {
                /// $value['policy_detail_id']
                $check_payment = $this
                    ->db
                    ->select("*")
                    ->from("policy_payment_customer_mapping AS ppcm,master_payment_mode AS mpm")
                    ->where("ppcm.policy_id", $value['policy_detail_id'])->where("ppcm.mapping_id = mpm.id")
                    ->where("ppcm.type", "P")
                    ->group_by("ppcm.mapping_id")
                    ->get()
                    ->result_array();
                if ($check_payment[0]['payment_mode_name'] != "Cheque" && $check_payment[0]['id'] != 4)
                {
                    $status = "Payment Pending";
                }
                else
                {
                    $status = "Ready For Issuance";
                }
            }
            $date = date('Y-m-d');
            $proposal_no = $this
                ->db
                ->select("*")
                ->from("proposal_unique_number")
                ->get()
                ->row_array();
            //echo strtotime($date) ."==". strtotime($proposal_no['date']);exit;
            // if (strtotime($date) == strtotime($proposal_no['date']))
            // {
                // $number = ++$proposal_no['number'];
                // $array = ["number" => $number];
                // $this
                    // ->db
                    // ->where('id', $proposal_no['id']);
                // $this
                    // ->db
                    // ->update('proposal_unique_number', $array);
                // $propsal_number = "P-" . $number;
				// echo $this->db->last_query();

            // }
            // else
            // {
                // $number = date('Ymd') . '0000';
                // $propsal_number = "P-" . $number;
                // $array = ["number" => $number, "id" => 1, "date" => date('Y-m-d') ];
                // $this
                    // ->db
                    // ->where('id', '1');
                // $this
                    // ->db
                    // ->delete('proposal_unique_number');
                // $this
                    // ->db
                    // ->insert('proposal_unique_number', $array);
					// echo $this->db->last_query();
            // }
			$propsal_number = $this->proposal_no_checker();
			
			//echo $this->db->last_query();
			
			
			
			

            if ($update_data == 'update')
            {
                $policy_details_ids = $value['policy_detail_id'];
                $get_proposal = $this->db->query("select * from proposal where  emp_id = '$emp_id' AND policy_detail_id = '$policy_details_ids'")->row_array();
                $prop_ids = $get_proposal['id'];
                $check_proposal_entry = $this
                    ->db
                    ->query("select count(*) as count from proposal_member where  proposal_id = '$prop_ids'")->row_array();
                if ($check_proposal_entry['count'] > 0)
                {
                }
                else
                {
                    $check_proposal_entrys = $this
                        ->db
                        ->query("delete  from proposal where  id = '$prop_ids'");
                }
            }
            //echo count($response);exit;
            if (count($response) > 0)
            {
                if ($payment_mod == 'Pay U')
                {
                    $EasyPay_PayU_status = 1;
                }
                else
                {
                    $EasyPay_PayU_status = 0;
                }
				

                $proposal_array = ["proposal_no" => $propsal_number, "policy_detail_id" => $value['policy_detail_id'], "product_id" => $value['policy_parent_id'], "created_by" => $this->emp_id, "status" => $status, "branch_code" => $branch_code, "IMDCode" => $IMDCode, "created_date" => date('Y-m-d H:i:s') , "EasyPay_PayU_status" => 1, "emp_id" => $emp_id,"modified_date" => date('Y-m-d H:i:s')];

                if ($update_data == 'update')
                {
                }
                else
                {
					$policy_details_ids = $value['policy_detail_id'];
               	$get_proposal = $this->db->query("select id,emp_id from proposal where  emp_id = '$emp_id' AND policy_detail_id = '$policy_details_ids'")->row_array();
				//echo $this->db->last_query();
				if(count($get_proposal)<= 0){
					
					
					$this->db->insert("proposal", $proposal_array);
					
					$proposal_id = $this
                        ->db
                        ->insert_id();
						$pay_data = array(
												'proposal_id' => $proposal_id,
												'txndate' => date('Y-m-d H:i:s'),
												'payment_status' => 'Payment Pending',
												'payment_mode'=> 'Pay U',
												'emp_id'=>$emp_id
												
											 );
							
							$this->db->insert("payment_details", $pay_data);
							//echo $this->db->last_query();
				  
				}else{
					//will have to update modified date
					$proposal_id = $get_proposal['id'];
					$update_date = ["modified_date" => date('Y-m-d H:i:s')];
					$this->db->where('emp_id',$emp_id);
					$this->db->update("proposal", $update_date);
				}

                    $logs_array['data'] = ["type" => "insert_proposal", "req" => json_encode($proposal_array) , "lead_id" => $lead_id, "product_id" => $product_id];
                    $this
                        ->Logs_m
                        ->insertLogs($logs_array);
                    $proposal_array1234[] = $proposal_id;
                }
                if (!empty($declare_prop))
                {
                    foreach ($declare_prop as $declare_m_prop)
                    {
                        $data1_prop = array(
                            "emp_id" => $emp_id,
                            "proposal_number" => $propsal_number,
                            "product_id" => $value['policy_parent_id'],
                            "remark" => $declare_m_prop['label'],
                            "proposal_declare_id" => $declare_m_prop['question_prop'],
                            "format" => $declare_m_prop['format_prop'],
                            "created_by" => $this->emp_id
                        );
                        if ($update_data == 'update')
                        {
                            $updata_prop = array(
                                "format" => $declare_m_prop['format_prop']
                            );
                            $this
                                ->db
                                ->where(['emp_id' => $emp_id, 'emp_proposal_declare_id' => $declare_m_prop['question_prop']]);
                            $this
                                ->db
                                ->update("employee_declare_proposal_data", $updata_prop);
                            $logs_array['data'] = ["type" => "update_ghd_proposal", "req" => json_encode($updata_prop) , "lead_id" => $lead_id, "product_id" => $product_id];
                            $this
                                ->Logs_m
                                ->insertLogs($logs_array);
                        }
                        else
                        {
                            $this
                                ->db
                                ->insert('employee_declare_proposal_data', $data1_prop);
                            $logs_array['data'] = ["type" => "insert_ghd_proposal", "req" => json_encode($data1_prop) , "lead_id" => $lead_id, "product_id" => $product_id];
                            $this
                                ->Logs_m
                                ->insertLogs($logs_array);
                        }
                    }
                }
                if (!empty($declare))
                {

                    foreach ($declare as $declare_m)
                    {

                        $data1 = array(
                            "emp_id" => $emp_id,
                            "proposal_number" => $propsal_number,
                            "product_id" => $value['policy_parent_id'],
                            "remark" => $declare_m['remark'],
                            "p_declare_id" => $declare_m['question'],
                            "format" => $declare_m['format'],
                            "created_by" => $this->emp_id
                        );

                        if ($update_data == 'update')
                        {
                            $updata = array(
                                "format" => $declare_m['format']
                            );
                            $this
                                ->db
                                ->where(['emp_id' => $emp_id, 'p_declare_id' => $declare_m['question']]);
                            $this
                                ->db
                                ->update("employee_declare_data", $updata);
                            $logs_array['data'] = ["type" => "update_ghd_health_proposal", "req" => json_encode($updata) , "lead_id" => $lead_id, "product_id" => $product_id];
                            $this
                                ->Logs_m
                                ->insertLogs($logs_array);
                        }
                        else
                        {
                            $this
                                ->db
                                ->insert('employee_declare_data', $data1);
                            $logs_array['data'] = ["type" => "insert_ghd_health_proposal", "req" => json_encode($data1) , "lead_id" => $lead_id, "product_id" => $product_id];
                            $this
                                ->Logs_m
                                ->insertLogs($logs_array);
                        }
                    }
                }
                $sum = 0;
				$del_proposal_mem = $this->db ->query("delete  from proposal_member where  proposal_id = '$proposal_id'");
                foreach ($response as $value)
                {
                    //update staus to confirmed
                    // print_pre($value);
                    $update_array = ["member_status" => "confirmed"];
                    //sahil
                    if(count($get_proposal)<= 0){//if ($update_data == 'update'){
                        $member = $this
                            ->db
                            ->where("policy_member_id", $value["policy_member_id"])->get("employee_policy_member")
                            ->row_array();
                        $policy_details_ids = $value['policy_detail_id'];
                        $policy_member_id = $value['policy_member_id'];
                        $get_proposal_id = $this
                            ->db
                            ->query("select * from proposal_member where  policy_member_id = '$policy_member_id'")->row_array();
                        if (count($get_proposal_id) > 0)
                        {
                            $proposal_id = $get_proposal_id['proposal_id'];
                            $this
                                ->db
                                ->where('policy_member_id', $value['policy_member_id']);
                            $this
                                ->db
                                ->update('proposal_member', $member);
                            $logs_array['data'] = ["type" => "update_proposal_member", "req" => json_encode($member) , "lead_id" => $lead_id, "product_id" => $product_id];
                            $this
                                ->Logs_m
                                ->insertLogs($logs_array);
                        }
                        else
                        {
                            $get_proposal = $this
                                ->db
                                ->query("select * from proposal where  emp_id = '$emp_id' AND policy_detail_id = '$policy_details_ids'")->row_array();
                            if (count($get_proposal) > 0)
                            {
                            }
                            else
                            {
                                $this
                                    ->db
                                    ->insert("proposal", $proposal_array);
                                $proposal_id = $this
                                    ->db
                                    ->insert_id();
                                $logs_array['data'] = ["type" => "update_proposal", "req" => json_encode($proposal_array) , "lead_id" => $lead_id, "product_id" => $product_id];
                                $this
                                    ->Logs_m
                                    ->insertLogs($logs_array);
                            }
                            $member['proposal_id'] = $proposal_id;
                            $this
                                ->db
                                ->insert("proposal_member", $member);
                            $logs_array['data'] = ["type" => "update_proposal_member", "req" => json_encode($member) , "lead_id" => $lead_id, "product_id" => $product_id];
                            $this
                                ->Logs_m
                                ->insertLogs($logs_array);
                        }
                    }
                    else
                    {
                        $this
                            ->db
                            ->where('policy_member_id', $value['policy_member_id']);
                        $this
                            ->db
                            ->update('employee_policy_member', $update_array);
                        $logs_array['data'] = ["type" => "update_proposal_policy_member", "req" => json_encode($update_array) , "lead_id" => $lead_id, "product_id" => $product_id];
                        $this
                            ->Logs_m
                            ->insertLogs($logs_array);
							
                        //get member details row
						 $policy_member_id = $value['policy_member_id'];
						 
                      
                        $member = $this
                            ->db
                            ->where("policy_member_id", $value["policy_member_id"])->get("employee_policy_member")
                            ->row_array();
                        $member['proposal_id'] = $proposal_id;
						 
                        $this
                            ->db
                            ->insert("proposal_member", $member);
						$logs_array['data'] = ["type" => "insert_proposal_member", "req" => json_encode($member) , "lead_id" => $lead_id, "product_id" => $product_id];

						
                        $this
                            ->Logs_m
                            ->insertLogs($logs_array);
                    }

                    if ($product_id == 'T01')
                    {
                        //print_R($value['policy_sub_type_id']);
                        if ($value['policy_sub_type_id'] != 1)
                        {

                            $sum += $value['policy_mem_sum_premium'];
                        }
                        else
                        {
                            $sum = $value['policy_mem_sum_premium'];
                        }

                    }
                    else
                    {
                        $sum = $value['policy_mem_sum_premium'];
                    }
					
                    $this
                        ->db
                        ->where('id', $proposal_id);
                    $this
                        ->db
                        ->update('proposal', ["sum_insured" => $value['policy_mem_sum_insured'], "premium" => $sum]);
					

                    $logs_array['data'] = ["type" => "update_proposal_suminsured_premium", "req" => json_encode($value['policy_mem_sum_insured']."".$sum) , "lead_id" => $lead_id, "product_id" => $product_id];
                    $this
                        ->Logs_m
                        ->insertLogs($logs_array);

                }

            }

        }

       
        $array = ["status" => true, "proposal_ids" => $proposal_array1234];
        print_r(json_encode($array));

    }

     /*healthpro changes created below new function*/
    public function get_all_policy_data_new()
	{
		
			
		$parent_id = $this->parent_id;
		
		print_r(json_encode($this->obj_home->get_all_policy_data_new($parent_id)));
	} 
    public function get_premium_construct_age()
    {
        extract($this
            ->input
            ->post(null));
        $policy_detail_ids = (explode(",", $policyNo));
        $policy_detail_id = $policyNo;
        $family_construct = $familyConstruct;
        $sum_insured = $sum_insures;
        $return_data = $this
            ->obj_home
            ->get_premium_from_policy_memberage($policy_detail_ids, $sum_insured, $family_construct, $maxPremiumAge);
        //$return_data = $this->obj_home->get_premium_construct_age_data($policy_detail_ids,$family_construct,$sum_insured,$emp_id);
        echo json_encode($return_data);

    }

    function verify_occupation(){
       // print_r($_POST);exit;
        $id = $_POST['occupation_id'];
        $occupation_details = $this
                      ->db
                      ->where(["id" => $id])->get("master_occupation")
                      ->row_array();
        
        $risk = $occupation_details['Risk_Category'];
        $allowedRisk = ['RS001','RS002'];
        if(!in_array($risk, $allowedRisk)){
            $data['status'] = 'error';
        }else{
            $data['status'] = 'success';
        }
        echo json_encode($data);exit;
    }
    /*healthpro changes ends*/

    public function index()
    {
	}
	public function employee_data()
	{
		$emp_id = $this->emp_id;
		$result =  $this->db->select('pid,deductable,saksham_id,emp_middlename,ISNRI,lead_id,emp_id,emp_code,emp_firstname,emp_lastname,gender,bdate,mob_no,email,emp_address,emp_city,emp_state,emp_pincode,street,location,doj,pancard,adhar,address,comm_address,ref1,ref2,salutation,emp_city,emp_state,emp_pincode,ifsc_code,occupation,annual_income')->where(["emp_id" => $emp_id])->get("employee_details")->row();
		print_r(json_encode($result));
	}
	public function get_agent_details()
	{
		
		echo json_encode($this->obj_home->get_agent_details($this->agent_id));
	}
    public function create_proposal()
    {


	//echo "<a href='/tele_av_upload'>BAck</a>";exit;
	$emp_id = $this->emp_id;
		extract($this->input->post());
		$data['disposition'] = $this->db->select('*')->from('disposition_master')->where('display', 1)->get()->result_array();

		/*
		$lead_creation  = $this->db->select('employee_details.created_at,employee_details.axis_process,tls_agent_mst.agent_name,employee_details.makerchecker,employee_details.is_makerchecker_journey')->from('employee_details,tls_agent_mst')
		->where('employee_details.assigned_to = tls_agent_mst.id')
		->where('emp_id',$emp_id)
		->get()->row_array();
		*/

		//upendra - maker/checker - 30-07-2021
		$check_maker_checker = $this->db->select("makerchecker,is_makerchecker_journey,is_makerchecker_journey")
							 ->from('employee_details')
							 ->where('emp_id',$emp_id)
							->get()->row_array();
		//echo $this->db->last_query();
		//print_r($check_maker_checker['is_makerchecker_journey']);exit;	
		//$check_maker_checker_flag = $check_maker_checker['is_makerchecker_journey'];
		//echo $check_maker_checker['is_makerchecker_journey'];exit;

			if(empty($check_maker_checker['makerchecker'])&&$check_maker_checker['is_makerchecker_journey']=='yes'){
				$data['disposition'] = $this->db->select('*')->from('disposition_master')->where('display', 1)->where('maker_checker','Maker')->get()->result_array();
			}

			if(strtolower($check_maker_checker['makerchecker'])=='maker'&&$check_maker_checker['is_makerchecker_journey']=='yes'){
				$data['disposition'] = $this->db->select('*')->from('disposition_master')->where('display', 1)->where('maker_checker','Maker')->get()->result_array();
			}

			if(strtolower($check_maker_checker['makerchecker'])=='checker'&&$check_maker_checker['is_makerchecker_journey']=='yes'){
				$data['disposition'] = $this->db->select('*')->from('disposition_master')->where('display', 1)->where('maker_checker','Checker')->get()->result_array();
			}
	//		echo $this->db->last_query();
	//print_pre($data['disposition']);exit;

		if($check_maker_checker['is_makerchecker_journey'] == "yes"){


			$lead_creation  = $this->db->select('employee_details.makerchecker,employee_details.emp_id,employee_details.is_makerchecker_journey,employee_details.created_at,employee_details.emp_id,employee_details.axis_process,tls_base_agent_tbl.base_agent_name as agent_name')->from('employee_details,tls_base_agent_tbl')
			->where('employee_details.assigned_to = tls_base_agent_tbl.base_id')
			->where('emp_id',$emp_id)
			->get()->row_array();
			//echo $this->db->last_query();
			$lead_creation_merge[0]['type'] = 'DO';		


		}else{
			$lead_creation  = $this->db->select('employee_details.makerchecker,employee_details.emp_id,employee_details.is_makerchecker_journey,employee_details.created_at,employee_details.emp_id,employee_details.axis_process,tls_agent_mst.agent_name')->from('employee_details,tls_agent_mst')
		->where('employee_details.assigned_to = tls_agent_mst.id')
		->where('emp_id',$emp_id)
		->get()->row_array();
			$lead_creation_merge[0]['type'] = 'AV';		

		}

//		print_pre($lead_creation);exit;
		
		if($lead_creation['makerchecker']=='checker'&&$lead_creation['is_makerchecker_journey']=='yes'){
			$data['checker_edit']='yes';	
			$data['checker_sendbackto_do']='yes';
			$data['checker_sendbackto_do_agent_name']=$_SESSION['telesales_session']['agent_name'];
			$data['checker_sendbackto_do_emp_id']=$lead_creation['emp_id'];

		}else{
			$data['checker_edit']='no';	
			$data['checker_sendbackto_do']='no';
			$data['checker_sendbackto_do_agent_name']=$_SESSION['telesales_session']['agent_name'];
			$data['checker_sendbackto_do_emp_id']=$lead_creation['emp_id'];

		}

		
		
		$data['payment_summary'] = $this->db->select('ed.date as "date",ed.disposition_id,dm.Dispositions,dm.`Sub-dispositions`,ed.agent_name,ed.remarks,ed.type')
         ->from('employee_disposition ed')
         ->join('disposition_master dm', 'ed.disposition_id = dm.id')
		 ->where('emp_id',$emp_id)
		 //->group_by('ed.disposition_id') 
		 ->order_by('ed.id')
		->get()
		->result_array();
		$lead_creation_merge[0]['date'] = $lead_creation['created_at'];
		$lead_creation_merge[0]['disposition_id'] = 123;
		$lead_creation_merge[0]['Dispositions'] = 'LEAD';
		$lead_creation_merge[0]['Sub-dispositions'] = 'LEAD CREATION';
		$lead_creation_merge[0]['agent_name'] = $lead_creation['agent_name'];
		
		$data['axis_process']=$lead_creation['axis_process'];
		
		$data['payment_summary'] = array_merge($lead_creation_merge,$data['payment_summary']);
		//print_pre($data['payment_summary']);
		//exit;
			//print_pre($this->db->last_query());exit;
		//$data['disposition'] = 'dd';
		//print_Pre($data);
		$data['selected_disposition'] = $this->db->select('*')
         ->from('employee_disposition ed')
         ->join('disposition_master dm', 'ed.disposition_id = dm.id')
		 ->where('emp_id',$emp_id)
		 ->order_by('ed.id','desc')
		->get()
		
		->row_array();
         //echo $this->db->last_query();exit;
		//$data['selected_disposition']['Open/Close'] = 'Open';
		$data['lob'] = $this->db->select('*')
         ->from('tls_axis_lob l')
		 ->where('axis_process',$data['axis_process'])
		//  ->group_by('l.axis_lob')
         ->order_by('axis_lob','asc')
		->get()
		->result_array();
//	print_r($data);	
		/*healthpro changes data of occupation master*/
		$data['occupation'] = $this->db->select('*')->get('master_occupation')->result_array();		
		/*healthpro changes done*/
		//print_r($this->session->userdata('telesales_session'));exit;
		$this->load->telesales_template("create_proposal_view",$data);
		
    }
	
	public function master_salutation()
	{
		$query = $this->db->query("select s_id,salutation from master_salutation");
	
		echo json_encode( $query->result_array());
	}
	
	public function master_nominee()
	{
		$query = $this->db->query("select nominee_id,nominee_type,gender from master_nominee");
	
		echo json_encode( $query->result_array());
	}

	public function master_axis_location()
	{

		$query = $this->db->query("select axis_loc_id,axis_location from tls_axis_location");
	
		echo json_encode( $query->result_array());
	}
	public function master_axis_vendor()
	{
		$location_post = $this->input->post();		
		if($location_post != null || !empty($location_post))
		$axis_loc_id = $location_post['axis_loc'];
		$query = $this->db->query("select axis_vendor_id,axis_vendor from tls_axis_vendor where axis_loc_id = '$axis_loc_id'");
	
		echo json_encode( $query->result_array());
	}
	public function master_axis_lob()
	{
		$vendor_post = $this->input->post();		
		if($vendor_post != null || !empty($vendor_post))
		$axis_vendor_id = $vendor_post['axis_vendor'];
		$query = $this->db->query("select axis_lob_id,axis_lob from tls_axis_lob where axis_vendor_id = '$axis_vendor_id'");
	
		echo json_encode( $query->result_array());
	}
	public function get_all_policy_data()
	{
		
			
		$parent_id = $this->parent_id;
		
		print_r(json_encode($this->obj_home->get_all_policy_data($parent_id)));
	} 
	
	public function get_suminsured_data()
	{
		
		$parent_id = $this->parent_id;
		/*healthpro changes passed product_id to below function*/
		//echo json_encode($this->obj_home->get_suminsured_data($parent_id));

		$product_id = $_POST['product_id'];
		echo json_encode($this->obj_home->get_suminsured_data($parent_id,$product_id));
	}
	
	public function get_family_construct()
	{
		
		//dedupe logic
        $res = $this->check_dedupe_logic();
        if($res['status'] == 'error'){
            echo json_encode($res);exit;
        }else{
            $data = $this->obj_home->get_family_construct();
            $res = ["status" => "success","data"=> $data ];
            echo json_encode($res);exit;
        }
	}
	
	public function get_premium() 
	{
        /*ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/
       /*healthpro changes created new function*/
		echo json_encode($this->obj_home->get_premium_new());
        //echo json_encode($this->obj_home->get_premium());
    }
	
	public function family_details_relation()
	{
		
		$data['family_data'] = $this->obj_home->family_details_relation();
		echo json_encode($data);
       
	}
	public function member_declare_data()
	{
		$parent_id = $this->parent_id;
	  
		$member_id = $this->input->post('member_id',true);
		
		$policy_member_declare = $this->obj_home->member_declare_data($parent_id);
		
		$p_sub=$this->db->query("select * from policy_declaration_subtype");

	    if(!empty($policy_member_declare))
		{

			if($p_sub->num_rows()>0)
			{
					$data .= '<table class="table table-bordered text-center">
					<thead class="text-uppercase" id="chronic">
					<tr>';
					$results = $p_sub->result_array();


					$data.='Do You Have Any One Or More Of Below Mentioned Chronic Disease ?';
					
					foreach($results as $sub_types)
					{
			 
							$data.='<th scope="col" style="width: 750px; text-align: left; font-weight: 600;"><input type="checkbox" class="sub_type" value="'.$sub_types['declare_subtype_id'].'" name="sub_type_check[]" id="subdeclare_'.$sub_types['declare_subtype_id'].$member_id.'" /> '. $sub_types['sub_type_name'].'</th>';
			 
					}
					$data.= '</tr>
					</thead>';
		
			}

			echo  $data;
		}

	}
	
	public function member_declaration_question()
	{
		$declare_post = $this->input->post();
		
		if($declare_post != null || !empty($declare_post))
		$sub_type_id = $declare_post['sub_type_id'];
		$parent_id = $this->parent_id;
		$member_id = $declare_post['member_id'];
		$policy_member_declare = $this->obj_home->member_declare_answer($parent_id,$sub_type_id);

		if(!empty($policy_member_declare))
		{
			$subtype_name = $this->obj_home->sub_type_name($sub_type_id);
			$data .= '<table class="table table-bordered text-center">
			<thead class="text-uppercase">
			<tr>
			<th scope="col" style="width: 750px; text-align: left; font-weight: 600;">'.$subtype_name['sub_type_name'].'</th>
			<th scope="col" style="font-weight: 600;">Answer</th>
			</tr>
			</thead>
			<tbody id="mydatasmember">';
			foreach ($policy_member_declare as $key => $value) 
			{
				$data .= '<tr>
				<td style="text-align:left;"><input type="hidden" class="mycontent" value="' . $value['p_member_id'].$member_id. '"/>' . $value['content'] . '</td>
				<td style="width: 150px;"><div class="custom-control custom-radio" style="float: left;"><input type="radio"  name="' . $value['p_member_id'].$member_id. '" id="' . $value['p_member_id'].$member_id. '" class="custom-control-input radios_out" value="Yes" > <label class="custom-control-label" for="' . $value['p_member_id'].$member_id. '"> Yes </label> </div>
				<div class="custom-control custom-radio" style="float:right;"> <input type="radio" name="' . $value['p_member_id'].$member_id. '" class="custom-control-input radios_out " value="No" id="' . $value['p_member_id'].$member_id. '_1" checked="">  <label class="custom-control-label" for="' . $value['p_member_id'].$member_id. '_1" > No </label></div>
				</td>
				</tr>';

			}
			$data .= '</tbody></table>';
			echo  $data;
		}
	}
	public function get_data_declaration($policy_id)
{
	$data = $this->db
	->select('policy_declaration.proposal_continue,policy_declaration.policy_detail_id,policy_declaration.p_declare_id,policy_declaration.content,policy_label_declarartion.label,policy_label_declarartion.p_label_id,,policy_declaration.is_remark,policy_declaration.is_answer')
	->from('policy_declaration')
	->join('policy_label_declarartion ', 'policy_label_declarartion.p_declare_id = policy_declaration.p_declare_id', 'left')
	->where('policy_declaration.parent_policy_id', $policy_id)
    ->where('policy_declaration.label','ghd')
	->get()
	->result_array();
    // echo $this->db->last_query();exit;
	return $data;
}


		//updated by upendra on 09-04-2021
	    public function get_declaration($product_id)
    {
		// echo $product_id;exit;
        extract($_POST);
		// echo $policy_id;exit;
		if($product_id == 'T01'){
			$policy_id = 'test123';//'RMxC5efb5c5a320e7';
		}
		if($product_id == 'T03'){
			$policy_id = 'NvpnoiwGGDQPVA23w';//'RMxC5efb5c5a320e7';
		}
		
        $policy_declarration_data = $this
            ->get_data_declaration($policy_id);
        $arr = array();

        foreach ($policy_declarration_data as $key => $check_header)
        {
            //print_R($policy_declarration_data);
            $arr['is_remark'][] = $check_header['is_remark'];

        }

        $data .= '<table class="table table-bordered text-center">
	<thead class="text-uppercase">
	<tr>
	<th scope="col" style="width: 750px; text-align: left; font-weight: 600;">Questionnaire</th>
	<th scope="col" style="font-weight: 600;">Answer</th>';
        if ($arr['is_remark'][0] == 1 && $arr['is_remark'][1] == 1)
        {

            $data .= '<th scope="col" style="font-weight: 600;">Remark</th>';
        }
        $data .= '</tr>
	</thead>
	<tbody id="mydatas1">';

	//updated by upendra on 09-04-2021
	$cell_count = 1;
        foreach ($policy_declarration_data as $key => $value)
        {
            $input_radio = '<td style="width: 150px;"><div class="custom-control custom-radio" style="float: left;"><input type="radio"  name="' . $value['p_declare_id'] . '" id="' . $value['p_declare_id'] . '" class="custom-control-input radios_out" value="Yes" > <label class="custom-control-label" for="' . $value['p_declare_id'] . '"> Yes </label> </div>
	<div class="custom-control custom-radio" style="float:right;"> <input type="radio" name="' . $value['p_declare_id'] . '" class="custom-control-input radios_out " value="No" id="' . $value['p_declare_id'] . '_1" checked="">  <label class="custom-control-label" for="' . $value['p_declare_id'] . '_1"> No </label></div>
	</td>';
				
            $input_text = '<td style="text-align:left;"> <textarea class="myremark" name = "textarea_' . $value['p_declare_id'] . '" value="" > </textarea></td>';

            $data .= '<tr>
	<td style="text-align:left;"> <input type="hidden" class="mycontent" value="' . $value['p_declare_id'] . '"/>' . $value['content'];

			//updated by upendra on 09-04-2021
			if($cell_count == 2){


				if($product_id == 'T03' || $product_id == 'T01'){
					if ($value['is_remark'] == 1 && $value['is_answer'] == 0)
					{
						$data .= '<span id="hpi_ghd_td"></span>';
						
					}
				}

				else{
					$data .= $input_text;
				}

				
				
			}

		$data .= '</td>';
            if ($value['is_remark'] == 1 && $value['is_answer'] == 1)
            {
                $data .= $input_radio;
                $data .= $input_text;
            }
            if ($value['is_remark'] == 0 && $value['is_answer'] == 1)
            {
                $data .= $input_radio;
            }

			//updated by upendra on 06-04-2021
			if ($value['is_remark'] == 1 && $value['is_answer'] == 0)
				{
				 if($product_id != 'T03' || $product_id!='T01'){
					$data .= $input_text;
				}else{
					$data .= '<td></td>';
				}
			}


            // if ($value['is_remark'] == 1 && $value['is_answer'] == 0)
            // {
            //     $data .= $input_text;
            // }

            '</tr>';


			//updated by upendra on 09-04-2021
			$cell_count++;
        }
        $data .= '</tbody></table>';
		
		//print_pre($data);exit;	
        return $data;
    }
    public function health_declaration()
    {
		
		$product_id = $this->input->post('product_id');
		

		//updated by upendra on 09-04-2021
		if($product_id == 'T01' || $product_id == 'T03'){
			$ghd = $this->get_declaration($product_id);

			if($product_id == "T01"){
				$parent_id = 'test123';
			}
			if($product_id == 'T03'){
				$parent_id = 'NvpnoiwGGDQPVA23w';
			}
			
		}
		// if($product_id == 'T03'){
		// 	$ghd = $this->get_declaration($product_id);
		// 	$parent_id = 'NvpnoiwGGDQPVA23w';
		// }
		else{
			$parent_id = $this->parent_id;
		}
			
		
		$policy_declaration_data = $this->obj_home->health_declaration($parent_id);
	//	print_pre($policy_declaration_data);exit;
		$data .= '<table class="table table-bordered text-center">
		<thead class="text-uppercase">
		<tr>
		<th scope="col" style="width: 750px; text-align: left; font-weight:600 !important;">Questionnaire</th>
		<th scope="col" style="font-weight:600 !important;">Answer</th>
		</tr>
		</thead>
		<tbody id="mydatas">';
        //print_pre($policy_declaration_data);exit;
		foreach ($policy_declaration_data as $key => $value) 
		{   
            
            $data .= '<tr>
                 <td style="text-align:left; font-weight:600 !important;"> <input type="hidden" class="mycontent" value="' . $value['p_declare_id'] . '"/>' . $value['content'] . '</td>
                 <td style="width: 150px;"><div class="custom-control custom-radio" style="float: left;"><input type="radio"  name="' . $value['p_declare_id'] . '" id="' . $value['p_declare_id'] . '" class="custom-control-input radios_out" value="Yes" > <label class="custom-control-label" for="' . $value['p_declare_id'] . '"> Yes </label> </div>
                 <div class="custom-control custom-radio" style="float:right;"> <input type="radio" name="' . $value['p_declare_id'] . '" class="custom-control-input radios_out " value="No" id="' . $value['p_declare_id'] . '_1" checked="">  <label class="custom-control-label" for="' . $value['p_declare_id'] . '_1"> No </label></div>
                 </td>
                 </tr>';
            
			

		}
		$data .= '</tbody></table>';
		
		
		$response = ['ghd' => html_entity_decode($ghd), 'employee_declaration' => $data];
		//print_pre($response);exit;
		echo json_encode($response);
        //echo  $data;
	}   
    	
	public function family_details_insert()
	{

        // print_r($_POST);exit;
		/*healthpro changes created new function commented old*/
		//$family_data = $this->obj_home->family_details_insert();
		//update product_code in employee_details Table
		if(isset($this->emp_id) && ($this->emp_id != '' || $this->emp_id != undefined)){
			$array = [];
			$product_id = ($_POST["plan_name"] != '' || $_POST["plan_name"] != undefined) ? $_POST["plan_name"] : 'R06';
			
			if($_POST["plan_name"]){
				$array['product_id'] = $_POST["plan_name"];
			}

            $occupation = (isset($_POST["occupation"]) && ($_POST["occupation"] != '' || $_POST["occupation"] != undefined)) ? $_POST["occupation"] : '';
			$array['occupation'] = $occupation;
			$GCI_optional = (isset($_POST["GCI_optional"]) && ($_POST["GCI_optional"] != '' || $_POST["GCI_optional"] != undefined)) ? $_POST["GCI_optional"] : 'Yes';
			$array['GCI_optional'] = $GCI_optional;
            $array['deductable'] = $_POST["hidden_deductable"];
            $array['pid'] = $_POST["hidden_policy_id"];
			//$array = ["product_id" => $product_id,"occupation" => $occupation,"GCI_optional" => $GCI_optional];
			$this->db->where('emp_id', $this->emp_id);
			$this->db->update('employee_details', $array);
            //dedupe update unique ref no - 17 may 21
            $this->update_ref_no($this->emp_id,$product_id);
			// echo $this->db->last_query();exit;
			//set session of parent id selected
			$res = $this->db->get_where('product_master_with_subtype',array("product_code"=> $product_id))->row_array();
			if(!empty($res)){
				$_SESSION['telesales_session']['parent_id'] = $res['policy_parent_id'];
				$_SESSION['telesales_session']['product_code'] = $product_id;
			}
			
		}		
        //echo "in";exit;
		$family_data = $this->obj_home->family_details_insert_new();
		echo json_encode($family_data);
	}
	public function family_details_insert_bkkkkk()
	{

        // print_r($_POST);exit;
		/*healthpro changes created new function commented old*/
		//$family_data = $this->obj_home->family_details_insert();
		//update product_code in employee_details Table
		if(isset($this->emp_id) && ($this->emp_id != '' || $this->emp_id != undefined)){
			$array = [];
			$product_id = ($_POST["plan_name"] != '' || $_POST["plan_name"] != undefined) ? $_POST["plan_name"] : 'R06';
			
			if($_POST["plan_name"]){
				$array['product_id'] = $_POST["plan_name"];
			}
            $occupation = (isset($_POST["occupation"]) && ($_POST["occupation"] != '' || $_POST["occupation"] != undefined)) ? $_POST["occupation"] : '';
			$array['occupation'] = $occupation;
			$GCI_optional = (isset($_POST["GCI_optional"]) && ($_POST["GCI_optional"] != '' || $_POST["GCI_optional"] != undefined)) ? $_POST["GCI_optional"] : 'Yes';
			$array['GCI_optional'] = $GCI_optional;
            $array['deductable'] = $_POST["hidden_deductable"];
            $array['pid'] = $_POST["hidden_policy_id"];
			//$array = ["product_id" => $product_id,"occupation" => $occupation,"GCI_optional" => $GCI_optional];
			$this->db->where('emp_id', $this->emp_id);
			$this->db->update('employee_details', $array);
			// echo $this->db->last_query();exit;
			//set session of parent id selected
			$res = $this->db->get_where('product_master_with_subtype',array("product_code"=> $product_id))->row_array();
			if(!empty($res)){
				$_SESSION['telesales_session']['parent_id'] = $res['policy_parent_id'];
				$_SESSION['telesales_session']['product_code'] = $product_id;
			}
			
		}		

		$family_data = $this->obj_home->family_details_insert_new();
		echo json_encode($family_data);
	}

	//healthpro updating annual income
    function update_annual_income()
    {
        extract($this
            ->input
            ->post(null, true));
       $emp_id = $this->emp_id;
        $status = false;

        if (!empty($emp_id))
        {

            if ($insert == 'true')
            {
                //update database
                $this
                    ->db
                    ->where(["emp_id" => $emp_id])->update("employee_details", ["annual_income" => $annual_income]);

		echo $annual_income;
            }
            else
            {
                //fetch annual income and return
                $get_annual_income = $this
                    ->db
                    ->select("annual_income")
                    ->from("employee_details")
                    ->where("emp_id", $emp_id)->get()
                    ->row_array();
                if (count($get_annual_income) > 0)
                {
                    echo $get_annual_income['annual_income'];
                }

            }
        }
	//echo $this->db->last_query();	
        //print_r(json_encode($return_array));
        
    }

    /*healthpro changes created new function old function enamed to get_all_data_bk()*/

    function get_all_data()
    {
		
		/*ini_set('display_errors', 1);
         ini_set('display_startup_errors', 1);
         error_reporting(E_ALL);*/
        $emp_id = $this->emp_id;
		$parent_id = $this->parent_id;
		//echo $parent_id;exit;
		$product_id = (isset($_POST['product_id'])) ? $_POST['product_id'] : '';
		//echo $parent_id;exit;
		if($product_id == 'T01' || $product_id == 'R12'){
			//$parent_id = 'test123';
			$data = [];
			if($parent_id != ''){
				$ghi_policy_detail_id = $this->db->select('policy_detail_id')->from('employee_policy_detail')
									->where('parent_policy_id',$parent_id)
									->where('policy_sub_type_id',1)
									->get()
									->row_array()['policy_detail_id'];
									// echo $this->db->last_query();exit;
									$member_data = $this->db->select('epm.policy_mem_sum_insured,epm.familyConstruct')->from('family_relation fr,employee_policy_member as epm')
									->where('fr.family_relation_id = epm.family_relation_id')
									->where('fr.emp_id',$this->emp_id)
									->where('epm.policy_detail_id',$ghi_policy_detail_id)
									->get()
									->row_array();
			// echo $this->db->last_query();//exit;
            $gci_options = $this->db->get_where("employee_details",array("emp_id" => $this->emp_id))->row_array()['GCI_optional'];
            
            if($gci_options == 'Yes'){
                $gci_option = true;
            }else{
                $gci_option = false;
            }
			
			$data = $this->obj_home->get_all_member_data_new_gpa($product_id,'update',$ghi_policy_detail_id,$member_data['policy_mem_sum_insured'],$member_data['familyConstruct'],false,$gci_option,'',$this->emp_id);
			// echo "----";
			//print_pre($data);exit;true
		}

		 $result["data"] = $data;
 
 
       
	
		echo json_encode($result);


		}else{
			$data = [];
			if($emp_id != ''){
		        $query ='SELECT fr.family_relation_id,ed.emp_id,epm.policy_mem_sum_premium,epm.policy_mem_sum_insured, epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
							epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type",epd.policy_sub_type_id,epd.policy_detail_id ,ed.emp_firstname as firstname,ed.emp_lastname as lastname,ed.bdate AS "dob",ed.mob_no AS "mobile",ed.email AS "email",
							ed.pancard AS "pan",ed.adhar AS "adhar",ed.gender AS "gender",ed.comm_address AS "comm_address",
							ed.address AS "adress",epm.policy_member_email_id,epm.policy_member_mob_no
							FROM 
							employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_details AS ed
							WHERE epd.policy_detail_id = epm.policy_detail_id
							AND epm.family_relation_id = fr.family_relation_id
							AND fr.family_id = 0
							AND fr.emp_id = ed.emp_id
							AND ed.emp_id = ' . $emp_id . '
							UNION all SELECT fr.family_relation_id,fr.emp_id,epm.policy_mem_sum_premium,epm.policy_mem_sum_insured, epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
							epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type",epd.policy_sub_type_id,epd.policy_detail_id,efd.family_firstname,efd.family_lastname,efd.family_dob AS "dob",efd.fr_id AS "mob",efd.family_flat AS "email",
							efd.fr_id AS "pan",efd.fr_id AS "adhar",epm.policy_mem_gender AS "gender",efd.fr_id AS "comm_address",
							efd.fr_id AS "address",epm.policy_member_email_id,epm.policy_member_mob_no
							FROM 
							employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
							master_family_relation AS mfr
							WHERE epd.policy_detail_id = epm.policy_detail_id
							AND epm.family_relation_id = fr.family_relation_id

							AND fr.family_id = efd.family_id 
							AND efd.fr_id = mfr.fr_id
							AND fr.emp_id = ' . $emp_id ;

				//echo $query;exit;

		        $query = $this->db->query($query);
		        $data = $query->result_array();
				//print_pre($data);exit;
		    }

	        $result["data"] = $data;
		
			echo json_encode($result);
		}
	}
	
	function get_all_data_bk()
    {
		
        $emp_id = $this->emp_id;
		$parent_id = $this->parent_id;



		$query ='SELECT ed.emp_id,epm.policy_mem_sum_premium,epm.policy_mem_sum_insured, epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
		epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type",epd.policy_sub_type_id,epd.policy_detail_id ,ed.emp_firstname as firstname,ed.emp_lastname as lastname,ed.bdate AS "dob",ed.mob_no AS "mobile",ed.email AS "email",
		ed.pancard AS "pan",ed.adhar AS "adhar",ed.gender AS "gender",ed.comm_address AS "comm_address",
		ed.address AS "adress"
		FROM 
		employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_details AS ed
		WHERE epd.policy_detail_id = epm.policy_detail_id
		AND epm.family_relation_id = fr.family_relation_id
		AND fr.family_id = 0
		AND fr.emp_id = ed.emp_id
		AND ed.emp_id = ' . $emp_id . '
		UNION all SELECT fr.emp_id,epm.policy_mem_sum_premium,epm.policy_mem_sum_insured, epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
		epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type",epd.policy_sub_type_id,epd.policy_detail_id,efd.family_firstname,efd.family_lastname,efd.family_dob AS "dob",efd.fr_id AS "mob",efd.family_flat AS "email",
		efd.fr_id AS "pan",efd.fr_id AS "adhar",efd.family_gender AS "gender",efd.fr_id AS "comm_address",
		efd.fr_id AS "address"
		FROM 
		employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
		master_family_relation AS mfr
		WHERE epd.policy_detail_id = epm.policy_detail_id
		AND epm.family_relation_id = fr.family_relation_id

		AND fr.family_id = efd.family_id 
		AND efd.fr_id = mfr.fr_id
		AND fr.emp_id = ' . $emp_id ;


        $query = $this->db->query($query);
       

        $result["data"] = $query->result_array();
	
		echo json_encode($result);
    }
	
	public function edit_member()
	{

		print_r(json_encode($this->obj_home->edit_member()));
	}

	public function get_subtype_id()
	{
		$edit_member_subtype = $this->input->post(null,true);
		if($edit_member_subtype != null || !empty($edit_member_subtype))

		$emp_mem_id = (isset($edit_member_subtype['emp_edit_id']) || $edit_member_subtype['emp_edit_id'] != '') ? $edit_member_subtype['emp_edit_id'] : $this->emp_id;
		$emp_policy_member = $edit_member_subtype['emp_policy_mem'];
		$subtype_id = $this->obj_home->get_sub_type_data($emp_mem_id, $emp_policy_member);
		echo json_encode($subtype_id);
	} 
	public function edit_declare_member_data()
	{
			$edit_member_declare = $this->input->post(null,true);
			if($edit_member_declare != null || !empty($edit_member_declare))
				
			$emp_mem_id = $edit_member_declare['emp_edit_id'];
			$emp_policy_member = $edit_member_declare['emp_policy_mem'];

			$subtype_id = $this->obj_home->get_sub_type_data($emp_mem_id, $emp_policy_member);

		if(!empty($subtype_id))
		{
	
			foreach($subtype_id as $subid)
			{
	
				$subtype_name = $this->obj_home->sub_type_name($subid['declare_sub_type_id']);

				$data .= '<div id="di'.$subid['declare_sub_type_id'].'"><table class="table table-bordered text-center">
				<thead class="text-uppercase">
				<tr>
				<th scope="col" style="width: 750px; text-align: left; font-weight: 600;">'.$subtype_name['sub_type_name'].'</th>
				<th scope="col" style="font-weight: 600;">Answer</th>


				</tr>
				</thead>

				<tbody id="mydatasmember">';
				$policy_member_declare = $this->obj_home->edit_member_declare_data($emp_mem_id, $emp_policy_member,$subid['declare_sub_type_id']);

				foreach ($policy_member_declare as $key => $value) 
				{

					$data .= '<tr>
					<td style="text-align:left;"><input type="hidden" class="mycontent" value="' . $value['p_member_id'] . '"/>' . $value['content'] . '</td>
					<td style="width: 150px;"><div class="custom-control custom-radio" style="float: left;"><input type="radio"  name="' . $value['p_member_id'] . '" id="' . $value['p_member_id'] . '" class="custom-control-input radios_out" value="Yes" > <label class="custom-control-label" for="' . $value['p_member_id'] . '"> Yes </label> </div>
					<div class="custom-control custom-radio" style="float:right;"> <input type="radio" name="' . $value['p_member_id'] . '" class="custom-control-input radios_out " value="No" id="' . $value['p_member_id'] . '_1" checked="">  <label class="custom-control-label" for="' . $value['p_member_id'] . '_1" > No </label></div>
					</td>
					</tr>';

				}
				$data .= '</tbody></table></div>';

			}

			echo  $data;
		}
	} 

	public function edit_ghd_emp_declare()
	{
			$edit_ghd_member_declare = $this->input->post(null,true);
			if($edit_ghd_member_declare != null || !empty($edit_ghd_member_declare))
				
			$emp_mem_id = $edit_ghd_member_declare['emp_edit_id'];
			$emp_policy_member = $edit_ghd_member_declare['emp_policy_mem'];
			$get_family_relation =  $this->db->query("select fr_id from employee_policy_member where policy_member_id = '$emp_policy_member'")->row_array();
			$fr_id = $get_family_relation['fr_id'];
			$get_data = $this->db->query("select type,format,remark from tls_ghd_employee_declare where family_relation_id = '$fr_id' AND emp_id = '$emp_mem_id'")->result_array();
			
			echo json_encode($get_data);

	}
	/*healthpro changes created new function old fnction renamed to delete_member_bk()*/

	public function delete_member()
	{
		extract($this
            ->input
            ->post());
		$emp_id = $this->emp_id;
		//echo $emp_id;exit;
        if($policy_member_id != '' || $policy_member_id != 'undefined' || $policy_detail_id != undefined){
            $get_lead_id = $this
                        ->db
                        ->query("select lead_id,loan_acc_no,product_id,new_remarks from employee_details where emp_id = '$emp_id' ")->row_array();
                    if ($get_lead_id['lead_id'] != 0)
                    {
                        $lead_id = $get_lead_id['lead_id'];
                        $product_id = $get_lead_id['product_id'];
                    }
                    else
                    {
                        $lead_id = $get_lead_id['loan_acc_no'];
                        $product_id = 'H01';
                    }


					//update GHD on member Deletion/ updated by upendra on 17-04-2021
					if($product_id == 'T03' || $product_id == 'T01' || $product_id == 'R06'){
						$new_remark = $get_lead_id['new_remarks'];
						$get_family_relation_id = $this
                        ->db
                        ->query("select family_relation_id from employee_policy_member where policy_member_id = '$policy_member_id' ")->row_array();

						$family_relation_id = $get_family_relation_id['family_relation_id'];

						$new_remark = stripslashes(html_entity_decode($new_remark));
						$new_remark = json_decode($new_remark, TRUE);
						$new_remark_length = count($new_remark);

						if($new_remark_length > 1){

							// $new_remark_update = [];

							foreach ($new_remark as $k=>$v){ 
								if($v['relation_code'] == $family_relation_id){
									unset($new_remark[$k]);
								}
							}

							$new_remark_update = addslashes(json_encode(array_values($new_remark)));

						}else{
							$new_remark_update = '';
						}


						$update_remark =
						$this->db->set('new_remarks', $new_remark_update);
						$this->db->where('emp_id', $emp_id);
						$this->db->update('employee_details');
					 }



                    $logs_array['data'] = ["type" => "delete_member_insured_post", "req" => json_encode($_POST) , "lead_id" => $lead_id, "product_id" => $product_id];
                    $this
                        ->Logs_m
                        ->insertLogs($logs_array);

                    //for ro7
                    if ($product_id == 'R12' || $product_id == 'T01' || $product_id == 'T03')
                    {
                        $policy_member_data = $this
                                ->db
                                ->select('family_relation_id,fr_id,policy_detail_id')
                                ->from('employee_policy_member')
                                ->where('policy_member_id', $policy_member_id)->get()
                                ->row_array();
                            $policy_id = $policy_member_data['policy_detail_id'];
                            $fr_id = $policy_member_data['fr_id'];
                        $family_relation_id = $policy_member_data['family_relation_id'];
                            $policy_detail_id = $this
                                ->db
                                ->query("select distinct epd.policy_detail_id from employee_policy_detail as epd,product_master_with_subtype as mpst where epd.parent_policy_id = mpst.policy_parent_id AND product_code = '$product_id' AND epd.policy_detail_id != '$policy_id'")->result_array();
                           // echo $this->db->last_query();print_pre($policy_detail_id);exit;
                            foreach ($policy_detail_id as $policy_nos)
                            {
                                $policy_detail_ids = $policy_nos['policy_detail_id'];
                               //$this
                                 //  ->db
                                   // ->query("delete from employee_policy_member where policy_detail_id = '$policy_detail_ids' AND policy_member_id = $policy_member_id ");
                                $this->db->query("delete from employee_policy_member where policy_detail_id = '$policy_detail_ids' AND family_relation_id = '$family_relation_id'");

                            }

                       // }

                        $this
                            ->db
                            ->where(["policy_member_id" => $policy_member_id])->delete("employee_policy_member");
                        $this
                            ->db
                            ->where(["policy_member_id" => $policy_member_id])->delete("employee_declare_member_data");
                        $this
                            ->db
                            ->where(["policy_member_id" => $policy_member_id])->delete("employee_declare_member_sub_type");
                        $this
                            ->db
                            ->where(["policy_member_id" => $policy_member_id])->delete("proposal_member");
                        /*$policy_member_data = $this
                            ->db
                            ->select('family_relation_id,fr_id,policy_detail_id')
                            ->from('employee_policy_member')
                            ->where('policy_member_id', $policy_member_id)->get()
                            ->row_array();
                        $policy_id = $policy_member_data['policy_detail_id'];
                        $fr_id = $policy_member_data['fr_id'];
                        $policy_detail_id = $this
                            ->db
                            ->query("select distinct epd.policy_detail_id from employee_policy_detail as epd,product_master_with_subtype as mpst where epd.parent_policy_id = mpst.policy_parent_id AND product_code = '$product_id' AND epd.policy_detail_id != '$policy_id'")->result_array();
                        foreach ($policy_detail_id as $policy_nos)
                        {
                            $policy_detail_ids = $policy_nos['policy_detail_id'];
                            $this
                                ->db
                                ->query("delete from employee_policy_member where policy_detail_id = '$policy_detail_ids' AND fr_id = '$fr_id'");

                        }

                    

                    $this
                        ->db
                        ->where(["policy_member_id" => $policy_member_id])->delete("employee_policy_member");
                    $this
                        ->db
                        ->where(["policy_member_id" => $policy_member_id])->delete("employee_declare_member_data");
                    $this
                        ->db
                        ->where(["policy_member_id" => $policy_member_id])->delete("employee_declare_member_sub_type");
                    $this
                        ->db
                        ->where(["policy_member_id" => $policy_member_id])->delete("proposal_member");*/
                    }else{
                        $del_member = $this->input->post(null,true);
                        if($del_member != null || !empty($del_member))
                        $emp_policy_member = $del_member['policy_member_id'];
                        $this->db->where([
                            "policy_member_id" => $emp_policy_member
                        ])->delete("employee_policy_member");

                        $this->db->where([
                            "policy_member_id" => $emp_policy_member
                        ])->delete("employee_declare_member_data");
                        
                         $this->db->where([
                            "policy_member_id" => $emp_policy_member
                        ])->delete("employee_declare_member_sub_type");
                        
                        $this->db->where([
                            "policy_member_id" => $emp_policy_member
                        ])->delete("proposal_member");
                        
                        $this->db->where([
                            "policy_member_id" => $emp_policy_member
                        ])->delete("tls_ghd_employee_declare");
                    }
        }
        
			
	}


	public function delete_member_wrong_code()
	{
		extract($this
            ->input
            ->post());
		$emp_id = $this->emp_id;
		//echo $emp_id;exit;
        if($policy_member_id != '' || $policy_member_id != 'undefined' || $policy_detail_id != undefined){
            $get_lead_id = $this
                        ->db
                        ->query("select lead_id,loan_acc_no,product_id,new_remarks from employee_details where emp_id = '$emp_id' ")->row_array();
                    if ($get_lead_id['lead_id'] != 0)
                    {
                        $lead_id = $get_lead_id['lead_id'];
                        $product_id = $get_lead_id['product_id'];
                    }
                    else
                    {
                        $lead_id = $get_lead_id['loan_acc_no'];
                        $product_id = 'H01';
                    }

		    //update GHD on member Deletion/ updated by upendra on 17-04-2021
					if($product_id == 'T03' || $product_id == 'T01'){
						$new_remark = $get_lead_id['new_remarks'];
						$get_family_relation_id = $this
                        ->db
                        ->query("select family_relation_id from employee_policy_member where policy_member_id = '$policy_member_id' ")->row_array();

						$family_relation_id = $get_family_relation_id['family_relation_id'];

						$new_remark = stripslashes(html_entity_decode($new_remark));
						$new_remark = json_decode($new_remark, TRUE);
						$new_remark_length = count($new_remark);

						if($new_remark_length > 1){

							// $new_remark_update = [];

							foreach ($new_remark as $k=>$v){ 
								if($v['relation_code'] == $family_relation_id){
									unset($new_remark[$k]);
								}
							}

							$new_remark_update = addslashes(json_encode(array_values($new_remark)));

						}else{
							$new_remark_update = '';
						}


						$update_remark =
						$this->db->set('new_remarks', $new_remark_update);
						$this->db->where('emp_id', $emp_id);
						$this->db->update('employee_details');
					 }


                    $logs_array['data'] = ["type" => "delete_member_insured_post", "req" => json_encode($_POST) , "lead_id" => $lead_id, "product_id" => $product_id];
                    $this
                        ->Logs_m
                        ->insertLogs($logs_array);

                    //for ro7
                    if ($product_id == 'R12' || $product_id == 'T01' || $product_id == 'T03')
                    {
			$policy_member_data = $this
                                ->db
                                ->select('family_relation_id,fr_id,policy_detail_id')
                                ->from('employee_policy_member')
                                ->where('policy_member_id', $policy_member_id)->get()
                                ->row_array();
                        $policy_id = $policy_member_data['policy_detail_id'];
                        $fr_id = $policy_member_data['fr_id'];
                        $family_relation_id = $policy_member_data['family_relation_id'];
                            $policy_detail_id = $this
                                ->db
                                ->query("select distinct epd.policy_detail_id from employee_policy_detail as epd,product_master_with_subtype as mpst where epd.parent_policy_id = mpst.policy_parent_id AND product_code = '$product_id' AND epd.policy_detail_id != '$policy_id'")->result_array();
                           // echo $this->db->last_query();print_pre($policy_detail_id);exit;
                            foreach ($policy_detail_id as $policy_nos)
                            {
                                $policy_detail_ids = $policy_nos['policy_detail_id'];
                               //$this
                                 //  ->db
                                   // ->query("delete from employee_policy_member where policy_detail_id = '$policy_detail_ids' AND policy_member_id = $policy_member_id ");
                                $this->db->query("delete from employee_policy_member where policy_detail_id = '$policy_detail_ids' AND family_relation_id = '$family_relation_id'");

                            }

                       // }

                        $this
                            ->db
                            ->where(["policy_member_id" => $policy_member_id])->delete("employee_policy_member");
                        $this
                            ->db
                            ->where(["policy_member_id" => $policy_member_id])->delete("employee_declare_member_data");
                        $this
                            ->db
                            ->where(["policy_member_id" => $policy_member_id])->delete("employee_declare_member_sub_type");
                        $this
                            ->db
                            ->where(["policy_member_id" => $policy_member_id])->delete("proposal_member");
                        /*$policy_member_data = $this
                            ->db
                            ->select('family_relation_id,fr_id,policy_detail_id')
                            ->from('employee_policy_member')
                            ->where('policy_member_id', $policy_member_id)->get()
                            ->row_array();
                        $policy_id = $policy_member_data['policy_detail_id'];
                        $fr_id = $policy_member_data['fr_id'];
                        $policy_detail_id = $this
                            ->db
                            ->query("select distinct epd.policy_detail_id from employee_policy_detail as epd,product_master_with_subtype as mpst where epd.parent_policy_id = mpst.policy_parent_id AND product_code = '$product_id' AND epd.policy_detail_id != '$policy_id'")->result_array();
//print_pre($policy_detail_id);exit;                       
 foreach ($policy_detail_id as $policy_nos)
                        {
                            $policy_detail_ids = $policy_nos['policy_detail_id'];
                            $this
                                ->db
                                ->query("delete from employee_policy_member where policy_detail_id = '$policy_detail_ids' AND fr_id = '$fr_id'");

                        }

                    

                    $this
                        ->db
                        ->where(["policy_member_id" => $policy_member_id])->delete("employee_policy_member");
                    $this
                        ->db
                        ->where(["policy_member_id" => $policy_member_id])->delete("employee_declare_member_data");
                    $this
                        ->db
                        ->where(["policy_member_id" => $policy_member_id])->delete("employee_declare_member_sub_type");
                    $this
                        ->db
                        ->where(["policy_member_id" => $policy_member_id])->delete("proposal_member");*/
                    }else{
                        $del_member = $this->input->post(null,true);
                        if($del_member != null || !empty($del_member))
                        $emp_policy_member = $del_member['policy_member_id'];
                        $this->db->where([
                            "policy_member_id" => $emp_policy_member
                        ])->delete("employee_policy_member");

                        $this->db->where([
                            "policy_member_id" => $emp_policy_member
                        ])->delete("employee_declare_member_data");
                        
                         $this->db->where([
                            "policy_member_id" => $emp_policy_member
                        ])->delete("employee_declare_member_sub_type");
                        
                        $this->db->where([
                            "policy_member_id" => $emp_policy_member
                        ])->delete("proposal_member");
                        
                        $this->db->where([
                            "policy_member_id" => $emp_policy_member
                        ])->delete("tls_ghd_employee_declare");
                    }
        }
        
			
	}

	public function delete_member_bk()
	{
			$del_member = $this->input->post(null,true);
			if($del_member != null || !empty($del_member))
			$emp_policy_member = $del_member['policy_member_id'];
			$this->db->where([
				"policy_member_id" => $emp_policy_member
			])->delete("employee_policy_member");

			$this->db->where([
				"policy_member_id" => $emp_policy_member
			])->delete("employee_declare_member_data");
			
			 $this->db->where([
				"policy_member_id" => $emp_policy_member
			])->delete("employee_declare_member_sub_type");
			
			$this->db->where([
				"policy_member_id" => $emp_policy_member
			])->delete("proposal_member");
			
			$this->db->where([
				"policy_member_id" => $emp_policy_member
			])->delete("tls_ghd_employee_declare");
	}

	public function nominee_family_details()
	{
			$nominee_member = $this->input->post(null,true);
			if($nominee_member != null || !empty($nominee_member))
			$family_id_data = $nominee_member['family_id'];
        
			$data = $this->db
                    ->select('*')
                    ->from('employee_policy_member efd')
                    ->where('efd.policy_member_id', $family_id_data)
                    ->get()
                    ->row_array();
					
 
			echo json_encode($data);
        //}
    }
	public function send_otp()
	{
		echo json_encode($this->obj_home->send_otp());
	}
	public function validate_otp()
	{

		echo json_encode($this->obj_home->validate_otp());
	}

       public function aprove_status()
	{
		$product_id = $this->input->post('product_id');
		$ismakerchecker= $this->input->post('ismakerchecker');
		$isagent_name= $this->input->post('updateaddagentname');
                $mode_of_payment= $this->input->post('mode_of_payment');
		if($ismakerchecker==1){
			
			$disposition=['emp_id'=>$this->emp_id,'disposition_id'=>'56','date'=>date('Y-m-d H:i:s'),'agent_name'=>$isagent_name];
			$this->db->insert('employee_disposition',$disposition);	
		}

		//echo $product_id;exit;
		if($product_id == 'T01' || $product_id == 'T03'){
			
			$this->aprove_status_new();  return;
		}
			$emp_id = $this->emp_id;
			if($product_id == 'T01'){
				$parent_id = 'test123';
			}else{
				$parent_id = $this->parent_id;
			}
			
		    $get_lead_id = $this->db->query("select lead_id,imd_code from employee_details where emp_id = '$emp_id' ")->row_array();
			
			if($get_lead_id['lead_id'] != 0)
			{
				$lead_id = $get_lead_id['lead_id'];
				if($product_id == 'T01'){
					$product_id == 'T01';
				}else if($product_id == 'T03'){
                    $product_id == 'T03';
                }else{
					$product_id = 'R06';
				}
				
			}
			$branch_code = '';
			$IMDCode = $get_lead_id['imd_code'];
			//Add Data into logs Table		
			$logs_array['data'] = ["type" => "post_proposal_create","req" => json_encode($_POST), "lead_id" => $lead_id,"product_id" => $product_id];
			$this->Logs_m->insertLogs($logs_array);
			//if combo policy addd member into check_validations_adult_count policy
			$this->db->trans_start();
			$policies = $this->db->select("ed.policy_detail_id,p.combo_flag,p.policy_parent_id")
						->from("product_master_with_subtype AS p,employee_policy_detail AS ed")
						->where("ed.product_name = p.id")->where("p.policy_parent_id", $parent_id)
						->order_by("ed.policy_sub_type_id")->get()->result_array();
			
			   //print_pre($policies);exit;
		 
			foreach ($policies as $value) 
			{
				$subQuery1 = $this->db
				   ->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id,
					epd.policy_type_id, epd.policy_sub_type_id, epd.insurer_id, epd.broker_id,
					epm.policy_detail_id, epm.family_relation_id, epm.family_id, epd.policy_no,
					mps.policy_type_id, mpst.policy_sub_type_id, mpst.policy_sub_type_name, mpst.policy_sub_type_image_path,
					mf.family_relation_id, mf.family_id, mf.emp_id, ic.insurer_id, ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, "0" AS fr_id, "Self" AS fr_name')
				   ->from('employee_details AS ed,
					family_relation AS mf,
					employee_policy_member AS epm,
					employee_policy_detail AS epd,
					master_policy_sub_type AS mpst,
					master_policy_type AS mps,
					master_insurance_companies AS ic')
				   ->where('ed.emp_id = mf.emp_id')->where('mf.family_relation_id = epm.family_relation_id')
				   ->where('epm.policy_detail_id = epd.policy_detail_id')->where('epd.policy_sub_type_id = mpst.policy_sub_type_id')
				   ->where('mpst.policy_type_id = mps.policy_type_id')->where('epd.insurer_id = ic.insurer_id')
				   ->where('epm.status != ', 'Inactive')->where('mf.family_id', 0)->where('mf.emp_id', $emp_id)
				   ->where('epd.policy_detail_id', $value['policy_detail_id'])->get_compiled_select();
				   
				   $op = $this->db->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id, epd.policy_type_id, epd.policy_sub_type_id,
					epd.insurer_id, epd.broker_id, epm.policy_detail_id,
					epm.family_relation_id, epm.family_id, epd.policy_no,
					mps.policy_type_id, mpst.policy_sub_type_id,
					mpst.policy_sub_type_name, mpst.policy_sub_type_image_path, fr.family_relation_id,
					fr.family_id, fr.emp_id, ic.insurer_id,
					ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, mfr.
					fr_id, mfr.fr_name')
				   ->from('employee_policy_member` AS `epm`, `employee_policy_detail` AS `epd`, `master_policy_type` AS `mps`, `master_policy_sub_type` AS `mpst`, `master_insurance_companies` AS `ic`, `family_relation` AS `fr`, `master_family_relation` AS `mfr`, `employee_family_details` AS `efd`')
				   ->where('epd`.`policy_detail_id` = `epm`.`policy_detail_id')
				   ->where('mps`.`policy_type_id` = `epd`.`policy_type_id')
				   ->where('mpst`.`policy_sub_type_id` = `epd`.`policy_sub_type_id')
				   ->where('ic`.`insurer_id` = `epd`.`insurer_id')
				   ->where('fr`.`family_relation_id` = `epm`.`family_relation_id')
				   ->where('fr`.`family_id` = `efd`.`family_id')->where('epm.status!=', 'Inactive')
				   ->where('efd`.`fr_id` = `mfr`.`fr_id')->where('fr`.`emp_id`', $emp_id)
				   ->where('epd.policy_detail_id', $value['policy_detail_id'])->get_compiled_select();
				   
				   $response = $this->db->query($subQuery1 . ' UNION ALL ' . $op)->result_array();
			//print_pre($this->db->last_query());continue;
				//Print_pre($response);continue;
					$policy_details_individual = $this->db->where("policy_detail_id", $value['policy_detail_id'])->get("employee_policy_detail")->row_array();
		  
					if ($policy_details_individual['proposal_approval'] == 'Y')
					{
						$status = "Ready For Issuance";
					} 
					else 
					{
			
						$check_payment = $this->db->select("mpm.payment_mode_name,ppcm.id")->from("policy_payment_customer_mapping AS ppcm,master_payment_mode AS mpm")
						->where("ppcm.policy_id",$value['policy_detail_id'])
						->where("ppcm.mapping_id = mpm.id")
						->where("ppcm.type", "P")
						->group_by("ppcm.mapping_id")->get()->result_array();
					
						if($check_payment[0]['payment_mode_name'] != "Cheque" && $check_payment[0]['id'] != 4)
						{
							 $status = "Payment Pending";
						}
						else 
						{
							$status = "Ready For Issuance";
						}
					}
					$date = date('Y-m-d');
					$proposal_no = $this->db->select("number,date,id")->from("proposal_unique_number")->get()->row_array();
		
					if (strtotime($date) == strtotime($proposal_no['date'])) 
					{

						$number = ++$proposal_no['number'];
						$array = ["number" => $number];
						$this->db->where('id', $proposal_no['id']);
						$this->db->update('proposal_unique_number', $array);
						$propsal_number = "P-" . $number;
					} 
					else 
					{

						$number = date('Ymd') . '0000';
						$propsal_number = "P-" . $number;

						$array = ["number" => $number, "id" => 1, "date" => date('Y-m-d')];
						$this->db->where('id', '1');
						$this->db->delete('proposal_unique_number');
						$this->db->insert('proposal_unique_number', $array);
					}
					if($update_data == 'update')
					{
							$policy_details_ids = $value['policy_detail_id'];

							$get_proposal = $this->db->query("select id from proposal where  emp_id = '$emp_id' AND policy_detail_id = '$policy_details_ids'")->row_array();
							$prop_ids = $get_proposal['id'];
							
							$check_proposal_entry = $this->db->query("select count(*) as count from proposal_member where  proposal_id = '$prop_ids'")->row_array();

							if($check_proposal_entry['count'] > 0)
							{
							}
							else
							{
								$check_proposal_entrys = $this->db->query("delete  from proposal where  id = '$prop_ids'");
							}
					}
		
					if(count($response)>0)
					{
			
						if($payment_mod == 'Pay U')
						{
							$EasyPay_PayU_status = 1;
						}
						else
						{
							$EasyPay_PayU_status = 0;
						}
			
						$proposal_array = [
							"proposal_no" => $propsal_number,
							"policy_detail_id" => $value['policy_detail_id'],
							"product_id" => $value['policy_parent_id'],
							"created_by" => $this->agent_id,
							"status" => $status,
							"branch_code" => $branch_code,
							 "IMDCode"=> $IMDCode,
							"created_date" => date('Y-m-d H:i:s'),
							"EasyPay_PayU_status"=>$EasyPay_PayU_status,
							"emp_id" => $emp_id,
							"modified_date" => date('Y-m-d H:i:s')
						];       
                        $proposal_create = $this->db->query("select id from proposal where emp_id = '$emp_id' AND policy_detail_id = '".$value['policy_detail_id']."'")->row_array();
                        //sahil
						//$proposal_create = $this->db->query("select id from proposal where emp_id = '$emp_id'")->row_array();
						//echo $this->db->last_query();exit;
						if($this->db->affected_rows() > 0)
						{
							$proposal_id = $proposal_create['id'];
							$this->db->where('emp_id',$emp_id);
							$this->db->update("proposal", $proposal_array);
							$proposal_array1234[] = $proposal_id;
							
							//Add Data into logs Table						  
					  	    $logs_array['data'] = ["type" => "update_proposal","req" => json_encode($proposal_array), "lead_id" => $lead_id,"product_id" => $product_id];
						    $this->Logs_m->insertLogs($logs_array);
							
						}
						else
						{
							$this->db->insert("proposal", $proposal_array);
							$proposal_id = $this->db->insert_id();
							//payment_details
							$pay_data = array(
												'proposal_id' => $proposal_id,
												'txndate' => date('Y-m-d H:i:s'),
												'payment_status' => 'Payment Pending',
												'payment_mode'=> $mode_of_payment,
												'emp_id'=>$emp_id
												
											 );
							
							$this->db->insert("payment_details", $pay_data);
							
							$proposal_array1234[] = $proposal_id;
						
							//Add Data into logs Table						  
					  	    $logs_array['data'] = ["type" => "insert_proposal","req" => json_encode($proposal_array), "lead_id" => $lead_id,"product_id" => $product_id];
						    $this->Logs_m->insertLogs($logs_array);
						}
						
						

                        $sum = 0;
						foreach ($response as $value) 
						{
								$update_array = [
													"member_status" => "confirmed"
												];
							
										$member = $this->db->where("policy_member_id", $value["policy_member_id"])->get("employee_policy_member")->row_array();
									
										$policy_details_ids = $value['policy_detail_id'];
										$policy_member_id = $value['policy_member_id'];
										
										$get_proposal_id = $this->db->query("select proposal_id from proposal_member where  policy_member_id = '$policy_member_id'")->row_array();
									
										if(count($get_proposal_id)>0)
										{
											$proposal_id = $get_proposal_id['proposal_id'];
											$this->db->where('policy_member_id', $value['policy_member_id']);
											$this->db->update('proposal_member', $member);
											
											//Add Data into logs Table												
											$logs_array['data'] = ["type" => "update_proposal_member","req" => json_encode($member), "lead_id" => $lead_id,"product_id" => $product_id];
											$this->Logs_m->insertLogs("logs_post_data",$logs_array);
										}
										else
										{
											$this->db->where('policy_member_id', $value['policy_member_id']);
											$this->db->update('employee_policy_member', $update_array);
											
											//Add Data into logs Table									
											$logs_array['data'] = ["type" => "update_proposal_policy_member","req" => json_encode($update_array), "lead_id" =>$lead_id,"product_id" => $product_id];
											$this->Logs_m->insertLogs($logs_array);
											
											//get member details row
											$member = $this->db->where("policy_member_id", $value["policy_member_id"])->get("employee_policy_member")->row_array();
											$member['proposal_id'] = $proposal_id;
									
											$this->db->insert("proposal_member", $member);
											
											//Add Data into logs Table											
											$logs_array['data'] = ["type" => "insert_proposal_member","req" => json_encode($member), "lead_id" => $lead_id,"product_id" => $product_id];
											$this->Logs_m->insertLogs($logs_array);
										}

                                    $prodArr = ['T01','T03'];
									//echo "hi";exit;
									//if ($product_id == 'R07')
                                    if(in_array($product_id, $prodArr))
                                    {
                                        //print_R($value['policy_sub_type_id']);
                                        if ($value['policy_sub_type_id'] != 1)
                                        {

                                            $sum += $value['policy_mem_sum_premium'];
                                        }
                                        else
                                        {
                                            $sum = $value['policy_mem_sum_premium'];
                                        }

                                    }else
                                    {
                                        $sum = $value['policy_mem_sum_premium'];
                                    }
									
									
									$this->db->where('id', $proposal_id);
									$this->db->update('proposal', [
										"sum_insured" => $value['policy_mem_sum_insured'],
										"premium" => $sum
									]);
						
									//Add Data into logs Table	
									$logs_array['data'] = ["type" => "update_proposal_suminsured_premium","req" => json_encode($value['policy_mem_sum_insured'].",".$value['policy_mem_sum_premium']), "lead_id" => $lead_id,"product_id" => $product_id];
									$this->Logs_m->insertLogs($logs_array);
				
						}
					}
			}
			$this->db->trans_commit();
			$array = ["status" => true, "proposal_ids" => $proposal_array1234];
			print_r(json_encode($array));
	}

	public function aprove_statusss_bkk()
	{
		//ini_set('display_errors', 1);
      //  ini_set('display_startup_errors', 1);
       // error_reporting(E_ALL);
		$product_id = $this->input->post('product_id');
		//echo $product_id;exit;
		if($product_id == 'T01'){
			
			$this->aprove_status_new();  return;
		}
			$emp_id = $this->emp_id;
			if($product_id == 'T01'){
				$parent_id = 'test123';
			}else{
				$parent_id = $this->parent_id;
			}
			
		    $get_lead_id = $this->db->query("select lead_id,imd_code from employee_details where emp_id = '$emp_id' ")->row_array();
			
			if($get_lead_id['lead_id'] != 0)
			{
				$lead_id = $get_lead_id['lead_id'];
				if($product_id == 'T01'){
					$product_id == 'T01';
				}else if($product_id == 'T03'){
                    $product_id == 'T03';
                }else{
					$product_id = 'R06';
				}
				
			}
			$branch_code = '';
			$IMDCode = $get_lead_id['imd_code'];
			//Add Data into logs Table		
			$logs_array['data'] = ["type" => "post_proposal_create","req" => json_encode($_POST), "lead_id" => $lead_id,"product_id" => $product_id];
			$this->Logs_m->insertLogs($logs_array);
			//if combo policy addd member into check_validations_adult_count policy
			$this->db->trans_start();
			$policies = $this->db->select("ed.policy_detail_id,p.combo_flag,p.policy_parent_id")
						->from("product_master_with_subtype AS p,employee_policy_detail AS ed")
						->where("ed.product_name = p.id")->where("p.policy_parent_id", $parent_id)
						->order_by("ed.policy_sub_type_id")->get()->result_array();
			
			   //print_pre($policies);exit;
		 
			foreach ($policies as $value) 
			{
				$subQuery1 = $this->db
				   ->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id,
					epd.policy_type_id, epd.policy_sub_type_id, epd.insurer_id, epd.broker_id,
					epm.policy_detail_id, epm.family_relation_id, epm.family_id, epd.policy_no,
					mps.policy_type_id, mpst.policy_sub_type_id, mpst.policy_sub_type_name, mpst.policy_sub_type_image_path,
					mf.family_relation_id, mf.family_id, mf.emp_id, ic.insurer_id, ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, "0" AS fr_id, "Self" AS fr_name')
				   ->from('employee_details AS ed,
					family_relation AS mf,
					employee_policy_member AS epm,
					employee_policy_detail AS epd,
					master_policy_sub_type AS mpst,
					master_policy_type AS mps,
					master_insurance_companies AS ic')
				   ->where('ed.emp_id = mf.emp_id')->where('mf.family_relation_id = epm.family_relation_id')
				   ->where('epm.policy_detail_id = epd.policy_detail_id')->where('epd.policy_sub_type_id = mpst.policy_sub_type_id')
				   ->where('mpst.policy_type_id = mps.policy_type_id')->where('epd.insurer_id = ic.insurer_id')
				   ->where('epm.status != ', 'Inactive')->where('mf.family_id', 0)->where('mf.emp_id', $emp_id)
				   ->where('epd.policy_detail_id', $value['policy_detail_id'])->get_compiled_select();
				   
				   $op = $this->db->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id, epd.policy_type_id, epd.policy_sub_type_id,
					epd.insurer_id, epd.broker_id, epm.policy_detail_id,
					epm.family_relation_id, epm.family_id, epd.policy_no,
					mps.policy_type_id, mpst.policy_sub_type_id,
					mpst.policy_sub_type_name, mpst.policy_sub_type_image_path, fr.family_relation_id,
					fr.family_id, fr.emp_id, ic.insurer_id,
					ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, mfr.
					fr_id, mfr.fr_name')
				   ->from('employee_policy_member` AS `epm`, `employee_policy_detail` AS `epd`, `master_policy_type` AS `mps`, `master_policy_sub_type` AS `mpst`, `master_insurance_companies` AS `ic`, `family_relation` AS `fr`, `master_family_relation` AS `mfr`, `employee_family_details` AS `efd`')
				   ->where('epd`.`policy_detail_id` = `epm`.`policy_detail_id')
				   ->where('mps`.`policy_type_id` = `epd`.`policy_type_id')
				   ->where('mpst`.`policy_sub_type_id` = `epd`.`policy_sub_type_id')
				   ->where('ic`.`insurer_id` = `epd`.`insurer_id')
				   ->where('fr`.`family_relation_id` = `epm`.`family_relation_id')
				   ->where('fr`.`family_id` = `efd`.`family_id')->where('epm.status!=', 'Inactive')
				   ->where('efd`.`fr_id` = `mfr`.`fr_id')->where('fr`.`emp_id`', $emp_id)
				   ->where('epd.policy_detail_id', $value['policy_detail_id'])->get_compiled_select();
				   
				   $response = $this->db->query($subQuery1 . ' UNION ALL ' . $op)->result_array();
			//print_pre($this->db->last_query());continue;
				//Print_pre($response);continue;
					$policy_details_individual = $this->db->where("policy_detail_id", $value['policy_detail_id'])->get("employee_policy_detail")->row_array();
		  
					if ($policy_details_individual['proposal_approval'] == 'Y')
					{
						$status = "Ready For Issuance";
					} 
					else 
					{
			
						$check_payment = $this->db->select("mpm.payment_mode_name,ppcm.id")->from("policy_payment_customer_mapping AS ppcm,master_payment_mode AS mpm")
						->where("ppcm.policy_id",$value['policy_detail_id'])
						->where("ppcm.mapping_id = mpm.id")
						->where("ppcm.type", "P")
						->group_by("ppcm.mapping_id")->get()->result_array();
					
						if($check_payment[0]['payment_mode_name'] != "Cheque" && $check_payment[0]['id'] != 4)
						{
							 $status = "Payment Pending";
						}
						else 
						{
							$status = "Ready For Issuance";
						}
					}
					$date = date('Y-m-d');
					$proposal_no = $this->db->select("number,date,id")->from("proposal_unique_number")->get()->row_array();
		
					if (strtotime($date) == strtotime($proposal_no['date'])) 
					{

						$number = ++$proposal_no['number'];
						$array = ["number" => $number];
						$this->db->where('id', $proposal_no['id']);
						$this->db->update('proposal_unique_number', $array);
						$propsal_number = "P-" . $number;
					} 
					else 
					{

						$number = date('Ymd') . '0000';
						$propsal_number = "P-" . $number;

						$array = ["number" => $number, "id" => 1, "date" => date('Y-m-d')];
						$this->db->where('id', '1');
						$this->db->delete('proposal_unique_number');
						$this->db->insert('proposal_unique_number', $array);
					}
					if($update_data == 'update')
					{
							$policy_details_ids = $value['policy_detail_id'];

							$get_proposal = $this->db->query("select id from proposal where  emp_id = '$emp_id' AND policy_detail_id = '$policy_details_ids'")->row_array();
							$prop_ids = $get_proposal['id'];
							
							$check_proposal_entry = $this->db->query("select count(*) as count from proposal_member where  proposal_id = '$prop_ids'")->row_array();

							if($check_proposal_entry['count'] > 0)
							{
							}
							else
							{
								$check_proposal_entrys = $this->db->query("delete  from proposal where  id = '$prop_ids'");
							}
					}
		
					if(count($response)>0)
					{
			
						if($payment_mod == 'Pay U')
						{
							$EasyPay_PayU_status = 1;
						}
						else
						{
							$EasyPay_PayU_status = 0;
						}
			
						$proposal_array = [
							"proposal_no" => $propsal_number,
							"policy_detail_id" => $value['policy_detail_id'],
							"product_id" => $value['policy_parent_id'],
							"created_by" => $this->agent_id,
							"status" => $status,
							"branch_code" => $branch_code,
							 "IMDCode"=> $IMDCode,
							"created_date" => date('Y-m-d H:i:s'),
							"EasyPay_PayU_status"=>$EasyPay_PayU_status,
							"emp_id" => $emp_id,
							"modified_date" => date('Y-m-d H:i:s')
						];       
                        $proposal_create = $this->db->query("select id from proposal where emp_id = '$emp_id' AND policy_detail_id = '".$value['policy_detail_id']."'")->row_array();
                        //sahil
						//$proposal_create = $this->db->query("select id from proposal where emp_id = '$emp_id'")->row_array();
						//echo $this->db->last_query();exit;
						if($this->db->affected_rows() > 0)
						{
							 $proposal_id = $proposal_create['id'];
							$this->db->where('emp_id',$emp_id);
							$this->db->update("proposal", $proposal_array);
							$proposal_array1234[] = $proposal_id;
							
							//Add Data into logs Table						  
					  	    $logs_array['data'] = ["type" => "update_proposal","req" => json_encode($proposal_array), "lead_id" => $lead_id,"product_id" => $product_id];
						    $this->Logs_m->insertLogs($logs_array);
							
						}
						else
						{
							$this->db->insert("proposal", $proposal_array);
							$proposal_id = $this->db->insert_id();
							//payment_details
							$pay_data = array(
												'proposal_id' => $proposal_id,
												'txndate' => date('Y-m-d H:i:s'),
												'payment_status' => 'Payment Pending',
												'payment_mode'=> $mode_payment,
												'emp_id'=>$emp_id
												
											 );
							
							$this->db->insert("payment_details", $pay_data);
							
							$proposal_array1234[] = $proposal_id;
						
							//Add Data into logs Table						  
					  	    $logs_array['data'] = ["type" => "insert_proposal","req" => json_encode($proposal_array), "lead_id" => $lead_id,"product_id" => $product_id];
						    $this->Logs_m->insertLogs($logs_array);
						}
						
						

                        $sum = 0;
						foreach ($response as $value) 
						{
								$update_array = [
													"member_status" => "confirmed"
												];
							
										$member = $this->db->where("policy_member_id", $value["policy_member_id"])->get("employee_policy_member")->row_array();
									
										$policy_details_ids = $value['policy_detail_id'];
										$policy_member_id = $value['policy_member_id'];
										
										$get_proposal_id = $this->db->query("select proposal_id from proposal_member where  policy_member_id = '$policy_member_id'")->row_array();
									
										if(count($get_proposal_id)>0)
										{
											$proposal_id = $get_proposal_id['proposal_id'];
											$this->db->where('policy_member_id', $value['policy_member_id']);
											$this->db->update('proposal_member', $member);
											
											//Add Data into logs Table												
											$logs_array['data'] = ["type" => "update_proposal_member","req" => json_encode($member), "lead_id" => $lead_id,"product_id" => $product_id];
											$this->Logs_m->insertLogs("logs_post_data",$logs_array);
										}
										else
										{
											$this->db->where('policy_member_id', $value['policy_member_id']);
											$this->db->update('employee_policy_member', $update_array);
											
											//Add Data into logs Table									
											$logs_array['data'] = ["type" => "update_proposal_policy_member","req" => json_encode($update_array), "lead_id" =>$lead_id,"product_id" => $product_id];
											$this->Logs_m->insertLogs($logs_array);
											
											//get member details row
											$member = $this->db->where("policy_member_id", $value["policy_member_id"])->get("employee_policy_member")->row_array();
											$member['proposal_id'] = $proposal_id;
									
											$this->db->insert("proposal_member", $member);
											
											//Add Data into logs Table											
											$logs_array['data'] = ["type" => "insert_proposal_member","req" => json_encode($member), "lead_id" => $lead_id,"product_id" => $product_id];
											$this->Logs_m->insertLogs($logs_array);
										}

                                    $prodArr = ['T01','T03'];
									//echo "hi";exit;
									//if ($product_id == 'R07')
                                    if(in_array($product_id, $prodArr))
                                    {
                                        //print_R($value['policy_sub_type_id']);
                                        if ($value['policy_sub_type_id'] != 1)
                                        {

                                            $sum += $value['policy_mem_sum_premium'];
                                        }
                                        else
                                        {
                                            $sum = $value['policy_mem_sum_premium'];
                                        }

                                    }else
                                    {
                                        $sum = $value['policy_mem_sum_premium'];
                                    }
									
									
									$this->db->where('id', $proposal_id);
									$this->db->update('proposal', [
										"sum_insured" => $value['policy_mem_sum_insured'],
										"premium" => $sum
									]);
						
									//Add Data into logs Table	
									$logs_array['data'] = ["type" => "update_proposal_suminsured_premium","req" => json_encode($value['policy_mem_sum_insured'].",".$value['policy_mem_sum_premium']), "lead_id" => $lead_id,"product_id" => $product_id];
									$this->Logs_m->insertLogs($logs_array);
				
						}
					}
			}
			$this->db->trans_commit();
			$array = ["status" => true, "proposal_ids" => $proposal_array1234];
			print_r(json_encode($array));
	}
	public function tele_payment_details_insert(){
		extract($this->input->post());
			$emp_id = $this->emp_id;
		
			//if lead marked as junk change proposal status - ankita junk dedupe changes
            if(isset($disposition)){
                $q = "SELECT Dispositions FROM disposition_master WHERE id = '".$disposition."'";
                $disRes = $this->db->query($q)->row_array();
                if(!empty($disRes) && ($disRes['Dispositions'] == "Junk" || $disRes['Dispositions'] == "Not Interested" || $disRes['Dispositions'] == "Not Eligible")){
                    //update proposal status to cancel
                    $this->db->where_not_in('status', array("Payment Received","Success"));
                    $proposalData = $this->db->get_where("proposal",array("emp_id" => $emp_id))->result_array();
                    if(!empty($proposalData)){
                        $this->db->where('emp_id', $emp_id);
                        $this->db->update('proposal', array("status"=>"Cancelled"));
                        // Add Data into logs Table      
                        $logs_array['data'] = ["type" => "update_proposal_status_on_junk_disposition","req" => json_encode($this->db->last_query()), "lead_id" => $_SESSION['telesales_session']['emp_id'],"product_id" => $_SESSION['telesales_session']['product_code']];
                        $this->Logs_m->insertLogs($logs_array);
                    }
                }
            }			

			$mode_payment =  $this->input->post('mode_payment', true);
			$preferred_contact_date =  $this->input->post('preferred_contact_date', true);
			$preferred_contact_time =  $this->input->post('preferred_contact_time', true);
			$av_remark =  $this->input->post('av_remark', true);
			$emp_agent_data = array(
									'payment_mode'=> $mode_payment,
									'preferred_contact_date'=> $preferred_contact_date,
									'preferred_contact_time'=> $preferred_contact_time,
									'av_remark'=> $av_remark);
							 
			$this->db->where('emp_id',$emp_id);
			$this->db->update('employee_details',$emp_agent_data);
			$agent_name = $this->db->select('agent_name')->from('tls_agent_mst')->where('id',encrypt_decrypt_password($agent_id,'D'))->get()->row_array()['agent_name'];


			$lead_creation  = $this->db->select('employee_details.created_at,tls_agent_mst.agent_name')->from('employee_details,tls_agent_mst')
		->where('employee_details.assigned_to = tls_agent_mst.id')
		->where('emp_id',$emp_id)
		->get()->row_array();


			$agent_type='AV';
			//upendra - maker/checker - 30-07-2021
			if(isset($_SESSION['telesales_session']['is_maker_checker'])){
				$is_maker_checker = $_SESSION['telesales_session']['is_maker_checker'];
				
				if($is_maker_checker == "yes"){
					
					$agent_name = $_SESSION['telesales_session']['base_caller_name'];
					$agent_type = "DO"; 


$lead_creation  = $this->db->select('employee_details.created_at,tls_base_agent_tbl.base_agent_name as agent_name')->from('employee_details,tls_base_agent_tbl')
		->where('employee_details.assigned_to = tls_base_agent_tbl.base_id')
			->where('emp_id',$emp_id)
				->get()->row_array();


				}
			}	
			else{
				//$agent_type = ""; 
			}
				
			//upendra - maker/checker - add $agent_type	
			$paymment_array = ['date' => date('Y-m-d H:i:s'),'disposition_id' => $sub_isposition,'agent_name' => $agent_name,'emp_id' => $emp_id,'remarks' => $av_remark,"type"=>$agent_type];
			$this->db->insert('employee_disposition',$paymment_array);
			//echo $this->db->last_query();exit;
		//	$lead_creation  = $this->db->select('employee_details.created_at as date,tls_agent_mst.agent_name')->from('employee_details,tls_agent_mst')
		//->where('employee_details.assigned_to = tls_agent_mst.id')
		//->where('emp_id',$emp_id)
		//->get()->row_array();
			$data = $this->db->select('ed.date as "date",ed.disposition_id,dm.Dispositions,dm.`Sub-dispositions`,ed.agent_name,ed.remarks,ed.type')
         ->from('employee_disposition ed')
         ->join('disposition_master dm', 'ed.disposition_id = dm.id')
		 ->where('ed.emp_id',$emp_id)
		// ->group_by('ed.disposition_id') 
		 ->order_by('ed.id','desc')
		->get()
		->result_array();
		//echo $this->db->last_query();exit;
		$lead_creation_merge[0]['date'] = $lead_creation['created_at'];
		$lead_creation_merge[0]['disposition_id'] = 123;
		$lead_creation_merge[0]['Dispositions'] = 'LEAD';
		$lead_creation_merge[0]['Sub-dispositions'] = 'LEAD CREATION';
		$lead_creation_merge[0]['agent_name'] = $lead_creation['agent_name'];
		$lead_creation_merge[0]['remarks'] = $lead_creation['remarks'];
		$lead_creation_merge[0]['type'] = $agent_type;

		$data = array_merge($lead_creation_merge,$data);
			//$disabled = $this->db->select('"Open" as  "Open/Close"')->from('disposition_master')->where('id',$sub_isposition)->get()->row_array()['Open/Close'];
			$disabled = $this->db->select('Open/Close')->from('disposition_master')->where('id',$sub_isposition)->get()->row_array()['Open/Close'];
			$array = ["status" => true, "message" => 'Payment Details Saved','disabled' => $disabled,'data' => $data];
			print_r(json_encode($array));
			
		
	}
	public function proposal_validation()
    {
		
		$this->form_validation->set_error_delimiters('','');
		//$this->form_validation->set_rules('mode_payment', 'Mode Payment', 'trim');
		//$this->form_validation->set_rules('auto_renewal', 'Auto Renewal', 'trim|required');
		//$this->form_validation->set_rules('av_remark', 'AV Remark', 'trim|required');
		// $this->form_validation->set_rules('nominee_relation', 'Nominee Relation', 'required|trim');
		// $this->form_validation->set_rules('nominee_fname', 'Nominee First Name', 'required|trim');
		// $this->form_validation->set_rules('nominee_lname', 'Nominee Last Name', 'required|trim');
		// $this->form_validation->set_rules('nominee_gender', 'Nominee Gender', 'required|trim');
		// $this->form_validation->set_rules('nominee_dob', 'Nominee DOB', 'required|trim');
		// $this->form_validation->set_rules('nominee_salutation', 'Nominee Salutation', 'required|trim');
		//$this->form_validation->set_rules('nominee_contact', 'Nominee Contact', 'required|trim');
		// $this->form_validation->set_rules('axis_location', 'Axis Location', 'required|trim');
		// $this->form_validation->set_rules('axis_vendor', 'Axis Vendor', 'required|trim');
		// $this->form_validation->set_rules('axis_lob', 'Axis Lob', 'required|trim');
		// $this->form_validation->set_rules('agent_id', 'Agent Code', 'required|trim');
		// $this->form_validation->set_rules('comAdd', 'Communication Address', 'required|trim');
		// $this->form_validation->set_rules('pin_code', 'Pin Code', 'required|trim');
		// $this->form_validation->set_rules('city', 'City', 'required|trim');
		// $this->form_validation->set_rules('state', 'State', 'required|trim');
		// $this->form_validation->set_rules('mobile_no2', 'mobile_no2', 'trim');
		
		//$this->form_validation->set_rules('preferred_contact_date', 'Preferred Contact Date', 'trim|required');
		//$this->form_validation->set_rules('preferred_contact_time', 'Preferred Contact Time', 'trim|required');
		
		//if ($this->form_validation->run() == FALSE)
			$product_id = $this->input->post('product_id');
			if (FALSE)
		{
			
			 $z = validation_errors();
			 $validation_err= ["status" => false, "message" => $z];
             print_r(json_encode($validation_err));   
		}
		else
		{
		
			$emp_id = $this->emp_id;
			if($product_id == 'T01'){
				$parent_id = 'test123';
			}else{
				$parent_id = $this->parent_id;
			}
			
			$proposal_data_all = $this->input->post(null,true);
			if(($proposal_data_all != null) || !empty($proposal_data_all))
			// $nominee_relation =  $this->input->post('nominee_relation', true);
			// $nominee_fname =  $this->input->post('nominee_fname', true);
			// $nominee_lname =  $this->input->post('nominee_lname', true);
			// $nominee_gender =  $this->input->post('nominee_gender', true);
			// $nominee_dob =  $this->input->post('nominee_dob', true);
			// $nominee_contact =  $this->input->post('nominee_contact', true);
			// $nominee_email =  $this->input->post('nominee_email', true);
			// $axis_location =  $this->input->post('axis_location', true);
			// $axis_vendor =  $this->input->post('axis_vendor', true);
			// $axis_lob =  $this->input->post('axis_lob', true);
			// $av_code =  $this->input->post('avCode', true);
			// $agent_id =  $this->input->post('agent_id', true);
			// $agent_name =  $this->input->post('agent_name', true);
			// $address =  $this->input->post('comAdd', true);
			// $address2 =  $this->input->post('comAdd2', true);
			// $address3 =  $this->input->post('comAdd3', true);
			// $pinCode =  $this->input->post('pin_code', true);
			// $city =  $this->input->post('city', true);
			// $state =  $this->input->post('state', true);
			// $mobile_no2 =  $this->input->post('mobile_no2', true);
			
			$declares =  $this->input->post('declares', true);
			$remarks_new =  $this->input->post('remarks_new', true);
			$myGHD = $this->input->post('myGHD',true);
			//echo $product_id;exit;
			$nominee_salutation = $this->input->post('nominee_salutation', true);
			$get_lead_id = $this->db->query("select GCI_optional,lead_id,imd_code,pid from employee_details where emp_id = '$emp_id' ")->row_array();
			$this->db->where(['emp_id' => $emp_id]);
			$this->db->update("employee_details", ['new_remarks' => $remarks_new ]);
			//echo $this->db->last_query();exit;
			if($get_lead_id['lead_id'] != 0)
			{
				$lead_id = $get_lead_id['lead_id'];
				if($product_id == 'T01'){
					$product_id = 'T01';
				}else if($product_id == 'T03'){
                    $product_id = 'T03';
                }else{
					$product_id = 'R06';
				}
				
			}
			
			//Add Data into logs Table		
			$logs_array['data'] = ["type" => "post_all_data","req" => json_encode($_POST), "lead_id" => $lead_id,"product_id" => $product_id];
			$this->Logs_m->insertLogs($logs_array);
		
			 $emp_agent_data = array(
										'payment_mode'=> $mode_payment,
										'auto_renewal'=> $auto_renewal,
										'preferred_contact_date'=> $preferred_contact_date,
										'preferred_contact_time'=> $preferred_contact_time,
										'av_remark'=> $av_remark
			 
										// 'axis_location' => $axis_location,
										// 'axis_vendor' => $axis_vendor,
										// 'axis_lob' => $axis_lob,
										// 'av_code' => $av_code,
										// 'agent_id' => $agent_id,
										// 'agent_name' => $agent_name,
										// 'address'=>$address,
										// 'comm_address'=>$address2,
										// 'comm_address1'=>$address3,
										// 'emp_pincode'=>$pinCode,
										// 'emp_city'=>$city,
										// 'emp_state'=>$state,
										// 'emg_cno'=> $mobile_no2,
										// 'payment_mode'=> $mode_payment,
										// 'auto_renewal'=> $auto_renewal,
										// 'preferred_contact_date'=> $preferred_contact_date,
										// 'preferred_contact_time'=> $preferred_contact_time,
										// 'av_remark'=> $av_remark,

									 );
								
		     //$agent_data = $this->obj_home->add_agent_emp_details($emp_id,$emp_agent_data);		
		
			 // if($agent_data == true)
			// {	
				//Add Data into logs Table							
				// $logs_array['data'] = ["type" => "insert_payement_details","req" => json_encode($emp_agent_data), "lead_id" => $lead_id,"product_id" => $product_id];
				// $this->Logs_m->insertLogs($logs_array);
			 // }
			
			$declare = json_decode($declares);

			$ghd_check = $this->obj_home->ghd_declined_insert($myGHD,$emp_id,'insert',$lead_id,$product_id);
			
			if($ghd_check['status'] == 'false')
			{
				$array_data = ["status" => false, "message" => $ghd_check['message'] ];
				print_r(json_encode($array_data));
				return;
			}
			//product mapping policy number
			//get all members in the above master policy and update their status
			//emp id of whom the proposal is created
	  
			$employee_details_new_api = $this->db->where("emp_id",$emp_id)->get('employee_details')->row_array();
			$response_api = json_decode($employee_details_new_api['json_qote'],true);
		 
			//$branch_code = $response_api['BRANCH_SOL_ID'];
			//$IMDCode = $this->db->where('BranchCode',$branch_code)->get('master_imd')->row_array()['IMDCode'];
			$branch_code = '';
			$IMDCode = $get_lead_id['imd_code'];
			$proposal_array1234 = [];
			 
			 
			 
			 
			//if combo policy addd member into check_validations_adult_count policy
			
		
			//$family_construct1 = explode("+", $family_construct);
            if($product_id == 'T03'){
                $pid = $get_lead_id['pid'];
                $policies = $this->db->select("ed.policy_detail_id,p.combo_flag,p.policy_parent_id")
                        ->from("product_master_with_subtype AS p,employee_policy_detail AS ed")
                        ->where("ed.product_name = p.id")->where_in("ed.policy_detail_id", $pid)
                        ->order_by("ed.policy_sub_type_id")->get()->result_array();
            }else{
                $policies = $this->db->select("ed.policy_detail_id,p.combo_flag,p.policy_parent_id")
                        ->from("product_master_with_subtype AS p,employee_policy_detail AS ed")
                        ->where("ed.product_name = p.id")->where("p.policy_parent_id", $parent_id)
                        ->order_by("ed.policy_sub_type_id")->get()->result_array();
            }
			//print_r($policies);exit;

            //condition for healthproinfinity 

            //echo $this->db->last_query();exit;
			//echo $product_id;exit;
			   $gmc_id = "";
			   $q = 0;
			   //r07 code 
			   if($product_id == 'T01'){
				   
				    foreach ($policies as $value)
                    {
			            $decare_proposal_ghd = $this->db->query("delete  from employee_declare_proposal_data where  emp_id = '$emp_id'");
						
						if(!empty($declare_prop))
						{
							foreach ($declare_prop as $declare_m_prop) 
							{
								 $data1_prop = array(
								"emp_id" => $emp_id,
								//"proposal_number" => '',
								"product_id" => $value['policy_parent_id'],
								"remark" => $declare_m_prop['label'],
								"proposal_declare_id" => $declare_m_prop['question_prop'],
								"format" => $declare_m_prop['format_prop'],
								"created_by" => $this->agent_id
							);
							
									if ($update_data == 'update') 
									{
										$updata_prop = array(
											"format" => $declare_m_prop['format_prop']
										);
										$this->db->where(['emp_id' => $emp_id, 'emp_proposal_declare_id' => $declare_m_prop['question_prop']]);
										$this->db->update("employee_declare_proposal_data", $updata_prop);
										
										//Add Data into logs Table	
										$logs_array['data'] = ["type" => "update_proposal_ghd_declare","req" => json_encode($data1_prop), "lead_id" => $lead_id,"product_id" => $product_id];
						                $this->Logs_m->insertLogs($logs_array);
									} 
									else 
									{
										 $this->db->insert('employee_declare_proposal_data', $data1_prop);
										 
										 //Add Data into logs Table						  
										 $logs_array['data'] = ["type" => "insert_proposal_ghd_declare","req" => json_encode($data1_prop), "lead_id" => $lead_id,"product_id" => $product_id];
						                 $this->Logs_m->insertLogs($logs_array);
									}
							}
						}
							$decare_member_proposal_ghd = $this->db->query("delete  from employee_declare_data where  emp_id = '$emp_id'");
						if(!empty($declare))
						{
							
							foreach ($declare as $declare_m) 
							{

									$data1 = array(
													"emp_id" => $emp_id,
													//"proposal_number" => '',
													"product_id" => $value['policy_parent_id'],
													"remark" => $declare_m->label,
													"p_declare_id" => $declare_m->question,
													"format" => $declare_m->format,
													"created_by" => $this->agent_id
												  );
									if ($update_data == 'update') 
									{

										$updata = array(
														"format" => $declare_m['format']
														);

										$this->db->where(['emp_id' => $emp_id, 'p_declare_id' => $declare_m['question']]);
										$this->db->update("employee_declare_data", $updata);
										
										//Add Data into logs Table							  
										$logs_array['data'] = ["type" => "update_proposal_employee_health","req" => json_encode($updata), "lead_id" => $lead_id,"product_id" => $product_id];
						                $this->Logs_m->insertLogs("logs_post_data",$logs_array);
									} 
									else 
									{
							
										 $this->db->insert('employee_declare_data', $data1);
										 
										 //Add Data into logs Table							  
										 $logs_array['data'] = ["type" => "insert_proposal_employee_health","req" => json_encode($data1), "lead_id" => $lead_id,"product_id" => $product_id];
						                 $this->Logs_m->insertLogs($logs_array);
									}
							}	
						}
                        //create individual proposals
                        //get workflow from policy detail table
                        //ends
                        //get all members in particular policy
                        $subQuery1 = $this
                            ->db
                            ->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id,
            						epd.policy_type_id, epd.policy_sub_type_id, epd.insurer_id, epd.broker_id,
            						epm.policy_detail_id, epm.family_relation_id, epm.family_id, epd.policy_no,
            						mps.policy_type_id, mpst.policy_sub_type_id, mpst.policy_sub_type_name, mpst.policy_sub_type_image_path,
            						mf.family_relation_id, mf.family_id, mf.emp_id, ic.insurer_id, ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, "0" AS fr_id, "Self" AS fr_name')
                            ->from('employee_details AS ed,
            						family_relation AS mf,
            						employee_policy_member AS epm,
            						employee_policy_detail AS epd,
            						master_policy_sub_type AS mpst,
            						master_policy_type AS mps,
            						master_insurance_companies AS ic')
                            ->where('ed.emp_id = mf.emp_id')
                            ->where('mf.family_relation_id = epm.family_relation_id')
                            ->where('epm.policy_detail_id = epd.policy_detail_id')
                            ->where('epd.policy_sub_type_id = mpst.policy_sub_type_id')
                            ->where('mpst.policy_type_id = mps.policy_type_id')
                            ->where('epd.insurer_id = ic.insurer_id')
                            ->where('epm.status != ', 'Inactive')
                            ->where('mf.family_id', 0)
                            ->where('mf.emp_id', $emp_id)->where('epd.policy_detail_id', $value['policy_detail_id'])->get_compiled_select();
                        $op = $this
                            ->db
                            ->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id, epd.policy_type_id, epd.policy_sub_type_id,
            						epd.insurer_id, epd.broker_id, epm.policy_detail_id,
            						epm.family_relation_id, epm.family_id, epd.policy_no,
            						mps.policy_type_id, mpst.policy_sub_type_id,
            						mpst.policy_sub_type_name, mpst.policy_sub_type_image_path, fr.family_relation_id,
            						fr.family_id, fr.emp_id, ic.insurer_id,
            						ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, mfr.
            						fr_id, mfr.fr_name')
                            ->from('employee_policy_member` AS `epm`, `employee_policy_detail` AS `epd`, `master_policy_type` AS `mps`, `master_policy_sub_type` AS `mpst`, `master_insurance_companies` AS `ic`, `family_relation` AS `fr`, `master_family_relation` AS `mfr`, `employee_family_details` AS `efd`')
                            ->where('epd`.`policy_detail_id` = `epm`.`policy_detail_id')
                            ->where('mps`.`policy_type_id` = `epd`.`policy_type_id')
                            ->where('mpst`.`policy_sub_type_id` = `epd`.`policy_sub_type_id')
                            ->where('ic`.`insurer_id` = `epd`.`insurer_id')
                            ->where('fr`.`family_relation_id` = `epm`.`family_relation_id')
                            ->where('fr`.`family_id` = `efd`.`family_id')
                            ->where('epm.status!=', 'Inactive')
                            ->where('efd`.`fr_id` = `mfr`.`fr_id')
                            ->where('fr`.`emp_id`', $emp_id)->where('epd.policy_detail_id', $value['policy_detail_id'])->get_compiled_select();
                        $response = $this
                            ->db
                            ->query($subQuery1 . ' UNION ALL ' . $op)->result_array();

                        //now change their status based on each policy and create proposal independently
                        //print_R($response);
                        $check = $this
                            ->obj_home
                            ->check_validations_adult_count($response[0]['familyConstruct'], $emp_id, $value['policy_detail_id']);
                        //Check validations for ".$response[0]['policy_sub_type_name']." policy"
            			//print_pre($get_lead_id);
            			if($value['policy_detail_id'] == 467 && $get_lead_id['GCI_optional'] == 'No'){
            				$check = true;
        			     }
                        if (!$check)
                        {
                            $array = ["status" => false, "message" => "Member Not Added As Per Selection in " . $response[0]['policy_sub_type_name'] . " policy"];
                            print_r(json_encode($array));
                            return;
                        }
    				//$this->db->trans_commit();
                	
                
    			
    							
                }
        		//echo 1;exit;
        		$array = ["status" => true, "message" => "Validated successfully"];
                        print_r(json_encode($array));
                        return;
		
				   
				   
				   
				   
				   
			   }
			   
			   
			   
			   $this->db->trans_start();

			foreach ($policies as $value) 
			{
				$q++;
				$policy_details_individual = $this->db->where("policy_detail_id", $value['policy_detail_id'])->get("employee_policy_detail")->row_array();
					
				if ($policy_details_individual['policy_sub_type_id'] == 1 && $value['combo_flag'] == 'Y')
				{
					$z = true;
					$gmc_id = $policy_details_individual['policy_detail_id'];
					$max_adult_count = $this->db
							->select('*')
							->from('master_broker_ic_relationship as fc')
							->where('fc.policy_id', $policy_details_individual['policy_detail_id'])
							->get()->row_array();
						
							$relationid = $max_adult_count['relationship_id'];
							$member_data = $this->obj_home->get_adult_count_new($emp_id, $gmc_id,$relationid);
					
							if(empty($member_data))
							{
								
								$array = ["status" => false, "message" => "Please Add Member"];
								print_r(json_encode($array));
								return;
							}
				}
				else
				{
							if($value['combo_flag'] != 'Y')
							{
									// $gmc_id = $policy_details_individual['policy_detail_id'];
									if($update_data == 'update' && $proposalEdit_id !='')
									{
										$ind_update_policy  = $this->db->query("select p.policy_detail_id from proposal where id ='$proposalEdit_id'")->row_array();
										$gmc_id = $ind_update_policy['policy_detail_id'];
									}
									else
									{
										$gmc_id = $policy_details_individual['policy_detail_id'];
									}
									
									
									$max_adult_count = $this->db
															->select('fc.relationship_id,fc.max_adult')
															->from('master_broker_ic_relationship as fc')
															->where('fc.policy_id', $policy_details_individual['policy_detail_id'])
															->get()->row_array();
					
									$relationid = $max_adult_count['relationship_id'];
									//print_pre($gmc_id);
									//print_pre($relationid);
									$member_data = $this->obj_home->get_adult_count_new($emp_id, $gmc_id,$relationid);
									//print_pre($member_data);exit;
									if(!empty($member_data))
									{
										$z = true;
									}
									if(count($policies) == $q)
									{
										if(!$z)
										{
											
											
											$array = ["status" => false, "message" => "Please Add Member"];
											print_r(json_encode($array));
											return;
										}
									}
							}
				}
				
				if($z == true)
				{
				   $family_construct1 = explode("+", $member_data[0]['familyConstruct']);
				
					if ($value['combo_flag'] == "Y" && $policy_details_individual['policy_sub_type_id'] != 1) 
					{
						//GET MAX ADULT COUNT OF COMBO POLICY
						$max_adult_count = $this->db
								->select('fc.relationship_id,fc.max_adult')
								->from('master_broker_ic_relationship as fc')
								->where('fc.policy_id', $policy_details_individual['policy_detail_id'])
								->get()->row_array();
			  
						if ($max_adult_count['max_adult'] . "A" == $family_construct1[0] || $max_adult_count['max_adult'] . "A" == $family_construct1[0]) 
						{
						   $member = $max_adult_count['max_adult'] . "A";
						   $relationid = $max_adult_count['relationship_id'];
						} 
						else 
						{
							$member = $family_construct1[0];
							$relationid = $max_adult_count['relationship_id'];
						}
				   
						$member_data = $this->obj_home->get_adult_count_new($emp_id, $gmc_id, $relationid);
					 
						$premium_and_sum_insured = $this->db
								->select('fc.PremiumServiceTax')
								->from('family_construct_wise_si as fc')
								->where('fc.policy_detail_id', $policy_details_individual['policy_detail_id'])
								->where('fc.family_type', $member)
								->where('fc.sum_insured', $member_data[0]['policy_mem_sum_insured'])
								->get()->row_array();
							$policy_detail_id =   $policy_details_individual['policy_detail_id'];
							$delete_data = $this->db->query("delete epm from employee_policy_member as epm,family_relation as fr  where fr.family_relation_id = epm.family_relation_id AND epm.policy_detail_id = '$policy_detail_id' AND fr.emp_id = '$emp_id'");
						//enter into combo policy
						for ($i = 0; $i < count($member_data); $i++)
						{
							$member_arrays = [
							"policy_detail_id" => $value['policy_detail_id'],
							"family_relation_id" => $member_data[$i]['family_relation_id'],
							"policy_mem_sum_insured" => $member_data[$i]['policy_mem_sum_insured'],
							"policy_mem_sum_premium" =>  $premium_and_sum_insured['PremiumServiceTax'],
							"policy_mem_gender" => $member_data[$i]['policy_mem_gender'],
							"policy_mem_dob" => $member_data[$i]['policy_mem_dob'],
							"age" => $member_data[$i]['age'],
							"age_type" => $member_data[$i]['age_type'],
							"member_status" => "pending",
							"fr_id" => $member_data[$i]['fr_id'],
							"policy_member_first_name" => $member_data[$i]['firstname'],
							"policy_member_last_name" => $member_data[$i]['lastname'],
							"familyConstruct" => $member
							];

						  $this->obj_m->insert_policy_member($member_arrays);
						  
						  //Add Data into logs Table	
					  	  $logs_array['data'] = ["type" => "proposal_insured_member","req" => json_encode($member_arrays), "lead_id" => $lead_id,"product_id" => $product_id];
						  $this->Logs_m->insertLogs($logs_array);
						}
					}
				}
				$decare_proposal_ghd = $this->db->query("delete  from employee_declare_proposal_data where  emp_id = '$emp_id'");
						
						if(!empty($declare_prop))
						{
							foreach ($declare_prop as $declare_m_prop) 
							{
								 $data1_prop = array(
								"emp_id" => $emp_id,
								//"proposal_number" => '',
								"product_id" => $value['policy_parent_id'],
								"remark" => $declare_m_prop['label'],
								"proposal_declare_id" => $declare_m_prop['question_prop'],
								"format" => $declare_m_prop['format_prop'],
								"created_by" => $this->agent_id
							);
							
									if ($update_data == 'update') 
									{
										$updata_prop = array(
											"format" => $declare_m_prop['format_prop']
										);
										$this->db->where(['emp_id' => $emp_id, 'emp_proposal_declare_id' => $declare_m_prop['question_prop']]);
										$this->db->update("employee_declare_proposal_data", $updata_prop);
										
										//Add Data into logs Table	
										$logs_array['data'] = ["type" => "update_proposal_ghd_declare","req" => json_encode($data1_prop), "lead_id" => $lead_id,"product_id" => $product_id];
						                $this->Logs_m->insertLogs($logs_array);
									} 
									else 
									{
										 $this->db->insert('employee_declare_proposal_data', $data1_prop);
										 
										 //Add Data into logs Table						  
										 $logs_array['data'] = ["type" => "insert_proposal_ghd_declare","req" => json_encode($data1_prop), "lead_id" => $lead_id,"product_id" => $product_id];
						                 $this->Logs_m->insertLogs($logs_array);
									}
							}
						}
						$decare_member_proposal_ghd = $this->db->query("delete  from employee_declare_data where  emp_id = '$emp_id'");
						if(!empty($declare))
						{
							
							foreach ($declare as $declare_m) 
							{

									$data1 = array(
													"emp_id" => $emp_id,
													//"proposal_number" => '',
													"product_id" => $value['policy_parent_id'],
													"remark" => $declare_m->label,
													"p_declare_id" => $declare_m->question,
													"format" => $declare_m->format,
													"created_by" => $this->agent_id
												  );
									if ($update_data == 'update') 
									{

										$updata = array(
														"format" => $declare_m['format']
														);

										$this->db->where(['emp_id' => $emp_id, 'p_declare_id' => $declare_m['question']]);
										$this->db->update("employee_declare_data", $updata);
										
										//Add Data into logs Table							  
										$logs_array['data'] = ["type" => "update_proposal_employee_health","req" => json_encode($updata), "lead_id" => $lead_id,"product_id" => $product_id];
						                $this->Logs_m->insertLogs("logs_post_data",$logs_array);
									} 
									else 
									{
							
										 $this->db->insert('employee_declare_data', $data1);
										 
										 //Add Data into logs Table							  
										 $logs_array['data'] = ["type" => "insert_proposal_employee_health","req" => json_encode($data1), "lead_id" => $lead_id,"product_id" => $product_id];
						                 $this->Logs_m->insertLogs($logs_array);
									}
							}	
						}
		
			}
				//create individual proposals
					//get workflow from policy detail table
					//ends
					//get all members in particular policy
				   $subQuery1 = $this->db
				   ->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id,
					epd.policy_type_id, epd.policy_sub_type_id, epd.insurer_id, epd.broker_id,
					epm.policy_detail_id, epm.family_relation_id, epm.family_id, epd.policy_no,
					mps.policy_type_id, mpst.policy_sub_type_id, mpst.policy_sub_type_name, mpst.policy_sub_type_image_path,
					mf.family_relation_id, mf.family_id, mf.emp_id, ic.insurer_id, ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, "0" AS fr_id, "Self" AS fr_name')
				   ->from('employee_details AS ed,
					family_relation AS mf,
					employee_policy_member AS epm,
					employee_policy_detail AS epd,
					master_policy_sub_type AS mpst,
					master_policy_type AS mps,
					master_insurance_companies AS ic')
				   ->where('ed.emp_id = mf.emp_id')->where('mf.family_relation_id = epm.family_relation_id')
				   ->where('epm.policy_detail_id = epd.policy_detail_id')->where('epd.policy_sub_type_id = mpst.policy_sub_type_id')
				   ->where('mpst.policy_type_id = mps.policy_type_id')->where('epd.insurer_id = ic.insurer_id')
				   ->where('epm.status != ', 'Inactive')->where('mf.family_id', 0)->where('mf.emp_id', $emp_id)
				   ->where('epd.policy_detail_id', $value['policy_detail_id'])->get_compiled_select();
				   
				   $op = $this->db->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id, epd.policy_type_id, epd.policy_sub_type_id,
					epd.insurer_id, epd.broker_id, epm.policy_detail_id,
					epm.family_relation_id, epm.family_id, epd.policy_no,
					mps.policy_type_id, mpst.policy_sub_type_id,
					mpst.policy_sub_type_name, mpst.policy_sub_type_image_path, fr.family_relation_id,
					fr.family_id, fr.emp_id, ic.insurer_id,
					ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, mfr.
					fr_id, mfr.fr_name')
				   ->from('employee_policy_member` AS `epm`, `employee_policy_detail` AS `epd`, `master_policy_type` AS `mps`, `master_policy_sub_type` AS `mpst`, `master_insurance_companies` AS `ic`, `family_relation` AS `fr`, `master_family_relation` AS `mfr`, `employee_family_details` AS `efd`')
				   ->where('epd`.`policy_detail_id` = `epm`.`policy_detail_id')
				   ->where('mps`.`policy_type_id` = `epd`.`policy_type_id')
				   ->where('mpst`.`policy_sub_type_id` = `epd`.`policy_sub_type_id')
				   ->where('ic`.`insurer_id` = `epd`.`insurer_id')
				   ->where('fr`.`family_relation_id` = `epm`.`family_relation_id')
				   ->where('fr`.`family_id` = `efd`.`family_id')->where('epm.status!=', 'Inactive')
				   ->where('efd`.`fr_id` = `mfr`.`fr_id')->where('fr`.`emp_id`', $emp_id)
				   ->where('epd.policy_detail_id', $value['policy_detail_id'])->get_compiled_select();
				   
				   $response = $this->db->query($subQuery1 . ' UNION ALL ' . $op)->result_array();
		   
				   //now change their status based on each policy and create proposal independently
				   $check = $this->obj_home->check_validations_adult_count($response[0]['familyConstruct'],$emp_id,$value['policy_detail_id']);
		
					//Check validations for ".$response[0]['policy_sub_type_name']." policy"
					if(!$check)
					{
						$array = ["status" => false, "message" => "Member Not Added As Per Selection in ".$response[0]['policy_sub_type_name']." policy"];
						print_r(json_encode($array));
						return;
					}


			$this->db->trans_commit();
			$array = ["status" => true];
			print_r(json_encode($array));
		}
	}	
	public function thank_you_data()
	{

		$emp_id = $this->emp_id;

		$query = $this->db->query("select p.proposal_no,ed.lead_id from proposal as p left join employee_details as ed ON p.emp_id = ed.emp_id where ed.emp_id ='$emp_id'");
		if($query->num_rows()>0)
		{
			
			$data= $query->result_array();
		
		}
		if($create_proposal == 'create_proposal')
		{
			$data['proposal_data'] = $query->result_array();
			$data['create_proposal'] = 'create_proposal';
			echo   $string = $this->load->telesales_template("thankyou",compact('data'),true);
		}
		else
		{
			echo   $string = $this->load->telesales_template("thankyou",compact('data'),true);
		}

		
	}
	public function check_proposal()
	{
		$emp_id = $this->emp_id;
		 $data = $this->obj_home->get_common_data();
		$check_proposal = $this->db->query("select emp_id from proposal where emp_id = '$emp_id' and status !='Payment Pending'")->row_array();
		$data['audit_data'] = $this->db->select('*')
         ->from('employee_disposition ed')
         ->join('disposition_master dm', 'ed.disposition_id = dm.id')
		 ->where('dm.Dispositions','Payment pending')
		 ->where('dm.Sub-dispositions','PG link triggered')
		 ->order_by('ed.id','desc')
		->get()
		->row_array();
		//echo $this->db->last_query();
		$check_proposal_new = $this->db->query("select emp_id from proposal where emp_id = '$emp_id'")->row_array();
        if(count($check_proposal_new)>0)
        {
            $data['proposal_status'] = 'Yes'; 
        }
        else
        {$data['proposal_status'] = 'No';}

		if(count($check_proposal)>0)
		{
			$data['status'] = 'Yes'; 
		}
		else
		{$data['status'] = 'No';}
		echo json_encode($data);
	}
    public function check_proposal_created()
    {
        $emp_id = $this->emp_id;
         $data = $this->obj_home->get_common_data();
        $check_proposal = $this->db->query("select emp_id from proposal where emp_id = '$emp_id' and status !='Payment Pending'")->row_array();
        $data['audit_data'] = $this->db->select('*')
         ->from('employee_disposition ed')
         ->join('disposition_master dm', 'ed.disposition_id = dm.id')
         ->where('dm.Dispositions','Payment pending')
         ->where('dm.Sub-dispositions','PG link triggered')
         ->order_by('ed.id','desc')
        ->get()
        ->row_array();
        //echo $this->db->last_query();
        $check_proposal_new = $this->db->query("select emp_id from proposal where emp_id = '$emp_id'")->row_array();
        if(count($check_proposal_new)>0)
        {
            $data['proposal_status'] = 'Yes'; 
        }
        else
        {$data['proposal_status'] = 'No';}

        if(count($check_proposal)>0)
        {
            $data['status'] = 'Yes'; 
        }
        else
        {$data['status'] = 'No';}
        return $data;
    }
	public function tele_agent_data_insert()
	{

		$this->form_validation->set_error_delimiters('','');
		$this->form_validation->set_rules('axis_location', 'Axis Location', 'required|trim');
		$this->form_validation->set_rules('axis_vendor', 'Axis Vendor', 'required|trim');
		$this->form_validation->set_rules('axis_lob', 'Axis Lob', 'required|trim');
		$this->form_validation->set_rules('agent_id', 'Agent Code', 'required|trim');
		$this->form_validation->set_rules('imd_code', 'IMD Code', 'required|trim');
		if ($this->form_validation->run() == FALSE)
		{
			 $z = validation_errors();
			 $validation_err= ["status" => false, "message" => $z];
             print_r(json_encode($validation_err));   
		}
		else
		{
			$emp_id = $this->emp_id;
			$get_lead_id = $this->db->query("select lead_id from employee_details where emp_id = '$emp_id' ")->row_array();
			if($get_lead_id['lead_id'] != 0)
			{
				$lead_id = $get_lead_id['lead_id'];
				$product_id = 'R06';
			}
			
			$axis_location =  $this->input->post('axis_location', true);
			$axis_vendor =  $this->input->post('axis_vendor', true);
			$axis_lob =  $this->input->post('axis_lob', true);
			$av_code =  $this->input->post('avCode', true);
			$agent_id =  $this->input->post('agent_id', true);
			$agent_name =  $this->input->post('agent_name', true);
			$imd_code =  $this->input->post('imd_code', true);
				$emp_agent_data = array(
			 
										'axis_location' => $axis_location,
										'axis_vendor' => $axis_vendor,
										'axis_lob' => $axis_lob,
										'av_code' => $av_code,
										'agent_id' => $agent_id,
										'agent_name' => $agent_name,
										'imd_code'=>$imd_code
										

									);
		    $agent_data = $this->obj_home->add_agent_emp_details($emp_id,$emp_agent_data);		
		
			if($agent_data == true)
			{	
				//Add Data into logs Table							
				$logs_array['data'] = ["type" => "insert_agent_details","req" => json_encode($emp_agent_data), "lead_id" => $lead_id,"product_id" => $product_id];
				$this->Logs_m->insertLogs($logs_array);
			}
			$insert_data= ["status" => true, "message" => ''];
             print_r(json_encode($insert_data));  
		}
	}
	public function tele_emp_data_insert()
	{

		$this->form_validation->set_error_delimiters('','');

		$this->form_validation->set_rules('comAdd', 'Communication Address', 'required|trim');
		$this->form_validation->set_rules('pin_code', 'Pin Code', 'required|trim');
		$this->form_validation->set_rules('city', 'City', 'required|trim');
		$this->form_validation->set_rules('state', 'State', 'required|trim');
		$this->form_validation->set_rules('mobile_no2', 'mobile_no2', 'trim');
		$this->form_validation->set_rules('email', 'Email', 'required|trim');
		
	

		if ($this->form_validation->run() == FALSE)
		{
			 $z = validation_errors();
			 $validation_err= ["status" => false, "message" => $z];
             print_r(json_encode($validation_err));   
		}
		else
		{
			$emp_id = $this->emp_id;
			$get_lead_id = $this->db->query("select lead_id from employee_details where emp_id = '$emp_id' ")->row_array();
			if($get_lead_id['lead_id'] != 0)
			{
				$lead_id = $get_lead_id['lead_id'];
				$product_id = 'R06';
			}
			
			$address =  $this->input->post('comAdd', true);
			$address2 =  $this->input->post('comAdd2', true);
			$address3 =  $this->input->post('comAdd3', true);
			$pinCode =  $this->input->post('pin_code', true);
			$city =  $this->input->post('city', true);
			$state =  $this->input->post('state', true);
			$mobile_no2 =  $this->input->post('mobile_no2', true);
			$email =  $this->input->post('email', true);
            $firstname =  $this->input->post('firstname', true);
            $lastname =  $this->input->post('lastname', true);
            $gender1  = $this->input->post('gender1', true);
            $salutation  = $this->input->post('salutation', true);
            $dob  = $this->input->post('dob', true);
            if($salutation == 1){
                $salutation = 'Mr';
            }else if($salutation == 2){
                $salutation = 'Mrs';
            }else if($salutation == 3){
                $salutation = 'Ms';
            }
							$emp_data_insert = array(
                                'bdate' => $dob,
                                        'salutation' => $salutation,
                                        'emp_firstname' => $firstname,
                                        'emp_lastname' => $lastname,
                                        'gender' => $gender1,
										'address'=>$address,
										'comm_address'=>$address2,
										'comm_address1'=>$address3,
										'emp_pincode'=>$pinCode,
										'emp_city'=>$city,
										'emp_state'=>$state,
										'emg_cno'=> $mobile_no2,
										'email'=> $email
									);
		    $emp_data = $this->obj_home->add_agent_emp_details($emp_id,$emp_data_insert);		
	//echo $emp_data;exit;	
			if($emp_data == true)
			{	
				//Add Data into logs Table							
				$logs_array['data'] = ["type" => "insert_agent_details","req" => json_encode($emp_data_insert), "lead_id" => $lead_id,"product_id" => $product_id];
				$this->Logs_m->insertLogs($logs_array);
			}
            $update = 0;
            //if(isset($_POST['self_edit_clicked']) && $_POST['self_edit_clicked'] != 0){
                //check if self data is inserted in policy member
                $qry = "SELECT epm.policy_member_id,epm.fr_id FROM family_relation AS fr, employee_policy_member AS epm WHERE fr.family_relation_id = epm.family_relation_id AND epm.fr_id = 0 AND fr.emp_id = ".$emp_id;
    			$res = $this->db->query($qry)->result_array();
//echo $this->db->last_query();exit;
                if(!empty($res)){
                    foreach ($res as $key => $value) {
                        if($value['policy_member_id'] != ''){
                            //update self details
                            $bday = new DateTime($dob); // Your date of birth
                            $today = new Datetime(date('d-m-Y'));
                            $diff = $today->diff($bday);
                            $updateArr = array(//"policy_mem_salutation" => $salutation,
                                //"policy_mem_gender" => $gender1,
                                //"policy_mem_dob"=>$dob,
                                "policy_member_first_name" => $firstname,
                                "policy_member_last_name" => $lastname,
                                //"age" => $diff->y
                                );
                            $this->db->where('policy_member_id', $value['policy_member_id']);
                            $this->db->update('employee_policy_member', $updateArr);
                            //if(isset($_POST['self_edit_clicked']) && $_POST['self_edit_clicked'] != 0){
                                $update =1;
                            //}
                           //$this->db->delete('employee_policy_member', array('policy_member_id' => $value['policy_member_id']));
                        }
                    }
                    
                }
                //update data in proposal member table
                $qry1 = "SELECT epm.proposal_member_id,epm.fr_id FROM family_relation AS fr, proposal_member AS epm WHERE fr.family_relation_id = epm.family_relation_id AND epm.fr_id = 0 AND fr.emp_id =".$emp_id;
                $res1 = $this->db->query($qry1)->result_array();
                if(!empty($res1)){
                    foreach ($res1 as $key => $value) {
                        if($value['proposal_member_id'] != ''){
                            //update self
                            $updateArr = array(
                                "policy_member_first_name" => $firstname,
                                "policy_member_last_name" => $lastname,
                                );
                            $this->db->where('proposal_member_id', $value['proposal_member_id']);
                            $this->db->update('proposal_member', $updateArr);
                            //if(isset($_POST['self_edit_clicked']) && $_POST['self_edit_clicked'] != 0){
                                $update =1;
                            //}
                           //$this->db->delete('employee_policy_member', array('policy_member_id' => $value['policy_member_id']));
                        }
                    }
                    
                }
            //}
            $proposal_status = $this->check_proposal_created();
	  //  print_pre($proposal_status);exit;
            $insert_data= ["status" => true, "message" => '', "update" => $update, "proposal_status" => $proposal_status['proposal_status']];
            print_r(json_encode($insert_data));  
		}
	
	}
		
	public function tele_nominee_data_insert()
	{
		$this->form_validation->set_error_delimiters('','');
		$this->form_validation->set_rules('nominee_relation', 'Nominee Relation', 'required|trim');
		$this->form_validation->set_rules('nominee_fname', 'Nominee First Name', 'required|trim');
		//$this->form_validation->set_rules('nominee_lname', 'Nominee Last Name', 'required|trim');
		$this->form_validation->set_rules('nominee_gender', 'Nominee Gender', 'required|trim');
		$this->form_validation->set_rules('nominee_dob', 'Nominee DOB', 'required|trim');
		$this->form_validation->set_rules('nominee_salutation', 'Nominee Salutation', 'required|trim');


		if ($this->form_validation->run() == FALSE)
		{
			 $z = validation_errors();
			 $validation_err= ["status" => false, "message" => $z];
             print_r(json_encode($validation_err));   
		}
		else
		{
			$emp_id = $this->emp_id;
			$parent_id = $this->parent_id;
			$nominee_data_all = $this->input->post(null,true);
			if(($nominee_data_all != null) || !empty($nominee_data_all))
			$nominee_relation =  $this->input->post('nominee_relation', true);
			$nominee_fname =  $this->input->post('nominee_fname', true);
			$nominee_lname =  $this->input->post('nominee_lname', true);
			$nominee_gender =  $this->input->post('nominee_gender', true);
			$nominee_dob =  $this->input->post('nominee_dob', true);
			$nominee_contact =  $this->input->post('nominee_contact', true);
			$nominee_email =  $this->input->post('nominee_email', true);
			$nominee_salutation = $this->input->post('nominee_salutation', true);
			$get_lead_id = $this->db->query("select lead_id,product_id from employee_details where emp_id = '$emp_id' ")->row_array();

			if($get_lead_id['lead_id'] != 0)
			{
				$lead_id = $get_lead_id['lead_id'];
				$product_id = $get_lead_id['product_id'];

			}
			
			// add nominee
			$this->db->query("Delete from member_policy_nominee where emp_id = '$emp_id'");
			
			$nominee_data = array(
									'fr_id' => $nominee_relation,
									'nominee_fname' =>$nominee_fname,
									'nominee_lname'=>$nominee_lname,
									'nominee_gender'=>$nominee_gender,
									'nominee_dob'=>$nominee_dob,
									'nominee_contact'=>$nominee_contact,
									'nominee_email'=>$nominee_email,
									'nominee_salutation'=>$nominee_salutation,
									'emp_id'=>$emp_id,
									'confirmed_flag'=>'N',
									'status'=>'active'
								);
			$nominee_data_insert = $this->obj_home->add_nominee($nominee_data);
			if($nominee_data_insert == true)
			{
				//Add Data into logs Table							
				$logs_array['data'] = ["type" => "insert_nominee_details","req" => json_encode($nominee_data), "lead_id" => $lead_id,"product_id" => $product_id];
				$this->Logs_m->insertLogs($logs_array);			
			} 
			$insert_data= ["status" => true, "message" => ''];
             print_r(json_encode($insert_data));  
		}
	
	}
	public function summary()
	{
        
		if($_GET['product_id'] == 'T01'){
			$parent_id = 'test123';
		}else{
			$parent_id = $this->parent_id;
		}
		
		//upendra - maker/checker - 30-07-2021
	/*	
		if(isset($_GET['leadid'])){
			
			$lead_id = $_GET['leadid'];
			$lead_id = encrypt_decrypt_password($lead_id,"D");

			$emp_id_query = $this
			->db
			->query("SELECT emp_id
			FROM 
			employee_details 
			WhERE lead_id = '$lead_id'")->row_array();

			$emp_id = $emp_id_query['emp_id'];

			$telSalesSession = $this->session->userdata('telesales_session');

			$telSalesSession['emp_id'] = $emp_id;
			$this->session->set_userdata($telSalesSession);

			// print_r($telSalesSession);

			$this->emp_id = $telSalesSession['emp_id'];

		}
	 */	

		$data = $this->obj_home->get_common_data();		
		$data['agent_details'] = $this->obj_home->get_agent_details();
		$data['employee_declaration'] = $this->obj_home->health_declaration_emp_data($parent_id);
		$data['policy_details'] = $this->obj_home->get_policy_data_emp($parent_id);
		$data['redirectFrom_email'] = 'No';
		$emp_id_encrypt = encrypt_decrypt_password($this->emp_id,'E');
		$data['emp_id'] = $emp_id_encrypt;
        $data['sum_insured_data'] = $this->db->query("select  sum(epm.policy_mem_sum_premium) as premium,epm.policy_mem_sum_insured,epm.familyConstruct  from employee_policy_member as epm,family_relation as fr where fr.family_relation_id = epm.family_relation_id AND fr.emp_id  = '".$this->emp_id."' AND epm.fr_id = 0 group by fr.emp_id")->row_array();
		// echo $this->db->last_query();exit;
        $data['disposition']  = $this->db->select('*')
         ->from('employee_disposition ed')
         ->join('disposition_master dm', 'ed.disposition_id = dm.id')
		 ->where('emp_id',$this->emp_id)
		 ->order_by('ed.id','desc')
		->get()
		
		->row_array();
		$data['parent_id'] = $parent_id;
		$data['remarks'] = $parent_id;
		
		//echo 1;exit;
		
		// print_pre($data);exit;
		$string = $this->load->telesales_template("summary",compact('data'),true);
	}

		//upendra - maker/checker - 30-07-2021
		public function maker_checker_update(){

			extract($this
			->input
			->post());
	
			$lead_id = encrypt_decrypt_password($lead_id,"D");
	
			$update_remark =
			$this->db->set('makerchecker', 'checker');
			$this->db->set('lead_flag', NULL);
			$this->db->where('lead_id', $lead_id);
			$this->db->update('employee_details');
	
		}


	//updated by upendra on 09-04-2021 
	public function tele_get_member_dropdown()
	{

		$lead_id_1 = $this->input->post('lead_id_1');
		$lead_id_1 = encrypt_decrypt_password($lead_id_1,'D');
		


			$output_query = $this
			->db
			->query("SELECT ed.emp_id,mfr.fr_id, mfr.fr_name, mfr.relation_code, epm.policy_detail_id, epm.familyConstruct,epm.family_relation_id
				 FROM 
			employee_details  ed, family_relation fr, 
			master_family_relation mfr,employee_policy_member epm
			WHERE ed.emp_id = fr.emp_id 
			AND fr.family_relation_id = epm.family_relation_id 
			AND mfr.fr_id = epm.fr_id 
			AND ed.lead_id = '$lead_id_1' GROUP BY epm.family_relation_id; ")->result_array();


			$arr_size = sizeof($output_query);
			
			$family_construct = $output_query[0]['familyConstruct'];

			

			$family_construct = explode('+', $family_construct);
			$selected_child_count = (!empty($family_construct[1])) ? $family_construct[1][0] : 0;
			$selected_adult_count = (!empty($family_construct[0])) ? $family_construct[0][0] : 0;

			$total_members = $selected_child_count + $selected_adult_count;

			$output = '';
			
			$chk_dup_arr = [];
			foreach ($output_query as $oq)
			{
				array_push($chk_dup_arr,$oq['fr_id']);
			}

			function array_is_unique($array) {
				return array_unique($array) == $array;
			 }

			$is_arr_unique =  array_is_unique($chk_dup_arr) ? "yes" : "no";
			$kid_counter = "";
			if($is_arr_unique == 'no'){
				$kid_counter = 1;
			}			


			$sr_no = 1;
			$sr_count = 0;
			
			$output .= "<br><br><table class='table-bordered text-center'>";
			



			$output .= "
				<tr>
					<td>Sr No</td>
					<td>Member</td>
					<td>Answer</td>
					<td width='60%' >Remark</td>
				<tr>
			";

			foreach ($output_query as $oq)
			{
			
				$relation_code_m = trim($oq['relation_code']);
				$fr_name = $oq['fr_name'];
				if(($relation_code_m == 'R003') || ($relation_code_m == 'R004')){
					$fr_name = $fr_name.' '.$kid_counter;
				}

				$output .= '<tr class="count_sr_ghd_infi">';
				$output .= '<td>'.$sr_no.'</td>';
				$output .= '<td>'.$fr_name.'</td>';

				$output .= '<td><div style="display: flex;"><div class="custom-control custom-radio">';
				$output .= '<input type="radio" id="yes'.$sr_count.'" value="yes" name="ghd_mem_radio_'.$sr_count.'"  class="rd_ghd_mem custom-control-input" data="'.$oq['fr_name'].'"  data-rc="'.$oq['family_relation_id'].'"> <label class="custom-control-label" for="yes'.$sr_count.'">Yes</label> </div>
				<div class="custom-control custom-radio ml-2">
				<input type="radio"  id="no'.$sr_count.'"  value="no" name="ghd_mem_radio_'.$sr_count.'"  class="rd_ghd_mem custom-control-input " data="'.$oq['fr_name'].'" data-rc="'.$oq['family_relation_id'].'"  checked> <label class="custom-control-label" for="no'.$sr_count.'">No</label>';
				$output .= '</div></div></td>';

				$output .= '<td>';
				$output .= '<textarea maxlength="100" name="ghd_mem_text_'.$sr_count.'"  class="ghd_mem_text_'.$sr_count.' textAreaGHD"  id="ghd_mem_text_'.$sr_count.'"  style="display:none;width:100%;"></textarea>';
				$output .= '</td>';

				$output .= '</tr>';
				$sr_no++;
				$sr_count++;

				if($is_arr_unique == 'no' && (($relation_code_m=="R003") || ($relation_code_m=="R004"))){
					$kid_counter++;
				}
			



			}

			$output .= "</table>";
			echo $output;
	}





}
