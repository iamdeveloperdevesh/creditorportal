<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once(APPPATH . "controllers/MY_TelesalesSessionCheck.php");

class Product_configuartion extends MY_TelesalesSessionCheck
{

    public function __construct()
    {
        parent::__construct();
        /*if (!$this->session->userdata('telesales_session')) {
            redirect('login');
        }

        if ($_SESSION['telesales_session']['is_redirect_allow'] != "1")
        {
            redirect('login');
        }*/
        $this->db= $this->load->database('telesales_fyntune',TRUE);
        $this->load->model('employee/policy_creation_m', 'obj_pcreate');


    }


    public function policy_configuration_fyntune($is_policy_created=0)
    {

        $data['members'] = $this->getMembers();

        $data['adult_members'] = $data['child_members'] = array();
        foreach ($data['members'] as $member) {
            if ($member->is_adult == "Y") {
                $data['adult_members'][] = $member;
            } else {
                $data['child_members'][] = $member;
            }
        }
        $data['max_member_count']=count($data['members']);
        //var_dump($data);
       // $data['creditors'] = $this->getCreditors();
      $data['payment_modes'] = ($this->obj_pcreate->get_payment_mode_data());
        $data['payment_workflows'] = $this->getPaymentWorkflow();
        $data['policytypes'] = $this->getPolicyType();
       // $data['policysubtypes'] = $this->getPolicySubType();
        //echo 123;exit;
        $data['is_policy_created']=$is_policy_created;
        //var_dump($data['adult_members']);exit;
        $this->load->telesales_template("Product_configuartion",$data);
       // $this->load->view('Telesales_fyntune/Product_configuartion.php',$data);
    }
    function getPaymentWorkflow()
    {
        $data = $this->db->get_where('payment_workflow_master', array('isactive' => 1))->result();
        return $data;
    }
    public function getMembers(){
        //master_family_relation
        $this->db->select('fr_id, fr_name, status,is_adult');
        $data = $this->db->get_where('master_family_relation',array('status'=>1))->result();

        return $data;
    }
    public function getPaymentModes() {
        echo json_encode ($this->obj_pcreate->get_payment_mode_data());
    }

    public function getPolicyType(){

    }
    public function get_policy_creation_det() {
        $data['policyType'] = $this->db->get('master_policy_type')->result();
       /* $data['masterInsurance'] = $this->db->get('master_insurance_companies')->result();
        $data['tpaCode'] = $this->db->get('tpa_masters')->result();*/

        echo json_encode($data);
    }
    public function get_policy_subType_fyntune() {
        extract($this->input->post(null, true));

        $data['policySubType'] = $this->db->where(['policy_type_id' => $policy_type_id])->get('master_policy_sub_type')->result();

        echo json_encode($data);
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
    function save_tem_data(){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $this->load->library('excel');
        extract($this->input->post(null, true));

        $policy_subtypes_id = explode(',',$policy_subtypes_id);
        //  echo $this->db;exit;

        $data1 = $this->db->select("*")
            ->from('product_master_with_subtype')
            ->where('product_master_with_subtype.product_name', trim($product_name))
            ->get()
            ->result_array();

        if(count($data1) < 1){

            $test = $this->generateRandomString() . uniqid();

            for($i=0; $i<count($policy_subtypes_id); $i++){

                $this->db->insert('product_master_with_subtype', [
                    'product_name' => trim($product_name),
                    'policy_parent_id' => $test,
                    'policy_subtype_id' => $policy_subtypes_id[$i],
                    'product_code' => $product_code,
                    'policy_type_id'=>$policy_types_id
                ]);

                for($j = 0; $j<count($payment_modes); $j++){

                    $this->db->insert('policy_payment_customer_mapping', [
                        'policy_id' => $policy_subtypes_id[$j],
                        'mapping_id' => $payment_modes[$j],
                        'type' => 'P',
                        'status' => 'Active',

                    ]);
                }

            }

            $id = $this->db->insert_id();
            if($id){
                $data['subtype'] = $this->db->select("*")
                    ->from('product_master_with_subtype , master_policy_sub_type ')
                    ->where('product_master_with_subtype.policy_subtype_id = master_policy_sub_type.policy_sub_type_id')
                    ->where('product_master_with_subtype.policy_parent_id', $test)
                    ->get()
                    ->result_array();
            }
            $data['parent_id']=$test;
            $GroupCodeFile = $_FILES['filename']['tmp_name'];
            $objPHPExcel = PHPExcel_IOFactory::load($GroupCodeFile);
            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                //$worksheetTitle = $worksheet->getTitle();
                $highestRow = $worksheet->getHighestRow(); // e.g. 10

                for ($col = 2; $col <= $highestRow; ++$col) {
                    $product_code = $worksheet->getCellByColumnAndRow(0, $col);
                    $group_code = $worksheet->getCellByColumnAndRow(1, $col);
                    $EW_group_code = $worksheet->getCellByColumnAndRow(2, $col);
                    $spouse_group_code = $worksheet->getCellByColumnAndRow(3, $col);
                    $family_construct = $worksheet->getCellByColumnAndRow(4, $col);
                    $si_per_member = $worksheet->getCellByColumnAndRow(5, $col);
                    $si_group = $worksheet->getCellByColumnAndRow(6, $col);
                    if($this->db->database != "axis_retail"){
                        $this->db->insert('master_group_code', [
                            'product_code' => $product_code,
                            'group_code' => $group_code,
                            'EW_group_code' => $EW_group_code,
                            'spouse_group_code' => $spouse_group_code,
                            'family_construct' => $family_construct,
                            'si_per_member' => $si_per_member,
                            'si_group' => $si_group
                        ]);
                    }else{
                        $this->db->insert('master_group_code', [
                            'product_code' => $product_code,
                            'group_code' => $group_code,
                            'spouse_group_code' => $spouse_group_code,
                            'family_construct' => $family_construct,
                            'si_per_member' => $si_per_member,
                            'si_group' => $si_group
                        ]);
                    }


                }
            }

            echo json_encode($data);
        }
        else {
            echo json_encode(false);
        }

    }

