<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class API_m extends CI_Model {


    function __construct() {
        parent::__construct();

    }

    function get_date_diff($return, $date1, $date2 = '')
    {
        $datetime1 = new DateTime($date1);
        $datetime2 = new DateTime($date2);
        $difference = $datetime1->diff($datetime2);
        $diff = false;
        switch (strtolower($return))
        {
            case 'year':
            $diff = $difference->y;
            break;
            case 'month':
            $diff = ($difference->y * 12) + $difference->m;
            break;
            case 'day':
            $diff = $difference->days;
            break;
        }
        if ($datetime1 > $datetime2)
        {
            $diff = $diff * (-1);
        }

        return $diff;
    }

    function get_hospitals_by_name() {
        extract($this->input->post(null, true));
        $data = $this->db
        ->select('*')
        ->from('network_hospitals')
        ->where('HOSPITAL_NAME',$hospital_name)
        ->group_by('HOSPITAL_NAME')
        ->get()
        ->result_array();
        return $data;
    }

    function get_policy_type() {
        return $this->db->get("master_policy_type")->result_array();
    }
    
    function get_ecard() 
    {
        extract($this->input->post());

        // $this->load->model('common_m');

        if($ecard){
            $data = $this->db
            ->select('*')
            ->from('employee_policy_member,family_relation,employee_details')
            ->where('employee_policy_member.family_relation_id = family_relation.family_relation_id')
            ->where('family_relation.emp_id = employee_details.emp_id')
            ->where('employee_policy_member.tpa_member_id',$member_id)
            ->get()
            ->row_array();

        }
        else{
            $data = $this->db
            ->select('ed.emp_code')
            ->from('employee_details ed')
            ->where('ed.emp_id', base64_decode($emp_id))
            ->get()
            ->row_array();

        }

        if ($tpa_id == 2) {
            $array = [
                "USERNAME" => "TATA-MOTORS",
                "PASSWORD" => "ADMIN@123",
                "POLICY_NO" => $policy_no,
                "EMPLOYEE_NO"    => $data["emp_code"],
                "NAME"  =>  $member_name
             //"NAME"  =>  $member_id
            ];

            $envelope = json_encode($array);
            $result = $this->get_paramount_member_url($envelope);
            if ($result) {
                if (strpos($result, 'Status') !== false) {
                    $return_array["tpa_id"] = 0;
                    $return_array["response"] = '';
                    echo json_encode($return_array);
                    exit;
                } else {
                    $return_array["tpa_id"] = 2;
                    $return_array["response"] = $result;
                    echo json_encode($return_array);
                    exit;
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

            $z = $this->ecard_link($envelope);
            if (is_array($z)) {

                $z['tpa_id'] = 1;
                $z["response"] = '';

                echo(json_encode($z));
                exit;
            } else {
                $return_array["tpa_id"] = 0;
                $return_array["response"] = '';
                echo(json_encode($return_array));
                exit;
            }
        } else if ($tpa_id == 4) {

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
            exit;
            // return $result;
        }
        else if ($tpa_id == 5) {

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
            exit;
            //return $result;
           // return $ecard_link;
        }

//catch exception
        catch (Exception $e) {

        }

    }

}

public function ecard_link($envelope) {
    extract($this->input->post(null, true));
    $this->load->library("soaprequest");
        // return $this->soaprequest->getdata($envelope);

    $data = $this->soaprequest->getdata($envelope, 'https://integration.medibuddy.in/TataMotorsAPI/soap11', array(
        "Content-Type: text/xml",
        "SOAPAction: EcardRequest",
        "cache-control: no-cache"
    ));
    return $data;
}

function get_paramount_member_url($envelope){
    $this->load->library("paramount");
    $data = $this->paramount->getdata($envelope,'https://webintegrations.paramounttpa.com/TataMotorsWebAPI/Service1.svc/Get_Member_Ecards',array(
        "Content-Type: application/json",
        "cache-control: no-cache" 
    ));
    return  (($data["Get_Member_EcardsResult"]));
}

function get_policy_detail($emp_id) {
    $subQuery1 = $this->db
    ->select('epd.policy_detail_id, 
        epd.policy_type_id, epd.policy_sub_type_id, epd.insurer_id, epd.broker_id, 
        epm.policy_detail_id, epm.family_relation_id, epm.family_id, epd.policy_no, 
        mps.policy_type_id, mpst.policy_sub_type_id, mpst.policy_sub_type_name, mpst.policy_sub_type_image_path, 
        mf.family_relation_id, mf.family_id, mf.emp_id, ic.insurer_id, ic.ins_co_name, ic.insurer_companies_img_path,epm.policy_mem_sum_insured, epm.policy_mem_sum_premium, "0" AS fr_id, "Self" AS fr_name')
    ->from('employee_details AS ed,
        family_relation AS mf,
        employee_policy_member AS epm, 
        employee_policy_detail AS epd, 
        master_policy_sub_type AS mpst, 
        master_policy_type AS mps, 
        master_insurance_companies AS ic')
    ->where('ed.emp_id=mf.emp_id')
    ->where('mf.family_relation_id=epm.family_relation_id')
    ->where('epm.policy_detail_id=epd.policy_detail_id')
    ->where('epd.policy_sub_type_id=mpst.policy_sub_type_id')
    ->where('mpst.policy_type_id=mps.policy_type_id')
    ->where('epd.insurer_id=ic.insurer_id')
    ->where('mf.family_id', 0)
    ->where('epm.status!=', 'Inactive')
    ->where('mf.emp_id', $emp_id)
    ->get_compiled_select();
    $op = $this->db->select('epd.policy_detail_id,epd.policy_type_id,epd.policy_sub_type_id,
        epd.insurer_id,epd.broker_id, epm.policy_detail_id,
        epm.family_relation_id, epm.family_id, epd.policy_no,
        mps.policy_type_id, mpst.policy_sub_type_id,
        mpst.policy_sub_type_name, mpst.policy_sub_type_image_path, fr.family_relation_id, 
        fr.family_id, fr.emp_id, ic.insurer_id, 
        ic.ins_co_name, ic.insurer_companies_img_path,epm.policy_mem_sum_insured, epm.policy_mem_sum_premium, mfr.
        fr_id, mfr.fr_name')
    ->from('employee_policy_member` AS `epm`, `employee_policy_detail` AS `epd`, `master_policy_type` AS `mps`, `master_policy_sub_type` AS `mpst`, `master_insurance_companies` AS `ic`, `family_relation` AS `fr`, `master_family_relation` AS `mfr`, `employee_family_details` AS `efd`')
    ->where('epd`.`policy_detail_id`=`epm`.`policy_detail_id')
    ->where('mps`.`policy_type_id`=`epd`.`policy_type_id')
    ->where('mpst`.`policy_sub_type_id`=`epd`.`policy_sub_type_id')
    ->where('ic`.`insurer_id`=`epd`.`insurer_id')
    ->where('fr`.`family_relation_id`=`epm`.`family_relation_id')
    ->where('fr`.`family_id`=`efd`.`family_id')
    ->where('efd`.`fr_id`=`mfr`.`fr_id')
    ->where('epm.status!=', 'Inactive')
    ->where('fr`.`emp_id`', $emp_id)
    ->get_compiled_select();
    $response = $this->db->query($subQuery1 . ' UNION ALL ' . $op)->result_array();

    $claim_amount_das = $this->get_claim_re_amount_forDashboard($emp_id);
        // print_r($claim_amount_das);
        // // echo $response[0]['policy_mem_sum_insured'];
        // print_r($response);
        // // print_r($claim_amount_das['total_claim_amount']);
        // // print_r($response);

        // // echo $est = $response[0]['policy_mem_sum_insured']-$claim_amount_das[0]['td'];

        // exit;

    $policy_details = [];
        // print_r($response);

    for ($i = 0; $i < count($response); $i++) {

        $policy_sum_in = 0;

        if($claim_amount_das['policy_sub_type_id'] == $response[$i]['policy_sub_type_id']){

          if($claim_amount_das['policy_detail_id'] == $response[$i]['policy_detail_id']){

             @$policy_sum_in = @$response[$i]['policy_mem_sum_insured'] - $claim_amount_das['total_claim_amount'];


         }

     }

     if (!isset($policy_details[$response[$i]['policy_detail_id']])) {
        $policy_details[$response[$i]['policy_detail_id']]['policy_id'] = $response[$i]['policy_detail_id'];
        $policy_details[$response[$i]['policy_detail_id']]['policy_type_id'] = $response[$i]['policy_type_id'];
        $policy_details[$response[$i]['policy_detail_id']]['ins_co_name'] = $response[$i]['ins_co_name'];
        $policy_details[$response[$i]['policy_detail_id']]['policy_mem_sum_insured'] = $response[$i]['policy_mem_sum_insured'];
        $policy_details[$response[$i]['policy_detail_id']]['cover_balance'] = !empty($policy_sum_in) ? ''.$policy_sum_in.'':$response[$i]['policy_mem_sum_insured'];
        $policy_details[$response[$i]['policy_detail_id']]['policy_mem_sum_premium'] = $response[$i]['policy_mem_sum_premium'];
        $policy_details[$response[$i]['policy_detail_id']]['policy_sub_type_name'] = $response[$i]['policy_sub_type_name'];
        $policy_details[$response[$i]['policy_detail_id']]['policy_sub_type_image_path'] = base_url($response[$i]['policy_sub_type_image_path']);
        $policy_details[$response[$i]['policy_detail_id']]['insurer_companies_img_path'] = base_url($response[$i]['insurer_companies_img_path']);
        $policy_details[$response[$i]['policy_detail_id']]['policy_sub_type_id'] = $response[$i]['policy_sub_type_id'];
        $policy_details[$response[$i]['policy_detail_id']]['member_count'] = 1;
        $policy_details[$response[$i]['policy_detail_id']]['members'][] = $response[$i]['fr_name'];
    } else {
        $policy_details[$response[$i]['policy_detail_id']]['members'][] = $response[$i]['fr_name'];
        $policy_details[$response[$i]['policy_detail_id']]['member_count'] ++;
    }
}
$policy_details = array_values($policy_details);
for ($i = 0; $i < count($policy_details); $i++) {
    $policy_details[$i]['members'] = implode(', ', array_unique($policy_details[$i]['members']));
}
        //print_pre($policy_details);
return $policy_details;
}

function get_claim_re_amount_forDashboard($emp_id) {

    $res = $this->db->select('sum(total_claim_amount) as td,epd.policy_detail_id,ecr.policy_no, mpst.policy_sub_type_id, mpt.policy_type_id')
    ->from('employee_details as ed, employee_claim_reimb as ecr,employee_policy_member as epm,employee_policy_detail as epd,master_policy_sub_type as mpst,family_relation as fr, master_policy_type As mpt')
    ->where('ecr.policy_member_id = epm.policy_member_id')
    ->where('epm.family_relation_id = fr.family_relation_id')
    ->where('epm.policy_detail_id = epd.policy_detail_id')
    ->where('epd.policy_sub_type_id = mpst.policy_sub_type_id')
    ->where('epd.policy_type_id = mpt.policy_type_id')
    ->where('ed.emp_id = fr.emp_id')
    ->where('fr.emp_id', $emp_id)
    ->where('fr.family_id',0 )

    ->get_compiled_select();
    $res1 = $this->db->select('sum(total_claim_amount) as td ,epd.policy_detail_id,ecr.policy_no, mpst.policy_sub_type_id, mpt.policy_type_id')
    ->from('employee_claim_reimb as ecr,employee_family_details as efd, employee_policy_member as epm,employee_policy_detail as epd,master_policy_sub_type as mpst,family_relation as fr, master_policy_type As mpt')
    ->where('ecr.policy_member_id = epm.policy_member_id')
    ->where('epm.family_relation_id = fr.family_relation_id')
    ->where('epm.policy_detail_id = epd.policy_detail_id')
    ->where('epd.policy_sub_type_id = mpst.policy_sub_type_id')
    ->where('epd.policy_type_id = mpt.policy_type_id')
    ->where('fr.emp_id', $emp_id)
    ->where('fr`.`family_id`=`efd`.`family_id')
    ->get_compiled_select();
    $response = $this->db->query($res . ' UNION ALL ' . $res1)->result_array();
    $sum = 0;        
    for($i = 0; $i < count($response); $i++){
     if($response[$i]["td"]){
         $sum += $response[$i]["td"];
         $response['policy_detail_id'] = $response[$i]['policy_detail_id'];
         $response['policy_sub_type_id'] = $response[$i]['policy_sub_type_id'];
     }
 }

 $response["total_claim_amount"] = $sum;
 return $response;
}

public function get_emp_data_flexi($emp_id)
{
  $check_data = $this->db->
  select('mpst.policy_sub_type_name,mpst.policy_sub_type_id,epm.policy_mem_sum_insured,epd.policy_no')
  ->from('master_policy_sub_type as mpst, employee_policy_detail as epd, employee_policy_member as epm,
    family_relation as fr')
  ->where('mpst.policy_sub_type_id = epd.policy_sub_type_id')
  ->where('epm.policy_detail_id = epd.policy_detail_id')
  ->where('epm.family_relation_id = fr.family_relation_id')
  ->where('fr.family_id',0)
  ->where('fr.emp_id',$emp_id)
  ->get()
  ->result_array();
  return $check_data;
}

public function all_flex_data($emp_id)
{
   $benifit_data = $this->db
   ->select('*')
   ->from('master_flexi_benefit mfb')
   ->join('employee_flexi_benefit_transaction ed','mfb.master_flexi_benefit_id = ed.employee_flexi_benifit_id','left')
   ->where('ed.emp_id', base64_decode($emp_id))
   ->get()
   ->result_array();
                          // echo $this->db->last_query();
   return  $benifit_data;
}

public function all_confirmed_flex_data()
{
    extract($this->input->post(NULL,true));

    $benifit_data = $this->db
    ->select('*')
    ->from('master_flexi_benefit mfb')
    ->join('employee_flexi_benefit_transaction ed','mfb.master_flexi_benefit_id = ed.employee_flexi_benifit_id','left')
    ->where('ed.emp_id', base64_decode($emp_id))
    ->where_in('ed.employee_flexi_benifit_id',array("3","4","5"))
    ->where('ed.confirmed_flag', 'Y')
    ->get()
    ->result_array();
    $nominee_data = $this->db
    ->select('*')
    ->from('member_policy_nominee AS mpn')
    ->where('mpn.emp_id', base64_decode($emp_id)) 
    ->where('mpn.confirmed_flag', 'Y') 
    ->get()
    ->result_array();                      
    $subQuery1 = $this->db
    ->select('epm.confirmed_flag,epm.policy_member_id,epd.policy_sub_type_id,ed.doj,epd.policy_detail_id, 
        epd.broker_id, epm.policy_detail_id, 
        epm.family_relation_id,epm.status,epd.TPA_id, 
        epd.policy_no, mf.family_relation_id,  
        mf.family_id, "0" AS fr_id, "Self" AS fr_name,
        ed.emp_firstname,ed.emp_lastname, ed.bdate,  epm.member_id, epm.tpa_member_id,epm.tpa_member_name,epm.policy_mem_sum_insured, epm.policy_mem_sum_premium, epd.start_date,epd.status,epm.policy_doc_path,epm.child_condition,mc.enrollment_start_date, mc.enrollment_end_date,epm.client_id')
    ->from('employee_details AS ed, family_relation AS mf, employee_policy_member AS epm, employee_policy_detail AS epd,master_company as mc')
    ->where('ed.emp_id = mf.emp_id')
    ->where('mf.family_relation_id = epm.family_relation_id')
    ->where('epm.policy_detail_id = epd.policy_detail_id')
    ->where('epd.company_id = mc.company_id')
    ->where('epm.status!=', 'Inactive')
    ->where('mf.family_id', 0)
    ->where('mf.emp_id ', base64_decode($emp_id))
    ->where('epd.policy_detail_id', $policy_no)
    ->get()->result_array();
    if (!empty($benifit_data)) 
    {
        if (!empty($nominee_data)) 
        {
            $data = array(
              'benifit_all_data' => $benifit_data,
              'nominee_data' => $nominee_data,
              'subQuery1' => $subQuery1[0]['confirmed_flag']
          );
        }
        else
        {
           $data = array(
              'benifit_all_data' => $benifit_data,
              'nominee_data' => null,
              'subQuery1' => $subQuery1[0]['confirmed_flag']
          );
       }

   }else{
      $data = array(
        'benifit_all_data' => null,
        'nominee_data' => null,
        'subQuery1' => $subQuery1[0]['confirmed_flag']
    );
  } 
  return $data;
} 

function list_view($emp_id) {
    $subQuery1 = $this->db
    ->select('epd.policy_detail_id, 
        epd.policy_type_id, epd.policy_sub_type_id, epd.insurer_id, epd.broker_id, 
        epm.policy_detail_id, epm.family_relation_id, epm.family_id, epd.policy_no, 
        mps.policy_type_id, mpst.policy_sub_type_id, mpst.policy_sub_type_name,mpst.policy_sub_type_image_path, 
        mf.family_relation_id, mf.family_id, mf.emp_id, ic.insurer_id, ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, "0" AS fr_id, "Self" AS fr_name')
    ->from('employee_details AS ed,
        family_relation AS mf,
        employee_policy_member AS epm, 
        employee_policy_detail AS epd, 
        master_policy_sub_type AS mpst, 
        master_policy_type AS mps, 
        master_insurance_companies AS ic')
    ->where('ed.emp_id=mf.emp_id')
    ->where('mf.family_relation_id=epm.family_relation_id')
    ->where('epm.policy_detail_id=epd.policy_detail_id')
    ->where('epd.policy_sub_type_id=mpst.policy_sub_type_id')
    ->where('mpst.policy_type_id=mps.policy_type_id')
    ->where('epd.insurer_id=ic.insurer_id')
    ->where('epm.status!=', 'Inactive')
    ->where('mf.family_id', 0)
    ->where('mf.emp_id', $emp_id)
    ->get_compiled_select();
    $op = $this->db->select('epd.policy_detail_id,epd.policy_type_id,epd.policy_sub_type_id,
        epd.insurer_id,epd.broker_id, epm.policy_detail_id,
        epm.family_relation_id, epm.family_id, epd.policy_no,
        mps.policy_type_id, mpst.policy_sub_type_id,
        mpst.policy_sub_type_name,mpst.policy_sub_type_image_path, fr.family_relation_id, 
        fr.family_id, fr.emp_id, ic.insurer_id, 
        ic.ins_co_name, ic.insurer_companies_img_path,epd.sum_insured, mfr.
        fr_id, mfr.fr_name')
    ->from('employee_policy_member` AS `epm`, `employee_policy_detail` AS `epd`, `master_policy_type` AS `mps`, `master_policy_sub_type` AS `mpst`, `master_insurance_companies` AS `ic`, `family_relation` AS `fr`, `master_family_relation` AS `mfr`, `employee_family_details` AS `efd`')
    ->where('epd`.`policy_detail_id`=`epm`.`policy_detail_id')
    ->where('mps`.`policy_type_id`=`epd`.`policy_type_id')
    ->where('mpst`.`policy_sub_type_id`=`epd`.`policy_sub_type_id')
    ->where('ic`.`insurer_id`=`epd`.`insurer_id')
    ->where('fr`.`family_relation_id`=`epm`.`family_relation_id')
    ->where('fr`.`family_id`=`efd`.`family_id')
    ->where('epm.status!=', 'Inactive')
    ->where('efd`.`fr_id`=`mfr`.`fr_id')
    ->where('fr`.`emp_id`', $emp_id)
    ->get_compiled_select();
    $response = $this->db->query($subQuery1 . ' UNION ALL ' . $op)->result_array();
    $policy_details = [];
    for ($i = 0; $i < count($response); $i++) {
        if (!isset($policy_details[$response[$i]['policy_detail_id']])) {
            $policy_details[$response[$i]['policy_detail_id']]['policy_id'] = $response[$i]['policy_detail_id'];
            $policy_details[$response[$i]['policy_detail_id']]['policy_type_id'] = $response[$i]['policy_type_id'];
            $policy_details[$response[$i]['policy_detail_id']]['policy_no'] = $response[$i]['policy_no'];
            $policy_details[$response[$i]['policy_detail_id']]['ins_co_name'] = $response[$i]['ins_co_name'];
            $policy_details[$response[$i]['policy_detail_id']]['sum_insured'] = $response[$i]['sum_insured'];
            $policy_details[$response[$i]['policy_detail_id']]['policy_sub_type_name'] = $response[$i]['policy_sub_type_name'];
            $policy_details[$response[$i]['policy_detail_id']]['policy_sub_type_image_path'] = base_url($response[$i]['policy_sub_type_image_path']);
            $policy_details[$response[$i]['policy_detail_id']]['insurer_companies_img_path'] =base_url( $response[$i]['insurer_companies_img_path']);
            $policy_details[$response[$i]['policy_detail_id']]['policy_sub_type_id'] = $response[$i]['policy_sub_type_id'];
            $policy_details[$response[$i]['policy_detail_id']]['member_count'] = 1;
            $policy_details[$response[$i]['policy_detail_id']]['members'][] = $response[$i]['fr_name'];
        } else {
            $policy_details[$response[$i]['policy_detail_id']]['members'][] = $response[$i]['fr_name'];
            $policy_details[$response[$i]['policy_detail_id']]['member_count'] ++;
        }
    }
    $policy_details = array_values($policy_details);
    for ($i = 0; $i < count($policy_details); $i++) {
        $policy_details[$i]['members'] = implode(', ', array_unique($policy_details[$i]['members']));
    }
    return $policy_details;
}

function get_policy_view($id,$emp_id) {
    $subQuery1 = $this->db
    ->select('epd.policy_sub_type_id,ed.doj,epd.policy_detail_id, 
        epd.broker_id, epm.policy_detail_id, 
        epm.family_relation_id,epm.status,epd.TPA_id, 
        epd.policy_no, mf.family_relation_id,  
        mf.family_id, "0" AS fr_id, "Self" AS fr_name,
        ed.emp_firstname, ed.emp_lastname, ed.bdate,  epm.member_id, epm.tpa_member_id,epm.tpa_member_name,epm.policy_mem_sum_insured, epm.policy_mem_sum_premium, epd.start_date,epd.status,epm.policy_doc_path,epm.child_condition,mc.enrollment_start_date, mc.enrollment_end_date,epm.client_id,ed.gender')
    ->from('employee_details AS ed, family_relation AS mf, employee_policy_member AS epm, employee_policy_detail AS epd,master_company as mc')
    ->where('ed.emp_id = mf.emp_id')
    ->where('mf.family_relation_id = epm.family_relation_id')
    ->where('epm.policy_detail_id = epd.policy_detail_id')
    ->where('epd.company_id = mc.company_id')
    ->where('epm.status!=', 'Inactive')
    ->where('mf.family_id', 0)
    ->where('mf.emp_id ', $emp_id)
    ->where('epd.policy_no', $id)
    ->get_compiled_select();
    $op = $this->db->select('epd.policy_sub_type_id,ed.doj,epd.policy_detail_id,  epd.broker_id,
        epm.policy_detail_id, epm.family_relation_id,epm.status, epd.TPA_id,
        epd.policy_no,  
        fr.family_relation_id,  
        fr.family_id, mfr. fr_id, mfr.fr_name, efd.family_firstname,efd.family_lastname, 
        efd.family_dob, epm.member_id, epm.tpa_member_id,epm.tpa_member_name,epm.policy_mem_sum_insured, epm.policy_mem_sum_premium, epd.start_date,epd.status,epm.policy_doc_path,epm.child_condition,mc.enrollment_start_date, mc.enrollment_end_date,epm.client_id,efd.family_gender')
    ->from('employee_details as ed,employee_family_details AS efd, family_relation AS fr,employee_policy_member AS epm, employee_policy_detail AS epd, 
        master_family_relation AS mfr,master_company as mc')
    ->where('epd.policy_detail_id = epm.policy_detail_id')
    ->where('fr.family_relation_id = epm.family_relation_id')
    ->where('fr.family_id = efd.family_id')
    ->where('epd.company_id = mc.company_id')
    ->where('epm.status!=', 'Inactive')
    ->where('efd.fr_id= mfr.fr_id')
    ->where('fr.emp_id', $emp_id)
    ->where('fr.emp_id = ed.emp_id')
    ->where('epd.policy_no', $id)
    ->get_compiled_select();
    $response = $this->db->query($subQuery1 . ' UNION ALL ' . $op)->result_array();
        //echo $response;

    return $response;
}


public function get_all_policy_no($emp_id) {

    $data = $this->db
    ->select('*')
    ->from('employee_policy_member,employee_policy_detail,family_relation,master_policy_sub_type')
    ->where('employee_policy_detail.policy_detail_id = employee_policy_member.policy_detail_id')
    ->where('family_relation.family_relation_id = employee_policy_member.family_relation_id')
    ->where('family_relation.emp_id', $emp_id)
    ->where('master_policy_sub_type.policy_sub_type_id = employee_policy_detail.policy_sub_type_id')
    ->group_by('employee_policy_detail.policy_no')
    ->get()
    ->result_array();


    return $data;
}

    //send sms
public function network_hospital_sms() {

    extract($this->input->post(null, true));
    $details['sms'] = [
        'template' => $this->db->get_where('sms_template', ['module_name' => 'NETWORK_HOSPITAL'])->row_array(),
        'send_details' => [
            'name' => $emp_name,
                //'last_name' => $this->session->emp_last_name,
            'adddress' => $address,
            'mobile_no' => $mobile_no
        ]
    ];
        // print_r($_POST);
    return save_queue($details);
}

function set_network_hospital_mails() {

    extract($this->input->post(null, true));
    $data["hospital_address"] = $hospital_address;
    $data["contact_no"] = $contact_no;

    $details['email'] = [
        'to' => $email_id,
        'subject' => 'Network Hospital',
        'message' => preg_replace('~>\s+<~', '><', $this->load->view('email_new', [
            'subview' => network_creation_template($data),
            'name' => "alsdkalsdjkaldjad",
            'mailer_image' => 'false'
        ], TRUE)),
                //'bcc' => CAR_EMAIL
    ];
    return save_queue($details);
}

public function get_family_membername_from_policy_no() {

    extract($this->input->post(null,true));
    $emp_id = base64_decode($emp_id);

    $subQuery1 = $this->db
    ->select('DISTINCT(ed.emp_id) AS id, ed.emp_firstname AS name, "Self" AS relationship,"0" AS fr_id, "0"  as family_id,epm.policy_member_id as emp_member_id')
    ->from('employee_policy_detail AS epd, employee_details AS ed, family_relation AS fr,employee_policy_member AS epm')
    ->where('ed.emp_id=fr.emp_id AND fr.family_id=0')
    ->where('fr.family_relation_id=epm.family_relation_id')
    ->where('epm.policy_detail_id=epd.policy_detail_id')
    ->where('epd.policy_no',$policy_no)
    ->where('fr.emp_id', $emp_id)

    ->get_compiled_select();
    $subQuery2 = $this->db
    ->select('efd.family_id AS familyid, efd.family_firstname AS name, mfr.fr_name AS relationship ,mfr.fr_id AS fr_id,efd.family_id as family_id,epm.policy_member_id as emp_member_id')
    ->from('employee_policy_detail AS epd,employee_policy_member AS epm ,employee_family_details AS efd, family_relation AS fr, master_family_relation AS mfr')
    ->where('efd.family_id=fr.family_id')
    ->where('fr.family_relation_id=epm.family_relation_id')
    ->where('epm.policy_detail_id=epd.policy_detail_id')
    ->where('efd.fr_id=mfr.fr_id')
    ->where('epd.policy_no', $policy_no)
    ->where('epm.status!=','Inactive')
    ->where('fr.emp_id', $emp_id)
    ->get_compiled_select();

    $op = $this->db->query($subQuery1.' UNION '.$subQuery2)->result_array();
    return $op;
}

public function employee_claim_intimate_insert() {
    extract($this->input->post(NULL, true));

    $initimate_no = 0;

    // $this->load->model('employee/Intimate_claims_m','em_claim');

    $data = $this->db
    ->select('epm.tpa_member_name,epm.tpa_member_id,epd.TPA_id')
    ->from('employee_policy_member as epm,employee_policy_detail as epd')
    ->where('epm.policy_member_id', $patient_name)
    ->where('epm.policy_detail_id = epd.policy_detail_id')
    ->get()
    ->row_array();
        // if($data["TPA_id"] == 1)
    if ($data["TPA_id"] == 1) {
        $res = json_encode([
            "USERNAME" => "TATA-MOTORS",
            "PASSWORD" => "ADMIN@123",
                "PHM" => "30402096", //get this from employee policy member
                "PATIENT_NAME" => "JAPAN PARIKH", //get tpa member from table
                "AILMENT" => $admitted_for,
                "CLAIM_AMOUNT" => $claim_Amount,
                "DATE_OF_ADMISSION" => date('d/m/Y', strtotime($planned_date)),
                "HOSPITAL_NO" => "10",
                "HOSPITAL_NAME" => $hospital_id,
                "NAME_OF_DOCTOR" => $doctor_name,
                "MOBILE_NO" => $mob_no,
                "EMAIL_ID" => $email,
                "CLAIM_TYPE" => "R",
                "HOSPITAL_STATE" => "MAHARASHTRA",
                "HOSPITAL_CITY" => "MUMBAI",
                "HOSPITAL_ADDRESS" => "SDJOPSOPDJSD"
            ]);
        $this->load->library("paramount");
        $data = $this->paramount->getdata($res, 'https://webintegrations.paramounttpa.com/TataMotorsWebAPI/Service1.svc/INTIMATE_CLAIM', array(
            "Content-Type: application/json",
            "cache-control: no-cache"
        ));
        $z = json_decode($data["INTIMATE_CLAIMResult"], true);
        if (!isset($z[0]["Status"])) {

            $initimate_no = $z[0]["Claim Intimation No"];
        }
    } else {
//get policy from policy no
        $envelope_request = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
        <IntimateClaimRequest xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.datacontract.org/2004/07/BrokerIntegration.DTO">
        <AilmentDescription>sdfgsdfg</AilmentDescription>
        <ContactNo>7777777777</ContactNo>
        <DateOfAdmission>2001-01-01T00:00:00</DateOfAdmission>
        <HospName>hosp name</HospName>
        <MemberId>5039545400</MemberId> 
        <Password>TM3FA5g</Password>
        <PolicyNo>431500/48/conneqt_Ex</PolicyNo>
        <UserName>TataMotors</UserName>
        </IntimateClaimRequest>
        </soap:Body>
        </soap:Envelope>';

        $initimate_no = '';
    }

    if ($initimate_no != 0) {

        $details['sms'] = [
            'template' => $this->db->get_where('sms_template', ['module_name' => 'INTIMATECLAIM'])->row_array(),
            'send_details' => [
                'name' => $patient_name,
                'REF' => $initimate_no,
                'mobile_no' => $mob_no
            ]
        ];
        save_queue($details);
    }


    if ($discharge_date) {
        $discharge_date1 = date('Y-m-d', strtotime($discharge_date));
    } else {
        $discharge_date1 = date('Y-m-d', strtotime($planned_date));
    }

    if ($data_override == 'NO' || $data_override == '') {

        $details = $this->employee_claim_intimate_details($policy_no, $patient_name, $planned_date, $discharge_date);
        if ($details == 'same_date') {

            $data_result['success'] = 'You Have Already Submiited Claim On a Same Date Range';
            $data_result['status'] = '2'; 
        } else if ($details == 'in_month') {
            $data_result['success'] = 'You Have Already Submiited Do U Want To New Claim';
            $data_result['status'] = '3'; 
        } else {
            $data = array(
                'member_id' => '',
                'claim_id' => '',
                'policy_no' => $policy_no,
                'claim_patient_name' => $patient_name,
                'hospital_name' => $hospital_id,
                'claim_type' => 'reimbursement',
                'doctor_name' => $doctor_name,
                'planned_date' => date('Y-m-d', strtotime($planned_date)),
                'mob_no' => $mob_no,
                'discharge_date' => $discharge_date1,
                'email' => $email,
                'reason' => $admitted_for,
                'fil_no' => '',
                'claim_Amount' => $claim_Amount,
                'created_date' => date('Y-m-d H:i:s'),
                'tpa_intimate_no' => ''
            );

            $result = $this->db->insert('employee_claim_intimate', $data);
            if ($result) {
                $data_result['success'] = 'Your Intimate Claims Submitted Successfully';
                $data_result['status'] = '1'; 
            } else {
                $data_result['success'] = 'Some Error Has Been Occured';
            }
        }
    } else {
        $data = array(
            'member_id' => '',
            'claim_id' => '',
            'policy_no' => $policy_no,
            'claim_patient_name' => $patient_name,
            'hospital_name' => $hospital_id,
            'claim_type' => 'reimbursement',
            'doctor_name' => $doctor_name,
            'planned_date' => date('Y-m-d', strtotime($planned_date)),
            'mob_no' => $mob_no,
            'discharge_date' => $discharge_date1,
            'email' => $email,
            'reason' => $admitted_for,
            'fil_no' => '',
            'claim_Amount' => $claim_Amount,
            'created_date' => date('Y-m-d H:i:s'),
            'tpa_intimate_no' => $initimate_no
        );
        $result = $this->db->insert('employee_claim_intimate', $data);
        if ($result) {
            $data_result['success'] = 'Your Intimate Claims Submitted Successfully';
            $data_result['status'] = '1'; 
        } else {
            $data_result['success'] = 'Some Error Has Been Occured';
        }
    }
    return $data_result;
}

public function employee_claim_intimate_details($policy_no = "", $patient_name = "", $planned_date = "", $discharge_date = "") {
    $month_planned = date("m", strtotime($planned_date));
    $month_discharge = date("m", strtotime($discharge_date));
        $first_day_this_month = date('Y-m-01'); // hard-coded '01' for first day
        $last_day_this_month = date('Y-m-t');

        $query2 = $this->db->select('*')
        ->from('employee_claim_intimate')
        ->where('policy_no', $policy_no)
        ->where('claim_patient_name', $patient_name)
        ->where('claim_type', 'reimbursement')
        ->where('planned_date <=', date('Y-m-d', strtotime($planned_date)))
        ->where('discharge_date >=', date('Y-m-d', strtotime($planned_date)))
        ->get()
        ->result_array();
        if ($query2) {
            return 'same_date';
        } else {
            $query1 = $this->db->select('*')
            ->from('employee_claim_intimate')
            ->where('policy_no', $policy_no)
            ->where('claim_patient_name', $patient_name)
            ->where('claim_type', 'reimbursement')
            ->where('planned_date <=', $first_day_this_month)
            ->where('discharge_date >=', $last_day_this_month)
            ->get()
            ->result_array();
            if ($query1) {
                return 'in_month';
            }
        }
    }

    public function employee_claim_cashless_insert()
    {
        extract($this->input->post(NULL,true));

     // $this->load->model('employee/Cashless_claims_m','em_cashclaim');

         /* $envelope_request = '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
        <Body>
            <GetIntimationNumber xmlns="http://www.mediassistindia.net/">
                <Username>TataMotors</Username>
                <Password>TM3FA5g</Password>
                <MAID>5024112640</MAID>
                <PolicyNo>500500/28/18/P1/01252348</PolicyNo>
                <TelphoneNumber>7022969826</TelphoneNumber>
                <DateOfAdmission>07-02-2019</DateOfAdmission>
                <HospitalName>Manipal</HospitalName>
                <AilmentDescription>Fever</AilmentDescription>
            </GetIntimationNumber>
        </Body>
    </Envelope>';
             $initimate_no = 0;
             $envolope = $this->common_m->ecard_link($envelope_request);
             if($envolope)
                $initimate_no = (int)(explode(":",$envolope['soapBody']['GetIntimationNumberResponse']['GetIntimationNumberResult'])[1]);
             else 
             $initimate_no; */
             if ($discharge_date) {
              $discharge_date1 = date('Y-m-d',strtotime($discharge_date));
          }
          else
          {
              $discharge_date1 = date('Y-m-d',strtotime($planned_date));
          }
          if($data_override == 'NO' || $data_override == '')
          {


              $details = $this->employee_claim_intimate_details($policy_no,$patient_name,$planned_date,$discharge_date1);

              if($details == 'same_date')
              {
                 $data_result['success'] = 'You Have Already Submiited Claim On a Same Date Range';   
                 $data_result['status'] = '2';   
             }
             else if($details == 'in_month')
             {
                 $data_result['success'] = 'You Have Already Submiited Do U Want To New Claim'; 
                 $data_result['status'] = '3'; 
             }
             else
             {
                 $data = array(
                    'member_id' => '',
                    'claim_id' => '',
                    'policy_no' => $policy_no,
                    'claim_patient_name' => $patient_name,
                    'hospital_name' => $hospital_name,
                    'claim_type' => 'cashless',
                    'doctor_name' => $doctor_name,
                    'planned_date' =>  date('Y-m-d',strtotime($planned_date)),
                    'mob_no' =>$mob_no,
                    'discharge_date' =>  $discharge_date1,
                    'email' => $email,
                    'reason' => $admitted_for,
                    'fil_no' => $file_no,
                    'claim_Amount' => '',
                    'created_date' => date('Y-m-d H:i:s'),
                    'tpa_intimate_no' => ''
                );
                 $result = $this->db->insert('employee_claim_intimate',$data);
                 if ($result) 
                 {
                    $data_result['success'] = 'Your Cashless Claims Submitted Successfully'; 
                    $data_result['status'] = '1'; 
                }
                else
                {
                    $data_result['success'] = 'Some Error Has Been Occured';
                }
            }
        }
        else
        {
            $data = array(
                'member_id' => '',
                'claim_id' => '',
                'policy_no' => $policy_no,
                'claim_patient_name' => $patient_name,
                'hospital_name' => $hospital_name,
                'claim_type' => 'cashless',
                'doctor_name' => $doctor_name,
                'planned_date' =>  date('Y-m-d',strtotime($planned_date)),
                'mob_no' =>$mob_no,
                'discharge_date' =>  $discharge_date1,
                'email' => $email,
                'reason' => $admitted_for,
                'fil_no' => $file_no,
                'claim_Amount' => '',
                'created_date' => date('Y-m-d H:i:s'),
                'tpa_intimate_no' => $initimate_no
            );
            $result = $this->db->insert('employee_claim_intimate',$data);
            if ($result) 
            {
                $data_result['success'] = 'Your Cashless Claims Submitted Successfully'; 
                $data_result['status'] = '1'; 
            }
            else
            {
                $data_result['success'] = 'Some Error Has Been Occured';
            }
        }
        return $data_result;
    }


    public function get_all_states() {
        $data = $this->db
        ->select('*')
        ->from('master_state')
        ->get()
        ->result_array();
        return $data;
    }

    public function get_city_from_states() {
        extract($this->input->post(null, true));
        $data = $this->db
        ->where('state_id', $state_names)
        ->get('master_city')
        ->result_array();
        return $data;
    }


    public function get_hospital_name() {
        extract($this->input->post(null, true));
        
        $where = [
        ];
        $this->db
        ->select('*')
        ->from('network_hospitals');
        if (!isset($all_hospitals)) {

            if (isset($state_names)) {
                $this->db->where('network_hospitals.STATE_NAME', strtoupper($state_names));
                //$where += ['network_hospitals.STATE_NAME' =>  strtoupper($state_name)];
                //$this->db->group_by('STATE_NAME');
            } else {
                $this->db->group_by('STATE_NAME');
            }
            if (isset($city_names)) {
                $this->db->where('network_hospitals.CITY_NAME', strtoupper($city_names));
                //$where += ['network_hospitals.CITY_NAME ' => strtoupper($city_names)];
            } elseif (isset($state_names) && $state_names != '') {
                $this->db->group_by('CITY_NAME');
            }
        }

        $this->db->where('network_hospitals.policy_no', $policy_no);
        $data = $this->db->get()
        ->result_array();
        // echo $this->db->last_query();exit;
        return $data;
    }

    public function claims_save()
    {
     extract($this->input->post(NULL,true));
     // $this->load->model('employee/Claims_m','em_subclaim');

     if (empty($claim_reimb_id)) 
     {
      if($data_override == 'NO' || $data_override == '')
      {

        $data_claim = $this->db->select('ecr.total_claim_amount')
        ->from('employee_claim_reimb as ecr')
        ->where('ecr.claim_patient_name',$patient_name)
        ->where('ecr.claim_hospitalization_date BETWEEN "' . date('Y-m-d', strtotime($hospitalization_date)) . '" and "' . date('Y-m-d', strtotime($discharge_date)) . '"')
        ->or_where('ecr.claim_discharge_date BETWEEN "' . date('Y-m-d', strtotime($hospitalization_date)) . '" and "' . date('Y-m-d', strtotime($discharge_date)) . '"')
        ->get()->result_array();
                                               //echo $this->db->last_query();
                                                  // print_r($data_claim);exit();
        if(empty($data_claim[0]['total_claim_amount'])){

            $chk_claim = $this->db->select('count(ecr.claim_reimb_id) as id,ecr.claim_reimb_id')
            ->from('employee_claim_reimb as ecr')
            ->where('ecr.claim_patient_name',$patient_name)
            ->where('ecr.claim_hospitalization_date BETWEEN "' . date('Y-m-d', strtotime($hospitalization_date)) . '" and "' . date('Y-m-d', strtotime($discharge_date)) . '"')
            ->or_where('ecr.claim_discharge_date BETWEEN "' . date('Y-m-d', strtotime($hospitalization_date)) . '" and "' . date('Y-m-d', strtotime($discharge_date)) . '"')
            ->get()->result_array()[0];
            $chk_in_hospitaliztion = $this->db->select('count(ecr.claim_reimb_id) as reim_id')
            ->from('employee_claim_reimb as ecr,employee_claim_reimb_hospitalization as ecrh,employee_reimb_bills as erb')
            ->where('ecr.claim_reimb_id = ecrh.claim_reimb_id')
            ->where('ecr.claim_reimb_id = erb.claim_reimb_id')
            ->where('ecr.claim_patient_name',$patient_name)
            ->where('ecr.claim_hospitalization_date BETWEEN "' . date('Y-m-d', strtotime($hospitalization_date)) . '" and "' . date('Y-m-d', strtotime($discharge_date)) . '"')
            ->or_where('ecr.claim_discharge_date BETWEEN "' . date('Y-m-d', strtotime($hospitalization_date)) . '" and "' . date('Y-m-d', strtotime($discharge_date)) . '"')
            ->get()->result_array()[0]; 
                                                        //  print_r($chk_in_hospitaliztion);
                                                        // print_r($chk_claim);exit();
            if($chk_claim['id'] > 0 ||  $chk_in_hospitaliztion['reim_id'] > 0){
             $this->db->delete('employee_claim_reimb',array('claim_reimb_id' => $chk_claim['claim_reimb_id']));
             $this->db->delete('employee_claim_reimb_hospitalization',array('claim_reimb_id' => $chk_claim['claim_reimb_id']));
             $this->db->delete('employee_reimb_bills',array('claim_reimb_id' => $chk_claim['claim_reimb_id']));
             $data = [
              'claim_patient_name' => $patient_name,
              'policy_member_id' => $patient_name,
              'policy_no' => $policy_numbers,
              'claim_relation' => $relationship_status,
              'claim_discharge_date' => date('Y-m-d',strtotime($discharge_date)),
              'claim_mob' =>$mobile_no,
              'claim_email' => $email_id,
              'claim_hospitalization_date' => date('Y-m-d',strtotime($hospitalization_date)),
              'created_at' => date('Y-m-d h:m')
          ];
          $query = $this->db->insert('employee_claim_reimb',$data);
          $id = $this->db->insert_id();
          $result = $this->db->get_where('employee_claim_reimb',array('claim_reimb_id'=>$id))->result_array()[0];

          if ($query) {
              $data_result['success'] = 'Your Cashless Claims Submitted Successfully'; 
              $data_result['status'] = '1'; 
              $data_result['claim_id'] = $result['claim_reimb_id'];
              $data_result['hospitalization_date'] = $hospitalization_date;
              $data_result['discharge_date'] = $discharge_date;
              $data_result['min_date'] = date('Y-m-d', strtotime('-30 day', strtotime($hospitalization_date)));
              $data_result['max_date'] = date('Y-m-d', strtotime($discharge_date. ' + 60 days'));
          }
          else{
              $data_result['success'] = 'Some Error Has Been Occured';
          }
      }else{
        $data = [
          'claim_patient_name' => $patient_name,
          'policy_member_id' => $patient_name,
          'policy_no' => $policy_numbers,
          'claim_relation' => $relationship_status,
          'claim_discharge_date' => date('Y-m-d',strtotime($discharge_date)),
          'claim_mob' =>$mobile_no,
          'claim_email' => $email_id,
          'claim_hospitalization_date' => date('Y-m-d',strtotime($hospitalization_date)),
          'created_at' => date('Y-m-d h:m')
      ];
      $query = $this->db->insert('employee_claim_reimb',$data);
      $id = $this->db->insert_id();
      $result = $this->db->get_where('employee_claim_reimb',array('claim_reimb_id'=>$id))->result_array()[0];

      if ($query) {
          $data_result['success'] = 'Your Cashless Claims Submitted Successfully'; 
          $data_result['status'] = '1'; 
          $data_result['claim_id'] = $result['claim_reimb_id'];
          $data_result['hospitalization_date'] = $hospitalization_date;
          $data_result['discharge_date'] = $discharge_date;

          $data_result['min_date'] = date('Y-m-d', strtotime('-30 day', strtotime($hospitalization_date)));
          $data_result['max_date'] = date('Y-m-d', strtotime($discharge_date. ' + 60 days'));

      }
      else{
          $data_result['success'] = 'Some Error Has Been Occured';
      }
  }                         

}else{

   $details = $this->employee_claim_details($policy_numbers,$patient_name,$hospitalization_date,$discharge_date);
   if($details == 'same_date')
   {

      $data_result['success'] = 'You Have Already Submiited Claim On a Same Date Range'; 
      $data_result['status'] = '2';   
  }
  else if($details == 'in_month')
  {
   $data_result['success'] = 'You Have Already Submiited Do U Want To New Claim';
   $data_result['status'] = '3';  
}
else
{

    $data = [
        'claim_patient_name' => $patient_name,
        'policy_member_id' => $patient_name,
        'policy_no' => $policy_numbers,
        'claim_relation' => $relationship_status,
        'claim_discharge_date' =>  date('Y-m-d',strtotime($discharge_date)),
        'claim_mob' =>$mobile_no,
        'claim_email' => $email_id,
        'claim_hospitalization_date' =>  date('Y-m-d',strtotime($hospitalization_date)),
        'created_at' => date('Y-m-d h:m:s')
    ];
    $result1 = $this->db->insert('employee_claim_reimb',$data);
    $id = $this->db->insert_id();
    $result = $this->db->get_where('employee_claim_reimb',array('claim_reimb_id'=>$id))->result_array()[0];
    
    if ($result) 
    {
      $data_result['success'] = 'Your Cashless Claims Submitted Successfully';
      $data_result['status'] = '1'; 
      $data_result['claim_id'] = $result['claim_reimb_id'];
      $data_result['hospitalization_date'] = $hospitalization_date;
      $data_result['discharge_date'] = $discharge_date; 

      $data_result['min_date'] = date('Y-m-d', strtotime('-30 day', strtotime($hospitalization_date)));
      $data_result['max_date'] = date('Y-m-d', strtotime($discharge_date. ' + 60 days'));

  }
  else
  {
      $data_result['success'] = 'Some Error Has Been Occured';
  }
}

}

}
else
{
    $data = [
        'claim_patient_name' => $patient_name,
        'policy_member_id' => $patient_name,
        'policy_no' => $policy_numbers,
        'claim_relation' => $relationship_status,
        'claim_discharge_date' => date('Y-m-d',strtotime($discharge_date)),
        'claim_mob' =>$mobile_no,
        'claim_email' => $email_id,
        'claim_hospitalization_date' => date('Y-m-d',strtotime($hospitalization_date)),
        'created_at' => date('Y-m-d h:m:s')
    ];
    $result1 = $this->db->insert('employee_claim_reimb',$data);
    $id = $this->db->insert_id();
    $result = $this->db->get_where('employee_claim_reimb',array('claim_reimb_id'=>$id))->result_array()[0];
    if ($result) 
    {
      $data_result['success'] = 'Your Cashless Claims Submitted Successfully';
      $data_result['status'] = '1'; 
      $data_result['claim_id'] = $result['claim_reimb_id'];
      $data_result['hospitalization_date'] = $hospitalization_date;
      $data_result['discharge_date'] = $discharge_date; 

      $data_result['min_date'] = date('Y-m-d', strtotime('-30 day', strtotime($hospitalization_date)));
      $data_result['max_date'] = date('Y-m-d', strtotime($discharge_date. ' + 60 days'));
  }
  else
  {
      $data_result['success'] = 'Some Error Has Been Occured';
  }
}

return $data_result;
}

}

public function employee_claim_details($policy_no="",$patient_name ="",$planned_date="",$discharge_date="")
{
    $month_planned = date("m",strtotime($planned_date));
    $month_discharge = date("m",strtotime($discharge_date));
          $first_day_this_month = date('Y-m-01'); // hard-coded '01' for first day
          $last_day_this_month  = date('Y-m-t');



          $query2 = $this->db->select('*')
          ->from('employee_claim_reimb')
          ->where('policy_no', $policy_no) 
          ->where('claim_patient_name', $patient_name) 
          ->where('claim_hospitalization_date <=', date('Y-m-d',strtotime($planned_date)))
          ->where('claim_discharge_date >=', date('Y-m-d',strtotime($planned_date)))
          ->get()
          ->result_array();
          if ($query2) {
            return 'same_date';
        }
        else
        {
           $query1 = $this->db->select('*')
           ->from('employee_claim_reimb')
           ->where('policy_no', $policy_no) 
           ->where('claim_patient_name', $patient_name) 
           ->where('claim_hospitalization_date <=', $first_day_this_month)
           ->where('claim_discharge_date >=', $last_day_this_month)
           ->get()
           ->result_array();
           if ($query1) 
           {
              return 'in_month';
          }
      }

  }

  public function save_claim_reimb_hospitalization()
  {
      extract($this->input->post(NULL,true));

      // $this->load->model('employee/Claims_m','em_subclaim');

      $bill_no = json_decode($bill_noArr);
      $bill_date = json_decode($bill_dateArr);
      $claim_amount = json_decode($claim_amountArr);
      $comment = json_decode($commentArr);
      $cost = json_decode($costArr);

      $result = array_unique($bill_no);

      if(count($bill_no) !== count($result)){

        $data_result['success'] = 'Please cannot enter double bill no';
        return $data_result;
    }

    $data_arr = [
        'claim_reimb_id' => $claim_reimb_id,
        'hospital_address' => $hospital_address,
        'claim_hospital_id' => $hospital_name,
        'claim_reimb_reason' => $reason,
        'claim_reimb_disease_name' => $diseases,
        'claim_reimb_state' => $state_names,
        'claim_reimb_city' => $cities,
        'claim_id' => '',
        'claim_reimb_exp' => ''
    ];

    $final_data = $this->db->insert('employee_claim_reimb_hospitalization',$data_arr);
    $id = $this->db->insert_id();
    if (!empty($id)) 
    {
        for($i = 0; $i < count($bill_no); ++$i) {
            $arr = [
                'reimb_bill_no' => $bill_no[$i],
                'reimb_bill_date' => $bill_date[$i],
                'reimb_claim_amount' => $claim_amount[$i],
                'reimb_comment' => $comment[$i],
                'reimb_cost_type' => $cost[$i],
                'claim_hospital_id' => $id,
                'claim_reimb_id' => $claim_reimb_id
            ];
            $status = $this->save_employee_reimb_hospitalization_bills($arr);


            if ($status) {
                $data_result['success'] = 'Your Details Submitted Successfully';
                $data_result['id'] = $id;
            }
            else
            {
                $data_result['success'] = 'Oops Something Went Wrong';
            } 
        }
    }
    else
    {
      $data_result['success'] = 'Oops Something Went Wrong';
  }
  return $data_result;

}

public function save_employee_reimb_hospitalization_bills($data)
{
  $query = $this->db->insert('employee_reimb_bills',$data);
  if ($query) {
    return true;
}
else
{
    return false;
}
}

public function save_claims_bill(){

 extract($this->input->post(NULL,true));
//   print_r($_FILES);
//   print_r($_POST);
//   exit;
     // $this->load->model('employee/Claims_m','em_subclaim');
 $emp_id = base64_decode($emp_id);

 $claim_amount = $this->get_claimamount_on_claimid($claim_id);
 $data1 = $this->db
 ->select('*')
 ->from('employee_policy_detail,employee_policy_member,employee_claim_reimb,family_relation')
 ->where('employee_policy_detail.policy_detail_id = employee_policy_member.policy_detail_id')
 ->where('employee_policy_member.family_relation_id = family_relation.family_relation_id')
 ->where('employee_claim_reimb.policy_member_id = employee_policy_member.policy_member_id')
 ->where('employee_policy_detail.policy_detail_id = employee_policy_member.policy_detail_id')
 ->where('family_relation.emp_id',$emp_id)
 ->where('employee_policy_detail.policy_no',$policy_numbers)
 ->group_by('family_relation.family_relation_id')
 ->get()
 ->result_array();
 $sum = 0;
 $sum_insured = 0;
 $balance = 0;
 if (count($data1) > 0) {
     for ($i=0; $i < count($data1); $i++) { 
      $sum += $data1[$i]['total_claim_amount'];

      if (isset($data1[$i]['policy_mem_sum_insured']) && !empty($data1[$i]['policy_mem_sum_insured'])) {

         $sum_insured = $data1[$i]['policy_mem_sum_insured'];
     }
 }

 $balance = $sum_insured - $sum;
 if ($claim_amount[0]['sum'] > $balance) {
  $this->db->delete('employee_reimb_bills',array('claim_reimb_id'=>$claim_id));
  $this->db->delete('employee_claim_reimb_hospitalization',array('claim_reimb_id'=>$claim_id));

  $result['success'] = 'claim amount cannot greater than to your balance cover!!!';
  return $result;
}
}
$this->load->library('upload');

for($i = 0; $i < $fileSize; $i++) {
 $config['max_filename']=0;
 $config['allowed_types'] = 'gif|png|jpg|jpeg|pdf';
 $config['file_name'] = 'docfile'.$i;
 if (!is_dir(APPPATH.'/resources/uploads/claim_document/'.$claim_id)) {
    mkdir(APPPATH.'/resources/uploads/claim_document/'.$claim_id, 0777, TRUE);
}
$config['upload_path'] = APPPATH.'/resources/uploads/claim_document/'.$claim_id.'/';
$config['max_size'] = '0';

if (!empty('docfile'.$i)) {
   $this->upload->initialize($config);
   if ($this->upload->do_upload('docfile'.$i)) {
      $data=[
       "claim_reimb_id"=>$claim_id,
       "claim_reimb_hosp_id"=>$claim_reimb_hosp_id,
       "claim_doc_medical_bill_path"=> '/resources/uploads/claim_document/'.$claim_id.'/'.$_FILES['docfile'.$i]['name']
   ];

   $data1 = $this->db->insert('employee_claim_documents',$data);
   if ($data1) 
   {
    $result['success'] = $this->update_amount($claim_amount[0]['sum'],$claim_id);

    $result['id'] = $claim_id;
}
else
{
    $result['success'] = 'error';
}

}
else{

    $result['success'] = $this->upload->display_errors();

}
}
else
{
  $result['success'] = 'error';
}
}
return $result;
}

public function update_amount($set,$where)
{
   $data = $this->db->update('employee_claim_reimb',array('total_claim_amount'=>$set),array('claim_reimb_id'=>$where));
   if ($data) {
     return true;
 }
 else
 {
  return false;
}
}

public function get_claimamount_on_claimid($claimid)
{
 $data = $this->db
 ->select('sum(erb.reimb_claim_amount) AS sum')
 ->from('employee_claim_reimb as ecr,employee_reimb_bills as erb')
 ->where('ecr.claim_reimb_id = erb.claim_reimb_id')
 ->where('ecr.claim_reimb_id', $claimid)
 ->get()
 ->result_array();
 return $data;

}

public function get_dates_on_claim_id()
{
 extract($this->input->post(NULL,true));
 $query = $this->db
 ->select('created_at AS register_date')
 ->from('employee_claim_reimb')
 ->where('claim_reimb_id',$claim_id)
 ->group_by('claim_reimb_id')
 ->get()
 ->row();
 
 if ( $query) {
  return $query;
}
}

public function get_datesdocs_on_claim_id()
{
  extract($this->input->post(null,true));
  $query1 = $this->db
  ->select('created_at AS doc_submitted')
  ->from('employee_claim_documents')
  ->where('claim_reimb_id',$claim_id)
  ->group_by('claim_reimb_id')
  ->get()
  ->row();
  if ($query1) {
   return $query1;
}
}

public function get_claimid_on_member_id()
{
   extract($this->input->post(null,true));
   $query = $this->db->select('*')->get_where('employee_claim_reimb',array('policy_no'=>$policy_no,'claim_patient_name'=>$member_id))->result_array();
   return $query;
}

function get_member_details() {
    extract($this->input->post(null, true));
    $empl_id = base64_decode($empl_id);

    $patient_data = $this->db->select('family_relation_id')->get_where('employee_policy_member', array('policy_member_id' => $patient_id))->result_array()[0];
    $emp_id = $this->db->select('emp_id,family_id')->get_where('family_relation', array('family_relation_id' => $patient_data['family_relation_id']))->result_array()[0];
    if ($emp_id['family_id'] == 0) {
        $data = $this->db
        ->select('IFNULL(ed.email,"") as email,IFNULL(ed.mob_no,"") as mob_no,IFNULL(ed.emp_id,"") AS id,"0" AS fr_id, "Self" AS relationship')
        ->from('employee_details AS ed')
        ->where('ed.emp_id', $empl_id)
        ->get()
        ->result_array();
        return $data;
    } else {
        $data = $this->db
        ->select('IFNULL(efd.family_id,"") AS id,IFNULL(efd.family_contact,"") AS mob_no,IFNULL(efd.family_email,"") AS email ,IFNULL(mfr.fr_id,"") AS fr_id,mfr.fr_name AS relationship')
        ->from('employee_family_details AS efd, master_family_relation AS mfr')
        ->where('efd.family_id', $emp_id['family_id'])
        ->where('efd.fr_id =  mfr.fr_id')
        ->get()
        ->result_array();
        return $data;
    }
}

public function get_family_memberrel_from_policy_no() {

    extract($this->input->post(null,true));
    $emp_id = base64_decode($emp_id);

    $subQuery1 = $this->db
    ->select('DISTINCT(ed.emp_id) AS id, ed.emp_firstname AS name, "Self" AS relationship,"0" AS fr_id, "0"  as family_id,epm.policy_member_id as emp_member_id')
    ->from('employee_policy_detail AS epd, employee_details AS ed, family_relation AS fr,employee_policy_member AS epm')
    ->where('ed.emp_id=fr.emp_id AND fr.family_id=0')
    ->where('fr.family_relation_id=epm.family_relation_id')
    ->where('epm.policy_detail_id=epd.policy_detail_id')
    ->where('epd.policy_no',$policy_no)
    ->where('ed.emp_id', $emp_id)

    ->get_compiled_select();
    $subQuery2 = $this->db
    ->select('efd.family_id AS familyid, efd.family_firstname AS name, mfr.fr_name AS relationship ,mfr.fr_id AS fr_id,efd.family_id as family_id,epm.policy_member_id as emp_member_id')
    ->from('employee_policy_detail AS epd,employee_policy_member AS epm ,employee_family_details AS efd, family_relation AS fr, master_family_relation AS mfr')
    ->where('efd.family_id=fr.family_id')
    ->where('fr.family_relation_id=epm.family_relation_id')
    ->where('epm.policy_detail_id=epd.policy_detail_id')
    ->where('efd.fr_id=mfr.fr_id')
    ->where('epd.policy_no', $policy_no)
    ->where('epm.status!=','Inactive')
    ->where('fr.emp_id', $emp_id)
    ->group_by('mfr.fr_id')
    ->get_compiled_select();

    $op = $this->db->query($subQuery1.' UNION '.$subQuery2)->result_array();
    return $op;
}

public function get_family_details_on_relationship() {
    extract($this->input->post(null, true));
    $emp_id = base64_decode($emp_id);
    $data = $this->db
    ->select('IFNULL(efd.family_firstname,"") as name,IFNULL(epm.policy_member_id,"") as policy_member_id,IFNULL(fr.family_relation_id,""),IFNULL(mfr.fr_name,"") AS relationship ,IFNULL(mfr.fr_id,"") AS fr_id,IFNULL(efd.family_id,"") as family_id,IFNULL(efd.family_email,"") as email_id')
    ->from('employee_policy_detail AS epd,employee_policy_member AS epm ,employee_family_details AS efd, family_relation AS fr, master_family_relation AS mfr')
    ->where('efd.family_id=fr.family_id')
    ->where('fr.family_relation_id=epm.family_relation_id')
    ->where('epm.policy_detail_id=epd.policy_detail_id')
    ->where('efd.fr_id=mfr.fr_id')
    ->where('epd.policy_no', $policy_no)
    ->where('fr.emp_id', $emp_id)
    ->where('efd.fr_id', $fr_id)
    ->get()
    ->result_array();
    return $data;
}

public function get_all_submit_claim_data()
{
    extract($this->input->post(null, true));
    $emp_id = base64_decode($emp_id);
    $subQuery1 = $this->db
    ->select('DISTINCT(ed.emp_id),ecr.claim_reimb_id,ed.emp_firstname AS name,ecr.created_at,ecr.total_claim_amount,ecr.claim_approved_amount,ecrh.claim_reimb_reason')
    ->from('employee_claim_reimb AS ecr,employee_policy_member AS epm,family_relation AS fr,master_family_relation AS mfr,employee_details AS ed,employee_claim_reimb_hospitalization AS ecrh')
    ->where('ecr.policy_member_id = epm.policy_member_id')
    ->where('ecr.claim_reimb_id = ecrh.claim_reimb_id')
    ->where('epm.family_relation_id = fr.family_relation_id')
    ->where('ed.emp_id = fr.emp_id')
    ->where('fr.family_id = 0')
    ->where('fr.emp_id',$emp_id)
    ->get_compiled_select();
    $subQuery2 = $this->db
    ->select('efd.family_id AS familyid,ecr.claim_reimb_id, efd.family_firstname AS name,ecr.created_at,ecr.total_claim_amount,ecr.claim_approved_amount,ecrh.claim_reimb_reason')
    ->from('employee_claim_reimb AS ecr,employee_policy_member AS epm,family_relation AS fr, employee_family_details AS efd,employee_claim_reimb_hospitalization AS ecrh')
    ->where('ecr.policy_member_id = epm.policy_member_id')
    ->where('ecr.claim_reimb_id = ecrh.claim_reimb_id')
    ->where('epm.family_relation_id = fr.family_relation_id')
    ->where('fr.family_id = efd.family_id')
    ->where('fr.emp_id',$emp_id)
    ->get_compiled_select();

    $op = $this->db->query($subQuery1.' UNION '.$subQuery2.'
        ORDER BY claim_reimb_id desc')->result_array();
    // echo $this->db->last_query();exit;
    return $op;     
}

public function get_all_intimate_claim_data()
{
    extract($this->input->post(null, true));
    $emp_id = base64_decode($emp_id);
    $subQuery1 = $this->db
    ->select('DISTINCT(ed.emp_id),ed.emp_firstname AS name,eci.claim_intimate_id,eci.claim_type,eci.created_date,eci.claim_Amount,eci.reason')
    ->from('employee_claim_intimate AS eci,employee_policy_member AS epm,family_relation AS fr,master_family_relation AS mfr,employee_details AS ed')
    ->where('eci.claim_patient_name = epm.policy_member_id')
    ->where('epm.family_relation_id = fr.family_relation_id')
    ->where('ed.emp_id = fr.emp_id')
    ->where('fr.family_id = 0')
    ->where('fr.emp_id',$emp_id)
    ->get_compiled_select();
    $subQuery2 = $this->db
    ->select('DISTINCT(eci.claim_patient_name),efd.family_firstname AS name,eci.claim_intimate_id,eci.claim_type,eci.created_date,eci.claim_Amount,eci.reason')
    ->from('employee_claim_intimate AS eci,employee_policy_member AS epm,family_relation AS fr,master_family_relation AS mfr,employee_family_details AS efd')
    ->where('eci.claim_patient_name = epm.policy_member_id')
    ->where('epm.family_relation_id = fr.family_relation_id')
    ->where('fr.family_id = efd.family_id')
    ->where('fr.emp_id',$emp_id)
    ->get_compiled_select();
    $op = $this->db->query($subQuery1.' UNION '.$subQuery2.'
        ORDER BY claim_intimate_id desc')->result_array();
            // echo $this->db->last_query();
    return $op;     
}

function add_dependent() {
    extract($this->input->post(NULL, true));
    $emp_id = base64_decode($emp_id);

    $family_members_id[0]=$family_members_id_arr;
    $first_name[0]=$first_name_arr;
    $last_name[0]=$last_name_arr;
    $family_date_birth[0]=$family_date_birth_arr;
    $family_gender[0]=$family_gender_arr;
    $policyid[0]=$policy_id_arr;
    $familyId[0]=$family_id_arr;
    $marriage_date[0]=$marriage_date_arr;
    $disable_child[0]=$disable_child_arr;
    $age[0]=$age_arr;
    $age_type[0]=$age_type_arr;

// echo $family_gender_arr;

    if($family_gender_arr == 'null' || empty($family_gender_arr)){
        return [
            "status" => "Please select gender",
            "message" => "Please select gender"
        ];
    }

    if($deduction_type != '')
    {
        if($contri_parent!=0)
        {
            $premium = ($premium_parent)*($contri_parent/100);
        }
        else 
        {
           $premium = $premium_parent;
       }

       $data = $this->db
       ->select('ed.flex_amount')
       ->from('employee_details AS ed')
       ->where('ed.emp_id', $emp_id)
       ->get()
       ->result_array();
            // print_r($data[0]['flex_amount']);

       $result = $this->db->select('sum(flex_amount) AS utilized_amt')
       ->from('employee_flexi_benefit_transaction')
       ->where('emp_id', $emp_id)
       ->get()
       ->result_array();
             // print_r($result[0]['utilized_amt']);

       $wallet_bal = ($data[0]['flex_amount'] - $result[0]['utilized_amt']);

       if($deduction_type == 'F')
       {

        if($premium > $wallet_bal && $wallet_bal != 0)
        {
            return [
                "status" => "Cut amount - ".$cut_amt = ($premium - $wallet_bal)."& Your Total Amount is".$wallet_bal,
                "message" => "Cut amount - ".$cut_amt = ($premium - $wallet_bal)."& Your Total Amount is".$wallet_bal
            ];
                        // echo "Cut amount - ".$cut_amt = ($premium - $wallet_bal)."& Your Total Amount is".$wallet_bal;
                        // exit();
        }
        else if($wallet_bal == 0 && $premium > $wallet_bal)
        {
         return [
            "status" => "Flex balance is not enough",
            "message" => "Flex balance is not enough"
        ];

                        // exit();
    }
    else
    {
        $flex_amount = $premium;
        $pay_amount = '';
        $final_amount = $premium;
    }
}
else
{
    $flex_amount = '';
    $pay_amount = $premium;
    $final_amount = $premium;
}

}
else
{
    $flex_amount = '';
    $pay_amount = '';
    $final_amount = '';
}


$path = '';

$query2 = $this->db->select('*')
->from('employee_details AS ed,master_company AS mc')
->where('ed.company_id = mc.company_id')
->where('mc.enrollment_end_date >=', date('Y-m-d'))
->where('ed.emp_id', $emp_id)
->get()
->result_array();
if (count($query2) == 0) {

    for ($i = 0; $i < count($first_name); ++$i) {

        if ($family_members_id[$i] == 1) {

            $date = date("Y-m-d", strtotime($marriage_date[$i] . "+30 days"));

            if (strtotime($date) <= strtotime(date('Y-m-d'))) {
                return [
                    "status" => "Enrollment window close",
                    "message" => "Enrollment window close"
                ];
            }
        }
    }
}

        // for previous grade based 
$var = $this->db->select('ed.emp_id, ed.fr_id, ed.company_code, ed.broker_id, ed.company_id, ed.member_id, ed.gmc_grade_id, ed.emp_pay, ed.doj, ed.total_salary, ed.flex_amount,gge.gmc_grade_id, gge.gmc_grade, gge.gmc_sum_insured, gge.gmc_sum_premium')
->from('employee_details AS ed, gmc_grade_employee AS gge')
->where('ed.gmc_grade_id=`gge`.`gmc_grade_id')
->where('emp_id', $emp_id)
->get()
->row_array();

$arr = [];
$arrFamily = [];
$arrFamilydata = [];
$counterFamily = 0;
$max_allowed_relation = $this->db->select('*')
->from('master_broker_ic_relationship as `mbir, master_family_relation as mfr`')
->where('find_in_set(mfr.fr_id, mbir.relationship_id)')
->where('mbir`.`policy_id`', $policyid[0])
->get()->result_array();

$employee_dob = $this->db->select('IFNULL(bdate, 0) as bdate')
->from('employee_details')
->where('emp_id', $emp_id)
->get()
->row_array();
$employee_age = get_date_diff('year', $employee_dob['bdate']);

if (in_array($family_members_id[0], [2, 3])) {
    $child_age = get_date_diff('year', $family_date_birth[0]);

    if (($employee_age - $child_age) < 18) {
        return (["status" => "Employee and family member age difference must be atleast 18 years"]);
    }

    $response = $this->db->select('family_dob')
    ->from('employee_family_details AS efd, family_relation AS fr, employee_policy_member AS epm ')
    ->where('epm`.`family_relation_id`=`fr`.`family_relation_id')
    ->where('efd`.`family_id`=`fr`.`family_id')
    ->where("efd.fr_id BETWEEN 2 AND 3")
    ->where("epm.status!=", 'Inactive')
    ->where("epm.policy_detail_id", $policyid[0])
    ->where('fr.emp_id', $emp_id)
    ->get()
    ->result_array();

    foreach ($response as $res) {
        if (empty($arrFamily)) {
            array_push($arrFamily, $res['family_dob']);
        } else if (in_array($res['family_dob'], $arrFamily)) {
                    // ++$counterFamily;
        } else {
            array_push($arrFamily, $res['family_dob']);
        }
    }

    for ($i = 0; $i < count($first_name); ++$i) {
        if ($family_members_id[$i] == 2 || $family_members_id[$i] == 3) {
            if (empty($arrFamily)) {
                array_push($arrFamily, $family_date_birth[$i]);
            } else if (in_array($family_date_birth[$i], $arrFamily)) {
                        // ++$counterFamily;
            } else {
                array_push($arrFamily, $family_date_birth[$i]);
            }
        }
    }

    if ($counterFamily == 0 && count($arrFamily) <= $max_allowed_relation[0]['max_child']) {

    } else if ($counterFamily == 1 && count($arrFamily) <= $max_allowed_relation[0]['max_child']) {

    } else {
        return (["status" => "Sorry You can not add more than 2 kids"]);
    }

    $count_of_child = count($response) + 1;
    if ($count_of_child > $max_allowed_relation[0]['twins_child_limit']) {
        return (["status" => "Maximum Child Limit Exceeded!"]);
    }
} elseif (!in_array($family_members_id[0], [2, 3])) {
    if (!in_array($family_members_id[0], [0, 1])) {
        $adult_age = get_date_diff('year', $family_date_birth[0]);
        if (($adult_age - $employee_age) < 18) {
            return (["status" => "Employee and family member age difference must be atleast 18 years"]);
        }
    }

            // for checking maximum adult and add as per db
    $chk_adults = $this->db->select('efd.fr_id')
    ->from('employee_family_details AS efd, family_relation AS fr, employee_policy_member AS epm ')
    ->where('epm`.`family_relation_id`=`fr`.`family_relation_id')
    ->where('efd`.`family_id`=`fr`.`family_id')
    ->where("efd.fr_id in(1,4,5,6,7)")
    ->where("epm.status!=", 'Inactive')
    ->where("epm.policy_detail_id", $policyid[0])
    ->where('fr.emp_id', $emp_id)
    ->get()
    ->result_array();
            //$arrFamilydata = [];

    $max_allowed_relation[0]['max_adult'] -= 1;
    $motherFatherCombo = [4, 5];
    $lawCombo = [6, 7];

    foreach ($chk_adults as $adul) {

        if (empty($arrFamilydata[$adul['fr_id']])) {
            $arrFamilydata[$adul['fr_id']] = 1;
        } else {
            return (["status" =>"You canot add More than" . " " . $adul['fr_id'], "message" => "You canot add More than" . " " . $adul['fr_id']]);
        }
    }

    for ($i = 0; $i < count($first_name); ++$i) {

        if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {

            if (empty($arrFamilydata[$family_members_id[$i]])) {

                $arrFamilydata[$family_members_id[$i]] = 1;
            } else {

                return (["status" => "You canot add More than 1" . " " . $this->db->where(["fr_id" => $family_members_id[$i]])->get("master_family_relation")->row()->fr_name, "message" => "You canot add More than 1" . " " . $this->db->where(["fr_id" => $family_members_id[$i]])->get("master_family_relation")->row()->fr_name]);
            }
        }

        if ($counterFamily == 0 && count($arrFamilydata) <= $max_allowed_relation[0]['max_adult']) {

        } else if ($counterFamily == 1 && count($arrFamilydata) <= $max_allowed_relation[0]['max_adult']) {

        } else {
            return (["status" => "You canot add More than 1" . " " . $this->db->where(["fr_id" => $family_members_id[$i]])->get("master_family_relation")->row()->fr_name, "message" => "You canot add More than 1" . " " . $this->db->where(["fr_id" => $family_members_id[$i]])->get("master_family_relation")->row()->fr_name]);
        }
    }

    if ($max_allowed_relation[0]['max_adult'] == 1) {
        foreach ($chk_adults as $adul) {
            if ($adul['fr_id'] != 1) {
                return (["status" => "You canot add More than" . " " . $adul['fr_id'], "message" => "You canot add More than" . " " . $adul['fr_id']]);
            }
        }

        for ($i = 0; $i < count($first_name); ++$i) {
            if ($family_members_id[$i] != 1 && $family_members_id[$i] != 2 && $family_members_id[$i] != 3) {
                return (["status" => "You canot add More than" . " " . $adul['fr_id'], "message" => "You canot add More than" . " " . $adul['fr_id']]);
            }
        }
    } else if ($max_allowed_relation[0]['max_adult'] >= 2) {
        $parent_cross_selection = $this->db->select('epd.parent_cross_selection')
        ->from('family_relation AS fr, employee_policy_member AS epm, employee_policy_detail as epd')
        ->where('epm`.`family_relation_id`=`fr`.`family_relation_id')
        ->where("epm.policy_detail_id", $policyid[0])
        ->where("epm.policy_detail_id = epd.policy_detail_id")
        ->where('fr.emp_id', $emp_id)
        ->get()
        ->row_array();
        if ($parent_cross_selection['parent_cross_selection'] == 'N') {
            foreach ($chk_adults as $chk) {

                if ($chk['fr_id'] != 1) {
                    if (in_array($family_members_id[0], $motherFatherCombo)) {
                        if (!(in_array($chk['fr_id'], $motherFatherCombo) && in_array($family_members_id[0], $motherFatherCombo))) {
                            return (["status" => "incorrect Family", "message" => "You canot add More than" . " "]);
                        }
                    }

                    if (in_array($family_members_id[0], $lawCombo)) {
                        if (!(in_array($chk['fr_id'], $lawCombo) && in_array($family_members_id[0], $lawCombo))) {
                            return (["status" => "incorrect Family", "message" => "You canot add More than" . " "]);
                        }
                    }
                }
            }
        }

    }
}

for ($i = 0; $i < count($first_name); ++$i) {
    $path = '';
    if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
        $response = $this->db->select('*')
        ->from('employee_family_details AS efd, family_relation AS fr ')
        ->where('efd`.`family_id`=`fr`.`family_id')
        ->where('efd.fr_id', $family_members_id[$i])
        ->where('efd.family_id', $familyId[$i])
        ->where('fr.emp_id', $emp_id)
        ->get_compiled_select();
        $op = $this->db->query($response)->row_array();
    } else if ($family_members_id[$i] == 2 || $family_members_id[$i] == 3) {
        $response = $this->db->select('*')
        ->from('employee_family_details AS efd, family_relation AS fr ')
        ->where('efd`.`family_id`=`fr`.`family_id')
        ->where('efd.fr_id', $family_members_id[$i])
        ->where('efd.family_id', $familyId[$i])
        ->where('fr.emp_id', $emp_id)
        ->get_compiled_select();
        $op = $this->db->query($response)->row_array();
    }
    $sum_insure = 0;
    $sum_premium = 0;
    $employee_policy_mem_sum_premium = 0;
    $employer_policy_mem_sum_premium = 0;
            // echo $this->db->last_query();
            // exit();
    $arr[$i] = $op;
            // for designation
    $designation_res = [];
    $designation_res = $this->db->select('epd.premiumCalType, epd.PremiumServiceTax,epd.policy_detail_id, policy_sub_type_id, sum_insured_type,sum_insured, addition_premium, epd.premium as p_premuim,special_child_check, special_child_contri,unmarried_child_check,unmarried_child_contri,applicable_for_designation_id,pal.policy_detail_id, relation_id,min_age,max_age,employee_contri,employer_contri, pal.premium as topremium')
    ->from('employee_policy_detail AS epd, employee_details AS ed, master_company AS mc, policy_age_limit AS pal')
    ->where('FIND_IN_SET(`ed`.`emp_designation`, epd.applicable_for_designation_id)')
    ->where('mc.company_id = ed.company_id')
    ->where('pal`.`policy_detail_id` = `epd`.`policy_detail_id`')
    ->where('pal`.`relation_id`', $family_members_id[$i])
    ->where('emp_id', $emp_id)
    ->get()
    ->row_array();


    $non_des = $this->db->select('epd.premiumCalType, epd.PremiumServiceTax,epd.policy_detail_id, epd.applicable_for, policy_sub_type_id, sum_insured_type,epd.sum_insured, addition_premium,'
        . ' epd.premium as p_premuim,special_child_check, '
        . 'special_child_contri,unmarried_child_check,'
        . 'unmarried_child_contri,applicable_for_designation_id,unmarried_child_cover,  broker_percent, epd.flex_allocate, epd.payroll_allocate, epd.parent_cross_selection, epd.marital_status, epd.status_wise_single_si, 
        epd.status_wise_single_pre, epd.status_wise_married_si, epd.status_wise_married_pre, epd.premium_paid, epd.cd_balance_threshold, epd.premium_type,
        epd.gpa_no_of_times, pal.policy_detail_id, relation_id,min_age,max_age,employee_contri,employer_contri, pal.premium as topremium')
    ->from('employee_policy_detail AS epd, employee_details AS ed, master_company AS mc, policy_age_limit AS pal')
    ->where('mc.company_id = ed.company_id')
    ->where('pal`.`policy_detail_id` = `epd`.`policy_detail_id`')
    ->where('pal`.`relation_id`', $family_members_id[$i])
    ->where('ed.emp_id', $emp_id)
    ->where('epd`.`policy_detail_id', $policyid[$i])
    ->get()
    ->row_array();
    $grade = ord($this->session->userdata("grade"));
    $grade_wise_data = $this->db->query("SELECT ASCII(SUBSTRING_INDEX(policy_grade, '-', 1)) AS MIN, SUBSTRING_INDEX(policy_grade, '-', 1),epd.policy_detail_id, epd.applicable_for, policy_sub_type_id, sum_insured_type,epd.sum_insured, addition_premium,
      epd.premium as p_premuim,special_child_check,special_child_contri,unmarried_child_check,
      unmarried_child_contri,applicable_for_designation_id,unmarried_child_cover,  broker_percent, epd.flex_allocate, epd.payroll_allocate, epd.parent_cross_selection, epd.marital_status, epd.status_wise_single_si,
      epd.status_wise_single_pre, epd.status_wise_married_si, epd.status_wise_married_pre, epd.premium_paid, epd.cd_balance_threshold, epd.premium_type,pcg.policy_id,pcg.policy_grade, pcg.sum_insured as pcg_sumInsured, pcg.premium as pcgpremium, pcg.employee_contri_percent, pcg.employer_contri_percent,
      epd.gpa_no_of_times, pal.policy_detail_id, relation_id,min_age,max_age,employee_contri,employer_contri, pal.premium as topremium
      FROM employee_policy_detail AS epd, employee_details AS ed, master_company AS mc, policy_age_limit AS pal,  policy_creation_grade as pcg
      WHERE mc.company_id = ed.company_id AND pal.policy_detail_id = epd.policy_detail_id
      AND pcg.policy_id = epd.policy_detail_id
      AND pal.relation_id = " .  $family_members_id[$i] . " AND epd.policy_detail_id =  $policyid[$i]
      AND  " . $grade . " >= ASCII(SUBSTRING_INDEX(policy_grade, '-', 1))  AND " . $grade. " <= ASCII(SUBSTRING_INDEX(policy_grade, '-', -1))")->result_array();



    $agr_wise_data = $this->db->select('epd.policy_detail_id, epd.applicable_for, policy_sub_type_id, sum_insured_type,epd.sum_insured, addition_premium,'
        . ' epd.premium as p_premuim,special_child_check, '
        . 'special_child_contri,unmarried_child_check,'
        . 'unmarried_child_contri,applicable_for_designation_id,unmarried_child_cover,  broker_percent, epd.flex_allocate, epd.payroll_allocate, epd.parent_cross_selection, epd.marital_status, epd.status_wise_single_si, 
        epd.status_wise_single_pre, epd.status_wise_married_si, epd.status_wise_married_pre, epd.premium_paid, epd.cd_balance_threshold, epd.premium_type,pca.policy_id,pca.policy_age, pca.sum_insured as pca_sumInsured, pca.premium as pcapremium, pca.employee_contri_percent, pca.employer_contri_percent,
        epd.gpa_no_of_times, pal.policy_detail_id, relation_id,min_age,max_age,employee_contri,employer_contri, pal.premium as topremium')
    ->from('employee_policy_detail AS epd, employee_details AS ed, master_company AS mc, policy_age_limit AS pal, policy_creation_age as pca')
    ->where('mc.company_id = ed.company_id')
    ->where('pal`.`policy_detail_id` = `epd`.`policy_detail_id`')
    ->where('pca`.`policy_id` = `epd`.`policy_detail_id`')
    ->where('pal`.`relation_id`', $family_members_id[$i])
    ->where('ed.emp_id', $emp_id)
    ->where('epd`.`policy_detail_id',  $policyid[$i])
    ->group_by('pca.policy_age')
    ->get()
    ->result_array();


    if (count($query2) == 0) {

        $policy_date = $this->db->select('epd.policy_detail_id, epd.policy_no, epd.broker_id, epd.company_id,epd.start_date, epd.end_date')
        ->from('employee_policy_detail AS epd')
        ->where('epd.policy_detail_id', $policyid[0])
        ->get()
        ->row_array();
        $emp_dj = $this->db->select('ed.emp_id,ed.emp_code,ed.fr_id, ed.company_code, ed.access_right_id, ed.broker_id, ed.company_id, ed.employer_id, ed.member_id, ed.emp_firstname, 
            ed.emp_lastname, ed.gender,ed.bdate,ed.email,ed.emp_grade,ed.emp_designation, ed.emp_pay, ed.doj')
        ->from('employee_details AS ed')
        ->where('emp_id', $emp_id)
        ->get()
        ->row_array();

        $emp_start_date = date('Y-m-d');
                //$emp_start_date = $emp_dj['doj'];
        $policy_end_date = $policy_date['end_date'];
        $policy_start_date = $policy_date['start_date'];
        $policy_days_diff = date_diff_in_days($emp_start_date, $policy_end_date);
        $policy_date_days = date_diff_in_days($policy_start_date, $policy_end_date);
        $current_year = date('Y');

        if ($non_des['applicable_for_designation_id'] == "") {


            if ($non_des['policy_sub_type_id'] == 1) {

                if ($non_des['premium_type'] == 'memberAge') {

                    for ($k = 0; $k < count($agr_wise_data); $k++) {
                        $min_age = explode("-", $agr_wise_data[$k]['policy_age']);

                        if ($age[$i] >= $min_age[0] && $age[$i] <= $min_age[1]) {

                            $sum_insure = $agr_wise_data[$k]['pca_sumInsured'];
                                                //$sum_premium = $agr_wise_data[$k]['pcapremium']; 
                            $per_day_premium = $agr_wise_data[$k]['pcapremium'] / $policy_date_days;
                            $premium = $policy_days_diff * $per_day_premium;
                            $sum_premium = $premium;


                            if ($agr_wise_data[$k]['addition_premium'] == 1) {

                                if ($agr_wise_data[$k]['sum_insured_type'] == 'individual' || $agr_wise_data[$k]['sum_insured_type'] == 'familyIndividual') {

                                    if ($family_members_id[$i] == 0) { 

                                        if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
                                            $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100; 
                                            $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
                                        } else {
                                            return [
                                                "status" => "Min age".$non_des['min_age']."Max age".$non_des['max_age'],
                                                "message" => "Min age".$non_des['min_age']."Max age".$non_des['max_age']
                                            ];
                                                                // return [
                                                                //     "Min age" => $non_des['min_age'],
                                                                //     "Max age" => $non_des['max_age'],
                                                                // ];
                                        }
                                    } else if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
                                        if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
                                            $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                                            $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
                                        } 
                                    }else {

                                        if($agr_wise_data[$k]['special_child_check'] == 1 || $agr_wise_data[$k]['unmarried_child_check'] == 1){
                                            if ($agr_wise_data[$k]['special_child_check'] == 1) {
                                                $special = explode(",", $special);
                                                $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                                                $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                                            } else if ($age >= 25 && $age_type == "years" && $agr_wise_data[$k]['unmarried_child_check'] == 1) {
                                                $unmarried = explode(",", $unmarried);
                                                $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                                                $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                                            } else {
                                                $employee_policy_mem_sum_premium = 0;
                                                $employer_policy_mem_sum_premium = $sum_premium;
                                            }
                                        }
                                        else {
                                            if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
                                                $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                                                $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
                                            } 
                                        }
                                    }
                                } 
                                else {
                                    $unmarried = $agr_wise_data[$k]['unmarried_child_contri'];
                                    $special = $agr_wise_data[$k]['special_child_contri'];

                                    if ($family_members_id[$i] == 2 || $family_members_id[$i] == 3) {

                                     if($agr_wise_data[$k]['special_child_check'] == 1 || $agr_wise_data[$k]['unmarried_child_check'] == 1){
                                        if ($age >= 25 && $age_type == "years" && $agr_wise_data[$k]['unmarried_child_check'] == 1) {
                                            $unmarried = explode(",", $unmarried);
                                            $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                                            $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                                        } else if ($age > 0 && $agr_wise_data[$k]['special_child_check'] == 1) {
                                            $special = explode(",", $special);
                                            $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                                            $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                                        } else {
                                            $employee_policy_mem_sum_premium = 0;
                                            $employer_policy_mem_sum_premium = $sum_premium;
                                        }
                                    }
                                    else {

                                        if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
                                            $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                                            $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
                                        } 
                                    }

                                } else if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7 || $family_members_id[$i] == 0 ) {
                                 if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
                                    $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                                    $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
                                }
                            } 
                        }
                    } 

                }
            }


        } 
        else if($non_des['premium_type'] == 'grade'){


            $sum_premium = 0;
            $sum_insured = 0;
            for ($i = 0; $i < count($grade_wise_data); $i++) {

                if ($grade_wise_data[$i]['addition_premium'] == 0) {
                    $sum_insured = $grade_wise_data[$i]['pcg_sumInsured'];
                                            //$sum_premium = $grade_wise_data[$i]['pcgpremium'];
                    $per_day_premium = $non_des['pcgpremium'] / $policy_date_days;
                    $premium = $policy_days_diff * $per_day_premium;
                    $sum_premium = $premium;


                    if ($grade_wise_data[$i]['sum_insured_type'] == 'individual' || $grade_wise_data[$i]['sum_insured_type'] == 'familyIndividual') {
                        if ($family_members_id[$i] == 0) {
                            if ($grade_wise_data[$i]['min_age'] <= $age || $grade_wise_data[$i]['max_age'] >= $age) {
                                $employee_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employee_contri_percent']) / 100;
                                $employer_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employer_contri_percent']) / 100;
                            } else {
                             return [
                                "status" => "Min age".$grade_wise_data[$i]['min_age']."Max age".$grade_wise_data[$i]['max_age'],
                                "message" => "Min age".$grade_wise_data[$i]['min_age']."Max age".$grade_wise_data[$i]['max_age']
                            ];
                                                        // return [
                                                        //     "Min age" => $grade_wise_data[$i]['min_age'],
                                                        //     "Max age" => $grade_wise_data[$i]['max_age'],
                                                        // ];
                        }
                    } else if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
                        $sum_premium = 0;
                        $employee_policy_mem_sum_premium = 0;
                        $employer_policy_mem_sum_premium = 0;
                    } else {
                        if ($grade_wise_data['min_age'] <= $age || $grade_wise_data['max_age'] >= $age) {
                         if($grade_wise_data[$i]['special_child_check'] == 1 || $grade_wise_data[$i]['unmarried_child_check'] == 1){
                            if ($grade_wise_data[$i]['special_child_check'] == 1) {
                                $special = explode(",", $special);
                                $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                                $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                            } else if ($age >= 25 && $age_type == "years" && $grade_wise_data[$i]['unmarried_child_check'] == 1) {
                                $unmarried = explode(",", $unmarried);
                                $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                                $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                            } else {
                                $employee_policy_mem_sum_premium = 0;
                                $employer_policy_mem_sum_premium = $sum_premium;
                            }
                        }
                        else {
                         $employee_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employee_contri_percent']) / 100;
                         $employer_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employer_contri_percent']) / 100;
                     }
                 }
             }
         } else {
            $unmarried = $grade_wise_data[$i]['unmarried_child_contri'];
            $special = $grade_wise_data[$i]['special_child_contri'];
            $sum_insured = $grade_wise_data[$i]['sum_insured'];
                                                //$sum_premium = $non_des['p_premuim'];
            if ($family_members_id[$i] == 2 || $family_members_id[$i] == 3) {
             if($grade_wise_data[$i]['special_child_check'] == 1 || $grade_wise_data[$i]['unmarried_child_check'] == 1){
                if ($age >= 25 && $grade_wise_data[$i]['unmarried_child_check'] == 1) {
                    $unmarried = explode(",", $unmarried);
                    $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                } else if ($age > 0 && $grade_wise_data[$i]['special_child_check'] == 1) {
                    $special = explode(",", $special);
                    $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                } else {
                    $employee_policy_mem_sum_premium = 0;
                    $employer_policy_mem_sum_premium = $sum_premium;
                }
            }
            else {
             $employee_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employee_contri_percent']) / 100;
             $employer_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employer_contri_percent']) / 100;
         }
     } else if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
        $sum_premium = 0;
        $employee_policy_mem_sum_premium = 0;
        $employer_policy_mem_sum_premium = 0;
    } else {
        if ($grade_wise_data['min_age'] <= $age || $grade_wise_data[$i]['max_age'] >= $age) {
            $employee_policy_mem_sum_premium = 0;
            $employer_policy_mem_sum_premium = $sum_premium;
        } else {
            return [
                "status" => "Min age".$grade_wise_data[$i]['min_age']."Max age".$grade_wise_data[$i]['max_age'],
                "message" => "Min age".$grade_wise_data[$i]['min_age']."Max age".$grade_wise_data[$i]['max_age']
            ];
                                                        // return [
                                                        //     "Min age" => $grade_wise_data[$i]['min_age'],
                                                        //     "Max age" => $grade_wise_data[$i]['max_age'],
                                                        // ];
        }
    }
}
} else {
                                            // $sum_premium = $grade_wise_data[$i]['topremium'] ;
    $sum_premium = $grade_wise_data[$i]['topremium'];

    if ($grade_wise_data[$i]['sum_insured_type'] == 'individual' || $grade_wise_data[$i]['sum_insured_type'] == 'familyIndividual') {
        $sum_insured = $grade_wise_data[$i]['sum_insured'];
        if ($family_members_id[$i] == 0 || $family_members_id[$i] == 1 || $family_members_id[$i] == 2 || $family_members_id[$i] == 3 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
            if ($grade_wise_data[$i]['min_age'] <= $age || $grade_wise_data[$i]['max_age'] >= $age) {
                if ($grade_wise_data[$i]['relation_id'] == 1 || $grade_wise_data[$i]['relation_id'] == 0 || $grade_wise_data[$i]['relation_id'] == 2 || $grade_wise_data[$i]['relation_id'] == 3 || $grade_wise_data[$i]['relation_id'] == 4 || $grade_wise_data[$i]['relation_id'] == 5 || $grade_wise_data[$i]['relation_id'] == 6 || $grade_wise_data[$i]['relation_id'] == 7) {
                    $employee_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employee_contri']) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employer_contri']) / 100;
                } else {
                    $sum_premium = $sum_premium;
                    $employee_policy_mem_sum_premium = 0;
                    $employer_policy_mem_sum_premium = $sum_premium;
                }
            } else {
                return [
                    "status" => "Min age".$grade_wise_data[$i]['min_age']."Max age".$grade_wise_data[$i]['max_age'],
                    "message" => "Min age".$grade_wise_data[$i]['min_age']."Max age".$grade_wise_data[$i]['max_age']
                ];
                                                        // return [
                                                        //     "Min age" => $grade_wise_data[$i]['min_age'],
                                                        //     "Max age" => $grade_wise_data[$i]['max_age'],
                                                        // ];
            }
        }
    } else {
        $sum_insured = $grade_wise_data[$i]['sum_insured'];
        if ($family_members_id[$i] == 0 || $family_members_id[$i]== 1 || $family_members_id[$i] == 2 || $family_members_id[$i] == 3 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
            if ($grade_wise_data[$i]['min_age'] <= $age || $grade_wise_data[$i]['max_age'] >= $age) {
                if ($grade_wise_data[$i]['relation_id'] == 1 || $grade_wise_data[$i]['relation_id'] == 0 || $grade_wise_data[$i]['relation_id'] == 2 || $grade_wise_data[$i]['relation_id'] == 3 || $grade_wise_data[$i]['relation_id'] == 4 || $grade_wise_data[$i]['relation_id'] == 5 || $grade_wise_data[$i]['relation_id'] == 6 || $grade_wise_data[$i]['relation_id'] == 7) {
                    $employee_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employee_contri']) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employer_contri']) / 100;
                } else {
                    $sum_premium = $sum_premium;
                    $employee_policy_mem_sum_premium = 0;
                    $employer_policy_mem_sum_premium = $sum_premium;
                }
            } else {
                return [
                    "status" => "Min age".$grade_wise_data[$i]['min_age']."Max age".$grade_wise_data[$i]['max_age'],
                    "message" => "Min age".$grade_wise_data[$i]['min_age']."Max age".$grade_wise_data[$i]['max_age']
                ];
                                                        // return [
                                                        //     "Min age" => $grade_wise_data[$i]['min_age'],
                                                        //     "Max age" => $grade_wise_data[$i]['max_age'],
                                                        // ];
            }
        }
    }
}
}

}              
else if ($non_des['premium_type'] == 'age') {

    for ($k = 0; $k < count($agr_wise_data); $k++) {
      $min_age = explode("-", $agr_wise_data[$k]['policy_age']);

      if ($age >= $min_age[0] && $age <= $min_age[1]) {
        $sum_insure = $agr_wise_data[$k]['pca_sumInsured'];
                                               // $sum_premium = $agr_wise_data[$k]['pcapremium'];
        $per_day_premium = $non_des['pcapremium'] / $policy_date_days;
        $premium = $policy_days_diff * $per_day_premium;
        $sum_premium = $premium;


        if ($agr_wise_data[$k]['addition_premium'] == 0) {
            if ($agr_wise_data[$k]['sum_insured_type'] == 'individual' || $agr_wise_data[$k]['sum_insured_type'] == 'familyIndividual') {
                if ($family_members_id[$i] == 0) {
                    if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
                        $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                        $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
                    } else {
                        return [
                            "status" => "Min age".$non_des['min_age']."Max age".$non_des['max_age'],
                            "message" => "Min age".$non_des['min_age']."Max age".$non_des['max_age']
                        ];
                                                                // return [
                                                                //     "Min age" => $non_des['min_age'],
                                                                //     "Max age" => $non_des['max_age'],
                                                                // ];
                    }
                } else if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
                   $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                   $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
               } else {
                 if($agr_wise_data[$k]['special_child_check'] == 1 || $agr_wise_data[$k]['unmarried_child_check'] == 1){
                    if ($agr_wise_data[$k]['special_child_check'] == 1) {
                        $special = explode(",", $special);
                        $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                        $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                    } else if ($age >= 25 && $age_type == "years" && $agr_wise_data[$k]['unmarried_child_check'] == 1) {
                        $unmarried = explode(",", $unmarried);
                        $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                        $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                    } else {
                        $employee_policy_mem_sum_premium = 0;
                        $employer_policy_mem_sum_premium = $sum_premium;
                    }
                }
                else {
                   $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                   $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
               }
           }
       } 
       else {
        $unmarried = $agr_wise_data[$k]['unmarried_child_contri'];
        $special = $agr_wise_data[$k]['special_child_contri'];

        if ($family_members_id[$i] == 2 || $family_members_id[$i] == 3) {
            if($agr_wise_data[$k]['special_child_check'] == 1 || $agr_wise_data[$k]['unmarried_child_check'] == 1){
                if ($age >= 25 && $age_type == "years" && $agr_wise_data[$k]['unmarried_child_check'] == 1) {
                    $unmarried = explode(",", $unmarried);
                    $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                } else if ($age > 0 && $agr_wise_data[$k]['special_child_check'] == 1) {
                    $special = explode(",", $special);
                    $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                } else {
                    $employee_policy_mem_sum_premium = 0;
                    $employer_policy_mem_sum_premium = $sum_premium;
                }
            }
            else {
               $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
               $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
           }
       } else if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
           $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
           $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
       } else {
        if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
           $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
           $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
       } else {

         return [
            "status" => "Min age".$agr_wise_data[$k]['min_age']."Max age".$agr_wise_data[$k]['max_age'],
            "message" => "Min age".$agr_wise_data[$k]['min_age']."Max age".$agr_wise_data[$k]['max_age']
        ];
                                                                // return [
                                                                //     "Min age" => $agr_wise_data[$k]['min_age'],
                                                                //     "Max age" => $agr_wise_data[$k]['max_age'],
                                                                // ];
    }
}
}
} 
else {
    if ($agr_wise_data[$k]['sum_insured_type'] == 'individual' || $agr_wise_data[$k]['sum_insured_type'] == 'familyIndividual') {
        if ($family_member_relation == 0) {
            if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
                $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
            } else {
                return [
                    "status" => "Min age".$non_des['min_age']."Max age".$non_des['max_age'],
                    "message" => "Min age".$non_des['min_age']."Max age".$non_des['max_age']
                ];
                                                                // return [
                                                                //     "Min age" => $non_des['min_age'],
                                                                //     "Max age" => $non_des['max_age'],
                                                                // ];
            }
        } else if ($family_member_relation == 1 || $family_member_relation == 4 || $family_member_relation == 5 || $family_member_relation == 6 || $family_member_relation == 7) {
            if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
                $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
            } 
        }else {
            if($agr_wise_data[$k]['special_child_check'] == 1 || $agr_wise_data[$k]['unmarried_child_check'] == 1){
                if ($agr_wise_data[$k]['special_child_check'] == 1) {
                    $special = explode(",", $special);
                    $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                } else if ($age >= 25 && $age_type == "years" && $agr_wise_data[$k]['unmarried_child_check'] == 1) {
                    $unmarried = explode(",", $unmarried);
                    $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                } else {
                    $employee_policy_mem_sum_premium = 0;
                    $employer_policy_mem_sum_premium = $sum_premium;
                }
            }
            else {
                if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
                    $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
                } 
            }
        }
    }
    else {

        $unmarried = $agr_wise_data[$k]['unmarried_child_contri'];
        $special = $agr_wise_data[$k]['special_child_contri'];

        if ($family_member_relation== 2 || $family_member_relation == 3) {
         if($agr_wise_data[$k]['special_child_check'] == 1 || $agr_wise_data[$k]['unmarried_child_check'] == 1){

             if ($age >= 25 && $age_type == "years" && $agr_wise_data[$k]['unmarried_child_check'] == 1) {
                $unmarried = explode(",", $unmarried);
                $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
            } else if ($age > 0 && $agr_wise_data[$k]['special_child_check'] == 1) {
                $special = explode(",", $special);
                $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
            } else {
                $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
            }
        }
        else {
         $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
         $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
     }
 } else if ($family_member_relation == 1 || $family_member_relation == 4 || $family_member_relation == 5 || $family_member_relation == 6 || $family_member_relation == 7) {
    $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
    $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
} else {
    if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
       $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
       $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
   } else {
    return [
        "status" => "Min age".$agr_wise_data[$k]['min_age']."Max age".$agr_wise_data[$k]['max_age'],
        "message" => "Min age".$agr_wise_data[$k]['min_age']."Max age".$agr_wise_data[$k]['max_age']
    ];
                                                                // return [
                                                                //     "Min age" => $agr_wise_data[$k]['min_age'],
                                                                //     "Max age" => $agr_wise_data[$k]['max_age'],
                                                                // ];
}
}
}

}
}
}
}          
else{
    $sum_insure = $non_des['sum_insured'];
                       // $sum_premium = $non_des['PremiumServiceTax'];
    if ($non_des['addition_premium'] == 0) {

        $years_days = cal_days_in_year($current_year);
        $per_day_premium = $non_des['PremiumServiceTax'] / $policy_date_days;
        $premium = $policy_days_diff * $per_day_premium;
                             //$premium = round($premium);
        $sum_premium = $premium;


        if ($non_des['sum_insured_type'] == 'individual' || $non_des['sum_insured_type'] == 'familyIndividual') {
            if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
                if ($non_des['min_age'] <= $age[$i] || $non_des['max_age'] >= $age[$i]) {
                    $employee_policy_mem_sum_premium = 0;
                    $employer_policy_mem_sum_premium = 0;
                    $sum_premium = 0;
                                        //$employer_policy_mem_sum_premium = $premium;
                } else {
                    return [
                        "status" => "Min age".$non_des[$j]['min_age']."Max age".$non_des[$j]['max_age'],
                        "message" => "Min age".$non_des[$j]['min_age']."Max age".$non_des[$j]['max_age']
                    ];
                                        // return [
                                        //     "Min age" => $non_des[$j]['min_age'],
                                        //     "Max age" => $non_des[$j]['max_age'],
                                        // ];
                }
            } else {
                if ($non_des['min_age'] <= $age[$i] || $non_des['max_age'] >= $age[$i]) {
                    if ($disable_child[$i] == "disable_child") {
                        $special = explode(",", $special);
                        $employee_policy_mem_sum_premium = ($premium * $special[0]) / 100;
                        $employer_policy_mem_sum_premium = ($premium * $special[1]) / 100;
                    } else if ($age[$i] >= 25 && $age_type[$i] == "years" && $disable_child[$i] == "unmarried_child") {
                        $unmarried = explode(",", $unmarried);
                        $employee_policy_mem_sum_premium = ($premium * $unmarried[0]) / 100;
                        $employer_policy_mem_sum_premium = ($premium * $unmarried[1]) / 100;
                    } else {
                        $employee_policy_mem_sum_premium = 0;
                        $employer_policy_mem_sum_premium = $premium;
                    }
                }
            }
        } else {
            $unmarried = $non_des['unmarried_child_contri'];
            $special = $non_des['special_child_contri'];
            $sum_insure = $non_des['sum_insured'];
                                //$sum_premium = $non_des['p_premuim'];
            if ($family_members_id[$i] == 2 || $family_members_id[$i] == 3) {
                if ($age[$i] >= 25 && $age_type[$i] == "years" && $disable_child[$i] == "unmarried_child") {
                    $unmarried = explode(",", $unmarried);
                    $employee_policy_mem_sum_premium = ($premium * $unmarried[0]) / 100;
                    $employer_policy_mem_sum_premium = ($premium * $unmarried[1]) / 100;
                } else if ($age[$i] > 0 && $disable_child[$i] == "disable_child") {
                    $special = explode(",", $special);
                    $employee_policy_mem_sum_premium = ($premium * $special[0]) / 100;
                    $employer_policy_mem_sum_premium = ($premium * $special[1]) / 100;
                } else {
                    $employee_policy_mem_sum_premium = 0;
                    $employer_policy_mem_sum_premium = $premium;
                }
            } else {
                if ($non_des['min_age'] <= $age[$i] || $non_des['max_age'] >= $age[$i]) {
                                        //$employee_policy_mem_sum_premium = 0;
                    $employee_policy_mem_sum_premium = 0;
                    $employer_policy_mem_sum_premium = 0;
                    $sum_premium = 0;
                                        //$employer_policy_mem_sum_premium = $premium;
                } else {
                    return [
                        "status" => "Min age".$non_des['min_age']."Max age".$non_des['max_age'],
                        "message" => "Min age".$non_des['min_age']."Max age".$non_des['max_age']
                    ];
                                        // return [
                                        //     "Min age" => $non_des['min_age'],
                                        //     "Max age" => $non_des['max_age'],
                                        // ];
                }
            }
        }
    } 
    else {
        $years_days = cal_days_in_year($current_year);
        $per_day_premium = $non_des['PremiumServiceTax'] / $policy_date_days;
        $premium = $policy_days_diff * $per_day_premium;
                             //$premium = round($premium);
        $sum_premium = $premium;

        if ($non_des['sum_insured_type'] == 'individual' || $non_des['sum_insured_type'] == 'familyIndividual') {
            $sum_insure = $non_des['sum_insured'];
            if ($family_members_id[$i] == 1 || $family_members_id[$i] == 2 || $family_members_id[$i] == 3 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
                if ($non_des['min_age'] <= $age[$i] || $non_des['max_age'] >= $age[$i]) {
                    if ($non_des['relation_id'] == 2 || $non_des['relation_id'] == 3 || $non_des['relation_id'] == 1 || $non_des['relation_id'] == 4 || $non_des['relation_id'] == 5 || $non_des['relation_id'] == 6 || $non_des['relation_id'] == 7) {
                        $employee_policy_mem_sum_premium = ($premium * $non_des['employee_contri']) / 100;
                        $employer_policy_mem_sum_premium = ($premium * $non_des['employer_contri']) / 100;
                    } else {
                        $sum_premium = $premium;
                        $employee_policy_mem_sum_premium = 0;
                        $employer_policy_mem_sum_premium  = $premium;
                    }
                } else {
                    return [
                        "status" => "Min age".$non_des['min_age']."Max age".$non_des['max_age'],
                        "message" => "Min age".$non_des['min_age']."Max age".$non_des['max_age']
                    ];
                                        // return [
                                        //     "Min age" => $non_des['min_age'],
                                        //     "Max age" => $non_des['max_age'],
                                        // ];
                }
            }
        } else {
            $sum_insure = $non_des['sum_insured'];
            if ($family_members_id[$i] == 1 || $family_members_id[$i] == 2 || $family_members_id[$i] == 3 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
                if ($non_des['min_age'] <= $age[$i] || $non_des['max_age'] >= $age[$i]) {
                    if ($non_des['relation_id'] == 2 || $non_des['relation_id'] == 3 || $non_des['relation_id'] == 1 || $non_des['relation_id'] == 4 || $non_des['relation_id'] == 5 || $non_des['relation_id'] == 6 || $non_des['relation_id'] == 7) {
                        $employee_policy_mem_sum_premium = ($premium * $non_des['employee_contri']) / 100;
                        $employer_policy_mem_sum_premium = ($premium * $non_des['employer_contri']) / 100;
                    } else {
                        $sum_premium = $premium;
                        $employee_policy_mem_sum_premium = 0;
                        $employer_policy_mem_sum_premium  = $premium;
                    }
                } else {
                    return [
                        "status" => "Min age".$non_des['min_age']."Max age".$non_des['max_age'],
                        "message" => "Min age".$non_des['min_age']."Max age".$non_des['max_age']
                    ];
                                        // return [
                                        //     "Min age" => $non_des['min_age'],
                                        //     "Max age" => $non_des['max_age'],
                                        // ];
                }
            }
        }
    }
}
}
}
else { 
    if ($designation_res && count($designation_res) > 0) {
        $arra[$i] = 0;
        if ($designation_res['policy_sub_type_id'] == 1) {
            $sum_insure = $designation_res['sum_insured'];
            $sum_premium = $designation_res['PremiumServiceTax'];
            if ($designation_res['addition_premium'] == 0) {
                $years_days = cal_days_in_year($current_year);
                $per_day_premium = $designation_res['p_premuim'] / $policy_date_days;
                $premium = $policy_days_diff * $per_day_premium;
                             //$premium = round($premium);
                $sum_premium = $premium;
                if ($designation_res['sum_insured_type'] == 'individual' || $designation_res['sum_insured_type'] == 'familyIndividual') {
                    if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
                        if ($designation_res['min_age'] <= $age[$i] || $designation_res['max_age'] >= $age[$i]) {
                            $employee_policy_mem_sum_premium = 0;
                            $employer_policy_mem_sum_premium = 0;
                            $sum_premium = 0;
                                       // $employer_policy_mem_sum_premium = $premium;
                        } else {
                            return [
                                "status" => "Min age".$designation_res[$j]['min_age']."Max age".$designation_res[$j]['max_age'],
                                "message" => "Min age".$designation_res[$j]['min_age']."Max age".$designation_res[$j]['max_age']
                            ];
                                        // return [
                                        //     "Min age" => $designation_res[$j]['min_age'],
                                        //     "Max age" => $designation_res[$j]['max_age'],
                                        // ];
                        }
                    } else {
                        if ($designation_res['min_age'] <= $age[$i] || $designation_res['max_age'] >= $age[$i]) {
                            if ($disable_child[$i] == "disable_child") {
                                $special = explode(",", $special);
                                $employee_policy_mem_sum_premium = ($premium * $special[0]) / 100;
                                $employer_policy_mem_sum_premium = ($premium * $special[1]) / 100;
                            } else if ($age[$i] >= 25 && $age_type[$i] == "years" && $disable_child[$i] == "unmarried_child") {
                                $unmarried = explode(",", $unmarried);
                                $employee_policy_mem_sum_premium = ($premium * $unmarried[0]) / 100;
                                $employer_policy_mem_sum_premium = ($premium * $unmarried[1]) / 100;
                            } else {
                                $employee_policy_mem_sum_premium = 0;
                                $employer_policy_mem_sum_premium = $premium;
                            }
                        }
                    }
                } else {
                    $unmarried = $designation_res['unmarried_child_contri'];
                    $special = $designation_res['special_child_contri'];
                    $sum_insure = $designation_res['sum_insured'];

                    if ($family_members_id[$i] == 2 || $family_members_id[$i] == 3) {
                        if ($age[$i] >= 25 && $age_type[$i] == "years" && $disable_child[$i] == "unmarried_child") {
                            $unmarried = explode(",", $unmarried);
                            $employee_policy_mem_sum_premium = ($premium * $unmarried[0]) / 100;
                            $employer_policy_mem_sum_premium = ($premium * $unmarried[1]) / 100;
                        } else if ($age[$i] > 0 && $disable_child[$i] == "disable_child") {
                            $special = explode(",", $special);
                            $employee_policy_mem_sum_premium = ($premium * $special[0]) / 100;
                            $employer_policy_mem_sum_premium = ($premium * $special[1]) / 100;
                        } else {
                            $employee_policy_mem_sum_premium = 0;
                            $employer_policy_mem_sum_premium = $premium;
                        }
                    } else {
                        if ($designation_res['min_age'] <= $age[$i] || $designation_res['max_age'] >= $age[$i]) {

                                        //$employer_policy_mem_sum_premium = $premium;
                            $employee_policy_mem_sum_premium = 0;
                            $employer_policy_mem_sum_premium = 0;
                            $sum_premium = 0;
                        } else {
                            return [
                                "status" => "Min age".$designation_res['min_age']."Max age".$designation_res['max_age'],
                                "message" => "Min age".$designation_res['min_age']."Max age".$designation_res['max_age'],
                            ];
                                        // return [
                                        //     "Min age" => $designation_res['min_age'],
                                        //     "Max age" => $designation_res['max_age'],
                                        // ];
                        }
                    }
                }
            } else {
                $years_days = cal_days_in_year($current_year);
                $per_day_premium = $designation_res['topremium'] / $policy_date_days;
                $premium = $policy_days_diff * $per_day_premium;
                             //$premium = round($premium);
                $sum_premium = $premium;
                if ($designation_res['sum_insured_type'] == 'individual' || $designation_res['sum_insured_type'] == 'familyIndividual') {
                    $sum_insure = $designation_res['sum_insured'];
                    if ($family_members_id[$i] == 1 || $family_members_id[$i] == 2 || $family_members_id[$i] == 3 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
                        if ($designation_res['min_age'] <= $age[$i] || $designation_res['max_age'] >= $age[$i]) {
                            if ($designation_res['relation_id'] == 2 || $designation_res['relation_id'] == 3 || $designation_res['relation_id'] == 1 || $designation_res['relation_id'] == 4 || $designation_res['relation_id'] == 5 || $designation_res['relation_id'] == 6 || $designation_res['relation_id'] == 7) {
                                $employee_policy_mem_sum_premium = ($premium * $designation_res['employee_contri']) / 100;
                                $employer_policy_mem_sum_premium = ($premium * $designation_res['employer_contri']) / 100;
                            } else {
                                $sum_premium = $premium;
                                $employee_policy_mem_sum_premium = 0;
                                $employer_policy_mem_sum_premium = $premium;
                            }
                        } else {
                            return [
                                "status" => "Min age".$designation_res['min_age']."Max age".$designation_res['max_age'],
                                "message" => "Min age".$designation_res['min_age']."Max age".$designation_res['max_age'],
                            ];
                                        // return [
                                        //     "Min age" => $designation_res['min_age'],
                                        //     "Max age" => $designation_res['max_age'],
                                        // ];
                        }
                    }
                } else {

                    $sum_insure = $designation_res['sum_insured'];
                    if ($family_members_id[$i] == 1 || $family_members_id[$i] == 2 || $family_members_id[$i] == 3 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
                        if ($designation_res['min_age'] <= $age[$i] || $designation_res['max_age'] >= $age[$i]) {
                            if ($designation_res['relation_id'] == 2 || $designation_res['relation_id'] == 3 || $designation_res['relation_id'] == 1 || $designation_res['relation_id'] == 4 || $designation_res['relation_id'] == 5 || $designation_res['relation_id'] == 6 || $designation_res['relation_id'] == 7) {
                             $employee_policy_mem_sum_premium = ($premium * $designation_res['employee_contri']) / 100;
                             $employer_policy_mem_sum_premium = ($premium * $designation_res['employer_contri']) / 100;
                         } else {
                            $sum_premium = $premium;
                            $employee_policy_mem_sum_premium = 0;
                            $employer_policy_mem_sum_premium = $premium;
                        }
                    } else {
                        return [
                            "status" => "Min age".$designation_res['min_age']."Max age".$designation_res['max_age'],
                            "message" => "Min age".$designation_res['min_age']."Max age".$designation_res['max_age'],
                        ];
                                        // return [
                                        //     "Min age" => $designation_res['min_age'],
                                        //     "Max age" => $designation_res['max_age'],
                                        // ];
                    }
                }
            }
        }
    }
}
}
} 
else {

    if ($non_des['applicable_for_designation_id'] == "") {

        if ($non_des['policy_sub_type_id'] == 1) {
           if ($non_des['premium_type'] == 'memberAge') {

            for ($k = 0; $k < count($agr_wise_data); $k++) {
                $min_age = explode("-", $agr_wise_data[$k]['policy_age']);

                if ($age[$i] >= $min_age[0] && $age[$i] <= $min_age[1]) {

                    $sum_insure = $agr_wise_data[$k]['pca_sumInsured'];
                    $sum_premium = $agr_wise_data[$k]['pcapremium']; 


                    if ($agr_wise_data[$k]['addition_premium'] == 1) {

                        if ($agr_wise_data[$k]['sum_insured_type'] == 'individual' || $agr_wise_data[$k]['sum_insured_type'] == 'familyIndividual') {

                            if ($family_members_id[$i] == 0) { 

                                if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
                                    $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100; 
                                    $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
                                } else {
                                    return [
                                        "status" => "Min age".$non_des['min_age']."Max age".$non_des['max_age'],
                                        "message" => "Min age".$non_des['min_age']."Max age".$non_des['max_age'],
                                    ];
                                                                // return [
                                                                //     "Min age" => $non_des['min_age'],
                                                                //     "Max age" => $non_des['max_age'],
                                                                // ];
                                }
                            } else if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
                                if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
                                    $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                                    $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
                                } 
                            }else {

                                if($agr_wise_data[$k]['special_child_check'] == 1 || $agr_wise_data[$k]['unmarried_child_check'] == 1){
                                    if ($agr_wise_data[$k]['special_child_check'] == 1) {
                                        $special = explode(",", $special);
                                        $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                                        $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                                    } else if ($age >= 25 && $age_type == "years" && $agr_wise_data[$k]['unmarried_child_check'] == 1) {
                                        $unmarried = explode(",", $unmarried);
                                        $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                                        $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                                    } else {
                                        $employee_policy_mem_sum_premium = 0;
                                        $employer_policy_mem_sum_premium = $sum_premium;
                                    }
                                }
                                else {
                                    if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
                                        $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                                        $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
                                    } 
                                }
                            }
                        } 
                        else {
                            $unmarried = $agr_wise_data[$k]['unmarried_child_contri'];
                            $special = $agr_wise_data[$k]['special_child_contri'];

                            if ($family_members_id[$i] == 2 || $family_members_id[$i] == 3) {

                             if($agr_wise_data[$k]['special_child_check'] == 1 || $agr_wise_data[$k]['unmarried_child_check'] == 1){
                                if ($age >= 25 && $age_type == "years" && $agr_wise_data[$k]['unmarried_child_check'] == 1) {
                                    $unmarried = explode(",", $unmarried);
                                    $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                                    $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                                } else if ($age > 0 && $agr_wise_data[$k]['special_child_check'] == 1) {
                                    $special = explode(",", $special);
                                    $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                                    $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                                } else {
                                    $employee_policy_mem_sum_premium = 0;
                                    $employer_policy_mem_sum_premium = $sum_premium;
                                }
                            }
                            else {

                                if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
                                    $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                                    $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
                                } 
                            }

                        } else if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7 || $family_members_id[$i] == 0 ) {
                         if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
                            $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                            $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
                        }
                    } 
                }
            } 

        }
    }


} 
else if($non_des['premium_type'] == 'grade'){


    $sum_premium = 0;
    $sum_insured = 0;
    for ($i = 0; $i < count($grade_wise_data); $i++) {

        if ($grade_wise_data[$i]['addition_premium'] == 0) {
            $sum_insured = $grade_wise_data[$i]['pcg_sumInsured'];
            $sum_premium = $grade_wise_data[$i]['pcgpremium'];


            if ($grade_wise_data[$i]['sum_insured_type'] == 'individual' || $grade_wise_data[$i]['sum_insured_type'] == 'familyIndividual') {
                if ($family_members_id[$i] == 0) {
                    if ($grade_wise_data[$i]['min_age'] <= $age || $grade_wise_data[$i]['max_age'] >= $age) {
                        $employee_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employee_contri_percent']) / 100;
                        $employer_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employer_contri_percent']) / 100;
                    } else {
                        return [
                            "status" => "Min age".$grade_wise_data[$i]['min_age']."Max age".$grade_wise_data[$i]['max_age'],
                            "message" => "Min age".$grade_wise_data[$i]['min_age']."Max age".$grade_wise_data[$i]['max_age'],
                        ];
                                                        // return [
                                                        //     "Min age" => $grade_wise_data[$i]['min_age'],
                                                        //     "Max age" => $grade_wise_data[$i]['max_age'],
                                                        // ];
                    }
                } else if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
                    $sum_premium = 0;
                    $employee_policy_mem_sum_premium = 0;
                    $employer_policy_mem_sum_premium = 0;
                } else {
                    if ($grade_wise_data['min_age'] <= $age || $grade_wise_data['max_age'] >= $age) {
                     if($grade_wise_data[$i]['special_child_check'] == 1 || $grade_wise_data[$i]['unmarried_child_check'] == 1){
                        if ($grade_wise_data[$i]['special_child_check'] == 1) {
                            $special = explode(",", $special);
                            $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                            $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                        } else if ($age >= 25 && $age_type == "years" && $grade_wise_data[$i]['unmarried_child_check'] == 1) {
                            $unmarried = explode(",", $unmarried);
                            $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                            $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                        } else {
                            $employee_policy_mem_sum_premium = 0;
                            $employer_policy_mem_sum_premium = $sum_premium;
                        }
                    }
                    else {
                     $employee_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employee_contri_percent']) / 100;
                     $employer_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employer_contri_percent']) / 100;
                 }
             }
         }
     } else {
        $unmarried = $grade_wise_data[$i]['unmarried_child_contri'];
        $special = $grade_wise_data[$i]['special_child_contri'];
        $sum_insured = $grade_wise_data[$i]['sum_insured'];
                                                //$sum_premium = $non_des['p_premuim'];
        if ($family_members_id[$i] == 2 || $family_members_id[$i] == 3) {
         if($grade_wise_data[$i]['special_child_check'] == 1 || $grade_wise_data[$i]['unmarried_child_check'] == 1){
            if ($age >= 25 && $grade_wise_data[$i]['unmarried_child_check'] == 1) {
                $unmarried = explode(",", $unmarried);
                $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
            } else if ($age > 0 && $grade_wise_data[$i]['special_child_check'] == 1) {
                $special = explode(",", $special);
                $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
            } else {
                $employee_policy_mem_sum_premium = 0;
                $employer_policy_mem_sum_premium = $sum_premium;
            }
        }
        else {
         $employee_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employee_contri_percent']) / 100;
         $employer_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employer_contri_percent']) / 100;
     }
 } else if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
    $sum_premium = 0;
    $employee_policy_mem_sum_premium = 0;
    $employer_policy_mem_sum_premium = 0;
} else {
    if ($grade_wise_data['min_age'] <= $age || $grade_wise_data[$i]['max_age'] >= $age) {
        $employee_policy_mem_sum_premium = 0;
        $employer_policy_mem_sum_premium = $sum_premium;
    } else {
     return [
        "status" => "Min age".$grade_wise_data[$i]['min_age']."Max age".$grade_wise_data[$i]['max_age'],
        "message" => "Min age".$grade_wise_data[$i]['min_age']."Max age".$grade_wise_data[$i]['max_age'],
    ];
                                                        // return [
                                                        //     "Min age" => $grade_wise_data[$i]['min_age'],
                                                        //     "Max age" => $grade_wise_data[$i]['max_age'],
                                                        // ];
}
}
}
} else {
                                            // $sum_premium = $grade_wise_data[$i]['topremium'] ;
    $sum_premium = $grade_wise_data[$i]['topremium'];

    if ($grade_wise_data[$i]['sum_insured_type'] == 'individual' || $grade_wise_data[$i]['sum_insured_type'] == 'familyIndividual') {
        $sum_insured = $grade_wise_data[$i]['sum_insured'];
        if ($family_members_id[$i] == 0 || $family_members_id[$i] == 1 || $family_members_id[$i] == 2 || $family_members_id[$i] == 3 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
            if ($grade_wise_data[$i]['min_age'] <= $age || $grade_wise_data[$i]['max_age'] >= $age) {
                if ($grade_wise_data[$i]['relation_id'] == 1 || $grade_wise_data[$i]['relation_id'] == 0 || $grade_wise_data[$i]['relation_id'] == 2 || $grade_wise_data[$i]['relation_id'] == 3 || $grade_wise_data[$i]['relation_id'] == 4 || $grade_wise_data[$i]['relation_id'] == 5 || $grade_wise_data[$i]['relation_id'] == 6 || $grade_wise_data[$i]['relation_id'] == 7) {
                    $employee_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employee_contri']) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employer_contri']) / 100;
                } else {
                    $sum_premium = $sum_premium;
                    $employee_policy_mem_sum_premium = 0;
                    $employer_policy_mem_sum_premium = $sum_premium;
                }
            } else {
             return [
                "status" => "Min age".$grade_wise_data[$i]['min_age']."Max age".$grade_wise_data[$i]['max_age'],
                "message" => "Min age".$grade_wise_data[$i]['min_age']."Max age".$grade_wise_data[$i]['max_age'],
            ];
                                                        // return [
                                                        //     "Min age" => $grade_wise_data[$i]['min_age'],
                                                        //     "Max age" => $grade_wise_data[$i]['max_age'],
                                                        // ];
        }
    }
} else {
    $sum_insured = $grade_wise_data[$i]['sum_insured'];
    if ($family_members_id[$i] == 0 || $family_members_id[$i]== 1 || $family_members_id[$i] == 2 || $family_members_id[$i] == 3 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
        if ($grade_wise_data[$i]['min_age'] <= $age || $grade_wise_data[$i]['max_age'] >= $age) {
            if ($grade_wise_data[$i]['relation_id'] == 1 || $grade_wise_data[$i]['relation_id'] == 0 || $grade_wise_data[$i]['relation_id'] == 2 || $grade_wise_data[$i]['relation_id'] == 3 || $grade_wise_data[$i]['relation_id'] == 4 || $grade_wise_data[$i]['relation_id'] == 5 || $grade_wise_data[$i]['relation_id'] == 6 || $grade_wise_data[$i]['relation_id'] == 7) {
                $employee_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employee_contri']) / 100;
                $employer_policy_mem_sum_premium = ($sum_premium * $grade_wise_data[$i]['employer_contri']) / 100;
            } else {
                $sum_premium = $sum_premium;
                $employee_policy_mem_sum_premium = 0;
                $employer_policy_mem_sum_premium = $sum_premium;
            }
        } else {
         return [
            "status" => "Min age".$grade_wise_data[$i]['min_age']."Max age".$grade_wise_data[$i]['max_age'],
            "message" => "Min age".$grade_wise_data[$i]['min_age']."Max age".$grade_wise_data[$i]['max_age'],
        ];
                                                        // return [
                                                        //     "Min age" => $grade_wise_data[$i]['min_age'],
                                                        //     "Max age" => $grade_wise_data[$i]['max_age'],
                                                        // ];
    }
}
}
}
}

}              
else if ($non_des['premium_type'] == 'age') {

    for ($k = 0; $k < count($agr_wise_data); $k++) {
        $min_age = explode("-", $agr_wise_data[$k]['policy_age']);

        if ($age >= $min_age[0] && $age <= $min_age[1]) {
            $sum_insure = $agr_wise_data[$k]['pca_sumInsured'];
            $sum_premium = $agr_wise_data[$k]['pcapremium'];

            if ($agr_wise_data[$k]['addition_premium'] == 0) {
                if ($agr_wise_data[$k]['sum_insured_type'] == 'individual' || $agr_wise_data[$k]['sum_insured_type'] == 'familyIndividual') {
                    if ($family_members_id[$i] == 0) {
                        if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
                            $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                            $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
                        } else {
                         return [
                            "status" => "Min age".$non_des['min_age']."Max age".$non_des['max_age'],
                            "message" => "Min age".$non_des['min_age']."Max age".$non_des['max_age'],
                        ];
                                                                // return [
                                                                //     "Min age" => $non_des['min_age'],
                                                                //     "Max age" => $non_des['max_age'],
                                                                // ];
                    }
                } else if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
                   $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                   $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
               } else {
                 if($agr_wise_data[$k]['special_child_check'] == 1 || $agr_wise_data[$k]['unmarried_child_check'] == 1){
                    if ($agr_wise_data[$k]['special_child_check'] == 1) {
                        $special = explode(",", $special);
                        $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                        $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                    } else if ($age >= 25 && $age_type == "years" && $agr_wise_data[$k]['unmarried_child_check'] == 1) {
                        $unmarried = explode(",", $unmarried);
                        $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                        $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                    } else {
                        $employee_policy_mem_sum_premium = 0;
                        $employer_policy_mem_sum_premium = $sum_premium;
                    }
                }
                else {
                   $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                   $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
               }
           }
       } 
       else {
        $unmarried = $agr_wise_data[$k]['unmarried_child_contri'];
        $special = $agr_wise_data[$k]['special_child_contri'];

        if ($family_members_id[$i] == 2 || $family_members_id[$i] == 3) {
            if($agr_wise_data[$k]['special_child_check'] == 1 || $agr_wise_data[$k]['unmarried_child_check'] == 1){
                if ($age >= 25 && $age_type == "years" && $agr_wise_data[$k]['unmarried_child_check'] == 1) {
                    $unmarried = explode(",", $unmarried);
                    $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                } else if ($age > 0 && $agr_wise_data[$k]['special_child_check'] == 1) {
                    $special = explode(",", $special);
                    $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                } else {
                    $employee_policy_mem_sum_premium = 0;
                    $employer_policy_mem_sum_premium = $sum_premium;
                }
            }
            else {
               $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
               $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
           }
       } else if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
           $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
           $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
       } else {
        if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
           $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
           $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
       } else {
        return [
            "status" => "Min age".$agr_wise_data[$k]['min_age']."Max age".$agr_wise_data[$k]['max_age'],
            "message" => "Min age".$agr_wise_data[$k]['min_age']."Max age".$agr_wise_data[$k]['max_age'],
        ];
                                                                // return [
                                                                //     "Min age" => $agr_wise_data[$k]['min_age'],
                                                                //     "Max age" => $agr_wise_data[$k]['max_age'],
                                                                // ];
    }
}
}
} 
else {
    if ($agr_wise_data[$k]['sum_insured_type'] == 'individual' || $agr_wise_data[$k]['sum_insured_type'] == 'familyIndividual') {
        if ($family_member_relation == 0) {
            if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
                $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
            } else {
                return [
                    "status" => "Min age".$non_des['min_age']."Max age".$non_des['max_age'],
                    "message" => "Min age".$non_des['min_age']."Max age".$non_des['max_age'],
                ];
                                                                // return [
                                                                //     "Min age" => $non_des['min_age'],
                                                                //     "Max age" => $non_des['max_age'],
                                                                // ];
            }
        } else if ($family_member_relation == 1 || $family_member_relation == 4 || $family_member_relation == 5 || $family_member_relation == 6 || $family_member_relation == 7) {
            if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
                $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
            } 
        }else {
            if($agr_wise_data[$k]['special_child_check'] == 1 || $agr_wise_data[$k]['unmarried_child_check'] == 1){
                if ($agr_wise_data[$k]['special_child_check'] == 1) {
                    $special = explode(",", $special);
                    $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                } else if ($age >= 25 && $age_type == "years" && $agr_wise_data[$k]['unmarried_child_check'] == 1) {
                    $unmarried = explode(",", $unmarried);
                    $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                } else {
                    $employee_policy_mem_sum_premium = 0;
                    $employer_policy_mem_sum_premium = $sum_premium;
                }
            }
            else {
                if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
                    $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
                } 
            }
        }
    }
    else {

        $unmarried = $agr_wise_data[$k]['unmarried_child_contri'];
        $special = $agr_wise_data[$k]['special_child_contri'];

        if ($family_member_relation== 2 || $family_member_relation == 3) {
         if($agr_wise_data[$k]['special_child_check'] == 1 || $agr_wise_data[$k]['unmarried_child_check'] == 1){

             if ($age >= 25 && $age_type == "years" && $agr_wise_data[$k]['unmarried_child_check'] == 1) {
                $unmarried = explode(",", $unmarried);
                $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
            } else if ($age > 0 && $agr_wise_data[$k]['special_child_check'] == 1) {
                $special = explode(",", $special);
                $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
            } else {
                $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
            }
        }
        else {
         $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
         $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
     }
 } else if ($family_member_relation == 1 || $family_member_relation == 4 || $family_member_relation == 5 || $family_member_relation == 6 || $family_member_relation == 7) {
    $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
    $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
} else {
    if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
       $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
       $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
   } else {
    return [
        "status" => "Min age".$agr_wise_data[$k]['min_age']."Max age".$agr_wise_data[$k]['max_age'],
        "message" => "Min age".$agr_wise_data[$k]['min_age']."Max age".$agr_wise_data[$k]['max_age'],
    ];
                                                                // return [
                                                                //     "Min age" => $agr_wise_data[$k]['min_age'],
                                                                //     "Max age" => $agr_wise_data[$k]['max_age'],
                                                                // ];
}
}
}

}
}
}
} 

