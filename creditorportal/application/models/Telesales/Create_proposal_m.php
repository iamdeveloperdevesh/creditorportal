<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Create_proposal_m extends CI_Model
{

    private $emp_id;

    function __construct()
    {
        parent::__construct();
        
		if (!$this->session->userdata('telesales_session')) 
		{
            redirect('login');
        }
		$this->load->model("Logs_m", "Logs_m", true);
		$telSalesSession = $this->session->userdata('telesales_session');

		$this->agent_id = encrypt_decrypt_password($telSalesSession['agent_id'],'D');
		$this->emp_id = $telSalesSession['emp_id'];
		$this->parent_id = $telSalesSession['parent_id'];

		     //upendra - maker/checker - 30-07-2021
		// 	 if(isset($_GET['leadid']) && $this->uri->segment(1)=="tele_summary"){

		// 		$lead_id = $_GET['leadid'];
		// 		$lead_id = encrypt_decrypt_password($lead_id,"D");

		// 		$emp_id_query = $this
		// 		->db
		// 		->query("SELECT emp_id
		// 		FROM
		// 		employee_details
		// 		WhERE lead_id = '$lead_id'")->row_array();

		// 		$emp_id = $emp_id_query['emp_id'];

		// 		$telSalesSession = $this->session->userdata('telesales_session');

		// 		$telSalesSession['emp_id'] = $emp_id;
		// 		$this->session->set_userdata($telSalesSession);

		// 		// print_r($telSalesSession);

		// 		$this->emp_id = $telSalesSession['emp_id'];

		// }

		
    }
    


    /*healthpro changes created new function*/
    /**
	* This function get all the details of Family Members.
	*
	* @author Ankita  <ankita.badak@fyntune.com>
	* @return Data in Array Format.
	*/
    function get_all_member_data_new_gpa_optional($action,$ghi_policy_detail_id,$sum_insured,$family_construct,$update_ghi = false,$gpa =false,$fr_id,$emp_id,$only_gci_insert = false){
		// echo "in";exit;
		$parent_id  = $this->db->select('parent_policy_id')->from('employee_policy_detail')->where('policy_detail_id',$ghi_policy_detail_id)->get()->row_array();

	    $gpa_policy_detail_id = $this->db->select('policy_detail_id,suminsured_type')->from('employee_policy_detail')
	    ->where('parent_policy_id',$parent_id['parent_policy_id'])
	    ->where('policy_sub_type_id',2)
	    ->get()
	    ->row_array();


	    $all_members_ghi  = $this->get_all_member_data_new($emp_id, $ghi_policy_detail_id,true);
	    
	   	$ageArr = [];//array_column($all_members_ghi, 'age');
	    foreach ($all_members_ghi as $key => $ageval) {
	        if($ageval['fr_id'] == 0 || $ageval['fr_id'] == 1){
	            array_push($ageArr, $ageval['age']);
	        }  
	    }
        // print_pre($all_members_ghi);exit;
	    $all_members = [];
		$new_preium = [];
	    for($i =0; $i < count($all_members_ghi); $i++){

		
	        $data = [];
			if($all_members_ghi[$i]['fr_id'] ==  2 || $all_members_ghi[$i]['fr_id'] ==  3){
				$age_new = 18; 
			}else{
				$age_new = $all_members_ghi[$i]['age'];
			}
	        $data['policy_mem_sum_insured'] = $sum_insured;
	        $data['policy_mem_gender'] = $all_members_ghi[$i]['gender'];
	        $data['policy_mem_dob'] = $all_members_ghi[$i]['dob'];
	        $data['age'] = $all_members_ghi[$i]['age'];
	        $data['age_type'] = $all_members_ghi[$i]['age_type'];
	        $data['fr_id'] = $all_members_ghi[$i]['fr_id'];
	        $data['policy_member_first_name'] = $all_members_ghi[$i]['firstname'];
	        $data['policy_member_last_name'] = $all_members_ghi[$i]['lastname'];
	        $data['policy_detail_id'] = $ghi_policy_detail_id;
	        $data['family_relation_id'] = $all_members_ghi[$i]['family_relation_id'];
	        $data['familyConstruct'] = $family_construct;
	        $data['policy_member_id'] = $all_members_ghi[$i]['policy_member_id'];
	        $data['policy_member_email_id'] = $all_members_ghi[$i]['policy_member_email_id'];
	        $data['policy_member_mob_no'] = $all_members_ghi[$i]['policy_member_mob_no'];
	        $max_age = max($ageArr);
	        $data['policy_mem_sum_premium'] = $this->get_premium_from_policy($ghi_policy_detail_id,$sum_insured,$family_construct,$max_age);
			$new_preium[$ghi_policy_detail_id]=$data['policy_mem_sum_premium'];
	        if($update_ghi){
			//echo 1;exit;
	            $this->db->where('policy_member_id', $all_members_ghi[$i]['policy_member_id']);
	            $this->db->update('employee_policy_member',$data);
	            $logs_array['data'] = ["type" => "update_ghi", "req" => json_encode($all_members_ghi),"res" => json_encode($this->db->last_query()) , "lead_id" => $all_members_ghi[$i]['family_relation_id'], "product_id" => $product_id];
	        	$this->Logs_m->insertLogs($logs_array);
	            $data['policy_sub_type_id'] = 1;
	            $relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
	            $data['relationship'] = $relation['fr_name'];

	            $all_members[] = $data;
	        }else{
	        	//echo 2;exit;
	            $data['policy_sub_type_id'] = 1;
	            
	            $relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
	            $data['relationship'] = $relation['fr_name'];
	            $all_members[] = $data;
	        }


	        $gpa_go_ahead = ($fr_id !== '') ? (($fr_id == $all_members_ghi[$i]['fr_id']) ? true : false)   : true;
	        $premium_gpa = $this->get_premium_from_policy($gpa_policy_detail_id['policy_detail_id'],$sum_insured,$family_construct,$all_members_ghi[$i]['age']);
	        
	        $familyConstructs = explode('+',$family_construct)[0];
	        if($familyConstructs == '2A')
	        {
	            $premium_gpa = $premium_gpa/2;
	        }

	        $gpa_data = [];
	        $gpa_data['policy_mem_sum_insured'] = $sum_insured;
	        $gpa_data['policy_mem_gender'] = $all_members_ghi[$i]['gender'];
	        $gpa_data['policy_mem_dob'] = $all_members_ghi[$i]['dob'];
	        $gpa_data['age'] = $all_members_ghi[$i]['age'];
	        $gpa_data['age_type'] = $all_members_ghi[$i]['age_type'];

	        $gpa_data['fr_id'] = $all_members_ghi[$i]['fr_id'];
	        $gpa_data['policy_member_first_name'] = $all_members_ghi[$i]['firstname'];
	        $gpa_data['policy_member_last_name'] = $all_members_ghi[$i]['lastname'];
	        $gpa_data['policy_detail_id'] = $gpa_policy_detail_id['policy_detail_id'];
	        $gpa_data['family_relation_id'] = $all_members_ghi[$i]['family_relation_id'];
	        $data['policy_member_id'] = $all_members_ghi[$i]['policy_member_id'];
	        $gpa_data['familyConstruct'] =  explode('+',$family_construct)[0];
	        $gpa_data['policy_member_email_id'] = $all_members_ghi[$i]['policy_member_email_id'];
	        $gpa_data['policy_member_mob_no'] = $all_members_ghi[$i]['policy_member_mob_no'];
	        $gpa_data['policy_mem_sum_premium'] = $premium_gpa;//$this->get_premium_from_policy($gpa_policy_detail_id['policy_detail_id'],$sum_insured,$family_construct,$all_members_ghi[$i]['age']);
			$new_preium[$gpa_policy_detail_id['policy_detail_id']]=$gpa_data['policy_mem_sum_premium'];
	        // print_pre($gpa_data);exit;

	        if($only_gci_insert){
	            $gpa_go_ahead = true;
	            $action1 = 'insert';
	        }else{
	            $action1 = '';
	        }
	        // echo $gpa_go_ahead.'---'.$gpa.'---'.$all_members_ghi[$i]['fr_id'];exit;
	        if($gpa_go_ahead  && $gpa && ($all_members_ghi[$i]['fr_id'] == 1 || $all_members_ghi[$i]['fr_id'] == 0)){
	           //echo "if - ".$action;exit;
	        if($action == 'update' && $action1 == ''){
	        $this->db->where('family_relation_id', $all_members_ghi[$i]['family_relation_id']);
	        $this->db->where('policy_detail_id', $gpa_policy_detail_id['policy_detail_id']);
	        $this->db->update('employee_policy_member',$gpa_data);

	        $gpa_data['policy_sub_type_id'] = 2;
	        $relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
	        $gpa_data['relationship'] = $relation['fr_name'];



	        $all_members[] = $gpa_data;
	        }else if($action == 'insert' || $action1 == 'insert'){
	        $this->db->insert('employee_policy_member',$gpa_data);
	        // echo $this->db->last_query();exit;
	        $gpa_data['policy_sub_type_id'] = 2;
	        $relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
	        $gpa_data['relationship'] = $relation['fr_name'];

	        $all_members[] = $gpa_data;
	        }

	        }else{
	            // echo "else";exit;
	            if($gpa && ($all_members_ghi[$i]['fr_id'] == 1 || $all_members_ghi[$i]['fr_id'] == 0)){
	                $this->db->where('family_relation_id', $all_members_ghi[$i]['family_relation_id']);
	                $this->db->where('policy_detail_id', $gpa_policy_detail_id['policy_detail_id']);
	                $this->db->update('employee_policy_member',$gpa_data);

	                $gpa_data['policy_sub_type_id'] = 2;
	                $relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
	                $gpa_data['relationship'] = $relation['fr_name'];
	                $all_members[] = $gpa_data;
	            }
	            else{
	                
	                $this->db->where(['family_relation_id' => $all_members_ghi[$i]['family_relation_id'],'policy_detail_id' => $gpa_policy_detail_id['policy_detail_id'] ])->delete("employee_policy_member");
	            }
	            

	        }

	        //update in logssssssssssssssssssssssss
	        $logs_array['data'] = ["type" => "update_gpa", "req" => json_encode($all_members) , "lead_id" => $all_members_ghi[$i]['family_relation_id'], "product_id" => $product_id];
	        $this->Logs_m->insertLogs($logs_array);

	    }

		// print_pre($new_preium);

		$new_preium=array_sum($new_preium);

		$all_members[0]['new_premium']=$new_preium;

	    // print_pre($all_members);exit;
	    return $all_members;

	}

    //added deductable parameter healthproxl
    /**
	* This function get all the details of Family Members.
	*
	* @author Ankita  <ankita.badak@fyntune.com>
	* @return Data in Array Format.
	*/
    function get_all_member_data_healthproxl($emp_id, $policy_detail_id,$deductable = 0)
    {
        $response = $this->db
        ->query('SELECT epd.policy_detail_id,epd.policy_sub_type_id,epm.family_relation_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
            epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type","R001" as relation_code,epm.policy_member_email_id,epm.policy_member_mob_no
            FROM 
            employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_details AS ed
            WHERE epd.policy_detail_id = epm.policy_detail_id
            AND epm.family_relation_id = fr.family_relation_id
            AND fr.family_id = 0
            AND fr.emp_id = ed.emp_id
            AND ed.emp_id = ' . $emp_id . '
            AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' UNION all SELECT epd.policy_detail_id,epd.policy_sub_type_id,epm.family_relation_id, epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
            epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type",mfr.relation_code,epm.policy_member_email_id,epm.policy_member_mob_no
            FROM 
            employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
            master_family_relation AS mfr
            WHERE epd.policy_detail_id = epm.policy_detail_id
            AND epm.family_relation_id = fr.family_relation_id
            AND fr.family_id = efd.family_id 
            AND efd.fr_id = mfr.fr_id
            AND fr.emp_id = ' . $emp_id . '
            AND `epd`.`policy_detail_id` = ' . $policy_detail_id)->result_array();
          // echo $this->db->last_query();
			//print_pre($response);exit;
			if($response[0]['suminsured_type'] == 'family_construct_age'){
				$change_premium = false;
                // print_pre($response);exit;
		    $age = [];
				foreach($response as $value){
                    if($value['age_type'] == 'days'){
                        $age[] = 0;
                    }else{
                        $age[] = $value['age'];
                    }
										
				}
                //print_pre($age);exit;
                if($policy_detail_id == TELE_HEALTHPROINFINITY_GHI_ST){
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
				
				 $max_age = max($age);
				foreach($check as $value){
					$min_max_age = explode("-",$value['age_group']);
					if($max_age >= $min_max_age[0] && $max_age <= $min_max_age[1]){
						$premium = $value['PremiumServiceTax'];
					}
				}
				
				foreach($response as $key => $value1){
                    //echo $premium.'<br>';
					
                    if($response[$key]['policy_sub_type_id'] == 1){
                        $response[$key]['policy_mem_sum_premium'] = trim($premium);
    					$this->db->where('policy_member_id', $response[$key]['policy_member_id']);
    					$this->db->update('employee_policy_member', ['policy_mem_sum_premium' => $premium]);
    					if($this->db->affected_rows()){
    						$change_premium = true;
    					}
                    }
				}
				if($change_premium){	
					$response[0]["message"] = "Premium has been changed as per your inputs to ".$premium;
					$response[0]["new_premium"] = $premium;
				}	
			}
        //print_pre($response);//exit;  
        return $response;
    }
    public function get_all_policy_data_new($parent_id) 
	{
	   	
        $data = $this->db
                ->select('pms.product_code,pms.master_policy_no,pms.product_name,pms.policy_parent_id,epd.policy_detail_id,pms.combo_flag,epd.suminsured_type')
                ->from('product_master_with_subtype as pms, employee_policy_detail as epd')
                ->where('pms.id = epd.product_name')
                ->where('pms.`is_telesales_product` ', 1)
                ->group_by('pms.product_name')
                ->get()
                ->result_array();
        if(!empty($data)){
			$planDrpdown = '<option value="">Select Product Name</option>';
			foreach($data as $val)
			{
				$cls = '';
				if($_SESSION['telesales_session']['product_code'] == $val['product_code']){
					$cls = 'selected';
				}
				$planDrpdown .= '<option value="'.$val['product_code'].'" '.$cls.'>'.$val['product_name'].'</option>';
					
			}
		}
        // echo $this->db->last_query();exit;
		return $planDrpdown;
     
    }
	
	public function get_all_policy_data($parent_id) 
	{
       
	   
        $data = $this->db
                ->select('pms.master_policy_no,pms.product_code,pms.product_name,pms.policy_parent_id,epd.policy_detail_id,pms.combo_flag,mfr.fr_id,mfr.fr_name,mbir.relationship_id,mbir.max_adult,mbir.max_child,epd.policy_sub_type_id,epd.suminsured_type,mpst.policy_sub_type_name,mfr.gender_option')
                ->from('product_master_with_subtype as pms, employee_policy_detail as epd,master_broker_ic_relationship as mbir, master_family_relation as mfr,master_policy_sub_type as mpst')
                ->where('pms.id = epd.product_name')
                ->where('epd.policy_detail_id = mbir.policy_id')
                ->where('pms.policy_subtype_id = mpst.policy_sub_type_id')
                ->where('find_in_set(mfr.fr_id, mbir.relationship_id)')
                ->where('pms.policy_parent_id', $parent_id)
                //->group_by('pms.id')
                ->get()
                ->result_array();

        $check = [];
		
        $new_array = [];
         if ($data[0]['combo_flag'] == "Y") 
		{
	
            for ($i = 0; $i < count($data); $i++) 
			{
                $data[$i]['policy_sub_type_id'] = 1;
		
				$get_sub_name = $this->get_sub_name($parent_id);
				$data[$i]['policy_sub_type_name'] = $get_sub_name;

                if (empty($check)) 
				{
						
                    array_push($check, $data[$i]['fr_id']);
                    array_push($new_array, $data[$i]);
                } 
				else 
				{
                    if (!(in_array($data[$i]['fr_id'], $check))) 
					{
                        array_push($check, $data[$i]['fr_id']);
                        array_push($new_array, $data[$i]);
                    }
                }
            }
		
            
            return $new_array;
        }
		
		return $data;

      
    }
	
	
	public function get_sub_name($parent_id)
	{
		
		$query = $this->db->query("select * from product_master_with_subtype as psw Join master_policy_sub_type as mpst ON psw.policy_subtype_id = mpst.policy_sub_type_id where psw.policy_parent_id = '".$parent_id."' AND psw.combo_flag ='".Y."'");
		$query->result_array();
		//echo $this->db->last_query();exit;
		$arr_new = [];
		if($query->num_rows()>0)
		{
			$result = $query->result_array();

			foreach($result as $arr)
			{
				
				$newname = $arr['policy_sub_type_name'];
				array_push($arr_new,$newname);
			}
			
		}
		$new_arrays = implode(" + ",$arr_new);

		return $new_arrays;
	}
	/*healthpro changes changes code of this fumction old function renamed to get_suminsured_data_bk()*/
	public function get_suminsured_data($parent_id,$product_id) 
	{
		if($product_id == 'T03'){
        	$data = $this->db
		    ->select('*')
		    ->from('product_master_with_subtype as pms, employee_policy_detail as epd')
		    ->where('pms.id = epd.product_name')
		    ->where('pms.product_name', TELE_HEALTHPROINFINITY_PRODUCT_NAME)
		    ->group_by('pms.policy_subtype_id')
		    ->get()
		    ->result_array();
	    }else{
			$data = $this->db
			->select('*')
			->from('product_master_with_subtype as pms, employee_policy_detail as epd')
			->where('pms.id = epd.product_name')
			->where('pms.product_code', $product_id)
			->get()
			->result_array();
		}
		for ($i = 0; $i < count($data); $i++) {
		if ($data[$i]['suminsured_type'] == "flate" && ($data[$i]['premium_type'] == "flate1" || $data[$i]['premium_type'] == "memberage1")) {
		$data1[$i]['flate'] = $this->db
		->select('*')
		->from('employee_policy_detail as epd')
		->join('policy_creation_age_bypremium as pcapremium', 'epd.policy_detail_id = pcapremium.policy_id', 'left')
		->join('policy_creation_designation_bypremium as pcdpremium', 'epd.policy_detail_id = pcdpremium.policy_id', 'left')
		->where('epd.policy_detail_id', $data[$i]['policy_detail_id'])
		->get()
		->result_array();
		}
		if ($data[$i]['suminsured_type'] == "family_construct") {
		if ($data[$i]['combo_flag'] == "Y") {
			
		$policy_ids[] = $data[$i]['policy_detail_id'];
		
		
		}
		//print_pre($policy_ids);exit;
		$data1[$i]['family_construct'] = $this->db
		->select('*')
		->from('employee_policy_detail as epd,family_construct_wise_si as pcapremium')
		->where('epd.policy_detail_id = pcapremium.policy_detail_id')
		->where('epd.policy_detail_id', $data[$i]['policy_detail_id'])
		->group_by("pcapremium.sum_insured")
		->get()
		->result_array();
		$variable = 'family_construct';
		}
		if ($data[$i]['suminsured_type'] == "memberAge") {
			if ($data[$i]['combo_flag'] == "Y") {
			$variable = 'memberAge';
		$policy_ids[] = $data[$i]['policy_detail_id'];
		
		
		}

		$data1[$i]['memberAge'] = $this->db
		->select('*')
		->from('employee_policy_detail as epd')
		->join('product_master_with_subtype as pms','pms.id = epd.product_name')
		->join('policy_creation_age as pcapremium', 'epd.policy_detail_id = pcapremium.policy_id', 'left')
		//->join('policy_creation_age_bypremium as pcapremium1', 'epd.policy_detail_id = pcapremium1.policy_id', 'left')
		->where('epd.policy_detail_id', $data[$i]['policy_detail_id'])
		->group_by("pcapremium.sum_insured")
		->get()
		->result_array();
		
		
			
		
		}
		if ($data[$i]['suminsured_type'] == "family_construct_age") {
		if ($data[$i]['combo_flag'] == "Y") {
		$policy_ids[] = $data[$i]['policy_detail_id'];

		}
		
		
		$data1[$i]['family_construct_age'] = $this->db
		->select('*')
		->from('employee_policy_detail as epd,family_construct_age_wise_si as pcapremium')
		->where('epd.policy_detail_id = pcapremium.policy_detail_id')
		->where('epd.policy_detail_id', $data[$i]['policy_detail_id'])
		->group_by("pcapremium.sum_insured")
		->get()
		->result_array();
		}
		}
		
			
		if ($data[0]['combo_flag'] == "Y") {
		$y1 = implode(",", $policy_ids);


		for ($i = 0; $i < count($data1); $i++) {



		for ($j = 0; $j < count($data1[$i][$variable]); $j++) {
		
			
					
		$data1[$i][$variable][$j]['policy_sub_type_id'] = 1;
		$data1[$i][$variable][$j]['policy_detail_id'] = $y1;
		$data1[$i][$variable][$j]['combo_flag'] = "Y";
		
		
		}
		}
		}

		$key_defined = '';
		$arr_keys = [];
		$new_array = [];
		if(!empty($data1)){
		foreach($data1 as $key => $value){
		foreach($value as $key1 => $value1){
		$key_defined = $key1;
		array_push($arr_keys,$key1);
		foreach($value1 as $key2 => $value2){
		$new_array[] = $value2;
		}
		}

		}
		}
		usort($new_array, function($a, $b) {
		return $a['sum_insured'] - $b['sum_insured'];
		});
		$count_arr_keys = count(array_unique($arr_keys));
		if($count_arr_keys > 1)
		{
		$key_defined = "combo_diff_construct";
		}
		$return_array[0][$key_defined] = $new_array;
		//print_pre($return_array);exit;
		return $return_array;

     
    }
	
	public function get_suminsured_data_bk($parent_id) 
	{
		
        $data = $this->db
                ->select('*')
                ->from('product_master_with_subtype as pms, employee_policy_detail as epd')
                ->where('pms.id = epd.product_name')
                ->where('pms.policy_parent_id', $parent_id)
                ->get()
                ->result_array();
	
        for ($i = 0; $i < count($data); $i++) 
		{
            if ($data[$i]['suminsured_type'] == "flate" && ($data[$i]['premium_type'] == "flate1" || $data[$i]['premium_type'] == "memberage1")) 
			{
                $data1[$i]['flate'] = $this->db
                        ->select('*')
                        ->from('employee_policy_detail as epd')
                        ->join('policy_creation_age_bypremium as pcapremium', 'epd.policy_detail_id = pcapremium.policy_id', 'left')
                        ->join('policy_creation_designation_bypremium as pcdpremium', 'epd.policy_detail_id = pcdpremium.policy_id', 'left')
                        ->where('epd.policy_detail_id', $data[$i]['policy_detail_id'])
                        ->get()
                        ->result_array();
            }

            if ($data[$i]['suminsured_type'] == "family_construct") 
			{
                if ($data[0]['combo_flag'] == "Y") 
				{
                    $policy_ids[] = $data[$i]['policy_detail_id'];
                }

                $data1[$i]['family_construct'] = $this->db
                        ->select('*')
                        ->from('employee_policy_detail as epd,family_construct_wise_si as pcapremium')
                        ->where('epd.policy_detail_id = pcapremium.policy_detail_id')
                        ->where('epd.policy_detail_id', $data[$i]['policy_detail_id'])
                        ->group_by("pcapremium.sum_insured")
                        ->get()
                        ->result_array();
              
            }

            if ($data[$i]['suminsured_type'] == "memberAge") 
			{

				$data1[$i]['memberAge'] = $this->db
                        ->select('*,pcapremium.premium AS "premium"')
                        ->from('employee_policy_detail as epd')
                        ->join('policy_creation_age as pcapremium', 'epd.policy_detail_id = pcapremium.policy_id', 'left')
                        ->join('policy_creation_age_bypremium as pcapremium1', 'epd.policy_detail_id = pcapremium1.policy_id', 'left')
                        ->where('epd.policy_detail_id', $data[$i]['policy_detail_id'])
                        ->get()
                        ->result_array();
					
						
            }
			if ($data[$i]['suminsured_type'] == "family_construct_age") 
			{
					 if ($data[0]['combo_flag'] == "Y") 
					 {
						 $policy_ids[] = $data[$i]['policy_detail_id'];
					 }
						$data1[$i]['family_construct_age'] = $this->db
                        ->select('*')
                        ->from('employee_policy_detail as epd,family_construct_age_wise_si as pcapremium')
                        ->where('epd.policy_detail_id = pcapremium.policy_detail_id')
                        ->where('epd.policy_detail_id', $data[$i]['policy_detail_id'])
                        ->group_by("pcapremium.sum_insured")
                        ->get()
                        ->result_array();
					
						
            }
			
			
        }

        if ($data[0]['combo_flag'] == "Y") 
		{
			
			//print_Pre()

            $y1 = implode(",", $policy_ids);
            for ($i = 0; $i < count($data1); $i++) 
			{
         
                for ($j = 0; $j < count($data1[$i]['family_construct']); $j++)
				{
                    $data1[$i]['family_construct'][$j]['policy_sub_type_id'] = 1;
                    $data1[$i]['family_construct'][$j]['policy_detail_id'] = $y1;
					 $data1[$i]['family_construct'][$j]['combo_flag'] = "Y";
                }
            }
        }
		$key_defined = '';
		$new_array = [];
	
		if(!empty($data1))
		{
			foreach($data1 as $key => $value)
			{
			
				foreach($value as $key1 => $value1)
				{
					$key_defined = $key1;
					foreach($value1 as $key2 => $value2)
					{
				
						$new_array[] = $value2;
					}
				}
			
			}
		}
		usort($new_array, function($a, $b)
		{
			return $a['sum_insured'] - $b['sum_insured'];
		});
		
		$return_array[0][$key_defined] = $new_array;

        return $return_array;


     
    }
	public function add_agent_emp_details($emp_id,$emp_agent_data)
	{
		//print_pre($emp_agent_data);exit;

		// print_pre($this->input->post('email'));exit;
		$employee_policy_member_email_update=$this->input->post('email');

		$this->db->where('emp_id',$emp_id);
		$this->db->update('employee_details',$emp_agent_data);




		$data = $this->db
                ->select('*')
                ->from('employee_policy_member epm,family_relation fr,employee_family_details efd')
                ->where('efd.family_id = fr.family_id')
                ->where('fr.family_relation_id = epm.family_relation_id')
                ->where('fr.emp_id',$emp_id)
                ->where('efd.fr_id',1)
                ->get()
                ->result_array();
				
				if(!empty($data)){
					if($emp_agent_data['gender'] == 'Male'){
						$update_gender = 'Female';
					}else{
						$update_gender = 'Male';
					}
					foreach($data as $value){
						$this->db->where('policy_member_id', $value['policy_member_id']);
						$this->db->update('employee_policy_member',['policy_mem_gender' => $update_gender]);
						
						$proposal_data = $this->db->select('*')->from('proposal_member')->where('policy_member_id',$value['policy_member_id'])->get()->row_array();
						if(!empty($proposal_data)){
							$this->db->where('policy_member_id', $value['policy_member_id']);
							$this->db->update('proposal_member',['policy_mem_gender' => $update_gender]);
							
						}
					}
				}

				$get_family_relation_id=$this->db->select('family_relation_id,emp_id')->from('family_relation')->where('emp_id',$emp_id)->where('family_id',0)->get()->row_array();

				if($get_family_relation_id){
					$this->db->where('family_relation_id',$get_family_relation_id['family_relation_id'])->update('employee_policy_member',['policy_member_email_id' => $employee_policy_member_email_update]);
				}

				// print_pre($get_family_relation_id['family_relation_id']);exit;

				
	}
	public function add_nominee($nominee_data)
	{
		$this->db->insert('member_policy_nominee',$nominee_data);
		// echo $this->db->last_query();exit;
		return true;
	}
	public function ghd_declined_insert($myGHD,$emp_id,$log_insert,$lead_id,$product_id)
	{
		
				if(@$myGHD)
				    $query_ghd = $this->db->query("Delete  from  tls_ghd_employee_declare where emp_id ='".$emp_id."'");
					foreach ($myGHD as $key => $row) 
					{
						
					
			 
						$split_format = explode('_',$row['format']);
						if(!empty($row['format']))
						{
							$GHD_data = [
								   
									"format" => $split_format[1],
									"remark" => $row['remark'],
									"type" => $key,
									"emp_id" => $emp_id
									];
							$this->db->insert("tls_ghd_employee_declare", $GHD_data);
							
							
				//logs							
							$logs_array = ["type" => "insert_member_ghd_declare","req" => json_encode($GHD_data), "lead_id" => $lead_id,"product_id" => $product_id];
							$this->db->insert("logs_post_data",$logs_array);
							
						if($row['format']== 'B_Yes')
						{
							
							$msg = "Enrollment would be declined, Pls check the Product guidelines";
							
							return ["status" => "false", "message" => $msg ];
						}
						elseif($row['format']== 'C1_Yes' || $row['format']== 'C2_Yes')
						{
							$msg = "Enrollment would be declined, Pls check the Product guidelines";
							return ["status" => "false", "message" => $msg ];
						}
						}
						
						
						
					}								
	}
	public function insert_policy_member($member_arrays)
	{
		$this->db->insert("employee_policy_member", $member_arrays);
		return true;
		
	}
	
	public function get_family_construct()
	{
		
		
		$family_construct_post = $this->input->post(null,true);
		if($family_construct_post != null || !empty($family_construct_post))
		  
	    $sumInsured = $family_construct_post['sumInsured'];
		$table = $family_construct_post['table'];
		$policyNo = $family_construct_post['policyNo'];	
		
			if($table == 'family_construct')
			{
				$table = 'family_construct_wise_si';
			}
			else
			{
				$table = 'family_construct_age_wise_si';
			}
			$data = $this->db->select("*")
					->from($table)
					->where("policy_detail_id", $policyNo)
					->where("sum_insured", $sumInsured)
					->group_by("family_type")
					->get()
					->result_array();
		
		return $data;
	}

	/*healthpro changes created new function*/
	public function get_premium_from_policy($policy_detail_id,$sum_insure,$family_construct,$age)
{
	
	
	$ew_status  = 0;//$this->EW_status($emp_id);
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
	// print_pre($check_gmc);exit;
	if($check_gmc['suminsured_type'] == 'family_construct')	
	{
		
			$checks = $this->db->select("PremiumServiceTax,sum_insured,EW_PremiumServiceTax")
			->from("family_construct_wise_si")
			->where("sum_insured", $sum_insure)
			->where("family_type", $member_id)
			->where("policy_detail_id", $policy_detail_id)
			->get()
			->row_array();
			//echo $this->db->last_query().'===='.$ew_status.'===';
			
			if($ew_status == 1)
			{
				$premium_value  = $checks['EW_PremiumServiceTax'];
			}
			else
			{
				$premium_value  = $checks['PremiumServiceTax'];
			}
			//echo $premium_value;exit;
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
		//echo $premium_value;exit;
			return $premium_value;
			
	}
	}
	}

}
/*healthpro changes created new function*/
public function get_premium_from_policy_memberage($policy_detail_ids,$sum_insure,$family_construct,$age)
	{
		
		
		$ew_status  = 0;//$this->EW_status($emp_id);
		$premium_value = '';
		foreach ($policy_detail_ids as $key => $policy_detail_id) {
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
					//echo $age .">=". $min_max_age[0] ."&&". $age ."<=". $min_max_age[1];exit;
					if((int)$age >= (int)$min_max_age[0] && (int)$age <= (int)$min_max_age[1]){
						
						if($EW_status == 1)
						{
							$premium_value  = $values_age['EW_premium_with_tax'];
						}
						else
						{
							$premium_value  = $values_age['premium_with_tax'];
						}

							
					}
				}
				if($family_construct == '2A'){
					$premium_value = $premium_value * 2;
				}
				$premium = $premium + $premium_value;
			}
		
		}
		if($premium == 0){
			$data = [
						'status'=>'error',
						'message'=>'Age is not as per policy',
						'premium' => 0
					];			
		}else{
			$data = [
					'status'=>'success',
					'message'=>'',
					'premium' => $premium
				];
		}
		
		return $data;
	}
