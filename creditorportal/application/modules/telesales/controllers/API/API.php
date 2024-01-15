
<?php

if (!defined('BASEPATH')) {
  exit('No direct script access allowed');
}

class API extends CI_Controller 
{

  function __construct() {
    parent::__construct();
    
    $this->load->model("API/API_m", "API_m");
  }

  public function get_hospitals_by_name() {
    if(!empty($this->input->post('hospital_name'))){
     echo json_encode($this->API_m->get_hospitals_by_name());
   }
 }

 function getProposalService() {
  //policy_number policy_detail_id, company_id 
  extract($this->input->post(null, true));
  $search_by = "account_number";
  
  $search_by_field = $this->db->where([
    "search_by" => $search_by
  ])->get("master_customer_search")->row();


  $this->load->library("Curl_request");

  $opt = [
    CURLOPT_URL => "https://jsonplaceholder.typicode.com/todos/".$search_by_field->id,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"test\"\r\n\r\ntest\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
    CURLOPT_HTTPHEADER => array(
      "cache-control: no-cache",
      "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
      "postman-token: 15cfe0aa-44d5-b854-45e6-7c7a1632c674"
    ),
  ];

  $data = $this->curl_request->getData($opt);

  $this->db->where([
    "employer_id" => $employer_id
  ])->get("api_master_match_column")->result();

 }

 public function get_ecard(){
  extract($this->input->post(null, true));
  if(!empty($emp_id)){
    echo json_encode($this->API_m->get_ecard($emp_id));
  }
}

public function get_login_details_API() {
  extract($this->input->post(null, true));

  $emp_type_id = 1;

  $employee_pwd = encrypt_decrypt_password($employee_pwd);

  $query = $this->db
  ->select('employee_details.*,master_company.flex_allocation')
  ->from('employee_details')
  ->join('master_company','employee_details.company_id = master_company.company_id','inner')
  ->where(['email' => $employee_email, 'password' => $employee_pwd])
  ->where("find_in_set('" . $emp_type_id . "',access_right_id) != 0")
  ->get()
  ->row_array(); 



  if ($query && count($query) > 0) {

    $query2 = $this->db->where_in('access_right_id', explode(',', $query['access_right_id']))->get('access_rights')->result_array();
    $currentAccessRight = $query2[0]['access_right_id'];

    $arr = [
      'current_access_right' => $currentAccessRight,
      'emp_id' => base64_encode($query['emp_id']),
      'emp_code' => $query['emp_code'],
      'emp_name' => $query['emp_firstname'],
      'emp_last_name' => $query['emp_lastname'],
      'emp_full_name' => $query['emp_firstname'] . ' ' . $query['emp_lastname'],
      'desc_id' => $query['emp_designation'],
      'company_id' => $query['company_id'],
      'email_id' => $query['email'],
      'flex_status' => $query['flex_allocation'],
      'gender' => $query['gender'],
    ];

    echo json_encode($arr);

  } else {
    echo '0';
  }
}


public function home_API() {

        // $emp_id = $this->input->post('emp_id');
  $emp_id = base64_decode($this->input->post('emp_id'));

  if(!empty($emp_id)){

    $data['employee_get_otherpolicy'] = employee_get_otherpolicy($emp_id);
    $data['show_topupon_summary_flex'] = show_topupon_summary_flex($emp_id);

    $this->load->helper('conversion_function_helper');

    $data['policy_data'] = $this->API_m->get_policy_detail($emp_id);
    $data['family_cover_data'] = $this->API_m->get_emp_data_flexi($emp_id);
    $data['Voluntary_Wellness_data'] = $this->API_m->all_flex_data($emp_id);

    $data['policy_list'] = $this->API_m->list_view($emp_id);
    $data['policy_type'] = $this->API_m->get_policy_type();

    echo json_encode($data,JSON_UNESCAPED_SLASHES);

  }
}

public function policy_member_detail() {

  $policy_no = $this->input->post('policy_no');
  $emp_id = base64_decode($this->input->post('emp_id'));

  if(!empty($policy_no)){

    $data['policy_view'] = $this->API_m->get_policy_view($policy_no,$emp_id);

    echo json_encode($data,JSON_UNESCAPED_SLASHES);

  }
}

public function resetPassword() {
  if (!empty($this->input->post('emp_id'))) {
    extract($this->input->post(null, true));
    $emp_id = base64_decode($emp_id);

    $oldPass = encrypt_decrypt_password($oldPass);
    $rowCount = $this->db->where(["emp_id" => $emp_id, "password" => $oldPass])->get("employee_details")->num_rows();

    if ($rowCount > 0) {
      $newPass = encrypt_decrypt_password($newPass);
      $this->db->where(["emp_id" => $emp_id])->update("employee_details", [
        "password" => $newPass
      ]);

      echo json_encode("Password Reset Success");
    } else {
      echo json_encode("Invalid Old Password");
    }
  }

}

function get_all_policy_no(){
  $emp_id = base64_decode($this->input->post('emp_id'));
  if(!empty($emp_id)){
    echo json_encode($this->API_m->get_all_policy_no($emp_id),JSON_UNESCAPED_SLASHES);
  }

}

function network_hospital_sms(){
  if(!empty($this->input->post('mobile_no'))){
    echo json_encode($this->API_m->network_hospital_sms());
  }

}

function set_network_hospital_mails(){
  if(!empty($this->input->post('email_id'))){
    echo json_encode($this->API_m->set_network_hospital_mails());
  }
}



public function get_all_formcenter_files() {
  //print_r($_POST['company_id']);exit;
    if(!empty($this->input->post('company_id'))){

        $dir = APPPATH . '/resources/uploads/formcenter/' . $this->input->post('company_id') . '/';
        //print_r($dir);exit;
        $i = 0;
        $data = [];
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
               // echo "1";exit;
                $scanned_directory = array_diff(scandir($dir), array('..', '.'));
              
                $z = array_values($scanned_directory);
               // print_pre($z);exit;
               //$z = [];
                for ($i = 0; $i < count($z); $i++) {
                    
                     $scanned_directory1 = array_diff(scandir($dir."/".$z[$i]), array('..', '.'));
                     $y = array_values($scanned_directory1);
                   // print_pre($y);exit;
                   
                   for ($j = 0; $j < count($y); $j++) {
                       $file_name = explode(".", $y[$j]);
                     // echo str_replace('_',' ',$file_name[0]);exit;
                      $data[$z[$i]][] = [
                          "name" => str_replace('_',' ',$file_name[0]),
                          // "src" => '/application/resources/uploads/formcenter/' . $this->session->userdata('company_id') . '/' . $z[$i].'/'.$y[$j]
                          "src" => '/application/resources/uploads/formcenter/' . $this->input->post('company_id') . '/' . $z[$i].'/'.$y[$j]
                      ];
                   }
                   
                   
                } 
                closedir($dh);
            }
        }
        //print_r(json_encode($data));
        echo json_encode($data);
        //return ($data);

    }

}