else{
    $sum_insure = $non_des['sum_insured'];
    $sum_premium = $non_des['PremiumServiceTax']; 
    if ($non_des['addition_premium'] == 0) {
        if ($non_des['sum_insured_type'] == 'individual' || $non_des['sum_insured_type'] == 'familyIndividual') {

            if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
                if ($non_des['min_age'] <= $age[$i] || $non_des['max_age'] >= $age[$i]) {

                    $employee_policy_mem_sum_premium = 0;
                    $employer_policy_mem_sum_premium = 0;
                    $sum_premium = 0;
                                       // $employer_policy_mem_sum_premium = $sum_premium;
                } else {
                    return [
                        "status" => "Min age".$non_des[$j]['min_age']."Max age".$non_des[$j]['max_age'],
                        "message" => "Min age".$non_des[$j]['min_age']."Max age".$non_des[$j]['max_age'],
                    ];
                                        // return [
                                        //     "Min age" => $non_des[$j]['min_age'],
                                        //     "Max age" => $non_des[$j]['max_age'],
                                        // ];
                }
            } else {
                if ($non_des['min_age'] <= $age[$i] || $non_des['max_age'] >= $age[$i]) {
                    if ($disable_child[$i] == "disable_child") {
                        $special = explode(",", $special);
                        $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                        $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                    } else if ($age[$i] >= 25 && $age_type[$i] == "years" && $disable_child[$i] == "unmarried_child") {
                        $unmarried = explode(",", $unmarried);
                        $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                        $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                    } else {
                        $employee_policy_mem_sum_premium = 0;
                        $employer_policy_mem_sum_premium = $sum_premium;
                    }
                }
            }
        } else {
            $unmarried = $non_des['unmarried_child_contri'];
            $special = $non_des['special_child_contri'];
            $sum_insure = $non_des['sum_insured'];
            $sum_premium = $non_des['PremiumServiceTax'];

            if ($family_members_id[$i] == 2 || $family_members_id[$i] == 3) {
                if ($age[$i] >= 25 && $age_type[$i] == "years" && $disable_child[$i] == "unmarried_child") {
                    $unmarried = explode(",", $unmarried);
                    $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                } else if ($age[$i] > 0 && $disable_child[$i] == "disable_child") {
                    $special = explode(",", $special);
                    $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                } else {
                    $employee_policy_mem_sum_premium = 0;
                    $employer_policy_mem_sum_premium = $sum_premium;
                }
            } else {
                if ($non_des['min_age'] <= $age[$i] || $non_des['max_age'] >= $age[$i]) {
                   $employee_policy_mem_sum_premium = 0;
                   $employer_policy_mem_sum_premium = 0;
                   $sum_premium = 0;
                                        //$employer_policy_mem_sum_premium = $sum_premium;
               } else {
                return [
                    "status" => "Min age".$non_des['min_age']."Max age".$non_des['max_age'],
                    "message" => "Min age".$non_des['min_age']."Max age".$non_des['max_age'],
                ];
                                        // return [
                                        //     "Min age" => $non_des['min_age'],
                                        //     "Max age" => $non_des['max_age'],
                                        // ];
            }
        }
    }
} 
else {
    if ($non_des['sum_insured_type'] == 'individual' || $non_des['sum_insured_type'] == 'familyIndividual') {
        $sum_insure = $non_des['sum_insured'];
        if ($family_members_id[$i] == 1 || $family_members_id[$i] == 2 || $family_members_id[$i] == 3 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
            if ($non_des['min_age'] <= $age[$i] || $non_des['max_age'] >= $age[$i]) {
                if ($non_des['relation_id'] == 2 || $non_des['relation_id'] == 3 || $non_des['relation_id'] == 1 || $non_des['relation_id'] == 4 || $non_des['relation_id'] == 5 || $non_des['relation_id'] == 6 || $non_des['relation_id'] == 7) {
                    $employee_policy_mem_sum_premium = ($non_des['topremium'] * $non_des['employee_contri']) / 100;
                    $employer_policy_mem_sum_premium = ($non_des['topremium'] * $non_des['employer_contri']) / 100;
                } else {
                    $sum_premium = $non_des['PremiumServiceTax'];
                }
            } else {
                return [
                    "status" => "Min age".$non_des['min_age']."Max age".$non_des['max_age'],
                    "message" => "Min age".$non_des['min_age']."Max age".$non_des['max_age'],
                ];
                                        // return [
                                        //     "Min age" => $non_des['min_age'],
                                        //     "Max age" => $non_des['max_age'],
                                        // ];
            }
        }
    } else {
        $sum_insure = $non_des['sum_insured'];
        if ($family_members_id[$i] == 1 || $family_members_id[$i] == 2 || $family_members_id[$i] == 3 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
            if ($non_des['min_age'] <= $age[$i] || $non_des['max_age'] >= $age[$i]) {
                if ($non_des['relation_id'] == 2 || $non_des['relation_id'] == 3 || $non_des['relation_id'] == 1 || $non_des['relation_id'] == 4 || $non_des['relation_id'] == 5 || $non_des['relation_id'] == 6 || $non_des['relation_id'] == 7) {
                    $employee_policy_mem_sum_premium = ($non_des['topremium'] * $non_des['employee_contri']) / 100;
                    $employer_policy_mem_sum_premium = ($non_des['topremium'] * $non_des['employer_contri']) / 100;
                } else {
                    $sum_premium = $non_des['PremiumServiceTax'];
                }
            } else {
                return [
                    "status" => "Min age".$non_des['min_age']."Max age".$non_des['max_age'],
                    "message" => "Min age".$non_des['min_age']."Max age".$non_des['max_age'],
                ];
                                        // return [
                                        //     "Min age" => $non_des['min_age'],
                                        //     "Max age" => $non_des['max_age'],
                                        // ];
            }
        }
    }
}
}
}
}


