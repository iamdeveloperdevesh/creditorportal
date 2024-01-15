<?php

class Proposal_summary extends CI_Controller {
	  function __construct() {
        parent::__construct();
  
		$telSalesSession = $this->session->userdata('telesales_session');	
		$this->parent_id = $telSalesSession['parent_id'];
		$this->emp_id = $telSalesSession['emp_id'];
    }
		public function redirect_summary($emp_id_url,$encrypt_date,$product_id)
	{ 

	$date = encrypt_decrypt_password($encrypt_date,"D");

	if($emp_id_url)
		{
			$emp_id_decrypt = encrypt_decrypt_password($emp_id_url,'D');
			
			$query = $this->db->query("SELECT status,modified_date FROM proposal where emp_id = '".$emp_id_decrypt."'")->row_array();

			$is_makerchecker_journey=$this->db->query("SELECT is_makerchecker_journey FROM employee_details where emp_id = '".$emp_id_decrypt."'")->row_array();
			
			$start_date = strtotime($date);
			$end_date = strtotime(date('Y-m-d H:i:s'));
			$date_diff = ($end_date - $start_date)/60/60/24;
			
			$link_expire_check = 1;
			if(!empty($query['modified_date'])){
				$link_expire_check = ($start_date >= strtotime($query['modified_date']))?1:0;
			}
			// echo strtotime($query['modified_date']);
// echo $link_expire_check;
// echo $start_date;
			if($date_diff < 5 && $query['status'] != 'Rejected' && $link_expire_check)
			{
			  
			  if($product_id == 'T01'){
				  
				  $parent_id = 'test123';
				 
				  $agent_id = $this->db->select('tlm.id')
                ->from('employee_details l')
				->join('tls_agent_mst tlm', 'l.av_code = tlm.agent_id')
                ->where('l.emp_id', $emp_id_decrypt)
                ->get()
                ->row_array();

				if($is_makerchecker_journey['is_makerchecker_journey']=='yes'){
					$agent_id = $this->db->select('tlm.base_id')
					->from('employee_details l')
					->join('tls_base_agent_tbl tlm', 'l.assigned_to = tlm.base_id')
					->where('l.emp_id', $emp_id_decrypt)
					->get()
					->row_array();
	
				}
				
				 $agent_id = encrypt_decrypt_password($agent_id['id'],'E');

				  
			  }else{
			  $parent_id = $this->db->select('p.policy_parent_id,tlm.id')
                ->from('employee_details l')
                ->join('product_master_with_subtype p', 'l.product_id = p.product_code')
				->join('tls_agent_mst tlm', 'l.av_code = tlm.agent_id')
                ->where('l.emp_id', $emp_id_decrypt)
                ->get()
                ->row_array();

				if($is_makerchecker_journey['is_makerchecker_journey']=='yes'){

					$parent_id=$this->db->query("SELECT l.is_makerchecker_journey,p.policy_parent_id, tlm.base_id FROM employee_details l JOIN product_master_with_subtype p ON l.product_id = p.product_code JOIN tls_base_agent_tbl tlm ON l.assigned_to = tlm.base_id WHERE  l.emp_id = '".$emp_id_decrypt."'")->row_array();				
}			

				$parent_id = $parent_id['policy_parent_id'];
				$agent_id = encrypt_decrypt_password($parent_id['id'],'E');
			  }
             $telSalesSession['telesales_session'] = ['emp_id' => $emp_id_decrypt,'parent_id' => $parent_id,'agent_id'=> ($agent_id)];			
			 $this->session->set_userdata($telSalesSession);
			 $telSalesSession = $this->session->userdata('telesales_session');
				//print_pre($telSalesSession);exit;
			 redirect(base_url('tele_customer_summary'));
			 
			}else{
				echo "Payment link has been expired .Please call Axis Bank Helpline 18604195555";
			}
		}	
	 	
		
	}
	
	public function customer_summary()
	{
		
		$this->load->model("Telesales/create_proposal_m", "obj_home", true);
		$data = $this->obj_home->get_common_data();		
		$data['agent_details'] = $this->obj_home->get_agent_details();
		$data['employee_declaration'] = $this->obj_home->health_declaration_emp_data($this->parent_id);
//print_pre($data);echo $this->parent_id;exit;
		$data['policy_details'] = $this->obj_home->get_policy_data_emp($this->parent_id);
		$data['redirectFrom_email'] = 'Yes';
		$emp_id_encrypt = encrypt_decrypt_password($this->emp_id,'E');
		$data['emp_id_encrypt'] = $emp_id_encrypt;
		$data['parent_id'] = $this->parent_id;
		$data['sum_insured_data'] = $this->db->query("select  sum(epm.policy_mem_sum_premium) as premium,epm.policy_mem_sum_insured,epm.familyConstruct  from employee_policy_member as epm,family_relation as fr where fr.family_relation_id = epm.family_relation_id AND fr.emp_id  = '".$this->emp_id."' AND epm.fr_id = 0 group by fr.emp_id")->row_array();
	        
                //check dedupe logic - 26 may 21
		$lead_id = $data['emp_details']['lead_id'];
		$check_dedupe = common_function_ref_id_exist($lead_id);
        	if($check_dedupe['status'] == 'error'){
            		echo $check_dedupe['msg']; exit;
        	}                
		
		//check disposition status ankita junk dedupe changes
        $q = "SELECT ed.id,dm.Dispositions from employee_disposition ed,disposition_master dm where ed.disposition_id = dm.id AND ed.emp_id = '".$this->emp_id."' order by id desc";
        $resDisposition = $this->db->query($q)->row_array();
        if(!empty($resDisposition) && ($resDisposition['Dispositions'] == "Junk" || $resDisposition['Dispositions'] == "Not Interested" || $resDisposition['Dispositions'] == "Not Eligible")){
        	echo "Payment link is expired this lead is marked as ".$resDisposition['Dispositions']." by agent !";exit;
        }
	
		$string = $this->load->telesales_template("summary",compact('data'),true);
	}
}
