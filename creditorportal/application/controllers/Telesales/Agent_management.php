<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once(APPPATH."controllers/MY_TelesalesSessionCheck.php");

class Agent_management extends MY_TelesalesSessionCheck
{

    public function __construct()
    {
        parent::__construct();
		
		$this->load->model("Telesales/Agent_management_m", "agent_m", true);
		if (!$this->session->userdata('telesales_session')) 
		{
            redirect('login');
        }
		$telSalesSession = $this->session->userdata('telesales_session');
		$this->agent_id = encrypt_decrypt_password($telSalesSession['agent_id'],'D');

		if ($_SESSION['telesales_session']['is_redirect_allow'] != "1")
        {
            redirect('login');
        }
        if (!$this->input->is_ajax_request()) {
                moduleCheck();
        }

    }

    public function index()
    {
	}
	public function create_agent()
    {
		$data['lob'] = $this->db->select('*')
         ->from('tls_axis_lob l')
         ->order_by('l.axis_lob','asc')
		->get()
		->result_array();

		$data['location'] = $this->db->select('*')
         ->from('tls_axis_location l')
         ->order_by('l.axis_location','asc')
		->get()
		->result_array();

		$data['vendor'] = $this->db->select('*')
         ->from('tls_axis_vendor l')
		 ->group_by('l.axis_vendor')
         ->order_by('l.axis_vendor','asc')
		->get()
		->result_array();
		$this->load->telesales_template("Agent_management_view", $data);
    }
	public function create_av()
    {

		$data['lob'] = $this->db->select('axis_lob')
         ->from('tls_axis_lob l')
         ->order_by('l.axis_lob','asc')
		->get()
		->result_array();

		$data['location'] = $this->db->select('axis_location')
         ->from('tls_axis_location l')
         ->order_by('l.axis_location','asc')
		->get()
		->result_array();

		$data['axis_process'] = $this->db->distinct()->select('axis_process')
         ->from('tls_axis_lob l')
         ->order_by('l.axis_lob','asc')
		->get()
		->result_array();

		$this->load->telesales_template("Agent_av_upload_view",$data);
   }
	public function create_outbond()
    {

		$this->load->telesales_template("Agent_outbond_upload_view.php");
    }

	public function create_doupload()
    {

		$this->load->telesales_template("Agent_do_upload_view.php");
    }

	public function get_datatable_ajax()
    {
		//ajax call
        //get Permission
		$emp_id=$this->agent_id;
		$query=$this->db->query('select password_change_access from tls_agent_mst where id='.$emp_id)->row();
		 $password_change_access=$query->password_change_access;
		$fetch_data = $this->agent_m->make_datatables($table = "table",$select_column = "select_column",$agent_id = "base_agent_id",$agent_name = "base_agent_name");  		

		$data = array();  
		$i = 0;
		foreach($fetch_data as $row)  
		{  

			$sub_array = array();  		
			$i++;
			$sub_array[] = $i;  
			$sub_array[] = $row->base_agent_id;  
			$sub_array[] = $row->base_agent_name;  
			$sub_array[] = $row->tl_name;  
			$sub_array[] = $row->am_name;  
			$sub_array[] = $row->om_name;  
			$sub_array[] = $row->status;
			if($password_change_access == 1){
                $sub_array[] = '<button type="button" name="edit" class="btn btn-nope btn-xs" onclick = "editAgent(this);" id="'.$row->base_id.'" data-toggle="modal" data-target="#myModal"><i class="ti-pencil"></i></button>
                                <button type="button" name="delete" onclick = "deleteAgent(this);" id="'.$row->base_id.'" class="btn btn-nope btn-xs"><i class="ti-trash"></i></button>
                                <button type="button" name="edit" class="btn btn-nope btn-xs" id="'.$row->base_id.'" onclick = "ResetPasswordModal(this);"><i class="ti-user"></i></button>
                                ';
            }else{
                $sub_array[] = '<button type="button" name="edit" class="btn btn-nope btn-xs" onclick = "editAgent(this);" id="'.$row->base_id.'" data-toggle="modal" data-target="#myModal"><i class="ti-pencil"></i></button><button type="button" name="delete" onclick = "deleteAgent(this);" id="'.$row->base_id.'" class="btn btn-nope btn-xs"><i class="ti-trash"></i></button>';
            }

			$data[] = $sub_array;  
		}  
		$output = array(  
			"draw"            => intval($_POST["draw"]),  
			"recordsTotal"    => $this->agent_m->get_all_data($table = "table"),  
			"recordsFiltered" => $this->agent_m->get_filtered_data($table = "table",$select_column = "select_column",$agent_id = "base_agent_id",$agent_name = "base_agent_name"),  
			"data"            => $data  
		);  

		echo json_encode($output);
	}
	
		public function get_av_datatable_ajax()
    {
		//ajax call

        $emp_id=$this->agent_id;
        $query=$this->db->query('select password_change_access from tls_agent_mst where id='.$emp_id)->row();
        $password_change_access=$query->password_change_access;
		// $fetch_data = $this->agent_m->make_datatables($table = 'table1',$select_column = "select_column1",$agent_id = "id",$agent_name = "agent_name");  		
		$this->db->select('*');
		$this->db->from('tls_agent_mst');
		$this->db->order_by('id','DESC');
		
		if ($_POST["length"] != -1) {
			$this->db->limit($_POST['length'], $_POST['start']);
		}

		if(isset($_POST["search"]["value"]) && !empty($_POST["search"]["value"]))  
	   {  
		
		    $this->db->group_start();
		    $this->db->like('agent_id', $_POST["search"]["value"]);  
			$this->db->or_like('agent_name', $_POST["search"]["value"]); 
			$this->db->or_like("axis_process", $_POST["search"]["value"]); 
			$this->db->or_like("lob", $_POST["search"]["value"]); 
			$this->db->or_like("center", $_POST["search"]["value"]); 
			$this->db->group_end();
	   }
		
		$query=$this->db->get()->result();

$fetch_data = $query;
		
		$data = array();  
		$i = 0;
		foreach($fetch_data as $row)  
		{  

			$sub_array = array();  		
			$i++;
			$sub_array[] = $i;  
			$sub_array[] = $row->agent_id;  
			$sub_array[] = $row->agent_name;  
			// $sub_array[] = $row->lob;  
			$sub_array[] = $row->center;  
			$sub_array[] = $row->axis_process;  

			$sub_array[] = $row->license_from?date('d-M-y',strtotime($row->license_from)):'';  
			$sub_array[] = $row->license_to?date('d-M-y',strtotime($row->license_to)):'';  
			$sub_array[] = '<button type="button" name="autit_agent_mst" class="btn btn-cta btn-primary" onclick = "aduit_agent_mst(this);" id="'.$row->agent_id.'" data-toggle="modal" data-target="#myModalaudit_mst">Audit</button>';
			$sub_array[] = $row->status;
            if($password_change_access == 1){
                $sub_array[] = '<button type="button" name="edit" class="btn btn-nope btn-xs" onclick = "editAgent(this);" id="'.$row->id.'" data-toggle="modal" data-target="#myModal"><i class="ti-pencil"></i></button><button type="button" name="delete" onclick = "deleteAgent(this);" id="'.$row->id.'" class="btn btn-nope btn-xs"><i class="ti-trash"></i></button>
 <button type="button" name="edit" class="btn btn-nope btn-xs" id="'.$row->id.'" onclick = "ResetPasswordModal(this);"><i class="ti-user"></i></button>
';

            }else{
                $sub_array[] = '<button type="button" name="edit" class="btn btn-nope btn-xs" onclick = "editAgent(this);" id="'.$row->id.'" data-toggle="modal" data-target="#myModal"><i class="ti-pencil"></i></button><button type="button" name="delete" onclick = "deleteAgent(this);" id="'.$row->id.'" class="btn btn-nope btn-xs"><i class="ti-trash"></i></button>';
            }

			$data[] = $sub_array;  
		}  
		$output = array(  
			"draw"            => intval($_POST["draw"]),  
			"recordsTotal"    => $this->agent_m->get_all_data($table = "table1"),  
			"recordsFiltered" => 0,//$this->agent_m->get_filtered_data($table = 'table1',$select_column = "select_column1",$agent_id = "id",$agent_name = "agent_name"),  
			"data"            => $data  
		);  
		echo json_encode($output);
	}

