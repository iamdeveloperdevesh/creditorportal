<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Creditors extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		checklogin();
		$this->RolePermission = getRolePermissions();
		ini_set('upload_max_filesize', '20M');  
		ini_set('post_max_size', '25M');  
	}

	function index()
	{
		$data['policy_type']=$this->db->query("select * from master_policy_type where isactive=1")->result();
		$this->load->view('template/header.php');
		$this->load->view('creditors/index',$data);
		$this->load->view('template/footer.php');
	}

	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		//echo "<pre>GET ";print_r($_GET);exit;
		$creditorListing = curlFunction(SERVICE_URL.'/api/creditorListing',$_GET);
		//print_r($creditorListing);die;
		$jsonData = stripslashes(html_entity_decode($creditorListing));
		$creditorListing = json_decode($jsonData, true);
		//echo "<pre>";print_r($creditorListing);exit;
		if($creditorListing['status_code'] == '401'){
			//echo "in condition";
			redirect('login');
			exit();
		}
		
		
		//$get_result = $this->adcategorymodel->getRecords($_GET);

		$result = array();
		$result["sEcho"]= $_GET['sEcho'];

		$result["iTotalRecords"] = $creditorListing['Data']['totalRecords'];	//iTotalRecords get no of total recors
		$result["iTotalDisplayRecords"]= $creditorListing['Data']['totalRecords']; //iTotalDisplayRecords for display the no of records in data table.

		$items = array();
		
		if(!empty($creditorListing['Data']['query_result']) && count($creditorListing['Data']['query_result']) > 0)
		{
			for($i=0;$i<sizeof($creditorListing['Data']['query_result']);$i++)
			{
				$temp = array();
				array_push($temp, $creditorListing['Data']['query_result'][$i]['creaditor_name'] );
				array_push($temp, $creditorListing['Data']['query_result'][$i]['creditor_code'] );
				array_push($temp, $creditorListing['Data']['query_result'][$i]['ceditor_email'] );
				array_push($temp, $creditorListing['Data']['query_result'][$i]['creditor_mobile'] );
				array_push($temp, $creditorListing['Data']['query_result'][$i]['creditor_phone'] );
				array_push($temp, $creditorListing['Data']['query_result'][$i]['creditor_pancard'] );
				array_push($temp, $creditorListing['Data']['query_result'][$i]['creditor_gstn'] );
				if($creditorListing['Data']['query_result'][$i]['isactive'] == 1){
					array_push($temp, 'Active' );
				}else{
					array_push($temp, 'In-Active' );
				}
				
				$actionCol = "";
				if(in_array('CreditorEdit',$this->RolePermission)){
					$actionCol .='<a href="creditors/addEdit?text='.rtrim(strtr(base64_encode("id=".$creditorListing['Data']['query_result'][$i]['creditor_id'] ), '+/', '-_'), '=').'" title="Edit"><span class="spn-9"><i class="ti-pencil"></i></span></a>';

				}
				$actionCol .='<a onclick="openModal(\''.$creditorListing['Data']['query_result'][$i]['creditor_id'] .'\')" title="Broker Template"><span class="spn-9"><i class="ti-book"></i></span></a>';
				if(in_array('CreditorDelete',$this->RolePermission)){
					if($creditorListing['Data']['query_result'][$i]['isactive'] == 1){
						$actionCol .='&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\''.$creditorListing['Data']['query_result'][$i]['creditor_id'] .'\');" title="Delete"><span class="spn-9"><i class="ti-trash" style="color: red;"></i></span></a>';
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
		$record_id = "";
		$data = array();
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
			$checkDetails = curlFunction(SERVICE_URL.'/api/getCreditorFormData',$data);
			$checkDetails = json_decode($checkDetails, true);
			//echo "<pre>";print_r($checkDetails);exit;
			$result['getDetails'] = $checkDetails['Data'];
			
		}else{
			$result['getDetails'] = array();
		}
		
		//echo $user_id;
		$this->load->view('template/header.php');
		$this->load->view('creditors/addEdit',$result);
		$this->load->view('template/footer.php');
	}
	 
	function submitForm()
	{
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$logo_url = '';
			if(isset($_FILES['creditor_logo'])){
				
				$upload_dir = FCPATH.'assets'. DIRECTORY_SEPARATOR .'partner-logos';
				$file_ext = pathinfo($_FILES['creditor_logo']['name'], PATHINFO_EXTENSION);
				$size = $_FILES['creditor_logo']['size'];
				$savename = strtolower($_POST['creaditor_name']).'-logo.' . $file_ext;
				$path = $upload_dir . DIRECTORY_SEPARATOR . $savename;

				if(!in_array($file_ext, ['png', 'jpeg', 'jpg', 'bmp'])){

					echo json_encode(array("success"=>false, 'msg'=>'Allowed logo extensions are png, jpeg, jpg, bmp'));
					exit;	
				}
				else if($size > 5000000){

					echo json_encode(array("success"=>false, 'msg'=>'Logo size should be less than 5 MB'));
					exit;					
				}


				if(move_uploaded_file($_FILES['creditor_logo']['tmp_name'], $path)){
				
					$logo_url = FRONT_URL."/assets/partner-logos/$savename";
					$data['creditor_logo'] = $logo_url;
				}
			}
			//check duplicate record.
			$checkdata = array();
			$checkdata['creaditor_name'] = $_POST['creaditor_name'];
			$checkdata['utoken'] = $_SESSION['webpanel']['utoken'];
			if(isset($_POST['creditor_id']) && $_POST['creditor_id'] > 0){
				$checkdata['creditor_id'] = $_POST['creditor_id'];
			}
			
			$checkDetails = curlFunction(SERVICE_URL.'/api/checkDuplicateCreditor',$checkdata);
			//echo "<pre>";print_r($checkDetails);exit;
			$checkDetails = json_decode($checkDetails, true);
			
			if($checkDetails['status_code'] == '200')
			{
				echo json_encode(array("success"=>false, 'msg'=>'Record Already Present!'));
				exit;
			}
			
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['creaditor_name'] = (!empty($_POST['creaditor_name'])) ? $_POST['creaditor_name'] : '';
			$data['creditor_code'] = (!empty($_POST['creditor_code'])) ? $_POST['creditor_code'] : '';
			$data['ceditor_email'] = (!empty($_POST['ceditor_email'])) ? $_POST['ceditor_email'] : '';
			$data['creditor_mobile'] = (!empty($_POST['creditor_mobile'])) ? $_POST['creditor_mobile'] : '';
			$data['creditor_phone'] = (!empty($_POST['creditor_phone'])) ? $_POST['creditor_phone'] : '';
			$data['creditor_pancard'] = (!empty($_POST['creditor_pancard'])) ? $_POST['creditor_pancard'] : '';
			$data['creditor_gstn'] = (!empty($_POST['creditor_gstn'])) ? $_POST['creditor_gstn'] : '';
			$data['cd_balance'] = (!empty($_POST['cd_balance'])) ? $_POST['cd_balance'] : '';
			$data['threshold'] = (!empty($_POST['threshold'])) ? $_POST['threshold'] : '';
			$data['threshold_value'] = (!empty($_POST['threshold_value'])) ? $_POST['threshold_value'] : '';
			$data['address'] = (!empty($_POST['address'])) ? $_POST['address'] : '';
			$data['isactive'] = (!empty($_POST['isactive'])) ? $_POST['isactive'] : '';
			$data['short_code'] = (!empty($_POST['short_code'])) ? $_POST['short_code'] : '';
			$data['tc_text'] = (!empty($_POST['tc_text'])) ? $_POST['tc_text'] : '';
			$data['negative_issuance'] = (!empty($_POST['negative_issuance'])) ? $_POST['negative_issuance'] : 0;
			
			$addEdit = curlFunction(SERVICE_URL.'/api/addEditCreditor',$data);
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
			echo json_encode(array('success'=>false, 'msg'=>'Problem While Add/Edit Data.'));
			exit;
		}
	}
		
	function delRecord($id)
	{
		//$appdResult = $this->adcategorymodel->delrecord("tbl_categories","category_id ",$id);
		$data = array();
		$data['id'] = $id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$delRecord = curlFunction(SERVICE_URL.'/api/delCreditor',$data);
		//echo "<pre>";print_r($checkDetails);exit;
		$delRecord = json_decode($delRecord, true);
		 
		if($delRecord['status_code'] == '200'){
			echo "1";
		}else{
			echo "2";
		}	
	}
	function savepdfdata(){
		$creditor_id=$this->input->post('creditor_id');
		$pdf_type=$this->input->post('pdf_type');
		$textareaValue=$this->input->post('textareaValue');
		$policy_type=$this->input->post('policy_type');
		if($pdf_type == 1){
			$set=array(
				'pdf_html'=>$textareaValue,
			);
			$where=array('creditor_id'=>$creditor_id);
			$this->db->where($where);
			$q=$this->db->update('master_ceditors',$set);
		}else{
			$where=array(
				"creditor_id"=>$creditor_id,
				"policy_type"=>$policy_type
			);
			$delete_data=$this->db->delete("coi_format_html",$where);
			$data=array(
				"creditor_id"=>$creditor_id,
				"policy_type"=>$policy_type,
				"coi_html"=>$textareaValue,
			);
			$q=$this->db->insert("coi_format_html",$data);
		}
		if($q){
			echo true;
		}else{
			echo false;
		}
	}
	function getPdfData(){
		$creditor_id=$this->input->post('creditor_id');
		$policy_type=$this->input->post('policy_type');
		$pdf_type=$this->input->post('pdf_type');
		if(($pdf_type) == 2){
			$query=$this->db->query("select coi_html from coi_format_html where creditor_id=".$creditor_id." AND policy_type=".$policy_type)->row_array();
			if($this->db->affected_rows() >0 ){
				$response['data']=$query;
				echo json_encode($response, JSON_HEX_QUOT | JSON_HEX_TAG);
			}else{
				echo false;
			}
		}else{
			$query=$this->db->query("select pdf_html,title_pdf from master_ceditors where creditor_id=".$creditor_id)->row_array();
			if($this->db->affected_rows() >0 ){
				$response['data']=$query;
				echo json_encode($response, JSON_HEX_QUOT | JSON_HEX_TAG);
			}else{
				echo false;
			}
		}

	}
}

?>
