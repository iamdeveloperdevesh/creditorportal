<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class ThemeConfiguaration extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		checklogin();
		$this->RolePermission = getRolePermissions();
	}
 
	function index()
	{
		$this->load->view('template/header.php');
		$this->load->view('ThemeConfiguaration/index');
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
		$this->load->view('ThemeConfiguaration/index',$result);
		$this->load->view('template/footer.php');
	}
 
	function submitForm()
	{
			
		$theme_for=$this->input->post('theme_for');
		$creditor_id=$this->input->post('creditor_id');
		$primary_color=$this->input->post('primary_color');
		$secondary_color=$this->input->post('secondary_color');
		$text_color=$this->input->post('text_color');
		$background_color=$this->input->post('background_color');
		$cta_color=$this->input->post('cta_color');
		if($theme_for == 2){
			$delete_partner_record=$this->db->delete('theme_configuaration',array('creditor_id'=>$creditor_id));
			$data=array(
				"theme_for"=>$theme_for,
				"creditor_id"=>$creditor_id,
				"primary_color"=>$primary_color,
				"secondary_color"=>$secondary_color,
				"text_color"=>$text_color,
				"background_color"=>$background_color,
				"cta_color"=>$cta_color,
			);
		}else{
			if(isset($_FILES['logo_url'])){

				$upload_dir = FCPATH.'assets'. DIRECTORY_SEPARATOR .'images';
				$file_ext = pathinfo($_FILES['logo_url']['name'], PATHINFO_EXTENSION);
				$size = $_FILES['logo_url']['size'];
				$savename = 'logo.' . $file_ext;
				//$NewName='logo.' .date('dmyhis');
				$path = $upload_dir . DIRECTORY_SEPARATOR . $savename;
				
				if(!in_array($file_ext, ['png'])){

					echo json_encode(array("success"=>false, 'msg'=>'Allowed logo extensions are png, jpeg, jpg, bmp'));
					exit;
				}
				else if($size > 5000000){

					echo json_encode(array("success"=>false, 'msg'=>'Logo size should be less than 5 MB'));
					exit;
				}


				if(move_uploaded_file($_FILES['logo_url']['tmp_name'], $path)){

					$logo_url = "/assets/images/$savename";
				}
			}else{
				$logo_url = $this->input->post('logo_url');
			}
			$delete_admin_record=$this->db->delete('theme_configuaration',array('theme_for'=>$theme_for));
			$data=array(
				"theme_for"=>$theme_for,
				"primary_color"=>$primary_color,
				"secondary_color"=>$secondary_color,
				"text_color"=>$text_color,
				"background_color"=>$background_color,
				"cta_color"=>$cta_color,
				"logo_url"=>$logo_url,
			);
		}
		$insert_record=$this->db->insert("theme_configuaration",$data);
		if($insert_record == true){
			$response['code']=200;
			$response['msg']="Added Successfully";
		}else{
			$response['code']=201;
			$response['msg']="Something went wrong!";
		}echo json_encode($response);

	}
	function getPartnerTheme(){
		$creditor_id=$this->input->post('creditor_id');
		$theme_for=$this->input->post('theme_for');
		$where=' theme_for='.$theme_for;
		if($theme_for == 2){
			$where .=' AND creditor_id='.$creditor_id;
		}
		$query=$this->db->query("select * from theme_configuaration where ".$where);
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