	public function get_do_datatable_ajax()
	{
	//ajax call
        $emp_id=$this->agent_id;
        $query=$this->db->query('select password_change_access from tls_agent_mst where id='.$emp_id)->row();
        $password_change_access=$query->password_change_access;
	$this->db->select('*');
		$this->db->from('tls_master_do');
		$this->db->order_by('created_at','DESC');
		
		if ($_POST["length"] != -1) {
			$this->db->limit($_POST['length'], $_POST['start']);
		}

		if(isset($_POST["search"]["value"]) && !empty($_POST["search"]["value"]))  
	   {  
		
		    $this->db->group_start();
		    $this->db->like('do_id', $_POST["search"]["value"]);  
			$this->db->or_like('do_name', $_POST["search"]["value"]); 
			$this->db->or_like("tl_id", $_POST["search"]["value"]); 
			$this->db->or_like("tl_name", $_POST["search"]["value"]); 
			$this->db->or_like("center", $_POST["search"]["value"]); 
			$this->db->group_end();
	   }
		
		$query=$this->db->get()->result();
	
	$fetch_data = $query;  			
	$data = array();  
	$i = 0;
	foreach($fetch_data as $row)  
	{  

		$sub_array = array();  		
		$i++;
		// $sub_array[] = $i;  
		$sub_array[] = $row->do_id;  
		$sub_array[] = $row->do_name;  
		$sub_array[] = $row->tl_id;  
		$sub_array[] = $row->tl_name;  
		$sub_array[] = $row->center;  
		$sub_array[] = $row->status; 
		 //$sub_array[] = '<button type="button" name="edit" class="btn btn-nope btn-xs" onclick = "editAgent(this);" id="'.$row->id.'" data-toggle="modal" data-target="#myModal"><i class="ti-pencil"></i></button><button type="button" name="delete" onclick = "deleteAgent(this);" id="'.$row->id.'" class="btn btn-nope btn-xs"><i class="ti-trash"></i></button>';
        if($password_change_access == 1) {
            $sub_array[] = ' <button type="button" name="edit" class="btn btn-nope btn-xs" id="' . $row->id . '" onclick = "ResetPasswordModal(this);"><i class="ti-user"></i></button>';
        }else{
            $sub_array[] = ' <button type="button" name="edit" class="btn btn-nope btn-xs" disabled><i class="ti-user"></i></button>';
        }
        $data[] = $sub_array;
	}  

	$this->db->select("*");  
$this->db->from('tls_master_do');  
$query = $this->db->get();
$query->num_rows();
$recordsTotal = $query->num_rows();

	$output = array(  
		"draw"            => intval($_POST["draw"]),  
		"recordsTotal"    => $recordsTotal,  
		"recordsFiltered" => $recordsTotal,  
		"data"            => $data  
	);  
	echo json_encode($output);
}


public function get_outbond_datatable_ajax()
{
//ajax call
$this->db->select('*');
		$this->db->from('tls_agent_mst_outbound');
		$this->db->order_by('created_at','DESC');
		
		if ($_POST["length"] != -1) {
			$this->db->limit($_POST['length'], $_POST['start']);
		}

		if(isset($_POST["search"]["value"]) && !empty($_POST["search"]["value"]))  
	   {  
		
		    $this->db->group_start();
		    $this->db->like('agent_id', $_POST["search"]["value"]);  
			$this->db->or_like('agent_name', $_POST["search"]["value"]); 
			$this->db->or_like("axis_process", $_POST["search"]["value"]); 
			$this->db->or_like("center", $_POST["search"]["value"]); 
			$this->db->group_end();
	   }
		
		$query=$this->db->get()->result();

$fetch_data = $query;  			
$data = array();  
$i = 0;
foreach($fetch_data as $row)  
{  

	$sub_array = array();  		
	$i++;
	// $sub_array[] = $i;  
	$sub_array[] = $row->agent_id;  
	$sub_array[] = $row->agent_name;  
	$sub_array[] = $row->axis_process;  
	$sub_array[] = $row->center;  
	$sub_array[] = $row->status;  
	$sub_array[] = $row->created_at; 
	// $sub_array[] = '<button type="button" name="edit" class="btn btn-nope btn-xs" onclick = "editAgent(this);" id="'.$row->id.'" data-toggle="modal" data-target="#myModal"><i class="ti-pencil"></i></button><button type="button" name="delete" onclick = "deleteAgent(this);" id="'.$row->id.'" class="btn btn-nope btn-xs"><i class="ti-trash"></i></button>';  
	$data[] = $sub_array;  
}  

// $role_access_module =  $this->db->select("COUNT(id) as count_id")
// 										->from("tls_agent_mst_outbound")
// 										->get()
// 			
							// ->row_array();
$this->db->select("*");  
$this->db->from('tls_agent_mst_outbound');  
$query = $this->db->get();
$query->num_rows();
$recordsTotal = $query->num_rows();

// $recordsTotal = $this->db->count_all_results('', TRUE); 

$recordsFiltered = $this->get_filtered_rows();
// echo $recordsFiltered;exit;
$output = array(  
	"draw"            => intval($_POST["draw"]),  
	"recordsTotal"    => $recordsTotal,  
	"recordsFiltered" => $recordsTotal,    
	"data"            => $data  
);  
// print_r($output);exit;
echo json_encode($output);
}

public function get_filtered_rows(){
	

		 $this->db->select("*");  
	$this->db->from('tls_agent_mst_outbound');  

	if ($_POST["length"] != -1) {
		$this->db->limit($_POST['length'], $_POST['start']);
	}

	$query = $this->db->get();
         $query->num_rows();
		 return $query->num_rows();
}

