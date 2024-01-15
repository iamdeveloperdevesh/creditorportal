<?PHP
class Commonapimodel extends CI_Model
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
	
	function commongetdata($table, $fields, $condition = '1=1'){
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
	public function getMasterQuotesByLeadID($cols, $lead_id)
	{

		$this->db->select($cols);
		$this->db->from('master_quotes');
		$this->db->where('lead_id', $lead_id);
		$data = $this->db->get();

		return $data->result_array();
	}	

	/*
	Author : Amol Koli
	Date : 02th jan, 2021
	***/
	public function doCommunicate($request)
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
}
?>