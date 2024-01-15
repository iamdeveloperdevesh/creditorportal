<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
class Discrepancyproposals extends CI_Controller 
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
		$this->load->view('discrepancyproposals/index',$result);
		$this->load->view('template/footer.php');
	}
 
	function fetch()
	{
		$_GET['utoken'] = $_SESSION['webpanel']['utoken'];
		$_GET['role_id'] = $_SESSION['webpanel']['role_id'];
		$_GET['user_id'] = $_SESSION['webpanel']['employee_id'];
		//echo "<pre>GET ";print_r($_GET);exit;
		$userListing = curlFunction(SERVICE_URL.'/api/discrepancyProposalListing',$_GET);
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
				//array_push($temp, $userListing['Data']['query_result'][$i]['status'] );
				
				array_push($temp, $userListing['Data']['query_result'][$i]['discrepancy_type_val'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['discrepancy_subtype_val'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['remark'] );
				array_push($temp, $userListing['Data']['query_result'][$i]['payment_mode_name'] );

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
				array_push($temp, date("d-m-Y H:i:s", strtotime($userListing['Data']['query_result'][$i]['updatedon'])) );
				
				$actionCol = "";
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
					
					
				}else{
					if(in_array('ProposalView',$this->RolePermission)){
						$actionCol .='<a href="policyproposal/addedit?text='.rtrim(strtr(base64_encode("id=".$userListing['Data']['query_result'][$i]['lead_id'] ), '+/', '-_'), '=').'" title="Edit">View Proposal</a>';
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

}
?>