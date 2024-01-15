<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Features extends CI_Controller
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
		$this->load->view('features/index');
		$this->load->view('template/footer.php');
	}

	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		$insurerListing = curlFunction(SERVICE_URL . '/api2/FeaturesListing', $_GET);
		$insurerListing = json_decode($insurerListing, true);
		// echo "<pre>";print_r($insurerListing);exit;
		if ($insurerListing['status_code'] == '401') {
			//echo "in condition";
			redirect('login');
			exit();
		}


		//$get_result = $this->adcategorymodel->getRecords($_GET);

		$result = array();
		$result["sEcho"] = $_GET['sEcho'];

		$result["iTotalRecords"] = $insurerListing['Data']['totalRecords'];	//iTotalRecords get no of total recors
		$result["iTotalDisplayRecords"] = $insurerListing['Data']['totalRecords']; //iTotalDisplayRecords for display the no of records in data table.

		$items = array();
        // echo "here";
		if (!empty($insurerListing['Data']['query_result']) && count($insurerListing['Data']['query_result']) > 0) {
			// echo "in";exit;
			for ($i = 0; $i < sizeof($insurerListing['Data']['query_result']); $i++) {
				$temp = array();
				array_push($temp, $insurerListing['Data']['query_result'][$i]['title']);
				array_push($temp, $insurerListing['Data']['query_result'][$i]['short_description']);
				array_push($temp, $insurerListing['Data']['query_result'][$i]['long_description']);
				if ($insurerListing['Data']['query_result'][$i]['isactive'] == 1) {
					array_push($temp, 'Active');
				} else {
					array_push($temp, 'In-Active');
				}

				$actionCol = "";
				//if($this->privilegeduser->hasPrivilege("CategoriesAddEdit"))
				//{
				$actionCol .= '<a href="features/addEdit?text=' . rtrim(strtr(base64_encode("id=" . $insurerListing['Data']['query_result'][$i]['id']), '+/', '-_'), '=') . '" title="Edit">
					<span class="spn-9"><i class="ti-pencil"></i></span></a>';
				//}
				//if($this->privilegeduser->hasPrivilege("CategoryDelete")){
				if ($insurerListing['Data']['query_result'][$i]['isactive'] == 1) {
					$actionCol .= '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\'' . $insurerListing['Data']['query_result'][$i]['id'] . '\');" title="Delete">
						<span class="spn-9"><i class="ti-trash" style="color: red;"></i></span></a>';
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
		// echo $record_id;exit;
		
		$result = array();
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$result['datalist'] = json_decode(curlFunction(SERVICE_URL . '/api2/addNewFeatureView', $data));
		//var_dump($result['datalist'] );die;
		if(!empty($record_id)){
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['id'] = $record_id;
			$checkDetails = curlFunction(SERVICE_URL.'/api2/getfeaturebyid',$data);
			$checkDetails = json_decode($checkDetails, true);
			// echo $checkDetails['Data']['plan_details'][0]['creditor_id'];exit;
			$data['creditor_id'] = $checkDetails['Data']['plan_details'][0]['creditor_id'];
			$plans=[];
			$plans = json_decode(curlFunction(SERVICE_URL . '/api2/fetchFeature', $data),TRUE);
			// echo "<pre>hhh";print_r($plans);exit;
			$result['plans'] = $plans;
			// echo "<pre>hhh";print_r($result);exit;
			$result['plan_details'] = $checkDetails['Data']['plan_details'];
			// echo "<pre>hhh";print_r($result);exit;

			
		}else{
			$result['plan_details'] = array();
		}
		
		$this->load->view('template/header.php');
		$this->load->view('features/addEdit',$result);
		$this->load->view('template/footer.php');
	}

	function submitForm()
	{
		/*print_r($_FILES);
		exit;*/
		
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			
			$data = array();

			$thumnail_value = "";
			//echo "<pre>";print_r($_FILES);exit;
			if(isset($_FILES) && isset($_FILES["form_file"]["name"]))
			{
				if (!is_dir(DOC_ROOT_FRONT."/assets/features/")) {
                        mkdir(DOC_ROOT_FRONT."/assets/features/");
                }
				$config = array();
				$config['upload_path'] = DOC_ROOT_FRONT."/assets/features/";
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
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['id'] = (!empty($_POST['id'])) ? $_POST['id'] : '';
			$data['creditor'] = (!empty($_POST['creditor'])) ? $_POST['creditor'] : '';
			$data['plan_name'] = (!empty($_POST['plan_name'])) ? $_POST['plan_name'] : '';
			$data['feature'] = (!empty($_POST['feature'])) ? $_POST['feature'] : '';
			$data['title'] = (!empty($_POST['title'])) ? $_POST['title'] : '';
			$data['short_description'] = (!empty($_POST['short_description'])) ? $_POST['short_description'] : '';
			$data['long_description'] = (!empty($_POST['long_description'])) ? $_POST['long_description'] : '';
			$data['is_active'] = (!empty($_POST['is_active'])) ? $_POST['is_active'] : '';
			$data['login_user_id'] = $_SESSION["webpanel"]['employee_id'];
			$data['image'] = $thumnail_value;
			// print_r($data);exit;
			
			$addEdit = curlFunction(SERVICE_URL.'/api2/addEditFeature',$data);
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

	function fetch_plans(){
		$data['creditor_id'] = $_POST['creditor_id'];
		$features = json_decode(curlFunction(SERVICE_URL . '/api2/fetchFeature', $data));
		$html = '<option value="">Select Plan</option>';
		foreach ($features as $key => $value) {
			
					$html .= "<option value = '".$value->plan_id."'>".$value->plan_name."</option>";
				}
				$res = array();		
		$res['html']=$html;
		$res['status'] = "Success";
		echo json_encode($res);
	}


	function AddNewView()
	{
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['datalist'] = json_decode(curlFunction(SERVICE_URL . '/api2/addNewFeatureView', $data));
		$this->load->view('template/header.php');
		$this->load->view('features/addNew', $data);
		$this->load->view('template/footer.php');
	}


	function AddNew()
	{
		//var_dump($_POST['payment_modes']);exit;
		//echo "<pre>";print_r($_POST);exit;
		//Check Duplicated Payment Mode
		if(isset($_POST['payment_modes'][0]) && !empty($_POST['payment_modes'][0]) ){
			//echo "if";exit;
			$sel_modes = array();
			for ($i = 0; $i < sizeof($_POST['payment_modes']); $i++) {
				if($_POST['payment_modes'][$i] !=""){
					if(in_array($_POST['payment_modes'][$i], $sel_modes)){
						echo json_encode(array('success' => false, 'msg' => "Duplicate Payment Mode Selected."));
						exit;
					}else{
						$sel_modes[] = $_POST['payment_modes'][$i];
					}
				}else{
					echo json_encode(array('success' => false, 'msg' => "Select atleast one payment mode."));
					exit;
				}
				
			}			
		}else{
			//echo "else";exit;
			echo json_encode(array('success' => false, 'msg' => "Select atleast one payment mode."));
			exit;
		}
		//exit;
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['plan_name'] = $this->input->post('plan_name');
		$data['creditor_id'] = $this->input->post('creditor');
		$data['policy_type_id'] = 1;
		$data['policy_sub_type_id'] = implode(',', $this->input->post('policy_sub_type'));
		//$data['payment_modes'] = implode(',', $this->input->post('payment_modes'));
		$data['payment_modes'] = (!empty($_POST['payment_modes'])) ? $_POST['payment_modes'] : '';
		$data['payment_workflow'] = (!empty($_POST['payment_workflow'])) ? $_POST['payment_workflow'] : '';
		$response = json_decode(curlFunction(SERVICE_URL . '/api2/addNewProduct', $data), true);
		// print_r($response);die;
		if ($response['status_code'] == '200') {

			echo json_encode(array('success' => true, 'msg' => $response['Metadata']['Message'], 'data' => $response['Data']));
			exit;
		} else {
			echo json_encode(array('success' => false, 'msg' => $response['Metadata']['Message']));
			exit;
		}
	}

	function update()
	{
		
		//Check Duplicated Payment Mode
		if(!empty($_POST['payment_modes'])){
			$sel_modes = array();
			for ($i = 0; $i < sizeof($_POST['payment_modes']); $i++) {
				if(in_array($_POST['payment_modes'][$i], $sel_modes)){
					echo json_encode(array('success' => false, 'msg' => "Duplicate Payment Mode Selected."));
					exit;
				}else{
					$sel_modes[] = $_POST['payment_modes'][$i];
				}
			}			
		}else{
			echo json_encode(array('success' => false, 'msg' => "Select atleast one payment mode."));
			exit;
		}

		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['plan_id'] = $this->input->post('plan_id');
		$data['plan_name'] = $this->input->post('plan_name');
		$data['creditor_id'] = $this->input->post('creditor');
		$data['policy_type_id'] = 1;
		$data['policy_sub_type_id'] = implode(',', $this->input->post('policy_sub_type'));
		//$data['payment_modes'] = implode(',', $this->input->post('payment_modes'));
		$data['payment_modes'] = (!empty($_POST['payment_modes'])) ? $_POST['payment_modes'] : '';
		$data['payment_workflow'] = (!empty($_POST['payment_workflow'])) ? $_POST['payment_workflow'] : '';

		$response = json_decode(curlFunction(SERVICE_URL . '/api2/UpdateProduct', $data), true);
		//echo "<pre>";print_r($response);exit;
		if ($response['status_code'] == '200') {
			echo json_encode(array('success' => true, 'msg' => $response['Metadata']['Message'], 'data' => $response['Data']));
			exit;
		} else {
			echo json_encode(array('success' => false, 'msg' => $response['Metadata']['Message']));
			exit;
		}
	}

	
	/*
	Author : Jitendra Gamit
	Date : 19th November, 2020
	**/
	function delRecord($id)
	{
		//$appdResult = $this->adcategorymodel->delrecord("tbl_categories","category_id ",$id);
		$data = array();
		$data['id'] = $id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$delRecord = curlFunction(SERVICE_URL . '/api2/delFeature', $data);
		//echo "<pre>";print_r($checkDetails);exit;
		$delRecord = json_decode($delRecord, true);

		if ($delRecord['status_code'] == '200') {
			echo "1";
		} else {
			echo "2";
		}
	} // EO delRecord()
}