	public function get_role_module()
	{
		$role_access_module =  $this->db->select("role_module_id,acc_module_name")
										->from("role_access_module AS ram")
										->where("ram.product = 'R06'")
										->get()
										->result_array();
								
										
		echo json_encode($role_access_module);
	}
	public function create_agent_insert()
	{
		
		
		$this->form_validation->set_rules('agentCode', 'Agent Code', 'required|trim');
		$this->form_validation->set_rules('agentName', 'Agent Name', 'required|trim');
		//$this->form_validation->set_rules('role_types', 'Role Type', 'required|trim');
		//$this->form_validation->set_rules('create_module[]', 'Module Access', 'required|trim');
		$this->form_validation->set_error_delimiters('','');
		if ($this->form_validation->run() == FALSE)
		{
			
			 $z = validation_errors();
			
			 $validation_err= ["status" => false, "message" => $z];
             print_r(json_encode($validation_err));
		
			 
			   
		}
		else
		{
			$create_agent_post = $this->input->post(null,true);
			if($create_agent_post != null || !empty($create_agent_post))
			$edit = $create_agent_post['edit'];
			$role_types = $this->input->post('role_types',true);
			if($role_types == 'Admin')
			{
				$is_admin = 1;
			}
			else
			{
				$is_admin = 0;
			}
			if($create_agent_post['login'] == 'Y')
			{
				$password = encrypt_decrypt_password($create_agent_post['agentCode']);
			}
			else
			{
				$password = '';
			}
			$password = encrypt_decrypt_password($create_agent_post['agentCode']);

			$agent_code = $create_agent_post['agentCode'];
			$agent_name = $create_agent_post['agentName'];
			$tl_id      = $create_agent_post['tlId'];
			$tl_name	= $create_agent_post['tlName'];
			$am_id	    = $create_agent_post['amId'];	
			$am_name	= $create_agent_post['amName'];
			$om_id	    = $create_agent_post['omId'];
			$om_name	= $create_agent_post['omName'];	
			$module     = implode(',',$create_agent_post['create_module']);
			$is_login   = $create_agent_post['login'];
			$lob   = $create_agent_post['lob'];
			$center   = $create_agent_post['center'];
			$vendor   = $create_agent_post['vendor'];
			$abhi_sales_manager = $create_agent_post['abhi_sales_manager'];
			$abhi_area_head = $create_agent_post['abhi_area_head'];
			$rec_manager_code = $create_agent_post['rec_manager_code'];
			$imd_code = $create_agent_post['imd_code'];
			$base_emp_id =  $create_agent_post['base_emp_id'];
			$module_access_rights='22,33,59';
			$data = array(
			
								'base_agent_id'   => $agent_code,
								'base_agent_name' => $agent_name,
								'tl_name'    => $tl_name,
								'tl_emp_id'  => $tl_id,
								'am_name'    => $am_name,
								'am_emp_id'  => $am_id,
								'om_name'    => $om_name,
								'om_emp_id'    => $om_id,
								'status' => 'Active',
								'lob'=>$lob,
								'center'=>$center,
								'vendor'=>$vendor,
								'abhi_sales_manager' => $abhi_sales_manager,
								'abhi_area_head' => $abhi_area_head,
								'rec_manager_code' => $rec_manager_code,
								'imd_code' => $imd_code,
								'base_emp_id' => $base_emp_id,
								'password'=>$password,
								'module_access_rights'=>$module_access_rights
							
						);
			$success_data = $this->agent_m->insert_agent($data,$edit);			

             print_r(json_encode($success_data));
		}
		
	}
	public function edit_agent_data()
	{
			$edit_agent_post = $this->input->post(null,true);
			if($edit_agent_post != null || !empty($edit_agent_post))
			
			$agent_id = $edit_agent_post['edit'];
			$edit_data =  $this->db->select("base_id,base_agent_id,base_emp_id,base_agent_name,tl_name,tl_emp_id,am_emp_id,am_name,om_emp_id,om_name,center,lob,vendor,abhi_sales_manager,abhi_area_head,rec_manager_code,imd_code")
											->from("tls_base_agent_tbl AS tam")
											->where("tam.base_id",$agent_id)
											->get()
											->result_array();
			print_r(json_encode($edit_data));
	}
	public function delete_agent_data()
	{
			$delete_agent_post = $this->input->post(null,true);
			if($delete_agent_post != null || !empty($delete_agent_post))			
			$del_id = $delete_agent_post['delete_data'];			
			$data = array(
							'status' => 'Inactive'
						 );
			            $this->db->where('base_id',$del_id);
		    $result =	$this->db->update('tls_base_agent_tbl',$data);
			if($result == 1)
			{
				$success_data = ["status" => true, "message" => "Sucessfully Deleted"];
			}
			else
			{
				$success_data = ["status" => false, "message" => "Something Went Wrong"];
			}
			print_r(json_encode($success_data));
	}

