<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI

require_once PATH_VENDOR.'vendor/autoload.php';


use Dompdf\Dompdf;

class Reports extends CI_Controller
{


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
        $result['getCreditorDetails'] = json_decode(curlFunction(SERVICE_URL.'/api/getCreditorsDetails',[]),TRUE);


        $this->load->view('template/header.php');
        $this->load->view('index',$result);
        $this->load->view('template/footer.php');
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


        $data['creditor_name'] = ($_POST['creditor_name']) ? $_POST['creditor_name'] : '';

        $data['from_date'] = ($_POST['from_date']) ? $_POST['from_date'] : '';
        $data['to_date'] = ($_POST['to_date']) ? $_POST['to_date'] : '';

       // echo "<pre>";print_r($data);exit;
        $proposal_data = curlFunction(SERVICE_URL.'/api/exportreport',$data);
        // echo "<pre>";print_r($proposal_data);exit;
        $mobiledata = json_decode($proposal_data, true);
        //echo "<pre>";print_r($mobiledata['Data']);exit;
        //echo $mobiledata['Data'][0]['trace_id'];exit;
        //echo ABSOLUTE_DOC_ROOT;exit;

        if($mobiledata['status_code'] != '200'){
            echo json_encode(array('success'=>false, 'msg'=>$mobiledata['Metadata']['Message']));
            exit;
        }else{

            $fileName = 'Report-'.time().'.xls';

            $this->load->library('excel');
            //$mobiledata = $data;
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            // set Header
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'S.No.');
            $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Mzapp-Alliance File Reference No.');
            $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Policy Year');
            $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Client name');
            $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Insured cont no.');
            $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Mail ID');
            $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Claim No.');
            $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Policy No');
            $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Policy Period');
            $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Name of insurer');
            $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Insurer cont no.');
            $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Type of policy');
            $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Location of loss');
            $objPHPExcel->getActiveSheet()->SetCellValue('N1', 'Cause of loss');
            $objPHPExcel->getActiveSheet()->SetCellValue('O1', 'Extent of loss');
            $objPHPExcel->getActiveSheet()->SetCellValue('P1', 'Transit From To');
            $objPHPExcel->getActiveSheet()->SetCellValue('Q1', 'Invoice & LR No.');
            $objPHPExcel->getActiveSheet()->SetCellValue('R1', 'Month');
            $objPHPExcel->getActiveSheet()->SetCellValue('S1', 'Date of loss DD/MM/YY');
            $objPHPExcel->getActiveSheet()->SetCellValue('T1', 'Date of intimation from insured DD/MM/YY');
            $objPHPExcel->getActiveSheet()->SetCellValue('U1', 'Date of intimation to insurer DD/MM/YY');
            $objPHPExcel->getActiveSheet()->SetCellValue('V1', 'Estimate of loss');
            $objPHPExcel->getActiveSheet()->SetCellValue('W1', 'Settled Amount');
            $objPHPExcel->getActiveSheet()->SetCellValue('X1', 'Final Settlement Date');
            $objPHPExcel->getActiveSheet()->SetCellValue('Y1', 'UTR No.');
            $objPHPExcel->getActiveSheet()->SetCellValue('Z1', 'Surveyor/ Investigator name & cont no.');
            $objPHPExcel->getActiveSheet()->SetCellValue('AA1', 'Claim handler');
            $objPHPExcel->getActiveSheet()->SetCellValue('AB1', 'Status');
            $objPHPExcel->getActiveSheet()->SetCellValue('AC1', 'Action lies');
            $objPHPExcel->getActiveSheet()->SetCellValue('AD1', 'Remarks');



