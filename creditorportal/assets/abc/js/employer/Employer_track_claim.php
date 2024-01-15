<?php 

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class Employer_track_claim extends CI_Controller {
    function __construct() {

       parent::__construct();
       $this->session->set_userdata("employer_id","1");
       $this->emp_id = $this->session->userdata("employer_id");
        $this->load->model('employer/all_details_m');
        // $this->load->model('employer/employer_track_claim_m');
    }

    public function index()
    {
    	$this->load->employer_template("employer_track_claim_view");
    }

    public function get_all_insurer()
    {
       $data = $this->all_details_m->get_all_insurer();
        echo json_encode($data);
    }
    public function get_policytype_on_insurer()
    {
        print_r(json_encode($this->all_details_m->get_policytype_on_insurer()));
    }
    public function get_policyno_on_policytype()
    {
       print_r(json_encode($this->all_details_m->get_policyno_on_policytype()));
    }
    public function get_employee_on_policy_no()
    {
      print_r(json_encode($this->all_details_m->get_employee_on_policy_no()));
    }
    public function get_employee_family_on_emp_id()
    {
      print_r(json_encode($this->all_details_m->get_employee_family_on_emp_id()));
    }
    public function get_claimid_on_member_id()
    {
      print_r(json_encode($this->all_details_m->get_claimid_on_member_id()));
    }
    public function get_dates_on_claim_id()
    {
      print_r(json_encode($this->all_details_m->get_dates_on_claim_id()));
    }
    public function get_datesdocs_on_claim_id()
    {
        print_r(json_encode($this->all_details_m->get_datesdocs_on_claim_id()));
    }
  }