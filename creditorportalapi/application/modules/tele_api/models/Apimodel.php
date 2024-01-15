<?PHP

class Apimodel extends CI_Model
{

	function getSortedData($select, $table, $condition = "", $sort_col = "", $sort_order = "")
	{	
		$this->db->select($select);
		$this->db->from($table);
		
		if(!empty($condition)){
			$this->db->where($condition);
		}
		
		if(!empty($sort_col) && !empty($sort_order)){
			$this->db->order_by($sort_col, $sort_order);
		}
		
		$query = $this->db->get();
		
		// echo "<br/>";
		// print_r($this->db->last_query());
		// exit;
	   
		if($query->num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}
	}
	
	function getInsurerList($post)
	{
		$table = "master_insurer";
		$table_id = 'insurer_id ';
		$default_sort_column = 'insurer_id ';
		$default_sort_order = 'desc';
		$condition = "1=1";


		$colArray = array('i.insurer_name');
		$sortArray = array('i.insurer_name', 'i.insurer_code', 'i.isactive');

		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset

		// sort order by column
		$sort = isset($post['iSortCol_0']) ? strval($sortArray[$post['iSortCol_0']]) : $default_sort_column;
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for ($i = 0; $i < 1; $i++) {
			if (isset($post['sSearch_' . $i]) && $post['sSearch_' . $i] != '') {
				$condition .= " AND $colArray[$i] like '%" . $_POST['sSearch_' . $i] . "%'";
			}
		}

		//echo "Condition: ".$condition;
		//exit;
		$this->db->select('*');
		$this->db->from('master_insurer as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows, $page);

		$query = $this->db->get();

		//print_r($this->db->last_query());
		//exit;

		$this->db->select('*');
		$this->db->from('master_insurer as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);

		$query1 = $this->db->get();
		//echo "total: ".$query1 -> num_rows();
		//exit;

		if ($query->num_rows() >= 1) {
			$totcount = $query1->num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		} else {
			return array("totalRecords" => 0);
		}
	}
	function getInsurerFormData($ID)
	{
		$this->db->select('i.*');
		$this->db->from('master_insurer as i');
		$this->db->where('i.insurer_id', $ID);

		$query = $this->db->get();

		//print_r($this->db->last_query());
		//exit;

		if ($query->num_rows() >= 1) {
			return $query->result();
		} else {
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
		$sortArray = array('i.suminsured_type', 'i.isactive');

		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset

		// sort order by column
		$sort = isset($post['iSortCol_0']) ? strval($sortArray[$post['iSortCol_0']]) : $default_sort_column;
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for ($i = 0; $i < 1; $i++) {
			if (isset($post['sSearch_' . $i]) && $post['sSearch_' . $i] != '') {
				$condition .= " AND $colArray[$i] like '%" . $_POST['sSearch_' . $i] . "%'";
			}
		}

		//echo "Condition: ".$condition;
		//exit;
		$this->db->select('*');
		$this->db->from('master_suminsured_type as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows, $page);

		$query = $this->db->get();

		//print_r($this->db->last_query());
		//exit;

		$this->db->select('*');
		$this->db->from('master_suminsured_type as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);

		$query1 = $this->db->get();
		//echo "total: ".$query1 -> num_rows();
		//exit;

		if ($query->num_rows() >= 1) {
			$totcount = $query1->num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		} else {
			return array("totalRecords" => 0);
		}
	}
	function getSuminsuredFormData($ID)
	{
		$this->db->select('i.*');
		$this->db->from('master_suminsured_type as i');
		$this->db->where('i.suminsured_type_id', $ID);

		$query = $this->db->get();

		//print_r($this->db->last_query());
		//exit;

		if ($query->num_rows() >= 1) {
			return $query->result();
		} else {
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
		$sortArray = array('i.policy_sub_type_name', 'i.policy_type_id', 'i.isactive');

		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset

		// sort order by column
		$sort = isset($post['iSortCol_0']) ? strval($sortArray[$post['iSortCol_0']]) : $default_sort_column;
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for ($i = 0; $i < 1; $i++) {
			if (isset($post['sSearch_' . $i]) && $post['sSearch_' . $i] != '') {
				$condition .= " AND $colArray[$i] like '%" . $_POST['sSearch_' . $i] . "%'";
			}
		}
		$condition .= " AND ij.isactive = 1";
		//echo "Condition: ".$condition;
		//exit;
		$this->db->select('i.*,ij.policy_type_id as typeid,ij.policy_type_name as typename');
		$this->db->from('master_policy_sub_type as i');
		$this->db->join('master_policy_type as ij', 'i.policy_type_id = ij.policy_type_id');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows, $page);

		$query = $this->db->get();

		//print_r($this->db->last_query());
		//exit;

		$this->db->select('i.*,ij.policy_type_id as typeid,ij.policy_type_name as typename');
		$this->db->from('master_policy_sub_type as i');
		$this->db->join('master_policy_type as ij', 'i.policy_type_id = ij.policy_type_id');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);

		$query1 = $this->db->get();
		//echo "total: ".$query1 -> num_rows();
		//exit;

		if ($query->num_rows() >= 1) {
			$totcount = $query1->num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		} else {
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

		for ($i = 0; $i < 1; $i++) {
			if (isset($post['sSearch_' . $i]) && $post['sSearch_' . $i] != '') {
				$condition .= " AND $colArray[$i] like '%" . $_POST['sSearch_' . $i] . "%'";
			}
		}

		//echo "Condition: ".$condition;
		//exit;
		$this->db->select('i.*');
		$this->db->from('family_construct as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows, $page);

		$query = $this->db->get();

		//print_r($this->db->last_query());
		//exit;

		$this->db->select('i.*');
		$this->db->from('family_construct as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);

		$query1 = $this->db->get();
		//echo "total: ".$query1 -> num_rows();
		//exit;

		if ($query->num_rows() >= 1) {
			$totcount = $query1->num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		} else {
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
		$sortArray = array('i.plan_name', 'i.policy_type_id', 'i.creditor_id', 'i.isactive');

		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset

		// sort order by column
		$sort = isset($post['iSortCol_0']) ? strval($sortArray[$post['iSortCol_0']]) : $default_sort_column;
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for ($i = 0; $i < 1; $i++) {
			if (isset($post['sSearch_' . $i]) && $post['sSearch_' . $i] != '') {
				$condition .= " AND $colArray[$i] like '%" . $_POST['sSearch_' . $i] . "%'";
			}
		}

		//echo "Condition: ".$condition;
		//exit;
		$this->db->select('i.*,ij.creaditor_name as creditorname,ik.policy_type_name as policytype');
		$this->db->from('master_plan as i');
		$this->db->join('master_ceditors as ij', 'i.creditor_id = ij.creditor_id');
		$this->db->join('master_policy_type as ik', 'i.policy_type_id = ik.policy_type_id');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows, $page);

		$query = $this->db->get();

		//print_r($this->db->last_query());
		//exit;

		$this->db->select('i.*,ij.creaditor_name as creditorname,ik.policy_type_name as policytype');
		$this->db->from('master_plan as i');
		$this->db->join('master_ceditors as ij', 'i.creditor_id = ij.creditor_id');
		$this->db->join('master_policy_type as ik', 'i.policy_type_id = ik.policy_type_id');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);

		$query1 = $this->db->get();
		//echo "total: ".$query1 -> num_rows();
		//exit;

		if ($query->num_rows() >= 1) {
			$totcount = $query1->num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		} else {
			return array("totalRecords" => 0);
		}
	}
	function getPolicySubTypeFormData($ID)
	{
		$this->db->select('i.*');
		$this->db->from('master_policy_sub_type as i');
		$this->db->where('i.policy_sub_type_id', $ID);

		$query = $this->db->get();

		//print_r($this->db->last_query());
		//exit;

		if ($query->num_rows() >= 1) {
			return $query->result();
		} else {
			return false;
		}
	}
	function getFamilyConstructFormData($ID)
	{
		$this->db->select('i.*');
		$this->db->from('family_construct as i');
		$this->db->where('i.id', $ID);

		$query = $this->db->get();

		//print_r($this->db->last_query());
		//exit;

		if ($query->num_rows() >= 1) {
			return $query->result();
		} else {
			return false;
		}
	}
	function getInsurer()
	{
		$data = $this->db->get_where('master_insurer', array('isactive' => 1))->result();
		return $data;
	}
	function getCreditors()
	{
		$data = $this->db->get_where('master_ceditors', array('isactive' => 1))->result();
		return $data;
	}
	function getFeaturesList($post)
	{
		$table = "features_config";
		$table_id = 'id ';
		$default_sort_column = 'id ';
		$default_sort_order = 'desc';
		$condition = "1=1";

		$colArray = array('i.title');
		$sortArray = array('i.title');

		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset

		// sort order by column
		$sort = isset($post['iSortCol_0']) ? strval($sortArray[$post['iSortCol_0']]) : $default_sort_column;
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for ($i = 0; $i < 1; $i++) {
			if (isset($post['sSearch_' . $i]) && $post['sSearch_' . $i] != '') {
				$condition .= " AND $colArray[$i] like '%" . $_POST['sSearch_' . $i] . "%'";
			}
		}

		//echo "Condition: ".$condition;
		//exit;
		$this->db->select('i.*');
		$this->db->from('features_config as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows, $page);

		$query = $this->db->get();

		//print_r($this->db->last_query());
		//exit;

		$this->db->select('i.*');
		$this->db->from('features_config as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);

		$query1 = $this->db->get();
		//echo "total: ".$query1 -> num_rows();
		//exit;

		if ($query->num_rows() >= 1) {
			$totcount = $query1->num_rows();
			return array("query_result" => $query->result(), "totalRecords" => $totcount);
		} else {
			return array("totalRecords" => 0);
		}
	}
	function getFeaturesList_bk(){
		$data = $this->db->get_where('features_config', array('isactive' => 1))->result();
		return $data;
	}
	function getFeatures()
	{
		$data = $this->db->get_where('master_features', array('isactive' => 1))->result();
		return $data;
	}
	function getPaymentModes()
	{
        $data = $this->db
            ->select('*')
            ->from('master_payment_mode')
            ->get()
            ->result_array();

        return $data;
	}
	function getPaymentWorkflow()
	{
		$data = $this->db->get_where('payment_workflow_master', array('isactive' => 1))->result();
		return $data;
	}
	function getPolicyType()
	{
		$data = $this->db->get_where('master_policy_type', array('isactive' => 1))->result();
		return $data;
	}
	function getPolicySubType()
	{
		$data = $this->db->get_where('master_policy_sub_type', array('isactive' => 1))->result();
		return $data;
	}
	function getSiType()
	{
		$data = $this->db->get_where('master_suminsured_type', array('isactive' => 1))->result();
		return $data;
	}
	function checkplanname($name, $id = null)
	{
		if (!empty($id)) {
			$this->db->where(array('plan_id !=' => $id));
		}
		$plan = $this->db->get_where('master_plan', array('plan_name' => $name))->result();
		return $plan;
	}
	function checkpolicynumber($name, $id = null)
	{
		if (!empty($id)) {
			$this->db->where(array('policy_id !=' => $id));
		}
		$plan = $this->db->get_where('master_policy', array('policy_number' => $name))->result();
		return $plan;
	}
	function getPlanPayments($id)
	{
		$this->db->select('plan_payment_mode.*,payment_modes.payment_mode_name');
		$this->db->join('payment_modes', 'plan_payment_mode.payment_mode_id = payment_modes.payment_mode_id');
		$plan = $this->db->get_where('plan_payment_mode', array('master_plan_id' => $id))->result();
		return $plan;
	}
	function getProductDetails($id, $policy_id = null)
	{
		$this->db->select('mp.*,master_plan.creditor_id,master_plan.plan_name,mpst.policy_sub_type_name,mc.creaditor_name as creditor_name');
		$this->db->where('mp.plan_id', $id);
		if (!empty($policy_id)) {
			$this->db->where('mp.policy_id', $policy_id);
		}
		$this->db->where('mp.isactive', 1);
		$this->db->join('master_plan', 'mp.plan_id = master_plan.plan_id');
		$this->db->join('master_ceditors as mc', 'mc.creditor_id = master_plan.creditor_id');
		$this->db->join('master_policy_sub_type as mpst', 'mpst.policy_sub_type_id = mp.policy_sub_type_id');
		$data = $this->db->get('master_policy as mp')->result();
		return $data;
	}

	function getProductDetailsAll($id, $sort_by = '', $order = '', $policy_id_arr = [])
	{
		$this->db->select('mp.*,master_plan.creditor_id,master_plan.plan_name,master_plan.product_type_id,mpst.policy_sub_type_name,mpst.code as policy_sub_type_code,mpsi.suminsured_type_id as sitype_id,mppb.si_premium_basis_id as basis_id,mc.creaditor_name as creditor_name,mc.creditor_logo');
		$this->db->where('mp.plan_id', $id);
		$this->db->where('mp.isactive', 1);
		$this->db->where('mpsi.isactive', 1);
		$this->db->where('mppb.isactive', 1);
		$this->db->join('master_plan', 'mp.plan_id = master_plan.plan_id');
		$this->db->join('master_ceditors as mc', 'mc.creditor_id = master_plan.creditor_id');
		$this->db->join('master_policy_sub_type as mpst', 'mpst.policy_sub_type_id = mp.policy_sub_type_id');
		$this->db->join('master_policy_premium_basis_mapping as mppb', 'mppb.master_policy_id = mp.policy_id');
		$this->db->join('master_policy_si_type_mapping as mpsi', 'mpsi.master_policy_id = mp.policy_id');
	//	echo $this->db->last_query();exit;
		if(!empty($policy_id_arr)){

			$this->db->where_in('mp.policy_id', $policy_id_arr);
		}
		
		if($sort_by && $order){

			$this->db->order_by($sort_by,$order);
		}
		
		$data = $this->db->get('master_policy as mp')->result();
		return $data;
	}
	function getProductDetailsAll_diff($id, $sort_by = '', $order = '', $policy_id_arr = [])
	{
		$this->db->select('mp.*,master_plan.creditor_id,master_plan.plan_name,master_plan.product_type_id,mpst.policy_sub_type_name,mpst.code as policy_sub_type_code,mpsi.suminsured_type_id as sitype_id,mppb.si_premium_basis_id as basis_id,mc.creaditor_name as creditor_name,mc.creditor_logo,(select group_concat(member_type_id) from master_policy_family_construct fc where fc.master_policy_id=mp.policy_id and isactive=1) as family_construct');
		$this->db->where('mp.plan_id', $id);
		$this->db->where('mp.isactive', 1);
		$this->db->where('mpsi.isactive', 1);
		$this->db->where('mppb.isactive', 1);
		$this->db->join('master_plan', 'mp.plan_id = master_plan.plan_id');
		$this->db->join('master_ceditors as mc', 'mc.creditor_id = master_plan.creditor_id');
		$this->db->join('master_policy_sub_type as mpst', 'mpst.policy_sub_type_id = mp.policy_sub_type_id');
		$this->db->join('master_policy_premium_basis_mapping as mppb', 'mppb.master_policy_id = mp.policy_id');
		$this->db->join('master_policy_si_type_mapping as mpsi', 'mpsi.master_policy_id = mp.policy_id');
		
		if(!empty($policy_id_arr)){

			$this->db->where_in('mp.policy_id', $policy_id_arr);
		}
		
		if($sort_by && $order){

			$this->db->order_by($sort_by,$order);
		}
		
		$data = $this->db->get('master_policy as mp')->result_array();
		return $data;
	}
	function getPolicyDetails($id)
	{
		$this->db->select('mp.*,mpst.code as policy_sub_type_code,mpstm.suminsured_type_id,mpst.policy_sub_type_id,mpst.policy_sub_type_name');
		$this->db->join('master_policy_sub_type as mpst', 'mpst.policy_sub_type_id = mp.policy_sub_type_id');
		$this->db->join('master_policy_si_type_mapping as mpstm', 'mpstm.master_policy_id = mp.policy_id');
		$this->db->where('mp.policy_id', $id);
		$this->db->where('mpstm.isactive', 1);
		$data = $this->db->get('master_policy as mp')->result();
		return $data;
	}
	function getSiPremiumBasis()
	{
		$data = $this->db->get_where('master_si_premium_basis', array('isactive' => 1))->result();
		return $data;
	}
	function getproposalpolicybylead($leads)
	{
		$this->db->select('*');
		$this->db->where_in('lead_id', $leads);
		$data = $this->db->get('proposal_policy')->result();
		return $data;
	}
	function getMembers()
	{

        $this->db->select('fr_id, fr_name, status,is_adult');
        $data = $this->db->get_where('master_family_relation',array('status'=>1))->result();
        return $data;
	}

	function getpolicysubtypeofplan($plan)
	{
		$this->db->select('policy_sub_type_id');
		$data = $this->db->get_where('master_policy', array('plan_id' => $plan))->result();
		return $data;
	}

	/* 
	Jitendra
	**/
	function getPolicySubTypePlanCreditor($plan_id, $creditor_id)
	{
		$this->db->select('policy_sub_type_id,policy_id , is_optional,is_combo, bm.si_premium_basis_id as basis_id');
		$this->db->join('master_policy_premium_basis_mapping as bm', 'bm.master_policy_id = mp.policy_id');

		$data = $this->db->get_where('master_policy mp', array('mp.plan_id' => $plan_id, 'mp.creditor_id' => $creditor_id, 'mp.isactive' => '1', 'bm.isactive' => 1))->result();
	
		return $data;
	}
	function getPolicyPlan($plan_id)
	{
		$this->db->select('policy_sub_type_id, policy_id ,is_combo, is_optional, bm.si_premium_basis_id as basis_id');
		$this->db->join('master_policy_premium_basis_mapping as bm', 'bm.master_policy_id = mp.policy_id');
		$data = $this->db->get_where('master_policy mp', array('mp.plan_id' => $plan_id,  'mp.isactive' => '1', 'bm.isactive' => 1))->result_array();
		return $data;
	}

	function getSumInsureData($policy_id, $table = 'master_policy_premium')
	{

		$this->db->distinct();
		$this->db->select('sum_insured');
		$this->db->order_by('sum_insured', 'ASC');
		$data = $this->db->get_where($table, array('master_policy_id' => $policy_id, 'isactive' => 1))->result();
       // echo $this->db->last_query();exit;
		return $data;
	}

	/*
	Jitendra
	***/
	function getSumInsureDataDeductible($policy_id)
	{
		$this->db->distinct();
		$this->db->select('deductable');
		$this->db->order_by('deductable', 'ASC');
		$data = $this->db->get_where('master_policy_premium', array('master_policy_id' => $policy_id, 'isactive' => 1))->result();
		return $data;
	}

	//master_policy_premium_permile
	/*
	permile rate no. of CI
	***/
	function getNoOfCI($policy_id)
	{
		$this->db->distinct();
		$this->db->select('numbers_of_ci');
		$this->db->order_by('numbers_of_ci', 'ASC');
		$data = $this->db->get_where('master_policy_premium_permile', array('master_policy_id' => $policy_id))->result();
		return $data;
	} // EO getNoOfCI()

	function getpaymentofplan($plan)
	{
		$this->db->select('payment_mode_id');
		$data = $this->db->get_where('plan_payment_mode', array('master_plan_id' => $plan))->result();
		return $data;
	}
	function deletemasterpolicy($id, $plan)
	{
		$this->db->where('policy_sub_type_id', $id);
		$this->db->where('plan_id', $plan);
		$this->db->update('master_policy', array('isactive' => 0));
	}
	function deletepolicypayment($plan)
	{
		$this->db->where('master_plan_id', $plan);
		$this->db->update('plan_payment_mode', array('isactive' => 0));
	}
	function deletempfc($id)
	{
		$this->db->where('master_policy_id', $id);
		$this->db->update('master_policy_family_construct', array('isactive' => 0));
	}
	function deletemppb($id)
	{
		$this->db->where('master_policy_id', $id);
		$this->db->update('master_policy_premium_basis_mapping', array('isactive' => 0));
	}
	function deletempp($id)
	{
		$this->db->where('master_policy_id', $id);
		$this->db->update('master_policy_premium', array('isactive' => 0));
	}
	function deletempsi($id)
	{
		$this->db->where('master_policy_id', $id);
		$this->db->update('master_policy_si_type_mapping', array('isactive' => 0));
	}
	function updatepolicypayment($plan, $id)
	{
		$this->db->where('master_plan_id', $plan);
		$this->db->where('payment_mode_id', $id);
		$this->db->update('plan_payment_mode', array('isactive' => 1));
	}
	function getLeadDetails($id)
	{
		$this->db->select('lead_details.*,mc.customer_id,mc.salutation,mc.first_name,mc.pan,mc.middle_name,mc.last_name,mc.gender,mc.dob,mc.email_id,mc.mobile_no as customer_mobile_no,mc.mobile_no2 as customer_mobile_no2,mc.city,mc.state,mc.address_line1,mc.address_line2,mc.address_line3,mc.pincode');
		$this->db->join('master_customer as mc', 'mc.lead_id = lead_details.lead_id');
		$data = $this->db->get_where('lead_details', array('lead_details.lead_id' => $id))->result();
		return $data;
	}
	function getProposalPolicy($id)
	{
		$data = $this->db->get_where('proposal_policy', array('lead_id' => $id))->result();
		return $data;
	}

	/*
	Jitendra
	Date : 17thDec,2020
	***/
	function getProposalPolicyLead($id, $master_policy_id)
	{
		$data = $this->db->get_where('proposal_policy', array('lead_id' => $id, 'master_policy_id' => $master_policy_id))->result();
		return $data;
	}

	/*
	Author : Jitendra
	Date : 19th Dec, 2020
	***/
	function getCustomerDetails($customer_id)
	{
		$data = $this->db->get_where('master_customer', array('customer_id' => $customer_id))->result();
		return $data;
	}


	function getProposalPolicy1($id)
	{
		$data = $this->db->get_where('proposal_policy', array('proposal_details_id' => $id))->result();
		return $data;
	}
	function getPolicyPremium($id)
	{
		$data = $this->db->get_where('master_policy_premium', array('master_policy_id' => $id, 'isactive' => 1))->result();
		return $data;
	}


	function getProposalPolicyMember($id)
	{
		$data = $this->db->get_where('proposal_policy_member', array('policy_id' => $id))->result();
		return $data;
	}

	function getProposalPolicyMemberDetails($lead_id, $customer_id)
	{
		$this->db->select('relation_with_proposal,member_id');
		$this->db->from('proposal_policy_member_details');
		$this->db->where(array('lead_id' => $lead_id, 'customer_id' => $customer_id));
		$this->db->order_by('relation_with_proposal', 'ASC');
		$data = $this->db->get()->result_array();
		return $data;
	}

	function getProposalPolicyMemberByLeadId($lead_id, $customer_id)
	{
		//$data = $this->db->get_where('proposal_policy_member', array('lead_id' => $lead_id, 'customer_id' => $customer_id))->result();
		$data = $this->db->select('*')
			->from('proposal_policy_member as ppm')
			->join('proposal_policy_member_details as ppmd', 'ppmd.member_id = ppm.member_id')
			->where('ppmd.lead_id', $lead_id)
			->where('ppmd.customer_id', $customer_id)
			->order_by('relation_with_proposal', 'ASC')
			->get()
			->result_array();

		return $data;
	}

	function getProposalMemberDetails($lead_id)
	{
		$this->db->select('member_id');
		$this->db->where(array('lead_id' => $lead_id, 'relation_with_proposal' => 1));
		$data = $this->db->get('proposal_policy_member_details')->result();
		return $data;
	}

	function update_proposal_policy_member_details($data_member, $member_id)
	{
		$this->db->where('member_id', $member_id);
		$this->db->update('proposal_policy_member_details', $data_member);
		return 1;
	}


	protected function hydrateEmptyRates($rate)
	{
		$premium = $rate->premium_rate;
		$premium_with_tax = $rate->premium_with_tax;
		$tax_rate = (int) getConfigValue('tax_percent');

		if ($rate->is_taxable && empty($premium_with_tax) && !empty($premium)) {
			$premium_with_tax = $premium  * (100 + $tax_rate) / 100;
		} else if (!$rate->is_taxable && empty($premium_with_tax) && !empty($premium)) {
			$premium_with_tax = $premium;
			$premium  = $premium - ($premium * $tax_rate / 100); // Remove tax from premium
		} else if (!empty($premium_with_tax) && empty($premium)) {
			$premium  = $premium_with_tax - ($premium_with_tax * $tax_rate / 100);
		}

		$rate->premium_with_tax = number_format(ceil($premium_with_tax), 2, '.', '');
		$rate->premium_rate = number_format(round($premium, 2), 2, '.', '');

		return $rate;
	}

	public function getGHDQuestions()
	{
		$questions = $this->getdata1("ghd_questions", "*", "isactive=1");
		return $questions;
	}

	public function getPolicyAddedMembers($customer_id, $lead_id)
	{
		$sql = "SELECT member_id,relation_with_proposal, family_construct.member_type
			FROM proposal_policy_member_details as ppmd
			JOIN family_construct
			ON ppmd.relation_with_proposal = family_construct.id
			WHERE customer_id=$customer_id 
			AND lead_id=$lead_id";
		$query = $this->db->query($sql);

		return $query->result();
	}

	protected function roundUpRates($number)
	{
		for ($i = 5; $i >= 2; $i--) {
			$number = round($number, $i, PHP_ROUND_HALF_EVEN);
		}
		return $number;
	}

	function getPolicyPremiumFlat($data)
	{
		$response = [
			'status' => false,
			'messages' => [],
			'rate' => null
		];

		if ($messages = $this->validator($data)) {
			$response['messages'] = $messages;
			return $response;
		}

		if(in_array($data['policy_sub_type_id'], [2,3]) && $data['hospi_cash_group_code'] != ''){
			//echo $data['policy_sub_type_id'] ." : master_policy_id = " . $data['policy_id'] . " AND ".$data['group_code_type']." = '".$data['hospi_cash_group_code']."' AND isactive=1";echo "<br>";
			$rate = $this->getdata1("master_policy_premium", "*", "master_policy_id = " . $data['policy_id'] . " AND ".$data['group_code_type']." = '".$data['hospi_cash_group_code']."' AND isactive=1");
		}
		else{
			$rate = $this->getdata1("master_policy_premium", "*", " master_policy_id = " . $data['policy_id'] . " and sum_insured=" . $data['sum_insured'] . " and isactive=1");
		}

		if (empty($rate)) {
			$response['messages'][] = "No rates found for policy";
			return $response;
		}

		$rate = $rate[0];
		$rate = $this->hydrateEmptyRates($rate);

		$response['rate'] = $rate->premium_with_tax;
		$response['rate_without_tax'] = $rate->premium_rate;
		$response['status'] = true;
		$response['group_code'] = $rate->group_code;
		$response['group_code_spouse'] = $rate->group_code_spouse;
		return $response;
	}

	function getPolicyPerDayTenurePremium($data)
	{
		$response = [
			'status' => false,
			'messages' => [],
			'rate' => null
		];

		if ($messages = $this->validator($data)) {
			$response['messages'] = $messages;
			return $response;
		}

		if(in_array($data['policy_sub_type_id'], [2,3]) && $data['hospi_cash_group_code'] != ''){

			$rate = $this->getdata1("master_per_day_tenure_premiums", "*", "master_policy_id = " . $data['policy_id'] . " AND ".$data['group_code_type']." = '".$data['hospi_cash_group_code']."' AND isactive=1");
		}
		else{
			$rate = $this->getdata1("master_per_day_tenure_premiums", "*", " master_policy_id = " . $data['policy_id'] . " and sum_insured=" . $data['sum_insured'] . "  and tenure=" . $data['tenure'] . " and isactive=1");
		}

		if (empty($rate)) {
			$response['messages'][] = "No rates found for policy";
			return $response;
		}

		$rate = $rate[0];
		$rate = $this->hydrateEmptyRates($rate);

		$response['rate'] = $rate->premium_with_tax;
		$response['rate_without_tax'] = $rate->premium_rate;
		$response['status'] = true;
		$response['group_code'] = $rate->group_code;
		$response['group_code_spouse'] = $rate->group_code_spouse;

		return $response;
	}

	function getPolicyPremiumFamilyConstruct($data)
	{
		$response = [
			'status' => false,
			'messages' => [],
			'rate' => null
		];

		if ($messages = $this->validator($data)) {
			$response['messages'] = $messages;
			return $response;
		}

		if(in_array($data['policy_sub_type_id'], [2,3]) && $data['hospi_cash_group_code'] != ''){

			$rate = $this->getdata1("master_policy_premium", "*", "master_policy_id = " . $data['policy_id'] . " AND ".$data['group_code_type']." = '".$data['hospi_cash_group_code']."' AND isactive=1");
		}
		else{ //echo "master_policy_id = " . $data['policy_id'] . " AND sum_insured = " . $data['sum_insured'] . " AND adult_count = " . $data['adult_count'] . " AND child_count = " . $data['child_count'] . " and isactive=1";
			$rate = $this->apimodel->getdata1("master_policy_premium", "*", " master_policy_id = " . $data['policy_id'] . " AND sum_insured = " . $data['sum_insured'] . " AND adult_count = " . $data['adult_count'] . " AND child_count = " . $data['child_count'] . " and isactive=1");
		}

		if (empty($rate)) {
			$response['messages'][] = "No rates found for policy";
			return $response;
		}

		$rate = $rate[0];
		$rate = $this->hydrateEmptyRates($rate);

		$response['rate'] = $rate->premium_with_tax;
		$response['rate_without_tax'] = $rate->premium_rate;
		$response['status'] = true;
		$response['group_code'] = $rate->group_code;
		$response['group_code_spouse'] = $rate->group_code_spouse;

		return $response;
	}

	function getPolicyFamilyDeductable($data)
	{
		$response = [
			'status' => false,
			'messages' => [],
			'rate' => null
		];

		if ($messages = $this->validator($data)) {
			$response['messages'] = $messages;
			return $response;
		}

		try {

			if(in_array($data['policy_sub_type_id'], [2,3]) && $data['hospi_cash_group_code'] != ''){

				$rate = $this->getdata1("master_policy_premium", "*", "master_policy_id = " . $data['policy_id'] . " AND ".$data['group_code_type']." = '".$data['hospi_cash_group_code']."' AND isactive=1");
			}
			else{
				$rate = $this->apimodel->getdata1("master_policy_premium", "*", " master_policy_id = " . $data['policy_id'] . " AND sum_insured = " . $data['sum_insured'] . " AND adult_count = " . $data['adult_count'] . " AND child_count = " . $data['child_count'] . " AND deductable=" . $data['deductable'] . " and isactive=1");
			}
		} catch (\Exception $e) {
			$response['messages'][] = "An Error Ocurred while calculating premium";
			return $response;
		}


		if (empty($rate)) {
			$response['messages'][] = "No rates found for policy";
		}

		$rate = $rate[0];
		$rate = $this->hydrateEmptyRates($rate);

		$response['rate'] = $rate->premium_with_tax;
		$response['rate_without_tax'] = $rate->premium_rate;
		$response['status'] = true;
		$response['group_code'] = $rate->group_code;
		$response['group_code_spouse'] = $rate->group_code_spouse;

		return $response;
	}

	function getFamilyConstructAgeWisePremium($data)
	{
		$response = [
			'status' => false,
			'messages' => [],
			'rate' => null
		];

		if ($messages = $this->validator($data)) {
			$response['messages'] = $messages;
			return $response;
		}

		if(in_array($data['policy_sub_type_id'], [2,3]) && $data['hospi_cash_group_code'] != ''){

			$rate = $this->getdata1("master_policy_premium", "*", "master_policy_id = " . $data['policy_id'] . " AND ".$data['group_code_type']." = '".$data['hospi_cash_group_code']."' AND isactive=1 AND min_age <= " . $data['age'] . " AND max_age >= " . $data['age']);
		}
		else{
			$rate = $this->getdata1("master_policy_premium", "*", " master_policy_id = " . $data['policy_id'] . " AND adult_count = " . $data['adult_count'] . " AND 
			child_count = " . $data['child_count'] . " AND min_age <= " . $data['age'] . " AND max_age >= " . $data['age'] . " AND sum_insured=" . $data['sum_insured'] . " and isactive=1");
		}

		if (empty($rate)) {
			$response['messages'][] = "No rates found for policy";
			return $response;
		}

		$rate = $rate[0];
		$rate = $this->hydrateEmptyRates($rate);

		$response['rate'] = $rate->premium_with_tax;
		$response['rate_without_tax'] = $rate->premium_rate;
		$response['status'] = true;
		$response['group_code'] = $rate->group_code;
		$response['group_code_spouse'] = $rate->group_code_spouse;

		return $response;
	}

	function getPolicyMemberAgeWisePremium($data)
	{
		$response = [
			'status' => false,
			'messages' => [],
			'rate' => null
		];

		if ($messages = $this->validator($data)) {
			$response['messages'] = $messages;
			return $response;
		}

		if(in_array($data['policy_sub_type_id'], [2,3]) && $data['hospi_cash_group_code'] != ''){

			$rate = $this->getdata1("master_policy_premium", "*", "master_policy_id = " . $data['policy_id'] . " AND ".$data['group_code_type']." = '".$data['hospi_cash_group_code']."' AND isactive=1 AND min_age <= " . $data['age'] . " AND max_age >= " . $data['age']);
		}
		else{
			$rate = $this->getdata1("master_policy_premium", "*", " master_policy_id = " . $data['policy_id'] . " AND min_age <= " . $data['age'] . " AND max_age >= " . $data['age'] . " AND sum_insured=" . $data['sum_insured'] . " and isactive=1");
		}

		if (empty($rate)) {
			$response['messages'][] = "No rates found for policy";
			return $response;
		}

		$rate = $rate[0];
		$rate = $this->hydrateEmptyRates($rate);

		$response['rate'] = $rate->premium_with_tax;
		$response['rate_without_tax'] = $rate->premium_rate;
		$response['status'] = true;
		$response['group_code'] = $rate->group_code;
		$response['group_code_spouse'] = $rate->group_code_spouse;

		return $response;
	}

	function getPerMileWisePremium($data)
	{
		$response = [
			'status' => false,
			'messages' => [],
			'rate' => null
		];

		if ($messages = $this->validator($data)) {
			$response['messages'] = $messages;
			return $response;
		}
		$response = [
			'status' => false,
			'messages' => [],
			'rate' => null
		];

		if(in_array($data['policy_sub_type_id'], [2,3]) && $data['hospi_cash_group_code'] != ''){

			$sql = "SELECT * FROM master_policy_premium_permile WHERE min_age<=" . $data['age'] . " AND max_age>=" . $data['age'] . " AND ". $data['group_code_type'] ." = '".$data['hospi_cash_group_code']."' AND master_policy_id=" . $data['policy_id'] . " AND isactive=1";
		}
		else{

			if (isset($data['number_of_ci']) && $data['number_of_ci'] > 0) {
				$sql = "SELECT * FROM master_policy_premium_permile WHERE min_age<=" . $data['age'] . " AND max_age>=" . $data['age'] . " and numbers_of_ci=" . $data['number_of_ci'] . " and tenure=" . $data['tenure'] . " and master_policy_id=" . $data['policy_id'] . " and isactive=1";
			} else {
				$sql = "SELECT * FROM master_policy_premium_permile WHERE min_age<=" . $data['age'] . " AND max_age>=" . $data['age'] . " and tenure=" . $data['tenure'] . " and master_policy_id=" . $data['policy_id'] . " and isactive=1";
			}
		}

		$query = $this->db->query($sql);

		$rate = $query->result();

		if (empty($rate)) {
			$response['messages'][] = "No rates found for policy";
			return $response;
		}

		$amount = ($data['sum_insured'] / 1000) * $rate[0]->policy_rate;

		$final_rate = $amount  *  (100 + (int) getConfigValue('tax_percent')) / 100;

		$response['rate'] = number_format(ceil($final_rate), 2, '.', '');
		$response['rate_without_tax'] = number_format(round($amount, 2), 2, '.', '');
		$response['status'] = true;
		$response['group_code'] = $rate[0]->group_code;
		$response['group_code_spouse'] = $rate[0]->group_code_spouse;

		return $response;
	}

    function getPerMileWisePremium2($data)
    {
        
        //print_r($data);die;
        $response = [
            'status' => false,
            'messages' => [],
            'rate' => null
        ];

        if ($messages = $this->validator($data)) {
            $response['messages'] = $messages;
            return $response;
        }
        $response = [
            'status' => false,
            'messages' => [],
            'rate' => null
        ];

        if(in_array($data['policy_sub_type_id'], [2,3]) && $data['hospi_cash_group_code'] != ''){

            $sql = "SELECT * FROM master_policy_premium_permile WHERE min_age<=" . $data['age'] . " AND max_age>=" . $data['age'] . " AND master_policy_id=" . $data['policy_id'] . " AND isactive=1";
        }
        else{

            if (isset($data['number_of_ci']) && $data['number_of_ci'] > 0) {
                $sql = "SELECT * FROM master_policy_premium_permile WHERE min_age<=" . $data['age'] . " AND max_age>=" . $data['age'] . " and numbers_of_ci=" . $data['number_of_ci'] . " and tenure=" . $data['tenure'] . " and master_policy_id=" . $data['policy_id'] . " and isactive=1";
            } else {
                $sql = "SELECT * FROM master_policy_premium_permile WHERE min_age<=" . $data['age'] . " AND max_age>=" . $data['age'] . " and tenure=" . $data['tenure'] . " and master_policy_id=" . $data['policy_id'] . " and isactive=1";
            }
        }

        $query = $this->db->query($sql);

        $rate = $query->result();
        if (empty($rate)) {
            $response['messages'][] = "No rates found for policy";
            return $response;
        }

        $amount = ($data['sum_insured'] / 1000) * $rate[0]->policy_rate;

        $final_rate = $amount  *  (100 + (int) getConfigValue('tax_percent')) / 100;

        $response['rate'] = number_format(ceil($final_rate), 2, '.', '');
        $response['rate_without_tax'] = number_format(round($amount, 2), 2, '.', '');
        $response['status'] = true;
        $response['group_code'] = $rate[0]->group_code;
        $response['group_code_spouse'] = $rate[0]->group_code_spouse;

        return $response;
    }

	function getProposalPolicyMemberLead($id, $lead_id, $customer_id, $member_id = '')
	{

		if ($member_id == '') {

			//$data = $this->db->get_where('proposal_policy_member', array('policy_id' => $id, 'lead_id' => $lead_id, 'customer_id' => $customer_id))->result();\
			$data = $this->db->select('ppm.member_id, ppm.policy_id, ppm.lead_id, ppmd.customer_id')
				->from('proposal_policy_member as ppm')
				->join('proposal_policy_member_details as ppmd', 'ppmd.member_id = ppm.member_id')
				->where('ppmd.lead_id', $lead_id)
				->where_in('ppm.policy_id', $id)
				->where('ppmd.customer_id', $customer_id)
				->get()
				->result_array();
		} else {

			//$data = $this->db->get_where('proposal_policy_member', array('policy_id' => $id, 'lead_id' => $lead_id, 'customer_id' => $customer_id, 'member_id' => $member_id))->result();
			$data = $this->db->select('ppm.member_id, ppm.policy_id, ppm.lead_id, ppmd.customer_id')
				->from('proposal_policy_member as ppm')
				->join('proposal_policy_member_details as ppmd', 'ppmd.member_id = ppm.member_id')
				->where('ppmd.lead_id', $lead_id)
				->where_in('ppm.policy_id', $id)
				->where('ppmd.member_id', $member_id)
				->where('ppmd.customer_id', $customer_id)
				->get()
				->result_array();
		}

		return $data;
	}

	function select_member_quote_details_by_lead_customer_member_type_id($lead_id, $customer_id)
	{
		$this->db->select('*');
		$this->db->from('master_quotes');
		$this->db->where(['lead_id' => $lead_id, 'master_customer_id' => $customer_id]);
		$data = $this->db->get();

		return $data->result();
	}

	function select_member_details($member_id, $lead_id, $customer_id)
	{

		$data = $this->db->get_where('proposal_policy_member_details', array('member_id' => $member_id, 'lead_id' => $lead_id, 'customer_id' => $customer_id))->result();

		return $data;
	}

	function getTenureForPolicies($policies)
	{
		$sql = "SELECT distinct tenure FROM master_policy_premium_permile WHERE master_policy_id in (" . implode(',', $policies) . ")";

		$query = $this->db->query($sql);

		return  $query->result();
	}

	function validator($array)
	{
		$validationMessages = [];

		foreach ($array as $key => $value) {
			if ($key == 'child_count' || $key == 'number_of_ci' || $key == 'hospi_cash_group_code' || $key == 'group_code_type') continue;
			$value = trim($value);
			if (!$value) {
				$validationMessages[] = "Invalid $key";
			}
		}

		return $validationMessages;
	}

	function getProposersAge($lead_id, $customer_id)
	{
		$age = null;
		$data = $this->getLeadDetails($lead_id);
		foreach ($data as $customer) {
			if ($customer->customer_id == $customer_id) {
				$age = date_diff(date_create($data[0]->dob), date_create('today'))->y;
				break;
			}
		}

		return $age;
	}

	function getGeneratedQuote($data)
	{
		$response = [
			'family_construct' => '',
			'ghi_cover' => "",
			'pa_cover' => "",
			'ci_cover' => "",
			'spouse_age' => "",
			'hospi_cash' => "",
			'tenure' => ""
		];


		$data = $this->db->get_where('master_quotes', array('master_customer_id' => $data['customer_id'], 'lead_id' => $data['lead_id']))->result();

		foreach ($data as $key => $policy) {
			$policyDetails = $this->getPolicyDetails($policy->master_policy_id)[0];

			if ($policyDetails->policy_sub_type_id == 1) {
				$response['ghi_cover'] = $data[$key]->sum_insured;
			}

			if ($policyDetails->policy_sub_type_id == 2) {
				$response['pa_cover'] = $data[$key]->sum_insured;
			}

			if ($policyDetails->policy_sub_type_id == 3) {
				$response['ci_cover'] = $data[$key]->sum_insured;
				$response['number_of_ci'] = $data[$key]->number_of_ci;
			}

			if ($policyDetails->policy_sub_type_id == 6) {
				$response['hospi_cash'] = $data[$key]->sum_insured;
			}

			if ($policyDetails->policy_sub_type_id == 5) {
				$response['super_top_up_cover'] = $data[$key]->sum_insured;
				$response['deductable'] = $data[$key]->deductable;
			}

			$response['family_construct'] =  $policy->family_construct;
			$response['spouse_age'] =  $policy->spouse_age;
			$response['spouse_dob'] =  $policy->spouse_dob;
			$response['tenure'] =  $policy->tenure;
		}

		return  $response;
	}

	function getGeneratedPremiums($data)
	{
		$response = [];

		$this->db->select('*');
		$where = "lead_id=" . $data['lead_id'] . " and premium_with_tax is NOT NULL";
		$this->db->where($where);
		$data = $this->db->get('master_quotes')->result();
		foreach ($data as $key => $policy) {
			$policyDetails = $this->getPolicyDetails($policy->master_policy_id)[0];

			$members = $this->getMembers();

			$member = array_filter($members, function ($member) use ($policy) {
				if ($policy->member_type_id == $member->id) return true;
			});

			$member = current($member);

			$premium = number_format(round($data[$key]->premium_with_tax, 2), 2, '.', '');

			if ($policyDetails->suminsured_type_id == 1) {
				$response[$policy->master_customer_id][$policyDetails->policy_sub_type_code . "-" . $member->member_type] =
					$premium;
			} else {
				$response[$policy->master_customer_id][$policyDetails->policy_sub_type_code] = 	$premium;
			}
		}

		return  $response;
	}


	function getPlanProposal($id, $lead_id)
	{
		$data = $this->db->get_where('proposal_details', array('plan_id' => $id, 'lead_id' => $lead_id))->result();
		return $data;
	}
	function getPolicyPremiumBasis($id)
	{
		$data = $this->db->get_where('master_policy_premium_basis_mapping', array('master_policy_id' => $id, 'isactive' => 1))->result();
		return $data;
	}
	function getPolicySiType($id)
	{
		$data = $this->db->get_where('master_policy_si_type_mapping', array('master_policy_id' => $id, 'isactive' => 1))->result();
		return $data;
	}

	function getPolicyFamilyDetails($id)
	{
		$this->db->select('*');
		$this->db->from('master_policy_family_construct');
		$this->db->join('family_construct', 'master_policy_family_construct.member_type_id =family_construct.id');

		if (!is_array($id)) {

			$this->db->where(array('master_policy_id' => $id, 'master_policy_family_construct.isactive' => 1));
		} else {

			$this->db->where_in('master_policy_id', $id);
			$this->db->where(array('master_policy_family_construct.isactive' => 1));
		}

		$this->db->order_by("member_type_id", "asc");

		$query = $this->db->get();
		return $query->result();
	}

	function getPolicyFamilyConstruct($id)
	{
		$data = $this->db->get_where('master_policy_family_construct', array('master_policy_id' => $id, 'isactive' => 1))->result();
		return $data;
	}
	function getsubtypenamebyid($id)
	{
		$data = $this->db->get_where('master_policy_sub_type', array('policy_sub_type_id' => $id))->row()->policy_sub_type_name;
		return $data;
	}
	function getPolicyDeclaration($id)
	{
		$data = $this->db->get_where('ghd_declaration', array('plan_id' => $id, 'is_active' => 1))->row();
		return $data;
	}
	function getAssignmentDeclaration($id)
	{
		$data = $this->db->get_where('assignment_declaration', array('plan_id' => $id, 'is_active' => 1))->row();
		return $data;
	}
	function checkleadproposal($id)
	{
		$data = $this->db->get_where('proposal_details', array('proposal_details_id' => $id))->num_rows();
		return $data;
	}
	function checkproposalpolicy($id, $policy, $details_id)
	{
		$data = $this->db->get_where('proposal_policy', array('lead_id' => $id, 'master_policy_id' => $policy, 'proposal_details_id' => $details_id))->result();
		return $data;
	}
	function inactivateRecord($table, $cond)
	{
		$this->db->where($cond);
		$this->db->update($table, array('isactive' => 0));
		return 1;
	}

	function select_member_quote_details_by_lead_customer_policy_id($lead_id, $customer_id, $policy_id_arr)
	{
		$this->db->select('premium,premium_with_tax');
		$this->db->from('master_quotes');
		$this->db->where(['lead_id' => $lead_id, 'master_customer_id' => $customer_id]);
		$this->db->where_in('master_policy_id', $policy_id_arr);
		$data = $this->db->get();

		return $data->result();
	}

	function getMemberIdAndRelation($lead_id, $customer_id)
	{
		$this->db->select('member_id,relation_with_proposal');
		$this->db->from('proposal_policy_member_details');
		$this->db->where(['lead_id' => $lead_id, 'customer_id' => $customer_id]);
		$data = $this->db->get();

		return $data->result();
	}

	function select_member_quote_details($quote_ids)
	{

		$this->db->select('*');
		$this->db->from('master_quotes');
		$this->db->where_in('master_quote_id', $quote_ids);
		$data = $this->db->get();

		return $data->result();
	}

	function insertData($tbl_name, $data_array, $sendid = NULL)
	{
		$this->db->insert($tbl_name, $data_array);
		$result_id = $this->db->insert_id();

		if ($sendid == 1) {
			//return id
			return $result_id;
		}

		return;
	}

	/*
		 * Fetch records from multiple tables [Join Queries] with multiple condition, Sorting, Limit, Group By
	*/
	function getdata_join($main_table = array(), $join_tables = array(), $condition = null, $sort_by = null, $group_by = null)
	{
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
	public function get_quote_data($lead_id, $emp_id, $master_policy_id, $proposal_policy_id, $proposal_details, $policy_sub_type_id, $sum_insured, $count = 1)
	{

		//get proposal policy details
		$data = array();
		$data['customer_data'] = (array)$this->get_profile($emp_id);
		$data['member_data'] = (array)$this->get_all_member_data($proposal_policy_id);

		$nominees = array();
		if (!empty($proposal_details)) {
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
		$getApiUrl = $this->db->query("select api_url from master_policy_sub_type where policy_sub_type_id='" . $policy_sub_type_id . "' ")->row_array();

		$url = trim($getApiUrl['api_url']);

		if ($url == '') {
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

		$explode_name = array($data['customer_data']['first_name'], $data['customer_data']['middle_name'], $data['customer_data']['last_name']);
		$explode_name_nominee = array($data['nominee_data']['nominee_first_name'], $data['nominee_data']['nominee_last_name']);

		for ($i = 0; $i < $totalMembers; $i++) {
			//Checking relation based on self, spouse, son and daughter
			if ($data['member_data'][$i]['relation_with_proposal'] == 1) {
				$data['member_data'][$i]['relation_code'] = "R001";
			} else if ($data['member_data'][$i]['relation_with_proposal'] == 2) {
				$data['member_data'][$i]['relation_code'] = "R002";
			} else if ($data['member_data'][$i]['relation_with_proposal'] == 3) {
				$data['member_data'][$i]['relation_code'] = "R003";
			} else {
				$data['member_data'][$i]['relation_code'] = "R004";
			}

			//Nominee relation code
			if ($data['nominee_data']['nominee_relation'] == 1) {
				$data['nominee_data']['relation_code'] = "R001";
			} else if ($data['nominee_data']['nominee_relation'] == 2) {
				$data['nominee_data']['relation_code'] = "R002";
			} else if ($data['nominee_data']['nominee_relation'] == 3) {
				$data['nominee_data']['relation_code'] = "R003";
			} else {
				$data['nominee_data']['relation_code'] = "R004";
			}


			$abc = ["PEDCode" => null, "Remarks" => null];

			$explode_name_member = explode(" ", trim($data['member_data'][$i]['policy_member_first_name']), 2);


			$member[] = ["MemberNo" => $i + 1, "Salutation" => (($data['member_data'][$i]['policy_member_gender'] == "Male") ? 'Mr' : (($data['member_data'][$i]['policy_member_age'] >= 18) ? 'Mrs' : 'Ms')), "First_Name" => $data['member_data'][$i]['policy_member_first_name'], "Middle_Name" => null, "Last_Name" => !empty($data['member_data'][$i]['policy_member_last_name']) ? $data['member_data'][$i]['policy_member_last_name'] : '.', "Gender" => ($data['member_data'][$i]['policy_member_gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['policy_member_dob'])), "Relation_Code" => trim($data['member_data'][$i]['relation_code']), "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => "O553", "PrimaryMember" => (($i == 0) ? "Y" : "N"), "MemberproductComponents" => [["PlanCode" => $plan_code, "MemberQuestionDetails" => [["QuestionCode" => null, "Answer" => null, "Remarks" => null]]]], "MemberPED" => $abc, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $explode_name_nominee[0], "Nominee_Last_Name" => !empty($explode_name_nominee[1]) ? $explode_name_nominee[1] : '.', "Nominee_Contact_Number" => (!empty($data['nominee_data']['nominee_contact'])) ? $data['nominee_data']['nominee_contact'] : null, "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data']['relation_code']),];
		}

		//echo "<pre>";print_r($member);exit;
		$data['proposal_data']['SourceSystemName_api'] = 'CreditorPortal';


		//echo $policy_detail_id;exit;
		if ($policy_sub_type_id == 1) { // ghi
			$fqrequest = ["ClientCreation" => ["Member_Customer_ID" =>  $Member_Customer_ID, "salutation" => $data['customer_data']['salutation'], "firstName" => $explode_name[0], "middleName" => "", "lastName" => !empty($explode_name[1]) ? $explode_name[1] : '.', "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['dob'])), "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['pincode'], "uidNo" => $uidNo, "maritalStatus" => null, "nationality" => "Indian", "occupation" => $occupation, "primaryEmailID" => $data['customer_data']['email_id'], "contactMobileNo" => substr(trim($data['customer_data']['mobile_no']), -10), "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => null, "remarks" => null, "startDate" => date('Y-m-d'), "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address_line1'], "homeAddressLine2" => null, "homeAddressLine3" => null, "homePinCode" => $data['customer_data']['pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => "", "MasterPolicyNumber" => $MasterPolicyNumber, "GroupID" => $GroupID, "Product_Code" => $Product_Code, "intermediaryCode" => $intermediaryCode, "AutoRenewal" => $AutoRenewal, "intermediaryBranchCode" => $intermediaryBranchCode, "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $leadID, "Source_Name" => $data['proposal_data']['SourceSystemName_api'], "SPID" => $SPID, "TCN" => null, "CRTNO" => null, "RefCode1" => $RefCode1, "RefCode2" => $RefCode2, "Employee_Number" => $data['customer_data']['customer_id'], "enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "0", "PaymentMode" => "", "PolicyproductComponents" => [["PlanCode" => $plan_code, "SumInsured" => $SumInsured, "SchemeCode" => $SchemeCode]]], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "", "modeOfEntry" => "", "cdAcNo" => null, "expiryDate" => null, "payerType" => "", "payerCode" => null, "paymentBy" => "", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => "", "collectionRcvdDate" => null, "collectionMode" => "", "remarks" => null, "instrumentNumber" => null, "instrumentDate" => null, "bankName" => null, "branchName" => null, "bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => "", "PaymentGatewayName" => "", "TerminalID" => "", "CardNo" => null]];
		} else {
			//group policies
			$fqrequest = ["ClientCreation" => ["Member_Customer_ID" => $Member_Customer_ID, "salutation" => $data['customer_data']['salutation'], "firstName" => $explode_name[0], "middleName" => "", "lastName" => !empty(trim($explode_name[1])) ? $explode_name[1] : '.', "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['dob'])), "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['pincode'], "uidNo" => $uidNo, "maritalStatus" => null, "nationality" => "Indian", "occupation" => $occupation, "primaryEmailID" => $data['customer_data']['email_id'], "contactMobileNo" => substr(trim($data['customer_data']['mobile_no']), -10), "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => null, "remarks" => null, "startDate" => date('Y-m-d'), "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address_line1'], "homeAddressLine2" => null, "homeAddressLine3" => null, "homePinCode" => $data['customer_data']['pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => null, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => "", "MasterPolicyNumber" => $MasterPolicyNumber, "GroupID" => $GroupID, "Product_Code" => $Product_Code, "SumInsured_Type" => $SumInsured_Type, "Policy_Tanure" => $Policy_Tanure, "Member_Type_Code" => $Member_Type_Code, "intermediaryCode" => $intermediaryCode, "AutoRenewal" => $AutoRenewal, "intermediaryBranchCode" => $intermediaryBranchCode, "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $leadID, "Source_Name" => $data['proposal_data']['SourceSystemName_api'], "SPID" => $SPID, "TCN" => null, "CRTNO" => null, "RefCode1" => $RefCode1, "RefCode2" => $RefCode2, "Employee_Number" => $data['customer_data']['customer_id'], "enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "0", "PaymentMode" => null, "PolicyproductComponents" => ["PlanCode" => $plan_code, "SumInsured" => $SumInsured, "SchemeCode" => $SchemeCode]], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "", "modeOfEntry" => "", "cdAcNo" => null, "expiryDate" => null, "payerType" => "", "payerCode" => null, "paymentBy" => "", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => "", "collectionRcvdDate" => null, "collectionMode" => "", "remarks" => null, "instrumentNumber" => null, "instrumentDate" => null, "bankName" => null, "branchName" => null, "bankLocation" => null, "micrNo" => null, "chequeType" => null, "ifscCode" => "", "PaymentGatewayName" => "", "TerminalID" => "", "CardNo" => null]];
		}

		//echo "<pre>";print_r($fqrequest);exit;

		$request_arr = ["lead_id" => $leadID, "req" => json_encode($fqrequest), "product_id" => $product_id, "type" => "quick_quote_request", "proposal_policy_id" => $proposal_policy_id];

		$this->db->insert("logs_docs", $request_arr);
		$insert_id = $this->db->insert_id();

		//Application log entries
		$this->db->insert("application_logs", [
			"lead_id" => $leadID,
			"action" => "quick_quote_request",
			"request_data" => json_encode($fqrequest),
			"created_on" => date("Y-m-d H:i:s")
		]);
		$app_log_insert_id = $this->db->insert_id();

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($fqrequest),
			CURLOPT_HTTPHEADER => array(
				"Accept: */*",
				"Cache-Control: no-cache",
				"Connection: keep-alive",
				"Content-Length: " . strlen(json_encode($fqrequest)),
				"Content-Type: application/json",
				"Host: bizpre.adityabirlahealth.com"
			),
		));


		$response = curl_exec($curl);

		$err = curl_error($curl);
		if ($response == '' || $response == NULL) {
			$response = $err;
		}

		//echo "<pre>";print_r($response);exit;

		$request_arr = ["res" => json_encode($response)];
		$this->db->where("id", $insert_id);
		$this->db->update("logs_docs", $request_arr);

		//Application log entries
		$app_log_res_arr = ["response_data" => json_encode($response)];
		$this->db->where("log_id", $app_log_insert_id);
		$this->db->update("application_logs", $app_log_res_arr);

		curl_close($curl);

		if ($err) {
			return array(
				"status" => "error",
				"msg" => $err
			);
			if ($count <= 3) {
				sleep(15);
				$this->get_quote_data($lead_id, $emp_id, $master_policy_id, $proposal_policy_id, $proposal_details, $policy_sub_type_id, $sum_insured, $count++);
			}
		} else {

			$new = simplexml_load_string($response);
			$con = json_encode($new);
			$newArr = json_decode($con, true);
			$errorObj = $newArr['errorObj'];
			//print_pre($errorObj);exit;
			if ($errorObj['ErrorNumber'] == '00') {
				$policydetail = $newArr['policyDtls'];

				$arr = ["emp_id" => $emp_id, "lead_id" => $lead_id, "proposal_policy_id" => $proposal_policy_id, "policy_subtype_id" => $policy_sub_type_id, "QuotationNumber" => $policydetail['QuotationNumber'], "PolicyNumber" => $policydetail['PolicyNumber'], "status" => "success"];

				$query = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' AND proposal_policy_id='$proposal_policy_id'")->row_array();

				if ($query > 0) {
					$this->db->where("emp_id", $emp_id);
					$this->db->where("lead_id", $lead_id);
					$this->db->where("proposal_policy_id", $proposal_policy_id);

					$this->db->update("ghi_quick_quote_response", $arr);
				} else {
					$this->db->insert("ghi_quick_quote_response", $arr);
				}


				return array(
					"status" => "Success",
					"msg" => $policydetail['QuotationNumber']
				);
			} else {

				$query = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' AND proposal_policy_id='$proposal_policy_id'")->row_array();
				if ($query > 0) {
					$arr = ["count" => $query['count'] + 1, "status" => "error"];
					$this->db->where("emp_id", $emp_id);
					$this->db->where("lead_id", $lead_id);
					$this->db->where("proposal_policy_id", $proposal_policy_id);
					$this->db->update("ghi_quick_quote_response", $arr);
				} else {
					$arr = ["emp_id" => $emp_id, "status" => "error"];
					$this->db->insert("ghi_quick_quote_response", $arr);
				}

				return array(
					"status" => "error",
					"msg" => $errorObj['ErrorMessage']
				);
				if ($count <= 3) {
					sleep(15);
					$this->get_quote_data($lead_id, $emp_id, $master_policy_id, $proposal_policy_id, $proposal_details, $policy_sub_type_id, $sum_insured, $count++);
				}
			}
		}
	}


	//Full Quote Request
	public function get_full_quote_data($lead_id, $emp_id, $master_policy_id, $proposal_policy_id, $proposal_details, $policy_sub_type_id, $sum_insured)
	{

		//get proposal policy details
		$data = array();
		$data['customer_data'] = (array)$this->get_profile($emp_id);
		$data['member_data'] = (array)$this->get_all_member_data($proposal_policy_id);
		$nominees = array();
		if (!empty($proposal_details)) {
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
		$getApiUrl = $this->db->query("select api_url from master_policy_sub_type where policy_sub_type_id='" . $policy_sub_type_id . "' ")->row_array();

		$url = trim($getApiUrl['api_url']);

		if ($url == '') {
			return array(
				"status" => "error",
				"msg" => "Something Went Wrong"
			);
			exit;
		}

		//Get Policy Details
		$policyDetails = $this->db->query("select * from proposal_policy where proposal_policy_id='" . $proposal_policy_id . "' ")->row_array();

		$explode_name = explode(" ", trim($data['customer_data']['customer_name']), 2);

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

		$collectionAmount = ($policyDetails['premium_amount'] + $tax_amount);

		$transaction_date = explode(" ", $data['proposal_data'][0]['transaction_date']);
		$trans_date = date("Y-m-d", strtotime($transaction_date[0]));
		//online,RTGS/ NEFT,Cheque
		$collectionMode = (!empty($data['proposal_data'][0]['mode_of_payment'])) ? $data['proposal_data'][0]['mode_of_payment'] : 2;

		$SPID = 0;
		$RefCode1 = 0;
		$RefCode2 = 0;
		$Policy_Tanure = 1;
		$AutoRenewal = 'Y';


		$query_quote = [];

		if ($policy_sub_type_id == 1) {
			$data['customer_data']['customer_id'] = $data['customer_data']['customer_id'] . "GHI";

			$query_quote = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and lead_id='$lead_id' and proposal_policy_id='$proposal_policy_id' and status = 'success'")->row_array();

			/*if(empty($query_quote)){
				$quote_data = $this->get_quote_data($emp_id, $policy_no);
				if($quote_data['status'] == 'Success')
				{
					$query_quote['QuotationNumber'] = $quote_data['msg'];
				}
			}*/
		} else {


			$query_quote = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and lead_id='$lead_id' and proposal_policy_id='$proposal_policy_id' and status = 'success' ")->row_array();

			/*if(empty($query_quote)){
				$quote_data = $this->get_quote_data($emp_id, $policy_no, $mem_data);
				if($quote_data['status'] == 'Success')
				{
					$query_quote['QuotationNumber'] = $quote_data['msg'];
				}
			}*/

			if ($policy_sub_type_id == 2) {
				$concat_string = "GPA";
			} elseif ($policy_sub_type_id == 3) {
				$concat_string = "GCI";
			} elseif ($policy_sub_type_id == 7) {
				$concat_string = "GP";
			}

			$data['customer_data']['customer_id'] = $data['customer_data']['cust_id'] . $concat_string;
		}

		if ($query_quote['QuotationNumber']) {

			$totalMembers = count($data['member_data']);
			$member = [];

			$explode_name = array($data['customer_data']['first_name'], $data['customer_data']['middle_name'], $data['customer_data']['last_name']);
			$explode_name_nominee = array($data['nominee_data']['nominee_first_name'], $data['nominee_data']['nominee_last_name']);

			for ($i = 0; $i < $totalMembers; $i++) {

				//Checking relation based on self, spouse, son and daughter
				if ($data['member_data'][$i]['relation_with_proposal'] == 1) {
					$data['member_data'][$i]['relation_code'] = "R001";
				} else if ($data['member_data'][$i]['relation_with_proposal'] == 2) {
					$data['member_data'][$i]['relation_code'] = "R002";
				} else if ($data['member_data'][$i]['relation_with_proposal'] == 3) {
					$data['member_data'][$i]['relation_code'] = "R003";
				} else {
					$data['member_data'][$i]['relation_code'] = "R004";
				}

				//Nominee relation code
				if ($data['nominee_data']['nominee_relation'] == 1) {
					$data['nominee_data']['relation_code'] = "R001";
				} else if ($data['nominee_data']['nominee_relation'] == 2) {
					$data['nominee_data']['relation_code'] = "R002";
				} else if ($data['nominee_data']['nominee_relation'] == 3) {
					$data['nominee_data']['relation_code'] = "R003";
				} else {
					$data['nominee_data']['relation_code'] = "R004";
				}


				$abc = ["PEDCode" => null, "Remarks" => null];

				$member[] = ["MemberNo" => $i + 1, "Salutation" => $data['member_data'][$i]['policy_member_salutation'], "First_Name" => $data['member_data'][$i]['policy_member_first_name'], "Middle_Name" => null, "Last_Name" => !empty($data['member_data'][$i]['policy_member_last_name']) ? $data['member_data'][$i]['policy_member_last_name'] : '.', "Gender" => ($data['member_data'][$i]['policy_member_gender'] == "Male") ? "M" : "F", "DateOfBirth" => date('m/d/Y', strtotime($data['member_data'][$i]['policy_member_dob'])), "Relation_Code" => trim($data['member_data'][$i]['relation_code']), "Marital_Status" => null, "height" => "0.00", "weight" => "0", "occupation" => "O553", "PrimaryMember" => (($i == 0) ? "Y" : "N"), "MemberproductComponents" => [["PlanCode" => $plan_code, "MemberQuestionDetails" => [["QuestionCode" => null, "Answer" => null, "Remarks" => null]]]], "MemberPED" => $abc, "exactDiagnosis" => null, "dateOfDiagnosis" => null, "lastDateConsultation" => null, "detailsOfTreatmentGiven" => null, "doctorName" => null, "hospitalName" => null, "phoneNumberHosital" => null, "Nominee_First_Name" => $explode_name_nominee[0], "Nominee_Last_Name" => !empty($explode_name_nominee[1]) ? $explode_name_nominee[1] : '.', "Nominee_Contact_Number" => $data['nominee_data']['nominee_contact'], "Nominee_Home_Address" => null, "Nominee_Relationship_Code" => trim($data['nominee_data']['relation_code']),];
			}

			$data['proposal_data']['SourceSystemName_api'] = 'CreditorPortal';


			$fqrequest = ["ClientCreation" => ["Member_Customer_ID" => $data['customer_data']['customer_id'], "salutation" => $data['customer_data']['salutation'], "firstName" => $data['customer_data']['first_name'], "middleName" => "", "lastName" => !empty($data['customer_data']['last_name']) ? $data['customer_data']['last_name'] : '.', "dateofBirth" => date('m/d/Y', strtotime($data['customer_data']['dob'])), "gender" => ($data['customer_data']['gender'] == "Male") ? "M" : "F", "educationalQualification" => null, "pinCode" => $data['customer_data']['pincode'], "uidNo" => $uidNo, "maritalStatus" => null, "nationality" => "Indian", "occupation" => "O553", "primaryEmailID" => $data['customer_data']['email_id'], "contactMobileNo" => substr(trim($data['customer_data']['mobile_no']), -10), "stdLandlineNo" => null, "panNo" => null, "passportNumber" => null, "contactPerson" => null, "annualIncome" => null, "remarks" => null, "startDate" => date('Y-m-d'), "endDate" => null, "IdProof" => "Adhaar Card", "residenceProof" => null, "ageProof" => null, "others" => null, "homeAddressLine1" => $data['customer_data']['address_line1'], "homeAddressLine2" => null, "homeAddressLine3" => null, "homePinCode" => $data['customer_data']['pincode'], "homeArea" => null, "homeContactMobileNo" => null, "homeContactMobileNo2" => null, "homeSTDLandlineNo" => null, "homeFaxNo" => null, "sameAsHomeAddress" => "1", "mailingAddressLine1" => null, "mailingAddressLine2" => null, "mailingAddressLine3" => null, "mailingPinCode" => null, "mailingArea" => null, "mailingContactMobileNo" => null, "mailingContactMobileNo2" => null, "mailingSTDLandlineNo" => null, "mailingSTDLandlineNo2" => null, "mailingFaxNo" => null, "bankAccountType" => null, "bankAccountNo" => null, "ifscCode" => $ifscCode, "GSTIN" => null, "GSTRegistrationStatus" => "Consumers", "IsEIAavailable" => "0", "ApplyEIA" => "0", "EIAAccountNo" => null, "EIAWith" => "0", "AccountType" => null, "AddressProof" => null, "DOBProof" => null, "IdentityProof" => null], "PolicyCreationRequest" => ["Quotation_Number" => $query_quote['QuotationNumber'], "MasterPolicyNumber" => $MasterPolicyNumber, "GroupID" => $GroupID, "Product_Code" => $Product_Code, "SumInsured_Type" => $SumInsured_Type, "Policy_Tanure" => $Policy_Tanure, "Member_Type_Code" => $Member_Type_Code, "intermediaryCode" => $intermediaryCode, "AutoRenewal" => $AutoRenewal, "intermediaryBranchCode" => $intermediaryBranchCode, "agentSignatureDate" => null, "Customer_Signature_Date" => null, "businessSourceChannel" => null, "AssignPolicy" => "0", "AssigneeName" => null, "leadID" => $leadID, "Source_Name" => $source_name, "SPID" => $SPID, "TCN" => null, "CRTNO" => null, "RefCode1" => $RefCode1, "RefCode2" => $RefCode2, "Employee_Number" => $data['customer_data']['customer_id'], "enumIsEmployeeDiscount" => null, "QuoteDate" => null, "IsPayment" => "1", "PaymentMode" => $collectionMode, "PolicyproductComponents" => [["PlanCode" => $plan_code, "SumInsured" => $SumInsured, "SchemeCode" => $SchemeCode]]], "MemObj" => ["Member" => $member], "ReceiptCreation" => ["officeLocation" => "Mumbai", "modeOfEntry" => "Direct", "cdAcNo" => null, "expiryDate" => null, "payerType" => "Customer", "payerCode" => null, "paymentBy" => "Customer", "paymentByName" => null, "paymentByRelationship" => null, "collectionAmount" => round($collectionAmount, 2), "collectionRcvdDate" => $trans_date, "collectionMode" => $collectionMode, "remarks" => null, "instrumentNumber" => $txnRefNumber, "instrumentDate" => $trans_date, "bankName" => $bankName, "branchName" => $branchName, "bankLocation" => $bankLocation, "micrNo" => $micrNo, "chequeType" => $chequeType, "ifscCode" => $ifscCode, "PaymentGatewayName" => $source_name, "TerminalID" => $terminal_id, "CardNo" => null]];

			//print_pre($fqrequest);exit;
			//Monolog::saveLog("full_quote_request2", "I", json_encode($fqrequest));

			$request_arr = ["lead_id" => $leadID, "req" => json_encode($fqrequest), "product_id" => $product_id, "type" => "full_quote_request", "proposal_policy_id" => $proposal_policy_id];

			$this->db->insert("logs_docs", $request_arr);
			$insert_id = $this->db->insert_id();

			//Application log entries
			$this->db->insert("application_logs", [
				"lead_id" => $leadID,
				"action" => "full_quote_request",
				"request_data" => json_encode($fqrequest),
				"created_on" => date("Y-m-d H:i:s")
			]);
			$app_log_insert_id = $this->db->insert_id();

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 90,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => json_encode($fqrequest),
				CURLOPT_HTTPHEADER => array(
					"Accept: */*",
					"Cache-Control: no-cache",
					"Connection: keep-alive",
					"Content-Length: " . strlen(json_encode($fqrequest)),
					"Content-Type: application/json",
					"Host: bizpre.adityabirlahealth.com"
				),
			));

			$response = curl_exec($curl);

			//Monolog::saveLog("full_quote_reponse2", "I", json_encode($response));

			$request_arr = ["res" => json_encode($response)];
			$this->db->where("id", $insert_id);
			$this->db->update("logs_docs", $request_arr);

			//Application log entries
			$app_log_request_arr = ["response_data" => json_encode($response)];
			$this->db->where("log_id", $app_log_insert_id);
			$this->db->update("application_logs", $app_log_request_arr);

			$err = curl_error($curl);
			//echo "<pre>";print_r($response);exit;
			curl_close($curl);

			if ($err) {
				return array(
					"status" => "error",
					"msg" => $err
				);
			} else {
				$new = simplexml_load_string($response);
				$con = json_encode($new);
				$newArr = json_decode($con, true);

				$errorObj = $newArr['errorObj'];
				$premium = $newArr['premium'];
				$receiptObj = $newArr['receiptObj'];
				$return_data = [];

				if ($errorObj['ErrorNumber'] == '00' || ($errorObj['ErrorNumber'] == '302' && $master_policy_id == $newArr['policyDtls']['PolicyNumber'])) {

					$return_data['status'] = 'Success';
					$return_data['msg'] = $errorObj['ErrorMessage'];

					$api_insert = array(
						"emp_id" => $emp_id,
						"client_id" => $newArr['policyDtls']['ClientID'],
						"certificate_number" => $newArr['policyDtls']['CertificateNumber'],
						"quotation_no" => $newArr['policyDtls']['QuotationNumber'],
						"proposal_no" => $newArr['policyDtls']['ProposalNumber'],
						"policy_no" => $newArr['policyDtls']['PolicyNumber'],
						"gross_premium" => empty($premium['GrossPremium']) ? '' : $premium['GrossPremium'],
						"status" => "Success",
						"start_date" => $newArr['policyDtls']['startDate'],
						"end_date" => $newArr['policyDtls']['EndDate'],
						"created_date" => date('Y-m-d H:i:s'),
						"proposal_no_lead" => $proposal_policy_id,
						"PolicyStatus" => $newArr['policyDtls']['PolicyStatus'],
						"letter_url" => $newArr['policyDtls']['LetterURL'],
						"CustomerID" => $newArr['policyDtls']['MemberCustomerID'],
						"MemberCustomerID" => $newArr['policyDtls']['MemberCustomerID'],
						"ReceiptNumber" => empty($receiptObj['ReceiptNumber']) ? '' : $receiptObj['ReceiptNumber'],
						//"COI_url" => $newArr['policyDtls']['COIUrl'],

					);

					$this->db->insert('api_proposal_response', $api_insert);

					//Monolog::saveLog("api_insert", "I", json_encode($api_insert));	

					//HB emandate call
					$this->emandate_HB_call($emp_id, $data['proposal_data'][0]['proposal_details_id']);

					$request_arr = ["lead_id" => $leadID, "req" => json_encode($api_insert), "product_id" => $product_id, "type" => "api_insert"];
					$this->db->insert("logs_docs", $request_arr);
					$this->Logs_m->insertLogs($dataArray);

					//Application log entries
					$this->db->insert("application_logs", [
						"lead_id" => $leadID,
						"action" => "api_insert",
						"request_data" => json_encode($api_insert),
						"created_on" => date("Y-m-d H:i:s")
					]);

				} else {

					$return_data = array(
						'status' => 'error',
						"msg" => $errorObj['ErrorMessage']
					);
				}

				return $return_data;
			}
		} else {

			return $return_data = array(
				'status' => 'error',
				"msg" => "Quote error"
			);
		}
	}

	// Emadate HB Call

	function emandate_HB_call($emp_id, $proposal_details_id)
	{
		$query_check = $this->db->query("select ed.lead_id,ed.plan_id,ed.json_qote,apr.certificate_number,apr.proposal_no,apr.pr_api_id from proposal_details as ed,proposal_policy as p,api_proposal_response as apr,emandate_data as emd where ed.proposal_details_id = p.proposal_details_id and p.proposal_policy_id = apr.proposal_no_lead and p.status in('Success','Payment Received') and apr.mandate_send_status = 0 and emd.lead_id = ed.lead_id and emd.status = 'Success' and ed.proposal_details_id = '$proposal_details_id' group by p.proposal_details_id")->result_array();

		if ($query_check) {

			foreach ($query_check as $val) {

				//BIZ HB call start
				$json_data = json_decode($val['json_qote'], true);

				$url = 'https://bizpre.adityabirlahealth.com/ABHICL_HealthCoreXML/Service1.svc/AddEmendateDetails';

				$req_arr = [
					'EmendateDeatails' =>
					[
						'EmendateList' =>
						[
							[
								'Bank_Name' => ($json_data['AXISBANKACCOUNT'] == 'Y') ? 'Axis Bank' : 'Other',
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
					CURLOPT_POSTFIELDS => json_encode($req_arr),
					CURLOPT_HTTPHEADER => array(
						"Accept: */*",
						"Cache-Control: no-cache",
						"Connection: keep-alive",
						"Content-Length: " . strlen(json_encode($req_arr)),
						"Content-Type: application/json",
						"Host: bizpre.adityabirlahealth.com"
					),
				));

				$response = curl_exec($curl);

				curl_close($curl);

				// if($response){
				// $response = json_decode($response);
				// }

				$update_arr = ["mandate_send_status" => 1];

				$this->db->where("pr_api_id", $val['pr_api_id']);
				$this->db->update("api_proposal_response", $update_arr);

				$request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($req_arr), "res" => json_encode($response), "product_id" => "ABC", "type" => "emandate_HB_post"];

				$this->db->insert('logs_docs', $request_arr);
				//BIZ HB call end

				//Application log entries
				$this->db->insert("application_logs", [
					"lead_id" => $query_check['lead_id'],
					"action" => "emandate_HB_post",
					"request_data" => json_encode($req_arr),
					"response_data" => json_encode($response),
					"created_on" => date("Y-m-d H:i:s")
				]);

			}
		}
	}


	//Get Customer data
	function get_profile($emp_id)
	{
		return $this->db->query("select e.* from master_customer as e where e.customer_id='$emp_id'")->row();
	}

	//Get all member data
	function get_all_member_data($proposal_policy_id)
	{

		$response = $this->db->query('select * from proposal_policy_member where policy_id=' . $proposal_policy_id . ' ')->result_array();
		//echo $this->db->last_query();
		return $response;
	}

	function real_pg_check($lead_id)
	{
		$check_pg = false;

		$query = $this->db->query("SELECT ed.lead_id,ed.trace_id,ed.primary_customer_id,plan_id FROM lead_details as ed where ed.lead_id =" . $lead_id)->row_array();

		if ($query) {

			$vertical = 'ABCGRP';

			$CKS_data = "AX|" . $vertical . "|LEADID|" . $query['trace_id'] . "|" . $this->hash_key;

			$CKS_value = hash($this->hashMethod, $CKS_data);

			$url = "https://pg_uat.adityabirlahealth.com/PGMANDATE/service/api/enquirePayment";
			$fqrequest = array(
				"signature" => $CKS_value,
				"Source" => "AX",
				"Vertical" => $vertical,
				"SearchMode" => "LEADID",
				"UniqueIdentifierValue" => $query['trace_id'],
				"PaymentMode" => "PO"
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
				CURLOPT_POSTFIELDS => json_encode($fqrequest),
				CURLOPT_HTTPHEADER => array(
					"Accept: */*",
					"Cache-Control: no-cache",
					"Connection: keep-alive",
					"Content-Length: " . strlen(json_encode($fqrequest)),
					"Content-Type: application/json"
				),
			));


			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			$result = json_decode($response, true);

			if ($err) {
				$request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest), "res" => json_encode($err), "product_id" => $query['plan_id'], "type" => "pg_real_fail"];
				$this->db->insert('logs_docs', $request_arr);

				//Application log entries
				$this->db->insert("application_logs", [
					"lead_id" => $query['lead_id'],
					"action" => "pg_real_fail",
					"request_data" => json_encode($fqrequest),
					"response_data" => json_encode($err),
					"created_on" => date("Y-m-d H:i:s")
				]);

			} else {

				if ($result && $result['PaymentStatus'] == 'PR') {

					$TxStatus = "Success";

					$request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest), "res" => json_encode($result), "product_id" => $query['plan_id'], "type" => "pg_real_success"];

					$this->db->insert('logs_docs', $request_arr);

					//Application log entries
					$this->db->insert("application_logs", [
						"lead_id" => $query['lead_id'],
						"action" => "pg_real_success",
						"request_data" => json_encode($fqrequest),
						"response_data" => json_encode($result),
						"created_on" => date("Y-m-d H:i:s")
					]);

					$arr = ["remark" => $result['TxMsg'], "payment_status" => $TxStatus, "proposal_status" => "PaymentReceived", "premium_with_tax" => $result['amount'], "transaction_date" => $result['txnDateTime'], "transaction_number" => $result['TxRefNo']];

					$this->db->where("lead_id", $lead_id);
					$this->db->update("proposal_payment_details", $arr);

					$this->db->where("lead_id", $query['lead_id']);
					$this->db->update("lead_details", array('status' => "Customer-Payment-Received"));
					if ($result['Registrationmode']) {

						$query_emandate = $this->db->query("select * from emandate_data where lead_id=" . $query['lead_id'])->row_array();

						if ($result['EMandateStatus'] == 'MS') {
							$mandate_status = 'Success';
						} elseif ($result['EMandateStatus'] == 'MI') {
							$mandate_status = 'Emandate Pending';
						} elseif ($result['EMandateStatus'] == 'MR') {
							$mandate_status = 'Emandate Received';
						} elseif ($result['EMandateStatus'] == '') {
							$mandate_status = 'Emandate Pending';
						} else {
							$mandate_status = 'Fail';
						}

						if ($query_emandate > 0) {

							$arr = ["TRN" => $result['EMandateRefno'], "status_desc" => $result['EMandateStatusDesc'], "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($result['EMandateDate'])), "Registrationmode" => $result['Registrationmode'], "EMandateFailureReason" => $result['EMandateFailureReason']];

							$this->db->where("lead_id", $query['lead_id']);
							$this->db->update("emandate_data", $arr);
						} else {

							$arr = ["lead_id" => $query['lead_id'], "TRN" => $result['EMandateRefno'], "status_desc" => $result['EMandateStatusDesc'], "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($result['EMandateDate'])), "Registrationmode" => $result['Registrationmode'], "EMandateFailureReason" => $result['EMandateFailureReason']];

							$this->db->insert("emandate_data", $arr);
						}

						if ($mandate_status == 'Success') {
							//$this->send_message($query['lead_id'],'success');
						}

						if ($mandate_status == 'Fail') {
							//$this->send_message($query['lead_id'],'fail');
						}
					}

					$check_pg = true;
				} else {

					$request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest), "res" => json_encode($result), "product_id" => $query['plan_id'], "type" => "pg_real_fail"];
					$this->db->insert('logs_docs', $request_arr);

					//Application log entries
					$this->db->insert("application_logs", [
						"lead_id" => $query['lead_id'],
						"action" => "pg_real_fail",
						"request_data" => json_encode($fqrequest),
						"response_data" => json_encode($result),
						"created_on" => date("Y-m-d H:i:s")
					]);

				}
			}
		}

		return $check_pg;
	}
	// function real_pg_check($lead_id)
	// {
	// 	$check_pg = false;

	// 	$query = $this->db->query("SELECT ed.lead_id,ed.primary_customer_id,plan_id FROM lead_details as ed where ed.lead_id =" . $lead_id)->row_array();

	// 	if ($query) {

	// 		if ($query['plan_id'] != 'R06') {
	// 			$vertical = 'AXBBGRP';
	// 		} else {
	// 			$vertical = 'AXATGRP';
	// 		}

	// 		$CKS_data = "AX|" . $vertical . "|LEADID|" . $query['lead_id'] . "|" . $this->hash_key;

	// 		$CKS_value = hash($this->hashMethod, $CKS_data);

	// 		$url = "https://pg_uat.adityabirlahealth.com/PGMANDATE/service/api/enquirePayment";
	// 		$fqrequest = array(
	// 			"signature" => $CKS_value,
	// 			"Source" => "AX",
	// 			"Vertical" => $vertical,
	// 			"SearchMode" => "LEADID",
	// 			"UniqueIdentifierValue" => $query['lead_id'],
	// 			"PaymentMode" => "PP"
	// 		);

	// 		$curl = curl_init();

	// 		curl_setopt_array($curl, array(
	// 			CURLOPT_URL => $url,
	// 			CURLOPT_RETURNTRANSFER => true,
	// 			CURLOPT_ENCODING => "",
	// 			CURLOPT_MAXREDIRS => 10,
	// 			CURLOPT_TIMEOUT => 90,
	// 			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	// 			CURLOPT_CUSTOMREQUEST => "POST",
	// 			CURLOPT_POSTFIELDS => json_encode($fqrequest),
	// 			CURLOPT_HTTPHEADER => array(
	// 				"Accept: */*",
	// 				"Cache-Control: no-cache",
	// 				"Connection: keep-alive",
	// 				"Content-Length: " . strlen(json_encode($fqrequest)),
	// 				"Content-Type: application/json"
	// 			),
	// 		));


	// 		$response = curl_exec($curl);
	// 		$err = curl_error($curl);

	// 		curl_close($curl);

	// 		$result = json_decode($response, true);

	// 		// print_r($response); 
	// 		// echo json_encode($fqrequest); exit;

	// 		if ($err) {
	// 			$request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest), "res" => json_encode($err), "product_id" => $query['plan_id'], "type" => "pg_real_fail"];
	// 			$this->db->insert('logs_docs', $request_arr);
	// 		} else {

	// 			if ($result && $result['PaymentStatus'] == 'PR') {

	// 				$TxStatus = "success";
	// 				$TxMsg = "Approved";

	// 				$request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest), "res" => json_encode($result), "product_id" => $query['plan_id'], "type" => "pg_real_success"];

	// 				$this->db->insert('logs_docs', $request_arr);

	// 				$arr = ["payment_remark" => $TxStatus, "status" => $TxMsg, "premium_amount" => $result['amount'], "transaction_date" => $result['txnDateTime'], "transaction_number" => $result['TxRefNo']];

	// 				$proposal_ids = $this->db->query("select id as proposal_id from proposal_details where lead_id='" . $query['lead_id'] . "'")->result_array();

	// 				foreach ($proposal_ids as $query_val) {
	// 					$this->db->where("proposal_details_id", $query_val['proposal_id']);
	// 					$this->db->update("proposal_details", $arr);
	// 				}
	// 				$this->db->where("lead_id", $query['lead_id']);
	// 				$this->db->update("lead_details", array('status' => "Approved"));
	// 				if ($result['EMandateStatus']) {

	// 					$query_emandate = $this->db->query("select * from emandate_data where lead_id=" . $query['lead_id'])->row_array();

	// 					if ($result['EMandateStatus'] == 'MS') {
	// 						$mandate_status = 'Success';
	// 						//HB emandate call
	// 						//$this->emandate_HB_call($query['lead_id']);
	// 					} elseif ($result['EMandateStatus'] == 'MI') {
	// 						$mandate_status = 'Emandate Pending';
	// 					} elseif ($result['EMandateStatus'] == 'MR') {
	// 						$mandate_status = 'Emandate Received';
	// 					} else {
	// 						$mandate_status = 'Fail';
	// 					}

	// 					if ($query_emandate > 0) {

	// 						$arr = ["TRN" => $result['EMandateRefno'], "status_desc" => $result['EMandateStatusDesc'], "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($result['EMandateDate'])), "Registrationmode" => $result['Registrationmode'], "EMandateFailureReason" => $result['EMandateFailureReason']];

	// 						$this->db->where("lead_id", $query['lead_id']);
	// 						$this->db->update("emandate_data", $arr);
	// 					} else {

	// 						$arr = ["lead_id" => $query['lead_id'], "TRN" => $result['EMandateRefno'], "status_desc" => $result['EMandateStatusDesc'], "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($result['EMandateDate'])), "Registrationmode" => $result['Registrationmode'], "EMandateFailureReason" => $result['EMandateFailureReason']];

	// 						$this->db->insert("emandate_data", $arr);
	// 					}

	// 					if ($mandate_status == 'Success') {
	// 						//$this->send_message($query['lead_id'],'success');
	// 					}

	// 					if ($mandate_status == 'Fail') {
	// 						//$this->send_message($query['lead_id'],'fail');
	// 					}
	// 				}

	// 				$check_pg = true;
	// 			} else {

	// 				$request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($fqrequest), "res" => json_encode($result), "product_id" => $query['plan_id'], "type" => "pg_real_fail"];
	// 				$this->db->insert('logs_docs', $request_arr);
	// 			}
	// 		}
	// 	}

	// 	return $check_pg;
	// }

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


	function insertBatchData($tbl_name, $data_array, $sendid = NULL)
	{
		$this->db->insert_batch($tbl_name, $data_array);
		$result_id = $this->db->insert_id();

		if ($sendid == 1) {
			//return id
			return $result_id;
		}
	}

	function login_check($condition)
	{
		$this->db->select('i.employee_id , i.employee_fname, i.employee_mname,i.employee_lname,i.employee_code,i.email_id,i.mobile_number,i.role_id');
		$this->db->from('master_employee as i');
		$this->db->where("($condition)");

		$query = $this->db->get();
		//print_r($this->db->last_query());
		//exit;

		if ($query->num_rows() >= 1) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	function fetch_otp($mobile, $country_code)
	{

		//valid check 15 minutes
		$current_date_time = date("Y-m-d H:i:s");
		$current_timstamp = strtotime($current_date_time);
		$current_timstamp = $current_timstamp - (15 * 60);
		$from_date_time = date("Y-m-d H:i:s", $current_timstamp);

		$otp_condition = "";
		$sql = $this->db->query("select * from tbl_otp_check where  mobile = " . $this->db->escape($mobile) . " && country_code = " . $this->db->escape($country_code) . " && (time_stamp >='" . $from_date_time . "' && time_stamp <='" . $current_date_time . "' ) ");
		if ($sql->num_rows() > 0) {
			return $sql->result();
		} else {
			return false;
		}
	}

	function generate_otp($mobile, $country_code, $otp)
	{
		$sql = $this->db->query("select * from tbl_otp_check where mobile = " . $this->db->escape($mobile) . ' And country_code =' . $this->db->escape($country_code));
		$data = array(
			"mobile" => $mobile,
			"country_code" => $country_code,
			"otp" => $otp,
			"time_stamp" => date('Y-m-d H:i:s')
		);

		if ($sql->num_rows() > 0) {
			$this->updatedataotp($data, "tbl_otp_check", "mobile", $mobile, "country_code", $country_code);
		} else {
			$this->insertData("tbl_otp_check", $data, 1);
		}

		return true;
	}

	function updatedataotp($data, $table, $column1, $value1, $column2, $value2)
	{
		$this->db->where($column1, $value1);
		$this->db->where($column2, $value2);
		$this->db->update($table, $data);
	}

	function runquery($sql_query = '')
	{
		$query = $this->db->query($sql_query);
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}

	function runquery_array($sql_query = '')
	{
		$query = $this->db->query($sql_query);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	function getdata($table, $fields, $condition = '1=1')
	{
		//echo "Select $fields from $table where $condition";
		//exit;
		$sql = $this->db->query("Select $fields from $table where $condition");
		if ($sql->num_rows() > 0) {
			return $sql->result_array();
		} else {
			return []; //false;
		}
	}
	function getdata1($table, $fields, $condition = '1=1')
	{
		//echo "Select $fields from $table where $condition and  isactive = 1";exit;
		$sql = $this->db->query("Select $fields from $table where $condition and isactive = 1");

		if ($sql->num_rows() > 0) {
			//print_r($sql->result());
			return $sql->result();
		} else {
			return false;
		}
	}

	function getdataCount($table, $fields, $condition = '1=1')
	{

		$sql = $this->db->query("Select count(*) as total  from $table where $condition");
		if ($sql->num_rows() > 0) {
			$cnt = $sql->result_array();
			return $cnt[0]['total'];
		} else {
			return 0;
		}
	}

	function getdata_orderby($table, $fields, $condition = '1=1', $order_by)
	{
		//echo "Select $fields from $table where $condition";exit;
		$sql = $this->db->query("Select $fields from $table where $condition $order_by");
		if ($sql->num_rows() > 0) {
			return $sql->result_array();
		} else {
			return false;
		}
	}

	function getdata_groupby_orderby($table, $fields, $condition = '1=1', $group_by = "", $order_by = "")
	{
		//echo "Select $fields from $table where $condition";exit;
		$sql = $this->db->query("Select $fields from $table where $condition $group_by $order_by");
		if ($sql->num_rows() > 0) {
			return $sql->result_array();
		} else {
			return false;
		}
	}

	function getdata_groupby_orderby_limit($table, $fields, $condition = '1=1', $group_by = "", $order_by = "", $page)
	{
		$rows = 10;
		$page = $rows * $page;
		$limit = $page . "," . $rows;

		$sql = $this->db->query("Select $fields from $table where $condition $group_by $order_by limit $limit");
		//print_r($this->db->last_query());
		//exit;

		if ($sql->num_rows() > 0) {
			return $sql->result_array();
		} else {
			return false;
		}
	}

	function getdata_orderby_limit($table, $fields, $condition = '1=1', $order_by, $page)
	{

		$rows = 10;
		$page = $rows * $page;
		$limit = $page . "," . $rows;

		$sql = $this->db->query("Select $fields from $table where $condition $order_by limit $limit");
		//print_r($this->db->last_query());
		//exit;

		if ($sql->num_rows() > 0) {
			return $sql->result_array();
		} else {
			return false;
		}
	}

	function getdata_orderby1($table, $fields, $condition = '1=1', $order_by)
	{
		//echo "Select $fields from $table where $condition";exit;
		$sql = $this->db->query("Select $fields from $table where $condition order by $order_by desc limit 1");
		if ($sql->num_rows() > 0) {
			return $sql->result_array();
		} else {
			return false;
		}
	}

	function getdata_orderby2($table, $fields, $condition = '1=1', $order_by)
	{
		//echo "Select $fields from $table where $condition";exit;
		$sql = $this->db->query("Select $fields from $table where $condition order by $order_by limit 1");
		if ($sql->num_rows() > 0) {
			return $sql->result_array();
		} else {
			return false;
		}
	}



	function updateRecord($tbl_name, $datar, $condition)
	{
		//$this -> db -> where($comp_col, $eid);
		$this->db->where("($condition)");
		$this->db->update($tbl_name, $datar);

		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return true;
		}
	}
	function updateRecordarr($tbl_name, $datar, $condition)
	{
		//$this -> db -> where($comp_col, $eid);
		$this->db->where($condition);
		$this->db->update($tbl_name, $datar);

		if ($this->db->affected_rows() > 0) {
	
			return 1;
		} else {
			return 0;
		}
	}

	function delrecord($tbl_name, $tbl_id, $record_id)
	{
		$this->db->where($tbl_id, $record_id);
		$this->db->delete($tbl_name);
		if ($this->db->affected_rows() >= 1) {
			return true;
		} else {
			return false;
		}
	}

	function lockTable($table_name, $mode)
	{
		$this->db->query("LOCK TABLE $table_name $mode");
	}

	function unLockTable()
	{

		$this->db->query("UNLOCK TABLES");
	}

	function getLastProposalNo()
	{

		$date = date('Y-m-d');
		$sql = "SELECT MAX(proposal_no) As proposal_no FROM proposal_policy WHERE created_at LIKE '" . $date . "%'";
		return $this->db->query($sql)->row();
	}

	function delrecord_condition($tbl_name, $condition)
	{
		//$this->db->where($tbl_id, $record_id);
		$this->db->where("($condition)");
		$this->db->delete($tbl_name);
		if ($this->db->affected_rows() >= 1) {
			return true;
		} else {
			return false;
		}
	}
	function delrecord_array($tbl_name, $condition)
	{
		//$this->db->where($tbl_id, $record_id);
		$this->db->where($condition);
		$this->db->delete($tbl_name);
		if ($this->db->affected_rows() >= 1) {
			return true;
		} else {
			return false;
		}
	}

	function check_utoken($decode_utoken, $usertype, $apptype, $login_userid)
	{
		$this->db->select('u.*');
		$this->db->from('tbl_users_master as u');

		if ($apptype != 'W') {
			if ($usertype == 'P') {
				$this->db->where('concat(u.user_master_id,provider_device_id)', $decode_utoken);
			} else if ($usertype == 'C') {
				$this->db->where('concat(u.user_master_id,consumer_device_id)', $decode_utoken);
			}
		}
		// if called via web check decode_utoken only with user id
		else {
			$this->db->where('u.user_master_id', $decode_utoken);
		}

		//also check with login user id	- bascially user id within the decode_utoken should be same as loginuser id
		$this->db->where('u.user_master_id', $login_userid);
		$this->db->where('u.status', "Active");


		$query = $this->db->get();

		//print_r($this->db->last_query());
		//exit;

		if ($query->num_rows() >= 1) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	function check_registered_utoken($utoken)
	{
		$this->db->select('u.*');
		$this->db->from('tbl_users as u');
		$this->db->where('u.email', $utoken);
		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result_array();
		} else {
			return false;
		}
	}


	function getRecords($tbl_name, $condition, $page = null, $default_sort_column = null, $default_sort_order = null, $group_by = null)
	{

		$table = $tbl_name;
		$default_sort_column = $default_sort_column;
		$default_sort_order = $default_sort_order;
		if ($page != null) {
			$rows = 10;
			$page = $rows * $page;
		}

		// sort order by column
		$sort = $default_sort_column;
		$order = $default_sort_order;

		$this->db->select('*');
		$this->db->from($tbl_name);

		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		if ($page != null) {
			$this->db->limit($rows, $page);
		}

		$query = $this->db->get();

		//print_r($this->db->last_query());
		//exit;

		if ($query->num_rows() >= 1) {
			return $query->result();
		} else {
			return false;
		}
	}

	/*
	Author : Amol Koli
	Date : 24th dec, 2020
	***/
	function getPolicyMemberDetails($proposal_policy_id, $policy_id, $lead_id)
	{
		$data = $this->db->select('ppm.*,ppmd.*,fc.member_type')
			->from('proposal_policy_member_details as ppmd')
			->join('proposal_policy_member as ppm', 'ppmd.member_id=ppm.member_id')
			->join('family_construct as fc', 'fc.id=ppmd.relation_with_proposal')
			->where('ppmd.lead_id', $lead_id)
			->where('ppm.proposal_policy_id', $proposal_policy_id)
			->where('ppm.policy_id', $policy_id)
			->order_by('ppm.member_id', 'ASC')
			->get()
			->result_array();
		return $data;
	}

	function getPolicyMemberDetailsForCombo($lead_id, $master_policy_id_arr){

		$where = 'ppmd.policy_member_age >= 18';
		$data = $this->db->select('ppm.*,ppmd.*,fc.member_type,mp.policy_number,mp.plan_code')
			->from('proposal_policy_member_details as ppmd')
			->join('proposal_policy_member as ppm', 'ppmd.member_id=ppm.member_id')
			->join('family_construct as fc', 'fc.id=ppmd.relation_with_proposal')
			->join('master_policy as mp', 'mp.policy_id = ppm.policy_id')
			->where('ppmd.lead_id', $lead_id)
			//->where('ppm.proposal_policy_id', $proposal_policy_id)
			->where($where)
			->where_in('ppm.policy_id', $master_policy_id_arr)
			->order_by('ppm.member_id', 'ASC')
			->get()
			->result_array();

		$response = [];
		if(!empty($data)){

			foreach($data as $key => $value){

				$response[$value['customer_id']][$value['policy_number']][$value['member_id']] = $value;
				$response[$value['customer_id']]['plan_code'][$value['policy_number']][$value['plan_code']] = $value['plan_code'];
			}
		}

		return $response;
	}

	/*
	Author : Amol Koli
	Date : 28th dec, 2020
	***/
	// function getSumInsuredType($master_policy_id)
	// {
	// 	$sumins_type = $this->db->query("select mi.suminsured_type from master_policy_si_type_mapping as m left join master_suminsured_type as mi ON m.suminsured_type_id = mi.suminsured_type_id where m.master_policy_id='$master_policy_id' ")->row();
	// 	return $sumins_type->suminsured_type;
	// }

	function getSumInsuredType($master_policy_id)
	{
		$sumins_type = $this->db->query("select mi.suminsured_type from master_policy_si_type_mapping as m left join master_suminsured_type as mi ON m.suminsured_type_id = mi.suminsured_type_id where m.master_policy_id='$master_policy_id' ")->row();
		return $sumins_type->suminsured_type;
	}

	/*
	Author : Amol Koli
	Date : 29th dec, 2020
	***/
	function getGroupCode($plan_id, $SumInsured, $FamilyConstruct)
	{
		$groupCode = $this->db->query("select * from master_group_code where plan_id='$plan_id' and si_group = '$SumInsured' and family_construct = '$FamilyConstruct' ")->row_array();

		return $groupCode;
	}
	// function getGroupCode($policy_number, $SumInsured, $adult_count, $child_count, $getPolicyMemberDetails)
	// {
	// 	$totalMembers = count($getPolicyMemberDetails);
	// 	for ($i = 0; $i < $totalMembers; $i++) {
	// 		//Checking relation based on self, spouse, son and daughter
	// 		if ($getPolicyMemberDetails[$i]['relation_with_proposal'] == 2) {
	// 			$primary_insured = 'Spouse';
	// 		}
	// 	}

	// 	$family_type = "Self+Spouse+Kid1+Kid2";
	// 	$groupCode = 'GRP011';
	// 	if ($policy_number == 1) {
	// 		if ($adult_count == 1 && $child_count == 0) {
	// 			$family_type = 'Individual';
	// 			if ($SumInsured == 500000) {
	// 				$groupCode = 'GRP001';
	// 			}
	// 			if ($SumInsured == 1000000) {
	// 				$groupCode = 'GRP002';
	// 			}
	// 		} else if ($adult_count == 2 && $child_count == 0) {
	// 			$family_type = 'Self+Spouse';
	// 			if ($SumInsured == 500000) {
	// 				$groupCode = 'GRP003';
	// 			}
	// 			if ($SumInsured == 1000000) {
	// 				$groupCode = 'GRP004';
	// 			}
	// 		} else if ($adult_count == 2 && $child_count == 1) {
	// 			$family_type = 'Self+Spouse+Kid1';
	// 			if ($SumInsured == 500000) {
	// 				$groupCode = 'GRP005';
	// 			}
	// 			if ($SumInsured == 1000000) {
	// 				$groupCode = 'GRP006';
	// 			}
	// 		} else if ($adult_count == 1 && $child_count == 1) {
	// 			if ($primary_insured == "Spouse") {
	// 				$family_type = 'Spouse+Kid1';
	// 				if ($SumInsured == 500000) {
	// 					$groupCode = 'GRP013';
	// 				}
	// 				if ($SumInsured == 1000000) {
	// 					$groupCode = 'GRP014';
	// 				}
	// 			} else {
	// 				$family_type = 'Self+Kid1';
	// 				if ($SumInsured == 500000) {
	// 					$groupCode = 'GRP007';
	// 				}
	// 				if ($SumInsured == 1000000) {
	// 					$groupCode = 'GRP008';
	// 				}
	// 			}
	// 		} else if ($adult_count == 1 && $child_count == 2) {
	// 			if ($primary_insured == "Spouse") {
	// 				$family_type = 'Spouse+Kid1+Kid2';
	// 				if ($SumInsured == 500000) {
	// 					$groupCode = 'GRP015';
	// 				}
	// 				if ($SumInsured == 1000000) {
	// 					$groupCode = 'GRP016';
	// 				}
	// 			} else {
	// 				$family_type = 'Self+Kid1+Kid2';
	// 				if ($SumInsured == 500000) {
	// 					$groupCode = 'GRP009';
	// 				}
	// 				if ($SumInsured == 1000000) {
	// 					$groupCode = 'GRP010';
	// 				}
	// 			}
	// 		} else if ($adult_count == 2 && $child_count == 2) {
	// 			$family_type = 'Self+Spouse+Kid1+Kid2';
	// 			if ($SumInsured == 500000) {
	// 				$groupCode = 'GRP011';
	// 			}
	// 			if ($SumInsured == 1000000) {
	// 				$groupCode = 'GRP012';
	// 			}
	// 		}
	// 	}
	// 	//else if($policy_number == 2)
	// 	else {
	// 		//10000
	// 		if ($adult_count == 1) {
	// 			if ($SumInsured == 500000) {
	// 				$groupCode = 'GRP001';
	// 			}
	// 			if ($SumInsured == 1000000) {
	// 				$groupCode = 'GRP002';
	// 			}
	// 		} else if ($adult_count == 2) {
	// 			if ($SumInsured == 500000) {
	// 				$groupCode = 'GRP003';
	// 			}
	// 			if ($SumInsured == 1000000) {
	// 				$groupCode = 'GRP004';
	// 			}
	// 		}
	// 		$groupCode = 'GRP001';
	// 	}
	// 	$return['family_type'] = $family_type;
	// 	$return['groupCode'] = $groupCode;
	// 	return $return;
	// }

	/*
	Author : Amol Koli
	Date : 29th dec, 2020
	***/
	function getIntermediaryCode($policy_id)
	{
		/*$data = $this->db->query("select intermediary_code from master_policy where policy_id='$policy_id'")->row();
		return $data->intermediary_code;*/

		$this->db->select('bim.imd_code, bim.branch_code');
		$this->db->from('branch_imd_mapping bim');
		$this->db->join('master_policy mp', 'mp.policy_number = bim.policy_number');
		$this->db->where('mp.policy_id='.$policy_id);
		$data = $this->db->get()->row();

		if(!empty($data)){

			return $data;
		}

		return '';
	}

	/*
	Author : Amol Koli
	Date : 29th dec, 2020
	***/
	function getMasterPolicyCode($policy_id)
	{
		$data = $this->db->query("select master_policy_code from master_policy where policy_id='$policy_id'")->row();
		return $data->master_policy_code;
	}

	/*
	Author : Amol Koli
	Date : 29th dec, 2020
	***/
	function getPlanCode($policy_id)
	{
		$data = $this->db->query("select plan_code from master_policy where policy_id='$policy_id'")->row();
		return $data->plan_code;
		/*
		$plan_code = "";
		if($policy_number=='GHI'){
			$plan_code = '4211';
		}else if($policy_number=='GPA'){
			$plan_code = '4224';
		}else if($policy_number=='GC'){
			$plan_code = '4216';
		}else if($policy_number=='GP'){
			$plan_code = '4224';
		}
		return $plan_code;*/
	}

	/*
	Author : Amol Koli
	Date : 29th dec, 2020
	***/
	function getProductCode($policy_id)
	{

		$data = $this->db->query("select product_code from master_policy where policy_id='$policy_id' ")->row();
		return $data->product_code;
		/*
		$Product_Code = "";
		if($policy_number=='GHI'){
			$Product_Code = '4211';
		}else if($policy_number=='GPA'){
			$Product_Code = '4224';
		}else if($policy_number=='GC'){
			$Product_Code = '4216';
		}else if($policy_number=='GP'){
			$Product_Code = '4224';
		}
		return $Product_Code;
		*/
	}

	/*
	Author : Amol Koli
	Date : 30th dec, 2020
	***/
	function getRelationCode($relation_with_proposal)
	{
		$relation_code = '';
		if ($relation_with_proposal == 1) {
			$relation_code = "R001";
		} else if ($relation_with_proposal == 2) {
			$relation_code = "R002";
		} else if ($relation_with_proposal == 3) {
			$relation_code = "R003";
		} else {
			$relation_code = "R004";
		}
		return $relation_code;
	}

	/*
	Author : Amol Koli
	Date : 31th dec, 2020
	***/
	function getNomineeRelationCode($nominee_relation)
	{
		$relation_code = '';
		if ($nominee_relation == 1) {
			$relation_code = "R001";
		} else if ($nominee_relation == 2) {
			$relation_code = "R002";
		} else if ($nominee_relation == 3) {
			$relation_code = "R003";
		} else {
			$relation_code = "R004";
		}
		return $relation_code;
	}

	/*
	Author : Amol Koli
	Date : 29th dec, 2020
	***/
	function getMembersForQuote($row, $getPolicyMemberDetails, $nominees)
	{
		$policy_number = isset($row['policy_number']) ? $row['policy_number'] : '';
		$master_policy_id = isset($row['master_policy_id']) ? $row['master_policy_id'] : '';
		$plan_code = $row['plan_code']; //$this->getPlanCode($master_policy_id);
		$totalMembers = count($getPolicyMemberDetails);
		$member = [];
		for ($i = 0; $i < $totalMembers; $i++) {
			//Checking relation based on self, spouse, son and daughter
			$relation_with_proposal = $this->getRelationCode($getPolicyMemberDetails[$i]['relation_with_proposal']);
			$relation_code = $this->getNomineeRelationCode($nominees['nominee_relation']);

			$abc = ["PEDCode" => null, "Remarks" => null];
			$explode_name_member = explode(" ", trim($getPolicyMemberDetails[$i]['policy_member_first_name']), 2);
			$explode_name = array($row['first_name'], $row['middle_name'], $row['last_name']);
			$explode_name_nominee = array($row['nominee_first_name'], $row['nominee_last_name']);

			$member[] = [
				"MemberNo" => $i + 1,
				"Salutation" => (($getPolicyMemberDetails[$i]['policy_member_gender'] == "Male") ? 'Mr' : (($getPolicyMemberDetails[$i]['policy_member_age'] >= 18) ? 'Mrs' : 'Ms')),
				"First_Name" => $getPolicyMemberDetails[$i]['policy_member_first_name'],
				"Middle_Name" => null,
				"Last_Name" => !empty($getPolicyMemberDetails[$i]['policy_member_last_name']) ? $getPolicyMemberDetails[$i]['policy_member_last_name'] : '.',
				"Gender" => ($getPolicyMemberDetails[$i]['policy_member_gender'] == "Male") ? "M" : "F",
				"DateOfBirth" => date('m/d/Y', strtotime($getPolicyMemberDetails[$i]['policy_member_dob'])),
				"Relation_Code" => trim($relation_with_proposal),
				"Marital_Status" => null,
				"height" => "0.00",
				"weight" => "0",
				"occupation" => "O553",
				"PrimaryMember" => (($i == 0) ? "Y" : "N"),
				"MemberproductComponents" => [
					[
						"PlanCode" => $plan_code,
						"MemberQuestionDetails" => [[
							"QuestionCode" => null,
							"Answer" => null,
							"Remarks" => null
						]]
					]
				],
				"MemberPED" => $abc,
				"exactDiagnosis" => null,
				"dateOfDiagnosis" => null,
				"lastDateConsultation" => null,
				"detailsOfTreatmentGiven" => null,
				"doctorName" => null,
				"hospitalName" => null,
				"phoneNumberHosital" => null,
				"Nominee_First_Name" => $explode_name_nominee[0],
				"Nominee_Last_Name" => !empty($explode_name_nominee[1]) ? $explode_name_nominee[1] : '.',
				"Nominee_Contact_Number" => (!empty($nominees['nominee_contact'])) ? $nominees['nominee_contact'] : null,
				"Nominee_Home_Address" => null,
				"Nominee_Relationship_Code" => trim($relation_code),
			];
		}
		return $member;
	}

	function getMembersForQuoteCombo($row, $getPolicyMemberDetails,$MemberproductComponents){

		$policy_number = isset($row['policy_number']) ? $row['policy_number'] : '';
		$master_policy_id = isset($row['master_policy_id']) ? $row['master_policy_id'] : '';
		$plan_code = $row['plan_code']; //$this->getPlanCode($master_policy_id);
		$totalMembers = count($getPolicyMemberDetails);
		$member = [];
		for ($i = 0; $i < $totalMembers; $i++) {
			//Checking relation based on self, spouse, son and daughter
			$relation_with_proposal = $this->getRelationCode($getPolicyMemberDetails[$i]['relation_with_proposal']);
			$relation_code = $this->getNomineeRelationCode($row['nominee_relation']);

			$abc = ["PEDCode" => null, "Remarks" => null];
			$explode_name_member = explode(" ", trim($getPolicyMemberDetails[$i]['policy_member_first_name']), 2);
			$explode_name = array($row['first_name'], $row['middle_name'], $row['last_name']);
			$explode_name_nominee = array($row['nominee_first_name'], $row['nominee_last_name']);

			$member[] = [
				"MemberNo" => $i + 1,
				"Salutation" => (($getPolicyMemberDetails[$i]['policy_member_gender'] == "Male") ? 'Mr' : (($getPolicyMemberDetails[$i]['policy_member_age'] >= 18) ? 'Mrs' : 'Ms')),
				"First_Name" => $getPolicyMemberDetails[$i]['policy_member_first_name'],
				"Middle_Name" => null,
				"Last_Name" => !empty($getPolicyMemberDetails[$i]['policy_member_last_name']) ? $getPolicyMemberDetails[$i]['policy_member_last_name'] : '.',
				"Gender" => ($getPolicyMemberDetails[$i]['policy_member_gender'] == "Male") ? "M" : "F",
				"DateOfBirth" => date('m/d/Y', strtotime($getPolicyMemberDetails[$i]['policy_member_dob'])),
				"Relation_Code" => trim($relation_with_proposal),
				"Marital_Status" => null,
				"height" => "0.00",
				"weight" => "0",
				"occupation" => "O553",
				"PrimaryMember" => (($i == 0) ? "Y" : "N"),
				/*"MemberproductComponents" => [
					[
						"PlanCode" => $plan_code,
						"MemberQuestionDetails" => [[
							"QuestionCode" => null,
							"Answer" => null,
							"Remarks" => null
						]]
					]
				],*/
				"MemberproductComponents" => array_values($MemberproductComponents),
				"MemberPED" => $abc,
				"exactDiagnosis" => null,
				"dateOfDiagnosis" => null,
				"lastDateConsultation" => null,
				"detailsOfTreatmentGiven" => null,
				"doctorName" => null,
				"hospitalName" => null,
				"phoneNumberHosital" => null,
				"Nominee_First_Name" => $explode_name_nominee[0],
				"Nominee_Last_Name" => !empty($explode_name_nominee[1]) ? $explode_name_nominee[1] : '.',
				"Nominee_Contact_Number" => (!empty($row['nominee_contact'])) ? $row['nominee_contact'] : null,
				"Nominee_Home_Address" => null,
				"Nominee_Relationship_Code" => trim($relation_code),
			];
		}
		return $member;
	}

	// function getMembersForQuote($row, $getPolicyMemberDetails, $nominees)
	// {
	// 	$policy_number = isset($row['policy_number']) ? $row['policy_number'] : '';
	// 	$master_policy_id = isset($row['master_policy_id']) ? $row['master_policy_id'] : '';
	// 	$plan_code = $this->getPlanCode($master_policy_id);
	// 	$totalMembers = count($getPolicyMemberDetails);
	// 	$member = [];
	// 	for ($i = 0; $i < $totalMembers; $i++) {
	// 		//Checking relation based on self, spouse, son and daughter
	// 		$relation_with_proposal = $this->getRelationCode($getPolicyMemberDetails[$i]['relation_with_proposal']);
	// 		$relation_code = $this->getNomineeRelationCode($nominees['nominee_relation']);

	// 		$abc = ["PEDCode" => null, "Remarks" => null];
	// 		$explode_name_member = explode(" ", trim($getPolicyMemberDetails[$i]['policy_member_first_name']), 2);
	// 		$explode_name = array($row['first_name'], $row['middle_name'], $row['last_name']);
	// 		$explode_name_nominee = array($row['nominee_first_name'], $row['nominee_last_name']);

	// 		$member[] = [
	// 			"MemberNo" => $i + 1,
	// 			"Salutation" => (($getPolicyMemberDetails[$i]['policy_member_gender'] == "Male") ? 'Mr' : (($getPolicyMemberDetails[$i]['policy_member_age'] >= 18) ? 'Mrs' : 'Ms')),
	// 			"First_Name" => $getPolicyMemberDetails[$i]['policy_member_first_name'],
	// 			"Middle_Name" => null,
	// 			"Last_Name" => !empty($getPolicyMemberDetails[$i]['policy_member_last_name']) ? $getPolicyMemberDetails[$i]['policy_member_last_name'] : '.',
	// 			"Gender" => ($getPolicyMemberDetails[$i]['policy_member_gender'] == "Male") ? "M" : "F",
	// 			"DateOfBirth" => date('m/d/Y', strtotime($getPolicyMemberDetails[$i]['policy_member_dob'])),
	// 			"Relation_Code" => trim($relation_with_proposal),
	// 			"Marital_Status" => null,
	// 			"height" => "0.00",
	// 			"weight" => "0",
	// 			"occupation" => "O553",
	// 			"PrimaryMember" => (($i == 0) ? "Y" : "N"),
	// 			"MemberproductComponents" => [
	// 				[
	// 					"PlanCode" => $plan_code,
	// 					"MemberQuestionDetails" => [[
	// 						"QuestionCode" => null,
	// 						"Answer" => null,
	// 						"Remarks" => null
	// 					]]
	// 				]
	// 			],
	// 			"MemberPED" => $abc,
	// 			"exactDiagnosis" => null,
	// 			"dateOfDiagnosis" => null,
	// 			"lastDateConsultation" => null,
	// 			"detailsOfTreatmentGiven" => null,
	// 			"doctorName" => null,
	// 			"hospitalName" => null,
	// 			"phoneNumberHosital" => null,
	// 			"Nominee_First_Name" => $explode_name_nominee[0],
	// 			"Nominee_Last_Name" => !empty($explode_name_nominee[1]) ? $explode_name_nominee[1] : '.',
	// 			"Nominee_Contact_Number" => (!empty($nominees['nominee_contact'])) ? $nominees['nominee_contact'] : null,
	// 			"Nominee_Home_Address" => null,
	// 			"Nominee_Relationship_Code" => trim($relation_code),
	// 		];
	// 	}
	// 	return $member;
	// }

	/*
	Author : Amol Koli
	Date : 30th dec, 2020
	***/
	function getSourceName($policy_id)
	{
		$data = $this->db->query("select source_name from master_policy where policy_id='$policy_id' ")->row();
		return $data->source_name;
		/*
		$SourceSystemName_api = "";
		if($policy_number=='GHI'){
			$SourceSystemName_api = 'AXIS_PL_GHI';
		}else if($policy_number=='GPA'){
			$SourceSystemName_api = 'AXIS_PL_GP';
		}else if($policy_number=='GC'){
			$SourceSystemName_api = 'AXIS_PL_GP';
		}else if($policy_number=='GP'){
			$SourceSystemName_api = 'AXIS_PL_GP';
		}
		return $SourceSystemName_api;
		*/
	}
	// function getSourceName($policy_id)
	// {
	// 	$data = $this->db->query("select source_name from master_policy where policy_id='$policy_id' ")->row();
	// 	return $data->source_name;
	// 	/*
	// 	$SourceSystemName_api = "";
	// 	if($policy_number=='GHI'){
	// 		$SourceSystemName_api = 'AXIS_PL_GHI';
	// 	}else if($policy_number=='GPA'){
	// 		$SourceSystemName_api = 'AXIS_PL_GP';
	// 	}else if($policy_number=='GC'){
	// 		$SourceSystemName_api = 'AXIS_PL_GP';
	// 	}else if($policy_number=='GP'){
	// 		$SourceSystemName_api = 'AXIS_PL_GP';
	// 	}
	// 	return $SourceSystemName_api;
	// 	*/
	// }

	/*
	Author : Amol Koli
	Date : 30th dec, 2020
	***/
	function getSchemeCode($policy_id)
	{
		$data = $this->db->query("select scheme_code from master_policy where policy_id='$policy_id' ")->row();
		return $data->scheme_code;
	}

	/*
	Author : Amol Koli
	Date : 30th dec, 2020
	***/
	function getApiUrl($policy_sub_type_id)
	{
		$data = $this->db->query("select api_url from master_policy_sub_type where policy_sub_type_id='$policy_sub_type_id'")->row();
		return $data->api_url;
	}
	// function getApiUrl($policy_sub_type_id)
	// {
	// 	$data = $this->db->query("select api_url from master_policy_sub_type where policy_sub_type_id='$policy_sub_type_id' ")->row();
	// 	return $data->api_url;
	// }

	/*
	Author : Amol Koli
	Date : 30th dec, 2020
	***/
	function getCurl($url, $request)
	{
		if (empty($url) || empty($request)) {
			return array(
				"status" => "Error",
				"data" => "Empty Request or Url"
			);
		}

		$this->load->helper('web_service');
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 120,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($request),
			CURLOPT_HTTPHEADER => array(
				"Accept: */*",
				"Cache-Control: no-cache",
				"Connection: keep-alive",
				"Content-Length: " . strlen(json_encode($request)),
				"Content-Type: application/json",
				"Host: bizpre.adityabirlahealth.com",
			),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		if ($response == '' || $response == NULL) {
			$response = $err;
		}
		if ($err) {
			$return = array(
				"status" => "Error",
				"data" => $err
			);
		} else {
			if (empty($response)) {
				$return = array(
					"status" => "Error",
					"data" => "Empty Response"
				);
			} else {
				$response = XML2Array::createArray($response);
				if ($response["errorObj"]["ErrorNumber"] == "00" || $response["errorObj"]["ErrorNumber"] == "302") {
					$return = array(
						"status" => "Success",
						"data" => $response
					);
				} else {
					$return = array(
						"status" => "Error",
						"data" => $response["errorObj"]["ErrorMessage"]
					);
				}
			}
		}
		//print_r($response);exit;
		return $return;
	}
	// function getCurl($url, $request)
	// {

	// 	if (empty($url) || empty($request)) {
	// 		return array(
	// 			"status" => "Error",
	// 			"message" => "Empty Request or Url"
	// 		);
	// 	}

	// 	$this->load->helper('web_service');
	// 	$curl = curl_init();
	// 	curl_setopt_array($curl, array(
	// 		CURLOPT_URL => $url,
	// 		CURLOPT_RETURNTRANSFER => true,
	// 		CURLOPT_ENCODING => "",
	// 		CURLOPT_MAXREDIRS => 10,
	// 		CURLOPT_TIMEOUT => 600,
	// 		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	// 		CURLOPT_CUSTOMREQUEST => "POST",
	// 		CURLOPT_POSTFIELDS => json_encode($request),
	// 		CURLOPT_HTTPHEADER => array(
	// 			"Accept: */*",
	// 			"Cache-Control: no-cache",
	// 			"Connection: keep-alive",
	// 			"Content-Length: " . strlen(json_encode($request)),
	// 			"Content-Type: application/json",
	// 			"Host: bizpre.adityabirlahealth.com",
	// 		),
	// 	));
	// 	$response = curl_exec($curl);
	// 	$err = curl_error($curl);
	// 	if ($response == '' || $response == NULL) {
	// 		$response = $err;
	// 	}
	// 	if ($err) {
	// 		$return = array(
	// 			"status" => "Error",
	// 			"message" => $err
	// 		);
	// 	} else {
	// 		if (empty($response)) {
	// 			$return = array(
	// 				"status" => "Error",
	// 				"message" => "Empty Response"
	// 			);
	// 		} else {
	// 			$response = XML2Array::createArray($response);
	// 			if ($response["errorObj"]["ErrorMessage"] == "Success") {
	// 				$return = array(
	// 					"status" => "Success",
	// 					"data" => $response
	// 				);
	// 			} else {
	// 				$return = array(
	// 					"status" => "Error",
	// 					"message" => $response["errorObj"]["ErrorMessage"]
	// 				);
	// 			}
	// 		}
	// 	}
	// 	return $return;
	// }

	/*
	Author : Amol Koli
	Date : 30th dec, 2020
	***/
	function saveProposalResponse($responseGhi, $policyData, $statusRes, $log_docs_ref_id)
	{

		$QuotationNumber = isset($responseGhi["policyDtls"]["QuotationNumber"]) ? $responseGhi["policyDtls"]["QuotationNumber"] : "";
		$ProductName = isset($responseGhi["policyDtls"]["ProductName"]) ? $responseGhi["policyDtls"]["ProductName"] : "";
		$ProductCode = isset($responseGhi["policyDtls"]["ProductCode"]) ? $responseGhi["policyDtls"]["ProductCode"] : "";
		$PolicyNumber = isset($responseGhi["policyDtls"]["PolicyNumber"]) ? $responseGhi["policyDtls"]["PolicyNumber"] : "";
		$BasePremium = isset($responseGhi["premium"]["BasePremium"]) ? $responseGhi["premium"]["BasePremium"] : "";
		$NetPremium = isset($responseGhi["premium"]["NetPremium"]) ? $responseGhi["premium"]["NetPremium"] : "";
		$GST = isset($responseGhi["premium"]["GST"]) ? $responseGhi["premium"]["GST"] : "";
		$GrossPremium = isset($responseGhi["premium"]["GrossPremium"]) ? $responseGhi["premium"]["GrossPremium"] : "";

		$lead_id = isset($policyData['lead_id']) ? $policyData['lead_id'] : '';
		$proposal_policy_id = isset($policyData['proposal_policy_id']) ? $policyData['proposal_policy_id'] : '';
		$policy_sub_type_id = isset($policyData['policy_sub_type_id']) ? $policyData['policy_sub_type_id'] : '';
		$customer_id = isset($policyData['customer_id']) ? $policyData['customer_id'] : '';
		$master_policy_id = isset($policyData['master_policy_id']) ? $policyData['master_policy_id'] : '';

		$request_arr = [
			"lead_id" => $lead_id,
			"proposal_policy_id" => $proposal_policy_id,
			"policy_sub_type_id" => $policy_sub_type_id,
			"cust_id" => $customer_id,
			"master_policy_id" => $master_policy_id,
			"log_docs_ref_id" => $log_docs_ref_id,
			"QuotationNumber" => $QuotationNumber,
			"PolicyNumber" => $PolicyNumber,
			"product_name" => $ProductName,
			"product_code" => $ProductCode,
			"base_premium" => $BasePremium,
			"net_premium" => $NetPremium,
			"gst" => $GST,
			"gross_premium" => $GrossPremium,
			"status" => $statusRes,
			"count" => 0,
			"cron_count" => 0
		];

		if(isset($policyData['is_combination'])){

			$request_arr['is_combination'] = $policyData['is_combination'];
		}

		$quoteResponse = $this->db->query("select id from quote_response 
		where lead_id='$lead_id' AND proposal_policy_id='$proposal_policy_id'
		AND cust_id='$customer_id' AND master_policy_id='$master_policy_id'
		AND policy_sub_type_id='$policy_sub_type_id'
		")->row_array();

		if ($quoteResponse > 0) {
			$this->db->where("lead_id", $lead_id);
			$this->db->where("proposal_policy_id", $proposal_policy_id);
			$this->db->where("cust_id", $customer_id);
			$this->db->where("master_policy_id", $master_policy_id);
			$this->db->where("policy_sub_type_id", $policy_sub_type_id);
			$this->db->update("quote_response", $request_arr);
			$insert_id = $quoteResponse['id'];
		} else {
			$this->db->insert("quote_response", $request_arr);
			$insert_id = $this->db->insert_id();
		}
		//echo $this->db->last_query();exit;
		$updateProposalPolicyStatus = $this->updateProposalPolicyStatus('proposal_policy_id', $proposal_policy_id, "Quick-Quote-Done");
		return $insert_id;
	}
	// function saveProposalResponse($responseGhi, $policyData, $policy_number, $proposal_details_id, $statusRes, $log_docs_ref_id)
	// {

	// 	$QuotationNumber = isset($responseGhi["policyDtls"]["QuotationNumber"]) ? $responseGhi["policyDtls"]["QuotationNumber"] : "";
	// 	$ProductName = isset($responseGhi["policyDtls"]["ProductName"]) ? $responseGhi["policyDtls"]["ProductName"] : "";
	// 	$ProductCode = isset($responseGhi["policyDtls"]["ProductCode"]) ? $responseGhi["policyDtls"]["ProductCode"] : "";
	// 	$PolicyNumber = isset($responseGhi["policyDtls"]["PolicyNumber"]) ? $responseGhi["policyDtls"]["PolicyNumber"] : "";
	// 	$BasePremium = isset($responseGhi["premium"]["BasePremium"]) ? $responseGhi["premium"]["BasePremium"] : "";
	// 	$NetPremium = isset($responseGhi["premium"]["NetPremium"]) ? $responseGhi["premium"]["NetPremium"] : "";
	// 	$GST = isset($responseGhi["premium"]["GST"]) ? $responseGhi["premium"]["GST"] : "";
	// 	$GrossPremium = isset($responseGhi["premium"]["GrossPremium"]) ? $responseGhi["premium"]["GrossPremium"] : "";

	// 	$lead_id = isset($policyData[$proposal_details_id][$policy_number]['lead_id']) ? $policyData[$proposal_details_id][$policy_number]['lead_id'] : '';
	// 	$proposal_policy_id = isset($policyData[$proposal_details_id][$policy_number]['proposal_policy_id']) ? $policyData[$proposal_details_id][$policy_number]['proposal_policy_id'] : '';
	// 	$policy_sub_type_id = isset($policyData[$proposal_details_id][$policy_number]['policy_sub_type_id']) ? $policyData[$proposal_details_id][$policy_number]['policy_sub_type_id'] : '';
	// 	$customer_id = isset($policyData[$proposal_details_id][$policy_number]['customer_id']) ? $policyData[$proposal_details_id][$policy_number]['customer_id'] : '';
	// 	$master_policy_id = isset($policyData[$proposal_details_id][$policy_number]['master_policy_id']) ? $policyData[$proposal_details_id][$policy_number]['master_policy_id'] : '';

	// 	$request_arr = [
	// 		"lead_id" => $lead_id,
	// 		"proposal_policy_id" => $proposal_policy_id,
	// 		"policy_sub_type_id" => $policy_sub_type_id,
	// 		"cust_id" => $customer_id,
	// 		"master_policy_id" => $master_policy_id,
	// 		"log_docs_ref_id" => $log_docs_ref_id,
	// 		"QuotationNumber" => $QuotationNumber,
	// 		"PolicyNumber" => $PolicyNumber,
	// 		"product_name" => $ProductName,
	// 		"product_code" => $ProductCode,
	// 		"base_premium" => $BasePremium,
	// 		"net_premium" => $NetPremium,
	// 		"gst" => $GST,
	// 		"gross_premium" => $GrossPremium,
	// 		"status" => $statusRes,
	// 		"count" => 0,
	// 		"cron_count" => 0
	// 	];

	// 	$quoteResponse = $this->db->query("select id from quote_response 
	// 	where lead_id='$lead_id' AND proposal_policy_id='$proposal_policy_id'
	// 	AND cust_id='$customer_id' AND master_policy_id='$master_policy_id'
	// 	AND policy_sub_type_id='$policy_sub_type_id'
	// 	")->row_array();

	// 	if ($quoteResponse > 0) {
	// 		$this->db->where("lead_id", $lead_id);
	// 		$this->db->where("proposal_policy_id", $proposal_policy_id);
	// 		$this->db->where("cust_id", $customer_id);
	// 		$this->db->where("master_policy_id", $master_policy_id);
	// 		$this->db->where("policy_sub_type_id", $policy_sub_type_id);
	// 		$this->db->update("quote_response", $request_arr);
	// 		$insert_id = $quoteResponse['id'];
	// 	} else {
	// 		$this->db->insert("quote_response", $request_arr);
	// 		$insert_id = $this->db->insert_id();
	// 	}
	// 	$updateProposalPolicyStatus = $this->updateProposalPolicyStatus('proposal_policy_id', $proposal_policy_id, "Quick-Quote-Done");
	// 	return $insert_id;
	// }

	/*
	Author : Amol Koli
	Date : 05th jan, 2021
	***/
	function updateProposalPolicyStatus($column_name, $column_value, $status)
	{
		$proposalPolicy = $this->db->query("SELECT proposal_policy_id FROM proposal_policy WHERE `$column_name` = '$column_value'")->row_array();
		if ($proposalPolicy > 0) {
			$updateArr = ["status" => $status];
			$this->db->where($column_name, $column_value);
			$this->db->update("proposal_policy", $updateArr);
			$insert_id = $proposalPolicy['proposal_policy_id'];
		}
		return true;
	}

	/*
	Author : Amol Koli
	Date : 30th dec, 2020
	***/
	function getPolicyCode($policy_sub_type_id)
	{
		$data = $this->db->query("select code from master_policy_sub_type where policy_sub_type_id='$policy_sub_type_id' ")->row();
		return $data->code;
	}



	/*
	Author : Amol Koli
	Date : 01th jan, 2021
	***/
	function saveApiProposalResponse($responseGhi, $policyData, $statusRes, $log_docs_ref_id)
	{
		$ClientID = isset($responseGhi["policyDtls"]["ClientID"]) ? $responseGhi["policyDtls"]["ClientID"] : "";
		$CertificateNumber = isset($responseGhi["policyDtls"]["CertificateNumber"]) ? $responseGhi["policyDtls"]["CertificateNumber"] : "";
		$QuotationNumber = isset($responseGhi["policyDtls"]["QuotationNumber"]) ? $responseGhi["policyDtls"]["QuotationNumber"] : "";
		$ProposalNumber = isset($responseGhi["policyDtls"]["ProposalNumber"]) ? $responseGhi["policyDtls"]["ProposalNumber"] : "";
		$PolicyNumber = isset($responseGhi["policyDtls"]["PolicyNumber"]) ? $responseGhi["policyDtls"]["PolicyNumber"] : "";
		$NetPremium = isset($responseGhi["premium"]["NetPremium"]) ? $responseGhi["premium"]["NetPremium"] : "";
		$GST = isset($responseGhi["premium"]["GST"]) ? $responseGhi["premium"]["GST"] : "";
		$GrossPremium = isset($responseGhi["premium"]["GrossPremium"]) ? $responseGhi["premium"]["GrossPremium"] : "";
		$startDate = isset($responseGhi["policyDtls"]["startDate"]) ? $responseGhi["policyDtls"]["startDate"] : "";
		$EndDate = isset($responseGhi["policyDtls"]["EndDate"]) ? $responseGhi["policyDtls"]["EndDate"] : "";
		$PolicyStatus = isset($responseGhi["policyDtls"]["PolicyStatus"]) ? $responseGhi["policyDtls"]["PolicyStatus"] : "";
		$LetterURL = isset($responseGhi["policyDtls"]["LetterURL"]) ? $responseGhi["policyDtls"]["LetterURL"] : "";
		$MemberCustomerID = isset($responseGhi["policyDtls"]["MemberCustomerID"]) ? $responseGhi["policyDtls"]["MemberCustomerID"] : "";
		$COIUrl = isset($responseGhi["policyDtls"]["COIUrl"]) ? $responseGhi["policyDtls"]["COIUrl"] : "";
		$ReceiptNumber = isset($responseGhi["receiptObj"]["ReceiptNumber"]) ? $responseGhi["receiptObj"]["ReceiptNumber"] : "";

		$lead_id = isset($policyData['lead_id']) ? $policyData['lead_id'] : '';
		$proposal_policy_id = isset($policyData['proposal_policy_id']) ? $policyData['proposal_policy_id'] : '';
		$policy_sub_type_id = isset($policyData['policy_sub_type_id']) ? $policyData['policy_sub_type_id'] : '';
		$customer_id = isset($policyData['customer_id']) ? $policyData['customer_id'] : '';
		$master_policy_id = isset($policyData['master_policy_id']) ? $policyData['master_policy_id'] : '';

		$request_arr = array(
			"lead_id" => $lead_id,
			"client_id" => $ClientID,
			"certificate_number" => $CertificateNumber,
			"quotation_no" => $QuotationNumber,
			"proposal_no" => $ProposalNumber,
			"policy_sub_type_id" => $policy_sub_type_id,
			"master_policy_id" => $master_policy_id,
			"policy_no" => $PolicyNumber,
			"gross_premium" => $GrossPremium,
			"status" => $statusRes,
			"start_date" => date('Y-m-d H:i:s', strtotime($startDate)),
			"end_date" => date('Y-m-d H:i:s', strtotime($EndDate)),
			"created_date" => date('Y-m-d H:i:s'),
			"proposal_policy_id" => $proposal_policy_id,
			"PolicyStatus" => $PolicyStatus,
			"letter_url" => $LetterURL,
			"customer_id" => $customer_id,
			"MemberCustomerID" => $MemberCustomerID,
			//"COI_Url" => $COIUrl
			"ReceiptNumber" => $ReceiptNumber
		);

		if(isset($policyData['is_combination'])){

			$request_arr['is_combination'] = $policyData['is_combination'];
		}

		$apiProposalResponse = $this->db->query("SELECT pr_api_id FROM api_proposal_response 
		WHERE lead_id='$lead_id' AND proposal_policy_id='$proposal_policy_id'
		AND customer_id='$customer_id' AND master_policy_id='$master_policy_id'
		AND policy_sub_type_id='$policy_sub_type_id'")->row_array();

		if ($apiProposalResponse > 0) {
			$this->db->where("lead_id", $lead_id);
			$this->db->where("proposal_policy_id", $proposal_policy_id);
			$this->db->where("customer_id", $customer_id);
			$this->db->where("master_policy_id", $master_policy_id);
			$this->db->where("policy_sub_type_id", $policy_sub_type_id);
			$this->db->update("api_proposal_response", $request_arr);
			$insert_id = $apiProposalResponse['pr_api_id'];
		} else {
			$this->db->insert("api_proposal_response", $request_arr);
			$insert_id = $this->db->insert_id();
		}
		$updateProposalPolicyStatus = $this->updateProposalPolicyStatus('proposal_policy_id', $proposal_policy_id, "Full-Quote-Done");
		return $insert_id;
	}
	// function saveApiProposalResponse($responseGhi, $policyData, $policy_number, $proposal_details_id, $statusRes, $log_docs_ref_id)
	// {
	// 	$ClientID = isset($responseGhi["policyDtls"]["ClientID"]) ? $responseGhi["policyDtls"]["ClientID"] : "";
	// 	$CertificateNumber = isset($responseGhi["policyDtls"]["CertificateNumber"]) ? $responseGhi["policyDtls"]["CertificateNumber"] : "";
	// 	$QuotationNumber = isset($responseGhi["policyDtls"]["QuotationNumber"]) ? $responseGhi["policyDtls"]["QuotationNumber"] : "";
	// 	$ProposalNumber = isset($responseGhi["policyDtls"]["ProposalNumber"]) ? $responseGhi["policyDtls"]["ProposalNumber"] : "";
	// 	$PolicyNumber = isset($responseGhi["policyDtls"]["PolicyNumber"]) ? $responseGhi["policyDtls"]["PolicyNumber"] : "";
	// 	$NetPremium = isset($responseGhi["premium"]["NetPremium"]) ? $responseGhi["premium"]["NetPremium"] : "";
	// 	$GST = isset($responseGhi["premium"]["GST"]) ? $responseGhi["premium"]["GST"] : "";
	// 	$GrossPremium = isset($responseGhi["premium"]["GrossPremium"]) ? $responseGhi["premium"]["GrossPremium"] : "";
	// 	$startDate = isset($responseGhi["policyDtls"]["startDate"]) ? $responseGhi["policyDtls"]["startDate"] : "";
	// 	$EndDate = isset($responseGhi["policyDtls"]["EndDate"]) ? $responseGhi["policyDtls"]["EndDate"] : "";
	// 	$PolicyStatus = isset($responseGhi["policyDtls"]["PolicyStatus"]) ? $responseGhi["policyDtls"]["PolicyStatus"] : "";
	// 	$LetterURL = isset($responseGhi["policyDtls"]["LetterURL"]) ? $responseGhi["policyDtls"]["LetterURL"] : "";
	// 	$MemberCustomerID = isset($responseGhi["policyDtls"]["MemberCustomerID"]) ? $responseGhi["policyDtls"]["MemberCustomerID"] : "";
	// 	$COIUrl = isset($responseGhi["policyDtls"]["COIUrl"]) ? $responseGhi["policyDtls"]["COIUrl"] : "";

	// 	$lead_id = isset($policyData[$proposal_details_id][$policy_number]['lead_id']) ? $policyData[$proposal_details_id][$policy_number]['lead_id'] : '';
	// 	$proposal_policy_id = isset($policyData[$proposal_details_id][$policy_number]['proposal_policy_id']) ? $policyData[$proposal_details_id][$policy_number]['proposal_policy_id'] : '';
	// 	$policy_sub_type_id = isset($policyData[$proposal_details_id][$policy_number]['policy_sub_type_id']) ? $policyData[$proposal_details_id][$policy_number]['policy_sub_type_id'] : '';
	// 	$customer_id = isset($policyData[$proposal_details_id][$policy_number]['customer_id']) ? $policyData[$proposal_details_id][$policy_number]['customer_id'] : '';
	// 	$master_policy_id = isset($policyData[$proposal_details_id][$policy_number]['master_policy_id']) ? $policyData[$proposal_details_id][$policy_number]['master_policy_id'] : '';

	// 	$request_arr = array(
	// 		"lead_id" => $lead_id,
	// 		"client_id" => $ClientID,
	// 		"certificate_number" => $CertificateNumber,
	// 		"quotation_no" => $QuotationNumber,
	// 		"proposal_no" => $ProposalNumber,
	// 		"policy_sub_type_id" => $policy_sub_type_id,
	// 		"master_policy_id" => $master_policy_id,
	// 		"policy_no" => $PolicyNumber,
	// 		"gross_premium" => $GrossPremium,
	// 		"status" => $statusRes,
	// 		"start_date" => date('Y-m-d H:i:s', strtotime($startDate)),
	// 		"end_date" => date('Y-m-d H:i:s', strtotime($EndDate)),
	// 		"created_date" => date('Y-m-d H:i:s'),
	// 		"proposal_policy_id" => $proposal_policy_id,
	// 		"PolicyStatus" => $PolicyStatus,
	// 		"letter_url" => $LetterURL,
	// 		"customer_id" => $customer_id,
	// 		"MemberCustomerID" => $MemberCustomerID,
	// 		"COI_Url" => $COIUrl
	// 	);

	// 	$apiProposalResponse = $this->db->query("SELECT pr_api_id FROM api_proposal_response 
	// 	WHERE lead_id='$lead_id' AND proposal_policy_id='$proposal_policy_id'
	// 	AND customer_id='$customer_id' AND master_policy_id='$master_policy_id'
	// 	AND policy_sub_type_id='$policy_sub_type_id'")->row_array();

	// 	if ($apiProposalResponse > 0) {
	// 		$this->db->where("lead_id", $lead_id);
	// 		$this->db->where("proposal_policy_id", $proposal_policy_id);
	// 		$this->db->where("customer_id", $customer_id);
	// 		$this->db->where("master_policy_id", $master_policy_id);
	// 		$this->db->where("policy_sub_type_id", $policy_sub_type_id);
	// 		$this->db->update("api_proposal_response", $request_arr);
	// 		$insert_id = $apiProposalResponse['pr_api_id'];
	// 	} else {
	// 		$this->db->insert("api_proposal_response", $request_arr);
	// 		$insert_id = $this->db->insert_id();
	// 	}
	// 	$updateProposalPolicyStatus = $this->updateProposalPolicyStatus('proposal_policy_id', $proposal_policy_id, "Full-Quote-Done");
	// 	return $insert_id;
	// }

	/*
	Author : Amol Koli
	Date : 01th jan, 2021
	***/
	function getQuickQuoteData($lead_id, $proposal_policy_id = '')
	{
		$where_extra = '';
		if (!empty($proposal_policy_id)) {
			$where_extra = 'AND pp.proposal_policy_id = ' . $proposal_policy_id;
		}

		/*$arr_proposal_policy = $this->db->query("SELECT pp.proposal_policy_id, pp.group_code, pp.master_policy_id, pp.proposal_details_id, mc.salutation, mc.gender,
		pp.sum_insured,pp.adult_count, pp.child_count, pp.policy_sub_type_name, mp.plan_code, mp.product_code, mp.scheme_code, ppd.go_green, bim.branch_code, bim.imd_code,
		mp.policy_sub_type_id, mp.policy_number, mc.first_name, mc.middle_name, mc.last_name ,mc.pincode as pin_code, mc.address_line1 as customer_address_line1, mc.address_line2 as customer_address_line2, mc.address_line3 as customer_address_line3, mc.mobile_no as mobile_no, ld.plan_id,mc.customer_id, mc.email_id as customer_email, mc.mobile_no as customer_mobile, mc.pan, mc.dob, pd.nominee_relation, pd.nominee_first_name, pd.nominee_last_name, pd.nominee_salutation, pd.nominee_gender, pd.nominee_dob, pd.nominee_contact, pd.nominee_email, mp.source_name, ps.code
		FROM proposal_policy as pp 
		JOIN master_policy as mp ON mp.policy_id=pp.master_policy_id 
		JOIN proposal_details as pd ON pd.proposal_details_id=pp.proposal_details_id 
		JOIN master_customer as mc ON mc.customer_id=pd.customer_id 
		JOIN lead_details as ld ON ld.lead_id=pd.lead_id 
		JOIN proposal_payment_details as ppd ON ppd.lead_id=ld.lead_id 
		JOIN master_policy_sub_type as ps ON mp.policy_sub_type_id=ps.policy_sub_type_id
        JOIN branch_imd_mapping as bim ON mp.policy_number = bim.policy_number
		WHERE  pp.lead_id = '$lead_id' $where_extra 
		ORDER BY mp.policy_sub_type_id ASC")->result_array();*/
        $arr_proposal_policy = $this->db->query("SELECT pp.proposal_policy_id, pp.group_code, pp.master_policy_id, pp.proposal_details_id, mc.salutation, mc.gender,
		pp.sum_insured,pp.adult_count, pp.child_count, pp.policy_sub_type_name, mp.plan_code, mp.product_code, mp.scheme_code, ppd.go_green, 
		mp.policy_sub_type_id, mp.policy_number, mc.first_name, mc.middle_name, mc.last_name ,mc.pincode as pin_code, mc.address_line1 as customer_address_line1, mc.address_line2 as customer_address_line2, mc.address_line3 as customer_address_line3, mc.mobile_no as mobile_no, ld.plan_id,mc.customer_id, mc.email_id as customer_email, mc.mobile_no as customer_mobile, mc.pan, mc.dob, pd.nominee_relation, pd.nominee_first_name, pd.nominee_last_name, pd.nominee_salutation, pd.nominee_gender, pd.nominee_dob, pd.nominee_contact, pd.nominee_email, mp.source_name, ps.code
		FROM proposal_policy as pp 
		JOIN master_policy as mp ON mp.policy_id=pp.master_policy_id 
		JOIN proposal_details as pd ON pd.proposal_details_id=pp.proposal_details_id 
		JOIN master_customer as mc ON mc.customer_id=pd.customer_id 
		JOIN lead_details as ld ON ld.lead_id=pd.lead_id 
		JOIN proposal_payment_details as ppd ON ppd.lead_id=ld.lead_id 
		JOIN master_policy_sub_type as ps ON mp.policy_sub_type_id=ps.policy_sub_type_id
        
		WHERE  pp.lead_id = '$lead_id' $where_extra 
		ORDER BY mp.policy_sub_type_id ASC")->result_array();

		//echo $this->db->last_query();exit;
		return $arr_proposal_policy;
	}
	// function getQuickQuoteData($lead_id, $mode_of_payment)
	// {
	// 	$arr_proposal_policy = $this->db
	// 		->select('pp.proposal_policy_id,pp.master_policy_id,pp.proposal_details_id,
	// 		pp.sum_insured,pp.adult_count,pp.child_count,
	// 		pp.policy_sub_type_name,pp.policy_sub_type_id,mp.policy_number,pp.sum_insured,
	// 		mc.pincode as pin_code,
	// 		mc.address_line1 as address_line1,
	// 		mc.address_line2 as address_line2,
	// 		mc.address_line3 as address_line3,
	// 		mc.mobile_no as mobile_no,
	// 		pd.plan_id,
	// 		mc.customer_id,
	// 		,pd.*,mc.*,ld.*')
	// 		->from('proposal_policy as pp')
	// 		->join('master_policy as mp', 'mp.policy_id=pp.master_policy_id')
	// 		->join('proposal_details as pd', 'pd.proposal_details_id=pp.proposal_details_id')
	// 		->join('master_customer as mc', 'mc.customer_id=pd.customer_id')
	// 		->join('lead_details as ld', 'ld.lead_id=pd.lead_id')
	// 		->where('pp.lead_id', $lead_id)
	// 		->where('pd.mode_of_payment', $mode_of_payment)
	// 		->get()
	// 		->result_array();
	// 	return $arr_proposal_policy;
	// }


	/*
	Author : Amol Koli
	Date : 01th jan, 2021
	//$new_url = get_tiny_url('https://davidwalsh.name/php-imdb-information-grabber');
	***/
	function get_tiny_url($url)
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	/*
	Author : Amol Koli
	Date : 02th jan, 2021
	***/
	function doCommunicate($request)
	{
		$url = "http://10.1.226.32/ABHICL_ClickPSS/Service1.svc/click";
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 90,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($request),
			CURLOPT_HTTPHEADER => array(
				"Accept: */*",
				"Cache-Control: no-cache",
				"Connection: keep-alive",
				"Content-Length: " . strlen(json_encode($request)),
				"Content-Type: application/json"
			),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);

		if ($response == '' || $response == NULL) {
			$response = $err;
		}
		if ($err) {
			$return = array(
				"status" => "Error",
				"msg" => $err
			);
		} else {
			return $response;
		}
	}



	function check_error_data_m()
	{
		$lead_id = $this->input->post('lead_id');

		$lead_id_encrypt = encrypt_decrypt_password($lead_id);

		$query_check = $this
			->db
			->query("select * from quote_response where lead_id='$lead_id' and status = 'success'")->row_array();

		if ($query_check > 0) {

			$query = $this
				->db
				->query("select pd.payment_status,pd.proposal_status from lead_details as ed,proposal_payment_details as pd where ed.lead_id=pd.lead_id and ed.lead_id = '$lead_id'")->row_array();

			if ($query['payment_status'] == 'Success' && $query['proposal_status'] != 'Success') {
				// quote genarate,payment done but policy pending
				$data = array(
					"status" => "1",
					"check" => "2",
					"url" => base_url() . "api2/payment_success_view/" . $lead_id_encrypt
				);
			} else {
				// quote genarate but payment pending
				$data = array(
					"status" => "1",
					"check" => "1",
					"url" => base_url() . "api2/payment_redirection/" . $lead_id_encrypt
				);
			}
		} else {

			$query = $this
				->db
				->query("select * from quote_response where lead_id='$lead_id' and status = 'error'")->row_array();

			if ($query > 0) {
				// quote pending,payment pending
				$data = array(
					"status" => "1",
					"check" => "1",
					"url" => base_url() . "api2/payment_redirection/" . $lead_id_encrypt
				);
			} else {
				// quote pending 3 count hit exceeded
				$data = array(
					"status" => "2",
					"check" => "3",
					"url" => "#"
				);
			}
		}

		return $data;
	}

	function coi_download_m($certificate_no = '')
	{
		if (empty($certificate_no)) {
			$certificate_no = $this
				->input
				->post('certificate_no');
		}

		$data = $this
			->db
			->query("select ed.lead_id,ed.plan_id,apr.certificate_number,apr.letter_url,apr.pr_api_id from lead_details as ed,api_proposal_response as apr where ed.lead_id = apr.lead_id and apr.certificate_number = '$certificate_no' ")->row_array();

		//$result = ["status" => "error", "url" => ""];

		if ($data['letter_url'] == "false" || $data['letter_url'] == "") {

			$url = 'https://bizpre.adityabirlahealth.com/ABHICL_OmniDocs/Service1.svc/searchRequest';

			// TODO: Need to pass Value Dynamically based on COI Number
			$req_arr = ["SearchRequest" => [["CategoryID" => "", "DocumentID" => "", "ReferenceID" => "", "FileName" => "", "Description" => "", "DataClassParam" => [[
				"DocSearchParamId" => "23", 
				//"Value" => "GHI-20-2000458",
				"Value"=>$certificate_no,
			]]]], "SourceSystemName" => "Axis", "SearchOperator" => "Or"];

			$request = json_encode($req_arr);

			$res = $this->curl_call($url, $request);
			$response = json_decode($res, true);

			$request_arr = ["lead_id" => $data['lead_id'], "product_id" => $data['plan_id'], "req" => json_encode($request), "res" => json_encode($response), "type" => "coi_genarate_req1"];

			$this->db->insert("logs_docs", $request_arr);

			//Application log entries
			$this->db->insert("application_logs", [
				"lead_id" => $data['lead_id'],
				"action" => "coi_genarate_req1",
				"request_data" => json_encode($request),
				"response_data" => json_encode($response),
				"created_on" => date("Y-m-d H:i:s")
			]);

			if (isset($response['SearchResponse']) && $response['SearchResponse']) {
				$ress = $response['SearchResponse'];

				if ($ress[0]['Error'][0]['Code'] == 0) {
					$check_data = $ress[0]['OmniDocImageIndex'];
				}
			}

			if (!empty($check_data)) {

				$url = 'https://bizpre.adityabirlahealth.com/ABHICL_OmniDocs/Service1.svc/downloadRequest';

				$req_arr = ['DownloadRequest' => [['GlobalId' => "", 'OmniDocImageIndex' => $ress[0]['OmniDocImageIndex'], 'FileName' => $ress[0]['FileName'],],], 'Identifier' => 'ByteArray', 'SourceSystemName' => 'Axis',];

				$request = json_encode($req_arr);

				$res = $this->curl_call($url, $request);
				$response = json_decode($res, true);

				$request_arr = ["lead_id" => $data['lead_id'], "product_id" => $data['plan_id'], "req" => json_encode($request), "res" => json_encode($response['DownloadResponse'][0]['Error'][0]['Description']), "type" => "coi_genarate_req2"];

				$this->db->insert("logs_docs", $request_arr);
				//print_r($response);exit;

				//Application log entries
				$this->db->insert("application_logs", [
					"lead_id" => $data['lead_id'],
					"action" => "coi_genarate_req2",
					"request_data" => json_encode($request),
					"response_data" => json_encode($response['DownloadResponse'][0]['Error'][0]['Description']),
					"created_on" => date("Y-m-d H:i:s")
				]);

				if ($response) {
					$ress = $response['DownloadResponse'];

					if ($ress[0]['Error'][0]['Code'] == 0) {

						$decoded = base64_decode($ress[0]['ByteArray']);
						$file = $ress[0]['FileName'];
						$path = APPPATH . '/resources/' . $file;
						file_put_contents($path, $decoded);
						// move_uploaded_file($a, $path);
						$update_arr = ["letter_url" => '/resources/' . $file];

						$this
							->db
							->where("pr_api_id", $data['pr_api_id']);
						$this
							->db
							->update("api_proposal_response", $update_arr);

						$result = array(
							"status" => "success",
							"url" => '/resources/' . $file
						);
					}
				}
			}
		} else {
			$result = array(
				"status" => "success",
				"url" => $data['letter_url']
			);
		}

		return $result;
	}

	public function curl_call($url, $request)
	{

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 60,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $request,
			CURLOPT_HTTPHEADER => array(
				"username: esb_axis",
				"password: esb@axis@ABHI",
				"Accept: */*",
				"Cache-Control: no-cache",
				"Connection: keep-alive",
				"Content-Length: " . strlen($request),
				"Content-Type: application/json",
				"Host: bizpre.adityabirlahealth.com"
			),
		));

		return $response = curl_exec($curl);
	}
	// function doCommunicate($request)
	// {
	// 	$url = "http://10.1.226.32/ABHICL_ClickPSS/Service1.svc/click";
	// 	$curl = curl_init();
	// 	curl_setopt_array($curl, array(
	// 		CURLOPT_URL => $url,
	// 		CURLOPT_RETURNTRANSFER => true,
	// 		CURLOPT_ENCODING => "",
	// 		CURLOPT_MAXREDIRS => 10,
	// 		CURLOPT_TIMEOUT => 600,
	// 		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	// 		CURLOPT_CUSTOMREQUEST => "POST",
	// 		CURLOPT_POSTFIELDS => json_encode($request),
	// 		CURLOPT_HTTPHEADER => array(
	// 			"Accept: */*",
	// 			"Cache-Control: no-cache",
	// 			"Connection: keep-alive",
	// 			"Content-Length: " . strlen(json_encode($request)),
	// 			"Content-Type: application/json"
	// 		),
	// 	));
	// 	$response = curl_exec($curl);
	// 	$err = curl_error($curl);

	// 	if ($response == '' || $response == NULL) {
	// 		$response = $err;
	// 	}
	// 	if ($err) {
	// 		$return = array(
	// 			"status" => "Error",
	// 			"msg" => $err
	// 		);
	// 	} else {
	// 		if (empty($response)) {
	// 			return $response;
	// 		}
	// 	}
	// }

	function getTriggers($name)
	{
		$this->db->select('*');
		$data = $this->db->get_where('comm_triggers', array('comm_trigger' => $name))->row();
		return $data;
	}

	// function getTriggers($name)
	// {
	// 	$this->db->select('*');
	// 	$data = $this->db->get_where('comm_triggers', array('comm_trigger' => $name))->row();
	// 	return $data;
	// }

	/*
	Author : Amol Koli
	Date : 24th dec, 2020
	***/
	// function doQuickQuote($lead_id, $mode_of_payment)
	// {
	// 	//ini_set('display_errors', 1);
	// 	$arr_proposal_policy = $this->apimodel->getQuickQuoteData($lead_id, $mode_of_payment);
	// 	//print_r($arr_proposal_policy);
	// 	//exit;

	// 	$nominees = $member = $policyData = [];
	// 	if (!empty($arr_proposal_policy)) {
	// 		foreach ($arr_proposal_policy as $row) {
	// 			$policy_sub_type_id = isset($row['policy_sub_type_id']) ? $row['policy_sub_type_id'] : '';
	// 			$proposal_policy_id = isset($row['proposal_policy_id']) ? $row['proposal_policy_id'] : '';
	// 			$proposal_details_id = isset($row['proposal_details_id']) ? $row['proposal_details_id'] : '';
	// 			$master_policy_id = isset($row['master_policy_id']) ? $row['master_policy_id'] : '';
	// 			$plan_id = isset($row['plan_id']) ? $row['plan_id'] : '';
	// 			$customer_id = isset($row['customer_id']) ? $row['customer_id'] : '';
	// 			$SumInsured = isset($row['sum_insured']) ? $row['sum_insured'] : '';
	// 			$adult_count = isset($row['adult_count']) ? $row['adult_count'] : '';
	// 			$child_count = isset($row['child_count']) ? $row['child_count'] : '';
	// 			$policy_number = $this->apimodel->getPolicyCode($policy_sub_type_id);

	// 			$proposal_policy[$policy_number] = $row;
	// 			$getPolicyMemberDetails = $this->apimodel->getPolicyMemberDetails($proposal_policy_id, $master_policy_id, $lead_id);
	// 			$SumInsured_Type = $this->apimodel->getSumInsuredType($master_policy_id);
	// 			$getGroupCode = $this->apimodel->getGroupCode($policy_sub_type_id, (int) $SumInsured, (int) $adult_count, (int) $child_count, $getPolicyMemberDetails);
	// 			$MasterPolicyNumber = $this->apimodel->getMasterPolicyNumber($master_policy_id);


	// 			$ukey = $proposal_details_id . '__' . $policy_number;
	// 			$policyData[$proposal_details_id][$policy_number]['api_url'] = $this->apimodel->getApiUrl($policy_sub_type_id);
	// 			$policyData[$proposal_details_id][$policy_number]['proposal_policy_id'] = $proposal_policy_id;
	// 			$policyData[$proposal_details_id][$policy_number]['plan_id'] = $plan_id;
	// 			$policyData[$proposal_details_id][$policy_number]['policy_sub_type_id'] = $policy_sub_type_id;
	// 			$policyData[$proposal_details_id][$policy_number]['lead_id'] = $lead_id;
	// 			$policyData[$proposal_details_id][$policy_number]['customer_id'] = $customer_id;
	// 			$policyData[$proposal_details_id][$policy_number]['master_policy_id'] = $master_policy_id;

	// 			$arr_proposal_policy_id[$proposal_details_id][$policy_number] = $ukey;

	// 			/* Nominees Details*/
	// 			if (empty($nominees)) {
	// 				$nominees['nominee_relation'] = $row['nominee_relation'];
	// 				$nominees['nominee_first_name'] = $row['nominee_first_name'];
	// 				$nominees['nominee_last_name'] = $row['nominee_last_name'];
	// 				$nominees['nominee_salutation'] = $row['nominee_salutation'];
	// 				$nominees['nominee_gender'] = $row['nominee_gender'];
	// 				$nominees['nominee_dob'] = $row['nominee_dob'];
	// 				$nominees['nominee_contact'] = $row['nominee_contact'];
	// 				$nominees['nominee_email'] = $row['nominee_email'];
	// 			}

	// 			$member = $this->apimodel->getMembersForQuote($row, $getPolicyMemberDetails, $nominees);
	// 			/*
	// 			if($policy_number == "GHI"){
	// 				print_r($member);
	// 				exit;
	// 			}*/

	// 			$SourceSystemName_api = $this->apimodel->getSourceName($master_policy_id);
	// 			$uidNo = isset($getPolicyMemberDetails[0]['policy_member_pan']) ? $getPolicyMemberDetails[0]['policy_member_pan'] : null;
	// 			$GroupID = isset($getGroupCode['groupCode']) ? $getGroupCode['groupCode'] : '';
	// 			$plan_code = $this->apimodel->getPlanCode($master_policy_id);
	// 			$Product_Code = $this->apimodel->getProductCode($master_policy_id);
	// 			$SchemeCode = $this->apimodel->getSchemeCode($master_policy_id);
	// 			$intermediaryCode = $this->apimodel->getIntermediaryCode($master_policy_id);

	// 			$Member_Type_Code = 'M209';
	// 			//$intermediaryCode = '2108233';
	// 			$intermediaryBranchCode = '10MHMUM01';
	// 			$product_id = isset($row['plan_id']) ? $row['plan_id'] : "";
	// 			$SPID = 0;
	// 			$RefCode1 = 0;
	// 			$RefCode2 = 0;
	// 			$Policy_Tanure = 1;
	// 			$AutoRenewal = 'Y';
	// 			$occupation = "Self Employed";
	// 			$explode_name = array($row['first_name'], $row['middle_name'], $row['last_name']);
	// 			$explode_name_nominee = array($row['nominee_first_name'], $row['nominee_last_name']);

	// 			//echo $policy_detail_id;exit;
	// 			if ($policy_sub_type_id == 1) { // ghi
	// 				$fqrequest[$proposal_details_id][$policy_number] = [
	// 					"ClientCreation" =>
	// 					[
	// 						"Member_Customer_ID" => $row['customer_id'],
	// 						"salutation" => $row['salutation'],
	// 						"firstName" => isset($row['first_name']) ? $row['first_name'] : "",
	// 						"middleName" => isset($row['middle_name']) ? $row['middle_name'] : "",
	// 						"lastName" => isset($row['last_name']) ? $row['last_name'] : "",
	// 						"dateofBirth" => date('m/d/Y', strtotime($row['dob'])),
	// 						"gender" => ($row['gender'] == "Male") ? "M" : "F",
	// 						"educationalQualification" => "null",
	// 						"pinCode" => $row['pin_code'],
	// 						"uidNo" => $uidNo,
	// 						"maritalStatus" => null,
	// 						"nationality" => "Indian",
	// 						"occupation" => $occupation,
	// 						"primaryEmailID" => $row['email_id'],
	// 						"contactMobileNo" => substr(trim($row['mobile_no']), -10),
	// 						"stdLandlineNo" => null,
	// 						"panNo" => null,
	// 						"passportNumber" => null,
	// 						"contactPerson" => null,
	// 						"annualIncome" => null,
	// 						"remarks" => null,
	// 						"startDate" => date('Y-m-d'),
	// 						"endDate" => null,
	// 						"IdProof" => "Adhaar Card",
	// 						"residenceProof" => null,
	// 						"ageProof" => null,
	// 						"others" => null,
	// 						"homeAddressLine1" => $row['address_line1'],
	// 						"homeAddressLine2" => $row['address_line2'],
	// 						"homeAddressLine3" => $row['address_line3'],
	// 						"homePinCode" => $row['pin_code'],
	// 						"homeArea" => null,
	// 						"homeContactMobileNo" => substr(trim($row['mobile_no']), -10),
	// 						"homeContactMobileNo2" => null,
	// 						"homeSTDLandlineNo" => null,
	// 						"homeFaxNo" => null,
	// 						"sameAsHomeAddress" => "1",
	// 						"mailingAddressLine1" => null,
	// 						"mailingAddressLine2" => null,
	// 						"mailingAddressLine3" => null,
	// 						"mailingPinCode" => null,
	// 						"mailingArea" => null,
	// 						"mailingContactMobileNo" => null,
	// 						"mailingContactMobileNo2" => null,
	// 						"mailingSTDLandlineNo" => null,
	// 						"mailingSTDLandlineNo2" => null,
	// 						"mailingFaxNo" => null,
	// 						"bankAccountType" => null,
	// 						"bankAccountNo" => null,
	// 						"ifscCode" => null,
	// 						"GSTIN" => null,
	// 						"GSTRegistrationStatus" => "Consumers",
	// 						"IsEIAavailable" => "0",
	// 						"ApplyEIA" => "0",
	// 						"EIAAccountNo" => null,
	// 						"EIAWith" => "0",
	// 						"AccountType" => null,
	// 						"AddressProof" => null,
	// 						"DOBProof" => null,
	// 						"IdentityProof" => null
	// 					],
	// 					"PolicyCreationRequest" => [
	// 						"Quotation_Number" => "",
	// 						"MasterPolicyNumber" => $MasterPolicyNumber,
	// 						"GroupID" => $GroupID,
	// 						"Product_Code" => $Product_Code,
	// 						"intermediaryCode" => $intermediaryCode,
	// 						"AutoRenewal" => $AutoRenewal,
	// 						"intermediaryBranchCode" => $intermediaryBranchCode,
	// 						"agentSignatureDate" => null,
	// 						"Customer_Signature_Date" => null,
	// 						"businessSourceChannel" => null,
	// 						"AssignPolicy" => "0",
	// 						"AssigneeName" => null,
	// 						"leadID" => $lead_id,
	// 						"Source_Name" => $SourceSystemName_api,
	// 						"SPID" => $SPID,
	// 						"TCN" => null,
	// 						"CRTNO" => null,
	// 						"RefCode1" => $RefCode1,
	// 						"RefCode2" => $RefCode2,
	// 						"Employee_Number" => $row['customer_id'],
	// 						"enumIsEmployeeDiscount" => null,
	// 						"QuoteDate" => null,
	// 						"IsPayment" => "0",
	// 						"PaymentMode" => "",
	// 						"PolicyproductComponents" => [
	// 							[
	// 								"PlanCode" => $plan_code,
	// 								"SumInsured" => $SumInsured,
	// 								"SchemeCode" => $SchemeCode
	// 							]
	// 						]
	// 					],
	// 					"MemObj" => ["Member" => $member],
	// 					"ReceiptCreation" => [
	// 						"officeLocation" => "",
	// 						"modeOfEntry" => "",
	// 						"cdAcNo" => null,
	// 						"expiryDate" => null,
	// 						"payerType" => "",
	// 						"payerCode" => null,
	// 						"paymentBy" => "",
	// 						"paymentByName" => null,
	// 						"paymentByRelationship" => null,
	// 						"collectionAmount" => "",
	// 						"collectionRcvdDate" => null,
	// 						"collectionMode" => "",
	// 						"remarks" => null,
	// 						"instrumentNumber" => null,
	// 						"instrumentDate" => null,
	// 						"bankName" => null,
	// 						"branchName" => null,
	// 						"bankLocation" => null,
	// 						"micrNo" => null,
	// 						"chequeType" => null,
	// 						"ifscCode" => "",
	// 						"PaymentGatewayName" => "",
	// 						"TerminalID" => "",
	// 						"CardNo" => null
	// 					]
	// 				];
	// 			} else {
	// 				$fqrequest[$proposal_details_id][$policy_number] = [
	// 					"ClientCreation" =>
	// 					[
	// 						"Member_Customer_ID" => $row['customer_id'],
	// 						"salutation" => $row['salutation'],
	// 						"firstName" => isset($row['first_name']) ? $row['first_name'] : "",
	// 						"middleName" => isset($row['middle_name']) ? $row['middle_name'] : "",
	// 						"lastName" => isset($row['last_name']) ? $row['last_name'] : "",
	// 						"dateofBirth" => date('m/d/Y', strtotime($row['dob'])),
	// 						"gender" => ($row['gender'] == "Male") ? "M" : "F",
	// 						"educationalQualification" => "null",
	// 						"pinCode" => $row['pin_code'],
	// 						"uidNo" => $uidNo,
	// 						"maritalStatus" => null,
	// 						"nationality" => "Indian",
	// 						"occupation" => $occupation,
	// 						"primaryEmailID" => $row['email_id'],
	// 						"contactMobileNo" => substr(trim($row['mobile_no']), -10),
	// 						"stdLandlineNo" => null,
	// 						"panNo" => null,
	// 						"passportNumber" => null,
	// 						"contactPerson" => null,
	// 						"annualIncome" => null,
	// 						"remarks" => null,
	// 						"startDate" => date('Y-m-d'),
	// 						"endDate" => null,
	// 						"IdProof" => "Adhaar Card",
	// 						"residenceProof" => null,
	// 						"ageProof" => null,
	// 						"others" => null,
	// 						"homeAddressLine1" => $row['address_line1'],
	// 						"homeAddressLine2" => $row['address_line2'],
	// 						"homeAddressLine3" => $row['address_line3'],
	// 						"homePinCode" => $row['pin_code'],
	// 						"homeArea" => null,
	// 						"homeContactMobileNo" => substr(trim($row['mobile_no']), -10),
	// 						"homeContactMobileNo2" => null,
	// 						"homeSTDLandlineNo" => null,
	// 						"homeFaxNo" => null,
	// 						"sameAsHomeAddress" => "1",
	// 						"mailingAddressLine1" => null,
	// 						"mailingAddressLine2" => null,
	// 						"mailingAddressLine3" => null,
	// 						"mailingPinCode" => null,
	// 						"mailingArea" => null,
	// 						"mailingContactMobileNo" => null,
	// 						"mailingContactMobileNo2" => null,
	// 						"mailingSTDLandlineNo" => null,
	// 						"mailingSTDLandlineNo2" => null,
	// 						"mailingFaxNo" => null,
	// 						"bankAccountType" => null,
	// 						"bankAccountNo" => null,
	// 						"ifscCode" => null,
	// 						"GSTIN" => null,
	// 						"GSTRegistrationStatus" => "Consumers",
	// 						"IsEIAavailable" => "0",
	// 						"ApplyEIA" => "0",
	// 						"EIAAccountNo" => null,
	// 						"EIAWith" => "0",
	// 						"AccountType" => null,
	// 						"AddressProof" => null,
	// 						"DOBProof" => null,
	// 						"IdentityProof" => null
	// 					],
	// 					"PolicyCreationRequest" => [
	// 						"Quotation_Number" => "",
	// 						"MasterPolicyNumber" => $MasterPolicyNumber,
	// 						"GroupID" => $GroupID,
	// 						"Product_Code" => $Product_Code,
	// 						"intermediaryCode" => $intermediaryCode,
	// 						"AutoRenewal" => $AutoRenewal,
	// 						"intermediaryBranchCode" => $intermediaryBranchCode,
	// 						"agentSignatureDate" => null,
	// 						"Customer_Signature_Date" => null,
	// 						"businessSourceChannel" => null,
	// 						"AssignPolicy" => "0",
	// 						"AssigneeName" => null,
	// 						"leadID" => $lead_id,
	// 						"Source_Name" => $SourceSystemName_api,
	// 						"SPID" => $SPID,
	// 						"TCN" => null,
	// 						"CRTNO" => null,
	// 						"RefCode1" => $RefCode1,
	// 						"RefCode2" => $RefCode2,
	// 						"Employee_Number" => $row['customer_id'],
	// 						"enumIsEmployeeDiscount" => null,
	// 						"QuoteDate" => null,
	// 						"IsPayment" => "0",
	// 						"PaymentMode" => "",
	// 						"PolicyproductComponents" => [
	// 							[
	// 								"PlanCode" => $plan_code,
	// 								"SumInsured" => $SumInsured,
	// 								"SchemeCode" => $SchemeCode
	// 							]
	// 						]
	// 					],
	// 					"MemObj" => ["Member" => $member],
	// 					"ReceiptCreation" => [
	// 						"officeLocation" => "",
	// 						"modeOfEntry" => "",
	// 						"cdAcNo" => null,
	// 						"expiryDate" => null,
	// 						"payerType" => "",
	// 						"payerCode" => null,
	// 						"paymentBy" => "",
	// 						"paymentByName" => null,
	// 						"paymentByRelationship" => null,
	// 						"collectionAmount" => "",
	// 						"collectionRcvdDate" => null,
	// 						"collectionMode" => "",
	// 						"remarks" => null,
	// 						"instrumentNumber" => null,
	// 						"instrumentDate" => null,
	// 						"bankName" => null,
	// 						"branchName" => null,
	// 						"bankLocation" => null,
	// 						"micrNo" => null,
	// 						"chequeType" => null,
	// 						"ifscCode" => "",
	// 						"PaymentGatewayName" => "",
	// 						"TerminalID" => "",
	// 						"CardNo" => null
	// 					]
	// 				];
	// 			}
	// 		}
	// 	}

	// 	//print_r($arr_proposal_policy_id);
	// 	//print_r($fqrequest["55"]["HC"]);
	// 	//exit;
	// 	$arrReturn["Status"] = "Success";
	// 	if (!empty($arr_proposal_policy_id)) {
	// 		foreach ($arr_proposal_policy_id as $key => $row) {
	// 			$fqrequestGhi = isset($fqrequest[$key]['GHI']) ? $fqrequest[$key]['GHI'] : [];
	// 			$urlGhi = isset($policyData[$key]['GHI']['api_url']) ? $policyData[$key]['GHI']['api_url'] : '';
	// 			$product_id_Ghi = isset($policyData[$key]['GHI']['plan_id']) ? $policyData[$key]['GHI']['plan_id'] : '';
	// 			$proposal_policy_id_Ghi = isset($policyData[$key]['GHI']['proposal_policy_id']) ? $policyData[$key]['GHI']['proposal_policy_id'] : '';

	// 			if (empty($fqrequestGhi)) {
	// 				$arrReturn[$key]["GHI"]["status"] = "Error";
	// 				$arrReturn[$key]["GHI"]["message"] = "Empty Request";
	// 				$arrReturn["Status"] = "Error";
	// 				continue;
	// 			}
	// 			$getCurl = $this->apimodel->getCurl($urlGhi, $fqrequestGhi);
	// 			//print_r($getCurl);
	// 			//exit;
	// 			if (!empty($getCurl)) {
	// 				$status = isset($getCurl["status"]) ? $getCurl["status"] : '';
	// 				if (isset($getCurl["status"]) && $getCurl["status"] == "Success") {
	// 					$responseGhi = isset($getCurl["data"]) ? $getCurl["data"] : [];

	// 					$request_arr = [
	// 						"lead_id" => $lead_id,
	// 						"req" => json_encode($fqrequestGhi),
	// 						"res" => json_encode($responseGhi),
	// 						"product_id" => $product_id_Ghi,
	// 						"type" => "quick_quote",
	// 						"proposal_policy_id" => $proposal_policy_id_Ghi
	// 					];
	// 					$this->db->insert("logs_docs", $request_arr);
	// 					$insert_id_ghi = $this->db->insert_id();

	// 					$statusGhi = isset($responseGhi["errorObj"]["ErrorMessage"]) ? $responseGhi["errorObj"]["ErrorMessage"] : "";

	// 					if ($statusGhi == "Success") {

	// 						$saveProposal = $this->apimodel->saveProposalResponse($responseGhi, $policyData, 'GHI', $key, $statusGhi, $insert_id_ghi);
	// 						$arrReturn[$key]["GHI"]["status"] = "Success";
	// 						$arrReturn[$key]["GHI"]["message"] = "";
	// 						if ($saveProposal) {
	// 							if (isset($fqrequest[$key]) && !empty($fqrequest[$key])) {
	// 								foreach ($fqrequest[$key] as $rkey => $arrRequest) {
	// 									//echo "Process started for ".$key . " - ". $rkey;
	// 									if ($rkey == "GHI") {
	// 										continue;
	// 									}

	// 									$product_id = isset($policyData[$key][$rkey]['plan_id']) ? $policyData[$key][$rkey]['plan_id'] : '';
	// 									$proposal_policy_id = isset($policyData[$key][$rkey]['proposal_policy_id']) ? $policyData[$key][$rkey]['proposal_policy_id'] : '';
	// 									$request = isset($fqrequest[$key][$rkey]) ? $fqrequest[$key][$rkey] : [];
	// 									$url = isset($policyData[$key][$rkey]['api_url']) ? $policyData[$key][$rkey]['api_url'] : [];

	// 									if (empty($request)) {
	// 										$arrReturn[$key][$rkey]["status"] = "Error";
	// 										$arrReturn[$key][$rkey]["message"] = "Empty Request";
	// 										$arrReturn["Status"] = "Error";
	// 										continue;
	// 									}

	// 									$getCurl = $this->apimodel->getCurl($url, $request);
	// 									if (!empty($getCurl)) {
	// 										$status = isset($getCurl["status"]) ? $getCurl["status"] : '';
	// 										$message = isset($getCurl["message"]) ? $getCurl["message"] : '';
	// 										//if(isset($getCurl["status"]) && $getCurl["status"] == "Success"){
	// 										$response = isset($getCurl["data"]) ? $getCurl["data"] : [];
	// 										//}
	// 										/*
	// 										if($rkey=="GPA"){
	// 											print_r($response);
	// 											exit;
	// 										}*/

	// 										$request_arr = [];
	// 										$request_arr = [
	// 											"lead_id" => $lead_id,
	// 											"req" => json_encode($request),
	// 											"res" => json_encode($response),
	// 											"product_id" => $product_id,
	// 											"type" => "quick_quote",
	// 											"proposal_policy_id" => $proposal_policy_id
	// 										];
	// 										$this->db->insert("logs_docs", $request_arr);
	// 										$insert_id = $this->db->insert_id();
	// 										if ($status != "Success") {
	// 											$statusMessage = $status . '-' . $message;
	// 										} else {
	// 											$statusMessage = $status;
	// 										}
	// 										$saveProposalResponse = $this->apimodel->saveProposalResponse($response, $policyData, $rkey, $key, $statusMessage, $insert_id);

	// 										$arrReturn[$key][$rkey]["status"] = $status;
	// 										$arrReturn[$key][$rkey]["message"] = $message;
	// 										$arrReturn["Status"] = $status;
	// 									} else {
	// 										$request_arr = [
	// 											"lead_id" => $lead_id,
	// 											"req" => json_encode($fqrequest),
	// 											"product_id" => $product_id,
	// 											"type" => "quick_quote",
	// 											"proposal_policy_id" => $proposal_policy_id
	// 										];
	// 										$this->db->insert("logs_docs", $request_arr);
	// 										$insert_id = $this->db->insert_id();

	// 										$arrReturn[$key][$rkey]["status"] = "Error";
	// 										$arrReturn[$key][$rkey]["message"] = isset($getCurl["msg"]) ? $getCurl["msg"] : "";
	// 										$arrReturn["Status"] = "Error";
	// 									}
	// 								}
	// 							}
	// 						}
	// 					}
	// 				} else {
	// 					$arrReturn[$key]["GHI"]["status"] = "Error";
	// 					$arrReturn[$key]["GHI"]["message"] = isset($getCurl["message"]) ? $getCurl["message"] : "";
	// 					$arrReturn["Status"] = "Error";
	// 					continue;
	// 				}
	// 			} else {
	// 				$request_arr = [
	// 					"lead_id" => $lead_id,
	// 					"req" => json_encode($fqrequestGhi),
	// 					"product_id" => $product_id_Ghi,
	// 					"type" => "quick_quote",
	// 					"proposal_policy_id" => $proposal_policy_id_Ghi
	// 				];
	// 				$this->db->insert("logs_docs", $request_arr);
	// 				$insert_id = $this->db->insert_id();

	// 				$arrReturn[$key]["GHI"]["status"] = "Error";
	// 				$arrReturn[$key]["GHI"]["message"] = isset($getCurl["message"]) ? $getCurl["message"] : "";
	// 				$arrReturn["Status"] = "Error";
	// 			}
	// 		}
	// 	}
	// 	return $arrReturn;
	// }

	function getGoGreen($lead_id){

		$go_green = $this->getdata('proposal_payment_details', 'go_green', "lead_id = $lead_id");

		if(!empty($go_green)){

			return ($go_green[0]['go_green'] == 'Y') ? 1 : 0;
		}
		else{

			return 0;
		}
	}
	

	/**
	 * 10-05-2021 - Commented as new change request from AB
	 */
	/*function doQuickQuote($lead_id, $proposal_policy_id_full_quote = '')
	{
		//ini_set('display_errors', 1);
		$arr_proposal_policy = $this->getQuickQuoteData($lead_id, $proposal_policy_id_full_quote);
		//print_r($arr_proposal_policy);
		//exit;

		$nominees = $member = $policyData = [];
		if (!empty($arr_proposal_policy)) {
			foreach ($arr_proposal_policy as $key => $row) {
				$data = $this->db->query("select status,proposal_policy_id from quote_response where proposal_policy_id='" . $row['proposal_policy_id'] . "'")->row_array();
				//echo $this->db->last_query();
				if (empty($data['proposal_policy_id']) || $data['status'] != 'Success') {

					$policy_sub_type_id = isset($row['policy_sub_type_id']) ? $row['policy_sub_type_id'] : '';
					$proposal_policy_id = isset($row['proposal_policy_id']) ? $row['proposal_policy_id'] : '';
					$proposal_details_id = isset($row['proposal_details_id']) ? $row['proposal_details_id'] : '';
					$master_policy_id = isset($row['master_policy_id']) ? $row['master_policy_id'] : '';
					$plan_id = isset($row['plan_id']) ? $row['plan_id'] : '';
					$customer_id = isset($row['customer_id']) ? $row['customer_id'] : '';
					$SumInsured = isset($row['sum_insured']) ? $row['sum_insured'] : '';
					$adult_count = isset($row['adult_count']) ? $row['adult_count'] : '';
					$child_count = isset($row['child_count']) ? $row['child_count'] : '';
					$group_code_value = isset($row['group_code']) ? $row['group_code'] : null;
					$policy_number = $this->getPolicyCode($policy_sub_type_id);

					$proposal_policy[$policy_number] = $row;
					$getPolicyMemberDetails = $this->getPolicyMemberDetails($proposal_policy_id, $master_policy_id, $lead_id);
					$SumInsured_Type = $this->getSumInsuredType($master_policy_id);

					if (!empty($child_count)) {
						$FamilyConstruct = $adult_count . "A+" . $child_count . "K";
					} else {
						$FamilyConstruct = $adult_count . "A";
					}

					//$getGroupCode = $this->getGroupCode($plan_id, $SumInsured, $FamilyConstruct);
					$MasterPolicyNumber = $this->getMasterPolicyNumber($master_policy_id);


					$ukey = $proposal_details_id . '__' . $policy_number;
					$api_url = $this->getApiUrl($policy_sub_type_id);
					$policyData['lead_id'] = $lead_id;
					$policyData['customer_id'] = $customer_id;
					$policyData['master_policy_id'] = $master_policy_id;
					$policyData['policy_sub_type_id'] = $policy_sub_type_id;
					$policyData['proposal_policy_id'] = $proposal_policy_id;

					/* Nominees Details*/
					/*if (empty($nominees)) {
						$nominees['nominee_relation'] = $row['nominee_relation'];
						$nominees['nominee_first_name'] = $row['nominee_first_name'];
						$nominees['nominee_last_name'] = $row['nominee_last_name'];
						$nominees['nominee_salutation'] = $row['nominee_salutation'];
						$nominees['nominee_gender'] = $row['nominee_gender'];
						$nominees['nominee_dob'] = $row['nominee_dob'];
						$nominees['nominee_contact'] = $row['nominee_contact'];
						$nominees['nominee_email'] = $row['nominee_email'];
					}

					$member = $this->getMembersForQuote($row, $getPolicyMemberDetails, $nominees);

					$SourceSystemName_api = $this->getSourceName($master_policy_id);
					$uidNo = isset($getPolicyMemberDetails[0]['policy_member_pan']) ? $getPolicyMemberDetails[0]['policy_member_pan'] : null;
					//$GroupID = isset($getGroupCode['group_code']) ? $getGroupCode['group_code'] : '';
					$GroupID = isset($group_code_value) ? $group_code_value : '';
					$plan_code = $this->getPlanCode($master_policy_id);
					$Product_Code = $this->getProductCode($master_policy_id);
					$SchemeCode = $this->getSchemeCode($master_policy_id);
					$branch_imd_code_arr = $this->getIntermediaryCode($master_policy_id);
					$go_green = $this->getGoGreen($lead_id);

					if($branch_imd_code_arr == ''){

						return array(
							"status" => "error",
							"msg" => "Branch and Intermediary Code for policy number is missing"
						);
					}

					$Member_Type_Code = 'M209';
					//$intermediaryCode = '2108233';
					//$intermediaryBranchCode = '10MHMUM01';

					$intermediaryCode = $branch_imd_code_arr->imd_code;
					$intermediaryBranchCode =  $branch_imd_code_arr->branch_code;
					$product_id = isset($row['plan_id']) ? $row['plan_id'] : "";
					$SPID = 0;
					$RefCode1 = 0;
					$RefCode2 = 0;
					$Policy_Tanure = 1;
					$AutoRenewal = 'Y';
					$occupation = "Self Employed";
					$explode_name = array($row['first_name'], $row['middle_name'], $row['last_name']);
					$explode_name_nominee = array($row['nominee_first_name'], $row['nominee_last_name']);

					// if ($api_url == '' || $GroupID == '') {
					if ($api_url == '') {
						return array(
							"status" => "error",
							"msg" => "Error in Service URL or Group code"
						);
					}

					$fqrequest = [
						"ClientCreation" =>
						[
							"Member_Customer_ID" => $row['customer_id'] . $policy_number . $proposal_policy_id,
							"salutation" => $row['salutation'],
							"firstName" => isset($row['first_name']) ? $row['first_name'] : "",
							"middleName" => isset($row['middle_name']) ? $row['middle_name'] : "",
							"lastName" => isset($row['last_name']) ? $row['last_name'] : "",
							"dateofBirth" => date('m/d/Y', strtotime($row['dob'])),
							"gender" => ($row['gender'] == "Male") ? "M" : "F",
							"educationalQualification" => "null",
							"pinCode" => $row['pin_code'],
							"uidNo" => $uidNo,
							"maritalStatus" => null,
							"nationality" => "Indian",
							"occupation" => $occupation,
							"primaryEmailID" => $row['email_id'],
							"contactMobileNo" => substr(trim($row['mobile_no']), -10),
							"stdLandlineNo" => null,
							"panNo" => null,
							"passportNumber" => null,
							"contactPerson" => null,
							"annualIncome" => null,
							"remarks" => null,
							"startDate" => date('Y-m-d'),
							"endDate" => null,
							"IdProof" => "Adhaar Card",
							"residenceProof" => null,
							"ageProof" => null,
							"others" => null,
							"homeAddressLine1" => $row['address_line1'],
							"homeAddressLine2" => $row['address_line2'],
							"homeAddressLine3" => $row['address_line3'],
							"homePinCode" => $row['pin_code'],
							"homeArea" => null,
							"homeContactMobileNo" => substr(trim($row['mobile_no']), -10),
							"homeContactMobileNo2" => null,
							"homeSTDLandlineNo" => null,
							"homeFaxNo" => null,
							"sameAsHomeAddress" => "1",
							"mailingAddressLine1" => null,
							"mailingAddressLine2" => null,
							"mailingAddressLine3" => null,
							"mailingPinCode" => null,
							"mailingArea" => null,
							"mailingContactMobileNo" => null,
							"mailingContactMobileNo2" => null,
							"mailingSTDLandlineNo" => null,
							"mailingSTDLandlineNo2" => null,
							"mailingFaxNo" => null,
							"bankAccountType" => null,
							"bankAccountNo" => null,
							"ifscCode" => null,
							"GSTIN" => null,
							"GSTRegistrationStatus" => "Consumers",
							"IsEIAavailable" => "0",
							"ApplyEIA" => "0",
							"EIAAccountNo" => null,
							"EIAWith" => "0",
							"AccountType" => null,
							"AddressProof" => null,
							"DOBProof" => null,
							"IdentityProof" => null
						],
						"PolicyCreationRequest" => [
							"Quotation_Number" => "",
							"MasterPolicyNumber" => $MasterPolicyNumber,
							"GroupID" => $GroupID,
							"Product_Code" => $Product_Code,
							"intermediaryCode" => $intermediaryCode,
							"AutoRenewal" => $AutoRenewal,
							"intermediaryBranchCode" => $intermediaryBranchCode,
							"agentSignatureDate" => null,
							"Customer_Signature_Date" => null,
							"businessSourceChannel" => null,
							"AssignPolicy" => "0",
							"AssigneeName" => null,
							"leadID" => $lead_id,
							"Source_Name" => $SourceSystemName_api,
							"SPID" => $SPID,
							"TCN" => null,
							"CRTNO" => null,
							"RefCode1" => $RefCode1,
							"RefCode2" => $RefCode2,
							"Employee_Number" => $row['customer_id'],
							"enumIsEmployeeDiscount" => null,
							"QuoteDate" => null,
							"IsPayment" => "0",
							"PaymentMode" => "",
							"PolicyproductComponents" => [
								[
									"PlanCode" => $plan_code,
									"SumInsured" => $SumInsured,
									"SchemeCode" => $SchemeCode
								]
							]
						],
						"MemObj" => ["Member" => $member],
						"ReceiptCreation" => [
							"officeLocation" => "",
							"modeOfEntry" => "",
							"cdAcNo" => null,
							"expiryDate" => null,
							"payerType" => "",
							"payerCode" => null,
							"paymentBy" => "",
							"paymentByName" => null,
							"paymentByRelationship" => null,
							"collectionAmount" => "",
							"collectionRcvdDate" => null,
							"collectionMode" => "",
							"remarks" => null,
							"instrumentNumber" => null,
							"instrumentDate" => null,
							"bankName" => null,
							"branchName" => null,
							"bankLocation" => null,
							"micrNo" => null,
							"chequeType" => null,
							"ifscCode" => "",
							"PaymentGatewayName" => "",
							"TerminalID" => "",
							"CardNo" => null
						]
					];

					if($go_green){

						$fqrequest['PolicyCreationRequest']['goGreen'] = 1;
					}

					if (empty($fqrequest)) {
						$arrReturn[$key][$policy_number]["status"] = "Error";
						$arrReturn[$key][$policy_number]["message"] = "Empty Request";
						$arrReturn["Status"] = "Error";
						continue;
					}

					//print_r($fqrequest);exit;

					$getCurl = $this->getCurl($api_url, $fqrequest);

					if (!empty($getCurl)) {
						$status = isset($getCurl["status"]) ? $getCurl["status"] : '';

						$responseAPI = isset($getCurl["data"]) ? $getCurl["data"] : [];

						$request_arr = [
							"lead_id" => $lead_id,
							"req" => json_encode($fqrequest),
							"res" => json_encode($responseAPI),
							"product_id" => $plan_id,
							"type" => "quick_quote",
							"proposal_policy_id" => $proposal_policy_id
						];

						$this->db->insert("logs_docs", $request_arr);
						$insert_id_ghi = $this->db->insert_id();

						//Application log entries
						$this->db->insert("application_logs", [
							"lead_id" => $lead_id,
							"action" => "quick_quote",
							"request_data" => json_encode($fqrequest),
							"response_data" => json_encode($responseAPI),
							"created_on" => date("Y-m-d H:i:s")
						]);

						if (isset($getCurl["status"]) && $getCurl["status"] == "Success" && !empty($responseAPI["policyDtls"]["QuotationNumber"])) {
							$saveProposal = $this->saveProposalResponse($responseAPI, $policyData, "Success", $insert_id_ghi);
							$arrReturn[$key][$policy_number]["status"] = "Success";
							$arrReturn[$key][$policy_number]["message"] = "";
						} else {
							$saveProposal = $this->saveProposalResponse($responseAPI, $policyData, "Error", $insert_id_ghi);

							$arrReturn[$key][$policy_number]["status"] = "Error";
							$arrReturn[$key][$policy_number]["message"] = isset($getCurl["message"]) ? $getCurl["message"] : "";
							$arrReturn["Status"] = "Error";
							continue;
						}
					} else {
						$request_arr = [
							"lead_id" => $lead_id,
							"req" => json_encode($fqrequest),
							"product_id" => $plan_id,
							"type" => "quick_quote",
							"proposal_policy_id" => $proposal_policy_id
						];
						$this->db->insert("logs_docs", $request_arr);
						$insert_id = $this->db->insert_id();
						//echo $this->db->last_query();
						//exit;

						//Application log entries
						$this->db->insert("application_logs", [
							"lead_id" => $lead_id,
							"action" => "quick_quote",
							"request_data" => json_encode($fqrequest),
							"created_on" => date("Y-m-d H:i:s")
						]);

						$arrReturn[$key][$policy_number]["status"] = "Error";
						$arrReturn[$key][$policy_number]["message"] = isset($getCurl["message"]) ? $getCurl["message"] : "";
						$arrReturn["Status"] = "Error";
					}
				}
			}
		}

		if (empty($proposal_policy_id_full_quote)) {

			$success_check = $this->db->query("select count(lead_id) total_success from quote_response where lead_id='" . $lead_id . "' and status = 'Success'")->row_array();
			$proposal_policy_check = sizeof($arr_proposal_policy);
			$arrReturnCheck["Status"] = (!empty($success_check['total_success']) && $success_check['total_success'] == $proposal_policy_check) ? "Success" : "Fail";
		} else {

			$arrReturnCheck["Status"] = (isset($responseAPI['errorObj']['ErrorNumber']) && $responseAPI['errorObj']['ErrorNumber'] == '00') ? "Success" : "Fail";
			$arrReturnCheck["QuotationNumber"] = (isset($responseAPI['errorObj']['ErrorNumber']) && $responseAPI['errorObj']['ErrorNumber'] == '00') ? $responseAPI['policyDtls']['QuotationNumber'] : "";
		}

		return $arrReturnCheck;
	}*/

	/**
	 * 
	 * New function as per change request - Quick Quote
	 */
	public function doQuickQuote($lead_id, $proposal_policy_id_full_quote = '', $full_quote_customer_id = '', $is_combination = false){

		$arr_proposal_policy = $this->getQuickQuoteData($lead_id, $proposal_policy_id_full_quote);
		
		$nominees = $member = $policyData = [];
		if (!empty($arr_proposal_policy)) {
			
			$master_policy_id_arr = [];
			$count = 0;

			foreach ($arr_proposal_policy as $key => $row) {
				
				$data = $this->db->query("select status,proposal_policy_id from quote_response where proposal_policy_id='" . $row['proposal_policy_id'] . "'")->row_array();

				if (empty($data['proposal_policy_id']) || $data['status'] != 'Success') {

					$api_url = $this->getApiUrl($row['policy_sub_type_id']);
			
					/*if($row['imd_code'] == '' || $row['branch_code'] == ''){
	
						return array(
							"status" => "error",
							"msg" => "Branch or Intermediary Code for policy number is missing"
						);
					}*/

					if ($api_url == '') {
						return array(
							"status" => "error",
							"msg" => "Error in Service URL or Group code"
						);
					}
					
					/* Nominees Details*/
					//if (empty($nominees)) {
						$nominees['nominee_relation'] = $row['nominee_relation'];
						$nominees['nominee_first_name'] = $row['nominee_first_name'];
						$nominees['nominee_last_name'] = $row['nominee_last_name'];
						$nominees['nominee_salutation'] = $row['nominee_salutation'];
						$nominees['nominee_gender'] = $row['nominee_gender'];
						$nominees['nominee_dob'] = $row['nominee_dob'];
						$nominees['nominee_contact'] = $row['nominee_contact'];
						$nominees['nominee_email'] = $row['nominee_email'];
					//}
						

					$fqrequest = $this->createQuickQuoteRequest($lead_id, $row);

					//Check if GHI/Supertopup then request as per Siddhi's code
					if(in_array($row['policy_sub_type_id'], [1,5])){
						
						$getPolicyMemberDetails = $this->getPolicyMemberDetails($row['proposal_policy_id'], $row['master_policy_id'], $lead_id);
						$member = $this->getMembersForQuote($row, $getPolicyMemberDetails, $nominees);

						$fqrequest['PolicyCreationRequest']['PolicyproductComponents'] =  [
							[
								"PlanCode" => $row['plan_code'],
								"SumInsured" => $row['sum_insured'],
								"SchemeCode" => $row['scheme_code']
							]
						];

						$fqrequest["MemObj"] = ["Member" => $member];

						if (empty($fqrequest)) {
							$arrReturn[$key][$row['policy_number']]["status"] = "Error";
							$arrReturn[$key][$row['policy_number']]["message"] = "Empty Request";
							$arrReturn["Status"] = "Error";
							continue;
						}
		
						//print_r($fqrequest);exit;
		
						$getCurl = $this->getCurl($api_url, $fqrequest);
		
						if (!empty($getCurl)) {
							$status = isset($getCurl["status"]) ? $getCurl["status"] : '';
		
							$responseAPI = isset($getCurl["data"]) ? $getCurl["data"] : [];
		
							$request_arr = [
								"lead_id" => $lead_id,
								"req" => json_encode($fqrequest),
								"res" => json_encode($responseAPI),
								"product_id" => $row['plan_id'],
								"type" => "quick_quote",
								"proposal_policy_id" => $row['proposal_policy_id']
							];
		
							$this->db->insert("logs_docs", $request_arr);
							$insert_id_ghi = $this->db->insert_id();
		
							//Application log entries
							$this->db->insert("application_logs", [
								"lead_id" => $lead_id,
								"action" => "quick_quote",
								"request_data" => json_encode($fqrequest),
								"response_data" => json_encode($responseAPI),
								"created_on" => date("Y-m-d H:i:s")
							]);
							
							$policyData['lead_id'] = $lead_id;
							$policyData['customer_id'] = $row['customer_id'];
							$policyData['master_policy_id'] = $row['master_policy_id'];
							$policyData['policy_sub_type_id'] = $row['policy_sub_type_id'];
							$policyData['proposal_policy_id'] = $row['proposal_policy_id'];

							if (isset($getCurl["status"]) && $getCurl["status"] == "Success" && !empty($responseAPI["policyDtls"]["QuotationNumber"])) {
								$saveProposal = $this->saveProposalResponse($responseAPI, $policyData, "Success", $insert_id_ghi);
								$arrReturn[$key][$row['policy_number']]["status"] = "Success";
								$arrReturn[$key][$row['policy_number']]["message"] = "";
								$count++;
							} else {
								$saveProposal = $this->saveProposalResponse($responseAPI, $policyData, "Error", $insert_id_ghi);
		
								$arrReturn[$key][$row['policy_number']]["status"] = "Error";
								$arrReturn[$key][$row['policy_number']]["message"] = isset($getCurl["message"]) ? $getCurl["message"] : "";
								$arrReturn["Status"] = "Error";
								continue;
							}
						} else {
							$request_arr = [
								"lead_id" => $lead_id,
								"req" => json_encode($fqrequest),
								"product_id" => $row['plan_id'],
								"type" => "quick_quote",
								"proposal_policy_id" => $row['proposal_policy_id']
							];
							$this->db->insert("logs_docs", $request_arr);
							$insert_id = $this->db->insert_id();
							//echo $this->db->last_query();
							//exit;
		
							//Application log entries
							$this->db->insert("application_logs", [
								"lead_id" => $lead_id,
								"action" => "quick_quote",
								"request_data" => json_encode($fqrequest),
								"created_on" => date("Y-m-d H:i:s")
							]);
		
							$arrReturn[$key][$row['policy_number']]["status"] = "Error";
							$arrReturn[$key][$row['policy_number']]["message"] = isset($getCurl["message"]) ? $getCurl["message"] : "";
							$arrReturn["Status"] = "Error";
						}
					}
					else{
						
						$master_policy_id_arr[$row['master_policy_id']] = $row['master_policy_id'];

						$arr_proposal_policy_for_combo[$row['customer_id']][$row['policy_number']] = $row;

						$proposal_policy_id_combo[$row['customer_id']][$row['policy_number']][$row['master_policy_id']] = $row['proposal_policy_id'];
						$policy_sub_type_id_combo[$row['customer_id']][$row['policy_number']][$row['master_policy_id']] = $row['policy_sub_type_id'];

						$PolicyproductComponents[$row['customer_id']][$row['policy_number']][$row['plan_code']] = [
							"PlanCode" => $row['plan_code'],
							"SumInsured" => $row['sum_insured'],
							"SchemeCode" => $row['scheme_code']
						];
					}
				}
			}
			
			if(isset($arr_proposal_policy_for_combo)){
				
				$getPolicyMemberDetailsForCombo = $this->getPolicyMemberDetailsForCombo($lead_id, $master_policy_id_arr);
				$member = $plan_codes = [];
				if(!empty($getPolicyMemberDetailsForCombo)){

					foreach ($arr_proposal_policy_for_combo as $customer_id => $policy_number_arr) {

						foreach($policy_number_arr as $policy_number => $row){

							if(isset($PolicyproductComponents[$customer_id][$policy_number])){

								$fqrequest = $this->createQuickQuoteRequest($lead_id, $row);
								$fqrequest['PolicyCreationRequest']['PolicyproductComponents'] = array_values($PolicyproductComponents[$customer_id][$policy_number]);
								
								if(isset($getPolicyMemberDetailsForCombo[$customer_id][$policy_number])){

									foreach($getPolicyMemberDetailsForCombo[$customer_id][$policy_number] as $member_id => $member_arr){
										
										$plan_codes = $getPolicyMemberDetailsForCombo[$customer_id]['plan_code'][$policy_number] ?? [];
										$MemberproductComponents = [];

										foreach($plan_codes as $plan_code){

											$MemberproductComponents[$plan_code] = [
												"PlanCode" => $plan_code,
												"MemberQuestionDetails" => [[
													"QuestionCode" => null,
													"Answer" => null,
													"Remarks" => null
												]]
											];
										}

										$member = $this->getMembersForQuoteCombo($row, [$member_arr], $MemberproductComponents);
										$fqrequest["MemObj"] = ["Member" => $member];

										$api_url = $this->getApiUrl($row['policy_sub_type_id']);

										$getCurl = $this->getCurl($api_url, $fqrequest);

										if (!empty($getCurl)) {
											
											$status = isset($getCurl["status"]) ? $getCurl["status"] : '';
						
											$responseAPI = isset($getCurl["data"]) ? $getCurl["data"] : [];
						
											$request_arr = [
												"lead_id" => $lead_id,
												"req" => json_encode($fqrequest),
												"res" => json_encode($responseAPI),
												"product_id" => $row['plan_id'],
												"type" => "quick_quote",
												"proposal_policy_id" => $row['proposal_policy_id']
											];
						
											$this->db->insert("logs_docs", $request_arr);
											$insert_id_ghi = $this->db->insert_id();
						
											//Application log entries
											$this->db->insert("application_logs", [
												"lead_id" => $lead_id,
												"action" => "quick_quote",
												"request_data" => json_encode($fqrequest),
												"response_data" => json_encode($responseAPI),
												"created_on" => date("Y-m-d H:i:s")
											]);
											
											foreach($master_policy_id_arr as $master_policy_id_key => $master_policy_id_value){

												if(isset($proposal_policy_id_combo[$customer_id][$policy_number][$master_policy_id_key])){
													$policyData['lead_id'] = $lead_id;
													$policyData['customer_id'] = $row['customer_id'];
													$policyData['master_policy_id'] = $master_policy_id_key;
													$policyData['policy_sub_type_id'] = $policy_sub_type_id_combo[$customer_id][$policy_number][$master_policy_id_key];
													$policyData['proposal_policy_id'] = $proposal_policy_id_combo[$customer_id][$policy_number][$master_policy_id_key];
													//$policyData['proposal_policy_id_combo'] = implode(',', $proposal_policy_id_combo);
													//$policyData['policy_sub_type_id_combo'] = implode(',', $policy_sub_type_id_combo);
													
													$policyData['is_combination'] = 'N';
													
													if(count($proposal_policy_id_combo[$customer_id][$policy_number]) > 1){

														$policyData['is_combination'] = 'Y';
													}
										
													if (isset($getCurl["status"]) && $getCurl["status"] == "Success" && !empty($responseAPI["policyDtls"]["QuotationNumber"])) {

														$saveProposal = $this->saveProposalResponse($responseAPI, $policyData, "Success", $insert_id_ghi);
														$arrReturn[$member_id][$row['policy_number']]["status"] = "Success";
														$arrReturn[$member_id][$row['policy_number']]["message"] = "";

														if($row['customer_id'] == $full_quote_customer_id){

															$count++;
															$api_response[$full_quote_customer_id]['QuotationNumber'] = $responseAPI["policyDtls"]["QuotationNumber"];
														}
													} else {
														$saveProposal = $this->saveProposalResponse($responseAPI, $policyData, "Error", $insert_id_ghi);
								
														$arrReturn[$member_id][$row['policy_number']]["status"] = "Error";
														$arrReturn[$member_id][$row['policy_number']]["message"] = isset($getCurl["message"]) ? $getCurl["message"] : "";
														$arrReturn["Status"] = "Error";
														continue;
													}
												}
											}
										} else {
											$request_arr = [
												"lead_id" => $lead_id,
												"req" => json_encode($fqrequest),
												"product_id" => $row['plan_id'],
												"type" => "quick_quote",
												"proposal_policy_id" => $row['proposal_policy_id']
											];
											$this->db->insert("logs_docs", $request_arr);
											$insert_id = $this->db->insert_id();
											//echo $this->db->last_query();
											//exit;
						
											//Application log entries
											$this->db->insert("application_logs", [
												"lead_id" => $lead_id,
												"action" => "quick_quote",
												"request_data" => json_encode($fqrequest),
												"created_on" => date("Y-m-d H:i:s")
											]);
						
											$arrReturn[$member_id][$row['policy_number']]["status"] = "Error";
											$arrReturn[$member_id][$row['policy_number']]["message"] = isset($getCurl["message"]) ? $getCurl["message"] : "";
											$arrReturn["Status"] = "Error";
										}
									}
								}
							}
						}
					}
				}
			}
		}
		
		if (empty($proposal_policy_id_full_quote)) {
			
			$sql = "select count(lead_id) total_success from quote_response where lead_id='" . $lead_id . "' and status = 'Success'";

			if($is_combination || $full_quote_customer_id){

				if($is_combination){

					$sql .= " AND is_combination = 'Y'";
				}

				if($full_quote_customer_id){

					$sql .= " AND cust_id = '$full_quote_customer_id'";
				}
			}
			else{

				$query = "SELECT count(proposal_policy_id) AS total_policies FROM proposal_policy WHERE lead_id = $lead_id";
				$total_policies = $this->db->query($query)->row_array();
				$count = $total_policies['total_policies'];
			}
			
			$success_check = $this->db->query($sql)->row_array();

			$proposal_policy_check = $count;
			$arrReturnCheck["Status"] = (!empty($success_check['total_success']) && $success_check['total_success'] == $proposal_policy_check) ? "Success" : "Fail";
		} else {
	
			$arrReturnCheck["Status"] = (isset($responseAPI['errorObj']['ErrorNumber']) && $responseAPI['errorObj']['ErrorNumber'] == '00') ? "Success" : "Fail";
		}

		if($full_quote_customer_id){

			$arrReturnCheck["QuotationNumber"] = (isset($api_response[$full_quote_customer_id]['QuotationNumber'])) ? $api_response[$full_quote_customer_id]['QuotationNumber'] : "";
		}
		else{
			$arrReturnCheck["QuotationNumber"] = (isset($responseAPI['errorObj']['ErrorNumber']) && $responseAPI['errorObj']['ErrorNumber'] == '00') ? $responseAPI['policyDtls']['QuotationNumber'] : "";
		}
	
		return $arrReturnCheck;
	}

	function createQuickQuoteRequest($lead_id, $row){

		$policy_sub_type_id = isset($row['policy_sub_type_id']) ? $row['policy_sub_type_id'] : '';
		$proposal_policy_id = isset($row['proposal_policy_id']) ? $row['proposal_policy_id'] : '';
		$proposal_details_id = isset($row['proposal_details_id']) ? $row['proposal_details_id'] : '';
		$master_policy_id = isset($row['master_policy_id']) ? $row['master_policy_id'] : '';
		$plan_id = isset($row['plan_id']) ? $row['plan_id'] : '';
		$customer_id = isset($row['customer_id']) ? $row['customer_id'] : '';
		$SumInsured = isset($row['sum_insured']) ? $row['sum_insured'] : '';
		$adult_count = isset($row['adult_count']) ? $row['adult_count'] : '';
		$child_count = isset($row['child_count']) ? $row['child_count'] : '';
		$group_code_value = isset($row['group_code']) ? $row['group_code'] : null;
		$policy_number = $row['code']; //$this->getPolicyCode($policy_sub_type_id);

		$proposal_policy[$policy_number] = $row;
		//$SumInsured_Type = $this->getSumInsuredType($master_policy_id);

		if (!empty($child_count)) {
			$FamilyConstruct = $adult_count . "A+" . $child_count . "K";
		} else {
			$FamilyConstruct = $adult_count . "A";
		}

		//$getGroupCode = $this->getGroupCode($plan_id, $SumInsured, $FamilyConstruct);
		$MasterPolicyNumber = $row['policy_number']; //$this->getMasterPolicyNumber($master_policy_id);

		$ukey = $proposal_details_id . '__' . $policy_number;

		$SourceSystemName_api = $row['source_name']; //$this->getSourceName($master_policy_id);
		$uidNo = $row['pan'] ?? null; //isset($getPolicyMemberDetails[0]['policy_member_pan']) ? $getPolicyMemberDetails[0]['policy_member_pan'] : null;
		//$GroupID = isset($getGroupCode['group_code']) ? $getGroupCode['group_code'] : '';
		$GroupID = isset($group_code_value) ? $group_code_value : '';
		$plan_code = $row['plan_code']; //$this->getPlanCode($master_policy_id);
		$Product_Code = $row['product_code']; //$this->getProductCode($master_policy_id);
		$SchemeCode = $row['scheme_code']; //$this->getSchemeCode($master_policy_id);
		//$branch_imd_code_arr = $this->getIntermediaryCode($master_policy_id);
		$go_green = $row['go_green'] == 'Y' ? 1 : 0; //$this->getGoGreen($lead_id);

		$Member_Type_Code = 'M209';
		//$intermediaryCode = '2108233';
		//$intermediaryBranchCode = '10MHMUM01';

		$intermediaryCode = '';
		$intermediaryBranchCode =  '';
		$product_id = isset($row['plan_id']) ? $row['plan_id'] : "";
		$SPID = 0;
		$RefCode1 = 0;
		$RefCode2 = 0;
		$Policy_Tanure = 1;
		$AutoRenewal = 'Y';
		$occupation = "Self Employed";
		$explode_name = array($row['first_name'], $row['middle_name'], $row['last_name']);
		$explode_name_nominee = array($row['nominee_first_name'], $row['nominee_last_name']);

		$fqrequest = [
			"ClientCreation" =>
			[
				"Member_Customer_ID" => $row['customer_id'] . $policy_number . $proposal_policy_id,
				"salutation" => $row['salutation'],
				"firstName" => isset($row['first_name']) ? $row['first_name'] : "",
				"middleName" => isset($row['middle_name']) ? $row['middle_name'] : "",
				"lastName" => isset($row['last_name']) ? $row['last_name'] : "",
				"dateofBirth" => date('m/d/Y', strtotime($row['dob'])),
				"gender" => ($row['gender'] == "Male") ? "M" : "F",
				"educationalQualification" => "null",
				"pinCode" => $row['pin_code'],
				"uidNo" => $uidNo,
				"maritalStatus" => null,
				"nationality" => "Indian",
				"occupation" => $occupation,
				"primaryEmailID" => $row['customer_email'],
				"contactMobileNo" => substr(trim($row['customer_mobile']), -10),
				"stdLandlineNo" => null,
				"panNo" => $uidNo,
				"passportNumber" => null,
				"contactPerson" => null,
				"annualIncome" => null,
				"remarks" => null,
				"startDate" => date('Y-m-d'),
				"endDate" => null,
				"IdProof" => "Adhaar Card",
				"residenceProof" => null,
				"ageProof" => null,
				"others" => null,
				"homeAddressLine1" => $row['customer_address_line1'],
				"homeAddressLine2" => $row['customer_address_line2'],
				"homeAddressLine3" => $row['customer_address_line3'],
				"homePinCode" => $row['pin_code'],
				"homeArea" => null,
				"homeContactMobileNo" => substr(trim($row['customer_mobile']), -10),
				"homeContactMobileNo2" => null,
				"homeSTDLandlineNo" => null,
				"homeFaxNo" => null,
				"sameAsHomeAddress" => "1",
				"mailingAddressLine1" => null,
				"mailingAddressLine2" => null,
				"mailingAddressLine3" => null,
				"mailingPinCode" => null,
				"mailingArea" => null,
				"mailingContactMobileNo" => null,
				"mailingContactMobileNo2" => null,
				"mailingSTDLandlineNo" => null,
				"mailingSTDLandlineNo2" => null,
				"mailingFaxNo" => null,
				"bankAccountType" => null,
				"bankAccountNo" => null,
				"ifscCode" => null,
				"GSTIN" => null,
				"GSTRegistrationStatus" => "Consumers",
				"IsEIAavailable" => "0",
				"ApplyEIA" => "0",
				"EIAAccountNo" => null,
				"EIAWith" => "0",
				"AccountType" => null,
				"AddressProof" => null,
				"DOBProof" => null,
				"IdentityProof" => null
			],
			"PolicyCreationRequest" => [
				"Quotation_Number" => "",
				"MasterPolicyNumber" => $MasterPolicyNumber,
				"GroupID" => $GroupID,
				"Product_Code" => $Product_Code,
				"intermediaryCode" => $intermediaryCode,
				"AutoRenewal" => $AutoRenewal,
				"intermediaryBranchCode" => $intermediaryBranchCode,
				"agentSignatureDate" => null,
				"Customer_Signature_Date" => null,
				"businessSourceChannel" => null,
				"AssignPolicy" => "0",
				"AssigneeName" => null,
				"leadID" => $lead_id,
				"Source_Name" => $SourceSystemName_api,
				"SPID" => $SPID,
				"TCN" => null,
				"CRTNO" => null,
				"RefCode1" => $RefCode1,
				"RefCode2" => $RefCode2,
				"Employee_Number" => $row['customer_id'],
				"enumIsEmployeeDiscount" => null,
				"QuoteDate" => null,
				"IsPayment" => "0",
				"PaymentMode" => "",
			],
			"ReceiptCreation" => [
				"officeLocation" => "",
				"modeOfEntry" => "",
				"cdAcNo" => null,
				"expiryDate" => null,
				"payerType" => "",
				"payerCode" => null,
				"paymentBy" => "",
				"paymentByName" => null,
				"paymentByRelationship" => null,
				"collectionAmount" => "",
				"collectionRcvdDate" => null,
				"collectionMode" => "",
				"remarks" => null,
				"instrumentNumber" => null,
				"instrumentDate" => null,
				"bankName" => null,
				"branchName" => null,
				"bankLocation" => null,
				"micrNo" => null,
				"chequeType" => null,
				"ifscCode" => "",
				"PaymentGatewayName" => "",
				"TerminalID" => "",
				"CardNo" => null
			]
		];

		if($go_green){

			$fqrequest['PolicyCreationRequest']['goGreen'] = 1;
		}

		return $fqrequest;
	}

	/*
	Author : Amol Koli
	Date : 04th jan, 2021
	***/
	function getMasterPolicyNumber($policy_id)
	{
		$data = $this->db->query("select policy_number from master_policy where policy_id='$policy_id'")->row();
		return $data->policy_number;
	}
	// function getMasterPolicyNumber($policy_id)
	// {
	// 	$data = $this->db->query("select master_policy_code from master_policy where policy_id='$policy_id' ")->row();
	// 	return $data->master_policy_code;
	// }

	/*
	Author : Amol Koli
	Date : 01th jan, 2021
	***/
	// function getFullQuoteData($lead_id)
	// {
	// 	$arr_data = $this->db
	// 		->select('qr.*,ld.*,pd.*,mc.*,
	// 		pp.proposal_policy_id,
	// 		pp.master_policy_id,
	// 		pp.proposal_details_id,
	// 		pp.sum_insured,
	// 		pp.adult_count,
	// 		pp.child_count,
	// 		pp.policy_sub_type_name,
	// 		pp.policy_sub_type_id,
	// 		pp.premium_amount,
	// 		pp.tax_amount,
	// 		mp.policy_number,
	// 		pp.sum_insured,
	// 		mc.pincode as pin_code,
	// 		mc.address_line1 as address_line1,
	// 		mc.address_line2 as address_line2,
	// 		mc.address_line3 as address_line3,
	// 		mc.mobile_no as mobile_no,
	// 		mc.customer_id,
	// 		qr.created_at as quote_date,
	// 		pd.mode_of_payment,
	// 		ppd.transaction_date as instrumentDate,
	// 		ppd.transaction_number as instrumentNumber,
	// 		ppd.payment_mode as ppd_payment_mode,
	// 		ppd.ifsc_code as ppd_ifsc_code,
	// 		ppd.bank_branch as ppd_bank_branch,
	// 		ppd.bank_city as ppd_bank_city,
	// 		ppd.cheque_number as ppd_cheque_number,
	// 		ppd.account_number as ppd_account_number,
	// 		ppd.cheque_date as ppd_cheque_date,
	// 		ppd.bank_name as ppd_bank_name
	// 		')
	// 		->from('quote_response as qr')
	// 		->join('proposal_policy as pp', 'pp.proposal_policy_id=qr.proposal_policy_id')
	// 		->join('master_policy as mp', 'mp.policy_id=qr.master_policy_id')
	// 		->join('proposal_details as pd', 'pd.proposal_details_id=pp.proposal_details_id')
	// 		->join('master_customer as mc', 'mc.customer_id=qr.cust_id')
	// 		->join('lead_details as ld', 'ld.lead_id=qr.lead_id')
	// 		->join('proposal_payment_details as ppd', 'ld.lead_id=ppd.lead_id')
	// 		->where('qr.lead_id', $lead_id)
	// 		->where('qr.status', "Success")
	// 		->get()
	// 		->result_array();
	// 	return $arr_data;
	// }

	function getFullQuoteData($lead_id)
	{
		$arr_proposal_policy = $this->db
			->select('qr.*,ld.*,pd.*,mc.*,
			pp.proposal_policy_id,
			pp.master_policy_id,
			pp.proposal_details_id,
			pp.sum_insured,
			pp.adult_count,
			pp.child_count,
			pp.policy_sub_type_name,
			pp.tax_amount,
			mp.policy_number,
			pp.sum_insured,
			pp.group_code,
			mc.pincode as pin_code,
			mc.address_line1 as address_line1,
			mc.address_line2 as address_line2,
			mc.address_line3 as address_line3,
			mc.mobile_no as mobile_no,
			mc.customer_id,
			pd.mode_of_payment,
			ppd.transaction_date as instrumentDate,
			ppd.transaction_number as instrumentNumber,
			ppd.payment_mode as ppd_payment_mode,
			ppd.ifsc_code as ppd_ifsc_code,
			ppd.bank_branch as ppd_bank_branch,
			ppd.bank_city as ppd_bank_city,
			ppd.cheque_number as ppd_cheque_number,
			ppd.account_number as ppd_account_number,
			ppd.cheque_date as ppd_cheque_date,
			ppd.bank_name as ppd_bank_name,
			ppd.go_green,
			mpst.code,
			mp.source_name,
			mp.plan_code,
			mp.product_code,
			bim.branch_code,
			bim.imd_code,
			mp.scheme_code,
			mp.policy_sub_type_id')
			->from('proposal_policy as pp')
			->join('master_policy as mp', 'mp.policy_id=pp.master_policy_id')
			->join('master_policy_sub_type as mpst', 'mp.policy_sub_type_id=mpst.policy_sub_type_id')
			->join('proposal_details as pd', 'pd.proposal_details_id=pp.proposal_details_id')
			->join('master_customer as mc', 'mc.customer_id=pd.customer_id')
			->join('lead_details as ld', 'ld.lead_id=pd.lead_id')
			->join('proposal_payment_details as ppd', 'ppd.lead_id=ld.lead_id')
			->join('quote_response as qr', 'pp.proposal_policy_id=qr.proposal_policy_id and mp.policy_id=qr.master_policy_id and mc.customer_id=qr.cust_id and ld.lead_id=qr.lead_id', 'left')
			->join('branch_imd_mapping as bim', 'mp.policy_number = bim.policy_number')
			->where('ppd.payment_status', 'Success')
			->where('bim.status', '1')
			->where('pp.lead_id', $lead_id)
			->group_by('qr.id')
			->get()
			->result_array();

		return $arr_proposal_policy;
	}

	/*
	Author : Amol Koli
	Date : 01th jan, 2020
	***/
	// function doFullQuote($lead_id)
	// {
	// 	ini_set('display_errors', 1);
	// 	//$lead_id = $this->input->post('lead_id');
	// 	$arr_data = $this->apimodel->getFullQuoteData($lead_id);
	// 	$nominees = $member = $policyData = $arrReturn = [];

	// 	//print_r($arr_data);
	// 	//exit;

	// 	if (!empty($arr_data)) {
	// 		foreach ($arr_data as $row) {

	// 			$policy_sub_type_id = isset($row['policy_sub_type_id']) ? $row['policy_sub_type_id'] : null;
	// 			$proposal_policy_id = isset($row['proposal_policy_id']) ? $row['proposal_policy_id'] : null;
	// 			$proposal_details_id = isset($row['proposal_details_id']) ? $row['proposal_details_id'] : null;
	// 			$master_policy_id = isset($row['master_policy_id']) ? $row['master_policy_id'] : null;
	// 			$plan_id = isset($row['plan_id']) ? $row['plan_id'] : null;
	// 			$customer_id = isset($row['customer_id']) ? $row['customer_id'] : null;
	// 			$SumInsured = isset($row['sum_insured']) ? $row['sum_insured'] : null;
	// 			$adult_count = isset($row['adult_count']) ? $row['adult_count'] : null;
	// 			$child_count = isset($row['child_count']) ? $row['child_count'] : null;
	// 			$QuotationNumber = isset($row['QuotationNumber']) ? $row['QuotationNumber'] : null;
	// 			$quote_date = isset($row['quote_date']) ? date("Y-m-d", strtotime($row['quote_date'])) : null;
	// 			$PolicyNumber = isset($row['PolicyNumber']) ? $row['PolicyNumber'] : '';
	// 			$mode_of_payment = isset($row['ppd_payment_mode']) ? $row['ppd_payment_mode'] : null;

	// 			$tax_amount = isset($row['tax_amount']) ? $row['tax_amount'] : 0;
	// 			$premium_amount = isset($row['premium_amount']) ? $row['premium_amount'] : 0;
	// 			$transaction_date = isset($row['instrumentDate']) ? $row['instrumentDate'] : null;
	// 			$transaction_number = isset($row['instrumentNumber']) ? $row['instrumentNumber'] : null;

	// 			$cheque_date = isset($row['ppd_cheque_date']) ? $row['ppd_cheque_date'] : null;
	// 			$cheque_number = isset($row['ppd_cheque_number']) ? $row['ppd_cheque_number'] : null;
	// 			$account_number = isset($row['ppd_account_number']) ? $row['ppd_account_number'] : null;
	// 			$bank_city = isset($row['ppd_bank_city']) ? $row['ppd_bank_city'] : null;
	// 			$bank_branch = isset($row['ppd_bank_branch']) ? $row['ppd_bank_branch'] : null;
	// 			$ifsc_code = isset($row['ppd_ifsc_code']) ? $row['ppd_ifsc_code'] : null;
	// 			$bank_name = isset($row['ppd_bank_name']) ? $row['ppd_bank_name'] : null;
	// 			$collectionAmount = ($premium_amount + $tax_amount);

	// 			$policy_number = $this->apimodel->getPolicyCode($policy_sub_type_id);

	// 			$proposal_policy[$policy_number] = $row;
	// 			$getPolicyMemberDetails = $this->apimodel->getPolicyMemberDetails($proposal_policy_id, $master_policy_id, $lead_id);
	// 			$SumInsured_Type = $this->apimodel->getSumInsuredType($master_policy_id);
	// 			$getGroupCode = $this->apimodel->getGroupCode($policy_sub_type_id, (int) $SumInsured, (int) $adult_count, (int) $child_count, $getPolicyMemberDetails);
	// 			$MasterPolicyNumber = $this->apimodel->getMasterPolicyNumber($master_policy_id);

	// 			$ukey = $proposal_details_id . '__' . $policy_number;
	// 			$policyData[$proposal_details_id][$policy_number]['api_url'] = $this->apimodel->getApiUrl($policy_sub_type_id);
	// 			$policyData[$proposal_details_id][$policy_number]['proposal_policy_id'] = $proposal_policy_id;
	// 			$policyData[$proposal_details_id][$policy_number]['plan_id'] = $plan_id;
	// 			$policyData[$proposal_details_id][$policy_number]['policy_sub_type_id'] = $policy_sub_type_id;
	// 			$policyData[$proposal_details_id][$policy_number]['lead_id'] = $lead_id;
	// 			$policyData[$proposal_details_id][$policy_number]['customer_id'] = $customer_id;
	// 			$policyData[$proposal_details_id][$policy_number]['master_policy_id'] = $master_policy_id;

	// 			$arr_proposal_policy_id[$proposal_details_id][$policy_number] = $ukey;

	// 			/* Nominees Details*/
	// 			if (empty($nominees)) {
	// 				$nominees['nominee_relation'] = $row['nominee_relation'];
	// 				$nominees['nominee_first_name'] = $row['nominee_first_name'];
	// 				$nominees['nominee_last_name'] = $row['nominee_last_name'];
	// 				$nominees['nominee_salutation'] = $row['nominee_salutation'];
	// 				$nominees['nominee_gender'] = $row['nominee_gender'];
	// 				$nominees['nominee_dob'] = $row['nominee_dob'];
	// 				$nominees['nominee_contact'] = $row['nominee_contact'];
	// 				$nominees['nominee_email'] = $row['nominee_email'];
	// 			}

	// 			$member = $this->apimodel->getMembersForQuote($row, $getPolicyMemberDetails, $nominees);

	// 			$SourceSystemName_api = $this->apimodel->getSourceName($master_policy_id);

	// 			$uidNo = isset($getPolicyMemberDetails[0]['policy_member_pan']) ? $getPolicyMemberDetails[0]['policy_member_pan'] : null;
	// 			$GroupID = isset($getGroupCode['groupCode']) ? $getGroupCode['groupCode'] : '';
	// 			$plan_code = $this->apimodel->getPlanCode($master_policy_id);
	// 			$Product_Code = $this->apimodel->getProductCode($master_policy_id);
	// 			$SchemeCode = $this->apimodel->getSchemeCode($master_policy_id);
	// 			$intermediaryCode = $this->apimodel->getIntermediaryCode($master_policy_id);
	// 			$Member_Type_Code = 'M209';
	// 			//$intermediaryCode = '2108233';
	// 			$intermediaryBranchCode = '10MHMUM01';
	// 			$product_id = isset($row['plan_id']) ? $row['plan_id'] : "";
	// 			$SPID = 0;
	// 			$RefCode1 = 0;
	// 			$RefCode2 = 0;
	// 			$terminal_id = "EuxJCz8cZV9V63";
	// 			$Policy_Tanure = 1;
	// 			$AutoRenewal = 'Y';
	// 			$occupation = "O553";
	// 			$explode_name = array($row['first_name'], $row['middle_name'], $row['last_name']);
	// 			$explode_name_nominee = array($row['nominee_first_name'], $row['nominee_last_name']);


	// 			$fqrequest[$proposal_details_id][$policy_number] = [
	// 				"ClientCreation" => [
	// 					"Member_Customer_ID" => time() . "-" . $row['customer_id'],
	// 					"salutation" => $row['salutation'],
	// 					"firstName" => isset($row['first_name']) ? $row['first_name'] : "",
	// 					"middleName" => isset($row['middle_name']) ? $row['middle_name'] : "",
	// 					"lastName" => isset($row['last_name']) ? $row['last_name'] : "",
	// 					"dateofBirth" => date('m/d/Y', strtotime($row['dob'])),
	// 					"gender" => ($row['gender'] == "Male") ? "M" : "F",
	// 					"educationalQualification" => null,
	// 					"pinCode" => $row['pin_code'],
	// 					"uidNo" => $uidNo,
	// 					"maritalStatus" => null,
	// 					"nationality" => "Indian",
	// 					"occupation" => "O553",
	// 					"primaryEmailID" => $row['email_id'],
	// 					"contactMobileNo" =>  substr(trim($row['mobile_no']), -10),
	// 					"stdLandlineNo" => null,
	// 					"panNo" => null,
	// 					"passportNumber" => null,
	// 					"contactPerson" => null,
	// 					"annualIncome" => null,
	// 					"remarks" => null,
	// 					"startDate" => date('Y-m-d'),
	// 					"endDate" => null,
	// 					"IdProof" => "AdhaarCard",
	// 					"residenceProof" => null,
	// 					"ageProof" => null,
	// 					"others" => null,
	// 					"homeAddressLine1" => $row['address_line1'],
	// 					"homeAddressLine2" =>  $row['address_line2'],
	// 					"homeAddressLine3" =>  $row['address_line3'],
	// 					"homePinCode" => $row['pin_code'],
	// 					"homeArea" => null,
	// 					"homeContactMobileNo" => substr(trim($row['mobile_no']), -10),
	// 					"homeContactMobileNo2" => null,
	// 					"homeSTDLandlineNo" => null,
	// 					"homeFaxNo" => null,
	// 					"sameAsHomeAddress" => "1",
	// 					"mailingAddressLine1" => null,
	// 					"mailingAddressLine2" => null,
	// 					"mailingAddressLine3" => null,
	// 					"mailingPinCode" => null,
	// 					"mailingArea" => null,
	// 					"mailingContactMobileNo" => null,
	// 					"mailingContactMobileNo2" => null,
	// 					"mailingSTDLandlineNo" => null,
	// 					"mailingSTDLandlineNo2" => null,
	// 					"mailingFaxNo" => null,
	// 					"bankAccountType" => null,
	// 					"bankAccountNo" => null,
	// 					"ifscCode" => null,
	// 					"GSTIN" => null,
	// 					"GSTRegistrationStatus" => "Consumers",
	// 					"IsEIAavailable" => "0",
	// 					"ApplyEIA" => "0",
	// 					"EIAAccountNo" => null,
	// 					"EIAWith" => "0",
	// 					"AccountType" => null,
	// 					"AddressProof" => null,
	// 					"DOBProof" => null,
	// 					"IdentityProof" => null
	// 				],
	// 				"PolicyCreationRequest" => [
	// 					"Quotation_Number" => $QuotationNumber,
	// 					"MasterPolicyNumber" => $MasterPolicyNumber,
	// 					"GroupID" => $GroupID,
	// 					"Product_Code" => $Product_Code,
	// 					"SumInsured_Type" => $SumInsured_Type,
	// 					"Policy_Tanure" => $Policy_Tanure,
	// 					"Member_Type_Code" => $Member_Type_Code,
	// 					"intermediaryCode" => $intermediaryCode,
	// 					"AutoRenewal" => $AutoRenewal,
	// 					"intermediaryBranchCode" => $intermediaryBranchCode,
	// 					"agentSignatureDate" => null,
	// 					"Customer_Signature_Date" => null,
	// 					"businessSourceChannel" => null,
	// 					"AssignPolicy" => "0",
	// 					"AssigneeName" => null,
	// 					"leadID" => $lead_id,
	// 					"Source_Name" => $SourceSystemName_api,
	// 					"SPID" => $SPID,
	// 					"TCN" => null,
	// 					"CRTNO" => null,
	// 					"RefCode1" => $RefCode1,
	// 					"RefCode2" => $RefCode2,
	// 					"Employee_Number" => $row['customer_id'],
	// 					"enumIsEmployeeDiscount" => null,
	// 					"QuoteDate" => null,
	// 					"IsPayment" => "1",
	// 					"PaymentMode" => null,
	// 					"PolicyproductComponents" => [
	// 						[
	// 							"PlanCode" => $plan_code,
	// 							"SumInsured" => $SumInsured,
	// 							"SchemeCode" => $SchemeCode
	// 						]
	// 					]
	// 				],
	// 				"MemObj" => ["Member" => $member],
	// 				"ReceiptCreation" => [
	// 					"officeLocation" => "Mumbai",
	// 					"modeOfEntry" => "Direct",
	// 					"cdAcNo" => null,
	// 					"expiryDate" => null,
	// 					"payerType" => "Customer",
	// 					"payerCode" => null,
	// 					"paymentBy" => "Customer",
	// 					"paymentByName" => null,
	// 					"paymentByRelationship" => null,
	// 					"collectionAmount" => round($collectionAmount),
	// 					"collectionRcvdDate" => date('Y-m-d', strtotime($transaction_date)),
	// 					"collectionMode" => $mode_of_payment,
	// 					"remarks" => null,
	// 					"instrumentNumber" => $transaction_number,
	// 					"instrumentDate" => date('Y-m-d', strtotime($transaction_date)),
	// 					"bankName" => $bank_name,
	// 					"branchName" => $bank_branch,
	// 					"bankLocation" => $bank_city,
	// 					"micrNo" => null,
	// 					"chequeType" => null,
	// 					"ifscCode" => null,
	// 					"PaymentGatewayName" => $SourceSystemName_api,
	// 					"TerminalID" => $terminal_id,
	// 					"CardNo" => null
	// 				]
	// 			];
	// 		}

	// 		$arrReturn["Status"] = "Success";
	// 		if (!empty($arr_proposal_policy_id)) {
	// 			foreach ($arr_proposal_policy_id as $key => $row) {
	// 				$fqrequestGhi = isset($fqrequest[$key]['GHI']) ? $fqrequest[$key]['GHI'] : [];
	// 				$urlGhi = isset($policyData[$key]['GHI']['api_url']) ? $policyData[$key]['GHI']['api_url'] : '';
	// 				$product_id_Ghi = isset($policyData[$key]['GHI']['plan_id']) ? $policyData[$key]['GHI']['plan_id'] : '';
	// 				$proposal_policy_id_Ghi = isset($policyData[$key]['GHI']['proposal_policy_id']) ? $policyData[$key]['GHI']['proposal_policy_id'] : '';

	// 				//print_r(json_encode($fqrequestGhi));
	// 				//exit;

	// 				$getCurl = $this->apimodel->getCurl($urlGhi, $fqrequestGhi);

	// 				if (empty($fqrequestGhi)) {
	// 					$arrReturn[$key]["GHI"]["status"] = "Error";
	// 					$arrReturn[$key]["GHI"]["message"] = "Empty Request";
	// 					$arrReturn["Status"] = "Error";
	// 					continue;
	// 				}
	// 				if (!empty($getCurl)) {
	// 					$status = isset($getCurl["status"]) ? $getCurl["status"] : '';
	// 					if (isset($getCurl["status"]) && $getCurl["status"] == "Success") {
	// 						$responseGhi = isset($getCurl["data"]) ? $getCurl["data"] : [];

	// 						$request_arr = [
	// 							"lead_id" => $lead_id,
	// 							"req" => json_encode($fqrequestGhi),
	// 							"res" => json_encode($responseGhi),
	// 							"product_id" => $product_id_Ghi,
	// 							"type" => "full_quote",
	// 							"proposal_policy_id" => $proposal_policy_id_Ghi
	// 						];
	// 						$this->db->insert("logs_docs", $request_arr);
	// 						$insert_id_ghi = $this->db->insert_id();

	// 						$statusGhi = isset($responseGhi["errorObj"]["ErrorMessage"]) ? $responseGhi["errorObj"]["ErrorMessage"] : "";
	// 						if ($statusGhi == "Success") {
	// 							$saveProposal = $this->apimodel->saveApiProposalResponse($responseGhi, $policyData, 'GHI', $key, $statusGhi, $insert_id_ghi);
	// 							$arrReturn[$key]["GHI"]["status"] = $statusGhi;
	// 							$arrReturn[$key]["GHI"]["message"] = $statusGhi;
	// 							if ($saveProposal) {
	// 								if (isset($fqrequest[$key]) && !empty($fqrequest[$key])) {
	// 									foreach ($fqrequest[$key] as $rkey => $request) {
	// 										if ($rkey == "GHI") {
	// 											continue;
	// 										}

	// 										$product_id = isset($policyData[$key][$rkey]['plan_id']) ? $policyData[$key][$rkey]['plan_id'] : '';
	// 										$proposal_policy_id = isset($policyData[$key][$rkey]['proposal_policy_id']) ? $policyData[$key][$rkey]['proposal_policy_id'] : '';
	// 										$request = isset($fqrequest[$key][$rkey]) ? $fqrequest[$key][$rkey] : [];
	// 										$url = isset($policyData[$key][$rkey]['api_url']) ? $policyData[$key][$rkey]['api_url'] : [];

	// 										if (empty($request)) {
	// 											$arrReturn[$key][$rkey]["status"] = "Error";
	// 											$arrReturn[$key][$rkey]["message"] = "Empty Request";
	// 											$arrReturn["Status"] = "Error";
	// 											continue;
	// 										}

	// 										$getCurl = $this->apimodel->getCurl($url, $request);
	// 										if (!empty($getCurl)) {
	// 											$status = isset($getCurl["status"]) ? $getCurl["status"] : '';
	// 											//if(isset($getCurl["status"]) && $getCurl["status"] == "Success"){
	// 											$response = isset($getCurl["data"]) ? $getCurl["data"] : [];
	// 											//}

	// 											$message = isset($response["errorObj"]["ErrorMessage"]) ? $response["errorObj"]["ErrorMessage"] : "";
	// 											if ($status != "Success") {
	// 												$statusMessage = $status . '-' . $message;
	// 											} else {
	// 												$statusMessage = $status;
	// 											}
	// 											$request_arr = [];
	// 											$request_arr = [
	// 												"lead_id" => $lead_id,
	// 												"req" => json_encode($request),
	// 												"res" => json_encode($response),
	// 												"product_id" => $product_id,
	// 												"type" => "quick_quote",
	// 												"proposal_policy_id" => $proposal_policy_id
	// 											];
	// 											$this->db->insert("logs_docs", $request_arr);
	// 											$insert_id = $this->db->insert_id();

	// 											$arrReturn[$key][$rkey]["status"] = $status;
	// 											$arrReturn[$key][$rkey]["message"] = $message;

	// 											$saveApiProposalResponse = $this->apimodel->saveApiProposalResponse($response, $policyData, $rkey,  $key, $statusMessage, $insert_id);
	// 										} else {
	// 											$request_arr = [
	// 												"lead_id" => $lead_id,
	// 												"req" => json_encode($fqrequest),
	// 												"product_id" => $product_id,
	// 												"type" => "quick_quote",
	// 												"proposal_policy_id" => $proposal_policy_id
	// 											];
	// 											$this->db->insert("logs_docs", $request_arr);
	// 											$insert_id = $this->db->insert_id();

	// 											$arrReturn[$key][$rkey]["status"] = "Error";
	// 											$arrReturn[$key][$rkey]["message"] = isset($getCurl["msg"]) ? $getCurl["msg"] : "";
	// 											$arrReturn["Status"] = "Error";
	// 										}
	// 									}
	// 								}
	// 							}
	// 						}
	// 					} else {
	// 						$arrReturn[$key]["GHI"]["status"] = "Error";
	// 						$arrReturn[$key]["GHI"]["message"] = isset($getCurl["message"]) ? $getCurl["message"] : "";
	// 						$arrReturn["Status"] = "Error";
	// 						continue;
	// 					}
	// 				} else {
	// 					$request_arr = [
	// 						"lead_id" => $lead_id,
	// 						"req" => json_encode($fqrequestGhi),
	// 						"product_id" => $product_id_Ghi,
	// 						"type" => "quick_quote",
	// 						"proposal_policy_id" => $proposal_policy_id_Ghi
	// 					];
	// 					$this->db->insert("logs_docs", $request_arr);
	// 					$insert_id = $this->db->insert_id();

	// 					$arrReturn[$key]["GHI"]["status"] = "Error";
	// 					$arrReturn[$key]["GHI"]["message"] = isset($getCurl["msg"]) ? $getCurl["msg"] : "";
	// 					$arrReturn["Status"] = "Error";
	// 				}
	// 			}
	// 		}
	// 	}
	// 	return $arrReturn;
	// }
	
	/**
	 * 10-05-2021 - Commented as new change request from AB
	 */
	/*function doFullQuote($lead_id)
	{
		ini_set('display_errors', 1);
		//$lead_id = $this->input->post('lead_id');
		$arr_data = $this->getFullQuoteData($lead_id);
		$go_green = $this->getGoGreen($lead_id);
		$nominees = $member = $policyData = $arrReturn = [];

		if (!empty($arr_data)) {
			$countQuery = "update proposal_payment_details set issuance_count=(issuance_count+1) where lead_id=$lead_id";
			$this->db->query($countQuery);
			foreach ($arr_data as $key => $row) {

				$updateProposalPaymentStatus = $this->updateProposalPaymentDetail(["proposal_status" => "PaymentReceived"], ['lead_id' => $lead_id]);

				$data = $this->db->query("select status,proposal_policy_id from api_proposal_response where proposal_policy_id='" . $row['proposal_policy_id'] . "'")->row_array();
				//echo $this->db->last_query();exit;
				if (empty($data['proposal_policy_id']) || $data['status'] != 'Success') {

					$QuotationNumber = isset($row['QuotationNumber']) ? $row['QuotationNumber'] : null;
					$proposal_policy_id = isset($row['proposal_policy_id']) ? $row['proposal_policy_id'] : null;
					$group_code_value = isset($row['group_code']) ? $row['group_code'] : null;

					if (empty($QuotationNumber)) {
						$quote_data = $this->doQuickQuote($lead_id, $proposal_policy_id);
						if ($quote_data['Status'] == 'Success') {
							$QuotationNumber = $quote_data['QuotationNumber'];
						} else {
							continue;
						}
					}

					$policy_sub_type_id = isset($row['policy_sub_type_id']) ? $row['policy_sub_type_id'] : null;
					$proposal_details_id = isset($row['proposal_details_id']) ? $row['proposal_details_id'] : null;
					$master_policy_id = isset($row['master_policy_id']) ? $row['master_policy_id'] : null;
					$plan_id = isset($row['plan_id']) ? $row['plan_id'] : null;
					$customer_id = isset($row['customer_id']) ? $row['customer_id'] : null;
					$SumInsured = isset($row['sum_insured']) ? $row['sum_insured'] : null;
					$adult_count = isset($row['adult_count']) ? $row['adult_count'] : null;
					$child_count = isset($row['child_count']) ? $row['child_count'] : null;
					//$quote_date = isset($row['quote_date']) ? date("Y-m-d", strtotime($row['quote_date'])) : null;
					$PolicyNumber = isset($row['PolicyNumber']) ? $row['PolicyNumber'] : '';
					$mode_of_payment = isset($row['ppd_payment_mode']) ? $row['ppd_payment_mode'] : null;

					$tax_amount = isset($row['tax_amount']) ? $row['tax_amount'] : 0;
					$premium_amount = isset($row['premium_amount']) ? $row['premium_amount'] : 0;
					$transaction_date = isset($row['instrumentDate']) ? $row['instrumentDate'] : null;
					$transaction_number = isset($row['instrumentNumber']) ? $row['instrumentNumber'] : null;

					$cheque_date = isset($row['ppd_cheque_date']) ? $row['ppd_cheque_date'] : null;
					$cheque_number = isset($row['ppd_cheque_number']) ? $row['ppd_cheque_number'] : null;
					$account_number = isset($row['ppd_account_number']) ? $row['ppd_account_number'] : null;
					$bank_city = isset($row['ppd_bank_city']) ? $row['ppd_bank_city'] : null;
					$bank_branch = isset($row['ppd_bank_branch']) ? $row['ppd_bank_branch'] : null;
					$ifsc_code = isset($row['ppd_ifsc_code']) ? $row['ppd_ifsc_code'] : null;
					$bank_name = isset($row['ppd_bank_name']) ? $row['ppd_bank_name'] : null;
					$collectionAmount = ($premium_amount + $tax_amount);

					$policy_number = $this->getPolicyCode($policy_sub_type_id);

					$proposal_policy[$policy_number] = $row;
					$getPolicyMemberDetails = $this->getPolicyMemberDetails($proposal_policy_id, $master_policy_id, $lead_id);
					$SumInsured_Type = $this->getSumInsuredType($master_policy_id);

					if (!empty($child_count)) {
						$FamilyConstruct = $adult_count . "A+" . $child_count . "K";
					} else {
						$FamilyConstruct = $adult_count . "A";
					}

					//$getGroupCode = $this->getGroupCode($plan_id, $SumInsured, $FamilyConstruct);
					$MasterPolicyNumber = $this->getMasterPolicyNumber($master_policy_id);

					$ukey = $proposal_details_id . '__' . $policy_number;
					$api_url = $this->getApiUrl($policy_sub_type_id);
					$policyData['lead_id'] = $lead_id;
					$policyData['customer_id'] = $customer_id;
					$policyData['master_policy_id'] = $master_policy_id;
					$policyData['policy_sub_type_id'] = $policy_sub_type_id;
					$policyData['proposal_policy_id'] = $proposal_policy_id;
					$policyData['plan_id'] = $plan_id;

					/* Nominees Details*/
					/*if (empty($nominees)) {
						$nominees['nominee_relation'] = $row['nominee_relation'];
						$nominees['nominee_first_name'] = $row['nominee_first_name'];
						$nominees['nominee_last_name'] = $row['nominee_last_name'];
						$nominees['nominee_salutation'] = $row['nominee_salutation'];
						$nominees['nominee_gender'] = $row['nominee_gender'];
						$nominees['nominee_dob'] = $row['nominee_dob'];
						$nominees['nominee_contact'] = $row['nominee_contact'];
						$nominees['nominee_email'] = $row['nominee_email'];
					}

					$member = $this->getMembersForQuote($row, $getPolicyMemberDetails, $nominees);

					$SourceSystemName_api = $this->getSourceName($master_policy_id);

					$uidNo = isset($getPolicyMemberDetails[0]['policy_member_pan']) ? $getPolicyMemberDetails[0]['policy_member_pan'] : null;
					//$GroupID = isset($getGroupCode['group_code']) ? $getGroupCode['group_code'] : '';
					$GroupID = isset($group_code_value) ? $group_code_value : '';
					$plan_code = $this->getPlanCode($master_policy_id);
					$Product_Code = $this->getProductCode($master_policy_id);
					$SchemeCode = $this->getSchemeCode($master_policy_id);
					//$intermediaryCode = $this->getIntermediaryCode($master_policy_id);
					$Member_Type_Code = 'M209';
					//$intermediaryCode = '2108233';
					//$intermediaryBranchCode = '10MHMUM01';
					$branch_imd_code_arr = $this->getIntermediaryCode($master_policy_id);

					if($branch_imd_code_arr == ''){

						return array(
							"status" => "error",
							"msg" => "Branch and Intermediary Code for policy number is missing"
						);
					}

					$Member_Type_Code = 'M209';
					//$intermediaryCode = '2108233';
					//$intermediaryBranchCode = '10MHMUM01';

					$intermediaryCode = $branch_imd_code_arr->imd_code;
					$intermediaryBranchCode =  $branch_imd_code_arr->branch_code;
					$product_id = isset($row['plan_id']) ? $row['plan_id'] : "";
					$SPID = 0;
					$RefCode1 = 0;
					$RefCode2 = 0;
					$terminal_id = ($mode_of_payment == 1) ? "EuxJCz8cZV9V63" : "";
					$Policy_Tanure = 1;
					$AutoRenewal = 'Y';
					$occupation = "O553";
					$explode_name = array($row['first_name'], $row['middle_name'], $row['last_name']);
					$explode_name_nominee = array($row['nominee_first_name'], $row['nominee_last_name']);


					if (empty($transaction_date) || empty($transaction_number) || empty($GroupID)) {
						continue;
					}

					if ($api_url == '') {
						return array(
							"status" => "error",
							"msg" => "Error in Service URL"
						);
					}

					$fqrequest = [
						"ClientCreation" => [
							"Member_Customer_ID" => $row['customer_id'] . $policy_number . $proposal_policy_id,
							"salutation" => $row['salutation'],
							"firstName" => isset($row['first_name']) ? $row['first_name'] : "",
							"middleName" => isset($row['middle_name']) ? $row['middle_name'] : "",
							"lastName" => isset($row['last_name']) ? $row['last_name'] : "",
							"dateofBirth" => date('m/d/Y', strtotime($row['dob'])),
							"gender" => ($row['gender'] == "Male") ? "M" : "F",
							"educationalQualification" => null,
							"pinCode" => $row['pin_code'],
							"uidNo" => $uidNo,
							"maritalStatus" => null,
							"nationality" => "Indian",
							"occupation" => "O553",
							"primaryEmailID" => $row['email_id'],
							"contactMobileNo" =>  substr(trim($row['mobile_no']), -10),
							"stdLandlineNo" => null,
							"panNo" => null,
							"passportNumber" => null,
							"contactPerson" => null,
							"annualIncome" => null,
							"remarks" => null,
							"startDate" => date('Y-m-d'),
							"endDate" => null,
							"IdProof" => "AdhaarCard",
							"residenceProof" => null,
							"ageProof" => null,
							"others" => null,
							"homeAddressLine1" => $row['address_line1'],
							"homeAddressLine2" =>  $row['address_line2'],
							"homeAddressLine3" =>  $row['address_line3'],
							"homePinCode" => $row['pin_code'],
							"homeArea" => null,
							"homeContactMobileNo" => substr(trim($row['mobile_no']), -10),
							"homeContactMobileNo2" => null,
							"homeSTDLandlineNo" => null,
							"homeFaxNo" => null,
							"sameAsHomeAddress" => "1",
							"mailingAddressLine1" => null,
							"mailingAddressLine2" => null,
							"mailingAddressLine3" => null,
							"mailingPinCode" => null,
							"mailingArea" => null,
							"mailingContactMobileNo" => null,
							"mailingContactMobileNo2" => null,
							"mailingSTDLandlineNo" => null,
							"mailingSTDLandlineNo2" => null,
							"mailingFaxNo" => null,
							"bankAccountType" => null,
							"bankAccountNo" => null,
							"ifscCode" => null,
							"GSTIN" => null,
							"GSTRegistrationStatus" => "Consumers",
							"IsEIAavailable" => "0",
							"ApplyEIA" => "0",
							"EIAAccountNo" => null,
							"EIAWith" => "0",
							"AccountType" => null,
							"AddressProof" => null,
							"DOBProof" => null,
							"IdentityProof" => null
						],
						"PolicyCreationRequest" => [
							"Quotation_Number" => $QuotationNumber,
							"MasterPolicyNumber" => $MasterPolicyNumber,
							"GroupID" => $GroupID,
							"Product_Code" => $Product_Code,
							"SumInsured_Type" => $SumInsured_Type,
							"Policy_Tanure" => $Policy_Tanure,
							"Member_Type_Code" => $Member_Type_Code,
							"intermediaryCode" => $intermediaryCode,
							"AutoRenewal" => $AutoRenewal,
							"intermediaryBranchCode" => $intermediaryBranchCode,
							"agentSignatureDate" => null,
							"Customer_Signature_Date" => null,
							"businessSourceChannel" => null,
							"AssignPolicy" => "0",
							"AssigneeName" => null,
							"leadID" => $lead_id,
							"Source_Name" => $SourceSystemName_api,
							"SPID" => $SPID,
							"TCN" => null,
							"CRTNO" => null,
							"RefCode1" => $RefCode1,
							"RefCode2" => $RefCode2,
							"Employee_Number" => $row['customer_id'],
							"enumIsEmployeeDiscount" => null,
							"QuoteDate" => null,
							"IsPayment" => "1",
							"PaymentMode" => null,
							"PolicyproductComponents" => [
								[
									"PlanCode" => $plan_code,
									"SumInsured" => $SumInsured,
									"SchemeCode" => $SchemeCode
								]
							]
						],
						"MemObj" => ["Member" => $member],
						"ReceiptCreation" => [
							"officeLocation" => "Mumbai",
							"modeOfEntry" => "Direct",
							"cdAcNo" => null,
							"expiryDate" => null,
							"payerType" => "Customer",
							"payerCode" => null,
							"paymentBy" => "Customer",
							"paymentByName" => null,
							"paymentByRelationship" => null,
							// "collectionAmount" => round($collectionAmount),
							// TODO: change this amount later dynamically
							"collectionAmount" => 25000,
							"collectionRcvdDate" => date('Y-m-d', strtotime($transaction_date)),
							"collectionMode" => $mode_of_payment,
							"remarks" => null,
							"instrumentNumber" => $transaction_number,
							"instrumentDate" => date('Y-m-d', strtotime($transaction_date)),
							"bankName" => $bank_name,
							"branchName" => $bank_branch,
							"bankLocation" => $bank_city,
							"micrNo" => null,
							"chequeType" => null,
							"ifscCode" => null,
							"PaymentGatewayName" => $SourceSystemName_api,
							"TerminalID" => $terminal_id,
							"CardNo" => null
						]
					];

					if($go_green){

						$fqrequest['PolicyCreationRequest']['goGreen'] = 1;
					}

					if (empty($fqrequest)) {
						$arrReturn[$key][$policy_number]["status"] = "Error";
						$arrReturn[$key][$policy_number]["message"] = "Empty Request";
						$arrReturn["Status"] = "Error";
						continue;
					}

					$getCurl = $this->getCurl($api_url, $fqrequest);

					if (!empty($getCurl)) {
						$status = isset($getCurl["status"]) ? $getCurl["status"] : '';

						$responseAPI = isset($getCurl["data"]) ? $getCurl["data"] : [];

						$request_arr = [
							"lead_id" => $lead_id,
							"req" => json_encode($fqrequest),
							"res" => json_encode($responseAPI),
							"product_id" => $plan_id,
							"type" => "full_quote",
							"proposal_policy_id" => $proposal_policy_id
						];

						$this->db->insert("logs_docs", $request_arr);
						$insert_id_ghi = $this->db->insert_id();

						//Application log entries
						$this->db->insert("application_logs", [
							"lead_id" => $lead_id,
							"action" => "full_quote",
							"request_data" => json_encode($fqrequest),
							"response_data" => json_encode($responseAPI),
							"created_on" => date("Y-m-d H:i:s")
						]);

						if (isset($getCurl["status"]) && $getCurl["status"] == "Success") {
							$saveProposal = $this->saveApiProposalResponse($responseAPI, $policyData, "Success", $insert_id_ghi);
							$arrReturn[$key][$policy_number]["status"] = "Success";
							$arrReturn[$key][$policy_number]["message"] = "";
						} else {
							$saveProposal = $this->saveApiProposalResponse($responseAPI, $policyData, "Error", $insert_id_ghi);

							$arrReturn[$key][$policy_number]["status"] = "Error";
							$arrReturn[$key][$policy_number]["message"] = isset($getCurl["message"]) ? $getCurl["message"] : "";
							$arrReturn["Status"] = "Error";
							continue;
						}
					} else {
						$request_arr = [
							"lead_id" => $lead_id,
							"req" => json_encode($fqrequest),
							"product_id" => $plan_id,
							"type" => "full_quote",
							"proposal_policy_id" => $proposal_policy_id
						];
						$this->db->insert("logs_docs", $request_arr);
						$insert_id = $this->db->insert_id();
						//echo $this->db->last_query();
						//exit;

						//Application log entries
						$this->db->insert("application_logs", [
							"lead_id" => $lead_id,
							"action" => "full_quote",
							"request_data" => json_encode($fqrequest),
							"created_on" => date("Y-m-d H:i:s")
						]);

						$arrReturn[$key][$policy_number]["status"] = "Error";
						$arrReturn[$key][$policy_number]["message"] = isset($getCurl["message"]) ? $getCurl["message"] : "";
						$arrReturn["Status"] = "Error";
					}
				}
			}
		}

		$success_check = $this->db->query("select count(lead_id) total_success from api_proposal_response where lead_id='" . $lead_id . "' and status = 'Success'")->row_array();

		$proposal_policy_check = sizeof($arr_data);

		$arrReturnCheck["Status"] = ($success_check['total_success'] == $proposal_policy_check) ? "Success" : "Fail";

		if ($arrReturnCheck["Status"] == 'Success') {
			$updateProposalPaymentStatus = $this->updateProposalPaymentDetail(["proposal_status" => "Success"], ['lead_id' => $lead_id]);
		}

		return $arrReturnCheck;
	}*/


	function createFullQuoteRequest($lead_id, $row, $QuotationNumber, $SumInsured_Type){

		$policy_sub_type_id = isset($row['policy_sub_type_id']) ? $row['policy_sub_type_id'] : null;
		$proposal_details_id = isset($row['proposal_details_id']) ? $row['proposal_details_id'] : null;
		$master_policy_id = isset($row['master_policy_id']) ? $row['master_policy_id'] : null;
		$plan_id = isset($row['plan_id']) ? $row['plan_id'] : null;
		$customer_id = isset($row['customer_id']) ? $row['customer_id'] : null;
		$SumInsured = isset($row['sum_insured']) ? $row['sum_insured'] : null;
		$adult_count = isset($row['adult_count']) ? $row['adult_count'] : null;
		$child_count = isset($row['child_count']) ? $row['child_count'] : null;
		//$quote_date = isset($row['quote_date']) ? date("Y-m-d", strtotime($row['quote_date'])) : null;
		$PolicyNumber = isset($row['PolicyNumber']) ? $row['PolicyNumber'] : '';
		$mode_of_payment = isset($row['ppd_payment_mode']) ? $row['ppd_payment_mode'] : null;

		$transaction_date = isset($row['instrumentDate']) ? $row['instrumentDate'] : null;
		$transaction_number = isset($row['instrumentNumber']) ? $row['instrumentNumber'] : null;

		$cheque_date = isset($row['ppd_cheque_date']) ? $row['ppd_cheque_date'] : null;
		$cheque_number = isset($row['ppd_cheque_number']) ? $row['ppd_cheque_number'] : null;
		$account_number = isset($row['ppd_account_number']) ? $row['ppd_account_number'] : null;
		$bank_city = isset($row['ppd_bank_city']) ? $row['ppd_bank_city'] : null;
		$bank_branch = isset($row['ppd_bank_branch']) ? $row['ppd_bank_branch'] : null;
		$ifsc_code = isset($row['ppd_ifsc_code']) ? $row['ppd_ifsc_code'] : null;
		$bank_name = isset($row['ppd_bank_name']) ? $row['ppd_bank_name'] : null;

		$policy_number = $row['code']; //$this->getPolicyCode($policy_sub_type_id);
		$proposal_policy[$policy_number] = $row;

		if (!empty($child_count)) {
			$FamilyConstruct = $adult_count . "A+" . $child_count . "K";
		} else {
			$FamilyConstruct = $adult_count . "A";
		}

		//$getGroupCode = $this->getGroupCode($plan_id, $SumInsured, $FamilyConstruct);
		$MasterPolicyNumber = $row['policy_number']; //$this->getMasterPolicyNumber($master_policy_id);

		$proposal_policy_id = isset($row['proposal_policy_id']) ? $row['proposal_policy_id'] : null;
		$group_code_value = isset($row['group_code']) ? $row['group_code'] : null;

		$SourceSystemName_api = $row['source_name']; //$this->getSourceName($master_policy_id);
	
		//$uidNo = isset($getPolicyMemberDetails[0]['policy_member_pan']) ? $getPolicyMemberDetails[0]['policy_member_pan'] : null;
		$uidNo = isset($row['pan']) ? $row['pan'] : null;
		//$GroupID = isset($getGroupCode['group_code']) ? $getGroupCode['group_code'] : '';
		$GroupID = isset($group_code_value) ? $group_code_value : '';

		$plan_code = $row['plan_code']; //$this->getPlanCode($master_policy_id);
		$Product_Code = $row['product_code']; //$this->getProductCode($master_policy_id);
		$SchemeCode = $row['scheme_code']; //$this->getSchemeCode($master_policy_id);
		//$intermediaryCode = $this->getIntermediaryCode($master_policy_id);
		$Member_Type_Code = 'M209';
		//$intermediaryCode = '2108233';
		//$intermediaryBranchCode = '10MHMUM01';
		//$branch_imd_code_arr = $this->getIntermediaryCode($master_policy_id);

		//$intermediaryCode = '2108233';
		//$intermediaryBranchCode = '10MHMUM01';

		$intermediaryCode = $row['imd_code'];
		$intermediaryBranchCode =  $row['branch_code'];
		$product_id = isset($row['plan_id']) ? $row['plan_id'] : "";
		$SPID = 0;
		$RefCode1 = 0;
		$RefCode2 = 0;
		$terminal_id = ($row['ppd_payment_mode'] == 1) ? "EuxJCz8cZV9V63" : "";
		$Policy_Tanure = 1;
		$AutoRenewal = 'Y';
		$occupation = "O553";
		$explode_name = array($row['first_name'], $row['middle_name'], $row['last_name']);
		$explode_name_nominee = array($row['nominee_first_name'], $row['nominee_last_name']);
		$ukey = $row['proposal_details_id'] . '__' . $row['policy_number'];

		$fqrequest = [
			"ClientCreation" => [
				"Member_Customer_ID" => $row['customer_id'] . $policy_number . $proposal_policy_id,
				"salutation" => $row['salutation'],
				"firstName" => isset($row['first_name']) ? $row['first_name'] : "",
				"middleName" => isset($row['middle_name']) ? $row['middle_name'] : "",
				"lastName" => isset($row['last_name']) ? $row['last_name'] : "",
				"dateofBirth" => date('m/d/Y', strtotime($row['dob'])),
				"gender" => ($row['gender'] == "Male") ? "M" : "F",
				"educationalQualification" => null,
				"pinCode" => $row['pin_code'],
				"uidNo" => $uidNo,
				"maritalStatus" => null,
				"nationality" => "Indian",
				"occupation" => "O553",
				"primaryEmailID" => $row['email_id'],
				"contactMobileNo" =>  substr(trim($row['mobile_no']), -10),
				"stdLandlineNo" => null,
				"panNo" => null,
				"passportNumber" => null,
				"contactPerson" => null,
				"annualIncome" => null,
				"remarks" => null,
				"startDate" => date('Y-m-d'),
				"endDate" => null,
				"IdProof" => "AdhaarCard",
				"residenceProof" => null,
				"ageProof" => null,
				"others" => null,
				"homeAddressLine1" => $row['address_line1'],
				"homeAddressLine2" =>  $row['address_line2'],
				"homeAddressLine3" =>  $row['address_line3'],
				"homePinCode" => $row['pin_code'],
				"homeArea" => null,
				"homeContactMobileNo" => substr(trim($row['mobile_no']), -10),
				"homeContactMobileNo2" => null,
				"homeSTDLandlineNo" => null,
				"homeFaxNo" => null,
				"sameAsHomeAddress" => "1",
				"mailingAddressLine1" => null,
				"mailingAddressLine2" => null,
				"mailingAddressLine3" => null,
				"mailingPinCode" => null,
				"mailingArea" => null,
				"mailingContactMobileNo" => null,
				"mailingContactMobileNo2" => null,
				"mailingSTDLandlineNo" => null,
				"mailingSTDLandlineNo2" => null,
				"mailingFaxNo" => null,
				"bankAccountType" => null,
				"bankAccountNo" => null,
				"ifscCode" => $ifsc_code,
				"GSTIN" => null,
				"GSTRegistrationStatus" => "Consumers",
				"IsEIAavailable" => "0",
				"ApplyEIA" => "0",
				"EIAAccountNo" => null,
				"EIAWith" => "0",
				"AccountType" => null,
				"AddressProof" => null,
				"DOBProof" => null,
				"IdentityProof" => null
			],
			"PolicyCreationRequest" => [
				"Quotation_Number" => $QuotationNumber,
				"MasterPolicyNumber" => $MasterPolicyNumber,
				"GroupID" => $GroupID,
				"Product_Code" => $Product_Code,
				"SumInsured_Type" => $SumInsured_Type,
				"Policy_Tanure" => $Policy_Tanure,
				"Member_Type_Code" => $Member_Type_Code,
				"intermediaryCode" => $intermediaryCode,
				"AutoRenewal" => $AutoRenewal,
				"intermediaryBranchCode" => $intermediaryBranchCode,
				"agentSignatureDate" => null,
				"Customer_Signature_Date" => null,
				"businessSourceChannel" => null,
				"AssignPolicy" => "0",
				"AssigneeName" => null,
				"leadID" => $lead_id,
				"Source_Name" => $SourceSystemName_api,
				"SPID" => $SPID,
				"TCN" => null,
				"CRTNO" => null,
				"RefCode1" => $RefCode1,
				"RefCode2" => $RefCode2,
				"Employee_Number" => $row['customer_id'],
				"enumIsEmployeeDiscount" => null,
				"QuoteDate" => null,
				"IsPayment" => "1",
				"PaymentMode" => null,
				/*"PolicyproductComponents" => [
					[
						"PlanCode" => $plan_code,
						"SumInsured" => $SumInsured,
						"SchemeCode" => $SchemeCode
					]
				]*/
			],
			"ReceiptCreation" => [
				"officeLocation" => "Mumbai",
				"modeOfEntry" => "Direct",
				"cdAcNo" => null,
				"expiryDate" => null,
				"payerType" => "Customer",
				"payerCode" => null,
				"paymentBy" => "Customer",
				"paymentByName" => null,
				"paymentByRelationship" => null,
				// "collectionAmount" => round($collectionAmount),
				// TODO: change this amount later dynamically
				//"collectionAmount" => 25000,
				"collectionRcvdDate" => date('Y-m-d', strtotime($transaction_date)),
				"collectionMode" => $mode_of_payment,
				"remarks" => null,
				"instrumentNumber" => $transaction_number,
				"instrumentDate" => date('Y-m-d', strtotime($transaction_date)),
				"bankName" => $bank_name,
				"branchName" => str_replace(',', ' ', $bank_branch),
				"bankLocation" => $bank_city,
				"micrNo" => null,
				"chequeType" => null,
				"ifscCode" => $ifsc_code,
				"PaymentGatewayName" => $SourceSystemName_api,
				"TerminalID" => $terminal_id,
				"CardNo" => null
			]
		];

		$fqrequest['PolicyCreationRequest']['goGreen'] = ($row['go_green'] && $row['go_green'] == 'Y') ? 1 : 0;

		return $fqrequest;
	}

	/**
	 * New function as per change request
	 */
	function doFullQuote($lead_id){

//		ini_set('display_errors', 1);
		$arr_data = $this->getFullQuoteData($lead_id);

		$proposal_policy_check = sizeof($arr_data);
    	$nominees = $member = $policyData = $arrReturn = $policy_quote_arr = $master_policy_id_arr = [];
		
		if (!empty($arr_data)) {
			//$countQuery = "update proposal_payment_details set issuance_count=(issuance_count+1) where lead_id=$lead_id";
			//$this->db->query($countQuery);
			$updateProposalPaymentStatus = $this->updateProposalPaymentDetail(["proposal_status" => "PaymentReceived"], ['lead_id' => $lead_id]);

			$this->db->where("lead_id", $lead_id);
			$this->db->update("lead_details", array('status' => "Customer-Payment-Received"));

			foreach ($arr_data as $key => $row) {
	
				$data = $this->db->query("select status,proposal_policy_id from api_proposal_response where proposal_policy_id='" . $row['proposal_policy_id'] . "'")->row_array();
				//echo $this->db->last_query();exit;
				if (empty($data['proposal_policy_id']) || $data['status'] != 'Success') {
					
					$api_url = $this->getApiUrl($row['policy_sub_type_id']);
					
					if($row['imd_code'] == '' || $row['branch_code'] == ''){
	
						return array(
							"status" => "error",
							"msg" => "Branch and Intermediary Code for policy number is missing"
						);
					}
					
					if ($api_url == '') {
						return array(
							"status" => "error",
							"msg" => "Error in Service URL"
						);
					}
					
					if (empty($row['instrumentDate']) || empty($row['instrumentNumber']) || empty($row['group_code'])) {
						continue;
					}
					
					if(in_array($row['policy_sub_type_id'], [1,5])){
						
						$QuotationNumber = isset($row['QuotationNumber']) ? $row['QuotationNumber'] : null;
	
						if (empty($QuotationNumber)) {
							$quote_data = $this->doQuickQuote($lead_id, $row['proposal_policy_id']);
							if ($quote_data['Status'] == 'Success') {
								$QuotationNumber = $quote_data['QuotationNumber'];
							} else {
								continue;
							}
						}

						$getPolicyMemberDetails = $this->getPolicyMemberDetails($row['proposal_policy_id'], $row['master_policy_id'], $lead_id);
						$SumInsured_Type = $this->getSumInsuredType($row['master_policy_id']);
						
						$policyData['lead_id'] = $lead_id;
						$policyData['customer_id'] = $row['customer_id'];
						$policyData['master_policy_id'] = $row['master_policy_id'];
						$policyData['policy_sub_type_id'] = $row['policy_sub_type_id'];
						$policyData['proposal_policy_id'] = $row['proposal_policy_id'];
						$policyData['plan_id'] = $row['plan_id'];
		
						/* Nominees Details*/
						//if (empty($nominees)) {
							$nominees['nominee_relation'] = $row['nominee_relation'];
							$nominees['nominee_first_name'] = $row['nominee_first_name'];
							$nominees['nominee_last_name'] = $row['nominee_last_name'];
							$nominees['nominee_salutation'] = $row['nominee_salutation'];
							$nominees['nominee_gender'] = $row['nominee_gender'];
							$nominees['nominee_dob'] = $row['nominee_dob'];
							$nominees['nominee_contact'] = $row['nominee_contact'];
							$nominees['nominee_email'] = $row['nominee_email'];
						//}
		
						$member = $this->getMembersForQuote($row, $getPolicyMemberDetails, $nominees);
							
						$fqrequest = $this->createFullQuoteRequest($lead_id, $row, $QuotationNumber, $SumInsured_Type);
						
						if (empty($fqrequest)) {
							$arrReturn[$key][$row['policy_number']]["status"] = "Error";
							$arrReturn[$key][$row['policy_number']]["message"] = "Empty Request";
							$arrReturn["Status"] = "Error";
							continue;
						}
						
						if(!empty($fqrequest)){

							$fqrequest['PolicyCreationRequest']['PolicyproductComponents'] = [[
								"PlanCode" => isset($row['plan_code']) ? $row['plan_code'] : null,
								"SumInsured" => isset($row['sum_insured']) ? $row['sum_insured'] : null,
								"SchemeCode" => isset($row['scheme_code']) ? $row['scheme_code'] : null
							]];
							
							$fqrequest["MemObj"] = ["Member" => $member];
							
							$tax_amount = isset($row['tax_amount']) ? $row['tax_amount'] : 0;
							//$premium_amount = isset($row['premium_amount']) ? $row['premium_amount'] : 0;
							$collectionAmount = $tax_amount;

							$fqrequest['ReceiptCreation']['collectionAmount'] = $collectionAmount;						
						}
						
						//Full quote for GHI/Supertopup
						$getCurl = $this->getCurl($api_url, $fqrequest);
		
						if (!empty($getCurl)) {
							$status = isset($getCurl["status"]) ? $getCurl["status"] : '';
		
							$responseAPI = isset($getCurl["data"]) ? $getCurl["data"] : [];
		
							$request_arr = [
								"lead_id" => $lead_id,
								"req" => json_encode($fqrequest),
								"res" => json_encode($responseAPI),
								"product_id" => $row['plan_id'],
								"type" => "full_quote",
								"proposal_policy_id" => $row['proposal_policy_id']
							];
		
							$this->db->insert("logs_docs", $request_arr);
							$insert_id_ghi = $this->db->insert_id();
		
							//Application log entries
							$this->db->insert("application_logs", [
								"lead_id" => $lead_id,
								"action" => "full_quote",
								"request_data" => json_encode($fqrequest),
								"response_data" => json_encode($responseAPI),
								"created_on" => date("Y-m-d H:i:s")
							]);
		
							if (isset($getCurl["status"]) && $getCurl["status"] == "Success") {
								$saveProposal = $this->saveApiProposalResponse($responseAPI, $policyData, "Success", $insert_id_ghi);
								$arrReturn[$key][$row['policy_number']]["status"] = "Success";
								$arrReturn[$key][$row['policy_number']]["message"] = "";
							} else {
								$saveProposal = $this->saveApiProposalResponse($responseAPI, $policyData, "Error", $insert_id_ghi);
		
								$arrReturn[$key][$row['policy_number']]["status"] = "Error";
								$arrReturn[$key][$row['policy_number']]["message"] = isset($getCurl["message"]) ? $getCurl["message"] : "";
								$arrReturn["Status"] = "Error";
								continue;
							}
						} else {
							$request_arr = [
								"lead_id" => $lead_id,
								"req" => json_encode($fqrequest),
								"product_id" => $row['plan_id'],
								"type" => "full_quote",
								"proposal_policy_id" => $row['proposal_policy_id']
							];
							$this->db->insert("logs_docs", $request_arr);
							$insert_id = $this->db->insert_id();
							//echo $this->db->last_query();
							//exit;
		
							//Application log entries
							$this->db->insert("application_logs", [
								"lead_id" => $lead_id,
								"action" => "full_quote",
								"request_data" => json_encode($fqrequest),
								"created_on" => date("Y-m-d H:i:s")
							]);
		
							$arrReturn[$key][$row['policy_number']]["status"] = "Error";
							$arrReturn[$key][$row['policy_number']]["message"] = isset($getCurl["message"]) ? $getCurl["message"] : "";
							$arrReturn["Status"] = "Error";
						}
					}
					else{
						
						/*$policy_quote_arr[$row['customer_id']][$row['proposal_policy_id']] = $row;

						$policies[$row['customer_id']][$row['plan_code']] = [
							"PlanCode" => isset($row['plan_code']) ? $row['plan_code'] : null,
							"SumInsured" => isset($row['sum_insured']) ? $row['sum_insured'] : null,
							"SchemeCode" => isset($row['scheme_code']) ? $row['scheme_code'] : null
						];

						$tax_amount = isset($row['tax_amount']) ? $row['tax_amount'] : 0;
						//$premium_amount = isset($row['premium_amount']) ? $row['premium_amount'] : 0;

						if(isset($collectionAmount[$row['customer_id']])){

							$collectionAmount[$row['customer_id']] += $tax_amount;
						}
						else{

							$collectionAmount[$row['customer_id']] = $tax_amount;
						}

						$master_policy_id_arr[$row['master_policy_id']] = $row['master_policy_id'];

						$customer_wise_data[$row['customer_id']] = $row;*/

						$tax_amount_arr[$row['policy_number']] = isset($row['tax_amount']) ? $row['tax_amount'] : 0;
						//$premium_amount = isset($row['premium_amount']) ? $row['premium_amount'] : 0;

						if(isset($collectionAmountArr[$row['customer_id']][$row['policy_number']])){

							$collectionAmountArr[$row['customer_id']][$row['policy_number']] += $tax_amount_arr[$row['policy_number']];
						}
						else{

							$collectionAmountArr[$row['customer_id']][$row['policy_number']] = $tax_amount_arr[$row['policy_number']];
						}

						$master_policy_id_arr[$row['master_policy_id']] = $row['master_policy_id'];

						$arr_proposal_policy_for_combo[$row['customer_id']][$row['policy_number']] = $row;

						$proposal_policy_id_combo[$row['customer_id']][$row['policy_number']][$row['master_policy_id']] = $row['proposal_policy_id'];
						$policy_sub_type_id_combo[$row['customer_id']][$row['policy_number']][$row['master_policy_id']] = $row['policy_sub_type_id'];

						$PolicyproductComponents[$row['customer_id']][$row['policy_number']][$row['plan_code']] = [
							"PlanCode" => $row['plan_code'],
							"SumInsured" => $row['sum_insured'],
							"SchemeCode" => $row['scheme_code']
						];
					}
				}
			}

			if(isset($arr_proposal_policy_for_combo)){
				
				$getPolicyMemberDetailsForCombo = $this->getPolicyMemberDetailsForCombo($lead_id, $master_policy_id_arr);
				$member = $plan_codes = [];
				$quote_no_arr = [];

				if(!empty($getPolicyMemberDetailsForCombo)){
					
					foreach ($arr_proposal_policy_for_combo as $customer_id => $policy_number_arr) {

						foreach($policy_number_arr as $policy_number => $row)
						{

							if(!isset($quote_no_arr[$customer_id])){

								$arr_data = $this->db->query("SELECT QuotationNumber, cust_id, proposal_policy_id FROM quote_response WHERE lead_id = $lead_id AND policy_sub_type_id NOT IN (1,5)")->result_array();
						
								if(!empty($arr_data)){
	
									foreach($arr_data as $key => $value){
	
										$quote_no_arr[$value['cust_id']][$value['proposal_policy_id']] = $value['QuotationNumber'];
									}
								}
							}

							$QuotationNumber = isset($quote_no_arr[$customer_id][$row['proposal_policy_id']]) ? $quote_no_arr[$customer_id][$row['proposal_policy_id']] : null; //isset($row['QuotationNumber']) ? $row['QuotationNumber'] : null;
							$SumInsured_Type = $this->getSumInsuredType($row['master_policy_id']);
							
							if (empty($QuotationNumber)) {

								$is_combination = false;

								if(count($proposal_policy_id_combo[$customer_id][$policy_number]) > 1){

									$is_combination = true;
								}

								$quote_data = $this->doQuickQuote($lead_id, '', $customer_id, $is_combination);
								if ($quote_data['Status'] == 'Success') {
									$QuotationNumber = $quote_data['QuotationNumber'];
								} else {
									continue;
								}
							}
							
							$fqrequest = $this->createFullQuoteRequest($lead_id, $row, $QuotationNumber, $SumInsured_Type);
							$fqrequest['PolicyCreationRequest']['PolicyproductComponents'] = array_values($PolicyproductComponents[$customer_id][$policy_number]);

							$fqrequest['ReceiptCreation']['collectionAmount'] = isset($collectionAmountArr[$customer_id][$policy_number]) ? $collectionAmountArr[$customer_id][$policy_number] : '';
							
							foreach($getPolicyMemberDetailsForCombo[$customer_id][$policy_number] as $member_id => $member_arr){

								$plan_codes = $getPolicyMemberDetailsForCombo[$customer_id]['plan_code'][$policy_number] ?? [];
								$MemberproductComponents = [];

								foreach($plan_codes as $plan_code){

									$MemberproductComponents[$plan_code] = [
										"PlanCode" => $plan_code,
										"MemberQuestionDetails" => [[
											"QuestionCode" => null,
											"Answer" => null,
											"Remarks" => null
										]]
									];
								}

								$member = $this->getMembersForQuoteCombo($row, [$member_arr], $MemberproductComponents);
								$fqrequest["MemObj"] = ["Member" => $member];
								
								$api_url = $this->getApiUrl($row['policy_sub_type_id']);
								
								$getCurl = $this->getCurl($api_url, $fqrequest);

								if (!empty($getCurl)) {
									
									$status = isset($getCurl["status"]) ? $getCurl["status"] : '';
				
									$responseAPI = isset($getCurl["data"]) ? $getCurl["data"] : [];
				
									$request_arr = [
										"lead_id" => $lead_id,
										"req" => json_encode($fqrequest),
										"res" => json_encode($responseAPI),
										"product_id" => $row['plan_id'],
										"type" => "full_quote",
										"proposal_policy_id" => $row['proposal_policy_id']
									];
				
									$this->db->insert("logs_docs", $request_arr);
									$insert_id_ghi = $this->db->insert_id();
				
									//Application log entries
									$this->db->insert("application_logs", [
										"lead_id" => $lead_id,
										"action" => "full_quote",
										"request_data" => json_encode($fqrequest),
										"response_data" => json_encode($responseAPI),
										"created_on" => date("Y-m-d H:i:s")
									]);
									
									foreach($master_policy_id_arr as $master_policy_id_key => $master_policy_id_value){

										if(isset($proposal_policy_id_combo[$customer_id][$policy_number][$master_policy_id_key])){
											
											$policyData['lead_id'] = $lead_id;
											$policyData['customer_id'] = $customer_id;
											$policyData['master_policy_id'] = $master_policy_id_key;
											$policyData['policy_sub_type_id'] = $policy_sub_type_id_combo[$customer_id][$policy_number][$master_policy_id_key];
											$policyData['proposal_policy_id'] = $proposal_policy_id_combo[$customer_id][$policy_number][$master_policy_id_key];
											$policyData['plan_id'] = $row['plan_id'];

											$policyData['is_combination'] = 'N';

											if(count($proposal_policy_id_combo[$customer_id][$policy_number]) > 1){

												$policyData['is_combination'] = 'Y';
											}
						
											if (isset($getCurl["status"]) && $getCurl["status"] == "Success") {
												$saveProposal = $this->saveApiProposalResponse($responseAPI, $policyData, "Success", $insert_id_ghi);
												$arrReturn[$member_id][$row['policy_number']]["status"] = "Success";
												$arrReturn[$member_id][$row['policy_number']]["message"] = "";
											} else {
												$saveProposal = $this->saveApiProposalResponse($responseAPI, $policyData, "Error", $insert_id_ghi);
						
												$arrReturn[$member_id][$row['policy_number']]["status"] = "Error";
												$arrReturn[$member_id][$row['policy_number']]["message"] = isset($getCurl["message"]) ? $getCurl["message"] : "";
												$arrReturn["Status"] = "Error";
												continue;
											}
										}
									}
								} else {
									$request_arr = [
										"lead_id" => $lead_id,
										"req" => json_encode($fqrequest),
										"product_id" => $row['plan_id'],
										"type" => "full_quote",
										"proposal_policy_id" => $row['proposal_policy_id']
									];
									$this->db->insert("logs_docs", $request_arr);
									$insert_id = $this->db->insert_id();
									//echo $this->db->last_query();
									//exit;
				
									//Application log entries
									$this->db->insert("application_logs", [
										"lead_id" => $lead_id,
										"action" => "full_quote",
										"request_data" => json_encode($fqrequest),
										"created_on" => date("Y-m-d H:i:s")
									]);
				
									$arrReturn[$member_id][$row['policy_number']]["status"] = "Error";
									$arrReturn[$member_id][$row['policy_number']]["message"] = isset($getCurl["message"]) ? $getCurl["message"] : "";
									$arrReturn["Status"] = "Error";
								}
							}
						}
					}
				}
			}
			
			/*if(!empty($policy_quote_arr)){
				
				$getPolicyMemberDetailsForCombo = $this->getPolicyMemberDetailsForCombo($lead_id, $master_policy_id_arr);
				
				foreach($policy_quote_arr as $customer_id => $proposal_policy_id_arr){

					foreach($proposal_policy_id_arr as $proposal_policy_id => $arr){

						$QuotationNumber = isset($arr['QuotationNumber']) ? $arr['QuotationNumber'] : null;
						$SumInsured_Type = $this->getSumInsuredType($row['master_policy_id']);

						if (empty($QuotationNumber)) {
							$quote_data = $this->doQuickQuote($lead_id);
							if ($quote_data['Status'] == 'Success') {
								$QuotationNumber = $quote_data['QuotationNumber'];
								break;
							} else {
								continue;
							}
						}
					}

					//Full Quote Request
					$fqrequest = $this->createFullQuoteRequest($lead_id, $customer_wise_data[$customer_id], $QuotationNumber, $SumInsured_Type);
					
					if(!empty($fqrequest)){

						$fqrequest['ReceiptCreation']['collectionAmount'] = $collectionAmount[$customer_id];
						$fqrequest['PolicyCreationRequest']['PolicyproductComponents'] = array_values($policies[$customer_id]);
					}

					if(!empty($getPolicyMemberDetailsForCombo)){
						
						foreach($getPolicyMemberDetailsForCombo[$customer_id] as $member_id => $member_arr){

							$member = $this->getMembersForQuoteCombo($customer_wise_data[$customer_id], [$member_arr]);

							$fqrequest["MemObj"] = ["Member" => $member];
							
							$api_url = $this->getApiUrl($customer_wise_data[$customer_id]['policy_sub_type_id']);
							$getCurl = $this->getCurl($api_url, $fqrequest);print_r($fqrequest);

							if (!empty($getCurl)) {
								$status = isset($getCurl["status"]) ? $getCurl["status"] : '';
			
								$responseAPI = isset($getCurl["data"]) ? $getCurl["data"] : [];
			
								$request_arr = [
									"lead_id" => $lead_id,
									"req" => json_encode($fqrequest),
									"res" => json_encode($responseAPI),
									"product_id" => $customer_wise_data[$customer_id]['plan_id'],
									"type" => "full_quote",
									"proposal_policy_id" => $customer_wise_data[$customer_id]['proposal_policy_id']
								];

								$policyData['lead_id'] = $lead_id;
								$policyData['customer_id'] = $customer_id;
								$policyData['master_policy_id'] = $customer_wise_data[$customer_id]['master_policy_id'];
								$policyData['policy_sub_type_id'] = $customer_wise_data[$customer_id]['policy_sub_type_id'];
								$policyData['proposal_policy_id'] = $customer_wise_data[$customer_id]['proposal_policy_id'];
								$policyData['plan_id'] = $customer_wise_data[$customer_id]['plan_id'];
			
								$this->db->insert("logs_docs", $request_arr);
								$insert_id_ghi = $this->db->insert_id();
			
								//Application log entries
								$this->db->insert("application_logs", [
									"lead_id" => $lead_id,
									"action" => "full_quote",
									"request_data" => json_encode($fqrequest),
									"response_data" => json_encode($responseAPI),
									"created_on" => date("Y-m-d H:i:s")
								]);
			
								if (isset($getCurl["status"]) && $getCurl["status"] == "Success") {
									$saveProposal = $this->saveApiProposalResponse($responseAPI, $policyData, "Success", $insert_id_ghi);
									$arrReturn[$member_id][$row['policy_number']]["status"] = "Success";
									$arrReturn[$member_id][$row['policy_number']]["message"] = "";
								} else {
									$saveProposal = $this->saveApiProposalResponse($responseAPI, $policyData, "Error", $insert_id_ghi);
			
									$arrReturn[$member_id][$row['policy_number']]["status"] = "Error";
									$arrReturn[$member_id][$row['policy_number']]["message"] = isset($getCurl["message"]) ? $getCurl["message"] : "";
									$arrReturn["Status"] = "Error";
									continue;
								}
							} else {
								$request_arr = [
									"lead_id" => $lead_id,
									"req" => json_encode($fqrequest),
									"product_id" => $row['plan_id'],
									"type" => "full_quote",
									"proposal_policy_id" => $row['proposal_policy_id']
								];
								$this->db->insert("logs_docs", $request_arr);
								$insert_id = $this->db->insert_id();
								//echo $this->db->last_query();
								//exit;
			
								//Application log entries
								$this->db->insert("application_logs", [
									"lead_id" => $lead_id,
									"action" => "full_quote",
									"request_data" => json_encode($fqrequest),
									"created_on" => date("Y-m-d H:i:s")
								]);
			
								$arrReturn[$member_id][$row['policy_number']]["status"] = "Error";
								$arrReturn[$member_id][$row['policy_number']]["message"] = isset($getCurl["message"]) ? $getCurl["message"] : "";
								$arrReturn["Status"] = "Error";
							}
						}						
					}
				}
			}*/

			$success_check = $this->db->query("select count(lead_id) total_success from api_proposal_response where lead_id='" . $lead_id . "' and status = 'Success'")->row_array();

			$arr_data = $this->getFullQuoteData($lead_id);

			$proposal_policy_check = sizeof($arr_data);

			$arrReturnCheck["Status"] = ($success_check['total_success'] == $proposal_policy_check) ? "Success" : "Fail";

			if ($arrReturnCheck["Status"] == 'Success') {
				$updateProposalPaymentStatus = $this->updateProposalPaymentDetail(["proposal_status" => "Success"], ['lead_id' => $lead_id]);

				$this->db->where("lead_id", $lead_id);
				$this->db->update("lead_details", array('status' => "Approved"));
			}

			return $arrReturnCheck;
		}
	}

	public function updateProposalPaymentDetail($arr, $condition)
	{
		$this->db->where($condition);
		return $this->db->update("proposal_payment_details", $arr);
	}



	function geProposalPolicyDetails($lead_id, $status)
	{
		$arr_data = $this->db
			->select('pp.*,apr.*')
			->from('proposal_policy as pp')
			->join('api_proposal_response as apr', 'pp.proposal_policy_id=apr.proposal_policy_id')
			->where('pp.lead_id', $lead_id)
			->where('pp.status', $status)
			->get()
			->result_array();
		$arr_policy_members = [];
		foreach ($arr_data as $key => $value) {
			$geProposalPolicyMembers = $this->geProposalPolicyMembers($value["proposal_policy_id"]);
			if (!empty($geProposalPolicyMembers)) {
				foreach ($geProposalPolicyMembers as $key => $valuePpm) {
					$arr_policy_members[$value["proposal_policy_id"]][] = isset($valuePpm["member_type"]) ? $valuePpm["member_type"] : "";
				}
			}
		}
		$arr_data["policy_members"] = $arr_policy_members;
		return $arr_data;
	}

	function geProposalPolicyMembers($proposal_policy_id)
	{
		$arr_data = $this->db
			->select('ppm.member_id,ppmd.relation_with_proposal,fc.id,fc.member_type')
			->from('proposal_policy_member as ppm')
			->join('proposal_policy_member_details as ppmd', 'ppmd.member_id=ppm.member_id')
			->join('family_construct as fc', 'fc.id=ppmd.relation_with_proposal')
			->where('ppm.proposal_policy_id', $proposal_policy_id)
			->order_by("fc.id ASC")
			->get()
			->result_array();
		return $arr_data;
	}

	function geLeadId($customer_id)
	{
		$masterCustomer = $this->db->query("SELECT lead_id FROM master_customer WHERE customer_id='$customer_id' ORDER BY customer_id ASC")->row();
		return $lead_id  = isset($masterCustomer->lead_id) ? $masterCustomer->lead_id : "";
	}

	public function updateProposalPolicy($arr, $condition)
	{
		$this->db->where($condition);
		return $this->db->update("proposal_details", $arr);
	}

	public function getLeadByID($cols, $lead_id)
	{

		$this->db->select($cols);
		$this->db->from('lead_details');
		$this->db->where('lead_id', $lead_id);
		$data = $this->db->get()->result_array();
		return $data;
	}

	public function getMasterQuotesByLeadID($cols, $lead_id)
	{

		$this->db->select($cols);
		$this->db->from('master_quotes');
		$this->db->where('lead_id', $lead_id);
		$data = $this->db->get();

		return $data->result_array();
	}

	public function getProposalPaymentDocuments($lead_id)
	{
		$this->db->select('id_document_type,go_green');
		$data = $this->db->get_where('proposal_payment_details', ['lead_id' => $lead_id])->result_array();

		if (!empty($data)) {

			$result['id_document_type'] = $data[0]['id_document_type'];
			$result['go_green'] = $data[0]['go_green'];
		}

		$this->db->select('document_type,document_url');
		$data = $this->db->get_where('proposal_payment_documents', ['lead_id' => $lead_id])->result_array();

		$result['selected_payment_docs'] = [];
		if (!empty($data)) {

			foreach ($data as $row) {

				$result['selected_payment_docs'][$row['document_type']] = $row['document_url'];
			}
		}

		return $result;
	}

	public function getProposalDetails($lead_id)
	{

		//$this->db->select('*');
		//$this->db->join('master_customer as mc', 'mc.lead_id = lead_details.lead_id');
		//$this->db->join('proposal_details as pd', 'pd.lead_id = lead_details.lead_id');
		$data = $this->db->get_where('proposal_details', array('lead_id' => $lead_id))->result();
		return $data;
	}

	public function getProposalPolicySumInsured($lead_id)
	{

		$sql = "SELECT SUM(sum_insured) AS sum_insured FROM proposal_policy WHERE lead_id = $lead_id";
		$query = $this->db->query($sql);

		return $sum_insured_arr = $query->result_array();
	}

	function getAssignmentDeclarationByPlanIDCreditorID($plan_id, $creditor_id)
	{
		$this->db->select('content');
		$data = $this->db->get_where('assignment_declaration', array('plan_id' => $plan_id, 'creditor_id' => $creditor_id, 'is_active' => 1))->row();
		return $data;
	}

	function getMemberDetails($lead_id)
	{
		$arr_data = $this->db
			->select('ppmd.member_id,ppmd.policy_member_salutation,ppmd.relation_with_proposal,ppm.policy_id,ppmd.policy_member_gender,ppmd.policy_member_first_name,ppmd.policy_member_last_name,ppm.proposal_policy_id,fc.member_type,ppmd.policy_member_dob,mpst.policy_sub_type_name,ppmd.customer_id')
			->from('proposal_policy_member as ppm')
			->join('proposal_policy_member_details as ppmd', 'ppmd.member_id=ppm.member_id')
			->join('family_construct as fc', 'fc.id=ppmd.relation_with_proposal')
			->join('master_policy as mp', 'mp.policy_id=ppm.policy_id')
			->join('master_policy_sub_type as mpst', 'mpst.policy_sub_type_id=mp.policy_sub_type_id')
			//->join('master_quotes as mq', 'mq.lead_id = ppmd.lead_id')
			->where('ppm.lead_id', $lead_id)
			->get()
			->result_array();
		return $arr_data;
	}

	function getPolicyIDForInsuredMembers($member_type_id, $plan_id, $policy_id_arr)
	{
		$sql = "SELECT master_policy.policy_id, master_policy_si_type_mapping.suminsured_type_id, master_policy_family_construct.member_type_id FROM master_policy_family_construct
		JOIN master_policy ON master_policy.policy_id = master_policy_family_construct.master_policy_id
		JOIN master_policy_si_type_mapping ON master_policy_si_type_mapping.master_policy_id = master_policy.policy_id 
		WHERE member_type_id = $member_type_id AND plan_id = $plan_id
		AND master_policy.isactive = 1
		AND master_policy.policy_id IN (".implode(',', $policy_id_arr).")
		AND master_policy_family_construct.isactive =1 
		AND master_policy_si_type_mapping.isactive = 1";

		$query = $this->db->query($sql);

		return $policy_id_arr = $query->result_array();
	}

	function deleteEarlierQuotes($lead_id, $customer_id)
	{
		$sql = "delete from master_quotes where lead_id=" . $lead_id . " and master_customer_id=" . $customer_id;
		$this->db->query($sql);
	}

	function deleteEarlierGHDAnswers($customer_id, $lead_id)
	{
		$sql = "delete from ghd_declaration_answers where lead_id=" . $lead_id . " and customer_id=" . $customer_id;
		$this->db->query($sql);
	}

	function getMandatoryIfNotSelections($policy_id)
	{
		$sql = "select * from master_policy_mandatory_if_not_selected_rules where master_policy_id=" . $policy_id . " and isactive=1";
		$result = $this->db->query($sql)->result();

		return $result;
	}

	function branchImdList($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "branch_imd_mapping";
		$table_id = 'branch_imd_map_id';
		$default_sort_column = 'branch_code';
		$default_sort_order = 'ASC';
		$condition = "1=1";
		
		$colArray = array('i.policy_number','i.branch_code','i.imd_code','i.status');
		$sortArray = array('i.policy_number','i.branch_code','i.imd_code','i.status');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		//echo $post['iSortCol_0'];exit;
		//$s = $post['iSortCol_0'];
		$sort = isset($post['iSortCol_0']) ? strval($post['iSortCol_0']) : $default_sort_column; 
		//echo $sort;exit;		
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<8;$i++)
		{
			if($i==3)
			{
				if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
				{
					if($post['sSearch_'.$i] == 0){
						
						$condition .= " AND $colArray[$i] = ".$_POST['sSearch_'.$i];
					}
					else{
						
						$condition .= " AND $colArray[$i] = '".$_POST['sSearch_'.$i]."'";
					}
				}
			}
			else
			{
				if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
				{
					$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
				}
			}
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('*');
		$this -> db -> from('branch_imd_mapping as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('*');
		$this -> db -> from('branch_imd_mapping as i');
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

	public function get_min_max_age($creditor_id, $plan_id){

		$this->db->select('mf.master_policy_id, mf.member_min_age, mf.member_max_age');
		$this->db->from('master_policy mp');
		$this->db->join('master_policy_family_construct mf', 'mp.policy_id = mf.master_policy_id');
		$this->db->where('mp.creditor_id = '.$creditor_id.' AND mp.plan_id = '.$plan_id.' AND mf.member_type_id = 1 AND mf.isactive = 1 AND mp.isactive = 1');

		return $this->db->get()->result_array();
	}

	public function get_data_with_join(string $table, string $columns, string $join_table, string $join_on, string $join_type ='', string $where){

		$this->db->select($columns);
		$this->db->from($table);
		$this->db->join($join_table, $join_on, $join_type);
		$this->db->where($where);
		return $this->db->get()->result_array();
	}
}
