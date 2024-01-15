<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Products extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		checklogin();
		$this->RolePermission = getRolePermissions();
	}

	function index()
	{
        $data['utoken'] = $_SESSION['webpanel']['utoken'];
        $data['role_id'] = $_SESSION['webpanel']['role_id'];
        $data['user_id'] = $_SESSION['webpanel']['employee_id'];
        $data['cid'] = $_GET['cid'];
        //echo $_GET['cid'];exit;
        if($_SESSION['webpanel']['role_id'] == 3){
            $getCreditors = curlFunction(SERVICE_URL.'/api/getRoleWiseCreditorsData',$data);
        }else{
            $getCreditors = curlFunction(SERVICE_URL.'/api/getCreditorsData',$data);
        }
        $getCreditors = json_decode($getCreditors, true);
        //echo "<pre>";print_r($getCreditors);exit;
        $result['creditors'] = $getCreditors['Data'];
       // print_r($result['creditors']);die;
		$this->load->view('template/header.php');
		$this->load->view('products/index',$result);
		$this->load->view('template/footer.php');
	}

    function fetch()
    {
        $_GET['utoken'] = $_SESSION['webpanel']['utoken'];
        $insurerListing = curlFunction(SERVICE_URL . '/api2/ProductsListing', $_GET);
        $insurerListing = json_decode($insurerListing, true);
        //echo "<pre>";print_r($insurerListing);exit;
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

        if (!empty($insurerListing['Data']['query_result']) && count($insurerListing['Data']['query_result']) > 0) {
            for ($i = 0; $i < sizeof($insurerListing['Data']['query_result']); $i++) {
                $temp = array();
                $query=$this->db->query("select group_concat(policy_sub_type_id) as policy_sub_type_id   from master_policy where plan_id=".$insurerListing['Data']['query_result'][$i]['plan_id'])->row();
                $policy_sub_type_id=$query->policy_sub_type_id;
                array_push($temp, $insurerListing['Data']['query_result'][$i]['plan_name']);
                array_push($temp, $insurerListing['Data']['query_result'][$i]['creditorname']);
                array_push($temp, $insurerListing['Data']['query_result'][$i]['policytype']);
                if ($insurerListing['Data']['query_result'][$i]['isactive'] == 1) {
                    array_push($temp, 'Active');
                } else {
                    array_push($temp, 'In-Active');
                }

                $actionCol = "";
                //if($this->privilegeduser->hasPrivilege("CategoriesAddEdit"))
                //{
                $actionCol .= '<a href="products/edit?text=' . rtrim(strtr(base64_encode("id=" . $insurerListing['Data']['query_result'][$i]['plan_id']), '+/', '-_'), '=') . '" title="Edit">
					<span class="spn-9"><i class="ti-pencil"></i></span></a>';
                //}
                //if($this->privilegeduser->hasPrivilege("CategoryDelete")){
                if ($insurerListing['Data']['query_result'][$i]['isactive'] == 1) {
                    $actionCol .= '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\'' . $insurerListing['Data']['query_result'][$i]['plan_id'] . '\');" title="Delete">
						<span class="spn-9"><i class="ti-trash" style="color: red;"></i></span></a>';
                }
                $actionCol .='<button class="btn btn-link" onclick="configureComparedata(\''.$insurerListing['Data']['query_result'][$i]['plan_id'] .'\',\''.$policy_sub_type_id .'\')" title="Compare Features"><i class="ti-book"></i></button>';
                $actionCol .='<a onclick="openModalAPIrequest(\''.$insurerListing['Data']['query_result'][$i]['plan_id'] .'\')"  title="API Request to generate Token"><span class="spn-9"><i class="ti-eye"></i></span></a>';
                //}

                $actionCol .= '<a href="products/addEdit?text=' . rtrim(strtr(base64_encode("id=" . $insurerListing['Data']['query_result'][$i]['plan_id']), '+/', '-_'), '=') . '" title="Add/Edit NSTP RULE">
					<span class="spn-9">Add/Edit NSTP RULE</span></a>';
                $actionCol .= '<a href="#" onclick="MappedModal(\''.$insurerListing['Data']['query_result'][$i]['plan_id'] .'\')" title="Map new partner">
					<span class="spn-9">Map new partner</span></a>';
               //policy_type_id
                if($insurerListing['Data']['query_result'][$i]['policy_type_id'] == 1){
                    $actionCol .= '<a href="products/DownloadSampleApiExcel/'.$insurerListing['Data']['query_result'][$i]['plan_id'].'"  title="Download Api Excel">
					<span class="spn-9">Download Api Excel</span></a>';

                }else{
                    $actionCol .= '<a href="products/DownloadGadgetSampleApiExcel/'.$insurerListing['Data']['query_result'][$i]['plan_id'].'"  title="Download Api Excel">
					<span class="spn-9">Download Api Excel</span></a>';

                }

				if($_GET['sSearch_1'] == 2){
                    $actionCol .= '<a href="' . base_url().'GadgetInsurance?'.$insurerListing['Data']['query_result'][$i]['URL'] . '" title="Journey Link" target="__blank">
					<span class="spn-9">Journey Link</span></a>';
                }else{
                    $actionCol .= '<a href="' . base_url().'customerportal?'.$insurerListing['Data']['query_result'][$i]['URL'] . '" title="Journey Link" target="__blank">
					<span class="spn-9">Journey Link</span></a>';
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
		$record_id = "";
		//print_r($_GET);
		if(!empty($_GET['text']) && isset($_GET['text'])){
			$varr=base64_decode(strtr($_GET['text'], '-_', '+/'));	
			parse_str($varr,$url_prams);
			$record_id = $url_prams['id'];
		}
		//echo $record_id;
		
		$result = array();
		
		if(!empty($record_id)){
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['id'] = $record_id;
			$checkDetails = curlFunction(SERVICE_URL.'/api2/getUWWorkFlowData',$data);
			$checkDetails = json_decode($checkDetails, true);
			//echo "<pre>hhh";print_r($checkDetails);exit;
			$result['plan_details'] = $checkDetails['Data']['plan_details'];
			$result['uwworkflow_details'] = $checkDetails['Data']['uwworkflow_details'];
			
		}else{
			$result['plan_details'] = array();
			$result['uwworkflow_details'] = array();
		}
		
		//echo $user_id;
		$this->load->view('template/header.php');
		$this->load->view('products/addEdit',$result);
		$this->load->view('template/footer.php');
	}

	function submitForm()
	{
		/*print_r($_POST);
		exit;*/
		
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['uw_case_id'] = (!empty($_POST['uw_case_id'])) ? $_POST['uw_case_id'] : '';
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['master_plan_id'] = (!empty($_POST['master_plan_id'])) ? $_POST['master_plan_id'] : '';
			$data['sum_insured'] = (!empty($_POST['sum_insured'])) ? $_POST['sum_insured'] : '';
			$data['login_user_id'] = $_SESSION["webpanel"]['employee_id'];
			
			$addEdit = curlFunction(SERVICE_URL.'/api2/addEditUWWorkflow',$data);
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


	function AddNewView()
	{
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['datalist'] = json_decode(curlFunction(SERVICE_URL . '/api2/addNewProductView', $data));
		//print_r($data['datalist']);die;
		$this->load->view('template/header.php');
		$this->load->view('products/addNew', $data);
		$this->load->view('template/footer.php');
	}
	function getPolicySubtype(){
	    $policy_type=$this->input->post('value');
        $data = $this->db->get_where('master_policy_sub_type', array('isactive' => 1,'policy_type_id'=>$policy_type))->result();
        $response['success']=true;
        $response['data']=$data;
        echo json_encode($response);
    }

	function edit()
	{
		// print_r($_SESSION);

		$data = array();
		if (!empty($_GET['text']) && isset($_GET['text'])) {
			$varr = base64_decode(strtr($_GET['text'], '-_', '+/'));
			parse_str($varr, $url_prams);
			$id = $url_prams['id'];
		}
		$data['id'] = $id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['datalist'] = json_decode(curlFunction(SERVICE_URL . '/api2/editproduct', $data));
		$subtype = array();
      //  var_dump($data['datalist']->details[0]->coi_type); die;
		foreach ($data['datalist']->details as $detail) {
			array_push($subtype, $detail->policy_sub_type_id);
		}
		$data['subtypes'] = $subtype;
		//echo "<pre>";print_r($data['datalist']->planpayments_modes);exit;
		/*$planpay = array();

		foreach ($data['datalist']->planpayments as $detail) {
			array_push($planpay, $detail->payment_mode_id);
		}

		$data['planpay'] = $planpay;
		*/
		$data['planmodes']= $data['datalist']->planpayments_modes;

		//echo "<pre>";print_r($data['datalist']);exit;
		$this->load->view('template/header.php');
		$this->load->view('products/edit', $data);
		$this->load->view('template/footer.php');
	}

	function productlist()
	{
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$this->load->view('template/header.php');
		$this->load->view('products/productlist', $data);
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
		$data['policy_type_id'] = $this->input->post('policy_type');
		$data['policy_sub_type_id'] = implode(',', $this->input->post('policy_sub_type'));
		//$data['payment_modes'] = implode(',', $this->input->post('payment_modes'));
		$data['payment_modes'] = (!empty($_POST['payment_modes'])) ? $_POST['payment_modes'] : '';
		$data['payment_workflow'] = (!empty($_POST['payment_workflow'])) ? $_POST['payment_workflow'] : '';
        $data['coi_type'] =  $this->input->post('coi_type');
        $data['is_single_coi'] =  $this->input->post('is_single_coi');
        $data['gender'] =  $this->input->post('gender');
        $data['coi_download'] =  $this->input->post('coi_download');
        $data['pan_mandatory'] =  $this->input->post('pan_mandatory');
        $data['nominee_mandatory'] =  $this->input->post('nominee_mandatory');
        $data['series_digit_count'] =$this->input->post('series_digit_count');
        $data['coi_start_series'] =$this->input->post('coi_start_series');
        $data['duplicate_coi_allow'] =$this->input->post('duplicate_coi_allow');
        $data['payment_first'] =$this->input->post('payment_first');
        $data['payment_page'] =$this->input->post('payment_page');
        if( $data['coi_type'] == 1 && (empty($data['series_digit_count']) || empty($data['coi_start_series']))){
            echo json_encode(array('success' => false, 'msg' => "CoI start series & Series count digit are compulsory!"));
            exit;
        }
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
		$data['policy_type_id'] =  $this->input->post('policy_type');
		$data['policy_sub_type_id'] = implode(',', $this->input->post('policy_sub_type'));
		//$data['payment_modes'] = implode(',', $this->input->post('payment_modes'));
		$data['payment_modes'] = (!empty($_POST['payment_modes'])) ? $_POST['payment_modes'] : '';
		$data['payment_workflow'] = (!empty($_POST['payment_workflow'])) ? $_POST['payment_workflow'] : '';
        $data['coi_type'] =  $this->input->post('coi_type');
        $data['is_single_coi'] =  $this->input->post('is_single_coi');
		$data['gender'] = $this->input->post('gender');
		$data['coi_download'] = $this->input->post('coi_download');
		$data['pan_mandatory'] =  $this->input->post('pan_mandatory');
        $data['nominee_mandatory'] =  $this->input->post('nominee_mandatory');
        $data['series_digit_count'] =$this->input->post('series_digit_count');
        $data['coi_start_series'] =$this->input->post('coi_start_series');
        $data['duplicate_coi_allow'] =$this->input->post('duplicate_coi_allow');
        $data['payment_first'] =$this->input->post('payment_first');
        $data['payment_page'] =$this->input->post('payment_page');
        if( $data['coi_type'] == 1 && (empty($data['series_digit_count']) || empty($data['coi_start_series']))){
            echo json_encode(array('success' => false, 'msg' => "CoI start series & Series count digit are compulsory!"));
            exit;
        }
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

	function uploadexcel()
	{
		if (isset($_FILES["file"]["name"])) {
			$path = $_FILES["file"]["tmp_name"];
			$object = PHPExcel_IOFactory::load($path);
			foreach ($object->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				for ($row = 2; $row <= $highestRow; $row++) {
					$family_construct = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
					$sum_insured = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
					$premium = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					$tax = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
					$data1[] = array(
						'master_policy_id'   => 1,
						'family_construct'  => $family_construct,
						'sum_insured'   => $sum_insured,
						'premium'    => $premium,
						'tax'  => $tax
					);
				}
			}
			$data['exceldata'] = json_encode($data1);
			$response = json_decode(curlFunction(SERVICE_URL . '/api2/uploadexcel', $data), true);

			echo 'Data Imported successfully';
		}
	}
    function gettypeid($plan_id){
        return  $this->db->query("select  policy_type_id from master_plan where plan_id=".$plan_id)->row()->policy_type_id;
    }
	function AddPolicyNew()
	{
		$data = array();
		$this->load->library('excel');
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['policy_sub_type_id'] = $this->input->post('policySubType');
		$data['plan_id'] = $this->input->post('plan_id');
		$data['creditor_id'] = $this->input->post('creditor_id');
		$data['policy_number'] = $this->input->post('policyNo');

        $mandatory = $this->input->post('mandatory');
        $data['policy_subtype_idNew'] =$this->input->post('policy_subtype_idNew');
		//$data['premium_type'] = $this->input->post('premium_type');
		//$absolute = $data['premium_type']; // changes 

		if ($mandatory == 1) {
			$data['is_optional'] = 0;
		} else {
			$data['is_optional'] = 1;
		}

		$combo = $this->input->post('combo');
		if ($combo == 1) {
			$data['is_combo'] = 1;
		} else {
			$data['is_combo'] = 0;
		}

		// $data['pdf_type'] = $this->input->post('pdf_type');
		$data['insurer_id'] = $this->input->post('masterInsurance');
		$data['policy_start_date'] = date('Y-m-d', strtotime(trim($this->input->post('policyStartDate'))));
		$data['policy_end_date'] = date('Y-m-d', strtotime(trim($this->input->post('policyEndDate'))));
		$data['plan_code'] = $this->input->post('plan_code');
		$data['product_code'] = $this->input->post('product_code');
		$data['scheme_code'] = $this->input->post('scheme_code');
		$data['source_name'] = $this->input->post('source_name');
		$data['max_member_count'] = $this->input->post('membercount');		
		$data['max_insured_count'] = $this->input->post('max_mi');
		$data['mandatory_if_not_selected'] = $this->input->post('mandatory_if_not_selected');
		$data['sitype'] = $this->input->post('sum_insured_type');
		$data['sibasis'] = $this->input->post('companySubTypePolicy');
		$data['adult_count'] = $this->input->post('adult_count');
		$data['child_count'] = $this->input->post('child_count');
        $data['cd_balance'] = $this->input->post('cd_balance');
        $data['threshold'] = $this->input->post('threshold');
        $data['gadget_eligibilty'] = $this->input->post('gadget_eligibilty');
        $data['start_series'] = $this->input->post('start_series');
        $data['end_series'] = $this->input->post('end_series');
      //  echo $data['plan_id'];die;
         $policy_type_id=$this->gettypeid($data['plan_id']);
        $data['policy_type_id'] = $policy_type_id;
        $data['default_sumInsured'] = $this->input->post('default_sumInsured');
        $data['cover_limit'] = $this->input->post('cover_limit');
        $data['cover_initial'] = $this->input->post('cover_initial');
        $data['is_adult_consider'] = $this->input->post('adult_consider'); // uncomment
        $data['business_type'] = $this->input->post('business_type');
        $data['per_sending_limit'] = $this->input->post('per_sending_limit');
        $data['per_location_limit'] = $this->input->post('per_location_limit');
        $data['b2c_type'] = $this->input->post('b2c_type');
        $data['per_sending_limit_b2c'] = $this->input->post('per_sending_limit_b2c');
        $data['per_location_limit_b2c'] = $this->input->post('per_location_limit_b2c');
        $data['excess'] = $this->input->post('excess'); // new added

        $data['default_rate'] = $this->input->post('default_rate');
        //--all risk
        $data['cover_det']= $this->input->post('cover_det');
        $data['coverage_type']= $this->input->post('coverage_type');
        $data['policyTenureStartDate']= $this->input->post('policyTenureStartDate');
        $data['policyTenureEndDate']= $this->input->post('policyTenureEndDate');
        if($data['sibasis'] == 5 && ($data['default_sumInsured'] == 0 || empty($data['default_sumInsured']))){
            echo json_encode(array('success' => false, 'msg' => "Default SumInsured is Compulsory!"));
            exit;
        }
        //echo $data['policy_sub_type_id'];die;
        if($data['policy_sub_type_id']== 3 && $data['sibasis'] == 5 && ($data['default_rate'] == 0 || empty($data['default_rate']))){
            echo json_encode(array('success' => false, 'msg' => "Rate is Compulsory!"));
            exit;
        }
		if ($data['sibasis'] != 1 && $data['sibasis'] != 8) {

			if (isset($_FILES["ageFile"]["name"])) {
				$data['testing'] = "dfdfdsfd";

				$path = $_FILES["ageFile"]["tmp_name"];
				$object = PHPExcel_IOFactory::load($path);
				foreach ($object->getWorksheetIterator() as $worksheet) {
					$highestRow = $worksheet->getHighestRow();
					$highestColumn = $worksheet->getHighestColumn();

					if ($data['sibasis'] == 4) {
						for ($row = 2; $row <= $highestRow; $row++) {
							$min_age = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
							$max_age = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
							$sum_insured = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
							$premium = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
							$tax = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
							$group_code = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
							$group_code_spouse = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
							if (!empty($premium)) {
							    if($tax == 1){
                                $taxRate=$premium*(18/100);
                                    $premium_with_tax=$taxRate;
                                }
								$dataexcel[] = array(
									'master_policy_id'   => $data['policy_sub_type_id'],
									'min_age'  => $min_age,
									'max_age'  => $max_age,
									'sum_insured'   => $sum_insured,
									'premium_rate'    => $premium,
									'premium_with_tax'    => $premium_with_tax,
									'is_taxable'  => $tax,
									'group_code'	=> $group_code,
									'group_code_spouse'	=> $group_code_spouse
									//  'is_absolute'  => $absolute
								);
							}
						}
					}

					if ($data['sibasis'] == 2) {

						for ($row = 2; $row <= $highestRow; $row++) {
							/*	
                 $adult_count = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                 $child_count = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                 $sum_insured = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                 $premium = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                 $tax = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
				***/

							$adult_count = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
							$child_count = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
							$sum_insured = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
							$premium = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
							$premium_with_tax = $worksheet->getCellByColumnAndRow(4, $row)->getValue();

							if (!empty($worksheet->getCellByColumnAndRow(5, $row)->getValue())) {
								$deductable = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
							} else {
								$deductable = '';
							}

							$group_code = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
							$group_code_spouse = $worksheet->getCellByColumnAndRow(7, $row)->getValue();

							if (!empty($premium)) {
								$dataexcel[] = array(
									'master_policy_id'   => $data['policy_sub_type_id'],
									'adult_count'  => $adult_count,
									'child_count'  => $child_count,
									'sum_insured'   => $sum_insured,
									'premium_rate'    => $premium,
									'premium_with_tax'    => $premium_with_tax,
									'deductable' => $deductable,
									'group_code'	=> $group_code,
									'group_code_spouse'	=> $group_code_spouse
									//  'is_absolute'  => $absolute
								);
							}
						}
					}

					if ($data['sibasis'] == 3) {
						for ($row = 2; $row <= $highestRow; $row++) {
							$adult_count = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
							$child_count = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
							$min_age = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
							$max_age = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
							$sum_insured = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
							$premium = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
							// $premium_with_tax = $worksheet->getCellByColumnAndRow(6, $row)->getValue();

							$tax = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
							$group_code = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
							$group_code_spouse = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
							if (!empty($premium)) {
								$dataexcel[] = array(
									'master_policy_id'   => $data['policy_sub_type_id'],
									'adult_count'  => $adult_count,
									'child_count'  => $child_count,
									'min_age'  => $min_age,
									'max_age'  => $max_age,
									'sum_insured'   => $sum_insured,
									'premium_rate'    => $premium,
									'is_taxable'  => $tax,
									'group_code'	=> $group_code,
									'group_code_spouse'	=> $group_code_spouse
								);
							}
						}
					}

					////////////////
					if ($data['sibasis'] == 6) {
						for ($row = 2; $row <= $highestRow; $row++) {
							$adult_count = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
							$child_count = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
							$deductable = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
							$sum_insured = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
							$premium = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
							$tax = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
							$group_code = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
							$group_code_spouse = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
							$age_band = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                            $age_band_arr=explode("-",$age_band);
                            $min_age=$age_band_arr[0];
                            $max_age=$age_band_arr[1];
                            $si_band = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                            $si_band_arr=explode("-",$si_band);
                            $min_si=$si_band_arr[0];
                            $max_si=$si_band_arr[1];
							if (!empty($premium)) {
								$dataexcel[] = array(
									'master_policy_id'   => $data['policy_sub_type_id'],
									'adult_count'  => $adult_count,
									'child_count'  => $child_count,
									'deductable'  => $deductable,
									'sum_insured'   => $sum_insured,
									'premium_rate'    => $premium,
									'is_taxable'  => $tax,
									'group_code'	=> $group_code,
									'group_code_spouse'	=> $group_code_spouse,
									'min_age'	=> $min_age,
									'max_age'	=> $max_age, 
									'min_si'	=> $min_si,
                                    'max_si'	=> $max_si,
									// 'is_absolute'  => $absolute
								);
							}
						}
					}




					// new added 
					$ip_address = $_SERVER['REMOTE_ADDR'];
					$created_by = $_SESSION['webpanel']['employee_id'];

					if ($data['sibasis'] == 5) {


                            for ($row = 2; $row <= $highestRow; $row++) {
                                $age_band = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                                $tenure = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                                $policy_rate = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                                $numbers_of_ci = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                                $group_code = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                                $group_code_spouse = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                                $is_taxable = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                                //$sum_insured = $worksheet->getCellByColumnAndRow(6, $row)->getValue();

                                if (trim($numbers_of_ci) == '') {
                                    $numbers_of_ci = 0;
                                }

                                if (trim($age_band) != '' and strlen($age_band) > 1) {

                                    $ages = preg_split('/(–|-)/', str_replace(' ', '', $age_band));

                                    $premium_min_age = $ages[0];
                                    $premium_max_age = $ages[1];

                                    $dataexcel[] = array(
                                        'master_policy_id'   => $data['policy_sub_type_id'],
                                        'age_band'   => $age_band,
                                        'min_age' => $premium_min_age,
                                        'max_age' => $premium_max_age,
                                        'tenure'   => $tenure,
                                        'policy_rate'   => $policy_rate,
                                        'numbers_of_ci'   => $numbers_of_ci,
                                        'ip_address'   => $ip_address,
                                        'created_by'   => $created_by,
                                        'group_code'	=> $group_code,
                                        'group_code_spouse'	=> $group_code_spouse,
                                        'is_taxable'	=> $is_taxable
                                        //'sum_insured' => $sum_insured
                                    );
                                }
                            }

					}

					if ($data['sibasis'] == 7) {

						for ($row = 2; $row <= $highestRow; $row++) {
							$tenure = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
							$sum_insured = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
							$premium = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
							$tax = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
							$group_code = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
							$group_code_spouse = $worksheet->getCellByColumnAndRow(5, $row)->getValue();

							if (!empty($premium)) {
								$dataexcel[] = array(
									'master_policy_id'   => $data['policy_sub_type_id'],
									'tenure'  => $tenure,
									'sum_insured'   => $sum_insured,
									'premium_rate'    => $premium,
									'is_taxable'  => $tax,
									'group_code'	=> $group_code,
									'group_code_spouse'	=> $group_code_spouse
								);
							}
						}
					}
				}

				$data['exceldata'] = json_encode($dataexcel);
			}else{
                $arr=array(3,4);
                if(in_array($policy_type_id,$arr)){
                    if ($data['sibasis'] == 5) {
                        $dataexcel[] = array(
                            'master_policy_id' => $data['policy_sub_type_id'],
                            'policy_rate' => $data['default_rate'],
                            //'sum_insured' => $sum_insured
                        );
                    }
                }
                $data['exceldata'] = json_encode($dataexcel);
            }
		} else {
		    if($data['sibasis'] == 1 ){
                $data['sum_insured_opt'] = implode(',', $this->input->post('sum_insured_opt1'));
                $data['premium_opt'] = implode(',', $this->input->post('premium_opt'));
                $data['group_code'] = implode(',', $this->input->post('group_code'));
                $data['group_code_spouse'] = implode(',', $this->input->post('group_code_spouse'));
                $data['tax_opt'] = implode(',', $this->input->post('tax_opt'));
            }else{
		    //    echo 123;die;
                if($this->input->post('policy_subtype_idNew') == 19){

                    $premium_type = $this->input->post('premium_type');
                	$premium_array=[];
                	foreach ($premium_type as $key => $value) {
                		$intra_pre['premium_type']='intra_'.$value;
                		$intra_pre['premium_rate']=$this->input->post('intra_'.$value);
                		$intra_pre['premium_with_tax']=$this->input->post('intra_gst_'.$value);
                		$premium_array[]=$intra_pre; 
						$inter_pre['premium_type']='inter_'.$value;
                		$inter_pre['premium_rate']=$this->input->post('inter_'.$value);
                		$inter_pre['premium_with_tax']=$this->input->post('inter_gst_'.$value);
                		$premium_array[]=$inter_pre; 

                	}
                   $data['premium_array']=json_encode($premium_array);
                }else {
                    $data['sum_insured_opt'] = implode(',', $this->input->post('sum_insured_per_opt1'));
                    $data['premium_opt'] = implode(',', $this->input->post('premium_Per_opt'));
                }
            }

		}
		//var_dump($data['sum_insured_opt']);die;
        if(!empty($this->input->post('member')) && count($this->input->post('member')) >0){
		$data['members'] = implode(',', $this->input->post('member'));
		$data['minage'] = implode(',', $this->input->post('minage'));
		$data['maxage'] = implode(',', $this->input->post('maxage'));
		$data['min_age_type'] = implode(',', $this->input->post('min_age_type'));
        }

      /*  ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/
        //print_r($data);
      //  print_r($data['exceldata']);
      // print_r(curlFunction(SERVICE_URL . '/api2/addNewPolicy', $data));die;
		$response = json_decode(curlFunction(SERVICE_URL . '/api2/addNewPolicy', $data), true);
      //  print_r($response);die;
		if ($response['status_code'] == '200') {
			echo json_encode(array('success' => true, 'msg' => $response['Metadata']['Message'], 'data' => $response['Data']));
			exit;
		} else {
			echo json_encode(array('success' => false, 'msg' => $response['Metadata']['Message']));
			exit;
		}
	}

    function getCoverageType()
    {
        $data['utoken'] = $_SESSION['webpanel']['utoken'];

        $data['coverage_type'] = $this->input->post('coverage_type');
        $data['policy_id'] = $this->input->post('policy_id');
	//	print_r(curlFunction(SERVICE_URL . '/api2/getCoverageDetails', $data));die;
        $response = json_decode(curlFunction(SERVICE_URL . '/api2/getCoverageDetails', $data), true);
        echo json_encode($response);
    }
    function getbusinessType()
    {
        $data['utoken'] = $_SESSION['webpanel']['utoken'];

        $data['business_type'] = $this->input->post('business_type');
        $data['policy_id'] = $this->input->post('policy_id');
        //	print_r(curlFunction(SERVICE_URL . '/api2/getBusinessDetails', $data));die;
        $response = json_decode(curlFunction(SERVICE_URL . '/api2/getBusinessDetails', $data), true);
        echo json_encode($response);
    }
	function UpdatePolicyNew()
	{

        //ini_set('display_errors', 1);
		$data = array();
		$this->load->library('excel');
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['policy_sub_type_id'] = $this->input->post('policySubType');
		$data['policy_subtype_idNew'] =$this->input->post('policy_subtype_idNew');
		$data['plan_id'] = $this->input->post('plan_id');
        $data['cover_limit'] = $this->input->post('cover_limit');
        $data['cover_initial'] = $this->input->post('cover_initial');
        $data['excess'] = $this->input->post('excess'); // new added

		$data['creditor_id'] = $this->input->post('creditor_id'); // new added
		$data['policy_number'] = $this->input->post('policyNo');
		$mandatory = $this->input->post('mandatory');
		$data['premium_type'] = $this->input->post('premium_type');
		$data['start_series'] = $this->input->post('start_series');
		$data['end_series'] = $this->input->post('end_series');
		//$absolute = $data['premium_type']; changes
        $policy_type_id=$this->gettypeid($data['plan_id']);
        $data['policy_type_id'] = $policy_type_id;
        //--all risk
        $data['cover_det']= $this->input->post('cover_det');
        $data['coverage_type']= $this->input->post('coverage_type');
        $data['policyTenureStartDate']= $this->input->post('policyTenureStartDate');
        $data['policyTenureEndDate']= $this->input->post('policyTenureEndDate');

		if ($mandatory == 1) {
			$data['is_optional'] = 0;
		} else {
			$data['is_optional'] = 1;
		}

		$combo = $this->input->post('combo');
		if ($combo == 1) {
			$data['is_combo'] = 1;
		} else {
			$data['is_combo'] = 0;
		}

		//$data['pdf_type'] = $this->input->post('pdf_type');
		$data['insurer_id'] = $this->input->post('masterInsurance');
		$data['policy_start_date'] = date('Y-m-d', strtotime($this->input->post('policyStartDate')));
		$data['policy_end_date'] = date('Y-m-d', strtotime($this->input->post('policyEndDate')));
		$data['plan_code'] = $this->input->post('plan_code');
		$data['product_code'] = $this->input->post('product_code');
		$data['scheme_code'] = $this->input->post('scheme_code');
		$data['source_name'] = $this->input->post('source_name');
		$data['max_member_count'] = $this->input->post('membercount');
		$data['max_insured_count'] = $this->input->post('max_mi');
		$data['mandatory_if_not_selected'] = $this->input->post('mandatory_if_not_selected');
		$data['adult_count'] = $this->input->post('adult_count');
		$data['child_count'] = $this->input->post('child_count');
        $data['cd_balance'] = $this->input->post('cd_balance');
        $data['threshold'] = $this->input->post('threshold');
		$data['sitype'] = $this->input->post('sum_insured_type'); // uncomment
        $data['is_adult_consider'] = $this->input->post('adult_consider'); // uncomment
        $data['business_type'] = $this->input->post('business_type');
        $data['per_sending_limit'] = $this->input->post('per_sending_limit');
        $data['per_location_limit'] = $this->input->post('per_location_limit');
        $data['b2c_type'] = $this->input->post('b2c_type');
        $data['per_sending_limit_b2c'] = $this->input->post('per_sending_limit_b2c');
        $data['per_location_limit_b2c'] = $this->input->post('per_location_limit_b2c');
		$data['sibasis'] = $this->input->post('companySubTypePolicy');
		$data['default_sumInsured'] = $this->input->post('default_sumInsured');
		$data['default_rate'] = $this->input->post('default_rate');
		$data['gadget_eligibilty'] = $this->input->post('gadget_eligibilty');
        if($data['sibasis'] == 5 && ($data['default_sumInsured'] == 0 || empty($data['default_sumInsured']))){
            echo json_encode(array('success' => false, 'msg' => "Default SumInsured is Compulsory!"));
            exit;
        }
        if($data['policy_sub_type_id']== 3 && $data['sibasis'] == 5 && ($data['default_rate'] == 0 || empty($data['default_rate']))){
            echo json_encode(array('success' => false, 'msg' => "Rate is Compulsory!"));
            exit;
        }
	//	var_dump($data);exit;
//echo $data['sibasis'];exit;
		if ($data['sibasis'] != 1  && $data['sibasis'] != 8) {
			if (isset($_FILES["ageFile"]["name"])) {
				$path = $_FILES["ageFile"]["tmp_name"];
				$object = PHPExcel_IOFactory::load($path);
				foreach ($object->getWorksheetIterator() as $worksheet) {
					$highestRow = $worksheet->getHighestRow();
					$highestColumn = $worksheet->getHighestColumn();
					if ($data['sibasis'] == 4) {
						for ($row = 2; $row <= $highestRow; $row++) {
							$min_age = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
							$max_age = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
							$sum_insured = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
							$premium = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
							$tax = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
							$group_code = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
							$group_code_spouse = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
							//echo $sum_insured;die;
                            if($tax == 1){
                                $taxRate=$premium*(18/100);
                                $premium_with_tax=$taxRate;
                            }
							$dataexcel[] = array(
								'master_policy_id'   => $data['policy_sub_type_id'],
								'min_age'  => $min_age,
								'max_age'  => $max_age,
								'sum_insured'   => $sum_insured,
								'premium_rate'    => $premium,
								'premium_with_tax'    => $premium_with_tax,
								'is_taxable'  => $tax,
								'group_code'	=> $group_code,
								'group_code_spouse'	=> $group_code_spouse
								//  'is_absolute'  => $absolute
							);
						}
					}
					if ($data['sibasis'] == 2) {
						for ($row = 2; $row <= $highestRow; $row++) {
							$adult_count = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
							$child_count = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
							$sum_insured = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
							$premium = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
							$tax = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
							$group_code = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
							$group_code_spouse = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
							$dataexcel[] = array(
								'master_policy_id'   => $data['policy_sub_type_id'],
								'adult_count'  => $adult_count,
								'child_count'  => $child_count,
								'sum_insured'   => $sum_insured,
								'premium_rate'    => $premium,
								'is_taxable'  => $tax,
								'group_code'	=> $group_code,
								'group_code_spouse'	=> $group_code_spouse
								// 'is_absolute'  => $absolute
							);
						}
					}

					if ($data['sibasis'] == 3) {
						for ($row = 2; $row <= $highestRow; $row++) {
							$adult_count = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
							$child_count = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
							$min_age = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
							$max_age = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
							$sum_insured = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
							$premium = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
							// $premium_with_tax = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
							$tax = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
							$group_code = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
							$group_code_spouse = $worksheet->getCellByColumnAndRow(8, $row)->getValue();

							$dataexcel[] = array(
								'master_policy_id'   => $data['policy_sub_type_id'],
								'adult_count'  => $adult_count,
								'child_count'  => $child_count,
								'min_age'  => $min_age,
								'max_age'  => $max_age,
								'sum_insured'   => $sum_insured,
								'premium_rate'    => $premium,
								'is_taxable'  => $tax,
								'group_code'	=> $group_code,
								'group_code_spouse'	=> $group_code_spouse
							);
						}
					}
					// new added 

					# type 6
					if ($data['sibasis'] == 6) {
						for ($row = 2; $row <= $highestRow; $row++) {
							$adult_count = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
							$child_count = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
							$deductable = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
							$sum_insured = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
							$premium = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
							$tax = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
							$group_code = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
							$group_code_spouse = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                            $age_band = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                            $age_band_arr=explode("-",$age_band);
                            $min_age=$age_band_arr[0];
                            $max_age=$age_band_arr[1];
                            $si_band = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                            $si_band_arr=explode("-",$si_band);
                            $min_si=$si_band_arr[0];
                            $max_si=$si_band_arr[1];
							if (!empty($premium)) {
								$dataexcel[] = array(
									'master_policy_id'   => $data['policy_sub_type_id'],
									'adult_count'  => $adult_count,
									'child_count'  => $child_count,
									'deductable'  => $deductable,
									'sum_insured'   => $sum_insured,
									'premium_rate'    => $premium,
									'is_taxable'  => $tax,
									'group_code'	=> $group_code,
									'group_code_spouse'	=> $group_code_spouse,
									'min_age'	=> $min_age,
									'max_age'	=> $max_age,
									'min_si'	=> $min_si,
                                    'max_si'	=> $max_si,
									// 'is_absolute'  => $absolute
								);
							}
						}
					}

					if ($data['sibasis'] == 7) {

						for ($row = 2; $row <= $highestRow; $row++) {
							$tenure = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
							$sum_insured = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
							$premium = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
							$tax = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
							$group_code = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
							$group_code_spouse = $worksheet->getCellByColumnAndRow(5, $row)->getValue();

							if (!empty($premium)) {
								$dataexcel[] = array(
									'master_policy_id'   => $data['policy_sub_type_id'],
									'tenure'  => $tenure,
									'sum_insured'   => $sum_insured,
									'premium_rate'    => $premium,
									'is_taxable'  => $tax,
									'group_code'	=> $group_code,
									'group_code_spouse'	=> $group_code_spouse
								);
							}
						}
					}

					$ip_address = $_SERVER['REMOTE_ADDR'];
					$created_by = $_SESSION['webpanel']['employee_id'];

					if ($data['sibasis'] == 5) {
						for ($row = 2; $row <= $highestRow; $row++) {
							$age_band = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
							$tenure = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
							$policy_rate = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
							$numbers_of_ci = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
							$group_code = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
							$group_code_spouse = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
							//$sum_insured = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                            $is_taxable = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
							if (trim($numbers_of_ci) == '') {
								$numbers_of_ci = 0;
							}

							if (trim($age_band) != '' and strlen($age_band) > 1) {

								$ages = preg_split('/(–|-)/', str_replace(' ', '', $age_band));

								$premium_min_age = $ages[0];
								$premium_max_age = $ages[1];

								$dataexcel[] = array(
									'master_policy_id'   => $data['policy_sub_type_id'],
									'age_band'   => $age_band,
									'min_age' => $premium_min_age,
									'max_age' => $premium_max_age,
									'tenure'   => $tenure,
									'policy_rate'   => $policy_rate,
									'numbers_of_ci'   => $numbers_of_ci,
									'ip_address'   => $ip_address,
									'created_by'   => $created_by,
									'group_code'	=> $group_code,
									'group_code_spouse'	=> $group_code_spouse,
									'is_taxable'	=> $is_taxable,
									//'sum_insured' => $sum_insured
								);
							}
						}
					}
				}
				$data['exceldata'] = json_encode($dataexcel);
			} else {
                $arr=array(3,4);
                if(in_array($policy_type_id,$arr)){
                    if ($data['sibasis'] == 5) {
                        $dataexcel[] = array(
                            'master_policy_id' => $data['policy_sub_type_id'],
                            'policy_rate' => $data['default_rate'],
                            //'sum_insured' => $sum_insured
                        );
                    }
                }
                $data['exceldata'] = json_encode($dataexcel);
			}
		} else {
		 //   echo 1234;die;

            if($data['sibasis'] == 1 ){
                $data['sum_insured_opt'] = implode(',', $this->input->post('sum_insured_opt1'));
                $data['premium_opt'] = implode(',', $this->input->post('premium_opt'));
                $data['group_code'] = implode(',', $this->input->post('group_code'));
                $data['group_code_spouse'] = implode(',', $this->input->post('group_code_spouse'));
                $data['tax_opt'] = implode(',', $this->input->post('tax_opt'));
            }else{
                //    echo 123;die;
          //     echo $data['policy_sub_type_id'];
                if($this->input->post('policy_subtype_idNew') == 19){

                    $premium_type = $this->input->post('premium_type');
                	$premium_array=[];
                	foreach ($premium_type as $key => $value) {
                		$intra_pre['premium_type']='intra_'.$value;
                		$intra_pre['premium_rate']=$this->input->post('intra_'.$value);
                		$intra_pre['premium_with_tax']=$this->input->post('intra_gst_'.$value);
                		$premium_array[]=$intra_pre; 
						$inter_pre['premium_type']='inter_'.$value;
                		$inter_pre['premium_rate']=$this->input->post('inter_'.$value);
                		$inter_pre['premium_with_tax']=$this->input->post('inter_gst_'.$value);
                		$premium_array[]=$inter_pre; 

                	}
                    $data['premium_array']=json_encode($premium_array);
                }else{
                    $data['sum_insured_opt'] = implode(',', $this->input->post('sum_insured_per_opt1'));
                    $data['premium_opt'] = implode(',', $this->input->post('premium_Per_opt'));
                }

            }

        }
		if(!empty($this->input->post('member'))){
        if(count($this->input->post('member')) >0) {
            $data['members'] = implode(',', $this->input->post('member'));
            $data['minage'] = implode(',', $this->input->post('minage'));
            $data['maxage'] = implode(',', $this->input->post('maxage'));
            $data['min_age_type'] = implode(',', $this->input->post('min_age_type'));
        }
}
//print_r(curlFunction(SERVICE_URL . '/api2/updateNewPolicy', $data));die;
//print_r( $data);die;
		$response = json_decode(curlFunction(SERVICE_URL . '/api2/updateNewPolicy', $data),true);
		// print_r($response);die;
		if ($response['status_code'] == '200') {
			echo json_encode(array('success' => true, 'msg' => $response['Metadata']['Message'], 'data' => $response['Data']));
			exit;
		} else {
			echo json_encode(array('success' => false, 'msg' => $response['Metadata']['Message']));
			exit;
		}
	}
	function UpdatePolicy()
	{
		$data = array();
		$this->load->library('excel');
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['policy_sub_type_id'] = $this->input->post('policySubType');
		$data['plan_id'] = $this->input->post('plan_id');
		$data['policy_number'] = $this->input->post('policyNo');
		$mandatory = $this->input->post('mandatory');
		$data['premium_type'] = $this->input->post('premium_type');
		$absolute = $data['premium_type'];

		if ($mandatory == 1) {
			$data['is_optional'] = 0;
		} else {
			$data['is_optional'] = 1;
		}
		$combo = $this->input->post('combo');

		if ($combo == 1) {
			$data['is_combo'] = 1;
		} else {
			$data['is_combo'] = 0;
		}

		// $data['pdf_type'] = $this->input->post('pdf_type');
		$data['insurer_id'] = $this->input->post('masterInsurance');
		$data['policy_start_date'] = date('Y-m-d', strtotime($this->input->post('policyStartDate')));
		$data['policy_end_date'] = date('Y-m-d', strtotime($this->input->post('policyEndDate')));
		$data['max_member_count'] = $this->input->post('membercount');
		$data['max_insured_count'] = $this->input->post('max_mi');

        $data['sitype'] = $this->input->post('sum_insured_type');
		$data['sibasis'] = $this->input->post('companySubTypePolicy');
		if ($data['sibasis'] != 1) {
			if (isset($_FILES["ageFile"]["name"])) {
				$path = $_FILES["ageFile"]["tmp_name"];
				$object = PHPExcel_IOFactory::load($path);
				foreach ($object->getWorksheetIterator() as $worksheet) {
					$highestRow = $worksheet->getHighestRow();
					$highestColumn = $worksheet->getHighestColumn();

					if ($data['sibasis'] == 4) {
						for ($row = 2; $row <= $highestRow; $row++) {
							$min_age = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
							$max_age = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
							$sum_insured = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
							$premium = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
							$tax = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
							$dataexcel[] = array(
								'master_policy_id'   => $data['policy_sub_type_id'],
								'min_age'  => $min_age,
								'max_age'  => $max_age,
								'sum_insured'   => $sum_insured,
								'premium_rate'    => $premium,
								'is_taxable'  => $tax,
								'is_absolute'  => $absolute
							);
						}
					}

					if ($data['sibasis'] == 2) {
						for ($row = 2; $row <= $highestRow; $row++) {
							$adult_count = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
							$child_count = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
							$sum_insured = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
							$premium = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
							$tax = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
							$dataexcel[] = array(
								'master_policy_id'   => $data['policy_sub_type_id'],
								'adult_count'  => $adult_count,
								'child_count'  => $child_count,
								'sum_insured'   => $sum_insured,
								'premium_rate'    => $premium,
								'is_taxable'  => $tax,
								'is_absolute'  => $absolute
							);
						}
					}

					if ($data['sibasis'] == 3) {
						for ($row = 2; $row <= $highestRow; $row++) {
							$adult_count = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
							$child_count = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
							$min_age = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
							$max_age = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
							$sum_insured = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
							$premium = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
							$tax = $worksheet->getCellByColumnAndRow(6, $row)->getValue();

							$dataexcel[] = array(
								'master_policy_id'   => $data['policy_sub_type_id'],
								'adult_count'  => $adult_count,
								'child_count'  => $child_count,
								'min_age'  => $min_age,
								'max_age'  => $max_age,
								'sum_insured'   => $sum_insured,
								'premium_rate'    => $premium,
								'is_taxable'  => $tax,
							);
						}
					}

					// new added 
					$ip_address = $_SERVER['REMOTE_ADDR'];
					$created_by = $_SESSION['webpanel']['employee_id'];

					if ($data['sibasis'] == 5) {
						for ($row = 2; $row <= $highestRow; $row++) {
							$age_band = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
							$tenure = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
							$policy_rate = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
							$numbers_of_ci = $worksheet->getCellByColumnAndRow(3, $row)->getValue();

							if (trim($numbers_of_ci) == '') {
								$numbers_of_ci = 0;
							}

							if (trim($age_band) != '' and strlen($age_band) > 1) {
								$dataexcel[] = array(
									'master_policy_id'   => $data['policy_sub_type_id'],
									'age_band'   => $age_band,
									'tenure'   => $tenure,
									'policy_rate'   => $policy_rate,
									'numbers_of_ci'   => $numbers_of_ci,
									'ip_address'   => $ip_address,
									'created_by'   => $created_by
								);
							}
						}
					}
				}
				$data['exceldata'] = json_encode($dataexcel);
			}
		} else {
			$data['sum_insured_opt'] = implode(',', $this->input->post('sum_insured_opt1'));
			$data['premium_opt'] = implode(',', $this->input->post('premium_opt'));
			$data['tax_opt'] = implode(',', $this->input->post('tax_opt'));
		}
		$data['members'] = implode(',', $this->input->post('member'));
		$data['minage'] = implode(',', $this->input->post('minage'));
		$data['maxage'] = implode(',', $this->input->post('maxage'));
		$response = json_decode(curlFunction(SERVICE_URL . '/api2/UpdatePolicy', $data), true);
		if ($response['status_code'] == '200') {
			echo json_encode(array('success' => true, 'msg' => $response['Metadata']['Message'], 'data' => $response['Data']));
			exit;
		} else {
			echo json_encode(array('success' => false, 'msg' => $response['Metadata']['Message']));
			exit;
		}
	}
	function addpolicyview($id)
	{
		$data2['utoken'] = $_SESSION['webpanel']['utoken'];

		$data['default_policy_start_date'] = date('d-m-Y');
		$data['default_policy_end_date'] = date('d-m-Y', strtotime(date("Y-m-d", time()) . " + 365 day"));

		$data2['id'] = $id;
		$data['datalist'] = json_decode(curlFunction(SERVICE_URL . '/api2/getproductDetails', $data2));
		//var_dump($data['datalist']);die;
		$html = $this->load->view('products/addNew_load', $data); // changes 
		echo $html;
	}

	function updatepolicyview($id, $policy_id = '')
	{
		$data2['utoken'] = $_SESSION['webpanel']['utoken'];
		$data2['id'] = $id;
		if (!empty($policy_id)) {
			$data2['policy_id'] = $policy_id;
		}
		$data['default_policy_start_date'] = date('d-m-Y');
		$data['default_policy_end_date'] = date('d-m-Y', strtotime(date("Y-m-d", time()) . " + 365 day"));

		$data['datalist'] = json_decode(curlFunction(SERVICE_URL . '/api2/getPolicyUpdateDetails', $data2));
		//echo SERVICE_URL . '/api2/getPolicyUpdateDetails/'; 	$data['sipremiumbasis']
//	print_r($data['datalist']->sipremiumbasis);exit;
        if(!empty($data['datalist']->premium_basis[0]->si_premium_basis_id)){
            $d=array("rater_id"=>$data['datalist']->premium_basis[0]->si_premium_basis_id,"policy_id"=>$policy_id);
            $data['premiumData'] = json_decode(curlFunction(SERVICE_URL . '/api2/getPremiumDataedit', $d));
//print_r($data['premiumData']);die;
        }
		$data['policyview'] = 1;
		$html = $this->load->view('products/edit_load', $data); // changes 
		echo $html;
	}

	function checkplanname($id = null)
	{
		$data2['utoken'] = $_SESSION['webpanel']['utoken'];
		$data2['plan'] = $this->input->post('name');
		if (!empty($id)) {
			$data2['id'] = $id;
		}
		$response = json_decode(curlFunction(SERVICE_URL . '/api2/checkplanname', $data2), true);
		if ($response['status_code'] == '200') {
			echo json_encode(array('success' => true, 'msg' => $response['Metadata']['Message']));
			exit;
		} else {
			echo json_encode(array('success' => false, 'msg' => $response['Metadata']['Message']));
			exit;
		}
	}

	function checkpolicynumber($id = null)
	{
		$data2['utoken'] = $_SESSION['webpanel']['utoken'];
		$data2['policy'] = $this->input->post('name');
		if (!empty($id)) {
			$data2['id'] = $id;
		}
		$response = json_decode(curlFunction(SERVICE_URL . '/api2/checkpolicynumber', $data2), true);
		if ($response['status_code'] == '200') {
			echo json_encode(array('success' => true, 'msg' => $response['Metadata']['Message']));
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
		$delRecord = curlFunction(SERVICE_URL . '/api2/delProduct', $data);
		//echo "<pre>";print_r($checkDetails);exit;
		$delRecord = json_decode($delRecord, true);

		if ($delRecord['status_code'] == '200') {
			echo "1";
		} else {
			echo "2";
		}
	} // EO delRecord()
    function getpolicysubtypeName(){
	    $policy_subtype_id=$this->input->post('policy_subtype_id');
	    $query=$this->db->query("select policy_sub_type_id,policy_sub_type_name from master_policy_sub_type where policy_sub_type_id in (".$policy_subtype_id.")")->result();
	    $response['data']=$query;
	    echo json_encode($response);
	    exit;
    }
    function updatepolicysubtyefeature(){
        $plan_id=$this->input->post('plan_id');
        $checked=$this->input->post('checked');
        $implode=implode(",",$checked);
        //print_r($implode);die;
        $policysubtypeid=$this->input->post('policysubtypeid');
        $where=array('policy_sub_type_id'=>$policysubtypeid,'plan_id'=>$plan_id);
        $this->db->where($where);
        $update=$this->db->update('master_policy',array('feature_id'=>$implode));

        if($update){
            echo "1";
        }else{
            echo "2";
        }
    }
    function getFeatures(){
        $policysubtypeid=$this->input->post('id');
        $plan_id=$this->input->post('plan_id');
        $query=$this->db->query("select feature_id   from master_policy where policy_sub_type_id=".$policysubtypeid." and plan_id=".$plan_id)->row();
        $feature_id=$query->feature_id;
        $query=$this->db->query("select * from compare_features where is_active=1 and policy_sub_type_id=".$policysubtypeid)->result();
        if($this->db->affected_rows()  > 0){
            $response['status']=200;
            $response['data']=$query;
            $response['feature_id']=$feature_id;
        }else{
            $response['status']=201;
        }echo json_encode($response);exit;
    }
    function getProductIDapi(){

        $plan_id=$this->input->post('plan_id');
        $query=$this->db->query("select policy_id,policy_sub_type_id,
 (select policy_type_id from master_policy_sub_type mpst where mpst.policy_sub_type_id=mp.policy_sub_type_id) as policy_type_id,
 (select plan_code from master_policy_sub_type mpst where mpst.policy_sub_type_id=mp.policy_sub_type_id) as plan_code,
 (select plan_name from master_plan mpp where mpp.plan_id=mp.plan_id) as plan_name,
 (select creaditor_name from master_ceditors mc where mc.creditor_id=mp.creditor_id) as creaditor_name,
 (select code from master_policy_sub_type mpst where mpst.policy_sub_type_id=mp.policy_sub_type_id) as code
 from master_policy mp where isactive = 1 and plan_id=".$plan_id)->result();


        // echo 123;die;
        $api='';
        $plan_name='';
        $creditorname='';
        if($this->db->affected_rows() > 0) {
            $aapi='';
            $h_aapi='';
            foreach ($query as $row){
                $plan_name=$row->plan_name;
                $creaditor_name=$row->creaditor_name;
                if($row->policy_type_id == 1){
                    $h_aapi .=' <br> {"PlanCode":'.$row->plan_code.',"SumInsured":"500000","Shortcode":"'.$row->code.'","Premium":"300"},';
                }	elseif($row->policy_type_id == 5){
					$api = '{<br>
						<br>"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzOjE2OTA1NjQ1Njd9.3ftL-mPhnJUL04i_-Q1RSGBwQXi_XI86rO5WAZhg7tk",
						<br>"userId":"46",
						<br>"Plan_id":'.$plan_id.',
						<br>"Customer_ID": "123456",
						<br>"Insurer_Name": "HDFC",
						<br>"Cust_Salutation":"",
						<br>"Cust_First_Name":"Amit",
						<br>"Cust_Middle_Name":"",
						<br>"Cust_Last_Name":"Matani",
						<br>"Cust_Mobile_No":"8850525214",
						<br>"Cust_email_ID":"amit@gmail.com",
						<br>"Cust_DOB":"",
						<br>"Cust_Gender":"",
						<br>"Cust_Pincode":"410210",
						<br>"Cust_Add1": "abcd123-",
						<br>"Cust_Add2": "",
						<br>"Sum_Insured":"200000",
						<br>"Invoice_Amount": "200000",
						<br>"Policy_Start_Date": "27-09-2023",
						<br>"Policy_End_Date": "26-09-2024",
						<br>"Product_Name": "Jewellery",
						<br>"Master_Policy_No": "25996555",
						<br>"Invoice_No": "782656",
						<br>"Invoice_Date": "27-09-2023" ,
						<br>"Policy_Tenure":"",
						<br>"Nominee_first_name":"",
						<br>"Nominee_last_name": "",
						<br>"Nominee_Relation": "" ,
						<br>"Nominee_DOB":"",
						<br>"Nominee_Mobile": "",
						<br>"Trace_Id": "56545",
						<br>"Lead_Id": "789864"
				
				}';

				}else if($row->policy_type_id == 3) {
                    if (count($query) == 1) {
                        if ($row->policy_sub_type_id == 19) {
                            $api = '
                    { <br>
  "ClientCreation": {
     <br>  "token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6IjQ2IiwiaWF0IjoxNjg",
    <br>"Api_type": "Issuance",
    <br>"Invoice_number": "GHTd5690678", 
	<br>"plan_id": ' . $plan_id . ',
    <br>"policy_id": ' . $row->policy_id . ',
	<br>"salutation": "Mr",
	<br>"first_name": "AmitSir",
	<br>"middle_name": "",
	<br>"last_name": "MataniT",
	<br>"gender": "Male",
	<br>"mobile_number": "8793164535",
   <br> "pincode": "410203",
    <br>"Address1": "Address1",
    <br>"Address2": "Address2",
    <br>"Address3": "Address3",
	<br>"email_id": "poojalote123@gmail.com",
    <br>"mode_of_shipment": "xyz",
    <br>"from_country": "xyz",
    <br>"to_country": "xyz",
    <br>"from_city": "xyz",
    <br>"to_city": "xyz",
    <br>"type_of_shipment": "Inter",
    <br>"currency_type": "INR",
    <br>"cargo_value": "xyz",
    <br>"rate_of_exchange": "xyz",
    <br>"date_of_shipment": "03-04-2023",
    <br>"Bill_number": "xyz123",
   <br> "Bill_date": "03-04-2023",
    <br>"credit_number": "xyz",
    <br>"credit_description": "xyz",
    <br>"place_of_issuence": "xyz",
    <br>"Invoice_date": "03-04-2023",
   <br> "subject_matter_insured": "Household Goods",
   <br> "marks_number": "xyz",
   <br> "vessel_name": "xyz",
    <br>"Consignee_name": "xyz",
   <br> "Consignee_add": "xyz",
   <br> "Financier_name": "xyz",
   <br> "SumInsured": "200000",
   <br> "userId":"47"
 <br>  }
 <br>}
                    ';
                        }
					
                        else{
                            $api='{<br>
  "ClientCreation": {<br>
    "partner": "'.$creaditor_name.'",
	<br>"plan_id": '.$plan_id.',
	<br>"salutation": "Mr",
	<br>"first_name": "Amit",
	<br>"middle_name": "",
	<br>"last_name": "Matani",
	<br>"gender": "Male",
	<br>"email_id": "poojalote123@gmail.com",
	<br>"mobile_number": "8793164535",
   <br> "make_model": "fgt56565",
   <br> "gadget_purchase_date": "2023-03-20",
   <br> "gadget_purchase_price": 54200,
	<br>"tenure": "1",
    <br>"userId":"46"
   
  <br>},
  <br>"QuoteRequest": {
   <br>    "SumInsuredData":
   <br> [{"PlanCode":'.$row->plan_code.',"SumInsured":"500000","Shortcode":"'.$row->code.'"}
   <br> ]
   <br>   },
 
  <br>"ReceiptCreation": {
  <br>  "modeOfEntry": "Direct",
  <br>  "PaymentMode": "1",
  <br>  "bankName": "",
  <br>  "branchName": "",
  <br>  "bankLocation": "",
  <br>  "chequeType": "",
  <br>  "ifscCode": ""
  
 <br> },
 
 <br>"PolicyCreationRequest": {
 
  <br>  "TransactionNumber": "Pay_kbToSSUXXtt",
   <br> "TransactionRcvdDate": "2011-11-10",
   <br> "PaymentMode": "Payment Gateway"
  
 <br> }

<br>}';
                        }
                    }else{

                        $aapi .=' <br> {"PlanCode":'.$row->plan_code.',"SumInsured":"500000","Shortcode":"'.$row->code.'"},';
                    }
                }
            }
            if(!empty($aapi)){

                $gadgetapi=  $this->gadgetapi($creaditor_name,$plan_id);
                //print_r($gadgetapi);die;
                $aapi=rtrim($aapi,",");
                $api=   $gadgetapi[0].$aapi.$gadgetapi[1];
            }

            if(!empty($h_aapi)){
                $healthapi=  $this->healthapi($plan_name,$creaditor_name);
                $h_aapi=rtrim($h_aapi,",");
                $api=   $healthapi[0].$h_aapi.$healthapi[1];
            }
            $response['api']=$api;
            echo json_encode($response);
        }
    }
    function healthapi($plan_name,$creaditor_name){
        $healthapi1='{
    <br>"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6IjQ2IiwiaWF0Ijo",
  <br>"ClientCreation": {
  <br>"partner": "'.$creaditor_name.'",
	<br>"plan": "'.$plan_name.'",
	<br>"unique_id": "12456",
	<br>"salutation": "Mr",
	<br>"first_name": "Amihjt",
	<br>"middle_name": "",
	<br>"last_name": "Matani",
	<br>"gender": "Male",
	<br>"dob": "03-11-2004",
	<br>"email_id": "poojalote123@gmail.com",
	<br>"mobile_number": "8793164535",
	<br>"tenure": "1",
	<br>"is_coapplicant": "No",
	<br>"coapplicant_no": "",
   <br> "userId":"46",
	<br>"sm_location": "Mumbai",
    <br>"alternateMobileNo": null,
  <br>  "homeAddressLine1": "Om hrad ntalatiof, holOmshre, talathioficeeachol, OmshreeSada9talathi",
   <br> "homeAddressLine2": null,
   <br> "homeAddressLine3": null,
   <br>"pincode": "410209"
   
 <br> },
 <br> "QuoteRequest": {
 <br>"NoOfLives":"1",   
   <br> "adult_count": "1",
   <br> "child_count": "0",
    <br>"LoanDetails":{
<br>"LoanDisbursementDate":"03-04-2023",
<br>"LoanAmount":"3000",
<br>"LoanAccountNo":"898989898",
<br>"LoanTenure":"1"
<br>},
   <br> "SumInsuredData":
 <br>  [';
        $healthapi2=' <br>]
    
     <br> },
  <br>"MemObj": {
  <br>  "Member": [
  <br>    {
   <br>     "MemberNo": 1,
   <br>     "Salutation": "Mr",
     <br>   "First_Name": "praghghkash",
     <br>   "Middle_Name": null,
     <br>   "Last_Name": "k abhi axis",
     <br>   "Gender": "M",
     <br>   "DateOfBirth": "23-11-1991",
     <br>   "Relation_Code": "1"
     <br> }
   <br> ]
 <br> },
 <br> "ReceiptCreation": {
 <br>   "modeOfEntry": "Direct",
 <br>   "PaymentMode": "4",
  <br>  "bankName": "",
  <br>  "branchName": "",
  <br>  "bankLocation": "",
  <br>  "chequeType": "",
    <br>"ifscCode": ""
  
<br>  },
 <br> "Nominee_Detail":{
   <br>   "Nominee_First_Name": "gfgh",
   <br>     "Nominee_Last_Name": "gfg",
   <br>     "Nominee_Contact_Number": "8793164535",
   <br>     "Nominee_Home_Address": null,
    <br>    "Nominee_gender": "M",
    <br>    "Nominee_dob": "03-04-2023",
    <br>    "Nominee_Salutation": "Mr",
    <br>    "Nominee_Email": "pooja@gmail.com",
     <br>   "Nominee_Relationship_Code": "1"
  <br>},
 <br>"PolicyCreationRequest": {
 
  <br>  "TransactionNumber": "Pay_kbToSSUXXtt",
  <br>  "TransactionRcvdDate": "23-11-2023",
  <br>  "CollectionAmount":"3000",
  <br>  "PaymentMode": "CD Balance"
  
  <br>}

<br>}';
        return array($healthapi1,$healthapi2);
    }
    function gadgetapi($creaditor_name,$plan_id){
        $gadegetapi='{<br>
  "ClientCreation": {<br>
    "partner": "'.$creaditor_name.'",
	<br>"plan_id": '.$plan_id.',
	<br>"salutation": "Mr",
	<br>"first_name": "Amit",
	<br>"middle_name": "",
	<br>"last_name": "Matani",
	<br>"gender": "Male",
	<br>"email_id": "poojalote123@gmail.com",
	<br>"mobile_number": "8793164535",
   <br> "make_model": "fgt56565",
   <br> "gadget_purchase_date": "2023-03-20",
   <br> "gadget_purchase_price": 54200,
	<br>"tenure": "1",
    <br>"userId":"46"
   
  <br>}
  <br>"QuoteRequest": {
   <br>    "SumInsuredData":[';

        $gadegetapi1='<br>   ]},
 
  <br>"ReceiptCreation": {
  <br>  "modeOfEntry": "Direct",
  <br>  "PaymentMode": "1",
  <br>  "bankName": "",
  <br>  "branchName": "",
  <br>  "bankLocation": "",
  <br>  "chequeType": "",
  <br>  "ifscCode": ""
  
 <br> },
 
 <br>"PolicyCreationRequest": {
 
  <br>  "TransactionNumber": "Pay_kbToSSUXXtt",
   <br> "TransactionRcvdDate": "2011-11-10",
   <br> "PaymentMode": "Payment Gateway"
  
 <br> }

<br>}';
        return array($gadegetapi,$gadegetapi1);
    }

    function mappedWithNewPartner(){
	    $creditor=$this->input->post('creditor');
	    $plan_id=$this->input->post('plan_id');
	    $get_plan_data=$this->db->query("select * from master_plan where plan_id=".$plan_id)->row_array();
        unset($get_plan_data['plan_id']);
        $policy_type_id=$get_plan_data['policy_type_id'];
        $get_plan_data['creditor_id']=$creditor;
        $plan_insert=$this->db->insert('master_plan',$get_plan_data);
        $new_plan_id = $this->db->insert_id();
        $arr=array(3,4);
        $get_policy_details=$this->db->query("select * from master_policy where plan_id=".$plan_id)->result_array();

        foreach ($get_policy_details as $policy){
            $old_policy_id=$policy['policy_id'];
            unset($policy['policy_id']);
            $policy['plan_id']=$new_plan_id;
            $policy['creditor_id']=$creditor;
            $policy_insert=$this->db->insert('master_policy',$policy);
            $new_policy_id = $this->db->insert_id();

            //-----------Rules Data Insert----------------
            $rulesData=$this->db->query("select * from master_policy_mandatory_if_not_selected_rules where isactive=1 and master_policy_id=".$old_policy_id)->row_array();
            unset($rulesData['id']);
            $rulesData['master_policy_id']=$new_policy_id;
            $ruleInsert=$this->db->insert('master_policy_mandatory_if_not_selected_rules',$rulesData);


            if(!in_array($policy_type_id,$arr)) {
            //-------------Family Construct------------
            $familyConstructData=$this->db->query("select * from master_policy_family_construct where isactive=1 and master_policy_id=".$old_policy_id)->result_array();
            foreach ($familyConstructData as $family){
                unset($family['family_construct_id']);
                $family['master_policy_id']=$new_policy_id;
                $familyConstructInsert=$this->db->insert('master_policy_family_construct',$family);
            }
            //-----------Policy SI type mapping---------
                $siTypeMapping = $this->db->query("select * from master_policy_si_type_mapping where isactive=1 and master_policy_id=" . $old_policy_id)->row_array();
                  if($siTypeMapping){
                      unset($siTypeMapping['si_type_mapping_id']);
                      $siTypeMapping['master_policy_id'] = $new_policy_id;
                      $siTypeInsert=$this->db->insert('master_policy_si_type_mapping',$siTypeMapping);
                  }

            }

            //--------------Premium Basis mapping--------------
            $premiumBasisMapping=$this->db->query("select * from master_policy_premium_basis_mapping where isactive=1 and master_policy_id=" . $old_policy_id)->row_array();
            if($premiumBasisMapping){
                unset($premiumBasisMapping['mapping_id']);
                $basis_id=$premiumBasisMapping['si_premium_basis_id'];
                $premiumBasisMapping['master_policy_id'] = $new_policy_id;
                $premiumInsert=$this->db->insert('master_policy_premium_basis_mapping',$premiumBasisMapping);
            }


            //Premium calculation insert
            if ($basis_id == 5) {
                $querypermile=$this->db->query("select * from master_policy_premium_permile where isactive=1 and master_policy_id=".$old_policy_id)->result_array();
                foreach ($querypermile as $permile){
                    unset($permile['id']);
                    $permile['master_policy_id'] = $new_policy_id;
                    $premiumInsert=$this->db->insert('master_policy_premium_permile',$permile);
                }
                $query=$this->db->query("select * from master_policy_premium where isactive=1 and master_policy_id=".$old_policy_id)->result_array();
                foreach ($query as $item){
                    unset($item['policy_premium_id']);
                    $item['master_policy_id'] = $new_policy_id;
                    $Insert=$this->db->insert('master_policy_premium',$item);
                }
            }else if ($basis_id == 7) {
                $query=$this->db->query("select * from master_per_day_tenure_premiums where isactive=1 and master_policy_id=".$old_policy_id)->result_array();
                foreach ($query as $item){
                    unset($item['id']);
                    $item['master_policy_id'] = $new_policy_id;
                    $Insert=$this->db->insert('master_per_day_tenure_premiums',$item);
                }
            }else if ($basis_id == 8) {
                $query=$this->db->query("select * from master_policy_premium where isactive=1 and master_policy_id=".$old_policy_id)->result_array();
                foreach ($query as $item){
                    unset($item['policy_premium_id']);
                    $item['master_policy_id'] = $new_policy_id;
                    $Insert=$this->db->insert('master_policy_premium',$item);
                }
            }else if ($basis_id != 1) {
                $query=$this->db->query("select * from master_policy_premium where isactive=1 and master_policy_id=".$old_policy_id)->result_array();
                foreach ($query as $item){
                    unset($item['policy_premium_id']);
                    $item['master_policy_id'] = $new_policy_id;
                    $Insert=$this->db->insert('master_policy_premium',$item);
                }
            }else{
                $query=$this->db->query("select * from master_policy_premium where isactive=1 and master_policy_id=".$old_policy_id)->result_array();
                foreach ($query as $item){
                    unset($item['policy_premium_id']);
                    $item['master_policy_id'] = $new_policy_id;
                    $Insert=$this->db->insert('master_policy_premium',$item);
                }
            }
        }
        //-----------Payment Mode Data Insert----------------
        $plan_payment_mode=$this->db->query("select * from plan_payment_mode where master_plan_id=".$plan_id)->result_array();
        foreach ($plan_payment_mode as $payment){
            unset($payment['id']);
            $payment['master_plan_id']=$new_plan_id;
            $payment_insert=$this->db->insert('plan_payment_mode',$payment);
        }

        echo json_encode(true);



	}

	function DownloadSampleApiExcel($plan_id=''){
	    $get_family_construct=$this->db->query("select id,member_type from family_construct where isactive=1")->result();
	    $family_construct=array();
	    foreach ($get_family_construct as $item){
            $family_construct[$item->id]=$item->member_type;
        }
	    $query_get_details=$this->db->query("select mp.plan_id,mc.creaditor_name,mp.plan_name,mpp.policy_id,mp.policy_type_id,mpp.policy_sub_type_id,mpp.creditor_id
,mpst.code,mpst.plan_code,
(select group_concat(mpfc.member_type_id) from master_policy_family_construct mpfc where mpfc.master_policy_id=mpp.policy_id and mpfc.isactive=1) as member_type
 from master_plan mp
join master_policy mpp on mpp.plan_id=mp.plan_id and mpp.isactive=1
join master_policy_sub_type mpst on mpst.policy_sub_type_id=mpp.policy_sub_type_id
join master_ceditors mc on mc.creditor_id=mp.creditor_id
 where mp.plan_id=".$plan_id);
	 //   echo $this->db->last_query();die;
        $creaditor_name='';
        $plan_name='';
        $code_array=array();
        $members=array();
	    if($this->db->affected_rows() > 0){
	        $data=$query_get_details->result();
	        $creaditor_name=$data[0]->creaditor_name;
	        $plan_name=$data[0]->plan_name;
	        foreach ($data as $row){
	            if(!empty($row->member_type) && !is_null($row->member_type)){
                    $code_array[]=$row->code."(".$row->plan_code.")";
                    $members[]=$row->member_type;
                }
            }
        }
	   $family_array=array();

        $members=array_unique($members);

	    foreach ($members as $mem){
	        $arr=explode(",",$mem);
	        foreach ($arr as $a){
                $family_array[]=$family_construct[$a];
            }
        }
        $family_array=array_unique($family_array);
	    $implode_fam_arr=implode(",",$family_array);
	    $excel_coulumns=array(
	        'partner','plan','salutation','first_name','middle_name','last_name','gender','dob','email_id','mobile_number',
            'tenure','is_coapplicant','coapplicant_no','userId','sm_location','alternateMobileNo','homeAddressLine1','homeAddressLine2',
            'homeAddressLine3','pincode','NoOfLives','adult_count','child_count','MemberNo','Salutation','First_Name',
            'Middle_Name','Last_Name','Gender','DateOfBirth','Relation_Code','LoanDisbursementDate','LoanAmount',
            'LoanAccountNo','LoanTenure','modeOfEntry','PaymentMode','bankName','branchName','bankLocation','chequeType',
            'ifscCode','Nominee_First_Name','Nominee_Last_Name','Nominee_Contact_Number','Nominee_Home_Address','Nominee_gender',
            'Nominee_Salutation','Nominee_Email','Nominee_Relationship_Code','TransactionNumber','TransactionRcvdDate','CollectionAmount','PaymentMode'
        );
	    foreach ($code_array as $code){
	        array_push($excel_coulumns,$code." SumInsure");
	        array_push($excel_coulumns,$code." Premium");
        }
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $filename=$plan_name."_SampleFile.xls";
        $cnt=1;
        $char1='A';
        foreach ($excel_coulumns as $column){
            if($char1 == "AE"){
                $objValidation = $objPHPExcel->getActiveSheet()->getCell('AE'.$cnt)->getDataValidation();
                $objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
                $objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
                $objValidation->setAllowBlank(false);
                $objValidation->setShowInputMessage(true);
                $objValidation->setShowErrorMessage(true);
                $objValidation->setShowDropDown(true);
                $objValidation->setErrorTitle('Input error');
                $objValidation->setError('Value is not in list.');
                $objValidation->setPromptTitle('Pick from list');
                $objValidation->setPrompt('Please pick a value from the drop-down list.');
                $objValidation->setFormula1('"'.$implode_fam_arr.'"');
            }else{
                $objPHPExcel->getActiveSheet()->SetCellValue($char1 . $cnt, $column);
            }

            $char1++;
        }
        $objPHPExcel->getActiveSheet()->SetCellValue("A" . 2, $creaditor_name);
        $objPHPExcel->getActiveSheet()->SetCellValue("B" . 2, $plan_name);
        ob_end_clean();
        $filename=FCPATH."assets/SampleExcel.xlsx";
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(str_replace(__FILE__,  $filename, __FILE__));

        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=".$plan_name."_SampleExcel.xlsx");
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary");

        // read the file from disk
        readfile($filename);
    }
    function DownloadGadgetSampleApiExcel($plan_id=''){
	    $get_family_construct=$this->db->query("select id,member_type from family_construct where isactive=1")->result();
	    $family_construct=array();
	    foreach ($get_family_construct as $item){
            $family_construct[$item->id]=$item->member_type;
        }
	    $query_get_details=$this->db->query("select mp.plan_id,mc.creaditor_name,mp.plan_name,mpp.policy_id,mp.policy_type_id,mpp.policy_sub_type_id,mpp.creditor_id
,mpst.code,mpst.plan_code from master_plan mp
join master_policy mpp on mpp.plan_id=mp.plan_id and mpp.isactive=1
join master_policy_sub_type mpst on mpst.policy_sub_type_id=mpp.policy_sub_type_id
join master_ceditors mc on mc.creditor_id=mp.creditor_id
 where mp.plan_id=".$plan_id);
	 //   echo $this->db->last_query();die;
        $creaditor_name='';
        $plan_name='';
        $code_array=array();
	    if($this->db->affected_rows() > 0){
	        $data=$query_get_details->result();
	        $creaditor_name=$data[0]->creaditor_name;
	        $plan_name=$data[0]->plan_name;
	        $plan_id=$data[0]->plan_id;
	        foreach ($data as $row){
                    $code_array[]=$row->code."(".$row->plan_code.")";
            }
        }
	    $excel_coulumns=array(
	        'partner','plan_id','salutation','first_name','middle_name','last_name','gender','dob','email_id','mobile_number',
            'make_model','gadget_purchase_date','gadget_purchase_price','tenure','userId','modeOfEntry','PaymentMode','bankName',
            'branchName','bankLocation','chequeType','ifscCode','TransactionNumber','TransactionRcvdDate','PaymentMode'
        );
	    foreach ($code_array as $code){
	        array_push($excel_coulumns,$code." SumInsure");
        }
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $filename=$plan_name."_Gadget_SampleFile.xls";
        $cnt=1;
        $char1='A';
        foreach ($excel_coulumns as $column){

                $objPHPExcel->getActiveSheet()->SetCellValue($char1 . $cnt, $column);

            $char1++;
        }
        $objPHPExcel->getActiveSheet()->SetCellValue("A" . 2, $creaditor_name);
        $objPHPExcel->getActiveSheet()->SetCellValue("B" . 2, $plan_id);
        ob_end_clean();
        $filename=FCPATH."assets/SampleExcel.xlsx";
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(str_replace(__FILE__,  $filename, __FILE__));

        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=".$plan_name."_Gadget_SampleFile.xlsx");
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary");

        // read the file from disk
        readfile($filename);
    }
}