public function get_family_membername_from_policy_no()
{
  if(!empty($this->input->post('emp_id'))){
   echo json_encode($this->API_m->get_family_membername_from_policy_no());
 }
}

public function add_reimbursement_claim()
{
  if(!empty($this->input->post('patient_name'))){
    echo json_encode($this->API_m->employee_claim_intimate_insert());
  }

}

public function add_cashless_claim()
{
 if(!empty($this->input->post('patient_name'))){
  echo json_encode($this->API_m->employee_claim_cashless_insert());
}
}

public function get_all_states() {
  echo json_encode($this->API_m->get_all_states());
}

public function get_city_from_states() {

  if(!empty($this->input->post('state_names'))){
    echo json_encode($this->API_m->get_city_from_states());
  }

}

function get_hospital_name()
{
  if(!empty($this->input->post('policy_no'))){
    echo json_encode($this->API_m->get_hospital_name());
  }
}

public function claims_save() {
  if(!empty($this->input->post('patient_name'))){
    echo json_encode($this->API_m->claims_save());
  }

}

public function save_hospitalizationdetails() {
 if(!empty($this->input->post('claim_reimb_id'))){
  echo json_encode($this->API_m->save_claim_reimb_hospitalization());
}

}

public function save_claims_bill(){
  if(!empty($this->input->post('claim_id'))){
   echo json_encode($this->API_m->save_claims_bill());
 }
} 

public function get_dates_on_claim_id()
{
  if(!empty($this->input->post('claim_id'))){
    $data['date_docs'] = $this->API_m->get_datesdocs_on_claim_id();
    $data['date_claim'] = $this->API_m->get_dates_on_claim_id();

    echo json_encode($data);
  }
}   

public function get_claimid_on_member_id()
{
  if(!empty($this->input->post('member_id'))){
    echo json_encode($this->API_m->get_claimid_on_member_id());
  }
}

function get_member_details(){
  if(!empty($this->input->post('patient_id'))){
   echo json_encode($this->API_m->get_member_details());
 }
}

public function get_family_memberrel_from_policy_no()
{
  if(!empty($this->input->post('policy_no'))){
   echo json_encode($this->API_m->get_family_memberrel_from_policy_no());
 }
}

function get_family_details_on_relationship() {
  if(!empty($this->input->post('fr_id'))){
   echo json_encode($this->API_m->get_family_details_on_relationship());
 }
}

public function get_all_submit_claim_data()
{
  if(!empty($this->input->post('emp_id'))){
   echo json_encode($this->API_m->get_all_submit_claim_data());
 }
}

public function get_all_intimate_claim_data()
{
  if(!empty($this->input->post('emp_id'))){
   echo json_encode($this->API_m->get_all_intimate_claim_data());
 }
}


public function add_dependent()
{
  if(!empty($this->input->post('emp_id'))){
   echo json_encode($this->API_m->add_dependent());
 }
}

function final_enllorment_save(){
  if(!empty($this->input->post('emp_id'))){
        // $data['send_mail'] = $this->API_m->set_enrollment_mails();
   $data['enrol_flex_submit'] = $this->API_m->enrollment_submit_flex_data();

   echo json_encode($data);
 }
}

function get_all_enrollment_data() {

 if(!empty($this->input->post('emp_id'))){
   $data['voluntary_cover_data'] = $this->API_m->get_utilised_data();
   $data['group_cover_data'] = $this->API_m->get_base_policy_record();
   $data['amt_pay_data'] = $this->API_m->get_flex_active_record();
   echo json_encode($data);
 }
}

