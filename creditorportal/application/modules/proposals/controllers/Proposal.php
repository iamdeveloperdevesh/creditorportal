<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Proposal extends CI_Controller
{

    public function index()
    {   
        $req_data['lead_id'] = $this->session->userdata('lead_id');
        $req_data['customer_id'] = $this->session->userdata('customer_id');
        $req_data['trace_id'] = $this->session->userdata('trace_id'); 

        $req_data['plan_id'] = $this->input->post('plan_id');
        $req_data['master_policy_id'] = $this->input->post('master_policy_id');

        $response = curlFunction(SERVICE_URL . '/customer_api/getQuoteDetails', $req_data);
        
        $this->load->view('template/customer_portal_header.php');
        $this->load->view('proposal/index');
        $this->load->view('template/customer_portal_footer.php');
    }

   
}