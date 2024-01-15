<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI

require_once PATH_VENDOR.'vendor/autoload.php';


use Dompdf\Dompdf;

class Dashboarddetails extends CI_Controller
{
    function _remap($method_name = 'index')
    {
        if (!method_exists($this, $method_name)) {
            $this->index();
        } else {
            $this->{$method_name}();
        }
    }

    function __construct()
    {
        parent::__construct();
        checklogin();
        $this->RolePermission = getRolePermissions();
        /*  ini_set('display_errors', 1);
          ini_set('display_startup_errors', 1);
          error_reporting(E_ALL);*/
    }

    public function index()
    {
        $result = array();
        //Get all creditors
        $data = array();
        $data['utoken'] = $_SESSION['webpanel']['utoken'];
        $data['role_id'] = $_SESSION['webpanel']['role_id'];
        $data['user_id'] = $_SESSION['webpanel']['employee_id'];
        $data['cid'] = $_GET['cid'];
        //echo $_GET['cid'];exit;
        if ($_SESSION['webpanel']['role_id'] == 3) {
            $getCreditors = curlFunction(SERVICE_URL . '/api/getRoleWiseCreditorsData', $data);
        } else {
            $getCreditors = curlFunction(SERVICE_URL . '/api/getCreditorsData', $data);
        }
        $getCreditors = json_decode($getCreditors, true);
        //echo "<pre>";print_r($getCreditors);exit;
        $result['creditors'] = $getCreditors['Data'];

        //Get all login sm locations
        $data = array();
        $data['utoken'] = $_SESSION['webpanel']['utoken'];
        $getLocations = curlFunction(SERVICE_URL . '/api/getLocationsData', $data);
        $getLocations = json_decode($getLocations, true);
        //echo "<pre>";print_r($getLocations);exit;
        $result['locations'] = $getLocations['Data'];

        //Get all SM
        $data = array();
        $data['utoken'] = $_SESSION['webpanel']['utoken'];
        $data['sm_id'] = $_GET['smid'];
        $getSMs = curlFunction(SERVICE_URL . '/api/getSMData', $data);
        $getSMs = json_decode($getSMs, true);
        //echo "<pre>";print_r($getLocations);exit;
        $result['sm'] = $getSMs['Data'];

        $this->load->view('template/header.php');
        $this->load->view('dashboarddetails/index', $result);
        $this->load->view('template/footer.php');
    }

    function fetch()
    {
        $_GET['utoken'] = $_SESSION['webpanel']['utoken'];
        //echo "get: ".$_GET['cid'];exit;
        $_GET['cid'] = $_GET['cid'];
        $_GET['smid'] = $_GET['smid'];
        if (empty($_GET['sSearch_0'])) {
            $_GET['sSearch_0'] = 'All';
        }
        if (empty($_GET['sSearch_0'])) {
            $_GET['sSearch_0'] = 'Approved';
        } else if (!empty($_GET['sSearch_0']) && $_GET['sSearch_0'] == 'All') {
            $_GET['sSearch_0'] = '';
        } else {
            $_GET['sSearch_0'] = $_GET['sSearch_0'];
        }
        //$_GET['sSearch_0'] = (!empty($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : 'Approved';
        //$_GET['cid'] = 11;
        //$_GET['smid'] = 15;
        /*$url = parse_url($_SERVER['REQUEST_URI']);
        //echo $url['query'];exit;
        parse_str($url['query'], $params);
        $cid = $params['cid'];
        echo $cid;exit;
        */

        $dataListing = curlFunction(SERVICE_URL . '/api/adminDashBoradDetails', $_GET);
        //echo "<pre>";print_r($dataListing);exit;
        $dataListing = json_decode($dataListing, true);

        if ($dataListing['status_code'] == '401') {
            redirect('login');
            exit();
        }

        $result = array();
        $result["sEcho"] = $_GET['sEcho'];

        $result["iTotalRecords"] = $dataListing['Data']['totalRecords'];
        $result["iTotalDisplayRecords"] = $dataListing['Data']['totalRecords'];

        $items = array();


        if (!empty($dataListing['Data']['query_result']) && count($dataListing['Data']['query_result']) > 0) {
            for ($i = 0; $i < sizeof($dataListing['Data']['query_result']); $i++) {
                $temp = array();
                array_push($temp, $dataListing['Data']['query_result'][$i]['trace_id']);
                array_push($temp, date("d-m-Y", strtotime($dataListing['Data']['query_result'][$i]['createdon'])));
                array_push($temp, $dataListing['Data']['query_result'][$i]['first_name']);
                array_push($temp, $dataListing['Data']['query_result'][$i]['last_name']);
                array_push($temp, $dataListing['Data']['query_result'][$i]['plan_name']);
                array_push($temp, $dataListing['Data']['query_result'][$i]['policy_type_name']);
                array_push($temp, $dataListing['Data']['query_result'][$i]['premium']);
                array_push($temp, $dataListing['Data']['query_result'][$i]['COI_numbers']);
                array_push($temp, $dataListing['Data']['query_result'][$i]['sum_insured']);
                array_push($temp, $dataListing['Data']['query_result'][$i]['payment_mode_name']);
                if ($dataListing['Data']['query_result'][$i]['status'] == 'Pending') {
                    array_push($temp, "Proposal Creation");
                } else if ($dataListing['Data']['query_result'][$i]['status'] == 'Client-Approval-Awaiting') {
                    array_push($temp, "Pending at Customer");
                } else if ($dataListing['Data']['query_result'][$i]['status'] == 'Customer-Payment-Awaiting') {
                    array_push($temp, "Pending For Payment");
                } else if ($dataListing['Data']['query_result'][$i]['status'] == 'BO-Approval-Awaiting') {
                    array_push($temp, "Pending Branch Ops Verification");
                } else if ($dataListing['Data']['query_result'][$i]['status'] == 'CO-Approval-Awaiting') {
                    array_push($temp, "Pending Central Ops Verification");
                } else if ($dataListing['Data']['query_result'][$i]['status'] == 'Discrepancy') {
                    array_push($temp, "Discrepancy Raised");
                } else if ($dataListing['Data']['query_result'][$i]['status'] == 'Approved') {
                    array_push($temp, "Issued");
                } else if ($dataListing['Data']['query_result'][$i]['status'] == 'Rejected') {
                    array_push($temp, "Cancelled");
                } else if ($dataListing['Data']['query_result'][$i]['status'] == 'UW-Approval-Awaiting') {
                    array_push($temp, "Pending With Underwriting");
                } else {
                    array_push($temp, $dataListing['Data']['query_result'][$i]['status']);
                }

                $actionCol = "";
                //if(in_array('ProposalView',$this->RolePermission)){
                $actionCol .= '<a href="policyproposal/preview?text=' . rtrim(strtr(base64_encode("id=" . $dataListing['Data']['query_result'][$i]['lead_id']), '+/', '-_'), '=') . '" title="Edit" target="_blank">View Proposal</a>';
                //}
                if ($dataListing['Data']['query_result'][$i]['status'] == 'Customer-Payment-Received') {
                    $actionCol .= ' |<a href="#" onclick="DownloadCOI(\'' . $dataListing['Data']['query_result'][$i]['lead_id'] . '\')"> Download COI </a>';
                }

                array_push($temp, $actionCol);

                array_push($items, $temp);
            }
        }

        $result["aaData"] = $items;
        echo json_encode($result);
        exit;
    }