	public function create_av_insert()
	{
		$this->form_validation->set_rules('agentCode', 'Agent Code', 'required|trim');
		$this->form_validation->set_rules('agentName', 'Agent Name', 'required|trim');
		$this->form_validation->set_rules('center', 'Center', 'required|trim');
		$this->form_validation->set_rules('axis_process', 'Axis Process', 'required|trim');
		$this->form_validation->set_rules('role_types', 'Role Type', 'required|trim');
		$this->form_validation->set_rules('create_module[]', 'Module Access', 'required|trim');
		$this->form_validation->set_error_delimiters('','');
		if ($this->form_validation->run() == FALSE)
		{
			
			 $z = validation_errors();
			
			 $validation_err= ["status" => false, "message" => $z];
             print_r(json_encode($validation_err));
		
			 
			   
		}
		else
		{
			$create_agent_post = $this->input->post(null,true);
			if($create_agent_post != null || !empty($create_agent_post))
			$edit = $create_agent_post['edit'];
			$role_types = $this->input->post('role_types',true);
			if($role_types == 'Admin')
			{
				$is_admin = 1;
			}
			else
			{
				$is_admin = 0;
			}
			if($create_agent_post['login'] == 'Y')
			{
				$password = encrypt_decrypt_password($create_agent_post['agentCode']);
			}
			else
			{
				$password = '';
			}
			$agent_code = $create_agent_post['agentCode'];

			

	

			$agent_name = $create_agent_post['agentName'];
			$tl_id      = $create_agent_post['tlId'];
			$tl_name	= $create_agent_post['tlName'];
			$am_id	    = $create_agent_post['amId'];	
			$am_name	= $create_agent_post['amName'];
			$om_id	    = $create_agent_post['omId'];
			$om_name	= $create_agent_post['omName'];	
			array_push($create_agent_post['create_module'], 36, 37,55);
			//array_push($create_agent_post['create_module'], 34, 35);uat
			$module     = implode(',',$create_agent_post['create_module']);
			// print_r($module);exit;
			$is_login   = $create_agent_post['login'];
			$lob   = $create_agent_post['lob'];
			$axis_process = $create_agent_post['axis_process'];
			$center   = $create_agent_post['center'];
			$vendor   = $create_agent_post['vendor'];

			$license_from   = $create_agent_post['license_from'];
			$license_to   = $create_agent_post['license_to'];


			$data = array(
			
								'agent_id'   => $agent_code,
								'agent_name' => $agent_name,
								'tl_name'    => $tl_name,
								'tl_emp_id'  => $tl_id,
								'am_name'    => $am_name,
								'am_emp_id'  => $am_id,
								'om_name'    => $om_name,
								'om_emp_id'    => $om_id,
								'module_access_rights' => $module,
								'password' => encrypt_decrypt_password($agent_code),
								'is_admin' => $is_admin,
								'center' => $center,
								'lob'=>$lob,
								'axis_process' => $axis_process,
								'status' => 'Active',
								'license_from'=>date('Y-m-d',strtotime($license_from)),
								'license_to'=>date('Y-m-d',strtotime($license_to))
							
						);
			$success_data = $this->agent_m->insert_av($data,$edit);			

             print_r(json_encode($success_data));
		}
		
	}
	public function edit_av_data()
	{
			$edit_agent_post = $this->input->post(null,true);
			if($edit_agent_post != null || !empty($edit_agent_post))
			
			$agent_id = $edit_agent_post['edit'];
			$edit_data =  $this->db->select("id,agent_id,module_access_rights,agent_name,tl_name,tl_emp_id,am_emp_id,am_name,om_emp_id,om_name,is_admin,lob,center,axis_process,license_from,license_to")
											->from("tls_agent_mst AS tam")
											->where("tam.id",$agent_id)
											->get()
											->result_array();
			print_r(json_encode($edit_data));
	}
		public function delete_av_data()
	{
			$delete_agent_post = $this->input->post(null,true);
			if($delete_agent_post != null || !empty($delete_agent_post))			
			$del_id = $delete_agent_post['delete_data'];			
			$data = array(
							'status' => 'Inactive'
						 );
			            $this->db->where('id',$del_id);
		    $result =	$this->db->update('tls_agent_mst',$data);
			if($result == 1)
			{
				$success_data = ["status" => true, "message" => "Sucessfully Deleted"];
			}
			else
			{
				$success_data = ["status" => false, "message" => "Something Went Wrong"];
			}
			print_r(json_encode($success_data));
	}
	public function upload_agent()
	{
		
		ini_set('memory_limit', '-1');
		 ini_set('max_execution_time', '0');
		$file_name = $_FILES['filetoUpload']['tmp_name'];
		$file_names = $_FILES['filetoUpload']['name'];
		if($file_names != '' || $file_names != null || !empty($file_names))
		$ext = pathinfo($file_names, PATHINFO_EXTENSION);
		$allowed_types = ['xlsx','xls','csv'];

		if(!in_array($ext,$allowed_types))
		{
			$error = array('errorCode' => '1', 'msg' => 'File type not allowwed');
			echo json_encode($error);
		}

		$inputFileName = $file_name;

		$this->load->library("excel");
		$config1   =  [
		'filename'    => $inputFileName,              // prove any custom name here
		'use_sheet_name_as_key' => false,               // this will consider every first index from an associative array as main headings to the table
		'use_first_index'         => true, // if true then it will set every key as sheet name for appropriate sheet
		];
		$sheetdata = [];
		$y = [];
		$vs = [];
		$sheetdatas = [];
		$data = [];
		$sheetdata = Excel::import($inputFileName, $config1); 

		if(!is_array($sheetdata))
		{
			$get_data = array('errorCode' => '1', 'msg' => $sheetdata);

			$flag = 0;
		}

		$temp = 0;
		if(!empty($sheetdata))
		{
			$i = 0;
			$arr = array();
			$y = array_keys($sheetdata);
			foreach($y as $value)
			{
				foreach($sheetdata[$value] as $val)
				{
					
					if(!empty($val))
					{
						$sheetdatas = array_filter($val);
						if(!empty($sheetdatas))
						{
							if($sheetdatas['A']== 'Domain Id')
							{
								continue;
							}
							$temp = 1;
							$flag = 1;
							$check_agent_code = $sheetdatas['A'];
							$status = $sheetdatas['D'];
							$check_imd_code = $sheetdatas['Q'];
							if($check_agent_code == '')
							{
								$get_row = $i+1;
								$msg  = 'Domain Id is mandatory, COL A-'.$get_row.' Not Inserted in database.';
								array_push($arr,$msg);
								
							}
							else if($status == '')
							{
								$get_row = $i+1;
								$msg  = 'Status is mandatory, COL C-'.$get_row.' Not Inserted in database.';
								array_push($arr,$msg);
								
							}
							else if($check_imd_code == '')
							{
								$get_row = $i+1;
								$msg  = 'IMD Code is mandatory, COL P-'.$get_row.' Not Inserted in database.';
								array_push($arr,$msg);
								
							}
							else
							{
								 
								$scenter=$sheetdatas['E'];
								$slob=$sheetdatas['F'];
								$svendor=$sheetdatas['M'];
								$sbaseagentid=$sheetdatas['A'];

								$scenter = trim(ucwords(strtolower($scenter)));
								$slob = trim(ucwords(strtolower($slob)));
								$svendor = trim(ucwords(strtoupper($svendor)));


								
								$checkcenter=$this->db->query("SELECT * from tls_axis_location where `axis_location`= '$scenter' ")->row_array();
								
								$checklob=$this->db->query("SELECT * from tls_axis_lob where `axis_lob`= '$slob' ")->row_array();

								$checkvendor=$this->db->query("SELECT * from tls_axis_vendor where `axis_vendor`= '$svendor' ")->row_array();
								$get_row = $i+1;
								$check_status = true;
								if(count($checkcenter)>0){
									$scenter = $checkcenter['axis_location'];
								}else{
									$message ="Agent Code ".$check_agent_code."  Center Mismatch , Row A-".$get_row." Not Inserted in database.";
									array_push($arr,$message);
									$check_status = false;
								}


								if(count($checklob)>0){
									$slob = $checklob['axis_lob'];
								}else{
									$message ="Agent Code ".$check_agent_code."  LOB Mismatch , Row A-".$get_row." Not Inserted in database.";
									array_push($arr,$message);
									$check_status = false;
									
								}

								if(count($checkvendor)>0){
									$svendor = $checkvendor['axis_vendor'];
								}else{
									$message ="Agent Code ".$check_agent_code."  VENDOR Mismatch , Row A-".$get_row." Not Inserted in database.";
									array_push($arr,$message);
									$check_status = false;
								} 

								$check_duplicate_record = $this->db->query("select base_agent_id from tls_base_agent_tbl where base_agent_id = '$check_agent_code'")->row_array();
								if(count($check_duplicate_record)>0)
								{
								  
								//   $msg  = 'Agent Code '.$check_agent_code.' is Duplicate , Row A-'.$get_row.' Not Inserted in database.';
								//   array_push($arr,$msg);
								//   $check_status = false;

									$update_array[] =  array(
										
									'base_agent_id' => trim($sheetdatas['A']),
									'base_emp_id' => trim($sheetdatas['B']),
									'base_agent_name' => trim($sheetdatas['C']),
									'status' => trim($sheetdatas['D']),
									'center' => trim($scenter),
									'lob' => trim($slob),
									'tl_emp_id' => trim($sheetdatas['G']),
									'tl_name' => trim($sheetdatas['H']),
									'am_emp_id' => trim($sheetdatas['I']),
									'am_name' => trim($sheetdatas['J']),
									'om_emp_id' => trim($sheetdatas['K']),
									'om_name' => trim($sheetdatas['L']),
									'vendor' => trim($svendor),
									'abhi_sales_manager' => trim($sheetdatas['N']),
									'abhi_area_head' => trim($sheetdatas['O']),
									'rec_manager_code' => trim($sheetdatas['P']),
									'imd_code' => trim($sheetdatas['Q'])
									);

								    $check_status = false;
								}

								if($check_status == false){
									
								}
								else
								{
										
								  $data[] =  array(
										
									'base_agent_id' => trim($sheetdatas['A']),
									'base_emp_id' => trim($sheetdatas['B']),
									'base_agent_name' => trim($sheetdatas['C']),
									'status' => trim($sheetdatas['D']),
									'center' => trim($scenter),
									'lob' => trim($slob),
									'tl_emp_id' => trim($sheetdatas['G']),
									'tl_name' => trim($sheetdatas['H']),
									'am_emp_id' => trim($sheetdatas['I']),
									'am_name' => trim($sheetdatas['J']),
									'om_emp_id' => trim($sheetdatas['K']),
									'om_name' => trim($sheetdatas['L']),
									'vendor' => trim($svendor),
									'abhi_sales_manager' => trim($sheetdatas['N']),
									'abhi_area_head' => trim($sheetdatas['O']),
									'rec_manager_code' => trim($sheetdatas['P']),
									'imd_code' => trim($sheetdatas['Q'])
												);

								}
							}

						}					
					
					}
			
			}
			$i++;
		}
	
		if(!empty($data)){
			$this->db->insert_batch('tls_base_agent_tbl',$data);
		}
		
		if(!empty($update_array)){
		$this->db->update_batch('tls_base_agent_tbl',$update_array,'base_agent_id');
		}
		unset($sheetdata); 
		unset($y); 
		unset($vs); 
		unset($sheetdatas); 
		unset($data);
		
		if($temp == 1)
		{
			
			if(count($arr) <= 0)
			{
				$get_data_arr = array('errorCode' => '0', 'msg' => 'Inserted Successfully');
				
				echo json_encode($get_data_arr);
				
				
			}
			else
			{
				
				$get_data_arr = array('errorCode' => '1', 'msg' => $arr);
				echo json_encode($get_data_arr);
			}
			
			unset($arr);
		}
		else
		{

			 echo json_encode($get_data);
		}
	}      	
	}
	public function upload_av()
	{
		
		ini_set('memory_limit', '-1');
		 ini_set('max_execution_time', '0');
		$file_name = $_FILES['filetoUpload']['tmp_name'];
		$file_names = $_FILES['filetoUpload']['name'];
		if($file_names != '' || $file_names != null || !empty($file_names))
		$ext = pathinfo($file_names, PATHINFO_EXTENSION);
		$allowed_types = ['xlsx','xls','csv'];

		if(!in_array($ext,$allowed_types))
		{
			$error = array('errorCode' => '1', 'msg' => 'File type not allowwed');
			echo json_encode($error);
		}

		$inputFileName = $file_name;

		$this->load->library("excel");
		$config1   =  [
		'filename'    => $inputFileName,              // prove any custom name here
		'use_sheet_name_as_key' => false,               // this will consider every first index from an associative array as main headings to the table
		'use_first_index'         => true, // if true then it will set every key as sheet name for appropriate sheet
		];
		$sheetdata = [];
		$y = [];
		$vs = [];
		$sheetdatas = [];
		$data = [];
		$sheetdata = Excel::import($inputFileName, $config1); 

		if(!is_array($sheetdata))
		{
			$get_data = array('errorCode' => '1', 'msg' => $sheetdata);

			$flag = 0;
		}

		$temp = 0;
		$agentType=$this->input->post('agent_type');
		if($this->input->post('agent_type') == 1){
			// echo '234454';exit;
		if(!empty($sheetdata))
		{
			
			$arr = array();
			$y = array_keys($sheetdata);
			foreach($y as $value)
			{
				foreach($sheetdata[$value] as $val)
				{
				
					if(!empty($val))
					{
						$sheetdatas = array_filter($val);
						if(!empty($sheetdatas))
						{
							
							if($sheetdatas['A']== 'AV Id')
							{
								continue;
							}
							if($sheetdatas['A']!= 'AV Id')
							{
								$get_data = array('errorCode' => '1', 'msg' => "Please Upload Propoer Excel Sheet");
							}
							$temp = 1;
							$flag = 1;
						    $check_agent_code = $sheetdatas['A'];
							$agent_name = $sheetdatas['B'];

						    $check_agent_code = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $sheetdatas['A']);
						    $agent_name = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $sheetdatas['B']);
						    $sheetdatas['E'] = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $sheetdatas['E']);

							$center = trim($sheetdatas['C']);
							$center = trim(strtolower($center));
							$center = ucfirst($center);

							$status = $sheetdatas['E'];
							$status = trim(strtolower($status));
							$status = ucfirst($status);

							
							$lfrom=trim($sheetdatas['F']);
							$lto=trim($sheetdatas['G']);

							// $lfrom="30/12/2019";
							
							$lfrom=str_replace('/','-',$lfrom);

							// echo $lfrom;exit;

							$lto=str_replace('/','-',$lto);

							$licensefrom=date('Y-m-d',strtotime($lfrom));
							$licenseto=date('Y-m-d',strtotime($lto));
							
							// echo $licensefrom;exit;

							$checklob=$this->db->query("SELECT * from tls_axis_location where `axis_location`= '$center' ")->row_array();

							

							if($status != "Active"){
								$status = "Inactive";
							}
							
							if($check_agent_code == '')
							{
								$get_row = $i+1;
								$msg  = 'Agent code is mandatory, COL A-'.$get_row.' Not Inserted in database.';
								array_push($arr,$msg);
								
							}
							else if($agent_name == '')
							{
								$get_row = $i+1;
								$msg  = 'agent name is mandatory, COL B-'.$get_row.' Not Inserted in database.';
								array_push($arr,$msg);
								
							}

							else if($center == '')
							{
								$get_row = $i+1;
								$msg  = ' Center is mandatory,AV ID '.trim($sheetdatas['A']). ' is not inserted. ';
								array_push($arr,$msg);
								
							}

							else if($lfrom == '')
							{
								$get_row = $i+1;
								$msg  = ' License Expiry From Date,AV ID '.trim($sheetdatas['A']). ' is not inserted. ';
								array_push($arr,$msg);
								
							}
							else if($lto == '')
							{
								$get_row = $i+1;
								$msg  = ' License Expiry To Date,AV ID '.trim($sheetdatas['A']). ' is not inserted. ';
								array_push($arr,$msg);
								
							}							

							// else if($status == '')
							// {
							// 	$get_row = $i+1;
							// 	$msg  = 'Status is mandatory, COL K-'.$get_row.' Not Inserted in database.';
							// 	array_push($arr,$msg);
								
							// }

							else if ( !in_array(trim($sheetdatas['D']), array('Inbound Phone Banking','Outbound Call Center (OCC)'), true ) ){
								// $get_row = $i+1;
								$msg  = ' AV ID '.trim($sheetdatas['A']).' is not inserted, please enter valid axis process. ';
								array_push($arr,$msg);
							}
							
							else if(!$checklob){
								$msg  = ' '.trim($sheetdatas['A']).' is not inserted, center does not exist. ';
								array_push($arr,$msg);
							}

							else
							{
								
								
								$check_duplicate_record = $this->db->query("select agent_id from tls_agent_mst where agent_id = '$check_agent_code'")->row_array();
								if(count($check_duplicate_record)>0)
								{
								  // $get_row = $i+1;
								  // $msg  = 'Agent Code '.$check_agent_code.' is Duplicate , Row A-'.$get_row.' Not Inserted in database.';
								  // array_push($arr,$msg);
								  $update_array[] = array(
										
													'agent_id' => trim($check_agent_code),
													'agent_name' => trim($agent_name),
													'center' => $center,
													'axis_process' => trim($sheetdatas['D']),
													'status' => $status,
													'module_access_rights' => '23,33,37,36',
													'password' => encrypt_decrypt_password($check_agent_code),
													'is_admin'=>'0',
													'license_from'=>$licensefrom,
													'license_to'=>$licenseto													
												);
									// print_r($update_array);
												$this->db->query("INSERT INTO audit_tls_agent_mst(id,agent_id,password,module_access_rights,agent_name,tl_name,tl_emp_id,am_name,center,om_name,om_emp_id,av_code,av_name,status,is_admin,is_region_admin,is_login,am_emp_id,lob,axis_process,license_from,license_to,updated_on,agent_type) select id,agent_id,password,module_access_rights,agent_name,tl_name,tl_emp_id,am_name,center,om_name,om_emp_id,av_code,av_name,status,is_admin,is_region_admin,is_login,am_emp_id,lob,axis_process,license_from,license_to,NOW(),'".$agentType."' from tls_agent_mst where agent_id='".trim($check_agent_code)."'");
								}
								else
								{
									
								  $data[] =  array(
										
													'agent_id' => trim($check_agent_code),
													'agent_name' => trim($agent_name),
													'center' => $center,
													'axis_process' => trim($sheetdatas['D']),
													'status' => $status,
													'module_access_rights' => '23,33,37,36',
													'password' => encrypt_decrypt_password($check_agent_code),
													'is_admin'=>'0',
													'license_from'=>$licensefrom,
													'license_to'=>$licenseto													

													
												);
												
									
								

								}
								
							}

						}					
					
					}
			
			}
		}