    function AddPolicyNew(){
        /*var_dump($_POST);
        exit;*/

        $data = array();
        $this->load->library('excel');
        $data['utoken'] = $_SESSION['webpanel']['utoken'];
        $data['policy_sub_type_id'] = $this->input->post('policySubType');
        $data['plan_id'] = $this->input->post('plan_id');
        $data['creditor_id'] = $this->input->post('creditor_id');   // *
        $data['policy_number'] = $this->input->post('policyNo');
        $mandatory = $this->input->post('mandatory');  // *



        if ($mandatory == 1) {
            $data['is_optional'] = 0;
        } else {
            $data['is_optional'] = 1;
        }

        $combo = $this->input->post('combo');  // *
        if ($combo == 1) {
            $data['is_combo'] = 1;
        } else {
            $data['is_combo'] = 0;
        }
        $siBasisNew=array('flate'=>1,'family_construct'=>2,'family_construct_age'=>3,'memberAge'=>4);
        $data['insurer_id'] = $this->input->post('masterInsurance');
        $data['policy_start_date'] = date('Y-m-d', strtotime(trim($this->input->post('policyStartDate'))));
        $data['policy_end_date'] = date('Y-m-d', strtotime(trim($this->input->post('policyEndDate'))));
        $data['plan_code'] = $this->input->post('plan_code'); // *
        $data['product_code'] = $this->input->post('product_code');  // *
        $data['scheme_code'] = $this->input->post('scheme_code');  // *
        $data['source_name'] = $this->input->post('source_name');  // *
        $data['max_member_count'] = $this->input->post('membercount');
        $data['mandatory_if_not_selected'] = $this->input->post('mandatory_if_not_selected'); // *
        $data['sitype'] = $this->input->post('sum_insured_type');
        $data['sibasis'] = $siBasisNew;
        $data['siBasisName'] = $this->input->post('companySubTypePolicy');
        $data['adult_count'] = $this->input->post('adult_count');
        $data['child_count'] = $this->input->post('child_count');
        $data['policy_type_id'] = $this->input->post('policy_type_id');

        $data['members'] = implode(',', $this->input->post('member'));
        $data['minage'] = implode(',', $this->input->post('minage'));
        $data['maxage'] = implode(',', $this->input->post('maxage'));
        $data['min_age_type'] = implode(',', $this->input->post('min_age_type'));
        //echo 123;exit;
       // $response = json_decode(curlFunction(SERVICE_URL . '/api2/addNewPolicy', $data), true);
        $response = $this->addNewPolicy1($data);

        if ($response['status_code'] == '200') {
            echo json_encode(array('success' => true, 'msg' => $response['Metadata']['Message'], 'data' => $response['Data']));
            exit;
        } else {
            echo json_encode(array('success' => false, 'msg' => $response['Metadata']['Message']));
            exit;
        }
    }

