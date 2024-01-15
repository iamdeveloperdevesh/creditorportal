<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI

require_once PATH_VENDOR.'vendor/autoload.php';


use Dompdf\Dompdf;

class Trackclaim extends CI_Controller
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

       $result = $this->db->query("select * from claim_mst_url as cm  join master_ceditors as mc where cm.creditor_id = mc.creditor_id and type = 1")->result_array();

        $this->load->view('template/header.php');
        $this->load->view('TrackClaims', $result);
        $this->load->view('template/footer.php');
    }
    function fetch()
    {
        $_GET['utoken'] = $_SESSION['webpanel']['utoken'];
        //	echo "<pre>GET ";print_r($_GET['i_type']);exit;
        $dataListing = curlFunction(SERVICE_URL.'/api/trackclaimListing',$_GET);
       // print_r($dataListing);die;
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

                $actionurl .=' <a href='.$dataListing['Data']['query_result'][$i]['URL'].' target="__blank">Track Claim</a>';

                array_push($temp,$actionurl);



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

}

?>