<?php
/*
@author of this files - Ankita <ankita.badak@fyntune.com> and Siddhi <siddhi.yendhe@fyntune.com>
*/
if (!defined('BASEPATH')) {
  exit('No direct script access allowed');
}


class GenericApi extends CI_Controller 
{

  function __construct() {
    parent::__construct();
  
   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);
    
    $this->load->model("API/GenericApi_m", "GenericApi_m");
  //$this->load->model("API/Payment_integration_freedom_plus", "obj_external_afpp", true);
  }
  
  
  public function download_coi(){
    $JSON_data = file_get_contents("php://input");
    $request = json_decode($JSON_data,true);
    
    // $request = $this->formatData($request['data']);
    // $request = json_decode($request, true);
    //print_pre($request);exit;
    /*if( $request == NULL ){
      $returnData['status'] = false;
      $returnData['errors']['proposal'][] = ['ErrorNumber' => '63', 'ErrorMessage' => 'Invalid Json.'];
      header('Content-Type: application/json');
      echo json_encode($returnData);exit;
    }
    //verify access token
    $headers = apache_request_headers();
    if(isset($headers['access_token'])){
      $res = $this->verify_access_token($headers['access_token']);      
      if($res['Error']['ErrorNumber'] == 01){
        header('Content-Type: application/json');
        echo json_encode($res);exit;
      }
    }*/
  

  $return_data = array(
            "status" => "error",
            "url" => ""
            );


    $search_by = $request['search_by'];   
    $search_value = $request['search_value'];   
  
  $file_path = APPPATH.'resources/';
  $file = 'Thanos_'.$search_value.'.pdf';
  $merge_path = $file_path.$file;
  $cmd = "gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=$merge_path ";

  if($search_by == 'lead_id'){
    $data = $this->db->query("select ed.lead_id,apr.certificate_number from employee_details as ed,proposal as p,api_proposal_response as apr where ed.emp_id = p.emp_id and p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id and p.status in('Success','Payment Received') and ed.lead_id = '$search_value' ")->result_array();
    
    
    foreach ($data as $key => $val) {
      
      $get_data = $this->obj_external_afpp->coi_download_m($val['certificate_number']);
      
      if($get_data['status'] == 'success'){
        //$arr[] = APPPATH.$get_data['url'];
        $cmd .= APPPATH.$get_data['url']." ";
      }
      
    }
    
    if($get_data['status'] == 'success'){
      
      $result = shell_exec($cmd);
      
      $return_data = array(
              "status" => "success",
              "url" => "resources/".$file
              );
    
    }
    
  }
  
  if($search_by == 'certificate_number'){
    $return_data = $this->obj_external_afpp->coi_download_m($search_value);
  }
  
  if($return_data['status'] == 'success'){
    $return_data = array(
              "status" => "success",
              "url" => base_url().$return_data['url']
              );
  }
    
  header('Content-Type: application/json');
  echo json_encode($return_data);
   
  }
  
  
  public function get_imd_code(){
    $JSON_data = file_get_contents("php://input");
    $request = json_decode($JSON_data,true);
    
    // $request = $this->formatData($request['data']);
    // $request = json_decode($request, true);
    //print_pre($request);exit;
    /*if( $request == NULL ){
      $returnData['status'] = false;
      $returnData['errors']['proposal'][] = ['ErrorNumber' => '63', 'ErrorMessage' => 'Invalid Json.'];
      header('Content-Type: application/json');
      echo json_encode($returnData);exit;
    }
    //verify access token
    $headers = apache_request_headers();
    if(isset($headers['access_token'])){
      $res = $this->verify_access_token($headers['access_token']);      
      if($res['Error']['ErrorNumber'] == 01){
        header('Content-Type: application/json');
        echo json_encode($res);exit;
      }
    }*/
  

  $return_data = array(
            "status" => "error",
            "response" => "Invalid Input"
            );


    $BranchSolId = $request['BranchSolId'];   
    $ProductCode = $request['ProductCode']; //BBAFP/BBAHP/D2CGAH/TELE  
  
  if(!empty($BranchSolId) && !empty($ProductCode)){
    
    switch ($ProductCode) {
      case "BBAFP":
      $product_id = 'R03';
      break;
      case "BBAHP":
      $product_id = 'R07';
      break;
      case "D2CGAH":
      $product_id = 'R05';
      break;
      case "TELE":
      $product_id = 'R06';
      break;
      default:
      $product_id = '';
    }

    if(!empty($product_id)){
      
      if($product_id == 'R05'){
        $this->db1 = $this->load->database('axis_retail',TRUE);
        //ImdCode Validation
        $IMDCode = $this->db1->query("select * from master_imd where BranchCode = '$BranchSolId'")->row_array();
        if (count($IMDCode) > 0) {
          $abc = 1;
        } else {
          $abc = 0;
        }
        
        if($abc == 0){
          $return_data = array(
                "status" => "error",
                "response" => "Invalid Branch code"
                );
        }    
        
        if($abc){
          $return_data = array(
                "status" => "success",
                "response" => $IMDCode['IMDCode']
                );
        }
        
      }else{
        
        //ImdCode Validation
         $imd_refer = $this->db->query("select imd_refer_product_code from product_master_with_subtype where  product_code='$product_id'")->row_array();
        $refer_prod_code = $imd_refer['imd_refer_product_code'];
         
        $IMDCode = $this->db->query("select * from master_imd where BranchCode = '$BranchSolId' AND product_code = '$refer_prod_code'")->row_array();
        if (count($IMDCode) > 0) {
          $abc = 1;
        } else {
          $abc = 0;
        }
        
        if($abc == 0){
          $return_data = array(
                "status" => "error",
                "response" => "Invalid Branch code"
                );
        }    
        
        if($abc){
          $return_data = array(
                "status" => "success",
                "response" => $IMDCode['IMDCode']
                );
        }
        
      }
      
    }else{
      $return_data = array(
              "status" => "error",
              "response" => "Invalid Product Code"
              );
    }
    
    
    
  }
  
    header('Content-Type: application/json');
  echo json_encode($return_data);
   
  }

  /*
  Created By - Ankita <ankita.badak@fyntune.com>
  Created On - 10 Aug 2020
  Updated By - Ankita <ankita.badak@fyntune.com>
  Updated On - 10 Aug 2020
  Remark - this is create_access_token function which accepts 2 parameters and returns access_token.
  */
  public function create_access_token(){
    $JSON_data = file_get_contents("php://input");
    $data = json_decode($JSON_data,true);
    //print_r($data);exit;
    $cust_id = $data['data'][0]['cust_id'];
    $cust_secret = $data['data'][0]['cust_secret'];
    $data = $this->GenericApi_m->create_access_token($cust_id,$cust_secret);
    header('Content-Type: application/json');
    echo json_encode($data);
  }

  /*
  Created By - Ankita <ankita.badak@fyntune.com>
  Created On - 10 Aug 2020
  Updated By - Ankita <ankita.badak@fyntune.com>
  Updated On - 10 Aug 2020
  Remark - this is verify_access_token function which accepts 1 parameters and verifies the access token
  */
  public function verify_access_token($access_token){   
    $data = $this->GenericApi_m->verify_access_token($access_token);
    return $data;
  }

  /*
  Created By - Ankita <ankita.badak@fyntune.com>
  Created On - 20 Aug 2020
  Updated By - Ankita <ankita.badak@fyntune.com>
  Updated On - 20 Aug 2020
  Remark - this function returns lead id
  */
  public function generate_lead_id(){
    $lead_id = time();
    $select = '*';
    $table_name = 'employee_details';
    $where = "lead_id = '".$lead_id."'";
    $result_type = 'row_array';    
    $EmployeeData = $this->GenericApi_m->common_select_query($select,$table_name,$where,$result_type);
    if(!empty($EmployeeData)){
      $this->generate_lead_id();
    }
    return $lead_id;
  }

  /*
  Created By - Ankita <ankita.badak@fyntune.com>
  Created On - 10 Aug 2020
  Updated By - Ankita <ankita.badak@fyntune.com>
  Updated On - 10 Aug 2020
  Remark - this function is used for server side validation before creating proposal
  */
  public function validate_data($proposer_data,$intermediaryCode,$singleMemberObject,$branch_sol_id,$lead_id,$policy_tenure,$ReceiptObj,$is_payment_flag,$quotation_number,$source_name,$SPID){
    //print_pre($proposer_data);exit;
    if($is_payment_flag == ''){
      $returnData['status'] = false;
      $returnData['errors']['proposal'][] = ['ErrorNumber' => '64', 'ErrorMessage' => 'is_payment_flag is required.'];
      header('Content-Type: application/json');
      echo json_encode($returnData);exit;
    }
    $allow_arr = [0,1];
    if (!in_array($is_payment_flag, $allow_arr))
    {
      $returnData['errors']['proposal'][] = ['ErrorNumber' => '67', 'ErrorMessage' => 'is_payment_flag is wrong.'];
      header('Content-Type: application/json');
      echo json_encode($returnData);exit;
    }
    //print_pre($proposer_data);exit;
    $product_id = $proposer_data['ApplicationProductcode'];
    if($product_id == ''){
      $returnData['status'] = false;
      $returnData['errors']['proposal'][] = ['ErrorNumber' => '78', 'ErrorMessage' => 'Application product code is required.'];
      header('Content-Type: application/json');
      echo json_encode($returnData);exit;
    }
    $returnArr = [];
    $responseArr = [];
    $Customer_id = $proposer_data['Member_Customer_ID'];
    $proposer_data['nominee_first_name'] = $singleMemberObject['Nominee_First_Name'];
    $proposer_data['nominee_last_name'] = $singleMemberObject['Nominee_Last_Name'];
    $proposer_data['nominee_relationship_code'] = $singleMemberObject['Nominee_Relationship_Code'];
    $proposer_data['nominee_contact_number'] = $singleMemberObject['Nominee_Contact_Number'];
    $proposer_data['lead_id'] = $lead_id;
    $proposer_data['intermediaryCode'] = $intermediaryCode;
    $proposer_data['branch_sol_id'] = $branch_sol_id;
    $proposer_data['policy_tenure'] = $policy_tenure;
    //common validation
    $source = $proposer_data['Metadata']['Sender']['Source'];
    if($source == ''){
      //$returnArr['42'] = 'Source is required.';  
      $returnArr[] = array('ErrorNumber'=>'42','ErrorMessage'=> 'Source is required.');  
    }
    $mandatory = array( '02'=>'salutation',
                        '03' =>'firstName',
                        '10' =>'dateofBirth',
                        '09' =>'gender',
                        '11' => 'primaryEmailID',
                        '13' => 'contactMobileNo',
                        '06' =>'homeAddressLine1',
                        /*'21' => 'nominee_first_name',
                        '24' => 'nominee_relationship_code',
                        '40' => 'nominee_contact_number',*/
                        '43' => 'Member_Customer_ID',
                        '17' => 'ApplicationProductcode',
                        '01' => 'lead_id',
                        '63' => 'policy_tenure',
                        '65' => 'nationality',
                        '66' => 'Is_NRI',
                        '08' => 'pinCode');     
    
    foreach ($mandatory as $error_code => $field) {
      if($proposer_data[$field] == ''){
        //$returnArr[$error_code] =  str_replace('_', ' ', $field).' is required.';
        $returnArr[] = array('ErrorNumber'=>$error_code,'ErrorMessage'=> str_replace('_', ' ', $field).' is required.'); 
      }
    }

    if($proposer_data['firstName'] != ''){
      if(strlen($proposer_data['firstName']) >= 30){
         //$returnArr['04'] = 'First Name is blank or contains more than 30 characters';
          $returnArr[] = array('ErrorNumber'=>'04','ErrorMessage'=>'First Name is blank or contains more than 30 characters');  
      } 
    }

    if($proposer_data['lastName'] != ''){
      if(strlen($proposer_data['lastName']) >= 30){
        //$returnArr['05'] = 'Last Name contains more than 30 characters';  
        $returnArr[] = array('ErrorNumber'=>'05','ErrorMessage'=>'Last Name contains more than 30 characters'); 
      }
    }

    if($proposer_data['homeAddressLine1'] != ''){
      if(preg_match('/[{}\\\\"]/',$proposer_data['homeAddressLine1'])){
        //$returnArr['07'] = 'Address contains special characters list which is not allowed is \, {, }, "';   
        $returnArr[] = array('ErrorNumber'=>'07','ErrorMessage'=>'Address contains special characters list which is not allowed is \, {, }, "');
      } 
    }

    if($proposer_data['primaryEmailID'] != ''){
      if (!filter_var($proposer_data['primaryEmailID'], FILTER_VALIDATE_EMAIL)) {
         $returnArr[] = array('ErrorNumber'=>'12','ErrorMessage'=>'Email address is not valid');
      }
    }

    // Nominee Validation
    /*if(strlen($proposer_data['nominee_first_name']) >= 30){
      // $returnArr['22'] = 'Nominee First Name contains more than 30 characters';
    $returnArr[] = array('ErrorNumber'=>'22','ErrorMessage'=>'Nominee First Name contains more than 30 characters');
    }
    if($proposer_data['nominee_last_name'] != ''){
      if(strlen($proposer_data['nominee_last_name']) >= 30){
        // $returnArr['23'] = 'Nominee Last Name contains more than 30 characters';
    $returnArr[] = array('ErrorNumber'=>'23','ErrorMessage'=>'Nominee Last Name contains more than 30 characters');
      }
    } */

    $already_cust_id = $this->db->where(["CustomerID" => $Customer_id])->get("api_proposal_response")->num_rows();

    if($already_cust_id > 0){
      $returnArr[] = array('ErrorNumber'=>'39','ErrorMessage'=>'This policy cannot be processed since the said proposer has already purchased this policy');
      //$returnArr['39'] = 'This policy cannot be processed since the said proposer has already purchased this policy';  
    }

    //GHD validationThis policy cannot be processed since the said proposer has already purchased this policy
    /*if($proposer_data['GHD_flag'] != 0){
      $returnArr['error'] = ['code' => '27', 'Description' => 'GHD should be No'];     
    }*/

    //contact number validations
    if($proposer_data['Is_NRI'] == 'N'){
      if($proposer_data['contactMobileNo'] != ''){
        /*if(strlen($proposer_data['nominee_contact_number']) != 10){
          $returnArr['41'] = 'Nominee contact number length should be 10 digit.';
      
        }else*/
        if (strlen($proposer_data['contactMobileNo']) != 10) {
          //$returnArr['14'] = 'primary contact number length should be 10 digit.';
          $returnArr[] = array('ErrorNumber'=>'14','ErrorMessage'=>'primary contact number length should be 10 digit.');
        } 
      }
    }else{
      if($proposer_data['contactMobileNo'] != ''){
        /*if (strlen($proposer_data['nominee_contact_number']) > 20 && strlen($proposer_data['nominee_contact_number']) < 10) {
          $returnArr['41'] = 'Nominee contact number length should be between 10 to 20';        
        }else*/ 
        if (strlen($proposer_data['contactMobileNo']) > 20 && strlen($proposer_data['contactMobileNo']) < 10) {
           $returnArr[] = array('ErrorNumber'=>'14','ErrorMessage'=>'primary contact number length should be between 10 to 20');
          //$returnArr['14'] = 'primary contact number length should be between 10 to 20';
        } 
      }
    }

    if($branch_sol_id == ''){
      if($intermediaryCode == ''){
        $returnArr[] = array('ErrorNumber'=>'19','ErrorMessage'=>'intermediaryCode is required.');
      }else{
        $IMDCode = $this
                    ->db
                    ->query("select * from master_imd where IMDCode = '$intermediaryCode' AND product_code = '$product_id'")->row_array();
        if(!empty($IMDCode)){
          $responseArr['branch_sol_id'] = $IMDCode['BranchCode'];
        }else{
          $returnArr[] = array('ErrorNumber'=>'19','ErrorMessage'=>'intermediaryCode is invalid.');
        }
      }
    }else{
      $IMDCode = $this
                  ->db
                  ->query("select * from master_imd where BranchCode = '$branch_sol_id' AND product_code = '$product_id'")->row_array();
      if(empty($IMDCode)){
        $returnArr[] = array('ErrorNumber'=>'64','ErrorMessage'=>'Branch-sol-id is invalid.');
      }
    }

    //ImdCode Validation
   /* $imd_refer = $this
        ->db
        ->query("select imd_refer_product_code from product_master_with_subtype where  product_code='$product_id'")->row_array();
    $refer_prod_code = $imd_refer['imd_refer_product_code'];
   
    $IMDCode = $this
        ->db
        ->query("select * from master_imd where BranchCode = '$branch_sol_id' AND product_code = '$refer_prod_code'")->row_array();
    if (count($IMDCode) > 0) {
      $abc = 1;
    } else {
      $abc = 0;
    }
  
    if($abc == 0){
      $returnArr[] = array('ErrorNumber'=>'31','ErrorMessage'=>'intermediary Branch Code is invalid');
      //$error_messages['31'] = 'intermediary Branch Code is invalid';    
    }*/    

    //Pincode Validation
    $select = '*';
    $table_name = 'axis_postal_code';
    $where = "PINCODE = '".$proposer_data['pinCode']."'";
    $result_type = 'row_array';    
    $PincodeData = $this->GenericApi_m->common_select_query($select,$table_name,$where,$result_type);

    if(empty($PincodeData)){
      $returnArr[] = array('ErrorNumber'=>'08','ErrorMessage'=>'Pincode does not match');
      //$returnArr['08'] = 'Pincode does not match';
    }
    //server side validation completed
    $responseArr['errors'] = $returnArr;
    if($is_payment_flag == 0){
      //insertion into employee_details and family_relation table
      $insertArr = array("lead_id" => $lead_id, 
                          "salutation" => $proposer_data['salutation'], 
                          "product_id" => $product_id, 
                          "emp_firstname" => $proposer_data['firstName'], 
                          "emp_lastname" => $proposer_data['lastName'],  
                          "gender" => ($proposer_data['gender'] == 'M') ? "Male" : "Female", 
                          "bdate" => $proposer_data['dateofBirth'], 
                          "mob_no" => $proposer_data['contactMobileNo'], 
                          "access_right_id" => "2", 
                          "module_access_rights" => "1,8", 
                          "email" => $proposer_data['primaryEmailID'], 
                          "pancard" => $proposer_data['panNo'], 
                          "adhar" => ($proposer_data['uidNo'] == '') ? '.' : $proposer_data['uidNo'],  
                          "address" => $proposer_data['homeAddressLine1'], 
                          "emp_city" => $PincodeData['CITY'], 
                          "emp_state" => $PincodeData['STATE'], 
                          "emp_pincode" => $proposer_data['pinCode'], 
                          "ifsc_code" => $proposer_data['ifscCode'], 
                          "ISNRI" => $proposer_data['Is_NRI'], 
                          "json_qote" => json_encode($proposer_data) ,
                          //"lead_owner_name" => $LEADOWNERNAME, 
                          //"sp_email" => $SPEMAIL,
                          //"USERID" => $USERID,
                          //"REQUESTUUID" => $REQUESTUUID,
                          //"LEADOWNERID" => $LEADOWNERID,
                          //"AXISBANKACCOUNT" => $AXISBANKACCOUNT,
                          //"OTHERBANKCHEQUE" => $OTHERBANKCHEQUE,
                          "source" => $source,
                          "cust_id" => $proposer_data['Member_Customer_ID'],
                          "branch_id" => $branch_sol_id,
                          "acc_no" => $proposer_data['bankAccountNo']);
      //echo "lead_id => ".$lead_id;exit;
      $res = $this->insertEmployee($insertArr,$lead_id,$product_id);
      //print_pre($res);exit;
      if(isset($res['emp_id'])){
        $responseArr['emp_id'] = $res['emp_id'];
        //$returnArr['policy_parent_id'] = $res['policy_parent_id'];
      }else{//send error response if lead id already exist
        $responseArr['emp_id'] = $res['emp_id'];
      }
    }

    if($is_payment_flag == 1){  
      if($quotation_number == ''){
        $responseArr['errors'][] = array('ErrorNumber'=>'66','ErrorMessage'=>'Quotation Number is required.');
      }
      
      //print_pre($ReceiptObj);  
      //echo $lead_id;exit;  
      $employee_details = $this
                      ->db
                      ->where(["lead_id" => $lead_id])->get("employee_details")
                      ->row_array();
      $emp_id = $employee_details['emp_id'];
      //echo $emp_id;exit;
      $responseArr['emp_id'] = $emp_id;
      $proposal = $this
                      ->db
                      ->where(["emp_id" => $emp_id])->get("proposal")
                      ->row_array();
      if($proposal){
      //siddhi change
     $policy_no = $this->GenericApi_m->generic_policy_create($proposal['master_proposal_no'],$ReceiptObj);
        // $returnData['status'] = false;
        // $returnData['errors']['proposal'][] = ['ErrorNumber' => '65', 'ErrorMessage' => 'Proposal already exist for this user.'];
        header('Content-Type: application/json');
        echo json_encode($policy_no);exit;
      } 
      if($source_name == ''){
        $returnData['errors'][] = ['ErrorNumber' => '76', 'ErrorMessage' => 'Source Name is required.'];
      }
      if($SPID == ''){
        $returnData['errors'][] = ['ErrorNumber' => '77', 'ErrorMessage' => 'SPID is required.'];
      }

      // common validation is payement 1
      $mandatoryReciept = array( '68'=>'officeLocation',
                        '69' =>'modeOfEntry',
                        '70' =>'payerType',
                        '71' =>'collectionAmount',
                        '72' => 'collectionRcvdDate',
                        '73' => 'collectionMode',
                        '74' =>'instrumentNumber',
                        '75' => 'instrumentDate');     
    
    foreach ($mandatoryReciept as $error_code => $field) {
      if($ReceiptObj[$field] == ''){
        //$returnArr[$error_code] =  str_replace('_', ' ', $field).' is required.';
        $responseArr['errors']['ReceiptObj'][] = array('ErrorNumber'=>$error_code,'ErrorMessage'=> str_replace('_', ' ', $field).' is required.'); 
      }
    }
      


    }
    //print_r($responseArr);exit;
    return $responseArr;

  }

  public function insertEmployee($insertArr,$lead_id,$product_id){
    
    $alreadyExists = $this
                      ->db
                      ->where(["lead_id" => $lead_id])->get("employee_details")
                      ->num_rows();
    //echo $alreadyExists;exit;
    $query = $this
          ->db
          ->query("select policy_parent_id from product_master_with_subtype where product_code='$product_id'")->row_array();
    if ($alreadyExists == 0)
    { 
                      
      $this
      ->db
      ->insert("employee_details", $insertArr);

       $emp_id = $this
      ->db
      ->insert_id();
   
   
      $this
      ->db
      ->insert("family_relation", ["emp_id" => $emp_id, "family_id" => 0]);
      
      $return_array = ['emp_id' => $emp_id, 'policy_parent_id' => $query['policy_parent_id']];

    }else{
      //$return_array = ['ErrorNumber' => '62', 'ErrorMessage' => 'Lead id is already exist.'];
      //$return_array['62'] = 'Lead id is already exist.';
      $row = $this
          ->db
          ->select("emp_id, product_id")
          ->where(["lead_id" => $lead_id])->get("employee_details")
          ->row();
      //print_pre($row);exit;       
      $emp_id = $row->emp_id;
      $return_array = ['emp_id' => $emp_id, 'policy_parent_id' => $query['policy_parent_id']];
    }
    return $return_array;
  }

  public function test(){
    
    $request = '{
                   "ClientCreation": {
                      "Metadata": {
                         "Sender": {
                            "Source": "Thanos",
                            "TODID": "jghf-kjhk-hkjh-kjhjkh"
                         }
                      },
                      "Member_Customer_ID": "645656767",
                      "salutation": "Mrs",
                      "firstName": "pihuu",
                      "middleName": "",
                      "lastName": "Shah",
                      "dateofBirth": "01/08/1980",
                      "gender": "F",
                      "educationalQualification": "",
                      "pinCode": "400604",
                      "uidNo": null,
                      "UIDAcknowledgementNo": null,
                      "maritalStatus": "Single",
                      "nationality": "Indian",
                      "occupation": "O553",
                      "primaryEmailID": "kuhu@gmail.com",
                      "contactMobileNo": "9823121919",
                      "familyEmailID": null,
                      "familyMobileNo": null,
                      "stdLandlineNo": "",
                      "panNo": "",
                      "passportNumber": null,
                      "contactPerson": "",
                      "annualIncome": "0",
                      "remarks": "",
                      "startDate": "2019-11-25",
                      "endDate": "",
                      "IdProof": "Aadhar Card",
                      "residenceProof": "",
                      "ageProof": "",
                      "others": "1234",
                      "homeAddressLine1": "2/3, chennai\{ pondicherry  panvel} ",
                      "homeAddressLine2": "",
                      "homeAddressLine3": "",
                      "homePinCode": 400604,
                      "homeArea": "",
                      "homeContactMobileNo": "",
                      "homeContactMobileNo2": "",
                      "homeSTDLandlineNo": "",
                      "homeSTDLandlineNo2": null,
                      "homeFaxNo": "",
                      "sameAsHomeAddress": "1",
                      "mailingAddressLine1": ".",
                      "mailingAddressLine2": ".",
                      "mailingAddressLine3": ".",
                      "mailingPinCode": "",
                      "mailingArea": "",
                      "mailingContactMobileNo": "",
                      "mailingContactMobileNo2": "",
                      "mailingSTDLandlineNo": "",
                      "mailingSTDLandlineNo2": "",
                      "mailingFaxNo": "",
                      "bankAccountType": "",
                      "bankAccountNo": "",
                      "ifscCode": "",
                      "GSTIN": "",
                      "GSTRegistrationStatus": "Consumers",
                      "IsEIAavailable": "0",
                      "ApplyEIA": "0",
                      "EIAAccountNo": "",
                      "EIAWith": "0",
                      "AccountType": "",
                      "AddressProof": "",
                      "DOBProof": "",
                      "IdentityProof": "",
                      "ApplicationProductcode": "R07",
                      "Is_NRI": "N"     
                   },
                   "PolicyCreationRequest": {
                      "Quotation_Number": "",
                      "Policy_Tanure": null,
                      "Member_Type_Code": null,
                      "Branch_sol_id": "2789",
                      "intermediaryCode": "2106233",
                      "AutoRenewal": "",
                      "intermediaryBranchCode": "10MHMUM01",
                      "agentSignatureDate": null,
                      "Customer_Signature_Date": null,
                      "businessSourceChannel": "",
                      "AssignPolicy": "0",
                      "AssigneeName": "",
                      "leadID": "54645222200",
                      "Source_Name": "AXIS_COMBI_GFB",
                      "SPID": "",
                      "TCN": "",
                      "CRTNO": "",
                      "RefCode1": "",
                      "RefCode2": "",
                      "Employee_Number": "",
                      "EmployeeNumber": null,
                      "EmployeeDiscount": null,
                      "enumIsEmployeeDiscount": "",
                      "QuoteDate": "11/25/2019",
                      "IsPayment": "0",
                      "goGreen": null,
                      "PaymentMode": "Others",
                      "PolicyproductComponents": [{
                            "GroupID": "Grp001",
                            "ProductCode": "4211",
                            "Plan_Code": null,
                            "SchemeCode": "4211000003",
                            "MasterPolicyNumber": "61-20-00026-00-00"
                         },
                         {
                            "GroupID": "Grp001",
                            "ProductCode": "4112",
                            "Plan_Code": null,
                            "SchemeCode": "4112000003",
                            "MasterPolicyNumber": "62-20-00014-00-00"
                         },
                         {
                            "GroupID": "Grp001",
                            "ProductCode": "4216",
                            "Plan_Code": null,
                            "SchemeCode": "4112000003",
                            "MasterPolicyNumber": "62-20-00014-00-00"
                         }
                      ],
                      "familyDoctor": null
                   },
                   "MemObj": {
                      "Member": [{
                         "MemberNo": "1",
                         "Salutation": "Mrs",
                         "First_Name": "Kuhu",
                         "Middle_Name": "",
                         "Last_Name": "Shah",
                         "Email": "kuhu@gmail.com",
                         "Mobile_Number": "9823121919",
                         "Gender": "F",
                         "DateOfBirth": "01/08/1980",
                         "Relation_Code": "R001",
                         "Marital_Status": "",
                         "height": "0.00",
                         "weight": "0",
                         "occupation": "O553",
                         "PrimaryMember": "Y",
                         "optionalCovers": null,
                         "personalhabitDetail": null,
                         "MemberproductComponents": [{
                               "ProductCode": "4211",
                               "PlanCode": "4211000003",
                               "SumInsured": "500000",
                               "ChronicDetails": null,
                               "CoversDetails": [{
                                  "PlanCode": "",
                                  "CoverCode": "41124101"
                               }],
                               "MemberQuestionDetails": [{
                                  "QuestionCode": "Q101",
                                  "Value": "0",
                                  "Remarks": ""
                               }]
                            },
                            {
                               "ProductCode": "4112",
                               "PlanCode": "4112100001",
                               "SumInsured": "500000",
                               "ChronicDetails": null,
                               "CoversDetails": [{
                                  "PlanCode": "",
                                  "CoverCode": "40004101"
                               }],
                               "MemberQuestionDetails": [{
                                  "QuestionCode": "Q101",
                                  "Value": "0",
                                  "Remarks": ""
                               }]
                            },
                            {
                               "ProductCode": "4216",
                               "PlanCode": "4112100001",
                               "SumInsured": "500000",
                               "ChronicDetails": null,
                               "CoversDetails": [{
                                  "PlanCode": "",
                                  "CoverCode": "40004101"
                               }],
                               "MemberQuestionDetails": [{
                                  "QuestionCode": "Q101",
                                  "Value": "0",
                                  "Remarks": ""
                               }]
                            }
                         ],
                         "MemberPED": [{
                            "PEDCode": "PE009",
                            "Remarks": "Asthma",
                            "WaitingPeriod": null
                         }, {
                            "PEDCode": "PE002",
                            "Remarks": "Hypertension",
                            "WaitingPeriod": null
                         }, {
                            "PEDCode": "PE003",
                            "Remarks": "Diabetes",
                            "WaitingPeriod": null
                         }, {
                            "PEDCode": "PE293",
                            "Remarks": "Cholesterol",
                            "WaitingPeriod": null
                         }],
                         "MemberQuestionDetails": null,
                         "exactDiagnosis": null,
                         "dateOfDiagnosis": null,
                         "lastDateConsultation": null,
                         "detailsOfTreatmentGiven": null,
                         "doctorName": null,
                         "hospitalName": null,
                         "phoneNumberHosital": null,
                         "labReport": null,
                         "dischargeCardSummary": null,
                         "Nominee_First_Name": "sdf",
                         "Nominee_Last_Name": ".",
                         "Nominee_Contact_Number": "8989898989",
                         "Nominee_Home_Address": null,
                         "Nominee_Relationship_Code": "R002",
                         "Gaurdian_First_Name": "aaa",
                         "Gaurdian_Middle_Name": ".",
                         "Gaurdian_Last_Name": ".",
                         "Gaurdian_relationcode": ".",
                         "PreviousInsuranceDetails": null
                      }]
                   },
                   "ReceiptObj": {
                      "ReceiptCreation": [{
                            "ReceiptNumber": "",
                            "officeLocation": "Mumbai",
                            "modeOfEntry": "Direct",
                            "cdAcNo": "",
                            "expiryDate": "",
                            "payerType": "Customer",
                            "payerCode": "",
                            "paymentBy": "Customer",
                            "paymentByName": "xxxx yyyy",
                            "paymentByRelationship": "R001",
                            "collectionAmount": "10000",
                            "collectionRcvdDate": "08/09/2019",
                            "collectionMode": "Online Collections",
                            "PaymentGatewayName": "HDFC Pay-U",
                            "TerminalID": "87987",
                            "remarks": "",
                            "instrumentNumber": "jjjj",
                            "instrumentDate": "08/09/2019",
                            "bankName": "",
                            "branchName": "",
                            "bankLocation": "",
                            "micrNo": "",
                            "chequeType": "",
                            "ifscCode": "",
                            "CardNo": ""
                         }
                      ]
                   }
                }';
      $key = "Axis_abhi@123456";
      $iv = 'encryptionIntVec';
      (16 == strlen($key)) or $key = hash('MD5', $key, true);
      (16 == strlen($iv)) or $iv = hash('MD5', $iv, true);
      $xml_data = base64_encode($request);
      //echo $xml_data;exit;
      $xml_data = openssl_encrypt($xml_data, 'AES-128-CBC', $key,0, $iv);
     
     $request = $this->formatData($xml_data);

      $arr = json_decode($request,true);  
      if(json_decode($request) === FALSE) {
         echo "json is invalid";
      }else{
         echo "json is valid";
      }
      exit;
      $proposalArr = $arr['ClientCreation'];      
      //print_pre($proposalArr);exit;
      $intermediaryCode = $arr['PolicyCreationRequest']['intermediaryCode'];
      $branch_sol_id = $arr['PolicyCreationRequest']['Branch_sol_id'];
      $lead_id = $arr['PolicyCreationRequest']['leadID'];
      $singleMemberObject = $arr['MemObj']['Member'][0];
      //$res = $this->validate_data($proposalArr,$intermediaryCode,$singleMemberObject,$branch_sol_id,$lead_id);
      $res = $this->validate_annual_income_and_occupation('1500000','O001','300000');
      print_pre($res);

  }


  public function generate_checksumjson_data(){
    
    $data['encrypted_data'] = '';
    if(isset($_POST['json_cust_data'])){
      if($_POST['json_cust_data'] != ''){
        $data['json_data'] = $this->encryptData($_POST['json_cust_data']);
      }
    }
    $this->load->view("checksum_generic_api",$data);
  }
   public function generate_checksum_decryptjson_data(){
    
    $data['encrypted_data'] = '';
    if(isset($_POST['json_cust_data'])){
      if($_POST['json_cust_data'] != ''){
        $data['json_data'] = $this->formatData($_POST['json_cust_data']);
      }
    }
    $this->load->view("checksum_generic_decrypt_api",$data);
  }

  
  public function create_proposal(){
    //print_r($_POST['data']);exit;
    $JSON_data = file_get_contents("php://input");
    $request = json_decode($JSON_data,true);
    
    $request = $this->formatData($request['data']);
    $request = json_decode($request, true);
    //print_pre($request);exit;
    if( $request == NULL ){
      $returnData['status'] = false;
      $returnData['errors']['proposal'][] = ['ErrorNumber' => '63', 'ErrorMessage' => 'Invalid Json.'];
      header('Content-Type: application/json');
      echo json_encode($returnData);exit;
    }
    //verify access token
    $headers = apache_request_headers();
    if(isset($headers['access_token'])){
      $res = $this->verify_access_token($headers['access_token']);      
      if($res['Error']['ErrorNumber'] == 01){
        header('Content-Type: application/json');
        echo json_encode($res);exit;
      }
    }

    //do server side validation
    $proposalArr = $request['ClientCreation'];   
    $intermediaryCode = $request['PolicyCreationRequest']['intermediaryCode'];
    $branch_sol_id = $request['PolicyCreationRequest']['Branch_sol_id'];
    $lead_id = $request['PolicyCreationRequest']['leadID'];
    $quotation_number = $request['PolicyCreationRequest']['Quotation_Number'];
    $source_name = $request['PolicyCreationRequest']['Source_Name'];
    $SPID = $request['PolicyCreationRequest']['SPID'];
    if(isset($request['PolicyCreationRequest']['Policy_Tanure'])){
      $policy_tenure = $request['PolicyCreationRequest']['Policy_Tanure'];
    }
    /*if(isset($request['PolicyCreationRequest']['Policy_Tenure']){
      $policy_tenure = $request['PolicyCreationRequest']['Policy_Tenure'];
    }*/
    $ReceiptObj = $request['ReceiptObj']['ReceiptCreation'][0];
    $singleMemberObject = $request['MemObj']['Member'][0];
    $is_payment_flag =  $request['PolicyCreationRequest']['IsPayment'];
    $res = $this->validate_data($proposalArr,$intermediaryCode,$singleMemberObject,$branch_sol_id,$lead_id,$policy_tenure,$ReceiptObj,$is_payment_flag,$quotation_number,$source_name,$SPID);
    
      //member and proposal validation    
    $emp_id = $res['emp_id'];
    $common_errors = $res['errors'];
  $branch_sol_id = (isset($res['branch_sol_id'])) ? $res['branch_sol_id'] : ''; 
  
  
  
  try {
    $members_array = ($request['MemObj']['Member']);
    $policy_array = $request['PolicyCreationRequest']['PolicyproductComponents'];
    $product_member = [];
    
    foreach ($members_array as $value) {
      foreach ($value['MemberproductComponents'] as $key => $value1) {
      $product_code = $value1['ProductCode'];
      if(!$product_code){
            $return_arr = ['status' => false, 'errors' => ['ErrorNumber' => '44','ErrorMessage' => 'Product code is required']];
            echo json_encode($return_arr);exit;

                    }
      if (!isset($product_member[$product_code]['grpcode'])) {

            $last_names = array_search($product_code, array_column($policy_array, 'ProductCode'));
            $product_member[$product_code]['grpcode'] = $policy_array[$last_names]['GroupID'];
            $product_member[$product_code]['product_code'] = $request['ClientCreation']['ApplicationProductcode'];
                        $product_member[$product_code]['plan_code'] = $policy_array[$last_names]['Plan_Code'];
                        $product_member[$product_code]['master_policy_no'] = $policy_array[$last_names]['MasterPolicyNumber'];
                        $product_member[$product_code]['scheme_code'] = $policy_array[$last_names]['SchemeCode'];
                        $product_member[$product_code]['is_payment'] = $request['PolicyCreationRequest']['IsPayment'];
            $product_member[$product_code]['emp_id'] = $emp_id;                    
                        $product_member[$product_code]['quotation_no'] = $request['PolicyCreationRequest']['Quotation_Number'];
                        $product_member[$product_code]['collection_amount'] = $request['ReceiptObj']['ReceiptCreation'][0]['collectionAmount'];
      }
      $member = [
                        'member_no' => $value['MemberNo'],
                        'salutation' => $value['Salutation'],
                        'firstname' => $value['First_Name'],
                        'middlename' => $value['Middle_Name'],
                        'lastname' => $value['Last_Name'],
                        'gender' => $value['Gender'],
                        'dob' => $value['DateOfBirth'],
                        'relation_code' => $value['Relation_Code'],
                        'memberped' => $value['MemberPED'],
                        'suminsured' => $value1['SumInsured'],
                        'occupation' => $value['occupation'],
                        'nominee_firstname' => $value['Nominee_First_Name'],
                        'nominee_lastname' => $value['Nominee_Last_Name'],
                        'nominee_contact' => $value['Nominee_Contact_Number'],
                        'nominee_home_address' => $value['Nominee_Home_Address'],
                        'nominee_relation_code' => $value['Nominee_Relationship_Code'],
                        'emp_id' => $emp_id,
                         'product_code' => $request['ClientCreation']['ApplicationProductcode'],
                        'branch_sol_id' => $branch_sol_id,
                        'annual_income' => $request['ClientCreation']['annualIncome'],
                        'max_age' => $request['ClientCreation']['Maxage'],
                        // 'group_code' => $policy_array[$last_names]['GroupID'],
                        // 'master_policy_no' => $policy_array[$last_names]['MasterPolicyNumber']

                    ];

          
      $product_member[$product_code]['members'][] = $member;

      }
    }

  }catch (ErrorException  $e){
        $return_arr = ['status' => false, 'errors' => ['ErrorNumber' => '70','ErrorMessage' => 'INVALID REQUEST']];
                echo json_encode($return_arr);exit;
      
    }
  
  set_error_handler(function() { return true; });   
    $this->GenericApi_m->member_validations($product_member,$common_errors,$ReceiptObj);
   
  }

  public function get_premium($intermediaryCode,$product_code,$family_construct,$sum_insured,$policy_parent_id,$member_age) 
  {
    //verify access token
    $headers = apache_request_headers();
    if(isset($headers['access_token'])){
      $res = $this->verify_access_token($headers['access_token']);
      
      if($res['Error']['code'] == 01){
        header('Content-Type: application/json');
        echo json_encode($res);exit;
      }
    }
   // echo '--'.$res['Error']['code'].'--';print_r($res);exit;
    //print_r($headers);exit;
    
    $select = '*';
    $table_name = 'master_imd';
    $where = "IMDCode = '".$intermediaryCode."' AND product_code = '".$product_code."'";
    $result_type = 'row_array';       
    $ImdData = $this->GenericApi_m->common_select_query($select,$table_name,$where,$result_type);
    $ew_status = $ImdData['EW_status'];

    //echo $ew_status;
    $select = '*';
    $table_name = 'product_master_with_subtype AS p,employee_policy_detail AS ed';
    $where = "ed.product_name = p.id AND p.product_code = '".$product_code."' order by ed.policy_sub_type_id";
    $result_type = 'result_array';     
    $policies = $this->GenericApi_m->common_select_query($select,$table_name,$where,$result_type);
    //echo '<PRE>';print_r($policies);exit;
    
    if($product_code == 'R03' || $product_code == 'R07'){//get_premium_new
      foreach ($policies as $key => $value) {
          $check_gmc = $this->db
            ->select('*')
            ->from('employee_policy_detail as epd')
            ->join('master_policy_sub_type as mpst', "epd.policy_sub_type_id = mpst.policy_sub_type_id")
            ->where('epd.policy_detail_id', $value['policy_detail_id'])
            ->get()
            ->row_array();

            if ($check_gmc['policy_sub_type_id'] == '1') {
            //get gmc premium
            $premium[$key] = $this->db
            ->select('*')
            ->from('family_construct_wise_si as fc')
            ->where('fc.policy_detail_id', $value['policy_detail_id'])
            ->where('fc.family_type', $family_construct)
            ->where('fc.sum_insured', $sum_insured)
            ->get()
            ->row_array();
            // $premium[$key]['policy_sub_type_id'] = 'Group Mediclaim';
            $premium[$key]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
            } else {
            $family_construct1 = explode("+", $family_construct);

            //get max adult count
            $premium1 = $this->db
            ->select('*')
            ->from('master_broker_ic_relationship as fc')
            ->where('fc.policy_id', $value['policy_detail_id'])
            ->get()
            ->row_array();

            if ($premium1['max_adult'] . "A" == $family_construct1[0] || $premium1['max_adult'] . "A" > $family_construct1[0]) {
            $premium[$key] = $this->db
            ->select('*')
            ->from('family_construct_wise_si as fc')
            ->where('fc.policy_detail_id', $value['policy_detail_id'])
            ->where('fc.family_type', $family_construct1[0])
            ->where('fc.sum_insured', $sum_insured)
            ->get()
            ->row_array();
            //echo $this->db->last_query();exit;
            if ($check_gmc['policy_sub_type_id'] == 2) {
            $premium[$key]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
            } else {
            $premium[$key]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
            }
            } else {
            if ($check_gmc['policy_sub_type_id'] == 2) {
            $premium[$key]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
            } else {
            $premium[$key]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
            }
            $premium[$key] = $this->db
            ->select('*')
            ->from('family_construct_wise_si as fc')
            ->where('fc.policy_detail_id', $value['policy_detail_id'])
            ->where('fc.family_type', $premium1['max_adult'] . "A")
            ->get()
            ->row_array();
            }
            }
            $premium[$key]['ew_status'] = $get_ew_status['EW_status'];            
      }
      $premium_amt = 0;
      if(!empty($premium)){        
        foreach ($premium as $key => $value) {
          $premium_amt = $premium_amt + $value['EW_PremiumServiceTax'];
        }
        $return_data = ["premium" => $premium_amt, "status" => true];
      }else{
        $return_data = ["premium" => $premium_amt, "status" => false, "message" => "No premium for specified age"];
      }
      

    }else if($product_code == 'R04'){//get_premium_age 
      foreach ($policies as $key => $value) {
        $policy_detail_id = $value['policy_detail_id'];
        $check = $this
            ->db
            ->select("*")
            ->from("family_construct_age_wise_si")
            ->where("sum_insured", $sum_insured)->where("family_type", $family_construct)->where("policy_detail_id", $policy_detail_id)->get()
            ->result_array();
        //echo $this->db->last_query();exit;

        foreach ($check as $value)
        {
          //print_pre($value);
          $min_max_age = explode("-", $value['age_group']);
          //echo $member_age.' >= '.$min_max_age[0].' && '.$member_age .'<='. $min_max_age[1];exit;
          if ($member_age >= $min_max_age[0] && $member_age <= $min_max_age[1])
          {
              $premium = $value['PremiumServiceTax'];
          }
        }
        if (!$premium)
        {
            $return_data = ["premium" => $premium, "status" => false, "message" => "No premium for specified age"];
        }
        else
        {
            $return_data = ["premium" => $premium, "status" => true];
        }
      }
    }else if($product_code == 'R06'){//get_premium_telesales
      foreach ($policies as $key => $value) {
          $policy_detail_id = $value['policy_detail_id'];
          $check_gmc = $this->db
                  ->select('*')
                  ->from('employee_policy_detail as epd')
                  ->join('master_policy_sub_type as mpst', "epd.policy_sub_type_id = mpst.policy_sub_type_id")
                  ->where('epd.policy_detail_id', $policy_detail_id)
                  ->get()
                  ->row_array();
          if ($check_gmc['policy_sub_type_id'] == '1'){       
              $premium[$key] = $this->db
                      ->select('*')
                      ->from('family_construct_wise_si as fc')
                      ->where('fc.policy_detail_id', $policy_detail_id)
                      ->where('fc.family_type', $family_construct)
                      ->where('fc.sum_insured', $sum_insured)
                      ->get()
                      ->row_array();      
              $premium[$key]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
          }else{
              $family_construct1 = explode("+", $family_construct);
              //get max adult count
              $premium1 = $this->db
                      ->select('*')
                      ->from('master_broker_ic_relationship as fc')
                      ->where('fc.policy_id', $policy_detail_id)
                      ->get()
                      ->row_array();
              if ($premium1['max_adult'] . "A" == $family_construct1[0] || $premium1['max_adult'] . "A" > $family_construct1[0]){
        
                  $premium[$key] = $this->db
                          ->select('*')
                          ->from('family_construct_wise_si as fc')
                          ->where('fc.policy_detail_id', $policy_detail_id)
                          ->where('fc.family_type', $family_construct1[0])
                          ->where('fc.sum_insured', $sum_insured)
                          ->get()
                          ->row_array();
                  //echo $this->db->last_query();exit;
                  if ($check_gmc['policy_sub_type_id'] == 2){
                      $premium[$key]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
                  }else{
                      $premium[$key]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
                  }
              }else{
                  if ($check_gmc['policy_sub_type_id'] == 2){
                      $premium[$key]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
                  } 
                  else{
                      $premium[$key]['policy_sub_type_id'] = $check_gmc['policy_sub_type_name'];
                  }
                  $premium[$key] = $this->db
                          ->select('*')
                          ->from('family_construct_wise_si as fc')
                          ->where('fc.policy_detail_id', $policy_detail_id)
                          ->where('fc.family_type', $premium1['max_adult'] . "A")
                          ->get()
                          ->row_array();
              }  
          }
      }
      $premium_amt = 0;     
      if(!empty($premium)){        
        foreach ($premium as $key => $value) {
          $premium_amt = $premium_amt + $value['PremiumServiceTax'];
        }
        $return_data = ["premium" => $premium_amt, "status" => true];
      }else{
        $return_data = ["premium" => $premium_amt, "status" => false, "message" => "No premium for specified age"];
      }
    }else if($product_code == 'R05'){
      $query = $this->db->query("select epd.policy_detail_id,epd.suminsured_type,pms.combo_flag from product_master_with_subtype as pms,employee_policy_detail as epd where pms.policy_parent_id = epd.parent_policy_id AND epd.parent_policy_id = '".$policy_parent_id."' ")->result_array();
      
      foreach($query as $key =>  $value){
        $suminsured_type = $value['suminsured_type'];
        //echo $suminsured_type;exit;
        $premium_new[$key]['premium'] = $this->get_premium_data($suminsured_type,$value['policy_detail_id'],$family_construct,$sum_insured);    
      } 
      $premium_amt = 0;     
      if(!empty($premium_new)){        
        foreach ($premium_new as $key => $value) {
          $premium_amt = $premium_amt + $value['premium'];
        }
        $return_data = ["premium" => $premium_amt, "status" => true];
      }else{
        $return_data = ["premium" => $premium_amt, "status" => false, "message" => "No premium for specified age"];
      }
    }
    print_r($return_data);exit;
  }

  public function get_premium_data($suminsured_type,$policy_detail_id,$family_construct,$sum_insured)
  {
    if($suminsured_type == 'family_construct_age')
        {
          
           $premium = $this->db
                      ->select('pcapremium.*')
                      ->from('employee_policy_detail as epd,family_construct_age_wise_si as pcapremium')
                      ->where('epd.policy_detail_id = pcapremium.policy_detail_id')
                      ->where('epd.policy_detail_id', $policy_detail_id)
                      ->where('pcapremium.family_type', $family_construct)
                      ->where('pcapremium.sum_insured', $sum_insured)
                      
                      ->get()
                      ->row_array();
            //echo $this->db->last_query();exit;          
            $premium_new = $premium['PremiumServiceTax'];
          
        }
        if($suminsured_type == 'family_construct')
        {
          
           $premium = $this->db
                      ->select('pcapremium.*')
                      ->from('employee_policy_detail as epd,family_construct_age_wise_si as pcapremium')
                      ->where('epd.policy_detail_id = pcapremium.policy_detail_id')
                      ->where('epd.policy_detail_id', $policy_detail_id)
                      ->where('pcapremium.family_type', $family_construct)
                      ->where('pcapremium.sum_insured', $sum_insured)
                      
                      ->get()
                      ->row_array();
            //echo $this->db->last_query();exit;
            //print_pre($premium);exit;         
            $premium_new = $premium['PremiumServiceTax'];
          
        }

      return $premium_new;
  }

  public function formatData($data){
    $key = "GenericAPI_Axis_abhi@123456";
    $iv = 'encryptionIntVec';
    (27 == strlen($key)) or $key = hash('MD5', $key, true);
    (16 == strlen($iv)) or $iv = hash('MD5', $iv, true);
    $data = base64_decode($data);
    $data = openssl_decrypt($data, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return $data;
  }


  public function encryptData($data){
    // $key = "GenericAPI_Axis_abhi@123456";
    $key = "lumGSVCXwk2A6fRjS9GM/kpecgnemZJKMlpqMnsPwC8=";
    $iv = 'encryptionIntVec';
    (27 == strlen($key)) or $key = hash('MD5', $key, true);
    (16 == strlen($iv)) or $iv = hash('MD5', $iv, true);
    $encryptedData = base64_encode(openssl_encrypt($data, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv));
    return $encryptedData;
  }

  /*
  Created By - Ankita <ankita.badak@fyntune.com>
  Created On - 20 Aug 2020
  Updated By - Ankita <ankita.badak@fyntune.com>
  Updated On - 20 Aug 2020
  Remark - this function is used for server side validation of ocupation and anual income
  */
  function validate_annual_income_and_occupation($sum_insured,$occupation,$annual_income){
    $isSumAssuredMoreThan = 1000000;
    $incomeSum = $annual_income * 8;
    $errorArr = [];
    if($sum_insured > $isSumAssuredMoreThan){
      if($occupation == ''){
        // $errorArr['36'] = 'occupation is required';
    $returnArr[] = array('ErrorNumber'=>'36','ErrorMessage'=>'Occupation is required');
      }else{
        $select = '*';
        $table_name = 'master_occupation';
        $where = "occupation_id = '".$occupation."'";
        $result_type = 'row_array';     
        $occupationDetails = $this->GenericApi_m->common_select_query($select,$table_name,$where,$result_type);
        if(empty($occupationDetails)){
          // $errorArr['37'] = 'occupation does not match with master';
      $returnArr[] = array('ErrorNumber'=>'37','ErrorMessage'=>'Occupation does not match with master');
        }
      }

      if($annual_income == ''){
        // $errorArr['20'] = 'anuual income is required';
    $returnArr[] = array('ErrorNumber'=>'20','ErrorMessage'=>'Anuual Income is required');
      }

      if($incomeSum < $sum_insured ){
        // $errorArr['38'] = 'Request to select SI lesser than opted';
    $returnArr[] = array('ErrorNumber'=>'38','ErrorMessage'=>'Request to select SI lesser than opted');
      }
    }

    if(empty($errorArr)){
      $errorArr['00'] = 'validated successfully';
    }
    return $errorArr;
  }

  




  }

  ?>