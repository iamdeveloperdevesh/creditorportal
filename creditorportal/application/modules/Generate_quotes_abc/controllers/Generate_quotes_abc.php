<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Generate_quotes_abc extends CI_Controller 
{

    function __construct(){
        parent::__construct();
		// checklogin();
		// $this->RolePermission = getRolePermissions();

    }

    public function index(){
    }

    public function generate_quotes_home_abc($plan_id)
    {

        $data['title-head'] = 'Quotation Page';
        
        $checkDetails = curlFunction(SERVICE_URL.'/customer_api/get_product_details', [
            'plan_id' => $plan_id
        ]);
        
        $checkDetails = json_decode($checkDetails, true);
        $data['data'] = $checkDetails['data'];
        $data['data']['plan_id'] = $plan_id;
        $data['data']['trace_id'] = $this->session->userdata('trace_id');
        $data['data']['customer_id'] = $this->session->userdata('customer_id');
        $data['data']['lead_id'] = $this->session->userdata('lead_id');

        // echo "<pre>";print_r($data);exit;

		$this->load->view('template/customer_header.php');
		$this->load->view('Generate_quotes_abc/generate_quotes_view_abc.php',$data);
		$this->load->view('template/customer_footer.php');	        
        
    }

}