<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Products extends CI_Controller {
    
    function __construct()
	{
		parent::__construct();
		checklogin();
	    $this->RolePermission = getRolePermissions();
	}
	
	function index()
	{
		$this->load->view('template/header.php');
		$this->load->view('products/index');
		$this->load->view('template/footer.php');
	} 
	
	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		$insurerListing = curlFunction(SERVICE_URL.'/api2/ProductsListing',$_GET);
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
				array_push($temp, $insurerListing['Data']['query_result'][$i]['plan_name'] );
				array_push($temp, $insurerListing['Data']['query_result'][$i]['creditorname'] );
				array_push($temp, $insurerListing['Data']['query_result'][$i]['policytype'] );
				if($insurerListing['Data']['query_result'][$i]['isactive'] == 1){
					array_push($temp, 'Active' );
				}else{
					array_push($temp, 'In-Active' );
				}
				
				$actionCol = "";
				//if($this->privilegeduser->hasPrivilege("CategoriesAddEdit"))
				//{
					$actionCol .='<a href="products/edit?text='.rtrim(strtr(base64_encode("id=".$insurerListing['Data']['query_result'][$i]['plan_id'] ), '+/', '-_'), '=').'" title="Edit"><i class="fa fa-edit"></i></a>';
				//}
				//if($this->privilegeduser->hasPrivilege("CategoryDelete")){
					if($insurerListing['Data']['query_result'][$i]['isactive'] == 1){
						$actionCol .='&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\''.$insurerListing['Data']['query_result'][$i]['plan_id'] .'\');" title="Delete"><i class="fa fa-trash"></i></a>';
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
	
	
    function AddNewView()
	{
	    $data = array();
	    $data['utoken'] = $_SESSION['webpanel']['utoken'];
	    $data['datalist'] = json_decode(curlFunction(SERVICE_URL.'/api2/addNewProductView',$data));
		$this->load->view('template/header.php');
		$this->load->view('products/addNew',$data);
		$this->load->view('template/footer.php');
	   
	}
	
	function edit()
	{
	    $data = array();
		if(!empty($_GET['text']) && isset($_GET['text'])){
			$varr=base64_decode(strtr($_GET['text'], '-_', '+/'));	
			parse_str($varr,$url_prams);
			$id = $url_prams['id'];
		}
	    $data['id'] = $id;
	    $data['utoken'] = $_SESSION['webpanel']['utoken'];
	    $data['datalist'] = json_decode(curlFunction(SERVICE_URL.'/api2/editproduct',$data));
	    $subtype = array();
	    foreach($data['datalist']->details as $detail){
	        array_push($subtype,$detail->policy_sub_type_id);
	    }
	    $data['subtypes'] = $subtype;
	    $planpay = array();
	    foreach($data['datalist']->planpayments as $detail){
	        array_push($planpay,$detail->payment_mode_id);
	    }
	    $data['planpay'] = $planpay;
		$this->load->view('template/header.php');
		$this->load->view('products/edit',$data);
		$this->load->view('template/footer.php');
	   
	}
	
	function productlist()
	{
	    $data = array();
	    $data['utoken'] = $_SESSION['webpanel']['utoken'];
		$this->load->view('template/header.php');
		$this->load->view('products/productlist',$data);
		$this->load->view('template/footer.php');
	   
	}
	function AddNew()
	{
	    $data = array();
	    $data['utoken'] = $_SESSION['webpanel']['utoken'];
	    $data['plan_name'] = $this->input->post('plan_name');
	    $data['creditor_id'] = $this->input->post('creditor');
	    $data['policy_type_id'] = 1;
	    $data['policy_sub_type_id'] = implode(',',$this->input->post('policy_sub_type'));
	    $data['payment_modes'] = implode(',',$this->input->post('payment_modes'));
	    $response = json_decode(curlFunction(SERVICE_URL.'/api2/addNewProduct',$data),true);
	    if($response['status_code'] == '200'){
	        
	        echo json_encode(array('success'=>true, 'msg'=>$response['Metadata']['Message'], 'data'=>$response['Data']));
			exit;
		}else{
			echo json_encode(array('success'=>false, 'msg'=>$response['Metadata']['Message']));
			exit;
		} 
		
	}
	function update()
	{
	    $data = array();
	    $data['utoken'] = $_SESSION['webpanel']['utoken'];
	    $data['plan_id'] = $this->input->post('plan_id');
	    $data['plan_name'] = $this->input->post('plan_name');
	    $data['creditor_id'] = $this->input->post('creditor');
	    $data['policy_type_id'] = 1;
	    $data['policy_sub_type_id'] = implode(',',$this->input->post('policy_sub_type'));
	    $data['payment_modes'] = implode(',',$this->input->post('payment_modes'));
	    $response = json_decode(curlFunction(SERVICE_URL.'/api2/UpdateProduct',$data),true);
	    if($response['status_code'] == '200'){
	        echo json_encode(array('success'=>true, 'msg'=>$response['Metadata']['Message'], 'data'=>$response['Data']));
			exit;
		}else{
			echo json_encode(array('success'=>false, 'msg'=>$response['Metadata']['Message']));
			exit;
		} 
		
	}
	function uploadexcel(){
	    if(isset($_FILES["file"]["name"]))
          {
           $path = $_FILES["file"]["tmp_name"];
           $object = PHPExcel_IOFactory::load($path);
           foreach($object->getWorksheetIterator() as $worksheet)
           {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            for($row=2; $row<=$highestRow; $row++)
            {
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
           $response = json_decode(curlFunction(SERVICE_URL.'/api2/uploadexcel',$data),true);
           
           echo 'Data Imported successfully';
          } 

	}
	function AddPolicyNew()
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
	    if($mandatory == 1){
	        $data['is_optional'] = 0;
	    }else{$data['is_optional'] = 1;}
	    $combo = $this->input->post('combo');
	    if($combo == 1){
	        $data['is_combo'] = 1;
	    }else{$data['is_combo'] = 0;}
	    $data['pdf_type'] = $this->input->post('pdf_type');
	    $data['insurer_id'] = $this->input->post('masterInsurance');
	    $data['policy_start_date'] = $this->input->post('policyStartDate');
	    $data['policy_end_date'] = $this->input->post('policyEndDate');
	    $data['max_member_count'] = $this->input->post('membercount');
	    $data['sitype'] = $this->input->post('sum_insured_type');
	    $data['sibasis'] = $this->input->post('companySubTypePolicy');
	    if($data['sibasis'] != 1){
	        if(isset($_FILES["ageFile"]["name"]))
          {
           $path = $_FILES["ageFile"]["tmp_name"];
           $object = PHPExcel_IOFactory::load($path);
           foreach($object->getWorksheetIterator() as $worksheet)
           {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            if($data['sibasis'] == 4){
                for($row=2; $row<=$highestRow; $row++)
                {
                 $min_age = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                 $max_age = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                 $sum_insured = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                 $premium = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                 $tax = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
				if(!empty($premium)){
                 $dataexcel[] = array(
                  'master_policy_id'   => $data['policy_sub_type_id'],
                  'min_age'  => $min_age,
                  'max_age'  => $max_age,
                  'sum_insured'   => $sum_insured,
                  'premium_rate'    => $premium,
                  'is_taxable'  => $tax,
				  'is_absolute'  => $absolute
				);}
                }
            }
            if($data['sibasis'] == 2){
                for($row=2; $row<=$highestRow; $row++)
                {
                 $adult_count = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                 $child_count = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                 $sum_insured = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                 $premium = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                 $tax = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
				 if(!empty($premium)){
                 $dataexcel[] = array(
                  'master_policy_id'   => $data['policy_sub_type_id'],
                  'adult_count'  => $adult_count,
                  'child_count'  => $child_count,
                  'sum_insured'   => $sum_insured,
                  'premium_rate'    => $premium,
                  'is_taxable'  => $tax,
				  'is_absolute'  => $absolute
                 );}
                }
            }
            if($data['sibasis'] == 3){
                for($row=2; $row<=$highestRow; $row++)
                {
                 $adult_count = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                 $child_count = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                 $min_age = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                 $max_age = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                 $sum_insured = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                 $premium = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                 $tax = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                 if(!empty($premium)){
				 $dataexcel[] = array(
                  'master_policy_id'   => $data['policy_sub_type_id'],
                  'adult_count'  => $adult_count,
                  'child_count'  => $child_count,
                   'min_age'  => $min_age,
                   'max_age'  => $max_age,
                  'sum_insured'   => $sum_insured,
                  'premium_rate'    => $premium,
                  'is_taxable'  => $tax,
				  'is_absolute'  => $absolute
                 );}
                }
            }
           }
           $data['exceldata'] = json_encode($dataexcel);
          }
	    }else{
	        $data['sum_insured_opt'] = implode(',',$this->input->post('sum_insured_opt1'));
	        $data['premium_opt'] = implode(',',$this->input->post('premium_opt'));
	        $data['tax_opt'] = implode(',',$this->input->post('tax_opt'));
	    }
	    $data['members'] = implode(',',$this->input->post('member'));
	    $data['minage'] = implode(',',$this->input->post('minage'));
	    $data['maxage'] = implode(',',$this->input->post('maxage'));
	    $response = json_decode(curlFunction(SERVICE_URL.'/api2/addNewPolicy',$data),true);
	    if($response['status_code'] == '200'){
	        echo json_encode(array('success'=>true, 'msg'=>$response['Metadata']['Message'], 'data'=>$response['Data']));
			exit;
		}else{
			echo json_encode(array('success'=>false, 'msg'=>$response['Metadata']['Message']));
			exit;
		} 
		
	}
	function UpdatePolicyNew()
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
	    if($mandatory == 1){
	        $data['is_optional'] = 0;
	    }else{$data['is_optional'] = 1;}
	    $combo = $this->input->post('combo');
	    if($combo == 1){
	        $data['is_combo'] = 1;
	    }else{$data['is_combo'] = 0;}
	    $data['pdf_type'] = $this->input->post('pdf_type');
	    $data['insurer_id'] = $this->input->post('masterInsurance');
	    $data['policy_start_date'] = $this->input->post('policyStartDate');
	    $data['policy_end_date'] = $this->input->post('policyEndDate');
	    $data['max_member_count'] = $this->input->post('membercount');
	    $data['sitype'] = $this->input->post('sum_insured_type');
	    $data['sibasis'] = $this->input->post('companySubTypePolicy');
	    if($data['sibasis'] != 1){
	     if(isset($_FILES["ageFile"]["name"]))
          {
           $path = $_FILES["ageFile"]["tmp_name"];
           $object = PHPExcel_IOFactory::load($path);
           foreach($object->getWorksheetIterator() as $worksheet)
           {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            if($data['sibasis'] == 4){
                for($row=2; $row<=$highestRow; $row++)
                {
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
            if($data['sibasis'] == 2){
                for($row=2; $row<=$highestRow; $row++)
                {
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
            if($data['sibasis'] == 3){
                for($row=2; $row<=$highestRow; $row++)
                {
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
				  'is_absolute'  => $absolute
                 );
                }
            }
           }
           $data['exceldata'] = json_encode($dataexcel);
          }else{
              $data['exceldata'] = array();
          }
          
	    }else{
	        $data['sum_insured_opt'] = implode(',',$this->input->post('sum_insured_opt1'));
	        $data['premium_opt'] = implode(',',$this->input->post('premium_opt'));
	        $data['tax_opt'] = implode(',',$this->input->post('tax_opt'));
	    }
	    $data['members'] = implode(',',$this->input->post('member'));
	    $data['minage'] = implode(',',$this->input->post('minage'));
	    $data['maxage'] = implode(',',$this->input->post('maxage'));
	    $response = json_decode(curlFunction(SERVICE_URL.'/api2/updateNewPolicy',$data),true);
	    if($response['status_code'] == '200'){
	        echo json_encode(array('success'=>true, 'msg'=>$response['Metadata']['Message'], 'data'=>$response['Data']));
			exit;
		}else{
			echo json_encode(array('success'=>false, 'msg'=>$response['Metadata']['Message']));
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
	    if($mandatory == 1){
	        $data['is_optional'] = 0;
	    }else{$data['is_optional'] = 1;}
	    $combo = $this->input->post('combo');
	    if($combo == 1){
	        $data['is_combo'] = 1;
	    }else{$data['is_combo'] = 0;}
	    $data['pdf_type'] = $this->input->post('pdf_type');
	    $data['insurer_id'] = $this->input->post('masterInsurance');
	    $data['policy_start_date'] = date('Y-m-d',strtotime($this->input->post('policyStartDate')));
	    $data['policy_end_date'] = date('Y-m-d',strtotime($this->input->post('policyEndDate')));
	    $data['max_member_count'] = $this->input->post('membercount');
	    $data['sitype'] = $this->input->post('sum_insured_type');
	    $data['sibasis'] = $this->input->post('companySubTypePolicy');
	    if($data['sibasis'] != 1){
	        if(isset($_FILES["ageFile"]["name"]))
          {
           $path = $_FILES["ageFile"]["tmp_name"];
           $object = PHPExcel_IOFactory::load($path);
           foreach($object->getWorksheetIterator() as $worksheet)
           {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            if($data['sibasis'] == 4){
                for($row=2; $row<=$highestRow; $row++)
                {
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
            if($data['sibasis'] == 2){
                for($row=2; $row<=$highestRow; $row++)
                {
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
            if($data['sibasis'] == 3){
                for($row=2; $row<=$highestRow; $row++)
                {
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
				  'is_absolute'  => $absolute
                 );
                }
            }
           }
           $data['exceldata'] = json_encode($dataexcel);
          }
	    }else{
	        $data['sum_insured_opt'] = implode(',',$this->input->post('sum_insured_opt1'));
	        $data['premium_opt'] = implode(',',$this->input->post('premium_opt'));
	        $data['tax_opt'] = implode(',',$this->input->post('tax_opt'));
	    }
	    $data['members'] = implode(',',$this->input->post('member'));
	    $data['minage'] = implode(',',$this->input->post('minage'));
	    $data['maxage'] = implode(',',$this->input->post('maxage'));
	    $response = json_decode(curlFunction(SERVICE_URL.'/api2/UpdatePolicy',$data),true);
	    if($response['status_code'] == '200'){
	        echo json_encode(array('success'=>true, 'msg'=>$response['Metadata']['Message'], 'data'=>$response['Data']));
			exit;
		}else{
			echo json_encode(array('success'=>false, 'msg'=>$response['Metadata']['Message']));
			exit;
		} 
		
	}
	function addpolicyview($id){
	    $data2['utoken'] = $_SESSION['webpanel']['utoken'];
	    $data2['id'] = $id;
		$data['datalist'] = json_decode(curlFunction(SERVICE_URL.'/api2/getproductDetails',$data2));
		$html = $this->load->view('products/addNew',$data);
		echo $html;
	}
	function updatepolicyview($id,$policy_id=''){
	    $data2['utoken'] = $_SESSION['webpanel']['utoken'];
	    $data2['id'] = $id;
	    if(!empty($policy_id)){
	         $data2['policy_id'] = $policy_id;
	    }
		$data['datalist'] = json_decode(curlFunction(SERVICE_URL.'/api2/getPolicyUpdateDetails',$data2));
		$data['policyview'] = 1;
		$html = $this->load->view('products/edit',$data);
		echo $html;
	}
	function checkplanname($id = null){
	    $data2['utoken'] = $_SESSION['webpanel']['utoken'];
	    $data2['plan'] = $this->input->post('name');
	    if(!empty($id)){
	    $data2['id'] = $id;}
		$response = json_decode(curlFunction(SERVICE_URL.'/api2/checkplanname',$data2),true);
		if($response['status_code'] == '200'){
	        echo json_encode(array('success'=>true, 'msg'=>$response['Metadata']['Message']));
			exit;
		}else{
			echo json_encode(array('success'=>false, 'msg'=>$response['Metadata']['Message']));
			exit;
		} 
	}
	function checkpolicynumber($id = null){
	    $data2['utoken'] = $_SESSION['webpanel']['utoken'];
	    $data2['policy'] = $this->input->post('name');
	    if(!empty($id)){
	    $data2['id'] = $id;}
		$response = json_decode(curlFunction(SERVICE_URL.'/api2/checkpolicynumber',$data2),true);
		if($response['status_code'] == '200'){
	        echo json_encode(array('success'=>true, 'msg'=>$response['Metadata']['Message']));
			exit;
		}else{
			echo json_encode(array('success'=>false, 'msg'=>$response['Metadata']['Message']));
			exit;
		} 
	}
} 
    
?>