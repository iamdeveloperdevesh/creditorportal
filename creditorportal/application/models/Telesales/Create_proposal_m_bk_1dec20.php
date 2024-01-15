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
		
    }
	
	public function get_all_policy_data($parent_id) 
	{
       
	   
        $data = $this->db
                ->select('pms.master_policy_no,pms.product_name,pms.policy_parent_id,epd.policy_detail_id,pms.combo_flag,mfr.fr_id,mfr.fr_name,mbir.relationship_id,mbir.max_adult,mbir.max_child,epd.policy_sub_type_id,epd.suminsured_type,mpst.policy_sub_type_name,mfr.gender_option')
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
	
	public function get_suminsured_data($parent_id) 
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
		$this->db->where('emp_id',$emp_id);
		$this->db->update('employee_details',$emp_agent_data);
	}
	public function add_nominee($nominee_data)
	{
		$this->db->insert('member_policy_nominee',$nominee_data);
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
			$response = $this->db->select('ed.emp_id,ed.emp_code,ed.salutation,ed.emp_firstname,ed.emp_middlename,ed.emp_lastname,ed.fr_id,ed.company_id,ed.gender,ed.bdate,ed.mob_no,ed.email,ed.emp_grade,ed.emp_designation,ed.emp_address,ed.emp_city,ed.emp_state,ed.emp_pincode,ed.street,ed.location,ed.flex_amount,ed.total_salary,ed.gmc_grade_id,ed.emp_pay,ed.doj,fr.family_relation_id,fr.emp_id,fr.family_id')
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
	public function member_declare_data($parent_id)
    {
		$datas=[];
		$data=array();
		$data1 = $this->db
				->select('*')
				->from('policy_declaration_member')
				->where('parent_policy_id', $parent_id)
				->where('declare_subtype_id is not null',NULL,false)
				->get()
				->result_array();
			
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

		$data = $this->db
				->select('policy_declaration.policy_detail_id,policy_declaration.policy_detail_id,policy_declaration.proposal_continue,policy_declaration.p_declare_id,policy_declaration.content,policy_declaration.is_remark,policy_declaration.is_answer,policy_label_declarartion.label,policy_label_declarartion.p_label_id')
				->from('policy_declaration')
				->join('policy_label_declarartion ', 'policy_label_declarartion.p_declare_id = policy_declaration.p_declare_id', 'left')
				->where('policy_declaration.parent_policy_id', $parent_id)
				->get()
				->result_array();
		return $data;
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
				
				$test = ["status" => true, "message" => "Sucessfully Changed", "data" => $data];
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
					return ["message" => "Min age for ".$check_min_max['fr_name']. " is ".$check_min_max['min_age']." and max age is ".$check_min_max['max_age'] ];

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
	function get_all_member_data_new($emp_id, $policy_detail_id)
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
				->select('agent_id,agent_name,tl_name,tl_emp_id,am_name,am_emp_id,om_emp_id,om_name')
				->from('tls_agent_mst')
				->where('id', $agent_id)
				->get();
			
		if(!empty($agent_post) && !empty($agent_post['agent_id']))
		{

			$base_agent_id = $agent_post['agent_id'];
			$data =    $this->db
							->select('tl_name,tl_emp_id,am_name,am_emp_id,om_emp_id,om_name,base_agent_name,imd_code,center,lob,vendor')
							->from('tls_base_agent_tbl')
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
		$data['base_agent_details'] = $this->db->query("select ba.*,ed.comm_address,ed.comm_address1,ed.emg_cno,ed.axis_lob,ed.axis_location,ed.axis_vendor from employee_details as ed,tls_base_agent_tbl as ba where ed.agent_id = ba.base_agent_id AND emp_id = '$emp_id'")->row_array();
		$data['axis_details'] = $this->db->query("select tav.axis_vendor,tal.axis_lob,talc.axis_location from tls_axis_lob as tal,tls_axis_vendor as tav,tls_axis_location as talc,employee_details as ed where ed.axis_lob = tal.axis_lob_id AND ed.axis_vendor = tav.axis_vendor_id AND ed.axis_location = talc.axis_loc_id AND emp_id = '$emp_id'")->row_array();
		$data['ghd_proposer'] =  $this->db->query("select * from tls_ghd_employee_declare where emp_id = '$emp_id'")->result_array();
		$data['emp_declare'] = $this->db->query("select p_declare_id,format from employee_declare_data where emp_id = '$emp_id'")->result_array();
		$data['emp_details'] = $this->db->select('saksham_id,emp_middlename,ISNRI,lead_id,emp_id,emp_code,emp_firstname,emp_lastname,gender,bdate,emg_cno,mob_no,email,emp_address,emp_city,emp_state,emp_pincode,street,location,doj,pancard,adhar,address,comm_address,comm_address1,ref1,ref2,salutation,emp_city,emp_state,emp_pincode,ifsc_code,auto_renewal,payment_mode,preferred_contact_date,preferred_contact_time,av_remark')->where(["emp_id" => $emp_id])->get("employee_details")->row_array();

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
		return $data;
	}
	public function get_policy_data_emp($parent_id)
	{
		$arr_new = array();
		$emp_id = $this->emp_id;
		$parent_id = $this->parent_id;
		 $combo_product = $this->db->query("select * from product_master_with_subtype where policy_parent_id = '$parent_id' ")->result_array();
		foreach ($combo_product as $combo_data)
        {
            if ($combo_data['combo_flag'] == 'Y')
            {
				$query = $this->db->query("select * from product_master_with_subtype as psw Join master_policy_sub_type as mpst ON psw.policy_subtype_id = mpst.policy_sub_type_id where psw.product_name = '" . $combo_data['product_name'] . "'AND psw.combo_flag ='" . Y . "'")->row_array();
                $arr_new['comboData']['plan_name'] = $query['product_name'];
				
                $arr_new['comboData']['product_name'] = $this
                    ->obj_home
                    ->get_sub_name($combo_data['product_name']);
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
		foreach($arr_new as $get_policy_data)
		{

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
					$member_data = $this
					->obj_home
					->get_all_member_data_new($emp_id, $get_member);
					$arr_new['comboData']['customer_detail'] = $this->get_insured_summary($member_data, $policy_sub_type_name, $parent_id, $emp_id,$get_policy_data['plan_name'],$master_policy);
					//print_R($arr_new);
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
				$member_data = $this
				->obj_home
				->get_all_member_data_new($emp_id, $get_member);
				$arr_new['indivisual']['customer_detail'] = $this->get_insured_summary($member_data, $policy_sub_type_name, $parent_id, $emp_id,$get_policy_data['plan_name'],$master_policy);

				}
				}
			
		}
		
		
		
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
    public function get_insured_summary($member_datas, $policy_sub_type_name, $parent_id, $emp_id,$plan_name,$master_policy)
    {
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
            //$sub_type_chronic_ans = $this->db->query("select pds.sub_type_name,edmd.format,pdm.content from employee_declare_member_sub_type as edms,policy_declaration_subtype as pds,employee_declare_member_data as edmd,policy_declaration_member as pdm where edms.declare_sub_type_id = pds.declare_subtype_id AND edms.policy_member_id = edmd.policy_member_id AND edmd.p_member_id = pdm.p_member_id AND edms.policy_member_id = '$policy_member_id' ")->result_array();
            $subtype_id = $this->get_sub_type_data($emp_id, $policy_member_id);
            $customer_detail .= '<div class="col-md-12"> <table class="table table-bordered text-center">
	<thead class="text-uppercase col-da80">';
	
		 if($z == 1){
		
		 $customer_detail .= '<tr><th scope="col" style="">Plan Name</th>
	<th scope="col" style="font-weight:600 !important;">' . $plan_name . '</th>
	<th style="width: 1px;border: none !important;border-color: #fff;"></th>
	<th scope="col" style="">Master Policy No.</th>
	<th scope="col" style="font-weight:600 !important;">' . $master_policy['master_policy_no'] . '</th>
	
	</tr>
	<tr>
	<th scope="col" style="">Sum Insured</th>
	<th scope="col" style="font-weight:600 !important;">' . $member_data['policy_mem_sum_insured'] . '</th>
	<th style="width: 1px;border: none !important;border-color: #fff;"></th>
	<th scope="col" style="">Premium </th>
	<th scope="col" style="font-weight:600 !important;">Rs ' . $member_data['policy_mem_sum_premium'] . '</th>
	</tr>
	<tr>
	<th scope="col" style="">Family Construct </th><th scope="col" style="font-weight:600 !important;">' . $member_data['familyConstruct'] . '</th>
	</tr>';
		
		 }
	
            $customer_detail .= '<tr>
	<th scope="col" colspan="5" style="background: #da8089;color: #fff;">Member '.$z.' Details</th>
	</tr>';
      $customer_detail .= '
	<tr>			
	<th scope="col" style="">First Name</th>
	<th scope="col" style="font-weight:600 !important;word-break: break-word;">' . strtoupper($member_data['firstname']) . '</th>
	<th style="width: 1px;border: none !important;border-color: #fff;"></th>
	<th scope="col" style="">Relation</th>
	<th scope="col" style="font-weight:600 !important;">' . $member_data['relationship'] . '</th>
	</tr>
	<tr>
	<th scope="col" style="">Last Name </th>
	<th scope="col" style="font-weight:600 !important;word-break: break-word;">' . strtoupper($member_data['lastname']) . '</th>
	<th style="width: 1px;border: none !important;border-color: #fff;"></th>
	<th scope="col" style="">DOB(DD-MM-YYYY) </th>
	<th scope="col" style="font-weight:600 !important;">' . $member_data['dob'] . '</th>
	</tr>
	<tr>';
            if (!empty($chronnic_disease))
            {
                $customer_detail .= '<th scope="col" style="">Chronic Disease </th><th scope="col" style="font-weight:600 !important;">' . $cronic_disease . '</th>';
            }
            '<th style="width: 1px;border: none !important;border-color: #fff;"></th>
	<th scope="col" style="">Gender </th><th scope="col" style="font-weight:600 !important;">' . $member_data['gender'] . '</th>
	</tr>
	<tr>';
            /*********************************************** Cronic Disease **************************************************************************/
            if (!empty($subtype_id))
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
            }
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