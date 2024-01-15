<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Enrollmentforms extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		checklogin();
		$this->RolePermission = getRolePermissions();
	}
 
	function index()
	{
		$this->load->view('template/header.php');
		$this->load->view('enrollmentforms/index');
		$this->load->view('template/footer.php');
	}
 
	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		//echo "<pre>GET ";print_r($_GET);exit;
		$dataListing = curlFunction(SERVICE_URL.'/api/enrollmentformListing',$_GET);
		$dataListing = json_decode($dataListing, true);
		//echo "<pre>";print_r($dataListing);exit;
		if($dataListing['status_code'] == '401'){
			//echo "in condition";
			redirect('login');
			exit();
		}
		
		$result = array();
		$result["sEcho"]= $_GET['sEcho'];

		$result["iTotalRecords"] = $dataListing['Data']['totalRecords'];	//iTotalRecords get no of total recors
		$result["iTotalDisplayRecords"]= $dataListing['Data']['totalRecords']; //iTotalDisplayRecords for display the no of records in data table.

		$items = array();
		
		if(!empty($dataListing['Data']['query_result']) && count($dataListing['Data']['query_result']) > 0)
		{
			for($i=0;$i<sizeof($dataListing['Data']['query_result']);$i++)
			{
				$temp = array();
				array_push($temp, $dataListing['Data']['query_result'][$i]['form_title'] );
				
				$actionCol = "";
				if($_SESSION['webpanel']['role_id'] == 1){
				if(in_array('EnrollmentFormEdit',$this->RolePermission)){
					$actionCol .='<a href="enrollmentforms/addEdit?text='.rtrim(strtr(base64_encode("id=".$dataListing['Data']['query_result'][$i]['enrollmentforms_id'] ), '+/', '-_'), '=').'" title="Edit"><span class="spn-9"><i class="ti-pencil"></i></span></a>';
				}}
				if(in_array('EnrollmentFormDelete',$this->RolePermission)){
					/*if($dataListing['Data']['query_result'][$i]['isactive'] == 1){
						$actionCol .='&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\''.$dataListing['Data']['query_result'][$i]['perm_id'] .'\');" title="Delete"><span class="spn-9"><i class="ti-trash" style="color: red;"></i></span></a>';
					}*/
				}
				
				$downloadFile = "";
				//if($_SESSION['webpanel']['role_id'] == 1){
					$downloadFile='<a href="'.FRONT_URL.'/assets/enrollmentforms/'.$dataListing['Data']['query_result'][$i]['form_file'].'" target="_blank" title="Download Form">Download Form</a>';
				//}
				
				array_push($temp, $downloadFile);
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
		
		$result = array();
		
		if(!empty($record_id)){
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['id'] = $record_id;
			$checkDetails = curlFunction(SERVICE_URL.'/api/getEnrollmentFormsData',$data);
			$checkDetails = json_decode($checkDetails, true);
			//echo "<pre>";print_r($checkDetails);exit;
			$result['getDetails'] = $checkDetails['Data'];
			
		}else{
			$result['getDetails'] = array();
		}
		
		//echo $user_id;
		$this->load->view('template/header.php');
		$this->load->view('enrollmentforms/addEdit',$result);
		$this->load->view('template/footer.php');
	}
 
	function submitForm()
	{
		/*print_r($_POST);
		exit;*/
		
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			
			$data = array();

			$thumnail_value = "";
			//echo "<pre>";print_r($_FILES);exit;
			if(isset($_FILES) && isset($_FILES["form_file"]["name"]))
			{
				$config = array();
				$config['upload_path'] = DOC_ROOT_FRONT."/assets/enrollmentforms/";
				$config['max_size']    = '0';
				//$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['allowed_types'] = '*';
				$config['file_name']     = md5(uniqid("100_ID", true));
				
				//print_r($config);exit;
				$this->load->library('upload', $config);
				if (!$this->upload->do_upload("form_file"))
				{
					$image_error = array('error' => $this->upload->display_errors());
					echo json_encode(array("success"=>false, "msg"=>$image_error['error']));
					exit;
				}
				else
				{
					$image_data = array('upload_data' => $this->upload->data());
					$thumnail_value = $image_data['upload_data']['file_name'];
					 
					// print_r($config);
					// exit;
				}
				
			}
			else
			{
				$thumnail_value = $_POST['input_file'];
			}

			//exit;

			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['enrollmentforms_id'] = (!empty($_POST['enrollmentforms_id'])) ? $_POST['enrollmentforms_id'] : '';
			$data['form_title'] = (!empty($_POST['form_title'])) ? $_POST['form_title'] : '';
			$data['form_file'] = (!empty($thumnail_value)) ? $thumnail_value : '';
			$data['login_user_id'] = $_SESSION["webpanel"]['employee_id'];
			
			$addEdit = curlFunction(SERVICE_URL.'/api/addEditEnrollmentForm',$data);
			//echo "<pre>";print_r($addEdit);exit;
			$addEdit = json_decode($addEdit, true);
			
			if($addEdit['status_code'] == '200'){
				echo json_encode(array('success'=>true, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}else{
				echo json_encode(array('success'=>false, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}
		}
		else
		{
			echo json_encode(array("success"=>false, 'msg'=>'Problem While Add/Edit Record..'));
			exit;
		}
	}
 
}

?>