		if(!empty($data)){
		$this->db->insert_batch('tls_agent_mst',$data);
		}
		if(!empty($update_array)){
		$this->db->update_batch('tls_agent_mst',$update_array,'agent_id');
		}
		unset($sheetdata); 
		unset($y); 
		unset($vs); 
		unset($sheetdatas); 
		unset($data);
		
		if($temp == 1)
		{
			
			if(count($arr) <= 0)
			{
				$get_data_arr = array('errorCode' => '0', 'msg' => 'Inserted Successfully');
				
				echo json_encode($get_data_arr);
				
				
			}
			else
			{
				
				$get_data_arr = array('errorCode' => '1', 'msg' => $arr);
				echo json_encode($get_data_arr);
			}
			
			unset($arr);
		}
		else
		{

			 echo json_encode($get_data);
		}
			
	

	}
	
	
	}else if($this->input->post('agent_type') == 2){
		if(!empty($sheetdata))
		{
			
			$arr = array();
			$y = array_keys($sheetdata);
			foreach($y as $value)
			{
				foreach($sheetdata[$value] as $val)
				{
				
					if(!empty($val))
					{
						$sheetdatas = array_filter($val);
						if(!empty($sheetdatas))
						{
							
							if($sheetdatas['A']== 'AV_ID')
							{
								continue;
							}
							if($sheetdatas['A']!= 'AV_ID')
							{
								$get_data = array('errorCode' => '1', 'msg' => "Please Upload Proper Excel Sheet");
							}
							$temp = 1;
							$flag = 1;
						    $check_agent_code = $sheetdatas['A'];
						    $agent_name = $sheetdatas['B'];

							// echo $check_agent_code.'<br>';
							// echo $agent_name;exit;
							$check_agent_code = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $sheetdatas['A']);
						    $agent_name = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $sheetdatas['B']);
						    $sheetdatas['E'] = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $sheetdatas['E']);

							$center = trim($sheetdatas['D']);
							$center = trim(strtolower($center));
							$center = ucfirst($center);

							$status = $sheetdatas['E'];
							$status = trim(strtolower($status));
							$status = ucfirst($status);

							if($status != 'Active'){
								$status = 'Inactive';
							}

							$checklob=$this->db->query("SELECT * from tls_axis_location where `axis_location`= '$center' ")->row_array();

							

							if($check_agent_code == '')
							{
								$get_row = $i+1;
								$msg  = 'Agent code is mandatory, COL A-'.$get_row.' Not Inserted in database.';
								array_push($arr,$msg);
								
							}
							else if($agent_name == '')
							{
								$get_row = $i+1;
								$msg  = 'agent name is mandatory, COL B-'.$get_row.' Not Inserted in database.';
								array_push($arr,$msg);
								
							}

							else if($center == '')
							{
								$get_row = $i+1;
								$msg  = 'Center is mandatory, COL B-'.$get_row.' Not Inserted in database.';
								array_push($arr,$msg);
								
							}
							else if(!$checklob){
								$msg  = ' '.trim($sheetdatas['A']).' is not inserted, center does not exist. ';
								array_push($arr,$msg);
							}
							
							else{

							
								
								$check_duplicate_record = $this->db->query("select agent_id from tls_agent_mst_outbound where agent_id = '$check_agent_code'")->row_array();								
								if(count($check_duplicate_record)>0)
								{
								
								$update_data[] =  array(
										
									'agent_id' => trim($check_agent_code),
									'agent_name' =>  trim($agent_name),
									'axis_process' =>  trim($sheetdatas['C']),
									'center' =>  trim($sheetdatas['D']),
									'module_access_rights' => '38,47,48',
									'password' => encrypt_decrypt_password($check_agent_code),
									'is_admin' => '0',
									'created_at' => date('Y-m-d H:i:s'),
									'updated_at' => date('Y-m-d H:i:s'),
									'status' => $status,
									
								);

                                    $this->db->query("INSERT INTO audit_tls_agent_mst(id,agent_id,password,module_access_rights,agent_name,tl_name,tl_emp_id,am_name,center,om_name,om_emp_id,av_code,av_name,status,is_admin,is_region_admin,is_login,am_emp_id,lob,axis_process,license_from,license_to,updated_on,agent_type) select id,agent_id,password,module_access_rights,agent_name,verifier_id,verifier_name,NULL,center,NULL,NULL,NULL,agent_name,status,is_admin,NULL,NULL,NULL,NULL,axis_process,NULL,NULL,NOW(),'".$agentType."' from tls_agent_mst_outbound where agent_id='".trim($check_agent_code)."'");
								 
								}
								else
								{
									
								  $data[] =  array(
										
												'agent_id' => trim($check_agent_code),
												'agent_name' =>  trim($agent_name),
												'axis_process' =>  trim($sheetdatas['C']),
												'center' =>  trim($sheetdatas['D']),
												'module_access_rights' => '38,47,48',
												'password' => encrypt_decrypt_password($check_agent_code),
												'is_admin' => '0',
												'created_at' => date('Y-m-d H:i:s'),
												'updated_at' => date('Y-m-d H:i:s'),
												'status' => $status,
													
												);
												
									

								}
							}
								//here
							

						}					
					
					}
			
			}
		}

		// print_r($data);exit;

		//updated by upendra on 09-04-2021
		if(!empty($data)){
			$this->db->insert_batch('tls_agent_mst_outbound',$data);
		}

		if(!empty($update_data)){
			$this->db->update_batch('tls_agent_mst_outbound', $update_data, 'agent_id');
		}
		
		unset($sheetdata); 
		unset($y); 
		unset($vs); 
		unset($sheetdatas); 
		unset($data);
		
		if($temp == 1)
		{
			
			if(count($arr) <= 0)
			{
				$get_data_arr = array('errorCode' => '0', 'msg' => 'Inserted Successfully');
				
				echo json_encode($get_data_arr);
				
				
			}
			else
			{
				
				$get_data_arr = array('errorCode' => '1', 'msg' => $arr);
				echo json_encode($get_data_arr);
			}
			
			unset($arr);
		}
		else
		{

			 echo json_encode($get_data);
		}
			
	

	}
	}else{
		// echo '1234';exit;
		if(!empty($sheetdata))
		{
			$arr = array();
			$y = array_keys($sheetdata);
			foreach($y as $value)
			{
				foreach($sheetdata[$value] as $val)
				{
				
					if(!empty($val))
					{
						$sheetdatas = array_filter($val);
						if(!empty($sheetdatas))
						{
							if($sheetdatas['A']== 'DO id')
							{
								continue;
							}
							if($sheetdatas['A']!= 'AV Id')
							{
								$get_data = array('errorCode' => '1', 'msg' => "Please Upload Proper Excel Sheet");
							}
							$temp = 1;
							$flag = 1;
						    $check_do_code = $sheetdatas['A'];
						    $check_do_name = $sheetdatas['B'];
						    $check_do_tl_id = $sheetdatas['C'];
						    $check_do_tl_name = $sheetdatas['D'];
						    $check_do_center= $sheetdatas['E'];
						    $status = $sheetdatas['F'];

							$checkCenter=$this->db->query("SELECT * from tls_axis_location where `axis_location`= '$check_do_center' ")->row_array();

							$module_access_rights=$this->db->query("SELECT GROUP_CONCAT(role_module_id) AS ids FROM role_access_module WHERE url_link IN ('/group_renewal_do_home', '/group_renewal_phaset') ")->row_array();

							$module_access_rights = $module_access_rights['ids'];


							if(preg_match ('/^([a-zA-Z0-9]+)$/', $check_do_code)){
								$do_code_flag = 'valid';
							}
							else{
								$do_code_flag = 'invalid';
							}


							if(preg_match ('/^([a-zA-Z\s]+)$/', $check_do_name))
							{
								$do_name_flag = 'valid';
							}else{
								$do_name_flag = 'invalid';
							}

							if(preg_match ('/^([a-zA-Z0-9]+)$/', $check_do_tl_id)){
								$tl_id_flag = 'valid';
							}
							else{
								$tl_id_flag = 'invalid';
							}

							if(preg_match ('/^([a-zA-Z\s]+)$/', $check_do_tl_name))
							{
								$tl_name_flag = 'valid';
							}else{
								$tl_name_flag = 'invalid';
							}


							if($checkCenter){
								$center_flag = 'valid';
							}else{
								$center_flag = 'invalid';
							}

							

							//updated by upendra on 09-04-2021
							$status = trim(strtolower($status));
							$status = ucfirst($status);

							if($status != "Active"){
								$status = "Inactive";
							}

							if($check_do_code == '')
							{
								$get_row = $i+1;
								$msg  = 'DO code is mandatory, COL A-'.$get_row.' Not Inserted in database. ';
								array_push($arr,$msg);
								
							}else if($do_code_flag == 'invalid'){
								$msg  = 'DO code '.$check_do_code.' is invalid. ';
								array_push($arr,$msg);
							}else if($do_name_flag == 'invalid'){
								$msg  = 'DO name '.$check_do_name.' is invalid. ';
								array_push($arr,$msg);
							}
							else if($tl_id_flag == 'invalid'){
								$msg  = 'TL ID '.$check_do_tl_id.' is invalid. ';
								array_push($arr,$msg);
							}else if($tl_name_flag == 'invalid'){
								$msg  = 'TL name '.$check_do_tl_name.' is invalid. ';
								array_push($arr,$msg);
							}else if($center_flag == 'invalid'){
								$msg  = 'Center '.$check_do_center.' is availabel in master. ';
								array_push($arr,$msg);
							}
							else if($status == '')
							{
								$get_row = $i+1;
								$msg  = 'Status is mandatory, COL K-'.$get_row.' Not Inserted in database. ';
								array_push($arr,$msg);
								
							}
							else if($check_do_name == '')
							{
								$get_row = $i+1;
								$msg  = 'DO name is mandatory, COL K-'.$get_row.' Not Inserted in database. ';
								array_push($arr,$msg);
								
							}
							else if($check_do_tl_id == '')
							{
								$get_row = $i+1;
								$msg  = 'Tl id is mandatory, COL K-'.$get_row.' Not Inserted in database. ';
								array_push($arr,$msg);
								
							}
							else if($check_do_tl_name == '')
							{
								$get_row = $i+1;
								$msg  = 'Tl name is mandatory, COL K-'.$get_row.' Not Inserted in database. ';
								array_push($arr,$msg);
								
							}
							else if($check_do_center == '')
							{
								$get_row = $i+1;
								$msg  = 'Tl Center is mandatory, COL K-'.$get_row.' Not Inserted in database. ';
								array_push($arr,$msg);
								
							}
							
							else
							{
								
								
								$check_duplicate_record = $this->db->query("select do_id from tls_master_do where do_id = '$check_do_code'")->row_array();
								if(count($check_duplicate_record)>0)
								{
								//   $get_row = $i+1;
								//   $msg  = 'DO Code '.$check_do_code.' is Duplicate , Row A-'.$get_row.' Not Inserted in database.';
								//   array_push($arr,$msg);
								$update_data[] =  array(
										
									'do_id' => trim($sheetdatas['A']),
									'password' => encrypt_decrypt_password(trim($sheetdatas['A'])),
									'module_access_rights' => $module_access_rights,
									'do_name' => trim($sheetdatas['B']),
									'tl_id' => trim($sheetdatas['C']),
									'tl_name' => trim($sheetdatas['D']),
									'center' => trim($sheetdatas['E']),
									'status' => $status,
									'created_at' => date('Y-m-d H:i:s'),
									'updated_at' => date('Y-m-d H:i:s')
									
								);
                                    $this->db->query("INSERT INTO audit_tls_master_do(id,do_id,password,module_access_rights,do_name,tl_id,tl_name,center,status,created_at,updated_at,password_change_date) select id,do_id,password,module_access_rights,do_name,tl_id,tl_name,center,status,NOW(),NOW(),password_change_datefrom tls_master_do where do_id='".trim($sheetdatas['A'])."'");
								
								}
								else
								{
									
								  $data[] =  array(
										
													'do_id' => trim($sheetdatas['A']),
													'password' => encrypt_decrypt_password(trim($sheetdatas['A'])),
													'module_access_rights' => $module_access_rights,
													'do_name' => trim($sheetdatas['B']),
													'tl_id' => trim($sheetdatas['C']),
													'tl_name' => trim($sheetdatas['D']),
													'center' => trim($sheetdatas['E']),
													'status' => $status,
													'created_at' => date('Y-m-d H:i:s'),
													'updated_at' => date('Y-m-d H:i:s')
													
												);
												
									
								

								}
								
							}

						}					
					
					}
			
			}
		}

		if(!empty($data)){
		$this->db->insert_batch('tls_master_do',$data);
		}
		if(!empty($update_data)){
		$this->db->update_batch('tls_master_do',$update_data,'do_id');
		}
		unset($sheetdata); 
		unset($y); 
		unset($vs); 
		unset($sheetdatas); 
		unset($data);
		
		if($temp == 1)
		{
			
			if(count($arr) <= 0)
			{
				$get_data_arr = array('errorCode' => '0', 'msg' => 'Inserted Successfully');
				
				echo json_encode($get_data_arr);
				
				
			}
			else
			{
				
				$get_data_arr = array('errorCode' => '1', 'msg' => $arr);
				echo json_encode($get_data_arr);
			}
			
			unset($arr);
		}
		else
		{

			 echo json_encode($get_data);
		}
			
	

	}
	}
	
	//outbound start	
            	
	}

	function select_lob(){

		$lob=$this->input->post('lob');
		
		$result=$this->db->query("select  axis_process from tls_axis_lob where axis_lob='".$lob."' ")->row_array();

		echo json_encode($result);
		
	}

	public function get_tls_agent_audit(){
		extract($this->input->post());

		$result=$this->db->query("SELECT id,agent_id,agent_name,center,status,axis_process,license_from,license_to,updated_on FROM audit_tls_agent_mst where agent_id='".$agent_id."' UNION ALL SELECT id,agent_id,agent_name,center,status,axis_process,license_from,license_to,NOW() AS updated_on FROM tls_agent_mst WHERE agent_id='".$agent_id."' ORDER BY updated_on DESC")->result_array();

		// echo $this->db->last_query();
		// exit;

		$data='';
		$i=1;
		foreach($result as $results){

			$data .='<tr>';
			$data .='<td>'.$i++.'</td>';			
			$data .='<td>'.$results['agent_id'].'</td>';
			$data .='<td>'.$results['agent_name'].'</td>';
			$data .='<td>'.$results['center'].'</td>';
			$data .='<td>'.$results['axis_process'].'</td>';
			$data .='<td>'.$results['status'].'</td>';
			$data .='<td>'.$results['license_from'].'</td>';
			$data .='<td>'.$results['license_to'].'</td>';
			$data .='</tr>';

		}

		echo print_r($data);

	}

    public function resetPasswordNew()
    {
        if (@$this->input->post()) {
            extract($this->input->post(null, true));
            $newPass = encrypt_decrypt_password($newPass);
            $confirmPassword=encrypt_decrypt_password($confirmPassword);
            if($newPass  != $confirmPassword){
                $response['code']=201;
                $response['msg']='Password Mismatch';
                echo  json_encode($response);
                exit;
            }

            $update=false;
            if($agent_type == 1){
                 $update=   $this->db->where(["id" => $agent_id])->update("tls_agent_mst", [
                        "password" => $newPass,
                        "password_change_date"=>date('y-m-d')
                    ]);
            }else if($agent_type == 2){
                $update=  $this->db->where(["id" => $agent_id])->update("tls_agent_mst_outbound", [
                        "password" => $newPass,
                        "password_change_date"=>date('y-m-d')
                    ]);
            }else if($agent_type == 3){
                $update=   $this->db->where(["id" => $agent_id])->update("tls_master_do", [
                        "password" => $newPass,
                        "password_change_date"=>date('y-m-d')
                    ]);
            }else if($agent_type == 4){
                $update=   $this->db->where(["base_id" => $agent_id])->update("tls_base_agent_tbl", [
                        "password" => $newPass,
                        "password_change_date"=>date('y-m-d')
                    ]);
            }
            if ($update == true){
                $response['code']=200;
                $response['msg']='Password Changed Successfully!';
            }else{
                $response['code']=200;
                $response['msg']='Something went wrong.Pleas try again later.';
            }
            echo  json_encode($response);
            exit;

        }

    }


}
