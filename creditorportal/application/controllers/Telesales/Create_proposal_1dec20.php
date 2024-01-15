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
		
		$telSalesSession = $this->session->userdata('telesales_session');
		$this->agent_id = encrypt_decrypt_password($telSalesSession['agent_id'],'D');
		$this->emp_id = $telSalesSession['emp_id'];
		$this->parent_id = $telSalesSession['parent_id'];
		$this->db->query("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
    }

    public function index()
    {
	}
	public function employee_data()
	{
		$emp_id = $this->emp_id;
		$result =  $this->db->select('saksham_id,emp_middlename,ISNRI,lead_id,emp_id,emp_code,emp_firstname,emp_lastname,gender,bdate,mob_no,email,emp_address,emp_city,emp_state,emp_pincode,street,location,doj,pancard,adhar,address,comm_address,ref1,ref2,salutation,emp_city,emp_state,emp_pincode,ifsc_code')->where(["emp_id" => $emp_id])->get("employee_details")->row();
		print_r(json_encode($result));
	}
	public function get_agent_details()
	{
		
		echo json_encode($this->obj_home->get_agent_details($this->agent_id));
	}
    public function create_proposal()
    {
	$emp_id = $this->emp_id;
		extract($this->input->post());
		$data['disposition'] = $this->db->select('*')->from('disposition_master')->get()->result_array();
		$data['payment_summary'] = $this->db->select('max(ed.date) as "date",ed.disposition_id,dm.Dispositions,dm.`Sub-dispositions`,ed.agent_name')
         ->from('employee_disposition ed')
         ->join('disposition_master dm', 'ed.disposition_id = dm.id')
		 ->where('emp_id',$emp_id)
		 ->group_by('ed.disposition_id') 
		 ->order_by('ed.id','desc')
		->get()
		->result_array();
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
		$data['lob'] = $this->db->select('*')
         ->from('tls_axis_lob l')
		->get()
		->result_array();
		
		
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
		
			
		$parent_id = $this->parent_id;;
		
		print_r(json_encode($this->obj_home->get_all_policy_data($parent_id)));
	} 
	
	public function get_suminsured_data()
	{
		
		$parent_id = $this->parent_id;
		
		echo json_encode($this->obj_home->get_suminsured_data($parent_id));
	}
	
	public function get_family_construct()
	{
		
		echo json_encode($this->obj_home->get_family_construct());
	}
	
	public function get_premium() 
	{

        echo json_encode($this->obj_home->get_premium());
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
	
    public function health_declaration()
    {
		$parent_id = $this->parent_id;
		$policy_declaration_data = $this->obj_home->health_declaration($parent_id);
		$data .= '<table class="table table-bordered text-center">
		<thead class="text-uppercase">
		<tr>
		<th scope="col" style="width: 750px; text-align: left; font-weight:600 !important;">Questionnaire</th>
		<th scope="col" style="font-weight:600 !important;">Answer</th>
		</tr>
		</thead>
		<tbody id="mydatas">';
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
        echo  $data;
	}   
    
	public function family_details_insert()
	{
		$family_data = $this->obj_home->family_details_insert();
		echo json_encode($family_data);
	}
	
	function get_all_data()
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

		$emp_mem_id = $edit_member_subtype['emp_edit_id'];
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

	public function delete_member()
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
			$emp_id = $this->emp_id;
			$parent_id = $this->parent_id;
		    $get_lead_id = $this->db->query("select lead_id,imd_code from employee_details where emp_id = '$emp_id' ")->row_array();
			if($get_lead_id['lead_id'] != 0)
			{
				$lead_id = $get_lead_id['lead_id'];
				$product_id = 'R06';
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

						$proposal_create = $this->db->query("select id from proposal where emp_id = '$emp_id'")->row_array();
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
									
									$this->db->where('id', $proposal_id);
									$this->db->update('proposal', [
										"sum_insured" => $value['policy_mem_sum_insured'],
										"premium" => $value['policy_mem_sum_premium']
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
							
			$paymment_array = ['date' => date('Y-m-d H:i:s'),'disposition_id' => $sub_isposition,'agent_name' => $agent_name,'emp_id' => $emp_id];
			$this->db->insert('employee_disposition',$paymment_array);
			//echo $this->db->last_query();exit;
			
			$data = $this->db->select('max(ed.date) as "date",ed.disposition_id,dm.Dispositions,dm.`Sub-dispositions`,ed.agent_name')
         ->from('employee_disposition ed')
         ->join('disposition_master dm', 'ed.disposition_id = dm.id')
		 ->where('ed.emp_id',$emp_id)
		 ->group_by('ed.disposition_id') 
		 ->order_by('ed.id','desc')
		->get()
		->result_array();
			
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
			if (FALSE)
		{
			
			 $z = validation_errors();
			 $validation_err= ["status" => false, "message" => $z];
             print_r(json_encode($validation_err));   
		}
		else
		{
		
			$emp_id = $this->emp_id;
			$parent_id = $this->parent_id;
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
			$myGHD = $this->input->post('myGHD',true);
			$nominee_salutation = $this->input->post('nominee_salutation', true);
			$get_lead_id = $this->db->query("select lead_id,imd_code from employee_details where emp_id = '$emp_id' ")->row_array();
			if($get_lead_id['lead_id'] != 0)
			{
				$lead_id = $get_lead_id['lead_id'];
				$product_id = 'R06';
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
			$this->db->trans_start();
		
			//$family_construct1 = explode("+", $family_construct);
			$policies = $this->db->select("ed.policy_detail_id,p.combo_flag,p.policy_parent_id")
						->from("product_master_with_subtype AS p,employee_policy_detail AS ed")
						->where("ed.product_name = p.id")->where("p.policy_parent_id", $parent_id)
						->order_by("ed.policy_sub_type_id")->get()->result_array();
			
			   $gmc_id = "";
			   $q = 0;
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
		$check_proposal = $this->db->query("select emp_id from proposal where emp_id = '$emp_id'")->row_array();

		if(count($check_proposal)>0)
		{
			$data['status'] = 'Yes'; 
		}
		else
		{$data['status'] = 'No';}
		echo json_encode($data);
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
							$emp_data_insert = array(

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
		
			if($emp_data == true)
			{	
				//Add Data into logs Table							
				$logs_array['data'] = ["type" => "insert_agent_details","req" => json_encode($emp_data_insert), "lead_id" => $lead_id,"product_id" => $product_id];
				$this->Logs_m->insertLogs($logs_array);
			}
			$insert_data= ["status" => true, "message" => ''];
             print_r(json_encode($insert_data));  
		}
	
	}
		
	public function tele_nominee_data_insert()
	{
		$this->form_validation->set_error_delimiters('','');
		$this->form_validation->set_rules('nominee_relation', 'Nominee Relation', 'required|trim');
		$this->form_validation->set_rules('nominee_fname', 'Nominee First Name', 'required|trim');
		$this->form_validation->set_rules('nominee_lname', 'Nominee Last Name', 'required|trim');
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
			$get_lead_id = $this->db->query("select lead_id from employee_details where emp_id = '$emp_id' ")->row_array();
			if($get_lead_id['lead_id'] != 0)
			{
				$lead_id = $get_lead_id['lead_id'];
				$product_id = 'R06';
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
									'emp_id'=>$emp_id
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
		

		$data = $this->obj_home->get_common_data();		
		$data['agent_details'] = $this->obj_home->get_agent_details();
		$data['employee_declaration'] = $this->obj_home->health_declaration_emp_data($this->parent_id);
		$data['policy_details'] = $this->obj_home->get_policy_data_emp($this->parent_id);
		$data['redirectFrom_email'] = 'No';
		$emp_id_encrypt = encrypt_decrypt_password($this->emp_id,'E');
		$data['emp_id'] = $emp_id_encrypt;
		$data['disposition']  = $this->db->select('*')
         ->from('employee_disposition ed')
         ->join('disposition_master dm', 'ed.disposition_id = dm.id')
		 ->where('emp_id',$this->emp_id)
		 ->order_by('ed.id','desc')
		->get()
		
		->row_array();
		//print_pre($data);exit;
		$string = $this->load->telesales_template("summary",compact('data'),true);
	}
}
