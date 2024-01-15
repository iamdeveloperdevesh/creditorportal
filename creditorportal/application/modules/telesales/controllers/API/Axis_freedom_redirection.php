<?php if (!defined('BASEPATH'))
{
	exit('No direct script access allowed');
}

class Axis_freedom_redirection extends CI_Controller
{
	public $algoMethod;
    public $hashMethod;
    public $hash_key;
	public $encrypt_key;
	
	function __construct()
	{
		parent::__construct();	
		
		$this->algoMethod = 'aes-128-ecb';
        $this->hashMethod = 'SHA512';
        $this->hash_key = 'razorpay';
        $this->encrypt_key = 'axisbank12345678';
		
//		 ini_set('display_errors', 1);
//		 ini_set('display_startup_errors', 1);
//		 error_reporting(E_ALL);
		
		$this->load->model("API/Payment_integration_freedom_plus", "obj_api", true);
		$this->load->model("Logs_m");
		//echo encrypt_decrypt_password(8453);
		$this
        ->load
        ->model("employee/home_m", "obj_home", true);
		
	}
/* Premium missmatch cases in Healthpro Infinity*/

	public function checkPremiumMissmatchInfinity(){
        // $leads = array('6849490','89989298','320103');//
ini_set('memory_limit',-1);
        $leads = $this->db->query("SELECT ed.emp_id,ed.deductable,ed.lead_id,sum(p.premium) as premium,CAST(pd.premium_amount AS UNSIGNED INTEGER) as premium_amount,p.created_date,p.status
            FROM employee_policy_detail AS epd,
            product_master_with_subtype AS mpst,
            employee_details AS ed,
            proposal as p,payment_details as pd 
            WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id 
            AND mpst.policy_subtype_id = epd.policy_sub_type_id 
            AND p.policy_detail_id=epd.policy_detail_id 
            AND p.id = pd.proposal_id  
            and p.policy_detail_id in (460,461,462)
            AND ed.lead_id IN (413723620,413746729,414177800,414329870,414337601,414353822,414358226,414367069,414369904,414390235,414391570,414552936,414578888,414582950,414617991,414618917,414595910,414627196,414631721,414648305,414668731,414714291,414721390,414731121,414776568,414781403,414785005,414785360,414795243,414795473,414795711,414824820,414832668,414861694,414879607,414891843,414898936,414919690,414923152,414925182,414926527,414926976,414927972,414929360,414944575,414947118,414952266,414956470,414924851,414959343,414961139,414964324,414969296,414970939,414971738,414973227,414974534,414945879,414976381,414980955,414985323,414985996,414986569,414990814,414991938,414992954,414996375,414997092,415001089,415004764,415006681,415017335,415022161,415028261,415029479,415010876,415038089,415053222,415053462,415060057,415067049,415082728,415085030,415084876,415086143,415069954,415120300,415123734,415124292,415125477,415133075,415142328,415146109,415152690,415157265,415159014,415166071,415177553,415181535,415188771,415191038,415196673,415200084,415200550,415199811,415240511,415245532,415269935,415295070,415300831,415300921,415316804,415328396,415331518,415320137,415336780,415337346,415338557,415340659,415341732,415342413,415348596,415348891,415350995,415350498,415355774,415357975,415359024,415358410,415359486,415360528,415365164,415364556,415370844,415371070,415376740,415377139,415379489,415381456,415381606,415381698,415386479,415387431,415386436,415386871,415389074,415398470,415414505,415420868,415445898,415449681,415459372,415460073,415463405,415463485,415465962,415468828,415470087,415504675,415508326,415512313,415512978,415514013,415527305,415523893,415532363,415537213,415542238,415546586,415554511,415554935,415565337,415565720,415573124,415576329,415581196,415583444,415585274,415585447,415585892,415586358,415587138,416288064,416316237,416325367,416328913,417420039,416321908,416252175,416298226,416300734,416321151,416353089,416345376,416306746,416286315,416341197,416315402,416269433,416341627,416312638,416323989,416349783,416311924,416307385,416359645,416335882,416269355,416297010,416347676,416322180,416341726,416320504,416340596,416319417,416311323,416321458,416294998,416316309,416334015,416316729,416337723,416301700,416338311,416269182,416318570,416322488,416338733,416321938,416316837,416337642,416342176,416390902,416284114,416336655,416331341,416355755,416283701,416311860,416322547,416305271,416334221,416350205,416277514,416340529,416299357,416278647,416353302,416352045,416280874,416344354,416338247,416352653,416266054,416321965,416382360,416367356,416307033,416310371,416320593,416319470,416330325,416337583,416351049,416304774,416344663,416338229,416478036,416977054,417757463)
            GROUP BY p.emp_id "
        )->result_array();
//echo $this->db->last_query();exit;
        $data = [];
        foreach ($leads as $key => $value) {
            $data[$key]["Lead_Id"] = $value['lead_id'];
            $data[$key]["Proposal_table_premium"] = $value['premium'];
            $data[$key]["Payment_Detail_table_premium"] = $value['premium_amount'];
            $data[$key]["Proposal_Date"] = $value['created_date'];

            $emp_data = $this->db->query("SELECT emp_id,deductable from employee_details WHERE lead_id = '".$value['lead_id']."'")->row_array();
            $emp_id = $emp_data['emp_id'];
            $deductable = $emp_data['deductable'];
            $q = "SELECT policy_detail_id,id,premium from proposal where emp_id = '".$emp_id."'";
            $fail_data = $this->db->query($q)->result_array();
            $totalPremium = 0;
            foreach ($fail_data as $key1 => $pid) {
                // echo 'proposal table premium = '.$pid['premium'].'<br>';
                // echo "Lead Id - ".$value.'<br>';
		        //echo "Cover : ".$pid['policy_detail_id']."<br>";
                $policy_detail_id = $pid['policy_detail_id'];
                $proposal_id = $pid['id'];
                $age = [];
                $response = $this->db
                    ->query('SELECT epd.policy_detail_id,epd.policy_sub_type_id,epm.family_relation_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
                        epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type","R001" as relation_code
                        FROM 
                        employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_details AS ed
                        WHERE epd.policy_detail_id = epm.policy_detail_id
                        AND epm.family_relation_id = fr.family_relation_id
                        AND fr.family_id = 0
                        AND fr.emp_id = ed.emp_id
                        AND ed.emp_id = ' . $emp_id . '
                        AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' UNION all SELECT epd.policy_detail_id,epd.policy_sub_type_id,epm.family_relation_id, epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
                        epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type",mfr.relation_code
                        FROM 
                        employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
                        master_family_relation AS mfr
                        WHERE epd.policy_detail_id = epm.policy_detail_id
                        AND epm.family_relation_id = fr.family_relation_id
                        AND fr.family_id = efd.family_id 
                        AND efd.fr_id = mfr.fr_id
                        AND fr.emp_id = ' . $emp_id . '
                        AND `epd`.`policy_detail_id` = ' . $policy_detail_id)->result_array();

                    // print_pre($response);
                if($response[0]['suminsured_type'] == 'family_construct_age'){
                    $change_premium = false;
                    //print_pre($response);//exit;
                    foreach($response as $value){
                        if($value['age_type'] == 'days'){
                            $age[] = 0;
                        }else{
                            $age[] = $value['age'];
                        }
                                            
                    }
                    //print_pre($age);exit;
                    if($policy_detail_id == HEALTHPROXL_GHI_ST){
                       $check = $this->db->select("*")
                                ->from("family_construct_age_wise_si")
                                ->where("sum_insured", $response[0]['policy_mem_sum_insured'])
                                ->where("family_type", $response[0]['familyConstruct'])
                                ->where("policy_detail_id", $policy_detail_id)
                                ->where("deductable", $deductable)
                                ->get()
                                ->result_array(); 
                    }else{
                        $check = $this->db->select("*")
                                ->from("family_construct_age_wise_si")
                                ->where("sum_insured", $response[0]['policy_mem_sum_insured'])
                                ->where("family_type", $response[0]['familyConstruct'])
                                ->where("policy_detail_id", $policy_detail_id)
                                ->get()
                                ->result_array(); 

                    }           
                   //echo $this->db->last_query().'<br>';
                    $max_age = max($age);
                    foreach($check as $value){
                        $min_max_age = explode("-",$value['age_group']);
                        if($max_age >= $min_max_age[0] && $max_age <= $min_max_age[1]){
                            $premium = $value['PremiumServiceTax'];
                        }
                    }
                    $totalPremium += $premium;
                    //print_r($response);exit;
                }
            }
            $data[$key]["max_age"] = $max_age;
            $data[$key]["totalPremium"] = $totalPremium;
        }
	//print_pre($data);exit;
 $agentData = [$data];
        $this
            ->load
            ->library("excel");
        $config = ['filename' => 'mismatch_data_xl' . date("d-m-Y") , // prove any custom name here
        'use_sheet_name_as_key' => false, // this will consider every first index from an associative array as main headings to the table
        'use_first_index' => true, // if true then it will set every key as sheet name for appropriate sheet
        ];
        $sheetdata = Excel::export($agentData, $config);

        /*foreach ($data as $keyy => $value) {
            if($value['Proposal_table_premium'] != $value['totalPremium'){

            }
        }*/
       // exit;
    }


public function checkPremiumMissmatchInfinity___bk(){
        // $leads = array('6849490','89989298','320103');//

        $leads = $this->db->query("SELECT ed.emp_id,ed.deductable,ed.lead_id,sum(p.premium) as premium,CAST(pd.premium_amount AS UNSIGNED INTEGER) as premium_amount,p.created_date,p.status
            FROM employee_policy_detail AS epd,
            product_master_with_subtype AS mpst,
            employee_details AS ed,
            proposal as p,payment_details as pd
            ,user_payu_activity as ua 
            WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id 
            AND mpst.policy_subtype_id = epd.policy_sub_type_id 
            AND p.policy_detail_id=epd.policy_detail_id 
            AND p.id = pd.proposal_id AND ed.emp_id = ua.emp_id 
            and p.policy_detail_id in (460,461,462)
            -- and p.policy_detail_id in (470,471,472)
            AND p.status = 'Payment Pending'
            GROUP BY p.emp_id
            having  sum(p.premium) != CAST(premium_amount AS UNSIGNED INTEGER)"
        )->result_array();

        foreach ($leads as $key => $value) {
        // foreach ($leads as $key => $value) {
            echo "Lead Id - ".$value['lead_id'].'<br>';
            echo "Proposal table premium - ".$value['premium'].'<br>';
            echo "Payment Detail table premium - ".$value['premium_amount'].'<br>';
            echo "Proposal Date - ".$value['created_date'].'<br>';

            $emp_data = $this->db->query("SELECT emp_id,deductable from employee_details WHERE lead_id = '".$value['lead_id']."'")->row_array();
            $emp_id = $emp_data['emp_id'];
            $deductable = $emp_data['deductable'];
            $q = "SELECT policy_detail_id,id,premium from proposal where emp_id = '".$emp_id."'";
            $fail_data = $this->db->query($q)->result_array();
            $totalPremium = 0;
            foreach ($fail_data as $key => $pid) {
                // echo 'proposal table premium = '.$pid['premium'].'<br>';
                // echo "Lead Id - ".$value.'<br>';
		echo "Cover : ".$pid['policy_detail_id']."<br>";
                $policy_detail_id = $pid['policy_detail_id'];
                $proposal_id = $pid['id'];
                $age = [];
                $response = $this->db
                    ->query('SELECT epd.policy_detail_id,epd.policy_sub_type_id,epm.family_relation_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
                        epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type","R001" as relation_code
                        FROM 
                        employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_details AS ed
                        WHERE epd.policy_detail_id = epm.policy_detail_id
                        AND epm.family_relation_id = fr.family_relation_id
                        AND fr.family_id = 0
                        AND fr.emp_id = ed.emp_id
                        AND ed.emp_id = ' . $emp_id . '
                        AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' UNION all SELECT epd.policy_detail_id,epd.policy_sub_type_id,epm.family_relation_id, epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
                        epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type",mfr.relation_code
                        FROM 
                        employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
                        master_family_relation AS mfr
                        WHERE epd.policy_detail_id = epm.policy_detail_id
                        AND epm.family_relation_id = fr.family_relation_id
                        AND fr.family_id = efd.family_id 
                        AND efd.fr_id = mfr.fr_id
                        AND fr.emp_id = ' . $emp_id . '
                        AND `epd`.`policy_detail_id` = ' . $policy_detail_id)->result_array();

                    // print_pre($response);
                if($response[0]['suminsured_type'] == 'family_construct_age'){
                    $change_premium = false;
                    //print_pre($response);//exit;
                    foreach($response as $value){
                        if($value['age_type'] == 'days'){
                            $age[] = 0;
                        }else{
                            $age[] = $value['age'];
                        }
                                            
                    }
                    //print_pre($age);exit;
                    if($policy_detail_id == HEALTHPROXL_GHI_ST){
                       $check = $this->db->select("*")
                                ->from("family_construct_age_wise_si")
                                ->where("sum_insured", $response[0]['policy_mem_sum_insured'])
                                ->where("family_type", $response[0]['familyConstruct'])
                                ->where("policy_detail_id", $policy_detail_id)
                                ->where("deductable", $deductable)
                                ->get()
                                ->result_array(); 
                    }else{
                        $check = $this->db->select("*")
                                ->from("family_construct_age_wise_si")
                                ->where("sum_insured", $response[0]['policy_mem_sum_insured'])
                                ->where("family_type", $response[0]['familyConstruct'])
                                ->where("policy_detail_id", $policy_detail_id)
                                ->get()
                                ->result_array(); 

                    }           
                   //echo $this->db->last_query().'<br>';
                    $max_age = max($age);
                    foreach($check as $value){
                        $min_max_age = explode("-",$value['age_group']);
                        if($max_age >= $min_max_age[0] && $max_age <= $min_max_age[1]){
                            $premium = $value['PremiumServiceTax'];
                        }
                    }
                    $totalPremium += $premium;
                    //print_r($response);exit;
                }
            }
            echo $max_age.'<---->'.$totalPremium.'<hr>';
        }
       // exit;
    }
public function checkPremiumMissmatchInfinity_bk(){
        // $leads = array('6849490','89989298','320103');//

        $leads = $this->db->query("SELECT ed.emp_id,ed.deductable,ed.lead_id,sum(p.premium) as premium,CAST(pd.premium_amount AS UNSIGNED INTEGER) as premium_amount,p.created_date,p.status
            FROM employee_policy_detail AS epd,
            product_master_with_subtype AS mpst,
            employee_details AS ed,
            proposal as p,payment_details as pd
            ,user_payu_activity as ua 
            WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id 
            AND mpst.policy_subtype_id = epd.policy_sub_type_id 
            AND p.policy_detail_id=epd.policy_detail_id 
            AND p.id = pd.proposal_id AND ed.emp_id = ua.emp_id 
            and p.policy_detail_id in (460,461,462)
            -- and p.policy_detail_id in (470,471,472)
            AND p.status = 'Payment Pending'
            GROUP BY p.emp_id
            having  sum(p.premium) != CAST(premium_amount AS UNSIGNED INTEGER)"
        )->result_array();

        //print_pre($leads);exit;

        foreach ($leads as $key => $value) {
        // foreach ($leads as $key => $value) {
            echo "Lead Id - ".$value['lead_id'].'<br>';
            echo "Proposal table premium - ".$value['premium'].'<br>';
            echo "Payment Detail table premium - ".$value['premium_amount'].'<br>';
	    echo "Proposal Date - ".$value['created_date'].'<br>';

            $emp_data = $this->db->query("SELECT emp_id,deductable from employee_details WHERE lead_id = '".$value['lead_id']."'")->row_array();
            $emp_id = $emp_data['emp_id'];
            $deductable = $emp_data['deductable'];
            $q = "SELECT policy_detail_id,id,premium from proposal where emp_id = '".$emp_id."'";
            $fail_data = $this->db->query($q)->result_array();
            // echo $q;exit;
            // print_pre($fail_data);exit;
            foreach ($fail_data as $key => $pid) {
                // echo 'proposal table premium = '.$pid['premium'].'<br>';
                // echo "Lead Id - ".$value.'<br>';
                $policy_detail_id = $pid['policy_detail_id'];
                $proposal_id = $pid['id'];
                $age = [];
                $response = $this->db
                    ->query('SELECT epd.policy_detail_id,epd.policy_sub_type_id,epm.family_relation_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
                        epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type","R001" as relation_code
                        FROM 
                        employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_details AS ed
                        WHERE epd.policy_detail_id = epm.policy_detail_id
                        AND epm.family_relation_id = fr.family_relation_id
                        AND fr.family_id = 0
                        AND fr.emp_id = ed.emp_id
                        AND ed.emp_id = ' . $emp_id . '
                        AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' UNION all SELECT epd.policy_detail_id,epd.policy_sub_type_id,epm.family_relation_id, epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
                        epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type",mfr.relation_code
                        FROM 
                        employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
                        master_family_relation AS mfr
                        WHERE epd.policy_detail_id = epm.policy_detail_id
                        AND epm.family_relation_id = fr.family_relation_id
                        AND fr.family_id = efd.family_id 
                        AND efd.fr_id = mfr.fr_id
                        AND fr.emp_id = ' . $emp_id . '
                        AND `epd`.`policy_detail_id` = ' . $policy_detail_id)->result_array();

                    //print_pre($response);
                if($response[0]['suminsured_type'] == 'family_construct_age'){
                    $change_premium = false;
                    //print_pre($response);//exit;
                    foreach($response as $value){
                        if($value['age_type'] == 'days'){
                            $age[] = 0;
                        }else{
                            $age[] = $value['age'];
                        }
                                            
                    }
                    //print_pre($age);exit;
                    if($policy_detail_id == HEALTHPROXL_GHI_ST){
                       $check = $this->db->select("*")
                                ->from("family_construct_age_wise_si")
                                ->where("sum_insured", $response[0]['policy_mem_sum_insured'])
                                ->where("family_type", $response[0]['familyConstruct'])
                                ->where("policy_detail_id", $policy_detail_id)
                                ->where("deductable", $deductable)
                                ->get()
                                ->result_array(); 
                    }else{
                        $check = $this->db->select("*")
                                ->from("family_construct_age_wise_si")
                                ->where("sum_insured", $response[0]['policy_mem_sum_insured'])
                                ->where("family_type", $response[0]['familyConstruct'])
                                ->where("policy_detail_id", $policy_detail_id)
                                ->get()
                                ->result_array(); 

                    }           
                   echo $this->db->last_query().'<br>';
                    $max_age = max($age);
                    foreach($check as $value){
                        $min_max_age = explode("-",$value['age_group']);
                        if($max_age >= $min_max_age[0] && $max_age <= $min_max_age[1]){
                            $premium = $value['PremiumServiceTax'];
                        }
                    }
                    echo $max_age.'<---->'.$premium.'<hr>';
                    //print_r($response);exit;
                }
            }
        }
        exit;
    }

	/*script to update premium for R11
    Author - Ankita
    Date - 22/03/2021*/
    public function update_premium_script(){
echo "remove exit to proceed";exit;
//$leads = array('415514013','416244542','416278647','416288064','416316237','416325367','416325422','416326222','416328913');
$leads = array(2021346072323232353,54650,54600,20213529066446,76576546546,76565664655,2021361116455396,6565654543,689094890,56404560,87980,464800,63202103,4540540,413718498,413752970,413754965,413874807,20213954783,414269396,414335837,414361286,414375128,414376452,414547345,414574110,414584776,414610674,414673099,414689748,414777079,414781671,414751030,414875737,414890154,414926499,414946985,414949572,414950301,414957227,414959284,414972422,414974190,414983564,414987427,414991927,414993465,414994098,414995302,414995831,414994061,414997391,415003296,415013050,415013633,415013747,415009256,415025385,415038361,415043785,415043448,415094035,415094578,415139323,415166711,415167525,415174855,415181939,415181383,415184292,415188126,415190319,415193662,415202598,415226626,415237639,415265635,415301685,415317298,415325194,415336819,415327897,415350047,415380099,415383879,415386176,415386985,415397581,415398557,415399919,415429228,415432013,415433487,415442543,415505538,415506449,415509996,415512366,415538001,415544729,415543403,415546079,415558783,415574922,415586811,415571805,415611234,415623023,415623993,415623737,415625672);

//$leads = array('413723620','413746729','414177800','414337601','414329870','414353822','414358226','414367069','414369904','414390235','414391570','414552936','414582950','414578888','414618917','414595910','414617991','414627196','414648305','414631721','414668731','414714291','414721390','414731121','414776568','414781403','414785005','414785360','414795711','414795243','414795473','414824820','414832668','414861694','414879607','414898936','414919690','414926976','414926527','414929360','414927972','414923152','414944575','414947118','414948890','414952266','414956470','414924851','414959343','414961139','414964324','414969296','414970939','414971738','414973227','414974534','414945879','414980955','414985996','414986569','414990814','414976381','414991938','414992954','414997092','414985323','414996375','415004764','415006681','415017277','415017335','415028261','415022161','415029479','415038089','415010876','415001089','415067049','415060057','415086143','415082728','415084876','415085030','415069954','415120300','415123734','415124292','415133075','415142328','415152690','415157265','415159014','415166071','415146109','415177553','415181535','415188771','415191038','415053462','415196673','415200084','415199811','415200550','415240511','415245532','415125477','415269935','414925182','415295964','415300831','415295070','415316804','415328396','415331128','415331128','415331518','415332057','415334802','415320137','415336780','415338557','415340659','415341732','415342413','415346236','415300921','415348891','415350498','415350995','415352954','415353514','415348596','415358410','415359486','415359024','415364241','415337346','415365164','415364556','415368696','415370844','415371070','415374936','415377139','415379388','415379489','415381606','415381698','415381456','415383974','415385178','415379067','415386479','415387431','415389074','415390233','415386871','415398470','415403587','415414505','415419926','415420868','415444018','415445898','415448230','415449681','415456892','415459372','415462310','415463485','415449061','415465962','415464185','415468828','415470087','415474044','415476452','415477306','415479828','415480899','415484513');



        //$leads = array('414948890');//
        foreach ($leads as $key => $value) {
            $emp_data = $this->db->query("SELECT emp_id,deductable from employee_details WHERE lead_id = '".$value."'")->row_array();
            $emp_id = $emp_data['emp_id'];
            $deductable = $emp_data['deductable'];
            //$q = "SELECT policy_detail_id,id from proposal where emp_id = '".$emp_id."' AND status = 'Success'";
	    $q = "SELECT policy_detail_id,id from proposal where emp_id = '".$emp_id."' AND status = 'Payment Pending'";
            echo $q;//exit;
            $fail_data = $this->db->query($q)->result_array();
            print_pre($fail_data);//exit;
            foreach ($fail_data as $key => $pid) {
                echo "<br><br>Lead Id - ".$value.'<br>';
                $policy_detail_id = $pid['policy_detail_id'];
                $proposal_id = $pid['id'];
		$age = [];
                $response = $this->db
                    ->query('SELECT epd.policy_detail_id,epd.policy_sub_type_id,epm.family_relation_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
                        epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type","R001" as relation_code
                        FROM 
                        employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_details AS ed
                        WHERE epd.policy_detail_id = epm.policy_detail_id
                        AND epm.family_relation_id = fr.family_relation_id
                        AND fr.family_id = 0
                        AND fr.emp_id = ed.emp_id
                        AND ed.emp_id = ' . $emp_id . '
                        AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' UNION all SELECT epd.policy_detail_id,epd.policy_sub_type_id,epm.family_relation_id, epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
                        epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type",mfr.relation_code
                        FROM 
                        employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
                        master_family_relation AS mfr
                        WHERE epd.policy_detail_id = epm.policy_detail_id
                        AND epm.family_relation_id = fr.family_relation_id
                        AND fr.family_id = efd.family_id 
                        AND efd.fr_id = mfr.fr_id
                        AND fr.emp_id = ' . $emp_id . '
                        AND `epd`.`policy_detail_id` = ' . $policy_detail_id)->result_array();
                if($response[0]['suminsured_type'] == 'family_construct_age'){
                    $change_premium = false;
                    //print_pre($response);//exit;
                    foreach($response as $value){
                        if($value['age_type'] == 'days'){
                            $age[] = 0;
                        }else{
                            $age[] = $value['age'];
                        }
                                            
                    }
                    print_pre($age);//exit;
                    if($policy_detail_id == HEALTHPROXL_GHI_ST){
                       $check = $this->db->select("*")
                                ->from("family_construct_age_wise_si")
                                ->where("sum_insured", $response[0]['policy_mem_sum_insured'])
                                ->where("family_type", $response[0]['familyConstruct'])
                                ->where("policy_detail_id", $policy_detail_id)
                                ->where("deductable", $deductable)
                                ->get()
                                ->result_array(); 
                    }else{
                        $check = $this->db->select("*")
                                ->from("family_construct_age_wise_si")
                                ->where("sum_insured", $response[0]['policy_mem_sum_insured'])
                                ->where("family_type", $response[0]['familyConstruct'])
                                ->where("policy_detail_id", $policy_detail_id)
                                ->get()
                                ->result_array(); 

                    }           
//print_pre($check);exit;
                   echo $this->db->last_query().'<br>';
                    $max_age = max($age);
                    foreach($check as $value){
                        $min_max_age = explode("-",$value['age_group']);
                        if($max_age >= $min_max_age[0] && $max_age <= $min_max_age[1]){
                            $premium = $value['PremiumServiceTax'];
                        }
                    }
                    echo $premium;
                    foreach($response as $key => $value1){
                        //echo $premium.'<br>';
                        
                        if($response[$key]['policy_sub_type_id'] == 1){
                            
                            // Proposal 
                            $this->db->where('id', $proposal_id);
                            $this->db->update('proposal', ['premium' => $premium]);
                            if($this->db->affected_rows()){
                                echo $this->db->last_query().'<br>';
                            }
                            //employee policy member
                            $response[$key]['policy_mem_sum_premium'] = trim($premium);
                            $this->db->where('policy_member_id', $response[$key]['policy_member_id']);
                            $this->db->update('employee_policy_member', ['policy_mem_sum_premium' => $premium]);
                            if($this->db->affected_rows()){
                                echo $this->db->last_query().'<br>';
                            }
                            // Proposal Member
                            $this->db->where('policy_member_id', $response[$key]['policy_member_id']);
                            $this->db->update('proposal_member', ['policy_mem_sum_premium' => $premium]);
                            if($this->db->affected_rows()){
                                echo $this->db->last_query().'<br>';
                            }
                            
                        }else if($response[$key]['policy_sub_type_id'] == 2){
                            // Proposal 
                            $this->db->where('id', $proposal_id);
                            $this->db->update('proposal', ['premium' => $premium]);
                            if($this->db->affected_rows()){
                                echo $this->db->last_query().'<br>';
                            }
                            $familyConstructs = explode('+',$response[0]['familyConstruct'])[0];
                            //echo $familyConstructs;
                            if($familyConstructs == '2A')
                            {
                                $premium_gpa = $premium/2;
                            }else{
                                $premium_gpa = $premium;
                            }
                            $response[$key]['policy_mem_sum_premium'] = trim($premium_gpa);
                            $this->db->where('policy_member_id', $response[$key]['policy_member_id']);
                            $this->db->update('employee_policy_member', ['policy_mem_sum_premium' => $premium_gpa]);
                            if($this->db->affected_rows()){
                                echo $this->db->last_query().'<br>';
                            }
                            // Proposal Member
                            $this->db->where('policy_member_id', $response[$key]['policy_member_id']);
                            $this->db->update('proposal_member', ['policy_mem_sum_premium' => $premium]);
                            if($this->db->affected_rows()){
                                echo $this->db->last_query().'<br>';
                            }
                        }
                    }
                    //print_r($response);exit;
                }
            }
        }
       // exit;
    }
    public function update_premium_script_bk(){
        //$leads = array('413723620');//
        foreach ($leads as $key => $value) {
            $emp_data = $this->db->query("SELECT emp_id,deductable from employee_details WHERE lead_id = '".$value."'")->row_array();
//            print_pre($emp_data);exit;
	    $emp_id = $emp_data['emp_id'];
            $deductable = $emp_data['deductable'];
            $q = "SELECT policy_detail_id,id from proposal where emp_id = '".$emp_id."' AND status = 'Payment Received'";
            //echo $q;//exit;
            $fail_data = $this->db->query($q)->result_array();
 print_pre($fail_data);//exit;          
            foreach ($fail_data as $key => $pid) {
                $policy_detail_id = $pid['policy_detail_id'];
                $proposal_id = $pid['id'];
                $response = $this->db
                    ->query('SELECT epd.policy_detail_id,epd.policy_sub_type_id,epm.family_relation_id,epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,"self" AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
                        epm.policy_mem_gender AS "gender",epm.policy_mem_dob AS "dob", epm.age AS "age",epm.age_type AS "age_type","R001" as relation_code
                        FROM 
                        employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_details AS ed
                        WHERE epd.policy_detail_id = epm.policy_detail_id
                        AND epm.family_relation_id = fr.family_relation_id
                        AND fr.family_id = 0
                        AND fr.emp_id = ed.emp_id
                        AND ed.emp_id = ' . $emp_id . '
                        AND `epd`.`policy_detail_id` = ' . $policy_detail_id . ' UNION all SELECT epd.policy_detail_id,epd.policy_sub_type_id,epm.family_relation_id, epm.policy_mem_sum_premium,epd.suminsured_type,epm.policy_mem_sum_insured,epm.policy_member_id,mfr.fr_name AS "relationship",epm.policy_member_first_name AS "firstname", epm.policy_member_last_name AS "lastname",epm.fr_id as "fr_id",epm.familyConstruct as "familyConstruct",
                        epm.policy_mem_gender AS "gender", epm.policy_mem_dob AS "dob",epm.age AS "age",epm.age_type AS "age_type",mfr.relation_code
                        FROM 
                        employee_policy_detail AS epd,employee_policy_member AS epm,family_relation AS fr,employee_family_details AS efd,
                        master_family_relation AS mfr
                        WHERE epd.policy_detail_id = epm.policy_detail_id
                        AND epm.family_relation_id = fr.family_relation_id
                        AND fr.family_id = efd.family_id 
                        AND efd.fr_id = mfr.fr_id
                        AND fr.emp_id = ' . $emp_id . '
                        AND `epd`.`policy_detail_id` = ' . $policy_detail_id)->result_array();
                if($response[0]['suminsured_type'] == 'family_construct_age'){
                    $change_premium = false;
                    print_pre($response);//exit;
                    foreach($response as $value){
                        if($value['age_type'] == 'days'){
                            $age[] = 0;
                        }else{
                            $age[] = $value['age'];
                        }
                                            
                    }
                    //print_pre($age);exit;
                    if($policy_detail_id == HEALTHPROXL_GHI_ST){
                       $check = $this->db->select("*")
                                ->from("family_construct_age_wise_si")
                                ->where("sum_insured", $response[0]['policy_mem_sum_insured'])
                                ->where("family_type", $response[0]['familyConstruct'])
                                ->where("policy_detail_id", $policy_detail_id)
                                ->where("deductable", $deductable)
                                ->get()
                                ->result_array(); 
                    }else{
                        $check = $this->db->select("*")
                                ->from("family_construct_age_wise_si")
                                ->where("sum_insured", $response[0]['policy_mem_sum_insured'])
                                ->where("family_type", $response[0]['familyConstruct'])
                                ->where("policy_detail_id", $policy_detail_id)
                                ->get()
                                ->result_array(); 

                    }           
                   // echo $this->db->last_query();exit;
                    $max_age = max($age);
                    foreach($check as $value){
                        $min_max_age = explode("-",$value['age_group']);
                        if($max_age >= $min_max_age[0] && $max_age <= $min_max_age[1]){
                            $premium = $value['PremiumServiceTax'];
                        }
                    }
                    //echo $this->db->last_query();$premium;exit;
                    foreach($response as $key => $value1){
                        //echo $premium.'<br>';
                        
                        if($response[$key]['policy_sub_type_id'] == 1){
                            
                            // Proposal 
                            $this->db->where('id', $proposal_id);
                            $this->db->update('proposal', ['premium' => $premium]);
                            //employee policy member
                            $response[$key]['policy_mem_sum_premium'] = trim($premium);
                            $this->db->where('policy_member_id', $response[$key]['policy_member_id']);
                            $this->db->update('employee_policy_member', ['policy_mem_sum_premium' => $premium]);
                            // Proposal Member
                            $this->db->where('policy_member_id', $response[$key]['policy_member_id']);
                            $this->db->update('proposal_member', ['policy_mem_sum_premium' => $premium]);
                            
                        }else if($response[$key]['policy_sub_type_id'] == 2){
                            // Proposal 
                            $this->db->where('id', $proposal_id);
                            $this->db->update('proposal', ['premium' => $premium]);
                            $familyConstructs = explode('+',$response[0]['familyConstruct'])[0];
                            //echo $familyConstructs;
                            if($familyConstructs == '2A')
                            {
                                $premium_gpa = $premium/2;
                            }else{
                                $premium_gpa = $premium;
                            }
                            $response[$key]['policy_mem_sum_premium'] = trim($premium_gpa);
                            $this->db->where('policy_member_id', $response[$key]['policy_member_id']);
                            $this->db->update('employee_policy_member', ['policy_mem_sum_premium' => $premium_gpa]);
                            // Proposal Member
                            $this->db->where('policy_member_id', $response[$key]['policy_member_id']);
                            $this->db->update('proposal_member', ['policy_mem_sum_premium' => $premium]);
                        }
                    }
                    //print_r($response);exit;
                }
            }
        }
       // exit;
    }

	public function acknowledgement_download()
    {
        echo json_encode($this
            ->obj_api
            ->acknowledgement_download_m());
    }
	
	public function coi_download() 
	{
		echo json_encode($this->obj_api->coi_download_m());
	}

	public function redirect_url_check() 
	{
		echo json_encode($this->obj_api->redirect_url_check_m());
	}
	
	public function redirect_url_send()
	{
		$this->obj_api->redirect_url_send_m();
		echo json_encode(['status' =>'success']);
	}
	
	/* cron */
	public function update_payu_rejected()
	{
		$this->obj_api->update_payu_rejected_m();
	}
	
	/* cron */
	public function emandate_enquiry_HB_call()
	{
		$this->obj_api->emandate_enquiry_HB_call_m();
		echo json_encode(['status' =>'success']);
	}
	
	public function check_error_data()
	{
        $res = $this->db->get_where("employee_details",array("emp_id" => $_POST["emp_id"]))->row_array()["is_non_integrated_single_journey"];
        if($res['is_non_integrated_single_journey'] == 1){
            echo json_encode($this->obj_api->check_error_data_m_rpay()); 
        }else{
            echo json_encode($this->obj_api->check_error_data_m()); 
        }
		
	}

    public function payment_success_view_rpay($emp_id_encrypt)
    {
         $emp_id = encrypt_decrypt_password($emp_id_encrypt, 'D');

        $encrypted = $this
            ->input
            ->post('RESPONSE');

        if ($encrypted)
        {
            $decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);
            $post_data = json_decode($decrypted, true);

            extract($post_data);

            if ($TxStatus && ($TxMsg == 'Payment Received' || $TxMsg == 'success') && $PaymentStatus == 'PR')
            {

               

                $TxStatus = "success";
                $TxMsg = "No Error";
            }
        }

        $query = $this
            ->db
            ->query("SELECT GROUP_CONCAT(p.id) proposal_id,ed.emp_id,ed.lead_id,mpst.product_code FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,payment_details AS pd,user_payu_activity as ua WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND p.id = pd.proposal_id AND ed.emp_id = ua.emp_id and (TIMESTAMPDIFF(SECOND, ua.created_time,now()) < 259200) AND ed.emp_id='" . $emp_id . "' GROUP BY p.emp_id")->row_array();
            
    
        if (!empty($query['proposal_id']))
        {

            $ids = explode(',', $query['proposal_id']);

            if (isset($TxRefNo))
            {
                $request_arr = ["lead_id" => $query['lead_id'], "req" => $encrypted, "res" => $decrypted, "product_id" => $query['product_code'], "type" => "payment_response_post"];

                $dataArray['tablename'] = 'logs_docs';
                $dataArray['data'] = $request_arr;
                $this
                    ->Logs_m
                    ->insertLogs($dataArray);

                $request_arr = ["payment_status" => $TxMsg, "premium_amount" => $amount, "payment_type" => $paymentMode, "txndate" => $txnDateTime, "TxRefNo" => $TxRefNo, "TxStatus" => $TxStatus, "json_quote_payment" => json_encode($post_data) ];

                $this
                    ->db
                    ->where_in('proposal_id', $ids);
                $this
                    ->db
                    ->where('TxStatus != ', 'success');
                $this
                    ->db
                    ->update("payment_details", $request_arr);

            }
        

            if (isset($Registrationmode))
            {
                $query_emandate = $this
                    ->db
                    ->query("select * from emandate_data where lead_id='" . $query['lead_id']."' ")->row_array();

                if ($EMandateStatus == 'MS')
                {
                    $mandate_status = 'Success';
                }
                elseif ($EMandateStatus == 'MI')
                {
                    $mandate_status = 'Emandate Pending';
                }
                elseif ($EMandateStatus == 'MR')
                {
                    $mandate_status = 'Emandate Received';
                }
                elseif ($EMandateStatus == '')
                {
                    $mandate_status = 'Emandate Pending';
                }
                else
                {
                    $mandate_status = 'Fail';
                }

                if ($query_emandate > 0)
                {

                    $arr = ["TRN" => $EMandateRefno, "status_desc" => $EMandateStatusDesc, "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($EMandateDate)) , "Registrationmode" => $Registrationmode, "EMandateFailureReason" => $EMandateFailureReason, "MandateLink" => $MandateLink];

                    $this
                        ->db
                        ->where("lead_id", $query['lead_id']);
                    $this
                        ->db
                        ->update("emandate_data", $arr);
                    
                }
                else
                {

                    $arr = ["lead_id" => $query['lead_id'], "TRN" => $EMandateRefno, "status_desc" => $EMandateStatusDesc, "status" => $mandate_status, "mandate_date" => date('Y-m-d h:i:s', strtotime($EMandateDate)) , "Registrationmode" => $Registrationmode, "EMandateFailureReason" => $EMandateFailureReason, "MandateLink" => $MandateLink];

                    $this
                        ->db
                        ->insert("emandate_data", $arr);
                }

                if ($mandate_status == 'Success')
                {
                    //$this->obj_api->send_message($query['lead_id'], 'success');
                        //echo 3456;die;
                }

                if ($mandate_status == 'Fail')
                {
                    $this
                        ->obj_api
                        ->send_message($query['lead_id'], 'fail');
                }

                if ($paymentMode == 'PP' && ($Registrationmode == 'SAD' || $Registrationmode == 'EMI'))
                {
                    $this
                        ->obj_api
                        ->send_message($query['lead_id'], 'SAD_EMI_one');
                    $this
                        ->obj_api
                        ->send_message($query['lead_id'], 'SAD_EMI_two');
                }

            }

            if (isset($PaymentStatus) && $PaymentStatus == 'PI')
            {
                // echo 987;die;
                $check_pg = $this
                    ->obj_api
                    ->real_pg_check($query['lead_id']);
                if ($check_pg)
                {
                    redirect(base_url("payment_success_view_call_axis/" . $emp_id_encrypt));
                }
                else
                {
                    // echo 1234;die;

                    $arr_update = ["is_payment_initiated" => 1];

                    $this
                        ->db
                        ->where("lead_id", $query['lead_id']);
                    $this
                        ->db
                        ->update("employee_details", $arr_update);

                    echo "Response on payment status is pending. Post payment confirmation, proposal will be initiated. Thanks !!";
                    exit;
                }
            }

            $proposal_id = $ids[0];

            $payment_data = $this
                ->db
                ->query("select payment_status,TxStatus from payment_details where proposal_id='$proposal_id'")->row_array();

            if ($payment_data['TxStatus'] == 'success')
            {

                $check_result = $this
                    ->obj_api
                    ->policy_creation_call($query['lead_id']);

                if ($check_result['Status'] == 'Success')
                {  

                    $query_lead = $this->db->query("select lead_id from employee_details where emp_id='$emp_id'")->row_array();

                    $lead_id_mis = $query_lead['lead_id'];
    
                    $query_check_mis = $this->db->query("select id from mis_ack_coi where lead_id='$lead_id_mis'")->row_array();
    
                    if(empty($query_check_mis)){
                        $insert_mis_coi_table = $this->obj_home->save_ack_mis($query_lead['lead_id']);
                    }
                    

                    $data_policy[0] = $this
                        ->db
                        ->query("SELECT GROUP_CONCAT(DISTINCT(certificate_number)) certificate_number FROM api_proposal_response m WHERE m.emp_id = '$emp_id' GROUP BY emp_id")->row_array();

                    $data = $this
                        ->db
                        ->query("select ed.AXISBANKACCOUNT,mpst.product_name,p.proposal_no,ed.lead_id,pd.txndate,ed.emp_firstname from employee_policy_detail AS epd,product_master_with_subtype AS mpst,proposal as p,employee_details as ed,payment_details as pd  where epd.product_name = mpst.id and mpst.policy_subtype_id = epd.policy_sub_type_id and p.policy_detail_id=epd.policy_detail_id and p.emp_id = ed.emp_id and p.id = pd.proposal_id and ed.emp_id ='$emp_id'")->result_array();

                    $MandateLink_data = $this
                        ->db
                        ->query("select MandateLink,Registrationmode from emandate_data where lead_id = '" . $query['lead_id'] . "'")->row_array();
                        
                    $is_cust_journey = 1;

                    $this
                        ->load
                        ->employee_template_api("thankyou", compact('data_policy', 'data', 'MandateLink_data','is_cust_journey'));

                }
                else
                {

                    redirect(base_url("/payment_error_view_call_axis/" . $emp_id_encrypt));

                }

            }
            else
            {

                $data = $this
                    ->db
                    ->query("select p.proposal_no,ed.lead_id,ed.emp_firstname from proposal as p,employee_details as ed  where p.emp_id = ed.emp_id and ed.emp_id ='$emp_id'")->result_array();

                $this
                    ->load
                    ->employee_template_api("thankyou", compact('data'));
            }

        }
        else
        {

            echo "Payment link has been expired, Please get in touch with your Branch RM";

        }

    }
	public function policy_generate_view()
	{
		$this->load->employee_template_api('policy_generate_view');
		
	}
	public function save_policy_generate()
	{
	
		$lead_id = $this->input->post('lead_id');
		$txt_ref_no = $this->input->post('txt_ref_no');
		$premium = $this->input->post('premium');
		$txndate = $this->input->post('txndate');

		$emp_data = $this->db->query("select emp_id from employee_details where lead_id = '$lead_id'")->row_array();
		$emp_id = $emp_data['emp_id'];
		$proposal_update = $this->db->query("update proposal set status = 'Payment Received' where emp_id ='$emp_id'");

		$proposal_data = $this->db->query("select proposal_id from proposal where emp_id = '$emp_id'")->result_array();
		if(!empty($proposal_data)){
		foreach($proposal_data as $val){
		$proposal_id = $val['proposal_id'];

		$update = $this->db->query("update payment_details set TxRefNo = '$txt_ref_no',premium_amount = '$premium',txndate = '$txndate',payment_status = 'No Error',TxStatus = 'success' where proposal_id = '$proposal_id'");
		if($update)
		{
 $query1 = $this->db->query("SELECT DISTINCT e.lead_id,e.emp_id,e.product_id from proposal p,employee_details e,payment_details as pd WHERE p.emp_id = e.emp_id and p.id = pd.proposal_id and pd.payment_status = 'No Error' and p.status IN('Payment Received') and p.EasyPay_PayU_status = 1 and e.product_id in('R03','R07','R11') and  e.lead_id = '$lead_id'")->result_array();
		
		    if($query1)
                                {
                                        foreach($query1 as $val)
                                        {
                                                sleep(30);
                                                $where_arr = ["emp_id"=>$val['emp_id'],"status"=>"Payment Received"];
                                                $arr = ["count" => 0];
                                                $this->db->where($where_arr);
                                                $this->db->update("proposal",$arr);
                                                $where_arr_emp = ["emp_id"=>$val['emp_id']];
                                                $employee_arr = ["is_policy_issue_initiated" => 0];
                                                $this->db->where($where_arr_emp);
                                                $this->db->update("employee_details",$employee_arr);
                                                $data = $this->obj_api->policy_creation_call($val['lead_id'], 1);
                                                //$data = json_decode($check_result,true);

                                                $request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($data),"res" => json_encode($data)
 ,"product_id"=> $val['product_id'], "type"=>"8clock_cron"];
                                                $dataArray['tablename'] = 'logs_docs';
                                                $dataArray['data'] = $request_arr;
                                                $this->Logs_m->insertLogs($dataArray);

                                                //echo $data['Status']." hii".$val['lead_id'];

                                        }

                                }
		
		
		
		}
		}
		}
	}
	public function payment_error_view($emp_id_encrypt)
	{
			
		$emp_id = encrypt_decrypt_password($emp_id_encrypt,'D');
		$lead_arr = $this->db->query("select lead_id,email from employee_details where emp_id = '$emp_id' ")->row_array();
		$lead_id = $lead_arr['lead_id'];
		$email = $lead_arr['email'];
		
		$this->load->employee_template_api('payment_error_view',compact('emp_id','lead_id','email'));
		
	}

     public function payment_redirect_view_rpay($emp_id_encrypt)
    {
        //echo 111;exit;
        $emp_id = encrypt_decrypt_password($emp_id_encrypt, 'D');

        if ($emp_id)
        {
            //Dedupe - sonal
            $query_lead = $this
            ->db
            ->query('SELECT emp_id
    FROM employee_details AS ed
    where ed.emp_id = "' . $emp_id . '"
    AND ed.lead_status = "Rejected"');
        if ($query_lead->num_rows() > 0)
        {
                        echo "Lead is Rejected.Please continue journey with Fresh Lead ";
                                                                exit;
                }
            //Quote Expired - sonal
        $quote_exp_check = common_quote_expired_bb($emp_id);
                if($quote_exp_check['status'] == 1){
                  echo   $quote_exp_check['msg'];exit;
                }


            /*//AFPP policy expired.Payment has to be stopped.(AFPP plus payment allow)
            $new_check = $this->db->query("SELECT ed.lead_id,p.created_date FROM employee_details AS ed,proposal as p WHERE ed.emp_id = p.emp_id and date(p.created_date) < '2020-12-16' and ed.product_id = 'R03' and p.status = 'Payment Pending' and ed.emp_id= '".$emp_id."' GROUP BY p.emp_id")->row_array();
            
            if(!empty($new_check['lead_id']))
            {
            echo "Please get in touch with your branch / RM for new version of AFPP plan.";exit;
            }*/

            //replaced cust_id with unique_ref_no on 15-05-2021
            $query = $this
                ->db
                ->query("SELECT ed.is_non_integrated_single_journey,p.sum_insured,ed.emp_firstname,ed.emp_lastname,ed.lead_id,ed.emp_id,epd.policy_detail_id,mpst.payment_source_code,ed.email,ed.mob_no,sum(p.premium) as premium,mpst.payment_url,p.status,mpst.product_code,ed.json_qote,ed.unique_ref_no,ed.cust_id FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,payment_details as pd,user_payu_activity as ua WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND p.id = pd.proposal_id AND  ed.emp_id = ua.emp_id and (TIMESTAMPDIFF(SECOND, ua.created_time,now()) < 259200) AND ed.emp_id= '" . $emp_id . "' GROUP BY p.emp_id")->row_array();
            //echo $this->db->last_query();exit;
            if (!empty($query))
            {

                //commented by upendra for updated dedupe logic - 10-05-2021
                // $already_cust_id = $this
                //     ->db
                //     ->query("select ed.lead_id from employee_details as ed,proposal as p where ed.emp_id = p.emp_id and p.status in('Success','Payment Received') and ed.cust_id = '" . $query['cust_id'] . "' and ed.product_id = '" . $query['product_code'] . "' and ed.lead_id != '" . $query['lead_id'] . "' group by p.emp_id")->num_rows();

                    //pm.familyConst 2A,2A+1k,2A+2K //end journey
                    //pm.familyConstruct 1A+1K /1A/1A+2K // self / spouse

                    // $already_cust_id = $this
                    // ->db
                    // ->query("select ed.lead_id,epm.familyConstruct,p.sum_insured from employee_details as ed,proposal 
                    // AS p, family_relation as fr, master_family_relation as mfr,employee_policy_member as epm 
                    // where ed.emp_id = p.emp_id and p.status in('Success','Payment Received') and ed.cust_id = '" . $query['cust_id'] . "' and ed.product_id = '" . $query['product_code'] . "' and ed.lead_id != '" . $query['lead_id'] . "' group by p.emp_id")->row_array();
                
                    //replaced cust_id with unique_ref_no on 15-05-2021


                    //dedupe logic
                    $check_dedupe = common_function_ref_id_exist($query['lead_id']);
                    if($check_dedupe['status'] == 'error'){
                        echo $check_dedupe['msg']; 
                        exit;
                    }


                    if ($query['status'] != 'Payment Pending')
                    {
                        redirect(base_url("payment_success_view_call_axis_rpay/" . $emp_id_encrypt));
                    }
                    else
                    {

                        $lead_data = $this
                            ->obj_api
                            ->get_all_quote_call($query['emp_id']);
                      //print_pre($lead_data);exit;  
                        if ($lead_data['status'] == 'Success')
                        {

                            $check_pg = $this
                                ->obj_api
                                ->real_pg_check($query['lead_id']);
//print_pre($check_pg);exit;
                            if ($check_pg)
                            {
                                redirect(base_url("payment_success_view_call_axis_rpay/" . $emp_id_encrypt));
                            }
                            else
                            {
//echo 22;exit;
                                $ProductInfo = '';
                                
                                $Source = "AX";
                                $Vertical = "AXBBGRP";
                                if($query['is_non_integrated_single_journey'] == 1){//ankita single link journey changes
                                    $PaymentMode = "PO";
                                    $Vertical = "AXSLGRP";
                                }else{
                                    $PaymentMode = "PP";
                                }
                                $ReturnURL = base_url("payment_success_view_call_axis_rpay/" . $emp_id_encrypt);
                                $UniqueIdentifier = "LEADID";
                                $UniqueIdentifierValue = $query['lead_id'];
                                $CustomerName = $query['emp_firstname']." ".$query['emp_lastname'];
                                $Email = $query['email'];
                                $PhoneNo = substr(trim($query['mob_no']), -10);
                                $FinalPremium = round($query['premium'],2);
                                
                                if ($query['product_code'] == 'R03')
                                {
                                    $ProductInfo = 'Group Activ Health and Group Protect';
                                }
                                else if ($query['product_code'] == 'R07')
                                {
                                    $ProductInfo = 'Group Activ Health and Group Activ Secure';
                                }
                                else if ($query['product_code'] == 'R10')
                                {
                                    $ProductInfo = 'Group Activ Secure';
                                }
                                else if ($query['product_code'] == 'R11')
                                {         $ProductInfo="Group Activ Health";
                                         $super_topup=$this->db->query("select policy_detail_id from proposal where emp_id = '$emp_id'")->result_array();

                                        if( $query['is_non_integrated_single_journey'] == 1 &&  count($super_topup)>1){
                                                $ProductInfo="Group Activ Health and Group Activ Secure";
                                        }

                                   /* if($query['is_non_integrated_single_journey'] == 1){//ankita single link journey changes
                                        $ProductInfo = 'Group Active Health';
                                    }else{
                                        $ProductInfo = 'Health Pro Infinity';
                                    }*/

                                }


                                $CKS_data = $Source."|".$Vertical."|".$PaymentMode."|".$ReturnURL."|".$UniqueIdentifier."|".$UniqueIdentifierValue."|".$CustomerName."|".$Email."|".$PhoneNo."|".$FinalPremium."|".$ProductInfo."|".$this->hash_key;

                                $CKS_value = hash($this->hashMethod, $CKS_data);

                                $bank_data = json_decode($query['json_qote'], true);

                                $manDateInfo = array(
                                    "ApplicationNo" => $UniqueIdentifierValue,
                                    "AccountHolderName" => $CustomerName,
                                    "BankName" => ($bank_data['AXISBANKACCOUNT'] == 'Y') ? 'Axis Bank' : 'Other',
                                    "AccountNumber" => empty($bank_data['ACCOUNTNUMBER']) ? '' : $bank_data['ACCOUNTNUMBER'],
                                    "AccountType" => null,
                                    "BankBranchName" => empty($bank_data['BRANCH_NAME']) ? '' : $bank_data['BRANCH_NAME'],
                                    "MICRNo" => null,
                                    "IFSC_Code" => empty($bank_data['IFSCCODE']) ? '' : $bank_data['IFSCCODE'],
                                    "Frequency" => "As and when presented"//commented on - 6/4/21(as per CR by ABHI) "ANNUALLY"
                                );

                                $dataPost = array(
                                    "signature"=> $CKS_value,
                                    "Source"=> $Source,
                                    "Vertical"=> $Vertical,
                                    "PaymentMode"=> $PaymentMode,
                                    "ReturnURL"=> $ReturnURL,
                                    "UniqueIdentifier" => $UniqueIdentifier,
                                    "UniqueIdentifierValue" => $UniqueIdentifierValue,
                                    "CustomerName"=> $CustomerName,
                                    "Email"=> $Email,
                                    "PhoneNo"=> $PhoneNo,
                                    "FinalPremium"=> $FinalPremium,
                                    "ProductInfo"=> $ProductInfo,
                                    //"Additionalfield1"=> "",
                                    "MandateInfo"=>$manDateInfo 
                                    );

                                $data_string = json_encode($dataPost);

                                $encrypted = openssl_encrypt($data_string, $this->algoMethod, $this->encrypt_key, 0);
                                $decrypted = openssl_decrypt($encrypted, $this->algoMethod, $this->encrypt_key, 0);

                                $url = "https://pg_uat.adityabirlahealth.com/pgmandate/service/home/sourcelanding";
                                $data = array(
                                    'REQUEST' => $encrypted
                                );

                                $c = curl_init();
                                curl_setopt($c, CURLOPT_URL, $url);
                                curl_setopt($c, CURLOPT_POST, 0);
                                curl_setopt($c, CURLOPT_POSTFIELDS, $data);
                                curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
                                curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
                                curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);

                                $result = curl_exec($c);
                                curl_close($c);
                                $result = json_decode($result, true);

                                $request_arr = ["lead_id" => $query['lead_id'], "req" => "ecrypt-" . json_encode($data) . "decrypt-" . $decrypted, "res" => json_encode($result) , "product_id" => $query['product_code'], "type" => "payment_request_post"];

                                $dataArray['tablename'] = 'logs_docs';
                                $dataArray['data'] = $request_arr;
                                $this
                                    ->Logs_m
                                    ->insertLogs($dataArray);

                                if ($result && $result['Status'])
                                {

                                    $query_check = $this
                                        ->db
                                        ->query("select * from payment_txt_ids where lead_id='" . $query['lead_id'] . "'")->row_array();

                                    if (empty($query_check))
                                    {
                                        $data_arr = ["lead_id" => $query['lead_id'], "txt_id" => 1, "pg_type" => "New"];
                                        $this
                                            ->db
                                            ->insert("payment_txt_ids", $data_arr);
                                    }
                                    else
                                    {
                                        $update_arr = ["cron_count" => 0];
                                        $this
                                            ->db
                                            ->where("lead_id", $query['lead_id']);
                                        $this
                                            ->db
                                            ->update("payment_txt_ids", $update_arr);
                                    }

                                    //echo "WELCOME To ABHI";
                                    //$a='http://'.$result['PaymentLink'];
                                    //$var = $result['PaymentLink'];

                                    /*if(strpos($var, 'http://') !== 0) {
                                       redirect('http://' . $var,refresh);
                                    } 
                                    else{
                                    redirect($var,refresh);
                                    }*/
                                    redirect($result['PaymentLink']);
                                    
                                }
                                else
                                {
                                    if ($result['ErrorList'][0]['ErrorCode'] == 'E005')
                                    {
                                        //Payment already received - E005
                                        $check_pg = $this
                                            ->obj_api
                                            ->real_pg_check($query['lead_id']);
                                        if ($check_pg)
                                        {
                                            redirect(base_url("payment_success_view_call_axis_rpay/" . $emp_id_encrypt));
                                        }
                                        else
                                        {
                                            echo "Response on payment status is received. Post payment confirmation, proposal will be initiated. Thanks !! Error in enquiry API";
                                            exit;
                                        }
                                    }
                                    else if ($result['ErrorList'][0]['ErrorCode'] == 'E006')
                                    {
                                        //Payment initiated - E006
                                        $check_pg = $this
                                            ->obj_api
                                            ->real_pg_check($query['lead_id']);
                                        if ($check_pg)
                                        {
                                            redirect(base_url("payment_success_view_call_axis_rpay/" . $emp_id_encrypt));
                                        }
                                        else
                                        {

                                            $arr_update = ["is_payment_initiated" => 1];

                                            $this
                                                ->db
                                                ->where("lead_id", $query['lead_id']);
                                            $this
                                                ->db
                                                ->update("employee_details", $arr_update);

                                            echo "Response on payment status is pending. Post payment confirmation, proposal will be initiated. Thanks !!";
                                            exit;
                                        }
                                    }
                                    else
                                    {
                                        echo $result['ErrorList'][0]['Message'];
                                    }

                                }
                            }

                        }
                        else
                        {
                            redirect(base_url("/payment_error_view_call_axis/" . $emp_id_encrypt));

                        }

                    }

                
                // else
                // {
                //     echo "This policy cannot be processed since the said proposer has already purchased this policy";
                // }

            }
            else
            {
                echo "Payment link has been expired, Please get in touch with your Branch RM";
            }

        }
    }
	
	public function payment_redirect_view($emp_id_encrypt) {
		$emp_id = encrypt_decrypt_password($emp_id_encrypt,'D');
		if($emp_id){
				//Dedupe - sonal
			$query_lead = $this
            ->db
            ->query('SELECT emp_id
    FROM employee_details AS ed
    where ed.emp_id = "' . $emp_id . '"
    AND ed.lead_status = "Rejected"');
        if ($query_lead->num_rows() > 0)
        {
                        echo "Lead is Rejected.Please continue journey with Fresh Lead ";
                                                                exit;
                }
//Quote Expired - sonal
      $quote_exp_check = common_quote_expired_bb($emp_id);
                if($quote_exp_check['status'] == 1){
                  echo   $quote_exp_check['msg'];exit;
                }
			//AFPP policy expired.Payment has to be stopped.(AFPP plus payment allow) 
			$new_check = $this->db->query("SELECT ed.lead_id,p.created_date,ed.is_non_integrated_single_journey FROM employee_details AS ed,proposal as p WHERE ed.emp_id = p.emp_id and date(p.created_date) < '2020-12-16' and ed.product_id = 'R03' and p.status = 'Payment Pending' and ed.emp_id= '".$emp_id."' GROUP BY p.emp_id")->row_array();
			
			if(!empty($new_check['lead_id']))
			{
				echo "Please get in touch with your branch / RM for new version of AFPP plan.";exit;
			}



			$lead_arr = $this->db->query("select ed.is_non_integrated_single_journey,ed.lead_id,ed.emp_firstname,ed.emp_lastname,ed.mob_no,sum(p.premium) as premium,ed.cust_id,ed.product_id from employee_details as ed,proposal as p,user_payu_activity as ua where ed.emp_id = p.emp_id and ed.emp_id = ua.emp_id and (TIMESTAMPDIFF(SECOND, ua.created_time,now()) < 259200) and ed.emp_id = '$emp_id' group by p.emp_id")->row_array();
            if($lead_arr['is_non_integrated_single_journey'] == 1){
                $this->payment_redirect_view_rpay($emp_id_encrypt);
            }else{
			
                if(!empty($lead_arr)){
    				
    				/*
    				$already_cust_id = $this->db->query("select ed.lead_id from employee_details as ed,proposal as p where ed.emp_id = p.emp_id and p.status in('Success','Payment Received') and ed.cust_id = '".$lead_arr['cust_id']."' and ed.product_id = '".$lead_arr['product_id']."' and ed.lead_id != '".$lead_arr['lead_id']."' group by p.emp_id")->num_rows();
    				*/

    				/*if($already_cust_id == 0){*/
    				 //dedupe logic
                        		$check_dedupe = common_function_ref_id_exist($lead_arr['lead_id']);
                        		if($check_dedupe['status'] == 'error'){
                            		echo $check_dedupe['msg']; 
                            		exit;
                                    }				
    	
    				$data['lead_id'] = $lead_arr['lead_id'];
    				$data['name'] = $lead_arr['emp_firstname']." ".$lead_arr['emp_lastname'];
    				$data['mob_no'] = $lead_arr['mob_no'];
    				$data['premium'] = round($lead_arr['premium'],2);
    				
    				$this->load->employee_template_api('payment_redirect_view',compact('emp_id','data'));
    				
    				/*}*/
    				
    				/*
    				else{
    					echo "This policy cannot be processed since the said proposer has already purchased this policy";
    				}
    				*/
    				
    			}else{
    				
    				echo "Payment link has been expired, Please get in touch with your Branch RM";
    			}
            }
			
		}
	}
	
	public function payment_redirection($emp_id_encrypt)
	{
        //echo 11;exit;
		extract($this->input->post(null, true));	
		
		$emp_id = encrypt_decrypt_password($emp_id_encrypt,'D');
        $query_lead = $this->db->query('SELECT emp_id FROM employee_details AS ed where ed.emp_id = "' . $emp_id . '" AND ed.lead_status = "Rejected"');
        if ($query_lead->num_rows() > 0)
        {
                        echo "Lead is Rejected.Please continue journey with Fresh Lead ";
                                                                exit;
                }
		$query = $this
		->db
		->query("SELECT ed.lead_id,ed.emp_id,epd.policy_detail_id,mpst.payment_source_code,ed.email,ed.mob_no,sum(p.premium) as premium,mpst.payment_url,p.status,mpst.product_code FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,user_payu_activity as ua WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND ed.emp_id = ua.emp_id and (TIMESTAMPDIFF(SECOND, ua.created_time,now()) < 259200) AND ed.emp_id=".$emp_id)->row_array();

        
                if(!empty($query))
                {
                 //print_pre($query);exit;   
                    if($query['status'] != 'Payment Pending'){
                        redirect("payment_success_view_call_axis/".$emp_id_encrypt);
                    }else{
                        
                        $query_data = $this->db->query("select * from ghi_quick_quote_response where emp_id='$emp_id' and status = 'success'")->row_array();
                        
                        $lead_data = $this->obj_api->get_all_quote_call($query['emp_id']);
                        
                        if($lead_data['status'] == 'Success'){
                            
                            $digitrandom = $this->getRandomUniqueString(10);
                            
                            $url = $query['payment_url'];
                            // for testing purpose
                            //$query['payment_source_code'] = 'AXDC0070';
                            $data = array(
                                "Email"=>$query['email'],
                                "PhoneNo"=>substr(trim($query['mob_no']), -10),
                                "SourceCode"=>$query['payment_source_code'],
                                "OrderAmount"=>round($query['premium'],2),
                                "Currency"=>"INR",
                                "secSignature"=>"fed47b72baebd4f5f98a3536b8537dc4e17f60beeb98c77c97dadc917004b3bb",
                                "ReturnURL"=> base_url("payment_success_view_call_axis/".$emp_id_encrypt),
                                "QuoteId"=>$lead_data['msg'],
                                "SubCode"=>"",
                                "GrossPremium"  =>round($query['premium'],2),
                                "FinalPremium"=>round($query['premium'],2),
                                "SourceTxnId"=>"AXFP".$digitrandom.$query['lead_id'],
                                "ProductInfo"=>"Axis_".$query['lead_id']
                            );

                            /* new payment gateway common landing url start*/
                            $redirect_to_commonpgnew = TRUE; //FALSE; TRUE;
                            if($redirect_to_commonpgnew){
                                
                                $data['secSignature'] = '';
                                $data['product_code'] = $query['product_code'];
                                $data['url'] = $url;
                                $data = array_map( 'trim', $data ); //to trim the array element 
                                $this->payment_redirect_view_rpay($emp_id_encrypt);
                               // $this->callPGCommonSourceLanding($query['lead_id'],$data);  
                            }else{
                                
                                $request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($data) ,"product_id"=> $query['product_code'], "type"=>"payment_request_post"];
                                
                                $dataArray['tablename'] = 'logs_docs'; 
                                $dataArray['data'] = $request_arr; 
                                $this->Logs_m->insertLogs($dataArray);
                                
                                $this->load->employee_template_api('payment_hidden_submit',compact('url','data'));
                            }
                            /* new payment gateway common landing url end */
                            
                            
                        }else{
                            
                            redirect("/payment_error_view_call_axis/" . $emp_id_encrypt);
                            
                        }
                        
                    }
                    
                }else{
                    echo "Error in proposal create or proposal rejected";
                //redirect("/payment_error_view_call_axis/" . $emp_id_encrypt);
                    
                }
            
		
		
		
	}
	
	function getRandomUniqueString($length=10) {
		$randNumberLen= $length;
		$numberset =time(). 11111;
		if(strlen($numberset) == $randNumberLen){
			return $numberset;
		}else{
			if(strlen($numberset) < $randNumberLen){
                                //add if length not match
				$addRandom = $randNumberLen - strlen($numberset);
				$i = 1;
				$ramdom_number = mt_rand(1,9);
				do {
					$ramdom_number .= mt_rand(0, 9);
				} while(++$i < $addRandom);
				$numberset .= $ramdom_number;
			}else{
                                 //substract if length not match
				$substractRandom = strlen($numberset) - $randNumberLen;
				$numberset = substr($numberset,0,-$substractRandom);
			}

			return $numberset;
		}
	}	

	/* new payment gateway common landing url start*/
	public function callPGCommonSourceLanding($lead_id,$data){		
		$url = "https://pg_uat.adityabirlahealth.com/ABHI_PG_Integration/ABHICOMMONPGSourceLanding.aspx";					
		//$url = "https://pg-abhi.adityabirlahealth.com/ABHIPGIntegration/ABHICOMMONPGSourceLanding.aspx";	

		//$url = $data['url'];
		
		$checkSumValue = '';
		$payUCheckSumOrderFormat ='CURRENCY|EMAIL|FINALPREMIUM|GROSSPREMIUM|MOBILE|ORDERAMOUNT|QUOTEID|REDIRECTURL|RETURN_URL|SOURCECODE|SUBCODE|TXNID';
		
		$data['REDIRECTURL'] = $url;
		
		$aPayUOrderData = explode('|',$payUCheckSumOrderFormat);
		foreach($aPayUOrderData as $val){
			switch($val){
				
				case 'CURRENCY': $checkSumValue .= "INR"; break;
				case 'EMAIL': 
				if(!empty($data['Email'])){
					$checkSumValue .= "|".$data['Email'];
				}
				break;
				case 'FINALPREMIUM': 
				if(!empty($data['FinalPremium'])){
					$checkSumValue .= "|".$data['FinalPremium'];
				}
				break;
				case 'GROSSPREMIUM': 
				if(!empty($data['GrossPremium'])){
					$checkSumValue .= "|".$data['GrossPremium'];
				}
				break;
				case 'MOBILE': 
				if(!empty($data['PhoneNo'])){
					$checkSumValue .= "|".$data['PhoneNo'];
				}					
				break;
				case 'ORDERAMOUNT': 
				if(!empty($data['OrderAmount'])){
					$checkSumValue .= "|".$data['OrderAmount'];
				}
				break;				
				case 'QUOTEID': 
				if(!empty($data['QuoteId'])){
					$checkSumValue .= "|".$data['QuoteId'];
				}
				break;
				case 'REDIRECTURL': 
				if(!empty($data['REDIRECTURL'])){
					$checkSumValue .= "|".$data['REDIRECTURL'];
				}
				break;
				case 'RETURN_URL': 
				if(!empty($data['ReturnURL'])){
					$checkSumValue .= "|".$data['ReturnURL'];
				}
				break;
				case 'SOURCECODE': 
				if(!empty($data['SourceCode'])){
					$checkSumValue .= "|".$data['SourceCode'];
				}
				break;	
				case 'SUBCODE': 
				if(!empty($data['SubCode'])){
					$checkSumValue .= "|".$data['SubCode'];
				}
				break;					
				case 'TXNID': 
				if(!empty($data['SourceTxnId'])){
					$checkSumValue .= "|".$data['SourceTxnId'];
				}
				break;
				
			}
			
		}
		
		$hashMethod = 'SHA512';
		$hashedData	 = hash($hashMethod,$checkSumValue);
		$hashedData = strtoupper($hashedData);
		
		$data['secSignature'] = $hashedData;
		
		$logData = 	$data;
		$logData['checksum_values'] = $checkSumValue;
		$logData['checksum_hash'] = $hashedData;
	
		$data_arr = ["lead_id" => $lead_id, "txt_id" => $data['SourceTxnId'],"pg_type" => "PayU"];
		$this->db->insert("payment_txt_ids",$data_arr);
	
		$request_arr = ["lead_id" => $lead_id, "req" => json_encode($logData) ,"product_id"=> $data['product_code'], "type"=>"payment_request_post"];
		
		$dataArray['tablename'] = 'logs_docs'; 
		$dataArray['data'] = $request_arr; 
		$this->Logs_m->insertLogs($dataArray);
		
		unset($data['REDIRECTURL']);//this key is not required in hidden element.		
		$this->load->view('employee/pgcommon_payment_hidden_submit',compact('url','data'));					
	}		
	
	public function payment_success_view($emp_id_encrypt) {
		
		extract($this->input->post(null, true));
		
		$emp_id = encrypt_decrypt_password($emp_id_encrypt,'D');
		
		// $request_arr = ["lead_id" => $emp_id, "req" => json_encode($this->input->post()) ,"product_id"=> "R03", "type"=>"payment_response_post_check"];
		
  	    // $dataArray['tablename'] = 'logs_docs'; 
		// $dataArray['data'] = $request_arr; 
		// $this->Logs_m->insertLogs($dataArray);
		
		$query = $this->db->query("SELECT GROUP_CONCAT(p.id) proposal_id,ed.emp_id,ed.lead_id,mpst.product_code FROM employee_policy_detail AS epd,product_master_with_subtype AS mpst,employee_details AS ed,proposal as p,payment_details AS pd,user_payu_activity as ua WHERE p.emp_id = ed.emp_id and epd.product_name = mpst.id AND mpst.policy_subtype_id = epd.policy_sub_type_id AND p.policy_detail_id=epd.policy_detail_id AND p.id = pd.proposal_id AND ed.emp_id = ua.emp_id and (TIMESTAMPDIFF(SECOND, ua.created_time,now()) < 259200) AND ed.emp_id='".$emp_id."' GROUP BY p.emp_id")->row_array();
		
		if(!empty($query['proposal_id'])){
			
			$ids = explode(',',$query['proposal_id']);
			
			if(isset($TxRefNo)){
				
				$request_arr = ["lead_id" => $query['lead_id'], "req" => json_encode($this->input->post()) ,"product_id"=> $query['product_code'], "type"=>"payment_response_post"];
				
				$dataArray['tablename'] = 'logs_docs'; 
				$dataArray['data'] = $request_arr; 
				$this->Logs_m->insertLogs($dataArray);
				
				$request_arr = ["payment_status" => $TxMsg,"premium_amount" => $amount,"payment_type" => $paymentMode,"pgRespCode" => $pgRespCode,"merchantTxnId" => $merchantTxnId,"SourceTxnId" => $SourceTxnId,"txndate" => $txnDateTime,"TxRefNo" => $TxRefNo,"TxStatus"=>$TxStatus,"json_quote_payment"=>json_encode($_REQUEST)];
				
				$this->db->where_in('proposal_id', $ids);
				$this->db->where('TxStatus != ','success');
				$this->db->update("payment_details",$request_arr);
			}
			
			$proposal_id = $ids[0];
			
			$payment_data = $this->db->query("select payment_status,TxStatus from payment_details where proposal_id='$proposal_id'")->row_array();
			
			
			if($payment_data['TxStatus'] == 'success'){
				
				$check_result = $this->obj_api->policy_creation_call($query['lead_id']);
				
				if($check_result['Status'] == 'Success'){
					
					$query_lead = $this->db->query("select lead_id from employee_details where emp_id='$emp_id'")->row_array();

                    $lead_id_mis = $query_lead['lead_id'];
    
                    $query_check_mis = $this->db->query("select id from mis_ack_coi where lead_id='$lead_id_mis'")->row_array();
    
                    if(empty($query_check_mis)){
                        $insert_mis_coi_table = $this->obj_home->save_ack_mis($query_lead['lead_id']);
                    }

	
					$data_policy[0] = $this->db->query("SELECT GROUP_CONCAT(DISTINCT(certificate_number)) certificate_number FROM api_proposal_response m WHERE m.emp_id = '$emp_id' GROUP BY emp_id")->row_array();
					
					//old GCS certificate
					if($query['product_code'] == 'R03')
					{
						$data_policy[1] = $this->db->query("select COI_No from api_cancer_response where emp_id = '$emp_id'")->row_array();
					}
											
					/*$data = $this->db->query("select p.proposal_no,ed.lead_id,pd.txndate,ed.emp_firstname from proposal as p,employee_details as ed,payment_details as pd  where p.emp_id = ed.emp_id and p.id = pd.proposal_id and ed.emp_id ='$emp_id'")->result_array();
					
					$this->load->employee_template_api("thankyou",compact('data_policy','data'));*/
					//Acknowledgement code - 16 march 21
					$data = $this
                    ->db
                    ->query("select ed.AXISBANKACCOUNT,mpst.product_name,p.proposal_no,ed.lead_id,pd.txndate,ed.emp_firstname from employee_policy_detail AS epd,product_master_with_subtype AS mpst,proposal as p,employee_details as ed,payment_details as pd  where epd.product_name = mpst.id and mpst.policy_subtype_id = epd.policy_sub_type_id and p.policy_detail_id=epd.policy_detail_id and p.emp_id = ed.emp_id and p.id = pd.proposal_id and ed.emp_id ='$emp_id'")->result_array();

                    $MandateLink_data = $this
                        ->db
                        ->query("select MandateLink,Registrationmode from emandate_data where lead_id = '" . $query['lead_id'] . "'")->row_array();
						
					$is_cust_journey = 1;
					
                    $this
                        ->load
                        ->employee_template_api("thankyou", compact('data_policy', 'data', 'MandateLink_data','is_cust_journey'));
					
				}else{
					
					redirect("/payment_error_view_call_axis/" . $emp_id_encrypt);
					
				}
				
			}else{	
				
				$data = $this->db->query("select p.proposal_no,ed.lead_id,ed.emp_firstname from proposal as p,employee_details as ed  where p.emp_id = ed.emp_id and ed.emp_id ='$emp_id'")->result_array();
				
				$this->load->employee_template_api("thankyou",compact('data'));
				//redirect("/payment_error_view_call_axis/" . $emp_id_encrypt);
			}
			
			
		}else{
			
			echo "Payment link has been expired, Please get in touch with your Branch RM";
			
		}
		
		
	}	
	
	public function all_cron_payu_manual($check=2){

		if($check == 2){
			//echo "8 clock cron devolmnt pending";exit;

			$query1 = $this->db->query("SELECT DISTINCT e.lead_id,e.emp_id,e.product_id from proposal p,employee_details e,payment_details as pd WHERE p.emp_id = e.emp_id and p.id = pd.proposal_id and pd.payment_status = 'No Error' and p.status IN('Payment Received') and p.EasyPay_PayU_status = 1 and e.product_id in('R03','R07','R11') and date(p.created_date) = date(now())")->result_array();

//print_pre($query1);exit;
				if($query1)
				{
					foreach($query1 as $val)
					{
						sleep(30);
						$where_arr = ["emp_id"=>$val['emp_id'],"status"=>"Payment Received"];
						$arr = ["count" => 2];
						$this->db->where($where_arr);
						$this->db->update("proposal",$arr);
						 $where_arr_emp = ["emp_id"=>$val['emp_id']];
                                                $employee_arr = ["is_policy_issue_initiated" => 0];
                                                $this->db->where($where_arr_emp);
                                                $this->db->update("employee_details",$employee_arr);								
						$data = $this->obj_api->policy_creation_call_manual($val['lead_id'], 1);
						//$data = json_decode($check_result,true);
		                                 // print_pre($data);exit;	
						$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($data),"res" => json_encode($data) ,"product_id"=> $val['product_id'], "type"=>"8clock_cron_manual"];
						$dataArray['tablename'] = 'logs_docs'; 
						$dataArray['data'] = $request_arr; 
						$this->Logs_m->insertLogs($dataArray);
						
						echo $data['Status']." hii".$val['lead_id'];
						
					}
					
				}
			
		}else{
			
			echo "for new pg rollback";exit;
			
			// after 2020-11-17 (for new PG real pg status check)
			
			$query1 = $this->db->query("SELECT ed.lead_id,pt.id FROM employee_details as ed,proposal AS p,payment_txt_ids as pt WHERE ed.emp_id=p.emp_id AND ed.lead_id = pt.lead_id AND p.status IN('Payment Pending','Rejected') AND ed.product_id in('R03','R07') AND pt.pg_type = 'New' AND pt.cron_count < 2  limit 15")->result_array();

			if($query1)
			{
				
				foreach($query1 as $val1){
					
					$this->db->query("UPDATE payment_txt_ids SET cron_count = cron_count + 1 WHERE id =".$val1['id']);
								
						$check_pg = $this->obj_api->real_pg_check($val1['lead_id']);
								
						if($check_pg){
							$check_result = $this->obj_api->policy_creation_call($val1['lead_id'], 1);
						}
				}
				
			}
		
		}
		
	}	
	// new payu old,new  cron
	public function all_cron_payu($check){

		if($check == 2){
			//echo "8 clock cron devolmnt pending";exit;

			$query1 = $this->db->query("SELECT DISTINCT e.lead_id,e.emp_id,e.product_id from proposal p,employee_details e,payment_details as pd WHERE p.emp_id = e.emp_id and p.id = pd.proposal_id and pd.payment_status = 'No Error' and p.status IN('Payment Received') and p.EasyPay_PayU_status = 1 and e.product_id in('R03','R07','R11') and date(p.created_date) = date(now())")->result_array();
//echo $this->db->last_query();exit;
				if($query1)
				{
					foreach($query1 as $val)
					{
						sleep(30);
						$where_arr = ["emp_id"=>$val['emp_id'],"status"=>"Payment Received"];
						$arr = ["count" => 0];
						$this->db->where($where_arr);
						$this->db->update("proposal",$arr);
						$where_arr_emp = ["emp_id"=>$val['emp_id']];
						$employee_arr = ["is_policy_issue_initiated" => 0];
						$this->db->where($where_arr_emp);
						$this->db->update("employee_details",$employee_arr);						
						$data = $this->obj_api->policy_creation_call($val['lead_id'], 1);
						//$data = json_decode($check_result,true);
			
						$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($data),"res" => json_encode($data) ,"product_id"=> $val['product_id'], "type"=>"8clock_cron"];
						$dataArray['tablename'] = 'logs_docs'; 
						$dataArray['data'] = $request_arr; 
						$this->Logs_m->insertLogs($dataArray);
						
						//echo $data['Status']." hii".$val['lead_id'];
						
					}
					
				}
			
		}else{
			
			echo "for new pg rollback";exit;
			
			// after 2020-11-17 (for new PG real pg status check)
			
			$query1 = $this->db->query("SELECT ed.lead_id,pt.id FROM employee_details as ed,proposal AS p,payment_txt_ids as pt WHERE ed.emp_id=p.emp_id AND ed.lead_id = pt.lead_id AND p.status IN('Payment Pending','Rejected') AND ed.product_id in('R03','R07','R11') AND pt.pg_type = 'New' AND pt.cron_count < 2  limit 15")->result_array();

			if($query1)
			{
				
				foreach($query1 as $val1){
					
					$this->db->query("UPDATE payment_txt_ids SET cron_count = cron_count + 1 WHERE id =".$val1['id']);
								
						$check_pg = $this->obj_api->real_pg_check($val1['lead_id']);
								
						if($check_pg){
							$check_result = $this->obj_api->policy_creation_call($val1['lead_id'], 1);
						}
				}
				
			}
		
		}
		
	}


	/* cron */
	public function rehit_policy_create()
	{	
	//echo 1;exit;	
							$key = "XGaHm4";
						$salt = "dC2qLagI";
						//$wsUrl = "https://info.payu.in/merchant/postservice?form=2";
						//$key = "nAtwzQ";
						//$salt = "TqhIAHgl";
						$wsUrl = "https://test.payu.in/merchant/postservice.php?form=2";
						$command = "verify_payment";
						$var1 = "AXFP1653375131475304306"; // SourceTxnId

						$hash_str = $key  . '|' . $command . '|' . $var1 . '|' . $salt ;
						$hash = strtolower(hash('sha512', $hash_str));

						$r = array('key' => $key , 'hash' =>$hash , 'var1' => $var1, 'command' => $command);
						$qs= http_build_query($r);
print_r($qs);die;
		$query = $this
		->db
		->query("SELECT ed.lead_id,ed.emp_id,ed.email,ed.mob_no,p.premium,g.QuotationNumber,mpst.payu_info_url,mpst.product_code,pt.txt_id,pt.pg_type,pt.id FROM product_master_with_subtype AS mpst,employee_policy_detail AS epd,employee_details as ed,proposal AS p,ghi_quick_quote_response AS g,payment_txt_ids as pt WHERE epd.product_name = mpst.id AND p.policy_detail_id=epd.policy_detail_id AND p.emp_id = g.emp_id AND ed.emp_id=p.emp_id AND ed.lead_id = pt.lead_id AND g.`status` = 'success' AND p.status IN('Payment Pending','Rejected')  AND ed.product_id in('R03','R07','R11') AND date(p.created_date) >= '2020-10-05' AND pt.cron_count < 2   group by pt.txt_id limit 15")->result_array();
//	print_pre($query);exit;
		if($query)
		{
			
			foreach($query as $val){
			sleep(30);	
				$this->db->query("UPDATE payment_txt_ids SET cron_count = cron_count + 1 WHERE id =".$val['id']);
	
				if($val['pg_type'] == 'PayU'){
						
						$key = "XGaHm4";
						$salt = "dC2qLagI";
						//$wsUrl = "https://info.payu.in/merchant/postservice?form=2";
						//$key = "nAtwzQ";
						//$salt = "TqhIAHgl";
						$wsUrl = "https://test.payu.in/merchant/postservice.php?form=2";
						$command = "verify_payment";
						$var1 = "AXFP1653375131475304306"; // SourceTxnId

						$hash_str = $key  . '|' . $command . '|' . $var1 . '|' . $salt ;
						$hash = strtolower(hash('sha512', $hash_str));

						$r = array('key' => $key , 'hash' =>$hash , 'var1' => $var1, 'command' => $command);
						$qs= http_build_query($r);
print_r($qs);die;
						$c = curl_init();
						curl_setopt($c, CURLOPT_URL, $wsUrl);
						curl_setopt($c, CURLOPT_POST, 1);
						curl_setopt($c, CURLOPT_POSTFIELDS, $qs);
						curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
						curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
						curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);

						$o = curl_exec($c);
						$err = curl_error($c);

						curl_close($c);
						
						if ($err) {
							$request_arr = ["lead_id" => $val['lead_id'],"req" => json_encode($qs), "res" => json_encode($err) ,"product_id"=> $val['product_code'], "type"=>"pg_status_curl_error_cron"];
							$dataArray['tablename'] = 'logs_docs'; 
							$dataArray['data'] = $request_arr; 
							$this->Logs_m->insertLogs($dataArray);
							
						}else{
							$valueSerialized = @unserialize($o);
							
							if($o === 'b:0;' || $valueSerialized !== false) {
								
								$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($qs) ,"res" => json_encode($o),"product_id"=> $val['product_code'], "type"=>"pg_status_error1_cron"];
								$dataArray['tablename'] = 'logs_docs'; 
								$dataArray['data'] = $request_arr; 
								$this->Logs_m->insertLogs($dataArray);
							}

							$rs = json_decode($o,true);
							$payUStatus = $rs['status'];
							$result = $rs['transaction_details'];
							$response_arr = $result[$val['txt_id']];

							if($payUStatus && $response_arr['status'] == 'success'){
							//echo 2222;exit;	
								$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($qs) ,"res" => json_encode($o),"product_id"=> $val['product_code'], "type"=>"pg_status_success"];
								$dataArray['tablename'] = 'logs_docs'; 
								$dataArray['data'] = $request_arr; 
								$this->Logs_m->insertLogs($dataArray);
								
								$date = new DateTime($response_arr['addedon']);
								$txt_date = $date->format('m/d/Y g:i:s A'); 
								
								$arr = ["payment_status" => "No Error","premium_amount" => round($response_arr['transaction_amount'],2),"payment_type" => $response_arr['PG_TYPE'],"pgRespCode" => $response_arr['error_code'],"merchantTxnId" => $response_arr['txnid'],"SourceTxnId" => $response_arr['txnid'],"txndate" => $txt_date,"TxRefNo" => $response_arr['mihpayid'],"TxStatus"=>$response_arr['status'],"json_quote_payment"=>json_encode($response_arr)];
								
								$proposal_ids = $this->db->query("select GROUP_CONCAT(id) proposal_id from proposal where emp_id='".$val['emp_id']."'")->row_array();
								
								$ids = explode(',',$proposal_ids['proposal_id']);
								
								$this->db->where_in('proposal_id', $ids);
								$this->db->update("payment_details",$arr);	
								//echo 2;
								$check_result = $this->obj_api->policy_creation_call($val['lead_id'], 1);
								
							}else{
								
								$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($qs) ,"res" => json_encode($o),"product_id"=> $val['product_code'], "type"=>"pg_status_error2_cron"];
								$dataArray['tablename'] = 'logs_docs'; 
								$dataArray['data'] = $request_arr; 
								$this->Logs_m->insertLogs($dataArray);
								
								$this->db->where("emp_id",$val['emp_id']);
								$this->db->update("proposal",["count"=>"1"]);
								
							}
					}
					
				}
				
				
			}	
	
		}else{
				$query1 = $this->db->query("SELECT DISTINCT lead_id from proposal p,employee_details e,payment_details as pd WHERE p.emp_id = e.emp_id and p.id = pd.proposal_id and pd.payment_status = 'No Error' and p.status IN('Payment Received') and p.EasyPay_PayU_status = 1 and e.product_id in('R03','R07','R11') and p.count < 3 and DATE(pd.updated_date) = DATE(NOW()) limit 5")->result_array();


				if($query1)
				{
					foreach($query1 as $val){
						sleep(30);
						$check_result = $this->obj_api->policy_creation_call($val['lead_id'], 1);
						//$check_result = json_decode($check_result,true);
						
						$request_arr = ["lead_id" => $val['lead_id'], "req" => json_encode($check_result),"res" => json_encode($check_result) ,"product_id"=> "", "type"=>"payu_real_check_both"];
						$dataArray['tablename'] = 'logs_docs'; 
						$dataArray['data'] = $request_arr; 
						$this->Logs_m->insertLogs($dataArray);
												
					}
					
				}
				
		}
		
	}
	
	
	
	
}


