<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Categories extends CI_Controller 
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('adcategorymodel','',TRUE);
		checklogin();
		$this->RolePermission = getRolePermissions();
		ini_set('upload_max_filesize', '20M');  
		ini_set('post_max_size', '25M');  
	}

	function index()
	{
		$this->load->view('template/header.php');
		$this->load->view('categories/index');
		$this->load->view('template/footer.php');
	}

	function fetch()
	{
		//print_r($_GET);
		$get_result = $this->adcategorymodel->getRecords($_GET);

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
				array_push($temp, $get_result['query_result'][$i]->category_name );
				array_push($temp, $get_result['query_result'][$i]->status );
				
				$status_type = '';
				if($get_result['query_result'][$i]->status == 'Active')
				{
					$status_type = 'Deactivate';
				}
				else
				{
					$status_type = 'Activate';
				}
				
				$status_change = '<span class="btn btn-sm" style="cursor: pointer;" onclick="changestatus('.$get_result['query_result'][$i]->category_id.');">'.$status_type.'</span>';
				
				array_push($temp, $status_change);
				
				$actionCol = "";
				//if($this->privilegeduser->hasPrivilege("CategoriesAddEdit"))
				//{
					$actionCol .='<a href="categories/addEdit?text='.rtrim(strtr(base64_encode("id=".$get_result['query_result'][$i]->category_id ), '+/', '-_'), '=').'" title="Edit"><i class="fa fa-edit"></i></a>';
				//}
				//if($this->privilegeduser->hasPrivilege("CategoryDelete")){
					//$actionCol .='&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\''.$get_result['query_result'][$i]->category_id .'\');" title="Delete"><i class="icon-remove-sign"></i></a>';
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
		$record_id = "";
		//print_r($_GET);
		if(!empty($_GET['text']) && isset($_GET['text'])){
			$varr=base64_decode(strtr($_GET['text'], '-_', '+/'));	
			parse_str($varr,$url_prams);
			$record_id = $url_prams['id'];
		}
		
		//echo $user_id;
		$result = array();
		$result['categories_details'] = $this->adcategorymodel->getFormdata($record_id);
		
		$this->load->view('template/header.php');
		$this->load->view('categories/addEdit',$result);
		$this->load->view('template/footer.php');
	}
	 
	function submitForm()
	{
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$data = array();
			$condition = "category_name= '".$_POST['category_name']."' ";
			if(isset($_POST['category_id']) && $_POST['category_id'] > 0)
			{
				$condition .= " AND  category_id != ".$_POST['category_id'];
			}
			
			$check_name = $this->adcategorymodel->checkRecord("tbl_categories",$_POST,$condition);
			
			if(!empty($check_name[0]->category_id))
			{
				echo json_encode(array("success"=>false, 'msg'=>'Record Already Present!'));
				exit;
			}
			
			$thumnail_value = "";
			if(isset($_FILES) && isset($_FILES["category_image"]["name"]))
			{
				 $config = array();
				$config['upload_path'] = DOC_ROOT_FRONT."/images/category_images/";
				$config['max_size']    = '0';
				//$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['allowed_types'] = '*';
				//$config['file_name']     = md5(uniqid("100_ID", true));
				$config['file_name']     = $_FILES["category_image"]["name"];
				
				// print_r($config);
				// exit;
				$this->load->library('upload', $config);
				if (!$this->upload->do_upload("category_image"))
				{
					$image_error = array('error' => $this->upload->display_errors());
					echo json_encode(array("success"=>false, "msg"=>$image_error['error']));
					exit;
				}
				else
				{
					$image_data = array('upload_data' => $this->upload->data());
					$thumnail_value = $image_data['upload_data']['file_name']; 
					 $config['image_library'] = 'gd2'; 
                     $config['source_image'] = DOC_ROOT_FRONT.'/images/category_images/'.$thumnail_value;  
                     $config['maintain_ratio'] = FALSE;  
                     $config['quality'] = '100%';  
                     $config['width'] = 420;  
                     $config['height'] = 560;  
                     $config['new_image'] =  DOC_ROOT_FRONT.'/images/category_images/'.$thumnail_value;  
                     $this->load->library('image_lib', $config);  
					 if (!$this->image_lib->resize()) {
						return $this->image_lib->display_errors();
					} 
					// print_r($config);
					// exit;

					

				}
				/* $config = array();
				$config['image_library'] = 'gd2';
				$config['source_image'] = DOC_ROOT_FRONT."/images/category_images/".$thumnail_value;
				//  $config['maintain_ratio'] = TRUE;
				$config['maintain_ratio'] = TRUE;  
				$config['quality'] = '30%';
				$config['new_image'] =  DOC_ROOT_FRONT."/images/category_images/".$thumnail_value;
				// $config['width']    = 640;
				// $config['height']   = 480;
				$this->load->library('image_lib', $config); 
				// $this->image_lib->resize();
				if (!$this->image_lib->resize()) {
					return $this->image_lib->display_errors();
				} */
				
				/* Unlink previous category image */
				if(!empty($_POST['category_id']))
				{
					$image = $this->adcategorymodel->getFormdata($_POST['category_id']);
					if(is_array($image) && !empty($image[0]->category_image) && file_exists(DOC_ROOT_FRONT."/images/category_images/".$image[0]->category_image))
					{
						unlink(DOC_ROOT_FRONT."/images/category_images/".$image[0]->category_image);
					}
				}
			}
			else
			{
				$thumnail_value = $_POST['input_category_image'];
			}
			
			$data['category_image'] = $thumnail_value;
			$data['category_name'] = $_POST['category_name'];
			$category_name = (str_replace(' ', '-', strtolower($_POST['category_name'])));
			$data['link'] = $category_name;
			$data['meta_title'] = $_POST['category_name'];
			$data['meta_description'] = $_POST['category_name'];
			$data['meta_keywords'] = $_POST['category_name'];
			$data['updated_on'] = date("Y-m-d H:i:s");
			$data['updated_by'] = $_SESSION["chheda_webadmin"][0]->user_id;
				
			if(!empty($_POST['category_id']))
			{
				$result = $this->adcategorymodel->updateRecord($data,$_POST['category_id']);
				
				if($result)
				{
					echo json_encode(array('success'=>true, 'msg'=>'Record Updated Successfully.'));
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
				$data['created_on'] = date("Y-m-d H:i:s");
				$data['created_by'] = $_SESSION["chheda_webadmin"][0]->user_id;
				
				$result = $this->adcategorymodel->insertData('tbl_categories',$data,'1');
				
				if(!empty($result))
				{
					echo json_encode(array('success'=>true, 'msg'=>'Record Added Successfully.'));
					exit;
				}
				else
				{
					echo json_encode(array('success'=>false, 'msg'=>'Problem while adding data.'));
					exit;
				}
			}
		}
		else
		{
			echo json_encode(array('success'=>false, 'msg'=>'Problem While Add/Edit Data.'));
			exit;
		}
	}
	
	function changestatus($category_id = "")
	{
		if(!empty($category_id))
		{
			$data = $this->adcategorymodel->getFormdata($category_id);
			$get_status= '';
			
			if(is_array($data))
			{
				$update_data = array();
				$get_status = $data[0]->status;
				if($get_status == 'Active')
				{
					$update_data = array('status'=>'In-active');
				}
				else
				{
					$update_data = array('status'=>'Active');
				}
				
				$res = $this->adcategorymodel->updateRecord($update_data,$category_id);
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
	
	function deleteImage()
	{
		$category_id = (int)$_POST['category_id'];
		
		if(!empty($category_id))
		{
			$this -> db -> select('category_image');
			$this -> db -> from('tbl_categories');
			$this -> db -> where('category_id', $category_id);
			$query = $this -> db -> get();
		   
			if($query -> num_rows() >= 1)
			{
				$result = $query->result();
				if(!empty($result[0]->category_image))
				{
					$img_path = DOC_ROOT_FRONT."/images/category_images/".$result[0]->category_image;
					// echo $img_path;
					// exit;
					if(is_file($img_path))
					{
						unlink($img_path);
					}			
					
					$update_data['category_image'] = "";
					if($this->adcategorymodel->updateRecord($update_data,$category_id))
					{
						echo json_encode(array('success' => true,'msg' => 'Image is deleted'));
						exit;
					}
					else
					{
						echo json_encode(array('success' => false,'msg' => 'Problem while deleting image.'));
						exit;
					}
				}	
			}
			else
			{
				echo json_encode(array('success' => false,'msg' => 'Problem while deleting image.'));
				exit;
			}
		}
		else
		{
			echo json_encode(array('success' => false,'msg' => 'Problem while deleting image.'));
			exit;
		}	
	}
	
	//For Delete
	private function set_upload_options()
	{   
		//upload an image options //products
		$config = array();
		$config['upload_path'] = "admin/category_image";
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']      = '7097152';
		$config['overwrite']     = FALSE;

		return $config;
	}
		
	function delRecord($id)
	{
		$appdResult = $this->adcategorymodel->delrecord("tbl_categories","category_id ",$id);
		 
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
