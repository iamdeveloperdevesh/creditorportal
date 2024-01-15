<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
require(APPPATH.'libraries/razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
class Teleproposal extends CI_Controller
{
	function __construct()
	{//echo 123;die;
		parent::__construct();
		//checklogin();
        $this->algoMethod = 'aes-128-ecb';
        $this->hashMethod = 'SHA512';
        $this->hash_key = 'razorpay';
        $this->encrypt_key = 'axisbank12345678';
        $this->keyId = 'rzp_test_HxRHmUmojTTNs4';
        $this->keySecret = 'JEkpsKDaDPZ9RNoBpomvm2ib';
        $this->displayCurrency = 'INR';
	//	$this->RolePermission = getRolePermissions();
        $this->db = $this->load->database('telesales_fyntune',true);
        $this->load->model('teleproposal_m', 'obj_home');
        if($this->input->get('leadid', TRUE)){
            // echo "in";exit;
            $lead_id = encrypt_decrypt_password($_REQUEST['leadid'],'D');
            if($lead_id != 0 || $lead_id != ''){
                $res = $this->db->select('employee_details.emp_id,employee_details.product_id,product_master_with_subtype.policy_parent_id')
                    ->from('employee_details,product_master_with_subtype')
                    ->where('employee_details.product_id = product_master_with_subtype.product_code')
                    ->where('employee_details.lead_id',$lead_id)
                    ->get()->row_array();
            }
//echo $this->db->last_query();exit;

            if(!empty($res)){
                $_SESSION['telesales_session']['emp_id'] = $res['emp_id'];
                $_SESSION['telesales_session']['product_code'] = $res['product_id'];
                $_SESSION['telesales_session']['parent_id'] = $res['policy_parent_id'];
            }else{
                echo "No Data Found!";exit;
            }
        }
        $telSalesSession = $this->session->userdata('telesales_session');
        $this->agent_id = encrypt_decrypt_password($telSalesSession['agent_id'],'D');
        $this->emp_id = $telSalesSession['emp_id'];

        $this->parent_id = $telSalesSession['parent_id'];
        $this->lead_id = $lead_id;

    }
    public function get_combo_rel()
    {


        echo json_encode($this->obj_home->get_combo_rel());
    }
    public function get_premium_data()
    {
        echo json_encode($this->obj_home->get_premium_data());

    }
    public function thankyou()
    {
        $lead_id = $this
            ->input
            ->post('hiddenleadId');
        $query = $this->db->query("select p.*,ed.lead_id from proposal as p left join employee_details as ed ON p.emp_id = ed.emp_id where ed.lead_id ='$lead_id'");
        if($query->num_rows()>0)
        {

            $data= $query->result_array();
        }
        if($create_proposal == 'create_proposal')
        {
            $data['proposal_data'] = $query->result_array();
            $data['create_proposal'] = 'create_proposal';
            $this->load->view('template/customer-header.php');
            $this->load->view('teleproposal/si_thankyou',compact('data'));
            $this->load->view('template/footer_tele.php');        }
        else
        {
            $this->load->view('template/customer-header.php');
            $this->load->view('teleproposal/si_thankyou',compact('data'));
            $this->load->view('template/footer_tele.php');        }


//print_r($data);die;

        //  $this->load->telesales_template("thankyou",compact('data_policy','data','MandateLink_data'));
    }
    function combo_set_data()
    {
        $empId = $this->emp_id;
        $data = $this->db->query("select * from modal_age_dob where  emp_id = '$empId'")->result_array();
        echo json_encode($data);
    }
function get_premium_plan()
{
    extract($this->input->post());

    $rel_allowed = [];
     $empId = $this->emp_id;
    $get_fr_data = $this->db->query("select * from modal_age_dob where emp_id = '$empId' ")->result_array();
   // print_r($get_fr_data);die;
    $familyConstruct = $_POST['family_construct'];
    $familyConstruct = str_replace(' ', '+', $familyConstruct);
    $cons = explode("+", $familyConstruct);
    $policy_detail_id = $this->obj_home->get_all_data_sumPremium($hiddenpolicyarr);
    //    print_r($policy_detail_id);die;
    $max_limit_data = $this->obj_home->get_adult_child_limit($policy_detail_id);
//print_r($max_limit_data);die;
    $rel_allowed   = explode(",",$max_limit_data[0]['relationship_id']);

    $memArr = [];
    if($max_limit_data[0]['max_adult']."A">=$cons[0])
    {//echo 23;die;
        if(!empty($get_fr_data) && $cons[0] == '1A'){
            // echo 123;die;
            foreach($get_fr_data as $key=>$data)
            {
                //  print_r($data);
                $fr_id = $data[fr_id];
                if($fr_id !=0 && $data['rel_key'] =='A') {
                    $dob = $data['dob'];
                    $ages = $this->cal_age($dob);
                    //$adult_key = $data[$key.'_adult'];
                    $age = $ages['age'];
                    $age_type = $ages['age_type'];
                    array_push($memArr, $fr_id);
                    // $res = $this->obj_home->check_validations($fr_id, $max_limit_data[0]['policy_id'], $empId, $age,$age_type,$familyConstruct);
                    // print_r(123);die;

                }
                else{
                    $self_det = $this->db->query("select bdate from employee_details where emp_id = '$empId'")->row_array();
                    // print_r($self_det);die;
                    $dob= $self_det['bdate'];
                    $ages = $this->cal_age($dob);
                    $age = $ages['age'];
                    //array_push($age_arr,$age_self);
                    $age_type = $ages['age_type'];

                }
                $get_premium = $this->get_premium_with_age($dob, $max_limit_data[0]['policy_id'], $empId, $age, $age_type, $familyConstruct, $sumInsured, $hiddenpolicyarr);

            }}else
        {
//echo 123;die;
            $age_arr = [];
            if(!empty($get_fr_data)){
                foreach($get_fr_data as $key=>$data)
                {
                    $fr_id = $data['fr_id'];
                    $dob = $data['dob'];
                    //$adult_key = $data[$key.'_adult'];
                    $ages = $this->cal_age($dob);
                    $age_other = $ages['age'];
                    array_push($age_arr,$age_other);
                    $age_other_type = $ages['age_type'];
                    //$res = $this->obj_home->check_validations($fr_id, $max_limit_data[0]['policy_id'], $empId, $age_other,$age_other_type,$familyConstruct);

                    array_push($memArr,$fr_id);

                }}
//echo "select bdate from employee_details where emp_id = '$empId'";die;
            $self_det = $this->db->query("select bdate from employee_details where emp_id = '$empId'")->row_array();
            // print_r($self_det);die;
            $dob_self = $self_det['bdate'];
            $ages_self = $this->cal_age($dob_self);
            $age_self = $ages_self['age'];
            array_push($age_arr,$age_self);
            $age_type = $ages_self['age_type'];
            $age = max($age_arr);
//print_R($age);

            array_push($memArr,0);
            $get_premium = $this->get_premium_with_age($dob_self,$max_limit_data[0]['policy_id'],$empId, $age,$age_type,$familyConstruct,$sumInsured,$hiddenpolicyarr);
            //  print_r($get_premium);die;
        }

    }



    $memarr_implode = implode(',',$memArr);

    $result= ['status' => true, 'message'=>'true', 'get_premium' => $get_premium,'memArr'=>array_unique($memArr),'rel_allowed'=>$rel_allowed ];
    echo json_encode($result);
}
    function familyconst_dob()
    {
        extract($this->input->post());

        $rel_allowed = [];
        $empId = $this->emp_id;
        $familyConstruct = $_POST['family_construct'];
        $familyConstruct = str_replace(' ', '+', $familyConstruct);
        $cons = explode("+", $familyConstruct);
         $policy_detail_id = $this->obj_home->get_all_data_sumPremium($hiddenpolicyarr);
     //    print_r($policy_detail_id);die;
        $max_limit_data = $this->obj_home->get_adult_child_limit($policy_detail_id);
//print_r($max_limit_data);die;
        $rel_allowed   = explode(",",$max_limit_data[0]['relationship_id']);

        $memArr = [];
        if($max_limit_data[0]['max_adult']."A">=$cons[0])
        {//echo 23;die;
            if(!empty($rel_name) && $adult_fr_id != 0 && $cons[0] == '1A'){
               // echo 123;die;
                foreach($rel_name as $key=>$data)
                {
                  //  print_r($data);
                    $fr_id = $data[$key.'_rel'];
                    $dob = $data[$key.'_datebirth'];
                    $ages = $this->cal_age($dob);
                    $adult_key = $data[$key.'_adult'];
                    $age = $ages['age'];
                    $age_type = $ages['age_type'];
                    array_push($memArr,$fr_id);
                    $res = $this->obj_home->check_validations($fr_id, $max_limit_data[0]['policy_id'], $empId, $age,$age_type,$familyConstruct);
                   // print_r(123);die;
                    if($res['message'] != 'true'){
                        echo json_encode($res); exit;
                    }else{
                        $insert_data = $this->insert_data($fr_id,$dob,$key,$empId,$age,$age_type,$adult_key);}

                }
              //  $get_premium = $this->get_premium_with_age($dob,$max_limit_data[0]['policy_id'],$empId, $age,$age_type,$familyConstruct,$sumInsured,$hiddenpolicyarr);
            }else
            {
//echo 123;die;
                $age_arr = [];
                if(!empty($rel_name)){
                    foreach($rel_name as $key=>$data)
                    {
                        $fr_id = $data[$key.'_rel'];
                        $dob = $data[$key.'_datebirth'];
                        $adult_key = $data[$key.'_adult'];
                        $ages = $this->cal_age($dob);
                        $age_other = $ages['age'];
                        array_push($age_arr,$age_other);
                        $age_other_type = $ages['age_type'];
                        $res = $this->obj_home->check_validations($fr_id, $max_limit_data[0]['policy_id'], $empId, $age_other,$age_other_type,$familyConstruct);

                        array_push($memArr,$fr_id);
                        if($res['message'] != 'true'){
                        //    echo 45;
                            echo json_encode($res); exit;
                        }else{
                            $insert_data = $this->insert_data($fr_id,$dob,$key,$empId,$age_other,$age_other_type,$adult_key);}
                    }}
//echo "select bdate from employee_details where emp_id = '$empId'";die;
                $self_det = $this->db->query("select bdate from employee_details where emp_id = '$empId'")->row_array();
               // print_r($self_det);die;
                $dob_self = $self_det['bdate'];
                $ages_self = $this->cal_age($dob_self);
                $age_self = $ages_self['age'];
                array_push($age_arr,$age_self);
                $age_type = $ages_self['age_type'];
                $age = max($age_arr);
//print_R($age);

                array_push($memArr,0);
              //  $get_premium = $this->get_premium_with_age($dob_self,$max_limit_data[0]['policy_id'],$empId, $age,$age_type,$familyConstruct,$sumInsured,$hiddenpolicyarr);
          //  print_r($get_premium);die;
            }

        }

        if($max_limit_data[0]['max_child']."K">=$cons[1] && $cons[1]!='')
        {
           // echo 23;die;
            foreach($kid_rel as $key=>$kid_data)
            {
                $fr_id = $kid_data[$key.'_rel'];

                $dob = $kid_data[$key.'_date_birth'];
                $kid_key = 'K';
                $ages = $this->cal_age($dob);

                $age = $ages['age'];
                $age_type = $ages['age_type'];
                array_push($memArr,$fr_id);
//print_r($max_limit_data[0]['policy_id']);
                $res = $this->obj_home->check_validations($fr_id, $max_limit_data[0]['policy_id'], $empId, $age,$age_type,$familyConstruct);

                if($res['message'] != 'true'){
                   // echo 65;die;
                    echo json_encode($res); exit;
                }else{
                    $insert_data = $this->insert_data($fr_id,$dob,$key,$empId,$age,$age_type,$kid_key);}

            }
        }

        $memarr_implode = implode(',',$memArr);
//print_r($memarr_implode); die;
        $policy_for = array(
            'policy_for'=>$memarr_implode
        );
        $this->db->where('emp_id',$empId);
        $this->db->update('employee_details',$policy_for);
        $result= ['status' => true, 'message'=>'true', 'memArr'=>array_unique($memArr),'rel_allowed'=>$rel_allowed ];
        echo json_encode($result);
    }
    function insert_data($fr_id,$dob,$key,$empId,$age,$age_type,$rel_key)
    {
        $data = array(
            'fr_id'=> $fr_id,
            'dob'=>$dob,
            'rel_name'=>$key,
            'emp_id'=>$empId,
            'age'=>$age,
            'age_type'=>$age_type,
            'rel_key'=>$rel_key
        );

        $sql = $this->db->query("select * from modal_age_dob where fr_id = '$fr_id' and emp_id = '$empId' and rel_name = '$key'")->row_array();
        if (count($sql) > 0) {
            $this->db->where('emp_id',$empId);
            $this->db->where('rel_name',$key);
            $this->db->where('fr_id',$fr_id);
            $this->db->update('modal_age_dob',$data);




        }
        else{

            $this->db->insert('modal_age_dob',$data);


        }



    }

    function cal_age($dob){
        $arr = [];
        $today = date("Y-m-d",strtotime(date("Y-m-d")));

        $diff = date_diff(date_create(date('d-m-Y', strtotime($dob))) , date_create($today));
        $age = $diff->format('%y');
        $age_type = 'years';
        if($age == 0){
            $age = $diff->format('%a');
            $age_type = 'days';
        }
//print_R($age);die;
        $arr = ['age'=>$age,'age_type'=>$age_type];
        return $arr;
    }

    function get_premium_with_age($dob,$max_policy,$empId, $age,$age_type,$familyConstruct,$sumInsured,$policy_detail_id)
    {//print_r($dob);die;
        if($dob!=''){
            $arr_policy = [];
            $policyNo = json_decode($policy_detail_id);
          //  print_r($policyNo);die;
            foreach($policyNo as $key=>$value){
                $policy_ids = $value->policy_no;
                $sumInsured = $value->sum_insured;
                $deductable = $value->deductable;
                $get_policy_data = $this->db->query("select mpst.policy_sub_type_name from master_policy_sub_type as mpst,employee_policy_detail epd where epd.policy_sub_type_id = mpst.policy_sub_type_id AND epd.policy_detail_id = '$policy_ids'")->row_array();
                $get_premium = $this->obj_home->get_premium_from_policy($policy_ids,$sumInsured,$familyConstruct,$age,$deductable);
           //   print_r($get_premium);
                $arr_policy[] = ["policy_sub_type_name"=>$get_policy_data['policy_sub_type_name'],"premium"=>$get_premium,"sumInsured"=>$sumInsured];
            }

        }

        return $arr_policy;
    }
    function get_refresh_const_premium(){
        extract($this->input->post());
        $arr_data = [];
        $memArr = [];
        $pre_data = [];
        $rel_data = [];
        $empId = encrypt_decrypt_password($_POST['empId'],'D');
        $policy_for = $this->db->query("select policy_for from employee_details where emp_id = '$empId'")->row_array();
        $policy_member_fr_id = explode(',',$policy_for['policy_for']);
        $familyConstruct = $_POST['family_construct'];
        $familyConstruct = str_replace(' ', '+', $familyConstruct);
        $cons = explode("+", $familyConstruct);
        $max_limit_data = $this->obj_home->get_adult_child_limit($policy_detail_id);
        $rel_allowed   = explode(",",$max_limit_data[0]['relationship_id']);
        $policyNo = explode(',',$policy_detail_id);
        foreach($policyNo as $key=>$policy_ids){
            $get_member_data = $this->obj_home->get_all_member_data_new($empId, $policy_ids);
            //print_R(array_filter($get_member_data));
            $arr_data = array_merge($arr_data,$get_member_data);
            //array_merge($arr,$get_member_data);
        }
        //print_r($arr_data);
        foreach($arr_data as $data){

            $fr_id = $data['fr_id'];
            $policy_ids = $data['policy_detail_id'];
            $get_policy_data = $this->db->query("select mpst.policy_sub_type_name from master_policy_sub_type as mpst,employee_policy_detail epd where epd.policy_sub_type_id = mpst.policy_sub_type_id AND epd.policy_detail_id = '$policy_ids'")->row_array();
            $policy_sub_type_name =  $get_policy_data['policy_sub_type_name'];
            $get_premium = $data['policy_mem_sum_premium'];
            $memArr['premium'] = $get_premium;
            $memArr['policy_sub_type_name'] = $policy_sub_type_name;
            $memArr['sumInsured'] = $sumInsured;
            array_push($pre_data,$memArr);
            array_push($rel_data,$fr_id);

        }
        $get_premium = array_unique($pre_data, SORT_REGULAR);

        $get_rel = array_unique($policy_member_fr_id, SORT_REGULAR);

        $result= ['status' => true, 'message'=>'true', 'get_premium' => array_values($get_premium),'rel_allowed'=>$rel_allowed,'get_rel'=>$get_rel];
        echo json_encode($result);

    }

    public function family_details_insert()
    {

        // print_r($_POST);exit;
        /*healthpro changes created new function commented old*/
        //$family_data = $this->obj_home->family_details_insert();
        //update product_code in employee_details Table
        if(isset($this->emp_id) && ($this->emp_id != '' || $this->emp_id != undefined)){
            $array = [];
            $product_id = ($_POST["plan_name"] != '' || $_POST["plan_name"] != undefined) ? $_POST["plan_name"] : 'R06';

            if($_POST["plan_name"]){
                $array['product_id'] = $_POST["plan_name"];
            }

            $occupation = (isset($_POST["occupation"]) && ($_POST["occupation"] != '' || $_POST["occupation"] != undefined)) ? $_POST["occupation"] : '';
            $array['occupation'] = $occupation;
            $GCI_optional = (isset($_POST["GCI_optional"]) && ($_POST["GCI_optional"] != '' || $_POST["GCI_optional"] != undefined)) ? $_POST["GCI_optional"] : 'Yes';
            $array['GCI_optional'] = $GCI_optional;
            $array['deductable'] = $_POST["hidden_deductable"];
            $array['pid'] = $_POST["hidden_policy_id"];
            //$array = ["product_id" => $product_id,"occupation" => $occupation,"GCI_optional" => $GCI_optional];
            $this->db->where('emp_id', $this->emp_id);
            $this->db->update('employee_details', $array);
            //dedupe update unique ref no - 17 may 21
          //  $this->update_ref_no($this->emp_id,$product_id);
            // echo $this->db->last_query();exit;
            //set session of parent id selected
            $res = $this->db->get_where('product_master_with_subtype',array("product_code"=> $product_id))->row_array();
            if(!empty($res)){
                $_SESSION['telesales_session']['parent_id'] = $res['policy_parent_id'];
                $_SESSION['telesales_session']['product_code'] = $product_id;
            }

        }
        //echo "in";exit;
        $family_data = $this->obj_home->family_details_insert_new();
        echo json_encode($family_data);
    }
    public function tele_nominee_data_insert()
    {

            $emp_id = $this->emp_id;
            $parent_id = $this->parent_id;
                $nominee_relation =  $this->input->post('nominee_relation', true);
            $nominee_fname =  $this->input->post('nominee_fname', true);
            $nominee_lname =  $this->input->post('nominee_lname', true);
            $nominee_gender =  $this->input->post('nominee_gender', true);
            $nominee_dob =  $this->input->post('nominee_dob', true);
            $nominee_contact =  $this->input->post('nominee_contact', true);
            $nominee_email =  $this->input->post('nominee_email', true);
            $nominee_salutation = $this->input->post('nominee_salutation', true);
            $get_lead_id = $this->db->query("select lead_id,product_id from employee_details where emp_id = '$emp_id' ")->row_array();

            if($get_lead_id['lead_id'] != 0)
            {
                $lead_id = $get_lead_id['lead_id'];
                $product_id = $get_lead_id['product_id'];

            }

            // add nominee
            $this->db->query("Delete from member_policy_nominee where emp_id = '$emp_id'");

            $nominee_data = array(
                'fr_id' => $nominee_relation,
                'nominee_fname' =>$nominee_fname,
                'nominee_lname'=>$nominee_lname,
                'nominee_dob'=>$nominee_dob,
                'nominee_contact'=>$nominee_contact,
                'emp_id'=>$emp_id,
                'confirmed_flag'=>'N',
                'status'=>'active'
            );
           // print_r($nominee_data);die;
            $nominee_data_insert = $this->obj_home->add_nominee($nominee_data);
            if($nominee_data_insert == true)
            {
                //Add Data into logs Table
                $logs_array['data'] = ["type" => "insert_nominee_details","req" => json_encode($nominee_data), "lead_id" => $lead_id,"product_id" => $product_id];
                $this->Logs_m->insertLogs($logs_array);
            }
            $insert_data= ["status" => true, "message" => 'Nominee Added Successfully'];
            print_r(json_encode($insert_data));


    }
    public function tele_payment_details_insert(){
        extract($this->input->post());
        $emp_id = $this->emp_id;

        //if lead marked as junk change proposal status - ankita junk dedupe changes
        if(isset($disposition)){
            $q = "SELECT Dispositions FROM disposition_master WHERE id = '".$disposition."'";
            $disRes = $this->db->query($q)->row_array();
            if(!empty($disRes) && ($disRes['Dispositions'] == "Junk" || $disRes['Dispositions'] == "Not Interested" || $disRes['Dispositions'] == "Not Eligible")){
                //update proposal status to cancel
                $this->db->where_not_in('status', array("Payment Received","Success"));
                $proposalData = $this->db->get_where("proposal",array("emp_id" => $emp_id))->result_array();
                if(!empty($proposalData)){
                    $this->db->where('emp_id', $emp_id);
                    $this->db->update('proposal', array("status"=>"Cancelled"));
                    // Add Data into logs Table
                    $logs_array['data'] = ["type" => "update_proposal_status_on_junk_disposition","req" => json_encode($this->db->last_query()), "lead_id" => $_SESSION['telesales_session']['emp_id'],"product_id" => $_SESSION['telesales_session']['product_code']];
                    $this->Logs_m->insertLogs($logs_array);
                }
            }
        }

        $mode_payment =  $this->input->post('mode_payment', true);
        $preferred_contact_date =  $this->input->post('preferred_contact_date', true);
        $preferred_contact_time =  $this->input->post('preferred_contact_time', true);
        $av_remark =  $this->input->post('av_remark', true);
        $emp_agent_data = array(
            'payment_mode'=> $mode_payment,
            'preferred_contact_date'=> $preferred_contact_date,
            'preferred_contact_time'=> $preferred_contact_time,
            'av_remark'=> $av_remark);

        $this->db->where('emp_id',$emp_id);
        $this->db->update('employee_details',$emp_agent_data);
        $agent_name = $this->db->select('agent_name')->from('tls_agent_mst')->where('id',encrypt_decrypt_password($agent_id,'D'))->get()->row_array()['agent_name'];
        $is_maker_checker=$this->db->select('is_makerchecker_journey')->from('employee_details')->where('emp_id',$emp_id)->get()->row_array();

        /*
                    $lead_creation  = $this->db->select('employee_details.created_at,tls_agent_mst.agent_name')->from('employee_details,tls_agent_mst')
                ->where('employee_details.assigned_to = tls_agent_mst.id')
                ->where('emp_id',$emp_id)
                ->get()->row_array();


                    $agent_type='AV';
                //	echo 123;die;
                    //upendra - maker/checker - 30-07-2021
                    $this->db->select('is_makerchecker_journey');
                                $this->db->from('employee_details');
                                $this->db->where('emp_id',$emp_id);
                                $is_maker_checker = $this->db->get()->row_array();

                                $is_maker_checker = $is_maker_checker['is_makerchecker_journey'];

                            if($is_maker_checker == "yes") {

                                                //$agent_name = $_SESSION['telesales_session']['base_caller_name'];
                                                //$agent_type = "DO";

                                                        if(isset($_SESSION['telesales_session']['base_caller_name'])){
                                                        $agent_name = $_SESSION['telesales_session']['base_caller_name'];
                                                        $agent_type = "DO";
                                                }else{
                                                        $agent_name = $_SESSION['telesales_session']['agent_name'];
                                                        $agent_type = "AV";
                                                }






        $lead_creation  = $this->db->select('employee_details.created_at,tls_base_agent_tbl.base_agent_name as agent_name')->from('employee_details,tls_base_agent_tbl')->where('employee_details.assigned_to = tls_base_agent_tbl.base_id')->where('emp_id',$emp_id)->get()->row_array();


                                        }else{
                        //$agent_type = "";
                    }

                    //upendra - maker/checker - add $agent_type
                    $paymment_array = ['date' => date('Y-m-d H:i:s'),'disposition_id' => $sub_isposition,'agent_name' => $agent_name,'emp_id' => $emp_id,'remarks' => $av_remark,"type"=>$agent_type];
                    $this->db->insert('employee_disposition',$paymment_array);
                    //echo $this->db->last_query();exit;
                //	$lead_creation  = $this->db->select('employee_details.created_at as date,tls_agent_mst.agent_name')->from('employee_details,tls_agent_mst')
                //->where('employee_details.assigned_to = tls_agent_mst.id')
                //->where('emp_id',$emp_id)
                //->get()->row_array();
                    $data = $this->db->select('ed.date as "date",ed.disposition_id,dm.Dispositions,dm.`Sub-dispositions`,ed.agent_name,ed.remarks,ed.type')
                 ->from('employee_disposition ed')
                 ->join('disposition_master dm', 'ed.disposition_id = dm.id')
                 ->where('ed.emp_id',$emp_id)
                // ->group_by('ed.disposition_id')
                 ->order_by('ed.id','desc')
                ->get()
                ->result_array();
                //echo $this->db->last_query();exit;
                $lead_creation_merge[0]['date'] = $lead_creation['created_at'];
                $lead_creation_merge[0]['disposition_id'] = 123;
                $lead_creation_merge[0]['Dispositions'] = 'LEAD';
                $lead_creation_merge[0]['Sub-dispositions'] = 'LEAD CREATION';
                $lead_creation_merge[0]['agent_name'] = $lead_creation['agent_name'];
                $lead_creation_merge[0]['remarks'] = $lead_creation['remarks'];
                $lead_creation_merge[0]['type'] = $agent_type;

                $data = array_merge($lead_creation_merge,$data);*/
        //$disabled = $this->db->select('"Open" as  "Open/Close"')->from('disposition_master')->where('id',$sub_isposition)->get()->row_array()['Open/Close'];
        if($is_maker_checker['is_makerchecker_journey'] == "yes"){
            //$agent_name = $_SESSION['telesales_session']['base_caller_name'];
            //$agent_type = "DO";

            if(isset($_SESSION['telesales_session']['base_caller_name'])){
                $agent_name = $_SESSION['telesales_session']['base_caller_name'];
                $agent_type = "DO";
            }else{
                $agent_name = $_SESSION['telesales_session']['agent_name'];
                $agent_type = "AV";
            }

        }else{ $agent_type = "AV";}
        $paymment_array = ['date' => date('Y-m-d H:i:s'),'disposition_id' => $sub_isposition,'agent_name' => $agent_name,'emp_id' => $emp_id,'remarks' => $av_remark,"type"=>$agent_type];
        $this->db->insert('employee_disposition',$paymment_array);
        if($is_maker_checker['is_makerchecker_journey'] == "yes"){
            //$agent_name = $_SESSION['telesales_session']['base_caller_name'];
            //$agent_type = "DO";




            $data  = $this->db->select('*')
                ->from('employee_disposition ed')
                ->join('disposition_master dm', 'ed.disposition_id = dm.id')
                ->where('emp_id',$emp_id)
                ->order_by('ed.id')
                ->get()
                ->result_array();


            $lead_creation  = $this->db->select('employee_details.created_at,tls_base_agent_tbl.base_agent_name')->from('employee_details,tls_base_agent_tbl')
                ->where('employee_details.assigned_to = tls_base_agent_tbl.base_id')
                ->where('emp_id',$emp_id)
                ->get()->row_array();
            $lead_creation_merge[0]['date'] = $lead_creation['created_at'];
            $lead_creation_merge[0]['disposition_id'] = 123;
            $lead_creation_merge[0]['Dispositions'] = 'LEAD';
            $lead_creation_merge[0]['Sub-dispositions'] = 'LEAD CREATION';
            $lead_creation_merge[0]['agent_name'] = $lead_creation['base_agent_name'];
            $lead_creation_merge[0]['type'] = 'DO';
            //	$agent_type = "DO";
            //	$agent_name =  $lead_creation['base_agent_name'];

            $data = array_merge($lead_creation_merge,$data);
            //     echo json_encode($data);
        }
        else{

            $data  = $this->db->select('*')
                ->from('employee_disposition ed')
                ->join('disposition_master dm', 'ed.disposition_id = dm.id')
                ->where('emp_id',$emp_id)
                //->group_by('dm.Dispositions')
                ->order_by('ed.id')
                ->get()
                ->result_array();
            $lead_creation  = $this->db->select('employee_details.created_at,tls_agent_mst.agent_name')->from('employee_details,tls_agent_mst')
                ->where('employee_details.assigned_to = tls_agent_mst.id')
                ->where('emp_id',$emp_id)
                ->get()->row_array();
            $agent_type = "AV";
            $lead_creation_merge[0]['date'] = $lead_creation['created_at'];
            $lead_creation_merge[0]['disposition_id'] = 123;
            $lead_creation_merge[0]['Dispositions'] = 'LEAD';
            $lead_creation_merge[0]['Sub-dispositions'] = 'LEAD CREATION';
            $lead_creation_merge[0]['agent_name'] = $lead_creation['agent_name'];
            $data = array_merge($lead_creation_merge,$data);
            //  $agent_name =  $lead_creation['agent_name'];
            //echo $this->db->last_query();
            //   echo json_encode($data);
        }
//$paymment_array = ['date' => date('Y-m-d H:i:s'),'disposition_id' => $sub_isposition,'agent_name' => $agent_name,'emp_id' => $emp_id,'remarks' => $av_remark,"type"=>$agent_type];
        //                      $this->db->insert('employee_disposition',$paymment_array);

        $disabled = $this->db->select('Open/Close')->from('disposition_master')->where('id',$sub_isposition)->get()->row_array()['Open/Close'];
        $array = ["status" => true, "message" => 'Payment Details Saved','disabled' => $disabled,'data' => $data];
        print_r(json_encode($array));


    }
    public function health_declaration()
    {

        $product_id = $this->input->post('product_id');


        //updated by upendra on 09-04-2021
        if($product_id == 'T01' || $product_id == 'T03'){
            $ghd = $this->get_declaration($product_id);

            if($product_id == "T01"){
                $parent_id = 'test123';
            }
            if($product_id == 'T03'){
                $parent_id = 'NvpnoiwGGDQPVA23w';
            }

        }
        // if($product_id == 'T03'){
        // 	$ghd = $this->get_declaration($product_id);
        // 	$parent_id = 'NvpnoiwGGDQPVA23w';
        // }
        else{
            $parent_id = $this->parent_id;
        }


        $policy_declaration_data = $this->obj_home->health_declaration($parent_id);
        //	print_pre($policy_declaration_data);exit;
        //$data .= '<table class="table table-bordered text-center">
        //<thead class="text-uppercase">
        //<tr>
        //<th scope="col" style="width: 750px; text-align: left; font-weight:600 !important;">Questionnaire</th>
        //<th scope="col" style="font-weight:600 !important;">Answer</th>
        //</tr>
        //</thead>
        //<tbody id="mydatas">';
        $data = "";
        $data .= '<table class="table table-bordered text-center">
		<tbody id="mydatas">';
        //print_pre($policy_declaration_data);exit;
        foreach ($policy_declaration_data as $key => $value)
        {

            //$data .= '<tr>
            //    <td style="text-align:left; font-weight:600 !important;"> <input type="hidden" class="mycontent" value="' . $value['p_declare_id'] . '"/>' . $value['content'] . '</td>
            //  <td style="width: 150px;"><div class="custom-control custom-radio" style="float: left;"><input type="radio"  name="' . $value['p_declare_id'] . '" id="' . $value['p_declare_id'] . '" class="custom-control-input radios_out" value="Yes" > <label class="custom-control-label" for="' . $value['p_declare_id'] . '"> Yes </label> </div>
            // <div class="custom-control custom-radio" style="float:right;"> <input type="radio" name="' . $value['p_declare_id'] . '" class="custom-control-input radios_out " value="No" id="' . $value['p_declare_id'] . '_1" checked="">  <label class="custom-control-label" for="' . $value['p_declare_id'] . '_1"> No </label></div>
            //  </td>
            //  </tr>';

            $data .= '<tr>
							<td style="border:none;"><input type="checkbox"  name="' . $value['p_declare_id'] . '" id="' . $value['p_declare_id'] . '" class="" value="Yes"></td>
							<td style="text-align:left;border:none; font-weight:600 !important;"> <input type="hidden" class="mycontent" value="' . $value['p_declare_id'] . '"/>' . $value['content'] . '</td>
                     </tr>';

        }
        $data .= '</tbody></table>';


        $response = ['ghd' => html_entity_decode($ghd), 'employee_declaration' => $data];
        //print_pre($response);exit;
        echo json_encode($response);
        //echo  $data;
    }

    public function family_details_relation()
    {

        $data['family_data'] = $this->obj_home->family_details_relation();
        echo json_encode($data);

    }
    function get_family_details_from_relationship()
    {
        $data['family_data'] = $this->obj_home->get_family_details_from_relationship();
        echo json_encode($data);
    }
    public function member_declare_data()
    {
        $parent_id = $this->parent_id;

        $member_id = $this->input->post('member_id',true);

        $policy_member_declare = $this->obj_home->member_declare_data($parent_id);

        $p_sub=$this->db->query("select * from policy_declaration_subtype");

        if(!empty($policy_member_declare))
        {

            if($p_sub->num_rows()>0)
            {
                $data .= '<table class="table table-bordered text-center">
					<thead class="text-uppercase" id="chronic">
					<tr>';
                $results = $p_sub->result_array();


                $data.='Do You Have Any One Or More Of Below Mentioned Chronic Disease ?';

                foreach($results as $sub_types)
                {

                    $data.='<th scope="col" style="width: 750px; text-align: left; font-weight: 600;"><input type="checkbox" class="sub_type" value="'.$sub_types['declare_subtype_id'].'" name="sub_type_check[]" id="subdeclare_'.$sub_types['declare_subtype_id'].$member_id.'" /> '. $sub_types['sub_type_name'].'</th>';

                }
                $data.= '</tr>
					</thead>';

            }

            echo  $data;
        }

    }

    public function get_employee_data_new()
    {

        $emp_id = $this->emp_id;

        $data =  $this->db->select('policy_for,spouse_dob,kid1_dob,kid2_dob,kid1_rel,kid2_rel,GCI_optional,annual_income,occupation,loan_acc_no,cust_id,emp_middlename,occupation,ISNRI,lead_id, json_qote,emp_id,emp_code,emp_firstname,fr_id,company_id,emp_lastname,gender,bdate,mob_no,email,emp_grade,emp_designation,emp_address,emp_city,emp_state,emp_pincode,street,location,flex_amount,total_salary,gmc_grade_id,emp_pay,doj,pancard,adhar,address,comm_address,ref1,ref2,salutation,emp_city,emp_state,emp_pincode,ifsc_code,annual_income')->where(["emp_id" => $emp_id])->get("employee_details")->row();
        echo json_encode($data);
    }
	function index()
	{	$emp_id = $this->emp_id;
        $result = array();
        $telesession = $this->session->userdata('telesales_session');
        $data['center_process'] = $telesession['axis_process'];
        $admin = $telesession['is_admin'];
        $data['lead_id'] = $this->lead_id;
//print_R($data);die;
        extract($this->input->post());

        $data['disposition'] = $this->db->select('*')->from('disposition_master')->where('display', 1)->get()->result_array();


        /*
        $lead_creation  = $this->db->select('employee_details.created_at,employee_details.axis_process,tls_agent_mst.agent_name,employee_details.makerchecker,employee_details.is_makerchecker_journey')->from('employee_details,tls_agent_mst')
        ->where('employee_details.assigned_to = tls_agent_mst.id')
        ->where('emp_id',$emp_id)
        ->get()->row_array();
        */

        //upendra - maker/checker - 30-07-2021
        $check_maker_checker = $this->db->select("makerchecker,is_makerchecker_journey,is_makerchecker_journey")
            ->from('employee_details')
            ->where('emp_id',$emp_id)
            ->get()->row_array();
//		echo $this->db->last_query();exit;
        //print_r($check_maker_checker['is_makerchecker_journey']);exit;
        //$check_maker_checker_flag = $check_maker_checker['is_makerchecker_journey'];
        //echo $check_maker_checker['is_makerchecker_journey'];exit;

        if(empty($check_maker_checker['makerchecker'])&&$check_maker_checker['is_makerchecker_journey']=='yes'){
            $data['disposition'] = $this->db->select('*')->from('disposition_master')->where('display', 1)->where('maker_checker','Maker')->get()->result_array();
        }

        if(strtolower($check_maker_checker['makerchecker'])=='maker'&&$check_maker_checker['is_makerchecker_journey']=='yes'){
            $data['disposition'] = $this->db->select('*')->from('disposition_master')->where('display', 1)->where('maker_checker','Maker')->get()->result_array();
        }

        if(strtolower($check_maker_checker['makerchecker'])=='checker'&&$check_maker_checker['is_makerchecker_journey']=='yes'){
            $data['disposition'] = $this->db->select('*')->from('disposition_master')->where('display', 1)->where('maker_checker','Checker')->get()->result_array();
        }

        if($admin == 1){
            $data['disposition'] = $this->db->select('*')->from('disposition_master')->where(array('display'=>1,'Dispositions !='=>'Payment pending','maker_checker'=>'Checker'))->get()->result_array();
        }
        //	echo $this->db->last_query();
        //print_pre($data['disposition']);exit;

        if($check_maker_checker['is_makerchecker_journey'] == "yes"){


            $lead_creation  = $this->db->select('employee_details.makerchecker,employee_details.emp_id,employee_details.is_makerchecker_journey,employee_details.created_at,employee_details.emp_id,employee_details.axis_process,tls_base_agent_tbl.base_agent_name as agent_name')->from('employee_details,tls_base_agent_tbl')
                ->where('employee_details.assigned_to = tls_base_agent_tbl.base_id')
                ->where('emp_id',$emp_id)
                ->get()->row_array();
            //echo $this->db->last_query();
            $lead_creation_merge[0]['type'] = 'DO';


        }else{
            $lead_creation  = $this->db->select('employee_details.makerchecker,employee_details.emp_id,employee_details.is_makerchecker_journey,employee_details.created_at,employee_details.emp_id,employee_details.axis_process,tls_agent_mst.agent_name')->from('employee_details,tls_agent_mst')
                ->where('employee_details.assigned_to = tls_agent_mst.id')
                ->where('emp_id',$emp_id)
                ->get()->row_array();
            $lead_creation_merge[0]['type'] = 'AV';

        }

//		print_pre($lead_creation);exit;

        if($lead_creation['makerchecker']=='checker'&&$lead_creation['is_makerchecker_journey']=='yes'){
            $data['checker_edit']='yes';
            $data['checker_sendbackto_do']='yes';
            $data['checker_sendbackto_do_agent_name']=$_SESSION['telesales_session']['agent_name'];
            $data['checker_sendbackto_do_emp_id']=$lead_creation['emp_id'];

        }else{
            $data['checker_edit']='no';
            $data['checker_sendbackto_do']='no';
            $data['checker_sendbackto_do_agent_name']=$_SESSION['telesales_session']['agent_name'];
            $data['checker_sendbackto_do_emp_id']=$lead_creation['emp_id'];

        }



        $data['payment_summary'] = $this->db->select('ed.date as "date",ed.disposition_id,dm.Dispositions,dm.`Sub-dispositions`,ed.agent_name,ed.remarks,ed.type')
            ->from('employee_disposition ed')
            ->join('disposition_master dm', 'ed.disposition_id = dm.id')
            ->where('emp_id',$emp_id)
            //->group_by('ed.disposition_id')
            ->order_by('ed.id')
            ->get()
            ->result_array();
        $lead_creation_merge[0]['date'] = $lead_creation['created_at'];
        $lead_creation_merge[0]['disposition_id'] = 123;
        $lead_creation_merge[0]['Dispositions'] = 'LEAD';
        $lead_creation_merge[0]['Sub-dispositions'] = 'LEAD CREATION';
        $lead_creation_merge[0]['agent_name'] = $lead_creation['agent_name'];

        $data['axis_process']=$lead_creation['axis_process'];

        $data['payment_summary'] = array_merge($lead_creation_merge,$data['payment_summary']);
        //print_pre($data['payment_summary']);
        //exit;
        //print_pre($this->db->last_query());exit;
        //$data['disposition'] = 'dd';
        //print_Pre($data);
        $data['selected_disposition'] = $this->db->select('*')
            ->from('employee_disposition ed')
            ->join('disposition_master dm', 'ed.disposition_id = dm.id')
            ->where('emp_id',$emp_id)
            ->order_by('ed.id','desc')
            ->get()

            ->row_array();
        //echo $this->db->last_query();exit;
        //$data['selected_disposition']['Open/Close'] = 'Open';
        $data['lob'] = $this->db->select('*')
            ->from('tls_axis_lob l')
            ->where('axis_process',$data['center_process'])
            //  ->group_by('l.axis_lob')
            ->order_by('axis_lob','asc')
            ->get()
            ->result_array();
//	print_r($data);
        /*healthpro changes data of occupation master*/
        $data['occupation'] = $this->db->select('*')->get('master_occupation')->result_array();
//print_r($data);die;

        $this->load->view('template/header_tele.php');
		$this->load->view('teleproposal/index',$data);
		$this->load->view('template/footer_tele.php');
	}
    public function aprove_status()
    {
        $product_id = $this->input->post('product_id');
        $ismakerchecker= $this->input->post('ismakerchecker');
        $isagent_name= $this->input->post('updateaddagentname');
        $mode_of_payment= $this->input->post('mode_of_payment');
        if($ismakerchecker==1){

            $disposition=['emp_id'=>$this->emp_id,'disposition_id'=>'56','date'=>date('Y-m-d H:i:s'),'agent_name'=>$isagent_name];
            $this->db->insert('employee_disposition',$disposition);
        }

        //echo $product_id;exit;
        if($product_id == 'T01' || $product_id == 'T03'){

            $this->aprove_status_new();  return;
        }
        $emp_id = $this->emp_id;
        if($product_id == 'T01'){
            $parent_id = 'test123';
        }else{
            $parent_id = $this->parent_id;
        }

        $get_lead_id = $this->db->query("select lead_id,imd_code from employee_details where emp_id = '$emp_id' ")->row_array();

        if($get_lead_id['lead_id'] != 0)
        {
            $lead_id = $get_lead_id['lead_id'];
            if($product_id == 'T01'){
                $product_id == 'T01';
            }else if($product_id == 'T03'){
                $product_id == 'T03';
            }else{
                $product_id = 'R06';
            }

        }
        $branch_code = '';
        $IMDCode = $get_lead_id['imd_code'];
        //Add Data into logs Table
        $logs_array['data'] = ["type" => "post_proposal_create","req" => json_encode($_POST), "lead_id" => $lead_id,"product_id" => $product_id];
        $this->Logs_m->insertLogs($logs_array);
        //if combo policy addd member into check_validations_adult_count policy
        $this->db->trans_start();
        $policies = $this->db->select("ed.policy_detail_id,p.combo_flag,p.policy_parent_id")
            ->from("product_master_with_subtype AS p,employee_policy_detail AS ed")
            ->where("ed.product_name = p.id")->where("p.policy_parent_id", $parent_id)
            ->order_by("ed.policy_sub_type_id")->get()->result_array();

        //print_pre($policies);exit;

        foreach ($policies as $value)
        {
            $subQuery1 = $this->db
                ->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id,
					epd.policy_type_id, epd.policy_sub_type_id, epd.insurer_id, epd.broker_id,
					epm.policy_detail_id, epm.family_relation_id, epm.family_id, epd.policy_no,
					mps.policy_type_id, mpst.policy_sub_type_id, mpst.policy_sub_type_name, mpst.policy_sub_type_image_path,
					mf.family_relation_id, mf.family_id, mf.emp_id, ic.insurer_id, ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, "0" AS fr_id, "Self" AS fr_name')
                ->from('employee_details AS ed,
					family_relation AS mf,
					employee_policy_member AS epm,
					employee_policy_detail AS epd,
					master_policy_sub_type AS mpst,
					master_policy_type AS mps,
					master_insurance_companies AS ic')
                ->where('ed.emp_id = mf.emp_id')->where('mf.family_relation_id = epm.family_relation_id')
                ->where('epm.policy_detail_id = epd.policy_detail_id')->where('epd.policy_sub_type_id = mpst.policy_sub_type_id')
                ->where('mpst.policy_type_id = mps.policy_type_id')->where('epd.insurer_id = ic.insurer_id')
                ->where('epm.status != ', 'Inactive')->where('mf.family_id', 0)->where('mf.emp_id', $emp_id)
                ->where('epd.policy_detail_id', $value['policy_detail_id'])->get_compiled_select();

            $op = $this->db->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id, epd.policy_type_id, epd.policy_sub_type_id,
					epd.insurer_id, epd.broker_id, epm.policy_detail_id,
					epm.family_relation_id, epm.family_id, epd.policy_no,
					mps.policy_type_id, mpst.policy_sub_type_id,
					mpst.policy_sub_type_name, mpst.policy_sub_type_image_path, fr.family_relation_id,
					fr.family_id, fr.emp_id, ic.insurer_id,
					ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, mfr.
					fr_id, mfr.fr_name')
                ->from('employee_policy_member` AS `epm`, `employee_policy_detail` AS `epd`, `master_policy_type` AS `mps`, `master_policy_sub_type` AS `mpst`, `master_insurance_companies` AS `ic`, `family_relation` AS `fr`, `master_family_relation` AS `mfr`, `employee_family_details` AS `efd`')
                ->where('epd`.`policy_detail_id` = `epm`.`policy_detail_id')
                ->where('mps`.`policy_type_id` = `epd`.`policy_type_id')
                ->where('mpst`.`policy_sub_type_id` = `epd`.`policy_sub_type_id')
                ->where('ic`.`insurer_id` = `epd`.`insurer_id')
                ->where('fr`.`family_relation_id` = `epm`.`family_relation_id')
                ->where('fr`.`family_id` = `efd`.`family_id')->where('epm.status!=', 'Inactive')
                ->where('efd`.`fr_id` = `mfr`.`fr_id')->where('fr`.`emp_id`', $emp_id)
                ->where('epd.policy_detail_id', $value['policy_detail_id'])->get_compiled_select();

            $response = $this->db->query($subQuery1 . ' UNION ALL ' . $op)->result_array();
            //print_pre($this->db->last_query());continue;
            //Print_pre($response);continue;
            $policy_details_individual = $this->db->where("policy_detail_id", $value['policy_detail_id'])->get("employee_policy_detail")->row_array();

            if ($policy_details_individual['proposal_approval'] == 'Y')
            {
                $status = "Ready For Issuance";
            }
            else
            {

                $check_payment = $this->db->select("mpm.payment_mode_name,ppcm.id")->from("policy_payment_customer_mapping AS ppcm,master_payment_mode AS mpm")
                    ->where("ppcm.policy_id",$value['policy_detail_id'])
                    ->where("ppcm.mapping_id = mpm.id")
                    ->where("ppcm.type", "P")
                    ->group_by("ppcm.mapping_id")->get()->result_array();

                if($check_payment[0]['payment_mode_name'] != "Cheque" && $check_payment[0]['id'] != 4)
                {
                    $status = "Payment Pending";
                }
                else
                {
                    $status = "Ready For Issuance";
                }
            }
            $date = date('Y-m-d');
            $proposal_no = $this->db->select("number,date,id")->from("proposal_unique_number")->get()->row_array();

            if (strtotime($date) == strtotime($proposal_no['date']))
            {

                $number = ++$proposal_no['number'];
                $array = ["number" => $number];
                $this->db->where('id', $proposal_no['id']);
                $this->db->update('proposal_unique_number', $array);
                $propsal_number = "P-" . $number;
            }
            else
            {

                $number = date('Ymd') . '0000';
                $propsal_number = "P-" . $number;

                $array = ["number" => $number, "id" => 1, "date" => date('Y-m-d')];
                $this->db->where('id', '1');
                $this->db->delete('proposal_unique_number');
                $this->db->insert('proposal_unique_number', $array);
            }
            if($update_data == 'update')
            {
                $policy_details_ids = $value['policy_detail_id'];

                $get_proposal = $this->db->query("select id from proposal where  emp_id = '$emp_id' AND policy_detail_id = '$policy_details_ids'")->row_array();
                $prop_ids = $get_proposal['id'];

                $check_proposal_entry = $this->db->query("select count(*) as count from proposal_member where  proposal_id = '$prop_ids'")->row_array();

                if($check_proposal_entry['count'] > 0)
                {
                }
                else
                {
                    $check_proposal_entrys = $this->db->query("delete  from proposal where  id = '$prop_ids'");
                }
            }

            if(count($response)>0)
            {

                if($payment_mod == 'Pay U')
                {
                    $EasyPay_PayU_status = 1;
                }
                else
                {
                    $EasyPay_PayU_status = 0;
                }

                $proposal_array = [
                    "proposal_no" => $propsal_number,
                    "policy_detail_id" => $value['policy_detail_id'],
                    "product_id" => $value['policy_parent_id'],
                    "created_by" => $this->agent_id,
                    "status" => $status,
                    "branch_code" => $branch_code,
                    "IMDCode"=> $IMDCode,
                    "created_date" => date('Y-m-d H:i:s'),
                    "EasyPay_PayU_status"=>$EasyPay_PayU_status,
                    "emp_id" => $emp_id,
                    "modified_date" => date('Y-m-d H:i:s')
                ];
                $proposal_create = $this->db->query("select id from proposal where emp_id = '$emp_id' AND policy_detail_id = '".$value['policy_detail_id']."'")->row_array();
                //sahil
                //$proposal_create = $this->db->query("select id from proposal where emp_id = '$emp_id'")->row_array();
                //echo $this->db->last_query();exit;
                if($this->db->affected_rows() > 0)
                {
                    $proposal_id = $proposal_create['id'];
                    $this->db->where('emp_id',$emp_id);
                    $this->db->update("proposal", $proposal_array);
                    $proposal_array1234[] = $proposal_id;

                    //Add Data into logs Table
                    $logs_array['data'] = ["type" => "update_proposal","req" => json_encode($proposal_array), "lead_id" => $lead_id,"product_id" => $product_id];
                    $this->Logs_m->insertLogs($logs_array);

                }
                else
                {
                    $this->db->insert("proposal", $proposal_array);
                    $proposal_id = $this->db->insert_id();
                    //payment_details
                    $pay_data = array(
                        'proposal_id' => $proposal_id,
                        'txndate' => date('Y-m-d H:i:s'),
                        'payment_status' => 'Payment Pending',
                        'payment_mode'=> $mode_of_payment,
                        'emp_id'=>$emp_id

                    );

                    $this->db->insert("payment_details", $pay_data);

                    $proposal_array1234[] = $proposal_id;

                    //Add Data into logs Table
                    $logs_array['data'] = ["type" => "insert_proposal","req" => json_encode($proposal_array), "lead_id" => $lead_id,"product_id" => $product_id];
                    $this->Logs_m->insertLogs($logs_array);
                }



                $sum = 0;
                foreach ($response as $value)
                {
                    $update_array = [
                        "member_status" => "confirmed"
                    ];

                    $member = $this->db->where("policy_member_id", $value["policy_member_id"])->get("employee_policy_member")->row_array();

                    $policy_details_ids = $value['policy_detail_id'];
                    $policy_member_id = $value['policy_member_id'];

                    $get_proposal_id = $this->db->query("select proposal_id from proposal_member where  policy_member_id = '$policy_member_id'")->row_array();

                    if(count($get_proposal_id)>0)
                    {
                        $proposal_id = $get_proposal_id['proposal_id'];
                        $this->db->where('policy_member_id', $value['policy_member_id']);
                        $this->db->update('proposal_member', $member);

                        //Add Data into logs Table
                        $logs_array['data'] = ["type" => "update_proposal_member","req" => json_encode($member), "lead_id" => $lead_id,"product_id" => $product_id];
                        $this->Logs_m->insertLogs("logs_post_data",$logs_array);
                    }
                    else
                    {
                        $this->db->where('policy_member_id', $value['policy_member_id']);
                        $this->db->update('employee_policy_member', $update_array);

                        //Add Data into logs Table
                        $logs_array['data'] = ["type" => "update_proposal_policy_member","req" => json_encode($update_array), "lead_id" =>$lead_id,"product_id" => $product_id];
                        $this->Logs_m->insertLogs($logs_array);

                        //get member details row
                        $member = $this->db->where("policy_member_id", $value["policy_member_id"])->get("employee_policy_member")->row_array();
                        $member['proposal_id'] = $proposal_id;

                        $this->db->insert("proposal_member", $member);

                        //Add Data into logs Table
                        $logs_array['data'] = ["type" => "insert_proposal_member","req" => json_encode($member), "lead_id" => $lead_id,"product_id" => $product_id];
                        $this->Logs_m->insertLogs($logs_array);
                    }

                    $prodArr = ['T01','T03'];
                    //echo "hi";exit;
                    //if ($product_id == 'R07')
                    if(in_array($product_id, $prodArr))
                    {
                        //print_R($value['policy_sub_type_id']);
                        if ($value['policy_sub_type_id'] != 1)
                        {

                            $sum += $value['policy_mem_sum_premium'];
                        }
                        else
                        {
                            $sum = $value['policy_mem_sum_premium'];
                        }

                    }else
                    {
                        $sum = $value['policy_mem_sum_premium'];
                    }


                    $this->db->where('id', $proposal_id);
                    $this->db->update('proposal', [
                        "sum_insured" => $value['policy_mem_sum_insured'],
                        "premium" => $sum
                    ]);

                    //Add Data into logs Table
                    $logs_array['data'] = ["type" => "update_proposal_suminsured_premium","req" => json_encode($value['policy_mem_sum_insured'].",".$value['policy_mem_sum_premium']), "lead_id" => $lead_id,"product_id" => $product_id];
                    $this->Logs_m->insertLogs($logs_array);

                }
            }
        }
        $this->db->trans_commit();
        $array = ["status" => true, "proposal_ids" => $proposal_array1234];
        print_r(json_encode($array));
    }

    function summary_url()
    {//echo 123;die;
        $aTLSession = $this->session->userdata('telesales_session');
        $agent_id_decrypt = encrypt_decrypt_password($aTLSession['agent_id'],'D');

        $product_id = $this->input->post('product_id');
        if(empty($product_id)){
            $product_id = 'R06';
        }
        /*if($product_id == 'R11'){
            $product_id = 'R11';
        }else{
        $product_id = 'R06';
        }*/
        if($aTLSession && $aTLSession['emp_id']){
            $emp_id = $aTLSession['emp_id'];
        }else{
            $emp_id_encrypt = $this->input->post('emp_id');
            $emp_id = encrypt_decrypt_password($emp_id_encrypt,"D");
        }

        // $emp_id = '620520';
        // print_r($_SESSION);

        if($emp_id){

            $query_check = $this->db->query("select ed.product_id,ed.emp_id,ed.lead_id,p.created_date,ed.email,ed.mob_no,ed.emp_firstname,p.id from employee_details as ed,proposal as p where ed.emp_id = p.emp_id and p.status = 'Payment Pending' and ed.emp_id = '$emp_id' group by p.emp_id")->row_array();

            if($query_check){
                //print_R($query_check);die;
                $request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($emp_id),"res" => json_encode($query_check) ,"product_id"=> $query_check['product_id'], "type"=>"bitly_url_check"];

                $dataArray['tablename'] = 'logs_post_data';
                $dataArray['data'] = $request_arr;
                $this->Logs_m->insertLogs($dataArray);

                $res_arr = ["modified_date" => date('Y-m-d H:i:s')];
                $this->db->where("emp_id",$query_check['emp_id']);
                $this->db->update("proposal",$res_arr);

                $this->db->query("DELETE FROM ghi_quick_quote_response WHERE emp_id = '".$emp_id."'");

                $click_url =$this->db->query("select click_pss_url from product_master_with_subtype where product_code = 'R06'")->row_array();

                $emp_id_encrypt = encrypt_decrypt_password($query_check['emp_id']);

                // if(!empty($this->input->post('emp_id'))){
                // $url = base_url("tls_payment_redirect_view/".$emp_id_encrypt);
                // $name_data = "payu";
                // }else{
                $url = base_url("tele_proposal_summary/redirect_summary/".$emp_id_encrypt."/".encrypt_decrypt_password(date('Y-m-d H:i:s'))."/".$product_id);
              // print_r($url);die;
                $name_data = "summary";
                //}
            $result=   $this->get_tiny_url($url);
print_r($result);die;

                $request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($url_req),"res" => json_encode($result) ,"product_id"=> $query_check['product_id'], "type"=>"bitly_url_".$name_data];

                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this->Logs_m->insertLogs($dataArray);


                $query = $this
                    ->db
                    ->query("select * from user_payu_activity where emp_id='".$query_check['emp_id']."'")->row_array();

                if(empty($query)){
                    $this->db->insert("user_payu_activity",["emp_id" => $query_check['emp_id']]);
                }

                $res_data = json_decode($response,true);

                /* comment this on prod envirment start */
                $res_arr = ["sms_trigger_status" => '1'];
                $this->db->where("emp_id",$query_check['emp_id']);
                $this->db->update("proposal",$res_arr);
                /* end */

                //if($res_data && $res_data['STATUS']=='0'){
                //link trigger check update
                $res_arr = ["sms_trigger_status" => '1'];

                $this->db->where("emp_id",$query_check['emp_id']);
                $this->db->update("proposal",$res_arr);

                //employee disposition status add
                //$agent_data = $this->db->query("select a.agent_name from employee_details ed,tls_agent_mst a where ed.assigned_to = a.id and ed.emp_id='".$query_check['emp_id']."'")->row_array();

                $agent_name = $this->db->select('agent_name')->from('tls_agent_mst')->where('id',$agent_id_decrypt)->get()->row_array()['agent_name'];

                $this->db->insert("employee_disposition",["emp_id" => $query_check['emp_id'],"disposition_id" => 45,"agent_name" => $agent_name,"date" => date('Y-m-d H:i:s'),"remarks"=>"Payment link trigger"]);
                // $this->db->insert("employee_disposition",["emp_id" => $query_check['emp_id'],"disposition_id" => 45,"agent_name" => $agent_data['agent_name'],"date" => date('Y-m-d H:i:s'),"remarks"=>"Payment link trigger"]);

                //}

                $request_arr = ["lead_id" => $query_check['lead_id'], "req" => json_encode($parameters),"res" => json_encode($response) ,"product_id"=> $query_check['product_id'], "type"=>"sms_logs_redirect_".$name_data];

                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this->Logs_m->insertLogs($dataArray);
            }

        }

    }
    function get_tiny_url($url)
    {
        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    public function get_agent_details()
    {

        echo json_encode($this->obj_home->get_agent_details($this->agent_id));
    }
    public function master_axis_location()
    {

        $query = $this->db->query("select axis_loc_id,axis_location from tls_axis_location");

        echo json_encode( $query->result_array());
    }

    public function addAgent()
    {


            $emp_id = $this->emp_id;
            $get_lead_id = $this->db->query("select lead_id from employee_details where emp_id = '$emp_id' ")->row_array();
            if($get_lead_id['lead_id'] != 0)
            {
                $lead_id = $get_lead_id['lead_id'];
                $product_id = 'R06';
            }

            $axis_location =  $this->input->post('axis_location', true);
            $axis_vendor =  $this->input->post('axis_vendor', true);
            $axis_lob =  $this->input->post('axis_lob', true);
            $av_code =  $this->input->post('avCode', true);
            $agent_id =  $this->input->post('agent_id', true);
            $agent_name =  $this->input->post('agent_name', true);
            $imd_code =  $this->input->post('imd_code', true);
            $emp_agent_data = array(

                'axis_location' => $axis_location,
                'axis_vendor' => $axis_vendor,
                'axis_lob' => $axis_lob,
                'av_code' => $this->agent_id,
                'agent_id' => $agent_id,
                'agent_name' => $agent_name,
                'imd_code'=>$imd_code


            );
           // print_r($emp_agent_data);die;
            $agent_data = $this->obj_home->add_agent_emp_details($emp_id,$emp_agent_data);

            if($agent_data == true)
            {
                //Add Data into logs Table
                $logs_array['data'] = ["type" => "insert_agent_details","req" => json_encode($emp_agent_data), "lead_id" => $lead_id,"product_id" => $product_id];
                $this->Logs_m->insertLogs($logs_array);
            }
            $insert_data= ["status" => true, "message" => 'Agent Successfully Updated'];
            print_r(json_encode($insert_data));

    }
    public function employee_data()
    {
        $emp_id = $this->emp_id;
        $result =  $this->db->select('emg_cno,comm_address1,pid,deductable,saksham_id,emp_middlename,ISNRI,lead_id,emp_id,emp_code,emp_firstname,emp_lastname,gender,bdate,mob_no,email,emp_address,emp_city,emp_state,emp_pincode,street,location,doj,pancard,adhar,address,comm_address,ref1,ref2,salutation,emp_city,emp_state,emp_pincode,ifsc_code,occupation,annual_income')->where(["emp_id" => $emp_id])->get("employee_details")->row();
        print_r(json_encode($result));
    }
    public function addCustomer()
    {


            $emp_id = $this->emp_id;
            $get_lead_id = $this->db->query("select lead_id from employee_details where emp_id = '$emp_id' ")->row_array();
            if($get_lead_id['lead_id'] != 0)
            {
                $lead_id = $get_lead_id['lead_id'];
                $product_id = 'R06';
            }

            $address =  $this->input->post('comAdd', true);
            $address2 =  $this->input->post('comAdd2', true);
            $address3 =  $this->input->post('comAdd3', true);
            $pinCode =  $this->input->post('pin_code', true);
            $city =  $this->input->post('city', true);
            $state =  $this->input->post('state', true);
            $mobile_no2 =  $this->input->post('mobile_no2', true);
            $email =  $this->input->post('email', true);
            $firstname =  $this->input->post('firstname', true);
            $lastname =  $this->input->post('lastname', true);
            $gender1  = $this->input->post('gender1', true);
            $salutation  = $this->input->post('salutation', true);
            $dob  = $this->input->post('dob', true);
            if($salutation == 1){
                $salutation = 'Mr';
            }else if($salutation == 2){
                $salutation = 'Mrs';
            }else if($salutation == 3){
                $salutation = 'Ms';
            }
            $emp_data_insert = array(
                'bdate' => $dob,
                'salutation' => $salutation,
                'emp_firstname' => $firstname,
                'emp_lastname' => $lastname,
                'gender' => $gender1,
                'address'=>$address,
                'comm_address'=>$address2,
                'comm_address1'=>$address3,
                'emp_pincode'=>$pinCode,
                'emp_city'=>$city,
                'emp_state'=>$state,
                'emg_cno'=> $mobile_no2,
                'email'=> $email
            );
            $emp_data = $this->obj_home->add_agent_emp_details($emp_id,$emp_data_insert);
            //echo $emp_data;exit;
            if($emp_data == true)
            {
                //Add Data into logs Table
                $logs_array['data'] = ["type" => "insert_agent_details","req" => json_encode($emp_data_insert), "lead_id" => $lead_id,"product_id" => $product_id];
                $this->Logs_m->insertLogs($logs_array);
            }
            $update = 0;
            //if(isset($_POST['self_edit_clicked']) && $_POST['self_edit_clicked'] != 0){
            //check if self data is inserted in policy member
            $qry = "SELECT epm.policy_member_id,epm.fr_id FROM family_relation AS fr, employee_policy_member AS epm WHERE fr.family_relation_id = epm.family_relation_id AND epm.fr_id = 0 AND fr.emp_id = ".$emp_id;
            $res = $this->db->query($qry)->result_array();
//echo $this->db->last_query();exit;
            if(!empty($res)){
                foreach ($res as $key => $value) {
                    if($value['policy_member_id'] != ''){
                        //update self details
                        $bday = new DateTime($dob); // Your date of birth
                        $today = new Datetime(date('d-m-Y'));
                        $diff = $today->diff($bday);
                        $updateArr = array(//"policy_mem_salutation" => $salutation,
                            //"policy_mem_gender" => $gender1,
                            //"policy_mem_dob"=>$dob,
                            "policy_member_first_name" => $firstname,
                            "policy_member_last_name" => $lastname,
                            //"age" => $diff->y
                        );
                        $this->db->where('policy_member_id', $value['policy_member_id']);
                        $this->db->update('employee_policy_member', $updateArr);
                        //if(isset($_POST['self_edit_clicked']) && $_POST['self_edit_clicked'] != 0){
                        $update =1;
                        //}
                        //$this->db->delete('employee_policy_member', array('policy_member_id' => $value['policy_member_id']));
                    }
                }

            }
            //update data in proposal member table
            $qry1 = "SELECT epm.proposal_member_id,epm.fr_id FROM family_relation AS fr, proposal_member AS epm WHERE fr.family_relation_id = epm.family_relation_id AND epm.fr_id = 0 AND fr.emp_id =".$emp_id;
            $res1 = $this->db->query($qry1)->result_array();
            if(!empty($res1)){
                foreach ($res1 as $key => $value) {
                    if($value['proposal_member_id'] != ''){
                        //update self
                        $updateArr = array(
                            "policy_member_first_name" => $firstname,
                            "policy_member_last_name" => $lastname,
                        );
                        $this->db->where('proposal_member_id', $value['proposal_member_id']);
                        $this->db->update('proposal_member', $updateArr);
                        //if(isset($_POST['self_edit_clicked']) && $_POST['self_edit_clicked'] != 0){
                        $update =1;
                        //}
                        //$this->db->delete('employee_policy_member', array('policy_member_id' => $value['policy_member_id']));
                    }
                }

            }
            //}
            $proposal_status = $this->check_proposal_created();
           //   print_r($proposal_status);exit;
            $insert_data= ["status" => true, "message" => 'Customer Details Added Successfully', "update" => $update, "proposal_status" => $proposal_status['proposal_status']];
            print_r(json_encode($insert_data));


    }
    public function get_city_state(){
        extract($this->input->post(null, true));
        print_r(json_encode($this->obj_home->axis_state_city($pincode)));
    }
    public function check_proposal()
    {
        $emp_id = $this->emp_id;
        $data = $this->obj_home->get_common_data();
        $check_proposal = $this->db->query("select emp_id from proposal where emp_id = '$emp_id' and status !='Payment Pending'")->row_array();
        $data['audit_data'] = $this->db->select('*')
            ->from('employee_disposition ed')
            ->join('disposition_master dm', 'ed.disposition_id = dm.id')
            ->where('dm.Dispositions','Payment pending')
            ->where('dm.Sub-dispositions','PG link triggered')
            ->order_by('ed.id','desc')
            ->get()
            ->row_array();
        //echo $this->db->last_query();
        $check_proposal_new = $this->db->query("select emp_id from proposal where emp_id = '$emp_id'")->row_array();
        if(count($check_proposal_new)>0)
        {
            $data['proposal_status'] = 'Yes';
        }
        else
        {$data['proposal_status'] = 'No';}

        if(count($check_proposal)>0)
        {
            $data['status'] = 'Yes';
        }
        else
        {$data['status'] = 'No';}
        echo json_encode($data);
    }
    public function master_nominee()
    {
        $query = $this->db->query("select nominee_id,nominee_type,gender from master_nominee");

        echo json_encode( $query->result_array());
    }
    public function check_proposal_created()
    {
        $emp_id = $this->emp_id;
        $data = $this->obj_home->get_common_data();
        $check_proposal = $this->db->query("select emp_id from proposal where emp_id = '$emp_id' and status !='Payment Pending'")->row_array();
        $data['audit_data'] = $this->db->select('*')
            ->from('employee_disposition ed')
            ->join('disposition_master dm', 'ed.disposition_id = dm.id')
            ->where('dm.Dispositions','Payment pending')
            ->where('dm.Sub-dispositions','PG link triggered')
            ->where('ed.emp_id',$emp_id)

            ->order_by('ed.id','desc')
            ->get()
            ->row_array();
        //echo $this->db->last_query();
        $check_proposal_new = $this->db->query("select emp_id from proposal where emp_id = '$emp_id'")->row_array();
        if(count($check_proposal_new)>0)
        {
            $data['proposal_status'] = 'Yes';
        }
        else
        {$data['proposal_status'] = 'No';}

        if(count($check_proposal)>0)
        {
            $data['status'] = 'Yes';
        }
        else
        {$data['status'] = 'No';}
        return $data;
    }
    public function get_all_policy_data_new()
    {


        $parent_id = $this->parent_id;

        print_r(json_encode($this->obj_home->get_all_policy_data_new($parent_id)));
    }
    public function get_all_policy_data()
    {


        $parent_id = $this->parent_id;

        print_r(json_encode($this->obj_home->get_all_policy_data($parent_id)));
    }
    public function get_deductable_amount()
    {
        $parent_id = $this->parent_id;
        /*healthpro changes passed product_id to below function*/
        //echo json_encode($this->obj_home->get_suminsured_data($parent_id));

        $product_id = $_POST['product_id'];
        $data = $this->obj_home->get_deductable_amount();
        $res = ["status" => "success","data"=> $data ];
        echo json_encode($res);exit;
    }
    public function get_suminsured_data()
    {

        $parent_id = $this->parent_id;
        /*healthpro changes passed product_id to below function*/
        //echo json_encode($this->obj_home->get_suminsured_data($parent_id));

        $product_id = $_POST['product_id'];
        $family_construct = $_POST['family_construct'];
        echo json_encode($this->obj_home->get_suminsured_data($parent_id,$product_id,$family_construct));
    }
    function apply_changes()
    {

        extract($this
            ->input
            ->post());
        $gci_sent = $gci;
        $gci = ($gci == 'Yes') ? true : false;

        $emp_id = $this->emp_id;
        $ghi_policy_detail_id = $this
            ->db
            ->select('policy_detail_id')
            ->from('employee_policy_detail')
            ->where('parent_policy_id', $this->parent_id)->where('policy_sub_type_id', 1)
            ->get()
            ->row_array() ['policy_detail_id'];
        $child_count = $this
            ->obj_home
            ->get_child_count($emp_id, $ghi_policy_detail_id) ['count'];
        $adult_count = $this
            ->obj_home
            ->get_adult_count($emp_id, $ghi_policy_detail_id) ['count'];

        $family_construct_selected = $family_construct;
        $family_construct = explode('+', $family_construct);
        $selected_child_count = (!empty($family_construct[1])) ? $family_construct[1][0] : 0;
        $selected_adult_count = (!empty($family_construct[0])) ? $family_construct[0][0] : 0;

        if (!empty($adult_count))
        {

            $compare_adult = ($adult_count == $selected_adult_count) ? false : true;
            $compare_child = ($child_count == $selected_child_count) ? false : true;

            if ($compare_adult || $compare_child)
            {

                $message = 'Please Add Or Delete Member To Buy Policy';

            }
            else
            {
                $message = 'Changes Applied';
            }
            $only_gci_insert = false;
            if (!empty($only_gci))
            {

                $only_gci_insert = true;

                $this
                    ->db
                    ->where('emp_id', $this->emp_id);
                $this
                    ->db
                    ->update('employee_details', ['GCI_optional' => $gci_sent]);

                $message = ($gci) ? 'You have successfully opted for gci policy' : 'You have successfully opted out from gci policy';

            }
            //echo "here";exit;
            $data = $this
                ->obj_home
                ->get_all_member_data_new_gpa($product_id,'update', $ghi_policy_detail_id, $sum_insured, $family_construct_selected, true, $gci, '', $this->emp_id, $only_gci_insert);
            $return_array = ['message' => $message, 'data' => $data];
            echo json_encode($return_array);

        }
        else
        {

            $message = 'Please Add member';
            $message = '';
            $return_array = ['message' => $message, 'data' => ''];
            echo json_encode($return_array);
        }
    }

    public function get_premium()
    {
        /*ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/
        /*healthpro changes created new function*/
        echo json_encode($this->obj_home->get_premium_new());
        //echo json_encode($this->obj_home->get_premium());
    }
    public function get_family_construct()
    {

        //echo 123;die;
        //dedupe logic
     //   $res = $this->check_dedupe_logic();
//$res['status'] =true;
       /* if($res['status'] == 'error'){
            echo json_encode($res);exit;
        }else{*/
//echo 23;die;
            $data = $this->obj_home->get_family_construct();

            $res = ["status" => "success","data"=> $data ];
            echo json_encode($res);exit;
       // }
    }
    public function proposal_validation()
    {

      //  $this->form_validation->set_error_delimiters('','');
        //$this->form_validation->set_rules('mode_payment', 'Mode Payment', 'trim');
        //$this->form_validation->set_rules('auto_renewal', 'Auto Renewal', 'trim|required');
        //$this->form_validation->set_rules('av_remark', 'AV Remark', 'trim|required');
        // $this->form_validation->set_rules('nominee_relation', 'Nominee Relation', 'required|trim');
        // $this->form_validation->set_rules('nominee_fname', 'Nominee First Name', 'required|trim');
        // $this->form_validation->set_rules('nominee_lname', 'Nominee Last Name', 'required|trim');
        // $this->form_validation->set_rules('nominee_gender', 'Nominee Gender', 'required|trim');
        // $this->form_validation->set_rules('nominee_dob', 'Nominee DOB', 'required|trim');
        // $this->form_validation->set_rules('nominee_salutation', 'Nominee Salutation', 'required|trim');
        //$this->form_validation->set_rules('nominee_contact', 'Nominee Contact', 'required|trim');
        // $this->form_validation->set_rules('axis_location', 'Axis Location', 'required|trim');
        // $this->form_validation->set_rules('axis_vendor', 'Axis Vendor', 'required|trim');
        // $this->form_validation->set_rules('axis_lob', 'Axis Lob', 'required|trim');
        // $this->form_validation->set_rules('agent_id', 'Agent Code', 'required|trim');
        // $this->form_validation->set_rules('comAdd', 'Communication Address', 'required|trim');
        // $this->form_validation->set_rules('pin_code', 'Pin Code', 'required|trim');
        // $this->form_validation->set_rules('city', 'City', 'required|trim');
        // $this->form_validation->set_rules('state', 'State', 'required|trim');
        // $this->form_validation->set_rules('mobile_no2', 'mobile_no2', 'trim');

        //$this->form_validation->set_rules('preferred_contact_date', 'Preferred Contact Date', 'trim|required');
        //$this->form_validation->set_rules('preferred_contact_time', 'Preferred Contact Time', 'trim|required');

        //if ($this->form_validation->run() == FALSE)
        $product_id = $this->input->post('product_id');
        if (FALSE)
        {

           // $z = validation_errors();
           // $validation_err= ["status" => false, "message" => $z];
           // print_r(json_encode($validation_err));
        }
        else
        {

            $emp_id = $this->emp_id;
            if($product_id == 'T01'){
                $parent_id = 'test123';
            }else{
                $parent_id = $this->parent_id;
            }

            $proposal_data_all = $this->input->post(null,true);
            if(($proposal_data_all != null) || !empty($proposal_data_all))
                // $nominee_relation =  $this->input->post('nominee_relation', true);
                // $nominee_fname =  $this->input->post('nominee_fname', true);
                // $nominee_lname =  $this->input->post('nominee_lname', true);
                // $nominee_gender =  $this->input->post('nominee_gender', true);
                // $nominee_dob =  $this->input->post('nominee_dob', true);
                // $nominee_contact =  $this->input->post('nominee_contact', true);
                // $nominee_email =  $this->input->post('nominee_email', true);
                // $axis_location =  $this->input->post('axis_location', true);
                // $axis_vendor =  $this->input->post('axis_vendor', true);
                // $axis_lob =  $this->input->post('axis_lob', true);
                // $av_code =  $this->input->post('avCode', true);
                // $agent_id =  $this->input->post('agent_id', true);
                // $agent_name =  $this->input->post('agent_name', true);
                // $address =  $this->input->post('comAdd', true);
                // $address2 =  $this->input->post('comAdd2', true);
                // $address3 =  $this->input->post('comAdd3', true);
                // $pinCode =  $this->input->post('pin_code', true);
                // $city =  $this->input->post('city', true);
                // $state =  $this->input->post('state', true);
                // $mobile_no2 =  $this->input->post('mobile_no2', true);

                $declares =  $this->input->post('declares', true);
            $remarks_new =  $this->input->post('remarks_new', true);
            $myGHD = $this->input->post('myGHD',true);
            //echo $product_id;exit;
            $nominee_salutation = $this->input->post('nominee_salutation', true);
            $get_lead_id = $this->db->query("select GCI_optional,lead_id,imd_code,pid from employee_details where emp_id = '$emp_id' ")->row_array();
            $this->db->where(['emp_id' => $emp_id]);
            $this->db->update("employee_details", ['new_remarks' => $remarks_new ]);
            //echo $this->db->last_query();exit;
            if($get_lead_id['lead_id'] != 0)
            {
                $lead_id = $get_lead_id['lead_id'];
                if($product_id == 'T01'){
                    $product_id = 'T01';
                }else if($product_id == 'T03'){
                    $product_id = 'T03';
                }else{
                    $product_id = 'R06';
                }

            }

            //Add Data into logs Table
            $logs_array['data'] = ["type" => "post_all_data","req" => json_encode($_POST), "lead_id" => $lead_id,"product_id" => $product_id];
            $this->Logs_m->insertLogs($logs_array);

            $emp_agent_data = array(
                'payment_mode'=> $mode_payment,
                'auto_renewal'=> $auto_renewal,
                'preferred_contact_date'=> $preferred_contact_date,
                'preferred_contact_time'=> $preferred_contact_time,
                'av_remark'=> $av_remark

                // 'axis_location' => $axis_location,
                // 'axis_vendor' => $axis_vendor,
                // 'axis_lob' => $axis_lob,
                // 'av_code' => $av_code,
                // 'agent_id' => $agent_id,
                // 'agent_name' => $agent_name,
                // 'address'=>$address,
                // 'comm_address'=>$address2,
                // 'comm_address1'=>$address3,
                // 'emp_pincode'=>$pinCode,
                // 'emp_city'=>$city,
                // 'emp_state'=>$state,
                // 'emg_cno'=> $mobile_no2,
                // 'payment_mode'=> $mode_payment,
                // 'auto_renewal'=> $auto_renewal,
                // 'preferred_contact_date'=> $preferred_contact_date,
                // 'preferred_contact_time'=> $preferred_contact_time,
                // 'av_remark'=> $av_remark,

            );

            //$agent_data = $this->obj_home->add_agent_emp_details($emp_id,$emp_agent_data);

            // if($agent_data == true)
            // {
            //Add Data into logs Table
            // $logs_array['data'] = ["type" => "insert_payement_details","req" => json_encode($emp_agent_data), "lead_id" => $lead_id,"product_id" => $product_id];
            // $this->Logs_m->insertLogs($logs_array);
            // }

            $declare = json_decode($declares);

            $ghd_check = $this->obj_home->ghd_declined_insert($myGHD,$emp_id,'insert',$lead_id,$product_id);

            if($ghd_check['status'] == 'false')
            {
                $array_data = ["status" => false, "message" => $ghd_check['message'] ];
                print_r(json_encode($array_data));
                return;
            }
            //product mapping policy number
            //get all members in the above master policy and update their status
            //emp id of whom the proposal is created

            $employee_details_new_api = $this->db->where("emp_id",$emp_id)->get('employee_details')->row_array();
            $response_api = json_decode($employee_details_new_api['json_qote'],true);

            //$branch_code = $response_api['BRANCH_SOL_ID'];
            //$IMDCode = $this->db->where('BranchCode',$branch_code)->get('master_imd')->row_array()['IMDCode'];
            $branch_code = '';
            $IMDCode = $get_lead_id['imd_code'];
            $proposal_array1234 = [];




            //if combo policy addd member into check_validations_adult_count policy


            //$family_construct1 = explode("+", $family_construct);
            if($product_id == 'T03'){
                $pid = $get_lead_id['pid'];
                $policies = $this->db->select("ed.policy_detail_id,p.combo_flag,p.policy_parent_id")
                    ->from("product_master_with_subtype AS p,employee_policy_detail AS ed")
                    ->where("ed.product_name = p.id")->where_in("ed.policy_detail_id", $pid)
                    ->order_by("ed.policy_sub_type_id")->get()->result_array();
            }else{
                $policies = $this->db->select("ed.policy_detail_id,p.combo_flag,p.policy_parent_id")
                    ->from("product_master_with_subtype AS p,employee_policy_detail AS ed")
                    ->where("ed.product_name = p.id")->where("p.policy_parent_id", $parent_id)
                    ->order_by("ed.policy_sub_type_id")->get()->result_array();
            }
            //print_r($policies);exit;

            //condition for healthproinfinity

            //echo $this->db->last_query();exit;
            //echo $product_id;exit;
            $gmc_id = "";
            $q = 0;
            //r07 code
            if($product_id == 'T01'){

                foreach ($policies as $value)
                {
                    $decare_proposal_ghd = $this->db->query("delete  from employee_declare_proposal_data where  emp_id = '$emp_id'");

                    if(!empty($declare_prop))
                    {
                        foreach ($declare_prop as $declare_m_prop)
                        {
                            $data1_prop = array(
                                "emp_id" => $emp_id,
                                //"proposal_number" => '',
                                "product_id" => $value['policy_parent_id'],
                                "remark" => $declare_m_prop['label'],
                                "proposal_declare_id" => $declare_m_prop['question_prop'],
                                "format" => $declare_m_prop['format_prop'],
                                "created_by" => $this->agent_id
                            );

                            if ($update_data == 'update')
                            {
                                $updata_prop = array(
                                    "format" => $declare_m_prop['format_prop']
                                );
                                $this->db->where(['emp_id' => $emp_id, 'emp_proposal_declare_id' => $declare_m_prop['question_prop']]);
                                $this->db->update("employee_declare_proposal_data", $updata_prop);

                                //Add Data into logs Table
                                $logs_array['data'] = ["type" => "update_proposal_ghd_declare","req" => json_encode($data1_prop), "lead_id" => $lead_id,"product_id" => $product_id];
                                $this->Logs_m->insertLogs($logs_array);
                            }
                            else
                            {
                                $this->db->insert('employee_declare_proposal_data', $data1_prop);

                                //Add Data into logs Table
                                $logs_array['data'] = ["type" => "insert_proposal_ghd_declare","req" => json_encode($data1_prop), "lead_id" => $lead_id,"product_id" => $product_id];
                                $this->Logs_m->insertLogs($logs_array);
                            }
                        }
                    }
                    $decare_member_proposal_ghd = $this->db->query("delete  from employee_declare_data where  emp_id = '$emp_id'");
                    if(!empty($declare))
                    {

                        foreach ($declare as $declare_m)
                        {

                            $data1 = array(
                                "emp_id" => $emp_id,
                                //"proposal_number" => '',
                                "product_id" => $value['policy_parent_id'],
                                "remark" => $declare_m->label,
                                "p_declare_id" => $declare_m->question,
                                "format" => $declare_m->format,
                                "created_by" => $this->agent_id
                            );
                            if ($update_data == 'update')
                            {

                                $updata = array(
                                    "format" => $declare_m['format']
                                );

                                $this->db->where(['emp_id' => $emp_id, 'p_declare_id' => $declare_m['question']]);
                                $this->db->update("employee_declare_data", $updata);

                                //Add Data into logs Table
                                $logs_array['data'] = ["type" => "update_proposal_employee_health","req" => json_encode($updata), "lead_id" => $lead_id,"product_id" => $product_id];
                                $this->Logs_m->insertLogs("logs_post_data",$logs_array);
                            }
                            else
                            {

                                $this->db->insert('employee_declare_data', $data1);

                                //Add Data into logs Table
                                $logs_array['data'] = ["type" => "insert_proposal_employee_health","req" => json_encode($data1), "lead_id" => $lead_id,"product_id" => $product_id];
                                $this->Logs_m->insertLogs($logs_array);
                            }
                        }
                    }
                    //create individual proposals
                    //get workflow from policy detail table
                    //ends
                    //get all members in particular policy
                    $subQuery1 = $this
                        ->db
                        ->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id,
            						epd.policy_type_id, epd.policy_sub_type_id, epd.insurer_id, epd.broker_id,
            						epm.policy_detail_id, epm.family_relation_id, epm.family_id, epd.policy_no,
            						mps.policy_type_id, mpst.policy_sub_type_id, mpst.policy_sub_type_name, mpst.policy_sub_type_image_path,
            						mf.family_relation_id, mf.family_id, mf.emp_id, ic.insurer_id, ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, "0" AS fr_id, "Self" AS fr_name')
                        ->from('employee_details AS ed,
            						family_relation AS mf,
            						employee_policy_member AS epm,
            						employee_policy_detail AS epd,
            						master_policy_sub_type AS mpst,
            						master_policy_type AS mps,
            						master_insurance_companies AS ic')
                        ->where('ed.emp_id = mf.emp_id')
                        ->where('mf.family_relation_id = epm.family_relation_id')
                        ->where('epm.policy_detail_id = epd.policy_detail_id')
                        ->where('epd.policy_sub_type_id = mpst.policy_sub_type_id')
                        ->where('mpst.policy_type_id = mps.policy_type_id')
                        ->where('epd.insurer_id = ic.insurer_id')
                        ->where('epm.status != ', 'Inactive')
                        ->where('mf.family_id', 0)
                        ->where('mf.emp_id', $emp_id)->where('epd.policy_detail_id', $value['policy_detail_id'])->get_compiled_select();
                    $op = $this
                        ->db
                        ->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id, epd.policy_type_id, epd.policy_sub_type_id,
            						epd.insurer_id, epd.broker_id, epm.policy_detail_id,
            						epm.family_relation_id, epm.family_id, epd.policy_no,
            						mps.policy_type_id, mpst.policy_sub_type_id,
            						mpst.policy_sub_type_name, mpst.policy_sub_type_image_path, fr.family_relation_id,
            						fr.family_id, fr.emp_id, ic.insurer_id,
            						ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, mfr.
            						fr_id, mfr.fr_name')
                        ->from('employee_policy_member` AS `epm`, `employee_policy_detail` AS `epd`, `master_policy_type` AS `mps`, `master_policy_sub_type` AS `mpst`, `master_insurance_companies` AS `ic`, `family_relation` AS `fr`, `master_family_relation` AS `mfr`, `employee_family_details` AS `efd`')
                        ->where('epd`.`policy_detail_id` = `epm`.`policy_detail_id')
                        ->where('mps`.`policy_type_id` = `epd`.`policy_type_id')
                        ->where('mpst`.`policy_sub_type_id` = `epd`.`policy_sub_type_id')
                        ->where('ic`.`insurer_id` = `epd`.`insurer_id')
                        ->where('fr`.`family_relation_id` = `epm`.`family_relation_id')
                        ->where('fr`.`family_id` = `efd`.`family_id')
                        ->where('epm.status!=', 'Inactive')
                        ->where('efd`.`fr_id` = `mfr`.`fr_id')
                        ->where('fr`.`emp_id`', $emp_id)->where('epd.policy_detail_id', $value['policy_detail_id'])->get_compiled_select();
                    $response = $this
                        ->db
                        ->query($subQuery1 . ' UNION ALL ' . $op)->result_array();

                    //now change their status based on each policy and create proposal independently
                    //print_R($response);
                    $check = $this
                        ->obj_home
                        ->check_validations_adult_count($response[0]['familyConstruct'], $emp_id, $value['policy_detail_id']);
                    //Check validations for ".$response[0]['policy_sub_type_name']." policy"
                    //print_pre($get_lead_id);
                    if($value['policy_detail_id'] == 467 && $get_lead_id['GCI_optional'] == 'No'){
                        $check = true;
                    }
                    if (!$check)
                    {
                        $array = ["status" => false, "message" => "Member Not Added As Per Selection in " . $response[0]['policy_sub_type_name'] . " policy"];
                        print_r(json_encode($array));
                        return;
                    }
                    //$this->db->trans_commit();




                }
                //echo 1;exit;
                $array = ["status" => true, "message" => "Validated successfully"];
                print_r(json_encode($array));
                return;






            }



            $this->db->trans_start();

            foreach ($policies as $value)
            {
                $q++;
                $policy_details_individual = $this->db->where("policy_detail_id", $value['policy_detail_id'])->get("employee_policy_detail")->row_array();

                if ($policy_details_individual['policy_sub_type_id'] == 1 && $value['combo_flag'] == 'Y')
                {
                    $z = true;
                    $gmc_id = $policy_details_individual['policy_detail_id'];
                    $max_adult_count = $this->db
                        ->select('*')
                        ->from('master_broker_ic_relationship as fc')
                        ->where('fc.policy_id', $policy_details_individual['policy_detail_id'])
                        ->get()->row_array();

                    $relationid = $max_adult_count['relationship_id'];
                    $member_data = $this->obj_home->get_adult_count_new($emp_id, $gmc_id,$relationid);

                    if(empty($member_data))
                    {

                        $array = ["status" => false, "message" => "Please Add Member"];
                        print_r(json_encode($array));
                        return;
                    }
                }
                else
                {
                    if($value['combo_flag'] != 'Y')
                    {
                        // $gmc_id = $policy_details_individual['policy_detail_id'];
                        if($update_data == 'update' && $proposalEdit_id !='')
                        {
                            $ind_update_policy  = $this->db->query("select p.policy_detail_id from proposal where id ='$proposalEdit_id'")->row_array();
                            $gmc_id = $ind_update_policy['policy_detail_id'];
                        }
                        else
                        {
                            $gmc_id = $policy_details_individual['policy_detail_id'];
                        }


                        $max_adult_count = $this->db
                            ->select('fc.relationship_id,fc.max_adult')
                            ->from('master_broker_ic_relationship as fc')
                            ->where('fc.policy_id', $policy_details_individual['policy_detail_id'])
                            ->get()->row_array();

                        $relationid = $max_adult_count['relationship_id'];
                        //print_pre($gmc_id);
                        //print_pre($relationid);
                        $member_data = $this->obj_home->get_adult_count_new($emp_id, $gmc_id,$relationid);
                        //print_pre($member_data);exit;
                        if(!empty($member_data))
                        {
                            $z = true;
                        }
                        if(count($policies) == $q)
                        {
                            if(!$z)
                            {


                                $array = ["status" => false, "message" => "Please Add Member"];
                                print_r(json_encode($array));
                                return;
                            }
                        }
                    }
                }

                if($z == true)
                {
                    $family_construct1 = explode("+", $member_data[0]['familyConstruct']);

                    if ($value['combo_flag'] == "Y" && $policy_details_individual['policy_sub_type_id'] != 1)
                    {
                        //GET MAX ADULT COUNT OF COMBO POLICY
                        $max_adult_count = $this->db
                            ->select('fc.relationship_id,fc.max_adult')
                            ->from('master_broker_ic_relationship as fc')
                            ->where('fc.policy_id', $policy_details_individual['policy_detail_id'])
                            ->get()->row_array();

                        if ($max_adult_count['max_adult'] . "A" == $family_construct1[0] || $max_adult_count['max_adult'] . "A" == $family_construct1[0])
                        {
                            $member = $max_adult_count['max_adult'] . "A";
                            $relationid = $max_adult_count['relationship_id'];
                        }
                        else
                        {
                            $member = $family_construct1[0];
                            $relationid = $max_adult_count['relationship_id'];
                        }

                        $member_data = $this->obj_home->get_adult_count_new($emp_id, $gmc_id, $relationid);

                        $premium_and_sum_insured = $this->db
                            ->select('fc.PremiumServiceTax')
                            ->from('family_construct_wise_si as fc')
                            ->where('fc.policy_detail_id', $policy_details_individual['policy_detail_id'])
                            ->where('fc.family_type', $member)
                            ->where('fc.sum_insured', $member_data[0]['policy_mem_sum_insured'])
                            ->get()->row_array();
                        $policy_detail_id =   $policy_details_individual['policy_detail_id'];
                        $delete_data = $this->db->query("delete epm from employee_policy_member as epm,family_relation as fr  where fr.family_relation_id = epm.family_relation_id AND epm.policy_detail_id = '$policy_detail_id' AND fr.emp_id = '$emp_id'");
                        //enter into combo policy
                        for ($i = 0; $i < count($member_data); $i++)
                        {
                            $member_arrays = [
                                "policy_detail_id" => $value['policy_detail_id'],
                                "family_relation_id" => $member_data[$i]['family_relation_id'],
                                "policy_mem_sum_insured" => $member_data[$i]['policy_mem_sum_insured'],
                                "policy_mem_sum_premium" =>  $premium_and_sum_insured['PremiumServiceTax'],
                                "policy_mem_gender" => $member_data[$i]['policy_mem_gender'],
                                "policy_mem_dob" => $member_data[$i]['policy_mem_dob'],
                                "age" => $member_data[$i]['age'],
                                "age_type" => $member_data[$i]['age_type'],
                                "member_status" => "pending",
                                "fr_id" => $member_data[$i]['fr_id'],
                                "policy_member_first_name" => $member_data[$i]['firstname'],
                                "policy_member_last_name" => $member_data[$i]['lastname'],
                                "familyConstruct" => $member
                            ];

                            $this->obj_m->insert_policy_member($member_arrays);

                            //Add Data into logs Table
                            $logs_array['data'] = ["type" => "proposal_insured_member","req" => json_encode($member_arrays), "lead_id" => $lead_id,"product_id" => $product_id];
                            $this->Logs_m->insertLogs($logs_array);
                        }
                    }
                }
                $decare_proposal_ghd = $this->db->query("delete  from employee_declare_proposal_data where  emp_id = '$emp_id'");

                if(!empty($declare_prop))
                {
                    foreach ($declare_prop as $declare_m_prop)
                    {
                        $data1_prop = array(
                            "emp_id" => $emp_id,
                            //"proposal_number" => '',
                            "product_id" => $value['policy_parent_id'],
                            "remark" => $declare_m_prop['label'],
                            "proposal_declare_id" => $declare_m_prop['question_prop'],
                            "format" => $declare_m_prop['format_prop'],
                            "created_by" => $this->agent_id
                        );

                        if ($update_data == 'update')
                        {
                            $updata_prop = array(
                                "format" => $declare_m_prop['format_prop']
                            );
                            $this->db->where(['emp_id' => $emp_id, 'emp_proposal_declare_id' => $declare_m_prop['question_prop']]);
                            $this->db->update("employee_declare_proposal_data", $updata_prop);

                            //Add Data into logs Table
                            $logs_array['data'] = ["type" => "update_proposal_ghd_declare","req" => json_encode($data1_prop), "lead_id" => $lead_id,"product_id" => $product_id];
                            $this->Logs_m->insertLogs($logs_array);
                        }
                        else
                        {
                            $this->db->insert('employee_declare_proposal_data', $data1_prop);

                            //Add Data into logs Table
                            $logs_array['data'] = ["type" => "insert_proposal_ghd_declare","req" => json_encode($data1_prop), "lead_id" => $lead_id,"product_id" => $product_id];
                            $this->Logs_m->insertLogs($logs_array);
                        }
                    }
                }
                $decare_member_proposal_ghd = $this->db->query("delete  from employee_declare_data where  emp_id = '$emp_id'");
                if(!empty($declare))
                {

                    foreach ($declare as $declare_m)
                    {

                        $data1 = array(
                            "emp_id" => $emp_id,
                            //"proposal_number" => '',
                            "product_id" => $value['policy_parent_id'],
                            "remark" => $declare_m->label,
                            "p_declare_id" => $declare_m->question,
                            "format" => $declare_m->format,
                            "created_by" => $this->agent_id
                        );
                        if ($update_data == 'update')
                        {

                            $updata = array(
                                "format" => $declare_m['format']
                            );

                            $this->db->where(['emp_id' => $emp_id, 'p_declare_id' => $declare_m['question']]);
                            $this->db->update("employee_declare_data", $updata);

                            //Add Data into logs Table
                            $logs_array['data'] = ["type" => "update_proposal_employee_health","req" => json_encode($updata), "lead_id" => $lead_id,"product_id" => $product_id];
                            $this->Logs_m->insertLogs("logs_post_data",$logs_array);
                        }
                        else
                        {

                            $this->db->insert('employee_declare_data', $data1);

                            //Add Data into logs Table
                            $logs_array['data'] = ["type" => "insert_proposal_employee_health","req" => json_encode($data1), "lead_id" => $lead_id,"product_id" => $product_id];
                            $this->Logs_m->insertLogs($logs_array);
                        }
                    }
                }

            }
            //create individual proposals
            //get workflow from policy detail table
            //ends
            //get all members in particular policy
            $subQuery1 = $this->db
                ->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id,
					epd.policy_type_id, epd.policy_sub_type_id, epd.insurer_id, epd.broker_id,
					epm.policy_detail_id, epm.family_relation_id, epm.family_id, epd.policy_no,
					mps.policy_type_id, mpst.policy_sub_type_id, mpst.policy_sub_type_name, mpst.policy_sub_type_image_path,
					mf.family_relation_id, mf.family_id, mf.emp_id, ic.insurer_id, ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, "0" AS fr_id, "Self" AS fr_name')
                ->from('employee_details AS ed,
					family_relation AS mf,
					employee_policy_member AS epm,
					employee_policy_detail AS epd,
					master_policy_sub_type AS mpst,
					master_policy_type AS mps,
					master_insurance_companies AS ic')
                ->where('ed.emp_id = mf.emp_id')->where('mf.family_relation_id = epm.family_relation_id')
                ->where('epm.policy_detail_id = epd.policy_detail_id')->where('epd.policy_sub_type_id = mpst.policy_sub_type_id')
                ->where('mpst.policy_type_id = mps.policy_type_id')->where('epd.insurer_id = ic.insurer_id')
                ->where('epm.status != ', 'Inactive')->where('mf.family_id', 0)->where('mf.emp_id', $emp_id)
                ->where('epd.policy_detail_id', $value['policy_detail_id'])->get_compiled_select();

            $op = $this->db->select('epm.familyConstruct,epm.policy_mem_sum_insured,epm.policy_mem_sum_premium,epm.policy_member_id, epd.policy_detail_id, epd.policy_type_id, epd.policy_sub_type_id,
					epd.insurer_id, epd.broker_id, epm.policy_detail_id,
					epm.family_relation_id, epm.family_id, epd.policy_no,
					mps.policy_type_id, mpst.policy_sub_type_id,
					mpst.policy_sub_type_name, mpst.policy_sub_type_image_path, fr.family_relation_id,
					fr.family_id, fr.emp_id, ic.insurer_id,
					ic.ins_co_name, ic.insurer_companies_img_path, epd.sum_insured, mfr.
					fr_id, mfr.fr_name')
                ->from('employee_policy_member` AS `epm`, `employee_policy_detail` AS `epd`, `master_policy_type` AS `mps`, `master_policy_sub_type` AS `mpst`, `master_insurance_companies` AS `ic`, `family_relation` AS `fr`, `master_family_relation` AS `mfr`, `employee_family_details` AS `efd`')
                ->where('epd`.`policy_detail_id` = `epm`.`policy_detail_id')
                ->where('mps`.`policy_type_id` = `epd`.`policy_type_id')
                ->where('mpst`.`policy_sub_type_id` = `epd`.`policy_sub_type_id')
                ->where('ic`.`insurer_id` = `epd`.`insurer_id')
                ->where('fr`.`family_relation_id` = `epm`.`family_relation_id')
                ->where('fr`.`family_id` = `efd`.`family_id')->where('epm.status!=', 'Inactive')
                ->where('efd`.`fr_id` = `mfr`.`fr_id')->where('fr`.`emp_id`', $emp_id)
                ->where('epd.policy_detail_id', $value['policy_detail_id'])->get_compiled_select();

            $response = $this->db->query($subQuery1 . ' UNION ALL ' . $op)->result_array();

            //now change their status based on each policy and create proposal independently
            $check = $this->obj_home->check_validations_adult_count($response[0]['familyConstruct'],$emp_id,$value['policy_detail_id']);

            //Check validations for ".$response[0]['policy_sub_type_name']." policy"
            if(!$check)
            {
                $array = ["status" => false, "message" => "Member Not Added As Per Selection in ".$response[0]['policy_sub_type_name']." policy"];
                print_r(json_encode($array));
                return;
            }


            $this->db->trans_commit();
            $array = ["status" => true];
            print_r(json_encode($array));
        }
    }
    public function payment_redirect_view($emp_id_encrypt)
    {

        $emp_id = encrypt_decrypt_password($emp_id_encrypt,'D');
        $this->session->set_userdata('emp_id', $emp_id);

        if($emp_id){

            $query = $this->db->query("SELECT ed.address,ed.emp_firstname,ed.emp_lastname,ed.lead_id,ed.emp_id,epd.policy_detail_id,mpst.payment_source_code,ed.email,ed.mob_no,sum(p.premium) as premium,mpst.payment_url,p.status,mpst.product_code,mpst.product_name,pd.ifscCode,pd.branch,pd.bank_name,ed.acc_no FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,payment_details as pd WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND p.id = pd.proposal_id AND ed.emp_id=".$emp_id)->row_array();

            // 7 days = 604800 sec
            // $productname="Axis Tele Inbound Affinity Portal for ABHI";

            $productname="Axis Tele Inbound";


            if($query['product_code']=='R06'){
                $productname="Group Activ Health";
            }

            if($query['product_code']=='T01'){
                $productname="Group Activ Health and Group Activ Secure";
            }

            if($query['product_code']=='T03'){

                $productname="Group Activ Health and Group Activ Secure";
                $super_topup=$this->db->select('emp_id')->from('proposal')->where('emp_id',$emp_id)->where('policy_detail_id',475)->get()->result_array();
                if(!empty($super_topup)){
                    $productname="Group Activ Health";
                }
            }



            if($query['product_code']=='R06'){
                $productname="Group Activ Health";
            }

            if($query['product_code']=='T01'){
                $productname="Group Activ Health and Group Activ Secure";
            }

            if($query['product_code']=='T03'){
                $productname="Group Activ Health and Group Activ Secure";
            }

            // print_r($productname);
            // exit;
            $UniqueIdentifier = "LEADID";
            $UniqueIdentifierValue = $query['lead_id'];
            $CustomerName = $query['emp_firstname']." ".$query['emp_lastname'];
            $Email = $query['email'];
            $PhoneNo = substr(trim($query['mob_no']), -10);
            $FinalPremium = round($query['premium'],2);
            $ProductInfo = $query['product_name'];
            $address = $query['address'];
            $FinalPremium = round($query['premium'],2);

            if(!empty($query))
            {
                $api = new Api($this->keyId, $this->keySecret);
                $orderData = [
                    'receipt'         => 3456,
                    'amount'          => $FinalPremium*100 , // 2000 rupees in paise
                    'currency'        => 'INR',
                    'payment_capture' => 1 // auto capture
                ];

                $razorpayOrder = $api->order->create($orderData);

                $razorpayOrderId = $razorpayOrder['id'];

                $_SESSION['razorpay_order_id'] = $razorpayOrderId;

                $displayAmount = $amount = $orderData['amount'];
                $displayCurrency = $this->displayCurrency;

                if ($displayCurrency !== 'INR')
                {
                    $url = 'https://api.razorpay.com/v1/orders';
                    $exchange = json_decode(file_get_contents($url), true);

                    $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
                }

                $checkout = 'automatic';

                if (isset($_GET['checkout']) and in_array($_GET['checkout'], ['automatic', 'manual'], true))
                {
                    $checkout = $_GET['checkout'];
                }

                $data = [
                    "key"               => $this->keyId,
                    "amount"            => $amount,
                    "name"              => 'FynTune',
                    "description"       => $ProductInfo,
                    "image"             => "http://fyntune.com/images/logo/logo.png",
                    "prefill"           => [
                        "name"              => $CustomerName,
                        "email"             => $Email,
                        "contact"           => $PhoneNo,
                    ],
                    "notes"             => [
                        "address"           => $address,
                        "merchant_order_id" => "12312321",
                    ],
                    "theme"             => [
                        "color"             => "#F37254"
                    ],
                    "order_id"          => $razorpayOrderId,
                ];


                $data['display_currency']  = $displayCurrency;
                $data['display_amount']    = $displayAmount;
                $data['customer_id']    = $UniqueIdentifierValue;
                $data['lead_id']    = encrypt_decrypt_password($UniqueIdentifierValue);
                $data['emp_id']= encrypt_decrypt_password($emp_id);
                $res['data'] = $data;

                //$json = json_encode($data);
                //$this->load->view('template/customer_portal_header.php');
                $this->load->view('template/customer-header.php');
                $this->load->view('teleproposal/pg_submit',$data);
                $this->load->view('template/footer_tele.php');
                //$this->load->telesales_template("pg_submit",compact('data'));

            }else{
                echo "Payment link has been expired, Please get in touch with your Branch RM";
            }
        }
    }
    public function coi_download()
    {

        $lead_id = $this
            ->input
            ->post('lead_id');



        $data = $this
            ->db
            ->query("select ed.emp_id,ed.lead_id,ed.product_id,CONCAT(ed.emp_firstname,ed.emp_lastname) as cust_details,apr.certificate_number,apr.COI_url,apr.pr_api_id from employee_details as ed,proposal as p,api_proposal_response as apr where ed.emp_id = p.emp_id and p.proposal_no = apr.proposal_no_lead and p.emp_id = apr.emp_id and p.status in('Success','Payment Received') and ed.lead_id = TRIM('$lead_id') ")->row_array();
    //   print_r($this->db->last_query());
        $product_code = $data['product_id'];
        $emp_id = $data['emp_id'];
        $quer = $this->db->query("select GROUP_CONCAT(DISTINCT(master_policy_no)) as policy_number,product_name,policy_parent_id,id from product_master_with_subtype where product_code = '$product_code'")->row_array();
        $data['policy_number'] = $quer['policy_number'];
        $data['plan_name'] = $quer['product_name'];
        $policy_parent_id = $quer['policy_parent_id'];
        $product_id = $quer['id'];
        $policy_data = $this->db->query("select GROUP_CONCAT(DISTINCT(policy_no)) as policy_name,policy_detail_id,policy_sub_type_id from employee_policy_detail where parent_policy_id = '$policy_parent_id' and product_name = '$product_id'")->result_array();
        //	print_r($this->db->last_query());
        $nominee_det = $this->db->query("select concat(nominee_fname,nominee_lname)as nominee_name from member_policy_nominee where emp_id = '$emp_id'")->row_array();
        $data['nominee_name'] = $nominee_det['nominee_name'];

        foreach($policy_data as $value)
        {

            $policy_detail_id = $value['policy_detail_id'];
            $policy_sub_type_id = $value['policy_sub_type_id'];
            $policy_sub_type_name = $this->db->query("select policy_sub_type_name from master_policy_sub_type where policy_sub_type_id = '$policy_sub_type_id'")->row_array();

            $quer_det = $this->db->query("select epm.policy_member_first_name,epm.policy_member_last_name,epm.policy_mem_dob,epm.policy_mem_gender,epm.policy_mem_sum_insured as cover,mfr.fr_name from family_relation as fr,employee_policy_member as epm,master_family_relation as mfr where fr.family_relation_id = epm.family_relation_id and fr.emp_id = '$emp_id' and epm.policy_detail_id = '$policy_detail_id' and epm.fr_id = mfr.fr_id")->result_array();
            $data_ref['insured_member'][$policy_sub_type_id]['member'] = $quer_det;
            $data_ref['insured_member'][$policy_sub_type_id]['policy_sub_type_name'] = $policy_sub_type_name['policy_sub_type_name'];
        }
        $data['insured_details'] =$data_ref;
    //    print_R($data);die;
        $html = $this
            ->load
            ->view("teleproposal/coi_pdf", $data, true);
        echo $html;
    }


    public function payment_return_view($emp_id_encrypt){
        //	echo 123;die;
        //print_R($_POST);die;
        //$emp_id = $this->session->userdata('emp_id');
        $emp_id = encrypt_decrypt_password($emp_id_encrypt,'D');
        $this->session->set_userdata('emp_id', $emp_id);
        $success = true;
        $encrypted = $this->input->post('RESPONSE');

        if(isset($_POST['razorpay_payment_id'])){
            // var_dump($success);exit;
            if ($success === true)
            {



                extract($_POST);
                $TxRefNo = $_POST['razorpay_payment_id'];
                $TxStatus = "success";
                $TxMsg = "No Error";


            }

            $query = $this->db->query("SELECT GROUP_CONCAT(p.id) proposal_id,ed.emp_id,ed.lead_id,mpst.product_code FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,payment_details AS pd WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND p.id = pd.proposal_id AND ed.emp_id='".$emp_id."' GROUP BY p.emp_id")->row_array();
            //print_pre($query);die;
            if(!empty($query['proposal_id'])){

                $ids = explode(',',$query['proposal_id']);

                if(isset($TxRefNo)){

                    $request_arr = ["lead_id" => $query['lead_id'], "req" => '',"res"=>json_encode($_POST),"product_id"=> $query['product_code'], "type"=>"payment_response_post"];
                    $txnDateTime = date('Y-m-d H:i:s');
                    $dataArray['tablename'] = 'logs_docs';
                    $dataArray['data'] = $request_arr;
                    $this->Logs_m->insertLogs($dataArray);

                    $request_arr = ["payment_status" => $TxMsg,"premium_amount" => ($amount/2),"payment_type" => 'PR',"txndate" => $txnDateTime,"TxRefNo" => $TxRefNo,"TxStatus"=>$TxStatus,"json_quote_payment"=>json_encode($_POST)];

                    $this->db->where_in('proposal_id', $ids);
                    $this->db->where('TxStatus != ','success');
                    $this->db->update("payment_details",$request_arr);

                }





                $proposal_id = $ids[0];

                $payment_data = $this->db->query("select payment_status,TxStatus,txndate,TxRefNo from payment_details where proposal_id='$proposal_id'")->row_array();

              //  print_R($payment_data);die;
                if($payment_data['TxStatus'] == 'success'){

                    $data_res = $this->obj_home->policy_creation_call($query['lead_id']);

                    //print_R($data_res);die;

                    if($data_res['Status'] == 'Success'){

                        $data_policy = $this->db->query("select GROUP_CONCAT(DISTINCT(certificate_number)) certificate_number from api_proposal_response where emp_id='$emp_id' GROUP BY emp_id")->row_array();

                        if($data_policy){

                            $data['proposal_no'] = $query['proposal_no'];
                            $data['lead_id'] = $query['lead_id'];
                            $data['TxRefNo'] = $payment_data['TxRefNo'];

                            $MandateLink_data = $this->db->query("select MandateLink,Registrationmode from emandate_data where lead_id = '".$query['lead_id']."'")->row_array();

                            $data['html'] = '<p class="g-success mt-1 text-center">Your payment was successful</p>
                    <div class="color_red text-left back-thnk">
                        <p>Lead ID:
                            <span id="lead_view"> '.$data['lead_id'] .'</span>
                        </p>
                        <p>Payment ID:
                            <span>'. $data['TxRefNo'].'</span>
                        </p>
                        <p>Certificate Number:
                            <span style="word-break: break-all;text-align: right;line-height: 16px;width: 72%;margin-top: 1%;">'.$data_policy['certificate_number'].'</span>
                        </p>
                    </div>';
//print_r($data);die;
                            $this->load->view('template/customer-header.php');
                            $this->load->view('teleproposal/thankyou',compact('data_policy','data','MandateLink_data'));
                            $this->load->view('template/footer_tele.php');
                          //  $this->load->telesales_template("thankyou",compact('data_policy','data','MandateLink_data'));
                        }

                    }    else
                    {
                        $data['html'] = '<p class="g-success mt-1 text-center">Your payment failed</p>
                <p>Lead ID:<span id="lead_view"> '.$lead_id.'</span></p>
                         <p> <span>'.$error.'</span></p>';
                    }

                }else{
                    $data['html'] = '<p class="g-success mt-1 text-center">Your payment failed</p>
                <p>Lead ID:<span id="lead_view"> '.$lead_id.'</span></p>
                         <p> <span>'.$error.'</span></p>';                }


            }else{

                echo "Payment link has been expired, Please get in touch with your Branch RM";

            }


        }
    }



    public function proposalSummary()
    {
        $parent_id = $this->parent_id;
        $data = $this->obj_home->get_common_data();
        $data['agent_details'] = $this->obj_home->get_agent_details();
        $data['employee_declaration'] = $this->obj_home->health_declaration_emp_data($parent_id);
        $data['policy_details'] = $this->obj_home->get_policy_data_emp($parent_id);
        $data['redirectFrom_email'] = 'No';
        $emp_id_encrypt = encrypt_decrypt_password($this->emp_id,'E');
        $data['emp_id'] = $emp_id_encrypt;
        $data['sum_insured_data'] = $this->db->query("select  sum(epm.policy_mem_sum_premium) as premium,epm.policy_mem_sum_insured,epm.familyConstruct  from employee_policy_member as epm,family_relation as fr where fr.family_relation_id = epm.family_relation_id AND fr.emp_id  = '".$this->emp_id."' AND epm.fr_id = 0 group by fr.emp_id")->row_array();
        // echo $this->db->last_query();exit;
        $data['disposition']  = $this->db->select('*')
            ->from('employee_disposition ed')
            ->join('disposition_master dm', 'ed.disposition_id = dm.id')
            ->where('emp_id',$this->emp_id)
            ->order_by('ed.id','desc')
            ->get()

            ->row_array();
        $data['parent_id'] = $parent_id;
        $data['remarks'] = $parent_id;

        //echo 1;exit;

       //  print_r($data);
       // $string = $this->load->telesales_template("summary",compact('data'),true);
        $this->load->view('template/header_tele.php');
        $this->load->view('teleproposal/Summary',$data);
        $this->load->view('template/footer_tele.php');

    }

}

?>