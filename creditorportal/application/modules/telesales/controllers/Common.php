<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common extends CI_Controller {
    function __construct() {
        parent::__construct();
		$this->load->model('common_m');
	  $this->emp_id = $this->session->userdata('emp_id');               
    }
    
    function set_enrollment_mails(){
        print_r(json_encode($this->common_m->set_enrollment_mails()));
    }
function get_all_policy_no(){

 $this->load->model('common_m');
        print_r(json_encode($this->common_m->get_all_policy_no()));
}
function get_family_details_from_policy_no(){
 print_r(json_encode($this->common_m->get_family_details_from_policy_no()));
}
    function get_family_details_from_employee(){
         print_r(json_encode($this->common_m->get_family_details_from_employee()));
    }
function get_member_details(){
 print_r(json_encode($this->common_m->get_member_details()));
}
function get_employee_data(){
 print_r(json_encode($this->common_m->get_employee_data()));
}
function get_hospital_name()
    {
        print_r(json_encode($this->common_m->get_hospital_name()));
    }
    function get_states_from_policy_no()
    {
        print_r(json_encode($this->common_m->get_states_from_policy_no()));
    }
    function get_city_from_states_insurer()
    {
        print_r(json_encode($this->common_m->get_city_from_states_insurer()));
    }
      function get_state_city() {
        print_r(json_encode($this->common_m->get_state_city()));
    }

     function get_family_details_on_relationship() {
        print_r(json_encode($this->common_m->get_family_details_on_relationship()));
    }
     function get_individual_family_details() {
        print_r(json_encode($this->common_m->get_individual_family_details()));
    }
       function get_employee_data_for_sso() {
        print_r(json_encode($this->common_m->get_employee_data_for_sso()));
    }
			 function get_paramount_member_url($envelope){
        $this->load->library("paramount");
         $data = $this->paramount->getdata($envelope,'https://webintegrations.paramounttpa.com/TataMotorsWebAPI/Service1.svc/Get_Member_Ecards',array(
            "Content-Type: application/json",
            "cache-control: no-cache" 
         ));
    return  (($data["Get_Member_EcardsResult"]));
    }
        function get_ecard() {
			
			
			
			
			extract($this->input->post());
			
			if($ecard){
                           
				
				$data = $this->db
                     ->select('*')
                     ->from('employee_policy_member,family_relation,employee_details')
                     ->where('employee_policy_member.family_relation_id = family_relation.family_relation_id')
					 ->where('family_relation.emp_id = employee_details.emp_id')
					 ->where('employee_policy_member.tpa_member_id',$member_id)
                     ->get()
                     ->row_array();
					// print_pre($data);exit;
					
                                
			}
			else{
				$data = $this->db
                     ->select('ed.emp_code')
                     ->from('employee_details ed')
                     ->where('ed.emp_id', $this->emp_id)
                     ->get()
                     ->row_array();
					
   
			}
			
                       // echo "here";exit;
                        //$tpa_id = 5;
			
			 if ($tpa_id == 2) {
			//print_pre($envelope); die();
			 $array = [
            "USERNAME" => "TATA-MOTORS",
            "PASSWORD" => "ADMIN@123",
            "POLICY_NO" => $policy_no,
            "EMPLOYEE_NO"    => $data["emp_code"],
			  "NAME"  =>  $member_name
             //"NAME"  =>  $member_id
];
            
             $envelope = json_encode($array);
			// print_pre($envelope);exit;
			$result = $this->get_paramount_member_url($envelope);
			//print_pre($result);exit;
			if ($result) {
                if (strpos($result, 'Status') !== false) {
                    $return_array = [];
                    echo json_encode($return_array);
                } else {
                    $return_array["tpa_id"] = 2;
                    $return_array["response"] = $result;
                    echo json_encode($return_array);
                }
            }
			 }
			  else if ($tpa_id == 1) {
            $envelope = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>

<EcardRequest xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.datacontract.org/2004/07/BrokerIntegration.DTO">
  <EmployeeCode>' . $data["emp_code"] . '</EmployeeCode>
  <MemberId>' . $member_id . '</MemberId>
  <Password>TM3FA5g</Password>
  <PolicyNo>' . $policy_no . '</PolicyNo>
  <UserName>TataMotors</UserName>
</EcardRequest>

    </soap:Body>
</soap:Envelope>';

            //print_Pre($envelope);exit;
            $z = $this->common_m->ecard_link($envelope);
            if (is_array($z)) {

                $z['tpa_id'] = '1';

                echo(json_encode($z));
            } else {
                echo(json_encode([]));
            }
        } else if ($tpa_id == 4) {
            

            //health india block
//             $array = [
//                "UserName" => "TATA-MOTORS",
//                "Password" => "ADMIN@123",
//                "PolicyNo" => $policy_no,
//                "EmployeeCode" => $data["emp_code"],
//                "MemberId" => $member_id
//                    //"NAME"  =>  $member_id
//            ];
            $array = [
                "UserName" => "Rdo7L2MaIwmMXKSiKrefKg==",
                "Password" => "x51N2RZvkTsOYBZxASfwijCUe48DvM6bVHQx3gbzPtU=",
                "PolicyNo" => $policy_no,
                "EmployeeCode" => $data["emp_code"],
                "MemberId" => $member_id
                    //"NAME"  =>  $member_id
            ];
            $envelope = json_encode($array);
            $this->load->library("paramount");
            $data = $this->paramount->getdata($envelope, 'https://software.healthindiatpa.com/HiWebApi/Tata/EcardRequest', array(
                "Content-Type: application/json",
                "cache-control: no-cache", "health_india:true"
            ));
            $data = str_replace("&", "&amp;", $data);

            $xml = simplexml_load_string($data);


            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            try {
                $result = $array["TATAMappingECard"]["EcardLink"];
            }

//catch exception
            catch (Exception $e) {
                return "";
            }
                    $return_array["tpa_id"] = 4;
                    $return_array["response"] = $result;
                    echo json_encode($return_array);
            return $result;
        }
         else if ($tpa_id == 5) {
             
            // $policy_no ,$member_name
              
            // echo "here";exit;
             $envelope = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:tem="http://tempuri.org/">
   <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing"><wsa:Action>http://tempuri.org/IBCService/GetEnrollmentDetailsAndEcard</wsa:Action></soap:Header>
   <soap:Body>
      <tem:GetEnrollmentDetailsAndEcard>
         <tem:UserName>TataMotors</tem:UserName>
         <tem:Password>fhgn179ta</tem:Password>
         <tem:PolicyNumber>'.$policy_no.'</tem:PolicyNumber>
         <tem:EmployeeID>'.$member_name.'</tem:EmployeeID>
      </tem:GetEnrollmentDetailsAndEcard>
   </soap:Body>
</soap:Envelope>';
//echo $envelope;exit;

        try {
            $this->load->library("soaprequest");
            // return $this->soaprequest->getdata($envelope);
            $data1 = $this->soaprequest->getdata($envelope, 'https://m.fhpl.net/Bunnyconnect/BCService.svc', array(
                "content-type: application/soap+xml;charset=UTF-8;",
                "fhpl: true"
            ));
           // print_pre($data1);exit;
             $envelope = json_decode($data1['sBody']['GetEnrollmentDetailsAndEcardResponse']['GetEnrollmentDetailsAndEcardResult'], true);
           $ecard_link = $envelope[0]["EcardLink"];
           $return_array["tpa_id"] = 5;
                    $return_array["response"] = $ecard_link;
                    echo json_encode($return_array);
            //return $result;
           // return $ecard_link;
        }

//catch exception
        catch (Exception $e) {
            
        }
             
         }
	
			
           /*  extract($this->input->post(null,true));
        $envelope = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>

<EcardRequest xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.datacontract.org/2004/07/BrokerIntegration.DTO">
  <EmployeeCode>117294</EmployeeCode>
  <MemberId>5024110257</MemberId>
  <Password>TM3FA5g</Password>
  <PolicyNo>500500/28/18/P1/01252348</PolicyNo>
  <UserName>TataMotors</UserName>
</EcardRequest>

    </soap:Body>
</soap:Envelope>';
        print_r(json_encode($this->common_m->ecard_link($envelope))); */
    }
	
	
         function get_network_hospitals() {
            extract($this->input->post(null,true));
        $envelope = '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
    <Body>
        <getNetworkHospitals xmlns="http://www.mediassistindia.net/">
            <UserName>TataMotors</UserName>
            <Password>TM3FA5g</Password>
            <startIndex>0</startIndex>
            <endIndex>1000</endIndex>
        </getNetworkHospitals>
    </Body>
</Envelope>';

        $network_hospitals = $this->common_m->ecard_link($envelope)['soapBody']['getNetworkHospitalsResponse']['getNetworkHospitalsResult']['ProviderData']['Provider'];
      
        for($i = 0; $i<count($network_hospitals); $i++){
            $data[] = [
                'HOSIDNO1' => $network_hospitals[$i]['HOSIDNO1'],
                 'PARTNER_ID' =>$network_hospitals[$i]['PARTNER_ID'],
                 'ZONE_NAME' =>$network_hospitals[$i]['ZONE_NAME'],
                 'HOSPITAL_NAME' =>$network_hospitals[$i]['HOSPITAL_NAME'],
                 'ADDRESS1' =>$network_hospitals[$i]['ADDRESS1'],
                 'ADDRESS2' =>$network_hospitals[$i]['ADDRESS2'],
                 'CITY_NAME' =>$network_hospitals[$i]['CITY_NAME'],
                 'STATE_NAME' =>$network_hospitals[$i]['STATE_NAME'],
                 'PIN_CODE' =>$network_hospitals[$i]['PIN_CODE'],
                 'LANDMARK_1' =>$network_hospitals[$i]['LANDMARK_1'],
                 'LANDMARK_2' =>$network_hospitals[$i]['LANDMARK_2'],
                 'PHONE_NO' => $network_hospitals[$i]['PHONE_NO'],
                  'EMAIL' =>$network_hospitals[$i]['EMAIL'],
                  'LEVEL_OF_CARE' =>$network_hospitals[$i]['LEVEL_OF_CARE'],
                  'ISHOSPITALACTIVE' =>$network_hospitals[$i]['ISHOSPITALACTIVE'],
                  'Insurance_Company' =>$network_hospitals[$i]['Insurance_Company'],
                  'HOSP_CREATED_ON' =>$network_hospitals[$i]['HOSP_CREATED_ON'],
                  'HOSP_MODIFIED_ON ' =>$network_hospitals[$i]['HOSP_MODIFIED_ON'],
    ];   
        }
        $this->db->insert_batch('network_hospitals', $data); echo "inserted";die();
    }
 function check_enrollment(){
        print_r(json_encode($this->common_m->check_enrollment()));
    }
    
    //send sms
     function network_hospital_sms(){
        print_r(json_encode($this->common_m->network_hospital_sms()));
    }
 function get_smallest_date(){
        print_r(json_encode($this->common_m->get_smallest_date()));
    }
 function set_network_hospital_mails(){
        print_r(json_encode($this->common_m->set_network_hospital_mails()));
    }
    function get_family_details_from_policy_number(){
 print_r(json_encode($this->common_m->get_family_details_from_policy_number()));
}
    
}