function get_type_by_date(){
  if(!empty($this->input->post('claim_id'))){
   echo json_encode($this->API_m->get_data());
 }
}

     /*
      employee get profile details
    */


      public function get_profile_per_details(){
        // echo 111;exit;
        extract($this->input->post(null, true));
      //print_r(json_encode($this->API_m->get_emp_details($emp_id)));

        $data1['profile_details'] = $this->API_m->get_emp_details($emp_id);
        $data1['flag'] = $this->API_m->all_confirmed_flex_data();

        $data1['total_flex_balance'] = $this->API_m->fetch_flexi_benefit_flex_summary_typewise($emp_id);
        $data2 = $this->API_m->testing_new($emp_id);

        if(empty($data2)){
        	$data1['remain_amt'] = $data1['total_flex_balance']->Flex_Wallet;
        }else{
        	$data1['remain_amt'] = $data1['total_flex_balance']->total_balance;
        }
        print_r(json_encode($data1));
      }

      public function get_family_details_per_emp(){
        extract($this->input->post(null, true));
        print_r(json_encode($this->API_m->get_family_details_per_emp($emp_id)));
      //print_r(json_encode($this->API_m->get_family_members($emp_id)));
      }

      public function get_same_employee_address(){
        extract($this->input->post(null, true));
        print_r(json_encode($this->API_m->get_employeee_address($emp_id)));
      }

      public function getIfscCode()
      {
		
		  extract($this->input->post(null,true));
		  if($policy_id)
		  {
			
		  $query = $this->db->query("select epd.company_id,mcom.comapny_name,pmws.master_ifsc_tbl_name from employee_policy_detail as epd left join master_company as mcom ON epd.company_id=mcom.company_id right join product_master_with_subtype as pmws ON epd.parent_policy_id=pmws.policy_parent_id where epd.parent_policy_id='$policy_id' group by epd.parent_policy_id");
		  if($query->num_rows()>0)
		  {
			  $result = $query->row_array();
			if($bank)
			{
				
				 if($result['company_id']=='327' || $result['company_id']=='251')
			  {
				  
					$bank_name = $result['comapny_name'];
					
				
					//$data = $this->API_m->getIfscCode($condition);
					
					echo json_encode(strtoupper($bank_name));
					
					
			  }else {
				  
				  $table = $result['master_ifsc_tbl_name'];
  				$condition = ['ifsc_code' => $this->input->post('ifsc_code')];
  				$data = $this->API_m->getIfscCode1($condition,$table);
  				echo json_encode($data);
			  }
			}
			else
			{
		
				 $table = $result['master_ifsc_tbl_name'];
				 $condition = ['ifsc_code' => $this->input->post('ifsc_code')];
				
				 $data = $this->API_m->getIfscCode1($condition,$table);
				 echo json_encode($data); 
				 
			}
			 
		  }
		  }
		  else
		  {
			 $condition = ['ifsc_code' => $this->input->post('ifsc_code')];
				$data = $this->API_m->getIfscCode($condition);
				echo json_encode($data); 
		  }
		  
      }

      public function getBankName()
      {
        echo json_encode($this->API_m->getBankName());
      }

      public function getBankCity()
      {
        $condition = ['bank_name' => $this->input->post('bank_name')];
        echo json_encode($this->API_m->getBankCity($condition));
      }

      public function getBankBranch()
      {
        $condition = ['bank_name' => $this->input->post('bank_name'), 'bank_city' => $this->input->post('bank_city')];
        echo json_encode($this->API_m->getBankBranch($condition));
      }

      

    /*
       employee pincode fetch city and state data
    */

       public function pincode_get_state_city(){
        extract($this->input->post(null, true));
        print_r(json_encode($this->API_m->get_state_city($pincode)));
      }
	  //sonal
	  public function axis_pincode_get_state_city(){
        extract($this->input->post(null, true));
        print_r(json_encode($this->API_m->axis_state_city($pincode)));
      }
	  
      function generateRandomString($length = 4) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
      }

    /*
       employee -: add_family_members
    */


       public function add_family_members()
       {
        extract($this->input->post(NULL, true));

        $family_code = $this->generateRandomString().uniqid();

        $emp_id = base64_decode($this->input->post('emp_id'));

        $data_exists = $this->API_m->check_employee_exists($emp_id, $fr_id);
          //print_r($data_exists);exit;

        if(!empty($data_exists))
        {

          if ($data_exists[0]['multiple_allowed'] == 'Y') 
          {
            $fami_dob = str_replace('/', '-', $family_dob);
            $family_dob_date = date('Y-m-d',strtotime($fami_dob));

            if($fr_id == 1)
            {
              if(!empty($marriage_date))
              {
                $ma_date = str_replace('/', '-', $marriage_date);
                $m_date = date('Y-m-d',strtotime($ma_date));

                if($m_date < $family_dob_date) {
                  echo json_encode(['status' => 0, 'msg' => 'Marriage date cannot be less than Date of Birth']);
                  exit();
                }
              }
              else
              {
                echo json_encode(['status' => 0, 'msg' => 'Please select Marriage date']);
                exit();
              }
            }

            $data = array(
              'fr_id' => $fr_id,
              'family_dob' => date('Y-m-d',strtotime($family_dob)),
              'marriage_date' => (!empty($marriage_date))?date('Y-m-d',strtotime($marriage_date)):'',
              'family_firstname' => $family_firstname,
              'family_lastname' => $family_lastname,
              'family_flat' => $family_flat,
              'cities' => $cities,
              'family_location' => $family_location,
              'family_street' => $family_street,
              'state_names' => $state_names,
              'family_pincode' => $family_pincode,
              'family_contact' => $family_contact,
              'family_email' => trim($family_email),
              'family_gender' => $family_gender,
              'created_at' => date('Y-m-d H:i'),
              'family_code' => $family_code
            );

            $status = $this->db->insert('employee_family_details', $data);

            $family_id = $this->db->insert_id();

            $arr_family = [
              'emp_id' => $emp_id,
              'family_id' => $family_id,            
              'created_on' => date('Y-m-d H:i:s')];

              $res = $this->db->insert('family_relation', $arr_family);
              if($res)
              {
                echo json_encode(['status' => 1, 'msg' => 'Family Member Data Insert Successfully']);
              }else{
                echo json_encode(['status' => 0, 'msg' => 'Data not inserted']);
              }
            }
            else 
            {
              echo json_encode(['status' => 0, 'msg' => 'you can add only 1 '. $data_exists[0]['fr_name']]);
            }
          }
          else
          {

            $fami_dob = str_replace('/', '-', $family_dob);
          // print_r($fami_dob);exit;
            $family_dob_date = date('Y-m-d',strtotime($fami_dob));
          //print_r($family_dob_date);exit;

            if($fr_id == 1)
            {
              if(!empty($marriage_date))
              {
                $ma_date = str_replace('/', '-', $marriage_date);
                $m_date = date('Y-m-d',strtotime($ma_date));

                if($m_date < $family_dob_date) {
                  echo json_encode(['status' => 0, 'msg' => 'Marriage date cannot be less than Date of Birth']);
                  exit();
                }
              }
              else
              {
                echo json_encode(['status' => 0, 'msg' => 'Please select Marriage date']);
                exit();
              }
            }

            $data = array(
              'fr_id' => $fr_id,
              'family_dob' => date('Y-m-d',strtotime($family_dob)),
              'marriage_date' => (!empty($marriage_date))?date('Y-m-d',strtotime($marriage_date)):'',
              'family_firstname' => $family_firstname,
              'family_lastname' => $family_lastname,
              'family_flat' => $family_flat,
              'cities' => $cities,
              'family_location' => $family_location,
              'family_street' => $family_street,
              'state_names' => $state_names,
              'family_pincode' => $family_pincode,
              'family_contact' => $family_contact,
              'family_email' => trim($family_email),
              'family_gender' => $family_gender,
              'created_at' => date('Y-m-d H:i'),
              'family_code' => $family_code
            );

            $status = $this->db->insert('employee_family_details', $data);

            $family_id = $this->db->insert_id();

            $arr_family = [
              'emp_id' => $emp_id,
              'family_id' => $family_id,            
              'created_on' => date('Y-m-d H:i:s')
            ];

            $res = $this->db->insert('family_relation', $arr_family);
            if($res)
            {
              echo json_encode(['status' => 1, 'msg' => 'Family Member Data Insert Successfully']);
            }else{
              echo json_encode(['status' => 0, 'msg' => 'Data not inserted']);
            }
          }
        }



    /*
       employee -: update_family_members
    */
      public function update_family_members()
      {
        date_default_timezone_set('Asia/Kolkata');
        extract($this->input->post(NULL, true));
        $date = date('m/d/Y h:i:s', time());
        $family_id = $this->input->post('family_id');
        //print_r($family_dob);exit;
        $query = $this->db->where(['family_id' => $family_id])->get('employee_family_details')->num_rows();
        //print_r($query);exit;
        

        if($query > 0)
         {
            $data = $this->db->select('fr_id')->from('employee_family_details')->where('family_id',$family_id)->get()
                ->row();
            $fami_dob = str_replace('/', '-', $family_dob);
            $family_dob_date = date('Y-m-d',strtotime($fami_dob));

            //print_r($family_dob_date);exit;
            
            if($data->fr_id == 1)
            {
              if(!empty($marriage_date))
              {
                $ma_date = str_replace('/', '-', $marriage_date);
                $m_date = date('Y-m-d',strtotime($ma_date));
                if($m_date < $family_dob_date) {
                echo json_encode(['status' => 0, 'msg' => 'Marriage date cannot be less than Date of Birth']);
                  exit();
                }
              }
              else
              {
                echo json_encode(['status' => 0, 'msg' => 'Please select Marriage date']);
                exit();
              }
            }

            $data1 = array(
                'family_firstname' => $this->input->post('family_firstname'),
                'family_lastname' => $this->input->post('family_lastname'),
                'family_dob' => date('Y-m-d',strtotime($family_dob)),
                'marriage_date' => (!empty($marriage_date))?date('Y-m-d',strtotime($marriage_date)):'',
                'family_email' => $this->input->post('family_email'),
                'family_contact' => $this->input->post('family_contact'),                    
                'family_flat' => $this->input->post('family_flat'),
                'family_location'=> $this->input->post('family_location'),
                'family_street' => $this->input->post('family_street'),
                'family_pincode' => $this->input->post('family_pincode'),
                'cities' => $this->input->post('cities'),
                'state_names' => $this->input->post('state_names'),
                'created_at' => date('Y-m-d H:i:s')
            );

            $result = $this->API_m->update_family_members($data1, $family_id);
            // print_r($db);exit;
            if($result){
                 echo json_encode(['status' => 1, 'msg' => 'Family Member Data Update Successfully']);
             }else{
                 echo json_encode(['status' => 0, 'msg' => 'Not a updated a data']);
             }
          }
        else
         {
             echo json_encode(['status' => 0, 'msg' => 'Not a valid a data']);
         }
      }

      /*
       end update_family_members
      */
       

    public function get_policy_detail_type_wise(){
      extract($this->input->post(null, true));
      echo json_encode($this->API_m->fetch_detail_policy_type_wise($emp_id));
    }

    /*
       Employee flexi benefites details
    */

       public function flexi_details(){
        //echo 111;exit;
        extract($this->input->post(null, true));
      // print_r($_POST);
        $policy_type = $this->input->post('policy_type');

        switch($policy_type){

          case 'Voluntary':

          $dataa = $this->API_m->fetch_flexi_benefit_all_voluntary_typewise($emp_id);

          $def_data[0]['master_flexi_benefit_id'] = '1';
          $def_data[0]['flexi_benefit_name'] = 'Sodexo';
          $def_data[0]['img_name'] = '/public/assets/images/new-icons/sodexo.png';
          $def_data[0]['flex_amount'] = '0';
          $def_data[0]['final_amount'] = '0';
          $def_data[0]['balance_amount'] = '0';
          $def_data[0]['pay_amount'] = '0';
          $def_data[0]['deduction_type'] = 'F';
          $def_data[0]['sum_insured'] = '0';

          $status = 0;
          if(!empty($dataa)){

           foreach ($dataa as $key => $value1) {
            if($value1['master_flexi_benefit_id'] == '1'){
              $status = 1;
            }
          }

          if(!empty($def_data) && $status==0){
            $data1['data'] = array_merge($dataa,$def_data);
          }else{
            $data1['data'] = $dataa;
          }

        }else{

          if(!empty($def_data)){
           $data1['data']=$def_data;
         }else{
          $data1 = [];
        }
      }

      break;

      case 'Wellness':
      $dataa = $this->API_m->fetch_detail_policy_type_wellness($emp_id);
      $data = $this->API_m->amt_data($emp_id);

      $mergeData = [];

      foreach ($dataa as $key => $value1) {

        if(!empty($data)){
          foreach ($data as $key => $value) {

            if($value1['master_flexi_benefit_id']==$value['master_flexi_benefit_id']) {
             $mergeData['flex_amount'] = $value['flex_amount'];
             $mergeData['final_amount'] = $value['final_amount'];
             $mergeData['balance_amount'] = $value['balance_amount'];
             $mergeData['pay_amount'] = $value['pay_amount'];
             break;
           }else{
             $mergeData['flex_amount'] = '0';
             $mergeData['final_amount'] = '0';
             $mergeData['balance_amount'] = '0';
             $mergeData['pay_amount'] = '0';
           }
         }
       }else{
         $mergeData['flex_amount'] = '0';
         $mergeData['final_amount'] = '0';
         $mergeData['balance_amount'] = '0';
         $mergeData['pay_amount'] = '0';
       }



       $data1['data'][] = array_merge($value1, $mergeData);
     }

     break;


     case 'Flex Summary':

     $data['Group_cover'] = $this->API_m->fetch_detail_policy_type($emp_id);
            // var_dump($data);exit;
     $data['family_cover_data'] = $this->API_m->get_emp_data_flexi(base64_decode($emp_id));
            // var_dump($data);exit;
     $data['profile_details'] = $this->API_m->get_emp_details($emp_id);

     $abc[] = $data['profile_details'][0]->emp_firstname." ".$data['profile_details'][0]->emp_lastname;
     $abc1[] = $data['profile_details'][0]->bdate;
     $abc2[] = $data['profile_details'][0]->gender;
     $abc3[] = 'Self';

     foreach ($data['Group_cover'] as $key => $value) {

      $test1[] = $value['name']; 
      $test2[] = $value['bdate']; 
      $test3[] = $value['policy_mem_gender']; 
      $test4[] = $value['relationship']; 
      

    }

    $abcd = array_merge($abc,$test1);
    $abcd1 = array_merge($abc1,$test2);
    $abcd2 = array_merge($abc2,$test3);
    $abcd3 = array_merge($abc3,$test4);

    foreach ($data['family_cover_data'] as $key => $value) {

      if($value['policy_sub_type_name'] == 'Group Mediclaim' && $value['policy_sub_type_name'] == $data['Group_cover'][$key]['policy_sub_type_name']){
        $test['family_cover'][] = $value['policy_mem_sum_insured'];
        $test['type'][] = $value['policy_sub_type_name'];
        $test['member_name'][] = $abcd; 
        $test['bdate'][] = $abcd1; 
        $test['gender'][] = $abcd2; 
        $test['rel'][] = $abcd3; 
        $test['policy_no'][] = $value['policy_no']; 
      }

      if($value['policy_sub_type_name'] == 'Group Term Life'){

        $test['family_cover'][] = $value['policy_mem_sum_insured'];
        $test['type'][] = $value['policy_sub_type_name'];
        $test['member_name'][] = $abc; 
        $test['bdate'][] = $abc1; 
        $test['gender'][] = $abc2; 
        $test['rel'][] = $abc3; 
        $test['policy_no'][] = $value['policy_no'];
      }

      if($value['policy_sub_type_name'] == 'Group Personal Accident'){
        $test['family_cover'][] = $value['policy_mem_sum_insured'];
        $test['type'][] = $value['policy_sub_type_name'];
        $test['member_name'][] = $abc; 
        $test['bdate'][] = $abc1; 
        $test['gender'][] = $abc2; 
        $test['rel'][] = $abc3; 
        $test['policy_no'][] = $value['policy_no'];
      }
    }


    $data1['Group_cover'] = $test;


    $data1['Voluntary_Coverage'] = $this->API_m->fetch_flexi_benefit_voluntary_typewise_summary($emp_id);
            //echo json_encode($data2);

    $data1['Wellness'] = $this->API_m->fetch_detail_policy_type_wellness_summary($emp_id);
            //echo json_encode($data3);

    $data1['total_flex_balence'] = $this->API_m->fetch_flexi_benefit_flex_summary_typewise($emp_id);
	
	$data2 = $this->API_m->testing_new($emp_id);

        if(empty($data2)){
        	$data1['total_flex_balence']->total_balance = $data1['total_flex_balence']->Flex_Wallet;
        }else{
        	$data1['total_flex_balence']->total_balance = $data1['total_flex_balence']->total_balance;
        }
            // echo json_encode($data4);
    break;

    default:
    echo 'Please make a new selection...';
    break;
  }

  echo json_encode($data1);
}