/*healthpro changes created new function*/
	public function get_premium_new() 
	{
      // echo 123;die;
	extract($this->input->post(null, true));
	$ew_status = 0;//$this->EW_status($emp_id);
	//sum insured
	if($product_id == 'T01'){
		if($gci_optional == 'Yes'){
			$q = "SELECT epd.policy_detail_id as policy_detail_id FROM product_master_with_subtype as pms,employee_policy_detail as epd WHERE pms.id = epd.product_name AND pms.product_code = '".$product_id."' AND pms.policy_subtype_id = 3";
			$gci_policy_detail_id = $this->db->query($q)->row_array()['policy_detail_id'];
			$policy_detail_id = $policy_detail_id.",".$gci_policy_detail_id;
		}
		
	}
	//family construct
	$policy_detail_ids = (explode(",", $policy_detail_id));
	//print_pre($policy_detail_ids);exit;
	for ($i = 0; $i < count($policy_detail_ids); $i++) {
	$check_gmc = $this->db
	->select('*')
	->from('employee_policy_detail as epd')
	->join('master_policy_sub_type as mpst', "epd.policy_sub_type_id = mpst.policy_sub_type_id")
	->where('epd.policy_detail_id', $policy_detail_ids[$i])
	->get()
	->row_array();

	//upendra - 29-06-2021
	// if($check_gmc['policy_sub_type_id'] == '1' && $product_id == 'T01')
		
	    //health pro infinity - premium breakup changes - akash/upendra
		if(($check_gmc['policy_sub_type_id'] == '1' && $product_id == 'T01') || (($check_gmc['policy_sub_type_id'] == '1') && $product_id == 'T03')){
		

		$age_array = array();

		$family_construct1 = explode("+", $family_construct);


		if($family_construct1[0] == '2A'){
			$q1 = "SELECT bdate FROM employee_details WHERE emp_id = '".$this->emp_id."'";
			$bdate = $this->db->query($q1)->row_array()['bdate'];
			$birthdate = new DateTime($bdate);
			$today   = new DateTime('today');
			$age_purchaser = $birthdate->diff($today)->y;
			array_push($age_array,$age_purchaser);

			$q1 = "SELECT spouse_dob,deductable FROM employee_details WHERE emp_id = '".$this->emp_id."'";
			$bdate = $this->db->query($q1)->row_array()['spouse_dob'];
			$birthdate = new DateTime($bdate);
			$today   = new DateTime('today');
			$age_spouse = $birthdate->diff($today)->y;
			array_push($age_array,$age_spouse);

		}else if($family_construct1[0] == '1A'){

			$q1 = "SELECT policy_for,deductable FROM employee_details WHERE emp_id = '".$this->emp_id."'";
			$policy_for = $this->db->query($q1)->row_array()['policy_for'];

			if($policy_for == "0"){

				$q1 = "SELECT bdate,deductable FROM employee_details WHERE emp_id = '".$this->emp_id."'";
				$bdate = $this->db->query($q1)->row_array()['bdate'];
				$birthdate = new DateTime($bdate);
				$today   = new DateTime('today');
				$age_purchaser = $birthdate->diff($today)->y;
				array_push($age_array,$age_purchaser);

			}else if($policy_for == "1"){

				$q1 = "SELECT spouse_dob,deductable FROM employee_details WHERE emp_id = '".$this->emp_id."'";
				$bdate = $this->db->query($q1)->row_array()['spouse_dob'];
				$birthdate = new DateTime($bdate);
				$today   = new DateTime('today');
				$age_spouse = $birthdate->diff($today)->y;
				array_push($age_array,$age_spouse);

			}

		}

if($deductable!='')
		{
			$ded = "fc.deductable = '$deductable'";
		}else{
			$ded = "fc.deductable = '0' OR fc.deductable = ''";
		}
		$max_age = max($age_array);

		if($max_age > 45){
			$age_group = "46-55";
		}else{
			$age_group = "18-45";
		}

		$premium[$i] = $this->db
		->select('*')
		->from('family_construct_age_wise_si as fc')
		->where('fc.policy_detail_id', $policy_detail_ids[$i])
		->where('fc.family_type', $family_construct)
		->where('fc.sum_insured', $sum_insured)
		->where('fc.age_group', $age_group)
		->where($ded,Null,false)
		
		->get()
		->row_array();

		
	// print_r($premium);exit;
	// $premium[$i]['policy_sub_type_id'] = 'Group Mediclaim';
	$premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
	// print_r($premium);exit;
	}
		
	else if ($check_gmc['policy_sub_type_id'] == '1') {
	//get gmc premium
	$premium[$i] = $this->db
	->select('*')
	->from('family_construct_wise_si as fc')
	->where('fc.policy_detail_id', $policy_detail_ids[$i])
	->where('fc.family_type', $family_construct)
	->where('fc.sum_insured', $sum_insured)
	->get()
	->row_array();
	// $premium[$i]['policy_sub_type_id'] = 'Group Mediclaim';
	$premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
	
	}else if($check_gmc['policy_sub_type_id'] == '3'){
		// echo $check_gmc['suminsured_type'];exit;
		if($check_gmc['suminsured_type'] == 'memberAge'){

			    $q1 = "SELECT bdate FROM employee_details WHERE emp_id = '".$this->emp_id."'";
			    $bdate = $this->db->query($q1)->row_array()['bdate'];
			    $birthdate = new DateTime($bdate);
		        $today   = new DateTime('today');
		        $age = $birthdate->diff($today)->y;

				//upendra - 29-06-2021
				if($product_id == "T01"){
					$age_array = array();

					$family_construct1 = explode("+", $family_construct);

					if($family_construct1[0] == '2A'){
						$q1 = "SELECT bdate FROM employee_details WHERE emp_id = '".$this->emp_id."'";
						$bdate = $this->db->query($q1)->row_array()['bdate'];
						$birthdate = new DateTime($bdate);
						$today   = new DateTime('today');
						$age_purchaser = $birthdate->diff($today)->y;
						array_push($age_array,$age_purchaser);

						$q1 = "SELECT spouse_dob FROM employee_details WHERE emp_id = '".$this->emp_id."'";
						$bdate = $this->db->query($q1)->row_array()['spouse_dob'];
						$birthdate = new DateTime($bdate);
						$today   = new DateTime('today');
						$age_spouse = $birthdate->diff($today)->y;
						array_push($age_array,$age_spouse);

					}else if($family_construct1[0] == '1A'){

						$q1 = "SELECT policy_for FROM employee_details WHERE emp_id = '".$this->emp_id."'";
						$policy_for = $this->db->query($q1)->row_array()['policy_for'];

						if($policy_for == "0"){

							$q1 = "SELECT bdate FROM employee_details WHERE emp_id = '".$this->emp_id."'";
							$bdate = $this->db->query($q1)->row_array()['bdate'];
							$birthdate = new DateTime($bdate);
							$today   = new DateTime('today');
							$age_purchaser = $birthdate->diff($today)->y;
							array_push($age_array,$age_purchaser);

						}else if($policy_for == "1"){

							$q1 = "SELECT spouse_dob FROM employee_details WHERE emp_id = '".$this->emp_id."'";
							$bdate = $this->db->query($q1)->row_array()['spouse_dob'];
							$birthdate = new DateTime($bdate);
							$today   = new DateTime('today');
							$age_spouse = $birthdate->diff($today)->y;
							array_push($age_array,$age_spouse);
							
						}

					}


					$age = max($age_array);
				}
			    
				$check_age = $this->db->select("policy_age,premium_with_tax,sum_insured,EW_premium_with_tax")
					->from("policy_creation_age")
					->where("sum_insured",$sum_insured)
					->where("policy_id", $policy_detail_ids[$i])
					->get()
					->result_array();	
				foreach($check_age as $values_age){
					$min_max_age = explode("-",$values_age['policy_age']);
					//echo $age .">=". $min_max_age[0] ."&&". $age ."<=". $min_max_age[1];exit;
					if((int)$age >= (int)$min_max_age[0] && (int)$age <= (int)$min_max_age[1]){
						
						$premium_value  = $values_age['premium_with_tax'];

						$premium[$i] = $values_age;

							
					}
				}

				$family_construct1 = explode("+", $family_construct);

				if($family_construct1[0] == '2A'){
					$premium_value = $premium_value * 2;
				}
				//check data already added or not
				$fr_q = "SELECT family_relation_id FROM family_relation WHERE emp_id = '".$this->emp_id."'";
				$frRes = $this->db->query($fr_q)->result_array();
				if(!empty($frRes)){
					$frRes = array_column($frRes, 'family_relation_id');
					//print_pre($frRes);
					$fr_ids = implode(",", $frRes);
					$em_q = "SELECT policy_mem_sum_premium FROM employee_policy_member WHERE family_relation_id IN (".$fr_ids.") AND policy_detail_id = '".$check_gmc['policy_detail_id']."'";
					$emRes = $this->db->query($em_q)->result_array();
					//echo $em_q;
					//print_pre($emRes);
					if(!empty($emRes)){
						$p = 0;
						foreach ($emRes as $keyp => $valp) {
							//echo $valp['policy_mem_sum_premium'];
							$p += $valp['policy_mem_sum_premium'];
						}
						$premium_value = $p;
					}
					
				}
				//echo $premium_value;exit;
				$premium[$i]['policy_detail_id'] = $policy_detail_ids[$i];
				$premium[$i]['PremiumServiceTax'] = $premium_value;
				$premium[$i]['family_type'] = $family_construct1[0];
				$premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
				// print_pre($premium);exit;
				$ew_status = "0";
				
			}

	} else {
		$family_construct1 = explode("+", $family_construct);

		//get max adult count
		$premium1 = $this->db
		->select('*')
		->from('master_broker_ic_relationship as fc')
		->where('fc.policy_id', $policy_detail_ids[$i])
		->get()
		->row_array();

		if ($premium1['max_adult'] . "A" == $family_construct1[0] || $premium1['max_adult'] . "A" > $family_construct1[0]) {
		$premium[$i] = $this->db
		->select('*')
		->from('family_construct_wise_si as fc')
		->where('fc.policy_detail_id', $policy_detail_ids[$i])
		->where('fc.family_type', $family_construct1[0])
		->where('fc.sum_insured', $sum_insured)
		->get()
		->row_array();
		//echo $this->db->last_query();exit;
		if ($check_gmc['policy_sub_type_id'] == 2) {
		$premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
		//echo "here123";exit;

			//health pro infinity - premium breakup changes - akash/upendra
			if($product_id == "T03"){
				$family_construct1 = explode("+", $family_construct);


				$premium[$i] = $this->db
				->select('*')
				->from('family_construct_age_wise_si as fc')
				->where('fc.policy_detail_id', $policy_detail_ids[$i])
				->where('fc.family_type', $family_construct1[0])
				->where('fc.sum_insured', $sum_insured)
				->where('fc.age_group', "18-55")
				->get()
				->row_array();


			// print_r($premium);exit;
			// $premium[$i]['policy_sub_type_id'] = 'Group Mediclaim';
			$premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];

			}

		} else {
		$premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
		}
		} else {
		if ($check_gmc['policy_sub_type_id'] == 2) {
		$premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
		} else {
		$premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
		}
		$premium[$i] = $this->db
		->select('*')
		->from('family_construct_wise_si as fc')
		->where('fc.policy_detail_id', $policy_detail_ids[$i])
		->where('fc.family_type', $premium1['max_adult'] . "A")
		->get()
		->row_array();
		}
	}

	$premium[$i]['ew_status'] = $ew_status;
	}
	return ($premium);
       
    }
	
	public function get_premium() 
	{
       
		$premium_post = $this->input->post(null,true);
		if($premium_post != null || !empty($premium_post))
		$policy_detail_id = $premium_post['policy_detail_id'];
		$family_construct = $premium_post['family_construct'];
		$sum_insured = $premium_post['sum_insured'];
        
        $policy_detail_ids = (explode(",", $policy_detail_id));

        for ($i = 0; $i < count($policy_detail_ids); $i++)
		{
	
            $check_gmc = $this->db
                    ->select('*')
                    ->from('employee_policy_detail as epd')
                    ->join('master_policy_sub_type as mpst', "epd.policy_sub_type_id = mpst.policy_sub_type_id")
                    ->where('epd.policy_detail_id', $policy_detail_ids[$i])
                    ->get()
                    ->row_array();

            if ($check_gmc['policy_sub_type_id'] == '1') 
			{

               
                $premium[$i] = $this->db
                        ->select('*')
                        ->from('family_construct_wise_si as fc')
                        ->where('fc.policy_detail_id', $policy_detail_ids[$i])
                        ->where('fc.family_type', $family_construct)
                        ->where('fc.sum_insured', $sum_insured)
                        ->get()
                        ->row_array();
               
                $premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
				
			
            } 
			else 
			{
                $family_construct1 = explode("+", $family_construct);
				
                //get max adult count
                $premium1 = $this->db
                        ->select('*')
                        ->from('master_broker_ic_relationship as fc')
                        ->where('fc.policy_id', $policy_detail_ids[$i])
                        ->get()
                        ->row_array();
				
                if ($premium1['max_adult'] . "A" == $family_construct1[0] || $premium1['max_adult'] . "A" > $family_construct1[0]) 
				{
					
                    $premium[$i] = $this->db
                            ->select('*')
                            ->from('family_construct_wise_si as fc')
                            ->where('fc.policy_detail_id', $policy_detail_ids[$i])
                            ->where('fc.family_type', $family_construct1[0])
                            ->where('fc.sum_insured', $sum_insured)
                            ->get()
                            ->row_array();
							//echo $this->db->last_query();exit;
                    if ($check_gmc['policy_sub_type_id'] == 2)
					{
                        $premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
                    } 
					else 
					{
                        $premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
                    }
                } 
				else 
				{
                    if ($check_gmc['policy_sub_type_id'] == 2) 
					{
                        $premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
                    } 
					else 
					{
                        $premium[$i]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
                    }
                    $premium[$i] = $this->db
                            ->select('*')
                            ->from('family_construct_wise_si as fc')
                            ->where('fc.policy_detail_id', $policy_detail_ids[$i])
                            ->where('fc.family_type', $premium1['max_adult'] . "A")
                            ->get()
                            ->row_array();
                }
				
					
            }
		
        }
		return ($premium);
       
    }
	public function family_details_relation()
	{
		
		$family_details_post = $this->input->post(null,true);
		
		if($family_details_post != null || !empty($family_details_post))
		$relation_id = $family_details_post['relation_id'];
	    $emp_id = $this->emp_id;
		if ($relation_id == 0) 
		{
			$response = $this->db->select('ed.product_id,ed.GCI_optional,ed.occupation,ed.annual_income,ed.emp_id,ed.emp_code,ed.salutation,ed.emp_firstname,ed.emp_middlename,ed.emp_lastname,ed.fr_id,ed.company_id,ed.gender,ed.bdate,ed.mob_no,ed.email,ed.emp_grade,ed.emp_designation,ed.emp_address,ed.emp_city,ed.emp_state,ed.emp_pincode,ed.street,ed.location,ed.flex_amount,ed.total_salary,ed.gmc_grade_id,ed.emp_pay,ed.doj,fr.family_relation_id,fr.emp_id,fr.family_id')
						->from('employee_details as ed,family_relation as fr')
						->where('ed.emp_id = fr.emp_id')
						->where('fr.family_id', 0)
						->where('fr.emp_id', $emp_id)
						->get()->result_array();
				
			return $response;
		} 
		else
		{
			$response = $this->db->select('efd.*')
						->from('employee_policy_member as efd,family_relation as fr')
						->where('efd.family_relation_id = fr.family_relation_id')
						->where('fr_id', $relation_id)
						->where('fr.emp_id', $emp_id)
						->get()->result_array();

			return $response;
		}
	}
	//healthproxl changes
	function get_family_details_from_relationship_healthpro_xl()
	{

	    $family_details_post = $this->input->post(null,true);
	    //print_pre($family_details_post);exit;
		if(isset($family_details_post['selectedRelation'])){
			$selectedRelation = $family_details_post['selectedRelation'];
		}else{
			$selectedRelation = [];
		}
		$mem_occurances = array_count_values($selectedRelation);
		if($family_details_post != null || !empty($family_details_post))
		$relation_id = $family_details_post['relation_id'];
	    //encrypt change    
	  	$emp_id = $this->emp_id;
	        
	    if ($relation_id == 0) {
	        $response = $this->db->select('ed.emp_id,ed.emp_code,ed.emp_firstname,ed.salutation,ed.emp_middlename,ed.emp_lastname,ed.fr_id,ed.company_id,ed.gender,ed.bdate,ed.mob_no,ed.email,ed.emp_grade,ed.emp_designation,ed.emp_address,ed.emp_city,ed.emp_state,ed.emp_pincode,ed.street,ed.location,ed.flex_amount,ed.total_salary,ed.gmc_grade_id,ed.emp_pay,ed.doj,fr.family_relation_id,fr.emp_id,fr.family_id')
	        ->from('employee_details as ed,family_relation as fr')
	        ->where('ed.emp_id = fr.emp_id')
	        ->where('fr.family_id', 0)
	        ->where('fr.emp_id', $emp_id)
	        ->get()->result_array();
	        //echo $this->db->last_query();exit;
	        return $response;
	    } else {
	        $response = $this->db->select('efd.*')
	        //->from('employee_family_details as efd,family_relation as fr')
	        ->from('employee_policy_member as efd,family_relation as fr')
	        ->where('efd.family_relation_id = fr.family_relation_id')
	        ->where('fr_id', $relation_id)
	        ->where('fr.emp_id', $emp_id)
	        ->get()->result_array();

	        if(empty($response)){
	            if($relation_id == 1){
	                $q = "SELECT spouse_dob as policy_mem_dob
	                    FROM employee_details
	                    WHERE  emp_id = ".$emp_id;
	                $response = $this->db->query($q)->result_array();
	            }else{
	                $q = "SELECT kid1_dob , kid1_rel , kid2_dob ,kid2_rel
	                    FROM employee_details
	                    WHERE  emp_id = '".$emp_id."' AND (kid1_rel = '".$relation_id."' OR kid2_rel = '".$relation_id."') ";
	                $res = $this->db->query($q)->result_array();
	                foreach ($res as $key => $value) {
	                    if($value['kid1_rel'] == $relation_id){
	                    	//print_r($mem_occurances);
	                    	//echo $mem_occurances[$relation_id];exit;
	                    	if($mem_occurances[$relation_id] == 1){
	                    		$response[0]['policy_mem_dob'] = $value['kid1_dob'];
	                        	$response[0]['fr_id'] = $value['kid1_rel'];
	                    	}
	                        
	                    }
	                    //echo $response[0]['policy_mem_dob'];exit;
	                    if($response[0]['policy_mem_dob'] == ''){
	                        if($value['kid2_rel'] == $relation_id){
	                        	if($mem_occurances[$relation_id] == 2){
		                            $response[0]['policy_mem_dob'] = $value['kid2_dob'];
		                            $response[0]['fr_id'] = $value['kid2_rel'];
		                        }else{
		                        	$response[0]['policy_mem_dob'] = $value['kid2_dob'];
		                            $response[0]['fr_id'] = $value['kid2_rel'];
		                        }
	                        }
	                    }
	                    
	                }
	            }
	            
	            
	        }else{
	            // echo "in";exit;
	            $q = "SELECT kid1_dob , kid1_rel , kid2_dob ,kid2_rel
	                FROM employee_details
	                WHERE  emp_id = '".$emp_id."' AND (kid1_rel = '".$relation_id."' OR kid2_rel = '".$relation_id."') ";
	            $res = $this->db->query($q)->result_array();
	            //print_pre($res);exit;
	            foreach ($res as $key => $value) {
	                if($value['kid1_rel'] == $relation_id){
	                    //echo $value['kid1_dob'] ."!= ".$response[0]['policy_mem_dob'];exit;
	                    if($value['kid1_dob'] != $response[0]['policy_mem_dob']){
	                         //echo "1";exit; 
	                        $response[0]['policy_mem_dob'] = $value['kid1_dob'];
	                        $response[0]['fr_id'] = $value['kid1_rel']; 
	                    }
	                    
	                } 
	                if($value['kid2_rel'] == $relation_id){
	                    //echo $value['kid2_dob'] ."!= ".$response[0]['policy_mem_dob'];exit;
	                    if($value['kid2_dob'] != $response[0]['policy_mem_dob']){
	                       // echo "2";exit;
	                        $response[0]['policy_mem_dob'] = $value['kid2_dob'];
	                        $response[0]['fr_id'] = $value['kid2_rel'];
	                    }
	                    
	                }
	            }
	        }
	        //echo $this->db->last_query();
	        return $response;
	    }
	}
	public function member_declare_data($parent_id)
    {
    	$parent_id = 'IQDp5ebcd2b721105';
		$datas=[];
		$data=array();
		$data1 = $this->db
				->select('*')
				->from('policy_declaration_member')
				->where('parent_policy_id', $parent_id)
				->where('declare_subtype_id is not null',NULL,false)
				->get()
				->result_array();
		//echo $this->db->last_query();exit;	
		$arr_merge = array_merge($data, $data1);
        array_push($datas, $arr_merge);
		if(!empty($arr_merge))
		{
				return $datas;
		}
		else
		{
			return $datas=[];
		}
		
        return $datas;
    }
			
	public function member_declare_answer($parent_id, $subtype)
    {

        $datas = [];

        $data = $this->db
            ->select('*')
            ->from('policy_declaration_member')
            ->where('parent_policy_id', $parent_id)
            ->where('declare_subtype_id', $subtype)
            ->get()
            ->result_array();

			return $data;
    }
	
	public function sub_type_name($sub_type_id)
	{
				
		$data = $this->db
				->select('*')
				->from('policy_declaration_subtype')
				->where('declare_subtype_id', $sub_type_id)
				->get()
				->row_array();

				return $data;
	}
	public function health_declaration($parent_id)
	{
		if($parent_id == 'test123' || $parent_id == 'NvpnoiwGGDQPVA23w'){
			$data = $this->db
				->select('policy_declaration.label,policy_declaration.policy_detail_id,policy_declaration.proposal_continue,policy_declaration.p_declare_id,policy_declaration.content,policy_declaration.is_remark,policy_declaration.is_answer,policy_label_declarartion.label,policy_label_declarartion.p_label_id')
				->from('policy_declaration')
				->join('policy_label_declarartion ', 'policy_label_declarartion.p_declare_id = policy_declaration.p_declare_id', 'left')
				->where('policy_declaration.parent_policy_id', $parent_id)
				->where('policy_declaration.label', 'epd')
				->get()
				->result_array();
		}else{
			$data = $this->db
				->select('policy_declaration.label,policy_declaration.policy_detail_id,policy_declaration.proposal_continue,policy_declaration.p_declare_id,policy_declaration.content,policy_declaration.is_remark,policy_declaration.is_answer,policy_label_declarartion.label,policy_label_declarartion.p_label_id')
				->from('policy_declaration')
				->join('policy_label_declarartion ', 'policy_label_declarartion.p_declare_id = policy_declaration.p_declare_id', 'left')
				->where('policy_declaration.parent_policy_id', $parent_id)
				->get()
				->result_array();
		}
		
				// echo $this->db->last_query();exit;
		return $data;
	}

	//healthpro added new function
	public function EW_status($emp_id)
	{
		$get_emp_details = $this->db
							->select('ed.json_qote,ed.product_id')
							->from('employee_details as ed')
							->where('ed.emp_id', $emp_id)
							->get()
							->row_array();
		$json_branch_id = json_decode($get_emp_details['json_qote']);
		$product_code = $get_emp_details['product_id'];
		$branch_id = $json_branch_id->BRANCH_SOL_ID;
							
		$get_ew_status = $this->db
							->select('mi.EW_status')
							->from('master_imd  as mi')
							->where('mi.product_code', $product_code)
							->where('mi.BranchCode', $branch_id)
							->get()
							->row_array();					
		 $ew_status = $get_ew_status['EW_status'];
		 return $ew_status;
	}
	//healthpro added new function
	function get_all_member_data_new_gpa($product_id,$action,$ghi_policy_detail_id,$sum_insured,$family_construct,$update_ghi = false,$gci =false,$fr_id,$emp_id,$only_gci_insert = false){



$parent_id  = $this->db->select('parent_policy_id')->from('employee_policy_detail')->where('policy_detail_id',$ghi_policy_detail_id)->get()->row_array();
// echo $this->db->last_query();
$gpa_policy_detail_id = $this->db->select('policy_detail_id,suminsured_type')->from('employee_policy_detail')
->where('parent_policy_id',$parent_id['parent_policy_id'])
->where('policy_sub_type_id',2)
->get()
->row_array();
// print_pre($gpa_policy_detail_id);exit;
$gci_policy_detail_id = $this->db->select('policy_detail_id,suminsured_type')->from('employee_policy_detail')
->where('parent_policy_id',$parent_id['parent_policy_id'])
->where('policy_sub_type_id',3)
->get()
->row_array();
$all_members_ghi  = $this->get_all_member_data_new($emp_id, $ghi_policy_detail_id,true);

$all_members = [];

for($i =0; $i < count($all_members_ghi); $i++){


$data = [];

$data['policy_mem_sum_insured'] = $sum_insured;
$data['policy_mem_gender'] = $all_members_ghi[$i]['gender'];
$data['policy_mem_dob'] = $all_members_ghi[$i]['dob'];
$data['age'] = $all_members_ghi[$i]['age'];
$data['age_type'] = $all_members_ghi[$i]['age_type'];
$data['fr_id'] = $all_members_ghi[$i]['fr_id'];
$data['policy_member_first_name'] = $all_members_ghi[$i]['firstname'];
$data['policy_member_last_name'] = $all_members_ghi[$i]['lastname'];
$data['policy_detail_id'] = $ghi_policy_detail_id;
$data['family_relation_id'] = $all_members_ghi[$i]['family_relation_id'];
$data['familyConstruct'] = $family_construct;
$data['policy_member_id'] = $all_members_ghi[$i]['policy_member_id'];
$data['policy_mem_sum_premium'] = $this->get_premium_from_policy($ghi_policy_detail_id,$sum_insured,$family_construct,$all_members_ghi[$i]['age']);
$data['policy_member_email_id'] = $all_members_ghi[$i]['policy_member_email_id'];
$data['policy_member_mob_no'] = $all_members_ghi[$i]['policy_member_mob_no'];
if($update_ghi){
	$this->db->where('policy_member_id', $all_members_ghi[$i]['policy_member_id']);
	$this->db->update('employee_policy_member',$data);
	$data['policy_sub_type_id'] = 1;
	$relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
	$data['relationship'] = $relation['fr_name'];

	$all_members[] = $data;
}else{
	$data['policy_sub_type_id'] = 1;
	
	$relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
	$data['relationship'] = $relation['fr_name'];
	$all_members[] = $data;
}

//update in logssssssssssssssssss
$gpa_go_ahead = ($fr_id !== '') ? (($fr_id == $all_members_ghi[$i]['fr_id']) ? true : false)   : true;

$premium_gpa = $this->get_premium_from_policy($gpa_policy_detail_id['policy_detail_id'],$sum_insured,$family_construct,$all_members_ghi[$i]['age']);
$familyConstructs = explode('+',$family_construct)[0];
if($familyConstructs == '2A')
{
	$premium_gpa = $premium_gpa/2;
}
$gpa_data = [];

$gpa_data['policy_mem_sum_insured'] = $sum_insured;
$gpa_data['policy_mem_gender'] = $all_members_ghi[$i]['gender'];
$gpa_data['policy_mem_dob'] = $all_members_ghi[$i]['dob'];
$gpa_data['age'] = $all_members_ghi[$i]['age'];
$gpa_data['fr_id'] = $all_members_ghi[$i]['fr_id'];
$gpa_data['policy_member_first_name'] = $all_members_ghi[$i]['firstname'];
$gpa_data['policy_member_last_name'] = $all_members_ghi[$i]['lastname'];
$gpa_data['policy_detail_id'] = $gpa_policy_detail_id['policy_detail_id'];
$gpa_data['family_relation_id'] = $all_members_ghi[$i]['family_relation_id'];
$gpa_data['familyConstruct'] = explode('+',$family_construct)[0];
$gpa_data['age_type'] = $all_members_ghi[$i]['age_type'];
$data['policy_member_id'] = $all_members_ghi[$i]['policy_member_id'];
$gpa_data['policy_mem_sum_premium'] = $premium_gpa ;
$gpa_data['policy_member_email_id'] = $all_members_ghi[$i]['policy_member_email_id'];
$gpa_data['policy_member_mob_no'] = $all_members_ghi[$i]['policy_member_mob_no'];
// print_pre($gpa_data);exit;
if($gpa_go_ahead & ($all_members_ghi[$i]['fr_id'] == 1 || $all_members_ghi[$i]['fr_id'] == 0)){

if($action == 'update'){
$this->db->where('family_relation_id', $all_members_ghi[$i]['family_relation_id']);
$this->db->where('policy_detail_id', $gpa_policy_detail_id['policy_detail_id']);
$this->db->update('employee_policy_member',$gpa_data);
$gpa_data['policy_sub_type_id'] = 2;
$relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
$gpa_data['relationship'] = $relation['fr_name'];



$all_members[] = $gpa_data;
}else if($action == 'insert'){

$this->db->insert('employee_policy_member',$gpa_data);
$gpa_data['policy_sub_type_id'] = 2;
$relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
$gpa_data['relationship'] = $relation['fr_name'];



$all_members[] = $gpa_data;
}


}else{
	if($all_members_ghi[$i]['fr_id'] == 1 || $all_members_ghi[$i]['fr_id'] == 0){
$this->db->where('family_relation_id', $all_members_ghi[$i]['family_relation_id']);
$this->db->where('policy_detail_id', $gpa_policy_detail_id['policy_detail_id']);
$this->db->update('employee_policy_member',$gpa_data);
$gpa_data['policy_sub_type_id'] = 2;
$relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
$gpa_data['relationship'] = $relation['fr_name'];
$all_members[] = $gpa_data;
	}
	

}

//update logsssssss
$gci_go_ahead = ($fr_id !== '') ? (($fr_id == $all_members_ghi[$i]['fr_id']) ? true : false)   : true;
// echo $gci_go_ahead;exit;

$gci_data = [];
$gci_data['policy_mem_sum_insured'] = $sum_insured;
$gci_data['policy_mem_gender'] = $all_members_ghi[$i]['gender'];
$gci_data['policy_mem_dob'] = $all_members_ghi[$i]['dob'];
$gci_data['age'] = $all_members_ghi[$i]['age'];
$gci_data['age_type'] = $all_members_ghi[$i]['age_type'];

$gci_data['fr_id'] = $all_members_ghi[$i]['fr_id'];
$gci_data['policy_member_first_name'] = $all_members_ghi[$i]['firstname'];
$gci_data['policy_member_last_name'] = $all_members_ghi[$i]['lastname'];
$gci_data['policy_detail_id'] = $gci_policy_detail_id['policy_detail_id'];
$gci_data['family_relation_id'] = $all_members_ghi[$i]['family_relation_id'];
$data['policy_member_id'] = $all_members_ghi[$i]['policy_member_id'];
$gci_data['familyConstruct'] =  explode('+',$family_construct)[0];
$gci_data['policy_mem_sum_premium'] = $this->get_premium_from_policy($gci_policy_detail_id['policy_detail_id'],$sum_insured,$family_construct,$all_members_ghi[$i]['age']);
$gci_data['policy_member_email_id'] = $all_members_ghi[$i]['policy_member_email_id'];
$gci_data['policy_member_mob_no'] = $all_members_ghi[$i]['policy_member_mob_no'];
// print_pre($gci_data);exit;

if($only_gci_insert){
	// echo '****';
	$gci_go_ahead = true;
	$action1 = 'insert';
}else{
	// echo '####';
	$action1 = '';
}
// echo "<BR>".$gci_go_ahead."===".$gci."====".$all_members_ghi[$i]['fr_id'];//exit;
if($gci_go_ahead  && $gci && ($all_members_ghi[$i]['fr_id'] == 1 || $all_members_ghi[$i]['fr_id'] == 0)){
// echo "insert GCI";exit;
if($action == 'update' && $action1 == ''){
$this->db->where('family_relation_id', $all_members_ghi[$i]['family_relation_id']);
$this->db->where('policy_detail_id', $gci_policy_detail_id['policy_detail_id']);
$this->db->update('employee_policy_member',$gci_data);

$gci_data['policy_sub_type_id'] = 3;
$relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
$gci_data['relationship'] = $relation['fr_name'];



$all_members[] = $gci_data;
}else if($action == 'insert' || $action1 == 'insert'){
$this->db->insert('employee_policy_member',$gci_data);
$gci_data['policy_sub_type_id'] = 3;
$relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
$gci_data['relationship'] = $relation['fr_name'];

$all_members[] = $gci_data;
}

}else{
	// echo "update GCI";exit;
	if($gci && ($all_members_ghi[$i]['fr_id'] == 1 || $all_members_ghi[$i]['fr_id'] == 0)){
$this->db->where('family_relation_id', $all_members_ghi[$i]['family_relation_id']);
$this->db->where('policy_detail_id', $gci_policy_detail_id['policy_detail_id']);
$this->db->update('employee_policy_member',$gci_data);

$gci_data['policy_sub_type_id'] = 3;
$relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
$gci_data['relationship'] = $relation['fr_name'];
$all_members[] = $gci_data;
	}
	else{
		
		$this->db->where(['family_relation_id' => $all_members_ghi[$i]['family_relation_id'],'policy_detail_id' => $gci_policy_detail_id['policy_detail_id'] ])->delete("employee_policy_member");
	}
	

}
//update in logssssssssssssssssssssssss
$logs_array['data'] = ["type" => "update_gpa_gci", "req" => json_encode($all_members) , "lead_id" => $all_members_ghi[$i]['family_relation_id'], "product_id" => $product_id];
        $this
            ->Logs_m
            ->insertLogs($logs_array);

}

//print_pre($all_members);exit;
return $all_members;

}
public function get_member_exist($family_relation_id,$policy_detail_id)
{
$check = $this->db->query("select * from employee_policy_member where family_relation_id = '$family_relation_id' AND policy_detail_id = '$policy_detail_id'")->row_array();

if(!empty($check))
{
	return true;
}
else
{
return false;
}

}
function get_all_member_data_new_gci($product_id,$action,$ghi_policy_detail_id,$sum_insured,$family_construct,$update_ghi = false,$gci =false,$fr_id,$emp_id,$only_gci_insert = false){
//for R10
//echo $action."---".$ghi_policy_detail_id."---".$sum_insured."---".$family_construct."---".$update_ghi."---".$gci."---".$fr_id."---".$emp_id."---".$only_gci_insert;exit;
$parent_id  = $this->db->select('parent_policy_id')->from('employee_policy_detail')->where('policy_detail_id',$ghi_policy_detail_id)->get()->row_array();


$gpa_policy_detail_id = $this->db->select('policy_detail_id,suminsured_type')->from('employee_policy_detail')
->where('parent_policy_id',$parent_id['parent_policy_id'])
->where('policy_sub_type_id',2)
->get()
->row_array();
// echo $this->db->last_query();exit;
$gci_policy_detail_id = $this->db->select('policy_detail_id,suminsured_type')->from('employee_policy_detail')
->where('parent_policy_id',$parent_id['parent_policy_id'])
->where('policy_sub_type_id',3)
->get()
->row_array();
$all_members_ghi  = $this->get_all_member_data_new($emp_id, $ghi_policy_detail_id,true);
$all_members = [];
for($i =0; $i < count($all_members_ghi); $i++){


$data = [];

$data['policy_mem_sum_insured'] = $sum_insured;
$data['policy_mem_gender'] = $all_members_ghi[$i]['gender'];
$data['policy_mem_dob'] = $all_members_ghi[$i]['dob'];
$data['age'] = $all_members_ghi[$i]['age'];
$data['age_type'] = $all_members_ghi[$i]['age_type'];
$data['fr_id'] = $all_members_ghi[$i]['fr_id'];
$data['policy_member_first_name'] = $all_members_ghi[$i]['firstname'];
$data['policy_member_last_name'] = $all_members_ghi[$i]['lastname'];
$data['policy_detail_id'] = $ghi_policy_detail_id;
$data['family_relation_id'] = $all_members_ghi[$i]['family_relation_id'];
$data['familyConstruct'] = $family_construct;
$data['policy_member_id'] = $all_members_ghi[$i]['policy_member_id'];
$data['policy_mem_sum_premium'] = $this->get_premium_from_policy($ghi_policy_detail_id,$sum_insured,$family_construct,$all_members_ghi[$i]['age']);
$data['policy_member_email_id'] = $all_members_ghi[$i]['policy_member_email_id'];
$data['policy_member_mob_no'] = $all_members_ghi[$i]['policy_member_mob_no'];
if($update_ghi){
$this->db->where('policy_member_id', $all_members_ghi[$i]['policy_member_id']);
$this->db->update('employee_policy_member',$data);
$data['policy_sub_type_id'] = 1;
$relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
$data['relationship'] = $relation['fr_name'];

//$all_members[] = $data;
}else{
	$data['policy_sub_type_id'] = 1;
	
	$relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
	$data['relationship'] = $relation['fr_name'];
	//$all_members[] = $data;
}


$gpa_go_ahead = ($fr_id !== '') ? (($fr_id == $all_members_ghi[$i]['fr_id']) ? true : false)   : true;

$premium_gpa = $this->get_premium_from_policy($gpa_policy_detail_id['policy_detail_id'],$sum_insured,$family_construct,$all_members_ghi[$i]['age']);
$familyConstructs = explode('+',$family_construct)[0];
// if($familyConstructs == '2A')
// {
	// $premium_gpa = $premium_gpa/2;
// }
$gpa_data = [];

$gpa_data['policy_mem_sum_insured'] = $sum_insured;
$gpa_data['policy_mem_gender'] = $all_members_ghi[$i]['gender'];
$gpa_data['policy_mem_dob'] = $all_members_ghi[$i]['dob'];
$gpa_data['age'] = $all_members_ghi[$i]['age'];
$gpa_data['fr_id'] = $all_members_ghi[$i]['fr_id'];
$gpa_data['policy_member_first_name'] = $all_members_ghi[$i]['firstname'];
$gpa_data['policy_member_last_name'] = $all_members_ghi[$i]['lastname'];
$gpa_data['policy_detail_id'] = $gpa_policy_detail_id['policy_detail_id'];
$gpa_data['family_relation_id'] = $all_members_ghi[$i]['family_relation_id'];
$gpa_data['familyConstruct'] = explode('+',$family_construct)[0];
$gpa_data['age_type'] = $all_members_ghi[$i]['age_type'];
$data['policy_member_email_id'] = $all_members_ghi[$i]['policy_member_email_id'];
$gpa_data['policy_member_mob_no'] = $all_members_ghi[$i]['policy_member_mob_no'];
$gpa_data['policy_member_id'] = $all_members_ghi[$i]['policy_member_id'];
$gpa_data['policy_mem_sum_premium'] = $premium_gpa ;
if($gpa_go_ahead && ($all_members_ghi[$i]['fr_id'] == 1 || $all_members_ghi[$i]['fr_id'] == 0)){

if($action == 'update'){
$this->db->where('family_relation_id', $all_members_ghi[$i]['family_relation_id']);
$this->db->where('policy_detail_id', $gpa_policy_detail_id['policy_detail_id']);
$this->db->update('employee_policy_member',$gpa_data);
 
$logs_array['data'] = ["type" => "update_gpa", "req" => json_encode($this->db->last_query()) , "lead_id" => $emp_id, "product_id" => $product_id];
        $this
            ->Logs_m
            ->insertLogs($logs_array);
//print_R($this->db->last_query());
$gpa_data['policy_sub_type_id'] = 2;
$relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
$gpa_data['relationship'] = $relation['fr_name'];

//print_R($gpa_data);
 $get_gpa_member = $this->get_member_exist($all_members_ghi[$i]['family_relation_id'],$gpa_policy_detail_id['policy_detail_id']);
/*if($get_gpa_member)
{
        $all_members[] = $gpa_data;
}*/
$gpa_data['policy_member_id'] = $all_members_ghi[$i]['policy_member_id'];
$all_members[] = $gpa_data;
}else if($action == 'insert'){
$get_gpa_member = $this->get_member_exist($all_members_ghi[$i]['family_relation_id'],$gpa_policy_detail_id['policy_detail_id']);
if(empty($get_gpa_member)){
$this->db->insert('employee_policy_member',$gpa_data);
 
$logs_array['data'] = ["type" => "insert_gpa", "req" => json_encode($this->db->last_query()) , "lead_id" => $emp_id, "product_id" => $product_id];
        $this
            ->Logs_m
            ->insertLogs($logs_array);
			
}
$gpa_data['policy_sub_type_id'] = 2;
$relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
$gpa_data['relationship'] = $relation['fr_name'];

 $get_gpa_member = $this->get_member_exist($all_members_ghi[$i]['family_relation_id'],$gpa_policy_detail_id['policy_detail_id']);
/*if($get_gpa_member)
{
        $all_members[] = $gpa_data;
}*/
$gpa_data['policy_member_id'] = $all_members_ghi[$i]['policy_member_id'];
$all_members[] = $gpa_data;
}


}else{
	if($all_members_ghi[$i]['fr_id'] == 1 || $all_members_ghi[$i]['fr_id'] == 0){
$this->db->where('family_relation_id', $all_members_ghi[$i]['family_relation_id']);
$this->db->where('policy_detail_id', $gpa_policy_detail_id['policy_detail_id']);
$this->db->update('employee_policy_member',$gpa_data);
$logs_array['data'] = ["type" => "update_gpa", "req" => json_encode($this->db->last_query()) , "lead_id" => $emp_id, "product_id" => $product_id];
        $this
            ->Logs_m
            ->insertLogs($logs_array);
$gpa_data['policy_sub_type_id'] = 2;
$relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
$gpa_data['relationship'] = $relation['fr_name'];
 $get_gpa_member = $this->get_member_exist($all_members_ghi[$i]['family_relation_id'],$gpa_policy_detail_id['policy_detail_id']);
/*if($get_gpa_member)
{
        $all_members[] = $gpa_data;
}*/
$gpa_data['policy_member_id'] = $all_members_ghi[$i]['policy_member_id'];
$all_members[] = $gpa_data;
	}
	

}

$gci_go_ahead = ($fr_id !== '') ? (($fr_id == $all_members_ghi[$i]['fr_id']) ? true : false)   : true;


$gci_data = [];
$gci_data['policy_mem_sum_insured'] = $sum_insured;
$gci_data['policy_mem_gender'] = $all_members_ghi[$i]['gender'];
$gci_data['policy_mem_dob'] = $all_members_ghi[$i]['dob'];
$gci_data['age'] = $all_members_ghi[$i]['age'];
$gci_data['age_type'] = $all_members_ghi[$i]['age_type'];

$gci_data['fr_id'] = $all_members_ghi[$i]['fr_id'];
$gci_data['policy_member_first_name'] = $all_members_ghi[$i]['firstname'];
$gci_data['policy_member_last_name'] = $all_members_ghi[$i]['lastname'];
$gci_data['policy_detail_id'] = $gci_policy_detail_id['policy_detail_id'];
$gci_data['family_relation_id'] = $all_members_ghi[$i]['family_relation_id'];
$data['policy_member_id'] = $all_members_ghi[$i]['policy_member_id'];
$gci_data['familyConstruct'] =  explode('+',$family_construct)[0];
$gci_data['policy_mem_sum_premium'] = $this->get_premium_from_policy($gci_policy_detail_id['policy_detail_id'],$sum_insured,$family_construct,$all_members_ghi[$i]['age']);



if($only_gci_insert){
	$gci_go_ahead = true;
	$action1 = 'insert';
}else{
	$action1 = '';
}

if($gci_go_ahead  && $gci && ($all_members_ghi[$i]['fr_id'] == 1 || $all_members_ghi[$i]['fr_id'] == 0)){

if($action == 'update' && $action1 == ''){
$this->db->where('family_relation_id', $all_members_ghi[$i]['family_relation_id']);
$this->db->where('policy_detail_id', $gci_policy_detail_id['policy_detail_id']);
$this->db->update('employee_policy_member',$gci_data);
$logs_array['data'] = ["type" => "update_gci_action", "req" => json_encode($this->db->last_query()) , "lead_id" => $emp_id, "product_id" => $product_id];
        $this
            ->Logs_m
            ->insertLogs($logs_array);

$gci_data['policy_sub_type_id'] = 3;
$relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
$gci_data['relationship'] = $relation['fr_name'];

 $get_gci_member = $this->get_member_exist($all_members_ghi[$i]['family_relation_id'],$gci_policy_detail_id['policy_detail_id']);
/*if($get_gci_member)
{
        $all_members[] = $gci_data;
}*/
$gci_data['policy_member_id'] = $all_members_ghi[$i]['policy_member_id'];
$all_members[] = $gci_data;
}else if($action == 'insert' || $action1 == 'insert'){
	$get_gci_member = $this->get_member_exist($all_members_ghi[$i]['family_relation_id'],$gci_policy_detail_id['policy_detail_id']);
	if(empty($get_gci_member)){
$this->db->insert('employee_policy_member',$gci_data);
 
$logs_array['data'] = ["type" => "insert_gci", "req" => json_encode($this->db->last_query()) , "lead_id" => $emp_id, "product_id" => $product_id];
        $this
            ->Logs_m
            ->insertLogs($logs_array);
			
	}
$gci_data['policy_sub_type_id'] = 3;
$relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
$gci_data['relationship'] = $relation['fr_name'];
 $get_gci_member = $this->get_member_exist($all_members_ghi[$i]['family_relation_id'],$gci_policy_detail_id['policy_detail_id']);
/*if($get_gci_member)
{
        $all_members[] = $gci_data;
}*/
$gci_data['policy_member_id'] = $all_members_ghi[$i]['policy_member_id'];
$all_members[] = $gci_data;
}

}else{
	if($gci && ($all_members_ghi[$i]['fr_id'] == 1 || $all_members_ghi[$i]['fr_id'] == 0)){
$this->db->where('family_relation_id', $all_members_ghi[$i]['family_relation_id']);
$this->db->where('policy_detail_id', $gci_policy_detail_id['policy_detail_id']);
$this->db->update('employee_policy_member',$gci_data);
$logs_array['data'] = ["type" => "update_gci", "req" => json_encode($this->db->last_query()) , "lead_id" => $emp_id, "product_id" => $product_id];
        $this
            ->Logs_m
            ->insertLogs($logs_array);

$gci_data['policy_sub_type_id'] = 3;
$relation = $this->get_data('master_family_relation','fr_id',$all_members_ghi[$i]['fr_id']);
$gci_data['relationship'] = $relation['fr_name'];
 $get_gci_member = $this->get_member_exist($all_members_ghi[$i]['family_relation_id'],$gci_policy_detail_id['policy_detail_id']);
/*if($get_gci_member)
{
        $all_members[] = $gci_data;
}*/
$gci_data['policy_member_id'] = $all_members_ghi[$i]['policy_member_id'];
$all_members[] = $gci_data;
	}
	else{
		
		$this->db->where(['family_relation_id' => $all_members_ghi[$i]['family_relation_id'],'policy_detail_id' => $gci_policy_detail_id['policy_detail_id'] ])->delete("employee_policy_member");

$logs_array['data'] = ["type" => "delete_gci", "req" => json_encode($this->db->last_query()) , "lead_id" => $emp_id, "product_id" => $product_id];
        $this
            ->Logs_m
            ->insertLogs($logs_array);	
}
	

}
//update in logssssssssssssssssssssssss
$logs_array['data'] = ["type" => "update_gci", "req" => json_encode($this->db->last_query()) , "lead_id" => $emp_id, "product_id" => $product_id];
        $this
            ->Logs_m
            ->insertLogs($logs_array);

}

//print_pre($all_members);exit;
return $all_members;

}

