<?php header('Access-Control-Allow-Origin: *'); if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Policysubtype extends CI_Controller 
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
		$this->load->view('policysubtype/index');
		$this->load->view('template/footer.php');
	}

	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		$insurerListing = curlFunction(SERVICE_URL.'/api2/PolicySubTypeListing',$_GET);
		$insurerListing = json_decode($insurerListing, true);
		//echo "<pre>";print_r($insurerListing);exit;
		if($insurerListing['status_code'] == '401'){
			//echo "in condition";
			redirect('login');
			exit();
		}
		
		
		//$get_result = $this->adcategorymodel->getRecords($_GET);

		$result = array();
		$result["sEcho"]= $_GET['sEcho'];

		$result["iTotalRecords"] = $insurerListing['Data']['totalRecords'];	//iTotalRecords get no of total recors
		$result["iTotalDisplayRecords"]= $insurerListing['Data']['totalRecords']; //iTotalDisplayRecords for display the no of records in data table.

		$items = array();
		
		if(!empty($insurerListing['Data']['query_result']) && count($insurerListing['Data']['query_result']) > 0)
		{
			for($i=0;$i<sizeof($insurerListing['Data']['query_result']);$i++)
			{
				$temp = array();
				array_push($temp, $insurerListing['Data']['query_result'][$i]['policy_sub_type_name'] );
				array_push($temp, $insurerListing['Data']['query_result'][$i]['typename'] );
				
				if($insurerListing['Data']['query_result'][$i]['isactive'] == 1){
					array_push($temp, 'Active' );
				}else{
					array_push($temp, 'In-Active' );
				}
				
				$actionCol = "";
				//if($this->privilegeduser->hasPrivilege("CategoriesAddEdit"))
				//{
					$actionCol .='<a href="policysubtype/addEdit?text='.rtrim(strtr(base64_encode("id=".$insurerListing['Data']['query_result'][$i]['policy_sub_type_id'] ), '+/', '-_'), '=').'" title="Edit">
					<span class="spn-9"><i class="ti-pencil"></i></span>
					</a>';
				$actionCol .='<button class="btn btn-link" onclick="configureComparedata(\''.$insurerListing['Data']['query_result'][$i]['policy_sub_type_id'] .'\')" title="Compare Features"><i class="ti-book"></i></button>';
				//}
				//if($this->privilegeduser->hasPrivilege("CategoryDelete")){
					if($insurerListing['Data']['query_result'][$i]['isactive'] == 1){
						$actionCol .='&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\''.$insurerListing['Data']['query_result'][$i]['policy_sub_type_id'] .'\');" title="Delete">
						<span class="spn-9"><i class="ti-trash" style="color: red;"></i></span>
						</a>';
					}
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
		
		$result = array();
		
		if(!empty($record_id)){
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['id'] = $record_id;
			$checkDetails = curlFunction(SERVICE_URL.'/api2/getPolicySubTypeFormData',$data);
			$checkDetails = json_decode($checkDetails, true);
			//echo "<pre>";print_r($checkDetails);exit;
			$result['getDetails'] = $checkDetails['Data'];
			$types = json_decode(curlFunction(SERVICE_URL.'/api2/getPolicyTypes',$data),true);
			$result['policytypes'] = $types['Data'];
			
			
		}else{
		    $data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
		    $types = json_decode(curlFunction(SERVICE_URL.'/api2/getPolicyTypes',$data),true);
			$result['policytypes'] = $types['Data'];
		}
		
		//echo $user_id;
		$this->load->view('template/header.php');
		$this->load->view('policysubtype/addEdit',$result);
		$this->load->view('template/footer.php');
	}
	 
	function submitForm()
	{
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		//print_r($_FILES);die;
			$data = array();
		    $data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['policy_sub_type_id'] = (!empty($_POST['policy_sub_type_id'])) ? $_POST['policy_sub_type_id'] : '';
			$data['policy_sub_type_name'] = (!empty($_POST['policy_sub_type_name'])) ? $_POST['policy_sub_type_name'] : '';
			$data['policy_type_id'] = (!empty($_POST['policy_type_id'])) ? $_POST['policy_type_id'] : '';
			$data['description'] = (!empty($_POST['description'])) ? str_replace("'", "", $_POST['description']) : '';
			$data['gadget_name'] = (!empty($_POST['gadget_name'])) ? $_POST['gadget_name'] : '';
			$data['short_name'] = (!empty($_POST['policy_type_id'])) ? $_POST['short_name'] : '';

			$data['isactive'] = (!empty($_POST['isactive'])) ? $_POST['isactive'] : '';
		$logo_url = '';
		if(isset($_FILES['demo2'])){

			$upload_dir = FCPATH.'assets'. DIRECTORY_SEPARATOR .'images';
			$file_ext = pathinfo($_FILES['demo2']['name'], PATHINFO_EXTENSION);
			$size = $_FILES['demo2']['size'];
			$savename = strtolower($_POST['policy_sub_type_name']).'-logo.' . $file_ext;
			$path = $upload_dir . DIRECTORY_SEPARATOR . $savename;

			if(!in_array($file_ext, ['png', 'jpeg', 'jpg', 'bmp'])){

				echo json_encode(array("success"=>false, 'msg'=>'Allowed logo extensions are png, jpeg, jpg, bmp'));
				exit;
			}
			else if($size > 5000000){

				echo json_encode(array("success"=>false, 'msg'=>'Logo size should be less than 5 MB'));
				exit;
			}


			if(move_uploaded_file($_FILES['demo2']['tmp_name'], $path)){

				$logo_url = FRONT_URL."/assets/images/$savename";
			}
		}
		$logo_url2='';
		if(isset($_FILES['demo3'])){

			$upload_dir = FCPATH.'assets'. DIRECTORY_SEPARATOR .'images';
			$file_ext = pathinfo($_FILES['demo3']['name'], PATHINFO_EXTENSION);
			$size = $_FILES['demo3']['size'];
			$savename = strtolower($_POST['policy_sub_type_name']).'-desclogo.' . $file_ext;
			$path = $upload_dir . DIRECTORY_SEPARATOR . $savename;

			if(!in_array($file_ext, ['png', 'jpeg', 'jpg', 'bmp'])){

				echo json_encode(array("success"=>false, 'msg'=>'Allowed logo extensions are png, jpeg, jpg, bmp'));
				exit;
			}
			else if($size > 5000000){

				echo json_encode(array("success"=>false, 'msg'=>'Logo size should be less than 5 MB'));
				exit;
			}


			if(move_uploaded_file($_FILES['demo3']['tmp_name'], $path)){

				$logo_url2 = FRONT_URL."/assets/images/$savename";
			}
		}
		$data['logo'] = $logo_url;
		$data['desc_img'] = $logo_url2;
			$addEdit = curlFunction(SERVICE_URL.'/api2/addEditPolicySubType',$data);
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
		
	function delRecord($id)
	{
		//$appdResult = $this->adcategorymodel->delrecord("tbl_categories","category_id ",$id);
		$data = array();
		$data['id'] = $id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$delRecord = curlFunction(SERVICE_URL.'/api2/delPolicySubType',$data);
		//echo "<pre>";print_r($checkDetails);exit;
		$delRecord = json_decode($delRecord, true);
		 
		if($delRecord['status_code'] == '200'){
			echo "1";
		}else{
			echo "2";
		}	
	}
	function delRecordfeature($id)
	{
	//	echo $id;die;
		//$appdResult = $this->adcategorymodel->delrecord("tbl_categories","category_id ",$id);
		$data = array();
		$data['id'] = $id;
		$where=array('id'=>$data['id']);
		$this->db->where($where);
		$delete=$this->db->update('compare_features',array('is_active'=>0));

		if($delete){
			echo "1";
		}else{
			echo "2";
		}
	}
	function addFeature(){
		$policysubtypeid=$this->input->post('policysubtypeid');
		$feature=$this->input->post('feature');
		if(!empty($feature)){
			$data=array(
				'policy_sub_type_id'=>$policysubtypeid,
				'feature'=>$feature,
				'is_active'=>1,
			);
			$query=$this->db->insert('compare_features',$data);
			if($query){
				$response['status']=200;
			}else{
				$response['status']=201;
			}echo json_encode($response);exit;
		}

	}
	function getFeatures(){
		$policysubtypeid=$this->input->post('id');
		$query=$this->db->query("select * from compare_features where is_active=1 and policy_sub_type_id=".$policysubtypeid)->result();
		if($this->db->affected_rows()  > 0){
			$response['status']=200;
			$response['data']=$query;
		}else{
			$response['status']=201;
		}echo json_encode($response);exit;
	}
}

?>
