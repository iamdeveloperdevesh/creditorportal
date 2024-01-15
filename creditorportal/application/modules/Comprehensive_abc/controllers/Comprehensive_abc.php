<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Comprehensive_abc extends CI_Controller 
{

    function __construct(){
        parent::__construct();
		// checklogin();
		// $this->RolePermission = getRolePermissions();
        $this->load->model("fyntune_home/ABC_m", "Abc_m");
        $this->emp_id=1;        
    }

    public function index(){
    }

    public function comprehensiveabc()
    {
		/*$this->load->model("Comprehensive_abc/Comprehensive_product_abc_m");

		$data['product'] = $this->Comprehensive_product_abc_m->product_display_abc();

		if($this->emp_id != null){
            //$this->db->set('modified_date', date("Y-m-d H:i:s"));
            $this->db->where('emp_id', $this->emp_id);
            $this->db->update("employee_details",array("dropoff_flag" => "0",'modified_date' => date("Y-m-d H:i:s")));
        }

        $userActivity = $this->Abc_m->insertOrUpdateUserActivity("1");*/

        $partner_id = $_GET['partner'];
        $lead_id = $this->session->userdata('lead_id');

        $checkDetails = curlFunction(SERVICE_URL.'/customer_api/get_partner_products', [
            'partner_id' => $partner_id
        ]);

        $checkDetails = json_decode($checkDetails, true);
        //echo $this->db->last_query();exit;
        echo "<pre>";print_r($checkDetails);exit;
        $data = [];
        if($checkDetails['status']){

            $data['lead_id'] = $lead_id;
            $data['data'] = $checkDetails['data'];
            $data['title-head'] = 'Comprehensive Product Page';  
            //echo "<pre>";print_r($data);exit;
            $this->load->view('template/customer_header.php');
            $this->load->view('Comprehensive_abc/comprehensive_view_abc.php',$data);
            $this->load->view('template/customer_footer.php');	  
        }      
    }

}