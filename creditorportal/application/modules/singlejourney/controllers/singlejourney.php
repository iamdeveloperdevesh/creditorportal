<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Singlejourney extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		checklogin();
		$this->RolePermission = getRolePermissions();
	}
 
	function index()
	{
		// echo 111;exit;
		$this->load->view('template/header.php');
		$this->load->view('singlejourney/index');
		$this->load->view('template/footer.php');
	}

    function fetch()
    {
        $_GET['utoken'] = $_SESSION['webpanel']['utoken'];
        //	echo "<pre>GET ";print_r($_GET['i_type']);exit;
        $dataListing = curlFunction(SERVICE_URL.'/api/singlejourneyListing',$_GET);
        $dataListing = json_decode($dataListing, true);
        // echo "<pre>";print_r($dataListing);exit;
        if($dataListing['status_code'] == '401'){
            //echo "in condition";
            redirect('login');
            exit();
        }

        $result = array();
        $result["sEcho"]= $_GET['sEcho'];

        $result["iTotalRecords"] = $dataListing['Data']['totalRecords'];	//iTotalRecords get no of total recors
        $result["iTotalDisplayRecords"]= $dataListing['Data']['totalRecords']; //iTotalDisplayRecords for display the no of records in data table.

        $items = array();

        if(!empty($dataListing['Data']['query_result']) && count($dataListing['Data']['query_result']) > 0)
        {
            for($i=0;$i<sizeof($dataListing['Data']['query_result']);$i++)
            {
                $temp = array();
                array_push($temp, $dataListing['Data']['query_result'][$i]['creaditor_name'] );
                if($_GET['sSearch_1'] == 2){
                    array_push($temp, base_url().'GadgetInsurance?'.$dataListing['Data']['query_result'][$i]['URL'] );
                }else{
                    array_push($temp, base_url().'customerportal?'.$dataListing['Data']['query_result'][$i]['URL'] );
                }


                if ($dataListing['Data']['query_result'][$i]['is_active'] == 1) {
                    array_push($temp, 'Active');
                } else {
                    array_push($temp, 'In-Active');
                }

                $actionCol = "";

                $actionCol .='<a href="singlejourney/addEdit?text='.rtrim(strtr(base64_encode("id=".$dataListing['Data']['query_result'][$i]['id'] ), '+/', '-_'), '=').'" title="Edit"><span class="spn-9"><i class="ti-pencil"></i></span></a>';



                array_push($temp, $actionCol);
                array_push($items, $temp);
            }
        }

        $result["aaData"] = $items;
        echo json_encode($result);
        exit;
    }
 
	function addEdit($id=NULL)
	{
		$record_id = "";
		//print_r($_GET);
		if(!empty($_GET['text']) && isset($_GET['text'])){
			$varr=base64_decode(strtr($_GET['text'], '-_', '+/'));	
			parse_str($varr,$url_prams);
			$record_id = $url_prams['id'];
		}
		
		$result = array();
		
		if(!empty($record_id)){
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['id'] = $record_id;
			$checkDetails = curlFunction(SERVICE_URL.'/api/getSingleJourneyData',$data);
			$checkDetails = json_decode($checkDetails, true);
			//echo "<pre>";print_r($checkDetails);exit;
			$result['getCreditorDetails'] = json_decode(curlFunction(SERVICE_URL.'/api/getCreditorsDetails',[]),TRUE);
			$result['getDetails'] = $checkDetails['Data'];
			
		}else{
			$result['getCreditorDetails'] = json_decode(curlFunction(SERVICE_URL.'/api/getCreditorsDetails',[]),TRUE);
			$result['getDetails'] = array();
		}
		// print_r($result);exit;
		//echo $user_id;
		$this->load->view('template/header.php');
		$this->load->view('singlejourney/addEdit',$result);
		$this->load->view('template/footer.php');
	}
 
	function submitForm()
	{
		/*print_r($_POST);
		exit;*/
		
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
			//check duplicate record.
			$checkdata = array();
			$checkdata['creditor_id'] = $_POST['creditor_id'];
			$checkdata['is_active'] = $_POST['is_active'];
			$checkdata['utoken'] = $_SESSION['webpanel']['utoken'];
			if(isset($_POST['single_journey_id']) && $_POST['single_journey_id'] > 0){
				$checkdata['id'] = $_POST['single_journey_id'];
			}
			
			$checkDetails = curlFunction(SERVICE_URL.'/api/checkDuplicateSingleJourney',$checkdata);
			//echo "<pre>";print_r($checkDetails);exit;
			$checkDetails = json_decode($checkDetails, true);
			
			if($checkDetails['status_code'] == '200')
			{
				echo json_encode(array("success"=>false, 'msg'=>'Record Already Present!'));
				exit;
			}
			
			
			$data = array();
			$data['utoken'] = $_SESSION['webpanel']['utoken'];
			$data['creditor_id'] = (!empty($_POST['creditor_id'])) ? $_POST['creditor_id'] : '';
			$data['id'] = (!empty($_POST['single_journey_id'])) ? $_POST['single_journey_id'] : '';
			$data['isactive'] = (!empty($_POST['is_active'])) ? $_POST['is_active'] : '';
			$data['login_user_id'] = $_SESSION["webpanel"]['employee_id'];
			
			$addEdit = curlFunction(SERVICE_URL.'/api/addEditSingleJourney',$data);
			// echo "<pre>";print_r($addEdit);exit;
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
			echo json_encode(array("success"=>false, 'msg'=>'Problem While Add/Edit Record..'));
			exit;
		}
	}
 
}

?>