else {   
    if ($designation_res && count($designation_res) > 0) {
        $arra[$i] = 0;
        if ($designation_res['policy_sub_type_id'] == 1) {
            $sum_insure = $designation_res['sum_insured'];
            $sum_premium = $designation_res['PremiumServiceTax'];
            if ($designation_res['addition_premium'] == 0) {
                if ($designation_res['sum_insured_type'] == 'individual' || $designation_res['sum_insured_type'] == 'familyIndividual') {

                    if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
                        if ($designation_res['min_age'] <= $age[$i] || $designation_res['max_age'] >= $age[$i]) {
                           $employee_policy_mem_sum_premium = 0;
                           $employer_policy_mem_sum_premium = 0;
                           $sum_premium = 0;
                                        //$employer_policy_mem_sum_premium = $sum_premium;
                       } else {
                        return [
                            "status" => "Min age".$designation_res[$j]['min_age']."Max age".$designation_res[$j]['max_age'],
                            "message" => "Min age".$designation_res[$j]['min_age']."Max age".$designation_res[$j]['max_age'],
                        ];
                                        // return [
                                        //     "Min age" => $designation_res[$j]['min_age'],
                                        //     "Max age" => $designation_res[$j]['max_age'],
                                        // ];
                    }
                } else {
                    if ($designation_res['min_age'] <= $age[$i] || $designation_res['max_age'] >= $age[$i]) {
                        if ($disable_child[$i] == "disable_child") {
                            $special = explode(",", $special);
                            $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                            $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                        } else if ($age[$i] >= 25 && $age_type[$i] == "years" && $disable_child[$i] == "unmarried_child") {
                            $unmarried = explode(",", $unmarried);
                            $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                            $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                        } else {
                            $employee_policy_mem_sum_premium = 0;
                            $employer_policy_mem_sum_premium = $sum_premium;
                        }
                    }
                }
            } else {
                $unmarried = $designation_res['unmarried_child_contri'];
                $special = $designation_res['special_child_contri'];
                $sum_insure = $designation_res['sum_insured'];
                $sum_premium = $designation_res['PremiumServiceTax'];

                if ($family_members_id[$i] == 2 || $family_members_id[$i] == 3) {
                    if ($age[$i] >= 25 && $age_type[$i] == "years" && $disable_child[$i] == "unmarried_child") {
                        $unmarried = explode(",", $unmarried);
                        $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                        $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                    } else if ($age[$i] > 0 && $disable_child[$i] == "disable_child") {
                        $special = explode(",", $special);
                        $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                        $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                    } else {
                        $employee_policy_mem_sum_premium = 0;
                        $employer_policy_mem_sum_premium = $sum_premium;
                    }
                } else {
                    if ($designation_res['min_age'] <= $age[$i] || $designation_res['max_age'] >= $age[$i]) {
                        $employee_policy_mem_sum_premium = 0;
                        $employer_policy_mem_sum_premium = 0;
                        $sum_premium = 0;
                                        //$employer_policy_mem_sum_premium = $sum_premium;
                    } else {
                        return [
                            "status" => "Min age".$designation_res['min_age']."Max age".$designation_res['max_age'],
                            "message" => "Min age".$designation_res['min_age']."Max age".$designation_res['max_age'],
                        ];
                                        // return [
                                        //     "Min age" => $designation_res['min_age'],
                                        //     "Max age" => $designation_res['max_age'],
                                        // ];
                    }
                }
            }
        } else {
            if ($designation_res['sum_insured_type'] == 'individual' || $designation_res['sum_insured_type'] == 'familyIndividual') {
                $sum_insure = $designation_res['sum_insured'];
                if ($family_members_id[$i] == 1 || $family_members_id[$i] == 2 || $family_members_id[$i] == 3 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
                    if ($designation_res['min_age'] <= $age[$i] || $designation_res['max_age'] >= $age[$i]) {
                        if ($designation_res['relation_id'] == 2 || $designation_res['relation_id'] == 3 || $designation_res['relation_id'] == 1 || $designation_res['relation_id'] == 4 || $designation_res['relation_id'] == 5 || $designation_res['relation_id'] == 6 || $designation_res['relation_id'] == 7) {
                            $employee_policy_mem_sum_premium = ($designation_res['topremium'] * $designation_res['employee_contri']) / 100;
                            $employer_policy_mem_sum_premium = ($designation_res['topremium'] * $designation_res['employer_contri']) / 100;
                        } else {
                            $sum_premium = $designation_res['PremiumServiceTax'];
                        }
                    } else {
                        return [
                            "status" => "Min age".$designation_res['min_age']."Max age".$designation_res['max_age'],
                            "message" => "Min age".$designation_res['min_age']."Max age".$designation_res['max_age'],
                        ];
                                        // return [
                                        //     "Min age" => $designation_res['min_age'],
                                        //     "Max age" => $designation_res['max_age'],
                                        // ];
                    }
                }
            } else {
                $sum_insure = $designation_res['sum_insured'];
                if ($family_members_id[$i] == 1 || $family_members_id[$i] == 2 || $family_members_id[$i] == 3 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
                    if ($designation_res['min_age'] <= $age[$i] || $designation_res['max_age'] >= $age[$i]) {
                        if ($designation_res['relation_id'] == 1 || $designation_res['relation_id'] == 2 || $designation_res['relation_id'] == 3 || $designation_res['relation_id'] == 4 || $designation_res['relation_id'] == 5 || $designation_res['relation_id'] == 6 || $designation_res['relation_id'] == 7) {
                            $employee_policy_mem_sum_premium = ($designation_res['topremium'] * $designation_res['employee_contri']) / 100;
                            $employer_policy_mem_sum_premium = ($designation_res['topremium'] * $designation_res['employer_contri']) / 100;
                        } else {
                            $sum_premium = $designation_res['PremiumServiceTax'];
                        }
                    } else {
                        return [
                            "status" => "Min age".$designation_res['min_age']."Max age".$designation_res['max_age'],
                            "message" => "Min age".$designation_res['min_age']."Max age".$designation_res['max_age'],
                        ];
                                        // return [
                                        //     "Min age" => $designation_res['min_age'],
                                        //     "Max age" => $designation_res['max_age'],
                                        // ];
                    }
                }
            }
        }
    }
}
}
}

