<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Member_detail_abc extends CI_Controller 
{

    function __construct(){
        parent::__construct();
		// checklogin();
		// $this->RolePermission = getRolePermissions();

    }

    public function member_proposer_detail(){
        
        $data['title-head'] = 'Proposer Detail Page';  
        $data['mobile'] = $this->session->userdata('mobile');
        $data['generated_quote'] = $this->session->userdata('generated_quote');

        // print_r($data);exit;
		$this->load->view('template/customer_header.php');
		$this->load->view('Member_detail_abc/member_proposer_detail',$data);
		$this->load->view('template/customer_footer.php');	        

    }

    public function member_detailabc(){

        // print_r($_SESSION);
        // exit;

        $lead_id['lead_id']=$_SESSION['lead_id'];



        $result = curlFunction(SERVICE_URL . '/customer_api/get_family_construct_details',$lead_id);
        $data['nominee_relation'] = curlFunction(SERVICE_URL . '/api2/getNomineeRelations',[]);

        $nominee_data['lead_id'] = $this->session->userdata('lead_id');

        $data['review_page_details'] = json_decode(curlFunction(SERVICE_URL . '/api2/review_page_details', $nominee_data),true);

        // echo "<pre";
        // print_r($review_page_details);
        // exit;

        $data['title_head'] = 'Member Detail Page';
        $data['result']=$result;        
        $data['generated_quote'] = $this->session->userdata('generated_quote');
        
        // print_r($data);exit;

		$this->load->view('template/customer_header.php');
		$this->load->view('Member_detail_abc/member_detail_view_abc',$data);
		$this->load->view('template/customer_footer.php');	        

    }

    public function saveFamilyDetails(){

        //print_r($_POST);exit;
        $result = [];
        for($i=0;$i<count($_POST['full_name']);$i++){

            if($i == 0){

                continue;
            }

            if($_POST['is_adult'][$i] == 'Y'){

                if($_POST['gender'][$i] == 'male'){

                    $salutation = 'Mr';
                }
                else if($_POST['gender'][$i] == 'female'){

                    $salutation = 'Mrs';
                }

                $relation = $_POST['relation'][$i];
            }
            else if($_POST['is_adult'][$i] == 'N'){

                if($_POST['gender'][$i] == 'male'){

                    $salutation = 'Master';
                    $relation = 5;
                }
                else if($_POST['gender'][$i] == 'female'){

                    $salutation = 'Ms';
                    $relation = 6;
                }
            }

            $data = [];

            $data['member_salutation']  = $salutation;
            $data['relation_with_proposal']  = $relation;
            $data['first_name'] = $_POST['full_name'][$i];
            $data['last_name'] = '';
            $data['gender'] = $_POST['gender'][$i];
            $data['insured_member_dob'] = date('Y-m-d', strtotime($_POST['dob'][$i]));
            $data['created_by'] = 0;
            $data['member_id']  = isset($_POST['member_id'][$i]) ? $_POST['member_id'][$i] : 0;
            $data['lead_id'] = $this->session->userdata('lead_id');
            $data['customer_id'] = $this->session->userdata('customer_id');
            $data['trace_id'] = $this->session->userdata('trace_id');
            $data['plan_id'] = $this->session->userdata('plan_id');
            $data['proposal_id']  = $this->session->userdata('proposal_details_id');
            $data['quote_id'] = $this->session->userdata('quote_ids');
            $data['source'] = 'customer';
    
            $addEdit = curlFunction(SERVICE_URL . '/api2/proposalInsuredMemberSubmit', $data);
    
            $addEdit = json_decode($addEdit, true);
    
    
            if ($addEdit['status_code'] == '200') {

                $result[] = array('success' => true, 'msg' => $addEdit['Metadata']['Message'], 'member_id' => $addEdit['Metadata']['member_id'], 'data_added' => $addEdit['Metadata']['data_added']);
                
            } else {
    
                if(strtolower($addEdit['Metadata']['Message']) == 'member already added'){
                    
                    $result[] = array('success' => false, 'proceed' => 1, 'msg' => $addEdit['Metadata']['Message']);
                }
                else{
                    
                    $result[] = array('success' => false, 'msg' => $addEdit['Metadata']['Message']);
                }
            }
        }

        echo json_encode($result);exit;
    }

    public function saveCustomerDetails(){

        $_POST['source'] = 'customer';
        $_POST['lead_id'] = $this->session->userdata('lead_id');
        $_POST['customer_id'] = $this->session->userdata('customer_id');
        $_POST['trace_id'] = $this->session->userdata('trace_id');
        $_POST['plan_id'] = $this->session->userdata('plan_id');

        
        $result = curlFunction(SERVICE_URL . '/customer_api/saveCustomerDetails', $_POST);

        $result = json_decode($result, true);

        // print_r($result);exit;

        if($result['status'] == 200){

            $proposal_details_id = encrypt_decrypt_password($result['data']['proposal_details_id'], 'E');
            $quotes_id = encrypt_decrypt_password($result['data']['quote_ids'], 'E');

            $this->session->set_userdata('proposal_details_id', $proposal_details_id);
            $this->session->set_userdata('quote_ids', $quotes_id);

            $data['member_salutation']  = $_POST['salutation'];
            $data['relation_with_proposal']  = 1;
            $data['first_name'] = $_POST['first_name'];
            $data['last_name'] = $_POST['last_name'];
            $data['gender'] = $_POST['gender'];
            $data['insured_member_dob'] = date('Y-m-d', strtotime($_POST['proposer_dob']));
            $data['customer_id']  = $_POST['customer_id'];
            $data['created_by'] = 0;
            $data['member_id']  = isset($_POST['member_id']) ? $_POST['member_id'] : 0;
            $data['lead_id']  = $_POST['lead_id'];
            $data['plan_id']  = $_POST['plan_id'];
            $data['trace_id']  = $_POST['trace_id'];
            $data['proposal_id']  = $proposal_details_id;
            $data['quote_id'] = $quotes_id;
            $data['source'] = $_POST['source'];
            
            $addEdit = curlFunction(SERVICE_URL . '/api2/proposalInsuredMemberSubmit', $data);

            $addEdit = json_decode($addEdit, true);


			if ($addEdit['status_code'] == '200') {
				echo json_encode(array('success' => true, 'msg' => $addEdit['Metadata']['Message'], 'member_id' => $addEdit['Metadata']['member_id'], 'data_added' => $addEdit['Metadata']['data_added']));
				exit;
			} else {

                if(strtolower($addEdit['Metadata']['Message']) == 'member already added'){

                    echo json_encode(array('success' => false, 'proceed' => 1, 'msg' => $addEdit['Metadata']['Message']));
                }
                else{

                    echo json_encode(array('success' => false, 'msg' => $addEdit['Metadata']['Message']));
                }
				
				exit;
			}
        }
        else{

            echo json_encode(array('success' => false, 'msg' => "No Records Found"));
			exit;
        }
    }
}

