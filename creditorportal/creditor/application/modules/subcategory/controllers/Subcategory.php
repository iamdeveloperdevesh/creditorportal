<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Subcategory extends CI_Controller 
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('adsubcategorymodel','',TRUE);
		checklogin();
		$this->RolePermission = getRolePermissions();
	}
 
	function index()
	{      
		$result['categories'] = $this->adsubcategorymodel->getData("category_id, category_name", "tbl_categories", "status = 'Active' ", "category_name", "asc");
		// echo "<pre>";
		// print_r($result);
		// exit;
		
		$this->load->view('template/header.php');
		$this->load->view('index', $result);
		$this->load->view('template/footer.php');
	}
 
	function fetch()
	{
		//print_r($_GET);
		$get_result = $this->adsubcategorymodel->getRecords($_GET);
		
		$result = array();
		$result["sEcho"]= $_GET['sEcho'];
		
		$result["iTotalRecords"] = $get_result['totalRecords'];	//iTotalRecords get no of total recors
		$result["iTotalDisplayRecords"]= $get_result['totalRecords']; //iTotalDisplayRecords for display the no of records in data table.
		
		$items = array();
		
		if(!empty($get_result['query_result']) && count($get_result['query_result']) > 0)
		{
			for($i=0;$i<sizeof($get_result['query_result']);$i++)
			{
				$temp = array();
				array_push($temp, $get_result['query_result'][$i]->category_name);
				array_push($temp, $get_result['query_result'][$i]->subcategory_name);
				array_push($temp, $get_result['query_result'][$i]->statuss);
				
				$status_type = '';
				if($get_result['query_result'][$i]->statuss == 'Active')
				{
					$status_type = 'Deactivate';
				}
				else
				{
					$status_type = 'Activate';
				}
				
				$status_change = '<span class="btn btn-sm" style="cursor: pointer;" onclick="changestatus('.$get_result['query_result'][$i]->subcategory_id.');">'.$status_type.'</span>';
				array_push($temp, $status_change);
				
				$actionCol = "";
				
				// if ($this->privilegeduser->hasPrivilege("SubCategoriesAddEdit"))
				// {
					$actionCol .='<a href="subcategory/addEdit?text='.rtrim(strtr(base64_encode("id=".$get_result['query_result'][$i]->subcategory_id), '+/', '-_'), '=').'" title="Edit"><i class="fa fa-edit"></i></a>';
				// }
				//if ($this->privilegeduser->hasPrivilege("SubCategoryDelete")){
					//$actionCol .='&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\''.$get_result['query_result'][$i]->subcategory_id.'\');" title="Delete"><i class="icon-remove-sign"></i></a>';
				//}
				
				array_push($temp, $actionCol);
				array_push($items, $temp);
			}
		}
		
		$result["aaData"] = $items;
		echo json_encode($result);
		exit;
	}
 
	function addEdit($id=NULL)
	{
		$subcategory_id = "";
		if(!empty($_GET['text']) && isset($_GET['text']))
		{
			$varr=base64_decode(strtr($_GET['text'], '-_', '+/'));	
			parse_str($varr,$url_prams);
			$subcategory_id = $url_prams['id'];
		}
		
		//echo $subcategory_id;
		// $result['categories'] = $this->adsubcategorymodel->getDropdown("tbl_categories","category_id,category_name");
		$result['categories'] = $this->adsubcategorymodel->getData("category_id, category_name", "tbl_categories", "status = 'Active' ", "category_name", "asc");
		$result['subcategory_details'] = $this->adsubcategorymodel->getFormdata($subcategory_id);
		// echo "<pre>";
		// print_r($result);
		// exit;
		$this->load->view('template/header.php');
		$this->load->view('subcategory/addEdit',$result);
		$this->load->view('template/footer.php');
	}
 
	function submitForm()
	{
		
		
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$condition = "category_id = '". $_POST['category_id']."' AND subcategory_name = '".$_POST['subcategory_name']."' ";
			if(isset($_POST['subcategory_id']) && $_POST['subcategory_id'] > 0)
			{
				$condition .= " AND  subcategory_id != ".$_POST['subcategory_id'];
			}
			
			$check_name = $this->adsubcategorymodel->checkRecord("tbl_subcategories",$_POST,$condition);
			
			if(!empty($check_name[0]->subcategory_id))
			{
				echo json_encode(array("success"=>false, 'msg'=>'Record Already Present!'));
				exit;
			}
			// print_r($_POST);
			// exit;
			$data = array();
			$data['category_id'] = $_POST['category_id'];
			$data['subcategory_name'] = $_POST['subcategory_name'];
			$subcategory_name = (str_replace(' ', '-', strtolower($_POST['subcategory_name'])));
			$data['link'] = $subcategory_name;
			$data['meta_title'] = $_POST['subcategory_name'];
			$data['meta_description'] = $_POST['subcategory_name'];
			$data['meta_keywords'] = $_POST['subcategory_name'];
			$data['updated_on'] = date("Y-m-d H:i:s");
			$data['updated_by'] = $_SESSION["chheda_webadmin"][0]->user_id;
			
			if(!empty($_POST['sub_category_id']))
			{
				//update
				$result = $this->adsubcategorymodel->updateRecord($data,$_POST['sub_category_id']);	
				if($result)
				{
					echo json_encode(array('success'=>true, 'msg'=>'Record Updated Successfully'));
					exit;
				}
				else
				{
					echo json_encode(array('success'=>false, 'msg'=>'Problem while updating data.'));
					exit;
				}
			}
			else
			{
				//add
				$data['created_on'] = date("Y-m-d H:i:s");
				$data['created_by'] = $_SESSION["chheda_webadmin"][0]->user_id;
				
				$result = $this->adsubcategorymodel->insertData('tbl_subcategories',$data,'1');
				
				if(!empty($result))
				{
					echo json_encode(array("success"=>true, 'msg'=>'Record added Successfully.'));
					exit;
				}
				else
				{
					echo json_encode(array("success"=>false, 'msg'=>'Problem while adding data.'));
					exit;
				}
			}
		}
		else
		{
			echo json_encode(array("success"=>false, 'msg'=>'Problem while add/edit data.'));
			exit;
		}
	}

	function changestatus($subcategory_id = "")
	{
		if(!empty($subcategory_id))
		{
			$data = $this->adsubcategorymodel->getFormdata($subcategory_id);
			//echo '<pre>';
			//print_r($data);
			//exit();
			$get_status= '';
			if(is_array($data))
			{
				$update_data  = array();
				$get_status = $data[0]->status;
				if($get_status == 'Active')
				{
					$update_data = array('status'=>'In-active');
				}
				else
				{
					$update_data = array('status'=>'Active');
				}
				
				$res = $this->adsubcategorymodel->updateRecord($update_data,$subcategory_id);
				if($res)
				{
					echo json_encode(array('success'=>true));
					exit;
				}
				else
				{
					echo json_encode(array('success'=>false));
					exit;
				}	
			}
			else
			{
				echo json_encode(array('success'=>false));
				exit;
			}
		}
		else
		{
			echo json_encode(array('success'=>false));
			exit;
		}
	}
	
	//For Delete
	function delRecord($id)
	{
		$appdResult = $this->adsubcategorymodel->delrecord("tbl_subcategories","subcategory_id",$id);
		 
		if($appdResult)
		{
			echo "1";
		}
		else
		{
			echo "2";	
				 
		}	
	}
}

?>