public function forgotPassword() {

  if (@$this->input->post()) {

    extract($this->input->post());

    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;

    for ($i = 0; $i < 8; $i++) {
      $n = rand(0, $alphaLength);
      $pass[] = $alphabet[$n];
    }

    $passEmail = implode($pass);

//        $salt = sha1($passEmail);
//        $pass = md5($salt . $passEmail);
    $pass = encrypt_decrypt_password($passEmail);

    $num = $this->db->where(["email" => $email])->get("employee_details")->num_rows();

    if ($num > 0) {
      $this->db->where(["email" => $email])->update("employee_details", [
        "password" => $pass
      ]);


      $user_mail_data = [
        'to' => $email,
        'subject' => "Reset Password",
        'message' => "<p>Here is your User Name: $email</p><p>Password: $passEmail</p>",
      ];
      $user_mail_data['to'] = 'amit.matani12@gmail.com';

           // echo "here";exit;
      $this->load->library('email');
      if (Email::sendMail($user_mail_data)) {
        $insert_data = [
          "type" => "E",
          "to" => $email,
          "content" => json_encode($user_mail_data),
          "status" => "true"
        ];
        $this->db->insert('queue_log',$insert_data);
               // echo "inserted";exit;
        
      }
      $data = 'Password reset instructions is send to you by Email';

    } else {

      $data = 'Email not found';
    }


  }else{
    $data = 'Please fill email id';
  }
  echo json_encode($data);
}



