<?PHP

class Customerapimodel extends CI_Model
{
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
		
		if(!empty($policy_id_arr)){

			$this->db->where_in('mp.policy_id', $policy_id_arr);
		}
		
		if($sort_by && $order){

			$this->db->order_by($sort_by,$order);
		}
		
		$data = $this->db->get('master_policy as mp')->result();
		return $data;
	}

	function getCustomerDetailByid($customer_id){
		return $this->db->get_where("master_customer",array("customer_id" => $customer_id))->row_array();
	}

	function getNoOfCI($policy_id)
	{
		$this->db->distinct();
		$this->db->select('numbers_of_ci');
		$this->db->order_by('numbers_of_ci', 'ASC');
		$data = $this->db->get_where('master_policy_premium_permile', array('master_policy_id' => $policy_id))->result();
		return $data;
	}
    function getPolicySubTypeName($policy_id)
    {

        $this->db->select('mpst.policy_sub_type_name');
        $this->db->join('master_policy_sub_type as mpst', 'mpst.policy_sub_type_id = mp.policy_sub_type_id');

        $data = $this->db->get_where('master_policy mp', array('mp.policy_id' => $policy_id))->row_array();
        return $data;
    }

    function getPolicySubTypePlanCreditor($plan_id, $creditor_id)
	{
	
		$this->db->select('mp.policy_sub_type_id, mp.policy_id , mp.is_combo,mp.is_optional, bm.si_premium_basis_id as basis_id,mpst.policy_sub_type_name');
		$this->db->join('master_policy_premium_basis_mapping as bm', 'bm.master_policy_id = mp.policy_id');
		$this->db->join('master_policy_sub_type as mpst', 'mpst.policy_sub_type_id = mp.policy_sub_type_id');

		$data = $this->db->get_where('master_policy mp', array('mp.plan_id' => $plan_id, 'mp.creditor_id' => $creditor_id, 'mp.isactive' => '1', 'bm.isactive' => 1))->result();
		return $data;
	}

	function getSumInsureDataDeductible($policy_id)
	{
		$this->db->distinct();
		$this->db->select('deductable');
		$this->db->order_by('deductable', 'ASC');
		$data = $this->db->get_where('master_policy_premium', array('master_policy_id' => $policy_id, 'isactive' => 1))->result();
		return $data;
	}

	function getfeaturesbyplan($plan_id, $creditor_id){
		$features_mst = $this->db->get('master_features')->result_array();
		foreach ($features_mst as $key => $value) {
			$features_mst[$key]['features'] = $this->db->get_where("features_config",["feature_id" => $value['id'], "plan_id" => $plan_id,"creditor_id" => $creditor_id, "isactive" => 1])->result_array();
		}
		return $features_mst;
	}

    function getSumInsureData($policy_id, $table = 'master_policy_premium')
	{
		$this->db->distinct();
		$this->db->select('sum_insured');
		$this->db->order_by('sum_insured', 'ASC');
		$data = $this->db->get_where($table, array('master_policy_id' => $policy_id, 'isactive' => 1))->result();
		//print_r($this->db->last_query());
		return $data;
	}
    function getSumInsureDataP($policy_id, $table = 'master_policy_premium')
    {
        $this->db->distinct();
        $this->db->select('sum_insured');
        $this->db->order_by('sum_insured', 'ASC');
        $data = $this->db->get_where($table, array('master_policy_id' => $policy_id, 'isactive' => 1))->result_array();
        //print_r($this->db->last_query());
        return $data;
    }
    function getSumInsureDataPolicy_compare($policy_id,$table,$min,$max)
    {
        $wherecon = "master_policy_id = $policy_id AND isactive = 1";
        if($min == 0 && $max==0){

        }else{
            if($min != 0) {
                $wherecon .= " AND (sum_insured >=" . $min . " AND sum_insured <= " . $max . ")";
                // print_r($where);
            }else{
                $wherecon .= " AND (sum_insured >=".$max.")";
            }
        }




  $data = $this->db->query("select distinct(sum_insured) from $table where $wherecon  order by sum_insured ASC ")->result_array();

        return $data;
    }
    function getSumInsureDataPolicy($policy_id,$table,$where=array())
    {
		
			        $w=array('master_policy_id' => $policy_id, 'isactive' => 1);

		
        $w=array('master_policy_id' => $policy_id, 'isactive' => 1);
        if(count($where) > 0){
            $w=   array_merge($w,$where);
        }
        $this->db->distinct();
        $this->db->select('sum_insured');
        $this->db->order_by('ABS(sum_insured)', 'ASC');
        $data = $this->db->get_where($table, $w)->result_array();
      //  print_r($this->db->last_query());
        return $data;
    }
	    function getSumInsureDataPolicy_permile($policy_id,$table,$max_age)
    {
	     $w="min_age<=" . $max_age. " AND max_age>=" . $max_age . " AND master_policy_id=" . $policy_id . " AND isactive=1";

       
        $this->db->distinct();
        $this->db->select('sum_insured');
        $this->db->order_by('sum_insured', 'ASC');
        $data = $this->db->get_where($table, $w)->result_array();
        //print_r($this->db->last_query());
        return $data;
    }
    function getPolicyFamilyConstruct($id)
	{
		$data = $this->db->get_where('master_policy_family_construct', array('master_policy_id' => $id, 'isactive' => 1))->result();
		return $data;
	}

    function getPolicyPremium($id)
	{
		$data = $this->db->get_where('master_policy_premium', array('master_policy_id' => $id, 'isactive' => 1))->result();
		return $data;
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
}