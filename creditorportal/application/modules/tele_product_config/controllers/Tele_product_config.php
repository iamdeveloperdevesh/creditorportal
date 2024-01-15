<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//session_start(); //we need to call PHP's session object to access it through CI
class Tele_product_config extends CI_Controller
{
	function __construct()
	{
      //  echo 123444;die;
		parent::__construct();
		//checklogin();
	//	$this->RolePermission = getRolePermissions();
        $this->db = $this->load->database('telesales_fyntune',true);
        $this->load->model('Policy_creation_m', 'obj_pcreate');

    }

	function index($is_policy_created=0)
	{
	  //  echo 123;exit;
		$result = array();
        $data['members'] = $this->getMembers();
//var_dump($data['members']);die;
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
        // $data['payment_workflows'] = $this->getPaymentWorkflow();
        $data['policytypes'] = $this->getPolicyType();
        // $data['policysubtypes'] = $this->getPolicySubType();
        //echo 123;exit;
        $data['is_policy_created']=$is_policy_created;
        $telesession = $this->session->userdata('telesales_session');
        $data['s_axis_process'] = $telesession['axis_process'];
		$this->load->view('template/header_tele.php');
		$this->load->view('tele_product_config/index',$data);
		$this->load->view('template/footer_tele.php');
	}
	function edit_details(){
	    //edit_policy_details
       // var_dump();exit;
        $data=array();
      //  echo base64_decode($_GET['text']);die;
        $id=base64_decode($_GET['text']);
        $data['id']=$id;
        $this->load->view('template/header_tele.php');
        $this->load->view('tele_product_config/edit_policy_details',$data);
        $this->load->view('template/footer_tele.php');
    }
    function edit_details_after($parent_id=''){
        $data=array();
        $data['id']=$parent_id;
        $data['policyview'] = 1;
        $data['members'] = $this->getMembers();
        $data['datalist']=$this->getPlanDetails($parent_id);
        $data['adult_members'] = $data['child_members'] = array();
        foreach ($data['members'] as $member) {
            if ($member->is_adult == "Y") {
                $data['adult_members'][] = $member;
            } else {
                $data['child_members'][] = $member;
            }
        }
        $this->load->view('template/header_tele.php');
        $this->load->view('tele_product_config/edit_policy_details',$data);
        $this->load->view('template/footer_tele.php');
    }