public function update_flexi_details(){
	extract($this->input->post(null,true));

	if(!empty($flex_amount) && !empty($emp_id) && !empty($master_flexi_benefit_id) && !empty($policy_type) && !empty($deduction_type)){

		switch($policy_type){
      case 'Voluntary' :
      $flexi_type = "C";
      if(!empty($deduction_type)){
       
        $data = $this->API_m->update_balance_in_flex_benefit();
      
    }

    break;

    case 'Wellness' :
    $flexi_type = "N";
    if(!empty($deduction_type)){
     
      $data = $this->API_m->update_balance_in_flex_benefit();
  }

  break;

  default:
  echo 'Please select at list one flexi benefites..!!';
  break;
}

}


}

public function reset_flexi_benefits(){
  extract($this->input->post(null,true));
      //emp_id, policy_type, master_flexi_benefit_id
  $policy_type = $this->input->post('policy_type');
  $emp_id = $this->input->post('emp_id');
      //var_dump($emp_id);exit;
  $master_flexi_benefit_id = $this->input->post('master_flexi_benefit_id');
      // var_dump($master_flexi_benefit_id);exit;

  switch($policy_type){
    case 'Voluntary' :
    $data = $this->API_m->reset_data_from_flexi_benefit_voluntary_transaction($emp_id, $master_flexi_benefit_id);
    break;

    case 'Wellness' :
    $data = $this->API_m->reset_data_from_flexi_benefit_wellness_transaction($emp_id, $master_flexi_benefit_id);
    break;

    default:
    echo 'Please select at list one flexi benefites..!!';
    break;
  }
      //echo json_encode($data);
}

    /*
       employee_enrollment insert or update
    */

       public function update_employee_enrollment_details(){

        extract($this->input->post(null, true));
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y/m/d h:i:s', time());
        $query = $this->db->where(['emp_id' => base64_decode($emp_id)])->get('employee_details')->num_rows();
      // var_dump($query);exit;

        if($query > 0)
        {
          $data = array(
                    // 'fr_id' => $this->input->post('fr_id'),
                    // 'emp_firstname' => $this->input->post('emp_firstname'),
                    // 'emp_lastname' => $this->input->post('emp_lastname'),
                    // 'bdate' => $this->input->post('bdate'),
                    // 'doj' => $this->input->post('doj'),
            'mob_no' => $this->input->post('mob_no'),
                    // 'email' => $this->input->post('email'),
            'alt_email' => $this->input->post('alt_email'),
            'emp_emg_cont_name' => $this->input->post('emp_emg_cont_name'),
            'emg_cno' => $this->input->post('emg_cno'),
                    // 'emp_designation' => $this->input->post('emp_designation'),
            'created_at' => $date
          );

          $result = $this->API_m->update_employee_details($data,base64_decode($emp_id));
            //print_r($db);exit;
          if($result){
           echo json_encode(['status' => 1, 'msg' => 'Employee Data Update Successfully']);
         }else{
           echo json_encode(['status' => 0, 'msg' => 'Not a updated a data']);
         }

       }
       else
       {
        echo json_encode(['status' => 0, 'msg' => 'Not a valid a data updated.']);
      }
    }



    /*
      employee nominee added.
    */
      public function add_nominee_for_employee()
      {
        extract($this->input->post(null, true));
        //exit;

        $data = $this->API_m->get_share_per_nominee($emp_id);
        //var_dump($data);exit;
        //$total;
        $total = array_sum(json_decode($share_perArr));

        if($data !=0)
        {
          $balance = (100 - $data);
          
          if ($balance == 0 && $total == 0)
          {
            $test['status'] = "Nominees Share % Cannot Exceedd 100%";
          }
          else if($balance == $total)
          {
            $data1 = $this->API_m->deactivate_replace_add_nominee();
            if($data1['msg'] == 1)
            {
              $test['status'] = "Submitted Successfully";
                    //$test['response'] = $data1;

            }
            else if($data1['status'] == 'error'){
              $test['status'] = "Please Fill All Nominee And Guardian Details";
            }
            else{
              $test['status'] = "Nominees Share % Cannot Exceeddd 100%";
            }
          }
          else if($balance == 0)
          {
            $test['status'] = "Nominees Share % Cannot Exceeddd 100%";
          }
          else
          {   
            $test['status'] = "Share percent should be " . $balance . "%";
          }
        }
        else
        {
          if ($total > 100 || $total < 100) 
          {
            $test['status'] = "Share percent should be 100%";
          }
          else
          {
            $data2 = $this->API_m->add_nominee();
                // print_r($data2);exit;
            if($data2['msg'] == 1)
            {
              $test['status'] = "Submitted Successfully";
            }         

            else if($data2['status'] == 'error'){
              $test['status'] = "Please Fill All Nominee And Guardian Details";
            }else if($data2['status'] == 'error1'){
              $test['status'] = "You cannot add nominee until employee is not enrolled";
            }else if($data2['status'] == 'error2'){
              $test['status'] = "You cannot add 0 share percentage";
            }
            else{
              $test['status'] = "Nominees Share % Cannot Exceeddddd 100%";
            }
          }
        }
        echo json_encode($test);
      }
      
    /*
      update nominee for employee.
    */
      public function update_nominee_data(){
       print_r(json_encode($this->API_m->update_nominee_data()));
     }

    /*
      get nominee for employee.
    */
      
      public function get_nominee_details_for_employee()
      {
        extract($this->input->post(null, true));
        print_r(json_encode($this->API_m->get_nominee_details_for_employee($emp_id)));
      }

    /*
      delete nominee for employee.
    */

      public function delete_nominee() {
        extract($this->input->post(NULL, true));
        $emp_id = base64_decode($emp_id);
        //var_dump($emp_id);
        $data_present = $this->db->select('*')
        ->from('member_policy_nominee AS mpn')
        ->where('mpn.nominee_id', $nominee_id)
        ->where('mpn.emp_id', $emp_id)
        ->get()->result_array();
                        //echo $this->db->last_query();exit;
        if (!empty($data_present)) {
          $where = array(
            'emp_id' => $emp_id,
            'nominee_id' => $nominee_id
          );
          $update = array(
            'status' => 'deactive'
          );
          $data = $this->db->update('member_policy_nominee', $update, $where);
            // print_r($data);exit;
          if ($data == 1) {
            echo json_encode(['status' => 1, 'msg' => 'Nominee Deleted Successfully.']);
                // return true;
          } else {
            echo json_encode(['status' => 0, 'msg' => 'failed..']);
                // return false;
          }
        }
      }

      public function get_family_relation(){
        extract($this->input->post(null, true));
        print_r(json_encode($this->API_m->fetch_family_relation($emp_id)));
      }


      public function check_relation_already_exist_in_enrollment(){
        extract($this->input->post(null,true));
      //emp_id,fr_id
        $data1= $this->API_m->fetch_family_details_via_emp($emp_id, $fr_id);
      //var_dump($data1);exit;
      }

      public function fetch_details_relationwise_in_enrollment(){
        extract($this->input->post(null,true));
        $data['relation_details'] = $this->API_m->fetch_relationwise_details($emp_id, $rel_id);
        $data['policy_parent_data'] = $this->API_m->policy_parent_data($emp_id, $rel_id);

      //$data['get_min_max_age'] = $this->API_m->get_min_max_age($policy_id, $rel_id);
      //$data['get_family_details_from_relationship'] = $this->API_m->get_family_details_from_relationship($emp_id);
        
        echo json_encode($data);
        
      }

      public function voluntary_coverage_fetch_sodexo_amount(){
        extract($this->input->post(null,true));
        $data = $this->API_m->fetch_sodexo_amount_emp($emp_id);
        $data1 = explode(',', $data[0]->amount);
        $test1 = array();
        foreach ($data1 as $key => $value) {
          $test1[$key] = $value;
        }
        print_r(json_encode($test1));
      }

      public function calculate_age(){
        extract($this->input->post(null, true));
      //print_r($_POST);
        date_default_timezone_set('Asia/Kolkata');
        $family_dob = str_replace('/', '-', $family_dob);
        $dob = date('Y-m-d',strtotime($family_dob));
        $today = date("Y-m-d");
        $diff = date_diff(date_create($dob), date_create($today));
      // print_r($diff);

        $age_year = $diff->format('%y');

      $now = time(); // or your date as well
      $your_date = strtotime($dob);
      $datediff = $now - $your_date;

      $days =  round($datediff / (60 * 60 * 24));

      if($age_year == 0 && $days < 365){
        $data['age'] = $days;
        $data['age_type'] = "days";
      }else{
        $data['age'] = $age_year;
        $data['age_type'] = "years";
      }

      echo json_encode($data);
    }


    public function get_enrollment_policy_member_details() {
      extract($this->input->post(null, true));
      print_r(json_encode($this->API_m->get_policy_member_details($emp_id)));
    }

    public function delete_enrollment(){
      extract($this->input->post(null, true));
      print_r(json_encode($this->API_m->delete_enrollment($emp_id, $policy_member_id)));
    }

    public function update_enrollment(){
      print_r(json_encode($this->API_m->update_enrollment_policy()));
    }


    public function get_covers_details(){
      extract($this->input->post(null, true));
      $abcd = $this->API_m->get_covers_details($emp_id);

      $abc = $this->API_m->getGtliTopUpcalc($abcd[2]['sum_insured']); 
      foreach ($abcd as $key => $value) {
        if($value['policy_sub_type_name']== 'Voluntary Term Life'){
          $value['premium'] = "".$abc."";
          $data[] = $value;
        }else{
          $data[] = $value;
        }
      }
      echo json_encode($data);
      
    }

    public function add_covers()
    {
      extract($this->input->post(null, true));

      $arr = array("content" => json_encode($_POST));

      $this->db->insert('queue_log',$arr);

      //var_dump($emp_id);exit;
      $policy_sub_type_name = $this->input->post('policy_sub_type_name');//Voluntary group policies sub type name

      
      $balance = $this->input->post('total_balance');//total_balance
      $Wallet_Utilization = $this->input->post('Wallet_Utilization');//flex_utilised

      // $current_val = $this->input->post('premium');//premium OR ghi_total_premium OR ghi_estimate
      $current_val = $this->input->post('amount');//premium OR ghi_total_premium OR ghi_estimate OR pay amount
      $sum_insured = $this->input->post('sum_insured');//sum_insured
      $deduction_type = $this->input->post('deduction_type');//wallet or payroll

      if($deduction_type == 'F'){
        $flex_amount = $current_val;
      }else{
        $pay_amount = $current_val;
      }

      switch($policy_sub_type_name)
      {

        case 'Mediclaim Top-Up' :

        if ($current_val == '')
        {
          echo "Please Select Sum Insured";
          return false;
        } 
        else if ($deduction_type == undefined)
        {
          echo "Please Select Deduction Type";
          return false;
        }
        else
        {
          if ($deduction_type == 'F')
          {
              //echo 111;exit;
            $flex_utilised = $Wallet_Utilization + $current_val - $current_val;
              //print_r($flex_utilised);exit;
            if(($balance < $current_val && $balance != 0))
            {
              $cut_amt = $current_val - $balance;
            }
            else if($balance <= 0)
            {
              echo "Flex balance is not enough";
              return false;
            }
            else
            {
              $benifit_type = 3;
              $name = $policy_sub_type_name;
              $transac_type = 'C';
              $data2 = $this->API_m->save_all_data($benifit_type,$name,$transac_type);
                  // print_r($data2);exit;
              if($data2)
              {
                $data['status'] = "Submitted Successfully";
                $data['response'] = $data2;
                echo json_encode($data);
              }
              else
              {
                $data['status'] = "error occur";
                $data['response'] ='';
                echo json_encode($data);
              }
            }
          }
          else if($deduction_type == 'S')
          {
            $salary_deduction = $this->API_m->fetch_emp_pay_employeewise($emp_id);
              //print_r($salary_deduction[0]['emp_pay']);
            $total_salary = $salary_deduction[0]['total_salary'];
              // var_dump($total_salary);
            $salary_deduct = $salary_deduction[0]['emp_pay'] +  $current_val - $current_val;
            if($salary_deduct > $total_salary){
              echo "Please contact HR";
              return false;
            }

            $benifit_type = 3;
            $name = $policy_sub_type_name;
            $transac_type = 'C';
            $data2 = $this->API_m->save_all_data($benifit_type,$name,$transac_type);
              // var_dump($data2);
            if($data2)
            {
              $data['status'] = "Submitted Successfully";
              $data['response'] = $data2;
              echo json_encode($data);
            }
            else
            {
              $data['status'] = "error occur";
              $data['response'] ='';
              echo json_encode($data);
            }
          }
        }

        break;

        case 'Personal Accident Top-Up' :

        if ($current_val == '')
        {
          echo "Please Select Sum Insured";
          return false;
        } 
        else if ($deduction_type == undefined)
        {
          echo "Please Select Deduction Type";
          return false;
        }
        else
        {
          if ($deduction_type == 'F')
          {
              //echo 111;exit;
            $flex_utilised = $Wallet_Utilization + $current_val - $current_val;
              //print_r($flex_utilised);exit;
            if(($balance < $current_val && $balance != 0))
            {
              $cut_amt = $current_val - $balance;
            }
            else if($balance <= 0)
            {
              echo "Flex balance is not enough";
              return false;
            }
            else
            {
              $benifit_type = 4;
              $name = $policy_sub_type_name;
              $transac_type = 'C';
              $data2 = $this->API_m->save_all_data($benifit_type,$name,$transac_type);
              if($data2)
              {
                $data['status'] = "Submitted Successfully";
                $data['response'] = $data2;
                echo json_encode($data);
              }
              else
              {
                $data['status'] = "error occur";
                $data['response'] ='';
                echo json_encode($data);
              }
            }
          }
          else if($deduction_type == 'S')
          {
            $salary_deduction = $this->API_m->fetch_emp_pay_employeewise($emp_id);
              //print_r($salary_deduction[0]['emp_pay']);
            $total_salary = $salary_deduction[0]['total_salary'];
              // var_dump($total_salary);
            $salary_deduct = $salary_deduction[0]['emp_pay'] +  $current_val - $current_val;
            if($salary_deduct > $total_salary){
              echo "Please contact HR";
              return false;
            }

            $benifit_type = 4;
            $policy_sub_type_name;
            $transac_type = 'C';
            $data2 = $this->API_m->save_all_data($benifit_type,$name,$transac_type);
            if($data2)
            {
              $data['status'] = "Submitted Successfully";
              $data['response'] = $data2;
              echo json_encode($data);
            }
            else
            {
              $data['status'] = "error occur";
              $data['response'] ='';
              echo json_encode($data);
            }
          }
        }

        break;

        case 'Voluntary Term Life' :

        if ($current_val == '')
        {
          echo "Please Select Sum Insured";
          return false;
        } 
        else if ($deduction_type == undefined)
        {
          echo "Please Select Deduction Type";
          return false;
        }
        else
        {
          if ($deduction_type == 'F')
          {
              //echo 111;exit;
            $flex_utilised = $Wallet_Utilization + $current_val - $current_val;
              //print_r($flex_utilised);exit;
            if(($balance < $current_val && $balance != 0))
            {
              $cut_amt = $current_val - $balance;
            }
            else if($balance <= 0)
            {
              echo "Flex balance is not enough";
              return false;
            }
            else
            {
              $benifit_type = 5;
              $name = $policy_sub_type_name;
              $transac_type = 'C';
              $data2 = $this->API_m->save_all_data($benifit_type,$name,$transac_type);
              if($data2)
              {
                $data['status'] = "Submitted Successfully";
                $data['response'] = $data2;
                echo json_encode($data);
              }
              else
              {
                $data['status'] = "error occur";
                $data['response'] ='';
                echo json_encode($data);
              }
            }
          }
          else
          {
            $salary_deduction = $this->API_m->fetch_emp_pay_employeewise($emp_id);
              //print_r($salary_deduction[0]['emp_pay']);
            $total_salary = $salary_deduction[0]['total_salary'];
              // var_dump($total_salary);
            $salary_deduct = $salary_deduction[0]['emp_pay'] +  $current_val - $current_val;
            if($salary_deduct > $total_salary){
              echo "Please contact HR";
              return false;
            }

            $benifit_type = 5;
            $name = $policy_sub_type_name;
            $transac_type = 'C';
            $data2 = $this->API_m->save_all_data($benifit_type,$name,$transac_type);
            if($data2)
            {
              $data['status'] = "Submitted Successfully";
              $data['response'] = $data2;
              echo json_encode($data);
            }
            else
            {
              $data['status'] = "error occur";
              $data['response'] ='';
              echo json_encode($data);
            }
          }
        }

        break;

        default:
        echo 'Please select at list one covers..!!';
        break;
      }

    }

    public function reset_flexi_cover_data(){
      extract($this->input->post(null, true));
      $policy_sub_type_name = $this->input->post('policy_sub_type_name'); //Voluntary group policies sub type name
      switch ($policy_sub_type_name) {
        case 'Mediclaim Top-Up':
          # code...
        $benifit_type = 3;
        $data = $this->API_m->reset_flexi_data($benifit_type);
        break;

        case 'Personal Accident Top-Up':
          # code...
        $benifit_type = 4;
        $data = $this->API_m->reset_flexi_data($benifit_type);
        break;

        case 'Voluntary Term Life':
          # code...
        $benifit_type = 5;
        $data = $this->API_m->reset_flexi_data($benifit_type);
        break;
        
        default:
        echo 'Please select at list one covers..!!';
        break;
      }

      echo json_encode($data);
    }

    public function get_company_contactus_detail(){
      extract($this->input->post(null, true));
      if(!empty($emp_id)){
        echo json_encode($this->API_m->get_company_contactus_detail($emp_id));
      }
    }




  }

  ?>