<?PHP
class Apimodel extends CI_Model
{
    
    function getInsurerList($post)
	{
		$table = "master_insurer";
		$table_id = 'insurer_id ';
		$default_sort_column = 'insurer_id ';
		$default_sort_order = 'desc';
		$condition = "1=1";
		
		
		$colArray = array('i.insurer_name');
		$sortArray = array('i.insurer_name','i.insurer_code','i.isactive');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		$sort = isset($post['iSortCol_0']) ? strval($sortArray[$post['iSortCol_0']]) : $default_sort_column;  
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<1;$i++)
		{
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('*');
		$this -> db -> from('master_insurer as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('*');
		$this -> db -> from('master_insurer as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	function getInsurerFormData($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('master_insurer as i');
		$this -> db -> where('i.insurer_id', $ID);
	
		$query = $this -> db -> get();
	   
		//print_r($this->db->last_query());
		//exit;
	   
		if($query -> num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}	
	}
	function getSuminsuredList($post)
	{
		$table = "master_suminsured_type";
		$table_id = 'suminsured_type_id ';
		$default_sort_column = 'suminsured_type_id ';
		$default_sort_order = 'desc';
		$condition = "1=1";
		
		$colArray = array('i.suminsured_type');
		$sortArray = array('i.suminsured_type','i.isactive');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		$sort = isset($post['iSortCol_0']) ? strval($sortArray[$post['iSortCol_0']]) : $default_sort_column;  
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<1;$i++)
		{
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('*');
		$this -> db -> from('master_suminsured_type as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('*');
		$this -> db -> from('master_suminsured_type as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	function getSuminsuredFormData($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('master_suminsured_type as i');
		$this -> db -> where('i.suminsured_type_id', $ID);
	
		$query = $this -> db -> get();
	   
		//print_r($this->db->last_query());
		//exit;
	   
		if($query -> num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}	
	}
    function getPolicySubTypeList($post)
	{
		$table = "master_policy_sub_type";
		$table_id = 'policy_sub_type_id ';
		$default_sort_column = 'policy_sub_type_id ';
		$default_sort_order = 'desc';
		$condition = "1=1";
		
		$colArray = array('i.policy_sub_type_name');
		$sortArray = array('i.policy_sub_type_name','i.policy_type_id','i.isactive');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		$sort = isset($post['iSortCol_0']) ? strval($sortArray[$post['iSortCol_0']]) : $default_sort_column;  
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<1;$i++)
		{
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
		}
		$condition .= " AND ij.isactive = 1";
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.*,ij.policy_type_id as typeid,ij.policy_type_name as typename');
		$this -> db -> from('master_policy_sub_type as i');
		$this -> db -> join('master_policy_type as ij','i.policy_type_id = ij.policy_type_id');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.*,ij.policy_type_id as typeid,ij.policy_type_name as typename');
		$this -> db -> from('master_policy_sub_type as i');
		$this -> db -> join('master_policy_type as ij','i.policy_type_id = ij.policy_type_id');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	
	function getFamilyConstructList($post)
	{
		$table = "family_construct";
		$table_id = 'id ';
		$default_sort_column = 'id ';
		$default_sort_order = 'desc';
		$condition = "1=1";
		
		$colArray = array('i.member_type');
		$sortArray = array('i.member_type');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		$sort = isset($post['iSortCol_0']) ? strval($sortArray[$post['iSortCol_0']]) : $default_sort_column;  
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<1;$i++)
		{
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.*');
		$this -> db -> from('family_construct as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.*');
		$this -> db -> from('family_construct as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	function getProductsList($post)
	{
		$table = "master_plan";
		$table_id = 'plan_id ';
		$default_sort_column = 'plan_id ';
		$default_sort_order = 'desc';
		$condition = "1=1";
		
		$colArray = array('i.plan_name');
		$sortArray = array('i.plan_name','i.policy_type_id','i.creditor_id','i.isactive');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		$sort = isset($post['iSortCol_0']) ? strval($sortArray[$post['iSortCol_0']]) : $default_sort_column;  
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<1;$i++)
		{
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.*,ij.creaditor_name as creditorname,ik.policy_type_name as policytype');
		$this -> db -> from('master_plan as i');
		$this -> db -> join('master_ceditors as ij','i.creditor_id = ij.creditor_id');
		$this -> db -> join('master_policy_type as ik','i.policy_type_id = ik.policy_type_id');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.*,ij.creaditor_name as creditorname,ik.policy_type_name as policytype');
		$this -> db -> from('master_plan as i');
		$this -> db -> join('master_ceditors as ij','i.creditor_id = ij.creditor_id');
		$this -> db -> join('master_policy_type as ik','i.policy_type_id = ik.policy_type_id');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		}
		else
		{
			return array("totalRecords" => 0);
		}
	}
	function getPolicySubTypeFormData($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('master_policy_sub_type as i');
		$this -> db -> where('i.policy_sub_type_id', $ID);
	
		$query = $this -> db -> get();
	   
		//print_r($this->db->last_query());
		//exit;
	   
		if($query -> num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}	
	}
	function getFamilyConstructFormData($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('family_construct as i');
		$this -> db -> where('i.id', $ID);
	
		$query = $this -> db -> get();
	   
		//print_r($this->db->last_query());
		//exit;
	   
		if($query -> num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}	
	}
	function getInsurer(){
        $data = $this->db->get_where('master_insurer',array('isactive'=>1))->result();
        return $data;
    }
    function getCreditors(){
        $data = $this->db->get_where('master_ceditors',array('isactive'=>1))->result();
        return $data;
    }
    function getPaymentModes(){
        $data = $this->db->get_where('payment_modes',array('isactive'=>1))->result();
        return $data;
    }
    function getPolicyType(){
        $data = $this->db->get_where('master_policy_type',array('isactive'=>1))->result();
        return $data;
    }
    function getPolicySubType(){
        $data = $this->db->get_where('master_policy_sub_type',array('isactive'=>1))->result();
        return $data;
    }
    function getSiType(){
        $data = $this->db->get_where('master_suminsured_type',array('isactive'=>1))->result();
        return $data;
    }
    function checkplanname($name,$id=null){
        if(!empty($id)){
            $this->db->where(array('plan_id !='=>$id));
        }
        $plan = $this->db->get_where('master_plan',array('plan_name'=>$name))->result();
        return $plan;
    }
    function checkpolicynumber($name,$id=null){
        if(!empty($id)){
            $this->db->where(array('policy_id !='=>$id));
        }
        $plan = $this->db->get_where('master_policy',array('policy_number'=>$name))->result();
        return $plan;
    }
    function getPlanPayments($id){
        $this->db->select('plan_payment_mode.*,payment_modes.payment_mode_name');
        $this->db->join('payment_modes','plan_payment_mode.payment_mode_id = payment_modes.payment_mode_id');
        $plan = $this->db->get_where('plan_payment_mode',array('master_plan_id'=>$id))->result();
        return $plan;
    }
    function getProductDetails($id,$policy_id=null){
        $this->db->select('mp.*,master_plan.creditor_id,master_plan.plan_name,mpst.policy_sub_type_name,mc.creaditor_name as creditor_name');
        $this->db->where('mp.plan_id',$id);
        if(!empty($policy_id)){
            $this->db->where('mp.policy_id',$policy_id);
        }
        $this->db->where('mp.isactive',1);
        $this->db->join('master_plan','mp.plan_id = master_plan.plan_id');
        $this->db->join('master_ceditors as mc','mc.creditor_id = master_plan.creditor_id');
        $this->db->join('master_policy_sub_type as mpst','mpst.policy_sub_type_id = mp.policy_sub_type_id');
        $data = $this->db->get('master_policy as mp')->result();
        return $data;
    }
    
    function getProductDetailsAll($id){
        $this->db->select('mp.*,master_plan.creditor_id,master_plan.plan_name,mpst.policy_sub_type_name,mpsi.suminsured_type_id as sitype_id,mppb.si_premium_basis_id as basis_id,mc.creaditor_name as creditor_name');
        $this->db->where('mp.plan_id',$id);
        $this->db->where('mp.isactive',1);
        $this->db->where('mpsi.isactive',1);
        $this->db->where('mppb.isactive',1);
        $this->db->join('master_plan','mp.plan_id = master_plan.plan_id');
        $this->db->join('master_ceditors as mc','mc.creditor_id = master_plan.creditor_id');
        $this->db->join('master_policy_sub_type as mpst','mpst.policy_sub_type_id = mp.policy_sub_type_id');
        $this->db->join('master_policy_premium_basis_mapping as mppb','mppb.master_policy_id = mp.policy_id');
        $this->db->join('master_policy_si_type_mapping as mpsi','mpsi.master_policy_id = mp.policy_id');
        $data = $this->db->get('master_policy as mp')->result();
        return $data;
    }
    function getPolicyDetails($id){
        $this->db->select('mp.*');
        $this->db->where('mp.policy_id',$id);
        $data = $this->db->get('master_policy as mp')->result();
        return $data;
    }
    function getSiPremiumBasis(){
        $data = $this->db->get_where('master_si_premium_basis',array('isactive'=>1))->result();
        return $data;
    }
	function getproposalpolicybylead($leads){
		$this->db->select('*');
		$this->db->where_in('lead_id',$leads);
		$data = $this->db->get('proposal_policy')->result();
		return $data;
	}
    function getMembers(){
        $data = $this->db->get_where('family_construct',array('isactive'=>1))->result();
        return $data;
    }
    function getpolicysubtypeofplan($plan){
        $this->db->select('policy_sub_type_id');
        $data = $this->db->get_where('master_policy',array('plan_id'=>$plan))->result();
        return $data;
    }
    function getpaymentofplan($plan){
        $this->db->select('payment_mode_id');
        $data = $this->db->get_where('plan_payment_mode',array('master_plan_id'=>$plan))->result();
        return $data;
    }
    function deletemasterpolicy($id,$plan){
        $this->db->where('policy_sub_type_id',$id);
        $this->db->where('plan_id',$plan);
        $this->db->update('master_policy',array('isactive'=>0));
    }
    function deletepolicypayment($plan){
        $this->db->where('master_plan_id',$plan);
        $this->db->update('plan_payment_mode',array('isactive'=>0));
    }
    function deletempfc($id){
        $this->db->where('master_policy_id',$id);
        $this->db->update('master_policy_family_construct',array('isactive'=>0));
    }
    function deletemppb($id){
        $this->db->where('master_policy_id',$id);
        $this->db->update('master_policy_premium_basis_mapping',array('isactive'=>0));
    }
    function deletempp($id){
        $this->db->where('master_policy_id',$id);
        $this->db->update('master_policy_premium',array('isactive'=>0));
    }
    function deletempsi($id){
        $this->db->where('master_policy_id',$id);
        $this->db->update('master_policy_si_type_mapping',array('isactive'=>0));
    }
    function updatepolicypayment($plan,$id){
        $this->db->where('master_plan_id',$plan);
        $this->db->where('payment_mode_id',$id);
        $this->db->update('plan_payment_mode',array('isactive'=>1));
    }
    function getLeadDetails($id){
        $this->db->select('lead_details.*,mc.customer_id,mc.salutation,mc.first_name,mc.middle_name,mc.last_name,mc.gender,mc.dob,mc.email_id,mc.mobile_no,mc.city,mc.state,mc.address_line1,mc.address_line2,mc.address_line3,mc.pincode,mc.mobile_no2');
        $this->db->join('master_customer as mc','mc.lead_id = lead_details.lead_id');
        $data = $this->db->get_where('lead_details',array('lead_details.lead_id'=>$id))->result();
        return $data;
    }
	function getProposalPolicy($id){
        $data = $this->db->get_where('proposal_policy',array('lead_id'=>$id))->result();
        return $data;
    }
	function getProposalPolicy1($id){
        $data = $this->db->get_where('proposal_policy',array('proposal_details_id'=>$id))->result();
        return $data;
    }
    function getPolicyPremium($id){
        $data = $this->db->get_where('master_policy_premium',array('master_policy_id'=>$id,'isactive'=>1))->result();
        return $data;
    }
	function getProposalPolicyMember($id){
        $data = $this->db->get_where('proposal_policy_member',array('policy_id'=>$id))->result();
        return $data;
    }
    function getPlanProposal($id,$lead_id){
        $data = $this->db->get_where('proposal_details',array('plan_id'=>$id,'lead_id'=>$lead_id))->result();
        return $data;
    }
    function getPolicyPremiumBasis($id){
        $data = $this->db->get_where('master_policy_premium_basis_mapping',array('master_policy_id'=>$id,'isactive'=>1))->result();
        return $data;
    }
    function getPolicySiType($id){
        $data = $this->db->get_where('master_policy_si_type_mapping',array('master_policy_id'=>$id,'isactive'=>1))->result();
        return $data;
    }
    function getPolicyFamilyConstruct($id){
        $data = $this->db->get_where('master_policy_family_construct',array('master_policy_id'=>$id,'isactive'=>1))->result();
        return $data;
    }
	function getsubtypenamebyid($id){
        $data = $this->db->get_where('master_policy_sub_type',array('policy_sub_type_id'=>$id))->row()->policy_sub_type_name;
        return $data;
    }
	function getPolicyDeclaration($id){
        $data = $this->db->get_where('ghd_declaration',array('plan_id'=>$id,'is_active'=>1))->row();
        return $data;
    }
	function getAssignmentDeclaration($id){
        $data = $this->db->get_where('assignment_declaration',array('plan_id'=>$id,'is_active'=>1))->row();
        return $data;
    }
    function checkleadproposal($id){
        $data = $this->db->get_where('proposal_details',array('proposal_details_id'=>$id))->num_rows();
        return $data;
    }
	function checkproposalpolicy($id,$policy,$details_id){
        $data = $this->db->get_where('proposal_policy',array('lead_id'=>$id,'master_policy_id'=>$policy,'proposal_details_id'=>$details_id))->result();
        return $data; 
    }
    function inactivateRecord($table,$cond){
        $this->db->where($cond);
        $this->db->update($table,array('isactive'=>0));
        return 1;
    }
	function insertData($tbl_name,$data_array,$sendid = NULL)
	{
	 	$this->db->insert($tbl_name,$data_array);
	 	$result_id = $this->db->insert_id();
	 	
	 	/*echo $result_id;
	 	exit;*/
	 	
	 	if($sendid == 1)
	 	{
	 		//return id
	 		return $result_id;
	 	}
	}
	
	/*
		 * Fetch records from multiple tables [Join Queries] with multiple condition, Sorting, Limit, Group By
	*/
	function getdata_join($main_table = array(), $join_tables = array(), $condition = null, $sort_by = null, $group_by = null) {
		$columns = isset($main_table[1]) ? $main_table[1] : array();
		$main_table = $main_table[0];

		$join_str = "";
		foreach ($join_tables as $join_table) {
			$join_str .= $join_table[0] . " join " . $join_table[1] . " on (" . $join_table[2] . ") ";
			if (isset($join_table[3])) {
				$columns = array_merge($columns, $join_table[3]);
			}
		}

		$columns = (sizeof($columns) > 0) ? implode(", ", $columns) : "*";

		if (is_null($condition) || $condition == "") {
			$condition = "1=1";
		}

		$sort_order = "";
		if (is_array($sort_by) && $sort_by != null) {
			foreach ($sort_by as $key => $val) {
				$sort_order .= ($sort_order == "") ? "order by $key $val" : ", $key $val";
			}
		}

		if ($group_by != null) {
			$group_by = "group by " . $group_by;
		}

		//$this->db->query($this->set_timezone_query);
		$sql = trim("select $columns from $main_table $join_str where $condition $group_by $sort_order");
		// echo $sql.'<br/><br/><br/>';
		// exit;
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	// get quick quote call
   public function get_quote_data($lead_id, $emp_id,$master_policy_id, $proposal_policy_id, $proposal_details, $policy_sub_type_id, $sum_insured,$count=1){ 
		
		//get proposal policy details
		$data = array();
		$data['customer_data'] = (array)$this->get_profile($emp_id);
		$data['member_data'] = (array)$this->get_all_member_data($proposal_policy_id);
		
		$nominees = array();
		if(!empty($proposal_details)){
			$nominees['nominee_relation'] = $proposal_details[0]['nominee_relation'];
			$nominees['nominee_first_name'] = $proposal_details[0]['nominee_first_name'];
			$nominees['nominee_last_name'] = $proposal_details[0]['nominee_last_name'];
			$nominees['nominee_salutation'] = $proposal_details[0]['nominee_salutation'];
			$nominees['nominee_gender'] = $proposal_details[0]['nominee_gender'];
			$nominees['nominee_dob'] = $proposal_details[0]['nominee_dob'];
			$nominees['nominee_contact'] = $proposal_details[0]['nominee_contact'];
			$nominees['nominee_email'] = $proposal_details[0]['nominee_email'];
		}
		$data['nominee_data'] = $nominees;
		$data['proposal_data'] = $proposal_details;
		
		
		
		//echo "<pre>";print_r($data);exit;
		
		//get Api URL with policy_sub_type_id
		$getApiUrl = $this->db->query("select api_url from master_policy_sub_type where policy_sub_type_id='".$policy_sub_type_id."' ")->row_array();
		
		$url = trim($getApiUrl['api_url']);
		
		if($url == ''){
			return array(
				"status" => "error",
				"msg" => "Something Went Wrong"
			);
			exit;
		}
		 
		//echo $url;exit;
		
		$occupation = '';
		
		//Data Variables
		//$Member_Customer_ID = $data['customer_data']['customer_id'];
		$Member_Customer_ID = 10000000000004;
		$uidNo = (!empty($data['customer_data']['adhar'])) ? $data['customer_data']['adhar'] : null;
		
		$MasterPolicyNumber = '61-20-00040-00-00';
		$GroupID = 'GRP001';
		//$PlanCode = '4211';
		//$plan_code = (!empty($data['proposal_data']['plan_code'])) ? $data['proposal_data']['plan_code'] : null;
		$plan_code = '4211';
		$Product_Code = '4211';
		
		$Member_Type_Code = '';
		$intermediaryCode = '2108233';
		$intermediaryBranchCode = '10MHMUM01';
		$SchemeCode = '4112000003';
		
		//get SumInsured Type
		$sumins_type = $this->db->query("select mi.suminsured_type from master_policy_si_type_mapping as m left join master_suminsured_type as mi ON m.suminsured_type_id = mi.suminsured_type_id where m.master_policy_id='$master_policy_id' ")->row();
		
		//echo "<pre>";print_r($sumins_type);exit;
		$SumInsured_Type = $sumins_type->suminsured_type;
		//echo $SumInsured_Type;exit;
		
		
		
		$leadID = $lead_id;
		//$SumInsured = $sum_insured;
		$SumInsured = 500000;
		$product_id = $data['proposal_data'][0]['plan_id'];
		
		
		$SPID = 0;
		$RefCode1 = 0;
		$RefCode2 = 0;
		$Policy_Tanure = 1;
		$AutoRenewal = 'Y';
		//$intermediaryBranchCode = "10MHMUM01";
		
		$totalMembers = count($data['member_data']);
		$member = [];
		
		$explode_name = array($data['customer_data']['first_name'],$data['customer_data']['middle_name'],$data['customer_data']['last_name']);
		$explode_name_nominee = array($data['nominee_data']['nominee_first_name'],$data['nominee_data']['nominee_last_name']);
		
		for ($i = 0; $i < $totalMembers; $i++){
			//Checking relation based on self, spouse, son and daughter
			if($data['member_data'][$i]['relation_with_proposal'] == 1 ){
				$data['member_data'][$i]['relation_code'] = "R001";
			}else if($data['member_data'][$i]['relation_with_proposal'] == 2 ){
				$data['member_data'][$i]['relation_code'] = "R002";
			}else if($data['member_data'][$i]['relation_with_proposal'] == 3 ){
				$data['member_data'][$i]['relation_code'] = "R003";
			}else{
				$data['member_data'][$i]['relation_code'] = "R004";
			}
			
			//Nominee relation code
			if($data['nominee_data']['nominee_relation'] == 1 ){
				$data['nominee_data']['relation_code'] = "R001";
			}else if($data['nominee_data']['nominee_relation'] == 2 ){
				$data['nominee_data']['relation_code'] = "R002";
			}else if($data['nominee_data']['nominee_relation'] == 3 ){
				$data['nominee_data']['relation_code'] = "R003";
			}else{
				$data['nominee_data']['relation_code'] = "R004";
			}
			
			
			$abc = ["PEDCode" => null, "Remarks" => null];
			
			$explode_name_member = explode(" ", trim($data['member_data'][$i]['policy_member_first_name']),2);
		 
		
			$member[] = ["MemberNo" => $i + 1, "Salutation" => (($data['member_data'][$i]['policy_member_gender'] == "Male") ? 'Mr' : (($data['member_data'][$i]['policy_member_age'] >= 18) ? 'Mrs' : 'Ms')) , "First_Name" => $data['member_data'][$i]['policy_member_first_name'], "Middle_Name" => null, "Last_Name" => !empty($data['member_data'][$i]['policy_member_last_name']) ? $data['member_data'][$i]['policy_member_last_name'] : '.', "Gender" => ($data['member_data'][$i]['policy_member_gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['policy_member_dob'])) , "Relation_Code" => trim($data['member_data'][$i]['relation_code']), "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => "O553", "PrimaryMember" => (($i == 0) ? "Y" : "N") , "MemberproductComponents" => [["PlanCode" => $plan_code, "MemberQuestionDetails" => [["QuestionCode" => null, "Answer" => null, "Remarks" => null]]]], "MemberPED" => $abc, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $explode_name_nominee[0], "Nominee_Last_Name" => !empty($explode_name_nominee[1]) ? $explode_name_nominee[1] : '.', "Nominee_Contact_Number" => (!empty($data['nominee_data']['nominee_contact'])) ? $data['nominee_data']['nominee_contact'] : null, "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data']['relation_code']), ];

		}
		
		//echo "<pre>";print_r($member);exit;
		$data['proposal_data']['SourceSystemName_api'] = 'CreditorPortal';
		
		
		//echo $policy_detail_id;exit;
		if($policy_sub_type_id == 1){ // ghi
			$fqrequest = ["ClientCreation" => ["Member_Customer_ID" =>  $Member_Customer_ID , "salutation" => $data['customer_data']['salutation'], "firstName" => $explode_name[0], "middleName" => "", "lastName" => !empty($explode_name[1]) ? $explode_name[1] : '.', "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['dob'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['pincode'], "uidNo" => $uidNo, "maritalStatus" => null, "nationality" => "Indian", "occupation" => $occupation, "primaryEmailID" => $data['customer_data']['email_id'], "contactMobileNo" => substr(trim($data['customer_data']['mobile_no']), -10), "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => null, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address_line1'], "homeAddressLine2" => null, "homeAddressLine3" => null, "homePinCode" => $data['customer_data']['pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => "", "MasterPolicyNumber" => $MasterPolicyNumber, "GroupID" => $GroupID, "Product_Code" => $Product_Code, "intermediaryCode" => $intermediaryCode, "AutoRenewal" => $AutoRenewal, "intermediaryBranchCode" => $intermediaryBranchCode, "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $leadID, "Source_Name" => $data['proposal_data']['SourceSystemName_api'], "SPID" => $SPID, "TCN" => null, "CRTNO" => null, "RefCode1" => $RefCode1, "RefCode2" => $RefCode2, "Employee_Number" => $data['customer_data']['customer_id'],"enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "0", "PaymentMode" => "", "PolicyproductComponents" => [["PlanCode" => $plan_code, "SumInsured" => $SumInsured, "SchemeCode" => $SchemeCode]]], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "", "modeOfEntry" => "", "cdAcNo" => null, "expiryDate" => null, "payerType" => "", "payerCode" => null, "paymentBy" => "", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => "", "collectionRcvdDate" => null, "collectionMode" => "", "remarks" => null, "instrumentNumber" => null, "instrumentDate" => null, "bankName" => null, "branchName" => null, "bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => "", "PaymentGatewayName" => "", "TerminalID" => "", "CardNo" => null]];
			
		}else{
			//group policies
			$fqrequest = ["ClientCreation" => ["Member_Customer_ID" => $Member_Customer_ID, "salutation" => $data['customer_data']['salutation'], "firstName" => $explode_name[0], "middleName" => "", "lastName" => !empty(trim($explode_name[1]))?$explode_name[1]:'.', "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['dob'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['pincode'], "uidNo" => $uidNo, "maritalStatus" => null, "nationality" => "Indian", "occupation" => $occupation, "primaryEmailID" => $data['customer_data']['email_id'], "contactMobileNo" => substr(trim($data['customer_data']['mobile_no']), -10), "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => null, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address_line1'], "homeAddressLine2" => null, "homeAddressLine3" => null, "homePinCode" => $data['customer_data']['pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => "", "MasterPolicyNumber" => $MasterPolicyNumber, "GroupID" => $GroupID, "Product_Code" => $Product_Code,"SumInsured_Type"=> $SumInsured_Type,"Policy_Tanure"=> $Policy_Tanure,"Member_Type_Code"=> $Member_Type_Code, "intermediaryCode" => $intermediaryCode, "AutoRenewal" => $AutoRenewal, "intermediaryBranchCode" => $intermediaryBranchCode, "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $leadID, "Source_Name" => $data['proposal_data']['SourceSystemName_api'], "SPID" => $SPID, "TCN" => null, "CRTNO" => null, "RefCode1" => $RefCode1, "RefCode2" => $RefCode2, "Employee_Number" => $data['customer_data']['customer_id'],"enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "0", "PaymentMode" => null, "PolicyproductComponents" => ["PlanCode" => $plan_code, "SumInsured" => $SumInsured, "SchemeCode" => $SchemeCode] ], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "", "modeOfEntry" => "", "cdAcNo" => null, "expiryDate" => null, "payerType" => "", "payerCode" => null, "paymentBy" => "", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => "", "collectionRcvdDate" => null, "collectionMode" => "", "remarks" => null, "instrumentNumber" => null, "instrumentDate" => null, "bankName" => null, "branchName" => null, "bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => "", "PaymentGatewayName" => "", "TerminalID" => "", "CardNo" => null]];
		}
		
		//echo "<pre>";print_r($fqrequest);exit;

		$request_arr = ["lead_id" => $leadID, "req" => json_encode($fqrequest) ,"product_id" => $product_id, "type"=>"quick_quote_request", "proposal_policy_id"=> $proposal_policy_id];
	
		$this->db->insert("logs_docs",$request_arr);
		$insert_id = $this->db->insert_id();

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($fqrequest) ,
			CURLOPT_HTTPHEADER => array(
				"Accept: */*",
				"Cache-Control: no-cache",
				"Connection: keep-alive",
				"Content-Length: " . strlen(json_encode($fqrequest)) ,
				"Content-Type: application/json",
				"Host: bizpre.adityabirlahealth.com"
			) ,
		));


		$response = curl_exec($curl);
		
		$err = curl_error($curl);
		if($response == '' || $response == NULL){
			$response = $err;
		}
		
		//echo "<pre>";print_r($response);exit;
		
		$request_arr = ["res" => json_encode($response)];
		$this->db->where("id",$insert_id);
		$this->db->update("logs_docs",$request_arr);

		curl_close($curl);

		if ($err){
			return array(
				"status" => "error",
				"msg" => $err
			);
			if($count <= 3){
				sleep(15);
				$this->get_quote_data($lead_id, $emp_id, $master_policy_id, $proposal_policy_id, $proposal_details, $policy_sub_type_id, $sum_insured,$count++);
			}
		}else{
			
			$new = simplexml_load_string($response);
			$con = json_encode($new);
			$newArr = json_decode($con, true);
			$errorObj = $newArr['errorObj'];
			//print_pre($errorObj);exit;
			if($errorObj['ErrorNumber'] == '00'){
			  $policydetail = $newArr['policyDtls'];
			  
			  $arr = ["emp_id" => $emp_id, "lead_id" => $lead_id,"proposal_policy_id" => $proposal_policy_id,"policy_subtype_id" =>$policy_sub_type_id, "QuotationNumber" => $policydetail['QuotationNumber'], "PolicyNumber" => $policydetail['PolicyNumber'],"status"=>"success"];
			  
			  $query = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' AND proposal_policy_id='$proposal_policy_id'")->row_array();
				
				if($query > 0){
					$this->db->where("emp_id",$emp_id);
					$this->db->where("lead_id",$lead_id);
					$this->db->where("proposal_policy_id",$proposal_policy_id);
					
					$this->db->update("ghi_quick_quote_response",$arr);
				}else{
					$this->db->insert("ghi_quick_quote_response", $arr);
				}
			 
			  
				return array(
					"status" => "Success",
					"msg" => $policydetail['QuotationNumber']
				);
				
			}else{
			  
				$query = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' AND proposal_policy_id='$proposal_policy_id'")->row_array();
				if($query > 0){
					$arr = ["count" => $query['count'] + 1,"status"=>"error"];
					$this->db->where("emp_id",$emp_id);
					$this->db->where("lead_id",$lead_id);
					$this->db->where("proposal_policy_id",$proposal_policy_id);
					$this->db->update("ghi_quick_quote_response",$arr);
				}else{
					$arr = ["emp_id" => $emp_id,"status"=>"error"];
					$this->db->insert("ghi_quick_quote_response", $arr);
				}
			  
				return array(
					"status" => "error",
					"msg" => $errorObj['ErrorMessage']
				);
				if($count <= 3){
					sleep(15);
					$this->get_quote_data($lead_id, $emp_id,$master_policy_id, $proposal_policy_id, $proposal_details, $policy_sub_type_id, $sum_insured,$count++);
				}
			}
		}
	}
	
	
	//Full Quote Request
	public function get_full_quote_data($lead_id, $emp_id,$master_policy_id, $proposal_policy_id, $proposal_details, $policy_sub_type_id, $sum_insured){ 

		//get proposal policy details
		$data = array();
		$data['customer_data'] = (array)$this->get_profile($emp_id);
		$data['member_data'] = (array)$this->get_all_member_data($proposal_policy_id);
		$nominees = array();
		if(!empty($proposal_details)){
			$nominees['nominee_relation'] = $proposal_details[0]['nominee_relation'];
			$nominees['nominee_first_name'] = $proposal_details[0]['nominee_first_name'];
			$nominees['nominee_last_name'] = $proposal_details[0]['nominee_last_name'];
			$nominees['nominee_salutation'] = $proposal_details[0]['nominee_salutation'];
			$nominees['nominee_gender'] = $proposal_details[0]['nominee_gender'];
			$nominees['nominee_dob'] = $proposal_details[0]['nominee_dob'];
			$nominees['nominee_contact'] = $proposal_details[0]['nominee_contact'];
			$nominees['nominee_email'] = $proposal_details[0]['nominee_email'];
		}
		$data['nominee_data'] = $nominees;
		$data['proposal_data'] = $proposal_details;
		// print_pre($data);exit;
		//get Api URL with policy_sub_type_id
		$getApiUrl = $this->db->query("select api_url from master_policy_sub_type where policy_sub_type_id='".$policy_sub_type_id."' ")->row_array();
		
		$url = trim($getApiUrl['api_url']);
		
		if($url == ''){
			return array(
				"status" => "error",
				"msg" => "Something Went Wrong"
			);
			exit;
		}
		
		//Get Policy Details
		$policyDetails = $this->db->query("select * from proposal_policy where proposal_policy_id='".$proposal_policy_id."' ")->row_array();
		
		$explode_name = explode(" ", trim($data['customer_data']['customer_name']),2);
		
		//Data Variables
		$uidNo = (!empty($data['customer_data']['adhar'])) ? $data['customer_data']['adhar'] : null;
		$source_name = "ABC_GFB";		
		$MasterPolicyNumber = '61-20-00040-00-00';
		$GroupID = 'GRP001';
		$plan_code = '4211';
		$Product_Code = '4211';
		$micrNo = null;
		
		$Member_Type_Code = "M209";
		$intermediaryCode = '2108233';
		$intermediaryBranchCode = '10MHMUM01';
		$SchemeCode = '4112000003';
		
		//get SumInsured Type
		$sumins_type = $this->db->query("select mi.suminsured_type from master_policy_si_type_mapping as m left join master_suminsured_type as mi ON m.suminsured_type_id = mi.suminsured_type_id where m.master_policy_id='$master_policy_id' ")->row();
		
		//echo "<pre>";print_r($sumins_type);exit;
		$SumInsured_Type = $sumins_type->suminsured_type;
		//echo $SumInsured_Type;exit;
		
		$leadID = $lead_id;
		$txnRefNumber = "pay_FrAaDQjzQFtQWG";
		
		$PaymentMode = "online";
		$bankName = 0;
		$branchName = null;
		$bankLocation = null;
		$chequeType = null;
		$ifscCode = null;
		$terminal_id = "EuxJCz8cZV9V63";
		
		$SumInsured = $sum_insured;
		$product_id = $data['proposal_data'][0]['plan_id'];
		$tax_amount = $policyDetails['tax_amount'];
		
		$collectionAmount = ( $policyDetails['premium_amount'] + $tax_amount );
		
		$transaction_date = explode(" ",$data['proposal_data'][0]['transaction_date']);
		$trans_date = date("Y-m-d", strtotime($transaction_date[0])); 
		//online,RTGS/ NEFT,Cheque
		$collectionMode = (!empty($data['proposal_data'][0]['mode_of_payment'])) ? $data['proposal_data'][0]['mode_of_payment'] : "Cheque";
		
		$SPID = 0;
		$RefCode1 = 0;
		$RefCode2 = 0;
		$Policy_Tanure = 1;
		$AutoRenewal = 'Y';
	
	
		$query_quote = [];
	
		if($policy_sub_type_id == 1){
			$data['customer_data']['customer_id'] = $data['customer_data']['customer_id']."GHI";
			
			$query_quote = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and lead_id='$lead_id' and proposal_policy_id='$proposal_policy_id' and status = 'success'")->row_array();
			
			/*if(empty($query_quote)){
				$quote_data = $this->get_quote_data($emp_id, $policy_no);
				if($quote_data['status'] == 'Success')
				{
					$query_quote['QuotationNumber'] = $quote_data['msg'];
				}
			}*/
			
		}else{
			
			
			$query_quote = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and lead_id='$lead_id' and proposal_policy_id='$proposal_policy_id' and status = 'success' ")->row_array();
			
			/*if(empty($query_quote)){
				$quote_data = $this->get_quote_data($emp_id, $policy_no, $mem_data);
				if($quote_data['status'] == 'Success')
				{
					$query_quote['QuotationNumber'] = $quote_data['msg'];
				}
			}*/
			
			if($policy_sub_type_id == 2){
				$concat_string = "GPA";
			}elseif($policy_sub_type_id == 3){
				$concat_string = "GCI";
			}elseif($policy_sub_type_id == 7){
				$concat_string = "GP";
			}
			
			$data['customer_data']['customer_id'] = $data['customer_data']['cust_id'].$concat_string;
			
				
		}
	
		if($query_quote['QuotationNumber']){
		   
			$totalMembers = count($data['member_data']);
			$member = [];
				
			$explode_name = array($data['customer_data']['first_name'],$data['customer_data']['middle_name'],$data['customer_data']['last_name']);
			$explode_name_nominee = array($data['nominee_data']['nominee_first_name'],$data['nominee_data']['nominee_last_name']);
			
			for ($i = 0;$i < $totalMembers; $i++){
					
				//Checking relation based on self, spouse, son and daughter
				if($data['member_data'][$i]['relation_with_proposal'] == 1 ){
					$data['member_data'][$i]['relation_code'] = "R001";
				}else if($data['member_data'][$i]['relation_with_proposal'] == 2 ){
					$data['member_data'][$i]['relation_code'] = "R002";
				}else if($data['member_data'][$i]['relation_with_proposal'] == 3 ){
					$data['member_data'][$i]['relation_code'] = "R003";
				}else{
					$data['member_data'][$i]['relation_code'] = "R004";
				}
				
				//Nominee relation code
				if($data['nominee_data']['nominee_relation'] == 1 ){
					$data['nominee_data']['relation_code'] = "R001";
				}else if($data['nominee_data']['nominee_relation'] == 2 ){
					$data['nominee_data']['relation_code'] = "R002";
				}else if($data['nominee_data']['nominee_relation'] == 3 ){
					$data['nominee_data']['relation_code'] = "R003";
				}else{
					$data['nominee_data']['relation_code'] = "R004";
				}
			
				
				$abc = ["PEDCode" => null, "Remarks" => null];
			
				$member[] = ["MemberNo" => $i + 1, "Salutation" => $data['member_data'][$i]['policy_member_salutation'] , "First_Name" => $data['member_data'][$i]['policy_member_first_name'], "Middle_Name" => null, "Last_Name" => !empty($data['member_data'][$i]['policy_member_last_name']) ? $data['member_data'][$i]['policy_member_last_name'] : '.', "Gender" => ($data['member_data'][$i]['policy_member_gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['policy_member_dob'])) , "Relation_Code" => trim($data['member_data'][$i]['relation_code']), "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => "O553", "PrimaryMember" => (($i == 0) ? "Y" : "N") , "MemberproductComponents" => [["PlanCode" => $plan_code, "MemberQuestionDetails" => [["QuestionCode" => null, "Answer" => null, "Remarks" => null]]]], "MemberPED" => $abc, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $explode_name_nominee[0], "Nominee_Last_Name" => !empty($explode_name_nominee[1]) ? $explode_name_nominee[1] : '.', "Nominee_Contact_Number" => $data['nominee_data']['nominee_contact'], "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data']['relation_code']), ];

			}
			
			$data['proposal_data']['SourceSystemName_api'] = 'CreditorPortal';	


			$fqrequest = ["ClientCreation" => ["Member_Customer_ID" => $data['customer_data']['customer_id'], "salutation" => $data['customer_data']['salutation'],"firstName" => $data['customer_data']['first_name'], "middleName" => "", "lastName" => !empty($data['customer_data']['last_name']) ? $data['customer_data']['last_name'] : '.',"dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['dob'])) , "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F","educationalQualification" => null, "pinCode" => $data['customer_data']['pincode'], "uidNo" => $uidNo,"maritalStatus" => null, "nationality" => "Indian", "occupation" => "O553", "primaryEmailID" => $data['customer_data']['email_id'],"contactMobileNo" => substr(trim($data['customer_data']['mobile_no']), -10), "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null,"annualIncome" => null, "remarks" => null, "startDate" => date('Y-m-d') , "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null,"ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address_line1'], "homeAddressLine2" => null, "homeAddressLine3" => null,"homePinCode" => $data['customer_data']['pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null,"homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null,"mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null,"mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null,"ifscCode" => $ifscCode, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null,"EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null],"PolicyCreationRequest" => ["Quotation_Number" => $query_quote['QuotationNumber'], "MasterPolicyNumber" => $MasterPolicyNumber,"GroupID" => $GroupID, "Product_Code" => $Product_Code,"SumInsured_Type"=> $SumInsured_Type,"Policy_Tanure"=> $Policy_Tanure,"Member_Type_Code"=> $Member_Type_Code, "intermediaryCode" => $intermediaryCode,"AutoRenewal" => $AutoRenewal, "intermediaryBranchCode" => $intermediaryBranchCode, "agentSignatureDate" => null,"Customer_Signature_Date" => null,"businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $leadID, "Source_Name" => $source_name, "SPID" => $SPID, "TCN" => null, "CRTNO" => null, "RefCode1" => $RefCode1,"RefCode2" => $RefCode2, "Employee_Number" => $data['customer_data']['customer_id'],"enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "1", "PaymentMode" => $collectionMode,"PolicyproductComponents" => [["PlanCode" => $plan_code, "SumInsured" => $SumInsured, "SchemeCode" => $SchemeCode]]], "MemObj" => ["Member" => $member],"ReceiptCreation" => ["officeLocation" => "Mumbai", "modeOfEntry" => "Direct", "cdAcNo" => null, "expiryDate" => null, "payerType" => "Customer", "payerCode" => null,"paymentBy" => "Customer", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => round($collectionAmount,2),"collectionRcvdDate" => $trans_date,"collectionMode" => $collectionMode, "remarks" => null, "instrumentNumber" => $txnRefNumber,"instrumentDate" => $trans_date, "bankName" => $bankName, "branchName" => $branchName,"bankLocation" => $bankLocation, "micrNo" => $micrNo, "chequeType" => $chequeType, "ifscCode" => $ifscCode, "PaymentGatewayName" => $source_name, "TerminalID" => $terminal_id,"CardNo" => null]];
		
			//print_pre($fqrequest);exit;
			//Monolog::saveLog("full_quote_request2", "I", json_encode($fqrequest));

			$request_arr = ["lead_id" => $leadID, "req" => json_encode($fqrequest) ,"product_id" => $product_id, "type"=>"full_quote_request", "proposal_policy_id"=> $proposal_policy_id];
		
			$this->db->insert("logs_docs",$request_arr);
			$insert_id = $this->db->insert_id();

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 90,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => json_encode($fqrequest) ,
				CURLOPT_HTTPHEADER => array(
					"Accept: */*",
					"Cache-Control: no-cache",
					"Connection: keep-alive",
					"Content-Length: " . strlen(json_encode($fqrequest)) ,
					"Content-Type: application/json",
					"Host: bizpre.adityabirlahealth.com"
				) ,
			));

			$response = curl_exec($curl);

			//Monolog::saveLog("full_quote_reponse2", "I", json_encode($response));

			$request_arr = ["res" => json_encode($response)];
			$this->db->where("id",$insert_id);
			$this->db->update("logs_docs",$request_arr);

			$err = curl_error($curl);
			//echo "<pre>";print_r($response);exit;
			curl_close($curl);

			if ($err){
				return array(
					"status" => "error",
					"msg" => $err
				);
			}else{
				$new = simplexml_load_string($response);
				$con = json_encode($new);
				$newArr = json_decode($con, true);
				
				$errorObj = $newArr['errorObj'];
				$premium = $newArr['premium'];
				$return_data = [];

				if($errorObj['ErrorNumber']=='00' || ($errorObj['ErrorNumber']=='302' && $master_policy_id == $newArr['policyDtls']['PolicyNumber'])){
					
					$return_data['status'] = 'Success';
					$return_data['msg'] = $errorObj['ErrorMessage'];

					$api_insert = array(
						"emp_id" => $emp_id,
						"client_id" => $newArr['policyDtls']['ClientID'],
						"certificate_number" => $newArr['policyDtls']['CertificateNumber'],
						"quotation_no" => $newArr['policyDtls']['QuotationNumber'],
						"proposal_no" => $newArr['policyDtls']['ProposalNumber'],
						"policy_no" => $newArr['policyDtls']['PolicyNumber'],
						"gross_premium" => empty($premium['GrossPremium'])?'':$premium['GrossPremium'],
						"status" => "Success",
						"start_date" => $newArr['policyDtls']['startDate'],
						"end_date" => $newArr['policyDtls']['EndDate'],
						"created_date" => date('Y-m-d H:i:s'),
						"proposal_no_lead"=>$proposal_policy_id,
						"PolicyStatus" => $newArr['policyDtls']['PolicyStatus'],
						"letter_url" => $newArr['policyDtls']['LetterURL'],
						"CustomerID" => $newArr['policyDtls']['MemberCustomerID'],
						"MemberCustomerID" => $newArr['policyDtls']['MemberCustomerID'],
						//"COI_url" => $newArr['policyDtls']['COIUrl'],

					);
					
					$this->db->insert('api_proposal_response', $api_insert);
					
					//Monolog::saveLog("api_insert", "I", json_encode($api_insert));	
					
					//HB emandate call
					$this->emandate_HB_call($emp_id,$data['proposal_data'][0]['proposal_details_id']);

					$request_arr = ["lead_id" => $leadID, "req" => json_encode($api_insert) ,"product_id"=> $product_id, "type"=>"api_insert"];
					$this->db->insert("logs_docs",$request_arr);
					$this->Logs_m->insertLogs($dataArray);
					
					

				}else{

					$return_data = array(
						'status'=>'error',
						"msg" => $errorObj['ErrorMessage']
					);
				}
				
				return $return_data;
				
			}

		}else{
			
			return $return_data = array(
				'status'=>'error',
				"msg" => "Quote error"
			);
		}

	}
	
	// Emadate HB Call
	
	function emandate_HB_call($emp_id,$proposal_details_id)
	{ 	
		$query_check = $this->db->query("select ed.lead_id,ed.plan_id,ed.json_qote,apr.certificate_number,apr.proposal_no,apr.pr_api_id from proposal_details as ed,proposal_policy as p,api_proposal_response as apr,emandate_data as emd where ed.proposal_details_id = p.proposal_details_id and p.proposal_policy_id = apr.proposal_no_lead and p.status in('Success','Payment Received') and apr.mandate_send_status = 0 and emd.lead_id = ed.lead_id and emd.status = 'Success' and ed.proposal_details_id = '$proposal_details_id' group by p.proposal_details_id")->result_array();

		if($query_check){ 
		
		foreach ($query_check as $val)
		{
			
			//BIZ HB call start
			$json_data = json_decode($val['json_qote'],true);

			$url = 'https://bizpre.adityabirlahealth.com/ABHICL_HealthCoreXML/Service1.svc/AddEmendateDetails';
			
			$req_arr = [
					  'EmendateDeatails' => 
					  [
						'EmendateList' => 
						[ 
						  [
							'Bank_Name' => ($json_data['AXISBANKACCOUNT'] == 'Y')?'Axis Bank':'Other',
							'Debit_Account_Number' => $json_data['ACCOUNTNUMBER'],
							'Mandate_Start_Date' => '10-02-2020',
							'Mandate_End_Date' => '11-09-2020',
							'Account_Type' => 'Saving',
							'Bank_Branch_Name' => $json_data['BRANCH_NAME'],
							'MICR' => '123',
							'IFSC' => $json_data['IFSCCODE'],
							'Frequency' => "Annual",
							'Policy_Number' => $val['certificate_number'],
						  ],
						],
					  ],
					];
					
				   $curl = curl_init();

					curl_setopt_array($curl, array(
						CURLOPT_URL => $url,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => "",
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 60,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => "POST",
						CURLOPT_POSTFIELDS => json_encode($req_arr) ,
						CURLOPT_HTTPHEADER => array(
							"Accept: */*",
							"Cache-Control: no-cache",
							"Connection: keep-alive",
							"Content-Length: " . strlen(json_encode($req_arr)) ,
							"Content-Type: application/json",
							"Host: bizpre.adityabirlahealth.com"
						) ,
					));

				   $response = curl_exec($curl);
				   
				   curl_close($curl);
				   
				   // if($response){
					   // $response = json_decode($response);
				   // }
				   
				   $update_arr = ["mandate_send_status" => 1];
						
					$this->db->where("pr_api_id",$val['pr_api_id']);
					$this->db->update("api_proposal_response",$update_arr);
				   
				   $request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($req_arr),"res" => json_encode($response),"product_id"=> "ABC", "type"=>"emandate_HB_post"];
				
					$this->db->insert('logs_docs',$request_arr);
					//BIZ HB call end
					
		}
		
		}
	}
	
	
	//Get Customer data
	function get_profile($emp_id){
		return $this->db->query("select e.* from master_customer as e where e.customer_id='$emp_id'")->row();
    }
	
	//Get all member data
	function get_all_member_data($proposal_policy_id)
    {
        
		$response = $this->db->query('select * from proposal_policy_member where policy_id='.$proposal_policy_id.' ')->result_array();
		//echo $this->db->last_query();
        return $response;
    }
	function real_pg_check($lead_id){
		$check_pg = false;
			
		$query = $this->db->query("SELECT ed.lead_id,ed.primary_customer_id,plan_id FROM lead_details as ed where ed.lead_id =".$lead_id)->row_array();
		
		if($query){
			
			if($query['plan_id'] != 'R06'){
				$vertical = 'AXBBGRP';
			}else{
				$vertical = 'AXATGRP';
			}
			
				$CKS_data = "AX|".$vertical."|LEADID|".$query['lead_id']."|".$this->hash_key;
				
				$CKS_value = hash($this->hashMethod, $CKS_data);
		
				$url = "https://pg_uat.adityabirlahealth.com/PGMANDATE/service/api/enquirePayment";
				$fqrequest = array(
						"signature"=> $CKS_value,
						"Source"=> "AX",
						"Vertical"=> $vertical,
						"SearchMode"=> "LEADID",
						"UniqueIdentifierValue"=> $query['lead_id'],
						"PaymentMode"=> "PP"
						);
						
				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => $url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 90,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => json_encode($fqrequest) ,
					CURLOPT_HTTPHEADER => array(
						"Accept: */*",
						"Cache-Control: no-cache",
						"Connection: keep-alive",
						"Content-Length: " . strlen(json_encode($fqrequest)) ,
						"Content-Type: application/json"
					) ,
				));


				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);
				
				$result = json_decode($response, true);
				
				// print_r($response); 
				// echo json_encode($fqrequest); exit;
				
				if ($err) {
				  $request_arr = ["lead_id" => $query['lead_id'],"req" => json_encode($fqrequest), "res" => json_encode($err) ,"product_id"=> $query['plan_id'],"type"=>"pg_real_fail"];
				  $this->db->insert('logs_docs',$request_arr);
				  
				}else{
					
					if($result && $result['PaymentStatus'] == 'PR'){
				
						$TxStatus = "success";
						$TxMsg = "Approved";
										
						$request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest) ,"res" => json_encode($result),"product_id"=> $query['plan_id'], "type"=>"pg_real_success"];
						
						$this->db->insert('logs_docs',$request_arr);
						
						$arr = ["payment_remark" => $TxStatus,"status" => $TxMsg,"premium_amount" => $result['amount'],"transaction_date" => $result['txnDateTime'],"transaction_number" => $result['TxRefNo']];
				
						$proposal_ids = $this->db->query("select id as proposal_id from proposal_details where lead_id='".$query['lead_id']."'")->result_array();
						
						foreach ($proposal_ids as $query_val)
						{
							$this->db->where("proposal_details_id",$query_val['proposal_id']);
							$this->db->update("proposal_details",$arr);	
						}
						$this->db->where("lead_id",$query['lead_id']);
						$this->db->update("lead_details",array('status'=>"Approved"));	
						if($result['EMandateStatus']){
				
							$query_emandate = $this->db->query("select * from emandate_data where lead_id=".$query['lead_id'])->row_array();
							
							if($result['EMandateStatus'] == 'MS'){
								$mandate_status = 'Success';
								//HB emandate call
								//$this->emandate_HB_call($query['lead_id']);
							}elseif($result['EMandateStatus'] == 'MI'){
								$mandate_status = 'Emandate Pending';
							}elseif($result['EMandateStatus'] == 'MR'){
								$mandate_status = 'Emandate Received';
							}else{
								$mandate_status = 'Fail';
							}
						
							if($query_emandate > 0){
								
								$arr = ["TRN" => $result['EMandateRefno'],"status_desc" => $result['EMandateStatusDesc'],"status" => $mandate_status,"mandate_date" => date('Y-m-d h:i:s',strtotime($result['EMandateDate'])),"Registrationmode" => $result['Registrationmode'], "EMandateFailureReason" => $result['EMandateFailureReason']];
								
								$this->db->where("lead_id",$query['lead_id']);
								$this->db->update("emandate_data",$arr);
							}else{
								
								$arr = ["lead_id" => $query['lead_id'],"TRN" => $result['EMandateRefno'],"status_desc" => $result['EMandateStatusDesc'],"status" => $mandate_status,"mandate_date" => date('Y-m-d h:i:s',strtotime($result['EMandateDate'])),"Registrationmode" => $result['Registrationmode'], "EMandateFailureReason" => $result['EMandateFailureReason']];
								
								$this->db->insert("emandate_data", $arr);
							}
							
							if($mandate_status == 'Success'){
								//$this->send_message($query['lead_id'],'success');
							}
							
							if($mandate_status == 'Fail'){
								//$this->send_message($query['lead_id'],'fail');
							}
							
						}
		
						$check_pg = true;
						
					}else{
						
					   $request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest) ,"res" => json_encode($result), "product_id"=> $query['plan_id'],"type"=>"pg_real_fail"];
					   $this->db->insert('logs_docs',$request_arr);
					}
				
				}
		}

		return $check_pg;
}

	//Get Policy nominee
	function get_all_nominee($emp_id)
    {
        $response = $this->db->select('*,mfr.relation_code,mfr.nominee_type as fr_name')
        ->from('member_policy_nominee AS mpn,master_nominee as mfr')
        ->where('mpn.emp_id', $emp_id)
        ->where('mpn.fr_id = mfr.nominee_id')
        ->get()->row_array();
        if ($response) {
            return $response;
        }
    }
	
	
	function insertBatchData($tbl_name,$data_array,$sendid = NULL)
	{
	 	$this->db->insert_batch($tbl_name,$data_array);
	 	$result_id = $this->db->insert_id();
	 	
	 	/*echo $result_id;
	 	exit;*/
	 	
	 	if($sendid == 1)
	 	{
	 		//return id
	 		return $result_id;
	 	}
	}
	
	function login_check($condition) {
		$this -> db -> select('i.employee_id , i.employee_fname, i.employee_mname,i.employee_lname,i.employee_code,i.email_id,i.mobile_number,i.role_id');
		$this -> db -> from('master_employee as i');
		$this -> db -> where("($condition)");
		
		$query = $this -> db -> get();
		//print_r($this->db->last_query());
		//exit;
	 
		if($query -> num_rows() >= 1)
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}
    
	function fetch_otp($mobile,$country_code){
		
		//valid check 15 minutes
		$current_date_time = date("Y-m-d H:i:s");
		$current_timstamp = strtotime($current_date_time);
		$current_timstamp = $current_timstamp - (15 * 60);
		$from_date_time = date("Y-m-d H:i:s", $current_timstamp);

		$otp_condition = "";
		$sql = $this->db->query("select * from tbl_otp_check where  mobile = ".$this->db->escape($mobile)." && country_code = ".$this->db->escape($country_code)." && (time_stamp >='".$from_date_time."' && time_stamp <='".$current_date_time."' ) ");
		if($sql->num_rows()>0){
			return $sql->result();
		}else{
			return false;
		}
	}
  
	function generate_otp($mobile,$country_code,$otp){
		$sql = $this->db->query("select * from tbl_otp_check where mobile = ".$this->db->escape($mobile).' And country_code ='.$this->db->escape($country_code) );
		$data = array(
               "mobile"=>$mobile,
               "country_code"=>$country_code,
               "otp" => $otp,
               "time_stamp"=>date('Y-m-d H:i:s')
		);
    
		if($sql->num_rows() > 0){
			$this->updatedataotp($data, "tbl_otp_check", "mobile", $mobile,"country_code", $country_code);
		}else{
			$this->insertData("tbl_otp_check",$data,1);
		}
		
		return true;
	}
  
	function updatedataotp($data,$table,$column1,$value1,$column2,$value2){
		$this->db->where($column1,$value1);
		$this->db->where($column2,$value2);
		$this->db->update($table,$data);
	}
	
	function runquery($sql_query = ''){
		$query = $this->db->query($sql_query);
		if($query->num_rows() > 0){
			return $query->result();
		}else{
			return false;
		}
	}
	
	function runquery_array($sql_query = ''){
		$query = $this->db->query($sql_query);
		if($query->num_rows() > 0){
			return $query->result_array();
		}else{
			return false;
		}
	}
	
	function getdata($table, $fields, $condition = '1=1'){
		//echo "Select $fields from $table where $condition";exit;
		$sql = $this->db->query("Select $fields from $table where $condition");
		if($sql->num_rows() > 0){
			return $sql->result_array();
		}else{
			return false;
		}
	}
	function getdata1($table, $fields, $condition = '1=1'){
		//echo "Select $fields from $table where $condition";exit;
		$sql = $this->db->query("Select $fields from $table where $condition");
		if($sql->num_rows() > 0){
			return $sql->result();
		}else{
			return false;
		}
	}
	
	function getdataCount($table, $fields, $condition = '1=1'){
		
		$sql = $this->db->query("Select count(*) as total  from $table where $condition");
		if($sql->num_rows() > 0){
		    $cnt = $sql->result_array();
			return $cnt[0]['total'];
		}else{
			return 0;
		}
	}
	
	function getdata_orderby($table, $fields, $condition = '1=1', $order_by){
		//echo "Select $fields from $table where $condition";exit;
		$sql = $this->db->query("Select $fields from $table where $condition $order_by");
		if($sql->num_rows() > 0){
			return $sql->result_array();
		}else{
			return false;
		}
	}
	
	function getdata_groupby_orderby($table, $fields, $condition = '1=1', $group_by="", $order_by=""){
		//echo "Select $fields from $table where $condition";exit;
		$sql = $this->db->query("Select $fields from $table where $condition $group_by $order_by");
		if($sql->num_rows() > 0){
			return $sql->result_array();
		}else{
			return false;
		}
	}
	
	function getdata_groupby_orderby_limit($table, $fields, $condition = '1=1', $group_by="", $order_by="", $page ){
		$rows = 10;
		$page = $rows * $page;
		$limit = $page.",".$rows;
		
		$sql = $this->db->query("Select $fields from $table where $condition $group_by $order_by limit $limit");
		//print_r($this->db->last_query());
		//exit;
		
		if($sql->num_rows() > 0){
			return $sql->result_array();
		}else{
			return false;
		}
	}
	
	function getdata_orderby_limit($table, $fields, $condition = '1=1', $order_by, $page){
	
		$rows = 10;
		$page = $rows * $page;
		$limit = $page.",".$rows;
		
		$sql = $this->db->query("Select $fields from $table where $condition $order_by limit $limit");
		//print_r($this->db->last_query());
		//exit;
		
		if($sql->num_rows() > 0){
			return $sql->result_array();
		}else{
			return false;
		}
	}
	
	function getdata_orderby1($table, $fields, $condition = '1=1', $order_by){
		//echo "Select $fields from $table where $condition";exit;
		$sql = $this->db->query("Select $fields from $table where $condition order by $order_by desc limit 1");
		if($sql->num_rows() > 0){
			return $sql->result_array();
		}else{
			return false;
		}
	}

	function getdata_orderby2($table, $fields, $condition = '1=1', $order_by){
		//echo "Select $fields from $table where $condition";exit;
		$sql = $this->db->query("Select $fields from $table where $condition order by $order_by limit 1");
		if($sql->num_rows() > 0){
			return $sql->result_array();
		}else{
			return false;
		}
	}
    
    
    
	function updateRecord($tbl_name,$datar,$condition)
	{
		//$this -> db -> where($comp_col, $eid);
		$this->db->where("($condition)");
		$this -> db -> update($tbl_name,$datar);
		 
		if ($this->db->affected_rows() > 0){
			return true;
		}else{
			return true;
		} 
	}
	function updateRecordarr($tbl_name,$datar,$condition)
	{
		//$this -> db -> where($comp_col, $eid);
		$this->db->where($condition);
		$this->db->update($tbl_name,$datar);
		 
		if ($this->db->affected_rows() > 0){
			return 1;
		}else{
			return 0;
		} 
	}	
	
	function delrecord($tbl_name,$tbl_id,$record_id)
	{
		$this->db->where($tbl_id, $record_id);
	    $this->db->delete($tbl_name);
		if($this->db->affected_rows() >= 1)
		{
			return true;
	    }
	    else
	    {
			return false;
	    }
	}
	
	function delrecord_condition($tbl_name,$condition)
	{
		//$this->db->where($tbl_id, $record_id);
		$this->db->where("($condition)");
		$this->db->delete($tbl_name);
		if($this->db->affected_rows() >= 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function delrecord_array($tbl_name,$condition)
	{
		//$this->db->where($tbl_id, $record_id);
		$this->db->where($condition);
		$this->db->delete($tbl_name);
		if($this->db->affected_rows() >= 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function check_utoken($decode_utoken,$usertype,$apptype,$login_userid) {
		$this -> db -> select('u.*');
		$this -> db -> from('tbl_users_master as u');
		
		if ($apptype != 'W'){
			if($usertype == 'P' ){
				$this -> db -> where('concat(u.user_master_id,provider_device_id)',$decode_utoken);
			}
			else if($usertype == 'C'){
				$this -> db -> where('concat(u.user_master_id,consumer_device_id)',$decode_utoken);
			}
		}
		// if called via web check decode_utoken only with user id
		else{
			$this -> db -> where('u.user_master_id',$decode_utoken);
		}

		//also check with login user id	- bascially user id within the decode_utoken should be same as loginuser id
		$this -> db -> where('u.user_master_id',$login_userid); 
		$this -> db -> where('u.status',"Active");	

		
		$query = $this -> db -> get();
	   
		//print_r($this->db->last_query());
		//exit;
	   
		if($query -> num_rows() >= 1)
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}
	
	function check_registered_utoken($utoken) {
		$this -> db -> select('u.*');
		$this -> db -> from('tbl_users as u');	
		$this -> db -> where('u.email',$utoken);
		$query = $this -> db -> get();
	
		if($query -> num_rows() >= 1)
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
		
	}
	
	
	function getRecords($tbl_name, $condition, $page=null, $default_sort_column=null, $default_sort_order=null, $group_by=null){
		
		$table = $tbl_name;
		$default_sort_column = $default_sort_column;
		$default_sort_order = $default_sort_order;
		if($page != null){
			$rows = 10;
			$page = $rows * $page;
		}
		
		// sort order by column
		$sort = $default_sort_column;  
		$order = $default_sort_order;

		$this -> db -> select('*');
		$this -> db -> from($tbl_name);
		
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		if($page != null){
			$this->db->limit($rows,$page);
		}
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
				
		if($query -> num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}
	}

}
?>
