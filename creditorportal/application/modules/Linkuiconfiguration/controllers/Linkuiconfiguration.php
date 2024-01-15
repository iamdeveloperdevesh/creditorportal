<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Linkuiconfiguration extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		checklogin();
		$this->RolePermission = getRolePermissions();
	}
 
	function index()
	{
		$this->load->view('template/header.php');
		$this->load->view('Linkuiconfiguration/index');
		$this->load->view('template/footer.php');
	}
 
	function addEdit($id=NULL)
	{
		//echo $user_id;
		$result = array();
		//echo "<pre>";print_r($_SESSION["webpanel"]);exit;
		//$result['user_details'] = $this->myprofile_model->getFormdata($_SESSION["webpanel"]['employee_id']);
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['id'] = $_SESSION["webpanel"]['employee_id'];
		$getLoginUserDetails = curlFunction(SERVICE_URL.'/api/getLoginUserDetails',$data);
		$getLoginUserDetails = json_decode($getLoginUserDetails, true);
		//echo "<pre>ddd";print_r($getLoginUserDetails);exit;
		$result['user_details'] = $getLoginUserDetails['Data']['user_data'][0];
		
		$this->load->view('template/header.php');
		$this->load->view('Linkuiconfiguration/index',$result);
		$this->load->view('template/footer.php');
	}
	function getproductName(){

		$query=$this->db->query("select plan_name,plan_id from master_plan where isactive=1")->result();
	//	echo $this->db->last_query();die;
		$response['data']=$query;
		$response['code']=200;
		echo json_encode($response);
	}
	function get_image_url($file_name_post,$rename=true){
		if(isset($_FILES[$file_name_post])){

			$upload_dir = FCPATH.'assets'. DIRECTORY_SEPARATOR .'images';
			$file_ext = pathinfo($_FILES[$file_name_post]['name'], PATHINFO_EXTENSION);
			$size = $_FILES[$file_name_post]['size'];
			if($rename){
				$savename = $file_name_post.time().'.' . $file_ext;
			}else{
				$savename = $_FILES[$file_name_post]['name'];
			}
			$path = $upload_dir . DIRECTORY_SEPARATOR . $savename;

			if(!in_array($file_ext, ['png', 'jpeg', 'jpg', 'bmp','gif','pdf'])){

				echo json_encode(array("success"=>false, 'msg'=>'Allowed logo extensions are png, jpeg, jpg, bmp,gif'));
				exit;
			}
			else if($size > 5000000){

				echo json_encode(array("success"=>false, 'msg'=>'Logo size should be less than 5 MB'));
				exit;
			}


			if(move_uploaded_file($_FILES[$file_name_post]['tmp_name'], $path)){

				$url = "/assets/images/$savename";
				return $url;
			}else{
				return false;
			}
		}else{
			return $this->input->post($file_name_post);
		}
	}
	function submitForm()
	{

		$config_for=$this->input->post('config_for');
		$is_update=$this->input->post('is_update');
		$creditor_id=$this->input->post('creditor_id');
		$plan_id=$this->input->post('product_id');
		$lead_header_text=$this->input->post('lead_header_text');
		$quote_header_text=$this->input->post('quote_header_text');
		$quote_right_text=$this->input->post('quote_right_text');
		$fp_header=$this->input->post('fp_header');
		$fp_image=$this->get_image_url('fp_image');
		$loader_text=$this->input->post('loader_text');
		$proposal_text = $this->input->post('proposal_text');
		$feature_array=$this->input->post('feature_array');
		$customer_support_number=$this->input->post('customer_support_number');
		$tc_text=$this->input->post('tc_text');
		$deductible_text=$this->input->post('deductible_text');
		$qt_image=$this->get_image_url('qt_image');
		$g_qt_image=$this->get_image_url('g_qt_image');
		$sm_image1=$this->get_image_url('sm_image1');
		$sm_image2=$this->get_image_url('sm_image2');
		$sm_image3=$this->get_image_url('sm_image3');
		$sm_image4=$this->get_image_url('sm_image4');
		$know_more_pdf=$this->get_image_url('know_more_pdf',false);
		$pf_image1=$this->get_image_url('pf_image1');
		$pf_image2=$this->get_image_url('pf_image2');
		$pf_image3=$this->get_image_url('pf_image3');

		$data=array(
			"config_for"=>$config_for,
			"creditor_id"=>$creditor_id,
			"first_page_header"=>$fp_header,
			"first_page_features"=>$feature_array[0],
			"Loader_text"=>$loader_text,
			"customer_support_number"=>$customer_support_number,
			"tc_text"=>$tc_text,
			"deductible_text"=>$deductible_text,
			"know_more_pdf"=>$know_more_pdf,
			"proposer_details_image"=>$pf_image1,
			"insured_detail_image"=>$pf_image2,
			"nominee_detail_image"=>$pf_image3,
			"lead_header_text"=>$lead_header_text,
			"quote_header_text"=>$quote_header_text,
			"quote_right_text"=>$quote_right_text,
			"proposal_header_text"=>$proposal_text,
		);


		if($config_for == 1){
			$data['creditor_id']=$creditor_id;
			$where=array('config_for'=>$config_for,'creditor_id'=>$creditor_id);
		}else{
			$data['plan_id']=$plan_id;
			$where=array('config_for'=>$config_for,'plan_id'=>$plan_id);
		}
		//print_r($data);die;
		if($is_update == 1){
			$data['first_page_image']=$fp_image;
			$data['quote_card_image']=$qt_image;
			$data['generate_quote_image']=$g_qt_image;
			$data['summary_page_image1']=$sm_image1;
			$data['summary_page_image2']=$sm_image2;
			$data['summary_page_image3']=$sm_image3;
			$data['summary_page_image4']=$sm_image4;
			$this->db->where($where);
			$insert_record=$this->db->update("link_ui_configuaration",$data);
		}else{
			$data['first_page_image']=$fp_image;
			$data['quote_card_image']=$qt_image;
			$data['generate_quote_image']=$g_qt_image;
			$data['summary_page_image1']=$sm_image1;
			$data['summary_page_image2']=$sm_image2;
			$data['summary_page_image3']=$sm_image3;
			$data['summary_page_image4']=$sm_image4;
			$insert_record=$this->db->insert("link_ui_configuaration",$data);
		}

		if($insert_record == true){
			$response['code']=200;
			$response['msg']="Added Successfully";
		}else{
			$response['code']=201;
			$response['msg']="Something went wrong!";
		}echo json_encode($response);

	}

	function submitForm_bk()
	{

		$config_for=$this->input->post('config_for');
		$is_update=$this->input->post('is_update');
		$creditor_id=$this->input->post('creditor_id');
		$plan_id=$this->input->post('product_id');
		$fp_header=$this->input->post('fp_header');
		$fp_image=$this->get_image_url('fp_image');
		$loader_text=$this->input->post('loader_text');
		$feature_array=$this->input->post('feature_array');
		$customer_support_number=$this->input->post('customer_support_number');
		$tc_text=$this->input->post('tc_text');
		$qt_image=$this->get_image_url('qt_image');
		$g_qt_image=$this->get_image_url('g_qt_image');
		$sm_image1=$this->get_image_url('sm_image1');
		$sm_image2=$this->get_image_url('sm_image2');
		$sm_image3=$this->get_image_url('sm_image3');
		$sm_image4=$this->get_image_url('sm_image4');
		$know_more_pdf=$this->get_image_url('know_more_pdf');
		$pf_image1=$this->get_image_url('pf_image1');
		$pf_image2=$this->get_image_url('pf_image2');
		$pf_image3=$this->get_image_url('pf_image3');

		$data=array(
			"config_for"=>$config_for,
			"creditor_id"=>$creditor_id,
			"first_page_header"=>$fp_header,
			"first_page_features"=>$feature_array[0],
			"Loader_text"=>$loader_text,
			"customer_support_number"=>$customer_support_number,
			"tc_text"=>$tc_text,
			"know_more_pdf"=>$know_more_pdf,
			"proposer_details_image"=>$pf_image1,
			"insured_detail_image"=>$pf_image2,
			"nominee_detail_image"=>$pf_image3,
		);

		if($fp_image != false){
			$data['first_page_image']=$fp_image;
		}
		if($qt_image != false){
			$data['quote_card_image']=$qt_image;
		}
		if($g_qt_image != false){
			$data['generate_quote_image']=$g_qt_image;
		}
		if($sm_image1 != false){
			$data['summary_page_image1']=$sm_image1;
		}
		if($sm_image2 != false){
			$data['summary_page_image2']=$sm_image2;
		}
		if($sm_image3 != false){
			$data['summary_page_image3']=$sm_image3;
		}
		if($sm_image4 != false){
			$data['summary_page_image4']=$sm_image4;
		}
		if($config_for == 1){
			$data['creditor_id']=$creditor_id;
			$where=array('config_for'=>$config_for,'creditor_id'=>$creditor_id);
		}else{
			$data['plan_id']=$plan_id;
			$where=array('config_for'=>$config_for,'plan_id'=>$plan_id);
		}
		//print_r($data);die;
		if($is_update == 1){
			$this->db->where($where);
			$insert_record=$this->db->update("link_ui_configuaration",$data);
		}else{
			$insert_record=$this->db->insert("link_ui_configuaration",$data);
		}

		if($insert_record == true){
			$response['code']=200;
			$response['msg']="Added Successfully";
		}else{
			$response['code']=201;
			$response['msg']="Something went wrong!";
		}echo json_encode($response);

	}
	function getconfiguaration(){
		$id=$this->input->post('id');
		$config_for=$this->input->post('config_for');
		$where=' config_for='.$config_for;
		if($config_for == 1){
			$where .=' AND creditor_id='.$id;
		}else{
			$where .=' AND plan_id='.$id;
		}
		$query=$this->db->query("select * from link_ui_configuaration where ".$where);
		if($this->db->affected_rows() > 0){
			$result=$query->row();
			$response['code']=200;
			$response['data']=$result;
		}else{
			$response['code']=201;
		}echo json_encode($response);
	}


}

?>
