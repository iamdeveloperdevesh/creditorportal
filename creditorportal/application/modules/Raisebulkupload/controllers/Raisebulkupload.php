<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI

require_once PATH_VENDOR.'vendor/autoload.php';


use Dompdf\Dompdf;

class Raisebulkupload extends CI_Controller
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


        $this->load->view('template/header.php');
        $this->load->view('index');
        $this->load->view('template/footer.php');
    }
    function fetch()
    {
        $_GET['utoken'] = $_SESSION['webpanel']['utoken'];
        $_GET['role_id'] = $_SESSION['webpanel']['role_id'];
        $_GET['user_id'] = $_SESSION['webpanel']['employee_id'];
        //echo "<pre>GET ";print_r($_GET);exit;
        $userListing = curlFunction(SERVICE_URL.'/api/getraisebulkdata',$_GET);
        //echo "<pre>";print_r($userListing);exit;


        $userListing = json_decode($userListing, true);
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

                array_push($temp, $userListing['Data']['query_result'][$i]['creaditor_name'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['ref_no'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['policy_year'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['client_name'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['Insured_cont_no'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['email'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['claim_no'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['policy_no'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['policy_period'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['Name_of_insurer'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['Insurer_cont_no'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['Type_of_policy'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['Location_of_loss'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['cause_of_loss'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['Extent_of_loss'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['Transit_From_To'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['invoice_lr_no'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['month'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['date_of_loss'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['date_intimation_insured'] );                array_push($temp, $userListing['Data']['query_result'][$i]['date_intimation_insured'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['date_intimation_insurer'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['estimate_of_loss'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['settled_amount'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['final_settlement_date'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['utr_no'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['investigator_name_cont_no'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['claim_handler'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['status'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['action_lies'] );
                array_push($temp, $userListing['Data']['query_result'][$i]['remarks'] );


                array_push($items, $temp);
            }
        }

        $result["aaData"] = $items;
        echo json_encode($result);
        exit;
    }
    function addupload()
    {
        $result['getCreditorDetails'] = json_decode(curlFunction(SERVICE_URL.'/api/getCreditorsDetails',[]),TRUE);


        $this->load->view('template/header.php');
        $this->load->view('addupload',$result);
        $this->load->view('template/footer.php');
    }
    function excelconvert($data)
    {
        $excelDate = $data; //2018-11-03
        $miliseconds = ($excelDate - (25567 + 2)) * 86400 * 1000;
        $seconds = $miliseconds / 1000;
        return  date("Y-m-d", $seconds);
    }
    function AddRaiseBulkFile(){
       // print_r($_POST);die;
        $this->load->library('excel');
        if (isset($_FILES["uploadfilebulk"]["name"])) {
            $path = $_FILES["uploadfilebulk"]["tmp_name"];
            $object = PHPExcel_IOFactory::load($path);
            $worksheet = $object->getSheet(0);
            $highestRow = $worksheet->getHighestRow();
           // print_r($highestRow);
            $highestColumn = $worksheet->getHighestColumn();
            $rowDatafirst = $worksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, false);
         //   print_r($highestColumn);
            $excel_header=array('S.No.','Mzapp-Alliance File Reference No.','Policy Year','Client name','Insured cont no.','Mail ID','Claim No.','Policy No','Policy Period','Name of insurer','Insurer cont no.','Mail ID','Claim No.','Policy No','Policy Period','Name of insurer','Insurer cont no.','Type of policy','Location of loss','Cause of loss','Extent of loss','Transit From To','Invoice & LR No.','Month','Date of loss DD/MM/YY','Date of intimation from insured DD/MM/YY','Date of intimation to insurer DD/MM/YY','Estimate of loss','Settled Amount','Final Settlement Date','Estimate of loss','Settled Amount','Settled Amount','UTR No.','Surveyor/ Investigator name & cont no.','Claim handler','Status','Action lies','Remarks');
            $difference_array=	array_merge(array_diff($excel_header,$rowDatafirst[0]),array_diff($rowDatafirst[0],$excel_header));
            //print_r($difference_array);die;

            if(count($difference_array) == 0){
                for ($row = 1; $row <= $highestRow; $row++) {
                    $rowData = $worksheet->rangeToArray('B' . $row . ':' . $highestColumn . $row,
                        null, true, false);
                    $rowData=$rowData[0];
                    //print_r($rowDatafirst);die;

                    if($row != 1){

                        $data=array();
                        $data['ClientCreation']['creditor_id']= $_POST['creditor_id'];
                        $data['ClientCreation']['ref_no']=$rowData[0];
                        $data['ClientCreation']['policy_year']=$rowData[1];
                        $data['ClientCreation']['client_name']=$rowData[2];
                        $data['ClientCreation']['Insured_cont_no']=$rowData[3];
                        $data['ClientCreation']['email']=$rowData[4];
                        $data['ClientCreation']['claim_no']=$rowData[5];
                        $data['ClientCreation']['policy_no']=$rowData[6];
                        $data['ClientCreation']['policy_period']=$rowData[7];
                        $data['ClientCreation']['Name_of_insurer']=$rowData[8];
                        $data['ClientCreation']['Insurer_cont_no']=$rowData[9];
                        $data['ClientCreation']['Type_of_policy']=$rowData[10];
                        $data['ClientCreation']['Location_of_loss']=$rowData[11];
                        $data['ClientCreation']['cause_of_loss']=$rowData[12];
                        $data['ClientCreation']['Extent_of_loss']=$rowData[13];
                        $data['ClientCreation']['Transit_From_To']=$rowData[14];
                        $data['ClientCreation']['invoice_lr_no']=$rowData[15];
                        $data['ClientCreation']['month']=$rowData[16];
                        $data['ClientCreation']['date_of_loss']=$this->excelconvert($rowData[17]);
                        $data['ClientCreation']['date_intimation_insured']=$this->excelconvert($rowData[18]);
                        $data['ClientCreation']['date_intimation_insurer']=$this->excelconvert($rowData[19]);
                        $data['ClientCreation']['estimate_of_loss']=$rowData[20];
                        $data['ClientCreation']['settled_amount']=$rowData[21];
                        $data['ClientCreation']['final_settlement_date']=$rowData[22];
                        $data['ClientCreation']['utr_no']=$rowData[23];
                        $data['ClientCreation']['investigator_name_cont_no']=$rowData[24];
                        $data['ClientCreation']['claim_handler']=$rowData[25];
                        $data['ClientCreation']['status']=$rowData[26];
                        $data['ClientCreation']['action_lies']=$rowData[27];
                        $data['ClientCreation']['remarks']=$rowData[28];
                       // print_r($data);die;
                        $request=$this->db->insert('raise_bulk_upload',$data['ClientCreation']);
                    }

                }
                $response['msg']="Done.Check application logs for more details!";
                $response['success']=true;
                echo json_encode($response);
            }else{
                $response['msg']="Wrong file Uploaded";
                $response['success']=false;
                echo json_encode($response);
            }

        }else{
            $response['msg']="Please upload file.";
            $response['success']=false;
            echo json_encode($response);
        }

    }

}

?>