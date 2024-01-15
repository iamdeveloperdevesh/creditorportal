<?PHP
class Myprofile_model extends CI_Model
{
	function insertData($tbl_name,$data_array,$sendid = NULL)
	{
	 	$this->db->insert($tbl_name,$data_array);
	 	$result_id = $this->db->insert_id();
	 	
	 	if($sendid == 1)
	 	{
	 		return $result_id;
	 	}
	}
	
	function getFormdata($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('tbl_admin_users as i');
		$this -> db -> where('i.user_id', $ID);
	
		$query = $this -> db -> get();
	   
		// print_r($this->db->last_query());
		// exit;
	   
		if($query -> num_rows() >= 1)
		{
			$res = $query->result_array();
			return $res[0];
		}
		else
		{
			return false;
		}
	}
	
	function updateRecord($table_name, $datar, $comp_id, $eid)
	{
		$this -> db -> where($comp_id, $eid);
		 
		if ($this -> db -> update($table_name,$datar))
		{
			return true;
		}
		else
		{
			return false;
		} 
	}
	
	function checkRecord($tbl_name,$POST,$condition)
	{	
		//print_r($POST);
		//exit;
		$this -> db -> select('*');
		$this -> db -> from($tbl_name);
		$this->db->where("($condition)");
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