public function family_details_insert_new()
    {
		// print_pre($this->input->post());exit;
	
		$this->form_validation->set_error_delimiters('','');
        $this->form_validation->set_rules('sum_insure', 'sum insure', 'required|trim|integer');
		$this->form_validation->set_rules('familyConstruct', 'family Construct', 'required|trim');
		$this->form_validation->set_rules('premium', 'premium', 'required|trim');
		$this->form_validation->set_rules('businessType', 'Business Type', 'required|trim');
		$this->form_validation->set_rules('family_members_id[]', 'Relation With Proposer', 'required|trim');
		$this->form_validation->set_rules('family_gender[]', 'family gender', 'required|trim');
		//$this->form_validation->set_rules('family_salutation[]', 'family salutation', 'required|trim');
		$this->form_validation->set_rules('first_name[]', 'first_name', 'required|trim');
		//$this->form_validation->set_rules('last_name[]', 'last name', 'required|trim');
		$this->form_validation->set_rules('family_date_birth[]', 'family date birth', 'required|trim');
		$this->form_validation->set_rules('age[]', 'age', 'required|trim');
		//$this->form_validation->set_rules('age_type[]', 'age type', 'required|trim');
	
		if ($this->form_validation->run() == FALSE)
		{
			 $z = validation_errors();
			 return ["status" => false, "message" => $z ];   
		}
		
		
		$parent_id = $this->parent_id;
		$empId = $this->emp_id;
		$fam_post = $this->input->post(null,true);
		if($fam_post != null || !empty($fam_post))
		$policy_no = $this->input->post('policy_no',true);
		$declare = $this->input->post('declare',true);
		$sub_type_check = $this->input->post('sub_type_check',true);
		$family_members_id = $this->input->post('family_members_id',true);
		$sum_insure = $this->input->post('sum_insure',true);
		$premium = $this->input->post('premium',true);
		$businessType =$this->input->post('businessType',true);
		$family_date_birth = $this->input->post('family_date_birth',true);
		$age = $this->input->post('age',true);
		$age_type = $this->input->post('age_type',true);
		$first_name = $this->input->post('first_name',true);
		$middle_name = $this->input->post('middle_name',true);
		$last_name = $this->input->post('last_name',true);
		$familyConstruct = $this->input->post('familyConstruct',true);
		$tenure = $this->input->post('tenure',true);
		$edit = $this->input->post('edit',true);
		$family_gender = $this->input->post('family_gender',true);
		$subtype_text =$this->input->post('subtype_text',true);
		//$remark = $this->input->post('remark',true);
		$family_salutation = $this->input->post('family_salutation',true);
		$edit_member_id = $this->input->post('edit_member_id',true);
		$chronic = $this->input->post('chronic',true);
		$declare_member = json_decode($declare,true);
		$chronic = json_decode($chronic,true);		
		$gci_options = $this->input->post('GCI_optional',true);
		
		$product_id = $this->input->post('plan_name',true);
		$hidden_policy_id = $this->input->post('hidden_policy_id',true);
		$hidden_deductable = $this->input->post('hidden_deductable',true);
		$mem_email_id = $this->input->post('mem_email_id',true);
		$mem_mob_no = $this->input->post('mem_mob_no',true);

		$familyConstruct = str_replace(" ", "+", trim($familyConstruct));
		// echo "GCI => ".$gci_options;exit;
		if($gci_options == 'Yes'){
			$gci_option = true;
		}else{
			$gci_option = false;
		}
		// echo $product_id;exit;
		// print_R($_POST);
		// foreach ($family_members_id as $key => $val) 
		// { 
		// echo $first_name[$key];
		// echo $key;
		// }
		// exit;
		$get_lead_id = $this->db->query("select lead_id,product_id,kid2_rel,kid1_rel,kid2_dob,kid1_dob,spouse_dob from employee_details where emp_id = '$empId' ")->row_array();
		if($get_lead_id['lead_id'] != 0)
		{
			$lead_id = $get_lead_id['lead_id'];
			$product_id = $get_lead_id['product_id'];
		}
		// Update Except R06
		if($product_id!='R06'){
			$update_log=['product_id'=>$product_id];
			$this->db->where('lead_id',$lead_id);
			$this->db->update('logs_post_data',$update_log);
		}		

		
		//Add Data into logs Table			
		$logs_array = ["type" => "post_insured_member","req" => json_encode($_POST), "lead_id" => $lead_id,"product_id" => $product_id];
		$this->db->insert("logs_post_data",$logs_array);
		
		
		if($premium == 'undefined' || $premium == '')
		{
			return ["status" => false, "message" => "Invalid Premium"];
		}

		$declare_member = json_decode($declare,true);
		if($product_id == 'T03'){
			$policy_no = $hidden_policy_id;
            $deductable = $hidden_deductable;
            if($hidden_policy_id == TELE_HEALTHPROINFINITY_GHI_GPA){
	            $gpa_option = true;
	        }else{
	            $gpa_option = false;
	        }
		}
		$policy_no = explode(",", $policy_no);
      	// print_pre($policy_no);exit;
		foreach ($policy_no as $policy_no) 
		{
			// echo $policy_no.'----';
			//print_pre($sub_type_check);
			//continue;
			if(!empty($sub_type_check))
			{
				foreach ($sub_type_check as $key1 => $sub_type_checks) 
				{       
					if($edit == 0)
					{
						$query=$this->db->query("select count(declare_sub_type_id) as total from employee_declare_member_sub_type where emp_id='$empId' AND product_id='$parent_id' AND policy_detail_id='$policy_no' ")->result_array();
							
						if($query[0]['total']>0)
						{
							//return ["status" => false, "message" => "As Per The Product Only One Chronic Member Is Allowed In A Policy" ,"check" => "declaration"];
						}
					}
					else
					{
						$query=$this->db->query("select count(declare_sub_type_id) as total from employee_declare_member_sub_type where emp_id='$empId' AND product_id='$parent_id' AND policy_detail_id='$policy_no' AND policy_member_id !='$edit'")->result_array();
							
						if($query[0]['total']>0)
						{
							//return ["status" => false, "message" => "As Per The Product Only One Chronic Member Is Allowed In A Policy" ,"check" => "declaration"];
						}
					}
				}
			}
			if(@$declare_member)
			foreach ($declare_member as $key => $valuei) 
			{       
				if($valuei['format']=='Yes')
				{
					//return ["status" => false, "message" => "Basis information provided this proposal cannot be processed online" ,"check" => "declaration"];
				}
			}

            if (count($policy_no) > 1) 
			{
                $get_policy_type = $this->db->select("*")->from("employee_policy_detail")->where("policy_detail_id", $policy_no)->get()->row_array();
                if ($get_policy_type['policy_sub_type_id'] == 1) 
				{
                    $go = true;
                }
            } 
			else
			{
                $go = true;
            }

			//check min max age
		    $premium_logic =  $this->db->select("*")->from("policy_age_limit")->where("policy_detail_id", $policy_no)->get()->row_array();
		   
			$premium_get = $this->db->select("suminsured_type")->from("employee_policy_detail")->where("policy_detail_id", $policy_no)->get()->row_array();
			
			$premium_type_tbl = $premium_get['suminsured_type'];
			
			if($premium_type_tbl == 'memberAge')
			{
				$get_cal = $this->db->query("select premium_with_tax from policy_creation_age where policy_id = '$policy_no' AND sum_insured = '$sum_insure'")->row_array();
			}
			elseif($premium_type_tbl == "family_construct")
			{
				$get_cal = $this->db->query("select PremiumServiceTax from family_construct_wise_si where policy_detail_id = '$policy_no' AND family_type = '$familyConstruct' AND sum_insured = '$sum_insure'")->row_array();
			}
			//echo $go . '-- <br>';
			if ($go) 
			{
				$adult_count = [];
				$child_count = [];
				$sposue_check = [];
				$tempArr = [];
				//echo "go";
				foreach ($family_members_id as $key => $val) 
				{
					if($product_id == 'R06')
					{
						if($familyConstruct == '1A' || $familyConstruct == '1A+1K' || $familyConstruct == '1A+2K') {
							if ($val == 1){
								array_push($sposue_check, $val);
							}
						}
					}
					if ($val == 0 || $val == 1){
						array_push($adult_count, $val);
					}else{
						array_push($child_count, $val);
					}
				
					//$edit = 0;
					if ($val == 0) 
					{
						
						// $family_relation_id = $this->db->select("family_relation_id")->from("family_relation")->where("emp_id", $empId)->where("family_id", 0)->get()->row_array();
						
						// $member_exist_check= $this->db->select("policy_member_id")->from("employee_policy_member")->where("family_relation_id", $family_relation_id['family_relation_id'])->where("policy_detail_id", $policy_no)->get()->row_array();
						//print_pre($this->db->last_query());exit;
						if($edit_member_id[$key]){
							
							//$edit = $member_exist_check['policy_member_id'];
							$edit = $edit_member_id[$key];
							
						}
								
					}else{
						
						// $family_relation_id = $this->db->select("*")->from("family_relation AS fr,employee_family_details AS efd")->where("fr.family_id = efd.family_id")->where('fr.emp_id',$empId)->where('efd.fr_id',$val)->get()->row_array();
						
						// $member_exist_check= $this->db->select("policy_member_id")->from("employee_policy_member")->where("family_relation_id", $family_relation_id['family_relation_id'])->where("policy_detail_id", $policy_no)->get()->row_array();
						if($edit_member_id[$key]){
							
							//$edit = $member_exist_check['policy_member_id'];
							$edit = $edit_member_id[$key];
							
						}
						
					}
					
					//CHECK VALIDATIONS
					$check = $this->check_validations($val,$policy_no,$empId,$age[$key],$age_type[$key],$familyConstruct,$edit);
					// print_pre($check);
					if($check["message"] != "true")
					{
						return ["status" => false, "message" => $check["message"]];
					}

					if($edit != '' && $edit != undefined){
						$action = 'update';
					}else{
						$action = 'insert';
					}

					if(empty($tempArr)){
						$tempArr[$key] = array("fname" => $first_name[$key],
								   "lname" => $last_name[$key],
								   "dob" => $family_date_birth[$key],
								   "policy_no" => $policy_no);
						//array_push($tempArr,$t);
					}else{
						// print_pre($tempArr);exit;
						//check if in current lead any member is repeating
						foreach ($tempArr as $key1 => $value1) {
							
							if($value1['policy_no'] == $policy_no && $value1['fname'] == $first_name[$key] && $value1['lname'] == $last_name[$key] && $value1['dob'] == $family_date_birth[$key]){						
								return ["status" => false, "message" => "member already exist with same name and dob !" ]; 
							}else{
								$tempArr[$key] = array("fname" => $first_name[$key],
								   "lname" => $last_name[$key],
								   "dob" => $family_date_birth[$key],
								   "policy_no" => $policy_no);
							}
						}
						//print_pre($tempArr);exit;
					}
					//print_pre($tempArr);exit;
					//echo $action.'---'.$edit;//exit;

					// updated by ankita on 13-05-2021 - for dedupe logic
					$common_dedupe_logic = common_dedupe_logic($first_name[$key],$last_name[$key],$family_date_birth[$key],$product_id,$sum_insure,$familyConstruct,$empId,$val,$action);
						//print_pre($common_dedupe_logic);//exit;
					if($common_dedupe_logic['status'] != 'success'){
						return ["status" => false, "message" => $common_dedupe_logic['msg'] ]; 
					}
					
				}
				//exit;
				//check adult and child count as per family construct 
				$familyConstructArr = explode('+', $familyConstruct);
				$familyConstruct_adult = str_replace('A', '', $familyConstructArr[0]);
				$familyConstruct_child = 0;
				if(isset($familyConstructArr[1])){
					$familyConstruct_child = str_replace('K', '', $familyConstructArr[1]);
				}

				if((int)$familyConstruct_adult != count($adult_count) && (int)$familyConstruct_child != count($child_count)){
					return ["status" => false, "message" => "Member not added as per policy"];
				}

				if(!empty($sposue_check)){
					return ["status" => false, "message" => "Self is mandatory in this policy."];
				}
				
				foreach ($family_members_id as $key => $val) 
				{ 
				$edit = '';
				//echo $val;//exit;
				if ($val == 0) 
				{
					
					//disease code 
					
							$family_relation_id = $this->db->select("family_relation_id")->from("family_relation")->where("emp_id", $empId)->where("family_id", 0)->get()->row_array();
							
							$member_exist_check= $this->db->select("policy_member_id")->from("employee_policy_member")->where("family_relation_id", $family_relation_id['family_relation_id'])->where("policy_detail_id", $policy_no)->get()->row_array();
							
							if($edit_member_id[$key]){
							
								//$edit = $member_exist_check['policy_member_id'];
								$edit = $edit_member_id[$key];
								
							}
							//echo $edit;exit;

							$member_array = [
												"policy_detail_id" => $policy_no,
												"family_relation_id" => $family_relation_id['family_relation_id'],
												"policy_mem_sum_insured" => $sum_insure,
												"policy_mem_sum_premium" => $premium,
												"policy_mem_gender" => $family_gender[$key],
												"policy_mem_salutation" => $family_salutation[$key],
												"policy_mem_dob" => $family_date_birth[$key],
												"age" => $age[$key],
												"age_type" => $age_type[$key],
												"member_status" => "pending",
												"fr_id" => $val,
												"policy_member_first_name" => $first_name[$key],
												"policy_member_middle_name" => $middle_name,
												"policy_member_last_name" => $last_name[$key],
												"familyConstruct" => $familyConstruct,
												"tenure" => $tenure,
												"businessType" => $businessType,
												"policy_member_email_id" => $mem_email_id[$key],
												"policy_member_mob_no" => $mem_mob_no[$key],
												//"remark" => $remark
											];
					if ($edit != 0)
					{
						
						 // echo "!= 0";exit;
						$check = $this->check_validations($val, $policy_no,$empId,$age[$key],$age_type[$key],$familyConstruct,$edit);
						
						if($check["message"] != "true")
						{

							return ["status" => false, "message" => $check["message"]];
						}

						$employee_array = [
												"gender" => $family_gender[$key],
												"bdate" => $family_date_birth[$key],
												"emp_firstname" => $first_name[$key],
												"emp_lastname" => $last_name[$key]												
										  ];
						$this->db->where('emp_id', $empId);
						$this->db->update('employee_details', $employee_array);
						

						//update check
						//first check in employee policy member as it wont be present in proposal member in some situations

						$this->db->where('policy_member_id', $edit);
						$this->db->update('employee_policy_member', $member_array);
						
						
						//Add Data into logs Table	
						$logs_array['data'] = ["type" => "update_insured_member","req" => json_encode($member_array), "lead_id" => $lead_id,"product_id" => $product_id];
						$this->Logs_m->insertLogs($logs_array);
						// echo $product_id;
						if($product_id == 'T01')
							{
								//echo "1866 line no";exit;
								//echo 'update'.'--'.$policy_no.'--'.$sum_insure.'--'.$familyConstruct.'--'.true.'--'.$gci_option.'--'.$val."--".$empId;
								//$product_id,'update',$policy_no,$sum_insure,$familyConstruct,true,$gci_option,$val,$empId
								$data = $this->get_all_member_data_new_gpa($product_id,'update',$policy_no,$sum_insure,$familyConstruct,true,$gci_option,$val,$empId);
							}else if($product_id == 'T03'){
                                if($policy_no != TELE_HEALTHPROINFINITY_GHI_ST){
                                    $data = $this->get_all_member_data_new_gpa_optional('update',$policy_no,$sum_insure,$familyConstruct,true,$gpa_option,$val,$empId);
                                }else{
                                    $data = $this->get_all_member_data_healthproxl($empId, $policy_no, $deductable);
                                }
                                
                            }else if($product_id == 'R12'){
								$data = $this->get_all_member_data_new_gci($product_id,'update',$policy_no,$sum_insure,$familyConstruct,true,$gci_option,$val,$empId);
							}else{
							$data = $this->get_all_member_data_new($empId, $policy_no);

							}
						// print_pre($data);exit;
						//$data = $this->get_all_member_data_new($empId, $policy_no);
						$check_member = $this->db->select("*")
						->from("employee_policy_member")->where("policy_member_id", $edit)->get()->row_array();

						//if it exists in proposal then update it in proposal also
						$check_member_proposal = $this->db->select("*")
												->from("proposal_member")->where("policy_member_id", $edit)->get()->row_array();
												
						if (count($check_member_proposal) > 0)
						{
							$this->db->where('policy_member_id', $edit);
							$this->db->update('proposal_member', $check_member);
						}
						
					   // if(!empty($sub_type_check))
						// {
						   // $query = $this->db->query("Delete  from  employee_declare_member_sub_type where policy_member_id ='".$edit."' ");
						   // foreach ($sub_type_check as $key1 => $sub_type_checks) 
							// {       
						
									// $data_mb_diesease = array(
														// "declare_sub_type_id" =>$sub_type_checks,
														// "policy_detail_id" =>$policy_no,
														// "product_id" =>$parent_id,
														// "policy_member_id" => $edit,
														// "emp_id"=>$empId,
														// "created_date" =>date("Y-m-d H:i:s")

													// );

									// $this->db->insert("employee_declare_member_sub_type", $data_mb_diesease);
									
									//Add Data into logs Table	
									// $logs_array['data'] = ["type" => "update_chronic_diesease","req" => json_encode($data_mb_diesease), "lead_id" => $lead_id,"product_id" => $product_id];
									// $this->Logs_m->insertLogs($logs_array);
							// }
						// }
					   // else
					   // {
							// $query = $this->db->query("Delete  from  employee_declare_member_sub_type where policy_member_id ='".$edit."' ");
					   // }
					
						   // $query_dels = $this->db->query("Delete  from  employee_declare_member_data where policy_member_id ='".$edit."' ");
					
							// foreach ($declare_member as $key => $valuedeclare)
							// {
								// if ($valuedeclare['format'] != '') 
								// {
								
									// $data_mb = array(
														// "p_member_id" =>$valuedeclare['question'],
														// "format" =>$valuedeclare['format'],
														// "policy_detail_id" =>$policy_no,
														// "product_id" =>$parent_id,
														// "policy_member_id" => $edit,
														// "emp_id"=>$empId,
														// "created_date" =>date("Y-m-d H:i:s")
													// );

												// $this->db->insert("employee_declare_member_data", $data_mb);
												
												//Add Data into logs Table	
												// $logs_array['data'] = ["type" => "update_diesease_member_answer","req" => json_encode($data_mb), "lead_id" => $lead_id,"product_id" => $product_id];
												// $this->Logs_m->insertLogs($logs_array);
								// }
							// }
							
							//$ghd_check = $this->ghd_declined_insert($myGHD,$empId,$edit,'update',$family_members_id);
							if($ghd_check['status'] == 'false')
							{
								$arr[] = 0;
							}
							
							$arr[] = 1;
					}

					//will have to write if edit or insert
					//$ghd_check = $this->ghd_declined_insert($myGHD,$empId,$id_members,'insert',$family_members_id);
					//if($ghd_check['status'] == 'false')
					//{
						//return ["status" => false, "message" => $ghd_check['message'] ];
					//}
					else{
						
					 // echo "== 0";exit;
						
						//CHECK VALIDATIONS
						$check = $this->check_validations($val, $policy_no,$empId,$age[$key],$age_type[$key],$familyConstruct);
						
						// Return Status Message for Validtion Rules
						if($check["message"] != "true"){
							return ["status" => false, "message" => $check["message"]];
						}
					$this->db->insert("employee_policy_member", $member_array);
					//print_pre($this->db->last_query());
					$id_members = $this->db->insert_id();
					
					//Add Data into logs Table	
					$logs_array['data'] = ["type" => "insert_insured_member","req" => json_encode($member_array), "lead_id" => $lead_id,"product_id" => $product_id];
					$this->Logs_m->insertLogs($logs_array);
					
					// if(!empty($sub_type_check))
					// {
						// foreach ($sub_type_check as $key1 => $sub_type_checks) 
						// {       
								// $data_mb_diesease = array(
									// "declare_sub_type_id" =>$sub_type_checks,
									// "policy_detail_id" =>$policy_no,
									// "product_id" =>$parent_id,
									// "policy_member_id" => $id_members,
									// "emp_id"=>$empId,
									// "created_date" =>date("Y-m-d H:i:s")
								// );

								// $this->db->insert("employee_declare_member_sub_type", $data_mb_diesease);
								
								//Add Data into logs Table	
								// $logs_array['data'] = ["type" => "insert_chronic_diesease","req" => json_encode($data_mb_diesease), "lead_id" => $lead_id,"product_id" => $product_id];
								// $this->Logs_m->insertLogs($logs_array);
						// }
					// }
					
					// if(@$declare_member)
					// foreach ($declare_member as $key => $value) 
					// {       
						// if($value['format']!='')
						// {
							// $data_mb = array(
								// "p_member_id" =>$value['question'],
								// "format" =>$value['format'],
								// "policy_detail_id" =>$policy_no,
								// "product_id" =>$parent_id,
								// "policy_member_id" => $id_members,
								// "emp_id"=>$empId,
								// "created_date" =>date("Y-m-d H:i:s")
							// );

							// $this->db->insert("employee_declare_member_data", $data_mb);
							
							//							Add Data into logs Table	
							// $logs_array['data'] = ["type" => "insert_diesease_member_answer","req" => json_encode($data_mb), "lead_id" => $lead_id,"product_id" => $product_id];
							// $this->Logs_m->insertLogs($logs_array);
						// }
					// }
					
					
					
					if ($this->db->affected_rows()) 
					{
						// echo "here==".$product_id;
						if($product_id == 'T01')
						{
							
						//echo 'insert'.'--'.$policy_no.'--'.$sum_insure.'--'.$familyConstruct.'--'.false.'-- gci=>'.$gci_option.'--'.$val."--".$empId;//exit;
						$data = $this->get_all_member_data_new_gpa($product_id,'insert',$policy_no,$sum_insure,$familyConstruct,false,$gci_option,$val,$empId);
						}else if($product_id == 'R12'){
							$data = $this->get_all_member_data_new_gci($product_id,'insert',$policy_no,$sum_insure,$familyConstruct,false,$gci_option,$val,$empId);
						}else if($product_id == 'T03'){
                            //echo "here";
                            if($policy_no != TELE_HEALTHPROINFINITY_GHI_ST){
                               // echo "in";exit;
                                $data = $this->get_all_member_data_new_gpa_optional('insert',$policy_no,$sum_insure,$familyConstruct,false,$gpa_option,$val,$empId);
                            }else{
                                //echo "out";exit;
                                $data = $this->get_all_member_data_healthproxl($empId, $policy_no, $deductable);
                            }
                            
                        }else{
							$data = $this->get_all_member_data_new($empId, $policy_no);

						}
						//$data = $this->get_all_member_data_new($empId, $policy_no);
						$arr[] = 1;
					} 
					else 
					{
						$arr[] = 0;
					}
					}
					
					// print_pre($data);exit;
					
					
					//add logic for disease here common for edit and insert
					
					if($edit != 0){
						$member_id_disease = $edit;
					}else{
						$member_id_disease = $id_members;
					}
					$query = $this->db->query("Delete  from  employee_declare_member_sub_type where policy_member_id ='".$member_id_disease."' ");
					// print_pre($this->db->last_query());
						   foreach ($chronic[$key] as $key1 => $sub_type_checks) 
							{       
								
									$data_mb_diesease = array(
														"declare_sub_type_id" =>$sub_type_checks,
														"policy_detail_id" =>$policy_no,
														"product_id" =>$parent_id,
														"policy_member_id" => $member_id_disease,
														"emp_id"=>$empId,
														"created_date" =>date("Y-m-d H:i:s")

													);

									$this->db->insert("employee_declare_member_sub_type", $data_mb_diesease);
									//print_pre($this->db->last_query());
											//Add Data into logs Table	
									$logs_array['data'] = ["type" => "update_chronic_diesease","req" => json_encode($data_mb_diesease), "lead_id" => $lead_id,"product_id" => $product_id];
									$this->Logs_m->insertLogs($logs_array);
							}
							
							$query_dels = $this->db->query("Delete  from  employee_declare_member_data where policy_member_id ='".$member_id_disease."' ");
							//print_pre($this->db->last_query());
							foreach ($declare_member[$key] as $key2 => $valuedeclare)
							{
								if ($valuedeclare['format'] != '') 
								{
								
									$data_mb = array(
														"p_member_id" =>$valuedeclare['question'],
														"format" =>$valuedeclare['format'],
														"policy_detail_id" =>$policy_no,
														"product_id" =>$parent_id,
														"policy_member_id" => $member_id_disease,
														"emp_id"=>$empId,
														"created_date" =>date("Y-m-d H:i:s")
													);

												$this->db->insert("employee_declare_member_data", $data_mb);
												//print_pre($this->db->last_query());
												//Add Data into logs Table	
												$logs_array['data'] = ["type" => "update_diesease_member_answer","req" => json_encode($data_mb), "lead_id" => $lead_id,"product_id" => $product_id];
												$this->Logs_m->insertLogs($logs_array);
								}
							}
					
				}
				else 
				{
				//member addition other than self
				
					$family_relation_id = $this->db->select("*")->from("family_relation AS fr,employee_family_details AS efd")->where("fr.family_id = efd.family_id")->where('fr.emp_id',$empId)->where('efd.fr_id',$val)->get()->row_array();
				
					$member_exist_check= $this->db->select("policy_member_id")->from("employee_policy_member")->where("family_relation_id", $family_relation_id['family_relation_id'])->where("policy_detail_id", $policy_no)->get()->row_array();
								
					if($edit_member_id[$key]){
							
							//$edit = $member_exist_check['policy_member_id'];
							$edit = $edit_member_id[$key];
							
						}

					if ($edit != 0)
					{
						
						$check = $this->check_validations($val, $policy_no,$empId,$age[$key],$age_type[$key],$familyConstruct,$edit);
						if($check["message"] != "true")
						{
							return ["status" => false, "message" => $check["message"]];
						}
			
						$family_relation_id = $this->db->select("*")->from("employee_policy_member epm")->where('epm.policy_member_id',$edit)->get()->row_array();
			
						$family_array_update_new = $this->db->select("*")->from("family_relation AS fr,employee_family_details AS efd")->where("fr.family_id = efd.family_id")->where('fr.emp_id',$empId)->where('fr.family_relation_id',$family_relation_id['family_relation_id'])->where('fr.emp_id',$empId)->get()->row_array();
					
						if(empty($family_array_update_new)) 
						{
							//add it into family details family relation employee policy member
							 $family_array = [
												"fr_id" => $val,
												"family_dob" => $family_date_birth[$key],
												"family_firstname" => $first_name[$key],
												"family_lastname" => $last_name[$key],
												"family_middlename" => $middle_name,
												"family_status" => 1,
												"family_gender" => $family_gender[$key],
											];
							$this->db->insert("employee_family_details", $family_array);
				
							if ($this->db->affected_rows()) 
							{
								//insert into family relation
								$this->db->insert("family_relation", ["emp_id" => $empId, "family_id" => $this->db->insert_id()]);
								//family relation ends
								//insert into employee policy member
								$family_relation_id = $this->db->insert_id();
								
								//Add Data into logs Table										
								$logs_array['data'] = ["type" => "insert_insured_family_details","req" => json_encode($family_array), "lead_id" => $lead_id,"product_id" => $product_id];
								$this->Logs_m->insertLogs($logs_array);
							} 
							else 
							{
								return ["status" => false, "message" => "something went wrong"];
							}
						}	else{
							
							$family_array = [
											"fr_id" => $val,
											"family_dob" => $family_date_birth[$key],
											"family_firstname" => $first_name[$key],
											"family_middlename" => $middle_name,
											"family_lastname" => $last_name[$key],
											"family_status" => 1,
											"family_gender" => $family_gender[$key],
										];
						$this->db->where('family_id', $family_array_update_new['family_id']);
						$this->db->update('employee_family_details', $family_array);
					
						
						//Add Data into logs Table							
						$logs_array['data'] = ["type" => "update_insured_family_details","req" => json_encode($family_array), "lead_id" => $lead_id,"product_id" => $product_id];
						$this->Logs_m->insertLogs($logs_array);
						}	

						$query_mem_data = $this->db->query("select * from employee_policy_member where policy_member_id = '".$edit."'")->row_array();
                        //$logs_array['data'] = ["type" => "update_dob","req" => json_encode($query_mem_data), "lead_id" => $lead_id,"product_id" => $product_id];
						//$this->Logs_m->insertLogs($logs_array);
                        if($product_id == 'T03'){
                        	$arr = [];
                        	if($val == '2' || $val == '3'){                           
	                            if($query_mem_data['kid_title'] == 'kid1'){
	                                $arr = ['kid1_dob' => $family_date_birth[$key] , 'kid1_rel' => $val];
	                            }else if($query_mem_data['kid_title'] == 'kid2'){
	                                $arr = ['kid2_dob' => $family_date_birth[$key] , 'kid2_rel' => $val];
	                            }
	                        }else if($val == '1'){
	                            $arr = ['spouse_dob' => $family_date_birth[$key]];
	                        }
	                        // print_r($arr);exit;
	                        if(!empty($arr)){
	                            $this->db->where('emp_id', $empId);
	                            $this->db->update('employee_details', $arr);
	                            //$logs_array['data'] = ["type" => "update_dob","req" => json_encode($this->db->last_query()), "lead_id" => $lead_id,"product_id" => $product_id];
								//$this->Logs_m->insertLogs($logs_array);
	                        }
                        }
                        

						$member_array = [
											"policy_detail_id" => $policy_no,
											"family_relation_id" => $family_array_update_new['family_relation_id'],
											"policy_mem_sum_insured" => $sum_insure,
											"policy_mem_sum_premium" => $premium,
											"policy_mem_gender" => $family_gender[$key],
											"policy_mem_salutation" => $family_salutation[$key],
											"policy_mem_dob" => $family_date_birth[$key],
											"age" => $age[$key],
											"age_type" => $age_type[$key],
											"member_status" => "pending",
											"fr_id" => $val,
											"policy_member_first_name" => $first_name[$key],
											"policy_member_middle_name" => $middle_name,
											"policy_member_last_name" => $last_name[$key],
											"familyConstruct" => $familyConstruct,
											"tenure" => $tenure,
											"businessType" => $businessType,
											"policy_member_email_id" => $mem_email_id[$key],
												"policy_member_mob_no" => $mem_mob_no[$key],
											//"remark" => $remark
										];
					
						$this->db->where('policy_member_id', $edit);
						$this->db->update('employee_policy_member', $member_array);
						// print_pre($this->emp_id);
						//Add Data into logs Table							
						$logs_array['data'] = ["type" => "update_insured_member","req" => json_encode($member_array), "lead_id" => $lead_id,"product_id" => $product_id];
						$this->Logs_m->insertLogs($logs_array);
						
						//$data = $this->get_all_member_data_new($empId, $policy_no);
						if($product_id == 'T01')
							{
							//echo "2203 line no";exit;	
							// echo 'insert'.'--'.$policy_no.'--'.$sum_insure.'--'.$familyConstruct.'--'.true.'--'.$gci_option.'--'.$val."--".$empId;//exit;
							$data = $this->get_all_member_data_new_gpa($product_id,'update',$policy_no,$sum_insure,$familyConstruct,true,$gci_option,$val,$empId);
							}else if($product_id == 'R12'){
								$data = $this->get_all_member_data_new_gci($product_id,'update',$policy_no,$sum_insure,$familyConstruct,true,$gci_option,$val,$empId);
							}else if($product_id == 'T03'){
                                if($policy_no != TELE_HEALTHPROINFINITY_GHI_ST){
                                    $data = $this->get_all_member_data_new_gpa_optional('update',$policy_no,$sum_insure,$familyConstruct,true,$gpa_option,$val,$empId);
                                }else{
                                    $data = $this->get_all_member_data_healthproxl($empId, $policy_no, $deductable);
                                }
                                
                            }else{
							$data = $this->get_all_member_data_new($empId, $policy_no);

							}
						$check_member = $this->db->select("epm.*,fr.family_id")
						->from("employee_policy_member as epm,family_relation as fr")
						->where('fr.family_relation_id = epm.family_relation_id')->where("epm.policy_member_id", $edit)
						->get()->row_array();

			
						//if it exists in proposal then update it in proposal also
						$check_member_proposal = $this->db->select("*")
						->from("proposal_member")->where("policy_member_id", $edit)
						->get()->row_array();
						
						if (count($check_member_proposal) > 0) 
						{
							$this->db->where('policy_member_id', $edit);
							$this->db->update('proposal_member', $check_member);
						}

						$array5 = [
									'family_dob' => $family_date_birth[$key],
									'family_firstname' => $first_name[$key],
									'family_middlename' => $middle_name,
									'family_lastname' => $last_name[$key],
									'family_gender' => $family_gender[$key],
								  ];
						$where = [
									'family_id' => $check_member['family_id']
								 ];
						$this->db->UPDATE('employee_family_details', $array5, $where);
					
						//for update in all policy same relation  hdfc
					
						// if(!empty($sub_type_check))
						// {	
							// $query_del_subs = $this->db->query("Delete  from  employee_declare_member_sub_type where policy_member_id ='".$edit."' ");
							// foreach ($sub_type_check as $key1 => $sub_type_checks) 
							// {       
									// $data_mb_diesease = array(
															// "declare_sub_type_id" =>$sub_type_checks,
															// "policy_detail_id" =>$policy_no,
															// "product_id" =>$parent_id,
															// "policy_member_id" => $edit,
															// "emp_id"=>$empId,
															// "created_date" =>date("Y-m-d H:i:s")
													 // );

									// $this->db->insert("employee_declare_member_sub_type", $data_mb_diesease);
									
									//Add Data into logs Table						
									// $logs_array['data'] = ["type" => "update_cronic_diesease","req" => json_encode($data_mb_diesease), "lead_id" => $lead_id,"product_id" => $product_id];
									// $this->Logs_m->insertLogs($logs_array);
							// }			
						// }
						// else
						// {
							// $query_del_sub = $this->db->query("Delete  from  employee_declare_member_sub_type where policy_member_id ='".$edit."' ");
						// }
						
						// $query_del = $this->db->query("Delete  from  employee_declare_member_data where policy_member_id ='".$edit."' ");
						// if(@$declare_member)
						// foreach ($declare_member as $key => $valuedeclare)
						// {
							// if ($valuedeclare['format'] != '') 
							// {
								// if ($valuedeclare['format'] != '') 
								// {
									// $data_mb = array(

										// "p_member_id" =>$valuedeclare['question'],
										// "format" =>$valuedeclare['format'],
										// "policy_detail_id" =>$policy_no,
										// "product_id" =>$parent_id,
										// "policy_member_id" => $edit,
										// "emp_id"=>$empId,
										// "created_date" =>date("Y-m-d H:i:s")

									 // );

									// $this->db->insert("employee_declare_member_data", $data_mb);
									
									//Add Data into logs Table	
									// $logs_array['data'] = ["type" => "insert_diesease_member_answer","req" => json_encode($data_mb), "lead_id" => $lead_id,"product_id" => $product_id];
									// $this->Logs_m->insertLogs($logs_array);	
								// }		
							// }
						// }
						
						//$ghd_check = $this->ghd_declined_insert($myGHD,$empId,$edit,'update',$family_members_id);
						if($ghd_check['status'] == 'false')
						{
							$arr[] = 0;
						}
						$arr[] = 1;
					}
					//else for member
					else{
						
						//CHECK VALIDATIONS
						$check = $this->check_validations($val, $policy_no,$empId,$age[$key],$age_type[$key],$familyConstruct);
						
						// Return Status Message for Validtion Rules
						if($check["message"] != "true"){
							return ["status" => false, "message" => $check["message"]];
						}
					$family_array = [
										"fr_id" => $val,
										"family_dob" => $family_date_birth[$key],
										"family_firstname" => $first_name[$key],
										"family_middlename" => $middle_name,
										"family_lastname" => $last_name[$key],
										"family_status" => 1,
										"family_gender" => $family_gender[$key],
									];
					
					$this->db->insert("employee_family_details", $family_array);
					//print_pre($this->db->last_query());

					if ($this->db->affected_rows()) 
					{
						 //insert into family relation
						 $this->db->insert("family_relation", ["emp_id" => $empId, "family_id" => $this->db->insert_id()]);
						 
						 //family relation ends
						 //insert into employee policy member
						 $family_relation_id = $this->db->insert_id();
						 
						 //Add Data into logs Table	
						 $logs_array['data'] = ["type" => "insert_insured_family_details","req" => json_encode($family_array), "lead_id" => $lead_id,"product_id" => $product_id];
						 $this->Logs_m->insertLogs($logs_array);
						 if($product_id == 'T03'){
						 	$arr = [];
	                        if($val == '2' || $val == '3'){
	                            //echo "innn";exit;
	                            $child_count = $this->get_child_count($empId, $policy_no);
	                            $child_count = $child_count['count'];
	                            //echo $child_count;exit;
	                            if($child_count == 0){
	                                if($get_lead_id['kid1_rel'] == $val){
	                                    $kid_title = 'kid1';
	                                    $arr = ['kid1_dob' => $family_date_birth[$key] , 'kid1_rel' => $val];
	                                }else{
	                                    $kid_title = 'kid2';
	                                    $arr = ['kid2_dob' => $family_date_birth[$key] , 'kid2_rel' => $val];
	                                }
	                                
	                            }else if($child_count == 1){
	                                if($get_lead_id['kid1_rel'] == $get_lead_id['kid2_rel']){
	                                    $kid_title = 'kid2';
	                                    $arr = ['kid2_dob' => $family_date_birth[$key] , 'kid2_rel' => $val];
	                                }else{
	                                    if($get_lead_id['kid1_rel'] == $val){
	                                        $kid_title = 'kid1';
	                                        $arr = ['kid1_dob' => $family_date_birth[$key] , 'kid1_rel' => $val];
	                                    }else{
	                                        $kid_title = 'kid2';
	                                        $arr = ['kid2_dob' => $family_date_birth[$key] , 'kid2_rel' => $val];
	                                    }
	                                }
	                                
	                            }
	                        }else if($val == '1'){
	                            $kid_title = '';
	                            $arr = ['spouse_dob' => $family_date_birth[$key]];
	                        }
	                       // print_pre($arr);exit;
	                        if(!empty($arr)){
	                            $this->db->where('emp_id', $empId);
	                            $this->db->update('employee_details', $arr);
	                        }
						 }
						$member_array = [
											"policy_detail_id" => $policy_no,
											"family_relation_id" => $family_relation_id,
											"policy_mem_sum_insured" => $sum_insure,
											"policy_mem_sum_premium" => $premium, //($premium_logic['suminsured_type'] == 'memberAge') ? $premium  : '',
											"policy_mem_gender" => $family_gender[$key],
											"policy_mem_salutation" => $family_salutation[$key],
											"policy_mem_dob" => $family_date_birth[$key],
											"age" => $age[$key],
											"age_type" => $age_type[$key],
											"member_status" => "pending",
											"fr_id" => $val,
											"policy_member_first_name" => $first_name[$key],
											"policy_member_middle_name" => $middle_name,
											"policy_member_last_name" => $last_name[$key],
											"familyConstruct" => $familyConstruct,
											"tenure" => $tenure,
											"businessType" => $businessType,
											"policy_member_email_id" => $mem_email_id[$key],
											"policy_member_mob_no" => $mem_mob_no[$key],
											"kid_title" => $kid_title,
											//"remark" => $remark,
										];
						
						$this->db->insert("employee_policy_member", $member_array);
						//print_pre($this->db->last_query());
								
						$id_membersi = $this->db->insert_id();
						
						//Add Data into logs Table						
						$logs_array['data'] = ["type" => "insert_insured_member","req" => json_encode($member_array), "lead_id" => $lead_id,"product_id" => $product_id];
						$this->Logs_m->insertLogs($logs_array);
								
						// if(!empty($sub_type_check))
						// {
							// foreach ($sub_type_check as $key1 => $sub_type_checks) 
							// {     
								// $data_mb_diesease = array(
									// "declare_sub_type_id" =>$sub_type_checks,
									// "policy_detail_id" =>$policy_no,
									// "product_id" =>$parent_id,
									// "policy_member_id" => $id_membersi,
									// "emp_id"=>$empId,
									// "created_date" =>date("Y-m-d H:i:s")
								// );

								// $this->db->insert("employee_declare_member_sub_type", $data_mb_diesease);
								
								//Add Data into logs Table	
								// $logs_array['data'] = ["type" => "insert_cronic_diesease","req" => json_encode($data_mb_diesease), "lead_id" => $lead_id,"product_id" => $product_id];
								// $this->Logs_m->insertLogs($logs_array);
							// }
						// }
						// if(@$declare_member)
						// foreach ($declare_member as $key => $value) 
						// {       
							// if($value['format']!='')
							// {
								// $data_mbi = array(
									// "p_member_id" =>$value['question'],
									// "emp_id"=>$empId,
									// "format" =>$value['format'],
									// "policy_detail_id" =>$policy_no,
									// "product_id" =>$parent_id,
									// "policy_member_id" => $id_membersi,
									// "created_date" =>date("Y-m-d H:i:s")

								// );
								// $this->db->insert("employee_declare_member_data", $data_mbi);
								
								//Add Data into logs Table							
								// $logs_array['data'] = ["type" => "insert_diesease_member_answer","req" => json_encode($data_mbi), "lead_id" => $lead_id,"product_id" => $product_id];
								// $this->Logs_m->insertLogs($logs_array);
							// }
						// }

						if($product_id == 'T01')
							{
								
								// echo 'insert'.'--'.$policy_no.'--'.$sum_insure.'--'.$familyConstruct.'--'.true.'--'.$gci_option.'--'.$val."--".$empId;//exit;
								$data = $this->get_all_member_data_new_gpa($product_id,'insert',$policy_no,$sum_insure,$familyConstruct,false,$gci_option,$val,$empId);
							}else if($product_id == 'R12'){
								$data = $this->get_all_member_data_new_gci($product_id,'insert',$policy_no,$sum_insure,$familyConstruct,false,$gci_option,$val,$empId);
							}else if($product_id == 'T03'){
                                //echo "insert---".$family_members_id."<br>";
                                if($policy_no != TELE_HEALTHPROINFINITY_GHI_ST){
                                    //echo "in";exit;
                                    $data = $this->get_all_member_data_new_gpa_optional('insert',$policy_no,$sum_insure,$familyConstruct,false,$gpa_option,$val,$empId);
                                }else{
                                   // echo "out";exit;
                                    $data = $this->get_all_member_data_healthproxl($empId, $policy_no, $deductable);
                                }
                                
                                //$data = $this->get_all_member_data_new_gpa_optional('insert',$policy_no,$sum_insure,$familyConstruct,false,$gpa_option,$family_members_id,$empId);
                            }else{
								$data = $this->get_all_member_data_new($empId, $policy_no);
							}
						
						//$data = $this->get_all_member_data_new($empId, $policy_no);
						$arr[] = 1;
					} 
					else
					{
						$arr[] = 0;
					}
					
				}
				
				//add logic for diseases both insert and update
				if($edit != 0){
						$member_id_disease = $edit;
					}else{
						$member_id_disease = $id_membersi;
					}
					$query = $this->db->query("Delete  from  employee_declare_member_sub_type where policy_member_id ='".$member_id_disease."' ");
					//print_pre($this->db->last_query());
						   foreach ($chronic[$key] as $key1 => $sub_type_checks) 
							{       
						
									$data_mb_diesease = array(
														"declare_sub_type_id" =>$sub_type_checks,
														"policy_detail_id" =>$policy_no,
														"product_id" =>$parent_id,
														"policy_member_id" => $member_id_disease,
														"emp_id"=>$empId,
														"created_date" =>date("Y-m-d H:i:s")

													);

									$this->db->insert("employee_declare_member_sub_type", $data_mb_diesease);
									//print_pre($this->db->last_query());
									//Add Data into logs Table	
									$logs_array['data'] = ["type" => "update_chronic_diesease","req" => json_encode($data_mb_diesease), "lead_id" => $lead_id,"product_id" => $product_id];
									$this->Logs_m->insertLogs($logs_array);
							}
							
							$query_dels = $this->db->query("Delete  from  employee_declare_member_data where policy_member_id ='".$member_id_disease."' ");
					
							foreach ($declare_member[$key] as $key2 => $valuedeclare)
							{
								if ($valuedeclare['format'] != '') 
								{
								
									$data_mb = array(
														"p_member_id" =>$valuedeclare['question'],
														"format" =>$valuedeclare['format'],
														"policy_detail_id" =>$policy_no,
														"product_id" =>$parent_id,
														"policy_member_id" => $member_id_disease,
														"emp_id"=>$empId,
														"created_date" =>date("Y-m-d H:i:s")
													);

												$this->db->insert("employee_declare_member_data", $data_mb);
												//print_pre($this->db->last_query());
												//Add Data into logs Table	
												$logs_array['data'] = ["type" => "update_diesease_member_answer","req" => json_encode($data_mb), "lead_id" => $lead_id,"product_id" => $product_id];
												$this->Logs_m->insertLogs($logs_array);
								}
							}
				}
				
				}
				//echo $edit;
				// echo"one";				
				// print_pre($data);
				// echo"second";
				// exit;
				if($edit == undefined || $edit == ''){
					$msg = "Successfully Added";
				}else{
					$msg = "Successfully Changed";
				}
				//echo $msg;
				//commented healthpro
				$test = ["status" => true, "message" => $msg, "data" => $data];
				if(in_array(0, $arr)){
					$test = ["status" => false, "message" => "something went wrong"];
				}
				return $test;
			}
		}
		// exit;
		/*$test = ["status" => true, "message" => "Sucessfully Changed", "data" => $data];
				if(in_array(0, $arr)){
					$test = ["status" => false, "message" => "something went wrong"];
				}
				return $test;*/
    }

	public function family_details_insert()
    {
		//print_pre($this->input->post());exit;
	
		$this->form_validation->set_error_delimiters('','');
        $this->form_validation->set_rules('sum_insure', 'sum insure', 'required|trim|integer');
		$this->form_validation->set_rules('familyConstruct', 'family Construct', 'required|trim');
		$this->form_validation->set_rules('premium', 'premium', 'required|trim');
		$this->form_validation->set_rules('businessType', 'Business Type', 'required|trim');
		$this->form_validation->set_rules('family_members_id[]', 'Relation With Proposer', 'required|trim');
		$this->form_validation->set_rules('family_gender[]', 'family gender', 'required|trim');
		//$this->form_validation->set_rules('family_salutation[]', 'family salutation', 'required|trim');
		$this->form_validation->set_rules('first_name[]', 'first_name', 'required|trim');
		$this->form_validation->set_rules('last_name[]', 'last name', 'required|trim');
		$this->form_validation->set_rules('family_date_birth[]', 'family date birth', 'required|trim');
		$this->form_validation->set_rules('age[]', 'age', 'required|trim');
		//$this->form_validation->set_rules('age_type[]', 'age type', 'required|trim');
	
		if ($this->form_validation->run() == FALSE)
		{
			 $z = validation_errors();
			 return ["status" => false, "message" => $z ];   
		}
		
		
		$parent_id = $this->parent_id;
		$empId = $this->emp_id;
		$fam_post = $this->input->post(null,true);
		if($fam_post != null || !empty($fam_post))
		$policy_no = $this->input->post('policy_no',true);
		$declare = $this->input->post('declare',true);
		$sub_type_check = $this->input->post('sub_type_check',true);
		$family_members_id = $this->input->post('family_members_id',true);
		$sum_insure = $this->input->post('sum_insure',true);
		$premium = $this->input->post('premium',true);
		$businessType =$this->input->post('businessType',true);
		$family_date_birth = $this->input->post('family_date_birth',true);
		$age = $this->input->post('age',true);
		$age_type = $this->input->post('age_type',true);
		$first_name = $this->input->post('first_name',true);
		$middle_name = $this->input->post('middle_name',true);
		$last_name = $this->input->post('last_name',true);
		$familyConstruct = $this->input->post('familyConstruct',true);
		$tenure = $this->input->post('tenure',true);
		$edit = $this->input->post('edit',true);
		$family_gender = $this->input->post('family_gender',true);
		$subtype_text =$this->input->post('subtype_text',true);
		//$remark = $this->input->post('remark',true);
		$family_salutation = $this->input->post('family_salutation',true);
		$edit_member_id = $this->input->post('edit_member_id',true);
		$chronic = $this->input->post('chronic',true);
		$declare_member = json_decode($declare,true);
		$chronic = json_decode($chronic,true);
		
		
		
		
		
		
		// print_R($_POST);
		// foreach ($family_members_id as $key => $val) 
		// { 
		// echo $first_name[$key];
		// echo $key;
		// }
		// exit;
		$get_lead_id = $this->db->query("select lead_id from employee_details where emp_id = '$empId' ")->row_array();
		if($get_lead_id['lead_id'] != 0)
		{
			$lead_id = $get_lead_id['lead_id'];
			$product_id = 'R06';
		}
		
		//Add Data into logs Table			
		$logs_array = ["type" => "post_insured_member","req" => json_encode($_POST), "lead_id" => $lead_id,"product_id" => $product_id];
		$this->db->insert("logs_post_data",$logs_array);
		
		
		if($premium == 'undefined' || $premium == '')
		{
			return ["status" => false, "message" => "Invalid Premium"];
		}

		$declare_member = json_decode($declare,true);
		$policy_no = explode(",", $policy_no);
      
		foreach ($policy_no as $policy_no) 
		{
			if(!empty($sub_type_check))
			{
				foreach ($sub_type_check as $key1 => $sub_type_checks) 
				{       
					if($edit == 0)
					{
						$query=$this->db->query("select count(declare_sub_type_id) as total from employee_declare_member_sub_type where emp_id='$empId' AND product_id='$parent_id' AND policy_detail_id='$policy_no' ")->result_array();
							
						if($query[0]['total']>0)
						{
							//return ["status" => false, "message" => "As Per The Product Only One Chronic Member Is Allowed In A Policy" ,"check" => "declaration"];
						}
					}
					else
					{
						$query=$this->db->query("select count(declare_sub_type_id) as total from employee_declare_member_sub_type where emp_id='$empId' AND product_id='$parent_id' AND policy_detail_id='$policy_no' AND policy_member_id !='$edit'")->result_array();
							
						if($query[0]['total']>0)
						{
							//return ["status" => false, "message" => "As Per The Product Only One Chronic Member Is Allowed In A Policy" ,"check" => "declaration"];
						}
					}
				}
			}
			if(@$declare_member)
			foreach ($declare_member as $key => $valuei) 
			{       
				if($valuei['format']=='Yes')
				{
					//return ["status" => false, "message" => "Basis information provided this proposal cannot be processed online" ,"check" => "declaration"];
				}
			}

            if (count($policy_no) > 1) 
			{
                $get_policy_type = $this->db->select("*")->from("employee_policy_detail")->where("policy_detail_id", $policy_no)->get()->row_array();
                if ($get_policy_type['policy_sub_type_id'] == 1) 
				{
                    $go = true;
                }
            } 
			else
			{
                $go = true;
            }

			//check min max age
		    $premium_logic =  $this->db->select("*")->from("policy_age_limit")->where("policy_detail_id", $policy_no)->get()->row_array();
		   
			$premium_get = $this->db->select("suminsured_type")->from("employee_policy_detail")->where("policy_detail_id", $policy_no)->get()->row_array();
			
			$premium_type_tbl = $premium_get['suminsured_type'];
			
			if($premium_type_tbl == 'memberAge')
			{
			}
			elseif($premium_type_tbl == "family_construct")
			{
				$get_cal = $this->db->query("select PremiumServiceTax from family_construct_wise_si where policy_detail_id = '$policy_no' AND family_type = '$familyConstruct' AND sum_insured = '$sum_insure'")->row_array();
			}
			
			if ($go) 
			{
				foreach ($family_members_id as $key => $val) 
				{ 
				
				
					//$edit = 0;
					if ($val == 0) 
					{
						
						// $family_relation_id = $this->db->select("family_relation_id")->from("family_relation")->where("emp_id", $empId)->where("family_id", 0)->get()->row_array();
						
						// $member_exist_check= $this->db->select("policy_member_id")->from("employee_policy_member")->where("family_relation_id", $family_relation_id['family_relation_id'])->where("policy_detail_id", $policy_no)->get()->row_array();
						//print_pre($this->db->last_query());exit;
						if($edit_member_id[$key]){
							
							//$edit = $member_exist_check['policy_member_id'];
							$edit = $edit_member_id[$key];
							
						}
								
					}else{
						
						// $family_relation_id = $this->db->select("*")->from("family_relation AS fr,employee_family_details AS efd")->where("fr.family_id = efd.family_id")->where('fr.emp_id',$empId)->where('efd.fr_id',$val)->get()->row_array();
						
						// $member_exist_check= $this->db->select("policy_member_id")->from("employee_policy_member")->where("family_relation_id", $family_relation_id['family_relation_id'])->where("policy_detail_id", $policy_no)->get()->row_array();
						if($edit_member_id[$key]){
							
							//$edit = $member_exist_check['policy_member_id'];
							$edit = $edit_member_id[$key];
							
						}
						
					}
					//CHECK VALIDATIONS
					$check = $this->check_validations($val,$policy_no,$empId,$age[$key],$age_type[$key],$familyConstruct,$edit);

					if($check["message"] != "true")
					{
						return ["status" => false, "message" => $check["message"]];
					}
					
				}
				
				foreach ($family_members_id as $key => $val) 
				{ 
				$edit = '';
				if ($val == 0) 
				{
					
					//disease code 
					
							$family_relation_id = $this->db->select("family_relation_id")->from("family_relation")->where("emp_id", $empId)->where("family_id", 0)->get()->row_array();
							
							$member_exist_check= $this->db->select("policy_member_id")->from("employee_policy_member")->where("family_relation_id", $family_relation_id['family_relation_id'])->where("policy_detail_id", $policy_no)->get()->row_array();
							
							if($edit_member_id[$key]){
							
							//$edit = $member_exist_check['policy_member_id'];
							$edit = $edit_member_id[$key];
							
						}

							$member_array = [
												"policy_detail_id" => $policy_no,
												"family_relation_id" => $family_relation_id['family_relation_id'],
												"policy_mem_sum_insured" => $sum_insure,
												"policy_mem_sum_premium" => $premium,
												"policy_mem_gender" => $family_gender[$key],
												"policy_mem_salutation" => $family_salutation[$key],
												"policy_mem_dob" => $family_date_birth[$key],
												"age" => $age[$key],
												"age_type" => $age_type[$key],
												"member_status" => "pending",
												"fr_id" => $val,
												"policy_member_first_name" => $first_name[$key],
												"policy_member_middle_name" => $middle_name,
												"policy_member_last_name" => $last_name[$key],
												"familyConstruct" => $familyConstruct,
												"tenure" => $tenure,
												"businessType" => $businessType,
												//"remark" => $remark
											];
					if ($edit != 0)
					{
						$check = $this->check_validations($val, $policy_no,$empId,$age[$key],$age_type[$key],$familyConstruct,$edit);
				
						if($check["message"] != "true")
						{

							return ["status" => false, "message" => $check["message"]];
						}

						$employee_array = [
												"gender" => $family_gender[$key],
												"bdate" => $family_date_birth[$key],
												"emp_firstname" => $first_name[$key],
												"emp_lastname" => $last_name[$key]
										  ];
						$this->db->where('emp_id', $empId);
						$this->db->update('employee_details', $employee_array);
						

						//update check
						//first check in employee policy member as it wont be present in proposal member in some situations

						$this->db->where('policy_member_id', $edit);
						$this->db->update('employee_policy_member', $member_array);
						
						
						//Add Data into logs Table	
						$logs_array['data'] = ["type" => "update_insured_member","req" => json_encode($member_array), "lead_id" => $lead_id,"product_id" => $product_id];
						$this->Logs_m->insertLogs($logs_array);
						
						$data = $this->get_all_member_data_new($empId, $policy_no);
						$check_member = $this->db->select("*")
						->from("employee_policy_member")->where("policy_member_id", $edit)->get()->row_array();

						//if it exists in proposal then update it in proposal also
						$check_member_proposal = $this->db->select("*")
												->from("proposal_member")->where("policy_member_id", $edit)->get()->row_array();
												
						if (count($check_member_proposal) > 0)
						{
							$this->db->where('policy_member_id', $edit);
							$this->db->update('proposal_member', $check_member);
						}
						
					   // if(!empty($sub_type_check))
						// {
						   // $query = $this->db->query("Delete  from  employee_declare_member_sub_type where policy_member_id ='".$edit."' ");
						   // foreach ($sub_type_check as $key1 => $sub_type_checks) 
							// {       
						
									// $data_mb_diesease = array(
														// "declare_sub_type_id" =>$sub_type_checks,
														// "policy_detail_id" =>$policy_no,
														// "product_id" =>$parent_id,
														// "policy_member_id" => $edit,
														// "emp_id"=>$empId,
														// "created_date" =>date("Y-m-d H:i:s")

													// );

									// $this->db->insert("employee_declare_member_sub_type", $data_mb_diesease);
									
									//Add Data into logs Table	
									// $logs_array['data'] = ["type" => "update_chronic_diesease","req" => json_encode($data_mb_diesease), "lead_id" => $lead_id,"product_id" => $product_id];
									// $this->Logs_m->insertLogs($logs_array);
							// }
						// }
					   // else
					   // {
							// $query = $this->db->query("Delete  from  employee_declare_member_sub_type where policy_member_id ='".$edit."' ");
					   // }
					
						   // $query_dels = $this->db->query("Delete  from  employee_declare_member_data where policy_member_id ='".$edit."' ");
					
							// foreach ($declare_member as $key => $valuedeclare)
							// {
								// if ($valuedeclare['format'] != '') 
								// {
								
									// $data_mb = array(
														// "p_member_id" =>$valuedeclare['question'],
														// "format" =>$valuedeclare['format'],
														// "policy_detail_id" =>$policy_no,
														// "product_id" =>$parent_id,
														// "policy_member_id" => $edit,
														// "emp_id"=>$empId,
														// "created_date" =>date("Y-m-d H:i:s")
													// );

												// $this->db->insert("employee_declare_member_data", $data_mb);
												
												//Add Data into logs Table	
												// $logs_array['data'] = ["type" => "update_diesease_member_answer","req" => json_encode($data_mb), "lead_id" => $lead_id,"product_id" => $product_id];
												// $this->Logs_m->insertLogs($logs_array);
								// }
							// }
							
							//$ghd_check = $this->ghd_declined_insert($myGHD,$empId,$edit,'update',$family_members_id);
							if($ghd_check['status'] == 'false')
							{
								$arr[] = 0;
							}
							
							$arr[] = 1;
					}

					//will have to write if edit or insert
					//$ghd_check = $this->ghd_declined_insert($myGHD,$empId,$id_members,'insert',$family_members_id);
					/*if($ghd_check['status'] == 'false')
					{
						return ["status" => false, "message" => $ghd_check['message'] ];
					}*/
					else{
						
					
					$this->db->insert("employee_policy_member", $member_array);
					//print_pre($this->db->last_query());
					$id_members = $this->db->insert_id();
					
					//Add Data into logs Table	
					$logs_array['data'] = ["type" => "insert_insured_member","req" => json_encode($member_array), "lead_id" => $lead_id,"product_id" => $product_id];
					$this->Logs_m->insertLogs($logs_array);
					
					// if(!empty($sub_type_check))
					// {
						// foreach ($sub_type_check as $key1 => $sub_type_checks) 
						// {       
								// $data_mb_diesease = array(
									// "declare_sub_type_id" =>$sub_type_checks,
									// "policy_detail_id" =>$policy_no,
									// "product_id" =>$parent_id,
									// "policy_member_id" => $id_members,
									// "emp_id"=>$empId,
									// "created_date" =>date("Y-m-d H:i:s")
								// );

								// $this->db->insert("employee_declare_member_sub_type", $data_mb_diesease);
								
								//Add Data into logs Table	
								// $logs_array['data'] = ["type" => "insert_chronic_diesease","req" => json_encode($data_mb_diesease), "lead_id" => $lead_id,"product_id" => $product_id];
								// $this->Logs_m->insertLogs($logs_array);
						// }
					// }
					
					// if(@$declare_member)
					// foreach ($declare_member as $key => $value) 
					// {       
						// if($value['format']!='')
						// {
							// $data_mb = array(
								// "p_member_id" =>$value['question'],
								// "format" =>$value['format'],
								// "policy_detail_id" =>$policy_no,
								// "product_id" =>$parent_id,
								// "policy_member_id" => $id_members,
								// "emp_id"=>$empId,
								// "created_date" =>date("Y-m-d H:i:s")
							// );

							// $this->db->insert("employee_declare_member_data", $data_mb);
							
//							Add Data into logs Table	
							// $logs_array['data'] = ["type" => "insert_diesease_member_answer","req" => json_encode($data_mb), "lead_id" => $lead_id,"product_id" => $product_id];
							// $this->Logs_m->insertLogs($logs_array);
						// }
					// }
					
					
					
					if ($this->db->affected_rows()) 
					{
						$data = $this->get_all_member_data_new($empId, $policy_no);
						$arr[] = 1;
					} 
					else 
					{
						$arr[] = 0;
					}
					}
					
					
					
					//add logic for disease here common for edit and insert
					if($edit != 0){
						$member_id_disease = $edit;
					}else{
						$member_id_disease = $id_members;
					}
$query = $this->db->query("Delete  from  employee_declare_member_sub_type where policy_member_id ='".$member_id_disease."' ");
//print_pre($this->db->last_query());
						   foreach ($chronic[$key] as $key1 => $sub_type_checks) 
							{       
								
									$data_mb_diesease = array(
														"declare_sub_type_id" =>$sub_type_checks,
														"policy_detail_id" =>$policy_no,
														"product_id" =>$parent_id,
														"policy_member_id" => $member_id_disease,
														"emp_id"=>$empId,
														"created_date" =>date("Y-m-d H:i:s")

													);

									$this->db->insert("employee_declare_member_sub_type", $data_mb_diesease);
									//print_pre($this->db->last_query());
											//Add Data into logs Table	
									$logs_array['data'] = ["type" => "update_chronic_diesease","req" => json_encode($data_mb_diesease), "lead_id" => $lead_id,"product_id" => $product_id];
									$this->Logs_m->insertLogs($logs_array);
							}
							
$query_dels = $this->db->query("Delete  from  employee_declare_member_data where policy_member_id ='".$member_id_disease."' ");
					//print_pre($this->db->last_query());
							foreach ($declare_member[$key] as $key2 => $valuedeclare)
							{
								if ($valuedeclare['format'] != '') 
								{
								
									$data_mb = array(
														"p_member_id" =>$valuedeclare['question'],
														"format" =>$valuedeclare['format'],
														"policy_detail_id" =>$policy_no,
														"product_id" =>$parent_id,
														"policy_member_id" => $member_id_disease,
														"emp_id"=>$empId,
														"created_date" =>date("Y-m-d H:i:s")
													);

												$this->db->insert("employee_declare_member_data", $data_mb);
												//print_pre($this->db->last_query());
												//Add Data into logs Table	
												$logs_array['data'] = ["type" => "update_diesease_member_answer","req" => json_encode($data_mb), "lead_id" => $lead_id,"product_id" => $product_id];
												$this->Logs_m->insertLogs($logs_array);
								}
							}
					
				}
				else 
				{
				//member addition other than self
				
					$family_relation_id = $this->db->select("*")->from("family_relation AS fr,employee_family_details AS efd")->where("fr.family_id = efd.family_id")->where('fr.emp_id',$empId)->where('efd.fr_id',$val)->get()->row_array();
				
					$member_exist_check= $this->db->select("policy_member_id")->from("employee_policy_member")->where("family_relation_id", $family_relation_id['family_relation_id'])->where("policy_detail_id", $policy_no)->get()->row_array();
								
					if($edit_member_id[$key]){
							
							//$edit = $member_exist_check['policy_member_id'];
							$edit = $edit_member_id[$key];
							
						}

					if ($edit != 0)
					{
						$check = $this->check_validations($val, $policy_no,$empId,$age[$key],$age_type[$key],$familyConstruct,$edit);
						if($check["message"] != "true")
						{
							return ["status" => false, "message" => $check["message"]];
						}
			
						$family_relation_id = $this->db->select("*")->from("employee_policy_member epm")->where('epm.policy_member_id',$edit)->get()->row_array();
			
						$family_array_update_new = $this->db->select("*")->from("family_relation AS fr,employee_family_details AS efd")->where("fr.family_id = efd.family_id")->where('fr.emp_id',$empId)->where('fr.family_relation_id',$family_relation_id['family_relation_id'])->where('fr.emp_id',$empId)->get()->row_array();
					
						if(empty($family_array_update_new)) 
						{
							//add it into family details family relation employee policy member
							 $family_array = [
												"fr_id" => $val,
												"family_dob" => $family_date_birth[$key],
												"family_firstname" => $first_name[$key],
												"family_lastname" => $last_name[$key],
												"family_middlename" => $middle_name,
												"family_status" => 1,
												"family_gender" => $family_gender[$key],
											];
							$this->db->insert("employee_family_details", $family_array);
				
							if ($this->db->affected_rows()) 
							{
								//insert into family relation
								$this->db->insert("family_relation", ["emp_id" => $empId, "family_id" => $this->db->insert_id()]);
								//family relation ends
								//insert into employee policy member
								$family_relation_id = $this->db->insert_id();
								
								//Add Data into logs Table										
								$logs_array['data'] = ["type" => "insert_insured_family_details","req" => json_encode($family_array), "lead_id" => $lead_id,"product_id" => $product_id];
								$this->Logs_m->insertLogs($logs_array);
							} 
							else 
							{
								return ["status" => false, "message" => "something went wrong"];
							}
						}	else{
							
							$family_array = [
											"fr_id" => $val,
											"family_dob" => $family_date_birth[$key],
											"family_firstname" => $first_name[$key],
											"family_middlename" => $middle_name,
											"family_lastname" => $last_name[$key],
											"family_status" => 1,
											"family_gender" => $family_gender[$key],
										];
						$this->db->where('family_id', $family_array_update_new['family_id']);
						$this->db->update('employee_family_details', $family_array);
					
						
						//Add Data into logs Table							
						$logs_array['data'] = ["type" => "update_insured_family_details","req" => json_encode($family_array), "lead_id" => $lead_id,"product_id" => $product_id];
						$this->Logs_m->insertLogs($logs_array);
						}	
				
						$member_array = [
											"policy_detail_id" => $policy_no,
											"family_relation_id" => $family_array_update_new['family_relation_id'],
											"policy_mem_sum_insured" => $sum_insure,
											"policy_mem_sum_premium" => $premium,
											"policy_mem_gender" => $family_gender[$key],
											"policy_mem_salutation" => $family_salutation[$key],
											"policy_mem_dob" => $family_date_birth[$key],
											"age" => $age[$key],
											"age_type" => $age_type[$key],
											"member_status" => "pending",
											"fr_id" => $val,
											"policy_member_first_name" => $first_name[$key],
											"policy_member_middle_name" => $middle_name,
											"policy_member_last_name" => $last_name[$key],
											"familyConstruct" => $familyConstruct,
											"tenure" => $tenure,
											"businessType" => $businessType,
											//"remark" => $remark
										];
					
						$this->db->where('policy_member_id', $edit);
							$this->db->update('employee_policy_member', $member_array);
							
						//Add Data into logs Table							
						$logs_array['data'] = ["type" => "update_insured_member","req" => json_encode($member_array), "lead_id" => $lead_id,"product_id" => $product_id];
						$this->Logs_m->insertLogs($logs_array);
						
						$data = $this->get_all_member_data_new($empId, $policy_no);

						$check_member = $this->db->select("epm.*,fr.family_id")
						->from("employee_policy_member as epm,family_relation as fr")
						->where('fr.family_relation_id = epm.family_relation_id')->where("epm.policy_member_id", $edit)
						->get()->row_array();

			
						//if it exists in proposal then update it in proposal also
						$check_member_proposal = $this->db->select("*")
						->from("proposal_member")->where("policy_member_id", $edit)
						->get()->row_array();
						
						if (count($check_member_proposal) > 0) 
						{
							$this->db->where('policy_member_id', $edit);
							$this->db->update('proposal_member', $check_member);
						}

						$array5 = [
									'family_dob' => $family_date_birth[$key],
									'family_firstname' => $first_name[$key],
									'family_middlename' => $middle_name,
									'family_lastname' => $last_name[$key],
									'family_gender' => $family_gender[$key],
								  ];
						$where = [
									'family_id' => $check_member['family_id']
								 ];
						$this->db->UPDATE('employee_family_details', $array5, $where);
					
						//for update in all policy same relation  hdfc
					
						// if(!empty($sub_type_check))
						// {	
							// $query_del_subs = $this->db->query("Delete  from  employee_declare_member_sub_type where policy_member_id ='".$edit."' ");
							// foreach ($sub_type_check as $key1 => $sub_type_checks) 
							// {       
									// $data_mb_diesease = array(
															// "declare_sub_type_id" =>$sub_type_checks,
															// "policy_detail_id" =>$policy_no,
															// "product_id" =>$parent_id,
															// "policy_member_id" => $edit,
															// "emp_id"=>$empId,
															// "created_date" =>date("Y-m-d H:i:s")
													 // );

									// $this->db->insert("employee_declare_member_sub_type", $data_mb_diesease);
									
									//Add Data into logs Table						
									// $logs_array['data'] = ["type" => "update_cronic_diesease","req" => json_encode($data_mb_diesease), "lead_id" => $lead_id,"product_id" => $product_id];
									// $this->Logs_m->insertLogs($logs_array);
							// }			
						// }
						// else
						// {
							// $query_del_sub = $this->db->query("Delete  from  employee_declare_member_sub_type where policy_member_id ='".$edit."' ");
						// }
						
						// $query_del = $this->db->query("Delete  from  employee_declare_member_data where policy_member_id ='".$edit."' ");
						// if(@$declare_member)
						// foreach ($declare_member as $key => $valuedeclare)
						// {
							// if ($valuedeclare['format'] != '') 
							// {
								// if ($valuedeclare['format'] != '') 
								// {
									// $data_mb = array(

										// "p_member_id" =>$valuedeclare['question'],
										// "format" =>$valuedeclare['format'],
										// "policy_detail_id" =>$policy_no,
										// "product_id" =>$parent_id,
										// "policy_member_id" => $edit,
										// "emp_id"=>$empId,
										// "created_date" =>date("Y-m-d H:i:s")

									 // );

									// $this->db->insert("employee_declare_member_data", $data_mb);
									
									//Add Data into logs Table	
									// $logs_array['data'] = ["type" => "insert_diesease_member_answer","req" => json_encode($data_mb), "lead_id" => $lead_id,"product_id" => $product_id];
									// $this->Logs_m->insertLogs($logs_array);	
								// }		
							// }
						// }
						
						//$ghd_check = $this->ghd_declined_insert($myGHD,$empId,$edit,'update',$family_members_id);
						if($ghd_check['status'] == 'false')
						{
							$arr[] = 0;
						}
						$arr[] = 1;
					}
					//else for member
					else{
					$family_array = [
										"fr_id" => $val,
										"family_dob" => $family_date_birth[$key],
										"family_firstname" => $first_name[$key],
										"family_middlename" => $middle_name,
										"family_lastname" => $last_name[$key],
										"family_status" => 1,
										"family_gender" => $family_gender[$key],
									];
					
					$this->db->insert("employee_family_details", $family_array);
					//print_pre($this->db->last_query());

					if ($this->db->affected_rows()) 
					{
						 //insert into family relation
						 $this->db->insert("family_relation", ["emp_id" => $empId, "family_id" => $this->db->insert_id()]);
						 
						 //family relation ends
						 //insert into employee policy member
						 $family_relation_id = $this->db->insert_id();
						 
						 //Add Data into logs Table	
						 $logs_array['data'] = ["type" => "insert_insured_family_details","req" => json_encode($family_array), "lead_id" => $lead_id,"product_id" => $product_id];
						 $this->Logs_m->insertLogs($logs_array);

						$member_array = [
											"policy_detail_id" => $policy_no,
											"family_relation_id" => $family_relation_id,
											"policy_mem_sum_insured" => $sum_insure,
											"policy_mem_sum_premium" => $premium, //($premium_logic['suminsured_type'] == 'memberAge') ? $premium  : '',
											"policy_mem_gender" => $family_gender[$key],
											"policy_mem_salutation" => $family_salutation[$key],
											"policy_mem_dob" => $family_date_birth[$key],
											"age" => $age[$key],
											"age_type" => $age_type[$key],
											"member_status" => "pending",
											"fr_id" => $val,
											"policy_member_first_name" => $first_name[$key],
											"policy_member_middle_name" => $middle_name,
											"policy_member_last_name" => $last_name[$key],
											"familyConstruct" => $familyConstruct,
											"tenure" => $tenure,
											"businessType" => $businessType,
											//"remark" => $remark,
										];
						
						$this->db->insert("employee_policy_member", $member_array);
						//print_pre($this->db->last_query());
								
						$id_membersi = $this->db->insert_id();
						
						//Add Data into logs Table						
						$logs_array['data'] = ["type" => "insert_insured_member","req" => json_encode($member_array), "lead_id" => $lead_id,"product_id" => $product_id];
						$this->Logs_m->insertLogs($logs_array);
								
						// if(!empty($sub_type_check))
						// {
							// foreach ($sub_type_check as $key1 => $sub_type_checks) 
							// {     
								// $data_mb_diesease = array(
									// "declare_sub_type_id" =>$sub_type_checks,
									// "policy_detail_id" =>$policy_no,
									// "product_id" =>$parent_id,
									// "policy_member_id" => $id_membersi,
									// "emp_id"=>$empId,
									// "created_date" =>date("Y-m-d H:i:s")
								// );

								// $this->db->insert("employee_declare_member_sub_type", $data_mb_diesease);
								
								//Add Data into logs Table	
								// $logs_array['data'] = ["type" => "insert_cronic_diesease","req" => json_encode($data_mb_diesease), "lead_id" => $lead_id,"product_id" => $product_id];
								// $this->Logs_m->insertLogs($logs_array);
							// }
						// }
						// if(@$declare_member)
						// foreach ($declare_member as $key => $value) 
						// {       
							// if($value['format']!='')
							// {
								// $data_mbi = array(
									// "p_member_id" =>$value['question'],
									// "emp_id"=>$empId,
									// "format" =>$value['format'],
									// "policy_detail_id" =>$policy_no,
									// "product_id" =>$parent_id,
									// "policy_member_id" => $id_membersi,
									// "created_date" =>date("Y-m-d H:i:s")

								// );
								// $this->db->insert("employee_declare_member_data", $data_mbi);
								
								//Add Data into logs Table							
								// $logs_array['data'] = ["type" => "insert_diesease_member_answer","req" => json_encode($data_mbi), "lead_id" => $lead_id,"product_id" => $product_id];
								// $this->Logs_m->insertLogs($logs_array);
							// }
						// }
						
						$data = $this->get_all_member_data_new($empId, $policy_no);
						$arr[] = 1;
					} 
					else
					{
						$arr[] = 0;
					}
					
				}
				
				//add logic for diseases both insert and update
				if($edit != 0){
						$member_id_disease = $edit;
					}else{
						$member_id_disease = $id_membersi;
					}
$query = $this->db->query("Delete  from  employee_declare_member_sub_type where policy_member_id ='".$member_id_disease."' ");
//print_pre($this->db->last_query());
						   foreach ($chronic[$key] as $key1 => $sub_type_checks) 
							{       
						
									$data_mb_diesease = array(
														"declare_sub_type_id" =>$sub_type_checks,
														"policy_detail_id" =>$policy_no,
														"product_id" =>$parent_id,
														"policy_member_id" => $member_id_disease,
														"emp_id"=>$empId,
														"created_date" =>date("Y-m-d H:i:s")

													);

									$this->db->insert("employee_declare_member_sub_type", $data_mb_diesease);
									//print_pre($this->db->last_query());
									//Add Data into logs Table	
									$logs_array['data'] = ["type" => "update_chronic_diesease","req" => json_encode($data_mb_diesease), "lead_id" => $lead_id,"product_id" => $product_id];
									$this->Logs_m->insertLogs($logs_array);
							}
							
$query_dels = $this->db->query("Delete  from  employee_declare_member_data where policy_member_id ='".$member_id_disease."' ");
					
							foreach ($declare_member[$key] as $key2 => $valuedeclare)
							{
								if ($valuedeclare['format'] != '') 
								{
								
									$data_mb = array(
														"p_member_id" =>$valuedeclare['question'],
														"format" =>$valuedeclare['format'],
														"policy_detail_id" =>$policy_no,
														"product_id" =>$parent_id,
														"policy_member_id" => $member_id_disease,
														"emp_id"=>$empId,
														"created_date" =>date("Y-m-d H:i:s")
													);

												$this->db->insert("employee_declare_member_data", $data_mb);
												//print_pre($this->db->last_query());
												//Add Data into logs Table	
												$logs_array['data'] = ["type" => "update_diesease_member_answer","req" => json_encode($data_mb), "lead_id" => $lead_id,"product_id" => $product_id];
												$this->Logs_m->insertLogs($logs_array);
								}
							}
				}
				
				}
				
				$test = ["status" => true, "message" => "Successfully Changed", "data" => $data];
				if(in_array(0, $arr)){
					$test = ["status" => false, "message" => "something went wrong"];
				}
				return $test;
			}
		}
    }
	/*function ghd_declined_insert($myGHD,$emp_id,$policy_member_id,$log_insert,$family_members_id)
	{
		
				if(@$myGHD)
				    $query_ghd = $this->db->query("Delete  from  tls_ghd_employee_declare where emp_id ='".$emp_id."' AND family_relation_id ='".$family_members_id."'");
					foreach ($myGHD as $key => $row) 
					{
						
					
			 
						$split_format = explode('_',$row['format']);
						if(!empty($row['format']))
						{
							$GHD_data = [
								   
									"format" => $split_format[1],
									"remark" => $row['remark'],
									"type" => $key,
									"emp_id" => $emp_id,
									"policy_member_id" => $policy_member_id,
									"family_relation_id" => $family_members_id

									];
							$this->db->insert("tls_ghd_employee_declare", $GHD_data);
							
							$type = $log_insert."_member_ghd_declare";
				//logs							
							$logs_array = ["type" => $log_insert,"req" => json_encode($GHD_data), "lead_id" => $lead_id,"product_type" => $product_type];
							$this->db->insert("logs_post_data",$logs_array);
							
						if($row['format']== 'B_Yes')
						{
							
							$msg = "Enrollment would be declined, Pls check the Product guidelines";
							
							return ["status" => "false", "message" => $msg ];
						}
						elseif($row['format']== 'C1_Yes' || $row['format']== 'C2_Yes')
						{
							$msg = "Enrollment would be declined, Pls check the Product guidelines";
							return ["status" => "false", "message" => $msg ];
						}
						}
						
						
						
					}
					
			
	}*/
	function check_validations($relation_id, $policy_detail_id, $empId, $age,$age_type,$familyConstruct,$edit = 0) 
	{
 	
        if ($relation_id == 0) 
		{
            $family_relation_id = $this->db->select("family_relation_id")->from("family_relation")->where("emp_id", $empId)->where("family_id", $relation_id)->get()->row_array();
        } 
		else 
		{
            $family_relation_id = $this->db->query('SELECT *
               FROM 
               employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
               master_family_relation AS mfr
               WHERE epd.policy_detail_id = epm.policy_detail_id
               AND epm.family_relation_id = fr.family_relation_id

               AND fr.family_id = efd.family_id 
               AND efd.fr_id = mfr.fr_id
               AND fr.emp_id = ' . $empId . '
               and mfr.fr_id = ' . $relation_id . '
               AND `epd`.`policy_detail_id` = ' . $policy_detail_id)->row_array();
        }
        
        if ($family_relation_id)
		{
            $check = $this->db->select("*")->from("employee_policy_member")->where("family_relation_id", $family_relation_id["family_relation_id"])->where("policy_detail_id", $policy_detail_id)->get()->row_array();
            if ($relation_id != '2' && $relation_id != '3' && $edit == 0) 
			{
                if (count($check) > 0) 
				{
				//check if self is already present
                    return ["message" => "Member Already Exists"];
                }
            }
		
		}
				//now check max adult count
        $check_min_max = $this->db->select("*")->from("policy_age_limit,master_family_relation")->where("policy_detail_id", $policy_detail_id)->where("policy_age_limit.relation_id", $relation_id)
        ->where("master_family_relation.fr_id = policy_age_limit.relation_id")
        ->get()->row_array();

        if ($check_min_max['max_age'] != 0) 
		{	
			if($age_type == 'days' && ($relation_id == 2 || $relation_id == 3))
			{
				if($age < 91)
				{
					return ["message" => $check_min_max['fr_name']." age should be greater than 91 days"];			
			
				}
				else
				{

					$age = 1;
				}	
			}
			if($age_type == 'days' && ($relation_id != 2 &&  $relation_id != 3))
			{
			  return ["message" => "Min age for ".$check_min_max['fr_name']. " is ".$check_min_max['min_age']." and max age
				is ".$check_min_max['max_age'] ];
			}		
	
				//check age between max  and min
			if($age_type)
			{
				if (!($age >= $check_min_max['min_age'] && $age <= $check_min_max['max_age']))
				{
					if($check_min_max['min_age'] == 0){
						$check_min_max['min_age'] = "91 days";
					}

					return ["message" => "Min age for ".$check_min_max['fr_name']. " is ".$check_min_max['min_age']." and max age is ".$check_min_max['max_age']. " years" ];

                }
            }
        }
		
				//now check max adult count
		if($edit == 0)
		{
			if ($relation_id != '2' && $relation_id != '3')
			{
				$max_adult = $this->db->select("*")->from("master_broker_ic_relationship")->where("policy_id", $policy_detail_id)->get()->row_array();
				$count = ($this->get_adult_count($empId, $policy_detail_id));
				$count = $count['count'];
          
         
				if ($count >= $max_adult['max_adult']) 
				{

						return ["message" => "Adult count exceeded"];
				}
			
            
			} 
			else 
			{
		
					   $max_child = $this->db->select("*")->from("master_broker_ic_relationship")->where("policy_id", $policy_detail_id)->get()->row_array();
					   $count = ($this->get_child_count($empId, $policy_detail_id));
			
                       $count = $count['count'];
				
						if ($count >= $max_child['max_child'])
						{

							return ["message" => "Child count exceeded"];
						}
		    }
			
			
           
         
        }
		
		$check1 = $this->check_validations_adult_count_add_member($familyConstruct,$empId,$policy_detail_id,$relation_id,$edit);
		if(!$check1)
		{
			
			 return ["message" => "Cannot add member as per selection"];
		}
		
        return ["message" => "true"];
    }
	
	function get_adult_count_new($emp_id, $policy_detail_id,$ids = 0) 
	{
   
        $response = $this->db->query('SELECT epm.policy_mem_dob,epm.policy_mem_gender,epm.family_relation_id,epm.policy_mem_sum_insured,epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
            epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type"
            FROM 
            employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_details AS ed
            WHERE epd.policy_detail_id = epm.policy_detail_id
            AND epm.family_relation_id = fr.family_relation_id
            AND fr.family_id = 0
            AND fr.emp_id = ed.emp_id
            AND ed.emp_id = ' . $emp_id . '
            AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' UNION all SELECT epm.policy_mem_dob,epm.policy_mem_gender,epm.family_relation_id,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
            epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type"
            FROM 
            employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
            master_family_relation AS mfr
            WHERE epd.policy_detail_id = epm.policy_detail_id
            AND epm.family_relation_id = fr.family_relation_id

            AND fr.family_id = efd.family_id 
            AND efd.fr_id = mfr.fr_id
            and mfr.fr_id  IN ('.$ids.')
            AND fr.emp_id = ' . $emp_id . '
            AND `epd`.`policy_detail_id` = ' . $policy_detail_id)->result_array();

        
			return ($response);
    }
	
	function get_child_count($emp_id, $policy_detail_id) 
	{

        $response = $this->db->query('SELECT epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
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
	function get_adult_count($emp_id, $policy_detail_id)
	{

        $response = $this->db->query('SELECT epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
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
/*healthpro changes created new function old function renamed to get_all_member_data_new_bk()*/
    function get_all_member_data_new($emp_id, $policy_detail_id,$deductable = 0)
    {
		
$response = $this->db
        ->query('SELECT epm.policy_member_mob_no,epm.policy_member_email_id,epm.fr_id,epm.policy_detail_id,epd.policy_sub_type_id,epm.family_relation_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
            epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type","R001" as relation_code,epm.policy_member_email_id,epm.policy_member_mob_no
            FROM 
            employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_details AS ed
            WHERE epd.policy_detail_id = epm.policy_detail_id
            AND epm.family_relation_id = fr.family_relation_id
            AND fr.family_id = 0
            AND fr.emp_id = ed.emp_id
            AND ed.emp_id = ' . $emp_id . '
            AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' UNION all SELECT epm.policy_member_mob_no,epm.policy_member_email_id,epm.fr_id,epm.policy_detail_id,epd.policy_sub_type_id,epm.family_relation_id, epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
            epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type",mfr.relation_code,epm.policy_member_email_id,epm.policy_member_mob_no
            FROM 
            employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
            master_family_relation AS mfr
            WHERE epd.policy_detail_id = epm.policy_detail_id
            AND epm.family_relation_id = fr.family_relation_id
            AND fr.family_id = efd.family_id 
            AND efd.fr_id = mfr.fr_id
            AND fr.emp_id = ' . $emp_id . '
            AND `epd`.`policy_detail_id` = ' . $policy_detail_id)->result_array();
			
			
			if($response[0]['suminsured_type'] == 'family_construct_age')
			{
				$change_premium = false;

				foreach($response as $value)
				{
					//$age[] = $value['age'];
					if($value['age_type'] == 'days'){
                        $age[] = 0;
                    }else{
                        $age[] = $value['age'];
                    }
					
				}
				
				$check = $this->db->select("*")
                ->from("family_construct_age_wise_si")
                ->where("sum_insured", $response[0]['policy_mem_sum_insured'])
                ->where("family_type", $response[0]['familyConstruct'])
                ->where("policy_detail_id", $policy_detail_id)
                ->get()
                ->result_array();
				 $max_age = max($age);
				 
				foreach($check as $value)
				{
					$min_max_age = explode("-",$value['age_group']);
					if($max_age >= $min_max_age[0] && $max_age <= $min_max_age[1])
					{
						$premium = $value['PremiumServiceTax'];
					}
				}
				// echo "here";exit;
				foreach($response as $value1)
				{
					if($policy_detail_id != TELE_HEALTHPROINFINITY_GPA){
						$this->db->where('policy_member_id', $value1['policy_member_id']);
						$this->db->update('employee_policy_member', ['policy_mem_sum_premium' => $premium]);
						if($this->db->affected_rows())
						{
							$change_premium = true;
						}
					}
					
				}
				
				if($change_premium)
				{
					
					$response[0]["message"] = "Premium has been changed as per your inputs to ".$premium;
					$response[0]["new_premium"] = $premium;
				}
				
				
			}

        return $response;
    }
	function get_all_member_data_new_bk($emp_id, $policy_detail_id)
    {
		
        $response = $this->db->query('SELECT ed.emp_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
            epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type","R001" as relation_code
            FROM 
            employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_details AS ed
            WHERE epd.policy_detail_id = epm.policy_detail_id
            AND epm.family_relation_id = fr.family_relation_id
            AND fr.family_id = 0
            AND fr.emp_id = ed.emp_id
            AND ed.emp_id = ' . $emp_id . '
            AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' UNION all SELECT fr.emp_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
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
			
			
			if($response[0]['suminsured_type'] == 'family_construct_age')
			{
				$change_premium = false;

				foreach($response as $value)
				{
					$age[] = $value['age'];
					
				}
				
				$check = $this->db->select("*")
                ->from("family_construct_age_wise_si")
                ->where("sum_insured", $response[0]['policy_mem_sum_insured'])
                ->where("family_type", $response[0]['familyConstruct'])
                ->where("policy_detail_id", $policy_detail_id)
                ->get()
                ->result_array();
				 $max_age = max($age);
				 
				foreach($check as $value)
				{
					$min_max_age = explode("-",$value['age_group']);
					if($max_age >= $min_max_age[0] && $max_age <= $min_max_age[1])
					{
						$premium = $value['PremiumServiceTax'];
					}
				}

				foreach($response as $value1)
				{
					$this->db->where('policy_member_id', $value1['policy_member_id']);
					$this->db->update('employee_policy_member', ['policy_mem_sum_premium' => $premium]);
					if($this->db->affected_rows())
					{
						$change_premium = true;
					}
				}
				
				if($change_premium)
				{
					
					$response[0]["message"] = "Premium has been changed as per your inputs to ".$premium;
					$response[0]["new_premium"] = $premium;
				}
				
				
			}

        return $response;
    }
	
	public function check_validations_adult_count_add_member($family_construct,$emp_id,$policy_detail_id,$relation_id,$edit)
	{
		$data = explode("+",$family_construct);
		
		if (strpos(trim($family_construct), ' ') !== false) {
		    $data = explode(" ",$family_construct);
		}
		
        if($data[0])
		{
                  
            preg_match_all('!\d+!', $data[0], $matches);
				//get total adult count
			if($relation_id != '2' && $relation_id != '3')
			{
				  $count = $this->get_adult_count($emp_id,$policy_detail_id);
                   
				if($edit == 0)
				{
					if($count['count'] == $matches[0][0])
					{
                     
                        return false;
                    }
				}

                    
			}
                    
        }
        if($data[1])
		{
					
            preg_match_all('!\d+!', $data[1], $matches);
				//get total adult count
			if($relation_id == '2' || $relation_id == '3')
			{
				 $count = $this->get_child_count($emp_id,$policy_detail_id);
				//print_pre($count);exit;
                    if($edit == 0)
				    {
					   if($count['count'] == $matches[0][0])
					   {
                         return false;
                       }
				    }
                    
			}
                   
                    
        }
		else
		{
					
			if($relation_id == '2' || $relation_id == '3')
			{
				
				return false;
			}
	    }
        return true;
                
                
                
    }
	
	public function edit_member()
    {
			$edit_member = $this->input->post(null,true);
			if($edit_member != null || !empty($edit_member))
			$policy_member_id = $edit_member['policy_member_id'];
			$data = $this->db
			->select('*')
			->from('employee_policy_member')
			->where('policy_member_id', $policy_member_id)
			->get()
			->row_array();
			return $data;
    }
	
	public function get_sub_type_data($emp_id, $emp_policy_mem)
    {
					

		$data = $this->db
				->select('*')
				->from('employee_declare_member_sub_type')
				->where('emp_id', $emp_id)
				->where('policy_member_id', $emp_policy_mem)
				->group_by('declare_sub_type_id')
				->get();
	
		if($data->num_rows()>0)
		{
			
			$result= $data->result_array();
			
			return $result;
		}

    }
   public function edit_member_declare_data($emp_id, $emp_policy_mem,$subid)
	{
		$data_sub = $this->db->query("select pdmd.*,pdm.content,pds.sub_type_name,pds.declare_subtype_id from employee_declare_member_data as pdmd left join policy_declaration_member as pdm ON pdmd.p_member_id = pdm.p_member_id left join employee_declare_member_sub_type as edms ON pdm.declare_subtype_id = edms.declare_sub_type_id left join policy_declaration_subtype as pds ON pds.declare_subtype_id = pdm.declare_subtype_id where edms.declare_sub_type_id = ".$subid." AND pdmd.policy_member_id=".$emp_policy_mem." AND pdmd.emp_id='".$emp_id."'group by emp_declare_id  ");
		if($data_sub->num_rows()>0)
		{
		$datanew = $data_sub->result_array();
		return $datanew;
		}

	}
	public function send_otp()
	{
		$emp_id = $this->emp_id;
		$otp = random_string('numeric', 6);

		$this->session->set_userdata('otp_code','1234');
			
		$employee_details = $this->db->where('emp_id',$emp_id)->get('employee_details')->row_array();
		$this->load->library('smslib');
		$content = [
						'template'     => $this->db
						->get_where('sms_template', ['module_name' => 'OTP_CODE'])
						->row_array(),
						'send_details' => [
						'otp_code'     => $otp,
						'name' => $employee_details['emp_firstname'],
						],
					];
        $trans_details = $this->smslib->sendTransactionalSms(
                $content['template']['template_id'], $employee_details['mob_no'], $content['template']['template'], array_values($content['send_details'])
            );



		return ["status" => "true"];
   
	}
	
	function validate_otp()
    {
		
        extract($this->input->post());
	
        if ($this->session->userdata('otp_code') !== null)
        {
            if ($this->session->userdata('otp_code') == $otp)
            {
                $this->session->unset_userdata('otp_code');
                $return_data = ['status' => 'true', 'message' => 'OTP validated successfully.'];
            }
            else
            {
                $return_data = ['status' => 'false', 'message' => 'Invalid OTP entered.'];
            }
        }
        else
        {
            $return_data = ['status' => 'false', 'message' => 'OTP expired. Please resend the OTP'];
        }
        return $return_data;
    }
	function get_agent_details()
	{
		
		$agent_post = $this->input->post(null,true);
	
		
	 $agent_id = $this->agent_id;
		
		$data = $this->db
				->select('agent_id,agent_name,tl_name,tl_emp_id,am_name,am_emp_id,om_emp_id,om_name,axis_process')
				->from('tls_agent_mst')
				->where('id', $agent_id)
				->get();
			
		if(!empty($agent_post) && !empty($agent_post['agent_id']))
		{

			$base_agent_id = $agent_post['agent_id'];
			$axis_process=$agent_post['axis_process'];

			$data =    $this->db
				->select('tl_name,tl_emp_id,am_name,am_emp_id,om_emp_id,om_name,base_agent_name,imd_code,center,lob,vendor,axis_lob_id,axis_process')
				->from('tls_base_agent_tbl')
				->join('tls_axis_lob','tls_base_agent_tbl.lob=tls_axis_lob.axis_lob','left')
				->where('base_agent_id', $base_agent_id)
				->get();
		
		}
		if($data->num_rows()>0)
		{
			
			$result = $data->row_array();
			
			return $result;	
		}
		
	}
	
	public function check_validations_adult_count($family_construct,$emp_id,$policy_detail_id)
	{
				if(!$family_construct){
					return false;
				}
				$data = explode("+",$family_construct);
				
				if($data[0])
				{

					preg_match_all('!\d+!', $data[0], $matches);
					
					//get total adult count
					
					$count = $this->get_adult_count($emp_id,$policy_detail_id);
					if($count['count'] != $matches[0][0])
					{

						return false;
					}
				}
				if($data[1])
				{
					preg_match_all('!\d+!', $data[1], $matches);

					$count = $this->get_child_count($emp_id,$policy_detail_id);
					if($count['count'] != $matches[0][0])
					{
						return false;
					}

				}


				return true;
                
    }
	public function get_common_data()
	{
		 $emp_id = $this->emp_id;
	
		$data = array();
		$nominee_data = $this->db->query("select mn.nominee_type,mpn.fr_id,mpn.nominee_fname,mpn.nominee_lname,mpn.nominee_salutation,mpn.nominee_gender,mpn.nominee_dob,mpn.nominee_contact,mpn.nominee_email from member_policy_nominee as mpn,master_nominee as mn where mpn.fr_id = mn.nominee_id AND  emp_id = '$emp_id'")->row_array();
		$data['nominee_data'] = $nominee_data;
		$data['base_agent_details'] = $this->db->query("select ba.*,tlob.axis_process,ed.new_remarks,ed.comm_address,ed.comm_address1,ed.emg_cno,ed.axis_lob,ed.axis_location,ed.axis_vendor,ed.imd_code from employee_details as ed,tls_base_agent_tbl as ba,tls_axis_lob as tlob where ba.lob=tlob.axis_lob AND ed.agent_id = ba.base_agent_id AND ed.emp_id = '$emp_id'")->row_array();
		$data['axis_details'] = $this->db->query("select tav.axis_vendor,tal.axis_lob,talc.axis_location,ed.axis_process from tls_axis_lob as tal,tls_axis_vendor as tav,tls_axis_location as talc,employee_details as ed where ed.axis_lob = tal.axis_lob_id AND ed.axis_vendor = tav.axis_vendor_id AND ed.axis_location = talc.axis_loc_id AND emp_id = '$emp_id'")->row_array();
		$data['ghd_proposer'] =  $this->db->query("select * from tls_ghd_employee_declare where emp_id = '$emp_id'")->result_array();
		$data['emp_declare'] = $this->db->query("select p_declare_id,format from employee_declare_data where emp_id = '$emp_id'")->result_array();
		$data['emp_details'] = $this->db->select('pid,deductable,product_id,annual_income,occupation,saksham_id,emp_middlename,ISNRI,lead_id,emp_id,emp_code,emp_firstname,emp_lastname,gender,bdate,emg_cno,mob_no,email,emp_address,emp_city,emp_state,emp_pincode,street,location,doj,pancard,adhar,address,comm_address,comm_address1,ref1,ref2,salutation,emp_city,emp_state,emp_pincode,ifsc_code,auto_renewal,payment_mode,preferred_contact_date,preferred_contact_time,av_remark,GCI_optional,makerchecker,is_makerchecker_journey')->where(["emp_id" => $emp_id])->get("employee_details")->row_array();
		
		if($data['emp_details']['occupation'] != ""){
			$data['emp_details']['occupation_name'] = $this->db->get_where("master_occupation",array("id"=>$data['emp_details']['occupation']))->row_array()['name'];
			
		}
	return $data;
	}
	public function health_declaration_emp_data($parent_id)
	{
		$emp_id = $this->emp_id;
		$data = $this->db
				->select('employee_declare_data.format,employee_declare_data.remark,policy_declaration.policy_detail_id,policy_declaration.policy_detail_id,policy_declaration.proposal_continue,policy_declaration.p_declare_id,policy_declaration.content,policy_declaration.is_remark,policy_declaration.is_answer,policy_label_declarartion.label,policy_label_declarartion.p_label_id')
				->from('policy_declaration')
				->join('policy_label_declarartion ', 'policy_label_declarartion.p_declare_id = policy_declaration.p_declare_id', 'left')
				->join('employee_declare_data ', 'employee_declare_data.p_declare_id = policy_declaration.p_declare_id', 'left')
				->where('policy_declaration.parent_policy_id', $parent_id)
				->where('employee_declare_data.emp_id', $emp_id)
				->get()
				->result_array();
				// echo $this->db->last_query();exit;
		return $data;
	}
	public function get_policy_data_emp($parent_id)
	{
		
		
		$arr_new = array();
		$emp_id = $this->emp_id;
		//$parent_id = $parent_id;
		//$parent_id = $this->parent_id;
		//condition for t03
		if($parent_id == 'NvpnoiwGGDQPVA23w'){
			$data = $this->db->query("SELECT pid,deductable from employee_details where emp_id = '".$emp_id."'")->row_array();
			//print_pre($data);exit;
			$query = $this->db->query("SELECT *
										FROM employee_policy_detail as epd
										JOIN product_master_with_subtype AS psw ON epd.product_name= psw.id
										JOIN master_policy_sub_type AS mpst ON psw.policy_subtype_id = mpst.policy_sub_type_id
										WHERE epd.policy_detail_id IN (".$data['pid'].")")->result_array();
			// echo $this->db->last_query();print_r($query);exit;
			foreach ($query as $key => $value) {
				$arr_new['comboData']['plan_name'] = $value['product_name'];
				//echo $combo_data['product_name'];
	            $arr_new['comboData']['product_name'] = $this
	                ->obj_home
	                ->get_sub_name($value['policy_parent_id']);
					
	            // $arr_new['comboData']['combo_flag'] = "Y";
				$arr_new['comboData']['combo_flag'] = ($data['pid'] == TELE_HEALTHPROINFINITY_GHI_GPA) ? "Y" : "N";
	            $arr_new['comboData']['policy_detail_id'] = $data['pid'];
	            $arr_new['comboData']['deductable'] = $data['deductable'];
			}
            
        }else{
        	$combo_product = $this->db->query("select * from product_master_with_subtype where policy_parent_id = '$parent_id' ")->result_array();
			//echo $this->db->last_query();exit;
			foreach ($combo_product as $combo_data)
	        {
				
				//print_pre($combo_data);exit;
	            if ($combo_data['combo_flag'] == 'Y')
	            {
					$query = $this->db->query("select * from product_master_with_subtype as psw Join master_policy_sub_type as mpst ON psw.policy_subtype_id = mpst.policy_sub_type_id where psw.product_name = '" . $combo_data['product_name'] . "'AND psw.combo_flag ='" . Y . "'")->row_array();
	                $arr_new['comboData']['plan_name'] = $query['product_name'];
					//echo $combo_data['product_name'];
	                $arr_new['comboData']['product_name'] = $this
	                    ->obj_home
	                    ->get_sub_name($combo_data['policy_parent_id']);
						
	                $arr_new['comboData']['combo_flag'] = $combo_data['combo_flag'];
	                $arr_new['comboData']['policy_detail_id'] = $this
	                    ->obj_home
	                    ->policy_detail_combo_flag($combo_data['product_name']);
	            }
	            else
	            {
	                $query = $this
	                    ->db
	                    ->query("select * from product_master_with_subtype as psw Join master_policy_sub_type as mpst ON psw.policy_subtype_id = mpst.policy_sub_type_id where psw.product_name = '" . $combo_data['product_name'] . "'AND psw.combo_flag !='" . Y . "'")->row_array();
	                $arr_new['indivisual']['product_name'] = $query['product_name'];
					$arr_new['indivisual']['plan_name'] = $query['product_name'];
	                $arr_new['indivisual']['combo_flag'] = $combo_data['combo_flag'];
	                $arr_new['indivisual']['policy_detail_id'] = $this
	                    ->obj_home
	                    ->policy_detail_not_combo($combo_data['id']);
	            }

	        }
        }
		
	// print_pre($arr_new);exit;
		foreach($arr_new as $get_policy_data)
		{
			// echo $get_policy_data['combo_flag'];exit;
			$data_explode = explode(',', $get_policy_data['policy_detail_id']);
			if ($get_policy_data['combo_flag'] == 'Y')
				{	
					foreach($data_explode as $get_member)
					{
						
					$policy_sub_type_name = $this->get_policy_name($get_member);
					$sub_type_id = $this->get_data('employee_policy_detail', 'policy_detail_id', $get_member);
					$policy_sub_type_id = $sub_type_id['policy_sub_type_id'];
					$master_policy = $this->get_data_multiple('product_master_with_subtype', 'policy_parent_id', $parent_id, 'policy_subtype_id', $policy_sub_type_id);
					$policy_sub_type_name = $this->get_policy_name($get_member);
					$arr_new['comboData']['policy_sub_type_name'] = $policy_sub_type_name;

					if($get_member == TELE_HEALTHPROINFINITY_GHI_ST){
						$member_data = $this->get_all_member_data_healthproxl($emp_id, $get_member,$get_policy_data['deductable']);
					}else{
						$member_data = $this
							->obj_home
							->get_all_member_data_new($emp_id, $get_member);
					}
					
					
					$arr_new['comboData']['customer_detail'][]= $this->get_insured_summary($member_data, $policy_sub_type_name, $parent_id, $emp_id,$get_policy_data['plan_name'],$master_policy);
					// print_pre($policy_sub_type_name);
					// print_pre($policy_sub_type_id);
					// print_pre($master_policy);
					// print_pre($policy_sub_type_name);
					//print_pre($member_data);exit;
					
					
					}
				}
				else
				{	
					foreach($data_explode as $get_member)
					{
						$policy_sub_type_name = $this->get_policy_name($get_member);
						$sub_type_id = $this->get_data('employee_policy_detail', 'policy_detail_id', $get_member);
						$policy_sub_type_id = $sub_type_id['policy_sub_type_id'];

						$master_policy = $this->get_data_multiple('product_master_with_subtype', 'policy_parent_id', $parent_id, 'policy_subtype_id', $policy_sub_type_id);

						
						
						$policy_sub_type_name = $this->get_policy_name($get_member);
						$arr_new['indivisual']['policy_sub_type_name'] = $policy_sub_type_name;
						if($get_member == TELE_HEALTHPROINFINITY_GHI_ST){
							$member_data = $this->get_all_member_data_healthproxl($emp_id, $get_member,$get_policy_data['deductable']);
							 
							$master_policy = $this
							->db
							->query("SELECT pmws.master_policy_no FROM employee_policy_detail AS epd
							INNER JOIN product_master_with_subtype AS pmws
							ON epd.product_name = pmws.id
							WHERE epd.policy_detail_id = ".$get_member."
							LIMIT 1")->row_array();


						}else{
							$member_data = $this
								->obj_home
								->get_all_member_data_new($emp_id, $get_member);
						}
						$arr_new['indivisual']['customer_detail'] = $this->get_insured_summary($member_data, $policy_sub_type_name, $parent_id, $emp_id,$get_policy_data['plan_name'],$master_policy);

					}
				}
			
		}
		
		// print_pre($arr_new);exit;
		
		return $arr_new;
	}

public function policy_detail_combo_flag($product_name)
{
	$data = $this->db
	->select('*')
	->from('product_master_with_subtype as pms, employee_policy_detail as epd')
	->where('pms.id = epd.product_name')
	->where('pms.product_name', $product_name)
	->get()
	->result_array();
	for ($i = 0; $i < count($data); $i++) {
	if ($data[$i]['suminsured_type'] == "family_construct") {
	if ($data[$i]['combo_flag'] == "Y") {		
	$policy_ids[] = $data[$i]['policy_detail_id'];
	}
	}
	if ($data[$i]['suminsured_type'] == "family_construct_age") {
	if ($data[$i]['combo_flag'] == "Y") {
	$policy_ids[] = $data[$i]['policy_detail_id'];
	}
	}
		
		
	}
	
		$y1a = implode(",", $policy_ids);
	
	print_R($y1);
	return $y1a;
}

public function policy_detail_not_combo($product_name)
{
	$data = $this->db
	->select('*')
	->from('product_master_with_subtype as pms, employee_policy_detail as epd')
	->where('pms.id = epd.product_name')
	->where('pms.id', $product_name)
	->where('pms.combo_flag!=', 'Y')
	->get()
	->result_array();
	for ($i = 0; $i < count($data); $i++) {
	if ($data[$i]['suminsured_type'] == "family_construct") {		
	$policy_ids[] = $data[$i]['policy_detail_id'];

	}
	if ($data[$i]['suminsured_type'] == "family_construct_age") {
	$policy_ids[] = $data[$i]['policy_detail_id'];
	}
	if ($data[$i]['suminsured_type'] == "memberAge") {
	$policy_ids[] = $data[$i]['policy_detail_id'];
	}	
		
	}
	$y1 = implode(",", $policy_ids);
	return $y1;
}
	public function get_total_premium($data,$id){
		$sum = 0;
	
		foreach($data as $value){
			//echo $value['policy_sub_type_id'].'--'.$value['policy_mem_sum_premium'];
			if($id == 'test123' && $value['policy_sub_type_id'] != 1){
				$sum+= $value['policy_mem_sum_premium'];
			}else if($id == 'NvpnoiwGGDQPVA23w' && $value['policy_sub_type_id'] != 1){
				
				$sum+= $value['policy_mem_sum_premium'];
			}else{
				return $value['policy_mem_sum_premium'];
			}
		}
		
		return $sum;
	}
    public function get_insured_summary($member_datas, $policy_sub_type_name, $parent_id, $emp_id,$plan_name,$master_policy)
    {
		//print_pre($member_datas);continue;
		$z = 1;
        foreach ($member_datas as $member_data)
        {

            $i = 1;
            $chronnic_disease = $this
                ->db
                ->query("select * from policy_declaration_member where parent_policy_id ='$parent_id'")->row_array();

            $policy_member_id = $member_data['policy_member_id'];
            $sub_type_chronic = $this
                ->db
                ->query("select group_concat(pds.sub_type_name) as chronic_subtype from employee_declare_member_sub_type as edms,policy_declaration_subtype as pds where edms.declare_sub_type_id = pds.declare_subtype_id AND edms.policy_member_id = '$policy_member_id' group by edms.policy_member_id")->row_array();
            if (!empty($sub_type_chronic))
            {
                $cronic_disease = $sub_type_chronic['chronic_subtype'];
            }
            else
            {
                $cronic_disease = 'No';
            }
            $st_text = '';
            $sum_insure = $member_data['policy_mem_sum_insured'];
		 	if($parent_id == 'NvpnoiwGGDQPVA23w'){
		 		 $data = $this->db->query("SELECT pid,deductable from employee_details where emp_id = '".$emp_id."'")->row_array();
		 		if($data['pid'] == TELE_HEALTHPROINFINITY_GHI_ST){
		 			$sum_insure = $member_data['policy_mem_sum_insured'] - $data['deductable'];
		 			$st_text = ' - Super Topup';
		 		}
		 	}
            //$sub_type_chronic_ans = $this->db->query("select pds.sub_type_name,edmd.format,pdm.content from employee_declare_member_sub_type as edms,policy_declaration_subtype as pds,employee_declare_member_data as edmd,policy_declaration_member as pdm where edms.declare_sub_type_id = pds.declare_subtype_id AND edms.policy_member_id = edmd.policy_member_id AND edmd.p_member_id = pdm.p_member_id AND edms.policy_member_id = '$policy_member_id' ")->result_array();
            $subtype_id = $this->get_sub_type_data($emp_id, $policy_member_id);
            
			if($z == 1){
				$customer_detail .= '<div class="col-md-12"> <p class="mt-2 mb-2 text-center" style="color: #da8089;font-size: 17px;">'.$policy_sub_type_name .''.$st_text.'</p><table class="table table-bordered text-center"><thead class="text-uppercase col-da80">';
			}else{
				$customer_detail .= '<div class="col-md-12"><table class="table table-bordered text-center"><thead class="text-uppercase col-da80">';
			}
	
		 if($z == 1){
		 	
			//maker-checker update - 30/07/2021
			 $this->db->select('is_makerchecker_journey');
			 $this->db->from('employee_details');
			 $this->db->where('emp_id',$emp_id);
			 $is_maker_checker = $this->db->get()->row_array();

			 $is_maker_checker = $is_maker_checker['is_makerchecker_journey'];


			// 03-02-2022 - SVK005 - remove if
				if($plan_name == 'Group Activ Health-Tele'){
					$plan_name = 'Group Activ Health';
				}
				if($plan_name == 'Tele - Health Pro Infinity'){
					$plan_name = 'Health Pro Infinity';
				}
		 	
		
		 $customer_detail .= '<tr><th scope="col" style="width:25%;">Plan Name</th>
	<th scope="col" style="font-weight:600 !important; width:25%;">' .$plan_name.'</th>
	<th style="width: 1px;border: none !important;border-color: #fff;"></th>
	<th scope="col" style="width:25%;">Master Policy No.</th>
	<th scope="col" style="font-weight:600 !important;width:25%">' . $master_policy['master_policy_no'] . '</th>
	
	</tr>
	<tr>
	<th scope="col" style="width:25%;">Sum Insured</th>
	<th scope="col" style="font-weight:600 !important;width:25%;">' . $sum_insure . '</th>
	<th style="width: 1px;border: none !important;border-color: #fff;"></th>
	<th scope="col" style="width:25%;">Premium </th>
	<th scope="col" style="font-weight:600 !important;width:25%;">Rs ' . $this->get_total_premium($member_datas,$parent_id) . '</th>
	</tr>
	<tr>
	<th scope="col" style="width:25%;">Family Construct </th><th scope="col" style="font-weight:600 !important;width:25%;">' . $member_data['familyConstruct'] . '</th>';
	if($parent_id == 'NvpnoiwGGDQPVA23w'){
		 		 
		 		if($data['pid'] == TELE_HEALTHPROINFINITY_GHI_ST){
		 			$customer_detail .= '<th style="width: 1px;border: none !important;border-color: #fff;"></th><th scope="col" style="width:25%;">Deductible </th><th scope="col" style="font-weight:600 !important;width:25%;">' . $data['deductable'] . '</th>';
		 		}	 	
		
		 }
	$customer_detail .= '</tr>';

		
		 }
	
            $customer_detail .= '<tr>
	<th scope="col" colspan="5" style="background: #da8089;color: #fff;">Member '.$z.' Details</th>
	</tr>';
      $customer_detail .= '
	<tr>			
	<th scope="col" style="width:25%;">First Name</th>
	<th scope="col" style="font-weight:600 !important;word-break: break-word;width:25%;">' . strtoupper($member_data['firstname']) . '</th>
	<th style="width: 1px;border: none !important;border-color: #fff;"></th>
	<th scope="col" style="width:25%;">Relation</th>
	<th scope="col" style="font-weight:600 !important;width:25%;">' . $member_data['relationship'] . '</th>
	</tr>
	<tr>
	<th scope="col" style="width:25%;">Last Name </th>
	<th scope="col" style="font-weight:600 !important;word-break: break-word;width:25%;">' . strtoupper($member_data['lastname']) . '</th>
	<th style="width: 1px;border: none !important;border-color: #fff;"></th>
	<th scope="col" style="width:25%;">DOB(DD-MM-YYYY) </th>
	<th scope="col" style="font-weight:600 !important;width:25%;">' . $member_data['dob'] . '</th>
	</tr>';
	if($parent_id == 'NvpnoiwGGDQPVA23w'){
		if($member_data['fr_id'] == 0 || $member_data['fr_id'] == 1){
		//if($data['pid'] != TELE_HEALTHPROINFINITY_GHI_ST && ($member_data['fr_id'] == 0 || $member_data['fr_id'] == 1)){
			$customer_detail .= '<tr>
				<th scope="col" style="width:25%;">Email ID </th>
				<th scope="col" style="font-weight:600 !important;word-break: break-word;width:25%;">' . strtoupper($member_data['policy_member_email_id']) . '</th>
				<th style="width: 1px;border: none !important;border-color: #fff;"></th>
				<th scope="col" style="width:25%;">Mobile Number </th>
				<th scope="col" style="font-weight:600 !important;width:25%;">' . $member_data['policy_member_mob_no'] . '</th>
				</tr>';
		}
	}
	// if($plan_name != 'Group Activ Health-Tele'){
		 // $customer_detail .= '<tr>
	// <th scope="col" style="width:25%;">Member Premium </th>
	// <th scope="col" style="font-weight:600 !important;word-break: break-word;width:25%;">' . strtoupper($member_data['policy_mem_sum_premium']) . '</th>
	
	// <tr>';
	// }
	
            if (!empty($chronnic_disease))
            {
                $customer_detail .= '<th scope="col" style="">Chronic Disease </th><th scope="col" style="font-weight:600 !important;">' . $cronic_disease . '</th>';
            }
            '<th style="width: 1px;border: none !important;border-color: #fff;"></th>
	<th scope="col" style="width:25%;">Gender </th><th scope="col" style="font-weight:600 !important;width:25%;">' . $member_data['gender'] . '</th>
	</tr>
	<tr>';
            /*********************************************** Cronic Disease **************************************************************************/
           	if (!empty($subtype_id))
            {
                foreach ($subtype_id as $subid)
                {

                    $subtype_name = $this->sub_type_name($subid['declare_sub_type_id']);
                    //print_pre($subtype_name);
                    $customer_detail .= '<div id="di' . $subid['declare_sub_type_id'] . '" class="col-md-12"><table class="table table-bordered text-center">
					<thead class="text-uppercase">
					<tr>
					<th scope="col" style="text-align: left; font-weight: 600;">' . $subtype_name['sub_type_name'] . '</th>
					<th scope="col" style="font-weight: 600;">Answer</th>
					</tr>
					</thead>

					<tbody id="mydatasmembers">';
                    $policy_member_declare = $this->db->query("select md.disease,z.content,e.policy_member_id from 
																employee_declare_member_sub_type e,master_disease md,policy_declaration_member z
																where e.declare_sub_type_id = md.id
																and z.declare_subtype_id = md.id
																and e.emp_id = '".$emp_id."'
																and z.parent_policy_id = '".$parent_id."'
																and md.sub_member_code = '".$subtype_name['sub_member_code']."'
																and z.declare_subtype_id = '".$subid['declare_sub_type_id']."'
																order by md.disease
																")->result_array();//$this->edit_member_declare_data($emp_id, $policy_member_id, $subid['declare_sub_type_id']);
                    //print_pre($policy_member_declare);exit;
                    foreach ($policy_member_declare as $key => $value)
                    {
                        $customer_detail .= '<tr>
											<td style="text-align:left;"><input type="hidden" class="mycontent" value="' . $value['p_member_id'] . '"/>' . $value['content'] . '</td>
											<td style="width: 150px;"><div class="custom-control custom-radio" style="float: left;"><input type="radio"  disabled name="' . $value['p_member_id'] . '" id="' . $value['p_member_id'] . '" class="custom-control-input radios_out" value="Yes" > <label class="custom-control-label" for="' . $value['p_member_id'] . '"> Yes123 </label> </div>
											<div class="custom-control custom-radio" style="float:right;"> <input type="radio" disabled name="' . $value['p_member_id'] . '" class="custom-control-input radios_out " value="No" id="' . $value['p_member_id'] . '_1" checked="">  <label class="custom-control-label" for="' . $value['p_member_id'] . '_1" > No </label></div>
											</td>
											</tr>';
                    }
                    $customer_detail .= '</tbody></table></div>';
                } 
            }
            /*if (!empty($subtype_id))
            {
                foreach ($subtype_id as $subid)
                {
                    $subtype_name = $this
                        ->obj_home
                        ->sub_type_name($subid['declare_sub_type_id']);
                    $customer_detail .= '<div id="di' . $subid['declare_sub_type_id'] . '" class="col-md-12"><table class="table table-bordered text-center">
					<thead class="text-uppercase">
					<tr>
					<th scope="col" style="text-align: left; font-weight: 600;">' . $subtype_name['sub_type_name'] . '</th>
					<th scope="col" style="font-weight: 600;">Answer</th>
					</tr>
					</thead>

					<tbody id="mydatasmembers">';
                    $policy_member_declare = $this
                        ->obj_home
                        ->edit_member_declare_data($emp_id, $policy_member_id, $subid['declare_sub_type_id']);

                    foreach ($policy_member_declare as $key => $value)
                    {
                        $customer_detail .= '<tr>
											<td style="text-align:left;"><input type="hidden" class="mycontent" value="' . $value['p_member_id'] . '"/>' . $value['content'] . '</td>
											<td style="width: 150px;">No</td>
											
											</tr>';
                    }
                    $customer_detail .= '</tbody></table>';
                }
            } */
            /*********************************************** Insured Member 2nd col **************************************************************************/
            $i++;
			
			$z++;

            $customer_detail .= '</thead></table></div>';
        }
	
        return $customer_detail;
    }
	public function get_policy_name($policy_detail_id)
{
	$query = $this->db->query("select * from employee_policy_detail as epd Join master_policy_sub_type as mpst ON epd.policy_sub_type_id = mpst.policy_sub_type_id where epd.policy_detail_id = '".$policy_detail_id."'");
	
	$result = $query->row_array();

	$new_arrays = $result['policy_sub_type_name'];
	
	return $new_arrays;
}
public function get_data($table,$col1,$result)
	{
		$data = $this->db->select("*")
				->from($table)
				->where($col1, $result)
				->get()->row_array();
				return $data;
	}
	public function get_data_multiple($table,$col1,$result,$col2,$result2)
	{
		$data = $this->db->select("*")
				->from($table)
				->where($col1, $result)
				->where($col2, $result2)
				->get()->row_array();
				return $data;
	}
}