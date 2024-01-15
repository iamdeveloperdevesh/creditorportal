<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
class Customerleads extends CI_Controller 
{
	function __construct()
	{
	    //echo 123;die;
		parent::__construct();
		checklogin();
		$this->RolePermission = getRolePermissions();
	}
 
	function index()
	{
		$result = array();
		$this->load->view('template/header.php');
		$this->load->view('customerleads/index',$result);
		$this->load->view('template/footer.php');
	}
 
	function fetch()
	{//echo 123;die;
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		$_GET['role_id'] = $_SESSION['webpanel']['role_id'];
		$_GET['user_id'] = $_SESSION['webpanel']['employee_id'];

        $getCreditors = curlFunction(SERVICE_URL.'/api/getRoleWiseCreditorsData',$_GET);
        $getCreditors = json_decode($getCreditors, true);
       // print_r($getCreditors);die;
        $creditor_id=$getCreditors['Data'][0]['creditor_id'];
        $_GET['creditor_id'] = $creditor_id;
     //   echo "<pre>GET ";print_r($_GET);exit;
        $userListing = curlFunction(SERVICE_URL.'/api/leadListing',$_GET);
        //print_r($userListing);die;
		$userListing = json_decode($userListing, true);
		//echo 123;die;
        /*ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/
	//	print_r($userListing);exit;
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
				if( $userListing['Data']['query_result'][$i]['is_api_lead'] == 1){
                    array_push($temp, "Yes" );
                }else{
                    array_push($temp, "No" );
                }
				array_push($temp, $userListing['Data']['query_result'][$i]['plan_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['creaditor_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['employee_full_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['full_name'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['mobile_no'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['email_id'] );
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
				}else if($userListing['Data']['query_result'][$i]['status'] =='Rejected'){
					array_push($temp, "Cancelled" );
				}else if($userListing['Data']['query_result'][$i]['status'] =='UW-Approval-Awaiting'){
					array_push($temp, "Pending With Underwriting" );
				}else if($userListing['Data']['query_result'][$i]['status'] =='Customer-Payment-Received'){
					array_push($temp, "Payment Received" );
				}else{
					array_push($temp, $userListing['Data']['query_result'][$i]['status']);
				}
				
				$actionCol = "";
				/*if(in_array('ProposalAdd',$this->RolePermission)){
					$actionCol .='<a href="policyproposal/addedit?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">Create Proposal</a>';
				}*/
				$lead_id_enc = encrypt_decrypt_password($userListing['Data']['query_result'][$i]['lead_id'], 'E');

				if($userListing['Data']['query_result'][$i]['payment_status'] == 'Success'){
					if(in_array('ProposalView',$this->RolePermission)){
                        if( $userListing['Data']['query_result'][$i]['is_api_lead'] == 1){
                            $actionCol .='<a href="customerleads/view_proposal_single?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';

                        }else{
                            $actionCol .='<a href="policyproposal/preview?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';
                        }
                      //  echo $userListing['Data']['query_result'][$i]['coi_type'];
                        //coi_type
                        if($userListing['Data']['query_result'][$i]['coi_type'] == 1){

                            $actionCol .=' |<a href="#" onclick="DownloadCOI(\''.$userListing['Data']['query_result'][$i]['lead_id'].'\',\''.$userListing['Data']['query_result'][$i]['policy_type_id'].'\')"> Download COI </a>';
                        }else{



                            //COI_url is_single_coi
                            $is_single_coi=$userListing['Data']['query_result'][$i]['is_single_coi'];

                            if(!empty($userListing['Data']['query_result'][$i]['COI_url']) && $userListing['Data']['query_result'][$i]['COI_url'] != null){
                                if($is_single_coi == 1){
                                    $coi_url=explode(",",$userListing['Data']['query_result'][$i]['COI_url']);
                                    $actionCol .=' |<a target="_blank" href="'.$coi_url[0].'" > Download COI </a>';
                                }else{
                                    $coi_url=explode(",",$userListing['Data']['query_result'][$i]['COI_url']);
                                    $certificate_number=explode(",",$userListing['Data']['query_result'][$i]['certificate_number']);
                                    $arr=array();
                                    foreach ($coi_url as $coi){
                                        $arr[]=$coi;
                                    }
                                    $arr=array_unique($arr);
                                    $certificate_numberarr=array_unique($certificate_number);
                                    $url=implode(",",$arr);
                                    $coinum=implode(",",$certificate_numberarr);
                                    $actionCol .=' |<a href="#" onclick="download_allFiles(\''.$url.'\',\''.$coinum.'\');"  > Download COI </a>';
                                  //  $actionCol .=' |<a  target="_blank" href="#" onclick="window.open(\''.$arr[0].'\');window.open(\''.$arr[1].'\')"  > Download COI </a>';
                                }
                            }
                            else{
                                if($userListing['Data']['query_result'][$i]['ic_api'] == 1 && $userListing['Data']['query_result'][$i]['coi_download'] == 1 && !empty($userListing['Data']['query_result'][$i]['certificate_number']))
                                {
                                    $actionCol .=' |<a href="#" onclick="DownloadCOI(\''.$userListing['Data']['query_result'][$i]['lead_id'].'\',\''.$userListing['Data']['query_result'][$i]['policy_type_id'].'\')"> Rehit COI </a>';

                                }
                            }
                        }

					}
				}else{
					if($userListing['Data']['query_result'][$i]['status'] == 'Pending'){
						if(in_array('ProposalAdd',$this->RolePermission)){
                            if( $userListing['Data']['query_result'][$i]['is_api_lead'] == 1){
                                $actionCol .='<a href="customerleads/view_proposal_single?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';

                            }else {
                                $actionCol .= '<a href="policyproposal/addedit?text=' . rtrim(strtr(base64_encode("id=" . $userListing['Data']['query_result'][$i]['lead_id']), '+/', '-_'), '=') . '" title="Edit">Add Proposal</a>';
                            }
                            }
					}else if($userListing['Data']['query_result'][$i]['status'] == 'In-Progress' || $userListing['Data']['query_result'][$i]['status'] == 'Discrepancy'){
						
						if(in_array('ProposalView',$this->RolePermission)){
                            if( $userListing['Data']['query_result'][$i]['is_api_lead'] == 1){
                                $actionCol .='<a href="customerleads/view_proposal_single?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';

                            }else {
                                $actionCol .= '<a href="policyproposal/preview?text=' . rtrim(strtr(base64_encode("id=" . $userListing['Data']['query_result'][$i]['lead_id']), '+/', '-_'), '=') . '" title="Edit">View Proposal</a>';
                            }
                            }
						
						if(in_array('ProposalAdd',$this->RolePermission)){
                            if( $userListing['Data']['query_result'][$i]['is_api_lead'] == 1){
                                $actionCol .='<a href="customerleads/view_proposal_single?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';

                            }else {
                                $actionCol .= ' | <a href="policyproposal/addedit?text=' . rtrim(strtr(base64_encode("id=" . $userListing['Data']['query_result'][$i]['lead_id']), '+/', '-_'), '=') . '" title="Edit">Edit Proposal</a>';
                            }
						}
						
						
					}else if($userListing['Data']['query_result'][$i]['status'] == 'Client-Approval-Awaiting'){
						if(in_array('ProposalView',$this->RolePermission)){
                            if( $userListing['Data']['query_result'][$i]['is_api_lead'] == 1){
                                $actionCol .='<a href="customerleads/view_proposal_single?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';

                            }else {
                                $actionCol .= '<a href="policyproposal/preview?text=' . rtrim(strtr(base64_encode("id=" . $userListing['Data']['query_result'][$i]['lead_id']), '+/', '-_'), '=') . '" title="Edit">View Proposal</a>';
                            }
						}
						
						if(in_array('ProposalAdd',$this->RolePermission)){
                            if( $userListing['Data']['query_result'][$i]['is_api_lead'] == 1){
                                $actionCol .='<a href="customerleads/view_proposal_single?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';

                            }else {
                                $actionCol .= ' | <a href="policyproposal/addedit?text=' . rtrim(strtr(base64_encode("id=" . $userListing['Data']['query_result'][$i]['lead_id']), '+/', '-_'), '=') . '" title="Edit">Edit Proposal</a>';
                            }
						}
						if($actionCol != ''){

							$actionCol .= ' | ';
						}
						$actionCol .='<a href="javascript:void(0);" class="retrigger-link" data-lead="'.$lead_id_enc.'" title="Retrigger Link">Retrigger Link</a>';
					}else if($userListing['Data']['query_result'][$i]['status'] == 'Customer-Payment-Awaiting'){
            
						if(in_array('ProposalView',$this->RolePermission)){
                            if( $userListing['Data']['query_result'][$i]['is_api_lead'] == 1){
                                $actionCol .='<a href="customerleads/view_proposal_single?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';

                            }else {
                                $actionCol .= '<a href="policyproposal/preview?text=' . rtrim(strtr(base64_encode("id=" . $userListing['Data']['query_result'][$i]['lead_id']), '+/', '-_'), '=') . '" title="Edit">View Proposal</a>';
                            }
						}

						if(in_array('ProposalAdd',$this->RolePermission)){
                            if( $userListing['Data']['query_result'][$i]['is_api_lead'] == 1){
                                $actionCol .='<a href="customerleads/view_proposal_single?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';

                            }else {
                                $actionCol .= ' | <a href="policyproposal/addedit?text=' . rtrim(strtr(base64_encode("id=" . $userListing['Data']['query_result'][$i]['lead_id']), '+/', '-_'), '=') . '" title="Edit">Edit Proposal</a>';
                            }
						}
						if($actionCol != ''){

							$actionCol .= ' | ';
						}
						$actionCol .= '<a href="javascript:void(0);" class="retrigger-link" data-lead="'.$lead_id_enc.'" title="Retrigger Link">Retrigger Link</a>';
					}else if($userListing['Data']['query_result'][$i]['status'] == 'BO-Approval-Awaiting'){
            
						if(in_array('ProposalView',$this->RolePermission)){
                            if( $userListing['Data']['query_result'][$i]['is_api_lead'] == 1){
                                $actionCol .='<a href="customerleads/view_proposal_single?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';

                            }else {
                                $actionCol .= '<a href="policyproposal/preview?text=' . rtrim(strtr(base64_encode("id=" . $userListing['Data']['query_result'][$i]['lead_id']), '+/', '-_'), '=') . '" title="Edit">View Proposal</a>';
                            }
						}

						if(in_array('ProposalAdd',$this->RolePermission)){
                            if( $userListing['Data']['query_result'][$i]['is_api_lead'] == 1){
                                $actionCol .='<a href="customerleads/view_proposal_single?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';

                            }else {
                                $actionCol .= ' | <a href="policyproposal/addedit?text=' . rtrim(strtr(base64_encode("id=" . $userListing['Data']['query_result'][$i]['lead_id']), '+/', '-_'), '=') . '" title="Edit">Edit Proposal</a>';
                            }
						}
						
					}else if($userListing['Data']['query_result'][$i]['status'] == 'CO-Approval-Awaiting'){
            
						if(in_array('ProposalView',$this->RolePermission)){
                            if( $userListing['Data']['query_result'][$i]['is_api_lead'] == 1){
                                $actionCol .='<a href="customerleads/view_proposal_single?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';

                            }else {
                                $actionCol .= '<a href="policyproposal/preview?text=' . rtrim(strtr(base64_encode("id=" . $userListing['Data']['query_result'][$i]['lead_id']), '+/', '-_'), '=') . '" title="Edit">View Proposal</a>';
                            }
						}

						if(in_array('ProposalAdd',$this->RolePermission)){
                            if( $userListing['Data']['query_result'][$i]['is_api_lead'] == 1){
                                $actionCol .='<a href="customerleads/view_proposal_single?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';

                            }else {
                                $actionCol .= ' | <a href="policyproposal/addedit?text=' . rtrim(strtr(base64_encode("id=" . $userListing['Data']['query_result'][$i]['lead_id']), '+/', '-_'), '=') . '" title="Edit">Edit Proposal</a>';
                            }
						}
						
					}else if($userListing['Data']['query_result'][$i]['status'] == 'UW-Approval-Awaiting'){
            
						if(in_array('ProposalView',$this->RolePermission)){
                            if( $userListing['Data']['query_result'][$i]['is_api_lead'] == 1){
                                $actionCol .='<a href="customerleads/view_proposal_single?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';

                            }else {
                                $actionCol .= '<a href="policyproposal/preview?text=' . rtrim(strtr(base64_encode("id=" . $userListing['Data']['query_result'][$i]['lead_id']), '+/', '-_'), '=') . '" title="Edit">View Proposal</a>';
                            }
						}

						if(in_array('ProposalAdd',$this->RolePermission)){
                            if( $userListing['Data']['query_result'][$i]['is_api_lead'] == 1){
                                $actionCol .='<a href="customerleads/view_proposal_single?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';

                            }else {
                                $actionCol .= ' | <a href="policyproposal/addedit?text=' . rtrim(strtr(base64_encode("id=" . $userListing['Data']['query_result'][$i]['lead_id']), '+/', '-_'), '=') . '" title="Edit">Edit Proposal</a>';
                            }
						}
						
					}else{
						if(in_array('ProposalView',$this->RolePermission)){
                            if( $userListing['Data']['query_result'][$i]['is_api_lead'] == 1){
                                $actionCol .='<a href="customerleads/view_proposal_single?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';

                            }else {
                                $actionCol .= '<a href="policyproposal/preview?text=' . rtrim(strtr(base64_encode("id=" . $userListing['Data']['query_result'][$i]['lead_id']), '+/', '-_'), '=') . '" title="Edit">View Proposal</a>';
                            }
	
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

	function exportexcel(){
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
		
		//echo "<pre>";print_r($data);exit;
		$proposal_data = curlFunction(SERVICE_URL.'/api/exportLeads',$data);
       // echo "<pre>";print_r($proposal_data);exit;
		$mobiledata = json_decode($proposal_data, true);
		//echo "<pre>";print_r($mobiledata['Data']);exit;
		//echo $mobiledata['Data'][0]['trace_id'];exit;
		//echo ABSOLUTE_DOC_ROOT;exit;
		 
		if($mobiledata['status_code'] != '200'){
			echo json_encode(array('success'=>false, 'msg'=>$mobiledata['Metadata']['Message']));
				exit;
		}else{
		
			$fileName = 'CustomerLeadsExcel-'.time().'.xls'; 
	 
			$this->load->library('excel');
			//$mobiledata = $data;
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			// set Header
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Trace/Lead Id');
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Lan Number');
            $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Plan Type');
            $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Product Name');
            $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Creditor Name');
            $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'SM');
            $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Customer Name');
            $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Mobile');
            $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Email');
            $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Proposal Number');
            $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Status');
			// set Row
			$rowCount = 2;
			//foreach ($mobiledata as $val) 
			for($i=0;$i < sizeof($mobiledata['Data']['query_result']);$i++){
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $mobiledata['Data']['query_result'][$i]['trace_id']);
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $mobiledata['Data']['query_result'][$i]['lan_id']);

                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $mobiledata['Data']['query_result'][$i]['plan_name']);
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $mobiledata['Data']['query_result'][$i]['policy_sub_type_name']);

                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $mobiledata['Data']['query_result'][$i]['creaditor_name']);

                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $mobiledata['Data']['query_result'][$i]['employee_full_name']);
                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $mobiledata['Data']['query_result'][$i]['full_name']);

                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $mobiledata['Data']['query_result'][$i]['mobile_no']);
                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $mobiledata['Data']['query_result'][$i]['email_id']);
                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $mobiledata['Data']['query_result'][$i]['proposal_no']);

                if($mobiledata['Data']['query_result'][$i]['status'] =='Pending'){
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, 'Proposal Creation');
                }else if($mobiledata['Data']['query_result'][$i]['status'] =='Client-Approval-Awaiting'){
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, 'Pending at Customer');
                }else if($mobiledata['Data']['query_result'][$i]['status'] =='Customer-Payment-Awaiting'){
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, 'Pending For Payment');
                }else if($mobiledata['Data']['query_result'][$i]['status'] =='BO-Approval-Awaiting'){
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, 'Pending Branch Ops Verification');
                }else if($mobiledata['Data']['query_result'][$i]['status'] =='CO-Approval-Awaiting'){
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, 'Pending Central Ops Verification');
                }else if($mobiledata['Data']['query_result'][$i]['status'] =='Discrepancy'){
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, 'Discrepancy Raised');
                }else if($mobiledata['Data']['query_result'][$i]['status'] =='Approved'){
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, 'Issued');
                }else if($mobiledata['Data']['query_result'][$i]['status'] =='Rejected'){
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, 'Cancelled');
                }else if($mobiledata['Data']['query_result'][$i]['status'] =='UW-Approval-Awaiting'){
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, 'Pending With Underwriting');
                }else{
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $mobiledata['Data']['query_result'][$i]['status']);
                }

                $rowCount++;
            }
			
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$objWriter->save(ABSOLUTE_DOC_ROOT.'/assets/smdashboardexports/'.$fileName);
			$filepath = FRONT_URL.'/assets/smdashboardexports/'.$fileName;
			echo json_encode(array('success'=>true, 'msg'=>"Records Generated", 'Data'=>$filepath));
			exit;
		}
	}
	
	function addEdit($id=NULL)
	{
		//print_r($_GET);
		$record_id = "";
		if(!empty($_GET['text']) && isset($_GET['text']))
		{
			$varr=base64_decode(strtr($_GET['text'], '-_', '+/'));	
			parse_str($varr,$url_prams);
			$record_id = $url_prams['id'];
		}
		
		//Get all creditors
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['role_id'] = $_SESSION['webpanel']['role_id'];
		$data['user_id'] = $_SESSION['webpanel']['employee_id'];
		if($_SESSION['webpanel']['role_id'] == 3){
			$getCreditors = curlFunction(SERVICE_URL.'/api/getRoleWiseCreditorsData',$data);
		}else{
			$getCreditors = curlFunction(SERVICE_URL.'/api/getCreditorsData',$data);
		}
		$getCreditors = json_decode($getCreditors, true);
		//echo "<pre>";print_r($getCreditors);exit;
		$result['creditors'] = $getCreditors['Data'];
		
		//Get all login sm locations
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['user_id'] = $_SESSION['webpanel']['employee_id'];
		$getSMLocations = curlFunction(SERVICE_URL.'/api/getSMLocations',$data);
		$getSMLocations = json_decode($getSMLocations, true);
		//echo "<pre>";print_r($getSMLocations);exit;
		$result['locations'] = $getSMLocations['Data'];
		
		//Get all SM
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$getSMs = curlFunction(SERVICE_URL.'/api/getSMData',$data);
		$getSMs = json_decode($getSMs, true);
		//echo "<pre>";print_r($getLocations);exit;
		$result['sm'] = $getSMs['Data'];
		
		/*$result['salutation'] = get_enum_values('master_customer','salutation');
		$result['gender'] = get_enum_values('master_customer','gender');*/

		//Get all SM
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$getCustomerEnum = curlFunction(SERVICE_URL.'/api/getCustomerEnumValues',$data);
		$getCustomerEnum = json_decode($getCustomerEnum, true);
		//echo "<pre>";print_r($getCustomerEnum);exit;

		$result['salutation'] = (!empty($getCustomerEnum['Data']['get_customer_salutation'])) ? $getCustomerEnum['Data']['get_customer_salutation'] : [];

		$result['gender'] = (!empty($getCustomerEnum['Data']['get_customer_gender'])) ? $getCustomerEnum['Data']['get_customer_gender'] : [];

		//echo "<pre>";print_r($result);exit;
		
		$this->load->view('template/header.php');
		$this->load->view('customerleads/addEdit',$result);
		$this->load->view('template/footer.php');
	}
	function view_proposal_single(){
        $varr = base64_decode(strtr($_GET['text'], '-_', '+/'));
        parse_str($varr, $url_prams);
        $lead_id = $url_prams['id'];
        $data['lead_id'] = $lead_id;
        $getData = curlFunction(SERVICE_URL.'/api/getSingleLinkData',$data);
      //  print_r($getData);die;
        $getData = json_decode($getData);
        if($getData !== false){
            $customer_data= array();
            $policyDataFinal= array();
            foreach ($getData as $row){
                $customer_data['trace_id']=$row->trace_id;
                $customer_data['lead_id']=$row->lead_id;
                $customer_data['first_name']=$row->first_name;
                $customer_data['last_name']=$row->last_name;
                $customer_data['email_id']=$row->email_id;
                $customer_data['mobile_no']=$row->mobile_no;
                $customer_data['address_line1']=$row->address_line1;
                $customer_data['address_line2']=$row->address_line2;
                $customer_data['address_line3']=$row->address_line3;
                $customer_data['pincode']=$row->pincode;
                $customer_data['no_of_lives']=$row->no_of_lives;
                $customer_data['lan_id']=$row->lan_id;
                $customer_data['loan_amt']=$row->loan_amt;
                $customer_data['loan_disbursement_date']=$row->loan_disbursement_date;
                $customer_data['loan_tenure']=$row->loan_tenure;
                $customer_data['plan_name']=$row->plan_name;
                $customer_data['transaction_number']=$row->transaction_number;
                $customer_data['trans_amount']=$row->trans_amount;
                $customer_data['payment_status']=$row->payment_status;
                $policyData= array();
                $policyData['plan_name']=$row->plan_name;
                $policyData['policy_subtype']=$row->policy_subtype;
                $policyData['premium_amount']=$row->premium_amount;
                $policyData['cover']=$row->cover;
                $policyData['certificate_number']=$row->certificate_number;
                $policyData['proposal_no']=$row->proposal_no;
                $policyData['start_date']=$row->start_date;
                $policyData['end_date']=$row->end_date;
                array_push($policyDataFinal,$policyData);
            }
            $data['customer_data']=$customer_data;
            $data['policyData']=$policyDataFinal;
            $marine_query=$this->db->query("select * from marine_customer_info where lead_id=".$lead_id);
            if($this->db->affected_rows() >0){
                $data['marine_data']=$marine_query->row_array();
            }else{
                $data['marine_data']=false;
            }
        }
        $this->load->view('template/header.php');
        $this->load->view('customerleads/view_proposal_single',$data);
        $this->load->view('template/footer.php');
    }
	public function getPlans() {
		//Get all creditors
		$data = array();
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$data['creditor_id'] = $_POST['creditor_id'];
		$getPlans = curlFunction(SERVICE_URL.'/api/getCreditorsPlansData',$data);
		$getPlans = json_decode($getPlans, true);
		//echo "<pre>";print_r($getPlans);exit;
		$plans = $getPlans['Data'];
		
		$option = '';
		if(!empty($plans)){
			for ($i = 0; $i < sizeof($plans); $i++){
				$option .= '<option value="'.$plans[$i]['plan_id'].'" >'.$plans[$i]['plan_name'].'</option>';
			}
		}
		//echo $option;exit;
		echo json_encode(array("status" => "success", "option" => $option));
		exit;
		
	}
 
	function submitForm()
	{
		//echo "<pre>";print_r($_POST);exit;
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['plan_id'] = (!empty($_POST['plan_id'])) ? $_POST['plan_id'] : '';
			if(!empty($_POST['sm_id'])){
				$data['sm_id'] = (!empty($_POST['sm_id'])) ? $_POST['sm_id'] : '';
			}else{
				$data['sm_id'] = $_SESSION["webpanel"]['employee_id'];
			}
			$data['salutation'] = (!empty($_POST['salutation'])) ? $_POST['salutation'] : '';
			$data['first_name'] = (!empty($_POST['first_name'])) ? $_POST['first_name'] : '';
			$data['middle_name'] = (!empty($_POST['middle_name'])) ? $_POST['middle_name'] : '';
			$data['last_name'] = (!empty($_POST['last_name'])) ? $_POST['last_name'] : '';
			$data['gender'] = (!empty($_POST['gender'])) ? $_POST['gender'] : '';
			$data['dob'] = (!empty($_POST['dob'])) ? $_POST['dob'] : '';
			$data['email_id'] = (!empty($_POST['email_id'])) ? $_POST['email_id'] : '';
			$data['mobile_number'] = (!empty($_POST['mobile_number'])) ? $_POST['mobile_number'] : '';
			$data['login_user_id'] = $_SESSION["webpanel"]['employee_id'];
			
			
			$data['lan_id'] = (!empty($_POST['lan_id'])) ? $_POST['lan_id'] : '';
			$data['portal_id'] = 'Creditor Portal';
			$data['vertical'] = 'Vertical';
			$data['loan_amt'] = (!empty($_POST['loan_amt'])) ? $_POST['loan_amt'] : '';
			$data['tenure'] = (!empty($_POST['tenure'])) ? $_POST['tenure'] : '';
			$data['is_coapplicant'] = (!empty($_POST['is_coapplicant'])) ? $_POST['is_coapplicant'] : '';
			$data['coapplicant_no'] = (!empty($_POST['coapplicant_no'])) ? $_POST['coapplicant_no'] : 0;
			
			$data['lead_location_id'] = (!empty($_POST['location_id'])) ? $_POST['location_id'] : 0;
			
			//echo "<pre>";print_r($data);exit;
			
			$addEdit = curlFunction(SERVICE_URL.'/api/addLead',$data);
			//echo "<pre>";print_r($addEdit);exit;
			$addEdit = json_decode($addEdit, true);
			
			if($addEdit['status_code'] == '200'){
				echo json_encode(array('success'=>true, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}else{
				echo json_encode(array('success'=>false, 'msg'=>$addEdit['Metadata']['Message']));
				exit;
			}
			
		}
		else
		{
			echo json_encode(array('success' => false, 'msg'=>'Problem while updating record.'));
			exit;
		}
	}
 
	//For Delete
	function delRecord($id)
	{
		//$appdResult = $this->adcategorymodel->delrecord("tbl_categories","category_id ",$id);
		$data = array();
		$data['id'] = $id;
		$data['utoken'] = $_SESSION['webpanel']['utoken'];
		$delRecord = curlFunction(SERVICE_URL.'/api/delUser',$data);
		//echo "<pre>";print_r($checkDetails);exit;
		$delRecord = json_decode($delRecord, true);
		 
		if($delRecord['status_code'] == '200'){
			echo "1";
		}else{
			echo "2";
		}	
	}	
	
	public function getPlanDetailsForLead(){

		$_POST['utoken'] = $_SESSION['webpanel']['utoken'];
		$response = curlFunction(SERVICE_URL.'/api2/getPlanDetailsForLead',$_POST);
		$response = json_decode($response, true);
        $plan_id = htmlspecialchars(strip_tags(trim($this->input->post('plan_id'))));
        $query=$this->db->query("select policy_type_id from master_plan where plan_id=".$plan_id)->row()->policy_type_id;
		if($query == 3){
            $result = ['success', false];
        }else{
            if(isset($response['status'])){

                $min_age = date('Y', strtotime('-'.$response['data']['min_age'].' years'));
                $max_age = date('Y', strtotime('-'.$response['data']['max_age'].' years'));

                //$result = ['success' => true, 'min_age' => $min_age, 'max_age' => $max_age, 'tenure' => $response['tenure']];
                $result = ['success' => true, 'min_age' => $min_age, 'max_age' => $max_age];
            }
            else{

                $result = ['success', false];
            }
        }


		echo json_encode($result);
		exit;
	}
}

?>