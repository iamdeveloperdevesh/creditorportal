<?PHP
class Usersmodel extends CI_Model
{
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
	 
	function getRecords($get)
	{
		//echo "here...<br/>";
		//print_r($get);
		
		$table = "tbl_admin_users";
		$table_id = 'user_id';
		$default_sort_column = 'user_id';
		$default_sort_order = 'asc';
		$condition = "1=1 AND i.user_type='1' ";
		
		$colArray = array('i.user_name','i.first_name','i.last_name','i.email_id','i.phone','i.role_id','i.status');
		
		$page = $get['iDisplayStart']; //iDisplayStart starting offset of limit funciton
		$rows = $get['iDisplayLength']; //iDisplayLength no of records from the offset
		
		// sort order by column
		$sort = isset($get['iSortCol_0']) ? strval($colArray[$get['iSortCol_0']]) : $default_sort_column;  
		$order = isset($get['sSortDir_0']) ? strval($get['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<6;$i++)
		{
			if($i==5)
			{
				if(isset($get['sSearch_'.$i]) && $get['sSearch_'.$i]!='')
				{
					$condition .= " AND $colArray[$i] = '".$get['sSearch_'.$i]."'";
				}
			}
			else
			{
				if(isset($get['sSearch_'.$i]) && $get['sSearch_'.$i]!='')
				{
					$condition .= " AND $colArray[$i] like '%".$_GET['sSearch_'.$i]."%'";
				}
			}
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('*');
		$this -> db -> from('tbl_admin_users as i');
		$this -> db -> join('roles as r', 'i.role_id  = r.role_id', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('*');
		$this -> db -> from('tbl_admin_users as i');
		$this -> db -> join('roles as r', 'i.role_id  = r.role_id', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		
		$query1 = $this -> db -> get();
		//echo "total: ".$query1 -> num_rows();
		//exit;
		
		if($query -> num_rows() >= 1)
		{
			$totcount = $query1 -> num_rows();
			return array("query_result" => $query->result(),"totalRecords"=>$totcount);
		}
		else
		{
			return array("totalRecords"=>0);
		}
	}
	
	function getData($select, $table, $condition = "", $sort_col = "", $sort_order = "")
	{	
		$this -> db -> select($select);
		$this -> db -> from($table);
		
		if(!empty($condition))
		{
			$this -> db -> where($condition);
		}
		
		if(!empty($sort_col) && !empty($sort_order))
		{
			$this -> db -> order_by($sort_col, $sort_order);
		}
		
		$query = $this -> db -> get();
		
		// echo "<br/>";
		// print_r($this->db->last_query());
		// exit;
	   
		if($query -> num_rows() >= 1)
		{
			return $query->result();
		}
		else
		{
			return false;
		}
	}
	
	function getFormdata($ID)
	{	
		$this -> db -> select('u.*');
		$this -> db -> from('tbl_admin_users as u');
		$this -> db -> where('u.user_id', $ID);
	
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
	
	//Update customer by id
	function updateUserId($datar,$eid)
	{
		$this -> db -> where('user_id', $eid);
		if($this -> db -> update('tbl_admin_users',$datar))
		{
			return true;
		}
		 else
		{
			return false;
		}
	}
	 
	function delrecord($tbl_name,$tbl_id,$record_id,$data)
	{
		//print_r($data);exit;
		$this -> db -> where($tbl_id, $record_id);
		if ($this -> db -> update($tbl_name,$data))
		{
			return true;
		}
		else
		{
			return false;
		} 
	}
	
	function delrecords($tbl_name,$tbl_id,$record_id)
	{
		$this->db->where($tbl_id, $record_id);
	    if($this->db->delete($tbl_name))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	 
	function checkRecord($POST,$condition)
	{
		//print_r($POST);
		//exit;
		$this -> db -> select('r.user_id');
		$this -> db -> from('tbl_admin_users as r');
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
	function getEmailRecord($tbl_name, $fields, $condition){
    	$this -> db -> select($fields);
		$this -> db -> from($tbl_name);
        $this -> db -> where($condition);
		$query = $this -> db -> get();
    	return $query->result();
    }
	
	function dataExist($table,$data,$label){
		$this -> db -> select("*");
		$this -> db -> from($table);
		$this -> db -> where($label, $data);
		$query = $this -> db -> get();
		if($query -> num_rows() >= 1){
			return $query ->result()[0]->user_id;
		}else{
			return false;
		}
	}
}
?>