$this->load->library('upload');
if ($family_members_id[$i] == 2 || $family_members_id[$i] == 3) {
    $same_name_of_kid = $this->db->select('COUNT(efd.fr_id) AS count_fr')
    ->from('employee_family_details AS efd, family_relation AS fr, employee_policy_member AS epm ')
    ->where('epm`.`family_relation_id`=`fr`.`family_relation_id')
    ->where('efd`.`family_id`=`fr`.`family_id')
    ->where('epm`.`policy_detail_id', $policyid[$i])
    ->where('epm`.`status` !=', 'Inactive')
    ->where('epm`.`family_relation_id', $op['family_relation_id'])
    ->where('efd.fr_id', $family_members_id[$i])
    ->where('fr.emp_id', $emp_id)
    ->get()->row();

    if ($disable_child[$i] == 'disable_child') {

        if (empty($_FILES)) {
            return [
                "status" => "Empty",
            ];
        }
        $path_parts = pathinfo($_FILES["files" . $i]["name"]);
        $extension = $path_parts['extension'];
                    //echo($extension);
        $config['max_filename'] = 0;
        $config['allowed_types'] = 'gif|png|jpg|jpeg|pdf';
        $config['file_name'] = 'files' . $i;
        if (!is_dir(APPPATH . '/resources/uploads/policy_member/' . $policyid[$i])) {
            mkdir(APPPATH . '/resources/uploads/policy_member/' . $policyid[$i], 0777, TRUE);
        }
        $config['upload_path'] = APPPATH . '/resources/uploads/policy_member/' . $policyid[$i] . '/';
        $config['max_size'] = '0';
        $this->upload->initialize($config);
        $this->upload->do_upload('files' . $i);
        if ($_FILES == '') {
            $path = '';
        } else {
            $path = '/application/resources/uploads/policy_member/' . $policyid[$i] . '/' . 'files' . $i . '.' . $extension;
        }
    }
    if ($same_name_of_kid->count_fr >= 1) {
        return [
            "status" => "Same child can not add",
            "message" => "Same child can not add"
        ];
    } else {
        if (!empty($op)) {

            $array5 = [
                'family_dob' => $family_date_birth[$i],
                'family_firstname' => $first_name[$i],
                'family_lastname' => $last_name[$i],
                'family_gender' => $family_gender[$i],
            ];
            $where = [
                'family_id' => $familyId[$i]
            ];

            $status = $this->db->UPDATE('employee_family_details', $array5, $where);
            $array2 = [
                'policy_detail_id' => $policyid[$i],
                'policy_mem_dob' => date('d-m-Y', (strtotime($family_date_birth[$i]))),
                'fr_id' => $family_members_id[$i],
                'family_relation_id' => $op['family_relation_id'],
                'family_id' => $op['family_id'],
                'policy_mem_gender' => $family_gender[$i],
                'age' => $age[$i],
                'age_type' => $age_type[$i],
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+1 years')),
                'policy_doc_path' => $path,
                'child_condition' => $disable_child[$i],
                'company_id' => $query2[0]['company_id'],
                'policy_mem_sum_insured' => $sum_insure,
                'employer_policy_mem_sum_premium' => $employer_policy_mem_sum_premium,
                'employee_policy_mem_sum_premium' => $employee_policy_mem_sum_premium,
                'policy_mem_sum_premium' => $sum_premium
            ];

            $status = $this->db->insert('employee_policy_member', $array2);
            $arr['status'] = "Dependent Member Added Successfully";
        } else {
            $array = [
                'family_dob' => date('d-m-Y', (strtotime($family_date_birth[$i]))),
                'fr_id' => $family_members_id[$i],
                'family_firstname' => $first_name[$i],
                'family_lastname' => $last_name[$i],
                'family_gender' => $family_gender[$i]
            ];

            $status = $this->db->insert('employee_family_details', $array);
            $family_id = $this->db->insert_id();
            $array4 = [
                'emp_id' => $emp_id,
                'family_id' => $family_id,
                'created_on' => date('d-m-Y')
            ];

            $status = $this->db->insert('family_relation', $array4);
            $insert_id = $this->db->insert_id();

            $array3 = [
                'policy_detail_id' => $policyid[$i],
                'policy_mem_dob' => date('d-m-Y', (strtotime($family_date_birth[$i]))),
                'fr_id' => $family_members_id[$i],
                'family_relation_id' => $insert_id,
                'family_id' => $family_id,
                'policy_mem_gender' => $family_gender[$i],
                'age' => $age[$i],
                'age_type' => $age_type[$i],
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+1 years')),
                'policy_doc_path' => $path,
                'child_condition' => $disable_child[$i],
                'company_id' => $query2[0]['company_id'],
                'policy_mem_sum_insured' => $sum_insure,
                'employer_policy_mem_sum_premium' => $employer_policy_mem_sum_premium,
                'employee_policy_mem_sum_premium' => $employee_policy_mem_sum_premium,
                'policy_mem_sum_premium' => $sum_premium
            ];
            $status = $this->db->insert('employee_policy_member', $array3);
            $arr['status'] = "Dependent Member Added Successfully";
//                          $result1 = $this->db->select('*')
//                                ->from('employee_flexi_benefit_transaction ')
//                                ->where('employee_flexi_benifit_id', 2)
//                                ->where('emp_id', $this->emp_id)
//                                ->get_compiled_select();
//                        $response = $this->db->query($result1)->row_array();
        }
        if (!empty($deduction_type)) {
            if (empty($pay_amount)) {
                $array5 = [
                    'emp_id' => $emp_id,
                    'fr_id' => $family_members_id[$i],
                    'employee_flexi_benifit_id' => ((in_array($family_members_id[$i], [4, 5, 6, 7])) ? 2 : null),
                    'deduction_type' => $deduction_type,
                    'transac_type' => ((in_array($family_members_id[$i], [4, 5, 6, 7])) ? 'C' : null),
                    'final_amount' => $final_amount,
                    'flex_amount' => $flex_amount,
                    'pay_amount' => 0,
                    'balance_amount' => $flex_amount,
                    'sum_insured' => $sum_insure,
                    'allocated_flag' => 'Y',
                    'confirmed_flag' => 'N',
                    'ip_address' => $this->input->ip_address(),
                    'allocated_updated_at' => date('Y-m-d H:i:s')
                ];
            } else if (empty($flex_amount)) {
                $array5 = [
                    'emp_id' => $emp_id,
                    'fr_id' => $family_members_id[$i],
                    'employee_flexi_benifit_id' => ((in_array($family_members_id[$i], [4, 5, 6, 7])) ? 2 : null),
                    'deduction_type' => $deduction_type,
                    'transac_type' => ((in_array($family_members_id[$i], [4, 5, 6, 7])) ? 'C' : null),
                    'final_amount' => $final_amount,
                    'flex_amount' => 0,
                    'pay_amount' => $pay_amount,
                    'balance_amount' => $pay_amount,
                    'sum_insured' => $sum_insure,
                    'allocated_flag' => 'Y',
                    'confirmed_flag' => 'N',
                    'ip_address' => $this->input->ip_address(),
                    'allocated_updated_at' => date('Y-m-d H:i:s')
                ];
            } else {
                $array5 = [
                    'emp_id' => $emp_id,
                    'fr_id' => $family_members_id[$i],
                    'employee_flexi_benifit_id' => ((in_array($family_members_id[$i], [4, 5, 6, 7])) ? 2 : null),
                    'deduction_type' => $deduction_type,
                    'transac_type' => ((in_array($family_members_id[$i], [4, 5, 6, 7])) ? 'C' : null),
                    'final_amount' => $final_amount,
                    'flex_amount' => $flex_amount,
                    'pay_amount' => $pay_amount,
                    'balance_amount' => $flex_amount,
                    'sum_insured' => $sum_insure,
                    'allocated_flag' => 'Y',
                    'confirmed_flag' => 'N',
                    'ip_address' => $this->input->ip_address(),
                    'allocated_updated_at' => date('Y-m-d H:i:s')
                ];
            }
            $status = $this->db->insert('employee_flexi_benefit_transaction', $array5);
        }
//                    }
//                        else 
//                        {
//                                $arr['msg'] = ["status" => "error"];
//                        }
    }
} 
else if ($family_members_id[$i] == 1 || $family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {

    $response = $this->db->select('COUNT(efd.fr_id) AS count_fr')
    ->from('employee_family_details AS efd, family_relation AS fr, employee_policy_member AS epm ')
    ->where('epm`.`family_relation_id`=`fr`.`family_relation_id')
    ->where('efd`.`family_id`=`fr`.`family_id')
    ->where("epm.status!=", 'Inactive')
                        //->where('epm`.`family_relation_id', $op['family_relation_id'])
    ->where('efd.fr_id', $family_members_id[$i])
    ->where('fr.emp_id', $emp_id)
    ->get_compiled_select();
    $res = $this->db->query($response)->row();
    $chk_adults = $this->db->select('COUNT(efd.fr_id) AS count_fr')
    ->from('employee_family_details AS efd, family_relation AS fr, employee_policy_member AS epm ')
    ->where('epm`.`family_relation_id`=`fr`.`family_relation_id')
    ->where('efd`.`family_id`=`fr`.`family_id')
    ->where("efd.fr_id in(1,4,5,6,7)")
    ->where("epm.status!=", 'Inactive')
    ->where("epm.policy_detail_id", $policyid[0])
    ->where('fr.emp_id', $emp_id)
    ->get()->result();

    if ($res->count_fr < 1) {

        if (!empty($op['fr_id'])) {

            $array5 = [
                'family_dob' => $family_date_birth[$i],
                'family_firstname' => $first_name[$i],
                'family_lastname' => $last_name[$i],
                'family_gender' => $family_gender[$i],
            ];
            $where = [
                'family_id' => $familyId[$i]
            ];
            $status = $this->db->update('employee_family_details', $array5, $where);
            $array2 = [
                'policy_detail_id' => $policyid[$i],
                'policy_mem_dob' => date('d-m-Y', (strtotime($family_date_birth[$i]))),
                'fr_id' => $family_members_id[$i],
                'family_relation_id' => $op['family_relation_id'],
                'family_id' => $op['family_id'],
                'policy_mem_gender' => $family_gender[$i],
                'company_id' => $query2[0]['company_id'],
                'age' => $age[$i],
                'age_type' => $age_type[$i],
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+1 years')),
                'policy_mem_sum_insured' => $sum_insure,
                'employer_policy_mem_sum_premium' => $employer_policy_mem_sum_premium,
                'employee_policy_mem_sum_premium' => $employee_policy_mem_sum_premium,
                'policy_mem_sum_premium' => $sum_premium
            ];

            $status = $this->db->insert('employee_policy_member', $array2);
            $arr['status'] = "Dependent Member Added Successfully";
        } else {
            $array = [
                'family_dob' => date('d-m-Y', (strtotime($family_date_birth[$i]))),
                'fr_id' => $family_members_id[$i],
                'family_firstname' => $first_name[$i],
                'family_lastname' => $last_name[$i],
                'family_gender' => $family_gender[$i]
            ];
            $status = $this->db->insert('employee_family_details', $array);
            $family_id = $this->db->insert_id();
            $array4 = [
                'emp_id' => $emp_id,
                'family_id' => $family_id,
                'created_on' => date('d-m-Y')
            ];
            $status = $this->db->insert('family_relation', $array4);
            $insert_id = $this->db->insert_id();
            $array3 = [
                'policy_detail_id' => $policyid[$i],
                'policy_mem_dob' => date('d-m-Y', (strtotime($family_date_birth[$i]))),
                'fr_id' => $family_members_id[$i],
                'family_relation_id' => $insert_id,
                'family_id' => $family_id,
                'policy_mem_gender' => $family_gender[$i],
                'company_id' => $query2[0]['company_id'],
                'age' => $age[$i],
                'age_type' => $age_type[$i],
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+1 years')),
                'policy_mem_sum_insured' => $sum_insure,
                'employer_policy_mem_sum_premium' => $employer_policy_mem_sum_premium,
                'employee_policy_mem_sum_premium' => $employee_policy_mem_sum_premium,
                'policy_mem_sum_premium' => $sum_premium
            ];

            $status = $this->db->insert('employee_policy_member', $array3);
            $arr['status'] = "Dependent Member Added Successfully";
        }
//                    if ($family_members_id[$i] == 4 || $family_members_id[$i] == 5 || $family_members_id[$i] == 6 || $family_members_id[$i] == 7) {
        $result1 = $this->db->select('*')
        ->from('employee_flexi_benefit_transaction ')
        ->where('employee_flexi_benifit_id', 2)
        ->where('emp_id', $emp_id)
        ->get_compiled_select();
        $response = $this->db->query($result1)->row_array();


        if (!empty($deduction_type)) {
            if (empty($pay_amount)) {
                $array5 = [
                    'emp_id' => $emp_id,
                    'fr_id' => $family_members_id[$i],
                    'employee_flexi_benifit_id' => ((in_array($family_members_id[$i], [4, 5, 6, 7])) ? 2 : null),
                    'deduction_type' => $deduction_type,
                    'transac_type' => ((in_array($family_members_id[$i], [4, 5, 6, 7])) ? 'C' : null),
                    'final_amount' => $final_amount,
                    'flex_amount' => $flex_amount,
                    'pay_amount' => 0,
                    'balance_amount' => $flex_amount,
                    'sum_insured' => $sum_insure,
                    'allocated_flag' => 'Y',
                    'confirmed_flag' => 'N',
                    'ip_address' => $this->input->ip_address(),
                    'allocated_updated_at' => date('Y-m-d H:i:s')
                ];
            } else if (empty($flex_amount)) {
                $array5 = [
                    'emp_id' => $emp_id,
                    'fr_id' => $family_members_id[$i],
                    'employee_flexi_benifit_id' => ((in_array($family_members_id[$i], [4, 5, 6, 7])) ? 2 : null),
                    'deduction_type' => $deduction_type,
                    'transac_type' => ((in_array($family_members_id[$i], [4, 5, 6, 7])) ? 'C' : null),
                    'final_amount' => $final_amount,
                    'flex_amount' => 0,
                    'pay_amount' => $pay_amount,
                    'balance_amount' => $pay_amount,
                    'sum_insured' => $sum_insure,
                    'allocated_flag' => 'Y',
                    'confirmed_flag' => 'N',
                    'ip_address' => $this->input->ip_address(),
                    'allocated_updated_at' => date('Y-m-d H:i:s')
                ];
            } else {
                $array5 = [
                    'emp_id' => $emp_id,
                    'fr_id' => $family_members_id[$i],
                    'employee_flexi_benifit_id' => ((in_array($family_members_id[$i], [4, 5, 6, 7])) ? 2 : null),
                    'deduction_type' => $deduction_type,
                    'transac_type' => ((in_array($family_members_id[$i], [4, 5, 6, 7])) ? 'C' : null),
                    'final_amount' => $final_amount,
                    'flex_amount' => $flex_amount,
                    'pay_amount' => $pay_amount,
                    'balance_amount' => $flex_amount,
                    'sum_insured' => $sum_insure,
                    'allocated_flag' => 'Y',
                    'confirmed_flag' => 'N',
                    'ip_address' => $this->input->ip_address(),
                    'allocated_updated_at' => date('Y-m-d H:i:s')
                ];
            }
            $status = $this->db->insert('employee_flexi_benefit_transaction', $array5);
        }
//                    }
//                        else 
//                        {
//                    $arr['msg'] = ["status" => "error"];
//                }
    } else {
        if (!empty($op["fr_id"])) {
            $array5 = [
                'family_dob' => $family_date_birth[$i],
                'family_firstname' => $first_name[$i],
                'family_lastname' => $last_name[$i],
                'family_gender' => $family_gender[$i],
            ];

            $where = [
                'family_id' => $familyId[$i]
            ];

            $status = $this->db->UPDATE('employee_family_details', $array5, $where);

            $array2 = [
                'policy_detail_id' => $policyid[$i],
                'policy_mem_dob' => date('d-m-Y', (strtotime($family_date_birth[$i]))),
                'fr_id' => $family_members_id[$i],
                'family_relation_id' => $op['family_relation_id'],
                'family_id' => $op['family_id'],
                'policy_mem_gender' => $family_gender[$i],
                'company_id' => $query2[0]['company_id'],
                'age' => $age[$i],
                'age_type' => $age_type[$i],
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+1 years')),
                'policy_mem_sum_insured' => $sum_insure,
                'employer_policy_mem_sum_premium' => $employer_policy_mem_sum_premium,
                'employee_policy_mem_sum_premium' => $employee_policy_mem_sum_premium,
                'policy_mem_sum_premium' => $sum_premium
            ];

            $status = $this->db->insert('employee_policy_member', $array2);
            $arr['status'] = "Dependent Member Added Successfully";
        } else {
            $array = [
                'family_dob' => date('d-m-Y', (strtotime($family_date_birth[$i]))),
                'fr_id' => $family_members_id[$i],
                'family_firstname' => $first_name[$i],
                'family_lastname' => $last_name[$i],
                'family_gender' => $family_gender[$i]
            ];

            $status = $this->db->insert('employee_family_details', $array);
            $family_id = $this->db->insert_id();

            $array4 = [
                'emp_id' => $emp_id,
                'family_id' => $family_id,
                'created_on' => date('d-m-Y')
            ];

            $status = $this->db->insert('family_relation', $array4);
            $insert_id = $this->db->insert_id();

            $array3 = [
                'policy_detail_id' => $policyid[$i],
                'policy_mem_dob' => date('d-m-Y', (strtotime($family_date_birth[$i]))),
                'fr_id' => $family_members_id[$i],
                'family_relation_id' => $insert_id,
                'family_id' => $family_id,
                'policy_mem_gender' => $family_gender[$i],
                'company_id' => $query2[0]['company_id'],
                'age' => $age[$i],
                'age_type' => $age_type[$i],
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+1 years')),
                'policy_mem_sum_insured' => $sum_insure,
                'employer_policy_mem_sum_premium' => $employer_policy_mem_sum_premium,
                'employee_policy_mem_sum_premium' => $employee_policy_mem_sum_premium,
                'policy_mem_sum_premium' => $sum_premium
            ];
            $status = $this->db->insert('employee_policy_member', $array3);

        }
        if (!empty($deduction_type)) {
            if (empty($pay_amount)) {
                $array5 = [
                    'emp_id' => $emp_id,
                    'fr_id' => $family_members_id[$i],
                    'employee_flexi_benifit_id' => ((in_array($family_members_id[$i], [4, 5, 6, 7])) ? 2 : null),
                    'deduction_type' => $deduction_type,
                    'transac_type' => ((in_array($family_members_id[$i], [4, 5, 6, 7])) ? 'C' : null),
                    'final_amount' => $final_amount,
                    'flex_amount' => $flex_amount,
                    'pay_amount' => 0,
                    'balance_amount' => $flex_amount,
                    'sum_insured' => $sum_insure,
                    'allocated_flag' => 'Y',
                    'confirmed_flag' => 'N',
                    'ip_address' => $this->input->ip_address(),
                    'allocated_updated_at' => date('Y-m-d H:i:s')
                ];
            } else if (empty($flex_amount)) {
                $array5 = [
                    'emp_id' => $emp_id,
                    'fr_id' => $family_members_id[$i],
                    'employee_flexi_benifit_id' => ((in_array($family_members_id[$i], [4, 5, 6, 7])) ? 2 : null),
                    'deduction_type' => $deduction_type,
                    'transac_type' => ((in_array($family_members_id[$i], [4, 5, 6, 7])) ? 'C' : null),
                    'final_amount' => $final_amount,
                    'flex_amount' => 0,
                    'pay_amount' => $pay_amount,
                    'balance_amount' => $pay_amount,
                    'sum_insured' => $sum_insure,
                    'allocated_flag' => 'Y',
                    'confirmed_flag' => 'N',
                    'ip_address' => $this->input->ip_address(),
                    'allocated_updated_at' => date('Y-m-d H:i:s')
                ];
            } else {
                $array5 = [
                    'emp_id' => $emp_id,
                    'fr_id' => $family_members_id[$i],
                    'employee_flexi_benifit_id' => ((in_array($family_members_id[$i], [4, 5, 6, 7])) ? 2 : null),
                    'deduction_type' => $deduction_type,
                    'transac_type' => ((in_array($family_members_id[$i], [4, 5, 6, 7])) ? 'C' : null),
                    'final_amount' => $final_amount,
                    'flex_amount' => $flex_amount,
                    'pay_amount' => $pay_amount,
                    'balance_amount' => $flex_amount,
                    'sum_insured' => $sum_insure,
                    'allocated_flag' => 'Y',
                    'confirmed_flag' => 'N',
                    'ip_address' => $this->input->ip_address(),
                    'allocated_updated_at' => date('Y-m-d H:i:s')
                ];
            }
            $status = $this->db->insert('employee_flexi_benefit_transaction', $array5);
        }
        $arr['status'] = "Dependent Member Added Successfully";
    }
}
return $arr;
}
}