    function exportexcel()
    {
        $data = array();
        //echo "<pre>";print_r($_POST);exit;
        $data['utoken'] = $_SESSION['webpanel']['utoken'];
        $data['creditor_id'] = ($_POST['creditor_id']) ? $_POST['creditor_id'] : '';
        $data['sm_id'] = ($_POST['sm_id']) ? $_POST['sm_id'] : '';
        $data['status'] = ($_POST['status']) ? $_POST['status'] : '';

        //echo "<pre>";print_r($data);exit;
        $proposal_data = curlFunction(SERVICE_URL . '/api/exportDashBoradDetails', $data);
        $mobiledata = json_decode($proposal_data, true);
        //echo "<pre>";print_r($mobiledata['Data']['query_result']);exit;
        //echo $mobiledata['Data'][0]['trace_id'];exit;
        //echo ABSOLUTE_DOC_ROOT;exit;

        if ($mobiledata['status_code'] != '200') {
            echo json_encode(array('success' => false, 'msg' => $mobiledata['Metadata']['Message']));
            exit;
        } else {

            $fileName = 'dashboardDetailsExcel-' . time() . '.xls';

            $this->load->library('excel');
            //$mobiledata = $data;
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            // set Header
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Lead ID/Trace ID');
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Date');
            $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'First Name');
            $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Last Name');
            $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Plan Type');
            $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Policy Type');
            $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Premium Amount');
            $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Sum Insured');
            $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Payment Option');
            $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Status');

            // set Row
            $rowCount = 2;
            //foreach ($mobiledata as $val)
            for ($i = 0; $i < sizeof($mobiledata['Data']['query_result']); $i++) {
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $mobiledata['Data']['query_result'][$i]['trace_id']);
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, date("d-m-Y", strtotime($mobiledata['Data']['query_result'][$i]['createdon'])));
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $mobiledata['Data']['query_result'][$i]['first_name']);
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $mobiledata['Data']['query_result'][$i]['last_name']);
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $mobiledata['Data']['query_result'][$i]['plan_name']);
                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $mobiledata['Data']['query_result'][$i]['policy_type_name']);
                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $mobiledata['Data']['query_result'][$i]['premium']);
                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $mobiledata['Data']['query_result'][$i]['sum_insured']);
                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $mobiledata['Data']['query_result'][$i]['payment_mode_name']);
                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $mobiledata['Data']['query_result'][$i]['status']);

                $rowCount++;
            }


            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $objWriter->save(ABSOLUTE_DOC_ROOT . '/assets/smdashboardexports/' . $fileName);
            $filepath = FRONT_URL . '/assets/smdashboardexports/' . $fileName;
            echo json_encode(array('success' => true, 'msg' => "Records Generated", 'Data' => $filepath));
            exit;
        }
    }
    function coverbalance()
    {
        $result = array();
        //Get all creditors
        $data = array();
        $data['utoken'] = $_SESSION['webpanel']['utoken'];
        $data['role_id'] = $_SESSION['webpanel']['role_id'];
        $data['user_id'] = $_SESSION['webpanel']['employee_id'];
        $data['cid'] = $_GET['cid'];
        //echo $_GET['cid'];exit;
        if ($_SESSION['webpanel']['role_id'] == 3) {
            $getCreditors = curlFunction(SERVICE_URL . '/api/getRoleWiseCreditorsData', $data);
        } else {
            $getCreditors = curlFunction(SERVICE_URL . '/api/getCreditorsData', $data);
        }
        $getCreditors = json_decode($getCreditors, true);
        //echo "<pre>";print_r($getCreditors);exit;
        $result['creditors'] = $getCreditors['Data'];

        //Get all login sm locations
        $data = array();
        $data['utoken'] = $_SESSION['webpanel']['utoken'];
        $getLocations = curlFunction(SERVICE_URL . '/api/getLocationsData', $data);
        $getLocations = json_decode($getLocations, true);
        //echo "<pre>";print_r($getLocations);exit;
        $result['locations'] = $getLocations['Data'];

        //Get all SM
        $data = array();
        $data['utoken'] = $_SESSION['webpanel']['utoken'];
        $data['sm_id'] = $_GET['smid'];
        $getSMs = curlFunction(SERVICE_URL . '/api/getSMData', $data);
        $getSMs = json_decode($getSMs, true);
        //	echo "<pre>";print_r($getSMs);exit;
        $result['sm'] = $getSMs['Data'];

        $this->load->view('template/header.php');
        $this->load->view('dashboarddetails/coverbalance', $result);
        $this->load->view('template/footer.php');

    }
    function cdbalance()
    {

        $result = array();
        //Get all creditors
        $data = array();
        $data['utoken'] = $_SESSION['webpanel']['utoken'];
        $data['role_id'] = $_SESSION['webpanel']['role_id'];
        $data['user_id'] = $_SESSION['webpanel']['employee_id'];
        $data['cid'] = $_GET['cid'];
        //echo $_GET['cid'];exit;
        if ($_SESSION['webpanel']['role_id'] == 3) {
            $getCreditors = curlFunction(SERVICE_URL . '/api/getRoleWiseCreditorsData', $data);
        } else {
            $getCreditors = curlFunction(SERVICE_URL . '/api/getCreditorsData', $data);
        }
        $getCreditors = json_decode($getCreditors, true);
        //echo "<pre>";print_r($getCreditors);exit;


        $result['creditors'] = $getCreditors['Data'];

        //Get all login sm locations
        $data = array();
        $data['utoken'] = $_SESSION['webpanel']['utoken'];
        $getLocations = curlFunction(SERVICE_URL . '/api/getLocationsData', $data);
        $getLocations = json_decode($getLocations, true);
        //echo "<pre>";print_r($getLocations);exit;
        $result['locations'] = $getLocations['Data'];

        //Get all SM
        $data = array();
        $data['utoken'] = $_SESSION['webpanel']['utoken'];
        $data['sm_id'] = $_GET['smid'];
        $getSMs = curlFunction(SERVICE_URL . '/api/getSMData', $data);
        $getSMs = json_decode($getSMs, true);
        //	echo "<pre>";print_r($getSMs);exit;
        $result['sm'] = $getSMs['Data'];

        $this->load->view('template/header.php');
        $this->load->view('dashboarddetails/cdbalance', $result);
        $this->load->view('template/footer.php');

    }
    function fetchcoverBalance()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $_GET['utoken'] = $_SESSION['webpanel']['utoken'];
        //echo "get: ".$_GET['cid'];exit;
        $_GET['cid'] = $_POST['cid'];
        $_GET['plan_id'] = $_POST['plan_id'];
        //print_r();die;
        $_GET['smid'] = $_POST['smid'];
        if(empty($_GET['sSearch_0'] )){
            $_GET['sSearch_0'] ='All';
        }
        if(empty($_GET['sSearch_0'])){
            $_GET['sSearch_0'] = 'Approved';
        }else if(!empty($_GET['sSearch_0']) && $_GET['sSearch_0'] == 'All'){
            $_GET['sSearch_0'] = '';
        }else{
            $_GET['sSearch_0'] = $_GET['sSearch_0'];
        }

        $dataListing = curlFunction(SERVICE_URL.'/api/coverDashBoradDetails',$_GET);
         // echo "<pre>";print_r($dataListing);exit;
        $dataListing = json_decode($dataListing, true);


        if($dataListing['status_code'] == '401'){
            redirect('login');
            exit();
        }

        $result = array();
        //  $result["sEcho"]= $_GET['sEcho'];

        $result["iTotalRecords"] = $dataListing['Data']['totalRecords'];
        $result["iTotalDisplayRecords"]= $dataListing['Data']['totalRecords'];

        $items = array();


        if(!empty($dataListing['Data']['query_result']) && count($dataListing['Data']['query_result']) > 0)
        {
            for($i=0;$i<sizeof($dataListing['Data']['query_result']);$i++)
            {
               // print_r($dataListing['Data']['query_result']);
                $policy_id = $dataListing['Data']['query_result'][$i]['policy_id'];
                $plan_id = $_POST['plan_id'];
                $creditor_id = $_POST['cid'];
                $amount_cancelled=$this->db->query("select sum(mt.amount) as amount_utilzed,count(mt.type_trans) as type_trans from master_cover_credit_debit_transaction mt
  join lead_details ld on ld.lead_id=mt.lead_id where  mt.type_trans = 'Policy Cancelled' and mt.creditor_id='".$_POST['cid']."' and mt.plan_id = '$plan_id' and mt.policy_id = '$policy_id'")->row_array();

                $amount_issuance=$this->db->query("select sum(mt.amount) as amount_utilzed ,count(mt.type_trans) as type_trans from master_cover_credit_debit_transaction mt
  join lead_details ld on ld.lead_id=mt.lead_id where  mt.type_trans = 'Policy Issuance' and mt.creditor_id='".$_POST['cid']."' and mt.plan_id = '$plan_id' and mt.policy_id = '$policy_id'")->row_array();

                $amount_utilzed = $amount_issuance['amount_utilzed'] - $amount_cancelled['amount_utilzed'];

                $coi_issuance = $amount_issuance['type_trans'] - $amount_cancelled['type_trans'];

                $amount_utilzed_all = $this->db->query("select sum(mt.amount) as amount_utilzed from master_cover_credit_debit_transaction mt
  join lead_details ld on ld.lead_id=mt.lead_id where mt.type = 2 and mt.creditor_id='".$_POST['cid']."'and mt.plan_id = '$plan_id' and mt.policy_id = '$policy_id'")->row()->amount_utilzed;



                $amount_deposite = $this->db->query("select sum(mt.amount) as amount_deposite from master_cover_credit_debit_transaction mt
where mt.type=1  and mt.creditor_id='".$_POST['cid']."'and mt.plan_id = '$plan_id' and mt.policy_id = '$policy_id'")->row()->amount_deposite;


                $amount_deposite_all = $this->db->query("select sum(mt.amount) as amount_deposite from master_cover_credit_debit_transaction mt
where mt.type=1  and mt.type_trans != 'Policy Cancelled' and mt.creditor_id='".$_POST['cid']."'and mt.plan_id = '$plan_id' and mt.policy_id = '$policy_id'")->row()->amount_deposite;

                $coi_issuance = $this->db->query("select  count(certificate_number)as COI_numbers from api_proposal_response as apr left join proposal_policy as pp ON apr.lead_id = pp.lead_id where    apr.master_policy_id = '$policy_id' and pp.status != 'Cancelled'")->row_array();
                $temp = array();
                array_push($temp, $dataListing['Data']['query_result'][$i]['creaditor_name'] );
                array_push($temp, $dataListing['Data']['query_result'][$i]['plan_name'] );
                array_push($temp, $dataListing['Data']['query_result'][$i]['policy_sub_type_name'] );
                array_push($temp, $dataListing['Data']['query_result'][$i]['initial_cover'] );
                array_push($temp, (!empty($amount_deposite_all)) ? round($amount_deposite_all,2) : $dataListing['Data']['query_result'][$i]['initial_cover']);
                array_push($temp, $coi_issuance['COI_numbers'] );
                array_push($temp, (!empty($amount_utilzed)) ? round($amount_utilzed,2) : '0');
                array_push($temp, ($amount_deposite)-$amount_utilzed_all );

                if (in_array('AddCoverLimit', $this->RolePermission)) {

                    $actionCol1 = '<button class="btn btn-link" onclick="ViewModal(' . $_POST['cid'] . ",". $_POST['plan_id'].",".$policy_id.')">Enhance Cover</button>';
                }else{
                    $actionCol1='-';
                }
                $actionCol2 ='<button class="btn btn-link" type="button" onclick="openstateModal(' . $_POST['cid'] . ','. $_POST['plan_id'].','.$policy_id.')">Cover Statement</button>';
                //$actionCol3 ='<a href="/dashboarddetails/OpenPdfNew?c_id='.$_POST['cid'].'" target="_blank"><button class="btn btn-link">CD Statement</button></a>';

                array_push($temp, $actionCol1);
                array_push($temp, $actionCol2);

                array_push($items, $temp);
            }

        }
        //  exit;

        $result["aaData"] = $items;
       // print_R($result);exit;
        echo json_encode($result);
        exit;

    }

    function fetchcdBalance()
    {

        $_GET['utoken'] = $_SESSION['webpanel']['utoken'];
        //echo "get: ".$_GET['cid'];exit;
        $_GET['cid'] = $_POST['cid'];
        //print_r();die;
        $_GET['smid'] = $_POST['smid'];
        if(empty($_GET['sSearch_0'] )){
            $_GET['sSearch_0'] ='All';
        }
        if(empty($_GET['sSearch_0'])){
            $_GET['sSearch_0'] = 'Approved';
        }else if(!empty($_GET['sSearch_0']) && $_GET['sSearch_0'] == 'All'){
            $_GET['sSearch_0'] = '';
        }else{
            $_GET['sSearch_0'] = $_GET['sSearch_0'];
        }
        //$_GET['sSearch_0'] = (!empty($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : 'Approved';
        //$_GET['cid'] = 11;
        //$_GET['smid'] = 15;
        /*$url = parse_url($_SERVER['REQUEST_URI']);
        //echo $url['query'];exit;
        parse_str($url['query'], $params);
        $cid = $params['cid'];
        echo $cid;exit;
        */
        //	print_R($_GET);die;
        $dataListing = curlFunction(SERVICE_URL.'/api/cdDashBoradDetails',$_GET);
        //  echo "<pre>";print_r($dataListing);exit;
        $dataListing = json_decode($dataListing, true);


        if($dataListing['status_code'] == '401'){
            redirect('login');
            exit();
        }

        $result = array();
        //  $result["sEcho"]= $_GET['sEcho'];

        $result["iTotalRecords"] = $dataListing['Data']['totalRecords'];
        $result["iTotalDisplayRecords"]= $dataListing['Data']['totalRecords'];

        $items = array();

        $amount_cancelled=$this->db->query("select sum(mt.amount) as amount_utilzed,count(mt.type_trans) as type_trans from master_cd_credit_debit_transaction mt
  join lead_details ld on ld.lead_id=mt.lead_id where  mt.type_trans = 'Policy Cancelled' and  mt.creditor_id=".$_POST['cid'])->row_array();

        $amount_issuance=$this->db->query("select sum(mt.amount) as amount_utilzed ,count(mt.type_trans) as type_trans from master_cd_credit_debit_transaction mt
  join lead_details ld on ld.lead_id=mt.lead_id where  mt.type_trans = 'Policy Issuance' and  mt.creditor_id=".$_POST['cid'])->row_array();

        $amount_utilzed = $amount_issuance['amount_utilzed'] - $amount_cancelled['amount_utilzed'];

        $amount_utilzed_all = $this->db->query("select sum(mt.amount) as amount_utilzed from master_cd_credit_debit_transaction mt
  join lead_details ld on ld.lead_id=mt.lead_id where mt.type = 2 and  mt.creditor_id=".$_POST['cid'])->row()->amount_utilzed;

        $coi_issuance = $amount_issuance['type_trans'] - $amount_cancelled['type_trans'];
        $amount_deposite=$this->db->query("select sum(mt.amount) as amount_deposite from master_cd_credit_debit_transaction mt
where mt.type=1 and mt.creditor_id=".$_POST['cid'])->row()->amount_deposite;

        $amount_deposite_total=$this->db->query("select sum(mt.amount) as amount_deposite from master_cd_credit_debit_transaction mt
where mt.type=1 and mt.type_trans != 'Policy Cancelled' and mt.creditor_id=".$_POST['cid'])->row()->amount_deposite;
        if(!empty($dataListing['Data']['query_result']) && count($dataListing['Data']['query_result']) > 0)
        {
            for($i=0;$i<sizeof($dataListing['Data']['query_result']);$i++)
            {
                $temp = array();
                array_push($temp, $dataListing['Data']['query_result'][$i]['creaditor_name'] );
                array_push($temp, $dataListing['Data']['query_result'][$i]['initial_cd'] );
                array_push($temp, round($amount_deposite_total,2) );
                array_push($temp, $coi_issuance );
                array_push($temp, round($amount_utilzed,2) );
                array_push($temp, round($amount_utilzed,2) );
                array_push($temp, ($amount_deposite)-$amount_utilzed_all);

                $actionCol1 ='<button class="btn btn-link" onclick="ViewDetails('.$_POST['cid'].')">View</button>';
                if (in_array('addcdbalance', $this->RolePermission)) {
                    $actionCol2 = '<button class="btn btn-link" onclick="ViewModal(' . $_POST['cid'] . ')">Add CD</button>';
                }else{
                    $actionCol2='-';
                }
                $actionCol3 ='<button class="btn btn-link" type="button" onclick="openModal('.$_POST['cid'].')">CD Statement</button>';
                //$actionCol3 ='<a href="/dashboarddetails/OpenPdfNew?c_id='.$_POST['cid'].'" target="_blank"><button class="btn btn-link">CD Statement</button></a>';

                array_push($temp, $actionCol1);
                array_push($temp, $actionCol2);
                array_push($temp, $actionCol3);

                array_push($items, $temp);
            }

        }
        //  exit;

        $result["aaData"] = $items;
        //print_R($result);exit;
        echo json_encode($result);
        exit;

    }

    function fetchcdBalanceTrans()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $_GET['utoken'] = $_SESSION['webpanel']['utoken'];
        //echo "get: ".$_GET['cid'];exit;
        $_GET['cid'] = $_POST['cid'];
        //print_r();die;
        $_GET['smid'] = $_POST['smid'];
        if (empty($_GET['sSearch_0'])) {
            $_GET['sSearch_0'] = 'All';
        }
        if (empty($_GET['sSearch_0'])) {
            $_GET['sSearch_0'] = 'Approved';
        } else if (!empty($_GET['sSearch_0']) && $_GET['sSearch_0'] == 'All') {
            $_GET['sSearch_0'] = '';
        } else {
            $_GET['sSearch_0'] = $_GET['sSearch_0'];
        }
        //$_GET['sSearch_0'] = (!empty($_GET['sSearch_0'])) ? $_GET['sSearch_0'] : 'Approved';
        //$_GET['cid'] = 11;
        //$_GET['smid'] = 15;
        /*$url = parse_url($_SERVER['REQUEST_URI']);
        //echo $url['query'];exit;
        parse_str($url['query'], $params);
        $cid = $params['cid'];
        echo $cid;exit;
        */
        //	print_R($_GET);die;
        $dataListing = curlFunction(SERVICE_URL . '/api/cdDashBoradDetailsTrans', $_GET);
        //  echo "<pre>";print_r($dataListing);exit;
        $dataListing = json_decode($dataListing, true);


        if ($dataListing['status_code'] == '401') {
            redirect('login');
            exit();
        }

        $result = array();
//        $result["sEcho"]= $_GET['sEcho'];

        $result["iTotalRecords"] = $dataListing['Data']['totalRecords'];
        $result["iTotalDisplayRecords"] = $dataListing['Data']['totalRecords'];

        $items = array();


        if (!empty($dataListing['Data']['query_result']) && count($dataListing['Data']['query_result']) > 0) {
            for ($i = 0; $i < sizeof($dataListing['Data']['query_result']); $i++) {
                $temp = array();
                array_push($temp, $dataListing['Data']['query_result'][$i]['creaditor_name'] );
                array_push($temp, $dataListing['Data']['query_result'][$i]['plan_name'] );
                array_push($temp, $dataListing['Data']['query_result'][$i]['policy_type_name'] );
                $subtypearr=explode(',',$dataListing['Data']['query_result'][$i]['policy_sub_type_name']);
                $vals = array_count_values($subtypearr);
                $str='';
                foreach ($vals as $key => $it){
                    //$str .=$key."-".$it."|";
                    $str .=$key."|";
                }
                array_push($temp, rtrim($str,"|") );
                array_push($temp, $dataListing['Data']['query_result'][$i]['COI_numbers'] );
                array_push($temp, $dataListing['Data']['query_result'][$i]['premium'] );
                array_push($temp, $dataListing['Data']['query_result'][$i]['premium'] );

                // array_push($temp, ($dataListing['Data']['query_result'][$i]['initial_cd']+$dataListing['Data']['query_result'][$i]['cd_deposited'])-$dataListing['Data']['query_result'][$i]['premium'] );

                //  $actionCol1 ='<button class="btn btn-link" onclick="ViewDetails('.$dataListing['Data']['query_result'][$i]['creaditor_ids'].')">View</button>';
                //$actionCol2 ='<button class="btn btn-link">Add CD</button>';
                //$actionCol3 ='<button class="btn btn-link">CD Statement</button>';

                // array_push($temp, $actionCol1);
                //  array_push($temp, $actionCol2);
                // array_push($temp, $actionCol3);

                array_push($items, $temp);
            }

        }
        //  exit;

        $result["aaData"] = $items;
        //print_R($result);exit;
        echo json_encode($result);
        exit;
    }

    function addDepositePartner()
    {
        $data = $_POST;
        $res = curlFunction(SERVICE_URL . '/api/addDeposit', $_POST);
        // var_dump($res);die;
        echo($res);
        exit;
    }
    function addDepositeCover()
    {
        $data = $_POST;
        $res = curlFunction(SERVICE_URL . '/api/addCover', $_POST);
        // var_dump($res);die;
        echo($res);
        exit;
    }
    function sortFunction($a, $b)
    {
        return strtotime($a["date"]) - strtotime($b["date"]);
    }
    function OpenPdfCoverNew(){
        /*ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/
        ini_set('memory_limit','256M');
        $cid=$_GET['c_id'];
        $plan_id=$_GET['plan_state_id'];
        $policy_id =$_GET['policy_state_id'];
        $start_date=$_GET['stDate'];
        $end_date=$_GET['endDate'];
        $query=$this->db->query("select * from master_policy where policy_id=".$policy_id)->row();
        $query_cre=$this->db->query("select creaditor_name from master_ceditors where creditor_id= ".$cid)->row();


        $quu=$this->db->query("select sum(amount) as amount,count(date) as coi_count,date(date) as created_date,type_trans,type 
from master_cover_credit_debit_transaction mt where mt.creditor_id= '$cid' AND mt.plan_id = '$plan_id' AND mt.policy_id= '$policy_id' AND date(date) >= '$start_date' 
AND date(date) <= '$end_date' group by date(date),type")->result_array();

        $amount_utilzed=$this->db->query("select sum(mt.amount) as amount_utilzed from master_cover_credit_debit_transaction mt
  join lead_details ld on ld.lead_id=mt.lead_id where mt.type=2 and mt.creditor_id='$cid' AND mt.plan_id = '$plan_id' AND mt.policy_id= '$policy_id'")->row()->amount_utilzed;
        $amount_deposite=$this->db->query("select sum(mt.amount) as amount_deposite from master_cover_credit_debit_transaction mt
where mt.type=1 and mt.creditor_id= '$cid' AND mt.plan_id = '$plan_id' AND mt.policy_id= '$policy_id'")->row()->amount_deposite;
        $tbody="";


        $array1 = array(
            "Plan", "Transaction Date", "Transaction Type", "Cover", "Entry Type", "COI number", "COI issued on"
        );
        $initial=$query->initial_cd;
        $bal=$initial;
        foreach ($quu as $dnew){
            $lead_id=trim($dnew['lead_id']);

            if($dnew['type_trans'] == "Policy Issuance"){
                $dnew['transaction']="Coi Issued";
            }else{
                $dnew['transaction']=$dnew['type_trans'];
            }
            if($dnew['type'] == "1"){
                $dnew['coi_count']='-';
                $dnew['Entry_type']="Credit";
                $balance += $dnew['amount'];
            }else{
                $dnew['Entry_type']="Debit";
                $balance -= $dnew['amount'];
            }
            $bal=$balance;


            $tbody .= "
            <tr>
            <td>".date('d-m-Y',strtotime($dnew['created_date']))."</td>
            <td>".$dnew['coi_count']."</td>
            <td>".$dnew['type_trans']."</td>
            <td>".$dnew['Entry_type']."</td>
            <td>".$dnew['Cover']."</td>
            <td>".$bal."</td>
</tr>
            ";
        }
        $total_amount=$amount_deposite;
        $remain_amount=$total_amount-$amount_utilzed;

        $html='<html>
<head>
<style>
.tbl {background-color:#000;}
.tbl td,th,caption{background-color:#fff}
tr{text-align: center}
</style>
</head>
<body>
<center><h1>CD Statement</h1></center>
<div class="col-md-12">
<div class="col-md-6">
<p>Partner Name : '.$query_cre->creaditor_name.'</p>
<p>Initial Cover Amount : '.$query->initial_cover.'</p>
<p>Cover Utilised : '.$amount_utilzed.'</p>
<p>Cover Balance : '.$remain_amount.'</p>
</div>
</div>
<table cellspacing="3" class="tbl">
<thead>
<tr>
<th>Transaction Date</th>
<th>COI Issued</th>
<th>Transaction Type</th>
<th>Debit/Credit</th>
<th>Cover</th>
<th>Cover balance</th>
</tr>
</thead>
<tbody>
'.$tbody.'
</tbody>
</table>
</body>
</html>';

        define("DOMPDF_ENABLE_REMOTE", false);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4','landscape');
        $dompdf->render();
        $dompdf->stream("",array("Attachment" => false));
    }

    function OpenExcelCoverNew(){
        /*ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/
        ini_set('memory_limit','256M');
        $cid=$_GET['c_id'];
        $plan_id=$_GET['plan_state_id'];
        $policy_id =$_GET['policy_state_id'];
        $start_date=$_GET['stDate'];
        $end_date=$_GET['endDate'];
        $query=$this->db->query("select * from master_policy where policy_id=".$policy_id)->row();

        $query_cre=$this->db->query("select * from master_ceditors where creditor_id=".$cid)->row();

        $quu=$this->db->query("select sum(amount) as amount,count(date) as coi_count,date(date) as created_date,type_trans,type 
from master_cover_credit_debit_transaction mt where mt.creditor_id= '$cid' AND mt.plan_id = '$plan_id' AND mt.policy_id= '$policy_id' AND date(date) >= '$start_date' 
AND date(date) <= '$end_date' group by date(date),type")->result_array();

        $amount_utilzed=$this->db->query("select sum(mt.amount) as amount_utilzed from master_cover_credit_debit_transaction mt
  join lead_details ld on ld.lead_id=mt.lead_id where mt.type=2 and mt.creditor_id='$cid' AND mt.plan_id = '$plan_id' AND mt.policy_id= '$policy_id'")->row()->amount_utilzed;
        $amount_deposite=$this->db->query("select sum(mt.amount) as amount_deposite from master_cover_credit_debit_transaction mt
where mt.type=1 and mt.creditor_id= '$cid' AND mt.plan_id = '$plan_id' AND mt.policy_id= '$policy_id'")->row()->amount_deposite;
        $tbody="";

        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $array1 = array(
            "Plan", "Transaction Date", "Transaction Type", "Premium Amount", "Entry Type", "COI number", "COI issued on"
        );


        $total_amount=$amount_deposite;
        $remain_amount=$total_amount-$amount_utilzed;
        $cnt1 = 1;
        $char1 = 'A';
        foreach ($array1 as $a1) {
            $objPHPExcel->getActiveSheet()->SetCellValue($char1 . $cnt1, $a1);
            $char1++;
        }

        $cnt2=2;
        foreach ($quu as $dnew){
            $lead_id=trim($dnew['lead_id']);
            $coi_number='';
            $created_date='';
            if(!empty($lead_id) && ($dnew['type_trans'] == "Policy Issuance" || $dnew['type_trans'] == "Policy Updated"|| $dnew['type_trans'] == "Policy Cancelled")){
                $qqr=$this->db->query("select certificate_number,created_date from api_proposal_response where lead_id=".$lead_id);
                if($this->db->affected_rows() > 0){
                    $qqr=$qqr->row();
                    $coi_number=$qqr->certificate_number;
                    $created_date=date('d-m-Y',strtotime($qqr->created_date));
                }else{
                    $coi_number='';
                    $created_date='';
                }

            }
            if($dnew['type_trans'] == "Policy Issuance"){
                $dnew['transaction']="Coi Issued";
            }else{
                $dnew['transaction']=$dnew['type_trans'];
            }
            if($dnew['type'] == "1"){
                $dnew['Entry_type']="Credit";
            }else{
                $dnew['Entry_type']="Debit";
            }
            $arr=array($dnew['transaction'],date('d-m-Y',strtotime($dnew['date'])),$dnew['type_trans'],$dnew['amount'],$dnew['Entry_type'],$coi_number,$created_date);
            $char2='A';
            foreach ($arr as $a){
                $objPHPExcel->getActiveSheet()->SetCellValue($char2 . $cnt2, $a);
                $char2++;

            }
            $cnt2++;
        }
//exit;
        $filename = "CDBalance_data.xls";
        // echo $filename;die;
        ob_end_clean();
        header("Content-Disposition: attachment; filename=$filename");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(str_replace(__FILE__, FCPATH."/assets/" . $filename, __FILE__));
        $f = FCPATH . '/assets/' . $filename;
        //file_put_contents($f);
        //  ob_end_clean();
        header("Content-Type: application/octet-stream; ");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ". filesize($f).";");
        header("Content-disposition: attachment; filename=" . $filename);
        readfile($f);
        die();
    }

    function OpenPdfNew(){
        /*ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/
        ini_set('memory_limit','256M');
        $cid=$_GET['c_id'];
        $start_date=$_GET['stDate'];
        $end_date=$_GET['endDate'];
        $query=$this->db->query("select * from master_ceditors where creditor_id=".$cid)->row();


        $quu=$this->db->query("select sum(amount) as amount,count(date) as coi_count,date(date) as created_date,type_trans,type 
from master_cd_credit_debit_transaction mt where mt.creditor_id=".$cid." AND date(date) >= '".$start_date."' 
AND date(date) <= '".$end_date."' group by date(date),type")->result_array();

        $amount_utilzed=$this->db->query("select sum(mt.amount) as amount_utilzed from master_cd_credit_debit_transaction mt
  join lead_details ld on ld.lead_id=mt.lead_id where mt.type=2 and mt.creditor_id=".$cid)->row()->amount_utilzed;
        $amount_deposite=$this->db->query("select sum(mt.amount) as amount_deposite from master_cd_credit_debit_transaction mt
where mt.type=1 and mt.creditor_id=".$cid)->row()->amount_deposite;
        $tbody="";

        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $array1 = array(
            "Action", "Transaction Date","Transaction Type", "Premium Amount", "Entry Type", "COI number", "COI issued on"
        );
        $initial=$query->initial_cd;
        $bal=$initial;
        foreach ($quu as $dnew){
            $lead_id=trim($dnew['lead_id']);
            if($dnew['type_trans'] == "Policy Issuance"){
                $dnew['transaction']="Coi Issued";
            }else{
                $dnew['transaction']=$dnew['type_trans'];
            }
            if($dnew['type'] == "1"){
                $dnew['coi_count']='-';
                $dnew['Entry_type']="Credit";
                $balance += $dnew['amount'];
            }else{
                $dnew['Entry_type']="Debit";
                $balance -= $dnew['amount'];
            }
            $bal=$balance;


            $tbody .= "
            <tr>
            <td>".date('d-m-Y',strtotime($dnew['created_date']))."</td>
            <td>".$dnew['coi_count']."</td>
            <td>".$dnew['type_trans']."</td>
            <td>".$dnew['Entry_type']."</td>
            <td>".$dnew['amount']."</td>
</tr>
            ";
        }
        $total_amount=$amount_deposite;
        $remain_amount=$total_amount-$amount_utilzed;

        $html='<html>
<head>
<style>
.tbl {background-color:#000;}
.tbl td,th,caption{background-color:#fff}
tr{text-align: center}
</style>
</head>
<body>
<center><h1>CD Statement</h1></center>
<div class="col-md-12">
<div class="col-md-6">
<p>Partner Name : '.$query->creaditor_name.'</p>
<p>Initial CD Amount : '.$query->initial_cd.'</p>
<p>CD Utilised : '.$amount_utilzed.'</p>
<p>CD Balance : '.$remain_amount.'</p>
</div>
</div>
<table cellspacing="3" class="tbl">
<thead>
<tr>
<th>Transaction Date</th>
<th>COI Issued</th>
<th>Transaction Type</th>
<th>Debit/Credit</th>
<th>Amount in Rs.</th>
</tr>
</thead>
<tbody>
'.$tbody.'
</tbody>
</table>
</body>
</html>';

        define("DOMPDF_ENABLE_REMOTE", false);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4','landscape');
        $dompdf->render();
        $dompdf->stream("",array("Attachment" => false));
    }

    function OpenExcelNew(){
        /*ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/
        ini_set('memory_limit','256M');
        $cid=$_GET['c_id'];
        $start_date=$_GET['stDate'];
        $end_date=$_GET['endDate'];
        $query=$this->db->query("select * from master_ceditors where creditor_id=".$cid)->row();

        $quu=$this->db->query("select mt.* from master_cd_credit_debit_transaction mt where mt.creditor_id=".$cid." AND date(date) >= '".$start_date."' 
AND date(date) <= '".$end_date."'")->result_array();

        $amount_utilzed=$this->db->query("select sum(mt.amount) as amount_utilzed from master_cd_credit_debit_transaction mt
  join lead_details ld on ld.lead_id=mt.lead_id where mt.type=2 and mt.creditor_id=".$cid)->row()->amount_utilzed;
        $amount_deposite=$this->db->query("select sum(mt.amount) as amount_deposite from master_cd_credit_debit_transaction mt
where mt.type=1 and mt.creditor_id=".$cid)->row()->amount_deposite;
        $tbody="";

        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $array1 = array(
            "Action", "Transaction Date","Unique Id", "Transaction Type", "Premium Amount", "Entry Type", "COI number", "COI issued on"
        );


        $total_amount=$amount_deposite;
        $remain_amount=$total_amount-$amount_utilzed;
        $cnt1 = 1;
        $char1 = 'A';
        foreach ($array1 as $a1) {
            $objPHPExcel->getActiveSheet()->SetCellValue($char1 . $cnt1, $a1);
            $char1++;
        }

        $cnt2=2;
        foreach ($quu as $dnew){
            $lead_id=trim($dnew['lead_id']);
            $unique_id = $this->db->query("select unique_id from lead_details  where lead_id = '$lead_id'")->row_array();

            $coi_number='';
            $created_date='';
            if(!empty($lead_id) && ($dnew['type_trans'] == "Policy Issuance" || $dnew['type_trans'] == "Policy Updated"|| $dnew['type_trans'] == "Policy Cancelled")){
                $qqr=$this->db->query("select certificate_number,created_date from api_proposal_response where lead_id=".$lead_id);
                if($this->db->affected_rows() > 0){
                    $qqr=$qqr->row();
                    $coi_number=$qqr->certificate_number;
                    $created_date=date('d-m-Y',strtotime($qqr->created_date));
                }else{
                    $coi_number='';
                    $created_date='';
                }

            }
            if($dnew['type_trans'] == "Policy Issuance"){
                $dnew['transaction']="Coi Issued";
            }else{
                $dnew['transaction']=$dnew['type_trans'];
            }
            if($dnew['type'] == "1"){
                $dnew['Entry_type']="Credit";
            }else{
                $dnew['Entry_type']="Debit";
            }
            $arr=array($dnew['transaction'],date('d-m-Y',strtotime($dnew['date'])),$unique_id['unique_id'],$dnew['type_trans'],$dnew['amount'],$dnew['Entry_type'],$coi_number,$created_date);
            $char2='A';
            foreach ($arr as $a){
                $objPHPExcel->getActiveSheet()->SetCellValue($char2 . $cnt2, $a);
                $char2++;

            }
            $cnt2++;
        }
//exit;
        $filename = "CDBalance_data.xls";
        // echo $filename;die;
        ob_end_clean();
        header("Content-Disposition: attachment; filename=$filename");
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(str_replace(__FILE__, FCPATH."/assets/" . $filename, __FILE__));
        $f = FCPATH . '/assets/' . $filename;
        //file_put_contents($f);
        //  ob_end_clean();
        header("Content-Type: application/octet-stream; ");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ". filesize($f).";");
        header("Content-disposition: attachment; filename=" . $filename);
        readfile($f);
        die();


    }

    function OpenPdfNewOlddd()
    {
        $cid = $_GET['c_id'];
        $start_date = $_GET['stDate'];
        $end_date = $_GET['endDate'];
        $query = $this->db->query("select * from master_ceditors where creditor_id=" . $cid)->row();

        $quu = $this->db->query("select * from master_cd_credit_debit_transaction where creditor_id=" . $cid)->result_array();
        $tbody = "";
        foreach ($quu as $dnew) {
            $lead_id = trim($dnew['lead_id']);
            $coi_number = '';
            $created_date = '';
            if (!empty($lead_id) && ($dnew['type_trans'] == "Policy Issuance" || $dnew['type_trans'] == "Policy Updated" || $dnew['type_trans'] == "Policy Cancelled")) {
                $qqr = $this->db->query("select certificate_number,created_date from api_proposal_response where lead_id=" . $lead_id)->row();
                $coi_number = $qqr->certificate_number;
                $created_date = date('d-m-Y', strtotime($qqr->created_date));
            }
            if ($dnew['type_trans'] == "Policy Issuance") {
                $dnew['transaction'] = "Coi Issued";
            } else {
                $dnew['transaction'] = $dnew['type_trans'];
            }
            if ($dnew['type'] == "1") {
                $dnew['Entry_type'] = "Credit";
            } else {
                $dnew['Entry_type'] = "Debit";
            }


            $tbody .= "
            <tr>
            <td>" . $dnew['transaction'] . "</td>
            <td>" . date('d-m-Y', strtotime($dnew['date'])) . "</td>
            <td>" . $dnew['type_trans'] . "</td>
            <td>" . $dnew['amount'] . "</td>
            <td>" . $dnew['Entry_type'] . "</td>
            <td>" . $coi_number . "</td>
            <td>" . $created_date . "</td>
</tr>
            ";
        }
        $html = '<html>
<head>
<style>
.tbl {background-color:#000;}
.tbl td,th,caption{background-color:#fff}
tr{text-align: center}
</style>
</head>
<body>
<center><h1>CD Statement</h1></center>
<div class="col-md-12">
<div class="col-md-6">
<p>Partner Name : ' . $query->creaditor_name . '</p>
<p>Initial CD Amount : ' . $query->initial_cd . '</p>
<p>CD Utilised : ' . $query->cd_utilised . '</p>
<p>CD Balance : ' . $query->cd_balance_remain . '</p>
</div>
</div>
<table cellspacing="3" class="tbl">
<thead>
<tr>
<th>Plan</th>
<th>Transaction Date</th>
<th>Transaction Type</th>
<th>Premium Amount</th>
<th>Entry Type</th>
<th>COI number</th>
<th>COI issued on</th>
</tr>
</thead>
<tbody>
' . $tbody . '
</tbody>
</table>
</body>
</html>';
        define("DOMPDF_ENABLE_REMOTE", false);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("", array("Attachment" => false));
    }

    function OpenPdfNewOLD()
    {
        $cid = $_GET['c_id'];
        $start_date = $_GET['stDate'];
        $end_date = $_GET['endDate'];
        $query = $this->db->query("select * from master_ceditors where creditor_id=" . $cid)->row();
        $query_data_deposite = $this->db->query("select * from cd_deposit where partner_id=" . $cid)->result();

        $dataNew = array();
        foreach ($query_data_deposite as $row) {
            $data = array();
            $data['transaction'] = 'CD Amount Deposit';
            $data['trans_date'] = $row->trans_date;
            $data['trans_type'] = 'CD Deposited';
            $data['amount'] = $row->amount;
            $data['Entry_type'] = 'Credit';
            $data['coinumber'] = '';
            $data['EndDate'] = '';
            array_push($dataNew, $data);
        }
        $query_COI = $this->db->query("select gross_premium,created_date,certificate_number,created_date from api_proposal_response apr
LEFT JOIN `proposal_payment_details` as `ppd` ON `ppd`.`lead_id`  = `apr`.`lead_id`
 where master_policy_id in (select group_concat(mp.policy_id) from master_policy mp where mp.creditor_id=" . $cid . ")
  and (date(apr.created_date) >= date('" . $start_date . "') and date(apr.created_date) <= date('" . $end_date . "') )
  and ppd.payment_mode=4")->result();
        //  echo $this->db->last_query();die;
        foreach ($query_COI as $row) {
            $data = array();
            $data['transaction'] = 'Coi Issued';
            $data['trans_date'] = $row->created_date;
            $data['trans_type'] = 'Policy Issuance';
            $data['amount'] = $row->gross_premium;
            $data['Entry_type'] = 'Debit';
            $data['coinumber'] = $row->certificate_number;
            $data['EndDate'] = $row->created_date;
            array_push($dataNew, $data);
        }
        usort($dataNew, "sortFunction");
        $tbody = "";
        foreach ($dataNew as $dnew) {
            $tbody .= "
            <tr>
            <td>" . $dnew['transaction'] . "</td>
            <td>" . $dnew['trans_date'] . "</td>
            <td>" . $dnew['trans_type'] . "</td>
            <td>" . $dnew['amount'] . "</td>
            <td>" . $dnew['Entry_type'] . "</td>
            <td>" . $dnew['coinumber'] . "</td>
            <td>" . $dnew['EndDate'] . "</td>
</tr>
            ";
        }
        $html = '<html>
<head>
<style>
.tbl {background-color:#000;}
.tbl td,th,caption{background-color:#fff}
tr{text-align: center}
</style>
</head>
<body>
<center><h1>CD Statement</h1></center>
<div class="col-md-12">
<div class="col-md-6">
<p>Partner Name : ' . $query->creaditor_name . '</p>
<p>Initial CD Amount : ' . $query->initial_cd . '</p>
<p>CD Utilised : ' . $query->cd_utilised . '</p>
<p>CD Balance : ' . $query->cd_balance_remain . '</p>
</div>
</div>
<table cellspacing="3" class="tbl">
<thead>
<tr>
<th>Plan</th>
<th>Transaction Date</th>
<th>Transaction Type</th>
<th>Premium Amount</th>
<th>Entry Type</th>
<th>COI number</th>
<th>COI issued on</th>
</tr>
</thead>
<tbody>
' . $tbody . '
</tbody>
</table>
</body>
</html>';
        define("DOMPDF_ENABLE_REMOTE", false);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("", array("Attachment" => false));
    }
}

?>