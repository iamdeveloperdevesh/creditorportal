<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
class Assignmentdeclaration extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		checklogin();
		$this->RolePermission = getRolePermissions();
	}
 
	function index()
	{
	    
		$result = array();
		$this->load->view('template/header.php');
		$this->load->view('assignmentdeclaration/index',$result);
		$this->load->view('template/footer.php');
	}
 
	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		//echo "<pre>GET ";print_r($_GET);exit;
		$userListing = curlFunction(SERVICE_URL.'/api/assignmentDeclarationListing',$_GET);
		$userListing = json_decode($userListing, true);
		//echo "<pre>";print_r($userListing);exit;
		if($userListing['status_code'] == '401'){
			//echo "in condition";
			redirect('login');
			exit();
		}
		
		
		//$get_result = $this->adcategorymodel->getRecords($_GET);

		$result = array();
		$result["sEcho"]= $_GET['sEcho'];

		$result["iTotalRecords"] = $userListing['Data']['totalRecords'];	//iTotalRecords get no of total recors
		$result["iTotalDisplayRecords"]= $userListing['Data']['totalRecords']; //iTotalDisplayRecords for display the no of records in data table.

		$items = array();
		
		if(!empty($userListing['Data']['query_result']) && count($userListing['Data']['query_result']) > 0)
		{
			for($i=0;$i<sizeof($userListing['Data']['query_result']);$i++)
			{
				$temp = array();
				array_push($temp, $userListing['Data']['query_result'][$i]['plan_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['creaditor_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['label'] );
				if($userListing['Data']['query_result'][$i]['is_active'] == 1){
					array_push($temp, 'Active' );
				}else{
					array_push($temp, 'In-Active' );
				}
				
				$actionCol = "";
				if(in_array('AssignmentDeclarationEdit',$this->RolePermission)){
					$actionCol .='<a href="assignmentdeclaration/addEdit?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['assignment_declaration_id'] ), '+/', '-_'), '=').'" title="Edit"><span class="spn-9"><i class="ti-pencil"></i></span></a>';
				}
				if(in_array('AssignmentDeclarationDelete',$this->RolePermission)){
					if($userListing['Data']['query_result'][$i]['is_active'] == 1){
						$actionCol .='&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\''.$userListing['Data']['query_result'][$i]['assignment_declaration_id'] .'\');" title="Delete"><span class="spn-9"><i class="ti-trash" style="color: red;"></i></span></a>';
					}
				}
			
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
		//print_r($_GET);
		$record_id = "";
		if(!empty($_GET['text']) && isset($_GET['text']))
		{
			$varr=base64_decode(strtr($_GET['text'], '-_', '+/'));	
			parse_str($varr,$url_prams);
			$record_id = $url_prams['id'];
		}
		
		//Get all creditors
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['role_id'] = $_SESSION['webpanel']['role_id'];
		$data['user_id'] = $_SESSION['webpanel']['employee_id'];
		
		$getCreditors = curlFunction(SERVICE_URL.'/api/getCreditorsData',$data);
		$getCreditors = json_decode($getCreditors, true);
		//echo "<pre>";print_r($getCreditors);exit;
		$result['creditors'] = $getCreditors['Data'];
		
		//Get user details
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['id'] = $record_id;
		$getRecordDetails = curlFunction(SERVICE_URL.'/api/getAssignmentDeclarationDetails',$data);
		$getRecordDetails = json_decode($getRecordDetails, true);
		//echo "<pre>ddd";print_r($getRecordDetails);exit;
		$result['user_details'] = $getRecordDetails['Data'][0];
		
		
		$this->load->view('template/header.php');
		$this->load->view('assignmentdeclaration/addEdit',$result);
		$this->load->view('template/footer.php');
	}
	
	public function getPlans() {
		//Get all creditors
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['creditor_id'] = $_POST['creditor_id'];
		//$data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
		$getPlans = curlFunction(SERVICE_URL.'/api/getCreditorsPlansData',$data);
		$getPlans = json_decode($getPlans, true);
		//echo "<pre>";print_r($getPlans);exit;
		$plans = $getPlans['Data'];
		
		$option = '';
		$plan_id = '';
		if(isset($_POST['plan_id']) && !empty($_POST['plan_id'])) {
		   $plan_id = $_POST['plan_id'];
		}
		//echo "plan_id: ".$plan_id;exit;
		
		if(!empty($plans)){
			for ($i = 0; $i < sizeof($plans); $i++){
				$sel = ($plans[$i]['plan_id'] == $plan_id) ? 'selected="selected"' : '';
				$option .= '<option value="'.$plans[$i]['plan_id'].'" ' . $sel . ' >'.$plans[$i]['plan_name'].'</option>';
			}
		}
		//echo $option;exit;
		echo json_encode(array("status" => "success", "option" => $option));
		exit;
		
		
	}
 
	function submitForm()
	{
		// print_r($_POST);
		// exit;
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['assignment_declaration_id'] = (!empty($_POST['assignment_declaration_id'])) ? $_POST['assignment_declaration_id'] : '';
			
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
			
			$data['label'] = (!empty($_POST['label'])) ? $_POST['label'] : '';
			$data['content'] = (!empty($_POST['content'])) ? $_POST['content'] : '';
			$data['is_active'] = (!empty($_POST['is_active'])) ? $_POST['is_active'] : '';
			$data['login_user_id'] = $_SESSION["webpanel"]['employee_id'];
			
			$addEdit = curlFunction(SERVICE_URL.'/api/addEditAssignmentDeclaration',$data);
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
			echo json_encode(array('success' => false, 'msg'=>'Problem while updating record.'));
			exit;
		}
	}
 
	//For Delete
	function delRecord($id)
	{
		//$appdResult = $this->adcategorymodel->delrecord("tbl_categories","category_id ",$id);
		$data = array();
		$data['id'] = $id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$delRecord = curlFunction(SERVICE_URL.'/api/delAssignmentDeclaration',$data);
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