public function enrollment_submit_flex_data()
{

  extract($this->input->post());
  $emp_id = base64_decode($emp_id);

  $data_flex = $this->db->select('*')
  ->from('employee_flexi_benefit_transaction as efbt,master_flexi_benefit as mfb')
  ->where('efbt.employee_flexi_benifit_id = mfb.master_flexi_benefit_id')
  ->where('emp_id',$emp_id)
  ->where_in('employee_flexi_benifit_id',array('3','4','5'))
  ->get()
  ->result_array();
  foreach ($data_flex as $key => $value) 
  {
      $udata = array(
          'confirmed_flag' => 'Y',
          'confirmed_created_at' => date('Y-m-d H:i:s')
      );
      $where = array(
          'emp_id' => $emp_id,
          'employee_flexi_benifit_id' => $value['employee_flexi_benifit_id'],
          'employee_flexi_benefit_transaction_id' => $value['employee_flexi_benefit_transaction_id']
      );
      $test = $this->db->update('employee_flexi_benefit_transaction',$udata,$where);
      $test = $this->insert_employee_policy_details($value['flexi_benefit_name'],$value['final_amount'],$value['sum_insured'],$emp_id);                 
  }

  $data_nominee = $this->db->select('*')
  ->from('member_policy_nominee AS mpn')
  ->where('mpn.emp_id',$emp_id)
  ->get()->result_array();
  if (!empty($data_nominee)) 
  {
    foreach ($data_nominee as $key => $value_nominee) 
    {
       $udata = array(
        'confirmed_flag' => 'Y'
    );
       $where = array(
          'emp_id' => $emp_id
      );
       $test = $this->db->update('member_policy_nominee',$udata,$where);
   }
}
                  // echo $this->db->last_query();
if($test)
{   
    return ['status'=> 'successfully submitted'];
}
else
{
    return ['status' => 'Something went wrong'];
}                 
}

