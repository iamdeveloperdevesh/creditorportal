<?PHP
class Adsubcategorymodel extends CI_Model
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
		$table = "tbl_subcategories";
		$table_id = 'subcategory_id';
		$default_sort_column = 'subcategory_id';
		$default_sort_order = 'desc';
		$condition = "1=1";
		
		$colArray = array('i.category_id', 'i.subcategory_name','i.status');
		$sortArray = array('c.category_name', 'i.subcategory_name', 'i.status');
		
		$page = $get['iDisplayStart']; //iDisplayStart starting offset of limit funciton
		$rows = $get['iDisplayLength'];	//iDisplayLength no of records from the offset
		
		// sort order by column
		$sort = isset($get['iSortCol_0']) ? strval($sortArray[$get['iSortCol_0']]) : $default_sort_column;  
		$order = isset($get['sSortDir_0']) ? strval($get['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<3;$i++)
		{
			if($i==0)
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
		
		// echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('*,i.status as statuss');
		$this -> db -> from('tbl_subcategories as i');
		$this -> db -> join('tbl_categories as c', 'i.category_id  = c.category_id', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('*,i.status as statuss');
		$this -> db -> from('tbl_subcategories as i');
		$this -> db -> join('tbl_categories as c', 'i.category_id  = c.category_id', 'left');
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
		$this -> db -> from('tbl_subcategories as u');
		$this -> db -> where('u.subcategory_id', $ID);
	
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
  
	function updateRecord($datar,$eid)
	{
		$this -> db -> where('subcategory_id', $eid);
		if($this -> db -> update('tbl_subcategories',$datar))
		{
			return true;
		}
		else
		{
			return false;
		}	 
	}
	 
	function delrecord($tbl_name,$tbl_id,$record_id)
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