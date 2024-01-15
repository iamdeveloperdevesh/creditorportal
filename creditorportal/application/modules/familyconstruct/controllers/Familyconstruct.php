<?php header('Access-Control-Allow-Origin: *'); if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Familyconstruct extends CI_Controller 
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
		$this->load->view('familyconstruct/index');
		$this->load->view('template/footer.php');
	}

	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		$insurerListing = curlFunction(SERVICE_URL.'/api2/FamilyConstructListing',$_GET);
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
				array_push($temp, $insurerListing['Data']['query_result'][$i]['member_type'] );
				
				$actionCol = "";
				//if($this->privilegeduser->hasPrivilege("CategoriesAddEdit"))
				//{
					$actionCol .='<a href="familyconstruct/addEdit?text='.rtrim(strtr(base64_encode("id=".$insurerListing['Data']['query_result'][$i]['id'] ), '+/', '-_'), '=').'" title="Edit"><span class="spn-9"><i class="ti-pencil"></i></span></a>';
				//}
				//if($this->privilegeduser->hasPrivilege("CategoryDelete")){
					if($insurerListing['Data']['query_result'][$i]['isactive'] == 1){
						$actionCol .='&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\''.$insurerListing['Data']['query_result'][$i]['id'] .'\');" title="Delete"><span class="spn-9"><i class="ti-trash" style="color: red;"></i></span></a>';
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
			$checkDetails = curlFunction(SERVICE_URL.'/api2/getFamilyConstructFormData',$data);
			$checkDetails = json_decode($checkDetails, true);
			//echo "<pre>";print_r($checkDetails);exit;
			$result['getDetails'] = $checkDetails['Data'];
		
		}
		
		//echo $user_id;
		$this->load->view('template/header.php');
		$this->load->view('familyconstruct/addEdit',$result);
		$this->load->view('template/footer.php');
	}
	 
	function submitForm()
	{
		
			$data = array();
		    $data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['family_construct_id'] = (!empty($_POST['family_construct_id'])) ? $_POST['family_construct_id'] : '';
			
			$data['member_type'] = (!empty($_POST['member_type'])) ? $_POST['member_type'] : '';
			
			$addEdit = curlFunction(SERVICE_URL.'/api2/addEditFamilyConstruct',$data);
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
		$delRecord = curlFunction(SERVICE_URL.'/api2/delFamilyConstruct',$data);
		//echo "<pre>";print_r($checkDetails);exit;
		$delRecord = json_decode($delRecord, true);
		 
		if($delRecord['status_code'] == '200'){
			echo "1";
		}else{
			echo "2";
		}	
	}
}

?>