public function insert_employee_policy_details($name,$amount,$si,$emp_id)
{
        // extract($this->input->post(NULL,true));

          // $top_name[] = explode(",", $name);
  if ($name == 'Voluntary Term Life' || $name == 'Personal Accident Top-Up') 
  {
     $data = $this->db->select('epd.policy_detail_id,epm.policy_member_id,epd.policy_no')
     ->from('master_policy_sub_type AS mpst,employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr')
     ->where('mpst.policy_sub_type_id = epd.policy_sub_type_id')
     ->where('epd.policy_detail_id = epm.policy_detail_id')
     ->where('fr.family_relation_id = epm.family_relation_id')
     ->where('fr.emp_id',$emp_id)
     ->where('fr.family_id',0)
     ->where('mpst.policy_sub_type_name',$name)
     ->get()
     ->result_array();
     if ($data) 
     {
      $update =  [
       'employee_policy_mem_sum_premium' => $si,
       'policy_mem_sum_insured' => $si,
       'policy_mem_sum_premium' => $amount,
       'status' => 'Active'
   ];
   $this->db->where('policy_member_id',$data[0]['policy_member_id']);
   $this->db->update('employee_policy_member',$update);
}
else
{
   $data1 = $this->db->
   select('*')
   ->from('master_policy_sub_type AS mpst,employee_policy_detail AS epd')
   ->where('mpst.policy_sub_type_id = epd.policy_sub_type_id')
   ->where('mpst.policy_sub_type_name',$name)
   ->get()
   ->result_array();

   $relation_id = $this->db
   ->select('*')
   ->from('family_relation')
   ->where('emp_id',  $emp_id)
   ->where('family_id',  0)
   ->get()
   ->result_array();

   $udata = [
    'company_id' =>$data1[0]['company_id'],
    'broker_id'=> $data1[0]['broker_id'],
    'fr_id'=> '0',
    'policy_no'=> $data1[0]['policy_no'],
    'policy_detail_id'=> $data1[0]['policy_detail_id'],
    'family_relation_id' => $relation_id[0]['family_relation_id'],
    'member_id' => $emp_id,
    'employee_policy_mem_sum_premium' => $si,
    'policy_mem_sum_insured' => $si,
    'policy_mem_sum_premium' => $amount,
    'status' => 'Active'

];

$this->db->insert('employee_policy_member',$udata);  

}
}
else
{
  if($name == 'Mediclaim Top-Up')
  {
    $data = $this->db->select('epd.policy_detail_id,epm.policy_member_id,epd.policy_no')
    ->from('master_policy_sub_type AS mpst,employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr')
    ->where('mpst.policy_sub_type_id = epd.policy_sub_type_id')
    ->where('epd.policy_detail_id = epm.policy_detail_id')
    ->where('fr.emp_id',$emp_id)
    ->where('mpst.policy_sub_type_name',$name)
    ->where('fr.family_relation_id = epm.family_relation_id')
    ->get()
    ->result_array();
    if ($data) 
    {
     foreach ($data as $key => $value) 
     {
      $where =  [
         'policy_member_id' => $value['policy_member_id']
     ];
     $this->db->delete('employee_policy_member',$where);
 }
}
$data_policy_details = $this->db->
select('*')
->from('master_policy_sub_type AS mpst,employee_policy_detail AS epd')
->where('mpst.policy_sub_type_id = epd.policy_sub_type_id')
->where('mpst.policy_sub_type_name',$name)
->get()
->result_array();
$data1 = $this->db->
select('*')
->from('master_policy_sub_type as mpst,employee_policy_detail as epd,
    employee_policy_member as epm,family_relation as fr')
->where('mpst.policy_sub_type_id',1)
->where('epd.policy_sub_type_id = mpst.policy_sub_type_id')
->where('epd.policy_sub_type_id = mpst.policy_sub_type_id')
->where('epd.policy_detail_id = epm.policy_detail_id')
->where('fr.family_relation_id = epm.family_relation_id')
->where('fr.emp_id',$emp_id)
->where('epm.status!=','Inactive')
->get()
->result_array();
foreach ($data1 as $key => $value) {
  $udata = [
    'company_id' => $data_policy_details[0]['company_id'],
    'broker_id'=> $data_policy_details[0]['broker_id'],
    'policy_no'=> $data_policy_details[0]['policy_no'],
    'policy_detail_id'=> $data_policy_details[0]['policy_detail_id'],
    'family_relation_id' => $value['family_relation_id'],
    'member_id' => $emp_id,
    'employee_policy_mem_sum_premium' => $si,
    'policy_mem_sum_insured' => $si,
    'policy_mem_sum_premium' => $amount,
    'status' => 'Active'
];

$this->db->insert('employee_policy_member',$udata);  
}

}
}

}

public function get_utilised_data()
{

    extract($this->input->post());
    $emp_id = base64_decode($emp_id);

    $flex_data = $this->db
    ->select('SUM(flex_amount) As final_amount')
    ->from('employee_flexi_benefit_transaction ed')
    ->where('ed.emp_id', $emp_id)
    ->get()
    ->row_array();
    $salary_data = $this->db
    ->select('SUM(pay_amount) As final_amount')
    ->from('employee_flexi_benefit_transaction ed')
    ->where('ed.emp_id', $emp_id)
    ->get()
    ->row_array();
    $benifit_data = $this->db
    ->select('*')
    ->from('master_flexi_benefit mfb')
    ->join('employee_flexi_benefit_transaction ed','mfb.master_flexi_benefit_id = ed.employee_flexi_benifit_id','left')
    ->where('ed.emp_id', $emp_id)
    ->get()
    ->result_array(); 
    $flex_parent_premium = $this->db
    ->select('IFNULL(SUM(final_amount), 0) As final_flex_amount')
    ->from('employee_flexi_benefit_transaction ed')
    ->where('ed.emp_id', $emp_id)
    ->where('ed.employee_flexi_benifit_id', 2)
    ->get()
    ->result_array();
    $ed_flex_amount = $this->db->select('ed.flex_amount')
    ->from('employee_details as ed')
    ->where('ed.emp_id', $emp_id)
    ->get()
    ->result_array();             


    return ['flex_data' => $flex_data['final_amount'],
    'salary_data' => $salary_data['final_amount'],
    'benifit_data' => $benifit_data,
    'flex_parent_premium' => $flex_parent_premium[0]['final_flex_amount'],
    'ed_flex_amount' => $ed_flex_amount[0]['flex_amount']
];
}


function get_base_policy_record() {

    extract($this->input->post());
    $emp_id = base64_decode($emp_id);

    $data = $this->db->select('epm.employee_policy_mem_sum_premium,epm.policy_mem_sum_insured,mpst.policy_sub_type_name,epd.sum_insured_type,mpst.policy_sub_type_id ')
    ->from('employee_policy_detail as epd,master_policy_sub_type as mpst,employee_policy_member as epm,family_relation as fr,employee_details as ed')
    ->where('epd.policy_sub_type_id = mpst.policy_sub_type_id')
    ->where('epm.policy_detail_id = epd.policy_detail_id')
    ->where('epm.family_relation_id = fr.family_relation_id')
    ->where('fr.emp_id = ed.emp_id')
    ->where('ed.emp_id',$emp_id)
    ->where('epm.status !=', 'Inactive')
    ->where_in('mpst.policy_sub_type_id',array('1','2','3'))
    ->get()
    ->result_array();

    return $data;
}

function get_flex_active_record() {
    extract($this->input->post());
    $emp_id = base64_decode($emp_id);

    $data = $this->db->select('IFNULL(SUM(efbt.flex_amount),0) AS flex_amount,IFNULL(SUM(efbt.pay_amount),0) AS pay_amount')
    ->from('employee_flexi_benefit_transaction as efbt')
//                          ->where_in('efbt.employee_flexi_benifit_id',array('4','2','3','5'))
    ->where('efbt.emp_id',$emp_id)
    ->get()
    ->result_array();
    return $data;
}


function get_data() {
    extract($this->input->post());

    $data = $this->db->select('*')
    ->from('employee_claim_reimb')
    ->where('claim_reimb_id',$claim_id)
    ->get()
    ->result_array();
    
    $claim_reimb_hospitalization_date = $data[0]['claim_hospitalization_date'];
    $claim_reimb_discharge_date = $data[0]['claim_discharge_date'];

    if(!empty($bill_date)){

        if(date('Y-m-d',strtotime($bill_date)) < date('Y-m-d',strtotime($claim_reimb_hospitalization_date))){
            $msg =  "Pre-Hospitalization";
        }elseif(date('Y-m-d',strtotime($bill_date)) > date('Y-m-d',strtotime($claim_reimb_discharge_date))){
            $msg = "Post-Hospitalization";
        }else{
            $msg = "Hospitalization";
        }

        return $msg;

    }

}

public function getIfscCode($condition = '')
{
	

    return $this->db->where($condition)->get('ifsc_master')->row();
	
}
public function getIfscCode1($condition = '',$table)
{


   $data=  $this->db->where($condition)->get($table)->row();
	return $data;
	
}
public function getBankName($condition = '')
{
    if ($condition) {
        return $this->db->select('bank_name')->where($condition)->group_by('bank_name')->get('ifsc_master')->result_array();
    } else {
        return $this->db->select('bank_name')->group_by('bank_name')->get('ifsc_master')->result_array();
    }
}

public function getBankCity($condition = '')
{
    return $this->db->select('bank_city')->where($condition)->group_by('bank_city')->get('ifsc_master')->result_array();
}

public function getBankBranch($condition = '')
{
    return $this->db->select('bank_branch, ifsc_code')->where($condition)->get('ifsc_master')->result_array();
}

function get_emp_details($emp_id) {
    return $this->db
    ->select("emp_id, emp_firstname, emp_lastname, bdate, emp_code, gender, email, mob_no, email, emg_cno, emp_grade, emp_designation, emp_city, emp_state, emp_pincode, emp_status, master_company.company_id, comapny_name, designation_name, bank_name, bank_branch, bank_city, ifsc_master.ifsc_code, emp_address, uri_id, flex_allocate, payroll_allocate, enrollment_start_date, enrollment_end_date, employee_details.bank_id, acc_name, acc_no, emp_bank_doc_path, emp_bank_id_proof, ifsc_master.ifsc_code, street, location, flex_amount, total_salary, gmc_grade_id, emp_pay, doj,emg_cno,emp_emg_cont_name,alt_email")
        //->where(["emp_id"=>$this->emp_id])
    ->where('emp_id', base64_decode($emp_id))
    ->join("master_company","employee_details.company_id=master_company.company_id")
    ->join("ifsc_master","ifsc_master.bank_id=employee_details.bank_id","left")
    ->join("master_designation", "employee_details.emp_designation=master_designation.master_desg_id", "left")
    ->get("employee_details")->result();

}




public function get_family_details_per_emp($emp_id){
    $SQL =  $this->db->select('efd.family_id, 
        IFNULL(efd.family_firstname, " ") as family_firstname, 
        IFNULL(efd.family_lastname, " ") as family_lastname,
        IFNULL(efd.marriage_date, " ") as marriage_date,  
        IFNULL(mfr.fr_name, " ") as fr_name,
        IFNULL(efd.family_dob, " ") as family_dob,
        IFNULL(efd.family_contact, " ") as family_contact,  
        IFNULL(efd.family_email, " ") as family_email,  
        IFNULL(efd.family_pincode, " ") as family_pincode,  
        IFNULL(efd.family_flat, " ") as family_flat,
        IFNULL(efd.family_location, " ") as family_location,
        IFNULL(efd.cities, " ") as cities,
        IFNULL(efd.state_names, " ") as state_names,
        IFNULL(efd.family_street, " ") as family_street')

    ->from('employee_family_details efd, family_relation fr, employee_details ed, master_family_relation mfr')

    ->where('efd.family_id = fr.family_id')
    ->where('efd.family_status', 1)

    ->where('ed.emp_id = fr.emp_id')

    ->where('efd.fr_id = mfr.fr_id')

    ->where('ed.emp_id', base64_decode($emp_id))
    ->order_by('efd.family_id', desc)

    ->get()->result();

        // echo $this->db->last_query();exit;

    return $SQL;
}



public function get_employeee_address($emp_id){
        //var_dump($emp_id);exit;

   $result = $this->db
   ->select("IFNULL(emp_address,'') as emp_address,
    IFNULL(location,'') as location,
    IFNULL(street,'') as street,
    IFNULL(emp_pincode,'') as emp_pincode,
    IFNULL(emp_city,'') as emp_city,
    IFNULL(emp_state,'') as emp_state")
   ->from('employee_details AS ed')
   ->where("ed.emp_id", base64_decode($emp_id))
   ->get()
   ->result_array();

   return $result;
}


public function update_family_members($data, $family_id) {
  $this->db->where('family_id', $family_id);
  return $this->db->update('employee_family_details', $data); 
}

public function update_employee_details($data, $emp_id) {
  $this->db->where('emp_id', $emp_id);
  return $this->db->update('employee_details', $data); 
}

public function get_state_city($pincode = '')
{
    if (isset($_POST['pincode'])) {
        $pincode = $this->input->post('pincode');
    }

    $details = $this->db
    ->select('state_name,city_name,state_code')
    ->from('postal_codes as pc, master_state  as ms, master_city as mc')
    ->where('pc.state_id = ms.state_id')
    ->where('pc.city_id = mc.city_id')
    ->where('pc.pincode', $pincode)
    ->get()
    ->row();

    return $details;
}

public function axis_state_city($pincode = null)
{
    if (isset($_POST['pincode'])) {
        $pincode = $this->input->post('pincode');
    }

    $details = $this->db
    ->select('state,city,state_code')
    ->from('axis_postal_code as pc')
   
    ->where('pc.pincode', $pincode)
    ->get()
    ->row();

    return $details;
}
public function fetch_detail_policy_type_wise($emp_id){
    $subQuery1 = $this->db
    ->select('epd.policy_detail_id, 
        epd.policy_type_id, epd.policy_sub_type_id, epd.insurer_id, epd.broker_id, 
        epm.policy_detail_id, epm.family_relation_id, epm.family_id, epd.policy_no, 
        mps.policy_type_id, mpst.policy_sub_type_id, mpst.policy_sub_type_name, mpst.policy_sub_type_image_path, 
        mf.family_relation_id, mf.family_id, mf.emp_id, ic.insurer_id, ic.ins_co_name, ic.insurer_companies_img_path,epm.policy_mem_sum_insured, epm.policy_mem_sum_premium, "0" AS fr_id, "Self" AS fr_name')
    ->from('employee_details AS ed,
        family_relation AS mf,
        employee_policy_member AS epm, 
        employee_policy_detail AS epd, 
        master_policy_sub_type AS mpst, 
        master_policy_type AS mps, 
        master_insurance_companies AS ic')
    ->where('ed.emp_id=mf.emp_id')
    ->where('mf.family_relation_id=epm.family_relation_id')
    ->where('epm.policy_detail_id=epd.policy_detail_id')
    ->where('epd.policy_sub_type_id=mpst.policy_sub_type_id')
    ->where('mpst.policy_type_id=mps.policy_type_id')
    ->where('epd.insurer_id=ic.insurer_id')
    ->where('mf.family_id', 0)
    ->where('epm.status!=', 'Inactive')
    ->where('mf.emp_id', base64_decode($emp_id))
    ->get_compiled_select();
    $op = $this->db->select('epd.policy_detail_id,epd.policy_type_id,epd.policy_sub_type_id,
        epd.insurer_id,epd.broker_id, epm.policy_detail_id,
        epm.family_relation_id, epm.family_id, epd.policy_no,
        mps.policy_type_id, mpst.policy_sub_type_id,
        mpst.policy_sub_type_name, mpst.policy_sub_type_image_path, fr.family_relation_id, 
        fr.family_id, fr.emp_id, ic.insurer_id, 
        ic.ins_co_name, ic.insurer_companies_img_path,epm.policy_mem_sum_insured, epm.policy_mem_sum_premium, mfr.
        fr_id, mfr.fr_name')
    ->from('employee_policy_member` AS `epm`, `employee_policy_detail` AS `epd`, `master_policy_type` AS `mps`, `master_policy_sub_type` AS `mpst`, `master_insurance_companies` AS `ic`, `family_relation` AS `fr`, `master_family_relation` AS `mfr`, `employee_family_details` AS `efd`')
    ->where('epd`.`policy_detail_id`=`epm`.`policy_detail_id')
    ->where('mps`.`policy_type_id`=`epd`.`policy_type_id')
    ->where('mpst`.`policy_sub_type_id`=`epd`.`policy_sub_type_id')
    ->where('ic`.`insurer_id`=`epd`.`insurer_id')
    ->where('fr`.`family_relation_id`=`epm`.`family_relation_id')
    ->where('fr`.`family_id`=`efd`.`family_id')
    ->where('efd`.`fr_id`=`mfr`.`fr_id')
    ->where('epm.status!=', 'Inactive')
    ->where('fr`.`emp_id`', base64_decode($emp_id))
    ->get_compiled_select();
    $response = $this->db->query($subQuery1 . ' UNION ALL ' . $op)->result_array();

    $policy_details = [];

    for ($i = 0; $i < count($response); $i++) {
        if (!isset($policy_details[$response[$i]['policy_detail_id']])) {
            $policy_details[$response[$i]['policy_detail_id']]['policy_id'] = $response[$i]['policy_detail_id'];
            $policy_details[$response[$i]['policy_detail_id']]['policy_type_id'] = $response[$i]['policy_type_id'];
            $policy_details[$response[$i]['policy_detail_id']]['ins_co_name'] = $response[$i]['ins_co_name'];
            $policy_details[$response[$i]['policy_detail_id']]['policy_mem_sum_insured'] = $response[$i]['policy_mem_sum_insured'];
            $policy_details[$response[$i]['policy_detail_id']]['policy_mem_sum_premium'] = $response[$i]['policy_mem_sum_premium'];
            $policy_details[$response[$i]['policy_detail_id']]['policy_sub_type_name'] = $response[$i]['policy_sub_type_name'];
            $policy_details[$response[$i]['policy_detail_id']]['policy_sub_type_image_path'] = $response[$i]['policy_sub_type_image_path'];
            $policy_details[$response[$i]['policy_detail_id']]['insurer_companies_img_path'] = $response[$i]['insurer_companies_img_path'];
            $policy_details[$response[$i]['policy_detail_id']]['policy_sub_type_id'] = $response[$i]['policy_sub_type_id'];
            $policy_details[$response[$i]['policy_detail_id']]['member_count'] = 1;
            $policy_details[$response[$i]['policy_detail_id']]['members'][] = $response[$i]['fr_name'];
        } else {
            $policy_details[$response[$i]['policy_detail_id']]['members'][] = $response[$i]['fr_name'];
            $policy_details[$response[$i]['policy_detail_id']]['member_count'] ++;
        }
    }
    $policy_details = array_values($policy_details);
    for ($i = 0; $i < count($policy_details); $i++) {
        $policy_details[$i]['members'] = implode(', ', array_unique($policy_details[$i]['members']));
    }
        // print_pre($policy_details);
    return $policy_details;

}




public function flexi_benefit_voluntary_details($emp_id){
    $result = $this->db->select('mfb.flexi_benefit_name,mfb.img_name, efbt.flex_amount, efbt.final_amount, efbt.balance_amount, efbt.pay_amount')
    ->from('employee_flexi_benefit_transaction as efbt, master_flexi_benefit as mfb, employee_details as ed, family_relation as fr, employee_family_details as efd')
    ->where('mfb.master_flexi_benefit_id = efbt.employee_flexi_benifit_id')
    ->where('ed.emp_id = efbt.emp_id')
    ->where('fr.emp_id = ed.emp_id')
    ->where('efd.family_id = fr.family_id')
    ->where('efbt.employee_flexi_benifit_id >=', 1)
    ->where('efbt.employee_flexi_benifit_id <=', 6)
    ->where('efbt.emp_id', base64_decode($emp_id))
    ->get()->row();
    return $result;
}


public function fetch_flexi_benefit_flex_summary_typewise($emp_id){
    $result = $this->db->select('IFNULL(ed.flex_amount, 0) as Flex_Wallet, 
        IFNULL((ed.flex_amount - sum(efbt.flex_amount)), 0) as `total_balance`, IFNULL(sum(efbt.flex_amount), 0) as Wallet_Utilization,IFNULL(sum(efbt.pay_amount), 0) As to_pay')
    ->from('employee_flexi_benefit_transaction as efbt, master_flexi_benefit as mfb, employee_details as ed')
    ->where('mfb.master_flexi_benefit_id = efbt.employee_flexi_benifit_id')
    ->where('ed.emp_id = efbt.emp_id')
    ->where('efbt.emp_id', base64_decode($emp_id))
    ->get()->row();
    // echo $this->db->last_query();
    return $result;

}

public function testing_new($emp_id){
    $result = $this->db->select('*')
    ->from('employee_flexi_benefit_transaction as efbt, master_flexi_benefit as mfb, employee_details as ed')
    ->where('mfb.master_flexi_benefit_id = efbt.employee_flexi_benifit_id')
    ->where('ed.emp_id = efbt.emp_id')
    ->where('efbt.emp_id', base64_decode($emp_id))
    ->get()->result_array();
    // echo $this->db->last_query();
    return $result;

}

public function fetch_flexi_benefit_all_voluntary_typewise($emp_id){
    $result = $this->db->select('mfb.master_flexi_benefit_id, mfb.flexi_benefit_name, mfb.img_name,IFNULL(efbt.flex_amount, 0) as flex_amount, IFNULL(efbt.final_amount, 0) as final_amount, IFNULL(efbt.balance_amount, 0) as balance_amount,IFNULL(efbt.pay_amount, 0) as pay_amount, IFNULL(efbt.deduction_type, " ") as deduction_type,IFNULL(efbt.sum_insured, 0) as sum_insured')
    ->from('employee_flexi_benefit_transaction as efbt,master_flexi_benefit as mfb,employee_details as ed')
    ->where('mfb.master_flexi_benefit_id = efbt.employee_flexi_benifit_id')
    ->where('ed.emp_id = efbt.emp_id')
    ->where_in('efbt.employee_flexi_benifit_id',array('1','2','3','4','5'))
                // ->where('mfb.master_flexi_benefit_id >=', 1)
                // ->where('efbt.employee_flexi_benifit_id <=', 6)
    ->where('efbt.emp_id', base64_decode($emp_id))
    ->get()->result_array();
    return $result;
}

// public function testing($emp_id){
//     $result = $this->db->select('mfb.master_flexi_benefit_id, mfb.flexi_benefit_name, mfb.img_name,IFNULL(efbt.flex_amount, 0) as flex_amount, IFNULL(efbt.final_amount, 0) as final_amount, IFNULL(efbt.balance_amount, 0) as balance_amount,IFNULL(efbt.pay_amount, 0) as pay_amount, IFNULL(efbt.deduction_type, " ") as deduction_type,IFNULL(efbt.sum_insured, 0) as sum_insured')
//     ->from('employee_details as ed,master_flexi_benefit as mfb,employer_flexi_benifit_relation as efbr,employee_flexi_benefit_transaction as efbt')
//     ->where('ed.company_id = efbr.employer_id')
//             // ->join('employee_flexi_benefit_transaction as efbt', 'efbr.master_flexi_benefit_id = efbt.employee_flexi_benifit_id',"left")
//     ->where('efbr.master_flexi_benefit_id = mfb.master_flexi_benefit_id')
//     ->where('ed.emp_id', base64_decode($emp_id))
//     ->where('efbr.is_active', Y)
//     ->where_in('efbr.master_flexi_benefit_id',array('1'))
//     ->group_by('efbr.master_flexi_benefit_id')

//     ->get()->result_array();
//             // echo $this->db->last_query();
//     return $result;
// }


    // public function fetch_flexi_benefit_all_voluntary_typewise($emp_id){

    //   $result = $this->db->select('mfb.master_flexi_benefit_id, mfb.flexi_benefit_name, mfb.img_name,IFNULL(efbt.flex_amount, 0) as flex_amount, IFNULL(efbt.final_amount, 0) as final_amount, IFNULL(efbt.balance_amount, 0) as balance_amount,IFNULL(efbt.pay_amount, 0) as pay_amount, IFNULL(efbt.deduction_type, " ") as deduction_type')

    //         ->from('employee_details as ed')

    //         ->join('employer_flexi_benifit_relation as efbr', 'ed.company_id = efbr.employer_id',"left")

    //         ->join('employee_flexi_benefit_transaction as efbt', 'efbr.master_flexi_benefit_id = efbt.employee_flexi_benifit_id',"left")

    //         ->join('master_flexi_benefit as mfb', 'efbr.master_flexi_benefit_id = mfb.master_flexi_benefit_id',"left")

    //         ->where('ed.emp_id', base64_decode($emp_id))

    //         ->where('efbr.is_active', Y)

    //         ->where_in('efbr.master_flexi_benefit_id',array('1','2','3','4','5','6'))
    //         ->group_by('efbr.master_flexi_benefit_id')
    //         ->get()->result();
    //         return $result;
    //     }

public function fetch_flexi_benefit_voluntary_typewise_summary($emp_id){
    $result = $this->db->select('mfb.flexi_benefit_name, mfb.img_name, IFNULL(efbt.flex_amount, 0) as flex_amount, IFNULL(efbt.final_amount, 0) as premium, IFNULL(efbt.balance_amount, 0) as balance_amount,IFNULL(efbt.pay_amount, 0) as payroll_amount, IFNULL(efbt.sum_insured, 0) as cover, efbt.deduction_type')
    ->from('employee_flexi_benefit_transaction as efbt,master_flexi_benefit as mfb,employee_details as ed')
    ->where('mfb.master_flexi_benefit_id = efbt.employee_flexi_benifit_id')
    ->where('ed.emp_id = efbt.emp_id')
    ->where_in('efbt.employee_flexi_benifit_id',array('1','2','3','4','5'))
                // ->where('mfb.master_flexi_benefit_id >=', 1)
                // ->where('efbt.employee_flexi_benifit_id <=', 6)
    ->where('efbt.emp_id', base64_decode($emp_id))
    ->get()->result_array();
    return $result;
}

public function fetch_detail_policy_type_wellness_summary($emp_id){
    $result = $this->db->select('mfb.flexi_benefit_name,mfb.img_name, IFNULL(efbt.flex_amount, 0) as flex_amount, IFNULL(efbt.final_amount, 0) as final_amount, IFNULL(efbt.balance_amount, 0) as balance_amount,IFNULL(efbt.pay_amount, 0) as pay_amount,efbt.deduction_type')
    ->from('employee_flexi_benefit_transaction as efbt, master_flexi_benefit as mfb, employee_details as ed')
    ->where('mfb.master_flexi_benefit_id = efbt.employee_flexi_benifit_id')
    ->where('ed.emp_id = efbt.emp_id')
                   //->where_in('efbt.employee_flexi_benifit_id',array('7','8','9','10','11','12','13','14','15','16','17'))
    ->where('mfb.master_flexi_benefit_id >=', 6)
    ->where('efbt.employee_flexi_benifit_id <=', 20)
    ->where('efbt.emp_id', base64_decode($emp_id))
    ->get()->result();
                   // echo
    return $result;

}


public function fetch_detail_policy_type_wellness($emp_id){

    $result = $this->db->select('mfb.master_flexi_benefit_id, mfb.flexi_benefit_name,mfb.img_name, IFNULL(efbt.deduction_type, " ") as deduction_type,efbr.is_active')

    ->from('employee_details as ed,master_flexi_benefit as mfb,employer_flexi_benifit_relation as efbr')

    ->where('ed.company_id = efbr.employer_id')

    ->join('employee_flexi_benefit_transaction as efbt', 'efbr.master_flexi_benefit_id = efbt.employee_flexi_benifit_id',"left")

    ->where('efbr.master_flexi_benefit_id = mfb.master_flexi_benefit_id')

    ->where('ed.emp_id', base64_decode($emp_id))
            // ->where('efbt.emp_id = ed.emp_id')

            // ->where('efbr.is_active', Y)
            // ->where('mfb.active','Y')
            // ->where('mfb.flexi_type','N')

    ->where_in('efbr.master_flexi_benefit_id',array('6','10','11','12','13','14','15','16','17'))
    ->group_by('efbr.master_flexi_benefit_id')

    ->get()->result_array();
             // echo $this->db->last_query();
    return $result;

}

public function amt_data($emp_id){

    $result = $this->db->select('mfb.master_flexi_benefit_id,  IFNULL(efbt.flex_amount, 0) as flex_amount, IFNULL(efbt.final_amount, 0) as final_amount, IFNULL(efbt.balance_amount, 0) as balance_amount,IFNULL(efbt.pay_amount, 0) as pay_amount')

    ->from('employee_details as ed,master_flexi_benefit as mfb,employer_flexi_benifit_relation as efbr')
    ->where('ed.company_id = efbr.employer_id')
    ->join('employee_flexi_benefit_transaction as efbt', 'efbr.master_flexi_benefit_id = efbt.employee_flexi_benifit_id',"left")
    ->where('efbr.master_flexi_benefit_id = mfb.master_flexi_benefit_id')
    ->where('ed.emp_id', base64_decode($emp_id))
    ->where('efbt.emp_id = ed.emp_id')
    ->where_in('efbr.master_flexi_benefit_id',array('6','10','11','12','13','14','15','16','17'))
    ->group_by('efbr.master_flexi_benefit_id')
    ->get()->result_array();
    return $result;

}

public function fetch_detail_policy_type() {
    extract($this->input->post(null, true));
    $subQuery1 = $this->db
    ->select('DISTINCT(ed.emp_id) AS id, ed.bdate as bdate,
     CONCAT(ed.emp_firstname, " ", ed.emp_lastname) AS name, "Self" AS relationship, "0" AS fr_id, "0" as family_id, epm.policy_member_id as emp_member_id,mpst.policy_sub_type_name,epm.policy_mem_sum_insured,epm.policy_mem_gender')
    ->from('employee_policy_detail AS epd, employee_details AS ed, family_relation AS fr,employee_policy_member AS epm, master_policy_sub_type as mpst')
    ->where('ed.emp_id=fr.emp_id AND fr.family_id=0')
    ->where('mpst.policy_sub_type_id = epd.policy_sub_type_id')
    ->where('fr.family_relation_id=epm.family_relation_id')
    ->where('epm.policy_detail_id=epd.policy_detail_id')
                // ->where('epd.policy_no', $policy_no)
    ->where('epm.fr_id !=', 0)
    ->where('ed.emp_id', base64_decode($emp_id))
    ->where('epm.status !=', 'Inactive')
    ->group_by('fr.family_id')
    ->get_compiled_select();

    $subQuery2 = $this->db
    ->select('efd.family_id AS familyid,efd.family_dob as bdate,             
     CONCAT(efd.family_firstname, " ", efd.family_lastname) AS name, mfr.fr_name AS relationship ,mfr.fr_id AS fr_id,efd.family_id as family_id,epm.policy_member_id as emp_member_id, mpst.policy_sub_type_name,epm.policy_mem_sum_insured,epm.policy_mem_gender')
    ->from('employee_policy_detail AS epd,employee_policy_member AS epm ,employee_family_details AS efd, family_relation AS fr, master_family_relation AS mfr, master_policy_sub_type as mpst')
    ->where('efd.family_id=fr.family_id')
    ->where('fr.family_relation_id=epm.family_relation_id')
    ->where('epm.policy_detail_id=epd.policy_detail_id')
    ->where('efd.fr_id=mfr.fr_id')
    ->where('mpst.policy_sub_type_id = epd.policy_sub_type_id')
                // ->where('epd.policy_no', $policy_no)
    ->where('fr.emp_id', base64_decode($emp_id))
    ->where('mpst.policy_sub_type_id',1)
    ->where('epm.status !=', 'Inactive')
    ->where('epm.fr_id !=', 0)
    ->group_by('fr.family_id')
    ->get_compiled_select();
    $z = array();
    $op = $this->db->query($subQuery1 . ' UNION ' . $subQuery2)->result_array();
        //echo $this->db->last_query();exit;
    return $op;
}





public function fetch_family_relation($emp_id){

    $result = $this->db->select('mbir.relationship_id')
    ->from('employee_policy_detail as epd, employee_policy_member as epm, family_relation as fr, employee_details as ed, master_broker_ic_relationship as mbir')
    ->where('epd.policy_detail_id = epm.policy_detail_id')
    ->where('epm.family_relation_id = fr.family_relation_id')
    ->where('fr.emp_id = ed.emp_id')
    ->where('ed.emp_id', base64_decode($emp_id))
    ->where('fr.family_id', 0)
    ->where('epd.policy_sub_type_id', 1)
    ->where('epd.policy_detail_id = mbir.policy_id')
    ->get()->result_array();
                  //echo $this->db->last_query();exit;

    $data = $this->db->select('mfr.fr_id,mfr.fr_name')
    ->from('master_family_relation as mfr')
    ->where('mfr.fr_id IN ('.$result[0]['relationship_id'].')')->get()->result();

    return $data;
}


public function get_nominee_details_for_employee($emp_id){
   $result = $this->db
   ->select("mfr.fr_name, mpn.*")
   ->from('member_policy_nominee AS mpn, master_family_relation AS mfr')
   ->where('mfr.fr_id = mpn.fr_id')
   ->where('mpn.status', 'active')
   ->where("mpn.emp_id", base64_decode($emp_id))
   ->get()
   ->result_array();

   return $result;
}


    //update flexi_benefit

public function update_balance_in_flex_benefit()
{
  extract($this->input->post(null,true));
  $query = $this->db->where(['emp_id' => base64_decode($emp_id), 'employee_flexi_benifit_id'=>$master_flexi_benefit_id])->get('employee_flexi_benefit_transaction')->num_rows();

  $ipaddress = $this->input->ip_address();

  if($deduction_type=='F'){
    $pay_amt = 0;
    $final_amount = $flex_amount;
}else{
    $pay_amt = $flex_amount;
    $final_amount = $flex_amount;
    $flex_amount = 0;
}

if($policy_type=='Voluntary'){
    $transac_type = 'C';
}else{
    $transac_type = 'N';
}

if($query > 0)
{

 $data=array('flex_amount'=>$flex_amount, 'final_amount'=>$final_amount,'pay_amount'=>$pay_amt, 'balance_amount'=>$final_amount,'sum_insured'=>$sum_insured,'transac_type'=>$transac_type,'deduction_type'=>$deduction_type,'allocated_updated_at'=>date('Y-m-d H:i:s'));
 $this->db->where('emp_id',base64_decode($emp_id));
 $this->db->where('employee_flexi_benifit_id', $master_flexi_benefit_id);
         // $this->db->where('employee_flexi_benifit_id >=', 1)
         // $this->db->where('employee_flexi_benifit_id <=', 6)
 
 $res = $this->db->update('employee_flexi_benefit_transaction', $data);
 if($res){
    echo json_encode(['status' => 1, 'msg' => 'Update flex amount successfully.']);
}

}
else
{

if(($balance <= 0 || $balance < $flex_amount) && $deduction_type == 'F')
    {
    echo json_encode(['status' => 0, 'msg' => 'Flex balance is not enough']);
    return false;
    }else{

     $data = array(
        'emp_id'=> base64_decode($emp_id),
        'employee_flexi_benifit_id'=>$master_flexi_benefit_id,

        'transac_type' => $flexi_type,
        'flex_amount'=>$flex_amount, 
        'final_amount'=>$final_amount, 
        'balance_amount'=>$final_amount,
        'pay_amount'=>$pay_amt,
        'allocated_flag'=> Y,
        'confirmed_flag'=> N,
        'ip_address' => $ipaddress,
        'deduction_type' => $deduction_type,
        'sum_insured' => $sum_insured,
        'transac_type' => $transac_type,
        'allocated_created_at' => date('Y-m-d H:i:s')
    );

     $res = $this->db->insert('employee_flexi_benefit_transaction',$data);
     if($res == true){
      echo json_encode(['status' => 1, 'msg' => 'Employee Flexi Benefit transaction added successfully.']);
    }else{
      echo json_encode(['status' => 0, 'msg' => 'error occur...']);
    }

    }

}  
}

    //reset flexi benefit

public function reset_data_from_flexi_benefit_voluntary_transaction($emp_id, $master_flexi_benefit_id){
    $result = $this->db->select("employee_flexi_benefit_transaction_id")
    ->from('employee_flexi_benefit_transaction as efbt')
    ->where("efbt.emp_id", base64_decode($emp_id))
    ->where("efbt.employee_flexi_benifit_id", $master_flexi_benefit_id)
    ->where('efbt.employee_flexi_benifit_id >=', 1)
    ->where('efbt.employee_flexi_benifit_id <=', 6)
    ->get()->result();
                           //echo $this->db->last_query();exit;
                           // print_r($result);exit;
    if(!$result)
    {
      $data['status'] = "Already Value is reseted.";
              //echo 'Value already reset OR Wrong selected flexi benefit id';
  }
  else{
      $this->db->where('employee_flexi_benefit_transaction_id', $result[0]->employee_flexi_benefit_transaction_id);
      $res = $this->db->delete('employee_flexi_benefit_transaction');

      if($res == 1){
        $data['status'] = "Reset data successfully.";
    }
    else{
        $data['status'] = "failed...";
    }

}
echo json_encode($data);
}

public function reset_data_from_flexi_benefit_wellness_transaction($emp_id, $master_flexi_benefit_id){
    $result = $this->db->select("employee_flexi_benefit_transaction_id")
    ->from('employee_flexi_benefit_transaction as efbt')
    ->where("efbt.emp_id", base64_decode($emp_id))
    ->where("efbt.employee_flexi_benifit_id", $master_flexi_benefit_id)
    ->where_in('efbt.employee_flexi_benifit_id',array('7','8','9','10','11','12','13','14','15','16','17'))
                          // ->where('efbt.employee_flexi_benifit_id >=', 7)
                          // ->where('efbt.employee_flexi_benifit_id <=', 20)
    ->get()->result();
                           //echo $this->db->last_query();exit;
                           //print_r($result);exit;
    if(!$result)
    {
      $data['status'] = "Already Value is reseted.";
              //echo 'Value already reset OR Wrong selected flexi benefit id';
  }
  else{

      $this->db->where('employee_flexi_benefit_transaction_id', $result[0]->employee_flexi_benefit_transaction_id);
      $res = $this->db->delete('employee_flexi_benefit_transaction');
      if($res == 1){
       $data['status'] = "Reset data successfully.";
   }
   else{
    $data['status'] = "failed...";
}

}
echo json_encode($data);
}



public function fetch_family_details_via_emp($emp_id, $fr_id)
{
    $result = $this->db->select('count(efd.fr_id)')
    ->from('family_relation as fr')
    ->join('employee_family_details as efd', 'fr.family_id = efd.family_id', "left")
    ->where('fr.emp_id',base64_decode($emp_id))
    ->where('efd.fr_id',$fr_id)
                  // ->where('efd.family_id = fr.family_id')
                  // ->where('epm.family_relation_id = fr.family_relation_id')
    ->get()->result();
                  // print_r($result);exit;
                  // echo $this->db->last_query();exit;

    if ($result > 0){
        echo true;
    }
    else{
        echo false;
    }
}



public function fetch_sodexo_amount_emp($emp_id){
      //var_dump($emp_id);
  $result = $this->db->select('efbr.amount')
  ->from('employee_details as ed, employer_flexi_benifit_relation as efbr')
  ->where('efbr.employer_id = ed.company_id')
  ->where('efbr.master_flexi_benefit_id', 1)
  ->where('ed.emp_id', base64_decode($emp_id))
  ->get()->result();
            // echo $this->db->last_query();exit;

  return $result;
}

function get_policy_member_details($emp_id) {
      //var_dump($emp_id);exit;
        //echo "1111";exit;
      // echo "abhi";
    $subQuery1 = $this->db
    ->select('epd.company_id,epm.tpa_member_name,epm.tpa_member_id,epd.policy_detail_id, 
        epd.broker_id, epm.policy_detail_id, 
        epm.family_relation_id,epm.status, 
        epd.policy_no, mf.family_relation_id,epd.TPA_id,epd.policy_sub_type_id,  
        mf.family_id, "0" AS fr_id, "Self" AS fr_name,
        ed.emp_firstname,ed.emp_lastname, ed.gender, ed.bdate,epm.member_id,epm.policy_mem_sum_insured,epm.age,epm.age_type, epm.policy_mem_sum_premium, epm.start_date,epd.status, epm.policy_member_id, epm.employee_policy_mem_sum_premium')
    ->from('employee_details AS ed, family_relation AS mf, employee_policy_member AS epm, employee_policy_detail AS epd')
    ->where('ed.emp_id = mf.emp_id')
    ->where('mf.family_relation_id = epm.family_relation_id')
    ->where('epm.policy_detail_id = epd.policy_detail_id')
    ->where('epm.status!=', 'Inactive')
    ->where('epd.policy_sub_type_id', 1)
    ->where('mf.family_id', 0)
    ->where('mf.emp_id ', base64_decode($emp_id))
    ->get_compiled_select();
                // var_dump($subQuery1);
    $op = $this->db->select('epd.company_id,epm.tpa_member_name,epm.tpa_member_id,epd.policy_detail_id,  epd.broker_id,
        epm.policy_detail_id, epm.family_relation_id,epm.status, epd.policy_no,  
        fr.family_relation_id,epd.TPA_id,epd.policy_sub_type_id,  
        fr.family_id, mfr. fr_id, mfr.fr_name, efd.family_firstname, efd.family_lastname,efd.family_gender,
        efd.family_dob, epm.member_id,epm.policy_mem_sum_insured,epm.age,epm.age_type, epm.policy_mem_sum_premium, epm.start_date,epd.status, epm.policy_member_id, epm.employee_policy_mem_sum_premium')
    ->from('employee_family_details AS efd, family_relation AS fr,employee_policy_member AS epm, employee_policy_detail AS epd,master_family_relation AS mfr')
    ->where('epd.policy_detail_id = epm.policy_detail_id')
    ->where('fr.family_relation_id = epm.family_relation_id')
    ->where('fr.family_id = efd.family_id')
    ->where('epm.status!=', 'Inactive')
    ->where('epd.policy_sub_type_id', 1)
    ->where('efd.fr_id= mfr.fr_id')
    ->where('fr.emp_id', base64_decode($emp_id))
    ->get_compiled_select();
    $response = $this->db->query($subQuery1 . ' UNION ALL ' . $op)->result_array();
        // var_dump($response);exit;

    // echo $this->db->last_query();

    for ($i = 0; $i < count($response); $i++) {
        $data_select = $this->db->select('efbt.deduction_type')
        ->from('employee_flexi_benefit_transaction as efbt')
                            //->where('efbt.employee_flexi_benifit_id',2)
        ->where('efbt.emp_id', base64_decode($emp_id))
        ->where('efbt.fr_id', $response[$i]['fr_id'])
        ->get()->result_array();

        if (count($data_select) > 0) {
            $deduction_types = [
                'F' => 'Wallet',
                'S' => 'Payroll'
            ];
            $details = [];

            foreach ($data_select as $value) {
                $deduction = explode(',', $value['deduction_type']);
                foreach ($deduction as $ded) {
                    $details[] = $deduction_types[$ded];
                }
            }

            $response[$i]['pay_type'] = implode(', ', $details);
        } else {
            $response[$i]['pay_type'] = '';
        }
    }
        // echo $response;
    return $response;
}


public function delete_enrollment($emp_id,$policy_member_id) {
        //print_r($this->input->post());
    extract($this->input->post(NULL, true));
    $data = $this->db->update('employee_policy_member', array('status' => 'Inactive'), array('policy_member_id' => $policy_member_id));
    $data_select = $this->db->select('epm.fr_id')
    ->from('employee_policy_member as epm')
    ->where('epm.policy_member_id', $policy_member_id)
    ->get()->row_array();
    $data_delete = $this->db->delete('employee_flexi_benefit_transaction', array('emp_id' => base64_decode($emp_id), 'employee_flexi_benifit_id' => 2, 'fr_id' => $data_select['fr_id']));
    if ($data_delete) {
        return true;
    } else {
        return false;
    }
}

public function update_enrollment_policy(){
        //echo 111;exit;
  extract($this->input->post(NULL, true));

  $emp_id = base64_decode($emp_id);
      // $emp_id = base64_decode($this->input->post('emp_id'));
  $arr = [];
  $arrFamily = [];
  $counterFamily = 0;
  $response = $this->db->select('family_dob')
  ->from('employee_family_details AS efd, family_relation AS fr, employee_policy_member AS epm ')
  ->where('epm`.`family_relation_id`=`fr`.`family_relation_id')
  ->where('efd`.`family_id`=`fr`.`family_id')
  ->where("efd.fr_id BETWEEN 2 AND 3")
  ->where("epm.status!=", 'Inactive')
  ->where("epm.policy_detail_id", $policy_detail_id)
  ->where("efd.family_id!=", $family_id)
  ->where('fr.emp_id', $emp_id)
  ->get()->result_array();
  $max_allowed_relation = $this->db->select('*')
  ->from('master_broker_ic_relationship as `mbir, master_family_relation as mfr`')
  ->where('find_in_set(mfr.fr_id, mbir.relationship_id)')
  ->where('mbir`.`policy_id`', $policy_detail_id)
  ->get()->result_array();

  $agr_wise_data = $this->db->select('epd.policy_detail_id, epd.applicable_for, policy_sub_type_id, sum_insured_type,epd.sum_insured, addition_premium,' . ' epd.premium as p_premuim,special_child_check, '
    . 'special_child_contri,unmarried_child_check,'
    . 'unmarried_child_contri,applicable_for_designation_id,unmarried_child_cover,  broker_percent, epd.flex_allocate, epd.payroll_allocate, epd.parent_cross_selection, epd.marital_status, epd.status_wise_single_si, 
    epd.status_wise_single_pre, epd.status_wise_married_si, epd.status_wise_married_pre, epd.premium_paid, epd.cd_balance_threshold, epd.premium_type,pca.policy_id,pca.policy_age, pca.sum_insured as pca_sumInsured, pca.premium as pcapremium, pca.employee_contri_percent, pca.employer_contri_percent,
    epd.gpa_no_of_times, pal.policy_detail_id, relation_id,min_age,max_age,employee_contri,employer_contri, pal.premium as topremium')
  ->from('employee_policy_detail AS epd, employee_details AS ed, master_company AS mc, policy_age_limit AS pal, policy_creation_age as pca')
  ->where('mc.company_id = ed.company_id')
  ->where('pal`.`policy_detail_id` = `epd`.`policy_detail_id`')
  ->where('pca`.`policy_id` = `epd`.`policy_detail_id`')
  ->where('pal`.`relation_id`', $relation_id)
  ->where('ed.emp_id', $emp_id)
  ->where('epd`.`policy_detail_id', $policy_detail_id)
  ->group_by('pca.policy_age')
  ->get()
  ->result_array();

  if ($max_allowed_relation[0]['max_child'] == 0) {
    return (["status" => "You can not add child", "message" => "You can not add child"]);
}

foreach ($response as $res) {
    if (empty($arrFamily)) {
        array_push($arrFamily, $res['family_dob']);
    } else if (in_array($res['family_dob'], $arrFamily)) {
                // ++$counterFamily;
    } else {
        array_push($arrFamily, $res['family_dob']);
    }
}
if ($relation_id == 2 || $relation_id == 3) {
    if (empty($arrFamily)) {
        array_push($arrFamily, $dob_date);
    } else if (in_array($dob_date, $arrFamily)) {
                // ++$counterFamily;
    } else {
        array_push($arrFamily, $dob_date);
    }
}
if ($counterFamily == 0 && count($arrFamily) <= $max_allowed_relation[0]['max_child']) {

} else if ($counterFamily == 1 && count($arrFamily) <= $max_allowed_relation[0]['max_child']) {

} else {
    return (["status" => "Sorry You can not add more than 2 kids"]);
}

$employee_dob = $this->db->select('IFNULL(bdate, 0) as bdate')
->from('employee_details')
->where('emp_id', $emp_id)
->get()
->row_array();
$employee_age = get_date_diff('year', $employee_dob['bdate']);
        //print_r($employee_age);exit;
if (in_array($relation_id, [2, 3])) {
  $child_age = get_date_diff('year', $dob_date);

  if (($employee_age - $child_age) < 18) {
    return (["status" => "Employee and family member age difference must be atleast 18 years"]);
}
}
elseif (!in_array($relation_id, [2, 3])) {
  if (!in_array($relation_id, [0, 1])) {
    $adult_age = get_date_diff('year', $dob_date);

    if (($adult_age - $employee_age) < 18) {
        return (["status" => "Employee and family member age difference must be atleast 18 years"]);
    }
}
}

$sum_insure = 0;
$sum_premium = 0;
$employee_policy_mem_sum_premium = 0;
$employer_policy_mem_sum_premium = 0;
$this->load->library('upload');
$designation_res = $this->db->select('epd.policy_detail_id, policy_sub_type_id, sum_insured_type,sum_insured, addition_premium, epd.premium as p_premuim,special_child_check, special_child_contri,unmarried_child_check,unmarried_child_contri,applicable_for_designation_id,pal.policy_detail_id, relation_id,min_age,max_age,employee_contri,employer_contri, pal.premium as topremium')
->from('employee_policy_detail AS epd, employee_details AS ed, master_company AS mc, policy_age_limit AS pal')
->where('FIND_IN_SET(`ed`.`emp_designation`, epd.applicable_for_designation_id)')
->where('mc.company_id = ed.company_id')
->where('pal`.`policy_detail_id` = `epd`.`policy_detail_id`')
->where('pal`.`relation_id`', $relation_id)
->where('emp_id', $emp_id)
->get()
->row_array();

$non_des = $this->db->select('epd.policy_detail_id, epd.applicable_for, policy_sub_type_id, sum_insured_type,epd.sum_insured, addition_premium,'
    . ' epd.premium as p_premuim,special_child_check, '
    . 'special_child_contri,unmarried_child_check,'
    . 'unmarried_child_contri,applicable_for_designation_id,unmarried_child_cover,  broker_percent, epd.flex_allocate, epd.payroll_allocate, epd.parent_cross_selection, epd.marital_status, epd.status_wise_single_si, 
    epd.status_wise_single_pre, epd.status_wise_married_si, epd.status_wise_married_pre, epd.premium_paid, epd.cd_balance_threshold, epd.premium_type,
    epd.gpa_no_of_times, pal.policy_detail_id, relation_id,min_age,max_age,employee_contri,employer_contri, pal.premium as topremium')
->from('employee_policy_detail AS epd, employee_details AS ed, master_company AS mc, policy_age_limit AS pal')
->where('mc.company_id = ed.company_id')
->where('pal`.`policy_detail_id` = `epd`.`policy_detail_id`')
->where('pal`.`relation_id`', $relation_id)
->where('ed.emp_id',$emp_id)
->where('epd`.`policy_detail_id', $policy_detail_id)
->get()
->row_array();

if($non_des['applicable_for_designation_id'] == '')
{
    if($non_des['premium_type'] == 'memberAge')
    {
      for ($k = 0; $k < count($agr_wise_data); $k++) 
      {
        $min_age = explode("-", $agr_wise_data[$k]['policy_age']);
        if ($age >= $min_age[0] && $age <= $min_age[1]) 
        {
            $sum_insured = $agr_wise_data[$k]['pca_sumInsured'];
            $sum_premium = $agr_wise_data[$k]['pcapremium'];  
            if ($agr_wise_data[$k]['addition_premium'] == 1) 
            {
              if ($agr_wise_data[$k]['sum_insured_type'] == 'individual' || $agr_wise_data[$k]['sum_insured_type'] == 'familyIndividual') 
              {
                if ($relation_id == 0) 
                {
                  if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) 
                  {
                    $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100; 
                    $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
                }
                else 
                {
                    return [
                        "Min age" => $non_des['min_age'],
                        "Max age" => $non_des['max_age'],
                    ];
                }
            } 
            else if ($relation_id == 1 || $relation_id == 4 || $relation_id == 5 || $relation_id == 6 || $relation_id == 7) 
            {
              if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) 
              {
                $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
                $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
            } 
        }
        else 
        {
          if($agr_wise_data[$k]['special_child_check'] == 1 || $agr_wise_data[$k]['unmarried_child_check'] == 1)
          {
            if ($agr_wise_data[$k]['special_child_check'] ==1) 
            {
              $special = explode(",", $special);
              $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
              $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
          } 
          else if ($age >= 25 && $age_type == "years" && $agr_wise_data[$k]['unmarried_child_check'] == 1) 
          {
              $unmarried = explode(",", $unmarried);
              $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
              $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
          } 
          else 
          {
              $employee_policy_mem_sum_premium = 0;
              $employer_policy_mem_sum_premium = $sum_premium;
          }
      }
      else 
      {
        if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
            $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
            $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
        } 
    }
}
} 
else 
{
    $unmarried = $agr_wise_data[$k]['unmarried_child_contri'];
    $special = $agr_wise_data[$k]['special_child_contri'];

    if ($relation_id == 2 || $relation_id == 3) 
    {
      if($agr_wise_data[$k]['special_child_check'] == 1 || $agr_wise_data[$k]['unmarried_child_check'] == 1)
      {
        if ($age >= 25 && $age_type == "years" && $agr_wise_data[$k]['unmarried_child_check'] == 1) 
        {
          $unmarried = explode(",", $unmarried);
          $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
          $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
      } 
      else if ($age > 0 && $agr_wise_data[$k]['special_child_check'] == 1) 
      {
          $special = explode(",", $special);
          $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
          $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
      } 
      else 
      {
          $employee_policy_mem_sum_premium = 0;
          $employer_policy_mem_sum_premium = $sum_premium;
      }
  }
  else 
  {
    if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
        $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
        $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
    } 
}
} 
else if ($relation_id == 1 || $relation_id == 0 || $relation_id == 4 || $relation_id == 5 || $relation_id == 6 || $relation_id == 7) {
    $sum_premium = $agr_wise_data[$k]['pcapremium']; 
    $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
    $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;

} else {

    if ($agr_wise_data[$k]['min_age'] <= $age || $agr_wise_data[$k]['max_age'] >= $age) {
       $employee_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employee_contri']) / 100;
       $employer_policy_mem_sum_premium = ($sum_premium * $agr_wise_data[$k]['employer_contri']) / 100;
   } else {
    return [
        "Min age" => $agr_wise_data[$k]['min_age'],
        "Max age" => $agr_wise_data[$k]['max_age'],
    ];
}
}
}
} 

}
}



}
}
else {
    $sum_insure = $designation_res['sum_insured'];
    $sum_premium = $designation_res['p_premuim'];
    if ($designation_res['addition_premium'] == 0) {
        if ($designation_res['sum_insured_type'] == 'individual' || $designation_res['sum_insured_type'] == 'familyIndividual') {
            if ($relation_id == 2 || $relation_id == 3) {
                if ($designation_res['min_age'] <= $age || $designation_res['max_age'] >= $age) {
                    if ($document_id == "disable_child") {
                        $special = explode(",", $special);
                        $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                        $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                    } else if ($age >= 25 && $document_id == "unmarried_child") {
                        $unmarried = explode(",", $unmarried);
                        $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                        $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                    }
                }
            }
        } else {
            $unmarried = $designation_res['unmarried_child_contri'];
            $special = $designation_res['special_child_contri'];
            $sum_insure = '0';
            $sum_premium = $designation_res['p_premuim'];
            if ($relation_id == 2 || $relation_id == 3) {
                if ($age >= 25 && $document_id == "unmarried_child") {
                    $unmarried = explode(",", $unmarried);
                    $employee_policy_mem_sum_premium = ($sum_premium * $unmarried[0]) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $unmarried[1]) / 100;
                } else if ($age > 0 && $document_id == "disable_child") {
                    $special = explode(",", $special);
                    $employee_policy_mem_sum_premium = ($sum_premium * $special[0]) / 100;
                    $employer_policy_mem_sum_premium = ($sum_premium * $special[1]) / 100;
                } else {
                    $employee_policy_mem_sum_premium = 0;
                    $employer_policy_mem_sum_premium = $sum_premium;
                }
            }
        }
    } 
    else {
        if ($designation_res['sum_insured_type'] == 'individual' || $designation_res['sum_insured_type'] == 'familyIndividual') {
            $sum_insure = $designation_res['sum_insured'];
            if ($relation_id == 2 || $relation_id == 3) {
                if ($designation_res['min_age'] <= $age || $designation_res['max_age'] >= $age) {
                    if ($designation_res['relation_id'] == 2 || $designation_res['relation_id'] == 3) {
                        $$employee_policy_mem_sum_premium = ($designation_res['topremium'] * $designation_res['employee_contri']) / 100;
                        $employer_policy_mem_sum_premium = ($designation_res['topremium'] * $designation_res['employer_contri']) / 100;
                    } else {
                        $sum_premium = $designation_res['p_premuim'];
                    }
                } else {
                    return [
                        "Min age" => $designation_res['min_age'],
                        "Max age" => $designation_res['max_age'],
                    ];
                }
            }
        } else {
            $sum_insure = '0';
            if ($relation_id == 2 || $relation_id == 3) {
                if ($designation_res['min_age'] <= $age || $designation_res['max_age'] >= $age) {
                    if ($designation_res['relation_id'] == 2 || $designation_res['relation_id'] == 3) {
                        $employee_policy_mem_sum_premium = ($designation_res['topremium'] * $designation_res['employee_contri']) / 100;
                        $employer_policy_mem_sum_premium = ($designation_res['topremium'] * $designation_res['employer_contri']) / 100;
                    } else {
                        $sum_premium = $designation_res['p_premuim'];
                    }
                } else {
                    return [
                        "Min age" => $designation_res['min_age'],
                        "Max age" => $designation_res['max_age'],
                    ];
                }
            }
        }
    }
}
if ($document_id == 'disable_child') {
    if (empty($_FILES)) {
        return [
            "status" => "Empty",
        ];
    }
    $path_parts = pathinfo($_FILES["upload_document_id"]["name"]);
    $extension = $path_parts['extension'];
    $config['max_filename'] = 0;
    $config['allowed_types'] = 'gif|png|jpg|jpeg|pdf';
    $config['file_name'] = 'upload_document_id';
    if (!is_dir(APPPATH . '/resources/uploads/policy_member/1')) {
        mkdir(APPPATH . '/resources/uploads/policy_member/1/', 0777, TRUE);
    }
    $config['upload_path'] = APPPATH . '/resources/uploads/policy_member/1/';
    $config['max_size'] = '0';
    $this->upload->initialize($config);
    $this->upload->do_upload('upload_document_id');
            // print_r($_FILES['upload_document_id']['name']); die(); 
    if ($_FILES == '') {
        $path = '';
    } else {
                //$path = '/resources/uploads/policy_member/'.$policy_numbers.'/'.$_FILES['upload_document_id']['name'];
                // $path = '/resources/uploads/policy_member/1/'.$_FILES['upload_document_id']['name'];
        $path = '/application/resources/uploads/policy_member/1/' . 'upload_document_id' . '.' . $extension;
    }
}
$arr = [
    'family_firstname' => $f_name,
    'family_lastname' => $l_name,
    'family_gender' => $gender,
    'family_dob' => date('d-m-Y', (strtotime($dob_date)))
];
$status = $this->db->where('family_id', $family_id);
$this->db->update('employee_family_details', $arr);
$arr2 = [
    'age_type' => $age_type,
    'age' => $age,
    'policy_mem_gender' => $gender,
    'policy_mem_sum_premium' => $sum_premium,
    'employer_policy_mem_sum_premium' => $employer_policy_mem_sum_premium,
    'employee_policy_mem_sum_premium' => $employee_policy_mem_sum_premium,
    'child_condition' => $document_id,
    'policy_doc_path' => $path,
    'policy_mem_sum_insured' => $sum,
    'policy_mem_dob' => date('d-m-Y', (strtotime($dob_date)))
];

$status = $this->db->where('policy_member_id', $member_id);
$this->db->update('employee_policy_member', $arr2);
return ['status' => "successfully updated"];
}



public function fetch_relationwise_details($emp_id, $rel_id){

    $response = $this->db->select('*')
    ->from('employee_family_details as efd,family_relation as fr')
    ->where('efd.family_id = fr.family_id')
    ->where('fr_id', $rel_id)
    ->where('fr.emp_id', base64_decode($emp_id))
    ->order_by('efd.family_id',desc)
    ->get()->result();
    return $response;
}

    // function get_min_max_age($policy_id, $rel_id) {
        // $data = $this->db
                // ->select('*')
                // ->from('policy_age_limit as pal')
                // ->where('pal.policy_detail_id', $policy_id)
                // ->where('pal.relation_id', $rel_id)
                // ->get()
                // ->row_array();
                //echo $this->db->last_query();exit;
        // return $data;
    // }

function policy_parent_data($emp_id, $rel_id) {
    extract($this->input->post(NULL, true));
        //var_dump($this->input->post());exit;
    $result_data = [];
    $data = $this->db
    ->select('employee_policy_detail.policy_detail_id,employee_policy_detail.addition_premium,employee_policy_detail.flex_allocate,employee_policy_detail.payroll_allocate,employee_policy_detail.premium')
    ->from('employee_policy_member,employee_policy_detail,family_relation,master_policy_sub_type')
    ->where('employee_policy_detail.policy_detail_id = employee_policy_member.policy_detail_id')
    ->where('family_relation.family_relation_id = employee_policy_member.family_relation_id')
    ->where('family_relation.emp_id', base64_decode($emp_id))
    ->where('master_policy_sub_type.policy_sub_type_id = employee_policy_detail.policy_sub_type_id')
    ->where('employee_policy_detail.policy_sub_type_id', 1)
    ->group_by('employee_policy_detail.policy_no')
    ->get()
    ->result_array();
    if (!empty($data)) {
        if ($data[0]['addition_premium'] == 1) {
            $data_age_limit = $this->db->select('pal.employee_contri,pal.premium')
            ->from('policy_age_limit as pal')
            ->where('pal.policy_detail_id', $data[0]['policy_detail_id'])
            ->where('pal.relation_id', $rel_id)
            ->get()
            ->result_array();
            if (!empty($data_age_limit)) {
                $result_data = [
                    'employee_contri' => $data_age_limit[0]['employee_contri'],
                    'premium' => $data_age_limit[0]['premium'],
                    'flex_allocate' => $data[0]['flex_allocate'],
                    'payroll_allocate' => $data[0]['payroll_allocate']
                ];
            }
        } else {
            $result_data = [
                'employee_contri' => 0,
                'premium' => $data[0]['premium'],
                'flex_allocate' => $data[0]['flex_allocate'],
                'payroll_allocate' => $data[0]['payroll_allocate']
            ];
        }
    }
    return $result_data;
}

function get_family_details_from_relationship() {
    extract($this->input->post(null, true));
    if ($rel_id == 0) {
        $response = $this->db->select('ed.emp_id,ed.emp_code,ed.emp_firstname,ed.emp_lastname,ed.fr_id,ed.company_id,ed.gender,ed.bdate,ed.mob_no,ed.email,ed.emp_grade,ed.emp_designation,ed.emp_address,ed.emp_city,ed.emp_state,ed.emp_pincode,ed.street,ed.location,ed.flex_amount,ed.total_salary,ed.gmc_grade_id,ed.emp_pay,ed.doj,fr.family_relation_id,fr.emp_id,fr.family_id')
        ->from('employee_details as ed,family_relation as fr')
        ->where('ed.emp_id = fr.emp_id')
        ->where('fr.family_id', 0)
        ->where('fr.emp_id', base64_decode($emp_id))
        ->get()->result_array();
        return $response;
    } else {
        $response = $this->db->select('*')
        ->from('employee_family_details as efd,family_relation as fr')
        ->where('efd.family_id = fr.family_id')
        ->where('fr_id', $rel_id)
        ->where('fr.emp_id', base64_decode($emp_id))
        ->get()->result_array();
        return $response;
    }
}

    // Nominee added for employee

public function get_share_per_nominee($emp_id) {
    extract($this->input->post(NULL, true));
    $response = $this->db->select('sum(share_percentile) as sum')
    ->from('member_policy_nominee AS mpn')
    ->where('mpn.status', 'active')
    ->where('mpn.emp_id', base64_decode($emp_id))
    ->get()->result_array();
                        // echo $this->db->last_query();exit;
                        // echo "<pre>";
                        // print_r($response);
                        // echo "</pre>";

    if (!empty($response[0]['sum'])) {
          //echo $response[0]['sum'];
      return $response[0]['sum'];
  } else {
      return 0;
          //echo 0;
  }
}


function add_nominee() {
  extract($this->input->post(NULL, true));
  $emp_id = base64_decode($this->input->post('emp_id'));

  $arr = [];
  $check = "";
  $check1 = "";
  $this->db->trans_begin();
        // print_r($_POST);
  $family_members_idArr = json_decode($family_members_idArr);
  $first_nameArr = json_decode($first_nameArr);
  $last_nameArr = json_decode($last_nameArr);        
  $family_date_birthArr = json_decode($family_date_birthArr);
  $share_perArr = json_decode($share_perArr);
  $guardian_relationArr = json_decode($guardian_relationArr);
  $guardian_fnameArr = json_decode($guardian_fnameArr);
  $guardian_lnameArr = json_decode($guardian_lnameArr);
  $guardian_dobArr = json_decode($guardian_dobArr);

  for ($i=0; $i < count($family_members_idArr); $i++) { 

    // if (((isset($family_members_idArr[$i])) && empty($family_members_idArr[$i])) && ((isset($first_nameArr[$i])) && empty($first_nameArr[$i])) && ((isset($last_nameArr[$i])) && empty($last_nameArr[$i])) && ((isset($family_date_birthArr[$i])) && empty($family_date_birthArr[$i])) && ((isset($share_perArr[$i])) && empty($share_perArr[$i])) && ((isset($guardian_relationArr[$i])) && empty($guardian_relationArr[$i])) && ((isset($guardian_fnameArr[$i])) && empty($guardian_fnameArr[$i])) && ((isset($guardian_lnameArr[$i])) && empty($guardian_lnameArr[$i])) && ((isset($guardian_dobArr[$i])) && empty($guardian_dobArr[$i]))) {
    //     return (["status" => "error"]);
    // } else if (((isset($guardian_relationArr[$i])) && empty($guardian_relationArr[$i])) && ((isset($guardian_fnameArr[$i])) && empty($guardian_fnameArr[$i])) && ((isset($guardian_lnameArr[$i])) && empty($guardian_lnameArr[$i])) && ((isset($guardian_dobArr[$i])) && empty($guardian_dobArr[$i]))) {
    //     $check = 'true';
    // }else if (((isset($guardian_relationArr[$i])) && empty($guardian_relationArr[$i])) && ((isset($guardian_fnameArr[$i])) && empty($guardian_fnameArr[$i])) || ((isset($guardian_lnameArr[$i])) && empty($guardian_lnameArr[$i])) || ((isset($guardian_dobArr[$i])) && empty($guardian_dobArr[$i]))) {
    //     $check = 'true';
    // }else {
        $response_share = $this->db->select('sum(share_percentile) as sum')
        ->from('member_policy_nominee as mpn')
        ->where('mpn.emp_id', $emp_id)
        ->where('mpn.status', 'active')
        ->get()->result_array();
                                // echo $this->last_query();
        if ($response_share[0]['sum'] == 100 || $response_share[0]['sum'] >= 100) {
            return (["status" => "false"]);
        } else {
            if ((!empty($guardian_relationArr[$i])) && (!empty($guardian_fnameArr[$i])) && (!empty($guardian_lnameArr[$i])) && (!empty($guardian_dobArr[$i]))) {
                $array_nominee = [
                    'emp_id' => $emp_id,
                    //'nominee_dob' => $family_date_birthArr[$i],
                    'nominee_dob' => date('Y-m-d',strtotime($family_date_birthArr[$i])),
                    'fr_id' => $family_members_idArr[$i],
                    'nominee_fname' => $first_nameArr[$i],
                    'nominee_lname' => $last_nameArr[$i],
                    'share_percentile' => $share_perArr[$i],
                    'guardian_relation' => $guardian_relationArr[$i],
                    'guardian_fname' => $guardian_fnameArr[$i],
                    'guardian_lname' => $guardian_lnameArr[$i],
                    'guardian_dob' => $guardian_dobArr[$i],
                    'status' => 'active',
                    'confirmed_flag' => 'N'
                ];
            } else {
                $array_nominee = [
                    'emp_id' => $emp_id,
                    //'nominee_dob' => $family_date_birthArr[$i],
                    'nominee_dob' => date('Y-m-d',strtotime($family_date_birthArr[$i])),
                    'fr_id' => $family_members_idArr[$i],
                    'nominee_fname' => $first_nameArr[$i],
                    'nominee_lname' => $last_nameArr[$i],
                    'share_percentile' => $share_perArr[$i],
                    'status' => 'active',
                    'confirmed_flag' => 'N'
                ];
            }
            $status = $this->db->insert('member_policy_nominee', $array_nominee);
            $last_inserted_nominee_id = $this->db->insert_id();
            $get_policy_id = $this->db
            ->select('epd.policy_detail_id,epd.policy_no')
            ->from('employee_details AS ed,employee_policy_member as epm,
              employee_policy_detail as epd,family_relation as fr')
            ->where('fr.family_relation_id = epm.family_relation_id')
            ->where('epm.policy_detail_id = epd.policy_detail_id')
            ->where('ed.emp_id', $emp_id)
            ->where('fr.emp_id', $emp_id)
            ->where('fr.family_id', 0)
            ->get()
            ->result_array();
            if (empty($get_policy_id)) {
                $check1 = 'true';
            } else {
                foreach ($get_policy_id as $key => $value_data) {
                    $array = array(
                        'nominee_id' => $last_inserted_nominee_id,
                        'policy_id' => $value_data['policy_detail_id'],
                        'share_per' => $share_perArr[$i],
                        'flag' => 'I');
                    $status = $this->db->insert('nominee_with_policy', $array);


                    if ($status) {
                        $arr['msg'] = true;
                    } else {
                        $arr['msg'] = false;
                    }
                }
            }
        }
    // }
}
if ($check == 'true') {
    $this->db->trans_rollback();
    return (["status" => "error"]);
} else if ($check1 == 'true') {
    $this->db->trans_rollback();
    return (["status" => "error1"]);
}
$this->db->trans_complete();
return $arr;
}


function deactivate_replace_add_nominee() 
{
    extract($this->input->post(NULL, true));
    $emp_id = base64_decode($this->input->post('emp_id'));

    $arr = [];
    $check = "";
    $check1 = "";
    $this->db->trans_begin();

    for ($i=0; $i < count($family_members_idArr); $i++) 
    { 

        // if (((isset($family_members_idArr[$i])) && empty($family_members_idArr[$i])) && ((isset($first_nameArr[$i])) && empty($first_nameArr[$i])) && ((isset($last_nameArr[$i])) && empty($last_nameArr[$i])) && ((isset($family_date_birthArr[$i])) && empty($family_date_birthArr[$i])) && ((isset($share_perArr[$i])) && empty($share_perArr[$i])) && ((isset($guardian_relationArr[$i])) && empty($guardian_relationArr[$i])) && ((isset($guardian_fnameArr[$i])) && empty($guardian_fnameArr[$i])) && ((isset($guardian_lnameArr[$i])) && empty($guardian_lnameArr[$i])) && ((isset($guardian_dobArr[$i])) && empty($guardian_dobArr[$i]))) {
        //     return (["status" => "error"]);
        // } else if (((isset($guardian_relationArr[$i])) && empty($guardian_relationArr[$i])) && ((isset($guardian_fnameArr[$i])) && empty($guardian_fnameArr[$i])) && ((isset($guardian_lnameArr[$i])) && empty($guardian_lnameArr[$i])) && ((isset($guardian_dobArr[$i])) && empty($guardian_dobArr[$i]))) {
        //     $check = 'true';
        // }else if (((isset($guardian_relationArr[$i])) && empty($guardian_relationArr[$i])) && ((isset($guardian_fnameArr[$i])) && empty($guardian_fnameArr[$i])) || ((isset($guardian_lnameArr[$i])) && empty($guardian_lnameArr[$i])) || ((isset($guardian_dobArr[$i])) && empty($guardian_dobArr[$i]))) {
        //     $check = 'true';
        // } else 
        // {
            if ((!empty($guardian_relationArr[$i])) && (!empty($guardian_fnameArr[$i])) && (!empty($guardian_lnameArr[$i])) && (!empty($guardian_dobArr[$i]))) {

                $array_nominee = [
                    'emp_id' => $emp_id,
                    //'nominee_dob' => $family_date_birthArr[$i],
                    'nominee_dob' => date('Y-m-d',strtotime($family_date_birthArr[$i])),
                    'fr_id' => $family_members_idArr[$i],
                    'nominee_fname' => $first_nameArr[$i],
                    'nominee_lname' => $last_nameArr[$i],
                    'share_percentile' => $share_perArr[$i],
                    'guardian_relation' => $guardian_relationArr[$i],
                    'guardian_fname' => $guardian_fnameArr[$i],
                    'guardian_lname' => $guardian_lnameArr[$i],
                    'guardian_dob' => $guardian_dobArr[$i],
                    'status' => 'active',
                    'confirmed_flag' => 'N'
                ];
            } else {
                $array_nominee = [
                    'emp_id' => $emp_id,
                    //'nominee_dob' => $family_date_birthArr[$i],
                    'nominee_dob' => date('Y-m-d',strtotime($family_date_birthArr[$i])),
                    'fr_id' => $family_members_idArr[$i],
                    'nominee_fname' => $first_nameArr[$i],
                    'nominee_lname' => $last_nameArr[$i],
                    'share_percentile' => $share_perArr[$i],
                    'status' => 'active',
                    'confirmed_flag' => 'N'
                ];
            }


            $status = $this->db->insert('member_policy_nominee', $array_nominee);
            $last_inserted_nominee_id = $this->db->insert_id();
            $get_policy_id = $this->db
            ->select('epd.policy_detail_id,epd.policy_no')
            ->from('employee_details AS ed,employee_policy_member as epm,
                employee_policy_detail as epd,family_relation as fr')
            ->where('fr.family_relation_id = epm.family_relation_id')
            ->where('epm.policy_detail_id = epd.policy_detail_id')
            ->where('ed.emp_id', $emp_id)
            ->where('fr.emp_id', $emp_id)
            ->where('fr.family_id', 0)
            ->get()
            ->result_array();
            if (empty($get_policy_id)) {
                $check1 = 'true';
            } else {
                foreach ($get_policy_id as $key => $value_data) {
                    $array = array(
                        'nominee_id' => $last_inserted_nominee_id,
                        'policy_id' => $value_data['policy_detail_id'],
                        'share_per' => $share_perArr[$i],
                        'flag' => 'I'
                    );
                    $status = $this->db->insert('nominee_with_policy', $array);
                }
            }

            if ($status) {
                $arr['msg'] = true;
            } else {
                $arr['msg'] = false;
            }

        // }
    }
    if ($check == 'true') {
        $this->db->trans_rollback();
        return (["status" => "error"]);
    } else if ($check1 == 'true') {
        $this->db->trans_rollback();
        return (["status" => "error1"]);
    }
    $this->db->trans_complete();
    return $arr;
}

function update_nominee_data() 
{
    extract($this->input->post(NULL, true));
    $emp_id = base64_decode($this->input->post('emp_id'));
    $today = date("Y-m-d");
    $diff = date_diff(date_create($nominee_date), date_create($today));
        // print_r($diff);
    $age_distance = $diff->y;
        //print_r($age_distance);exit;
    $arr = [];
    if ($age_distance <= 18) {
        if ((isset($guardian_name) && empty($guardian_name)) && (isset($guardina_date) && empty($guardina_date)) && (isset($guardian_rel) && empty($guardian_rel)) && (isset($rel_nominee) && empty($rel_nominee)) && (isset($nominee_firstname) && empty($nominee_firstname)) && (isset($nominee_lastname) && empty($nominee_lastname)) && (isset($nominee_date) && empty($nominee_date))) {
            $arr['error'] = 'please fill the Nominee and Guardian details';
        } else if ((isset($guardian_name) && empty($guardian_name)) && (isset($guardina_date) && empty($guardina_date)) && (isset($guardian_rel) && empty($guardian_rel))) {
            $arr['error'] = 'please fill the guardian details';
        } else if ((isset($guardian_name) && empty($guardian_name)) || (isset($guardina_date) && empty($guardina_date)) || (isset($guardian_rel) && empty($guardian_rel))) {
            $arr['error'] = 'please fill the guardian details';
        } else if ((isset($rel_nominee) && empty($rel_nominee)) || (isset($nominee_firstname) && empty($nominee_firstname)) || (isset($nominee_lastname) && empty($nominee_lastname)) || (isset($nominee_date) && empty($nominee_date))) {
            $arr['error'] = 'please fill the Nominee details';
        } else {
            $data_present = $this->db->select('*')
            ->from('member_policy_nominee AS mpn')
            ->where('mpn.nominee_id', $nomineeid)
            ->where('mpn.emp_id', $emp_id)
            ->get()->result_array();
                                // echo $this->db->last_query();exit;
            if (!empty($data_present)) {
                $udata = array(
                    'nominee_fname' => $nominee_firstname,
                    'nominee_lname' => $nominee_lastname,
                    'fr_id' => $rel_nominee,
                    'nominee_dob' => $nominee_date,
                    'guardian_relation' => $guardian_rel,
                    'guardian_fname' => $guardian_name,
                    'guardian_lname' => $data_present[0]['guardian_lname'],
                    'guardian_dob' => $guardina_date
                );
                $where = array(
                    'emp_id' => $emp_id,
                    'nominee_id' => $nomineeid
                );
                $data = $this->db->update('member_policy_nominee', $udata, $where);
                $data_nominee_policy = $this->db->update('nominee_with_policy', array('flag' => 'U'), array('nominee_id' => $nomineeid));
                if ($data) {
                    $arr['success'] = 'Nominee Details Updated Successfully!';
                } else {
                    $arr['error'] = 'Oops Something Went Wrong!';
                }
            }
        }
    } else {
        if ((isset($rel_nominee) && empty($rel_nominee)) || (isset($nominee_firstname) && empty($nominee_firstname)) || (isset($nominee_lastname) && empty($nominee_lastname)) || (isset($nominee_date) && empty($nominee_date))) {
            $arr['error'] = 'please fill the Nominee details';
        } else if ((isset($rel_nominee) && empty($rel_nominee)) || (isset($nominee_firstname) && empty($nominee_firstname)) || (isset($nominee_lastname) && empty($nominee_lastname)) || (isset($nominee_date) && empty($nominee_date))) {
            $arr['error'] = 'please fill the Nominee details';
        } else if ((!empty($guardian_name)) && (!empty($guardina_date)) && (!empty($guardian_rel))) {
            $arr['error'] = 'Nominee above 18 years no need to fill guardian details';
        } else if ((!empty($guardian_name)) || (!empty($guardina_date)) || (!empty($guardian_rel))) {
            $arr['error'] = 'Nominee above 18 years no need to fill guardian details';
        } else {
            $data_present = $this->db->select('*')
            ->from('member_policy_nominee AS mpn')
            ->where('mpn.nominee_id', $nomineeid)
            ->where('mpn.emp_id', $emp_id)
            ->get()->result_array();
            $udata = array(
                'nominee_fname' => $nominee_firstname,
                'nominee_lname' => $nominee_lastname,
                'fr_id' => $rel_nominee,
                'nominee_dob' => $nominee_date,
                'guardian_relation' => '',
                'guardian_fname' => '',
                'guardian_lname' => '',
                'guardian_dob' => ''
            );
            $where = array(
                'emp_id' => $emp_id,
                'nominee_id' => $nomineeid
            );
            $data = $this->db->update('member_policy_nominee', $udata, $where);
            $data_nominee_policy = $this->db->update('nominee_with_policy', array('flag' => 'U'), array('nominee_id' => $nomineeid));
            if ($data) {
                $arr['success'] = 'Nominee Details Updated Successfully!';
            } else {
                $arr['error'] = 'Oops Something Went Wrong!';
            }
        }
    }
    return $arr;
}

public function get_covers_details($emp_id){
  $result = $this->db->select("mpst.policy_sub_type_name,epd.sum_insured,epd.premium, mc.flex_allocate, mc.payroll_allocate,mpst.policy_sub_type_id")
  ->from('employee_details AS ed, employee_policy_detail as epd, master_policy_sub_type as mpst, master_company as mc')
  ->where("ed.emp_id", base64_decode($emp_id))
  ->where('epd.company_id = ed.company_id')
  ->where('mc.company_id = ed.company_id')
  ->where('epd.policy_type_id', 2)
  ->where('mpst.policy_sub_type_id = epd.policy_sub_type_id')
  ->get()
  ->result_array();
                        // echo $this->db->last_query();exit;
  return $result;
}

public function getGtliTopUpcalc($sumValue) {
    extract($this->input->post(null, true));

    $query = $this->db->select("bdate")->where(["emp_id" => base64_decode($emp_id)])->get("employee_details")->row();
    
    $from = new DateTime($query->bdate);
    $to = new DateTime('today');

    $query1 = $this->db->where(["emp_age" => $from->diff($to)->y])->get("gtli_topup_premium_calc")->row();
        // print_pre($query1);

    $gtlRate = $sumValue;
    $ciRate = 10;

        //10 lakh will be for critical
    $gtlRate -= 1000000;
        // $gtlRate *= 100000;
    $gtlRate /= 1000;

    $gtlRate *= $query1->gtli_rate;

        // echo $gtlRate."<br/>";
        //critical rate calc
    $ciRate *= 100000;
    $ciRate /= 1000;
    $ciRate *= $query1->ci_rate;

        // echo $ciRate."<br/>";

    $sumValue = $gtlRate + $ciRate;

    $sumValue += $sumValue * 0.18;
        // $sumValue +/ 1000;

    return round($sumValue);
}

function save_all_data($benifit_type,$name,$transac_type){
  extract($this->input->post(NULL,true));
      //print_r($this->input->post());exit;
      $emp_id = base64_decode($this->input->post('emp_id'));//employee_id
       // var_dump($emp_id);exit;
       // print_r($this->input->post());exit;
      if ($amount == 0) 
      {
          //echo 111;
        $select_flex_data = $this->db->get_where('employee_flexi_benefit_transaction',array('employee_flexi_benifit_id'=>$benifit_type))->result_array();
        if(!empty($select_flex_data))
        {
            $del_data = $this->db->delete('employee_flexi_benefit_transaction',array('employee_flexi_benifit_id'=>$benifit_type));
            if ($del_data) 
            {
               $select_all_data = $this->db
               ->select('SUM(pay_amount) As pay_amount,SUM(flex_amount) As flex_amount')
               ->from('employee_flexi_benefit_transaction')
               ->where('emp_id',$emp_id)
               ->get()
               ->result_array();
               if (empty($select_all_data[0]['pay_amount']) && empty($select_all_data[0]['flex_amount'])) 
               {
                  $select_all_data[0] = array(
                    'pay_amount' => 0,
                    'flex_amount' => 0 
                );
              }
              else if(empty($select_all_data[0]['pay_amount']))
              {
                  $select_all_data[0] = array(
                    'pay_amount' => 0,
                    'flex_amount' => $select_all_data[0]['flex_amount']
                );
              }
              else if(empty($select_all_data[0]['flex_amount']))
              {
                  $select_all_data[0] = array(
                    'pay_amount' => $select_all_data[0]['pay_amount'],
                    'flex_amount' => 0
                );
              }
              return $select_all_data[0];         
          }
          else
          {
            return false;
        }
    }
    else
    {
      return false;
  }
}
else
{
            // var_dump($pay_amount);exit;
   if(isset($sum_insured) && !empty($sum_insured))
   {
      $sum_in = $sum_insured;
  }
  else
  {
      $sum_in = ' ';
  }

  if (isset($fr_id) && !empty($fr_id)) 
  {
      $frid = implode(",", $fr_id);
  }
  else
  {
      $frid = '';
  }

  if (isset($flex_amount) && !empty($flex_amount)) 
  {
     $flexamount = $flex_amount;
                 //var_dump($flexamount);exit;
 }
 else
 {
  $flexamount = 0;
}

if (isset($pay_amount) && !empty($pay_amount)) 
{
 $payamount = $pay_amount;
}
else
{
  $payamount = 0;
}

$select_flex_data = $this->db->get_where('employee_flexi_benefit_transaction',array('employee_flexi_benifit_id'=>$benifit_type))->result_array();
               // echo $this->db->last_query();exit;
if (empty($select_flex_data))
{
                  //echo 333;
   $inserted_array = array(
    'emp_id' => $emp_id,
    'fr_id' => $frid,
    'employee_flexi_benifit_id' => $benifit_type,
    'flex_amount' => $flexamount,
    'pay_amount' => $payamount,
    'transac_type' => $transac_type,
    'final_amount' => $amount,
    'balance_amount' => $amount,
    'deduction_type' => $deduction_type,
    'sum_insured' => $sum_in,
    'allocated_flag' => 'Y',
    'confirmed_flag' => 'N',
    'ip_address' => $this->input->ip_address(),
    'allocated_created_at' => date('Y-m-d H:i:s')
);
   $result = $this->db->insert('employee_flexi_benefit_transaction',$inserted_array);
}
else
{
  $updated_array = array(
    'emp_id' => $emp_id,
    'fr_id' => $frid,
    'flex_amount' => $flexamount,
    'pay_amount' => $payamount,
    'final_amount' => $amount,
    'transac_type' => $transac_type,
    'balance_amount' => $amount,
    'deduction_type' => $deduction_type,
    'sum_insured' => $sum_in,
    'allocated_flag' => 'Y',
    'confirmed_flag' => 'N',
    'ip_address' => $this->input->ip_address(),
    'allocated_updated_at' => date('Y-m-d H:i:s')
);
  $where_array = array(
    'employee_flexi_benifit_id' => $benifit_type,
);
  $result = $this->db->update('employee_flexi_benefit_transaction',$updated_array,$where_array);
}

if ($result) 
{
 $select_all_data = $this->db
 ->select('SUM(pay_amount) As pay_amount,SUM(flex_amount) As flex_amount')
 ->from('employee_flexi_benefit_transaction')
 ->where('emp_id',$emp_id)
 ->get()
 ->result_array();
 return $select_all_data[0];         
}
else
{
  return false;
}
}

}

public function fetch_emp_pay_employeewise($emp_id){
  $result = $this->db->select('emp_pay, total_salary')->from('employee_details as ed')
            // ->where('mfr.fr_id = mpn.fr_id')
            // ->where('ed.emp_status', 1)
  ->where("ed.emp_id", $emp_id)
  ->get()
  ->result_array();
            //echo $this->db->last_query();exit;
  return $result;
}

public function reset_flexi_data($benifit_type)
{
  extract($this->input->post(NULL,true));
      // print_r($_POST);exit;
      $emp_id = base64_decode($this->input->post('emp_id'));//employee_id
      $udata = array(
        'final_amount' => 0,
        'flex_amount' => 0,
        'pay_amount' => 0,
        'balance_amount' => 0,
        'sum_insured' => 0,
        'deduction_type' => ''
    );
      $this->db->delete('employee_flexi_benefit_transaction',array('employee_flexi_benifit_id'=>$benifit_type,'emp_id'=>$emp_id));
      // echo $this->db->last_query();exit;
      $select_all_data = $this->db
      ->select('SUM(pay_amount) As pay_amount,SUM(flex_amount) As flex_amount')
      ->from('employee_flexi_benefit_transaction')
      ->get()
      ->result_array();
      return $select_all_data[0];  
      if ($benefit == 5) 
      {
          $flex_data = $this->db
          ->select('epm.policy_member_id')
          ->from('master_policy_sub_type as mpst,employee_policy_detail as epd,
            employee_policy_member as epm,family_relation as fr')
          ->where('mpst.policy_sub_type_id = epd.policy_sub_type_id')
          ->where('epd.policy_detail_id = epm.policy_detail_id')
          ->where('epm.family_relation_id = fr.family_relation_id')
          ->where('mpst.policy_sub_type_id',6)
          ->where('fr.emp_id', $emp_id)
          ->get()
          ->result_array();
          $this->db->delete('employee_policy_member',array('policy_member_id'=>$flex_data[0]['policy_member_id']));     
          $select_all_data = $this->db
          ->select('SUM(pay_amount) As pay_amount,SUM(flex_amount) As flex_amount')
          ->from('employee_flexi_benefit_transaction')
          ->get()
          ->result_array();
          return $select_all_data[0];        
      }

      if ($benefit == 4) 
      {
          $flex_data = $this->db
          ->select('epm.policy_member_id')
          ->from('master_policy_sub_type as mpst,employee_policy_detail as epd,
            employee_policy_member as epm,family_relation as fr')
          ->where('mpst.policy_sub_type_id = epd.policy_sub_type_id')
          ->where('epd.policy_detail_id = epm.policy_detail_id')
          ->where('epm.family_relation_id = fr.family_relation_id')
          ->where('mpst.policy_sub_type_id',5)
          ->where('fr.emp_id',$emp_id)
          ->get()
          ->result_array();
          $this->db->delete('employee_policy_member',array('policy_member_id'=>$flex_data[0]['policy_member_id'])); 
          $select_all_data = $this->db
          ->select('SUM(pay_amount) As pay_amount,SUM(flex_amount) As flex_amount')
          ->from('employee_flexi_benefit_transaction')
          ->get()
          ->result_array();
          return $select_all_data[0];   
      }

      if ($benefit == 3) 
      {
        $flex_data = $this->db
        ->select('epm.policy_member_id')
        ->from('master_policy_sub_type as mpst,employee_policy_detail as epd,
            employee_policy_member as epm,family_relation as fr')
        ->where('mpst.policy_sub_type_id = epd.policy_sub_type_id')
        ->where('epd.policy_detail_id = epm.policy_detail_id')
        ->where('epm.family_relation_id = fr.family_relation_id')
        ->where('mpst.policy_sub_type_id',4)
        ->where('fr.emp_id',$emp_id)
        ->get()
        ->result_array();
        foreach ($flex_data as $key => $value) {
            $this->db->delete('employee_policy_member',array('policy_member_id'=>$value['policy_member_id'])); 
        }
        $select_all_data = $this->db
        ->select('SUM(pay_amount) As pay_amount,SUM(flex_amount) As flex_amount')
        ->from('employee_flexi_benefit_transaction')
        ->get()
        ->result_array();
        return $select_all_data[0];   
    }
    if ($benefit == 2) 
    {
      $get_parent_data = $this->db->get_where('employee_flexi_benefit_transaction',array('employee_flexi_benifit_id'=>$benifit_type,'emp_id'=>$emp_id))->result_array();
      $fr_id = explode(",", $get_parent_data[0]['fr_id']);
      foreach ($fr_id as $key => $value) {
        $flex_data = $this->db
        ->select('epm.policy_member_id')
        ->from('master_policy_sub_type as mpst,employee_policy_detail as epd,
            employee_policy_member as epm,family_relation as fr,master_family_relation as mfr,employee_family_details as efd')
        ->where('mpst.policy_sub_type_id = epd.policy_sub_type_id')
        ->where('epd.policy_detail_id = epm.policy_detail_id')
        ->where('epm.family_relation_id = fr.family_relation_id')
        ->where('efd.family_id = fr.family_id')
        ->where('mpst.policy_sub_type_id',1)
        ->where('fr.emp_id',$emp_id)
        ->where('efd.fr_id',$value)
        ->group_by('efd.fr_id')
        ->get()
        ->result_array();
        $flex_data_topup = $this->db
        ->select('epm.policy_member_id')
        ->from('master_policy_sub_type as mpst,employee_policy_detail as epd,
            employee_policy_member as epm,family_relation as fr,master_family_relation as mfr,employee_family_details as efd')
        ->where('mpst.policy_sub_type_id = epd.policy_sub_type_id')
        ->where('epd.policy_detail_id = epm.policy_detail_id')
        ->where('epm.family_relation_id = fr.family_relation_id')
        ->where('efd.family_id = fr.family_id')
        ->where('mpst.policy_sub_type_id',4)
        ->where('fr.emp_id',$emp_id)
        ->where('efd.fr_id',$value)
        ->group_by('efd.fr_id')
        ->get()
        ->result_array();

        $this->db->delete('employee_policy_member',array('policy_member_id'=>$flex_data[0]['policy_member_id'])); 
        $this->db->delete('employee_policy_member',array('policy_member_id'=>$flex_data_topup[0]['policy_member_id'])); 

    }
    $updata = array(
        'fr_id' => 0
    );
    $this->db->delete('employee_flexi_benefit_transaction',array(' employee_flexi_benifit_id'=>$benifit_type,'emp_id'=>$emp_id));
    $select_all_data = $this->db
    ->select('SUM(pay_amount) As pay_amount,SUM(flex_amount) As flex_amount')
    ->from('employee_flexi_benefit_transaction')
    ->get()
    ->result_array();
    return $select_all_data[0];   
}
}

public function get_company_contactus_detail($emp_id){
    $result = $this->db->select('IFNULL(mc.company_head_name, "") as company_head_name,IFNULL(mc.company_address, "") as company_address
        ,IFNULL(mc.company_cont_name, "") as company_cont_name,
        IFNULL(mc.company_mob, "") as company_mob,
        IFNULL(mc.company_email, "") as company_email')
    ->from('employee_details ed, master_company mc')
    ->where('mc.company_id = ed.company_id')
    ->where('ed.emp_id', base64_decode($emp_id))
    ->get()
    ->result();
    return $result; 
}

public function check_employee_exists(){
  extract($this->input->post(null,true));
  $result = $this->db
  ->select("*")
  ->from('employee_family_details AS efd, family_relation as fr,master_family_relation AS mfr')
  ->where("efd.family_id = fr.family_id")
  ->where("efd.fr_id = mfr.fr_id")
  ->where("efd.fr_id", $fr_id)
  ->where("fr.emp_id ", base64_decode($emp_id))
  ->get()
  ->result_array();
        //echo $this->db->last_query(); die();
  
  return $result;
}
}