    function addNewPolicy1($data1)
    {
      //      var_dump($_POST);exit;

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

            $data = array();
            $utoken = $this->input->post('utoken');
            $plan_id = $this->input->post('plan_id');

            $data['creditor_id'] = $this->input->post('creditor_id');
            $policy_sub_type_id = $this->input->post('policySubType');
            $data['policy_number'] = $this->input->post('policyNo');
            $data['is_optional'] = intval($this->input->post('is_optional'));
            $data['is_combo'] = intval($this->input->post('is_combo'));
            $adult_count = $this->input->post('adult_count');
            $child_count = $this->input->post('child_count');
            $data['isactive'] = 1;
            $data['pdf_type'] = $this->input->post('pdf_type');
            $data['insurer_id'] = $this->input->post('insurer_id');
            $data['policy_start_date'] = date('Y-m-d', strtotime($this->input->post('policy_start_date')));
            $data['policy_end_date'] = date('Y-m-d', strtotime($this->input->post('policy_end_date')));
            $data['plan_code'] = $this->input->post('plan_code');
            $data['product_code'] = $this->input->post('product_code');
            $data['scheme_code'] = $this->input->post('scheme_code');
            $data['source_name'] = $this->input->post('source_name');
            $data['max_member_count'] = $this->input->post('max_member_count');
            $data['adult_count'] =  $this->input->post('adult_count');
            $data['child_count'] =  $this->input->post('child_count');
            $data['policy_type_id'] =  $this->input->post('policy_type_id');
             $sitype = $this->input->post('sitype');
            $sibasisAll = $data1['sibasis'];
            $d=$data1['siBasisName'];
            $basisType= $sibasisAll[$d];

            $members =  $this->input->post('member');
            $minages =  $this->input->post('minage');
            $maxages =  $this->input->post('maxage');
            $min_age_type =  $this->input->post('min_age_type');
            $mandatory_if_not_selected = $this->input->post('mandatory_if_not_selected');

            $mandatory_insert = [];
            if (is_array($mandatory_if_not_selected)) {
                foreach ($mandatory_if_not_selected as $policy_id) {
                    $mandatory_insert[] = [
                        'master_policy_id' => $policy_sub_type_id,
                        'dependent_on_policy_id' => $policy_id
                    ];
                }


            }

//product_master_with_subtype
        $policyD=$this->db->query('select policy_subtype_id,policy_parent_id from product_master_with_subtype where id='.$policy_sub_type_id)->row();

            $policySUBID=$policyD->policy_subtype_id;
        $policy_parent_id=$policyD->policy_parent_id;
        $sum_insured = "";
        $premium_si = "";
        $tax = "";
        $premium_with_tax = "";
        if ($basisType == 1) {
           // echo 123;exit;
            $arrUnMaryChild = [];
            $strUnMaryChild = 0;
            $unmarried_child_contri = 0;
            $isunMaryChildCheck=0;
            if ($isunMaryChildCheck == 1) {
                $unmarried_child_contri = $employeeContriUnMaryChild . ',' . $employerContriUnMaryChild;
                if ($isDaughterCheck == 1) {
                    array_push($arrUnMaryChild, 3);
                }

                if ($isSonCheck == 1) {
                    array_push($arrUnMaryChild, 2);
                }

                $strUnMaryChild = implode(',', $arrUnMaryChild);
            }

            if($d == "flate"){

                $sum_insured = implode(',',$this->input->post('sum_insured_opt1'));
                $premium_si = implode(',',$this->input->post('premium_opt'));
                $tax = implode(',',$this->input->post('tax_opt'));
                $premium_with_tax = implode(',',$this->input->post('tax_opt'));
            }
            $special_child_contri = 0;

            $query_check_policy_exist=$this->check_policy_exist($policy_sub_type_id,$policy_parent_id,$basisType);
           // var_dump( $query_check_policy_exist);exit;
            if($query_check_policy_exist == true){
                $this->db->insert('employee_policy_detail', [
                    'policy_no' => trim($data['policy_number']),
                    'broker_id' => '',
                    'policy_type_id' => $data['policy_type_id'], //
                    'policy_sub_type_id' => $policySUBID,
                    'insurer_id' => $data['insurer_id'],
                    'company_id' => 2, //
                    'start_date' => $data['policy_start_date'] ,
                    'end_date' =>  $data['policy_end_date'] ,
                    'tax' => trim($tax),
                    'PremiumServiceTax' => trim($premium_with_tax),
                    //'TPA_id' => $TPA_id,
                    'sum_insured_type' => $sitype,

                    'pdf_status' => $data['pdf_type'],
                    'special_child_contri' => $special_child_contri,
                    'unmarried_child_check' => $isunMaryChildCheck,
                    'unmarried_child_contri' => $unmarried_child_contri,
                    'unmarried_child_cover' => $strUnMaryChild,

                    'sum_insured' => $sum_insured,
                    'premium' => $premium_si,


                    'policy_status' => 'Pending',
                    'product_name'=>$policy_sub_type_id,
                    'parent_policy_id' => $policy_parent_id,
                    'proposal_approval'=>'N',
                    'customer_search_status'=>'N',
                    'premium_type' => '',
                    'suminsured_type' => $data1['siBasisName'],
                    'policy_enrollment_start_date' => $data['policy_start_date'],
                    'policy_enrollment_end_date' => $data['policy_end_date'],
                ]);

                $policy_id = $this->db->insert_id();

                foreach ($members as $key => $value) {
                    $this->db->insert('policy_age_limit', [
                        'relation_id' => $value,
                        'policy_detail_id' => $policy_id,
                        'min_age' => $minages[$key],
                        'max_age' => $maxages[$key],
                        'premium' => 0,
                        'employee_contri' => 0,
                        'employer_contri' => 0,
                    ]);
                    // }
                }
                if($policy_id){
                    $result=true;
                }else{
                    $result=false;
                }
            }

        }
        else if ($basisType == 2 || $basisType == 3) {


            $arrUnMaryChild = [];
            $strUnMaryChild = 0;
            $unmarried_child_contri = 0;
            $isunMaryChildCheck=0;
            if ($isunMaryChildCheck == 1) {
                $unmarried_child_contri = $employeeContriUnMaryChild . ',' . $employerContriUnMaryChild;
                if ($isDaughterCheck == 1) {
                    array_push($arrUnMaryChild, 3);
                }

                if ($isSonCheck == 1) {
                    array_push($arrUnMaryChild, 2);
                }

                $strUnMaryChild = implode(',', $arrUnMaryChild);
            }

            $special_child_contri = 0;
            $isSpChildCheck = 0;
            if ($isSpChildCheck == 1) {
                $special_child_contri = $employeeContriSpChild . ',' . $employerContriSpChild;
            }

            $premiumCalTypeValue = "";
            $masterInsurance="";
            if ($masterInsurance == 201 && $policyType == 1 && $policySubType == 2) {
                $premiumCalTypeValue = $premiumCalType;
            }

            if($d == "flate"){

                $sum_insured = implode(',',$this->input->post('sum_insured_opt1'));
                $premium_si = implode(',',$this->input->post('premium_opt'));
                $tax = implode(',',$this->input->post('tax_opt'));
                $premium_with_tax = implode(',',$this->input->post('tax_opt'));
            }

            $query_check_policy_exist=$this->check_policy_exist($policy_sub_type_id,$policy_parent_id,$basisType);

            if($query_check_policy_exist == true) {
                $this->db->insert('employee_policy_detail', [
                    'policy_no' => trim($data['policy_number']),
                    'broker_id' => '',
                    'policy_type_id' => $data['policy_type_id'], //
                    'policy_sub_type_id' => $policySUBID,
                    'insurer_id' => $data['insurer_id'],
                    'company_id' => 2, //
                    'start_date' => $data['policy_start_date'],
                    'end_date' => $data['policy_end_date'],
                    'tax' => trim($tax),
                    'PremiumServiceTax' => trim($premium_with_tax),
                    //'TPA_id' => $TPA_id,
                    'sum_insured_type' => $sitype,

                    'pdf_status' => $data['pdf_type'],
                    'special_child_contri' => $special_child_contri,
                    'unmarried_child_check' => $isunMaryChildCheck,
                    'unmarried_child_contri' => $unmarried_child_contri,
                    'unmarried_child_cover' => $strUnMaryChild,

                    'sum_insured' => $sum_insured,
                    'premium' => $premium_si,


                    'policy_status' => 'Pending',
                    'product_name' => $policy_sub_type_id,
                    'parent_policy_id' => $policy_parent_id,
                    'proposal_approval' => 'N',
                    'customer_search_status' => 'N',
                    'premium_type' => '',
                    'suminsured_type' => $data1['siBasisName'],
                    'policy_enrollment_start_date' => $data['policy_start_date'],
                    'policy_enrollment_end_date' => $data['policy_end_date'],
                ]);
                $policy_id = $this->db->insert_id();
                foreach ($members as $key => $value) {
                    $this->db->insert('policy_age_limit', [
                        'relation_id' => $value,
                        'policy_detail_id' => $policy_id,
                        'min_age' => $minages[$key],
                        'max_age' => $maxages[$key],
                        'premium' => 0,
                        'employee_contri' => 0,
                        'employer_contri' => 0,
                    ]);
                    // }
                }

                if($policy_id){
                    $result=true;
                }else{
                    $result=false;
                }
            }
        }
        else {

            if($d == "flate"){

                $sum_insured = implode(',',$this->input->post('sum_insured_opt1'));
                $premium_si = implode(',',$this->input->post('premium_opt'));
                $tax = implode(',',$this->input->post('tax_opt'));
                $premium_with_tax = implode(',',$this->input->post('tax_opt'));
            }
            $isunMaryChildCheck=0;
            if ($isunMaryChildCheck == 1) {
                $unmarried_child_contri = $employeeContriUnMaryChild . ',' . $employerContriUnMaryChild;
                if ($isDaughterCheck == 1) {
                    array_push($arrUnMaryChild, 3);
                }

                if ($isSonCheck == 1) {
                    array_push($arrUnMaryChild, 2);
                }

                $strUnMaryChild = implode(',', $arrUnMaryChild);
            }

            $special_child_contri = 0;
            $isSpChildCheck = 0;
            if ($isSpChildCheck == 1) {
                $special_child_contri = $employeeContriSpChild . ',' . $employerContriSpChild;
            }


            $query_check_policy_exist=$this->check_policy_exist($policy_sub_type_id,$policy_parent_id,$basisType);

            if($query_check_policy_exist == true) {
                $this->db->insert('employee_policy_detail', [
                    'policy_no' => trim($data['policy_number']),
                    'broker_id' => '',
                    'policy_type_id' => $data['policy_type_id'], //
                    'policy_sub_type_id' => $policySUBID,
                    'insurer_id' => $data['insurer_id'],
                    'company_id' => 2, //
                    'start_date' => $data['policy_start_date'],
                    'end_date' => $data['policy_end_date'],
                    'tax' => trim($tax),
                    'PremiumServiceTax' => trim($premium_with_tax),
                    //'TPA_id' => $TPA_id,
                    'sum_insured_type' => $sitype,

                    'pdf_status' => $data['pdf_type'],
                    'special_child_contri' => $special_child_contri,
                    'unmarried_child_check' => $isunMaryChildCheck,
                    'unmarried_child_contri' => $unmarried_child_contri,
                    'unmarried_child_cover' => $strUnMaryChild,

                    'sum_insured' => $sum_insured,
                    'premium' => $premium_si,


                    'policy_status' => 'Pending',
                    'product_name' => $policy_sub_type_id,
                    'parent_policy_id' => $policy_parent_id,
                    'proposal_approval' => 'N',
                    'customer_search_status' => 'N',
                    'premium_type' => '',
                    'suminsured_type' => $data1['siBasisName'],
                    'policy_enrollment_start_date' => $data['policy_start_date'],
                    'policy_enrollment_end_date' => $data['policy_end_date'],
                ]);
                $policy_id = $this->db->insert_id();
                foreach ($members as $key => $value) {
                    $this->db->insert('policy_age_limit', [
                        'relation_id' => $value,
                        'policy_detail_id' => $policy_id,
                        'min_age' => $minages[$key],
                        'max_age' => $maxages[$key],
                        'premium' => 0,
                        'employee_contri' => 0,
                        'employer_contri' => 0,
                    ]);
                    // }
                }
                if($policy_id){
                    $result=true;
                }else{
                    $result=false;
                }
            }
        }
        $this->db->insert('cd_balance_transaction_log', [
            'policy_detail_id' => $policy_id,
            'amount' => 0,
            'transaction_type' => 'cr',
            'created_date' => date('Y-m-d H:i:s'),
        ]);


        $this->db->insert('master_broker_ic_relationship', [
            'policy_id' => $policy_id,
            // 'relationship_id' => $family_cons_rel,
            'relationship_id' => implode(',',$members),
            'max_adult' => $data['adult_count'],
            'max_child' => $data['child_count'],
            'twins_child_limit' => 0,
        ]);
        $this->policyCreationSaveExcel($policy_id, $basisType);
        if ($result) {

                echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created successfully.'), "Data" => $plan_id));
                exit;
            } else {
                echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
                exit;
            }

    }
    function policyCreationSaveExcel($policy_id, $policySubType) {

        if($policySubType)

        extract($this->input->post(null, true));


        $this->load->library("excel");
        //    var_dump($_POST);exit;

        if ($fileUploadType) {
           // var_dump($_FILES);exit;
            $filename = $_FILES['filename']['tmp_name'];
           // $premiumfilename = $_FILES['premiumfilename']['tmp_name'];

            $premiumfilename='';

            if (!$filename) {

                return ['mgs' => 'Please add 1 file to create policy'];
            }

            $objPHPExcel = PHPExcel_IOFactory::load($filename);
            //$objPHPExcel1 = PHPExcel_IOFactory::load($premiumfilename);

            if($fileUploadType == 'byAge') {

                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    //$worksheetTitle = $worksheet->getTitle();
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    /* $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                      $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                      $nrColumns = ord($highestColumn) - 64;
                      $total_rows = $highestRow - 1; */

                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $policyAge = $worksheet->getCellByColumnAndRow(0, $col);

                        $sumInsured = $worksheet->getCellByColumnAndRow(1, $col);

                        $premium = $worksheet->getCellByColumnAndRow(2, $col);

                        $employeeContri = 0; //$worksheet->getCellByColumnAndRow(3, $col);

                        $employerContri = 0; //$worksheet->getCellByColumnAndRow(4, $col);
                        $tax = $worksheet->getCellByColumnAndRow(3, $col);
                        $premium_with_tax = $worksheet->getCellByColumnAndRow(4, $col);
                        if ($premium_with_tax >= $premium) {
                            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                            if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                                mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                            }
                            $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/age_wise_xl.xls');
                            $this->db->insert('policy_creation_age', [
                                'policy_id' => $policy_id,
                                'policy_age' => $policyAge,
                                'sum_insured' => $sumInsured,
                                'premium' => $premium,
                                'premium_tax' => $tax,
                                'premium_with_tax' => $premium_with_tax,
                                'employee_contri_percent' => $employeeContri,
                                'employer_contri_percent' => $employerContri,
                                'file_path' => '/application/resources/policy/' . $policy_id . '/age_wise_xl.xls'
                            ]);
                            // echo "here";exit;
                        } else {
                            return false;
                        }
                    }
                }
            } else if($fileUploadType == 'byMemberAge') {

                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                    $highestRow = $worksheet->getHighestRow(); // e.g. 10


                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $policyAge = $worksheet->getCellByColumnAndRow(0, $col);

                        $sumInsured = $worksheet->getCellByColumnAndRow(1, $col);

                        $premium = $worksheet->getCellByColumnAndRow(2, $col);

                        $employeeContri = 0; //$worksheet->getCellByColumnAndRow(3, $col);

                        $employerContri = 0; //$worksheet->getCellByColumnAndRow(4, $col);
                        $tax = $worksheet->getCellByColumnAndRow(3, $col);
                        $premium_with_tax = $worksheet->getCellByColumnAndRow(4, $col);
                        if ($premium_with_tax >= $premium) {
                            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                            if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                                mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                            }
                            $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/mem_age_wise_xl.xls');
                            $this->db->insert('policy_creation_age', [
                                'policy_id' => $policy_id,
                                'policy_age' => $policyAge,
                                'sum_insured' => $sumInsured,
                                'premium' => $premium,
                                'premium_tax' => $tax,
                                'premium_with_tax' => $premium_with_tax,
                                'employee_contri_percent' => $employeeContri,
                                'employer_contri_percent' => $employerContri,
                                'file_path' => '/application/resources/policy/' . $policy_id . '/mem_age_wise_xl.xls'
                            ]);
                        } else {
                            return false;
                        }
                    }
                }
            } else if($fileUploadType == 'byDesignation') {


                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                    $highestRow = $worksheet->getHighestRow(); // e.g. 10


                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $policydesignation = $worksheet->getCellByColumnAndRow(0, $col);

                        $sumInsured = $worksheet->getCellByColumnAndRow(1, $col);
                        //print_pre();exit();
                        $premium = $worksheet->getCellByColumnAndRow(2, $col);

                        $employeeContri = 0; //$worksheet->getCellByColumnAndRow(3, $col);

                        $employerContri = 0; //$worksheet->getCellByColumnAndRow(4, $col);

                        $tax = $worksheet->getCellByColumnAndRow(3, $col);
                        $premium_with_tax = $worksheet->getCellByColumnAndRow(4, $col);
//                         print_pre(trim($premium));
//                         print_pre($premium_with_tax);exit();
                        // if ($premium_with_tax >= $premium) {
                        if ($sumInsured) {
                            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                            if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                                mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                            }
                            $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/designation_wise_xl.xls');
                            $designation = $this->db->where(['designation_name' => trim($policydesignation)])->get('master_designation')->row_array();
                            $designationId = $designation['master_desg_id'];

                            if ($designation == "") {
                                $this->db->insert('master_designation', [
                                    'designation_name' => ucwords(strtolower($policydesignation)),
                                ]);
                                $designationId = $this->db->insert_id();
                            }


                            $this->db->insert('policy_creation_designation', [
                                'policy_id' => $policy_id,
                                'policy_designation' => $designationId,
                                'sum_insured' => trim($sumInsured),
                                // 'premium' => $premium,
                                'policy_desig_tax' => trim($tax),
                                'policy_desig_premiumWithtax' => trim($premium_with_tax),
                                'file_path' => '/application/resources/policy/' . $policy_id . '/designation_wise_xl.xls'
                            ]);
                        } else {
                            return false;
                        }
                    }
                }


            }

            else if($fileUploadType == 'byFamilyConstruct'){
                //echo "IN byFamilyConstruct";exit;
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10

                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $family_type = $worksheet->getCellByColumnAndRow(0, $col);
                        $family_types = explode("+",$family_type);

                        if(count($family_types) > 1){
                            $family_child=  $family_types[1][0];
                        }
                        else {
                            $family_child = 0;
                        }

                        $sumInsured = $worksheet->getCellByColumnAndRow(1, $col);
                        $premium = $worksheet->getCellByColumnAndRow(2, $col);
                        $tax = $worksheet->getCellByColumnAndRow(3, $col);
                        $premium_with_tax = $worksheet->getCellByColumnAndRow(4, $col);

                        if ($premium_with_tax >= $premium) {

                            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                            if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                                mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                            }

                            $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/age_wise_xl.xls');
                            $this->db->insert('family_construct_wise_si', [
                                'policy_detail_id' => $policy_id,
                                'family_type' => $family_type,
                                'sum_insured' => $sumInsured,
                                'adult'=>$family_types[0][0],
                                'child'=>$family_child,
                                'premium' => $premium,
                                'policy_family_tax' => trim($tax),
                                'PremiumServiceTax' => trim($premium_with_tax),
                                'file_path' => '/application/resources/policy/' . $policy_id . '/family_construct_xl.xls'
                            ]);

                        } else {
                            return false;
                        }
                    }
                }
            }
            else if($fileUploadType == 'byFamilyAgeConstruct'){

                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $family_type = $worksheet->getCellByColumnAndRow(0, $col);
                        $family_types = explode("+",$family_type);

                        if(count($family_types) > 1){
                            $family_child=  $family_types[1][0];
                        }
                        else {
                            $family_child = 0;
                        }

                        $age_group = $worksheet->getCellByColumnAndRow(1, $col);
                        $sumInsured = $worksheet->getCellByColumnAndRow(2, $col);
                        $premium = $worksheet->getCellByColumnAndRow(3, $col);
                        $tax = $worksheet->getCellByColumnAndRow(4, $col);
                        $premium_with_tax = $worksheet->getCellByColumnAndRow(5, $col);

                        if ($premium_with_tax >= $premium) {

                            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                            if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                                mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                            }

                            $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/family_age_wise_xl.xls');
                            $this->db->insert('family_construct_age_wise_si', [
                                'policy_detail_id' => $policy_id,
                                'family_type' => $family_type,
                                'age_group' => $age_group,
                                'sum_insured' => $sumInsured,
                                'adult'=>$family_types[0][0],
                                'child'=>$family_child,
                                'premium' => $premium,
                                'policy_family_tax' => trim($tax),
                                'PremiumServiceTax' => trim($premium_with_tax),
                                'file_path' => '/application/resources/policy/' . $policy_id . '/family_age_construct_xl.xls'
                            ]);

                        } else {
                            return false;
                        }
                    }
                }
            }
            else {

                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    //$worksheetTitle = $worksheet->getTitle();
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    /* $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                      $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                      $nrColumns = ord($highestColumn) - 64;
                      $total_rows = $highestRow - 1; */

                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $policyGrade = $worksheet->getCellByColumnAndRow(0, $col);

                        $sumInsured = $worksheet->getCellByColumnAndRow(1, $col);

                        $premium = $worksheet->getCellByColumnAndRow(2, $col);

                        // $relation = $worksheet->getCellByColumnAndRow(3, $col);

                        $employeeContri = 0; //$worksheet->getCellByColumnAndRow(3, $col);

                        $employerContri = 0; //$worksheet->getCellByColumnAndRow(4, $col);
                        $s_premium = $worksheet->getCellByColumnAndRow(3, $col);
                        $son_premium = $worksheet->getCellByColumnAndRow(4, $col);
                        $d_premium = $worksheet->getCellByColumnAndRow(5, $col);
                        $f_premium = $worksheet->getCellByColumnAndRow(6, $col);
                        $m_premium = $worksheet->getCellByColumnAndRow(7, $col);
                        $f_inlow_premium = $worksheet->getCellByColumnAndRow(8, $col);
                        $m_inlow_premium = $worksheet->getCellByColumnAndRow(9, $col);
                        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                        if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                            mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                        }
                        $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/grade_wise_xl.xls');

                        if ($policySubType == 2 || $policySubType == 3) {
                            $this->db->insert('grade_permilli', [
                                'policy_id' => $policy_id,
                                'grade' => strtoupper($policyGrade),
                                'sum_insured' => $sumInsured,
                                'per_mili' => $premium,
                                'file_path' => 'application/resources/policy/' . $policy_id . '/grade_wise_xl.xls'
                            ]);
                        } else {
                            $this->db->insert('policy_creation_grade', [
                                'policy_id' => $policy_id,
                                'policy_grade' => strtoupper($policyGrade),
                                'sum_insured' => $sumInsured,
                                'premium' => $premium,
                                //'relation' => $relation,
                                'employee_contri_percent' => $employeeContri,
                                'employer_contri_percent' => $employerContri,
                                'spouse_premium' => $s_premium,
                                'son_premium' => $son_premium,
                                'daughter_premium' => $d_premium,
                                'mother_premium' => $m_premium,
                                'father_premium' => $f_premium,
                                'mother_in_premium' => $m_inlow_premium,
                                'father_in_premium' => $f_inlow_premium,
                                'file_path' => '/application/resources/policy/' . $policy_id . '/grade_wise_xl.xls'
                            ]);
                        }
                    }
                }
            }



        }


    }
    function check_policy_exist($policy_sub_type_id,$policy_parent_id,$basisType){
        $query=$this->db->query("select policy_detail_id from employee_policy_detail where parent_policy_id='".$policy_parent_id."' and product_name=".$policy_sub_type_id);
       // echo $this->db->last_query();
        if($this->db->affected_rows() >0){
            $where=array('parent_policy_id'=>$policy_parent_id,'product_name'=>$policy_sub_type_id);
            $delete=$this->db->delete('employee_policy_detail',$where);
            $policy_detail_id=$query->row()->policy_detail_id;
            $where2=array('policy_detail_id'=>$policy_detail_id);
            $delete2=$this->db->delete('policy_age_limit',$where2);
            return true;
        }else{
            return true;
        }

     }

    function insertBatchData($tbl_name, $data_array, $sendid = NULL)
    {
        $this->db->insert_batch($tbl_name, $data_array);
        $result_id = $this->db->insert_id();

        if ($sendid == 1) {
            //return id
            return $result_id;
        }
    }
    function insertData($tbl_name, $data_array, $sendid = NULL)
    {
        $this->db->insert($tbl_name, $data_array);
        $result_id = $this->db->insert_id();

        if ($sendid == 1) {
            //return id
            return $result_id;
        }

        return;
    }





}