    function getPlanDetails($plan_id=''){
	    //echo 123;

        $return =false;
        if($plan_id == ''){
            $plan_id=$this->input->post('plan_id');
        }else{
            $return =true;
        }
//	    echo "select id,product_name,policy_type_id,concat(policy_subtype_id) from product_master_with_subtype where policy_parent_id=''".$plan_id;
	    $query=$this->db->query("select id,product_name,policy_type_id,
(select policy_name from master_policy_type mpt where mpt.policy_type_id = pms.policy_type_id) as policy_name,
(select policy_sub_type_name from master_policy_sub_type mpst where mpst.policy_sub_type_id = pms.policy_subtype_id) as policy_sub_type_name,
(policy_subtype_id),product_code,
(select group_concat(mapping_id) from policy_payment_customer_mapping ppm where ppm.parent_id =pms.policy_parent_id) as policy_payment_modes  from product_master_with_subtype pms where policy_parent_id='".$plan_id."'")->result();
        $data=array();
        $subtype_id=array();
        $subtype_name=array();
        foreach ($query as $q){
             $subtype_id[]=$q->policy_subtype_id;
             $subtype_name[]=$q->policy_sub_type_name;

            $data['id']=$q->id;
            $data['product_name']=$q->product_name;
            $data['policy_type_id']=$q->policy_type_id;
            $data['product_code']=$q->product_code;
            $data['policy_payment_modes']=$q->policy_payment_modes;
            $data['policy_name']=$q->policy_name;
        }
	    $data['policy_subtype_id']=implode(",",$subtype_id);
	    $data['policy_subtype_name']=implode(",",$subtype_name);
	    $data['subtype_id_array']=$subtype_id;
	    $data['subtype_name_array']=$subtype_name;
        if($return){
            return $data;
        }else{
            echo json_encode($data);
            exit;
        }

    }
    function getPlanDeatilsSubtype(){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
	    $subtype_id=$this->input->post('subtype_id');
	    $parent_id=$this->input->post('parent_id');
	    $query=$this->db->query("select *,
(select group_concat(max_adult,',',max_child) from master_broker_ic_relationship mbc where mbc.policy_id=epd.policy_detail_id ) as max_count
from employee_policy_detail epd where policy_sub_type_id=".$subtype_id." AND parent_policy_id='".$parent_id."'")->row();
	  //echo $this->db->last_query();die;
      ///  $data['policy_premium'] = $this->getPolicyPremium($subtype_id);
       // var_dump($query);die;

        $queryTo=$this->db->query("select * from policy_age_limit where policy_detail_id=".$query->policy_detail_id)->result();
       $dataMember=array();
        foreach ($queryTo as $row){
            if($row->member_type_id == 'years'){
                $data1=array(
                    "member_min_age"=>$row->min_age,
                    "member_max_age"=>$row->max_age,
                    "fr_id"=>$row->relation_id,
                );
            }else{
                $data1=array(
                    "member_min_age_days"=>$row->min_age,
                    "member_max_age"=>$row->max_age,
                    "fr_id"=>$row->relation_id,
                );
            }
            array_push($dataMember,$data1);

        }

        if($this->db->affected_rows() > 0){
            $response['data']=$query;
            $response['data2']=$dataMember;
            $response['status']=200;
        }else{
            $response['status']=201;
        }echo json_encode($response);
    }
	function viewPolicyDetails(){
        $this->load->view('template/header_tele.php');
        $this->load->view('tele_product_config/view_policy_detail');
        $this->load->view('template/footer_tele.php');
    }
    function getPolicyDetails(){
        $result = array();
        $result["sEcho"] = $_GET['sEcho'];
        $insurerListing['Data']= $this->getProductsList();
        //var_dump($insurerListing['Data']);die;
        $result["iTotalRecords"] = $insurerListing['Data']['totalRecords'];	//iTotalRecords get no of total recors
        $result["iTotalDisplayRecords"] = $insurerListing['Data']['totalRecords']; //iTotalDisplayRecords for display the no of records in data table.

        $items = array();
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        if (!empty($insurerListing['Data']['query_result']) && count($insurerListing['Data']['query_result']) > 0) {
            for ($i = 0; $i < sizeof($insurerListing['Data']['query_result']); $i++) {
                $temp = array();
                array_push($temp, $insurerListing['Data']['query_result'][$i]['product_name']);
                array_push($temp, $insurerListing['Data']['query_result'][$i]['policy_type']);
                array_push($temp, 'Active');
               /* if ($insurerListing['Data']['query_result'][$i]['isactive'] == 1) {
                    array_push($temp, 'Active');
                } else {
                    array_push($temp, 'In-Active');
                }*/

                $actionCol = "";
                //if($this->privilegeduser->hasPrivilege("CategoriesAddEdit"))
                //{
                $actionCol .= '<a href="tele_product_config/edit_details?text=' . rtrim(strtr(base64_encode($insurerListing['Data']['query_result'][$i]['policy_parent_id']), '+/', '-_'), '=') . '" title="Edit">
					<span class="spn-9"><i class="ti-pencil"></i></span></a>';
                //}
                //if($this->privilegeduser->hasPrivilege("CategoryDelete")){
              //  if ($insurerListing['Data']['query_result'][$i]['isactive'] == 1) {
                    $actionCol .= '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="deleteData(\'' . $insurerListing['Data']['query_result'][$i]['id'] . '\');" title="Delete">
						<span class="spn-9"><i class="ti-trash" style="color: red;"></i></span></a>';
              //  }
                //}



                array_push($temp, $actionCol);
                array_push($items, $temp);
            }
          // var_dump($items);die;
        }

        $result["aaData"] = $items;
        echo json_encode($result);
        exit;
    }

    function getProductsList()
    {
        $query=  $this->db->query("select id,product_name,policy_type_id,policy_parent_id,(select (policy_name) from master_policy_type mps where mps.policy_type_id=pm.policy_type_id) as policy_type
 from product_master_with_subtype pm where pm.is_active=1 group by policy_parent_id order by created_at desc 
");
        if ($query->num_rows() >= 1) {
            $totcount = $query->num_rows();
            return array("query_result" => $query->result_array(), "totalRecords" => $totcount);
        } else {
            return array("totalRecords" => 0);
        }
    }
    public function deletePolicy(){
	  $id=  $this->input->post('id');
	  if($id){
	      $where=array(
	          'id'=>$id
          );
	      $this->db->where($where);
	      $query=$this->db->update('product_master_with_subtype',array('is_active'=>0));
	      if($query){
              $result["status"] = 200;
              $result["msg"] = "Deleted Successfully.";
              echo json_encode($result);
              exit;
          }else{
              $result["status"] = 201;
              $result["msg"] = "Something went wrong.";
              echo json_encode($result);
              exit;
          }
      }else{
          $result["status"] = 201;
          $result["msg"] = "Something went wrong.";
          echo json_encode($result);
          exit;
      }

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
        //var_dump($policy_subtypes_id);die;
        //  echo $this->db;exit;

        $data1 = $this->db->select("*")
            ->from('product_master_with_subtype')
            ->where('product_master_with_subtype.product_name', trim($product_name))
            ->get()
            ->result_array();

        if(count($data1) < 1){

            $test = $this->generateRandomString() . uniqid();

            for($i=0; $i<count($policy_subtypes_id); $i++){

               $abc= $this->db->insert('product_master_with_subtype', array(
                    'product_name' => trim($product_name),
                    'policy_parent_id' => $test,
                    'policy_subtype_id' => $policy_subtypes_id[$i],
                    'product_code' => $product_code,
                    'policy_type_id'=>$policy_types_id
                ));



            }
            if($abc){
                for($j = 0; $j<count($payment_modes); $j++){

                    $this->db->insert('policy_payment_customer_mapping', array(
                        'parent_id' => $test,
                        'mapping_id' => $payment_modes[$j],
                        'type' => 'P',
                        'status' => 'Active',

                    ));
                }
            }
            $data['result']=$abc;
            $data['error']=$this->db->error();
            $data['policy_subtypes_id']=$policy_subtypes_id;
            $id = $this->db->insert_id();
            if($abc){
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
                        $this->db->insert('master_group_code', array(
                            'product_code' => $product_code,
                            'group_code' => $group_code,
                            'EW_group_code' => $EW_group_code,
                            'spouse_group_code' => $spouse_group_code,
                            'family_construct' => $family_construct,
                            'si_per_member' => $si_per_member,
                            'si_group' => $si_group
                        ));
                    }else{
                        $this->db->insert('master_group_code', array(
                            'product_code' => $product_code,
                            'group_code' => $group_code,
                            'spouse_group_code' => $spouse_group_code,
                            'family_construct' => $family_construct,
                            'si_per_member' => $si_per_member,
                            'si_group' => $si_group
                        ));
                    }


                }
            }

            echo json_encode($data);
        }
        else {
            echo json_encode(false);
        }

    }

    function update_product_detail(){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $this->load->library('excel');
        extract($this->input->post(null, true));
      //  var_dump($_POST);die;
        $parent_id=$plan_id;
        //var_dump($policy_sub_type);die;
        $policy_subtypes_id = $policy_sub_type;
        //var_dump($policy_subtypes_id);die;
        //  echo $this->db;exit;

        $data1 = $this->db->select("*")
            ->from('product_master_with_subtype')
            ->where('product_master_with_subtype.policy_parent_id', trim($parent_id))
            ->get()
            ->result_array();
          //  var_dump($data1);die;
        if(count($data1) > 0){
            //echo 123;
            $test = $parent_id;


            for($i=0; $i<count($policy_subtypes_id); $i++){
                $this->db->where(array('policy_parent_id'=>$test,'policy_subtype_id'=>$policy_subtypes_id[$i]));
                $abc= $this->db->update('product_master_with_subtype', array(
                    'product_name' => trim($plan_name),
                    'product_code' => $product_code,
                    'policy_type_id'=>$policy_type
                ));

            }
          //  var_dump($abc);die;
            if($abc){
                $this->db->delete('policy_payment_customer_mapping',array('parent_id'=>$test));
                for($j = 0; $j<count($payment_modes); $j++){

                    $this->db->insert('policy_payment_customer_mapping', array(
                        'parent_id' => $test,
                        'mapping_id' => $payment_modes[$j],
                        'type' => 'P',
                        'status' => 'Active',

                    ));
                }
            }
            $data['result']=$abc;
            $data['error']=$this->db->error();
            $data['policy_subtypes_id']=$policy_subtypes_id;
            $id = $this->db->insert_id();
            if($abc){
                $data['subtype'] = $this->db->select("*")
                    ->from('product_master_with_subtype , master_policy_sub_type ')
                    ->where('product_master_with_subtype.policy_subtype_id = master_policy_sub_type.policy_sub_type_id')
                    ->where('product_master_with_subtype.policy_parent_id', $test)
                    ->get()
                    ->result_array();
            }
            $data['parent_id']=$test;


            if(count($_FILES) > 0){
                $GroupCodeFile = $_FILES['filename']['tmp_name'];
                $objPHPExcel = PHPExcel_IOFactory::load($GroupCodeFile);
                $this->db->delete('master_group_code',array('product_code'=>$product_code));
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

                            $this->db->insert('master_group_code', array(
                                'product_code' => $product_code,
                                'group_code' => $group_code,
                                'spouse_group_code' => $spouse_group_code,
                                'family_construct' => $family_construct,
                                'si_per_member' => $si_per_member,
                                'si_group' => $si_group
                            ));


                    }
                }
            }

            $data['msg']='Updated Successfully!';
            echo json_encode($data);
        }
        else {
            $data['msg']='Something went wrong!';
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
        $siBasisNew=array('flate'=>1,'family_construct'=>2,'family_construct_age'=>3,'memberAge'=>4,'permilerate'=>5,'deductable'=>6,'perdaytenure'=>7);
        $data['insurer_id'] = $this->input->post('masterInsurance');
        $data['policy_start_date'] =$this->input->post('policyStartDate');
        $data['policy_end_date'] = $this->input->post('policyEndDate');
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
        $data['mandatory_optional'] = ($this->input->post('mandatory_optional'));
        $data['Combo_option'] = ($this->input->post('Combo_option'));
        //echo 123;exit;
        // $response = json_decode(curlFunction(SERVICE_URL . '/api2/addNewPolicy', $data), true);
        $response = $this->addNewPolicy1($data);
           // var_dump($response);die;
        if ($response['status_code'] == '200') {
            echo json_encode(array('success' => true, 'msg' => $response['Metadata']['Message'], 'data' => $response['Data']));
            exit;
        } else {
            echo json_encode(array('success' => false, 'msg' => $response['Metadata']['Message']));
            exit;
        }
    }
    function UpdatePolicyNew(){
        /*var_dump($_POST);
        exit;*/

        $data = array();
        $this->load->library('excel');
        $data['utoken'] = $_SESSION['webpanel']['utoken'];
        $data['policy_sub_type_id'] = $this->input->post('policy_sub_type_id');
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
        $siBasisNew=array('flate'=>1,'family_construct'=>2,'family_construct_age'=>3,'memberAge'=>4,'permilerate'=>5,'deductable'=>6,'perdaytenure'=>7);
        $data['insurer_id'] = $this->input->post('masterInsurance');
        $data['policy_start_date'] =$this->input->post('policyStartDate');
        $data['policy_end_date'] = $this->input->post('policyEndDate');
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
        $data['mandatory_optional'] = ($this->input->post('mandatory_optional'));
        $data['Combo_option'] = ($this->input->post('Combo_option'));
        //echo 123;exit;
        // $response = json_decode(curlFunction(SERVICE_URL . '/api2/addNewPolicy', $data), true);
        $response = $this->updateNewPolicy1($data);
           // var_dump($response);die;
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
        $data['is_optional'] = intval($this->input->post('mandatory_optional'));
        $data['is_combo'] = intval($this->input->post('Combo_option'));
        $adult_count = $this->input->post('adult_count');
        $child_count = $this->input->post('child_count');
        $data['isactive'] = 1;
        $data['pdf_type'] = $this->input->post('pdf_type');
        $data['insurer_id'] = $this->input->post('masterInsurance');
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
        $sitype = $data1['sitype'];
        $sibasisAll = $data1['sibasis'];
        $d=$data1['siBasisName'];
        $basisType= $sibasisAll[$d];
      //  echo $basisType;exit;
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
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
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
//echo $policy_parent_id;die;
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
                    'product_name'=>$policy_sub_type_id,
                    'parent_policy_id' => $policy_parent_id,
                    'proposal_approval'=>'N',
                    'customer_search_status'=>'N',
                    'premium_type' => '',
                    'suminsured_type' => $data1['siBasisName'],
                    'policy_enrollment_start_date' => $data['policy_start_date'],
                    'is_optional' => $data['is_optional'],
                    'is_combo' => $data['is_combo'],
                    'policy_enrollment_end_date' => $data['policy_end_date'],

                ]);

                $policy_id = $this->db->insert_id();
       // echo $policy_id;die;
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

                    'is_optional' => $data['is_optional'],
                    'is_combo' => $data['is_combo'],
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
        else if($basisType == 4){
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
                    //'TPA_id' => $TPA_id,
                    'sum_insured_type' => $sitype,

                    'pdf_status' => $data['pdf_type'],
                    'special_child_contri' => $special_child_contri,
                    'unmarried_child_check' => $isunMaryChildCheck,
                    'unmarried_child_contri' => $unmarried_child_contri,
                    'unmarried_child_cover' => $strUnMaryChild,
                    'policy_status' => 'Pending',
                    'product_name' => $policy_sub_type_id,
                    'parent_policy_id' => $policy_parent_id,
                    'proposal_approval' => 'N',
                    'customer_search_status' => 'N',
                    'premium_type' => '',
                    'is_optional' => $data['is_optional'],
                    'is_combo' => $data['is_combo'],
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
        else if($basisType == 5){
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
                    //'TPA_id' => $TPA_id,
                    'sum_insured_type' => $sitype,

                    'pdf_status' => $data['pdf_type'],

                    'policy_status' => 'Pending',
                    'product_name' => $policy_sub_type_id,
                    'parent_policy_id' => $policy_parent_id,
                    'proposal_approval' => 'N',
                    'customer_search_status' => 'N',
                    'is_optional' => $data['is_optional'],
                    'is_combo' => $data['is_combo'],
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
        else if($basisType == 6){
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
                    //'TPA_id' => $TPA_id,
                    'sum_insured_type' => $sitype,

                    'pdf_status' => $data['pdf_type'],
                    'special_child_contri' => $special_child_contri,
                    'unmarried_child_check' => $isunMaryChildCheck,
                    'unmarried_child_contri' => $unmarried_child_contri,
                    'unmarried_child_cover' => $strUnMaryChild,
                    'policy_status' => 'Pending',
                    'product_name' => $policy_sub_type_id,
                    'parent_policy_id' => $policy_parent_id,
                    'proposal_approval' => 'N',
                    'customer_search_status' => 'N',
                    'is_optional' => $data['is_optional'],
                    'is_combo' => $data['is_combo'],
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
        else if($basisType == 7){
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
                    //'TPA_id' => $TPA_id,
                    'sum_insured_type' => $sitype,
                    'is_optional' => $data['is_optional'],
                    'is_combo' => $data['is_combo'],
                    'pdf_status' => $data['pdf_type'],
                    'special_child_contri' => $special_child_contri,
                    'unmarried_child_check' => $isunMaryChildCheck,
                    'unmarried_child_contri' => $unmarried_child_contri,
                    'unmarried_child_cover' => $strUnMaryChild,
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
                    'is_optional' => $data['is_optional'],
                    'is_combo' => $data['is_combo'],
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
        if ($basisType != 1) {
            $this->policyCreationSaveExcel($policy_id, $basisType);
        }
       // var_dump($result);die;
        if ($result == true) {

            echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record created successfully.'), "Data" => $plan_id));
            exit;
        } else {
            echo json_encode(array("status_code" => "400", "Metadata" => array("Message" => 'No data found.'), "Data" => NULL));
            exit;
        }

    }
    function updateNewPolicy1($data1)
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
        $data['insurer_id'] = $this->input->post('masterInsurance');
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
        $data['policy_subType_id'] =  $this->input->post('policy_subType_id');
        $data['parent_policy_id'] =  $this->input->post('parent_policy_id');
        $data['mandatory_optional'] = ($this->input->post('mandatory_optional'));
        $data['Combo_option'] = ($this->input->post('Combo_option'));
        $sitype = $data1['sitype'];
        $sibasisAll = $data1['sibasis'];
        $d=$data1['siBasisName'];
        $basisType= $sibasisAll[$d];
      //  echo $basisType;exit;
        $members =  $this->input->post('member');
        $minages =  $this->input->post('minage');
        $maxages =  $this->input->post('maxage');
        $min_age_type =  $this->input->post('min_age_type');
        $mandatory_if_not_selected = $this->input->post('mandatory_if_not_selected');


        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $policySUBID=$policy_sub_type_id;
        $policy_parent_id= $data['parent_policy_id'];
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

                $sum_insured = implode(',',array_filter($this->input->post('sum_insured_opt1')));
                $premium_si = implode(',',array_filter($this->input->post('premium_opt')));
                $tax = implode(',',$this->input->post('tax_opt'));
                $premium_with_tax = implode(',',$this->input->post('tax_opt'));
            }
            $special_child_contri = 0;
//echo $policy_parent_id;die;
         //   $query_check_policy_exist=$this->check_policy_exist($policy_sub_type_id,$policy_parent_id,$basisType);
          //  echo$data['policy_subType_id'];die;
            if(isset($data['policy_subType_id'])){

                $where=array(
                    "policy_detail_id"=>$data['policy_subType_id']
                );
                $set=array( 'policy_no' => trim($data['policy_number']),
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
                    'is_optional' => $data['is_optional'],
                    'is_combo' => $data['is_combo'],
                    'pdf_status' => $data['pdf_type'],
                    'special_child_contri' => $special_child_contri,
                    'unmarried_child_check' => $isunMaryChildCheck,
                    'unmarried_child_contri' => $unmarried_child_contri,
                    'unmarried_child_cover' => $strUnMaryChild,

                    'sum_insured' => $sum_insured,
                    'premium' => $premium_si,


                    'policy_status' => 'Pending',
                    'product_name'=>$policy_sub_type_id,
                    'proposal_approval'=>'N',
                    'customer_search_status'=>'N',
                    'premium_type' => '',
                    'suminsured_type' => $data1['siBasisName'],
                    'policy_enrollment_start_date' => $data['policy_start_date'],
                    'policy_enrollment_end_date' => $data['policy_end_date'],);
                $this->db->where($where);
            $query=  $this->db->update('employee_policy_detail',$set);

            $deleteOld=$this->db->delete("policy_age_limit",$where);
            if($deleteOld){
                foreach ($members as $key => $value) {
                    $this->db->insert('policy_age_limit', [
                        'relation_id' => $value,
                        'policy_detail_id' => $data['policy_subType_id'],
                        'min_age' => $minages[$key],
                        'max_age' => $maxages[$key],
                        'premium' => 0,
                        'employee_contri' => 0,
                        'employer_contri' => 0,
                    ]);
                    // }
                }
            }
                $where=array(
                    "policy_id"=>$data['policy_subType_id']
                );
                $deleteOld=$this->db->delete("master_broker_ic_relationship",$where);
                $this->db->insert('master_broker_ic_relationship', [
                    'policy_id' => $data['policy_subType_id'],
                    // 'relationship_id' => $family_cons_rel,
                    'relationship_id' => implode(',',$members),
                    'max_adult' => $data['adult_count'],
                    'max_child' => $data['child_count'],
                    'twins_child_limit' => 0,
                ]);

                if($query){
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

            if(isset($data['policy_subType_id'])){

                $where=array(
                    "policy_detail_id"=>$data['policy_subType_id']
                );
                $set=array( 'policy_no' => trim($data['policy_number']),
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
                    'is_optional' => $data['is_optional'],
                    'is_combo' => $data['is_combo'],
                    'pdf_status' => $data['pdf_type'],
                    'special_child_contri' => $special_child_contri,
                    'unmarried_child_check' => $isunMaryChildCheck,
                    'unmarried_child_contri' => $unmarried_child_contri,
                    'unmarried_child_cover' => $strUnMaryChild,

                    'sum_insured' => $sum_insured,
                    'premium' => $premium_si,


                    'policy_status' => 'Pending',
                    'product_name'=>$policy_sub_type_id,
                    'proposal_approval'=>'N',
                    'customer_search_status'=>'N',
                    'premium_type' => '',
                    'suminsured_type' => $data1['siBasisName'],
                    'policy_enrollment_start_date' => $data['policy_start_date'],
                    'policy_enrollment_end_date' => $data['policy_end_date'],);
                $this->db->where($where);
                $query=  $this->db->update('employee_policy_detail',$set);

                $deleteOld=$this->db->delete("policy_age_limit",$where);
                if($deleteOld){
                    foreach ($members as $key => $value) {
                        $this->db->insert('policy_age_limit', [
                            'relation_id' => $value,
                            'policy_detail_id' => $data['policy_subType_id'],
                            'min_age' => $minages[$key],
                            'max_age' => $maxages[$key],
                            'premium' => 0,
                            'employee_contri' => 0,
                            'employer_contri' => 0,
                        ]);
                        // }
                    }
                }

                if($query){
                    $result=true;
                }else{
                    $result=false;
                }
            }
        }
        else if($basisType == 4){
            if(isset($data['policy_subType_id'])){

                $where=array(
                    "policy_detail_id"=>$data['policy_subType_id']
                );
                $set=array( 'policy_no' => trim($data['policy_number']),
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

                    'sum_insured' => $sum_insured,
                    'premium' => $premium_si,
                    'is_optional' => $data['is_optional'],
                    'is_combo' => $data['is_combo'],

                    'policy_status' => 'Pending',
                    'product_name'=>$policy_sub_type_id,
                    'proposal_approval'=>'N',
                    'customer_search_status'=>'N',
                    'premium_type' => '',
                    'suminsured_type' => $data1['siBasisName'],
                    'policy_enrollment_start_date' => $data['policy_start_date'],
                    'policy_enrollment_end_date' => $data['policy_end_date'],);
                $this->db->where($where);
                $query=  $this->db->update('employee_policy_detail',$set);

                $deleteOld=$this->db->delete("policy_age_limit",$where);
                if($deleteOld){
                    foreach ($members as $key => $value) {
                        $this->db->insert('policy_age_limit', [
                            'relation_id' => $value,
                            'policy_detail_id' => $data['policy_subType_id'],
                            'min_age' => $minages[$key],
                            'max_age' => $maxages[$key],
                            'premium' => 0,
                            'employee_contri' => 0,
                            'employer_contri' => 0,
                        ]);
                        // }
                    }
                }

                if($query){
                    $result=true;
                }else{
                    $result=false;
                }
            }
        }
        else if($basisType == 5){
            if(isset($data['policy_subType_id'])){

                $where=array(
                    "policy_detail_id"=>$data['policy_subType_id']
                );
                $set=array( 'policy_no' => trim($data['policy_number']),
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

                    'sum_insured' => $sum_insured,
                    'premium' => $premium_si,
                    'is_optional' => $data['is_optional'],
                    'is_combo' => $data['is_combo'],

                    'policy_status' => 'Pending',
                    'product_name'=>$policy_sub_type_id,
                    'proposal_approval'=>'N',
                    'customer_search_status'=>'N',
                    'premium_type' => '',
                    'suminsured_type' => $data1['siBasisName'],
                    'policy_enrollment_start_date' => $data['policy_start_date'],
                    'policy_enrollment_end_date' => $data['policy_end_date'],);
                $this->db->where($where);
                $query=  $this->db->update('employee_policy_detail',$set);

                $deleteOld=$this->db->delete("policy_age_limit",$where);
                if($deleteOld){
                    foreach ($members as $key => $value) {
                        $this->db->insert('policy_age_limit', [
                            'relation_id' => $value,
                            'policy_detail_id' => $data['policy_subType_id'],
                            'min_age' => $minages[$key],
                            'max_age' => $maxages[$key],
                            'premium' => 0,
                            'employee_contri' => 0,
                            'employer_contri' => 0,
                        ]);
                        // }
                    }
                }

                if($query){
                    $result=true;
                }else{
                    $result=false;
                }
            }
        }
        else if($basisType == 6){
            if(isset($data['policy_subType_id'])){

                $where=array(
                    "policy_detail_id"=>$data['policy_subType_id']
                );
                $set=array( 'policy_no' => trim($data['policy_number']),
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

                    'sum_insured' => $sum_insured,
                    'premium' => $premium_si,
                    'is_optional' => $data['is_optional'],
                    'is_combo' => $data['is_combo'],

                    'policy_status' => 'Pending',
                    'product_name'=>$policy_sub_type_id,
                    'proposal_approval'=>'N',
                    'customer_search_status'=>'N',
                    'premium_type' => '',
                    'suminsured_type' => $data1['siBasisName'],
                    'policy_enrollment_start_date' => $data['policy_start_date'],
                    'policy_enrollment_end_date' => $data['policy_end_date'],);
                $this->db->where($where);
                $query=  $this->db->update('employee_policy_detail',$set);

                $deleteOld=$this->db->delete("policy_age_limit",$where);
                if($deleteOld){
                    foreach ($members as $key => $value) {
                        $this->db->insert('policy_age_limit', [
                            'relation_id' => $value,
                            'policy_detail_id' => $data['policy_subType_id'],
                            'min_age' => $minages[$key],
                            'max_age' => $maxages[$key],
                            'premium' => 0,
                            'employee_contri' => 0,
                            'employer_contri' => 0,
                        ]);
                        // }
                    }
                }

                if($query){
                    $result=true;
                }else{
                    $result=false;
                }
            }
        }
        else if($basisType == 7){
            if(isset($data['policy_subType_id'])){

                $where=array(
                    "policy_detail_id"=>$data['policy_subType_id']
                );
                $set=array( 'policy_no' => trim($data['policy_number']),
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

                    'sum_insured' => $sum_insured,
                    'premium' => $premium_si,
                    'is_optional' => $data['is_optional'],
                    'is_combo' => $data['is_combo'],

                    'policy_status' => 'Pending',
                    'product_name'=>$policy_sub_type_id,
                    'proposal_approval'=>'N',
                    'customer_search_status'=>'N',
                    'premium_type' => '',
                    'suminsured_type' => $data1['siBasisName'],
                    'policy_enrollment_start_date' => $data['policy_start_date'],
                    'policy_enrollment_end_date' => $data['policy_end_date'],);
                $this->db->where($where);
                $query=  $this->db->update('employee_policy_detail',$set);

                $deleteOld=$this->db->delete("policy_age_limit",$where);
                if($deleteOld){
                    foreach ($members as $key => $value) {
                        $this->db->insert('policy_age_limit', [
                            'relation_id' => $value,
                            'policy_detail_id' => $data['policy_subType_id'],
                            'min_age' => $minages[$key],
                            'max_age' => $maxages[$key],
                            'premium' => 0,
                            'employee_contri' => 0,
                            'employer_contri' => 0,
                        ]);
                        // }
                    }
                }

                if($query){
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

            if(isset($data['policy_subType_id'])){

                $where=array(
                    "policy_detail_id"=>$data['policy_subType_id']
                );
                $set=array( 'policy_no' => trim($data['policy_number']),
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
                    'is_optional' => $data['is_optional'],
                    'is_combo' => $data['is_combo'],

                    'policy_status' => 'Pending',
                    'product_name'=>$policy_sub_type_id,
                    'proposal_approval'=>'N',
                    'customer_search_status'=>'N',
                    'premium_type' => '',
                    'suminsured_type' => $data1['siBasisName'],
                    'policy_enrollment_start_date' => $data['policy_start_date'],
                    'policy_enrollment_end_date' => $data['policy_end_date'],);
                $this->db->where($where);
                $query=  $this->db->update('employee_policy_detail',$set);

                $deleteOld=$this->db->delete("policy_age_limit",$where);
                if($deleteOld){
                    foreach ($members as $key => $value) {
                        $this->db->insert('policy_age_limit', [
                            'relation_id' => $value,
                            'policy_detail_id' => $data['policy_subType_id'],
                            'min_age' => $minages[$key],
                            'max_age' => $maxages[$key],
                            'premium' => 0,
                            'employee_contri' => 0,
                            'employer_contri' => 0,
                        ]);
                        // }
                    }
                }

                if($query){
                    $result=true;
                }else{
                    $result=false;
                }
            }
        }

        $whereNew=array(
            "policy_detail_id"=>$data['policy_subType_id']
        );
        $setNew=array('policy_id' => $data['policy_subType_id'],
            // 'relationship_id' => $family_cons_rel,
            'relationship_id' => implode(',',$members),
            'max_adult' => $data['adult_count'],
            'max_child' => $data['child_count'],
            'twins_child_limit' => 0,);
        $this->db->where($whereNew);
        $this->db->update('master_broker_ic_relationship',$setNew);
        if ($basisType != 1) {
            $this->policyupdateSaveExcel($data['policy_subType_id'], $basisType);
        }
       // var_dump($result);die;
        if ($result == true) {

            echo json_encode(array("status_code" => "200", "Metadata" => array("Message" => 'Record updated successfully.'), "Data" => $plan_id));
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
      //  var_dump($_FILES);exit;
        $fileUploadType=$companySubTypePolicy;
        if ($companySubTypePolicy != 'flate' && $fileUploadType) {
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
            }
            else if($fileUploadType == 'byMemberAge') {

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
            }
            else if($fileUploadType == 'byDesignation') {


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
            else if($fileUploadType == 'permilerate'){
                $dataexcel=array();
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $row=$col;
                        $age_band = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                        $tenure = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                        $policy_rate = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                        $numbers_of_ci = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                        $group_code = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                        $group_code_spouse = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                        //$sum_insured = $worksheet->getCellByColumnAndRow(6, $row)->getValue();

                        if (trim($numbers_of_ci) == '') {
                            $numbers_of_ci = 0;
                        }


                            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                            if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                                mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                            }

                            $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/per_mil_rate_wise.xls');
                        if (trim($age_band) != '' and strlen($age_band) > 1) {

                            $ages = preg_split('/(–|-)/', str_replace(' ', '', $age_band));

                            $premium_min_age = $ages[0];
                            $premium_max_age = $ages[1];

                            $dataexcel[] = array(
                                'master_policy_id'   => $policy_id,
                                'age_band'   => $age_band,
                                'min_age' => $premium_min_age,
                                'max_age' => $premium_max_age,
                                'tenure'   => $tenure,
                                'policy_rate'   => $policy_rate,
                                'created_by'   => 0,
                                'group_code'	=> $group_code,
                                'group_code_spouse'	=> $group_code_spouse
                                //'sum_insured' => $sum_insured
                            );
                        }


                    }
                }
               // var_dump($dataexcel);die;
                if(count($dataexcel) > 0){

                    $this->insertBatchData('master_policy_premium_permile', $dataexcel);

                }else{
                    return false;
                }

            }
            else if($fileUploadType == 'perdaytenure'){
                $dataexcel=array();
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $row=$col;
                        $tenure = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                        $sum_insured = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                        $premium = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                        $tax = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                        $group_code = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                        $group_code_spouse = $worksheet->getCellByColumnAndRow(5, $row)->getValue();



                            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                            if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                                mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                            }

                            $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/per_day_tenure_wise.xls');
                       // if (!empty($premium)) {
                            $dataexcel[] = array(
                                'master_policy_id'   => $policy_id,
                                'tenure'  => $tenure,
                                'sum_insured'   => $sum_insured,
                                'premium_rate'    => $premium,
                                'is_taxable'  => $tax,
                                'group_code'	=> $group_code,
                                'group_code_spouse'	=> $group_code_spouse
                            );
                     //   }


                    }
                }
              //  var_dump($dataexcel);die;
                if(count($dataexcel) > 0){
                    $this->insertBatchData('master_per_day_tenure_premiums', $dataexcel);
                }else{
                    return false;
                }

            }
            else if($fileUploadType == 'deductable'){
                $dataexcel=array();
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $row=$col;
                        $adult_count = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                        $child_count = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                        $deductable = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                        $sum_insured = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                        $premium = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                        $tax = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                        $group_code = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                        $group_code_spouse = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                      
                        if($child_count == 0){
                            $family_type=$adult_count.'A';
                        }else{
                            $family_type=$adult_count.'A+'.$child_count.'C';
                        }
                        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                        if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                            mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                        }

                        $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/per_mil_rate_wise.xls');
                        if (!empty($premium)) {


                            $this->db->insert('family_construct_wise_si', [
                                'policy_detail_id' => $policy_id,
                                'family_type' => $family_type,
                                'sum_insured' => $sum_insured,
                                'adult'=>$adult_count,
                                'child'=>$child_count,
                                'premium' => $premium,
                                'policy_family_tax' => trim($tax),
                                'deductable' => trim($deductable),
                                'file_path' => '/application/resources/policy/' . $policy_id . '/family_construct_ded_xl.xls'
                            ]);
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
    function policyupdateSaveExcel($policy_id, $policySubType) {

        if($policySubType)

            extract($this->input->post(null, true));


        $this->load->library("excel");
        //    var_dump($_POST);exit;
      //  var_dump($_FILES);exit;
        $fileUploadType=$companySubTypePolicy;
        if ($companySubTypePolicy != 'flate' && $fileUploadType) {
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
                    $where=array("policy_id"=>$policy_id);
                    $deleteOld=$this->db->delete("policy_creation_age",$where);
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
            }
            else if($fileUploadType == 'byMemberAge') {

                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    $where=array("policy_id"=>$policy_id);
                    $deleteOld=$this->db->delete("policy_creation_age",$where);

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
            }
            else if($fileUploadType == 'byDesignation') {


                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                    $highestRow = $worksheet->getHighestRow(); // e.g. 10

                    $where=array("policy_id"=>$policy_id);
                    $deleteOld=$this->db->delete("policy_creation_designation",$where);
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
                $where=array("policy_detail_id"=>$policy_id);
                $deleteOld=$this->db->delete("family_construct_wise_si",$where);
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
                $where=array("policy_detail_id"=>$policy_id);
                $deleteOld=$this->db->delete("family_construct_age_wise_si",$where);
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
            else if($fileUploadType == 'permilerate'){
                $dataexcel=array();
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $row=$col;
                        $age_band = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                        $tenure = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                        $policy_rate = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                        $numbers_of_ci = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                        $group_code = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                        $group_code_spouse = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                        //$sum_insured = $worksheet->getCellByColumnAndRow(6, $row)->getValue();

                        if (trim($numbers_of_ci) == '') {
                            $numbers_of_ci = 0;
                        }


                            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                            if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                                mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                            }

                            $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/per_mil_rate_wise.xls');
                        if (trim($age_band) != '' and strlen($age_band) > 1) {

                            $ages = preg_split('/(–|-)/', str_replace(' ', '', $age_band));

                            $premium_min_age = $ages[0];
                            $premium_max_age = $ages[1];

                            $dataexcel[] = array(
                                'master_policy_id'   => $policy_id,
                                'age_band'   => $age_band,
                                'min_age' => $premium_min_age,
                                'max_age' => $premium_max_age,
                                'tenure'   => $tenure,
                                'policy_rate'   => $policy_rate,
                                'created_by'   => 0,
                                'group_code'	=> $group_code,
                                'group_code_spouse'	=> $group_code_spouse
                                //'sum_insured' => $sum_insured
                            );
                        }


                    }
                }
               // var_dump($dataexcel);die;
                if(count($dataexcel) > 0){
                    $where=array("master_policy_id"=>$policy_id);
                    $deleteOld=$this->db->delete("master_policy_premium_permile",$where);
                    $this->insertBatchData('master_policy_premium_permile', $dataexcel);

                }else{
                    return false;
                }

            }
            else if($fileUploadType == 'perdaytenure'){
                $dataexcel=array();
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $row=$col;
                        $tenure = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                        $sum_insured = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                        $premium = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                        $tax = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                        $group_code = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                        $group_code_spouse = $worksheet->getCellByColumnAndRow(5, $row)->getValue();



                            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                            if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                                mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                            }

                            $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/per_day_tenure_wise.xls');
                       // if (!empty($premium)) {
                            $dataexcel[] = array(
                                'master_policy_id'   => $policy_id,
                                'tenure'  => $tenure,
                                'sum_insured'   => $sum_insured,
                                'premium_rate'    => $premium,
                                'is_taxable'  => $tax,
                                'group_code'	=> $group_code,
                                'group_code_spouse'	=> $group_code_spouse
                            );
                     //   }


                    }
                }
              //  var_dump($dataexcel);die;
                if(count($dataexcel) > 0){
                    $where=array("master_policy_id"=>$policy_id);
                    $deleteOld=$this->db->delete("master_per_day_tenure_premiums",$where);
                    $this->insertBatchData('master_per_day_tenure_premiums', $dataexcel);
                }else{
                    return false;
                }

            }
            else if($fileUploadType == 'deductable'){
                $dataexcel=array();
                $where=array("policy_detail_id"=>$policy_id);
                $deleteOld=$this->db->delete("family_construct_wise_si",$where);
                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    for ($col = 2; $col <= $highestRow; ++$col) {
                        $row=$col;
                        $adult_count = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                        $child_count = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                        $deductable = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                        $sum_insured = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                        $premium = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                        $tax = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                        $group_code = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                        $group_code_spouse = $worksheet->getCellByColumnAndRow(7, $row)->getValue();

                        if($child_count == 0){
                            $family_type=$adult_count.'A';
                        }else{
                            $family_type=$adult_count.'A+'.$child_count.'C';
                        }
                        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                        if (!file_exists(APPPATH . 'resources/policy/' . $policy_id)) {
                            mkdir(APPPATH . 'resources/policy/' . $policy_id, 0777, true);
                        }

                        $writer->save(APPPATH . 'resources/policy/' . $policy_id . '/per_mil_rate_wise.xls');
                        if (!empty($premium)) {


                            $this->db->insert('family_construct_wise_si', [
                                'policy_detail_id' => $policy_id,
                                'family_type' => $family_type,
                                'sum_insured' => $sum_insured,
                                'adult'=>$adult_count,
                                'child'=>$child_count,
                                'premium' => $premium,
                                'policy_family_tax' => trim($tax),
                                'deductable' => trim($deductable),
                                'file_path' => '/application/resources/policy/' . $policy_id . '/family_construct_ded_xl.xls'
                            ]);
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

?>