            // set Row
            $rowCount = 2;
            //foreach ($mobiledata as $val)
            for($i=0;$i < sizeof($mobiledata['Data']['query_result']);$i++){
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount,$i+1);
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $mobiledata['Data']['query_result'][$i]['ref_no']);

                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $mobiledata['Data']['query_result'][$i]['policy_year']);
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $mobiledata['Data']['query_result'][$i]['client_name']);

                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $mobiledata['Data']['query_result'][$i]['Insured_cont_no']);

                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $mobiledata['Data']['query_result'][$i]['email']);
                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, $mobiledata['Data']['query_result'][$i]['claim_no']);

                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, $mobiledata['Data']['query_result'][$i]['policy_no']);
                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $rowCount, $mobiledata['Data']['query_result'][$i]['policy_period']);
                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $rowCount, $mobiledata['Data']['query_result'][$i]['Name_of_insurer']);
                $objPHPExcel->getActiveSheet()->SetCellValue('K' . $rowCount, $mobiledata['Data']['query_result'][$i]['Insurer_cont_no']);
                $objPHPExcel->getActiveSheet()->SetCellValue('L' . $rowCount, $mobiledata['Data']['query_result'][$i]['Type_of_policy']);
                $objPHPExcel->getActiveSheet()->SetCellValue('M' . $rowCount, $mobiledata['Data']['query_result'][$i]['Location_of_loss']);
                $objPHPExcel->getActiveSheet()->SetCellValue('N' . $rowCount, $mobiledata['Data']['query_result'][$i]['cause_of_loss']);
                $objPHPExcel->getActiveSheet()->SetCellValue('O' . $rowCount, $mobiledata['Data']['query_result'][$i]['Extent_of_loss']);
                $objPHPExcel->getActiveSheet()->SetCellValue('P' . $rowCount, $mobiledata['Data']['query_result'][$i]['Transit_From_To']);
                $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $rowCount, $mobiledata['Data']['query_result'][$i]['invoice_lr_no']);
                $objPHPExcel->getActiveSheet()->SetCellValue('R' . $rowCount, $mobiledata['Data']['query_result'][$i]['month']);
                $objPHPExcel->getActiveSheet()->SetCellValue('S' . $rowCount, $mobiledata['Data']['query_result'][$i]['date_of_loss']);
                $objPHPExcel->getActiveSheet()->SetCellValue('T' . $rowCount, $mobiledata['Data']['query_result'][$i]['date_intimation_insured']);
                $objPHPExcel->getActiveSheet()->SetCellValue('U' . $rowCount, $mobiledata['Data']['query_result'][$i]['date_intimation_insurer']);
                $objPHPExcel->getActiveSheet()->SetCellValue('V' . $rowCount, $mobiledata['Data']['query_result'][$i]['estimate_of_loss']);
                $objPHPExcel->getActiveSheet()->SetCellValue('W' . $rowCount, $mobiledata['Data']['query_result'][$i]['settled_amount']);
                $objPHPExcel->getActiveSheet()->SetCellValue('X' . $rowCount, $mobiledata['Data']['query_result'][$i]['final_settlement_date']);
                $objPHPExcel->getActiveSheet()->SetCellValue('Y' . $rowCount, $mobiledata['Data']['query_result'][$i]['utr_no']);
                $objPHPExcel->getActiveSheet()->SetCellValue('Z' . $rowCount, $mobiledata['Data']['query_result'][$i]['investigator_name_cont_no']);
                $objPHPExcel->getActiveSheet()->SetCellValue('AA' . $rowCount, $mobiledata['Data']['query_result'][$i]['claim_handler']);
                $objPHPExcel->getActiveSheet()->SetCellValue('AB' . $rowCount, $mobiledata['Data']['query_result'][$i]['status']);
                $objPHPExcel->getActiveSheet()->SetCellValue('AC' . $rowCount, $mobiledata['Data']['query_result'][$i]['action_lies']);
                $objPHPExcel->getActiveSheet()->SetCellValue('AD' . $rowCount, $mobiledata['Data']['query_result'][$i]['remarks']);











                $rowCount++;
            }

            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $objWriter->save(ABSOLUTE_DOC_ROOT.'/assets/smdashboardexports/'.$fileName);
            $filepath = FRONT_URL.'/assets/smdashboardexports/'.$fileName;
            echo json_encode(array('success'=>true, 'msg'=>"Records Generated", 'Data'=>$filepath));
            exit;
        }
    }

}

?>