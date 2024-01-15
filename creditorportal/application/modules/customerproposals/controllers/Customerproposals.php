<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
class Customerproposals extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		checklogin();
		$this->RolePermission = getRolePermissions();
	}
 
	function index()
	{
		$result = array();
		$this->load->view('template/header.php');
		$this->load->view('customerproposals/index',$result);
		$this->load->view('template/footer.php');
	}
   
	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		$_GET['role_id'] = $_SESSION['webpanel']['role_id'];
		$_GET['user_id'] = $_SESSION['webpanel']['employee_id'];
		//echo "<pre>GET ";print_r($_SESSION);exit;
        $getCreditors = curlFunction(SERVICE_URL.'/api/getRoleWiseCreditorsData',$_GET);
        $getCreditors = json_decode($getCreditors, true);
        $creditor_id=$getCreditors['Data'][0]['creditor_id'];
        $_GET['creditor_id'] = $creditor_id;
   //     print_r($getCreditors);die;
		$userListing = curlFunction(SERVICE_URL.'/api/leadListing',$_GET);
		$userListing = json_decode($userListing, true);
		//echo "<pre>";print_r($userListing);exit;
		if($userListing['status_code'] == '401'){
			//echo "in condition";
			redirect('login');
			exit();
		}
		
		
		//$get_result = $this->adcategorymodel->getRecords($_GET);

		$result = array();
		$result["sEcho"]= $_GET['sEcho'];

		$result["iTotalRecords"] = $userListing['Data']['totalRecords'];	//iTotalRecords get no of total recors
		$result["iTotalDisplayRecords"]= $userListing['Data']['totalRecords']; //iTotalDisplayRecords for display the no of records in data table.

		$items = array();
		
		if(!empty($userListing['Data']['query_result']) && count($userListing['Data']['query_result']) > 0)
		{
			for($i=0;$i<sizeof($userListing['Data']['query_result']);$i++)
			{
				$temp = array();
				array_push($temp, $userListing['Data']['query_result'][$i]['trace_id'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['lan_id'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['plan_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['creaditor_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['employee_full_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['full_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['mobile_no'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['email_id'] );

				$coi = "NA";
				$end_date = "";
				$renewal_text = "";

				if($userListing['Data']['query_result'][$i]['status'] =='Pending'){
					array_push($temp, "Proposal Creation" );
				}else if($userListing['Data']['query_result'][$i]['status'] =='Client-Approval-Awaiting'){
					array_push($temp, "Pending at Customer" );
				}else if($userListing['Data']['query_result'][$i]['status'] =='Customer-Payment-Awaiting'){
					array_push($temp, "Pending For Payment" );
				}else if($userListing['Data']['query_result'][$i]['status'] =='BO-Approval-Awaiting'){
					array_push($temp, "Pending Branch Ops Verification" );
				}else if($userListing['Data']['query_result'][$i]['status'] =='CO-Approval-Awaiting'){
					array_push($temp, "Pending Central Ops Verification" );
				}else if($userListing['Data']['query_result'][$i]['status'] =='Discrepancy'){
					array_push($temp, "Discrepancy Raised" );
				}else if($userListing['Data']['query_result'][$i]['status'] =='Approved'){
					array_push($temp, "Issued" );
					$coi ='<a href="proposalcoi/downloadCOI?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="COI" target="_blank">Get COIs</a>';

					$end_date = date("d-m-Y H:i:s", strtotime($userListing['Data']['query_result'][$i]['end_date']));

					//Check pending for renewal
					$current_date = date("Y-m-d");
					$expiry_date = $userListing['Data']['query_result'][$i]['end_date'];
					$renewal_date = date('Y-m-d', strtotime($expiry_date. ' + 60 days'));

					if ($current_date < $renewal_date) {
						$renewal_text = "";
					}else{
						$renewal_text = "Pending for Renewal";
					}

				}else if($userListing['Data']['query_result'][$i]['status'] =='Rejected'){
					array_push($temp, "Cancelled" );
				}else if($userListing['Data']['query_result'][$i]['status'] =='UW-Approval-Awaiting'){
					array_push($temp, "Pending With Underwriting" );
				}else{
					array_push($temp, $userListing['Data']['query_result'][$i]['status']);
				}
				array_push($temp, $userListing['Data']['query_result'][$i]['payment_mode_name'] );

				array_push($temp, $userListing['Data']['query_result'][$i]['remark'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['location_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['premium'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['premium_with_tax'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['loan_amt'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['sum_insured'] );
				

				//No. of days
				$no_days = 0;
				if($userListing['Data']['query_result'][$i]['status'] !='Approved' || $userListing['Data']['query_result'][$i]['status'] !='Rejected' ){
					$now = time(); // or your date as well
					$your_date = strtotime($userListing['Data']['query_result'][$i]['createdon']);
					$datediff = $now - $your_date;

					$no_days = round($datediff / (60 * 60 * 24));
				}

				array_push($temp, $no_days );

				array_push($temp, $coi );
				array_push($temp, date("d-m-Y H:i:s", strtotime($userListing['Data']['query_result'][$i]['updatedon'])) );

				array_push($temp, $userListing['Data']['query_result'][$i]['transaction_number'] );

				array_push($temp, $end_date );
				array_push($temp, $renewal_text );

				

				
				$actionCol = "";
				$lead_id_enc = encrypt_decrypt_password($userListing['Data']['query_result'][$i]['lead_id'], 'E');

				if($userListing['Data']['query_result'][$i]['payment_status'] == 'Success'){
					if(in_array('ProposalView',$this->RolePermission)){
						$actionCol .='<a href="policyproposal/preview?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';						
					}
				}else{
					if($userListing['Data']['query_result'][$i]['status'] == 'Pending'){
						if(in_array('ProposalAdd',$this->RolePermission)){
							$actionCol .='<a href="policyproposal/addedit?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">Add Proposal</a>';
						}
					}else if($userListing['Data']['query_result'][$i]['status'] == 'In-Progress' || $userListing['Data']['query_result'][$i]['status'] == 'Discrepancy'){
						
						if(in_array('ProposalView',$this->RolePermission)){
							$actionCol .='<a href="policyproposal/preview?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';						
						}
						
						if(in_array('ProposalAdd',$this->RolePermission)){
							$actionCol .=' | <a href="policyproposal/addedit?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">Edit Proposal</a>';
						}
						
						
					}else if($userListing['Data']['query_result'][$i]['status'] == 'Client-Approval-Awaiting'){
            
						if(in_array('ProposalView',$this->RolePermission)){
							$actionCol .='<a href="policyproposal/preview?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';						
						}

						if(in_array('ProposalAdd',$this->RolePermission)){
							$actionCol .=' | <a href="policyproposal/addedit?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">Edit Proposal</a>';
						}
						if($actionCol != ''){

							$actionCol .= ' | ';
						}
						$actionCol .= '<a href="javascript:void(0);" class="retrigger-link" data-lead="'.$lead_id_enc.'" title="Retrigger Link">Retrigger Link</a>';
					}else if($userListing['Data']['query_result'][$i]['status'] == 'Customer-Payment-Awaiting'){
            
						if(in_array('ProposalView',$this->RolePermission)){
							$actionCol .='<a href="policyproposal/preview?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';						
						}

						if(in_array('ProposalAdd',$this->RolePermission)){
							$actionCol .=' | <a href="policyproposal/addedit?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">Edit Proposal</a>';
						}
						if($actionCol != ''){

							$actionCol .= ' | ';
						}
						$actionCol .= '<a href="javascript:void(0);" class="retrigger-link" data-lead="'.$lead_id_enc.'" title="Retrigger Link">Retrigger Link</a>';
					}else if($userListing['Data']['query_result'][$i]['status'] == 'BO-Approval-Awaiting'){
            
						if(in_array('ProposalView',$this->RolePermission)){
							$actionCol .='<a href="policyproposal/preview?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';						
						}

						if(in_array('ProposalAdd',$this->RolePermission)){
							$actionCol .=' | <a href="policyproposal/addedit?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">Edit Proposal</a>';
						}
						
					}else if($userListing['Data']['query_result'][$i]['status'] == 'CO-Approval-Awaiting'){
            
						if(in_array('ProposalView',$this->RolePermission)){
							$actionCol .='<a href="policyproposal/preview?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';						
						}

						if(in_array('ProposalAdd',$this->RolePermission)){
							$actionCol .=' | <a href="policyproposal/addedit?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">Edit Proposal</a>';
						}
						
					}else if($userListing['Data']['query_result'][$i]['status'] == 'UW-Approval-Awaiting'){
            
						if(in_array('ProposalView',$this->RolePermission)){
							$actionCol .='<a href="policyproposal/preview?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';						
						}

						if(in_array('ProposalAdd',$this->RolePermission)){
							$actionCol .=' | <a href="policyproposal/addedit?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">Edit Proposal</a>';
						}
						
					}else{
						if(in_array('ProposalView',$this->RolePermission)){
							$actionCol .='<a href="policyproposal/preview?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';
	
						}
					}
				}
				
				array_push($temp, $actionCol);
				array_push($items, $temp);
			}
		}

		$result["aaData"] = $items;
		echo json_encode($result);
		exit;
	}
	
	
	public function getCOI() {
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['lead_id'] = $_POST['lead_id'];
		$getCOIs = curlFunction(SERVICE_URL.'/api/getCOINumbers',$data);
		$getCOIs = json_decode($getCOIs, true);
		//echo "<pre>";print_r($getCOIs);exit;
		$cois = $getCOIs['Data'];
		//echo "<pre>";print_r($cois);exit;
		
		$cois_numbers = array();
		$cois_numbers_str = "";
		if(!empty($cois)){
			for ($i = 0; $i < sizeof($cois); $i++){
				$cois_numbers[] = $cois[$i]['certificate_number'];
			}
			$cois_numbers_str = implode(",", $cois_numbers);
		}
		//echo "<pre>";print_r($cois_numbers_str);exit;
		echo json_encode(array("status" => "success", "cois_numbers" => $cois_numbers_str));
		exit;
		
	}

	public function retriggerLink(){

		if($this->input->is_ajax_request()){

			$data['lead_id'] = $this->input->post('lead_id');
			$data['alert_ids'] = ['A1655'];
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['user_id'] = $_SESSION['webpanel']['employee_id'];

			$response = curlFunction(SERVICE_URL.'/api2/retriggerLink', $data);
			echo $response;exit;
		}
	}
    function exportexcel(){
        /*ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/
        $data = array();
        //echo "<pre>";print_r($_POST);exit;
        $data['utoken'] = $_SESSION['webpanel']['utoken'];
        $data['role_id'] = $_SESSION['webpanel']['role_id'];
        $data['user_id'] = $_SESSION['webpanel']['employee_id'];

        $data['trace_id'] = ($_POST['trace_id']) ? $_POST['trace_id'] : '';
        $data['lan_id'] = ($_POST['lan_id']) ? $_POST['lan_id'] : '';
        $data['plan_name'] = ($_POST['plan_name']) ? $_POST['plan_name'] : '';
        $data['creditor_name'] = ($_POST['creditor_name']) ? $_POST['creditor_name'] : '';
        $data['employee_full_name'] = ($_POST['employee_full_name']) ? $_POST['employee_full_name'] : '';
        $data['full_name'] = ($_POST['full_name']) ? $_POST['full_name'] : '';
        $data['mobile_no'] = ($_POST['mobile_no']) ? $_POST['mobile_no'] : '';
        $data['email_id'] = ($_POST['email_id']) ? $_POST['email_id'] : '';
        $data['from_date'] = ($_POST['from_date']) ? $_POST['from_date'] : '';
        $data['to_date'] = ($_POST['to_date']) ? $_POST['to_date'] : '';
        $data['type_download'] = ($_POST['type_download']) ? $_POST['type_download'] : '';

        //echo "<pre>";print_r($data);exit;
        $proposal_data = $this->exportProposal($data);
         // print_r($proposal_data);die;
        $m_data=$proposal_data['query_result'];
        //  print_r($m_data);die();
        //echo $mobiledata['Data'][0]['trace_id'];exit;
        //echo ABSOLUTE_DOC_ROOT;exit;
        $this->load->library('excel');
        if($data['type_download'] == 1){
            $getAllpolicySubtype=$this->db->query("select code,policy_sub_type_id from master_policy_sub_type where isactive=1 and policy_type_id=1");
            if($this->db->affected_rows() > 0){
                $resultN=$getAllpolicySubtype->result();
            }


            $fileName = 'CustomerProposalExcel-'.time().'.xls';


            //$mobiledata = $data;
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $excel_coulumns=array(
                'partner','plan','Unique Id','salutation','first_name','middle_name','last_name','gender','dob','email_id','mobile_number',
                'tenure','is_coapplicant','coapplicant_no','userId','sm_location','alternateMobileNo','homeAddressLine1','homeAddressLine2',
                'homeAddressLine3','pincode','NoOfLives','adult_count','child_count','MemberNo','Salutation','First_Name',
                'Middle_Name','Last_Name','Gender','DateOfBirth','Relation_Code','LoanDisbursementDate','LoanAmount',
                'LoanAccountNo','LoanTenure','modeOfEntry','PaymentMode','bankName','branchName','bankLocation','chequeType',
                'ifscCode','Nominee_First_Name','Nominee_Last_Name','Nominee_Contact_Number','Nominee_Home_Address','Nominee_gender',
                'Nominee_Salutation','Nominee_Email','Nominee_dob','Nominee_Relationship_Code','TransactionNumber','TransactionRcvdDate','PaymentMode'
            );
            $policy_subtype_name=array();
            foreach ($resultN as $code){ //row BC-BR
                array_push($excel_coulumns,$code->code." SumInsure");
                array_push($excel_coulumns,$code->code." Premium");
                array_push($excel_coulumns,$code->code." Proposal Number");
                $policy_subtype_name[$code->policy_sub_type_id]=$code->code;
            }
            $cnt=1;
            $char1='A';
            // set Header
            foreach ($excel_coulumns as $column){
                $objPHPExcel->getActiveSheet()->SetCellValue($char1 . $cnt, $column);
                $char1++;
            }



            $final_array=array();
            foreach ($m_data as $data){
                if(!empty($data->member_details)){
                    $member_array=explode(",",$data->member_details);
                    $premium_details=explode(",",$data->premium_details);
                    $premData=array();
                    foreach ($premium_details as $p_data){
                        $p_data_n=explode("--",$p_data);
                        $policy_sub_type_id=$p_data_n[0];
                        $premData[$policy_sub_type_id]=$p_data_n;
                    }
                    $i=1;
                    foreach ($member_array as $member_data){
                        $member=explode("|",$member_data);
                        $relation=$member[0];
                        $policy_member_salutation=$member[1];
                        $policy_member_gender=$member[2];
                        $policy_member_first_name=$member[3];
                        $policy_member_last_name=$member[4];
                        $policy_member_dob=$member[5];

                        $final_array1=array(
                            $data->creaditor_name,
                            $data->plan_name,
                            $data->unique_id,

                            $data->salutation,
                            $data->first_name,
                            $data->middle_name,
                            $data->last_name,
                            $data->gender,
                            $data->dob,
                            $data->email_id,
                            (string) $data->mobile_no,
                            $data->tenure,
                            $data->is_coapplicant,
                            $data->coapplicant_no,
                            $data->createdby,
                            $data->location_name,
                            $data->mobile_no2,
                            $data->address_line1,
                            $data->address_line2,
                            $data->address_line3,
                            $data->pincode,
                            $data->no_of_lives,
                            "",
                            "",
                            $i,
                            $policy_member_salutation,
                            $policy_member_first_name,
                            "",
                            $policy_member_last_name,
                            $policy_member_gender,
                            $policy_member_dob,
                            $relation,
                            $data->loan_disbursement_date,
                            $data->loan_amt,
                            $data->lan_id,
                            $data->loan_tenure,
                            "Direct",
                            $data->payment_mode_name,
                            "",
                            "",
                            "",
                            "",
                            "",
                            $data->nominee_first_name,
                            $data->nominee_last_name,
                            $data->nominee_contact,
                            "",
                            $data->nominee_gender,
                            $data->nominee_salutation,
                            $data->nominee_email,
                            $data->nominee_dob,
                            $data->nominee_relation,
                            $data->transaction_number,
                            $data->transaction_date,
                            $data->payment_mode_name,


                        );
//print_r($resultN);die;
                        foreach ($resultN as $code){

                            if(array_key_exists($code->policy_sub_type_id,$premData)){

                                $sum_insured=$premData[$code->policy_sub_type_id][2];
                                $premium_amount=$premData[$code->policy_sub_type_id][3];
                                $proposal_number=$premData[$code->policy_sub_type_id][5];
                                //$final_array[]=$sum_insured;
                                //$final_array[]=$premium_amount;
                                array_push($final_array1,$sum_insured);
                                array_push($final_array1,$premium_amount);
                                array_push($final_array1,$proposal_number);
                            }else{
                                $sum_insured=0;
                                $premium_amount=0;
                                $proposal_number=0;
                                array_push($final_array1,$sum_insured);
                                array_push($final_array1,$premium_amount);
                                array_push($final_array1,$proposal_number);
                            }
                        }
                        array_push($final_array,$final_array1);
                        $i++;
                    }
                }
            }
            // echo count($final_array);die;
            // print_r($final_array);die;
            // set Row
            $rowCount = 2;
            foreach ($final_array as $eachrow){
                $char='A';
                foreach ($eachrow as $key=>$row){
                    // print_r($row);die;
                    $objPHPExcel->getActiveSheet()->SetCellValue($char . $rowCount, $row);
                    $char++;
                }
                $rowCount++;
            }
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $objWriter->save(ABSOLUTE_DOC_ROOT.'/assets/smdashboardexports/'.$fileName);
            $filepath = FRONT_URL.'/assets/smdashboardexports/'.$fileName;
            echo json_encode(array('success'=>true, 'msg'=>"Records Generated", 'Data'=>$filepath));
            exit;
        }else{
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $array1 = array(
                "Api_type", "Invoice_number", "Proposal number", "plan_id", "policy_id", "salutation", "first_name", "middle_name",
                "last_name", "gender", "mobile_number", "pincode", "Address1", "Address2", "Address3", "email_id", "mode_of_shipment",
                "from_country","to_country","from_city","to_city","type_of_shipment","currency_type","cargo_value",
                "rate_of_exchange","date_of_shipment","Bill_number","Bill_date","credit_number","credit_description","place_of_issuence",
                "Invoice_date","subject_matter_insured","marks_number","vessel_name","Consignee_name","Consignee_add","Financier_name","SumInsured",
                "userId","COI number","COI url","Issuance date"
            );
            $cnt1 = 1;
            $char1 = 'A';
            foreach ($array1 as $a1) {
                $objPHPExcel->getActiveSheet()->SetCellValue($char1 . $cnt1, $a1);
                $char1++;
            }
            $cnt = 2;


            foreach ($m_data as $key => $row) {
                if($row->status !='Cancelled'){
                    $row->status="Issuance";
                }else{
                    $row->status='Cancelled';
                }
                $coi_url = (!empty($row->COI_url))?FRONT_URL.$row->COI_url:'';
                $array2 = array(
                    $row->status, $row->Invoice_number, $row->proposal_no, $row->plan_id, $row->policy_id, $row->salutation,
                    $row->first_name, $row->middle_name, $row->last_name, $row->gender,(string) $row->mobile_no,$row->pincode,
                    $row->address_line1,$row->address_line2,$row->address_line3,$row->email_id,$row->mode_of_shipment,$row->from_country,
                    $row->to_country, $row->from_city, $row->to_city, $row->type_of_shipment, $row->currency_type, $row->cargo_value,
                    $row->rate_of_exchange,$row->date_of_shipment,$row->Bill_number,$row->Bill_date,$row->credit_number,$row->credit_description,
                    $row->place_of_issuence, $row->Invoice_date, $row->subject_matter_insured, $row->marks_number, $row->vessel_name,
                    $row->Consignee_name, $row->Consignee_add,$row->Financier_name,$row->cover,$row->createdby,'',$coi_url,''
                );

                $char = 'A';
                foreach ($array2 as $k => $r) {
                    if ($k == 2) {
                        $objPHPExcel->getActiveSheet()->getStyle($char . $cnt)
                            ->getNumberFormat()
                            ->setFormatCode('0');
                    }
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit($char . $cnt, $r, PHPExcel_Cell_DataType::TYPE_STRING);
                   
                    $char++;
                }
                $cnt++;

            }
            $fileName = "MarineCustomerProposal" . date("Y-m-d") . ".xls";
            // echo $filename;die;
            ob_end_clean();
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $objWriter->save(ABSOLUTE_DOC_ROOT.'/assets/smdashboardexports/'.$fileName);
            $filepath = FRONT_URL.'/assets/smdashboardexports/'.$fileName;
            echo json_encode(array('success'=>true, 'msg'=>"Records Generated", 'Data'=>$filepath));
            exit;
        }

    }
    function exportProposal($post)
    {
       // echo "<pre>";print_r($post);
        if(!empty($post)){
            $_POST['user_id'] = $post['user_id'];
        }
        if($_POST['role_id'] == 3 || $post['role_id'] == 3){
            $condition = "i.createdby='".$_POST['user_id']."' and p.policy_type_id=1";
        }else{
            $condition = "1=1";
        }
        $type_download=$_POST['type_download'];

        if(isset($_POST['trace_id']) && !empty($_POST['trace_id'])){
            $condition .= " AND i.trace_id = '".$_POST['trace_id']."'";
        }

        if(isset($_POST['lan_id']) && !empty($_POST['lan_id'])){
            $condition .= " AND i.lan_id = '".$_POST['lan_id']."'";
        }

        if(isset($_POST['plan_name']) && !empty($_POST['plan_name'])){
            $condition .= " AND p.plan_name like '%".$_POST['plan_name']."%' ";
        }

        if(isset($_POST['creditor_name']) && !empty($_POST['creditor_name'])){
            $condition .= " AND c.creaditor_name like '%".$_POST['creditor_name']."%' ";
        }

        if(isset($_POST['employee_full_name']) && !empty($_POST['employee_full_name'])){
            $condition .= " AND s.employee_full_name like '%".$_POST['employee_full_name']."%' ";
        }

        if(isset($_POST['full_name']) && !empty($_POST['full_name'])){
            $condition .= " AND cust.full_name like '%".$_POST['full_name']."%' ";
        }

        if(isset($_POST['mobile_no']) && !empty($_POST['mobile_no'])){
            $condition .= " AND i.mobile_no like '%".$_POST['mobile_no']."%' ";
        }

        if(isset($_POST['email_id']) && !empty($_POST['email_id'])){
            $condition .= " AND i.email_id like '%".$_POST['email_id']."%' ";
        }

        if(isset($_POST['from_date']) && !empty($_POST['from_date'])){
            $condition .= "  AND i.createdon >= '".date("Y-m-d",strtotime($_POST['from_date']))." 00:00:01' ";
        }

        if(isset($_POST['to_date']) && !empty($_POST['to_date'])){
            $condition .= "  AND i.createdon <= '".date("Y-m-d",strtotime($_POST['to_date']))." 23:59:59' ";
        }

        //echo "Condition: ".$condition;
        //exit;
        if($type_download == 1){
            $query=$this -> db -> query('SELECT i.unique_id,i.lead_id,p.plan_name,c.creaditor_name,cust.salutation,cust.first_name,cust.middle_name,i.tenure,cust.last_name,cust.gender,cust.dob,cust.email_id,cust.mobile_no,i.is_coapplicant,
i.coapplicant_no,i.createdby,l.location_name,cust.mobile_no2,cust.address_line1,cust.address_line2,cust.address_line3,cust.pincode,cust.no_of_lives,i.loan_disbursement_date,
i.lan_id,i.loan_amt,i.loan_tenure,i.status,pm.payment_mode_name,pd.transaction_number,pd.transaction_date,prd.nominee_first_name,prd.nominee_last_name,
prd.nominee_contact,prd.nominee_gender,prd.nominee_salutation,prd.nominee_email,
(select name from master_nominee_relations mnr where mnr.id=prd.nominee_relation ) as nominee_relation
,prd.nominee_dob,
( select group_concat(policy_sub_type_id,"--",master_policy_id,"--",sum_insured,"--",premium_amount,"--",tax_amount,"--",proposal_no) from proposal_policy pp where pp.lead_id=i.lead_id
) as premium_details,
( select group_concat(relation_with_proposal, "|",policy_member_salutation,"|", policy_member_gender,"|", policy_member_first_name,"|",
        policy_member_last_name,"|", policy_member_dob) from proposal_policy_member_details ppmd where ppmd.lead_id=i.lead_id
) as member_details
 FROM `lead_details` as `i`
 LEFT JOIN `master_plan` as `p` ON `i`.`plan_id` = `p`.`plan_id` 
 LEFT JOIN `master_ceditors` as `c` ON `i`.`creditor_id` = `c`.`creditor_id`
 LEFT JOIN `master_employee` as `s` ON `i`.`sales_manager_id` = `s`.`employee_id` 
 LEFT JOIN `master_customer` as `cust` ON `i`.`primary_customer_id` = `cust`.`customer_id` 
 LEFT JOIN `proposal_payment_details` as `pd` ON `i`.`lead_id` = `pd`.`lead_id`
 LEFT JOIN `proposal_details` as `prd` ON `i`.`lead_id` = `prd`.`lead_id`
 LEFT JOIN `payment_modes` as `pm` ON `i`.`mode_of_payment` = `pm`.`payment_mode_id` 
 LEFT JOIN `api_proposal_response` as `ar` ON `i`.`lead_id` = `ar`.`lead_id` 
 LEFT JOIN `proposal_discrepancies` as `d` ON `i`.`lead_id` = `d`.`lead_id`
 LEFT JOIN `master_location` as `l` ON `i`.`lead_location_id` = `l`.`location_id` WHERE ('.$condition.' ) GROUP BY `i`.`lead_id` order by lead_id desc');

        }else{
            $query = $this->db->query("select pp.status,mci.Invoice_number,pp.proposal_no,mp.plan_id,mp.policy_id,mc.salutation,mc.first_name,mc.middle_name,mc.last_name,mc.gender,mc.mobile_no,mc.pincode
,mc.address_line1,mc.address_line2,mc.address_line3,mc.email_id,mci.mode_of_shipment,mci.from_country,mci.to_country,mci.from_city,mci.to_city,mci.type_of_shipment
,mci.currency_type,mci.cargo_value,mci.rate_of_exchange,mci.date_of_shipment,mci.Bill_number,mci.Bill_date,mci.credit_number,mci.credit_description,
mci.place_of_issuence,mci.Invoice_date,mci.subject_matter_insured,mci.marks_number,mci.vessel_name,mci.Consignee_name,mci.Consignee_add,mci.Financier_name,pmpd.cover as cover,l.createdby,apr.COI_url
from lead_details l 
join master_customer mc on mc.lead_id=l.lead_id
join proposal_policy pp on pp.lead_id=l.lead_id
join proposal_details pd on pd.lead_id=mc.lead_id
join master_policy mp on mp.policy_id=pp.master_policy_id
join master_plan mpp on mpp.plan_id=mp.plan_id
join proposal_payment_details ppd on ppd.lead_id=l.lead_id
join marine_customer_info mci on mci.lead_id=l.lead_id
join policy_member_plan_details pmpd on pmpd.lead_id=l.lead_id
join api_proposal_response apr on apr.lead_id=l.lead_id
where mpp.isactive=1 and mpp.policy_type_id=3 order by l.lead_id desc");
        }
      //  print_r($this->db->last_query());

         // print_r($this->db->last_query());
       //   exit;

        if($query -> num_rows() >= 1)
        {
            $totcount = $query -> num_rows();
            return array("query_result" => $query->result(), "totalRecords" => $totcount);
        }
        else
        {
            return array("totalRecords" => 0);
        }
    }
}

?>