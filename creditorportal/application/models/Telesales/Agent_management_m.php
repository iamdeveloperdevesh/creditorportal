<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Agent_management_m extends CI_Model
{

    

    function __construct()
    {
        parent::__construct();
      
        $telSalesSession = $this->session->userdata('telesales_session');
		$this->agent_id = encrypt_decrypt_password($telSalesSession['agent_id'],'D'); 

    }
	/* datatables related code start */
	var $table = "tls_base_agent_tbl";  	
	var $select_column = array("base_id","base_agent_id", "base_agent_name", "tl_name", "am_name", "om_name","status");  
	var $order_column = array(null, "base_agent_id", "base_agent_name", "tl_name", "am_name", "om_name","status"); 
	
	var $table1 = "tls_agent_mst"; 
	var $select_column1 = array("id","agent_id", "agent_name", "tl_name", "am_name", "om_name","status");
	var $order_column1 = array(null, "agent_id", "agent_name", "tl_name", "am_name", "om_name","status");  
	
	function make_query($tables,$select_column,$agent_id,$agent_name)  
	{
		
	   $this->db->select($this->$select_column);  
	   $this->db->from($this->$tables);  
	   
	   if(isset($_POST["search"]["value"]) && !empty($_POST["search"]["value"]))  
	   {  
		
		    $this->db->group_start();
		    $this->db->like($agent_id, $_POST["search"]["value"]);  
			$this->db->or_like($agent_name, $_POST["search"]["value"]); 
			$this->db->or_where("status", $_POST["search"]["value"]); 
			$this->db->group_end();
	   }
	   else
	   {
		   $this->db->where('status','Active');
	   }
	   if(isset($_POST["order"]))  
	   {  
			$this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);  
	   }  
	   else  
	   {  
			$this->db->order_by($agent_id, 'DESC');  
	   }  
	   
	  
	}  

	function do_datatables($table){

		$this->db->select('*');
		$this->db->from($table);
		$this->db->order_by('id','DESC');
		
		if ($_POST["length"] != -1) {
			$this->db->limit($_POST['length'], $_POST['start']);
		}
		$query=$this->db->get()->result();

		return $query;
	}

	function get_do_data($tables)  
	{  
	
	   $this->db->select("*");  
	   $this->db->from($this->$tables);  

	   return $this->db->count_all_results(); 
	}  



	function make_datatables($tables,$select_column,$agent_id,$agent_name){
	   $this->make_query($tables,$select_column,$agent_id,$agent_name);  
	   if($_POST["length"] != -1)  
	   {  
			$this->db->limit($_POST['length'], $_POST['start']);  
	   }  
	   $this->db->group_by('base_agent_id');
	   $query = $this->db->get();

	   return $query->result();  
	}  
	function get_filtered_data($tables,$select_column,$agent_id,$agent_name){  
	   $this->make_query($tables,$select_column,$agent_id,$agent_name);  
	//    $this->db->group_by('base_agent_id');
	   $query = $this->db->get();  
	   return $query->num_rows();  
	}       
	function get_all_data($tables)  
	{  
	   $this->db->select("*");  
	   $this->db->from($this->$tables);  
	//    $this->db->group_by('base_agent_id');

	   return $this->db->count_all_results();  
	}  
	
	/* datatables related code finished */
	public function insert_agent($data,$edit)
	{
		if($edit == 0 || $edit == '')
		{			
			$this->db->insert('tls_base_agent_tbl',$data);
			$success_data = ["status" => true, "message" => "Sucessfully Created"];
		}
		else
		{
			$this->db->where('base_id',$edit);
			$this->db->update('tls_base_agent_tbl',$data);
			$success_data = ["status" => true, "message" => "Sucessfully Updated"];
		}
		return $success_data;
	}
	
		public function insert_av($data,$edit)
	{
		if($edit == 0 || $edit == '')
		{	
			$agent_code = $data['agent_id'];
			$agent_code_check=$this->db->query("SELECT * from tls_agent_mst where `agent_id`= '$agent_code' ")->row_array();
			if($agent_code_check){
				$validation_err= ["status" => false, "message" => "AV ID aleready exists"];
				print_r(json_encode($validation_err));
				exit;
			}
			$this->db->insert('tls_agent_mst',$data);
			$success_data = ["status" => true, "message" => "Sucessfully Created"];
		}
		else
		{
			$this->db->where('id',$edit);
			$this->db->update('tls_agent_mst',$data);
			$success_data = ["status" => true, "message" => "Sucessfully Updated"];
		}
		return $success_data;
	}
	
}