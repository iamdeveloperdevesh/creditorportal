<?PHP
class Apimodel extends CI_Model
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
		$this -> db -> where("($condition)");
		$this -> db -> update($tbl_name,$datar);
		 
		if ($this->db->affected_rows() > 0){
			return true;
		}else{
			return true;
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
	
	
	function getCreditorList($post)
	{
		$table = "master_ceditors";
		$table_id = 'creditor_id ';
		$default_sort_column = 'creditor_id ';
		$default_sort_order = 'desc';
		$condition = "1=1";
		
		$colArray = array('i.creaditor_name');
		$sortArray = array('i.creaditor_name','i.creditor_code','i.ceditor_email','i.creditor_mobile','i.creditor_phone','i.creditor_pancard','i.creditor_gstn','i.isactive');
		
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
		$this -> db -> from('master_ceditors as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('*');
		$this -> db -> from('master_ceditors as i');
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
	
	function getCreditorFormData($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('master_ceditors as i');
		$this -> db -> where('i.creditor_id', $ID);
	
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
	
	function getLoginUserDetails($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('master_employee as i');
		$this -> db -> where('i.employee_id', $ID);
	
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
	
	
	function getPermissionList($post)
	{
		$table = "permissions";
		$table_id = 'perm_id';
		$default_sort_column = 'perm_desc';
		$default_sort_order = 'asc';
		$condition = "1=1";
		
		$colArray = array('i.perm_desc');
		$sortArray = array('i.perm_desc');
		
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
		$this -> db -> from('permissions as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('*');
		$this -> db -> from('permissions as i');
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
	
	function getPermissionFormData($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('permissions as i');
		$this -> db -> where('i.perm_id', $ID);
	
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
	
	function getRoleList($post)
	{
		$table = "roles";
		$table_id = 'role_id';
		$default_sort_column = 'role_name';
		$default_sort_order = 'asc';
		$condition = "1=1";
		
		$colArray = array('i.role_name');
		$sortArray = array('i.role_name');
		
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
		$this -> db -> from('roles as i');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('*');
		$this -> db -> from('roles as i');
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
	
	function getRoleFormData($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('roles as i');
		$this -> db -> where('i.role_id', $ID);
	
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
	
	function getUserList($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "master_employee";
		$table_id = 'employee_id';
		$default_sort_column = 'employee_id';
		$default_sort_order = 'desc';
		$condition = "1=1";
		
		$colArray = array('i.user_name','i.employee_fname','i.employee_lname','i.employee_code','i.email_id','i.mobile_number','i.role_id','i.isactive');
		$sortArray = array('i.user_name','i.employee_fname','i.employee_lname','i.employee_code','i.email_id','i.mobile_number','i.role_id','i.isactive');
		
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
			if($i==6)
			{
				if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
				{
					$condition .= " AND $colArray[$i] = '".$_POST['sSearch_'.$i]."'";
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
		$this -> db -> from('master_employee as i');
		$this -> db -> join('roles as r', 'i.role_id  = r.role_id', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('*');
		$this -> db -> from('master_employee as i');
		$this -> db -> join('roles as r', 'i.role_id  = r.role_id', 'left');
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
	
	function getlocationList($post)
	{
		$table = "cities";
		$table_id = 'city_id';
		$default_sort_column = 's.state_name';
		$default_sort_order = 'asc';
		$condition = "1=1";
		
		$colArray = array('s.state_name','i.city_name');
		$sortArray = array('s.state_name','i.city_name');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		$sort = isset($post['iSortCol_0']) ? strval($sortArray[$post['iSortCol_0']]) : $default_sort_column;  
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<5;$i++)
		{
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.city_id,i.city_name, i.isactive,s.state_name');
		$this -> db -> from('cities as i');
		$this -> db -> join('states as s', 'i.city_state_id  = s.state_id ', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.city_id,i.city_name, i.isactive,s.state_name');
		$this -> db -> from('cities as i');
		$this -> db -> join('states as s', 'i.city_state_id  = s.state_id ', 'left');
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
	
	function getLocationFormData($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('cities as i');
		$this -> db -> where('i.city_id', $ID);
	
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
	
	function branchListing($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "creditor_branches";
		$table_id = 'branch_id';
		$default_sort_column = 'branch_name';
		$default_sort_order = 'asc';
		$condition = "1=1";
		
		$colArray = array('i.branch_name','c.creaditor_name','l.location_name','i.contact_no','i.email_id','i.isactive');
		$sortArray = array('i.branch_name','c.creaditor_name','l.location_name','i.contact_no','i.email_id','i.isactive');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		//echo $post['iSortCol_0'];exit;
		$sort = isset($post['iSortCol_0']) ? strval($post['iSortCol_0']) : $default_sort_column; 
		//echo $sort;exit;		
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<8;$i++)
		{
			if($i==6)
			{
				if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
				{
					$condition .= " AND $colArray[$i] = '".$_POST['sSearch_'.$i]."'";
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
		$this -> db -> select('i.*, c.creaditor_name, l.location_name');
		$this -> db -> from('creditor_branches as i');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('master_location as l', 'i.location_id  = l.location_id', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('*');
		$this -> db -> from('creditor_branches as i');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('master_location as l', 'i.location_id  = l.location_id', 'left');
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
	
	function getBranchesFormData($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('creditor_branches as i');
		$this -> db -> where('i.branch_id', $ID);
	
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
	
	function getLoginUserAccess($role_id)
	{	
		$condition = "i.role_id='".$role_id."' ";
		$this -> db -> select('p.perm_desc');
		$this -> db -> from('role_perm as i');
		$this -> db -> join('permissions as p', 'i.perm_id   = p.perm_id ', 'left');
		$this->db->where("($condition)");
		$this->db->order_by('i.perm_id', 'asc');
		
		$query = $this->db->get();
		
		// echo "<br/>";
		// print_r($this->db->last_query());
		// exit;
	   
		if($query->num_rows() >= 1)
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}
	
	function smCreditorMappingListing($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "sm_creditor_mapping";
		$table_id = 'sm_creditor_id';
		$default_sort_column = 'e.employee_full_name';
		$default_sort_order = 'asc';
		$condition = "1=1";
		
		$colArray = array('e.employee_full_name','c.creaditor_name','i.isactive');
		$sortArray = array('e.employee_full_name','c.creaditor_name','i.isactive');
		
		$page = $post['iDisplayStart'];	// iDisplayStart starting offset of limit funciton
		$rows = $post['iDisplayLength'];	// iDisplayLength no of records from the offset
		
		// sort order by column
		//echo $post['iSortCol_0'];exit;
		$sort = isset($post['iSortCol_0']) ? strval($post['iSortCol_0']) : $default_sort_column; 
		//echo $sort;exit;		
		$order = isset($post['sSortDir_0']) ? strval($post['sSortDir_0']) : $default_sort_order;

		for($i=0;$i<8;$i++)
		{
			if($i==6)
			{
				if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
				{
					$condition .= " AND $colArray[$i] = '".$_POST['sSearch_'.$i]."'";
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
		$this -> db -> select('i.*, e.employee_full_name, c.creaditor_name');
		$this -> db -> from('sm_creditor_mapping as i');
		$this -> db -> join('master_employee as e', 'i.sm_id  = e.employee_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.*, e.employee_full_name, c.creaditor_name');
		$this -> db -> from('sm_creditor_mapping as i');
		$this -> db -> join('master_employee as e', 'i.sm_id  = e.employee_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
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
	
	function getSMCreditorFormData($ID)
	{	
		$this -> db -> select('i.*');
		$this -> db -> from('sm_creditor_mapping as i');
		$this -> db -> where('i.sm_creditor_id', $ID);
	
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
	
	function leadListing($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "lead_details";
		$table_id = 'lead_id';
		$default_sort_column = 'lead_id';
		$default_sort_order = 'desc';
		if($_POST['role_id'] == 3){
			$condition = "i.createdby='".$_POST['user_id']."' ";
		}else{
			$condition = "1=1";
		}
		
		$colArray = array('i.trace_id','p.plan_name','c.creaditor_name','s.employee_full_name','cust.full_name','i.mobile_no','i.email_id','i.isactive');
		$sortArray = array('i.trace_id','p.plan_name','c.creaditor_name','s.employee_full_name','cust.full_name','i.mobile_no','i.email_id','i.isactive');
		
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
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
			
			
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.lead_id, i.trace_id, i.plan_id, i.creditor_id, i.sales_manager_id, i.customer_id, i.mobile_no, i.email_id, p.plan_name, c.creaditor_name, cust.full_name, s.employee_full_name');
		$this -> db -> from('lead_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('master_employee as s', 'i.sales_manager_id  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.customer_id  = cust.customer_id', 'left');
		
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.lead_id, i.trace_id, i.plan_id, i.creditor_id, i.sales_manager_id, i.customer_id, i.mobile_no, i.email_id, p.plan_name, c.creaditor_name, cust.full_name, s.employee_full_name');
		$this -> db -> from('lead_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> join('master_employee as s', 'i.sales_manager_id  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.customer_id  = cust.customer_id', 'left');
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
	
	
	function getRoleWiseCreditorsData($user_id)
	{	
		$condition = "i.sm_id='".$user_id."' ";
		
		$this -> db -> select('i.creditor_id, c.creaditor_name');
		$this -> db -> from('sm_creditor_mapping as i');
		$this -> db -> join('master_ceditors as c', 'i.creditor_id  = c.creditor_id', 'left');
		$this -> db -> where("($condition)");
		
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
	
	function discrepancyProposalListing($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "proposal_details";
		$table_id = 'proposal_details_id ';
		$default_sort_column = 'proposal_details_id ';
		$default_sort_order = 'desc';
		
		$condition = "i.status='Discrepancy' ";
		if($_POST['role_id'] == 3){
			$condition .= " && i.created_by='".$_POST['user_id']."' ";
		}
		
		$colArray = array('i.trace_id','p.plan_name','s.employee_full_name','cust.full_name');
		$sortArray = array('i.trace_id','p.plan_name','s.employee_full_name','cust.full_name');
		
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
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
			
			
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.proposal_details_id, i.lead_id, i.trace_id, i.plan_id, i.trace_id, i.created_by, i.customer_id, p.plan_name, cust.full_name, s.employee_full_name');
		$this -> db -> from('proposal_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_employee as s', 'i.created_by  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.customer_id  = cust.customer_id', 'left');
		
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.proposal_details_id, i.lead_id, i.trace_id, i.plan_id, i.trace_id, i.created_by, i.customer_id, p.plan_name, cust.full_name, s.employee_full_name');
		$this -> db -> from('proposal_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_employee as s', 'i.created_by  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.customer_id  = cust.customer_id', 'left');
		
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
	
	function customerProposalListing($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "proposal_details";
		$table_id = 'proposal_details_id ';
		$default_sort_column = 'proposal_details_id ';
		$default_sort_order = 'desc';
		if($_POST['role_id'] == 3){
			$condition = "i.created_by='".$_POST['user_id']."' ";
		}else{
			$condition = "1=1";
		}
		
		$colArray = array('i.trace_id','p.plan_name','s.employee_full_name','cust.full_name');
		$sortArray = array('i.trace_id','p.plan_name','s.employee_full_name','cust.full_name');
		
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
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
			
			
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.proposal_details_id, i.lead_id, i.trace_id, i.plan_id, i.trace_id, i.created_by, i.customer_id, p.plan_name, cust.full_name, s.employee_full_name');
		$this -> db -> from('proposal_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_employee as s', 'i.created_by  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.customer_id  = cust.customer_id', 'left');
		
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.proposal_details_id, i.lead_id, i.trace_id, i.plan_id, i.trace_id, i.created_by, i.customer_id, p.plan_name, cust.full_name, s.employee_full_name');
		$this -> db -> from('proposal_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_employee as s', 'i.created_by  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.customer_id  = cust.customer_id', 'left');
		
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
	
	
	function boProposalListing($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "proposal_details";
		$table_id = 'proposal_details_id ';
		$default_sort_column = 'proposal_details_id ';
		$default_sort_order = 'desc';
		
		$condition = "(i.status='BO-Approval-Awaiting' || i.status='Discrepancy' || i.status='Approved' || i.status='Rejected') ";
		if($_POST['role_id'] == 3){
			$condition .= " && i.created_by='".$_POST['user_id']."' ";
		}
		
		$colArray = array('i.trace_id','p.plan_name','s.employee_full_name','cust.full_name');
		$sortArray = array('i.trace_id','p.plan_name','s.employee_full_name','cust.full_name');
		
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
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
			
			
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.proposal_details_id, i.lead_id, i.trace_id, i.plan_id, i.trace_id, i.created_by, i.customer_id, i.status, i.updated_on, p.plan_name, cust.full_name, s.employee_full_name');
		$this -> db -> from('proposal_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_employee as s', 'i.created_by  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.customer_id  = cust.customer_id', 'left');
		
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.proposal_details_id, i.lead_id, i.trace_id, i.plan_id, i.trace_id, i.created_by, i.customer_id, i.status, i.updated_on, p.plan_name, cust.full_name, s.employee_full_name');
		$this -> db -> from('proposal_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_employee as s', 'i.created_by  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.customer_id  = cust.customer_id', 'left');
		
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
	
	
	function coProposalListing($post)
	{
		//echo "<pre>";print_r($post);exit;
		$table = "proposal_details";
		$table_id = 'proposal_details_id ';
		$default_sort_column = 'proposal_details_id ';
		$default_sort_order = 'desc';
		
		$condition = "(i.status='CO-Approval-Awaiting' || i.status='Discrepancy' || i.status='Approved' || i.status='Rejected') ";
		if($_POST['role_id'] == 3){
			$condition .= " && i.created_by='".$_POST['user_id']."' ";
		}
		
		$colArray = array('i.trace_id','p.plan_name','s.employee_full_name','cust.full_name');
		$sortArray = array('i.trace_id','p.plan_name','s.employee_full_name','cust.full_name');
		
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
			if(isset($post['sSearch_'.$i]) && $post['sSearch_'.$i]!='')
			{
				$condition .= " AND $colArray[$i] like '%".$_POST['sSearch_'.$i]."%'";
			}
			
			
		}
		
		//echo "Condition: ".$condition;
		//exit;
		$this -> db -> select('i.proposal_details_id, i.lead_id, i.trace_id, i.plan_id, i.trace_id, i.created_by, i.customer_id, i.status, i.updated_on, p.plan_name, cust.full_name, s.employee_full_name');
		$this -> db -> from('proposal_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_employee as s', 'i.created_by  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.customer_id  = cust.customer_id', 'left');
		
		$this->db->where("($condition)");
		$this->db->order_by($sort, $order);
		$this->db->limit($rows,$page);
		
		$query = $this -> db -> get();
		
		//print_r($this->db->last_query());
		//exit;
		
		$this -> db -> select('i.proposal_details_id, i.lead_id, i.trace_id, i.plan_id, i.trace_id, i.created_by, i.customer_id, i.status, i.updated_on, p.plan_name, cust.full_name, s.employee_full_name');
		$this -> db -> from('proposal_details as i');
		$this -> db -> join('master_plan as p', 'i.plan_id  = p.plan_id', 'left');
		$this -> db -> join('master_employee as s', 'i.created_by  = s.employee_id', 'left');
		$this -> db -> join('master_customer as cust', 'i.customer_id  = cust.customer_id', 'left');
		
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

}
?>
